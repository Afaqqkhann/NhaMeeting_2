<?php $menus = App\Permission::where('parent', '=', 1)->orderBy('sort', 'ASC')->get(); ?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>NHA Meetings | {{$page_title or ''}}</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.5 -->
  {{--<link rel="stylesheet" href="../../bootstrap/css/bootstrap.min.css">--}}
  {!! Html::style('/css/bootstrap/css/bootstrap.min.css') !!}
  {!! Html::style('/css/bootstrap/css/datatables.bootstrap.css') !!}
  {!! Html::style('/css/font-awesome-4.4.0/css/font-awesome.min.css') !!}
  {!! Html::style('/css/ionicons.min.css') !!}
  {!! Html::style('/css/dist/css/AdminLTE.min.css') !!}
  {!! Html::style('/css/dist/css/skins/_all-skins.min.css') !!}
  {!! Html::style('/plugins/daterangepicker/daterangepicker-bs3.css') !!}

  @stack('styles')


  <!-- jQuery 2.1.4 -->
  {!! Html::script("/plugins/jQuery/jQuery-2.1.4.min.js") !!}
  {!! Html::script("/css/bootstrap/js/bootstrap.min.js") !!}

  @stack('jsLib')

  <!-- Font Awesome -->

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
    {!! Html::script("/js/3.7.3/html5shiv.min.js") !!}
    {!! Html::script("/js/respond.min.js") !!}
    <![endif]-->

  <link rel="shortcut icon" href="{{URL::to('/img/favicon.ico')}}" />

  <meta name="csrf-token" content="{{ csrf_token() }}" />

  <script>
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
  </script>

</head>

