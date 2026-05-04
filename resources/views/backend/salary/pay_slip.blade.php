<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title> Salary pay slip | print</title>
    <link rel="shortcut icon" href="{{ static_asset(settings()->favicon_image) }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ static_asset('backend/') }}/css/payslip.css">
</head>

<body>
    <div class="print" style="text-align: right">
        <button type="button" class="btn-danger" onclick="window.close()">{{ __('menus.cancel') }}</button>
    </div>


    <div id="main-slip">
        <div>
            <table width="100%"  align="center" cellpadding="0" cellspacing="0"     >
                <tbody>
                    <tr>
                        <td style="width:47%!important" >
                            <img alt="Logo" src="{{ static_asset(settings()->logo_image) }}" class="logo"  style="width:140px;object-fit:contain">
                        </td>
                        <td style="border-left: 1px solid;padding-bottom:0px;padding-top:0px">
                            <span><b style="letter-spacing: 3px;"></b> {{ settings()->name }}</span><br>
                            <span>{{ settings()->email }}</span><br>
                            <span style="padding-top: 3px; "> {{ settings()->phone }} </span><br>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="invoice" id="printablediv">
            
                    <h3 style="text-align: center;margin-top:5px;margin-bottom:0px"> {{ __('salary.pay_slip') }}  </h3>  
                    <div class="row" style="margin-top: 5px">
                        <div class="col-sm-12 table-responsive" style="p-0">
                            <table class="userInfo" style="margin:0px" cellspacing="0">
                                 
                                <tr> 
                                    <td>Date:</td>
                                    <td> {{ dateFormat($salary->date) }}</td>
                                </tr> 
                                <tr>
                                    <td > {{ __('salary.pay_period') }}  </td>
                                    <td> {{ @\Carbon\Carbon::createFromFormat('Y-m', $salary->month)->format('M Y') }}  </td>
                                </tr>
                                <tr> 
                                    <td>{{ __('salary.employee_name') }}:</td>
                                    <td>{{ $salary->user->name }}</td>
                                </tr>  
                                <tr  >
                                    <td  > {{ __('salary.email') }}  </td>
                                    <td>  {{ $salary->user->email }} </td> 
                                </tr>
                                <tr>
                                    <td>{{ __('salary.hub') }}</td>
                                    <td>{{ @$salary->user->hub->name }}</td>
                                </tr>
                                <tr  >
                                    <td  > {{ __('salary.designation') }} </td>
                                    <td> {{ @$salary->user->designation->title }} </td> 
                                </tr>
                                <tr  >
                                    <td> {{ __('salary.department') }}  </td>
                                    <td> {{ @$salary->user->department->title }}  </td>
                                </tr>
                              
                                @php
                                    $thismonthSalary = App\Models\Backend\Payroll\SalaryGenerate::where('user_id',$salary->user_id)->where('month',$salary->month)->first(); 
                                @endphp
                                <tr>
                                    <td>  {{\Carbon\Carbon::parse($salary->month)->monthName  }} {{ __('salary.salary') }} </td>
                                    <td>
                                        {{ @settings()->currency }}{{ @$thismonthSalary->amount?? 0 }}
                                    </td>
                                </tr> 
                              
                                <tr style="background-color: rgb(0 166 75 / 38%);color:rgb(0, 0, 0);font-weight:bold">
                                    <td> {{ __('salary.salary_paid') }} </td>
                                    <td> {{ settings()->currency }}{{ $salary->amount }} </td>
                                </tr> 
                                  <tr style="background-color: rgb(0 166 75 / 38%);color:rgb(0, 0, 0);font-weight:bold">
                                    <td> Total Paid : </td>
                                    <td>{{ @settings()->currency }}{{ @$thismonthSalary->payments->sum('amount')}}  </td>
                                </tr> 
                                  <tr style="background-color: rgb(176, 14, 14);color:white;font-weight:bold">
                                    <td> {{\Carbon\Carbon::parse($salary->month)->monthName  }}  Due : </td>
                                    <td>{{ @settings()->currency }}{{ @$thismonthSalary->amount - @$thismonthSalary->payments->sum('amount') }}  </td>
                                </tr> 
                            </table>
                           
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script type="text/javascript">
        window.print();
    </script>

</body>

</html>
