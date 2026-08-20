const landingHeader = document.querySelector(".landing-header");
const pageTopButton = document.querySelector(".page-top-button");

let previousScrollPosition = window.scrollY;

function updateLandingNavigation() {
    const currentScrollPosition = window.scrollY;
    const scrollingDown = currentScrollPosition > previousScrollPosition;
    const passedHeroNavigation = currentScrollPosition > 160;

    landingHeader?.classList.toggle(
        "is-hidden",
        passedHeroNavigation && scrollingDown,
    );

    pageTopButton?.classList.toggle("is-visible", currentScrollPosition > 520);

    previousScrollPosition = Math.max(currentScrollPosition, 0);
}

window.addEventListener("scroll", updateLandingNavigation, { passive: true });

pageTopButton?.addEventListener("click", () => {
    window.scrollTo({ top: 0, behavior: "smooth" });
});
