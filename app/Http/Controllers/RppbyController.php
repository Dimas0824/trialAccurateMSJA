<?php

namespace App\Http\Controllers;

class RppbyController extends Controller
{
    public function index(array $data)
    {
        return app(PurchaseReportController::class)->page($data, 'rppby');
    }
}
