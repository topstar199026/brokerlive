<div class="btn-group" role="group">
    <a class="btn {{$page_type == 'pipeline' ? 'btn-primary' : 'btn-default'}}" href="{{url('whiteboard')}}">Active Pipeline</a>
    <a class="btn {{$page_type == 'combined' ? 'btn-primary' : 'btn-default'}}" href="{{url('whiteboard/combined')}}">Detailed Monthly Figures</a>
    <a class="btn {{$page_type == 'basic' ? 'btn-primary' : 'btn-default'}}" href="{{url('whiteboard/basic')}}">Monthly Figures</a>
    <a class="btn {{$page_type == 'business' ? 'btn-primary' : 'btn-default'}}" href="{{url('whiteboard/business')}}">Business Metrics</a>
    <a class="btn {{$page_type == 'marketing' ? 'btn-primary' : 'btn-default'}}" href="{{url('whiteboard/marketing')}}">Marketing</a>
</div>