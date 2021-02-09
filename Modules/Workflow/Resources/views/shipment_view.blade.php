@extends('layouts.main')

@section('dashboard')

@endsection
@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li><a href="{{ route('shipments') }}">Наряды на сборку</a></li>
        <li class="active">{{ $title }}</li>
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
            <div class="col-md-12">
                <h2 class="text-center">{{ $head }}</h2>
                <h4 class="text-center">дата создания {{ $doc->created_at }}</h4>
                <div class="tabbable pills">
                    <ul id="myTab3" class="nav nav-pills">
                        <li class="active">
                            <a href="#common" data-toggle="tab">
                                Задания на сборку
                            </a>
                        </li>
                        <li>
                            <a href="#links" data-toggle="tab">
                                Цепочка документов
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade in active" id="common">
                            <table class="table table-bordered table-hover">
                                <tr>
                                    <th>Основание</th>
                                    <td>Заказ клиента №{{ $doc->sale->doc_num }}</td>
                                    <th>Склад</th>
                                    <td>{{ $doc->warehouse->title }}</td>
                                </tr>
                                <tr>
                                    <th>Менеджер</th>
                                    <td>{{ $doc->author->name }}</td>
                                    <th>Ответственный</th>
                                    <td>{{ $doc->user->name }}</td>
                                </tr>
                                {!! Form::open(['url' => route('shipmentEdit'),'class'=>'form-horizontal','method'=>'POST', 'id'=>'form_ref']) !!}
                                {!! Form::hidden('id',$doc->id,['class' => 'form-control','required'=>'required']) !!}
                                <tr>
                                    <th>Статус</th>
                                    <td>
                                        {!! Form::select('stage',['0'=>'Комплектуется','1'=>'Собран','2'=>'Собран частично','3'=>'Оформление документов',
                                            '4'=>'Отгружен','5'=>'Отгружен частично'],$doc->stage, ['class' => 'form-control','required'=>'required','id'=>'stage']); !!}
                                    </td>
                                    <th>Приоритет</th>
                                    <td>
                                        {!! Form::number('rank',$doc->rank,['class' => 'form-control','placeholder'=>'Введите число','required'=>'required','min' => 1, 'max' => 10])!!}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Место сборки</th>
                                    <td>
                                        {!! Form::select('dst_id',$dstsel, $doc->dst_id, ['class' => 'form-control','required'=>'required']); !!}
                                    </td>
                                    <td colspan="2">
                                        <div class="col-xs-8">
                                            {!! Form::button('Обновить', ['class' => 'btn btn-primary','type'=>'submit', 'id'=>'save_btn']) !!}
                                        </div>
                                    </td>
                                </tr>
                            {!! Form::close() !!}
                            </table>
                            <div class="panel-heading">
                                @if($work_task)
                                <a href="{{ route('shipmentPrint',['id'=>$doc->id]) }}">
                                    <button type="button" class="btn btn-primary btn-sm btn-o" id="print">
                                        <i class="fa fa-print" aria-hidden="true"></i> Печать заданий
                                    </button>
                                </a>
                                @endif
                                @if($rows)
                                    <h4 class="pull-right" id="state"> Выполнено заданий: {{ $done_task }} из {{ $work_task }}</h4>
                                @else
                                    <h4 class="pull-right" id="state"> Выполнено заданий: 0 из 0</h4>
                                @endif
                            </div>
                            <div class="table-responsive">
                                <table id="doc_table" class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th>Из ячейки</th>
                                        <th>В ячейку</th>
                                        <th>Номенклатура</th>
                                        <th>Кол-во</th>
                                        <th>Ед.изм</th>
                                        <th>Действия</th>
                                    </tr>
                                    </thead>
                                    @if($rows)
                                        <tbody id="t_body">
                                        @foreach($rows as $row)
                                            <tr id="{{ $row->id }}">
                                                <td>{{ $row->src->title }}</td>
                                                <td>{{ $row->dest_location }}</td>
                                                <td>{{ $row->good->title }}</td>
                                                <td>{{ $row->qty }}</td>
                                                <td>{{ $row->unit->title }}</td>
                                                <td style="width:70px;">
                                                    @if($row->stage)
                                                    <button class="btn btn-success btn-sm"
                                                            type="button" title="Перемещено"><i
                                                            class="fa fa-cart-arrow-down fa-lg"
                                                            aria-hidden="true"></i>
                                                    </button>
                                                    @else
                                                        <button class="btn btn-warning btn-sm pos_edit"
                                                                type="button" title="Переместить"><i
                                                                class="fa fa-bell fa-lg"
                                                                aria-hidden="true"></i>
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    @endif
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="links">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table id="doc_table" class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <th>Документ</th>
                                            <th>Статус</th>
                                            <th>Дата создания</th>
                                            <th>Автор</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        {!! $tbody !!}
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
    <!-- /page content -->
@endsection

@section('user_script')

    <script>

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

        $(document).on({
            click: function () {
                let id = $(this).parent().parent().attr('id');
                $.ajax({
                    type: 'POST',
                    url: '{{ route('doneTask') }}',
                    data: {'id': id},
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        //alert(res);
                        if (res == 'BAD')
                            alert('Выполнение операции запрещено!');
                        if (res == 'NO')
                            alert('Не известный метод!');
                        let obj = jQuery.parseJSON(res);
                        if (typeof obj === 'object') {
                            $('#state').text('Выполнено заданий: ' + obj.done + ' из ' + obj.work);
                            $('#' + id).html(obj.content);
                            if(obj.work == 0) {
                                $('#stage option[value=1]').prop('selected', true);
                                $('#print').hide();
                            }
                        }
                    }
                });

            }
        }, ".pos_edit");

    </script>
@endsection
