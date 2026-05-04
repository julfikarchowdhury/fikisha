<?php

namespace App\Repositories\ParcelCategory;

use App\Enums\Status;
use App\Models\Backend\ParcelCategory;
use App\Repositories\ParcelCategory\ParcelCategoryInterface;

class ParcelCategoryRepository implements ParcelCategoryInterface
{
    protected $model;
    public function __construct(ParcelCategory $model)
    {
        $this->model = $model;
    }

    public function all(int $status = null, array $column = [], int $paginate = null, string $orderBy = 'position')
    {
        $query = $this->model::query();
        if ($status !== null) {
            $query->where('status', $status);
        }
        if (!empty($column)) {
            $query->select($column);
        }
        $query->orderBy($orderBy, 'ASC');
        if ($paginate !== null) {
            return  $query->paginate($paginate);
        } else {
            return $query->get();
        }
    }

    public function getFind($id)
    {
        return $this->model::find($id);
    }

    public function store($request)
    {
        try {
            $model           = $this->model;
            $model->name     = $request->name;
            $model->position = $request->position;
            $model->status   = $request->status;
            $model->save();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function update($request)
    {
        try {
            $model           = $this->model::find($request->id);
            $model->name     = $request->name;
            $model->position = $request->position;
            $model->status   = $request->status;
            $model->save();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function delete($id)
    {
        return $this->model::destroy($id);
    }
}
