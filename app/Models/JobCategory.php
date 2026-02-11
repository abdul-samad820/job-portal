<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobCategory extends Model
{
    protected $fillable = ['name','description','admin_id','category_image'];

    public function jobroles(){
        
    return $this->hasMany(JobRole::class);
}
public function jobs()
{
    return $this->hasMany(Job::class, 'category_id');
}

}

