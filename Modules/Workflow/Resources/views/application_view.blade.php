@extends('layouts.main')
@section('user_css')

@endsection
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
                            {!! Form::select('unit_id',$unsel,old('unit_id'),['class' => 'form-control','required'=>'required','id'=>'unit_id'])!!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('car_id', 'Техника:',['class'=>'col-xs-3 control-label']) !!}
                        <div class="col-xs-8">
                            {!! Form::select('car_id',$carsel,old('car_id'),['class' => 'form-control','required'=>'required','id'=>'car_id'])!!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('price','Цена:',['class' => 'col-xs-3 control-label'])   !!}
                        <div class="col-xs-8">
                            {!! Form::text('price','',['class' => 'form-control','placeholder'=>'Укажите цену', 'id'=>'price'])!!}
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
    <!-- Offer Modal -->
    <div class="modal fade" id="getOffer" tabindex="-1" role="dialog" aria-labelledby="getOffer"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times-circle fa-lg" aria-hidden="true"></i>
                    </button>
                    <h4 class="modal-title">Запросить цены у поставщиков</h4>
                </div>
                <div class="modal-body">
                    {!! Form::open(['url' => route('getOfferPrice'),'id'=>'get_offer','class'=>'form-horizontal','method'=>'POST']) !!}

                    <div class="form-group">
                        {!! Form::label('firm_id[]', 'Поставщики:',['class'=>'col-xs-3 control-label']) !!}
                        <div class="col-xs-8">
                            {!! Form::select('firm_id[]', $firmsel, old('firm_id'), ['class' => 'form-control',
                            'required'=>'required','multiple','size'=>'3','id'=>'firms_id'])!!}
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-offset-3 col-xs-6">
                            <div class="checkbox clip-check check-primary">
                                <input type="checkbox" id="all" value="1">
                                <label for="all">
                                    Выбрать всех поставщиков
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-8">
                            {!! Form::hidden('application_id',$application->id,['class' => 'form-control','id'=>'id_doc','required'=>'required']) !!}
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Отмена
                    </button>
                    {!! Form::submit('Запросить цены',['class'=>'btn btn-primary','id'=>'get_offer_btn']) !!}
                    </button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    <!-- Offer Modal -->
    <!-- Load Offer Modal -->
    <div class="modal fade" id="LoadOffer" tabindex="-1" role="dialog" aria-labelledby="LoadOffer"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times-circle fa-lg" aria-hidden="true"></i>
                    </button>
                    <h4 class="modal-title">Загрузить предложенные цены</h4>
                </div>
                <div class="modal-body">
                    {!! Form::open(['url' => '#','id'=>'import_price','class'=>'form-horizontal','method'=>'POST','files'=>'true']) !!}

                    <div class="form-group">
                        {!! Form::label('firm_id', 'Поставщик:',['class'=>'col-xs-3 control-label']) !!}
                        <div class="col-xs-8">
                            {!! Form::select('firm_id', $firmsel, old('firm_id'), ['class' => 'form-control',
                            'required'=>'required','id'=>'firm_id'])!!}
                        </div>
                    </div>

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
                    <button type="button" class="btn btn-default" data-dismiss="modal">Отмена
                    </button>
                    <button type="button" class="btn btn-primary" id="load_offer_btn">Обновить цены
                    </button>
                    <button type="button" class="btn btn-success" id="set_offer">Применить текущие
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Load Offer Modal -->
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
                                    <a href="#">
                                        <button type="button" class="btn btn-primary btn-sm btn-o" id="get_offer"
                                                data-toggle="modal" data-target="#getOffer">
                                            <i class="fa fa-envelope-o" aria-hidden="true"></i> Запросить цены
                                        </button>
                                    </a>
                                    <a href="#">
                                        <button type="button" class="btn btn-primary btn-sm btn-o" id="load_offer"
                                                data-toggle="modal" data-target="#LoadOffer">
                                            <i class="fa fa-download" aria-hidden="true"></i> Загрузить цены
                                        </button>
                                    </a>
                                    <a href="#">
                                        <button type="button" class="btn btn-success btn-sm btn-o" id="close_app">
                                            <i class="fa fa-check" aria-hidden="true"></i> Закрыть заявку
                                        </button>
                                    </a>
                                    <a href="#">
                                        <button type="button" class="btn btn-danger btn-sm btn-o" id="del_app">
                                            <i class="fa fa-trash" aria-hidden="true"></i> Отменить заявку
                                        </button>
                                    </a>
                                @else
                                    <a href="#">
                                        <button type="button" class="btn btn-warning btn-sm btn-o" id="open_app">
                                            <i class="fa fa-reply" aria-hidden="true"></i> Открыть заявку
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
                                            <th>Техника</th>
                                            <th>Цена продажи</th>
                                            <th>Предложения поставщиков</th>
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
                                                    <td>{{ $row->car->title }}</td>
                                                    <td>{{ $row->price }}</td>
                                                    <td>{!! $row->offers !!}</td>
                                                    <td style="width:70px;">
                                                        @if($row->application->state == 0)
                                                            <div class="form-group" role="group">
                                                                <button class="btn btn-danger btn-sm pos_delete"
                                                                        type="button" title="Удалить позицию"><i
                                                                        class="fa fa-trash fa-lg"
                                                                        aria-hidden="true"></i>
                                                                </button>
                                                            </div>
                                                        @endif
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
    <script>
        $("#car_id").prepend($('<option value="0">Выберите технику</option>'));
        $("#car_id :first").attr("selected", "selected");
        $("#car_id :first").attr("disabled", "disabled");

        $("#currency_id").prepend($('<option value="0">Выберите валюту</option>'));
        $("#currency_id :first").attr("selected", "selected");
        $("#currency_id :first").attr("disabled", "disabled");
        $('.offer_pos').css('cursor', 'pointer');

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

        $('#all').on('change', function () {
            if ($('#all').prop('checked')) {
                $('#firms_id option').each(function () {
                    $(this).prop("selected", true);
                });
            } else {
                $('#firms_id option').each(function () {
                    $(this).prop("selected", false);
                });
            }
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

        $('#load_offer_btn').click(function (e) {
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
                formData.append('firm_id', $('#firm_id').val());
                formData.append('app_id', $('#id_doc').val());
                $.ajax({
                    type: 'POST',
                    url: '{{ route('setOfferPrice') }}',
                    processData: false,
                    contentType: false,
                    cache: false,
                    dataType: 'text',
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
                            alert('Обработано строк ' + obj.num + ' из ' + obj.rows + '!');
                            location.reload();
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        alert(xhr.status + '\n' + thrownError);
                    }
                });
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
                        if (res == 'NOVALIDATE') {
                            alert('Не корректные значения формы!')
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

        $('#get_offer_btn').click(function () {
            let error = 0;
            $("#get_offer").find(":input").each(function () {// проверяем каждое поле ввода в форме
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
                alert("Не выбран поставщик!");
                return false;
            } else {
                return true;
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

        $('#del_app').click(function (e) {
            e.preventDefault();
            let id = $('#id_doc').val();
            let x = confirm("Выбранная заявка будет удалена. Продолжить (Да/Нет)?");
            if (x) {
                $.ajax({
                    type: 'POST',
                    url: '{{ route('applicationDelete') }}',
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
                            alert('Данную заявку удалить нельзя, т.к. она присутствует в связанных документах!');
                        if (res == 'OK')
                            window.location.replace('/applications');
                    }
                });
            } else {
                return false;
            }
        });

        $('#close_app').click(function (e) {
            e.preventDefault();
            let id = $('#id_doc').val();
            let x = confirm("Выбранная заявка будет закрыта. Продолжить (Да/Нет)?");
            if (x) {
                $.ajax({
                    type: 'POST',
                    url: '{{ route('applicationClose') }}',
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
                        if (res == 'OK') {
                            alert('Заявка закрыта!');
                            window.location.reload(true);
                        }
                    }
                });
            } else {
                return false;
            }
        });

        $('#open_app').click(function (e) {
            e.preventDefault();
            let id = $('#id_doc').val();
            let x = confirm("Выбранная заявка будет открыта. Продолжить (Да/Нет)?");
            if (x) {
                $.ajax({
                    type: 'POST',
                    url: '{{ route('applicationOpen') }}',
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
                        if (res == 'OK') {
                            alert('Заявка открыта!');
                            window.location.reload(true);
                        }
                    }
                });
            } else {
                return false;
            }
        });

        $(document).on({
            click: function () {
                let id = $(this).parent().parent().parent().attr("id");
                let x = confirm("Выбранная запись будет удалена. Продолжить (Да/Нет)?");
                if (x) {
                    $.ajax({
                        type: 'POST',
                        url: '{{ route('delApplicationPos') }}',
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
                            if (res == 'OK')
                                $('#' + id).hide();
                            if (res == 'NOT')
                                alert('Нельзя удалить позиции из закрытой заявки!');
                        }
                    });
                } else {
                    return false;
                }
            }
        }, ".pos_delete");

        $(document).on({
            click: function () {
                let id = $(this).parent().parent().parent().parent().parent().attr("id");
                let price = $(this).text();
                $.ajax({
                    type: 'POST',
                    url: '{{ route('setPosPrice') }}',
                    data: {'id': id, 'price': price},
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        //alert(res);
                        if (res == 'BAD')
                            alert('Выполнение операции запрещено!');
                        if (res == 'NO')
                            alert('Не известный метод!');
                        if (res == 'OK')
                            $('#' + id).children().eq(5).text(price);
                    }
                });
            }
        }, ".offer_pos");

        $(document).on({
            click: function () {
                let id = $(this).parent().parent().attr("id");
                let comment = $(this).parent().prev();
                let result = prompt("Введите комментарий", comment.text());
                if (result === null) {
                    return; //break out of the function early
                }
                $.ajax({
                    type: 'POST',
                    url: '{{ route('setPosComment') }}',
                    data: {'id': id, 'comment': result},
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        //alert(res);
                        if (res == 'BAD')
                            alert('Выполнение операции запрещено!');
                        if (res == 'NO')
                            alert('Не известный метод!');
                        if (res == 'OK')
                            comment.text(result);
                    }
                });
            }
        }, ".btn-xs");

    </script>
@endsection
