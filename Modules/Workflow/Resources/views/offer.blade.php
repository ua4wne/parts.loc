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
        <li><a href="{{ route('firmView',['id'=>$id]) }}">{{ $firm }}</a></li>
        <li class="active"><a href="{{ route('offer',['id'=>$id]) }}">{{ $title }}</a></li>
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
                <a href="{{route('offerAdd',['id'=>$id])}}">
                    <button type="button" class="btn btn-primary btn-sm btn-o"><i class="fa fa-plus"
                                                                                  aria-hidden="true"></i> Новая
                        запись
                    </button>
                </a>
                <a href="#">
                    <button type="button" class="btn btn-primary btn-sm btn-o" id="import" data-toggle="modal"
                            data-target="#importDoc">
                        <i class="fa fa-file-excel-o" aria-hidden="true"></i> Загрузить из файла
                    </button>
                </a>
            </div>
            <div class="col-md-12">
                <div class=" table-responsive">
                    @if($rows)
                        <table id="mytable" class="table table-bordered">
                            <thead>
                            <tr>
                                <th>Номенклатура</th>
                                <th>Цена</th>
                                <th>Наценка</th>
                                <th>Валюта</th>
                                <th>Ед. изм</th>
                                <th>Срок поставки</th>
                                <th>Комментарий</th>
                                <th>Действия</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($rows as $k => $row)
                                <tr id="{{ $row->id }}">
                                    <td>{{ $row->good->title }}</td>
                                    <td>{{ $row->price }}</td>
                                    <td>{{ $row->markup }}</td>
                                    <td>{{ $row->currency->title }}</td>
                                    <td>{{ $row->unit->title }}</td>
                                    <td>{{ $row->delivery_time }}</td>
                                    <td>{{ $row->comment }}</td>
                                    <td style="width:100px;">
                                        <div class="form-group" role="group">
                                            <button class="btn btn-info btn-sm pos_edit"
                                                    type="button" title="Редактировать позицию"><i
                                                    class="fa fa-edit fa-lg"
                                                    aria-hidden="true"></i>
                                            </button>
                                            <button class="btn btn-danger btn-sm pos_delete"
                                                    type="button" title="Удалить позицию"><i
                                                    class="fa fa-trash fa-lg"
                                                    aria-hidden="true"></i>
                                            </button>
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
    @include('confirm')

@endsection
