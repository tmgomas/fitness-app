<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait CustomUuid
{
    protected static function bootCustomUuid()
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                // Random UUID Generate කරන්න
                $uuid = Str::uuid()->toString();

                // 11-14 character (xxxxxx-xxxx-11ef-xxxx-xxxxxxxxxxxx) වෙනුවට "fffe" replace කරන්න
                $customUuid = substr($uuid, 0, 9) . '-fffe-' . substr($uuid, 14);

                $model->{$model->getKeyName()} = $customUuid;
            }
        });
    }
}
