<?php


namespace app\common\encrypt;


class Aes
{


    /**
     * [encrypt description]
     * 使用mcrypt库进行加密
     * @param  [type] $input
     * @param  [type] $key
     * @return [type]
     */
    public static function mcryptEncrypt($input)
    {
        $key = config('encryptKey.AES_TOKEN_KEY');
        $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
        $input = self::pkcs5Pad($input, $size);
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);//MCRYPT_DEV_URANDOM
        mcrypt_generic_init($td, $key, $iv);
        $data = mcrypt_generic($td, $input);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $data = base64_encode($data);
        return $data;
    }

    /**
     * [pkcs5Pad description]
     * @param  [type] $text
     * @param  [type] $blocksize
     * @return [type]
     */
    private static function pkcs5Pad($text, $blocksize)
    {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    /**
     * [decrypt description]
     * 使用mcrypt库进行解密
     * @param  [type] $sStr
     * @param  [type] $sKey
     * @return [type]
     */
    public static function mcryptDecrypt($sStr, $sKey)
    {
        $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB), MCRYPT_RAND);//MCRYPT_DEV_URANDOM
        $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $sKey, base64_decode($sStr), MCRYPT_MODE_ECB, $iv);
        $dec_s = strlen($decrypted);
        $padding = ord($decrypted[$dec_s - 1]);
        $decrypted = substr($decrypted, 0, -$padding);
        return $decrypted;
    }

    /**
     * [opensslDecrypt description]
     * 使用openssl库进行加密
     * @param  [type] $sStr
     * @param  [type] $sKey
     * @return [type]
     */
    public static function opensslEncrypt($sStr, $key = '', $method = 'AES-128-ECB')
    {
        if (empty($key))
            $str = openssl_encrypt($sStr, $method, config('encryptKey.AES_TOKEN_KEY'));
        else
            $str = openssl_encrypt($sStr, $method, $key);

        return $str;
    }

    /**
     * [opensslDecrypt description]
     * 使用openssl库进行解密
     * @param  [type] $sStr
     * @param  [type] $sKey
     * @return [type]
     */
    public static function opensslDecrypt($sStr, $key = '', $method = 'AES-128-ECB')
    {

        if (empty($key))
            $str = openssl_decrypt($sStr, $method, config('encryptKey.AES_TOKEN_KEY'));
        else
            $str = openssl_decrypt($sStr, $method, $key);

        return $str;
    }

}