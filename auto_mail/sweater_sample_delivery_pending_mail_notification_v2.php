<?php
date_default_timezone_set("Asia/Dhaka");
require_once('../mailer/class.phpmailer.php');
require_once('../includes/common.php');
require_once('setting/mail_setting.php');
extract($_REQUEST);


	$strtotime = ($_REQUEST['view_date'])?strtotime($_REQUEST['view_date']):time();
	$current_date = change_date_format(date("Y-m-d H:i:s", $strtotime),'','',1);
	$prev_date = change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', time())),'','',1); 

	  
	$companyLibArr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$brandArr=return_library_array( "select id, brand_name from lib_buyer_brand", "id", "brand_name");
	$buyerArr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$seasonArr=return_library_array( "select id,season_name from lib_buyer_season  where status_active =1 and is_deleted=0 ", "id", "season_name"  );
	$teamArr=return_library_array( "select id, team_name from lib_sample_production_team where  product_category=6 and status_active=1 and is_deleted=0", "id", "team_name"  );
    $dealingMerchantArr = return_library_array("select id, TEAM_MEMBER_NAME from lib_mkt_team_member_info", 'id', 'TEAM_MEMBER_NAME');
	$sampleArr=return_library_array( "select id, sample_name from lib_sample", "id", "sample_name");	
		
		
		$sql="SELECT a.ID,a.COMPANY_ID,a.REQUISITION_NUMBER,a.BUYER_NAME, a.BRAND_ID , a.season_buyer_wise as SEASON,a.SEASON_YEAR,a.STYLE_REF_NO,a.DEALING_MARCHANT,a.TEAM_LEADER,a.REQUISITION_DATE,a.REMARKS,b.SAMPLE_NAME,b.DELV_END_DATE
		
	 from sample_development_mst a,sample_development_dtls b where a.id=b.sample_mst_id and  a.requisition_number in('SSL-21-00001','SSL-21-00002','SSL-21-00003','OG-20-00004') and  a.is_deleted=0  and a.status_active=1 and  b.is_deleted=0  and b.status_active=1"; //and a.entry_form_id=459
		
		$dataArray=sql_select($sql);
		foreach($dataArray as $row){
			$deliveryIdArr[$row[ID]]=$row[ID];
			$dataArr[$row[COMPANY_ID]][]=$row;
		}
	
	$sampleExFacArr=return_library_array( "select SAMPLE_DEVELOPMENT_ID, SAMPLE_DEVELOPMENT_ID from SAMPLE_EX_FACTORY_DTLS where STATUS_ACTIVE=1 and IS_DELETED=0 ".where_con_using_array($deliveryIdArr,0,'SAMPLE_DEVELOPMENT_ID')."", "SAMPLE_DEVELOPMENT_ID", "SAMPLE_DEVELOPMENT_ID");
		
	$mail_sql = "SELECT A.COMPANY_ID,A.BUYER_IDS,A.BRAND_IDS,C.EMAIL_ADDRESS FROM mail_group_mst a, mail_group_child b, user_mail_address c where a.mail_item=29 and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_LOCKED=0 and b.mail_group_mst_id=a.id and c.IS_DELETED=0 and c.STATUS_ACTIVE=1  and b.mail_user_setup_id=c.id";
	$mail_sql_result=sql_select($mail_sql);
	foreach($mail_sql_result as $row)
	{
		$companyArr[$row[COMPANY_ID]]=$row[COMPANY_ID];
		$userWiseBuyerArr2[$row[COMPANY_ID]][$row[EMAIL_ADDRESS]]=explode(',',$row[BUYER_IDS]);
		$userWiseBrandArr2[$row[COMPANY_ID]][$row[EMAIL_ADDRESS]]=explode(',',$row[BRAND_IDS]);
		
		
		foreach(explode(',',$row[BUYER_IDS]) as $buyer_id){
			$userWiseBuyerArr[$row[COMPANY_ID]][$buyer_id]=$row[EMAIL_ADDRESS];
		}
		
		foreach(explode(',',$row[BRAND_IDS]) as $brand_id){
			$userWiseBrandArr[$row[COMPANY_ID]][$brand_id]=$row[EMAIL_ADDRESS];
		}
		
	}
	
	

	
	
	
	$htmlBodyArr	=array();

	foreach($companyArr as $company_id){
	$tempReceiverUser=array();
	ob_start();
	?>
	<div id="mstDiv">

    <table cellspacing="0" cellpadding="5" border="1" rules="all">
     <tr>
     	<td colspan="15" align="center"><strong style="font-size: 24px;"><? echo $companyLibArr[$company_id]; ?></strong><br /><b>Date:<?= change_date_format($prev_date);?></td>
     </tr>
     <tr style="background-color:#999999">
        <th rowspan="2">SL</th>
        <th rowspan="2">Req. No</th>
        <th rowspan="2">Sample Name</th>
        <th rowspan="2">Buyer</th>
        <th rowspan="2">Brand</th>
        <th rowspan="2">Season</th>
        <th rowspan="2">Season Year</th>
        <th rowspan="2">Master / Style Ref.</th>
        <th rowspan="2">Confirm Del. Date</th>
        <th rowspan="2">Dealing Merchandiser</th>
        <th rowspan="2">Sample Team</th>
        <th colspan="3">Sample Delivery Date</th>
        <th rowspan="2">REMARKS</th>
     </tr>
        
    <tr style="background-color:#999999">
        <td align="center">Plan Date</td>
        <td align="center">Actual Date</td>
        <td align="center">Delay Days</td>
    </tr>
        <?
  	$htmlHeader=ob_get_contents();
	ob_clean();
	
		$i=0;
		foreach($dataArr[$company_id] as $rows){
			if($sampleExFacArr[$rows[ID]]!=''){continue;}
		$i++;
		$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
		ob_start();	
		?>
        <tr bgcolor="<? echo $bgcolor; ?>">
        	<td><?= $i;?></td>
        	<td><?= $rows[REQUISITION_NUMBER];?></td>
        	<td><?= $sampleArr[$rows[SAMPLE_NAME]];?></td>
        	<td><?= $buyerArr[$rows[BUYER_NAME]];?></td>
        	<td><?= $brandArr[$rows[BRAND_ID]];?></td>
        	<td><?= $seasonArr[$rows[SEASON]];?></td>
        	<td><?= $rows[SEASON_YEAR];?></td>
        	<td><?= $rows[STYLE_REF_NO];?></td>
        	<td><?= change_date_format($rows[REQUISITION_DATE]);?></td>
        	<td><?= $dealingMerchantArr[$rows[DEALING_MARCHANT]];?></td>
        	<td><?= $teamArr[$rows[TEAM_LEADER]];?></td>
        	<td><?= change_date_format($rows[DELV_END_DATE]);?></td>
        	<td><?= $currDate=date('d-m-Y',time());?></td>
        	<td align="center"><?= datediff("d",$rows[DELV_END_DATE], $currDate);?></td>
        	<td><?= $rows[REMARKS];?></td>
        </tr>
        
        <?
			
			foreach($userWiseBuyerArr2[$company_id] as $user_mail=>$buyerRows){
				
				if(count($buyerRows)){
					$user_mail=$userWiseBuyerArr[$company_id][$rows[BUYER_NAME]];
				}
				if(count($userWiseBrandArr2[$company_id][$user_mail])){
					$user_mail=$userWiseBrandArr[$company_id][$rows[BRAND_ID]];
				}
				if($user_mail!=''){
					$htmlBodyArr[$user_mail].=ob_get_contents();
					$tempReceiverUser[$user_mail]=$user_mail;
				}
				
			
			}
			 
			 ob_clean();

			
			
		} 
		
	ob_start();	
		?>
    </table>
    </div>
   
      
  <?
  	$htmlFooter=ob_get_contents();
	ob_clean();
		
		
		foreach($tempReceiverUser as $umail){
			echo $mailBody=$htmlHeader.$htmlBodyArr[$umail].$htmlFooter;
		}
	
	
	}//end company loof;
	
	
  







?>
