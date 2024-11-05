<?php
error_reporting(0);
//$file = 'mail_config.txt';
//$active_cunnection = file_get_contents($file);
$file = 'config.php';
include($file);
list($user,$pass,$sender,$host,$port,$secure_port,$to,$is_smtp,$smtp_debug,$smtp_auth)=explode('_split_',$active_cunnection);
//file_put_contents($file, $active_cunnection);
?>

<table cellpadding="0" style="height:95vh;" width="100%">
    <tr>
        <td valign="top">
            <form method="post" action="mail_config.php">
            <table width="500">
                <tr>
                    <td width="130" align="right">IsSMTP</td><td>:</td>
                    <td>
                        <input type="radio" value="0" name="is_smtp" <?=($is_smtp==0)?"checked='checked'":""; ?>  />Off
                        <input type="radio" value="1" name="is_smtp" <?=($is_smtp==1)?"checked='checked'":""; ?> />On
                    </td>
                </tr>
                <tr>
                    <td width="130" align="right">SMTPDebug</td><td>:</td>
                    <td>
                        <input type="radio" value="0" name="smtp_debug" <?=($smtp_debug==0)?"checked='checked'":""; ?> />False
                        <input type="radio" value="1" name="smtp_debug" <?=($smtp_debug==1)?"checked='checked'":""; ?> />True
                    </td>
                </tr>
                <tr>
                    <td align="right">SMTPAuth</td><td>:</td>
                    <td>
                        <input type="radio" value="0" name="smtp_auth" <?=($smtp_auth==0)?"checked='checked'":""; ?> />False
                        <input type="radio" value="1" name="smtp_auth" <?=($smtp_auth==1)?"checked='checked'":""; ?> />True
                    </td>
                </tr>
                <tr>
                    <td align="right">IsHTML</td><td>:</td>
                    <td>true</td>
                </tr>
                <tr>
                    <td align="right">User</td><td>:</td>
                    <td><input type="text" name="user_id" value="<?=$user;?>" required="required" style="width:100%;" /></td>
                </tr>
                <tr>
                    <td align="right">User Pass</td><td>:</td>
                    <td><input type="text" name="user_password" value="<?=$pass;?>" required="required" style="width:100%;" /></td>
                </tr>
                <tr>
                    <td title="Sender Mail: youremail@gmail.com" align="right">Sender Mail</td><td>:</td>
                    <td><input type="text" name="sender_mail" value="<?=$sender;?>" style="width:100%;" required="required" /></td>
                </tr>
                <tr>
                    <td align="right">Test Receiver Mail</td><td>:</td>
                    <td><input type="text" name="resever_mail" value="<?=$to;?>" style="width:100%;" /></td>
                </tr>
                <tr>
                    <td title="Gmail Default Host: smtp.gmail.com" align="right">Host</td><td>:</td>
                    <td><input type="text" name="host" value="<?=$host;?>" required="required" style="width:100%;" /></td>
                </tr>
                <tr>
                    <td title="Port: For TLS: 587; For SSL: 465" align="right">Port</td><td>:</td>
                    <td><input type="text" name="port" value="<?=$port;?>" required="required" style="width:100%;"/></td>
                </tr>
                <tr>
                    <td title="Security Type: TLS or SSL" align="right">SMTPSecure</td><td>:</td>
                    <td><input type="text" name="smtp_secure_port" value="<?=$secure_port;?>" style="width:100%;" /></td>
                </tr>
                <tr>
                    <td colspan="3" align="right">
                        <input type="submit" name="send" value="Send Test Mail" style="width:120px; cursor:pointer;" />
                        <input type="submit" name="update" value="Update Settings" style="width:120px; cursor:pointer;" />
                    </td>
                </tr>
            </table>
            </form>
            <hr>
        </td>
        <td valign="top" align="center">
            ------------Quick Guide----------<br />
            Gmail SMTP settings and Gmail setup - a quick guide<br />
            The server address: smtp.gmail.com.<br />
            Username: youremail@gmail.com.<br />
            Security Type: TLS or SSL.<br />
            Port: For TLS: 587; For SSL: 465.<br />
            Server Address: either pop.gmail.com or imap.gmail.com.<br />
            Username: youremail@gmail.com.<br />
            Port: For POP3: 995; for IMAP: 993.<br />
        </td>
    </tr>
    
    <tr>
        <td colspan="2">
            <?
            extract($_REQUEST);
            if($user_id!='' && isset($_POST['update'])){
                $dataStr=trim($user_id).'_split_'.trim($user_password).'_split_'.trim($sender_mail).'_split_'.trim($host).'_split_'.trim($port).'_split_'.trim($smtp_secure_port).'_split_'.trim($resever_mail).'_split_'.trim($is_smtp).'_split_'.trim($smtp_debug).'_split_'.trim($smtp_auth);
                //file_put_contents($file, $dataStr);
                file_put_contents($file, "<?php $"."active_cunnection='".$dataStr."'; ?>");
                unset($_post);
                header("location:mail_config.php");
            }

            if($user_id!='' && isset($_POST['send'])){
                list($user,$pass,$sender,$host,$port,$secure_port,$to,$is_smtp)=explode('_split_',$active_cunnection);
                date_default_timezone_set("Asia/Dhaka");
    
                include('../../includes/common.php');
                include('mail_setting.php');
                
                $url =  "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
                $escaped_url = htmlspecialchars( $url, ENT_QUOTES, 'UTF-8' );
                $subject = "Test Mail Setting Subject";
                $message="Hi, Your mail setting is Ok. Mail Send From ".$escaped_url;
                $header=mailHeader();
                echo sendMailMailer( $to, $subject, $message, $from_mail);
                unset($_post);
            }
            
            ?>
        </td>
    </tr>
</table>

<table width="100%" bgcolor="#CCC" cellpadding="2" style="border-bottom:3px solid green;border-top: 1px dashed #666;">
    <tr>
        <td style="font-size: 12px;" align="left"><a href="add_auto_mail.php">GO TO ADD NEW MAIL &#187; </a></td>
        <td style="font-size: 12px;"><?= 'Current PHP Version: ' . phpversion();?></td>
        <td style="font-size: 10px;" align="right"><?= date('Y'); ?> © <a href="www.logicsoftbd.com/" target="_blank">Logic Software Limited</a> - Copyright All Rights Reserved</td>
    </tr>
</table>
 

<style>
    hr{
        border: 0;
        height: 1px;
        background-image: linear-gradient(to right,rgba(0,0,0,0),rgba(0,0,0,.75),rgba(0,0,0,0));
    }
    body{margin:0;}
</style>








