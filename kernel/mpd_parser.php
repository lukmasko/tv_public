<?php

namespace Kernel\MpdParser;

use App\Models\VideoMpdModel;

class MpdParser
{
    private function MpdParser(){

    }

    public static function getMpd($fname) : VideoMpdModel
    {
        $mpd = new VideoMpdModel();
        $xmlDoc=new \DOMDocument();
        $xmlDoc->load($fname);

        $node = $xmlDoc->getElementsByTagName('MPD')[0];
        $duration = $node->getAttribute('mediaPresentationDuration');
        $mpd->duration = MpdParser::parseDuration($duration);

        $node = $xmlDoc->getElementsByTagName('Representation')[0];
        $mpd->codecs = $node->getAttribute('codecs');
        $mpd->width = intval($node->getAttribute('width'));
        $mpd->height = intval($node->getAttribute('height'));
        $mpd->mime_type = $node->getAttribute('mimeType');

        return $mpd;
    }

    public static function parseDuration($str) : string
    {
        $res = '';
        for($i=0; $i<strlen($str); $i++) // "PT0H0M33.831S"
        {
            if($str[$i] == 'P' || $str[$i] == 'T' || $str[$i] == 'S')
                continue;

            if($str[$i] == 'H' || $str[$i] == 'M'){
                $res .= ':';
                continue;
            }
            
            $res .= $str[$i];
        }
        return $res;
    }
}