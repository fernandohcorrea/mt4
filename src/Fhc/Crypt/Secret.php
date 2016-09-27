<?php

namespace Fhc\Crypt;

/**
 * Description of Translator
 *
 * @author fcorrea
 */
class Secret
{
    private $cfg;
    private $master_key;

    public function __construct()
    {
        $this->cfg = \Fhc\Config\Loader::get('crypt_*');
        $this->master_key = $this->cfg['crypt_master_key'];
    }


    public function encrypt($plaintext, $encryptionKey)
    {
        $nonce = $this->generateRandomString(16);
        $ciphertext = openssl_encrypt(
                $plaintext, 'aes-256-ctr', $encryptionKey, OPENSSL_RAW_DATA, $nonce
        );
        $mac = hash_hmac('sha512', $nonce . $ciphertext, $this->master_key, true);
        return base64_encode($mac . $nonce . $ciphertext);
    }


    public function decrypt($message, $encryptionKey)
    {
        $message = base64_decode($message);
        $mac = mb_substr($message, 0, 64, '8bit');
        $nonce = mb_substr($message, 64, 16, '8bit');
        $ciphertext = mb_substr($message, 80, null, '8bit');

        $calc = hash_hmac('sha512', $nonce . $ciphertext, $this->master_key, true);
        if (!hash_equals($calc, $mac)) {
            throw new Exception('Invalid MAC');
        }
        return openssl_decrypt(
                $ciphertext, 'aes-256-ctr', $encryptionKey, OPENSSL_RAW_DATA, $nonce
        );
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
