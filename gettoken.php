<?
   $basedir="/opt/local/checkpoints";
   $username=$_SERVER['PHP_AUTH_USER'];
   $r = rand(100000,999999);
   $tokenfile =  $basedir."/tokens/".$r;
   if ( file_exists($tokenfile) ) {
       if (time()-filemtime($tokenfile) > 60 * 60) {
           unlink($tokenfile);
       }
   }
   if ( file_exists( $tokenfile) ) die("Could not allocate token. Try again.");
   $f=fopen($tokenfile,"w") or die("Could not create token file ".$tokenfile);
   fprintf($f,"$username");
   fclose($f);
   echo "<h1>Your token is...</h1>$r";

?>
