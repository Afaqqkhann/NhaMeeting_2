@extends("base")

@section('sidebar')
    @parent
    <h1>Edit Meeting Document</h1>
@endsection

@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h4> Edit Meeting Document </h4>

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
            <form action="{{ url('meeting_document/update/' . $meetingDoc->md_id) }}" method="POST" class="form-horizontal" enctype="multipart/form-data">
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
                        <select name="md_status" class="js-select2 form-control input-sm">
                            <option value="1" {{ $meetingDoc->md_status == 1 ? 'selected' : '' }}>Active</option>
                            <option  value="0" {{ $meetingDoc->md_status == 0 ? 'selected' : '' }}>InActive</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-2 control-label">Title <span class="required" style="color: red">*</span></label>
                    <div class="col-xs-4">
                        <input type="text" name="md_title" class="form-control input-sm" id="title" value="{{ $meetingDoc->md_title }}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-2 control-label">Upload date <span class="required" style="color: red">*</span></label>
                    <div class="col-xs-4">
                        <input type="date" name="md_upload_date" class="form-control input-sm" id="ma_upload_date" value="{{ $meetingDoc->md_upload_date ? date('Y-m-d', strtotime($meetingDoc->md_upload_date)) : '' }}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-2 control-label">Meeting Id <span class="required" style="color: red">*</span></label>
                    <div class="col-xs-4">
                        <input type="number" name="meeting_id" class="form-control input-sm" id="title" value="{{ $meetingDoc->meeting_id }}">
                    </div>
                </div>
                <div class="form-group">
                    <label for="doc_id" class="col-xs-2 control-label">Doc<span class="required" style="color: red">*</span></label>
                    <div class="col-xs-4">
                        <select name="doc_id" id="doc_id" class="form-control js-select2 input-sm">
                            <option value="{{ $meetingDoc->doctsandard->doc_id }}">{{ $meetingDoc->doctsandard->doc_title }}</option>
                            @foreach($alldoc as $doc)
                                @if($doc->doc_id != $meetingDoc->doctsandard->doc_id)
                                    <option value="{{ $doc->doc_id }}">
                                        {{ $doc->doc_title }}
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
                    <label class="col-xs-2 control-label">Meeting Doc PDF <span class="required" style="color: red">*</span></label>
                    <div class="col-xs-4">
                        <!-- Display existing file if available -->
                        @if(!empty($meetingDoc->md_edoc))
                            <p>Current file: <a href="{{ asset('path/to/your/files/' . $meetingDoc->md_edoc) }}" target="_blank" style="color:red;">{{ $meetingDoc->md_edoc }}</a></p>
                        @endif
                        <input type="file" name="md_edoc" class="form-control input-sm" id="title">
                    </div>
                </div>
                

                <button type="submit" class="btn btn-primary pull-right">Update</button>
            </form>
        </div>
    </div>
@endsection
