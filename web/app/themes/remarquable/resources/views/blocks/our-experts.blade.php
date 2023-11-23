<section class="{{ $classes }} bg-green-primary">
  <div class="ml-[50px] md:ml-[100px]">
    <div class="container-right">
      <div class="pb-24">
        {!! $fields['title_text'] !!}
      </div>

      <div class="flex flex-col md:flex-row gap-6">
        @foreach ($fields['cards'] as $card)
            <div class="w-full md:w-1/2 relative flex flex-col items-center justify-center group min-h-[370px] max-h-[470px]">
              <img class="absolute top-0 left-0 object-cover h-full w-full" src="{{ $card['image']['url'] }}" alt="{!! $card['image']['title'] !!}">
              <div class="absolute top-0 left-0 w-full h-full bg-cards-filter"></div>

              <div class="flex flex-col z-10 items-center gap-4 absolute top-0 left-0 h-full w-full justify-center">
                <div class="font-playfair font-bold text-2xl tracking-wide uppercase text-center">{!! $card['title'] !!}</div>
                <x-icons.horizontal-bar color="bg-white-cloud" />
              </div>

              <div class="h-full opacity-0 group-hover:opacity-100 duration-500 z-20 bg-green-primary py-20 px-16 flex flex-col gap-4 items-center justify-center border">
                <div class="font-playfair font-bold text-2xl tracking-wide uppercase text-center">{!! $card['title'] !!}</div>
                <x-icons.horizontal-bar color="bg-white-cloud" />
                <div class="text-xl text-center">{!! $card['text'] !!}</div>
              </div>
            </div>
        @endforeach
      </div>
    </div>
  </div>
</section>
