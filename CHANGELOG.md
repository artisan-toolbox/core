# Changelog

## [1.0.0] - 2026-08-18

### Features

- **Implement Versionable in Core** (`270bf4c`)
  Adds a Versionable contract/implementation to the Core so the package can represent and work with version metadata in a standardized way. This matters for downstream tooling and skills that need consistent version handling. User impact is limited to enabling this new core capability; no user-facing breaking behavior is described, but integrations may be able to rely on the new Versionable behavior going forward.

- **Add Translated cast and update middleware namespace** (`fe833d6`)
  Introduces a translated cast (supporting translating/casting localized values) and updates the middleware namespace wiring so middleware resolution aligns with the intended application structure. This improves i18n/translation ergonomics and fixes/clarifies middleware loading behavior. Compatibility impact depends on how existing code references middleware namespaces and how translated casting is used; consumers should verify any custom middleware or casting usage still aligns with the updated namespace/cast behavior.

- **Remove update-changelog workflows** (`b9ce060`)
  Removes previously existing GitHub workflows related to updating/generating changelogs. This matters because release note generation is likely centralized elsewhere (e.g., the release configuration) and the removed workflows should no longer run. User impact is CI/release process related: maintainers should ensure changelog updates continue via the new intended mechanism and that any references to the removed workflows are updated.

- **Add Inertia cross-domain visit middleware** (`b1c55ef`)
  Adds middleware to support Inertia cross-domain visits, enabling navigation behavior to work correctly when crossing domains. This matters for apps integrating Inertia in multi-domain setups, reducing broken navigation/session flows. User impact: apps can adopt the new middleware to improve cross-domain correctness; compatibility depends on how middleware is registered—verify registration points in your app and middleware stack.

### Documentation

- **Update README and documentation links for clarity** (`0b31e30`)
  Updates README and documentation links to be clearer and more directly usable. This improves onboarding and reduces friction when navigating project docs. No runtime/API behavior changes; users primarily benefit from more accurate documentation navigation.

- **Update README badges to reference 1.x branch workflows** (`1a00211`)
  Adjusts README badges so they reference 1.x branch CI/workflow targets rather than other branches. This matters to ensure users see the correct build status for the major version line. No code behavior changes, but it improves trust/clarity of CI signals.

- **Update README badges for consistency and clarity** (`f3a1f79`)
  Further refines README badges to ensure consistency and clearer presentation of status information. This is documentation-only and does not affect runtime behavior; it helps users quickly interpret repository health indicators.

### Code Style

- **Use Laravel attribute imports for core command** (`9198fb0`)
  Refactors the core command to use Laravel attribute imports (cleaner/modern attribute usage) rather than older import patterns. This is a code-style/ergonomics change with minimal runtime impact, but it may affect compatibility for tooling or PHP/Laravel versions if attribute usage differs; consumers should not need migration unless they fork/customize the command class.

### Build

- **Require PHP 8.5, Laravel 13, and Pest 5** (`3a1fbc0`)
  Updates project/dependency requirements to require PHP 8.5, Laravel 13, and Pest 5. This is a significant compatibility change: it sets the minimum supported runtime/framework/tooling versions. Users/migrators should ensure their environments match (or plan upgrades) before installing/upgrading, and verify that their tests and integrations are compatible with the newer versions.

### Maintenance

- **Initial commit** (`3058079`)
  Includes the changes introduced by commit 3058079: Initial commit.

# Release Notes

## [Unreleased](https://github.com/artisan-toolbox/core/compare/v0.1.0...1.x)

- Add `sift()` macros for `Arr`, `Collection`, and `LazyCollection`, plus the namespaced `sift_when()` helper, for lazily building conditional iterables without mutating their sources.
- Add the `Translated` Eloquent cast for resolving stored strings with the current application locale.
- Add `HandleInertiaCrossDomainVisits` for navigation between trusted hosts served by the same Inertia application.
- Require PHP 8.5 or later, Laravel 13, and Pest 5.

## [v0.1.0](https://github.com/artisan-toolbox/core/compare/...v0.1.0) - 202x-xx-xx

Initial pre-release.
