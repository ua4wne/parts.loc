@extends('layouts.main')
@section('user_css')
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
        <li><a href="{{ route('wh_corrects') }}">{{ $title }}</a></li>
        <li class="active"><a href="{{ route('wh_correctsView',['id'=>$id]) }}">{{ $head }}</a></li>
    </ul>
    <!-- END BREADCRUMB -->
    @if (session('error'))
        <div class="row">
            <div class="alert alert-error panel-remove col-md-8 col-md-offset-2">
                <a href="#" class="close" data-dismiss="alert">&times;</a>
                {{ session('error') }}
            </div>
        </div>
    @endif
    <!-- page content -->
    @if (session('status'))
        <div class="row">
            <div class="alert alert-success panel-remove col-md-8 col-md-offset-2">
                <a href="#" class="close" data-dismiss="alert">&times;</a>
                {{ session('status') }}
            </div>
        </div>
    @endif
    <!-- page content -->
    <div class="container-fluid container-fullw bg-white">
        <div id="loader"></div> <!--  идентификатор загрузки (анимация) - ожидания выполнения-->
        <div class="row">
            <!-- New Position Modal -->
            <div class="modal fade" id="newPos" tabindex="-1" role="dialog" aria-labelledby="newPos"
                 aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <i class="fa fa-times-circle fa-lg" aria-hidden="true"></i>
                            </button>
                            <h4 class="modal-title">Новая позиция документа</h4>
                        </div>
                        <div class="modal-body">
                            {!! Form::open(['url' => '#','id'=>'new_pos','class'=>'form-horizontal','method'=>'POST']) !!}

                            <div class="form-group">
                                <div class="col-xs-8">
                                    {!! Form::hidden('doc_id',$id,['class' => 'form-control','required'=>'required','id'=>'doc_id']) !!}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label">
                                    Артикул: <span class="symbol required" aria-required="true"></span>
                                </label>
                                <div class="col-xs-8">
                                    {!! Form::text('vendor_code','',['class' => 'form-control typeahead','placeholder'=>'Введите артикул','required'=>'required','id'=>'search_code'])!!}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label">
                                    Место хранения: <span class="symbol required" aria-required="true"></span>
                                </label>
                                <div class="col-xs-8">
                                    {!! Form::select('location_id',$locsel, old('location_id'), ['class' => 'form-control','required'=>'required','id'=>'location_id']); !!}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label">
                                    Количество: <span class="symbol required" aria-required="true"></span>
                                </label>
                                <div class="col-xs-8">
                                    {!! Form::number('qty','1',['class' => 'form-control','placeholder'=>'Укажите количество','min' => 0, 'max' => 1000,'required'=>'required','id'=>'qty'])!!}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label">
                                    Единица измерения: <span class="symbol required" aria-required="true"></span>
                                </label>
                                <div class="col-xs-8">
                                    {!! Form::select('unit_id',$usel, old('unit_id'), ['class' => 'form-control','required'=>'required','id'=>'unit_id']); !!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('price', 'Цена:',['class'=>'col-xs-3 control-label']) !!}
                                <div class="col-xs-8">
                                    {!! Form::text('price',old('price'),['class' => 'form-control','placeholder'=>'Укажите цену','id'=>'price'])!!}
                                </div>
                            </div>

                            {!! Form::close() !!}
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                            <button type="button" class="btn btn-primary" id="btn_new">Сохранить</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- New Position Modal -->
            <!-- Edit Position Modal -->
            <div class="modal fade" id="editPos" tabindex="-1" role="dialog" aria-labelledby="editPos"
                 aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <i class="fa fa-times-circle fa-lg" aria-hidden="true"></i>
                            </button>
                            <h4 class="modal-title">Редактирование</h4>
                        </div>
                        <div class="modal-body">
                            {!! Form::open(['url' => '#','id'=>'edit_pos','class'=>'form-horizontal','method'=>'POST']) !!}

                            <div class="form-group">
                                <div class="col-xs-8">
                                    {!! Form::hidden('id','',['class' => 'form-control','required'=>'required','id'=>'eid']) !!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('vendor_code','Артикул:',['class' => 'col-xs-3 control-label'])   !!}
                                <div class="col-xs-8">
                                    {!! Form::text('vendor_code','',['class' => 'form-control typeahead','disabled'=>'disabled'])!!}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label">
                                    Место хранения: <span class="symbol required" aria-required="true"></span>
                                </label>
                                <div class="col-xs-8">
                                    {!! Form::select('location_id',$locsel, old('location_id'), ['class' => 'form-control','required'=>'required','id'=>'elocation_id']); !!}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label">
                                    Количество: <span class="symbol required" aria-required="true"></span>
                                </label>
                                <div class="col-xs-8">
                                    {!! Form::number('qty','1',['class' => 'form-control','placeholder'=>'Укажите количество','min' => 0, 'max' => 1000,'required'=>'required','id'=>'eqty'])!!}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label">
                                    Единица измерения: <span class="symbol required" aria-required="true"></span>
                                </label>
                                <div class="col-xs-8">
                                    {!! Form::select('unit_id',$usel, old('unit_id'), ['class' => 'form-control','required'=>'required','id'=>'eunit_id']); !!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('price', 'Цена:',['class'=>'col-xs-3 control-label']) !!}
                                <div class="col-xs-8">
                                    {!! Form::text('price',old('price'),['class' => 'form-control','placeholder'=>'Укажите цену','id'=>'eprice'])!!}
                                </div>
                            </div>

                            {!! Form::close() !!}
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                            <button type="button" class="btn btn-primary" id="btn_save">Сохранить</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Edit Position Modal -->
            <!-- Import Doc Modal -->
            <div class="modal fade" id="importDoc" tabindex="-1" role="dialog" aria-labelledby="importDoc"
                 aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <i class="fa fa-times-circle fa-lg" aria-hidden="true"></i>
                            </button>
                            <h4 class="modal-title">Загрузка данных из Excel</h4>
                        </div>
                        {!! Form::open(['url' => route('importWhCorrect'),'class'=>'form-horizontal','method'=>'POST','files'=>'true','data-function'=>'no_delete']) !!}
                        <div class="modal-body">

                            <div class="form-group">
                                <label class="col-xs-3 control-label">
                                    Файл Excel: <span class="symbol required" aria-required="true"></span>
                                </label>
                                <div class="col-xs-8">
                                    {!! Form::file('file', ['class' => 'form-control','data-buttonText'=>'Выберите файл Excel','data-buttonName'=>"btn-primary",'data-placeholder'=>"Файл не выбран",'required'=>'required','id'=>'file']) !!}
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                            <button type="submit" class="btn btn-primary">Сохранить</button>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
            <!-- Import Doc Modal -->
            <!-- Export Doc Modal -->
            <div class="modal fade" id="exportDoc" tabindex="-1" role="dialog" aria-labelledby="exportDoc"
                 aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <i class="fa fa-times-circle fa-lg" aria-hidden="true"></i>
                            </button>
                            <h4 class="modal-title">Выгрузка шаблона</h4>
                        </div>
                        {!! Form::open(['url' => route('exportWhCorrect'),'class'=>'form-horizontal','method'=>'POST','data-function'=>'no_delete']) !!}
                        <div class="modal-body">
                            <div class="form-group">
                                <label class=" col-md-9 col-md-offset-1 control-label">
                                    Для документа <b>{{ $head }}</b>
                                </label>
                                {!! Form::hidden('doc_id',$id,['class' => 'form-control','required'=>'required']) !!}
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                            <button type="submit" class="btn btn-primary" id="btn_export">Сохранить</button>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
            <!-- Export Doc Modal -->
            <h2 class="text-center">{{ $head }}</h2>
            <div class="panel-heading">
                @if($status)
                    <button type="button" class="btn btn-primary btn-sm btn-o" data-toggle="modal"
                            data-target="#newPos">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                        Новая позиция
                    </button>
                    <button type="button" class="btn btn-primary btn-sm btn-o" id="import" data-toggle="modal"
                            data-target="#exportDoc">
                        <i class="fa fa-upload" aria-hidden="true"></i>
                        Выгрузить шаблон
                    </button>
                    <button type="button" class="btn btn-primary btn-sm btn-o" id="import" data-toggle="modal"
                            data-target="#importDoc">
                        <i class="fa fa-download" aria-hidden="true"></i>
                        Загрузить из шаблона
                    </button>

                    <button type="button" class="btn btn-primary btn-sm btn-o" id="btn_write">
                        <i class="fa fa-check" aria-hidden="true"></i>
                        Провести документ
                    </button>
                @endif
            </div>
            <div class="col-md-12">
                <div class=" table-responsive">
                    @if($rows)
                        <table id="mytable" class="table table-bordered table-full-width dataTable no-footer">
                            <thead>
                            <tr>
                                <th>Артикул</th>
                                <th>Наименование</th>
                                <th>Ячейка</th>
                                <th>Кол-во</th>
                                <th>Цена за ед, руб</th>
                                <th>Ед. упаковки</th>
                                <th>Итого, руб</th>
                                @if($status)
                                    <th>Действия</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($rows as $k => $row)
                                <tr id="row{{ $row->id }}">
                                    <td>{{ $row->good->vendor_code }}</td>
                                    <td>{{ $row->good->title }}</td>
                                    <td>{{ $row->location->title }}</td>
                                    <td>{{ $row->qty }}</td>
                                    <td>{{ $row->price }}</td>
                                    <td>{{ $row->unit->title }}</td>
                                    <td>{{ $row->amount }}</td>
                                    @if($status)
                                        <td style="width:80px;">
                                            <div class="form-group" role="group">
                                                <button class="btn btn-success btn-sm row_edit" type="button"
                                                        data-toggle="modal" data-target="#editPos"
                                                        title="Редактировать запись"><i class="fa fa-edit fa-lg"
                                                                                        aria-hidden="true"></i>
                                                </button>
                                                <button class="btn btn-danger btn-sm row_delete" type="button"
                                                        title="Удалить запись"><i class="fa fa-trash fa-lg"
                                                                                  aria-hidden="true"></i></button>
                                            </div>
                                        </td>
                                    @endif
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
    <script src="/js/bootstrap-typeahead.min.js"></script>
    @include('confirm')
    <script>
        $('#search_code').typeahead({
            hint: true,
            highlight: true,
            minLength: 3,
            ajax: {
                url: "{{ route('getCode') }}",
                triggerLength: 1
            }
        });
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

        $('#btn_new').click(function (e) {
            e.preventDefault();
            let error = 0;
            $("#new_pos").find(":input").each(function () {// проверяем каждое поле ввода в форме
                if ($(this).attr("required") == 'required') { //обязательное для заполнения поле формы?
                    if (!$(this).val()) {// если поле пустое
                        $(this).css('border', '1px solid red');// устанавливаем рамку красного цвета
                        error = 1;// определяем индекс ошибки
                    } else {
                        $(this).css('border', '1px solid green');// устанавливаем рамку зеленого цвета
                    }

                }
            })
            if (error) {
                alert("Необходимо заполнять все доступные поля!");
                return false;
            } else {
                $.ajax({
                    type: 'POST',
                    url: '{{ route('posWhcAdd') }}',
                    data: $('#new_pos').serialize(),
                    success: function (res) {
                        //alert(res);
                        if (res == 'NO')
                            alert('Выполнение операции запрещено!');
                        if (res == 'NO VALIDATE')
                            alert('Ошибки валидации данных формы!');
                        if (res == 'ERR')
                            alert('При обновлении данных возникла ошибка!');
                        let obj = jQuery.parseJSON(res);
                        if (typeof obj === 'object') {
                            const addedRow = table.row.add([
                                $('#search_code').val(),
                                obj.title,
                                $("#location_id option:selected").text(),
                                $('#qty').val(),
                                obj.price,
                                $("#unit_id option:selected").text(),
                                obj.amount,
                                '<div class="form-group" role="group"><button class="btn btn-success btn-sm row_edit" type="button" data-toggle="modal" data-target="#editPos" title="Редактировать запись"><i class="fa fa-edit fa-lg" aria-hidden="true"></i></button>' +
                                '\n<button class="btn btn-danger btn-sm row_delete" type="button" title="Удалить запись"><i class="fa fa-trash fa-lg" aria-hidden="true"></i></button></div>'
                            ]).draw();
                            const addedRowNode = addedRow.node();
                            $(addedRowNode).attr('id', 'row' + obj.id);
                            $('#new_pos')[0].reset();
                            $(".modal").modal("hide");
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        alert(xhr.status + '\n' + thrownError);
                    }
                });
            }
        });

        $(document).on({
            click: function (e) {
                e.preventDefault();
                $('#edit_pos')[0].reset();
                let id = $(this).parent().parent().parent().attr("id");
                let code = $(this).parent().parent().prevAll().eq(6).text();
                //alert('code=' + code);
                $.ajax({
                    type: 'POST',
                    url: '{{ route('findPosWhc') }}',
                    data: {'id': id},
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        //alert(res);
                        let obj = jQuery.parseJSON(res);
                        if (typeof obj === 'object') {
                            $('#eid').val(obj.id);
                            $('#vendor_code').val(code);
                            $("#elocation_id option[value='" + obj.location_id + "']").attr("selected", "selected");
                            $('#eqty').val(obj.qty);
                            $("#unit_id option[value='" + obj.unit_id + "']").attr("selected", "selected");
                            $('#eprice').val(obj.price);
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        alert(xhr.status);
                        alert(thrownError);
                    }
                });

            }
        }, ".row_edit");

        $(document).on({
            click: function (e) {
                e.preventDefault();
                var id = $(this).parent().parent().parent().attr("id");
                //alert('id=' + id);
                let x = confirm("Выбранная запись будет удалена. Продолжить (Да/Нет)?");
                if (x) {
                    $.ajax({
                        type: 'POST',
                        url: '{{ route('posWhcDel') }}',
                        data: {'id': id},
                        headers: {
                            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (res) {
                            //alert(res);
                            if (res == 'OK') {
                                $('#' + id).hide();
                            }
                            if (res == 'NO') {
                                alert('У вас нет прав для выполнения операции удаления!');
                            }
                            if (res == 'ERR') {
                                alert('В процессе удаления произошла ошибка!');
                            }
                            if (res == 'NOT')
                                alert('Выполнение операции запрещено!');
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            alert(xhr.status);
                            alert(thrownError);
                        }
                    });
                } else {
                    return false;
                }
            }
        }, ".row_delete");

        $('#btn_write').click(function (e) {
            e.preventDefault();
            let id = $('#doc_id').val();
            let x = confirm("Выбранный документ будет проведен! Продолжить (Да/Нет)?");
            if (x) {
                $('#loader').show();
                $.ajax({
                    type: 'POST',
                    url: '{{ route('writeWhCorrect') }}',
                    data: {'id': id},
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        //alert(res);
                        if (res == 'OK') {
                            alert('Документ успешно проведен!');
                            location.reload();
                        }
                        if (res == 'NO') {
                            alert('У вас нет прав для выполнения операции проведения документа!');
                        }
                        if (res == 'ERR') {
                            alert('При попытке проведения документа возникла ошибка!');
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        alert(xhr.status);
                        alert(thrownError);
                    }
                });
                $('#loader').hide();
            } else {
                return false;
            }
        });

        $('#btn_save').click(function (e) {
            e.preventDefault();
            let error = 0;
            $("#edit_pos").find(":input").each(function () {// проверяем каждое поле ввода в форме
                if ($(this).attr("required") == 'required') { //обязательное для заполнения поле формы?
                    if (!$(this).val()) {// если поле пустое
                        $(this).css('border', '1px solid red');// устанавливаем рамку красного цвета
                        error = 1;// определяем индекс ошибки
                    } else {
                        $(this).css('border', '1px solid green');// устанавливаем рамку зеленого цвета
                    }

                }
            })
            if (error) {
                alert("Необходимо заполнять все доступные поля!");
                return false;
            } else {
                $.ajax({
                    type: 'POST',
                    url: '{{ route('posWhcEdit') }}',
                    data: $('#edit_pos').serialize(),
                    success: function (res) {
                        //alert(res);
                        if (res == 'NO')
                            alert('Выполнение операции запрещено!');
                        if (res == 'NO VALIDATE')
                            alert('Ошибки валидации данных формы!');
                        if (res == 'ERR')
                            alert('При обновлении данных возникла ошибка!');
                        let obj = jQuery.parseJSON(res);
                        if (typeof obj === 'object') {
                            $(".modal").modal("hide");

                            $('#row' + obj.id).children('td').eq(0).text($('#vendor_code').val());
                            $('#row' + obj.id).children('td').eq(2).text($("#elocation_id option:selected").text());
                            $('#row' + obj.id).children('td').eq(3).text($('#eqty').val());
                            $('#row' + obj.id).children('td').eq(4).text(obj.price);
                            $('#row' + obj.id).children('td').eq(5).text($("#eunit_id option:selected").text());
                            $('#row' + obj.id).children('td').eq(6).text(obj.amount);
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        alert(xhr.status + '\n' + thrownError);
                    }
                });
                $(".modal").modal("hide");
            }
        });

    </script>
@endsection
