<?php

namespace Onlinepets\TimedMigrations\Contracts;

interface RunsInTimeframe
{
    /**
     * @return \Illuminate\Support\Carbon[]
     */
    public function getTimesToRunBetween(): array;
}
