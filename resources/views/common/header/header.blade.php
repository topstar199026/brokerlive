<div class="row border-bottom">
    <style>
        @media screen and (min-width: 768px){
            .dropdown.dropdown-lg .dropdown-menu {
                min-width: 828px;
            }
        }
    </style>
    <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
            {{-- <form role="search" class="navbar-form-custom" action="/search" method="get">
                <div class="form-group">
                    <input type="text" placeholder="Search for something..." class="form-control" name="top-search" id="top-search">
                </div>
            </form> --}}
            <form class="form-horizontal" role="form" action="/search" method="get">
                <div class="container-search" style="width: 1000px;padding-top: 11px;">
                    <div class="row">
                        <div class="col-md-11">
                            <div class="input-group" id="adv-search">
                                <input name="q" type="text" class="form-control" placeholder="Search for something..." value="{{$q ?? ''}}" />
                                <div class="input-group-btn">
                                    <div class="btn-group" role="group" style="font-size: 15px;">
                                        <div class="dropdown dropdown-lg">
                                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false" style="height: 36px;"><span class="caret" style="display: none;"></span></button>
                                            <div class="dropdown-menu dropdown-menu-right" role="menu" style="right: 0;left: auto;">
                                                <form class="form-horizontal" role="form" action="/search" method="get">
                                                    <!--<div class="form-group">
                                                        <label for="filter">Filter by</label>
                                                        <select class="form-control">
                                                            <option value="0" selected>All Snippets</option>
                                                            <option value="1">Featured</option>
                                                            <option value="2">Most popular</option>
                                                            <option value="3">Top rated</option>
                                                            <option value="4">Most commented</option>
                                                        </select>
                                                    </div>-->
                                                    <div class="form-group">
                                                        <label for="contain">Phone</label>
                                                        <input class="form-control" name="q-phone" type="text" value="{{$q_phone ?? ''}}"/>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
                                                </form>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
        </div>
        <ul class="nav navbar-top-links navbar-right">
            <li style="padding: 20px">
                <span class="m-r-sm text-muted welcome-message">Welcome to Brokerlive.</span>
            </li>
            {{-- <li class="dropdown">
                <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                    <i class="fa fa-envelope"></i>  <span class="label label-warning">16</span>
                </a>
                <ul class="dropdown-menu dropdown-messages dropdown-menu-right">
                    <li>
                        <div class="dropdown-messages-box">
                            <a class="dropdown-item float-left" href="profile.html">
                                <img alt="image" class="rounded-circle" src="/img/a7.jpg">
                            </a>
                            <div class="media-body">
                                <small class="float-right">46h ago</small>
                                <strong>Mike Loreipsum</strong> started following <strong>Monica Smith</strong>. <br>
                                <small class="text-muted">3 days ago at 7:58 pm - 10.06.2014</small>
                            </div>
                        </div>
                    </li>
                    <li class="dropdown-divider"></li>
                    <li>
                        <div class="dropdown-messages-box">
                            <a class="dropdown-item float-left" href="profile.html">
                                <img alt="image" class="rounded-circle" src="/img/a4.jpg">
                            </a>
                            <div class="media-body ">
                                <small class="float-right text-navy">5h ago</small>
                                <strong>Chris Johnatan Overtunk</strong> started following <strong>Monica Smith</strong>. <br>
                                <small class="text-muted">Yesterday 1:21 pm - 11.06.2014</small>
                            </div>
                        </div>
                    </li>
                    <li class="dropdown-divider"></li>
                    <li>
                        <div class="dropdown-messages-box">
                            <a class="dropdown-item float-left" href="profile.html">
                                <img alt="image" class="rounded-circle" src="/img/profile.jpg">
                            </a>
                            <div class="media-body ">
                                <small class="float-right">23h ago</small>
                                <strong>Monica Smith</strong> love <strong>Kim Smith</strong>. <br>
                                <small class="text-muted">2 days ago at 2:30 am - 11.06.2014</small>
                            </div>
                        </div>
                    </li>
                    <li class="dropdown-divider"></li>
                    <li>
                        <div class="text-center link-block">
                            <a href="mailbox.html" class="dropdown-item">
                                <i class="fa fa-envelope"></i> <strong>Read All Messages</strong>
                            </a>
                        </div>
                    </li>
                </ul>
            </li>
            <li class="dropdown">
                <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                    <i class="fa fa-bell"></i>  <span class="label label-primary">8</span>
                </a>
                <ul class="dropdown-menu dropdown-alerts">
                    <li>
                        <a href="mailbox.html" class="dropdown-item">
                            <div>
                                <i class="fa fa-envelope fa-fw"></i> You have 16 messages
                                <span class="float-right text-muted small">4 minutes ago</span>
                            </div>
                        </a>
                    </li>
                    <li class="dropdown-divider"></li>
                    <li>
                        <a href="profile.html" class="dropdown-item">
                            <div>
                                <i class="fa fa-twitter fa-fw"></i> 3 New Followers
                                <span class="float-right text-muted small">12 minutes ago</span>
                            </div>
                        </a>
                    </li>
                    <li class="dropdown-divider"></li>
                    <li>
                        <a href="grid_options.html" class="dropdown-item">
                            <div>
                                <i class="fa fa-upload fa-fw"></i> Server Rebooted
                                <span class="float-right text-muted small">4 minutes ago</span>
                            </div>
                        </a>
                    </li>
                    <li class="dropdown-divider"></li>
                    <li>
                        <div class="text-center link-block">
                            <a href="notifications.html" class="dropdown-item">
                                <strong>See All Alerts</strong>
                                <i class="fa fa-angle-right"></i>
                            </a>
                        </div>
                    </li>
                </ul>
            </li> --}}


            <li>
                <a href="/logout">
                    <i class="fa fa-sign-out"></i> Log out
                </a>
            </li>
            {{-- <li>
                <a class="right-sidebar-toggle">
                    <i class="fa fa-tasks"></i>
                </a>
            </li> --}}
        </ul>

    </nav>
</div>
