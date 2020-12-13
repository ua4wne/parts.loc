@extends('layouts.main')

@section('dashboard')

@endsection
@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li><a href="{{ route('applications') }}">{{ $title }}</a></li>
        <li class="active">{{ $head }}</li>
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
    <!-- New Position Modal -->
    <div class="modal fade" id="editDoc" tabindex="-1" role="dialog" aria-labelledby="editDoc"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times-circle fa-lg" aria-hidden="true"></i>
                    </button>
                    <h4 class="modal-title">Новая позиция</h4>
                </div>
                <div class="modal-body">
                    {!! Form::open(['url' => '#','id'=>'add_pos','class'=>'form-horizontal','method'=>'POST']) !!}

                    <div class="form-group">
                        <div class="col-xs-12">
                            <fieldset>
                                <legend>Поиск номенклатуры:</legend>
                                <div class="form-group">
                                    {!! Form::label('vendor_code', 'По артикулу:',['class'=>'col-xs-4 control-label']) !!}
                                    <div class="col-xs-7">
                                        {!! Form::text('vendor_code', '', ['class' => 'form-control','placeholder'=>'Начинайте вводить артикул','id'=>'by_vendor'])!!}
                                    </div>
                                </div>

                                <div class="form-group">
                                    {!! Form::label('by_catalog', 'По каталожному №:',['class'=>'col-xs-4 control-label']) !!}
                                    <div class="col-xs-7">
                                        {!! Form::text('by_catalog', '', ['class' => 'form-control','placeholder'=>'Начинайте вводить каталожный №','id'=>'by_catalog'])!!}
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('good_id', 'Наименование:',['class'=>'col-xs-3 control-label']) !!}
                        <div class="col-xs-8">
                            {!! Form::select('good_id', [], '', ['class' => 'form-control','id'=>'good_id','required'=>'required'])!!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('qty','Количество:',['class' => 'col-xs-3 control-label'])   !!}
                        <div class="col-xs-8">
                            {!! Form::number('qty','1',['class' => 'form-control','placeholder'=>'Введите количество','required'=>'required','min' => 1, 'max' => 1000,'id'=>'qty'])!!}
                            {!! $errors->first('qty', '<p class="text-danger">:message</p>') !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('unit_id', 'Ед. измерения:',['class'=>'col-xs-3 control-label']) !!}
                        <div class="col-xs-8">
                            {!! Form::select('unit_id', $unsel, old('unit_id'),['class' => 'form-control','required' => 'required']); !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('car_id', 'Техника:',['class'=>'col-xs-3 control-label']) !!}
                        <div class="col-xs-8">
                            {!! Form::select('car_id',$carsel,old('car_id'),['class' => 'form-control','required'=>'required','id'=>'car_id'])!!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('supplier_num','№ поставщика:',['class' => 'col-xs-3 control-label'])   !!}
                        <div class="col-xs-8">
                            {!! Form::text('supplier_num','',['class' => 'form-control','placeholder'=>'Укажите № поставщика','maxlength'=>'20','id'=>'supplier_num'])!!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('days','Срок поставки:',['class' => 'col-xs-3 control-label'])   !!}
                        <div class="col-xs-8">
                            {!! Form::text('days','',['class' => 'form-control','placeholder'=>'Укажите срок поставки в днях','maxlength'=>'5','id'=>'days'])!!}
                        </div>
                    </div>


                    <div class="form-group">
                        {!! Form::label('price','Цена:',['class' => 'col-xs-3 control-label'])   !!}
                        <div class="col-xs-8">
                            {!! Form::text('price','',['class' => 'form-control','placeholder'=>'Укажите цену','required'=>'required', 'id'=>'price'])!!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('currency_id','Валюта:',['class' => 'col-xs-3 control-label'])   !!}
                        <div class="col-xs-8">
                            {!! Form::select('currency_id',$cursel,old('currency_id'),['class' => 'form-control','required'=>'required','id'=>'currency_id'])!!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('comment', 'Комментарий:',['class'=>'col-xs-3 control-label']) !!}
                        <div class="col-xs-8">
                            {!! Form::textarea('comment','',['class'=>'form-control', 'rows' => 2, 'cols' => 50, 'id'=>'comment']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-8">
                            {!! Form::hidden('application_id',$application->id,['class' => 'form-control','id'=>'id_doc','required'=>'required']) !!}
                        </div>
                    </div>

                    {!! Form::close() !!}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Отмена
                    </button>
                    <button type="button" class="btn btn-primary" id="new_btn">Сохранить
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- New Position Modal -->
    <div class="container-fluid container-fullw bg-white">
        <div class="row">
            <div class="col-md-12">
                <h2 class="text-center">{{ $head }}</h2>
                <h4 class="text-center">дата создания {{ $application->created_at }}</h4>
                <div class="tabbable pills">
                    <ul id="myTab3" class="nav nav-pills">
                        <li class="active">
                            <a href="#common" data-toggle="tab">
                                Общая информация
                            </a>
                        </li>
                        <li>
                            <a href="#goods" data-toggle="tab">
                                Товары
                            </a>
                        </li>
                        <li>
                            <a href="#links" data-toggle="tab">
                                Цепочка документов
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade in active" id="common">
                            {!! Form::open(['url' => route('applicationEdit',['id'=>$application->id]),'class'=>'form-horizontal','method'=>'POST', 'id'=>'form_ref']) !!}
                            <table class="table table-bordered table-hover">
                                <tr>
                                    <th>Номер документа:</th>
                                    <td>{!! Form::text('doc_num',$application->doc_num,['class' => 'form-control','placeholder'=>'Введите номер документа','maxlength'=>'15','required'=>'required'])!!}
                                        {!! $errors->first('doc_num', '<p class="text-danger">:message</p>') !!}
                                    </td>
                                    <th>
                                        Заказ клиента: <span class="symbol required" aria-required="true"></span>
                                    </th>
                                    <td>{!! Form::text('sale_id',$application->sale->doc_num,['class' => 'form-control','placeholder'=>'Введите номер документа','maxlength'=>'15','required'=>'required','id'=>'search'])!!}
                                        {!! $errors->first('doc_num', '<p class="text-danger">:message</p>') !!}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        Приоритет: <span class="symbol required" aria-required="true"></span>
                                    </th>
                                    <td>
                                        {!! Form::select('priority_id',$psel, $application->priority_id, ['class' => 'form-control','required'=>'required']); !!}
                                    </td>
                                    <th>
                                        Степень важности: <span class="symbol required" aria-required="true"></span>
                                    </th>
                                    <td>
                                        {!! Form::number('rank',$application->rank,['class' => 'form-control','placeholder'=>'Введите число','min' => 0, 'max' => 250])!!}
                                        {!! $errors->first('rank', '<p class="text-danger">:message</p>') !!}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        Заявитель: <span class="symbol required" aria-required="true"></span>
                                    </th>
                                    <td>
                                        {!! Form::select('user_id',$usel, $application->author_id, ['class' => 'form-control','required'=>'required','disabled'=>'disabled']); !!}
                                    </td>

                                    <th>Ответственный:</th>
                                    <td>
                                        {!! Form::select('user_id',$usel, $application->user_id, ['class' => 'form-control','required'=>'required','id'=>'user_id']); !!}
                                    </td>
                                </tr>

                                <tr>
                                    <th>Статус:</th>
                                    <td>
                                        {!! Form::select('statuse_id',$statsel,$application->statuse_id,['class' => 'form-control','required'=>'required'])!!}
                                    </td>
                                    <th>Комментарий:</th>
                                    <td>
                                        {!! Form::textarea('comment',$application->comment,['class'=>'form-control', 'rows' => 2, 'cols' => 50]) !!}
                                    </td>
                                </tr>
                            </table>
                            <div class="form-group">
                                <div class="col-xs-8">
                                    {!! Form::button('Обновить', ['class' => 'btn btn-primary','type'=>'submit', 'id'=>'save_btn']) !!}
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                        <div class="tab-pane fade" id="goods">
                            <div class="panel-heading">
                                @if($application->state == 0)
                                    <a href="#">
                                        <button type="button" class="btn btn-primary btn-sm btn-o" id="new_pos"
                                                data-toggle="modal" data-target="#editDoc">
                                            <i class="fa fa-plus" aria-hidden="true"></i> Добавить
                                        </button>
                                    </a>
                                    <div class="btn-group">
                                        <a class="btn btn-primary btn-o btn-sm dropdown-toggle" data-toggle="dropdown"
                                           href="#" aria-expanded="false">
                                            Создать на основании <span class="caret"></span>
                                        </a>
                                        <ul role="menu" class="dropdown-menu dropdown-light">
                                            <li>
                                                <a href="#">
                                                    Заказ поставщику
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table id="doc_table" class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <th>Каталожный №</th>
                                            <th>Номера аналогов</th>
                                            <th>Наименование</th>
                                            <th>Кол-во</th>
                                            <th>Ед.изм</th>
                                            <th>Техника</th>
                                            <th>№ поставщика</th>
                                            <th>Срок поставки</th>
                                            <th>Цена</th>
                                            <th>Валюта</th>
                                            <th>Комментарий</th>
                                            <th>Действия</th>
                                        </tr>
                                        </thead>
                                        @if($rows)
                                            <tbody id="t_body">
                                            @foreach($rows as $k => $row)
                                                <tr id="{{ $row->id }}">
                                                    <td>{{ $row->good->catalog_num }}</td>
                                                    <td>{{ $row->good->analog_code }}</td>
                                                    <td>{{ $row->good->title }}</td>
                                                    <td>{{ $row->qty }}</td>
                                                    <td>{{ $row->unit->title }}</td>
                                                    <td>{{ $row->car->title }}</td>
                                                    <td>{{ $row->supplier_num }}</td>
                                                    <td>{{ $row->days }}</td>
                                                    <td>{{ $row->price }}</td>
                                                    <td>{{ $row->currency->title }}</td>
                                                    <td>{{ $row->comment }}</td>
                                                    <td style="width:70px;">
                                                        <div class="form-group" role="group">
                                                            <button class="btn btn-danger btn-sm pos_delete"
                                                                    type="button" title="Удалить позицию"><i
                                                                    class="fa fa-trash fa-lg"
                                                                    aria-hidden="true"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="links">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table id="doc_table" class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <th>Документ</th>
                                            <th>Статус</th>
                                            <th>Дата создания</th>
                                            <th>Автор</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        {!! $tbody !!}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /page content -->
@endsection

@section('user_script')
    <script src="/js/bootstrap-typeahead.min.js"></script>
    {{--    <script src="/js/jquery.tabledit.min.js"></script>--}}
    <script>
        /*$('#doc_table').Tabledit({
            url: 'example.php',
            columns: {
                identifier: [0, 'id'],
                editable: [[1, 'vendor_code'], [2, 'catalog_num'], [3, 'good_id'],[4, 'spec']]
            }
        });*/

        $("#car_id").prepend($('<option value="0">Выберите технику</option>'));
        $("#car_id :first").attr("selected", "selected");
        $("#car_id :first").attr("disabled", "disabled");

        $("#currency_id").prepend($('<option value="0">Выберите валюту</option>'));
        $("#currency_id :first").attr("selected", "selected");
        $("#currency_id :first").attr("disabled", "disabled");

        $('#search').typeahead({
            hint: true,
            highlight: true,
            minLength: 3,
            ajax: {
                url: "{{ route('getSale') }}",
                triggerLength: 1
            }
        });

        $('#by_vendor').typeahead({
            hint: true,
            highlight: true,
            minLength: 3,
            ajax: {
                url: "{{ route('getCode') }}",
                triggerLength: 1
            }
        });

        $('#search_evendor').typeahead({
            hint: true,
            highlight: true,
            minLength: 3,
            ajax: {
                url: "{{ route('getCode') }}",
                triggerLength: 1
            }
        });

        $('#by_catalog').typeahead({
            hint: true,
            highlight: true,
            minLength: 3,
            ajax: {
                url: "{{ route('getAnalog') }}",
                triggerLength: 1
            }
        });

        $('#by_vendor').focus(function () {
            $('#by_catalog').val('');
        });


        $('#by_catalog').focus(function () {
            $('#by_vendor').val('');
        });


        $('#save_btn').click(function () {
            let error = 0;
            $("#form_ref").find(":input").each(function () {// проверяем каждое поле ввода в форме
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
            }
        });

        $('#new_btn').click(function (e) {
            e.preventDefault();
            let error = 0;
            $("#add_pos").find(":input").each(function () {// проверяем каждое поле ввода в форме
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
                    url: '{{ route('addApplicationPos') }}',
                    data: $('#add_pos').serialize(),
                    success: function (res) {
                        //alert(res);
                        if (res == 'BAD') {
                            alert('У Вас нет прав для редактирования документа!')
                        }
                        if (res == 'NO') {
                            alert('Не известный запрос!')
                        }
                        let obj = jQuery.parseJSON(res);
                        if (typeof obj === 'object') {
                            $("#doc_table").append(obj.content);
                            $('#price').val('');
                            $('#by_vendor').val('');
                            $('#by_catalog').val('');
                            $('#good_id').empty();
                            $('#days').val('');
                            $('#supplier_num').val('');
                            $('#comment').val('');
                            $('#qty').val('1');
                            $(".modal").modal("hide");
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        alert(xhr.status);
                        alert(thrownError);
                    }
                });
            }
        });

        $("#good_id").focus(function () {
            $("#good_id").empty(); //очищаем от старых значений
            let vendor_code = $("#by_vendor").val();
            let by_catalog = $("#by_catalog").val();
            if (vendor_code.length > 3) {
                $.ajax({
                    type: 'POST',
                    url: '{{ route('searchByVendor') }}',
                    data: {'vendor_code': vendor_code},
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        //alert(res);
                        $("#good_id").prepend($(res));
                    }
                });
            }
            if (by_catalog.length > 3) {
                $.ajax({
                    type: 'POST',
                    url: '{{ route('searchByAnalog') }}',
                    data: {'analog_code': by_catalog},
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        //alert(res);
                        $("#good_id").prepend($(res));
                    }
                });
            }
        });

        $("#egood_id").focus(function () {
            $("#egood_id").empty(); //очищаем от старых значений
            let vendor_code = $("#search_evendor").val();
            $.ajax({
                type: 'POST',
                url: '{{ route('searchByVendor') }}',
                data: {'vendor_code': vendor_code},
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    //alert(res);
                    $("#egood_id").prepend($(res));
                }
            });
        });

        $("#comment").focus(function () {
            $("#comment").empty(); //очищаем от старых значений
            let id = $("#good_id").val();
            $.ajax({
                type: 'POST',
                url: '{{ route('specByVendor') }}',
                data: {'id': id},
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    //alert(res);
                    $("#comment").prepend($(res));
                }
            });
        });

        $("#ecomment").focus(function () {
            $("#ecomment").empty(); //очищаем от старых значений
            let id = $("#egood_id").val();
            $.ajax({
                type: 'POST',
                url: '{{ route('specByVendor') }}',
                data: {'id': id},
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    //alert(res);
                    $("#ecomment").prepend($(res));
                }
            });
        });

        $(document).on({
            click: function () {
                let id = $(this).parent().parent().parent().attr("id");
                $.ajax({
                    type: 'POST',
                    url: '{{ route('getSpecPos') }}',
                    data: {'id': id},
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        //alert(res);
                        $("#importSpfc").modal("show");
                        $('#pos_id').val(id);
                        $('#spec').empty(); //очищаем от предыдущих значений
                        $('#spec').prepend(res);
                    }
                });
            }
        }, ".pos_spec");

        $(document).on({
            click: function () {
                let row = $(this).parent().parent().parent();
                $("#errPoc").modal("show");
                $('#search_evendor').val(row.children('td').eq(0).text().trim());
                $('#eqty').val(row.children('td').eq(1).text());
                $('#eprice').val(row.children('td').eq(3).text());
                $('#evat').val(row.children('td').eq(5).text());
                $('#err_id').val(row.attr('id'));
                $('#egood_id').empty();
                $('#ecomment').empty();
            }
        }, ".pos_edit");

        $(document).on({
            click: function () {
                let id = $(this).parent().parent().parent().attr("id");
                let x = confirm("Выбранная запись будет удалена. Продолжить (Да/Нет)?");
                if (x) {
                    $.ajax({
                        type: 'POST',
                        url: '{{ route('delOrderPos') }}',
                        data: {'id': id},
                        headers: {
                            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (res) {
                            //alert(res);
                            if (res == 'BAD')
                                alert('Выполнение операции запрещено!');
                            if (res == 'NO')
                                alert('Не известный метод!');
                            if (res == 'LINK')
                                alert('Данную позицию удалить нельзя, т.к. она присутствует в связанных документах!');
                            let obj = jQuery.parseJSON(res);
                            if (typeof obj === 'object') {
                                $('#state').text('Всего позиций: ' + obj.num + ' на сумму с НДС ' + obj.amount + ' руб.');
                                $('#rem').text('Заказано с НДС: ' + obj.amount + ' руб.');
                                $('#' + id).hide();
                            }
                        }
                    });
                } else {
                    return false;
                }
            }
        }, ".pos_delete");

        $(document).on({
            click: function () {
                let id = $(this).parent().parent().parent().attr("id");
                let x = confirm("Выбранная запись будет удалена. Продолжить (Да/Нет)?");
                if (x) {
                    $.ajax({
                        type: 'POST',
                        url: '{{ route('delErrPos') }}',
                        data: {'id': id},
                        headers: {
                            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (res) {
                            //alert(res);
                            if (res == 'OK') {
                                hide_row(id);
                            }
                            if (res == 'BAD')
                                alert('Выполнение операции запрещено!');
                            if (res == 'NO')
                                alert('Не известный метод!');
                        }
                    });
                } else {
                    return false;
                }
            }
        }, ".err_delete");

        function hide_row(id) {
            let err = Number($('.badge-danger').text());
            err--;
            $('.badge-danger').text(err.toString());
            $('#' + id).hide();
        }

    </script>
@endsection
