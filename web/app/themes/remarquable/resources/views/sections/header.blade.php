@php
  $MENU = App\Controllers\Menu::make('primary_navigation') ?? null;

  $logo = App\Controllers\PageOptions::getLogo() ?? null;
  $buttons = App\Controllers\PageOptions::getButtonsHeader() ?? null;
  $HEADER_DATAS = App\Controllers\PageOptions::getHeaderDatas() ?? null;
@endphp
<header id="header-unique" class="fixed w-full z-50 text-5xl">
HELLOOO
</header>
