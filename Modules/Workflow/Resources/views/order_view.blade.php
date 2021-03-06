@extends('layouts.main')

@section('dashboard')

@endsection
@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li><a href="{{ route('orders') }}">{{ $title }}</a></li>
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
    <div class="modal fade" id="errPoc" tabindex="-1" role="dialog" aria-labelledby="errPos"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times-circle fa-lg" aria-hidden="true"></i>
                    </button>
                    <h4 class="modal-title">Обработка позиции с ошибкой</h4>
                </div>
                <div class="modal-body">
                    {!! Form::open(['url' => '#','id'=>'err_pos','class'=>'form-horizontal','method'=>'POST']) !!}

                    <div class="form-group">
                        {!! Form::label('vendor_code', 'Артикул:',['class'=>'col-xs-3 control-label']) !!}
                        <div class="col-xs-8">
                            {!! Form::text('vendor_code', old('vendor_code'), ['class' => 'form-control','placeholder'=>'Начинайте вводить артикул','required'=>'required','id'=>'search_evendor'])!!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('good_id', 'Наименование:',['class'=>'col-xs-3 control-label']) !!}
                        <div class="col-xs-8">
                            {!! Form::select('good_id', [], '', ['class' => 'form-control','id'=>'egood_id','required'=>'required'])!!}
                        </div>
                    </div>

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
                            {!! Form::select('unit_id', $unsel, old('unit_id'),['class' => 'form-control','required' => 'required']); !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('price','Цена:',['class' => 'col-xs-3 control-label'])   !!}
                        <div class="col-xs-8">
                            {!! Form::text('price',old('price'),['class' => 'form-control','placeholder'=>'Укажите цену','required'=>'required','id'=>'eprice'])!!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('vat','Ставка НДС:',['class' => 'col-xs-3 control-label'])   !!}
                        <div class="col-xs-8">
                            {!! Form::text('vat',$vat,['class' => 'form-control','placeholder'=>'Укажите ставку НДС','required'=>'required','id'=>'evat'])!!}
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-8">
                            {!! Form::hidden('order_id',$order->id,['class' => 'form-control','id'=>'id_doc','required'=>'required']) !!}
                            {!! Form::hidden('err_id','',['class' => 'form-control','id'=>'err_id','required'=>'required']) !!}
                        </div>
                    </div>

                    {!! Form::close() !!}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Отмена
                    </button>
                    <button type="button" class="btn btn-primary" id="err_btn">Сохранить
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Error Position Modal -->
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
                                        {!! Form::text('vendor_code', '', ['class' => 'form-control','placeholder'=>'Начинайте вводить артикул','id'=>'search_vendor'])!!}
                                    </div>
                                </div>

                                <div class="form-group">
                                    {!! Form::label('by_name', 'По наименованию:',['class'=>'col-xs-4 control-label']) !!}
                                    <div class="col-xs-7">
                                        {!! Form::text('by_name', '', ['class' => 'form-control','placeholder'=>'Начинайте вводить наименование','id'=>'by_name'])!!}
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
                            {!! Form::hidden('order_id',$order->id,['class' => 'form-control','id'=>'id_doc','required'=>'required']) !!}
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
    <!-- Specifications Modal -->
    <div class="modal fade" id="importSpfc" tabindex="-1" role="dialog" aria-labelledby="importSpfc"
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
                    {!! Form::open(['url' => '#','id'=>'import_spfc','class'=>'form-horizontal','method'=>'POST','files'=>'true']) !!}
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
                <h4 class="text-center">дата создания {{ $order->created_at }}</h4>
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
                            {!! Form::open(['url' => route('orderEdit',['id'=>$order->id]),'class'=>'form-horizontal','method'=>'POST', 'id'=>'form_ref']) !!}
                            <table class="table table-bordered table-hover">
                                <tr>
                                    <th>Номер документа:</th>
                                    <td>{!! Form::text('doc_num',$order->doc_num,['class' => 'form-control','placeholder'=>'Введите номер документа','maxlength'=>'15','disabled'=>'disabled'])!!}
                                    </td>
                                    <th>
                                        Хоз. операция: <span class="symbol required" aria-required="true"></span>
                                    </th>
                                    <td>
                                        {!! Form::select('hoperation_id',$hopsel, $order->hoperation_id, ['class' => 'form-control','required'=>'required','id'=>'hoperation_id']); !!}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        Организация: <span class="symbol required" aria-required="true"></span>
                                    </th>
                                    <td>
                                        {!! Form::select('organisation_id',$orgsel, $order->organisation_id, ['class' => 'form-control','required'=>'required','id'=>'org_id']); !!}
                                    </td>
                                    <th>
                                        Склад: <span class="symbol required" aria-required="true"></span>
                                    </th>
                                    <td>
                                        {!! Form::select('warehouse_id',$wxsel, $order->warehouse_id, ['class' => 'form-control','required'=>'required']); !!}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        Поставщик: <span class="symbol required" aria-required="true"></span>
                                    </th>
                                    <td>
                                        {!! Form::text('firm_id',$order->firm->name,['class' => 'form-control','placeholder'=>'Выберите поставщика','required'=>'required','id'=>'search_firm'])!!}
                                    </td>
                                    <th>
                                        Договор: <span class="symbol required" aria-required="true"></span>
                                    </th>
                                    <td>
                                        {!! Form::select('contract_id',$contsel, $order->contract_id, ['class' => 'form-control','required'=>'required','id'=>'contract_id']); !!}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        Статус: <span class="symbol required" aria-required="true"></span>
                                    </th>
                                    <td>
                                        {!! Form::select('statuse_id',$statsel, $order->statuse_id, ['class' => 'form-control','required'=>'required','id'=>'statuse_id']); !!}
                                    </td>
                                    <th>Номер заказа (опл. документа):</th>
                                    <td>
                                        {!! Form::text('doc_num_firm',$order->doc_num_firm,['class' => 'form-control','size'=>'15'])!!}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        Менеджер: <span class="symbol required" aria-required="true"></span>
                                    </th>
                                    <td>
                                        {!! Form::select('user_id',$usel, $order->user_id, ['class' => 'form-control','required'=>'required','id'=>'user_id']); !!}
                                    </td>

                                    <th>Дата заказа (опл. документа):</th>
                                    <td>
                                        @if($order->date_firm)
                                            {{ Form::date('date_firm', \Carbon\Carbon::createFromFormat('Y-m-d', date("$order->date_firm")),['class' => 'form-control']) }}</td>
                                    @else
                                    {{ Form::date('date_firm', \Carbon\Carbon::create(),['class' => 'form-control']) }}</td>
                                    @endif
                                </tr>
                                <tr>
                                    <th>
                                        Валюта: <span class="symbol required" aria-required="true"></span>
                                    </th>
                                    <td>{!! Form::select('currency_id',$cursel, $order->currency_id, ['class' => 'form-control','required'=>'required','id'=>'curr_id']); !!}
                                        <div class="checkbox clip-check check-primary">
                                            @if($order->has_vat)
                                                <input type="checkbox" name="has_vat" id="has_vat" value="1" checked>
                                            @else
                                                <input type="checkbox" name="has_vat" id="has_vat" value="0">
                                            @endif
                                            <label for="has_vat">
                                                Цена включает НДС
                                            </label>
                                        </div>
                                    </td>
                                    <th>Комментарий:</th>
                                    <td>
                                        {!! Form::textarea('comment',$order->comment,['class'=>'form-control', 'rows' => 2, 'cols' => 50]) !!}
                                    </td>
                                </tr>
                            </table>
                            <div class="form-group">
                                <div class="col-xs-8">
                                    {!! Form::button('Обновить', ['class' => 'btn btn-primary','type'=>'submit', 'id'=>'save_btn']) !!}
                                </div>
                            </div>
                            {!! Form::close() !!}
                            <h4 class="pull-right" id="rem"> Заказано с НДС: {{ $order->amount + $order->vat_amount }}
                                руб.</h4>
                        </div>
                        <div class="tab-pane fade" id="goods">
                            <div class="panel-heading">
                                @if($order->statuse->title != 'Закрыт')
                                    <a href="#">
                                        <button type="button" class="btn btn-primary btn-sm btn-o" id="new_pos"
                                                data-toggle="modal" data-target="#editDoc">
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
                                    <div class="btn-group">
                                        <a class="btn btn-primary btn-o btn-sm dropdown-toggle" data-toggle="dropdown"
                                           href="#" aria-expanded="false">
                                            Создать на основании <span class="caret"></span>
                                        </a>
                                        <ul role="menu" class="dropdown-menu dropdown-light">
                                            @if($order->hoperation->title == 'Импорт')
                                                <li>
                                                    <a href="{{ route('newPurchase',['id'=>$order->id]) }}">
                                                        Приобретение товаров и услуг
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                @endif
                                <h4 class="pull-right" id="state"> Всего позиций: {{ count($rows) }} на сумму с
                                    НДС: {{ $order->amount + $order->vat_amount }} руб.</h4>
                            </div>
                            @if($err)
                                <div class="tabbable pills">
                                    <ul id="myTab3" class="nav nav-pills">
                                        <li>
                                            <a href="#err" data-toggle="tab">
                                                Ошибки загрузки <span class="badge badge-danger">{{ $err }}</span>
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane fade" id="err">
                                            <div class="col-md-12">
                                                <div class="table-responsive">
                                                    <table id="err_table" class="table table-striped table-bordered">
                                                        <thead>
                                                        <tr>
                                                            <th>Артикул</th>
                                                            <th>Кол-во</th>
                                                            <th>Ед.изм</th>
                                                            <th>Цена</th>
                                                            <th>Сумма</th>
                                                            <th>Ставка НДС</th>
                                                            <th>Действия</th>
                                                        </tr>
                                                        </thead>
                                                        @if($err_rows)
                                                            <tbody id="t_body">
                                                            @foreach($err_rows as $k => $row)
                                                                <tr id="{{ $row->id }}">
                                                                    @if($row->multi)
                                                                        <td>
                                                                            <p class="text-info text-bold">
                                                                                <ins>{{ $row->vendor_code }}</ins>
                                                                            </p>
                                                                        </td>
                                                                    @else
                                                                        <td>{{ $row->vendor_code }}</td>
                                                                    @endif
                                                                    <td>{{ $row->qty }}</td>
                                                                    <td>{{ $row->unit }}</td>
                                                                    <td>{{ $row->price }}</td>
                                                                    <td>{{ $row->qty * $row->price }}</td>
                                                                    <td>{{ $row->vat }}</td>
                                                                    <td style="width:100px;">
                                                                        <div class="form-group" role="group">
                                                                            <button class="btn btn-info btn-sm pos_edit"
                                                                                    type="button"
                                                                                    title="Редактировать позицию"><i
                                                                                    class="fa fa-edit fa-lg"
                                                                                    aria-hidden="true"></i>
                                                                            </button>
                                                                            <button
                                                                                class="btn btn-danger btn-sm err_delete"
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
                                    </div>
                                </div>
                            @endif
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table id="doc_table" class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <th>Артикул</th>
                                            <th>Замена</th>
                                            <th>Номенклатура</th>
                                            <th>Характеристика</th>
                                            <th>Кол-во</th>
                                            <th>Ед.изм</th>
                                            <th>Цена</th>
                                            <th>Сумма</th>
                                            <th>Ставка НДС</th>
                                            <th>НДС</th>
                                            <th>Документ приобретения</th>
                                            <th>Действия</th>
                                        </tr>
                                        </thead>
                                        @if($rows)
                                            <tbody id="t_body">
                                            @foreach($rows as $k => $row)
                                                @if($row->free_pos)
                                                    <tr id="{{ $row->id }}">
                                                @else
                                                    <tr id="{{ $row->id }}" class="success">
                                                        @endif
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
                                                        <td>{{ $row->price }}</td>
                                                        <td>{{ $row->amount }}</td>
                                                        <td>{{ $row->vat }}</td>
                                                        <td>{{ $row->vat_amount }}</td>
                                                        <td>{!! $row->purchase !!}</td>
                                                        <td style="width:100px;">
                                                            <div class="form-group" role="group">
                                                                @if($row->good->has_specification)
                                                                    <button class="btn btn-info btn-sm pos_spec"
                                                                            type="button" title="Характеристики"><i
                                                                            class="fa fa-cog fa-lg"
                                                                            aria-hidden="true"></i>
                                                                    </button>
                                                                @endif
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
                $.ajax({
                    type: 'POST',
                    url: '{{ route('importOrderPos') }}',
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
                            alert('Заявка поставщику с таким номером, как в файле не обнаружена!');
                        if (res == 'ERR')
                            alert('При обновлении данных возникла ошибка!');
                        let obj = jQuery.parseJSON(res);
                        if (typeof obj === 'object') {
                            $(".modal").modal("hide");
                            alert('Загружено строк ' + obj.num + ' из ' + obj.rows + '.\n' +
                                'Позиций, не найденных в базе: ' + obj.err + '.\n' + 'Позиций с множественным выбором: ' + obj.multi);
                            location.reload();
                        }
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
                    url: '{{ route('addOrderPos') }}',
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

        $('#err_btn').click(function (e) {
            e.preventDefault();
            let error = 0;
            let id = $('#err_id').val();
            $("#err_pos").find(":input").each(function () {// проверяем каждое поле ввода в форме
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
                    url: '{{ route('editErrPos') }}',
                    data: $('#err_pos').serialize(),
                    success: function (res) {
                        //alert(res);
                        if (res == 'BAD') {
                            alert('У Вас нет прав для редактирования документа!');
                        }
                        if (res == 'NO') {
                            alert('Не известный запрос!');
                        }
                        if (res == 'DBL') {
                            alert('Позиция уже присутствует в заявке!');
                            $(".modal").modal("hide");
                            hide_row(id);
                        }
                        let obj = jQuery.parseJSON(res);
                        if (typeof obj === 'object') {
                            $('#state').text('Всего позиций: ' + obj.num + ' на сумму с НДС ' + obj.amount + ' руб.');
                            $('#rem').text('Заказано с НДС: ' + obj.amount + ' руб.');
                            $("#doc_table").append(obj.content);
                            $('#eprice').val('');
                            $('#search_evendor').val('');
                            //$('#by_name').val('');
                            $('#egood_id').empty();
                            $('#ecomment').empty();
                            $('#eqty').val('1');
                            $('#err_id').val('');
                            $(".modal").modal("hide");
                            hide_row(id);
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        alert(xhr.status);
                        alert(thrownError);
                    }
                });
            }
        });

        $('#btn_spec').click(function () {
            let error = 0;
            $("#import_spfc").find(":input").each(function () {// проверяем каждое поле ввода в форме
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
                    data: {'id': id, 'title': title, 'tbl_id':'order'},
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
                    data: {'id': id,'tbl_id':'sale'},
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
