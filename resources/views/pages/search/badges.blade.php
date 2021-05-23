
@foreach($tags as $tag)
    @if(strpos($tag, 'Urgent') > -1)
        <span class="label label-danger">Urgent</span>
    @endif
    @if(strpos($tag, 'File Work') > -1)
        <span class="label label-info"><i class="fa fa-folder"></i> File follow up</span>
    @endif
    @if(strpos($tag, 'Email') > -1)
        <span class="label label-info"><i class="fa fa-envelope"></i> Email</span>
    @endif
    @if(strpos($tag, 'Lodge') > -1)
        <span class="label label-info"><i class="fa fa-external-link-square"></i> Submit</span>
    @endif
    @if(strpos($tag, 'Submit') > -1)
        <span class="label label-info"><i class="fa fa-external-link-square"></i> Submit</span>
    @endif
    @if(strpos($tag, 'Research') > -1)
        <span class="label label-info"><i class="fa fa-search"></i> Research</span>
    @endif
    @if(strpos($tag, 'Note') > -1)
        <span class="label label-info"><i class="fa fa-pencil-square-o"></i> Note</span>
    @endif
    @if(strpos($tag, 'Sales') > -1)
        <span class="label label-info"><i class="fa fa-bar-chart-o"></i> Sales Meeting</span>
    @endif
    @if(strpos($tag, 'Client') > -1)
        <span class="label label-info"><i class="fa fa-users"></i> Client Meeting</span>
    @endif
    @if(strpos($tag, 'Prospecting') > -1)
        <span class="label label-info"><i class="fa fa-eye"></i> Prospecting Call</span>
    @elseif(strpos($tag, 'Database') > -1)
        <span class="label label-info"><i class="fa fa-database"></i> Database Call</span>
    @elseif(strpos($tag, 'Call') > -1)
        <span class="label label-info"><i class="fa fa-phone"></i> Call</span>
    @endif
@endforeach
