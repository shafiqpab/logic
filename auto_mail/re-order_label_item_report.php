<?php
date_default_timezone_set("Asia/Dhaka");
extract($_REQUEST);

$req_url_arr=explode('/',$_SERVER['REQUEST_URI']);
$base_path = $_SERVER['SERVER_NAME'].'/'.$req_url_arr[1];


require_once('../includes/common.php');
// require_once('../mailer/class.phpmailer.php');
require_once('setting/mail_setting.php');


	$comp_lib = return_library_array("select id, company_name from lib_company where status_active=1 and is_deleted=0", "id","company_name");
	//$item_lib = return_library_array("select id, ITEM_DESCRIPTION from LIB_ITEM_DETAILS where  is_deleted=0","id", "ITEM_DESCRIPTION");
	$item_group_lib = return_library_array("select id, ITEM_NAME from lib_item_group where  status_active=1 and is_deleted=0","id", "ITEM_NAME");
	
	
	//print_r($att_file_arr);die;
	
		
		foreach($comp_lib as $company_id=>$company_name){
		
		$sql = "SELECT A.COMPANY_ID, A.ITEM_CATEGORY_ID, A.ITEM_GROUP_ID, A.ITEM_DESCRIPTION, A.CURRENT_STOCK, A.RE_ORDER_LABEL,a.UNIT_OF_MEASURE FROM PRODUCT_DETAILS_MASTER A WHERE A.RE_ORDER_LABEL > 0 and A.CURRENT_STOCK <= A.RE_ORDER_LABEL and A.COMPANY_ID=$company_id";
		//echo $sql;
		
		$result_dtls=sql_select($sql);
		
		
		ob_start();
		?>
        
        <table border="1" rules="all">
        	<tr>
                 <th colspan="7" align="center">
                     <b style="font-size:18px"><?= $company_name;?></b><br />
                     <b> Re-Order Label Item Report</b>
                 </th>
              </tr>
        	<tr bgcolor="#999999">
                 <th>SL</th>
                 <th>ITEM CATEGORY</th>
                 <th>ITEM GROUP</th>
                 <th>ITEM NAME</th>
                 <th>ITEM UOM</th>
                 <th>Re-Order Label</th>
                 <th>Current Stock</th>
              </tr>
              <? 
			  $i=1;
			  foreach($result_dtls as $rows){ 
			  $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
			  ?>
              <tr bgcolor="<? echo $bgcolor; ?>">
               	<td><?= $i;?></td>
               	<td><?= $item_category[$rows[ITEM_CATEGORY_ID]];?></td>
               	<td><?= $item_group_lib[$rows[ITEM_GROUP_ID]];?></td>
               	<td align="right"><?= $rows[ITEM_DESCRIPTION];?></td>
               	<td align="center"><?= $unit_of_measurement[$rows[UNIT_OF_MEASURE]];?></td>
               	<td align="right"><?= number_format($rows[RE_ORDER_LABEL]);?></td>
               	<td align="right"><?= number_format($rows[CURRENT_STOCK],2);?></td>
              </tr>
              <? 
			  $i++;
			  } 
			  ?>
         </table>
         
         <?
	
			$message=ob_get_contents();
			ob_clean();
			
			$to="";	
			$sql = "SELECT a.BRAND_IDS,a.BUYER_IDS,c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id  and a.mail_item=b.MAIL_ITEM_MST and a.mail_item=56 and b.mail_user_setup_id=c.id and a.company_id =".$company_id."  and   A.IS_DELETED=0 and A.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1  and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
			 //echo $sql;die;
			
			
			$mail_sql=sql_select($sql);
			$receverMailArr=array();
			foreach($mail_sql as $row)
			{
				$receverMailArr[$row[csf('email_address')]]=$row[csf('email_address')];
				
			}
		
			$to=implode(',',$receverMailArr);
					
			$subject = "Re-Order Label Item Report";
			
			$header=mailHeader();
			//if($to!="")echo sendMailMailer( $to, $subject, $message, $from_mail,$att_file_arr);
		
			if($_REQUEST['isview']==1){
				echo $message;
			}
			else{
				if($to!="")echo sendMailMailer( $to, $subject, $message, $from_mail,$att_file_arr);
			}
	
		}
	exit();
	
 





	
	

?>