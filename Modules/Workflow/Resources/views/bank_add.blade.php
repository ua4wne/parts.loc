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
            {!! Form::open(['url' => route('bankAdd'),'class'=>'form-horizontal','method'=>'POST', 'id'=>'form_ref']) !!}

            <div class="form-group">
                <label class="col-xs-3 control-label">
                    Наименование: <span class="symbol required" aria-required="true"></span>
                </label>
                <div class="col-xs-8">
                    {!! Form::text('title',old('title'),['class' => 'form-control','placeholder'=>'Введите наименование','maxlength'=>'70','required'=>'required','id'=>'title'])!!}
                    {!! $errors->first('title', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('bik','БИК:',['class' => 'col-xs-3 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::text('bik',old('bik'),['class' => 'form-control','placeholder'=>'Введите БИК','maxlength'=>'10','id'=>'bik'])!!}
                    {!! $errors->first('bik', '<p class="text-danger">:message</p>') !!}
                </div>
                <button type="button" class="btn btn-sm btn-o btn-info" title="Заполнить по БИК" id="fill_bik">
                    <i class="fa fa-refresh" aria-hidden="true"></i>
                </button>
            </div>

            <div class="form-group">
                <label class="col-xs-3 control-label">
                    SWIFT: <span class="symbol required" aria-required="true"></span>
                </label>
                <div class="col-xs-8">
                    {!! Form::text('swift',old('swift'),['class' => 'form-control','placeholder'=>'Введите SWIFT','maxlength'=>'15','required'=>'required','id'=>'swift'])!!}
                    {!! $errors->first('swift', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-3 control-label">
                    Корр. счет: <span class="symbol required" aria-required="true"></span>
                </label>
                <div class="col-xs-8">
                    {!! Form::text('account',old('account'),['class' => 'form-control','placeholder'=>'Введите корр счет','maxlength'=>'30','required'=>'required','id'=>'account'])!!}
                    {!! $errors->first('account', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('city','Город:',['class' => 'col-xs-3 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::text('city',old('city'),['class' => 'form-control','placeholder'=>'Укажите город','maxlength'=>'50','id'=>'city'])!!}
                    {!! $errors->first('city', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('country','Страна:',['class' => 'col-xs-3 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::text('country',old('country'),['class' => 'form-control','placeholder'=>'Укажите страну','maxlength'=>'50','id'=>'country'])!!}
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
        $('#fill_bik').hide();
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

        $("#bik").keyup(function(){
            let bik = $('#bik').val();
            if(bik.length>8)
                $('#fill_bik').show();
            else
                $('#fill_bik').hide();
        });

        $('#fill_bik').click(function(e){
            e.preventDefault();
            let bik = $('#bik').val();
            $.ajax({
                type: 'POST',
                url: '{{ route('bankFill') }}',
                data: {'bik':bik},
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    //alert(res);
                    if(res=='NO_API_KEY'){
                        alert('Токен не обнаружен!');
                    }
                    else{
                        let state = res.data.state.status;
                        if(state!='ACTIVE')
                            alert('Внимание! Банк закрыт!!!');
                         $('#bik').val(res.data.bic);
                         $('#swift').val(res.data.swift);
                         $('#title').val(res.data.name.payment);
                        $('#account').val(res.data.correspondent_account);
                        $('#city').val(res.data.payment_city);
                        $('#country').val(res.data.address.data.country);
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(xhr.status);
                    alert(thrownError);
                }
            });
        });

    </script>
@endsection
