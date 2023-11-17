@php
  $footerDatas = App\Controllers\PageOptions::getFooterDatas() ?? null;
@endphp
<footer class="main-footer bg-green-primary px-10 pb-16 pt-12 border-t-2 border-white-cloud">
  <div class="pb-4">
    <img src="{{ $footerDatas['logo'] }}" alt="Remarquable!">
  </div>

  <div class="flex justify-between">
    <div class="text-sm">
        {!! $footerDatas['slogan'] !!}
    </div>

    <div class="flex flex-row gap-4">
      <x-link-with-arrow title="Terms"></x-link-with-arrow>
      <x-link-with-arrow title="Privacy"></x-link-with-arrow>
      <x-link-with-arrow title="Cookies"></x-link-with-arrow>
    </div>
  </div>
</footer>
