@extends('layouts.main')
@section('user_css')
    <link href="/css/select2.min.css" rel="stylesheet">
@endsection
@section('dashboard')

@endsection
@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li class="active"><a href="{{ route('sales') }}">{{ $title }}</a></li>
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
            <div class="col-md-5 panel panel-white">
                <div class="row">
                    <fieldset>
                        <legend>Поиск номенклатуры</legend>
                        <table class="table table-condensed">
                            <tr>
                                <td>
                                    {!! Form::select('search',['name'=>'Наименование','vendor'=>'Артикул','analog'=>'Аналог',
                                    'catalog'=>'Каталожный №',], old('search'), ['class' => 'form-control','required'=>'required','id'=>'filter']); !!}
                                </td>
                                <th>содержит:</th>
                                <td>
                                    {!! Form::text('name',old('name'),['class' => 'form-control master','placeholder'=>'Начинайте ввод для поиска',
                                        'required'=>'required','id'=>'by_name'])!!}
                                    {!! Form::text('vendor',old('vendor'),['class' => 'form-control master','placeholder'=>'Начинайте ввод для поиска',
                                        'required'=>'required','id'=>'by_vendor'])!!}
                                    {!! Form::text('analog',old('analog'),['class' => 'form-control master','placeholder'=>'Начинайте ввод для поиска',
                                        'required'=>'required','id'=>'by_analog'])!!}
                                    {!! Form::text('catalog',old('catalog'),['class' => 'form-control master','placeholder'=>'Начинайте ввод для поиска',
                                        'required'=>'required','id'=>'by_catalog'])!!}
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </div>
                <div class="row">
                    <div class="table-responsive">
                        <table class="table table-condensed table-bordered">
                            <thead>
                            <tr>
                                <th>Код</th>
                                <th>Артикул</th>
                                <th>Наименование</th>
                                <th>Группа</th>
                            </tr>
                            </thead>
                            <tbody id="t_body">
                            @if($rows)
                                @foreach($rows as $k => $row)
                                    <tr id="{{ $row->id }}" class="clicable good_pos">
                                        <td>{{ $row->code }}</td>
                                        <td>{{ $row->vendor_code }}</td>
                                        <td>{{ $row->title }}</td>
                                        <td>{{ $row->category->category }}</td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-7 panel panel-white">
                <div class="row">
                    <fieldset>
                        <legend>Покупатель</legend>
                        {!! Form::select('firm_id',$firmsel, old('firm_id'), ['class' => 'form-control select2','required'=>'required','id'=>'firm_id']); !!}
                    </fieldset>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="tabbable pills">
                            <ul id="myTab3" class="nav nav-pills">
                                <li>
                                    <a href="#common" data-toggle="tab">
                                        Параметры
                                    </a>
                                </li>
                                <li>
                                    <a href="#stock" data-toggle="tab">
                                        Остатки, цены
                                    </a>
                                </li>
                                <li class="active">
                                    <a href="#sales" data-toggle="tab">
                                        Цены в продажах
                                    </a>
                                </li>
                                <li>
                                    <a href="#purchases" data-toggle="tab">
                                        Цены в закупках
                                    </a>
                                </li>
                                <li>
                                    <a href="#offers" data-toggle="tab">
                                        Цены в предложениях
                                    </a>
                                </li>
                                <li>
                                    <a href="#requests" data-toggle="tab">
                                        Цены в запросах
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade" id="common">
                                    <div class="row">
                                        <div class="table-responsive">
                                            <table class="table table-condensed table-bordered">
                                                <thead>
                                                <tr>
                                                    <th>Характеристика</th>
                                                    <th>Значение</th>
                                                </tr>
                                                </thead>
                                                <tbody id="common_body">

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="stock">
                                    <div class="row">
                                        <div class="table-responsive">
                                            <table class="table table-condensed table-bordered">
                                                <thead>
                                                <tr>
                                                    <th>Склад</th>
                                                    <th>Номенклатура</th>
                                                    <th>Ячейка</th>
                                                    <th>Кол-во</th>
                                                    <th>Ед. изм</th>
                                                    <th>Стоимость</th>
                                                    <th>Партия</th>
                                                </tr>
                                                </thead>
                                                <tbody id="stock_body">

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade  in active" id="sales">
                                    <div class="row">
                                        <div class="table-responsive">
                                            <table class="table table-condensed table-bordered">
                                                <thead>
                                                <tr>
                                                    <th>Цена</th>
                                                    <th>Цена в валюте</th>
                                                    <th>Курс</th>
                                                    <th>Валюта</th>
                                                    <th>Дата</th>
                                                    <th>Кол-во</th>
                                                    <th>Заказы клиентов</th>
                                                    <th>Контрагент</th>
                                                    <th>Ответственный</th>
                                                </tr>
                                                </thead>
                                                <tbody id="sales_body">

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="purchases">
                                    <div class="row">
                                        <div class="table-responsive">
                                            <table class="table table-condensed table-bordered">
                                                <thead>
                                                <tr>
                                                    <th>Цена</th>
                                                    <th>Цена в валюте</th>
                                                    <th>Курс</th>
                                                    <th>Валюта</th>
                                                    <th>Дата</th>
                                                    <th>Кол-во</th>
                                                    <th>Заказы поставщику</th>
                                                    <th>Контрагент</th>
                                                    <th>Ответственный</th>
                                                </tr>
                                                </thead>
                                                <tbody id="purchases_body">

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="offers">
                                    <div class="row">
                                        <div class="table-responsive">
                                            <table class="table table-condensed table-bordered">
                                                <thead>
                                                <tr>
                                                    <th>Цена</th>
                                                    <th>Цена в валюте</th>
                                                    <th>Курс</th>
                                                    <th>Валюта</th>
                                                    <th>Дата</th>
                                                    <th>Кол-во</th>
                                                    <th>Заказы поставщику</th>
                                                    <th>Контрагент</th>
                                                    <th>Ответственный</th>
                                                </tr>
                                                </thead>
                                                <tbody id="offer_body">

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="requests">
                                    <div class="row">
                                        <div class="table-responsive">
                                            <table class="table table-condensed table-bordered">
                                                <thead>
                                                <tr>
                                                    <th>Цена</th>
                                                    <th>Цена в валюте</th>
                                                    <th>Курс</th>
                                                    <th>Валюта</th>
                                                    <th>Дата</th>
                                                    <th>Кол-во</th>
                                                    <th>Поставщик</th>
                                                    <th>Контрагент</th>
                                                    <th>Ответственный</th>
                                                </tr>
                                                </thead>
                                                <tbody id="request_body">

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="panel-heading">
                <a href="{{ route('saleAdd') }}" target="_blank">
                    <button type="button" class="btn btn-primary btn-sm btn-o"><i class="fa fa-rub"
                                                                                  aria-hidden="true"></i> Новая
                        продажа
                    </button>
                </a>
                <a href="#">
                    <button type="button" class="btn btn-primary btn-sm btn-o" id="list_doc"><i class="fa fa-list"
                                                                                  aria-hidden="true"></i> Список документов
                    </button>
                </a>
            </div>
            <div class="col-md-12">
                <div class=" table-responsive">
                    @if($sales)
                        <table class="table table-condensed table-bordered">
                            <thead>
                            <tr>
                                <th>Номер</th>
                                <th>Дата</th>
                                <th>Сумма</th>
                                <th>Клиент</th>
                                <th>Состояние</th>
                                <th>Срок выполнения</th>
                                <th>% оплаты</th>
                                <th>% долга</th>
                                <th>Валюта</th>
                                <th>Менеджер</th>
                                <th>Действия</th>
                            </tr>
                            </thead>
                            <tbody id="doclist">
                            @foreach($sales as $row)
                                <tr id="sale{{ $row->id }}" class="row_clicable sale-pos">
                                    <td>{{ $row->doc_num }}</td>
                                    <td>{{ $row->created_at }}</td>
                                    <td>{{ $row->amount }}</td>
                                    <td>{{ $row->firm->title }}</td>
                                    <td>{{ $row->status }}</td>
                                    @if(!empty($row->date_agreement))
                                        <td>{{ $row->date_agreement }}</td>
                                    @else
                                        <td></td>
                                    @endif
                                    <td>%</td>
                                    <td>%</td>
                                    <td>{{ $row->currency->title }}</td>
                                    <td>{{ $row->user->name }}</td>
                                    <td style="width:100px;">
                                        <div class="form-group" role="group">
                                            <a href="{{route('saleView',['id'=>$row->id])}}">
                                                <button class="btn btn-info" type="button"
                                                        title="Просмотр записи"><i class="fa fa-eye fa-lg>"
                                                                                   aria-hidden="true"></i></button>
                                            </a>
                                            <button class="btn btn-danger del_pos" type="button"
                                                    title="Удалить запись"><i class="fa fa-trash fa-lg>"
                                                                               aria-hidden="true"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- /page content -->
