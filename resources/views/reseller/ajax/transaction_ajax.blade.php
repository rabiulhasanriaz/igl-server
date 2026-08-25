@if(isset($transactionHistory) && count($transactionHistory) > 0)
    <h4 style="font-weight: bold;">LAST 5 TRANSACTION:</h4>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ccc;
        }

        th {
            background-color: #333;
            color: white;
        }

        .debit {
            color: green;
        }

        .credit {
            color: red;
        }
    </style>
    <table>
        <thead>
            <tr>
                <th>SL</th>
                <th>Transaction Date</th>
                <th>Debit</th>
                <th>Credit</th>
                <th>Reference</th>
            </tr>
        </thead>
        <tbody>
            @php
                $sl = 1;
            @endphp

            @foreach($transactionHistory as $transaction)
                <tr>
                    <td>{{ $sl }}</td>
                    <td>{{ $transaction->asb_submit_time }}</td>
                    <td class="debit">{{ number_format($transaction->asb_debit) }}</td>
                    <td class="credit">{{ number_format($transaction->asb_credit) }}</td>
                    <td>{{ $transaction->asb_pay_ref }}</td>
                </tr>
                @php
                    $sl++;
                @endphp
            @endforeach
        </tbody>
    </table>
@endif
