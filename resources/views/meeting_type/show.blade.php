@extends("base")

@section('sidebar')
@parent
    <h1>View Meeting Type</h1>
@endsection

@section('content')

<div class="box  box-primary">
    <div class="box-body">
        
          <table class="table table-bordered table-striped dtable dataTables">
            <tr>
                <th>Meeting Type ID</th><td>{{$meetings->mt_id}}</td>
                <th>Meeting Type Title</th><td>{{$meetings->mt_title}}</td>
                <th>Status</th> <td>
                    @if ($meetings->mt_status == 1)
                        Active
                        
                    @elseif ($meetings->mt_status == 0)
                    InActive
                  
                        @endif
                </td>
           
            
           
          </table>
         
         
    </div><!-- /.box-body -->
    <a href="{{URL::to('dashboard/meeting_types')}}" class="btn btn-primary">Back</a>
</div><!-- /.box -->

@stop