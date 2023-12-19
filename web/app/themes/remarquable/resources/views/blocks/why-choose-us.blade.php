<section class="{{ $classes }} text-green-primary {{ $fields['block_color'] ?? 'bg-white' }}">
  <div class="lg:ml-[80px]">
    <div class="container-right">
      <div class="pb-20">
        {!! $fields['title'] !!}
      </div>

      @if ($fields['cards'])
        <div class="hidden md:grid xl:grid-cols-4 gap-6">
          @foreach ($fields['cards'] as $card)
            <div class="bg-green-lightest flex flex-col gap-6 px-8 py-16">
              <div class="tracking-wider uppercase"><h6>{!! $card['title'] !!}</h6></div>
              <x-icons.horizontal-bar />
              <div>{!! $card['description'] !!}</div>
            </div>
          @endforeach
        </div>

        <div class="swiper swiper-choose-us md:hidden">
          <div class="swiper-wrapper">
              @foreach ($fields['cards'] as $card)
                  <div class="swiper-slide bg-green-lightest flex flex-col gap-6 px-8 py-16">
                      <div class="tracking-wider uppercase"><h6>{!! $card['title'] !!}</h6></div>
                      <x-icons.horizontal-bar />
                      <div>{!! $card['description'] !!}</div>
                  </div>
              @endforeach
          </div>
          <div class="mt-10 flex justify-end">
            <div id="nextSlideChooseUs" class="cursor-pointer w-max">
              <x-icons.mobile-arrow />
            </div>
          </div>
        </div>
      @endif
    </div>
  </div>
</section>
