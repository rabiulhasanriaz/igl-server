<div class="table-responsive">
    <table class="table table-bordered table-striped" id="dataTable">
        <thead>
            <tr>
                <th width="5%">SL</th>
                <th width="15%">Date & Time</th>
                <th width="15%">Reseller</th>
                <th width="15%">Paid To</th>
                <th width="15%">Reference</th>
                <th width="10%">Credit</th>
                <th width="10%">Debit</th>
                <th width="10%">Payment Mode</th>
                <th width="10%">Deal Type</th>
                <th width="10%">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $sl = ($balance_transactions->currentPage() - 1) * $balance_transactions->perPage(); ?>
            @forelse($balance_transactions as $transaction)
            <?php $sl++; ?>
            <tr>
                <td>{{ $sl }}</td>
                <td>{{ $transaction->asb_submit_time ? $transaction->asb_submit_time->format('Y-m-d H:i:s') : 'N/A' }}</td>
                <td>
                    <strong>{{ $transaction->paidByUser->company_name ?? $transaction->asb_paid_by ?? 'N/A' }}</strong><br>
                    <small class="text-muted">{{ $transaction->paidByUser->cellphone ?? '' }}</small>
                </td>
                <td>{{ $transaction->payToUser->company_name ?? $transaction->asb_pay_to ?? 'N/A' }}</td>
                <td>
                    <small>{{ $transaction->asb_pay_ref ?? 'N/A' }}</small>
                    @if($transaction->smsCampaignId)
                        <br><span class="label label-info">SMS</span>
                    @elseif($transaction->loadCampaignId)
                        <br><span class="label label-info">Flexi</span>
                    @endif
                </td>
                <td class="text-right text-success">
                    @if($transaction->asb_credit > 0)
                        <strong>{{ number_format($transaction->asb_credit, 2) }}</strong>
                    @else
                        -
                    @endif
                </td>
                <td class="text-right text-danger">
                    @if($transaction->asb_debit > 0)
                        <strong>{{ number_format($transaction->asb_debit, 2) }}</strong>
                    @else
                        -
                    @endif
                </td>
                <td>
                    @php
                        $paymentMode = '';
                        switch($transaction->asb_pay_mode) {
                            case '1': $paymentMode = 'Cash'; break;
                            case '2': $paymentMode = 'Bank Deposit'; break;
                            case '3': $paymentMode = 'Check'; break;
                            case '4': $paymentMode = 'Send SMS'; break;
                            default: $paymentMode = ucfirst($transaction->asb_pay_mode ?? 'N/A');
                        }
                    @endphp
                    <span class="label label-default">{{ $paymentMode }}</span>
                </td>
                <td>
                    <span class="label label-primary">{{ ucfirst($transaction->asb_deal_type ?? 'N/A') }}</span>
                </td>
                <td>
                    @if($transaction->asb_payment_status == 1)
                        <span class="label label-success"><i class="fa fa-check"></i> Completed</span>
                    @else
                        <span class="label label-warning"><i class="fa fa-clock-o"></i> Pending</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center">
                    <div class="alert alert-info" style="margin: 20px;">
                        <i class="fa fa-info-circle"></i> No transactions found for selected criteria
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-right">Total:</th>
                <th></th>
                <th class="text-right text-success">{{ number_format($totals->total_credit, 2) }}</th>
                <th class="text-right text-danger">{{ number_format($totals->total_debit, 2) }}</th>
                <th colspan="3"></th>
            </tr>
        </tfoot>
    </table>
    
    <!-- Custom Pagination for AJAX -->
    @if($balance_transactions->total() > 0)
        <div class="row">
            <div class="col-md-6">
                <small>Showing {{ $balance_transactions->firstItem() }} to {{ $balance_transactions->lastItem() }} of {{ $balance_transactions->total() }} entries</small>
            </div>
            <div class="col-md-6 text-right">
                <ul class="pagination" id="ajax-pagination">
                    @if($balance_transactions->onFirstPage())
                        <li class="disabled"><span>« Previous</span></li>
                    @else
                        <li><a href="#" data-page="{{ $balance_transactions->currentPage() - 1 }}">« Previous</a></li>
                    @endif
                    
                    @for($i = 1; $i <= $balance_transactions->lastPage(); $i++)
                        @if($i == $balance_transactions->currentPage())
                            <li class="active"><span>{{ $i }}</span></li>
                        @else
                            <li><a href="#" data-page="{{ $i }}">{{ $i }}</a></li>
                        @endif
                    @endfor
                    
                    @if($balance_transactions->hasMorePages())
                        <li><a href="#" data-page="{{ $balance_transactions->currentPage() + 1 }}">Next »</a></li>
                    @else
                        <li class="disabled"><span>Next »</span></li>
                    @endif
                </ul>
            </div>
        </div>
    @endif
</div>

<script>
$(document).ready(function() {
    // Handle AJAX pagination clicks
    $(document).on('click', '#ajax-pagination a', function(e) {
        e.preventDefault();
        var page = $(this).data('page');
        if (page) {
            loadTransactions(page);
        }
    });
    
    // Handle per page change
    $('#per_page').change(function() {
        loadTransactions(1);
    });
});
</script>
