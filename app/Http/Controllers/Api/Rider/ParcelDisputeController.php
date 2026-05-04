<?php

namespace App\Http\Controllers\Api\Rider;

use App\Enums\ParcelStatus;
use App\Http\Controllers\Controller;
use App\Models\Backend\Parcel;
use App\Models\Backend\ParcelDispute;
use App\Models\Backend\Upload;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ParcelDisputeController extends Controller
{
    public function reasons(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                ['key' => 'delivery_issue', 'label' => 'Delivery issue'],
                ['key' => 'damaged_parcel', 'label' => 'Damaged parcel'],
                ['key' => 'lost_parcel', 'label' => 'Lost parcel'],
                ['key' => 'wrong_item', 'label' => 'Wrong item'],
                ['key' => 'not_delivered', 'label' => 'Not delivered'],
                ['key' => 'payment_issue', 'label' => 'Payment issue'],
                ['key' => 'rider_behavior', 'label' => 'Rider behavior'],
                ['key' => 'other', 'label' => 'Other'],
            ],
        ]);
    }

    public function list(Request $request): JsonResponse
    {
        $request->validate([
            'status' => 'nullable|string|in:open,resolved,rejected',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $riderId = (int) $request->user()->id;
        $perPage = (int) $request->query('per_page', 15);
        $status = (string) $request->query('status', '');

        $query = ParcelDispute::query()
            ->with(['parcel:id,delivery_man_id,tracking_id,status,pickup_address,customer_address'])
            ->where('raised_by', 'rider')
            ->whereHas('parcel', function ($q) use ($riderId) {
                $q->where('delivery_man_id', $riderId);
            })
            ->orderByDesc('id');

        if ($status !== '') {
            $query->where('status', $status);
        }

        $disputes = $query->paginate($perPage);

        $allEvidenceIds = collect($disputes->items())
            ->flatMap(function ($dispute) {
                return is_array($dispute->evidence_files) ? $dispute->evidence_files : [];
            })
            ->filter()
            ->unique()
            ->values()
            ->all();

        $uploadMap = Upload::query()
            ->whereIn('id', $allEvidenceIds)
            ->get(['id', 'original'])
            ->keyBy('id');

        $items = collect($disputes->items())->map(function ($dispute) use ($uploadMap) {
            $evidence = collect(is_array($dispute->evidence_files) ? $dispute->evidence_files : [])
                ->map(function ($id) use ($uploadMap) {
                    $upload = $uploadMap->get((int) $id);
                    if (!$upload) {
                        return null;
                    }

                    $path = is_array($upload->original)
                        ? (string) data_get($upload->original, 'original')
                        : (string) $upload->original;

                    return $path !== '' ? static_asset($path) : null;
                })
                ->filter()
                ->values()
                ->all();

            return [
                'id' => $dispute->id,
                'parcel_id' => $dispute->parcel_id,
                'tracking_id' => optional($dispute->parcel)->tracking_id,
                'parcel_status' => (int) (optional($dispute->parcel)->status ?? 0),
                'reason_type' => $dispute->reason_type,
                'description' => $dispute->description,
                'status' => $dispute->status,
                'admin_decision' => $dispute->admin_decision,
                'liability' => $dispute->liability,
                'refund_amount' => $dispute->refund_amount,
                'rider_liability_amount' => $dispute->rider_liability_amount,
                'resolved_at' => optional($dispute->resolved_at)->toDateTimeString(),
                'created_at' => optional($dispute->created_at)->toDateTimeString(),
                'evidence_files' => $evidence,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => $items,
            'meta' => [
                'current_page' => $disputes->currentPage(),
                'per_page' => $disputes->perPage(),
                'total' => $disputes->total(),
                'last_page' => $disputes->lastPage(),
            ],
        ]);
    }

    public function raise(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reason_type' => 'required|string',
            'description' => 'nullable|string',
            'evidence_files.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $riderId = (int) $request->user()->id;
        $parcel = Parcel::where('id', $id)
            ->where('delivery_man_id', $riderId)
            ->first();

        if (!$parcel) {
            return response()->json([
                'success' => false,
                'message' => 'Parcel not found.',
            ], 404);
        }

        $allowedStatuses = [
            ParcelStatus::MARKETPLACE_DELIVERED,
            ParcelStatus::MARKETPLACE_PICKED_UP,
            ParcelStatus::MARKETPLACE_ACCEPTED,
        ];

        if (!in_array((int) $parcel->status, $allowedStatuses, true)) {
            return response()->json([
                'success' => false,
                'message' => 'Dispute cannot be raised for this parcel status.',
            ], 422);
        }

        $deliveredAt = $parcel->updated_at ? Carbon::parse($parcel->updated_at) : null;
        if ($deliveredAt && $deliveredAt->lt(now()->subHours(24))) {
            return response()->json([
                'success' => false,
                'message' => 'Dispute window has expired.',
            ], 422);
        }

        $hasAnyDispute = ParcelDispute::where('parcel_id', $parcel->id)->exists();
        if ($hasAnyDispute) {
            return response()->json([
                'success' => false,
                'message' => 'A dispute already exists for this parcel.',
            ], 422);
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
            return response()->json([
                'success' => false,
                'message' => 'Invalid reason type.',
            ], 422);
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
        $dispute = ParcelDispute::create([
            'parcel_id' => $parcel->id,
            'raised_by' => 'rider',
            'reason_type' => $request->reason_type,
            'description' => $request->description,
            'evidence_files' => $evidenceIds,
            'status' => 'open',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Dispute raised successfully.',
            'data' => [
                'id' => $dispute->id,
                'status' => $dispute->status,
            ],
        ]);
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
