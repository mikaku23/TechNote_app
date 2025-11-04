function konfirmasiHapus(id) {
    Swal.fire({
        title: "Apakah Anda yakin?",
        text: "Data pengguna ini akan dihapus permanen!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya",
        cancelButtonText: "Batal",
        reverseButtons: true,
        background: "rgba(255, 255, 255, 0.8)",
        backdrop: "rgba(0,0,0,0.5)",
        customClass: {
            popup: "glass-popup",
            confirmButton: "btn-confirm",
            cancelButton: "btn-cancel",
        },
    }).then((result) => {
        if (result.isConfirmed) {
            // Loading popup
            Swal.fire({
                title: "Menghapus data...",
                html: `
                    <div class="swal2-loading-icon">
                        <i class="fa fa-spinner fa-spin fa-3x"></i>
                    </div>
                    <p style="margin-top:10px;">Mohon tunggu sebentar</p>
                `,
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                background: "rgba(255, 255, 255, 0.8)",
                backdrop: "rgba(0,0,0,0.5)",
                customClass: { popup: "glass-popup" },
            });

            // Kirim request hapus
            const form = document.getElementById("formHapus" + id);
            const action = form.getAttribute("action");
            const token = form.querySelector('input[name="_token"]').value;
            const method = form.querySelector('input[name="_method"]').value;

            fetch(action, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": token,
                    Accept: "application/json",
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({ _method: method }),
            })
                .then((response) => {
                    if (!response.ok) throw new Error("Gagal menghapus data");
                    return response.json().catch(() => ({}));
                })
                .then(() => {
                    // Tampilkan animasi centang
                    Swal.fire({
                        title: "Berhasil!",
                        html: `
                        <div class="success-animation">
                            <div class="checkmark-circle">
                                <div class="background"></div>
                                <div class="checkmark draw"></div>
                            </div>
                            <p class="text-success-fade">Data pengguna telah dihapus.</p>
                        </div>
                    `,
                        showConfirmButton: false, // Tidak perlu tombol
                        allowOutsideClick: false,
                        background: "rgba(255, 255, 255, 0.85)",
                        backdrop: "rgba(0,0,0,0.45)",
                        customClass: { popup: "glass-popup" },
                    });

                    // Tutup / refresh langsung setelah animasi centang selesai (0.7s)
                    setTimeout(() => {
                        location.reload();
                    }, 300);
                })
                .catch(() => {
                    Swal.fire({
                        icon: "error",
                        title: "Gagal!",
                        text: "Terjadi kesalahan saat menghapus data.",
                        confirmButtonText: "Tutup",
                        background: "rgba(255, 255, 255, 0.8)",
                        backdrop: "rgba(0,0,0,0.5)",
                        customClass: { popup: "glass-popup" },
                    });
                });
        }
    });
}
