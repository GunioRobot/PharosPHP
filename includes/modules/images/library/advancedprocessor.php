<?php

class AdvancedProcessor extends ImageProcessor {
	
	private function num($x)
	{
		return sprintf("%c%c",($x>>8)&0xff,$x&0xff);
	}
	
	public function GenerateAco($colors=array(), $file)
	{
		
		$result 	= $this->num(1);
		$ncolors	= count($colors);
		$result 	.= $this->num($ncolors);
		$counter = 0;
	    
		foreach ($colors as $color)
		{
			
	        list($r, $g, $b) = $color;
	        $result 	.= $this->num(0);
	        $result 	.= $this->num(($r<<8)|$r);
	        $result 	.= $this->num(($g<<8)|$g);
	        $result 	.= $this->num(($b<<8)|$b);
	        $result 	.= $this->num(0);
	        $counter++;
		}
	
	    
	    $result 	.= $this->num(2);
	    $result 	.= $this->num($ncolors);
	
		foreach ($colors as $color)
		{
	            	
			list($r, $g, $b) = $color;
	        $result 	.= $this->num(0);
	       	$result 	.= $this->num(($r<<8)|$r);
	        $result 	.= $this->num(($g<<8)|$g);
	        $result 	.= $this->num(($b<<8)|$b);
	        $result 	.= $this->num(0);
	        $result 	.= $this->num(0);
	        $result 	.= $this->num(7);
	        $buf = sprintf("%02x",$r);
	        $x = $buf[0];
	        $result 	.= $this->num($x);
	        $result 	.= $this->num($x);
	        $buf = sprintf("%02x",$g);
	        $x = $buf[0];
	        $result 	.= $this->num($x);
	        $result 	.= $this->num($x);
	        $buf = sprintf("%02x",$b);
	        $x = $buf[0];
	        $result 	.= $this->num($x);
	        $result 	.= $this->num($x);
	        $result 	.= $this->num(0);
	   	
		}
	
	    file_put_contents($file, $result);
		
	}
	
	/**
	 * Get the color of the pixel on position $x,$y from
	 * the image $file
	 *
	 * @param string $file
	 * @param int $x
	 * @param int $y
	 * @return array
	 */
	public function GetPixelColor($file, $x, $y)
	{
		
		// Get extension
		$extension = end(explode(".", $file));
		
		// Get image source
		$this->Load($file);
		$image = $this->GetResource();
		
		// Get pixel color
		$rgb = imagecolorat($image, $x, $y);
		$r = ($rgb >> 16) & 0xFF;
		$g = ($rgb >> 8) & 0xFF;
		$b = $rgb & 0xFF;
		
		// Return pixel color
		return array($r,$g,$b);
	}
	
	/**
	 * Generate colorarray for a given image
	 *
	 * @param string $file
	 * @return array
	 */
	public function GetColorMap($file)
	{
		
		// Get image information
		list($width, $height, $type, $attr) = getimagesize($file);
		
		// Calculate step
		$horizontalstep = floor($width / 3);
		$verticalstep = floor($height / 3);
		
		// Get pixel colors
		$map[] = $this->GetPixelColor($file, $horizontalstep, $verticalstep);
		$map[] = $this->GetPixelColor($file, $horizontalstep * 2, $verticalstep);
		$map[] = $this->GetPixelColor($file, $horizontalstep, $verticalstep * 2);
		$map[] = $this->GetPixelColor($file, $horizontalstep * 2, $verticalstep * 2);
		
		// Return map
		return $map;
		
	}
	
	/**
	 * Create image from colorarray
	 * 
	 * If file is empty the image will be outputed to the screen else
	 * it will be saved to the given file
	 *
	 * @param array $map
	 * @param int $colorwidth
	 * @param int $colorheight
	 * @param string $file
	 */
	public function CreateMapImage($map, $colorwidth=40, $colorheight=20)
	{
	
		$img = imagecreatetruecolor($colorwidth * count($map), $colorheight);
		$backgroundcolor = imagecolorallocate($img, 255, 255, 255);
		imagefill($img, 0, 0, $backgroundcolor);
		
		$counter = 0;
		foreach ($map as $color)
		{
			$r = $color[0];
			$g = $color[1];
			$b = $color[2];
			$startpointx = $counter * $colorwidth;
			$startpointy = 0;
			$endpointx = $startpointx + $colorwidth;
			$endpointy = $colorheight;
			imagefilledrectangle($img, $startpointx, $startpointy, $endpointx, $endpointy, imagecolorallocate($img, $r, $g, $b));
			$counter++;
		}
		
		$this->_extension = "gif";
		$this->_mime = "image/gif";
		$this->SetResource($img);
	    
	}
	
	/**
	 * Batch resize
	 * 
	 * @access public
	 * @param string $directory
	 * @param string $destination
	 * @param int $width
	 * @param int $height
	 * @param defined $mode
	 * @return void
	 */
	public function BatchResize($directory, $destination, $width=null, $height=null, $mode=RESIZE_STRETCH){
		$dir_handle = @opendir($directory) or die("Unable to open $path");
		while ($file = readdir($dir_handle)) {
			$check_extension = strtolower($file);
			if(($file != ".." && $file != ".") && (strstr($check_extension, ".jpg") || strstr($check_extension, ".png") || strstr($check_extension, ".gif"))){
				$this->Load($directory . $file);
				$this->Resize($width, $height, $mode);
				$this->Save($destination . $this->_image_name . "." . $this->_extension);
			}
		}
		closedir($dir_handle);
	}
	
}

/*

$map = getColorMap("KioskModule1.png");

generateAco($map, "tester4.aco");
createMapImage($map);
*/

//$Colormap->GenerateAco($map, "new.aco");


?>