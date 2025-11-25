document.addEventListener("DOMContentLoaded", function () {
    // === TOGGLE PASSWORD (BISA UNTUK SEMUA HALAMAN) ===
    const toggleButtons = document.querySelectorAll("[data-toggle='password']");

    toggleButtons.forEach((btn) => {
        btn.addEventListener("click", function () {
            const inputId = this.dataset.target; // contoh: data-target="password"
            const pwd = document.getElementById(inputId);

            if (pwd) {
                const type =
                    pwd.getAttribute("type") === "password"
                        ? "text"
                        : "password";
                pwd.setAttribute("type", type);

                const icon = this.querySelector("i");
                if (icon) {
                    icon.classList.toggle("fa-eye");
                    icon.classList.toggle("fa-eye-slash");
                }
            }
        });
    });

    // === VALIDASI DAN KONFIRMASI FORM (UNTUK SEMUA FORM DENGAN KELAS .needs-validation) ===
    const forms = document.querySelectorAll(".needs-validation");

    forms.forEach((form) => {
        const redirectUrl = form.dataset.redirect || window.location.href;

        form.addEventListener("submit", function (e) {
            e.preventDefault();

            if (!form.checkValidity()) {
                e.stopPropagation();
                form.classList.add("was-validated");
                return;
            }

            Swal.fire({
                title: "Apakah Anda yakin?",
                text: "Apakah Anda yakin untuk menyimpan perubahan ini?",
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
                    // Loading
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

                    // Kirim data
                    fetch(form.action, {
                        method: "POST",
                        body: (() => {
                            const data = new FormData(form);
                            const methodInput = form.querySelector(
                                'input[name="_method"]'
                            );
                            if (methodInput)
                                data.append("_method", methodInput.value);
                            return data;
                        })(),
                        headers: { Accept: "application/json" },
                    })
                        .then((res) => {
                            if (!res.ok)
                                throw new Error("Gagal menyimpan data");
                            return res.json();
                        })
                        .then(() => {
                            Swal.fire({
                                title: "Berhasil!",
                                html: `
                                <div class="success-animation">
                                    <div class="checkmark-circle">
                                        <div class="checkmark draw"></div>
                                    </div>
                                    <p class="text-success-fade">Data berhasil disimpan.</p>
                                </div>
                            `,
                                showConfirmButton: false,
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                background: "rgba(255, 255, 255, 0.85)",
                                backdrop: "rgba(0,0,0,0.55)",
                                customClass: { popup: "glass-popup" },
                            });

                            setTimeout(() => {
                                window.location.href = redirectUrl;
                            }, 300);
                        })
                        .catch(() => {
                            Swal.fire({
                                icon: "error",
                                title: "Gagal!",
                                text: "Terjadi kesalahan saat menyimpan data.",
                                confirmButtonText: "Tutup",
                                background: "rgba(255, 255, 255, 0.8)",
                                backdrop: "rgba(0,0,0,0.55)",
                                customClass: { popup: "glass-popup" },
                            });
                        });
                }
            });
        });
    });
});
