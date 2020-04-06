<?php

declare(strict_types = 1);

namespace Lecramot\ArrayVisitor;

class ArrayVisitor implements ArrayVisitorInterface
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
    ): void {
        foreach ($array as $k => &$v) {

            if ($onEntry !== null) {
                call_user_func_array($onEntry, [$v, $k, &$array]);
            }

            if (is_array($v)) {
                $this->visit($v, $onEntry, $onExit);
            }

            if ($onExit !== null) {
                call_user_func_array($onExit, [$v, $k, &$array]);
            }
        }
    }

}