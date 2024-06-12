@extends("base")

@section('sidebar')
@parent
    <h1>View Wing</h1>
@endsection

@section('content')

<div class="box  box-primary">
    <div class="box-body">
        
          <table class="table table-bordered table-striped dtable dataTables">
            <tr>
                <th>Wing Title</th><td>{{$wings->action_title}}</td>
                <th>Status</th><td>{{$wings->action_status}}</td>
            </tr>
           
            
           
          </table>
         
         
    </div><!-- /.box-body -->
    <a href="{{URL::to('dashboard/wing')}}" class="btn btn-primary">Back</a>
</div><!-- /.box -->

@stop