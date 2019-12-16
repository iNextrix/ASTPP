<?php
function num2str($num) {
    $nul='ноль';
    $ten=array(
	array('','один','два','три','четыре','пять','шесть','семь', 'восемь','девять'),
	array('','одна','две','три','четыре','пять','шесть','семь', 'восемь','девять'),
    );
    $a20=array('десять','одиннадцать','двенадцать','тринадцать','четырнадцать' ,'пятнадцать','шестнадцать','семнадцать','восемнадцать','девятнадцать');
    $tens=array(2=>'двадцать','тридцать','сорок','пятьдесят','шестьдесят','семьдесят' ,'восемьдесят','девяносто');
    $hundred=array('','сто','двести','триста','четыреста','пятьсот','шестьсот', 'семьсот','восемьсот','девятьсот');
    $unit=array( // Units
	array('копейка' ,'копейки' ,'копеек',	 1),
	array('рубль'   ,'рубля'   ,'рублей'    ,0),
	array('тысяча'  ,'тысячи'  ,'тысяч'     ,1),
	array('миллион' ,'миллиона','миллионов' ,0),
	array('миллиард','милиарда','миллиардов',0),
    );
    //
    list($rub,$kop) = explode('.',sprintf("%015.2f", floatval($num)));
    $out = array();
    if (intval($rub)>0) {
	foreach(str_split($rub,3) as $uk=>$v) { // by 3 symbols
	    if (!intval($v)) continue;
	    $uk = sizeof($unit)-$uk-1; // unit key
	    $gender = $unit[$uk][3];
	    list($i1,$i2,$i3) = array_map('intval',str_split($v,1));
	    // mega-logic
	    $out[] = $hundred[$i1]; # 1xx-9xx
	    if ($i2>1) $out[]= $tens[$i2].' '.$ten[$gender][$i3]; # 20-99
	    else $out[]= $i2>0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
	    // units without rub & kop
	    if ($uk>1) $out[]= morph($v,$unit[$uk][0],$unit[$uk][1],$unit[$uk][2]);
	} //foreach
    }
    else $out[] = $nul;
    $out[] = morph(intval($rub), $unit[1][0],$unit[1][1],$unit[1][2]); // rub
    $out[] = $kop.' '.morph($kop,$unit[0][0],$unit[0][1],$unit[0][2]); // kop
    return trim(preg_replace('/ {2,}/', ' ', join(' ',$out)));
}
/**
 * Склоняем словоформу
 * @ author runcore
 */
function morph($n, $f1, $f2, $f5) {
    $n = abs(intval($n)) % 100;
    if ($n>10 && $n<20) return $f5;
    $n = $n % 10;
    if ($n>1 && $n<5) return $f2;
    if ($n==1) return $f1;
    return $f5;
}
?>
<table style="width: 100%;">
    <tbody>
	<tr>
	    <td style="width: 35.0596%; vertical-align: middle;"><img src="upload/<?php print($logo); ?>" style="width: 145px; display: block; vertical-align: top; margin: 5px auto 5px 0px; text-align: left;"></td>
	    <td style="width: 64.8487%;"><strong>АКТ № <?php print($invoicenumber); ?> от <?php print($invoice_date); ?></strong></td>
	</tr>
    </tbody>
</table>
<hr style="border:1px solid grey;">
<table style="width: 100%;">
    <tbody>
	<tr>
	    <td style="width: 16.6516%; vertical-align: top;">Исполнитель:</td>
	    <td style="width: 83.2581%;">
<?php
    print ($cmp_name.', ');
    print ('ИНН '.$cmp_inn.', ');
    print ('КПП '.$cmp_bank_kpp.', ');
    print ($cmp_city_zipcode.', ');
    print ($cmp_address.', ');
    print ($cmp_telephone.'.');
?>
	</td>
	</tr>
	<tr>
	    <td style="width: 16.6516%; vertical-align: top;">Заказчик:</td>
	    <td style="width: 83.2581%;">
<?php
    print ($fullname.', ');
    print ('ИНН '.$inn.', ');
    print ('КПП '.$bank_kpp.', ');
    print ($city_postalcode.', ');
    print ($address_1.', ');
    print ($telephone_1.'.');
?>
</td>
	</tr>
    </tbody>
