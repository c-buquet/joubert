@php
    $color_ariane = get_field('color_ariane', get_the_ID()) ?? 'white';
@endphp
<a class="sr-only focus:not-sr-only" href="#main">
    {{ __('Skip to content') }}
</a>

@include('sections.header')

<main id="main" class="main relative pt-[102px] lg:pt-[150px]">
    @yield('content')
</main>

@hasSection('sidebar')
    <aside class="sidebar">
        @yield('sidebar')
    </aside>
@endif

@include('sections.footer')
