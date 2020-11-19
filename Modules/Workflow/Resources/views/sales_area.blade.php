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
            <div class="panel-heading">
                <a href="#">
                    <button type="button" class="btn btn-primary btn-sm btn-o"><i class="fa fa-rub"
                                                                                  aria-hidden="true"></i> Новая
                        продажа
                    </button>
                </a>
            </div>
            <div class="col-md-6 panel panel-white">
                <div class="row">
                    <fieldset>
                        <legend>Поиск номенклатуры</legend>
                        <table class="table">
                            <tr>
                                <td>
                                    {!! Form::select('search',['name'=>'Наименование','vendor'=>'Артикул','analog'=>'Аналог',
                                    'catalog'=>'Каталожный №',], old('search'), ['class' => 'form-control','required'=>'required','id'=>'filter']); !!}
                                </td>
                                <th>содержит:</th>
                                <td>
                                    {!! Form::text('name',old('name'),['class' => 'form-control','placeholder'=>'Начинайте ввод для поиска',
                                        'required'=>'required','id'=>'by_name'])!!}
                                    {!! Form::text('vendor',old('vendor'),['class' => 'form-control','placeholder'=>'Начинайте ввод для поиска',
                                        'required'=>'required','id'=>'by_vendor'])!!}
                                    {!! Form::text('analog',old('analog'),['class' => 'form-control','placeholder'=>'Начинайте ввод для поиска',
                                        'required'=>'required','id'=>'by_analog'])!!}
                                    {!! Form::text('catalog',old('catalog'),['class' => 'form-control','placeholder'=>'Начинайте ввод для поиска',
                                        'required'=>'required','id'=>'by_catalog'])!!}
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </div>
                <div class="row">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
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
                                    <tr id="{{ $row->id }}">
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
            <div class="col-md-6 panel panel-white">
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
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade" id="common">
                                    common
                                </div>
                                <div class="tab-pane fade" id="stock">
                                    stock
                                </div>
                                <div class="tab-pane fade  in active" id="sales">
                                    sales
                                </div>
                                <div class="tab-pane fade" id="purchases">
                                    purchases
                                </div>
                            </div>
                        </div>
                    </div>
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
        $('.select2').css('width', '100%').select2({
            placeholder: "Выбор контрагента",
            allowClear: true
        })
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
            minLength: 3,
            ajax: {
                url: "{{ route('getAnalog') }}",
                triggerLength: 1
            }
        });

    </script>
@endsection

