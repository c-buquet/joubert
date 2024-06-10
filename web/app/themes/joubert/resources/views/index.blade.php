@extends('layouts.app')

@section('content')
  @php
  global $post;
  $page_for_posts_id = get_option('page_for_posts');
  if ( $page_for_posts_id ) :
      $post = get_page($page_for_posts_id);
      setup_postdata($post);
  @endphp
      <div id="post-{!! the_ID() !!}">
          <div>
              {{ the_content() }}
          </div>
      </div>
  @php
      rewind_posts(); endif;
  @endphp
@endsection

