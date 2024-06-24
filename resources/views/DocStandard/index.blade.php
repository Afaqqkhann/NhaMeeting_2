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
    .modal {
    display: none; 
    position: fixed; 
    z-index: 1; 
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto; 
    background-color: rgb(0, 0, 0); 
    background-color: rgba(0, 0, 0, 0.4); 
}


.modal-content {
    background-color: #fefefe;
    margin: 5% auto; 
    padding: 10px;
    border: 1px solid #888;
    width: 40%; 
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    border-radius: 10px;
    text-align: center;
}


.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}


.modal-button {
    padding: 10px 20px;
    margin: 10px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.modal-button.confirm {
    background-color: #4CAF50; 
    color: white;
}

.modal-button.confirm:hover {
    background-color: #45a049;
}

.modal-button.cancel {
    background-color: #f44336; 
    color: white;
}

.modal-button.cancel:hover {
    background-color: #da190b;
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
                            InActive
                            @endif
                    </td>
                   

                    <td>
                        <a class="btn btn-white pull-left" style="margin-right: 5px;" href="{{ route('docstandard.edit', ['id' => $type->doc_id]) }}" >
                            <i class="fa fa-edit " style="color: black;"></i>
                        </a>
                   
                    
                        <a class="btn btn-white pull-left delete-button" style="margin-right: 5px;" href="{{ route('documentstandard.destroy', ['id' => $type->doc_id]) }}" title="">
                            <i class=" fa fa-trash-o" style="color: black;"></i>
                        </a>
                        
                        <a class="btn btn-white pull-left" href="{{ route('documentstandard.show', ['id' => $type->doc_id]) }}" title="">
                            <i class=" fa fa-eye" style="color: black;" ></i>
                        </a>
                    </td>

                </tr>
                @endforeach

            </tbody>
        </table>
    </div><!-- /.box-body -->
</div><!-- /.box -->
<div id="customModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p>Are you sure you want to delete this row?</p>
        <button id="confirmDelete" class="modal-button confirm">Yes</button>
        <button id="cancelDelete" class="modal-button cancel">No</button>
    </div>
</div>
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
                            'copy','csv', 'excel', 'pdf', 'print'
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
    document.addEventListener('DOMContentLoaded', function() {
    var deleteButtons = document.querySelectorAll('.delete-button');
    var modal = document.getElementById("customModal");
    var closeModal = document.getElementsByClassName("close")[0];
    var confirmDelete = document.getElementById("confirmDelete");
    var cancelDelete = document.getElementById("cancelDelete");

    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(event) {
            event.preventDefault(); 
            modal.style.display = "block";

            confirmDelete.onclick = function() {
                window.location.href = button.href;
            };

            cancelDelete.onclick = function() {
                modal.style.display = "none";
            };

            closeModal.onclick = function() {
                modal.style.display = "none";
            };

            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            };
        });
    });
});
</script>

@stop