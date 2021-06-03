<?php

/**
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2020 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Equality;

use function abs;
use function min;

final class NearlyEquals
{
    private float $epsilon;

    private float $min;

    private float $max;

    public function __construct(
        float $epsilon = FloatingPoint::EPSILON,
        float $min = FloatingPoint::FLOAT_MIN,
        float $max = FloatingPoint::FLOAT_MAX
    ) {
        $this->epsilon = $epsilon;
        $this->min     = $min;
        $this->max     = $max;
    }

    /**
     * The main float comparison algorithm
     *
     * @see https://floating-point-gui.de/errors/comparison/
     */
    public function __invoke(float $first, float $second, ?float $epsilon = null): bool
    {
        if ($first === $second) {
            return true;
        }

        $epsilon ??= $this->epsilon;

        $absFirst  = abs($first);
        $absSecond = abs($second);
        $diff      = abs($first - $second);

        if ($first === 0.0 || $second === 0.0 || ($absFirst + $absSecond) < $this->min) {
            return $diff < $epsilon * $this->min;
        }

        $first  = abs($first);
        $second = abs($second);

        return $diff / min($first + $second, $this->max) < $epsilon;
    }
}
