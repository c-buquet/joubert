@php
    $color_ariane = get_field('color_ariane', get_the_ID()) ?? 'white';
@endphp
<a class="sr-only focus:not-sr-only" href="#main">
    {{ __('Skip to content') }}
</a>

@include('sections.header')

<main id="main" class="main relative pt-[102px] lg:pt-[150px]">
    @if (!is_front_page() || !is_404())
        <div class="absolute z-30 w-full hidden md:block breadcrumbs-remarquable pt-12">
            <div class="container mx-auto">
                <div class="breadcrumbs flex gap-x-4 {{ $color_ariane == 'white' ? 'text-grey-500' : 'text-grey-100' }} items-center text-sm typeof="BreadcrumbList" vocab="https://schema.org/">
                    @php
                        if (function_exists('bcn_display')) {
                            bcn_display();
                        }
                    @endphp
                </div>
            </div>
        </div>
    @endif
    @yield('content')
</main>

@hasSection('sidebar')
    <aside class="sidebar">
        @yield('sidebar')
    </aside>
@endif

@include('sections.footer')
