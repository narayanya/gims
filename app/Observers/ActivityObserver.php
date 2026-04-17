<?php

namespace App\Observers;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

class ActivityObserver
{
    protected string $module;

    public function __construct(string $module = '')
    {
        $this->module = $module;
    }

    public static function for(string $module): static
    {
        return new static($module);
    }

    protected function label(Model $model): string
    {
        foreach (['lot_number', 'accession_number', 'request_number', 'storage_id', 'crop_name', 'variety_name', 'name', 'title'] as $field) {
            if (!empty($model->$field)) return $model->$field;
        }
        return '#' . $model->getKey();
    }

    public function created(Model $model): void
    {
        ActivityLog::log('created', $this->module, $model->getKey(), $this->label($model), null, $model->getAttributes());
    }

    public function updated(Model $model): void
    {
        ActivityLog::log('updated', $this->module, $model->getKey(), $this->label($model), $model->getOriginal(), $model->getChanges());
    }

    public function deleted(Model $model): void
    {
        ActivityLog::log('deleted', $this->module, $model->getKey(), $this->label($model), $model->getAttributes(), null);
    }
}
