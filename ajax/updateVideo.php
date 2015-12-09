<?php
require_once('../libs/Storage.php');

$db = new Storage();

$data['getVideo'] = $db->select("SELECT meta_value FROM wp_postmeta WHERE meta_key = '_tern_wp_youtube_video' LIMIT 1101");

//print_r($data['getDatum']);
if(!sizeof($data['getVideo'])){
  echo
  '<div class="alert alert-info">No Data.</div>';
}else{
	foreach ($data['getVideo'] as $keys => $value) {
		$yid = $value['meta_value'];

		$check = $db->select("SELECT count(*) as count FROM yapi WHERE yid ='$yid'");

		if($check[0]['count']==0){
				$url = "https://www.youtube.com/watch?v=".$yid;
		    $result = get_web_page($url);
		    //echo $url;
		    if ( $result['errno'] != 0 ){
		      echo "error: bad url | timeout | redirect loop ...";
		    }elseif( $result['http_code'] != 200 ){
		      echo "error: no page | no permissions | no service ";
		    }else{
		      $page = $result['content'];
		      if($result==TRUE){
		        $str = $page;
		        $preg2=preg_match_all('#<title>(.*?)</title>#', $str, $parts2);
		        $preg3=preg_match_all('/<meta property="og:image" content="(.*)">/',$str,$parts3);
		        $preg4=preg_match_all('/<meta property="og:description" content="(.*)">/',$str,$parts4);
		        $preg5=preg_match_all('/<link itemprop="url" href="(.*)">/',$str,$parts5);
		        $daten['yid'] = $yid;
		        $daten['url'] = $url;
		        $daten['title'] = $parts2[1][0];
		        $daten['thumbnail'] = $parts3[1][0];
		        $daten['description'] = $parts4[1][0];
		        $daten['author'] = $parts5[1][2];
		        $db->insert('yapi',$daten);
		      }
		    }
		  }
	}
}

function get_web_page( $url )
{
					$options = array(

							CURLOPT_CUSTOMREQUEST  =>"GET",        //set request type post or get
							CURLOPT_POST           =>false,        //set to GET
							CURLOPT_RETURNTRANSFER => true,     // return web page
							CURLOPT_HEADER         => false,    // don't return headers
							CURLOPT_FOLLOWLOCATION => true,     // follow redirects
							CURLOPT_ENCODING       => "",       // handle all encodings
							CURLOPT_AUTOREFERER    => true,     // set referer on redirect
							CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
							CURLOPT_TIMEOUT        => 120,      // timeout on response
							CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
	);

					$ch      = curl_init( $url );
					curl_setopt_array( $ch, $options );
					$content = curl_exec( $ch );
					$err     = curl_errno( $ch );
					$errmsg  = curl_error( $ch );
					$header  = curl_getinfo( $ch );
					curl_close( $ch );

					$header['errno']   = $err;
					$header['errmsg']  = $errmsg;
					$header['content'] = $content;
					return $header;
}
?>
