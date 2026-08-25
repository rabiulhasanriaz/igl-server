@if($topUsers->count() > 0)
<div class="table-responsive">
    <table class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th style="width:2%;">SL</th>
                <th style="width:15%;">User</th>
                <th style="width:8%;">Phone</th>
                <th style="width:5%;">Count</th>
                <th style="width:5%;">%</th>
                <th style="width:65%;">Ratios</th>
            </tr>
        </thead>

        <tbody>
            @foreach($topUsers as $index => $user)

            @php
                $percentage = $totalSms > 0
                    ? ($user->sms_count / $totalSms) * 100
                    : 0;

                // Progress bar color
                if ($percentage >= 70) {
                    $barColor = '#28a745';
                } elseif ($percentage >= 50) {
                    $barColor = '#ffc107';
                } elseif ($percentage >= 20) {
                    $barColor = '#fd7e14';
                } else {
                    $barColor = '#d14747';
                }

                // Badge color
                if ($user->sms_count >= 10000) {
                    $badgeColor = '#28a745';
                } elseif ($user->sms_count >= 5000) {
                    $badgeColor = '#ffc107';
                } else {
                    $badgeColor = '#d14747';
                }
            @endphp

            <tr>
                <td>{{ $index + 1 }}</td>

                <td>
                    {{ $user->user->company_name ?? 'Unknown User' }}
                </td>

                <td>
                    {{ $user->user->cellphone ?? '-' }}
                </td>

                <td>
                    <span class="badge"
                          style="background-color: {{ $badgeColor }}; color: #fff;">
                        {{ number_format($user->sms_count) }}
                    </span>
                </td>

                <td>
                    {{ number_format($percentage, 1) }}%
                </td>

                <td>
                    <div class="progress progress-mini"
                         style="height:19px; margin-bottom:0;">
                         
                        <div class="progress-bar"
                             style="
                                width: {{ $percentage }}%;
                                line-height: 19px;
                                background-color: {{ $barColor }};
                                color: #fff;
                                font-weight: bold;
                             ">
                             
                            {{ number_format($percentage, 1) }}%
                        </div>
                    </div>
                </td>
            </tr>

            @endforeach
        </tbody>
    </table>
</div>
@else

<div class="alert alert-info">
    <i class="ace-icon fa fa-info-circle"></i>
    No SMS activity found for this date.
</div>

@endif