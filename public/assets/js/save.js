document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector(".needs-validation");
    if (!form) return;

    const redirectUrl = form.dataset.redirect; // ambil url redirect dari data-attribute

    form.addEventListener("submit", function (e) {
        e.preventDefault();

        if (!form.checkValidity()) {
            e.stopPropagation();
            form.classList.add("was-validated");
            return;
        }

        // Loading popup
        Swal.fire({
            title: "Sedang memproses...",
            html: `<div class="swal2-loading-icon">
                        <i class="fa fa-spinner fa-spin fa-3x"></i>
                   </div>
                   <p style="margin-top:10px;">Mohon tunggu sebentar</p>`,
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            background: "rgba(255,255,255,0.85)",
            backdrop: "rgba(0,0,0,0.55)",
            customClass: { popup: "glass-popup" },
        });

        fetch(form.action, {
            method: "POST",
            body: new FormData(form),
            headers: { Accept: "application/json" },
        })
            .then(async (res) => {
                const data = await res.json().catch(() => ({}));
                if (!res.ok) throw data; // lempar object error dari controller
                return data;
            })
            .then(() => {
                // Centang sukses
                Swal.fire({
                    title: "Berhasil!",
                    html: `
                    <div class="success-animation">
                        <div class="checkmark-circle">
                            <div class="checkmark draw"></div>
                        </div>
                        <p class="text-success-fade">Data berhasil ditambahkan</p>
                    </div>`,
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    background: "rgba(255,255,255,0.85)",
                    backdrop: "rgba(0,0,0,0.55)",
                    customClass: { popup: "glass-popup" },
                });

                // Redirect setelah animasi centang selesai (0.5s)
                setTimeout(() => {
                    window.location.href = redirectUrl;
                }, 300);
            })
            .catch((err) => {
                // Ambil error validasi Laravel jika ada
                let message = "Terjadi kesalahan saat menambahkan data.";
                if (err.errors) {
                    message =
                        "<ul style='text-align:left;margin:0;padding-left:18px;'>";
                    for (let key in err.errors) {
                        err.errors[key].forEach((msg) => {
                            message += `<li>${msg}</li>`;
                        });
                    }
                    message += "</ul>";
                }

                Swal.fire({
                    icon: "error",
                    title: "Gagal!",
                    html: message,
                    confirmButtonText: "Tutup",
                    background: "rgba(255,255,255,0.85)",
                    backdrop: "rgba(0,0,0,0.55)",
                    customClass: { popup: "glass-popup" },
                });
            });
    });
});
