<section class="{{ $classes }} scroll-content-slides">
  <div class="ml-[50px] md:ml-[100px]">
    <div class="flex">
      <div class="bg-green-primary flex items-center justify-center w-5/12">
        {!! $fields['title'] !!}
      </div>

      <div class="big-content-slides flex relative">
        @if ($fields['slides'])
          <div class="slides-container flex">
            @foreach ($fields['slides'] as $slide)
              <div class="slide-item">
                <img src="{{ $slide['image']['url'] }}" alt="{!! $slide['title'] !!}">
                <div>{!! $slide['title'] !!}</div>
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
