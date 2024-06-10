<button {{ $attributes->class([$type, 'flex-row-reverse' => $inverse])->merge() }}>
  {{ $slot }}
  @if($icon)
    <img src="{{ assetImg('icons/arrow-link.svg') }}" alt="{!! $title !!}">
  @endif
</button>
