<?php

use App\Enums\AccountHeads;
use App\Enums\AttendanceStatus;
use App\Enums\LeaveStatus;
use App\Enums\ParcelStatus;
use App\Enums\Status;
use App\Enums\TodoStatus;
use App\Enums\UserType;
use App\Http\Services\PushNotificationService;
use App\Models\Backend\DeliveryMan;
use App\Models\Backend\DeliverymanStatement;
use App\Models\Backend\Expense;
use App\Models\Backend\FrontWeb\Section;
use App\Models\Backend\HRM\Attendance;
use App\Models\Backend\HRM\LeaveAssign;
use App\Models\Backend\HRM\LeaveRequest;
use App\Models\Backend\Income;
use App\Models\Backend\Merchant;
use App\Models\Backend\MerchantSetting;
use App\Models\Backend\MerchantStatement;
use App\Models\Backend\NewsOffer;
use App\Models\Backend\Parcel;
use App\Models\Backend\Payment;
use App\Models\Backend\Setting;
use App\Models\Backend\SmsSendSetting;
use App\Models\Backend\SmsSetting;
use App\Models\Backend\Support;
use App\Models\Backend\SupportChat;
use App\Models\Config;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Backend\HRM\Holiday;
use App\Models\Backend\MerchantPaymentDate;
use App\Models\Backend\Vehicle;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

if (!function_exists('pluck')) {
    function pluck($array, $value, $key = null)
    {
        $returnArray = [];
        if (count($array)) {
            foreach ($array as $item) {
                if ($key != null) {
                    $returnArray[$item->$key] = strtolower($value) == 'obj' ? $item : $item->$value;
                } else {
                    if ($value == 'obj') {
                        $returnArray[] = $item;
                    } else {
                        $returnArray[] = $item->$value;
                    }
                }
            }
        }
        return $returnArray;
    }
}

if (!function_exists('settingHelper')) {
    function settingHelper($key)
    {
        $data = Config::where('key', $key)->first();
        if (!blank($data)) :
            return $data->value;
        else :
            return '';
        endif;
    }
}

if (!function_exists('SmsSendSettingHelper')) {
    function SmsSendSettingHelper($status)
    {
        $data = SmsSendSetting::where(['sms_send_status' => $status, 'status' => Status::ACTIVE])->first();
        if (!blank($data)) :
            return true;
        else :
            return false;
        endif;
    }
}

//permission
if (!function_exists('hasPermission')) {
    function hasPermission($permission = null)
    {

        if (in_array($permission, Auth::user()->permissions)) {
            return true;
        }
        return false;
    }
}

if (!function_exists('settings')) {
    function settings()
    {
        return  App\Models\Backend\GeneralSettings::with('rxlogo', 'rxfavicon')->find(1);
    }
}
if (!function_exists('notificationSettings')) {
    function notificationSettings()
    {
        return App\Models\Backend\NotificationSettings::find(1);
    }
}

if (!function_exists('setEnv')) {
    function setEnv($name, $value)
    {
        $path = base_path('.env');
        if (file_exists($path)) {
            file_put_contents($path, str_replace(
                $name . '=' . env($name),
                $name . '=' . $value,
                file_get_contents($path)
            ));
        }
        return true;
    }
}
//todo
if (!function_exists('user')) {
    function user()
    {
        $users = App\Models\User::all();
        return $users;
    }
}

if (!function_exists('dateFormat')) {
    function dateFormat($newDate = null)
    {
        $dt = Carbon::parse($newDate);
        return $dt->format('dS F Y');
    }
}

if (!function_exists('dateFormatDone')) {
    function dateFormatDone($newDate = null)
    {
        return  Carbon::parse($newDate)->format('d-m-Y');
    }
}


// date format2
if (!function_exists('dateFormat2')) {
    function dateFormat2($date)
    {
        return Carbon::parse($date)->format('d M Y');
    }
}
//end date format2

