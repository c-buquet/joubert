<div class="popup-entire-screen z-70" id="{{ toKebabCase(''.$title.'') }}">
  <div class="relative z-40 w-full h-12 md:h-24 border-b-2 border-white-cloud">
    <div class="flex justify-between items-center h-full px-3 md:px-7">
      <div class="flex">
        <img class="w-6 md:w-auto h-fit" src="{{ assetImg('logo.svg') }}" alt="Remarquable!">
      </div>

      <div class="bg-green-primary flex items-center cursor-pointer close-popup-entire-screen">
        <img class="w-5 md:w-auto" src="{{ assetImg('icons/close.svg') }}" alt="Close popup">
      </div>
    </div>
  </div>

  <div class="pt-16">
    <div class="ml-[50px] md:ml-[100px]">
      <div class="container-right">
        <div class="w-10/12 content-popup">
          @if (!$is_Form)
            <div>{!! $firstContent !!}</div>
            <div>
              @foreach ($contents as $content_popup)
                {!! $content_popup['surtitle'] !!}
                <div class="w-12 h-2 bg-white-cloud my-2"></div>
                {!! $content_popup['content'] !!}
              @endforeach
            </div>
          @else
            Formulaire
          @endif
        </div>
      </div>
    </div>
  </div>

  <div class="w-[50px] md:w-[100px] h-full absolute left-0 top-0 z-30 bg-green-primary border-r-2 border-white-cloud"></div>
</div>
