<?php

namespace App\Console\Commands;

use App\Enums\BooleanStatus;
use App\Enums\ParcelStatus; 
use App\Models\Backend\Parcel;
use App\Repositories\Invoice\InvoiceInterface;
use App\Repositories\Merchant\MerchantInterface;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class Invoice extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Merchant Schedule Invoice generate';

    /**
     * Execute the console command.
     *
     * @return int
     */

    protected $invoiceRepo;
    protected $merchantRepo;
    public function __construct(InvoiceInterface $invoiceRepo, MerchantInterface $merchantRepo)
    {
        parent::__construct();
        $this->invoiceRepo  = $invoiceRepo;
        $this->merchantRepo = $merchantRepo;
    }

    public function handle()
    {
         
        $parcels = Parcel::where(function($query){
            $query->where('status',ParcelStatus::DELIVERED);
            $query->orWhere('is_returned',BooleanStatus::YES);
        })->whereNull('invoice_id')->get(); 
        
        // Group parcels by merchant_id using keyBy
        $parcelsByMerchantId = $parcels->keyBy('merchant_id');
        $merchantIds         = $parcelsByMerchantId->keys();
        //auto merchant invoice generate
        foreach ($merchantIds as $merchant_id) { 
            $this->invoiceRepo->store($merchant_id);
        }
    }
}
