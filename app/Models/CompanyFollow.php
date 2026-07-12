<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyFollow extends Model
{
    protected $fillable = [
        'user_id',
        'admin_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}
