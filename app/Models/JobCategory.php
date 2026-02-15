<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class JobCategory extends Model
{
    protected $fillable = [
        'name',
        'description',
        'admin_id',
        'category_image'
    ];

    public function jobroles()
    {
        return $this->hasMany(JobRole::class);
    }

    public function jobs()
    {
        return $this->hasMany(Job::class, 'category_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($category) {

            if ($category->category_image &&
                Storage::disk('public')
                    ->exists('categories/' . $category->category_image)) {

                Storage::disk('public')
                    ->delete('categories/' . $category->category_image);
            }

        });
    }
}