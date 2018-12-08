<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShortLink;

class HashGeneratorController extends Controller
{
    const SHORT_CODE_ALPHABET = array(
        '0', '1', '2', '3', '4', '5', '6', '7',
        '8', '9', 'a', 'b', 'c', 'd', 'e', 'f',
        'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n',
        'o', 'p', 'q', 'r', 's', 't', 'u', 'v',
        'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D',
        'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L',
        'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T',
        'U', 'V', 'W', 'X', 'Y', 'Z', '_', '-'
    );
    
    const SHORT_CODE_CONVERT_BASE = 64;

    const MICROTIME_LENGTH = 3;
    
    public function newHash()
    {
        $hash = '';
        
        $idMax = ShortLink::getMaxSourceID();
        
        $microsecs = $this->_getMicroseconds(static::MICROTIME_LENGTH);

        if ($microsecs < 10 * (static::MICROTIME_LENGTH - 1)) {
            $microsecs = $microsecs + 100;
        }

        $uniqId = intval($idMax.$microsecs);

        while ($uniqId > 0) {
            $remainder = $uniqId % static::SHORT_CODE_CONVERT_BASE;
            $uniqId = ($uniqId - $remainder) / static::SHORT_CODE_CONVERT_BASE;
            $hash = static::SHORT_CODE_ALPHABET[$remainder].$hash;
        }

        $hash = strrev($hash);
        
        if ($this->_isExistsHash($hash)) {
            return $this->newHash();
        }
        
        return $hash;
    }
    
    private function _isExistsHash($hash)
    {
        return ShortLink::where('hash', $hash)->first() || 
               $this->_hasHashInBlackList($hash);
    }
    
    private function _getMicroseconds($length)
    {
        $microsec = explode(' ', microtime());
        $microsec = $microsec[0];
        $microsec = $microsec*1000000;
        $microsec = substr($microsec, $length);

        return $microsec;
    }
    
    public function isValidUrl($url)
    {
        return preg_match('#^((http)|(https))\:\/\/(.*?)\.(.*?)$#Umis', $url);
    }
    
    private function _hasHashInBlackList($hash)
    {
        $blackList = [
            'admin'
        ];
        
        return in_array($hash, $blackList);
    }
}
