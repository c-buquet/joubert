@php
  $MENU = App\Controllers\Menu::make('primary_navigation') ?? null;
@endphp
<header id="main-header" class="fixed w-full z-40 h-12 md:h-24 border-b-2 border-white-cloud">
  <div class="flex justify-between h-full">
    <div class="flex pl-3 p-2 md:pl-7 md:p-6">
      <img class="w-6 md:w-auto h-fit" src="{{ assetImg('logo.svg') }}" alt="Remarquable!">
    </div>
    <div class="flex">
      <a href="#" title="Refirection">
        <button class="h-full px-8 flex items-center font-lato font-bold tracking-wider bg-green-light text-green-primary hover:bg-green-primary hover:text-green-light duration-700 ">CONTACT US</button>
      </a>
      <div class="bg-green-primary w-24 p-6 flex items-center cursor-pointer">
        <img class="w-5 md:w-auto" src="{{ assetImg('icons/menu-burger-base.svg') }}" alt="Menu Burger">
      </div>
    </div>
  </div>
</header>
