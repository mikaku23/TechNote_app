@extends('template_dosen.layout')
@section('title', 'About')
@section('css')
<style>
    .features-grid {
        overflow: hidden;
        /* batasi gerakan */
        position: relative;
    }

    .features-inner {
        display: flex;
        gap: 18px;
        animation: scroll-horizontal 30s linear infinite;
    }

    .feature-card {
        flex: 0 0 32%;
        /* tetap kotak / grid 3 kolom */
        min-width: 220px;
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.03);
        border-radius: 14px;
        padding: 18px;
        transition: transform .22s ease, box-shadow .22s ease;
    }

    .feature-card:hover {
        transform: translateY(-6px) scale(1.05);
        box-shadow: 0 18px 32px rgba(2, 6, 23, 0.45);
    }

    @keyframes scroll-horizontal {
        0% {
            transform: translateX(0);
        }

        100% {
            transform: translateX(-80%);
        }

        /* smooth loop */
    }
</style>
@endsection
@section('konten')

<section class="card-hero glass-card about-section">
    <h2 class="section-title">Tentang TechNote App</h2>
    <p class="section-subtitle">Platform pencatatan penginstalan software dan layanan teknisi di STMIK Triguna Dharma.</p>
    <div class="about-content" style="text-align: justify;">
        <p>
            TechNote App dirancang untuk memudahkan mahasiswadosen dalam melakukan pendataan penginstalan software,
            pemantauan status layanan teknisi, serta riwayat perbaikan perangkat yang dilakukan di laboratorium kampus.
            Dengan aplikasi ini, proses pencatatan menjadi lebih efisien, akurat, dan dapat diakses secara digital,
            menggantikan metode manual yang sebelumnya digunakan.
        </p>
        <p>
            Aplikasi ini dibangun dengan konsep modern UI, menggunakan pendekatan glassmorphism,
            sentuhan interaktif, dan tampilan yang sederhana namun elegan. Fitur-fitur seperti daftar penginstalan,
            update status perangkat, dan histori perbaikan dirancang agar intuitif bagi pengguna, baik mahasiswadosen,
            dosen, maupun staf teknisi.
        </p>
    </div>

    <div class="features-grid" >
        <div class="features-inner">
            <div class="feature-card">
                <h4>Pencatatan Mudah</h4>
                <p>Catat penginstalan software dan servis teknisi dengan cepat dan terstruktur.</p>
            </div>
            <div class="feature-card">
                <h4>Status Real-Time</h4>
                <p>Pantau status pengerjaan secara langsung melalui dashboard pengguna.</p>
            </div>
            <div class="feature-card">
                <h4>Riwayat Tersimpan</h4>
                <p>Simpan dan lihat kembali riwayat instalasi dan perbaikan perangkat kapan saja.</p>
            </div>
            <div class="feature-card">
                <h4>Pencatatan Mudah</h4>
                <p>Catat penginstalan software dan servis teknisi dengan cepat dan terstruktur.</p>
            </div>
            <div class="feature-card">
                <h4>Status Real-Time</h4>
                <p>Pantau status pengerjaan secara langsung melalui dashboard pengguna.</p>
            </div>
            <div class="feature-card">
                <h4>Riwayat Tersimpan</h4>
                <p>Simpan dan lihat kembali riwayat instalasi dan perbaikan perangkat kapan saja.</p>
            </div>
        </div>
    </div>

</section>

@endsection