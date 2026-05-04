<?php

namespace App\Repositories\City;

interface CityInterface
{
        public function get();
        public function getFind($id);
        public function store($request);
        public function update($request, $id);
        public function delete($id);
        public function get_country();
}
