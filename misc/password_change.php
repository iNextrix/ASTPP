<?php
$astpp_config = parse_ini_file ( "/var/lib/astpp/astpp-config.conf" );
//print_r($astpp_config);exit;
$con=mysqli_connect( $astpp_config ['dbhost'],$astpp_config ['dbuser'],$astpp_config ['dbpass'],$astpp_config ['dbname']);
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }
//mysqli_select_db($astpp_config ['dbname']) or die(mysqli_error());
$private_key='8YSDaBtDHAB3EQkxPAyTz2I5DttzA9uR';
$query="CREATE TABLE accounts_backup LIKE accounts";
$result =mysqli_query($con,$query);
$query="insert into accounts_backup select * from accounts";
$result =mysqli_query($con,$query);
$query="select id,password from accounts";
$result =mysqli_query($con,$query);
//print_r($result);exit;
while($row=mysqli_fetch_assoc($result)){
	
	$plain_password=password_decrypt($row['password']);
//print_r($plain_password);exit;
	$new_password=password_encrypt($plain_password);
	$query="update accounts set password='$new_password' where id=".$row['id'];
	$update_result =mysqli_query($con,$query);
}

function password_decrypt($value){
	global $private_key;
	$crypttext = decode_params ( $value );
	$iv_size = mcrypt_get_iv_size ( MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB );
	$iv = mcrypt_create_iv ( $iv_size, MCRYPT_RAND );
	$decrypttext = mcrypt_decrypt ( MCRYPT_RIJNDAEL_256, $private_key, $crypttext, MCRYPT_MODE_ECB, $iv );
	return trim ( $decrypttext );
}
function encode_params($string) {
		$data = base64_encode ( $string );
		$data = str_replace ( array (
				'+',
				'/',
				'='
		), array (
				'-',
				'$',
				''
		), $data );
		return $data;
	}
function decode_params($string) {
		$data = str_replace ( array (
				'-',
				'$'
		), array (
				'+',
				'/'
		), $string );
		$mod4 = strlen ( $data ) % 4;
		if ($mod4) {
			$data .= substr ( '====', $mod4 );
		}
		return base64_decode ( $data );
	}
function password_encrypt($value){
	global $private_key;
	$ivSize = openssl_cipher_iv_length('BF-ECB');
	$iv = openssl_random_pseudo_bytes($ivSize);

	$encrypted = openssl_encrypt($value, 'BF-ECB', $private_key, OPENSSL_RAW_DATA, $iv);
	
	// For storage/transmission, we simply concatenate the IV and cipher text
	$encrypted = encode_params($encrypted);
	//echo $encrypted;exit;
	return  $encrypted;
}
?>
