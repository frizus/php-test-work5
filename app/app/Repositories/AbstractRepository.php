<?php

declare(strict_types=1);

namespace App\Repositories;

use Exception;
use Illuminate\Container\Container as Application;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

abstract class AbstractRepository
{
    public const array ORDER_FIELDS = [];

    final public const array ORDER_DIRECTIONS = ['asc', 'desc'];

    final public const string DEFAULT_ORDER_DIRECTION = 'asc';

    public const array SEARCH_FIELDS = [];

    protected Model $model;

    protected bool $created;

    protected bool $updated;

    /**
     * @throws Exception
     */
    public function __construct(
        protected Application $app
    ) {
        $this->makeModel();
    }

    public function allQuery(?array $search = [], ?int $skip = null, ?int $limit = null, mixed $order = null): Builder
    {
        $query = $this->model->newQuery();

        $this->addSearch($query, $search);

        if (!is_null($skip)) {
            $query->skip($skip);
        }

        if (!is_null($limit)) {
            $query->limit($limit);
        }

        if (!is_null($order)) {
            $this->addOrder($query, $order);
        }

        return $query;
    }

    protected function addOrder(Builder $query, array $orders): void
    {
        foreach ($orders as $order) {
            if (is_array($order)) {
                call_user_func_array([$query, 'orderBy'], $order);
            } else {
                $query->orderBy($order);
            }
        }
    }

    protected function addSearch(Builder $query, ?array $search): void
    {
        if ($search) {
            $search = Arr::only($search, static::SEARCH_FIELDS);
            $this->addSearchInternal($query, $search);
        }
    }

    protected function addSearchInternal(Builder $query, array $search): void
    {
        foreach ($search as $key => $value) {
            $query->where($key, $value);
        }
    }

    public function paginate(int $perPage, ?array $columns = ['*'], ?array $search = [], $order = null): LengthAwarePaginator
    {
        if (!$columns) {
            $columns = ['*'];
        }

        $query = $this->allQuery($search, null, null, $order);

        return $query->paginate($perPage, $columns);
    }

    /**
     * @return Model
     */
    abstract public function model(): string;

    /**
     * @throws Exception
     */
    public function makeModel(): Model
    {
        $model = $this->app->make($this->model());

        if (!$model instanceof Model) {
            throw new \Exception("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        return $this->model = $model;
    }

    public function create(array $input): Model
    {
        $model = $this->model->newInstance($input);

        $this->created = $model->save();

        return $model;
    }

    /**
     * @param int $id
     * @param array $columns
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|Model|null
     */
    public function find(string|int $id, array $columns = ['*'], $with = null)
    {
        $query = $this->model->newQuery();

        if (!is_null($with)) {
            $query->with($with);
        }

        return $query->find($id, $columns);
    }

    /**
     * @throws ModelNotFoundException
     */
    public function update(array $input, string|int|Model $id): Model
    {
        if ($id instanceof Model) {
            $model = $id;
        } else {
            $query = $this->model->newQuery();
            $model = $query->findOrFail($id);
        }

        $model->fill($input);

        $this->updated = $model->save();

        return $model;
    }

    /**
     * @throws ModelNotFoundException
     */
    public function delete(string|int|Model $id): bool
    {
        if ($id instanceof Model) {
            $model = $id;
        } else {
            $query = $this->model->newQuery();
            $model = $query->findOrFail($id);
        }

        return $model->delete() !== false;
    }

    public function wasCreated(): bool
    {
        return $this->created;
    }

    public function wasUpdated(): bool
    {
        return $this->updated;
    }
}
