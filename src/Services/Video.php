<?php

namespace App\Services;

class Video
{
    
    /**
     * Use Case : Check if the form sent data is correct
     *
     * @param string $videoCode
     * @return boolean
     */
    public function isVideoCodeCorrect($videoCode)
    {
        if($this->detectSource($videoCode) !== null) {
            return true;
        }
        return false;
    }

    /**
     * Use Case : convert sent code to url (if iframe sent)
     *
     * @param string $videoCode
     * @return boolean
     */
    public function isIframe($videoCode)
    {
        if(preg_match("#iframe#", $videoCode)) {
            return true;
        }
        return false;
    }

    /**
     * Use Case : detect if url is a video
     *
     * @param string $url
     * @return boolean
     */
    public function isVideo($url)
    {
        if($this->detectSource($url) !== null) {
            return true;
        }
        return false;
    }

    /**
     * Use Case : convert sent code to url (if iframe sent)
     *
     * @param string $videoCode
     * @return string
     */
    public function videoCodeToUrl($videoCode)
    {
        $videoCode = preg_replace("#[(\n)(\r\n)]#","", $videoCode);
        $videoCode = preg_replace("#(.*)src=[\"\']{0,1}([a-zA-Z0-9\.:/]*)[\"\']{0,1}(.*)#","$2",$videoCode);
        return $videoCode;
    }


    /**
     * Use Case : Return iframe html code from video url
     *
     * @param string $url
     * @return string
     */
    public function createIframe($url)
    {   
        $videoSource = $this->detectSource($url);
        switch ($videoSource) {
            case 'youtube':
                return '<iframe class="media" src="' . $url . '" data-media="' . $url . '" data-type="youtube" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
            case 'dailymotion':
                return '<iframe class="media" data-media="' . $url . '" frameborder="0" src="' . $url . '" allowfullscreen allow="autoplay"></iframe>';
            case 'vimeo':
                return '<iframe class="media" data-media="' . $url . '" src="' . $url . '" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>';
        }
        return null;    
    }

    /**
     * Detect source of iframe or url
     *
     * @param string $code
     * @return string
     */
    private function detectSource($code)
    {
        $domain = $this->extractDomain($code);
        if(preg_match("#youtu#", $domain)) {
            return "youtube";
        }
        if(preg_match("#dai#", $domain)) {
            return "dailymotion";
        }
        if(preg_match("#vimeo#", $domain)) {
            return "vimeo";
        }
        return null;
    }

    /**
     * Extract domain of a iframe or url
     *
     * @param string $code
     * @return string
     */
    private function extractDomain($code) {
        $code = preg_replace("#[(\n)(\r\n)]#","",$code);
        $code = preg_replace("#(.*?)://([a-z.]*)/(.*)#","$2",$code);
        return $code;
    }

}
