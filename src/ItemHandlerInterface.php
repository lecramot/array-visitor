<?php

declare(strict_types = 1);

namespace Lecramot\ArrayVisitor;

interface ItemHandlerInterface
{
    /**
     * @param bool|int|float|string|mixed[]|object $value
     * @param int|string $key
     * @param mixed[] $array
     */
    public function __invoke($value, $key, array &$array): void;
}