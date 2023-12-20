import Lenis from "@studio-freight/lenis";
import { gsap } from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";
gsap.registerPlugin(ScrollTrigger);

//active le js seulement en front-end !
if (window.location.href.indexOf("/wp") === -1) {
  const lenis = new Lenis({});

  function raf(time) {
    lenis.raf(time);
    requestAnimationFrame(raf);
  }
  requestAnimationFrame(raf);

  const tl = gsap.timeline({
    scrollTrigger: {
      trigger: ".main-hero",
      start: "top top",
      end: "bottom center",
      scrub: true,
    },
  });

  tl.fromTo(".main-hero .lenis-title", { y: "-25%" }, { y: "-45%" }, 0).fromTo(
    ".main-hero .lenis-title",
    { y: "-25%" },
    { y: "-45%" },
    0
  );

  //MAIN POPUP PARAMS
  $(".open-popup-entire-screen").click(function () {
    if (window.innerWidth > 1024) {
      lenis.stop();
    }
  });

  $(".close-popup-entire-screen").click(function () {
    if (window.innerWidth > 1024) {
      lenis.start();
    }
  });

    // Gestion de l'événement 'wheel' pour les écrans de plus de 768px
    if (window.innerWidth > 768) {
      $(".scroll-content").on("wheel", function(event) {
          var scrollContent = $(this);
          var currentScrollTop = scrollContent.scrollTop();
          scrollContent.scrollTop(currentScrollTop + event.originalEvent.deltaY);
      });
  }

  // Gestion de l'événement 'touchmove' pour les écrans de moins de 768px
  else {
      var startTouchY;

      $(".scroll-content").on("touchstart", function(event) {
          startTouchY = event.originalEvent.touches[0].clientY;
      });

      $(".scroll-content").on("touchmove", function(event) {
          var touchY = event.originalEvent.touches[0].clientY;
          var touchMoveDelta = startTouchY - touchY;
          var scrollContent = $(this);
          var currentScrollTop = scrollContent.scrollTop();

          scrollContent.scrollTop(currentScrollTop + touchMoveDelta);
          startTouchY = touchY; // Réinitialiser la position de départ du toucher
      });
  }
}
