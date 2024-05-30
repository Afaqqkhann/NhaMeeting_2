@extends("base")

@section('sidebar')
@parent
    <h1>Progress Activity</h1>
@endsection

@section('content')

<div class="box  box-primary">
    <div class="box-body">
        @foreach($progress as $key => $pro)
          <table class="table table-bordered table-striped dtable dataTables">
            <tr>
                <th>Task Title</th><td>{{$pro->title}}</td>
                <th>Assign To</th><td>{{$pro->title}}</td>
            </tr>
            <tr>
                <th>Module Name</th><td>{{ $pro->mod_title }}</td>
            
                <th>Core System Name</th><td>{{$pro->cs_title}}</td>
            </tr>
            
            <tr>
			

          </table>
          @endforeach
          <a href="{{ URL::to('progress_activity') }}" class="btn btn-primary">Back</a>
    </div><!-- /.box-body -->
</div><!-- /.box -->

@stop