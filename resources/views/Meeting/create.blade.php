@extends("base")

@section('sidebar')
    @parent
    <h1>Add Meeting</h1>
@endsection

@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h4>Add Meeting</h4>

            @if ($errors->any() || session('error'))
                <div class="alert alert-danger" role="alert">
                    {{ session('error') }}
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif
        </div>
        <div class="box-body">
            <form action="{{ url('meeting/store') }}" method="POST" class="form-horizontal" enctype="multipart/form-data">
              {{ csrf_field() }}<!-- Laravel CSRF Protection -->

                <div class="form-group">
                    {{-- <label class="col-xs-2 control-label">Module Name <span class="required" style="color: red">*</span></label>
                    <div class="col-xs-4">
                        <select name="module_id" class="js-select2 form-control input-sm">
                            <!-- Options should be added here dynamically if needed -->
                        </select>
                    </div> --}}

                    <label class="col-xs-2 control-label">Status <span class="required" style="color: red">*</span></label>
                    <div class="col-xs-4">
                      <select name="meeting_status" class="js-select2 form-control input-sm">
                          <option value="1">Active</option>
                          <option value="0">Inactive</option>
                      </select>
                  </div>
                </div>

               
                <div class="form-group">
                  <label class="col-xs-2 control-label">Upload Date <span class="required" style="color: red">*</span></label>
                  <div class="col-xs-4">
                      <input type="date" name="meeting_upload_date" class="form-control input-sm" id="title" placeholder="Upload Date">
                  </div>
              </div>
              <div class="form-group">
                <label for="meeting_type" class="col-xs-2 control-label">Meeting Type<span class="required" style="color: red">*</span></label>
                <div class="col-xs-4" class="js-select2 form-control input-sm">
                <select name="meeting_type" class="form-control">
                    @foreach($meetings as $meeting)
                        <option value="{{ $meeting->meetingType->mt_id }}">{{ $meeting->meetingType->mt_title }}</option>
                    @endforeach
                </select>
            </div>
            </div>      
            <div class="form-group">
              <label class="col-xs-2 control-label">Meeting No <span class="required" style="color: red">*</span></label>
              <div class="col-xs-4">
                  <input type="text" name="meeting_no" class="form-control input-sm" id="title" placeholder="Meeting No">
              </div>
          </div>
            <div class="form-group">
              <label class="col-xs-2 control-label">Meeting Date <span class="required" style="color: red">*</span></label>
              <div class="col-xs-4">
                  <input type="date" name="meeting_date" class="form-control input-sm" id="title" placeholder="Meeting Date">
              </div>
          </div>
          <div class="form-group">
            <label class="col-xs-2 control-label">Meeting pdf <span class="required" style="color: red">*</span></label>
            <div class="col-xs-4">
              <input type="file" name="meeting_edoc" class="form-control" id="site_title" accept=".jpeg,.bmp,.png,.jpg,.xlsx,.pdf,.html" >

            </div>
        </div>

                <button type="submit" class="btn btn-primary pull-right">Save</button>
            </form>
        </div>
    </div>
    <a href="{{ URL::to('meeting') }}" class="btn btn-primary"><i class="fa fa-arrow-circle-left">Back</i></a>
@endsection
