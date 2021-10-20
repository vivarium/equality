<?php

/**
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2020 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Equality\Test;

use PHPUnit\Framework\TestCase;
use stdClass;
use Vivarium\Equality\Equality;
use Vivarium\Equality\HashBuilder;

/**
 * @coversDefaultClass \Vivarium\Equality\HashBuilder
 */
final class HashBuilderTest extends TestCase
{
    /**
     * @param mixed $value
     *
     * @covers ::__construct
     * @covers ::append
     * @covers ::getHashCode
     * @dataProvider getTestAppendPrimitiveData
     */
    public function testAppendPrimitive($value, string $expected): void
    {
        $builder = new HashBuilder();
        $builder = $builder->append($value);

        static::assertEquals($expected, $builder->getHashCode());
    }

    /**
     * @covers ::append
     * @covers ::appendObject
     * @dataProvider getTestAppendObjectData
     */
    public function testAppendObject(object $object, string $expected): void
    {
        $builder = new HashBuilder();
        $builder = $builder->append($object);

        static::assertEquals($expected, $builder->getHashCode());
    }

    /**
     * @param mixed[] $array
     *
     * @covers ::append
     * @covers ::appendEach
     * @dataProvider getTestAppendEachData
     */
    public function testAppendEach(array $array, string $expected): void
    {
        $builder = new HashBuilder();
        $builder = $builder->append($array);

        static::assertEquals($expected, $builder->getHashCode());
    }

    /**
     * @param mixed $element
     *
     * @covers ::append
     * @covers ::appendCallable
     * @dataProvider getClonePointData
     */
    public function testImmutability($element): void
    {
        $builder  = new HashBuilder();
        $builder1 = $builder->append($element);

        static::assertNotSame($builder, $builder1);
    }

    /**
     * @return array<array-key, array{0: scalar|null, 1: string}>
     */
    public function getTestAppendPrimitiveData(): array
    {
        return [
            'Integer Hash' => [
                42,
                '02cfb7f5e7b293c5f7374ada94925074f62bb5e1',
            ],
            'String Hash' => [
                'hello',
                'a4bbe8f088dbfba72434f05585ed0ce4ecb3c952',
            ],
            'Boolean Hash' => [
                false,
                'e9d186a11303ede3bdf2431bfa7a5bbcd5a1a9eb',
            ],
            'Null Hash' => [
                null,
                'a03e9ce134099d2bd410bdc53e8abb7d3f95c397',
            ],
        ];
    }

    /**
     * @return array<array-key, array{0: object, 1: string}>
     */
    public function getTestAppendObjectData(): array
    {
        $stdClass      = new stdClass();
        $stdClass->foo = 42;
        $stdClass->bar = 'baz';

        $equality = $this->createMock(Equality::class);
        $equality
            ->expects(static::once())
            ->method('hash')
            ->willReturn('79169da20d8365b605a4d0802300cb30019eec9f');

        return [
            'Object without EqualityInterface' => [
                $stdClass,
                'a555f265bb7cf041fa3e8611ee4b3bb9087c44b7',
            ],
            'Object with EqualityInterface' => [
                $equality,
                '061dcb8bab856f8eb86506be2d4d9dfec34f9948',
            ],
        ];
    }

    /**
     * @return array{0: array{0: array<int>, 1: string}, 1: array{0: array<object>, 1: string}}
     */
    public function getTestAppendEachData(): array
    {
        $stdClass      = new stdClass();
        $stdClass->foo = 42;

        return [
            [
                [1, 2, 3],
                '5d412d63565a48053ec0b58fd97b98b6e7090ea4',
            ],
            [
                [$stdClass, $stdClass, $stdClass],
                '64a2be69d872b5787f9c3ed872da857cda804a51',
            ],
            [
                ['a' => 1, 'b' => 2],
                '381ba48afb9497d12392200c7ba6d2eafd82e77e',
            ],
            [
                ['a' => 1, 'b' => 2, 'c' => ['a', 'b', 'c' => [3 => 'c']]],
                '9356b8b9c963674063a6d422c8b21eeccd890148',
            ],
        ];
    }

    /**
     * @return array{0: array<int>, 1: array<float>, 2: array<Equality>, 3: array{empty?: int}, 4: array<callable>}
     */
    public function getClonePointData(): array
    {
        $equality = $this->createMock(Equality::class);
        $equality->method('hash');

        return [
            [ 1 ],
            [  0.5 ],
            [ $equality ],
            [
                [],
            ],
            [
                static function (): int {
                    return 1 + 1;
                },
            ],
        ];
    }
}
