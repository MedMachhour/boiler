<?php

return [
    'entities_namespace'     => 'App',
    'controllers_namespace'  => 'App\\Http\\Controllers',
    'transformers_namespace' => 'App\\Transformers',
    'services_namespace'     => 'App\\Services',
    'policies_namespace'     => 'App\\Policies',
    'exceptions'             => [
        \Illuminate\Auth\Access\AuthorizationException::class       => ['method' => 'unauthorized', 'message' => null],
        \Illuminate\Database\Eloquent\ModelNotFoundException::class => ['method' => 'notFound', 'message' => null],
        \Illuminate\Auth\AuthenticationException::class             => ['method'  => 'unauthorized', 'message' => 'Unauthenticated'],
    ],
];
