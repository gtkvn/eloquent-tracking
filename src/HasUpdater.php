<?php

namespace Gtk\EloquentTracking;

use Illuminate\Support\Facades\Auth;

trait HasUpdater
{
    /**
     * Boot the has updater trait for a model.
     *
     * @return void
     */
    public static function bootHasUpdater()
    {
        if (Auth::guest()) {
            return false;
        }

        static::saving(function ($model) {
            $model->updateUpdater();
        });
    }

    /**
     * Update the creater and updater.
     *
     * @return void
     */
    protected function updateUpdater()
    {
        $userId = Auth::id();

        if (! $this->isDirty('updated_by')) {
            $this->setUpdatedBy($userId);
        }

        if (! $this->exists && ! $this->isDirty('created_by')) {
            $this->setCreatedBy($userId);
        }
    }

    /**
     * Set the value of the "created by" attribute.
     *
     * @param  mixed  $value
     * @return $this
     */
    public function setCreatedBy($value)
    {
        $this->created_by = $value;

        return $this;
    }

    /**
     * Set the value of the "updated by" attribute.
     *
     * @param  mixed  $value
     * @return $this
     */
    public function setUpdatedBy($value)
    {
        $this->updated_by = $value;

        return $this;
    }
}
