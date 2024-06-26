<div class="popup-entire-screen z-70 text-white-cloud" id-popup="{{ toKebabCase(''.$title.'') }}">
  <div class="relative z-40 w-full h-14 md:h-20 border-b-2 border-white-cloud">
    <div class="flex justify-between items-center h-full px-3 md:px-5">
      <div class="flex pr-3 md:pr-5 bg-green-primary h-full items-center border-r-2 lg:border-r-0">
        <img class="w-7 md:w-auto h-fit" src="{{ assetImg('logo.svg') }}" alt="Joubert">
      </div>

      <div class="bg-green-primary flex items-center cursor-pointer close-popup-entire-screen" id-close-popup="{{ toKebabCase(''.$title.'') }}">
        <img class="w-5 md:w-auto" src="{{ assetImg('icons/close.svg') }}" alt="Close popup">
      </div>
    </div>
  </div>

  <div class="pt-16">
    <div class="lg:ml-[80px]">
      <div class="container-right scroll-content">
        <div class="w-full md:w-10/12 content-popup text-lg" id="{{ toKebabCase(''.$title.'') }}">
          @if (!$isform)
            <div class="text-base leading-loose">{!! $firstContent !!}</div>
            <div>
              @foreach ($contents as $content_popup)
                <div class="pt-12">
                  <div class="text-lg md:text-xl font-semibold tracking-wider">{!! $content_popup['surtitle'] !!}</div>
                  <div class="w-12 h-[2px] bg-white-cloud my-6"></div>
                  <div class="text-base leading-loose">{!! $content_popup['content'] !!}</div>
                </div>
              @endforeach
            </div>
          @else
            @php echo FrmFormsController::get_form_shortcode( array( 'id' => 1 ) ); @endphp
          @endif
        </div>
      </div>
    </div>
  </div>

  <div class="hidden lg:block w-[50px] md:w-[80px] h-full absolute left-0 top-0 z-30 bg-green-primary border-r-2 border-white-cloud"></div>
</div>
