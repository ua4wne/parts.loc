<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <!-- start: META -->
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <!--[if IE]><meta http-equiv='X-UA-Compatible' content="IE=edge,IE=9,IE=8,chrome=1" /><![endif]-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
    <link rel="icon" href="favicon.ico" type="image/ico"/>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? '' }}</title>
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <!-- end: META -->
    <!-- start: GOOGLE FONTS -->
    <link href="http://fonts.googleapis.com/css?family=Lato:300,400,400italic,600,700|Raleway:300,400,500,600,700|Crete+Round:400italic" rel="stylesheet" type="text/css" />
    <!-- end: GOOGLE FONTS -->
    <!-- start: MAIN CSS -->
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/font-awesome.min.css">
    <link rel="stylesheet" href="/themify-icons/themify-icons.min.css">
    <link href="/css/animate.min.css" rel="stylesheet" media="screen">
    <link href="/css/perfect-scrollbar.min.css" rel="stylesheet" media="screen">
    <link href="/css/switchery.min.css" rel="stylesheet" media="screen">
    <!-- end: MAIN CSS -->
    <!-- start: CLIP-TWO CSS -->
    <link rel="stylesheet" href="/css/styles.css">
    <link rel="stylesheet" href="/css/plugins.css">
    <link rel="stylesheet" href="/css/themes/theme-1.css" id="skin_color" />
    <link href="/css/jstree/themes/default/style.min.css" rel="stylesheet" media="screen">
    <!-- end: CLIP-TWO CSS -->
    <!-- start: CSS REQUIRED FOR THIS PAGE ONLY -->
    <!-- end: CSS REQUIRED FOR THIS PAGE ONLY -->
