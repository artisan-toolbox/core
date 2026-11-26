<?php

declare(strict_types=1);

use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

$root = dirname(__DIR__, 2);

require $root.'/vendor/autoload.php';

$files = new Filesystem;
$directory = sys_get_temp_dir().'/core-distribution-'.bin2hex(random_bytes(8));
$application = $directory.'/application';
$archive = $directory.'/core.zip';
$files->makeDirectory($application.'/bootstrap/cache', recursive: true);

$streamOutput = static function (string $stream, string $output): void {
    fwrite($stream === Process::ERR ? STDERR : STDOUT, $output);
};

try {
    new Process(['git', 'archive', '--format=zip', '--output='.$archive, 'HEAD'], $root)->mustRun($streamOutput);

    $zip = new ZipArchive;

    throw_if($zip->open($archive) !== true, RuntimeException::class, 'Could not open the Core package archive.');

    try {
        $package = json_decode((string) $zip->getFromName('composer.json'), true, flags: JSON_THROW_ON_ERROR);
    } finally {
        $zip->close();
    }

    throw_if($package['name'] !== 'artisan-toolbox/core', RuntimeException::class, 'The archive does not contain the Core package manifest.');

    $package['version'] = '0.0.0';
    $package['dist'] = ['type' => 'zip', 'url' => $archive];
    $manifest = [
        'repositories' => [['type' => 'package', 'package' => $package]],
        'require' => ['artisan-toolbox/core' => '0.0.0', 'laravel/framework' => '^13.0'],
    ];

    $files->put($application.'/composer.json', json_encode($manifest, JSON_THROW_ON_ERROR));

    new Process([
        'composer', 'install', '--no-dev', '--prefer-dist', '--no-scripts', '--no-plugins', '--no-interaction', '--no-progress',
    ], $application, ['COMPOSER_HOME' => $directory.'/composer'], timeout: 240)->mustRun($streamOutput);

    new Process([PHP_BINARY, '-r', <<<'PHP'
        use ArtisanToolbox\Core\Brasilian\Document;
        use ArtisanToolbox\Core\Brasilian\DocumentType;
        use ArtisanToolbox\Core\Brasilian\Phone;
        use ArtisanToolbox\Core\Brasilian\PhoneType;
        use ArtisanToolbox\Core\Brasilian\Rules\DocumentRule;
        use ArtisanToolbox\Core\Brasilian\Rules\PhoneRule;
        use Illuminate\Support\Facades\Validator;

        require 'vendor/autoload.php';
        $app = Illuminate\Foundation\Application::configure(getcwd())->create();
        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        if (! Illuminate\Support\Arr::hasMacro('sift')) {
            fwrite(STDERR, "Laravel did not discover the Core service provider.\n");

            exit(1);
        }

        $result = Illuminate\Support\Arr::sift([
            'name' => ArtisanToolbox\Core\sift_when(true, 'Ada'),
            'private' => ArtisanToolbox\Core\sift_when(false, 'hidden'),
        ]);

        if ($result !== ['name' => 'Ada']) {
            fwrite(STDERR, "The installed Core package did not filter the payload.\n");

            exit(1);
        }

        $validation = Validator::make([
            'tax_id' => '12.abc.345/01de-35',
            'phone' => '(11) 98765-4321',
        ], [
            'tax_id' => ['required', new DocumentRule(DocumentType::Cnpj)],
            'phone' => ['required', new PhoneRule(PhoneType::RegionalLandline, PhoneType::RegionalMobile)],
        ]);

        if (! $validation->passes()
            || Document::from('12.abc.345/01de-35')->unmask() !== '12ABC34501DE35'
            || Phone::from('11987654321')->mask() !== '(11) 98765-4321'
        ) {
            fwrite(STDERR, "The installed Core package could not validate and format Brazilian values.\n");

            exit(1);
        }

        $invalid = Validator::make(['tax_id' => '12ABC34501DE36'], ['tax_id' => [new DocumentRule]]);

        if ($invalid->errors()->first('tax_id') !== 'The tax id must be a valid Brazilian document.') {
            fwrite(STDERR, "The installed Core package did not report an invalid CNPJ with its validation message.\n");

            exit(1);
        }

        $installed = Composer\InstalledVersions::getInstallPath('artisan-toolbox/core');

        if (! is_file($installed.'/resources/boost/skills/core-development/SKILL.md')) {
            fwrite(STDERR, "The installed Core archive is missing its Boost skill.\n");

            exit(1);
        }

        echo "Laravel discovered Core. Sift, Brazilian values, and the bundled Boost skill are available.\n";
        PHP], $application)->mustRun($streamOutput);
} finally {
    $files->deleteDirectory($directory);
}
