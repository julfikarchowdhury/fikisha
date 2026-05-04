<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $attributes = [
            'dashboard'            => [
                'read'                       => 'dashboard_read',
                'calendar'                   => 'calendar_read',
                'total_Parcel'               => 'total_parcel',
                'total_user'                 => 'total_user',
                'total_merchant'             => 'total_merchant',
                'total_delivery_man'         => 'total_delivery_man',
                'total_hubs'                 => 'total_hubs',
                'total_accounts'             =>  'total_accounts',
                'total_parcels_pending'      =>  'total_parcels_pending',
                'total_pickup_assigned'      =>  'total_pickup_assigned',
                'total_received_warehouse'   =>  'total_received_warehouse',
                'total_deliveryman_assigned' =>  'total_deliveryman_assigned',
                'total_partial_deliverd'     =>  'total_partial_deliverd',
                'total_parcels_deliverd'     =>  'total_parcels_deliverd',

                'recent_accounts'             =>  'recent_accounts',
                'recent_salary'               =>  'recent_salary',
                'recent_hub'                  =>  'recent_hub',
                'all_statements'              =>  'all_statements',
                'income_expense_charts'       =>  'income_expense_charts',
                'merchant_revenue_charts'     =>  'merchant_revenue_charts',
                'deliveryman_revenue_charts'  =>  'deliveryman_revenue_charts',
                'courier_revenue_charts'      =>  'courier_revenue_charts',
                'recent_parcels'              =>  'recent_parcels',
                'bank_transaction'            =>  'bank_transaction'

            ],
            'statistics'                 => [
                'read'  => 'statistics_read'
            ],
            'logs'                 => [
                'read'  => 'log_read'
            ],
            'hubs'                 => [
                'read'  => 'hub_read',
                'create' => 'hub_create',
                'update' => 'hub_update',
                'delete' => 'hub_delete',
                'incharge_read' => 'hub_incharge_read',
                'incharge_create' => 'hub_incharge_create',
                'incharge_update' => 'hub_incharge_update',
                'incharge_delete' => 'hub_incharge_delete',
                'incharge_assigned' => 'hub_incharge_assigned',
                'view'          => 'hub_view',
            ],
            'accounts'             => [
                'read'  => 'account_read',
                'create' => 'account_create',
                'update' => 'account_update',
                'delete' => 'account_delete'
            ],
            'income'             => [
                'read'  => 'income_read',
                'create' => 'income_create',
                'update' => 'income_update',
                'delete' => 'income_delete'
            ],
            'expense'             => [
                'read'  => 'expense_read',
                'create' => 'expense_create',
                'update' => 'expense_update',
                'delete' => 'expense_delete'
            ],
            'todo'             => [
                'read'  => 'todo_read',
                'create' => 'todo_create',
                'update' => 'todo_update',
                'delete' => 'todo_delete'
            ],
            'fund_transfer'         => [
                'read'  => 'fund_transfer_read',
                'create' => 'fund_transfer_create',
                'update' => 'fund_transfer_update',
                'delete' => 'fund_transfer_delete'
            ],
            'roles'                => [
                'read'  => 'role_read',
                'create' => 'role_create',
                'update' => 'role_update',
                'delete' => 'role_delete'
            ],
            'designations'         => [
                'read'  => 'designation_read',
                'create' => 'designation_create',
                'update' => 'designation_update',
                'delete' => 'designation_delete'
            ],
            'departments'          => [
                'read'  => 'department_read',
                'create' => 'department_create',
                'update' => 'department_update',
                'delete' => 'department_delete'
            ],
            'users'                => [
                'read'  => 'user_read',
                'create' => 'user_create',
                'update' => 'user_update',
                'delete' => 'user_delete',
                'permission_update' => 'permission_update'
            ],
            'merchant'             => [
                'read'  => 'customer_read',
                'create' => 'customer_create',
                'update' => 'customer_update',
                'delete' => 'customer_delete',
                'view' => 'customer_view',

                'delivery_charge_read'  => 'merchant_delivery_charge_read',
                'delivery_charge_create' => 'merchant_delivery_charge_create',
                'delivery_charge_update' => 'merchant_delivery_charge_update',
                'delivery_charge_delete' => 'merchant_delivery_charge_delete',

                'shop_read'  => 'merchant_shop_read',
                'shop_create' => 'merchant_shop_create',
                'shop_update' => 'merchant_shop_update',
                'shop_delete' => 'merchant_shop_delete',

                'payment_read'  => 'merchant_payment_read',
                'payment_create' => 'merchant_payment_create',
                'payment_update' => 'merchant_payment_update',
                'payment_delete' => 'merchant_payment_delete',
            ],
            'payments'             => [
                'read'  => 'payment_read',
                'create' => 'payment_create',
                'update' => 'payment_update',
                'delete' => 'payment_delete',
                'reject' => 'payment_reject',
                'process' => 'payment_process',
            ],

            'hub_payments'       => [
                'read'  => 'hub_payment_read',
                'create' => 'hub_payment_create',
                'update' => 'hub_payment_update',
                'delete' => 'hub_payment_delete',
                'reject' => 'hub_payment_reject',
                'process' => 'hub_payment_process',
            ],
            'hub_payments_request'  => [
                'read'  => 'hub_payment_request_read',
                'create' => 'hub_payment_request_create',
                'update' => 'hub_payment_request_update',
                'delete' => 'hub_payment_request_delete',
            ],

            'liquid_fragile'       => [
                'read'  => 'liquid_fragile_read',
                'update'  => 'liquid_fragile_update',
                'status_change' => 'liquid_status_change',
            ],
            
            'extra_cost'       => [
                'read'  => 'extra_cost_read',
                'update'  => 'extra_cost_update',
                'status_change' => 'extra_cost_status_change',
            ],

            'database_backup'       => [
                'read'  => 'database_backup_read',
            ],
            'sms_settings'          => [
                'read'          => 'sms_settings_read',
                'create'        => 'sms_settings_create',
                'update'        => 'sms_settings_update',
                'delete'        => 'sms_settings_delete',
                'status_change' => 'sms_settings_status_change',
            ],
            'messanger_settings'          => [
                'manage'                  => 'messanger_settings_manage',
            ],
            'sms_send_settings'      => [
                'read'          => 'sms_send_settings_read',
                'create'        => 'sms_send_settings_create',
                'update'        => 'sms_send_settings_update',
                'delete'        => 'sms_send_settings_delete',
                'status_change' => 'sms_send_settings_status_change',
            ],
            'general_settings'      => [
                'read'  => 'general_settings_read',
                'update' => 'general_settings_update',
            ],
            'notification_settings'   => [
                'read'  => 'notification_settings_read',
                'update' => 'notification_settings_update',
            ],
            'push_notification'   => [
                'read'  => 'push_notification_read',
                'create' => 'push_notification_create',
                'update' => 'push_notification_update',
                'delete' => 'push_notification_delete'
            ],
            'parcel'               => [
                'read'  => 'parcel_read',
                'create' => 'parcel_create',
                'update' => 'parcel_update',
                'delete' => 'parcel_delete',
                'status_update' => 'parcel_status_update'
            ],
            'return_report'      => [
                'read'  => 'return_report_read',
            ],
            'delivery_man'         => [
                'read'  => 'delivery_man_read',
                'create' => 'delivery_man_create',
                'update' => 'delivery_man_update',
                'delete' => 'delivery_man_delete'
            ],
            'shipping_type'    => [
                'read'  => 'shipping_type_read',
                'create' => 'shipping_type_create',
                'update' => 'shipping_type_update',
                'delete' => 'shipping_type_delete'
            ],
            'delivery_charge'      => [
                'read'  => 'delivery_charge_read',
                'create' => 'delivery_charge_create',
                'update' => 'delivery_charge_update',
                'delete' => 'delivery_charge_delete'
            ],
            'delivery_type'      => [
                'read'   => 'delivery_type_read',
                'create' => 'delivery_type_create',
                'update' => 'delivery_type_update',
                'delete' => 'delivery_type_delete',
                'status_change' => 'delivery_type_status_change',
            ],

            'packaging'            => [
                'read'  => 'packaging_read',
                'create' => 'packaging_create',
                'update' => 'packaging_update',
                'delete' => 'packaging_delete'
            ],
            'category'             => [
                'read'  => 'category_read',
                'create' => 'category_create',
                'update' => 'category_update',
                'delete' => 'category_delete'
            ],
            'account_heads'         => [
                'read'  => 'account_heads_read',
            ],
            'salary'                => [
                'read'  => 'salary_read',
                'create' => 'salary_create',
                'update' => 'salary_update',
                'delete' => 'salary_delete'
            ],
            'support'             => [
                'read'  => 'support_read',
                'create' => 'support_create',
                'update' => 'support_update',
                'delete' => 'support_delete',
                'reply' => 'support_reply',
                'status_update' => 'support_status_update'
            ],
            'asset_category'      => [
                'read'  => 'asset_category_read',
                'create' => 'asset_category_create',
                'update' => 'asset_category_update',
                'delete' => 'asset_category_delete'
            ],
            'parcel_category'      => [
                'read'   => 'parcel_category_read',
                'create' => 'parcel_category_create',
                'update' => 'parcel_category_update',
                'delete' => 'parcel_category_delete'
            ],
            'batch_category'      => [
                'read'   => 'batch_category_read',
                'create' => 'batch_category_create',
                'update' => 'batch_category_update',
                'delete' => 'batch_category_delete'
            ],
            'package'              => [
                'read'               => 'package_read',
                'create'             => 'package_create',
                'update'             => 'package_update',
                'delete'             => 'package_delete',
                'status_update'      => 'package_status_update',
                'scan_status_update' => 'package_scan_status_update'
            ],
            'load'              => [
                'read'               => 'load_read',
                'create'             => 'load_create',
                'update'             => 'load_update',
                'delete'             => 'load_delete',
                'status_update'      => 'load_status_update',
                'scan_status_update' => 'load_scan_status_update'
            ],
            'assets'      => [
                'read'  => 'assets_read',
                'create' => 'assets_create',
                'update' => 'assets_update',
                'delete' => 'assets_delete'
            ],
            'vehicle'      => [
                'read'  => 'vehicle_read',
                'create' => 'vehicle_create',
                'update' => 'vehicle_update',
                'delete' => 'vehicle_delete'
            ],
            'news_offer'          => [
                'read'  => 'news_offer_read',
                'create' => 'news_offer_create',
                'update' => 'news_offer_update',
                'delete' => 'news_offer_delete'
            ],
            'bank_transaction'          => [
                'read'  => 'bank_transaction_read',
            ],

            'cash_received_from_delivery_man' => [
                'read'  => 'cash_received_from_delivery_man_read',
                'create' => 'cash_received_from_delivery_man_create', 
                'delete' => 'cash_received_from_delivery_man_delete'
            ],
            'reports' => [
                'parcel_status_reports'   => 'parcel_status_reports',
                'parcel_total_summery'    => 'parcel_total_summery',
                'salary_reports'          => 'salary_reports',
                'merchant_hub_deliveryman' => 'merchant_hub_deliveryman'
            ],
            'salary_generate' => [
                'read'       => 'salary_generate_read',
                'create'     => 'salary_generate_create',
                'update'     => 'salary_generate_update',
                'delete'     => 'salary_generate_delete',
            ],

            'fraud' => [
                'read'       => 'fraud_read',
                'create'     => 'fraud_create',
                'update'     => 'fraud_update',
                'delete'     => 'fraud_delete',
            ],
            'subscribe' => [
                'read'       => 'subscribe_read',
            ],
            'pickup_request' => [
                'regular'       => 'pickup_request_regular',
                'express'       => 'pickup_request_express'
            ],
            'invoice' => [
                'read'              => 'invoice_read',
                'status_update'     => 'invoice_status_update',
                'paid_invoice_read' => 'paid_invoice_read',
                'invoice_generate'  => 'invoice_generate_menually'
            ],
            'social_login_settings' => [
                'read'         => 'social_login_settings_read',
                'update'       => 'social_login_settings_update'
            ],
            'payout_setup_settings' => [
                'read'         => 'payout_setup_settings_read',
                'update'       => 'payout_setup_settings_update'
            ],
            'online_payment' => [
                'read'         => 'online_payment_read',
            ],
            'payout' => [
                'read'         => 'payout_read',
                'create'       => 'payout_create',
            ],
            'currency' => [
                'read'     => 'currency_read',
                'create'   => 'currency_create',
                'update'   => 'currency_update',
                'delete'   => 'currency_delete',
            ],

            'country' => [
                'read'     => 'country_read',
                'create'   => 'country_create',
                'update'   => 'country_update',
                'delete'   => 'country_delete',
            ],

            'city' => [
                'read'     => 'city_read',
                'create'   => 'city_create',
                'update'   => 'city_update',
                'delete'   => 'city_delete',
            ],

            'provinces' => [
                'read'     => 'province_read',
                'create'   => 'province_create',
                'update'   => 'province_update',
                'delete'   => 'province_delete',
            ],

            'district' => [
                'read'     => 'district_read',
                'create'   => 'district_create',
                'update'   => 'district_update',
                'delete'   => 'district_delete',
            ],

            'town' => [
                'read'     => 'town_read',
                'create'   => 'town_create',
                'update'   => 'town_update',
                'delete'   => 'town_delete',
            ],

            //front web
            'social_link' => [
                'read'     => 'social_link_read',
                'create'   => 'social_link_create',
                'update'   => 'social_link_update',
                'delete'   => 'social_link_delete',
            ],
            'services' => [
                'read'     => 'service_read',
                'create'   => 'service_create',
                'update'   => 'service_update',
                'delete'   => 'service_delete',
            ],
 
            'slider' => [
                'read'         => 'slider_read',
                'create'       => 'slider_create',
                'update'       => 'slider_update',
                'delete'       => 'slider_delete', 
            ],

            'why_courier' => [
                'read'     => 'why_courier_read',
                'create'   => 'why_courier_create',
                'update'   => 'why_courier_update',
                'delete'   => 'why_courier_delete',
            ],
            'faq' => [
                'read'     => 'faq_read',
                'create'   => 'faq_create',
                'update'   => 'faq_update',
                'delete'   => 'faq_delete',
            ],
            'partner' => [
                'read'     => 'partner_read',
                'create'   => 'partner_create',
                'update'   => 'partner_update',
                'delete'   => 'partner_delete',
            ],
            // 'blogs' => [
            //     'read'     => 'blogs_read',
            //     'create'   => 'blogs_create',
            //     'update'   => 'blogs_update',
            //     'delete'   => 'blogs_delete',
            // ],
            'pages' => [
                'read'     => 'pages_read',
                'update'   => 'pages_update',
            ],
            'sections' => [
                'read'     => 'section_read',
                'update'   => 'section_update',
            ],
            'wallet_request' => [
                'read'     => 'wallet_request_read',
                'create'    => 'wallet_request_create',
                'delete'   => 'wallet_request_delete',
            ],

            //leave type
            'leave_type' => [
                'read'           => 'leave_type_read',
                'create'         => 'leave_type_create',
                'update'         => 'leave_type_update',
                'delete'         => 'leave_type_delete',
            ],

            //Leave Assign
            'leave_assign' => [
                'read'           => 'leave_assign_read',
                'create'         => 'leave_assign_create',
                'update'         => 'leave_assign_update',
                'delete'         => 'leave_assign_delete',
            ],

            //leave
            'leave' => [
                'read'           => 'leave_read',
                'create'         => 'leave_create',
                'delete'         => 'leave_delete',
                'approval'       => 'leave_approval',
                'reports'        => 'leave_reports',
            ],
            //duty-schedule
            'duty_schedule' => [
                'read'           => 'duty_schedule_read',
                'create'         => 'duty_schedule_create',
                'delete'         => 'duty_schedule_update',
                'approval'       => 'duty_schedule_delete'
            ],
            //weekend
            'duty_schedule' => [
                'read'           => 'weekend_read',
                'create'         => 'weekend_update',
            ],

            //attendance
            'attendance' => [
                'read'           => 'attendance_read',
                'create'         => 'attendance_create',
                'update'         => 'attendance_update',
                'delete'         => 'attendance_delete',
                'reports'        => 'attendance_reports'
            ],
            //holiday
            'holiday' => [
                'read'           => 'holiday_read',
                'create'         => 'holiday_create',
                'update'         => 'holiday_update',
                'delete'         => 'holiday_delete',
            ],
            //analytics
            'analytics' => [
                'read'           => 'analytics_read',
            ],
            'receiver' => [
                'read'           => 'receiver_read',
            ],
            'dispute' => [
                'read'           => 'dispute_read',
                'update'         => 'dispute_update',
            ],
            'platform_ledger' => [
                'read'           => 'platform_ledger_read',
            ],
        ];

        foreach ($attributes as $key => $value) {
            $permission = new Permission();
            $permission->attribute = $key;
            $permission->keywords  = $value;
            $permission->save();
        }
    }
}

