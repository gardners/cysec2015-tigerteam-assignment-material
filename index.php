<html>
<head>
<title>CSEM Checkpoint Submission</title>
<style>
.done {
  background: #6060ff ;
  margin-left: 0;
  margin-top: 0;
  margin: 5px;
  width: 40px;
  height: 40px;
  text-align: center;
  vertical-align: middle;
  line-height: 40px;
  display:inline-block;
}
.avail {
  background: yellow ;
  margin-left: 0;
  margin-top: 0;
  margin: 5px;
  width: 40px;
  height: 40px;
  text-align: center;
  vertical-align: middle;
  line-height: 40px;
  display:inline-block;
}
</style>
</head>
<body>
<?php
  $basedir="/opt/local/checkpoints";

  $username=$_SERVER['PHP_AUTH_USER'];
  $checkpointfile=$basedir."/students/".$username;

  $topicdir=$basedir."/topics";

  // Check for form posting
  if ( array_key_exists("token",$_POST) ) {
     $token=$_POST["token"];
     $tokenfile = $basedir."/tokens/".$token;
     if ( file_exists($tokenfile) ) {
       if (time()-filemtime($tokenfile) > 60 * 60) {
         unlink($tokenfile) or die("Expired token won't go away.");
       }
     }
     if ( !file_exists($tokenfile) ) {
        echo "<h1>Token is not valid.</h1>";  die();
     } else {
         if ( ( $tf = fopen($basedir."/tokens/".$token,"r") ) !== null ) {
         } else {
            echo "<h1>Token is broken.</h1>";  die();
         }
         $demonstrator = fgets($tf);
         fclose($tf);
     }
     if (strlen($demonstrator)<6) die("Demonstrator name too short.");
     $date = new DateTime();
     $timestamp=date_format($date,"Y/m/d.H.i.s");
     foreach ( $_POST as $field => $fieldvalue ) {
       if ( $field != "token" ) {
         // Add checkpoint if demonstrator is in demonstrator file
         // for this topic.
         $topic = strtok($field,":");
         $demofile = $basedir."/topics/".$topic."/demonstrators"; 
         if ( !file_exists($demofile) ) {
            echo "<h1>$demofile does not exist."; die();
         }
         $demook=0;
         $df = fopen($demofile,"r") or die();
         while ( !feof($df) ) {
           $dl = rtrim(fgets($df));
           if ( $demonstrator == $dl ) {
             $demook=1;
           }
         }
         fclose($df);
         if ( $demook != 1 ) {
           echo "<h1>$demonstrator is not authorised for $topic"; die();
         }
         $checkpointline = $field.":".$demonstrator.":$timestamp\n";
         $cf=fopen($checkpointfile,"a") or die("Could not write to checkpoint file ". $checkpointfile);
         fprintf($cf,$checkpointline);
         fclose($cf);
       }
     }
  }

  echo "<h1>Checkpoints for $username</h1>";
  echo "<form method=post>";

  $td=opendir($topicdir);

  $de=readdir($td);
  while ($de !== false) {
    $studentlist=$topicdir."/".$de."/students";
    if (file_exists($studentlist)) {
       $intopic=0;
       $f=fopen($studentlist,"r");
       while (!feof($f)) {
          $l = fgets($f);
          if ( rtrim($l) == $username ) {
            $intopic=1;
          }
       }
       // echo "<br>$username $intopic";
       if ( $intopic == 1 ) {
          $rangefile=$topicdir."/".$de."/range";
          if (file_exists($rangefile)) {
             $rf=fopen($rangefile,"r"); $r = fgets($rf); fclose($rf);
             $maxcheckpoint=intval($r);
             $cpavailarray=array();
             for($i=1;$i<=$maxcheckpoint;$i++) {
                 $cpavailarray[$i] = $i;
             }
             echo "<hr><h2>$de</h2>";
             // Don't list checkpoints that have already been recorded.
             if (file_exists($checkpointfile) ) {
               $cf=fopen($checkpointfile,"r"); 
               while ( !feof($cf) ) {
                 $cl=rtrim(fgets($cf));
                 $cp = strtok($cl,":");
                 if ( $cp == $de ) {
                 $cp = strtok(":"); }
                 if ( array_key_exists($cp,$cpavailarray) ) {
                   unset($cpavailarray[$cp]);
                 }
               }
               fclose($cf);
             }
             for ($i=1;$i<$maxcheckpoint;$i++) {
                if ( !array_key_exists($i,$cpavailarray)) {
                   echo " <div class=\"done\">$i</div> "; }
                else {
                   echo " <div class=\"avail\"><input type=checkbox name=\"$de".":"."$i\" value=\"$de".":"."$i\">$i</div>";
                }
             } 
             echo "<br>";
             echo "";
             echo "<p>";
             
          }
       }
    } else {

    }
    $de=readdir($td);
  }
  closedir($td);

  echo "<hr> Demonstrator token: <input type=password name=token></input>";
  echo "<br><input type=submit></input>";
  echo "</form>";

?>
</body>
</html>
