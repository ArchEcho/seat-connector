<div class="box box-solid">
  <div class="box-header with-border">
    <i class="fa fa-plus"></i>
    <h3 class="box-title">{{ trans('seat-connector::seat.toolbox') }}</h3>
  </div>
  <div class="box-body">
    <form role="form" method="post" id="connector-toolbox">
      {{ csrf_field() }}

        <div class="form-group">
          <label for="connector-driver">{{ trans('seat-connector::seat.driver') }}</label>
          <select name="connector-driver" id="connector-driver" class="form-control">
            @foreach(config('seat-connector.drivers') as $driver => $metadata)
              <option value="{{ $driver }}">{{ ucfirst($metadata['name']) }}</option>
            @endforeach
          </select>
        </div>

        <div class="form-group">
          <label for="connector-filter-type">{{ trans_choice('web::seat.type', 1) }}</label>
          <select name="entity_type" id="connector-filter-type" class="form-control">
            <option value="public">{{ trans('seat-connector::seat.public_filter') }}</option>
            <option value="group">{{ trans('seat-connector::seat.user_filter') }}</option>
            <option value="role">{{ trans('seat-connector::seat.role_filter') }}</option>
            <option value="corporation">{{ trans('seat-connector::seat.corporation_filter') }}</option>
            <option value="title">{{ trans('seat-connector::seat.title_filter') }}</option>
            <option value="alliance">{{ trans('seat-connector::seat.alliance_filter') }}</option>
          </select>
        </div>

        <div class="form-group">
          <label for="connector-filter-group">{{ trans('web::seat.username') }}</label>
          <select name="entity_id" id="connector-filter-group" class="form-control" disabled></select>
        </div>

        <div class="form-group">
          <label for="connector-filter-role">{{ trans_choice('web::seat.role', 1) }}</label>
          <select name="entity_id" id="connector-filter-role" class="form-control" disabled></select>
        </div>

        <div class="form-group">
          <label for="connector-filter-corporation">{{ trans_choice('web::seat.corporation', 1) }}</label>
          <select name="entity_id" id="connector-filter-corporation" class="form-control" disabled></select>
        </div>

        <div class="form-group">
          <label for="connector-filter-title">{{ trans_choice('web::seat.title', 1) }}</label>
          <select name="entity_id" id="connector-filter-title" class="form-control" disabled></select>
        </div>

        <div class="form-group">
          <label for="connector-filter-alliance">{{ trans('web::seat.alliance') }}</label>
          <select name="entity_id" id="connector-filter-alliance" class="form-control" disabled></select>
        </div>

        <div class="form-group">
          <label for="connector-set">{{ trans_choice('seat-connector::seat.sets', 1) }}</label>
          <select name="set_id" id="connector-set" class="form-control"></select>
        </div>

    </form>
  </div>
  <div class="box-footer">
    <button type="submit" form="connector-toolbox" class="btn btn-success pull-right">{{ trans('web::seat.add') }}</button>
  </div>
</div>