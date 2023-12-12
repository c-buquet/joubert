@php
  $footerLinksPopup = App\Controllers\PageOptions::getFooterDatas()['links_popup'] ?? null;
@endphp

@include('sections.header')

<div id="loading" class="fixed block w-full h-full inset-0 {{ $_SESSION['disable_animations'] ? "hidden" : "" }}">
  <div id="bg-green-dark" class="absolute bg-green-primary inset-0 w-full h-full"></div>
  <div id="bg-green" class="absolute bg-green-light w-full h-full"></div>
  <div id="loading-icon" class="absolute left-1/2 top-1/2 transform -translate-x-1/2 -translate-y-1/2 h-72 w-72 z-10">
      <img class="w-full h-full object-contain" src="{{ assetImg('logo.svg') }}" alt="Remarquable!">
  </div>
</div>

<main id="main" class="main relative {{ is_admin() ? 'text-black' : 'text-white-cloud' }}">
    <div class="w-[50px] md:w-[80px] h-full absolute left-0 top-0 z-30 bg-green-primary border-r-2 border-white-cloud"></div>
    @yield('content')
</main>
@if ($footerLinksPopup)
    @foreach ($footerLinksPopup as $link)
      <x-popup-entire-screen title="{{ $link['link_title'] }}" firstContent="{!! $link['first_content_popup'] !!}" :contents="$link['content_popup']" />
    @endforeach
@endif
<x-popup-entire-screen title="contact-us" isform />

@include('sections.footer')
