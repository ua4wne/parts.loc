@extends('layouts.main')

@section('dashboard')

@endsection
@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li><a href="{{ route('firms') }}">{{ $title }}</a></li>
        <li class="active">{{ $head }}</li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- page content -->
    <div class="container-fluid container-fullw bg-white">
        <div class="row">
            {!! Form::open(['url' => route('firmAdd'),'class'=>'form-horizontal','method'=>'POST', 'id'=>'form_ref']) !!}
            <div class="col-md-10">
                <h2 class="text-center">{{ $head }}</h2>
                <div class="tabbable pills">
                    <ul id="myTab3" class="nav nav-pills">
                        <li class="active">
                            <a href="#common" data-toggle="tab">
                                Общая информация
                            </a>
                        </li>
                        <li>
                            <a href="#contacts" data-toggle="tab">
                                Адреса, телефоны
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade in active" id="common">
                            <div class="form-group">
                                <label class="col-xs-3 control-label">
                                    Вид контрагента: <span class="symbol required" aria-required="true"></span>
                                </label>
                                <div class="col-xs-8">
                                    {!! Form::select('firm_type_id',$ftsel, old('firm_type_id'), ['class' => 'form-control','required'=>'required','id'=>'ftype']); !!}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label">
                                    Код: <span class="symbol required" aria-required="true"></span>
                                </label>
                                <div class="col-xs-8">
                                    {!! Form::text('code',old('code'),['class' => 'form-control','placeholder'=>'Введите код','maxlength'=>'12','required'=>'required'])!!}
                                    {!! $errors->first('code', '<p class="text-danger">:message</p>') !!}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label">
                                    ИНН: <span class="symbol required" aria-required="true"></span>
                                </label>
                                <div class="col-xs-8">
                                    {!! Form::text('inn',old('inn'),['class' => 'form-control','placeholder'=>'Укажите ИНН','maxlength'=>'12','required'=>'required','id'=>'inn'])!!}
                                    {!! $errors->first('inn', '<p class="text-danger">:message</p>') !!}
                                </div>
                                <button type="button" class="btn btn-sm btn-o btn-info" title="Заполнить по ИНН" id="fill_btn">
                                    <i class="fa fa-refresh" aria-hidden="true"></i>
                                </button>
                            </div>

                            <div class="form-group">
                                {!! Form::label('kpp','КПП:',['class' => 'col-xs-3 control-label'])   !!}
                                <div class="col-xs-8">
                                    {!! Form::text('kpp',old('kpp'),['class' => 'form-control','placeholder'=>'Укажите КПП','maxlength'=>'9','id'=>'kpp'])!!}
                                    {!! $errors->first('kpp', '<p class="text-danger">:message</p>') !!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('okpo','Код по ОКПО:',['class' => 'col-xs-3 control-label'])   !!}
                                <div class="col-xs-8">
                                    {!! Form::text('okpo',old('okpo'),['class' => 'form-control','placeholder'=>'Укажите код ОКПО','maxlength'=>'10','id'=>'okpo'])!!}
                                    {!! $errors->first('okpo', '<p class="text-danger">:message</p>') !!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('country_id','Страна регистрации:',['class' => 'col-xs-3 control-label'])   !!}
                                <div class="col-xs-8">
                                    {!! Form::select('consel',$consel, old('consel'), ['class' => 'form-control','id'=>'country_id']); !!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('tax_number','Налоговый номер:',['class' => 'col-xs-3 control-label'])   !!}
                                <div class="col-xs-8">
                                    {!! Form::text('tax_number',old('tax_number'),['class' => 'form-control','placeholder'=>'Укажите налоговый номер','maxlength'=>'30','id'=>'tax_number'])!!}
                                    {!! $errors->first('tax_number', '<p class="text-danger">:message</p>') !!}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label">
                                    Полное наименование: <span class="symbol required" aria-required="true"></span>
                                </label>
                                <div class="col-xs-8">
                                    {!! Form::text('title',old('title'),['class' => 'form-control','placeholder'=>'Введите полное наименование','maxlength'=>'254','required'=>'required','id'=>'title'])!!}
                                    {!! $errors->first('title', '<p class="text-danger">:message</p>') !!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('name','Рабочее наименование:',['class' => 'col-xs-3 control-label'])   !!}
                                <div class="col-xs-8">
                                    {!! Form::text('name',old('name'),['class' => 'form-control','placeholder'=>'Введите рабочее наименование','maxlength'=>'150','id'=>'name'])!!}
                                    {!! $errors->first('name', '<p class="text-danger">:message</p>') !!}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label">
                                    Основной менеджер: <span class="symbol required" aria-required="true"></span>
                                </label>
                                <div class="col-xs-8">
                                    {!! Form::select('user_id',$usel, old('user_id'), ['class' => 'form-control','required'=>'required','id'=>'user_id']); !!}
                                </div>
                            </div>

                            <div class="col-md-8 col-md-offset-1">
                                <div class="col-md-3">
                                    <div class="checkbox clip-check check-primary">
                                        <input type="checkbox" name="client" id="client" value="1">
                                        <label for="client">
                                            Клиент
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="checkbox clip-check check-primary">
                                        <input type="checkbox" name="provider" id="provider" value="1">
                                        <label for="provider">
                                            Поставщик
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="checkbox clip-check check-primary">
                                        <input type="checkbox" name="foreigner" id="foreigner" value="1">
                                        <label for="foreigner">
                                            Иностранный поставщик
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="checkbox clip-check check-primary">
                                        <input type="checkbox" name="other" id="other" value="1">
                                        <label for="other">
                                            Другое
                                        </label>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="tab-pane fade" id="contacts">
                            <div class="form-group">
                                <label class="col-xs-3 control-label">
                                    Фамилия контактного лица: <span class="symbol required" aria-required="true"></span>
                                </label>
                                <div class="col-xs-8">
                                    {!! Form::text('lname',old('lname'),['class' => 'form-control','placeholder'=>'Введите фамилию','maxlength'=>'70','required'=>'required','id'=>'lname'])!!}
                                    {!! $errors->first('lname', '<p class="text-danger">:message</p>') !!}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label">
                                    Имя контактного лица: <span class="symbol required" aria-required="true"></span>
                                </label>
                                <div class="col-xs-8">
                                    {!! Form::text('fname',old('fname'),['class' => 'form-control','placeholder'=>'Введите имя','maxlength'=>'70','required'=>'required','id'=>'fname'])!!}
                                    {!! $errors->first('fname', '<p class="text-danger">:message</p>') !!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('mname','Отчество контактного лица:',['class' => 'col-xs-3 control-label'])   !!}
                                <div class="col-xs-8">
                                    {!! Form::text('mname',old('mname'),['class' => 'form-control','placeholder'=>'Введите отчество','maxlength'=>'70','id'=>'mname'])!!}
                                    {!! $errors->first('mname', '<p class="text-danger">:message</p>') !!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('position','Должность:',['class' => 'col-xs-3 control-label'])   !!}
                                <div class="col-xs-8">
                                    {!! Form::tel('position',old('position'),['class' => 'form-control','placeholder'=>'Укажите должность','size'=>'70','id'=>'position'])!!}
                                    {!! $errors->first('position', '<p class="text-danger">:message</p>') !!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('phone','Телефон:',['class' => 'col-xs-3 control-label'])   !!}
                                <div class="col-xs-8">
                                    {!! Form::tel('phone',old('phone'),['class' => 'form-control','placeholder'=>'Укажите телефон','size'=>'20','id'=>'phone'])!!}
                                    {!! $errors->first('phone', '<p class="text-danger">:message</p>') !!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('email','E-mail:',['class' => 'col-xs-3 control-label'])   !!}
                                <div class="col-xs-8">
                                    {!! Form::email('email',old('email'),['class' => 'form-control','placeholder'=>'Укажите e-mail','size'=>'50'])!!}
                                    {!! $errors->first('email', '<p class="text-danger">:message</p>') !!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('site','Сайт:',['class' => 'col-xs-3 control-label'])   !!}
                                <div class="col-xs-8">
                                    {!! Form::text('site',old('site'),['class' => 'form-control','placeholder'=>'Укажите адрес сайта','size'=>'70'])!!}
                                    {!! $errors->first('site', '<p class="text-danger">:message</p>') !!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('legal_address','Юридический адрес:',['class' => 'col-xs-3 control-label'])   !!}
                                <div class="col-xs-8">
                                    {!! Form::text('legal_address',old('legal_address'),['class' => 'form-control','placeholder'=>'Укажите юридический адрес контрагента','size'=>'254'])!!}
                                    {!! $errors->first('legal_address', '<p class="text-danger">:message</p>') !!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('post_address','Почтовый адрес:',['class' => 'col-xs-3 control-label'])   !!}
                                <div class="col-xs-8">
                                    {!! Form::text('post_address',old('post_address'),['class' => 'form-control','placeholder'=>'Укажите почтовый адрес контрагента','size'=>'254'])!!}
                                    {!! $errors->first('post_address', '<p class="text-danger">:message</p>') !!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('fact_address','Фактический адрес:',['class' => 'col-xs-3 control-label'])   !!}
                                <div class="col-xs-8">
                                    {!! Form::text('fact_address',old('fact_address'),['class' => 'form-control','placeholder'=>'Укажите фактический адрес контрагента','size'=>'254'])!!}
                                    {!! $errors->first('fact_address', '<p class="text-danger">:message</p>') !!}
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-xs-8">
                                    {!! Form::button('Сохранить', ['class' => 'btn btn-primary','type'=>'submit', 'id'=>'save_btn']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
    <!-- /page content -->
@endsection

@section('user_script')
    <script src="/js/jquery.maskedinput.min.js" type="text/javascript"></script>
    <script>
        $( document ).ready(function() {
            $('#country_id').parent().parent().hide();
            $('#tax_number').parent().parent().hide();
            // Снять checkbox
            $('#foreigner').prop('checked', false);
            $('#foreigner').parent().parent().hide();
            $("#country_id").prepend($('<option value="0">Выберите страну</option>'));
            $("#country_id :first").attr("selected", "selected");
            $("#country_id :first").attr("disabled", "disabled");
            $("#user_id option[value='{{Auth::user()->id}}']").attr("selected", "selected");
            $("#phone").mask("+7(999) 999-9999");
            $('#fill_btn').hide();
        });

        $("#ftype").change(function(){
            let txt = $("#ftype option:selected").text();
            if(txt.includes("за пределами РФ")){
                $('#country_id').parent().parent().show();
                $('#tax_number').parent().parent().show();
                // Снять checkbox
                $('#provider').prop('checked', false);
                $('#foreigner').prop('checked', true);
                $('#foreigner').parent().parent().show();
                $('#kpp').parent().parent().hide();
                $('#okpo').parent().parent().hide();
                $('#provider').parent().parent().hide();
            }
            else{
                $("#country_id").prepend($('<option value="0">Выберите страну</option>'));
                $("#country_id :first").attr("selected", "selected");
                $("#country_id :first").attr("disabled", "disabled");
                $('#country_id').parent().parent().hide();
                $('#tax_number').parent().parent().hide();
                // Снять checkbox
                $('#foreigner').prop('checked', false);
                $('#foreigner').parent().parent().hide();
                $('#kpp').parent().parent().show();
                $('#okpo').parent().parent().show();
                $('#provider').parent().parent().show();
            }
        });

        $('#save_btn').click(function () {
            let error = 0;
            $("#form_ref").find(":input").each(function () {// проверяем каждое поле ввода в форме
                if ($(this).attr("required") == 'required') { //обязательное для заполнения поле формы?
                    if (!$(this).val()) {// если поле пустое
                        $(this).css('border', '1px solid red');// устанавливаем рамку красного цвета
                        error = 1;// определяем индекс ошибки
                    } else {
                        $(this).css('border', '1px solid green');// устанавливаем рамку зеленого цвета
                    }

                }
            })
            if (error) {
                alert("Необходимо заполнять все доступные поля!");
                return false;
            }
        });

        $("#inn").keyup(function(){
            let inn = $('#inn').val();
            if(inn.length>9)
                $('#fill_btn').show();
            else
                $('#fill_btn').hide();
        });

        $('#fill_btn').click(function(e){
            e.preventDefault();
            let inn = $('#inn').val();
            $.ajax({
                type: 'POST',
                url: '{{ route('firmFill') }}',
                data: {'fns': 'find','inn':inn},
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    //alert(res);
                    if(res=='NO_API_KEY'){
                        alert('Токен не обнаружен!');
                    }
                    if(res=='NOT_METHOD'){
                        alert('Не известный метод запроса данных!');
                    }
                    else{
                        let obj = jQuery.parseJSON(res);
                        let state = obj.items[0].ЮЛ.Статус;
                        if(state!='Действующее')
                            alert('Внимание! Юрлицо не действительно!!!');
                        $('#kpp').val(obj.items[0].ЮЛ.КПП);
                        $('#name').val(obj.items[0].ЮЛ.НаимСокрЮЛ);
                        $('#title').val(obj.items[0].ЮЛ.НаимПолнЮЛ);
                        $('#legal_address').val(obj.items[0].ЮЛ.Адрес.Индекс+', '+obj.items[0].ЮЛ.Адрес.АдресПолн);
                        let fio = obj.items[0].ЮЛ.Руководитель.ФИОПолн;
                        let arrFio = fio.split(' ');
                        $('#lname').val(arrFio[0]);
                        $('#fname').val(arrFio[1]);
                        $('#mname').val(arrFio[2]);
                        $('#position').val(obj.items[0].ЮЛ.Руководитель.Должн);
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
