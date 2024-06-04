@extends("base")

@section('sidebar')
@parent
    <h1>View Agendas</h1>
@endsection

@section('content')

<div class="box box-primary">
    <div class="box-body">
        <table class="table table-bordered table-striped dtable dataTables">
           
            <tr>
                <th>M_A id</th><td>{{ $agendas->ma_id }}</td>
                <th>Wing Title</th><td>{{ $agendas->wing->action_title }}</td>
                <th>Meeting Agendas Title</th><td>{{ $agendas->ma_title }}</td>
                <th>Meeting_id</th><td>{{ $agendas->meeting->meeting_id }}</td>
            </tr>
            <tr>
                <th>Upload date</th><td>{{ $agendas->ma_upload_date }}</td>
                <th>Agendas pdf</th> <td>
                    @if($agendas->ma_edoc)
                    <a class="btn btn-white pull-left" href="{{ URL::to('public/agendas/'.$agendas->ma_edoc) }}" title="{{$agendas->meeting_edoc}}">
                        <i class="fa  fa-file-pdf-o" style="color:#7e0b0b">{{$agendas->ma_edoc}}</i>
                    </a>
                    @endif
                </td>
                <th>status</th><td>{{ $agendas->ma_status }}</td>
            </tr>
           
            
        </table>
        <a href="{{ URL::to('meeting') }}" class="btn btn-primary">Back</a>
    </div><!-- /.box-body -->
</div><!-- /.box -->

@stop
