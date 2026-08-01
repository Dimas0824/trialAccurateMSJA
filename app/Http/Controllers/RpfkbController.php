<?php

namespace App\Http\Controllers;

class RpfkbController extends Controller
{
    public function index(array $data)
    {
        return app(PurchaseReportController::class)->page($data, 'rpfkb');
    }
}
