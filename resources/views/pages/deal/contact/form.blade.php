<div class="loansplit-form">
    <form name="contactForm" method="post" action="/dealContact/createcontact" class="form-horizontal">
        @csrf
        <input type="hidden" name="id" value="{{$contact->id ?? ''}}"/>
        <input type="hidden" name="user_id" value="{{$userInfo->id}}"/>
        <input type="hidden" name="contact_id" value="{{$contact->contact_id ?? ''}}"/>
        <div class="row form-group form-group-sm">
            <label class="col-md-2 control-label">Name</label>
            <div class="col-md-2">
                <select type="text" class="form-control selectpicker" name="persontitle_id" style="width:100%;">
                    @foreach ($titles as $key => $title)
                        @if ($contact && $contact->id && $contact->id !== '' && $contact->persontitle_id === $title->id)
                    <option value="{{$title->id}}" selected>{{$title->name}}</option>
                        @else
                    <option value="{{$title->id}}">{{$title->name}}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="firstname" class="form-control input-sm autocomplete-name" placeholder="Firstname" value="{{$contact->firstname ?? ''}}"/>
            </div>
            <div class="col-md-2">
                <input type="text" name="middlename" class="form-control input-sm" placeholder="Middlename" value="{{$contact->middlename ?? ''}}"/>
            </div>
            <div class="col-md-3">
                <input type="text" name="lastname" class="form-control input-sm" placeholder="Surname" value="{{$contact->lastname ?? ''}}"/>
            </div>
        </div>
        <div class="row form-group form-group-sm">
            <label class="col-md-2 control-label">Type</label>
            <div class="col-md-10">
                <select class="form-control selectpicker" name="contacttype_id" style="width:100%;">
                    @foreach ($contactTypes as $key => $type)
                        @if ($contact && $contact->id && $contact->id !== '' && $contact->contacttype_id === $type->id)
                    <option value="{{$type->id}}" selected>{{$type->name}}</option>
                        @else
                    <option value="{{$type->id}}">{{$type->name}}</option>
                        @endif
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row form-group form-group-sm">
            <label class="col-md-2 control-label">Company</label>
            <div class="col-md-10">
                <input type="text" name="company" class="form-control input-sm" value="{{$contact->company ?? ''}}"/>
            </div>
        </div>
        <div class="row form-group form-group-sm">
            <label class="col-md-2 control-label">Mobile</label>
            <div class="col-md-4">
                <input type="text" name="phonemobile" class="form-control input-sm" value="{{$contact->phonemobile ?? ''}}"/>
            </div>
            <label class="col-md-2 control-label">Work</label>
            <div class="col-md-4">
                <input type="text" name="phonework" class="form-control input-sm" value="{{$contact->phonework ?? ''}}"/>
            </div>
        </div>
        <div class="row form-group form-group-sm">
            <label class="col-md-2 control-label">Home</label>
            <div class="col-md-4">
                <input type="text" name="phonehome" class="form-control input-sm" value="{{$contact->phonehome ?? ''}}"/>
            </div>
            <label class="col-md-2 control-label">Fax</label>
            <div class="col-md-4">
                <input type="text" name="phonefax" class="form-control input-sm" value="{{$contact->phonefax ?? ''}}"/>
            </div>
        </div>
        <div class="row form-group form-group-sm">
            <label class="col-md-2 control-label">Email</label>
            <div class="col-md-10">
                <input type="text" name="email" class="form-control input-sm" value="{{$contact->email ?? ''}}"/>
            </div>
        </div>
        <div class="row form-group form-group-sm">
            <label class="col-md-2 control-label">Address</label>
            <div class="col-md-10">
                <input type="text" name="address1" class="form-control input-sm" value="{{$contact->address1 ?? ''}}"/>
            </div>
        </div>
        <div class="row form-group form-group-sm">
            <label class="col-md-2 control-label">&nbsp;</label>
            <div class="col-md-10">
                <input type="text" name="address2" class="form-control input-sm" value="{{$contact->address2 ?? ''}}"/>
            </div>
        </div>
        <div class="row form-group form-group-sm">
            <label class="col-md-2 control-label">&nbsp;</label>
            <div class="col-md-4">
                <input type="text" name="suburb" class="form-control input-sm" placeholder="Suburb" value="{{$contact->suburb ?? ''}}"/>
            </div>
            <div class="col-md-4">
                <input type="text" name="state" class="form-control input-sm" placeholder="State" value="{{$contact->state ?? ''}}"/>
            </div>
            <div class="col-md-2">
                <input type="text" name="postcode" class="form-control input-sm" placeholder="Postcode" value="{{$contact->postcode ?? ''}}"/>
            </div>
        </div>
        <div class="row form-group form-group-sm">
            <label class="col-md-2 control-label">Notes</label>
            <div class="col-md-10">
                <input type="text" name="notes" class="form-control input-sm" value="{{$contact->notes ?? ''}}"/>
            </div>
        </div>
        <div class="form-actions clearfix">
            <div class="pull-right">
                <button class="btn btn-info btn-xs btn-cancel" href="{{ url('contact/list') }}"><i class="fa fa-times-circle"></i> Cancel</button>
                <button type="submit" class="btn btn-primary btn-xs btn-save"><i class="fa fa-save"></i> Save</button>
                @if ($contact && $contact->id && $contact->id !== '')
                <a class="btn btn-danger btn-xs btn-delete" href="{{ url('contact/delete') }}/{{$contact->id}}"><i class="fa fa-trash-o"></i> Delete</a>
                @endif
            </div>
        </div>
    </form>
</div>
