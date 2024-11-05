<?php
/*-------------------------------------------- Comments
Purpose         :   Sample requisition with booking Woven auto mail
Functionality   :   
JS Functions    :
Created by      :   Al-Hasan
Creation date   :   06-12-2023
Updated by      :
Update date     :  
QC Performed BY :
QC Date         :
Comments        :
*/
  
date_default_timezone_set("Asia/Dhaka");
require_once('../../includes/common.php');
require_once('../../mailer/class.phpmailer.php');
require_once('../setting/mail_setting.php');
 
extract($_REQUEST);

 


$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
$supplier_library = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");

$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name"  );
// $dealing_merchant_library = return_library_array("select id,team_member_name from lib_mkt_team_member_info", "id", "team_member_name");
$team_leader_arr = return_library_array("select id,team_leader_name from lib_marketing_team", "id", "team_leader_name");
$sample_library = return_library_array("select id, sample_name from lib_sample", "id", "sample_name");
$size_library = return_library_array("select id, size_name from lib_size", "id", "size_name");
$color_library = return_library_array("select id, color_name from lib_color", "id", "color_name");
$season_arr = return_library_array("select id,season_name from lib_buyer_season  where status_active =1 and is_deleted=0 ", "id", "season_name");
$trims_group_lib = return_library_array("select id,item_name from lib_item_group  where status_active =1 and is_deleted=0", "id", "item_name");
$user_arr = return_library_array("select id, user_name from user_passwd", "id", "user_name" );
$imge_arr = return_library_array("select master_tble_id,image_location from common_photo_library where form_name='sample_details_1' and file_type=1",'master_tble_id','image_location');
$fabric_composition_arr = return_library_array("select id,fabric_composition_name from lib_fabric_composition where  status_active=1", "id", "fabric_composition_name");


$current_time = ($_REQUEST['view_date'])?strtotime($_REQUEST['view_date']):time();
$current_date = date("d-M-Y", $current_time);
$prev_date = change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))), '', '', 1);
$date_condition	=" and requisition_date between '".$prev_date."' and '".$prev_date."'";

if($db_type==0)
{
    $current_date = date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0)));
    $previous_date = date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))); 
}
else
{
    $current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0))),'','',1);
    $previous_date = change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1);
}

// $user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name");
$user_result_library = sql_select( "select id, USER_NAME, USER_EMAIL from user_passwd where STATUS_ACTIVE=1 and IS_DELETED=0");
foreach($user_result_library as $row){
    $user_arr[$row['ID']] =$row['USER_NAME'];
    $user_mail_arr[$row['ID']] =$row['USER_EMAIL'];
}


// echo $date_condition;die;
 
//echo $sys_id;die;

$sysSql = "select ID, COMPANY_ID,REQUISITION_NUMBER,BUYER_NAME,BRAND_ID  from sample_development_mst where  entry_form_id=203 and is_deleted=0 and status_active=1  AND REQUISITION_NUMBER = '".$sys_id."'";
		//	echo $sysSql;die;
foreach(sql_select($sysSql) as $vals)
{
    $company_id=$vals['COMPANY_ID'];
    $brand_id=$vals['BRAND_ID'];
    $buyer_id=$vals['BUYER_NAME'];
    
    $sys_id=$vals['REQUISITION_NUMBER'];
    $mst_id=$vals['ID'];
}


    // $data[0] = $company_id;
    // $data[1] = 7616;
    // $data[2] = 'OG-SMN-23-00214';
    $cbo_template_id = 1;
    $company_img = sql_select("SELECT image_location FROM common_photo_library  WHERE master_tble_id='$company_id' and form_name='company_details' and is_deleted=0 and file_type=1");

     
    $sql_fab = sql_select("SELECT id, fabric_composition_id, construction FROM lib_yarn_count_determina_mst WHERE status_active=1");
	foreach($sql_fab as $row)
	{
		$lip_yarn_count[$row[csf("id")]] = $row[csf("fabric_composition_id")];
		$fab_constructArr[$row[csf("id")]] = $row[csf("construction")];
	}
	$sam_img = sql_select("SELECT image_location,master_tble_id FROM common_photo_library WHERE form_name='sample_details_1' and is_deleted=0 and file_type=1");
	$sam_img_arr = array();
	foreach($sam_img as $row)
	{
	  $sam_img_arr[$row[csf('master_tble_id')]]['img'] = $row[csf('image_location')];
	}

    ob_start();
    ?>

