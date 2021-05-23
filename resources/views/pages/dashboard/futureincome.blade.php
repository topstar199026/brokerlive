@inject('DashboardService', 'App\Services\DashboardService')
<div class="ibox float-e-margins ibox-dashboard">
    <div class="ibox-title">
        <span class="badge badge-info popup pull-right"
            data-content="Please note, incomes that are included in your future income totals are based on your loan split milestones.
                        Any loan split that has recorded a PENDING, UNCONDITIONAL (Approved), or SETTLED date will be included in the totals.
                        Your income for the next 3 calendar months is displayed and is based on your 'expected' settlement date PLUS your average time from
                        &quot;settlement till upfront comm paid&quot; which is automatically calculated. Any loans which do not have an
                        'expected' settlement date will contribute to the &quot;Unconfirmed&quot; total.
                        <br><br>Please remember to input your &quot;commission value&quot; in the configuration tab.
                        eg. If the bank pays 0.65% upfront commission, and your aggregator pays you 90% then your commission value is as follows: <b>0.65% X .9 = 0.585</b>"
                        rel="popover" data-html="true" data-placement="top" data-trigger="click" data-original-title="" title="" style="top: 14px;">
            <i class="fa fa-info"></i>
        </span>
        <h5>Future Income</h5>
    </div>
    <div class="ibox-content">
        <div class="row">
            @foreach ($results as $key => $value)
            <div class="col-md-3">
                <h1 class="no-margins" data-toggle="tooltip" title="${{$DashboardService->formatNumber($value)}}">${{$DashboardService->formatNumber($value)}}</h1>
                <div class="font-bold"><small style="font-size: 92%; font-weight: 800;">{{$key}}</small></div>
            </div>
            @endforeach
        </div>
    </div>
</div>