</head>
<!-- end: HEAD -->
<body>
<div id="app">
    <!-- sidebar -->
    <div class="sidebar app-aside" id="sidebar">
        <div class="sidebar-container perfect-scrollbar">
            <nav>
                <!-- start: SEARCH FORM -->
                <div class="search-form">
                    <a class="s-open" href="#">
                        <i class="ti-search"></i>
                    </a>
                    <form class="navbar-form" role="search">
                        <a class="s-remove" href="#" target=".navbar-form">
                            <i class="ti-close"></i>
                        </a>
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Search...">
                            <button class="btn search-button" type="submit">
                                <i class="ti-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <!-- end: SEARCH FORM -->
                <!-- start: MAIN NAVIGATION MENU -->
                @section('left_menu')
                <ul class="main-navigation-menu">
                    <li class="active open">
                        <a href="/">
                            <div class="item-content">
                                <div class="item-media">
                                    <i class="ti-home"></i>
                                </div>
                                <div class="item-inner">
                                    <span class="title"> Главная панель </span>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:void(0)">
                            <div class="item-content">
                                <div class="item-media">
                                    <i class="ti-book"></i>
                                </div>
                                <div class="item-inner">
                                    <span class="title"> Справочники </span><i class="icon-arrow"></i>
                                </div>
                            </div>
                        </a>
                        <ul class="sub-menu">
                            <li><a href="{{ route('goods') }}"><span class="title"> Номенклатура </span></a></li>
                            <li><a href="#"><span class="title"> Единицы измерений </span></a></li>
                            <li><a href="{{ route('orgs') }}"><span class="title"> Организации </span></a></li>
                            <li><a href="#"><span class="title"> Контрагенты </span></a></li>
                            <li><a href="#"><span class="title"> Валюты </span></a></li>
                            <li><a href="{{ route('orgforms') }}"><span class="title"> Организационные формы </span></a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0)">
                            <div class="item-content">
                                <div class="item-media">
                                    <i class="ti-dropbox"></i>
                                </div>
                                <div class="item-inner">
                                    <span class="title"> Складской учет </span><i class="icon-arrow"></i>
                                </div>
                            </div>
                        </a>
                        <ul class="sub-menu">
                            <li><a href="#"><span class="title">Склады</span></a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0)">
                            <div class="item-content">
                                <div class="item-media">
                                    <i class="ti-user"></i>
                                </div>
                                <div class="item-inner">
                                    <span class="title"> Кадровый учет </span><i class="icon-arrow"></i>
                                </div>
                            </div>
                        </a>
                        <ul class="sub-menu">
                            <li><a href="{{ route('personals') }}"><span class="title"> Сотрудники </span></a></li>
                            <li><a href="{{ route('positions') }}"><span class="title"> Должности </span></a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0)">
                            <div class="item-content">
                                <div class="item-media">
                                    <i class="ti-folder"></i>
                                </div>
                                <div class="item-inner">
                                    <span class="title"> 3 Level Menu </span><i class="icon-arrow"></i>
                                </div>
                            </div>
                        </a>
                        <ul class="sub-menu">
                            <li>
                                <a href="javascript:;">
                                    <span>Item 1</span> <i class="icon-arrow"></i>
                                </a>
                                <ul class="sub-menu">
                                    <li>
                                        <a href="#">
                                            Sample Link 1
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            Sample Link 2
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            Sample Link 3
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="javascript:;">
                                    <span>Item 2</span> <i class="icon-arrow"></i>
                                </a>
                                <ul class="sub-menu">
                                    <li>
                                        <a href="#">
                                            Sample Link 1
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            Sample Link 2
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            Sample Link 3
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="javascript:;">
                                    <span>Item 3</span> <i class="icon-arrow"></i>
                                </a>
                                <ul class="sub-menu">
                                    <li>
                                        <a href="#">
                                            Sample Link 1
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            Sample Link 2
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            Sample Link 3
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript:void(0)">
                            <div class="item-content">
                                <div class="item-media">
                                    <i class="ti-menu-alt"></i>
                                </div>
                                <div class="item-inner">
                                    <span class="title"> 4 Level Menu </span><i class="icon-arrow"></i>
                                </div>
                            </div>
                        </a>
                        <ul class="sub-menu">
                            <li>
                                <a href="javascript:;">
                                    <span>Item 1</span> <i class="icon-arrow"></i>
                                </a>
                                <ul class="sub-menu">
                                    <li>
                                        <a href="javascript:;">
                                            <span>Sample Link 1</span> <i class="icon-arrow"></i>
                                        </a>
                                        <ul class="sub-menu">
                                            <li>
                                                <a href="#">
                                                    Sample Link 1
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#">
                                                    Sample Link 2
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#">
                                                    Sample Link 3
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a href="javascript:;">
                                            <span>Sample Link 2</span> <i class="icon-arrow"></i>
                                        </a>
                                        <ul class="sub-menu">
                                            <li>
                                                <a href="#">
                                                    Sample Link 1
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#">
                                                    Sample Link 2
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#">
                                                    Sample Link 3
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a href="javascript:;">
                                            <span>Sample Link 3</span> <i class="icon-arrow"></i>
                                        </a>
                                        <ul class="sub-menu">
                                            <li>
                                                <a href="#">
                                                    Sample Link 1
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#">
                                                    Sample Link 2
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#">
                                                    Sample Link 3
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="javascript:;">
                                    <span>Item 2</span> <i class="icon-arrow"></i>
                                </a>
                                <ul class="sub-menu">
                                    <li>
                                        <a href="javascript:;">
                                            <span>Sample Link 1</span> <i class="icon-arrow"></i>
                                        </a>
                                        <ul class="sub-menu">
                                            <li>
                                                <a href="#">
                                                    Sample Link 1
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#">
                                                    Sample Link 2
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#">
                                                    Sample Link 3
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a href="javascript:;">
                                            <span>Sample Link 2</span> <i class="icon-arrow"></i>
                                        </a>
                                        <ul class="sub-menu">
                                            <li>
                                                <a href="#">
                                                    Sample Link 1
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#">
                                                    Sample Link 2
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#">
                                                    Sample Link 3
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a href="javascript:;">
                                            <span>Sample Link 3</span> <i class="icon-arrow"></i>
                                        </a>
                                        <ul class="sub-menu">
                                            <li>
                                                <a href="#">
                                                    Sample Link 1
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#">
                                                    Sample Link 2
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#">
                                                    Sample Link 3
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="javascript:;">
                                    <span>Item 3</span> <i class="icon-arrow"></i>
                                </a>
                                <ul class="sub-menu">
                                    <li>
                                        <a href="javascript:;">
                                            <span>Sample Link 1</span> <i class="icon-arrow"></i>
                                        </a>
                                        <ul class="sub-menu">
                                            <li>
                                                <a href="#">
                                                    Sample Link 1
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#">
                                                    Sample Link 2
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#">
                                                    Sample Link 3
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a href="javascript:;">
                                            <span>Sample Link 2</span> <i class="icon-arrow"></i>
                                        </a>
                                        <ul class="sub-menu">
                                            <li>
                                                <a href="#">
                                                    Sample Link 1
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#">
                                                    Sample Link 2
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#">
                                                    Sample Link 3
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a href="javascript:;">
                                            <span>Sample Link 3</span> <i class="icon-arrow"></i>
                                        </a>
                                        <ul class="sub-menu">
                                            <li>
                                                <a href="#">
                                                    Sample Link 1
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#">
                                                    Sample Link 2
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#">
                                                    Sample Link 3
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="maps.html">
                            <div class="item-content">
                                <div class="item-media">
                                    <i class="ti-location-pin"></i>
                                </div>
                                <div class="item-inner">
                                    <span class="title"> Maps </span>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <div class="item-content">
                                <div class="item-media">
                                    <i class="ti-pie-chart"></i>
                                </div>
                                <div class="item-inner">
                                    <span class="title"> Отчеты </span>
                                </div>
                            </div>
                        </a>
                    </li>
                </ul>
                @show
                <!-- end: MAIN NAVIGATION MENU -->
                <!-- start: CORE FEATURES -->
                @if(\App\User::hasRole('admin'))
                <ul class="main-navigation-menu">
                    <li>
                        <a href="javascript:void(0)">
                            <div class="item-content">
                                <div class="item-media">
                                    <i class="ti-settings"></i>
                                </div>
                                <div class="item-inner">
                                    <span class="title"> Настройки </span><i class="icon-arrow"></i>
                                </div>
                            </div>
                        </a>
                        <ul class="sub-menu">
                            <li><a href="{{ route('users') }}"><span class="title">Пользователи</span></a></li>
                            <li><a href="{{ route('roles') }}"><span class="title">Роли</span></a></li>
                            <li><a href="{{ route('actions') }}"><span class="title">Разрешения</span></a></li>
                        </ul>
                    </li>
                </ul>
                @endif
                <!-- end: CORE FEATURES -->
            </nav>
        </div>
    </div>
    <!-- / sidebar -->
    <div class="app-content">
        <!-- start: TOP NAVBAR -->
        @section('navbar')
        <header class="navbar navbar-default navbar-static-top">
            <!-- start: NAVBAR HEADER -->
            <div class="navbar-header">
                <a href="#" class="sidebar-mobile-toggler pull-left hidden-md hidden-lg btn btn-navbar sidebar-toggle" data-toggle-class="app-slide-off" data-toggle-target="#app" data-toggle-click-outside="#sidebar">
                    <i class="ti-align-justify"></i>
                </a>
                <a class="navbar-brand" href="/">
                    <img src="/images/logo.png" alt="Склад и Учет"/>
                </a>
                <a href="#" class="sidebar-toggler pull-right visible-md visible-lg" data-toggle-class="app-sidebar-closed" data-toggle-target="#app">
                    <i class="ti-align-justify"></i>
                </a>
                <a class="pull-right menu-toggler visible-xs-block" id="menu-toggler" data-toggle="collapse" href=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <i class="ti-view-grid"></i>
                </a>
            </div>
            <!-- end: NAVBAR HEADER -->
            <!-- start: NAVBAR COLLAPSE -->
            <div class="navbar-collapse collapse">
                <ul class="nav navbar-right">
                    <!-- start: MESSAGES DROPDOWN -->
                    <li class="dropdown">
                        <a href class="dropdown-toggle" data-toggle="dropdown">
                            <span class="dot-badge partition-red"></span> <i class="ti-comment"></i> <span>СООБЩЕНИЯ</span>
                        </a>
                        <ul class="dropdown-menu dropdown-light dropdown-messages dropdown-large">
                            <li>
                                <span class="dropdown-header"> Новые сообщения</span>
                            </li>
                            <li>
                                <div class="drop-down-wrapper ps-container">
                                    <ul>
                                        <li class="unread">
                                            <a href="javascript:;" class="unread">
                                                <div class="clearfix">
                                                    <div class="thread-image">
                                                        <img src="/images/avatar-4.jpg" alt="">
                                                    </div>
                                                    <div class="thread-content">
                                                        <span class="author">Nicole Bell</span>
                                                        <span class="preview">Duis mollis, est non commodo luctus, nisi erat porttitor ligula...</span>
                                                        <span class="time"> Just Now</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:;">
                                                <div class="clearfix">
                                                    <div class="thread-image">
                                                        <img src="/images/avatar-5.jpg" alt="">
                                                    </div>
                                                    <div class="thread-content">
                                                        <span class="author">Kenneth Ross</span>
                                                        <span class="preview">Duis mollis, est non commodo luctus, nisi erat porttitor ligula...</span>
                                                        <span class="time">14 hrs</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="view-all">
                                <a href="#">
                                    Открыть все
                                </a>
                            </li>
                        </ul>
                    </li>
                    <!-- end: MESSAGES DROPDOWN -->
                    <!-- start: USER OPTIONS DROPDOWN -->
                    <li class="dropdown current-user">
                        <a href class="dropdown-toggle" data-toggle="dropdown">
                            @if(Auth::user()->image)
                                <img src="{{ Auth::user()->image }}" alt="...">
                            @else
                                <img src="/images/default-user.png" alt="...">
                            @endif
                            <span class="username">{{ Auth::user()->login }} <i class="ti-angle-down"></i></span>
                        </a>
                        <ul class="dropdown-menu dropdown-dark">
                            <li>
                                <a href="{{ route('profiles') }}">
                                    Настройки профиля
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('logout') }}">
                                    Выход
                                </a>
                            </li>
                        </ul>
                    </li>
                    <!-- end: USER OPTIONS DROPDOWN -->
                </ul>
                <!-- start: MENU TOGGLER FOR MOBILE DEVICES -->
                <div class="close-handle visible-xs-block menu-toggler" data-toggle="collapse" href=".navbar-collapse">
                    <div class="arrow-left"></div>
                    <div class="arrow-right"></div>
                </div>
                <!-- end: MENU TOGGLER FOR MOBILE DEVICES -->
            </div>
            <!-- end: NAVBAR COLLAPSE -->
        </header>
        @show
        <!-- end: TOP NAVBAR -->
        <div class="main-content" >
            <div class="wrap-content container" id="container">
            @section('dashboard')
                <!-- start: DASHBOARD TITLE -->
                <section id="page-title" class="padding-top-15 padding-bottom-15">
                    <div class="row">
                        <div class="col-sm-7">
                            <h1 class="mainTitle">Главная панель</h1>
                        </div>
                        <div class="col-sm-5">
                            <!-- start: MINI STATS WITH SPARKLINE -->
                            <ul class="mini-stats pull-right">
                                <li>
                                    <div class="sparkline-1">
                                        <span ></span>
                                    </div>
                                    <div class="values">
                                        <strong class="text-dark">18304</strong>
                                        <p class="text-small no-margin">
                                            Sales
                                        </p>
                                    </div>
                                </li>
                                <li>
                                    <div class="sparkline-2">
                                        <span ></span>
                                    </div>
                                    <div class="values">
                                        <strong class="text-dark">&#36;3,833</strong>
                                        <p class="text-small no-margin">
                                            Earnings
                                        </p>
                                    </div>
                                </li>
                                <li>
                                    <div class="sparkline-3">
                                        <span ></span>
                                    </div>
                                    <div class="values">
                                        <strong class="text-dark">&#36;848</strong>
                                        <p class="text-small no-margin">
                                            Referrals
                                        </p>
                                    </div>
                                </li>
                            </ul>
                            <!-- end: MINI STATS WITH SPARKLINE -->
                        </div>
                    </div>
                </section>
                <!-- end: DASHBOARD TITLE -->
            @endsection
            @yield('dashboard')
                <!-- start: FEATURED BOX LINKS -->
                @section('tile_widget')
                <div class="container-fluid container-fullw bg-white">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="panel panel-white no-radius text-center">
                                <div class="panel-body">
                                    <span class="fa-stack fa-2x"> <i class="fa fa-square fa-stack-2x text-primary"></i> <i class="fa fa-smile-o fa-stack-1x fa-inverse"></i> </span>
                                    <h2 class="StepTitle">Manage Users</h2>
                                    <p class="text-small">
                                        To add users, you need to be signed in as the super user.
                                    </p>
                                    <p class="links cl-effect-1">
                                        <a href>
                                            view more
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="panel panel-white no-radius text-center">
                                <div class="panel-body">
                                    <span class="fa-stack fa-2x"> <i class="fa fa-square fa-stack-2x text-primary"></i> <i class="fa fa-paperclip fa-stack-1x fa-inverse"></i> </span>
                                    <h2 class="StepTitle">Manage Orders</h2>
                                    <p class="text-small">
                                        The Manage Orders tool provides a view of all your orders.
                                    </p>
                                    <p class="cl-effect-1">
                                        <a href>
                                            view more
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="panel panel-white no-radius text-center">
                                <div class="panel-body">
                                    <span class="fa-stack fa-2x"> <i class="fa fa-square fa-stack-2x text-primary"></i> <i class="fa fa-terminal fa-stack-1x fa-inverse"></i> </span>
                                    <h2 class="StepTitle">Manage Database</h2>
                                    <p class="text-small">
                                        Store, modify, and extract information from your database.
                                    </p>
                                    <p class="links cl-effect-1">
                                        <a href>
                                            view more
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endsection
            @yield('tile_widget')
                <!-- end: FEATURED BOX LINKS -->
            @yield('content')

            </div>
        </div>
    </div>
    <!-- start: FOOTER -->
    @section('footer')
    <footer>
        <div class="footer-inner">
            <div class="pull-left">
                &copy; <span class="current-year"></span>&nbsp;&nbsp;<span>Разработано для компании</span><span class="text-bold text-uppercase">  ООО Ремтехстрой</span>.
            </div>
            <div class="pull-right">
                <span class="go-top"><i class="ti-angle-up"></i></span>
            </div>
        </div>
    </footer>
    <!-- end: FOOTER -->
    <!-- start: SETTINGS -->
    <div class="settings panel panel-default hidden-xs hidden-sm" id="settings">
        <button ct-toggle="toggle" data-toggle-class="active" data-toggle-target="#settings" class="btn btn-default">
            <i class="fa fa-spin fa-gear"></i>
        </button>
        <div class="panel-heading">
            Настройки темы
        </div>
        <div class="panel-body">
            <!-- start: FIXED HEADER -->
            <div class="setting-box clearfix">
                <span class="setting-title pull-left"> Fixed header</span>
                <span class="setting-switch pull-right">
							<input type="checkbox" class="js-switch" id="fixed-header" />
						</span>
            </div>
            <!-- end: FIXED HEADER -->
            <!-- start: FIXED SIDEBAR -->
            <div class="setting-box clearfix">
                <span class="setting-title pull-left">Fixed sidebar</span>
                <span class="setting-switch pull-right">
							<input type="checkbox" class="js-switch" id="fixed-sidebar" />
						</span>
            </div>
            <!-- end: FIXED SIDEBAR -->
            <!-- start: CLOSED SIDEBAR -->
            <div class="setting-box clearfix">
                <span class="setting-title pull-left">Closed sidebar</span>
                <span class="setting-switch pull-right">
							<input type="checkbox" class="js-switch" id="closed-sidebar" />
						</span>
            </div>
            <!-- end: CLOSED SIDEBAR -->
            <!-- start: FIXED FOOTER -->
            <div class="setting-box clearfix">
                <span class="setting-title pull-left">Fixed footer</span>
                <span class="setting-switch pull-right">
							<input type="checkbox" class="js-switch" id="fixed-footer" />
						</span>
            </div>
            <!-- end: FIXED FOOTER -->
            <!-- start: THEME SWITCHER -->
            <div class="colors-row setting-box">
                <div class="color-theme theme-1">
                    <div class="color-layout">
                        <label>
                            <input type="radio" name="setting-theme" value="theme-1">
                            <span class="ti-check"></span>
                            <span class="split header"> <span class="color th-header"></span> <span class="color th-collapse"></span> </span>
                            <span class="split"> <span class="color th-sidebar"><i class="element"></i></span> <span class="color th-body"></span> </span>
                        </label>
                    </div>
                </div>
                <div class="color-theme theme-2">
                    <div class="color-layout">
                        <label>
                            <input type="radio" name="setting-theme" value="theme-2">
                            <span class="ti-check"></span>
                            <span class="split header"> <span class="color th-header"></span> <span class="color th-collapse"></span> </span>
                            <span class="split"> <span class="color th-sidebar"><i class="element"></i></span> <span class="color th-body"></span> </span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="colors-row setting-box">
                <div class="color-theme theme-3">
                    <div class="color-layout">
                        <label>
                            <input type="radio" name="setting-theme" value="theme-3">
                            <span class="ti-check"></span>
                            <span class="split header"> <span class="color th-header"></span> <span class="color th-collapse"></span> </span>
                            <span class="split"> <span class="color th-sidebar"><i class="element"></i></span> <span class="color th-body"></span> </span>
                        </label>
                    </div>
                </div>
                <div class="color-theme theme-4">
                    <div class="color-layout">
                        <label>
                            <input type="radio" name="setting-theme" value="theme-4">
                            <span class="ti-check"></span>
                            <span class="split header"> <span class="color th-header"></span> <span class="color th-collapse"></span> </span>
                            <span class="split"> <span class="color th-sidebar"><i class="element"></i></span> <span class="color th-body"></span> </span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="colors-row setting-box">
                <div class="color-theme theme-5">
                    <div class="color-layout">
                        <label>
                            <input type="radio" name="setting-theme" value="theme-5">
                            <span class="ti-check"></span>
                            <span class="split header"> <span class="color th-header"></span> <span class="color th-collapse"></span> </span>
                            <span class="split"> <span class="color th-sidebar"><i class="element"></i></span> <span class="color th-body"></span> </span>
                        </label>
                    </div>
                </div>
                <div class="color-theme theme-6">
                    <div class="color-layout">
                        <label>
                            <input type="radio" name="setting-theme" value="theme-6">
                            <span class="ti-check"></span>
                            <span class="split header"> <span class="color th-header"></span> <span class="color th-collapse"></span> </span>
                            <span class="split"> <span class="color th-sidebar"><i class="element"></i></span> <span class="color th-body"></span> </span>
                        </label>
                    </div>
                </div>
            </div>
            <!-- end: THEME SWITCHER -->
        </div>
    </div>
    <!-- end: SETTINGS -->
</div>
<!-- start: MAIN JAVASCRIPTS -->
<script src="/js/jquery.min.js"></script>
<script src="/js/bootstrap.min.js"></script>
<script src="/js/modernizr.js"></script>
<script src="/js/jquery.cookie.js"></script>
<script src="/js/perfect-scrollbar.min.js"></script>
<script src="/js/switchery.min.js"></script>
<!-- end: MAIN JAVASCRIPTS -->
<!-- start: CLIP-TWO JAVASCRIPTS -->
<script src="/js/main.js"></script>
<script>
    jQuery(document).ready(function() {
        Main.init();
    });
</script>
<!-- end: JavaScript Event Handlers for this page -->
<!-- end: CLIP-TWO JAVASCRIPTS -->
@show

@section('user_script')

@show
</body>
</html>
