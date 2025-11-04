document.addEventListener("DOMContentLoaded", function () {
    const pwd = document.getElementById("password");
    const toggle = document.getElementById("togglePassword");
    const form = document.querySelector(".needs-validation");

    if (toggle && pwd) {
        toggle.addEventListener("click", function () {
            const type =
                pwd.getAttribute("type") === "password" ? "text" : "password";
            pwd.setAttribute("type", type);
            const icon = this.querySelector("i");
            if (icon) {
                icon.classList.toggle("fa-eye");
                icon.classList.toggle("fa-eye-slash");
            }
        });
    }

    if (form) {
        const redirectUrl = form.dataset.redirect || "/";

        form.addEventListener("submit", function (e) {
            e.preventDefault();

            if (!form.checkValidity()) {
                e.stopPropagation();
                form.classList.add("was-validated");
                return;
            }

            // Konfirmasi edit
            Swal.fire({
                title: "Apakah Anda yakin?",
                text: "Apakah Anda yakin untuk mengedit data ini?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Batal",
                reverseButtons: true,
                background: "rgba(255, 255, 255, 0.75)",
                backdrop: "rgba(0, 0, 0, 0.55)",
                customClass: {
                    popup: "glass-popup",
                    confirmButton: "btn-confirm",
                    cancelButton: "btn-cancel",
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan loading dulu
                    Swal.fire({
                        title: "Menyimpan perubahan...",
                        html: `<div class="swal2-loading-icon"><i class="fa fa-spinner fa-spin fa-3x"></i></div>
                               <p style="margin-top:10px;">Mohon tunggu sebentar</p>`,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        background: "rgba(255, 255, 255, 0.8)",
                        backdrop: "rgba(0, 0, 0, 0.55)",
                        customClass: { popup: "glass-popup" },
                    });

                    // Kirim form via fetch
                    fetch(form.action, {
                        method: "POST",
                        body: new FormData(form),
                        headers: { Accept: "application/json" },
                    })
                        .then((res) => {
                            if (!res.ok)
                                throw new Error("Gagal memperbarui data");
                            return res.json();
                        })
                        .then(() => {
                            // Ganti popup loading dengan animasi centang
                            Swal.fire({
                                title: "Berhasil!",
                                html: `
                                <div class="success-animation">
                                    <div class="checkmark-circle">
                                        <div class="checkmark draw"></div>
                                    </div>
                                    <p class="text-success-fade">Data pengguna berhasil diperbarui.</p>
                                </div>
                            `,
                                showConfirmButton: false, // hilangkan tombol
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                background: "rgba(255, 255, 255, 0.85)",
                                backdrop: "rgba(0,0,0,0.55)",
                                customClass: { popup: "glass-popup" },
                            });

                            // Redirect otomatis saat animasi centang selesai (0.7s)
                            setTimeout(() => {
                                window.location.href = redirectUrl;
                            }, 300);
                        })
                        .catch(() => {
                            Swal.fire({
                                icon: "error",
                                title: "Gagal!",
                                text: "Terjadi kesalahan saat memperbarui data.",
                                confirmButtonText: "Tutup",
                                background: "rgba(255, 255, 255, 0.8)",
                                backdrop: "rgba(0,0,0,0.55)",
                                customClass: { popup: "glass-popup" },
                            });
                        });
                }
            });
        });
    }
});
