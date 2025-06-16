<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

abstract class BaseRepository
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    protected function query(array $with = [], bool $useActiveScope = false): Builder
    {
        $query = $this->model->newQuery();

        if (!empty($with)) {
            $query->with($with);
        }

        if ($useActiveScope && method_exists($this->model, 'scopeActive')) {
            $query->active();
        }

        return $query;
    }
}
