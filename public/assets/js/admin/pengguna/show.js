$(document).ready(function () {
    $(".show-btn").click(function () {
        var id = $(this).data("id");

        // Ambil segmen URL pertama (misalnya: /software, /barang, /pengguna)
        var baseUrl = window.location.pathname.split("/")[1];
        // Misal hasil: "software" atau "barang" atau "pengguna"

        // Buka modal
        $("#showModal").modal("show");

        // Tampilkan spinner dulu
        $("#modalContent").html(`
            <div class="spinner">
                <div></div><div></div><div></div>
            </div>
        `);

        // Delay sedikit biar spinner sempat muncul
        setTimeout(function () {
            $.get("/" + baseUrl + "/" + id, function (data) {
                $("#modalContent").html(data);
            });
        }, 400);
    });

    // Tutup modal
    $(".close-modal").click(function () {
        $(this).closest(".modal").modal("hide");
    });
});
