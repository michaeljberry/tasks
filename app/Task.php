<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $guarded = [];

    public function path()
    {
        return route('task', [
            'task' => $this->id
        ]);
    }

    public function scopeFilter($query, $filters)
    {
        return $filters->apply($query);
    }
}
