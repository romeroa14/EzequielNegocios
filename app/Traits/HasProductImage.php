<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait HasProductImage
{
    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }

        return asset(Storage::disk('public')->path($this->image));
    }

    public function deleteImage()
    {
        if ($this->image && Storage::disk('public')->exists($this->image)) {
            Storage::disk('public')->delete($this->image);
        }
    }

    protected static function bootHasProductImage()
    {
        static::deleting(function ($model) {
            $model->deleteImage();
        });
    }
} 