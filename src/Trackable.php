<?php

namespace Gtk\EloquentTracking;

trait Trackable
{
    public static function bootTrackable()
    {
        static::created(function ($model) {
            ModelTrackingLog::createByAction($model, 'create');
        });

        static::updated(function ($model) {
            ModelTrackingLog::createByAction($model, 'update');
        });

        static::deleted(function ($model) {
            ModelTrackingLog::createByAction($model, 'delete');
        });
    }

    public function trackingLogs()
    {
        return $this->morphMany(ModelTrackingLog::class, 'trackable');
    }
}
