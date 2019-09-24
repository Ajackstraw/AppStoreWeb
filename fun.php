<?php 
	function showSize($size)
	{
		$arrname = ['byte','Kb','Mb','Gb'];
		$i =0;
		while (floor($size/1024)!=0) {
			  $size=$size/1024;
			  $i++;
		}
		return round($size,2).$arrname[$i];
	}

	function create($name)
	{
		$pattern="/[\/,\*,<>,\?\|]/";
		if (!preg_match($pattern,basename($name))) {
				if (file_exists($name)) {
					return '已存在';
				}
				else {
					if (touch($name)) {
						return 'OK';
					}
				}
		}
		else {
			return '非法文件名';
		}
	}
 ?>