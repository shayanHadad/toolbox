(function () {
    const root = document.getElementById("toolbox-slideshow");
    if (!root) return;

    const slides = [...root.querySelectorAll(".slide")];
    const dotsWrap = root.querySelector(".slide-dots");
    let index = 0;
    let timer;

    slides.forEach((_, i) => {
        const dot = document.createElement("button");
        dot.type = "button";
        dot.className = "slide-dot" + (i === 0 ? " is-active" : "");
        dot.setAttribute("aria-label", "برو به اسلاید " + (i + 1));
        dot.addEventListener("click", () => go(i));
        dotsWrap.appendChild(dot);
    });

    const dots = [...dotsWrap.querySelectorAll(".slide-dot")];

    function go(i) {
        slides[index].classList.remove("is-active");
        dots[index].classList.remove("is-active");

        index = (i + slides.length) % slides.length;

        slides[index].classList.add("is-active");
        dots[index].classList.add("is-active");

        restart();
    }

    function restart() {
        clearInterval(timer);
        timer = setInterval(() => go(index + 1), 5000);
    }

    root.querySelector('[data-dir="next"]').addEventListener("click", () =>
        go(index + 1),
    );
    root.querySelector('[data-dir="prev"]').addEventListener("click", () =>
        go(index - 1),
    );

    root.addEventListener("mouseenter", () => clearInterval(timer));
    root.addEventListener("mouseleave", restart);

    restart();
})();
