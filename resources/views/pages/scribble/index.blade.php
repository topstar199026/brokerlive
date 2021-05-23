@extends('layouts.dashboard')
@section('content')
<div id="scribble-wrapper" class="scribble-wrapper wrapper wrapper-content animated fadeInRight">
    <div class="ibox scribble-search-box">
        <div class="ibox-content">
            <form action="search" method="get">
                <div class="input-group">
                    <input id="scribble-search-input" type="text" placeholder="Search for anything" class="input form-control" name="term" pattern=".{5,}" required title="Please input 5 characters minimum">
                    <span class="input-group-btn">
                        <button type="submit" class="btn btn-primary" data-action="search"> <i class="fa fa-search"></i> Search</button>
                    </span>
                </div>
            </form>
            <a id="new-scribble-category" class="btn btn-info btn-new-category" data-action="new-category"><i class="fa fa-plus-circle"></i> New Category</a>
        </div>
    </div>

    <div id="scribble-list" class="scribble-list">
        <div class="sk-spinner sk-spinner-wandering-cubes">
            <div class="sk-cube1"></div>
            <div class="sk-cube2"></div>
        </div>
    </div>
</div>
@endsection