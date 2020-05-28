# !!! NOTE !!! 
# For access to record dir, add web user to group freeswitch:
# #> usermod -G freeswitch www-data 
<?php
    if (isset($_REQUEST['uid'])){
        $call_params = ($db->run("SELECT accountid FROM cdrs WHERE fs_node = ".$config['fs_node']." AND is_recording = 0 AND uniqueid = '".$_REQUEST['uid']."'"))[0];
        if( isset($call_params['accountid']) && intval($call_params['accountid']) ){
            $file = (glob($config['fs_rec_dir'].'/'.$call_params['accountid'].'/'.$_REQUEST['uid'].'.*'))[0];

            if (file_exists($file)){
                $name = basename($file);

                if ($fh = fopen($file, 'rb')){
                    header ( 'Content-Type: application/octet-stream' );
                    header ( 'Content-Length: '. filesize($file) );
                    header ( 'Content-Disposition: attachment; filename="'.$name.'"' );

                    fpassthru($fh);
                    fclose($fh);
                }
            }
        };
    }

?>