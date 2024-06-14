export * from "gsap";
export * from "gsap/ScrollTrigger";
//Pour utiliser les plugins

import { gsap } from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

gsap.registerPlugin(ScrollTrigger);

setTimeout(() => {
  const contents = $("[data-gsap]");
  if (contents.length > 0) {
    contents.each((index, block) => {
      let children = $(block).find("[data-anim]");
      const tl = gsap.timeline();

      children.each((index, child) => {
        let position = 0;
        let from = JSON.parse($(child).attr("data-from"));
        let to = JSON.parse($(child).attr("data-to"));
        if (checkAttribute(child, "data-position")) {
          position = JSON.parse($(child).attr("data-position"));
        }
        tl.fromTo(child, from, to, position);
      });

      ScrollTrigger.create({
        trigger: block,
        animation: tl,
      });
    });
  }
}, "1");

function checkAttribute(element, attribute) {
  let attr = $(element).attr(attribute);
  return typeof attr !== typeof undefined && attr !== false;
}
