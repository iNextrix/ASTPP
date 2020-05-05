<?php
    $file = (glob($config['fs_rec_dir'].'/'.$_REQUEST['uid'].'.*'))[0];

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
?>