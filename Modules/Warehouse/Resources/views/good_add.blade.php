@extends('layouts.main')

@section('dashboard')

@endsection
@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li><a href="{{ route('goods') }}">{{ $title }}</a></li>
        <li class="active">{{ $head }}</li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- page content -->
    <div class="container-fluid container-fullw bg-white">
        <div class="row">
            <h2 class="text-center">{{ $head }}</h2>
            {!! Form::open(['url' => route('goodAdd'),'class'=>'form-horizontal','method'=>'POST', 'id'=>'form_wx']) !!}

            <div class="form-group">
                <label class="col-xs-3 control-label">
                    Категория номенклатуры: <span class="symbol required" aria-required="true"></span>
                </label>
                <div class="col-xs-8">
                    {!! Form::select('category_id',$catsel, old('category_id'), ['class' => 'form-control','required'=>'required']); !!}
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-3 control-label">
                    Товарная группа: <span class="symbol required" aria-required="true"></span>
                </label>
                <div class="col-xs-8">
                    {!! Form::select('group_id',$groupsel, old('group_id'), ['class' => 'form-control','required'=>'required']); !!}
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-3 control-label">
                    Наименование: <span class="symbol required" aria-required="true"></span>
                </label>
                <div class="col-xs-8">
                    {!! Form::text('title',old('title'),['class' => 'form-control','placeholder'=>'Введите наименование','maxlength'=>'200','required'=>'required'])!!}
                    {!! $errors->first('title', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('descr','Описание:',['class' => 'col-xs-3 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::text('descr',old('descr'),['class' => 'form-control','placeholder'=>'Введите описание','maxlength'=>'255'])!!}
                    {!! $errors->first('descr', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('bx_group','Код группы в каталоге на сайте:',['class' => 'col-xs-3 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::text('bx_group',old('bx_group'),['class' => 'form-control','placeholder'=>'Введите код группы','maxlength'=>'5'])!!}
                    {!! $errors->first('bx_group', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-3 control-label">
                    Артикул: <span class="symbol required" aria-required="true"></span>
                </label>
                <div class="col-xs-8">
                    {!! Form::text('vendor_code',old('vendor_code'),['class' => 'form-control','placeholder'=>'Введите артикул','maxlength'=>'64'])!!}
                    {!! $errors->first('vendor_code', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('code','Код:',['class' => 'col-xs-3 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::text('code',old('code'),['class' => 'form-control','placeholder'=>'Введите код номенклатуры','maxlength'=>'10'])!!}
                    {!! $errors->first('code', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('catalog_num','Каталожный №:',['class' => 'col-xs-3 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::text('catalog_num',old('code'),['class' => 'form-control','placeholder'=>'Введите номер по каталогу','maxlength'=>'30'])!!}
                    {!! $errors->first('catalog_num', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('analog_code','Коды аналогов:',['class' => 'col-xs-3 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::text('analog_code',old('analog_code'),['class' => 'form-control','placeholder'=>'Введите артикулы аналогов через запятую','maxlength'=>'64'])!!}
                    {!! $errors->first('analog_code', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('brand','Производитель:',['class' => 'col-xs-3 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::text('brand',old('brand'),['class' => 'form-control','placeholder'=>'Укажите производителя','maxlength'=>'200'])!!}
                    {!! $errors->first('brand', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('model','Модель:',['class' => 'col-xs-3 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::text('model',old('model'),['class' => 'form-control','placeholder'=>'Укажите модель','maxlength'=>'200'])!!}
                    {!! $errors->first('model', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-3 control-label">
                    Основная ед. измерения: <span class="symbol required" aria-required="true"></span>
                </label>
                <div class="col-xs-8">
                    {!! Form::select('unit_id',$unitsel, old('unit_id'), ['class' => 'form-control','required'=>'required']); !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('weight','Вес, кг:',['class' => 'col-xs-3 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::text('weight',old('weight'),['class' => 'form-control','placeholder'=>'Укажите вес'])!!}
                    {!! $errors->first('weight', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('capacity','Объем, м3:',['class' => 'col-xs-3 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::text('capacity',old('capacity'),['class' => 'form-control','placeholder'=>'Укажите объем'])!!}
                    {!! $errors->first('capacity', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('length','Длина, м:',['class' => 'col-xs-3 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::text('length',old('length'),['class' => 'form-control','placeholder'=>'Укажите длину'])!!}
                    {!! $errors->first('length', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('area','Площадь, м2:',['class' => 'col-xs-3 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::text('area',old('area'),['class' => 'form-control','placeholder'=>'Укажите площадь'])!!}
                    {!! $errors->first('area', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('vat','Ставка НДС, %:',['class' => 'col-xs-3 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::text('vat',old('vat'),['class' => 'form-control','placeholder'=>'Укажите процент НДС'])!!}
                    {!! $errors->first('vat', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('gtd','Учет по ГДТ:',['class' => 'col-xs-3 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::select('gtd',['0'=>'Нет','1'=>'Да'], old('gtd'), ['class' => 'form-control']); !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('wx_position','Складская позиция:',['class' => 'col-xs-3 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::select('wx_position',['0'=>'Нет','1'=>'Да'], old('wx_position'), ['class' => 'form-control']); !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('barcode','Штрихкод:',['class' => 'col-xs-3 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::text('barcode',old('barcode'),['class' => 'form-control','placeholder'=>'Введите штрихкод','maxlength'=>'100'])!!}
                    {!! $errors->first('barcode', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                <div class="col-xs-offset-2 col-xs-8">
                    {!! Form::button('Сохранить', ['class' => 'btn btn-primary','type'=>'submit', 'id'=>'save_btn']) !!}
                </div>
            </div>

            {!! Form::close() !!}
        </div>
    </div>
    <!-- /page content -->
@endsection

@section('user_script')
    <script>
        $('#save_btn').click(function(){
            let error=0;
            $("#form_wx").find(":input").each(function() {// проверяем каждое поле ввода в форме
                if($(this).attr("required")=='required'){ //обязательное для заполнения поле формы?
                    if(!$(this).val()){// если поле пустое
                        $(this).css('border', '1px solid red');// устанавливаем рамку красного цвета
                        error=1;// определяем индекс ошибки
                    }
                    else{
                        $(this).css('border', '1px solid green');// устанавливаем рамку зеленого цвета
                    }

                }
            })
            if(error){
                alert("Необходимо заполнять все доступные поля!");
                return false;
            }
        });
    </script>
@endsection
