<?php


namespace App\Http\Services;

use App\Enums\Status;
use Twilio\Rest\Client;
 
 class SmsService
{
    private function normalizePhone($phone): string
    {
        $phone = trim((string) $phone);
        if ($phone === '') {
            return $phone;
        }
        if (str_starts_with($phone, '+')) {
            return $phone;
        }
        return (string) settings()->country_code . $phone;
    }

    public function sendOtp($userPhone, $otpCode): array
    {
        $gateways = [
            'reve' => smsSettings('reve_status'),
            'twilio' => smsSettings('twilio_status'),
            'easysendsms' => smsSettings('easysendsms_status'),
            'bulk_gate' => smsSettings('bulk_gate_status'),
        ];

        $activeGateways = array_keys(array_filter($gateways, fn ($status) => $status == Status::ACTIVE));
        if (count($activeGateways) !== 1) {
            return [
                'success' => false,
                'message' => 'Exactly one SMS gateway must be enabled for OTP.',
                'gateway' => null,
            ];
        }

        $gateway = $activeGateways[0];
        try {
            if ($gateway === 'reve') {
                $result = $this->reveSms('otp', $userPhone, $otpCode);
            } elseif ($gateway === 'twilio') {
                $result = $this->twilioSms('otp', $userPhone, $otpCode);
            } elseif ($gateway === 'easysendsms') {
                $result = $this->easySendSms('otp', $userPhone, $otpCode);
            } else {
                $result = $this->bulkGateSms('otp', $userPhone, $otpCode);
            }
        } catch (\Throwable $e) {
            \Log::error('OTP SMS failed.', [
                'gateway' => $gateway,
                'error' => $e->getMessage(),
            ]);
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'gateway' => $gateway,
            ];
        }

        if ($result === true) {
            return [
                'success' => true,
                'message' => 'OTP sent.',
                'gateway' => $gateway,
            ];
        }

        return [
            'success' => false,
            'message' => 'OTP failed to send.',
            'gateway' => $gateway,
        ];
    }

    public function sendSms($userPhone, $msg)
    {
        $smsSetting             = smsSettings('reve_status');
        $smsTwilioSetting       = smsSettings('twilio_status');
        $smsNexmoSetting        = smsSettings('nexmo_status');
        $smsEasySendSmsSetting  = smsSettings('easysendsms_status');
        $bulkGateSendSmsSetting  = smsSettings('bulk_gate_status');

        if ($smsSetting == Status::ACTIVE) {
            $this->reveSms('sms', $userPhone, $msg);
        }

        if ($smsTwilioSetting == Status::ACTIVE) {
            $this->twilioSms('sms', $userPhone, $msg);
        }

        if ($smsNexmoSetting == Status::ACTIVE) {
            $this->nexmoSms('sms', $userPhone, $msg);
        }

        if ($smsEasySendSmsSetting == Status::ACTIVE) {
            $this->easySendSms('sms', $userPhone, $msg);
        }

        if ($bulkGateSendSmsSetting == Status::ACTIVE) {
            $this->bulkGateSms('sms', $userPhone, $msg);
        }
    }

    private function bulkGateSms($type, $userPhone, $userMsg)
    {
        try {
            $application_id = smsSettings('application_id');
            $application_token = smsSettings('application_token');

            if ($type == 'otp') {
                $message_text = $userMsg . ' is your ' . settings()->name . ' verification code.';
            } else {
                $message_text = $userMsg;
            }

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://portal.bulkgate.com/api/1.0/simple/transactional',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode([
                    'application_id' => $application_id,
                    'application_token' => $application_token,
                    'number' => $userPhone,
                    'text' => $message_text, 
                    'sender_id' => 'gMobile',
                    'sender_id_value' => '9855WHPTЕС',
                    "unicode" => true,
                ]),
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json'
                ],
            ]);

            $response = curl_exec($curl);
            if ($error = curl_error($curl)) {
                return false;
            } else {
                $response = json_decode($response);
                return $response; 
            }
            curl_close($curl);
 
        } catch (\Exception $exception) {
            return $exception;
        }
    }

    private function reveSms($type, $userPhone, $userMsg)
    {

        try {
            $api_key = smsSettings('reve_api_key');
            $api_secret = smsSettings('reve_secret_key');
            $api_url = smsSettings('reve_api_url');
            $callerID = settings()->name;

            if ($type == 'otp') {
                $message = $userMsg . ' is your ' . settings()->name . ' verification code.';
            } else {
                $message = $userMsg;
            }

            $params = [
                "apikey" => $api_key,
                "secretkey" => $api_secret,
                "callerID" => $callerID,
                "toUser" => $userPhone,
                "messageContent" => $message
            ];

            $url = $api_url . '?' . http_build_query($params);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_TIMEOUT, 80);
            $response = curl_exec($ch);
            curl_close($ch);
            return $response;
        } catch (\Exception $exception) {
            return $exception;
        }
    }

    private function twilioSms($type, $receiverNumber, $message)
    {

        try {
            $account_sid = smsSettings('twilio_sid');
            $auth_token = smsSettings('twilio_token');
            $twilio_number = smsSettings('twilio_from');

            $client = new Client($account_sid, $auth_token);
            $toNumber = $this->normalizePhone($receiverNumber);
            $messageResource = $client->messages->create($toNumber, [
                'from' => $twilio_number,
                'body' => $message
            ]);
            \Log::info('Twilio SMS sent.', [
                'sid' => $messageResource->sid ?? null,
                'status' => $messageResource->status ?? null,
                'to' => $toNumber,
                'from' => $twilio_number,
            ]);
            return true;
        } catch (\Exception $exception) {
            \Log::error('Twilio SMS failed.', [
                'error' => $exception->getMessage(),
                'to' => $this->normalizePhone($receiverNumber),
                'from' => $twilio_number,
            ]);
            return false;
        }
    }

    private function nexmoSms($type, $receiverNumber, $message)
    {

        try {
            $nexmoKey = smsSettings('nexmo_key');
            $nexmoSecretKey = smsSettings('nexmo_secret_key');
            $basic  = new \Vonage\Client\Credentials\Basic($nexmoKey, $nexmoSecretKey);
            $client = new \Vonage\Client($basic);
            $toNumber = $this->normalizePhone($receiverNumber);
            $response = $client->sms()->send(
                new \Vonage\SMS\Message\SMS($toNumber, settings()->name, $message)
            );
            $message = $response->current();

            if ($message->getStatus() == 0) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            \Log::error('Nexmo SMS failed.', [
                'error' => $e->getMessage(),
                'to' => $this->normalizePhone($receiverNumber),
            ]);
            return false;
        }
    }

    private function easySendSms($type, $receiverNumber, $message)
    {
        try {
            if ($type == 'otp') :
                $message = $message . ' is your ' . settings()->name . ' verification code.';
            else :
                $message = $message;
            endif;

            $username  = smsSettings('easysendsms_username');
            $password  = smsSettings('easysendsms_password');
            $from      = smsSettings('easysendsms_from');
            $to        = $receiverNumber;
            $text      = $message;

            $postFeild = 'username=' . $username . '&password=' . $password . '&to=' . $to . '&from=' . $from . '&text=' . $text . '&type=0';

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.easysendsms.app/bulksms',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $postFeild,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/x-www-form-urlencoded',
                    'Cookie: ASPSESSIONIDASCQBARR=NKOHDCHDOFEOOALJIGDGGPAM'
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            return $response;
        } catch (\Throwable $th) {
            return $th;
        }
    }
}
