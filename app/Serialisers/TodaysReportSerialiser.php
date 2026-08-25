<?php
namespace App\Serialisers;

use Illuminate\Database\Eloquent\Model;
use Cyberduck\LaravelExcel\Contract\SerialiserInterface;

class TodaysReportSerialiser implements SerialiserInterface
{
    public function getData($data)
    {
        $row = [];

        $row[] = $data->sender->sir_sender_id;
        $row[] = $data->sct_cell_no;
        $row[] = $data->sct_message;
        $row[] = $data->sct_sms_cost;
        $row[] = $data->created_at->format('Y-m-d h:sa');
        $row[] = $data->sct_delivery_report;

        return $row;
    }

    public function getHeaderRow()
    {
        return [
            'Sender Id',
            'Mobile Number',
            'Message',
            'Sms Cost',
            'Submit Time',
            'Delivery Status'
        ];
    }
}
?>