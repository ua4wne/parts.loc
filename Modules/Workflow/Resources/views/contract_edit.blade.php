@extends('layouts.main')

@section('dashboard')

@endsection
@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li><a href="{{ route('firmView',['id'=>$data['firm_id']]) }}">{{ $firm }}</a></li>
        <li class="active"><a href="{{ route('contracts',['id'=>$data['firm_id']]) }}">{{ $title }}</a></li>
        <li class="active">{{ $head }}</li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- page content -->
    <div class="container-fluid container-fullw bg-white">
        <div class="row">
            <h2 class="text-center">{{ $head }}</h2>
            {!! Form::open(['url' => route('contractEdit',['id'=>$data['id']]),'class'=>'form-horizontal','method'=>'POST', 'id'=>'form_ref']) !!}

            <div class="form-group">
                <label class="col-xs-3 control-label">
                    Номер: <span class="symbol required" aria-required="true"></span>
                </label>
                <div class="col-xs-8">
                    {!! Form::text('doc_num',$data['doc_num'],['class' => 'form-control','placeholder'=>'Укажите номер договора','maxlength'=>'15','required'=>'required'])!!}
                    {!! $errors->first('doc_num', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-3 control-label">
                    Статус: <span class="symbol required" aria-required="true"></span>
                </label>
                <div class="col-xs-8">
                    {!! Form::select('status',['1'=>'Действует','0'=>'Не действует'], $data['status'], ['class' => 'form-control','required'=>'required']); !!}
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
                {!! Form::label('print_title','Наименование для печати:',['class' => 'col-xs-3 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::text('print_title',$data['print_title'],['class' => 'form-control','placeholder'=>'Введите наименование для печати','maxlength'=>'150'])!!}
                    {!! $errors->first('print_title', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('start','Дата начала:',['class' => 'col-xs-3 control-label'])   !!}
                <div class="col-xs-8">
                    {{ Form::date('start', \Carbon\Carbon::parse($data['start']),['class' => 'form-control']) }}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('finish','Дата окончания:',['class' => 'col-xs-3 control-label'])   !!}
                <div class="col-xs-8">
                    {{ Form::date('finish', \Carbon\Carbon::parse($data['finish']),['class' => 'form-control']) }}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('type','Тип взаимоотношений:',['class' => 'col-xs-3 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::text('type',$data['type'],['class' => 'form-control','placeholder'=>'Укажите тип взаимоотношений','maxlength'=>'100'])!!}
                    {!! $errors->first('type', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('org','Организация:',['class' => 'col-xs-3 control-label'])   !!}
                {!! Form::hidden('organisation_id',$data['organisation_id'],['class' => 'form-control','id'=>'organisation_id','required'=>'required']) !!}
                <div class="col-xs-8">
                    {!! Form::text('org',$org,['class' => 'form-control','disabled'=>'disabled'])!!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('org_acc','Счет организации:',['class' => 'col-xs-3 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::text('org_acc',$data['org_acc'],['class' => 'form-control','maxlength'=>'25'])!!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('firm','Контрагент:',['class' => 'col-xs-3 control-label'])   !!}
                {!! Form::hidden('firm_id',$data['firm_id'],['class' => 'form-control','id'=>'firm_id','required'=>'required']) !!}
                <div class="col-xs-8">
                    {!! Form::text('firm',$firm,['class' => 'form-control','disabled'=>'disabled'])!!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('firm_acc','Счет контрагента:',['class' => 'col-xs-3 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::select('firm_acc',$basel, $data['firm_acc'], ['class' => 'form-control','id'=>'firm_acc']); !!}
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-3 control-label">
                    Валюта взаиморасчетов: <span class="symbol required" aria-required="true"></span>
                </label>
                <div class="col-xs-8">
                    {!! Form::select('currency_id',$cursel, $data['currency_id'], ['class' => 'form-control','required'=>'required','id'=>'currency_id']); !!}
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-3 control-label">
                    Менеджер: <span class="symbol required" aria-required="true"></span>
                </label>
                <div class="col-xs-8">
                    {!! Form::select('user_id',$usel, $data['user_id'], ['class' => 'form-control','required'=>'required','id'=>'user_id']); !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('uip','УИП:',['class' => 'col-xs-3 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::text('uip',$data['uip'],['class' => 'form-control','maxlength'=>'100'])!!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('gosid','Идентификатор госконтракта:',['class' => 'col-xs-3 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::text('gosid',$data['gosid'],['class' => 'form-control','maxlength'=>'100'])!!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('delivery_method','Способ доставки:',['class' => 'col-xs-3 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::text('delivery_method',$data['delivery_method'],['class' => 'form-control','maxlength'=>'150'])!!}
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
