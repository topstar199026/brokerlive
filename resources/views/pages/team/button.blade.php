<div class="btn-group" role="group">
    <a class="btn {{$page_type == 'team' ? 'btn-primary' : 'btn-default'}}" href="{{url('team')}}">Team</a>
    <a class="btn {{$page_type == 'broker' ? 'btn-primary' : 'btn-default'}}" href="{{url('team/brokers')}}">Brokers</a>
    <a class="btn {{$page_type == 'pipeline' ? 'btn-primary' : 'btn-default'}}" href="{{url('team/pipeline')}}">Pipeline</a>
    <a class="btn {{$page_type == 'combined' ? 'btn-primary' : 'btn-default'}}" href="{{url('team/combined')}}">Stats</a>
    <a class="btn {{$page_type == 'basic' ? 'btn-primary' : 'btn-default'}}" href="{{url('team/basic')}}">Basic</a>
</div>