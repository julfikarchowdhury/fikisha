<?php
namespace App\Repositories\LeaveType;

interface LeaveTypeInterface
{
    public function get(); 
    public function getActiveAll();
    public function getFind($id);
    public function store($request);
    public function update($id,$request);
    public function delete($id); 
}
