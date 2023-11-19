$(".scroll-content-slides").on("mousedown", function (event) {
  event.preventDefault();

  var isDragging = true;
  var startX = event.pageX - $(this).offset().left;
  var scrollLeft = $(this).scrollLeft();

  $(this).css("cursor", "grabbing");

  $(document).on("mousemove", function (event) {
    if (isDragging) {
      var x = event.pageX - $(".scroll-content-slides").offset().left;
      var walk = (x - startX) * 2; // Ajustez la vitesse du défilement
      $(".scroll-content-slides").scrollLeft(scrollLeft - walk);
    }
  });

  $(document).on("mouseup", function () {
    isDragging = false;
    $(".scroll-content-slides").css("cursor", "grab");
  });
});
