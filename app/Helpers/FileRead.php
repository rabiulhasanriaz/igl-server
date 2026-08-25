<?php

class FileRead
{

    public static function getFileType($file_name)
    {
        $extensionsExcel = array("xls","xlsx","xlm","xla","xlc","xlt","xlw");
        $extensionsCsv = array("csv");
        $extensionsText = array("txt");

        $ext = pathinfo($file_name, PATHINFO_EXTENSION);


        if(in_array($ext,$extensionsExcel)){
            return "Excel";
        }elseif (in_array($ext,$extensionsCsv)){
            return "Csv";
        }elseif (in_array($ext, $extensionsText)){
            return "Text";
        }else{
            return false;
        }
    }

}
