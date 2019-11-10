<?php 
// CSci 130 - Web Programming - Lab Week 11

// TO DO...
// Retrieve the filename  AND the size of the matrix from the client with a POST

// Upload the file



// Read the image file 
$source_file = "ALF.jpg";
// http://php.net/manual/en/function.imagecreatefromjpeg.php
$im = imagecreatefromjpeg($source_file); 
$imgw = imagesx($im);
$imgh = imagesy($im);

$map = [];
for ($i=0; $i<$imgw; $i++) {
	    $map[$i] = [];
        for ($j=0; $j<$imgh; $j++) {      
                // get the rgb value for current pixel
                $rgb = ImageColorAt($im,$i,$j);        
                // extract each value for R, G, B (RED, GREEN, BLUE)     
                $rr = ($rgb >> 16) & 0xFF;
                $gg = ($rgb >> 8) & 0xFF;
                $bb = $rgb & 0xFF;  
                // get the Value from the RGB value
                $g = round(($rr + $gg + $bb) / 3);
                // Grayscale values have r=g=b=g (just the average of the 3 channels)
                $val = imagecolorallocate($im, $g, $g, $g);
				$map[$i][$j]=$val;
                // set the gray value
                imagesetpixel ($im, $i, $j, $val);
        }
}

// TO DO... 

// Part 1: Create a matrix of size n x n that contains in each cell the average of the values in map of the corresponding area in the image

// Part 2: Consider a global threshold to transform the matrix with gray values into binary values

// Part 3: Display the matrix in a Table in HTML
// Use AJAX to retrieve the content of the matrix that you need to display in your table


// echo 'JSON string containing the matrix representing the image'


// Display the image:
// http://php.net/manual/en/function.imagejpeg.php
header('Content-type: image/jpeg');
imagejpeg($im);
?>