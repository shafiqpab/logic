﻿<? 
/*-------------------------------------------- Comments
Version                  :  V1
Purpose			         : 	This form will create Sample Fabric Booking
Functionality	         :	
JS Functions	         :
Created by		         :	Monzu 
Creation date 	         : 	27-12-2012
Requirment Client        : 
Requirment By            : 
Requirment type          : 
Requirment               : 
Affected page            : 
Affected Code            :                   
DB Script                : 
Updated by 		         : 		
Update date		         : 		   
QC Performed BY	         :		
QC Date			         :	
Comments		         : From this version oracle conversion is start
*/
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$permission=$_SESSION['page_permission'];
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');

//---------------------------------------------------- Start---------------------------------------------------------------------------
$po_number=return_library_array( "select id,po_number from wo_po_break_down", "id", "po_number"  );
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );

if ($action=="load_drop_down_buyer"){
	if($data != 0)
    {
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$data and buy.id in (select  buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'requires/sample_booking_controller', this.value, 'load_drop_down_buyer_tag_sample', 'sample_td' );" );
	exit();
	}
	else{
		echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and buy.id in (select  buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'requires/sample_booking_controller', this.value, 'load_drop_down_buyer_tag_sample', 'sample_td' );" );
	exit();
	}
} 
if ($action=="load_drop_down_buyer_tag_sample"){
	echo create_drop_down( "cbo_sample_type", 130, "select a.id, a.sample_name from lib_sample a,lib_buyer_tag_sample b,lib_buyer c where b.buyer_id=c.id and  b.tag_sample=a.id  and b.buyer_id=$data and b.sequ>0 and a.is_deleted=0","id,sample_name", 1, "--Select--", $selected, "" ); 
exit();
} 

