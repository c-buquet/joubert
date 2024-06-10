@extends('layouts.app')

@section('content')
    @while (have_posts())
        @php(the_post())
        <article class="single bg-green-primary py-20 md:py-36 relative overflow-x-clip">
          <div class="lg:ml-[80px]">
            <div class="container-right">
              <div class="">
                  <div class="text-red-0 surtitle">{!! the_category( ', ',) !!}</div>
                  <div class="title-h1 text-grey-100 leading-tight uppercase">{!! the_title() !!}</div>
                  <div class="text-sm text-grey-200 pt-1">{!! get_the_date('d/m/Y') !!} - 2 minutes de lecture</div>
              </div>
              <div class="wysiwyg-content pt-8 text-white-cloud">
                  @include('partials.content-page')
              </div>
            </div>
          </div>
        </article>
    @endwhile
@endsection
