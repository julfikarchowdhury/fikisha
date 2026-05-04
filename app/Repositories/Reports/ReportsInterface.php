<?php

namespace App\Repositories\Reports;

interface  ReportsInterface
{
    public function parcelReports($request);
    public function merchantParcelReports($request);
    public function parcelWiseProfitReports($request);
    public function salaryReports($request);
    public function salaryReportsPrint($request);
    public function MerchantExport($request);
    public function parcelTotalSummeryReports($request);
    public function commissionDeliveryman($request);
    public function cashReceivedDeliveryman($request);
    public function incomeExpense($type);
    public function deliverymanreportParcels($request);
    public function deliverymanStatement($request);
}
