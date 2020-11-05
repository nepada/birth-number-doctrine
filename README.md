Czech birth number Doctrine type
================================

[![Build Status](https://github.com/nepada/birth-number-doctrine/workflows/CI/badge.svg)](https://github.com/nepada/birth-number-doctrine/actions?query=workflow%3ACI+branch%3Amaster)
[![Coverage Status](https://coveralls.io/repos/github/nepada/birth-number-doctrine/badge.svg?branch=master)](https://coveralls.io/github/nepada/birth-number-doctrine?branch=master)
[![Downloads this Month](https://img.shields.io/packagist/dm/nepada/birth-number-doctrine.svg)](https://packagist.org/packages/nepada/birth-number-doctrine)
[![Latest stable](https://img.shields.io/packagist/v/nepada/birth-number-doctrine.svg)](https://packagist.org/packages/nepada/birth-number-doctrine)


Installation
------------

Via Composer:

```sh
$ composer require nepada/birth-number-doctrine
```

Register the type in your bootstrap:
```php
\Doctrine\DBAL\Types\Type::addType(
    \Nepada\BirthNumberDoctrine\BirthNumberType::NAME,
    \Nepada\BirthNumberDoctrine\BirthNumberType::class
);
```

In Nette with [nettrine/dbal](https://github.com/nettrine/dbal) integration, you can register the types in your configuration:
```yaml
dbal:
    connection:
        types:
            Nepada\BirthNumber\BirthNumber: Nepada\BirthNumberDoctrine\BirthNumberType
```


Usage
-----

`BirthNumberType` maps database value to Birth number value object (see [nepada/birth-number](https://github.com/nepada/birth-number) for further details) and back. The Birth number is stored as fixed string without the slash (e.g. `0001010009`).

Example usage in the entity:
```php
use Doctrine\ORM\Mapping as ORM;
use Nepada\BirthNumber\BirthNumber;

/**
 * @ORM\Entity
 */
class Person
{

    /** @ORM\Column(type=BirthNumber::class, nullable=false) */
    private BirthNumber $birthNumber;

    public function getBirthNumber(): BirthNumber
    {
        return $this->birthNumber;
    }

}
```

Example usage in query builder:
```php
$result = $repository->createQueryBuilder('foo')
    ->select('foo')
    ->where('foo.birthNumber = :birthNumber')
     // the parameter value is automatically normalized to '0001010009'
    ->setParameter('birthNumber', '000101 / 0009', BirthNumberType::NAME)
    ->getQuery()
    ->getResult();
```
