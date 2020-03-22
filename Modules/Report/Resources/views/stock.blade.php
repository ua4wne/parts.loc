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
        <li class="active"><a href="{{ route('stock-report') }}">{{ $title }}</a></li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- page content -->
    <div class="container-fluid container-fullw bg-white">
        <div id="loader"></div> <!--  идентификатор загрузки (анимация) - ожидания выполнения-->
        <div class="row">
            <div class="col-md-12">
                <div class="panel-heading">
                    <h2 class="text-header text-center">{{ $head }}</h2>
                    <h4 class="text-header text-center text-info"></h4>
                    <p class="text-header text-center text-info"></p>
                </div>
                <div class="x_content">
                    {!! Form::open(['url' => '#','class'=>'form-horizontal','method'=>'POST','id'=>'new_val']) !!}

                    <div class="form-group">
                        {!! Form::label('warehouse_id', 'Выбор склада:',['class'=>'col-xs-2 control-label']) !!}
                        <div class="col-xs-8">
                            {!! Form::select('warehouse_id', $wxsel, old('warehouse_id'),['class' => 'form-control','required' => 'required','id'=>'warehouse_id']); !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('category_id', 'Категории номенклатуры:',['class'=>'col-xs-2 control-label']) !!}
                        <div class="col-xs-8">
                            {!! Form::select('category_id', $catsel, old('category_id'),['class' => 'form-control','id'=>'category_id']); !!}
                            <div class="alert alert-warning"><p class="text-center">Если категория не выбрана, то в отчет попадет вся номенклатура!</p></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-offset-2 col-xs-8">
                            {!! Form::button('<span class="fa  fa-bar-chart-o"></span> Сформировать', ['class' => 'btn btn-success','type'=>'submit','id'=>'report']) !!}
                            {!! Form::button('<span class="fa  fa-file-excel-o"></span> Скачать', ['class' => 'btn btn-primary','type'=>'submit','name' => 'export','value' => 'export','id'=>'export']) !!}
                        </div>
                    </div>

                    {!! Form::close() !!}
                </div>
                <a href="#"
                   onclick="$('.x_content').show(); $('#result').hide(); $('.fa-plus-square-o').hide(); $('p.text-header').text(''); $('h2.text-header').text('Задайте условия отбора'); return false;"><i
                        class="fa fa-plus-square-o fa-lg" aria-hidden="true"></i></a>
                <div class="x_panel" id="result">

                </div>

            </div>
        </div>
    </div>
    <!-- /page content -->
@endsection

@section('user_script')
    <script src="/js/gstatic_charts_loader.js"></script>
    <script>
        $('#result').hide();
        $('.fa-plus-square-o').hide();
        let c = 0;
        $('.other').hide(); //скрыли значения в таблице вывода анкеты

        $("#warehouse_id").prepend($('<option value="0">Выберите склад</option>'));
        $("#warehouse_id :first").attr("selected", "selected");
        $("#warehouse_id :first").attr("disabled", "disabled");

        $("#category_id").prepend($('<option value="0">Выберите категорию номенклатуры</option>'));
        $("#category_id :first").attr("selected", "selected");
        $("#category_id :first").attr("disabled", "disabled");

        $(document).on({
            click: function () {
                $(this).removeClass('fa-expand');
                $(this).addClass('fa-compress');
                $(this).parent().next().show();

            }
        }, ".fa-expand");

        $(document).on({
            click: function () {
                $(this).removeClass('fa-compress');
                $(this).addClass('fa-expand');
                $(this).parent().next().hide();
            }
        }, ".fa-compress");

        $('#export').click(function () {
            //e.preventDefault();
            let error = 0;
            $("#new_val").find(":input").each(function () {// проверяем каждое поле ввода в форме
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
            $("#loader").show();
            return true;
        });

        $('#report').click(function (e) {
            e.preventDefault();
            let error = 0;
            $("#new_val").find(":input").each(function () {// проверяем каждое поле ввода в форме
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
            $("#loader").show();
            $.ajax({
                url: '{{ route('stock-report') }}',
                type: 'POST',
                data: $('#new_val').serialize(),
                success: function (res) {
                    //alert("Сервер вернул вот что: " + res);
                    let obj = jQuery.parseJSON(res);
                    $("p.text-header").text('Общее число товарных позиций ' + obj[0].qty + ' на сумму ' + obj[1].cost +' руб.');
                    $('#result').show();
                    $('#result').html(obj[2].content);
                    $("h2.text-header").text($('#warehouse_id option:selected').text());
                    //Выберите категорию номенклатуры
                    if($('#category_id option:selected').text()=='Выберите категорию номенклатуры')
                        $("h4.text-header").text('Все категории номенклатуры');
                    else
                        $("h4.text-header").text($('#category_id option:selected').text());
                    $('.x_content').hide();
                    $('.other').hide(); //скрыли значения в таблице вывода анкеты
                    $('.fa-plus-square-o').show();
                    $("#loader").hide();
                },
                error: function (xhr, response) {
                    alert('Error! ' + xhr.responseText);
                }
            });
        });

    </script>
@endsection
