<?php

namespace App\Models\Backend\Payroll;

use App\Enums\SalaryStatus;
use App\Enums\Status;
use App\Models\User;
use App\Models\Backend\Salary;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class SalaryGenerate extends Model
{
    use HasFactory,LogsActivity;

   protected $fillable = [
        'user_id',
        'month',
        'amount',
        'status',
        'due',
        'advance',
        'note'
    ];


    public function getActivitylogOptions(): LogOptions
    {

        $logAttributes = [
            'user.name',
            'month',
            'amount',
            'due',
            'advance',
            'note',
        ];
        return LogOptions::defaults()
        ->useLogName('Salary Generate')
        ->logOnly($logAttributes)
            ->setDescriptionForEvent(fn(string $eventName) => "{$eventName}");
    }


    // Get single row in User table.
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    //for status index blade
    public function getMyStatusAttribute()
    {
        $totalPaid = $this->payments()->sum('amount')?? 0;
        $status =__('SalaryStatus.'.SalaryStatus::UNPAID);
        if(!blank($this->payments)):
            
            if ($totalPaid > 0 && $this->amount > $totalPaid ):
               $status  = '<span class="badge badge-pill badge-warning">'.__('SalaryStatus.'.SalaryStatus::PARTIAL_PAID).'</span>';
            elseif($totalPaid <= 0):
              $status  = '<span class="badge badge-pill badge-danger">'.__('SalaryStatus.'.SalaryStatus::UNPAID).'</span>'; 
            elseif($totalPaid >= $this->amount):
                $status  = '<span class="badge badge-pill badge-success">'.__('SalaryStatus.'.SalaryStatus::PAID).'</span>';
            endif;
        else: 
            $status = '<span class="badge badge-pill badge-danger">'.__('SalaryStatus.'.SalaryStatus::UNPAID).'</span>';
        endif;

    
        return $status;
    }

    public function salary()
    {
        return $this->hasMany(Salary::class,'user_id','id');
    }

    public function payments(){
        return $this->hasMany(Salary::class,'user_id','user_id')->where('month',$this->month);
    }

   

}
