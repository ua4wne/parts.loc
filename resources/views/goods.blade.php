@extends('layouts.main')

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
            <div class="col-md-3">
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
            <div class="col-md-9">
                <div class="panel panel-white">
                    <div class="panel-heading">
                        <p class="panel-title" id="goods">Номенклатура</p>
                    </div>
                    <div class="panel-body">
                        таблица номенклатуры
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /page content -->
@endsection

@section('user_script')
    <script src="/js/jstree.min.js"></script>
    @include('confirm')
    <script>
        _loadData();
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
                success: function(resp) {
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
                    themes : {
                        "responsive" : false
                    },
                    check_callback: true,
                    multiple: false,
                    data: data
                },
                plugins : ["state","contextmenu","wholerow", "dnd"],
            }).bind('changed.jstree', function(e, data) {
                let category = data.node.text;
                ui.$goods.html('Товары из категории ' + category);
                //console.log('node data: ', data);
            }).bind('move_node.jstree', function(e, data) {
                let params = {
                    id: +data.node.id,
                    old_parent: +data.old_parent,
                    new_parent: +data.parent,
                    old_position: +data.old_position,
                    new_position: +data.position
                };
                _moveCategory(params);
                //console.log('move_node params', params);
            }).bind('create_node.jstree', function(e, data) {
                //console.log('data=', data.node.text);
                let params = {
                    id: +data.node.parent,
                    position: +data.position,
                    text: data.node.text,
                };
                _createCategory(params);
            }).bind('rename_node.jstree', function(e, data) {
                //console.log('data=', data);
                let params = {
                    id: +data.node.id,
                    text: data.text,
                };
                _renameCategory(params);
            }).bind('delete_node.jstree', function(e, data) {
                console.log('data=', data);
                /*let params = {
                    id: +data.node.id,
                };
                _deleteCategory(params);*/
            });
        }

        //Создание новой категории
        function _createCategory(params) {
            let data = $.extend(params, {
                action: 'create_category'
            });

            $.ajax({
                url: '{{ route('viewCategories') }}',
                method: 'POST',
                data: data,
                dataType: 'json',
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(resp) {
                    //alert(resp);
                    if (resp.code === 'success')
                        params.instance.set_id(params.node, resp.id);
                },
                error: function () {
                    params.instance.refresh();
                }
            });
        }

        //Переименование категории
        function _renameCategory(params) {
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
                success: function(resp) {
                    //alert(resp);
                },
                error: function () {
                    params.instance.refresh();
                }
            });
        }

        //Удаление категории
        function _deleteCategory(params) {
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
                success: function(resp) {
                    //alert(resp);
                },
                error: function () {
                    params.instance.refresh();
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
                success: function(resp) {
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
