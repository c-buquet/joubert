import { gsap, TimelineLite } from "gsap";
import { CSSPlugin } from "gsap/CSSPlugin"; // Importez le plugin CSS

// Enregistrez les plugins nécessaires
gsap.registerPlugin(CSSPlugin);

// Création de la timeline
var navigation = new TimelineLite({ paused: true, reversed: true });

// Définition de l'animation dans la timeline
navigation.to(".popup-entire-screen", 0.5, { opacity: 1, display: 'block' })
    .to(".close-popup-entire-screen", 0.4, { display: "block", opacity: 1 }, "-=0.1")
    .from(".content-popup", 0.5, { opacity: 0, y: 30 })

// Gestion de l'événement click
$(".open-popup-entire-screen, .close-popup-entire-screen").click(function () {
    navigation.reversed() ? navigation.play() : navigation.reverse();
});
