<?php
include 'imagecreatefrombmp.php';

$graphFolder = 'graphic';

//Folder name
$graph = 'crocy';

$path = $graphFolder . '/' . $graph. '/';

//Default size
$size = 128;


//Start point
$currentX = 1;
$currentY = 1;


/*
 File prefixes.
 Example file name: walking e0000
 Action: walking
 Direction: e
 Frame: 0000

 You have to change for different tiles.

**/
$prefixes = array(
/* sheep
'schaf frisst e', 'schaf frisst n', 'schaf frisst s', 'schaf frisst w',
'schaf frisst ne', 'schaf frisst se', 'schaf frisst sw', 'schaf frisst nw'
*/
/* 
'walking e', 'walking n', 'walking s', 'walking w',
'running e', 'running n', 'running s', 'running w',
'been hit e', 'been hit n', 'been hit s', 'been hit w',
'tipping over e', 'tipping over n', 'tipping over s', 'tipping over w'
*/
'walking se', 'walking nw', 'walking sw', 'walking ne',
//'running se', 'running nw', 'running sw', 'running ne',
//'been hit se', 'been hit nw', 'been hit sw', 'been hit ne',
'tipping over se', 'tipping over nw', 'tipping over sw', 'tipping over ne'
//'disintegrate se', 'disintegrate nw', 'disintegrate sw', 'disintegrate ne',
);

//Final sheet sizes
$sheetWidth = $size * 8;
$sheetHeight = $size * count($prefixes);

//Creating alpha channel supported image
$sheet = imagecreatetruecolor($sheetWidth, $sheetHeight);

//Enabling alpha channel
imagealphablending($sheet, false);
imagesavealpha($sheet, true);

foreach($prefixes as $prefix) {
	$currentX = 1;
	for($i = 0; $i<17; $i++) {
		
		//Skip some frames for dying effect.		
		if($currentY > 4)if($i == 1 || $i == 4 || $i == 8) continue;
		if($i < 10)
			$fileName = $prefix.'000'.$i . '.bmp';
		else 
			$fileName = $prefix.'00'.$i . '.bmp';
		$file = $path.$fileName;
		
		if(!file_exists($file)) continue;
		
		//Original file
		$img = imagecreatefrombmp($file);
		imagecopy($sheet,$img,
		($currentX-1)*$size,($currentY-1)*$size,
		0,0,$size,$size);
		
		
		$currentX ++;
	}
	$currentY++;
}

//Replacing the brown background to transparent.
for ($x = 0; $x < imagesx($sheet); $x++) {
    for ($y = 0; $y < imagesy($sheet); $y++) {
        $src_pix = imagecolorat($sheet,$x,$y);
        $src_pix_array = rgb_to_array($src_pix);

            // check for chromakey color. RGB(106,76,48), RGB(97,68,43) = different tons of brown
        	$backgroundColor['R'] = 97;
        	$backgroundColor['G'] = 68;
        	$backgroundColor['B'] = 43;
        	
            if ($src_pix_array[0] == $backgroundColor['R'] && $src_pix_array[1] == $backgroundColor['G'] 
            	&& $src_pix_array[2] == $backgroundColor['B']) {
       	 		imagesetpixel($sheet, $x, $y, imagecolorallocatealpha($sheet,0,0,0,127)); 
            }
    }
}

//Send image to browser
header('Content-Type:image/png');
imagepng($sheet);
imagedestroy($sheet);


//Bit shifting shit
function rgb_to_array($rgb) {
    $a[0] = ($rgb >> 16) & 0xFF;
    $a[1] = ($rgb >> 8) & 0xFF;
    $a[2] = $rgb & 0xFF;

    return $a;
}
