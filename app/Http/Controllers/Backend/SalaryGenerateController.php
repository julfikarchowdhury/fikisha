<?php

namespace App\Http\Controllers\Backend;

use App\Exports\Receiver as ExportsReceiver;
use App\Http\Controllers\Controller;
use App\Http\Requests\Payroll\AutoGenerateRequest;
use App\Http\Requests\Payroll\StoreRequest;
use App\Models\Backend\Payroll\SalaryGenerate;
use App\Models\Backend\Receiver;
use App\Models\Subscribe;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Repositories\Salary\SalaryInterface;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class SalaryGenerateController extends Controller
{

    protected $repo;
    public function __construct(SalaryInterface $repo)
    {
        $this->repo  = $repo;
    }

    public function index(){

        $salaries   = $this->repo->salaries();
        return view('backend.payroll.salary_generate',compact('salaries'));
    }

    public function salaryAutoGenerate(AutoGenerateRequest $request){
        if($this->repo->autogenerate($request)):
            Toastr::success('Salary Generated successfully.',__('message.success'));
            return redirect()->route('salary.generate.index');
        else:
            Toastr::error('Something went wrong.',__('message.error'));
            return redirect()->back();
        endif;
    }

    public function salaryGenerateDelete($id){
        if($this->repo->salaryGenerateDelete($id)):
            Toastr::success('Salary Generate Deleted successfully.',__('message.success'));
            return redirect()->route('salary.generate.index');
        else:
            Toastr::error('Something went wrong.',__('message.error'));
            return redirect()->back();
        endif;
    }

    public function create(){
        return view('backend.payroll.create');
    }

    public function store(StoreRequest $request){
      
        $user  = User::find($request->user_id);
        $salaryGenerated            = SalaryGenerate::where('user_id',$request->user_id)->where('month',$request->month)->first();
        if($salaryGenerated):
            Toastr::error('Already salary generated in this month.',__('message.error'));
            return redirect()->back();
        endif;
        if($this->repo->salaryGenerateStore($request)):
            Toastr::success('Salary created successfully.',__('message.success'));
            return redirect()->route('salary.generate.index');
        else:
            Toastr::error('Something went wrong.',__('message.error'));
            return redirect()->back();
        endif;
    }


    public function edit($id){
        $singleSalary  = $this->repo->singleSalaryGenerate($id);
        return view('backend.payroll.edit',compact('singleSalary'));
    }

    public function update(Request $request){
        Validator::make($request->all(),[
            'user_id'   => ['required'],
            'amount'    => ['required','numeric']
        ])->validate();
        if($this->repo->salaryGenerateUpdate($request)):
            Toastr::success('Salary updated successfully.',__('message.success'));
            return redirect()->route('salary.generate.index');
        else:
            Toastr::error('Something went wrong.',__('message.error'));
            return redirect()->back();
        endif;
    }

    public function subscribe(){
        $subscribes   = Subscribe::paginate(15);
        return view('backend.subscribe',compact('subscribes'));
    }
    public function receivers(Request $request){
        $receivers   = Receiver::paginate(15);
        return view('backend.receivers',compact('receivers','request'));
    }
    public function receiverExport(){
        $receivers   = Receiver::select('name','phone','address')->get();
        return Excel::download(new ExportsReceiver($receivers),'Receivers-'.Carbon::now()->format('d-m-Y His').'.xlsx');
    }
}
