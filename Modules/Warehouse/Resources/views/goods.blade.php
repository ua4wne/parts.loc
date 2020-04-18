@extends('layouts.main')
@section('user_css')
    <link href="/css/jstree/themes/default/style.min.css" rel="stylesheet" media="screen">
    <link href="/css/DT_bootstrap.css" rel="stylesheet" media="screen">
@endsection
@section('dashboard')

@endsection
@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li class="active"><a href="{{ route('goods') }}">{{ $title }}</a></li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- page content -->
    <div class="container-fluid container-fullw bg-white">
        <div id="loader"></div> <!--  идентификатор загрузки (анимация) - ожидания выполнения-->
        <div class="row">
            <div class="col-md-2">
                <div class="panel panel-white">
                    <div class="panel-heading">
                        <p class="panel-title">Категории номенклатуры</p>
                    </div>
                    <div class="panel-body">
                        <div id="categories" class="tree-demo jstree jstree-3 jstree-default" role="tree">

                        </div>
                    </div>
                </div>
            </div>
            <!-- Import Good Modal -->
            <div class="modal fade" id="importGood" tabindex="-1" role="dialog" aria-labelledby="importGood"
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
                            {!! Form::open(['url' => '#','id'=>'import_good','class'=>'form-horizontal','method'=>'POST','files'=>'true']) !!}

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
                            <span class="pull-left" id="file-loader"><img src="/images/file-loader.gif"></span> <!--  идентификатор загрузки (анимация) - ожидания выполнения-->
                            <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                            <button type="button" class="btn btn-primary" id="btn_import">Сохранить</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Import Good Modal -->
            <!-- Export Good Modal -->
            <div class="modal fade" id="exportGood" tabindex="-1" role="dialog" aria-labelledby="exportGood"
                 aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <i class="fa fa-times-circle fa-lg" aria-hidden="true"></i>
                            </button>
                            <h4 class="modal-title">Выгрузка данных в Excel</h4>
                        </div>
                        <div class="modal-body">
                            {!! Form::open(['url' => route('exportGood'),'class'=>'form-horizontal','method'=>'POST','data-function'=>'no_delete']) !!}
                            <div class="form-group">
                                <label class="col-xs-3 control-label">
                                    Категория номенклатуры: <span class="symbol required" aria-required="true"></span>
                                </label>
                                <div class="col-xs-8">
                                    {!! Form::select('category[]',$catsel, old('category'), ['class' => 'form-control','required'=>'required',' multiple'=>' multiple']); !!}
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-xs-offset-3 col-xs-8">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                                    {!! Form::submit('Создать',['class'=>'btn btn-primary']) !!}
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                        <div class="modal-footer">

                        </div>
                    </div>
                </div>
            </div>
            <!-- Export Good Modal -->
            <!-- Add Good Modal -->
            <div class="modal fade" id="newGood" tabindex="-1" role="dialog" aria-labelledby="newGood"
                 aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <i class="fa fa-times-circle fa-lg" aria-hidden="true"></i>
                            </button>
                            <h4 class="modal-title">Новая номенклатура</h4>
                        </div>
                        <div class="modal-body">
                            {!! Form::open(['url' => '#','id'=>'new_good','class'=>'form-horizontal','method'=>'POST']) !!}

                            <div class="form-group">
                                <label class="col-xs-3 control-label">
                                    Категория номенклатуры: <span class="symbol required" aria-required="true"></span>
                                </label>
                                <div class="col-xs-8">
                                    {!! Form::select('category_id',$catsel, old('category_id'), ['class' => 'form-control','required'=>'required','id'=>'category_id']); !!}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label">
                                    Товарная группа: <span class="symbol required" aria-required="true"></span>
                                </label>
                                <div class="col-xs-8">
                                    {!! Form::select('group_id',$groupsel, old('group_id'), ['class' => 'form-control','required'=>'required','id'=>'group_id']); !!}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label">
                                    Наименование: <span class="symbol required" aria-required="true"></span>
                                </label>
                                <div class="col-xs-8">
                                    {!! Form::text('title',old('title'),['class' => 'form-control','placeholder'=>'Введите наименование','maxlength'=>'200','required'=>'required','id'=>'title'])!!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('descr','Описание:',['class' => 'col-xs-3 control-label'])   !!}
                                <div class="col-xs-8">
                                    {!! Form::textarea('descr',old('descr'),['class' => 'form-control','rows' => 3, 'cols' => 54,'placeholder'=>'Введите описание','maxlength'=>'255','id'=>'descr'])!!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('descr','Код группы на сайте:',['class' => 'col-xs-3 control-label'])   !!}
                                <div class="col-xs-8">
                                    {!! Form::text('bx_group',old('bx_group'),['class' => 'form-control','placeholder'=>'Введите код группы','maxlength'=>'5'])!!}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label">
                                    Артикул: <span class="symbol required" aria-required="true"></span>
                                </label>
                                <div class="col-xs-8">
                                    {!! Form::text('vendor_code',old('vendor_code'),['class' => 'form-control','placeholder'=>'Введите артикул','maxlength'=>'64','id'=>'vendor_code'])!!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('analog_code','Коды аналогов:',['class' => 'col-xs-3 control-label'])   !!}
                                <div class="col-xs-8">
                                    {!! Form::text('analog_code',old('analog_code'),['class' => 'form-control','placeholder'=>'Введите артикулы аналогов через запятую','maxlength'=>'180','id'=>'analog_code'])!!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('brand','Производитель:',['class' => 'col-xs-3 control-label'])   !!}
                                <div class="col-xs-8">
                                    {!! Form::text('brand',old('brand'),['class' => 'form-control','placeholder'=>'Укажите производителя','maxlength'=>'200','id'=>'brand'])!!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('model','Модель:',['class' => 'col-xs-3 control-label'])   !!}
                                <div class="col-xs-8">
                                    {!! Form::text('model',old('model'),['class' => 'form-control','placeholder'=>'Укажите модель','maxlength'=>'200','id'=>'model'])!!}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label">
                                    Основная ед. измерения: <span class="symbol required" aria-required="true"></span>
                                </label>
                                <div class="col-xs-8">
                                    {!! Form::select('unit_id',$unitsel, old('unit_id'), ['class' => 'form-control','required'=>'required']); !!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('weight','Вес, кг:',['class' => 'col-xs-3 control-label'])   !!}
                                <div class="col-xs-8">
                                    {!! Form::text('weight',old('weight'),['class' => 'form-control','placeholder'=>'Укажите вес'])!!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('capacity','Объем, м3:',['class' => 'col-xs-3 control-label'])   !!}
                                <div class="col-xs-8">
                                    {!! Form::text('capacity',old('capacity'),['class' => 'form-control','placeholder'=>'Укажите объем'])!!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('length','Длина, м:',['class' => 'col-xs-3 control-label'])   !!}
                                <div class="col-xs-8">
                                    {!! Form::text('length',old('length'),['class' => 'form-control','placeholder'=>'Укажите длину'])!!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('area','Площадь, м2:',['class' => 'col-xs-3 control-label'])   !!}
                                <div class="col-xs-8">
                                    {!! Form::text('area',old('area'),['class' => 'form-control','placeholder'=>'Укажите площадь'])!!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('vat','Ставка НДС, %:',['class' => 'col-xs-3 control-label'])   !!}
                                <div class="col-xs-8">
                                    {!! Form::text('vat','20',['class' => 'form-control','placeholder'=>'Укажите процент НДС','id'=>'vat'])!!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('gtd','Учет по ГДТ:',['class' => 'col-xs-3 control-label'])   !!}
                                <div class="col-xs-8">
                                    {!! Form::select('gtd',['0'=>'Нет','1'=>'Да'], old('gtd'), ['class' => 'form-control','id'=>'gtd']); !!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('barcode','Штрихкод:',['class' => 'col-xs-3 control-label'])   !!}
                                <div class="col-xs-8">
                                    {!! Form::text('barcode',old('barcode'),['class' => 'form-control','placeholder'=>'Введите штрихкод','maxlength'=>'100','id'=>'barcode'])!!}
                                </div>
                            </div>

                            {!! Form::close() !!}
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                            <button type="button" class="btn btn-primary" id="btn_new">Сохранить</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Add Good Modal -->
            <!-- Edit Good Modal -->
            <div class="modal fade" id="editGood" tabindex="-1" role="dialog" aria-labelledby="editGood"
                 aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <i class="fa fa-times-circle fa-lg" aria-hidden="true"></i>
                            </button>
                            <h4 class="modal-title">Редактирование</h4>
                        </div>
                        <div class="modal-body">
                            {!! Form::open(['url' => '#','id'=>'edit_good','class'=>'form-horizontal','method'=>'POST']) !!}

                            <div class="form-group">
                                <div class="col-xs-8">
                                    {!! Form::hidden('id','',['class' => 'form-control','required'=>'required','id'=>'egood_id']) !!}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label">
                                    Категория номенклатуры: <span class="symbol required" aria-required="true"></span>
                                </label>
                                <div class="col-xs-8">
                                    {!! Form::select('category_id',$catsel, old('category_id'), ['class' => 'form-control','required'=>'required','id'=>'ecategory_id']); !!}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label">
                                    Товарная группа: <span class="symbol required" aria-required="true"></span>
                                </label>
                                <div class="col-xs-8">
                                    {!! Form::select('group_id',$groupsel, old('group_id'), ['class' => 'form-control','required'=>'required','id'=>'egroup_id']); !!}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label">
                                    Наименование: <span class="symbol required" aria-required="true"></span>
                                </label>
                                <div class="col-xs-8">
                                    {!! Form::text('title',old('title'),['class' => 'form-control','placeholder'=>'Введите наименование','maxlength'=>'200','required'=>'required','id'=>'etitle'])!!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('descr','Описание:',['class' => 'col-xs-3 control-label'])   !!}
                                <div class="col-xs-8">
                                    {!! Form::textarea('descr',old('descr'),['class' => 'form-control','rows' => 3, 'cols' => 54,'placeholder'=>'Введите описание','maxlength'=>'255','id'=>'edescr'])!!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('descr','Код группы на сайте:',['class' => 'col-xs-3 control-label'])   !!}
                                <div class="col-xs-8">
                                    {!! Form::text('bx_group',old('bx_group'),['class' => 'form-control','placeholder'=>'Введите код группы','maxlength'=>'5','id'=>'ebx_group'])!!}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label">
                                    Артикул: <span class="symbol required" aria-required="true"></span>
                                </label>
                                <div class="col-xs-8">
                                    {!! Form::text('vendor_code',old('vendor_code'),['class' => 'form-control','placeholder'=>'Введите артикул','maxlength'=>'64','id'=>'evendor_code'])!!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('analog_code','Коды аналогов:',['class' => 'col-xs-3 control-label'])   !!}
                                <div class="col-xs-8">
                                    {!! Form::text('analog_code',old('analog_code'),['class' => 'form-control','placeholder'=>'Введите артикулы аналогов через запятую','maxlength'=>'180','id'=>'eanalog_code'])!!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('brand','Производитель:',['class' => 'col-xs-3 control-label'])   !!}
                                <div class="col-xs-8">
                                    {!! Form::text('brand',old('brand'),['class' => 'form-control','placeholder'=>'Укажите производителя','maxlength'=>'200','id'=>'ebrand'])!!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('model','Модель:',['class' => 'col-xs-3 control-label'])   !!}
                                <div class="col-xs-8">
                                    {!! Form::text('model',old('model'),['class' => 'form-control','placeholder'=>'Укажите модель','maxlength'=>'200','id'=>'emodel'])!!}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label">
                                    Основная ед. измерения: <span class="symbol required" aria-required="true"></span>
                                </label>
                                <div class="col-xs-8">
                                    {!! Form::select('unit_id',$unitsel, old('unit_id'), ['class' => 'form-control','required'=>'required','id'=>'eunit_id']); !!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('weight','Вес, кг:',['class' => 'col-xs-3 control-label'])   !!}
                                <div class="col-xs-8">
                                    {!! Form::text('weight',old('weight'),['class' => 'form-control','placeholder'=>'Укажите вес','id'=>'eweight'])!!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('capacity','Объем, м3:',['class' => 'col-xs-3 control-label'])   !!}
                                <div class="col-xs-8">
                                    {!! Form::text('capacity',old('capacity'),['class' => 'form-control','placeholder'=>'Укажите объем','id'=>'ecapacity'])!!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('length','Длина, м:',['class' => 'col-xs-3 control-label'])   !!}
                                <div class="col-xs-8">
                                    {!! Form::text('length',old('length'),['class' => 'form-control','placeholder'=>'Укажите длину','id'=>'elength'])!!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('area','Площадь, м2:',['class' => 'col-xs-3 control-label'])   !!}
                                <div class="col-xs-8">
                                    {!! Form::text('area',old('area'),['class' => 'form-control','placeholder'=>'Укажите площадь','id'=>'earea'])!!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('vat','Ставка НДС, %:',['class' => 'col-xs-3 control-label'])   !!}
                                <div class="col-xs-8">
                                    {!! Form::text('vat',old('vat'),['class' => 'form-control','placeholder'=>'Укажите процент НДС','id'=>'evat'])!!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('gtd','Учет по ГДТ:',['class' => 'col-xs-3 control-label'])   !!}
                                <div class="col-xs-8">
                                    {!! Form::select('gtd',['0'=>'Нет','1'=>'Да'], old('gtd'), ['class' => 'form-control','id'=>'egtd']); !!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('barcode','Штрихкод:',['class' => 'col-xs-3 control-label'])   !!}
                                <div class="col-xs-8">
                                    {!! Form::text('barcode',old('barcode'),['class' => 'form-control','placeholder'=>'Введите штрихкод','maxlength'=>'100','id'=>'ebarcode'])!!}
                                </div>
                            </div>

                            {!! Form::close() !!}
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                            <button type="button" class="btn btn-primary" id="btn_save">Сохранить</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Edit Good Modal -->
            <div class="col-md-10">
                <div class="panel panel-white">
                    <div class="panel-body">
                        <div class="panel-body">
                            <button type="button" class="btn btn-primary btn-sm btn-o" data-toggle="modal"
                                    data-target="#newGood">
                                <i class="fa fa-plus" aria-hidden="true"></i>
                                Новый товар
                            </button>
                            <button type="button" class="btn btn-primary btn-sm btn-o" id="import" data-toggle="modal"
                                    data-target="#importGood">
                                <i class="fa fa-download" aria-hidden="true"></i>
                                Импорт
                            </button>
                            <a href="#">
                                <button type="button" class="btn btn-primary btn-sm btn-o" data-toggle="modal"
                                        data-target="#exportGood"><i class="fa fa-upload"
                                                                                              aria-hidden="true"></i>
                                    Экспорт
                                </button>
                            </a>
                            <p class="text-center panel-title panel-info" id="goods">Номенклатура</p>
                        </div>
                        <div class=" table-responsive">
                            <table id="mytable" class="table table-bordered table-full-width dataTable no-footer">
                                <thead>
                                <tr>
                                    <th>Группа</th>
                                    <th>Название</th>
                                    <th>Артикул</th>
                                    <th>Аналоги</th>
                                    <th>Производитель</th>
                                    <th>Модель</th>
                                    <th>Штрихкод</th>
                                    <th>Дата правки</th>
                                    <th>Действия</th>
                                </tr>
                                </thead>
                                <tbody id="tbody_goods">
                                @if($rows)
                                    @foreach($rows as $row)

                                        <tr id="row{{ $row->id }}">
                                            <td>{{ $row->group->title }}</td>
                                            <td>{{ $row->title }}</td>
                                            <td>{{ $row->vendor_code }}</td>
                                            <td>{{ $row->analog_code }}</td>
                                            <td>{{ $row->brand }}</td>
                                            <td>{{ $row->model }}</td>
                                            <td>{{ $row->barcode }}</td>
                                            <td>{{ $row->updated_at }}</td>
                                            <td style="width: 120px">
                                                <div class="form-group" role="group">
                                                    <button class="btn btn-success btn-sm row_edit" type="button"
                                                            data-toggle="modal" data-target="#editGood"
                                                            title="Редактировать запись"><i class="fa fa-edit fa-lg"
                                                                                            aria-hidden="true"></i>
                                                    </button>
                                                    <button class="btn btn-info btn-sm row_transfer" type="button"
                                                            title="Передать на сайт"><i class="fa fa-refresh fa-lg"
                                                                                      aria-hidden="true"></i></button>
                                                    <button class="btn btn-danger btn-sm row_delete" type="button"
                                                            title="Удалить запись"><i class="fa fa-trash fa-lg"
                                                                                      aria-hidden="true"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /page content -->