<body class="hold-transition skin-blue sidebar-mini sidebar-collapse">
  <?php $profile_picture = '';

  ?>


  <!-- Site wrapper -->
  <div class="wrapper">

    <header class="main-header">
      <!-- Logo -->
      <a href="{{ URL::to('/dashboard') }}" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini">NHA</span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg">NHA - Meetings</span>
      </a>

      <!-- Header Navbar: style can be found in header.less -->
      <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->

        <a href="#" class="sidebar-toggle" style="font-size:15px;" data-toggle="offcanvas" role="button">
          NHA Meetings
        </a>
        <div class="navbar-custom-menu">
          <ul class="nav navbar-nav">

            <!-- Notification area -->
            <li class="dropdown messages-menu">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                <i class="fa fa-envelope-o"></i>
                <span class="label label-success notification_label"></span>
              </a>
              <ul class="dropdown-menu">
                <li class="header" id="notification_count_msg">0 messages</li>
                <li>
                  <!-- inner menu: contains the actual data -->
                  <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 200px;">
                    <ul class="menu" id="notification_list" style="overflow: hidden; width: 100%; height: 200px;">

                    </ul>
                    <div class="slimScrollBar" style="width: 3px; position: absolute; top: 0px; opacity: 0.4; display: none; border-radius: 7px; z-index: 99; right: 1px; height: 131.148px; background: rgb(0, 0, 0);"></div>
                    <div class="slimScrollRail" style="width: 3px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; opacity: 0.2; z-index: 90; right: 1px; background: rgb(51, 51, 51);"></div>
                  </div>
                </li>
                <li class="footer"><a href="{{url('community_msg')}}">See All Messages</a></li>
              </ul>
            </li>

            <!-- User Account: style can be found in dropdown.less -->
            <li class="dropdown user user-menu">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <img src="{{ asset("/img/nha_logo.png") }}" class="user-image" alt="profile">
                <span class="hidden-xs"></span>
              </a>
              <ul class="dropdown-menu">
                <!-- User image -->
                <li class="user-header">
                  <img src="" class="img-circle" alt="profile">
                  <p>
                  </p>
                </li>
                <li class="user-footer">
                  <div class="pull-left">
                    <a href="{{URL::to('/users/update_profile')}}" class="btn btn-default btn-flat" style="    background: #00a65a;color: white;">Profile</a>
                  </div>
                  <div class="pull-right">
                    <a href="{{URL::to('/auth/logout')}}" class="btn btn-default btn-flat" style="background: #d74040;
    color: white;">Sign out</a>
                  </div>
                </li>
              </ul>
            </li>

          </ul>
        </div>
      </nav>
    </header>

    <!-- Left side column. contains the sidebar -->
    <aside class="main-sidebar">
      <!-- sidebar: style can be found in sidebar.less -->
      <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
          <div class="pull-left image">
            <img src="{{ asset("/img/nha_logo.png") }}" class="img-circle" alt="User Image">
          </div>
          <div class="pull-left info">
            <p>NHA Meeting User</p>
            <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
          </div>
        </div>

        <ul class="sidebar-menu">
          <li class="header">MAIN NAVIGATION</li>


         


          <li class=" treeview">
            <a href="{{url('dashboard')}}">
              <i class="fa fa-dashboard"></i> <span> EIS Dashboard</span>
              <!--<i class="fa fa-users"></i> <span>Hajj Draw Special</span> -->
            </a>
          </li>
          <li class=" treeview">
            <a href="{{url('meeting_types')}}">
              <i class="fa fa-book"></i> <span>Meeting Type</span>
              <!--<i class="fa fa-users"></i> <span>Hajj Draw Special</span> -->
            </a>
          </li>
          <li class=" treeview">
            <a href="{{url('wing')}}">
              <i class="fa fa-book"></i> <span>Wing</span>
              <!--<i class="fa fa-users"></i> <span>Hajj Draw Special</span> -->
            </a>
          </li>
          <li class=" treeview">
            <a href="{{url('docstandard')}}">
              <i class="fa fa-book"></i> <span>Document-Standard</span>
              <!--<i class="fa fa-users"></i> <span>Hajj Draw Special</span> -->
            </a>
          </li>
          <li class=" treeview">
            <a href="{{url('meeting')}}">
              <i class="fa   fa-book"></i> <span>Meeting</span>
              <!--<i class="fa fa-users"></i> <span>Hajj Draw Special</span> -->
            </a>
          </li>


        </ul>
      </section>
      <!-- /.sidebar -->
    </aside>


    <!-- =============================================== -->

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">

        @section('sidebar')
        @show

        {{-- <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Examples</a></li>
            <li class="active">Blank page</li>
          </ol>--}}
      </section>

      <!-- Main content -->
      <section class="content">

        @if(Session::has('message'))
        <div class="alert alert-info">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
          {{Session::get('message')}}
        </div>
        @endif

        @yield('content')


      </section><!-- /.content -->
    </div><!-- /.content-wrapper -->

    <footer class="main-footer hidden-print">
      <div class="pull-right hidden-xs">
        <b>Version</b> 1.4
      </div>
      <strong>Copyright &copy; 2016 <a href="http://www.nha.gov.pk">National Highway Authority</a>. </strong>CB NHA HQ. All rights reserved.
    </footer>


    <!-- Add the sidebar's background. This div must 
           immediately after the control sidebar -->
    <div class="control-sidebar-bg"></div>
  </div><!-- ./wrapper -->



  {!! Html::script("/plugins/slimScroll/jquery.slimscroll.min.js") !!}
  {!! Html::script("/plugins/fastclick/fastclick.min.js") !!}
  {!! Html::script("/css/dist/js/app.min.js") !!}
  {!! Html::script("/css/dist/js/demo.js") !!}
  {!! Html::script("/js/moment.min.js") !!}
  {!! Html::script("/plugins/daterangepicker/daterangepicker.js") !!}
  {!! Html::script("/css/dist/js/jquery-clone.js") !!}
  {!! Html::script("/js/dropzone.js") !!}

  <script src="{{ URL::to('/plugins/datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{ URL::to('/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>

  {!! Html::script("/plugins/input-mask/jquery.inputmask.js") !!}
  {!! Html::script("/plugins/input-mask/jquery.inputmask.date.extensions.js") !!}
  {!! Html::script("/plugins/input-mask/jquery.inputmask.extensions.js") !!}
  {!! Html::script("/plugins/input-mask/jquery.inputmask.numeric.extensions.js") !!}
  {!! Html::script("/plugins/input-mask/jquery.inputmask.phone.extensions.js") !!}
  {!! Html::script("/plugins/input-mask/jquery.inputmask.regex.extensions.js") !!}


  <link href="{{url('/plugins/select2/select2.css')}}" type="text/css" rel="stylesheet" />
  <script src="{{url('/plugins/select2/select2.js')}}"></script>

  @stack('scripts')


  <script>
    $(function() {
      $(".dateMask").inputmask();
      $('.only_digits').on('keydown', function(e) {
        -1 !== $.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) || /65|67|86|88/.test(e.keyCode) && (!0 === e.ctrlKey || !0 === e.metaKey) || 35 <= e.keyCode && 40 >= e.keyCode || (e.shiftKey || 48 > e.keyCode || 57 < e.keyCode) && (96 > e.keyCode || 105 < e.keyCode) && e.preventDefault()
      });

      $(".slimScrollDiv").slimScroll({
        height: '200px'
      });
      // first call
      updateAlerts();

      // ajax call
      function updateAlerts() {
        $.ajax({
          url: "{{url('community_msg/ajax_update')}}",
          dataType: "JSON",
          success: function(data) {
            // Update the DOM to show the new alerts!
            if (data) {
              // update the number in the DOM and make sure it is visible...
              $(".notification_label").html(data.total_messages);
              $("#notification_count_msg").html(data.total_messages + ' messages');

              var html = '';
              $.each(data.result, function(index, value) {
                html += '<li>';
                html += '<a href="{!! url() !!}/community_msg/' + value.community_id + '">';
                html += '<div class="pull-left">';
                html += '<img src="https://cdn2.iconfinder.com/data/icons/circle-icons-1/64/mail-128.png" class="img-circle" alt="Community Message Picture" data-pin-nopin="true">';
                html += '</div>';
                html += '<h4>' + value.message_title + '</h4>';
                html += '<p>' + value.message + '</p>';
                html += '</a>';
                html += '</li>';
              });
              $("#notification_list").html(html);
            }
          }
        });
      }
      setInterval(updateAlerts, 15000); // Every 15 seconds.
    });

    $('#acr_table').DataTable({
      processing: true,
      serverSide: true,
      ajax: '{{ url("employees/acr_data") }}'
    });

    $('#strength_table').DataTable({
      processing: true,
      serverSide: true,
      ajax: '{{ url("strength/strength_data") }}',
    });

    $('.theDataTable').DataTable({
      "order": [],
      "lengthMenu": [
        [10, 25, 50, 100, -1],
        [10, 25, 50, 100, "All"]
      ]
    });

    $(".js-select2").select2();

    //Datemask dd-mm-yyyy
    $('.datemask').inputmask('dd-mm-yyyy', {
      'placeholder': 'dd-mm-yyyy'
    })
  </script>

  <script>
    @section('pensionDoc')
    @show


    $('.date_field').daterangepicker({
      singleDatePicker: true
    });

    $(".chk").change(function() {
      if (this.checked) {
        $(this).parent().parent().find(".dField").show();
      } else {
        $(this).parent().parent().find(".dField").hide();
      }
    });
  </script>

  <script>
    @yield('js')
  </script>

</body>

</html>