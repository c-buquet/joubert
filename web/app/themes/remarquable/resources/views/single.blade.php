@extends('layouts.app')

@section('content')
    @while (have_posts())
        @php(the_post())
        <article class="single bg-grey-400 pt-12 md:pt-36 relative overflow-x-clip">
            <div class="container mx-auto">
              <div class="container mx-auto">
                <div class="text-red-0 surtitle">{!! the_category( ', ',) !!}</div>
                <div class="title-h3 text-grey-100 pt-6">Blog</div>
                <div class="title-h1 text-grey-100 leading-tight uppercase">{!! the_title() !!}</div>
                <div class="text-sm text-grey-200 pt-1">{!! get_the_date('d/m/Y') !!} - 5 minutes de lecture</div>
              </div>
            </div>
            <div class="pt-1 container mx-auto">
                @include('partials.content-page')
            </div>
        </article>

        <!-- Bloc reutilisable: End of page -->
        <?php echo do_shortcode('[reblex id=\'498\']'); ?>
    @endwhile
@endsection
