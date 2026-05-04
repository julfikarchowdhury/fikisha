<?php
namespace App\Repositories\SenderCustomer;

interface SenderCustomerInterface {

    public function all();
    public function senderCustomer($sender_id);
    public function getAll();
    public function get($id);
    public function store($request);
    public function update($request, $id);
    public function delete($id);

}
