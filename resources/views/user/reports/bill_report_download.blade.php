<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SMS Bill Report</title>



    <style type="text/css">
        * {
            font-size: 12px;
        }
        #invoice{
            /*padding: 15px;*/
            padding-top: 0px;
            padding-bottom: 0px;
        }

        .invoice {
            position: relative;
            background-color: #FFF;
            min-height: 680px;
            /*padding: 15px*/
        }

        .invoice header {
            padding: 10px 0;
            margin-bottom: 20px;
            border-bottom: 1px solid #3989c6
        }

        .invoice .company-details {
            text-align: right
        }

        .invoice .company-details .name {
            margin-top: 0;
            margin-bottom: 0
        }

        .invoice .contacts {
            margin-bottom: 20px
        }

        .invoice .invoice-to {
            text-align: left
        }

        .invoice .invoice-to .to {
            margin-top: 0;
            margin-bottom: 0
        }

        .invoice .invoice-details {
            text-align: center
        }

        .invoice .invoice-details .invoice-id {
            margin-top: 0;
            color: white;
        }

        .invoice main {
            padding-bottom: 50px
        }

        .invoice main .thanks {
            margin-top: -100px;
            /*font-size: 2em;*/
            margin-bottom: 50px
        }

        .invoice main .notices {
            padding-left: 6px;
            border-left: 6px solid #3989c6
        }

        .invoice main .notices .notice {
            /*font-size: 1.2em*/
        }

        .invoice table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
            margin-bottom: 20px
        }

        .invoice table td {

            background: #eee;
            /* border-bottom: 1px solid #fff */
        }

        .invoice table th {
            white-space: nowrap;
            font-weight: 400;
            /*font-size: 20px*/
        }

        .invoice table td h3 {
            margin: 0;
            font-weight: 400;
            color: #3989c6;
            /*font-size: 1.2em*/
        }

        .invoice table .qty,.invoice table .total,.invoice table .unit {
            text-align: right;
            font-size: 1.2em
        }

        .invoice table .no {
            color: #fff;
            /*font-size: 1.6em;*/
            background: #3989c6
        }

        .invoice table .unit {
            background: #ddd
        }

        .invoice table .total {
            background: #3989c6;
            color: #fff
        }

        /* .invoice table tbody tr:last-child td {
            border: none
        } */

        .invoice table tfoot td {
            background: 0 0;
            border-bottom: none;
            white-space: nowrap;
            /* text-align: right; */
            padding: 10px 20px;
            /*font-size: 1.2em;*/
            border-top: 1px solid #aaa
        }

        .invoice table tfoot tr:first-child td {
            border-top: none
        }

        .invoice table tfoot tr:last-child td {
            color: #3989c6;
            /*font-size: 1.4em;*/
            border-top: 1px solid #3989c6
        }

        .invoice table tfoot tr td:first-child {
            border: none
        }

        .invoice footer {
            width: 100%;
            text-align: center;
            color: #777;
            border-top: 1px solid #aaa;
            padding: 8px 0
        }
        .text-center {
            text-align: center;
        }
        .text-left {
            text-align: left;
        }
        .text-right {
            text-align: right;
        }

        @media print {
            .invoice {
                font-size: 11px!important;
                overflow: hidden!important
            }
            .invoice header {
                position: absolute;
                bottom: 10px;
                page-break-after: always
            }

            .invoice footer {
                position: absolute;
                bottom: 10px;
                page-break-after: always
            }

            .invoice>div:last-child {
                page-break-before: always
            }
        }


    </style>
