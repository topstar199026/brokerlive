<select id="{{$attributes->id}}" name="{{$attributes->name}}" class="{{$attributes->class}}">
    @foreach ($dealStatus as $key => $status)
    <option value="{{$status->id}}" {{$deal && $deal->status == $status->id ? 'selected' : ''}}>{{$status->description}}</option>
    @endforeach
</select>