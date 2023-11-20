import { gsap } from "gsap";

if (window.location.href.indexOf("/wp") === -1) {
  const wrapper = document.getElementById("loading");
  const bgBlue = document.getElementById("bg-green");
  const loadingIcon = document.getElementById("loading-icon");

  if (disableAnimations === 0) {
    const tl = gsap.timeline();

    tl.fromTo(loadingIcon, { opacity: 0 }, { opacity: 1, duration: 1 })
      .fromTo(bgBlue, { height: 0 }, { height: "100%", duration: 0.75 }, 3.5) // Démarre en même temps que l'icône
      .to(loadingIcon, { opacity: 0, duration: 0.4 }) // Démarre après que l'icône est apparue
      .to(wrapper, { height: 0, duration: 0.45 }) // Démarre après la disparition de l'icône
      .set(wrapper, { className: "+=hidden" }); // Démarre après la disparition du fond bleu
  }
}
