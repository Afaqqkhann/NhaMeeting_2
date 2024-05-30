@extends("base")

@section('sidebar')
@parent

@endsection

@section('content')

        <!-- Default box -->
<h3 class="box-title text-blue">Circular SMS:</h3>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Circular SMS (Funeral, Holidays, Info, Office timing etc.,)</h3>
    </div><!-- /.box-header -->
    <div class="box-body">
        <div class="form-group col-md-12">
            {!! Form::label('sms_text', 'SMS Text:', ['class' => 'col-sm-1 control-label']) !!}
            <div class="col-sm-7">
                {!! Form::textarea('sms_text', null, ['class' => 'form-control']) !!}
            </div><!-- /.col-sm-7 -->
		</div><!-- form-group -->
		
		<div class="form-group col-md-12">
            {!! Form::label('group', 'Group:', ['class' => 'col-sm-1 control-label']) !!}
            <div class="col-sm-4">
                {!! Form::select('group', array(0=>'All Employees', 1=>'17 & Above', 2=>'All Directors', 3=>'All GM', 4=>'All Members', 5=>'Christians', 6=>'Eid SMS'), null, array('class' => 'form-control input-sm')) !!}
            </div><!-- /.col-sm-4 -->
        </div><!-- /.form-group -->

    </div><!-- /.box-body -->

    <div class="box-footer">
        <div class="col-md-2 col-md-offset-6 text-right">
            <input class="btn btn-primary" type="submit" name="send" value="Send" />
        </div><!-- /.col-md-3 -->
    </div><!-- /.box-footer -->

</div><!-- /.box-primary -->


{!! Form::close() !!}

@endsection
@stop