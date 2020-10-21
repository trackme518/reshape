<?php

function downloadYouTubeThubnailImage($youTubeLink='',$thumbNamilQuality='',$fileNameWithExt='',$fileDownLoadPath='')
    {
        $videoIdExploded = explode('?v=', $youTubeLink);   

        if ( sizeof($videoIdExploded) == 1) 
        {
            $videoIdExploded = explode('&v=', $youTubeLink);

            $videoIdEnd = end($videoIdExploded);

            $removeOtherInVideoIdExploded = explode('&',$videoIdEnd);

            $youTubeVideoId = current($removeOtherInVideoIdExploded);
        }else{
            $videoIdExploded = explode('?v=', $youTubeLink);

            $videoIdEnd = end($videoIdExploded);

            $removeOtherInVideoIdExploded = explode('&',$videoIdEnd);

            $youTubeVideoId = current($removeOtherInVideoIdExploded);
        }

        switch ($thumbNamilQuality) 
        {
            case 'LOW':
                    $imageUrl = 'http://img.youtube.com/vi/'.$youTubeVideoId.'/sddefault.jpg';
                break;

            case 'MEDIUM':
                    $imageUrl = 'http://img.youtube.com/vi/'.$youTubeVideoId.'/mqdefault.jpg';
                break;

            case 'HIGH':
                    $imageUrl = 'http://img.youtube.com/vi/'.$youTubeVideoId.'/hqdefault.jpg';
                break;

            case 'MAXIMUM':
                    $imageUrl = 'http://img.youtube.com/vi/'.$youTubeVideoId.'/maxresdefault.jpg';
                break;
            default:
                return  'Choose The Quality Between [ LOW (or) MEDIUM  (or) HIGH  (or)  MAXIMUM]';
                break;
        }  

        if( empty($fileNameWithExt) || is_null($fileNameWithExt)  || $fileNameWithExt === '') 
        {
            $toArray = explode('/',$imageUrl);
            $fileNameWithExt = md5( time().mt_rand( 1,10 ) ).'.'.substr(strrchr(end($toArray),'.'),1);
          }

          if (! is_dir($fileDownLoadPath)) 
            {
                mkdir($fileDownLoadPath,0777,true);
            }

            file_put_contents($fileDownLoadPath.$fileNameWithExt, file_get_contents($imageUrl));
            return $fileNameWithExt;   
    }
?>