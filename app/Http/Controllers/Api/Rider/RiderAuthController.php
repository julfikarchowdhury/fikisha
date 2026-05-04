<?php

namespace App\Http\Controllers\Api\Rider;

use App\Enums\DriverType;
use App\Enums\RiderStatus;
use App\Enums\Status;
use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Services\SmsService;
use App\Models\Backend\City;
use App\Models\Backend\DeliveryMan;
use App\Models\Backend\Province;
use App\Models\Backend\RiderBankAccount;
use App\Models\Backend\Upload;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RiderAuthController extends Controller
{
    private const PASSWORD_RESET_OTP_EXPIRES_MINUTES = 10;
    private const SIGNUP_OTP_EXPIRES_MINUTES = 10;
    private const SIGNUP_TOKEN_EXPIRES_MINUTES = 20;

    public function states(): JsonResponse
    {
        $states = Province::query()
            ->where('status', Status::ACTIVE)
            ->orderBy('position')
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json([
            'success' => true,
            'states' => $states,
        ]);
    }

    public function config(): JsonResponse
    {
        $settings = settings();

        return response()->json([
            'success' => true,
            'config' => [
                'base_url' => rtrim((string) config('app.url', url('/')), '/'),
                'app_name' => (string) ($settings->name ?? config('app.name')),
                'currency' => (string) ($settings->currency ?? ''),
                'google_maps_api_key' => (string) env('GOOGLE_MAPS_API_KEY', ''),
                'mobile_app_logo_url' => (string) ($settings->mobile_app_logo_image ?? ''),
                'support_phone' => (string) ($settings->phone ?? ''),
                'location_system' => (string) ($settings->location_system ?? ''),
                'max_active_parcels_per_rider' => (int) ($settings->max_active_parcels_per_rider ?? 1),
                'rider_min_withdrawal_amount' => (float) ($settings->rider_min_withdrawal_amount ?? 0),
            ],
        ]);
    }

    public function citiesByState(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'state_id' => ['required', 'integer', 'exists:provinces,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $cities = City::query()
            ->where('province_id', (int) $request->input('state_id'))
            ->orderBy('name')
            ->get(['id', 'province_id', 'name']);

        return response()->json([
            'success' => true,
            'cities' => $cities,
        ]);
    }

    public function signupRequestOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'identifier' => ['required', 'string', 'max:191'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $identifier = trim((string) $request->input('identifier'));
        $channel = $this->detectIdentifierChannel($identifier);
        if (!$channel) {
            return response()->json([
                'success' => false,
                'message' => 'Identifier must be a valid email or phone number.',
            ], 422);
        }

        if ($channel === 'email') {
            $exists = User::query()->where('email', $identifier)->exists();
        } else {
            $normalizedPhone = ltrim($identifier, '+');
            $exists = User::query()
                ->where(function ($q) use ($identifier, $normalizedPhone) {
                    $q->where('mobile', $identifier)
                        ->orWhere('mobile', $normalizedPhone);
                })
                ->exists();
        }

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Identifier already exists.',
            ], 422);
        }

        $otp = (string) random_int(100000, 999999);
        $normalizedIdentifier = $this->normalizeSignupIdentifier($identifier, $channel);

        Cache::put(
            $this->signupOtpCacheKey($normalizedIdentifier),
            [
                'identifier' => $normalizedIdentifier,
                'channel' => $channel,
                'otp' => $otp,
            ],
            now()->addMinutes(self::SIGNUP_OTP_EXPIRES_MINUTES)
        );

        if ($channel === 'email') {
            try {
                Mail::raw(
                    $otp . ' is your ' . settings()->name . ' rider signup OTP.',
                    function ($message) use ($normalizedIdentifier) {
                        $message->to($normalizedIdentifier)->subject('Rider Signup OTP');
                    }
                );
            } catch (\Throwable $e) {
                \Log::error('Rider signup OTP email failed.', [
                    'identifier' => $normalizedIdentifier,
                    'error' => $e->getMessage(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'OTP failed to send via email.',
                ], 422);
            }
        } else {
            $smsResult = app(SmsService::class)->sendOtp($normalizedIdentifier, $otp);
            if (empty($smsResult['success'])) {
                return response()->json([
                    'success' => false,
                    'message' => $smsResult['message'] ?? 'OTP failed to send.',
                    'gateway' => $smsResult['gateway'] ?? null,
                ], 422);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully.',
            'sent_via' => $channel,
            'expires_in_seconds' => self::SIGNUP_OTP_EXPIRES_MINUTES * 60,
        ]);
    }

    public function signupVerifyOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'identifier' => ['required', 'string', 'max:191'],
            'otp' => ['required', 'digits:6'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $identifier = trim((string) $request->input('identifier'));
        $channel = $this->detectIdentifierChannel($identifier);
        if (!$channel) {
            return response()->json([
                'success' => false,
                'message' => 'Identifier must be a valid email or phone number.',
            ], 422);
        }

        $normalizedIdentifier = $this->normalizeSignupIdentifier($identifier, $channel);
        $otpData = Cache::get($this->signupOtpCacheKey($normalizedIdentifier));

        if (!$otpData || trim((string) data_get($otpData, 'otp')) !== trim((string) $request->input('otp'))) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP.',
            ], 422);
        }

        $signupToken = Str::random(64);
        Cache::put(
            $this->signupTokenCacheKey($signupToken),
            [
                'identifier' => $normalizedIdentifier,
                'channel' => $channel,
            ],
            now()->addMinutes(self::SIGNUP_TOKEN_EXPIRES_MINUTES)
        );

        Cache::forget($this->signupOtpCacheKey($normalizedIdentifier));

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully.',
            'signup_token' => $signupToken,
            'expires_in_seconds' => self::SIGNUP_TOKEN_EXPIRES_MINUTES * 60,
        ]);
    }

    public function signupComplete(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'signup_token' => ['required', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:191'],
            'last_name' => ['required', 'string', 'max:191'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'state_id' => ['required', 'integer', 'exists:provinces,id'],
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'vehicle_type' => ['required', 'string', 'max:50'],
            'referral_code' => ['nullable', 'string', 'max:100'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $signupData = Cache::get($this->signupTokenCacheKey((string) $request->input('signup_token')));
        if (!$signupData) {
            return response()->json([
                'success' => false,
                'message' => 'Signup session expired. Please verify OTP again.',
            ], 422);
        }

        $stateId = (int) $request->input('state_id');
        $cityId = (int) $request->input('city_id');
        $cityMatched = City::query()->where('id', $cityId)->where('province_id', $stateId)->exists();
        if (!$cityMatched) {
            return response()->json([
                'success' => false,
                'message' => 'Selected city does not belong to selected state.',
            ], 422);
        }

        $identifier = (string) data_get($signupData, 'identifier');
        $channel = (string) data_get($signupData, 'channel');
        if (!in_array($channel, ['email', 'phone'], true) || empty($identifier)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid signup session data.',
            ], 422);
        }

        if ($channel === 'email') {
            $exists = User::query()->where('email', $identifier)->exists();
        } else {
            $exists = User::query()
                ->where(function ($q) use ($identifier) {
                    $q->where('mobile', $identifier)
                        ->orWhere('mobile', ltrim($identifier, '+'));
                })
                ->exists();
        }

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Identifier already exists.',
            ], 422);
        }

        $user = DB::transaction(function () use ($request, $identifier, $channel, $stateId, $cityId) {
            $firstName = trim((string) $request->input('first_name'));
            $lastName = trim((string) $request->input('last_name'));

            $user = new User();
            $user->first_name = $firstName;
            $user->last_name = $lastName;
            $user->name = trim($firstName . ' ' . $lastName);
            $user->province_id = $stateId;
            $user->city_id = $cityId;
            $user->mobile = $channel === 'phone' ? ltrim($identifier, '+') : null;
            $user->email = $channel === 'email' ? $identifier : null;
            $user->password = Hash::make((string) $request->input('password'));
            $user->user_type = UserType::DELIVERYMAN;
            $user->status = Status::INACTIVE;
            $user->verification_status = Status::INACTIVE;
            $user->document_status = Status::INACTIVE;
            $user->submit_status = Status::INACTIVE;
            $user->permissions = [];
            if ($channel === 'email') {
                $user->email_verified_at = now();
            } else {
                $user->mobile_verified_at = now();
            }
            $user->save();

            $deliveryMan = new DeliveryMan();
            $deliveryMan->user_id = $user->id;
            $deliveryMan->vehicle_type = $request->input('vehicle_type');
            $deliveryMan->status = Status::ACTIVE;
            $deliveryMan->driver_type = DriverType::FREELANCER;
            $deliveryMan->city_id = $cityId;
            $deliveryMan->rider_status = RiderStatus::PENDING_KYC;
            $deliveryMan->front_side_scan = null;
            $deliveryMan->back_side_scan = null;
            $deliveryMan->driving_license_image_id = null;
            $deliveryMan->regis_front_scan = null;
            $deliveryMan->regis_back_scan = null;
            $deliveryMan->kyc_submitted_at = null;
            $deliveryMan->save();

            RiderBankAccount::firstOrCreate(['rider_id' => $user->id]);

            return $user;
        });

        Cache::forget($this->signupTokenCacheKey((string) $request->input('signup_token')));

        $token = $user->createToken($user->mobile ?: $user->email ?: Str::random(10))->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Rider registration completed successfully.',
            'token' => $token,
            'rider' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->mobile,
                'state_id' => $user->province_id,
                'city_id' => $user->city_id,
                'rider_status' => RiderStatus::PENDING_KYC,
            ],
        ]);
    }

    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'full_name' => ['required', 'string', 'max:191'],
            'phone' => ['required', 'string', 'max:50', 'unique:users,mobile'],
            'email' => ['nullable', 'email', 'max:191', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'state_id' => ['required', 'integer', 'exists:provinces,id'],
            'city_id' => ['required', 'integer'],
            'vehicle_type' => ['required', 'string', 'max:50'],
            'referral_code' => ['nullable', 'string', 'max:100'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $nameParts = preg_split('/\s+/', trim((string) $request->input('full_name')));
        $firstName = $nameParts[0] ?? '';
        $lastName = '';
        if (count($nameParts) > 1) {
            $lastName = implode(' ', array_slice($nameParts, 1));
        }
        $otp = random_int(100000, 999999);

        $user = DB::transaction(function () use ($request, $firstName, $lastName, $otp) {
            $user = new User();
            $user->first_name = $firstName;
            $user->last_name = $lastName;
            $user->name = trim($firstName . ' ' . $lastName);
            $user->province_id = (int) $request->input('state_id');
            $user->mobile = $request->input('phone');
            $user->email = $request->input('email');
            $user->password = Hash::make((string) $request->input('password'));
            $user->user_type = UserType::DELIVERYMAN;
            $user->status = Status::INACTIVE;
            $user->verification_status = Status::INACTIVE;
            $user->document_status = Status::INACTIVE;
            $user->submit_status = Status::INACTIVE;
            $user->otp = $otp;
            $user->city_id = (int) $request->input('city_id');
            $user->permissions = [];
            $user->save();

            $deliveryMan = new DeliveryMan();
            $deliveryMan->user_id = $user->id;
            $deliveryMan->vehicle_type = $request->input('vehicle_type');
            $deliveryMan->status = Status::ACTIVE;
            $deliveryMan->driver_type = DriverType::FREELANCER;
            $deliveryMan->city_id = (int) $request->input('city_id');
            $deliveryMan->rider_status = RiderStatus::PENDING_PHONE;
            $deliveryMan->front_side_scan = null;
            $deliveryMan->back_side_scan = null;
            $deliveryMan->driving_license_image_id = null;
            $deliveryMan->regis_front_scan = null;
            $deliveryMan->regis_back_scan = null;
            $deliveryMan->kyc_submitted_at = null;
            $deliveryMan->save();

            RiderBankAccount::firstOrCreate(['rider_id' => $user->id]);

            return $user;
        });

        $smsResult = app(SmsService::class)->sendOtp($user->mobile, $otp);
        if (empty($smsResult['success'])) {
            return response()->json([
                'success' => false,
                'message' => $smsResult['message'] ?? 'OTP failed to send.',
                'gateway' => $smsResult['gateway'] ?? null,
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'OTP sent to rider phone.',
        ]);
    }

    public function verifyPhone(Request $request): JsonResponse
    {
        $request->merge([
            'identifier' => $request->input('phone'),
        ]);

        return $this->verifyIdentifier($request);
    }

    public function verifyIdentifier(Request $request): JsonResponse
    {
        $request->validate([
            'identifier' => ['required', 'string'],
            'otp' => ['required', 'numeric'],
        ]);

        $identifier = trim((string) $request->input('identifier'));
        $otp = (string) $request->input('otp');

        $user = $this->findRiderByIdentifier($identifier);
        if ($user && trim((string) $user->otp) !== $otp) {
            $user = null;
        }

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP.',
            ], 422);
        }

        $deliveryMan = $user->deliveryman;
        if (!$deliveryMan) {
            return response()->json([
                'success' => false,
                'message' => 'Rider profile not found.',
            ], 404);
        }

        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            $user->email_verified_at = now();
        } else {
            $user->mobile_verified_at = now();
        }
        $user->otp = null;
        $user->save();

        $deliveryMan->rider_status = RiderStatus::PENDING_KYC;
        $deliveryMan->save();

        $token = $user->createToken($user->mobile ?: $user->email ?: Str::random(10))->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Identifier verified successfully.',
            'token' => $token,
        ]);
    }

    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $identifier = (string) $request->input('email');
        $userQuery = User::query()->where('user_type', UserType::DELIVERYMAN);
        if (is_numeric($identifier)) {
            $userQuery->where('mobile', $identifier);
        } else {
            $userQuery->where('email', $identifier);
        }

        $user = $userQuery->first();
        if (!$user || !Hash::check((string) $request->input('password'), (string) $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.',
            ], 401);
        }

        $deliveryMan = $user->deliveryman;
        if (!$deliveryMan) {
            return response()->json([
                'success' => false,
                'message' => 'Rider profile not found.',
            ], 404);
        }

        if (in_array((int) $deliveryMan->rider_status, [RiderStatus::SUSPENDED, RiderStatus::BLOCKED], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Rider account is not active.',
            ], 403);
        }

        $token = $user->createToken($user->mobile ?: Str::random(10))->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'token' => $token,
            'rider' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->mobile,
                'vehicle_type' => $deliveryMan->vehicle_type,
                'rider_status' => $deliveryMan->rider_status,
            ],
        ]);
    }

    public function requestForgotPasswordOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'identifier' => ['required', 'string', 'max:191'],
            'send_via' => ['nullable', 'in:phone,email'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $this->findRiderByIdentifier((string) $request->input('identifier'));
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Rider account not found.',
            ], 404);
        }

        $requestedChannel = $request->input('send_via');
        $identifier = (string) $request->input('identifier');
        $detectedChannel = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        $sendVia = $requestedChannel ?: $detectedChannel;

        if ($sendVia === 'email' && empty($user->email)) {
            return response()->json([
                'success' => false,
                'message' => 'Rider email is missing.',
            ], 422);
        }

        if ($sendVia === 'phone' && empty($user->mobile)) {
            return response()->json([
                'success' => false,
                'message' => 'Rider phone number is missing.',
            ], 422);
        }

        $otp = random_int(100000, 999999);
        $user->password_reset_otp = (string) $otp;
        $user->password_reset_otp_expires_at = now()->addMinutes(self::PASSWORD_RESET_OTP_EXPIRES_MINUTES);
        $user->password_reset_verified_at = null;
        $user->password_reset_token = null;
        $user->save();

        if ($sendVia === 'email') {
            try {
                Mail::raw(
                    $otp . ' is your ' . settings()->name . ' password reset OTP.',
                    function ($message) use ($user) {
                        $message->to($user->email)
                            ->subject('Password Reset OTP');
                    }
                );
            } catch (\Throwable $e) {
                \Log::error('Rider password reset OTP email failed.', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'OTP failed to send via email.',
                ], 422);
            }
        } else {
            $smsResult = app(SmsService::class)->sendOtp($user->mobile, $otp);
            if (empty($smsResult['success'])) {
                return response()->json([
                    'success' => false,
                    'message' => $smsResult['message'] ?? 'OTP failed to send.',
                    'gateway' => $smsResult['gateway'] ?? null,
                ], 422);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Password reset OTP sent successfully.',
            'sent_via' => $sendVia,
            'expires_in_seconds' => self::PASSWORD_RESET_OTP_EXPIRES_MINUTES * 60,
        ]);
    }

    public function verifyForgotPasswordOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'identifier' => ['required', 'string', 'max:191'],
            'otp' => ['required', 'digits:6'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $this->findRiderByIdentifier((string) $request->input('identifier'));
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Rider account not found.',
            ], 404);
        }

        if (empty($user->password_reset_otp) || (string) $user->password_reset_otp !== (string) $request->input('otp')) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP.',
            ], 422);
        }

        if (empty($user->password_reset_otp_expires_at) || now()->gt($user->password_reset_otp_expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'OTP has expired. Please request a new OTP.',
            ], 422);
        }

        $user->password_reset_verified_at = now();
        $user->password_reset_token = Str::random(64);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully.',
            'reset_token' => $user->password_reset_token,
        ]);
    }

    public function resetForgotPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'identifier' => ['required', 'string', 'max:191'],
            'reset_token' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $this->findRiderByIdentifier((string) $request->input('identifier'));
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Rider account not found.',
            ], 404);
        }

        if (empty($user->password_reset_verified_at) || empty($user->password_reset_token)) {
            return response()->json([
                'success' => false,
                'message' => 'OTP verification is required before password reset.',
            ], 422);
        }

        if (!hash_equals((string) $user->password_reset_token, (string) $request->input('reset_token'))) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid reset token.',
            ], 422);
        }

        if (empty($user->password_reset_otp_expires_at) || now()->gt($user->password_reset_otp_expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Reset session has expired. Please verify OTP again.',
            ], 422);
        }

        DB::transaction(function () use ($request, $user) {
            $user->password = Hash::make((string) $request->input('password'));
            $user->password_reset_otp = null;
            $user->password_reset_otp_expires_at = null;
            $user->password_reset_verified_at = null;
            $user->password_reset_token = null;
            $user->save();
            $user->tokens()->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Password reset successful.',
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.',
        ]);
    }

    public function status(Request $request): JsonResponse
    {
        $user = $request->user();
        $deliveryMan = $user ? $user->deliveryman : null;

        if (!$user || !$deliveryMan) {
            return response()->json([
                'success' => false,
                'message' => 'Rider profile not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'rider_status' => $deliveryMan->rider_status,
            'rider_status_label' => $deliveryMan->rider_status_label ?? null,
            'kyc_submitted_at' => $deliveryMan->kyc_submitted_at,
            'approved_at' => $deliveryMan->approved_at,
            'rejected_at' => $deliveryMan->rejected_at,
            'rejection_reason' => $deliveryMan->rejection_reason,
        ]);
    }

    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();
        $deliveryMan = $user ? $user->deliveryman : null;
        $bank = $user ? RiderBankAccount::where('rider_id', $user->id)->first() : null;

        if (!$user || !$deliveryMan) {
            return response()->json([
                'success' => false,
                'message' => 'Rider profile not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'profile' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->mobile,
                'address' => $user->address,
                'image' => $user->image,
                'vehicle_type' => $deliveryMan->vehicle_type,
                'rider_status' => $deliveryMan->rider_status,
                'kyc' => [
                    'submitted_at' => $deliveryMan->kyc_submitted_at,
                    'nid_front' => $this->uploadUrl($deliveryMan->front_side_scan),
                    'nid_back' => $this->uploadUrl($deliveryMan->back_side_scan),
                    'driving_license' => $this->uploadUrl($deliveryMan->driving_license_image_id),
                    'vehicle_reg_front' => $this->uploadUrl($deliveryMan->regis_front_scan),
                    'vehicle_reg_back' => $this->uploadUrl($deliveryMan->regis_back_scan),
                ],
                'bank' => $bank ? [
                    'bank_name' => $bank->bank_name,
                    'account_name' => $bank->account_name,
                    'account_number' => $bank->account_number,
                    'mobile_wallet_number' => $bank->mobile_wallet_number,
                    'routing_number' => $bank->routing_number,
                ] : null,
            ],
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['nullable', 'string', 'max:191'],
            'address' => ['nullable', 'string', 'max:255'],
            'vehicle_type' => ['nullable', 'string', 'max:50'],
            'image' => ['nullable', 'file', 'mimes:jpg,jpeg,png'],
            'bank_name' => ['nullable', 'string', 'max:191'],
            'account_name' => ['nullable', 'string', 'max:191'],
            'account_number' => ['nullable', 'string', 'max:191'],
            'mobile_wallet_number' => ['nullable', 'string', 'max:191'],
            'routing_number' => ['nullable', 'string', 'max:191'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();
        $deliveryMan = $user ? $user->deliveryman : null;
        if (!$user || !$deliveryMan) {
            return response()->json([
                'success' => false,
                'message' => 'Rider profile not found.',
            ], 404);
        }

        DB::transaction(function () use ($request, $user, $deliveryMan) {
            if ($request->filled('name')) {
                $user->name = $request->input('name');
            }
            if ($request->filled('address')) {
                $user->address = $request->input('address');
            }
            if ($request->hasFile('image')) {
                $user->image_id = $this->storeProfileImage($request->file('image'), $user->image_id);
            }
            $user->save();

            if ($request->filled('vehicle_type')) {
                $deliveryMan->vehicle_type = $request->input('vehicle_type');
            }
            $deliveryMan->save();

            if ($request->filled('bank_name') ||
                $request->filled('account_name') ||
                $request->filled('account_number') ||
                $request->filled('mobile_wallet_number') ||
                $request->filled('routing_number')) {
                RiderBankAccount::updateOrCreate(
                    ['rider_id' => $user->id],
                    [
                        'bank_name' => $request->input('bank_name'),
                        'account_name' => $request->input('account_name'),
                        'account_number' => $request->input('account_number'),
                        'mobile_wallet_number' => $request->input('mobile_wallet_number'),
                        'routing_number' => $request->input('routing_number'),
                    ]
                );
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
        ]);
    }

    public function toggleAvailability(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'is_available' => ['required', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();
        $deliveryMan = $user ? $user->deliveryman : null;
        if (!$user || !$deliveryMan) {
            return response()->json([
                'success' => false,
                'message' => 'Rider profile not found.',
            ], 404);
        }

        $deliveryMan->is_available = (int) $request->boolean('is_available');
        $deliveryMan->save();

        return response()->json([
            'success' => true,
            'message' => $deliveryMan->is_available ? 'Rider is online.' : 'Rider is offline.',
            'is_available' => $deliveryMan->is_available,
        ]);
    }

    public function deviceToken(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'device_token' => ['required', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Rider profile not found.',
            ], 404);
        }

        $user->device_token = $request->input('device_token');
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Device token updated.',
        ]);
    }

    public function kycUpload(Request $request): JsonResponse
    {
        $request->validate([
            'nid_front' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf'],
            'nid_back' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf'],
            'driving_license' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf'],
            'vehicle_reg_front' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf'],
            'vehicle_reg_back' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf'],
            'profile_photo' => ['required', 'file', 'mimes:jpg,jpeg,png'],
            'bank_name' => ['required', 'string', 'max:191'],
            'account_name' => ['required', 'string', 'max:191'],
            'account_number' => ['required', 'string', 'max:191'],
            'mobile_wallet_number' => ['nullable', 'string', 'max:191'],
            'routing_number' => ['nullable', 'string', 'max:191'],
        ]);

        $user = $request->user();
        $deliveryMan = $user ? $user->deliveryman : null;
        if (!$user || !$deliveryMan) {
            return response()->json([
                'success' => false,
                'message' => 'Rider profile not found.',
            ], 404);
        }

        if ((int) $deliveryMan->rider_status === RiderStatus::APPROVED) {
            return response()->json([
                'success' => false,
                'message' => 'Rider already approved.',
            ], 422);
        }

        if (in_array((int) $deliveryMan->rider_status, [RiderStatus::SUSPENDED, RiderStatus::BLOCKED], true)) {
            return response()->json([
                'success' => false,
                'message' => 'KYC upload is not allowed for suspended/blocked rider.',
            ], 422);
        }

        DB::transaction(function () use ($request, $user, $deliveryMan) {
            $deliveryMan->front_side_scan = $this->storeUpload($request->file('nid_front'), 'rider/kyc', $deliveryMan->front_side_scan);
            $deliveryMan->back_side_scan = $this->storeUpload($request->file('nid_back'), 'rider/kyc', $deliveryMan->back_side_scan);
            $deliveryMan->driving_license_image_id = $this->storeUpload($request->file('driving_license'), 'rider/kyc', $deliveryMan->driving_license_image_id);
            $deliveryMan->regis_front_scan = $this->storeUpload($request->file('vehicle_reg_front'), 'rider/kyc', $deliveryMan->regis_front_scan);
            $deliveryMan->regis_back_scan = $this->storeUpload($request->file('vehicle_reg_back'), 'rider/kyc', $deliveryMan->regis_back_scan);
            $deliveryMan->rider_status = RiderStatus::UNDER_REVIEW;
            $deliveryMan->kyc_submitted_at = now();
            $deliveryMan->save();

            $user->image_id = $this->storeProfileImage($request->file('profile_photo'), $user->image_id);
            $user->submit_status = Status::ACTIVE;
            $user->save();

            RiderBankAccount::updateOrCreate(
                ['rider_id' => $user->id],
                [
                    'bank_name' => $request->input('bank_name'),
                    'account_name' => $request->input('account_name'),
                    'account_number' => $request->input('account_number'),
                    'mobile_wallet_number' => $request->input('mobile_wallet_number'),
                    'routing_number' => $request->input('routing_number'),
                ]
            );
        });

        return response()->json([
            'success' => true,
            'message' => 'KYC submitted successfully.',
        ]);
    }

    private function storeUpload($file, string $folder, ?int $uploadId = null): ?int
    {
        if (!$file) {
            return $uploadId;
        }

        $destinationPath = public_path('uploads/' . $folder);
        if (!is_dir($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $fileName = Str::random(10) . date('YmdHis') . '.' . $file->getClientOriginalExtension();
        $file->move($destinationPath, $fileName);
        $relativePath = 'uploads/' . $folder . '/' . $fileName;

        if ($uploadId) {
            $upload = Upload::find($uploadId);
            if ($upload) {
                $oldPath = is_array($upload->original) ? (string) data_get($upload->original, 'original') : (string) $upload->original;
                if ($oldPath !== '' && File::exists(public_path($oldPath))) {
                    unlink(public_path($oldPath));
                }
            }
        }

        if (empty($upload)) {
            $upload = new Upload();
        }
        $upload->original = $relativePath;
        $upload->save();

        return $upload->id;
    }

    private function findRiderByIdentifier(string $identifier): ?User
    {
        $identifier = trim($identifier);
        if ($identifier === '') {
            return null;
        }

        $query = User::query()->where('user_type', UserType::DELIVERYMAN);
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            return $query->where('email', $identifier)->first();
        }

        $normalizedPhone = ltrim($identifier, '+');
        return $query
            ->where(function ($q) use ($identifier, $normalizedPhone) {
                $q->where('mobile', $identifier)
                    ->orWhere('mobile', $normalizedPhone);
            })
            ->first();
    }

    private function detectIdentifierChannel(string $identifier): ?string
    {
        $identifier = trim($identifier);
        if ($identifier === '') {
            return null;
        }

        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            return 'email';
        }

        if (preg_match('/^\+?[0-9]{6,20}$/', $identifier)) {
            return 'phone';
        }

        return null;
    }

    private function normalizeSignupIdentifier(string $identifier, string $channel): string
    {
        $identifier = trim($identifier);
        if ($channel === 'email') {
            return strtolower($identifier);
        }

        return preg_replace('/\s+/', '', $identifier);
    }

    private function signupOtpCacheKey(string $normalizedIdentifier): string
    {
        return 'rider_signup_otp_' . sha1($normalizedIdentifier);
    }

    private function signupTokenCacheKey(string $signupToken): string
    {
        return 'rider_signup_token_' . sha1($signupToken);
    }

    private function uploadUrl(?int $uploadId): ?string
    {
        if (empty($uploadId)) {
            return null;
        }

        $upload = Upload::find((int) $uploadId);
        if (!$upload) {
            return null;
        }

        $originalPath = is_array($upload->original)
            ? (string) data_get($upload->original, 'original')
            : (string) $upload->original;

        if ($originalPath === '') {
            return null;
        }

        return static_asset($originalPath);
    }

    private function storeProfileImage($file, ?int $imageId = null): ?int
    {
        if (!$file) {
            return $imageId;
        }

        $destinationPath = public_path('uploads/users');
        if (!is_dir($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $fileName = date('YmdHis') . '.' . $file->getClientOriginalExtension();
        $file->move($destinationPath, $fileName);
        $relativePath = 'uploads/users/' . $fileName;

        if ($imageId) {
            $upload = Upload::find($imageId);
            if ($upload && File::exists(public_path($upload->original))) {
                unlink(public_path($upload->original));
            }
        } else {
            $upload = new Upload();
        }

        if (!$upload) {
            return null;
        }

        $upload->original = $relativePath;
        $upload->save();

        return $upload->id;
    }
}
