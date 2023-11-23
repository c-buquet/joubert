<section class="{{ $classes }} text-green-primary {{ $fields['block_color'] ?? 'bg-white' }}">
  <div class="ml-[50px] md:ml-[100px]">
    <div class="container-right">
      <div class="pb-20">
        {!! $fields['title'] !!}
      </div>

      @if ($fields['cards'])
        <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-6">
          @foreach ($fields['cards'] as $card)
            <div class="bg-green-lightest flex flex-col gap-6 px-8 py-16">
              <div class="tracking-wider uppercase"><h6>{!! $card['title'] !!}</h6></div>
              <x-icons.horizontal-bar />
              <div>{!! $card['description'] !!}</div>
            </div>
          @endforeach
        </div>
      @endif
    </div>
  </div>
</section>
