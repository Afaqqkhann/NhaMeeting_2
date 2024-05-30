@extends("base")

@section('sidebar')
@parent

<audio src="{{url('public/Labbaik_Allahumma_Labbaik.mp3')}}" autoplay loop>
    <p>If you are reading this, it is because your browser does not support the audio element.</p>
</audio>


@endsection

@section('content')


<div class="row">
    <div class="col-xs-12 text-center">
        <div class="col-xs-3">
            <img src="{{url('public/img/kaaba-5.jpg')}}" alt="Ka'bah" style="width:80%;margin-top:15px;" />
        </div>
        <div class="col-xs-6">
            <img src="{{url('public/img/bismillah.png')}}" alt="Bismillah" style="width:30%; margin-bottom:10px;" />
            <img src="{{url('public/img/Bacaan-Talbiyah.jpg')}}" alt="Talbiyah" style="width:100%; margin-bottom:10px;" />
            <img src="{{url('public/img/nha_logo.png')}}" lt="NHA" style="width:9%; " />

            <h1 class="text-center heading" style="padding-left: 10px;vertical-align:middle;display:inline;font-size:30px; color:#137a03;"> <strong>8<sup>th</sup> Hajj Balloting 2024</strong></h1>
        </div>

        <div class="col-xs-3">
            <img src="{{url('public/img/madina-5.jpg')}}" alt="National Highway Authority" style="width:80%;margin-top:15px;" />
        </div>
    </div>
    <div class="col-xs-12 text-center" style="background-color:white; padding-top:10px; font-weight:bold;font-size:18px;">
        <p class="col-xs-4 text-left">Category "B" (BS 17 & above)</p>
        {{--<p class="text-right col-xs-6"><strong>Eligible Employees = 575</strong></p>--}}
        <p class="text-right col-xs-3"><strong>Eligible Employees = {{$elgEmp[0]->hajj_limit}}</strong></p>
        <p class="text-right col-xs-3"><strong>To Be Nominated = {{$nomEmp[0]->hajj_limit}}</strong></p>
        <p class="text-right col-xs-2"><strong>Reserved = {{$resEmp[0]->hajj_limit}}</strong></p>
    </div>
</div>

<div class="row">

    <div class="col-md-12">
        <div class="box  box-primary" style="margin-top:15px;">
            <div class="box-body">

                <div class="text-center col-md-12">

                    <div class="col-md-offset-4 col-md-4 hidden-print">
                        <div id="sevenSegment_2" class="sevenSegment"></div>
                    </div>

                    <div class="col-md-4 hidden-print">
                        <div class="col-md-12" style="margin:20px; margin-bottom:0px;">
                            <button class="btn btn-lg btn-primary" id="start_draw" onclick="draw_result(2)"> Start Draw </button>
                        </div>
                        <div class="col-md-12" style="margin:20px;">
                            <button class="btn btn-lg btn-success selection_btn">Selection</button>
                        </div>
                    </div>

                    <table class="table table-striped bordered text-left">
                        <tr>
                            <th>#</th>
                            <th>Emp ID</th>
                            <th>Name</th>
                            <th>F.Name</th>
                            <th>Designation (BS)</th>
                            <th>CNIC</th>
                            <th>Station</th>
                            <th>N/R</th>
                        </tr>
                        <tbody id="result_table_2">

                        </tbody>
                    </table>
                </div>

                <div class="col-md-12 text-right">
                    <button onclick="window.print();" id="print_btn" style="display:none;" class="hidden-print btn btn-lg btn-primary">Print</button>
                </div>

            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div>

    <div class="row visible-print" style="margin-top:80px;">
        <div class="col-xs-12 text-center">
            <div class="col-xs-3">
                <p style="border-top:2px solid;">DD (Welfare-II)</p>Secretary
            </div>
            <div class="col-xs-3">
                <p style="border-top:2px solid;">Dir (MIS)</p>Member
            </div>
            <div class="col-xs-3">
                <p style="border-top:2px solid;">GM (Audit)</p>Member
            </div>
            <div class="col-xs-3">
                <p style="border-top:2px solid;">Dir (Accounts)</p>Member
            </div>
        </div>

        <div class="gap hidden-print"></div>

        <div class="col-xs-12" style="margin-top:10%">
            <div class="col-xs-3  text-left">
                <p style="border-top:1px solid;">GM (Estab)</p>
                Chairman Comittee
            </div>
            <div class="col-xs-4 text-left">

                <p style="border-top:1px solid;font-size:18px;"><strong>Arshad Majeed Mohmand</strong></p>
                Chairman NHA
            </div>

            <div class="col-xs-5 text-right pull-right">

                <p style="border-top:1px solid; font-size:18px;"><strong>Shahid Ashraf Tarar</strong></p>
                Federal Minister for Communications
            </div>
        </div>
    </div>
