<?php

namespace App\Filters;

class TaskFilters extends Filters
{
    protected $filters = ['duedate'];

    protected function duedate($direction = "asc")
    {
        $this->builder->getQuery()->orders = [];
        $modifier = $direction === 'asc' ? '-' : '';
        return $this->builder->orderByRaw("{$modifier}due_date desc");
    }
}