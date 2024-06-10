var SwiperChooseUs = new Swiper('.swiper-choose-us', {
  slidesPerView: 1,
  spaceBetween: 30,
  loop: true,
  grabCursor: true,
});

document.getElementById('nextSlideChooseUs').addEventListener('click', function() {
  SwiperChooseUs.slideNext();
});
