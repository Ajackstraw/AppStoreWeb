<?php 
    function readf($path)
    {
    	$handle = opendir($path);
    	$arr = array();
		while (($item=readdir($handle))!==false) {
			
		if ($item!="."&&$item!="..") {
			if (is_file($path.'/'.$item)) {
			$arr['file'][]=$item;
			}
			if (is_dir($path.'/'.$item)) {
			$arr['dir'][]=$item;
			}			
		}		
      }
      closedir($handle);
		return $arr;
	}

	function allfile($file)
	{	
		global $size;
		$arr = readf($file);
		if ($arr['file']!='') {
			foreach ($arr['file'] as $value) {
			 $size+=filesize($file.'/'.$value);
			}
		}
		if ($arr['dir']!="") {
			foreach ($arr['dir'] as $val) {
				 allfile($file.'/'.$val);
			}
		}
		return $size;
	}
 ?>