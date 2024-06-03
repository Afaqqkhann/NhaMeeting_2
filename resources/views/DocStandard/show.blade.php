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
                <th>Doc Type Title</th><td>{{$docs->doc_title}}</td>
                <th>Status</th><td>{{$docs->doc_status}}</td>
            </tr>
           
            
           
          </table>
         
         
    </div><!-- /.box-body -->
    <a href="{{URL::to('docstandard')}}" class="btn btn-primary">Back</a>
</div><!-- /.box -->

@stop