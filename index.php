<style type="text/css">
.css {
	background-image: url(http://cdnlava360.saturn.netdna-cdn.com/wp-content/uploads/2014/01/Classic-Background-Images-For-Wordpress-Blogs-111.jpg);
	position: absolute;
	background-repeat: repeat;
}
</style>
<body class="css"><h2>Compare Your  Images!</h2> (copy the image URLs here.)
        <form method="post" action="">
          <p>
            <input type="text" placeholder="Image 1" name="i1" id="i1" />
            <input type="text" placeholder="Image 2" name="i2" id="i2" />
            <input type="submit" name="submit" value="Test it" />
          </p>
          <p><img name="" src="1.jpg" width="142" height="140" alt=""></a> <img name="" src="2.jpg" width="142" height="140" alt=""></a></p>
          <p>Image Comparison Values (output)</p>
          <p>&nbsp;</p>
        </form>

<p>
  <?php
error_reporting(0);

$_POST['i1'];
$content1 = $_POST['i1'];

//Get the file
$content1 = file_get_contents($content1);


//Store in the filesystem.
chmod("1.jpg", 0644);
$fp = fopen("1.jpg", "w");


fwrite($fp, $content1);
fclose($fp);


//Get the file
$_POST['i2'];
$content2 = $_POST['i2'];

$content2 = file_get_contents($content2);


//Store in the filesystem.
chmod("2.jpg", 0644);
$fp = fopen("2.jpg", "w");
fwrite($fp, $content2);
fclose($fp);


$class = new compareImages;

echo $class->compare('1.jpg','2.jpg');



class compareImages
{
	private function mimeType($i)
	{
		/*returns array with mime type and if its jpg or png. Returns false if it isn't jpg or png*/
		$mime = getimagesize($i);
		$return = array($mime[0],$mime[1]);
      
		switch ($mime['mime'])
		{
			case 'image/jpeg':
				$return[] = 'jpg';
				return $return;
			case 'image/png':
				$return[] = 'png';
				return $return;
			default:
				return false;
		}
    }  
    
	private function createImage($i)
	{
		/*retuns image resource or false if its not jpg or png*/
		$mime = $this->mimeType($i);
      
		if($mime[2] == 'jpg')
		{
			return imagecreatefromjpeg ($i);
		} 
		else if ($mime[2] == 'png') 
		{
			return imagecreatefrompng ($i);
		} 
		else 
		{
			return false; 
		} 
    }
    
	private function resizeImage($i,$source)
	{
		/*resizes the image to a 8x8 squere and returns as image resource*/
		$mime = $this->mimeType($source);
      
		$t = imagecreatetruecolor(8, 8);
		
		$source = $this->createImage($source);
		
		imagecopyresized($t, $source, 0, 0, 0, 0, 8, 8, $mime[0], $mime[1]);
		
		return $t;
	}
    
    private function colorMeanValue($i)
	{
		/*returns the mean value of the colors and the list of all pixel's colors*/
		$colorList = array();
		$colorSum = 0;
		for($a = 0;$a<8;$a++)
		{
		
			for($b = 0;$b<8;$b++)
			{
			
				$rgb = imagecolorat($i, $a, $b);
				$colorList[] = $rgb & 0xFF;
				$colorSum += $rgb & 0xFF;
				
			}
			
		}
		
		return array($colorSum/64,$colorList);
	}
    
    private function bits($colorMean)
	{
		/*returns an array with 1 and zeros. If a color is bigger than the mean value of colors it is 1*/
		$bits = array();
		 
		foreach($colorMean[1] as $color){$bits[]= ($color>=$colorMean[0])?1:0;}

		return $bits;

	}
	
    public function compare($a,$b)
	{
		/*main function. returns the hammering distance of two images' bit value*/
		$i1 = $this->createImage($a);
		$i2 = $this->createImage($b);
		
		if(!$i1 || !$i2){return false;}
		
		$i1 = $this->resizeImage($i1,$a);
		$i2 = $this->resizeImage($i2,$b);
		
		imagefilter($i1, IMG_FILTER_GRAYSCALE);
		imagefilter($i2, IMG_FILTER_GRAYSCALE);
		
		$colorMean1 = $this->colorMeanValue($i1);
		$colorMean2 = $this->colorMeanValue($i2);
		
		$bits1 = $this->bits($colorMean1);
		$bits2 = $this->bits($colorMean2);
		
	
		
		$hammeringDistance =100;
		
		for($a = 0;$a<64;$a++)
		{
		
			if($bits1[$a] != $bits2[$a])
			{
				$hammeringDistance=$hammeringDistance-2;
			}
			
		}
		  
		return $hammeringDistance;
	}
}
  
?>
</p>
<p>----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------</p>
<p>This PHP program compare two images and returns a number representing how similar they are. It is capable to tell if two pictures are similar even if they have different sizes or aspect ratio. If the program return 100, it means the images are same. if it is less than 100, it means there are some difference</p>
<p><strong>Example #1: Different size. Number you get: 96</strong></p>
<p><img src="images/1a.png" width="400" height="225"> <img src="images/1b.png" width="300" height="169"></p>
<p><strong>Example #2: Different size. And aspect ratio. Number you get: 95</strong></p>
<p><img src="images/2a.png" width="400" height="225"> <img src="images/2b.png" width="300" height="100"></p>
<p><strong>Example #3: Different light and contrast. Number you get: 98</strong></p>
<p><img src="images/3a.png" width="400" height="225"> <img src="images/3b.png" width="400" height="225"></p>
<p>&nbsp;</p>
<p><strong>Example #4: Exactly the same image. Number you get: 100</strong></p>
<p><img src="images/4.png" width="400" height="225"> <img src="images/4.png" width="400" height="225"></p>
<p><strong>Example #5: Completely different image. Number you get: 0</strong></p>
<p><img src="images/2a.png" width="400" height="225"> <img src="images/3a.png" width="400" height="225"></p>
<p>&nbsp;</p>
<p>&nbsp;</p>
