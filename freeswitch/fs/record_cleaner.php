<?php
include("lib/astpp.db.php");
include("lib/astpp.lib.php");
include("lib/astpp.logger.php");

$db     = new db();
$lib    = new lib();
$config = $lib->get_configurations($db);
$logger = new logger($lib);

if (isset($config['fs_node']) && intval($config['fs_node'])>0 && isset($config['fs_rec_dir'])){
    $expired_record_files = $db->run('SELECT c.uniqueid FROM accounts a, cdrs c WHERE a.record_store_period IS NOT null AND a.id = c.accountid AND c.fs_node = '.intval($config['fs_node']).' AND c.is_recording = 0 AND c.callstart<=DATE_SUB(curdate(), INTERVAL a.record_store_period MONTH)');

    if (!empty($expired_record_files)) {
        $logger->log('-----------------Start inspect expired record files------------------');

        foreach($expired_record_files as $file){
            $files_in_dir = glob($config['fs_rec_dir'].'/'.$file['uniqueid'].'.*');
            foreach($files_in_dir as $store_file){
                unlink($store_file);
                $logger->log('Delete expired record:'.$store_file);
            }
            $db->run("update cdrs set is_recording=1 where uniqueid='".$file['uniqueid']."'");
        }

        $logger->log('-----------------Stop inspect expired record files-------------------');
    }
}
?>