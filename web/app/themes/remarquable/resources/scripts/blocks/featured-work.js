import { gsap, TimelineLite } from "gsap";
import { CSSPlugin } from "gsap/CSSPlugin";

var SwiperFeaturedWork = new Swiper('.swiper-featured-work', {
  slidesPerView: 1,
  spaceBetween: 0,
  freeMode: true,
  grabCursor: true,
  speed: 100,
});

let buttons = document.querySelectorAll('[data-id="nextSlideFeaturedWork"]');

buttons.forEach(function(button) {
  button.addEventListener('click', function() {
    // Calculez l'index de la prochaine diapositive
    let nextSlideIndex = SwiperFeaturedWork.activeIndex + 1;

    // Assurez-vous de ne pas dépasser le nombre total de diapositives
    if (nextSlideIndex >= SwiperFeaturedWork.slides.length) {
      nextSlideIndex = 0; // ou vous pouvez choisir de ne pas boucler
    }

    // Transition vers la diapositive suivante avec un contrôle de vitesse personnalisé
    SwiperFeaturedWork.slideTo(nextSlideIndex, 700); // 500 est la durée en millisecondes
  });
});

gsap.registerPlugin(CSSPlugin);

var navigation = new TimelineLite({ paused: true, reversed: true });

$("#open-popup-feat-work").click(function () {
    // Réinitialiser la timeline à chaque ouverture de popup
    navigation.clear();

    var popupSelector = $("#popup-feat-work");
    var closeSelector = $("#close-popup-feat-work");
    var contentSelector = $("#content-popup-feat-work");

    // Réinitialiser manuellement l'état de contentSelector
    gsap.set(contentSelector, { opacity: 1, y: 0 });

    navigation
        .to(popupSelector, 0.5, { opacity: 1, display: 'block' })
        .to(closeSelector, 0.4, { display: "block", opacity: 1 }, "-=0.1")
        .from(contentSelector, 0.5, { opacity: 0, y: 30 });

    navigation.play();

});

$("#close-popup-feat-work").click(function () {
    var popupSelector = $("#popup-feat-work");

    // Inverser ou stopper l'animation spécifique à la popup
    if (popupSelector.length) {
        navigation.reverse();
    }
});
