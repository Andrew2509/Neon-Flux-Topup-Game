<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    public function index()
    {
        $deposits = Deposit::with('user')->latest()->paginate(10);
        return view('admin.deposits', compact('deposits'));
    }
}
