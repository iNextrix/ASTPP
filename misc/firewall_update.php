<?php
// Copy file astpp_firewall_update to /etc/cron.d

    $fw_reload_cmd  = '/etc/rc.voipeers';
    $fw_config_file = '/etc/rc.voipeers.lst';
    $astpp_config   = parse_ini_file ('/var/lib/astpp/astpp-config.conf');

    $dbh = @new mysqli(
        $astpp_config['dbhost'],
        $astpp_config['dbuser'],
        $astpp_config['dbpass'],
        $astpp_config['dbname']
    );

    $fw_content = "# This file created automatically.\n# Don't make custom changes.\n# After restart script your changes will be lost.\n# File created:".date("Y.m.d H:i:s")."\n";

    if ( ! $dbh->connect_errno ) {
        $dbh->set_charset('utf8');

        if ($q = $dbh->query('SELECT UNIX_TIMESTAMP(STR_TO_DATE(MAX(last_modified_date), "%Y-%m-%d %H:%i:%s")) AS last_update FROM ip_map where fw_rule=0')) {
            $o = $q->fetch_object();
            $db_last_update = intval( $o->last_update );
            $fl_last_update = 0;

            if ( $db_last_update > 0 ) {
                if ( file_exists($fw_config_file) && filesize($fw_config_file) > 0 ) {

                    $current_file_content = file_get_contents($fw_config_file);
                    foreach( array($current_file_content) as $line ) {
                        if ( preg_match("/# Timestamp:(.+)/", $line, $match) ){
                            $fl_last_update = intval($match[1]);
                            break;
                        }
                    }
                }

                if ($fl_last_update == 0 || $fl_last_update != $db_last_update) {
                    $fw_content = $fw_content."# Timestamp:$db_last_update\n\n";

                    if ($q = $dbh->query('SELECT name, ip FROM ip_map WHERE STATUS=0 and fw_rule=0')) {
                        while ($o = $q->fetch_object()){
                            $fw_content = $fw_content."# ".$o->name."\n".$o->ip."\n\n";
                        }
                    }

                    if ( $fl_last_update > 0 ) {
                        copy($fw_config_file, $fw_config_file.'.bak');
                    }

                    file_put_contents($fw_config_file, $fw_content);
                    exec($fw_reload_cmd);
                }
            }
        }
    }
?>