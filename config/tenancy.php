<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Tenant Identification
    |--------------------------------------------------------------------------
    | How tenants are identified from the request.
    | Options: 'subdomain', 'domain', 'path'
    */
    'identification' => env('TENANT_IDENTIFICATION', 'subdomain'),

    /*
    |--------------------------------------------------------------------------
    | Central Domain
    |--------------------------------------------------------------------------
    | The main domain for the super admin panel.
    */
    'central_domain' => env('CENTRAL_DOMAIN', 'localhost'),

    /*
    |--------------------------------------------------------------------------
    | Schema Prefix
    |--------------------------------------------------------------------------
    | PostgreSQL schema prefix for tenant schemas.
    | Tenant with slug "acme" will use schema "tenant_acme"
    */
    'schema_prefix' => env('TENANT_SCHEMA_PREFIX', 'tenant_'),

    /*
    |--------------------------------------------------------------------------
    | Default Schema
    |--------------------------------------------------------------------------
    | The default/public schema for admin panel.
    */
    'default_schema' => env('DB_SCHEMA', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Tenant Models
    |--------------------------------------------------------------------------
    | Tables that exist per-tenant schema.
    */
    'tenant_tables' => [
        'tasks',
    ],

    /*
    |--------------------------------------------------------------------------
    | Central Tables
    |--------------------------------------------------------------------------
    | Tables that exist only in the public/central schema.
    */
    'central_tables' => [
        'tenants',
        'super_admins',
    ],
];
