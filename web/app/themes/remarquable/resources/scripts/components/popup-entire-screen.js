import { gsap, TimelineLite } from "gsap";

// Création de la timeline
var navigation = new TimelineLite({ paused: true, reversed: true });

// Définition de l'animation dans la timeline
navigation.to(".popup-entire-screen", 0.5, { opacity: 1, display: 'block' })
    .to(".navbar", 0.3, { opacity: 0 }, "-=0.1")
    .to(".close-popup-entire-screen", 0.3, { display: "block", opacity: 1 }, "-=0.1")
    .from(".menu", 0.5, { opacity: 0, y: 30 })
    .from(".social", 0.5, { opacity: 0 });

// Gestion de l'événement click
$(".open-popup-entire-screen, .close-popup-entire-screen").click(function () {
    navigation.reversed() ? navigation.play() : navigation.reverse();
});
