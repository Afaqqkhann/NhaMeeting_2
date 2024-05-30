@extends("base")

@section('sidebar')
@parent
    <h1>Dashboard</h1>
@endsection


@section('content')

        <!-- Main content -->
        <section class="content">
          <!-- Small boxes (Stat box) -->
          <div class="row">
            <div class="col-lg-3 col-xs-6">
              <!-- small box -->
              <div class="small-box bg-aqua">
                <div class="inner">
                  <h3>1170/1053</h3>
                  <p>NHA Strength</p>
                </div>
                <div class="icon">
                  <i class="ion ion-ios-people"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
              </div>
            </div><!-- ./col -->
            <div class="col-lg-3 col-xs-6">
              <!-- small box -->
              <div class="small-box bg-green">
                <div class="inner">
                  <h3>140/200<!--<sup style="font-size: 20px">%</sup>--></h3>
                  <p>Posting / Transfer</p>
                </div>
                <div class="icon">
                  <i class="ion ion-paper-airplane"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
              </div>
            </div><!-- ./col -->
            <div class="col-lg-3 col-xs-6">
              <!-- small box -->
              <div class="small-box bg-yellow">
                <div class="inner">
                  <h3>80 <sup style="font-size: 10px">In Country</sup> / 70 <sup style="font-size: 10px">Foreign</sup></h3>
                  <p>Trainings</p>
                </div>
                <div class="icon">
                  <i class="ion ion-person-add"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
              </div>
            </div><!-- ./col -->
            <div class="col-lg-3 col-xs-6">
              <!-- small box -->
              <div class="small-box bg-red">
                <div class="inner">
                  <h3>27,687,61</h3>
                  <p>Medical</p>
                </div>
                <div class="icon">
                  <i class="ion ion-medkit"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
              </div>
            </div><!-- ./col -->
          </div><!-- /.row -->
		  
		 <!-- Charts row -->
		 <div class="row">
			<div class="col-lg-12">
			  
			  <div class="box box-success">
                <div class="box-header">
                  <i class="fa fa-bar-chart-o"></i>
                  <h3 class="box-title">Strength Chart</h3>
                  <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                  </div>
                </div><!-- /.box-header -->
                <div class="box-body no-padding">
				  <div class="row col-xs-12">
					  <div class="chart" id="strength-chart" style="margin:0 auto; height: 300px;"></div>
				  </div><!-- /.row -->
			     </div><!-- /.box-body -->
			   </div><!-- /.box -->
			   
			</div><!-- /.col-lg-12 -->

		 </div><!-- /.row -->
		 
		 <!-- Charts row -->
		 <div class="row">
			<div class="col-lg-12">
			  
			  <div class="box box-success">
                <div class="box-header">
                  <i class="fa fa-line-chart"></i>
                  <h3 class="box-title">Advertisement Chart</h3>
                  <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                  </div>
                </div><!-- /.box-header -->
                <div class="box-body no-padding">
				  <div class="row col-xs-12">
					  <div class="chart" id="advertisement_chart" style="margin:0 auto; height: 300px;"></div>
				  </div><!-- /.row -->
			     </div><!-- /.box-body -->
			   </div><!-- /.box -->
			   
			</div><!-- /.col-lg-12 -->

		 </div><!-- /.row -->
		 
          <!-- Main row -->
          <div class="row">
            <!-- Left col -->
            <section class="col-lg-6 connectedSortable">
              
              <div class="box box-success">
                <div class="box-header with-border">
                  <i class="fa fa-bar-chart-o"></i>
                  <h3 class="box-title">Strength Chart</h3>
                  <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                  </div>
                </div><!-- /.box-header -->
                <div class="box-body no-padding">
                  <div class="row col-sm-12">
                      <div class="chart" id="strength-chart" style="margin:0 auto; height: 300px;"></div>
                  </div><!-- /.row -->
               </div><!-- /.box-body -->
            </div><!-- /.box -->

              <!-- Chat box -->
              <div class="box box-success">
                <div class="box-header">
                  <i class="fa fa-comments-o"></i>
                  <h3 class="box-title">Chat</h3>
                  <div class="box-tools pull-right" data-toggle="tooltip" title="Status">
                    <div class="btn-group" data-toggle="btn-toggle" >
                      <button type="button" class="btn btn-default btn-sm active"><i class="fa fa-square text-green"></i></button>
                      <button type="button" class="btn btn-default btn-sm"><i class="fa fa-square text-red"></i></button>
                    </div>
                  </div>
                </div>
                <div class="box-body chat" id="chat-box">
                  <!-- chat item -->
                  <div class="item">
                    <img src="{{ asset("public/css/dist/img/user4-128x128.jpg") }}" alt="user image" class="online">
                    <p class="message">
                      <a href="#" class="name">
                        <small class="text-muted pull-right"><i class="fa fa-clock-o"></i> 2:15</small>
                        Sadaqat
                      </a>
                      I would like to meet you to discuss the latest news about
                      the arrival of the new employees. They say it is going to be one the
                      best chance to ...
                    </p>
                    <div class="attachment">
                      <h4>Attachments:</h4>
                      <p class="filename">
                        Theme-thumbnail-image.jpg
                      </p>
                      <div class="pull-right">
                        <button class="btn btn-primary btn-sm btn-flat">Open</button>
                      </div>
                    </div><!-- /.attachment -->
                  </div><!-- /.item -->
                  <!-- chat item -->
                  <div class="item">
                    <img src="{{ asset("public/css/dist/img/user3-128x128.jpg") }}" alt="user image" class="offline">
                    <p class="message">
                      <a href="#" class="name">
                        <small class="text-muted pull-right"><i class="fa fa-clock-o"></i> 5:15</small>
                        Pervaiz
                      </a>
                      I would like to meet you to discuss the latest news about
                      the arrival of the new mail. They say it is going to be one the
                      best package
                    </p>
                  </div><!-- /.item -->
                  <!-- chat item -->
                  <div class="item">
                    <img src="{{ asset("public/css/dist/img/user2-160x160.jpg") }}" alt="user image" class="offline">
                    <p class="message">
                      <a href="#" class="name">
                        <small class="text-muted pull-right"><i class="fa fa-clock-o"></i> 5:30</small>
                        Sajid Awan
                      </a>
                      I would like to meet you to discuss the latest news about
                      the arrival of the new technology. They say it is going to be one the
                      best in the market
                    </p>
                  </div><!-- /.item -->
                </div><!-- /.chat -->
                <div class="box-footer">
                  <div class="input-group">
                    <input class="form-control" placeholder="Type message...">
                    <div class="input-group-btn">
                      <button class="btn btn-success"><i class="fa fa-plus"></i></button>
                    </div>
                  </div>
                </div>
              </div><!-- /.box (chat box) -->

              <!-- TO DO List -->
              <div class="box box-primary">
                <div class="box-header">
                  <i class="ion ion-clipboard"></i>
                  <h3 class="box-title">To Do List</h3>
                  <div class="box-tools pull-right">
                    <ul class="pagination pagination-sm inline">
                      <li><a href="#">&laquo;</a></li>
                      <li><a href="#">1</a></li>
                      <li><a href="#">2</a></li>
                      <li><a href="#">3</a></li>
                      <li><a href="#">&raquo;</a></li>
                    </ul>
                  </div>
                </div><!-- /.box-header -->
                <div class="box-body">
                  <ul class="todo-list">
                    <li>
                      <!-- drag handle -->
                      <span class="handle">
                        <i class="fa fa-ellipsis-v"></i>
                        <i class="fa fa-ellipsis-v"></i>
                      </span>
                      <!-- checkbox -->
                      <input type="checkbox" value="" name="">
                      <!-- todo text -->
                      <span class="text">Design a nice theme</span>
                      <!-- Emphasis label -->
                      <small class="label label-danger"><i class="fa fa-clock-o"></i> 2 mins</small>
                      <!-- General tools such as edit or delete-->
                      <div class="tools">
                        <i class="fa fa-edit"></i>
                        <i class="fa fa-trash-o"></i>
                      </div>
                    </li>
                    <li>
                      <span class="handle">
                        <i class="fa fa-ellipsis-v"></i>
                        <i class="fa fa-ellipsis-v"></i>
                      </span>
                      <input type="checkbox" value="" name="">
                      <span class="text">Make the theme responsive</span>
                      <small class="label label-info"><i class="fa fa-clock-o"></i> 4 hours</small>
                      <div class="tools">
                        <i class="fa fa-edit"></i>
                        <i class="fa fa-trash-o"></i>
                      </div>
                    </li>
                    <li>
                      <span class="handle">
                        <i class="fa fa-ellipsis-v"></i>
                        <i class="fa fa-ellipsis-v"></i>
                      </span>
                      <input type="checkbox" value="" name="">
                      <span class="text">Let theme shine like a star</span>
                      <small class="label label-warning"><i class="fa fa-clock-o"></i> 1 day</small>
                      <div class="tools">
                        <i class="fa fa-edit"></i>
                        <i class="fa fa-trash-o"></i>
                      </div>
                    </li>
                    <li>
                      <span class="handle">
                        <i class="fa fa-ellipsis-v"></i>
                        <i class="fa fa-ellipsis-v"></i>
                      </span>
                      <input type="checkbox" value="" name="">
                      <span class="text">Let theme shine like a star</span>
                      <small class="label label-success"><i class="fa fa-clock-o"></i> 3 days</small>
                      <div class="tools">
                        <i class="fa fa-edit"></i>
                        <i class="fa fa-trash-o"></i>
                      </div>
                    </li>
                    <li>
                      <span class="handle">
                        <i class="fa fa-ellipsis-v"></i>
                        <i class="fa fa-ellipsis-v"></i>
                      </span>
                      <input type="checkbox" value="" name="">
                      <span class="text">Check your messages and notifications</span>
                      <small class="label label-primary"><i class="fa fa-clock-o"></i> 1 week</small>
                      <div class="tools">
                        <i class="fa fa-edit"></i>
                        <i class="fa fa-trash-o"></i>
                      </div>
                    </li>
                    <li>
                      <span class="handle">
                        <i class="fa fa-ellipsis-v"></i>
                        <i class="fa fa-ellipsis-v"></i>
                      </span>
                      <input type="checkbox" value="" name="">
                      <span class="text">Let theme shine like a star</span>
                      <small class="label label-default"><i class="fa fa-clock-o"></i> 1 month</small>
                      <div class="tools">
                        <i class="fa fa-edit"></i>
                        <i class="fa fa-trash-o"></i>
                      </div>
                    </li>
                  </ul>
                </div><!-- /.box-body -->
                <div class="box-footer clearfix no-border">
                  <button class="btn btn-default pull-right"><i class="fa fa-plus"></i> Add item</button>
                </div>
              </div><!-- /.box -->

              <!-- quick email widget -->
              <div class="box box-info">
                <div class="box-header">
                  <i class="fa fa-envelope"></i>
                  <h3 class="box-title">Quick Email</h3>
                  <!-- tools box -->
                  <div class="pull-right box-tools">
                    <button class="btn btn-info btn-sm" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
                  </div><!-- /. tools -->
                </div>
                <div class="box-body">
                  <form action="#" method="post">
                    <div class="form-group">
                      <input type="email" class="form-control" name="emailto" placeholder="Email to:">
                    </div>
                    <div class="form-group">
                      <input type="text" class="form-control" name="subject" placeholder="Subject">
                    </div>
                    <div>
                      <textarea class="textarea" placeholder="Message" style="width: 100%; height: 125px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"></textarea>
                    </div>
                  </form>
                </div>
                <div class="box-footer clearfix">
                  <button class="pull-right btn btn-default" id="sendEmail">Send <i class="fa fa-arrow-circle-right"></i></button>
                </div>
              </div>

            </section><!-- /.Left col -->
            <!-- right col (We are only adding the ID to make the widgets sortable)-->
            <section class="col-lg-6 connectedSortable">
                <div class="box box-success">
                <div class="box-header with-border">
                  <h3 class="box-title">Biometrics Report</h3>
                  <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                  </div>
                </div><!-- /.box-header -->
                <div class="box-body no-padding">
                <div class="row">
                  <div class="col-md-6">
                    <div class="info-box bg-yellow">
                    <span class="info-box-icon"><i class="ion ion-calendar"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Total Absent Average</span>
                      <span class="info-box-number">2%</span>
                      <div class="progress">
                      <div class="progress-bar" style="width: 20%"></div>
                      </div>
                      <span class="progress-description">
                      20% Increase in 90 Days
                      </span>
                    </div><!-- /.info-box-content -->
                    </div><!-- /.info-box -->
                  </div><!-- /.col-md-6 -->
                  
                  <div class="col-md-6">
                    <div class="info-box bg-red">
                    <span class="info-box-icon"><i class="ion ion-clock"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Late Comers Average</span>
                      <span class="info-box-number">11%</span>
                      <div class="progress">
                      <div class="progress-bar" style="width: 43%"></div>
                      </div>
                      <span class="progress-description">
                      43% Increase in 90 Days
                      </span>
                    </div><!-- /.info-box-content -->
                    </div><!-- /.info-box -->
                  </div><!-- /.col-md-6 -->
                </div><!-- /.row -->
                 </div><!-- /.box-body -->
              </div><!-- /.box -->

              <!-- solid sales graph -->
              <div class="box box-solid bg-teal-gradient">
                <div class="box-header">
                  <i class="fa fa-th"></i>
                  <h3 class="box-title">Sales Graph</h3>
                  <div class="box-tools pull-right">
                    <button class="btn bg-teal btn-sm" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <button class="btn bg-teal btn-sm" data-widget="remove"><i class="fa fa-times"></i></button>
                  </div>
                </div>
                <div class="box-body border-radius-none">
                  <div class="chart" id="line-chart" style="height: 250px;"></div>
                </div><!-- /.box-body -->
                <div class="box-footer no-border">
                  <div class="row">
                    <div class="col-xs-4 text-center" style="border-right: 1px solid #f4f4f4">
                      <input type="text" class="knob" data-readonly="true" value="20" data-width="60" data-height="60" data-fgColor="#39CCCC">
                      <div class="knob-label">Mail-Orders</div>
                    </div><!-- ./col -->
                    <div class="col-xs-4 text-center" style="border-right: 1px solid #f4f4f4">
                      <input type="text" class="knob" data-readonly="true" value="50" data-width="60" data-height="60" data-fgColor="#39CCCC">
                      <div class="knob-label">Online</div>
                    </div><!-- ./col -->
                    <div class="col-xs-4 text-center">
                      <input type="text" class="knob" data-readonly="true" value="30" data-width="60" data-height="60" data-fgColor="#39CCCC">
                      <div class="knob-label">In-Store</div>
                    </div><!-- ./col -->
                  </div><!-- /.row -->
                </div><!-- /.box-footer -->
              </div><!-- /.box -->

              <!-- Calendar -->
              <div class="box box-solid bg-green-gradient">
                <div class="box-header">
                  <i class="fa fa-calendar"></i>
                  <h3 class="box-title">Calendar</h3>
                  <!-- tools box -->
                  <div class="pull-right box-tools">
                    <!-- button with a dropdown -->
                    <div class="btn-group">
                      <button class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i></button>
                      <ul class="dropdown-menu pull-right" role="menu">
                        <li><a href="#">Add new event</a></li>
                        <li><a href="#">Clear events</a></li>
                        <li class="divider"></li>
                        <li><a href="#">View calendar</a></li>
                      </ul>
                    </div>
                    <button class="btn btn-success btn-sm" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <button class="btn btn-success btn-sm" data-widget="remove"><i class="fa fa-times"></i></button>
                  </div><!-- /. tools -->
                </div><!-- /.box-header -->
                <div class="box-body no-padding">
                  <!--The calendar -->
                  <div id="calendar" style="width: 100%"></div>
                </div><!-- /.box-body -->
                <div class="box-footer text-black">
                  <div class="row">
                    <div class="col-sm-6">
                      <!-- Progress bars -->
                      <div class="clearfix">
                        <span class="pull-left">Task #1</span>
                        <small class="pull-right">90%</small>
                      </div>
                      <div class="progress xs">
                        <div class="progress-bar progress-bar-green" style="width: 90%;"></div>
                      </div>

                      <div class="clearfix">
                        <span class="pull-left">Task #2</span>
                        <small class="pull-right">70%</small>
                      </div>
                      <div class="progress xs">
                        <div class="progress-bar progress-bar-green" style="width: 70%;"></div>
                      </div>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                      <div class="clearfix">
                        <span class="pull-left">Task #3</span>
                        <small class="pull-right">60%</small>
                      </div>
                      <div class="progress xs">
                        <div class="progress-bar progress-bar-green" style="width: 60%;"></div>
                      </div>

                      <div class="clearfix">
                        <span class="pull-left">Task #4</span>
                        <small class="pull-right">40%</small>
                      </div>
                      <div class="progress xs">
                        <div class="progress-bar progress-bar-green" style="width: 40%;"></div>
                      </div>
                    </div><!-- /.col -->
                  </div><!-- /.row -->
                </div>
              </div><!-- /.box -->

            </section><!-- right col -->
          </div><!-- /.row (main row) -->

        </section><!-- /.content -->


        <script src="http://code.highcharts.com/highcharts.js"></script>
        <script src="http://code.highcharts.com/modules/exporting.js"></script>


        <!-- script -->
        <script>
		
		 var total_data;
		 
		 // get designations
		 function desginations() {
			total_data = { designation: @foreach($strength as $designation) '{{$designation->strength_name}}', @endforeach ];
			console.log(data);
			return data;
		 }
		 // get post against designation
		 function get_strength(str) {
			var data = [];
			
			if(str == 'DO') 
				data = [ @foreach($strength as $row) 
							@if($row->app_direct == 1)
								{{$row->vacant_post}},
							@endif
						@endforeach
					];
			else if(str == 2)
				data = [ @foreach($strength as $row) 
							@if($row->approved == 2)
								{{$row->vacant_post}},
							@endif
						@endforeach
					];
			else if(str == 0)
				data = [ @foreach($strength as $row) 
							@if($row->approved == 0)
								{{$row->vacant_post}},
							@endif
						@endforeach
					];
					
			console.log(data);
			return data;
		 }
		 
          $(function () {
            $('#strength-chart').highcharts({
                chart: {
                    type: 'column',
                    panning: true,
                    panKey: 'shift'
                },
                title: {
                    text: ''
                },
                xAxis: {
                    categories: desginations(),
                    labels: {
                        rotation: -45,
                        style: {
                            fontSize: '13px',
                            fontFamily: 'Verdana, sans-serif'
                        }
                    }
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Total Strength'
                    },
                    stackLabels: {
                        enabled: true,
                        style: {
                            fontWeight: 'bold',
                            color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                        }
                    }
                },
                /*legend: {
                    align: 'right',
                    x: -30,
                    verticalAlign: 'top',
                    y: 25,
                    floating: true,
                    backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
                    borderColor: '#CCC',
                    borderWidth: 1,
                    shadow: false
                },*/
                tooltip: {
                    formatter: function () {
                        return '<b>' + this.x + '</b><br/>' +
                            this.series.name + ': ' + this.y + '<br/>' +
                            'Total: ' + this.point.stackTotal;
                    }
                },
                plotOptions: {
                    column: {
                        stacking: 'normal',
                        dataLabels: {
                            enabled: true,
                            color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
                            style: {
                                textShadow: '0 0 3px black'
                            }
                        }
                    }
                },
                series: [{
                    name: 'Direct Occupied',
                    data: get_strength('DO')
                }, {
                    name: 'Direct Vacant',
                    data: get_strength('DV')
                }, {
                    name: 'Promotion Occupied',
                    data: get_strength('PO')
                }, {
                    name: 'Promotion Vacant',
                    data: get_strength('PV')
                }]
            });
            // set default zoom (10 items) : COMMENT IT TO SEE THE FULL CHART
            $('#strength-chart').highcharts().xAxis[0].setExtremes(0,9);
			
			
			// ADVERTISEMENT CHART
			$('#advertisement_chart').highcharts({
				chart: {
					type: 'area'
				},
				title: {
					text: ''
				},
				/*legend: {
					layout: 'vertical',
					align: 'left',
					verticalAlign: 'top',
					x: 150,
					y: 100,
					floating: true,
					borderWidth: 1,
					backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
				},*/
				xAxis: {
					categories: [
						'DD (Legal)',
						'DD (MIS)',
						'Protocol Officer',
						'Director (Accounts)',
						'Public Relations Officer',
						'Member (Finance)',
						'Computer Operator'
					],
					/*plotBands: [{ // visualize the weekend
						from: 4.5,
						to: 6.5,
						color: 'rgba(68, 170, 213, .2)'
					}]*/
				},
				yAxis: {
					title: {
						text: 'Total Vacant'
					}
				},
				tooltip: {
					shared: true,
					valueSuffix: ''
				},
				credits: {
					enabled: false
				},
				plotOptions: {
					areaspline: {
						fillOpacity: 0.5
					}
				},
				plotOptions: {
				area: {
						pointStart: 0,
						marker: {
							enabled: false,
							symbol: 'circle',
							radius: 2,
							states: {
								hover: {
									enabled: true
								}
							}
						}
					}
				},
				series: [{
					name: 'Vacant',
					data: [13, 24, 13, 5, 14, 10, 12]
				}, {
					name: 'Applied',
					data: [12, 9, 13, 3, 10, 8, 6]
				}, {
					name: 'Interview',
					data: [1, 3, 1, 2, 2, 1, 3]
				}]
			});
        });
        </script>
@stop