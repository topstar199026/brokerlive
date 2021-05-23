@extends('layouts.dashboard')

@section('content')
<div class="wrapper wrapper-content">
    <div class="deal-summary">
        <div class="col-lg-12">
            <input type="hidden" name="deal-id" id="deal-id" value="{{$deal->id}}" />
            <input type="hidden" name="user_id" id="user_id" value="{{$userInfo->id}}"/>
            <input type="hidden" name="username" id="username" value="{{$userInfo->username}}"/>
            <input type="hidden" name="deal_user_id" id="deal_user_id" value="{{$deal->user_id}}"/>
        </div>
        <div class="row row-deal-header">
            <div class="col-md-6">
                <div class="deal-name">
                    <div class="deal-edit">
                        <h1 class="name">{{$deal->name}}</h1> <a class="btn btn-default btn-outline btn-xs btn-nameedit" title="Edit"><i class="fa fa-pencil"></i> Edit</a>
                        <p class="broker-name">{{$deal->broker->firstname}} {{$deal->broker->lastname}}</p>
                    </div>
                    <div class="deal-save" style="display:none;">
                        <input type="text" placeholder="Deal Name" name="deal-name"  id="deal-name" value="{{$deal->name}}" />
                        <button class="btn btn-default btn-xs btn-outline btn-namesave" title="Save"><i class="fa fa-save"></i></button>
                        <button class="btn btn-default btn-xs btn-outline btn-namecancel" title="Cancel"><i class="fa fa-times"></i></button>
                    </div>
                </div>
            </div>
            <div class="col-md-6 deal-tools">
                <div class="input-group input-group-sm tool tool-status">
                    <span class="input-group-addon"><i class="fa fa-globe" title="Status"></i></span>
                    @include('pages.deal.status.select')
                </div>
                <div class="tool tool-clone">
                    <button class="btn btn-xs btn-clone" href="#" title="Clone"><i class="fa fa-copy"></i></button>
                </div>
                <div class="input-group input-group-sm tool tool-linked">
                    <span class="input-group-addon"><i class="fa fa-link"></i></span>
                    <div class="input-group-btn">
                        <button data-toggle="dropdown" class="btn btn-white dropdown-toggle" type="button"><span class="button-text">{{$deal->name}}</span> <span class="caret"></span></button>
                        <div class="dropdown-menu deal-tree">
                            <ul>
                                {!! $dealTrees !!}     
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <!-- Contacts -->
            <div class="ibox ibox-brokerlive widget-contacts">
                <div class="ibox-title">
                    <h5>Contacts</h5>
                    <div class="tools">
                        <a class="btn btn-default btn-xs btn-outline link-new" href="/dealContact/edit"><i class="fa fa-plus"></i> Add</a>
                    </div>
                </div>
                <div class="ibox-content no-padding">
                    <div id="contactListDiv">
                        @include('pages.deal.contact.list', ['dealId' => $deal->id])
                    </div>
                </div>
                <div class="ibox-content form-edit" style="display:none;"></div>
            </div>
            <!-- Splits -->
            <div class="ibox ibox-brokerlive widget-splitloan">
                <div class="ibox-title">
                    <h5>Loan Splits</h5>
                    <div class="tools">
                        <a class="btn btn-default btn-xs btn-outline link-new" href="/loansplit/edit"><i class="fa fa-plus"></i> Add</a>
                    </div>
                </div>
                <div class="ibox-content no-padding">
                    <div id="loanSplitListDiv" class="loan-split-list">
                        @include('pages.deal.loansplit.list', ['dealId' => $deal->id])
                    </div>
                </div>
                <div class="ibox-content form-edit" style="display:none;"></div>
            </div>
            <!-- Notes -->
            <div class="ibox ibox-brokerlive">
                <div class="ibox-title">
                    <h5>General Notes</h5>
                    <div class="tools buttons-notes">
                        <div class="notes-edit">
                            <a class="btn btn-default btn-xs btn-outline btn-editnote" title="Edit"><i class="fa fa-pencil"></i> Edit</a>
                        </div>
                        <div class="notes-save" style="display:none;">
                            <a class="btn-savenote" title="Save"><i class="fa fa-save"></i></a>
                            <a class="btn-cancelnote" title="Cancel"><i class="fa fa-times"></i></a>
                        </div>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="deal-notes">
                        {!!$deal->notes!!}
                    </div>
                </div>
            </div>
            <!-- file management -->
            <div class="ibox ibox-brokerlive widget-files" data-deal="{{$deal->id}}">
                <div class="ibox-title">
                    <h5>Files</h5>
                    <!-- <div class="tools">
                        <a class="btn btn-default btn-xs btn-outline link-new" id="action_upload" href="javascript:void(0)"><i class="fa fa-paperclip"></i> Attach</a>
                    </div> -->
                </div>
                <div class="ibox-content no-padding">
                    <!-- <div id="drop_file" style="width: 100%;height: 50px;text-align: center;border: 3px dashed #ccc;">
                        Drop files here to upload
                    </div> -->
                    <div id="dropzoneForm" class="dropzone"></div>
                    <div id="fileListDiv" style="padding-top: 10px;padding-bottom: 50px;padding-right: 5px;">
                        
                    </div>
                </div>
                <div class="ibox-content form-edit" style="display:none;"></div>
            </div>
        </div>
        <div class="col-md-6">
            <!-- Reminders -->
            <div class="widget-reminders">
            </div>
            <!-- Journal -->
            <div class="widget-journal">
            </div>
        </div>
    </div>
    <div class="deal-save" style="display:none;">
        <input type="text" placeholder="Deal Name" name="deal-name"  id="deal-name" value="{{$deal->name}}" />
        <button class="btn btn-default btn-namesave" title="Save"><i class="fa fa-save"></i></button>
        <button class="btn btn-default btn-namecancel" title="Cancel"><i class="fa fa-times"></i></button>
    </div>
</div>
@endsection