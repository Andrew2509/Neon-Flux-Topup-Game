<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VisitorController extends Controller
{
    public function index()
    {
        $onlineCount = \App\Models\Visitor::online()->count();
        $todayUniqueCount = \App\Models\Visitor::today()->count();
        $totalRecordCount = \App\Models\Visitor::count();

        $visitors = \App\Models\Visitor::with('user')
            ->orderBy('last_active_at', 'desc')
            ->paginate(20);

        return view('admin.visitors', compact('onlineCount', 'todayUniqueCount', 'totalRecordCount', 'visitors'));
    }
}
