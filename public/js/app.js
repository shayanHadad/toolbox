(function () {
    const btn = document.getElementById("back-to-top");
    if (!btn) return;

    const toggle = () => {
        btn.classList.toggle("is-visible", window.scrollY > 400);
    };

    window.addEventListener("scroll", toggle, {
        passive: true,
    });

    toggle();

    btn.addEventListener("click", () => {
        window.scrollTo({
            top: 0,
            behavior: "smooth",
        });
    });
})();
