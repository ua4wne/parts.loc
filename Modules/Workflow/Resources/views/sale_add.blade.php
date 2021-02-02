@extends('layouts.main')

@section('dashboard')

@endsection
@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li><a href="{{ route('sale_orders') }}">{{ $title }}</a></li>
        <li class="active">{{ $head }}</li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- page content -->
    <div class="container-fluid container-fullw bg-white">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <h2 class="text-center">{{ $head }}</h2>
                {!! Form::open(['url' => route('saleAdd'),'class'=>'form-horizontal','method'=>'POST', 'id'=>'form_ref']) !!}
                <table class="table table-bordered table-hover">
                    <tr>
                        <th>Номер документа:</th>
                        <td>{!! Form::text('doc_num',$doc_num,['class' => 'form-control','placeholder'=>'Введите номер документа','maxlength'=>'15','required'=>'required'])!!}
                            {!! $errors->first('doc_num', '<p class="text-danger">:message</p>') !!}
                        </td>
                        <th>
                            Склад: <span class="symbol required" aria-required="true"></span>
                        </th>
                        <td>
                            {!! Form::select('warehouse_id',$wxsel, old('warehouse_id'), ['class' => 'form-control','required'=>'required','id'=>'whs_id']); !!}
                            {!! $errors->first('warehouse_id', '<p class="text-danger">:message</p>') !!}
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
                            Соглашение: <span class="symbol required" aria-required="true"></span>
                        </th>
                        <td>
                            {!! Form::select('agreement_id',$agrsel, old('agreement_id'), ['class' => 'form-control','required'=>'required','id'=>'agreement_id']); !!}
                            {!! $errors->first('agreement_id', '<p class="text-danger">:message</p>') !!}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Клиент: <span class="symbol required" aria-required="true"></span>
                        </th>
                        <td>
                            {!! Form::text('firm_id',old('firm_id'),['class' => 'form-control','placeholder'=>'Начинайте вводить наименование поставщика','required'=>'required','id'=>'search_firm'])!!}
                            {!! $errors->first('firm_id', '<p class="text-danger">:message</p>') !!}
                        </td>
                        <th>
                            Договор: <span class="symbol required" aria-required="true"></span>
                        </th>
                        <td>
                            {!! Form::select('contract_id',[], old('contract_id'), ['class' => 'form-control','required'=>'required','id'=>'contract']); !!}
                            {!! $errors->first('contract_id', '<p class="text-danger">:message</p>') !!}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Способ доставки: <span class="symbol required" aria-required="true"></span>
                        </th>
                        <td>
                            {!! Form::select('delivery_method_id',$dmethods, old('delivery_method_id'), ['class' => 'form-control','required'=>'required','id'=>'method_id']); !!}
                            {!! $errors->first('delivery_method_id', '<p class="text-danger">:message</p>') !!}
                        </td>
                        <th>Кто доставляет: <span class="symbol required" aria-required="true"></span></th>
                        <td>
                            {!! Form::select('delivery_id',$delivs, old('delivery_id'), ['class' => 'form-control','required'=>'required','id'=>'delivery_id']); !!}
                            {!! $errors->first('delivery_id', '<p class="text-danger">:message</p>') !!}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Пункт назначения: <span class="symbol required" aria-required="true"></span>
                        </th>
                        <td>
                            {!! Form::text('destination',old('destination'),['class' => 'form-control','placeholder'=>'Введите адрес пункта назначения','required'=>'required','maxlength'=>'150'])!!}
                            {!! $errors->first('destination', '<p class="text-danger">:message</p>') !!}
                        </td>
                        <th>Контактное лицо:</th>
                        <td>
                            {!! Form::text('contact',old('contact'),['class' => 'form-control','placeholder'=>'Укажите контактное лицо','maxlength'=>'100'])!!}
                            {!! $errors->first('contact', '<p class="text-danger">:message</p>') !!}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Менеджер: <span class="symbol required" aria-required="true"></span>
                        </th>
                        <td>
                            {!! Form::select('user_id',$usel, old('user_id'), ['class' => 'form-control','required'=>'required','id'=>'user_id']); !!}
                            {!! $errors->first('user_id', '<p class="text-danger">:message</p>') !!}
                        </td>

                        <th>Дата согласования:</th>
                        <td>{{ Form::date('date_agreement', \Carbon\Carbon::create(),['class' => 'form-control']) }}</td>
                    </tr>
                    <tr>
                        <th>
                            № заказа по данным клиента:
                        </th>
                        <td>
                            {!! Form::text('doc_num_firm',old('doc_num_firm'),['class' => 'form-control','maxlength'=>'15'])!!}
                            {!! $errors->first('doc_num_firm', '<p class="text-danger">:message</p>') !!}
                        </td>
                        <th>
                            от:
                        </th>
                        <td>
                            {{ Form::date('date_firm', \Carbon\Carbon::create(),['class' => 'form-control']) }}
                        </td>
                    </tr>
                    <tr>
                        <th>Комментарий:</th>
                        <td>
                            {!! Form::textarea('comment',null,['class'=>'form-control', 'rows' => 2, 'cols' => 50]) !!}
                        </td>
                        <th>
                            Валюта: <span class="symbol required" aria-required="true"></span>
                        </th>
                        <td>{!! Form::select('currency_id',$cursel, old('currency_id'), ['class' => 'form-control','required'=>'required','id'=>'curr_id']); !!}
                            {!! $errors->first('currency', '<p class="text-danger">:message</p>') !!}
                            <div class="checkbox clip-check check-primary">
                                <input type="checkbox" name="has_vat" id="has_vat" value="1">
                                <label for="has_vat">
                                    Цена включает НДС
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>Дополнительные условия:</th>
                        <td colspan="3">
                            <div class="checkbox clip-check check-primary col-xs-3">
                                <input type="checkbox" name="to_door" id="to_door" value="1">
                                <label for="to_door">
                                    Доставка до двери
                                </label>
                            </div>
                            <div class="checkbox clip-check check-primary col-xs-3">
                                <input type="checkbox" name="delivery_in_price" id="delivery_in_price" value="1">
                                <label for="delivery_in_price">
                                    Доставка включена в стоимость
                                </label>
                            </div>
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
        $("#whs_id").prepend($('<option value="0">Выберите склад</option>'));
        $("#whs_id :first").attr("selected", "selected");
        $("#whs_id :first").attr("disabled", "disabled");
        $("#user_id option[value='{{Auth::user()->id}}']").attr("selected", "selected");
        $("#agreement_id").prepend($('<option value="0">Выберите соглашение об условии продаж</option>'));
        $("#agreement_id :first").attr("selected", "selected");
        $("#agreement_id :first").attr("disabled", "disabled");
        $("#method_id").prepend($('<option value="0">Выберите способ доставки</option>'));
        $("#method_id :first").attr("selected", "selected");
        $("#method_id :first").attr("disabled", "disabled");
        $("#delivery_id").prepend($('<option value="0">Выберите компанию - перевозчика</option>'));
        $("#delivery_id :first").attr("selected", "selected");
        $("#delivery_id :first").attr("disabled", "disabled");
        $("#curr_id").prepend($('<option value="0">Выберите валюту</option>'));
        $("#curr_id :first").attr("selected", "selected");
        $("#curr_id :first").attr("disabled", "disabled");

        $('#search_firm').typeahead({
            hint: true,
            highlight: true,
            minLength: 3,
            ajax: {
                url: "{{ route('getFirm') }}",
                triggerLength: 1
            }
        });

        $("#search_firm").blur(function () {
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

        $('#curr_id').change(function () {
            let rub = $('#curr_id option:selected').text();
            if (rub.indexOf('рубль')) {
                $('#has_vat').prop('checked', false);
                $('#has_vat').val('0');
            }
            if (rub.indexOf('рубль') > 0) {
                $('#has_vat').prop('checked', true);
                $('#has_vat').val('1');
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

    </script>
@endsection
