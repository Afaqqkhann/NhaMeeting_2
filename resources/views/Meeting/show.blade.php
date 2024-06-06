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
    .btn-success-light{
        color: #141313;
        background-color: #e7ebee; /* Light gray background color */
        border-color: #e7ebee;
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
    .nav-tabs-custom>.nav-tabs>li {
		border: solid 1px #ccc;
		background-color: #E6E6E4;
       
	}

	.nav-tabs-custom>.nav-tabs>.active {
		border-bottom: none;
	}

	.nav-tabs-custom>.nav-tabs {
		border-bottom: solid 1px #ccc;
	}
    .box.box-primary {
    background-color: white; /* Set background color to white */
}
</style>
{!! Html::style('https://cdn.datatables.net/buttons/1.1.0/css/buttons.dataTables.min.css') !!}




<div class="box  box-primary">
    {{-- <div class="box-header">
        <h3 class="box-title">List of {{$page_title or ''}}</h3>
       <a href="{{ URL::to('meetingcreate') }}" class="btn btn-primary btn-sm btn-success pull-right">Add </a> 
    </div><!-- /.box-header --> --}}
    <div class="box-body]">
        <table class="table table-bordered table-striped" id="meeting_agenda">
            <thead class="bg-gray">
                <tr>
                    <th>Meeting Id</th>
                    <th>Meeting No</th>
                    <th>Meeting date</th>
                    <th>Meeting Type</th>

                </tr>
            </thead>
            <tbody>

               
                <tr>
                   
                    <td>{{ $meeting->meeting_id or '' }} </td>
                    
                    <td> {{ $meeting->meeting_no or '' }} </td>
                    <td> <?php echo $meeting->meeting_date ?  date('d-m-Y', strtotime($meeting->meeting_date)) : '' ?> </td>
                    <td> {{ $meeting->meetingType->mt_title or '' }} </td>

                   

                   
                    

                </tr>
             

            </tbody>
        </table>
        
        
    </div><!-- /.box-body -->
   
    
</div><!-- /.box -->
<div class="nav-tabs-custom">
            <ul class="nav nav-tabs pull-right" id="myTabs" role="tablist">
                <li class="">
                    <a class="nav-link active show-agendas" data-meeting-id="{{ $meeting->meeting_id }}" data-toggle="tab" href="#agendas" aria-controls="agendas" aria-selected="true" >MeetingAgenda</a>
                </li>
                <li class="">
                    <a class="nav-link show-documents" data-meeting-id="{{ $meeting->meeting_id }}" data-toggle="tab" href="#documents" aria-controls="documents" aria-selected="false">MeetingDocument</a>
                </li>
            </ul>
        
                <div id="agenda-container"  style="background-color: white"></div>
                <div id="doc-container" style="background-color: white"></div>
</div>
<a href="{{ URL::to('meeting') }}" class="btn btn-primary"><i class="fa fa-arrow-circle-left">Back</i></a>
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
  $(document).ready(function() {
    var agendaVisible = false;
    
    $('.show-agendas').click(function(e) {
        e.preventDefault();
        var meetingId = $(this).data('meeting-id');
        var $agendaContainer = $('#agenda-container');
        var $docContainer = $('#doc-container');

        if (agendaVisible) {
            $agendaContainer.hide();
            agendaVisible = false;
        } else {
            $.ajax({
                url: '/agenda/show/' + meetingId,
                type: 'GET',
                success: function(data) {
                    var agendasHtml = '<div style="text-align: right; margin-bottom: 10px;">';
                    agendasHtml += '<a href="{{route('createmeetingagenda.create')}}" class="btn btn-primary " style=" margin-top: 10px;"><i class="fa fa-user-plus "></i>Add-Agendas</a>';
                    agendasHtml += '</div>';
                    agendasHtml += '<table id="agendas-table" class="display table table-bordered">';
                    agendasHtml += '<thead style="background-color: #D1D1D1;"><tr><th>Wing</th><th>Agenda Title</th><th>EDoc</th><th>Action</th></tr></thead>';
                    agendasHtml += '<tbody>';
                    data.forEach(function(agenda) {
                    var showRoute="{{ route('agendas.show_2', ['id' => ':agendaId']) }}".replace(':agendaId', agenda.ma_id);
                    var editRoute = "{{ route('editmeetingagenda.edit', ['id' => ':agendaId']) }}".replace(':agendaId', agenda.ma_id);
                    var deleteRoute = "{{ route('deletemeetingagenda.delete', ['id' => ':agendaId']) }}".replace(':agendaId', agenda.ma_id);
                    agendasHtml += '<tr>';
                    agendasHtml += '<td>' + (agenda.wing.wing_head || '') + '</td>';
                    agendasHtml += '<td>' + (agenda.ma_title || '') + '</td>';
                    agendasHtml += '<td>';
                    if (agenda.ma_edoc) {
                    var baseUrl = '{{ URL::to('/') }}'; // base URL, assuming it's provided by the server-side templating engine
                    agendasHtml += '<a class="btn btn-white pull-left" href="' + baseUrl + '/public/agendas/' + agenda.ma_edoc + '" title="'+ agenda.ma_edoc +'">';
                    agendasHtml += '<i class="fa fa-file-pdf-o" style="color:#7e0b0b"></i>';
                    agendasHtml += '</a>';
                    }
                    agendasHtml += '</td>';
                    agendasHtml += '<td>';
                    agendasHtml += '<a href="' + editRoute + '" class="btn btn-white"><i class="fa fa-edit"></i></a>';
                    agendasHtml += '<a href="' + deleteRoute + '" class="btn btn-white delete-button"><i class="fa fa-trash-o"></i></a>';
                    agendasHtml += '<a href="' + showRoute+ '" class="btn" btn-white><i class="fa fa-eye"></i></a>';
                    agendasHtml += '</td></tr>';
    });
                    agendasHtml += '</tbody></table>';

                    $agendaContainer.html(agendasHtml);
                    $agendaContainer.show();
                    // agendaVisible = true;

                    
                    $('#agendas-table').DataTable({
                        dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" + 
                        "<'row'<'col-sm-12'tr>>" + 
                        "<'row'<'col-sm-12 col-md-6'B><'col-sm-12 col-md-6'p>>", 
                        buttons: [
                            'csv', 'excel', 'pdf', 'print'
                        ],
                        pageLength: 5,
                        initComplete: function() {
                            $('.dataTables_filter').css({
                                // 'float': 'right',
                                'margin-top': '20px',
                                'margin-right': '20px'
                            });
                            $('.dataTables_length ').css({
                                // 'float': 'right',
                                'margin-top': '40px',
                                // 'margin-right': '20px'
                            });
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }
        
        $docContainer.hide();
    });

    $('.show-documents').click(function(e) {
        var docVisible = false;
        e.preventDefault();
        var meetingId = $(this).data('meeting-id');
        var $docContainer = $('#doc-container');
        var $agendaContainer = $('#agenda-container');

        if (docVisible) {
            $docContainer.hide();
            docVisible = false;
        } else {
            $.ajax({
                url: '/doc/show/' + meetingId,
                type: 'GET',
                success: function(data) {
                    var docHtml = '<div style="text-align: right; margin-bottom: 10px;">';
                    docHtml += '<a href="{{route('createmeetingdocument.create')}}" class="btn btn-primary " style="margin-top: 10px;"><i class="fa fa-user-plus "></i>Add-Document</a>';
                    docHtml += '</div>';
                    docHtml += '<table class="table table-bordered" id="doc-table">';
                    docHtml += '<thead style="background-color: #D1D1D1;"><tr><th>Doc</th><th>Meeting-Doc-Title</th><th>EDoc</th><th>Action</th></tr></thead>';
                    docHtml += '<tbody>';
                    data.forEach(function(doc) {
                    var editRoute1 = "{{ route('meetingdocedit.edit', ['id' => ':agendaId']) }}".replace(':agendaId', doc.md_id);
                    var showRoute1 = "{{ route('doc.show', ['id' => ':agendaId']) }}".replace(':agendaId', doc.md_id);
                    var deleteRoute1 = "{{ route('meetingdocdelete.destroy', ['id' => ':agendaId']) }}".replace(':agendaId', doc.md_id);
                    docHtml += '<tr>';
                    docHtml += '<td>' + doc.doctsandard.doc_title + '</td>';
                    docHtml += '<td>' + doc.md_title + '</td>';
                    docHtml += '<td>';
                    if (doc.md_edoc) {
                    var baseUrl = '{{ URL::to('/') }}'; 
                    docHtml += '<a class="btn btn-white " href="' + baseUrl + '/public/Meeting-Document/' + doc.md_edoc + '" title="'+ doc.md_edoc +'">';
                    docHtml += '<i class="fa fa-file-pdf-o" style="color:#7e0b0b"></i>';
                    docHtml += '</a>';
                    }
                    docHtml += '</td>';
                    docHtml += '<td>';
                    docHtml += '<a href="' + editRoute1 + '" class="btn btn-white"><i class="fa fa-edit"></i></a>';
                    docHtml += '<a href="' + deleteRoute1 + '" class="btn btn-white delete-button"><i class="fa fa-trash-o"></i></a>';
                    docHtml += '<a href="' + showRoute1 + '" class="btn btn-white"><i class="fa fa-eye"></i></a>';
                    docHtml += '</td>';
                    docHtml += '</tr>';
                    });
                    docHtml += '</tbody></table>';

                    $docContainer.html(docHtml);
                    $docContainer.show();

                    
                    $('#doc-table').DataTable({
                        dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" + 
                        "<'row'<'col-sm-12'tr>>" + 
                        "<'row'<'col-sm-12 col-md-6'B><'col-sm-12 col-md-6'p>>", 
                        buttons: [
                            'csv', 'excel', 'pdf', 'print'
                        ],
                        pageLength: 5,
                        initComplete: function() {
                            $('.dataTables_filter').css({
                                // 'float': 'right',
                                'margin-right': '20px',
                                'margin-top': '20px',
                            });
                            $('.dataTables_length ').css({
                                // 'float': 'right',
                                'margin-top': '50px',
                                // 'margin-right': '20px'
                            });
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }
        
        $agendaContainer.hide();
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
    document.addEventListener('DOMContentLoaded', function() {
    var deleteButtons = document.querySelectorAll('.delete-button');

    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(event) {
            event.preventDefault(); 
            var userConfirmed = confirm('Are you sure you want to delete this item?');
            if (userConfirmed) {
                window.location.href = button.href; 
            }

            
        });
    });
    
});

</script>

@stop