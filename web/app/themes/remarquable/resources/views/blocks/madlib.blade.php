@php
  $fieldsOptions =  get_fields("options");
@endphp

<script>
  let madlibs = {!!  json_encode($fieldsOptions['steps'])!!};
  let contracts = {!!  json_encode($fieldsOptions['contracts'])  !!}
  let titleBtn = {!!  json_encode($fields['title_btn'])  !!}
  let titleBtnMobile = {!!  json_encode($fields['title_btn_mobile'])  !!}
  let legende = {!!  json_encode($fields['legende'])  !!}
</script>

@if (is_admin())
  <div class="bg-grey-300 relative z-30 px-4 sm:px-8">
    <span class="py-12 text-center flex justify-center items-center title-h2 text-grey-700">
      Ici se trouve le Madlib
    </span>
  </div>
@else
  <div id="madlibs"></div>
@endif
