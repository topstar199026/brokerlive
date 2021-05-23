@extends('layouts.configuration')
@section('content')
<div class="wrapper wrapper-content  animated fadeInRight" style="padding-top: 0px;width:100%;">
    <div class="row">
        <div class="col-md-12">
            <div class="ibox float-e-margins">
                @if ($user->id)
                @php
                    $lock = "Unlock";
                    $lockFunction = "unlock";
                    if (!empty($role_id)) {
                        foreach ($role_id as $role_value) {
                            if ($role_value['role_id'] == 1) {
                                $lock = "Lock out";
                                $lockFunction = "lockout";
                                break;
                            }
                        }
                    }
                @endphp
                <div class="ibox-title" style="border-top-width: 0px;">
                    <div class="ibox-tools" style="float:left;">
                        <h2 style="margin-bottom: 10px">Edit - {{$user->firstname}}</h2>
                    </div>
                    <div class="ibox-tools">
                        <a id="lock_{{$user->id}}" class="btn btn-white" user_id="{{$user->id}}" href="javascript:void(0);" onclick="{{$lockFunction}}(this)">{{$lock}}</a>
                        <a class="btn btn-white useradd" href="javascript:void(0);" data-toggle="modal" data-target="#myModal">Reset Password</a>
                        <a class="btn btn-white useradd" href="javascript:void(0);" data-toggle="modal" data-target="#cloneModal">Duplicate</a>
                        <!--a class="btn btn-white" href="configuration/user/rebuildindex/{{$user->id}}" >Rebuild Index</a-->
                    </div>
                </div>
                @else
                <div class="ibox-title" style="border-top-width: 0px;">
                    <div class="ibox-tools" style="float:left;">
                        <h2 style="margin-bottom: 10px">Create User</h2>
                    </div>
                </div>
                @endif
                <div class="ibox-content ibox">
                    @isset($message)
                    <div class="alert alert-warning col-md-5 col-md-offset-2">
                        {{$message}}
                    </div>
                    @endisset
                    <form id="add-user-form" class="form-horizontal" method="post" role="form">
                        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-3" id="add-user-error"></div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 control-label">Full name</label>
                            <div class="col-md-3">
                                <div id="user-firstname-error">
                                    @isset($errors['firstname'])
                                    <div class="alert alert-danger">{{$errors['firstname']}}</div>
                                    @endisset
                                </div>
                                <input type="text" class="form-control onblur" id="firstname" name="firstname" placeholder="First name" value="{{$user->firstname}}" required />
                            </div>
                            <div class="col-md-3">
                                <div id="user-lastname-error">
                                    @isset($errors['lastname'])
                                    <div class="alert alert-danger">{{$errors['lastname']}}</div>
                                    @endisset
                                </div>
                                <input type="text" class="form-control onblur"   id="lastname" name="lastname" placeholder="Last name" value="{{$user->lastname}}" required />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 control-label">Email address</label>
                            <div class="col-md-6">
                                <div id="user-email-error">
                                    @isset($errors['email'])
                                    <div class="alert alert-danger">{{$errors['email']}}</div>
                                    @endisset
                                </div>
                                <input type="email" class="form-control" name="email" required value="{{$user->email}}"/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 control-label">Phone (Office)</label>
                            <div class="col-md-6">
                                <input type="tel" class="form-control" name="phone_office" value="{{$user->phone_office}}"/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 control-label">Phone (Mobile)</label>
                            <div class="col-md-6">
                                <input type="tel" class="form-control" name="phone_mobile" value="<?=$user->phone_mobile?>"/>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 control-label">Username</label>
                            <div class="col-md-6">
                                <div id="user-username-error">
                                    <?php if (!empty($errors['username'])) : ?>
                                    <div class="alert alert-danger"><?php echo $errors['username']; ?></div>
                                    <?php endif; ?>
                                </div>
                                <input type="text" class="form-control" id="username" name="username" required value="<?=$user->username?>"/>
                            </div>
                        </div>
                        <?php if (!$user->id) {?>
                        <div class="form-group row">
                            <label class="col-md-3 control-label">Password</label>
                            <div class="col-md-6">
                                <div id="user-password-error"></div>
                                <input type="password" class="form-control" name="password" required />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 control-label">Confirm Password</label>
                            <div class="col-md-6">
                                <div id="user-password_confirm-error"></div>
                                <input type="password" class="form-control" name="password_confirm" required />
                            </div>
                        </div>
                        <?php }?>
                        <div class="form-group row">
                            <label class="col-md-3 control-label">Roles</label>
                            <div class="col-md-7">
                                <?php foreach ($roles as $role) {
                                    $selected='';

                                    if (!empty($role_id)) {
                                        foreach ($role_id as $role_value) {
                                            if ($role_value['role_id']==$role->id) {
                                                $selected ='checked="checked"';
                                                break;
                                            }
                                        }
                                    }
                                    ?>
                                    <input type="<?=$role->id==1 ? "hidden":"checkbox"?>" class="<?=$role->id==1 ? "":"multiselect"?>" name="roles[]" customid="<?=$role->id?>" <?php echo $selected;?> value="<?=$role->name?>">&nbsp;<?=$role->id ==1 ? "" : ucwords($role->name)?>&nbsp;

                                <?php } ?>
                            </div>
                        </div>
                        <?php if (isset($relations) && !empty($relations)) {?>
                            <?php foreach ($relations as $key => $relation) {?>
                                <div class="form-group row">
                                    <label class="col-md-3 control-label"><?= $relation?></label>
                                    <div class="col-md-6">
                                        <div id="user-relation_<?= $key?>-error"></div>
                                        <select class="select form-control" name="relation_<?= $key?>">
                                            <option disabled selected value >Select <?= $relation?></option>
                                            <?php if ($key == 1) {?>
                                                <?php if (isset($listAggregator)) {
                                                    foreach ($listAggregator as $item) {
                                                        ?>
                                                        <option value="<?= $item->id ?>" <?php if (isset(${"user_$key"}) && $item->id == ${"user_$key"}) { ?> selected <?php }?>><?= $item->name?></option>
                                                    <?php }} ?>
                                            <?php } else if ($key == 3) {?>
                                                <?php if (isset($listOrganisation)) {
                                                    foreach ($listOrganisation as $item) {
                                                        ?>
                                                        <option value="<?= $item->id ?>" <?php if (isset(${"user_$key"}) && $item->id == ${"user_$key"}) { ?> selected <?php }?>><?= $item->legal_name?></option>
                                                    <?php }}?>
                                            <?php } else {?>
                                                <?php if (isset($listBroker)) {
                                                    foreach ($listBroker as $item) {
                                                        ?>
                                                        <option value="<?= $item->id ?>" <?php if (isset(${"user_$key"}) && $item->id == ${"user_$key"}) { ?> selected <?php }?>><?php if (empty($item->firstname) && empty($item->lastname)) {echo $item->username;} else{ echo $item->firstname . " " . $item->lastname;} ?></option>
                                                    <?php }}?>
                                            <?php }?>
                                        </select>
                                    </div>
                                </div>
                            <?php }?>
                        <?php }?>
                        <div id="chkAdminDiv" style="display:none">
                            <div class="form-group">
                                <label class="col-md-3 control-label">Is Administration</label>
                                <div class="radio">
                                <label>
                                    <input type="radio" name="is_broker_admin" value="Y" >
                                Yes
                                </label>
                                </div>
                                <label class="col-md-3 control-label">&nbsp;</label>
                                <div class="radio">
                                <label>
                                    <input type="radio" name="is_broker_admin" checked value="N">
                                No
                                </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-buttons">
                            <div class="col-md-6 col-md-offset-3">
                                <button id="<?php if ($user->id) { ?>edit-user<?php } else { ?>add-new-user<?php } ?>" type="submit" class="btn btn-primary" name="btnSubmit" value="save"><span class="fa fa-save"></span> Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>




