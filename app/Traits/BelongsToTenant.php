<?php

namespace App\Traits;

use App\Models\Scopes\TenantScope;
use Illuminate\Support\Facades\App;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope());

        static::creating(/**
             * @throws ContainerExceptionInterface
             * @throws NotFoundExceptionInterface
             */ function ($model) {
            if (App::bound('currentTenant')) {
                $model->tenant_id = App::get('currentTenant')->id;
            }
        });
    }
}
