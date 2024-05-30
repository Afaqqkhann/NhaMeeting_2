@extends("base")

<link href="{{ URL::to('/public/plugins/datatables/dataTables.bootstrap.css') }}" rel="stylesheet">

@section('sidebar')
@parent
        <!--<h1>Advertisement(s)</h1>-->
@endsection



@section('content')

    <table id="users-table" class="table table-condensed">
        <thead>
        <tr>
            <th>Id</th>
            <th>Dated</th>
            <th>Status</th>
            <th>Last Dated</th>
            <th>E Doc</th>
        </tr>
        </thead>
    </table>



@stop


