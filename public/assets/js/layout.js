// pastikan loader terhapus setelah semua asset selesai load
window.addEventListener("load", function () {
    const loader = document.getElementById("loaderWrapper");
    if (!loader) return;

    // tampilkan minimal 300ms agar animasi terlihat singkat tapi tidak blinking
    const MIN_SHOW = 300;
    const shownAt = performance.now();

    // berikan sedikit delay agar terlihat halus
    const hide = () => {
        loader.style.transition = "opacity 0.25s ease, filter 0.25s ease";
        loader.style.filter = "blur(2px)"; // efek kabur ringan saat memudar
        loader.style.opacity = "0"; // tetap mempertahankan fade-out
        setTimeout(() => {
            loader.style.display = "none";
        }, 260);
    };

    const elapsed = performance.now() - shownAt;
    const remaining = Math.max(0, MIN_SHOW - elapsed);
    setTimeout(hide, remaining);
});
