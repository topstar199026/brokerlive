<table class="table table-condensed">
    <thead>
        <tr>
            <th>Deal</th>
            <th>Due Date</th>
            <th>Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($splits as $split)
        <tr>
            <td><a href="/deal/edit/{{$split->deal->id}}">{{$split->deal->name}}</a></td>
            <td>{{$split->{$column_name}}</td>
            <td>${{$split->subloan}}</td>
        </tr>
        @endforeach
    </tbody>
</table>
