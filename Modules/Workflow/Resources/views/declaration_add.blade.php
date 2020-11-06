@extends('layouts.main')

@section('dashboard')

@endsection
@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li><a href="{{ route('declarations') }}">{{ $title }}</a></li>
        <li class="active">{{ $head }}</li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- page content -->
    <div class="container-fluid container-fullw bg-white">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <h2 class="text-center">{{ $head }}</h2>
                {!! Form::open(['url' => route('declarationAdd'),'class'=>'form-horizontal','method'=>'POST', 'id'=>'form_ref']) !!}
                <table class="table table-bordered table-hover">
                    <tr>
                        <th>Номер документа: <span class="symbol required" aria-required="true"></span></th>
                        <td>{!! Form::text('doc_num',$doc_num,['class' => 'form-control','placeholder'=>'Введите номер документа','maxlength'=>'15','required'=>'required'])!!}
                            {!! $errors->first('doc_num', '<p class="text-danger">:message</p>') !!}
                        </td>
                        <th>
                            Номер декларации: <span class="symbol required" aria-required="true"></span>
                        </th>
                        <td>
                            {!! Form::text('declaration_num',old('declaration_num'),['class' => 'form-control','placeholder'=>'Введите номер декларации','maxlength'=>'30','required'=>'required','id'=>'declaration_num'])!!}
                            {!! $errors->first('firm_id', '<p class="text-danger">:message</p>') !!}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Организация: <span class="symbol required" aria-required="true"></span>
                        </th>
                        <td>
                            {!! Form::select('organisation_id',$orgsel, old('organisation_id'), ['class' => 'form-control','required'=>'required','id'=>'org_id']); !!}
                            {!! $errors->first('organisation_id', '<p class="text-danger">:message</p>') !!}
                        </td>
                        <th>
                            Поставщик: <span class="symbol required" aria-required="true"></span>
                        </th>
                        <td>
                            {!! Form::text('firm_id',old('firm_id'),['class' => 'form-control','placeholder'=>'Начинайте вводить наименование поставщика','required'=>'required','id'=>'search_firm'])!!}
                            {!! $errors->first('firm_id', '<p class="text-danger">:message</p>') !!}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            ГТД оформляется: <span class="symbol required" aria-required="true"></span>
                        </th>
                        <td>
                            {!! Form::select('who_register',['broker'=>'Таможенным брокером','yourself'=>'Самостоятельно'], old('who_register'), ['class' => 'form-control','required'=>'required']); !!}
                            {!! $errors->first('who_register', '<p class="text-danger">:message</p>') !!}
                        </td>
                        <th>
                            Брокер/Таможня: <span class="symbol required" aria-required="true"></span>
                        </th>
                        <td>
                            {!! Form::text('broker_id', '', ['class' => 'form-control','placeholder'=>'Начинайте вводить наименование брокера','required'=>'required','id'=>'broker_search']); !!}
                            {!! $errors->first('broker_id', '<p class="text-danger">:message</p>') !!}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Валюта: <span class="symbol required" aria-required="true"></span>
                        </th>
                        <td>{!! Form::select('currency_id',$cursel, old('currency_id'), ['class' => 'form-control','required'=>'required','id'=>'curr_id']); !!}
                            {!! $errors->first('currency', '<p class="text-danger">:message</p>') !!}
                        </td>
                        <th>
                            Договор:
                        </th>
                        <td>
                            {!! Form::select('contract_id',[], old('contract_id'), ['class' => 'form-control','id'=>'contract']); !!}
                            {!! $errors->first('contract_id', '<p class="text-danger">:message</p>') !!}
                        </td>

                    </tr>
                    <tr>
                        <th>Таможенный сбор: <span class="symbol required" aria-required="true"></span></th>
                        <td>
                            {!! Form::text('tax',old('tax'),['class'=>'form-control', 'required' => 'required','maxlength'=>'10','placeholder'=>'Укажите сумму','id'=>'tax']) !!}
                            {!! $errors->first('tax', '<p class="text-danger">:message</p>') !!}
                        </td>
                        <th>Таможенный штраф:</th>
                        <td>
                            {!! Form::text('fine',old('fine'),['class'=>'form-control','maxlength'=>'10','placeholder'=>'Укажите сумму','id'=>'fine']) !!}
                            {!! $errors->first('fine', '<p class="text-danger">:message</p>') !!}
                        </td>

                    </tr>
                    <tr>
                        <th>
                            Статья расходов: <span class="symbol required" aria-required="true"></span>
                        </th>
                        <td>
                            {!! Form::select('expense_id',$expsel, old('expense_id'), ['class' => 'form-control','required'=>'required','id'=>'expense_id']); !!}
                            {!! $errors->first('expense_id', '<p class="text-danger">:message</p>') !!}
                        </td>
                        <th>
                            Страна происхождения: <span class="symbol required" aria-required="true"></span>
                        </th>
                        <td>
                            {!! Form::select('country_id',$contsel, old('country_id'), ['class' => 'form-control','required'=>'required','id'=>'country_id']); !!}
                            {!! $errors->first('country_id', '<p class="text-danger">:message</p>') !!}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Таможенная стоимость: <span class="symbol required" aria-required="true"></span>
                        </th>
                        <td>
                            {!! Form::text('cost',old('cost'), ['class' => 'form-control','required'=>'required','maxlength'=>'10','id'=>'cost']); !!}
                            {!! $errors->first('cost', '<p class="text-danger">:message</p>') !!}
                        </td>
                        <th>
                            Менеджер: <span class="symbol required" aria-required="true"></span>
                        </th>
                        <td>
                            {!! Form::select('user_id',$usel, old('user_id'), ['class' => 'form-control','required'=>'required','id'=>'user_id']); !!}
                            {!! $errors->first('user_id', '<p class="text-danger">:message</p>') !!}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Ставка пошлины: <span class="symbol required" aria-required="true"></span>
                        </th>
                        <td>
                            {!! Form::text('rate',old('rate'), ['class' => 'form-control','required'=>'required','maxlength'=>'10','id'=>'rate']); !!}
                            {!! $errors->first('rate', '<p class="text-danger">:message</p>') !!}
                        </td>
                        <th>
                            Сумма пошлины:
                        </th>
                        <td>
                            {!! Form::text('amount','', ['class' => 'form-control','required'=>'required','id'=>'tax_amount']); !!}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Ставка НДС:
                        </th>
                        <td>
                            {!! Form::text('vat','', ['class' => 'form-control','id'=>'vat','required'=>'required','maxlength'=>'3']); !!}
                        </td>
                        <th>
                            Сумма НДС:
                        </th>
                        <td>
                            {!! Form::text('vat_amount','', ['class' => 'form-control','required'=>'required' ,'id'=>'vat_amount']); !!}
                        </td>
                    </tr>
                    <tr>
                        <th>Комментарий:</th>
                        <td colspan="3">
                            {!! Form::textarea('comment',null,['class'=>'form-control', 'rows' => 2, 'cols' => 50]) !!}
                        </td>
                    </tr>
                </table>
                <div class="form-group">
                    <div class="col-xs-8">
                        {!! Form::button('Сохранить', ['class' => 'btn btn-primary','type'=>'submit', 'id'=>'save_btn']) !!}
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    <!-- /page content -->
@endsection

