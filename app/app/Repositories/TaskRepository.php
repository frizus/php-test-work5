<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;

final class TaskRepository extends AbstractRepository
{
    public const array ORDER_FIELDS = [
        'id',
        'name',
        'description',
        'finished_at',
    ];

    public const array SEARCH_FIELDS = [
        'status',
    ];

    protected function addSearch(Builder $query, ?array $search): void
    {
        if (!$search) {
            return;
        }

        if (key_exists('status', $search)) {
            $query->whereNull('finished_at', not: (bool)$search['status']);
        }
    }

    public function model(): string
    {
        return Task::class;
    }
}
