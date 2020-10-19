@extends('layouts.main')
@section('user_css')

@endsection
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
    @if (session('error'))
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="alert alert-danger panel-remove">
                    <a href="#" class="close" data-dismiss="alert">&times;</a>
                    {{ session('error') }}
                </div>
            </div>
        </div>
    @endif
    @if (session('status'))
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="alert alert-success panel-remove">
                    <a href="#" class="close" data-dismiss="alert">&times;</a>
                    {{ session('status') }}
                </div>
            </div>
        </div>
    @endif
    <!-- page content -->
    <div class="container-fluid container-fullw bg-white">
        <div class="row">
            <h2 class="text-center">{{ $head }}</h2>
            <div class="panel-heading">
                <nav class="links cl-effect-5">
                    <a href="{{ route('firmView',['id'=>$firm->id]) }}"><span data-hover="Основное">Основное</span></a>
                    <a href="{{ route('bank_accounts',['id'=>$firm->id]) }}"><span data-hover="Банковские_счета">Банковские счета</span></a>
                    <a href="{{ route('contracts',['id'=>$firm->id]) }}"><span data-hover="Договоры">Договоры</span></a>
                    <a href="#"><span data-hover="Документы">Документы</span></a>
                </nav>
            </div>
            <div class="col-md-12">
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
                            {!! Form::open(['url' => route('firmEdit',['id'=>$firm->id]),'class'=>'form-horizontal','method'=>'POST', 'id'=>'form_ref']) !!}
                            <table class="table table-bordered table-hover">
                                <tr>
                                    <th>Вид контрагента:</th>
                                    <td>{!! Form::select('firm_type_id',$ftsel, $firm->firm_type_id, ['class' => 'form-control','required'=>'required','id'=>'ftype']); !!}</td>
                                </tr>
                                <tr>
                                    <th>Код:</th>
                                    <td>{!! Form::text('code',$firm->code,['class' => 'form-control','placeholder'=>'Введите код','maxlength'=>'12','required'=>'required'])!!}
                                        {!! $errors->first('code', '<p class="text-danger">:message</p>') !!}</td>
                                </tr>
                                <tr>
                                    <th>ИНН:</th>
                                    <td>{!! Form::text('inn',$firm->inn,['class' => 'form-control','placeholder'=>'Укажите ИНН','maxlength'=>'12','required'=>'required','id'=>'inn'])!!}
                                        {!! $errors->first('inn', '<p class="text-danger">:message</p>') !!}</td>
                                </tr>

                                <tr>
                                    <th>КПП:</th>
                                    <td>{!! Form::text('kpp',$firm->kpp,['class' => 'form-control','placeholder'=>'Укажите КПП','maxlength'=>'9','id'=>'kpp'])!!}
                                        {!! $errors->first('kpp', '<p class="text-danger">:message</p>') !!}</td>
                                </tr>
                                <tr>
                                    <th>ОКПО:</th>
                                    <td>{!! Form::text('okpo',$firm->okpo,['class' => 'form-control','placeholder'=>'Укажите код ОКПО','maxlength'=>'10','id'=>'okpo'])!!}
                                        {!! $errors->first('okpo', '<p class="text-danger">:message</p>') !!}</td>
                                </tr>

                                <tr>
                                    <th>Страна регистрации:</th>
                                    <td>{!! Form::select('consel',$consel, $country, ['class' => 'form-control','id'=>'country_id']); !!}</td>
                                </tr>
                                <tr>
                                    <th>Налоговый номер:</th>
                                    <td>{!! Form::text('tax_number',$firm->tax_number,['class' => 'form-control','placeholder'=>'Укажите налоговый номер','maxlength'=>'30','id'=>'tax_number'])!!}
                                        {!! $errors->first('tax_number', '<p class="text-danger">:message</p>') !!}</td>
                                </tr>

                                <tr>
                                    <th>Полное наименование:</th>
                                    <td>{!! Form::text('title',$firm->title,['class' => 'form-control','placeholder'=>'Введите полное наименование','maxlength'=>'254','required'=>'required','id'=>'title'])!!}
                                        {!! $errors->first('title', '<p class="text-danger">:message</p>') !!}</td>
                                </tr>
                                <tr>
                                    <th>Рабочее наименование:</th>
                                    <td>{!! Form::text('name',$firm->name,['class' => 'form-control','placeholder'=>'Введите рабочее наименование','maxlength'=>'150','id'=>'name'])!!}
                                        {!! $errors->first('name', '<p class="text-danger">:message</p>') !!}</td>
                                </tr>
                                <tr>
                                    <th>Основной менеджер:</th>
                                    <td>{!! Form::select('user_id',$usel, $firm->user_id, ['class' => 'form-control','required'=>'required','id'=>'user_id']); !!}</td>
                                </tr>
                                <tr>
                                    <th>Отношения с контрагентом:</th>
                                    <td>
                                        <div class="col-md-3">
                                            <div class="checkbox clip-check check-primary">
                                                @if($firm->client)
                                                    <input type="checkbox" name="client" id="client" value="1" checked>
                                                @else
                                                    <input type="checkbox" name="client" id="client" value="1">
                                                @endif
                                                <label for="client">
                                                    Клиент
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="checkbox clip-check check-primary">
                                                @if($firm->provider)
                                                    <input type="checkbox" name="provider" id="provider" value="1"
                                                           checked>
                                                @else
                                                    <input type="checkbox" name="provider" id="provider" value="1">
                                                @endif
                                                <label for="provider">
                                                    Поставщик
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="checkbox clip-check check-primary">
                                                @if($firm->foreigner)
                                                    <input type="checkbox" name="foreigner" id="foreigner" value="1"
                                                           checked>
                                                @else
                                                    <input type="checkbox" name="foreigner" id="foreigner"
                                                           value="1">
                                                @endif
                                                <label for="foreigner">
                                                    Иностранный поставщик
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="checkbox clip-check check-primary">
                                                @if($firm->other)
                                                    <input type="checkbox" name="other" id="other" value="1" checked>
                                                @else
                                                    <input type="checkbox" name="other" id="other" value="1">
                                                @endif
                                                <label for="other">
                                                    Другое
                                                </label>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        {!! Form::button('Обновить', ['class' => 'btn btn-info fill_btn','type'=>'button']) !!}
                                        {!! Form::button('Сохранить', ['class' => 'btn btn-primary','type'=>'submit', 'id'=>'save_btn']) !!}
                                    </td>
                                </tr>
                            </table>
                            {!! Form::close() !!}
                        </div>
                        <div class="tab-pane fade" id="contacts">
                            {!! Form::open(['url' => '#','class'=>'form-horizontal','method'=>'POST', 'id'=>'form_ref_cont']) !!}
                            {!! Form::hidden('firm_id',$firm->id,['class' => 'form-control','required'=>'required','id'=>'firm_id']) !!}
                            <table class="table table-bordered table-hover">
                                @if(!count($contact))
                                    <tr>
                                        <th>Фамилия контактного лица:</th>
                                        <td>{!! Form::text('lname',old('lname'),['class' => 'form-control','placeholder'=>'Введите фамилию','maxlength'=>'70','required'=>'required','id'=>'lname'])!!}
                                            {!! $errors->first('lname', '<p class="text-danger">:message</p>') !!}</td>
                                    </tr>
                                    <tr>
                                        <th>Имя контактного лица:</th>
                                        <td>{!! Form::text('fname',old('fname'),['class' => 'form-control','placeholder'=>'Введите имя','maxlength'=>'70','required'=>'required','id'=>'fname'])!!}
                                            {!! $errors->first('fname', '<p class="text-danger">:message</p>') !!}</td>
                                    </tr>
                                    <tr>
                                        <th>Отчество контактного лица:</th>
                                        <td>{!! Form::text('mname',old('mname'),['class' => 'form-control','placeholder'=>'Введите отчество','maxlength'=>'70','id'=>'mname'])!!}
                                            {!! $errors->first('mname', '<p class="text-danger">:message</p>') !!}</td>
                                    </tr>
                                    <tr>
                                        <th>Должность:</th>
                                        <td>{!! Form::tel('position',old('position'),['class' => 'form-control','placeholder'=>'Укажите должность','size'=>'70','id'=>'position'])!!}
                                            {!! $errors->first('position', '<p class="text-danger">:message</p>') !!}</td>
                                    </tr>
                                    <tr>
                                        <th>Телефон:</th>
                                        <td>{!! Form::tel('phone',old('phone'),['class' => 'form-control','placeholder'=>'Укажите телефон','size'=>'20','id'=>'phone'])!!}
                                            {!! $errors->first('phone', '<p class="text-danger">:message</p>') !!}</td>
                                    </tr>
                                    <tr>
                                        <th>E-mail:</th>
                                        <td>{!! Form::email('email',old('email'),['class' => 'form-control','placeholder'=>'Укажите e-mail','size'=>'50'])!!}
                                            {!! $errors->first('email', '<p class="text-danger">:message</p>') !!}</td>
                                    </tr>
                                    <tr>
                                        <th>Сайт:</th>
                                        <td>{!! Form::text('site',old('site'),['class' => 'form-control','placeholder'=>'Укажите адрес сайта','size'=>'70'])!!}
                                            {!! $errors->first('site', '<p class="text-danger">:message</p>') !!}</td>
                                    </tr>
                                    <tr>
                                        <th>Юридический адрес:</th>
                                        <td>{!! Form::text('legal_address',old('legal_address'),['class' => 'form-control','placeholder'=>'Укажите юридический адрес контрагента','size'=>'254','id'=>'legal_address'])!!}
                                            {!! $errors->first('legal_address', '<p class="text-danger">:message</p>') !!}</td>
                                    </tr>
                                    <tr>
                                        <th>Почтовый адрес:</th>
                                        <td>{!! Form::text('post_address',old('post_address'),['class' => 'form-control','placeholder'=>'Укажите почтовый адрес контрагента','size'=>'254'])!!}
                                            {!! $errors->first('post_address', '<p class="text-danger">:message</p>') !!}</td>
                                    </tr>
                                    <tr>
                                        <th>Фактический адрес:</th>
                                        <td>{!! Form::text('fact_address',old('fact_address'),['class' => 'form-control','placeholder'=>'Укажите фактический адрес контрагента','size'=>'254'])!!}
                                            {!! $errors->first('fact_address', '<p class="text-danger">:message</p>') !!}</td>
                                    </tr>
                                @else
                                    <tr>
                                        <th>Фамилия контактного лица:</th>
                                        <td>{!! Form::text('lname',$contact[0]->lname,['class' => 'form-control','placeholder'=>'Введите фамилию','maxlength'=>'70','required'=>'required','id'=>'lname'])!!}
                                            {!! $errors->first('lname', '<p class="text-danger">:message</p>') !!}</td>
                                    </tr>
                                    <tr>
                                        <th>Имя контактного лица:</th>
                                        <td>{!! Form::text('fname',$contact[0]->fname,['class' => 'form-control','placeholder'=>'Введите имя','maxlength'=>'70','required'=>'required','id'=>'fname'])!!}
                                            {!! $errors->first('fname', '<p class="text-danger">:message</p>') !!}</td>
                                    </tr>
                                    <tr>
                                        <th>Отчество контактного лица:</th>
                                        <td>{!! Form::text('mname',$contact[0]->mname,['class' => 'form-control','placeholder'=>'Введите отчество','maxlength'=>'70','id'=>'mname'])!!}
                                            {!! $errors->first('mname', '<p class="text-danger">:message</p>') !!}</td>
                                    </tr>
                                    <tr>
                                        <th>Должность:</th>
                                        <td>{!! Form::tel('position',$contact[0]->position,['class' => 'form-control','placeholder'=>'Укажите должность','size'=>'70','id'=>'position'])!!}
                                            {!! $errors->first('position', '<p class="text-danger">:message</p>') !!}</td>
                                    </tr>
                                    <tr>
                                        <th>Телефон:</th>
                                        <td>{!! Form::tel('phone',$contact[0]->phone,['class' => 'form-control','placeholder'=>'Укажите телефон','size'=>'20','id'=>'phone'])!!}
                                            {!! $errors->first('phone', '<p class="text-danger">:message</p>') !!}</td>
                                    </tr>
                                    <tr>
                                        <th>E-mail:</th>
                                        <td>{!! Form::email('email',$contact[0]->email,['class' => 'form-control','placeholder'=>'Укажите e-mail','size'=>'50'])!!}
                                            {!! $errors->first('email', '<p class="text-danger">:message</p>') !!}</td>
                                    </tr>
                                    <tr>
                                        <th>Сайт:</th>
                                        <td>{!! Form::text('site',$contact[0]->site,['class' => 'form-control','placeholder'=>'Укажите адрес сайта','size'=>'70'])!!}
                                            {!! $errors->first('site', '<p class="text-danger">:message</p>') !!}</td>
                                    </tr>
                                    <tr>
                                        <th>Юридический адрес:</th>
                                        <td>{!! Form::text('legal_address',$contact[0]->legal_address,['class' => 'form-control','placeholder'=>'Укажите юридический адрес контрагента','size'=>'254','id'=>'legal_address'])!!}
                                            {!! $errors->first('legal_address', '<p class="text-danger">:message</p>') !!}</td>
                                    </tr>
                                    <tr>
                                        <th>Почтовый адрес:</th>
                                        <td>{!! Form::text('post_address',$contact[0]->post_address,['class' => 'form-control','placeholder'=>'Укажите почтовый адрес контрагента','size'=>'254'])!!}
                                            {!! $errors->first('post_address', '<p class="text-danger">:message</p>') !!}</td>
                                    </tr>
                                    <tr>
                                        <th>Фактический адрес:</th>
                                        <td>{!! Form::text('fact_address',$contact[0]->fact_address,['class' => 'form-control','placeholder'=>'Укажите фактический адрес контрагента','size'=>'254'])!!}
                                            {!! $errors->first('fact_address', '<p class="text-danger">:message</p>') !!}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td colspan="2">
                                        {!! Form::button('Обновить', ['class' => 'btn btn-info fill_btn','type'=>'button']) !!}
                                        {!! Form::button('Сохранить', ['class' => 'btn btn-primary','type'=>'submit', 'id'=>'save_cont_btn']) !!}
                                    </td>
                                </tr>
                            </table>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /page content -->
