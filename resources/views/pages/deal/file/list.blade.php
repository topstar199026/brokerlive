<style>
    .brk {
        display: none;
    }
</style>
<table class="file-list table">
    <tbody>
        @if(isset($files))
        @foreach ($files as $keyFile => $file)
        <tr>
            <td>
                <i class="fa {{$file->file_icon}}"  style="font-size: 2em;float: left;"></i>
            </td>
            <td>
                <a title="{{ $file->file_name}}" href="/fileManagement/download/{{$file->id}}" class="link-download" style="float: left;padding-left: 20px;padding-top: 5px;">
                    <span class="filename">{{$file->file_name}}</span>
                </a>
            </td>
            <td>
                <span class="username" style="float: left;padding-left: 20px;padding-top: 5px;">attached by {{$file->user->fullName()}}</span>
            </td>
            <td>
                <span class="date" style="float: left;padding-left: 5px;padding-top: 5px;">{{date("jS M Y", strtotime($file->date))}}</span>
            </td>
            <td>
                <span class="file-delete" style="float: left;padding-left: 5px;padding-top: 5px;">
                    <a href="/fileManagement/delete/{{$file->id}}" class="link-delete" title="Delete">
                        <i class="fa fa-trash-o"></i>
                    </a>
                </span>
            </td>
        </tr>
        @endforeach
        @endif
        <tr style="display: none;" class="brk brk-template">
            <td>
                <i class="brk brk-icon fa " style="font-size: 2em;float: left"></i>
            </td>
            <td>
                <a href="javascript:void(0)" class="link-download" style="float: left;padding-left: 20px;padding-top: 5px;">
                    <span class="filename"></span>
                    <div class="upload_info" style="width: 200px;height: 5px;padding-top: 5px;">
                        <div class="progress" style="display: none;height: 5px;background-color: #777">
                            <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
                            </div>
                        </div>
                        <span class="error" style="display: none"></span>
                    </div>
                </a>
            </td>
            <td>
                <span class="brk brk-username username" style="float: left;padding-left: 20px;padding-top: 5px;"></span>
            </td>
            <td>
                <span class="brk brk-date date" style="float: left;padding-left: 5px;padding-top: 5px;"></span>
            </td>
            <td>
                <span class="brk brk-delete file-delete" style="float: left;padding-left: 5px;padding-top: 5px;">
                    <a href="javascript:void(0)" class="link-delete" title="Delete">
                        <i class="fa fa-trash-o"></i>
                    </a>
                </span>
            </td>
        </tr>
    </tbody>
</table>
