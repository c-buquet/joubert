<section class="{{ $classes }} scroll-content-slides">
  <div class="ml-[50px] md:ml-[100px]">
    <div class="flex">
      <div class="title-custom bg-green-primary flex items-center justify-center w-5/12 px-4">
        {!! $fields['title'] !!}
      </div>

      <div class="big-content-slides flex relative ml-10 xl:ml-40 pt-40 pb-96 text-green-primary">
        @if ($fields['slides'])
          <div class="slides-container flex">
            @foreach ($fields['slides'] as $slide)
              <div class="slide-item">
                <div class="flex justify-between items-center mb-10 relative">
                  <div class="z-1 absolute top-1/2 -translate-y-2/4 left-8 w-full h-[2px] bg-green-primary"></div>
                  <div class=" w-18 h-18 bg-green-lightest rounded-full relative">
                    <div class="w-6 h-6 bg-green-primary rounded-full absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2"></div>
                  </div>
                  <img class="relative z-2" src="{{ $slide['image']['url'] }}" alt="{!! $slide['title'] !!}">
                  <div ></div>
                </div>
                <div class="absolute w-7/12" style="white-space: normal;">
                  <div class="font-playfair text-5xl font-semibold pb-4">{!! $slide['title'] !!}</div>
                  <div class="text-xl leading-8">{!! $slide['text'] !!}</div>
                </div>
              </div>
            @endforeach
          </div>
        @endif
        <div class="absolute bottom-10 right-12">
          <x-icons.big-arrow />
        </div>
      </div>
    </div>
  </div>
</section>
