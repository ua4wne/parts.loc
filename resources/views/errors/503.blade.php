@extends('layouts.main')

@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li class="active">Доступ запрещен</li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- page content -->
    <div class="col-sm-12 page-error">
        <div class="error-number text-red">
            500
        </div>
        <div class="error-details col-sm-6 col-sm-offset-3">
            @if(empty($exception->getMessage()))
                <h3 class="text-center">Доступ к странице запрещен!</h3>
            @else
                <h3 class="text-center">{{ $exception->getMessage() }}</h3>
            @endif
        </div>
    </div>

@endsection

@section('user_script')

@endsection
