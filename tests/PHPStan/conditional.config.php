<?php
declare(strict_types = 1);

use Composer\InstalledVersions;
use Composer\Semver\VersionParser;
use Doctrine\DBAL\Types\Exception\ValueNotConvertible;

$config = [];

if (InstalledVersions::satisfies(new VersionParser(), 'nepada/birth-number', '<=1.1.0')) {
    $config['parameters']['ignoreErrors'][] = [
        'message' => '~Dead catch - Throwable is never thrown in the try block~',
        'path' => '../../src/BirthNumberDoctrine/BirthNumberType.php',
        'count' => 2,
    ];
}

if (class_exists(ValueNotConvertible::class)) { // DBAL 3.x compatibility
    $config['parameters']['ignoreErrors'][] = [
        'message' => '#^Call to an undefined static method Doctrine\\\\DBAL\\\\Types\\\\ConversionException\\:\\:conversionFailed\\(\\)\\.$#',
        'path' => '../../src/BirthNumberDoctrine/BirthNumberType.php',
        'count' => 1,
    ];
    $config['parameters']['ignoreErrors'][] = [
        'message' => '#^Call to an undefined static method Doctrine\\\\DBAL\\\\Types\\\\ConversionException\\:\\:conversionFailedInvalidType\\(\\)\\.$#',
        'path' => '../../src/BirthNumberDoctrine/BirthNumberType.php',
        'count' => 1,
    ];
}

return $config;