if (!function_exists('parcelStatus')) {
    function parcelStatus($parcel, $request = null)
    {
        $parcelStatus = '';
        $allowStatus = [];
        if ($parcel->status  == ParcelStatus::PENDING) {
            $allowStatus = [
                ParcelStatus::DELIVERY_MAN_ASSIGN,
            ];
        } elseif ($parcel->status == ParcelStatus::RECEIVED_BY_HUB) {
            $allowStatus = [
                ParcelStatus::DELIVERY_MAN_ASSIGN
            ];
        } elseif ($parcel->status == ParcelStatus::TRANSFER_TO_HUB) {
            $allowStatus = [
                ParcelStatus::RECEIVED_BY_HUB
            ];
        } elseif ($parcel->status == ParcelStatus::DELIVERY_MAN_ASSIGN_CANCEL) {
            $allowStatus = [
                ParcelStatus::DELIVERY_RE_SCHEDULE,
            ];
        } elseif ($parcel->status == ParcelStatus::DELIVERY_RE_SCHEDULE_CANCEL) {
            $allowStatus = [
                ParcelStatus::DELIVERY_RE_SCHEDULE,
            ];
        } elseif ($parcel->status == ParcelStatus::DELIVERY_RE_SCHEDULE) {
            if ((int)$parcel->scheduled_amount > 0) {
                $allowStatus = [
                    ParcelStatus::DELIVERY_RE_SCHEDULE_CANCEL,
                    ParcelStatus::CONFIRMED_BOOKING,
                ];
            } else {
                $allowStatus = [
                    ParcelStatus::DELIVERY_RE_SCHEDULE_CANCEL,
                    ParcelStatus::CONFIRMED,
                ];
            }
        } elseif ($parcel->status == ParcelStatus::DELIVERY_MAN_ASSIGN) {
            if ((int)$parcel->scheduled_amount > 0) {
                $allowStatus = [
                    ParcelStatus::DELIVERY_MAN_ASSIGN_CANCEL,
                    ParcelStatus::DELIVERY_RE_SCHEDULE,
                    ParcelStatus::CONFIRMED_BOOKING,
                ];
            } else {
                $allowStatus = [
                    ParcelStatus::DELIVERY_MAN_ASSIGN_CANCEL,
                    ParcelStatus::DELIVERY_RE_SCHEDULE,
                    ParcelStatus::CONFIRMED,
                ];
            }
        } elseif ($parcel->status == ParcelStatus::CONFIRMED || $parcel->status == ParcelStatus::CONFIRMED_BOOKING) {
            if ((int)$parcel->scheduled_amount > 0) {
                $allowStatus = [
                    ParcelStatus::UNCONFIRMED_BOOKING,
                    ParcelStatus::PROCESSING,
                ];
            } else {
                $allowStatus = [
                    ParcelStatus::UNCONFIRMED,
                    ParcelStatus::PROCESSING,
                ];
            }
        } elseif ($parcel->status == ParcelStatus::UNCONFIRMED || $parcel->status == ParcelStatus::UNCONFIRMED_BOOKING) {
            if ((int)$parcel->scheduled_amount > 0) {
                $allowStatus = [
                    ParcelStatus::CONFIRMED_BOOKING,
                ];
            } else {
                $allowStatus = [
                    ParcelStatus::CONFIRMED,
                ];
            }
        } elseif ($parcel->from_state_id == $parcel->to_state_id) {
            // Inside Door to Door
            if ($parcel->shipping_type == 1) {
                if ($parcel->status == ParcelStatus::PROCESSING) {
                    $allowStatus = [
                        ParcelStatus::HEADING_TO_PICKUP_POINT,
                    ];
                } elseif ($parcel->status == ParcelStatus::HEADING_TO_PICKUP_POINT) {
                    $allowStatus = [
                        ParcelStatus::PICKED_UP,
                    ];
                } elseif ($parcel->status == ParcelStatus::PICKED_UP) {
                    $allowStatus = [
                        ParcelStatus::HEADING_TO_DELIVERY_POINT,
                    ];
                } elseif ($parcel->status == ParcelStatus::HEADING_TO_DELIVERY_POINT) {
                    $allowStatus = [
                        ParcelStatus::DELIVERED,
                        ParcelStatus::DELIVERY_FAILURE,
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_FAILURE) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_FAILED,
                        ParcelStatus::PARCEL_CANCEL,
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_FAILED) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_ATTEMPT1,
                        ParcelStatus::RETURN_TO_COURIER,
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_ATTEMPT1) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_MAN_ASSIGN1,
                        ParcelStatus::DELIVERY_ATTEMPT2,
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_ATTEMPT2) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_MAN_ASSIGN1,
                        ParcelStatus::DELIVERY_ATTEMPT3,
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_ATTEMPT3) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_MAN_ASSIGN1,
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_MAN_ASSIGN1) {
                    $allowStatus = [
                        ParcelStatus::DELIVERED,
                        ParcelStatus::DELIVERY_FAILURE,
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_TO_COURIER) {
                    $allowStatus = [
                        ParcelStatus::RETURN_TO_COURIER_CANCEL,
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT,
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT) {
                    $allowStatus = [
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT_CANCEL,
                        ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE,
                        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1) {
                    $allowStatus = [
                        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
                    ];
                } elseif ($parcel->status == ParcelStatus::PARCEL_CANCEL) {
                    $allowStatus = [
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERED) {
                    // No Action
                }
                // Inside Door to hub
            } elseif ($parcel->shipping_type == 2) {
                if ($parcel->status == ParcelStatus::PROCESSING) {
                    $allowStatus = [
                        ParcelStatus::HEADING_TO_PICKUP_POINT,
                    ];
                } elseif ($parcel->status == ParcelStatus::HEADING_TO_PICKUP_POINT) {
                    $allowStatus = [
                        ParcelStatus::PICKED_UP,
                    ];
                } elseif ($parcel->status == ParcelStatus::PICKED_UP) {
                    $allowStatus = [
                        ParcelStatus::DROP_OFF_CITY,
                    ];
                } elseif ($parcel->status == ParcelStatus::DROP_OFF_CITY) {
                    $allowStatus = [
                        ParcelStatus::DELIVERED,
                        ParcelStatus::DELIVERY_FAILURE,
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_FAILURE) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_FAILED,
                        ParcelStatus::PARCEL_CANCEL,
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_FAILED) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_ATTEMPT1,
                        ParcelStatus::RETURN_TO_COURIER,
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_ATTEMPT1) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_MAN_ASSIGN1,
                        ParcelStatus::DELIVERY_ATTEMPT2,
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_ATTEMPT2) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_MAN_ASSIGN1,
                        ParcelStatus::DELIVERY_ATTEMPT3,
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_ATTEMPT3) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_MAN_ASSIGN1,
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_MAN_ASSIGN1) {
                    $allowStatus = [
                        ParcelStatus::DELIVERED,
                        ParcelStatus::DELIVERY_FAILURE,
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_TO_COURIER) {
                    $allowStatus = [
                        ParcelStatus::RETURN_TO_COURIER_CANCEL,
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT,
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT) {
                    $allowStatus = [
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT_CANCEL,
                        ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE,
                        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1) {
                    $allowStatus = [
                        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
                    ];
                } elseif ($parcel->status == ParcelStatus::PARCEL_CANCEL) {
                    $allowStatus = [
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERED) {
                    // No Action
                }
                // Inside Hub to Door
            } elseif ($parcel->shipping_type == 3) {
                if ($parcel->status == ParcelStatus::PROCESSING) {
                    $allowStatus = [
                        ParcelStatus::DROP_OFF_CITY,
                    ];
                } elseif ($parcel->status == ParcelStatus::DROP_OFF_CITY) {
                    $allowStatus = [
                        ParcelStatus::HEADING_TO_DELIVERY_POINT,
                    ];
                } elseif ($parcel->status == ParcelStatus::HEADING_TO_DELIVERY_POINT) {
                    $allowStatus = [
                        ParcelStatus::DELIVERED,
                        ParcelStatus::DELIVERY_FAILURE,
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_FAILURE) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_FAILED,
                        ParcelStatus::PARCEL_CANCEL,
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_FAILED) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_ATTEMPT1,
                        ParcelStatus::RETURN_TO_COURIER,
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_ATTEMPT1) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_MAN_ASSIGN1,
                        ParcelStatus::DELIVERY_ATTEMPT2,
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_ATTEMPT2) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_MAN_ASSIGN1,
                        ParcelStatus::DELIVERY_ATTEMPT3,
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_ATTEMPT3) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_MAN_ASSIGN1,
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_MAN_ASSIGN1) {
                    $allowStatus = [
                        ParcelStatus::DELIVERED,
                        ParcelStatus::DELIVERY_FAILURE,
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_TO_COURIER) {
                    $allowStatus = [
                        ParcelStatus::RETURN_TO_COURIER_CANCEL,
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT,
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT) {
                    $allowStatus = [
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT_CANCEL,
                        ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE,
                        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1) {
                    $allowStatus = [
                        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
                    ];
                } elseif ($parcel->status == ParcelStatus::PARCEL_CANCEL) {
                    $allowStatus = [
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERED) {
                    // No Action
                }
                // Inside Hub to Hub
            } elseif ($parcel->shipping_type == 4) {
                if ($parcel->status == ParcelStatus::PROCESSING) {
                    $allowStatus = [
                        ParcelStatus::DROP_OFf_HUB1,
                    ];
                } elseif ($parcel->status == ParcelStatus::DROP_OFf_HUB1) {
                    $allowStatus = [
                        ParcelStatus::HEADING_TO_DROP_OFF,
                    ];
                } elseif ($parcel->status == ParcelStatus::HEADING_TO_DROP_OFF) {
                    $allowStatus = [
                        ParcelStatus::DROPPED_OFF_HUB2,
                    ];
                } elseif ($parcel->status == ParcelStatus::DROPPED_OFF_HUB2) {
                    $allowStatus = [
                        ParcelStatus::DELIVERED,
                        ParcelStatus::DELIVERY_FAILURE,
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_FAILURE) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_FAILED,
                        ParcelStatus::PARCEL_CANCEL,
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_FAILED) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_ATTEMPT1,
                        ParcelStatus::RETURN_TO_COURIER,
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_ATTEMPT1) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_ATTEMPT2,
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_ATTEMPT2) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_ATTEMPT3,
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_ATTEMPT3) {
                    $allowStatus = [
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_TO_COURIER) {
                    $allowStatus = [
                        ParcelStatus::RETURN_TO_COURIER_CANCEL,
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT,
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT) {
                    $allowStatus = [
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT_CANCEL,
                        ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE,
                        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1) {
                    $allowStatus = [
                        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
                    ];
                } elseif ($parcel->status == ParcelStatus::PARCEL_CANCEL) {
                    $allowStatus = [
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERED) {
                    // No Action
                }
            }
        } elseif ($parcel->from_state_id != $parcel->to_state_id) {
            // Outside Door to Door
            if ($parcel->shipping_type == 5) {
                if ($parcel->status == ParcelStatus::PROCESSING) {
                    $allowStatus = [
                        ParcelStatus::HEADING_TO_PICKUP_POINT,
                    ];
                } elseif ($parcel->status == ParcelStatus::HEADING_TO_PICKUP_POINT) {
                    $allowStatus = [
                        ParcelStatus::PICKED_UP,
                    ];
                } elseif ($parcel->status == ParcelStatus::PICKED_UP) {
                    $allowStatus = [
                        ParcelStatus::DROP_OFf_HUB1,
                    ];
                } elseif ($parcel->status == ParcelStatus::DROP_OFf_HUB1) {
                    $allowStatus = [
                        ParcelStatus::TRANSIT_OUT_CITY,
                    ];
                } elseif ($parcel->status == ParcelStatus::TRANSIT_OUT_CITY) {
                    $allowStatus = [
                        ParcelStatus::ON_THE_WAY_TO_CITY,
                    ];
                } elseif ($parcel->status == ParcelStatus::ON_THE_WAY_TO_CITY) {
                    $allowStatus = [
                        ParcelStatus::ARRIVED_AT_CITY,
                    ];
                } elseif ($parcel->status == ParcelStatus::ARRIVED_AT_CITY) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_MAN_ASSIGN_SECOND_PROVINCE,
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_MAN_ASSIGN_SECOND_PROVINCE) {
                    $allowStatus = [
                        ParcelStatus::HEADING_TO_DELIVERY_POINT,
                    ];
                } elseif ($parcel->status == ParcelStatus::HEADING_TO_DELIVERY_POINT) {
                    $allowStatus = [
                        ParcelStatus::DELIVERED,
                        ParcelStatus::DELIVERY_FAILURE,
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_FAILURE) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_FAILED,
                        ParcelStatus::PARCEL_CANCEL,
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_FAILED) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_ATTEMPT1,
                        ParcelStatus::RETURN_TO_COURIER,
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_ATTEMPT1) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_MAN_ASSIGN1,
                        ParcelStatus::DELIVERY_ATTEMPT2,
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_ATTEMPT2) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_MAN_ASSIGN1,
                        ParcelStatus::DELIVERY_ATTEMPT3,
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_ATTEMPT3) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_MAN_ASSIGN1,
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_MAN_ASSIGN1) {
                    $allowStatus = [
                        ParcelStatus::DELIVERED,
                        ParcelStatus::DELIVERY_FAILURE,
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_TO_COURIER) {
                    $allowStatus = [
                        ParcelStatus::RETURN_TO_COURIER_CANCEL,
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT,
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT) {
                    $allowStatus = [
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT_CANCEL,
                        ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE,
                        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1) {
                    $allowStatus = [
                        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
                    ];
                } elseif ($parcel->status == ParcelStatus::PARCEL_CANCEL) {
                    $allowStatus = [
                        ParcelStatus::RETURNING
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURNING) {
                    $allowStatus = [
                        ParcelStatus::TRANSIT_SENDING_PROVINCE
                    ];
                } elseif ($parcel->status == ParcelStatus::TRANSIT_SENDING_PROVINCE) {
                    $allowStatus = [
                        ParcelStatus::ON_THE_WAY_SENDING_PROVINCE
                    ];
                } elseif ($parcel->status == ParcelStatus::ON_THE_WAY_SENDING_PROVINCE) {
                    $allowStatus = [
                        ParcelStatus::ARRIVED_TO_SENDING_HUB
                    ];
                } elseif ($parcel->status == ParcelStatus::ARRIVED_TO_SENDING_HUB) {
                    $allowStatus = [
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT,
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT) {
                    $allowStatus = [
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT_CANCEL,
                        ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE,
                        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1) {
                    $allowStatus = [
                        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERED) {
                    // No Action
                }
                // Outside Door to Hub
            } elseif ($parcel->shipping_type == 6) {
                if ($parcel->status == ParcelStatus::PROCESSING) {
                    $allowStatus = [
                        ParcelStatus::HEADING_TO_PICKUP_POINT,
                    ];
                } elseif ($parcel->status == ParcelStatus::HEADING_TO_PICKUP_POINT) {
                    $allowStatus = [
                        ParcelStatus::PICKED_UP,
                    ];
                } elseif ($parcel->status == ParcelStatus::PICKED_UP) {
                    $allowStatus = [
                        ParcelStatus::DROP_OFf_HUB1,
                    ];
                } elseif ($parcel->status == ParcelStatus::DROP_OFf_HUB1) {
                    $allowStatus = [
                        ParcelStatus::TRANSIT_OUT_CITY,
                    ];
                } elseif ($parcel->status == ParcelStatus::TRANSIT_OUT_CITY) {
                    $allowStatus = [
                        ParcelStatus::ON_THE_WAY_TO_CITY,
                    ];
                } elseif ($parcel->status == ParcelStatus::ON_THE_WAY_TO_CITY) {
                    $allowStatus = [
                        ParcelStatus::ARRIVED_AT_CITY,
                    ];
                } elseif ($parcel->status == ParcelStatus::ARRIVED_AT_CITY) {
                    $allowStatus = [
                        ParcelStatus::DELIVERED,
                        ParcelStatus::DELIVERY_FAILURE,
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_FAILURE) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_FAILED,
                        ParcelStatus::PARCEL_CANCEL,
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_FAILED) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_ATTEMPT1,
                        ParcelStatus::RETURN_TO_COURIER,
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_ATTEMPT1) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_ATTEMPT2,
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_ATTEMPT2) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_ATTEMPT3,
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_ATTEMPT3) {
                    $allowStatus = [
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_TO_COURIER) {
                    $allowStatus = [
                        ParcelStatus::RETURN_TO_COURIER_CANCEL,
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT,
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT) {
                    $allowStatus = [
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT_CANCEL,
                        ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE,
                        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1) {
                    $allowStatus = [
                        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
                    ];
                } elseif ($parcel->status == ParcelStatus::PARCEL_CANCEL) {
                    $allowStatus = [
                        ParcelStatus::RETURNING
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURNING) {
                    $allowStatus = [
                        ParcelStatus::TRANSIT_SENDING_PROVINCE
                    ];
                } elseif ($parcel->status == ParcelStatus::TRANSIT_SENDING_PROVINCE) {
                    $allowStatus = [
                        ParcelStatus::ON_THE_WAY_SENDING_PROVINCE
                    ];
                } elseif ($parcel->status == ParcelStatus::ON_THE_WAY_SENDING_PROVINCE) {
                    $allowStatus = [
                        ParcelStatus::ARRIVED_TO_SENDING_HUB
                    ];
                } elseif ($parcel->status == ParcelStatus::ARRIVED_TO_SENDING_HUB) {
                    $allowStatus = [
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT,
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT) {
                    $allowStatus = [
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT_CANCEL,
                        ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE,
                        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1) {
                    $allowStatus = [
                        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERED) {
                    // No Action
                }
                // Outside Hub to Door
            } elseif ($parcel->shipping_type == 7) {
                if ($parcel->status == ParcelStatus::PROCESSING) {
                    $allowStatus = [
                        ParcelStatus::DROP_OFf_HUB1,
                    ];
                } elseif ($parcel->status == ParcelStatus::DROP_OFf_HUB1) {
                    $allowStatus = [
                        ParcelStatus::TRANSIT_OUT_CITY,
                    ];
                } elseif ($parcel->status == ParcelStatus::TRANSIT_OUT_CITY) {
                    $allowStatus = [
                        ParcelStatus::ON_THE_WAY_TO_CITY,
                    ];
                } elseif ($parcel->status == ParcelStatus::ON_THE_WAY_TO_CITY) {
                    $allowStatus = [
                        ParcelStatus::ARRIVED_AT_CITY,
                    ];
                } elseif ($parcel->status == ParcelStatus::ARRIVED_AT_CITY) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_MAN_ASSIGN_SECOND_PROVINCE,
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_MAN_ASSIGN_SECOND_PROVINCE) {
                    $allowStatus = [
                        ParcelStatus::HEADING_TO_DELIVERY_POINT,
                    ];
                } elseif ($parcel->status == ParcelStatus::HEADING_TO_DELIVERY_POINT) {
                    $allowStatus = [
                        ParcelStatus::DELIVERED,
                        ParcelStatus::DELIVERY_FAILURE,
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_FAILURE) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_FAILED,
                        ParcelStatus::PARCEL_CANCEL,
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_FAILED) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_ATTEMPT1,
                        ParcelStatus::RETURN_TO_COURIER,
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_ATTEMPT1) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_MAN_ASSIGN1,
                        ParcelStatus::DELIVERY_ATTEMPT2,
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_ATTEMPT2) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_MAN_ASSIGN1,
                        ParcelStatus::DELIVERY_ATTEMPT3,
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_ATTEMPT3) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_MAN_ASSIGN1,
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_MAN_ASSIGN1) {
                    $allowStatus = [
                        ParcelStatus::DELIVERED,
                        ParcelStatus::DELIVERY_FAILURE,
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_TO_COURIER) {
                    $allowStatus = [
                        ParcelStatus::RETURN_TO_COURIER_CANCEL,
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT,
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT) {
                    $allowStatus = [
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT_CANCEL,
                        ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE,
                        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1) {
                    $allowStatus = [
                        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
                    ];
                } elseif ($parcel->status == ParcelStatus::PARCEL_CANCEL) {
                    $allowStatus = [
                        ParcelStatus::RETURNING
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURNING) {
                    $allowStatus = [
                        ParcelStatus::TRANSIT_SENDING_PROVINCE
                    ];
                } elseif ($parcel->status == ParcelStatus::TRANSIT_SENDING_PROVINCE) {
                    $allowStatus = [
                        ParcelStatus::ON_THE_WAY_SENDING_PROVINCE
                    ];
                } elseif ($parcel->status == ParcelStatus::ON_THE_WAY_SENDING_PROVINCE) {
                    $allowStatus = [
                        ParcelStatus::ARRIVED_TO_SENDING_HUB
                    ];
                } elseif ($parcel->status == ParcelStatus::ARRIVED_TO_SENDING_HUB) {
                    $allowStatus = [
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT,
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT) {
                    $allowStatus = [
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT_CANCEL,
                        ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE,
                        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1) {
                    $allowStatus = [
                        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERED) {
                    // No Action
                }
                // Outside Hub to Hub
            } elseif ($parcel->shipping_type == 8) {
                if ($parcel->status == ParcelStatus::PROCESSING) {
                    $allowStatus = [
                        ParcelStatus::DROP_OFf_HUB1,
                    ];
                } elseif ($parcel->status == ParcelStatus::DROP_OFf_HUB1) {
                    $allowStatus = [
                        ParcelStatus::TRANSIT_OUT_CITY,
                    ];
                } elseif ($parcel->status == ParcelStatus::TRANSIT_OUT_CITY) {
                    $allowStatus = [
                        ParcelStatus::ON_THE_WAY_TO_CITY,
                    ];
                } elseif ($parcel->status == ParcelStatus::ON_THE_WAY_TO_CITY) {
                    $allowStatus = [
                        ParcelStatus::ARRIVED_AT_CITY,
                    ];
                } elseif ($parcel->status == ParcelStatus::ARRIVED_AT_CITY) {
                    $allowStatus = [
                        ParcelStatus::DELIVERED,
                        ParcelStatus::DELIVERY_FAILURE,
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_FAILURE) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_FAILED,
                        ParcelStatus::PARCEL_CANCEL,
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_FAILED) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_ATTEMPT1,
                        ParcelStatus::RETURN_TO_COURIER,
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_ATTEMPT1) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_ATTEMPT2,
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_ATTEMPT2) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_ATTEMPT3,
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_ATTEMPT3) {
                    $allowStatus = [
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_TO_COURIER) {
                    $allowStatus = [
                        ParcelStatus::RETURN_TO_COURIER_CANCEL,
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT,
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT) {
                    $allowStatus = [
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT_CANCEL,
                        ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE,
                        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1) {
                    $allowStatus = [
                        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
                    ];
                } elseif ($parcel->status == ParcelStatus::PARCEL_CANCEL) {
                    $allowStatus = [
                        ParcelStatus::RETURNING
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURNING) {
                    $allowStatus = [
                        ParcelStatus::TRANSIT_SENDING_PROVINCE
                    ];
                } elseif ($parcel->status == ParcelStatus::TRANSIT_SENDING_PROVINCE) {
                    $allowStatus = [
                        ParcelStatus::ON_THE_WAY_SENDING_PROVINCE
                    ];
                } elseif ($parcel->status == ParcelStatus::ON_THE_WAY_SENDING_PROVINCE) {
                    $allowStatus = [
                        ParcelStatus::ARRIVED_TO_SENDING_HUB
                    ];
                } elseif ($parcel->status == ParcelStatus::ARRIVED_TO_SENDING_HUB) {
                    $allowStatus = [
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT,
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT) {
                    $allowStatus = [
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT_CANCEL,
                        ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE,
                        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1) {
                    $allowStatus = [
                        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERED) {
                    // No Action
                }
            }
        } elseif ($parcel->status == ParcelStatus::DELIVERY_FAILURE) {
            $allowStatus = [
                ParcelStatus::PARCEL_CANCEL,
                ParcelStatus::DELIVERY_FAILED,
            ];
        } elseif ($parcel->status == ParcelStatus::PARCEL_CANCEL) {
            $allowStatus = [
                ParcelStatus::RETURN_TO_COURIER,
            ];
        } elseif ($parcel->status == ParcelStatus::PICKUP_RE_SCHEDULE) {
            $allowStatus = [
                ParcelStatus::PICKUP_RE_SCHEDULE_CANCEL,
                ParcelStatus::PICKUP_RE_SCHEDULE,
                ParcelStatus::HEADING_TO_PICKUP_POINT,
                ParcelStatus::UNCONFIRMED,
            ];
        } elseif ($parcel->status == ParcelStatus::DROP_OFF_CITY) {
            $allowStatus = [
                ParcelStatus::DELIVERED,
                ParcelStatus::DELIVERY_FAILURE,
            ];
        } elseif ($parcel->status == ParcelStatus::RECEIVED_BY_HUB) {
            $allowStatus = [
                ParcelStatus::DELIVERED,
                ParcelStatus::DELIVERY_FAILURE,
            ];
        } elseif ($parcel->status == ParcelStatus::DELIVERY_FAILED) {
            $allowStatus = [
                ParcelStatus::PARCEL_CANCEL,
                ParcelStatus::RETURN_TO_COURIER,
            ];
        } elseif ($parcel->status == parcelStatus::TRANSFER_TO_HUB) {
            $allowStatus = [
                ParcelStatus::TRANSFER_TO_HUB_CANCEL,
                ParcelStatus::DELIVERY_FAILURE,
            ];
        } elseif ($parcel->status == parcelStatus::RETURN_TO_COURIER) {
            $allowStatus = [
                ParcelStatus::RETURN_TO_COURIER_CANCEL,
                ParcelStatus::RETURN_ASSIGN_TO_MERCHANT
            ];
        } elseif ($parcel->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT) {
            $allowStatus = [
                ParcelStatus::RETURN_ASSIGN_TO_MERCHANT_CANCEL,
                ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE,
                ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
            ];
        } elseif ($parcel->status == ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE) {
            $allowStatus = [
                ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE_CANCEL,
                ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE,
                ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
            ];
        } elseif ($parcel->status == ParcelStatus::RETURN_RECEIVED_BY_MERCHANT) {
            // No Action
        } elseif ($parcel->status == ParcelStatus::DELIVERED) {
            // No Action
        } elseif ($parcel->status == ParcelStatus::PARTIAL_DELIVERED) {
            // No Action
        }

        if ($parcel->from_state_id == $parcel->to_state_id) {
            $side_check_state_id = $parcel->from_state_id;
        } else {
            $side_check_state_id = $parcel->to_state_id;
        }

        if ($parcel->status > parcelStatus::PROCESSING) {
            if ($parcel->from_state_id == $parcel->to_state_id) {
                $outside_check_state_id = $parcel->from_state_id;
            } else {
                $outside_check_state_id = $parcel->to_state_id;
            }
        } else {
            if ($parcel->from_state_id == $parcel->to_state_id) {
                $outside_check_state_id = $parcel->from_state_id;
            } else {
                $outside_check_state_id = $parcel->from_state_id;
            }
        }

        $parcelStatusArray = [];
        if (!blank($allowStatus)) {
            foreach (trans('parcelStatus') as $key => $status) {
                if (in_array($key, $allowStatus)) {
                    $parcelStatusArray[$key] = $status;
                }
            }
        }

        if (!blank($parcelStatusArray)) {
            foreach ($parcelStatusArray as $key => $status) {
                if ($key == ParcelStatus::PICKUP_ASSIGN_CANCEL) {
                    $parcelStatus .= '<a  class="dropdown-item pickup-man-assign-cancel" data-title="pickup assign" data-url="' . route("parcel.pickup.man-assigned-cancel") . '" data-parcel="' . $parcel->id . '"  href="#">' . $status . '</a>';
                } elseif ($key == ParcelStatus::PICKUP_RE_SCHEDULE_CANCEL) {
                    $parcelStatus .= '<a  class="dropdown-item pickup-reschedule-cancel" data-title="Pickup re-schedule" data-url="' . route("parcel.pickup.re-schedule-cancel") . '" data-parcel="' . $parcel->id . '"  href="#">' . $status . '</a>';
                } elseif ($key == ParcelStatus::RECEIVED_BY_PICKUP_MAN_CANCEL) {
                    $parcelStatus .= '<a  class="dropdown-item receved-by-pickupman-cancel" data-title="Received by pickup-man" data-url="' . route("parcel.pickup.man-received-cancel") . '" data-parcel="' . $parcel->id . '"  href="#">' . $status . '</a>';
                } elseif ($key == ParcelStatus::RECEIVED_WAREHOUSE_CANCEL) {
                    $parcelStatus .= '<a  class="dropdown-item receved-warehouse-cancel" data-title="Received warehouse" data-url="' . route("parcel.received-warehouse-cancel") . '" data-parcel="' . $parcel->id . '"   href="#">' . $status . '</a>';
                } elseif ($key == ParcelStatus::DELIVERY_MAN_ASSIGN_CANCEL) {
                    $parcelStatus .= '<a  class="dropdown-item delivery-man-assign-cancel" data-title="Assigned Cancel" data-url="' . route("parcel.delivery-man-assign-cancel") . '" data-parcel="' . $parcel->id . '"  href="#">' . $status . '</a>';
                } elseif ($key == ParcelStatus::DELIVERY_RE_SCHEDULE_CANCEL) {
                    $parcelStatus .= '<a  class="dropdown-item delivery-re-schedule-cancel" data-title="Reassign Cancel" data-url="' . route("parcel.delivery-re-schedule-cancel") . '" data-parcel="' . $parcel->id . '"  href="#">' . $status . '</a>';
                } elseif ($key == ParcelStatus::RETURN_TO_COURIER_CANCEL) {
                    $parcelStatus .= '<a  class="dropdown-item return-to-courier-cancel" data-title="Return to courier" data-url="' . route("parcel.return-to-courier-cancel") . '" data-parcel="' . $parcel->id . '"  href="#">' . $status . '</a>';
                } elseif ($key == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT_CANCEL) {
                    $parcelStatus .= '<a  class="dropdown-item return-assign-to-merchant-cancel" data-title="Return assign to merchant" data-url="' . route("parcel.return-assign-to-merchant-cancel") . '" data-parcel="' . $parcel->id . '"  href="#">' . $status . '</a>';
                } elseif ($key == ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE_CANCEL) {
                    $parcelStatus .= '<a  class="dropdown-item return-assign-re-schedule-merchant-cancel" data-title="Return merchant Re-Schedule Cancel" data-url="' . route("parcel.return-assign-re-schedule-to-merchant-cancel") . '" data-parcel="' . $parcel->id . '"  href="#">' . $status . '</a>';
                } elseif ($key == ParcelStatus::RETURN_RECEIVED_BY_MERCHANT_CANCEL) {
                    $parcelStatus .= '<a  class="dropdown-item return-received-by-merchant-cancel" data-title="Return received by merchant" data-url="' . route("parcel.return-received-by-merchant-cancel") . '" data-parcel="' . $parcel->id . '"  href="#">' . $status . '</a>';
                } elseif ($key == ParcelStatus::DELIVERED_CANCEL) {
                    $parcelStatus .= '<a  class="dropdown-item delivered-cancel" data-title="Delivered cancel" data-url="' . route("parcel.delivered-cancel") . '" data-parcel="' . $parcel->id . '"  href="#">' . $status . '</a>';
                } elseif ($key == ParcelStatus::PARTIAL_DELIVERED_CANCEL) {
                    $parcelStatus .= '<a  class="dropdown-item partial-delivered-cancel" data-title="Partial delivered cancel" data-url="' . route("parcel.partial-delivered-cancel") . '" data-parcel="' . $parcel->id . '"  href="#">' . $status . '</a>';
                } else {
                    if ($key == ParcelStatus::PICKUP_RE_SCHEDULE) {
                        $parcelStatus .= '<a  class="dropdown-item parcel-id-pickup-man" data-parcelstatus="' . ParcelStatus::PICKUP_ASSIGN . '" data-parcel="' . $parcel->id . '" data-toggle="modal" data-target="#parcelstatus' . $key . '" href="#">' . $status . '</a>';
                    } elseif ($key == ParcelStatus::DELIVERY_RE_SCHEDULE) {
                        $parcelStatus .= '<a  class="dropdown-item parcel-id-delivery-man" data-parcelstatus="' . ParcelStatus::DELIVERY_MAN_ASSIGN . '" data-parcel="' . $parcel->id . '"  data-province_id="' . $side_check_state_id . '" data-toggle="modal" data-target="#parcelstatus' . $key . '" href="#">' . $status . '</a>';
                    } elseif ($key == ParcelStatus::DELIVERY_MAN_ASSIGN) {
                        $parcelStatus .= '<a  class="dropdown-item parcel-delivery-man" data-parcelstatus="' . ParcelStatus::DELIVERY_MAN_ASSIGN . '" data-province_id="' . $outside_check_state_id . '" data-parcel="' . $parcel->id . '" data-toggle="modal" data-target="#parcelstatus' . $key . '" href="#">' . $status . '</a>';
                    } elseif ($key == ParcelStatus::DELIVERY_MAN_ASSIGN1) {
                        $parcelStatus .= '<a  class="dropdown-item parcel-delivery-man" data-parcelstatus="' . ParcelStatus::DELIVERY_MAN_ASSIGN1 . '" data-province_id="' . $side_check_state_id . '" data-parcel="' . $parcel->id . '" data-toggle="modal" data-target="#parcelstatus' . $key . '" href="#">' . $status . '</a>';
                    } elseif ($key == ParcelStatus::DELIVERY_MAN_ASSIGN_SECOND_PROVINCE) {
                        $parcelStatus .= '<a  class="dropdown-item parcel-delivery-second-province" data-parcelstatus="' . ParcelStatus::DELIVERY_MAN_ASSIGN_SECOND_PROVINCE . '" data-province_id="' . $side_check_state_id . '" data-parcel="' . $parcel->id . '" data-toggle="modal" data-target="#parcelstatus' . $key . '" href="#">' . $status . '</a>';
                    } elseif ($key == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT) {
                        $parcelStatus .= '<a  class="dropdown-item parcelReturnAssignToMerchant" data-parcelstatus="' . ParcelStatus::RETURN_ASSIGN_TO_MERCHANT . '" data-province_id="' . $side_check_state_id . '" data-parcel="' . $parcel->id . '" data-toggle="modal" data-target="#parcelstatus' . $key . '" href="#">' . $status . '</a>';
                    } elseif ($key == ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE) {
                        $parcelStatus .= '<a  class="dropdown-item parcelReturnAssignToMerchantReschedule" data-parcelstatus="' . ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE . '" data-province_id="' . $side_check_state_id . '" data-parcel="' . $parcel->id . '" data-toggle="modal" data-target="#parcelstatus' . $key . '" href="#">' . $status . '</a>';
                    } else {
                        $parcelStatus .= '<a  class="dropdown-item parcel-id" data-parcel="' . $parcel->id . '" data-toggle="modal" data-target="#parcelstatus' . $key . '" href="#">' . $status . '</a>';
                    }
                }
            }
        }

        return $parcelStatus;
    }
}

if (!function_exists('parcelStatusDriver')) {
    function parcelStatusDriver($parcel, $request = null)
    {
        $parcelStatus = '';
        $allowStatus = [];
        if ($parcel->status  == ParcelStatus::PENDING) {
            $allowStatus = [
                ParcelStatus::DELIVERY_MAN_ASSIGN,
            ];
        } elseif ($parcel->status == ParcelStatus::RECEIVED_BY_HUB) {
            $allowStatus = [
                ParcelStatus::DELIVERY_MAN_ASSIGN
            ];
        } elseif ($parcel->status == ParcelStatus::TRANSFER_TO_HUB) {
            $allowStatus = [
                ParcelStatus::RECEIVED_BY_HUB
            ];
        } elseif ($parcel->status == ParcelStatus::DELIVERY_MAN_ASSIGN_CANCEL) {
            $allowStatus = [
                ParcelStatus::DELIVERY_RE_SCHEDULE,
            ];
        } elseif ($parcel->status == ParcelStatus::DELIVERY_RE_SCHEDULE_CANCEL) {
            $allowStatus = [
                ParcelStatus::DELIVERY_RE_SCHEDULE,
            ];
        } elseif ($parcel->status == ParcelStatus::DELIVERY_RE_SCHEDULE) {
            if ((int)$parcel->scheduled_amount > 0) {
                $allowStatus = [
                    ParcelStatus::DELIVERY_RE_SCHEDULE_CANCEL,
                    ParcelStatus::CONFIRMED_BOOKING,
                ];
            } else {
                $allowStatus = [
                    ParcelStatus::DELIVERY_RE_SCHEDULE_CANCEL,
                    ParcelStatus::CONFIRMED,
                ];
            }
        } elseif ($parcel->status == ParcelStatus::DELIVERY_MAN_ASSIGN) {
            if ((int)$parcel->scheduled_amount > 0) {
                $allowStatus = [
                    ParcelStatus::DELIVERY_MAN_ASSIGN_CANCEL,
                    ParcelStatus::DELIVERY_RE_SCHEDULE,
                    ParcelStatus::CONFIRMED_BOOKING,
                ];
            } else {
                $allowStatus = [
                    ParcelStatus::DELIVERY_MAN_ASSIGN_CANCEL,
                    ParcelStatus::DELIVERY_RE_SCHEDULE,
                    ParcelStatus::CONFIRMED,
                ];
            }
        } elseif ($parcel->status == ParcelStatus::CONFIRMED || $parcel->status == ParcelStatus::CONFIRMED_BOOKING) {
            if ((int)$parcel->scheduled_amount > 0) {
                $allowStatus = [
                    ParcelStatus::UNCONFIRMED_BOOKING,
                    ParcelStatus::PROCESSING,
                ];
            } else {
                $allowStatus = [
                    ParcelStatus::UNCONFIRMED,
                    ParcelStatus::PROCESSING,
                ];
            }
        } elseif ($parcel->status == ParcelStatus::UNCONFIRMED || $parcel->status == ParcelStatus::UNCONFIRMED_BOOKING) {
            if ((int)$parcel->scheduled_amount > 0) {
                $allowStatus = [
                    ParcelStatus::CONFIRMED_BOOKING,
                ];
            } else {
                $allowStatus = [
                    ParcelStatus::CONFIRMED,
                ];
            }
        } elseif ($parcel->from_state_id == $parcel->to_state_id) {
            // Inside Door to Door
            if ($parcel->shipping_type == 1) {
                if ($parcel->status == ParcelStatus::PROCESSING) {
                    $allowStatus = [
                        ParcelStatus::HEADING_TO_PICKUP_POINT,
                    ];
                } elseif ($parcel->status == ParcelStatus::HEADING_TO_PICKUP_POINT) {
                    $allowStatus = [
                        ParcelStatus::PICKED_UP,
                    ];
                } elseif ($parcel->status == ParcelStatus::PICKED_UP) {
                    $allowStatus = [
                        ParcelStatus::HEADING_TO_DELIVERY_POINT,
                    ];
                } elseif ($parcel->status == ParcelStatus::HEADING_TO_DELIVERY_POINT) {
                    $allowStatus = [
                        ParcelStatus::DELIVERED,
                        ParcelStatus::DELIVERY_FAILURE,
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_FAILURE) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_FAILED,
                        ParcelStatus::PARCEL_CANCEL,
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_FAILED) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_ATTEMPT1,
                        ParcelStatus::RETURN_TO_COURIER,
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_ATTEMPT1) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_MAN_ASSIGN1,
                        ParcelStatus::DELIVERY_ATTEMPT2,
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_ATTEMPT2) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_MAN_ASSIGN1,
                        ParcelStatus::DELIVERY_ATTEMPT3,
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_ATTEMPT3) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_MAN_ASSIGN1,
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_MAN_ASSIGN1) {
                    $allowStatus = [
                        ParcelStatus::DELIVERED,
                        ParcelStatus::DELIVERY_FAILURE,
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_TO_COURIER) {
                    $allowStatus = [
                        ParcelStatus::RETURN_TO_COURIER_CANCEL,
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT,
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT) {
                    $allowStatus = [
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT_CANCEL,
                        ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE,
                        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1) {
                    $allowStatus = [
                        // ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1_CANCEL,
                        // ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE,
                        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
                    ];
                } elseif ($parcel->status == ParcelStatus::PARCEL_CANCEL) {
                    $allowStatus = [
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERED) {
                    // No Action
                }
                // Inside Door to hub
            } elseif ($parcel->shipping_type == 2) {
                if ($parcel->status == ParcelStatus::PROCESSING) {
                    $allowStatus = [
                        ParcelStatus::HEADING_TO_PICKUP_POINT,
                    ];
                } elseif ($parcel->status == ParcelStatus::HEADING_TO_PICKUP_POINT) {
                    $allowStatus = [
                        ParcelStatus::PICKED_UP,
                    ];
                } elseif ($parcel->status == ParcelStatus::PICKED_UP) {
                    $allowStatus = [
                        ParcelStatus::DROP_OFF_CITY,
                    ];
                } elseif ($parcel->status == ParcelStatus::DROP_OFF_CITY) {
                    $allowStatus = [
                        ParcelStatus::DELIVERED,
                        ParcelStatus::DELIVERY_FAILURE,
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_FAILURE) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_FAILED,
                        ParcelStatus::PARCEL_CANCEL,
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_FAILED) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_ATTEMPT1,
                        ParcelStatus::RETURN_TO_COURIER,
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_ATTEMPT1) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_MAN_ASSIGN1,
                        ParcelStatus::DELIVERY_ATTEMPT2,
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_ATTEMPT2) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_MAN_ASSIGN1,
                        ParcelStatus::DELIVERY_ATTEMPT3,
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_ATTEMPT3) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_MAN_ASSIGN1,
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_MAN_ASSIGN1) {
                    $allowStatus = [
                        ParcelStatus::DELIVERED,
                        ParcelStatus::DELIVERY_FAILURE,
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_TO_COURIER) {
                    $allowStatus = [
                        ParcelStatus::RETURN_TO_COURIER_CANCEL,
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT,
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT) {
                    $allowStatus = [
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT_CANCEL,
                        ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE,
                        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1) {
                    $allowStatus = [
                        // ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1_CANCEL,
                        // ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE,
                        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
                    ];
                } elseif ($parcel->status == ParcelStatus::PARCEL_CANCEL) {
                    $allowStatus = [
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERED) {
                    // No Action
                }
                // Inside Hub to Door
            } elseif ($parcel->shipping_type == 3) {
                if ($parcel->status == ParcelStatus::PROCESSING) {
                    $allowStatus = [
                        ParcelStatus::DROP_OFF_CITY,
                    ];
                } elseif ($parcel->status == ParcelStatus::DROP_OFF_CITY) {
                    $allowStatus = [
                        ParcelStatus::HEADING_TO_DELIVERY_POINT,
                    ];
                } elseif ($parcel->status == ParcelStatus::HEADING_TO_DELIVERY_POINT) {
                    $allowStatus = [
                        ParcelStatus::DELIVERED,
                        ParcelStatus::DELIVERY_FAILURE,
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_FAILURE) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_FAILED,
                        ParcelStatus::PARCEL_CANCEL,
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_FAILED) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_ATTEMPT1,
                        ParcelStatus::RETURN_TO_COURIER,
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_ATTEMPT1) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_MAN_ASSIGN1,
                        ParcelStatus::DELIVERY_ATTEMPT2,
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_ATTEMPT2) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_MAN_ASSIGN1,
                        ParcelStatus::DELIVERY_ATTEMPT3,
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_ATTEMPT3) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_MAN_ASSIGN1,
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_MAN_ASSIGN1) {
                    $allowStatus = [
                        ParcelStatus::DELIVERED,
                        ParcelStatus::DELIVERY_FAILURE,
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_TO_COURIER) {
                    $allowStatus = [
                        ParcelStatus::RETURN_TO_COURIER_CANCEL,
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT,
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT) {
                    $allowStatus = [
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT_CANCEL,
                        ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE,
                        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1) {
                    $allowStatus = [
                        // ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1_CANCEL,
                        // ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE,
                        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
                    ];
                } elseif ($parcel->status == ParcelStatus::PARCEL_CANCEL) {
                    $allowStatus = [
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERED) {
                    // No Action
                }
                // Inside Hub to Hub
            } elseif ($parcel->shipping_type == 4) {
                if ($parcel->status == ParcelStatus::PROCESSING) {
                    $allowStatus = [
                        ParcelStatus::DROP_OFf_HUB1,
                    ];
                } elseif ($parcel->status == ParcelStatus::DROP_OFf_HUB1) {
                    $allowStatus = [
                        ParcelStatus::HEADING_TO_DROP_OFF,
                    ];
                } elseif ($parcel->status == ParcelStatus::HEADING_TO_DROP_OFF) {
                    $allowStatus = [
                        ParcelStatus::DROPPED_OFF_HUB2,
                    ];
                } elseif ($parcel->status == ParcelStatus::DROPPED_OFF_HUB2) {
                    $allowStatus = [
                        ParcelStatus::DELIVERED,
                        ParcelStatus::DELIVERY_FAILURE,
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_FAILURE) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_FAILED,
                        ParcelStatus::PARCEL_CANCEL,
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_FAILED) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_ATTEMPT1,
                        ParcelStatus::RETURN_TO_COURIER,
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_ATTEMPT1) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_ATTEMPT2,
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_ATTEMPT2) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_ATTEMPT3,
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_ATTEMPT3) {
                    $allowStatus = [
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_TO_COURIER) {
                    $allowStatus = [
                        ParcelStatus::RETURN_TO_COURIER_CANCEL,
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT,
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT) {
                    $allowStatus = [
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT_CANCEL,
                        ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE,
                        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1) {
                    $allowStatus = [
                        // ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1_CANCEL,
                        // ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE,
                        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
                    ];
                } elseif ($parcel->status == ParcelStatus::PARCEL_CANCEL) {
                    $allowStatus = [
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERED) {
                    // No Action
                }
            }
        } elseif ($parcel->from_state_id != $parcel->to_state_id) {
            // Outside Door to Door
            if ($parcel->shipping_type == 5) {
                if ($parcel->status == ParcelStatus::PROCESSING) {
                    $allowStatus = [
                        ParcelStatus::HEADING_TO_PICKUP_POINT,
                    ];
                } elseif ($parcel->status == ParcelStatus::HEADING_TO_PICKUP_POINT) {
                    $allowStatus = [
                        ParcelStatus::PICKED_UP,
                    ];
                } elseif ($parcel->status == ParcelStatus::PICKED_UP) {
                    $allowStatus = [
                        ParcelStatus::DROP_OFf_HUB1,
                    ];
                } elseif ($parcel->status == ParcelStatus::DROP_OFf_HUB1) {
                    $allowStatus = [
                        ParcelStatus::TRANSIT_OUT_CITY,
                    ];
                } elseif ($parcel->status == ParcelStatus::TRANSIT_OUT_CITY) {
                    $allowStatus = [
                        ParcelStatus::ON_THE_WAY_TO_CITY,
                    ];
                } elseif ($parcel->status == ParcelStatus::ON_THE_WAY_TO_CITY) {
                    $allowStatus = [
                        ParcelStatus::ARRIVED_AT_CITY,
                    ];
                } elseif ($parcel->status == ParcelStatus::ARRIVED_AT_CITY) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_MAN_ASSIGN_SECOND_PROVINCE,
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_MAN_ASSIGN_SECOND_PROVINCE) {
                    $allowStatus = [
                        ParcelStatus::HEADING_TO_DELIVERY_POINT,
                    ];
                } elseif ($parcel->status == ParcelStatus::HEADING_TO_DELIVERY_POINT) {
                    $allowStatus = [
                        ParcelStatus::DELIVERED,
                        ParcelStatus::DELIVERY_FAILURE,
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_FAILURE) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_FAILED,
                        ParcelStatus::PARCEL_CANCEL,
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_FAILED) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_ATTEMPT1,
                        ParcelStatus::RETURN_TO_COURIER,
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_ATTEMPT1) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_MAN_ASSIGN1,
                        ParcelStatus::DELIVERY_ATTEMPT2,
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_ATTEMPT2) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_MAN_ASSIGN1,
                        ParcelStatus::DELIVERY_ATTEMPT3,
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_ATTEMPT3) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_MAN_ASSIGN1,
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_MAN_ASSIGN1) {
                    $allowStatus = [
                        ParcelStatus::DELIVERED,
                        ParcelStatus::DELIVERY_FAILURE,
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_TO_COURIER) {
                    $allowStatus = [
                        ParcelStatus::RETURN_TO_COURIER_CANCEL,
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT,
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT) {
                    $allowStatus = [
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT_CANCEL,
                        ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE,
                        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1) {
                    $allowStatus = [
                        // ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1_CANCEL,
                        // ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE,
                        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
                    ];
                } elseif ($parcel->status == ParcelStatus::PARCEL_CANCEL) {
                    $allowStatus = [
                        ParcelStatus::RETURNING
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURNING) {
                    $allowStatus = [
                        ParcelStatus::TRANSIT_SENDING_PROVINCE
                    ];
                } elseif ($parcel->status == ParcelStatus::TRANSIT_SENDING_PROVINCE) {
                    $allowStatus = [
                        ParcelStatus::ON_THE_WAY_SENDING_PROVINCE
                    ];
                } elseif ($parcel->status == ParcelStatus::ON_THE_WAY_SENDING_PROVINCE) {
                    $allowStatus = [
                        ParcelStatus::ARRIVED_TO_SENDING_HUB
                    ];
                } elseif ($parcel->status == ParcelStatus::ARRIVED_TO_SENDING_HUB) {
                    $allowStatus = [
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT,
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT) {
                    $allowStatus = [
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT_CANCEL,
                        ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE,
                        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1) {
                    $allowStatus = [
                        // ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1_CANCEL,
                        // ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE,
                        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERED) {
                    // No Action
                }
                // Outside Door to Hub
            } elseif ($parcel->shipping_type == 6) {
                if ($parcel->status == ParcelStatus::PROCESSING) {
                    $allowStatus = [
                        ParcelStatus::HEADING_TO_PICKUP_POINT,
                    ];
                } elseif ($parcel->status == ParcelStatus::HEADING_TO_PICKUP_POINT) {
                    $allowStatus = [
                        ParcelStatus::PICKED_UP,
                    ];
                } elseif ($parcel->status == ParcelStatus::PICKED_UP) {
                    $allowStatus = [
                        ParcelStatus::DROP_OFf_HUB1,
                    ];
                } elseif ($parcel->status == ParcelStatus::DROP_OFf_HUB1) {
                    $allowStatus = [
                        ParcelStatus::TRANSIT_OUT_CITY,
                    ];
                } elseif ($parcel->status == ParcelStatus::TRANSIT_OUT_CITY) {
                    $allowStatus = [
                        ParcelStatus::ON_THE_WAY_TO_CITY,
                    ];
                } elseif ($parcel->status == ParcelStatus::ON_THE_WAY_TO_CITY) {
                    $allowStatus = [
                        ParcelStatus::ARRIVED_AT_CITY,
                    ];
                } elseif ($parcel->status == ParcelStatus::ARRIVED_AT_CITY) {
                    $allowStatus = [
                        ParcelStatus::DELIVERED,
                        ParcelStatus::DELIVERY_FAILURE,
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_FAILURE) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_FAILED,
                        ParcelStatus::PARCEL_CANCEL,
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_FAILED) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_ATTEMPT1,
                        ParcelStatus::RETURN_TO_COURIER,
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_ATTEMPT1) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_ATTEMPT2,
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_ATTEMPT2) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_ATTEMPT3,
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_ATTEMPT3) {
                    $allowStatus = [
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_TO_COURIER) {
                    $allowStatus = [
                        ParcelStatus::RETURN_TO_COURIER_CANCEL,
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT,
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT) {
                    $allowStatus = [
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT_CANCEL,
                        ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE,
                        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1) {
                    $allowStatus = [
                        // ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1_CANCEL,
                        // ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE,
                        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
                    ];
                } elseif ($parcel->status == ParcelStatus::PARCEL_CANCEL) {
                    $allowStatus = [
                        ParcelStatus::RETURNING
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURNING) {
                    $allowStatus = [
                        ParcelStatus::TRANSIT_SENDING_PROVINCE
                    ];
                } elseif ($parcel->status == ParcelStatus::TRANSIT_SENDING_PROVINCE) {
                    $allowStatus = [
                        ParcelStatus::ON_THE_WAY_SENDING_PROVINCE
                    ];
                } elseif ($parcel->status == ParcelStatus::ON_THE_WAY_SENDING_PROVINCE) {
                    $allowStatus = [
                        ParcelStatus::ARRIVED_TO_SENDING_HUB
                    ];
                } elseif ($parcel->status == ParcelStatus::ARRIVED_TO_SENDING_HUB) {
                    $allowStatus = [
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT,
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT) {
                    $allowStatus = [
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT_CANCEL,
                        ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE,
                        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1) {
                    $allowStatus = [
                        // ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1_CANCEL,
                        // ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE,
                        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERED) {
                    // No Action
                }
                // Outside Hub to Door
            } elseif ($parcel->shipping_type == 7) {
                if ($parcel->status == ParcelStatus::PROCESSING) {
                    $allowStatus = [
                        ParcelStatus::DROP_OFf_HUB1,
                    ];
                } elseif ($parcel->status == ParcelStatus::DROP_OFf_HUB1) {
                    $allowStatus = [
                        ParcelStatus::TRANSIT_OUT_CITY,
                    ];
                } elseif ($parcel->status == ParcelStatus::TRANSIT_OUT_CITY) {
                    $allowStatus = [
                        ParcelStatus::ON_THE_WAY_TO_CITY,
                    ];
                } elseif ($parcel->status == ParcelStatus::ON_THE_WAY_TO_CITY) {
                    $allowStatus = [
                        ParcelStatus::ARRIVED_AT_CITY,
                    ];
                } elseif ($parcel->status == ParcelStatus::ARRIVED_AT_CITY) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_MAN_ASSIGN_SECOND_PROVINCE,
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_MAN_ASSIGN_SECOND_PROVINCE) {
                    $allowStatus = [
                        ParcelStatus::HEADING_TO_DELIVERY_POINT,
                    ];
                } elseif ($parcel->status == ParcelStatus::HEADING_TO_DELIVERY_POINT) {
                    $allowStatus = [
                        ParcelStatus::DELIVERED,
                        ParcelStatus::DELIVERY_FAILURE,
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_FAILURE) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_FAILED,
                        ParcelStatus::PARCEL_CANCEL,
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_FAILED) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_ATTEMPT1,
                        ParcelStatus::RETURN_TO_COURIER,
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_ATTEMPT1) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_MAN_ASSIGN1,
                        ParcelStatus::DELIVERY_ATTEMPT2,
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_ATTEMPT2) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_MAN_ASSIGN1,
                        ParcelStatus::DELIVERY_ATTEMPT3,
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_ATTEMPT3) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_MAN_ASSIGN1,
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_MAN_ASSIGN1) {
                    $allowStatus = [
                        ParcelStatus::DELIVERED,
                        ParcelStatus::DELIVERY_FAILURE,
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_TO_COURIER) {
                    $allowStatus = [
                        ParcelStatus::RETURN_TO_COURIER_CANCEL,
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT,
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT) {
                    $allowStatus = [
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT_CANCEL,
                        ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE,
                        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1) {
                    $allowStatus = [
                        // ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1_CANCEL,
                        // ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE,
                        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
                    ];
                } elseif ($parcel->status == ParcelStatus::PARCEL_CANCEL) {
                    $allowStatus = [
                        ParcelStatus::RETURNING
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURNING) {
                    $allowStatus = [
                        ParcelStatus::TRANSIT_SENDING_PROVINCE
                    ];
                } elseif ($parcel->status == ParcelStatus::TRANSIT_SENDING_PROVINCE) {
                    $allowStatus = [
                        ParcelStatus::ON_THE_WAY_SENDING_PROVINCE
                    ];
                } elseif ($parcel->status == ParcelStatus::ON_THE_WAY_SENDING_PROVINCE) {
                    $allowStatus = [
                        ParcelStatus::ARRIVED_TO_SENDING_HUB
                    ];
                } elseif ($parcel->status == ParcelStatus::ARRIVED_TO_SENDING_HUB) {
                    $allowStatus = [
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT,
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT) {
                    $allowStatus = [
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT_CANCEL,
                        ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE,
                        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1) {
                    $allowStatus = [
                        // ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1_CANCEL,
                        // ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE,
                        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERED) {
                    // No Action
                }
                // Outside Hub to Hub
            } elseif ($parcel->shipping_type == 8) {
                if ($parcel->status == ParcelStatus::PROCESSING) {
                    $allowStatus = [
                        ParcelStatus::DROP_OFf_HUB1,
                    ];
                } elseif ($parcel->status == ParcelStatus::DROP_OFf_HUB1) {
                    $allowStatus = [
                        ParcelStatus::TRANSIT_OUT_CITY,
                    ];
                } elseif ($parcel->status == ParcelStatus::TRANSIT_OUT_CITY) {
                    $allowStatus = [
                        ParcelStatus::ON_THE_WAY_TO_CITY,
                    ];
                } elseif ($parcel->status == ParcelStatus::ON_THE_WAY_TO_CITY) {
                    $allowStatus = [
                        ParcelStatus::ARRIVED_AT_CITY,
                    ];
                } elseif ($parcel->status == ParcelStatus::ARRIVED_AT_CITY) {
                    $allowStatus = [
                        ParcelStatus::DELIVERED,
                        ParcelStatus::DELIVERY_FAILURE,
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_FAILURE) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_FAILED,
                        ParcelStatus::PARCEL_CANCEL,
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_FAILED) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_ATTEMPT1,
                        ParcelStatus::RETURN_TO_COURIER,
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_ATTEMPT1) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_ATTEMPT2,
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_ATTEMPT2) {
                    $allowStatus = [
                        ParcelStatus::DELIVERY_ATTEMPT3,
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERY_ATTEMPT3) {
                    $allowStatus = [
                        ParcelStatus::RETURN_TO_COURIER
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_TO_COURIER) {
                    $allowStatus = [
                        ParcelStatus::RETURN_TO_COURIER_CANCEL,
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT,
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT) {
                    $allowStatus = [
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT_CANCEL,
                        ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE,
                        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1) {
                    $allowStatus = [
                        // ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1_CANCEL,
                        // ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE,
                        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
                    ];
                } elseif ($parcel->status == ParcelStatus::PARCEL_CANCEL) {
                    $allowStatus = [
                        ParcelStatus::RETURNING
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURNING) {
                    $allowStatus = [
                        ParcelStatus::TRANSIT_SENDING_PROVINCE
                    ];
                } elseif ($parcel->status == ParcelStatus::TRANSIT_SENDING_PROVINCE) {
                    $allowStatus = [
                        ParcelStatus::ON_THE_WAY_SENDING_PROVINCE
                    ];
                } elseif ($parcel->status == ParcelStatus::ON_THE_WAY_SENDING_PROVINCE) {
                    $allowStatus = [
                        ParcelStatus::ARRIVED_TO_SENDING_HUB
                    ];
                } elseif ($parcel->status == ParcelStatus::ARRIVED_TO_SENDING_HUB) {
                    $allowStatus = [
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT,
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT) {
                    $allowStatus = [
                        ParcelStatus::RETURN_ASSIGN_TO_MERCHANT_CANCEL,
                        ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE,
                        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
                    ];
                } elseif ($parcel->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1) {
                    $allowStatus = [
                        // ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1_CANCEL,
                        // ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE,
                        ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
                    ];
                } elseif ($parcel->status == ParcelStatus::DELIVERED) {
                    // No Action
                }
            }
        } elseif ($parcel->status == ParcelStatus::DELIVERY_FAILURE) {
            $allowStatus = [
                ParcelStatus::PARCEL_CANCEL,
                ParcelStatus::DELIVERY_FAILED,
            ];
        } elseif ($parcel->status == ParcelStatus::PARCEL_CANCEL) {
            $allowStatus = [
                ParcelStatus::RETURN_TO_COURIER,
            ];
        } elseif ($parcel->status == ParcelStatus::PICKUP_RE_SCHEDULE) {
            $allowStatus = [
                ParcelStatus::PICKUP_RE_SCHEDULE_CANCEL,
                ParcelStatus::PICKUP_RE_SCHEDULE,
                ParcelStatus::HEADING_TO_PICKUP_POINT,
                ParcelStatus::UNCONFIRMED,
            ];
        } elseif ($parcel->status == ParcelStatus::DROP_OFF_CITY) {
            $allowStatus = [
                ParcelStatus::DELIVERED,
                ParcelStatus::DELIVERY_FAILURE,
            ];
        } elseif ($parcel->status == ParcelStatus::RECEIVED_BY_HUB) {
            $allowStatus = [
                ParcelStatus::DELIVERED,
                ParcelStatus::DELIVERY_FAILURE,
            ];
        } elseif ($parcel->status == ParcelStatus::DELIVERY_FAILED) {
            $allowStatus = [
                ParcelStatus::PARCEL_CANCEL,
                ParcelStatus::RETURN_TO_COURIER,
            ];
        } elseif ($parcel->status == parcelStatus::TRANSFER_TO_HUB) {
            $allowStatus = [
                ParcelStatus::TRANSFER_TO_HUB_CANCEL,
                ParcelStatus::DELIVERY_FAILURE,
            ];
        } elseif ($parcel->status == parcelStatus::RETURN_TO_COURIER) {
            $allowStatus = [
                ParcelStatus::RETURN_TO_COURIER_CANCEL,
                ParcelStatus::RETURN_ASSIGN_TO_MERCHANT
            ];
        } elseif ($parcel->status == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT) {
            $allowStatus = [
                ParcelStatus::RETURN_ASSIGN_TO_MERCHANT_CANCEL,
                ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE,
                ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
            ];
        } elseif ($parcel->status == ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE) {
            $allowStatus = [
                ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE_CANCEL,
                ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE,
                ParcelStatus::RETURN_RECEIVED_BY_MERCHANT
            ];
        } elseif ($parcel->status == ParcelStatus::RETURN_RECEIVED_BY_MERCHANT) {
            // No Action
        } elseif ($parcel->status == ParcelStatus::DELIVERED) {
            // No Action
        } elseif ($parcel->status == ParcelStatus::PARTIAL_DELIVERED) {
            // No Action
        }

        if ($parcel->from_state_id == $parcel->to_state_id) {
            $side_check_state_id = $parcel->from_state_id;
        } else {
            $side_check_state_id = $parcel->to_state_id;
        }

        if ($parcel->status > parcelStatus::PROCESSING) {
            if ($parcel->from_state_id == $parcel->to_state_id) {
                $outside_check_state_id = $parcel->from_state_id;
            } else {
                $outside_check_state_id = $parcel->to_state_id;
            }
        } else {
            if ($parcel->from_state_id == $parcel->to_state_id) {
                $outside_check_state_id = $parcel->from_state_id;
            } else {
                $outside_check_state_id = $parcel->from_state_id;
            }
        }

        $parcelStatusArray = [];
        if (!blank($allowStatus)) {
            foreach (trans('parcelStatus') as $key => $status) {
                if (in_array($key, $allowStatus)) {
                    $parcelStatusArray[$key] = $status;
                }
            }
        }

        return $parcelStatusArray;
    }
}


if (!function_exists('StatusParcel')) {
    function StatusParcel($status_id)
    {
        $status = '';
        if ($status_id == ParcelStatus::PENDING) {
            $status = '<span class="badge badge-pill badge-danger">' . trans("parcelStatusShow." . ParcelStatus::PENDING) . '</span>';
        } elseif ($status_id == ParcelStatus::BOOKING) {
            $status = '<span class="badge badge-pill badge-success">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } elseif ($status_id == ParcelStatus::READY_TO_REASSIGN_REGULAR) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } elseif ($status_id == ParcelStatus::READY_TO_REASSIGN_BOOKING) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } elseif ($status_id == ParcelStatus::PICKUP_ASSIGN) {
            $status = '<span class="badge badge-pill badge-primary">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } elseif ($status_id == ParcelStatus::CONFIRMED_BOOKING) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } elseif ($status_id == ParcelStatus::PROCESSING) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } elseif ($status_id == ParcelStatus::DELIVERY_MAN_ASSIGN_CANCEL) {
            $status = '<span class="badge badge-pill badge-warning">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } elseif ($status_id == ParcelStatus::DELIVERY_RE_SCHEDULE_CANCEL) {
            $status = '<span class="badge badge-pill badge-warning">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } elseif ($status_id == ParcelStatus::RECEIVED_WAREHOUSE) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } elseif ($status_id == ParcelStatus::DELIVERY_MAN_ASSIGN) {
            $status = '<span class="badge badge-pill badge-warning">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } elseif ($status_id == ParcelStatus::DELIVERY_RE_SCHEDULE) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } elseif ($status_id == ParcelStatus::RETURN_TO_COURIER) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } elseif ($status_id == ParcelStatus::RETURN_ASSIGN_TO_MERCHANT) {
            $status = '<span class="badge badge-pill badge-dark">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } elseif ($status_id == ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE) {
            $status = '<span class="badge badge-pill badge-dark">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } elseif ($status_id == ParcelStatus::RETURN_RECEIVED_BY_MERCHANT) {
            $status = '<span class="badge badge-pill badge-success">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } elseif ($status_id == ParcelStatus::DELIVER) {
            $status = '<span class="badge badge-pill badge-success">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } elseif ($status_id == ParcelStatus::DELIVERED) {
            $status = '<span class="badge badge-pill badge-success">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } elseif ($status_id == ParcelStatus::PARTIAL_DELIVERED) {
            $status = '<span class="badge badge-pill badge-success">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } elseif ($status_id == ParcelStatus::RETURN_WAREHOUSE) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } elseif ($status_id == ParcelStatus::ASSIGN_MERCHANT) {
            $status = '<span class="badge badge-pill badge-secondary">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } elseif ($status_id == ParcelStatus::RETURNED_MERCHANT) {
            $status = '<span class="badge badge-pill badge-dark">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } elseif ($status_id == ParcelStatus::PICKUP_RE_SCHEDULE) {
            $status = '<span class="badge badge-pill badge-dark">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } elseif ($status_id == ParcelStatus::RECEIVED_BY_PICKUP_MAN) {
            $status = '<span class="badge badge-pill badge-success">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } elseif ($status_id == ParcelStatus::TRANSFER_TO_HUB) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } elseif ($status_id == ParcelStatus::RECEIVED_BY_HUB) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } elseif ($status_id == ParcelStatus::DROPPED_OFF_AT_HUB) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . $status_id) . '</span>';
        } elseif ($status_id == ParcelStatus::PARCEL_CANCEL) {
            $status = '<span class="badge badge-pill badge-secondary">' . trans("parcel.cancelled") . '</span>';
        } elseif ($status_id == ParcelStatus::UNCONFIRMED) {
            $status = '<span class="badge badge-pill badge-dark">' . trans("parcelStatusShow." . ParcelStatus::UNCONFIRMED) . '</span>';
        } elseif ($status_id == ParcelStatus::UNCONFIRMED_BOOKING) {
            $status = '<span class="badge badge-pill badge-dark">' . trans("parcelStatusShow." . ParcelStatus::UNCONFIRMED_BOOKING) . '</span>';
        } elseif ($status_id == ParcelStatus::CONFIRMED) {
            $status = '<span class="badge badge-pill badge-success">' . trans("parcelStatusShow." . ParcelStatus::CONFIRMED) . '</span>';
        } elseif ($status_id == ParcelStatus::HEADING_TO_PICKUP_POINT) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . ParcelStatus::HEADING_TO_PICKUP_POINT) . '</span>';
        } elseif ($status_id == ParcelStatus::PICKED_UP) {
            $status = '<span class="badge badge-pill badge-warning">' . trans("parcelStatusShow." . ParcelStatus::PICKED_UP) . '</span>';
        } elseif ($status_id == ParcelStatus::HEADING_TO_DROP_OFF_HUB) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . ParcelStatus::HEADING_TO_DROP_OFF_HUB) . '</span>';
        } elseif ($status_id == ParcelStatus::HEADING_TO_DELIVERY_POINT) {
            $status = '<span class="badge badge-pill badge-warning">' . trans("parcelStatusShow." . ParcelStatus::HEADING_TO_DELIVERY_POINT) . '</span>';
        } elseif ($status_id == ParcelStatus::IMMEDIATE_EXECUTION) {
            $status = '<span class="badge badge-pill badge-dark">' . trans("parcelStatusShow." . ParcelStatus::IMMEDIATE_EXECUTION) . '</span>';
        } elseif ($status_id == ParcelStatus::DROP_OFf_HUB1) {
            $status = '<span class="badge badge-pill badge-warning">' . trans("parcelStatusShow." . ParcelStatus::DROP_OFf_HUB1) . '</span>';
        } elseif ($status_id == ParcelStatus::TRANSIT_OUT_CITY) {
            $status = '<span class="badge badge-pill badge-dark">' . trans("parcelStatusShow." . ParcelStatus::TRANSIT_OUT_CITY) . '</span>';
        } elseif ($status_id == ParcelStatus::ON_THE_WAY_TO_CITY) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . ParcelStatus::ON_THE_WAY_TO_CITY) . '</span>';
        } elseif ($status_id == ParcelStatus::ARRIVED_AT_CITY) {
            $status = '<span class="badge badge-pill badge-warning">' . trans("parcelStatusShow." . ParcelStatus::ARRIVED_AT_CITY) . '</span>';
        } elseif ($status_id == ParcelStatus::DROP_OFF_CITY) {
            $status = '<span class="badge badge-pill badge-info">' . trans("parcelStatusShow." . ParcelStatus::DROP_OFF_CITY) . '</span>';
        } elseif ($status_id == ParcelStatus::DELIVERY_FAILURE) {
            $status = '<span class="badge badge-pill badge-warning">' . trans("parcelStatusShow." . ParcelStatus::DROP_OFF_CITY) . '</span>';
        } elseif ($status_id == ParcelStatus::DELIVERY_FAILED) {
            $status = '<span class="badge badge-pill badge-warning">' . trans("parcelStatusShow." . ParcelStatus::DROP_OFF_CITY) . '</span>';
        }
        return $status;
    }
}

if (!function_exists('merchantPayments')) {
    function merchantPayments($merchantID)
    {
        $totalMerchantPayments['paidAmount'] = 0;
        $totalMerchantPayments['pendingAmount'] = 0;
        $totalMerchantPayments['paidAmount'] =  Payment::whereIn('merchant_id', $merchantID)->where(['status' => \App\Enums\ApprovalStatus::APPROVED])->sum('amount');
        $totalMerchantPayments['pendingAmount'] =  Payment::whereIn('merchant_id', $merchantID)->where(['status' => \App\Enums\ApprovalStatus::PENDING])->sum('amount');
        return $totalMerchantPayments;
    }
}

if (!function_exists('parcelExpense')) {
    function parcelExpense($id)
    {

        $income  = DeliverymanStatement::where('parcel_id', $id)->where('type', AccountHeads::INCOME)->where('cash_collection', 0)->sum('amount');
        $expense = DeliverymanStatement::where('parcel_id', $id)->where('type', AccountHeads::EXPENSE)->where('cash_collection', 0)->sum('amount');
        return ($income - $expense);
    }
}

if (!function_exists('parcelExpenseTotal')) {
    function parcelExpenseTotal($ids)
    {
        $income  = DeliverymanStatement::whereIn('parcel_id', $ids)->where('type', AccountHeads::INCOME)->where('cash_collection', 0)->sum('amount');
        $expense = DeliverymanStatement::whereIn('parcel_id', $ids)->where('type', AccountHeads::EXPENSE)->where('cash_collection', 0)->sum('amount');
        return ($income - $expense);
    }
}

if (!function_exists('totalParcelsCashcollection')) {
    function totalParcelsCashcollection($parcels)
    {
        $total_cash_collection = 0;
        foreach ($parcels as $key => $parcel) {
            $total_cash_collection += $parcel->sum('cash_collection');
        }
        return $total_cash_collection;
    }
}

if (!function_exists('withoutUser')) {

    function withoutUser($ids)
    {

        $user = User::WhereNotIn('id', $ids)->WhereNotIn('user_type', [UserType::DELIVERYMAN, UserType::MERCHANT])->where('status', Status::ACTIVE)->get();
        if (!blank($user)) :
            return $user;
        else :
            return [];
        endif;
    }
}

if (!function_exists('unpaidUser')) {
    function unpaidUser($ids)
    {
        $users = User::WhereIn('id', $ids)->WhereNotIn('user_type', [UserType::DELIVERYMAN, UserType::MERCHANT])->where('status', Status::ACTIVE)->get();
        if (!blank($users)) :
            return $users;
        else :
            return [];
        endif;
    }
}


if (!function_exists('user')) {
    function user($id)
    {
        $user = User::find($id);
        if (!blank($user)) :
            return $user;
        else :
            return '';
        endif;
    }
}

if (!function_exists('singleUser')) {
    function singleUser($id)
    {
        $user = User::find($id);
        if (!blank($user)) :
            return $user;
        else :
            return '';
        endif;
    }
}

if (!function_exists('todoStatus')) {
    function TodoStatus($todo)
    {
        $todoStatus = '';
        $allowStatus = [];

        if ($todo->status  == todoStatus::PENDING) {
            $allowStatus = [todoStatus::PROCESSING];
        } elseif ($todo->status == todoStatus::PROCESSING) {
            $allowStatus = [
                todoStatus::COMPLETED,
            ];
        } elseif ($todo->status  == todoStatus::COMPLETED) {
            $allowStatus = [];
        } else {
            $allowStatus = [todoStatus::PENDING];
        }
        $todoStatusArray = [];
        if (!blank($allowStatus)) {
            foreach (trans('to_do') as $key => $status) {
                if (in_array($key, $allowStatus)) {
                    $todoStatusArray[$key] = $status;
                }
            }
        }
        if (!blank($todoStatusArray)) {
            foreach ($todoStatusArray as $key => $status) {
                if ($key == todoStatus::PENDING) {
                    $todoStatus .= '<a  class="dropdown-item pending" data-title="pending" data-id="' . $todo->id . '" id="todo_btn" data-url="' . route("todo.processing") . '" data-toggle="modal" data-target="#todoStatus' . $key . '"  href="#">' . $status . '</a>';
                } elseif ($key == todoStatus::PROCESSING) {
                    $todoStatus .= '<a  class="dropdown-item processing" data-id="' . $todo->id . '" id="todo_btn" data-title="processing" data-url="' . route("todo.processing") . '" data-toggle="modal" data-target="#todoStatus' . $key . '"  href="#">' . $status . '</a>';
                } else {
                    $todoStatus .= '<a  class="dropdown-item completed" data-title="completed" data-id="' . $todo->id . '" id="todo_btn" data-url="' . route("todo.completed") . '" data-toggle="modal" data-target="#todoStatus1' . $key . '"  href="#">' . $status . '</a>';
                }
            }
            return $todoStatus;
        }
    }
}
//income
if (!function_exists('dayIncomeCount')) {
    function dayIncomeCount($date)
    {
        $date       = Carbon::parse($date)->format('Y-m-d');
        $income     = Income::where('date', $date)->get();
        if (!blank($income)) :
            return $income->sum('amount');
        else :
            return 0;
        endif;
    }
}

//expense
if (!function_exists('dayExpenseCount')) {
    function dayExpenseCount($date)
    {
        $date     = Carbon::parse($date)->format('Y-m-d');
        $Expense  = Expense::where('date', $date)->get();
        if (!blank($Expense)) :
            return $Expense->sum('amount');
        else :
            return 0;
        endif;
    }
}

//new parcel
if (!function_exists('dayNewParcelCount')) {
    function dayNewParcelCount($date)
    {
        $date       = Carbon::parse($date)->format('Y-m-d');
        $parcels    = Parcel::where('created_at', 'like', $date . '%')->count();
        return $parcels;
    }
}

//processing parcel
if (!function_exists('dayProcessingParcelCount')) {
    function dayProcessingParcelCount($date)
    {
        $date       = Carbon::parse($date)->format('Y-m-d');
        $parcels    = Parcel::wherein('status', [
            ParcelStatus::PROCESSING,
            ParcelStatus::HEADING_TO_PICKUP_POINT,
            ParcelStatus::PICKED_UP,
            ParcelStatus::HEADING_TO_DELIVERY_POINT,
            ParcelStatus::DROP_OFF_CITY,
            ParcelStatus::DROP_OFf_HUB1,
            ParcelStatus::HEADING_TO_DROP_OFF,
            ParcelStatus::TRANSIT_OUT_CITY,
            ParcelStatus::ON_THE_WAY_TO_CITY,
            ParcelStatus::ARRIVED_AT_CITY,
            ParcelStatus::DELIVERY_MAN_ASSIGN_SECOND_PROVINCE,
            ParcelStatus::MARKETPLACE_ACCEPTED,
            ParcelStatus::MARKETPLACE_PICKED_UP,
            ParcelStatus::MARKETPLACE_SENDER_PAYMENT_AWAITING,
            ParcelStatus::MARKETPLACE_RECEIVER_PAYMENT_AWAITING,
        ])->where('updated_at', 'like', $date . '%')->count();
        return $parcels;
    }
}

//delivered parcel
if (!function_exists('dayDeliveredParcelCount')) {
    function dayDeliveredParcelCount($date)
    {
        $date       = Carbon::parse($date)->format('Y-m-d');
        $parcels    = Parcel::wherein('status', [
            ParcelStatus::DELIVERED,
            ParcelStatus::PARTIAL_DELIVERED,
            ParcelStatus::MARKETPLACE_DELIVERED,
        ])->where('updated_at', 'like', $date . '%')->count();
        return $parcels;
    }
}

//merchant reve income
if (!function_exists('dayMerchantRevIncomeCount')) {

    function dayMerchantRevIncomeCount($date)
    {
        $date     = Carbon::parse($date)->format('Y-m-d');
        $merchant  = MerchantStatement::where('type', AccountHeads::INCOME)->where('date', $date)->get();
        if (!blank($merchant)) :
            return $merchant->sum('amount');
        else :
            return 0;
        endif;
    }
}

//merchant reve expense
if (!function_exists('dayMerchantRevExpenseCount')) {

    function dayMerchantRevExpenseCount($date)
    {
        $date     = Carbon::parse($date)->format('Y-m-d');
        $merchant  = MerchantStatement::where('type', AccountHeads::EXPENSE)->where('date', $date)->get();
        if (!blank($merchant)) :
            return $merchant->sum('amount');
        else :
            return 0;
        endif;
    }
}

//deliveryman reve income
if (!function_exists('dayDeliverymanRevIncomeCount')) {

    function dayDeliverymanRevIncomeCount($date)
    {
        $date     = Carbon::parse($date)->format('Y-m-d');
        $merchant  = DeliverymanStatement::where('type', AccountHeads::INCOME)->where('date', $date)->get();
        if (!blank($merchant)) :
            return $merchant->sum('amount');
        else :
            return 0;
        endif;
    }
}

//deliveryman reve expense
if (!function_exists('dayDeliverymanRevExpenseCount')) {

    function dayDeliverymanRevExpenseCount($date)
    {
        $date     = Carbon::parse($date)->format('Y-m-d');
        $merchant  = DeliverymanStatement::where('type', AccountHeads::EXPENSE)->where('date', $date)->get();
        if (!blank($merchant)) :
            return $merchant->sum('amount');
        else :
            return 0;
        endif;
    }
}
//end dashboard

if (!function_exists('parcelsStatus')) {

    function parcelsStatus($parcels, $ids = '', $parcel_ids = '')
    {

        if ($parcel_ids == '') :
            $parcel_ids = [];
            foreach ($parcels as $parcls) {
                foreach ($parcls as $key => $parcel) {
                    $parcel_ids[] = $parcel->id;
                }
            }
        endif;
        $parcels = Parcel::whereIn('id', $parcel_ids)->get();
        if ($ids !== '') :
            return $parcel_ids;

        else :
            return $parcels->groupBy('status');

        endif;
    }
}

if (!function_exists('idWiseParcels')) {

    function idWiseParcels($parcels, $neeId = '', $IdParcels = '')
    {

        if ($IdParcels !== '') :
            return Parcel::whereIn('id', $IdParcels)->get();
        elseif ($neeId !== '') :

            $p_ids = $parcels->pluck('id')->toArray();

            return $p_ids;
        endif;
    }
}


if (!function_exists('vehicle')) {
    function vehicle()
    {
        return Vehicle::all();
    }
}
if (!function_exists('driver')) {
    function driver()
    {
        return DeliveryMan::all();
    }
}


if (!function_exists('salaryPayments')) {

    function salaryPayments($user_id, $salaryPayments)
    {
        $user_id = $user_id ?? null;
        $amount = 0;
        foreach ($salaryPayments as $key => $payment) {
            if ($payment->user_id == $user_id && $payment->amount > 0) :
                $amount += $payment->amount;
            endif;
        }
        return $amount;
    }
}


if (!function_exists('oldLogDetails')) {

    function oldLogDetails($oldLogs, $newLogs)
    {
        foreach ($oldLogs as $key => $value) {
            if ($newLogs == $key) {
                return $value;
            }
        }
    }
}

//notifications
if (!function_exists('notifications')) {

    function notifications()
    {

        $notifications     = [];
        //suports
        $sevendaysBefore    = \Carbon\Carbon::today()->subDays(7)->startOfDay()->toDateTimeString();
        $today              = \Carbon\Carbon::today()->endOfDay()->toDateTimeString();

        $supports          = Support::whereNot('user_id', Auth::user()->id)->orderBy('created_at', 'DESC')->select('id', 'user_id', 'subject', 'created_at')->whereBetween('created_at', [$sevendaysBefore, $today])->get();

        foreach ($supports as  $support) {
            $notifications[] = [

                'type'      => 'support',
                'support_id' => $support->id,
                'user_id'   => $support->user_id,
                'subject'   => $support->subject,
                'created_at' => $support->created_at->format('Y-m-d H:i:s'),
            ];
        }

        $supportsChats          = SupportChat::orderBy('created_at', 'DESC')->select('id', 'support_id', 'user_id', 'created_at')->whereBetween('created_at', [$sevendaysBefore, $today])->get();
        $supportChats            =  $supportsChats->groupBy('support_id');
        foreach ($supportChats as  $key => $chats) {
            $supportCheck       = Support::find($key);
            if ($supportCheck->user_id == Auth::user()->id) :
                foreach ($chats as  $chat) {
                    if ($chat->user_id !== Auth::user()->id) :
                        $notifications[] = [
                            'type'      => 'support',
                            'support_id' => $chat->support_id,
                            'user_id'   => $chat->user_id,
                            'subject'   => $chat->support->subject,
                            'created_at' => $chat->created_at->format('Y-m-d H:i:s'),
                        ];
                    endif;
                }
            else :
                $chats_users   = $chats->pluck('user_id')->toArray();
                if (in_array(Auth::user()->id, $chats_users)) :
                    $firstChatCheck = SupportChat::where(['support_id' => $key, 'user_id' => Auth::user()->id])->first();

                    foreach ($chats as  $chat) {
                        $firstChatDate = strtotime(Carbon::parse($firstChatCheck->created_at)->format('Y-m-d H:i:s'));
                        if ($chat->user_id !== Auth::user()->id) :
                            $chatDateTime  = strtotime(Carbon::parse($chat->created_at)->format('Y-m-d H:i:s'));
                            if ($firstChatDate <= $chatDateTime) :
                                $notifications[] = [
                                    'type'      => 'support',
                                    'support_id' => $chat->support_id,
                                    'user_id'   => $chat->user_id,
                                    'subject'   => $chat->support->subject,
                                    'created_at' => $chat->created_at->format('Y-m-d H:i:s'),
                                ];
                            endif;
                        endif;
                    }
                endif;
            endif;
        }

        //news and offers
        $news_offer      = NewsOffer::orderBy('created_at', 'DESC')->limit(5)->get();
        foreach ($news_offer as  $newsoffer) {

            $notifications[] = [
                'type'      => 'newsoffer',
                'user_id'   => $newsoffer->author,
                'subject'   => $newsoffer->title,
                'created_at' => $newsoffer->created_at->format('Y-m-d H:i:s'),
            ];
        }
        //end news and offers
        return collect($notifications)->sortByDesc('created_at');
    }
}


if (!function_exists('calendarnewsoffer')) {
    function calendarnewsoffer($date)
    {
        $from  = Carbon::parse($date)->startOfDay()->format('Y-m-d H:i:s');
        $to    = Carbon::parse($date)->endOfDay()->format('Y-m-d H:i:s');
        return NewsOffer::whereBetween('created_at', [$from, $to])->orderBy('id', 'desc')->first();
    }
}


if (!function_exists('static_asset')) {
    function static_asset($path = '')
    {
        return asset($path);
        // if (strpos(php_sapi_name(), 'cli') !== false || defined('LARAVEL_START_FROM_PUBLIC')) {
        //     return  app('url')->asset($path);
        // } else {
        //     return  app('url')->asset('public/' . $path);
        // }
    }
}

if (!function_exists('paginate_redirect')) {
    function paginate_redirect($request)
    {
        return   $request->page ? 'admin/order/index?page=' . $request->page : 'admin/order/index';
    }
}

if (!function_exists('globalSettings')) {
    function globalSettings($key)
    {
        $settings   = Setting::where('key', $key)->first();
        if ($settings) :
            return $settings->value;
        endif;
        return null;
    }
}

if (!function_exists('smsSettings')) {
    function smsSettings($key)
    {
        $settings   = SmsSetting::where('key', $key)->first();
        if ($settings) :
            return $settings->value;
        endif;
        return null;
    }
}

if (!function_exists('MerchantSettings')) {
    function MerchantSettings($key)
    {
        $settings   = MerchantSetting::where(['merchant_id' => Auth::user()->merchant->id, 'key' => $key])->first();
        if ($settings) :
            return $settings->value;
        endif;
        return null;
    }
}

if (!function_exists('MerchantSearchSettings')) {
    function MerchantSearchSettings($merchant_id, $key)
    {
        $settings   = MerchantSetting::where(['merchant_id' => $merchant_id, 'key' => $key])->first();
        if ($settings) :
            return $settings->value;
        endif;
        return null;
    }
}


if (!function_exists('statusIcon')) {
    function statusIcon($status)
    {
        switch ($status) {
            case ParcelStatus::PENDING:
                return 'fas fa-hourglass-end';
                break;
            case ParcelStatus::PICKUP_ASSIGN:
                return 'fas fa-truck';
                break;
            case ParcelStatus::PICKUP_RE_SCHEDULE:
                return 'fas fa-truck';
                break;
            case ParcelStatus::RECEIVED_WAREHOUSE:
                return 'fas fa-warehouse';
                break;
            case ParcelStatus::TRANSFER_TO_HUB:
                return 'fa fa-right-left';
                break;
            case ParcelStatus::RECEIVED_BY_HUB:
                return 'fa fa-warehouse';
                break;
            case ParcelStatus::DELIVERY_MAN_ASSIGN:
                return 'fa fa-people-carry';
                break;
            case ParcelStatus::DELIVERY_RE_SCHEDULE:
                return 'fa fa-people-carry';
                break;
            case ParcelStatus::DELIVERED:
                return 'fas fa-handshake';
                break;
            case ParcelStatus::PARTIAL_DELIVERED:
                return 'fas fa-handshake';
                break;
            case ParcelStatus::RETURN_TO_COURIER:
                return 'fa fa-warehouse';
                break;
            case ParcelStatus::RETURN_ASSIGN_TO_MERCHANT:
                return 'fas fa-truck';
                break;
            case ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE:
                return 'fas fa-truck';
                break;
            case ParcelStatus::RETURN_RECEIVED_BY_MERCHANT:
                return 'fas fa-store';
                break;
        }
    }
}

if (!function_exists('MerchantParcels')) {
    function MerchantParcels($merchant_id)
    {
        $data = [];
        $data['total_parcels']         = Parcel::where('merchant_id', $merchant_id)->count();
        $data['total_cash_amount']     = Parcel::where('merchant_id', $merchant_id)->sum('cash_collection');
        $data['total_current_payable'] = Parcel::where('merchant_id', $merchant_id)->sum('current_payable');
        return (object)$data;
    }
}

if (!function_exists('section')) {
    function section($type, $key)
    {
        $all_sections = Section::with('upload')->select('type', 'key', 'value')->get();
        $sections = [];
        foreach ($all_sections as $section) {
            if (str_contains($section->key, 'image') || str_contains($section->key, 'banner')) {
                $sections[$section->type][$section->key] = $section->image;
            } else {
                $sections[$section->type][$section->key] = $section->value;
            }
        }
        return data_get($sections, $type . '.' . $key, '');
    }
}


if (!function_exists('MyLeave')) {
    function MyLeave($leave_assign_id, $user_id, $role_id)
    {
        $leaveAssign   = LeaveAssign::find($leave_assign_id);
        $leaveRequests = LeaveRequest::where([
            'user_id'    => $user_id,
            'leave_assign_id' => $leave_assign_id,
            'role_id'        => $role_id,
            'status'         => LeaveStatus::APPROVED
        ])->orderByDesc('id')->whereYear('created_at', Date('Y'))->get();

        $approvedDays  = 0;
        $a = [];
        foreach ($leaveRequests as  $leave) {
            $start_time    =  Carbon::parse($leave->leave_from)->startOfDay();
            $end_time      =  Carbon::parse($leave->leave_to)->endOfDay()->addMinute(1);
            $approvedDays +=  Carbon::parse($start_time)->diff($end_time)->days;
            $a[$leave->id] = Carbon::parse($start_time)->diff($end_time)->days;
        }
        $remaining_days = ($leaveAssign->days - $approvedDays);
        return $remaining_days;
    }
}




function holidayDates()
{
    //holiday
    $holidays      = Holiday::whereYear('from', date('Y'))->whereMonth('from', date('m'))->get();
    $holiday_dates = [];
    foreach ($holidays as $holiday) {
        $days            = Carbon::parse($holiday->from)->diffInDays($holiday->to);
        for ($i = 0; $i <= $days; $i++) {
            $holiday_dates[] =  Carbon::parse($holiday->from)->addDays($i)->format('Y-m-d');
        }
    }
    return $holiday_dates;
}

function leaveDates($user_id)
{
    $leave_requests      = LeaveRequest::where('user_id', $user_id)->where('status', LeaveStatus::APPROVED)->whereYear('leave_from', date('Y'))->whereMonth('leave_from', date('m'))->get();

    $leave_dates = [];
    foreach ($leave_requests as $key => $leave) {
        $leavedays            = Carbon::parse($leave->leave_from)->diffInDays($leave->leave_to);
        for ($i = 0; $i <= $leavedays; $i++) {
            $leave_dates[] =  Carbon::parse($leave->leave_from)->addDays($i)->format('Y-m-d');
        }
    }
    return $leave_dates;
}

if (!function_exists('dayAttendance')) {
    function dayAttendance($user_id, $date)
    {

        //holiday check
        if (in_array($date, holidayDates())) :
            return '<span class="m-2" title="' . __('holiday') . '"><i class="fa fa-star text-warning "></i></span>';
        //end holiday check
        endif;
        //leave check
        if (in_array($date, leaveDates($user_id))) :
            return '<span class="m-2 text-danger leave-icon" title="' . __('parcel.on_leave') . '"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0v2z"/>
                <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
              </svg></span>';
        endif;
        //end leave check

        $absent  =  '<a href="#" class="modalBtn" data-bs-toggle="modal" data-bs-target="#dynamic-modal" data-title="' . __('parcel.mark_attendance') . '" data-url="' . route('hrm.attendance.create.modal', ['user_id' => $user_id, 'date' => $date]) . '"  data-bs-toggle="tooltip" title="' . __('absent') . '"
            data-bs-placement="top" > <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16"><path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z"/></svg></a>';

        $present     = '<a href="#" class="modalBtn" data-bs-toggle="modal" data-bs-target="#dynamic-modal" data-title="' . __('parcel.attendance_details') . '" data-url="' . route('hrm.attendance.details.modal', ['user_id' => $user_id, 'date' => $date]) . '" data-bs-toggle="tooltip" title="' . __('present') . '"
            data-bs-placement="top"><i class="fa fa-check text-success"></i></a>';

        $notCheckout     = '<a href="#" class="modalBtn" data-bs-toggle="modal" data-bs-target="#dynamic-modal"  data-title="' . __('parcel.check_out_attendance') . '" data-url="' . route('hrm.attendance.checkout.modal', ['user_id' => $user_id, 'date' => $date]) . '" data-bs-toggle="tooltip" title="' . __('present') . '"
            data-bs-placement="top"><i class="fa fa-hourglass-start text-primary"></i></a>';
        $attendance = Attendance::where(['user_id' => $user_id])->whereDate('date', $date)->first();

        if ($attendance) :
            if ($attendance->status == AttendanceStatus::CHECK_IN) :
                return $notCheckout;
            else :
                return $present;
            endif;
        else :
            return $absent;
        endif;
    }
}


if (!function_exists('totalPresent')) {
    function totalPresent($user_id, $data)
    {
        $from  = $data['start_month'];
        $to    = Carbon::parse($data['end_month'])->subSecond(1)->toDateTimeString();

        $attendance = Attendance::where('user_id', $user_id)->where('status', AttendanceStatus::CHECK_OUT)->whereBetween('date', [$from, $to])->get();
        return $attendance->count();
    }
}

if (!function_exists('dateDay')) {
    function dateDay($date)
    {
        $data       = [];
        $data['d']  = Carbon::parse($date)->format('d');
        $data['D']  = Carbon::parse($date)->format('D');
        return $data;
    }
}






if (!function_exists('merchantPaymentNotification')) {
    function merchantPaymentNotification()
    {
        $merchants = Merchant::where('status', Status::ACTIVE)->get();
        foreach ($merchants as $merchant) {
            if (count($merchant->merchant_payment_date) > 0) {
                foreach ($merchant->merchant_payment_date as $key => $merchant_payment) {
                    if ($merchant_payment->date) {
                        $start_date = Carbon::createFromDate(date('Y-m-d'));
                        $end_date = Carbon::createFromDate($merchant_payment->date);
                        if (strtotime($start_date) > strtotime($end_date)) {
                            $diff_day = $end_date->diffInDays($start_date);
                            if (in_array($diff_day, array(0, 1, 2))) {
                                if (3 > $merchant_payment->total_count) {

                                    if (
                                        date('Y-m-d H:i:s') == date('Y-m-d 11:00:01') ||
                                        date('Y-m-d H:i:s') == date('Y-m-d 16:00:01') ||
                                        date('Y-m-d H:i:s') == date('Y-m-d 19:00:01')
                                    ) {
                                        $title = 'Payment Date';
                                        $msgNote = 'You have ' . $diff_day . ' day remaining until the payment date';
                                        $super_admins = User::where('user_type', UserType::ADMIN)->get();
                                        foreach ($super_admins as $super_admin) {
                                            if (isset($super_admin->web_token)) {
                                                app(PushNotificationService::class)->sendCustomPushNotification($super_admin->web_token, $title,  $msgNote);
                                                app(PushNotificationService::class)->sendCustomPushNotification($super_admin->device_token, $title,  $msgNote);
                                            }
                                        }
                                        $pushNotification = MerchantPaymentDate::find($merchant_payment->id);
                                        $pushNotification->total_count += 1;
                                        $pushNotification->save();
                                    }
                                } else {
                                    $merchant_payment_data = MerchantPaymentDate::find($merchant_payment->id);
                                    $date_update     = Carbon::createFromDate($merchant_payment->date);
                                    $new_date        = $date_update->addMonths();
                                    $merchant_payment_data->date  = $new_date;
                                    $merchant_payment_data->save();
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

if (!function_exists('checkPermission')) {
    function checkPermission($keywords, $permissions)
    {
        $counter = 0;
        foreach ($keywords as $key => $keyword) {
            if ($permissions !== null && in_array($keyword, $permissions)) {
                $counter += 1;
            }
        }
        return $counter;
    }
}

function array_dot($array, $prepend = '')
{
    $results = [];
    foreach ($array as $key => $value) {
        if (is_int($key)) {
            continue;
        }
        if (is_array($value) || is_object($value)) {
            continue;
        } else {
            $results[$prepend . $key] = $value;
        }
    }
    return $results;
}

function convertLangFilesToJson($locale)
{
    $phpLangPath = base_path("lang/$locale");
    if (!File::isDirectory($phpLangPath)) {
        return;
    }
    $translations = [];
    // Iterate over each PHP file in the lang directory
    foreach (File::allFiles($phpLangPath) as $file) {
        $fileTranslations = include $file->getPathname();
        // Flatten the array and merge it with existing translations
        $translations[] = array_dot($fileTranslations);
    }
    return ((object)Arr::collapse($translations));
}

if (!function_exists('getLanguageAllData')) {
    function getLanguageAllData()
    {
        $getLanguage = [];
        $path = base_path('/lang');
        $directories = File::directories($path);
        $directoryNames = array_map(function ($dir) {
            return basename($dir);
        }, $directories);
        foreach ($directoryNames as $key => $lang_code) {
            $getJsonData = convertLangFilesToJson($lang_code);
            $langData[$lang_code] =  $getJsonData;
            $newJsonData = json_encode($langData, JSON_UNESCAPED_UNICODE);
            File::put(base_path('/lang/vue-language.json'), $newJsonData);
            $getLanguage[] = $newJsonData;
        }
    }
}
