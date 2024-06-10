<section class="{{ $classes }} relative">
  <img class="w-full h-full object-cover object-center absolute top-0 left-0" src="{{ $fields['image']['url'] }}" alt="{!! $fields['image']['title'] !!}">

  <div class="lg:ml-[80px] flex items-center h-full pt-48 pb-24 md:py-32 md:pt-64 md:pb-60">
    <div class="container-right">
      <div class="z-10 relative flex flex-col gap-20 md:gap-14 items-center w-full md:w-8/12 mx-auto">
        <div class="text-white-cloud text-center" data-gsap>
          <h2 data-anim data-from='{"z":-20,"x": -100,"autoAlpha":0}' data-to='{"x":0,"autoAlpha":1,"duration": 1.25}'>
            {!! $fields['title'] !!}
          </h2>
        </div>
        <div class="flex flex-row flex-wrap justify-center items-center gap-7 w-full md:w-auto" data-gsap>
          <div class="cursor-pointer open-popup-entire-screen w-full lg:w-auto" data-id="contact-us"
          data-anim data-position="0.25" data-from='{"z":-20,"y": 30,"autoAlpha":0}' data-to='{"y":0,"autoAlpha":1,"duration": 1.25}'
          >
            <x-button>Contact us</x-button>
          </div>
          <a class="w-full lg:w-auto" href="{{ $fields['button']['url'] }}" title="{!! $fields['button']['title'] !!}" target="{{ $fields['button']['target'] }}"
          data-anim data-position="0.5" data-from='{"z":-20,"y": 30,"autoAlpha":0}' data-to='{"y":0,"autoAlpha":1,"duration": 1.25}'
          >
            <x-button type="secondary" color="white">{!! $fields['button']['title'] !!}</x-button>
          </a>
        </div>
      </div>
    </div>
  </div>
</section>
