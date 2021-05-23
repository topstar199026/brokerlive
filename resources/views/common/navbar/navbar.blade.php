<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element">
                    <img alt="image" width="48" height="48" class="rounded-circle" src="/img/avatar/avatar_default.png"/>
                    {{-- <a id="user__name" data-toggle="dropdown" class="dropdown-toggle" href="/configuration/profile"> --}}
                    <a id="user__name" class="dropdown-toggle" href="/configuration/profile">
                        <span class="block m-t-xs font-bold">
                            {{
                                $userInfo && $userInfo->firstname !== '' && $userInfo->lastname !== '' ?
                                $userInfo->firstname.' '.$userInfo->lastname
                                :
                                'No Fullname'
                            }}
                        </span>
                        {{-- <span class="text-muted text-xs block">
                            {{
                                $userInfo && $userInfo->username && $userInfo->username !== '' ?
                                $userInfo->username
                                :
                                'No Username'
                            }}
                            &nbsp;<b class="caret"></b>
                        </span> --}}
                    </a>
                    {{-- <ul class="dropdown-menu animated fadeInRight m-t-xs">
                        <li><a class="dropdown-item" href="profile.html">Profile</a></li>
                        <li><a class="dropdown-item" href="contacts.html">Contacts</a></li>
                        <li><a class="dropdown-item" href="mailbox.html">Mailbox</a></li>
                        <li class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="login.html">Logout</a></li>
                    </ul> --}}
                </div>
                {{-- <div class="logo-element">
                    IN+
                </div> --}}
            </li>

            <!-- User role 1 start -->
            @if($userRole->personalAssistant || $userRole->broker)
            @if($_currentUrl === 'pipeline')
            <li class="active">
            @else
            <li>
            @endif
                <a href="/pipeline"><i class="fa fa-th"></i> <span class="nav-label">Pipeline</span></a>
            </li>
            @if($_currentUrl === 'dashboard')
            <li class="active">
            @else
            <li>
            @endif
                <a href="/dashboard"><i class="fa fa-tachometer"></i> <span class="nav-label">Dashboard</span></a>
            </li>
            @if($_currentUrl === 'panel')
            <li class="active">
            @else
            <li>
            @endif
                <a href="/panel"><i class="fa fa-desktop"></i> <span class="nav-label">Panels</span></a>
            </li>
            @if($_currentUrl === 'reminder')
            <li class="active">
            @else
            <li>
            @endif
                <a href="/reminder"><i class="fa fa-clock-o"></i> <span class="nav-label">Reminders</span></a>
            </li>
            @if($_currentUrl === 'journal')
            <li class="active">
            @else
            <li>
            @endif
                <a href="/journal"><i class="fa fa-book"></i> <span class="nav-label">Journal</span></a>
            </li>
            @if($_currentUrl === 'lead')
            <li class="active">
            @else
            <li>
            @endif
                <a href="/lead"><i class="fa fa-archive"></i> <span class="nav-label">Leads</span></a>
            </li>
            @if(strpos($_currentUrl, 'whiteboard') !== false)
            <li class="active">
            @else
            <li>
            @endif
                <a href="/whiteboard"><i class="fa fa-table"></i> <span class="nav-label">Whiteboard</span></a>
            </li>
            @endif
            <!-- User role 1 end -->


            @if(strpos($_currentUrl, 'team') !== false)
            <li class="active">
            @else
            <li>
            @endif
                <a href="/team"><i class="fa fa-users"></i> <span class="nav-label">Team</span></a>
            </li>
            @if($_currentUrl === 'calendar')
            <li class="active">
            @else
            <li>
            @endif
                <a href="/calendar"><i class="fa fa-calendar-o"></i> <span class="nav-label">Calendar</span></a>
            </li>

            @if($userRole->organisationAdmin)
            @if($_currentUrl === 'gcontact')
            <li class="active">
            @else
            <li>
            @endif
                <a href="/gcontact"><i class="fa fa-list-alt"></i> <span class="nav-label">Global Contacts</span></a>
            </li>
            @endif
            <!-- User role 2 start -->
            @if($userRole->personalAssistant || $userRole->broker)
            @if($_currentUrl === 'contact')
            <li class="active">
            @else
            <li>
            @endif
                <a href="/contact"><i class="fa fa-tags"></i> <span class="nav-label">Contacts</span></a>
            </li>
            {{-- @if($_currentUrl === 'scribble')
            <li class="active">
            @else
            <li>
            @endif
                <a href="/scribble"><i class="fa fa-pencil"></i> <span class="nav-label">Scribble</span></a>
            </li> --}}
            @endif
            <!-- User role 2 start -->

            <!-- User role 3 start -->
            @if($userRole->broker)
            @if($_currentUrl === 'report')
            <li class="active">
            @else
            <li>
            @endif
                <a href="/report"><i class="fa fa-file-text-o"></i> <span class="nav-label">Reports</span></a>
            </li>
            @endif
            <!-- User role 3 start -->

            @if($_currentUrl === 'configuration')
            <li class="active">
            @else
            <li>
            @endif
                <a href="/configuration"><i class="fa fa-gear"></i> <span class="nav-label">Configuration</span></a>
            </li>

        </ul>

    </div>
</nav>
