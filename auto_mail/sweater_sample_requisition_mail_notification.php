<?php
date_default_timezone_set("Asia/Dhaka");
//require_once('../mailer/class.phpmailer.php');
include('../includes/common.php');
include('setting/mail_setting.php');
extract($_REQUEST);


// var returnValue=return_global_ajax_value(reponse[2], 'sweater_sample_requisition_mail_notification', '', '../../../auto_mail/sweater_sample_requisition_mail_notification');
//echo load_html_head_contents("Mail Notification", "../", 1, 1,'','','');


$action='sweater_sample_requisition_mail_notification';	
if($action=='sweater_sample_requisition_mail_notification'){
	list($data,$mail)=explode('__',$data);
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$season_arr=return_library_array("select id,season_name from  lib_buyer_season","id","season_name");
	$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
	$team_name_arr=return_library_array("select id,team_name from  lib_sample_production_team where product_category=6 and is_deleted=0","id","team_name");
	
	$sample_name_arr=return_library_array( "select id,sample_name from  lib_sample where  status_active=1 and is_deleted=0",'id','sample_name');
    $dealing_merchant_arr = return_library_array("select id, team_member_name from lib_mkt_team_member_info", 'id', 'team_member_name');
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$store_name_lib=return_library_array( "select id, store_name from lib_store_location", "id", "store_name"  );

    $strtotime = ($_REQUEST['view_date'])?strtotime($_REQUEST['view_date']):time();
    $current_date = change_date_format(date("Y-m-d", $strtotime),'','',1);
	$previous_date = change_date_format(date('Y-m-d', strtotime('-30 day', strtotime($current_date))),'','',1);
 


//foreach($company_arr as $company_id=>$company_name){	
	
	
$sql = "select a.COMPANY_ID,a.REQUISITION_NUMBER,a.REQUISITION_DATE,a.STYLE_REF_NO,a.SEASON,a.DEALING_MARCHANT,a.TEAM_LEADER,a.SAMPLE_TEAM_ID,a.BUYER_NAME ,a.INSERTED_BY,
	LISTAGG(CAST(b.SAMPLE_NAME AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.id) as SAMPLE_NAME,
	LISTAGG(CAST(b.DELV_END_DATE AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.id) as DELV_END_DATE,
	sum(b.SAMPLE_PROD_QTY) as SAMPLE_PROD_QTY,
	 sum(b.WEIGHT) as WEIGHT 
	FROM sample_development_mst a,sample_development_dtls b WHERE a.id=b.sample_mst_id and a.status_active = 1 AND a.is_deleted = 0 and a.id=".$data." and b.entry_form_id=341
	group by a.COMPANY_ID,a.REQUISITION_NUMBER,a.REQUISITION_DATE,a.STYLE_REF_NO,a.SEASON,a.DEALING_MARCHANT,a.TEAM_LEADER,a.SAMPLE_TEAM_ID,a.BUYER_NAME,a.INSERTED_BY";
    //echo $sql;die;
	$sql_result = sql_select($sql);
	$company_id=$sql_result[0][COMPANY_ID];
	$width=1000;
	ob_start();	
	?>
	<div style="width:<? echo $width;?>px; margin-bottom:5px;" align="left">
		<fieldset>
            <table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="caption">
                <tr>
                   <td align="center" width="100%" colspan="11" class="form_caption" ><strong style="font-size:18px"><? echo $company_arr[$company_id]; ?></strong></td>
                </tr> 
                <tr>  
                   <td align="center" width="100%" colspan="11">Sample Requisition</td>
                </tr>  
            </table>
            <table width="<? echo $width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="">
                <thead>
                    <tr>
                        <th width="35">Sl</th>
                        <th width="100">Req. No</th>
                        <th width="60">Req. Date</th>
                        <th width="100">Style Ref</th>
                        <th width="50">Season</th>
                        <th width="100">Sample Name</th>
                        <th width="100">Dealing Merchandiser</th>
                        <th width="100">Buyer Name</th>
                        <th width="60">Delivery Date</th>
                        <th width="100">Team Name</th>
                        <th width="70">Weight</th>
                        <th>Req. Qty. (Pcs)</th>
                    </tr>
                </thead>
	            <tbody>
	                <?
					$i= 1;
					foreach($sql_result as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
						$sample_name_temp_arr=array();
						foreach(explode(',',$row[SAMPLE_NAME]) as $sn){
							$sample_name_temp_arr[$sn]=$sample_name_arr[$sn];
						}
						
						$delivery_date_temp_arr=array();
						foreach(explode(',',$row[DELV_END_DATE]) as $deliveryDate){
							$delivery_date_temp_arr[$deliveryDate]=change_date_format($deliveryDate);
						}
						$team_leader_id=$row['TEAM_LEADER'];
						$dealing_marchant_id=$row['DEALING_MARCHANT'];
						$sample_team_id=$row['SAMPLE_TEAM_ID'];
						$insert_by=$row['INSERTED_BY'];
						?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
	                        <td align="center"><? echo $i;?></td>
	                        <td align="center"><? echo $row[REQUISITION_NUMBER];?></td>
	                        <td><? echo change_date_format($row[REQUISITION_DATE]);?></td>
	                        <td><? echo $row[STYLE_REF_NO];?></td>
	                        <td><? echo $season_arr[$row[SEASON]];?></td>
	                        <td><?  echo implode(', ',$sample_name_temp_arr);?></td>
	                        <td><? echo $dealing_merchant_arr[$row[DEALING_MARCHANT]]; ?></td>
	                        <td><?  echo $buyer_arr[$row[BUYER_NAME]];?></td>
	                        <td><? echo implode(", ",$delivery_date_temp_arr);?></td>
	                        <td align="center"><? echo $team_name_arr[$row[TEAM_LEADER]];?></td>
                            <td align="right"><? echo $row[WEIGHT];?></td>
                            <td align="right"><? echo $row[SAMPLE_PROD_QTY];?></td>
	                    </tr>
						<?
						
	                    $i++;
	                }
	                ?>
	                
	                </tbody>
	            </table>
                <br />
                
                <table cellspacing="0" border="1" class="rpt_table" rules="all" width="800">
                <thead>
                    <tr> 
                        <th colspan="11" align="center"><strong>Required Yarn</strong></th>
                    </tr>
                    <tr>
                        <th width="30" align="center">SL</th>
                        <th width="120" align="center">Garment Item</th>
                        <th width="120" align="center">Gmts Color</th>
                        <th width="170" align="center">Yarn Composition</th>
                        <th width="60" align="center">Count</th>
                        <th width="60" align="center">Guage</th>
                        <th width="60" align="center">Yarn Color</th>
                        <th width="60" align="center">Yarn Req. Qty.</th>
                        <th width="60" align="center">UOM</th>
                        <th width="60" align="center">Lot No</th>
                        <th width="60" align="center">Yarn Store</th>
                        <th width="60" align="center">Ref. No</th>
                        <th align="center">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?
                   $sql_qryf="SELECT a.uom_id,a.gauge, a.id, a.gmts_item_id, a.sample_mst_id, a.sample_name, a.body_part_id, a.fabric_nature_id, a.fabric_description, a.gsm, a.dia, a.SAMPLE_COLOR, a.color_data, a.color_type_id, a.width_dia_id,a.remarks_ra
            FROM sample_development_fabric_acc a WHERE a.sample_mst_id = $data AND a.form_type = 1 and  a.status_active =1 and a.is_deleted=0
        ORDER BY a.id ASC";
       //echo $sql_qryf;die;
                    $resultf=sql_select($sql_qryf);
                    $k=1;
                    $sl=0;
                    foreach($resultf as $row)
                    {
                    $color_data_arr=explode('-----',$row[csf('color_data')]);
                    foreach($color_data_arr as $dataArr){
                        list($txtSL,$txtColor,$hiddenColorId,$txtYarnColor,$txtCount,$txtComposition,$cboType,$cboYarnSource,$txtGreyQnty,$cboUom,$txtLot,$cboStore,$text_comments,$yarn_color_id,$ref_no)=explode('__',$dataArr);
                        $totalFinishReqQty+=$txtGreyQnty;

                     // echo $row[csf('id')]; var_dump(explode('__',$dataArr))."<br>";
                        ?>
                        <tr>
                            <td align="center"><? echo $k;?></td>
                            <td align="left"><? echo $garments_item[$row[csf('gmts_item_id')]];?></td>
                            <td align="left"><? echo $color_library[$row[SAMPLE_COLOR]];?></td>
                            <td align="left"><? echo $txtComposition;?></td>
                            <td align="left"><? echo $txtCount;?></td>
                            <td align="left"><? echo $gauge_arr[$row[csf('gauge')]];?></td>
                            <td align="left"><? echo  $txtYarnColor;?></td>
                            <td align="right"><? echo $txtGreyQnty;?></td>
                            <td align="center"><? echo $unit_of_measurement[$cboUom];?></td>
                            <td align="center"><? echo $txtLot;?></td>
                            <td align="center"><? echo $store_name_lib[$cboStore];?></td>
                            <td align="right"><?=$ref_no;?></td>
                            <td align="right"><p><? echo $text_comments;?></p></td>
                         </tr>
                       <?
                       $k++;
                    }
                   }
                   ?>
                </tbody>
                <tfoot>
                    <td colspan="7" align="right"><b>Total </b></td>
                    <td  align="right"><b><? echo number_format($totalFinishReqQty,2);?> </b></td>
                    <td  align="right"></td>
                    <td  align="right"></td>
                    <td  align="right"></td>
                    <td  align="right"></td>
                 </tfoot>
            </table>    
             
                
                
                
        </fieldset>
    </div>
	<?
	$emailBody=ob_get_contents();
	ob_clean();
	
	
	
	$team_email_arr=return_library_array("select id,email from  lib_sample_production_team where product_category=6 and is_deleted=0 and id=$sample_team_id","id","email");

	$user_arr=return_library_array("select id,USER_EMAIL from  USER_PASSWD where  is_deleted=0 and USER_EMAIL is not null  and id=$insert_by","id","USER_EMAIL");

    $team_member_email_arr = return_library_array("select id, TEAM_MEMBER_EMAIL from lib_mkt_team_member_info where TEAM_ID=$team_leader_id", 'id', 'TEAM_MEMBER_EMAIL');


    $team_leader_email_arr = return_library_array("select TEAM_LEADER_EMAIL from LIB_MARKETING_TEAM where id=$team_leader_id", 'id', 'TEAM_LEADER_EMAIL');
    
	
	$toArr=array();
    if($mail)$toArr[]=$mail;
	$toArr[]=implode(',',$team_member_email_arr);
	$toArr[]=implode(',',$team_leader_email_arr);
	if($team_email_arr[$sample_team_id])$toArr[]=$team_email_arr[$sample_team_id];
	if($user_arr[$insert_by])$toArr[]=$user_arr[$insert_by];
	$to=implode(',',$toArr);


	$subject="Sample Requisition";
	//if($to!=""){echo sendMailMailer( $to, $subject, $emailBody );}else{ echo 'Sorry. Email not found';}
	//echo $emailBody;
    if($_REQUEST['isview']==1){
        echo $to.$emailBody;
    }
    else{
        if($to!=""){echo sendMailMailer( $to, $subject, $emailBody,'' );}else{ echo 'Sorry. Email not found';}
    }

//}
	
exit();	

}

?>
