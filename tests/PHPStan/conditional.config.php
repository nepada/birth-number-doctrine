<?php
declare(strict_types = 1);

use Composer\InstalledVersions;
use Composer\Semver\VersionParser;
use Doctrine\DBAL\Types\Exception\ValueNotConvertible;

$config = ['parameters' => ['ignoreErrors' => []]];

if (InstalledVersions::satisfies(new VersionParser(), 'nepada/birth-number', '<=1.1.0')) {
    $config['parameters']['ignoreErrors'][] = [
        'message' => '~Dead catch - Throwable is never thrown in the try block~',
        'path' => __DIR__ . '/../../src/BirthNumberDoctrine/BirthNumberType.php',
        'count' => 2,
    ];
}

if (InstalledVersions::satisfies(new VersionParser(), 'doctrine/dbal', '<=4.0')) {
    $config['parameters']['ignoreErrors'][] = [
        'message' => '#^Parameter \\#1 \\$fieldDeclaration \\(array\\<string, mixed\\>\\) of method Nepada\\\\BirthNumberDoctrine\\\\BirthNumberType\\:\\:getSQLDeclaration\\(\\) should be contravariant with parameter \\$column \\(array\\<mixed\\>\\) of method Doctrine\\\\DBAL\\\\Types\\\\StringType\\:\\:getSQLDeclaration\\(\\)$#',
        'path' => __DIR__ . '/../../src/BirthNumberDoctrine/BirthNumberType.php',
        'count' => 1,
    ];
}

if (class_exists(ValueNotConvertible::class)) { // DBAL 3.x compatibility
    $config['parameters']['ignoreErrors'][] = [
        'message' => '#^Call to an undefined static method Doctrine\\\\DBAL\\\\Types\\\\ConversionException\\:\\:conversionFailed\\(\\)\\.$#',
        'path' => __DIR__ . '/../../src/BirthNumberDoctrine/BirthNumberType.php',
        'count' => 1,
    ];
    $config['parameters']['ignoreErrors'][] = [
        'message' => '#^Call to an undefined static method Doctrine\\\\DBAL\\\\Types\\\\ConversionException\\:\\:conversionFailedInvalidType\\(\\)\\.$#',
        'path' => __DIR__ . '/../../src/BirthNumberDoctrine/BirthNumberType.php',
        'count' => 1,
    ];
    $config['parameters']['ignoreErrors'][] = [
        'message' => '#^Invalid type mixed to throw\\.$#',
        'path' => __DIR__ . '/../../src/BirthNumberDoctrine/BirthNumberType.php',
        'count' => 2,
    ];
}

if (InstalledVersions::satisfies(new VersionParser(), 'doctrine/dbal', '<=4.0') && PHP_VERSION_ID >= 8_04_00) { // wtf fail on PHP 8.4 + lowest deps
    $config['parameters']['ignoreErrors'][] = [
        'message' => '#^Invalid type mixed to throw\\.$#',
        'path' => __DIR__ . '/../../src/BirthNumberDoctrine/BirthNumberType.php',
        'count' => 2,
    ];
}

return $config;