</head>
<body>
<div id="invoice">

    <hr>
    <div class="invoice overflow-auto">
        <div style="">
            <header>
                <div class="row">
                    <div class="col" style="text-align: center;">
                        <a target="_blank" href="sms.iglweb.com">
                            <img src="{{ OtherHelpers::website_logo() }}" data-holder-rendered="true" />
                        </a>
                    </div>
                    <!-- <div class="col company-details">
                        <h2 class="name">
                            <a target="_blank" href="https://lobianijs.com">
                            Arboshiki
                            </a>
                        </h2>
                        <div>455 Foggy Heights, AZ 85004, US</div>
                        <div>(123) 456-789</div>
                        <div>company@example.com</div>
                    </div> -->
                </div>
            </header>
            <main>
                <div class="row contacts">
                    <!--  <div class="col invoice-to">
                         <div class="text-gray-light">INVOICE TO:</div>
                         <h2 class="to">John Doe</h2>
                         <div class="address">796 Silver Harbour, TX 79273, US</div>
                         <div class="email"><a href="mailto:john@example.com">john@example.com</a></div>
                     </div> -->
                    <div class="col invoice-details">
                        <h1 class="invoice-id"><b style="background-color: #3989c6; border-radius: 25px; padding: 10px;">SMS Report</b></h1>
                        
                    </div>
                </div>
                <table border="1" cellspacing="0" cellpadding="0">
                    <thead>
                    <tr>
                        <th class="text-center"><b>SL</b></th>
                        <th class="text-center"><b>Type</b></th>
                        <th class="text-center"><b>Campaign Title</b></th>
                        <th class="text-center"><b>Submit Time</b></th>
                        <th class="text-center"><b>SenderID</b></th>
                        <th class="text-center"><b title="Submitted sms/load">Submit</b></th>
                        <th class="text-center"><b title="Total sent sms/load">T. Sent</b></th>
                        <th class="text-center" style="width: 69px;"><b>Charge</b></th>
                    </tr>
                    </thead>
                    <tbody>
                    @php($serial=1)

                    @php($total=0)
                    @php($total_sms=0)
                    @php($total_load=0)
                    @php($total_sub=0)
                    @php($total_sub_sms=0)
                    @php($total_sub_load=0)
                    @php($debit1=0)
                    @php($debit2=0)
                    @php($credit=0)
                    @if(!empty($transactions))
                        @foreach($transactions as $transaction)
                            <tr>
                                @if($transaction->asb_pay_mode == 4)
                                    @php($smsCampaign = $transaction->smsCampaignId)
                                    {{--@php($smsCampaign = \App\Model\AccSmsBalance::getSmsCampaignIdDetails($transaction->asb_pay_ref))--}}
                                    <td>{{ $serial++ }}</td>
                                    <td>SMS Campaign</td>
                                    <td title="{{ @$smsCampaign->sci_campaign_id }}">
                                        {{ @$smsCampaign->sci_campaign_title ?? @$smsCampaign->sci_campaign_id }}
                                    </td>
                                    <td class="text-center">
                                        @if(!empty($transaction->asb_submit_time))
                                            {{ $transaction->asb_submit_time->format('H:i a, d-M-Y') }}
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        {{ @$smsCampaign->sender->sir_sender_id }}
                                    </td>
                                    <td class="text-center">
                                        {{ @$smsCampaign->sci_total_submitted }}
                                        @php($total_sub_sms = $total_sub_sms + @$smsCampaign->sci_total_submitted)
                                    </td>
                                    <td class="text-center">
                                        {{ @$smsCampaign->sci_total_submitted }}
                                        @php($total_sms = $total_sms + @$smsCampaign->sci_total_submitted)
                                    </td>
                                    <td class="text-right" style="padding-right: 5px; width: 69px;">
                                        -{{ number_format($transaction->asb_debit, 2) }}
                                        @php($debit1 = $debit1 + $transaction->asb_debit)
                                    </td>
                                @elseif($transaction->asb_pay_mode == 5)
                                    @php($loadCampaign = $transaction->loadCampaignId)
                                    {{--                                @php($loadCampaign = \App\Model\AccSmsBalance::getLoadCampaignIdDetails($transaction->asb_pay_ref))--}}
                                    <td>{{ $serial++ }}</td>
                                    <td>Load Campaign</td>
                                    <td title="{{ @$loadCampaign->campaign_id }}">
                                        {{ (@$loadCampaign->campaign_name != '')? @$loadCampaign->campaign_name:@$loadCampaign->campaign_id }}
                                    </td>
                                    <td class="text-center">
                                        @if(!empty($transaction->asb_submit_time))
                                            {{ $transaction->asb_submit_time->format('H:i a, d-M-Y') }}
                                        @endif
                                    </td>
                                    <td>N/A</td>
                                    <td class="text-center">
                                        {{ @$loadCampaign->total_number }}
                                        @php($total_sub_load = $total_sub_load + @$loadCampaign->total_number)
                                    </td>
                                    <td class="text-center">
                                        {{ @$loadCampaign->total_number }}
                                        @php($total_load = $total_load + @$loadCampaign->total_number)
                                    </td>
                                    <td class="text-right" style="padding-right: 5px; width: 69px;">
                                        -{{ number_format($transaction->asb_debit, 2) }}
                                        @php($debit2 = $debit2 + $transaction->asb_debit)
                                    </td>
                                @else
                                    <td>{{ $serial++ }}</td>
                                    <td>Deposit</td>
                                    <td>
                                        Deposit
                                    </td>
                                    <td class="text-center">
                                        @if(!empty($transaction->asb_submit_time))
                                            {{ $transaction->asb_submit_time->format('H:i a, d-M-Y') }}
                                        @endif
                                    </td>
                                    <td>N/A</td>
                                    <td>N/A</td>
                                    <td>N/A</td>
                                    <td class="text-right" style="padding-right: 5px; width: 69px;">
                                        {{ number_format($transaction->asb_credit, 2) }}
                                        @php($credit = $credit + $transaction->asb_credit)
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    @endif

                    @php($sub_total = 0)
                    @php($sub_total2 = 0)
                    @php($amount = 0)
                    @php($sub_total = $total_sms + $total_load)
                    @php($sub_total2 = $total_sub_sms + $total_sub_load)
                    @php($amount = ($credit + $balancebd) - ($debit1 + $debit2))
                    @php($total_charge = $debit1 + $debit2)

                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="5" class="text-right"><b>Total:</b></td>
                        <td class="text-center">
                            <b>{{ $sub_total2 }}</b>
                        </td>
                        <td class="text-center">
                            <b>
                                {{ $sub_total }}
                            </b>
                        </td>
                        <td class="text-right" style="padding-right: 5px; width: 69px;"><b>{{ number_format($amount,2) }}</b></td>
                    </tr>
                    </tfoot>

                </table>
                {{-- <div class="thanks">sdfdfgd</div> --}}
                <div class="notices">
                    {{-- <div>NOTICE:</div> --}}
                    <div class="notice">
                        <div class="card" style="width: 18rem;">
                            <ul class="list-group list-group-flush">
                              <li class="list-group-item"><b>Balance BD: {{ number_format($balancebd,2) }}</b></li>
                              <li class="list-group-item"><b>Total Deposit: {{ number_format($credit,2) }}</b></li>
                              <li class="list-group-item"><b>Total Charge: {{ number_format($total_charge,2) }}</b></li>
                              <li class="list-group-item"><b>Available Balance: {{ number_format($amount,2) }}</b></li>
                            </ul>
                          </div>
                    </div>
                </div>
            </main>
            <footer>
                Powered By {{ OtherHelpers::user_creator_info('company_name') }}
                <br>
                report downloaded on {!! Carbon\Carbon::now() !!}
            </footer>
        </div>
        <!--DO NOT DELETE THIS div. IT is responsible for showing footer always at the bottom-->
        <div></div>
    </div>
</div>
</body>
</html>