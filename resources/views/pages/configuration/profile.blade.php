@extends('layouts.configuration')
@section('content')
<div class="wrapper wrapper-content  animated fadeInRight">
    <div class="row user-profile">
        <div class="col-md-3">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <h3>
                        {{
                            $userInfo && $userInfo->firstname !== '' && $userInfo->lastname !== '' ?
                            $userInfo->firstname.' '.$userInfo->lastname
                            :
                            'No Fullname'
                        }}
                    </h3>
                    <div class="profile-avatar" data-toggle="modal" data-target="#avatar" style="cursor: pointer;position: relative">
                        <img class="img-thumbnail" src="/configuration/profile/getAvatar" height="200" width="200" onerror="imgError(this);">
                        <i class="avatar-edit fa fa-camera" style="pointer-events:none;position: absolute;bottom: 5px;right: 7px;" title="Click to edit avatar"></i>
                    </div>

                    <div class="profile-roles">
                        @if ($userRole->admin == 1)
                        <span class="label label-warning">Admin</span>
                        @endif
                        @if ($userRole->broker == 1)
                        <span class="label label-inverse">Broker</span>
                        @endif
                        @if ($userRole->personalAssistant == 1)
                        <span class="label label-purple">PA</span>
                        @endif
                    </div>
                    <p class="profile-start">Username: {{$userInfo->username}}</p>
                    <p class="profile-start">Member since: {{date('d M Y', strtotime($userInfo->stamp_created))}}</p>
                    <hr>
                    <p class="profile-deals"><i class="fa fa-tasks"></i> Deals: <span></span></p>
                    <hr>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>User Info</h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="profile-details clearfix">
                                <div class="label">First Name</div>
                                <div class="value">{{$userInfo->firstname}}</div>
                            </div>
                            <div class="profile-details clearfix">
                                <div class="label">Last Name</div>
                                <div class="value">{{$userInfo->lastname}}</div>
                            </div>
                            <div class="profile-details clearfix">
                                <div class="label">Work #</div>
                                <div class="value">{{$userInfo->phone_office}}</div>
                            </div>
                            <div class="profile-details clearfix">
                                <div class="label">Mobile #</div>
                                <div class="value">{{$userInfo->phone_mobile}}</div>
                            </div>
                            <div class="profile-details clearfix">
                                <div class="label">Email</div>
                                <div class="value">{{$userInfo->email}}</div>
                            </div>
                            <div class="profile-details clearfix">
                                <div class="label">API token*</div>
                                <div class="value" style="">{{$userInfo->remember_token}}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                @if ($userRole->broker == 1)
                    <h5>Assistants</h5>
                    <div class="ibox-tools">
                        <a class="btn" title="Add Assistant" data-toggle="modal" data-target="#myModal">
                            <i class="fa fa-tag"></i>
                            Add Assistant
                        </a>
                    </div>
                @else
                    <h5>Brokers</h5>
                @endif
                </div>
                <div class="ibox-content" >
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover data-table dataTable table-profile">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Role</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Role</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Preferences</h5>
                    <div class="ibox-tools">
                        {{-- <a class="btn" title="Change Commission Value" data-toggle="modal" data-target="#commissionValue">
                            <i class="fa fa-tag"></i>
                            Change Commission Value
                        </a> --}}
                    </div>
                </div>
                {{-- <div class="ibox-content" >
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Commission Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{$userCommissionValue}}</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Commission Value</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div> --}}
                <div class="ibox-content" >
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Value</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($preferenceList as $preference)
                                <tr>
                                    <td>{{$preference['name']}}</td>
                                    <td>{{$preference['value'] ?? $preference['default']}}</td>
                                    <td>
                                        <a class="btn" data-toggle="modal" onclick="javascript: modalPreferenceEdit({{json_encode($preference)}});" data-target="#moadl-Edit-{{$preference['type']}}" style="padding: 0px; margin: 0px;">
                                            <i class="fa fa-tag"></i>
                                            Edit
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Name</th>
                                    <th>Value</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Files</h5>
                </div>
                <div class="ibox-content" >
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover data-table dataTable table-files">
                            <thead>
                            <tr>
                                <th>Deal Card Name</th>
                                <th>File Name</th>
                                <th>File Size</th>
                                <th>Attached By ...</th>
                                <th>Attached Date & time</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th>Deal Card Name</th>
                                <th>File Name</th>
                                <th>File Size</th>
                                <th>Attached By ...</th>
                                <th>Attached Date & time</th>
                                <th>Action</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Codes</h5>
                </div>
                <div class="ibox-content" >
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover data-table dataTable table-brokercode">
                            <thead>
                            <tr>
                                <th>Lender</th>
                                <th>Broker Code</th>
                                <th>Password</th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th>Lender</th>
                                <th>Broker Code</th>
                                <th>Password</th>
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
<!-- edit avatar modal -->

