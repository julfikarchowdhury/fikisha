<?php

namespace App\Repositories\Province;

interface ProvinceInterface
{
    public function get();
    public function getActive();
    public function getFind($id);
    public function store($request);
    public function update($request);
    public function delete($id);
}
