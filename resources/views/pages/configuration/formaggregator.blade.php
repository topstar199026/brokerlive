@extends('layouts.configuration')
@section('content')
        <div class="ibox float-e-margins" style="width:100%;">
            @if (isset($aggregator)&&isset($aggregator->id))
                <div class="ibox-title" style="border-top-width: 0px;">
                    <div class="ibox-tools" style="float:left;">
                        <h2 style="margin-bottom: 10px">Edit - {{$aggregator->name}}</h2>
                    </div>
                </div>
            @else
                <div class="ibox-title" style="border-top-width: 0px;">
                    <div class="ibox-tools" style="float:left;">
                        <h2 style="margin-bottom: 10px">Create Aggregator</h2>
                    </div>
                </div>
            @endif

            <div class="wrapper wrapper-content  animated fadeInRight" style="padding: 0px;">
                <div class="ibox-content ibox">
                    @if(isset($errors)&&$errors != '')
                        <div class="alert alert-warning col-md-5 col-md-offset-2">
                            <?=$errors?>
                        </div>
                    @endif
                    <form class="form-horizontal" method="post" role="form">
                        <div class="form-group row">
                            <label class="col-md-3 control-label">Name</label>
                            <div class="col-md-4">
                                <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                                <input type="text" class="form-control onblur" id="name" name="name" placeholder="Name" value="{{isset($aggregator)?$aggregator->name:''}}" required />
                            </div>
                        </div>
                        <div class="form-group form-buttons row">
                            <div class="col-md-6 col-md-offset-3">
                                <button type="submit" class="btn btn-primary" name="btnSubmit" value="save"><span class="fa fa-save"></span> Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
@endsection