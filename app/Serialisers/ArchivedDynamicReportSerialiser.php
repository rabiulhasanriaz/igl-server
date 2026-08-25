<?php
namespace App\Serialisers;

use Illuminate\Database\Eloquent\Model;
use Cyberduck\LaravelExcel\Contract\SerialiserInterface;

class ArchivedDynamicReportSerialiser implements SerialiserInterface
{
    public function getData($data)
    {
        $row = [];

        $row[] = $data->sd_cell_no;
        $row[] = $data->sd_message;
        $row[] = $data->sd_sms_cost;
        $row[] = $data->created_at->format('Y-m-d h:sa');
        $row[] = $data->sd_delivery_report;

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