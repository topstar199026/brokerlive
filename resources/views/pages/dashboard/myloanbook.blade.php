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
            <td>{{$loans["settled"]["count"]}}</td>
            <td>${{$DashboardService->formatNumber($loans["settled"]["value"])}}</td>
        </tr>
        <tr>
            <td>Total discharged loans</td>
            <td>{{$loans["discharged"]["count"]}}</td>
            <td>${{$DashboardService->formatNumber($loans["discharged"]["value"])}}</td>
        </tr>
        <tr>
            <td>Total active loans</td>
            <td>{{$loans["active"]["count"]}}</td>
            <td>${{$DashboardService->formatNumber($loans["active"]["value"])}}</td>
        </tr>
    </tbody>
</table>
