
@php
$cssPath=str_replace('contact','contacts',$cssPath);
$jsPath=str_replace('contact','contacts',$jsPath);
@endphp
@extends('layouts.dashboard')
@section('content')
{{$status}}
<div class="wrapper wrapper-content  animated fadeInRight">
    <div class="row">
        <div class="col-sm-8">
            <div class="ibox float-e-margins">
                <div class="ibox-content" style="min-height: 950px;">
                    <form action="/contact/search" method="GET" id="contact-search">
                    <div class="input-group">
                            <input type="text" placeholder="Search Contacts " class="input form-control" name="s">
                            <span class="input-group-btn">
                                    <button type="submit" class="btn btn btn-primary"> <i class="fa fa-search"></i> Search</button>
                            </span>
                    </div>
                    </form>
                    <div class="clients-list">
                        <ul class="nav nav-tabs">
                            @php
                            print('<li class="active"><a data-toggle="tab" href="#0" aria-expanded="true">All</a></li>');
                            foreach($contact_types as $group)
                            {
                                print('<li><a data-toggle="tab" href="#'.$group->id.'">'.$group->name.'</a></li>');
                            }
                            @endphp
                        </ul>
                        <div class="tab-content">
                            @php
                            $root=(object) array(
                                'id'=>0,
                                'name'=>'All'
                            );
                                $first = true;
                                $f=0;
                                $arr=array();
                                foreach($contact_types as $group)
                                {
                                    if($f==0){
                                        $arr[]=$root;
                                        $f=1;
                                    }
                                    $arr[]=$group;
                                }
                                $contact_types=$arr;
                                foreach($contact_types as $group)
                                {
                                    $class = "";
                                    if ($first)
                                    {
                                        $class = " active";
                                        $first = false;
                                    }
                                @endphp
                                <div id="{{$group->id}}" class="contact tab-pane{{$class}}">
                                    <div class="">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-hover table-contact" contact_type="{{$group->id}}" id="table-contact{{$group->id}}"  >
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Name</th>
                                                        <th>Company</th>
                                                        <th>Phone</th>
                                                        <th>Email</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @php
                                }
                            @endphp
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="ibox float-e-margins">
                <div class="ibox-content" >
                    <div class="contact-form">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection