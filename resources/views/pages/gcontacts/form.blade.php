@php
function format_name($firstname, $lastname) {
    if ($firstname == '') {
        return $lastname;
    }
    if ($lastname == '') {
        return $firstname;
    }

    return $lastname . ', ' . $firstname;
}
@endphp
<div class="loansplit-form">
    <h2>{{format_name($contact->firstname, $contact->lastname)}} <small class="text-muted">{{$contact->id ? "({$contact->id})": ""}}</small></h2>
    <div class="ibox-tools" style="top: 25px;right: 35px;">
        <a title="Add Contact" class="btn btn-white useradd" href="/gcontact/create"><i class="fa fa-tasks"></i> Add</a>
    </div>
    <hr/>
    <ul class="nav nav-tabs">
        <li class="active"><a aria-expanded="true" data-toggle="tab" href="#detail">Details</a></li>
        <li><a aria-expanded="true" data-toggle="tab" href="#deals">Deals</a></li>
    </ul>
    <div class="tab-content">
        <div id="detail" class="tab-pane active">
            <br/>
            <form name="contactForm" id="contactForm" method="post" action="/gcontact/edit/{{$contact->id}}" class="form-horizontal" data-parsley-validate="" novalidate="">
                <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                <input type="hidden" name="id" value="{{$contact->id}}"/>
                <input type="hidden" name="user_id" value="{{Auth::id()}}"/>
                <div class="row form-group form-group-sm">
                    <label class="col-md-2 control-label">Name</label>
                    <div class="col-md-2">
                        <select type="text" class="form-control selectpicker" name="persontitle_id" style="width:100%;">
                            @php
                            foreach($titles as $title)
                            {
                                $sel = '';
                                if ($contact->id != '')
                                {
                                    if($contact->persontitle_id == $title->id)
                                        $sel = 'selected';
                                }
                                echo '<option value="'.$title->id.'" '.$sel.'>'.$title->name.'</option>';
                            }
                            @endphp
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="firstname" class="form-control input-sm autocomplete-name" placeholder="Firstname" value="{{$contact->firstname}}" required="" />
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="middlename" class="form-control input-sm autocomplete-name" placeholder="Middlename" value="{{$contact->middlename}}" />
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="lastname" class="form-control input-sm" placeholder="Surname" value="{{$contact->lastname}}"/>
                    </div>
                </div>
                <div class="row form-group form-group-sm">
                    <label class="col-md-2 control-label">Type</label>
                    <div class="col-md-10">
                        <select class="form-control selectpicker" name="contacttype_id" style="width:100%;">
                            @php
                            foreach($contact_types as $type)
                            {
                                $sel = '';
                                if ($contact->id != '')
                                {
                                    if($contact->contacttype_id == $type->id)
                                        $sel = 'selected';
                                }
                                echo '<option value="'.$type->id.'" '.$sel.'>'.$type->name.'</option>';
                            }
                            @endphp
                        </select>
                    </div>
                </div>
                <div class="row form-group form-group-sm">
                    <label class="col-md-2 control-label">D.O.B</label>
                    <div class="col-md-4">
                        <input type="text" name="dob" class="form-control input-sm" value="{{$contact->dob}}"/>
                    </div>
                    <label class="col-md-2 control-label">Marital Status</label>
                    <div class="col-md-4">
                        <select class="form-control selectpicker" name="marital_status" style="width:100%;">
                            <option disabled selected value>Select</option>
                            @php
                            foreach($maritalStatuses as $status)
                            {
                                $sel = '';
                                if ($contact->id != '')
                                {
                                    if($contact->marital_status == $status->name)
                                        $sel = 'selected';
                                }
                                echo '<option value="'.$status->name.'" '.$sel.'>'.$status->name.'</option>';
                            }
                            @endphp
                        </select>
                    </div>
                </div>
                <div class="row form-group form-group-sm">
                    <label class="col-md-2 control-label">Gender</label>
                    <div class="col-md-4">
                        <select class="form-control selectpicker" name="gender" style="width:100%;">
                            <option value="male" {{$contact->gender=="male" ? "selected" : "" }}>Male</option>
                            <option value="female" {{$contact->gender=="female" ? "selected" : "" }}>Female</option>
                        </select>
                    </div>
                    <label class="col-md-2 control-label">Spouse</label>
                    <div class="col-md-4">
                        <select class="form-control selectpicker" name="spouse" style="width:100%;">
                            <option disabled selected value>Select</option>
                            @php
                            foreach($listContact as $spouse)
                            {
                                $sel = '';
                                if ($contact->id != '')
                                {
                                    if($contact->spouse == $spouse->id)
                                        $sel = 'selected';
                                }
                                echo '<option value="'.$spouse->id.'" '.$sel.'>'.format_name($spouse->firstname, $spouse->lastname).'</option>';
                            }
                            @endphp
                        </select>
                    </div>
                </div>
                <div class="row form-group form-group-sm">
                    <label class="col-md-2 control-label">Mobile</label>
                    <div class="col-md-4">
                        <input type="text" name="phonemobile" class="form-control input-sm" value="{{$contact->phonemobile}}" required=""/>
                    </div>
                    <label class="col-md-2 control-label">Work</label>
                    <div class="col-md-4">
                        <input type="text" name="phonework" class="form-control input-sm" value="{{$contact->phonework}}"/>
                    </div>
                </div>
                <div class="row form-group form-group-sm">
                    <label class="col-md-2 control-label">Home</label>
                    <div class="col-md-4">
                        <input type="text" name="phonehome" class="form-control input-sm" value="{{$contact->phonehome}}"/>
                    </div>
                    <label class="col-md-2 control-label">Fax</label>
                    <div class="col-md-4">
                        <input type="text" name="phonefax" class="form-control input-sm" value="{{$contact->phonefax}}"/>
                    </div>
                </div>
                <div class="row form-group form-group-sm">
                    <label class="col-md-2 control-label">Email</label>
                    <div class="col-md-10">
                        <input type="email" name="email" class="form-control input-sm" value="{{$contact->email}}" />
                    </div>
                </div>
                @php
                $i=0;
                if(!empty($contactAddress)){
                    foreach ($contactAddress as $address){
                @endphp
                    <div id="address{{$i}}" show="0">
                        <div class="row form-group form-group-sm short-address">
                            <label class="control-label col-md-2">Addr{{$i> 0? $i : ''}}</label>
                            <div class="col-md-9" onclick="showAddress({{$i}})" style="padding-top: 4px;color: blue;cursor: pointer;">
                                {{$address->homeaddress ? $address->homeaddress : "Home Address"}}
                            </div>
                        </div>
                        <div class="row form-group form-group-sm address" style="display: none;">
                            <label class="control-label col-md-2">Home Address{{$i> 0? $i : ''}}</label>
                            <div class="col-md-9">
                                <input type="text" name="contact_address[{{$i}}][homeaddress]" class="form-control input-sm" value="{{$address->homeaddress}}"/>
                            </div>
                        </div>
                        <div class="row form-group form-group-sm address" style="display: none;">
                            <div class="col-md-1"></div>
                            <div class="col-md-3">
                                <select class="form-control selectpicker" name="contact_address[{{$i}}][ownership]" style="width:100%;">
                                    <option disabled selected value>Owner Ship</option>
                                    @php
                                    foreach($contact->addressOwnerShip as $value)
                                    {
                                        $sel = '';
                                        if ($address->ownership != '')
                                        {
                                            if($address->ownership == $value)
                                                $sel = 'selected';
                                        }
                                        echo '<option value="'.$value.'" '.$sel.'>'.$value.'</option>';
                                    }
                                    @endphp
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-control selectpicker" name="contact_address[{{$i}}][status]" style="width:100%;">
                                    <option disabled selected value>Address Status</option>
                                    @php
                                    foreach($contact->addressStatus as $value)
                                    {
                                        $sel = '';
                                        if ($address->status != '')
                                        {
                                            if($address->status == $value)
                                                $sel = 'selected';
                                        }
                                        echo '<option value="'.$value.'" '.$sel.'>'.$value.'</option>';
                                    }
                                    @endphp
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="text" name="contact_address[{{$i}}][startdate]" class="form-control datepicker" placeholder="Start Date" value="{{$address->startdate}}"/>
                            </div>
                            <div class="col-md-2">
                                <input type="text" name="contact_address[{{$i}}][enddate]" class="form-control datepicker" placeholder="End Date" value="{{$address->enddate}}"/>
                            </div>
                        </div>
                        <div class="row form-group form-group-sm address" style="display: none;">
                            <div class="col-md-1"></div>
                            <div class="col-md-3 col-md-offset-2">
                                <input type="text" name="contact_address[{{$i}}][unit]" class="form-control input-sm" placeholder="Unit" value="{{$address->unit}}"/>
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="contact_address[{{$i}}][streetnumber]" class="form-control input-sm" placeholder="Street Number" value="{{$address->streetnumber}}"/>
                            </div>
                            <div class="col-md-2">
                                <input type="text" name="contact_address[{{$i}}][streetname]" class="form-control input-sm" placeholder="Street Name" value="{{$address->streetname}}"/>
                            </div>
                            <div class="col-md-2">
                                <select class="form-control selectpicker" name="contact_address[{{$i}}][streettype]" style="width:100%;">
                                    <option disabled selected value>Street Type</option>
                                    @php
                                    foreach($contact->streetTypes as $value)
                                    {
                                        $sel = '';
                                        if ($address->streettype != '')
                                        {
                                            if($address->streettype == $value)
                                                $sel = 'selected';
                                        }
                                        echo '<option value="'.$value.'" '.$sel.'>'.$value.'</option>';
                                    }
                                    @endphp
                                </select>
                            </div>
                        </div>
                        <div class="row form-group form-group-sm address" style="display: none;">
                            <div class="col-md-1"></div>
                            <div class="col-md-3 col-md-offset-2">
                                <input type="text" name="contact_address[{{$i}}][suburb]" class="form-control input-sm" placeholder="Suburb" value="{{$address->suburb}}"/>
                            </div>
                            <div class="col-md-3">
                                <select class="form-control selectpicker" name="contact_address[{{$i}}][state]" style="width:100%;">
                                    <option disabled selected value>State</option>
                                    @php
                                    foreach($contact->addressState as $value)
                                    {
                                        $sel = '';
                                        if ($address->state != '')
                                        {
                                            if($address->state == $value)
                                                $sel = 'selected';
                                        }
                                        echo '<option value="'.$value.'" '.$sel.'>'.$value.'</option>';
                                    }
                                    @endphp
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="text" name="contact_address[{{$i}}][postcode]" class="form-control input-sm" placeholder="Postcode" value="{{$address->postcode}}"/>
                            </div>
                            <div class="col-md-2">
                                <input type="text" name="contact_address[{{$i}}][country]" class="form-control input-sm" placeholder="Country" value="{{$address->country}}"/>
                            </div>
                        </div>
                        @php
                        if($i > 0){
                        @endphp
                            <div class="row form-group form-group-sm">
                                <div class="pull-right">
                                    <a class="btn btn-danger btn-xs btn-delete" onclick="deleteAddress({{$i}})"><i class="fa fa-trash-o"></i> Delete</a>
                                </div>
                            </div>
                        @php
                        }
                        $i++;
                        @endphp
                    </div>
                    @php
                    }
                }
                @endphp
                <div class="row form-group form-group-sm" id="add-address">
                    <div class="pull-right">
                        <a class="btn btn-success btn-xs btn-add" onclick="addAddress({{$i}})"><i class="fa fa-plus-o"></i> Add Address</a>
                    </div>
                </div>
                @php
                if(!empty($contactEmployment)) {
                @endphp
                    <div class="row form-group form-group-sm">
                        <label class="control-label col-md-2">Employment Detail</label>
                        <div class="col-md-3">
                            <input type="text" name="contact_employment[name]" class="form-control input-sm" placeholder="Employer Name" value="{{$contactEmployment->name}}"/>
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="contact_employment[startdate]" class="form-control datepicker" placeholder="Start Date" value="{{$contactEmployment->startdate}}"/>
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="contact_employment[enddate]" class="form-control datepicker" placeholder="End Date" value="{{$contactEmployment->enddate}}"/>
                        </div>
                    </div>
                    <div class="row form-group form-group-sm">
                        <div class="col-md-4 col-md-offset-2">
                            <select class="form-control selectpicker" name="contact_employment[category]" style="width:100%;">
                                <option disabled selected value>Employment Category</option>
                                @php
                                foreach($contact->employmentCategory as $value)
                                {
                                    $sel = '';
                                    if ($contactEmployment->status != '')
                                    {
                                        if($contactEmployment->status == $value)
                                            $sel = 'selected';
                                    }
                                    echo '<option value="'.$value.'" '.$sel.'>'.$value.'</option>';
                                }
                                @endphp
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select class="form-control selectpicker" name="contact_employment[status]" style="width:100%;">
                                <option disabled selected value>Employment Status</option>
                                @php
                                foreach($contact->employmentStatus as $value)
                                {
                                    $sel = '';
                                    if ($contactEmployment->status != '')
                                    {
                                        if($contactEmployment->status == $value)
                                            $sel = 'selected';
                                    }
                                    echo '<option value="'.$value.'" '.$sel.'>'.$value.'</option>';
                                }
                                @endphp
                            </select>
                        </div>
                    </div>
                @php
                }
                @endphp
                <!-- -->
                <div class="row form-group form-group-sm">
                    <label class="col-md-2 control-label">Work Address</label>
                    <div class="col-md-9">
                        <input type="text" name="work_address" class="form-control input-sm" value="{{$contact->work_address}}"/>
                    </div>
                </div>
                <div class="row form-group form-group-sm">
                    <label class="col-md-2 control-label">Notes</label>
                    <div class="col-md-10">
                        <input type="text" name="notes" class="form-control input-sm" value="{{$contact->notes}}"/>
                    </div>
                </div>
                <div class="row form-group form-group-sm">
                    <label class="col-md-2 control-label">Kids</label>
                    <div class="col-md-10">
                        <input type="text" name="kids" class="form-control input-sm" value="{{$contact->kids}}"/>
                    </div>
                </div>
                <div class="row form-group form-group-sm">
                    <label class="col-md-2 control-label">OZ, PR, O/S</label>
                    <div class="col-md-10">
                        <input type="text" name="oz_pr_os" class="form-control input-sm" value="{{$contact->oz_pr_os}}"/>
                    </div>
                </div>
                <div class="form-actions clearfix" style="padding-bottom: 50px;">
                    <div class="pull-right">
                        <a class="btn btn-info btn-xs btn-cancel" href="/gcontact/"><i class="fa fa-times-circle"></i> Cancel</a>
                        <button type="button" id="save_btn" class="btn btn-primary btn-xs btn-save"><i class="fa fa-save"></i> Save</button>
                        @php
                        if ($contact->id != '') {
                        @endphp
                            <a class="btn btn-danger btn-xs btn-delete" onclick="validateContact('{{$contact->id}}', '/gcontact/delete/{{$contact->id}}')" ><i class="fa fa-trash-o"></i> Delete</a>
                        @php
                        }
                        @endphp
                    </div>
                </div>
            </form>
        </div>
        <div id="deals" class="tab-pane">
            <div class="full-height-scroll">
                <div class="table-responsive">
                    @php
                    if(isset($deals)){
                    @endphp
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                            @php
                            foreach($deals as $deal)
                            {
                            @endphp
                                <tr>
                                    <td>{{$deal->id}}</td>
                                    <td><a href="/deal/edit/{{$deal->id}}" class="client-link">{{$deal->name}}</a></td>
                                    <td>{{$deal->created_at}}</td>
                                </tr>
                            @php
                            }
                            @endphp
                            </tbody>
                        </table>
                    @php
                    }
                    @endphp
                </div>
                </div>
            </div>
        </div>
    </div>
    <div id="address[template_id]" class="addressTemplate" style="display: none;">
    <div class="row form-group form-group-sm">
        <label class="control-label col-md-2">Home Address[template_id]</label>
        <div class="col-md-9">
            <input type="text" name="contact_address[[template_id]][homeaddress]" class="form-control input-sm" value=""/>
        </div>
    </div>
    <div class="row form-group form-group-sm">
        <div class="col-md-3 ">
            <select class="form-control selectpicker" name="contact_address[[template_id]][ownership]" style="width:100%;">
                <option disabled selected value>Owner Ship</option>
                <?php
                foreach($contact->addressOwnerShip as $value)
                {
                    echo '<option value="'.$value.'" '.'>'.$value.'</option>';
                }
                ?>
            </select>
        </div>
        <div class="col-md-3">
            <select class="form-control selectpicker" name="contact_address[[template_id]][status]" style="width:100%;">
                <option disabled selected value>Address Status</option>
                <?php
                foreach($contact->addressStatus as $value)
                {
                    echo '<option value="'.$value.'" '.'>'.$value.'</option>';
                }
                ?>
            </select>
        </div>
        <div class="col-md-3">
            <input type="text" name="contact_address[[template_id]][startdate]" class="form-control datepicker" placeholder="Start Date" value=""/>
        </div>
        <div class="col-md-3">
            <input type="text" name="contact_address[[template_id]][enddate]" class="form-control datepicker" placeholder="End Date" value=""/>
        </div>
    </div>
    <div class="row form-group form-group-sm">
        <div class="col-md-3">
            <input type="text" name="contact_address[[template_id]][unit]" class="form-control input-sm" placeholder="Unit" value=""/>
        </div>
        <div class="col-md-3">
            <input type="text" name="contact_address[[template_id]][streetnumber]" class="form-control input-sm" placeholder="Street Number" value=""/>
        </div>
        <div class="col-md-3">
            <input type="text" name="contact_address[[template_id]][streetname]" class="form-control input-sm" placeholder="Street Name" value=""/>
        </div>
        <div class="col-md-3">
            <select class="form-control selectpicker" name="contact_address[[template_id]][streettype]" style="width:100%;">
                <option disabled selected value>Street Type</option>
                <?php
                foreach($contact->streetTypes as $value)
                {
                    echo '<option value="'.$value.'" '.'>'.$value.'</option>';
                }
                ?>
            </select>
        </div>
    </div>
    <div class="row form-group form-group-sm">
        <div class="col-md-3">
            <input type="text" name="contact_address[[template_id]][suburb]" class="form-control input-sm" placeholder="Suburb" value=""/>
        </div>
        <div class="col-md-3">
            <select class="form-control selectpicker" name="contact_address[[template_id]][state]" style="width:100%;">
                <option disabled selected value>State</option>
                <?php
                foreach($contact->addressState as $value)
                {
                    echo '<option value="'.$value.'" '.'>'.$value.'</option>';
                }
                ?>
            </select>
        </div>
        <div class="col-md-3">
            <input type="text" name="contact_address[[template_id]][postcode]" class="form-control input-sm" placeholder="Postcode" value=""/>
        </div>
        <div class="col-md-3">
            <input type="text" name="contact_address[[template_id]][country]" class="form-control input-sm" placeholder="Country" value=""/>
        </div>
    </div>
    <?php if($i > 0){?>
        <div class="row form-group form-group-sm">
            <div class="pull-right">
                <a class="btn btn-danger btn-xs btn-delete" onclick="deleteAddress([template_id])"><i class="fa fa-trash-o"></i> Delete</a>
            </div>
        </div>
    <?php }?>
</div>
<script>
    $(document).ready(function(){
        whenLoadEditForm();
    });
</script>
