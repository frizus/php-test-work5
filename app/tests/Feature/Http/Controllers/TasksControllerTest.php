<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\AssertableJsonString;
use Tests\TestCase;

/*
 * Использовать sqlite БД, подключенную в .env.testing
 * и @see \Illuminate\Foundation\Testing\DatabaseMigrations
 * вместо @see \Illuminate\Foundation\Testing\DatabaseTransactions
 */
class TasksControllerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic feature test example.
     */
    public function test_index_order_filter_store_update_destroy(): void
    {
        // нагенерировать несколько задач
        $ids = $this->storeMultipleRows();

        // чтобы проверить, что при сортировке
        // id по убыванию, первой будет - последняя созданная
        $response = $this->getJson('/api/tasks?order=id&direction=desc');
        $response->assertStatus(200);
        $this->firstColumnIdMatches(
            end($ids),
            $response->decodeResponseJson()
        );

        // удалить
        foreach ($ids as $id) {
            $this->destroyRow($id);
        }

        // чтобы проверить, что фильтр по "статусу" (заполненности поля finished_at) работает
        // добавляем так записи, чтобы последней при сортировке
        // по id по убыванию
        // была бы задача без заполненной finished_at
        $ids = [
            $this->storeRow(['name' => 'test', 'finished_at' => '2025-12-12']),
            'this_one' => $this->storeRow(['name' => 'test', 'finished_at' => '2025-12-11']),
            $this->storeRow()
        ];

        $response = $this->getJson('/api/tasks?order=id&direction=desc&status=1');
        $response->assertStatus(200);

        $this->firstColumnIdMatches(
            $ids['this_one'],
            $response->decodeResponseJson()
        );

        // Удалить
        foreach ($ids as $id) {
            $this->destroyRow($id);
        }
    }

    public function test_store_and_update(): void
    {
        $id = $this->storeRow();
        $this->updateRow($id);
        $this->destroyRow($id);
    }

    /**
     * Проверка валидации
     * Создание задачи без обязательного поля name
     */
    public function test_store_empty(): void
    {
        $this->storeRow([]);
    }

    protected function storeRow(array $data = ['name' => 'test']): ?string
    {
        $response = $this->postJson('/api/tasks', $data);

        if (!$data) {
            $response->assertStatus(422);
            return null;
        }

        $response->assertStatus(201);
        $content = $response->decodeResponseJson();
        $this->compareJson($data, $content);
        $id = $content['id'];
        $this->assertIsInt($id);

        return $id;
    }

    protected function updateRow(string $id, array $data = ['name' => 'test2']): void
    {
        $response = $this->patchJson('/api/tasks/' . $id, $data);
        $response->assertStatus(200);
        $content = $response->decodeResponseJson();
        $this->compareJson($data, $content);
    }

    protected function destroyRow(string $id): void
    {
        $response = $this->deleteJson('/api/tasks/' . $id);
        $response->assertStatus(200);
    }

    protected function compareJson(array $data, AssertableJsonString $content): void
    {
        foreach ($data as $key => $value) {
            $this->assertTrue(isset($content[$key]));
            $this->assertEquals($value, $content[$key]);
        }
    }

    protected function firstColumnIdMatches(?string $id, AssertableJsonString $content): void
    {
        $this->assertTrue(isset($content['data']));
        foreach ($content['data'] as $value) {
            $this->assertTrue(isset($value['id']));
            $this->assertEquals($value['id'], $id);
            break;
        }
    }

    protected function storeMultipleRows(int $count = 3): array
    {
        $ids = [];
        for ($i = 1; $i <= $count; $i++) {
            $ids[] = $this->storeRow();
        }

        return $ids;
    }
}
