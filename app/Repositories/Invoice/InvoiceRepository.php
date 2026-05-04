<?php

namespace App\Repositories\Invoice;

use App\Enums\BooleanStatus;
use App\Enums\InvoiceStatus;
use App\Enums\ParcelStatus;
use App\Enums\UserType;
use App\Models\Backend\Merchant;
use App\Models\Backend\Merchantpanel\Invoice;
use App\Models\Backend\Merchantpanel\InvoiceParcel;
use App\Models\Backend\Parcel;
use App\Helpers\DeliveryChargeHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Invoice\InvoiceInterface;
use Illuminate\Support\Str;

class InvoiceRepository implements InvoiceInterface
{
    //merchant panel invoice
    public function get()
    {
        return Invoice::where('merchant_id', Auth::user()->merchant->id)->orderByDesc('id')->paginate(10);
    }

    public function getPaidInvoices()
    {
        return Invoice::where('status', InvoiceStatus::PAID)->orderByDesc('id')->paginate(10, ['*'], 'paid_invoice_page');
    }

    public function getProcessInvoices()
    {
        return Invoice::where('status', InvoiceStatus::PROCESSING)->orderByDesc('id')->paginate(10, ['*'], 'processing_invoice_page');
    }

    public function getUnpaidInvoices()
    {
        return Invoice::where('status', InvoiceStatus::UNPAID)->orderByDesc('id')->paginate(10, ['*'], 'unpaid_invoice_page');
    }


    public function InvoiceDetails($invoiceId)
    {
        return Invoice::where(['merchant_id' => Auth::user()->merchant->id, 'invoice_id' => $invoiceId])->first();
    }

    public function store($merchant_id)
    {
        try {

            $parcels = Parcel::where('merchant_id', $merchant_id)->where(function ($query) {
                $query->where('status', ParcelStatus::DELIVERED);
                $query->orWhere('is_returned', BooleanStatus::YES);
            })->whereNull('invoice_id')->get();

            $merchantFind         = Merchant::find($merchant_id);
            $merchant_date        = strtotime(\Carbon\Carbon::today()->subDays($merchantFind->payment_period)->format('d-m-Y'));
            $invoiceFind          = Invoice::where('merchant_id', $merchantFind->id)->get()->last();
            if ($invoiceFind) :
                $strtotime        = strtotime(\Carbon\Carbon::parse($invoiceFind->created_at)->format('d-m-Y'));
            else:
                $strtotime        = $merchant_date;
            endif;

            $invoiceAlreadyGenerated = Invoice::where('merchant_id', $merchant_id)
                ->whereBetween('created_at', [\Carbon\Carbon::today()->startOfDay(), \Carbon\Carbon::today()->endOfDay()])
                ->get()
                ->last();
            if ($strtotime <= $merchant_date && !$invoiceAlreadyGenerated) :
                if ($parcels->count() > 0) {
                    $invoice                  = new Invoice();
                    $invoice->merchant_id     = $merchant_id;
                    $invoice->invoice_id      = $this->invoiceId($merchant_id);
                    $invoice->invoice_date    = Carbon::today()->format('d-m-Y');
                    $invoice->total_charge    = (($parcels->sum('total_delivery_amount') + $parcels->sum('vat_amount') - $parcels->sum('discount_amount')));
                    $invoice->current_payable = $parcels->sum('current_payable');
                    $invoice->save();

                    foreach ($parcels as $key => $parcel) {

                        $discountAmount   = $parcel->discount_amount ?? 0;
                        $vatAmount        = $parcel->vat_amount ?? 0;

                        $totalShippingFee = $parcel->total_delivery_amount;
                        $totalShippingFee = ($totalShippingFee - $discountAmount);
                        $totalShippingFee = ($totalShippingFee + $vatAmount);

                        $invoiceParcel = new InvoiceParcel();
                        $invoiceParcel->invoice_id            = $invoice->id;
                        $invoiceParcel->parcel_id             = $parcel->id;
                        $invoiceParcel->parcel_status         = $parcel->status;
                        $invoiceParcel->total_delivery_amount = $parcel->total_delivery_amount;
                        $invoiceParcel->vat_amount            = $vatAmount;
                        $invoiceParcel->discount_amount       = $discountAmount;
                        $invoiceParcel->total_shipping_fee    = $totalShippingFee; // total shipping cost
                        $invoiceParcel->current_payable       = $parcel->current_payable; // customer need to pay courier
                        $invoiceParcel->save();
                        // set invoice_id parcel
                        $parcel->invoice_id = $invoice->id;
                        $parcel->save();
                    }
                }
            endif;
        } catch (\Throwable $th) {
            return false;
        }
    }