</table>
<p><br></p>
<table style="width: 100%" border="1" cellpadding="0" cellspacing="0">
	<tr>
	    <td style="width: 3.1622%;  text-align: center;"><strong>№</strong></td>
	    <td style="width: 54.8122%; text-align: center;"><strong>Наименование работ, услуг</strong></td>
	    <td style="width: 9.6257%;  text-align: center;"><strong>Кол-во</strong></td>
	    <td style="width: 10.1527%; text-align: center;"><strong>Ед.</strong></td>
	    <td style="width: 10.6513%; text-align: center;"><strong>Цена</strong></td>
	    <td style="width: 11.5072%; text-align: center;"><strong>Сумма</strong></td>
	</tr>
<?php
    $total_amt = 0;
    $idx = 1;
    foreach ($invoice_details_data as $invdatakey => $inv_data_value) {
	$sub_summ = 0;

	if ($inv_data_value['debit'] > 0) {
	    $sub_summ = $inv_data_value['debit'];
	}

	if ($inv_data_value['credit'] > 0) {
	    $sub_summ = $inv_data_value['credit'];
	}
	$sub_summ = floor($sub_summ*100)/100;
	$price   = floor(100*$sub_summ/$inv_data_value['quantity'])/100;
?>
	<tr>
	    <td style="width: 3.1622%;text-align: center;"><?=$idx?></td>
	    <td style="width: 54.8122%;text-align: center;"><?=$inv_data_value['description']?></td>
            <td style="width: 9.6257%;text-align: center;"><?=$inv_data_value['quantity']?></td>
	    <td style="width: 10.1527%;text-align: center;">услуга</td>
	    <td style="width: 10.6513%;text-align: center;"><?=$price?></td>
	    <td style="width: 11.5072%;text-align: center;"><?=$sub_summ?></td>
	</tr>
<?php
	$idx++;
	$total_amt=+$sub_summ;
    }
?>
</table>
<p><br></p>
<table style="width: 100%; margin-left: calc(0%);">
    <tbody>
	<tr>
	    <td style="width: 57.9743%;"></td>
	    <td style="width: 27.5894%;text-align: left;"><strong>Итого к оплате:</strong></td>
	    <td style="width: 14.3446%;text-align: right;"><?=$total_amt?></td>
	</tr>
	<tr>
	    <td style="width: 57.9743%;"></td>
	    <td style="width: 27.5894%;text-align: left;">В том числе НДС:</td>
	    <td style="width: 14.3446%;text-align: right;">Без НДС</td>
	</tr>
    </tbody>
</table>
<p><br></p>
<p>Общая стоимость выполненных работ, оказанных услуг: <?php print(ucfirst(num2str($total_amt)).'.'); ?></p>
<p>Вышеперечисленные услуги выполнены полностью. Заказчик претензий по объёму, качеству и срокам оказания услуг не имеет.</p>
<p><br></p>
<table style="width: 100%;">
    <tbody>
	<tr>
	    <td style="width: 11.3199%;">Исполнитель:</td>
	    <td style="width: 38.6343%;"><strong><?=$cmp_name?></strong></td>
	    <td style="width: 11.5154%;">Заказчик:</td>
	    <td style="width: 38.451%;"><strong><?=$fullname?></strong></td>
	</tr>
	<tr>
	    <td style="width: 11.3199%;">ИНН</td>
	    <td style="width: 38.6343%;"><?=$cmp_inn?></td>
	    <td style="width: 11.5154%;">ИНН</td>
	    <td style="width: 38.451%;"><?=$inn?></td>
	</tr>
	<tr>
	    <td style="width: 11.3199%;">КПП</td>
	    <td style="width: 38.6343%;"><?=$cmp_kpp?></td>
	    <td style="width: 11.5154%;">КПП</td>
	    <td style="width: 38.451%;"><?=$kpp?></td>
	</tr>
	<tr>
	    <td style="width: 11.3199%;">Адрес</td>
	    <td style="width: 38.6343%;"><?=$cmp_address?></td>
	    <td style="width: 11.5154%;">Адрес</td>
	    <td style="width: 38.451%;"><?=$address_1?></td>
	</tr>
	<tr>
	    <td style="width: 11.3199%;">Р/с</td>
	    <td style="width: 38.6343%;"><?=$cmp_bank_rs?></td>
	    <td style="width: 11.5154%;">Р/с</td>
	    <td style="width: 38.451%;"><?=$bank_rs?></td>
	</tr>
	<tr>
	    <td style="width: 11.3199%;">К/с</td>
	    <td style="width: 38.6343%;"><?=$cmp_bank_ks?></td>
	    <td style="width: 11.5154%;">К/с</td>
	    <td style="width: 38.451%;"><?=$bank_ks?></td>
	</tr>
	<tr>
	    <td style="width: 11.3199%;">Банк</td>
	    <td style="width: 38.6343%;"><?=$cmp_bank_name?></td>
	    <td style="width: 11.5154%;">Банк</td>
	    <td style="width: 38.451%;"><?=$bank_name?></td>
	</tr>
	<tr>
	    <td style="width: 11.3199%;">БИК</td>
	    <td style="width: 38.6343%;"><?=$cmp_bank_bik?></td>
	    <td style="width: 11.5154%;">БИК</td>
	    <td style="width: 38.451%;"><?=$bank_bik?></td>
	</tr>
	<tr>
	    <td style="width: 11.3199%;">Телефон</td>
	    <td style="width: 38.6343%;"><?=$cmp_telephone?></td>
	    <td style="width: 11.5154%;">Телефон</td>
	    <td style="width: 38.451%;"><?=$telephone_1?></td>
	</tr>
    </tbody>
