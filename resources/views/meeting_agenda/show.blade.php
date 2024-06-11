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
                <th>Wing Title</th><td>{{ $agendas->wing->wing_head }}</td>
                <th>Meeting Agendas Title</th><td>{{ $agendas->ma_title }}</td>
                <th>Meeting_id</th><td>{{ $agendas->meeting->meeting_id }}</td>
            </tr>
            <tr>
                <th>MeetingAgenda-Upload-Time</th><td> <?php echo $agendas->ma_upload_date ?  date('d-m-Y', strtotime($agendas->ma_upload_date)) : '' ?> </td>
                <th>Agendas pdf</th> <td>
                    @if($agendas->ma_edoc)
                    <a class="btn btn-white pull-left" href="{{ URL::to('public/agendas/'.$agendas->ma_edoc) }}" title="{{$agendas->meeting_edoc}}">
                        <i class="fa  fa-file-pdf-o" style="color:#7e0b0b">{{$agendas->ma_edoc}}</i>
                    </a>
                    @endif
                </td>
                <th>Status</th> <td>
                    @if ($agendas->ma_status == 1)
                        Active
                        
                    @elseif ($agendas->ma_status == 0)
                    InActive
                  
                        @endif
                </td>
            </tr>
           
            
        </table>
        <a href="{{ URL::to('meeting') }}" class="btn btn-primary">Back</a>
    </div><!-- /.box-body -->
</div><!-- /.box -->

@stop