    private function invoiceId($merchant_id)
    {
        $merchant          = Merchant::find($merchant_id);
        $merchantId        = $merchant->id;
        $prefix            = Str::upper(settings()->invoice_prefix) . '-';
        $invoicecount      = Invoice::all()->count();
        $invoice_id        = $prefix . $merchantId . ($invoicecount + 1);
        return $invoice_id;
    }

    //admin panel merchant invoice
    public function merchantInvoiceGet($merchantId)
    {
        return Invoice::where('merchant_id', $merchantId)->orderByDesc('id')->paginate(10);
    }

    public function merchantInvoiceDetails($merchantId, $invoiceId)
    {
        return Invoice::where(['merchant_id' => $merchantId, 'invoice_id' => $invoiceId])->first();
    }

    public function statusUpdate($request, $merchant_id)
    {

        try {
            $invoice  = Invoice::where([
                'id' => $request->id,
                'merchant_id' => $merchant_id,
                'invoice_id' => $request->invoice_id
            ])->first();

            if ($invoice) {
                $invoice->status  = $request->status;
                $invoice->save();
                foreach ($invoice->invoiceParcels as $value) {
                    $parcel = Parcel::find($value->parcel_id);
                    $parcel->payment_status = $request->status;
                    if ((int) $request->status === InvoiceStatus::PAID) {
                        $isMarketplaceStatus = in_array((int) $parcel->status, [
                            ParcelStatus::MARKETPLACE_PENDING,
                            ParcelStatus::MARKETPLACE_SENDER_PAYMENT_AWAITING,
                            ParcelStatus::MARKETPLACE_RECEIVER_PAYMENT_AWAITING,
                            ParcelStatus::MARKETPLACE_ACCEPTED,
                            ParcelStatus::MARKETPLACE_PICKED_UP,
                            ParcelStatus::MARKETPLACE_DELIVERED,
                            ParcelStatus::MARKETPLACE_CANCELLED,
                        ], true);

                        if ($isMarketplaceStatus) {
                            $distanceKm = (float) ($parcel->distance_km ?? 0);
                            $weightKg = (float) ($parcel->total_weight ?? $parcel->weight ?? 0);
                            $whoPays = (int) ($parcel->who_pays_either ?? 0);
                            $breakdown = (new DeliveryChargeHelper())->marketplacePricingBreakdown($distanceKm, $weightKg, $whoPays);

                            $parcel->base_delivery_charge = $breakdown['base'];
                            $parcel->receiver_markup = $breakdown['markup'];
                            $parcel->final_paid_amount = $breakdown['final'];
                            $parcel->commission_percent = (float) (settings()->marketplace_commission_percent ?? 0);
                        }
                    }
                    $parcel->save();
                }
            }
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
    //end admin panel merchant invoice


    //both panel invoice print
    public function InvoicePdf($merchant_id, $invoice_id)
    {
        try {
            $invoice  = Invoice::where(['merchant_id' => $merchant_id, 'invoice_id' => $invoice_id])->first();
            return $invoice;
        } catch (\Throwable $th) {
            return false;
        }
    }

    //get invoice
    public function invoiceGet($merchant_id, $invoice_id)
    {
        try {
            $invoice  = Invoice::where(['merchant_id' => $merchant_id, 'invoice_id' => $invoice_id])->first();
            return $invoice;
        } catch (\Throwable $th) {
            return false;
        }
    }

    //get invoice
    public function getFind($id)
    {
        try {
            $invoice  = Invoice::find($id);
            return $invoice;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function invoiceLists()
    {
        $invoices = Invoice::where('merchant_id', Auth::user()->merchant->id)->where('status', InvoiceStatus::PAID, InvoiceStatus::PROCESSING)->paginate(10);
        return $invoices;
    }
}
