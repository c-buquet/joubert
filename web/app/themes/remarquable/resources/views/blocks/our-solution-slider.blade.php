@php
   $title_mobile = preg_replace('/<br\s*\/?>/i', ' ', $fields['title']);
@endphp
<section class="{{ $classes }} relative" data-gsap>
  <div id="content-mobile" class="container-right pt-10 pb-14 text-green-primary md:hidden">
    <div class="swiper swiper-our-solution md:hidden">
      <div class="pb-9" data-anim data-from='{"autoAlpha":0}' data-to='{"autoAlpha":1, "duration": 1.5}'>
        <h2>{!! $title_mobile !!}</h2>
      </div>
      <div class="swiper-wrapper">
          @foreach ($fields['slides'] as $slide)
              <div class="swiper-slide" data-anim data-from='{"z":-20,"y": 100,"autoAlpha":0}' data-to='{"y":0,"autoAlpha":1,"duration": 2}'>
                <div class="flex justify-center items-center mb-4 md:mb-10">
                  <img class="relative z-2 max-h-[250px] w-full object-cover" src="{{ $slide['image']['url'] }}" alt="{!! $slide['title'] !!}">
                </div>
                <div class="flex flex-col">
                  <div class="big-surtitle opacity-80 pb-1">0{!! $loop->index + 1 !!}</div>
                  <div class="title-mobile-h3 md:title-h3 pb-4">{!! $slide['title'] !!}</div>
                  <div class="p-medium">{!! $slide['text'] !!}</div>
                </div>
              </div>
          @endforeach
      </div>
      <div class="mt-4 flex justify-end">
        <div id="nextSlideOurSolution" class="cursor-pointer w-max">
          <x-icons.mobile-arrow />
        </div>
      </div>
    </div>
  </div>

  <div id="content-desktop" class="scroll-content-slides hidden md:block">
    <div class="mx-auto lg:mx-0 lg:ml-[80px]">
      <div class="flex">
        <div class="title-custom bg-green-dark flex items-center justify-center min-w-[300px] w-5/12 px-4">
          <span data-anim data-from='{"autoAlpha":0}' data-to='{"autoAlpha":1, "duration": 1.5}'>
            {!! $fields['title'] !!}
          </span>
        </div>

        <div class="big-content-slides flex relative ml-10 xl:ml-40 pt-20 lg:pt-40 text-green-primary">
          @if ($fields['slides'])
            <div class="slides-container flex">
              @foreach ($fields['slides'] as $slide)
                <div class="slide-item">
                  <div class="flex justify-between items-center mb-10 relative" data-anim data-from='{"autoAlpha":0}' data-to='{"autoAlpha":1,"duration": 2}'>
                    <div class="z-1 absolute top-1/2 -translate-y-2/4 left-8 {{ $loop->last ? 'w-8/12' : 'w-full' }} h-[2px] bg-green-primary"></div>
                    <div class=" w-18 h-18 bg-green-lightest rounded-full relative">
                      <div class="w-6 h-6 bg-green-primary rounded-full absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2"></div>
                    </div>
                    <img class="relative z-2" src="{{ $slide['image']['url'] }}" alt="{!! $slide['title'] !!}">
                    <div ></div>
                  </div>
                  <div class="absolute w-1/2 lg:w-7/12" style="white-space: normal;" data-anim data-from='{"z":-20,"y": 100,"autoAlpha":0}' data-to='{"y":0,"autoAlpha":1,"duration": 2}'>
                    <div class="big-surtitle opacity-80 pb-1">0{!! $loop->index + 1 !!}</div>
                    <div class="title-mobile-h3 md:title-h3 pb-4">{!! $slide['title'] !!}</div>
                    <div class="p-medium">{!! $slide['text'] !!}</div>
                  </div>
                </div>
              @endforeach
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
  <div class="absolute bottom-20 right-20 icon-arrow-slide cursor-pointer">
    <x-icons.big-arrow />
  </div>
</section>
