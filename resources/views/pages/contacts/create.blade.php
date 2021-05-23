@php
$cssPath=str_replace('contact','contacts',$cssPath);
$jsPath=str_replace('contact','contacts',$jsPath);
@endphp
@extends('layouts.dashboard')
@section('content')
<div class="create-contact-form" style="margin: 40px 20px 20px 20px;max-width: 700px;text-align: center;">
    <form name="contactForm" id="contactForm" method="post" action="/contact/create" class="form-horizontal" data-parsley-validate="" novalidate="">
        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
        <div class="row form-group form-group-sm">
            <label class="col-md-2 control-label">Name</label>
            <div class="col-md-2">
                <select type="text" class="form-control selectpicker" name="persontitle_id" style="width:100%;">
                    @php
                    foreach($titles as $title)
                    {
                        echo '<option value="'.$title->id.'">'.$title->name.'</option>';
                    }
                    @endphp
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="firstname" class="form-control input-sm autocomplete-name" placeholder="Firstname" required="" />
            </div>
            <div class="col-md-3">
                <input type="text" name="middlename" class="form-control input-sm autocomplete-name" placeholder="Middlename"/>
            </div>
            <div class="col-md-2">
                <input type="text" name="lastname" class="form-control input-sm" placeholder="Surname"/>
            </div>
        </div>
        <div class="row form-group form-group-sm">
            <label class="col-md-2 control-label">Type</label>
            <div class="col-md-4">
                <select class="form-control selectpicker" name="contacttype_id" style="width:100%;">
                    @php
                    foreach($contact_types as $type)
                    {
                        echo '<option value="'.$type->id.'">'.$type->name.'</option>';
                    }
                    @endphp
                </select>
            </div>
        </div>
        <div class="row form-group form-group-sm">
            <label class="col-md-2 control-label">D.O.B</label>
            <div class="col-md-4">
                <input type="text" name="dob" class="form-control input-sm"/>
            </div>
            <label class="col-md-2 control-label">Marital Status</label>
            <div class="col-md-4">
                <select class="form-control selectpicker" name="marital_status" style="width:100%;">
                    <option disabled selected value>Select</option>
                    @php
                    foreach($maritalStatuses as $status)
                    {
                        echo '<option value="'.$status->name.'">'.$status->name.'</option>';
                    }
                    @endphp
                </select>
            </div>
        </div>
        <div class="row form-group form-group-sm">
            <label class="col-md-2 control-label">Gender</label>
            <div class="col-md-4">
                <select class="form-control selectpicker" name="gender" style="width:100%;">
                    <option value="male" selected>Male</option>
                    <option value="female">Female</option>
                </select>
            </div>
        </div>
        <div class="row form-group form-group-sm">
            <label class="col-md-2 control-label">Mobile</label>
            <div class="col-md-4">
                <input type="text" name="phonemobile" class="form-control input-sm" required=""/>
            </div>
            <label class="col-md-2 control-label">Work</label>
            <div class="col-md-4">
                <input type="text" name="phonework" class="form-control input-sm"/>
            </div>
        </div>
        <div class="row form-group form-group-sm">
            <label class="col-md-2 control-label">Home</label>
            <div class="col-md-4">
                <input type="text" name="phonehome" class="form-control input-sm"/>
            </div>
            <label class="col-md-2 control-label">Fax</label>
            <div class="col-md-4">
                <input type="text" name="phonefax" class="form-control input-sm"/>
            </div>
        </div>
        <div class="row form-group form-group-sm">
            <label class="col-md-2 control-label">Email</label>
            <div class="col-md-10">
                <input type="email" name="email" class="form-control input-sm"/>
            </div>
        </div>
        <!-- -->
        <div class="row form-group form-group-sm">
            <label class="col-md-2 control-label">Work Address</label>
            <div class="col-md-10">
                <input type="text" name="work_address" class="form-control input-sm">
            </div>
        </div>
        <div class="row form-group form-group-sm">
            <label class="col-md-2 control-label">Notes</label>
            <div class="col-md-10">
                <input type="text" name="notes" class="form-control input-sm"/>
            </div>
        </div>
        <div class="row form-group form-group-sm">
            <label class="col-md-2 control-label">Kids</label>
            <div class="col-md-10">
                <input type="text" name="kids" class="form-control input-sm"/>
            </div>
        </div>
        <div class="row form-group form-group-sm">
            <label class="col-md-2 control-label">OZ, PR, O/S</label>
            <div class="col-md-10">
                <input type="text" name="oz_pr_os" class="form-control input-sm"/>
            </div>
        </div>
        <div class="form-actions clearfix" style="padding-bottom: 50px;">
            <div class="pull-right">
                <button type="button" id="create_btn" class="btn btn-primary btn-save" style="margin-right: 15px;"><i class="fa fa-save"></i> Save</button>
            </div>
        </div>
    </form>
</div>
@endsection