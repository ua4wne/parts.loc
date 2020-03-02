@extends('layouts.main')

@section('dashboard')

@endsection
@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li><a href="{{ route('users') }}">Пользователи</a></li>
        <li class="active">Новая запись</li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- page content -->
    <div class="container-fluid container-fullw bg-white">
        <div class="row">
            <h2 class="text-center">Новый пользователь</h2>

            {!! Form::open(['url' => route('userAdd'),'class'=>'form-horizontal','method'=>'POST','id'=>'new_rec']) !!}

            <div class="form-group">
                {!! Form::label('login','Логин:',['class' => 'col-xs-2 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::text('login',old('login'),['class' => 'form-control','placeholder'=>'Введите логин','required'=>'','size'=>'50'])!!}
                    {!! $errors->first('login', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('name','ФИО:',['class' => 'col-xs-2 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::text('name',old('name'),['class' => 'form-control','placeholder'=>'Введите ФИО','required'=>''])!!}
                    {!! $errors->first('name', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('email','E-mail:',['class' => 'col-xs-2 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::email('email',old('email'),['class' => 'form-control','placeholder'=>'Введите e-mail','required'=>''])!!}
                    {!! $errors->first('email', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('sex', 'Пол:',['class'=>'col-xs-2 control-label']) !!}
                <div class="col-xs-8">
                    {!! Form::select('sex', array('male' => 'Мужской', 'female' => 'Женский'), 'male',['class' => 'form-control','required'=>'required']); !!}
                </div>
            </div>

            <div class="form-group">
                <div class="col-xs-offset-2 col-xs-10">
                    {!! Form::button('Сохранить', ['class' => 'btn btn-primary','type'=>'submit']) !!}
                </div>
            </div>

            {!! Form::close() !!}
        </div>
    </div>

@endsection

@section('user_script')
    <script>
        $( "#new_rec" ).submit(function( event ) {
            let error=0;
            $("#new_rec").find(":input").each(function() {// проверяем каждое поле ввода в форме
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
                event.preventDefault();
            }
        });
    </script>
@endsection
