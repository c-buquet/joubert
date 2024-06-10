<section class="{{ $classes }} relative w-full h-0" data-gsap>
  <div class="absolute top-0 right-0 -translate-y-2/4 w-10/12 md:w-1/2 flex justify-end">
    <img class="min-h-[275px] md:min-h-full lg:max-h-[400px] object-cover"
    data-anim data-from='{"autoAlpha":0}' data-to='{"autoAlpha":1,"duration": 1.5}'
    src="{{ $fields['image']['url'] }}" alt="{!! $fields['image']['title'] !!}">
  </div>
</section>
