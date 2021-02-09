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
    <!-- New Order Modal -->
    <div class="modal fade" id="editDoc" tabindex="-1" role="dialog" aria-labelledby="editDoc"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times-circle fa-lg" aria-hidden="true"></i>
                    </button>
                    <h4 class="modal-title">Заказ поставщику</h4>
                </div>
                <div class="modal-body">
                    {!! Form::open(['url' => '#','id'=>'add_order','class'=>'form-horizontal','method'=>'POST']) !!}
                    {!! Form::hidden('application_id',$application->id,['class' => 'form-control','id'=>'id_doc','required'=>'required']) !!}

                    <div class="form-group">
                        <div class="col-xs-3 control-label">
                            Организация: <span class="symbol required" aria-required="true"></span>
                        </div>
                        <div class="col-xs-9">
                            {!! Form::select('organisation_id',$orgsel, old('organisation_id'), ['class' => 'form-control','required'=>'required','id'=>'org_id']); !!}
                        </div>

                    </div>

                    <div class="form-group">
                        <div class="col-xs-3 control-label">
                            Склад: <span class="symbol required" aria-required="true"></span>
                        </div>
                        <div class="col-xs-9">
                            {!! Form::select('warehouse_id',$wxsel, old('warehouse_id'), ['class' => 'form-control','required'=>'required']); !!}
                        </div>

                    </div>

                    <div class="form-group">
                        <div class="col-xs-3 control-label">
                            Поставщик: <span class="symbol required" aria-required="true"></span>
                        </div>
                        <div class="col-xs-9">
                            {!! Form::text('firm_id',old('firm_id'),['class' => 'form-control','placeholder'=>'Начинайте вводить наименование поставщика','required'=>'required','id'=>'search_firm'])!!}
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-3 control-label">
                            Договор: <span class="symbol required" aria-required="true"></span>
                        </div>
                        <div class="col-xs-9">
                            {!! Form::select('contract_id',[], old('contract_id'), ['class' => 'form-control','required'=>'required','id'=>'contract']); !!}
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-3 control-label">
                            Статус: <span class="symbol required" aria-required="true"></span>
                        </div>
                        <div class="col-xs-9">
                            {!! Form::select('statuse_id',$statsel, old('statuse_id'), ['class' => 'form-control','required'=>'required']); !!}
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-3 control-label">
                            Хоз. операция: <span class="symbol required" aria-required="true"></span>
                        </div>
                        <div class="col-xs-9">
                            {!! Form::select('hoperation_id',$hopsel, old('hoperation_id'), ['class' => 'form-control','required'=>'required','id'=>'hoperation']); !!}
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-3 control-label">
                            Валюта: <span class="symbol required" aria-required="true"></span>
                        </div>
                        <div class="col-xs-9">
                            {!! Form::select('currency_id',$cursel, old('currency_id'), ['class' => 'form-control','required'=>'required','id'=>'curr_id']); !!}
                            <div class="checkbox clip-check check-primary">
                                <input type="checkbox" name="has_vat" id="has_vat" checked value="1">
                                <label for="has_vat">
                                    Цена включает НДС
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('comment', 'Комментарий:',['class'=>'col-xs-3 control-label']) !!}
                        <div class="col-xs-9">
                            {!! Form::textarea('comment',old('comment'),['class'=>'form-control', 'rows' => 2, 'cols' => 50]) !!}
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
    <!-- New Order Modal -->
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
                                <!--                                    <a href="#">
                                        <button type="button" class="btn btn-primary btn-sm btn-o" id="new_pos"
                                                data-toggle="modal" data-target="#editDoc">
                                            <i class="fa fa-plus" aria-hidden="true"></i> Добавить
                                        </button>
                                    </a>-->
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
                                                <a href="#" data-toggle="modal" data-target="#editDoc">
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
                                            <th>Артикул</th>
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
                                                    <td>{{ $row->good->vendor_code }}</td>
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
        $("#org_id").prepend($('<option value="0">Выберите организацию</option>'));
        $("#org_id :first").attr("selected", "selected");
        $("#org_id :first").attr("disabled", "disabled");

        $("#curr_id").prepend($('<option value="0">Выберите валюту</option>'));
        $("#curr_id :first").attr("selected", "selected");
        $("#curr_id :first").attr("disabled", "disabled");
        $("#hoperation :contains('Закупка у поставщика')").attr("selected", "selected");
        $('.offer_pos').css('cursor', 'pointer');

        $('#search_firm').typeahead({
            hint: true,
            highlight: true,
            minLength: 3,
            ajax: {
                url: "{{ route('getFirm') }}",
                triggerLength: 1
            }
        });

        $( "#search_firm" ).blur(function() {
            $("#contract").empty(); //очищаем от старых значений
            var firm = $("#search_firm").val();
            var org_id = $("#org_id option:selected").val();
            $.ajax({
                type: 'POST',
                url: '{{ route('findContract') }}',
                data: {'firm': firm,'org_id':org_id},
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    //alert(res);
                    $("#contract").prepend($(res));
                }
            });
        });


        $('#search').typeahead({
            hint: true,
            highlight: true,
            minLength: 3,
            ajax: {
                url: "{{ route('getSale') }}",
                triggerLength: 1
            }
        });

        $('#curr_id').change(function(){
            let rub = $('#curr_id option:selected').text();
            if(rub.indexOf('рубль')){
                $("#hoperation option:contains('Импорт')").prop("selected", true);
                $('#has_vat').prop('checked', false);
                $('#has_vat').val('0');
            }
            if(rub.indexOf('рубль')>0){
                $('#has_vat').prop('checked', true);
                $('#has_vat').val('1');
                $('#hoperation option').prop('selected', false);
                $('#hoperation option:contains("Закупка у поставщика")').prop("selected", true);
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
            $("#add_order").find(":input").each(function () {// проверяем каждое поле ввода в форме
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
                    url: '{{ route('newOrder') }}',
                    data: $('#add_order').serialize(),
                    success: function (res) {
                        //alert(res);
                        $(".modal").modal("hide");
                        if (res == 'BAD') {
                            alert('У Вас нет прав для создания документа!');
                        }
                        if (res == 'NO') {
                            alert('Не известный запрос!');
                        }
                        if (res == 'DBL') {
                            alert('Заявка поставщику уже была создана ранее!');
                        }
                        if (res == 'OK') {
                            alert('Заявка поставщику создана!');
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
                let ofr_id = $(this).parent().attr("id").substr(3);
                $.ajax({
                    type: 'POST',
                    url: '{{ route('setPosPrice') }}',
                    data: {'id':id, 'price':price, 'ofr_id':ofr_id},
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
