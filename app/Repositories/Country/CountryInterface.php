<?php

namespace App\Repositories\Country;

interface CountryInterface
{
        public function get();
        public function getFind($id);
        public function store($request);
        public function update($request, $id);
        public function delete($id);
}