if ($action=="load_drop_down_suplier"){
	if($data==5 || $data==3){
	echo create_drop_down( "cbo_supplier_name", 130, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name",1, "-- Select Supplier --", "", "validate_suplier();get_php_form_data( this.value+'_'+document.getElementById('cbo_pay_mode').value, 'load_drop_down_attention', 'requires/sample_booking_controller');",0,"" );
	}
	else{
		echo create_drop_down( "cbo_supplier_name", 130, "select id,supplier_name from lib_supplier where status_active =1 and is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "get_php_form_data( this.value+'_'+document.getElementById('cbo_pay_mode').value, 'load_drop_down_attention', 'requires/sample_booking_controller');",0 );
	}
	exit();
}

if($action=="load_drop_down_attention"){
	$data=explode("_",$data);
	$supplier=$data[0];
	$paymode=$data[1];
	$supplier_name=return_field_value("contact_person","lib_supplier","id ='".$supplier."' and is_deleted=0 and status_active=1");
	if($paymode==1 || $paymode==2){
		echo "document.getElementById('txt_attention').value = '".$supplier_name."';\n";
	}
	else{
		echo "document.getElementById('txt_attention').value = '';\n";
	}
	exit();	
}

if($action=="load_drop_down_attention"){
	$supplier_name=return_field_value("contact_person","lib_supplier","id ='".$data."' and is_deleted=0 and status_active=1");
	echo "document.getElementById('txt_attention').value = '".$supplier_name."';\n";
	exit();	
}

if ($action=="load_drop_down_buyer_order"){
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$data and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
}

if($action=="print_button_variable_setting"){
	$print_report_format=0;
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=2 and report_id=3 and is_deleted=0 and status_active=1");
	echo "document.getElementById('report_ids').value = '".$print_report_format."';\n";
	echo "print_report_button_setting('".$print_report_format."');\n";
	exit();	
}

if ($action=="load_drop_down_po_number"){
	echo create_drop_down( "cbo_order_id",172, $po_number,"", 1, "--Select--", "", "","",$data,"","","","" );
} 
if($action=="check_conversion_rate"){ 
	$data=explode("**",$data);
	if($db_type==0){
		$conversion_date=change_date_format($data[1], "Y-m-d", "-",1);
	}
	else{
		$conversion_date=change_date_format($data[1], "d-M-y", "-",1);
	}
	$currency_rate=set_conversion_rate( $data[0], $conversion_date );
	echo "1"."_".$currency_rate;
	exit();	
}
if($action=="check_month_maintain"){ 
	$sql_result=sql_select("select tna_integrated from variable_order_tracking where company_name='$data' and variable_list=14 and status_active=1 and is_deleted=0");
	$maintain_setting=$sql_result[0][csf('tna_integrated')];
	if($maintain_setting==1){
		echo "1"."_";
	}
	else{
		echo "0"."_";
	}
	exit();	
}

if ($action=="load_drop_down_fabric_description")
{
	$data=explode("_",$data);
	$txt_order_no_id=$data[0];
	$cbo_fabric_natu=$data[1];
	$cbo_fabric_source=$data[2];
	if ($cbo_fabric_natu!=0) $cbo_fabric_natu="and a.fab_nature_id='$cbo_fabric_natu'"; 
	if ($cbo_fabric_source!=0) $cbo_fabric_source_cond="and a.fabric_source='$cbo_fabric_source'"; 
	
	$nameArray=sql_select("select a.id as pre_cost_fabric_cost_dtls_id, a.body_part_id, a.color_type_id, a.gsm_weight, a.construction, a.composition FROM wo_pre_cost_fabric_cost_dtls a, wo_po_break_down b WHERE a.job_no=b.job_no_mst and b.id in (".$txt_order_no_id.")  $cbo_fabric_natu $cbo_fabric_source_cond group by a.id,a.body_part_id,a.color_type_id,a.gsm_weight,a.construction,a.composition order by a.id"); 
	$fabric_description_array= array();
	foreach ($nameArray as $result){
		if (count($nameArray)>0 ){
			$fabric_description_array[$result[csf("pre_cost_fabric_cost_dtls_id")]]=$body_part[$result[csf("body_part_id")]].', '.$color_type[$result[csf("color_type_id")]].', '.$result[csf("construction")].', '.$result[csf("composition")].', '.$result[csf("gsm_weight")];
		}
	}
	//print_r($fabric_description_array);
	echo create_drop_down( "cbo_fabricdescription_id", 420, $fabric_description_array,"", 1, "--Select--", "", "load_drop_down( 'requires/sample_booking_controller', this.value, 'load_drop_down_uom', 'uom_td');","","","","","","" );//
	exit();
}

if ($action=="load_drop_down_gmts_color"){
	$data=explode("_",$data);
	$txt_order_no_id=$data[0];
	$cbo_fabric_natu=$data[1];
	$cbo_fabric_source=$data[2];
	$color_library_order=return_library_array( "select b.color_number_id,a.color_name from lib_color a , wo_po_color_size_breakdown b where a.id=b.color_number_id and b.po_break_down_id in (".$txt_order_no_id.") ", "color_number_id", "color_name"  );
	echo create_drop_down( "cbo_garmentscolor_id", 172, $color_library_order,"", 1, "-- Select Color --", $selected, "" );
}

if ($action=="load_drop_down_fabric_color"){
	$data=explode("_",$data);
	$txt_order_no_id=$data[0];
	$cbo_fabric_natu=$data[1];
	$cbo_fabric_source=$data[2];
	if ($cbo_fabric_natu!=0) $cbo_fabric_natu="and fab_nature_id='$cbo_fabric_natu'"; 
	if ($cbo_fabric_source!=0) $cbo_fabric_source_cond="and fabric_source='$cbo_fabric_source'"; 
$nameArray=sql_select( "
	select
	a.id as pre_cost_fabric_cost_dtls_id,
	a.job_no,
	a.color_size_sensitive,
	a.color,
	a.color_break_down,
	c.color_number_id
	FROM
	wo_pre_cost_fabric_cost_dtls a,
	wo_po_color_size_breakdown c
	WHERE
	a.job_no=c.job_no_mst and 
	c.po_break_down_id in (".$txt_order_no_id.")  $cbo_fabric_natu $cbo_fabric_source_cond
	order by a.id"); 
	$fabric_color_array= array();
	foreach ($nameArray as $result){
		if (count($nameArray)>0 ){
			$constrast_color_arr=array();
			if($result[csf("color_size_sensitive")]==3){
				$constrast_color=explode('__',$result[csf("color_break_down")]);
				for($i=0;$i<count($constrast_color);$i++){
					$constrast_color2=explode('_',$constrast_color[$i]);
					$constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
				}
			}
			$color_id="";
			if($result[csf("color_size_sensitive")]==3){
				$color_id=return_field_value("contrast_color_id","wo_pre_cos_fab_co_color_dtls","job_no='".$result[csf('job_no')]."' and pre_cost_fabric_cost_dtls_id=".$result[csf('pre_cost_fabric_cost_dtls_id')]." and gmts_color_id=".$result[csf('color_number_id')]."");
				$fabric_color_array[$color_id]=$constrast_color_arr[$result[csf("color_number_id")]];
			}
			else if($result[csf("color_size_sensitive")]==0){
				$fabric_color_array[$result[csf("color")]]=$color_library[$result[csf("color")]];
			}
			else{
				$fabric_color_array[$result[csf("color_number_id")]]=$color_library[$result[csf("color_number_id")]];
			}
		}
	}
	echo create_drop_down( "cbo_fabriccolor_id",172, $fabric_color_array,"", 0, "", "", "","","","","","","" );
}
 
if ($action=="load_drop_down_gmts_size"){
	$data=explode("_",$data);
	$txt_order_no_id=$data[0];
	$cbo_fabric_natu=$data[1];
	$cbo_fabric_source=$data[2];
	$size_library_order=return_library_array( "select b.size_number_id,a.size_name from lib_size a, wo_po_color_size_breakdown b where a.id=b.size_number_id and b.po_break_down_id in (".$txt_order_no_id.") ", "size_number_id", "size_name");
	
	echo create_drop_down( "cbo_garmentssize_id", 172, $size_library_order,"", 1, "-- Select Size --", $selected, "" );
}

if ($action=="load_drop_down_item_size"){
	$data=explode("_",$data);
	$txt_order_no_id=$data[0];
	$cbo_fabric_natu=$data[1];
	$cbo_fabric_source=$data[2];
	if ($cbo_fabric_natu!=0) $cbo_fabric_natu="and fab_nature_id='$cbo_fabric_natu'"; 
	if ($cbo_fabric_source!=0) $cbo_fabric_source_cond="and fabric_source='$cbo_fabric_source'"; 
	$nameArray=sql_select( "
	select
	b.item_size
	FROM
	wo_pre_cost_fabric_cost_dtls a,
	wo_pre_cos_fab_co_avg_con_dtls b
	WHERE
	a.job_no=b.job_no and
	a.id=b.pre_cost_fabric_cost_dtls_id and
	b.po_break_down_id in (".$txt_order_no_id.")  $cbo_fabric_natu $cbo_fabric_source_cond
	order by b.item_size"); 
	$item_size_array= array();
	foreach ($nameArray as $result){
		if (count($nameArray)>0 ){
		    $item_size_array[$result[csf("item_size")]]=$result[csf("item_size")];
		}
	}
	echo create_drop_down( "cbo_itemsize_id",172, $item_size_array,"", 0, "", "", "","","","","","","" );
}

if ($action=="load_drop_down_uom"){
	//$data=explode("_",$data);
	$select_uom=return_field_value("uom","wo_pre_cost_fabric_cost_dtls","id ='".$data."' and is_deleted=0 and status_active=1");
	
	echo create_drop_down( "cbouom", 172, $unit_of_measurement,'', 1, '-Uom-',$select_uom, "",1,"1,12,23,27" );
}

if($action=="process_loss_method_id"){
	$data=explode("_",$data);
	$process_loss_method=return_field_value("process_loss_method", "variable_order_tracking", "company_name=$data[0]  and variable_list=18 and item_category_id=$data[1] and status_active=1 and is_deleted=0");
	echo $process_loss_method;
	
}

if($action=="show_fabric_booking"){
	extract($_REQUEST);
	$arr=array (0=>$po_number,1=>$body_part,2=>$color_type,6=>$color_library,11=>$unit_of_measurement);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$sql= "select a.po_break_down_id, b.body_part_id, b.color_type_id, b.construction, b.composition, b.gsm_weight, a.fabric_color_id, a.item_size, a.dia_width, a.fin_fab_qnty, a.process_loss_percent, a.uom, a.grey_fab_qnty, a.rate, a.amount, a.id, a.pre_cost_fabric_cost_dtls_id FROM wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b WHERE a.pre_cost_fabric_cost_dtls_id=b.id and a.booking_no='".$data."' and a.is_short=2 and a.status_active=1 and	a.is_deleted=0"; 
	 
	echo  create_list_view("list_view", "PO Number,Body Part,Color Type,Construction,Composition,GSM,Fab.Color,Item Size,Dia/ Width,Fin Fab Qty,Process Loss,Uom,Gray Qty,Rate,Amount", "100,120,80,100,120,50,80,70,50,60,60,60,60,60,60","1200","220",0, $sql , "get_php_form_data", "id", "'populate_details_data_from_for_update'", 1, "po_break_down_id,body_part_id,color_type_id,0,0,0,fabric_color_id,0,0,0,0,uom,0,0,0", $arr , "po_break_down_id,body_part_id,color_type_id,construction,composition,gsm_weight,fabric_color_id,item_size,dia_width,fin_fab_qnty,process_loss_percent,uom,grey_fab_qnty,rate,amount", "requires/sample_booking_controller",'','0,0,0,0,0,0,0,0,0,2,2,0,2,2,2') ;
	exit();
}
/*if ($action=="show_fabric_booking1")
{
	extract($_REQUEST);
	?>
    <table width="1280" class="rpt_table" border="0" rules="all">
        	<thead>
        	<tr>
                <th width="50">Sl</th>
            	<th width="120">PO Number</th>
                <th width="120">Body Part</th>
                <th width="120">Color Type</th>   
                <th width="120">Construction</th>
                <th width="120">Composition</th>
                <th width="50">GSM</th>
                <th width="100">Dia/Width</th>  
                <th width="100">Fab.Color</th>
                <th width="50">Item Size</th>
                <th width="150">Fin Fab Qnty</th>
                <th width="120">Process Loss</th>
                <th width="100">Gray Qnty</th>
                <th width="100">Rate</th> 
                <th width="100">Amount</th>  
                <th></th>          
           </tr>
       </thead>
       
    <?
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	$tot_finish_fab_qnty=0;
    $tot_grey_fab_qnty=0;
	if($type==1)
	{
	$nameArray=sql_select("select id,pre_cost_fabric_cost_dtls_id,po_break_down_id,fabric_color_id,item_size,dia_width,fin_fab_qnty,process_loss_percent,grey_fab_qnty,rate,amount FROM wo_booking_dtls WHERE booking_no ='".$txt_booking_no."' and is_short=1 and status_active=1 and	is_deleted=0"); 
	}
	$count=0;
	foreach ($nameArray as $result)
	{
		if (count($nameArray)>0 )
		{
			 if ($count%2==0)  
                	$bgcolor="#E9F3FF";
                else
                	$bgcolor="#FFFFFF";	
					
						 $count++;
						 $fabric_description=sql_select("select body_part_id,fab_nature_id,color_type_id,construction,composition,gsm_weight FROM  wo_pre_cost_fabric_cost_dtls WHERE id ='".$result[csf("pre_cost_fabric_cost_dtls_id")]."'  and status_active=1 and	is_deleted=0"); 
						 list($fabric_description_row)=$fabric_description
						 
			?>
                	<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $count; ?>','<? echo $bgcolor;?>');get_php_form_data( <? echo $result[csf("id")]  ?>, 'populate_details_data_from_for_update', 'requires/sample_booking_controller' )" id="tr_<? echo $count; ?>">
                        <td width="50"> 
						<? echo $count; ?>
                        </td>
                        <td width="120" align="center">
						<? echo $po_number[$result[csf("po_break_down_id")]];?> 
                        </td>
                        <td width="120" align="center">
						<? echo $body_part[$fabric_description_row[csf("body_part_id")]];?>
                        </td>
                        <td width="120" align="center">
						<? echo $color_type[$fabric_description_row[csf("color_type_id")]];?> 
                        </td>   
                        <td width="120" align="center">
						<? echo $fabric_description_row[csf("construction")]; ?> 
                        </td>
                        <td width="120" align="center">
						<? echo $fabric_description_row[csf("composition")]; ?>
                        </td>
                        
                        <td width="50" align="center"><?  echo $fabric_description_row[csf("gsm_weight")]; ?> </td>
                        <td width="100" align="center"><?  echo $result[csf("dia_width")]; ?></td> 
                        
                         <td width="100" align="center"><?  echo $color_library[$result[csf("fabric_color_id")]]; ?></td> 
                        <td width="50" align="center"><?  echo $result[csf("item_size")];?></td> 
                        <td width="150" align="right"><? echo number_format( $result[csf("fin_fab_qnty")],2); ?></td>  
                        <td width="120" align="right">
						<?
						echo number_format($result[csf("process_loss_percent")],2);
						?>
                        </td>
                        <td width="100" align="right">
						<? 
						echo number_format($result[csf("grey_fab_qnty")],2);
						?>
                        </td>
                        <td width="100" align="right"><? echo number_format($result[csf("rate")],2); ?></td>
                        <td align="right" width="100" align="right"> <? echo number_format($result[csf("amount")],2); ?> </td>
                        <td align="right"></td> 
                    </tr>
                <?
		} // if count namearray end
	} // for each name arra
	?>
	</tbody>
    </table>
    
    
   
<?
}*/

if($action=="check_is_booking_used"){
	$work_order_no=return_field_value("work_order_no","com_pi_item_details","work_order_no='$data' and status_active =1 and is_deleted=0");
	echo $work_order_no;
	die;
}

if($action=="delete_booking_item"){
	execute_query( "update wo_booking_dtls set status_active=0,is_deleted =1 where  booking_no ='$data'",0);	
}

if($action=="show_fabric_booking_report")
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
    $marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$po_qnty_tot=return_field_value( "sum(po_quantity)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	$sample_name_name_arr=return_library_array( "select id,sample_name from    lib_sample",'id','sample_name');
	?>
	<div style="width:1330px" align="center">       
    										<!--    Header Company Information         --> 
    <?php
		$nameArray_approved=sql_select( "select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=13"); 
		
		list($nameArray_approved_row)=$nameArray_approved;
		$nameArray_approved_date=sql_select( "select b.approved_date as approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=13 and b.approved_no='".$nameArray_approved_row[csf('approved_no')]."'");
		list($nameArray_approved_date_row)=$nameArray_approved_date;
		$nameArray_approved_comments=sql_select( "select b.comments as comments from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=13 and b.approved_no='".$nameArray_approved_row[csf('approved_no')]."'");
		list($nameArray_approved_comments_row)=$nameArray_approved_comments;
    ?>	
       <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black">
           <tr>
               <td width="100"> 
              	 <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               </td>
               <td width="1000">                                     
                    <table width="100%" cellpadding="0" cellspacing="0" >
                        <tr>
                            <td align="center" style="font-size:20px;"><? echo $company_library[$cbo_company_name]; ?></td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px">  
                            <?
								$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website from lib_company where id=$cbo_company_name"); 
								foreach ($nameArray as $result)
								{ 
									?>
									Plot No: <? echo $result[csf('plot_no')]; ?> 
									Level No: <? echo $result[csf('level_no')]?>
									Road No: <? echo $result[csf('road_no')]; ?> 
									Block No: <? echo $result[csf('block_no')];?> 
									City No: <? echo $result[csf('city')];?> 
									Zip Code: <? echo $result[csf('zip_code')]; ?> 
									Province No: <?php echo $result[csf('province')];?> 
									Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
									Email Address: <? echo $result[csf('email')];?> 
									Website No: <? echo $result[csf('website')];
								}
                            ?>   
                            </td> 
                        </tr>
                        <tr>
                            <td align="center" style="font-size:20px">  
                            	<strong><? echo $report_title;?> &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:#F00"><? if(str_replace("'","",$id_approved_id) ==1){ echo "(Approved)";}else{echo "";}; ?> </font></strong>
                            </td> 
                            
                              <td style="font-size:20px"> 
                              <?
								 if($nameArray_approved_row[csf('approved_no')]>1)
								 {
								 ?>
								 <strong> Revised No: <? echo $nameArray_approved_row[csf('approved_no')]-1; ?></strong>
                                  <br/>
								 
								  <?
								 }
							  	?>
                             </td>
                        </tr>
                    </table>
                </td>
                 <td width="250" id="barcode_img_id"> 
                 
               </td>       
            </tr>
       </table>
		<?
        $job_no='';
        $total_set_qnty=0;
        $colar_excess_percent=0;
        $cuff_excess_percent=0;
        $nameArray=sql_select( "select a.id,a.booking_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.po_break_down_id,a.colar_excess_percent,a.cuff_excess_percent,a.fabric_composition,a.delivery_date,a.is_apply_last_update,a.pay_mode,a.fabric_source,b.job_no,b.buyer_name, b.style_ref_no ,b.gmts_item_id,b.order_uom,b.total_set_qnty,b.style_description,b.season,b.product_dept,b.product_code,b.pro_sub_dep,b.dealing_marchant from wo_booking_mst a, wo_po_details_master b where  a.job_no=b.job_no and a.booking_no=$txt_booking_no and a.company_id='$cbo_company_name' and b.company_name='$cbo_company_name'"); //2 compnay check for sample job is FAL-15-00586 in development
        foreach ($nameArray as $result)
        {
			$total_set_qnty=$result[csf('total_set_qnty')];
			$colar_excess_percent=$result[csf('colar_excess_percent')];
			$cuff_excess_percent=$result[csf('cuff_excess_percent')];
			$po_no="";$file_no="";$ref_no="";
			$shipment_date="";
			 $sql_po= "select po_number,grouping,file_no,MIN(pub_shipment_date) pub_shipment_date from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].") group by po_number,grouping,file_no"; 
			$data_array_po=sql_select($sql_po);
			foreach ($data_array_po as $row_po)
			{
				$po_no.=$row_po[csf('po_number')].", ";
				$shipment_date.=change_date_format($row_po[csf('pub_shipment_date')],'dd-mm-yyyy','-').", ";
				$ref_no=$row_po[csf('grouping')].",";
				$file_no=$row_po[csf('file_no')].",";
			}
      		 //$file_no= rtrim($file_no,','); $ref_no= rtrim($ref_no,',');
			$lead_time="";
			if($db_type==0)
			{
				$sql_lead_time= "select DATEDIFF(pub_shipment_date,po_received_date) date_diff from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")"; 
			}
        
			if($db_type==2)
			{
				$sql_lead_time= "select (pub_shipment_date-po_received_date) date_diff from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")"; 
			}
			$data_array_lead_time=sql_select($sql_lead_time);
			foreach ($data_array_lead_time as $row_lead_time)
			{
				$lead_time.=$row_lead_time[csf('date_diff')].",";
				//$shipment_date.=change_date_format($row_po['pub_shipment_date'],'dd-mm-yyyy','-').",";
			}
        
			$po_received_date="";
			$sql_po_received_date= "select MIN(po_received_date) as po_received_date from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")"; 
			$data_array_po_received_date=sql_select($sql_po_received_date);
			foreach ($data_array_po_received_date as $row_po_received_date)
			{
				$po_received_date=change_date_format($row_po_received_date[csf('po_received_date')],'dd-mm-yyyy','-');
				//$shipment_date.=change_date_format($row_po['pub_shipment_date'],'dd-mm-yyyy','-').",";
			}
        
			$sql_po= "select po_number,MIN(pub_shipment_date) pub_shipment_date, MIN(insert_date) as insert_date,shiping_status from wo_po_break_down  where id in(".$result[csf('po_break_down_id')].") group by po_number,shiping_status"; 
			$data_array_po=sql_select($sql_po);
			foreach ($data_array_po as $rows)
			{
				$daysInHand.=(datediff('d',$result[csf('delivery_date')],$rows[csf('pub_shipment_date')])-1).",";
				$booking_date=$result[csf('update_date')];
				if($booking_date=="" || $booking_date=="0000-00-00 00:00:00")
				{
					$booking_date=$result[csf('insert_date')];
				}
				$WOPreparedAfter.=(datediff('d',$rows[csf('insert_date')],$booking_date)-1).",";
				
				if($rows[csf('shiping_status')]==1)
				{
					$shiping_status.= "FP".",";
				}
				else if($rows[csf('shiping_status')]==2)
				{
					$shiping_status.= "PS".",";
				}
				else if($rows[csf('shiping_status')]==3)
				{
					$shiping_status.= "FS".",";
				}
			}
			
			$varcode_booking_no=$result[csf('booking_no')];
			if($result[csf('style_ref_no')])$style_sting.=$result[csf('style_ref_no')].'_';
        ?>
            <table width="100%" style="border:1px solid black" >                    	
                <tr>
                	<td colspan="6" valign="top" style="font-size:18px; color:#F00"><? if($result[csf('is_apply_last_update')]==2){echo "Booking Info not synchronized with order entry and pre-costing. order entry or pre-costing has updated after booking entry.  Contact to ".$marchentrArr[$result[csf('dealing_marchant')]]; } else{ echo "";} ?></td>
                </tr>                                                
                <tr>
                    <td width="100"><span style="font-size:18px"><b>Buyer/Agent Name</b></span></td>
                    <td width="110">:&nbsp;<span style="font-size:18px"><b><? echo $buyer_name_arr[$result[csf('buyer_name')]]; ?></b></span></td>
                    <td width="100"><span style="font-size:12px"><b>Dept.</b></span></td>
                    <td width="110">:&nbsp;<? echo $product_dept[$result[csf('product_dept')]] ; if($result[csf('product_code')] !=""){ echo " (".$result[csf('product_code')].")";} if($result[csf('pro_sub_dep')] !=0){ echo " (".$pro_sub_dept_array[$result[csf('pro_sub_dep')]].")";}?></td>	
                    <td width="100"><span style="font-size:12px"><b>Order Qnty</b></span></td>
                    <td width="110">:&nbsp;<? echo $po_qnty_tot." ".$unit_of_measurement[$result[csf('order_uom')]] ; ?></td>
                </tr>
                <tr>
                    <td width="100" style="font-size:12px"><b>Garments Item</b></td>
                    <td width="110">:&nbsp;
                    <? 
						$gmts_item_name="";
						$gmts_item=explode(',',$result[csf('gmts_item_id')]);
						for($g=0;$g<=count($gmts_item); $g++)
						{
							$gmts_item_name.= $garments_item[$gmts_item[$g]].",";
						}
						echo rtrim($gmts_item_name,',');
                    ?>
                    </td>
                    <td width="100" style="font-size:12px"><b>Booking Date</b></td>
                    <td width="110">:&nbsp;<? echo change_date_format($result[csf('booking_date')],'dd-mm-yyyy','-');?>&nbsp;&nbsp;&nbsp;</td>
                    <td width="100" style="font-size:18px"><b>Style Ref.</b>   </td>
                    <td width="110" style="font-size:18px">:&nbsp;<b><? echo $result[csf('style_ref_no')];?> </b>   </td>
                </tr>
                <tr>
                    <td  width="100" style="font-size:12px"><b>Style Des.</b></td>
                    <td  width="110" >:&nbsp;<? echo $result[csf('style_description')]; $job_no= $result[csf('job_no')];?></td>
                    <td width="100" style="font-size:12px"><b>Lead Time </b>   </td>
                    <td width="110">:&nbsp;<?  echo rtrim($lead_time,",");;?> </td>
                    <td width="100" style="font-size:12px"><b>Dealing Merchant</b></td>
                    <td width="110">:&nbsp;<? echo $marchentrArr[$result[csf('dealing_marchant')]]; ?></td>
                </tr>
                <tr>
                    <td width="100" style="font-size:12px"><b>Supplier Name</b>   </td>
                    <td width="110">:&nbsp;<? 
					if($result[csf('pay_mode')]==5 || $result[csf('pay_mode')]==3){
					echo $company_library[$result[csf('supplier_id')]];
					}
					else{
					echo $supplier_name_arr[$result[csf('supplier_id')]];
					}
					//echo $supplier_name_arr[$result[csf('supplier_id')]];?>    </td>
                    <td width="100" style="font-size:12px"><b>Fab. Delivery Date</b></td>
                    <td width="110">:&nbsp;<? echo change_date_format( $result[csf('delivery_date')],'dd-mm-yyyy','-');?></td> 
                    <td width="100" style="font-size:18px"><b>Booking No </b>   </td>
                    <td width="110" style="font-size:18px">:&nbsp;<b><? echo $result[csf('booking_no')];?></b><? echo "(".$fabric_source[$result[csf('fabric_source')]].")"?></td>
                </tr> 
                <tr>
                    <td width="100" style="font-size:12px"><b>Season</b></td>
                    <td width="110">:&nbsp;<? echo $result[csf('season')]; ?></td>
                    <td width="100" style="font-size:12px"><b>Attention</b></td>
                    <td width="110" >:&nbsp;<? echo $result[csf('attention')]; ?></td>
                    <td width="100" style="font-size:12px"><b>Po Received Date</b></td>
                    <td width="110" >:&nbsp;<? echo $po_received_date; ?></td>
                </tr>  
                <tr>
                    <td width="100" style="font-size:18px"><b>Order No</b></td>
                    <td width="110" style="font-size:18px" colspan="5">:&nbsp;<b><? echo rtrim($po_no,", "); ?></b></td>
                </tr> 
                <tr>
                    <td width="100" style="font-size:12px"><b>Shipment Date</b></td>
                    <td width="110" colspan="5"> :&nbsp;<? echo rtrim($shipment_date,", "); ?></td>
                </tr> 
                <tr>
                    <td width="110" style="font-size:12px"><b>WO Prepared After</b></td>
                    <td width="300"> :&nbsp;<? echo rtrim($WOPreparedAfter,',').' Days' ?></td>
                    <td width="100" style="font-size:12px"><b>Ship.days in Hand</b></td>
                    <td width="300"> :&nbsp;<? echo rtrim($daysInHand,',').' Days'?></td>
                    <td width="100" style="font-size:12px"><b>Ex-factory status</b></td>
                    <td> :&nbsp;<? echo rtrim($shiping_status,','); ?></td>
                </tr> 
                 <tr>
                    <td width="110" style="font-size:12px"><b>File No</b></td>
                    <td width="300"> :&nbsp;<? echo rtrim($file_no,','); ?></td>
                    <td width="100" style="font-size:12px"><b>Internal Ref.</b></td>
                    <td width="300"> :&nbsp;<? echo rtrim($ref_no,',');?></td>
                    <td width="100" style="font-size:12px"></td>
                    <td>&nbsp;</td>
                </tr>
                
                <tr>
                    <td width="110" style="font-size:12px"><b>Fabric Composition</b></td>
                    <td  colspan="5">: &nbsp;<? echo $result[csf('fabric_composition')]; ?></td>
                    
                </tr>
            </table> 
        <?
		}
		?>
      <br/>   									 <!--  Here will be the main portion  -->
     <style>
		 .main_table tr th{
			 border:1px solid black;
			 font-size:13px;
			 outline: 0;
		 }
		  .main_table tr td{
			 border:1px solid black;
			 font-size:13px;
			 outline: 0;
		 }
	</style>
    <?
	$costing_per="";
	$costing_per_qnty=0;
	$costing_per_id=return_field_value( "costing_per", "wo_pre_cost_mst","job_no ='$job_no'");
	if($costing_per_id==1)
	{
		$costing_per="1 Dzn";
		$costing_per_qnty=12;
	}
	if($costing_per_id==2)
	{
		$costing_per="1 Pcs";
		$costing_per_qnty=1;
	}
	if($costing_per_id==3)
	{
		$costing_per="2 Dzn";
		$costing_per_qnty=24;
	}
	if($costing_per_id==4)
	{
		$costing_per="3 Dzn";
		$costing_per_qnty=36;
	}
	if($costing_per_id==5)
	{
		$costing_per="4 Dzn";
		$costing_per_qnty=48;
	}
	//$process_loss_method=return_field_value( "process_loss_method", "wo_pre_cost_fabric_cost_dtls","job_no='$job_no'");
			
	$process_loss_method=return_field_value("process_loss_method", "variable_order_tracking", "company_name=$cbo_company_name and variable_list=18 and item_category_id=$cbo_fabric_natu and status_active=1 and is_deleted=0");

	if(str_replace("'","",$cbo_fabric_source)==1)
	{
		$nameArray_fabric_description= sql_select("SELECT a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width,b.process_loss_percent FROM wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls b where b.booking_no =$txt_booking_no and a.id=b.pre_cost_fabric_cost_dtls_id  and b.status_active=1 and	
	b.is_deleted=0  group by a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width,b.process_loss_percent order by a.body_part_id");
	    ?>
        <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
            <tr align="center"><th colspan="3" align="left">Body Part</th>
				<? 
                foreach($nameArray_fabric_description  as $result_fabric_description)
                {
                    if( $result_fabric_description[csf('body_part_id')] == "")  echo "<td  colspan='2'>&nbsp</td>";	
                    else echo "<td  colspan='2'>". $body_part[$result_fabric_description[csf('body_part_id')]]."</td>";			
                }
                ?>
            	<td rowspan="8" width="50"><p>Total  Finish Fabric <? if(trim($cbo_fabric_natu ,"'")==2){echo "(KG)";}else{echo "(Yds)";} ?></p></td> <td  rowspan="8" width="50"><p>Total Grey Fabric <? if(trim($cbo_fabric_natu ,"'")==2){echo "(KG)";}else{echo "(Yds)";} ?>)</p></td>
            	<td rowspan="8" width="50"><p>Process Loss %</p></td>
            </tr> 
            <tr align="center"><th colspan="3" align="left">Color Type</th>
                <? 
                foreach($nameArray_fabric_description  as $result_fabric_description)
                {
					if( $result_fabric_description[csf('color_type_id')] == "")  echo "<td  colspan='2'>&nbsp</td>";	
					else echo "<td  colspan='2'>". $color_type[$result_fabric_description[csf('color_type_id')]]."</td>";			
                }
                ?>
            </tr>  
            <tr align="center"><th colspan="3" align="left">Construction</th>
				<? 
                foreach($nameArray_fabric_description  as $result_fabric_description)
                {
					if( $result_fabric_description[csf('construction')] == "")  echo "<td  colspan='2'>&nbsp</td>";	
					else echo "<td  colspan='2'>". $result_fabric_description[csf('construction')]."</td>";			
                }
                ?>
            </tr>       
            <tr align="center"><th   colspan="3" align="left">Composition</th>
				<? 
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
					if( $result_fabric_description[csf('composition')] == "")   echo "<td colspan='2' >&nbsp</td>";
					else echo "<td colspan='2' >".$result_fabric_description[csf('composition')]."</td>";			
                }
                ?>
            </tr>
            <tr align="center"><th  colspan="3" align="left">GSM</th>
				<? 
                foreach($nameArray_fabric_description  as $result_fabric_description)
                {
					if( $result_fabric_description[csf('gsm_weight')] == "")   echo "<td colspan='2'>&nbsp</td>";
					else echo "<td colspan='2' align='center'>". $result_fabric_description[csf('gsm_weight')]."</td>";			
                }
                ?>
            </tr>
            <tr align="center"><th   colspan="3" align="left">Dia/Width</th>
				<? 
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
                    if( $result_fabric_description[csf('dia_width')] == "")   echo "<td colspan='2'>&nbsp</td>";
                    else echo "<td colspan='2' align='center'>". $result_fabric_description[csf('dia_width')]."</td>";			
                }
                ?>
            </tr>
            <tr align="center"><th  colspan="3" align="left">Process Loss%</th>
				<? 
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
					if( $result_fabric_description[csf('process_loss_percent')] == "")   echo "<td colspan='2'>&nbsp</td>";
					else echo "<td align='center' colspan='2'>". $result_fabric_description[csf('process_loss_percent')]."</td>";			
                }
                ?>
            </tr>
            <tr>
                <th  width="100" align="left">Sample Name</th>
                <th  width="100" align="left">Fabric Color</th>
                <th  width="100" align="left">Lapdip No</th>
				<? 
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
                    echo "<th width='50'>Finish</th><th width='50' >Gray</th>";			
                }
                ?>
            </tr>
       		<?
	        $grand_total_fin_fab_qnty=0;
			$grand_total_grey_fab_qnty=0;
			$grand_totalcons_per_finish=0;
			$grand_totalcons_per_grey=0;
			if($db_type==0) $sample_type_id="group_concat(sample_type)";
			else if($db_type==2) $sample_type_id="listagg((cast(sample_type as varchar2(4000))),',') within group (order by sample_type)";
			
			$color_wise_wo_sql=sql_select("select job_no, fabric_color_id, $sample_type_id as sample_type
										  FROM 
										  wo_booking_dtls
										  WHERE 
										  booking_no =$txt_booking_no and
										  status_active=1 and
                                          is_deleted=0
										  group by job_no, fabric_color_id");
			foreach($color_wise_wo_sql as $color_wise_wo_result)
			{
				?> 
				<tr>
                    <td  width="100" align="left">
						<?
                        $sample_type_val="";
                        $ex_sample_type=array_unique(explode(",",$color_wise_wo_result[csf('sample_type')]));
                        foreach($ex_sample_type as $sm_val)
                        {
                        	if($sample_type_val=="") $sample_type_val=$sample_name_name_arr[$sm_val]; else $sample_type_val.=','.$sample_name_name_arr[$sm_val];
                        }
                   		echo $sample_type_val; 
                    ?></td>
                    <td  width="100" align="left">
						<?
                        echo $color_library[$color_wise_wo_result[csf('fabric_color_id')]];
                        ?>
                    </td>
                    <td  width="100" align="left">
						<? 
                        $lapdip_no="";
                        $lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='".$color_wise_wo_result[csf('job_no')]."' and approval_status=3 and color_name_id=".$color_wise_wo_result[csf('fabric_color_id')]."","lapdip_no");
                        if($lapdip_no=="") echo "&nbsp;"; echo $lapdip_no; 
                        ?>
                    </td>
                    <?
                    $total_fin_fab_qnty=0;
                    $total_grey_fab_qnty=0;
                    foreach($nameArray_fabric_description as $result_fabric_description)
                    {
						if($db_type==0)
						{
							$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty
															  FROM 
															  wo_pre_cost_fabric_cost_dtls a,
															  wo_booking_dtls b 
															  WHERE 
															  b.booking_no =$txt_booking_no  and
															  a.id=b.pre_cost_fabric_cost_dtls_id and
															  a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
															  a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and 
															  a.construction='".$result_fabric_description[csf('construction')]."' and 
															  a.composition='".$result_fabric_description[csf('composition')]."' and 
															  a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and 
															  b.dia_width='".$result_fabric_description[csf('dia_width')]."' and 
															  b.process_loss_percent='".$result_fabric_description[csf('process_loss_percent')]."' and 
															  b.fabric_color_id=".$color_wise_wo_result[csf('fabric_color_id')]." and
															  b.status_active=1 and
															  b.is_deleted=0");
						}
						if($db_type==2)
						{
							$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty
															  FROM 
															  wo_pre_cost_fabric_cost_dtls a,
															  wo_booking_dtls b 
															  WHERE 
															  b.booking_no =$txt_booking_no  and
															  a.id=b.pre_cost_fabric_cost_dtls_id and
															  nvl(a.body_part_id,0)=nvl('".$result_fabric_description[csf('body_part_id')]."',0) and
															  nvl(a.color_type_id,0)=nvl('".$result_fabric_description[csf('color_type_id')]."',0) and 
															  nvl(a.construction,0)=nvl('".$result_fabric_description[csf('construction')]."',0) and 
															  nvl(a.composition,0)=nvl('".$result_fabric_description[csf('composition')]."',0) and 
															  nvl(a.gsm_weight,0)=nvl('".$result_fabric_description[csf('gsm_weight')]."',0) and 
															  nvl(b.dia_width,0)=nvl('".$result_fabric_description[csf('dia_width')]."',0) and 
															  nvl(b.process_loss_percent,0)=nvl('".$result_fabric_description[csf('process_loss_percent')]."',0) and 
															  nvl(b.fabric_color_id,0)=nvl(".$color_wise_wo_result[csf('fabric_color_id')].",0) and
															  b.status_active=1 and
															  b.is_deleted=0");
						}
                    	list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
                    ?>
                    <td width='50' align='right'>
						<? 
                        if($color_wise_wo_result_qnty[csf('fin_fab_qnty')]!="")
                        {
							echo number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')],2) ;
							$total_fin_fab_qnty+=$color_wise_wo_result_qnty[csf('fin_fab_qnty')];
                        }
                        ?>
                    </td>
                    <td width='50' align='right' > 
						<? 
                        if($color_wise_wo_result_qnty[csf('grey_fab_qnty')]!="")
                        {
							echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],2); 
							$total_grey_fab_qnty+=$color_wise_wo_result_qnty[csf('grey_fab_qnty')];
                        }
                        ?>
                    </td>
                    <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_fin_fab_qnty,2); $grand_total_fin_fab_qnty+=$total_fin_fab_qnty;?></td>
                    <td align="right"><? echo number_format($total_grey_fab_qnty,2); $grand_total_grey_fab_qnty+=$total_grey_fab_qnty;?></td>
                    <td align="right">
                    <?
                    
                    if($process_loss_method==1)
                    {
                   		$process_percent=(($total_grey_fab_qnty-$total_fin_fab_qnty)/$total_fin_fab_qnty)*100;
                    }
                    
                    if($process_loss_method==2)
                    {
						//$devided_val = 1-(($total_grey_fab_qnty-$total_fin_fab_qnty)/100);
						//$process_percent=$total_grey_fab_qnty/$devided_val;
						$process_percent=(($total_grey_fab_qnty-$total_fin_fab_qnty)/$total_grey_fab_qnty)*100;
                    }
                    echo number_format($process_percent,2);
                    ?>
                    </td>
				</tr>
				<?
			}
			?>
			<tr>
				<td  width="120" align="left">&nbsp;</td>
				<td  width="120" align="left">&nbsp;</td>
				<td  width="120" align="left"><strong>Total</strong></td>
				<?
				foreach($nameArray_fabric_description as $result_fabric_description)
				{
					if($db_type==0)
					{
						$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty
															FROM 
															wo_pre_cost_fabric_cost_dtls a,
															wo_booking_dtls b 
															WHERE 
															b.booking_no =$txt_booking_no  and
															a.id=b.pre_cost_fabric_cost_dtls_id and
															a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
															a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and 
															a.construction='".$result_fabric_description[csf('construction')]."' and 
															a.composition='".$result_fabric_description[csf('composition')]."' and 
															a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and 
															b.dia_width='".$result_fabric_description[csf('dia_width')]."' and 
															b.process_loss_percent='".$result_fabric_description[csf('process_loss_percent')]."' and
															b.status_active=1 and
															b.is_deleted=0");
					}
					if($db_type==2)
					{
						$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty
															FROM 
															wo_pre_cost_fabric_cost_dtls a,
															wo_booking_dtls b 
															WHERE 
															b.booking_no =$txt_booking_no  and
															a.id=b.pre_cost_fabric_cost_dtls_id and
															nvl(a.body_part_id,0)=nvl('".$result_fabric_description[csf('body_part_id')]."',0) and
															nvl(a.color_type_id,0)=nvl('".$result_fabric_description[csf('color_type_id')]."',0) and 
															nvl(a.construction,0)=nvl('".$result_fabric_description[csf('construction')]."',0) and 
															nvl(a.composition,0)=nvl('".$result_fabric_description[csf('composition')]."',0) and 
															nvl(a.gsm_weight,0)=nvl('".$result_fabric_description[csf('gsm_weight')]."',0) and 
															nvl(b.dia_width,0)=nvl('".$result_fabric_description[csf('dia_width')]."',0) and 
															nvl(b.process_loss_percent,0)=nvl('".$result_fabric_description[csf('process_loss_percent')]."',0) and
															b.status_active=1 and
															b.is_deleted=0");
					}
					list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
					?>
					<td width='50' align='right'><?  echo number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')],2) ;?></td><td width='50' align='right' > <? echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],2);?></td>
				<?
				}
				?>
				<td align="right"><? echo number_format($grand_total_fin_fab_qnty,2);?></td>
				<td align="right"><? echo number_format($grand_total_grey_fab_qnty,2);?></td>
				<td align="right">
				<?
					if($process_loss_method==1)
					{
						$totalprocess_percent=(($grand_total_grey_fab_qnty-$grand_total_fin_fab_qnty)/$grand_total_fin_fab_qnty)*100;
					}
					if($process_loss_method==2)
					{
						$totalprocess_percent=(($grand_total_grey_fab_qnty-$grand_total_fin_fab_qnty)/$grand_total_grey_fab_qnty)*100;
					}
					echo number_format($totalprocess_percent,2);
					?>
				</td>
			</tr> 
        </table>
        <br/>
        <?
	 }
	 //echo  $cbo_fabric_source;
	if(str_replace("'","",$cbo_fabric_source)==2)
	{
		$nameArray_fabric_description= sql_select("SELECT a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width,b.process_loss_percent FROM wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls b where b.booking_no =$txt_booking_no and a.id=b.pre_cost_fabric_cost_dtls_id  and b.status_active=1 and	
		b.is_deleted=0  group by a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width,b.process_loss_percent order by a.body_part_id");
		?>
		<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
            <tr align="center"><th colspan="3" align="left">Body Part</th>
				<? 
                foreach($nameArray_fabric_description  as $result_fabric_description)
                {
					if( $result_fabric_description[csf('body_part_id')] == "")  echo "<td  colspan='3'>&nbsp</td>";	
					else echo "<td  colspan='3'>". $body_part[$result_fabric_description[csf('body_part_id')]]."</td>";			
                }
                ?>
                <td rowspan="8" width="50"><p>Total  Fabric <? if(trim($cbo_fabric_natu ,"'")==2){echo "(KG)";}else{echo "(Yds)";} ?></p></td> 
                <td rowspan="8" width="50"><p>Avg. Rate</p></td>
                <td rowspan="8" width="50"><p>Amount</p></td>
            </tr> 
            <tr align="center"><th colspan="3" align="left">Color Type</th>
				<? 
                foreach($nameArray_fabric_description  as $result_fabric_description)
                {
					if( $result_fabric_description[csf('color_type_id')] == "")  echo "<td  colspan='3'>&nbsp</td>";	
					else echo "<td  colspan='3'>". $color_type[$result_fabric_description[csf('color_type_id')]]."</td>";			
                }
                ?>
            </tr>  
            <tr align="center"><th colspan="3" align="left">Construction</th>
				<? 
                foreach($nameArray_fabric_description  as $result_fabric_description)
                {
					if( $result_fabric_description[csf('construction')] == "")  echo "<td  colspan='3'>&nbsp</td>";	
					else echo "<td  colspan='3'>". $result_fabric_description[csf('construction')]."</td>";			
                }
                ?>
            </tr>       
            <tr align="center"><th   colspan="3" align="left">Composition</th>
				<? 
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
					if( $result_fabric_description[csf('composition')] == "")   echo "<td colspan='3' >&nbsp</td>";
					else echo "<td colspan='3' >".$result_fabric_description[csf('composition')]."</td>";			
                }
                ?>
            </tr>
            <tr align="center"><th  colspan="3" align="left">GSM</th>
				<? 
                foreach($nameArray_fabric_description  as $result_fabric_description)
                {
					if( $result_fabric_description[csf('gsm_weight')] == "")   echo "<td colspan='3'>&nbsp</td>";
					else echo "<td colspan='3' align='center'>". $result_fabric_description[csf('gsm_weight')]."</td>";			
                }
                ?>
            </tr>
            <tr align="center"><th   colspan="3" align="left">Dia/Width</th>
				<? 
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
					if( $result_fabric_description[csf('dia_width')] == "")   echo "<td colspan='3'>&nbsp</td>";
					else echo "<td colspan='3' align='center'>". $result_fabric_description[csf('dia_width')]."</td>";			
                }
                ?>
            </tr>
            <tr align="center"><th  colspan="3" align="left">Process Loss%</th>
				<? 
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
					if( $result_fabric_description[csf('process_loss_percent')] == "")   echo "<td colspan='3'>&nbsp</td>";
					else echo "<td align='center' colspan='3'>". $result_fabric_description[csf('process_loss_percent')]."</td>";			
                }
                ?>
            </tr>
            <tr>
                <th  width="100" align="left">Sample Name</th>
                <th  width="100" align="left">Fabric Color</th>
                <th  width="100" align="left">Lapdip No</th>
                <? 
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
               		echo "<th width='50'>Fab Qty</th><th width='50'>Rate</th><th width='50'>Amount</th>";			
                }
                ?>
            </tr>
            <?
            $grand_total_fin_fab_qnty=0;
            $grand_total_grey_fab_qnty=0;
            $grand_totalcons_per_finish=0;
            $grand_totalcons_per_grey=0;
            if($db_type==0) $sample_type_id="group_concat(sample_type)";
            else if($db_type==2) $sample_type_id="listagg((cast(sample_type as varchar2(4000))),',') within group (order by sample_type)";
            
            $color_wise_wo_sql=sql_select("select job_no, fabric_color_id, $sample_type_id as sample_type
											FROM 
											wo_booking_dtls
											WHERE 
											booking_no =$txt_booking_no and
											status_active=1 and
											is_deleted=0
											group by job_no, fabric_color_id");
            foreach($color_wise_wo_sql as $color_wise_wo_result)
			{
				?> 
				<tr>
                    <td  width="100" align="left">
						<?
                        $sample_type_val="";
                        $ex_sample_type=array_unique(explode(",",$color_wise_wo_result[csf('sample_type')]));
                        foreach($ex_sample_type as $sm_val)
                        {
                        	if($sample_type_val=="") $sample_type_val=$sample_name_name_arr[$sm_val]; else $sample_type_val.=','.$sample_name_name_arr[$sm_val];
                        }
                        echo $sample_type_val; 
                    ?></td>
                    <td width="100" align="left">
						<?
                        echo $color_library[$color_wise_wo_result[csf('fabric_color_id')]];
                        ?>
                    </td>
                    <td width="100" align="left">
						<? 
                        $lapdip_no="";
                        $lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='".$color_wise_wo_result[csf('job_no')]."' and approval_status=3 and color_name_id=".$color_wise_wo_result[csf('fabric_color_id')]."","lapdip_no");
                        if($lapdip_no=="") echo "&nbsp;"; echo $lapdip_no; 
                        ?>
                    </td>
                    <?
                    $total_fin_fab_qnty=0;
                    $total_grey_fab_qnty=0;
                    $total_amount=0;
                    foreach($nameArray_fabric_description as $result_fabric_description)
                    {
						if($db_type==0)
						{
							$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty, avg(b.rate) as rate, sum(b.amount) as amount
																FROM 
																wo_pre_cost_fabric_cost_dtls a,
																wo_booking_dtls b 
																WHERE 
																b.booking_no =$txt_booking_no  and
																a.id=b.pre_cost_fabric_cost_dtls_id and
																a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
																a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and 
																a.construction='".$result_fabric_description[csf('construction')]."' and 
																a.composition='".$result_fabric_description[csf('composition')]."' and 
																a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and 
																b.dia_width='".$result_fabric_description[csf('dia_width')]."' and 
																b.process_loss_percent='".$result_fabric_description[csf('process_loss_percent')]."' and 
																b.fabric_color_id=".$color_wise_wo_result[csf('fabric_color_id')]." and
																b.status_active=1 and
																b.is_deleted=0");
						}
						if($db_type==2)
						{
							$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty, avg(b.rate) as rate, sum(b.amount) as amount
																FROM 
																wo_pre_cost_fabric_cost_dtls a,
																wo_booking_dtls b 
																WHERE 
																b.booking_no =$txt_booking_no  and
																a.id=b.pre_cost_fabric_cost_dtls_id and
																nvl(a.body_part_id,0)=nvl('".$result_fabric_description[csf('body_part_id')]."',0) and
																nvl(a.color_type_id,0)=nvl('".$result_fabric_description[csf('color_type_id')]."',0) and 
																nvl(a.construction,0)=nvl('".$result_fabric_description[csf('construction')]."',0) and 
																nvl(a.composition,0)=nvl('".$result_fabric_description[csf('composition')]."',0) and 
																nvl(a.gsm_weight,0)=nvl('".$result_fabric_description[csf('gsm_weight')]."',0) and 
																nvl(b.dia_width,0)=nvl('".$result_fabric_description[csf('dia_width')]."',0) and 
																nvl(b.process_loss_percent,0)=nvl('".$result_fabric_description[csf('process_loss_percent')]."',0) and 
																nvl(b.fabric_color_id,0)=nvl(".$color_wise_wo_result[csf('fabric_color_id')].",0) and
																b.status_active=1 and
																b.is_deleted=0");
						}
						list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
						?>
						<td width='50' align='right' > 
							<? 
                            if($color_wise_wo_result_qnty[csf('grey_fab_qnty')]!="")
                            {
								echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],2); 
								$total_grey_fab_qnty+=$color_wise_wo_result_qnty[csf('grey_fab_qnty')];
                            }
                            ?>
						</td>
						<td width='50' align='right'>
							<? 
                            if($color_wise_wo_result_qnty[csf('rate')]!="")
                            {
								echo number_format($color_wise_wo_result_qnty[csf('rate')],2) ;
								//$total_fin_fab_qnty+=$color_wise_wo_result_qnty[csf('fin_fab_qnty')];
                            }
                            ?>
						</td>
						<td width='50' align='right'>
							<? 
                            if($color_wise_wo_result_qnty[csf('amount')]!="")
                            {
								echo number_format($color_wise_wo_result_qnty[csf('amount')],2) ;
								//$total_fin_fab_qnty+=$color_wise_wo_result_qnty[csf('fin_fab_qnty')];
                            }
                            ?>
						</td>
						<?
						$total_amount+=$color_wise_wo_result_qnty[csf('amount')];
                    }
                    ?>
                    <td align="right"><? echo number_format($total_grey_fab_qnty,2); $grand_total_grey_fab_qnty+=$total_grey_fab_qnty;?></td>
                    <td align="right"><? echo number_format($total_amount/$total_grey_fab_qnty,2); //$grand_total_fin_fab_qnty+=$total_fin_fab_qnty;?></td>
                    <td align="right"><? echo number_format($total_amount,2); $grand_total_amount+=$total_amount;?></td>
				</tr>
				<?
			}
            ?>
            <tr>
                <td width="100" align="left">&nbsp;</td>
                <td width="100" align="left">&nbsp;</td>
                <td width="100" align="left"><strong>Total</strong></td>
                <?
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
					if($db_type==0)
					{
						$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty, avg(b.rate) as rate, sum(b.amount) as amount
															FROM 
															wo_pre_cost_fabric_cost_dtls a,
															wo_booking_dtls b 
															WHERE 
															b.booking_no =$txt_booking_no  and
															a.id=b.pre_cost_fabric_cost_dtls_id and
															a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
															a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and 
															a.construction='".$result_fabric_description[csf('construction')]."' and 
															a.composition='".$result_fabric_description[csf('composition')]."' and 
															a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and 
															b.dia_width='".$result_fabric_description[csf('dia_width')]."' and 
															b.process_loss_percent='".$result_fabric_description[csf('process_loss_percent')]."' and
															b.status_active=1 and
															b.is_deleted=0");
					}
					if($db_type==2)
					{
						$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty, avg(b.rate) as rate, sum(b.amount) as amount
															FROM 
															wo_pre_cost_fabric_cost_dtls a,
															wo_booking_dtls b 
															WHERE 
															b.booking_no =$txt_booking_no  and
															a.id=b.pre_cost_fabric_cost_dtls_id and
															nvl(a.body_part_id,0)=nvl('".$result_fabric_description[csf('body_part_id')]."',0) and
															nvl(a.color_type_id,0)=nvl('".$result_fabric_description[csf('color_type_id')]."',0) and 
															nvl(a.construction,0)=nvl('".$result_fabric_description[csf('construction')]."',0) and 
															nvl(a.composition,0)=nvl('".$result_fabric_description[csf('composition')]."',0) and 
															nvl(a.gsm_weight,0)=nvl('".$result_fabric_description[csf('gsm_weight')]."',0) and 
															nvl(b.dia_width,0)=nvl('".$result_fabric_description[csf('dia_width')]."',0) and 
															nvl(b.process_loss_percent,0)=nvl('".$result_fabric_description[csf('process_loss_percent')]."',0) and
															b.status_active=1 and
															b.is_deleted=0");
					}
					list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
					?>
					<td width='50' align='right' > <? echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],2);?></td>
					<td width='50' align='right'><?  echo number_format($color_wise_wo_result_qnty[csf('rate')],2) ;?></td>
					<td width='50' align='right'><?  echo number_format($color_wise_wo_result_qnty[csf('amount')],2) ;?></td>
					<?
                }
                ?>
                <td align="right"><? echo number_format($grand_total_grey_fab_qnty,2);?></td>
                <td align="right"><? echo number_format($grand_total_amount/$grand_total_grey_fab_qnty,2);?></td>
                <td align="right"><? echo number_format($grand_total_amount,2);?></td>
            </tr> 
		</table>
		<br/>
	<?
	}
	if(str_replace("'","",$cbo_fabric_source)==1)
	{
		$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');			
		//$yarn_sql_array=sql_select("SELECT min(id) as id ,count_id, copm_one_id, percent_one,copm_two_id, percent_two, type_id, sum(cons_qnty) as yarn_required, AVG(rate) as rate from wo_pre_cost_fab_yarn_cost_dtls where job_no='$job_no' and  status_active=1 and is_deleted=0 group by count_id,copm_one_id,percent_one, copm_two_id,percent_two,type_id order by id");
		//echo "SELECT min(a.id) as id ,a.count_id, a.copm_one_id, a.percent_one,a.copm_two_id, a.percent_two, a.color, a.type_id, sum(a.cons_qnty) as yarn_required, AVG(a.rate) as rate,b.po_break_down_id from wo_pre_cost_fab_yarn_cost_dtls a, wo_booking_dtls b where a.job_no=b.job_no and a.fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.job_no='$job_no' and b.booking_no=$txt_booking_no and  a.status_active=1 and a.is_deleted=0 group by a.count_id,a.copm_one_id,a.percent_one, a.copm_two_id,a.percent_two,a.color,a.type_id,b.po_break_down_id order by po_break_down_id";
		
		$yarn_sql_array=sql_select("SELECT a.fabric_cost_dtls_id,min(a.id) as id ,a.count_id, a.copm_one_id, a.percent_one,a.copm_two_id, a.percent_two, a.color, a.type_id, sum(a.cons_qnty) as yarn_required, AVG(a.rate) as rate,b.po_break_down_id,sum(b.grey_fab_qnty) as grey_fab_qnty, a.cons_ratio from wo_pre_cost_fab_yarn_cost_dtls a, wo_booking_dtls b where a.job_no=b.job_no and a.fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.job_no='$job_no' and b.booking_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0 group by a.count_id,a.copm_one_id,a.percent_one, a.copm_two_id,a.percent_two,a.color,a.type_id,b.po_break_down_id,a.fabric_cost_dtls_id, a.cons_ratio order by po_break_down_id ");
		?>
		<table  width="100%"  border="0" cellpadding="0" cellspacing="0" >
            <tr>
                <td width="49%" valign="top">
                    <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                        <tr align="center">
                        	<td colspan="7"><b>Yarn Required Summary</b></td>
                        </tr>
                        <tr align="center">
                            <td>Sl</td>
                            <td>PO</td>
                            <td>Yarn Description</td>
                            <td>Brand</td>
                            <td>Lot</td>
								<?
                                if($show_yarn_rate==1)
                                {
									?>
									<td>Rate</td>
									<?
                                }
                                ?>
                            <td>Cons for <? echo $costing_per; ?> Gmts</td>
                            <td>Total (KG)</td>
                        </tr>
                        <?
                        $i=0;
                        $total_yarn=0;
                        foreach($yarn_sql_array  as $row)
                        {
							$i++;
							?>
							<tr align="center">
                                <td><? echo $i; ?></td>
                                 <td><? echo $po_number[$row[csf('po_break_down_id')]]; ?></td>
                                <td>
									<?
                                    $yarn_des=$yarn_count_arr[$row[csf('count_id')]]." ".$composition[$row[csf('copm_one_id')]]." ".$row[csf('percent_one')]."%  ";
                                    if($row['copm_two_id'] !=0)
                                    {
                                    	$yarn_des.=$composition[$row[csf('copm_two_id')]]." ".$row[csf('percent_two')]."%";
                                    }
                                    $yarn_des.=$yarn_type[$row[csf('type_id')]];
                                    //echo $yarn_count_arr[$row['count_id']]." ".$composition[$row['copm_one_id']]." ".$row['percent_one']."%  ".$composition[$row['copm_two_id']]." ".$row['percent_two']."%  ".$yarn_type[$row['type_id']]; 
                                    echo $yarn_des;
                                    ?>
                                </td>
                                <td></td>
                                <td></td>
									<?
                                    if($show_yarn_rate==1)
                                    {
                                    ?>
                                    	<td><? echo number_format($row[csf('rate')],4); ?></td>
                                    <?
                                    }
                                    ?>
                                <td><? echo number_format($row[csf('yarn_required')],4); ?></td>
                                <!--<td><? //echo number_format(($row['yarn_required']/$po_qnty_tot)*$costing_per_qnty,2); ?></td>-->
                                <td align="right"><? $yarn=($row[csf('grey_fab_qnty')]*$row[csf('cons_ratio')])/100; echo number_format($yarn,2); $total_yarn+=$yarn; ?></td>
							</tr>
							<?
                        }
                        ?>
                        <tr align="center">
                            <td>Total</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
								<?
                                if($show_yarn_rate==1)
                                {
									?>
									<td></td>
									<?
                                }
                                ?>
                            <td></td>
                            <td align="right"><? echo number_format($total_yarn,2); ?></td>
                        </tr>
                    </table>
                </td>
                <td width="2%">
                </td>
                <td width="49%" valign="top" align="center">
                <?
                $yarn_sql_array=sql_select("SELECT min(a.id) as id, a.item_id, sum(a.qnty) as qnty ,min(b.supplier_id) as supplier_id,min(b.lot) as lot from inv_material_allocation_dtls a,product_details_master b where a.item_id=b.id and a.booking_no=$txt_booking_no and  a.status_active=1 and a.is_deleted=0 group by a.item_id order by a.id");
                if(count($yarn_sql_array)>0)
                {
					?>
					<table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                        <tr align="center">
                        	<td colspan="7"><b>Allocated Yarn</b></td>
                        </tr>
                        <tr align="center">
                            <td>Sl</td>
                            <td>Yarn Description</td>
                            <td>Brand</td>
                            <td>Lot</td>
                            <td>Allocated Qty (Kg)</td>
                        </tr>
                        <?
                        $total_allo=0;
                        $item=return_library_array( "select id, product_name_details from   product_details_master",'id','product_name_details');
                        $supplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
                        //$yarn_sql_array=sql_select("SELECT a.item_id, a.qnty,b.supplier_id,b.lot from inv_material_allocation_dtls a,product_details_master b where a.item_id=b.id and a.booking_no=$txt_booking_no and  a.status_active=1 and a.is_deleted=0");
                        $i=0;
                        $total_yarn=0;
                        foreach($yarn_sql_array  as $row)
                        {
							$i++;
							?>
							<tr align="center">
                                <td><? echo $i; ?></td>
                                <td><? echo $item[$row[csf('item_id')]]; ?></td>
                                <td><? echo $supplier[$row[csf('supplier_id')]]; ?></td>
                                <td><? echo $row[csf('lot')]; ?></td>
                                <td align="right"><? echo number_format($row[csf('qnty')],4); $total_allo+= $row[csf('qnty')];?></td>
							</tr>
							<?
                        }
                        ?>
                        <tr align="center">
                            <td>Total</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td align="right"><? echo number_format($total_allo,4); ?></td>
                        </tr>
					</table>
					<?
                }
                else
                {
					$is_yarn_allocated=return_field_value("allocation","variable_settings_inventory","company_name=$cbo_company_name and variable_list=18 and item_category_id=1"); 
					if($is_yarn_allocated==1)
					{
						?>
						<font style=" font-size:30px"><b>Draft</b></font>
						<?
					}
					else
					{
						echo "";
					}
                }
                ?>
                </td>
            </tr>
		</table>
		<?
	}
		?>
        <br/>


        <table  width="100%"  border="0" cellpadding="0" cellspacing="0" >
            <tr>
              
                <td width="2%">
                </td>
                <td width="49%" valign="top" align="center">
                <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                    <tr align="center">
                    <td colspan="4"><b>Approval Status</b></td>
                    
                    </tr>

                    <tr>
                    	    <th width="30">Sl   <? $bookingId=$nameArray[0][csf('id')]?> </th>
                            <th width="250">Name/Designation</th>
                            <th width="150">Approval Date</th>
                            <th width="80">Approval No</th>
                    </tr>
                     
                    <?
                    $user_arr=return_library_array( "select id, user_full_name from user_passwd", "id", "user_full_name"  );
                    $desg_arr=return_library_array( "select id, designation from user_passwd", "id", "designation"  );
 					$desg_name=return_library_array( "select id, custom_designation from lib_designation", "id", "custom_designation"  );
 					$sel=sql_select("select approved_by,approved_no,approved_date from approval_history where mst_id in(select id from wo_booking_mst where id=$bookingId) and entry_form=13 ");
					$i=1;
					foreach ($sel as $rows) {
                    
					?>

					<tr id="settr_1" align="">
                                    <td width="30"><? echo $i ?></td>
                                    <td width="250"><? echo $user_arr[$rows[csf('approved_by')]]." /".$desg_name[$desg_arr[$rows[csf('approved_by')]]] ?></td>
                                    <td width="150"><? echo $rows[csf('approved_date')] ?></td>
                                    <td width="80"><? echo $rows[csf('approved_no')] ?></td>
                                     
                                </tr>
                                <?
                                $i++;

                            }
                            ?>
                    
                    
                </table>
                   
                </td>
            </tr>
        </table>
        <br/>



        <?
		$sql_embelishment=sql_select("select emb_name,emb_type,cons_dzn_gmts,rate,amount from wo_pre_cost_embe_cost_dtls where job_no='$job_no' and status_active=1 and 	is_deleted=0");
		?>
        <table  width="100%"  border="0" cellpadding="0" cellspacing="0" >
            <tr>
                <td width="49%" valign="top">
                <?
				if(count($sql_embelishment)>0)
				{
				?>
                    <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                    <tr align="center">
                    <td colspan="7"><b>Embelishment (Pre Cost)</b></td>
                    </tr>
                    <tr align="center">
                    <td>Sl</td>
                    <td>Embelishment Name</td>
                    <td>Embelishment Type</td>
                    <td>Cons <? echo $costing_per; ?> Gmts</td>
                    <td>Rate</td>
                    <td>Amount</td>
                    
                    </tr>
                    <?
					$sql_embelishment=sql_select("select emb_name,emb_type,cons_dzn_gmts,rate,amount from wo_pre_cost_embe_cost_dtls where job_no='$job_no' and status_active=1 and 	is_deleted=0");
					$i=0;
					//$total_yarn=0;
					foreach($sql_embelishment  as $row_embelishment)
                    {
						$i++;
						?>
						<tr align="center">
                            <td><? echo $i; ?></td>
                            <td><? echo $emblishment_name_array[$row_embelishment[csf('emb_name')]]; ?></td>
                            <td>
								<?
                                if($row_embelishment[csf('emb_name')]==1)
                                {
                                echo $emblishment_print_type[$row_embelishment[csf('emb_type')]];
                                }
                                if($row_embelishment[csf('emb_name')]==2)
                                {
                                echo $emblishment_embroy_type[$row_embelishment[csf('emb_type')]];
                                }
                                if($row_embelishment[csf('emb_name')]==3)
                                {
                                echo $emblishment_wash_type[$row_embelishment[csf('emb_type')]];
                                }
                                if($row_embelishment[csf('emb_name')]==4)
                                {
                                echo $emblishment_spwork_type[$row_embelishment[csf('emb_type')]];
                                }
                                if($row_embelishment[csf('emb_name')]==5)
                                {
                                echo $row_embelishment[csf('emb_type')];
                                }
                            	?>
                            </td>
                            <td><? echo $row_embelishment[csf('cons_dzn_gmts')]; ?></td>
                            <td><? echo $row_embelishment[csf('rate')]; ?></td>
                            <td><? echo $row_embelishment[csf('amount')]; ?></td>
						</tr>
						<?
					}
					?>
                    </table>
                    <?
				}
					?>
                </td>
                <td width="2%">
                </td>
                <td width="49%" valign="top" align="center">
                <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                    <tr align="center">
                    <td><b>Approved Instructions</b></td>
                    
                    </tr>
                    <tr>
                    <td>
                <?  echo $nameArray_approved_comments_row[csf('comments')];  ?>
                </td>
                </tr>
                </table>
                   
                </td>
            </tr>
        </table>
        <br/>


 		<table  width="100%"  border="0" cellpadding="0" cellspacing="0">
            <table align="left" cellspacing="0" width="<? echo $width?>"  border="1" rules="all" class="rpt_table" >
            
            <?
				   $sql_req=("select gmts_color_id as gmts_color,gmts_size,sum(bh_qty) as bh_qty,sum(rf_qty) as rf_qty  FROM wo_booking_dtls  WHERE booking_no=$txt_booking_no  and status_active=1 and is_deleted=0 and gmts_size!=0 and bh_qty>0 group by gmts_color_id,gmts_size  order by gmts_size"); 
				$sql_data =sql_select($sql_req);
				$size_array=array();$qnty_array_bh=array();$qnty_array_rf=array();
				foreach($sql_data as $row)
				{
					$size_array[$row[csf('gmts_size')]]=$row[csf('gmts_size')];
					$qnty_array_bh[$row[csf('gmts_color')]][$row[csf('gmts_size')]]=$row[csf('bh_qty')];
					$qnty_array_rf[$row[csf('gmts_color')]][$row[csf('gmts_size')]]=$row[csf('rf_qty')];
					$qnty_array[$row[csf('gmts_color')]][$row[csf('gmts_size')]]=$row[csf('bh_qty')]+$row[csf('rf_qty')];
				}
				 $sql_color=("select gmts_color_id as gmts_color,sum(bh_qty) as bh_qty  FROM wo_booking_dtls  WHERE booking_no=$txt_booking_no  and status_active=1 and is_deleted=0 and gmts_size!=0 and bh_qty>0 group by gmts_color_id  order by gmts_color"); 
				$sql_data_color =sql_select($sql_color);
				$color_array=array();
				foreach($sql_data_color as $row)
				{
					$color_array[$row[csf('gmts_color')]]=$row[csf('gmts_color')];
				}
				 $sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
				 $colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
				 $width=400+(count($size_array)*150);
				 //count($size_array);
				 ?>
            
            
		        <thead align="center">
		         <tr>
		           		 <th align="left" colspan="<? echo count($size_array)+5;?>" width="30"><strong>Sample Requirement</strong></th>
		           </tr>
		            <tr>
		            <th width="30" rowspan="2">SL</th>
		            <th width="80" rowspan="2" align="center">Color/Size</th>
		            <?
		            foreach ($size_array as $sizid)
		            {
		            //$size_count=count($sizid);
		            ?>
		            <th width="" colspan="2"><strong><? echo  $sizearr[$sizid];  ?></strong>
		            </th>
		            
		            <?
		            } ?>
		           <th width="80" rowspan="2" align="center">Total Qnty.</th>
		            </tr>
		            <tr>
		             <?
		            foreach ($size_array as $sizid)
		            {
		            //$size_count=count($sizid);
		            ?>
		            <th width="75"> BH &nbsp;</th> <th width="75"> Rf.&nbsp;</th>
		            <?
		            } ?>
		            </tr>
		        </thead>
		        <tbody>
					<?
		            //$mrr_no=$dataArray[0][csf('issue_number')];
		            $i=1;
		            $tot_qnty=array();
		                foreach($color_array as $cid)
		                {
		                    if ($i%2==0)  
		                        $bgcolor="#E9F3FF";
		                    else
		                        $bgcolor="#FFFFFF";
							$color_count=count($cid);
		                    ?>
		                    <tr>
		                        <td><? echo $i;  ?></td>
		                        <td><? echo $colorarr[$cid]; ?></td>
		                       
		                         <?
								foreach ($size_array as $sizval)
								{
								//$size_count=count($sizid);
								$tot_qnty[$cid]+=$qnty_array[$cid][$sizval];
								$tot_qnty_size_bh[$sizval]+=$qnty_array_bh[$cid][$sizval];
								$tot_qnty_size_rf[$sizval]+=$qnty_array_rf[$cid][$sizval];
								?>
								<td width="75" align="right"> <? echo $qnty_array_bh[$cid][$sizval]; ?></td> <td width="75" align="right"> <? echo $qnty_array_rf[$cid][$sizval]; ?></td>
								<?
								
								} ?>
											
		                        <td align="right"><? echo $tot_qnty[$cid]; ?></td>
		                    </tr>
		                    <?
							$production_quantity+=$tot_qnty[$cid];
							$i++;
		                }
		            ?>
		        </tbody>
		        <tr>
		            <td colspan="2" align="right"><strong>Grand Total :</strong></td>
		            <?
						foreach ($size_array as $sizval)
						{
							?>
		                    <td align="right"><?php echo $tot_qnty_size_bh[$sizval]; ?></td>
		                    <td align="right"><?php echo $tot_qnty_size_rf[$sizval]; ?></td>
		                    <?
						}
					?>
		            <td align="right"><?php echo $production_quantity; ?></td>
		        </tr>                           
		    </table>
       		<br>
        </table>



        <table  width="100%"  border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td width="49%" style="border:solid; border-color:#000; border-width:thin" valign="top">
                    <table  width="100%"  border="0" cellpadding="0" cellspacing="0">
                	<thead>
                    	<tr>
                        	<th width="3%"></th><th width="97%" align="left"><u>Special Instruction</u></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no");// quotation_id='$data'
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							?>
                            	<tr id="settr_1" valign="top">
                                    <td style="vertical-align:top"><? echo $i;?></td>
                                    <td><strong style="font-size:20px"> <? echo $row[csf('terms')]; ?></strong></td>
                                </tr>
                            <?
						}
					}
					/*else
					{
				    $i=0;
					$data_array=sql_select("select id, terms from  lib_terms_condition");// quotation_id='$data'
					foreach( $data_array as $row )
						{
							$i++;
					?>
                    <tr id="settr_1" align="">
                                    <td valign="top">
                                    <? echo $i;?>
                                    </td>
                                    <td>
                                    <? echo $row['terms']; ?>
                                    </td>
                                    
                                </tr>
                    <? 
						}
					} */
					?>
                </tbody>
                </table>
                </td>
                <td width="2%">
                </td>
                <td width="49%" valign="top">
                <?
                if(str_replace("'","",$cbo_fabric_source)==1)
                {
					?>
                   <table width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                   <tr align="center">
                    <td colspan="10"><b>Comments</b></td>
                    
                    </tr>
                    <tr align="center">
                    <td>Sl</td>
                    <td>Po NO</td>
                    <td>Ship Date</td>
                    <td>Pre-Cost Qty</td>
                    <td>Mn.Book Qty</td>
                    <td>Sht.Book Qty</td>
                    <td>Smp.Book Qty</td>
                    <td>Tot.Book Qty</td>
                    <td>Balance</td>
                    <td>Comments</td>
                    </tr>
                    <?
	$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	if ($cbo_fabric_natu!=0) $cbo_fabric_natu="and a.fab_nature_id='$cbo_fabric_natu'"; 
	if ($cbo_fabric_source!=0) $cbo_fabric_source_cond="and a.fabric_source='$cbo_fabric_source'"; 
	$paln_cut_qnty_array=return_library_array( "select min(id) as id,sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown  where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and is_deleted=0 and status_active=1 group by color_mst_id,size_mst_id,item_mst_id", "id", "plan_cut_qnty");
	
	$item_ratio_array=return_library_array( "select gmts_item_id,set_item_ratio from wo_po_details_mas_set_details  where job_no =$txt_job_no", "gmts_item_id", "set_item_ratio");
	$nameArray=sql_select("
	select
	a.id,
	a.item_number_id,
	a.costing_per,
	b.po_break_down_id,
	b.color_size_table_id,
	b.requirment,
	c.po_number
FROM
	wo_pre_cost_fabric_cost_dtls a,
	wo_pre_cos_fab_co_avg_con_dtls b,
	wo_po_break_down c
WHERE
	a.job_no=b.job_no and
	a.job_no=c.job_no_mst and
    a.id=b.pre_cost_fabric_cost_dtls_id and
	b.po_break_down_id=c.id and
	b.po_break_down_id in (".str_replace("'","",$txt_order_no_id).")  $cbo_fabric_natu $cbo_fabric_source_cond and a.status_active=1 and a.is_deleted=0
	order by a.id");
	$count=0;
	$tot_grey_req_as_pre_cost_arr=array();
	foreach ($nameArray as $result)
	{
		if (count($nameArray)>0 )
		{
            if($result[csf("costing_per")]==1)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(12*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
			}
			if($result[csf("costing_per")]==2)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(1*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
			}
			if($result[csf("costing_per")]==3)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(24*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
			}
			if($result[csf("costing_per")]==4)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(36*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
			}
			if($result[csf("costing_per")]==5)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(48*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
			}
			$tot_grey_req_as_pre_cost_arr[$result[csf("po_number")]]+=$tot_grey_req_as_pre_cost;
        }
    }
	                $total_pre_cost=0;
					$total_booking_qnty_main=0;
					$total_booking_qnty_short=0;
					$total_booking_qnty_sample=0;
					$total_tot_bok_qty=0;
					$tot_balance=0;
					/*$booking_qnty=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and booking_type in(1,4)  and a.status_active=1 and a.is_deleted=0 group by b.po_number order by a.po_break_down_id", "po_break_down_id", "grey_fab_qnty");*/
					$booking_qnty_main=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b, wo_booking_mst c where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no  and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and a.booking_type =1 and c.fabric_source=$cbo_fabric_source and a.is_short=2 and c.item_category=2 and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");
					
					$booking_qnty_short=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b , wo_booking_mst c where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and a.booking_type =1 and c.fabric_source=$cbo_fabric_source and c.item_category=2 and a.is_short=1 and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");
					$booking_qnty_sample=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b , wo_booking_mst c  where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and a.booking_type =4 and c.fabric_source=$cbo_fabric_source and c.item_category=2  and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");
					//echo "select max(a.id) as id,  a.po_number,max(a.pub_shipment_date) as pub_shipment_date,sum(a.plan_cut) as plan_cut  from wo_po_break_down a,wo_pre_cost_sum_dtls b,wo_pre_cost_mst c where a.job_no_mst=b.job_no and a.job_no_mst=c.job_no and a.id in(".str_replace("'","",$txt_order_no_id).") group by a.po_number order by id";
					$sql_data=sql_select( "select max(a.id) as id,  a.po_number,max(a.pub_shipment_date) as pub_shipment_date,sum(a.plan_cut) as plan_cut  from wo_po_break_down a,wo_pre_cost_sum_dtls b,wo_pre_cost_mst c where a.job_no_mst=b.job_no and a.job_no_mst=c.job_no and a.id in(".str_replace("'","",$txt_order_no_id).") group by a.po_number order by id");
					foreach($sql_data  as $row)
                    {
					$col++;
					?>
                    <tr align="center">
                    <td><? echo $col; ?></td>
                    <td><? echo $row[csf("po_number")]; ?></td>
                     <td><? echo change_date_format($row[csf("pub_shipment_date")],"dd-mm-yyyy",'-'); ?></td>
                    <td align="right"><? echo number_format($tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]],2); $total_pre_cost+=$tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]]; ?></td>
                    <td align="right"><? echo number_format($booking_qnty_main[$row[csf("id")]],2); $total_booking_qnty_main+=$booking_qnty_main[$row[csf("id")]];?></td>
                    <td align="right"><? echo number_format($booking_qnty_short[$row[csf("id")]],2); $total_booking_qnty_short+=$booking_qnty_short[$row[csf("id")]];?></td>
                    <td align="right"><? echo number_format($booking_qnty_sample[$row[csf("id")]],2); $total_booking_qnty_sample+=$booking_qnty_sample[$row[csf("id")]];?></td>
                    <td align="right"><? $tot_bok_qty=$booking_qnty_main[$row[csf("id")]]+$booking_qnty_short[$row[csf("id")]]+$booking_qnty_sample[$row[csf("id")]]; echo number_format($tot_bok_qty,2); $total_tot_bok_qty+=$tot_bok_qty;?></td>
                    <td align="right">
					<? $balance= def_number_format($tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]]-$tot_bok_qty,2,""); echo number_format($balance,2); $tot_balance+= $balance?>
                    </td>
                    <td>
					<? 
					if( $balance>0)
					{
						echo "Less Booking";
					}
					else if ($balance<0) 
					{
						echo "Over Booking";
					} 
					else
					{
						echo "";
					}
					?>
                    </td>
                    </tr>
                    <?
					}
					?>
                    <tfoot>
                    
                    <tr>
                    <td colspan="3">Total:</td>
                    
                    <td align="right"><? echo number_format($total_pre_cost,2); ?></td>
                    <td align="right"><? echo number_format($total_booking_qnty_main,2); ?></td>
                    <td align="right"><? echo number_format($total_booking_qnty_short,2); ?></td>
                    <td align="right"><? echo number_format($total_booking_qnty_sample,2); ?></td>
                     <td align="right"><? echo number_format($total_tot_bok_qty,2); ?></td>
                    <td align="right"><? echo number_format($tot_balance,2); ?></td>
                    <td></td>
                    </tr>
                    </tfoot>
                    </table>
                    <?
				}
					?>
                </td>
                
            </tr>
        </table>
        
          <?
		 	echo signature_table(5, $cbo_company_name, "1330px");
			echo "****".custom_file_name($varcode_booking_no,$style_sting,$txt_job_no);
		 ?>
       </div>
	<script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
    fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
    </script>
       <?
      
} //First button end

