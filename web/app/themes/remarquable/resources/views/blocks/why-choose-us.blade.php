<section class="{{ $classes }} text-green-primary {{ $fields['block_color'] ?? 'bg-white' }}">
  <div class="lg:ml-[80px]">
    <div class="container-right">
      <div class="pb-10 md:pb-20" data-gsap>
        <span data-anim data-from='{"z":-20,"x": -100,"autoAlpha":0}' data-to='{"x":0,"autoAlpha":1,"duration": 1.25}'>
          {!! $fields['title'] !!}
        </span>
      </div>

      @if ($fields['cards'])
        <div class="hidden md:grid grid-cols-2 xl:grid-cols-4 gap-6" data-gsap>
          @foreach ($fields['cards'] as $card)
            <div class="bg-green-lightest flex flex-col gap-6 px-8 py-16"
            data-anim data-position="{{ (($loop->index + 1) / 3) + 0.25 }}" data-from='{"z":-20,"y": -100,"autoAlpha":0}' data-to='{"y":0,"autoAlpha":1,"duration": 1.25}'
            >
              <div class="tracking-wider uppercase"><h6>{!! $card['title'] !!}</h6></div>
              <x-icons.horizontal-bar />
              <div>{!! $card['description'] !!}</div>
            </div>
          @endforeach
        </div>

        <div class="swiper swiper-choose-us md:hidden">
          <div class="swiper-wrapper"
          data-anim data-from='{"autoAlpha":0}' data-to='{"autoAlpha":1,"duration": 1.25}'
          >
              @foreach ($fields['cards'] as $card)
                  <div class="swiper-slide bg-green-lightest flex flex-col gap-6 px-8 py-16">
                      <div class="tracking-wider uppercase"><h6>{!! $card['title'] !!}</h6></div>
                      <x-icons.horizontal-bar />
                      <div>{!! $card['description'] !!}</div>
                  </div>
              @endforeach
          </div>
          <div class="mt-10 flex justify-end">
            <div id="nextSlideChooseUs" class="cursor-pointer w-max text-green-primary anim-slider-arrows">
              <x-icons.mobile-arrow />
            </div>
          </div>
        </div>
      @endif
    </div>
  </div>
</section>
