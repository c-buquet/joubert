<section class="{{ $classes }} bg-green-primary">
  <div class="lg:ml-[80px]">
    <div class="container-right">
      <div class="pb-8 md:pb-24">
        {!! $fields['title_text'] !!}
      </div>

      <div id="content-mobile" class="flex md:hidden flex-col md:flex-row gap-6">
        @foreach ($fields['cards'] as $card)
            <div class="w-full md:w-1/2 relative flex flex-col items-center justify-end group min-h-[360px] max-h-[400px] p-6">
              <img class="absolute top-0 left-0 object-cover h-full w-full" src="{{ $card['image']['url'] }}" alt="{!! $card['image']['title'] !!}">
              <div class="absolute top-0 left-0 w-full h-full bg-cards-filter"></div>

              <div class="flex flex-col z-10 items-center gap-4 h-full w-full justify-center pt-28">
                <div class="font-playfair font-bold text-lg tracking-wider uppercase text-center">{!! $card['title'] !!}</div>
                <x-icons.horizontal-bar color="bg-white-cloud" />
                <div class="text-center leading-relaxed">{!! $card['text'] !!}</div>
              </div>
            </div>
        @endforeach
      </div>

      <div id="content-desktop" class="hidden md:flex flex-col md:flex-row gap-6">
        @foreach ($fields['cards'] as $card)
            <div class="w-full md:w-1/2 relative flex flex-col items-center justify-center group lg:min-h-[370px] max-h-[470px]">
              <img class="absolute top-0 left-0 object-cover h-full w-full" src="{{ $card['image']['url'] }}" alt="{!! $card['image']['title'] !!}">
              <div class="absolute top-0 left-0 w-full h-full bg-cards-filter"></div>

              <div class="flex flex-col z-10 items-center gap-4 absolute top-0 left-0 h-full w-full justify-center">
                <div class="font-playfair font-bold text-2xl tracking-wide uppercase text-center">{!! $card['title'] !!}</div>
                <x-icons.horizontal-bar color="bg-white-cloud" />
              </div>

              <div class="h-full opacity-0 group-hover:opacity-100 duration-500 z-20 bg-green-primary py-6 lg:py-20 px-6 lg:px-16 flex flex-col gap-4 items-center justify-center border">
                <div class="font-playfair font-bold text-2xl tracking-wide uppercase text-center">{!! $card['title'] !!}</div>
                <x-icons.horizontal-bar color="bg-white-cloud" />
                <div class="text-center">{!! $card['text'] !!}</div>
              </div>
            </div>
        @endforeach
      </div>
    </div>
  </div>
</section>
