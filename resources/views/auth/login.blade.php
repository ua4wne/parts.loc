@extends('layouts.app')

@section('content')
    @if (session('status'))
        <div class="row">
            <div class="alert alert-success panel-remove">
                <a href="#" class="close" data-dismiss="alert">&times;</a>
                {{ session('status') }}
            </div>
        </div>
    @endif
    <div class="main-login col-xs-10 col-xs-offset-1 col-sm-8 col-sm-offset-2 col-md-4 col-md-offset-4">
        <!-- start: LOGIN BOX -->
        <div class="box-login margin-top-30">
            {!! Form::open(['url' => route('login'),'class'=>'form-login','method'=>'POST']) !!}
                <fieldset>
                    <legend>
                        Авторизация
                    </legend>
                    <p>
                        Для входа введите свой логин и пароль.
                    </p>
                    <div class="form-group">
                            <span class="input-icon">
                            {!! Form::text('login',old('login'),['class' => 'form-control','placeholder'=>'Ваш логин','required'=>'required'])!!}
                            <i class="fa fa-user fa-lg"></i></span>
                    </div>
                    <div class="form-group form-actions">
                            <span class="input-icon">
                            {!! Form::password('password',['class' => 'form-control password','placeholder'=>'Ваш пароль','required'=>'required'])!!}
                            <i class="fa fa-key fa-lg"></i>
									<a class="forgot" href="#">
										Забыл пароль
									</a>
                            </span>
                    </div>
                    <div class="form-actions">
                        {!! Form::button('Войти в систему <i class="fa fa-sign-in fa-lg" aria-hidden="true"></i>', ['class' => 'btn btn-primary pull-right','type'=>'submit']) !!}
                    </div>
                </fieldset>
            {!! Form::close() !!}
        </div>
        <!-- end: LOGIN BOX -->
    </div>
@endsection
