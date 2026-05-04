<?php
namespace App\Repositories\Zone;

interface ZoneInterface{ 
    public function getAllActive();
    public function get();
    public function getFind($id);   
    public function store($request);
    public function update($request);
    public function delete($id);
}
