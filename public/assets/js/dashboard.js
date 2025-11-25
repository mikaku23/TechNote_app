document.addEventListener("DOMContentLoaded", function () {
    const el = document.getElementById("dashboardData");
    if (!el) {
        console.error("dashboardData tidak ditemukan");
        return;
    }

    const dd = {
        labelHari: JSON.parse(el.dataset.labelHari || "[]"),
        dataInstalasiHari: JSON.parse(el.dataset.instalasiHari || "[]"),
        dataPerbaikanHari: JSON.parse(el.dataset.perbaikanHari || "[]"),

        labelTanggal: JSON.parse(el.dataset.labelTanggal || "[]"),
        dataInstalasiTanggal: JSON.parse(el.dataset.instalasiTanggal || "[]"),
        dataPerbaikanTanggal: JSON.parse(el.dataset.perbaikanTanggal || "[]"),

        labelBulan: JSON.parse(el.dataset.labelBulan || "[]"),
        dataInstalasiBulan: JSON.parse(el.dataset.instalasiBulan || "[]"),
        dataPerbaikanBulan: JSON.parse(el.dataset.perbaikanBulan || "[]"),

        labelTahun: JSON.parse(el.dataset.labelTahun || "[]"),
        dataSemuaTahun: JSON.parse(el.dataset.semuaTahun || "{}"),
    };

    const menu = document.querySelector("#dropdownFilter");
    const dropdownToggle = document.querySelector("#dropdownMenu");
    const chartEl = document.querySelector("#chart-dashboard");

    function normalize(series, categories) {
        const len = categories.length;
        if (!Array.isArray(series)) series = [];
        return [...series, ...Array(len - series.length).fill(0)].slice(0, len);
    }

    const datasets = {
        week: {
            categories: dd.labelHari,
            instalasi: dd.dataInstalasiHari,
            perbaikan: dd.dataPerbaikanHari,
        },
        month: {
            categories: dd.labelTanggal,
            instalasi: dd.dataInstalasiTanggal,
            perbaikan: dd.dataPerbaikanTanggal,
        },
        year: {
            categories: dd.labelBulan,
            instalasi: dd.dataInstalasiBulan,
            perbaikan: dd.dataPerbaikanBulan,
        },
    };

    if (dd.dataSemuaTahun) {
        Object.keys(dd.dataSemuaTahun).forEach(function (yr) {
            datasets["year-" + yr] = {
                categories: dd.labelBulan,
                instalasi: dd.dataSemuaTahun[yr].instalasi,
                perbaikan: dd.dataSemuaTahun[yr].perbaikan,
            };
        });
    }

    let chart = null;
    function renderApex(categories, instalasi, perbaikan) {
        if (chart) chart.destroy();

        chart = new ApexCharts(chartEl, {
            series: [
                { name: "Instalasi", data: instalasi },
                { name: "Perbaikan", data: perbaikan },
            ],
            chart: { type: "line", height: 260, toolbar: { show: false } },
            stroke: { curve: "smooth", width: 3 },
            markers: { size: 4 },
            xaxis: { categories: categories },
            legend: { position: "top" },
        });
        chart.render();
    }

    function safeRender(key) {
        const ds = datasets[key] || datasets.month;
        const categories = ds.categories.length ? ds.categories : ["-"];
        renderApex(
            categories,
            normalize(ds.instalasi, categories),
            normalize(ds.perbaikan, categories)
        );
    }

    safeRender("month");

    if (menu) {
        dd.labelTahun.forEach(function (yr) {
            const li = document.createElement("li");
            li.innerHTML = `<a class="dropdown-item" href="#" data-period="year-${yr}">${yr}</a>`;
            menu.appendChild(li);
        });

        menu.addEventListener("click", function (e) {
            const a = e.target.closest("a[data-period]");
            if (!a) return;
            safeRender(a.getAttribute("data-period"));
            dropdownToggle.textContent = a.textContent.trim();
        });
    }

    console.log("Dashboard data loaded:", dd);
});
