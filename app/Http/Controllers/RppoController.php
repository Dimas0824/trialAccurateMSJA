<?php

namespace App\Http\Controllers;

class RppoController extends Controller
{
    public function index(array $data)
    {
        return app(PurchaseReportController::class)->page($data, 'rppo');
    }
}
