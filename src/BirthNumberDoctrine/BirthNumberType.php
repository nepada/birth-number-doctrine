<?php
declare(strict_types = 1);

namespace Nepada\BirthNumberDoctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\StringType;
use Nepada\BirthNumber\BirthNumber;

class BirthNumberType extends StringType
{

    public const NAME = BirthNumber::class;

    public function getName(): string
    {
        return static::NAME;
    }

    /**
     * @param BirthNumber|string|null $value
     */
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?BirthNumber
    {
        if ($value === null) {
            return $value;
        }

        if ($value instanceof BirthNumber) {
            return $value;
        }

        try {
            return BirthNumber::fromString($value);
        } catch (\Throwable $exception) {
            throw ConversionException::conversionFailed($value, $this->getName(), $exception);
        }
    }

    /**
     * @param BirthNumber|string|null $value
     */
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return $value;
        }

        if (! $value instanceof BirthNumber) {
            try {
                $value = BirthNumber::fromString($value);
            } catch (\Throwable $exception) {
                throw ConversionException::conversionFailedInvalidType($value, $this->getName(), ['null', BirthNumber::class, 'birth number string'], $exception);
            }
        }

        return $value->toStringWithoutSlash();
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }

    public function getDefaultLength(AbstractPlatform $platform): int
    {
        return 10;
    }

    /**
     * @param mixed[] $fieldDeclaration
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        $fieldDeclaration['length'] = $this->getDefaultLength($platform);
        $fieldDeclaration['fixed'] = true;

        return $platform->getVarcharTypeDeclarationSQL($fieldDeclaration);
    }

}
