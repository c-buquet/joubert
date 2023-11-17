@php
  $footerLinksPopup = App\Controllers\PageOptions::getFooterDatas()['links_popup'] ?? null;
@endphp

@include('sections.header')

<main id="main" class="main relative">
    <div class="w-[50px] md:w-[100px] h-full absolute left-0 top-0 z-30 bg-green-primary border-r-2 border-white-cloud"></div>
    @yield('content')
</main>
@if ($footerLinksPopup)
    @foreach ($footerLinksPopup as $link)
      <x-popup-entire-screen title="{{ $link['link_title'] }}" firstContent="{!! $link['first_content_popup'] !!}" :contents="$link['content_popup']" />
    @endforeach
@endif

@include('sections.footer')