@endsection

@section('user_script')
    <script src="/js/jquery.maskedinput.min.js" type="text/javascript"></script>
    <script>
        $(document).ready(function () {
            $("#ftype").change();
            $("#phone").mask("+7(999) 999-9999");
        });

        $("#ftype").change(function () {
            let txt = $("#ftype option:selected").text();
            if (txt.includes("за пределами РФ")) {
                $('#country_id').parent().parent().show();
                $('#tax_number').parent().parent().show();
                // Снять checkbox
                $('#provider').prop('checked', false);
                $('#foreigner').prop('checked', true);
                $('#foreigner').parent().parent().show();
                $('#kpp').parent().parent().hide();
                $('#okpo').parent().parent().hide();
                $('#provider').parent().parent().hide();
            } else {
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

        $('.fill_btn').click(function(e){
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

        $('#save_cont_btn').click(function (e) {
            e.preventDefault();
            let error = 0;
            $("#form_ref_cont").find(":input").each(function () {// проверяем каждое поле ввода в форме
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
            } else {
                $.ajax({
                    type: 'POST',
                    url: '{{ route('firmContactEdit') }}',
                    data: $('#form_ref_cont').serialize(),
                    success: function (res) {
                        //alert(res);
                        if (res == 'NOT')
                            alert('Выполнение операции запрещено!');
                        else if (res == 'NO VALIDATE')
                            alert('Ошибки валидации данных формы!');
                        else if (res == 'ERR')
                            alert('При обновлении данных возникла ошибка!');
                        if (res == 'OK')
                            alert('Контактные данные обновлены!');
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        alert(xhr.status + '\n' + thrownError);
                    }
                });
            }
        });
    </script>
@endsection
