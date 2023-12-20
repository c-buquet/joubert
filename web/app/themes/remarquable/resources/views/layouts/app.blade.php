@php
  $footerLinksPopup = App\Controllers\PageOptions::getFooterDatas()['links_popup'] ?? null;
@endphp

@include('sections.header')

<div id="loading" class="fixed block w-screen h-screen inset-0 bg-green-primary {{ $_SESSION['disable_animations'] ? "" : "" }}">
  <div id="loading-icon" class="absolute left-1/2 top-[48%] sm:top-1/2 transform -translate-x-1/2 -translate-y-1/2 h-full w-10/12 md:w-8/12 xl:w-1/2 z-10">
  </div>
</div>

<main id="main" class="main relative {{ is_admin() ? 'text-black' : 'text-white-cloud' }}">
    <div class="hidden lg:block w-[50px] md:w-[80px] h-full absolute left-0 top-0 z-30 bg-green-primary border-r-2 border-white-cloud"></div>
    @yield('content')
</main>
@if ($footerLinksPopup)
    @foreach ($footerLinksPopup as $link)
      <x-popup-entire-screen title="{{ $link['link_title'] }}" firstContent="{!! $link['first_content_popup'] !!}" :contents="$link['content_popup']" />
    @endforeach
@endif
<x-popup-entire-screen title="contact-us" isform />

@include('sections.footer')
