<?php

namespace Database\Seeders\hrm;

use App\Models\Backend\HRM\Weekend;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WeekendSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $weekends=[
            [ 'name'=>'Saturday' ,'position'=>1, 'is_weekend'=>0],
            [ 'name'=>'Sunday'   ,'position'=>2, 'is_weekend'=>0],
            [ 'name'=>'Monday'   ,'position'=>3, 'is_weekend'=>0],
            [ 'name'=>'Tuesday'  ,'position'=>4, 'is_weekend'=>0],
            [ 'name'=>'Wednesday','position'=>5, 'is_weekend'=>0],
            [ 'name'=>'Thursday' ,'position'=>6, 'is_weekend'=>0],
            [ 'name'=>'Friday'   ,'position'=>7, 'is_weekend'=>1],
        ];

        foreach ($weekends as $weekend) {
            Weekend::create([
                'name'          =>$weekend['name'],
                'position'      =>$weekend['position'],
                'is_weekend'    =>$weekend['is_weekend']
            ]);
        }
    }
}
