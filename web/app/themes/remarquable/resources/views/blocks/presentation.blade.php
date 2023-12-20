<section class="{{ $classes }} bg-green-dark">
  <div class="lg:ml-[80px]">
      <div class="flex flex-col lg:flex-row">
        <div class="h-full w-9/12 md:w-5/12 xl:w-1/3 mr-auto lg:mx-auto lg:mr-0 lg:-ml-16" data-gsap>
          <img class="min-h-[400px] object-cover"
          data-anim data-from='{"z":-20,"x": -100,"autoAlpha":0}' data-to='{"x":0,"autoAlpha":1,"duration": 1.25}'
          src="{{ $fields['image']['url'] }}" alt="{!! $fields['image']['title'] !!}">
        </div>
        <div class="flex flex-col justify-center pr-4 lg:pr-0 w-10/12 lg:w-5/12 ml-auto lg:mr-auto mt-14 lg:mt-0" data-gsap>
          <div class="relative">
            <div class="absolute -top-6 lg:-top-16 -left-12 lg:-left-9 text-green-light" data-anim data-from='{"z":-20,"y": -100,"autoAlpha":0}' data-to='{"y":0,"autoAlpha":1,"duration": 1.25}'>
              <x-icons.quotes />
            </div>
            <div class="text-quote" data-anim data-position="0.5" data-from='{"autoAlpha":0}' data-to='{"autoAlpha":1,"duration": 1.25}'>{!! $fields['text'] !!}</div>
          </div>

          @if (isset($fields['linkedin']) && $fields['linkedin'])
            <div class="flex flex-col md:flex-row gap-4 pt-8 md:pt-14 md:items-center"
            data-anim data-position="0.5" data-from='{"z":-20,"y": 100,"autoAlpha":0}' data-to='{"y":0,"autoAlpha":1,"duration": 1.25}'
            >
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
