<?php  
/*
NB: This is not stand alone code and is intended to be used within "seriti/slim3-skeleton" framework
The code snippet below is for use within an existing src/routes.php file within this framework
copy the "/reserve" group into the existing "/admin" group within existing "src/routes.php" file 
*/

//*** BEGIN admin access ***
$app->group('/admin', function () {

    $this->group('/reserve', function () {
        $this->any('/agent', \App\Reserve\AgentController::class);
        $this->post('/ajax', \App\Reserve\Ajax::class);
        $this->any('/calendar', \App\Reserve\CalendarReportController::class);
        $this->any('/cash_type', \App\Reserve\CashTypeController::class);
        $this->any('/dashboard', \App\Reserve\DashboardController::class);
        $this->any('/item', \App\Reserve\ItemController::class);
        $this->any('/item_category', \App\Reserve\ItemCategoryController::class);
        $this->any('/location', \App\Reserve\LocationController::class);
        $this->any('/reserve', \App\Reserve\ReserveController::class);
        $this->any('/reserve_file', \App\Reserve\ReserveFileController::class);
        $this->any('/reserve_payment', \App\Reserve\ReservePaymentController::class);
        $this->any('/reserve_cash', \App\Reserve\ReserveCashController::class);
        $this->any('/reserve_item', \App\Reserve\ReserveItemController::class);
        $this->any('/reserve_transfer', \App\Reserve\ReserveTransferController::class);
        $this->any('/reserve_people', \App\Reserve\ReservePeopleController::class);
        $this->any('/reserve_status', \App\Reserve\ReserveStatusController::class);
        $this->any('/source', \App\Reserve\SourceController::class);
        $this->any('/service_operator', \App\Reserve\ServiceOperatorController::class);
        $this->any('/package', \App\Reserve\PackageController::class);
        $this->any('/package_file', \App\Reserve\PackageFileController::class);
        $this->any('/package_image', \App\Reserve\PackageImageController::class);
        $this->any('/payment_option', \App\Reserve\PaymentOptionController::class);
        $this->any('/payment_type', \App\Reserve\PaymentTypeController::class);
        $this->any('/setup_dashboard', \App\Reserve\SetupDashboardController::class);
        $this->any('/setup_default', \App\Reserve\DefaultSetupController::class);
        $this->get('/setup_data', \App\Reserve\SetupDataController::class);
        $this->any('/report', \App\Reserve\ReportController::class);
        $this->any('/task', \App\Reserve\TaskController::class);
        $this->any('/transfer_type', \App\Reserve\TransferTypeController::class);
        $this->any('/user_extend', \App\Reserve\UserExtendController::class);
    })->add(\App\Reserve\Config::class);

    
})->add(\App\User\ConfigAdmin::class);
//*** END admin access ***


