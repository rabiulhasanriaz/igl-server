<html>
   <head>
        <title>Export Database || Bulk Sms</title>
        <meta http-equiv="refresh" content="1200">
    </head>
    <body>
        <?php
            if ($export_status == "no"){
                echo "Database exported allready for this hour";
            }else if($export_status == "yes"){
                echo "Database exporting";
                echo "<script>window.open('http://sms.iglweb.com/cron/abcdefujksdghhjsdhjkhgsdkj', '_blank'); </script>";
            }
        ?>
    </body>
</html>