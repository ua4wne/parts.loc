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
        <li><a href="{{ route('prices') }}">{{ $title }}</a></li>
        <li class="active"><a href="{{ route('priceView',['id'=>$id]) }}">{{ $head }}</a></li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- page content -->
    <div class="container-fluid container-fullw bg-white">
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
                                    {!! Form::hidden('price_id',$id,['class' => 'form-control','required'=>'required','id'=>'price_id']) !!}
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
                                    Цена для сайта, руб: <span class="symbol required" aria-required="true"></span>
                                </label>
                                <div class="col-xs-8">
                                    {!! Form::text('cost_1',old('cost_1'),['class' => 'form-control','placeholder'=>'Введите цену продажи','required'=>'required','id'=>'cost1'])!!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('cost_2','Цена в 1С, руб:',['class' => 'col-xs-3 control-label'])   !!}
                                <div class="col-xs-8">
                                    {!! Form::text('cost_2',old('cost_2'),['class' => 'form-control','placeholder'=>'Укажите цену из 1С','id'=>'cost2'])!!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('cost_3','Цена рыночная, руб:',['class' => 'col-xs-3 control-label'])   !!}
                                <div class="col-xs-8">
                                    {!! Form::text('cost_3',old('cost_3'),['class' => 'form-control','placeholder'=>'Укажите цену рынка','id'=>'cost3'])!!}
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
                                {!! Form::label('cost_3','Артикул:',['class' => 'col-xs-3 control-label'])   !!}
                                <div class="col-xs-8">
                                    {!! Form::text('vendor_code','',['class' => 'form-control typeahead','placeholder'=>'Введите артикул','disabled'=>'disabled','id'=>'vendor_code'])!!}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label">
                                    Цена для сайта, руб: <span class="symbol required" aria-required="true"></span>
                                </label>
                                <div class="col-xs-8">
                                    {!! Form::text('cost_1',old('cost_1'),['class' => 'form-control','placeholder'=>'Введите цену продажи','required'=>'required','id'=>'ecost1'])!!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('cost_2','Цена в 1С, руб:',['class' => 'col-xs-3 control-label'])   !!}
                                <div class="col-xs-8">
                                    {!! Form::text('cost_2',old('cost_2'),['class' => 'form-control','placeholder'=>'Укажите цену из 1С','id'=>'ecost2'])!!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('cost_3','Цена рыночная, руб:',['class' => 'col-xs-3 control-label'])   !!}
                                <div class="col-xs-8">
                                    {!! Form::text('cost_3',old('cost_3'),['class' => 'form-control','placeholder'=>'Укажите цену рынка','id'=>'ecost3'])!!}
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
            <!-- Import Price Modal -->
            <div class="modal fade" id="importPrice" tabindex="-1" role="dialog" aria-labelledby="importPrice"
                 aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <i class="fa fa-times-circle fa-lg" aria-hidden="true"></i>
                            </button>
                            <h4 class="modal-title">Загрузка данных из Excel</h4>
                        </div>
                        <div class="modal-body">
                            {!! Form::open(['url' => '#','id'=>'import_price','class'=>'form-horizontal','method'=>'POST','files'=>'true']) !!}

                            <div class="form-group">
                                <label class="col-xs-3 control-label">
                                    Файл Excel: <span class="symbol required" aria-required="true"></span>
                                </label>
                                <div class="col-xs-8">
                                    {!! Form::file('file', ['class' => 'form-control','data-buttonText'=>'Выберите файл Excel','data-buttonName'=>"btn-primary",'data-placeholder'=>"Файл не выбран",'required'=>'required','id'=>'file']) !!}
                                </div>
                            </div>

                            {!! Form::close() !!}
                        </div>
                        <div class="modal-footer">
                            <span class="pull-left" id="file-loader"><img src="/images/file-loader.gif"></span>
                            <!--  идентификатор загрузки (анимация) - ожидания выполнения-->
                            <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                            <button type="button" class="btn btn-primary" id="btn_import">Сохранить</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Import Price Modal -->
            <h2 class="text-center">{{ $head }}</h2>
            <div class="panel-heading">
                <button type="button" class="btn btn-primary btn-sm btn-o" data-toggle="modal"
                        data-target="#newPos">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                    Новая позиция
                </button>
                <button type="button" class="btn btn-primary btn-sm btn-o" id="import" data-toggle="modal"
                        data-target="#importPrice">
                    <i class="fa fa-download" aria-hidden="true"></i>
                    Импорт
                </button>
                <a href="{{ route('exportPrice',['id'=>$id]) }}">
                    <button type="button" class="btn btn-primary btn-sm btn-o"><i class="fa fa-upload"
                                                                                  aria-hidden="true"></i>
                        Экспорт
                    </button>
                </a>
            </div>
            <div class="col-md-12">
                <div class=" table-responsive">
                    @if($rows)
                        <table id="mytable" class="table table-bordered table-full-width dataTable no-footer">
                            <thead>
                            <tr>
                                <th>Артикул</th>
                                <th>Наименование</th>
                                <th>Цена сайт</th>
                                <th>Цена 1С</th>
                                <th>Рыночная цена</th>
                                <th>Действия</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($rows as $k => $row)
                                <tr id="row{{ $row->id }}">
                                    <td>{{ $row->good->vendor_code }}</td>
                                    <td>{{ $row->good->title }}</td>
                                    <td>{{ $row->cost_1 }}</td>
                                    <td>{{ $row->cost_2 }}</td>
                                    <td>{{ $row->cost_3 }}</td>
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
                    url: '{{ route('posPriceAdd') }}',
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
                                $('#cost1').val(),
                                $('#cost2').val(),
                                $('#cost3').val(),
                                '<div class="form-group" role="group"><button class="btn btn-success btn-sm row_edit" type="button" data-toggle="modal" data-target="#editPos" title="Редактировать запись"><i class="fa fa-edit fa-lg" aria-hidden="true"></i></button>' +
                                '\n<button class="btn btn-danger btn-sm row_delete" type="button" title="Удалить запись"><i class="fa fa-trash fa-lg" aria-hidden="true"></i></button></div>'
                            ]).draw();
                            const addedRowNode = addedRow.node();
                            $(addedRowNode).attr('id', 'row'+obj.id);
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
                let code = $(this).parent().parent().prevAll().eq(4).text();
                //alert('id=' + id);
                $.ajax({
                    type: 'POST',
                    url: '{{ route('findPosition') }}',
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
                            $('#ecost1').val(obj.cost_1);
                            $('#ecost2').val(obj.cost_2);
                            $('#ecost3').val(obj.cost_3);
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
                        url: '{{ route('posPriceDel') }}',
                        data: {'id': id},
                        headers: {
                            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (res) {
                            //alert(res);
                            if(res=='OK'){
                                $('#' + id).hide();
                            }
                            if(res=='NO'){
                                alert('У вас нет прав для выполнения операции удаления!');
                            }
                            if(res=='ERR'){
                                alert('В процессе удаления произошла ошибка!');
                            }
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
                    url: '{{ route('posPriceEdit') }}',
                    data: $('#edit_pos').serialize(),
                    success: function (res) {
                        //alert(res);
                        if (res == 'NO')
                            alert('Выполнение операции запрещено!');
                        if (res == 'NO VALIDATE')
                            alert('Ошибки валидации данных формы!');
                        if (res == 'ERR')
                            alert('При обновлении данных возникла ошибка!');
                        if (res == 'OK') {
                            //$(".modal").modal("hide");
                            location.reload();
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        alert(xhr.status + '\n' + thrownError);
                    }
                });
                $(".modal").modal("hide");
            }
        });

        $('#btn_import').click(function (e) {
            e.preventDefault();
            let error = 0;
            $("#import_price").find(":input").each(function () {// проверяем каждое поле ввода в форме
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
                let formData = new FormData();
                formData.append('file', $('#file').prop("files")[0]);
                $.ajax({
                    type: 'POST',
                    url: '{{ route('importPrice') }}',
                    processData: false,
                    contentType: false,
                    cache:false,
                    dataType : 'text',
                    data: formData,
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        //alert(res);
                        if (res == 'NO')
                            alert('Выполнение операции запрещено!');
                        if (res == 'ERR')
                            alert('При обновлении данных возникла ошибка!');
                        let obj = jQuery.parseJSON(res);
                        if (typeof obj === 'object') {
                            $(".modal").modal("hide");
                            alert('Обработано строк '+obj.num+' из '+obj.rows+'!');
                            location.reload();
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        alert(xhr.status + '\n' + thrownError);
                    }
                });
            }
        });

    </script>
@endsection
