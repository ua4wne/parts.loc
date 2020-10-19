@extends('layouts.main')

@section('dashboard')

@endsection
@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li><a href="{{ route('orders') }}">{{ $title }}</a></li>
        <li class="active">{{ $head }}</li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- page content -->
    <div class="container-fluid container-fullw bg-white">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <h2 class="text-center">{{ $head }}</h2>
                {!! Form::open(['url' => route('orderAdd'),'class'=>'form-horizontal','method'=>'POST', 'id'=>'form_ref']) !!}
                <table class="table table-bordered table-hover">
                    <tr>
                        <th>Номер документа:</th>
                        <td>{!! Form::text('doc_num',$doc_num,['class' => 'form-control','placeholder'=>'Введите номер документа','maxlength'=>'15','required'=>'required'])!!}
                            {!! $errors->first('doc_num', '<p class="text-danger">:message</p>') !!}
                        </td>
                        <th>
                            Хоз. операция: <span class="symbol required" aria-required="true"></span>
                        </th>
                        <td>
                            {!! Form::select('hoperation_id',$hopsel, old('hoperation_id'), ['class' => 'form-control','required'=>'required','id'=>'hoperation']); !!}
                            {!! $errors->first('hoperation_id', '<p class="text-danger">:message</p>') !!}
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
                            Склад: <span class="symbol required" aria-required="true"></span>
                        </th>
                        <td>
                            {!! Form::select('warehouse_id',$wxsel, old('warehouse_id'), ['class' => 'form-control','required'=>'required']); !!}
                            {!! $errors->first('warehouse_id', '<p class="text-danger">:message</p>') !!}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Поставщик: <span class="symbol required" aria-required="true"></span>
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
                            Статус: <span class="symbol required" aria-required="true"></span>
                        </th>
                        <td>
                            {!! Form::select('statuse_id',$statsel, old('statuse_id'), ['class' => 'form-control','required'=>'required','id'=>'statuse_id']); !!}
                            {!! $errors->first('statuse_id', '<p class="text-danger">:message</p>') !!}
                        </td>
                        <th>Номер оплаченного документа:</th>
                        <td>{!! Form::text('doc_num_firm',old('doc_num_firm'),['class' => 'form-control','size'=>'15'])!!}
                    </tr>
                    <tr>
                        <th>
                            Менеджер: <span class="symbol required" aria-required="true"></span>
                        </th>
                        <td>
                            {!! Form::select('user_id',$usel, old('user_id'), ['class' => 'form-control','required'=>'required','id'=>'user_id']); !!}
                            {!! $errors->first('user_id', '<p class="text-danger">:message</p>') !!}
                        </td>

                        <th>Дата оплаченного документа:</th>
                        <td>{{ Form::date('date_firm', \Carbon\Carbon::create(),['class' => 'form-control']) }}</td>
                    </tr>
                    <tr>
                        <th>
                            Валюта: <span class="symbol required" aria-required="true"></span>
                        </th>
                        <td>{!! Form::select('currency_id',$cursel, old('currency_id'), ['class' => 'form-control','required'=>'required','id'=>'curr_id']); !!}
                            {!! $errors->first('currency', '<p class="text-danger">:message</p>') !!}
                            <div class="checkbox clip-check check-primary">
                                <input type="checkbox" name="has_vat" id="has_vat" checked value="1">
                                <label for="has_vat">
                                    Цена включает НДС
                                </label>
                            </div>
                        </td>
                        <th>Комментарий:</th>
                        <td>
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
        $("#user_id option[value='{{Auth::user()->id}}']").attr("selected", "selected");
        $("#hoperation :contains('Закупка у поставщика')").attr("selected", "selected");

        $('#search_firm').typeahead({
            hint: true,
            highlight: true,
            minLength: 3,
            ajax: {
                url: "{{ route('getFirm') }}",
                triggerLength: 1
            }
        });

        $( "#search_firm" ).blur(function() {
            $("#contract").empty(); //очищаем от старых значений
            var firm = $("#search_firm").val();
            var org_id = $("#org_id option:selected").val();
            $.ajax({
                type: 'POST',
                url: '{{ route('findContract') }}',
                data: {'firm': firm,'org_id':org_id},
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

        $('#curr_id').change(function(){
            let rub = $('#curr_id option:selected').text();
                if(rub.indexOf('рубль')){
                    $("#hoperation option:contains('Импорт')").prop("selected", true);
                    $('#has_vat').prop('checked', false);
                    $('#has_vat').val('0');
                }
                if(rub.indexOf('рубль')>0){
                    $('#has_vat').prop('checked', true);
                    $('#has_vat').val('1');
                    $('#hoperation option').prop('selected', false);
                    $('#hoperation option:contains("Закупка у поставщика")').prop("selected", true);
                }
        });
    </script>
@endsection
