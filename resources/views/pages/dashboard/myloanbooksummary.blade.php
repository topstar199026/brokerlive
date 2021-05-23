@inject('DashboardService', 'App\Services\DashboardService')
<table class="table table-condensed">
    <thead>
        <tr>
            <th></th>
            <th>Loans</th>
            <th>Value</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Total settled loans</td>
            <td>{{$summaryLoans["settled"]["count"]}}</td>
            <td>${{$DashboardService->formatNumber($summaryLoans["settled"]["value"])}}</td>
        </tr>
        <tr>
            <td>Total discharged loans</td>
            <td>{{$summaryLoans["discharged"]["count"]}}</td>
            <td>${{$DashboardService->formatNumber($summaryLoans["discharged"]["value"])}}</td>
        </tr>
        <tr>
            <td></td>
            <td>
                @if($summaryLoans["settled"]["count"] > $summaryLoans["discharged"]["count"])
                    @php
                    $num = $summaryLoans["settled"]["count"] - $summaryLoans["discharged"]["count"];
                    @endphp
                    Your loan book has grown by {{$num}} loans over the last 12 months
                @else
                    @php
                    $num = $summaryLoans["discharged"]["count"] - $summaryLoans["settled"]["count"];
                    @endphp
                    Your loan book has decreased by {{$num}} loans over the last 12 months
                @endif
            </td>
            <td>
                @if($summaryLoans["settled"]["value"] > $summaryLoans["discharged"]["value"])
                    @php
                    $num = $summaryLoans["settled"]["value"] - $summaryLoans["discharged"]["value"];
                    @endphp
                    Your loan book has grown by {{$DashboardService->formatNumber($num)}} loans over the last 12 months
                @else
                    @php
                    $num = $summaryLoans["discharged"]["value"] - $summaryLoans["settled"]["value"];
                    @endphp
                    Your loan book has decreased by {{$DashboardService->formatNumber($num)}} loans over the last 12 months
                @endif
            </td>
        </tr>
    </tbody>
</table>
