<?php

namespace App\Repositories\Dashboard;

interface DashboardInterface
{

    public function FromTo($request);
    public function Dates($request);
    public function incomeDate($request);
    public function expenseDate($request);

    public function bankTransaction($date, $request = null);
    public function income($date, $request = null);
    public function expense($date, $request = null);
    public function merchantIncome($date, $request = null);
    public function merchantExpense($date, $request = null);
    public function deliverymanIncome($date, $request = null);
    public function deliverymanExpense($date, $request = null);
    public function courierIncome($date, $request = null);
    public function courierExpense($date, $request = null);

    public function parcelPosition($request, $status, $date);
    public function parcelPositionDeliveryFailure($request, $date);
    public function recentAccounts($request, $date);
    public function salary($date);
    public function salaries($date);
    public function salaryGenerated($date);

    public function balanceDetails();
    public function availableParcels();

    public function analyticsFromTo($date);
    public function analytics($request);
    public function seven_days_order_summery();
    public function monthly_order_summery();

    public function merchant_seven_days_order_summery();
    public function merchant_monthly_order_summery();
}
