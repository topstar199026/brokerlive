@foreach ($splits as $key => $split)
<div class="loan-split">
    <a href="/loansplit/edit/{{$split->id}}" class="link-edit">
        <span class="title">
            Loan {{!empty($split->loan_number) ? (int) $split->loan_number : ''}},
            Split {{!empty($split->split_number) ? (int) $split->split_number : ''}} - {{$split->documentstatus->name ?? ''}}
        </span>
        <div class="row">
            <div class="col-md-6">
                <label>Bank:</label> {{$split->lender}}
            </div>
            <div class="col-md-6">
                <label>File #:</label> {{$split->filenumber}}
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <label>Amount:</label> ${{$split->_subloan()}}
            </div>
            <div class="col-md-3">
                <label>LVR:</label> {{$split->lvr}}%
            </div>
            <div class="col-md-3">
                @if($split->lmi == 1)
                {{ 'inc. LMI' }}
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <label>Finance:</label> {{$split->_financeduedate()}}
            </div>
            <div class="col-md-6">
                <label>Settlement:</label> {{$split->_settlementdate()}}
            </div>
        </div>
    </a>
    <div class="form-edit" style="display:none;">
        @include('pages.deal.loansplit.form')
    </div>
</div>
@endforeach
