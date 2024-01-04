<section class="{{ $classes }}">
  <div class="block md:hidden relative h-full" data-gsap>
    <img class="absolute top-0 left-0 w-full h-full object-cover bg-[left_2rem]" src="{{ $fields['main_bg']['url'] }}" alt="main-bg">

    <div class="lg:ml-[80px] relative h-full" data-anim data-from='{"autoAlpha":0}' data-to='{"autoAlpha":1,"duration": 2}'>
      <div class="container-right h-full">
        <div class="flex flex-col py-24 gap-5 h-full">
          <div class="">
            <div class="text-green-primary">{!! $fields['title'] !!}</div>
            <div class="surtitle-primary pt-2">{!! $fields['subtitle'] !!}</div>
          </div>
          <div class="flex justify-center w-full">
            <img class="max-h-[447px]" src="{{ $fields['right_title_image']['url'] }}" alt="right_title_image">
          </div>
        </div>
      </div>

      <div id="open-popup-feat-work" class="absolute bottom-8 right-6 anim-slider-arrows cursor-pointer text-white-cloud">
        <x-icons.mobile-arrow />
      </div>
    </div>
  </div>

  <div class="hidden md:block" data-gsap>
    <div class="swiper swiper-featured-work">
      <div class="swiper-wrapper">

        <!-- FIRST SLIDE -->
        <div class="swiper-slide">
          <div class="relative h-full">
            <img class="absolute top-0 left-0 w-full h-full object-cover" src="{{ $fields['main_bg']['url'] }}" alt="main-bg">

            <div class="lg:ml-[80px] h-full">
              <div class="container-right h-full relative" data-anim data-from='{"autoAlpha":0}' data-to='{"autoAlpha":1,"duration": 2}'>
                <div class="w-full xl:w-10/12 mx-auto flex flex-row py-32 items-center justify-between h-full">
                  <div class="">
                    <div class="text-green-primary">{!! $fields['title'] !!}</div>
                    <div class="surtitle-primary pt-2">{!! $fields['subtitle'] !!}</div>
                  </div>
                  <div class="">
                    <img class="max-h-[670px]" src="{{ $fields['right_title_image']['url'] }}" alt="right_title_image">
                  </div>
                </div>

                <div data-id="nextSlideFeaturedWork" class="absolute bottom-20 right-4 anim-slider-arrows cursor-pointer text-white-cloud">
                  <x-icons.big-arrow />
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- SECOND SLIDE -->
        <div class="swiper-slide bg-featured-work">
          <div class="lg:ml-[80px] h-full">
            <div class="container-right h-full relative">
              <div class="flex flex-col lg:flex-row py-32 px-6 items-center justify-between h-full gap-x-4 gap-y-8">
                <div class="w-full lg:w-1/2">
                  {!! $fields['contenu'] !!}
                </div>
                <div class="w-8/12 lg:w-5/12">
                  <img class="object-contain max-h-[600px]" src="{{ $fields['right_image']['url'] }}" alt="">
                </div>
              </div>

              <div data-id="nextSlideFeaturedWork" class="absolute bottom-20 right-4 anim-slider-arrows cursor-pointer text-white-cloud">
                <x-icons.big-arrow />
              </div>
            </div>
          </div>
        </div>

        <!-- LAST SLIDE -->
        <div class="swiper-slide bg-green-feat-work-continue h-full">
          <div class="lg:ml-[80px] relative h-full">
            <div class="container-right h-full">
              <div class="flex flex-col lg:flex-row py-32 px-6 items-center justify-between h-full gap-4">
                <div class="w-8/12 lg:w-7/12">
                  <img class="object-contain max-h-[600px]" src="{{ $fields['left_second_image']['url'] }}" alt="">
                </div>
                <div class="w-8/12 lg:w-5/12">
                  <img class="object-contain max-h-[600px]" src="{{ $fields['right_second_image']['url'] }}" alt="">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>




  <!-- POPUP FEAT WORK -->
  <div class="popup-featured-work z-70 text-white-cloud" id="popup-feat-work">
    <div class="relative z-40 w-full h-14 border-b-2 border-white-cloud">
      <div class="flex justify-between items-center h-full pl-0 pr-5">
        <div class="flex px-3 bg-green-primary h-full items-center border-r-2 lg:border-r-0">
          <img class="w-7 h-fit" src="{{ assetImg('logo.svg') }}" alt="Remarquable!">
        </div>

        <div class="bg-green-feat-work-continue flex items-center cursor-pointer" id="close-popup-feat-work">
          <img class="w-5" src="{{ assetImg('icons/close.svg') }}" alt="Close popup">
        </div>
      </div>
    </div>

    <div class="pt-16">
      <div class="lg:ml-[80px]">
        <div class="container-right scroll-content">
          <div id="content-popup-feat-work" class="w-full text-lg">
            <div class="flex flex-col gap-8">
              <div class="w-full">
                {!! $fields['contenu'] !!}
              </div>
              <div class="w-full">
                <img class="object-contain max-h-[600px]" src="{{ $fields['right_image']['url'] }}" alt="">
              </div>
              <div class="w-full">
                <img class="object-contain max-h-[600px]" src="{{ $fields['left_second_image']['url'] }}" alt="">
              </div>
              <div class="w-fumm">
                <img class="object-contain max-h-[600px]" src="{{ $fields['right_second_image']['url'] }}" alt="">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
