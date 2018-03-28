<?php

namespace Onlinepets\ConditionalMigrations\Contracts;

interface ConditionalMigration
{
    /**
     * @return bool
     */
    public function shouldRun(): bool;
}
