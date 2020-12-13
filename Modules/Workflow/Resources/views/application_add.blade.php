@extends('layouts.main')

@section('dashboard')

@endsection
@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li><a href="{{ route('applications') }}">{{ $title }}</a></li>
        <li class="active">{{ $head }}</li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- page content -->
    <div class="container-fluid container-fullw bg-white">
        <div class="row">
            <h2 class="text-center">{{ $head }}</h2>
            {!! Form::open(['url' => route('applicationAdd'),'class'=>'form-horizontal','method'=>'POST', 'id'=>'form_ref']) !!}

            <div class="form-group">
                <label class="col-xs-3 control-label">
                    Номер: <span class="symbol required" aria-required="true"></span>
                </label>
                <div class="col-xs-8">
                    {!! Form::text('doc_num',$doc_num,['class' => 'form-control','placeholder'=>'Введите номер','maxlength'=>'15','required'=>'required'])!!}
                    {!! $errors->first('doc_num', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-3 control-label">
                    Приоритет: <span class="symbol required" aria-required="true"></span>
                </label>
                <div class="col-xs-8">
                    {!! Form::select('priority_id',$psel,old('priority_id'),['class' => 'form-control','required'=>'required','id'=>'priority_id'])!!}
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-3 control-label">
                    Степень важности: <span class="symbol required" aria-required="true"></span>
                </label>
                <div class="col-xs-8">
                    <div class="col-xs-8">
                        {!! Form::number('rank','10',['class' => 'form-control','placeholder'=>'Введите число','min' => 0, 'max' => 250])!!}
                        {!! $errors->first('rank', '<p class="text-danger">:message</p>') !!}
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-3 control-label">
                    Статус: <span class="symbol required" aria-required="true"></span>
                </label>
                <div class="col-xs-8">
                    {!! Form::select('statuse_id',$statsel,old('statuse_id'),['class' => 'form-control','required'=>'required'])!!}
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-3 control-label">
                    Заявка клиента: <span class="symbol required" aria-required="true"></span>
                </label>
                <div class="col-xs-8">
                    {!! Form::text('sale_id',old('sale_id'),['class' => 'form-control','placeholder'=>'Введите номер заявки','required'=>'required','id'=>'search'])!!}
                    {!! $errors->first('sale_id', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-3 control-label">
                    Ответственный: <span class="symbol required" aria-required="true"></span>
                </label>
                <div class="col-xs-8">
                    {!! Form::select('user_id',$usel, old('user_id'), ['class' => 'form-control','required'=>'required','id'=>'user_id']); !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('comment', 'Комментарий:',['class'=>'col-xs-3 control-label']) !!}
                <div class="col-xs-8">
                    {!! Form::textarea('comment',old('comment'),['class'=>'form-control', 'rows' => 2, 'cols' => 50]) !!}
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
        $("#priority_id").prepend($('<option value="0">Задайте приоритет</option>'));
        $("#priority_id :first").attr("selected", "selected");
        $("#priority_id :first").attr("disabled", "disabled");

        $("#user_id").prepend($('<option value="0">Укажите ответственного</option>'));
        $("#user_id :first").attr("selected", "selected");
        $("#user_id :first").attr("disabled", "disabled");

        $('#search').typeahead({
            hint: true,
            highlight: true,
            minLength: 3,
            ajax: {
                url: "{{ route('getSale') }}",
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
