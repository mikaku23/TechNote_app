document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector(".needs-validation");
    if (!form) return;

    const redirectUrl = form.dataset.redirect; // ambil url redirect dari data-attribute
    const SUPPRESS_KEY = "suppress_server_error_popup_v1";

    form.addEventListener("submit", function (e) {
        e.preventDefault();

        if (!form.checkValidity()) {
            e.stopPropagation();
            form.classList.add("was-validated");
            return;
        }

        // Popup loading saat menyimpan
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
                // Popup centang sukses
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

                // Redirect setelah animasi centang selesai
                setTimeout(() => {
                    window.location.href = redirectUrl;
                }, 800);
            })
            .catch((err) => {
                // Ambil pesan error validasi Laravel
                let message = "Terjadi kesalahan saat menambahkan data.";
                if (err && err.errors) {
                    message =
                        "<ul style='text-align:left;margin:0;padding-left:18px;'>";
                    for (let key in err.errors) {
                        err.errors[key].forEach((msg) => {
                            message += `<li>${msg}</li>`;
                        });
                    }
                    message += "</ul>";
                }

                // Popup gagal (tanpa reload otomatis)
                Swal.fire({
                    icon: "error",
                    title: "Gagal!",
                    html: message,
                    confirmButtonText: "Tutup",
                    background: "rgba(255,255,255,0.85)",
                    backdrop: "rgba(0,0,0,0.55)",
                    customClass: { popup: "glass-popup" },
                }).then((result) => {
                    if (result.isConfirmed) {
                        // set flag supaya server-side popup tidak muncul lagi setelah reload
                        try {
                            sessionStorage.setItem(SUPPRESS_KEY, "1");
                        } catch (e) {}
                        // submit normal agar Laravel mengembalikan old() dan $errors
                        form.submit();
                    }
                });
            });
    });

    // Jika ada error validasi dari Laravel (server-side)
    const hasErrors = document.querySelector(".alert.alert-danger");
    if (hasErrors) {
        // jika flag ada, berarti error sudah ditampilkan via Ajax popup sebelum reload:
        // jangan tampilkan ulang. Hapus flag.
        const suppressed = (() => {
            try {
                return sessionStorage.getItem(SUPPRESS_KEY);
            } catch (e) {
                return null;
            }
        })();

        if (suppressed) {
            try {
                sessionStorage.removeItem(SUPPRESS_KEY);
            } catch (e) {}
            // Jangan munculkan server-side popup karena sudah tampil sebelum reload
        } else {
            // Tampilkan popup server-side (kasus ketika user langsung reload atau langsung submit non-AJAX)
            const errorList = hasErrors.innerHTML;
            Swal.fire({
                icon: "error",
                title: "Terjadi Kesalahan!",
                html: errorList,
                confirmButtonText: "Tutup",
                confirmButtonColor: "#d33",
                background: "rgba(255,255,255,0.95)",
                backdrop: "rgba(0,0,0,0.55)",
                customClass: { popup: "glass-popup" },
            });
        }
    }

    // Jika ada pesan sukses dari session
    const successAlert = document.querySelector("[data-success]");
    if (successAlert) {
        Swal.fire({
            title: "Berhasil!",
            html: `
                <div class="success-animation">
                    <div class="checkmark-circle">
                        <div class="checkmark draw"></div>
                    </div>
                    <p class="text-success-fade">${successAlert.dataset.success}</p>
                </div>`,
            showConfirmButton: false,
            allowOutsideClick: false,
            allowEscapeKey: false,
            background: "rgba(255,255,255,0.85)",
            backdrop: "rgba(0,0,0,0.55)",
            customClass: { popup: "glass-popup" },
        });

        setTimeout(() => {
            window.location.href = redirectUrl;
        }, 800);
    }

    // Beri tanda valid otomatis untuk field yang punya old() value
    form.querySelectorAll(".form-control").forEach((input) => {
        if (input.value.trim() !== "") {
            input.classList.add("is-valid");
        }
    });
});
