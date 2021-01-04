@extends('layouts.main')

@section('dashboard')

@endsection
@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li class="active"><a href="{{ route('wh_corrects') }}">{{ $title }}</a></li>
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
                <a href="{{route('wh_correctsAdd')}}">
                    <button type="button" class="btn btn-primary btn-sm btn-o"><i class="fa fa-plus"
                                                                                  aria-hidden="true"></i> Новая
                        запись
                    </button>
                </a>
            </div>
            <div class="col-md-12">
                <div class=" table-responsive">
                    @if($rows)
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>Статус</th>
                                <th>Дата создания</th>
                                <th>№ документа</th>
                                <th>Склад</th>
                                <th>Основание</th>
                                <th>Ответственный</th>
                                <th>Действия</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($rows as $k => $row)
                                @if($row->status)
                                    <tr>
                                @else
                                    <tr class="success">
                                        @endif
                                        @if($row->status)
                                            <td class="text-center"><i class="fa fa-unlock fa-2x"
                                                                       aria-hidden="true"></i></td>
                                        @else
                                            <td class="text-center"><i class="fa fa-lock fa-2x" aria-hidden="true"></i>
                                            </td>
                                        @endif
                                        <td>{{ $row->created_at }}</td>
                                        <td>{{ $row->doc_num }}</td>
                                        <td>{{ $row->warehouse->title }}</td>
                                        <td>{{ $row->reason }}</td>
                                        <td>{{ $row->user->name }}</td>
                                        <td style="width:150px;">
                                            {!! Form::open(['url'=>route('wh_correctEdit',['id'=>$row->id]), 'class'=>'form-inline','method' => 'POST', 'onsubmit' => 'return confirmDelete()']) !!}
                                            {{ method_field('DELETE') }}
                                            <div class="form-group" role="group">
                                                <a href="{{route('wh_correctsView',['id'=>$row->id])}}">
                                                    <button class="btn btn-info" type="button"
                                                            title="Открыть документ"><i class="fa fa-eye fa-lg>"
                                                                                        aria-hidden="true"></i></button>
                                                </a>
                                                @if($row->status)
                                                    <a href="{{route('wh_correctEdit',['id'=>$row->id])}}">
                                                        <button class="btn btn-success" type="button"
                                                                title="Редактировать запись"><i
                                                                class="fa fa-edit fa-lg>"
                                                                aria-hidden="true"></i></button>
                                                    </a>
                                                    {!! Form::button('<i class="fa fa-trash-o fa-lg>" aria-hidden="true"></i>',['class'=>'btn btn-danger','type'=>'submit','title'=>'Удалить запись']) !!}
                                                @endif
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
@endsection
