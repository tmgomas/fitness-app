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

                // UUID format එකේ 11-14 digit "fffe" ලෙස වෙනස් කරන්න
                $customUuid = substr($uuid, 0, 9) . 'fffe' . substr($uuid, 13);

                // UUID එකේ අමතර dash නැතිවෙලා තියෙද කියලා check කරන්න
                $model->{$model->getKeyName()} = str_replace('--', '-', $customUuid);
            }
        });
    }
}
