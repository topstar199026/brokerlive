<link href="/css/bootstrap.min.css" rel="stylesheet">
<link href="/font-awesome/css/font-awesome.css" rel="stylesheet">

<link href="/css/plugins/datapicker/datepicker3.css" rel="stylesheet">
<link href="/css/plugins/daterangepicker/daterangepicker-bs3.css" rel="stylesheet">

<link href="/css/plugins/jQueryUI/jquery-ui.css" rel="stylesheet">
<!-- Toastr style -->
<link href="/css/plugins/toastr/toastr.min.css" rel="stylesheet">

<!-- Gritter -->
<link href="/js/plugins/gritter/jquery.gritter.css" rel="stylesheet">

<!-- Timer Picker -->
<link href="/js/new_plugins/timerpicker/jquery.timepicker.css" rel="stylesheet">

<link href="/css/plugins/datatables/datatables.min.css" rel="stylesheet">
<link href="/css/animate.css" rel="stylesheet">
<link href="/css/style.css" rel="stylesheet">

<style>
    .dropdown.dropdown-lg .dropdown-menu {
        padding: 6px 20px;
    }
    .input-group-btn .btn-group {
        display: flex !important;
    }
    .container-search .btn-group .btn {
        border-radius: 0;
        margin-left: -1px;
    }
    .container-search .btn-group .btn:last-child {
        border-top-right-radius: 4px;
        border-bottom-right-radius: 4px;
    }
    .container-search .btn-group .form-horizontal .btn[type="submit"] {
        border-top-left-radius: 4px;
        border-bottom-left-radius: 4px;
    }
    .form-horizontal .form-group {
        margin-left: 0;
        margin-right: 0;
    }
    .form-group .form-control:last-child {
        border-top-left-radius: 4px;
        border-bottom-left-radius: 4px;
    }

    @media screen and (min-width: 768px) {
        #adv-search {
            margin: 0 auto;
        }
        .dropdown.dropdown-lg {
            position: static !important;
        }
        .dropdown.dropdown-lg .dropdown-menu {
            min-width: 914px;
        }
    }

    .modal-header{
        display: inline-block;
    }

    .col-sm-12{ overflow: auto}
    .avatar-edit {
        display:none;
    }
    .profile-avatar img:hover + .avatar-edit{
        display: block;
    }


    .tox .tox-toolbar div:nth-child(3) button:nth-child(2) .tox-tbtn__select-label {
        width: 3em!important;
    }
    .tox .tox-toolbar div:nth-child(3) button:nth-child(1) .tox-tbtn__select-label {
        width: 6em!important;
    }
    .tox .tox-tbtn svg {
        fill:#888888!important;
    }

    @media (min-width: 768px){
        .navbar-static-side {
            position: fixed;
            height: 100%;
            overflow-y: auto;
        }

        #page-wrapper {
            margin: 0 0 0 220px;
        }
    }

    body.mini-navbar #page-wrapper {
        margin: 0 0 0 70px;
    }

    @media (max-width: 768px){
        #page-wrapper {
            margin: 0 0 0 0 !important;
        }
    }

    .plupload {
        display: none !important;
    }

    .ui-timepicker-wrapper {
        width: 158px !important;
    }
</style>
