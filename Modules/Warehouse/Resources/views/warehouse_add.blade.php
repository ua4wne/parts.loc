@extends('layouts.main')

@section('dashboard')

@endsection
@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li><a href="{{ route('warehouses') }}">{{ $title }}</a></li>
        <li class="active">{{ $head }}</li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- page content -->
    <div class="container-fluid container-fullw bg-white">
        <div class="row">
            <h2 class="text-center">{{ $head }}</h2>
            {!! Form::open(['url' => route('warehouseAdd'),'class'=>'form-horizontal','method'=>'POST', 'id'=>'form_wx']) !!}

            <div class="form-group">
                <label class="col-xs-3 control-label">
                    Организация: <span class="symbol required" aria-required="true"></span>
                </label>
                <div class="col-xs-8">
                    {!! Form::select('organisation_id',$orgsel, old('organisation_id'), ['class' => 'form-control','required'=>'required']); !!}
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-3 control-label">
                    Наименование: <span class="symbol required" aria-required="true"></span>
                </label>
                <div class="col-xs-8">
                    {!! Form::text('title',old('title'),['class' => 'form-control','placeholder'=>'Введите наименование','maxlength'=>'100','required'=>'required'])!!}
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
                <label class="col-xs-3 control-label">
                    Ответственный: <span class="symbol required" aria-required="true"></span>
                </label>
                <div class="col-xs-8">
                    {!! Form::select('user_id',$usersel, old('user_id'), ['class' => 'form-control','required'=>'required']); !!}
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
