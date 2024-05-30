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
                  <h3>1489/1076</h3>
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
					  <div class="chart" id="strength-chart" style="margin:0 auto; height: 430px;"></div>
				  </div><!-- /.row -->
			     </div><!-- /.box-body -->
                <!-- Loading -->
                <div class="overlay" id="strength_chart_loading">
                  <i class="fa fa-refresh fa-spin"></i>
                </div>
                <!-- end loading -->
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
		 
          

        </section><!-- /.content -->


        <script src="public/js/highcharts/highcharts.js"></script>
        <script src="public/js/highcharts/exporting.js"></script>


        <!-- script -->
        <script>
          // global variables for strength chart
          var designation, direct_o, direct_v, promot_o, promot_v;
          var strength_chart, advertisement_chart;

          $(function () {

            $("#strength_chart_loading").show();

            $.ajax({
              url: '{{URL::to("/ajax_strength")}}',
              type: 'POST',
              data: {_token: CSRF_TOKEN},
              dataType: 'JSON',
              success: function (response) {
                if(response.success == true) {
                  designation = response.data.designation;
                  direct_o = response.data.direct_o;
                  direct_v = response.data.direct_v;
                  promot_o = response.data.promot_o;
                  promot_v = response.data.promot_v;

                  // new update data values of strength_chart
                  strength_chart.series[0].setData(direct_o);
                  strength_chart.series[1].setData(direct_v);
                  strength_chart.series[2].setData(promot_o);
                  strength_chart.series[3].setData(promot_v);

                  // set default zoom (10 items) : COMMENT IT TO SEE THE FULL CHART
                  strength_chart.xAxis[0].setExtremes(0,9);
                  // update categories values
                  strength_chart.xAxis[0].setCategories(designation);

                  $("#strength_chart_loading").hide();
                }
              else
                alert('Strength chart initialization error!!');
              //console.log(response);
              }
            });

            strength_chart = new Highcharts.Chart({
                chart: {
                    renderTo: 'strength-chart',
                    type: 'column',
                    panning: true,
                    panKey: 'shift'
                },
                title: {
                    text: ''
                },
                xAxis: {
                    categories: designation,
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
                    data: direct_o
                }, {
                    name: 'Direct Vacant',
                    data: direct_v
                }, {
                    name: 'Promotion Occupied',
                    data: promot_o
                }, {
                    name: 'Promotion Vacant',
                    data: promot_v
                }]
            });

			// ADVERTISEMENT CHART
			advertisement_chart = new Highcharts.Chart({
				chart: {
                    renderTo: 'advertisement_chart',
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
						text: 'Total Apply'
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
					name: 'Long Listed',
					data: [1035, 2134, 1153, 859, 1372, 2120, 1442]
				}, {
					name: 'Short Listed',
					data: [520, 890, 430, 136, 660, 784, 562]
				}, {
					name: 'Interview',
					data: [110, 113, 141, 18, 112, 221, 63]
				}]
			});
        });
        </script>
@stop