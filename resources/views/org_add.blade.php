@extends('layouts.main')

@section('dashboard')

@endsection
@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li><a href="{{ route('orgs') }}">{{ $title }}</a></li>
        <li class="active">{{ $head }}</li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- page content -->
    <div class="container-fluid container-fullw bg-white">
        <div class="row">
            <h2 class="text-center">{{ $head }}</h2>
            {!! Form::open(['url' => route('orgAdd'),'class'=>'form-horizontal','method'=>'POST', 'id'=>'form_org']) !!}

            <div class="form-group">
                <label class="col-xs-3 control-label">
                    Полное название: <span class="symbol required" aria-required="true"></span>
                </label>
                <div class="col-xs-8">
                    {!! Form::text('title',old('title'),['class' => 'form-control','placeholder'=>'Введите полное название организации','maxlength'=>'150','required'=>'required'])!!}
                    {!! $errors->first('title', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-3 control-label">
                    Организационная форма: <span class="symbol required" aria-required="true"></span>
                </label>
                <div class="col-xs-8">
                    {!! Form::select('org_form_id',$orgsel, old('org_form_id'), ['class' => 'form-control','required'=>'required']); !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('print_name','Название для документов:',['class' => 'col-xs-3 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::text('print_name',old('print_name'),['class' => 'form-control','placeholder'=>'Введите название для документов','maxlength'=>'150'])!!}
                    {!! $errors->first('print_name', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-3 control-label">
                    Короткое название: <span class="symbol required" aria-required="true"></span>
                </label>
                <div class="col-xs-8">
                    {!! Form::text('short_name',old('short_name'),['class' => 'form-control','placeholder'=>'Введите короткое название','maxlength'=>'100','required'=>'required'])!!}
                    {!! $errors->first('short_name', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('inn','ИНН:',['class' => 'col-xs-3 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::text('inn',old('inn'),['class' => 'form-control','placeholder'=>'Укажите ИНН','maxlength'=>'12'])!!}
                    {!! $errors->first('inn', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('kpp','КПП:',['class' => 'col-xs-3 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::text('kpp',old('kpp'),['class' => 'form-control','placeholder'=>'Укажите КПП','maxlength'=>'9'])!!}
                    {!! $errors->first('kpp', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('ogrn','ОГРН:',['class' => 'col-xs-3 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::text('ogrn',old('ogrn'),['class' => 'form-control','placeholder'=>'Укажите ОГРН','maxlength'=>'15'])!!}
                    {!! $errors->first('ogrn', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-3 control-label">
                    Статус: <span class="symbol required" aria-required="true"></span>
                </label>
                <div class="col-xs-8">
                    {!! Form::select('status',['0'=>'Не активная','1'=>'Активная'], old('status'), ['class' => 'form-control','required'=>'required']); !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('prefix','Префикс для документов:',['class' => 'col-xs-3 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::text('prefix',old('prefix'),['class' => 'form-control','placeholder'=>'Укажите префикс для документов','maxlength'=>'10'])!!}
                    {!! $errors->first('prefix', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('account','Расчетный счет:',['class' => 'col-xs-3 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::text('account',old('account'),['class' => 'form-control','placeholder'=>'Введите расчетный счет','maxlength'=>'25'])!!}
                    {!! $errors->first('account', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('legal_address','Юридический адрес:',['class' => 'col-xs-3 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::text('legal_address',old('legal_address'),['class' => 'form-control','placeholder'=>'Укажите юридический адрес','maxlength'=>'255'])!!}
                    {!! $errors->first('legal_address', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('post_address','Почтовый адрес:',['class' => 'col-xs-3 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::text('post_address',old('post_address'),['class' => 'form-control','placeholder'=>'Укажите почтовый адрес','maxlength'=>'255'])!!}
                    {!! $errors->first('post_address', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('phone','Телефон:',['class' => 'col-xs-3 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::text('phone',old('phone'),['class' => 'form-control input-mask-phone','placeholder'=>'Укажите телефон','maxlength'=>'20'])!!}
                    {!! $errors->first('phone', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('email','E-mail:',['class' => 'col-xs-3 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::email('email',old('email'),['class' => 'form-control','placeholder'=>'Укажите e-mail','maxlength'=>'30'])!!}
                    {!! $errors->first('email', '<p class="text-danger">:message</p>') !!}
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
    <script src="/js/jquery.maskedinput.min.js"></script>
    <script>
        $('.input-mask-phone').mask('(999) 999-9999');

        $('#save_btn').click(function(){
            let error=0;
            $("#form_org").find(":input").each(function() {// проверяем каждое поле ввода в форме
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
