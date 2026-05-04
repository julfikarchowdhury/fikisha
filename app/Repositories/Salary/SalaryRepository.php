<?php

namespace App\Repositories\Salary;

use App\Enums\AccountHeads;
use App\Enums\AttendanceStatus;
use App\Enums\UserType;
use App\Enums\WeekendStatus;
use App\Models\Backend\Account;
use App\Models\Backend\BankTransaction;
use App\Models\Backend\HRM\Attendance;
use App\Models\Backend\HRM\DutySchedule;
use App\Models\Backend\HRM\Holiday;
use App\Models\Backend\HRM\Weekend;
use App\Models\Backend\Salary;
use App\Models\User;
use App\Models\Backend\Payroll\SalaryGenerate;
use App\Repositories\Salary\SalaryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SalaryRepository  implements SalaryInterface
{
    public function salaries()
    {
        return SalaryGenerate::orderBy('id', 'desc')->paginate(10);
    }

    public function monthSalary($salary)
    {
        return SalaryGenerate::where(['user_id' => $salary->user_id, 'month' => $salary->month])->first();
    }
    public function salaryFilter($request)
    {
        $salary  = SalaryGenerate::with('user')->where(function ($query) use ($request) {
            if ($request->month) {
                $query->where('month', $request->month);
            }
            if ($request->user_id):
                $query->where('user_id', $request->user_id);
            endif;
        })->orderBy('id', 'desc')->paginate(10);
        return $salary;
    }

    public function autogenerate($request)
    {
        try {
            DB::beginTransaction();
            $users          = User::whereIn('user_type', [UserType::ADMIN])->get();
            $startOfMonth   = Carbon::parse($request->month)->startOfMonth()->format('Y-m-d');
            $endOfMonth     = Carbon::parse($request->month)->endOfMonth()->format('Y-m-d');
            $totalMonthDays = Carbon::parse($request->month)->daysInMonth;

            foreach ($users as  $user) {

                $basic_salary_per_day           =  ($user->salary / $totalMonthDays);
                $attendances                    =  Attendance::where('status', AttendanceStatus::CHECK_OUT)->whereNot('check_out', null)->where('user_id', $user->id)->whereBetween('date', [$startOfMonth, $endOfMonth])->get();
                $totalDaysSalary                =  ($attendances->count() * $basic_salary_per_day); // ( days count * per day salary )
                $totalOverTime                  =  ($attendances->sum('over_stay_time') / 60); //hours 
                $totalOverTimeSalary            =  ($totalOverTime * $user->per_hour_salary); //over time salary 
                $totalSalary                    =  ($totalDaysSalary + $totalOverTimeSalary); //total salary

                $salaryGenerated                = SalaryGenerate::where('user_id', $user->id)->where('month', $request->month)->first();
                if (!$salaryGenerated):
                    $salaryGenerate             = new SalaryGenerate();
                    $salaryGenerate->user_id    = $user->id;
                    $salaryGenerate->month      = $request->month;
                    $salaryGenerate->amount     = $totalSalary;
                    $salaryGenerate->note       = 'Auto Generated';
                    $salaryGenerate->save();

                    $user->balance              = ($user->balance + $salaryGenerate->amount);
                    $user->save();
                endif;
            }
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }

    public function salaryGenerateStore($request)
    {
        try {
            DB::beginTransaction();
            $user  = User::find($request->user_id);

            $startOfMonth   = Carbon::parse($request->month)->startOfMonth()->format('Y-m-d');
            $endOfMonth     = Carbon::parse($request->month)->endOfMonth()->format('Y-m-d');
            $totalMonthDays = Carbon::parse($request->month)->daysInMonth;

            $basic_salary_per_day           =  ($user->salary / $totalMonthDays);
            $attendances                    =  Attendance::where('status', AttendanceStatus::CHECK_OUT)->whereNot('check_out', null)->where('user_id', $user->id)->whereBetween('date', [$startOfMonth, $endOfMonth])->get();
            $totalDaysSalary                =  ($attendances->count() * $basic_salary_per_day); // ( days count * per day salary )
            $totalOverTime                  =  ($attendances->sum('over_stay_time') / 60); //hours
            $totalOverTimeSalary            =  ($totalOverTime * $user->per_hour_salary); //over time salary 
            $totalSalary                    =  ($totalDaysSalary + $totalOverTimeSalary); //total salary

            $salaryGenerated            = SalaryGenerate::where('user_id', $request->user_id)->where('month', $request->month)->first();
            if (!$salaryGenerated):
                $salaryGenerate             = new SalaryGenerate();
                $salaryGenerate->user_id    = $request->user_id;
                $salaryGenerate->month      = $request->month;
                $salaryGenerate->amount     = $totalSalary;
                $salaryGenerate->note       = $request->note;
                $salaryGenerate->save();

                $user->balance              = ($user->balance + $salaryGenerate->amount);
                $user->save();
            endif;
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }

    public function singleSalaryGenerate($id)
    {
        return SalaryGenerate::find($id);
    }


    public function salaryGenerateUpdate($request)
    {
        try {
            DB::beginTransaction();
            $salaryGenerate             = SalaryGenerate::find($request->id);
            $user                       = User::find($salaryGenerate->user_id);
            $user->balance              = ($user->balance - $salaryGenerate->amount);
            $user->save();

            $salaryGenerate->user_id    = $request->user_id;
            $salaryGenerate->amount     = $request->amount;
            $salaryGenerate->note       = $request->note;
            $salaryGenerate->save();

            $updateUser                 = User::find($request->user_id);
            $updateUser->balance        = ($updateUser->balance + $salaryGenerate->amount);
            $updateUser->save();
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }


    public function salaryGenerateDelete($id)
    {
        try {
            $salary             =  SalaryGenerate::find($id);
            $user               =  User::find($salary->user_id);
            $user->balance      =  ($user->balance - $salary->amount);
            $user->save();
            $salary->delete();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    //end salary generate
    public function all()
    {
        return Salary::orderBy('id', 'desc')->paginate(10);
    }
    public function get($id)
    {
        return Salary::find($id);
    }
    public function store($request)
    {
        try {
            $salary               = new Salary();
            $salary->user_id      = $request->user_id;
            $salary->account_id   = $request->account_id;
            $salary->month        = $request->month;
            $salary->date         = $request->date;
            $salary->amount       = $request->amount;
            $salary->note         = $request->note;
            $salary->save();

            $account                           = Account::find($request->account_id);
            $account->balance                  = ($account->balance - $request->amount);
            $account->save();

            $user           = User::find($salary->user_id);
            $user->balance  = ($user->balance - $request->amount);
            $user->save();

            $transaction                       = new BankTransaction();
            $transaction->account_id           = $request->account_id;
            $transaction->type                 = AccountHeads::EXPENSE;
            $transaction->amount               = $request->amount;
            $transaction->date                 = $request->date;
            $transaction->note                 = __('salary.user_salary_expense');
            $transaction->save();
            return $salary;
        } catch (\Throwable $th) {
            return false;
        }
    }
    public function edit($id)
    {
        return Salary::find($id);
    }
    public function update($id, $request)
    {
        try {
            DB::beginTransaction();
            $salary  = Salary::find($id);
            //income
            $transaction                       = new BankTransaction();
            $transaction->account_id           = $salary->account_id;
            $transaction->type                 = AccountHeads::INCOME;
            $transaction->amount               = $salary->amount;
            $transaction->date                 = $salary->date;
            $transaction->note                 = __('salary.user_salary_expense');
            $transaction->save();


            $account            = Account::find($salary->account_id);
            $account->balance   = ($account->balance + $salary->amount);
            $account->save();

            $user           = User::find($salary->user_id);
            $user->balance  = ($user->balance + $salary->amount);
            $user->save();


            //income
            $salary->user_id      = $request->user_id;
            $salary->account_id   = $request->account_id;
            $salary->month        = $request->month;
            $salary->date         = $request->date;
            $salary->amount       = $request->amount;
            $salary->note         = $request->note;
            $salary->save();

            $account            = Account::find($request->account_id);
            $account->balance   = ($account->balance - $request->amount);
            $account->save();


            $updateUser           = User::find($request->user_id);
            $updateUser->balance  = ($updateUser->balance - $request->amount);
            $updateUser->save();


            $transaction                       = new BankTransaction();
            $transaction->account_id           = $request->account_id;
            $transaction->type                 = AccountHeads::EXPENSE;
            $transaction->amount               = $request->amount;
            $transaction->date                 = $request->date;
            $transaction->note                 = __('salary.user_salary_expense');
            $transaction->save();
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }
    public function delete($id)
    {
        try {
            $salary             =  Salary::find($id);
            $account            = Account::find($salary->account_id);
            $account->balance   = ($account->balance + $salary->amount);
            $account->save();

            $updateUser           = User::find($salary->user_id);
            $updateUser->balance  = ($updateUser->balance + $salary->amount);
            $updateUser->save();

            $transaction                       = new BankTransaction();
            $transaction->account_id           = $salary->account_id;
            $transaction->type                 = AccountHeads::INCOME;
            $transaction->amount               = $salary->amount;
            $transaction->date                 = $salary->date;
            $transaction->note                 = __('salary.user_salary_income');
            $transaction->save();
            $salary->delete();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}
