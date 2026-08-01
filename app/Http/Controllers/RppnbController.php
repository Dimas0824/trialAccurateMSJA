<?php

namespace App\Http\Controllers;

class RppnbController extends Controller
{
    public function index(array $data)
    {
        return app(PurchaseReportController::class)->page($data, 'rppnb');
    }
}
