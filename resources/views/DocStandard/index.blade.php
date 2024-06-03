@extends("base")

@section('sidebar')
@parent
<h1>{{$page_title or ''}}</h1>
@endsection

@section('content')

@if (Session::has('success'))
<div class="alert alert-success" id="flash-message">
    {{ Session::get('success') }}
</div>
@endif
<style>
    .info-box {
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    }

    .info-box-number {
        font-size: 36px;
    }

    h2 {
        font-weight: 700
    }

    .progress-description,
    .info-box-text {
        font-size: 16px;
        font-weight: 600;
        color: #515460;

    }

    .info-box-number {
        font-size: 28px;
    }

    .info-box-icon {
        background: rgb(0 0 0 / 0%);
        color: #F44336;

    }

    .info-box:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);

    }

    .small-box {
        height: 200px;
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    }

    .info-box {
        background: #e6e1e1;
    }

    .info-box-icon {
        font-size: 70px;
    }

    .info-box-text {
        font-size: 21px;
    }

    .info-box-number {
        font-size: 28px;
    }

    .small-box .inner {
        height: 162px;
    }

    .small-box .icon {
        width: 200px;
        height: 170px;
        top: 0;
    }

    .small-box:hover {
        color: #1e1d1d;
    }

    .small-box>.small-box-footer {
        font-size: 22px;
        font-weight: bold;
        background: #F44336;
    }

    .highcharts-container>svg>text,
    .highcharts-container>svg>text,
    .highcharts-container>svg>text {
        display: none;
    }

    .table-bordered>thead>tr>th,
    .table-bordered>tbody>tr>th,
    .table-bordered>tfoot>tr>th,
    .table-bordered>thead>tr>td,
    .table-bordered>tbody>tr>td,
    .table-bordered>tfoot>tr>td {
        border: 1px solid #ded7d7;
    }
</style>
{!! Html::style('https://cdn.datatables.net/buttons/1.1.0/css/buttons.dataTables.min.css') !!}



<div class="box  box-primary">
    <div class="box-header">
        <h3 class="box-title">List of {{$page_title or ''}}</h3>
         <a href="{{ URL::to('docstandard/create') }}" class="btn btn-primary btn-sm  pull-right"> <i class="fa fa-user-plus "></i> Add-Standards</a>
    </div><!-- /.box-header -->
   
    <div class="box-body">
        <table class="table table-bordered table-striped" id="meeting_agenda">
            <thead class="bg-gray">
                <tr>
                    <th>#</th>
                    <th>Doc Title</th>
                    <th>Status</th>
                    <th>Action</th>
                   

                </tr>
            </thead>
            <tbody>

                @foreach($docStandards as $key => $type)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $type->doc_title or '' }} </td>
                    <td>
                        @if ($type->doc_status == 1)
                            Active
                        @elseif ($type->doc_status == 0)
                            Inactive
                            @endif
                    </td>
                   

                    <td>
                        <a class="btn btn-white pull-left" href="{{ route('docstandard.edit', ['id' => $type->doc_id]) }}" onclick="confirm()">
                            <i class="fa fa-edit "></i>
                        </a>
                   
                    
                        <a class="btn btn-white pull-left" href="{{ route('documentstandard.destroy', ['id' => $type->doc_id]) }}" title="">
                            <i class=" fa fa-trash-o" ></i>
                        </a>
                        <a class="btn btn-white pull-left" href="{{ route('documentstandard.show', ['id' => $type->doc_id]) }}" title="">
                            <i class=" fa fa-eye" ></i>
                        </a>
                    </td>

                </tr>
                @endforeach

            </tbody>
        </table>
    </div><!-- /.box-body -->
</div><!-- /.box -->
<a href="{{ URL::to('dashboard') }}" class="btn btn-primary"><i class="fa fa-arrow-circle-left">Back</i></a>
{!! Html::script("https://code.jquery.com/jquery-1.11.3.min.js") !!}
{!! Html::script("https://cdn.datatables.net/1.10.10/js/jquery.dataTables.min.js") !!}
{!! Html::script("https://cdn.datatables.net/buttons/1.1.0/js/dataTables.buttons.min.js") !!}
{!! Html::script("https://cdn.datatables.net/buttons/1.1.0/js/buttons.flash.min.js") !!}
{!! Html::script("https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js") !!}
{!! Html::script("https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js") !!}
{!! Html::script("https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js") !!}
{!! Html::script("https://cdn.datatables.net/buttons/1.1.0/js/buttons.html5.min.js") !!}
{!! Html::script("https://cdn.datatables.net/buttons/1.1.0/js/buttons.print.min.js") !!}


<!-- Highcharts plugin -->
{!! Html::script("/highcharts/js/highcharts.js") !!}
{!! Html::script("/highcharts/js/highcharts-3d.js") !!}
{!! Html::script("/highcharts/js/modules/data.js") !!}
{!! Html::script("/highcharts/js/modules/drilldown.js") !!}
{!! Html::script("/highcharts/js/highcharts-more.js") !!}
{!! Html::script("/highcharts/js/modules/exporting.js") !!}
<script>
    $(function() {

        ///////////////////
        $('#meeting_agenda').DataTable({
                        dom: 'lrftipB', 
                        buttons: [
                            'csv', 'excel', 'pdf', 'print'
                        ],
                        pageLength: 10,
                        initComplete: function() {
                            $('.dataTables_filter').css({
                                // 'float': 'right',
                                // 'margin-top': '20px',
                                // 'margin-right': '20px'
                            });
                            $('.dataTables_length ').css({
                                
                                // 'margin-top': '40px',
                                
                            });
                        }
                    });
    });
    document.addEventListener('DOMContentLoaded', function () {
        setTimeout(function () {
            var flashMessage = document.getElementById('flash-message');
            if (flashMessage) {
                flashMessage.style.display = 'none';
            }
        }, 2000); 
    });
    function confirm(){
        var x=confirm('are you sure you want to delete ');
        if(x)
        return true;
    else
    return false;
    }
</script>

@stop