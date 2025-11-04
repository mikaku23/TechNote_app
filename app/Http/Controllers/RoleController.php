<?php

namespace App\Http\Controllers;

use App\Models\role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        $semuaRole = ['admin', 'dosen', 'mahasiswa', 'teknisi'];

        // Cek apakah semua role sudah ada
        $roleSudahPenuh = count($roles) >= count($semuaRole);

        return view('admin.role.index', [
            'menu' => 'role',
            'title' => 'Data Role',
            'datarole' => $roles,
            'roleSudahPenuh' => $roleSudahPenuh,
        ]);
    }


    public function create()
    {
        // Ambil semua status yang sudah ada di tabel roles
        $rolesTerdaftar = role::pluck('status')->toArray();

        // Daftar semua role yang tersedia
        $semuaRole = ['admin', 'dosen', 'mahasiswa', 'teknisi'];

        // Filter role yang belum ada
        $roleTersedia = array_diff($semuaRole, $rolesTerdaftar);

        return view('admin.role.create', [
            'menu' => 'role',
            'title' => 'Tambah Role',
            'roleTersedia' => $roleTersedia,
        ]);
    }


    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'status' => 'required|string|unique:roles,status',
        ], [
            'status.required' => 'Status role harus diisi.',
            'status.unique' => 'Status role sudah ada.',
        ]);

        $role = new role();
        $role->status = $validated['status'];
        $role->save();

        return redirect()->route('role.index')->with('success', 'Role berhasil ditambahkan.');
    }
}
