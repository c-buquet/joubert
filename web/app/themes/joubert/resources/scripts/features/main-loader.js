import { gsap } from "gsap";
import * as lottie from 'lottie-web'
import * as data from '../../data/remarquable-loader.json'

const animation = lottie.loadAnimation({
  container: document.getElementById('loading-icon'),
  renderer: 'svg',
  loop: false,
  autoplay: false,
  animationData: data,

})

animation.setSpeed(1)

if (window.location.href.indexOf("/wp") === -1) {
  const wrapper = document.getElementById("loading");
  const loadingIcon = document.getElementById("loading-icon");

  if (disableAnimations === 0) {
    animation.play()

    const tl = gsap.timeline();

    tl.to(loadingIcon, { opacity: 0, duration: 0.4, delay: 5 }) // Démarre après que l'icône est apparue
      .to(wrapper, { height: 0, duration: 0.45 }) // Démarre après la disparition de l'icône
      .set(wrapper, { className: "+=hidden" }); // Démarre après la disparition du fond bleu
  }
}
