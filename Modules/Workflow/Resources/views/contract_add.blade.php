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
        <li class="active"><a href="{{ route('contracts',['id'=>$id]) }}">{{ $title }}</a></li>
        <li class="active">{{ $head }}</li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- page content -->
    <div class="container-fluid container-fullw bg-white">
        <div class="row">
            <h2 class="text-center">{{ $head }}</h2>
            {!! Form::open(['url' => route('contractAdd',['id'=>$id]),'class'=>'form-horizontal','method'=>'POST', 'id'=>'form_ref']) !!}

            <div class="form-group">
                <label class="col-xs-3 control-label">
                    Номер: <span class="symbol required" aria-required="true"></span>
                </label>
                <div class="col-xs-8">
                    {!! Form::text('doc_num',old('doc_num'),['class' => 'form-control','placeholder'=>'Укажите номер договора','maxlength'=>'15','required'=>'required'])!!}
                    {!! $errors->first('doc_num', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-3 control-label">
                    Статус: <span class="symbol required" aria-required="true"></span>
                </label>
                <div class="col-xs-8">
                    {!! Form::select('status',['1'=>'Действует','0'=>'Не действует'], old('status'), ['class' => 'form-control','required'=>'required']); !!}
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-3 control-label">
                    Наименование: <span class="symbol required" aria-required="true"></span>
                </label>
                <div class="col-xs-8">
                    {!! Form::text('title',old('title'),['class' => 'form-control','placeholder'=>'Введите наименование','maxlength'=>'150','required'=>'required'])!!}
                    {!! $errors->first('title', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('print_title','Наименование для печати:',['class' => 'col-xs-3 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::text('print_title',old('print_title'),['class' => 'form-control','placeholder'=>'Введите наименование для печати','maxlength'=>'150'])!!}
                    {!! $errors->first('print_title', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('start','Дата начала:',['class' => 'col-xs-3 control-label'])   !!}
                <div class="col-xs-8">
                    {{ Form::date('start', \Carbon\Carbon::parse()->format('d-m-Y'),['class' => 'form-control']) }}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('finish','Дата окончания:',['class' => 'col-xs-3 control-label'])   !!}
                <div class="col-xs-8">
                    {{ Form::date('finish', \Carbon\Carbon::parse()->format('d-m-Y'),['class' => 'form-control']) }}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('type','Тип взаимоотношений:',['class' => 'col-xs-3 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::text('type',old('type'),['class' => 'form-control','placeholder'=>'Укажите тип взаимоотношений','maxlength'=>'100'])!!}
                    {!! $errors->first('type', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-3 control-label">
                    Организация: <span class="symbol required" aria-required="true"></span>
                </label>
                <div class="col-xs-8">
                    {!! Form::select('organisation_id',$orgsel, old('organisation_id'), ['class' => 'form-control','required'=>'required','id'=>'organisation_id']); !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('org_acc','Счет организации:',['class' => 'col-xs-3 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::select('org_acc',array(),old('org_acc'),['class' => 'form-control','id'=>'org_acc'])!!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('firm','Контрагент:',['class' => 'col-xs-3 control-label'])   !!}
                {!! Form::hidden('firm_id',$id,['class' => 'form-control','id'=>'firm_id','required'=>'required']) !!}
                <div class="col-xs-8">
                    {!! Form::text('firm',$firm,['class' => 'form-control','disabled'=>'disabled'])!!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('firm_acc','Счет контрагента:',['class' => 'col-xs-3 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::select('firm_acc',$basel, old('firm_acc'), ['class' => 'form-control','id'=>'firm_acc']); !!}
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-3 control-label">
                    Валюта взаиморасчетов: <span class="symbol required" aria-required="true"></span>
                </label>
                <div class="col-xs-8">
                    {!! Form::select('currency_id',$cursel, old('currency_id'), ['class' => 'form-control','required'=>'required','id'=>'currency_id']); !!}
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-3 control-label">
                    Менеджер: <span class="symbol required" aria-required="true"></span>
                </label>
                <div class="col-xs-8">
                    {!! Form::select('user_id',$usel, old('user_id'), ['class' => 'form-control','required'=>'required','id'=>'user_id']); !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('uip','УИП:',['class' => 'col-xs-3 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::text('uip',old('uip'),['class' => 'form-control','maxlength'=>'100'])!!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('gosid','Идентификатор госконтракта:',['class' => 'col-xs-3 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::text('gosid',old('gosid'),['class' => 'form-control','maxlength'=>'100'])!!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('delivery_method','Способ доставки:',['class' => 'col-xs-3 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::text('delivery_method',old('delivery_method'),['class' => 'form-control','maxlength'=>'150'])!!}
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
        $( document ).ready(function() {
            $("#organisation_id").prepend($('<option value="0">Выберите организацию</option>'));
            $("#organisation_id :first").attr("selected", "selected");
            $("#organisation_id :first").attr("disabled", "disabled");
            $("#user_id").prepend($('<option value="0">Выберите ответственного менеджера</option>'));
            $("#user_id :first").attr("selected", "selected");
            $("#user_id :first").attr("disabled", "disabled");
            $("#firm_acc").prepend($('<option value="0">Выберите счет контрагента</option>'));
            $("#firm_acc :first").attr("selected", "selected");
            $("#firm_acc :first").attr("disabled", "disabled");
            $("#currency_id").prepend($('<option value="0">Выберите валюту взаиморасчетов</option>'));
            $("#currency_id :first").attr("selected", "selected");
            $("#currency_id :first").attr("disabled", "disabled");
        });

        $( "#organisation_id" ).change(function() {
            $("#org_acc").empty(); //очищаем от старых значений
            //var firm = $("#firm_id").val();
            var org_id = $("#organisation_id option:selected").val();
            $.ajax({
                type: 'POST',
                url: '{{ route('findOrgAcc') }}',
                data: {'org_id':org_id},
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    //alert(res);
                    $("#org_acc").prepend($(res));
                }
            });
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
