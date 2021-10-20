<?php

/**
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Equality;

final class Equal
{
    /**
     * @param mixed $first
     * @param mixed $second
     *
     * @psalm-pure
     */
    public static function areEquals($first, $second): bool
    {
        return (new EqualsBuilder())
            ->append($first, $second)
            ->isEquals();
    }

    /**
     * @param mixed $element
     *
     * @psalm-pure
     */
    public static function hash($element): string
    {
        return (new HashBuilder())
            ->append($element)
            ->getHashCode();
    }
}
