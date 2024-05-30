@extends("base")

@section('sidebar')
@parent
<!--
<audio src="{{url('public/Labbaik_Allahumma_Labbaik.mp3')}}" autoplay loop>
    <p>If you are reading this, it is because your browser does not support the audio element.</p>
</audio>
-->


@endsection

@section('content')

    <div class="row">
        <div class="text-center">
            <div class="col-xs-3">
                <img src="{{url('public/img/nha_logo.png')}}" alt="National Highway Authority" style="width:52%; margin-bottom:10px;" />
            </div>
            <div class="col-xs-6">
                <!--<img src="{{url('public/img/bismillah.png')}}" alt="Bismillah" style="width:30%; margin-bottom:10px;" />
                <img src="{{url('public/img/Bacaan-Talbiyah.jpg')}}" alt="Talbiyah" style="width:100%; margin-bottom:10px;" /> -->
				<h1 class="text-center heading" style="font-size:34px; color:#2b6c80; text-decoration: underline;"><strong>NHA Cricket Tournament <?php echo date("Y"); ?></strong></h1>
                <h1 class="text-center heading" style="font-size:34px; color:#2b6c80; text-decoration: underline;"><strong>Team Contest Schedule </strong></h1>
                <h1 class="text-center heading" style="font-size:34px; color:#2b6c80; text-decoration: underline;"><strong>(Round - II)</strong></h1>
            </div>
            <div class="col-xs-3">
                <img src="{{url('public/img/cricket.jpg')}}" alt="cricket" style="width:80%;margin-top:15px;" />
            </div>
        </div>
      
    </div>
	


<div class="row">

    <div class="col-md-12">
        <div class="box  box-primary" style="margin-top:15px;">
            <div class="box-body">

                <div class="text-center col-md-12">

                    <div class="col-md-offset-4 col-md-4  hidden-print"><div id="sevenSegment_5" class="sevenSegment"></div></div>

                    

                    
                </div>

                 <table class="table table-striped bordered text-left">
						<thead>
							<th>Dates</th>
							<th colspan="2" >Matches</th>
							
						</thead>
                        
                        <tbody id="result_table_5">
						
						<tr>
							<td rowspan="2" style="vertical-align: middle;">16-07-2019</td>
							<td>Match 1 </td>
							<td><b>EF FALCONS</b> &nbsp;&nbsp; vs &nbsp;&nbsp; <b>IT ELEVEN</b></td>
						</tr>
						<tr>
							<td>Match 2 </td>
							<td><b>NHA SIXER</b> &nbsp;&nbsp; vs &nbsp;&nbsp; <b>CPF SUPER STAR</b></td>
						</tr>
						<hr/>
						<tr>
							<td rowspan="2" style="vertical-align: middle;">17-07-2019</td>
							<td>Match 3</td>
							<td><b>IT ELEVEN</b> &nbsp;&nbsp; vs &nbsp;&nbsp; <b>B&A TEAM</b></td>
						</tr>
						<tr>
							<td>Match 4 </td>
							<td><b>REVENUE TIGERS</b> &nbsp;&nbsp; vs &nbsp;&nbsp; <b>ENGINEER'S ELEVEN</b></td>
						</tr>
						<hr/>
						
						<tr>
							<td rowspan="2" style="vertical-align: middle;">18-07-2019</td>
							<td>Match 5</td>
							<td><b>NHA SIXER</b> &nbsp;&nbsp; vs &nbsp;&nbsp; <b>REVENUE TIGERS</b></td>
						</tr>
						<tr>
							<td>Match 6 </td>
							<td><b>EF FALCONS</b> &nbsp;&nbsp; vs &nbsp;&nbsp; <b>B&A TEAM</b></td>
						</tr>
						
						<tr>
							<td>19-07-2019</td>							
							<td>Match 7 </td>
							<td><b>CPF SUPER STAR</b> &nbsp;&nbsp; vs &nbsp;&nbsp; <b>ENGINEER'S ELEVEN</b></td>
						</tr>
						
						
						<tr>
						
							<td rowspan="2" style="vertical-align: middle;">20-07-2019</td>
							<td>Match 8</td>
							<td><b>CPF SUPER STAR</b> &nbsp;&nbsp; vs &nbsp;&nbsp; <b>REVENUE TIGERS</b></td>
						</tr>
						<tr>
							<td>Match 9 </td>
							<td><b>NHA SIXER</b> &nbsp;&nbsp; vs &nbsp;&nbsp; <b>ENGINEER'S ELEVEN</b></td>
						</tr>
						
						
					
					

                        </tbody>
                    </table>

            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div>
		<br/>
	<br/>
	<br/>
	<br/>

    <div class="row visible-print">
		<div class="col-xs-12 text-center">
            <div class="col-xs-3"><p style="border-top:2px solid;">AD (EF)</p>Member</div>
            
        </div>
			<br/>
	<br/>
	<br/>
	<br/>
	<br/>
	<br/>
        <div class="col-xs-12 text-center">
            <div class="col-xs-3"><p style="border-top:2px solid;">DD (Finance)</p>Member</div>
            <div class="col-xs-3"><p style="border-top:2px solid;">DD (B&T)</p>Member</div>
            <div class="col-xs-3"><p style="border-top:2px solid;">DD (Transport)</p>Member</div>
            <div class="col-xs-3"><p style="border-top:2px solid;">DD (Sports)</p>Member</div>
        </div>

        <p style="padding-top:10px; padding-bottom:10px;"></p>

        <div class="col-xs-12" style="margin-top:10%">
            <div class="col-xs-3  text-left">
                <p style="border-top:1px solid;">Director (Finance)</p>
                Chairman Comittee
            </div>

            <div class="col-xs-3 col-xs-offset-3 text-right pull-right">
                <p style="border-top:1px solid; font-size:18px;"><strong>Member (Admn)</strong></p>
            </div>
        </div>
    </div>

