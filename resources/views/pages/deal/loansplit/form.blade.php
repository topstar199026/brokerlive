<div class="brokerlive-form">
    <form name="splitForm" method="post" action="/loansplit/createloanentry" class="form-horizontal">
        {{ csrf_field() }}
        <input type="hidden" name="id" value="{{$split->id ?? ''}}"/>
        <!-- <input type="hidden" name="deal_id" value="{{$split->deal_id ?? ''}}"/> -->
        <div class="row">
            <div class="col-sm-6">
                <div class="row form-group form-group-sm">
                    <label class="col-md-2 control-label">Loan #</label>
                    <div class="col-md-10">
                        <select name="loan_number" class="form-control selectpicker">
                            <option value="0" disabled selected>Select Loan Number</option>
                            @for ($i = 1 ; $i <= 10 ; $i++)
                            <option value="{{$i}}" {{($split && $split->loan_number == $i) ? 'selected' : ''}} >
                            {{$i}}
                            </option>
                            @endfor 
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="row form-group form-group-sm">
                    <label class="col-md-2 control-label">Split #</label>
                    <div class="col-md-10">
                        <select name="split_number" class="form-control selectpicker">
                            <option value="0" disabled selected>Select Loan Number</option>
                            @for ($i = 1 ; $i <= 10 ; $i++)
                            <option value="{{$i}}" {{($split && $split->split_number == $i) ? 'selected' : ''}} >
                            {{$i}}
                            </option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="row form-group form-group-sm">
            <div class="col-md-6">
                <select name="lender_id" class="form-control selectpicker">
                @if(isset($lenders))
                    <option disabled selected value>Select Lender</option>
                    @foreach ($lenders as $keyLender => $lender)
                    @if($lender->name == 'OTHER')
                        @php
                        $otherId = $lender->id;
                        @endphp
                    @else
                    <option value="{{$lender->id}}" {{($split && $split->lender_id === $lender->id) ? 'selected' : '' }}>
                    {{$lender->name}}
                    </option>
                    @endif
                    @endforeach
                    @if($otherId != -1)
                    <option value="{{$otherId}}" {{($split && $split->lender_id == $otherId) ? 'selected' : ''}}>OTHER</option>
                    @endif
                @endif
                </select>
                <input type="hidden" value="{{$otherId}}" name="other_lender_id"/>
                <input  type="text" style="margin-top: 5px; display : {{($split && $split->lender_id == $otherId) ? 'block' : 'none'}};" class="form-control input-sm" name="other_lender" placeholder="Specify Lender" value="{{$split && $split->lender_id == $otherId? $split->lender : ''}}" />
            </div>
            <div class="col-md-6">
                <input  type="text" class="form-control input-sm " name="filenumber" placeholder="File number" data-toggle="tooltip" title="File number" value="{{$split->filenumber?? ''}}" />
            </div>
        </div>
        <div class="row form-group form-group-sm">
            <div class="col-md-6">
                <div class="input-group input-group-sm">
                    <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                    <input type="text" class="form-control input-sm" name="subloan" placeholder="Current Loan Amount" data-toggle="tooltip" title="Current Loan Amount" value="{{$split ? $split->_subloan() : ''}}" />
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control input-sm" name="lvr" placeholder="LVR" data-toggle="tooltip" title="LVR" value="{{$split->lvr ?? ''}}" />
                    <span class="input-group-addon" title="LVR"><i class="fa fa-percent" style="right:25px;"></i></span>
                </div>
            </div>
            <div class="col-md-3 checkbox-inline" style="padding-top: 11px; padding-left: 10px;">
                <input type="checkbox" name="lmi" value="1" {{($split && $split->lmi == '1') ? 'checked' : ''}}/> inc. LMI
            </div>
        </div>
        <div class="row form-group form-group-sm">
            <div class="col-md-6">
                <div class="input-group input-group-sm date datepicker">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    <input type="text" class="form-control input-sm" name="financeduedate" placeholder="Finance Due Date" value="{{$split ? $split->_financeduedate() : ''}}" />
                </div>
            </div>
            <div class="col-md-6">
                <div class="input-group input-group-sm date datepicker">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    <input type="text" class="form-control input-sm" name="settlementdate" placeholder="Settlement Date" value="{{$split ? $split->_settlementdate() : ''}}" />
                </div>
            </div>
        </div>
        <div class="row form-group form-group-sm">
            <label class="col-md-2 control-label">Referred by:</label>
            <div class="col-md-10">
                <input type="hidden" name="referrer_id" value="{{$split->referrer_id ?? ''}}" />
                <input type="text" name="referrer" class="form-control input-sm autocomplete-name" value="{{ ($split && $split->referrer && $split->referrer->id != '') ? '#'.$split->referrer->id.' '.$split->referrer->firstname.' '.$split->referrer->lastname : '' }}" >
            </div>
        </div>
        <div class="row form-group form-group-sm">
            <label class="col-md-2 control-label">Applicants:</label>
            <div class="col-md-10">
                <div class="applicant-error input-error-msg"></div>
                <div class="applicant-item new-applicant-item">
                    <input type="hidden" name="new_applicant_id" />
                    <input type="text" name="new_applicant" class="form-control input-sm autocomplete-applicant" >
                </div>
                @if($split)
                @foreach ($split->applicants as $applicant)
                <div class="applicant-item input-group input-group-sm">
                    <input type="hidden" name="applicant_ids[]" value="{{$applicant->id}}" />
                    <input class="applicant-value form-control input-sm" type="text" value="{{'#'.$applicant->id.' '.$applicant->firstname.' '.$applicant->lastname}}" readonly/>
                    <span class="input-group-btn">
                        <button type="button" class="btn-remove-applicant btn btn-danger">
                            <i class="fa fa-trash" aria-label="delete"></i> Delete
                        </button>
                    </span>
                </div>
                @endforeach
                @endif
                <div class="applicant_names"></div>
            </div>
        </div>
        @for ($i = 0 ; $i < count($tags) ; $i++)
        <div class="row form-group form-group-sm">
            @foreach ($tags[$i] as $keyTag => $valueTag)
            <div class="col-md-3">
                <input type="checkbox" name="tags[]" value="{{$keyTag}}" {{($split && $split->hasTag($keyTag)) ? 'checked' : ''}}/>
                &nbsp;{{$valueTag}}
            </div>
            @endforeach
        </div>
        @endfor
        <div class="row form-group form-group-sm">
            <label class="col-md-2 control-label">Product:</label>
            <div class="col-md-10">
                <input  type="text" class="form-control input-sm" name="product" value="{{$split->product ?? ''}}" />
            </div>
        </div>
        <div class="row form-group form-group-sm">
            <label class="col-md-2 control-label">Split Status:</label>
            <div class="col-md-10">
                <select name="documentstatus_id" class="form-control selectpicker">
                    @foreach ($splitStatus as $status)
                    <option value="{{$status->id}}" {{($split && $split->documentstatus_id == $status->id) ? 'selected' : ''}}>
                        {{$status->name}}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row form-group form-group-sm">
            <label class="col-md-2 control-label">Initial Appt:</label>
            <div class="col-md-10">
                <div class="input-group input-group-sm date datepicker">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    <input type="text" class="form-control input-sm" name="initial_appointment" placeholder="" value="{{$split ? $split->_initial_appointment() : ''}}" />
                </div>
            </div>
        </div>
        <div class="row form-group form-group-sm">
            <label class="col-md-4 col-md-offset-2 text-center">Date</label>
            <label class="col-md-3 text-center">Trail Value</label>
            <label class="col-md-3 text-center">Upfront Value</label>
        </div>
        <div class="row form-group form-group-sm">
            <label class="col-md-2 control-label">Submitted</label>
            <div class="col-md-4">
                <div class="input-group input-group-sm date datepicker">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    <input type="text" class="form-control input-sm" name="submitted" value="{{$split ? $split->_submitted() : ''}}" />
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-addon"><i class="fa fa-dollar" style="left:25px;"></i></span>
                    <input type="text" class="form-control input-sm" name="submittedtrail" value="{{$split ? $split->_submittedtrail() : ''}}" />
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-addon"><i class="fa fa-dollar" style="left:25px;"></i></span>
                    <input type="text" class="form-control input-sm" name="submittedvalue" value="{{$split ? $split->_submittedvalue() : ''}}" />
                </div>
            </div>
        </div>
        <div class="row form-group form-group-sm">
            <label class="col-md-2 control-label">AIP</label>
            <div class="col-md-4">
                <div class="input-group input-group-sm date datepicker">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    <input type="text" class="form-control input-sm" name="aip" value="{{$split ? $split->_aip() : ''}}" />
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-addon"><i class="fa fa-dollar" style="left:25px;"></i></span>
                    <input type="text" class="form-control input-sm" name="aiptrail" value="{{$split ? $split->_aiptrail() : ''}}" />
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-addon"><i class="fa fa-dollar" style="left:25px;"></i></span>
                    <input type="text" class="form-control input-sm" name="aipvalue" value="{{$split ? $split->_aipvalue() : ''}}" />
                </div>
            </div>
        </div>
        <div class="row form-group form-group-sm">
            <label class="col-md-2 control-label">Pending</label>
            <div class="col-md-4">
                <div class="input-group input-group-sm date datepicker" >
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    <input type="text" class="form-control input-sm" name="conditional" value="{{$split ? $split->_conditional() : ''}}" />
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-addon"><i class="fa fa-dollar" style="left:25px;"></i></span>
                    <input type="text" class="form-control input-sm" name="conditionaltrail" value="{{$split ? $split->_conditionaltrail() : ''}}" />
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-addon"><i class="fa fa-dollar" style="left:25px;"></i></span>
                    <input type="text" class="form-control input-sm" name="conditionalvalue" value="{{$split ? $split->_conditionalvalue() : ''}}" />
                </div>
            </div>
        </div>
        <div class="row form-group form-group-sm">
            <label class="col-md-2 control-label">Approved</label>
            <div class="col-md-4">
                <div class="input-group input-group-sm date datepicker">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    <input type="text" class="form-control input-sm" name="approved" value="{{$split ? $split->_approved() : ''}}" />
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-addon"><i class="fa fa-dollar" style="left:25px;"></i></span>
                    <input type="text" class="form-control input-sm" name="approvedtrail" value="{{$split ? $split->_approvedtrail() : ''}}" />
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-addon"><i class="fa fa-dollar" style="left:25px;"></i></span>
                    <input type="text" class="form-control input-sm" name="approvedvalue" value="{{$split ? $split->_approvedvalue() : ''}}" />
                </div>
            </div>
        </div>
        <div class="row form-group form-group-sm">
            <label class="col-md-2 control-label">Settled</label>
            <div class="col-md-4">
                <div class="input-group input-group-sm date datepicker">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    <input type="text" class="form-control input-sm" name="settled" value="{{$split ? $split->_settled() : ''}}" />
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-addon"><i class="fa fa-dollar" style="left:25px;"></i></span>
                    <input type="text" class="form-control input-sm" name="settledtrail" value="{{$split ? $split->_settledtrail() : ''}}" />
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-addon"><i class="fa fa-dollar" style="left:25px;"></i></span>
                    <input type="text" class="form-control input-sm" name="settledvalue" value="{{$split ? $split->_settledvalue() : ''}}" />
                </div>
            </div>
        </div>
        <div class="row form-group form-group-sm">
            <label class="col-md-2 control-label">Discharged</label>
            <div class="col-md-4">
                <div class="input-group input-group-sm date datepicker">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    <input type="text" class="form-control input-sm" name="discharged" value="{{$split ? $split->_discharged() : ''}}" />
                </div>
            </div>
        </div>
        <div class="row form-group form-group-sm">
            <label class="col-md-2 control-label">Comm. Paid</label>
            <div class="col-md-3 col-md-offset-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-addon">
                        <input type="checkbox" data-toggle="tooltip" title="Trail Value applicable" name="commission_trail_applicable" {{($split && $split->commission_trail_applicable == 1) ? 'checked' : ''}}  aria-label="...">
                    </span>
                    <input type="text" class="form-control input-sm date datepicker" name="commission_paid_trail" {{($split && $split->commission_trail_applicable == 1) ? '' : 'disabled'}} value="{{$split ? $split->_commission_paid_trail() : ''}}" />
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-addon">
                        <input data-toggle="tooltip" title="Upfront Value applicable" type="checkbox" name="commission_value_applicable" {{($split && $split->commission_value_applicable == 1) ? 'checked' : ''}} aria-label="...">
                    </span>
                    <input type="text" class="form-control input-sm date datepicker" name="commission_paid_value" {{($split && $split->commission_value_applicable == 1) ? '' : 'disabled'}} value="{{$split ? $split->_commission_paid_value() : ''}}" />
                </div>
            </div>
        </div>
        <div class="row form-group form-group-sm">
            <label class="col-md-2 control-label">Client Type:</label>
            <div class="col-md-3 col-md-offset-1 checkbox-inline">
                <input type="checkbox" name="hotclient" value="1" {{$split && $split->hotclient ? 'checked' : '' }} />
                &nbsp; Hot
            </div>
            <div class="col-md-3 checkbox-inline">
                <input type="checkbox" name="committedclient" value="1" {{$split && $split->committedclient ? 'checked' : '' }} />
                &nbsp; Committed
            </div>
        </div>
        <div class="row form-group form-group-sm">
            <div class="col-md-4 col-md-offset-2 checkbox-inline">
                <input type="checkbox" name="whiteboardhide" value="1" {{$split && $split->whiteboardhide ? 'checked' : ''}} />
                &nbsp; Not proceeding
            </div>
            <div class="col-md-4">
                <div class="input-group input-group-sm date notproceeding">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    <input type="text" class="form-control input-sm" name="notproceeding" value="{{$split ? $split->_notproceeding() : ''}}" />
                </div>
            </div>
        </div>
        <div class="form-actions clearfix">
            <div class="pull-right">
                <button class="btn btn-info btn-xs btn-cancel"><i class="fa fa-times-circle"></i> Cancel</button>
                <button type="submit" class="btn btn-primary btn-xs btn-save"><i class="fa fa-save"></i> Save</button>
                @if($split && $split->id !== '')
                <button class="btn btn-danger btn-xs btn-delete" href="{{url('loansplit/delete').'/'.$split->id}}"><i class="fa fa-trash-o"></i> Delete</button>
                @endif
            </div>
        </div>
    </form>
</div>