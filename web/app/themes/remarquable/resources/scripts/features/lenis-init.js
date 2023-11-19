import Lenis from '@studio-freight/lenis'

const lenis = new Lenis({});

function raf(time) {
  lenis.raf(time)
  requestAnimationFrame(raf)
}
requestAnimationFrame(raf)

//MAIN POPUP PARAMS
$(".open-popup-entire-screen").click(function () {
  lenis.stop();
});

$(".close-popup-entire-screen").click(function () {
  lenis.start();
});

$('.scroll-content').on('wheel', function(event) {
  event.preventDefault();

  // Récupérer l'élément .scroll-content
  var scrollContent = $(this);

  // Récupérer la valeur actuelle de la position de défilement
  var currentScrollTop = scrollContent.scrollTop();

  // Définir la nouvelle position de défilement en fonction de la direction de la molette
  scrollContent.scrollTop(currentScrollTop + event.originalEvent.deltaY);
});
