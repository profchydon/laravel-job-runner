<?php

namespace App\Repositories\Contracts;

interface BaseRepositoryInterface
{
    public function attachModel();

    public function all($paginate);

    public function allOrdered($column, $order);

    public function create($data);

    public function exist($column, $data): bool;

    public function first($column, $data);

    public function get($column, $data, $fieldsToFetch = ['*'], $chronological = false);

    public function firstWithRelation($column, $data, $models = []);

    public function getWithRelation($column, $data, $models = [], $chronological = false);

    public function firstLike($column, $data);

    public function manyLike($column, $data);

    public function getMany($column, array $data);

    public function getWithTrashed($column, $data);

    public function getManyWithTrashed($column, array $data);

    public function deleteById($id);

    public function softDeleteById($id, array $data);

    public function update($column, $value, $data);

    public function updateMultiple($column, $value, $data);

    public function updateOrCreate(array $condition, $data);

    public function firstOrCreate(array $condition, $data);

    public function updateById($id, $updateData);

    public function updateByUuid(string $uuid, $updateData);
}
