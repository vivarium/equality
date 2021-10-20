<?php

/**
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2020 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Equality;

use Closure;

use function array_keys;
use function is_array;
use function is_object;
use function serialize;
use function sha1;
use function spl_object_id;

final class HashBuilder
{
    private string $total;

    public function __construct()
    {
        $this->total = '';
    }

    /**
     * @param mixed $value
     */
    public function append($value): HashBuilder
    {
        if (is_array($value)) {
            return $this->appendEach($value);
        }

        if ($value instanceof Closure) {
            return $this->appendCallable($value);
        }

        if (is_object($value)) {
            return $this->appendObject($value);
        }

        $builder         = clone $this;
        $builder->total .= serialize($value);

        return $builder;
    }

    public function getHashCode(): string
    {
        return sha1($this->total);
    }

    /**
     * @param mixed[] $array
     */
    private function appendEach(array $array): HashBuilder
    {
        $builder = clone $this;
        foreach (array_keys($array) as $key) {
            $builder = $builder->append($key);
            $builder = $builder->append($array[$key]);
        }

        return $builder;
    }

    private function appendCallable(Closure $closure): HashBuilder
    {
        $builder         = clone $this;
        $builder->total .= spl_object_id($closure);

        return $builder;
    }

    private function appendObject(object $object): HashBuilder
    {
        $builder         = clone $this;
        $builder->total .= $object instanceof Equality ?
            $object->hash() : serialize($object);

        return $builder;
    }
}
