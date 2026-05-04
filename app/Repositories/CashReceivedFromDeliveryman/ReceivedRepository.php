<?php

namespace App\Repositories\CashReceivedFromDeliveryman;

use App\Enums\AccountHeads;
use App\Enums\UserType;
use App\Models\Backend\Account;
use App\Models\Backend\AccountHead;
use App\Models\Backend\BankTransaction;
use App\Models\Backend\CourierStatement;
use App\Models\Backend\DeliveryMan;
use App\Models\Backend\DeliverymanStatement;
use App\Models\Backend\Hub;
use App\Models\Backend\HubStatement;
use App\Models\Backend\Income;
use App\Models\Backend\Upload;
use App\Models\CashReceivedFromDeliveryman;
use App\Repositories\CashReceivedFromDeliveryman\ReceivedInterface;
use Carbon\Carbon;
use Database\Seeders\HubSeeder;
use Illuminate\Support\Facades\Auth;

class ReceivedRepository implements ReceivedInterface
{

    public function all()
    {
        return Income::where('hub_id', Auth::user()->hub_id)->orderByDesc('id')->get();
    }

    public function get($id)
    {
        return Income::find($id);
    }
    public function store($request)
    {
        try {

            //deliveryman  statements
            $deliveryman                           = DeliveryMan::find($request->delivery_man_id);

            $deliveryman_statment                  = new DeliverymanStatement();
            $deliveryman_statment->delivery_man_id = $request->delivery_man_id;
            $deliveryman_statment->hub_id          = auth()->user()->hub_id;
            $deliveryman_statment->type            = AccountHeads::INCOME;
            $deliveryman_statment->amount          = $request->amount;
            $deliveryman_statment->date            = $request->date . date(' H:i:s');

            $accountH                              = AccountHead::find(2);
            $deliveryman_statment->note            =  @$accountH->name;

            $deliveryman_statment->save();
            $deliveryman->current_balance      = ($deliveryman->current_balance + $request->amount);
            $deliveryman->save();

            //end balance check
            $account                   = Account::find($request->account_id);
            $income                    = new Income();
            $income->account_head_id   = 2;
            $income->from              = 2;
            $income->delivery_man_id   = $request->delivery_man_id;
            $income->hub_id            = auth()->user()->hub_id;
            $income->account_id        = $request->account_id;
            $income->amount            = $request->amount;
            $income->date              = Carbon::parse($request->date)->format('Y-m-d');
            $income->receipt           = $this->file($request->receipt, '');
            $income->note              = $request->note;
            $income->save();

            //add balance in accounts
            if ($income):
                $account->balance      = $account->balance + $request->amount;
                $account->save();
            endif;

            //add bank transaction
            if ($income && $account) {

                $bank_transaction                   =  new BankTransaction();
                $bank_transaction->account_id       =  $request->account_id;
                $bank_transaction->hub_id           =  auth()->user()->hub_id;
                $bank_transaction->type             =  AccountHeads::INCOME;
                $bank_transaction->amount           =  $income->amount;
                $bank_transaction->date             =  $request->date . date(' H:i:s');
                $bank_transaction->note             =  @$income->accounthead->name;
                $bank_transaction->income_id        =  $income->id;
                $bank_transaction->save();

                //add courier statements
                $courierStatement                       = new CourierStatement();
                $courierStatement->income_id            = $income->id;
                $courierStatement->amount               = $request->amount;
                $courierStatement->type                 = AccountHeads::INCOME;
                $courierStatement->date                 = date('Y-m-d H:i:s');
                $accountHn                              = AccountHead::find(2);
                $courierStatement->note                 = @$accountHn->name;
                $courierStatement->save();

                return true;
            } else {
                return false;
            }
        } catch (\Throwable $th) {

            return false;
        }
    }
    public function update($request)
    {

        return true;
    }
    public function delete($id)
    {

        try {

            $income = Income::with('upload')->find($id);

            //deliveryman  statements
            $deliveryman                           = DeliveryMan::find($income->delivery_man_id);
            $deliveryman_statment                  = new DeliverymanStatement();
            $deliveryman_statment->delivery_man_id = $income->delivery_man_id;
            $deliveryman_statment->hub_id          = $income->hub_id;
            $deliveryman_statment->type            = AccountHeads::EXPENSE;
            $deliveryman_statment->amount          = $income->amount;
            $deliveryman_statment->date            = $income->date . date(' H:i:s');
            $accountH                              = AccountHead::find($income->account_head_id);
            $deliveryman_statment->note            =  @$accountH->name;
            $deliveryman_statment->save();
            $deliveryman->current_balance = ($deliveryman->current_balance - $income->amount);
            $deliveryman->save();
            //end balance check

            $account = Account::find($income->account_id);
            //account  balance minus from account
            $account->balance          = ($account->balance - $income->amount);
            $account->save();

            //add courier statements
            $courierStatement                       = new CourierStatement();
            $courierStatement->income_id            = $income->id;
            $courierStatement->amount               = $income->amount;
            $courierStatement->type                 = AccountHeads::EXPENSE;
            $courierStatement->date                 = date('Y-m-d H:i:s');
            $accountHn                              = AccountHead::find($income->account_head_id);
            $courierStatement->note                 =  @$accountHn->name;
            $courierStatement->save();

            //bank transaction
            $bank_transaction                   =  new BankTransaction();
            $bank_transaction->account_id       =  $income->account_id;
            $bank_transaction->hub_id           =  $income->hub_id;
            $bank_transaction->type             =  AccountHeads::EXPENSE;
            $bank_transaction->amount           =  $income->amount;
            $bank_transaction->date             =  $income->date . date(' H:i:s');
            $bank_transaction->note             =  @$income->accounthead->name;
            $bank_transaction->income_id        =  $income->id;
            $bank_transaction->save();
            //end bank transaction

            if ($income->receipt != null) {
                Upload::destroy($income->upload->id);
                if (file_exists($income->upload->original))
                    unlink($income->upload->original);
            }
            $income->delete();
            return true;
        } catch (\Throwable $th) {
        }
    }

    // Request image Store in Upload Model and image copy file attach in public/upload/user folder.
    public function file($image, $image_id = '')
    {
        try {

            $image_name = '';
            if (!blank($image)) {
                $destinationPath       = public_path('uploads/income');
                $profileImage          = date('YmdHis') . "." . $image->getClientOriginalExtension();
                $image->move($destinationPath, $profileImage);
                $image_name            = 'uploads/income/' . $profileImage;
            }

            if (blank($image_id)) {
                $upload           = new Upload();
            } else {
                $upload           = Upload::find($image_id);
                if (file_exists($upload->original)) {
                    unlink($upload->original);
                }
            }

            $upload->original     = $image_name;
            $upload->save();
            return $upload->id;
        } catch (\Exception $e) {
            return false;
        }
    }
}
