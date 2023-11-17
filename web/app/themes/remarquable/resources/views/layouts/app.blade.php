@include('sections.header')

<main id="main" class="main relative">
    <div class="w-[50px] md:w-[100px] h-full absolute left-0 top-0 z-30 bg-green-primary border-r-2 border-white-cloud">
    </div>
    @yield('content')


</main>
<x-popup-entire-screen></x-popup-entire-screen>

@include('sections.footer')
