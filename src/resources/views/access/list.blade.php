@extends('web::layouts.grids.3-9')

@section('title', trans('web::seat.access-management'))
@section('page_header', trans('web::seat.access-management'))

@section('left')

  @include('seat-connector::access.includes.sidebar')

@stop

@section('right')
@stop

@push('javascript')
  <script>
      $('#connector-filter-type').change(function() {
          var filter_type = $('#connector-filter-type').val();

          $.each(['connector-filter-group', 'connector-filter-role', 'connector-filter-corporation', 'connector-filter-title', 'connector-filter-alliance'], function (key, value) {
              if (value === ('connector-filter-' + filter_type)) {
                  $(('#' + value)).prop('disabled', false);
              } else {
                  $(('#' + value)).prop('disabled', true);
              }
          });

          if (filter_type === 'title')
              $('#connector-filter-corporation, #connector-filter-title').prop('disabled', false);
      }).select2();

      $('#connector-filter-group').select2({
          ajax: {
              url: '{{ route('fastlookup.groups') }}',
              dataType: 'json',
              cache: true
          },
          minimumInputLength: 3
      });

      $('#connector-filter-role').select2({
          ajax: {
              url: '{{ route('seat-connector.api.roles') }}',
              dataType: 'json',
              cache: true
          },
          minimumInputLength: 3
      });

      $('#connector-filter-corporation').select2({
          ajax: {
              url: '{{ route('fastlookup.corporations') }}',
              dataType: 'json',
              cache: true
          },
          minimumInputLength: 3
      });

      $('#connector-filter-title').select2({
          ajax: {
              url: '{{ route('seat-connector.api.titles') }}',
              data: function (params) {
                  return {
                      search: params.term,
                      corporation_id: $('#connector-filter-corporation').val()
                  };
              },
              dataType: 'json',
              cache: true
          },
          minimumInputLength: 3
      });

      $('#connector-filter-alliance').select2({
          ajax: {
              url: '{{ route('fastlookup.alliances') }}',
              dataType: 'json',
              cache: true
          },
          minimumInputLength: 3
      });

      $('#connector-permission-group').select2();

      $(document).ready(function() {
          getCorporationTitle();
      });
  </script>
@endpush