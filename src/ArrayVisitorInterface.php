<?php

declare(strict_types = 1);

namespace Lecramot\ArrayVisitor;

interface ArrayVisitorInterface
{

    /**
     * @param mixed[] $array
     * @param callable|null $onEntry
     * @param callable|null $onExit
     */
    public function visit(
        array &$array,
        ?callable $onEntry = null,
        ?callable $onExit = null
    ): void;
}