@extends('layouts.main')
@section('user_css')
    <link href="/css/select2.min.css" rel="stylesheet">
@endsection
@section('dashboard')

@endsection
@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li><a href="{{ route('sales') }}">Помощник продаж</a></li>
        <li><a href="{{ route('sale_orders') }}">{{ $title }}</a></li>
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
                    <fieldset>
                        <legend>Поиск номенклатуры:</legend>
                        <div class="form-group">
                            {!! Form::label('vendor_code', 'По артикулу:',['class'=>'col-xs-4 control-label']) !!}
                            <div class="col-xs-8">
                                {!! Form::text('vendor_code', '', ['class' => 'form-control','placeholder'=>'Начинайте вводить артикул','id'=>'search_vendor'])!!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('by_name', 'По наименованию:',['class'=>'col-xs-4 control-label']) !!}
                            <div class="col-xs-8">
                                {!! Form::text('by_name', '', ['class' => 'form-control','placeholder'=>'Начинайте вводить наименование','id'=>'by_name'])!!}
                            </div>
                        </div>
                    </fieldset>


                    {!! Form::open(['url' => '#','id'=>'add_pos','class'=>'form-horizontal','method'=>'POST']) !!}

                    <div class="form-group">
                        {!! Form::label('good_id', 'Наименование:',['class'=>'col-xs-3 control-label']) !!}
                        <div class="col-xs-8">
                            {!! Form::select('good_id', [], '', ['class' => 'form-control','id'=>'good_id','required'=>'required'])!!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('comment', 'Характеристика:',['class'=>'col-xs-3 control-label']) !!}
                        <div class="col-xs-8">
                            {!! Form::select('comment', [], '', ['class' => 'form-control','id'=>'comment'])!!}
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
                        {!! Form::label('price','Цена:',['class' => 'col-xs-3 control-label'])   !!}
                        <div class="col-xs-8">
                            {!! Form::text('price',old('price'),['class' => 'form-control','placeholder'=>'Укажите цену','required'=>'required'])!!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('vat','Ставка НДС:',['class' => 'col-xs-3 control-label'])   !!}
                        <div class="col-xs-8">
                            {!! Form::text('vat',$vat,['class' => 'form-control','placeholder'=>'Укажите ставку НДС','required'=>'required','id'=>'vat'])!!}
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-8">
                            {!! Form::hidden('sale_id',$sale->id,['class' => 'form-control','id'=>'id_doc','required'=>'required']) !!}
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
    <!-- Edit Position Modal -->
    <div class="modal fade" id="editPos" tabindex="-1" role="dialog" aria-labelledby="editPos"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times-circle fa-lg" aria-hidden="true"></i>
                    </button>
                    <h4 class="modal-title" id="htitle">Редактирование позиции</h4>
                </div>
                <div class="modal-body">
                    {!! Form::open(['url' => '#','id'=>'edit_pos','class'=>'form-horizontal','method'=>'POST']) !!}

                    <div class="form-group">
                        {!! Form::label('comment', 'Характеристика:',['class'=>'col-xs-3 control-label']) !!}
                        <div class="col-xs-8">
                            {!! Form::select('comment', [], '', ['class' => 'form-control','id'=>'ecomment'])!!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('qty','Количество:',['class' => 'col-xs-3 control-label'])   !!}
                        <div class="col-xs-8">
                            {!! Form::number('qty','1',['class' => 'form-control','placeholder'=>'Введите количество','required'=>'required','min' => 1, 'max' => 1000,'id'=>'eqty'])!!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('unit_id', 'Ед. измерения:',['class'=>'col-xs-3 control-label']) !!}
                        <div class="col-xs-8">
                            {!! Form::select('unit_id', $unsel, old('unit_id'),['class' => 'form-control','required' => 'required','id'=>'eunit_id']); !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('price','Цена:',['class' => 'col-xs-3 control-label'])   !!}
                        <div class="col-xs-8">
                            {!! Form::text('price','',['class' => 'form-control','placeholder'=>'Укажите цену','required'=>'required','id'=>'eprice'])!!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('vat','Ставка НДС:',['class' => 'col-xs-3 control-label'])   !!}
                        <div class="col-xs-8">
                            {!! Form::text('vat','',['class' => 'form-control','placeholder'=>'Укажите ставку НДС','required'=>'required','id'=>'evat'])!!}
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-8">
                            {!! Form::hidden('pos_id','',['class' => 'form-control','id'=>'epos_id','required'=>'required']) !!}
                        </div>
                    </div>

                    {!! Form::close() !!}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Отмена
                    </button>
                    <button type="button" class="btn btn-primary" id="edit_btn">Сохранить
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Edit Position Modal -->
    <!-- Specifications Modal -->
    <div class="modal fade" id="Spfc" tabindex="-1" role="dialog" aria-labelledby="Spfc"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times-circle fa-lg" aria-hidden="true"></i>
                    </button>
                    <h4 class="modal-title">Характеристики номенклатуры</h4>
                </div>
                <div class="modal-body">
                    {!! Form::open(['url' => '#','id'=>'fspfc','class'=>'form-horizontal','method'=>'POST','files'=>'true']) !!}
                    {!! Form::hidden('pos_id','',['class' => 'form-control','required'=>'required','id'=>'pos_id'])!!}
                    <div class="form-group">
                        <label class="col-xs-3 control-label">
                            Наименование: <span class="symbol required" aria-required="true"></span>
                        </label>
                        <div class="col-xs-8">
                            {!! Form::select('spec',[], '', ['class' => 'form-control','required'=>'required','id'=>'spec']); !!}
                        </div>
                    </div>

                    {!! Form::close() !!}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                    <button type="button" class="btn btn-primary" id="btn_spec">Выбрать</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Specifications Modal -->
    <div class="container-fluid container-fullw bg-white">
        <div class="row">
            <div class="col-md-12">
                <h2 class="text-center">{{ $head }}</h2>
                <h4 class="text-center">дата создания {{ $sale->created_at }}</h4>
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
                            {!! Form::open(['url' => route('saleEdit',['id'=>$sale->id]),'class'=>'form-horizontal','method'=>'POST', 'id'=>'form_ref']) !!}
                            <table class="table table-bordered table-hover">
                                <tr>
                                    <th>Номер документа:</th>
                                    <td>{!! Form::text('doc_num',$sale->doc_num,['class' => 'form-control','placeholder'=>'Введите номер документа','maxlength'=>'15','required'=>'required'])!!}
                                        {!! $errors->first('doc_num', '<p class="text-danger">:message</p>') !!}
                                    </td>
                                    <th>
                                        Склад: <span class="symbol required" aria-required="true"></span>
                                    </th>
                                    <td>
                                        {!! Form::select('warehouse_id',$wxsel, $sale->warehouse_id, ['class' => 'form-control','required'=>'required','id'=>'whs_id']); !!}
                                        {!! $errors->first('warehouse_id', '<p class="text-danger">:message</p>') !!}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        Организация: <span class="symbol required" aria-required="true"></span>
                                    </th>
                                    <td>
                                        {!! Form::select('organisation_id',$orgsel, $sale->organisation_id, ['class' => 'form-control','required'=>'required','id'=>'org_id']); !!}
                                        {!! $errors->first('organisation_id', '<p class="text-danger">:message</p>') !!}
                                    </td>
                                    <th>
                                        Соглашение: <span class="symbol required" aria-required="true"></span>
                                    </th>
                                    <td>
                                        {!! Form::select('agreement_id',$agrsel, $sale->agreement_id, ['class' => 'form-control','required'=>'required','id'=>'agreement_id']); !!}
                                        {!! $errors->first('agreement_id', '<p class="text-danger">:message</p>') !!}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        Клиент: <span class="symbol required" aria-required="true"></span>
                                    </th>
                                    <td>
                                        {!! Form::select('firm_id',$firmsel, $sale->firm_id, ['class' => 'form-control select2','required'=>'required','id'=>'firm_id']); !!}
                                    </td>
                                    <th>
                                        Договор: <span class="symbol required" aria-required="true"></span>
                                    </th>
                                    <td>
                                        {!! Form::select('contract_id',$contsel, $sale->contract_id, ['class' => 'form-control','required'=>'required','id'=>'contract']); !!}
                                        {!! $errors->first('contract_id', '<p class="text-danger">:message</p>') !!}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        Способ доставки: <span class="symbol required" aria-required="true"></span>
                                    </th>
                                    <td>
                                        {!! Form::select('delivery_method_id',$dmethods, $sale->delivery_method_id, ['class' => 'form-control','required'=>'required','id'=>'method_id']); !!}
                                        {!! $errors->first('delivery_method_id', '<p class="text-danger">:message</p>') !!}
                                    </td>
                                    <th>Кто доставляет: <span class="symbol required" aria-required="true"></span></th>
                                    <td>
                                        {!! Form::select('delivery_id',$delivs, $sale->delivery_id, ['class' => 'form-control','required'=>'required','id'=>'delivery_id']); !!}
                                        {!! $errors->first('delivery_id', '<p class="text-danger">:message</p>') !!}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        Пункт назначения: <span class="symbol required" aria-required="true"></span>
                                    </th>
                                    <td>
                                        {!! Form::text('destination',$sale->destination,['class' => 'form-control','placeholder'=>'Введите адрес пункта назначения','required'=>'required','maxlength'=>'150'])!!}
                                        {!! $errors->first('destination', '<p class="text-danger">:message</p>') !!}
                                    </td>
                                    <th>Контактное лицо:</th>
                                    <td>
                                        {!! Form::text('contact',$sale->contact,['class' => 'form-control','placeholder'=>'Укажите контактное лицо','maxlength'=>'100'])!!}
                                        {!! $errors->first('contact', '<p class="text-danger">:message</p>') !!}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        Менеджер: <span class="symbol required" aria-required="true"></span>
                                    </th>
                                    <td>
                                        {!! Form::select('user_id',$usel, $sale->user_id, ['class' => 'form-control','required'=>'required','id'=>'user_id']); !!}
                                    </td>

                                    <th>Дата согласования:</th>
                                    <td>
                                        @if($sale->date_agreement)
                                            {{ Form::date('date_agreement', \Carbon\Carbon::createFromFormat('Y-m-d', date("$sale->date_agreement")),['class' => 'form-control']) }}</td>
                                    @else
                                    {{ Form::date('date_agreement', \Carbon\Carbon::create(),['class' => 'form-control']) }}</td>
                                    @endif
                                </tr>
                                <tr>
                                    <th>
                                        № заказа по данным клиента:
                                    </th>
                                    <td>
                                        {!! Form::text('doc_num_firm',$sale->doc_num_firm,['class' => 'form-control','maxlength'=>'15'])!!}
                                        {!! $errors->first('doc_num_firm', '<p class="text-danger">:message</p>') !!}
                                    </td>
                                    <th>
                                        от:
                                    </th>
                                    <td>
                                        @if($sale->date_firm)
                                            {{ Form::date('date_firm', \Carbon\Carbon::createFromFormat('Y-m-d', date("$sale->date_firm")),['class' => 'form-control']) }}</td>
                                    @else
                                    {{ Form::date('date_firm', \Carbon\Carbon::create(),['class' => 'form-control']) }}</td>
                                    @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Тип цены: <span class="symbol required" aria-required="true"></span></th>
                                    <td>
                                        {!! Form::select('price_type',['retail'=>'Розница','wholesale'=>'Оптовая','small'=>'Мелкооптовая'],
                                            $sale->price_type, ['class' => 'form-control','required'=>'required','id'=>'price_type']); !!}
                                        <em class="text-center text-danger">После смены типа цены не забудьте
                                            пересчитать цены в разделе Товары!!!</em>
                                    </td>
                                    <th>
                                        Валюта: <span class="symbol required" aria-required="true"></span>
                                    </th>
                                    <td>{!! Form::select('currency_id',$cursel, $sale->currency_id, ['class' => 'form-control','required'=>'required','id'=>'curr_id']); !!}
                                        <div class="checkbox clip-check check-primary">
                                            @if($sale->has_vat)
                                                <input type="checkbox" name="has_vat" id="has_vat" value="1" checked>
                                            @else
                                                <input type="checkbox" name="has_vat" id="has_vat" value="1">
                                            @endif
                                            <label for="has_vat">
                                                Цена включает НДС
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Статус</th>
                                    <td>
                                        {!! Form::select('state',['0'=>'Создан','1'=>'Комплектуется','2'=>'Собран','3'=>'Собран частично','4'=>'Оформление документов',
                                            '5'=>'Отгружен','6'=>'Отгружен частично'], $sale->state, ['class' => 'form-control','required'=>'required','id'=>'state']); !!}
                                    </td>
                                    <th>Дополнительные условия:</th>
                                    <td>
                                        <div class="checkbox clip-check check-primary col-xs-4">
                                            @if($sale->to_door)
                                                <input type="checkbox" name="to_door" id="to_door" value="1" checked>
                                            @else
                                                <input type="checkbox" name="to_door" id="to_door" value="1">
                                            @endif
                                            <label for="to_door">
                                                Доставка до двери
                                            </label>
                                        </div>
                                        <div class="checkbox clip-check check-primary col-xs-2">
                                            @if($sale->delivery_in_price)
                                                <input type="checkbox" name="delivery_in_price" id="delivery_in_price"
                                                       value="1" checked>
                                            @else
                                                <input type="checkbox" name="delivery_in_price" id="delivery_in_price"
                                                       value="1">
                                            @endif
                                            <label for="delivery_in_price">
                                                Доставка включена в стоимость
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Комментарий:</th>
                                    <td colspan="3">
                                        {!! Form::textarea('comment',$sale->comment,['class'=>'form-control', 'rows' => 2, 'cols' => 50]) !!}
                                    </td>
                                </tr>
                            </table>
                            <div class="form-group">
                                <div class="col-xs-8">
                                    {!! Form::button('Обновить', ['class' => 'btn btn-primary','type'=>'submit', 'id'=>'save_btn']) !!}
                                </div>
                            </div>
                            {!! Form::close() !!}
                            <h4 class="pull-right" id="rem"> Заказано с НДС: {{ $sale->amount + $sale->vat_amount }}
                                руб.</h4>
                        </div>
                        <div class="tab-pane fade" id="goods">
                            <div class="panel-heading">
                                @if($sale->state == 0)
                                    <a href="#">
                                        <button type="button" class="btn btn-primary btn-sm btn-o" id="new_pos"
                                                data-toggle="modal" data-target="#editDoc">
                                            <i class="fa fa-plus" aria-hidden="true"></i> Добавить
                                        </button>
                                    </a>
                                    <a href="#">
                                        <button type="button" class="btn btn-primary btn-sm btn-o" id="new_import"
                                                data-toggle="modal" data-target="#importDoc">
                                            <i class="fa fa-file-excel-o" aria-hidden="true"></i> Загрузить из файла
                                        </button>
                                    </a>
                                    <a href="#">
                                        <button type="button" class="btn btn-success btn-sm btn-o" id="set_reserv">
                                            <i class="fa fa-check" aria-hidden="true"></i> Зарезервировать
                                        </button>
                                    </a>
                                    <a href="#">
                                        <button type="button" class="btn btn-primary btn-sm btn-o" id="assembly">
                                            <i class="fa fa-archive" aria-hidden="true"></i> Наряд на сборку
                                        </button>
                                    </a>
                                    <a href="#">
                                        <button type="button" class="btn btn-warning btn-sm btn-o" id="drop_reserv">
                                            <i class="fa fa-square-o" aria-hidden="true"></i> Снять резерв
                                        </button>
                                    </a>
                                @endif
                                <div class="btn-group">
                                    <a class="btn btn-primary btn-o btn-sm dropdown-toggle" data-toggle="dropdown"
                                       href="#" aria-expanded="false">
                                        Создать на основании <span class="caret"></span>
                                    </a>
                                    <ul role="menu" class="dropdown-menu dropdown-light">
                                        @if($sale->state == 0)
                                            <li>
                                                <a href="#" id="new_appl">
                                                    Запрос по ценам
                                                </a>
                                            </li>
                                        @endif
                                        <li>
                                            <a href="{{ route('saleInvoice',['id'=>$sale->id]) }}">
                                                Счет на оплату
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <a href="#">
                                    <button type="button" class="btn btn-success btn-sm btn-o" id="update_price">
                                        <i class="fa fa-rouble" aria-hidden="true"></i> Пересчитать цены
                                    </button>
                                </a>
                                @if($sale->state == 0)
                                    <a href="#">
                                        <button type="button" class="btn btn-danger btn-sm btn-o" id="del_sale">
                                            <i class="fa fa-trash" aria-hidden="true"></i> Отменить заявку
                                        </button>
                                    </a>
                                @endif

                                <h4 class="pull-right" id="hstate"> Всего позиций: {{ count($rows) }} на сумму с
                                    НДС: {{ $sale->amount + $sale->vat_amount }} руб.</h4>
                            </div>
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table id="doc_table" class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <th>№ п\п</th>
                                            <th>Артикул</th>
                                            <th>Замена</th>
                                            <th>Номенклатура</th>
                                            <th>Характеристика</th>
                                            <th>Кол-во</th>
                                            <th>Резерв</th>
                                            <th>Ед.изм</th>
                                            <th>Цена</th>
                                            <th>Сумма</th>
                                            <th>Ставка НДС</th>
                                            <th>НДС</th>
                                            <th>Действия</th>
                                        </tr>
                                        </thead>
                                        @if($rows)
                                            <tbody id="t_body">
                                            @foreach($rows as $k => $row)
                                                <tr id="{{ $row->id }}">
                                                    <td>{{ $row->pos_num }}</td>
                                                    <td>{{ $row->good->vendor_code }}</td>
                                                    @if($row->good->vendor_code == $row->sub_good->vendor_code)
                                                        <td>Оригинал</td>
                                                    @else
                                                        <td>{{ $row->sub_good->vendor_code }}</td>
                                                    @endif
                                                    <td>{{ $row->good->title }}</td>
                                                    <td>{{ $row->comment }}</td>
                                                    <td>{{ $row->qty }}</td>
                                                    <td>{{ $row->reserved }}</td>
                                                    <td>{{ $row->unit->title }}</td>
                                                    <td>{{ round($row->price * $row->ratio,2) }}</td>
                                                    <td>{{ $row->amount }}</td>
                                                    <td>{{ $row->vat }}</td>
                                                    <td>{{ $row->vat_amount }}</td>
                                                    <td style="width:140px;">
                                                        @if($sale->state == 0)
                                                            <div class="form-group" role="group">
                                                                @if($row->good->has_specification)
                                                                    <button class="btn btn-info btn-sm pos_spec"
                                                                            type="button" title="Характеристики"><i
                                                                            class="fa fa-cog fa-lg"
                                                                            aria-hidden="true"></i>
                                                                    </button>
                                                                @endif
                                                                <button class="btn btn-info btn-sm pos_edit"
                                                                        type="button" title="Редактировать позицию"><i
                                                                        class="fa fa-edit fa-lg"
                                                                        aria-hidden="true"></i>
                                                                </button>
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
                                            <th>Ответственный</th>
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
    <script src="/js/select2.min.js"></script>
    {{--    <script src="/js/jquery.tabledit.min.js"></script>--}}
    <script>
        /*$('#doc_table').Tabledit({
            url: 'example.php',
            columns: {
                identifier: [0, 'id'],
                editable: [[1, 'vendor_code'], [2, 'catalog_num'], [3, 'good_id'],[4, 'spec']]
            }
        });*/
        $('#file-loader').hide();

        $('.select2').css('width', '100%').select2({
            placeholder: "Выбор контрагента",
            allowClear: true
        })

        $('#search_firm').typeahead({
            hint: true,
            highlight: true,
            minLength: 3,
            ajax: {
                url: "{{ route('getFirm') }}",
                triggerLength: 1
            }
        });

        $('#search_vendor').typeahead({
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

        $('#by_name').typeahead({
            hint: true,
            highlight: true,
            minLength: 3,
            ajax: {
                url: "{{ route('searchGood') }}",
                triggerLength: 1
            }
        });

        $('#search_vendor').focus(function () {
            $('#by_name').val('');
        });


        $('#by_name').focus(function () {
            $('#search_vendor').val('');
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
                $.ajax({
                    type: 'POST',
                    url: '{{ route('importSalePos') }}',
                    processData: false,
                    contentType: false,
                    cache: false,
                    dataType: 'text',
                    data: formData,
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function(){
                        $('#file-loader').show();
                    },
                    success: function (res) {
                        //alert(res);
                        if (res == 'NO')
                            alert('Заявка клиента с таким номером, как в файле не обнаружена!');
                        if (res == 'ERR')
                            alert('При обновлении данных возникла ошибка!');
                        let obj = jQuery.parseJSON(res);
                        if (typeof obj === 'object') {
                            $(".modal").modal("hide");
                            alert('Загружено строк ' + obj.num + ' из ' + obj.rows + '.\n' +
                                'Позиций, не найденных в базе: ' + obj.err + '.\n');
                            location.reload();
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        alert(xhr.status + '\n' + thrownError);
                        $('#file-loader').hide();
                    }
                });
            }
        });

        $("#firm_id").change(function () {
            $("#contract").empty(); //очищаем от старых значений
            var firm = $("#firm_id option:selected").text();
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

        $('#assembly').click(function () {
            let x = confirm("Будут созданы задания на сборку заказа. Продолжить (Да/Нет)?");
            if (x) {
                let saleid = $("#id_doc").val();
                $.ajax({
                    type: 'POST',
                    url: '{{ route('shipmentAdd') }}',
                    data: {'sale_id': saleid},
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        //alert(res);
                        if (res == 'BAD') {
                            alert('У Вас нет прав для редактирования документа!')
                        }
                        if (res == 'NO') {
                            alert('Не известный запрос!')
                        }
                        if (res == 'OK') {
                            alert('Задания на сборку заказа созданы!')
                            window.location.reload();
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
        });

        $("#set_reserv").click(function () {
            let x = confirm("Будет выполнено резервирование товаров на складе. Продолжить (Да/Нет)?");
            if (x) {
                let saleid = $("#id_doc").val();
                $.ajax({
                    type: 'POST',
                    url: '{{ route('dropReserv') }}',
                    data: {'sale_id': saleid},
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        $.ajax({
                            type: 'POST',
                            url: '{{ route('setReserv') }}',
                            data: {'sale_id': saleid},
                            headers: {
                                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (res) {
                                //alert(res);
                                let obj = jQuery.parseJSON(res);
                                if (res == 'BAD') {
                                    alert('У Вас нет прав для редактирования документа!')
                                }
                                if (res == 'NO') {
                                    alert('Не известный запрос!')
                                }
                                if (typeof obj === 'object') {
                                    $('#hstate').text('Всего позиций: ' + obj.num + ' на сумму с НДС ' + obj.amount + ' руб.');
                                    $("#t_body").html(obj.content);
                                    alert('Резервирование товара выполнено!');
                                }
                            },
                            error: function (xhr, ajaxOptions, thrownError) {
                                alert(xhr.status);
                                alert(thrownError);
                            }
                        });
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        alert(xhr.status);
                        alert(thrownError);
                    }
                });
            } else {
                return false;
            }
        });

        $("#drop_reserv").click(function () {
            let x = confirm("Будет выполнено снятие резервирования товаров на складе. Продолжить (Да/Нет)?");
            if (x) {
                let saleid = $("#id_doc").val();
                $.ajax({
                    type: 'POST',
                    url: '{{ route('dropReserv') }}',
                    data: {'sale_id': saleid},
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        //alert(res);
                        let obj = jQuery.parseJSON(res);
                        if (res == 'BAD') {
                            alert('У Вас нет прав для редактирования документа!')
                        }
                        if (res == 'NO') {
                            alert('Не известный запрос!')
                        }
                        if (typeof obj === 'object') {
                            $('#hstate').text('Всего позиций: ' + obj.num + ' на сумму с НДС ' + obj.amount + ' руб.');
                            $("#t_body").html(obj.content);
                            alert('Резервирование товара отменено!');
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
        });

        $('#update_price').click(function () {
            let saleid = $("#id_doc").val();
            $.ajax({
                type: 'POST',
                url: '{{ route('salePriceUpdate') }}',
                data: {'sale_id': saleid},
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    //alert(res);
                    let obj = jQuery.parseJSON(res);
                    if (res == 'BAD') {
                        alert('У Вас нет прав для редактирования документа!')
                    }
                    if (res == 'NO') {
                        alert('Не известный запрос!')
                    }
                    if (typeof obj === 'object') {
                        $('#hstate').text('Всего позиций: ' + obj.num + ' на сумму с НДС ' + obj.amount + ' руб.');
                        $("#t_body").html(obj.content);
                        alert('Пересчет цен выполнен!');
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(xhr.status);
                    alert(thrownError);
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

        $('#curr_id').change(function () {
            let rub = $('#curr_id option:selected').text();
            if (rub.indexOf('рубль')) {
                $("#hoperation_id option:contains('Импорт')").prop("selected", true);
                $('#has_vat').prop('checked', false);
                $('#has_vat').val('0');
            }
            if (rub.indexOf('рубль') > 0) {
                $('#has_vat').prop('checked', true);
                $('#has_vat').val('1');
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
                    url: '{{ route('addSalePos') }}',
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
                            $("#t_body").append(obj.content);
                            $('#price').val('');
                            $('#search_vendor').val('');
                            $('#by_name').val('');
                            $('#good_id').empty();
                            $('#comment').empty();
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
            let vendor_code = $("#search_vendor").val();
            let by_name = $("#by_name").val();
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
            if (by_name.length > 3) {
                $.ajax({
                    type: 'POST',
                    url: '{{ route('searchByName') }}',
                    data: {'by_name': by_name},
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

        $('#btn_spec').click(function () {
            let error = 0;
            $("#fspfc").find(":input").each(function () {// проверяем каждое поле ввода в форме
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
                let id = $('#pos_id').val();
                let title = $('#spec option:selected').text();
                $.ajax({
                    type: 'POST',
                    url: '{{ route('setSpecPos') }}',
                    data: {'id': id, 'title': title, 'tbl_id': 'sale'},
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        //alert(res);
                        if (res == 'OK') {
                            $('#' + id).children('td').eq(3).text(title);
                        }
                        $(".modal").modal("hide");
                    }
                });
            }
        });

        $('#new_appl').click(function (e) {
            e.preventDefault();
            let saleid = $("#id_doc").val();
            $.ajax({
                type: 'POST',
                url: '{{ route('genNewApp') }}',
                data: {'sale_id': saleid},
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    //alert(res);
                    if (res == 'BAD') {
                        alert('У Вас нет прав для редактирования документа!')
                    }
                    if (res == 'NO') {
                        alert('Не известный запрос!')
                    }
                    if (res == 'OK') {
                        alert('Запрос по ценам создан!')
                        window.location.reload();
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(xhr.status);
                    alert(thrownError);
                }
            });
        });

        $(document).on({
            click: function () {
                let id = $(this).parent().parent().parent().attr("id");
                $.ajax({
                    type: 'POST',
                    url: '{{ route('getSpecPos') }}',
                    data: {'id': id, 'tbl_id': 'sale'},
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        //alert(res);
                        $("#Spfc").modal("show");
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
                $("#editPos").modal("show");
                $('#htitle').text(row.children('td').eq(2).text().trim());
                $('#eqty').val(row.children('td').eq(5).text());
                $('#eprice').val(row.children('td').eq(8).text());
                $('#evat').val(row.children('td').eq(10).text());
                $('#epos_id').val(row.attr('id'));
                let eunit = row.children('td').eq(6).text();
                $('#eunit_id_id option:contains(eunit)').prop("selected", true);
                $.ajax({
                    type: 'POST',
                    url: '{{ route('getSpecPos') }}',
                    data: {'id': row.attr('id'), 'tbl_id': 'sale'},
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        //alert(res);
                        $('#ecomment').empty(); //очищаем от предыдущих значений
                        $('#ecomment').prepend(res);
                    }
                });

            }
        }, ".pos_edit");

        $(document).on({
            click: function () {
                let id = $(this).parent().parent().parent().attr("id");
                let x = confirm("Выбранная запись будет удалена. Продолжить (Да/Нет)?");
                if (x) {
                    $.ajax({
                        type: 'POST',
                        url: '{{ route('delSalePos') }}',
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
                            //if (res == 'LINK')
                            //alert('Данную позицию удалить нельзя, т.к. она присутствует в связанных документах!');
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

        $('#edit_btn').click(function (e) {
            e.preventDefault();
            let error = 0;
            let id = $('#epos_id').val();
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
                    url: '{{ route('editSalePos') }}',
                    data: $('#edit_pos').serialize(),
                    success: function (res) {
                        //alert(res);
                        if (res == 'BAD') {
                            alert('У Вас нет прав для редактирования документа!');
                        }
                        if (res == 'NO') {
                            alert('Не известный запрос!');
                        }
                        if (res == 'ERR') {
                            alert('Возникла ошибка при обновлении данных!');
                        }
                        let obj = jQuery.parseJSON(res);
                        if (typeof obj === 'object') {
                            $('#state').text('Всего позиций: ' + obj.num + ' на сумму с НДС ' + obj.amount + ' руб.');
                            $('#rem').text('Заказано с НДС: ' + obj.amount + ' руб.');
                            if ($('#ecomment').val())
                                $('#' + id).children('td').eq(4).text($("#ecomment option:selected").text());
                            $('#' + id).children('td').eq(5).text($('#eqty').val());
                            $('#' + id).children('td').eq(7).text($("#eunit_id option:selected").text());
                            $('#' + id).children('td').eq(8).text($('#eprice').val());
                            let amount = parseFloat($('#eqty').val()) * parseFloat($('#eprice').val());
                            $('#' + id).children('td').eq(9).text(amount);
                            $('#' + id).children('td').eq(10).text($('#evat').val());
                            $('#' + id).children('td').eq(11).text(obj.vat_amount);

                            $('#eprice').val('');
                            $('#epos_id').val('');
                            $('#evat').empty();
                            $('#ecomment').empty();
                            $('#eqty').val('1');
                            $('#eprice').val('');
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

        function hide_row(id) {
            $('#' + id).hide();
        }

    </script>
@endsection