</table>
<p><br></p>
<table style="width: 100%;">
    <tbody>
	<tr>
	    <td style="width: 25.0000%; text-align: center;">____________</td>
	    <td style="width: 25%; text-align: center;font-size: 12px;">/<u><?=$cmp_company_manager?></u></td>
	    <td style="width: 25.0000%;text-align: center;">____________</td>
	    <td style="width: 25%; text-align: center;">/_________________</td>
	</tr>
	<tr>
	    <td style="width: 25.0000%;text-align: center;"><span style="font-size: 12px;">М.П.</span></td>
	    <td style="width: 25%; text-align: center;"><span style="font-size: 12px;">расшифровка подписи</span></td>
	    <td style="width: 25%; text-align: center;"><span style="font-size: 12px;">М.П.</span></td>
	    <td style="width: 25%; text-align: center;"><span style="font-size: 12px;">расшифровка подписи</span></td>
	</tr>
    </tbody>
</table>

<pagebreak>

<table style="width: 100%;">
    <tbody>
	<tr>
	    <td style="width: 35.0596%; vertical-align: middle;"><img src="upload/3645668146.png" style="width: 219px; display: block; vertical-align: top; margin: 5px auto; text-align: center;"></td>
	    <td style="width: 64.8487%; vertical-align: top; text-align: right;">По всем возникающим вопросам
		<br>Звонить: +7 (3852) 99-20-21
		<?=(isset($cmp_emailaddress)?'<br>Писать: '.$cmp_emailaddress.'</td>':'');?>
	</tr>
    </tbody>
</table>
<p><br></p>
<table border="1" cellpadding="0" cellspacing="0" style="width: 100%;">
    <tbody>
	<tr>
	    <td colspan="4" rowspan="2" style="width: 60.6949%; vertical-align: top;"><?=$cmp_bank_name?>
		<br>
		<br>Банк получателя</td>
	    <td style="width: 10.9267%; text-align: center;">БИК</td>
	    <td rowspan="2" style="width: 28.2943%; vertical-align: top;"><?=$cmp_bank_bik?>
		<br>
		<br><?=$cmp_bank_ks?></td>
	</tr>
	<tr>
	    <td style="width: 10.9267%; text-align: center;">Сч. №</td>
	</tr>
	<tr>
	    <td style="width: 11.9585%; text-align: center;">ИНН</td>
	    <td style="width: 18.5033%; text-align: center;"><?=$cmp_inn?></td>
	    <td style="width: 11.3733%; text-align: center;">КПП</td>
	    <td style="width: 18.8553%; text-align: center;"><?=$cmp_bank_kpp?></td>
	    <td rowspan="2" style="width: 10.9267%; text-align: center;">Сч. №</td>
	    <td rowspan="2" style="width: 28.2943%;"><?=$cmp_bank_rs?></td>
	</tr>
	<tr>
	    <td colspan="4" style="width: 60.6949%;"><?=$cmp_name?>
		<br>
		<br>Получатель</td>
	</tr>
    </tbody>
</table>
<p><br></p>
<p style="text-align: center;"><span style="font-size: 18px;"><strong>Счёт № <?php print($invoicenumber); ?> от <?php print($invoice_date); ?></strong></span>
    <br>
    <br>
</p>
<table style="width: 100%;">
    <tbody>
	<tr>
	    <td style="width: 12.139%; vertical-align: top;">Поставщик:
		<br>
	    </td>
	    <td style="width: 87.7707%;">
