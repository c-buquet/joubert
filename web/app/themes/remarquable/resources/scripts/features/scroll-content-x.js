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
        momentum *= 0.95; // Ajustez le facteur de décélération
        requestAnimationFrame(inertialScroll);
      }
    }

    inertialScroll();
  });
});
