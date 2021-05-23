@extends('layouts.configuration')
@section('content')
<div class="row" style="width:100%;">
    <div class="col-md-12">
        <div class="ibox float-e-margins">
            @if(isset($errors)&&$errors!='')
                <div class="wrapper wrapper-content  animated fadeInRight alert alert-sucess" style="padding: 17px;">
                    {{$errors}}
                </div>
            @endif
            @if(isset($organisation)&&$organisation->id)
                <div class="ibox-title" style="border-top-width: 0px;">
                    <div class="ibox-tools" style="float:left;">
                        <h2 style="margin-bottom: 10px">Edit - {{$organisation->legal_name}}</h2>
                    </div>
                </div>
            @else
                <div class="ibox-title" style="border-top-width: 0px;">
                    <div class="ibox-tools" style="float:left;">
                        <h2 style="margin-bottom: 10px">Create Organisation</h2>
                    </div>
                </div>
            @endif
            <div class="wrapper wrapper-content  animated fadeInRight" style="padding: 0px;">
                <div class="ibox-content ibox">

                    <form class="form-horizontal" method="post" role="form">
                        <div class="form-group row">
                            <label class="col-md-5 control-label">Legal Name</label>
                            <div class="col-md-5">
                                <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                                <input type="text" class="form-control onblur" id="legal_name" name="legal_name" placeholder="Legal Name" value="{{isset($organisation)?$organisation->legal_name:''}}" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-5 control-label">Trading Name</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control onblur" id="trading_name" name="trading_name" placeholder="Trading Name" value="{{isset($organisation)?$organisation->trading_name:''}}" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-5 control-label">Short Name</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control onblur" id="short_name" name="short_name" placeholder="Short Name" value="{{isset($organisation)?$organisation->short_name:''}}" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-5 control-label">Australian Company Number</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control onblur" id="acn" name="acn" placeholder="Australian Company Number" value="{{isset($organisation)?$organisation->acn:''}}" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-5 control-label">Address 1</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control onblur" id="address_line1" name="address_line1" placeholder="Address 1" value="{{isset($organisation)?$organisation->address_line1:''}}" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-5 control-label">Address 2</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control onblur" id="address_line2" name="address_line2" placeholder="Address 2" value="{{isset($organisation)?$organisation->address_line2:''}}" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-5 control-label">Suburb</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control onblur" id="suburb" name="suburb" placeholder="Suburb" value="{{isset($organisation)?$organisation->suburb:''}}" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-5 control-label">State</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control onblur" id="state" name="state" placeholder="State" value="{{isset($organisation)?$organisation->state:''}}" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-5 control-label">Post Code</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control onblur" id="postcode" name="postcode" placeholder="Post Code" value="{{isset($organisation)?$organisation->postcode:''}}" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-5 control-label">Country</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control onblur" id="country" name="country" placeholder="Country" value="{{isset($organisation)?$organisation->country:''}}" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-5 control-label">Phone Number</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control onblur" id="phone_number" name="phone_number" placeholder="Phone Number" value="{{isset($organisation)?$organisation->phone_number:''}}" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-5 control-label">Fax Number</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control onblur" id="fax_number" name="fax_number" placeholder="Fax Number" value="{{isset($organisation)?$organisation->fax_number:''}}" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-5 control-label">Organisation Parent</label>
                            <div class="col-md-6">
                                <select class="select form-control" name="parent">
                                    <option disabled selected value >Select Parent</option>
                                    @if(isset($listOrganisation)){
                                        @foreach ($listOrganisation as $item) 
                                        <option value="{{$item->id}}" {{(isset($organisation)&&$item->id==$organisation->parent)?'selected':''}}>{{$item->legal_name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="form-group form-buttons">
                            <div class="col-md-6 col-md-offset-5">
                                @if(isset($organisation))
                                <button type="submit" class="btn btn-primary" name="btnSubmit" value="save"><span class="fa fa-save"></span> Save</button>
                                @else
                                <button type="submit" class="btn btn-primary" name="btnSubmit" value="save-add"><span class="fa fa-save"></span> Save and Add</button>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection