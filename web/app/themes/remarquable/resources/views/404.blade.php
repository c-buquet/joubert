@extends('layouts.app')

@section('content')
  @include('partials.page-header')

  @if (! have_posts())
      <div class="content-404-page flex flex-col justify-center items-center relative">
          <div class="flex flex-col justify-center items-center gap-y-6 relative {{ is_admin() ? '' : 'z-10' }}">
              <div class="flex gap-8 md:gap-24 font-optima-bold text-9xl md:text-11xl">
                <div>
                  4
                </div>
                <div>
                  0
                </div>
                <div>
                  4
                </div>
              </div>
              <div class="text-center">
                Cette page est inconnue ou n’existe pas
              </div>
              <a class="relative group ml-4" href="/" title="Accueil">
                <x-button class="w-max" shadow="yes" color="" type="primary">Revenir à l’accueil</x-button>
              </a>
          </div>
          <div class="absolute blur-xxxl top-16 left-16 rounded-full bg-red-200 {{ is_admin() ? '' : 'z-0' }} w-[340px] h-[340px]"></div>
          <div class="absolute top-16 left-16 z-0">
            <svg width="480" height="359" viewBox="0 0 480 359" fill="none" xmlns="http://www.w3.org/2000/svg">
              <g opacity="0.3">
              <path d="M179.359 352.73C407.388 302.34 462.347 113.016 462.347 113.016C462.347 113.016 449.239 231.449 281.838 340.769C281.838 340.769 383.491 348.46 480.001 294.649C480.001 294.649 381.769 375.801 189.598 354.451L179.359 352.73Z" fill="white"/>
              <path d="M305.76 273.306C305.76 273.306 168.25 370.688 0 217.016C0 217.016 122.979 325.417 305.76 273.306Z" fill="white"/>
              <path d="M35.8828 0C35.8828 0 129.248 223.484 299.885 257.115C299.793 257.069 45.5476 280.99 35.8828 0Z" fill="#EE3831"/>
              </g>
              </svg>

          </div>
          <div class="absolute blur-xxxl -bottom-12 -right-12 rounded-full bg-red-200 {{ is_admin() ? '' : 'z-0' }} w-[212px] h-[212px]"></div>
      </div>
  @endif
@endsection
