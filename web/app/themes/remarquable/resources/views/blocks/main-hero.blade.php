@php
    $hero_image = $fields['hero_image'] ?? null;
    $title = $fields['title'] ?? null;
@endphp
<section class="{{ $classes }} {{ is_admin() ? 'h-[700px]' : 'h-screen' }} relative">
  <div class="ml-[50px] md:ml-[80px] h-full">
    <div class="container-right h-full">
      <div class="relative z-10 flex items-center h-full pt-24">
        <div class="w-10/12 lg:w-[590px] ml-6 pt-28 lenis-title">{!! $title !!}</div>

        <div class="w-full flex justify-center absolute bottom-10 left-1/2 -translate-x-2/4">
          <img class="w-5 md:w-auto icon-mouse" src="{{ assetImg('icons/mouse-scroll.svg') }}" alt="Mouse scroll">
        </div>
      </div>
    </div>
  </div>

  <img class="w-full h-full object-cover object-right absolute top-0 left-0" src="{{ $hero_image['url'] }}" alt="{!! $hero_image['title'] !!}">
</section>
