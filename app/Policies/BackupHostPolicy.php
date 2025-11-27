<?php

namespace App\Policies;

class BackupHostPolicy
{
    use DefaultPolicies;

    protected string $modelName = 'backuphost';
}