if($action=="show_fabric_booking_report2")
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
    $marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$po_qnty_tot=return_field_value( "sum(po_quantity)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	$sample_name_name_arr=return_library_array( "select id,sample_name from    lib_sample",'id','sample_name');
	?>
	<div style="width:1330px" align="center">       
    										<!--    Header Company Information         --> 
    <?php
		$nameArray_approved=sql_select( "select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=13"); 
		list($nameArray_approved_row)=$nameArray_approved;
		$nameArray_approved_date=sql_select( "select b.approved_date as approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=13 and b.approved_no='".$nameArray_approved_row[csf('approved_no')]."'");
		list($nameArray_approved_date_row)=$nameArray_approved_date;
		$nameArray_approved_comments=sql_select( "select b.comments as comments from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=13 and b.approved_no='".$nameArray_approved_row[csf('approved_no')]."'");
		list($nameArray_approved_comments_row)=$nameArray_approved_comments;
    ?>	
       <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black">
           <tr>
               <td width="100"> 
              	 <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               </td>
               <td width="1000">                                     
                    <table width="100%" cellpadding="0" cellspacing="0" >
                        <tr>
                            <td align="center" style="font-size:20px;"><? echo $company_library[$cbo_company_name]; ?></td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px">  
                            <?
								$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website from lib_company where id=$cbo_company_name"); 
								foreach ($nameArray as $result)
								{ 
									?>
									Plot No: <? echo $result[csf('plot_no')]; ?> 
									Level No: <? echo $result[csf('level_no')]?>
									Road No: <? echo $result[csf('road_no')]; ?> 
									Block No: <? echo $result[csf('block_no')];?> 
									City No: <? echo $result[csf('city')];?> 
									Zip Code: <? echo $result[csf('zip_code')]; ?> 
									Province No: <?php echo $result[csf('province')];?> 
									Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
									Email Address: <? echo $result[csf('email')];?> 
									Website No: <? echo $result[csf('website')];
								}
                            ?>   
                            </td> 
                        </tr>
                        <tr>
                            <td align="center" style="font-size:20px">  
                            	<strong><? echo $report_title;?> &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:#F00"><? if(str_replace("'","",$id_approved_id) ==1){ echo "(Approved)";}else{echo "";}; ?> </font></strong>
                            </td> 
                            
                              <td> 
                              <?
								 if($nameArray_approved_row[csf('approved_no')]>1)
								 {
								 ?>
								 <strong> Revised No: <? echo $nameArray_approved_row[csf('approved_no')]-1; ?></strong>
                                  <br/>
								 
								  <?
								 }
							  	?>
                             </td>
                        </tr>
                    </table>
                </td>
                 <td width="250" id="barcode_img_id"> 
                 
               </td>       
            </tr>
       </table>
		<?
        $job_no='';
        $total_set_qnty=0;
        $colar_excess_percent=0;
        $cuff_excess_percent=0;
        $nameArray=sql_select( "select a.booking_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.po_break_down_id,a.colar_excess_percent,a.cuff_excess_percent,a.fabric_composition,a.delivery_date,a.is_apply_last_update,a.pay_mode,a.fabric_source,b.job_no,b.buyer_name, b.style_ref_no ,b.gmts_item_id,b.order_uom,b.total_set_qnty,b.style_description,b.season,b.product_dept,b.product_code,b.pro_sub_dep,b.dealing_marchant from wo_booking_mst a, wo_po_details_master b where  a.job_no=b.job_no and a.booking_no=$txt_booking_no and a.company_id='$cbo_company_name' and b.company_name='$cbo_company_name'"); //2 compnay check for sample job is FAL-15-00586 in development
        foreach ($nameArray as $result)
        {
			$total_set_qnty=$result[csf('total_set_qnty')];
			$colar_excess_percent=$result[csf('colar_excess_percent')];
			$cuff_excess_percent=$result[csf('cuff_excess_percent')];
			$po_no="";$file_no="";$ref_no="";
			$shipment_date="";
			 $sql_po= "select po_number,grouping,file_no,MIN(pub_shipment_date) pub_shipment_date from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].") group by po_number,grouping,file_no"; 
			$data_array_po=sql_select($sql_po);
			foreach ($data_array_po as $row_po)
			{
				$po_no.=$row_po[csf('po_number')].", ";
				$shipment_date.=change_date_format($row_po[csf('pub_shipment_date')],'dd-mm-yyyy','-').", ";
				$ref_no=$row_po[csf('grouping')].",";
				$file_no=$row_po[csf('file_no')].",";
			}
      		 //$file_no= rtrim($file_no,','); $ref_no= rtrim($ref_no,',');
			$lead_time="";
			if($db_type==0)
			{
				$sql_lead_time= "select DATEDIFF(pub_shipment_date,po_received_date) date_diff from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")"; 
			}
        
			if($db_type==2)
			{
				$sql_lead_time= "select (pub_shipment_date-po_received_date) date_diff from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")"; 
			}
			$data_array_lead_time=sql_select($sql_lead_time);
			foreach ($data_array_lead_time as $row_lead_time)
			{
				$lead_time.=$row_lead_time[csf('date_diff')].",";
				//$shipment_date.=change_date_format($row_po['pub_shipment_date'],'dd-mm-yyyy','-').",";
			}
        
			$po_received_date="";
			$sql_po_received_date= "select MIN(po_received_date) as po_received_date from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")"; 
			$data_array_po_received_date=sql_select($sql_po_received_date);
			foreach ($data_array_po_received_date as $row_po_received_date)
			{
				$po_received_date=change_date_format($row_po_received_date[csf('po_received_date')],'dd-mm-yyyy','-');
				//$shipment_date.=change_date_format($row_po['pub_shipment_date'],'dd-mm-yyyy','-').",";
			}
        
			$sql_po= "select po_number,MIN(pub_shipment_date) pub_shipment_date, MIN(insert_date) as insert_date,shiping_status from wo_po_break_down  where id in(".$result[csf('po_break_down_id')].") group by po_number,shiping_status"; 
			$data_array_po=sql_select($sql_po);
			foreach ($data_array_po as $rows)
			{
				$daysInHand.=(datediff('d',$result[csf('delivery_date')],$rows[csf('pub_shipment_date')])-1).",";
				$booking_date=$result[csf('update_date')];
				if($booking_date=="" || $booking_date=="0000-00-00 00:00:00")
				{
					$booking_date=$result[csf('insert_date')];
				}
				$WOPreparedAfter.=(datediff('d',$rows[csf('insert_date')],$booking_date)-1).",";
				
				if($rows[csf('shiping_status')]==1)
				{
					$shiping_status.= "FP".",";
				}
				else if($rows[csf('shiping_status')]==2)
				{
					$shiping_status.= "PS".",";
				}
				else if($rows[csf('shiping_status')]==3)
				{
					$shiping_status.= "FS".",";
				}
			}
			
			$varcode_booking_no=$result[csf('booking_no')];
			if($result[csf('style_ref_no')])$style_sting.=$result[csf('style_ref_no')].'_';
			
        ?>
            <table width="100%" style="border:1px solid black" >                    	
                <tr>
                	<td colspan="6" valign="top" style="font-size:18px; color:#F00"><? if($result[csf('is_apply_last_update')]==2){echo "Booking Info not synchronized with order entry and pre-costing. order entry or pre-costing has updated after booking entry.  Contact to ".$marchentrArr[$result[csf('dealing_marchant')]]; } else{ echo "";} ?></td>
                </tr>                                                
                <tr>
                    <td width="100"><span style="font-size:18px"><b>Buyer/Agent Name</b></span></td>
                    <td width="110">:&nbsp;<span style="font-size:18px"><b><? echo $buyer_name_arr[$result[csf('buyer_name')]]; ?></b></span></td>
                    <td width="100"><span style="font-size:12px"><b>Dept.</b></span></td>
                    <td width="110">:&nbsp;<? echo $product_dept[$result[csf('product_dept')]] ; if($result[csf('product_code')] !=""){ echo " (".$result[csf('product_code')].")";} if($result[csf('pro_sub_dep')] !=0){ echo " (".$pro_sub_dept_array[$result[csf('pro_sub_dep')]].")";}?></td>	
                    <td width="100"><span style="font-size:12px"><b>Order Qnty</b></span></td>
                    <td width="110">:&nbsp;<? echo $po_qnty_tot." ".$unit_of_measurement[$result[csf('order_uom')]] ; ?></td>
                </tr>
                <tr>
                    <td width="100" style="font-size:12px"><b>Garments Item</b></td>
                    <td width="110">:&nbsp;
                    <? 
						$gmts_item_name="";
						$gmts_item=explode(',',$result[csf('gmts_item_id')]);
						for($g=0;$g<=count($gmts_item); $g++)
						{
							$gmts_item_name.= $garments_item[$gmts_item[$g]].",";
						}
						echo rtrim($gmts_item_name,',');
                    ?>
                    </td>
                    <td width="100" style="font-size:12px"><b>Booking Date</b></td>
                    <td width="110">:&nbsp;<? echo change_date_format($result[csf('booking_date')],'dd-mm-yyyy','-');?>&nbsp;&nbsp;&nbsp;</td>
                    <td width="100" style="font-size:18px"><b>Style Ref.</b>   </td>
                    <td width="110" style="font-size:18px">:&nbsp;<b><? echo $result[csf('style_ref_no')];?> </b>   </td>
                </tr>
                <tr>
                    <td  width="100" style="font-size:12px"><b>Style Des.</b></td>
                    <td  width="110" >:&nbsp;<? echo $result[csf('style_description')]; $job_no= $result[csf('job_no')];?></td>
                    <td width="100" style="font-size:12px"><b>Lead Time </b>   </td>
                    <td width="110">:&nbsp;<?  echo rtrim($lead_time,",");;?> </td>
                    <td width="100" style="font-size:12px"><b>Dealing Merchant</b></td>
                    <td width="110">:&nbsp;<? echo $marchentrArr[$result[csf('dealing_marchant')]]; ?></td>
                </tr>
                <tr>
                    <td width="100" style="font-size:12px"><b>Supplier Name</b>   </td>
                    <td width="110">:&nbsp;<? 
					if($result[csf('pay_mode')]==5 || $result[csf('pay_mode')]==3){
					echo $company_library[$result[csf('supplier_id')]];
					}
					else{
					echo $supplier_name_arr[$result[csf('supplier_id')]];
					}
					//echo $supplier_name_arr[$result[csf('supplier_id')]];?>    </td>
                    <td width="100" style="font-size:12px"><b>Fab. Delivery Date</b></td>
                    <td width="110">:&nbsp;<? echo change_date_format( $result[csf('delivery_date')],'dd-mm-yyyy','-');?></td> 
                    <td width="100" style="font-size:18px"><b>Booking No </b>   </td>
                    <td width="110" style="font-size:18px">:&nbsp;<b><? echo $result[csf('booking_no')];?></b><? echo "(".$fabric_source[$result[csf('fabric_source')]].")"?></td>
                </tr> 
                <tr>
                    <td width="100" style="font-size:12px"><b>Season</b></td>
                    <td width="110">:&nbsp;<? echo $result[csf('season')]; ?></td>
                    <td width="100" style="font-size:12px"><b>Attention</b></td>
                    <td width="110" >:&nbsp;<? echo $result[csf('attention')]; ?></td>
                    <td width="100" style="font-size:12px"><b>Po Received Date</b></td>
                    <td width="110" >:&nbsp;<? echo $po_received_date; ?></td>
                </tr>  
                <tr>
                    <td width="100" style="font-size:18px"><b>Order No</b></td>
                    <td width="110" style="font-size:18px" colspan="5">:&nbsp;<b><? echo rtrim($po_no,", "); ?></b></td>
                </tr> 
                <tr>
                    <td width="100" style="font-size:12px"><b>Shipment Date</b></td>
                    <td width="110" colspan="5"> :&nbsp;<? echo rtrim($shipment_date,", "); ?></td>
                </tr> 
                <tr>
                    <td width="110" style="font-size:12px"><b>WO Prepared After</b></td>
                    <td width="300"> :&nbsp;<? echo rtrim($WOPreparedAfter,',').' Days' ?></td>
                    <td width="100" style="font-size:12px"><b>Ship.days in Hand</b></td>
                    <td width="300"> :&nbsp;<? echo rtrim($daysInHand,',').' Days'?></td>
                    <td width="100" style="font-size:12px"><b>Ex-factory status</b></td>
                    <td> :&nbsp;<? echo rtrim($shiping_status,','); ?></td>
                </tr> 
                 <tr>
                    <td width="110" style="font-size:12px"><b>File No</b></td>
                    <td width="300"> :&nbsp;<? echo rtrim($file_no,','); ?></td>
                    <td width="100" style="font-size:12px"><b>Internal Ref.</b></td>
                    <td width="300"> :&nbsp;<? echo rtrim($ref_no,',');?></td>
                    <td width="100" style="font-size:12px"></td>
                    <td>&nbsp;</td>
                </tr>
                
                <tr>
                    <td width="110" style="font-size:12px"><b>Fabric Composition</b></td>
                    <td  colspan="5">: &nbsp;<? echo $result[csf('fabric_composition')]; ?></td>
                    
                </tr>
            </table> 
        <?
		}
		?>
      <br/>   									 <!--  Here will be the main portion  -->
     <style>
		 .main_table tr th{
			 border:1px solid black;
			 font-size:13px;
			 outline: 0;
		 }
		  .main_table tr td{
			 border:1px solid black;
			 font-size:13px;
			 outline: 0;
		 }
	</style>
    <?
	$costing_per="";
	$costing_per_qnty=0;
	$costing_per_id=return_field_value( "costing_per", "wo_pre_cost_mst","job_no ='$job_no'");
	if($costing_per_id==1)
	{
		$costing_per="1 Dzn";
		$costing_per_qnty=12;
	}
	if($costing_per_id==2)
	{
		$costing_per="1 Pcs";
		$costing_per_qnty=1;
	}
	if($costing_per_id==3)
	{
		$costing_per="2 Dzn";
		$costing_per_qnty=24;
	}
	if($costing_per_id==4)
	{
		$costing_per="3 Dzn";
		$costing_per_qnty=36;
	}
	if($costing_per_id==5)
	{
		$costing_per="4 Dzn";
		$costing_per_qnty=48;
	}
	//$process_loss_method=return_field_value( "process_loss_method", "wo_pre_cost_fabric_cost_dtls","job_no='$job_no'");
			
	$process_loss_method=return_field_value("process_loss_method", "variable_order_tracking", "company_name=$cbo_company_name and variable_list=18 and item_category_id=$cbo_fabric_natu and status_active=1 and is_deleted=0");

	if(str_replace("'","",$cbo_fabric_source)==1)
	{
		$nameArray_fabric_description= sql_select("SELECT a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width,b.process_loss_percent FROM wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls b where b.booking_no =$txt_booking_no and a.id=b.pre_cost_fabric_cost_dtls_id  and b.status_active=1 and	
	b.is_deleted=0  group by a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width,b.process_loss_percent order by a.body_part_id");
	    ?>
        <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
            <tr align="center"><th colspan="3" align="left">Body Part</th>
				<? 
                foreach($nameArray_fabric_description  as $result_fabric_description)
                {
                    if( $result_fabric_description[csf('body_part_id')] == "")  echo "<td  colspan='2'>&nbsp</td>";	
                    else echo "<td  colspan='2'>". $body_part[$result_fabric_description[csf('body_part_id')]]."</td>";			
                }
                ?>
            	<td rowspan="8" width="50"><p>Total  Finish Fabric <? if(trim($cbo_fabric_natu ,"'")==2){echo "(KG)";}else{echo "(Yds)";} ?></p></td> <td  rowspan="8" width="50"><p>Total Grey Fabric <? if(trim($cbo_fabric_natu ,"'")==2){echo "(KG)";}else{echo "(Yds)";} ?>)</p></td>
            	<td rowspan="8" width="50"><p>Process Loss %</p></td>
            </tr> 
            <tr align="center"><th colspan="3" align="left">Color Type</th>
                <? 
                foreach($nameArray_fabric_description  as $result_fabric_description)
                {
					if( $result_fabric_description[csf('color_type_id')] == "")  echo "<td  colspan='2'>&nbsp</td>";	
					else echo "<td  colspan='2'>". $color_type[$result_fabric_description[csf('color_type_id')]]."</td>";			
                }
                ?>
            </tr>  
            <tr align="center"><th colspan="3" align="left">Construction</th>
				<? 
                foreach($nameArray_fabric_description  as $result_fabric_description)
                {
					if( $result_fabric_description[csf('construction')] == "")  echo "<td  colspan='2'>&nbsp</td>";	
					else echo "<td  colspan='2'>". $result_fabric_description[csf('construction')]."</td>";			
                }
                ?>
            </tr>       
            <tr align="center"><th   colspan="3" align="left">Composition</th>
				<? 
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
					if( $result_fabric_description[csf('composition')] == "")   echo "<td colspan='2' >&nbsp</td>";
					else echo "<td colspan='2' >".$result_fabric_description[csf('composition')]."</td>";			
                }
                ?>
            </tr>
            <tr align="center"><th  colspan="3" align="left">GSM</th>
				<? 
                foreach($nameArray_fabric_description  as $result_fabric_description)
                {
					if( $result_fabric_description[csf('gsm_weight')] == "")   echo "<td colspan='2'>&nbsp</td>";
					else echo "<td colspan='2' align='center'>". $result_fabric_description[csf('gsm_weight')]."</td>";			
                }
                ?>
            </tr>
            <tr align="center"><th   colspan="3" align="left">Dia/Width</th>
				<? 
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
                    if( $result_fabric_description[csf('dia_width')] == "")   echo "<td colspan='2'>&nbsp</td>";
                    else echo "<td colspan='2' align='center'>". $result_fabric_description[csf('dia_width')]."</td>";			
                }
                ?>
            </tr>
            <tr align="center"><th  colspan="3" align="left">Process Loss%</th>
				<? 
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
					if( $result_fabric_description[csf('process_loss_percent')] == "")   echo "<td colspan='2'>&nbsp</td>";
					else echo "<td align='center' colspan='2'>". $result_fabric_description[csf('process_loss_percent')]."</td>";			
                }
                ?>
            </tr>
            <tr>
                <th  width="100" align="left">Sample Name</th>
                <th  width="100" align="left">Fabric Color</th>
                <th  width="100" align="left">Lapdip No</th>
				<? 
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
                    echo "<th width='50'>Finish</th><th width='50' >Gray</th>";			
                }
                ?>
            </tr>
       		<?
	        $grand_total_fin_fab_qnty=0;
			$grand_total_grey_fab_qnty=0;
			$grand_totalcons_per_finish=0;
			$grand_totalcons_per_grey=0;
			if($db_type==0) $sample_type_id="group_concat(sample_type)";
			else if($db_type==2) $sample_type_id="listagg((cast(sample_type as varchar2(4000))),',') within group (order by sample_type)";
			
			$color_wise_wo_sql=sql_select("select job_no, fabric_color_id, $sample_type_id as sample_type
										  FROM 
										  wo_booking_dtls
										  WHERE 
										  booking_no =$txt_booking_no and
										  status_active=1 and
                                          is_deleted=0
										  group by job_no, fabric_color_id");
			foreach($color_wise_wo_sql as $color_wise_wo_result)
			{
				?> 
				<tr>
                    <td  width="100" align="left">
						<?
                        $sample_type_val="";
                        $ex_sample_type=array_unique(explode(",",$color_wise_wo_result[csf('sample_type')]));
                        foreach($ex_sample_type as $sm_val)
                        {
                        	if($sample_type_val=="") $sample_type_val=$sample_name_name_arr[$sm_val]; else $sample_type_val.=','.$sample_name_name_arr[$sm_val];
                        }
                   		echo $sample_type_val; 
                    ?></td>
                    <td  width="100" align="left">
						<?
                        echo $color_library[$color_wise_wo_result[csf('fabric_color_id')]];
                        ?>
                    </td>
                    <td  width="100" align="left">
						<? 
                        $lapdip_no="";
                        $lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='".$color_wise_wo_result[csf('job_no')]."' and approval_status=3 and color_name_id=".$color_wise_wo_result[csf('fabric_color_id')]."","lapdip_no");
                        if($lapdip_no=="") echo "&nbsp;"; echo $lapdip_no; 
                        ?>
                    </td>
                    <?
                    $total_fin_fab_qnty=0;
                    $total_grey_fab_qnty=0;
                    foreach($nameArray_fabric_description as $result_fabric_description)
                    {
						if($db_type==0)
						{
							$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty
															  FROM 
															  wo_pre_cost_fabric_cost_dtls a,
															  wo_booking_dtls b 
															  WHERE 
															  b.booking_no =$txt_booking_no  and
															  a.id=b.pre_cost_fabric_cost_dtls_id and
															  a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
															  a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and 
															  a.construction='".$result_fabric_description[csf('construction')]."' and 
															  a.composition='".$result_fabric_description[csf('composition')]."' and 
															  a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and 
															  b.dia_width='".$result_fabric_description[csf('dia_width')]."' and 
															  b.process_loss_percent='".$result_fabric_description[csf('process_loss_percent')]."' and 
															  b.fabric_color_id=".$color_wise_wo_result[csf('fabric_color_id')]." and
															  b.status_active=1 and
															  b.is_deleted=0");
						}
						if($db_type==2)
						{
							$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty
															  FROM 
															  wo_pre_cost_fabric_cost_dtls a,
															  wo_booking_dtls b 
															  WHERE 
															  b.booking_no =$txt_booking_no  and
															  a.id=b.pre_cost_fabric_cost_dtls_id and
															  nvl(a.body_part_id,0)=nvl('".$result_fabric_description[csf('body_part_id')]."',0) and
															  nvl(a.color_type_id,0)=nvl('".$result_fabric_description[csf('color_type_id')]."',0) and 
															  nvl(a.construction,0)=nvl('".$result_fabric_description[csf('construction')]."',0) and 
															  nvl(a.composition,0)=nvl('".$result_fabric_description[csf('composition')]."',0) and 
															  nvl(a.gsm_weight,0)=nvl('".$result_fabric_description[csf('gsm_weight')]."',0) and 
															  nvl(b.dia_width,0)=nvl('".$result_fabric_description[csf('dia_width')]."',0) and 
															  nvl(b.process_loss_percent,0)=nvl('".$result_fabric_description[csf('process_loss_percent')]."',0) and 
															  nvl(b.fabric_color_id,0)=nvl(".$color_wise_wo_result[csf('fabric_color_id')].",0) and
															  b.status_active=1 and
															  b.is_deleted=0");
						}
                    	list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
                    ?>
                    <td width='50' align='right'>
						<? 
                        if($color_wise_wo_result_qnty[csf('fin_fab_qnty')]!="")
                        {
							echo number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')]) ;
							$total_fin_fab_qnty+=$color_wise_wo_result_qnty[csf('fin_fab_qnty')];
                        }
                        ?>
                    </td>
                    <td width='50' align='right' > 
						<? 
                        if($color_wise_wo_result_qnty[csf('grey_fab_qnty')]!="")
                        {
							echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')]); 
							$total_grey_fab_qnty+=$color_wise_wo_result_qnty[csf('grey_fab_qnty')];
                        }
                        ?>
                    </td>
                    <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_fin_fab_qnty); $grand_total_fin_fab_qnty+=$total_fin_fab_qnty;?></td>
                    <td align="right"><? echo number_format($total_grey_fab_qnty); $grand_total_grey_fab_qnty+=$total_grey_fab_qnty;?></td>
                    <td align="right">
                    <?
                    
                    if($process_loss_method==1)
                    {
                   		$process_percent=(($total_grey_fab_qnty-$total_fin_fab_qnty)/$total_fin_fab_qnty)*100;
                    }
                    
                    if($process_loss_method==2)
                    {
						//$devided_val = 1-(($total_grey_fab_qnty-$total_fin_fab_qnty)/100);
						//$process_percent=$total_grey_fab_qnty/$devided_val;
						$process_percent=(($total_grey_fab_qnty-$total_fin_fab_qnty)/$total_grey_fab_qnty)*100;
                    }
                    echo number_format($process_percent);
                    ?>
                    </td>
				</tr>
				<?
			}
			?>
			<tr>
				<td  width="120" align="left">&nbsp;</td>
				<td  width="120" align="left">&nbsp;</td>
				<td  width="120" align="left"><strong>Total</strong></td>
				<?
				foreach($nameArray_fabric_description as $result_fabric_description)
				{
					if($db_type==0)
					{
						$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty
															FROM 
															wo_pre_cost_fabric_cost_dtls a,
															wo_booking_dtls b 
															WHERE 
															b.booking_no =$txt_booking_no  and
															a.id=b.pre_cost_fabric_cost_dtls_id and
															a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
															a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and 
															a.construction='".$result_fabric_description[csf('construction')]."' and 
															a.composition='".$result_fabric_description[csf('composition')]."' and 
															a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and 
															b.dia_width='".$result_fabric_description[csf('dia_width')]."' and 
															b.process_loss_percent='".$result_fabric_description[csf('process_loss_percent')]."' and
															b.status_active=1 and
															b.is_deleted=0");
					}
					if($db_type==2)
					{
						$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty
															FROM 
															wo_pre_cost_fabric_cost_dtls a,
															wo_booking_dtls b 
															WHERE 
															b.booking_no =$txt_booking_no  and
															a.id=b.pre_cost_fabric_cost_dtls_id and
															nvl(a.body_part_id,0)=nvl('".$result_fabric_description[csf('body_part_id')]."',0) and
															nvl(a.color_type_id,0)=nvl('".$result_fabric_description[csf('color_type_id')]."',0) and 
															nvl(a.construction,0)=nvl('".$result_fabric_description[csf('construction')]."',0) and 
															nvl(a.composition,0)=nvl('".$result_fabric_description[csf('composition')]."',0) and 
															nvl(a.gsm_weight,0)=nvl('".$result_fabric_description[csf('gsm_weight')]."',0) and 
															nvl(b.dia_width,0)=nvl('".$result_fabric_description[csf('dia_width')]."',0) and 
															nvl(b.process_loss_percent,0)=nvl('".$result_fabric_description[csf('process_loss_percent')]."',0) and
															b.status_active=1 and
															b.is_deleted=0");
					}
					list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
					?>
					<td width='50' align='right'><?  echo number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')]) ;?></td><td width='50' align='right' > <? echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')]);?></td>
				<?
				}
				?>
				<td align="right"><? echo number_format($grand_total_fin_fab_qnty);?></td>
				<td align="right"><? echo number_format($grand_total_grey_fab_qnty);?></td>
				<td align="right">
				<?
					if($process_loss_method==1)
					{
						$totalprocess_percent=(($grand_total_grey_fab_qnty-$grand_total_fin_fab_qnty)/$grand_total_fin_fab_qnty)*100;
					}
					if($process_loss_method==2)
					{
						$totalprocess_percent=(($grand_total_grey_fab_qnty-$grand_total_fin_fab_qnty)/$grand_total_grey_fab_qnty)*100;
					}
					echo number_format($totalprocess_percent);
					?>
				</td>
			</tr> 
        </table>
         <br>
        <table  width="750"  border="0" cellpadding="0" cellspacing="0" style="border:solid; border-color:#000; border-width:thin" align="left">
        <thead>
                <tr>
                    <th width="3%"></th><th width="97%" align="left"><u>Special Instruction</u></th>
                </tr>
         </thead>
           
                    <tbody>
                    <?
					$data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no");// quotation_id='$data'
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							?>
                            	<tr id="settr_1" valign="top">
                                    <td style="vertical-align:top"><? echo $i;?></td>
                                    <td><strong style="font-size:20px"> <? echo $row[csf('terms')]; ?></strong></td>
                                </tr>
                            <?
						}
					}
					
					?>
                </tbody>
              
                </table>
        <br/>
        <?
	 }
	 //echo  $cbo_fabric_source;
	if(str_replace("'","",$cbo_fabric_source)==2)
	{
		$nameArray_fabric_description= sql_select("SELECT a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width,b.process_loss_percent FROM wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls b where b.booking_no =$txt_booking_no and a.id=b.pre_cost_fabric_cost_dtls_id  and b.status_active=1 and	
		b.is_deleted=0  group by a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width,b.process_loss_percent order by a.body_part_id");
		?>
		<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
            <tr align="center"><th colspan="3" align="left">Body Part</th>
				<? 
                foreach($nameArray_fabric_description  as $result_fabric_description)
                {
					if( $result_fabric_description[csf('body_part_id')] == "")  echo "<td  colspan='3'>&nbsp</td>";	
					else echo "<td  colspan='3'>". $body_part[$result_fabric_description[csf('body_part_id')]]."</td>";			
                }
                ?>
                <td rowspan="8" width="50"><p>Total  Fabric <? if(trim($cbo_fabric_natu ,"'")==2){echo "(KG)";}else{echo "(Yds)";} ?></p></td> 
                <td rowspan="8" width="50"><p>Avg. Rate</p></td>
                <td rowspan="8" width="50"><p>Amount</p></td>
            </tr> 
            <tr align="center"><th colspan="3" align="left">Color Type</th>
				<? 
                foreach($nameArray_fabric_description  as $result_fabric_description)
                {
					if( $result_fabric_description[csf('color_type_id')] == "")  echo "<td  colspan='3'>&nbsp</td>";	
					else echo "<td  colspan='3'>". $color_type[$result_fabric_description[csf('color_type_id')]]."</td>";			

                }
                ?>
            </tr>  
            <tr align="center"><th colspan="3" align="left">Construction</th>
				<? 
                foreach($nameArray_fabric_description  as $result_fabric_description)
                {
					if( $result_fabric_description[csf('construction')] == "")  echo "<td  colspan='3'>&nbsp</td>";	
					else echo "<td  colspan='3'>". $result_fabric_description[csf('construction')]."</td>";			
                }
                ?>
            </tr>       
            <tr align="center"><th   colspan="3" align="left">Composition</th>
				<? 
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
					if( $result_fabric_description[csf('composition')] == "")   echo "<td colspan='3' >&nbsp</td>";
					else echo "<td colspan='3' >".$result_fabric_description[csf('composition')]."</td>";			
                }
                ?>
            </tr>
            <tr align="center"><th  colspan="3" align="left">GSM</th>
				<? 
                foreach($nameArray_fabric_description  as $result_fabric_description)
                {
					if( $result_fabric_description[csf('gsm_weight')] == "")   echo "<td colspan='3'>&nbsp</td>";
					else echo "<td colspan='3' align='center'>". $result_fabric_description[csf('gsm_weight')]."</td>";			
                }
                ?>
            </tr>
            <tr align="center"><th   colspan="3" align="left">Dia/Width</th>
				<? 
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
					if( $result_fabric_description[csf('dia_width')] == "")   echo "<td colspan='3'>&nbsp</td>";
					else echo "<td colspan='3' align='center'>". $result_fabric_description[csf('dia_width')]."</td>";			
                }
                ?>
            </tr>
            <tr align="center"><th  colspan="3" align="left">Process Loss%</th>
				<? 
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
					if( $result_fabric_description[csf('process_loss_percent')] == "")   echo "<td colspan='3'>&nbsp</td>";
					else echo "<td align='center' colspan='3'>". $result_fabric_description[csf('process_loss_percent')]."</td>";			
                }
                ?>
            </tr>
            <tr>
                <th  width="100" align="left">Sample Name</th>
                <th  width="100" align="left">Fabric Color</th>
                <th  width="100" align="left">Lapdip No</th>
                <? 
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
               		echo "<th width='50'>Fab Qty</th><th width='50'>Rate</th><th width='50'>Amount</th>";			
                }
                ?>
            </tr>
            <?
            $grand_total_fin_fab_qnty=0;
            $grand_total_grey_fab_qnty=0;
            $grand_totalcons_per_finish=0;
            $grand_totalcons_per_grey=0;
            if($db_type==0) $sample_type_id="group_concat(sample_type)";
            else if($db_type==2) $sample_type_id="listagg((cast(sample_type as varchar2(4000))),',') within group (order by sample_type)";
            
            $color_wise_wo_sql=sql_select("select job_no, fabric_color_id, $sample_type_id as sample_type
											FROM 
											wo_booking_dtls
											WHERE 
											booking_no =$txt_booking_no and
											status_active=1 and
											is_deleted=0
											group by job_no, fabric_color_id");
            foreach($color_wise_wo_sql as $color_wise_wo_result)
			{
				?> 
				<tr>
                    <td  width="100" align="left">
						<?
                        $sample_type_val="";
                        $ex_sample_type=array_unique(explode(",",$color_wise_wo_result[csf('sample_type')]));
                        foreach($ex_sample_type as $sm_val)
                        {
                        	if($sample_type_val=="") $sample_type_val=$sample_name_name_arr[$sm_val]; else $sample_type_val.=','.$sample_name_name_arr[$sm_val];
                        }
                        echo $sample_type_val; 
                    ?></td>
                    <td width="100" align="left">
						<?
                        echo $color_library[$color_wise_wo_result[csf('fabric_color_id')]];
                        ?>
                    </td>
                    <td width="100" align="left">
						<? 
                        $lapdip_no="";
                        $lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='".$color_wise_wo_result[csf('job_no')]."' and approval_status=3 and color_name_id=".$color_wise_wo_result[csf('fabric_color_id')]."","lapdip_no");
                        if($lapdip_no=="") echo "&nbsp;"; echo $lapdip_no; 
                        ?>
                    </td>
                    <?
                    $total_fin_fab_qnty=0;
                    $total_grey_fab_qnty=0;
                    $total_amount=0;
                    foreach($nameArray_fabric_description as $result_fabric_description)
                    {
						if($db_type==0)
						{
							$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty, avg(b.rate) as rate, sum(b.amount) as amount
																FROM 
																wo_pre_cost_fabric_cost_dtls a,
																wo_booking_dtls b 
																WHERE 
																b.booking_no =$txt_booking_no  and
																a.id=b.pre_cost_fabric_cost_dtls_id and
																a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
																a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and 
																a.construction='".$result_fabric_description[csf('construction')]."' and 
																a.composition='".$result_fabric_description[csf('composition')]."' and 
																a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and 
																b.dia_width='".$result_fabric_description[csf('dia_width')]."' and 
																b.process_loss_percent='".$result_fabric_description[csf('process_loss_percent')]."' and 
																b.fabric_color_id=".$color_wise_wo_result[csf('fabric_color_id')]." and
																b.status_active=1 and
																b.is_deleted=0");
						}
						if($db_type==2)
						{
							$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty, avg(b.rate) as rate, sum(b.amount) as amount
																FROM 
																wo_pre_cost_fabric_cost_dtls a,
																wo_booking_dtls b 
																WHERE 
																b.booking_no =$txt_booking_no  and
																a.id=b.pre_cost_fabric_cost_dtls_id and
																nvl(a.body_part_id,0)=nvl('".$result_fabric_description[csf('body_part_id')]."',0) and
																nvl(a.color_type_id,0)=nvl('".$result_fabric_description[csf('color_type_id')]."',0) and 
																nvl(a.construction,0)=nvl('".$result_fabric_description[csf('construction')]."',0) and 
																nvl(a.composition,0)=nvl('".$result_fabric_description[csf('composition')]."',0) and 
																nvl(a.gsm_weight,0)=nvl('".$result_fabric_description[csf('gsm_weight')]."',0) and 
																nvl(b.dia_width,0)=nvl('".$result_fabric_description[csf('dia_width')]."',0) and 
																nvl(b.process_loss_percent,0)=nvl('".$result_fabric_description[csf('process_loss_percent')]."',0) and 
																nvl(b.fabric_color_id,0)=nvl(".$color_wise_wo_result[csf('fabric_color_id')].",0) and
																b.status_active=1 and
																b.is_deleted=0");
						}
						list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
						?>
						<td width='50' align='right' > 
							<? 
                            if($color_wise_wo_result_qnty[csf('grey_fab_qnty')]!="")
                            {
								echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')]); 
								$total_grey_fab_qnty+=$color_wise_wo_result_qnty[csf('grey_fab_qnty')];
                            }
                            ?>
						</td>
						<td width='50' align='right'>
							<? 
                            if($color_wise_wo_result_qnty[csf('rate')]!="")
                            {
								echo number_format($color_wise_wo_result_qnty[csf('rate')]) ;
								//$total_fin_fab_qnty+=$color_wise_wo_result_qnty[csf('fin_fab_qnty')];
                            }
                            ?>
						</td>
						<td width='50' align='right'>
							<? 
                            if($color_wise_wo_result_qnty[csf('amount')]!="")
                            {
								echo number_format($color_wise_wo_result_qnty[csf('amount')]) ;
								//$total_fin_fab_qnty+=$color_wise_wo_result_qnty[csf('fin_fab_qnty')];
                            }
                            ?>
						</td>
						<?
						$total_amount+=$color_wise_wo_result_qnty[csf('amount')];
                    }
                    ?>
                    <td align="right"><? echo number_format($total_grey_fab_qnty); $grand_total_grey_fab_qnty+=$total_grey_fab_qnty;?></td>
                    <td align="right"><? echo number_format($total_amount/$total_grey_fab_qnty); //$grand_total_fin_fab_qnty+=$total_fin_fab_qnty;?></td>
                    <td align="right"><? echo number_format($total_amount); $grand_total_amount+=$total_amount;?></td>
				</tr>
				<?
			}
            ?>
            <tr>
                <td width="100" align="left">&nbsp;</td>
                <td width="100" align="left">&nbsp;</td>
                <td width="100" align="left"><strong>Total</strong></td>
                <?
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
					if($db_type==0)
					{
						$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty, avg(b.rate) as rate, sum(b.amount) as amount
															FROM 
															wo_pre_cost_fabric_cost_dtls a,
															wo_booking_dtls b 
															WHERE 
															b.booking_no =$txt_booking_no  and
															a.id=b.pre_cost_fabric_cost_dtls_id and
															a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
															a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and 
															a.construction='".$result_fabric_description[csf('construction')]."' and 
															a.composition='".$result_fabric_description[csf('composition')]."' and 
															a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and 
															b.dia_width='".$result_fabric_description[csf('dia_width')]."' and 
															b.process_loss_percent='".$result_fabric_description[csf('process_loss_percent')]."' and
															b.status_active=1 and
															b.is_deleted=0");
					}
					if($db_type==2)
					{
						$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty, avg(b.rate) as rate, sum(b.amount) as amount
															FROM 
															wo_pre_cost_fabric_cost_dtls a,
															wo_booking_dtls b 
															WHERE 
															b.booking_no =$txt_booking_no  and
															a.id=b.pre_cost_fabric_cost_dtls_id and
															nvl(a.body_part_id,0)=nvl('".$result_fabric_description[csf('body_part_id')]."',0) and
															nvl(a.color_type_id,0)=nvl('".$result_fabric_description[csf('color_type_id')]."',0) and 
															nvl(a.construction,0)=nvl('".$result_fabric_description[csf('construction')]."',0) and 
															nvl(a.composition,0)=nvl('".$result_fabric_description[csf('composition')]."',0) and 
															nvl(a.gsm_weight,0)=nvl('".$result_fabric_description[csf('gsm_weight')]."',0) and 
															nvl(b.dia_width,0)=nvl('".$result_fabric_description[csf('dia_width')]."',0) and 
															nvl(b.process_loss_percent,0)=nvl('".$result_fabric_description[csf('process_loss_percent')]."',0) and
															b.status_active=1 and
															b.is_deleted=0");
					}
					list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
					?>
					<td width='50' align='right' > <? echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')]);?></td>
					<td width='50' align='right'><?  echo number_format($color_wise_wo_result_qnty[csf('rate')]) ;?></td>
					<td width='50' align='right'><?  echo number_format($color_wise_wo_result_qnty[csf('amount')]) ;?></td>
					<?
                }
                ?>
                <td align="right"><? echo number_format($grand_total_grey_fab_qnty);?></td>
                <td align="right"><? echo number_format($grand_total_amount/$grand_total_grey_fab_qnty);?></td>
                <td align="right"><? echo number_format($grand_total_amount);?></td>
            </tr> 
		</table>
        <br>
        <table  width="750"  border="0" cellpadding="0" cellspacing="0" style="border:solid; border-color:#000; border-width:thin"  align="left">
        
       		 <thead>
                <tr>
                    <th width="3%"></th><th width="97%" align="left"><u>Special Instruction</u></th>
                </tr>
            </thead>
            
                    <tbody>
                    <?
					$data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no");// quotation_id='$data'
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							?>
                            	<tr id="settr_1" valign="top">
                                    <td style="vertical-align:top"><? echo $i;?></td>
                                    <td><strong style="font-size:20px"> <? echo $row[csf('terms')]; ?></strong></td>
                                </tr>
                            <?
						}
					}
					
					?>
                </tbody>
 	</table>
		
	<?
	}
	
		?>
      
        <br/>
        
          <?
		 	echo signature_table(5, $cbo_company_name, "1330px");
			echo "****".custom_file_name($varcode_booking_no,$style_sting,$txt_job_no);
		 ?>
       </div>
	<script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
    fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
    </script>
       <?
   exit();   
}
// button 3
if($action=="show_fabric_booking_report3") //Aziz
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
    $marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$po_qnty_tot=return_field_value( "sum(po_quantity)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	$sample_name_name_arr=return_library_array( "select id,sample_name from    lib_sample",'id','sample_name');
	?>
	<div style="width:1330px" align="center">       
    <?php
		$nameArray_approved=sql_select( "select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=13"); 
		list($nameArray_approved_row)=$nameArray_approved;
		$nameArray_approved_date=sql_select( "select b.approved_date as approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=13 and b.approved_no='".$nameArray_approved_row[csf('approved_no')]."'");
		list($nameArray_approved_date_row)=$nameArray_approved_date;
		$nameArray_approved_comments=sql_select( "select b.comments as comments from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=13 and b.approved_no='".$nameArray_approved_row[csf('approved_no')]."'");
		list($nameArray_approved_comments_row)=$nameArray_approved_comments;
    ?>	
       <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black">
           <tr>
               <td width="200"> 
              	 <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               </td>
               <td width="1000">                                     
                    <table width="100%" cellpadding="0" cellspacing="0" >
                        <tr>
                            <td align="center" style="font-size:20px;"><? echo $company_library[$cbo_company_name]; ?></td>
                             <td rowspan="3" width="350" style="margin-right:-100px;" >
                                <?
								 if($nameArray_approved_row[csf('approved_no')]>1)
								 {
								 ?>
								 <b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp Revised No: &nbsp; <? echo $nameArray_approved_row[csf('approved_no')]-1; ?></b>
                                  <br/>
								  	&nbsp;&nbsp; &nbsp;&nbsp;Approved Date: <? echo $nameArray_approved_date_row[csf('approved_date')]; ?>
								  <?
								 }
							  	?>
                                
                            
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px">  
                            <?
								$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website from lib_company where id=$cbo_company_name"); 
								foreach ($nameArray as $result)
								{ 
									?>
									 <? echo $result[csf('plot_no')].'&nbsp;'; ?> 
									 <? echo $result[csf('level_no')].'&nbsp;';?>
									 <? echo $result[csf('road_no')].'&nbsp;'; ?> 
									<? echo $result[csf('block_no')].'&nbsp;';?> 
									 <? echo $result[csf('city')].'&nbsp;';;?> 
									<? echo $result[csf('zip_code')].'&nbsp;'; ?> 
									 <?php echo $result[csf('province')].'&nbsp;';?> 
									 <? echo $country_arr[$result[csf('country_id')]].'&nbsp;'; ?>
									 <? echo $result[csf('email')].'&nbsp;';?> 
									 <? echo $result[csf('website')].'&nbsp;';
								}
                            ?>   
                            </td> 
                        </tr>
                        <tr>
                            <td align="center" style="font-size:20px">  
                            	<strong><? echo $report_title;?> &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:#F00"><? if(str_replace("'","",$id_approved_id) ==1){ echo "(Approved)";}else{echo "";}; ?> </font></strong>
                            </td> 
                              <td style="font-size:20px" width="150px"> 
                             
                             </td>
                        </tr>
                    </table>
                </td>
                 <td width="250" id="barcode_img_id"> 
                 
               </td>       
            </tr>
       </table>
		<?
		$nameseason=sql_select("select a.season, b.buyer_req_no,a.bh_merchant,a.style_ref_no,a.product_code,a.product_dept  from  sample_development_mst a, sample_development_dtls b  where  a.id=b.sample_mst_id ");
				$bh_merchant_arr=array();
				foreach ($nameseason as $season_row)
				{
					//$season=$season_row[csf('season')];
					//$buyer_req_no=$season_row[csf('buyer_req_no')];
					$bh_merchant_arr[$season_row[csf('style_ref_no')]]['style']=$season_row[csf('bh_merchant')];
					//$style_ref_no=$season_row[csf('style_ref_no')];
					//$product_code=$season_row[csf('product_code')];
					//$product_department=$product_dept[$season_row[csf('product_dept')]];
					
				}
				
        $job_no='';
        $total_set_qnty=0;
        $colar_excess_percent=0;
        $cuff_excess_percent=0;
        $nameArray=sql_select( "select a.booking_no,a.booking_date,a.supplier_id,a.pay_mode,a.currency_id,a.exchange_rate,a.attention,a.tagged_booking_no,b.bh_merchant,a.delivery_date,a.po_break_down_id,a.colar_excess_percent,a.cuff_excess_percent,a.fabric_composition,a.pay_mode,a.delivery_date,a.is_apply_last_update,a.fabric_source,b.job_no,b.buyer_name, b.style_ref_no ,b.gmts_item_id,b.order_uom,b.total_set_qnty,b.style_description,b.season,b.product_dept,b.product_code,b.pro_sub_dep,b.dealing_marchant from wo_booking_mst a, wo_po_details_master b where  a.job_no=b.job_no and a.booking_no=$txt_booking_no and a.company_id='$cbo_company_name' and b.company_name='$cbo_company_name'"); //2 compnay check for sample job is FAL-15-00586 in development
        foreach ($nameArray as $result)
        {
			$total_set_qnty=$result[csf('total_set_qnty')];
			$colar_excess_percent=$result[csf('colar_excess_percent')];
			$cuff_excess_percent=$result[csf('cuff_excess_percent')];
			$po_no="";$file_no="";$ref_no="";
			$shipment_date="";
			 $sql_po= "select po_number,grouping,file_no,MIN(pub_shipment_date) pub_shipment_date from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].") group by po_number,grouping,file_no"; 
			$data_array_po=sql_select($sql_po);
			foreach ($data_array_po as $row_po)
			{
				$po_no.=$row_po[csf('po_number')].", ";
				$shipment_date.=change_date_format($row_po[csf('pub_shipment_date')],'dd-mm-yyyy','-').", ";
				$ref_no=$row_po[csf('grouping')].",";
				$file_no=$row_po[csf('file_no')].",";
			}
      		 //$file_no= rtrim($file_no,','); $ref_no= rtrim($ref_no,',');
			$lead_time="";
			if($db_type==0)
			{
				$sql_lead_time= "select DATEDIFF(pub_shipment_date,po_received_date) date_diff from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")"; 
			}
        
			if($db_type==2)
			{
				$sql_lead_time= "select (pub_shipment_date-po_received_date) date_diff from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")"; 
			}
			$data_array_lead_time=sql_select($sql_lead_time);
			foreach ($data_array_lead_time as $row_lead_time)
			{
				$lead_time.=$row_lead_time[csf('date_diff')].",";
				//$shipment_date.=change_date_format($row_po['pub_shipment_date'],'dd-mm-yyyy','-').",";
			}
        
			$po_received_date="";
			$sql_po_received_date= "select MIN(po_received_date) as po_received_date from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")"; 
			$data_array_po_received_date=sql_select($sql_po_received_date);
			foreach ($data_array_po_received_date as $row_po_received_date)
			{
				$po_received_date=change_date_format($row_po_received_date[csf('po_received_date')],'dd-mm-yyyy','-');
				//$shipment_date.=change_date_format($row_po['pub_shipment_date'],'dd-mm-yyyy','-').",";
			}
        
			$sql_po= "select po_number,MIN(pub_shipment_date) pub_shipment_date, MIN(insert_date) as insert_date,shiping_status from wo_po_break_down  where id in(".$result[csf('po_break_down_id')].") group by po_number,shiping_status"; 
			$data_array_po=sql_select($sql_po);
			foreach ($data_array_po as $rows)
			{
				$daysInHand.=(datediff('d',$result[csf('delivery_date')],$rows[csf('pub_shipment_date')])-1).",";
				$booking_date=$result[csf('update_date')];
				if($booking_date=="" || $booking_date=="0000-00-00 00:00:00")
				{
					$booking_date=$result[csf('insert_date')];
				}
				$WOPreparedAfter.=(datediff('d',$rows[csf('insert_date')],$booking_date)-1).",";
				
				if($rows[csf('shiping_status')]==1)
				{
					$shiping_status.= "FP".",";
				}
				else if($rows[csf('shiping_status')]==2)
				{
					$shiping_status.= "PS".",";
				}
				else if($rows[csf('shiping_status')]==3)
				{
					$shiping_status.= "FS".",";
				}
			}
			
			$varcode_booking_no=$result[csf('booking_no')];
			if($result[csf('style_ref_no')])$style_sting.=$result[csf('style_ref_no')].'_';
			
        ?>
            <table width="100%" style="border:1px solid black" >                    	
                <tr>
                	<td colspan="6" valign="top" style="font-size:18px; color:#F00"><? if($result[csf('is_apply_last_update')]==2){echo "Booking Info not synchronized with order entry and pre-costing. order entry or pre-costing has updated after booking entry.  Contact to ".$marchentrArr[$result[csf('dealing_marchant')]]; } else{ echo "";} ?></td>
                </tr>                                                
                <tr>
                    <td width="100" style="font-size:18px"><b>Booking No </b>   </td>
                    <td width="110" style="font-size:18px">:&nbsp;<b><? echo $result[csf('booking_no')];?></b><? echo "(".$fabric_source[$result[csf('fabric_source')]].")"?></td>
                    
                    <td width="100"><span style="font-size:12px"><b>Booking Date</b></span></td>
                    <td width="110">:&nbsp;<span style="font-size:12px"><b><? echo change_date_format($result[csf('booking_date')],'dd-mm-yyyy','-');; ?></b></span></td>
                    <td width="100"><span style="font-size:12px"><b>Delivery Date</b></span></td>
                    <td width="110">:&nbsp;<? echo change_date_format( $result[csf('delivery_date')],'dd-mm-yyyy','-');//$product_dept[$result[csf('product_dept')]] ; if($result[csf('product_code')] !=""){ echo " (".$result[csf('product_code')].")";} if($result[csf('pro_sub_dep')] !=0){ echo " (".$pro_sub_dept_array[$result[csf('pro_sub_dep')]].")";}?></td>	
                    <td width="100"><span style="font-size:12px"><b>Garments Item</b></span></td>
                    <td width="110">:&nbsp;<? echo $gmts_item_name="";
						$gmts_item=explode(',',$result[csf('gmts_item_id')]);
						for($g=0;$g<=count($gmts_item); $g++)
						{
							$gmts_item_name.= $garments_item[$gmts_item[$g]].",";
						}
						echo rtrim($gmts_item_name,',');//$po_qnty_tot." ".$unit_of_measurement[$result[csf('order_uom')]] ; ?></td>
                </tr>
                <tr>
                    <td width="100" style="font-size:12px"><b>Buyer/Agent Name</b></td>
                    <td width="110">:&nbsp;
                    <? 
						echo $buyer_name_arr[$result[csf('buyer_name')]]; 
                    ?>
                    </td>
                    <td width="100" style="font-size:12px"><b>Po Received Date</b></td>
                    <td width="110">:&nbsp;<? echo change_date_format($result[csf('booking_date')],'dd-mm-yyyy','-');?>&nbsp;&nbsp;&nbsp;</td>
                    <td width="100" style="font-size:12px"><b>Order Qnty</b>   </td>
                    <td width="110" style="font-size:12px">:&nbsp;<b><? echo $po_qnty_tot." ".$unit_of_measurement[$result[csf('order_uom')]];//$result[csf('style_ref_no')];?> </b>   </td>
                <td width="100" style="font-size:12px"><b>Department Name</b></td>
                    <td width="110">:&nbsp;<? echo $product_dept[$result[csf('product_dept')]] ; if($result[csf('product_code')] !=""){ echo " (".$result[csf('product_code')].")";} if($result[csf('pro_sub_dep')] !=0){ echo " (".$pro_sub_dept_array[$result[csf('pro_sub_dep')]].")";};?></td> 
                </tr>
                <tr>
                    <td  width="100" style="font-size:12px"><b>Style Des.</b></td>
                    <td  width="110" >:&nbsp;<? echo $result[csf('style_description')]; $job_no= $result[csf('job_no')];?></td>
                    <td width="100" style="font-size:12px"><b>Lead Time </b>   </td>
                    <td width="110">:&nbsp;<?  echo rtrim($lead_time,",");;?> </td>
                    <td width="100" style="font-size:12px"><b>Style Ref.</b></td>
                    <td width="110">:&nbsp;<? echo $result[csf('style_ref_no')];//$marchentrArr[$result[csf('dealing_marchant')]]; ?></td>
                    <td width="100" style="font-size:12px"><b>Department No</b></td>
                    <td width="110">:&nbsp;<? echo $result[csf('product_code')];//$currency[$result[csf('currency_id')]]; ?></td>
                </tr>
                <tr>
                    <td width="100" style="font-size:18px"><b>Supplier Name</b>   </td>
                    <td width="110" style="font-size:18px">:&nbsp; <b><? 
					if($result[csf('pay_mode')]==5 || $result[csf('pay_mode')]==3){
					echo $company_library[$result[csf('supplier_id')]];
					}
					else{
					echo $supplier_name_arr[$result[csf('supplier_id')]];
					}
					//echo $supplier_name_arr[$result[csf('supplier_id')]];?></b>    </td>
                    <td width="100" style="font-size:12px"><b>Supplier Address</b></td>
                    <td width="110">:&nbsp;<? 
					if($result[csf('pay_mode')]==5 || $result[csf('pay_mode')]==3){
				$comAdd=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=".$result[csf('supplier_id')]); 
					foreach ($comAdd as $comAddRow){ 
						echo $comAddRow[csf('plot_no')].'&nbsp;'; 
						echo $comAddRow[csf('level_no')].'&nbsp;' ;
						echo $comAddRow[csf('road_no')].'&nbsp;'; 
						echo $comAddRow[csf('block_no')].'&nbsp;';
						echo $comAddRow[csf('city')].'&nbsp;';
						//echo $comAddRow[csf('zip_code')].'&nbsp;'; 
						//echo $comAddRow[csf('province')].'&nbsp;'; 
						//echo $country_arr[$comAddRow[csf('country_id')]].'&nbsp;'; 
						//echo $comAddRow[csf('email')]; 
						//echo $comAddRow[csf('website')];
					}
				}
				else{
					echo $supplier_address_arr[$result[csf('supplier_id')]];
				}
					/*if($result[csf('pay_mode')]==5 || $result[csf('pay_mode')]==3){
					echo $company_library[$result[csf('supplier_id')]];
					}
					else{
					echo $supplier_name_arr[$result[csf('supplier_id')]];
					}*/
					//echo $supplier_address_arr[$result[csf('supplier_id')]];?></td> 
                    <td width="100" style="font-size:12px"><b>Dealing Merchant </b>   </td>
                    <td width="110" style="font-size:12px">:&nbsp;<b><? echo $marchentrArr[$result[csf('dealing_marchant')]];?></b><? //echo "(".$fabric_source[$result[csf('fabric_source')]].")"?></td>
                    <td width="100" style="font-size:12px"><b>Buying Merchant</b>   </td>
                    <td width="110" style="font-size:12px">:&nbsp;<b><? echo $result[csf('bh_merchant')];//$result[csf('exchange_rate')];?></b><? //echo "(".$fabric_source[$result[csf('fabric_source')]].")"?></td>
                </tr> 
                <tr>
                    <td width="100" style="font-size:12px"><b>Season</b></td>
                    <td width="110">:&nbsp;<? echo $result[csf('season')]; ?></td>
                    <td width="100" style="font-size:12px"><b>Currency</b></td>
                    <td width="110" >:&nbsp;<? echo $currency[$result[csf('currency_id')]]; ?></td>
                    <td width="100" style="font-size:12px"><b>Conv. Rate</b></td>
                    <td width="110" >:&nbsp;<? echo $result[csf('exchange_rate')];//$po_received_date; ?></td>
                    <td width="100" style="font-size:12px"><b>Attention</b></td>
                    <td width="110" style="font-size:12px">:&nbsp;<b><? echo $result[csf('attention')]; ?></b></td>
                </tr>  
               
                <tr>
                    <td width="100" style="font-size:12px"><b>Order No</b></td>
                    <td width="110" colspan="5"> :&nbsp;<? echo rtrim($po_no,", "); //rtrim($shipment_date,", "); ?></td>
                </tr> 
                <tr>
                    <td width="110" style="font-size:12px"><b>Shipment Date</b></td>
                    <td width="300" colspan="5"> :&nbsp;<? echo rtrim($shipment_date,", "); ?></td>
                    
                </tr> 
                
                <?php if ($result[csf('tagged_booking_no')] != '') {?>
                <tr>
                    <td colspan="5">                       
                        <h3>This fabric will be Dyed along with Main Fabric Booking No:&nbsp;<? echo $result[csf('tagged_booking_no')];?></h3>
                    </td>
                </tr>
                <?php } ?>
               
            </table> 
        <?
		}
		?>
      <br/>   									 <!--  Here will be the main portion  -->
     <style>
		 .main_table tr th{
			 border:1px solid black;
			 font-size:13px;
			 outline: 0;
		 }
		  .main_table tr td{
			 border:1px solid black;
			 font-size:13px;
			 outline: 0;
		 }
	</style>
    <?
	$costing_per="";
	$costing_per_qnty=0;
	$costing_per_id=return_field_value( "costing_per", "wo_pre_cost_mst","job_no ='$job_no'");
	if($costing_per_id==1)
	{
		$costing_per="1 Dzn";
		$costing_per_qnty=12;
	}
	if($costing_per_id==2)
	{
		$costing_per="1 Pcs";
		$costing_per_qnty=1;
	}
	if($costing_per_id==3)
	{
		$costing_per="2 Dzn";
		$costing_per_qnty=24;
	}
	if($costing_per_id==4)
	{
		$costing_per="3 Dzn";
		$costing_per_qnty=36;
	}
	if($costing_per_id==5)
	{
		$costing_per="4 Dzn";
		$costing_per_qnty=48;
	}
	//$process_loss_method=return_field_value( "process_loss_method", "wo_pre_cost_fabric_cost_dtls","job_no='$job_no'");
			
	$process_loss_method=return_field_value("process_loss_method", "variable_order_tracking", "company_name=$cbo_company_name and variable_list=18 and item_category_id=$cbo_fabric_natu and status_active=1 and is_deleted=0");

	if(str_replace("'","",$cbo_fabric_source)==1)
	{
		$nameArray_fabric_description= sql_select("SELECT a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width,b.process_loss_percent FROM wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls b where b.booking_no =$txt_booking_no and a.id=b.pre_cost_fabric_cost_dtls_id  and b.status_active=1 and	
	b.is_deleted=0  group by a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width,b.process_loss_percent order by a.body_part_id");
	    ?>
        <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
            <tr align="center"><th colspan="4" align="left">Body Part</th>
				<? 
                foreach($nameArray_fabric_description  as $result_fabric_description)
                {
                    if( $result_fabric_description[csf('body_part_id')] == "")  echo "<td  colspan='2'>&nbsp</td>";	
                    else echo "<td  colspan='2'>". $body_part[$result_fabric_description[csf('body_part_id')]]."</td>";			
                }
                ?>
            	<td rowspan="8" width="50"><p>Total  Finish Fabric <? if(trim($cbo_fabric_natu ,"'")==2){echo "(KG)";}else{echo "(Yds)";} ?></p></td> 
                <td rowspan="8" width="50"><p>P. Loss %</p></td>
                <td  rowspan="8" width="50"><p>Total Grey Fabric <? if(trim($cbo_fabric_natu ,"'")==2){echo "(KG)";}else{echo "(Yds)";} ?>)</p></td>
            	
            </tr> 
            <tr align="center"><th colspan="4" align="left">Color Type</th>
                <? 
                foreach($nameArray_fabric_description  as $result_fabric_description)
                {
					if( $result_fabric_description[csf('color_type_id')] == "")  echo "<td  colspan='2'>&nbsp</td>";	
					else echo "<td  colspan='2'>". $color_type[$result_fabric_description[csf('color_type_id')]]."</td>";			
                }
                ?>
            </tr>  
            <tr align="center"><th colspan="4" align="left">Construction</th>
				<? 
                foreach($nameArray_fabric_description  as $result_fabric_description)
                {
					if( $result_fabric_description[csf('construction')] == "")  echo "<td  colspan='2'>&nbsp</td>";	
					else echo "<td  colspan='2'>". $result_fabric_description[csf('construction')]."</td>";			
                }
                ?>
            </tr>       
            <tr align="center"><th   colspan="4" align="left">Composition</th>
				<? 
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
					if( $result_fabric_description[csf('composition')] == "")   echo "<td colspan='2' >&nbsp</td>";
					else echo "<td colspan='2' >".$result_fabric_description[csf('composition')]."</td>";			
                }
                ?>
            </tr>
            <tr align="center"><th  colspan="4" align="left">GSM</th>
				<? 
                foreach($nameArray_fabric_description  as $result_fabric_description)
                {
					if( $result_fabric_description[csf('gsm_weight')] == "")   echo "<td colspan='2'>&nbsp</td>";
					else echo "<td colspan='2' align='center'>". $result_fabric_description[csf('gsm_weight')]."</td>";			
                }
                ?>
            </tr>
            <tr align="center"><th   colspan="4" align="left">Dia/Width</th>
				<? 
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
                    if( $result_fabric_description[csf('dia_width')] == "")   echo "<td colspan='2'>&nbsp</td>";
                    else echo "<td colspan='2' align='center'>". $result_fabric_description[csf('dia_width')]."</td>";			
                }
                ?>
            </tr>
            <tr align="center"><th  colspan="4" align="left">Process Loss%</th>
				<? 
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
					if( $result_fabric_description[csf('process_loss_percent')] == "")   echo "<td colspan='2'>&nbsp</td>";
					else echo "<td align='center' colspan='2'>". $result_fabric_description[csf('process_loss_percent')]."</td>";			
                }
                ?>
            </tr>
            <tr>
                <th  width="100" align="left">Sample Name</th>
                <th  width="50" align="left">Fabric Color</th>
                <th  width="50" align="left">Gmts Color</th>
                <th  width="100" align="left">Lapdip No</th>
				<? 
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
                    echo "<th width='50'>Finish</th><th width='50' >Gray</th>";			
                }
                ?>
            </tr>
       		<?
	        $grand_total_fin_fab_qnty=0;
			$grand_total_grey_fab_qnty=0;
			$grand_totalcons_per_finish=0;
			$grand_totalcons_per_grey=0;
			if($db_type==0) $sample_type_id="group_concat(sample_type)";
			else if($db_type==2) $sample_type_id="listagg((cast(sample_type as varchar2(4000))),',') within group (order by sample_type)";
			
			$color_wise_wo_sql=sql_select("select job_no, fabric_color_id,gmts_color_id, $sample_type_id as sample_type
										  FROM 
										  wo_booking_dtls
										  WHERE 
										  booking_no =$txt_booking_no and
										  status_active=1 and
                                          is_deleted=0
										  group by job_no, fabric_color_id,gmts_color_id");
										
			foreach($color_wise_wo_sql as $color_wise_wo_result)
			{
				?> 
				<tr>
                    <td  width="100" align="left">
						<?
                        $sample_type_val="";
                        $ex_sample_type=array_unique(explode(",",$color_wise_wo_result[csf('sample_type')]));
                        foreach($ex_sample_type as $sm_val)
                        {
                        	if($sample_type_val=="") $sample_type_val=$sample_name_name_arr[$sm_val]; else $sample_type_val.=','.$sample_name_name_arr[$sm_val];
                        }
                   		echo $sample_type_val; 
                    ?></td>
                    <td  width="50" align="left">
						<?
                        echo $color_library[$color_wise_wo_result[csf('fabric_color_id')]];
                        ?>
                    </td>
                     <td  width="50" align="left">
						<?
                        echo $color_library[$color_wise_wo_result[csf('gmts_color_id')]];
                        ?>
                    </td>
                    <td  width="100" align="left">
						<? 
                        $lapdip_no="";
                        $lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='".$color_wise_wo_result[csf('job_no')]."' and approval_status=3 and color_name_id=".$color_wise_wo_result[csf('fabric_color_id')]."","lapdip_no");
                        if($lapdip_no=="") echo "&nbsp;"; echo $lapdip_no; 
                        ?>
                    </td>
                    <?
                    $total_fin_fab_qnty=0;
                    $total_grey_fab_qnty=0;
                    foreach($nameArray_fabric_description as $result_fabric_description)
                    {
						if($db_type==0)
						{
							$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty
															  FROM 
															  wo_pre_cost_fabric_cost_dtls a,
															  wo_booking_dtls b 
															  WHERE 
															  b.booking_no =$txt_booking_no  and
															  a.id=b.pre_cost_fabric_cost_dtls_id and
															  a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
															  a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and 
															  a.construction='".$result_fabric_description[csf('construction')]."' and 
															  a.composition='".$result_fabric_description[csf('composition')]."' and 
															  a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and 
															  b.dia_width='".$result_fabric_description[csf('dia_width')]."' and 
															  b.process_loss_percent='".$result_fabric_description[csf('process_loss_percent')]."' and 
															  b.fabric_color_id=".$color_wise_wo_result[csf('fabric_color_id')]." and
															  b.gmts_color_id=".$color_wise_wo_result[csf('gmts_color_id')]." and
															  b.status_active=1 and
															  b.is_deleted=0");
						}
						if($db_type==2)
						{
							
							$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty
															  FROM 
															  wo_pre_cost_fabric_cost_dtls a,
															  wo_booking_dtls b 
															  WHERE 
															  b.booking_no =$txt_booking_no  and
															  a.id=b.pre_cost_fabric_cost_dtls_id and
															  nvl(a.body_part_id,0)=nvl('".$result_fabric_description[csf('body_part_id')]."',0) and
															  nvl(a.color_type_id,0)=nvl('".$result_fabric_description[csf('color_type_id')]."',0) and 
															  nvl(a.construction,0)=nvl('".$result_fabric_description[csf('construction')]."',0) and 
															  nvl(a.composition,0)=nvl('".$result_fabric_description[csf('composition')]."',0) and 
															  nvl(a.gsm_weight,0)=nvl('".$result_fabric_description[csf('gsm_weight')]."',0) and 
															  nvl(b.dia_width,0)=nvl('".$result_fabric_description[csf('dia_width')]."',0) and 
															  nvl(b.process_loss_percent,0)=nvl('".$result_fabric_description[csf('process_loss_percent')]."',0) and 
															  nvl(b.fabric_color_id,0)=nvl(".$color_wise_wo_result[csf('fabric_color_id')].",0) and
															  nvl(b.gmts_color_id,0)=nvl(".$color_wise_wo_result[csf('gmts_color_id')].",0) and
															  b.status_active=1 and
															  b.is_deleted=0");
						}
                    	list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
                    ?>
                    <td width='50' align='right'>
						<? 
                        if($color_wise_wo_result_qnty[csf('fin_fab_qnty')]!="")
                        {
							echo number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')],2) ;
							$total_fin_fab_qnty+=$color_wise_wo_result_qnty[csf('fin_fab_qnty')];
                        }
                        ?>
                    </td>
                    <td width='50' align='right' > 
						<? 
                        if($color_wise_wo_result_qnty[csf('grey_fab_qnty')]!="")
                        {
							echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],2); 
							$total_grey_fab_qnty+=$color_wise_wo_result_qnty[csf('grey_fab_qnty')];
                        }
                        ?>
                    </td>
                    <?
                    }
                    ?>
                    <td align="right"><? echo number_format($total_fin_fab_qnty,2); $grand_total_fin_fab_qnty+=$total_fin_fab_qnty;?></td>
                     <td align="right">
                    <?
                    
                    if($process_loss_method==1)
                    {
                   		$process_percent=(($total_grey_fab_qnty-$total_fin_fab_qnty)/$total_fin_fab_qnty)*100;
                    }
                    
                    if($process_loss_method==2)
                    {
						//$devided_val = 1-(($total_grey_fab_qnty-$total_fin_fab_qnty)/100);
						//$process_percent=$total_grey_fab_qnty/$devided_val;
						$process_percent=(($total_grey_fab_qnty-$total_fin_fab_qnty)/$total_grey_fab_qnty)*100;
                    }
                    echo number_format($process_percent,2);
                    ?>
                    </td>
                    <td align="right"><? echo number_format($total_grey_fab_qnty,2); $grand_total_grey_fab_qnty+=$total_grey_fab_qnty;?></td>
                   
				</tr>
				<?
			}
			?>
			<tr>
				<td  width="120" align="left">&nbsp;</td>
				<td  width="120" align="left">&nbsp;</td>
                <td  width="120" align="left">&nbsp;</td>
				<td  width="120" align="left"><strong>Total</strong></td>
				<?
				foreach($nameArray_fabric_description as $result_fabric_description)
				{
					if($db_type==0)
					{
						$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty
															FROM 
															wo_pre_cost_fabric_cost_dtls a,
															wo_booking_dtls b 
															WHERE 
															b.booking_no =$txt_booking_no  and
															a.id=b.pre_cost_fabric_cost_dtls_id and
															a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
															a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and 
															a.construction='".$result_fabric_description[csf('construction')]."' and 
															a.composition='".$result_fabric_description[csf('composition')]."' and 
															a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and 
															b.dia_width='".$result_fabric_description[csf('dia_width')]."' and 
															b.process_loss_percent='".$result_fabric_description[csf('process_loss_percent')]."' and
															b.status_active=1 and
															b.is_deleted=0");
					}
					if($db_type==2)
					{
						$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty
															FROM 
															wo_pre_cost_fabric_cost_dtls a,
															wo_booking_dtls b 
															WHERE 
															b.booking_no =$txt_booking_no  and
															a.id=b.pre_cost_fabric_cost_dtls_id and
															nvl(a.body_part_id,0)=nvl('".$result_fabric_description[csf('body_part_id')]."',0) and
															nvl(a.color_type_id,0)=nvl('".$result_fabric_description[csf('color_type_id')]."',0) and 
															nvl(a.construction,0)=nvl('".$result_fabric_description[csf('construction')]."',0) and 
															nvl(a.composition,0)=nvl('".$result_fabric_description[csf('composition')]."',0) and 
															nvl(a.gsm_weight,0)=nvl('".$result_fabric_description[csf('gsm_weight')]."',0) and 
															nvl(b.dia_width,0)=nvl('".$result_fabric_description[csf('dia_width')]."',0) and 
															nvl(b.process_loss_percent,0)=nvl('".$result_fabric_description[csf('process_loss_percent')]."',0) and
															b.status_active=1 and
															b.is_deleted=0");
					}
					list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
					?>
					<td width='50' align='right'><?  echo number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')],2) ;?></td><td width='50' align='right' > <? echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],2);?></td>
				<?
				}
				?>
				<td align="right"><? echo number_format($grand_total_fin_fab_qnty,2);?></td>
                <td align="right">
				<?
					if($process_loss_method==1)
					{
						$totalprocess_percent=(($grand_total_grey_fab_qnty-$grand_total_fin_fab_qnty)/$grand_total_fin_fab_qnty)*100;
					}
					if($process_loss_method==2)
					{
						$totalprocess_percent=(($grand_total_grey_fab_qnty-$grand_total_fin_fab_qnty)/$grand_total_grey_fab_qnty)*100;
					}
					echo number_format($totalprocess_percent,2);
					?>
				</td>
				<td align="right"><? echo number_format($grand_total_grey_fab_qnty,2);?></td>
				
			</tr> 
        </table>
       <table align="left" class="rpt_table" width="1330"  border="0" cellpadding="0" cellspacing="0" rules="all">
       <tr>
       <td colspan="15">&nbsp; </td>
       </tr>
       </table>
       <br/>
    
    <table align="left" class="rpt_table" width="1330"  border="1" cellpadding="0" cellspacing="0" rules="all">
        <thead align="center">
           <tr>
           		 <th align="left" colspan="6"><strong>Accessoris Requirement</strong></th>
           </tr>
               <tr>
                <th width="30">Sl</th>
                <th width="200">Item</th>
                <th width="300">Desc.</th>
                <th width="80">Qnty</th>
                <th width="80">UOM</th> 
                <th>Remarks</th>
         </tr>
       </thead>
       </table>
       <table class="rpt_table" width="1330"  border="1" cellpadding="0" cellspacing="0" rules="all" align="left">
        <tbody>
            <?
       $k=1;$total_acc_qty=0;
		 $sql_acces="select id,booking_no,item_group_id,description,uom,qty,remarks from wo_booking_accessories_dtls where booking_no=$txt_booking_no and description is not null";
		$sql_result= sql_select( $sql_acces);
		 foreach($sql_result as $row)
		 {
	   ?>
           <tr>
                <td width="30" align="center"><? echo $k; ?></td>
                <td width="200"><? echo $item_library[$row[csf('item_group_id')]]; ?></td>
                <td width="300"><? echo $row[csf('description')]; ?></td>
                <td  width="80" align="right"><? echo $row[csf('qty')]; ?></td>
                <td width="80" align="center"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                <td><? echo $row[csf('remarks')]; ?></td>
               
        </tr>
        <?
		$k++;
		$total_acc_qty+=$row[csf('qty')];
		 }
		?>
         </tbody>
          <!--<tfoot>
            <tr>
                <th colspan="3" align="right">Total </th>
                <th align="right"><? //echo number_format($total_acc_qty,2);  ?></th>
                <th align="right"></th>
           </tr>
         </tfoot>-->
       
 </table>
 
 <br/>
 <table align="left" class="rpt_table" width="100%"  border="0" cellpadding="0" cellspacing="0" rules="all">
 <tr>
 <td>&nbsp; </td>
 </tr>
 </table>
 <?
   $sql_req=("select gmts_color_id as gmts_color,gmts_size,sum(bh_qty) as bh_qty,sum(rf_qty) as rf_qty  FROM wo_booking_dtls  WHERE booking_no=$txt_booking_no  and status_active=1 and is_deleted=0 and gmts_size!=0 and bh_qty>0 group by gmts_color_id,gmts_size  order by gmts_size"); 
