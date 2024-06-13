@extends("base")

@section('sidebar')
    @parent
    <h1>Edit Meeting</h1>
@endsection

@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h4> Edit Meeting </h4>

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
            <form action="{{ url('meeting/update/' . $meetings->meeting_id) }}" method="POST" class="form-horizontal" enctype="multipart/form-data">
                {{ csrf_field() }}<!-- Laravel CSRF Protection -->
                <input type="hidden" name="_method" value="PUT">

                <div class="form-group">
                    {{-- <label class="col-xs-2 control-label">Module Name<span class="required" style="color: red">*</span></label>
                    <div class="col-xs-4">
                        <select name="module_id" class="js-select2 form-control input-sm">
                            <!-- Options should be added here dynamically if needed -->
                        </select>
                    </div> --}}
                    
                    <label class="col-xs-2 control-label">Status <span class="required" style="color: red">*</span></label>
                    <div class="col-xs-4">
                        <select name="meeting_status" class="js-select2 form-control input-sm">
                            <option value="1" {{ $meetings->meeting_status == 1 ? 'selected' : '' }}>Active</option>
                            <option  value="0" {{ $meetings->meeting_status == 0 ? 'selected' : '' }}>InActive</option>
                        </select>
                    </div>
                </div>

                
                <div class="form-group">
                    <label class="col-xs-2 control-label">Upload date <span class="required" style="color: red">*</span></label>
                    <div class="col-xs-4">
                        <input type="date" name="meeting_upload_date" class="form-control input-sm" id="meeting_upload_date" value="{{ $meetings->meeting_upload_date ? date('Y-m-d', strtotime($meetings->meeting_upload_date)) : '' }}">
                    </div>
                </div>
                
                
                
                
                <div class="form-group">
                    <label class="col-xs-2 control-label">Meeting date <span class="required" style="color: red">*</span></label>
                    <div class="col-xs-4">
                        <input type="date" name="meeting_date" class="form-control input-sm" id="title" value="{{ $meetings->meeting_date ? date('Y-m-d', strtotime($meetings->meeting_date)) : '' }}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-2 control-label">Meeting No <span class="required" style="color: red">*</span></label>
                    <div class="col-xs-4">
                        <input type="number" name="meeting_no" class="form-control input-sm" id="title" value="{{ $meetings->meeting_no }}">
                    </div>
                </div>
                <div class="form-group">
                    <label for="meeting_type" class="col-xs-2 control-label">Meeting Type<span class="required" style="color: red">*</span></label>
                    <div class="col-xs-4">
                        <select name="meeting_type" id="meeting_type" class="form-control js-select2 input-sm">
                            <option value="{{ $meetings->meetingType->mt_id }}">{{ $meetings->meetingType->mt_title }}</option>
                            @foreach($allMeetings as $meeting)
                                @if($meeting->mt_id != $meetings->mt_id)
                                    <option value="{{ $meeting->mt_id }}">
                                        {{ $meeting->mt_title }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-2 control-label">Meeting PDF <span class="required" style="color: red">*</span></label>
                    <div class="col-xs-4">
                        @if (!empty($meetings->meeting_edoc))
                            <p>Current file: <a href="{{ asset('path/to/your/files/' . $meetings->meeting_edoc) }}" target="_blank" style="color: red;">{{ $meetings->meeting_edoc }}</a></p>
                        @endif
                        <input type="file" name="meeting_edoc" class="form-control input-sm" id="meeting_edoc">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary pull-right">Update</button>
            </form>
        </div>
    </div>
    <a href="{{ URL::to('dashboard/meeting') }}" class="btn btn-primary"><i class="fa fa-arrow-circle-left">Back</i></a>
@endsection
