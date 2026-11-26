<?php

declare(strict_types=1);

namespace ArtisanToolbox\Core\Brasilian;

enum PhoneType
{
    /** Takes precedence over Landline when no types are specified. */
    case ServiceCenter;
    case Landline;
    case Mobile;
    case RegionalLandline;
    case RegionalMobile;
    case InternationalPhone;
    case ShortCode3;
    case ShortCode4;
    case ShortCode5;
    case TollFree;
    case SharedCost;
    case Donation;
    case Premium;
    case Telemarketing;
}
