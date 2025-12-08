@extends('template_mahasiswa.layout')
@section('title', 'Contact')
@section('css')
<style>
    .alert-glass {
        background: rgba(255, 255, 255, 0.1);
        /* transparan */
        backdrop-filter: blur(10px);
        /* efek blur */
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #fff;
        /* teks putih, bisa disesuaikan tema */
        padding: 15px 20px;
        border-radius: 15px;
        margin-bottom: 20px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }

    .alert-glass {
        color: #aaf39cff;
        /* untuk testing */
        background: rgba(0, 255, 0, 0.07);
    }



    .alert-glass.alert-danger {
        border-color: rgba(255, 0, 0, 0.3);
    }
</style>
@endsection
@section('konten')

<section class="card-hero glass-card contact-section">
    <h2 class="section-title">Hubungi TechNote App</h2>
    <p class="section-subtitle">Silakan kirim pertanyaan, saran, atau laporan kendala melalui formulir berikut.</p>

    <form action="{{ route('mahasiswa.contact.submit') }}" method="POST" class="contact-form">
        @csrf
        @if(session('message'))
        <div class="alert-glass {{ session('alert') ?? 'success' }}">
            {{ session('message') }}
        </div>


        @endif

        @if($errors->any())
        <div class="alert-glass alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif


        <div class="form-group">
            <label>Nama</label>
            <input type="text" name="nama" class="form-control" placeholder="Masukkan nama" required>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" placeholder="Masukkan email" required>
        </div>

        <div class="form-group">
            <label>Pesan</label>
            <textarea name="pesan" rows="5" class="form-control" placeholder="Tulis pesan Anda" required></textarea>
        </div>

        <button class="btn send-btn">Kirim Pesan</button>
    </form>
</section>

@endsection