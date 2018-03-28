<?php

return [

    /**
     * This closure returns a boolean value that should determine
     * whether the migration(s) should be run or not. You may
     * calculate times, check environment variables, etc.
     */
    'should_run' => function () {
        return config('app.debug', false);
    },

];
