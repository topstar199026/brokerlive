@extends('layouts.configuration')
@section('content')
<div class="wrapper wrapper-content  animated fadeInRight">
    <div class="row">
        <div class="col-md-12">
            <div class="ibox float-e-margins">
                <div class="ibox-content" >
                    @if ($errors != '')
                    <div class="alert alert-warning col-md-5 col-md-offset-2">{{$errors}}</div>
                    @endif
                    <div class="col-md-7 col-md-offset-3" id="change-password-error"></div> 
                    <form class="form-horizontal" method="post" role="form" id="password-change-form">
                        <div class="form-group row">
                            <label class="col-md-3 control-label">Current Password</label>
                            <div class="col-md-6">
                                <input type="password" class="form-control" id="password" name="password" required />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 control-label">New Password</label>
                            <div class="col-md-6">
                                <input type="password" class="form-control" id="newpassword"name="newpassword" required />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 control-label">Confirm Password</label>
                            <div class="col-md-6">
                                <input type="password" class="form-control" name="password_confirm" id="password_confirm"required />
                            </div>
                        </div>
                        <div class="form-group form-buttons">
                            <div class="col-md-6 col-md-offset-3">
                                <button type="button" class="btn btn-primary"  name="btnSubmit" id="password-change"><span class="fa fa-save"></span>Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection