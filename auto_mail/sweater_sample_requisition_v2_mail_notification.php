<?php
date_default_timezone_set("Asia/Dhaka");
// require_once('../mailer/class.phpmailer.php');
require_once('../includes/common.php');
require_once('setting/mail_setting.php');
extract($_REQUEST);
// var returnValue=return_global_ajax_value(reponse[2], 'sweater_sample_requisition_mail_notification', '', '../../../auto_mail/sweater_sample_requisition_mail_notification');
//echo load_html_head_contents("Mail Notification", "../", 1, 1,'','','');


$action='sweater_sample_requisition_mail_notification';	
if($action=='sweater_sample_requisition_mail_notification'){
	 //$data='20*3574';
	
	list($company_name,$update_id)=explode('*',$data);
	$company_library=return_library_array( "select id, company_name from lib_company where id=$company_name", "id", "company_name"  );
	
	$company_img=sql_select("select image_location  from common_photo_library  where master_tble_id='$company_name' and form_name='company_details' and is_deleted=0 and file_type=1");
	
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='sample_details_1' and file_type=1",'master_tble_id','image_location');
	
	$emb_imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='required_embellishment_1' and file_type=1",'master_tble_id','image_location');
	
	
	$size_arr=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
	$season_arr=return_library_array( "select id,season_name from lib_buyer_season  where status_active =1 and is_deleted=0 ", "id", "season_name"  );
	$dealing_merchant_library=return_library_array( "select id,team_member_name from lib_mkt_team_member_info", "id", "team_member_name"  );
	$sample_library=return_library_array( "select id, sample_name from lib_sample", "id", "sample_name");
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$trims_group_lib=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
	$brandArr=return_library_array( "select id, brand_name from lib_buyer_brand", "id", "brand_name");
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$bom_arr=return_library_array( "select id, bom_no from wo_quotation_inquery", "id", "bom_no");
	
	$store_name_lib=return_library_array( "select id, store_name from lib_store_location", "id", "store_name"  );
	
	$team_name_lib=return_library_array( "select id, team_name from lib_sample_production_team where  product_category=6 and status_active=1 and is_deleted=0", "id", "team_name"  );



	//$company_name=$sql_result[0][COMPANY_ID];

	ob_start();	
	?>
	<div id="mstDiv">

    <table cellspacing="0" cellpadding="5" border="0">
     <tr>
     	<td rowspan="2" colspan="4">
        	<img width="150" height="80" src="../<? echo $company_img[0][csf('image_location')]; ?>">
        </td>
     	<td align="center" colspan="6" style="font-size: 24px;"><strong><? echo $company_library[$company_name]; ?></strong></td>
     </tr>
     <tr>
        <td colspan="10" align="center">
            <?
                $val=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$company_name");
                echo ($val[0][csf('plot_no')])?   $val[0][csf('plot_no')].',': "";
                echo ($val[0][csf('level_no')])?  $val[0][csf('level_no')].',': "";
                echo ($val[0][csf('road_no')])?   $val[0][csf('road_no')].',': "";
                echo ($val[0][csf('block_no')])?  $val[0][csf('block_no')].',': "";
                echo ($val[0][csf('city')])?      $val[0][csf('city')].',': "";
                echo ($val[0][csf('zip_code')])?  $val[0][csf('zip_code')].',': "";
                echo ($val[0][csf('province')])?  $val[0][csf('province')].',': "";
                echo($val[0][csf('country_id')])? $country_arr[$val[0][csf('country_id')]]: "";
                echo ($val[0][csf('email')])?    "</br>". $val[0][csf('email')].',': "</br>";
                echo($val[0][csf('website')])?    $val[0][csf('website')]: "";
                  $sql="SELECT TEAM_LEADER,INSERTED_BY,id, requisition_number, requisition_number_prefix_num, style_ref_no, buyer_name, season, product_dept, DEALING_MARCHANT, agent_name, buyer_ref, estimated_shipdate, season_buyer_wise, remarks, quotation_id, sample_stage_id, requisition_date, team_leader, season_year, brand_id from sample_development_mst where id=$update_id and entry_form_id=459 and  is_deleted=0  and status_active=1";
                  $dataArray=sql_select($sql);
                  
				$sample_name=return_field_value("sample_name","sample_development_dtls","sample_mst_id=$update_id");
				
				$dealing_marchant_id=$dataArray[0][DEALING_MARCHANT];
				$sample_team_id=$dataArray[0][TEAM_LEADER];
				$insert_by=$dataArray[0][INSERTED_BY];
 
				  
            ?>
        </td>
        </tr>
        <tr>
            <td colspan="10" align="center"><strong>Sample Requisition</strong></td>
				<?
                    $is_app=return_field_value("is_approved","sample_development_mst","entry_form_id=459 and id=$update_id and status_active=1 and is_deleted=0");
                    if($is_app==3){$is_app=1;}
                    $appDate=explode(" ", $appDate);
                    if($is_app==1)
                    {
                        echo "<span style='color:red;border:2px solid black;'>
						- Approved By ".$user_arr[$appBy]."
                        , Approved Date: ". change_date_format($appDate[0],'yyyy-mm-dd')." </span>";
                    }
                 ?>
        	</td>
        </tr>
        <tr>
            <td colspan="2"><strong>Requisition No</strong></td><td>:</td>
            <td><? echo $dataArray[0][csf("requisition_number")]; ?></td> 
            <td><strong>Req. Date</strong></td><td>:</td>
            <td><? echo change_date_format($dataArray[0][csf("requisition_date")],"dd-mm-yyyy"); ?></td>
            <td><strong>Buyer Name</strong></td><td>:</td>
            <td><? echo $buyer_library[$dataArray[0][csf('buyer_name')]];?></td>
            
        </tr>
        <tr>
        	<td colspan="2"><strong>Master/Style Ref</strong></td><td>:</td>
            <td><? echo $dataArray[0][csf('style_ref_no')];?></td>
            <td><strong>Season</strong></td><td>:</td>
            <td><? echo $season_arr[$dataArray[0][csf('season')]];?></td>
            <td><strong>Saeason Year</strong></td><td>:</td>
            <td><?=$dataArray[0][csf('season_year')]; ?></td>
       	</tr>
       	 <tr>
         	<td colspan="2"><strong>Dealing Merchant</strong></td><td>:</td>
            <td><? echo $dealing_merchant_library[$dataArray[0][csf('dealing_marchant')]];?></td>
            <td><strong>Product Dept</strong></td><td>:</td>
            <td><? echo $product_dept[$dataArray[0][csf('product_dept')]];?></td>
            <td><strong>Sample Name</strong></td><td>:</td>
            <td><? echo $sample_library[$sample_name];?></td>
		 </tr>
		 <tr>
         	<td colspan="2"><strong>Buyer Ref</strong></td><td>:</td>
            <td><? echo $dataArray[0][csf('buyer_ref')];?></td>
            <td><strong>Est.Ship Date</strong></td><td>:</td>
            <td><? echo change_date_format($dataArray[0][csf('estimated_shipdate')]);?></td>
            <td><strong>Brand</strong></td><td>:</td>
            <td><P><?=$brandArr[$dataArray[0][csf('brand_id')]]; ?></P></td>
        </tr>
        <tr>
        	<td colspan="2"><strong>Team Name</strong></td><td>:</td>
            <td><P><? echo $team_name_lib[$dataArray[0][csf('team_leader')]];?></P></td>
            <td><strong>BOM</strong></td><td>:</td>
            <td><P><? echo $bom_arr[$dataArray[0][csf('quotation_id')]];?></P></td>
       		 
        </tr>
        <tr>
        	<td colspan="2"><strong>Remarks/Desc</strong></td><td>:</td>
       		<td colspan="3" style="word-wrap: break-word;word-break: break-all;" ><? echo $dataArray[0][csf('remarks')];?></td>
        </tr>
    </table>
        
    <table cellspacing="0" border="1" class="rpt_table" rules="all" width="870">
        <thead>
            <tr><td colspan="11" align="center"><strong>Sample Details</strong></td></tr>
            <tr>
                <th width="30">SL</th>
                <th width="120">Garment Item</th>
                <th width="55">Article No</th>
                <th width="70">Gmts Color</th>
                <th width="70">Color Combo NO.</th>
                <th colspan="2">Prod Qty</th>
                <th width="45">Submission Qty</th>
                <th width="70">Start Date</th>
                <th width="70">Delivery Date</th>
                <th>Images</th>
             </tr>
        </thead>
        <tbody>

            <?
		$size_select=sql_select("SELECT  BH_QTY,DTLS_ID,SIZE_ID,TOTAL_QTY  from sample_development_size where mst_id=$update_id and status_active=1 and is_deleted=0");
		foreach($size_select as $row)
		{
			$sizeDataArr[$row[DTLS_ID]][$row[SIZE_ID]]+=$row[TOTAL_QTY];
			$bhQtyArr[$row[DTLS_ID]]+=$row[BH_QTY];
		}
		  
		 $sql_qry="select id,sample_mst_id,sample_name,gmts_item_id,smv,article_no,sample_color,sample_prod_qty,submission_qty,delv_start_date,delv_end_date,sample_charge,sample_curency,color_combo_no from sample_development_dtls where status_active =1 and is_deleted=0 and entry_form_id=459 and sample_mst_id=$update_id and  status_active =1 and is_deleted=0 order by id asc";
			
		$result=sql_select($sql_qry);
		$i=1;$totalSizeQty=0;$totalSubmissionQty=0;
		foreach($result as $row)
		{
            $rowspan=count($sizeDataArr[$row[csf('id')]]);
			$totalSubmissionQty+=$bhQtyArr[$row[csf('id')]];
			?>
            <tr>
                <td rowspan="<? echo $rowspan;?>" align="center"><? echo $i;?></td>
                <td rowspan="<? echo $rowspan;?>"><? echo $garments_item[$row[csf('gmts_item_id')]];?></td>
                <td rowspan="<? echo $rowspan;?>"><? echo $row[csf('article_no')];?></td>
                <td rowspan="<? echo $rowspan;?>"><? echo $color_library[$row[csf('sample_color')]];?></td>
                <td rowspan="<? echo $rowspan;?>"><? echo $row[csf('color_combo_no')];?></td>
                <? 
				$ii=1;
				
				foreach($sizeDataArr[$row[csf('id')]] as $size_id=>$size_qty){
				   if($ii !=1){echo "<tr>";}
				   $totalSizeQty+=$size_qty;
				?>
                <td width="80" align="center"><p><? echo $size_arr[$size_id];?></p></td>
                <td align="right" width="50"><? echo $size_qty;?></td>
                <? 
					if($ii==1){?>
					<td rowspan="<? echo $rowspan;?>" align="right"><? echo $bhQtyArr[$row[csf('id')]];?></td>
					<td rowspan="<? echo $rowspan;?>" align="center"><? echo change_date_format($row[csf('delv_start_date')]);?> </td>
					<td rowspan="<? echo $rowspan;?>" align="center"><? echo change_date_format($row[csf('delv_end_date')]);?> </td>
					<td rowspan="<? echo $rowspan;?>"><img src="../../../<? echo $imge_arr[$row[csf('id')]];?>" width="120" height='60'></td>
					<?	
					}
					echo "</tr>";
				$ii++;
				}
			$i++;
           }

            ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" align="center"><b>Total</b></td>
                <td align="right"><b><? echo number_format($totalSizeQty,2);?> </b></td>
                <td  align="right"><b><? echo number_format($totalSubmissionQty,2);?> </b></td>
                <td colspan="3"></td>
            </tr>
        </tfoot>
   </table>
    <br>  
  	<table cellspacing="0" border="1" class="rpt_table" rules="all" width="800">
        <thead>
            <tr> 
                <th colspan="10" align="center"><strong>Required Yarn</strong></th>
            </tr>
            <tr>
                <th width="30" align="center">SL</th>
                <th width="120" align="center">Garment Item</th>
                <th width="70">Buyer Prov </th>
                <th width="120" align="center">Gmts Color</th>
                <th width="170" align="center">Yarn Composition</th>
                <th width="60" align="center">Count</th>
                <th width="60" align="center">Guage</th>
                <th width="70">No Of Ends </th>
                <th width="60" align="center">Yarn Color</th>
                <th width="60" align="center">Yarn Req. Qty.</th>
                <th width="60" align="center">UOM</th>
                <th width="60" align="center">Lot No</th>
                <th width="60" align="center">Yarn Store</th>
                <th align="center">Remarks</th>
            </tr>
        </thead>
        <tbody>
            <?
           $sql_qryf="SELECT a.uom_id,a.gauge, a.id, a.gmts_item_id, a.sample_mst_id, a.sample_name, a.body_part_id, a.fabric_nature_id, a.fabric_description, a.gsm, a.dia, a.SAMPLE_COLOR, a.color_data, a.color_type_id, a.width_dia_id,a.remarks_ra,a.buyer_prov,a.no_of_ends
    FROM sample_development_fabric_acc a WHERE a.sample_mst_id = $update_id AND a.form_type = 1 and  a.status_active =1 and a.is_deleted=0
ORDER BY a.id ASC";
		    $resultf=sql_select($sql_qryf);
            $k=1;
			$sl=0;
			foreach($resultf as $row)
            {
			$color_data_arr=explode('-----',$row[csf('color_data')]);
			foreach($color_data_arr as $dataArr){
				list($txtSL,$txtColor,$hiddenColorId,$txtYarnColor,$txtCount,$txtComposition,$cboType,$cboYarnSource,$txtGreyQnty,$cboUom,$txtLot,$cboStore,$text_comments,$yarn_color_id)=explode('__',$dataArr);
				$totalFinishReqQty+=$txtGreyQnty;
				?>
				<tr>
					<td align="center"><? echo $k;?></td>
					<td align="left"><? echo $garments_item[$row[csf('gmts_item_id')]];?></td>
					<td align="left"><? echo $row[csf('buyer_prov')];?></td>
					<td align="left"><? echo $color_library[$row[SAMPLE_COLOR]];?></td>
					<td align="left"><? echo $txtComposition;?></td>
					<td align="left"><? echo $count_arr[$txtCount];?></td>
					<td align="left"><? echo $gauge_arr[$row[csf('gauge')]];?></td>
					<td align="left"><? echo $row[csf('no_of_ends')];?></td>
					<td align="left"><? echo $txtYarnColor;?></td>
					<td align="right"><? echo $txtGreyQnty;?></td>
					<td align="center"><? echo $unit_of_measurement[$cboUom];?></td>
					<td align="center"><? echo $txtLot;?></td>
					<td align="center"><? echo $store_name_lib[$cboStore];?></td>
					<td align="right"><p><? echo $text_comments;?></p></td>
				 </tr>
			   <?
			   $k++;
			}
           }
           ?>
        </tbody>
        <tfoot>
            <td colspan="9" align="right"><b>Total </b></td>
            <td  align="right"><b><? echo number_format($totalFinishReqQty,2);?> </b></td>
            <td  align="right"></td>
            <td  align="right"></td>
            <td  align="right"></td>
         </tfoot>
    </table>    
    
    <br>  
 	<table cellspacing="0" border="1"  class="rpt_table" rules="all" width="800">
        <thead>
            <tr>
                 <td colspan="10" align="center"><strong>Required Accessories</strong></td>
             </tr>
             <tr>
                <th width="30" align="center">SL</th>
                <th width="120" align="center">Garment Item</th>
                <th width="100" align="center">Trims Group</th>
                <th width="100" align="center">Description</th>
                <th width="100" align="center">Brand/Supp.Ref</th>
                <th width="30" align="center">UOM</th>
                <th width="30" align="center">Req/Dzn </th>
                <th width="30" align="center">Req/Qty </th>
                <th align="center">Remarks </th>
            </tr>
        </thead>
        <tbody>
		<?
           $sql_qryA="select id,sample_mst_id,sample_name_ra,gmts_item_id_ra,trims_group_ra,description_ra,brand_ref_ra,uom_id_ra,req_dzn_ra,req_qty_ra,remarks_ra from sample_development_fabric_acc where status_active =1 and is_deleted=0 and form_type=2 and sample_mst_id='$update_id' order by id asc";

            $resultA=sql_select($sql_qryA);
            $k=1;
            $req_dzn_ra=0;
            $req_qty_ra=0;
            foreach($resultA as $rowA)
            {
                $req_dzn_ra=$req_dzn_ra+$rowA[csf('req_dzn_ra')];
                $req_qty_ra=$req_qty_ra+$rowA[csf('req_qty_ra')];
            ?>
            <tr>
                <td  align="center"><? echo $k;?></td>
                <td  align="left"><? echo $garments_item[$rowA[csf('gmts_item_id_ra')]];?></td>
                <td  align="left"><? echo $trims_group_lib[$rowA[csf('trims_group_ra')]];?></td>
                <td  align="left"><? echo $rowA[csf('description_ra')];?></td>
                <td  align="left"><? echo $rowA[csf('brand_ref_ra')];?></td>
                <td  align="center"><? echo $unit_of_measurement[$rowA[csf('uom_id_ra')]];?></td>
                <td  align="right"><? echo $rowA[csf('req_dzn_ra')];?></td>
                <td  align="right"><? echo $rowA[csf('req_qty_ra')];?></td>
                <td  align="left"><? echo $rowA[csf('remarks_ra')];?></td>
             <?
             $k++;
            }
            ?>
            </tr>
        </tbody>
        <tfoot>
            <td colspan="7" align="center"><b>Total </b></td>
            <td align="right"><b><? echo number_format($req_qty_ra,2);?> </b></td>
            <td>&nbsp;</td>
        </tfoot>
   </table> 
    <br>  
 	<table cellspacing="0" border="1" class="rpt_table" rules="all">
        <thead>
            <tr>
                <td width="150" colspan="6" align="center"><strong>Required Emebellishment</strong></td>
            </tr>
        <tr>
            <th width="30" align="center">SL</th>
            <th width="120" align="center">Garment Item</th>
            <th width="100" align="center">Name</th>
            <th width="70" align="center">Type</th>
            <th align="center">Remarks</th>
            <th width="100">Images</th>
        </tr>
        </thead>
        <tbody>
            <?
            $sql_qry="select id,sample_mst_id,sample_name_re,gmts_item_id_re,name_re,type_re,remarks_re from sample_development_fabric_acc where sample_mst_id='$update_id' and form_type=3 and is_deleted=0  and status_active=1 order by id asc";
            $result=sql_select($sql_qry);
            $k=1;
            $type_array=array(1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$emblishment_gmts_type);
            foreach($result as $row)
            {
            ?>
            <tr>
                <td  align="center"><? echo $k;?></td>
                <td  align="left"><? echo $garments_item[$row[csf('gmts_item_id_re')]];?></td>
                <td  align="left"><? echo $emblishment_name_array[$row[csf('name_re')]];?></td>
                <td  align="left">
                <?
                if($row[csf('name_re')]==1)
                {
                  echo $emblishment_print_type[$row[csf('type_re')]];
                }
                if($row[csf('name_re')]==2)
                {
                  echo $emblishment_embroy_type[$row[csf('type_re')]];
                }
                if($row[csf('name_re')]==3)
                {
                  echo $emblishment_wash_type[$row[csf('type_re')]];
                }
                if($row[csf('name_re')]==4)
                {
                  echo $emblishment_spwork_type[$row[csf('type_re')]];
                }
                if($row[csf('name_re')]==5)
                {
                  echo $emblishment_gmts_type[$row[csf('type_re')]];
                }
                ?>
                </td>
                <td><p><? echo $row[csf('remarks_re')];?></p></td>
				<td><img src="../<? echo $emb_imge_arr[$row[csf('id')]];?>" width="120" height='60'></td>
             <?
			 $k++;
            }

            ?>
            </tr>
        </tbody>
   </table>     

 	<? //echo signature_table(459, $data[0], "810px");?>    
     
    </div>
	<?
	$emailBody=ob_get_contents();
	ob_clean();
	
	
	$filename=$company_name.'_'.$update_id.".doc";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,$emailBody);
	$att_file_arr[]=$filename;
	
	
	

	$team_email_arr=return_library_array("select id,email from  lib_sample_production_team where product_category=6 and is_deleted=0 and id=$sample_team_id","id","email");
	$user_arr=return_library_array("select id,USER_EMAIL from  USER_PASSWD where  is_deleted=0 and USER_EMAIL is not null  and id=$insert_by","id","USER_EMAIL");

    $dealing_merchant_email_arr = return_library_array("select id, TEAM_MEMBER_EMAIL from lib_mkt_team_member_info where id=$dealing_marchant_id", 'id', 'TEAM_MEMBER_EMAIL');

   
    $toArr=array();
    $cad_user_name=sql_select("select CAD_USER_NAME from lib_mkt_team_member_info where TEAM_ID=$insert_by and STATUS_ACTIVE=1 and IS_DELETED=0");
    // print_r($cad_user_name); die(); 
    //echo  $cad_user_name[0][CAD_USER_NAME];die;
    foreach(explode(",",$cad_user_name[0][CAD_USER_NAME]) as $uid){
        $toArr[]=$user_arr[$uid];
    }
    
    //echo  $dealing_marchant_id;die();
    //print_r($dealing_merchant_email_arr); die();
	 //echo "select id, TEAM_MEMBER_EMAIL from lib_mkt_team_member_info where id=$dealing_marchant_id";
	
	
	$toArr[]=$dealing_merchant_email_arr[$dealing_marchant_id];
	$toArr[]=$team_email_arr[$sample_team_id];
	$toArr[]=$user_arr[$insert_by];
	$to=implode(',',$toArr);

	$subject="Sample Requisition V2";
	//if($to!=""){echo sendMailMailer( $to, $subject, $emailBody );}else{ echo 'Sorry. Email not found';}
    //if($to!=""){echo sendMailMailer( $to, $subject, $emailBody, $from_mail,$att_file_arr );}else{ echo 'Sorry. Email not found';}
    if($_REQUEST['isview']==1){
        echo $emailBody;
    }
    else{
        if($to!=""){echo sendMailMailer( $to, $subject, $emailBody, $from_mail,$att_file_arr );}else{ echo 'Sorry. Email not found';}
    }

	//echo $emailBody; die;
//}
	
exit();	

}

?>
