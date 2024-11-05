<?php
date_default_timezone_set("Asia/Dhaka");

require_once('../../includes/common.php');
//require_once('../../mailer/class.phpmailer.php');
require_once('../setting/mail_setting.php');


 
extract($_REQUEST);
list($update_id,$sys_id,$email,$mail_body)=explode('__',$data);

//http://202.22.203.82/erp/auto_mail/woven/buyer_inquiry_woven_auto_mail.php?sys_id=OG-QIN-21-00023
 
	$buyer_arr = return_library_array("select id,buyer_name from  lib_buyer ","id","buyer_name");
	$color_arr = return_library_array("select id,color_name from  lib_color ","id","color_name");
	$company_library = return_library_array("select id,company_name from lib_company","id","company_name");
	$season_name_library = return_library_array("select id,season_name from lib_buyer_season","id","season_name");
	$brandArr = return_library_array("select id,brand_name from  lib_buyer_brand ","id","brand_name");
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$team_leader_arr=return_library_array( "select id, team_leader_name from lib_marketing_team where project_type=2 and status_active =1 and is_deleted=0 order by team_leader_name",'id','team_leader_name');
	
	
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info  where status_active=1 and is_deleted=0","id","team_member_name");
	$marchentrMaillArr = return_library_array("select id,TEAM_MEMBER_EMAIL from lib_mkt_team_member_info  where status_active=1 and is_deleted=0","id","TEAM_MEMBER_EMAIL");
	
 
 
 	
	$sql = "select ID,system_number_prefix, system_number_prefix_num, system_number, company_id, buyer_id, season_buyer_wise, inquery_date, style_refernce, buyer_request, remarks, dealing_marchant, gmts_item, est_ship_date, fabrication, offer_qty, color, req_quotation_date, target_sam_sub_date, actual_req_quot_date, actual_sam_send_date, department_name, buyer_target_price, buyer_submit_price, insert_by, insert_date, status_active, is_deleted, season_year, brand_id,style_description,con_rec_target_date,concern_marchant,TEAM_LEADER,PRIORITY,COPY_SYSTEM_NUMBER,INSERT_BY from wo_quotation_inquery where id=".$sys_id." and status_active=1  order by id";
	//echo $sql;die;
	
	$sql_result=sql_select($sql);
	$mstRow=$sql_result[0];
	$INSERTED_BY=$mstRow[csf('INSERT_BY')];
	$ID=$mstRow['ID'];
	$company_name=$mstRow[csf('company_id')];
	 //echo $sql;die;
	
	
	$composition_arr=array();
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
	$sql_q="select mst_id, copmposition_id, percent, count_id, type_id from lib_yarn_count_determina_dtls where is_deleted=0 order by id";
						
	$data_array=sql_select($sql_q);
	if (count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('mst_id')],$composition_arr))
			{
				$composition_arr[$row[csf('mst_id')]]=$composition_arr[$row[csf('mst_id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
			}
			else
			{
				$composition_arr[$row[csf('mst_id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]];
			}
		}
	}
	
	
	//print_r($composition_arr);die;

	
	
	
	$sqlRd="select ID, RD_NO, CONSTRUCTION, GSM_WEIGHT, WEIGHT_TYPE, DESIGN, FABRIC_REF, RD_NO, COLOR_RANGE_ID from lib_yarn_count_determina_mst where entry_form=426 and status_active=1 and is_deleted=0 and id in (".$mstRow['FABRICATION'].") order by id";
	$sqlRdData=sql_select($sqlRd); 


	$imgSql="select FILE_TYPE,IMAGE_LOCATION,REAL_FILE_NAME, MASTER_TBLE_ID, FORM_NAME from common_photo_library where form_name in('quotation_inquery','quotation_inquery_front_image','quotation_inquery_back_image') and is_deleted=0  ".where_con_using_array(array($mstRow[ID]),1,'MASTER_TBLE_ID')."";//'quotation_entry',
	$imgSqlResult=sql_select($imgSql);
	foreach($imgSqlResult as $rows){
		$att_file_arr[]='../../'.$rows['IMAGE_LOCATION'].'**'.$rows['REAL_FILE_NAME'];
	}
	//echo $imgSql;
		


//-----------------------	
 $sql_team_mail="
SELECT c.CAD_USER_NAME,d.USER_EMAIL, b.TEAM_LEADER_EMAIL  FROM WO_QUOTATION_INQUERY a,  LIB_MARKETING_TEAM b,   LIB_MKT_TEAM_MEMBER_INFO c,  USER_PASSWD d WHERE a.INSERT_BY = c.USER_TAG_ID  AND b.id = c.TEAM_ID   AND c.USER_TAG_ID = d.id  AND a.id = $ID and c.STATUS_ACTIVE=1 and c.IS_DELETED=0";
//echo $sql_team_mail;die;
$sql_team_mail_result=sql_select($sql_team_mail);
$toArr=array();
foreach($sql_team_mail_result as $rows){
	if($rows['USER_EMAIL'])$toArr[$rows['USER_EMAIL']]=$rows['USER_EMAIL'];
	if($rows['TEAM_LEADER_EMAIL'])$toArr[$rows['TEAM_LEADER_EMAIL']]=$rows['TEAM_LEADER_EMAIL'];
	$CAD_USER_NAME=$rows['CAD_USER_NAME'];
}

if($CAD_USER_NAME!=''){$whereCon=" or d.id in(".$CAD_USER_NAME.")";}



$sql_team_mail="SELECT d.USER_EMAIL from USER_PASSWD d WHERE d.id = $INSERTED_BY $whereCon";
$sql_team_mail_result=sql_select($sql_team_mail);
foreach($sql_team_mail_result as $rows){
	if($rows['USER_EMAIL'])$toArr[$rows['USER_EMAIL']]=$rows['USER_EMAIL'];
}

$toArr[]=$marchentrMaillArr[$mstRow[csf('dealing_marchant')]];
if($email){$toArr[]=$email;}

ob_start();	

		?>
		
        
 	<div style="width:850px; font-size:20px; font-weight:bold" align="center">
        <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black">
            <tr>
                <td width="80">
                    <img  src='../../<? echo $imge_arr[$company_name]; ?>' height='100%' width='100%' />
                </td>
                <td width="450">
                    <table width="100%" cellpadding="0" cellspacing="0" >
                        <tr>
                            <td align="center" style="font-size:20px;"><?php echo $company_library[$company_name]; ?></td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px">
                            <?
                            $nameArray=sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$company_name");
                            foreach ($nameArray as $result)
                            {
                                ?>
                                <? echo $result[csf('plot_no')]; ?>
                                <? echo $result[csf('level_no')]?>
                                <? echo $result[csf('road_no')]; ?>
                                <? echo $result[csf('block_no')];?>
                                <? echo $result[csf('city')];?>
                                <? echo $result[csf('zip_code')]; ?>
                                <? echo $result[csf('province')]; ?>
                                <? echo $country_arr[$result[csf('country_id')]]; ?><br>
                                <? echo $result[csf('email')];?>
                                <? echo $result[csf('website')];
                            }
                            foreach ($sql_result as $row)
                            {
                                $system_number= $row[csf('system_number')];
                                $buyer_id = $row[csf('buyer_id')];
                                $season_buyer_wise= $row[csf('season_buyer_wise')];
                                $inquery_date= $row[csf('inquery_date')];
                                $style_refernce= $row[csf('style_refernce')];
                                $dealing_marchant= $row[csf('dealing_marchant')]; 
								$CONCERN_MARCHANT= $row[csf('CONCERN_MARCHANT')];
								$TEAM_LEADER= $row[csf('TEAM_LEADER')];
								$PRIORITY= $row[csf('PRIORITY')];
								$COPY_SYSTEM_NUMBER= $row[csf('COPY_SYSTEM_NUMBER')];
                                $concern_marchant= $row[csf('concern_marchant')];
                                $gmts_item= $row[csf('gmts_item')];
                                $est_ship_date= $row[csf('est_ship_date')];
                                $fabrication= $row[csf('fabrication')];
                                $offer_qty= $row[csf('offer_qty')];
                                $color= $row[csf('color')];
                                $req_quotation_date= $row[csf('req_quotation_date')];
                                $target_sam_sub_date= $row[csf('target_sam_sub_date')];
								$actual_sam_send_date= $row[csf('actual_sam_send_date')];
                                $actual_req_quot_date= $row[csf('actual_req_quot_date')];
                                $department_name= $row[csf('department_name')];
                                $buyer_target_price= $row[csf('buyer_target_price')];
                                $remarks= $row[csf('remarks')];$buyer_request= $row[csf('buyer_request')];
                                $buyer_submit_price= $row[csf('buyer_submit_price')];
								$style_description= $row[csf('style_description')];
								$season_year= $row[csf('season_year')];
								$brand_id= $row[csf('brand_id')];
								$rec_target_date=change_date_format($row[csf("con_rec_target_date")],"dd-mm-yyyy","-");
                            }
							
							$composition_arr=array();
							$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
							$sql_q="select mst_id, copmposition_id, percent, count_id, type_id from lib_yarn_count_determina_dtls where is_deleted=0 and mst_id in ($fabrication) order by id";
												
							$data_array=sql_select($sql_q);
							if (count($data_array)>0)
							{
								foreach( $data_array as $row )
								{
									$compo_per="";
									if(($row[csf('percent')]*1)>0) $compo_per=$row[csf('percent')]."% "; else $compo_per="";
									if(array_key_exists($row[csf('mst_id')],$composition_arr))
									{
										$composition_arr[$row[csf('mst_id')]]=$composition_arr[$row[csf('mst_id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$compo_per.$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
									}
									else
									{
										$composition_arr[$row[csf('mst_id')]]=$composition[$row[csf('copmposition_id')]]." ".$compo_per.$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]];
									}
								}
							}
							unset($data_array);
							$sqlRd="select id, type, construction, gsm_weight, weight_type, design, fabric_ref, rd_no, color_range_id, full_width, cutable_width from lib_yarn_count_determina_mst where entry_form=426 and status_active=1 and is_deleted=0 and id in ($fabrication)";
							$sqlRdData=sql_select($sqlRd); $fabricationData="";
							foreach($sqlRdData as $row)
							{
								if($fabricationData=="") $fabricationData="* ".$row[csf('rd_no')].', '.$row[csf('fabric_ref')].', '.$row[csf('type')].', '.$row[csf('construction')].', '.$row[csf('design')].', '.$row[csf('gsm_weight')].', '.$fabric_weight_type[$row[csf('weight_type')]].', '.$color_range[$row[csf('color_range_id')]].', '.$row[csf('full_width')].', '.$row[csf('cutable_width')].', '.$composition_arr[$row[csf('id')]];
								else $fabricationData.="<br> * ".$row[csf('rd_no')].', '.$row[csf('fabric_ref')].', '.$row[csf('type')].', '.$row[csf('construction')].', '.$row[csf('design')].', '.$row[csf('gsm_weight')].', '.$fabric_weight_type[$row[csf('weight_type')]].', '.$color_range[$row[csf('color_range_id')]].', '.$row[csf('full_width')].', '.$row[csf('cutable_width')].', '.$composition_arr[$row[csf('id')]];
							}
                            ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:20px">
                                <strong> Buyer Inquiry Woven </strong>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td align="center" style="font-size:20px;" colspan="4"> <strong>System ID :</strong>&nbsp;<?php echo $system_number; ?>  &nbsp;  &nbsp;  <strong>Copy System ID :</strong>&nbsp;<?php echo $COPY_SYSTEM_NUMBER; ?></td>
            </tr>
            <tr>
                <td align="left" style="font-size:20px;" width="150"><strong> Buyer</strong></td>
                <td align="left" style="font-size:20px;"> <?php echo $buyer_arr[$buyer_id]; ?></td>
                <td align="left" style="font-size:20px;" width="150"><strong>Style Ref.</strong></td>
                <td align="left" style="font-size:20px;"> <?php echo $style_refernce; ?></td>
            </tr>
            <tr>
                <td align="left" style="font-size:20px;"><strong>Inq.Rcvd Date</strong></td>
                <td align="left" style="font-size:20px;"><?php echo change_date_format($inquery_date); ?> </td>
                <td align="left" style="font-size:20px;"><strong> Buyer Inquiry No </strong></td>
                <td align="left" style="font-size:20px;"> <?php echo $buyer_request; ?> </td>
            </tr>
            
            <tr>
                <td align="left" style="font-size:20px;"><strong>Team Leader</strong> </td>
                <td align="left" style="font-size:20px;"><?php echo $team_leader_arr[$TEAM_LEADER]; ?> </td>
                <td align="left" style="font-size:20px;"><strong>Bulk Est. Ship Date</strong></td>
                <td align="left" style="font-size:20px;"><?php echo change_date_format($est_ship_date); ?></td>
            </tr>
            <tr>
                <td align="left" style="font-size:20px;"><strong>Gmts Item</strong> </td>
                <td align="left" style="font-size:20px;"><?php echo $garments_item[$gmts_item]; ?></td>
                <td align="left" style="font-size:20px;"><strong> Bulk Offer Qty</strong> </td>
                <td align="left" style="font-size:20px;"> <?php echo $offer_qty; ?> </td>
            </tr>
            <tr>
                <td align="left" style="font-size:20px;"><strong>Fabrication</strong></td>
                <td align="left" style="font-size:20px;" colspan="3"> <p><?=$fabricationData; ?></p> </td>
            </tr>
            <tr>
                <td align="left" style="font-size:20px;"><strong> Body Color </strong></td>
                <td align="left" style="font-size:20px;"> <?php echo $color; ?></td>
                <td align="left" style="font-size:20px;"> <strong> Season</strong>  </td>
                <td align="left" style="font-size:20px;"> <?php echo $season_name_library[$season_buyer_wise]; ?></td>
            </tr>
            <tr>
                <td align="left" style="font-size:20px;"><strong>Brand</strong></td>
                <td align="left" style="font-size:20px;"> <?php echo $brandArr[$brand_id]; ?></td>
                <td align="left" style="font-size:20px;"> <strong>Season Year</strong>  </td>
                <td align="left" style="font-size:20px;"> <?php echo $season_year; ?></td>
            </tr>
            <tr>
                <td align="left" style="font-size:20px;"> <strong>Target Req. Quot. Date </strong> </td>
                <td align="left" style="font-size:20px;"> <?php echo change_date_format($req_quotation_date); ?></td>
                <td align="left" style="font-size:20px;"> <strong> Target Samp Sub:Date</strong>  </td>
                <td align="left" style="font-size:20px;"> <?php echo change_date_format($target_sam_sub_date); ?></td>
            </tr>
            
            <tr>
                <td align="left" style="font-size:20px;"> <strong>Actual Samp.Send Date </strong></td>
                <td align="left" style="font-size:20px;"> <?php echo change_date_format($actual_sam_send_date); ?></td>
                <td align="left" style="font-size:20px;"> <strong>Actual Quot. Date</strong> </td>
                <td align="left" style="font-size:20px;"> <?php echo change_date_format($actual_req_quot_date); ?></td>
            </tr>
            <tr>
                <td align="left" style="font-size:20px;"><strong>Sample Merchant</strong> </td>
                <td align="left" style="font-size:20px;"><?php echo $marchentrArr[$CONCERN_MARCHANT]; ?> </td>
                <td align="left" style="font-size:20px;">  <strong>Dealing Merchant </strong> </td>
                <td align="left" style="font-size:20px;"><?php echo $marchentrArr[$dealing_marchant]; ?></td>
            </tr>
            <tr>
                <td align="left" style="font-size:20px;"> <strong>Buyer Submit Price </strong> </td>
                <td align="left" style="font-size:20px;"> <?php echo number_format($buyer_submit_price, 2); ?></td>
                <td align="left" style="font-size:20px;"> <strong>Style Description </strong> </td>
                <td align="left" style="font-size:20px;"> <?php echo $style_description; ?></td>
            </tr>
            <tr>
               
                <td align="left" style="font-size:20px;"><strong> Consumption Rec.Tgt.Date</strong>  </td>
                <td align="left" style="font-size:20px;"> <?php echo $rec_target_date; ?></td>
                <td align="left" style="font-size:20px;"><strong>Priority</strong>  </td>
                <td align="left" style="font-size:20px;"> <?php echo $priority_arr[$PRIORITY]; ?></td>
            </tr>
            <tr>
                <td align="left" style="font-size:20px;"><strong>Remarks </strong>  </td>
                <td align="left" style="font-size:20px;" colspan="3"> <?php echo $remarks; ?></td>
            </tr>
            
            
            <tr>
                <td align="left" style="font-size:20px;"><strong>Image</strong>  </td>
                <td align="left" style="font-size:20px;" colspan="3">
				<? $sql = "select id,master_tble_id,image_location from common_photo_library where master_tble_id='$ID' and FORM_NAME in('quotation_inquery_back_image','quotation_inquery_front_image')"; 
				$data_array=sql_select($sql);
			   ?>
					<? foreach($data_array as $inf){ ?>
						<img  src='../../<? echo $inf[csf("image_location")]; ?>' height='100' width='100' style="float:left;" />
					<?  } ?>
          </td>
            </tr>
        </table>
        <?  //echo signature_table(126, $company_name, "850px"); ?>
   	</div>       
        

	    <?
	
		//$mstRow[BRAND_ID]
		
	$message=ob_get_contents();
	ob_clean();
	

	$to='';
	$sql = "SELECT a.BRAND_IDS,a.BUYER_IDS,c.EMAIL_ADDRESS FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id  and a.mail_item=b.MAIL_ITEM_MST and a.mail_item=40 and b.mail_user_setup_id=c.id and a.company_id =".$mstRow['COMPANY_ID']."  and   A.IS_DELETED=0 and A.STATUS_ACTIVE=1 AND a.MAIL_TYPE=5 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1  and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";

	$mail_sql=sql_select($sql);
	$receverMailArr=array();
	foreach($mail_sql as $row)
	{
		if($row['BUYER_IDS']!=''){
			$buyerArr=explode(',',$row['BUYER_IDS']);
			if($row['BUYER_IDS'] )
			foreach($buyerArr as $buyerid){
				$receverMailArr[$buyerid][$row[csf('email_address')]]=$row[csf('email_address')];
			}
		}
		else{
			$toArr[$row['EMAIL_ADDRESS']] = $row['EMAIL_ADDRESS'];
		}
		
		
	}

	if($receverMailArr[$buyer_id]){$toArr[$receverMailArr[$buyer_id]] = $receverMailArr[$buyer_id];}
	$to=implode(',',array_unique($toArr));

	$subject="Buyer Inquiry Woven";
	$header=mailHeader();
	if($to!=""){
		echo sendMailMailer( $to, $subject, $message, $from_mail,$att_file_arr );
		//------last mail send update info---------------------------------------------------
			$con = connect();
			$rID=sql_update("wo_quotation_inquery",'mail_send_date',"'".$pc_date_time."'","id","".$ID."",0,0);
			//echo $rID;die;
			
			if($rID==1){
				oci_commit($con);
				//echo "1**".$update_id;
			}
			else{
				oci_rollback($con);
				//echo "10**".$update_id;
			}
			
			disconnect($con);
			die;
			
		//-------------------------------------------------------------------------
	
	}
	else{echo "Mail Not Send";}
	
		
		
	
		
?>