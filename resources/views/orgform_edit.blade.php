@extends('layouts.main')

@section('dashboard')

@endsection
@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li><a href="{{ route('orgforms') }}">{{ $title }}</a></li>
        <li class="active">{{ $head }}</li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- page content -->
    <div class="container-fluid container-fullw bg-white">
        <div class="row">
            <h2 class="text-center">{{ $head }}</h2>
            {!! Form::open(['url' => route('orgformEdit',['id'=>$data['id']]),'class'=>'form-horizontal','method'=>'POST']) !!}

            <div class="form-group">
                {!! Form::label('nameRU','Название (РУС):',['class' => 'col-xs-2 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::text('nameRU',$data['nameRU'],['class' => 'form-control','placeholder'=>'Введите аббревиатуру','size'=>'5','required'=>'required'])!!}
                    {!! $errors->first('nameRU', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('nameEN','Название (EN):',['class' => 'col-xs-2 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::text('nameEN',$data['nameEN'],['class' => 'form-control','placeholder'=>'Введите международное название','size'=>'5'])!!}
                    {!! $errors->first('nameEN', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('title','Полное наименование:',['class' => 'col-xs-2 control-label'])   !!}
                <div class="col-xs-8">
                    {!! Form::text('title',$data['title'],['class' => 'form-control','placeholder'=>'Введите полное название','size'=>'100','required'=>'required'])!!}
                    {!! $errors->first('title', '<p class="text-danger">:message</p>') !!}
                </div>
            </div>

            <div class="form-group">
                <div class="col-xs-offset-2 col-xs-10">
                    {!! Form::button('Сохранить', ['class' => 'btn btn-primary','type'=>'submit']) !!}
                </div>
            </div>

            {!! Form::close() !!}
        </div>
    </div>
    <!-- /page content -->
@endsection

@section('user_script')

@endsection
