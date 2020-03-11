@extends('layouts.main')

@section('dashboard')

@endsection
@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li><a href="{{ route('orgs') }}">{{ $title }}</a></li>
        <li class="active">{{ $head }}</li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- page content -->
    <div class="container-fluid container-fullw bg-white">
        <div class="row">
            <div class="panel-heading">
                <a href="{{route('orgEdit',['id'=>$model->id])}}">
                    <button type="button" class="btn btn-primary btn-sm btn-o"><i class="fa fa-edit"
                                                                                  aria-hidden="true"></i> Изменить данные
                    </button>
                </a>
            </div>
            <div class="col-md-10">
                <div class="tabbable pills">
                    <ul id="myTab3" class="nav nav-pills">
                        <li class="active">
                            <a href="#common" data-toggle="tab">
                                Общие
                            </a>
                        </li>
                        <li>
                            <a href="#contacts" data-toggle="tab">
                                Контакты
                            </a>
                        </li>
                        <li>
                            <a href="#accounts" data-toggle="tab">
                                Реквизиты
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade in active" id="common">
                            <table class="table table-bordered table-hover">
                                <tr>
                                    <th>Организационная форма</th>
                                    <td>{{ $model->org_form->title }}</td>
                                </tr>
                                <tr>
                                    <th>Полное название</th>
                                    <td>{{ $model->title }}</td>
                                </tr>
                                <tr>
                                    <th>Название для документов</th>
                                    <td>{{ $model->print_name }}</td>
                                </tr>
                                <tr>
                                    <th>Короткое название</th>
                                    <td>{{ $model->short_name }}</td>
                                </tr>
                                <tr>
                                    <th>Префикс для документов</th>
                                    <td>{{ $model->prefix }}</td>
                                </tr>
                                <tr>
                                    <th>Статус</th>
                                    <td>
                                        @if($model->status)
                                            Действующая
                                        @else
                                            Не действующая
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="tab-pane fade" id="contacts">
                            <div class="tab-pane fade in active" id="common">
                                <table class="table table-bordered table-hover">
                                    <tr>
                                        <th>Юридический адрес</th>
                                        <td>{{ $model->legal_address }}</td>
                                    </tr>
                                    <tr>
                                        <th>Почтовый адрес</th>
                                        <td>{{ $model->post_address }}</td>
                                    </tr>
                                    <tr>
                                        <th>Телефон</th>
                                        <td>{{ $model->phone }}</td>
                                    </tr>
                                    <tr>
                                        <th>E-mail</th>
                                        <td>{{ $model->email }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="accounts">
                            <table class="table table-bordered table-hover">
                                <tr>
                                    <th>ИНН</th>
                                    <td>{{ $model->inn }}</td>
                                </tr>
                                <tr>
                                    <th>КПП</th>
                                    <td>{{ $model->kpp }}</td>
                                </tr>
                                <tr>
                                    <th>ОГРН</th>
                                    <td>{{ $model->ogrn }}</td>
                                </tr>
                                <tr>
                                    <th>Расчетный счет</th>
                                    <td>{{ $model->account }}</td>
                                </tr>
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

@endsection
