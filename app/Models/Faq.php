<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $fillable = ['admin_id', 'question', 'answer', 'status'];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
