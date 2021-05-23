<select id="{{$attributes->id}}" name="{{$attributes->name}}" class="{{$attributes->class}}" {{$multiple}}>
    @foreach ($values as $key => $value)
    <option value="{{$key}}" {{$deal && $deal->status == $key ? 'selected' : ''}}>{{$value}}</option>
    @endforeach
</select>