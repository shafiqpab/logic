<?php
date_default_timezone_set("Asia/Dhaka");
// require_once('../mailer/class.phpmailer.php');
require_once('../includes/common.php');
require_once('setting/mail_setting.php');
extract($_REQUEST);
// var returnValue=return_global_ajax_value(reponse[2], 'sweater_sample_requisition_auto_mail', '', '../../../auto_mail/sweater_sample_requisition_mail_notification');
// echo load_html_head_contents("Mail Notification", "../", 1, 1,'','','');





	// ob_start();	
	?>
	<div id="mstDiv" style="width:1200px; margin: auto;" align="center" valign="middle">


        
    <table cellspacing="0" border="1" class="rpt_table" rules="all" width="100%">
        <thead>
         
            <tr>
                <th width="60" rowspan="2">Reg No</th>
                <th width="120" rowspan="2">Sample Name</th>
                <th width="55" rowspan="2">Buyer</th>
                <th width="70" rowspan="2">Brand</th>
                <th width="70" rowspan="2">Season</th>
                <th width="40" rowspan="2">Season Year</th>
                <th width="45" rowspan="2">Master/Style Ref.</th>
                <th width="70" rowspan="2">Confirm Del Date</th>
                <th width="70" rowspan="2">Dealing Merchandiser</th>
                <th rowspan="2">Sample Team</th>
                <th width="300"colspan="3"> Sample Delivery Date </th>
                <th rowspan="2"> Remarks  </th>
             </tr>
             <tr>
                    <td>Plan Date</td>
                    <td>Actual Date      </td>
                    <td>Delay Days </td>
           
            </tr>
        </thead>
        <tbody>

           
                 <tr>
                    <td align="center">TISL-20-00046</td>
                    <td>Critical Fit</td>
                    <td>GAP</td>
                    <td>GAP</td>
                    <td>FALL</td>
                    <td>2021</td>
                    <td>568048</td>
                    <td>12/21/2020</td>
                    <td>asaduzzam</td>
                    <td>team farid</td>
                    <td>12/01/2021 </td>
                    <td> </td>
                    <td> </td>
                    <td>WOEATING FOR KNITTING</td>
                </tr>
        </tbody>
     
   </table>
    <br> 
    <!-- <P>All Buyer Head</p> 
    <P>Project Head</p> 
    <P>Dealing Merchandising</p>  -->



 
     
    </div>
	<?

	// $emailBody=ob_get_contents();
	// ob_clean();
	
	
	// $filename=$company_name.'_'.$update_id.".doc";
	// $create_new_doc = fopen($filename, 'w');
	// $is_created = fwrite($create_new_doc,$emailBody);
	// $att_file_arr[]=$filename;

?>
