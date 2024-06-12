@extends("base")

@section('sidebar')
    @parent
    <h1>Wing</h1>
@endsection

@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h4>Wing</h4>

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
            <form action="{{ url('updatewing/' . $action->action_id) }}" method="POST" class="form-horizontal" enctype="multipart/form-data">
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
                        <select name="action_status" class="js-select2 form-control input-sm">
                            <option value="1" >Active</option>
                            <option  value="0">InActive</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-2 control-label">Title <span class="required" style="color: red">*</span></label>
                    <div class="col-xs-4">
                        <input type="text" name="action_title" class="form-control input-sm" id="title" value="{{ $action->action_title }}">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary pull-right">Update</button>
            </form>
        </div>
    </div>
    <a href="{{ URL::to('dashboard/wing') }}" class="btn btn-primary"><i class="fa fa-arrow-circle-left">Back</i></a>
@endsection
