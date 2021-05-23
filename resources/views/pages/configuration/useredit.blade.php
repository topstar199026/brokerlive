@extends('layouts.configuration')
@section('content')
@if($message != '')
    <div class="alert alert-warning col-md-5 col-md-offset-2">
        {{$message}}
    </div> 
@endif
<form class="form-horizontal" method="post" role="form" id="edit_form" style="width:100%;margin-top:20px;">
@if($user_details->role->personalAssistant || $user_details->role->broker)
<ul class="nav nav-tabs" style="margin-left:10px;margin-top:0px;width: 97%;">
  <li {{($relationship == 'N') ? 'class="active"' : ''}}><a href="#details" data-toggle="tab">User Details</a></li>
  <li {{($relationship == 'Y') ? 'class="active"' : ''}}><a href="#relationships" data-toggle="tab">Relationships</a></li>
</ul>

<div class="tab-content">
  <div class="tab-pane {{($relationship == 'N') ? 'active' : ''}}" id="details">
@endif   
        <input type="hidden" class="form-control" name="id"  id="id" value="{{$user_details->id}}"/>
        <div class="form-group row">
            <label class="col-md-3 control-label">Full name</label>
            <div class="col-md-3">
                <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                <input type="text" class="form-control" name="firstname"  value="{{$user_details->firstname}}" placeholder="First name" required />
                
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" name="lastname" value="{{$user_details->lastname}}" placeholder="Last name" required />
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 control-label">Email address</label>
            <div class="col-md-6">
                <input type="email" value="{{$user_details->email}}" class="form-control" name="email" readonly/>
            </div>
        </div>
        
        <div class="form-group form-buttons row">
            <div class="col-md-6 col-md-offset-3">
              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">Change Password</button>
            </div>
        </div>
            
        <div class="form-group row">
            <label class="col-md-3 control-label">Username</label>
            <div class="col-md-6">
                <input type="text" value="{{$user_details->username}}" class="form-control" name="username" />
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 control-label">Roles</label>
            <div class="col-md-6">
                <select multiple name="roles[]" id="roles" class="options" required>
                    @foreach($roles as $role)
                    @php
                      $sel='';
                      switch ($role->name)
                      {
                          case 'admin':
                              if($user_details->role->admin)$sel='selected';
                              break;
                          case 'PA':
                              if($user_details->role->personalAssistant)$sel='selected';
                              break;
                          case 'Broker':
                              if($user_details->role->broker)$sel='selected';
                              break;
                          case 'Head Broker':
                              if($user_details->role->headBroker)$sel='selected';
                              break;
                          case 'Org Manager':
                              if($user_details->role->organisationManager)$sel='selected';
                              break;
                          case 'Org Admin':
                              if($user_details->role->organisationAdmin)$sel='selected';
                              break;
                          default :
                              break;
                      }
                    @endphp
                    <option value="{{$role->id}}" {{$sel}}>{{$role->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <div id="chkAdminDiv" style="display:none">
          <div class="form-group row">
            <label class="col-md-3 control-label">Is Administration</label>
            <div class="radio">
              <label>
                <input type="radio" name="is_broker_admin" id="is_broker_admin" value="Y" <?php if($user_details->is_broker_admin=='Y') { ?> checked <?php } ?> >
              Yes
              </label>
            </div>
            <label class="col-md-3 control-label">&nbsp;</label>
            <div class="radio">
              <label>
                <input type="radio" name="is_broker_admin" id="is_broker_admin" <?php if($user_details->is_broker_admin=='N') { ?> checked <?php } ?> value="N">
              No
              </label>
            </div>
          </div>
        </div>
        
        <div class="form-group form-buttons row">
            <div class="col-md-6 col-md-offset-3">
                <button type="submit" id="edit_submit_btn" class="btn btn-primary"><span class="fa fa-save"></span> Save</button>
            </div>
        </div>
@if($user_details->role->personalAssistant || $user_details->role->broker)
  </div><!--end tab-->
  <div class="tab-pane {{($relationship == 'Y') ? 'active' : ''}}" id="relationships">
    
    @if(isset($relations) && !empty($relations))
      @foreach ($relations as $key => $relation)
        <div class="form-group row">
          <label class="col-md-3 control-label">{{$relation}}</label>
          <div class="col-md-6">
            <div id="user-relation_{{$key}}-error"></div>
            <select class="select form-control" name="relation_{{$key}}">
                <option disabled selected value >Select {{$relation}}</option>
                @if ($key == 1)
                    @if (isset($listAggregator))
                        @foreach ($listAggregator as $item)
                          <option value="{{$item->id}}" <?php if (isset(${"user_$key"}) && $item->id == ${"user_$key"}) { ?> selected <?php }?>>{{$item->name}}</option>
                        @endforeach
                    @endif
                @endif
                @if($key == 3)
                    @if (isset($listOrganisation))
                        @foreach ($listOrganisation as $item)
                          <option value="{{$item->id}}" <?php if (isset(${"user_$key"}) && $item->id == ${"user_$key"}) { ?> selected <?php }?>>{{$item->legal_name}}</option>
                        @endforeach
                    @endif
                @else
                    @if (isset($listBroker))
                        @foreach ($listBroker as $item)
                          <option value="{{$item->id}}" <?php if (isset(${"user_$key"}) && $item->id == ${"user_$key"}) { ?> selected <?php }?>><?php if (empty($item->firstname) && empty($item->lastname)) {echo $item->username;} else{ echo $item->firstname . " " . $item->lastname;} ?></option>
                        @endforeach
                    @endif
                @endif
            </select>
          </div>
        </div>
      @endforeach
    @endif
    
    <div class="form-group form-buttons row">
        <div class="col-md-6 col-md-offset-3">
            <button type="submit" class="btn btn-primary" name="btnSubmit" value="save"><span class="fa fa-save"></span> Save</button>
        </div>
    </div>
  </div>
</div>
@endif
</form>
<!-- Button trigger modal -->
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Change Password</h4>
      </div>
      <form class="form-horizontal" method="post" role="form" action="/user/update_password">
     
      <div class="modal-body">
         <input type="hidden" class="form-control" name="id"  id="id" value="{{$user_details->id}}"/>
         <div class="form-group row">
          <div class="alert alert-warning col-md-8 col-md-offset-2" style="display:none;" id="change_pass_error">
          confirm password mismatch
          </div> 
        </div>

        <div class="form-group row">
            <label class="col-md-3 control-label">Password</label>
            <div class="col-md-6">
                <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                <input type="password" class="form-control"  name="password" id="password" required />
            </div>
        </div>
        
        <div class="form-group row">
            <label class="col-md-3 control-label">Confirm Password</label>
            <div class="col-md-6">
                <input type="password" class="form-control" name="password_confirm" id="password_confirm" required />
            </div>
        </div>
       
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary" id="update_password_btn">Save</button>
      </div>
      </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endsection