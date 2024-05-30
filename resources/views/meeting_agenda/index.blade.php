@extends("base")

@section('sidebar')
@parent
<h1 style="margin:10px 0">EIS Dashboard</h1>
@endsection

@section('content')

@if(!empty(Session::get('success')))
<div class="alert alert-success" role="alter">
    {{Session::get('success')}}
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

    }

    .small-box {
        height: 200px;
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    }

    .small-box>.small-box-footer:hover {
        background: #ff1010;
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

<div class="row">
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box bg-maroon-gradientt">
            <span class="info-box-icon bg-maroon-gradientt"><i class="ion-social-dropbox-outline"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Total no. of Meetings</span>
                <span class="info-box-number" id="tt_meeting">1</span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box bg-purple-gradientt">
            <span class="info-box-icon bg-purple-gradientt"><i class="ion-ios-people"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">EXB Meetings</span>
                <span class="info-box-number" id="tt_exb_meeting">4</span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box bg-green-gradientt">
            <span class="info-box-icon bg-green-gradientt"><i class="ion-ios-people-outline"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">HC Meetings</span>
                <span class="info-box-number" id="tt_hc_meeting">1</span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <div class=" col-md-3 col-sm-6 col-xs-12">
        <div class="info-box bg-red-gradientt">
            <span class="info-box-icon bg-red-gradientt"><i class="ion-filing"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Agendas</span>
                <span class="info-box-number" id="no_agendas">0</span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>

</div>
<!-- Pie Chart -->
<div class="row">
    <div class="col-lg-6 col-xs-12">
        <div class="small-box">
            <div class="inner">
                <span style="font-size: 22px;">Total</span>
                <h3 style="display: inline;padding-left: 212px;">0</h3>
                <br />

                <span style="font-size: 22px;color:green;">Uploaded</span>
                <h2 id="second" style="display: inline;padding-left: 170px;">0</h2>
                <br />
                <span style="font-size: 22px;color:red;">Remaining</span>
                <h2 id="third" style="display: inline;padding-left: 160px;">0</h2>
            </div>
            <div class="icon" id="meeting_chart">

            </div>
            <a class="small-box-footer" href="javascript:;">Meeting EDocuments<i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>

    <div class="col-lg-6 col-xs-12">
        <div class="small-box">
            <div class="inner">
                <span style="font-size: 22px;">Total</span>
                <h3 style="display: inline;padding-left: 175px;">0</h3>
                <br />

                <span style="font-size: 22px;color:green;">Uploaded</span>
                <h2 id="second" style="display: inline;padding-left: 132px;">0</h2>
                <br />
                <span style="font-size: 22px;color:red;">Remaining</span>
                <h2 id="third" style="display: inline;padding-left: 125px;">0</h2>
            </div>
            <div class="icon" id="agenda_chart">

            </div>
            <a class="small-box-footer" href="javascript:;">Agendas EDocuments <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>


<div class="box  box-primary">
    <div class="box-header">
        <h3 class="box-title">List of {{$page_title or ''}}</h3>
        <!-- <a href="{{ URL::to('progress_activity/create') }}" class="btn btn-primary btn-sm btn-success pull-right">Add Progress Activities</a> -->
    </div><!-- /.box-header -->
    <div class="box-body">
        <table class="table table-bordered table-striped" id="meeting_agenda">
            <thead class="bg-gray">
                <tr>
                    <th>#</th>
                    <th>Meeting Title</th>
                    <th>Meeting Type</th>
                    <th>Date</th>
                    <th>Agenda</th>
                    <th>Wing</th>
                    <th>Meeting EDoc</th>
                    <th>Agenda EDoc</th>

                </tr>
            </thead>
            <tbody>

                @foreach($meetingAgendas as $key => $agenda)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $agenda->meeting->meeting_no or '' }} {{ $agenda->meeting->meetingType->mt_title or '' }}</td>
                    <td> {{ $agenda->meeting->meetingType->mt_title or '' }} </td>
                    <td> <?php echo $agenda->meeting->meeting_date ?  date('d-m-Y', strtotime($agenda->meeting->meeting_date)) : '' ?> </td>
                    <td> {{ $agenda->ma_title or '' }} </td>

                    <td>
                        {{ $agenda->wing->action_title or '' }}
                    </td>

                    <td>
                        @if($agenda->meeting->meeting_edoc)
                        <a class="btn btn-white pull-left" href="{{ URL::to('public/meetings/'.$agenda->meeting->meeting_edoc) }}" title="{{$agenda->meeting->meeting_edoc}}">
                            <i class="fa  fa-file-pdf-o" style="color:#7e0b0b"></i>
                        </a>
                        @endif
                    </td>
                    <td>
                        @if($agenda->ma_edoc)
                        <a class="btn btn-white pull-left" href="{{ URL::to('public/agendas/'.$agenda->ma_edoc) }}" title="{{$agenda->ma_edoc}}">
                            <i class="fa  fa-file-pdf-o" style="color:#7e0b0b"></i>
                        </a>
                        @endif
                    </td>

                </tr>
                @endforeach

            </tbody>
        </table>
    </div><!-- /.box-body -->
