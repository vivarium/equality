<?php

/**
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2020 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Equality;

use function count;
use function is_array;
use function is_float;
use function is_object;

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
     * @param mixed[] $first
     * @param mixed[] $second
     */
    private function appendEach(array $first, array $second): EqualsBuilder
    {
        $builder           = clone $this;
        $builder->isEquals = $this->isEquals;
        if (count($first) !== count($second)) {
            $builder->isEquals = false;

            return $builder;
        }

        for ($i = 0; $i < count($first); $i++) {
            $builder = $builder->append($first[$i], $second[$i]);
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
}
