<?php

namespace App\Http\Controllers\Backend\MerchantPanel;

use App\Enums\ParcelStatus;
use App\Http\Controllers\Controller;
use App\Models\Backend\Parcel;
use App\Models\Backend\ParcelDispute;
use App\Models\Backend\Upload;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ParcelDisputeController extends Controller
{
    public function create($id)
    {
        $merchant = Auth::user()?->merchant;
        if (!$merchant) {
            abort(403);
        }

        $parcel = Parcel::where('id', $id)
            ->where('merchant_id', $merchant->id)
            ->firstOrFail();

        return view('backend.merchant_panel.parcel.dispute_create', compact('parcel'));
    }

    public function store(Request $request, $id)
    {
        $request->validate([
            'reason_type' => 'required|string',
            'description' => 'nullable|string',
            'evidence_files.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $merchant = Auth::user()?->merchant;
        if (!$merchant) {
            abort(403);
        }

        $parcel = Parcel::where('id', $id)
            ->where('merchant_id', $merchant->id)
            ->firstOrFail();

        $allowedStatuses = [
            ParcelStatus::MARKETPLACE_ACCEPTED,
            ParcelStatus::MARKETPLACE_PICKED_UP,
            ParcelStatus::MARKETPLACE_DELIVERED,
        ];

        if (!in_array((int) $parcel->status, $allowedStatuses, true)) {
            Toastr::error('Dispute cannot be raised for this parcel status.', __('message.error'));
            return redirect()->back()->withInput();
        }

        $deliveredAt = $parcel->updated_at ? Carbon::parse($parcel->updated_at) : null;
        if ($deliveredAt && $deliveredAt->lt(now()->subHours(48))) {
            Toastr::error('Dispute window has expired.', __('message.error'));
            return redirect()->back()->withInput();
        }

        $hasAnyDispute = ParcelDispute::where('parcel_id', $parcel->id)->exists();
        if ($hasAnyDispute) {
            Toastr::error('A dispute is already open for this parcel.', __('message.error'));
            return redirect()->back();
        }

        $allowedReasons = [
            'delivery_issue',
            'damaged_parcel',
            'lost_parcel',
            'wrong_item',
            'not_delivered',
            'payment_issue',
            'rider_behavior',
            'other',
        ];

        if (!in_array($request->reason_type, $allowedReasons, true)) {
            Toastr::error('Invalid reason type.', __('message.error'));
            return redirect()->back()->withInput();
        }

        $evidenceIds = [];
        if ($request->hasFile('evidence_files')) {
            foreach ($request->file('evidence_files') as $file) {
                $uploadId = $this->storeUpload($file, 'disputes');
                if ($uploadId) {
                    $evidenceIds[] = $uploadId;
                }
            }
        }

        ParcelDispute::create([
            'parcel_id' => $parcel->id,
            'raised_by' => 'sender',
            'reason_type' => $request->reason_type,
            'description' => $request->description,
            'evidence_files' => $evidenceIds,
            'status' => 'open',
        ]);

        Toastr::success('Dispute raised successfully.', __('message.success'));
        return redirect()->route('merchant-panel.parcel.details', $parcel->id);
    }

    private function storeUpload($file, string $folder): ?int
    {
        if (!$file) {
            return null;
        }

        $destinationPath = public_path('uploads/' . $folder);
        if (!is_dir($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $fileName = Str::random(10) . date('YmdHis') . '.' . $file->getClientOriginalExtension();
        $file->move($destinationPath, $fileName);
        $relativePath = 'uploads/' . $folder . '/' . $fileName;

        $upload = new Upload();
        $upload->original = $relativePath;
        $upload->save();

        return $upload->id;
    }
}
