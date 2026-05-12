<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Firebase Sync Configuration
    |--------------------------------------------------------------------------
    |
    | immediate_push: If true, every individual model save triggers a push.
    |                 If false, changes are only marked as 'pending' for batch.
    |
    | read_budget: Maximum document reads allowed per execution to stay in Spark.
    |
    */

    'immediate_push' => env('FIREBASE_IMMEDIATE_PUSH', true),
    'read_budget'    => env('FIREBASE_READ_BUDGET', 45000),
    'write_budget'   => env('FIREBASE_WRITE_BUDGET', 18000),
    'batch_size'     => env('FIREBASE_BATCH_SIZE', 500),
];
