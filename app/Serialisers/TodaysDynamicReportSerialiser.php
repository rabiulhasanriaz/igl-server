<?php
namespace App\Serialisers;

use Illuminate\Database\Eloquent\Model;
use Cyberduck\LaravelExcel\Contract\SerialiserInterface;

class TodaysDynamicReportSerialiser implements SerialiserInterface
{
    public function getData($data)
    {
        $row = [];

        $row[] = $data->sdt_cell_no;
        $row[] = $data->sdt_message;
        $row[] = $data->sdt_sms_cost;
        $row[] = $data->created_at->format('Y-m-d h:sa');
        $row[] = $data->sdt_delivery_report;

        return $row;
    }

    public function getHeaderRow()
    {
        return [
            'Mobile Number',
            'Message',
            'Sms Cost',
            'Submit Time',
            'Delivery Status'
        ];
    }
}
?>