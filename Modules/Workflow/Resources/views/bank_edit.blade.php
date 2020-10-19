@extends('layouts.main')

@section('dashboard')

@endsection
@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li><a href="{{ route('banks') }}">{{ $title }}</a></li>
        <li class="active">{{ $head }}</li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- page content -->
    <div class="container-fluid container-fullw bg-white">
        <div class="row">
            <h2 class="text-center">{{ $head }}</h2>
            {!! Form::open(['url' => route('bankEdit',['id'=>$data['id']]),'class'=>'form-horizontal','method'=>'POST', 'id'=>'form_ref']) !!}

            <div class="form-group">
                <label class="col-xs-3 control-label">
                    Наименование: <span class="symbol required" aria-required="true"></span>
                </label>
                <div class="col-xs-8">
                    {!! Form::text('title',$data['title'],['class' => 'form-control','placeholder'=>'Введите наименование','disabled'=>'disabled'])!!}
                    {!! $errors->first('title', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('bik','БИК:',['class' => 'col-xs-3 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::text('bik',$data['bik'],['class' => 'form-control','placeholder'=>'Введите БИК','maxlength'=>'10'])!!}
                    {!! $errors->first('bik', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-3 control-label">
                    SWIFT: <span class="symbol required" aria-required="true"></span>
                </label>
                <div class="col-xs-8">
                    {!! Form::text('swift',$data['swift'],['class' => 'form-control','placeholder'=>'Введите SWIFT','disabled'=>'disabled'])!!}
                    {!! $errors->first('swift', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-3 control-label">
                    Корр. счет: <span class="symbol required" aria-required="true"></span>
                </label>
                <div class="col-xs-8">
                    {!! Form::text('account',$data['account'],['class' => 'form-control','placeholder'=>'Введите корр счет','disabled'=>'disabled'])!!}
                    {!! $errors->first('account', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('city','Город:',['class' => 'col-xs-3 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::text('city',$data['city'],['class' => 'form-control','placeholder'=>'Укажите город','maxlength'=>'50'])!!}
                    {!! $errors->first('city', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('country','Страна:',['class' => 'col-xs-3 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::text('country',$data['country'],['class' => 'form-control','placeholder'=>'Укажите страну','maxlength'=>'50'])!!}
                    {!! $errors->first('country', '<p class="text-danger">:message</p>') !!}
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
