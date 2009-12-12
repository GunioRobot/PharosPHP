<?php

	/* ---------------------------------------------------------------------------------------
	
		Image class for PHP - Requires PHP 5, but not the GD library
	
		Given a path, will tell you filetype, dimensions, let you resize and save.
		Can also get the HTML img element as string to insert into documents.
	
		Original code take from:
			http://us2.php.net/imagecopyresampled
			zorroswordsman@gmail.com
		
		Changelog
		------------------------------------------------------------------------------------
		
		Jan 24, 2009 (Matt)
			- Better error handling of missing files and empty or null inputs to all methods
			- Added 'get_html()' method for returning an HTML img element
			- Proper destructor method added
		
		Jan 9, 2009 (Matt)
			- Added constructor function for convenience
			- Rewrote set_img and save_img for clarity and to save transparency on resizing

	--------------------------------------------------------------------------------------- */

	class Image {
	
	    // Variables
	    private $img_input;
	    private $img_output;
	    private $img_src;
	    private $format;
	    private $quality = 80;
	    private $x_input;
	    private $y_input;
	    private $x_output;
	    private $y_output;
	    private $resize;
	    
	    
	    // For convenience, ie; $myImg = new Image($path,100,100,80);
	    public function __construct($path,$width=null,$height=null,$quality=null) {
	    	if ( !is_null($path) AND $path != '' AND is_file($path) ) {
		
				try {
			    	$this->set_img($path);
		    		if ( !is_null($width) && !is_null($height) ) $this->set_size($width,$height);
			    	if ( !is_null($quality) && is_int($quality) ) $this->set_quality($quality);
				} catch(Exception $e) {
					unset($this);
					throw new Exeception($e->getMessage());
				}
				
	    	} 
	    }
	    
	    
	    	
	    // Set image
	    public function set_img($path) {
	    	
	        // Find format
			$type = exif_imagetype($path);
				
	        // JPEG image
	        if( $type == IMAGETYPE_JPEG ) {
	            $this->format = $type;
	            $this->img_input = ImageCreateFromJPEG($path);
	            $this->img_src = $path;
	        }
	
	        // PNG image
	        elseif( $type == IMAGETYPE_PNG ) {
	            $this->format = $type;
	            $this->img_input = ImageCreateFromPNG($path);
	            $this->img_src = $path;
	        }
	
	        // GIF image
	        elseif( $type == IMAGETYPE_GIF) {
	            $this->format = $type;
	            $this->img_input = ImageCreateFromGIF($path);
	            $this->img_src = $path;
	        }
	        
	        // Unsupported file type or not a file
	        else throw new Exception("Unsupported filetype of ($type)!");
	
	        // Get dimensions
	        $this->x_input = $this->x_output = imagesx($this->img_input);
	        $this->y_input = $this->y_output = imagesy($this->img_input);
	    }
	
	
	
	    // Set maximum image size (width, height)
	    public function set_size($width, $height) {
	    	    	
	    	// If resizing
	    	if ( $this->x_input > $width OR $this->y_input > $height ) {
	    			
				// If wider than max width, width = max_width and resize on y axis
		        if( $this->x_input > $width ) {
		        	$this->x_output = $width;
		        	$this->y_output = round( ($this->x_output / $this->x_input) * $this->y_input);
		        }
		        
		        // If still taller than max height, then resize again
		        if ( $this->y_output > $height ) {
		        	$this->y_output = $height;
		        	$this->x_output = round( ($this->y_output / $this->y_input ) * $this->x_input);	        	
		        } 
	
	            // Ready
	            $this->resize = TRUE;
	       	}
	
	        // Don't resize
	        else $this->resize = FALSE;
	    }
	
	
	
	    // Set image quality (JPEG only)
	    public function set_quality($quality) {
	        if ( is_int($quality) ) $this->quality = $quality;
	    }
	
	
	
	    // Save image
	    public function save_img($path) {
	
	        // If resizing, create smaller image and preserve transparency on that new image if PNG or GIF
	        if ( $this->resize ) {
	        	
	        	// Create a new image with resized dimensions
	        	$this->img_output = ImageCreateTrueColor($this->x_output, $this->y_output);
	        	
	        	// Preserve Alpha Channel
	        	if ( $this->format == IMAGETYPE_GIF OR $this->format == IMAGETYPE_PNG ) {
	        		imageAlphaBlending($this->img_output, false);	
					imageSaveAlpha($this->img_output,true);
					$transparent = imageColorAllocateAlpha($this->img_output, 255, 255, 255, 127);
					imageFilledRectangle($this->img_output, 0, 0, $this->x_output, $this->y_output, $transparent);
	        	}
	        	
	        	// Finished image object
	            ImageCopyResampled($this->img_output, $this->img_input, 0, 0, 0, 0, $this->x_output, $this->y_output, $this->x_input, $this->y_input);

		        // Save using the correct function
		        switch ( $this->format ) {
		        	case IMAGETYPE_GIF: imageGIF($this->img_output, $path); break;
		        	case IMAGETYPE_JPEG:imageJPEG($this->img_output, $path, $this->quality);break;
		        	case IMAGETYPE_PNG:	imagePNG($this->img_output, $path);break;
		   		}
	        }
	        
	        // Just make a copy
	        else copy($this->img_src, $path);
	    }
	    
	
	
		// Just to get the filepath
		public function get_src() {
			return $this->img_src;
		}
	
	    
	    // A string representing the img element for this file
	    // 	- call set_size() if need to rescale for browser, doesn't effect original file until -save_img() call
	    public function get_html($alt=null, $class=null, $id=null, $path_prefix=null) {
	    	    		    	
	    	// Get the alt tag from the filename, if not given
	    	if ( is_null($alt) OR $alt == '' ) {
				$alt = explode('/', $this->img_src);
				$alt = explode('.', $alt[count($alt)-1]);
				$alt = $alt[0];
			}
			
			$src = !is_null($path_prefix) ? $path_prefix.$this->img_src : $this->img_src;
	    	
	    	// Build the html itself, with optional parts
	    	$html = "<img src='$src' alt='$alt' width='$this->x_output' height='$this->y_output' ";	
			if ( !is_null($class) AND $class != '' ) $html .= "class='$class' ";
			if ( !is_null($id) AND $id != '' ) $html .= "id='$id' ";
			$html .= '/>';
			
			return $html;	    	
	    }
	    
	    
	
	    // Get width
	    public function get_width() {
	        return $this->x_input;
	    }
	
	
	
	    // Get height
	    public function get_height() {
	        return $this->y_input;
	    }
	    
	    
	    
	    // Get the format of image
	    public function get_format() {
	    	return $this->format;	
	    }
	
	
		// For convenience
		public function __toString() {
			return '<img src="'.$this->img_src.'"/>';
		}
	    
	    
		// Destructor for the object
	    public function __destruct() {
	        @ImageDestroy($this->img_input);
	        @ImageDestroy($this->img_output);
	    }
	}
?>