<div class="modal fade out" role="dialog" id="avatar"  tabindex="-1" aria-hidden="false">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-body">

                <div id="drop_file" style="width: 300px;margin: 0 auto">
                    <div id="dropzoneForm" class="dropzone"></div>
                    <div id="fileListDiv" style=""></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
   </div>
</div>
<!-- add assistance modal -->
<div class="modal fade out" role="dialog" id="myModal"  tabindex="-1" aria-hidden="false">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add Assistant</h4>
            </div>
            <div class="modal-body">
                <div class="error-mesg"></div>
                <div class="bootbox-body">
                <form class="bootbox-form" id="add-assistant-form">
                  <input type="hidden" name="ass_id" id="ass_id" value="">
                  <input class="bootbox-input form-control assistance" type="text" name="assistant" autocomplete="off">
                  <div id="ajaxresponse"></div>
                </form>
                </div>
                <div id="show-details" style="margin-top:10px;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button class="btn btn-primary" type="button" id="add-assistant">Add</button>
            </div>
        </div>
   </div>
</div>
<!-- add commission modal -->
<div class="modal fade out" role="dialog" id="commissionValue"  tabindex="-1" aria-hidden="false">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Change Commission Value</h4>
            </div>
            <form action="/configuration/profile/preferencePost" method="post" class="bootbox-form" id="add-commission-form">
            <div class="modal-body">
                <div class="error-mesg"></div>
                <div class="bootbox-body">
                  <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                  <input type="hidden" name="id" value="commission_value">
                  <input class="bootbox-input form-control" type="text" name="value" autocomplete="off">
                  <div id="ajaxresponse"></div>
                </div>
                <div id="show-details" style="margin-top:10px;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button class="btn btn-primary" type="submit" >Submit</button>
            </div>
            </form>
        </div>
   </div>
</div>
<!-- -->


<!-- add BOOLEAN modal -->
<div class="modal fade out" role="dialog" id="moadl-Edit-BOOLEAN"  tabindex="-1" aria-hidden="false">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="BOOLEAN-Title">Change Boolean Value</h4>
            </div>
            <form action="/configuration/profile/preferencePost" method="post" class="bootbox-form" id="add-commission-form">
            <div class="modal-body">
                <div class="error-mesg"></div>
                <div class="bootbox-body">
                  <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                  <input type="hidden" name="_type" value="BOOLEAN">
                  <input type="hidden" name="BOOLEAN-key" id="BOOLEAN-key" value="">
                  <div class="switch">
                    <div class="onoffswitch">
                        <input type="checkbox" id="BOOLEAN-value" name="BOOLEAN-value" class="onoffswitch-checkbox">
                        <label class="onoffswitch-label" for="BOOLEAN-value">
                            <span class="onoffswitch-inner"></span>
                            <span class="onoffswitch-switch"></span>
                        </label>
                    </div>
                </div>
                  <div id="ajaxresponse"></div>
                </div>
                <div id="show-details" style="margin-top:10px;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button class="btn btn-primary" type="submit" >Submit</button>
            </div>
            </form>
        </div>
   </div>
</div>
<!-- -->

<!-- add NUMBER modal -->
<div class="modal fade out" role="dialog" id="moadl-Edit-NUMBER"  tabindex="-1" aria-hidden="false">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="NUMBER-Title">Change NUMBER Value</h4>
            </div>
            <form action="/configuration/profile/preferencePost" method="post" class="bootbox-form" id="add-commission-form">
            <div class="modal-body">
                <div class="error-mesg"></div>
                <div class="bootbox-body">
                  <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                  <input type="hidden" name="_type" value="NUMBER">
                  <input type="hidden" name="NUMBER-key" id="NUMBER-key" value="">
                  <input class="bootbox-input form-control" type="text" name="NUMBER-value" id="NUMBER-value" autocomplete="off">
                  <div id="ajaxresponse"></div>
                </div>
                <div id="show-details" style="margin-top:10px;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button class="btn btn-primary" type="submit" >Submit</button>
            </div>
            </form>
        </div>
   </div>
</div>
<!-- -->

<!-- add TIME modal -->
<div class="modal fade out" role="dialog" id="moadl-Edit-TIME"  tabindex="-1" aria-hidden="false">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="TIME-Title">Change TIME Value</h4>
            </div>
            <form action="/configuration/profile/preferencePost" method="post" class="bootbox-form" id="add-commission-form">
            <div class="modal-body">
                <div class="error-mesg"></div>
                <div class="bootbox-body">
                  <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                  <input type="hidden" name="_type" value="TIME">
                  <input type="hidden" name="TIME-key" id="TIME-key" value="">
                  <input type="text" class="form-control input-sm preferencetime" name="TIME-value" id="TIME-value" value="" autocomplete="off" />
                  <div id="ajaxresponse"></div>
                </div>
                <div id="show-details" style="margin-top:10px;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button class="btn btn-primary" type="submit" >Submit</button>
            </div>
            </form>
        </div>
   </div>
</div>
<!-- -->


