<?php

namespace Database\Seeders\hrm;

use App\Models\Backend\HRM\LeaveType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LeaveTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $types = [
            "Maternity leave",
            "Others",
            "Sick Leave",
            "Vacation Leaves"
        ];
        foreach ($types as $key=>$value) {
             $leaveType              = new LeaveType(); 
             $leaveType->name        = $value;
             $leaveType->position    = $key+1;
             $leaveType->save();
        }
    }
}
