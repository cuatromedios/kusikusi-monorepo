<?php

namespace Cuatromedios\Kusikusi\Models\Traits;

use Illuminate\Support\Facades\Config;
use PUGX\Shortid\Shortid;

trait UsesShortId
{

    /**
     * The "booting" method of the model, This help to magically create uuid for all new models
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            if (!isset($model[$model->getKeyName()])) {
                do {
                    $id = Shortid::generate(Config::get('kusikusi_models.shortIdLength', 10));
                    $found_duplicate = self::where($model->getKeyName(), $id)->first();
                } while (!!$found_duplicate);
                $model->setAttribute($model->getKeyName(), $id);
            } else {
                $model->setAttribute($model->getKeyName(), substr($model[$model->getKeyName()], 0, 16));
            }
        });
    }

    /**
     * Get the value indicating whether IDs are not incremental.
     *
     * @return bool
     */
    public function getIncrementing()
    {
        return false;
    }

    /**
     * Get the primary key for the model.
     *
     * @return string
     */
    public function getKeyName()
    {
        return 'id';
    }

    /**
     * Get the key type.
     *
     * @return string
     */
    public function getKeyType()
    {
        return 'string';
    }
}
