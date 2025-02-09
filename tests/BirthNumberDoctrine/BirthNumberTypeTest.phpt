<?php
declare(strict_types = 1);

namespace NepadaTests\BirthNumberDoctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Mockery\MockInterface;
use Nepada\BirthNumber\BirthNumber;
use Nepada\BirthNumberDoctrine\BirthNumberType;
use NepadaTests\TestCase;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';


/**
 * @testCase
 */
class BirthNumberTypeTest extends TestCase
{

    private BirthNumberType $type;

    /**
     * @var AbstractPlatform|MockInterface
     */
    private AbstractPlatform $platform;

    protected function setUp(): void
    {
        parent::setUp();

        if (Type::hasType(BirthNumberType::NAME)) {
            Type::overrideType(BirthNumberType::NAME, BirthNumberType::class);

        } else {
            Type::addType(BirthNumberType::NAME, BirthNumberType::class);
        }

        $type = Type::getType(BirthNumberType::NAME);
        Assert::type(BirthNumberType::class, $type);
        $this->type = $type;

        $this->platform = \Mockery::mock(AbstractPlatform::class);
    }

    public function testGetName(): void
    {
        Assert::same(BirthNumber::class, $this->type->getName());
    }

    public function testRequiresSQLCommentHint(): void
    {
        Assert::true($this->type->requiresSQLCommentHint($this->platform));
    }

    public function testConvertToDatabaseValueFails(): void
    {
        Assert::exception(
            function (): void {
                $this->type->convertToDatabaseValue('foo', $this->platform);
            },
            ConversionException::class,
            sprintf(
                'Could not convert PHP value \'foo\'%%a?%% to type %%S?%%%1$s%%S?%%. Expected one of the following types: null, %1$s, birth number string%%S?%%',
                BirthNumber::class,
            ),
        );
    }

    /**
     * @dataProvider getDataForConvertToDatabaseValue
     */
    public function testConvertToDatabaseValueSucceeds(BirthNumber|string|null $value, ?string $expected): void
    {
        Assert::same($expected, $this->type->convertToDatabaseValue($value, $this->platform));
    }

    /**
     * @return list<mixed[]>
     */
    protected function getDataForConvertToDatabaseValue(): array
    {
        return [
            [
                'value' => null,
                'expected' => null,
            ],
            [
                'value' => BirthNumber::fromString('000101 / 0009'),
                'expected' => '0001010009',
            ],
            [
                'value' => '000101 / 0009',
                'expected' => '0001010009',
            ],
        ];
    }

    public function testConvertToPHPValueFails(): void
    {
        Assert::exception(
            function (): void {
                $this->type->convertToPHPValue('foo', $this->platform);
            },
            ConversionException::class,
            sprintf('Could not convert database value "foo" to Doctrine Type %%S?%%%s%%S?%%', BirthNumber::class),
        );
    }

    /**
     * @dataProvider getDataForConvertToPHPValue
     */
    public function testConvertToPHPValueSucceeds(BirthNumber|string|null $value, ?BirthNumber $expected): void
    {
        $actual = $this->type->convertToPHPValue($value, $this->platform);
        if ($expected === null) {
            Assert::null($actual);
        } else {
            Assert::type(BirthNumber::class, $actual);
            Assert::same((string) $expected, (string) $actual);
        }
    }

    /**
     * @return list<mixed[]>
     */
    protected function getDataForConvertToPHPValue(): array
    {
        return [
            [
                'value' => null,
                'expected' => null,
            ],
            [
                'value' => BirthNumber::fromString('000101 / 0009'),
                'expected' => BirthNumber::fromString('000101 / 0009'),
            ],
            [
                'value' => '0001010009',
                'expected' => BirthNumber::fromString('000101 / 0009'),
            ],
        ];
    }

    public function testGetSQLDeclaration(): void
    {
        $this->platform->shouldReceive('getStringTypeDeclarationSQL')->with(['length' => 10, 'fixed' => true])->andReturn('MOCKVARCHAR');
        $declaration = $this->type->getSQLDeclaration(['length' => 255], $this->platform);
        Assert::same('MOCKVARCHAR', $declaration);
    }

}


(new BirthNumberTypeTest())->run();
