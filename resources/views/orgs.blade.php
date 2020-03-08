@extends('layouts.main')

@section('dashboard')

@endsection
@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li class="active"><a href="{{ route('orgs') }}">{{ $title }}</a></li>
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
                <a href="{{route('orgAdd')}}">
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
                                <th>Форма</th>
                                <th>Наименование</th>
                                <th>Полное название</th>
                                <th>ИНН</th>
                                <th>ОГРН</th>
                                <th>Юридический адрес</th>
                                <th>Статус</th>
                                <th>Действия</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($rows as $k => $row)
                                <tr>
                                    <td>{{ $row->org_form->name }}</td>
                                    <td>{{ $row->short_name }}</td>
                                    <td>{{ $row->title }}</td>
                                    <td>{{ $row->inn }}</td>
                                    <td>{{ $row->ogrn }}</td>
                                    <td>{{ $row->legal_address }}</td>
                                    <td>{{ $row->status }}</td>
                                    <td style="width:110px;">
                                        <div class="form-group" role="group">
                                            <button class="btn btn-info btn-sm view_org" type="button"
                                                    data-toggle="modal" data-target="#viewOrg"
                                                    title="Карточка организации"><i class="fa fa-eye fa-lg"
                                                                               aria-hidden="true"></i></button>
                                            <button class="btn btn-success btn-sm org_edit" type="button"
                                                    data-toggle="modal" data-target="#editOrg"
                                                    title="Редактировать запись"><i class="fa fa-edit fa-lg"
                                                                                    aria-hidden="true"></i>
                                            </button>
                                            <button class="btn btn-danger btn-sm org_delete" type="button"
                                                    title="Удалить запись"><i class="fa fa-trash fa-lg"
                                                                              aria-hidden="true"></i></button>
                                        </div>
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