$sql_data =sql_select($sql_req);
$size_array=array();$qnty_array_bh=array();$qnty_array_rf=array();
foreach($sql_data as $row)
{
	$size_array[$row[csf('gmts_size')]]=$row[csf('gmts_size')];
	$qnty_array_bh[$row[csf('gmts_color')]][$row[csf('gmts_size')]]=$row[csf('bh_qty')];
	$qnty_array_rf[$row[csf('gmts_color')]][$row[csf('gmts_size')]]=$row[csf('rf_qty')];
	$qnty_array[$row[csf('gmts_color')]][$row[csf('gmts_size')]]=$row[csf('bh_qty')]+$row[csf('rf_qty')];
}
 $sql_color=("select gmts_color_id as gmts_color,sum(bh_qty) as bh_qty  FROM wo_booking_dtls  WHERE booking_no=$txt_booking_no  and status_active=1 and is_deleted=0 and gmts_size!=0 and bh_qty>0 group by gmts_color_id  order by gmts_color"); 
$sql_data_color =sql_select($sql_color);
$color_array=array();
foreach($sql_data_color as $row)
{
	$color_array[$row[csf('gmts_color')]]=$row[csf('gmts_color')];
}
 $sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
 $colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
 $width=400+(count($size_array)*150);
 //count($size_array);
 ?>
   <div>
 <table align="left" cellspacing="0" width="<? echo $width?>"  border="1" rules="all" class="rpt_table" >
  
        <thead align="center">
         <tr>
           		 <th align="left" colspan="<? echo count($size_array)+5;?>" width="30"><strong>Sample Requirement</strong></th>
           </tr>
            <tr>
            <th width="30" rowspan="2">SL</th>
            <th width="80" rowspan="2" align="center">Color/Size</th>
            <?
            foreach ($size_array as $sizid)
            {
            //$size_count=count($sizid);
            ?>
            <th width="" colspan="2"><strong><? echo  $sizearr[$sizid];  ?></strong>
            </th>
            
            <?
            } ?>
           <th width="80" rowspan="2" align="center">Total Qnty.</th>
            </tr>
            <tr>
             <?
            foreach ($size_array as $sizid)
            {
            //$size_count=count($sizid);
            ?>
            <th width="75"> BH &nbsp;</th> <th width="75"> Rf.&nbsp;</th>
            <?
            } ?>
            </tr>
        </thead>
        <tbody>
			<?
            //$mrr_no=$dataArray[0][csf('issue_number')];
            $i=1;
            $tot_qnty=array();
                foreach($color_array as $cid)
                {
                    if ($i%2==0)  
                        $bgcolor="#E9F3FF";
                    else
                        $bgcolor="#FFFFFF";
					$color_count=count($cid);
                    ?>
                    <tr>
                        <td><? echo $i;  ?></td>
                        <td><? echo $colorarr[$cid]; ?></td>
                       
                         <?
						foreach ($size_array as $sizval)
						{
						//$size_count=count($sizid);
						$tot_qnty[$cid]+=$qnty_array[$cid][$sizval];
						$tot_qnty_size_bh[$sizval]+=$qnty_array_bh[$cid][$sizval];
						$tot_qnty_size_rf[$sizval]+=$qnty_array_rf[$cid][$sizval];
						?>
						<td width="75" align="right"> <? echo $qnty_array_bh[$cid][$sizval]; ?></td> <td width="75" align="right"> <? echo $qnty_array_rf[$cid][$sizval]; ?></td>
						<?
						
						} ?>
									
                        <td align="right"><? echo $tot_qnty[$cid]; ?></td>
                    </tr>
                    <?
					$production_quantity+=$tot_qnty[$cid];
					$i++;
                }
            ?>
        </tbody>
        <tr>
            <td colspan="2" align="right"><strong>Grand Total :</strong></td>
            <?
				foreach ($size_array as $sizval)
				{
					?>
                    <td align="right"><?php echo $tot_qnty_size_bh[$sizval]; ?></td>
                    <td align="right"><?php echo $tot_qnty_size_rf[$sizval]; ?></td>
                    <?
				}
			?>
            <td align="right"><?php echo $production_quantity; ?></td>
        </tr>                           
    </table>
    <br/>
 <table align="left" class="rpt_table" width="100%"  border="0" cellpadding="0" cellspacing="0" rules="all">
 <tr>
 <td>&nbsp; </td>
 </tr>
 </table>
 <br/>
    <?
   $sql_req=("select b.gmts_color_id as gmts_color,b.gmts_size,sum(b.bh_qty) as bh_qty,sum(b.rf_qty) as rf_qty  FROM wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c WHERE b.pre_cost_fabric_cost_dtls_id=c.id and b.booking_no=$txt_booking_no and c.body_part_id in(2,3)  and b.status_active=1 and b.is_deleted=0 and b.gmts_size!=0 and b.bh_qty>0 group by b.gmts_color_id,b.gmts_size  order by b.gmts_size"); 
