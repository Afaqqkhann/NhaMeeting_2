@extends("base")

@section('sidebar')
@parent
    <h1>View Meeting Document </h1>
@endsection

@section('content')

<div class="box box-primary">
    <div class="box-body">
        <table class="table table-bordered table-striped dtable dataTables">
           
            <tr>
                <th>MD-id</th><td>{{ $docs->md_id }}</td>
                <th>Doc Title</th><td>{{ $docs ->doctsandard->doc_title }}</td>
                <th>Meeting Document Title</th><td>{{ $docs->md_title }}</td>
                <th>Meeting_id</th><td>{{ $docs->meeting->meeting_id }}</td>
            </tr>
            <tr>
                <th>Meeting Upload-Time</th><td> <?php echo $docs->md_upload_date ?  date('d-m-Y', strtotime($docs->mt_upload_date)) : '' ?> </td>
                <th>MeetingDoc pdf</th> <td>
                    @if($docs->md_edoc)
                    <a class="btn btn-white pull-left" href="{{ URL::to('public/Meeting-Document/'.$docs->md_edoc) }}" title="{{$docs->md_edoc}}">
                        <i class="fa  fa-file-pdf-o" style="color:#7e0b0b">{{$docs->md_edoc}}</i>
                    </a>
                    @endif
                </td>
                <th>Status</th> <td>
                    @if ($docs->md_status == 1)
                        Active
                        
                    @elseif ($docs->md_status == 0)
                        InActive
                  
                        @endif
                </td>
            </tr>
           
            
        </table>
        <a href="{{ URL::to('dashboard/meeting') }}" class="btn btn-primary">Back</a>
    </div><!-- /.box-body -->
</div><!-- /.box -->

@stop
