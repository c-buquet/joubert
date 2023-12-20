@php
    $hero_image = $fields['hero_image'] ?? null;
    $hero_image_mobile = $fields['hero_image_mobile'] ?? null;
    $title = $fields['title'] ?? null;
@endphp
<section class="{{ $classes }} {{ is_admin() ? 'h-[700px]' : 'h-[750px] md:h-screen' }} relative" data-gsap>
  <div class="lg:ml-[80px] h-full">
    <div class="container-right h-full">
      <div class="relative z-10 flex items-end md:items-center h-full pb-6 md:pb-0 pt-24">
        <div class="w-10/12 lg:w-[590px] pt-28" data-anim data-from='{"autoAlpha":0, "duration": 1}' data-to='{"autoAlpha":1,"duration": 1.25}'>{!! $title !!}</div>

        <div class="w-full hidden md:flex justify-center absolute bottom-10 left-1/2 -translate-x-2/4">
          <img class="w-5 md:w-auto icon-mouse" data-anim data-position="0.25" data-from='{"autoAlpha":0, "duration": 1}' data-to='{"autoAlpha":1,"duration": 2}' src="{{ assetImg('icons/mouse-scroll.svg') }}" alt="Mouse scroll">
        </div>
      </div>
    </div>
  </div>

  <img class="hidden sm:block w-full h-full object-cover object-right-bottom md:object-right absolute top-0 left-0" src="{{ $hero_image['url'] }}" alt="{!! $hero_image['title'] !!}">
  <img class="sm:hidden w-full h-full object-cover object-right-bottom md:object-right absolute top-0 left-0" src="{{ $hero_image_mobile['url'] }}" alt="{!! $hero_image_mobile['title'] !!}">
  <div class="bg-main-hero-filter w-full h-full absolute top-0 left-0"></div>
</section>
