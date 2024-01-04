import Lenis from "@studio-freight/lenis";
import { gsap } from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

const iOS = () => {
  if (typeof window === `undefined` || typeof navigator === `undefined`) return false;

  return /iPhone|iPad|iPod/i.test(navigator.userAgent || navigator.vendor || (window.opera && opera.toString() === `[object Opera]`));
};
console.log(iOS())

if (!iOS()) {
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

    $("#open-popup-feat-work").click(function () {
      lenis.stop();
    });

    $("#close-popup-feat-work").click(function () {
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

  $("#open-popup-feat-work").click(function () {
    $("body").toggleClass("overflow-hidden");
  });

  $("#close-popup-feat-work").click(function () {
    $("body").toggleClass("overflow-hidden");
  });
}