$sql_data =sql_select($sql_req);
$size_array_cc_arr=array();$qnty_array_bh_cc_arr=array();$qnty_array_rf_cc_arr=array();
foreach($sql_data as $row)
{
	$size_array_cc_arr[$row[csf('gmts_size')]]=$row[csf('gmts_size')];
	$qnty_array_bh_cc_arr[$row[csf('gmts_color')]][$row[csf('gmts_size')]]=$row[csf('bh_qty')];
	$qnty_array_rf_cc_arr[$row[csf('gmts_color')]][$row[csf('gmts_size')]]=$row[csf('rf_qty')];
	$qnty_array_cc_arr[$row[csf('gmts_color')]][$row[csf('gmts_size')]]=$row[csf('bh_qty')]+$row[csf('rf_qty')];
}
 $sql_color=("select b.gmts_color_id as gmts_color,sum(b.bh_qty) as bh_qty  FROM wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c  WHERE b.pre_cost_fabric_cost_dtls_id=c.id and b.booking_no=$txt_booking_no and c.body_part_id in(2,3)   and b.status_active=1 and b.is_deleted=0 and b.gmts_size!=0 and b.bh_qty>0 group by b.gmts_color_id  order by b.gmts_color_id"); 
$sql_data_color =sql_select($sql_color);
$color_array=array();
foreach($sql_data_color as $row)
{
	$color_array_cc_arr[$row[csf('gmts_color')]]=$row[csf('gmts_color')];
}
 $sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
 $colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
 $width=400+(count($size_array)*150);
 //count($size_array);
 ?>
   <div>
 <table align="left" cellspacing="0" width="<? echo $width?>"  border="1" rules="all" class="rpt_table" >
  
        <thead align="center">
         <tr>
           		 <th align="left" colspan="<? echo count($size_array)+5;?>" width="30"><strong>Collar and Cuff Details</strong></th>
           </tr>
            <tr>
            <th width="30" rowspan="2">SL</th>
            <th width="80" rowspan="2" align="center">Color/Size</th>
            <?
            foreach ($size_array_cc_arr as $sizid)
            {
            //$size_count=count($sizid);
            ?>
            <th width="" colspan="2"><strong><? echo  $sizearr[$sizid];  ?></strong>
            </th>
            
            <?
            } ?>
           <th width="80" rowspan="2" align="center">Total Qnty.</th>
            </tr>
            <tr>
             <?
            foreach ($size_array_cc_arr as $sizid)
            {
            //$size_count=count($sizid);
            ?>
            <th width="75"> BH &nbsp;</th> <th width="75"> Rf.&nbsp;</th>
            <?
            } ?>
            </tr>
        </thead>
        <tbody>
			<?
            //$mrr_no=$dataArray[0][csf('issue_number')];
            $i=1;
            $tot_qnty_cc_arr=array();
                foreach($color_array_cc_arr as $cid)
                {
                    if ($i%2==0)  
                        $bgcolor="#E9F3FF";
                    else
                        $bgcolor="#FFFFFF";
					$color_count=count($cid);
                    ?>
                    <tr>
                        <td><? echo $i;  ?></td>
                        <td><? echo $colorarr[$cid]; ?></td>
                       
                         <?
						foreach ($size_array_cc_arr as $sizval)
						{
						//$size_count=count($sizid);
						$tot_qnty_cc_arr[$cid]+=$qnty_array_cc_arr[$cid][$sizval];
						$tot_qnty_size_bh_cc_arr[$sizval]+=$qnty_array_bh_cc_arr[$cid][$sizval];
						$tot_qnty_size_rf_cc_arr[$sizval]+=$qnty_array_rf_cc_arr[$cid][$sizval];
						?>
						<td width="75" align="right"> <? echo $qnty_array_bh_cc_arr[$cid][$sizval]; ?></td> <td width="75" align="right"> <? echo $qnty_array_rf_cc_arr[$cid][$sizval]; ?></td>
						<?
						
						} ?>
									
                        <td align="right"><? echo $tot_qnty_cc_arr[$cid]; ?></td>
                    </tr>
                    <?
					$production_quantity_cc+=$tot_qnty_cc_arr[$cid];
					$i++;
                }
            ?>
        </tbody>
        <tr>
            <td colspan="2" align="right"><strong>Grand Total :</strong></td>
            <?
				foreach ($size_array as $sizval)
				{
					?>
                    <td align="right"><?php echo $tot_qnty_size_bh_cc_arr[$sizval]; ?></td>
                    <td align="right"><?php echo $tot_qnty_size_rf_cc_arr[$sizval]; ?></td>
                    <?
				}
			?>
            <td align="right"><?php echo $production_quantity_cc; ?></td>
        </tr>                           
    </table>
    </div>
    </div>
   
         <br/>
          <table align="left"  width="1330"  border="0" cellpadding="0" cellspacing="0">
          <tr><td colspan="15"> &nbsp;&nbsp;&nbsp; </td> </tr>
          </table>
           <br/> <br/> 
           <div>
        <table  width="750"  border="0" cellpadding="0" cellspacing="0" style="border:solid; border-color:#000; border-width:thin" align="left">
        <thead>
                <tr>
                    <th width="3%"></th><th width="97%" align="left"><u>Special Instruction</u></th>
                </tr>
         </thead>
           
                    <tbody>
                    <?
					$data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no");// quotation_id='$data'
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							?>
                            	<tr id="settr_1" valign="top">
                                    <td style="vertical-align:top"><? echo $i;?></td>
                                    <td><strong style="font-size:20px"> <? echo $row[csf('terms')]; ?></strong></td>
                                </tr>
                            <?
						}
					}
					
					?>
                </tbody>
              
                </table>
                </div>
       
        <?
	 }
	 //echo  $cbo_fabric_source;
	if(str_replace("'","",$cbo_fabric_source)==2) //Purchase
	{
		$nameArray_fabric_description= sql_select("SELECT a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width,b.process_loss_percent FROM wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls b where b.booking_no =$txt_booking_no and a.id=b.pre_cost_fabric_cost_dtls_id  and b.status_active=1 and	
		b.is_deleted=0  group by a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width,b.process_loss_percent order by a.body_part_id");
		?>
		<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
            <tr align="center"><th colspan="3" align="left">Body Part</th>
				<? 
                foreach($nameArray_fabric_description  as $result_fabric_description)
                {
					if( $result_fabric_description[csf('body_part_id')] == "")  echo "<td  colspan='3'>&nbsp</td>";	
					else echo "<td  colspan='3'>". $body_part[$result_fabric_description[csf('body_part_id')]]."</td>";			
                }
                ?>
                <td rowspan="8" width="50"><p>Total  Fabric <? if(trim($cbo_fabric_natu ,"'")==2){echo "(KG)";}else{echo "(Yds)";} ?></p></td> 
                <td rowspan="8" width="50"><p>Avg. Rate</p></td>
                <td rowspan="8" width="50"><p>Amount</p></td>
            </tr> 
            <tr align="center"><th colspan="3" align="left">Color Type</th>
				<? 
                foreach($nameArray_fabric_description  as $result_fabric_description)
                {
					if( $result_fabric_description[csf('color_type_id')] == "")  echo "<td  colspan='3'>&nbsp</td>";	
					else echo "<td  colspan='3'>". $color_type[$result_fabric_description[csf('color_type_id')]]."</td>";			

                }
                ?>
            </tr>  
            <tr align="center"><th colspan="3" align="left">Construction</th>
				<? 
                foreach($nameArray_fabric_description  as $result_fabric_description)
                {
					if( $result_fabric_description[csf('construction')] == "")  echo "<td  colspan='3'>&nbsp</td>";	
					else echo "<td  colspan='3'>". $result_fabric_description[csf('construction')]."</td>";			
                }
                ?>
            </tr>       
            <tr align="center"><th   colspan="3" align="left">Composition</th>
				<? 
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
					if( $result_fabric_description[csf('composition')] == "")   echo "<td colspan='3' >&nbsp</td>";
					else echo "<td colspan='3' >".$result_fabric_description[csf('composition')]."</td>";			
                }
                ?>
            </tr>
            <tr align="center"><th  colspan="3" align="left">GSM</th>
				<? 
                foreach($nameArray_fabric_description  as $result_fabric_description)
                {
					if( $result_fabric_description[csf('gsm_weight')] == "")   echo "<td colspan='3'>&nbsp</td>";
					else echo "<td colspan='3' align='center'>". $result_fabric_description[csf('gsm_weight')]."</td>";			
                }
                ?>
            </tr>
            <tr align="center"><th   colspan="3" align="left">Dia/Width</th>
				<? 
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
					if( $result_fabric_description[csf('dia_width')] == "")   echo "<td colspan='3'>&nbsp</td>";
					else echo "<td colspan='3' align='center'>". $result_fabric_description[csf('dia_width')]."</td>";			
                }
                ?>
            </tr>
            <tr align="center"><th  colspan="3" align="left">Process Loss%</th>
				<? 
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
					if( $result_fabric_description[csf('process_loss_percent')] == "")   echo "<td colspan='3'>&nbsp</td>";
					else echo "<td align='center' colspan='3'>". $result_fabric_description[csf('process_loss_percent')]."</td>";			
                }
                ?>
            </tr>
            <tr>
                <th  width="100" align="left">Sample Name</th>
                <th  width="100" align="left">Fabric Color</th>
                <th  width="100" align="left">Lapdip No</th>
                <? 
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
               		echo "<th width='50'>Fab Qty</th><th width='50'>Rate</th><th width='50'>Amount</th>";			
                }
                ?>
            </tr>
            <?
            $grand_total_fin_fab_qnty=0;
            $grand_total_grey_fab_qnty=0;
            $grand_totalcons_per_finish=0;
            $grand_totalcons_per_grey=0;
            if($db_type==0) $sample_type_id="group_concat(sample_type)";
            else if($db_type==2) $sample_type_id="listagg((cast(sample_type as varchar2(4000))),',') within group (order by sample_type)";
            
            $color_wise_wo_sql=sql_select("select job_no, fabric_color_id, $sample_type_id as sample_type
											FROM 
											wo_booking_dtls
											WHERE 
											booking_no =$txt_booking_no and
											status_active=1 and
											is_deleted=0
											group by job_no, fabric_color_id");
            foreach($color_wise_wo_sql as $color_wise_wo_result)
			{
				?> 
				<tr>
                    <td  width="100" align="left">
						<?
                        $sample_type_val="";
                        $ex_sample_type=array_unique(explode(",",$color_wise_wo_result[csf('sample_type')]));
                        foreach($ex_sample_type as $sm_val)
                        {
                        	if($sample_type_val=="") $sample_type_val=$sample_name_name_arr[$sm_val]; else $sample_type_val.=','.$sample_name_name_arr[$sm_val];
                        }
                        echo $sample_type_val; 
                    ?></td>
                    <td width="100" align="left">
						<?
                        echo $color_library[$color_wise_wo_result[csf('fabric_color_id')]];
                        ?>
                    </td>
                    <td width="100" align="left">
						<? 
                        $lapdip_no="";
                        $lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='".$color_wise_wo_result[csf('job_no')]."' and approval_status=3 and color_name_id=".$color_wise_wo_result[csf('fabric_color_id')]."","lapdip_no");
                        if($lapdip_no=="") echo "&nbsp;"; echo $lapdip_no; 
                        ?>
                    </td>
                    <?
                    $total_fin_fab_qnty=0;
                    $total_grey_fab_qnty=0;
                    $total_amount=0;
                    foreach($nameArray_fabric_description as $result_fabric_description)
                    {
						if($db_type==0)
						{
							$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty, avg(b.rate) as rate, sum(b.amount) as amount
																FROM 
																wo_pre_cost_fabric_cost_dtls a,
																wo_booking_dtls b 
																WHERE 
																b.booking_no =$txt_booking_no  and
																a.id=b.pre_cost_fabric_cost_dtls_id and
																a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
																a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and 
																a.construction='".$result_fabric_description[csf('construction')]."' and 
																a.composition='".$result_fabric_description[csf('composition')]."' and 
																a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and 
																b.dia_width='".$result_fabric_description[csf('dia_width')]."' and 
																b.process_loss_percent='".$result_fabric_description[csf('process_loss_percent')]."' and 
																b.fabric_color_id=".$color_wise_wo_result[csf('fabric_color_id')]." and
																b.status_active=1 and
																b.is_deleted=0");
						}
						if($db_type==2)
						{
							$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty, avg(b.rate) as rate, sum(b.amount) as amount
																FROM 
																wo_pre_cost_fabric_cost_dtls a,
																wo_booking_dtls b 
																WHERE 
																b.booking_no =$txt_booking_no  and
																a.id=b.pre_cost_fabric_cost_dtls_id and
																nvl(a.body_part_id,0)=nvl('".$result_fabric_description[csf('body_part_id')]."',0) and
																nvl(a.color_type_id,0)=nvl('".$result_fabric_description[csf('color_type_id')]."',0) and 
																nvl(a.construction,0)=nvl('".$result_fabric_description[csf('construction')]."',0) and 
																nvl(a.composition,0)=nvl('".$result_fabric_description[csf('composition')]."',0) and 
																nvl(a.gsm_weight,0)=nvl('".$result_fabric_description[csf('gsm_weight')]."',0) and 
																nvl(b.dia_width,0)=nvl('".$result_fabric_description[csf('dia_width')]."',0) and 
																nvl(b.process_loss_percent,0)=nvl('".$result_fabric_description[csf('process_loss_percent')]."',0) and 
																nvl(b.fabric_color_id,0)=nvl(".$color_wise_wo_result[csf('fabric_color_id')].",0) and
																b.status_active=1 and
																b.is_deleted=0");
						}
						list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
						?>
						<td width='50' align='right' > 
							<? 
                            if($color_wise_wo_result_qnty[csf('grey_fab_qnty')]!="")
                            {
								echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],2); 
								$total_grey_fab_qnty+=$color_wise_wo_result_qnty[csf('grey_fab_qnty')];
                            }
                            ?>
						</td>
						<td width='50' align='right'>
							<? 
                            if($color_wise_wo_result_qnty[csf('rate')]!="")
                            {
								echo number_format($color_wise_wo_result_qnty[csf('rate')],4) ;
								//$total_fin_fab_qnty+=$color_wise_wo_result_qnty[csf('fin_fab_qnty')];
                            }
                            ?>
						</td>
						<td width='50' align='right'>
							<? 
                            if($color_wise_wo_result_qnty[csf('amount')]!="")
                            {
								echo number_format($color_wise_wo_result_qnty[csf('amount')],2) ;
								//$total_fin_fab_qnty+=$color_wise_wo_result_qnty[csf('fin_fab_qnty')];
                            }
                            ?>
						</td>
						<?
						$total_amount+=$color_wise_wo_result_qnty[csf('amount')];
                    }
                    ?>
                    <td align="right"><? echo number_format($total_grey_fab_qnty,2); $grand_total_grey_fab_qnty+=$total_grey_fab_qnty;?></td>
                    <td align="right"><? echo number_format($total_amount/$total_grey_fab_qnty,4); //$grand_total_fin_fab_qnty+=$total_fin_fab_qnty;?></td>
                    <td align="right"><? echo number_format($total_amount,2); $grand_total_amount+=$total_amount;?></td>
				</tr>
				<?
			}
            ?>
            <tr>
                <td width="100" align="left">&nbsp;</td>
                <td width="100" align="left">&nbsp;</td>
                <td width="100" align="left"><strong>Total</strong></td>
                <?
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
					if($db_type==0)
					{
						$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty, avg(b.rate) as rate, sum(b.amount) as amount
															FROM 
															wo_pre_cost_fabric_cost_dtls a,
															wo_booking_dtls b 
															WHERE 
															b.booking_no =$txt_booking_no  and
															a.id=b.pre_cost_fabric_cost_dtls_id and
															a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
															a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and 
															a.construction='".$result_fabric_description[csf('construction')]."' and 
															a.composition='".$result_fabric_description[csf('composition')]."' and 
															a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and 
															b.dia_width='".$result_fabric_description[csf('dia_width')]."' and 
															b.process_loss_percent='".$result_fabric_description[csf('process_loss_percent')]."' and
															b.status_active=1 and
															b.is_deleted=0");
					}
					if($db_type==2)
					{
						$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty, avg(b.rate) as rate, sum(b.amount) as amount
															FROM 
															wo_pre_cost_fabric_cost_dtls a,
															wo_booking_dtls b 
															WHERE 
															b.booking_no =$txt_booking_no  and
															a.id=b.pre_cost_fabric_cost_dtls_id and
															nvl(a.body_part_id,0)=nvl('".$result_fabric_description[csf('body_part_id')]."',0) and
															nvl(a.color_type_id,0)=nvl('".$result_fabric_description[csf('color_type_id')]."',0) and 
															nvl(a.construction,0)=nvl('".$result_fabric_description[csf('construction')]."',0) and 
															nvl(a.composition,0)=nvl('".$result_fabric_description[csf('composition')]."',0) and 
															nvl(a.gsm_weight,0)=nvl('".$result_fabric_description[csf('gsm_weight')]."',0) and 
															nvl(b.dia_width,0)=nvl('".$result_fabric_description[csf('dia_width')]."',0) and 
															nvl(b.process_loss_percent,0)=nvl('".$result_fabric_description[csf('process_loss_percent')]."',0) and
															b.status_active=1 and
															b.is_deleted=0");
					}
					list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
					?>
					<td width='50' align='right' > <? echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],2);?></td>
					<td width='50' align='right'><?  echo number_format($color_wise_wo_result_qnty[csf('rate')],4) ;?></td>
					<td width='50' align='right'><?  echo number_format($color_wise_wo_result_qnty[csf('amount')],2) ;?></td>
					<?
                }
                ?>
                <td align="right"><? echo number_format($grand_total_grey_fab_qnty,2);?></td>
                <td align="right"><? echo number_format($grand_total_amount/$grand_total_grey_fab_qnty,4);?></td>
                <td align="right"><? echo number_format($grand_total_amount,2);?></td>
            </tr> 
		</table>
       <br/> <br/> 
        <table  width="1330"  border="0" cellpadding="0" cellspacing="0" style="border:solid; border-color:#000; border-width:thin"  align="left">
        
       		 <thead>
                <tr>
                    <th width="3%"></th><th width="97%" align="left"><u>Special Instruction</u></th>
                </tr>
            </thead>
            
                    <tbody>
                    <?
					$data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no");// quotation_id='$data'
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							?>
                            	<tr id="settr_1" valign="top">
                                    <td style="vertical-align:top"><? echo $i;?></td>
                                    <td><strong style="font-size:20px"> <? echo $row[csf('terms')]; ?></strong></td>
                                </tr>
                            <?
						}
					}
					
					?>
                </tbody>
 	</table>
		
	<?
	}
	
		?>
      
        <br/>
        
          <?
		 	echo signature_table(5, $cbo_company_name, "1330px");
			echo "****".custom_file_name($varcode_booking_no,$style_sting,$txt_job_no);
		 ?>
       </div>
	<script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
    fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
    </script>
       <?
   exit();   
}
if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		//txt_fabriccomposition
		if($db_type==0)
		{
		$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'SM', date("Y",time()), 5, "select booking_no_prefix, booking_no_prefix_num from wo_booking_mst where company_id=$cbo_company_name and booking_type=4 and YEAR(insert_date)=".date('Y',time())." order by booking_no_prefix_num desc ", "booking_no_prefix", "booking_no_prefix_num" ));
		}
		if($db_type==2)
		{
		$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'SM', date("Y",time()), 5, "select booking_no_prefix, booking_no_prefix_num from wo_booking_mst where company_id=$cbo_company_name and booking_type=4 and to_char(insert_date,'YYYY')=".date('Y',time())." order by booking_no_prefix_num desc ", "booking_no_prefix", "booking_no_prefix_num" ));
		}
        
        if ($txt_fabric_booking_no !='') {
           $txt_fabric_booking_no = str_replace("'","",$txt_fabric_booking_no);
        } else {
            $txt_fabric_booking_no = '';
        }
        
		$id=return_next_id( "id", "wo_booking_mst", 1 ) ;
		$field_array="id,booking_type,is_short,booking_no_prefix,booking_no_prefix_num,booking_no,company_id,buyer_id,job_no,po_break_down_id,item_category,fabric_source,currency_id,exchange_rate,pay_mode,source,booking_date,delivery_date,booking_month,booking_year,supplier_id,attention,ready_to_approved,fabric_composition,inserted_by,insert_date,tagged_booking_no"; 
		$data_array ="(".$id.",4,2,'".$new_booking_no[1]."',".$new_booking_no[2].",'".$new_booking_no[0]."',".$cbo_company_name.",".$cbo_buyer_name.",".$txt_job_no.",".$txt_order_no_id.",".$cbo_fabric_natu.",".$cbo_fabric_source.",".$cbo_currency.",".$txt_exchange_rate.",".$cbo_pay_mode.",".$cbo_source.",".$txt_booking_date.",".$txt_delivery_date.",".$cbo_booking_month.",".$cbo_booking_year.",".$cbo_supplier_name.",".$txt_attention.",".$cbo_ready_to_approved.",".$txt_fabriccomposition.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$txt_fabric_booking_no."')";
		$rID=sql_insert("wo_booking_mst",$field_array,$data_array,0);
		if($db_type==0)
		{
			if($rID){
				mysql_query("COMMIT");  
				echo "0**".$new_booking_no[0]."**".$id;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_booking_no[0];
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID){
				oci_commit($con);  
				echo "0**".$new_booking_no[0]."**".$id;
			}
			else{
				oci_rollback($con);  
				echo "10**".$new_booking_no[0];
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1){
		$con = connect();
		
		$is_approved=0;
		$sql=sql_select("select is_approved from wo_booking_mst where booking_no=$txt_booking_no");
		foreach($sql as $row){
			$is_approved=$row[csf('is_approved')];
		}
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			disconnect($con);die;
		}
		
		/*$sales_order=0;
		$sqls=sql_select("select job_no from fabric_sales_order_mst where sales_booking_no=$txt_booking_no");
		foreach($sqls as $rows){
			$sales_order=$rows[csf('job_no')];
		}
		if($sales_order){
			echo "sal1**".str_replace("'","",$txt_booking_no)."**".$sales_order;
			die;
		}*/
		if(str_replace("'","",$cbo_pay_mode)==2){
			$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no  and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			if($pi_number){
				echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
				disconnect($con);die;
			}
		}
		
		if($db_type==0){
			mysql_query("BEGIN");
		}
		$field_array="company_id*buyer_id*job_no*po_break_down_id*item_category*fabric_source*currency_id*exchange_rate*pay_mode*source*booking_date*delivery_date*booking_month*booking_year*supplier_id*attention*ready_to_approved*fabric_composition*updated_by*update_date*tagged_booking_no"; 
		$data_array ="".$cbo_company_name."*".$cbo_buyer_name."*".$txt_job_no."*".$txt_order_no_id."*".$cbo_fabric_natu."*".$cbo_fabric_source."*".$cbo_currency."*".$txt_exchange_rate."*".$cbo_pay_mode."*".$cbo_source."*".$txt_booking_date."*".$txt_delivery_date."*".$cbo_booking_month."*".$cbo_booking_year."*".$cbo_supplier_name."*".$txt_attention."*".$cbo_ready_to_approved."*".$txt_fabriccomposition."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"."*".$txt_fabric_booking_no.""; 
		$rID=sql_update("wo_booking_mst",$field_array,$data_array,"id","".$update_id."",0);
		
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$update_id);
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);  
				echo "1**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$update_id);
			}
			else{
				oci_rollback($con);  
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2){
		$con = connect();
		
		$is_approved=0;
		$sql=sql_select("select is_approved from wo_booking_mst where booking_no=$txt_booking_no");
		foreach($sql as $row){
			$is_approved=$row[csf('is_approved')];
		}
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			disconnect($con);die;
		}
		
		/*$sales_order=0;
		$sqls=sql_select("select job_no from fabric_sales_order_mst where sales_booking_no=$txt_booking_no");
		foreach($sqls as $rows){
			$sales_order=$rows[csf('job_no')];
		}
		if($sales_order){
			echo "sal1**".str_replace("'","",$txt_booking_no)."**".$sales_order;
			die;
		}*/
		if(str_replace("'","",$cbo_pay_mode)==2){
			$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no  and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			if($pi_number){
				echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
				disconnect($con);die;
			}
		}
		if($db_type==0){
			mysql_query("BEGIN");
		}
		
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'2'*'1'";
		$rID=sql_delete("wo_booking_mst",$field_array,$data_array,"id","".$update_id."",1);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$update_id);
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);   
				echo "2**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$update_id);
			}
			else{
				oci_rollback($con);   
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		disconnect($con);
		die;
		 
		
	}
}

