@php
    $path=explode('/',$currentUrl);
    $page=$path[0];
    $action=count($path)>1?$path[1]:'profile';
    if(!isset($layout))$layout='single';
@endphp
<!DOCTYPE html>
<html class="no-js" lang="zxx">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="brokerlive, broker">
    <meta name="_token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Brokerlive') }} | {{$title}}</title>
    @include('common.css.css')
    @include('common.css.configuration.'.$action)
</head>
<body>
    <div id="wrapper">
        @include('common.navbar.navbar')
        <div id="page-wrapper" class="gray-bg dashbard-1">
            @include('common.header.header')
            @include('common.header.title')
            <div class="wrapper wrapper-content  animated fadeInRight">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="ibox float-e-margins">
                            <div class="ibox-content">
                                <div class="file-manager">
                                    <ul class="folder-list" style="padding: 0">
                                        <li style="text-decoration:{{$action == 'profile'?'underline':'none'}};">
                                            <a href="/configuration/profile" title="My Profile" class="stop-loading-page">
                                                <i class="fa fa-user"></i> <span class="">My Profile</span>
                                            </a>
                                        </li>
                                        @if($userRole->admin || $userRole->organisationAdmin)
                                        <li style="text-decoration:{{$action == 'user'?'underline':'none'}};">
                                            <a href="/configuration/user" title="Users" class="stop-loading-page">
                                                <i class="fa fa-users"></i> <span class="">Users</span>
                                            </a>
                                        </li>
                                        <li style="text-decoration:{{$action == 'aggregator'?'underline':'none'}};">
                                            <a href="/configuration/aggregator" title="Aggregator" class="stop-loading-page">
                                                <i class="fa fa-bank"></i> <span class="">Aggregator</span>
                                            </a>
                                        </li>
                                        <li style="text-decoration:{{$action == 'process'?'underline':'none'}};">
                                            <a href="/configuration/process" title="Processes" class="stop-loading-page">
                                                <i class="fa fa-tasks"></i> <span class="">Processes</span>
                                            </a>
                                        </li>
                                        <li style="text-decoration:{{$action == 'organisation'?'underline':'none'}};">
                                            <a href="/configuration/organisation" title="Organisations" class="stop-loading-page">
                                                <i class="fa fa-building"></i> <span class="">Organisations</span>
                                            </a>
                                        </li>
                                        <li style="text-decoration:{{$action == 'task'?'underline':'none'}};">
                                            <a href="/configuration/systemTasks" title="System tasks" class="stop-loading-page">
                                                <i class="fa fa-server"></i> <span class="">System tasks</span>
                                            </a>
                                        </li>
                                        @endif
                                    </ul>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-9 animated fadeInRight">
                        <div class="row">
                        @if ($layout == 'double')
                            <div class="row wrapper border-bottom white-bg page-heading" style="margin-left: 9px;width: 97%;">
                                <div class="col-md-7">
                                    <h2>{{$title}}</h2>
                                    <ol class="breadcrumb">
                                        <li>
                                            <a href="/">Home</a>
                                        </li>
                                        <li>
                                            <a href="/configuration/" style = "text-transform:capitalize;">{{$page}}</a>
                                        </li>
                                        <li class="active" style = "text-transform:capitalize;">
                                            <strong>{{$action}}</strong>
                                        </li>
                                    </ol>
                                </div>
                                <div class="col-md-5">
                                    <div class="title-action">
                                        <button type="button" class="btn btn-white" data-toggle="modal" data-target="#editModal"><i class="fa fa-user"></i>&nbsp;Edit</button>
                                        <button type="button" class="btn btn-white" onclick="location.href='/user/changepassword';"><i class="fa fa-user-secret"></i>&nbsp;Change Password</button>
                                    </div>
                                </div>
                            </div>
                            @yield('content')
                            <!-- edit profile modal -->
                            <div class="modal fade out" role="dialog" id="editModal"  tabindex="-1" aria-hidden="false">
                                <div class="modal-dialog modal-md">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h4 class="modal-title">Edit profile</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="edit-error"></div>
                                            <div class="bootbox-body">
                                            <form class="form-horizontal" id="edit-profile-form">

                                            <div class="form-group row">
                                                <label class="col-md-3 control-label">Firstname</label>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" id="firstname" name="firstname" value="{{$userInfo->firstname}}" required />
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3 control-label">Lastname</label>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" id="lastname"name="lastname" value="{{$userInfo->lastname}}" required />
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3 control-label">Email address</label>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" name="email" id="email" value="{{$userInfo->email}}" required />
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3 control-label">Office Phone</label>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" name="phone_office" value="{{$userInfo->phone_office}}" id="phone_office" required />
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-3 control-label">Mobile </label>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" name="phone_mobile" id="phone_mobile" value="{{$userInfo->phone_mobile}}" required />
                                                </div>
                                            </div>

                                            </form>
                                            </div>
                                            <div id="show-details" style="margin-top:10px;"></div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            <button class="btn btn-primary" type="button" id="edit-profile">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- -->
                        @else
                            @yield('content')
                        @endif
                        </div>
                    </div>
                </div>
            </div>
            @include('common.footer.footer')
        </div>
        @include('common.panel.chat')
        @include('common.navbar.sidebar')
    </div>
    @include('common.js.js')
    @include('common.js.configuration.'.$action)
    <script>
        var _hide = localStorage.getItem("_hide");
        $('.navbar-minimalize').click(function(){
            if(_hide == null || _hide == 'false') localStorage.setItem("_hide","true");
            if(_hide == 'true') localStorage.setItem("_hide","false");
        })

        if(_hide == 'true')document.body.className += ' mini-navbar';
    </script>
</body>
</html>

