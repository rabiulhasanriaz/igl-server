<?php

namespace App\Http\Controllers\Cron;


use App\Model\ExportTime;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Log;

class ExportDatabaseController extends Controller
{
    public function exportDatabase()
    {
        
        /*previous-ip=43.250.83.138*/
        /*103.196.232.206*/
        // dd(request()->ip());
        if ( request()->ip() != "27.147.180.165" )
        {
            return "Dont try this site.";
        }
        // return view('cron.export-database');

        $h = date('H');
        $h = intval($h);
        

        $next_hour = intval(ExportTime::first()->value('export_hour'));
        LOG::info('h-'.$h);
        LOG::info('next-hour-'.$next_hour);
        if ( $h == $next_hour ){
            echo "here-1";
            //dump("equal");
            $update = ExportTime::first();
            if ( $h == 23 ){
                $update->export_hour = 0;
            }else{
                $update->export_hour = $h + 1;
            }
            $update->save();
            return view('cron.export-database-f', ["export_status" => 'yes']);
        }else{
            echo "here-2";
            //dump("not-equal");
            $update = ExportTime::first();
            if( $h == 23 ){
                $update->export_hour = 0;
            }else{
                $update->export_hour = $h + 1;
            }
            $update->save();
            return view('cron.export-database-f', ["export_status" => 'no']);
        }

    }
}
