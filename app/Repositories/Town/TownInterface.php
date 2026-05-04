<?php

namespace App\Repositories\Town;

interface TownInterface
{
        public function get();
        public function getAll();
        public function getFind($id);
        public function store($request);
        public function update($request, $id);
        public function delete($id);
        public function get_country();
        public function get_city();
        public function get_district();
        public function cityByDistrict($id);
        public function districtByTown($id);
        public function townByPortalCode($id);
}
