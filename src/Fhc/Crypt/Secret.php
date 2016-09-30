<?php

namespace Fhc\Crypt;

/**
 * Description of Translator
 *
 * @author fcorrea
 */
class Secret
{
    public function encrypt($plaintext, $encryptionKey)
    {
        //$salt = openssl_random_pseudo_bytes(8);
        $salt = $this->generateRandomString(8);
        $salted = '';
        $dx = '';
        // Salt the key(32) and iv(16) = 48
        while (strlen($salted) < 48) {
            $dx = md5($dx.$encryptionKey.$salt, true);
            $salted .= $dx;
        }
        $key = substr($salted, 0, 32);
        $iv  = substr($salted, 32, 16);
        $encryptedData = openssl_encrypt($plaintext, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
        return base64_encode('Salted__' . $salt . $encryptedData);
    }


    public function decrypt($message, $encryptionKey)
    {
        $message = base64_decode($message);
        $salt = substr($message, 8, 8);
        $ciphertext = substr($message, 16);
        $rounds = 3;
        $data00 = $encryptionKey.$salt;
        $md5Hash = array();
        $md5Hash[0] = md5($data00, true);
        $result = $md5Hash[0];
        for ($i = 1; $i < $rounds; $i++) {
            $md5Hash[$i] = md5($md5Hash[$i - 1].$data00, true);
            $result .= $md5Hash[$i];
        }
        $key = substr($result, 0, 32);
        $iv  = substr($result, 32, 16);
        return openssl_decrypt($ciphertext, 'aes-256-cbc', $key, true, $iv);
    }
    
    function generateRandomString($length) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

}
