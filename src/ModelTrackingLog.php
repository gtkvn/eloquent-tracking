<?php

namespace Gtk\EloquentTracking;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class ModelTrackingLog extends Model
{
    public static function createByAction($model, $action)
    {
        return static::forceCreate([
            'trackable_id' => $model->id,
            'trackable_type' => get_class($model),
            'before' => ($action != 'delete') ? json_encode(array_intersect_key($model->getOriginal(), $model->getDirty())) : json_encode($model->getOriginal()),
            'after' => ($action != 'delete') ? json_encode($model->getDirty()) : null,
            'action' => $action,
            'user_id' => Auth::id() ?: null,
        ]);
    }
}
