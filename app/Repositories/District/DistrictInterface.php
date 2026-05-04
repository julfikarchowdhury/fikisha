<?php

namespace App\Repositories\District;

interface DistrictInterface
{
        public function get();
        public function getFind($id);
        public function store($request);
        public function update($request, $id);
        public function delete($id);
        public function get_country();
        public function get_city();
        public function countryByCity($id);
}