<div id="mstDiv">

        <table width="1100" cellspacing="0" border="0"  style="font-family: Arial Narrow;margin-left: 20px;" >
            <tr>
                <td rowspan="4" valign="top" width="300"><img width="150" height="80" src="<?= base_url($company_img[0][csf("image_location")]); ?>" ></td>
                <td colspan="4" style="font-size: 24px;"><strong><b><?= $company_library[$company_id]; ?></b></strong></td>
            </tr>
            <tr>
                <td colspan="5">
                    <?

                        $val=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$company_id");
                        echo ($val[0][csf('level_no')])? $val[0][csf('level_no')].',': "";
                        echo ($val[0][csf('road_no')]) ? $val[0][csf('road_no')].',': "";
                        echo ($val[0][csf('block_no')]) ? $val[0][csf('block_no')].',': "";
                        echo ($val[0][csf('city')]) ? $val[0][csf('city')].',': "";
                        echo ($val[0][csf('zip_code')]) ? $val[0][csf('zip_code')].',': "";
                        echo ($val[0][csf('province')]) ? $val[0][csf('province')].',': "";
                        echo($val[0][csf('country_id')]) ? $country_arr[$val[0][csf('country_id')]]: "";
                        echo ($val[0][csf('email')]) ? "</br>". $val[0][csf('email')].',': "</br>";
                        echo($val[0][csf('website')]) ? $val[0][csf('website')]: "";

                        // $sql="SELECT id, requisition_number, requisition_number_prefix_num, style_ref_no, buyer_name, product_dept, dealing_marchant, agent_name, buyer_ref, bh_merchant, estimated_shipdate, team_leader, season_buyer_wise, remarks, quotation_id, sample_stage_id, requisition_date, material_delivery_date, season_year, brand_id,INSERTED_BY from sample_development_mst where  id='$mst_id' and entry_form_id=449 and  is_deleted=0 and status_active=1";

                        $sql = "SELECT id, requisition_number, requisition_number_prefix_num, style_ref_no, style_desc, buyer_name, product_dept, dealing_marchant, agent_name, buyer_ref, bh_merchant, estimated_shipdate, team_leader, season_buyer_wise, remarks, quotation_id, sample_stage_id, requisition_date, material_delivery_date, season_year, brand_id, INSERTED_BY, internal_ref, revised_no as revised FROM sample_development_mst WHERE id='$mst_id' and entry_form_id=203 and is_deleted=0 and status_active=1 ORDER BY id DESC";

                        // echo $sql;die;
 
      
                        $dataArray = sql_select($sql); 
                        $id = $dataArray[0][csf('id')];
                        $barcode_no = $dataArray[0][csf('requisition_number')];
                        $dealing_marchant = $dataArray[0][csf('dealing_marchant')];
                        $team_leader = $dataArray[0][csf('team_leader')];
                        $remarks = $dataArray[0][csf('remarks')];
                        $style_desc = $dataArray[0][csf('style_desc')];
                        $sampleStageId = $dataArray[0][csf('sample_stage_id')];

                        $booking_sqls = "SELECT a.booking_no, a.is_approved, a.currency_id, a.fabric_source, a.pay_mode, a.team_leader, a.dealing_marchant, a.ready_to_approved,a.supplier_id,a.booking_date,a.revised_no,a.style_desc,a.attention,a.revised_number FROM wo_non_ord_samp_booking_dtls b,wo_non_ord_samp_booking_mst a where a.booking_no = b.booking_no and b.style_id = '$id' and b.status_active = 1 and b.is_deleted = 0 and a.status_active = 1 and a.is_deleted = 0 group by a.booking_no, a.is_approved, a.currency_id, a.fabric_source, a.pay_mode, a.team_leader, a.dealing_marchant, a.ready_to_approved, a.supplier_id, a.booking_date, a.revised_no, a.style_desc, a.attention, a.revised_number";
 
                       // echo $booking_sqls;die;

                        $booking_res = sql_select($booking_sqls);
                        $booking_no = $booking_res[0]['BOOKING_NO'];

                        if($dataArray[0][csf("sample_stage_id")] == 1)
                        {
                            $sqls = "SELECT supplier_id, revised_no, source, team_leader, dealing_marchant, pay_mode, booking_date FROM wo_booking_mst WHERE  booking_no = '$booking_no' and is_deleted = 0  and status_active = 1";
                        }
                        else{
                            $sqls = "SELECT style_desc, supplier_id, revised_no, buyer_req_no, source, team_leader, dealing_marchant, pay_mode, booking_date,revised_number,attention,team_leader,dealing_marchant FROM wo_non_ord_samp_booking_mst WHERE booking_no='$booking_no' and is_deleted = 0 and status_active = 1";
                           // echo $sqls;
                        }

                        // $sqls="SELECT style_desc, supplier_id, revised_no, buyer_req_no, source, booking_date, attention,TEAM_LEADER,DEALING_MARCHANT from wo_non_ord_samp_booking_mst where booking_no='$booking_no' and is_deleted=0 and status_active=1";
                        
                        // echo $sqls;die;

                        $dataArray_book = sql_select($sqls);

                        $TEAM_LEADER = $dataArray_book[0]['TEAM_LEADER'];
						$DEALING_MARCHANT = $dataArray_book[0]['DEALING_MARCHANT'];

                        // print_r($dataArray_book);
                            
                        $nameArray_approved = sql_select("SELECT approved_by,approved_date FROM wo_non_ord_samp_booking_mst a, approval_history b WHERE a.id=b.mst_id and a.booking_no='$booking_no' and b.entry_form = 9 and a.status_active = 1 and a.is_deleted = 0 ORDER BY b.id DESC");

                        $approved_by = $user_arr[$nameArray_approved[0][csf("approved_by")]];
                        $approved_date = change_date_format($nameArray_approved[0][csf("approved_date")]);

                        $appDate = return_field_value("approved_date", "approval_history", "entry_form=25 and mst_id='$id' order by id desc");
                        $appBy = return_field_value("approved_by", "approval_history", "entry_form=25 and mst_id='$id'");





                       // echo "select ID,TEAM_MEMBER_NAME,TEAM_MEMBER_EMAIL from lib_mkt_team_member_info where STATUS_ACTIVE=1 and IS_DELETED=0";die;

                        $team_leader_result_arr=sql_select( "select ID, TEAM_LEADER_NAME, TEAM_LEADER_EMAIL from lib_marketing_team where STATUS_ACTIVE=1 and IS_DELETED=0");
                        foreach($team_leader_result_arr as $row){
                            $team_leader_arr[$row['ID']] =$row['TEAM_LEADER_NAME'];
                            $team_leader_mail_arr[$row['ID']] =$row['TEAM_LEADER_EMAIL'];
                        }
                            
                        $dealing_merchant_result_library=sql_select( "select ID,TEAM_MEMBER_NAME,TEAM_MEMBER_EMAIL from lib_mkt_team_member_info where STATUS_ACTIVE=1 and IS_DELETED=0");
                        foreach($dealing_merchant_result_library as $row){
                            $dealing_merchant_library[$row['ID']] =$row['TEAM_MEMBER_NAME'];
                            $dealing_merchant_mail_library[$row['ID']] =$row['TEAM_MEMBER_EMAIL'];
                        }
                        
                    ?>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="font-size:medium"><strong> <b>Sample Requisition with Booking Knit</b></strong></td>
                <td colspan="2" id="" width="250"><b>Approved By :<?= $approved_by ?></b> </br><b>Approved Date :<?= $approved_date ?></b></td>
            </tr>
        </table>
        <table width="1100" cellspacing="0" border="0" class="rpt_table" style="font-family: Arial Narrow;margin-left: 20px;" >
            <tr>
                <td colspan="4" align="left"><strong>System No. &nbsp;<?= $dataArray[0][csf("requisition_number")]; ?> </strong></td>
                <td ><strong>Revise:</strong></td>
                <td ><?= $dataArray_book[0][csf('revised_number')];?></td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td width="100"><strong>Booking No: </strong></td>
                <td width="130" align="left"><?= $booking_no;?></td>
                <td width="120" align="left">&nbsp;&nbsp;<strong>Style Ref:</strong></td>
                <td width="110">&nbsp;<?= $dataArray[0][csf('style_ref_no')];?></td>
                <td width="110" align="left"><strong>Sample Sub Date:</strong></td>
                <td width="100"><?= change_date_format($dataArray[0][csf('material_delivery_date')]);?></td>
                <td width="110" align="left"><strong>Style Desc:</strong></td>
                <td><?= $style_desc;?></td>
            </tr>
            <tr>
                <td width="100"><strong>Buyer Name: </strong></td>
                <td width="130" align="left"><?= $buyer_library[$dataArray[0][csf('buyer_name')]];?></td>
                <td width="120" style="word-break:break-all;" align="left">&nbsp;&nbsp;<strong>Season (<?=$dataArray[0][csf('season_year')];?>) :</strong></td>
                <td width="110">&nbsp;<?= $season_arr[$dataArray[0][csf('season')]];?></td>
                <td width="110"><strong>BH Merchandiser:</strong></td>
                <td width="100"><?= $dataArray[0][csf('bh_merchant')];?></td>
                <td width="110"><strong>Remarks/Desc:</strong></td>
                <td style="word-wrap: break-word;word-break: break-all;" ><?= $dataArray[0][csf('remarks')];?></td>
            </tr>
            <tr>
                <td width="100" align="left"><strong>Buyer Ref:</strong></td>
                <td width="130"><?= $dataArray[0][csf('buyer_ref')];?></td>
                <td width="120">&nbsp;&nbsp;<strong>Product Dept:</strong></td>
                <td width="110">&nbsp;<?= $product_dept[$dataArray[0][csf('product_dept')]];?></td>
                <td width="110"><strong>Supplier:</strong></td>
                <td width="100">
                    <?
                    if($dataArray_book[0][csf('pay_mode')]==1 || $dataArray_book[0][csf('pay_mode')]==2){
                        echo $supplier_library[$dataArray_book[0][csf('supplier_id')]];
                    }elseif($dataArray_book[0][csf('pay_mode')]==3 || $dataArray_book[0][csf('pay_mode')]==4 || $dataArray_book[0][csf('pay_mode')]==4){
                        echo $company_library[$dataArray_book[0][csf('supplier_id')]];
                    }
                    ?>
                </td>
                <td width="110"><strong>Est. Ship Date:</strong></td>
                <td ><?= change_date_format($dataArray[0][csf('estimated_shipdate')]); ?></td>
            </tr>
            <tr>
                <td width="100"><strong>IR/Control No:</strong></td>
                <td width="130" ><?= $dataArray[0][csf('internal_ref')];?></td>
                <td width="100">&nbsp;&nbsp;<strong>Team Leader:</strong></td>
                <td width="130">&nbsp;<?= $team_leader_arr[$team_leader];?></td>
                <td width="110"><strong>Sample Stage:</strong></td>
                <td width="100"><?= $sample_stage[$dataArray[0][csf('sample_stage_id')]];?></td>
                <td width="110"><strong>Booking Date:</strong></td>
                <td width="100"><?=change_date_format($dataArray_book[0][csf('booking_date')]);?></td>
            </tr>
            <tr>
                <td colspan="2"><strong>Dealing Merchandiser:</strong></td>
                <td><?= $dealing_merchant_library[$dealing_marchant];?></td>
            </tr>
       </table>

   <table width="1100" cellspacing="0" border="0"   style="font-family: Arial Narrow;margin-left: 20px;" >
    <tr>
       <td width="250" align="left" valign="top" colspan="2">
       <table align="left" cellspacing="0" border="0" width="90%" >

       </table>
   </td>
   </tr>
    <tr> <td colspan="6">&nbsp;</td></tr>
   <tr>
       <td width="250" align="left" valign="top" colspan="2">
       <?
       $sql_sample_dtls = "SELECT a.sample_name, a.article_no, a.sample_color FROM sample_development_dtls a, lib_color b WHERE a.status_active=1 and a.is_deleted=0 and a.entry_form_id = 203 and sample_mst_id='$id' and b.status_active = 1 and a.status_active=1 and b.id=a.sample_color GROUP BY a.sample_name, a.article_no, a.sample_color";

       foreach(sql_select($sql_sample_dtls) as $key=>$value)
       {
           if($sample_wise_article_no[$value[csf("sample_name")]][$value[csf("sample_color")]] == "")
           {
               $sample_wise_article_no[$value[csf("sample_name")]][$value[csf("sample_color")]] = $value[csf("article_no")];
           }
           else
           {
               if(!in_array($value[csf("article_no")], $sample_wise_article_no))
               {
                   $sample_wise_article_no[$value[csf("sample_name")]][$value[csf("sample_color")]] .=  ', '.$value[csf("article_no")];
               }

           }	
       }
   $color_sql = "SELECT b.color_id, b.process_loss_percent, b.fabric_color, b.contrast, b.mst_id,b.dtls_id FROM sample_development_rf_color b WHERE b.status_active=1 and b.is_deleted=0 and b.qnty>0 and b.mst_id='$id' ";
   $color_res = sql_select($color_sql);
   $color_rf_data = array();
    foreach ($color_res as $val)
    {
        $color_rf_data[$val[csf('dtls_id')]][$val[csf('color_id')]][$val[csf('fabric_color')]]['process_loss_percent'] = $val[csf('process_loss_percent')];
        $color_rf_data[$val[csf('dtls_id')]][$val[csf('color_id')]][$val[csf('fabric_color')]]['contrast'] = $val[csf('contrast')];
    }
   
   if($sampleStageId==1){
       $sql_fab = "SELECT a.id,a.sample_name,a.gmts_item_id,c.color_id as color_id,c.qnty as qnty,a.delivery_date,a.fabric_description,a.body_part_id,c.process_loss_percent, a.fabric_source,a.remarks_ra ,a.gsm,a.dia, a.color_type_id,a.width_dia_id,a.uom_id,a.determination_id,c.grey_fab_qnty as grey_fab_qnty,c.dtls_id,c.contrast,c.fabric_color from sample_development_fabric_acc a, sample_development_rf_color c where a.id=c.dtls_id and a.form_type=1 and a.grey_fab_qnty>0 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.sample_mst_id='$id' ";
   }else{
       $sql_fab = "SELECT a.id, a.sample_name, a.gmts_item_id, c.gmts_color as color_id, c.finish_fabric as qnty, a.delivery_date, a.fabric_description, a.body_part_id, a.fabric_source,a.yarn_dtls,a.remarks_ra  ,a.gsm,a.dia, a.color_type_id,a.width_dia_id,a.uom_id,a.determination_id,c.grey_fabric as grey_fab_qnty,c.dtls_id,c.fabric_color FROM sample_development_fabric_acc a, wo_non_ord_samp_booking_dtls c WHERE  a.id=c.dtls_id and  c.style_id=a.sample_mst_id  and a.determination_id=c.lib_yarn_count_deter_id  and a.form_type=1 and c.grey_fabric>0 and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0  and a.sample_mst_id='$id'  ";
   }
    $sql_fab_arr=array();$determination_id_arr=array();
    foreach(sql_select($sql_fab) as $vals)
    {
       $contrast = $color_rf_data[$vals[csf('dtls_id')]][$vals[csf('color_id')]][$vals[csf('fabric_color')]]['contrast'];
       $process_loss_percent = $color_rf_data[$vals[csf('dtls_id')]][$vals[csf('color_id')]][$vals[csf('fabric_color')]]['process_loss_percent'];

       $article_no = rtrim($sample_wise_article_no[$vals[csf("sample_name")]][$vals[csf("color_id")]],',');
       $article_no = implode(",",array_unique(explode(",",$article_no)));

       $sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("gmts_item_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("determination_id")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["qnty"] += $vals[csf("qnty")];
       
       $sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("gmts_item_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("determination_id")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["process_loss_percent"] = $process_loss_percent;

       $sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("gmts_item_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("determination_id")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["grey_fab_qnty"] += $vals[csf("grey_fab_qnty")];

       $sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("gmts_item_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("determination_id")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["delivery_date"] = change_date_format($vals[csf("delivery_date")]);

       $sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("gmts_item_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("determination_id")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["fabric_source"] = $vals[csf("fabric_source")];

       $sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("gmts_item_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("determination_id")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["yarn_dtls"] = $vals[csf("yarn_dtls")];

       $sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("gmts_item_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("determination_id")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["uom_id"] = $vals[csf("uom_id")];
       
       $sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("gmts_item_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("determination_id")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["dia"] = $vals[csf("dia")];

       $sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("gmts_item_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("determination_id")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["width_dia_id"] = $vals[csf("width_dia_id")];

       $sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("gmts_item_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("determination_id")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["remarks"] = $vals[csf("remarks_ra")];
       
       $sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("gmts_item_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("determination_id")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["color_type_id"] = $vals[csf("color_type_id")];

       $sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("gmts_item_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("determination_id")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["fabric_description"] = $vals[csf("fabric_description")];

       $sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("gmts_item_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("determination_id")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["gmts_item_id"] = $vals[csf("gmts_item_id")];

       $sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("gmts_item_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("determination_id")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["determination_id"] = $vals[csf("determination_id")];

       $sql_fab_arr[$article_no][$vals[csf("sample_name")]][$vals[csf("gmts_item_id")]][$vals[csf("color_id")]][$vals[csf("body_part_id")]][$vals[csf("determination_id")]][$vals[csf("color_type_id")]][$vals[csf("gsm")]][$vals[csf("dia")]][$vals[csf("width_dia_id")]][$contrast]["dtls_id"] = $vals[csf("id")].',';

       $fab_idArr[$vals[csf("id")]]=$vals[csf("id")];
       array_push($determination_id_arr,$vals[csf('determination_id')]);

    }
       //  echo '<pre>'; print_r($sql_fab_arr);die; 
       $nameArray_imge =sql_select("SELECT b.id,a.image_location FROM common_photo_library a,sample_development_fabric_acc b where  b.id= nvl(a.master_tble_id,0) and a.file_type=1 and a.form_name='required_fabric_1' and b.id in(".implode(",",$fab_idArr).") ");
       foreach($nameArray_imge as $row)
       {
           $fab_imgArr[$row[csf("id")]]=$row[csf("image_location")];
       }
       unset($nameArray_imge);

        $sql_d = "SELECT b.fabric_composition_name, a.id, a.construction FROM lib_yarn_count_determina_mst a left join lib_fabric_composition b on a. fabric_composition_id = b.id AND b.status_active = 1 AND b.is_deleted = 0 WHERE a.status_active = 1 AND a.is_deleted = 0  $determination_id_cond";
        $determina_arr = sql_select($sql_d);
        $determina_data_arr=array();
        foreach ($determina_arr as $row)
        {
           
           $determina_data_arr[$row[csf('id')]].=$row[csf('fabric_composition_name')]."***";
           $construction_data_arr[$row[csf('id')]].=$row[csf('construction')]."***";
           
        }
        unset($determina_arr);

        $sample_item_wise_span=array(); $sample_item_wise_color_span=array();

        foreach($sql_fab_arr as $article_no=>$article_data) 
        {
            $article_no_span=0;
            foreach($article_data as $sample_type_id=>$sampleType_data) 
            {
                $sample_type_span=0;
                foreach($sampleType_data as $gmts_item_id=>$gmts_item_data)
                {
                    $sample_item_span=0;
                    foreach($gmts_item_data as $gmts_color_id=>$gmts_color_data)
                    {
                        $sample_span=0;
                        foreach($gmts_color_data as $body_part_id=>$body_part_data)
                        {
                            foreach($body_part_data as $fab_id=>$fab_desc_data)
                            {
                                foreach($fab_desc_data as $colorType=>$colorType_data)
                                {
                                    foreach($colorType_data as $gsm_id=>$gsm_data)
                                    {
                                        foreach($gsm_data as $dia_id=>$dia_data)
                                        {
                                                foreach($dia_data as $dia_type_id=>$diatype_data)
                                                {
                                                foreach($diatype_data as $contrast_id=>$value)
                                                {
                                                    $sample_span++;$sample_type_span++;$article_no_span++;$sample_item_span++;
                                                }
                                                $article_wise_span[$article_no]=$article_no_span;
                                                $sample_item_wise_span[$article_no][$sample_type_id]=$sample_type_span;
                                                $gmts_item_wise_span[$article_no][$sample_type_id][$gmts_item_id]=$sample_item_span;
                                                $sample_item_wise_color_span[$article_no][$sample_type_id][$gmts_item_id][$gmts_color_id]=$sample_span;
                                                }
                                        }
                                    }
                                }
                            }
                            }
                        }
                }
            }
        }

       ?>
        <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
           <thead>
           <tr>
               <th colspan="19">Required Fabric</th>
           </tr>
               <tr>
                   <th width="30">Sl</th>
                   <th width="110">Sample Type</th>
                   <th width="60">Garments Item</th>
                   <th width="80">Gmt Color</th>
                   <th width="80">ALT/Style Name</th>
                   <th width="120">Body Part</th>
                   <th width="200">Fabric Desc & Composition</th>
                   <th width="80">Color Type</th>
                   <th width="80">Fab.Color</th>
                   <th width="55">GSM</th>
                   <th width="55">Dia</th>
                   <th width="60">Dia Type</th>
                   <th width="40">UOM</th>
                   <th width="80">Fin Fab Qnty</th>
                   <th width="40">P. Loss</th>
                   <th width="60">Grey Qnty</th>
                   <th width="80">Fabric Source</th>
                   <th width="80">Image</th>
                   <th width="80">Yarn Details</th>
                   <th width="80">Remarks</th>

               </tr>
           </thead>
           <tbody>
            <?
            $p=1;$total_finish=0;$total_grey=0;$total_process=0;$total_processloss_kg =0;$total_processloss_yds = 0;
            foreach($sql_fab_arr as $article_no=>$article_data) 
            {
                $aa = 0;
                foreach($article_data as $sample_type_id=>$sampleType_data) 
                {
                    $nn=0;
                    foreach($sampleType_data as $gmts_item_id=>$gmts_item_data)
                    {
                        $gi = 0;
                        foreach($gmts_item_data as $gmts_color_id=>$gmts_color_data)
                        {
                            $cc=0;
                            foreach($gmts_color_data as $body_part_id=>$body_part_data)
                            {	        			
                                foreach($body_part_data as $fab_id=>$fab_desc_data)
                                {
                                    foreach($fab_desc_data as $colorType=>$colorType_data)
                                    {
                                        foreach($colorType_data as $gsm_id=>$gsm_data)
                                        {
                                            foreach($gsm_data as $dia_id=>$dia_data)
                                            {
                                                // $total_processloss_kg = 0;
                                                foreach($dia_data as $dia_type=>$diatype_data)
                                                {
                                                   
                                                    foreach($diatype_data as $contrast_id=>$value)
                                                    {														 
                                                        ?>
                                                        <tr>
                                                        <?
                                                        $dtls_id = rtrim($value["dtls_id"], ',');
                                                        $dtls_Arr = array_unique(explode(',',$dtls_id));
                                                        $compo = implode(",", array_unique(explode("***", chop($determina_data_arr[$value['determination_id']], "***"))));
                                                        $constr = implode(",", array_unique(explode("***", chop($construction_data_arr[$value['determination_id']], "***"))));	

                                                            if($aa==0)
                                                            {
                                                                ?>
                                                                <td rowspan="<?= $article_wise_span[$article_no];?>" align="center" style="word-wrap: break-word;word-break: break-all;"><?= $p;$p++;?></td>
                                                                <?
                                                            }
                                                            if($nn==0)
                                                            {
                                                                ?>
                                                                <td rowspan="<?= $sample_item_wise_span[$article_no][$sample_type_id];?>" align="center"><?= $sample_library[$sample_type_id]; ?></td>													
                                                                <?														
                                                            }
                                                            if($gi==0)
                                                            {
                                                                ?>
                                                                <td rowspan="<?= $gmts_item_wise_span[$article_no][$sample_type_id][$gmts_item_id];?>" align="center"><?= $garments_item[$gmts_item_id];?></td>
                                                                <?
                                                            }
                                                            if($cc==0)
                                                            {
                                                            ?>
                                                                <td align="center" rowspan="<?= $sample_item_wise_color_span[$article_no][$sample_type_id][$gmts_item_id][$gmts_color_id];?>"><?= $color_library[$gmts_color_id];?> </td>
                                                                <td rowspan="<?= $sample_item_wise_color_span[$article_no][$sample_type_id][$gmts_item_id][$gmts_color_id];?>" align="center" ><?= $value["delivery_date"];?> </td>
                                                                <?
                                                            }
                                                            $fab_desc=$fab_constructArr[$fab_id].','.$fabric_composition_arr[$lip_yarn_count[$fab_id]];
                                                            ?> 
                                                            <td width="120" align="center"><?= $body_part[$body_part_id];?></td>
                                                            <td align="center"><? if($compo!=""){echo $constr.','.$compo;} else{echo $constr;}?></td>

                                                            <td align="center"><?= $color_type[$colorType]; ?></td>

                                                            <td align="center"><? 
                                                            if($contrast_id!=""){
                                                            echo $contrast_id;
                                                            }else{
                                                            echo $color_library[$gmts_color_id];
                                                            }
                                                            ?>
                                                            </td>
                                                            <td align="center"><?= $gsm_id; ?></td>
                                                            <td align="center"><?= $value["dia"]; ?></td>
                                                            <td align="center"><?= $fabric_typee[$dia_type]; ?></td>
                                                            <td align="center"><?= $unit_of_measurement[$value["uom_id"]];?></td>
                                                            <td align="right"><?= number_format($value["qnty"],2);?></td>
                                                            <td align="right"><?= $value["process_loss_percent"];?></td>
                                                            <td align="right"><?= number_format($value["grey_fab_qnty"],2);?></td>
                                                            <td align="center"><?= $fabric_source[$value["fabric_source"]];?></td>
                                                            <td style="word-break:break-all"><?  $path='../../';
                                                                foreach($dtls_Arr as $img)
                                                            { if($fab_imgArr[$img]!=''){ ?>
                                                            <b> <img src="<?= $path.$fab_imgArr[$img]; ?>" width="45" height="auto" border="1" /></b>														
                                                            <?  }} ?></td>
                                                            <td align="center"><?= $value["yarn_dtls"]; ?></td>
                                                            <td align="center"><?= $value["remarks"];?></td>
                                                        </tr>
                                                        <?
                                                        $nn++;$cc++;$aa++;$gi++;
                                                        if($value["uom_id"]==12){
                                                            $total_finish += $value["qnty"];
                                                            $total_grey += $value["grey_fab_qnty"];
                                                            $total_processloss_kg += $value["process_loss_percent"];
                                                            $total_processloss_kg_arr[$value["process_loss_percent"]] .= $value["process_loss_percent"].",";
                                                            
                                                        }else{
                                                            $total_yds_finish += $value["qnty"];
                                                            $total_yds_grey += $value["grey_fab_qnty"];
                                                            $total_processloss_yds += $value["process_loss_percent"];
                                                            $total_processloss_yds_arr[$value["process_loss_percent"]] .= $value["process_loss_percent"].",";
                                                        }
                                                        
                                                    }
                                                    
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        $avg_processloss_kg = count(explode(",", chop(implode("", $total_processloss_kg_arr), ",")));
        $avg_processloss_yds = count(explode(",", chop(implode("", $total_processloss_yds_arr), ",")));
        ?>
        <tr>
            <th colspan="13" align="right"><b>Total</b></th>
            <th width="60" align="right"><?= number_format($total_finish, 2);?>(KG)</th>
            <th width="40" align="right"><?= number_format($total_processloss_kg/$avg_processloss_kg, 2);?></th>
            <th width="80" align="right"><?= number_format($total_grey, 2);?>(KG)</th>
            <th width="80" colspan="5"> </th>
        </tr>
        <tr>
            <th colspan="13" align="right"><b>Total</b></th>
            <th width="60" align="right"><?= number_format($total_yds_finish, 2);?>(YDS)</th>
            <th width="40" align="right"><?= number_format($total_processloss_yds, 2)/$avg_processloss_yds;?></th>
            <th width="80" align="right"><?= number_format($total_yds_grey, 2);?>(YDS)</th>
            <th width="80" colspan="5"> </th>
        </tr>
     </tbody>
</table> <br/>
    <?
    $sample_color_arr = return_library_array( "SELECT id, sample_color FROM sample_development_dtls", "id", "sample_color"  );
    $sql_qry = "SELECT id,sample_mst_id,sample_name,gmts_item_id,smv,article_no,sample_color,sample_prod_qty,submission_qty,delv_start_date,delv_end_date,sample_charge,sample_curency,sent_to_buyer_date,comments FROM sample_development_dtls WHERE status_active =1 and is_deleted=0 and entry_form_id=203 and sample_mst_id='$id' ORDER BY id ASC";

    $sql_qry_color = "SELECT a.id,a.sample_mst_id,a.sample_name,a.gmts_item_id,a.smv,a.article_no,a.sample_color,a.sample_prod_qty,a.submission_qty,a.delv_start_date,a.delv_end_date,a.sample_charge,a.sample_curency,a.sent_to_buyer_date,a.comments,c.dtls_id,c.size_id,c.bh_qty,c.self_qty,c.test_qty,c.plan_qty,c.dyeing_qty,c.samp_dept_qty,c.test_fit_qty,c.others_qty FROM sample_development_dtls a,sample_development_size c WHERE a.id=c.dtls_id and a.status_active =1 and a.is_deleted=0 and a.entry_form_id=203 and a.sample_mst_id='$id' ORDER BY a.id ASC";

    $size_type_arr = array(1=>"BH Qty", 2=>"Plan Qty", 3=>"Dyeing Qty", 4=>"Test Qty", 5=>"Self Qty", 6=>"Samp. Qty", 7=>"Test Fit Qty", 8=>"Others Qty");
    $color_size_arr = array();
    foreach(sql_select($sql_qry_color) as $vals)
    {
       if($vals[csf("bh_qty")]>0)
       {
       $color_size_arr[1][$vals[csf("size_id")]]='Bh Qty';
       $color_size_dtls_qty_arr[1][$vals[csf("id")]][$vals[csf("size_id")]] = $vals[csf("bh_qty")];
       }
       if($vals[csf("self_qty")]>0)
       {
       $color_size_arr[2][$vals[csf("size_id")]]='self qty';
       $color_size_dtls_qty_arr[2][$vals[csf("id")]][$vals[csf("size_id")]] = $vals[csf("self_qty")];
       }
       if($vals[csf("test_qty")]>0)
       {
       $color_size_arr[3][$vals[csf("size_id")]]='test qty';
       $color_size_dtls_qty_arr[3][$vals[csf("id")]][$vals[csf("size_id")]] = $vals[csf("test_qty")];
       }
       if($vals[csf("plan_qty")]>0)
       {
       $color_size_arr[4][$vals[csf("size_id")]]='plan qty';
       $color_size_dtls_qty_arr[4][$vals[csf("id")]][$vals[csf("size_id")]] = $vals[csf("plan_qty")];
       }
       if($vals[csf("dyeing_qty")]>0)
       {
       $color_size_arr[5][$vals[csf("size_id")]]='Dyeing qty';
       $color_size_dtls_qty_arr[5][$vals[csf("id")]][$vals[csf("size_id")]] = $vals[csf("dyeing_qty")];
       }
       if($vals[csf("samp_dept_qty")]>0)
       {
       $color_size_arr[6][$vals[csf("size_id")]]='Samp. Dept';
       $color_size_dtls_qty_arr[6][$vals[csf("id")]][$vals[csf("size_id")]] = $vals[csf("samp_dept_qty")];
       }
       if($vals[csf("test_fit_qty")]>0)
       {
       $color_size_arr[7][$vals[csf("size_id")]]='Test Fit';
       $color_size_dtls_qty_arr[7][$vals[csf("id")]][$vals[csf("size_id")]] = $vals[csf("test_fit_qty")];
       }
       if($vals[csf("others_qty")]>0)
       {
       $color_size_arr[8][$vals[csf("size_id")]]='Others';
       $color_size_dtls_qty_arr[8][$vals[csf("id")]][$vals[csf("size_id")]] = $vals[csf("others_qty")];
       }

   }
   $tot_row=count($color_size_arr);
   $result=sql_select($sql_qry);
?>
            <table align="left" cellspacing="0" border="1" width="100%" class="rpt_table" rules="all">
                <thead>
                    <tr>
                    <td width="150" colspan="<? echo 13+$tot_row;?>" align="center"><strong>Sample Details</strong></td>
                </tr>
                <tr>
                        <th width="30" rowspan="2" align="center">Sl</th>
                        <th width="100" rowspan="2" align="center">Sample Name</th>
                        <th width="120" rowspan="2" align="center">Garment Item</th>
                        <th width="55" rowspan="2" align="center">ALT/Style Name</th>
                        <th width="70" rowspan="2" align="center">Color</th>
                        <?
                        $tot_row_td=0;
                        foreach($color_size_arr as $type_id=>$val)
                        { 
                        ?>
                            <th width="45" align="center" colspan="<?= count($val);?>"><?= $size_type_arr[$type_id];?></th>
                        <?
                        }
                        ?>
                        <th rowspan="2" width="55" align="center">Total</th>
                        <th rowspan="2" width="55" align="center">Submn Qty</th>
                        <th rowspan="2" width="70" align="center">Buyer Submisstion Date</th>
                        <th rowspan="2" width="70">Image</th>
                        <th rowspan="2" width="70" align="center">Remarks</th>
                    </tr>
                    <tr>
                        <?
                        foreach($color_size_arr as $type_id=>$data_size)
                        {
                                foreach($data_size as $size_id=>$data_val)
                            {
                            $tot_row_td++;
                            ?>
                                <th width="40" align="center"><?= $size_library[$size_id]; ?></th>
                                <?
                            }
                        }
                        ?>
                    </tr>
                </thead>
                <tbody>
                    <?
                    $i=1;$k=0;
                    $gr_tot_sum=0;
                    $gr_sub_sum=0;
                    foreach($result as $row)
                    {
                        $dtls_ids = $row[csf('id')];
                        $prod_sum = $prod_sum+$row[csf('sample_prod_qty')];
                        $sub_sum = $sub_sum+$row[csf('submission_qty')];
                    ?>
                    <tr>
                        <td align="center"><?= $k++;?></td>
                        <td align="left"><?= $sample_library[$row[csf('sample_name')]];?></td>
                        <td align="left"><?= $garments_item[$row[csf('gmts_item_id')]];?></td>
                        <td align="left"><?= $row[csf('article_no')];?></td>
                        <td width="70" align="left"><?= $color_library[$row[csf('sample_color')]];?></td>
                        <?
                        $total_sizes_qty=0;
                        $total_sizes_qty_subm=0;
                            foreach($color_size_arr as $type_id=>$data_size)
                        {
                            foreach($data_size as $size_id=>$data_val)
                            {
                            $size_qty=$color_size_dtls_qty_arr[$type_id][$dtls_ids][$size_id];
                            ?>
                            <td align="right"><?= $size_qty; ?></td>
                            <?
                                if($type_id == 1)
                                {
                                $total_sizes_qty_subm += $size_qty;
                                }
                                $total_sizes_qty += $size_qty;
                            }
                        }
                        ?>
                        <td align="right"><?= $total_sizes_qty;?></td>
                        <td align="right"><?= $row[csf('submission_qty')];?></td>
                        <td   align="left"><?= change_date_format($row[csf('sent_to_buyer_date')]);?> </td>
                        <td align="middle"><? 
                            $img_ref_id= $dtls_ids;
                            $sam_req_img = $sam_img_arr[$img_ref_id]['img'];
                            ?>
                            <img src='../../<?= $sam_req_img; ?>' height='50' width='70'/>
                        </td>
                        <td align="left"><?= $row[csf('comments')];?> </td>
                        <?
                        $gr_tot_sum+=$total_sizes_qty;
                            $gr_sub_sum+=$row[csf('submission_qty')];
                    }
                    ?>
                    </tr>
                    <tr>
                        <td colspan="<? echo 5+$tot_row_td; ?>" align="right"><b>Total</b></td>
                        <td align="right"><b><?= number_format($gr_tot_sum,2);?> </b></td>
                        <td align="right"><b><?= number_format($gr_sub_sum,2);?> </b></td>
                        <td colspan="3"></td>
                    </tr>
                </tbody>
                <tfoot></tfoot>
            </table>
        </td>
    </tr>
    <tr><td colspan="6">&nbsp;</td></tr>
    <tr>
        <td width="250" align="left" valign="top" colspan="2"></td>
    </tr>
    <tr>
        <td width="250" align="left" valign="top" colspan="2">
        <table align="left" cellspacing="0" border="1" width="100%" class="rpt_table" rules="all">
            <thead>
                <tr>
                    <td width="150" colspan="10" align="center"><strong>Required Accessories</strong></td>
                </tr>
                <tr>
                    <th width="30" align="center">Sl</th>
                    <th width="100" align="center">Sample Name</th>
                    <th width="120" align="center">Garment Item</th>
                    <th width="100" align="center">Trims Group</th>
                    <th width="100" align="center">Description</th>
                    <th width="100" align="center">Supplier</th>
                    <th width="100" align="center">Brand/Supp.Ref</th>
                    <th width="30" align="center">UOM</th>
                    <th width="30" align="center">Req/Dzn </th>
                    <th width="30" align="center">Req/Qty </th>
                    <th width="80" align="center">Acc.Sour. </th>
                    <th width="100" align="center">Acc Delivery Date </th>
                    <th width="80" align="center">Remarks </th>
                </tr>
            </thead>
            <tbody>
                <?
                $sql_qryA = "SELECT id,sample_mst_id,sample_name_ra,gmts_item_id_ra,trims_group_ra,description_ra,brand_ref_ra,uom_id_ra,req_dzn_ra,req_qty_ra,remarks_ra,delivery_date,supplier_id,fabric_source FROM sample_development_fabric_acc WHERE status_active =1 and is_deleted=0 and form_type=2 and sample_mst_id='$id' ORDER BY id ASC";

                $resultA=sql_select($sql_qryA);
                $i = 1;$k = 0;
                $req_dzn_ra = 0;
                $req_qty_ra = 0;
                foreach($resultA as $rowA)
                {
                    $req_dzn_ra=$req_dzn_ra+$rowA[csf('req_dzn_ra')];
                    $req_qty_ra=$req_qty_ra+$rowA[csf('req_qty_ra')];
                    ?>
                    <tr>
                    <?
                    $k++;
                    ?>
                    <td align="center"><?= $k;?></td>
                    <td align="left"><?= $sample_library[$rowA[csf('sample_name_ra')]];?></td>
                    <td align="left"><?= $garments_item[$rowA[csf('gmts_item_id_ra')]];?></td>
                    <td align="left"><?= $trims_group_lib[$rowA[csf('trims_group_ra')]];?></td>
                    <td align="left"><?= $rowA[csf('description_ra')];?></td>
                    <td align="left"><?= $supplier_library[$rowA[csf('supplier_id')]];?></td>
                    <td align="left"><?= $rowA[csf('brand_ref_ra')];?></td>
                    <td align="center"><?= $unit_of_measurement[$rowA[csf('uom_id_ra')]];?></td>
                    <td align="right"><?= $rowA[csf('req_dzn_ra')];?></td>
                    <td align="right"><?= $rowA[csf('req_qty_ra')];?></td>
                    <td align="left"><?= $fabric_source[$rowA[csf('fabric_source')]];?></td>
                    <td align="left"><?= change_date_format($rowA[csf('delivery_date')]);?></td>
                    <td align="left"><?= $rowA[csf('remarks_ra')];?></td>
                    <?
                    }
                    ?>
                    </tr>
                    <tr>
                        <td colspan="9" align="right"><b>Total </b></td>
                        <td align="right"><b><?= number_format($req_qty_ra,2);?> </b></td>
                        <td colspan="3">&nbsp;</td>
                    </tr>
                </tbody>
                <tfoot></tfoot>
            </table>
        </td>
    </tr>
    <tr> <td colspan="6">&nbsp;</td></tr>
    <tr>
       <td width="250" align="left" valign="top" colspan="2">
            <table align="left" cellspacing="0" border="1" width="100%" class="rpt_table" rules="all">
                <thead>
                    <tr>
                            <td width="150" colspan="6" align="center"><strong>Required Emebellishment</strong></td>
                        </tr>
                        <tr>
                            <th width="30" align="center">Sl</th>
                            <th width="100" align="center">Sample Name</th>
                            <th width="110" align="center">Garment Item</th>
                            <th width="110" align="center">Body Part</th>
                            <th width="100" align="center">Supplier</th>
                            <th width="60" align="center">Name</th>
                            <th width="70" align="center">Type</th>
                            <th width="100" align="center">Emb.Del.Date</th>
                            <th width="70" align="center">Remarks</th>
                            </tr>
                </thead>
           <tbody>
                <?
                $sql_qry = "SELECT id,sample_mst_id,sample_name_re,gmts_item_id_re,name_re,type_re,remarks_re,body_part_id,delivery_date,supplier_id FROM sample_development_fabric_acc WHERE sample_mst_id='$id' and form_type=3 and is_deleted=0  and status_active=1 order by id asc";
                $result = sql_select($sql_qry);
                $k=0;
                $type_array = array(1=>$emblishment_print_type, 2=>$emblishment_embroy_type, 3=>$emblishment_wash_type, 4=>$emblishment_spwork_type, 5=>$emblishment_gmts_type);
               foreach($result as $row)
               {
               ?>
               <tr>
                   <?
                    $k++;
                   ?>
                   <td  align="center"><?= $k;?></td>
                   <td  align="left"><?= $sample_library[$row[csf('sample_name_re')]];?></td>
                   <td  align="left"><?= $garments_item[$row[csf('gmts_item_id_re')]];?></td>
                   <td  align="left"><?= $body_part[$row[csf('body_part_id')]];?></td>
                   <td  align="left"><?= $supplier_library[$row[csf('supplier_id')]];?></td>
                   <td  align="left"><?= $emblishment_name_array[$row[csf('name_re')]];?></td>
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
                   <td  align="left"><? echo change_date_format($row[csf('delivery_date')]);?></td>
                   <td  align="left"><? echo $row[csf('remarks_re')];?></td>
                <?
                }
               ?>
               </tr>
           </tbody>
      </table>
      <br>
      <table>
            <tr>
                <td>
                    <table  style="margin-top: 10px;" class="rpt_table" width="625" align="left" border="1" cellpadding="0" cellspacing="0" rules="all">
                        <caption> <b> Yarn Required Summary </b> </caption>
                            <thead>
                                <tr align="center">
                                    <th width="40">Sl</th>
                                    <th>Yarn Desc.</th>
                                    <th>Req. Qty</th> 
                                </tr>
                              </thead>
                              <tbody>
                              <?
                              $lib_yarn_count = return_library_array("select yarn_count,id from lib_yarn_count", "id", "yarn_count");
                              $lib_supllier_arr = return_library_array("select supplier_id,booking_no from wo_non_ord_samp_booking_mst where booking_no='$booking_no' and entry_form_id=203", "booking_no", "supplier_id");
                              $tot_req_qty=0; 
                               
                              $data_array = sql_select("SELECT b.booking_no, b.determin_id, b.count_id, b.copm_one_id, b.percent_one, b.type_id, sum (b.cons_qnty) as cons_qnty FROM  sample_development_yarn_dtls b WHERE b.status_active=1  and b.mst_id='$id' and b.determin_id in (SELECT determination_id FROM sample_development_fabric_acc WHERE status_active=1 and sample_mst_id='$id' and form_type=1) GROUP BY b.booking_no,b.determin_id,b.count_id,b.copm_one_id,b.percent_one,b.type_id");
  
                                if ( count($data_array)>0)
                                {
                                    $l=1;
                                    foreach( $data_array as $key=>$row )
                                    {
                                        $yarn_des=$lib_yarn_count[$row[csf("count_id")]].','.$composition[$row[csf("copm_one_id")]].','.$row[csf("percent_one")].'%,'.$yarn_type[$row[csf("type_id")]];
                                        ?>
                                            <tr>
                                                <td> <? echo $l;?> </td>
                                                <td> <? echo $yarn_des; ?> </td>
                                                <td align="right"> <? echo number_format($row[csf("cons_qnty")],2); ?> </td>
                                            </tr>
                                        <?
                                        $l++;
                                        $tot_req_qty+=$row[csf("cons_qnty")];
                                    }
                                }
                              ?>
                            <tr>
                                <th  colspan="2" align="right"><b>Total</b></th>
                                <th  align="right"><? echo number_format($tot_req_qty,2);?></th>
                            </tr>
                        </tbody>
                    </table>
                </td> 
            </tr>
        </table>

    <?
    $sample_stripe_data = sql_select("SELECT a.body_part_id, b.contrast, b.color_id, b.grey_fab_qnty,c.id as strip_mst_id, c.stripe_color, c.measurement, c.uom, c.fabreq, c.yarn_dyed,c.totfidder FROM sample_development_fabric_acc a JOIN sample_development_rf_color b ON a.id=b.dtls_id JOIN wo_sample_stripe_color c ON a.id=c.sample_fab_dtls_id and b.color_id=c.color_number_id where a.status_active=1 and a.is_deleted=0 and a.color_type_id in (2,3,4,6,31,32,33,34) and a.form_type=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.sample_mst_id=$id");
   
    foreach ($sample_stripe_data as $row) {
       $key=$row[csf('body_part_id')].'*'.$row[csf('color_id')];
       $sample_stripe_arr[$key]['body_part_id'] = $row[csf('body_part_id')];
       $sample_stripe_arr[$key]['fabric_color'] = $row[csf('color_id')];
       $sample_stripe_arr[$key]['fabric_qty'] = $row[csf('grey_fab_qnty')];
       
       $sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['color'] = $row[csf('stripe_color')];
       $sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['measurement'] = $row[csf('measurement')];
       $sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['uom'] = $row[csf('uom')];
       $sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['qty'] = $row[csf('fabreq')];
       $sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['yarn_dyed'] = $row[csf('yarn_dyed')];
       $sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['totfidder'] = $row[csf('totfidder')];
       $stripe_color_summ[$row[csf('stripe_color')]] += $row[csf('fabreq')];
    }
  
    $coller_cuff_data = sql_select("SELECT a.sample_color, a.size_id, a.item_size, a.qnty_pcs, c.body_part_type FROM sample_requisition_coller_cuff a JOIN sample_development_fabric_acc b ON b.id=a.dtls_id JOIN lib_body_part c on b.body_part_id=c.id WHERE a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.sample_mst_id=$id"); 
    $coller_data_arr = array(); $cuff_data_arr = array();
    foreach ($coller_cuff_data as $row) {
        if($row[csf('body_part_type')] == 40)
        {
           $coller_size_arr[$row[csf('size_id')]]=$row[csf('size_id')];
           $color_size_data[$row[csf('size_id')]]['item_size']=$row[csf('item_size')];
           $color_color_data[$row[csf('sample_color')]][$row[csf('size_id')]]['qnty_pcs'] = $row[csf('qnty_pcs')];
        }
        if($row[csf('body_part_type')] == 50)
        {
           $cuff_size_arr[$row[csf('size_id')]] = $row[csf('size_id')];
           $cuff_size_data[$row[csf('size_id')]]['item_size'] = $row[csf('item_size')];
           $cuff_color_data[$row[csf('sample_color')]][$row[csf('size_id')]]['qnty_pcs'] = $row[csf('qnty_pcs')];
        }
    } 
    ?>
    <div style="width:1000px; margin-top: 10px;">
    <?
    $collar_cuff_percent_arr=array(); $collar_cuff_body_arr=array(); $collar_cuff_color_arr=array(); $collar_cuff_size_arr=array(); $collar_cuff_item_size_arr=array(); $color_size_sensitive_arr=array();

    $collar_cuff_sql= "SELECT b.id, b.gmts_item_id as item_number_id, a.qnty_pcs,a.sample_color as color_number_id, a.size_id as gmts_sizes, a.item_size, a.size_id as size_number_id,  e.body_part_full_name, e.body_part_type
    FROM sample_requisition_coller_cuff a LEFT JOIN lib_size s on a.size_id=s.id, sample_development_fabric_acc b, lib_body_part  e 
    WHERE b.id=a.dtls_id   and b.body_part_id=e.id and e.body_part_type in (40,50)  and b.sample_mst_id=$id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.status_active=1 and e.is_deleted=0 ORDER BY  b.id,a.sample_color,s.sequence";
    //echo $collar_cuff_sql;
    $collar_cuff_sql_res = sql_select($collar_cuff_sql);
    $itemIdArr = array();

    foreach($collar_cuff_sql_res as $collar_cuff_row) 
    {
        $collar_cuff_percent_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('color_number_id')]][$collar_cuff_row[csf('gmts_sizes')]] = $collar_cuff_row[csf('colar_cuff_per')];
        $collar_cuff_body_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]] = $collar_cuff_row[csf('body_part_full_name')];
        $collar_cuff_size_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('size_number_id')]] = $collar_cuff_row[csf('size_number_id')];
        if(!empty($collar_cuff_row[csf('item_size')]))
        {
            $collar_cuff_item_size_arr[$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('size_number_id')]][$collar_cuff_row[csf('item_size')]] = $collar_cuff_row[csf('item_size')];
        }
        
        $color_size_sensitive_arr[$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('id')]][$collar_cuff_row[csf('color_number_id')]] = $collar_cuff_row[csf('qnty_pcs')];
        // $collar_cuff_size_Qty_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('id')]][$collar_cuff_row[csf('size_number_id')]]=$collar_cuff_row[csf('qnty_pcs')];

        $collar_cuff_size_Qty_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('id')]][$collar_cuff_row[csf('color_number_id')]][$collar_cuff_row[csf('size_number_id')]] = $collar_cuff_row[csf('qnty_pcs')];
        
        $itemIdArr[$collar_cuff_row[csf('body_part_type')]] .= $collar_cuff_row[csf('item_number_id')].',';
    }
    unset($collar_cuff_sql_res);
    foreach($collar_cuff_body_arr as $body_type => $body_name)
    {
        $gmtsItemId = array_filter(array_unique(explode(",", $itemIdArr[$body_type])));
        foreach($body_name as $body_val)
        {
            $count_collar_cuff = count($collar_cuff_size_arr[$body_type][$body_val]);
            $pre_grand_tot_collar = 0; $pre_grand_tot_collar_order_qty = 0;
            ?>
            <div style="max-height:1330px; overflow:auto; float:left; padding-top:5px; margin-left:5px; margin-bottom:5px; position:relative;font-size:18px;">
            <table width="625" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                <tr>
                    <td colspan="<? echo $count_collar_cuff+3; ?>" align="center"><b><? echo $body_val; ?> - Color Size Brakedown in Pcs.</b></td>
                </tr>
                <tr>
                    <td width="100">Size</td>
                        <?
                        foreach($collar_cuff_size_arr[$body_type][$body_val]  as $size_number_id)
                        {
                            ?>
                            <td align="center" style="border:1px solid black"><strong><? echo $size_library[$size_number_id];?></strong></td>
                            <?
                        }
                        ?>
                    <td width="60" rowspan="2" align="center"><strong>Total</strong></td>
                </tr>
                <tr>
                    <td style="font-size:12px"><? echo $body_val; ?> Size</td>
                    <?
                    foreach($collar_cuff_item_size_arr[$body_val]  as $size_number_id=>$size_number)
                    {
                        if(count($size_number)>0)
                        {
                            foreach($size_number  as $item_size=>$val)
                            {
                            ?>
                            <td align="center" style="border:1px solid black"><strong><? echo $item_size;?></strong></td>
                            <?
                            }
                        }
                        else
                        {
                            ?>
                            <td align="center" style="border:1px solid black"><strong> &nbsp;</strong></td>
                            <?
                        }
                    }
                    ?>
                </tr>
                    <?
                    $pre_size_total_arr=array();
                    foreach($color_size_sensitive_arr[$body_val] as $fab_req_id=>$pre_cost_data)
                    {
                        foreach($pre_cost_data as $color_number_id=>$color_number_data)
                        {
                            $pre_color_total_collar=0;
                            $pre_color_total_collar_order_qnty=0;
                                
                            ?>
                            <tr>
                                <td>
                                    <?
                                    
                                        echo $color_library[$color_number_id];
                                    ?>
                                </td>
                                <?
                                foreach($collar_cuff_size_arr[$body_type][$body_val] as $size_number_id)
                                {
                                    ?>
                                    <td align="center" style="border:1px solid black">
                                        <?   $collerqty=0;  
                                        $color_cuff_cut=0;
                                        // $color_cuff_cut=$collar_cuff_size_Qty_arr[$body_type][$body_val][$fab_req_id][$size_number_id];
                                        $color_cuff_cut=$collar_cuff_size_Qty_arr[$body_type][$body_val][$fab_req_id][$color_number_id][$size_number_id];
                                        if($body_type==50){
                                            // $collerqty=$color_cuff_cut*2;
                                            $collerqty=$color_cuff_cut;
                                        }else{
                                            $collerqty=$color_cuff_cut;
                                        }
                                        echo number_format($collerqty);
                                        $pre_size_total_arr[$size_number_id]+=$collerqty;
                                        $pre_color_total_collar+=$collerqty;
                                        $pre_color_total_collar_order_qnty+=$color_cuff_cut;
                                        ?>
                                    </td>
                                    <?
                                }
                                ?>
                                <td align="center"><? echo number_format($pre_color_total_collar); ?></td>      
                            </tr>
                            <?
                            $pre_grand_collar_ex_per+=$collar_ex_per;
                            $pre_grand_tot_collar+=$pre_color_total_collar;
                            $pre_grand_tot_collar_order_qty+=$pre_color_total_collar_order_qnty;
                        }
                    }
                    ?>
                <tr>
                    <td>Size Total</td>
                        <?
                            foreach($collar_cuff_size_arr[$body_type][$body_val] as $size_number_id)
                            {
                                $size_qty=$pre_size_total_arr[$size_number_id];
                                ?>
                                <td style="border:1px solid black;  text-align:center"><? echo number_format($size_qty); ?></td>
                                <?
                            }
                        ?>
                    <td style="border:1px solid black; text-align:center"><? echo number_format($pre_grand_tot_collar); ?></td>
                    <? echo number_format((($pre_grand_tot_collar-$pre_grand_tot_collar_order_qty)/$pre_grand_tot_collar_order_qty)*100,2); ?>
                </tr>
            </table>
        </div>
    <?
    }
    }
    ?>
    <br>
    <?
    $sample_stripe_data = sql_select("SELECT a.body_part_id, b.contrast, b.color_id, b.grey_fab_qnty,c.id as strip_mst_id, c.stripe_color, c.measurement, c.uom, c.fabreq, c.yarn_dyed FROM sample_development_fabric_acc a join sample_development_rf_color b on a.id=b.dtls_id join wo_sample_stripe_color c on a.id=c.sample_fab_dtls_id and b.color_id=c.color_number_id where a.status_active=1 and a.is_deleted=0 and a.color_type_id in (2,3,4,6,31,32,33,34) and a.form_type=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.sample_mst_id=$id");
    
    foreach ($sample_stripe_data as $row) {
        $key=$row[csf('body_part_id')].'*'.$row[csf('color_id')];
        $sample_stripe_arr[$key]['body_part_id'] = $row[csf('body_part_id')];
        $sample_stripe_arr[$key]['fabric_color'] = $row[csf('color_id')];
        $sample_stripe_arr[$key]['fabric_qty'] = $row[csf('grey_fab_qnty')];
        $sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['color'] = $row[csf('stripe_color')];
        $sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['measurement'] = $row[csf('measurement')];
        $sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['uom'] = $row[csf('uom')];
        $sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['qty'] = $row[csf('fabreq')];
        $sample_stripe_arr[$key]['stripe_color'][$row[csf('strip_mst_id')]][$row[csf('stripe_color')]]['yarn_dyed'] = $row[csf('yarn_dyed')];
        $stripe_color_summ[$row[csf('stripe_color')]] += $row[csf('fabreq')];
    }
    ?>
    </div>
    <div style="width:1000px; ">
        <table align="left" cellspacing="0" border="1" style="width:800px;float: left; right; margin-top: 5px;font-size:14px" class="rpt_table" rules="all">
            <thead>
                <tr>
                    <th colspan="9">Stripe Details</th>
                </tr>
                <tr>
                    <th width="30">SL</th>
                    <th width="100">Body Part</th>
                    <th width="60">Fabric Color</th>
                    <th width="60">Fabric Qty(KG)</th>
                    <th width="60">Stripe Color</th>
                    <th width="60">Stripe Measurement</th>
                    <th width="60">Stripe Uom</th>
                    <th width="60">Qty.(KG)</th>
                    <th width="60">Y/D Req.</th>
                </tr>
            </thead>
            <tbody>
                <? $sl=1;
                foreach ($sample_stripe_arr as $sdata) {
                    $rowspan = count($sdata['stripe_color']);
                    $i=1;
                    foreach ($sdata['stripe_color'] as $stripe_mst) {
                        foreach ($stripe_mst as $stripe_data) {
                        if($i==1){
                            $total_fabric += $sdata['fabric_qty'];
                            $total_stripe_fabric += $stripe_data['qty'];
                        ?>
                        <tr>
                            <td rowspan="<?=$rowspan?>"><?= $sl; ?></td>
                            <td rowspan="<?=$rowspan?>"><?= $body_part[$sdata['body_part_id']]; ?></td>
                            <td rowspan="<?=$rowspan?>"><?= $color_library[$sdata['fabric_color']]; ?></td>
                            <td align="right" rowspan="<?=$rowspan?>"><?= $sdata['fabric_qty']; ?></td>
                            <td><?= $color_library[$stripe_data['color']]; ?></td>
                            <td align="right"><?= $stripe_data['measurement']; ?></td>
                            <td><?= $unit_of_measurement[$stripe_data['uom']]; ?></td>
                            <td align="right"><?= $stripe_data['qty']; ?></td>
                            <td><?= $yes_no[$stripe_data['yarn_dyed']]; ?></td>
                        </tr>
                        <?
                            $i++;
                        }
                        else{
                            $total_stripe_fabric += $stripe_data['qty'];
                            ?>
                                <tr>
                                    <td><?= $color_library[$stripe_data['color']]; ?></td>
                                    <td align="right"><?= $stripe_data['measurement']; ?></td>
                                    <td><?= $unit_of_measurement[$stripe_data['uom']]; ?></td>
                                    <td align="right"><?= $stripe_data['qty']; ?></td>
                                    <td><?= $yes_no[$stripe_data['yarn_dyed']]; ?></td>
                                </tr>
                            <?
                        }
                    }
                    $sl++;
                    }
                } ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3">Total</th>
                    <th align="right"><?= $total_fabric ?></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th align="right"><?= $total_stripe_fabric ?></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
        <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:180px; margin-left: 2px; float: right; right; margin-top: 5px;font-size:14px" rules="all">
            <thead>
                <tr>
                    <th colspan="3">Stripe Color wise Summary</th>
                </tr>
                <tr>
                    <th>SL</th>
                    <th>Stripe Color</th>
                    <th>Qty.(KG)</th>
                </tr>
            </thead>
            <tbody>
                <?
                $sl=1;
                foreach ($stripe_color_summ as $color_id => $value) 
                {
                    $total_fabric_qty+= $value;
                ?>
                <tr>
                    <td><?= $sl ?></td>
                    <td><?= $color_library[$color_id]; ?></td>
                    <td><?= $value ?></td>
                </tr>
                <? $sl++;
                } ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2">Total</th>
                    <th><?= $total_fabric_qty; ?></th>
                </tr>
            </tfoot>
        </table>
     </div>
        <br>
        <br> 
        <table  style="margin-top: 10px;" class="rpt_table" width="600" align="left" border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
               <tr>
                   <th align="left" width="40">Sl</th>
                   <th align="left" >Special Instruction</th>
               </tr>
            </thead>
            <tbody>
            <?
            $data_array=sql_select("select id, terms from  wo_booking_terms_condition where entry_form=140 and booking_no='$booking_no'");
            if ( count($data_array)>0)
            {
                $l=1;
                foreach( $data_array as $key=>$row )
                {
                    ?>
                        <tr  align="">
                            <td><?= $l;?> </td>
                            <td><?= $row[csf("terms")]; ?></td>
                        </tr>
                    <?
                    $l++;
                }
            }
            ?>
            </tbody>
        </table>
    </td>
</tr>
<tr> <td colspan="6">&nbsp;</td></tr>

<tr>
   <td width="810" align="left" valign="top" colspan="2" >
       <table align="left" cellspacing="0" width="810" class="rpt_table" >
           <tr>
               <td colspan="6">
                    <?
                    $user_id = $_SESSION['logic_erp']['user_id'];
                    $user_arr = return_library_array( "select id, USER_NAME from user_passwd where id=$user_id", "id", "USER_NAME");
                    $prepared_by = $user_arr[$user_id];
                    echo signature_table(134, $company_id, "1080px",$cbo_template_id,$padding_top = 70,$prepared_by);
                   ?>
               </td>
           </tr>
       </table>
   </td>
</tr>
</table>
 
</div>
      
    <?
	$message=ob_get_contents();
	ob_clean();

	$to='';
	$sql = "SELECT a.BRAND_IDS,a.BUYER_IDS,c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c WHERE b.mail_group_mst_id=a.id  and a.mail_item=b.MAIL_ITEM_MST and a.mail_item=143 and b.mail_user_setup_id=c.id and a.company_id =".$company_id."  and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1  and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
	// echo $sql;die;
	
	
	$mail_sql=sql_select($sql);
	$receverMailArr=array();
	foreach($mail_sql as $row)
	{
		$buyerArr=explode(',',$row['BUYER_IDS']);
		$brandArr=explode(',',$row['BRAND_IDS']);
		foreach($buyerArr as $buyerid){
			foreach($brandArr as $brandid){
				$receverMailArr[$buyerid][$brandid][$row[csf('email_address')]]=$row[csf('email_address')];
			}
		}
		
	}


	$mailDataArr[]=$user_mail_arr[$dataArray[0][csf("INSERTED_BY")]];
	$mailDataArr[]=$team_leader_mail_arr[$TEAM_LEADER];
	$mailDataArr[]=$dealing_merchant_mail_library[$DEALING_MARCHANT];
	$mailDataArr[]=implode(',',($receverMailArr[$buyer_id][$brand_id]));
	
	$to = implode(',',$mailDataArr);
 	
	$subject="Sample Requisition with Booking Knit";
	$header=mailHeader();
	echo $to.$message;	
		
		if($_REQUEST['isview']==1){
			echo $to.$message;
		}
		else{
			if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail,$att_file_arr );}
		}
 
?>  
 