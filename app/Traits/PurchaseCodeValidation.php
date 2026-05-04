<?php

namespace App\Traits;

trait PurchaseCodeValidation
{


    public function PurchaseVerification($code)
    {
        try {
            $personalToken = env('WEMAX_KEY');
            // Surrounding whitespace can cause a 404 error, so trim it first
            $code = trim($code);
            // Make sure the code looks valid before sending it to Envato
            // This step is important - requests with incorrect formats can be blocked!
            if (!preg_match("/^([a-f0-9]{8})-(([a-f0-9]{4})-){3}([a-f0-9]{12})$/i", $code)) {
                return "Invalid purchase code";
            }

            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => "https://api.envato.com/v3/market/author/sale?code={$code}",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 20,
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer {$personalToken}",
                    "User-Agent: Purchase code verification script"
                )
            ));
            $response     = @curl_exec($ch);
            $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (curl_errno($ch) > 0) {
                return  "Failed to connect: " . curl_error($ch);
            }
            switch ($responseCode) {
                case 404:
                    return "Invalid purchase code";
                case 403:
                    return "The wemaxdevs token is missing the required permission for this script. Please contact to wemaxdevs.";
                case 401:
                    return "The wemaxdevs token is missing the required permission for this script. Please contact to wemaxdevs.";
            }
            if ($responseCode !== 200) {
                return "Got status {$responseCode}, try again shortly";
            }
            $body = @json_decode($response);
            if ($body === false && json_last_error() !== JSON_ERROR_NONE) {
                return "Error parsing response, try again";
            }
            if (!empty($response)):
                $result = json_decode($response, true);

                if (isset($result['buyer']) && isset($result['item']['id'])):
                    return $responseCode;
                endif;
            endif;
            return false;
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }
}
