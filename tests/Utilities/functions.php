<?php

function create($class, $parameters = [])
{
    $attributes = $parameters['attributes'] ?? [];
    $times = $parameters['times'] ?? null;
    $states = $parameters['states'] ?? [];
    return factory($class, $times)->states($states)->create($attributes);
}

function make($class, $parameters = [])
{
    $attributes = $parameters['attributes'] ?? [];
    $times = $parameters['times'] ?? null;
    $states = $parameters['states'] ?? [];
    return factory($class, $times)->make($attributes);
}
