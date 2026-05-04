<?php 
namespace App\Repositories\FrontWeb\Slider;
interface SliderInterface {
    public function all();
    public function get();
    public function getFind($id);
    public function store($request);
    public function update($id,$request);
    public function delete($id); 
}
