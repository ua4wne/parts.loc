@extends('layouts.main')

@section('dashboard')

@endsection
@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li class="active"><a href="{{ route('countries') }}">{{ $title }}</a></li>
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
        <!-- Import Country Modal -->
        <div class="modal fade" id="importCountries" tabindex="-1" role="dialog" aria-labelledby="importCountries"
             aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i class="fa fa-times-circle fa-lg" aria-hidden="true"></i>
                        </button>
                        <h4 class="modal-title">Загрузка данных из Excel</h4>
                    </div>
                    <div class="modal-body">
                        {!! Form::open(['url' => '#','id'=>'import_country','class'=>'form-horizontal','method'=>'POST','files'=>'true']) !!}

                        <div class="form-group">
                            <label class="col-xs-3 control-label">
                                Файл Excel: <span class="symbol required" aria-required="true"></span>
                            </label>
                            <div class="col-xs-8">
                                {!! Form::file('file', ['class' => 'form-control','data-buttonText'=>'Выберите файл Excel','data-buttonName'=>"btn-primary",'data-placeholder'=>"Файл не выбран",'required'=>'required','id'=>'file']) !!}
                            </div>
                        </div>

                        {!! Form::close() !!}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                        <button type="button" class="btn btn-primary" id="btn_import">Загрузить</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Import Country Modal -->
        <div id="loader"></div> <!--  идентификатор загрузки (анимация) - ожидания выполнения-->
        <div class="row">
            <h2 class="text-center">{{ $head }}</h2>
            <div class="panel-heading">
                <a href="{{route('countryAdd')}}">
                    <button type="button" class="btn btn-primary btn-sm btn-o"><i class="fa fa-plus"
                                                                                  aria-hidden="true"></i> Новая
                        запись
                    </button>
                </a>
                <button type="button" class="btn btn-primary btn-sm btn-o" id="import" data-toggle="modal"
                        data-target="#importCountries">
                    <i class="fa fa-download" aria-hidden="true"></i>
                    Импорт
                </button>
                <a href="{{ route('exportCountries') }}">
                    <button type="button" class="btn btn-primary btn-sm btn-o"><i class="fa fa-upload"
                                                                                  aria-hidden="true"></i> Экспорт
                    </button>
                </a>
            </div>
            <div class="col-md-12">
                <div class=" table-responsive">
                    @if($rows)
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>Наименование</th>
                                <th>Код</th>
                                <th>Код альфа-2</th>
                                <th>Код альфа-3</th>
                                <th>Участник ЕАЭС</th>
                                <th>Полное наименование</th>
                                <th>Действия</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($rows as $k => $row)
                                <tr>
                                    <td>{{ $row->title }}</td>
                                    <td>{{ $row->code1 }}</td>
                                    <td>{{ $row->code2 }}</td>
                                    <td>{{ $row->code3 }}</td>
                                    <td>
                                        @if($row->eaes)
                                            <span role="button" class="label label-success">Да</span>
                                        @else
                                            <span role="button" class="label label-danger">Нет</span>
                                        @endif
                                    </td>
                                    <td>{{ $row->full_name }}</td>
                                    <td style="width:100px;">
                                        {!! Form::open(['url'=>route('countryEdit',['id'=>$row->id]), 'class'=>'form-inline','method' => 'POST', 'onsubmit' => 'return confirmDelete()']) !!}
                                        {{ method_field('DELETE') }}
                                        <div class="form-group" role="group">
                                            <a href="{{route('countryEdit',['id'=>$row->id])}}">
                                                <button class="btn btn-success" type="button"
                                                        title="Редактировать запись"><i class="fa fa-edit fa-lg>"
                                                                                        aria-hidden="true"></i></button>
                                            </a>
                                            {!! Form::button('<i class="fa fa-trash-o fa-lg>" aria-hidden="true"></i>',['class'=>'btn btn-danger','type'=>'submit','title'=>'Удалить запись']) !!}
                                        </div>
                                        {!! Form::close() !!}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {{ $rows->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- /page content -->
@endsection

@section('user_script')
    @include('confirm')
    <script>
        $('#btn_import').click(function (e) {
            e.preventDefault();
            let error = 0;
            $("#import_country").find(":input").each(function () {// проверяем каждое поле ввода в форме
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
                $('#loader').hide();
                alert("Не выбран файл Excel!");
                return false;
            } else {
                let formData = new FormData();
                formData.append('file', $('#file').prop("files")[0]);
                $.ajax({
                    type: 'POST',
                    url: '{{ route('importCountries') }}',
                    processData: false,
                    contentType: false,
                    cache:false,
                    dataType : 'text',
                    data: formData,
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function(){
                        $('#loader').show();
                    },
                    success: function (res) {
                        //alert(res);
                        let obj = jQuery.parseJSON(res);
                        if (typeof obj === 'object') {
                            $(".modal").modal("hide");
                            alert('Обработано строк '+obj.num+' из '+obj.rows+'!');
                            location.reload();
                        }
                        if (res == 'NO')
                            alert('Выполнение операции запрещено!');
                        else if (res == 'ERR')
                            alert('При обновлении данных возникла ошибка!');
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        alert(xhr.status + '\n' + thrownError);
                    }
                });
                $('#loader').hide();
            }
        });
    </script>
@endsection
