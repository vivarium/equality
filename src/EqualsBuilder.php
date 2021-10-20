<?php

/**
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2020 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Equality;

use Vivarium\Float\NearlyEquals;

use function array_keys;
use function count;
use function is_array;
use function is_float;
use function is_object;

/**
 * @psalm-immutable
 */
final class EqualsBuilder
{
    private bool $isEquals;

    public function __construct()
    {
        $this->isEquals = true;
    }

    /**
     * @param mixed $first
     * @param mixed $second
     */
    public function append($first, $second): EqualsBuilder
    {
        if (! $this->isEquals) {
            return $this;
        }

        if ($first instanceof Equality && is_object($second)) {
            return $this->appendObject($first, $second);
        }

        if (is_array($first) && is_array($second)) {
            return $this->appendEach($first, $second);
        }

        if (is_float($first) && is_float($second)) {
            return $this->appendFloat($first, $second);
        }

        $builder           = clone $this;
        $builder->isEquals = $first === $second;

        return $builder;
    }

    private function appendFloat(float $first, float $second): EqualsBuilder
    {
        $builder           = clone $this;
        $builder->isEquals = (new NearlyEquals())($first, $second);

        return $builder;
    }

    /**
     * @param array<mixed> $first
     * @param array<mixed> $second
     */
    private function appendEach(array $first, array $second): EqualsBuilder
    {
        if (count($first) !== count($second)) {
            return $this->reject();
        }

        $builder           = clone $this;
        $builder->isEquals = $this->isEquals;
        foreach (array_keys($first) as $key) {
            if (! isset($second[$key])) {
                return $builder->reject();
            }

            $builder = $builder->append($first[$key], $second[$key]);
        }

        return $builder;
    }

    private function appendObject(Equality $first, object $second): EqualsBuilder
    {
        $builder           = clone $this;
        $builder->isEquals = $first->equals($second);

        return $builder;
    }

    public function isEquals(): bool
    {
        return $this->isEquals;
    }

    private function reject(): EqualsBuilder
    {
        $builder           = clone $this;
        $builder->isEquals = false;

        return $builder;
    }
}
