<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover table-whiteboard table-team m-b-xs clearfix">
        <thead>
            <tr>
                <th>Bkr</th>
                <th>Borrower</th>
                <th>Settlement</th>
                <th>Finance Due</th>
                <th>Referrer</th>
                <th>Lender</th>
                <th class="total-loan" data-total="${{number_format($section->total_loan)}}">Loan Amount</th>
                <th class="total-actual" data-total="${{number_format($section->total_actual)}}">Actual</th>
                <th>Submitted</th>
                <th>AIP</th>
                <th>Pending</th>
                <th>Full App</th>
                <th>Month</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th>Bkr</th>
                <th>Borrower</th>
                <th>Settlement</th>
                <th>Finance Due</th>
                <th>Referrer</th>
                <th>Lender</th>
                <th>Loan Amount</th>
                <th>Actual</th>
                <th>Submitted</th>
                <th>AIP</th>
                <th>Pending</th>
                <th>Full App</th>
                <th>Month</th>
            </tr>
        </tfoot>
        <tbody>
            @foreach($section->rows as $row)
            <tr>
                <td>{{$row->broker}}</td>                
                @if($row->borrower == 'Confidential') 
                    <td class="borrower">{{$row->borrower}}</td>
                @else 
                    <td class="borrower"><a href="'.URL::site('deal/edit/'.$row->deal_id).'">{{$row->borrower}}</a></td>
                @endif
                
                <td>{{$row->settlement_date}}</td>
                <td>{{$row->finance_due}}</td>
                <td>{{$row->referrer}}</td>
                <td>{{$row->lender}}</td>
                <td>{{$row->loan_amount != '' ? '$'.number_format($row->loan_amount) : ''}}</td>
                <td>{{$row->actual != '' ? '$'.number_format($row->actual) : ''}}</td>
                {{-- <td>row->doc_status</td> --}}
                <td>{{$row->submitted_date}}</td>
                <td>{{$row->aip}}</td>
                <td>{{$row->conditional}}</td>
                <td>{{$row->full_approval}}</td>
                <td>{{$row->month}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>