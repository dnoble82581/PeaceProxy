<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\App;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (App::bound('currentTenant')) {
            $builder->where($model->getTable().'.tenant_id', App::get('currentTenant')->id);
        }
    }
}
