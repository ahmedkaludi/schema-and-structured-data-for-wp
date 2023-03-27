<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SASWP_Youtube
{    
    static $api_base = 'https://www.googleapis.com/youtube/v3/videos';
    static $thumbnail_base = 'https://i.ytimg.com/vi/';

    // $vid - video id in youtube
    // returns - video info
    public static function getVideoInfo($vid, $api_key)
    {
        $params = array(
            'part' => 'contentDetails,snippet,statistics',
            'id' => $vid,
            'key' => $api_key,
        );

        $api_url = SASWP_Youtube::$api_base . '?' . http_build_query($params);
        $result = json_decode(@file_get_contents($api_url), true);

        if(isset($result['items'][0]['snippet']) && $result['items'][0]['snippet']){
       
            $vinfo['snippet'] = $result['items'][0]['snippet'];
        }        

        if(empty($result['items'][0]['contentDetails']))
            return null;
        $vinfo = $result['items'][0]['contentDetails'];

        if($result['items'][0]['snippet']['publishedAt']){ $vinfo['uploadDate']    = $result['items'][0]['snippet']['publishedAt']; }
        if($result['items'][0]['snippet']['title']){       $vinfo['title']         = $result['items'][0]['snippet']['title'];       }
        if($result['items'][0]['snippet']['description']){ $vinfo['description']   = $result['items'][0]['snippet']['description']; }
        if($result['items'][0]['statistics']['viewCount']){   $vinfo['viewCount']   = $result['items'][0]['statistics']['viewCount'];  }


        $interval = new DateInterval($vinfo['duration']);
        $vinfo['duration_sec'] = $interval->h * 3600 + $interval->i * 60 + $interval->s;

        $vinfo['thumbnail']['default']       = self::$thumbnail_base . $vid . '/default.jpg';
        $vinfo['thumbnail']['mqDefault']     = self::$thumbnail_base . $vid . '/mqdefault.jpg';
        $vinfo['thumbnail']['hqDefault']     = self::$thumbnail_base . $vid . '/hqdefault.jpg';
        $vinfo['thumbnail']['sdDefault']     = self::$thumbnail_base . $vid . '/sddefault.jpg';
        $vinfo['thumbnail']['maxresDefault'] = self::$thumbnail_base . $vid . '/maxresdefault.jpg';

        return $vinfo;
    }
}