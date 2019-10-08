<?php 
	require_once("dirRead.php");
	require_once("fun.php");
	require_once("comm.php");
	date_default_timezone_set("PRC");
	error_reporting(E_ALL^E_NOTICE^E_WARNING);
	$path = 'file';
	$path = $_REQUEST['path']?$_REQUEST['path']:$path;
		# code...
	$size = 0;
	$info = readf($path);
	if (!$info) {
		echo "<script>alert('没有文件');location.href='index.php'</script>";
	}

	$act= $_REQUEST['act'];
	$name= $_REQUEST['name'];
	// $path =$_REQUEST['path'];

	$redirect="index.php?path=$path";

	if ($act=='create') {
		$mes = create($path.'/'.$name);
		showMes($mes,$redirect);
		
	}
	elseif ($act=='createDir') {
		if (mkdir($path.'/'.$name)) {
			showMes("创建成功",$redirect);
		}
	}
	elseif ($act=='lookcontent') {
		 $lookname = $_REQUEST['lookname'];
		 $content = file_get_contents($lookname);
		 if ($content!='') {
		 	echo "<textarea>".$content."</textarea>";
		 }	 
		 else{
		 	showMes("没有内容编辑后，再查看",$redirect);
		 }
	}
	elseif ($act=="editcontent") {
		$editname = $_REQUEST['editname'];
		$editname = $_REQUEST['editname'];
		$content = file_get_contents($editname);
		$str= "<form action='index.php?act=doedit' method='post' class='form-inline'>
				<textarea name='content' cols='200' rows='10'>{$content}</textarea>
				<input type='hidden' name='editname' value='{$editname}'>
				<input type='hidden' name='path' value='{$path}'>
				<input type='submit' name='submit' value='修改''>
				</form>";

		echo $str;
	}
	elseif ($act=="doedit") {
		$content =$_REQUEST['content'];
		$editname= $_REQUEST['editname'];
		if (file_put_contents($editname, $content)) {
			showMes('修改成功',$redirect);
		}
		else{
			showMes('操作失败',$redirect);
		}

	}
	elseif($act=="renameact"){
		$rename = $_REQUEST['rename'];
		preg_match('/\/([^\/]+\.[a-z]+)[^\/]*$/',$rename,$match);
	    echo  "<form action='index.php?act=dorename' method='post' class='form-inline'>
				<textarea name='content' cols='20' rows='1'>{$match[1]}</textarea>
				<input type='hidden' name='rename' value='{$rename}'>
				<input type='hidden' name='path' value='{$path}'>
				<input type='submit' name='submit' value='重命名''>
				</form>";
	}
	elseif ($act=="dorename") {
		$rename = $_REQUEST['rename'];
		$testname = $_REQUEST['content'];
		$content = $path.'/'.$_REQUEST['content'];
		$pattern="/[\/,\*,<>,\?\|]/";
		if (!preg_match($pattern,basename($testname))) {
			if (file_exists($content)) {
				showMes("已存在",$redirect);
			}
			else{
				if (rename($rename,$content)) {
				showMes("命名成功啦",$redirect);
				}
				else{
				showMes("命名失败",$redirect);
				}
			}
		}
		else{
			showMes("非法名字",$redirect);
		}
	}
	elseif ($act=="delete") {
		$filename = $_REQUEST['filename'];
		if (is_file($filename)) {
			if (unlink($filename)) {
			showMes("删除成功",$redirect);
			}
		else{
			showMes("删除失败",$redirect);
		}		
	 }
	 elseif (is_dir($filename)) {
	 	  //可能是权限问题
	 		echo deleteDir($filename);
	 		 // rmdir($filename);
	 }
	}
	elseif ($act=='downfile') {
		$name = $_REQUEST['name'];
		header("content-disposition:attachment;filename=".basename($name));
		header("content-length:".filesize($name));
		readfile($name);
	}elseif ($act=='fuzhi') {
		$dirname = $_REQUEST['fuzhiname'];
		preg_match('/\/([^\/]+\.[a-z]+)[^\/]*$/',$dirname,$match);
	    echo  "<form action='index.php?act=dofuzhi' method='post' class='form-inline'>
	    		复制到：
				<textarea name='content' cols='20' rows='1'>{$match[1]}</textarea>
				<input type='hidden' name='dirname' value='{$dirname}'>
				<input type='hidden' name='path' value='{$path}'>
				<input type='submit' name='submit' value='复制''>
				</form>";	
	}elseif ($act=='dofuzhi') {
		$dstname=$_REQUEST['content'];
		$dirname=$_REQUEST['dirname'];
		$mes = copyfile($dirname,$path.'/'.$dstname.'/'.basename($dirname));
		echo $mes;
	}
	elseif($act=="upload"){
		$fileinfo = $_FILES['files'];
		uploadfile($fileinfo,$path);
	}
	function uploadfile($fileinfo,$path){

		if($fileinfo['error']>0){
			echo "出错";
		}
		else{
			 $name=$path.'/'.time().'_'.$fileinfo['name'];
			 echo $name;
			 move_uploaded_file($fileinfo["tmp_name"],$name);
		}
	}
	function deleteDir($filename){
		$handle = opendir($filename);
		while (($item=readdir($handle))!==false) {
			if ($item!="."&&$item!="..") {
			echo $filename."/".$item;
			 if (is_file($filename."/".$item)) {
			 	unlink($filename."/".$item);
			 	
			 }
			 if (is_dir($filename."/".$item)) {
			 	$fun = __FUNCTION__;
			 	$fun($filename."/".$item);
			 }
			}
			
		}
		rmdir($filename);
		closedir($handle);
		return "删除成功";
	}

	function copyfile($dir,$dst){
		
		if (is_dir($dir)) {
			if (!file_exists($dst)) {
			mkdir($dst,0777,true);
		}
			$handle = opendir($dir);
		while (($item=readdir($handle))!==false) {
			if ($item!="."&&$item!="..") {
			 if (is_file($dir."/".$item)) {
			 	copy($dir."/".$item,$dst."/".$item);
			 }
			 if (is_dir($dir.'/'.$item)) {
			 	$fun = __FUNCTION__;
			 	$fun($dir."/".$item,$dst."/".$item);
			 }
			}
		}
		closedir($handle);
		return "复制成功";
		}
		elseif (is_file($dir)) {
			 copy($dir,$dst);
			 echo "复制成功";
		}
	}
 ?>

 <!DOCTYPE html>
 <html lang="en">
 <head>
 	<meta charset="UTF-8">
 	<title>稻草人商店</title>
 	<link href="http://cdn.static.runoob.com/libs/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <script src="http://cdn.static.runoob.com/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="http://cdn.static.runoob.com/libs/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="format-detection" content="telephone=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <!-- Fonts-->
    <link rel="stylesheet" type="text/css" href="assets/fonts/fontawesome/font-awesome.min.css">
    <!-- Vendors-->
    <link rel="stylesheet" type="text/css" href="assets/vendors/bootstrap/grid.css">
    <link rel="stylesheet" type="text/css" href="assets/vendors/magnific-popup/magnific-popup.min.css">
    <link rel="stylesheet" type="text/css" href="assets/vendors/swiper/swiper.css">
    <link rel="stylesheet" type="text/css" href="assets/vendors/wow/animate.css">
    <!-- App & fonts-->
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Josefin+Sans:400,700|Montserrat:400,700">
    <link rel="stylesheet" type="text/css" id="app-stylesheet" href="assets/css/main.css">


    <script>
    function delfile (name) {
    	console.log(name);
    		if(confirm('你确定要删除'))
    		{
    		console.log(name);
       		 location.href='index.php?act=delete&filename='+name;
     		}
 		  else
   		  {}
    }
    function goback($path){
    	location.href="index.php?path="+$path;
    }
    function gohome(){
    	location.href="index.php";
    }
    $(function(){
    	$("#nav li").click(function (argument) {
    		// console.log($(this).index());
    		$('.formdiv div').hide();
    		$('.formdiv div').eq($(this).index()-1).show();
    	})
    })
    </script>
    <style>
	.main{
		padding: 20px;
	}
	h1{
		text-align: center;
	}
    </style>
 </head>
 <body>
    <div class="page-wrap" id="root">
     <div class="main">

        <!-- header -->
        <header class="header header--fixed">
            <div class="container">
                <div class="header__inner">
                    <div class="header__logo"><a>Android</a></div>
                    <div>
                        <ul class='nav nav-tabs' id="nav">
                            <li onclick="gohome()">
                                <button type="button" class="btn btn-default btn-lg">
                                    <span class="glyphicon glyphicon-home" aria-hidden="true"></span>
                                </button>
                            </li>
                            <li onclick="goback('<?php echo $back;?>')">
                                <button type="button" class="btn btn-default btn-lg" id="back">
                                    <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </header><!-- End / header -->

         <!-- Content-->
        <div class="awe-content">
            <!-- Section -->
            <section class="awe-section" id="id-3">
                <div class="container">
            <?php
                $back=$path=="file"?"file":dirname($path);
            ?>

                <?php
                    if($info['file']){
                        $i=1;
                        foreach(array_reverse($info['file']) as $val) {
                            $p=$path.'/'.$val;
                 ?>

                 <!--   上面是逆向循环，此处是正向循环
                        foreach ($info['file'] as $val) {
                            $p=$path.'/'.$val;
                  -->

                 <div class="col-lg-4 ">

                     <!-- service -->
                     <div class="service" data-wow-duration="1s" data-wow-delay="0.2s" data-wow-offset="20" data-wow-iteration="1">
                         <div class="service__icon"><a href="index.php?act=downfile&name=<?php echo $p; ?>"><span class="glyphicon glyphicon-arrow-down"></div>
                         <p class="service__title"><?php echo str_replace("_"," ", $val); ?></p>
                         <p class="service__text"><?php echo showSize(filesize($p)) ?></p>
                     </div><!-- End / service -->

                 </div>
                 <?php
                        $i++;
                    }
                 }?>

                  <?php
                    if($info['dir']){
                        foreach ($info['dir'] as $val) {
                            $p= $path.'/'.$val;
                 ?>

                 <div class="col-lg-4 ">
                      <!-- service -->
                      <div class="service" data-wow-duration="1s" data-wow-delay="0.2s" data-wow-offset="20" data-wow-iteration="1">
                          <div class="service__icon"><a href="index.php?path=<?php echo $p; ?>"><span class="glyphicon glyphicon-folder-open"></div>
                          <p class="service__title"><?php echo str_replace("_"," ", $val); ?></p>
                          <p class="service__text"><?phpecho showSize(allfile($p));$size=0;?></p>
                      </div><!-- End / service -->

                 </div>
                 <?php
                        $i++;
                    }
                 }?>

                </div>
            </section>
            <!-- End / Section -->

        </div>

        <script type="text/javascript" src="assets/vendors/_jquery/jquery.min.js"></script>
        <script type="text/javascript" src="assets/vendors/imagesloaded/imagesloaded.pkgd.js"></script>
        <script type="text/javascript" src="assets/vendors/isotope-layout/isotope.pkgd.js"></script>
        <script type="text/javascript" src="assets/vendors/jquery-one-page/jquery.nav.min.js"></script>
        <script type="text/javascript" src="assets/vendors/jquery.easing/jquery.easing.min.js"></script>
        <script type="text/javascript" src="assets/vendors/jquery.matchHeight/jquery.matchHeight.min.js"></script>
        <script type="text/javascript" src="assets/vendors/magnific-popup/jquery.magnific-popup.min.js"></script>
        <script type="text/javascript" src="assets/vendors/masonry-layout/masonry.pkgd.js"></script>
        <script type="text/javascript" src="assets/vendors/swiper/swiper.jquery.js"></script>
        <script type="text/javascript" src="assets/vendors/menu/menu.js"></script>
        <script type="text/javascript" src="assets/vendors/jquery.countTo/jquery.countTo.min.js"></script>
        <script type="text/javascript" src="assets/vendors/jquery.waypoints/jquery.waypoints.min.js"></script>
        <script type="text/javascript" src="assets/vendors/tabs/awe-tabs.js"></script>
        <script type="text/javascript" src="assets/vendors/wow/wow.js"></script>
        <script type="text/javascript" src="assets/vendors/jquery.appear/jquery.appear.js"></script>
        <script type="text/javascript" src="assets/vendors/waterpipe/waterpipe.js"></script>
        <!-- App-->
        <script type="text/javascript" src="assets/js/main.js"></script>
</div>
</body>
</html>