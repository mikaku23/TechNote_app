<?php

namespace App\Http\Controllers;

use App\Models\penginstalan;
use App\Models\perbaikan;
use App\Models\rekap;
use App\Models\software;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboardAdmin()
    {
        $jumlahUser = User::count(); // Menghitung jumlah User
        $jumlahsoftware = software::count(); // Menghitung jumlah software
        $jumlahrekap = rekap::count(); // Menghitung jumlah rekap
        $jumlahpenginstalan = penginstalan::count(); // Menghitung jumlah penginstalan
        $jumlahperbaikan = perbaikan::count(); // Menghitung jumlah perbaikan

        return view('admin.index', [
            'menu' => 'dashboard',
            'jumlahUser' => $jumlahUser,
            'jumlahsoftware' => $jumlahsoftware,
            'jumlahrekap' => $jumlahrekap,
            'jumlahpenginstalan' => $jumlahpenginstalan,
            'jumlahperbaikan' => $jumlahperbaikan
        ]);
    }
}
