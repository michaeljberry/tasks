<?php

namespace App\Filters;

class TaskFilters extends Filters
{
    protected $filters = ['duedate', 'users'];

    protected function duedate($direction = 'asc')
    {
        $this->builder->getQuery()->orders = [];
        $modifier = $direction === 'asc' ? '-' : '';
        return $this->builder->orderByRaw("{$modifier}due_date desc");
    }

    protected function users($direction = 'asc')
    {
        $this->builder->getQuery()->orders = [];
        $modifier = $direction === 'asc' ? '-' : '';
        return $this->builder->orderByRaw("{$modifier}user_id desc");
    }
}