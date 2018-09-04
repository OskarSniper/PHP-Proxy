<?php
/*
 * Author: Waldbauer Sebastian
 * Version: 0.2017.11.21
*/
/* Configuration */
$allowedRessources = array();

// Cache enabled ? Or just use interal proxy
$cacheEnabled = false;

// Absolute path to cache directory
$cacheDirectory = "cache/";

// Cache refresh after 5 minutes
$refreshCacheTime = 300;

/* Security section */
$serverSignature = "gws (rev2017.182.504.11)";
$allowedIpAddresses = array();

// Disable output compression by default...
ini_set('zlib.output_compression', 'Off');

/* Do not edit, only if you know what you're doing! */

function base64UrlEncode($data)
{
  return strtr(rtrim(base64_encode($data), '='), '+/', '-_');
}

function base64UrlDecode($base64)
{
  return base64_decode(strtr($base64, '-_', '+/'));
}

function find_closest($array, $date)
{
    foreach($array as $day)
    {
      $interval[] = abs($date - $day['Last-Modified']);
    }
    asort($interval);
    $closest = key($interval);
    return $array[$closest];
}

function getContentType($ext) {
        $mime_types = array(

            'txt' => array('content-type' => 'text/plain', 'type' => 'file'),
            'htm' => array('content-type' => 'text/html', 'type' => 'file'),
            'html' => array('content-type' => 'text/html', 'type' => 'file'),
            'php' => array('content-type' => 'text/html', 'type' => 'file'),
            'css' => array('content-type' => 'text/css', 'type' => 'file'),
            'js' => array('content-type' => 'text/javascript', 'type' => 'file'),
            'json' => array('content-type' => 'application/json', 'type' => 'file'),
            'xml' => array('content-type' => 'application/xml', 'type' => 'file'),
            'swf' => array('content-type' => 'application/x-shockwave-flash', 'type' => 'file'),
            'flv' => array('content-type' => 'video/x-flv', 'type' => 'file'),

            // images
            'png' => array('content-type' => 'image/png', 'type' => 'image'),
            'jpe' => array('content-type' => 'image/jpeg', 'type' => 'image'),
            'jpeg' => array('content-type' => 'image/jpeg', 'type' => 'image'),
            'jpg' => array('content-type' => 'image/jpeg', 'type' => 'image'),
            'gif' => array('content-type' => 'image/gif', 'type' => 'image'),
            'bmp' => array('content-type' => 'image/bmp', 'type' => 'image'),
            'ico' => array('content-type' => 'image/vnd.microsoft.icon', 'type' => 'image'),
            'tiff' => array('content-type' => 'image/tiff', 'type' => 'image'),
            'tif' => array('content-type' => 'image/tiff', 'type' => 'image'),
            'svg' => array('content-type' => 'image/svg+xml', 'type' => 'image'),
            'svgz' => array('content-type' => 'image/svg+xml', 'type' => 'image'),

            // archives
            'zip' => array('content-type' => 'application/zip', 'type' => 'archieve'),
            'rar' => array('content-type' => 'application/x-rar-compressed', 'type' => 'archieve'),
            'exe' => array('content-type' => 'application/x-msdownload', 'type' => 'archieve'),
            'msi' => array('content-type' => 'application/x-msdownload', 'type' => 'archieve'),
            'cab' => array('content-type' => 'application/vnd.ms-cab-compressed', 'type' => 'archieve'),

            // audio/video
            'mp3' => array('content-type' => 'audio/mpeg', 'type' => 'audio'),
            'qt' => array('content-type' => 'video/quicktime', 'type' => 'video'),
            'mov' => array('content-type' => 'video/quicktime', 'type' => 'video'),

            // adobe
            'pdf' => array('content-type' => 'application/pdf', 'type' => 'adobe'),
            'psd' => array('content-type' => 'image/vnd.adobe.photoshop', 'type' => 'adobe'),
            'ai' => array('content-type' => 'application/postscript', 'type' => 'adobe'),
            'eps' => array('content-type' => 'application/postscript', 'type' => 'adobe'),
            'ps' => array('content-type' => 'application/postscript', 'type' => 'adobe'),

            // ms office
            'doc' => array('content-type' => 'application/msword', 'type' => 'msoffice'),
            'rtf' => array('content-type' => 'application/rtf', 'type' => 'msoffice'),
            'xls' => array('content-type' => 'application/vnd.ms-excel', 'type' => 'msoffice'),
            'ppt' => array('content-type' => 'application/vnd.ms-powerpoint', 'type' => 'msoffice'),

            // open office
            'odt' => array('content-type' => 'application/vnd.oasis.opendocument.text', 'type' => 'openoffice'),
            'ods' => array('content-type' => 'application/vnd.oasis.opendocument.spreadsheet', 'type' => 'openoffice')
        );
    
    return $mime_types[$ext];
}

