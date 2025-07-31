<?php
$sitename="Chitkar";
$sitefooter="(c) Chitkar Development Team 2014";

$options['didnotsubscribe']="I don't want to receive CPSU newsletters";
$options['offended']="I was offended by something in the newsletter";
$options['toomany']="I receive too many newsletters from you";
$options['notrelevant']="The newsletters are not relevant to me";
$options['rationalising']="I'm just trying to manage my inbox";

$rid=filter_input(INPUT_GET, 'rid', FILTER_SANITIZE_STRING);
$email=filter_input(INPUT_GET, 'email', FILTER_SANITIZE_EMAIL);

?>
<html>
<head>
    <title>Unsubscribe from CPSU Newsletters</title>

</head>
<body style='background-color: #ff9339; text-align: center; font-family: verdana, arial'>
<div style='width: 600px; background-color: #FFAA64; margin: auto; margin-top: 150px; border-radius: 10px; border: 1px solid #111; text-align: keft'>
   <h1 style='background-color: #FFAA64'>CPSU Victoria Unsubscribe</h1>
<?php
    if(!isset($_GET['unsubscribe'])) {
?>
   <h2>Was it something we said?</h2>
   <div style='background-color: white; padding: 5px; font-size: 10pt'>
       <p>We see that you want to unsubscribe from CPSU newsletters. Before you go, would you mind leaving us a little feedback?</p>
       <form>
         <table style='width: 80%; margin: auto; font-size: 10pt'>
         <?php
             foreach($options as $option=>$text) {
                 echo "<tr><td><input type='checkbox' name='$option' id='$option'></td><td><label for='$option'>$text</label></td></tr>\n";
             }
         ?>
           <tr><td><input type='checkbox' name='other' id='other'></td><td><label for='other'>Another reason</label>&nbsp;<input type='text' name='othertext' size='40'></td></tr>
           <tr><td colspan='2'>&nbsp;</td></tr>
            
            <tr><td colspan='2'><label for='email'>Unsubscribe This Email Address:</label><br /><input type='text' size='60' name='email' value='<?php echo $email ?>'/></td></tr>
         </table>
         <p><input type='submit' value='Unsubscribe Now' /></p>
         <input type='hidden' name='unsubscribe' value='yes' />
       </form>
       <i>Note: You'll still receive emails from CPSU relating to your membership, such as membership renewal reminders, but you will not receive newsletters.</i>
   </div>

<?php
    } elseif ($_GET['unsubscribe']=="yes" && !empty($_GET['email'])) {
        /**
        * This file add recipientId and email address to unsubscribe.ctk
        * 
        * Unsubscribe.ctk is downloaded periodically by the central chitkar server
        * and processed internally for unsubscription management
        */
        $path = dirname(__FILE__);
        $datapath = $path."/unsubscribe.ctk";
        //$reasons="";
        $addreasons=array();
        foreach($options as $option=>$text) {
            if(isset($_GET{$option})) {$addreasons[]=$option;}
        }

        if(isset($_GET['other'])) {$addreasons[]="other".str_replace(";", " ", str_replace(":", " ",$_GET['othertext']));}
        $reasons=implode("|", $addreasons);
        if($email) {
            $handle=fopen($datapath, "a");
            $string=date("U").":".$rid.":".$email.":".$reasons.";";
            fwrite($handle, $string);
            fclose($handle);         
        }
        
        ?>
       <div style='background-color: white; padding: 5px; font-size: 10pt'>
           <p>Your unsubscribe request has been sent.</p>
           <p>Unsubscribe requests are usually processed within 24 hours.</p>
       </div>        
        <?php
    } elseif (empty($_GET['email'])) {
        ?>
       <div style='background-color: white; padding: 5px; font-size: 10pt'>
           <p>You did not provide an email address, so we cannot unsubscribe you.</p>
           <p>Please try again, and remember to include an email address to unsubscribe.</p>
           <p><a href='unsubscribe.php'>Try again</a></p>
       </div>        
        <?php
    }
    
?>
    <br />&nbsp;
    <span style='font-size: 7pt'><?php echo $sitefooter ?></span>
    <br />&nbsp;
</div>
</body>
</html>