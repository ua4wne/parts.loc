@extends('layouts.main')

@section('dashboard')

@endsection
@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li><a href="{{ route('pricing_rules') }}">{{ $title }}</a></li>
        <li class="active">{{ $head }}</li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- page content -->
    <div class="container-fluid container-fullw bg-white">
        <div class="row">
            <h2 class="text-center">{{ $head }}</h2>
            {!! Form::open(['url' => route('pricingRuleAdd'),'class'=>'form-horizontal','method'=>'POST','id'=>'form_ref']) !!}

            <div class="form-group">
                <label class="col-xs-3 control-label">
                    Наименование: <span class="symbol required" aria-required="true"></span>
                </label>
                <div class="col-xs-8">
                    {!! Form::text('title',old('title'),['class' => 'form-control','placeholder'=>'Введите наименование правила','maxlength'=>'100','required'=>'required'])!!}
                    {!! $errors->first('title', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-3 control-label">
                    Тип цены: <span class="symbol required" aria-required="true"></span>
                </label>
                <div class="col-xs-8">
                    {!! Form::select('price_type',['retail'=>'Розница','wholesale'=>'Оптовая','small'=>'Мелкооптовая'],
                        old('price_type'), ['class' => 'form-control','required'=>'required','id'=>'price_type']); !!}
                    {!! $errors->first('price_type', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-3 control-label">
                    Коэффициент: <span class="symbol required" aria-required="true"></span>
                </label>
                <div class="col-xs-8">
                    {!! Form::text('ratio',old('ratio'),['class' => 'form-control','placeholder'=>'Укажите коэффициент','maxlength'=>'8','required'=>'required'])!!}
                    {!! $errors->first('ratio', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-3 control-label">
                    Валюта: <span class="symbol required" aria-required="true"></span>
                </label>
                <div class="col-xs-8">
                    {!! Form::select('currency_id',$cursel, old('currency_id'), ['class' => 'form-control','required'=>'required','id'=>'curr_id']); !!}
                    {!! $errors->first('currency', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('category_id','Категория товаров:',['class' => 'col-xs-3 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::select('category_id',$catsel, old('category_id'), ['class' => 'form-control','id'=>'category_id']); !!}
                    {!! $errors->first('category_id', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('good_id','Номенклатура:',['class' => 'col-xs-3 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::text('good_id',old('good_id'),['class' => 'form-control','placeholder'=>'Начинайте вводить артикул','id'=>'search_vendor'])!!}
                    {!! $errors->first('good_id', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>


            <div class="form-group">
                <div class="col-xs-offset-2 col-xs-10">
                    {!! Form::button('Сохранить', ['class' => 'btn btn-primary','type'=>'submit','id'=>'save_btn']) !!}
                </div>
            </div>

            {!! Form::close() !!}
        </div>
    </div>
    <!-- /page content -->
@endsection

@section('user_script')
    <script src="/js/bootstrap-typeahead.min.js"></script>
    <script>
        $("#price_type").prepend($('<option value="0">Выберите тип цен</option>'));
        $("#price_type :first").attr("selected", "selected");
        $("#price_type :first").attr("disabled", "disabled");
        $("#curr_id").prepend($('<option value="0">Выберите валюту</option>'));
        $("#curr_id :first").attr("selected", "selected");
        $("#curr_id :first").attr("disabled", "disabled");
        $("#category_id").prepend($('<option value="0">Выберите категорию номенклатуры</option>'));
        $("#category_id :first").attr("selected", "selected");
        $("#category_id :first").attr("disabled", "disabled");

        $('#search_vendor').typeahead({
            hint: true,
            highlight: true,
            minLength: 3,
            ajax: {
                url: "{{ route('getCode') }}",
                triggerLength: 1
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
    </script>
@endsection