<!-- Button trigger modal -->
<!-- Modal -->
<div class="modal fade brokerlive-modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Reset Password</h4>
      </div>

      <div class="col-md-6 col-md-offset-3 brokerlive-modal-error" id="reset-password-error"></div>
      <form class="form-horizontal" method="post" role="form" action="/configuration/user/resetpassword">
      <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
      <div class="modal-body">
         <input type="hidden" class="form-control" name="id" value="<?=$user->id?>"/>
            <div class="form-group">
                <label class="col-md-3 control-label">Password</label>
                <div class="col-md-6">
                    <input type="password" class="form-control"  name="password" id="password" required />
                </div>
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button id="reset-password-btn" type="submit" class="btn btn-primary">Submit</button>
      </div>
      </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal --><div class="modal fade" id="cloneModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Duplicate</h4>
      </div>

      <form class="form-horizontal" id="cloneForm" method="post" role="form" action="/configuration/user/copydeal">
      <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
      <div class="modal-body">
         <input type="hidden" class="form-control" name="id"  value="<?=$user->id?>"/>
            <div class="form-group">
                <label class="col-md-3 control-label">Email</label>
                <div class="col-md-6">
                    <input type="text" class="form-control" name="email" id="email" required />
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">Reason</label>
                <div class="col-md-6">
                    <textarea name="reason" class="form-control" id="reason" required></textarea>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-12 cloneStatus text-danger" id="cloneError">
                    Error, cannot start the Dupplciation Process. Please try again later.
                </div>
                <div class="col-md-12 cloneStatus text-primary" id="cloneSuccess">
                    Duplication process triggered! You will receive an email when the process is completed.
                    <div class="text-info" id="cloneClosingMessage"></div>
                </div>
            </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Submit</button>
      </div>
      </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@endsection