$(".scroll-content-slides").on("mousedown", function (event) {
  event.preventDefault();

  var startX = event.pageX - $(this).offset().left;
  var scrollLeft = $(this).scrollLeft();
  var momentum = 0.8; // Facteur d'inertie

  $(this).css("cursor", "grabbing");

  $(document).on("mousemove", function handleMouseMove(event) {
    var x = event.pageX - $(".scroll-content-slides").offset().left;
    var walk = (x - startX) * 2; // Ajustez la vitesse du défilement
    $(".scroll-content-slides").scrollLeft(scrollLeft - walk);
  });

  $(document).on("mouseup", function () {
    $(".scroll-content-slides").css("cursor", "grab");
    $(document).off("mousemove");

    // Effet de défilement inertiel après le relâchement
    function inertialScroll() {
      if (Math.abs(momentum) > 0.1) {
        $(".scroll-content-slides").scrollLeft(
          $(".scroll-content-slides").scrollLeft() - momentum
        );
        momentum *= 0.95;
        requestAnimationFrame(inertialScroll);
      }
    }

    inertialScroll();
  });
});

$(".icon-arrow-slide").on("click", function() {
  var scrollAmount = 800;
  var currentScrollPosition = $(".scroll-content-slides").scrollLeft();
  $(".scroll-content-slides").animate({
      scrollLeft: currentScrollPosition + scrollAmount
  }, 600);
});

function checkScrollEnd() {
  var $container = $(".scroll-content-slides");
  var scrollLeft = $container.scrollLeft();
  var scrollWidth = $container.get(0).scrollWidth;
  var containerWidth = $container.width();

  // Vérifie si le défilement a atteint la fin
  if (scrollLeft + containerWidth >= scrollWidth - 100) {
    $(".icon-arrow-slide").fadeOut(); // Effet de fondu pour masquer la flèche
  } else {
    $(".icon-arrow-slide").fadeIn(); // Effet de fondu pour afficher la flèche
  }
}

// Appeler checkScrollEnd lors du défilement
$(".scroll-content-slides").on("scroll", function() {
  checkScrollEnd();
});
