const slides = document.querySelectorAll(".slide");
const nextBtn = document.querySelector(".next");
const prevBtn = document.querySelector(".prev");

let index = 0;

function showSlide(i) {
  slides.forEach((slide) => slide.classList.remove("active"));
  slides[i].classList.add("active");
}

// AUTO SLIDE
setInterval(() => {
  index = (index + 1) % slides.length;
  showSlide(index);
}, 4000);

// NEXT BUTTON
nextBtn.addEventListener("click", () => {
  index = (index + 1) % slides.length;
  showSlide(index);
});

// PREV BUTTON
prevBtn.addEventListener("click", () => {
  index = (index - 1 + slides.length) % slides.length;
  showSlide(index);
});
