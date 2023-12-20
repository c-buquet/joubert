var SwiperOurSolution = new Swiper('.swiper-our-solution', {
  slidesPerView: 1,
  spaceBetween: 30,
  loop: true,
});

document.getElementById('nextSlideOurSolution').addEventListener('click', function() {
  SwiperOurSolution.slideNext();
});
