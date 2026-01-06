<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        // Fetch only confirmed payments (LUNAS)
        $notifications = Tagihan::with('mahasiswa')
            ->where('status', 'Lunas')
            ->orderBy('updated_at', 'desc')
            ->limit(50)
            ->get();
            
        // Mark notifications as "read" by storing the ID of the latest Lunas transaction
        // This is more robust than timestamps which can have timezone issues
        $latestLunas = Tagihan::where('status', 'Lunas')->latest('updated_at')->first();
        
        if ($latestLunas) {
            session(['read_notification_id' => $latestLunas->id]);
        }

        return view('admin.notifications.index', compact('notifications'));
    }
}
