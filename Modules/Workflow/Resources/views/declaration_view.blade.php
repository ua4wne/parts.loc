@extends('layouts.main')

@section('dashboard')

@endsection
@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li><a href="{{ route('declarations') }}">{{ $title }}</a></li>
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
    <!-- Error Position Modal -->
    <div class="modal fade" id="editPoc" tabindex="-1" role="dialog" aria-labelledby="editPos"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times-circle fa-lg" aria-hidden="true"></i>
                    </button>
                    <h4 class="modal-title">Редактирование позиции</h4>
                </div>
                <div class="modal-body">
                    {!! Form::open(['url' => '#','id'=>'edit_pos','class'=>'form-horizontal','method'=>'POST']) !!}

                    <div class="form-group">
                        {!! Form::label('good_id', 'Наименование:',['class'=>'col-xs-3 control-label']) !!}
                        <div class="col-xs-8">
                            {!! Form::text('good_id', '', ['class' => 'form-control','disabled'=>'disabled','id'=>'egood'])!!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('amount','Таможенная стоимость:',['class' => 'col-xs-3 control-label'])   !!}
                        <div class="col-xs-8">
                            {!! Form::text('amount','',['class' => 'form-control','placeholder'=>'Укажите стоимость','required'=>'required','id'=>'eamount'])!!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('duty','Сумма пошлины:',['class' => 'col-xs-3 control-label'])   !!}
                        <div class="col-xs-8">
                            {!! Form::text('duty','',['class' => 'form-control','placeholder'=>'Укажите сумму','required'=>'required','id'=>'eduty'])!!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('vat','Сумма НДС:',['class' => 'col-xs-3 control-label'])   !!}
                        <div class="col-xs-8">
                            {!! Form::text('vat','',['class' => 'form-control','placeholder'=>'Укажите сумму','required'=>'required','id'=>'evat'])!!}
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-8">
                            {!! Form::hidden('pos_id','',['class' => 'form-control','id'=>'pos_id','required'=>'required']) !!}
                        </div>
                    </div>

                    {!! Form::close() !!}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal" id="eclose">Отмена
                    </button>
                    <button type="button" class="btn btn-primary" id="edit_btn">Сохранить
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Error Position Modal -->
    <!-- New Doc Modal -->
    <div class="modal fade" id="newDoc" tabindex="-1" role="dialog" aria-labelledby="newDoc"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times-circle fa-lg" aria-hidden="true"></i>
                    </button>
                    <h4 class="modal-title">Приобретение товаров и услуг</h4>
                </div>
                <div class="modal-body">
                    {!! Form::open(['url' => '#','id'=>'add_pos','class'=>'form-horizontal','method'=>'POST']) !!}

                    <div class="form-group">
                        <div class="col-xs-12">
                            <fieldset>
                                <legend>Выбор документов:</legend>
                                <div class="form-group">
                                    {!! Form::label('from_docs', '№ документа:',['class'=>'col-xs-3 control-label']) !!}
                                    <div class="col-xs-8">
                                        {!! Form::select('from_docs[]',[], '', ['class' => 'form-control','id'=>'from_docs','multiple'=>'multiple','size'=>5,'required'=>'required'])!!}
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-8">
                            {!! Form::hidden('declaration_id',$declaration->id,['class' => 'form-control','id'=>'id_doc','required'=>'required']) !!}
                        </div>
                    </div>

                    {!! Form::close() !!}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal" id="close">Отмена
                    </button>
                    <button type="button" class="btn btn-primary" id="new_btn">Выбрать
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- New Doc Modal -->
    <!-- Del Doc Modal -->
    <div class="modal fade" id="delDoc" tabindex="-1" role="dialog" aria-labelledby="delDoc"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times-circle fa-lg" aria-hidden="true"></i>
                    </button>
                    <h4 class="modal-title">Удаление приобретений из декларации</h4>
                </div>
                <div class="modal-body">
                    {!! Form::open(['url' => '#','id'=>'del_doc','class'=>'form-horizontal','method'=>'POST']) !!}

                    <div class="form-group">
                        <div class="col-xs-12">
                            <fieldset>
                                <legend>Выбор документов:</legend>
                                <div class="form-group">
                                    {!! Form::label('id_docs', '№ документа:',['class'=>'col-xs-3 control-label']) !!}
                                    <div class="col-xs-8">
                                        {!! Form::select('id_docs[]',[], '', ['class' => 'form-control','id'=>'id_docs','multiple'=>'multiple','size'=>5,'required'=>'required'])!!}
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-8">
                            {!! Form::hidden('declaration_id',$declaration->id,['class' => 'form-control','id'=>'declaration_id','required'=>'required']) !!}
                        </div>
                    </div>

                    {!! Form::close() !!}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                    <button type="button" class="btn btn-primary" id="del_doc_btn">Удалить</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Del Doc Modal -->
    <div class="container-fluid container-fullw bg-white">
        <div class="row">
            <div class="col-md-12">
                <h2 class="text-center">{{ $head }}</h2>
                <h4 class="text-center">дата создания {{ $declaration->created_at }}</h4>
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
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade in active" id="common">
                            {!! Form::open(['url' => route('declarationEdit',['id'=>$declaration->id]),'class'=>'form-horizontal','method'=>'POST', 'id'=>'form_ref']) !!}
                            <table class="table table-bordered table-hover">
                                <tr>
                                    <th>Номер документа: <span class="symbol required" aria-required="true"></span></th>
                                    <td>{!! Form::text('doc_num',$declaration->doc_num,['class' => 'form-control','placeholder'=>'Введите номер документа','maxlength'=>'15','required'=>'required'])!!}
                                        {!! $errors->first('doc_num', '<p class="text-danger">:message</p>') !!}
                                    </td>
                                    <th>
                                        Номер декларации: <span class="symbol required" aria-required="true"></span>
                                    </th>
                                    <td>
                                        {!! Form::text('declaration_num',$declaration->declaration_num,['class' => 'form-control','placeholder'=>'Введите номер декларации','maxlength'=>'30','required'=>'required','id'=>'declaration_num'])!!}
                                        {!! $errors->first('firm_id', '<p class="text-danger">:message</p>') !!}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        Организация: <span class="symbol required" aria-required="true"></span>
                                    </th>
                                    <td>
                                        {!! Form::select('organisation_id',$orgsel, $declaration->organisation_id, ['class' => 'form-control','required'=>'required','id'=>'org_id']); !!}
                                        {!! $errors->first('organisation_id', '<p class="text-danger">:message</p>') !!}
                                    </td>
                                    <th>
                                        Поставщик: <span class="symbol required" aria-required="true"></span>
                                    </th>
                                    <td>
                                        {!! Form::text('firm_id',$declaration->firm->name,['class' => 'form-control','placeholder'=>'Начинайте вводить наименование поставщика','required'=>'required','id'=>'search_firm'])!!}
                                        {!! $errors->first('firm_id', '<p class="text-danger">:message</p>') !!}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        ГТД оформляется: <span class="symbol required" aria-required="true"></span>
                                    </th>
                                    <td>
                                        {!! Form::select('who_register',['broker'=>'Таможенным брокером','yourself'=>'Самостоятельно'], $declaration->who_register, ['class' => 'form-control','required'=>'required']); !!}
                                        {!! $errors->first('who_register', '<p class="text-danger">:message</p>') !!}
                                    </td>
                                    <th>
                                        Брокер/Таможня: <span class="symbol required" aria-required="true"></span>
                                    </th>
                                    <td>
                                        {!! Form::text('broker_id', $declaration->broker->name, ['class' => 'form-control','placeholder'=>'Начинайте вводить наименование брокера','required'=>'required','id'=>'broker_search']); !!}
                                        {!! $errors->first('broker_id', '<p class="text-danger">:message</p>') !!}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        Валюта: <span class="symbol required" aria-required="true"></span>
                                    </th>
                                    <td>{!! Form::select('currency_id',$cursel, $declaration->currency_id, ['class' => 'form-control','required'=>'required','id'=>'curr_id']); !!}
                                        {!! $errors->first('currency', '<p class="text-danger">:message</p>') !!}
                                    </td>
                                    <th>
                                        Договор:
                                    </th>
                                    <td>
                                        {!! Form::select('contract_id',$contsel, $declaration->contract_id, ['class' => 'form-control','id'=>'contract']); !!}
                                        {!! $errors->first('contract_id', '<p class="text-danger">:message</p>') !!}
                                    </td>

                                </tr>
                                <tr>
                                    <th>Таможенный сбор: <span class="symbol required" aria-required="true"></span></th>
                                    <td>
                                        {!! Form::text('tax',$declaration->tax,['class'=>'form-control', 'required' => 'required','maxlength'=>'10','placeholder'=>'Укажите сумму','id'=>'tax']) !!}
                                        {!! $errors->first('tax', '<p class="text-danger">:message</p>') !!}
                                    </td>
                                    <th>Таможенный штраф:</th>
                                    <td>
                                        {!! Form::text('fine',$declaration->fine,['class'=>'form-control','maxlength'=>'10','placeholder'=>'Укажите сумму','id'=>'fine']) !!}
                                        {!! $errors->first('fine', '<p class="text-danger">:message</p>') !!}
                                    </td>

                                </tr>
                                <tr>
                                    <th>
                                        Статья расходов: <span class="symbol required" aria-required="true"></span>
                                    </th>
                                    <td>
                                        {!! Form::select('expense_id',$expsel, $declaration->expense_id, ['class' => 'form-control','required'=>'required','id'=>'expense_id']); !!}
                                        {!! $errors->first('expense_id', '<p class="text-danger">:message</p>') !!}
                                    </td>
                                    <th>
                                        Страна происхождения: <span class="symbol required" aria-required="true"></span>
                                    </th>
                                    <td>
                                        {!! Form::select('country_id',$contrsel, $declaration->country_id, ['class' => 'form-control','required'=>'required','id'=>'country_id']); !!}
                                        {!! $errors->first('country_id', '<p class="text-danger">:message</p>') !!}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        Таможенная стоимость: <span class="symbol required" aria-required="true"></span>
                                    </th>
                                    <td>
                                        {!! Form::text('cost',$declaration->cost, ['class' => 'form-control','required'=>'required','maxlength'=>'10','id'=>'cost']); !!}
                                        {!! $errors->first('cost', '<p class="text-danger">:message</p>') !!}
                                    </td>
                                    <th>
                                        Менеджер: <span class="symbol required" aria-required="true"></span>
                                    </th>
                                    <td>
                                        {!! Form::select('user_id',$usel, $declaration->user_id, ['class' => 'form-control','required'=>'required','id'=>'user_id']); !!}
                                        {!! $errors->first('user_id', '<p class="text-danger">:message</p>') !!}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        Ставка пошлины: <span class="symbol required" aria-required="true"></span>
                                    </th>
                                    <td>
                                        {!! Form::text('rate',$declaration->rate, ['class' => 'form-control','required'=>'required','maxlength'=>'10','id'=>'rate']); !!}
                                        {!! $errors->first('rate', '<p class="text-danger">:message</p>') !!}
                                    </td>
                                    <th>
                                        Сумма пошлины:
                                    </th>
                                    <td>
                                        {!! Form::text('amount',$declaration->amount, ['class' => 'form-control','id'=>'amount']); !!}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        Ставка НДС:
                                    </th>
                                    <td>
                                        {!! Form::text('vat',$declaration->vat, ['class' => 'form-control','id'=>'vat','required'=>'required','maxlength'=>'3']); !!}
                                    </td>
                                    <th>
                                        Сумма НДС:
                                    </th>
                                    <td>
                                        {!! Form::text('vat_amount',$declaration->vat_amount, ['class' => 'form-control','id'=>'vat-amount']); !!}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Комментарий:</th>
                                    <td colspan="3">
                                        {!! Form::textarea('comment',$declaration->comment,['class'=>'form-control', 'rows' => 2, 'cols' => 50]) !!}
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
                                <a href="#">
                                    <button type="button" class="btn btn-primary btn-sm btn-o" id="distribute_cost">
                                        <i class="fa fa-refresh" aria-hidden="true"></i> Распределить стоимость
                                    </button>
                                </a>
                            </div>

                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>Таможенная стоимость:</th>
                                            <td><u class="text-bold text-info">{{ $declaration->cost }}</u></td>
                                            <th>Ставка пошлины:</th>
                                            <td><u class="text-bold text-danger">{{ $declaration->rate }}</u></td>
                                            <th>Сумма пошлины:</th>
                                            <td><u class="text-bold text-info">{{ $declaration->amount }}</u></td>
                                            <th>Ставка НДС:</th>
                                            <td><u class="text-bold text-danger">{{ $declaration->vat }}</u></td>
                                            <th>Сумма НДС:</th>
                                            <td><u class="text-bold text-info">{{ $declaration->vat_amount }}</u></td>
                                            <th>№ декларации для с\ф:</th>
                                            <td>{{ $declaration->declaration_num }}</td>
                                        </tr>
                                    </table>
                                    <hr>
                                    <div class="panel-heading">
                                        <a href="#">
                                            <button type="button" class="btn btn-primary btn-sm btn-o" id="new_pos"
                                                    data-toggle="modal" data-target="#newDoc">
                                                <i class="fa fa-file-text-o" aria-hidden="true"></i> Добавить из
                                                приобретений
                                            </button>
                                        </a>
                                        <a href="#">
                                            <button type="button" class="btn btn-danger btn-sm btn-o" id="rem_doc"
                                                    data-toggle="modal" data-target="#delDoc">
                                                <i class="fa fa-ban" aria-hidden="true"></i> Удалить из
                                                приобретений
                                            </button>
                                        </a>
                                    </div>
                                    <table id="doc_table" class="table table-striped table-bordered">
                                        <thead>
                                        <tr>
                                            <th>Номенклатура</th>
                                            <th>Характеристика</th>
                                            <th>Кол-во</th>
                                            <th>Ед.изм</th>
                                            <th>Таможенная стоимость</th>
                                            <th>Сумма пошлины</th>
                                            <th>Сумма НДС</th>
                                            <th>Документ-основание</th>
                                            <th>Действия</th>
                                        </tr>
                                        </thead>
                                        @if($rows)
                                            <tbody id="t_body">
                                            @foreach($rows as $k => $row)
                                                <tr id="{{ $row->id }}">
                                                    <td>{{ $row->good->title }}</td>
                                                    <td>{{ $row->comment }}</td>
                                                    <td>{{ $row->qty }}</td>
                                                    <td>{{ $row->unit->title }}</td>
                                                    <td>{{ $row->amount }}</td>
                                                    <td>{{ $row->duty }}</td>
                                                    <td>{{ $row->vat }}</td>
                                                    <td><a href="/purchases/view/{{ $row->purchase->id }}"
                                                           target="_blank">{{ $row->purchase->doc_num }}
                                                            от {{$row->purchase->created_at}}</a></td>
                                                    <td style="width:70px;">
                                                        <div class="form-group" role="group">
                                                            <button class="btn btn-info btn-sm pos_edit"
                                                                    type="button" title="Редактировать позицию"><i
                                                                    class="fa fa-edit fa-lg" aria-hidden="true"></i>
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
        $('#search_firm').typeahead({
            hint: true,
            highlight: true,
            minLength: 3,
            ajax: {
                url: "{{ route('getFirm') }}",
                triggerLength: 1
            }
        });

        $('#broker_search').typeahead({
            hint: true,
            highlight: true,
            minLength: 3,
            ajax: {
                url: "{{ route('getFirm') }}",
                triggerLength: 1
            }
        });

        $("#broker_search").blur(function () {
            $("#contract").empty(); //очищаем от старых значений
            var firm = $("#search_firm").val();
            var org_id = $("#org_id option:selected").val();
            $.ajax({
                type: 'POST',
                url: '{{ route('findContract') }}',
                data: {'firm': firm, 'org_id': org_id},
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    //alert(res);
                    $("#contract").prepend($(res));
                }
            });
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
                    url: '{{ route('addDeclarationPos') }}',
                    data: $('#add_pos').serialize(),
                    success: function (res) {
                        //alert(res);
                        if (res == 'BAD') {
                            alert('У Вас нет прав для редактирования документа!')
                        }
                        else if (res == 'NO') {
                            alert('Не известный запрос!')
                        }
                        else {
                            $("#doc_table").empty();
                            $("#doc_table").append(res);
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

        $('#eclose').click(function (e) {
            e.preventDefault();
            $('#egood').val('');
            $('#eamount').val('');
            $('#eduty').val('');
            $('#evat').val('');
            $('#pos_id').val('');
        });

        $("#new_pos").click(function (e) {
            e.preventDefault();
            $("#from_docs").empty(); //очищаем от старых значений
            $.ajax({
                type: 'GET',
                url: '{{ route('searchPurchases') }}',
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    //alert(res);
                    $("#from_docs").prepend($(res));
                }
            });
        });

        $("#rem_doc").click(function (e) {
            e.preventDefault();
            let doc_id = $('#declaration_id').val();
            $("#id_docs").empty(); //очищаем от старых значений
            $.ajax({
                type: 'POST',
                url: '{{ route('getPurchaseFromDeclaration') }}',
                data: {'id': doc_id},
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    //alert(res);
                    $("#id_docs").prepend($(res));
                }
            });
        });

        $("#del_doc_btn").click(function (e) {
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: '{{ route('delPosPurchase') }}',
                data: $('#del_doc').serialize(),
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    //alert(res);
                    if(res = 'OK'){
                        window.location.reload();
                    }
                    if (res == 'BAD')
                        alert('Выполнение операции запрещено!');
                    if (res == 'NO')
                        alert('Не известный метод!');
                }
            });
        });

        $('#edit_btn').click(function () {
            let error = 0;
            let id = $('#pos_id').val();
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
                    url: '{{ route('DeclarationPosEdit') }}',
                    data: $('#edit_pos').serialize(),
                    success: function (res) {
                        //alert(res);
                        if (res = 'OK') {
                            $('#' + id).children('td').eq(4).text($('#eamount').val());
                            $('#' + id).children('td').eq(5).text($('#eduty').val());
                            $('#' + id).children('td').eq(6).text($('#evat').val());
                        }
                        if (res == 'BAD')
                            alert('Выполнение операции запрещено!');
                        if (res == 'NO')
                            alert('Не известный метод!');
                        if (res == 'ERR') {
                            alert('Ошибка при обновлении данных!');
                        }
                        $(".modal").modal("hide");
                    }
                });
            }
        });

        $(document).on({
            click: function () {
                let row = $(this).parent().parent().parent();
                $("#editPoc").modal("show");
                $('#egood').val(row.children('td').eq(0).text());
                $('#eamount').val(row.children('td').eq(4).text());
                $('#eduty').val(row.children('td').eq(5).text());
                $('#evat').val(row.children('td').eq(6).text());
                $('#pos_id').val(row.attr('id'));
            }
        }, ".pos_edit");

        $('#distribute_cost').click(function (e) {
            e.preventDefault();
            let doc_id = $('#id_doc').val();
            let cost = $('#cost').val();
            let amount = $('#amount').val();
            let vat_amount = $('#vat-amount').val();
            $.ajax({
                type: 'POST',
                url: '{{ route('CostAllocation') }}',
                data: {'id': doc_id,'cost': cost, 'amount': amount, 'vat_amount': vat_amount},
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    //alert(res);
                    if(res = 'OK'){
                        window.location.reload();
                    }
                    if (res == 'BAD')
                        alert('Выполнение операции запрещено!');
                    if (res == 'NO')
                        alert('Не известный метод!');
                }
            });
        });

    </script>
@endsection
