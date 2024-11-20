<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\ApiTrait;
use App\Http\Requests\TaskDestroyRequest;
use App\Http\Requests\TaskIndexRequest;
use App\Http\Requests\TaskStoreRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Http\Resources\TaskResource;
use App\Repositories\TaskRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Arr;

final class TasksController extends Controller
{
    use ApiTrait;

    public function __construct(
        protected TaskRepository $repository
    ) {
    }

    public function index(
        TaskIndexRequest $request
    ): AnonymousResourceCollection {
        return TaskResource::collection(
            $this->repository->paginate(
                10,
                null,
                $request->filterParams(),
                $request->getOrder()
            )
        );
    }

    public function store(
        TaskStoreRequest $request
    ): JsonResponse {
        $model = $this->repository->create(
            $request->validationData()
        );

        return $this->repository->wasCreated()
            ? response()->json(
                new TaskResource($model),
                201
            )
            : $this->error('Не удалось создать задачу');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        TaskUpdateRequest $request
    ): JsonResponse {
        $validationData = $request->validationData();
        $data = Arr::except($validationData, 'id');

        $model = $this->repository->update(
            $data,
            $validationData['id']
        );

        return $this->repository->wasUpdated()
            ? response()->json(new TaskResource($model))
            : $this->error('Не удалось обновить задачу');
    }

    /**
     * Remove the specified resource from storage.
     * @throws ModelNotFoundException
     */
    public function destroy(
        TaskDestroyRequest $request
    ): JsonResponse {
        $wasDeleted = $this->repository->delete(
            $request->validationData()['id']
        );

        return $wasDeleted
            ? $this->success()
            : $this->error('Не удалось удалить задачу');
    }
}