if($action=="save_update_delete_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0";disconnect($con); die;}		
		 $id=return_next_id( "id", "wo_booking_dtls", 1 ) ;
		 
		 $pre_cost_remarks=return_field_value("remarks","wo_pre_cos_fab_co_avg_con_dtls","pre_cost_fabric_cost_dtls_id=$cbo_fabricdescription_id and po_break_down_id=$cbo_order_id and color_number_id =$cbo_garmentscolor_id and gmts_sizes=$cbo_garmentssize_id");
		 
		 $field_array="id, job_no,booking_mst_id, po_break_down_id, pre_cost_fabric_cost_dtls_id, sample_type, booking_no, booking_type, is_short, fabric_color_id, gmts_color_id, item_size, gmts_size, dia_width, fin_fab_qnty, process_loss_percent, grey_fab_qnty, rate, amount, bh_qty, rf_qty, pre_cost_remarks, uom";
			$data_array="(".$id.",".$txt_job_no.",".$update_id.",".$cbo_order_id.",".$cbo_fabricdescription_id.",".$cbo_sample_type.",".$txt_booking_no.",4,2,".$cbo_fabriccolor_id.",".$cbo_garmentscolor_id.",".$cbo_itemsize_id.",".$cbo_garmentssize_id.",".$txt_dia_width.",".$txt_finish_qnty.",".$txt_process_loss.",".$txt_grey_qnty.",".$txt_rate.",".$txt_amount.",".$txt_bh_qty.",".$txt_rf_qty.",'".$pre_cost_remarks."',".$cbouom.")";
			//$id=$id+1;
		 
		 $rID=sql_insert("wo_booking_dtls",$field_array,$data_array,1);
		 
		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID){
				mysql_query("COMMIT");  
				echo "0**".str_replace("'","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);  
				echo "0**".str_replace("'","",$txt_booking_no);
			}
			else{
				oci_rollback($con); 
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		disconnect($con);
		die;
	}
	if ($operation==1)  // update Here
	{
		$con = connect();
		$is_approved=0;
		$sql=sql_select("select is_approved from wo_booking_mst where booking_no=$txt_booking_no");
		foreach($sql as $row){
			$is_approved=$row[csf('is_approved')];
		}
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			disconnect($con);die;
		}
		
		/*$sales_order=0;
		$sqls=sql_select("select job_no from fabric_sales_order_mst where sales_booking_no=$txt_booking_no");
		foreach($sqls as $rows){
			$sales_order=$rows[csf('job_no')];
		}
		if($sales_order){
			echo "sal1**".str_replace("'","",$txt_booking_no)."**".$sales_order;
			die;
		}*/
		if(str_replace("'","",$cbo_pay_mode)==2){
			$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no  and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			if($pi_number){
				echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
				disconnect($con);die;
			}
		}
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
	    if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1";disconnect($con); die;}		
	    $field_array_up="job_no*po_break_down_id*pre_cost_fabric_cost_dtls_id*sample_type*booking_no*booking_type*is_short*fabric_color_id*gmts_color_id*item_size*gmts_size*dia_width*fin_fab_qnty*process_loss_percent*grey_fab_qnty*rate*amount*bh_qty*rf_qty*pre_cost_remarks*uom";
		$pre_cost_remarks=return_field_value("remarks","wo_pre_cos_fab_co_avg_con_dtls","pre_cost_fabric_cost_dtls_id=$cbo_fabricdescription_id and po_break_down_id=$cbo_order_id and color_number_id =$cbo_garmentscolor_id and gmts_sizes=$cbo_garmentssize_id");

	    $data_array_up ="".$txt_job_no."*".$cbo_order_id."*".$cbo_fabricdescription_id."*".$cbo_sample_type."*".$txt_booking_no."*4*2*".$cbo_fabriccolor_id."*".$cbo_garmentscolor_id."*".$cbo_itemsize_id."*".$cbo_garmentssize_id."*".$txt_dia_width."*".$txt_finish_qnty."*".$txt_process_loss."*".$txt_grey_qnty."*".$txt_rate."*".$txt_amount."*".$txt_bh_qty."*".$txt_rf_qty."*'".$pre_cost_remarks."'*".$cbouom."";
	    $rID=sql_update("wo_booking_dtls",$field_array_up,$data_array_up,"id","".$update_id_details."",0);
		
		 
		 //echo "10**".str_replace("'","",$txt_booking_no).'**'."delete from wo_booking_accessories_dtls where wo_booking_dtls_id=".$update_id_details." and booking_no =".$txt_booking_no."".','.$rID; die;
		
		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID){
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID){
				oci_commit($con);  
				echo "1**".str_replace("'","",$txt_booking_no);
			}
			else{
				oci_rollback($con);   
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		disconnect($con);
		die;
	}
	if ($operation==2)  // Insert Here
	{
		$con = connect();
		$is_approved=0;
		$sql=sql_select("select is_approved from wo_booking_mst where booking_no=$txt_booking_no");
		foreach($sql as $row){
			$is_approved=$row[csf('is_approved')];
		}
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			disconnect($con);die;
		}
		
		/*$sales_order=0;
		$sqls=sql_select("select job_no from fabric_sales_order_mst where sales_booking_no=$txt_booking_no");
		foreach($sqls as $rows){
			$sales_order=$rows[csf('job_no')];
		}
		if($sales_order){
			echo "sal1**".str_replace("'","",$txt_booking_no)."**".$sales_order;
			die;
		}*/
		if(str_replace("'","",$cbo_pay_mode)==2){
			$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no  and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			if($pi_number){
				echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
				disconnect($con);die;
			}
		}
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$is_yarn_issued=sql_select("select id,issue_number from inv_issue_master where issue_basis=1 and issue_purpose=4 and item_category=1 and entry_form=3 and booking_no=$txt_booking_no and status_active=1	and is_deleted=0");
		
		if(count($is_yarn_issued)>0)
		{
		     echo "13**".str_replace("'","",$txt_booking_no);disconnect($con); die;
		}
		$rID=execute_query( "update wo_booking_dtls set status_active=0,is_deleted =1 where  id =$update_id_details",0);	
			
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);  
				echo "2**".str_replace("'","",$txt_booking_no);
			}
			else{
				rollback($con);  
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		disconnect($con);
		die;
	}
}


