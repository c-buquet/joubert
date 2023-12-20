import Lenis from "@studio-freight/lenis";
import { gsap } from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";
gsap.registerPlugin(ScrollTrigger);

//active le js seulement en front-end !
if (window.location.href.indexOf("/wp") === -1) {
  const lenis = new Lenis({});

  function raf(time) {
    lenis.raf(time);
    ScrollTrigger.update();
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



  $(".scroll-content").on("wheel", function (event) {
    // Récupérer l'élément .scroll-content
    var scrollContent = $(this);

    // Récupérer la valeur actuelle de la position de défilement
    var currentScrollTop = scrollContent.scrollTop();

    // Définir la nouvelle position de défilement en fonction de la direction de la molette
    scrollContent.scrollTop(currentScrollTop + event.originalEvent.deltaY);
  });
}