</div><!-- /.box -->
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
            footerCallback: function(row, data, start, end, display) {
                var api = this.api();
                // Remove the formatting to get integer data for summation
                var intVal = function(i) {
                    return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
                };
                // Total over all pages
                total = api
                    .column(4)
                    .data()
                    .count();
                /* .reduce(function(a, b) {
                    return intVal(a) + intVal(b);
                }, 0); */

                let totalAgendas = api
                    .column(4, {
                        page: 'current'
                    })
                    .data()
                    .count();

                // Total over this page
                pageTotal1 = api
                    .column(1, {
                        page: 'current'
                    })
                    .data()
                    .unique()
                    .count();
                let totEXM = api
                    .column(1, {
                        page: 'current'
                    })
                    .data()
                    .unique()
                    .filter(function(value, index) {
                        return value.indexOf("NH Executive Board Meeting") >= 0;
                    })
                    .count();
                let totHC = api
                    .column(1, {
                        page: 'current'
                    })
                    .data()
                    .unique()
                    .filter(function(value, index) {
                        console.log(value)
                        return value.indexOf("NH Council") >= 0;
                    })
                    .count();
                /* .unique()
                .count(); */
                /* .reduce(function(a, b) {
                    return intVal(a) + intVal(b);
                }, 0); */
                console.log(`p2 - ${totHC}`);

                // Update footer
                $(api.column(1).footer()).html('$' + pageTotal1 + ' ( $' + total + ' total)');
                $("#meeting_chart").parent().find('.inner h3').html((totEXM));
                let meeting_doc = pageTotal1;
                let tt_meeting = 100;
                ////update pie chart values
                var tt_meeting_edoc = Math.round((meeting_doc / tt_meeting) * 100);
                var tt_meeting_rem = 100 - tt_meeting_edoc;
                /*  $("#meeting_chart").parent().find('.inner h2#second').html((pageTotal1)); */

                /*  if (tot_mutated < 0 || isNaN(tot_mutated))
                     tot_mutated = 0;
                 if (remain_mutated < 0 || isNaN(remain_mutated))
                     remain_mutated = 0; */
                if (tt_meeting_edoc < 0 || isNaN(tt_meeting_edoc))
                    tt_meeting_edoc = 0;
                if (tt_meeting_rem < 0 || isNaN(tt_meeting_rem))
                    tt_meeting_rem = 0;
                console.log("mr" + tt_meeting_rem);

                $('.info-box-content #tt_meeting').html((Math.round(meeting_doc)) + "&nbsp;<span style='font-size:22px;color:#0044cc'></span>");
                $('.info-box-content #tt_exb_meeting').html((Math.round(totEXM)) + "&nbsp;<span style='font-size:22px;color:#0044cc'></span>");
                $('.info-box-content #tt_hc_meeting').html((Math.round(totHC)) + "&nbsp;<span style='font-size:22px;color:#0044cc'></span>");
                $('.info-box-content #no_agendas').html((Math.round(totalAgendas)) + "&nbsp;<span style='font-size:22px;color:#0044cc'></span>");

                let meetingDocs = api
                    .column(6, {
                        page: 'current'
                    })
                    .data()
                    .filter(function(value, index, array) {

                        return array.indexOf(value) === index;
                    })
                    .count();
                let upMeetingDocs = api
                    .column(6, {
                        page: 'current'
                    })
                    .data()
                    .filter(function(value, index, array) {
                        let docVal = $(value).attr('href');
                        if (docVal) {
                            return array.indexOf(value) === index;
                        }
                        /* console.log('vv' + value);
                        //console.log('vhref' + docVal);
                        return docVal.length > 0; */
                    })
                    //.unique()
                    .count();
                let remMeetingDocs = meetingDocs - upMeetingDocs;
                let meetingDocPer = (upMeetingDocs / meetingDocs) * 100;
                let remMeetingDocsPer = (remMeetingDocs / meetingDocs) * 100;

                $("#meeting_chart").parent().find('.inner h3').html((meetingDocs));
                $("#meeting_chart").parent().find('.inner h2#second').html((Math.round(upMeetingDocs)) + "&nbsp;<span style='font-size:22px;'>(" + meetingDocPer.toFixed(2) + "%)</span>");
                $("#meeting_chart").parent().find('.inner h2#third').html((Math.round(remMeetingDocs)) + "&nbsp;<span style='font-size:22px;'>(" + remMeetingDocsPer.toFixed(2) + "%)</span>");

                let agendaDocs = api
                    .column(7, {
                        page: 'current'
                    })
                    .data()
                    .count();
                let upAgendaDocs = api
                    .column(7, {
                        page: 'current'
                    })
                    .data()
                    .filter(function(value, index) {
                        return value.length > 0;
                    })
                    .count();
                let remAgendaDocs = agendaDocs - upAgendaDocs;
                let agendaDocPer = (upAgendaDocs / agendaDocs) * 100;
                let remAgendaDocsPer = (remAgendaDocs / agendaDocs) * 100;
                $("#agenda_chart").parent().find('.inner h3').html((agendaDocs));
                $("#agenda_chart").parent().find('.inner h2#second').html((Math.round(upAgendaDocs)) + "&nbsp;<span style='font-size:22px;'>(" + agendaDocPer.toFixed(2) + "%)</span>");
                $("#agenda_chart").parent().find('.inner h2#third').html((Math.round(remAgendaDocs)) + "&nbsp;<span style='font-size:22px;'>(" + remAgendaDocsPer.toFixed(2) + "%)</span>");


                /////// Meeting Chart
                $('#meeting_chart').highcharts({
                    chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false,
                        type: 'pie',
                        margin: [0, 0, 0, 0],
                        spacingTop: 0,
                        spacingBottom: 0,
                        spacingLeft: 0,
                        spacingRight: 0,
                        backgroundColor: 'transparent'
                    },
                    exporting: {
                        enabled: false
                    },
                    title: {
                        text: ''
                    },
                    tooltip: {
                        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                    },
                    plotOptions: {
                        pie: {
                            colors: [
                                '#11d947',
                                '#f24b4b',
                            ],
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: false,
                                format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                                style: {
                                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                                }
                            }
                        }
                    },
                    series: [{
                        name: 'Total Meeting EDocuments',
                        colorByPoint: true,
                        data: [{
                            name: 'Meeting EDoc',
                            y: upMeetingDocs
                        }, {
                            name: 'Meeting EDoc Remaining',
                            y: remMeetingDocs
                        }]
                    }]
                });

                /// Agendas Chart
                $('#agenda_chart').highcharts({
                    chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false,
                        type: 'pie',
                        margin: [0, 0, 0, 0],
                        spacingTop: 0,
                        spacingBottom: 0,
                        spacingLeft: 0,
                        spacingRight: 0,
                        backgroundColor: 'transparent'
                    },
                    exporting: {
                        enabled: false
                    },
                    title: {
                        text: ''
                    },
                    tooltip: {
                        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                    },
                    plotOptions: {
                        pie: {
                            colors: [
                                '#11d947',
                                '#f24b4b',
                            ],
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: false,
                                format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                                style: {
                                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                                }
                            }
                        }
                    },
                    series: [{
                        name: 'Total Land',
                        colorByPoint: true,
                        data: [{
                            name: 'EDocument',
                            y: upAgendaDocs
                        }, {
                            name: 'EDocument Remaining',
                            y: remAgendaDocs
                        }]
                    }]
                });
            }
        });
    });
</script>

@stop