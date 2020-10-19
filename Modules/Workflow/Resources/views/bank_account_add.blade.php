@extends('layouts.main')

@section('dashboard')

@endsection
@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li><a href="{{ route('firmView',['id'=>$id]) }}">{{ $firm }}</a></li>
        <li class="active"><a href="{{ route('bank_accounts',['id'=>$id]) }}">{{ $title }}</a></li>
        <li class="active">{{ $head }}</li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- page content -->
    <div class="container-fluid container-fullw bg-white">
        <div class="row">
            <h2 class="text-center">{{ $head }}</h2>
            {!! Form::open(['url' => route('bank_accountAdd',['id'=>$id]),'class'=>'form-horizontal','method'=>'POST', 'id'=>'form_ref']) !!}

            <div class="form-group">
                <label class="col-xs-3 control-label">
                    Наименование: <span class="symbol required" aria-required="true"></span>
                </label>
                <div class="col-xs-8">
                    {!! Form::text('title',old('title'),['class' => 'form-control','placeholder'=>'Введите наименование','maxlength'=>'100','required'=>'required'])!!}
                    {!! $errors->first('title', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>
            @if(!$foreinger)
            <div class="form-group">
                <label class="col-xs-3 control-label">
                    БИК: <span class="symbol required" aria-required="true"></span>
                </label>
                <div class="col-xs-8">
                    {!! Form::text('bik',old('bik'),['class' => 'form-control','placeholder'=>'Введите БИК для выбора банка','maxlength'=>'10','required'=>'required','id'=>'search_bik'])!!}
                    {!! $errors->first('bik', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>
            @else
            <div class="form-group">
                <label class="col-xs-3 control-label">
                    SWIFT: <span class="symbol required" aria-required="true"></span>
                </label>
                <div class="col-xs-8">
                    {!! Form::text('swift',old('swift'),['class' => 'form-control','placeholder'=>'Введите SWIFT','maxlength'=>'15','required'=>'required','id'=>'search_swift'])!!}
                    {!! $errors->first('swift', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>
            @endif

            <div class="form-group">
                <label class="col-xs-3 control-label">
                    Номер счета: <span class="symbol required" aria-required="true"></span>
                </label>
                <div class="col-xs-8">
                    {!! Form::text('account',old('account'),['class' => 'form-control','placeholder'=>'Введите корр счет','maxlength'=>'25','required'=>'required'])!!}
                    {!! $errors->first('account', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-3 control-label">
                    Валюта: <span class="symbol required" aria-required="true"></span>
                </label>
                <div class="col-xs-8">
                    {!! Form::select('currency_id',$cursel, old('currency_id'), ['class' => 'form-control','required'=>'required','id'=>'currency_id']); !!}
                </div>
            </div>

            <div class="form-group">
            <div class="col-md-6 col-md-offset-3">
                <div class="col-md-4">
                    <div class="checkbox clip-check check-primary">
                        <input type="checkbox" name="status" id="status" value="1" checked>
                        <label for="status">
                            Действующий
                        </label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="checkbox clip-check check-primary">
                        <input type="checkbox" name="is_main" id="is_main" value="1">
                        <label for="is_main">
                            Основной
                        </label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="checkbox clip-check check-primary">
                        <input type="checkbox" name="for_pay" id="for_pay" value="1">
                        <label for="for_pay">
                            Для платежей
                        </label>
                    </div>
                </div>
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
    <script src="/js/bootstrap-typeahead.min.js"></script>
    <script>
        $('#search_swift').typeahead({
            hint: true,
            highlight: true,
            minLength: 3,
            ajax: {
                url: "{{ route('getSwift') }}",
                triggerLength: 1
            }
        });
        $('#search_bik').typeahead({
            hint: true,
            highlight: true,
            minLength: 3,
            ajax: {
                url: "{{ route('getBik') }}",
                triggerLength: 1
            }
        });
        $('#save_btn').click(function(){
            let error=0;
            $("#form_ref").find(":input").each(function() {// проверяем каждое поле ввода в форме
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
