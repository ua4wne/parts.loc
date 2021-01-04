@extends('layouts.main')

@section('dashboard')

@endsection
@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li><a href="{{ route('stock_exchange') }}">Складские остатки</a></li>
        <li class="active">Импорт из 1С</li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- page content -->
    <div class="container-fluid container-fullw bg-white">
        <div id="loader"></div> <!--  идентификатор загрузки (анимация) - ожидания выполнения-->
        <div class="panel-heading">
            <h2 class="text-center">Загрузка данных из Excel</h2>
            {!! Form::open(['url' => '#','id'=>'import_doc','class'=>'form-horizontal','method'=>'POST','files'=>'true']) !!}

            <div class="form-group">
                <label class="col-xs-3 control-label">
                    Файл Excel: <span class="symbol required" aria-required="true"></span>
                </label>
                <div class="col-xs-8">
                    {!! Form::file('file', ['class' => 'form-control','data-buttonText'=>'Выберите файл Excel','data-buttonName'=>"btn-primary",'data-placeholder'=>"Файл не выбран",'required'=>'required','id'=>'file']) !!}
                </div>
            </div>

            <div class="form-group">
                <div class="col-xs-offset-2 col-xs-10">
                    {!! Form::button('Загрузить', ['class' => 'btn btn-primary','type'=>'submit','id'=>'save_btn']) !!}
                </div>
            </div>

            {!! Form::close() !!}
        </div>
    </div>
@endsection

@section('user_script')

    <script>

        $('#file').click(function(){
            $('#file').css('border', '1px solid green');// устанавливаем рамку зеленого цвета
            return true;
        });

        $('#save_btn').click(function (e) {
            e.preventDefault();
            let error = 0;
            $("#import_doc").find(":input").each(function () {// проверяем каждое поле ввода в форме
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
            else {
                $("#loader").show();
                let formData = new FormData();
                formData.append('file', $('#file').prop("files")[0]);
                $.ajax({
                    type: 'POST',
                    url: '{{ route('stock_exchange') }}',
                    processData: false,
                    contentType: false,
                    cache: false,
                    dataType: 'text',
                    data: formData,
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        $("#loader").hide();
                        //alert(res);
                        if (res == 'NO')
                            alert('Не достаточно прав!');
                        if (res == 'BAD_FILE'){
                            alert('Не верный формат файла для загрузки!');
                            $('#file').css('border', '1px solid red');// устанавливаем рамку красного цвета
                        }
                        if (res == 'ERR')
                            alert('При обновлении данных возникла ошибка!');
                        let obj = jQuery.parseJSON(res);
                        if (typeof obj === 'object') {
                            alert('Загружено строк ' + obj.num + ' из ' + obj.rows + '.\n' +
                                'Ошибок загрузки данных: ' + obj.err);

                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        $("#loader").hide();
                        alert(xhr.status + '\n' + thrownError);
                    }
                });
            }
        });
    </script>

@endsection