if(isset($_GET['u'])) {
    
    //Check if ip address contains smth, if true then check
    if(sizeof($allowedIpAddresses) > 0) {
        if(!in_array($_SERVER['REMOTE_ADDR'], $allowedIpAddresses)) {
            header("HTTP/1.1 401 Unauthorized"); 
            return; 
        }
    }
    
    // Clean url
    $url = filter_var($_GET['u'], FILTER_SANITIZE_URL);
    
    // Check if url is allowed
    if(sizeof($allowedRessources) > 0) {
        if(!in_array($url, $allowedRessources)) { 
            header("HTTP/1.1 403 Forbidden"); 
            exit; 
        }
    }
    
    $buffer = file_get_contents($url);
    
    $start = microtime(true);
    if(isset($_GET['c']) && $cacheEnabled) {
        $explodeExtension = explode('.', basename($url));
        $extension = end($explodeExtension);
        $buffer = "";

        // Write onto disk
        if((!file_exists($cacheDirectory . base64UrlEncode($url) . "." . $extension)))
        {
            $buffer = file_get_contents($url);
            $buffer = gzencode($buffer, 9);
            
            file_put_contents($cacheDirectory . base64UrlEncode($url) . "." . $extension, $buffer);
            $data = $buffer;
        } else {
            if(filemtime($cacheDirectory . base64UrlEncode($url) . "." . $extension) >= (filemtime($cacheDirectory . base64UrlEncode($url) . "." . $extension) + $refreshCacheTime))
            {
                $buffer = file_get_contents($url);
                $buffer = gzencode($buffer, 9);
                
                file_put_contents($cacheDirectory . base64UrlEncode($url) . "." . $extension, $buffer);
                $data = $buffer;
            } else {
                $data = file_get_contents($cacheDirectory . base64UrlEncode($url) . "." . $extension);
            }
        }

        // Send not modified back if file hasnt changed!
        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) || isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
            if (($_SERVER['HTTP_IF_MODIFIED_SINCE'] != (gmdate('D, d M Y H:i:s ', filemtime($cacheDirectory . base64UrlEncode($url) . "." . $extension)) . 'GMT')) || str_replace('"', '', stripslashes($_SERVER['HTTP_IF_NONE_MATCH'])) == hash('sha256', $data)) {
                header('HTTP/1.1 304 Not Modified');
                exit;
            }
        }

        $contentType = getContentType($extension);
        
        header("Server: " . $serverSignature);

        // Set Content-Type
        header("Content-Type: " . $contentType['content-type']);

        // Set max-age to 1 day ( recommended from google )
        header('Cache-Control: public, max-age=' . (86400 * 30) . ', s-maxage=' . (86400 * 30));

        // Last-Modified from latest file
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s ', filemtime($cacheDirectory . base64UrlEncode($url) . "." . $extension)) . 'GMT');

        // Etag
        header('ETag: ' . hash('sha256', $data));

        // Expire in one day
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + (86400 * 30)) . ' GMT');
        
        // GZip enconding
        header('Content-Encoding: gzip');
        
        // correct length
        header('Content-Length: ' . strlen($data));

        // Adding debug header
        $end = microtime(true) - $start;
        header("X-Service-ServingTime: " . $end);
        
        // Write everything out
        echo $data;
    } else {
        $explodeExtension = explode('.', basename($url));
        $extension = end($explodeExtension);
        $contentType = getContentType($extension);

        header("Server: " . $serverSignature);
        
        // Set Content-Type
        header("Content-Type: " . $contentType['content-type']);
        
        // GZip enconding
        header('Content-Encoding: gzip');
        
        // correct length
        header('Content-Length: ' . strlen($data));
        
        // Adding debug header
        $end = microtime(true) - $start;
        header("X-Service-ServingTime: " . $end);
        
        $buffer = file_get_contents($url);
        echo $buffer;
    }
    
} else {
	header("HTTP/1.1 404 Not Found");
	echo "<html>
            <head>
                <title>404 Not Found</title>
            </head>
            <body bgcolor=\"white\">
                <center>
                    <h1>404 Not Found</h1>
                </center>
		          <hr>
                <center>gws</center>
            </body>
		</html>
	";
}
?>
