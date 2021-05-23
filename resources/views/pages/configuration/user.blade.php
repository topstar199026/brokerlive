@extends('layouts.configuration')
@section('content')
<div class="wrapper wrapper-content  animated fadeInRight" style="padding-top: 0px;width:100%;">
    <div class="row">
        <div class="col-md-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title" style="border-top-width: 0px;">
                    <div class="ibox-tools" style="float:left;">
                        <h2 style="margin-bottom: 10px">Users</h2>
                    </div>
                    <div class="ibox-tools">
                        <a title="Add User" class="btn btn-white useradd" href="/configuration/user/create"><i class="fa fa-user"></i> Add User</a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div id="user_grid"></div>
                </div>
                <div class="ibox-content" style="display:none;">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover data-table dataTable table-users">
                            <thead>
                                <tr>
                                    <th>UserName</th>
                                    <th>FirstName</th>
                                    <th>Email</th>
                                    <th>role</th>
                                    <th>login_count</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>UserName</th>
                                    <th>FirstName</th>
                                    <th>Email</th>
                                    <th>role</th>
                                    <th>login_count</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection