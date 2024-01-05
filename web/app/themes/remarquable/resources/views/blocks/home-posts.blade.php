@php
    $cards = $fields['cards'] ?? null;
@endphp
<section class="{{ $classes }} bg-green-primary">
  <div class="lg:ml-[80px]">
    <div class="container-right">
      <div class="pb-8 md:pb-16" data-gsap>
        <span data-anim data-position="0" data-from='{"z":-20,"x": -100,"autoAlpha":0}' data-to='{"x":0,"autoAlpha":1,"duration": 1.25}'>
          {!! $fields['title'] !!}
        </span>
      </div>

      @if ($cards)
        <div class="grid md:grid-cols-2 gap-6">
          @foreach ($cards as $card)
              <div class="w-full flex flex-col" data-gsap>
                <a href="{{ $card['title_n_link']['url'] }}" title="{!! $card['title_n_link']['title'] !!}" target="{{ $card['title_n_link']['target'] }}" class="flex relative w-full group" data-anim data-position="0.25" data-from='{"autoAlpha":0}' data-to='{"autoAlpha":1,"duration": 2}'>
                  <img class="object-cover h-full w-full max-h-[370px]" src="{{ $card['image']['url'] }}" alt="{!! $card['title_n_link']['title'] !!}">
                  <div class="absolute top-0 left-0 w-full h-full {{ $loop->even ? 'bg-cards-filter-left' : 'bg-cards-filter-right' }}"></div>

                  <div class="absolute top-0 left-0 w-full h-full z-20 flex text-sm text-green-light uppercase items-center justify-center cursor-pointer opacity-0 group-hover:opacity-100 duration-500">
                    <span class="underline underline-offset-4 tracking-wider text-lg">View article</span>
                    <span><img src="{{ assetImg('icons/arrow-link.svg') }}" alt="View article"></span>
                  </div>
                  <div class="absolute top-0 left-0 w-full h-full z-10 bg-green-primary bg-opacity-0 group-hover:bg-opacity-70 duration-500"></div>
                </a>

                <a href="{{ $card['title_n_link']['url'] }}" title="{!! $card['title_n_link']['title'] !!}" target="{{ $card['title_n_link']['target'] }}" class="mt-4 hover:opacity-70 duration-500">
                  <div class="font-medium text-lg tracking-wide uppercase">{!! $card['title_n_link']['title'] !!}</div>
                  <div class="text-sm mt-2">{!! $card['date'] !!}</div>
                </a>
              </div>
          @endforeach
        </div>
      @endif
    </div>
  </div>
</section>
