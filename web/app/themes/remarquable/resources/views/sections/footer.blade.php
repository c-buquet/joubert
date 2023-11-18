@php
  $footerDatas = App\Controllers\PageOptions::getFooterDatas() ?? null;
@endphp
<footer class="main-footer bg-green-primary px-10 pb-16 pt-12 border-t-2 border-white-cloud relative z-40">
  <div class="pb-4">
    <img src="{{ $footerDatas['logo'] }}" alt="Remarquable!">
  </div>

  <div class="flex justify-between">
    <div class="text-sm">
        {!! $footerDatas['slogan'] !!}
    </div>

    @if ($footerDatas['links_popup'])
      <div class="flex flex-row gap-4">
        @foreach ($footerDatas['links_popup'] as $linkPopup)
          <x-link-with-arrow title="{{ $linkPopup['link_title'] }}"></x-link-with-arrow>
        @endforeach
      </div>
    @endif
  </div>
</footer>
