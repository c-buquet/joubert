<section class="{{ $classes }} relative">
  <img class="w-full h-full object-cover object-center absolute top-0 left-0" src="{{ $fields['image']['url'] }}" alt="{!! $fields['image']['title'] !!}">

  <div class="ml-[50px] md:ml-[100px] flex items-center h-full py-32 md:pt-64 md:pb-60">
    <div class="container-right">
      <div class="z-10 relative flex flex-col gap-14 items-center w-8/12 mx-auto">
        <div class="text-white-cloud text-center">
          <h2>{!! $fields['title'] !!}</h2>
        </div>
        <div class="flex flex-row flex-wrap justify-center gap-7">
          <div class="cursor-pointer open-popup-entire-screen" data-id="contact-us">
            <x-button>Contact us</x-button>
          </div>
          <a href="{{ $fields['button']['url'] }}" title="{!! $fields['button']['title'] !!}" target="{{ $fields['button']['target'] }}">
            <x-button type="secondary" color="white">{!! $fields['button']['title'] !!}</x-button>
          </a>
        </div>
      </div>
    </div>
  </div>
</section>
