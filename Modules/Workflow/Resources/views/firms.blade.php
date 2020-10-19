@extends('layouts.main')
@section('user_css')
    <link href="/css/jstree/themes/default/style.min.css" rel="stylesheet" media="screen">
    <link href="/css/DT_bootstrap.css" rel="stylesheet" media="screen">
@endsection
@section('dashboard')

@endsection
@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li class="active"><a href="{{ route('firms') }}">{{ $title }}</a></li>
    </ul>
    <!-- END BREADCRUMB -->
    @if (session('error'))
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="alert alert-danger panel-remove">
                    <a href="#" class="close" data-dismiss="alert">&times;</a>
                    {{ session('error') }}
                </div>
            </div>
        </div>
    @endif
    @if (session('status'))
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="alert alert-success panel-remove">
                    <a href="#" class="close" data-dismiss="alert">&times;</a>
                    {{ session('status') }}
                </div>
            </div>
        </div>
    @endif
    <!-- page content -->
    <div class="container-fluid container-fullw bg-white">
        <div class="row">
            <h2 class="text-center">{{ $head }}</h2>
            <div class="panel-heading">
                <a href="{{route('firmAdd')}}">
                    <button type="button" class="btn btn-primary btn-sm btn-o"><i class="fa fa-plus"
                                                                                  aria-hidden="true"></i> Новая
                        запись
                    </button>
                </a>
            </div>
            <div class="col-md-12">
                <div class=" table-responsive">
                    @if($rows)
                        <table id="mytable" class="table table-bordered">
                            <thead>
                            <tr>
                                <th>Код</th>
                                <th>Вид контрагента</th>
                                <th>Наименование</th>
                                <th>ИНН</th>
                                <th>Контактное лицо</th>
                                <th>Отв. менеджер</th>
                                <th>Дата обновления</th>
                                <th>Действия</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($rows as $k => $row)
                                <tr>
                                    <td>{{ $row->code }}</td>
                                    <td>{{ $row->firm_type->title }}</td>
                                    <td>{{ $row->title }}</td>
                                    <td>{{ $row->inn }}</td>
                                    @if(!empty($row->contact->full_name))
                                    <td>{{ $row->contact->full_name }}</td>
                                    @else
                                        <td></td>
                                    @endif
                                    <td>{{ $row->user->name }}</td>
                                    <td>{{ $row->updated_at }}</td>
                                    <td style="width:100px;">
                                        {!! Form::open(['url'=>route('firmDelete'), 'class'=>'form-inline','method' => 'POST', 'onsubmit' => 'return confirmDelete()']) !!}
                                        {!! Form::hidden('firm_id',$row->id,['class' => 'form-control','required'=>'required','id'=>'firm_id']) !!}
                                        <div class="form-group" role="group">
                                            <a href="{{route('firmView',['id'=>$row->id])}}">
                                                <button class="btn btn-info" type="button"
                                                        title="Просмотр записи"><i class="fa fa-eye fa-lg>"
                                                                                        aria-hidden="true"></i></button>
                                            </a>
                                            {!! Form::button('<i class="fa fa-trash-o fa-lg>" aria-hidden="true"></i>',['class'=>'btn btn-danger','type'=>'submit','title'=>'Удалить запись']) !!}
                                        </div>
                                        {!! Form::close() !!}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- /page content -->
@endsection

@section('user_script')
    <script src="/js/jquery.dataTables.min.js"></script>
    @include('confirm')
    <script>
        let table = $('#mytable').DataTable({
            "aoColumnDefs": [{
                "aTargets": [0]
            }],
            "language": {
                "processing": "Подождите...",
                "search": "Поиск: ",
                "lengthMenu": "Показать _MENU_ записей",
                "info": "Записи с _START_ до _END_ из _TOTAL_ записей",
                "infoEmpty": "Записи с 0 до 0 из 0 записей",
                "infoFiltered": "(отфильтровано из _MAX_ записей)",
                "infoPostFix": "",
                "loadingRecords": "Загрузка записей...",
                "zeroRecords": "Записи отсутствуют.",
                "emptyTable": "В таблице отсутствуют данные",
                "paginate": {
                    "first": "Первая",
                    "previous": "Предыдущая",
                    "next": "Следующая",
                    "last": "Последняя"
                },
                "aria": {
                    "sortAscending": ": активировать для сортировки столбца по возрастанию",
                    "sortDescending": ": активировать для сортировки столбца по убыванию"
                },
                "select": {
                    "rows": {
                        "_": "Выбрано записей: %d",
                        "0": "Кликните по записи для выбора",
                        "1": "Выбрана одна запись"
                    }
                }
            },
            //"aaSorting" : [[1, 'asc']],
            "aLengthMenu": [[5, 10, 15, 20, -1], [5, 10, 15, 20, "Все"] // change per page values here
            ],
            // set the initial value
            "iDisplayLength": 10,
        });
    </script>
@endsection
