import Lenis from "@studio-freight/lenis";
import { gsap } from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

if (window.innerWidth > 768) {
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

    tl.fromTo(
      ".main-hero .lenis-title",
      { y: "-25%" },
      { y: "-45%" },
      0
    ).fromTo(".main-hero .lenis-title", { y: "-25%" }, { y: "-45%" }, 0);

    //MAIN POPUP PARAMS
    $(".open-popup-entire-screen").click(function () {
      lenis.stop();
    });

    $(".close-popup-entire-screen").click(function () {
      lenis.start();
    });

    $(".scroll-content").on("wheel", function (event) {
      // Récupérer l'élément .scroll-content
      var scrollContent = $(this);

      // Récupérer la valeur actuelle de la position de défilement
      var currentScrollTop = scrollContent.scrollTop();

      // Définir la nouvelle position de défilement en fonction de la direction de la molette
      scrollContent.scrollTop(currentScrollTop + event.originalEvent.deltaY);
    });
  }
} else {
  $(".open-popup-entire-screen").click(function () {
    $("body").toggleClass("overflow-hidden");
  });

  $(".close-popup-entire-screen").click(function () {
    $("body").toggleClass("overflow-hidden");
  });
}
