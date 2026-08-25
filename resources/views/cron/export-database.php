<?php
    session_start();
    // session_destroy();
    // exit();
    date_default_timezone_set ('Asia/Dhaka');

    if( !isset($_SESSION['export_database'] ) ) {
        $_SESSION['export_database'] = 0;
    }

    echo "The Database will be exported at ".date('H : i')."<br>";
    echo $_SESSION['export_database'];

    $try_start_time = 1129; // This value always should be bellow 59 of second place
    $try_end_time = $try_start_time+2;

    if(intval(date('Hi'))>=$try_start_time && intval(date('Hi'))<=$try_end_time) {

        if($_SESSION['export_database'] == 0) {

            $_SESSION['export_database'] = 1;

            echo date('Y-m-d H:i:s');

            $year = date('Y'); $month = date('m'); $date = date('d');

            //ENTER THE RELEVANT INFO BELOW
            $mysqlUserName      = "root";
            $mysqlPassword      = "";
            $mysqlHostName      = "localhost";
            $DbName             = "igl_vas";
            $backup_name        = "";
            $tables             = "";

           //or add 5th parameter(array) of specific tables:    array("mytable1","mytable2","mytable3") for multiple tables

            Export_Database($mysqlHostName,$mysqlUserName,$mysqlPassword,$DbName,  $tables=false, $backup_name=false );

        }
    }
    if(intval(date('Hi')) > $try_end_time) {
        $_SESSION['export_database'] = 0;
    }


    function Export_Database($host,$user,$pass,$name,  $tables=false, $backup_name=false)
    {
        $mysqli = new mysqli($host,$user,$pass,$name);
        $mysqli->select_db($name);
        $mysqli->query("SET NAMES 'utf8'");

        $queryTables    = $mysqli->query('SHOW TABLES');
        while($row = $queryTables->fetch_row())
        {
            $target_tables[] = $row[0];
        }
        if($tables !== false)
        {
            $target_tables = array_intersect( $target_tables, $tables);
        }
        foreach($target_tables as $table)
        {
            $result         =   $mysqli->query('SELECT * FROM '.$table);
            $fields_amount  =   $result->field_count;
            $rows_num=$mysqli->affected_rows;
            $res            =   $mysqli->query('SHOW CREATE TABLE '.$table);
            $TableMLine     =   $res->fetch_row();
            $content        = (!isset($content) ?  '' : $content) . "\n\n".$TableMLine[1].";\n\n";

            for ($i = 0, $st_counter = 0; $i < $fields_amount;   $i++, $st_counter=0)
            {
                while($row = $result->fetch_row())
                { //when started (and every after 100 command cycle):
                    if ($st_counter%100 == 0 || $st_counter == 0 )
                    {
                            $content .= "\nINSERT INTO ".$table." VALUES";
                    }
                    $content .= "\n(";
                    for($j=0; $j<$fields_amount; $j++)
                    {
                        $row[$j] = str_replace("\n","\\n", addslashes($row[$j]) );
                        if (isset($row[$j]))
                        {
                            $content .= '"'.$row[$j].'"' ;
                        }
                        else
                        {
                            $content .= '""';
                        }
                        if ($j<($fields_amount-1))
                        {
                                $content.= ',';
                        }
                    }
                    $content .=")";
                    //every after 100 command cycle [or at last line] ....p.s. but should be inserted 1 cycle eariler
                    if ( (($st_counter+1)%100==0 && $st_counter!=0) || $st_counter+1==$rows_num)
                    {
                        $content .= ";";
                    }
                    else
                    {
                        $content .= ",";
                    }
                    $st_counter=$st_counter+1;
                }
            } $content .="\n\n\n";
        }
        //$backup_name = $backup_name ? $backup_name : $name."___(".date('H-i-s')."_".date('d-m-Y').")__rand".rand(1,11111111).".sql";
        $name = date('Y').date('m').date('d').date('H').date('i');
        $backup_name = $name.".sql";
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary");
        header("Content-disposition: attachment; filename=\"".$backup_name."\"");

        echo $content; exit;
    }
?>