if ($action=="fabric_booking_popup")
{
  	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
  	extract($_REQUEST);
?>
     
	<script>
	 var company="<? echo $company; ?>";
	$('#cbo_company_mst').val(company);	
	function js_set_value(booking_no)
	{
		document.getElementById('selected_booking').value=booking_no;
		parent.emailwindow.hide();
	}
	
    </script>

</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="1040" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
    	<tr>
        	<td align="center" width="100%">
            	<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                   <thead>
                           <th colspan="2"> </th>
                        	<th  >
                              <?
                               echo create_drop_down( "cbo_search_category", 130, $string_search_type,'', 1, "-- Search Catagory --" );
                              ?>
                            </th>
                            <th colspan="5"></th>
                     </thead>
                    <thead>                	 
                        <th width="150" class="must_entry_caption">Company Name</th>
                        <th width="150" class="must_entry_caption">Buyer Name</th>
                         <th width="100">Booking No</th>
                         <th width="70">File No</th>
                         <th width="70">Ref. No</th>
                        <th width="100">Job No</th>
                        <th width="200">Date Range</th><th></th>           
                    </thead>
        			<tr>
                    	<td> <input type="hidden" id="selected_booking">
							<? 
								echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $company, "load_drop_down( 'size_color_breakdown_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
							?>
                        </td>
                   	<td id="buyer_td">
                     <? 
						echo create_drop_down( "cbo_buyer_name", 172, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --" );
					?>	</td>
                     <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:100px"></td>
                     <td><input name="txt_file_no" id="txt_file_no" class="text_boxes_numeric" style="width:70px"></td>
                     <td><input name="txt_ref_no" id="txt_ref_no" class="text_boxes_numeric" style="width:70px"></td>
                    <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:100px"></td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
					  <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
					 </td> 
            		 <td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_ref_no').value, 'create_booking_search_list_view', 'search_div', 'sample_booking_controller','setFilterGrid(\'list_view\',-1)')" style="width:80px;" /></td>
        		</tr>
             </table>
          </td>
        </tr>
        <tr>
            <td  align="center" height="40" valign="middle">
            <? 
			echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );		
			?>
			<? echo load_month_buttons();  ?>
            </td>
            </tr>
        <tr>
            <td align="center"valign="top" id="search_div"> 
            </td>
        </tr>
    </table>    
    
    </form>
   </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if ($action=="create_booking_search_list_view")
{
	$data=explode('_',$data);
	if($data[0]==0 && $data[1]==0)
	{
		echo "<span style='color:red; font-weight:bold; font-size:20px; text-align:center'>Please select company or buyer first.";
		die;
	}
	if ($data[0]!=0) $company=" and company_id='$data[0]'"; else { $company=""; }
	if ($data[1]!=0) 
	{
		$buyer=" and buyer_id='$data[1]'";
		$buyer_cond=" and b.buyer_name=$data[1]";
	}
	else
	{ 
		$buyer="";
		$buyer_cond="";
	}
	
	//{ echo "Please Select Buyer First."; die; }
	if (str_replace("'","",$data[8])!="") $file_cond=" and a.file_no = '$data[8]'"; else  $file_cond="";
	if (str_replace("'","",$data[9])!="") $ref_cond=" and a.grouping = '$data[9]'"; else  $ref_cond=""; 
	//echo $ref_cond.'-'.$file_cond.'ggg';
	if($db_type==0)
	 {
		  $booking_year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[5]";
		  $year_cond=" and SUBSTRING_INDEX(b.insert_date, '-', 1)=$data[5]"; 
		  if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' 
		  and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date =""; 
     }
	if($db_type==2)
	 {
		  $booking_year_cond=" and to_char(a.insert_date,'YYYY')=$data[5]";
		  $year_cond=" and to_char(b.insert_date,'YYYY')=$data[5]";
		  if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."'
		  and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	 }
	if($data[7]==4 || $data[7]==0)
		{
			if (str_replace("'","",$data[4])!="") $job_cond=" and b.job_no_prefix_num like '%$data[4]%' $year_cond "; else  $job_cond=""; 
			if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[6]%'  $booking_year_cond  "; else $booking_cond="";
			
			
			 
		}
    if($data[7]==1)
		{
			if (str_replace("'","",$data[4])!="") $job_cond=" and b.job_no_prefix_num ='$data[4]' "; else  $job_cond=""; 
			if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num ='$data[6]'"; else $booking_cond="";
		}
   if($data[7]==2)
		{
			if (str_replace("'","",$data[4])!="") $job_cond=" and b.job_no_prefix_num like '$data[4]%'  $year_cond"; else  $job_cond=""; 
			if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num like '$data[6]%'  $booking_year_cond  "; else $booking_cond="";
		}
	if($data[7]==3)
		{
			if (str_replace("'","",$data[4])!="") $job_cond=" and b.job_no_prefix_num like '%$data[4]'  $year_cond"; else  $job_cond=""; 
			if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[6]'  $booking_year_cond  "; else $booking_cond="";
		}
		
	$po_data_sql="select a.id,a.po_number,b.style_ref_no,a.file_no,a.grouping,b.job_no_prefix_num,b.job_no from  wo_po_break_down a,wo_po_details_master b where  b.job_no=a.job_no_mst and b.company_name=$data[0] and b.status_active=1 and b.is_deleted=0  $buyer_cond $job_cond $file_cond $ref_cond";
	$po_data_arr=sql_select($po_data_sql);
	$all_job_num='';$file_arr='';$ref_arr='';
	foreach($po_data_arr as $row)
	{
		$file_arr[$row[csf('id')]]=$row[csf('file_no')];
		$ref_arr[$row[csf('id')]]=$row[csf('grouping')];
		$job_arr[$row[csf('job_no')]]['po_number'].=$row[csf('po_number')].',';
		$job_arr[$row[csf('job_no')]]['file_no'].=$row[csf('file_no')].',';
		$job_arr[$row[csf('job_no')]]['ref_no'].=$row[csf('grouping')].',';
		$job_arr[$row[csf('job_no')]]['style']=$row[csf('style_ref_no')];
		
		if($all_job_num=="") $all_job_num=$row[csf('job_no_prefix_num')]; else $all_job_num.=",".$row[csf('job_no_prefix_num')];
	}
	if (str_replace("'","",$data[8])!="" || str_replace("'","",$data[9])!="")
	{
		if($all_job_num!='' || $all_job_num!=0) $all_job_cond=" and job_no_prefix_num in($all_job_num)"; else $all_job_cond="";
	}
//echo $all_job_cond;
	/*$po_array=array();$po_file_array=array();$po_ref_array=array();
	$sql_po= sql_select("select a.booking_no_prefix_num, a.booking_no,a.po_break_down_id from  wo_booking_mst a where $company $buyer $booking_date and a.booking_type=4 and a.is_short=2 and a.status_active=1  and 	a.is_deleted=0 order by a.booking_no");
	foreach($sql_po as $row)
	{
		$po_id=explode(",",$row[csf("po_break_down_id")]);
		$po_number_string="";$po_file_string="";$po_ref_string="";
		foreach($po_id as $key=> $value )
		{
			$po_number_string.=$po_number[$value].",";
			$po_file_string.=$file_arr[$value].",";
			$po_ref_string.=$ref_arr[$value].",";
		}
		$po_array[$row[csf("po_break_down_id")]]=rtrim($po_number_string,",");
		 $po_file_array[$row[csf("po_break_down_id")]]=rtrim($po_file_string,",");
		$po_ref_array[$row[csf("po_break_down_id")]]=rtrim($po_ref_string,",");
	}*/
	 $approved=array(0=>"No",1=>"Yes");
	 $is_ready=array(0=>"No",1=>"Yes",2=>"No"); 
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	//$po_no=return_library_array( "select job_no, job_no_prefix_num from  wo_po_details_master",'job_no','job_no_prefix_num');
	//print_r($po_no);die;
	//$arr=array (2=>$comp,3=>$buyer_arr,5=>$po_array,6=>$po_file_array,7=>$po_ref_array,8=>$item_category,9=>$fabric_source,10=>$suplier,11=>$approved,12=>$is_ready);
	
	$sql= "select a.booking_no_prefix_num, b.job_no_prefix_num,b.job_no,a.booking_no,a.booking_date,a.company_id,buyer_id,a.po_break_down_id,a.item_category,a.pay_mode,a.fabric_source,a.supplier_id,a.is_approved,a.ready_to_approved from wo_booking_mst a,wo_po_details_master b  where a.status_active=1 $company $buyer $booking_date $job_cond  $all_job_cond $booking_cond ".set_user_lavel_filtering(' and buyer_id','buyer_id')." and a.job_no=b.job_no and a.booking_type=4 and a.is_short=2 and a.is_deleted=0 order by a.booking_no"; 
	//echo $sql;
	//echo  create_list_view("list_view", "Booking No,Booking Date,Company,Buyer,Job No.,PO number,File,Ref,Fabric Nature,Fabric Source,Supplier,Approved,Is-Ready", "80,80,80,100,90,200,70,80,80,80,50,50","1170","300",0, $sql , "js_set_value", "booking_no", "", 1, "0,0,company_id,buyer_id,0,po_break_down_id,po_break_down_id,po_break_down_id,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", $arr , "booking_no_prefix_num,booking_date,company_id,buyer_id,job_no_prefix_num,po_break_down_id,po_break_down_id,po_break_down_id,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", '','','0,0,0,0,0,0,0,0,0,0,0,0,0','','');
	?>
    <table class="rpt_table scroll" width="1300" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
   <thead>
        <th width="50">Sl</th> 
        <th width="100">Booking No</th>  
        <th width="80">Booking Date</th>           	 
        <th width="100">Company</th>
        <th width="100">Buyer</th>
        
        <th width="100">Job No.</th>
        <th width="150">PO No.</th>
        <th width="90">File No</th>
        <th width="100">Ref. No</th>
        
        <th width="80">Fabric Nature</th>
        <th width="80">Fabric Source</th>
        <th width="80">Pay Mode</th>
        <th width="80">Supplier</th>
      
        <th width="50">Approved</th>
        <th width="60">Is-Ready</th>
        </thead>
        <tbody>
        <? 
		$i=1;
		$sql_data=sql_select($sql);
		foreach($sql_data as $row){
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
			$po_number=rtrim($job_arr[$row[csf('job_no')]]['po_number'],',');
			$po_numbers=implode(",",array_unique(explode(",",$po_number)));
			//echo $po_number;
		$file_no=rtrim($job_arr[$row[csf('job_no')]]['file_no'],',');
		$ref_no=rtrim($job_arr[$row[csf('job_no')]]['ref_no'],',');
		$file_nos=implode(",",array_unique(explode(",",$file_no)));
		$ref_nos=implode(",",array_unique(explode(",",$ref_no)));  
		$style=$job_arr[$row[csf('job_no')]]['style']; 
		?>
        <tr bgcolor="<? echo $bgcolor;?>" onClick="js_set_value('<? echo $row[csf('booking_no')]  ?>')" style="cursor:pointer">
        <td width="50"><? echo $i;?></td> 
        <td width="100"><? echo $row[csf('booking_no_prefix_num')];?></td>  
        <td width="80"><? echo date("d-m-Y",strtotime($row[csf('booking_date')]));?></td>           	 
        <td width="100"><? echo $comp[$row[csf('company_id')]];?></td>
        <td width="100"><? echo $buyer_arr[$row[csf('buyer_id')]];?></td>
        <td width="100"><? echo $row[csf('job_no')];?></td>
        <td width="150"><p><? echo $po_numbers;?></p></td>
        <td width="90"><p><? echo $file_nos;?></p></td>
        <td width="100"><p><? echo $ref_nos;?></p></td>
        <td width="80"><? echo $item_category[$row[csf('item_category')]];?></td>
        <td width="80"><? echo $fabric_source[$row[csf('fabric_source')]];?></td>
        <td width="80">
        <? echo $pay_mode[$row[csf('pay_mode')]];?>
        </td>
        <td width="80">
		<? 
		if($row[csf('pay_mode')]==3 || $row[csf('pay_mode')]==5){
			echo $comp[$row[csf('supplier_id')]];
		}
		else{
			echo $suplier[$row[csf('supplier_id')]];
		}
		?>
        </td>
       
        <td width="50"><? echo $approved[$row[csf('is_approved')]];?></td>
        <td width="60"><? echo $is_ready[$row[csf('ready_to_approved')]];?></td>
        </tr>
        <?
		$i++;
         }
        ?>
        </tbody>
    </table>
    <?
}



if ($action=="order_search_popup")
{
	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array, selected_name = new Array();	
		function check_all_data() 
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length; 
			tbl_row_count = tbl_row_count;
			for( var i = 1; i <= tbl_row_count; i++ ) 
			{
				js_set_value( i );
			}
		}
		
		function toggle( x, origColor ) 
		{
			//alert(x)
			var newColor = 'yellow';
			//if ( x.style ) 
			//{
			document.getElementById(x).style.backgroundColor = ( newColor == document.getElementById(x).style.backgroundColor )? origColor : newColor;
			//}
		}
		
		function js_set_value( str_data,tr_id ) 
		{
			
			var str_all=str_data.split("_");
			var str_po=str_all[1];
			var str=str_all[0];
			if ( document.getElementById('job_no').value!="" && document.getElementById('job_no').value!=str_all[2] )
			{
				alert('No Job Mix Allowed')
				return;	
			}
			toggle( tr_id, '#FFFFCC');
			document.getElementById('job_no').value=str_all[2];
			
			if( jQuery.inArray( str , selected_id ) == -1 ) 
			{
				selected_id.push( str );
				selected_name.push( str_po );
			}
			else 
			{
				for( var i = 0; i < selected_id.length; i++ ) 
				{
					if( selected_id[i] == str ) break;
				}
			
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
					//alert(selected_id.length)
				if(selected_id.length==0)
				{
					document.getElementById('job_no').value="";
				}
			}
			var id = '' ; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) 
			{
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			$('#po_number_id').val( id );
			$('#po_number').val( name );
		}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
			<?
				$booking_month=0;
				if(str_replace("'","",$cbo_booking_month)<10)
				{
					$booking_month.=str_replace("'","",$cbo_booking_month);
				}
				else
				{
					$booking_month=str_replace("'","",$cbo_booking_month); 
				}
				$start_date="01"."-".$booking_month."-".str_replace("'","",$cbo_booking_year);
				$end_date=cal_days_in_month(CAL_GREGORIAN, $booking_month, str_replace("'","",$cbo_booking_year))."-".$booking_month."-".str_replace("'","",$cbo_booking_year);
				
				if($booking_month!=0)
				{
				 $start_date=$start_date;
				 $end_date=$end_date;
				
				}
				else
				{
				$start_date='';
				$end_date='';
				}
            ?>
            <form name="searchpofrm_1" id="searchpofrm_1">
                <table width="1040"  align="center" rules="all">
                    <tr>
                        <td align="center" width="100%">
                            <table  width="1040" class="rpt_table" align="center" rules="all">
                            
                                <thead> 
                                <tr>
                                 <th width="150" colspan="5"> </th>
                                        <th>
                                          <?
                                           echo create_drop_down( "cbo_string_search_type", 100, $string_search_type,'', 1, "-- Searching Type --" );
                                          ?>
                                        </th>
                                      <th width="150" colspan="5"> </th>
                                </tr> 
                                <tr>               	 
                                    <th width="150">Company Name</th>
                                    <th width="150">Buyer Name</th>
                                    <th width="80">Job No</th>
                                    <th width="100">Style Ref </th>
                                    <th width="70">Internal  Ref. </th>
                                    <th width="70">File No </th>
                                    <th width="80">Order No</th>
                                    <th width="180">Date Range</th>
                                    <th></th>
                                    </tr>           
                                </thead>
                                <tr>
                                    <td> 
                                    <? 
									if(str_replace("'","",$cbo_company_name)==0) $com_show=0;
									else  $com_show=1;
                                    echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "- Select Company -", str_replace("'","",$cbo_company_name), "load_drop_down( 'sample_booking_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );",$com_show);
                                    ?>
                                    </td>
                                    <td id="buyer_td">
                                    <?
									if(str_replace("'","",$cbo_buyer_name)==0) $buyer_show=0;
									else  $buyer_show=1;
									//echo $cbo_buyer_name;
                                    echo create_drop_down( "cbo_buyer_name", 172, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 $buyer_cond and b.buyer_id=buy.id and b.tag_company=$cbo_company_name and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", str_replace("'","",$cbo_buyer_name),"",$buyer_show );
                                    ?>	
                                    </td>
                                    <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:80px"></td>
                                     <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:100px"></td>
                                     <td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:70px"></td>
                                     <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:70px"></td>
                                    <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:80px"></td>
                                    <td>
                                    <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" value="<? echo $start_date; ?>"/>
                                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" value="<? echo $end_date; ?>"/>
                                    </td> 
                                    <td align="center">
                                    <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_internal_ref').value, 'create_po_search_list_view', 'search_div', 'fabric_booking_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100%;" />
                                    </td>
                                </tr>
                                <tr>
                                    <td  align="center"  valign="top" colspan="9">
                                    <input type="hidden" id="po_number_id">
                                    <input type="hidden" id="job_no">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="9" align="center">
                                    <strong>Selected PO Number:</strong> &nbsp;<input type="text" class="text_boxes" readonly style="width:550px" id="po_number">
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" >
                        <input type="button" name="close" onClick="parent.emailwindow.hide();"  class="formbutton" value="Close" style="width:100px" /> 
                        </td>
                    </tr>
                    <tr>
                        <td id="search_div" align="center">
                        </td>
                    </tr>
                </table>
            </form>
        </div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_po_search_list_view")
{
	//echo 'dd';die;
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and a.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.buyer_name='$data[1]'"; else $buyer=""; //{ echo "Please Select Buyer First."; die; }
	
	
	//if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num='$data[4]'  "; else  $job_cond=""; 
	//if (str_replace("'","",$data[5])!="") $order_cond=" and b.po_number like '%$data[5]%'  "; else  $order_cond=""; 
	$job_cond="";
	$order_cond=""; 
	$style_cond="";
	$file_cond="";
	$int_ref_cond="";
	if($data[7]==1)
	{
	if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num='$data[4]'"; //else  $job_cond=""; 
	if (str_replace("'","",$data[5])!="") $order_cond=" and b.po_number = '$data[5]'  "; //else  $order_cond=""; 
	if (trim($data[6])!="") $style_cond=" and a.style_ref_no ='$data[6]'"; //else  $style_cond=""; 
	if (str_replace("'","",$data[7])!="") $file_cond=" and b.file_no='$data[7]'";
	if (str_replace("'","",$data[8])!="") $int_ref_cond=" and b.grouping like '%$data[8]%'";
	}
	if($data[7]==2)
	{
	if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num like '$data[4]%'"; //else  $job_cond=""; 
	if (str_replace("'","",$data[5])!="") $order_cond=" and b.po_number like '$data[5]%'  "; //else  $order_cond=""; 
	if (trim($data[6])!="") $style_cond=" and a.style_ref_no like '$data[6]%'  "; //else  $style_cond="";
	if (str_replace("'","",$data[7])!="") $file_cond=" and b.file_no='$data[7]'";
	if (str_replace("'","",$data[8])!="") $int_ref_cond=" and b.grouping like '%$data[8]%'"; 
	}
	if($data[7]==3)
	{
	if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num like '%$data[4]'"; //else  $job_cond=""; 
	if (str_replace("'","",$data[5])!="") $order_cond=" and b.po_number like '%$data[5]'  "; //else  $order_cond=""; 
	if (trim($data[6])!="") $style_cond=" and a.style_ref_no like '%$data[6]'"; //else  $style_cond="";
	if (str_replace("'","",$data[7])!="") $file_cond=" and b.file_no='$data[7]'";
	if (str_replace("'","",$data[8])!="") $int_ref_cond=" and b.grouping like '%$data[8]%'"; 
	}
	if($data[7]==4 || $data[7]==0)
	{
	if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num like '%$data[4]%'"; //else  $job_cond=""; 
	if (str_replace("'","",$data[5])!="") $order_cond=" and b.po_number like '%$data[5]%'  "; //else  $order_cond=""; 
	if (trim($data[6])!="") $style_cond=" and a.style_ref_no like '%$data[6]%'"; //else  $style_cond=""; 
	if (str_replace("'","",$data[7])!="") $file_cond=" and b.file_no='$data[7]'";
	if (str_replace("'","",$data[8])!="") $int_ref_cond=" and b.grouping like '%$data[8]%'";
	}
	
	if($db_type==0)
	{
	if ($data[2]!="" &&  $data[3]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}
	
	if($db_type==2)
	{
	if ($data[2]!="" &&  $data[3]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}
	
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$arr=array (1=>$comp,2=>$buyer_arr);
	$sql= "select a.job_no_prefix_num,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity,b.id, b.po_number,b.po_quantity,b.shipment_date,a.job_no from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c where a.job_no=b.job_no_mst and a.job_no=c.job_no".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." and a.status_active=1 and b.status_active=1 and c.approved=1 $shipment_date $company $buyer $job_cond $order_cond $style_cond order by a.job_no"; 
	
	echo  create_list_view("list_view", "Job No,Company,Buyer,Style Ref. No,Job Qty.,PO number,PO Qty,Shipment Date", "60,60,50,100,70,150,80,80","750","320",0, $sql , "js_set_value", "id,po_number,job_no", "this.id", 1, "0,company_name,buyer_name,0,0,0,0", $arr , "job_no_prefix_num,company_name,buyer_name,style_ref_no,job_quantity,po_number,po_quantity,shipment_date", '','','0,0,0,0,1,0,1,3','','');
}  

if($action=="terms_condition_popup")
{
	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>
	<script>
function add_break_down_tr(i) 
 {
	var row_num=$('#tbl_termcondi_details tr').length-1;
	if (row_num!=i)
	{
		return false;
	}
	else
	{
		i++;
	 
		 $("#tbl_termcondi_details tr:last").clone().find("input,select").each(function() {
			$(this).attr({
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			  'name': function(_, name) { return name + i },
			  'value': function(_, value) { return value }              
			});  
		  }).end().appendTo("#tbl_termcondi_details");
		 $('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
		  $('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+")");
		  $('#termscondition_'+i).val("");
	}
		  
}

function fn_deletebreak_down_tr(rowNo) 
{   
	
	
		var numRow = $('table#tbl_termcondi_details tbody tr').length; 
		if(numRow==rowNo && rowNo!=1)
		{
			$('#tbl_termcondi_details tbody tr:last').remove();
		}
	
}

function fnc_fabric_booking_terms_condition( operation )
{
	    var row_num=$('#tbl_termcondi_details tr').length-1;
		var data_all="";
		for (var i=1; i<=row_num; i++)
		{
			
			if (form_validation('termscondition_'+i,'Term Condition')==false)
			{
				return;
			}
			
			data_all=data_all+get_submitted_data_string('txt_booking_no*termscondition_'+i,"");
		}
		var data="action=save_update_delete_fabric_booking_terms_condition&operation="+operation+'&total_row='+row_num+data_all;
		//freeze_window(operation);
		http.open("POST","sample_booking_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_fabric_booking_terms_condition_reponse;
}

function fnc_fabric_booking_terms_condition_reponse()
{
	
	if(http.readyState == 4) 
	{
	    var reponse=trim(http.responseText).split('**');
			if (reponse[0].length>2) reponse[0]=10;
			if(reponse[0]==0 || reponse[0]==1)
			{
				parent.emailwindow.hide();
			}
	}
}
    </script>

</head>

<body>
<div align="center" style="width:100%;" >
<? echo load_freeze_divs ("../../../",$permission);  ?>
<fieldset>
        	<form id="termscondi_1" autocomplete="off">
           <input type="text" id="txt_booking_no" name="txt_booking_no" value="<? echo str_replace("'","",$txt_booking_no) ?>"/>
            
            
            <table width="650" cellspacing="0" class="rpt_table" border="0" id="tbl_termcondi_details" rules="all">
                	<thead>
                    	<tr>
                        	<th width="50">Sl</th><th width="530">Terms</th><th ></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no");// quotation_id='$data'
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							?>
                            	<tr id="settr_1" align="center">
                                    <td>
                                    <? echo $i;?>
                                    </td>
                                    <td>
                                    <input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $row[csf('terms')]; ?>"  /> 
                                    </td>
                                    <td> 
                                    <input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
                                    <input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?>);" />
                                    </td>
                                </tr>
                            <?
						}
					}
					else
					{
					$data_array=sql_select("select id, terms from  lib_terms_condition  where is_default=1");// quotation_id='$data'
					foreach( $data_array as $row )
						{
							$i++;
					?>
                    <tr id="settr_1" align="center">
                                    <td>
                                    <? echo $i;?>
                                    </td>
                                    <td>
                                    <input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $row[csf('terms')]; ?>"  /> 
                                    </td>
                                    <td>
                                    <input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
                                    <input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> );" />
                                    </td>
                                </tr>
                    <? 
						}
					} 
					?>
                </tbody>
                </table>
                
                <table width="650" cellspacing="0" class="" border="0">
                	<tr>
                        <td align="center" height="15" width="100%"> </td>
                    </tr>
                	<tr>
                        <td align="center" width="100%" class="button_container">
						        <?
									echo load_submit_buttons( $permission, "fnc_fabric_booking_terms_condition", 0,0 ,"reset_form('termscondi_1','','','','')",1) ; 
									?>
                        </td> 
                    </tr>
                </table>
            </form>
        </fieldset>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}






if($action=="save_update_delete_fabric_booking_terms_condition")
{
$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0";disconnect($con); die;}		
		 $id=return_next_id( "id", "wo_booking_terms_condition", 1 ) ;
		 $field_array="id,booking_no,terms";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $termscondition="termscondition_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$txt_booking_no.",".$$termscondition.")";
			$id=$id+1;
		 }
		// echo  $data_array;
		$rID_de3=execute_query( "delete from wo_booking_terms_condition where  booking_no =".$txt_booking_no."",0);

		 $rID=sql_insert("wo_booking_terms_condition",$field_array,$data_array,1);
		 check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "0**".$new_booking_no[0];
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_booking_no[0];
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "0**".$new_booking_no[0];
			}
			else{
				oci_rollback($con);
				echo "10**".$new_booking_no[0];
			}
		}
		disconnect($con);
		die;
	}	
}


 





