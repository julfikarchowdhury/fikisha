<?php

namespace App\Traits;

trait BulkValidatorRequestTrait
{
    //Province request
    public function ProvinceRequest($request)
    {
        $data = null;
        if ($request->data):
            foreach ($request->data as $key => $value) {
                if (!empty($value[0]) && !empty($value[1])):
                    $data[$key]['name']          = $value[0];
                    $data[$key]['province_code'] = $value[1];
                    $data[$key]['position']      = $value[2];
                    $data[$key]['description']   = $value[3];
                endif;
            }
        endif;
        return $data;
    }
    //end province request
}
