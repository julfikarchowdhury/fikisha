<?php

namespace App\Http\Controllers\Api\Merchant;

use App\Enums\Status;
use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Services\SmsService;
use App\Models\Backend\Merchant;
use App\Models\Backend\Upload;
use App\Models\MerchantShops;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    private const PASSWORD_RESET_OTP_EXPIRES_MINUTES = 10;
    private const SIGNUP_OTP_EXPIRES_MINUTES = 10;
    private const SIGNUP_TOKEN_EXPIRES_MINUTES = 30;

    public function config(): JsonResponse
    {
        $settings = settings();

        return response()->json([
            'success' => true,
            'config' => [
                'app_name' => (string) ($settings->name ?? config('app.name')),
                'currency' => (string) ($settings->currency ?? ''),
                'google_maps_api_key' => (string) env('GOOGLE_MAPS_API_KEY', ''),
                'mobile_app_logo_url' => (string) ($settings->mobile_app_logo_image ?? ''),
            ],
        ]);
    }

    public function requestOtp(Request $request): JsonResponse
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

        $normalizedIdentifier = $this->normalizeIdentifier($identifier, $channel);
        if ($this->identifierExists($normalizedIdentifier, $channel)) {
            return response()->json([
                'success' => false,
                'message' => 'Identifier already exists.',
            ], 422);
        }

        $otp = (string) random_int(100000, 999999);
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
                    $otp . ' is your ' . settings()->name . ' merchant signup OTP.',
                    function ($message) use ($normalizedIdentifier) {
                        $message->to($normalizedIdentifier)->subject('Merchant Signup OTP');
                    }
                );
            } catch (\Throwable $e) {
                \Log::error('Merchant signup OTP email failed.', [
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

    public function verifyOtp(Request $request): JsonResponse
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

        $normalizedIdentifier = $this->normalizeIdentifier($identifier, $channel);
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

    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'signup_token' => ['required', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:191'],
            'last_name' => ['required', 'string', 'max:191'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $signupData = Cache::get($this->signupTokenCacheKey((string) $validated['signup_token']));
        if (!$signupData) {
            return response()->json([
                'success' => false,
                'message' => 'Signup session expired. Please verify OTP again.',
            ], 422);
        }

        $identifier = (string) data_get($signupData, 'identifier');
        $channel = (string) data_get($signupData, 'channel');
        if (!in_array($channel, ['email', 'phone'], true) || $identifier === '') {
            return response()->json([
                'success' => false,
                'message' => 'Invalid signup session data.',
            ], 422);
        }

        if ($this->identifierExists($identifier, $channel)) {
            return response()->json([
                'success' => false,
                'message' => 'Identifier already exists.',
            ], 422);
        }

        $user = null;
        DB::transaction(function () use ($validated, $identifier, $channel, &$user): void {
            $fullName = trim(($validated['first_name'] ?? '') . ' ' . ($validated['last_name'] ?? ''));

            $user = new User();
            $user->first_name = $validated['first_name'];
            $user->last_name = $validated['last_name'];
            $user->name = $fullName;
            $user->email = $channel === 'email' ? $identifier : null;
            $user->mobile = $channel === 'phone' ? ltrim($identifier, '+') : null;
            $user->address = null;
            $user->password = Hash::make($validated['password']);
            $user->user_type = UserType::MERCHANT;
            $user->status = Status::ACTIVE;
            $user->verification_status = Status::ACTIVE;
            $user->submit_status = Status::ACTIVE;
            // Merchant app flow: OTP-verified users are treated as fully verified.
            $user->email_verified_at = now();
            $user->mobile_verified_at = now();
            $user->save();

            $merchant = new Merchant();
            $merchant->user_id = $user->id;
            $merchant->account_type = 1;
            $merchant->business_name = $fullName;
            $merchant->address = null;
            $merchant->merchant_unique_id = $this->generateUniqueMerchantId();
            $merchant->cod_charges = [
                'inside_city' => '0',
                'sub_city' => '0',
                'outside_city' => '0',
            ];
            $merchant->status = Status::ACTIVE;
            $merchant->save();

            $shop = new MerchantShops();
            $shop->merchant_id = $merchant->id;
            $shop->name = $merchant->business_name;
            $shop->contact_no = $user->mobile;
            $shop->address = $merchant->address;
            $shop->status = Status::ACTIVE;
            $shop->default_shop = Status::ACTIVE;
            $shop->save();
        });

        Cache::forget($this->signupTokenCacheKey((string) $validated['signup_token']));

        Auth::login($user);
        $tokenName = $user->email ?: ($user->mobile ?: 'merchant-app');

        return response()->json([
            'success' => true,
            'message' => 'Merchant registered successfully.',
            'data' => [
                'token' => $user->createToken($tokenName)->plainTextToken,
                'user' => [
                    'id' => (int) $user->id,
                    'first_name' => (string) $user->first_name,
                    'last_name' => (string) $user->last_name,
                    'email' => (string) ($user->email ?? ''),
                    'mobile' => (string) ($user->mobile ?? ''),
                    'user_type' => (int) $user->user_type,
                ],
            ],
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'identifier' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $identifier = trim((string) $validated['identifier']);
        $credentials = filter_var($identifier, FILTER_VALIDATE_EMAIL)
            ? ['email' => $identifier, 'password' => $validated['password']]
            : ['mobile' => $identifier, 'password' => $validated['password']];

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.',
            ], 401);
        }

        /** @var User $user */
        $user = Auth::user();
        if ((int) $user->user_type !== UserType::MERCHANT) {
            Auth::logout();
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized user type for merchant login.',
            ], 403);
        }

        $tokenName = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? $identifier : ($user->mobile ?: 'merchant-app');

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'data' => [
                'token' => $user->createToken($tokenName)->plainTextToken,
                'user' => [
                    'id' => (int) $user->id,
                    'first_name' => (string) $user->first_name,
                    'last_name' => (string) $user->last_name,
                    'email' => (string) ($user->email ?? ''),
                    'mobile' => (string) ($user->mobile ?? ''),
                    'user_type' => (int) $user->user_type,
                ],
            ],
        ]);
    }

    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user || (int) $user->user_type !== UserType::MERCHANT) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized user type for merchant profile API.',
            ], 403);
        }

        $merchant = Merchant::query()->where('user_id', (int) $user->id)->first();
        if (!$merchant) {
            return response()->json([
                'success' => false,
                'message' => 'Merchant profile not found.',
            ], 404);
        }

        $profileData = $this->formatMerchantProfileData($user, $merchant);

        return response()->json([
            'success' => true,
            'data' => $profileData,
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['nullable', 'string', 'max:191', 'required_without_all:first_name,last_name'],
            'first_name' => ['nullable', 'string', 'max:191'],
            'last_name' => ['nullable', 'string', 'max:191'],
            'email' => ['required', 'email', 'max:191'],
            'mobile' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:500'],
            'business_name' => ['required', 'string', 'max:191'],
            'image_id' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'nid' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
            'trade_license' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();
        if (!$user || (int) $user->user_type !== UserType::MERCHANT) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized user type for merchant profile API.',
            ], 403);
        }

        $merchant = Merchant::query()->where('user_id', (int) $user->id)->first();
        if (!$merchant) {
            return response()->json([
                'success' => false,
                'message' => 'Merchant profile not found.',
            ], 404);
        }

        $mobileInput = (string) $request->input('mobile');

        $providedName = trim((string) $request->input('name', ''));
        $providedFirstName = trim((string) $request->input('first_name', ''));
        $providedLastName = trim((string) $request->input('last_name', ''));

        if ($providedName === '') {
            $providedName = trim($providedFirstName . ' ' . $providedLastName);
        }

        $nameParts = preg_split('/\s+/', trim($providedName), 2) ?: [];
        $firstName = $providedFirstName !== '' ? $providedFirstName : (string) ($nameParts[0] ?? '');
        $lastName = $providedLastName !== '' ? $providedLastName : (string) ($nameParts[1] ?? '');
        $fullName = trim($firstName . ' ' . $lastName);
        if ($fullName === '') {
            $fullName = $providedName;
        }

        $user->first_name = $firstName !== '' ? $firstName : null;
        $user->last_name = $lastName !== '' ? $lastName : null;
        $user->name = $fullName;
        $user->email = (string) $request->input('email');
        $user->mobile = ltrim($mobileInput, '+');
        $user->address = (string) $request->input('address');

        if ($request->hasFile('image_id')) {
            $user->image_id = $this->storeMerchantFile(
                $request->file('image_id'),
                'uploads/merchant/image',
                $user->image_id
            );
        }
        $user->save();

        $merchant->business_name = (string) $request->input('business_name');
        $merchant->address = (string) $request->input('address');

        if ($request->hasFile('nid')) {
            $merchant->nid_id = $this->storeMerchantFile(
                $request->file('nid'),
                'uploads/merchant/nid',
                $merchant->nid_id
            );
        }

        if ($request->hasFile('trade_license')) {
            $merchant->trade_license = $this->storeMerchantFile(
                $request->file('trade_license'),
                'uploads/merchant/trade_license',
                $merchant->trade_license
            );
        }

        $merchant->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'data' => $this->formatMerchantProfileData($user, $merchant),
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

        $user = $this->findMerchantByIdentifier((string) $request->input('identifier'));
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Merchant account not found.',
            ], 404);
        }

        $requestedChannel = $request->input('send_via');
        $identifier = (string) $request->input('identifier');
        $detectedChannel = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        $sendVia = $requestedChannel ?: $detectedChannel;

        if ($sendVia === 'email' && empty($user->email)) {
            return response()->json([
                'success' => false,
                'message' => 'Merchant email is missing.',
            ], 422);
        }

        if ($sendVia === 'phone' && empty($user->mobile)) {
            return response()->json([
                'success' => false,
                'message' => 'Merchant phone number is missing.',
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
                        $message->to($user->email)->subject('Password Reset OTP');
                    }
                );
            } catch (\Throwable $e) {
                \Log::error('Merchant password reset OTP email failed.', [
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

        $user = $this->findMerchantByIdentifier((string) $request->input('identifier'));
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Merchant account not found.',
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

        $user = $this->findMerchantByIdentifier((string) $request->input('identifier'));
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Merchant account not found.',
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

        DB::transaction(function () use ($request, $user): void {
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

    private function generateUniqueMerchantId(): string
    {
        do {
            $merchantUniqueId = (string) random_int(100000, 999999);
        } while (Merchant::query()->where('merchant_unique_id', $merchantUniqueId)->exists());

        return $merchantUniqueId;
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

    private function normalizeIdentifier(string $identifier, string $channel): string
    {
        $identifier = trim($identifier);
        if ($channel === 'email') {
            return strtolower($identifier);
        }

        return preg_replace('/\s+/', '', $identifier);
    }

    private function identifierExists(string $identifier, string $channel): bool
    {
        if ($channel === 'email') {
            return User::query()->where('email', $identifier)->exists();
        }

        return User::query()
            ->where(function ($q) use ($identifier) {
                $q->where('mobile', $identifier)
                    ->orWhere('mobile', ltrim($identifier, '+'));
            })
            ->exists();
    }

    private function signupOtpCacheKey(string $normalizedIdentifier): string
    {
        return 'merchant_signup_otp_' . sha1($normalizedIdentifier);
    }

    private function signupTokenCacheKey(string $signupToken): string
    {
        return 'merchant_signup_token_' . sha1($signupToken);
    }

    private function findMerchantByIdentifier(string $identifier): ?User
    {
        $identifier = trim($identifier);
        if ($identifier === '') {
            return null;
        }

        $query = User::query()->where('user_type', UserType::MERCHANT);
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            return $query->where('email', $identifier)->first();
        }

        return $query
            ->where(function ($q) use ($identifier) {
                $q->where('mobile', $identifier)
                    ->orWhere('mobile', ltrim($identifier, '+'));
            })
            ->first();
    }

    private function formatMerchantProfileData(User $user, Merchant $merchant): array
    {
        return [
            // Flat fields for mobile app form binding (same fields as merchant web profile).
            'name' => (string) ($user->name ?? ''),
            'email' => (string) ($user->email ?? ''),
            'mobile' => (string) ($user->mobile ?? ''),
            'business_name' => (string) ($merchant->business_name ?? ''),
            'address' => (string) ($merchant->address ?? ''),
            'image_url' => (string) ($user->image ?? ''),
            'nid_url' => (string) ($merchant->nid ?? ''),
            'trade_license_url' => (string) ($merchant->trade ?? ''),

            // Keep nested blocks for backward compatibility with existing app builds.
            'user' => [
                'id' => (int) $user->id,
                'first_name' => (string) ($user->first_name ?? ''),
                'last_name' => (string) ($user->last_name ?? ''),
                'name' => (string) ($user->name ?? ''),
                'email' => (string) ($user->email ?? ''),
                'mobile' => (string) ($user->mobile ?? ''),
                'address' => (string) ($user->address ?? ''),
                'image_url' => (string) ($user->image ?? ''),
            ],
            'merchant' => [
                'id' => (int) $merchant->id,
                'business_name' => (string) ($merchant->business_name ?? ''),
                'business_address' => (string) ($merchant->address ?? ''),
                'merchant_unique_id' => (string) ($merchant->merchant_unique_id ?? ''),
                'nid_url' => (string) ($merchant->nid ?? ''),
                'trade_license_url' => (string) ($merchant->trade ?? ''),
            ],
        ];
    }

    private function storeMerchantFile($file, string $relativeDirectory, ?int $existingUploadId = null): ?int
    {
        if (!$file) {
            return $existingUploadId;
        }

        $destinationPath = public_path($relativeDirectory);
        if (!is_dir($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $extension = (string) $file->getClientOriginalExtension();
        $filename = date('YmdHis') . '_' . Str::random(8) . ($extension ? ('.' . $extension) : '');
        $file->move($destinationPath, $filename);
        $storedPath = trim($relativeDirectory . '/' . $filename, '/');

        $upload = null;
        if (!empty($existingUploadId)) {
            $upload = Upload::query()->find($existingUploadId);
        }
        if (!$upload) {
            $upload = new Upload();
        } else {
            $oldPath = (string) $upload->original;
            if ($oldPath !== '' && file_exists(public_path($oldPath))) {
                @unlink(public_path($oldPath));
            }
        }

        $upload->original = $storedPath;
        $upload->save();

        return (int) $upload->id;
    }
}
