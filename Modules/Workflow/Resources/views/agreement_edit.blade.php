@extends('layouts.main')

@section('dashboard')

@endsection
@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li><a href="{{ route('agreements') }}">{{ $title }}</a></li>
        <li class="active">{{ $head }}</li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- page content -->
    <div class="container-fluid container-fullw bg-white">
        <div class="row">
            <h2 class="text-center">{{ $head }}</h2>
            {!! Form::open(['url' => route('agreementEdit',['id'=>$data['id']]),'class'=>'form-horizontal','method'=>'POST', 'id'=>'form_ref']) !!}

            <div class="form-group">
                <label class="col-xs-3 control-label">
                    Номер: <span class="symbol required" aria-required="true"></span>
                </label>
                <div class="col-xs-8">
                    {!! Form::text('doc_num',$data['doc_num'],['class' => 'form-control','placeholder'=>'Введите номер','maxlength'=>'15','required'=>'required'])!!}
                    {!! $errors->first('doc_num', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-3 control-label">
                    Наименование: <span class="symbol required" aria-required="true"></span>
                </label>
                <div class="col-xs-8">
                    {!! Form::text('title',$data['title'],['class' => 'form-control','placeholder'=>'Введите наименование','maxlength'=>'150','required'=>'required'])!!}
                    {!! $errors->first('title', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-3 control-label">
                    Статус: <span class="symbol required" aria-required="true"></span>
                </label>
                <div class="col-xs-8">
                    {!! Form::select('statuse_id',$statsel,$data['statuse_id'],['class' => 'form-control','required'=>'required'])!!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('start', 'Дата начала действия:',['class'=>'col-xs-3 control-label']) !!}
                <div class="col-xs-8">
                    {{ Form::date('start', $data['start'],['class' => 'form-control']) }}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('finish', 'Дата окончания действия:',['class'=>'col-xs-3 control-label']) !!}
                <div class="col-xs-8">
                    {{ Form::date('finish', $data['finish'],['class' => 'form-control']) }}
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-3 control-label">
                    Организация: <span class="symbol required" aria-required="true"></span>
                </label>
                <div class="col-xs-8">
                    {!! Form::select('organisation_id',$orgsel, $data['organisation_id'], ['class' => 'form-control','required'=>'required']); !!}
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-3 control-label">
                    Валюта: <span class="symbol required" aria-required="true"></span>
                </label>
                <div class="col-xs-8">
                    {!! Form::select('currency_id',$cursel, $data['currency_id'], ['class' => 'form-control','required'=>'required']); !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('comment', 'Комментарий:',['class'=>'col-xs-3 control-label']) !!}
                <div class="col-xs-8">
                    {!! Form::textarea('comment',$data['comment'],['class'=>'form-control', 'rows' => 2, 'cols' => 50]) !!}
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
