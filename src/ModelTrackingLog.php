<?php

namespace Gtk\EloquentTracking;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class ModelTrackingLog extends Model
{
    protected $casts = [
        'before', 'after'
    ];
    
    public static function createByAction($model, $action)
    {
        return static::forceCreate([
            'trackable_id' => $model->id,
            'trackable_type' => get_class($model),
            'before' => ($action != 'delete') ? array_intersect_key($model->getOriginal(), $model->getDirty()) : $model->getOriginal(),
            'after' => ($action != 'delete') ? $model->getDirty() : null,
            'action' => $action,
            'user_id' => Auth::id() ?: null,
        ]);
    }
}