@section('user_script')
    <script src="/js/bootstrap-typeahead.min.js"></script>
    <script>
        $("#org_id").prepend($('<option value="0">Выберите организацию</option>'));
        $("#org_id :first").attr("selected", "selected");
        $("#org_id :first").attr("disabled", "disabled");

        $("#expense_id").prepend($('<option value="0">Выберите статью затрат</option>'));
        $("#expense_id :first").attr("selected", "selected");
        $("#expense_id :first").attr("disabled", "disabled");

        $("#country_id").prepend($('<option value="0">Выберите страну происхождения</option>'));
        $("#country_id :first").attr("selected", "selected");
        $("#country_id :first").attr("disabled", "disabled");

        $("#user_id option[value='{{Auth::user()->id}}']").attr("selected", "selected");

        $('#search_firm').typeahead({
            hint: true,
            highlight: true,
            minLength: 3,
            ajax: {
                url: "{{ route('getFirm') }}",
                triggerLength: 1
            }
        });

        $('#broker_search').typeahead({
            hint: true,
            highlight: true,
            minLength: 3,
            ajax: {
                url: "{{ route('getFirm') }}",
                triggerLength: 1
            }
        });

        $("#broker_search").blur(function () {
            $("#contract").empty(); //очищаем от старых значений
            var firm = $("#search_firm").val();
            var org_id = $("#org_id option:selected").val();
            $.ajax({
                type: 'POST',
                url: '{{ route('findContract') }}',
                data: {'firm': firm, 'org_id': org_id},
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    //alert(res);
                    $("#contract").prepend($(res));
                }
            });
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

    </script>
@endsection
