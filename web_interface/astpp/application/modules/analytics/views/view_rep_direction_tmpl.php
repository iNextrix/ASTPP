<table style="width: 100%; border:none">
    <tbody>
        <tr>
            <td style="width: 17.8249%;"><img src="upload/logo.png" style="width: 300px; display: block; vertical-align: top; margin: 5px auto; text-align: center;"></td>
            <td style="width: 82.0848%;"></td>
        </tr>
    </tbody>
</table>

<p><br></p>

<table style="width: 100%; border:none">
    <tbody>
        <tr>
            <td style="text-align: center;">
                <div style="text-align: center;"><strong><span style="font-size: 18px;">Отчет по направлениям за период</span><br></strong></div>
            </td>
        </tr>
    </tbody>
</table>

<p><br></p>

<table style="width: 100%; border:none;">
    <tbody>
        <tr>
            <td style="width: 50.0000%;">
                <br>
            </td>
            <td style="width: 50.0000%;">
                <div style="text-align: center;"><u>Дата создания : <?=$cdate?></u></div>
            </td>
        </tr>
        <tr>
            <td style="width: 50.0000%;">
                <br>
            </td>
            <td style="width: 50.0000%;">
                <div style="text-align: center;"><u>Период : <?=$bdate?> - <?=$edate?></u></div>
            </td>
        </tr>
    </tbody>
</table>

<p><br></p>

<table style="width: 100%;" border="1" cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <td style="width: 80%;text-align: center;">
                <div>Название направления</div>
            </td>
            <td style="width: 10%;text-align: center;">
                <div>Процент</div>
            </td>
            <td style="width: 10%;text-align: center;">
                <div>Количество</div>
            </td>
        </tr>
    </thead>
    <tbody>
<?php
    function getPercent($i, $t) {
        $p = 0;

        if( $t > 0 ) {
            $p = round(($i/$t)*100);
        }

        return $p>=1 ? $p : '< 1';
    }

    $total_cnt = 0;

    foreach($data as $item){
        $total_cnt += intval($item['cnt']);
    }

    foreach($data as $item){
        print("<tr><td style='width:70%;text-align: center;'>{$item['destination']}</td><td style='width:15%;text-align: center;'>".getPercent($item['cnt'], $total_cnt)."%</td><td style='width:15%;text-align: center;'>{$item['cnt']}</td></tr>");
    }
?>
    </tbody>
</table>
