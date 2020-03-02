@extends('layouts.main')

@section('dashboard')

@endsection
@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li><a href="{{ URL::previous() }}">Назад</a></li>
        <li class="active">Страница не найдена</li>
    </ul>
    <!-- END BREADCRUMB -->
    <div class="error-full-page">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 page-error">
                    <div class="error-number text-azure">
                        404
                    </div>
                    <div class="error-details col-sm-6 col-sm-offset-3">
                        <h3>Опаньки. Что-то пошло не так!</h3>
                        <p>Такой страницы не существует. Попробуйте ввести правильную ссылку.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- page content -->

@endsection

@section('user_script')

@endsection
