@extends('layouts.main')

@section('dashboard')

@endsection
@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li><a href="{{ route('purchases') }}">{{ $title }}</a></li>
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
                        {!! Form::label('vendor_code', 'Артикул:',['class'=>'col-xs-3 control-label']) !!}
                        <div class="col-xs-8">
                            {!! Form::text('vendor_code', '', ['class' => 'form-control','disabled'=>'disabled','id'=>'evendor'])!!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('good_id', 'Наименование:',['class'=>'col-xs-3 control-label']) !!}
                        <div class="col-xs-8">
                            {!! Form::text('good_id', '', ['class' => 'form-control','disabled'=>'disabled','id'=>'egood'])!!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('analog_code', 'Аналог:',['class'=>'col-xs-3 control-label']) !!}
                        <div class="col-xs-8">
                            {!! Form::text('analog_code', '', ['class' => 'form-control','placeholder'=>'Начинайте вводить код аналога','id'=>'by_analog'])!!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('sub_good_id', 'Наименование:',['class'=>'col-xs-3 control-label']) !!}
                        <div class="col-xs-8">
                            {!! Form::select('sub_good_id', [], '',['class' => 'form-control','id'=>'sub_good']); !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('qty','Количество:',['class' => 'col-xs-3 control-label'])   !!}
                        <div class="col-xs-8">
                            {!! Form::number('qty','',['class' => 'form-control','placeholder'=>'Введите количество','required'=>'required','min' => 1, 'max' => 1000,'id'=>'eqty'])!!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('unit_id', 'Ед. измерения:',['class'=>'col-xs-3 control-label']) !!}
                        <div class="col-xs-8">
                            {!! Form::select('unit_id', $unsel, old('unit_id'),['class' => 'form-control','required' => 'required','id'=>'eunit']); !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('price2','Цена:',['class' => 'col-xs-3 control-label'])   !!}
                        <div class="col-xs-8">
                            {!! Form::text('price2',old('price2'),['class' => 'form-control','placeholder'=>'Укажите цену','required'=>'required','id'=>'eprice2'])!!}
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
    <!-- New Position Modal -->
    <div class="modal fade" id="newDoc" tabindex="-1" role="dialog" aria-labelledby="newDoc"
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
                        <fieldset>
                            <legend>Заказы поставщику:</legend>
                            <div class="form-group">
                                {!! Form::label('by_order', '№ документа:',['class'=>'col-xs-3 control-label']) !!}
                                <div class="col-xs-8">
                                    {!! Form::select('by_order',[], '', ['class' => 'form-control','id'=>'by_order'])!!}
                                </div>
                            </div>
                        </fieldset>
                    </div>

                    <div class="form-group">
                        {!! Form::label('order_pos[]', 'Позиции в документе:',['class'=>'col-xs-3 control-label']) !!}
                        <div class="col-xs-8">
                            {!! Form::select('order_pos[]', [], '', ['class' => 'form-control','id'=>'order_pos','required'=>'required','multiple'=>'multiple','size'=>10])!!}
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-8">
                            {!! Form::hidden('purchase_id',$purchase->id,['class' => 'form-control','id'=>'id_doc','required'=>'required']) !!}
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
    <!-- New Position Modal -->
    <!-- Import Positions Modal -->
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
                <div class="modal-body">
                    {!! Form::open(['url' => '#','id'=>'import_doc','class'=>'form-horizontal','method'=>'POST','files'=>'true']) !!}
                    {!! Form::hidden('doc_id', $purchase->id,['class' => 'form-control','id'=>'doc_id','required'=>'required']) !!}
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
    <!-- Import Positions Modal -->
    <div class="container-fluid container-fullw bg-white">
        <div class="row">
            <div class="col-md-12">
                <h2 class="text-center">{{ $head }}</h2>
                <h4 class="text-center">дата создания {{ $purchase->created_at }}</h4>
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
                            {!! Form::open(['url' => route('purchaseEdit',['id'=>$purchase->id]),'class'=>'form-horizontal','method'=>'POST', 'id'=>'form_ref']) !!}
                            <table class="table table-bordered table-hover">
                                <tr>
                                    <th>Номер документа:</th>
                                    <td>{!! Form::text('doc_num',$purchase->doc_num,['class' => 'form-control','placeholder'=>'Введите номер документа','maxlength'=>'15','disabled'=>'disabled'])!!}
                                    </td>
                                    <th>
                                        Хоз. операция: <span class="symbol required" aria-required="true"></span>
                                    </th>
                                    <td>
                                        {!! Form::select('hoperation_id',$hopsel, $purchase->hoperation_id, ['class' => 'form-control','required'=>'required','id'=>'hoperation_id']); !!}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        Организация: <span class="symbol required" aria-required="true"></span>
                                    </th>
                                    <td>
                                        {!! Form::select('organisation_id',$orgsel, $purchase->organisation_id, ['class' => 'form-control','required'=>'required','id'=>'org_id']); !!}
                                    </td>
                                    <th>
                                        Склад: <span class="symbol required" aria-required="true"></span>
                                    </th>
                                    <td>
                                        {!! Form::select('warehouse_id',$wxsel, $purchase->warehouse_id, ['class' => 'form-control','required'=>'required']); !!}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        Поставщик: <span class="symbol required" aria-required="true"></span>
                                    </th>
                                    <td>
                                        {!! Form::text('firm_id',$purchase->firm->name,['class' => 'form-control','placeholder'=>'Выберите поставщика','required'=>'required','id'=>'search_firm'])!!}
                                    </td>
                                    <th>
                                        Договор: <span class="symbol required" aria-required="true"></span>
                                    </th>
                                    <td>
                                        {!! Form::select('contract_id',$contsel, $purchase->contract_id, ['class' => 'form-control','required'=>'required','id'=>'contract_id']); !!}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        Статус: <span class="symbol required" aria-required="true"></span>
                                    </th>
                                    <td>
                                        {!! Form::select('statuse_id',$statsel, $purchase->statuse_id, ['class' => 'form-control','required'=>'required','id'=>'statuse_id']); !!}
                                    </td>
                                    <th>
                                        Менеджер: <span class="symbol required" aria-required="true"></span>
                                    </th>
                                    <td>
                                        {!! Form::select('user_id',$usel, $purchase->user_id, ['class' => 'form-control','required'=>'required','id'=>'user_id']); !!}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        Валюта: <span class="symbol required" aria-required="true"></span>
                                    </th>
                                    <td>
                                        {!! Form::select('currency_id',$cursel, $purchase->currency_id, ['class' => 'form-control','required'=>'required','id'=>'curr_id']); !!}
                                    </td>
                                    <th>Комментарий:</th>
                                    <td>
                                        {!! Form::textarea('comment',$purchase->comment,['class'=>'form-control', 'rows' => 2, 'cols' => 50]) !!}
                                    </td>
                                </tr>
                            </table>
                            <div class="form-group">
                                <div class="col-xs-8">
                                    {!! Form::button('Обновить', ['class' => 'btn btn-primary','type'=>'submit', 'id'=>'save_btn']) !!}
                                </div>
                            </div>
                            {!! Form::close() !!}
                            <h4 class="pull-right" id="rem"> К оплате с
                                НДС: {{ $purchase->amount + $purchase->vat_amount }} руб.</h4>
                        </div>
                        <div class="tab-pane fade" id="goods">
                            <div class="panel-heading">
                                <a href="#">
                                    <button type="button" class="btn btn-primary btn-sm btn-o" id="new_pos"
                                            data-toggle="modal" data-target="#newDoc">
                                        <i class="fa fa-plus" aria-hidden="true"></i> Добавить
                                    </button>
                                </a>
                                <a href="#">
                                    <button type="button" class="btn btn-primary btn-sm btn-o" id="import"
                                            data-toggle="modal"
                                            data-target="#importDoc">
                                        <i class="fa fa-file-excel-o" aria-hidden="true"></i> Загрузить из файла
                                    </button>
                                </a>
                                <h4 class="pull-right" id="state"> Всего позиций: {{ count($rows) }} на сумму с
                                    НДС: {{ $purchase->amount + $purchase->vat_amount }} руб.</h4>
                            </div>

                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table id="doc_table" class="table table-striped table-bordered">
                                        <thead>
                                        <tr>
                                            <th>Артикул</th>
                                            <th>Замена</th>
                                            <th>Номенклатура</th>
                                            <th>Характеристика</th>
                                            <th>Кол-во</th>
                                            <th>Ед.изм</th>
                                            <th>Цена в заказе</th>
                                            <th>Цена факт</th>
                                            <th>Сумма</th>
                                            <th>Заказ поставщику</th>
                                            <th>Действия</th>
                                        </tr>
                                        </thead>
                                        @if($rows)
                                            <tbody id="t_body">
                                            @foreach($rows as $k => $row)
                                                <tr id="{{ $row->id }}">
                                                    <td>{{ $row->good->vendor_code }}</td>
                                                    @if($row->good->vendor_code == $row->sub_good->vendor_code)
                                                        <td>Оригинал</td>
                                                        <td>{{ $row->good->title }}</td>
                                                    @else
                                                        <td>{{ $row->sub_good->vendor_code }}</td>
                                                        <td>{{ $row->sub_good->title }}</td>
                                                    @endif
                                                    <td>{{ $row->comment }}</td>
                                                    <td>{{ $row->qty }}</td>
                                                    <td>{{ $row->unit->title }}</td>
                                                    <td>{{ $row->price1 }}</td>
                                                    <td>{{ $row->price2 }}</td>
                                                    <td>{{ $row->amount }}</td>
                                                    <td><a href="/orders/view/{{ $row->order->id }}"
                                                           target="_blank">{{ $row->order->doc_num }}
                                                            от {{$row->order->created_at}}</a></td>
                                                    <td style="width:100px;">
                                                        <div class="form-group" role="group">
                                                            <button class="btn btn-info btn-sm pos_edit"
                                                                    type="button" title="Редактировать позицию"><i
                                                                    class="fa fa-edit fa-lg" aria-hidden="true"></i>
                                                            </button>
                                                            <button class="btn btn-danger btn-sm pos_delete"
                                                                    type="button" title="Удалить позицию"><i
                                                                    class="fa fa-trash fa-lg" aria-hidden="true"></i>
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

        $('#by_analog').typeahead({
            hint: true,
            highlight: true,
            minLength: 3,
            ajax: {
                url: "{{ route('getAnalog') }}",
                triggerLength: 1
            }
        });

        $("#search_firm").blur(function () {
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

        $('#btn_import').click(function (e) {
            e.preventDefault();
            let error = 0;
            $("#import_doc").find(":input").each(function () {// проверяем каждое поле ввода в форме
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
                formData.append('doc_id', $('#doc_id').val())
                $.ajax({
                    type: 'POST',
                    url: '{{ route('importPurchasePos') }}',
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
                            alert('У Вас нет прав для загрузки данных!');
                        if (res == 'ERR')
                            alert('При загрузке данных возникла ошибка!');
                        let obj = jQuery.parseJSON(res);
                        if (typeof obj === 'object') {
                            alert('Загружено строк ' + obj.num + ' из ' + obj.rows + '.\n' +
                                'Ошибки загрузки:\n' + obj.err + '.\n' + 'Позиций с множественным выбором: ' + obj.multi);
                            location.reload();
                        }
                        $(".modal").modal("hide");
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        alert(xhr.status + '\n' + thrownError);
                    }
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

        $('#curr_id').change(function () {
            let rub = $('#curr_id option:selected').text();
            if (rub.indexOf('рубль')) {
                $("#hoperation_id option:contains('Импорт')").prop("selected", true);
            }
            if (rub.indexOf('рубль') > 0) {
                $('#hoperation_id option').prop('selected', false);
                $('#hoperation_id option:contains("Закупка у поставщика")').prop("selected", true);
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
                    url: '{{ route('addPurchasePos') }}',
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
                            $('#state').text('Всего позиций: ' + obj.num + ' на сумму с НДС ' + obj.amount + ' руб.');
                            $('#rem').text('Заказано с НДС: ' + obj.amount + ' руб.');
                            $("#doc_table").append(obj.content);
                            $('#by_order').empty();
                            $('#order_pos').empty();
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

        $('#close').click(function (e) {
            e.preventDefault();
            $('#by_order').empty();
            $('#order_pos').empty();
        });

        $('#eclose').click(function (e) {
            e.preventDefault();
            $('#evendor').val('');
            $('#egood').val('');
            $('#eqty').val('');
            $('#eprice2').val('');
            $('#by_analog').val('');
            $('#sub_good').empty();
            $('#pos_id').val('');
        });

        $('#by_order').focus(function () {
            let firm = $('#search_firm').val();
            $('#by_order').empty();
            $.ajax({
                type: 'POST',
                url: '{{ route('searchByOrder') }}',
                data: {'firm': firm},
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    //alert(res);
                    $("#by_order").prepend($(res));
                }
            });
        });

        $('#sub_good').focus(function () {
            let analog = $('#by_analog').val();
            $('#sub_good').empty();
            $.ajax({
                type: 'POST',
                url: '{{ route('searchByAnalog') }}',
                data: {'analog_code': analog},
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    //alert(res);
                    $("#sub_good").prepend($(res));
                }
            });
        });

        $("#order_pos").focus(function () {
            $("#order_pos").empty(); //очищаем от старых значений
            let by_order = $("#by_order option:selected").text(); //$("#by_order").text();
            let id_doc = $("#id_doc").val();
            if (by_order.length > 3) {
                $.ajax({
                    type: 'POST',
                    url: '{{ route('getOrderPos') }}',
                    data: {'by_order': by_order, 'id_doc': id_doc},
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        //alert(res);
                        if (res == 'LOCK') {
                            alert('Заказ поставщику №' + by_order + ' имеет статус "Закрыт". Добавление позиций из него не возможно!')
                        } else if (res == 'NO') {
                            alert('В заказе поставщику №' + by_order + ' нет ни одной позиции для загрузки!')
                        } else {
                            $("#order_pos").prepend($(res));
                        }
                    }
                });
            }
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
                    url: '{{ route('PurchasePosEdit') }}',
                    data: $('#edit_pos').serialize(),
                    success: function (res) {
                        //alert(res);
                        let obj = jQuery.parseJSON(res);

                        if (typeof obj === 'object') {
                            $('#state').text('Всего позиций: ' + obj.num + ' на сумму с НДС ' + obj.amount + ' руб.');
                            $('#rem').text('Заказано с НДС: ' + obj.amount + ' руб.');
                            $('#' + id).children('td').eq(1).text(obj.code);
                            $('#' + id).children('td').eq(2).text(obj.title);
                            $('#' + id).children('td').eq(4).text($('#eqty').val());
                            $('#' + id).children('td').eq(5).text(obj.unit);
                            $('#' + id).children('td').eq(7).text($('#eprice2').val());
                            $('#' + id).children('td').eq(8).text(obj.sum);
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
                if(row.children('td').eq(1).text()=="Оригинал")
                    $('#evendor').val(row.children('td').eq(0).text());
                else
                    $('#evendor').val(row.children('td').eq(1).text());
                $('#egood').val(row.children('td').eq(2).text());
                $('#eqty').val(row.children('td').eq(4).text());
                $('#eunit option:selected').each(function () {
                    this.selected = false;
                });
                $("#eunit :contains('" + row.children('td').eq(5).text() + "')").attr("selected", "selected");
                $('#eprice2').val(row.children('td').eq(7).text());
                //$('#evat').val(row.children('td').eq(8).text());
                $('#pos_id').val(row.attr('id'));
                $('#by_analog').val('');
                $('#sub_good').empty();
            }
        }, ".pos_edit");

        $(document).on({
            click: function () {
                let id = $(this).parent().parent().parent().attr("id");
                let x = confirm("Выбранная запись будет удалена. Продолжить (Да/Нет)?");
                if (x) {
                    $.ajax({
                        type: 'POST',
                        url: '{{ route('delPurchasePos') }}',
                        data: {'id': id},
                        headers: {
                            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (res) {
                            //alert(res);
                            let obj = jQuery.parseJSON(res);
                            if (typeof obj === 'object') {
                                $('#state').text('Всего позиций: ' + obj.num + ' на сумму с НДС ' + obj.amount + ' руб.');
                                $('#rem').text('Заказано с НДС: ' + obj.amount + ' руб.');
                                $('#' + id).hide();
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
        }, ".pos_delete");

        function hide_row(id) {
            let err = Number($('.badge-danger').text());
            err--;
            $('.badge-danger').text(err.toString());
            $('#' + id).hide();
        }

    </script>
@endsection
