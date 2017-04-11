<?php
/**
 * Simple Anti-Spam System
 * 
 * Not for use in servers that experiences a large number of connections per second
 * 
 * FOR EDUCATIONAL PURPOSES AND EXPERIMENTAL USAGE ONLY
 */

// Prevent direct initialization of this file
if(!defined('LOCK')) { die('ACCESS DENIED!'); }

class Key {
    
    function genImage() {
        $random = bin2hex(openssl_random_pseudo_bytes(4));
        
        $im = imagecreate(100,20); // Creating an image that is 100px in width and 20px in height
        $background = imagecolorallocate($im,0,0,0); // Background color set to black
        $foreground = imagecolorallocate($im,255,255,255); // Text color set to white

        imagestring($im,5,15,2,$random,$foreground); // Time to incorporate our random key to this image
        
        // Now we need to capture the binary data of the newly generated image
        ob_start();
        imagepng($im);
        $imagedata = ob_get_contents();
        ob_end_clean();
        
        imagedestroy($im); // No more alterations can be made to the image after this point
        
        return array('data:image/png;base64,' . base64_encode($imagedata), $random);
    }
}