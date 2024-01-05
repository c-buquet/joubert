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
    SwiperFeaturedWork.slideNext();
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