@endsection

@section('user_script')
    <script src="/js/bootstrap-typeahead.min.js"></script>
    <script src="/js/select2.min.js"></script>
    <script>
        let active_tab = 'sales'; //по умолчанию
        let good_id = 0;
        $('.select2').css('width', '100%').select2({
            placeholder: "Выбор контрагента",
            allowClear: true
        })

        $('.clicable').css('cursor', 'pointer');
        $('.row_clicable').css('cursor', 'pointer');

        $("#firm_id").prepend($('<option value="0">Выберите организацию</option>'));
        $("#firm_id :first").attr("selected", "selected");
        $("#firm_id :first").attr("disabled", "disabled");

        $('#by_vendor').hide();
        $('#by_analog').hide();
        $('#by_catalog').hide();

        $('#filter').change(function () {
            let val = $('#filter').val();
            switch (val) {
                case 'name':
                    $('#by_name').show();
                    $('#by_vendor').hide();
                    $('#by_analog').hide();
                    $('#by_catalog').hide();
                    break;
                case 'vendor':
                    $('#by_vendor').show();
                    $('#by_name').hide();
                    $('#by_analog').hide();
                    $('#by_catalog').hide();
                    break;
                case 'analog':
                    $('#by_vendor').hide();
                    $('#by_name').hide();
                    $('#by_analog').show();
                    $('#by_catalog').hide();
                    break;
                case 'catalog':
                    $('#by_vendor').hide();
                    $('#by_name').hide();
                    $('#by_analog').hide();
                    $('#by_catalog').show();
                    break;
            }

        });

        $('#by_analog').typeahead({
            hint: true,
            highlight: true,
            minLength: 5,
            ajax: {
                url: "{{ route('getAnalog') }}",
                triggerLength: 1
            }
        });

        $('#by_name').typeahead({
            hint: true,
            highlight: true,
            minLength: 5,
            ajax: {
                url: "{{ route('searchGood') }}",
                triggerLength: 1
            }
        });

        $('#by_vendor').typeahead({
            hint: true,
            highlight: true,
            minLength: 5,
            ajax: {
                url: "{{ route('getCode') }}",
                triggerLength: 1
            }
        });

        $('#by_catalog').typeahead({
            hint: true,
            highlight: true,
            minLength: 5,
            ajax: {
                url: "{{ route('getCatalogNum') }}",
                triggerLength: 1
            }
        });

        $('.master').change(function(){
            let name = $(this).val();
            let filter = $('#filter').val();
            //$('#t_body').empty();
            if(name.length > 10){
                $.ajax({
                    type: 'POST',
                    url: '{{ route('findGoodAnalogs') }}',
                    data: {'name': name,'filter':filter},
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        //alert(res);
                        $("#t_body").html($(res));
                        $('.clicable').css('cursor', 'pointer');
                    }
                });
            }
        });

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            let name = e.target.href.split('#');
            active_tab = name[1];
            if(good_id > 0)
                getData(active_tab,good_id);
            //console.log(active_tab);
        })

        $(document).on({
            click: function () {
                good_id = $(this).attr('id');
                $('.clicable').each(function(i,elem) {
                    if ($(this).hasClass("info")) {
                        $(this).removeClass('info');
                    }
                });
                $(this).addClass('info');
                //console.log(active_tab + ' : ' + id);
                getData(active_tab,good_id);
            }
        }, ".clicable");

        function getData(name,id){
            $("#"+name+"_body").empty();
            $.ajax({
                type: 'POST',
                url: '{{ route('GoodParams') }}',
                data: {'name': name,'good_id': id},
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    //alert(res);
                    $("#"+name+"_body").prepend($(res));
                }
            });
        }

        $(document).on({
            dblclick: function () {
                good_id = $(this).attr('id');
                alert('id='+good_id);
            }
        }, ".clicable");

        $(document).on({
            dblclick: function () {
                sale_id = $(this).attr('id');
                alert('id='+sale_id);
            }
        }, ".row_clicable");

        $('#list_doc').click(function(){
            $.ajax({
                type: 'POST',
                url: '{{ route('saleList') }}',
                data: {'name': 'list'},
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    //alert(res);
                    $("#doclist").html($(res));
                }
            });
        });

        $(document).on({
            click: function () {
                sale_id = $(this).parent().parent().parent().attr('id');
                id = sale_id.replace('sale','')
                let x = confirm("Выбранная запись будет удалена. Продолжить (Да/Нет)?");
                if (x) {
                    $.ajax({
                        type: 'POST',
                        url: '{{ route('delSale') }}',
                        data: {'id': id},
                        headers: {
                            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (res) {
                            //alert(res);
                            if (res == 'OK') {
                                $('#'+sale_id).hide();
                            }
                            if (res == 'BAD')
                                alert('Выполнение операции запрещено!');
                            if (res == 'NO')
                                alert('Не известный метод!');
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            alert(xhr.status);
                            alert(thrownError);
                        }
                    });
                } else {
                    return false;
                }
            }
        }, ".del_pos");

    </script>
@endsection

