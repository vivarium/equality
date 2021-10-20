<?php

/**
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2020 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Equality;

interface Equality
{
    /**
     * @psalm-mutation-free
     */
    public function equals(object $object): bool;

    /**
     * @psalm-mutation-free
     */
    public function hash(): string;
}
