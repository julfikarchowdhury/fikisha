<?php
namespace App\Repositories\ParcelCategory;
interface ParcelCategoryInterface
{
    public function all(int $status = null, array $column = [], int $paginate = null, string $orderBy = 'position');
    public function getFind($id);
    public function store($request);
    public function update($request);
    public function delete($id);
}