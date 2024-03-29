<?php
/**
 * simple method to encrypt or decrypt a plain text string
 * initialization vector(IV) has to be the same when encrypting and decrypting
 * 
 * @param string $action: can be 'encrypt' or 'decrypt'
 * @param string $string: string to encrypt or decrypt
 *
 * @return string
 */
function encrypt_decrypt($action, $string) {
    $output = false;

    $encrypt_method = "AES-256-CBC";
    $secret_key = '@#%_!?0l';
    $secret_iv = 'l0?!_%#@';


    // hash
    $key = hash('sha256', $secret_key);
    
    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
    $iv = substr(hash('sha256', $secret_iv), 0, 16);

    if ( $action == 'encrypt' ) {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    } else if( $action == 'decrypt' ) {
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }

    return $output;
}

$plain_txt = "Securedb%2NOW2020@";
echo "Plain Text =" .$plain_txt. "<br/>";

$encrypted_txt = encrypt_decrypt('encrypt', $plain_txt);
echo "Encrypted Text = " .$encrypted_txt. "<br/>";

$decrypted_txt = encrypt_decrypt('decrypt', $encrypted_txt);
echo "Decrypted Text =" .$decrypted_txt. "<br/>";

if ( $plain_txt === $decrypted_txt ) echo "SUCCESS";
else echo "FAILED";

echo "<br/>";
echo "<br/>";
echo "MD5 =" . MD5($plain_txt);


// Mistura os caracteres
$str = 'SecureDb%20';
$misturada = str_shuffle($str);

// Isto exibirá algo como: bfdaec
echo "<br>";
echo "<br>";
echo $misturada;
