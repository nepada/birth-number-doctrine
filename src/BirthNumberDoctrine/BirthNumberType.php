<?php
declare(strict_types = 1);

namespace Nepada\BirthNumberDoctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\Exception\ValueNotConvertible;
use Doctrine\DBAL\Types\StringType;
use Nepada\BirthNumber\BirthNumber;
use function class_exists;

class BirthNumberType extends StringType
{

    /**
     * @final
     */
    public const NAME = BirthNumber::class;

    /**
     * @deprecated Kept for DBAL 3.x compatibility
     */
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
            throw class_exists(ValueNotConvertible::class)
                ? ValueNotConvertible::new($value, $this->getName(), null, $exception)
                : throw ConversionException::conversionFailed($value, $this->getName(), $exception);
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
                throw class_exists(InvalidType::class)
                    ? InvalidType::new($value, $this->getName(), ['null', BirthNumber::class, 'birth number string'], $exception)
                    : ConversionException::conversionFailedInvalidType($value, $this->getName(), ['null', BirthNumber::class, 'birth number string'], $exception);
            }
        }

        return $value->toStringWithoutSlash();
    }

    /**
     * @deprecated Kept for DBAL 3.x compatibility
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }

    /**
     * @deprecated Kept for DBAL 3.x compatibility
     */
    public function getDefaultLength(AbstractPlatform $platform): int
    {
        return 10;
    }

    /**
     * @param array<string, mixed> $fieldDeclaration
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        $fieldDeclaration['length'] = $this->getDefaultLength($platform);
        $fieldDeclaration['fixed'] = true;

        return parent::getSQLDeclaration($fieldDeclaration, $platform);
    }

}
