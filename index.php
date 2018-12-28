<?php
ini_set('memory_limit','60M');
set_time_limit(1200);

function nhlog($line){
  error_log("- ".$line."\n", 3, "simplescript_install.log");
}
function percent($num_amount, $num_total) {
  $count1 = $num_amount / $num_total;
  $count2 = $count1 * 100;
  $count = number_format($count2, 0);
  return $count;
}
?>
<head>
  <style>
    * {
      box-sizing: border-box;
      -webkit-box-sizing: border-box;
      -moz-box-sizing: border-box;
    }
    body {
    	font-weight: 300;
    	background: #fff;
    	color: #909090;
    	font-size:17px;
    	line-height: 1.37;
    	font-smooth: always;
    	font-family: Proxima Nova, "proxima-nova", "Helvetica Neue", Helvetica, Arial, sans-serif;
    	-webkit-font-smoothing: antialiased;
    }
    img{
    	max-width:100%;
    }
    a {
      transform: translate3d(0, 0, 0);
      -webkit-transform: translate3d(0, 0, 0);
      -moz-transform: translate3d(0, 0, 0);
      -o-transform: translate3d(0, 0, 0);
      transition: ease-in-out 0.1s;
      -webkit-transition: ease-in-out 0.1s;
      -moz-transition: ease-in-out 0.1s;
      -o-transition: ease-in-out 0.1s;
      color: rgb(34,130,248);
      position: relative;
    }
    a:hover {
      color: rgb(26,155,248);
      text-decoration: none;
    }
    h1,
    h2,
    h3 {
      font-weight: 200;
      color: #494949;
    }
  </style>
</head>
<body>
  <div style="text-align:center;margin:30px;">
    <?php

      if (!isset($_GET["part"])){
        $source = "https://github.com/CodeSimpleScript/core/archive/master.zip";
        $dest = "installme.zip";
        copy($source, $dest);
        copy("index.php", "installer.php");
        unlink('.htaccess');
        echo '<h1>SimpleScript install is now in progress</h1><h2>When we are done we will take you to the setup screen.</h2><BR><BR><h3>This might take a few minutes depending on installer size.</h3>';
        echo '<script>window.setTimeout(function(){ window.location.href = "installer.php?part=0"; }, 1000);</script>';
      }else{
        nhlog("** Install Started, if the install fails send this file to SimpleScript Development team **");
        nhlog("** admin@codesimplescript.com is our email. **");
        $path = 'installme.zip';
        $zip = new ZipArchive;
        if (isset($_GET["part"])){
          $i=$_GET["part"];
        }else{
          $i=0;
        }

        if ($i==0){
          nhlog("Delete junk files.");
          unlink('index.php');
          unlink('index.html');
          unlink('robots.txt');
        }
        $p=10;
        $d=false;
        if ($zip->open($path) === true){
          $files=$zip->numFiles;
          while ($p >= 1){
            $filename = $zip->getNameIndex($i);
            if ($filename!=""){
              $fileinfo = pathinfo($filename);
              $whatIWant = substr($filename, strpos($filename, "/") + 1);
              $move=true;

      				if (preg_match("|www|i", $whatIWant, $var)){ if (file_exists("/www")){ $move=false; } }
              if ($whatIWant==".htaccess"){ copy("zip://".$path."#".$filename, "temp.htaccess"); $move=false; }
              if ($whatIWant=="storage.json"){ if (file_exists("$whatIWant")){ $move=false; }}
              if ($whatIWant=="data.json"){ if (file_exists("$whatIWant")){ $move=false; }}
              //if ($whatIWant=="conf.json"){ if (file_exists("$whatIWant")){ $move=false; }} //we do want to overwrite the conf if we did major changes, if you dont want the file cleared uncomment this line.

      				if ($move==true){
      					$copy=false;
      					if (file_exists($whatIWant)){
      						if (is_dir($whatIWant)){
      							//--Already made folder
      						}else{
      							unlink($whatIWant);
      							$copy=true;
      						}
      					}else{
      						$copy=true;
      					}
      					if ($copy==true){
      						mkdir(dirname($whatIWant), 0777, true);
      						copy("zip://".$path."#".$filename, $whatIWant);
      						nhlog("File copy ".$filename." with ID ".$i.", part ".$p.".");
      					}
      				}else{
      					nhlog("File skip ".$filename." with ID ".$i.", part ".$p.". - We dont install the following files");
      				}
              $p=$p-1;
              $i=$i+1;
            }else{
              $p=0;
              $d=true;
              nhlog("All files installed with final file count of ".$i.".");
            }
          }
          $zip->close();
          if ($d==true){
            echo "<h1>We have just finished installing the system</h1><h2>Now taking you to the site to start the setup.</h2><script>window.setTimeout(function(){ window.location.href = \"/\"; }, 3000);</script>";
            unlink('installme.zip');
            unlink('installer.php');
            rename("temp.htaccess", ".htaccess");
            nhlog("Clear installme files.");
          }else{
            echo "<h1>Install in progress</h1><h2>Leave this page open, we are installing files...</h2><BR><BR><h3>".percent($i,$files)."%</h3></div><script>window.setTimeout(function(){ window.location.href = \"installer.php?part=".$i."\"; }, 700);</script>";
          }
        }else{
          echo "Doh! We couldn't open $file";
          nhlog("Not able to open installer zip file. Contact support.");
        }
      }
    ?>
  </div>
</body>
</html>
