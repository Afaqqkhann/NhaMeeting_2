@extends("base")

@section('sidebar')
    @parent
    <h1>Edit Meeting Agenda</h1>
@endsection

@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h4> Edit Meeting Agenda </h4>

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
            <form action="{{ url('meeting_agenda/update/' . $meetingAgenda->ma_id) }}" method="POST" class="form-horizontal" enctype="multipart/form-data">
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
                        <select name="ma_status" class="js-select2 form-control input-sm">
                            <option value="1" >Active</option>
                            <option  value="0">InActive</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-2 control-label">Title <span class="required" style="color: red">*</span></label>
                    <div class="col-xs-4">
                        <input type="text" name="ma_title" class="form-control input-sm" id="title" value="{{ $meetingAgenda->ma_title }}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-2 control-label">Upload date <span class="required" style="color: red">*</span></label>
                    <div class="col-xs-4">
                        <input type="date" name="ma_upload_date" class="form-control input-sm" id="title" value="{{ $meetingAgenda->ma_upload_date }}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-2 control-label">Meeting Id <span class="required" style="color: red">*</span></label>
                    <div class="col-xs-4">
                        <input type="number" name="meeting_id" class="form-control input-sm" id="title" value="{{ $meetingAgenda->meeting_id }}">
                    </div>
                </div>
                <div class="form-group">
                    <label for="action_id" class="col-xs-2 control-label">Wing<span class="required" style="color: red">*</span></label>
                    <div class="col-xs-4">
                        <select name="action_id" id="action_id" class="form-control js-select2 input-sm">
                            <option value="{{ $meetingAgenda->wing->action_id }}">{{ $meetingAgenda->wing->action_title }}</option>
                            @foreach($allagendas as $agenda)
                                @if($agenda->action_id != $meetingAgenda->wing->action_id)
                                    <option value="{{ $agenda->action_id }}">
                                        {{ $agenda->action_title }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                
                {{-- <div class="form-group">
                    <label class="col-xs-2 control-label">Meeting Type <span class="required" style="color: red">*</span></label>
                    <div class="col-xs-4">
                        <input type="number" name="meeting_type" class="form-control input-sm" id="title" value="{{ $meetings->meeting_type }}">
                    </div>
                </div> --}}
                <div class="form-group">
                    <label class="col-xs-2 control-label">Meeting Agenda pdf <span class="required" style="color: red">*</span></label>
                    <div class="col-xs-4">
                        <input type="file" name="ma_edoc" class="form-control input-sm" id="title" value="{{ $meetingAgenda->ma_edoc }}">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary pull-right">Update</button>
            </form>
        </div>
    </div>
    {{-- <a href="{{ URL::to('meeting/show') }}" class="btn btn-primary"><i class="fa fa-arrow-circle-left">Back</i></a> --}}
@endsection