</div>


<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<script src="//ajax.aspnetcdn.com/ajax/knockout/knockout-2.2.1.js"></script>
<script src="{{url('public/js/sevenSeg.js')}}"></script>

<script>
   window.print();

</script>

<style>
.table>tbody>tr>td{
    border-top: 1px solid #969696 !important;
}
    td div {
        display: none;
    }
    .button{
        position:relative;
        display:inline-block;
        margin:20px;
    }

    .button a{
        color:white;
        font-family:Helvetica, sans-serif;
        font-weight:bold;
        font-size:25px;
        text-align: center;
        text-decoration:none;
        background-color: #ff1401;
        display:block;
        position:relative;
        padding:15px 30px;

        -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
        text-shadow: 0px 1px 0px #000;
        filter: dropshadow(color=#000, offx=0px, offy=1px);

        -webkit-box-shadow:inset 0 1px 0 #ff423a, 0 10px 0 #911b0f;
        -moz-box-shadow:inset 0 1px 0 #ff423a, 0 10px 0 #911b0f;
        box-shadow:inset 0 1px 0 #ff423a, 0 10px 0 #911b0f;

        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
    }

    .button a:active{
        top:10px;
        background-color:#F78900;

        -webkit-box-shadow:inset 0 1px 0 #ff423a, inset 0 -3px 0 #911b0f;
        -moz-box-shadow:inset 0 1px 0 #ff423a, inset 0 -3pxpx 0 #911b0f;
        box-shadow:inset 0 1px 0 #ff423a, inset 0 -3px 0 #911b0f;
    }

    .button:after{
        content:"";
        height:100%;
        width:100%;
        padding:4px;
        position: absolute;
        bottom:-15px;
        left:-4px;
        z-index:-1;
        background-color:#2B1800;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
    }
    p{margin-bottom:0px;}
    @media print {
        .table { font-size: 12px;}
        .heading { font-size: 18px !important;}
    }
</style>

@stop