@endsection

@section('user_script')
    <script src="/js/jquery.dataTables.min.js"></script>
    <script src="/js/jstree.min.js"></script>
    @include('confirm')
    <script>
        let row_id;
        _loadData();
        let table = $('#mytable').DataTable({
            "aoColumnDefs": [{
                "aTargets": [0]
            }],
            "language": {
                "processing": "Подождите...",
                "search": "Поиск: ",
                "lengthMenu": "Показать _MENU_ записей",
                "info": "Записи с _START_ до _END_ из _TOTAL_ записей",
                "infoEmpty": "Записи с 0 до 0 из 0 записей",
                "infoFiltered": "(отфильтровано из _MAX_ записей)",
                "infoPostFix": "",
                "loadingRecords": "Загрузка записей...",
                "zeroRecords": "Записи отсутствуют.",
                "emptyTable": "В таблице отсутствуют данные",
                "paginate": {
                    "first": "Первая",
                    "previous": "Предыдущая",
                    "next": "Следующая",
                    "last": "Последняя"
                },
                "aria": {
                    "sortAscending": ": активировать для сортировки столбца по возрастанию",
                    "sortDescending": ": активировать для сортировки столбца по убыванию"
                },
                "select": {
                    "rows": {
                        "_": "Выбрано записей: %d",
                        "0": "Кликните по записи для выбора",
                        "1": "Выбрана одна запись"
                    }
                }
            },
            //"aaSorting" : [[1, 'asc']],
            "aLengthMenu": [[5, 10, 15, 20, -1], [5, 10, 15, 20, "Все"] // change per page values here
            ],
            // set the initial value
            "iDisplayLength": 10,
        });

        $('#btn_import').click(function (e) {
            e.preventDefault();
            let error = 0;
            $("#import_good").find(":input").each(function () {// проверяем каждое поле ввода в форме
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
                alert("Необходимо заполнять все доступные поля!");
                return false;
            } else {
                let formData = new FormData();
                formData.append('file', $('#file').prop("files")[0]);
                $.ajax({
                    type: 'POST',
                    url: '{{ route('importGood') }}',
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
                            _viewGoods($('#egood_id').val());
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

        $('#btn_new').click(function (e) {
            e.preventDefault();
            let error = 0;
            $("#new_good").find(":input").each(function () {// проверяем каждое поле ввода в форме
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
                    url: '{{ route('goodAdd') }}',
                    data: $('#new_good').serialize(),
                    success: function (res) {
                        //alert(res);
                        if (res == 'NO')
                            alert('Выполнение операции запрещено!');
                        else if (res == 'NO VALIDATE')
                            alert('Ошибки валидации данных формы!');
                        else if (res == 'ERR')
                            alert('При обновлении данных возникла ошибка!');
                        else {
                            /*let gtd = '';
                            if ($("#gtd option:selected").val() == '1')
                                gtd = '<span role="button" class="label label-success">Есть</span>';
                            else
                                gtd = '<span role="button" class="label label-danger">Нет</span>';*/
                            const addedRow = table.row.add([
                                $("#group_id option:selected").text(),
                                $('#title').val(),
                                $('#vendor_code').val(),
                                $('#analog_code').val(),
                                $('#brand').val(),
                                $('#model').val(),
                                $('#barcode').val(),
                                now(),
                                '<div class="form-group" role="group"><button class="btn btn-success btn-sm row_edit" type="button" data-toggle="modal" data-target="#editGood" title="Редактировать запись"><i class="fa fa-edit fa-lg" aria-hidden="true"></i></button>' +
                                '\n<button class="btn btn-info btn-sm row_transfer" type="button" title="Передать на сайт"><i class="fa fa-refresh fa-lg" aria-hidden="true"></i></button>' +
                                '\n<button class="btn btn-danger btn-sm row_delete" type="button" title="Удалить запись"><i class="fa fa-trash fa-lg" aria-hidden="true"></i></button></div>'
                            ]).draw();
                            const addedRowNode = addedRow.node();
                            $(addedRowNode).attr('id', 'row'+res);
                            $('#new_good')[0].reset();
                            /*$('input[type=text]').each(function () {
                                $(this).val('');
                            });*/
                            $('#vat').val('20');
                            $(".modal").modal("hide");
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        alert(xhr.status + '\n' + thrownError);
                    }
                });
            }
        });

        $(document).on({
            click: function (e) {
                e.preventDefault();
                $('#edit_good')[0].reset();
                let id = $(this).parent().parent().parent().attr("id");
                row_id = id;
                //alert('id=' + id);
                $.ajax({
                    type: 'POST',
                    url: '{{ route('findGood') }}',
                    data: {'id': id},
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        //alert(res);
                        let obj = jQuery.parseJSON(res);
                        if (typeof obj === 'object') {
                            $('#egood_id').val(obj.id);
                            $("#ecategory_id option[value='" + obj.category_id + "']").attr("selected", "selected");
                            $("#egroup_id option[value='" + obj.group_id + "']").attr("selected", "selected");
                            $('#etitle').val(obj.title);
                            $('#edescr').val(obj.descr);
                            $('#ebx_group').val(obj.bx_group);
                            $('#evendor_code').val(obj.vendor_code);
                            $('#eanalog_code').val(obj.analog_code);
                            $('#ebrand').val(obj.brand);
                            $('#emodel').val(obj.model);
                            $("#eunit_id option[value='" + obj.unit_id + "']").attr("selected", "selected");
                            $('#eweight').val(obj.weight);
                            $('#ecapacity').val(obj.capacity);
                            $('#elength').val(obj.length);
                            $('#earea').val(obj.area);
                            $('#evat').val(obj.vat);
                            $("#egtd option[value='" + obj.gtd + "']").attr("selected", "selected");
                            $('#ebarcode').val(obj.barcode);
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        alert(xhr.status);
                        alert(thrownError);
                    }
                });

            }
        }, ".row_edit");

        $(document).on({
            click: function (e) {
                e.preventDefault();
                var id = $(this).parent().parent().parent().attr("id");
                //alert('id=' + id);
                    $.ajax({
                        type: 'POST',
                        url: '{{ route('transferGood') }}',
                        data: {'id': id},
                        headers: {
                            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (res) {
                            //alert(res);
                            if(res=='OK'){
                                alert('Запись на сайте успешно обновлена!');
                            }
                            if(res=='ERR'){
                                alert('В процессе синхронизации данных на сайт произошла ошибка!');
                            }
                            if(res=='NOT'){
                                alert('У вас нет прав для выполнения операции выгрузки на сайт!');
                            }
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            alert(xhr.status);
                            alert(thrownError);
                        }
                    });
            }
        }, ".row_transfer");

        $(document).on({
            click: function (e) {
                e.preventDefault();
                var id = $(this).parent().parent().parent().attr("id");
                //alert('id=' + id);
                let x = confirm("Выбранная запись будет удалена. Продолжить (Да/Нет)?");
                if (x) {
                    alert('Не забудьте удалить эту номенклатуру на сайте. Удаление на сайте возможно только вручную!');
                    $.ajax({
                        type: 'POST',
                        url: '{{ route('delGood') }}',
                        data: {'id': id},
                        headers: {
                            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (res) {
                            //alert(res);
                            if(res=='OK'){
                                $('#' + id).hide();
                                _viewGoods($('#egood_id').val());
                            }
                            if(res=='NO'){
                                alert('У вас нет прав для выполнения операции удаления!');
                            }
                            if(res=='ERR'){
                                alert('В процессе удаления произошла ошибка!');
                            }
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
        }, ".row_delete");

        $('#btn_save').click(function (e) {
            e.preventDefault();
            let error = 0;
            $("#edit_good").find(":input").each(function () {// проверяем каждое поле ввода в форме
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
                    url: '{{ route('editGood') }}',
                    data: $('#edit_good').serialize(),
                    success: function (res) {
                        //alert(res);
                        if (res == 'NO')
                            alert('Выполнение операции запрещено!');
                        else if (res == 'NO VALIDATE')
                            alert('Ошибки валидации данных формы!');
                        else if (res == 'ERR')
                            alert('При обновлении данных возникла ошибка!');
                        else {
                            //_viewGoods(res);
                            $(".modal").modal("hide");
                            $('#'+row_id).children('td').eq(0).text($("#egroup_id option:selected").text());
                            $('#'+row_id).children('td').eq(1).text($('#etitle').val());
                            $('#'+row_id).children('td').eq(2).text($('#evendor_code').val());
                            $('#'+row_id).children('td').eq(3).text($('#eanalog_code').val());
                            $('#'+row_id).children('td').eq(4).text($('#ebrand').val());
                            $('#'+row_id).children('td').eq(5).text($('#emodel').val());
                            $('#'+row_id).children('td').eq(6).text($('#ebarcode').val());
                            $('#'+row_id).children('td').eq(7).text(now());
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        alert(xhr.status + '\n' + thrownError);
                    }
                });
                $.modal.close();
            }
        });

        function now(){
            Data = new Date();
            Year = Data.getFullYear();
            Month = Data.getMonth()+1;
            Day = Data.getDate();
            Hour = Data.getHours();
            Minutes = Data.getMinutes();
            Seconds = Data.getSeconds();
            return Year+'-'+Month+'-'+Day+' '+Hour+':'+Minutes+':'+Seconds;
        }

        // Загрузка категорий с сервера
        function _loadData() {
            let params = {
                action: 'get_categories'
            };

            $.ajax({
                url: '{{ route('viewCategories') }}',
                method: 'POST',
                data: params,
                dataType: 'json',
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (resp) {
                    //alert(resp.result);
                    // Инициализируем дерево категорий
                    if (resp.code === 'success') {
                        _initTree(resp.result);
                    } else {
                        alert('Ошибка получения данных с сервера: ', resp.message);
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(xhr.status);
                    alert(thrownError);
                }
            });
        }

        // Инициализация дерева категорий с помощью jstree
        function _initTree(data) {
            let ui = {
                $categories: $('#categories'),
                $goods: $('#goods')
            };
            ui.$categories.jstree({
                core: {
                    themes: {
                        "responsive": false
                    },
                    check_callback: true,
                    multiple: false,
                    data: data
                },
                types: {
                    "default": {
                        "icon": "fa fa-folder text-primary fa-lg"
                    },
                    "file": {
                        "icon": "fa fa-file text-primary fa-lg"
                    }
                },
                plugins: ["state", "contextmenu", "wholerow", "dnd", "types"],
            }).bind('changed.jstree', function (e, data) {
                let category = data.node.id;
                ui.$goods.html('Товары из категории ' + data.node.text);
                //console.log('node data: ', data);
                //загружаем товары категории
                _viewGoods(category);
            }).bind('move_node.jstree', function (e, data) {
                let params = {
                    id: +data.node.id,
                    old_parent: +data.old_parent,
                    new_parent: +data.parent,
                    old_position: +data.old_position,
                    new_position: +data.position
                };
                _moveCategory(params);
                //console.log('move_node params', params);
            }).bind('create_node.jstree', function (e, data) {
                //console.log('data=', data.node.text);
                let params = {
                    id: +data.node.parent,
                    position: +data.position,
                    text: data.node.text,
                };
                //_createCategory(params);
                let dat = $.extend(params, {
                    action: 'create_category'
                });

                $.ajax({
                    url: '{{ route('viewCategories') }}',
                    method: 'POST',
                    data: dat,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (resp) {
                        //alert(resp);
                        let obj = $.parseJSON(resp.result);
                        //console.log('resp=', obj.id);
                        if (resp.code === 'success')
                            data.instance.set_id(data.node, obj.id);
                    },
                    error: function () {
                        data.instance.refresh();
                    }
                });
            }).bind('rename_node.jstree', function (e, data) {
                //console.log('data=', data);
                let params = {
                    id: +data.node.id,
                    text: data.text,
                };
                _renameCategory(params, data);
            }).bind('delete_node.jstree', function (e, data) {
                //console.log('data=', data);
                let params = {
                    id: +data.node.id,
                };
                _deleteCategory(params, data);
            }).bind('loaded.jstree', function (e, data) {
                //console.log('data=', data);
                data.instance.select_node([{{ $node }}, {{ $sub }}]); //node ids that you want to check
            });
        }

        //Вывод товаров выбранной категории
        function _viewGoods(id) {
            $('#loader').show();
            $('#egood_id').val(id);
            $.ajax({
                type: 'POST',
                url: '{{ route('viewGood') }}',
                data: {'id': id},
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    //alert(res);
                    if (res != 'NODATA') {
                        $('#mytable').DataTable().destroy();
                        $('#mytable').find('tbody').html(res);
                        $('#mytable').DataTable({
                            "aoColumnDefs": [{
                                "aTargets": [0]
                            }],
                            "language": {
                                "processing": "Подождите...",
                                "search": "Поиск: ",
                                "lengthMenu": "Показать _MENU_ записей",
                                "info": "Записи с _START_ до _END_ из _TOTAL_ записей",
                                "infoEmpty": "Записи с 0 до 0 из 0 записей",
                                "infoFiltered": "(отфильтровано из _MAX_ записей)",
                                "infoPostFix": "",
                                "loadingRecords": "Загрузка записей...",
                                "zeroRecords": "Записи отсутствуют.",
                                "emptyTable": "В таблице отсутствуют данные",
                                "paginate": {
                                    "first": "Первая",
                                    "previous": "Предыдущая",
                                    "next": "Следующая",
                                    "last": "Последняя"
                                },
                                "aria": {
                                    "sortAscending": ": активировать для сортировки столбца по возрастанию",
                                    "sortDescending": ": активировать для сортировки столбца по убыванию"
                                },
                                "select": {
                                    "rows": {
                                        "_": "Выбрано записей: %d",
                                        "0": "Кликните по записи для выбора",
                                        "1": "Выбрана одна запись"
                                    }
                                }
                            },
                            //"aaSorting" : [[1, 'asc']],
                            "aLengthMenu": [[5, 10, 15, 20, -1], [5, 10, 15, 20, "Все"] // change per page values here
                            ],
                            // set the initial value
                            "iDisplayLength": 10,
                        }).draw();
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(xhr.status);
                    alert(thrownError);
                }
            });
            $('#loader').hide();
        }

        //Переименование категории
        function _renameCategory(params, dat) {
            let data = $.extend(params, {
                action: 'rename_category'
            });

            $.ajax({
                url: '{{ route('viewCategories') }}',
                method: 'POST',
                data: data,
                dataType: 'json',
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (resp) {
                    //alert(resp);
                },
                error: function () {
                    dat.instance.refresh();
                }
            });
        }

        //Удаление категории
        function _deleteCategory(params, dat) {
            let data = $.extend(params, {
                action: 'delete_category'
            });

            $.ajax({
                url: '{{ route('viewCategories') }}',
                method: 'POST',
                data: data,
                dataType: 'json',
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (resp) {
                    //alert(resp);
                },
                error: function () {
                    dat.instance.refresh();
                }
            });
        }

        // Перемещение категории
        function _moveCategory(params) {
            let data = $.extend(params, {
                action: 'move_category'
            });

            $.ajax({
                url: '{{ route('viewCategories') }}',
                method: 'POST',
                data: data,
                dataType: 'json',
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (resp) {
                    if (resp.code !== 'success')
                        alert('Ошибка получения данных с сервера: ', resp.message);
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(xhr.status);
                    alert(thrownError);
                }
            });
        }

    </script>
@endsection
