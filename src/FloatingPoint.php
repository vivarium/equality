<?php

/**
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2020 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Equality;

interface FloatingPoint
{
    public const EPSILON    = 2.2204460492503E-16;
    public const FLOAT_MIN  = 1.1754943508223E-38;
    public const FLOAT_MAX  = 3.4028234663853E+38;
    public const DOUBLE_MIN = 2.2250738585072E-308;
    public const DOUBLE_MAX = 1.7976931348623E+308;
}