if ($action=="populate_order_data_from_search_popup")
{
	 
	$data_array=sql_select("select a.job_no,a.company_name,a.buyer_name,b.file_no,b.grouping from wo_po_details_master a, wo_po_break_down b where b.id in (".$data.") and a.job_no=b.job_no_mst");
	foreach ($data_array as $row)
	{
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_name")]."';\n";  
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_name")]."';\n";  
		echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('txt_file').value = '".$row[csf("file_no")]."';\n";
		echo "document.getElementById('txt_ref').value = '".$row[csf("grouping")]."';\n";
		echo "document.getElementById('txt_file2').value = '".$row[csf("file_no")]."';\n";
		echo "document.getElementById('txt_ref2').value = '".$row[csf("grouping")]."';\n";
	}
}

if ($action=="populate_data_from_search_popup")
{
	 $sql= "select  id,booking_no,booking_date,company_id,buyer_id,job_no,po_break_down_id,item_category,fabric_source,currency_id,exchange_rate,pay_mode,booking_month,supplier_id,attention,delivery_date,source,booking_year,is_approved,fabric_composition,ready_to_approved,tagged_booking_no from wo_booking_mst  where booking_no='$data'"; 
	
	 $data_array=sql_select($sql);
	 foreach ($data_array as $row)
	 {
		echo "load_drop_down( 'requires/sample_booking_controller', '".$row[csf("pay_mode")]."', 'load_drop_down_suplier', 'sup_td' );\n";
        echo "document.getElementById('txt_fabric_booking_no').value = '".$row[csf("tagged_booking_no")]."';\n";  
		echo "document.getElementById('txt_order_no_id').value = '".$row[csf("po_break_down_id")]."';\n";  
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";  
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";  
		echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('txt_booking_no').value = '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('update_id').value = '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_fabric_natu').value = '".$row[csf("item_category")]."';\n";
		echo "document.getElementById('cbo_fabric_source').value = '".$row[csf("fabric_source")]."';\n";
		echo "document.getElementById('cbo_currency').value = '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('txt_exchange_rate').value = '".$row[csf("exchange_rate")]."';\n";
		
		
		echo "document.getElementById('cbo_pay_mode').value = '".$row[csf("pay_mode")]."';\n";
		echo "document.getElementById('txt_booking_date').value = '".change_date_format($row[csf("booking_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('cbo_booking_month').value = '".$row[csf("booking_month")]."';\n";
		echo "document.getElementById('cbo_supplier_name').value = '".$row[csf("supplier_id")]."';\n";
		echo "document.getElementById('txt_attention').value = '".$row[csf("attention")]."';\n";
		echo "document.getElementById('txt_delivery_date').value = '".change_date_format($row[csf("delivery_date")],'dd-mm-yyyy','-')."';\n";
	    echo "document.getElementById('cbo_source').value = '".$row[csf("source")]."';\n";
		echo "document.getElementById('cbo_booking_year').value = '".$row[csf("booking_year")]."';\n";
		echo "document.getElementById('id_approved_id').value = '".$row[csf("is_approved")]."';\n";
		echo "document.getElementById('cbo_ready_to_approved').value = '".$row[csf("ready_to_approved")]."';\n";
		echo "document.getElementById('txt_fabriccomposition').value = '".$row[csf("fabric_composition")]."';\n";
		if($row[csf("is_approved")]==1)
		{
			//echo "document.getElementById('app_sms').innerHTML = 'This booking is approved';\n";
			echo "document.getElementById('app_sms2').innerHTML = 'This booking is approved';\n";
		}
		else
		{
			//echo "document.getElementById('app_sms').innerHTML = '';\n";
			echo "document.getElementById('app_sms2').innerHTML = '';\n";
		}
		$po_no="";	$file_no="";	$ref_no="";
		$sql_po= "select po_number,file_no,grouping from  wo_po_break_down  where id in(".$row[csf('po_break_down_id')].")"; 
		$data_array_po=sql_select($sql_po);
		foreach ($data_array_po as $row_po)
		{
			$po_no.=$row_po[csf('po_number')].",";
			$file_no.=$row_po[csf('file_no')].",";
			$ref_no.=$row_po[csf('grouping')].",";
		}
		echo "document.getElementById('txt_order_no').value = '".substr($po_no, 0, -1)."';\n";
		echo "document.getElementById('txt_file').value = '".substr($file_no, 0, -1)."';\n";
		echo "document.getElementById('txt_ref').value = '".substr($ref_no, 0, -1)."';\n";
		echo "document.getElementById('txt_file2').value = '".substr($file_no, 0, -1)."';\n";
		echo "document.getElementById('txt_ref2').value = '".substr($ref_no, 0, -1)."';\n";
		echo "enable_disable('".$row[csf("fabric_source")]."');\n";
	 }
}

if($action=="populate_details_data_from_for_update")
{
	$data_array=sql_select("select id, pre_cost_fabric_cost_dtls_id, po_break_down_id, sample_type, fabric_color_id, gmts_color_id, item_size, gmts_size, dia_width, fin_fab_qnty, process_loss_percent, grey_fab_qnty, rate, amount, bh_qty, rf_qty, uom FROM wo_booking_dtls WHERE id ='".$data."' and is_short=2 and status_active=1 and is_deleted=0");
	foreach ($data_array as $row)
	{
		$file_no=""; $ref_no="";
		$sql_po= "select po_number,file_no,grouping from  wo_po_break_down  where id in(".$row[csf('po_break_down_id')].")"; 
		$data_array_po=sql_select($sql_po);
		foreach ($data_array_po as $row_po)
		{
			$file_no.=$row_po[csf('file_no')].",";
			$ref_no.=$row_po[csf('grouping')].",";
		}
		echo "document.getElementById('cbo_order_id').value = '".$row[csf("po_break_down_id")]."';\n";
		echo "document.getElementById('txt_file2').value = '".substr($file_no, 0, -1)."';\n";
		echo "document.getElementById('txt_ref2').value = '".substr($ref_no, 0, -1)."';\n";  
		echo "document.getElementById('cbo_fabricdescription_id').value = '".$row[csf("pre_cost_fabric_cost_dtls_id")]."';\n";  
		echo "document.getElementById('cbo_sample_type').value = '".$row[csf("sample_type")]."';\n";  
		echo "document.getElementById('cbo_fabriccolor_id').value = '".$row[csf("fabric_color_id")]."';\n";  
		
		echo "document.getElementById('cbo_garmentscolor_id').value = '".$row[csf("gmts_color_id")]."';\n";  
		echo "document.getElementById('cbo_itemsize_id').value = '".$row[csf("item_size")]."';\n";
		
		echo "document.getElementById('cbo_garmentssize_id').value = '".$row[csf("gmts_size")]."';\n";
		echo "document.getElementById('txt_dia_width').value = '".$row[csf("dia_width")]."';\n";
		echo "document.getElementById('txt_finish_qnty').value = '".$row[csf("fin_fab_qnty")]."';\n";
		echo "document.getElementById('txt_process_loss').value = '".$row[csf("process_loss_percent")]."';\n";
		echo "document.getElementById('txt_grey_qnty').value = '".$row[csf("grey_fab_qnty")]."';\n";
		echo "document.getElementById('txt_rate').value = '".$row[csf("rate")]."';\n";
		echo "document.getElementById('txt_amount').value = '".$row[csf("amount")]."';\n";
		echo "document.getElementById('update_id_details').value = '".$row[csf("id")]."';\n";
		
		echo "document.getElementById('txt_bh_qty').value = '".$row[csf("bh_qty")]."';\n";
		echo "document.getElementById('txt_rf_qty').value = '".$row[csf("rf_qty")]."';\n";
		echo "document.getElementById('cbouom').value = '".$row[csf("uom")]."';\n";
		
		 /* $sql_acc= "select item_group_id,description,uom,qty,remarks from  wo_booking_accessories_dtls   where wo_booking_dtls_id =".$row[csf("id")].""; 
		$data_array_acc=sql_select($sql_acc);
		$data_all="";
		foreach ($data_array_acc as $row)
		{
			if($data_all!=""){$data_all.="__";}
			$data_all.=$row[csf('item_group_id')].'*^*'.$row[csf('description')].'*^*'.$row[csf('uom')].'*^*'.$row[csf('qty')].'*^*'.$row[csf('remarks')];
		}*/
		//echo "document.getElementById('trims_acc_hidden_data').value = '".$data_all."';\n";
		
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_fabric_booking_dtls',2);\n";  
		//set_button_status(1, permission, 'fnc_fabric_booking_dtls',2)
	}
}

if($action=="save_update_delete_trims_acc")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		 $id=return_next_id( "id", "wo_booking_accessories_dtls", 1 ) ;
		 $field_array="id,booking_no,booking_mst_id,item_group_id,description,uom,qty,remarks";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $trim_group="itemgroup_".$i;
			 $description="description_".$i;
			 $cons_uom="uom_".$i;
			 $qty="qty_".$i;
			 $remarks="remarks_".$i;
			 
			if ($i!=1) $data_array .=",";
			$data_array.="(".$id.",".$txt_booking_no.",".$update_id.",".$$trim_group.",".$$description.",".$$cons_uom.",".$$qty.",".$$remarks.")";
			$id=$id+1;
		 }
		// echo  $data_array;
		$rID_de=execute_query( "delete from wo_booking_accessories_dtls where  booking_no =".$txt_booking_no."",0);

		 $rID=sql_insert("wo_booking_accessories_dtls",$field_array,$data_array,1);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "0**".$new_booking_no[0];
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_booking_no[0];
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "0**".$new_booking_no[0];
			}
			else{
				oci_rollback($con);
				echo "10**".$new_booking_no[0];
			}
		}
		disconnect($con);
		die;
	}	
}



if($action=="acc_popup")
{
	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//$lib_item_group_arr=return_library_array( "select item_name,id from lib_item_group where item_category=4 and is_deleted=0  and  status_active=1 order by item_name", "id", "item_name");
	 //$lib_item_uom_arr=return_library_array( "select trim_uom,id from lib_item_group where item_category=4 and is_deleted=0  and  status_active=1 order by trim_uom", "id", "trim_uom");
	
	?><script> var trim_uom_arr=Array(); </script><?
	
	$itemArray=sql_select( "select item_name,trim_uom,id from lib_item_group where item_category=4 and is_deleted=0  and  status_active=1 order by item_name" );
	foreach ($itemArray as $row)
	{
	?>
		<script> trim_uom_arr[<? echo $row[csf('id')];?>]='<? echo $row[csf('trim_uom')].'*'.$unit_of_measurement[$row[csf('trim_uom')]];?>' </script>
	<?
		$lib_item_group_arr[$row[csf('id')]]=$row[csf('item_name')];
	}
	
	
?>
	<script>
	
function load_trims_uom(item_group,str){
	var uom=trim_uom_arr[item_group].split('*');
	var html="<option value='"+uom[0]+"'>"+uom[1]+"</option>";
	document.getElementById('uom_'+str).innerHTML=html;
}
	
	
function add_break_down_tr(i) 
 {
	var row_num=$('#tbl_accessories_details tr').length-1;
	if (row_num!=i)
	{
		return false;
	}
	else
	{
		i++;
	 
		 $("#tbl_accessories_details tr:last").clone().find("input,select").each(function() {
			$(this).attr({
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			  'name': function(_, name) { return name + i },
			  'value': function(_, value) { return '' }              
			});  
		  }).end().appendTo("#tbl_accessories_details");
		
		 $('#itemgroup_'+i).removeAttr("onChange").attr("onChange","load_trims_uom(this.value,"+i+");");
		
		 $('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
		 $('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+")");
		 
		 $('#increase_'+i).removeAttr("value").attr("value","+");
		 $('#decrease_'+i).removeAttr("value").attr("value","-");
		 
		 
		 $('#termscondition_'+i).val("");
		 $('#tbl_accessories_details tbody tr:last td:first-child').text(i);
	}
		  
}

function fn_deletebreak_down_tr(rowNo) 
{   
	
		var numRow = $('table#tbl_accessories_details tbody tr').length; 
		if(numRow==rowNo && rowNo!=1)
		{
			$('#tbl_accessories_details tbody tr:last').remove();
		}
	
}


function fnc_trims_acc( operation )
{
	    var row_num=$('#tbl_accessories_details tr').length-1;
		var data_all="";
		for (var i=1; i<=row_num; i++)
		{
			
			
			data_all=data_all+get_submitted_data_string('txt_booking_no*update_id*itemgroup_'+i+'*description_'+i+'*uom_'+i+'*qty_'+i+'*remarks_'+i,"");
		}
		var data="action=save_update_delete_trims_acc&operation="+operation+'&total_row='+row_num+data_all;
		//freeze_window(operation);
		http.open("POST","sample_booking_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_trims_acc_reponse;
}

function fnc_trims_acc_reponse()
{
	
	if(http.readyState == 4) 
	{
	    var reponse=trim(http.responseText).split('**');
			if (reponse[0].length>2) reponse[0]=10;
			if(reponse[0]==0 || reponse[0]==1)
			{
				parent.emailwindow.hide();
			}
	}
}


    </script>

</head>

<body>
<div align="center" style="width:100%;" >
<? echo load_freeze_divs ("../../../",$permission);  ?>
<fieldset>
    <form id="termscondi_1" autocomplete="off">
    <input type="hidden" id="txt_booking_no"   name="txt_booking_no"  value="<? echo str_replace("'","",$txt_booking_no);?>"  />
    <input type="hidden" id="update_id"   name="update_id"  value="<? echo str_replace("'","",$update_id);?>"  /> 
    <table width="650" cellspacing="0" class="rpt_table" border="0" id="tbl_accessories_details" rules="all">
            <thead>
                <tr>
                    <th width="30">Sl</th>
                    <th width="100">Item Group</th>
                    <th width="100">Description</th>
                    <th width="70">UOM</th>
                    <th width="70">Qty</th>
                    <th>Remarks</th>
                    <th width="80"></th>
                </tr>
            </thead>
            <tbody>
            <?
        
           $sql = "select booking_no,qty,item_group_id,description, uom,remarks from wo_booking_accessories_dtls where booking_no=".$txt_booking_no."";
            
            $data_array=sql_select($sql);
                
            if ( count($data_array)>0)
            {
                
                $i=0;
                foreach( $data_array as $row )
                {
                    $i++;
                    ?>
                        <tr id="settr_1" align="center">
                            <td><? echo $i;?></td>
                            <td>
                            <?
                            echo create_drop_down( "itemgroup_$i", 95, $lib_item_group_arr,"", 1, "-- Select --", $row[csf('item_group_id')], "",0,"" );
                            ?>
                            </td>
                           
                            <td>
                            <input type="text" id="description_<? echo $i;?>"   name="description_<? echo $i;?>" style="width:80%"  class="text_boxes"  value="<? echo $row[csf('description')]; ?>"  /> 
                            </td>
                           
                            <td id="uom_td_<? echo $i;?>">
                            <?
                            echo create_drop_down( "uom_$i", 65, $unit_of_measurement,"", 1, "-- Select --", $row[csf('uom')], "",1,$row[csf('uom')] );
                            ?>
                            </td>
                           
                            <td>
                            <input type="text" id="qty_<? echo $i;?>"   name="qty_<? echo $i;?>" style="width:80%"  class="text_boxes_numeric"  value="<? echo$row[csf('qty')];?>"  /> 
                            </td>
                           
                            <td>
                            <input type="text" id="remarks_<? echo $i;?>"   name="remarks_<? echo $i;?>" style="width:90%"  class="text_boxes"  value="<? echo$row[csf('remarks')];?>"  /> 
                            </td>
                           
                           
                            <td> 
                            <input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
                            <input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?>);" />

                            </td>
                        </tr>
                    <?
                }
            }
            else
            {
                $sql = "select trim_group,description, cons_uom from wo_pre_cost_trim_cost_dtls where job_no=".$txt_job_no."";
				$data_arr=sql_select($sql);
				foreach( $data_arr as $row )
                {
					$data_array[]=array(
						csf('trim_group')=>$row[csf('trim_group')],
						csf('description')=>$row[csf('description')],
						csf('cons_uom')=>$row[csf('cons_uom')],
						csf('qty')=>$row[csf('qty')],
						csf('remarks')=>$row[csf('remarks')]
					);
					$custom_item_group_arr[$row[csf('trim_group')]]=$lib_item_group_arr[$row[csf('trim_group')]];
				}
				
				$i=0;
                foreach( $data_array as $row )
                {
                    $i++;
                    ?>
                        <tr id="settr_1" align="center">
                            <td><? echo $i;?></td>
                            <td>
                            <?
                            echo create_drop_down( "itemgroup_$i", 95, $custom_item_group_arr,"", 1, "-- Select --", $row[csf('trim_group')], "",0,"" );
                            ?>
                            </td>
                           
                            <td>
                            <input type="text" id="description_<? echo $i;?>"   name="description_<? echo $i;?>" style="width:80%"  class="text_boxes"  value="<? echo $row[csf('description')]; ?>"  /> 
                            </td>
                           
                            <td id="uom_td_<? echo $i;?>">
                            <?
                            echo create_drop_down( "uom_$i", 65, $unit_of_measurement,"", 1, "-- Select --", $row[csf('cons_uom')], "",0,$row[csf('cons_uom')] );
                            ?>
                            </td>
                           
                            <td>
                            <input type="text" id="qty_<? echo $i;?>"   name="qty_<? echo $i;?>" style="width:80%"  class="text_boxes_numeric"  value=""  /> 
                            </td>
                           
                            <td>
                            <input type="text" id="remarks_<? echo $i;?>"   name="remarks_<? echo $i;?>" style="width:90%"  class="text_boxes"  value=""  /> 
                            </td>
                           
                           
                            <td> 
                            <input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
                            <input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?>);" />

                            </td>
                        </tr>
                    <?
				}
            } 
            ?>
        </tbody>
        </table>
        
        <table width="650" cellspacing="0" class="" border="0">
            <tr>
                <td align="center" height="15" width="100%"> </td>
            </tr>
            <tr>
                <td align="center" width="100%" class="button_container">
                       <?
                        echo load_submit_buttons( $permission, "fnc_trims_acc", 0,0 ,"reset_form('termscondi_1','','','','')",1) ; 
                        ?>
                </td> 
            </tr>
        </table>
    </form>
</fieldset>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}


if ($action=="fabric_booking_no_popup"){
	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
	?>
	<script>
	function js_set_fabric_value(fabric_booking_no){
		document.getElementById('selected_fabric_booking_no').value=fabric_booking_no;       
		parent.emailwindow.hide();
	}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="1050" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
                <tr>
                    <th colspan="11">
                    <?
                    // echo create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --" );
                    ?>
                    <input type="hidden" id="cbo_search_category"> 
                    </th>
                </tr>
                <tr>               	 
                    <th width="150">Company Name</th>
                    <th width="150">Buyer Name</th>
                    <th width="80">Booking No</th>
                    <th width="80">Job No</th>
                    <th width="70">File No</th>
                    <th width="70">Internal Ref.</th>
                    <th width="100">Style Ref </th>
                    <th width="100">Order No</th>
                    <th width="130" colspan="2">Date Range</th>
                    <th>&nbsp;</th>  
                </tr>         
            </thead>
            <tr class="general">
                <td> 
                <input type="hidden" id="selected_fabric_booking_no"> 
                <? 
                echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", '', "load_drop_down( 'size_color_breakdown_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
                ?>
                </td>
                <td id="buyer_td">
                <? echo create_drop_down( "cbo_buyer_name", 172, $blank_array,"", 1, "-- Select Buyer --" );?>	
                </td>
                <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:70px"></td>
                <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:70px"></td>
                <td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:60px"></td>
                <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:60px"></td>
                <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:90px"></td>
                <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:90px"></td>
                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px"></td>
                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px"></td>
                <td align="center">
                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_order_search').value, 'create_fabric_booking_search_list_view', 'search_div', 'sample_booking_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
            </tr>
            <tr>
                <td colspan="11" align="center" valign="middle"> <?=load_month_buttons(1); ?></td>
            </tr>
        </table>
        </form>
        <div valign="top" id="search_div"></div> 
        </div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=="create_fabric_booking_search_list_view"){
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.buyer_id='$data[1]'"; else $buyer="";
	if($db_type==0) $year_cond=" and SUBSTRING_INDEX(b.insert_date, '-', 1)=$data[5]";
	if($db_type==2) $year_cond=" and to_char(b.insert_date,'YYYY')=$data[5]";
	if($db_type==0) $booking_year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[5]";
	if($db_type==2) $booking_year_cond=" and to_char(a.insert_date,'YYYY')=$data[5]";
	if($data[7]==1)
	{
		if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num='$data[6]'    "; else  $booking_cond="";
		if (str_replace("'","",$data[4])!="") $job_cond=" and b.job_no_prefix_num='$data[4]'  "; else  $job_cond=""; 
		if (trim($data[10])!="") $style_cond=" and b.style_ref_no ='$data[10]'"; //else  $style_cond=""; 
		if (str_replace("'","",$data[11])!="") $order_cond=" and c.po_number = '$data[11]'  "; //else  $order_cond="";
	}
	if($data[7]==2)
	{
		if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num like '$data[6]%'  $booking_year_cond  "; else  $booking_cond="";
		if (str_replace("'","",$data[4])!="") $job_cond=" and b.job_no_prefix_num like '$data[4]%'  $year_cond  "; else  $job_cond=""; 
		if (trim($data[10])!="") $style_cond=" and b.style_ref_no like '$data[10]%'"; //else  $style_cond=""; 
		if (str_replace("'","",$data[11])!="") $order_cond=" and c.po_number like '$data[11]%'  "; //else  $order_cond="";
	}
	
	if($data[7]==3)
	{
		if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[6]'  $booking_year_cond  "; else  $booking_cond="";
		if (str_replace("'","",$data[4])!="") $job_cond=" and b.job_no_prefix_num like '%$data[4]'  $year_cond  "; else  $job_cond=""; 
		if (trim($data[10])!="") $style_cond=" and b.style_ref_no like'%$data[10]'"; //else  $style_cond=""; 
		if (str_replace("'","",$data[11])!="") $order_cond=" and c.po_number like '%$data[11]'  "; //else  $order_cond="";
	}
	if($data[7]==4 || $data[7]==0)
	{
		if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[6]%'  $booking_year_cond  "; else  $booking_cond="";
		if (str_replace("'","",$data[4])!="") $job_cond=" and b.job_no_prefix_num like '%$data[4]%'  $year_cond  "; else  $job_cond=""; 
		if (trim($data[10])!="") $style_cond=" and b.style_ref_no like '%$data[10]%'"; //else  $style_cond=""; 
		if (str_replace("'","",$data[11])!="") $order_cond=" and c.po_number like '%$data[11]%'  "; //else  $order_cond="";
	}
	
	$file_no = str_replace("'","",$data[8]);
	$internal_ref = str_replace("'","",$data[9]);
	if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and c.file_no='".trim($file_no)."' "; 
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and c.grouping='".trim($internal_ref)."' "; 
	
	if($db_type==0){
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	if($db_type==2){
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	}
	
	$approved=array(0=>"No",1=>"Yes");
	$is_ready=array(0=>"No",1=>"Yes",2=>"No");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	
	$sql= "select a.booking_no, a.booking_no_prefix_num, a.booking_date, a.company_id, a.buyer_id, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved, b.job_no_prefix_num, b.style_ref_no, b.gmts_item_id, c.id as po_break_down_id, c.po_number, c.file_no, c.grouping as int_ref_no  
		from wo_booking_mst a, wo_booking_dtls p, wo_po_details_master b ,wo_po_break_down c 
		where a.job_no=p.job_no and a.booking_no=p.booking_no and p.po_break_down_id=c.id and b.job_no=c.job_no_mst and a.booking_type=1 and a.is_short=2 and  a.status_active=1 and a.is_deleted=0 ". set_user_lavel_filtering(' and a.buyer_id','buyer_id')." $company $buyer $job_cond $booking_date $booking_cond $file_no_cond  $internal_ref_cond $style_cond $order_cond";
	
	$sql_result=sql_select($sql);
	
	$booking_data=array();
	foreach($sql_result as $row)
	{
		$booking_data[$row[csf("booking_no")]]["booking_no"]=$row[csf("booking_no")];
		$booking_data[$row[csf("booking_no")]]["booking_no_prefix_num"]=$row[csf("booking_no_prefix_num")];
		$booking_data[$row[csf("booking_no")]]["booking_date"]=$row[csf("booking_date")];
		$booking_data[$row[csf("booking_no")]]["company_id"]=$row[csf("company_id")];
		$booking_data[$row[csf("booking_no")]]["buyer_id"]=$row[csf("buyer_id")];
		$booking_data[$row[csf("booking_no")]]["item_category"]=$row[csf("item_category")];
		
		$booking_data[$row[csf("booking_no")]]["fabric_source"]=$row[csf("fabric_source")];
		$booking_data[$row[csf("booking_no")]]["supplier_id"]=$row[csf("supplier_id")];
		$booking_data[$row[csf("booking_no")]]["is_approved"]=$row[csf("is_approved")];
		$booking_data[$row[csf("booking_no")]]["ready_to_approved"]=$row[csf("ready_to_approved")];
		$booking_data[$row[csf("booking_no")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
		$booking_data[$row[csf("booking_no")]]["style_ref_no"]=$row[csf("style_ref_no")];		
		$booking_data[$row[csf("booking_no")]]["gmts_item_id"]=$row[csf("gmts_item_id")];
		
		$booking_data[$row[csf("booking_no")]]["po_break_down_id"].=$row[csf("po_break_down_id")].",";
		$booking_data[$row[csf("booking_no")]]["po_number"].=$row[csf("po_number")].",";
		$booking_data[$row[csf("booking_no")]]["file_no"].=$row[csf("file_no")].",";
		$booking_data[$row[csf("booking_no")]]["int_ref_no"].=$row[csf("int_ref_no")].",";
	}
	
	?>
    <table width="1250" class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" align="left">
        <thead>
            <tr>
                <th width="30">Sl</th>
                <th width="50">Booking No</th>
                <th width="60">Booking Date</th>
                <th width="70">Company</th>
                <th width="70">Buyer</th>   
                <th width="50">Job No.</th>
                <th width="100">Style Ref.</th>
                <th width="100">Gmts Item</th>
                <th width="150">PO number</th> 
                <th width="80">Internal Ref</th> 
                <th width="80">File No</th>
                <th width="80">Fabric Nature</th>
                <th width="70">Fabric Source</th>
                <th width="80">Supplier</th>
                <th width="50">Approved</th> 
                <th>Is-Ready</th>  
            </tr>
        </thead>
    </table>
    <div style=" max-height:300px; overflow-y:scroll; width:1250px"  align="left">
    <table width="1230" class="rpt_table" id="list_view" border="1" rules="all">
        <tbody>
        <?
		$i=1;
		foreach($booking_data as $row)
		{
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
        	<tr bgcolor="<?=$bgcolor; ?>" style="cursor:pointer;" onClick="js_set_fabric_value('<?=$row[("booking_no")]; ?>');">
            	<td width="30" align="center"><?=$i; ?></td>
                <td width="50" align="center" style="word-break:break-all"><?=$row[("booking_no_prefix_num")]; ?>&nbsp;</td>
                <td width="60" align="center" style="word-break:break-all"><? if($row[("booking_date")]!="" && $row[("booking_date")]!="0000-00-00")  echo change_date_format($row[("booking_date")]);  ?></td>
                <td width="70" style="word-break:break-all"><?=$comp[$row[("company_id")]]; ?>&nbsp;</td>
                <td width="70" style="word-break:break-all"><? echo $buyer_arr[$row[("buyer_id")]]; ?>&nbsp;</td>   
                <td width="50" style="word-break:break-all" align="center"><? echo $row[("job_no_prefix_num")]; ?>&nbsp;</td>
                <td width="100" style="word-break:break-all"><? echo $row[("style_ref_no")]; ?>&nbsp;</td>
                <td width="100" style="word-break:break-all">
				<?
				$garments_item_arr=explode(",",$row[("gmts_item_id")]);
				$all_garments_item="";
				foreach($garments_item_arr as $item_id)
				{
					$all_garments_item.=$garments_item[$item_id].","; 
				}
				echo chop($all_garments_item,","); 
				?>&nbsp;</td>
                <td width="150" style="word-break:break-all"><?=implode(",",array_unique(explode(",",chop($row[("po_number")],","))));  ?>&nbsp;</td> 
                <td width="80" style="word-break:break-all"><?=implode(",",array_unique(explode(",",chop($row[("int_ref_no")],","))));  ?>&nbsp;</td> 
                <td width="80" style="word-break:break-all"><?=implode(",",array_unique(explode(",",chop($row[("file_no")],","))));  ?>&nbsp;</td>
                <td width="80" style="word-break:break-all"><?=$item_category[$row[("item_category")]]; ?>&nbsp;</td>
                <td width="70" style="word-break:break-all"><?=$fabric_source[$row[("fabric_source")]]; ?>&nbsp;</td>
                <td width="80" style="word-break:break-all"><?=$suplier[$row[("supplier_id")]]; ?>&nbsp;</td>
                <td width="50" style="word-break:break-all"><?=$approved[$row[("is_approved")]]; ?>&nbsp;</td> 
                <td style="word-break:break-all"><?=$is_ready[$row[("ready_to_approved")]]; ?>&nbsp;</td>
            </tr>
            <?
			$i++;
		}
		?>
        </tbody>
    </table>
    </div>
    <?
	exit();
}
?>