</div>


<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<script src="//ajax.aspnetcdn.com/ajax/knockout/knockout-2.2.1.js"></script>
<script src="{{url('public/js/sevenSeg.js')}}"></script>

<script>
    var interval;

    function draw_result(id) {

        var tmp = 0;
        interval = setInterval(function() {
            tmp = randomIntFromInterval(1, 4600);
            $("#sevenSegment_" + id).sevenSeg({
                value: tmp
            });
        }, 100);

        $("#start_draw").prop('disabled', 'disabled');
        $("#sevenSegment_" + id).show('slow');
        $.ajax({
            url: '{{url('/ajax_hajj_draw/')}}/' + id,
            success: function(data) {
                var html = '';
                if (data.success) {
                    var shortlist = short_reserve = '';
                    $(data.data).each(function(i, v) {

                        if (v.short_list == 1) {
                            shortlist = ' style="background-color:#f7be60;font-weight:bold;color:#000000;"';
                            short_reserve = 'Nominated';
                        }
                        if (v.short_list == 2) {
                            shortlist = ' style="background-color:#77fb8d;font-weight:bold;color:#000000;"';
                            short_reserve = 'Reserved';
                        }
                        html += '<tr' + shortlist + '><td><div>' + (i + 1) + '</div></td><td><div class="empID">' + v.emp_id + '</div></td><td><div>' + v.emp_name + '</div></td><td><div>' + v.f_name + '</div></td><td><div>' + v.designation + ' (' + v.bs + ')</div></td><td><div>' + v.cnic + '</div></td><td><div>' + v.place_of_posting + '</div></td><td><div>' + short_reserve + '</div></td></tr>';
                    });

                    //setTimeout(stop_loop, 4000);
                    $("#result_table_" + id).html(html);
                    //show_result();
                }
            }
        });
    }

    function stop_loop() {
        clearInterval(interval);
    }

    function show_result() {
        $('tr').each(function(i, v) {
            if ($('td div', this).is(':hidden')) {
                $('td div', this).delay((i + 1) * 100).fadeIn('slow');
                console.log($("#result_table_2 tr").length);
                if ($("#result_table_2 tr").length == i) {
                    setTimeout(stop_loop(), 2000);
                    var emp_id = $(this).find('.empID').html();
                    $(".sevenSegment").sevenSeg({
                        value: emp_id
                    });
                    $("#print_btn").show();
                }
                return false;
            }
        });
    }

    $(".selection_btn").click(function() {
        show_result();
    });

    function randomIntFromInterval(min, max) {
        return Math.floor(Math.random() * (max - min + 1) + min);
    }

    $(".sevenSegment").sevenSeg({
        digits: 4
    });
    var iArrayValue = 0;

    setInterval(function() {
        $("#sevenSegment").sevenSeg({
            value: iArrayValue
        });
        if (++iArrayValue > 34000) {
            iArrayValue = 0;
        }
    }, 50);
</script>

<style>
    td div {
        display: none;
    }

    .button {
        position: relative;
        display: inline-block;
        margin: 20px;
    }

    .button a {
        color: white;
        font-family: Helvetica, sans-serif;
        font-weight: bold;
        font-size: 25px;
        text-align: center;
        text-decoration: none;
        background-color: #ff1401;
        display: block;
        position: relative;
        padding: 15px 30px;

        -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
        text-shadow: 0px 1px 0px #000;
        filter: dropshadow(color=#000, offx=0px, offy=1px);

        -webkit-box-shadow: inset 0 1px 0 #ff423a, 0 10px 0 #911b0f;
        -moz-box-shadow: inset 0 1px 0 #ff423a, 0 10px 0 #911b0f;
        box-shadow: inset 0 1px 0 #ff423a, 0 10px 0 #911b0f;

        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
    }

    .button a:active {
        top: 10px;
        background-color: #F78900;

        -webkit-box-shadow: inset 0 1px 0 #ff423a, inset 0 -3px 0 #911b0f;
        -moz-box-shadow: inset 0 1px 0 #ff423a, inset 0 -3pxpx 0 #911b0f;
        box-shadow: inset 0 1px 0 #ff423a, inset 0 -3px 0 #911b0f;
    }

    .button:after {
        content: "";
        height: 100%;
        width: 100%;
        padding: 4px;
        position: absolute;
        bottom: -15px;
        left: -4px;
        z-index: -1;
        background-color: #2B1800;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
    }

    p {
        margin-bottom: 0px;
    }

    @media print {
        .table {
            font-size: 12px;
        }

        .heading {
            font-size: 18px !important;
        }
    }
</style>

@stop