<section class="{{ $classes }} bg-green-primary">
  <div class="lg:ml-[80px]">
    <div class="container-right">
      <div class="pb-8 md:pb-16" data-gsap>
        <span data-anim data-position="0" data-from='{"z":-20,"x": -100,"autoAlpha":0}' data-to='{"x":0,"autoAlpha":1,"duration": 1.25}'>
          {!! $fields['title'] !!}
        </span>
      </div>

      <div class="grid md:grid-cols-2 gap-6">
        @foreach ($fields['cards'] as $card)
            <div class="w-full flex flex-col" data-gsap>
              <div class="flex relative w-full" data-anim data-position="0.25" data-from='{"autoAlpha":0}' data-to='{"autoAlpha":1,"duration": 2}'>
                <img class="object-cover h-full w-full max-h-[370px]" src="{{ get_the_post_thumbnail_url($card->ID) }}" alt="{!! get_the_title($card->ID) !!}">
                <div class="absolute top-0 left-0 w-full h-full {{ $loop->even ? 'bg-cards-filter-left' : 'bg-cards-filter-right' }}"></div>
              </div>

              <a href="{{ get_the_permalink($card->ID) }}" title="{!! get_the_title($card->ID) !!}" class="mt-6 hover:opacity-70 duration-500">
                <div class="font-medium text-lg tracking-wide uppercase">{!! get_the_title($card->ID) !!}</div>
                <div class="text-sm mt-2">{!! get_the_date('d F Y', $card->ID) !!}</div>
              </a>
            </div>
        @endforeach
      </div>
    </div>
  </div>
</section>
