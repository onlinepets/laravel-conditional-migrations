<?php

namespace MLL\ConditionalMigrations\Contracts;

interface ConditionalMigration
{
    /**
     * Should the migration run?
     */
    public function shouldRun(): bool;
}
