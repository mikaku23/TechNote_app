document.addEventListener("DOMContentLoaded", function () {
    const canvas = document.getElementById("rankChart");
    if (!canvas) return;

    const ctx = canvas.getContext("2d");

    // ambil data dari attribute HTML
    const labels = JSON.parse(canvas.dataset.labels || "[]");
    const values = JSON.parse(canvas.dataset.values || "[]");
    const type = canvas.dataset.type || "mahasiswa";

    const chartData = {
        labels: labels.length ? labels : ["-"],
        datasets: [
            {
                label: type === "dosen" ? "Perbaikan" : "Penginstalan",
                data: values.length ? values : [0],
                borderWidth: 1,
            },
        ],
    };

    const config = {
        type: "bar",
        data: chartData,
        options: {
            indexAxis: "y",
            responsive: true,
            plugins: {
                legend: {
                    display: false,
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            const v = context.raw ?? 0;
                            return (
                                v +
                                (type === "dosen"
                                    ? " perbaikan"
                                    : " penginstalan")
                            );
                        },
                    },
                },
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                    },
                },
            },
        },
    };

    new Chart(ctx, config);
});
