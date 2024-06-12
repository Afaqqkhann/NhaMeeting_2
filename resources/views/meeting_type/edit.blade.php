@extends("base")

@section('sidebar')
    @parent
    <h1>Edit Meeting Type</h1>
@endsection

@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h4>Edit Meeting Type</h4>

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
            <form action="{{ url('meetingtype/update/' . $meeting_types->mt_id) }}" method="POST" class="form-horizontal" enctype="multipart/form-data">
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
                        <select name="mt_status" class="js-select2 form-control input-sm" value="{{$meeting_types->mt_status}}">
                            <option value="1" {{ $meeting_types->mt_status == 1 ? 'selected' : '' }} >Active</option>
                            <option  value="0" {{ $meeting_types->mt_status == 0 ? 'selected' : '' }}>InActive</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-2 control-label">Title <span class="required" style="color: red">*</span></label>
                    <div class="col-xs-4">
                        <input type="text" name="mt_title" class="form-control input-sm" id="title" value="{{ $meeting_types->mt_title }}">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary pull-right">Update</button>
            </form>
        </div>
    </div>
    <a href="{{ URL::to('dashboard/meeting_types') }}" class="btn btn-primary"><i class="fa fa-arrow-circle-left">Back</i></a>
@endsection
