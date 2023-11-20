@extends('layouts.app')

@section('content')
  @include('partials.page-header')

  @if (! have_posts())
      <div class="content-404-page flex flex-col justify-center items-center relative h-screen bg-green-dark">
          <div class="flex flex-col justify-center items-center gap-y-6 relative {{ is_admin() ? '' : 'z-10' }}">
              <div class="flex gap-8 md:gap-24 font-playfair text-9xl md:text-11xl">
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
                This page doesn't exist !
              </div>
              <a class="relative group ml-4" href="/" title="Accueil">
                <x-button>Back to home</x-button>
              </a>
          </div>
      </div>
  @endif
@endsection
