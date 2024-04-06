<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_completed'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)->withTimeStamps();
    }

    public function user(){
        return $this->belongsTo('App\Models\User','user_id');
    }

    public function taskUsers(){
        return $this->hasMany('App\Models\TaskUser','user_id');
    }
}
