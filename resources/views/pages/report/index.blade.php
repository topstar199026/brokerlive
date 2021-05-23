@extends('layouts.dashboard')
@section('content')
<div id="reports-wrapper" class="reports-wrapper wrapper wrapper-content animated fadeInRight">
    <div class="ibox">
        <div class="ibox-content">
            <div id="reports-list" class="reports-list">
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#tab-NestedReferrer">Referreral Tree</a></li>
                    </ul>
                    <div class="tab-content">
                        <div id="tab-NestedReferrer" class="tab-pane active">
                            <div id="NestedReferrerContainer" class="panel-body">
                                <p><button id="NestedReferrerRefresh" disabled="true" class="btn btn-warning">Refresh report</button>
                                <span id="NestedReferrerStatus" class="refresh-status"></span></p>
                                <p><strong class="text-info">Please note: This report may take 2 - 10 minutes to update. Please continue working during this time.</strong></p>
                                <div id="NestedReferrerFilter" class="report-filter">
                                    <label class="report-search">Search:<input id="NestedReferrerSearch" name="searchQuery" type="text" placeholder="Minimum 3 characters..."></label>
                                    <button id="NestedReferrerSearchClear">Clear search</button>
                                </div>
                                <div id="NestedReferrerLoading" class="report-loading">
                                    Loading reports
                                    <div class="sk-spinner sk-spinner-wandering-cubes">
                                        <div class="sk-cube1"></div>
                                        <div class="sk-cube2"></div>
                                    </div>
                                </div>
                                <div id="NestedReferrerTree" class="report-tree">
                                </div>
                            </div>
                        </div>
                        <div id="tab-ReferrerTree" class="tab-pane">
                            <div id="ReferrerTreeContainer" class="panel-body">
                                <button id="ReferrerTreeRefresh" disabled="true" class="btn btn-warning">Refresh tree</button>
                                <span id="ReferrerTreeStatus"></span>
                                <div id="ReferrerTreeLoading" class="report-loading">
                                    Loading reports
                                    <div class="sk-spinner sk-spinner-wandering-cubes">
                                        <div class="sk-cube1"></div>
                                        <div class="sk-cube2"></div>
                                    </div>
                                </div>
                                <div id="ReferrerTree" class="report-table">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection