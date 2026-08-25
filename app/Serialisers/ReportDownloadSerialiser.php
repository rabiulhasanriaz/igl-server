<?php
namespace App\Serialisers;

use Illuminate\Database\Eloquent\Model;
use Cyberduck\LaravelExcel\Contract\SerialiserInterface;

class ReportDownloadSerialiser implements SerialiserInterface
{
    public function getData($data)
    {
        $row = [];

        $row[] = $data->sender->sir_sender_id;
        $row[] = $data->sc_cell_no;
        $row[] = $data->sc_message;
        $row[] = $data->sc_sms_cost;
        $row[] = $data->created_at->format('Y-m-d h:sa');
        $row[] = $data->sc_delivery_report;

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