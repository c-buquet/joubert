<section class="{{ $classes }}">
  <img class="hidden sm:block object-cover {{ $fields['bg_position'] }} w-full min-h-[270px] max-h-[460px]" src="{{ $fields['image']['url'] }}" alt="{!! $fields['image']['title'] !!}">
  <img class="sm:hidden object-cover w-full min-h-[270px] max-h-[460px]" src="{{ $fields['image_mobile']['url'] }}" alt="{!! $fields['image_mobile']['title'] !!}">
</section>
