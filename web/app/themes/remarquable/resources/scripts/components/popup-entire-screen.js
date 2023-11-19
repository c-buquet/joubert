import { gsap, TimelineLite } from "gsap";
import { CSSPlugin } from "gsap/CSSPlugin";

gsap.registerPlugin(CSSPlugin);

var navigation = new TimelineLite({ paused: true, reversed: true });

$(".open-popup-entire-screen").click(function () {
    // Réinitialiser la timeline à chaque ouverture de popup
    navigation.clear();

    var dataId = $(this).attr("data-id");

    var popupSelector = $(".popup-entire-screen[id-popup='" + dataId + "']");
    var closeSelector = $(".close-popup-entire-screen[id-close-popup='" + dataId + "']");
    var contentSelector = $(".content-popup[id='" + dataId + "']");

    // Réinitialiser manuellement l'état de contentSelector
    gsap.set(contentSelector, { opacity: 1, y: 0 });

    navigation
        .to(popupSelector, 0.5, { opacity: 1, display: 'block' })
        .to(closeSelector, 0.4, { display: "block", opacity: 1 }, "-=0.1")
        .from(contentSelector, 0.5, { opacity: 0, y: 30 });

    navigation.play();

});

$(".close-popup-entire-screen").click(function () {
    var dataId = $(this).attr("id-close-popup");
    var popupSelector = $(".popup-entire-screen[id-popup='" + dataId + "']");

    // Inverser ou stopper l'animation spécifique à la popup
    if (popupSelector.length) {
        navigation.reverse();
    }
});
