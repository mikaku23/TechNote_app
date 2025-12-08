@php
$foto = Auth::user()->foto && file_exists(public_path('foto/' . Auth::user()->foto))
? asset('foto/' . Auth::user()->foto)
: asset('assets/images/default.png');
@endphp

<div class="profile-wrapper">
    <img src="{{ $foto }}" alt="Foto" class="profile-image">

    <div class="profile-info-item">
        <label>Nama</label>
        <div class="value">{{ $user->nama }}</div>
    </div>

    <div class="profile-info-item">
        <label>Username</label>
        <div class="value">{{ $user->username }}</div>
    </div>

    <div class="profile-info-item">
        <label>Role</label>
        <div class="value">{{ $user->role->status ?? '-' }}</div>
    </div>

    <div class="profile-info-item">
        <label>NIM / NIP</label>
        <div class="value">{{ $user->nim ?? $user->nip }}</div>
    </div>
</div>