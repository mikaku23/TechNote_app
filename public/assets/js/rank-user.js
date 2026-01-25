// rank-user.js — improved animation flow (no overlap, smooth)
(function () {
    const podium = document.getElementById("myPodium");
    if (!podium) return;

    const myRankNow = parseInt(podium.dataset.rank || 0);
    const progress = parseInt(podium.dataset.progress || 0);

    if (!myRankNow) return;

    const podiumNowEl = document.getElementById("podiumNow");
    const podiumOldEl = document.getElementById("podiumOld");
    const progressBar = podium.querySelector(".progress-bar");

    // animate progress
    if (progressBar) {
        progressBar.style.width = "0%";
        setTimeout(() => {
            progressBar.style.width = progress + "%";
        }, 180);
    }

    if (podiumNowEl) podiumNowEl.textContent = "#" + myRankNow;

    const storageKey = "my_prev_rank";
    const prev = localStorage.getItem(storageKey);
    const prevRank = prev !== null ? parseInt(prev, 10) : null;

    function cleanup() {
        if (podiumOldEl) {
            podiumOldEl.classList.remove("fly-up-old", "fly-down-old");
            podiumOldEl.style.opacity = 0;
            podiumOldEl.textContent = "";
        }
        if (podiumNowEl) {
            podiumNowEl.classList.remove("reveal-now", "pulse-now");
            podiumNowEl.style.opacity = 1;
        }
    }

    if (prevRank === null) {
        if (podiumNowEl) {
            podiumNowEl.style.opacity = 0;
            void podiumNowEl.offsetWidth;
            podiumNowEl.classList.add("reveal-now");
        }
        localStorage.setItem(storageKey, myRankNow);
        setTimeout(() => {
            if (podiumNowEl) podiumNowEl.classList.remove("reveal-now");
            if (podiumNowEl) podiumNowEl.style.opacity = 1;
        }, 700);
        return;
    }

    if (prevRank === myRankNow) {
        if (podiumNowEl) {
            podiumNowEl.classList.add("pulse-now");
            setTimeout(() => podiumNowEl.classList.remove("pulse-now"), 700);
        }
        localStorage.setItem(storageKey, myRankNow);
        return;
    }

    if (!podiumOldEl || !podiumNowEl) {
        localStorage.setItem(storageKey, myRankNow);
        return;
    }

    // hide new number while animating old
    podiumNowEl.style.opacity = 0;

    podiumOldEl.textContent = "#" + prevRank;
    podiumOldEl.style.opacity = 1;

    const improved = myRankNow < prevRank;

    if (improved) {
        podiumOldEl.classList.add("fly-up-old");
        setTimeout(() => podiumNowEl.classList.add("reveal-now"), 360);
    } else {
        podiumOldEl.classList.add("fly-down-old");
        setTimeout(() => podiumNowEl.classList.add("reveal-now"), 360);
    }

    setTimeout(() => {
        cleanup();
        podiumNowEl.textContent = "#" + myRankNow;
        localStorage.setItem(storageKey, myRankNow);
    }, 1100);
})();