<?php
    print ($cmp_name.', ');
    print ('ИНН '.$cmp_inn.', ');
    print ('КПП '.$cmp_bank_kpp.', ');
    print ($cmp_city_zipcode.', ');
    print ($cmp_address.', ');
    print ($cmp_telephone.'.');
?>
		<br>
	    </td>
	</tr>
	<tr>
	    <td style="width: 12.139%; vertical-align: top;">Покупатель:
		<br>
	    </td>
	    <td style="width: 87.7707%;">
<?php
    print ($fullname.', ');
    print ('ИНН '.$inn.', ');
    print ('КПП '.$bank_kpp.', ');
    print ($city_postalcode.', ');
    print ($address_1.', ');
    print ($telephone_1.'.');
?>
		<br>
	    </td>
	</tr>
    </tbody>
</table>

<p><br></p>

<table border="1" cellpadding="0" cellspacing="0" style="width: 100%;">
    <tbody>
	<tr>
	    <td style="width: 3.1622%;  text-align: center;"><strong>№</strong></td>
	    <td style="width: 54.8122%; text-align: center;"><strong>Товары (работы, услуги)</strong>
		<br>
	    </td>
	    <td style="width: 9.6257%;  text-align: center;"><strong>Кол-во</strong></td>
	    <td style="width: 10.1527%; text-align: center;"><strong>Ед.</strong></td>
	    <td style="width: 10.6513%; text-align: center;"><strong>Цена</strong></td>
	    <td style="width: 11.5072%; text-align: center;"><strong>Сумма</strong></td>
	</tr>

<?php
    $total_amt = 0;
    $idx = 1;
    foreach ($invoice_details_data as $invdatakey => $inv_data_value) {
	$sub_summ = 0;

	if ($inv_data_value['debit'] > 0) {
	    $sub_summ = $inv_data_value['debit'];
	}

	if ($inv_data_value['credit'] > 0) {
	    $sub_summ = $inv_data_value['credit'];
	}
	$sub_summ = floor($sub_summ*100)/100;
	$price   = floor(100*$sub_summ/$inv_data_value['quantity'])/100;
?>
	<tr>
	    <td style="width: 3.1622%;text-align: center;"><?=$idx?></td>
	    <td style="width: 54.8122%;text-align: center;"><?=$inv_data_value['description']?></td>
            <td style="width: 9.6257%;text-align: center;"><?=$inv_data_value['quantity']?></td>
	    <td style="width: 10.1527%;text-align: center;">услуга</td>
	    <td style="width: 10.6513%;text-align: center;"><?=$price?></td>
	    <td style="width: 11.5072%;text-align: center;"><?=$sub_summ?></td>
	</tr>
<?php
	$idx++;
	$total_amt=+$sub_summ;
    }
?>
    </tbody>
</table>

<p><br></p>

<table style="width: 100%; margin-left: calc(0%);">
    <tbody>
	<tr>
	    <td style="width: 57.9743%;">
		<br>
	    </td>
	    <td style="width: 27.5894%;text-align: left;"><strong>Итого к оплате:</strong></td>
	    <td style="width: 14.3446%;text-align: right;"><?=$total_amt?></td>
	</tr>
	<tr>
	    <td style="width: 57.9743%;">
		<br>
	    </td>
	    <td style="width: 27.5894%;text-align: left;">В том числе НДС:</td>
	    <td style="width: 14.3446%;text-align: right;">Без НДС</td>
	</tr>
    </tbody>
</table>

<p>
    <br>
</p>

<p>Всего к оплате: <?php print(ucfirst(num2str($total_amt)).'.'); ?></p>
<hr style="border:1px solid grey;">
<p><br></p>

<table style="width: 100%;">
    <tbody>
	<tr>
	    <td rowspan="2" style="width: 24.9774%;">Поставщик
		<br>
	    </td>
	    <td style="width: 25%; text-align: center;"><u><?=$cmp_company_shortdesc?></u>
		<br>
	    </td>
	    <td style="width: 25%; text-align: center;">__________
		<br>
	    </td>
	    <td style="width: 25%; text-align: center;">_____________________
		<br>
	    </td>
	</tr>
	<tr>
	    <td style="width: 25%; text-align: center;">должность
		<br>
	    </td>
	    <td style="width: 25%; text-align: center;">подпись
		<br>
	    </td>
	    <td style="width: 25%; text-align: center;">расшифровка подписи
		<br>
	    </td>
	</tr>
    </tbody>
</table>