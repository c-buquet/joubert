<section class="{{ $classes }} bg-green-dark">
  <div class="ml-[50px] md:ml-[80px]">
      <div class="flex flex-col lg:flex-row">
        <div class="h-full w-8/12 md:w-5/12 xl:w-1/3 mx-auto lg:-ml-16">
          <img class="" src="{{ $fields['image']['url'] }}" alt="{!! $fields['image']['title'] !!}">
        </div>
        <div class="flex flex-col justify-center w-10/12 lg:w-5/12 mx-auto lg:mr-auto mt-12 lg:mt-0">
          <div class="relative">
            <div class="absolute -top-16 -left-9 text-green-light">
              <x-icons.quotes />
            </div>
            <h5>{!! $fields['text'] !!}</h5>
          </div>

          @if (isset($fields['linkedin']) && $fields['linkedin'])
            <div class="flex flex-row gap-4 pt-14 items-center">
              <div class="text-sm font-medium tracking-wider uppercase">
                {!! $fields['linkedin']['title'] !!}
              </div>
              <div>
                <a href="{{ $fields['linkedin']['url'] }}" title="{!! $fields['linkedin']['title'] !!}" target="{{ $fields['linkedin']['target'] }}">
                  <div class="svg-container">
                    <x-icons.linkedin-logo />
                  </div>
                </a>
              </div>
            </div>
          @endif
        </div>
      </div>
  </div>
</section>
