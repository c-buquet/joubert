@php
    $hero_image = $fields['hero_image'] ?? null;
    $title = $fields['title'] ?? null;
@endphp
<section class="{{ $classes }} h-screen">
  <div class="container h-full">
    <div class="relative z-10 flex items-center h-full pt-24">
      <div class="w-11/12 ml-auto pt-28">
        <div class="w-10/12 lg:w-[735px] ml-6">{!! $title !!}</div>
      </div>
      <div class="w-full flex justify-center absolute bottom-10 left-1/2 -translate-x-2/4">
        <img class="w-5 md:w-auto" src="{{ assetImg('icons/mouse-scroll.svg') }}" alt="Mouse scroll">
      </div>
    </div>
  </div>

  <img class="w-full h-full object-cover object-right absolute top-0 left-0" src="{{ $hero_image['url'] }}" alt="{{ $hero_image['title'] }}">
</section>
