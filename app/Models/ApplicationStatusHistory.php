<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationStatusHistory extends Model
{
    protected $table = 'application_status_histories';

    protected $fillable = [
        'job_application_id',
        'from_status',
        'to_status',
        'changed_by_admin_id',
    ];

    public function application()
    {
        return $this->belongsTo(JobApplication::class, 'job_application_id');
    }

    public function changedBy()
    {
        return $this->belongsTo(Admin::class, 'changed_by_admin_id');
    }
}
