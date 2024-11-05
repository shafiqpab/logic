<?
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
require_once('../../../includes/class4/class.conditions.php');
require_once('../../../includes/class4/class.reports.php');
require_once('../../../includes/class4/class.fabrics.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$permission=$_SESSION['page_permission'];
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');

//---------------------------------------------------- Start---------------------------------------------------------------------------
//$po_number=return_library_array( "select id,po_number from wo_po_break_down", "id", "po_number"  );
$color_library=return_library_array( "select id,color_name from lib_color where status_active =1 and is_deleted=0", "id", "color_name"  );
$size_library=return_library_array( "select id,size_name from lib_size where status_active =1 and is_deleted=0", "id", "size_name"  );
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );

if ($action=="load_drop_down_buyer"){
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$data and buy.id in (select  buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'requires/sample_booking_controller', this.value, 'load_drop_down_buyer_tag_sample', 'sample_td' );" );
	exit();
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
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$data and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
}

if($action=="print_button_variable_setting"){
	$print_report_format=0;
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=2 and report_id=3 and is_deleted=0 and status_active=1");
	echo "document.getElementById('report_ids').value = '".$print_report_format."';\n";
	echo "print_report_button_setting('".$print_report_format."');\n";
	exit();
}

if ($action=="load_drop_down_po_number"){
	$po_number_arr=return_library_array( "select id,po_number from wo_po_break_down where id in($data)", "id", "po_number"  );

	echo create_drop_down( "cbo_order_id",130, $po_number_arr,"", 1, "--Select--", "", "","",$data,"","","","" );
}
if($action=="check_conversion_rate"){
	$data=explode("**",$data);
	if($db_type==0){
		$conversion_date=change_date_format($data[1], "Y-m-d", "-",1);
	}
	else{
		$conversion_date=change_date_format($data[1], "d-M-y", "-",1);
	}
	$currency_rate=set_conversion_rate( $data[0], $conversion_date,$data[2] );
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

	$nameArray=sql_select("select a.id as pre_cost_fabric_cost_dtls_id, a.body_part_id, a.color_type_id, a.gsm_weight, a.construction, a.composition FROM wo_pre_cost_fabric_cost_dtls a, wo_po_break_down b WHERE a.job_id=b.job_id and b.id in (".$txt_order_no_id.") and a.status_active=1 and b.status_active=1  $cbo_fabric_natu $cbo_fabric_source_cond group by a.id,a.body_part_id,a.color_type_id,a.gsm_weight,a.construction,a.composition order by a.id");
	$fabric_description_array= array();
	foreach ($nameArray as $result){
		if (count($nameArray)>0 ){
			$fabric_description_array[$result[csf("pre_cost_fabric_cost_dtls_id")]]=$body_part[$result[csf("body_part_id")]].', '.$color_type[$result[csf("color_type_id")]].', '.$result[csf("construction")].', '.$result[csf("composition")].', '.$result[csf("gsm_weight")];
		}
	}
	unset($nameArray);
	//print_r($fabric_description_array);
	echo create_drop_down( "cbo_fabricdescription_id", 420, $fabric_description_array,"", 1, "--Select--", "", "load_drop_down( 'requires/sample_booking_controller', this.value, 'load_drop_down_uom', 'uom_td');get_php_form_data( this.value, 'populate_rate_data', 'requires/sample_booking_controller' );","","","","","","" );//
	exit();
}

if ($action=="populate_rate_data")
{
	$rate=return_field_value("rate", "wo_pre_cost_fabric_cost_dtls","id='$data' and status_active =1 and is_deleted=0");
//echo $data;
echo "document.getElementById('txt_rate').value = '".$rate."';\n";
exit();
}
if ($action=="load_drop_down_gmts_color"){
	$data=explode("_",$data);
	$txt_order_no_id=$data[0];
	$cbo_fabric_natu=$data[1];
	$cbo_fabric_source=$data[2];
	$color_library_order=return_library_array( "select b.color_number_id,a.color_name from lib_color a , wo_po_color_size_breakdown b where a.id=b.color_number_id and b.po_break_down_id in (".$txt_order_no_id.") and b.status_active=1 ", "color_number_id", "color_name"  );
	echo create_drop_down( "cbo_garmentscolor_id", 130, $color_library_order,"", 1, "-- Select Color --", $selected, "" );
}

if ($action=="load_drop_down_fabric_color"){
	$data=explode("_",$data);
	$txt_order_no_id=$data[0];
	$cbo_fabric_natu=$data[1];
	$cbo_fabric_source=$data[2];
	if ($cbo_fabric_natu!=0) $cbo_fabric_natu="and fab_nature_id='$cbo_fabric_natu'";
	if ($cbo_fabric_source!=0) $cbo_fabric_source_cond="and fabric_source='$cbo_fabric_source'";
	
	$fabColor=sql_select("select d.job_no,d.pre_cost_fabric_cost_dtls_id as fab_dtls_id,d.gmts_color_id,d.contrast_color_id from wo_pre_cos_fab_co_color_dtls d,wo_po_break_down b where d.job_id=b.job_id and b.id in (".$txt_order_no_id.") and  b.status_active=1 and  d.status_active=1 ");
	 
	foreach ($fabColor as $row){
		$contrast_color_arr[$row[csf("job_no")]][$row[csf("fab_dtls_id")]][$row[csf("gmts_color_id")]]=$row[csf("contrast_color_id")];
	}
	unset($fabColor);
	
	$nameArray=sql_select( "select a.id as pre_cost_fabric_cost_dtls_id, a.job_no, a.color_size_sensitive, a.color,	a.color_break_down,	c.color_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b,	wo_po_color_size_breakdown c WHERE a.job_id=c.job_id  and a.job_id=b.job_id and c.job_id=b.job_id and a.id=b.pre_cost_fabric_cost_dtls_id and b.color_size_table_id=c.id and  a.item_number_id=c.item_number_id and  c.po_break_down_id=b.po_break_down_id and c.color_number_id=b.color_number_id and b.gmts_sizes=c.size_number_id  and  a.status_active=1 and c.status_active=1 and c.po_break_down_id in (".$txt_order_no_id.")  $cbo_fabric_natu $cbo_fabric_source_cond order by a.id");
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
				//$color_id=return_field_value("contrast_color_id","wo_pre_cos_fab_co_color_dtls","job_no='".$result[csf('job_no')]."' and pre_cost_fabric_cost_dtls_id=".$result[csf('pre_cost_fabric_cost_dtls_id')]." and gmts_color_id=".$result[csf('color_number_id')]."");
				$color_id=$contrast_color_arr[$result[csf("job_no")]][$result[csf("pre_cost_fabric_cost_dtls_id")]][$result[csf("color_number_id")]];
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
	echo create_drop_down( "cbo_fabriccolor_id",172, $fabric_color_array,"", 1, "-- Select Color --", "", "","","","","","","" );
}

if ($action=="load_drop_down_gmts_size"){
	$data=explode("_",$data);
	$txt_order_no_id=$data[0];
	$cbo_fabric_natu=$data[1];
	$cbo_fabric_source=$data[2];
	$size_library_order=return_library_array( "select b.size_number_id,a.size_name from lib_size a, wo_po_color_size_breakdown b where a.id=b.size_number_id and b.po_break_down_id in (".$txt_order_no_id.") and b.status_active=1 ", "size_number_id", "size_name");

	echo create_drop_down( "cbo_garmentssize_id", v, $size_library_order,"", 1, "-- Select Size --", $selected, "" );
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
	a.job_id=b.job_id and
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

	echo create_drop_down( "cbouom", 130, $unit_of_measurement,'', 1, '-Uom-',$select_uom, "",1,"1,12,23,27" );
}

if($action=="process_loss_method_id"){
	$data=explode("_",$data);
	$process_loss_method=return_field_value("process_loss_method", "variable_order_tracking", "company_name=$data[0]  and variable_list=18 and item_category_id=$data[1] and status_active=1 and is_deleted=0");
	echo $process_loss_method;

}

if($action=="show_fabric_booking"){
	extract($_REQUEST);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$po_number_arr=return_library_array( "select b.id,b.po_number from wo_po_break_down b, wo_booking_dtls a where a.po_break_down_id=b.id and a.booking_no='".$data."' ", "id", "po_number"  );
	$arr=array (0=>$po_number_arr,1=>$body_part,2=>$color_type,6=>$color_library,11=>$unit_of_measurement);
	
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
	$txt_booking_no="'".$data."'";

	$is_approved=0;
	$sql=sql_select("select is_approved from wo_booking_mst where booking_no=$txt_booking_no and status_active=1 and is_deleted=0");
	foreach($sql as $row){
		if($row[csf('is_approved')]==3) $is_approved=1; else $is_approved=$row[csf('is_approved')];
	}
	if($is_approved==1){
		echo "approved**".str_replace("'","",$txt_booking_no);
		die;
	}

	$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id and b.work_order_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0");
	if($pi_number){
		echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
		die;
	}
	$yarnAllo=sql_select("select id from inv_material_allocation_mst where booking_no=$txt_booking_no and status_active=1 and is_deleted=0");
	if(count($yarnAllo)>0){
		echo "yarnallocation**".str_replace("'","",$txt_booking_no);
		disconnect($con);die;
	}

	$pplbook=0;
	$ppl=sql_select("select b.id from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and a.booking_no=$txt_booking_no and a.is_sales!=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.id");
	foreach($ppl as $pplrow){
		$pplbook=$pplrow[csf('id')];
	}

	if($pplbook!=0){
		echo "PPL**".str_replace("'","",$txt_booking_no)."**".$pplbook;
		die;
	}

	$sales_order=0;
	$sqls=sql_select("select job_no from fabric_sales_order_mst where sales_booking_no=$txt_booking_no and status_active=1 and is_deleted=0");
	foreach($sqls as $rows){
		$sales_order=$rows[csf('job_no')];
	}
	if($sales_order){
		echo "sal1**".str_replace("'","",$txt_booking_no)."**".$sales_order;
		die;
	}

	$receive_mrr=0;
	$sqlre=sql_select("select recv_number from inv_receive_master where booking_no=$txt_booking_no  and status_active=1 and is_deleted=0");
	foreach($sqlre as $rows){
		$receive_mrr=$rows[csf('recv_number')];
	}
	if($receive_mrr){
		echo "rec1**".str_replace("'","",$txt_booking_no)."**".$receive_mrr;
		die;
	}

	$issue_mrr=0;
	$sqlis=sql_select("select issue_number from inv_issue_master where booking_no=$txt_booking_no  and status_active=1 and is_deleted=0");
	foreach($sqlis as $rows){
		$issue_mrr=$rows[csf('issue_number')];
	}
	if($issue_mrr){
		echo "iss1**".str_replace("'","",$txt_booking_no)."**".$issue_mrr;
		die;
	}
	exit();
}

if($action=="delete_booking_item"){
	execute_query( "update wo_booking_dtls set status_active=0,is_deleted =1 where  booking_no ='$data'",0);
}

if($action=="show_fabric_booking_report")
{
	extract($_REQUEST);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
    $marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	//$job_no_arr=return_library_array( "SELECT id,job_no from wo_po_details_master",'id','job_no');
	$po_qnty_tot=return_field_value( "sum(po_quantity)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	$sample_name_name_arr=return_library_array( "select id,sample_name from    lib_sample",'id','sample_name');
	?>
	<div style="width:1330px" align="center">
    										<!--    Header Company Information         -->
    <?php
$nameArray_approved = sql_select("select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=13");

list($nameArray_approved_row) = $nameArray_approved;
$nameArray_approved_date = sql_select("select b.approved_date as approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=13 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "'");
list($nameArray_approved_date_row) = $nameArray_approved_date;
$nameArray_approved_comments = sql_select("select b.comments as comments from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=13 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "'");
list($nameArray_approved_comments_row) = $nameArray_approved_comments;
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
									Province No: <?php echo $result[csf('province')]; ?>
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
        $nameArray=sql_select( "SELECT a.id,a.booking_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.po_break_down_id,a.colar_excess_percent,a.cuff_excess_percent,a.fabric_composition,a.delivery_date,a.is_apply_last_update,a.pay_mode,a.fabric_source,b.job_no,b.buyer_name, b.style_ref_no ,b.gmts_item_id,b.order_uom,b.total_set_qnty,b.style_description,b.season,b.product_dept,b.product_code,b.pro_sub_dep,b.dealing_marchant from wo_booking_mst a, wo_po_details_master b where  a.job_no=b.job_no and a.booking_no=$txt_booking_no and a.company_id='$cbo_company_name' and b.company_name='$cbo_company_name'"); //2 compnay check for sample job is FAL-15-00586 in development
        foreach ($nameArray as $result)
        {
			$total_set_qnty=$result[csf('total_set_qnty')];
			$colar_excess_percent=$result[csf('colar_excess_percent')];
			$cuff_excess_percent=$result[csf('cuff_excess_percent')];
			$po_no="";$file_no="";$ref_no="";
			$shipment_date="";
			 $sql_po= "SELECT po_number,grouping,file_no,MIN(pub_shipment_date) pub_shipment_date from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].") group by po_number,grouping,file_no";
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
				$sql_lead_time= "SELECT DATEDIFF(pub_shipment_date,po_received_date) date_diff from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")";
			}

			if($db_type==2)
			{
				$sql_lead_time= "SELECT (pub_shipment_date-po_received_date) date_diff from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")";
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
                    <td width="100"><span style="font-size:18px"><b>Job No</b></span></td>
                    <td width="110">:&nbsp;<span style="font-size:18px"><b><? echo $result[csf('job_no')]; ?></b></span></td>
                    <td width="100"><span style="font-size:12px"><b>Dept.</b></span></td>
                    <td width="110">:&nbsp;<? echo $product_dept[$result[csf('product_dept')]] ; if($result[csf('product_code')] !=""){ echo " (".$result[csf('product_code')].")";} if($result[csf('pro_sub_dep')] !=0){ echo " (".$pro_sub_dept_array[$result[csf('pro_sub_dep')]].")";}?></td>
                    
                </tr>
                <tr>
                	<td width="100"><span style="font-size:12px"><b>Order Qnty</b></span></td>
                    <td width="110">:&nbsp;<? echo $po_qnty_tot." ".$unit_of_measurement[$result[csf('order_uom')]] ; ?></td>
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
                    
                </tr>
                <tr>
                	<td width="100" style="font-size:18px"><b>Style Ref.</b>   </td>
                    <td width="110" style="font-size:18px">:&nbsp;<b><? echo $result[csf('style_ref_no')];?></b></td>
                    <td  width="100" style="font-size:12px"><b>Style Des.</b></td>
                    <td  width="110" >:&nbsp;<? echo $result[csf('style_description')]; $job_no= $result[csf('job_no')];?></td>
                    <td width="100" style="font-size:12px"><b>Lead Time </b>   </td>
                    <td width="110">:&nbsp;<?  echo rtrim($lead_time,",");;?> </td>
                    
                </tr>
                <tr>
                	<td width="100" style="font-size:12px"><b>Dealing Merchant</b></td>
                    <td width="110">:&nbsp;<? echo $marchentrArr[$result[csf('dealing_marchant')]]; ?></td>
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
                    
                </tr>
                <tr>
                	<td width="100" style="font-size:18px"><b>Booking No </b>   </td>
                    <td width="150" style="font-size:18px">:&nbsp;<b><? echo $result[csf('booking_no')];?></b><? echo " (".$fabric_source[$result[csf('fabric_source')]].")"?></td>
                    <td width="100" style="font-size:12px"><b>Season</b></td>
                    <td width="110">:&nbsp;<? echo $result[csf('season')]; ?></td>
                    <td width="100" style="font-size:12px"><b>Attention</b></td>
                    <td width="110" >:&nbsp;<? echo $result[csf('attention')]; ?></td>
                    
                </tr>
                <tr>
                	<td width="100" style="font-size:12px"><b>Po Received Date</b></td>
                    <td width="110" >:&nbsp;<? echo $po_received_date; ?></td>
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
		$nameArray_fabric_description= sql_select("SELECT a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width,b.process_loss_percent ,a.id as pre_cost_fab_cost_dtls  FROM wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls b where b.booking_no =$txt_booking_no and a.id=b.pre_cost_fabric_cost_dtls_id  and b.status_active=1 and
	b.is_deleted=0  group by a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width,b.process_loss_percent,a.id order by a.body_part_id");//b.gmts_color_id,

		
			$gmts_color_id_arr=array();	$pre_cost_fab_cost_dtls_arr=array();
		foreach($nameArray_fabric_description as $rows){
			$gmts_color_id_arr[$rows[csf('gmts_color_id')]]=$rows[csf('gmts_color_id')];
			$pre_cost_fab_cost_dtls_arr[$rows[csf('pre_cost_fab_cost_dtls')]]=$rows[csf('pre_cost_fab_cost_dtls')];

		}

		// echo "<pre>";
		// print_r($pre_cost_fab_cost_dtls_arr);

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

			$color_wise_wo_sql=sql_select("select job_no, fabric_color_id, po_break_down_id, $sample_type_id as sample_type
										  FROM
										  wo_booking_dtls
										  WHERE
										  booking_no =$txt_booking_no and
										  status_active=1 and
                                          is_deleted=0
										  group by job_no, fabric_color_id, po_break_down_id");
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
                        $lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='".$color_wise_wo_result[csf('job_no')]."' and approval_status=3 and color_name_id=".$color_wise_wo_result[csf('fabric_color_id')]." and po_break_down_id=".$color_wise_wo_result[csf('po_break_down_id')]."","lapdip_no");
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
															  b.is_deleted=0 and b.po_break_down_id=".$color_wise_wo_result[csf('po_break_down_id')]."");
							
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
	}?>
	<!-- start  -->
	<div style="width:1330px; float:left">
	
	<?
	// Body Part type used only Cuff and Flat Knit
	$colar_percent_size_wise_array=return_library_array( "select a.colar_cuff_per,b.gmts_sizes from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b,wo_pre_cost_fabric_cost_dtls c  where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.pre_cost_fabric_cost_dtls_id=c.id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.color_size_table_id and a.booking_no=$txt_booking_no and a.booking_type=1 and c.body_part_type in(40,50) and a.status_active=1 and a.is_deleted=0", "gmts_sizes", "colar_cuff_per");
	
	//	echo  "select a.body_part_type,a.body_part_id,sum(d.rmg_qty) as rmg_qty,d.gmts_size,d.gmts_color_id FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls d WHERE a.job_no=d.job_no and d.booking_no =$txt_booking_no and  a.id=d.pre_cost_fabric_cost_dtls_id  and d.status_active=1 and d.is_deleted=0 and d.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and a.body_part_type in(40,50) group by a.body_part_type,a.body_part_id,d.gmts_size,d.gmts_color_id order by a.body_part_id";
	
	$nameArray_body_part=sql_select( "select a.body_part_type,a.body_part_id,sum(d.bh_qty) as rmg_qty,d.gmts_size,d.gmts_color_id FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls d WHERE a.job_no=d.job_no and d.booking_no =$txt_booking_no and  a.id=d.pre_cost_fabric_cost_dtls_id  and d.status_active=1 and d.is_deleted=0 and d.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and a.body_part_type in(40,50) group by a.body_part_type,a.body_part_id,d.gmts_size,d.gmts_color_id order by a.body_part_id");
	
	
	
	$row_count=count($nameArray_body_part);
	//if($row_count==0) echo " <p style='color:#f00; text-align:center; font-size:15px;'> Body part type is  used only Flat Knit and Cuff.</p> ";
	foreach($nameArray_body_part as $row)
	{
		$body_part_arr[$row[csf('body_part_id')]]['bpart_type']=$row[csf('body_part_type')];
		$body_part_rmg_qty_arr[$row[csf('body_part_id')]][$row[csf('gmts_size')]][$row[csf('gmts_color_id')]]['rmg_qty']+=$row[csf('rmg_qty')];
	}
	// print_r($body_part_arr);
	$tbl_row_count=count($body_part_arr);
	//echo $tbl_row_count.'Dx';
	?>
	
	
	<?
	
	$k=1;
	foreach($body_part_arr as $body_id=>$val)
	{
		$k++;
	
		$bpart_type_id=$val['bpart_type'];
		$nameArray_item_size=sql_select( "select min(c.id) as id,b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no  and a.body_part_id=$body_id  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id  and d.status_active=1 and d.is_deleted=0 and a.body_part_type in(40,50) group by b.item_size,c.size_number_id order by id");
		
	
	
	
	?>
	
	<div style="max-height:1330px; width:660px; overflow:auto; float:left; padding-top:20px; margin-left:5px; margin-bottom:5px; position: relative;">
	<table  width="100%" align="left"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
	<tr>
	<td colspan="<? echo count($nameArray_size)+4;?>" align="center"><b><? echo $body_part[$body_id];?> -  Colour Size Breakedown in Pcs</b></td>
	</tr>
	<tr>
	<td width="70">Size</td>
	
	<?
	
	/*$nameArray_item_size=sql_select( "select b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no  and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 group by c.size_number_id,b.item_size order by c.size_number_id");*/
	foreach($nameArray_item_size  as $result_size)
	{
	?>
	<td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
	<?
	}
	?>
	<td rowspan="2" align="center"><strong>Total</strong></td>
	
	</tr>
	<tr>
	<td>Collar Size</td>
	
	<?
	foreach($nameArray_item_size  as $result_item_size)
	{
	?>
	<td align="center" style="border:1px solid black"><strong><? echo $result_item_size[csf('item_size')];?></strong></td>
	<?
	}
	?>
	 <?
		$color_library=return_library_array( "select id,color_name from lib_color where status_active =1 and is_deleted=0", "id", "color_name");
		$color_wise_wo_sql=sql_select("select a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method,c.color_number_id,sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no and a.body_part_id=$body_id  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 and a.body_part_type in(40,50) group by c.color_number_id,a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method order by a.id
	");
	foreach($color_wise_wo_sql as $color_wise_wo_result)
	{
		$color_total_collar=0;
		$color_total_collar_order_qnty=0;
		$process_loss_method=$color_wise_wo_result[csf("process_loss_method")];
		$constrast_color_arr=array();
		if($color_wise_wo_result[csf("color_size_sensitive")]==3)
		{
			$constrast_color=explode('__',$color_wise_wo_result[csf("color_break_down")]);
			for($i=0;$i<count($constrast_color);$i++)
			{
				$constrast_color2=explode('_',$constrast_color[$i]);
				$constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
			}
		}
	?>
		<tr>
		<td>
		<?
		if($color_wise_wo_result[csf("color_size_sensitive")]==3)
		{
			echo strtoupper ($constrast_color_arr[$color_wise_wo_result[csf('color_number_id')]]) ;
			$lab_dip_color_id=return_field_value("contrast_color_id","wo_pre_cos_fab_co_color_dtls","job_no='".$color_wise_wo_result[csf('job_no')]."' and pre_cost_fabric_cost_dtls_id=".$color_wise_wo_result[csf('id')]." and gmts_color_id=".$color_wise_wo_result[csf('color_number_id')]."");
		}
		else
		{
			echo $color_library[$color_wise_wo_result[csf('color_number_id')]];
			$lab_dip_color_id=$color_wise_wo_result[csf('color_number_id')];
		}
		?>
	
		</td>
		<?
		foreach($nameArray_item_size  as $result_size)
		{
			?>
			<td align="center" style="border:1px solid black">
	
			<?
			$rmg_qty=$body_part_rmg_qty_arr[$body_id][$result_size[csf('size_number_id')]][$color_wise_wo_result[csf('color_number_id')]]['rmg_qty'];
			//echo $bpart_type_id.'=';
			if($bpart_type_id==50)//Cuff
			{
				$fab_rmg_qty=$rmg_qty*2;
			}
			else //Flat Knit
			{
				$fab_rmg_qty=$rmg_qty;
			}
			//$color_wise_wo_sql_qnty=sql_select("select c.color_number_id,sum(c.order_quantity) as order_quantity, sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 and c.color_number_id='".$color_wise_wo_result['color_number_id']."' and c.size_number_id='".$result_size['size_number_id']."'");
			/*$color_wise_wo_sql_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$color_wise_wo_result[csf('color_number_id')]."   and  status_active=1 and is_deleted =0");
	
			list($plan_cut_qnty)=$color_wise_wo_sql_qnty;*/
			//$plan_cut=$plan_cut_qnty[csf('plan_cut_qnty')];
			//$colar_excess_per=($plan_cut*$colar_excess_percent)/100;
			echo number_format($fab_rmg_qty,0);
			$color_total_collar+=$fab_rmg_qty;
			$color_total_collar_order_qnty+=$fab_rmg_qty;
			$grand_total_collar+=$fab_rmg_qty;
			$grand_total_collar_order_qnty+=$fab_rmg_qty;
	
			$size_tatal[$result_size[csf('size_number_id')]]+=$fab_rmg_qty;
			?>
			</td>
			<?
		}
		?>
	
		<td align="center"><? echo number_format($color_total_collar,0); ?></td>
	
		</tr>
		<?
		}
		?>
		<tr>
			<td>Size Total</td>
	
			<?
			foreach($nameArray_item_size  as $result_size)
			{
				//$colar_excess_pers=($size_tatal[$result_size[csf('size_number_id')]]*$colar_excess_percent)/100;
				$tot_size_tatal=$size_tatal[$result_size[csf('size_number_id')]];
				//$size_tatal[$result_size[csf('size_number_id')]]=0;
			?>
			<td style="border:1px solid black;  text-align:center"><?  echo number_format($size_tatal[$result_size[csf('size_number_id')]],0);$size_tatal[$result_size[csf('size_number_id')]]=0; ?></td>
			<?
			}
			?>
			<td  style="border:1px solid black;  text-align:center"><?  echo number_format($grand_total_collar,0);$grand_total_collar=0; ?></td>
	
		</tr>
	</table>
	  <br/>
	</div>
	
		
	  <!--End here-->	
	<!-- end -->
	<?
	}
	
	
	




	if(str_replace("'","",$cbo_fabric_source)==1)
	{
		$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');
		//$yarn_sql_array=sql_select("SELECT min(id) as id ,count_id, copm_one_id, percent_one,copm_two_id, percent_two, type_id, sum(cons_qnty) as yarn_required, AVG(rate) as rate from wo_pre_cost_fab_yarn_cost_dtls where job_no='$job_no' and  status_active=1 and is_deleted=0 group by count_id,copm_one_id,percent_one, copm_two_id,percent_two,type_id order by id");
		//echo "SELECT min(a.id) as id ,a.count_id, a.copm_one_id, a.percent_one,a.copm_two_id, a.percent_two, a.color, a.type_id, sum(a.cons_qnty) as yarn_required, AVG(a.rate) as rate,b.po_break_down_id from wo_pre_cost_fab_yarn_cost_dtls a, wo_booking_dtls b where a.job_no=b.job_no and a.fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.job_no='$job_no' and b.booking_no=$txt_booking_no and  a.status_active=1 and a.is_deleted=0 group by a.count_id,a.copm_one_id,a.percent_one, a.copm_two_id,a.percent_two,a.color,a.type_id,b.po_break_down_id order by po_break_down_id";

		$po_number_arr=return_library_array( "select b.id,b.po_number from wo_po_break_down b,wo_booking_dtls a where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no ", "id", "po_number"  );
		$yarn_sql_array=sql_select("SELECT a.fabric_cost_dtls_id,min(a.id) as id ,a.count_id, a.copm_one_id, a.percent_one,a.copm_two_id, a.percent_two, a.color, a.type_id, sum(a.cons_qnty) as yarn_required, AVG(a.rate) as rate,b.po_break_down_id,sum(b.grey_fab_qnty) as grey_fab_qnty, a.cons_ratio from wo_pre_cost_fab_yarn_cost_dtls a, wo_booking_dtls b where a.job_no=b.job_no and a.fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.job_no='$job_no' and b.booking_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.count_id,a.copm_one_id,a.percent_one, a.copm_two_id,a.percent_two,a.color,a.type_id,b.po_break_down_id,a.fabric_cost_dtls_id, a.cons_ratio order by po_break_down_id ");
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
                                 <td><? echo $po_number_arr[$row[csf('po_break_down_id')]]; ?></td>
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
	<?
	$txt_req_no=$dataArray[0][csf("requisition_number")];
	$color_name_arr=return_library_array( "SELECT id,color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');

	$costing_per_arr=return_library_array( "select job_no,costing_per from wo_pre_cost_mst where  job_no='$job_no'", "job_no", "costing_per");
	$condition= new condition();
	if(str_replace("'","",$job_no) !=''){
		$condition->job_no("='$job_no'");
	}
	$condition->init();
	$GmtsitemRatioArr=$condition->getGmtsitemRatioArr();
	$cost_per_qty_arr=$condition->getCostingPerArr();
	//print_r($cost_per_qty_arr);
	$fabric= new fabric($condition);
	$fabric_costing_arr=$fabric->getQtyArray_by_FabriccostidAndGmtscolor_knitAndwoven_greyAndfinish();
	$TotalGreyreq=array_sum($fabric_costing_arr['knit']['grey'][$fabric_cost_id][$cbo_color_name]);
	$fabric_color=array(); $color_type_id=0; $fab_des=''; $plan_cut_qnty=0;
	
	

	$sql_data=sql_select("SELECT a.job_no, b.id ,c.item_number_id ,c.country_id ,c.color_number_id ,c.size_number_id ,c.order_quantity ,c.plan_cut_qnty  ,d.id as pre_cost_dtls_id , 
	d.item_number_id as cbogmtsitem, d.body_part_id ,d.fab_nature_id ,d.fabric_source ,d.color_type_id, d.fabric_description,d.color_size_sensitive,d.rate, d.uom,e.cons ,e.requirment,f.contrast_color_id  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e left join wo_pre_cos_fab_co_color_dtls f on e.pre_cost_fabric_cost_dtls_id=f.pre_cost_fabric_cost_dtls_id and e.color_number_id=f.gmts_color_id  where 1=1 ".where_con_using_array($pre_cost_fab_cost_dtls_arr,1,'d.id')." ".where_con_using_array($gmts_color_id_arr,1,'c.color_number_id')."  and a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no  and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and  c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and a.job_no='$job_no' and c.size_number_id=e.gmts_sizes  and e.cons !=0   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 order by b.id,d.id");//
	

	foreach($sql_data as $row){
		$plan_cut_qnty+=$row[csf('plan_cut_qnty')];
		$fab_des=$body_part[$row[csf("body_part_id")]].', '.$color_type[$row[csf("color_type_id")]].', '.$row[csf("fabric_description")];
		$color_type_id=$row[csf("color_type_id")];
		$fabric_uom = $row[csf("uom")];
		if($row[csf('color_size_sensitive')]==1){
			$fabric_color[$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		}else{
			$fabric_color[$row[csf('color_number_id')]]=$row[csf('contrast_color_id')];
		}
		$cbogmtsitem=$row[csf('cbogmtsitem')];

		$body_part_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]][$row[csf('pre_cost_dtls_id')]]['body_part_id']=$row[csf('body_part_id')];
		$body_part_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]][$row[csf('pre_cost_dtls_id')]]['fab_color']=$row[csf('color_number_id')];
		$body_part_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]][$row[csf('pre_cost_dtls_id')]]['gmts_ratio']=$GmtsitemRatioArr[$job_no][$cbogmtsitem];
		// $body_part_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]][$row[csf('pre_cost_dtls_id')]]['fab_kg_qty']=($TotalGreyreq/$plan_cut_qnty)*$cost_per_qty_arr[$job_no]*$GmtsitemRatioArr[$job_no][$cbogmtsitem];
		// $body_part_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]][$row[csf('pre_cost_dtls_id')]]['gmts_ratio']=$GmtsitemRatioArr[$job_no][$cbogmtsitem];

	}

	$GmtsitemRatio=$GmtsitemRatioArr[$txt_job_no][$cbogmtsitem];
	$cons_txt="";
	$cons_txt=$costing_per[$costing_per_arr[$txt_job_no]];
	
	$sql_data=sql_select("select a.stripe_color,a.measurement, a.uom, a.totfidder, a.fabreq, a.fabreqtotkg, a.yarn_dyed, a.stripe_type,a.pre_cost_fabric_cost_dtls_id as pre_cost_id,b.body_part_id,
	a.color_number_id,b.item_number_id as cbogmtsitem from wo_pre_stripe_color a , wo_pre_cost_fabric_cost_dtls b where a.status_active=1 
	".where_con_using_array($pre_cost_fab_cost_dtls_arr,1,'a.pre_cost_fabric_cost_dtls_id')." ".where_con_using_array($gmts_color_id_arr,1,'a.color_number_id')."   and a.is_deleted=0");

		foreach($sql_data as $row){

			$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]][$row[csf('pre_cost_id')]][$row[csf('stripe_color')]]['measurement']=$row[csf('measurement')];
			$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]][$row[csf('pre_cost_id')]][$row[csf('stripe_color')]]['uom']=$row[csf('uom')];
			$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]][$row[csf('pre_cost_id')]][$row[csf('stripe_color')]]['fabreq']=$row[csf('fabreq')];
			$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]][$row[csf('pre_cost_id')]][$row[csf('stripe_color')]]['yarn_dyed']=$row[csf('yarn_dyed')];
		}


	if(count($sql_data)>0) $stripeType=$sql_data[0][csf('stripe_type')]; else $stripeType=0;
	//echo $tot_stripe_measurement;


	if(count($sql_data)>0)
	{
		?>
		<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
			<tr>
				<td width="60%">
				<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
					<tr>
						<td colspan="9" align="center"><b>Stripe Details</b></td>
					</tr>

					<tr align="center">
						<th width="30"> SL</th>
					
						<th width="100"> Body Part</th>
						<th width="80"> Fabric Color</th>
						<th width="70"> Fabric Qty(KG)</th>
						<th width="70"> Stripe Color</th>
						<th width="70"> Stripe Measurement</th>
						<th width="70"> Stripe Uom</th>
						<th  width="70"> Qty.(KG)</th>
						<th  width="70"> Y/D Req.</th>
					</tr>

					<?
					$i=1;$total_fab_qty=0;
					$total_fabreqtotkg=0;
					$fab_data_array=array();
					$stripe_wise_fabkg_arr=array();
					$color_from_library=return_field_value("color_from_library", "variable_order_tracking", "company_name=$cbo_company_name  and variable_list=23  and status_active=1 and is_deleted=0");
					//	if($db_type==0) $color_cond="d.fabric_color_id='".$color_id."'";
					//else if($db_type==2) $color_cond="nvl(d.fabric_color_id,0)=nvl('".$color_id."',0)";
					$stripe_wise_fabkg_sql=sql_select("select b.color_id as color_id,a.sample_prod_qty,c.body_part_id,c.color_type_id from sample_development_dtls a,sample_development_rf_color b,sample_development_fabric_acc c where a.sample_mst_id=b.mst_id and   b.dtls_id=c.id  and a.sample_mst_id=c.sample_mst_id and a.sample_color=b.color_id and   a.sample_mst_id in($mst_style_id) and a.sample_prod_qty>0	and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

					// echo "<pre>";
					// print_r($stripe_arr);
					foreach($stripe_wise_fabkg_sql as $vals)
					{
						$stripe_wise_fabkg_arr[$vals[csf("body_part_id")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]] +=$vals[csf("sample_prod_qty")];
					}
					foreach($body_part_arr as $body_id=> $color_data)
					{
						foreach($color_data as $color_id=>$pre_cost_data)
						{
						foreach($pre_cost_data as $pre_cost_id=>$color_val){

							$s=1;
								$count=count($stripe_arr[$body_id][$color_id][$pre_cost_id]);
							foreach($stripe_arr[$body_id][$color_id][$pre_cost_id] as $stripe_color_id=>$stripe_data)
							{
							?>
							<tr>
								<?
							
								
								if($s==1){
								?>
								<td rowspan="<?=$count;?>"> <? echo $i; ?></td>						
								<td rowspan="<?=$count;?>"> <? echo $body_part[$body_id]; ?></td>
								<td rowspan="<?=$count;?>"> <? echo $color_name_arr[$color_id]; ?></td>
								<td align="right" rowspan="<?=$count;?>"> <? echo number_format($color_qty,2); ?></td>
								<?
								$s++;
								}
								$tot_stripe_measurement=$tot_stripe_measurement_arr[$color_id];
								$total_fab_qty+=$color_qty;
								//foreach($color_val['stripe_color'] as $strip_color_id=>$s_color_val)
								//{
									$measurement=$stripe_data['measurement'];
									$uom=$stripe_data['uom'];
									$fabreqtotkg=($measurement/$tot_stripe_measurement)*$color_qty;//$color_val['fabreqtotkg'][$strip_color_id];
									$yarn_dyed=$stripe_data['yarn_dyed'];
									?>
									<td><?  echo  $color_name_arr[$stripe_color_id]; ?></td>
									<td align="right"> <? echo  number_format($measurement,2); ?></td>
									<td> <? echo  $unit_of_measurement[$uom]; ?></td>
									<td align="right"> <? echo  number_format($stripe_data['fabreq'],4); ?></td>
									<td> <? echo  $yes_no[$yarn_dyed]; ?></td>
							</tr>
									<?
									$total_fabreqtotkg+=$fabreqtotkg;
									$i++;

							}}
								
						}}
					?>
					<tfoot>
						<tr>
							<td colspan="3">Total </td>
							<td align="right">  <? echo  number_format($total_fab_qty,2); ?> </td>
							<td></td>
							<td></td>
							<td>   </td>
							<td align="right"><? echo  number_format($total_fabreqtotkg,2); ?> </td>
						</tr>
					</tfoot>
				</table>
			 </td>
		 <td width="40%">
		<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
			<tr>
	             <td colspan="3" align="center"><b>Stripe Summery</b></td>
	        </tr>

	        <tr align="center">
	        	<th width="30"> SL</th>	        
	            <th width="70"> Stripe Color</th>	          
	            <th  width="70"> Qty.(KG)</th>
	           
	        </tr>

	        <?
			$i=1;$total_fab_qty=0;
			$total_fabreqtotkg=0;
			$fab_data_array=array();
			$stripe_wise_fabkg_arr=array();
			$color_from_library=return_field_value("color_from_library", "variable_order_tracking", "company_name=$cbo_company_name  and variable_list=23  and status_active=1 and is_deleted=0");
			//	if($db_type==0) $color_cond="d.fabric_color_id='".$color_id."'";
			//else if($db_type==2) $color_cond="nvl(d.fabric_color_id,0)=nvl('".$color_id."',0)";
			$stripe_wise_fabkg_sql=sql_select("select b.color_id as color_id,a.sample_prod_qty,c.body_part_id,c.color_type_id from sample_development_dtls a,sample_development_rf_color b,sample_development_fabric_acc c where a.sample_mst_id=b.mst_id and   b.dtls_id=c.id  and a.sample_mst_id=c.sample_mst_id and a.sample_color=b.color_id and   a.sample_mst_id in($mst_style_id) and a.sample_prod_qty>0	and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

			// echo "<pre>";
			// print_r($stripe_arr);
			$si=1;
			foreach($stripe_wise_fabkg_sql as $vals)
			{
				$stripe_wise_fabkg_arr[$vals[csf("body_part_id")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]] +=$vals[csf("sample_prod_qty")];
			}
	        foreach($body_part_arr as $body_id=> $color_data)
	        {
				foreach($color_data as $color_id=>$pre_cost_data)
				{
				foreach($pre_cost_data as $pre_cost_id=>$color_val){
					foreach($stripe_arr[$body_id][$color_id][$pre_cost_id] as $stripe_color_id=>$stripe_data)
					{
					?>
					<tr>
						<?
					
						$tot_stripe_measurement=$tot_stripe_measurement_arr[$color_id];
						$total_fab_qty+=$color_qty;
						//foreach($color_val['stripe_color'] as $strip_color_id=>$s_color_val)
						//{
							$measurement=$stripe_data['measurement'];
							$uom=$stripe_data['uom'];
							$fabreqtotkg=($measurement/$tot_stripe_measurement)*$color_qty;//$color_val['fabreqtotkg'][$strip_color_id];
							$yarn_dyed=$stripe_data['yarn_dyed'];
							?>
							<td ><?=$si;?> </td>
							<td><?  echo  $color_name_arr[$stripe_color_id]; ?></td>
						
							<td align="right"> <? echo  number_format($stripe_data['fabreq'],4); ?></td>
							
					</tr>
							<?
							$total_fabreqtotkg+=$fabreqtotkg;
							$si++;

					}}
						
				}}
			?>
	        <tfoot>
	        	<tr>
	        		<td >Total </td>
	        		<td align="right">  <? echo  number_format($total_fab_qty,2); ?> </td>        	
	        		
	        		<td align="right"><? echo  number_format($total_fabreqtotkg,2); ?> </td>
	        	</tr>
	        </tfoot>
		</table>
		</td>
		</tr>
		</table>
		<?
	}
	//$bookingId=$nameArray[0][csf('id')]
	?>

        <br/>


         <?
	$lib_designation_arr=return_library_array(" select id,custom_designation from lib_designation","id","custom_designation");
	$user_lib_designation_arr=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
	$user_lib_name_arr=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");

	$mst_id=return_field_value("id as mst_id","wo_booking_mst","booking_no=$txt_booking_no","mst_id");
	//echo $mst_id.'ssD';
	//and b.un_approved_date is null
	 $approve_data_array=sql_select("select b.approved_by,min(b.approved_date) as approved_date from   approval_history b where b.mst_id=$mst_id and b.entry_form=13  group by  b.approved_by order by b.approved_by asc");
	 //echo "select b.approved_by,min(b.approved_date) as approved_date from   approval_history b where b.mst_id=$mst_id and b.entry_form=13  group by  b.approved_by order by b.approved_by asc";
	 $unapprove_data_array=sql_select("select b.approved_by,b.approved_date,b.un_approved_reason,b.un_approved_date,b.approved_no from   approval_history b where b.mst_id=$mst_id and b.entry_form=13  order by b.approved_date,b.approved_by");

	?>
    <td width="49%" valign="top">
	<?
          if(count($approve_data_array)>0)
			{
 		?>
       <table  width="850" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
            <tr style="border:1px solid black;">
                <th colspan="5" style="border:1px solid black;">Approval Status</th>
                </tr>
                <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th>
				<th width="40%" style="border:1px solid black;">Name</th>
				<th width="30%" style="border:1px solid black;">Designation</th>
				<th width="27%" style="border:1px solid black;">Approval Date</th>

                </tr>
            </thead>
            <tbody>
            <?
			$i=1;
			foreach($approve_data_array as $row){


			?>
            <tr style="border:1px solid black;">
                <td width="3%" style="border:1px solid black;"><? echo $i;?></td>
				<td width="40%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]];?></td>
				<td width="30%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]];?></td>
				<td width="27%" style="border:1px solid black;text-align:center"><? echo date ("d-m-Y h:i:s",strtotime($row[csf('approved_date')]));?></td>

                </tr>
                <?
				$i++;
			}
				?>
            </tbody>
        </table>
		<?
		}
		?>
		<br>
		<?
		if(count($unapprove_data_array)>0)
		{
			//and approval_type=2
			$sql_unapproved=sql_select("select booking_id,approval_cause from fabric_booking_approval_cause where  entry_form=13 and is_deleted=0 and status_active=1 and booking_id=$mst_id");
			//echo "select booking_id,approval_cause from fabric_booking_approval_cause where  entry_form=13 and approval_type=2 and is_deleted=0 and status_active=1 and booking_id=$mst_id";
			$unapproved_request_arr=array();
			foreach($sql_unapproved as $rowu)
			{
			$unapproved_request_arr[$rowu[csf('booking_id')]]=$rowu[csf('approval_cause')];
			}
 		?>
       <table  width="850" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
            <tr style="border:1px solid black;">
                <th colspan="6" style="border:1px solid black;">Approval/Un Approval History</th>
                </tr>
                <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th>
				<th width="30%" style="border:1px solid black;">Name</th>
				<th width="20%" style="border:1px solid black;">Designation</th>
				<th width="5%" style="border:1px solid black;">Approval Status</th>
				<th width="20%" style="border:1px solid black;">Reason For Un Approval</th>
				<th width="22%" style="border:1px solid black;"> Date</th>

                </tr>
            </thead>
            <tbody>
            <?
			$i=1;
			foreach($unapprove_data_array as $row){

			?>
            <tr style="border:1px solid black;">
                <td width="3%" style="border:1px solid black;"><? echo $i;?></td>
				<td width="30%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]];?></td>
				<td width="20%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]];?></td>
				<td width="5%" style="border:1px solid black; text-align:center"><? echo 'Yes';?></td>
				<td width="20%" style="border:1px solid black;"><? echo '';?></td>
				<td width="22%" style="border:1px solid black;text-align:center"><? if($row[csf('approved_date')]!="") echo date ("d-m-Y h:i:s",strtotime($row[csf('approved_date')])); else echo "";?></td>
             </tr>
				<?
				$i++;
				$un_approved_date= explode(" ",$row[csf('un_approved_date')]);
				$un_approved_date=$un_approved_date[0];
				if($db_type==0) //Mysql
				{
					if($un_approved_date=="" || $un_approved_date=="0000-00-00") $un_approved_date="";else $un_approved_date=$un_approved_date;
				}
				else
				{
					if($un_approved_date=="") $un_approved_date="";else $un_approved_date=$un_approved_date;
				}

				if($un_approved_date!="")
				{
				?>
			<tr style="border:1px solid black;">
                <td width="3%" style="border:1px solid black;"><? echo $i;?></td>
				<td width="30%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]];?></td>
				<td width="20%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]];?></td>
				<td width="5%" style="border:1px solid black;text-align:center;"><? echo 'No';?></td>
				<td width="20%" style="border:1px solid black;text-align:center"><? echo $unapproved_request_arr[$mst_id];?></td>
				<td width="22%" style="border:1px solid black;text-align:center"><? if($row[csf('un_approved_date')]!="") echo date ("d-m-Y h:i:s",strtotime($row[csf('un_approved_date')])); else echo "";?></td>
              </tr>

                <?
				$i++;
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
				 $sizearr=return_library_array("select id,size_name from lib_size where status_active =1 and is_deleted=0","id","size_name");
				 $colorarr=return_library_array("select id,color_name from  lib_color where status_active =1 and is_deleted=0","id","color_name");
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
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
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
$nameArray_approved = sql_select("select max(b.approved_no) as approved_no, a.is_approved from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=13 group by a.is_approved");
list($nameArray_approved_row) = $nameArray_approved;
$nameArray_approved_date = sql_select("select b.approved_date as approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=13 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "'");
list($nameArray_approved_date_row) = $nameArray_approved_date;
$nameArray_approved_comments = sql_select("select b.comments as comments from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=13 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "'");
list($nameArray_approved_comments_row) = $nameArray_approved_comments;
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
									Province No: <?php echo $result[csf('province')]; ?>
									Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
									Email Address: <? echo $result[csf('email')];?>
									Website No: <? echo $result[csf('website')];
								}
                            ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:20px">
                            	<strong><? echo $report_title;?> &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:#F00"><? if($nameArray_approved_row[csf('is_approved')]==1){ echo "(Approved)";} else if($nameArray_approved_row[csf('is_approved')]==3){ echo "(Partial Approved)";}else{echo "";}; ?> </font></strong><!--//ISD-22-05701 by kausar-->
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

			$color_wise_wo_sql=sql_select("select job_no, fabric_color_id, po_break_down_id, $sample_type_id as sample_type
										  FROM
										  wo_booking_dtls
										  WHERE
										  booking_no =$txt_booking_no and
										  status_active=1 and
                                          is_deleted=0
										  group by job_no, fabric_color_id, po_break_down_id");
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
                        $lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='".$color_wise_wo_result[csf('job_no')]."' and approval_status=3 and color_name_id=".$color_wise_wo_result[csf('fabric_color_id')]." and po_break_down_id=".$color_wise_wo_result[csf('po_break_down_id')]."","lapdip_no");
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
$nameArray_approved = sql_select("select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=13");
list($nameArray_approved_row) = $nameArray_approved;
$nameArray_approved_date = sql_select("select b.approved_date as approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=13 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "'");
list($nameArray_approved_date_row) = $nameArray_approved_date;
$nameArray_approved_comments = sql_select("select b.comments as comments from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=13 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "'");
list($nameArray_approved_comments_row) = $nameArray_approved_comments;

$sql_book = sql_select("select sample_type from wo_booking_dtls where booking_no=$txt_booking_no");
$sample_name_booking = '';
foreach ($sql_book as $row) {
	if ($sample_name_booking == '') {
		$sample_name_booking = $sample_name_name_arr[$row[csf('sample_type')]];
	} else {
		$sample_name_booking .= "," . $sample_name_name_arr[$row[csf('sample_type')]];
	}

}
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
									 <?php echo $result[csf('province')] . '&nbsp;'; ?>
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
		// echo "select a.season, b.buyer_req_no,a.bh_merchant,a.style_ref_no,a.product_code,a.product_dept  from  sample_development_mst a, sample_development_dtls b  where  a.id=b.sample_mst_id";
				$bh_merchant_arr=array();
				foreach ($nameseason as $season_row)
				{
					// $season=$season_row[csf('season')];
					// $buyer_req_no=$season_row[csf('buyer_req_no')];
					// $bh_merchant_arr[$season_row[csf('style_ref_no')]]['style']=$season_row[csf('bh_merchant')];
					// $style_ref_no=$season_row[csf('style_ref_no')];
					// $product_code=$season_row[csf('product_code')];
					// $product_department=$product_dept[$season_row[csf('product_dept')]];

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
				<tr>
                    <td width="110" style="font-size:18px"><b>Sample Name</b></td>
                    <td width="300" colspan="5"> :&nbsp;<b><? echo implode(", ",array_unique(explode(",",$sample_name_booking))); ?></b></td>

                </tr>

                <?php if ($result[csf('tagged_booking_no')] != '') {?>
                <tr>
                    <td colspan="5">
                        <h3>This fabric will be Dyed along with Main Fabric Booking No:&nbsp;<? echo $result[csf('tagged_booking_no')];?></h3>
                    </td>
                </tr>
                <?php }?>

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
            	<td rowspan="8" width="50"><p>Total  Finish Fabric <? if(trim($cbo_fabric_natu ,"'")==2){echo "(KG)";}else{echo "(Yds)";} ?></p></td>
                <td rowspan="8" width="50"><p>P. Loss %</p></td>
                <td  rowspan="8" width="50"><p>Total Grey Fabric <? if(trim($cbo_fabric_natu ,"'")==2){echo "(KG)";}else{echo "(Yds)";} ?>)</p></td>

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
       <td colspan="14">&nbsp; </td>
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
 $sizearr=return_library_array("select id,size_name from lib_size where status_active =1 and is_deleted=0","id","size_name");
 $colorarr=return_library_array("select id,color_name from  lib_color where status_active =1 and is_deleted=0","id","color_name");
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
 $sizearr=return_library_array("select id,size_name from lib_size where status_active =1 and is_deleted=0","id","size_name");
 $colorarr=return_library_array("select id,color_name from  lib_color where status_active =1 and is_deleted=0","id","color_name");
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
            <tr align="center"><th colspan="2" align="left">Body Part</th>
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
            <tr align="center"><th colspan="2" align="left">Color Type</th>
				<?
                foreach($nameArray_fabric_description  as $result_fabric_description)
                {
					if( $result_fabric_description[csf('color_type_id')] == "")  echo "<td  colspan='3'>&nbsp</td>";
					else echo "<td  colspan='3'>". $color_type[$result_fabric_description[csf('color_type_id')]]."</td>";

                }
                ?>
            </tr>
            <tr align="center"><th colspan="2" align="left">Construction</th>
				<?
                foreach($nameArray_fabric_description  as $result_fabric_description)
                {
					if( $result_fabric_description[csf('construction')] == "")  echo "<td  colspan='3'>&nbsp</td>";
					else echo "<td  colspan='3'>". $result_fabric_description[csf('construction')]."</td>";
                }
                ?>
            </tr>
            <tr align="center"><th   colspan="2" align="left">Composition</th>
				<?
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
					if( $result_fabric_description[csf('composition')] == "")   echo "<td colspan='3' >&nbsp</td>";
					else echo "<td colspan='3' >".$result_fabric_description[csf('composition')]."</td>";
                }
                ?>
            </tr>
            <tr align="center"><th  colspan="2" align="left">GSM</th>
				<?
                foreach($nameArray_fabric_description  as $result_fabric_description)
                {
					if( $result_fabric_description[csf('gsm_weight')] == "")   echo "<td colspan='3'>&nbsp</td>";
					else echo "<td colspan='3' align='center'>". $result_fabric_description[csf('gsm_weight')]."</td>";
                }
                ?>
            </tr>
            <tr align="center"><th   colspan="2" align="left">Dia/Width</th>
				<?
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
					if( $result_fabric_description[csf('dia_width')] == "")   echo "<td colspan='3'>&nbsp</td>";
					else echo "<td colspan='3' align='center'>". $result_fabric_description[csf('dia_width')]."</td>";
                }
                ?>
            </tr>
            <tr align="center"><th  colspan="2" align="left">Process Loss%</th>
				<?
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
					if( $result_fabric_description[csf('process_loss_percent')] == "")   echo "<td colspan='3'>&nbsp</td>";
					else echo "<td align='center' colspan='3'>". $result_fabric_description[csf('process_loss_percent')]."</td>";
                }
                ?>
            </tr>
            <tr>
              <!--  <th  width="100" align="left">Sample Name</th>-->
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
                   <!-- <td  width="100" align="left">
						<?
                        /*$sample_type_val="";
                        $ex_sample_type=array_unique(explode(",",$color_wise_wo_result[csf('sample_type')]));
                        foreach($ex_sample_type as $sm_val)
                        {
                        	if($sample_type_val=="") $sample_type_val=$sample_name_name_arr[$sm_val]; else $sample_type_val.=','.$sample_name_name_arr[$sm_val];
                        }
                        echo $sample_type_val; */
                    ?></td>-->
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
                <!--<td width="100" align="left">&nbsp;</td>-->
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
			$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'SM', date("Y",time()), 5, "select id, booking_no_prefix, booking_no_prefix_num from wo_booking_mst where company_id=$cbo_company_name and booking_type=4 and YEAR(insert_date)=".date('Y',time())." order by id desc ", "booking_no_prefix", "booking_no_prefix_num" ));
		}
		else if($db_type==2)
		{
			$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'SM', date("Y",time()), 5, "select id, booking_no_prefix, booking_no_prefix_num from wo_booking_mst where company_id=$cbo_company_name and booking_type=4 and to_char(insert_date,'YYYY')=".date('Y',time())." order by id desc ", "booking_no_prefix", "booking_no_prefix_num" ));
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
				echo "0**".$new_booking_no[0]."**".str_replace("'","",$id);
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$new_booking_no[0]."**".str_replace("'","",$id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID){
				oci_commit($con);
				echo "0**".$new_booking_no[0]."**".str_replace("'","",$id);
			}
			else{
				oci_rollback($con);
				echo "10**".$new_booking_no[0]."**".str_replace("'","",$id);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)
	{
		$con = connect();

		$is_approved=0;
		$sql=sql_select("select is_approved from wo_booking_mst where booking_no=$txt_booking_no  and status_active=1 and is_deleted=0");
		foreach($sql as $row){
			if($row[csf('is_approved')]==3){
				$is_approved=1;
			}else{
				$is_approved=$row[csf('is_approved')];
			}
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
		$yarnAllo=sql_select("select id from inv_material_allocation_mst where booking_no=$txt_booking_no and status_active=1 and is_deleted=0");
		if(count($yarnAllo)>0){
			echo "yarnallocation**".str_replace("'","",$txt_booking_no);
			disconnect($con);die;
		}

		$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no  and a.status_active=1 and  a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		if($pi_number){
			echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
			disconnect($con);die;
		}

		$receive_mrr=0;
		$sqlre=sql_select("select recv_number from inv_receive_master where booking_no=$txt_booking_no  and status_active=1 and is_deleted=0");
		foreach($sqlre as $rows){
			$receive_mrr=$rows[csf('recv_number')];
		}
		if($receive_mrr){
			echo "rec1**".str_replace("'","",$txt_booking_no)."**".$receive_mrr;
			disconnect($con);die;
		}

		$issue_mrr=0;
		$sqlis=sql_select("select issue_number from inv_issue_master where booking_no=$txt_booking_no and status_active=1 and is_deleted=0");
		foreach($sqlis as $rows){
			$issue_mrr=$rows[csf('issue_number')];
		}
		if($issue_mrr){
			echo "iss1**".str_replace("'","",$txt_booking_no)."**".$issue_mrr;
			disconnect($con);die;
		}

		if($db_type==0){
			mysql_query("BEGIN");
		}
		$field_array="buyer_id*job_no*po_break_down_id*item_category*fabric_source*currency_id*exchange_rate*pay_mode*source*booking_date*delivery_date*booking_month*booking_year*supplier_id*attention*ready_to_approved*fabric_composition*updated_by*update_date*tagged_booking_no";
		$data_array ="".$cbo_buyer_name."*".$txt_job_no."*".$txt_order_no_id."*".$cbo_fabric_natu."*".$cbo_fabric_source."*".$cbo_currency."*".$txt_exchange_rate."*".$cbo_pay_mode."*".$cbo_source."*".$txt_booking_date."*".$txt_delivery_date."*".$cbo_booking_month."*".$cbo_booking_year."*".$cbo_supplier_name."*".$txt_attention."*".$cbo_ready_to_approved."*".$txt_fabriccomposition."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"."*".$txt_fabric_booking_no."";
		$rID=sql_update("wo_booking_mst",$field_array,$data_array,"id","".$update_id."",0);

		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "1**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$update_id);
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$update_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "1**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$update_id);
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$update_id);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)
	{
		$con = connect();

		$is_approved=0;
		$sql=sql_select("select is_approved from wo_booking_mst where booking_no=$txt_booking_no  and status_active=1 and is_deleted=0");
		foreach($sql as $row){
			if($row[csf('is_approved')]==3){
				$is_approved=1;
			}else{
				$is_approved=$row[csf('is_approved')];
			}
		}
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			disconnect($con);die;
		}
		
		$yarnAllo=sql_select("select id from inv_material_allocation_mst where booking_no=$txt_booking_no and status_active=1 and is_deleted=0");
		if(count($yarnAllo)>0){
			echo "yarnallocation**".str_replace("'","",$txt_booking_no);
			disconnect($con);die;
		}

		$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id and b.work_order_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0");
		if($pi_number){
			echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
			disconnect($con);die;
		}

		$pplbook=0;
		$ppl=sql_select("select b.id from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and a.booking_no=$txt_booking_no and a.is_sales!=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.id");
		foreach($ppl as $pplrow){
			$pplbook=$pplrow[csf('id')];
		}

		if($pplbook!=0){
			echo "PPL**".str_replace("'","",$txt_booking_no)."**".$pplbook;
			disconnect($con);die;
		}

		$sales_order=0;
		$sqls=sql_select("select job_no from fabric_sales_order_mst where sales_booking_no=$txt_booking_no and status_active=1 and is_deleted=0");
		foreach($sqls as $rows){
			$sales_order=$rows[csf('job_no')];
		}
		if($sales_order){
			//echo "sal1**".str_replace("'","",$txt_booking_no)."**".$sales_order;
			//disconnect($con);die;
		}

		$receive_mrr=0;
		$sqlre=sql_select("select recv_number from inv_receive_master where booking_no=$txt_booking_no and status_active=1 and is_deleted=0");
		foreach($sqlre as $rows){
			$receive_mrr=$rows[csf('recv_number')];
		}
		if($receive_mrr){
			echo "rec1**".str_replace("'","",$txt_booking_no)."**".$receive_mrr;
			disconnect($con);die;
		}

		$issue_mrr=0;
		$sqlis=sql_select("select issue_number from inv_issue_master where booking_no=$txt_booking_no and status_active=1 and is_deleted=0");
		foreach($sqlis as $rows){
			$issue_mrr=$rows[csf('issue_number')];
		}
		if($issue_mrr){
			echo "iss1**".str_replace("'","",$txt_booking_no)."**".$issue_mrr;
			disconnect($con);die;
		}

		if($db_type==0){
			mysql_query("BEGIN");
		}

		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'2'*'1'";
		$rID=sql_delete("wo_booking_mst",$field_array,$data_array,"id","".$update_id."",1);
		$rID=execute_query( "update wo_booking_dtls set status_active=0,is_deleted =1 where  booking_no =$txt_booking_no and  and status_active=1 and is_deleted=0",0);
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
		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "2**".str_replace("'","",$txt_booking_no);
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
	
	$sqljob=sql_select("select id from wo_po_details_master where job_no=$txt_job_no and status_active=1 and is_deleted=0");
	$jobid=$sqljob[0][csf('id')];
	$jobidArr[$jobid]=$jobid;

	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con);die;}
		 $id=return_next_id( "id", "wo_booking_dtls", 1 ) ;

		 $pre_cost_remarks=return_field_value("remarks","wo_pre_cos_fab_co_avg_con_dtls","pre_cost_fabric_cost_dtls_id=$cbo_fabricdescription_id and po_break_down_id=$cbo_order_id and color_number_id =$cbo_garmentscolor_id and gmts_sizes=$cbo_garmentssize_id");

		 $field_array="id, job_no,booking_mst_id, po_break_down_id, pre_cost_fabric_cost_dtls_id, sample_type, booking_no, booking_type, is_short, fabric_color_id, gmts_color_id, item_size, gmts_size, dia_width, fin_fab_qnty, process_loss_percent, grey_fab_qnty, rate, amount, bh_qty, rf_qty, pre_cost_remarks, uom,remark,additional_process,inserted_by,insert_date";
			$data_array="(".$id.",".$txt_job_no.",".$update_id.",".$cbo_order_id.",".$cbo_fabricdescription_id.",".$cbo_sample_type.",".$txt_booking_no.",4,2,".$cbo_fabriccolor_id.",".$cbo_garmentscolor_id.",".$cbo_itemsize_id.",".$cbo_garmentssize_id.",".$txt_dia_width.",".$txt_finish_qnty.",".$txt_process_loss.",".$txt_grey_qnty.",".$txt_rate.",".$txt_amount.",".$txt_bh_qty.",".$txt_rf_qty.",'".$pre_cost_remarks."',".$cbouom.",".$txt_remarks.",".$txt_additional_process_loss.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			//$id=$id+1;

		 $rID=sql_insert("wo_booking_dtls",$field_array,$data_array,1);

		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID){
				mysql_query("COMMIT");
				echo "0**".str_replace("'","",$txt_booking_no)."**".$id;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_booking_no)."**".$id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "0**".str_replace("'","",$txt_booking_no)."**".$id;
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_booking_no)."**".$id;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)  // update Here
	{
		$con = connect();
		$is_approved=0;
		$sql=sql_select("select is_approved from wo_booking_mst where booking_no=$txt_booking_no");
		foreach($sql as $row){
			if($row[csf('is_approved')]==3){
				$is_approved=1;
			}else{
				$is_approved=$row[csf('is_approved')];
			}
		}
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			disconnect($con);die;
		}
		$yarnAllo=sql_select("select id from inv_material_allocation_mst where booking_no=$txt_booking_no and status_active=1 and is_deleted=0");
		if(count($yarnAllo)>0){
			echo "yarnallocation**".str_replace("'","",$txt_booking_no);
			disconnect($con);die;
		}

		$receive_mrr=0;
		$sqlre=sql_select("select recv_number from inv_receive_master where booking_no=$txt_booking_no  and status_active=1 and is_deleted=0");
		foreach($sqlre as $rows){
			$receive_mrr=$rows[csf('recv_number')];
		}
		if($receive_mrr){
			echo "rec1**".str_replace("'","",$txt_booking_no)."**".$receive_mrr;
			disconnect($con);die;
		}

		$issue_mrr=0;
		$sqlis=sql_select("select issue_number from inv_issue_master where booking_no=$txt_booking_no and status_active=1 and is_deleted=0");
		foreach($sqlis as $rows){
			$issue_mrr=$rows[csf('issue_number')];
		}
		if($issue_mrr){
			echo "iss1**".str_replace("'","",$txt_booking_no)."**".$issue_mrr;
			disconnect($con);die;
		}
		
		$olddtlsidArr=array(); $preDataArr=array();
		if(str_replace("'","",$txt_booking_no)!=''){
			$sql_po= sql_select("select b.id as dtlsid, b.po_break_down_id, b.pre_cost_fabric_cost_dtls_id, b.fabric_color_id, c.color_type_id, c.construction, c.composition, c.gsm_weight, b.dia_width from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.booking_type=4 and a.is_short=2 and b.booking_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0");
			foreach($sql_po as $pdata){
				$olddtlsidArr[$pdata[csf('dtlsid')]]=$pdata[csf('dtlsid')];
				$strold=$pdata[csf('construction')].$pdata[csf('fabric_color_id')].$pdata[csf('composition')].$pdata[csf('color_type_id')].$pdata[csf('gsm_weight')].$pdata[csf('dia_width')];
				$preDataArr[$pdata[csf('pre_cost_fabric_cost_dtls_id')]][$pdata[csf('po_break_down_id')]][$strold]=$strold;
			}
		}

		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
	    if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1"; disconnect($con);die;}
	    $field_array_up="job_no*po_break_down_id*pre_cost_fabric_cost_dtls_id*sample_type*booking_no*booking_type*is_short*fabric_color_id*gmts_color_id*item_size*gmts_size*dia_width*fin_fab_qnty*process_loss_percent*grey_fab_qnty*rate*amount*bh_qty*rf_qty*pre_cost_remarks*uom*remark*additional_process*updated_by*update_date";
		$pre_cost_remarks=return_field_value("remarks","wo_pre_cos_fab_co_avg_con_dtls","pre_cost_fabric_cost_dtls_id=$cbo_fabricdescription_id and po_break_down_id=$cbo_order_id and color_number_id =$cbo_garmentscolor_id and gmts_sizes=$cbo_garmentssize_id");

	    $data_array_up ="".$txt_job_no."*".$cbo_order_id."*".$cbo_fabricdescription_id."*".$cbo_sample_type."*".$txt_booking_no."*4*2*".$cbo_fabriccolor_id."*".$cbo_garmentscolor_id."*".$cbo_itemsize_id."*".$cbo_garmentssize_id."*".$txt_dia_width."*".$txt_finish_qnty."*".$txt_process_loss."*".$txt_grey_qnty."*".$txt_rate."*".$txt_amount."*".$txt_bh_qty."*".$txt_rf_qty."*'".$pre_cost_remarks."'*".$cbouom."*".$txt_remarks."*".$txt_additional_process_loss."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
	    $rID=sql_update("wo_booking_dtls",$field_array_up,$data_array_up,"id","".$update_id_details."",0);
		
		fnc_isdyeingplan("WO_PO_DETAILS_MASTER", $jobidArr);
		fnc_isdyeingplan("WO_BOOKING_DTLS", str_replace("'",'',$update_id_details) );

		 //echo "10**".str_replace("'","",$txt_booking_no).'**'."delete from wo_booking_accessories_dtls where wo_booking_dtls_id=".$update_id_details." and booking_no =".$txt_booking_no."".','.$rID; die;

		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID){
				mysql_query("COMMIT");
				echo "1**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$update_id);
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$update_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID){
				oci_commit($con);
				echo "1**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$update_id);
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$update_id);
			}
		}
		disconnect($con); 
		die;
	}
	else if ($operation==2)  // Insert Here
	{
		$con = connect();
		$is_approved=0;
		$sql=sql_select("select company_id,is_approved from wo_booking_mst where booking_no=$txt_booking_no");
		foreach($sql as $row){
			if($row[csf('is_approved')]==3){
				$is_approved=1;
			}else{
				$is_approved=$row[csf('is_approved')];
			}
			$company_id=$row[csf('company_id')];
		}
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			disconnect($con);die;
		}
		
		$textile_vari_sales=return_field_value( "production_entry", "variable_settings_production","COMPANY_NAME=$company_id and variable_list=66 and  status_active=1 and  is_deleted=0 ");
		if($textile_vari_sales=="") $textile_vari_sales=0;

	//	echo "10**=A".$textile_vari_sales;die; 
		if($textile_vari_sales!=2)
		{							//issue ID-ISD-23-25224, for NZ
			$yarnAllo=sql_select("select id from inv_material_allocation_mst where booking_no=$txt_booking_no and status_active=1 and is_deleted=0");
			if(count($yarnAllo)>0){
				echo "yarnallocation**".str_replace("'","",$txt_booking_no);
				disconnect($con);die;
			}
			$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id and b.work_order_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0");
			if($pi_number){
				echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
				disconnect($con);die;
			}
			$pplbook=0;
			$ppl=sql_select("select b.id from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and a.booking_no=$txt_booking_no and a.is_sales!=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.id");
			foreach($ppl as $pplrow){
				$pplbook=$pplrow[csf('id')];
			}

			if($pplbook!=0){
				echo "PPL**".str_replace("'","",$txt_booking_no)."**".$pplbook;
				disconnect($con);die;
			}

			$sales_order=0;
			$sqls=sql_select("select job_no from fabric_sales_order_mst where sales_booking_no=$txt_booking_no  and status_active=1 and is_deleted=0");
			foreach($sqls as $rows){
				$sales_order=$rows[csf('job_no')];
			}
			if($sales_order){
				echo "sal1**".str_replace("'","",$txt_booking_no)."**".$sales_order;
				disconnect($con);die;
			}

			$receive_mrr=0;
			$sqlre=sql_select("select recv_number from inv_receive_master where booking_no=$txt_booking_no and status_active=1 and is_deleted=0");
			foreach($sqlre as $rows){
				$receive_mrr=$rows[csf('recv_number')];
			}
			if($receive_mrr){
				echo "rec1**".str_replace("'","",$txt_booking_no)."**".$receive_mrr;
				disconnect($con);die;
			}

			$issue_mrr=0;
			$sqlis=sql_select("select issue_number from inv_issue_master where booking_no=$txt_booking_no and status_active=1 and is_deleted=0");
			foreach($sqlis as $rows){
				$issue_mrr=$rows[csf('issue_number')];
			}
			if($issue_mrr){
				echo "iss1**".str_replace("'","",$txt_booking_no)."**".$issue_mrr;
				disconnect($con);die;
			}
		}
		
		

		
		$rID=execute_query( "update wo_booking_dtls set status_active=0,is_deleted =1 where  id =$update_id_details and status_active=1 and is_deleted=0",0);

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
		else if($db_type==2 || $db_type==1 )
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
        <table width="1040" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
           <thead>
                <th colspan="9">
                  <?
                   echo create_drop_down( "cbo_search_category", 130, $string_search_type,'', 1, "-- Search Catagory --" );
                  ?>
                </th>
             </thead>
            <thead>
                <th width="150" class="must_entry_caption">Company Name</th>
                <th width="150">Buyer Name</th>
                <th width="100">Booking No</th>
                <th width="70">File No</th>
                <th width="70">Ref. No</th>
                <th width="100">Job No</th>
                <th width="130" colspan="2">Date Range</th>
                <th>&nbsp;</th>
            </thead>
            <tr class="general">
                <td> <input type="hidden" id="selected_booking">
                    <?
                        echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", $company, "load_drop_down( 'size_color_breakdown_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
                    ?>
                </td>
            <td id="buyer_td">
             <?
                echo create_drop_down( "cbo_buyer_name", 172, $blank_array,"", 1, "-- Select Buyer --" );
            ?>	</td>
             <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:100px"></td>
             <td><input name="txt_file_no" id="txt_file_no" class="text_boxes_numeric" style="width:70px"></td>
             <td><input name="txt_ref_no" id="txt_ref_no" class="text_boxes_numeric" style="width:70px"></td>
            <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:100px"></td>
            <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px"></td>
             <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"></td>
             <td align="center">
                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_ref_no').value, 'create_booking_search_list_view', 'search_div', 'sample_booking_controller','setFilterGrid(\'list_view\',-1)')" style="width:80px;" /></td>
        </tr>
        <tr>
            <td colspan="9" align="center" valign="middle">
             
            <? echo load_month_buttons(1);  ?>
            </td>
        </tr>
     </table>
     <div id="search_div"></div>
    </form>
   </div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}
 
if ($action=="create_booking_search_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company="  company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
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
	 $approved=array(0=>"No",1=>"Yes",3=>"Yes");
	 $is_ready=array(0=>"No",1=>"Yes",2=>"No");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');

	$sql= "select a.booking_no_prefix_num, b.job_no_prefix_num, b.job_no, a.booking_no, a.booking_date, a.company_id, buyer_id, a.po_break_down_id, a.item_category, a.pay_mode, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved from wo_booking_mst a, wo_po_details_master b where $company $buyer $booking_date $job_cond  $all_job_cond $booking_cond ".set_user_lavel_filtering(' and buyer_id','buyer_id')." and a.job_no=b.job_no and a.entry_form is null and a.booking_type=4 and a.is_short=2 and  a.status_active=1  and a.is_deleted=0 order by a.id DESC";
	?>
    <table class="rpt_table" width="1160" cellpadding="0" cellspacing="0" border="1" rules="all">
        <thead>
            <th width="30">SL</th>
            <th width="60">Booking No</th>
            <th width="60">Booking Date</th>
            <th width="100">Buyer</th>
            <th width="100">Job No.</th>
            <th width="120">PO No.</th>
            <th width="90">File No</th>
            <th width="100">Ref. No</th>
            <th width="80">Fabric Nature</th>
            <th width="80">Fabric Source</th>
            <th width="80">Pay Mode</th>
            <th width="80">Supplier</th>
            <th width="50">Approved</th>
            <th>Is-Ready</th>
        </thead>
    </table>
    <div style="max-height:300px; overflow-y:scroll; width:1160px" >
        <table width="1140" class="rpt_table" id="list_view" border="1" rules="all">
            <tbody>
                <?
                $i=1;
                $sql_data=sql_select($sql);
                foreach($sql_data as $row)
                {
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
                        <td width="30"><? echo $i;?></td>
                        <td width="60"><? echo $row[csf('booking_no_prefix_num')];?></td>
                        <td width="60"><? echo date("d-m-Y",strtotime($row[csf('booking_date')]));?></td>
                        <td width="100" style="word-break:break-all"><? echo $buyer_arr[$row[csf('buyer_id')]];?></td>
                        <td width="100" style="word-break:break-all"><? echo $row[csf('job_no')];?></td>
                        <td width="120" style="word-break:break-all"><p><? echo $po_numbers;?></p></td>
                        <td width="90" style="word-break:break-all"><p><? echo $file_nos;?></p></td>
                        <td width="100" style="word-break:break-all"><p><? echo $ref_nos;?></p></td>
                        <td width="80"><? echo $item_category[$row[csf('item_category')]];?></td>
                        <td width="80"><? echo $fabric_source[$row[csf('fabric_source')]];?></td>
                        <td width="80"><? echo $pay_mode[$row[csf('pay_mode')]];?></td>
                        <td width="80" style="word-break:break-all">
                            <?
                            if($row[csf('pay_mode')]==3 || $row[csf('pay_mode')]==5) echo $comp[$row[csf('supplier_id')]]; else echo $suplier[$row[csf('supplier_id')]];
                            ?>
                        </td>
                        <td width="50"><? echo $approved[$row[csf('is_approved')]];?></td>
                        <td><? echo $is_ready[$row[csf('ready_to_approved')]];?></td>
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
                                    <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_internal_ref').value, 'create_po_search_list_view', 'search_div', 'sample_booking_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100%;" />
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
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and a.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.buyer_name='$data[1]'"; else $buyer=""; //{ echo "Please Select Buyer First."; die; }

	$job_cond=""; $order_cond=""; $style_cond="";
	if($data[7]==1)
	{
		if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num='$data[4]'"; //else  $job_cond="";
		if (str_replace("'","",$data[5])!="") $order_cond=" and b.po_number = '$data[5]'  "; //else  $order_cond="";
		if (trim($data[6])!="") $style_cond=" and a.style_ref_no ='$data[6]'"; //else  $style_cond="";
	}
	else if($data[7]==2)
	{
		if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num like '$data[4]%'"; //else  $job_cond="";
		if (str_replace("'","",$data[5])!="") $order_cond=" and b.po_number like '$data[5]%'  "; //else  $order_cond="";
		if (trim($data[6])!="") $style_cond=" and a.style_ref_no like '$data[6]%'  "; //else  $style_cond="";
	}
	else if($data[7]==3)
	{
		if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num like '%$data[4]'"; //else  $job_cond="";
		if (str_replace("'","",$data[5])!="") $order_cond=" and b.po_number like '%$data[5]'  "; //else  $order_cond="";
		if (trim($data[6])!="") $style_cond=" and a.style_ref_no like '%$data[6]'"; //else  $style_cond="";
	}
	else if($data[7]==4 || $data[7]==0)
	{
		if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num like '%$data[4]%'"; //else  $job_cond="";
		if (str_replace("'","",$data[5])!="") $order_cond=" and b.po_number like '%$data[5]%'  "; //else  $order_cond="";
		if (trim($data[6])!="") $style_cond=" and a.style_ref_no like '%$data[6]%'"; //else  $style_cond="";
	}

	$internal_ref = str_replace("'","",$data[8]);
	$file_no = str_replace("'","",$data[9]);
	$booking_no = str_replace("'","",$data[10]);
	$existingpoid = str_replace("'","",$data[11]);
	$job_no = str_replace("'","",$data[12]);
	//echo $job_no.'DSSA';
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no='".trim($job_no)."' ";
	if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='".trim($file_no)."' ";
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping like '%".trim($internal_ref)."%' ";

	if($db_type==0)
	{
		if ($data[2]!="" &&  $data[3]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}
	else if($db_type==2)
	{
		if ($data[2]!="" &&  $data[3]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}

	/*$sql=sql_select("select approval_need,allow_partial from approval_setup_mst a,approval_setup_dtls b where a.id=b.mst_id and a.company_id=$data[0] and b.page_id=25 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.setup_date");
	$app_nessity=2;

	// foreach($sql as $row){
	// 	$app_nessity=$row[csf('approval_need')];
	// }
	// if($app_nessity==1) $appCond=" and c.approved=1"; else $appCond="";
	
	if($sql[0][csf("approval_need")]==1 && $sql[0][csf("allow_partial")]==1){
		$app_nessity="and c.approved in (1,2,3)";
	}else{
		$app_nessity="and c.approved in (1)";
	}
*/



	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$arr=array (1=>$comp,2=>$buyer_arr);
	$sql= "select a.job_no_prefix_num,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity,b.id, b.po_number,b.grouping,b.file_no,b.po_quantity,b.shipment_date,a.job_no from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c where a.job_no=b.job_no_mst and a.job_no=c.job_no".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." and a.status_active=1 and b.status_active=1 and b.shiping_status not in(3) $job_no_cond  $appCond $shipment_date $company $buyer $job_cond $order_cond $style_cond $file_no_cond $internal_ref_cond order by a.job_no";
	//echo $sql;
	?>
    <table width="940" class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" >
        <thead>
            <tr>
                <th width="30">Sl</th>
                <th width="60">Job No</th>
                <th width="60">Company</th>
                <th width="50">Buyer</th>
                <th width="100">Style Ref.</th>
                <th width="100">Internal Ref.</th>
                <th width="100">File No</th>
                <th width="70">Job Qty</th>
                <th width="150">PO number</th>
                <th width="80">Po Qty</th>
                <th width="">Shipment Date</th>
            </tr>
        </thead>
    </table>
    <div style="max-height:280px; overflow-y:scroll; width:940px">
        <table width="920" class="rpt_table" id="list_view" border="1" rules="all">
        <?
		$sc_lc_variable=2;
		$sc_lc_variable=return_field_value("excut_source", "variable_order_tracking", "company_name=$data[0]  and variable_list=73 and status_active=1 and is_deleted=0");
			//echo $sc_lc_variable;

		$tna_integrated=return_field_value("tna_integrated", "variable_order_tracking", "company_name=$data[0]  and variable_list=14 and status_active=1 and is_deleted=0");
		if($sc_lc_variable==1)
		{
			$sql_sc=sql_select("select b.id,d.wo_po_break_down_id from wo_po_details_master a, wo_po_break_down b, com_sales_contract_order_info d where a.job_no=b.job_no_mst  and b.id= d.wo_po_break_down_id and a.status_active=1 and b.status_active=1 $appCond $shipment_date $company $buyer $job_cond $order_cond $style_cond $file_no_cond $internal_ref_cond order by a.job_no");
			$sc_Arr=array();
			foreach($sql_sc as $row){
				$sc_Arr[$row[csf('wo_po_break_down_id')]]=$row[csf('wo_po_break_down_id')];
			}
			unset($sql_sc);
			
			$sql_lc=sql_select("select b.id,d.wo_po_break_down_id from wo_po_details_master a, wo_po_break_down b , com_export_lc_order_info d where a.job_no=b.job_no_mst  and b.id= d.wo_po_break_down_id and a.status_active=1 and b.status_active=1 $appCond $shipment_date $company $buyer $job_cond $order_cond $style_cond $file_no_cond $internal_ref_cond order by a.job_no");
			$lc_Arr=array();
			foreach($sql_lc as $row){
				$lc_Arr[$row[csf('wo_po_break_down_id')]]=$row[csf('wo_po_break_down_id')];
			}
			unset($sql_lc);
		}
		
		$sqlt=sql_select("select b.id,a.job_no,d.task_start_date, d.task_finish_date from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c , tna_process_mst d where a.job_no=b.job_no_mst and a.job_no=c.job_no and a.job_no=d.job_no and b.id= d.po_number_id and d.task_number = 31 and a.status_active=1 and b.status_active=1 $appCond $shipment_date $company $buyer $job_cond $order_cond $style_cond $file_no_cond $internal_ref_cond order by a.job_no");
		$tnaArr=array();
		foreach($sqlt as $row){
			$tnaArr[$row[csf('id')]]=$row[csf('task_finish_date')];
		}
		unset($sqlt);
		
		$sqltBooking=sql_select("select b.id from  wo_po_details_master a,wo_po_break_down b, wo_booking_dtls c  where a.job_no=b.job_no_mst and a.job_no=c.job_no  and b.id= c.po_break_down_id and b.status_active=1 and c.booking_type=1  and c.status_active=1 $shipment_date $company $buyer $job_cond $order_cond $style_cond $file_no_cond $internal_ref_cond order by a.job_no");
		//echo "select b.id from  wo_po_details_master a,wo_po_break_down b, wo_booking_dtls c  where a.job_no=b.job_no_mst and a.job_no=c.job_no  and b.id= c.po_break_down_id and b.status_active=1  $shipment_date $company $buyer $job_cond $order_cond $style_cond $file_no_cond $internal_ref_cond order by a.job_no";
		$BookingArr=array();
		foreach($sqltBooking as $row){
			$BookingArr[$row[csf('id')]]=$row[csf('id')];
		}
		unset($sqltBooking);
		
	//	echo $booking_no.'SD';
		$yarnAlloArr=array();
		if($booking_no!="")
		{
			$yarnAllo=sql_select("select po_break_down_id from inv_material_allocation_dtls where booking_no='$booking_no' and status_active=1 and is_deleted=0");
			//echo "select po_break_down_id from inv_material_allocation_dtls where booking_no='$booking_no' and status_active=1 and is_deleted=0";
			foreach($yarnAllo as $yrow){
				$yarnAlloArr[$yrow[csf('po_break_down_id')]]=$yrow[csf('po_break_down_id')];
			}
			unset($yarnAllo);
		}

		$sql= "select a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.id, b.po_number, b.grouping, b.file_no, b.po_quantity, b.shipment_date, a.job_no from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c where a.job_no=b.job_no_mst and a.job_no=c.job_no".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." and b.shiping_status not in(3) and a.status_active=1 and b.status_active=1 $appCond $shipment_date $company $buyer $job_cond $order_cond $style_cond $file_no_cond $internal_ref_cond $job_no_cond  order by a.id DESC";
		//echo $sql;
		$i=1;
		$sqlD=sql_select($sql); $oldpoid=explode(",",$existingpoid); $selectedporow="";
		foreach($sqlD as $row)
		{
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$selectable=1;
			$tnad=strtotime($tnaArr[$row[csf('id')]]);
			$bod=strtotime(date("d-m-Y"));
			
			if($tnad < $bod && $tna_integrated==1){
				$bgcolor="#F00"; $selectable=0;
			}else{
				$bgcolor=$bgcolor; $selectable=1;
			}
			$sc_lc_check=0;
			if($sc_lc_variable==1)//Fabric Booking Control With SC/LC --Yes
			{
				//$sc_po=$sc_po=0;
				$sc_po=$sc_Arr[$row[csf('id')]];
				$lc_po=$lc_Arr[$row[csf('id')]];
				//echo $sc_po.'='.$lc_po.'<br>';
				
				if($sc_po || $lc_po) {
					$sc_lc_check=0; $msg="";
				}
				else { 
					$sc_lc_check=1; $msg="SC/LC is not found against your selected PO,Please ensure it then try again";
				}
			}
			
			/*else
			{
				$bgcolor=""; $isYarnAllo=0;
			}*/
			
			if($yarnAlloArr[$row[csf("id")]]!=""){
				$isYarnAllo=1; 
				$onloaddata="";$bgcolor="yellow"; 
				//echo "B";
				$font_color="#00CC66";
				$msg="Allocation Found";
			}
			else {
				$isYarnAllo=0;
				$font_color='';
			}
			
			/*if($BookingArr[$row[csf('id')]]!="")
			{
				$bgcolor="yellow"; $onloaddata="";
				//$isYarnAllo=0;
				//echo "A";
			}
			else {
				//$isYarnAllo=0;
				$font_color='';
			}*/
			
			//print_r($oldpoid);
			
			if(in_array($row[csf("id")],$oldpoid)) 
			{ 
				if($selectedporow=="") $selectedporow=$i; else $selectedporow.=",".$i;
			}
			
			?>
             <tr class="font_color"  style="color:<? echo $font_color;?>" bgcolor="<?=$bgcolor; ?>" style="cursor:pointer;" id="tr_<?=$i; ?>" onClick="js_set_value('<?=$row[csf("id")]."_".$row[csf("po_number")]."_".$row[csf("job_no")] ?>',this.id,<?=$selectable; ?>,<?=$sc_lc_check; ?>,<?=$isYarnAllo; ?>);">
                <td width="30"><?=$i; ?>
                	<input type="hidden" name="txtindividualid_<?=$i; ?>" id="txtindividualid_<?=$i; ?>" value="<?=$row[csf("id")]."_".$row[csf("po_number")]."_".$row[csf("job_no")]."_".$selectable."_".$sc_lc_check."_".$isYarnAllo; ?>"/>
                </td>
                <td width="60" style="word-break:break-all"><?=$row[csf("job_no_prefix_num")]; ?></td>
                <td width="60" style="word-break:break-all"><?=$comp[$row[csf("company_name")]]; ?></td>
                <td width="50" style="word-break:break-all"><?=$buyer_arr[$row[csf("buyer_name")]]; ?></td>
                <td width="100" style="word-break:break-all"><?=$row[csf("style_ref_no")]; ?></td>
                <td width="100" style="word-break:break-all"><?=$row[csf("grouping")]; ?></td>
                <td width="100" style="word-break:break-all"><?=$row[csf("file_no")]; ?></td>
                <td width="70" align="right"><?=$row[csf("job_quantity")]; ?></td>
                <td width="150" style="word-break:break-all" title="<?=$msg; ?>"><p><?=$row[csf("po_number")]; ?> </p></td>
                <td width="80" align="right"><?=$row[csf("po_quantity")]; ?></td>
                <td style="word-break:break-all"><?=change_date_format($row[csf("shipment_date")],"dd-mm-yyyy",'-'); ?></td>
            </tr>
            <?
			$i++;
		}
		?>
        <input type="hidden" name="hidd_oldRow_no" id="hidd_oldRow_no" value="<?=$selectedporow; ?>"/>
        </table>
    </div>
    <?
	exit();
}

if($action=="create_po_search_list_view_not")
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

		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con);die;}
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
	 $sql= "select booking_no,id,booking_date,company_id,buyer_id,job_no,po_break_down_id,item_category,fabric_source,currency_id,exchange_rate,pay_mode,booking_month,supplier_id,attention,delivery_date,source,booking_year,is_approved,fabric_composition,ready_to_approved,tagged_booking_no from wo_booking_mst  where booking_no='$data'";

	 $data_array=sql_select($sql);
	 foreach ($data_array as $row)
	 {
		echo "load_drop_down( 'requires/sample_booking_controller', '".$row[csf("pay_mode")]."', 'load_drop_down_suplier', 'sup_td' );\n";
        echo "document.getElementById('txt_fabric_booking_no').value = '".$row[csf("tagged_booking_no")]."';\n";
		echo "document.getElementById('txt_order_no_id').value = '".$row[csf("po_break_down_id")]."';\n";
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('update_id').value = '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_booking_no').value = '".$row[csf("booking_no")]."';\n";
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
		if($row[csf("is_approved")]==3){
			$is_approved=1;
		}else{
			$is_approved=$row[csf("is_approved")];
		}
		echo "document.getElementById('id_approved_id').value = '".$is_approved."';\n";
		echo "document.getElementById('cbo_ready_to_approved').value = '".$row[csf("ready_to_approved")]."';\n";
		echo "document.getElementById('txt_fabriccomposition').value = '".$row[csf("fabric_composition")]."';\n";
		if($row[csf("is_approved")]==1)
		{
			//echo "document.getElementById('app_sms').innerHTML = 'This booking is approved';\n";
			echo "document.getElementById('app_sms2').innerHTML = 'This booking is approved';\n";
		}
		else if($row[csf("is_approved")]==3)//ISD-22-05701 by kausar
		{
			//echo "document.getElementById('app_sms').innerHTML = 'This booking is approved';\n";
			echo "document.getElementById('app_sms2').innerHTML = 'This booking is partial approved';\n";
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
	$data_array=sql_select("select id, pre_cost_fabric_cost_dtls_id, po_break_down_id, sample_type, fabric_color_id, gmts_color_id, item_size, gmts_size, dia_width, fin_fab_qnty, process_loss_percent, grey_fab_qnty, rate, amount, bh_qty, rf_qty, uom,remark,additional_process FROM wo_booking_dtls WHERE id ='".$data."' and is_short=2 and status_active=1 and is_deleted=0");
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
		echo "document.getElementById('txt_remarks').value = '".$row[csf("remark")]."';\n";
		echo "document.getElementById('txt_additional_process_loss').value = '".$row[csf("additional_process")]."';\n";

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
            <table width="1300" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                <tr>
                    <td align="center" width="100%">
                        <table  cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                            <thead>
                                <tr>
                                    <th colspan="10">
                                    <?
                                    // echo create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --" );
                                    ?>
                                    <input type="hidden" id="cbo_search_category">
                                    </th>
                                </tr>
                                <tr>
                                    <th width="150">Company Name</th>
                                    <th width="150">Buyer Name</th>
                                    <th width="100">Booking No</th>
                                    <th width="100">Job No</th>
                                    <th width="100">File No</th>
                                    <th width="100">Internal Ref.</th>
                                    <th width="100">Style Ref </th>
                                    <th width="100">Order No</th>
                                    <th width="200">Date Range</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tr>
                                <td>
                                <input type="hidden" id="selected_fabric_booking_no">
                                <?
                                echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", '', "load_drop_down( 'size_color_breakdown_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
                                ?>
                                </td>
                                <td id="buyer_td">
                                <? echo create_drop_down( "cbo_buyer_name", 172, $blank_array,"", 1, "-- Select Buyer --" );?>
                                </td>
                                <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:100px"></td>
                                <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:100px"></td>
                                <td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:90px"></td>
                                <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:90px"></td>
                                <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:100px"></td>
                                <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:100px"></td>
                                <td>
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
                                </td>
                                <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_order_search').value, 'create_fabric_booking_search_list_view', 'search_div', 'sample_booking_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td  align="center" height="40" valign="middle">
                    <?
                    echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );
                    echo load_month_buttons();
					?>
                    </td>
                </tr>
                <tr>
                    <td align="center"valign="top" id="search_div"></td>
                </tr>
            </table>
        </form>
        </div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
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

	$approved=array(0=>"No",1=>"Yes",3=>"Yes");
	$is_ready=array(0=>"No",1=>"Yes",2=>"No");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');

	$sql= "select a.booking_no, a.booking_no_prefix_num, a.booking_date, a.company_id, a.buyer_id, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved, b.job_no_prefix_num, b.style_ref_no, b.gmts_item_id, c.id as po_break_down_id, c.po_number, c.file_no, c.grouping as int_ref_no
		from wo_booking_mst a, wo_booking_dtls p, wo_po_details_master b ,wo_po_break_down c
		where a.job_no=p.job_no and a.booking_no=p.booking_no and p.po_break_down_id=c.id and b.job_no=c.job_no_mst and a.booking_type=1 and a.is_short=2 and  a.status_active=1  and 	a.is_deleted=0  ". set_user_lavel_filtering(' and a.buyer_id','buyer_id')."  $company $buyer $job_cond $booking_date $booking_cond  $file_no_cond  $internal_ref_cond $style_cond $order_cond";

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
		if($row[csf("is_approved")]){
			$is_approved=1;
		}else{
			$is_approved=$row[csf("is_approved")];
		}
		$booking_data[$row[csf("booking_no")]]["is_approved"]=$is_approved;
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
    <table width="1350" class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" align="left">
        <thead>
            <tr>
                <th width="30">Sl</th>
                <th width="50">Booking No</th>
                <th width="70">Booking Date</th>
                <th width="70">Company</th>
                <th width="70">Buyer</th>
                <th width="50">Job No.</th>
                <th width="100">Style Ref.</th>
                <th width="150">Gmts Item</th>
                <th width="200">PO number</th>
                <th width="100">Internal Ref</th>
                <th width="100">File No</th>
                <th width="80">Fabric Nature</th>
                <th width="80">Fabric Source</th>
                <th width="80">Supplier</th>
                <th width="50">Approved</th>
                <th>Is-Ready</th>
            </tr>
        </thead>
    </table>
    <div style=" max-height:300px; overflow-y:scroll; width:1370px"  align="left">
    <table width="1350" class="rpt_table" id="list_view" border="1" rules="all">
        <tbody>
        <?
		$i=1;
		foreach($booking_data as $row)
		{
			if ($i%2==0)
			$bgcolor="#E9F3FF";
			else
			$bgcolor="#FFFFFF";
			?>
        	<tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer;" onClick="js_set_fabric_value('<? echo $row[("booking_no")]; ?>')">
            	<td width="30" align="center"><? echo $i; ?></td>
                <td width="50" align="center"><p><? echo $row[("booking_no_prefix_num")]; ?>&nbsp;</p></td>
                <td width="70" align="center"><? if($row[("booking_date")]!="" && $row[("booking_date")]!="0000-00-00")  echo change_date_format($row[("booking_date")]);  ?></td>
                <td width="70"><p><? echo $comp[$row[("company_id")]]; ?>&nbsp;</p></td>
                <td width="70"><p><? echo $buyer_arr[$row[("buyer_id")]]; ?>&nbsp;</p></td>
                <td width="50" align="center"><p><? echo $row[("job_no_prefix_num")]; ?>&nbsp;</p></td>
                <td width="100"><p><? echo $row[("style_ref_no")]; ?>&nbsp;</p></td>
                <td width="150"><p>
				<?
				$garments_item_arr=explode(",",$row[("gmts_item_id")]);
				$all_garments_item="";
				foreach($garments_item_arr as $item_id)
				{
					$all_garments_item.=$garments_item[$item_id].",";
				}
				echo chop($all_garments_item,",");
				?>&nbsp;</p></td>
                <td width="200"><p><?  echo implode(",",array_unique(explode(",",chop($row[("po_number")],","))));  ?>&nbsp;</p></td>
                <td width="100"><p><?  echo implode(",",array_unique(explode(",",chop($row[("int_ref_no")],","))));  ?>&nbsp;</p></td>
                <td width="100"><p><?  echo implode(",",array_unique(explode(",",chop($row[("file_no")],","))));  ?>&nbsp;</p></td>
                <td width="80"><p><? echo $item_category[$row[("item_category")]]; ?>&nbsp;</p></td>
                <td width="80"><p><? echo $fabric_source[$row[("fabric_source")]]; ?>&nbsp;</p></td>
                <td width="80"><p><? echo $suplier[$row[("supplier_id")]]; ?>&nbsp;</p></td>
                <td width="50"><p><? echo $approved[$row[("is_approved")]]; ?>&nbsp;</p></td>
                <td><p><? echo $is_ready[$row[("ready_to_approved")]]; ?>&nbsp;</p></td>
            </tr>
            <?
			$i++;
		}
		?>
        </tbody>
    </table>
    </div>
    <?
}
if ($action=="open_color_list_view")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	</head>
	<body>
	<div align="center" style="width:100%;">
	<div style="display:none"><?=load_freeze_divs ("../../../",$permission); ?></div>
    <div  style="font-size:18px; color:#33F">Stripe Color Details</div>
	 <?
		$costing_per_arr=return_library_array( "select job_no,costing_per from wo_pre_cost_mst where  job_no='$txt_job_no'", "job_no", "costing_per");
		$condition= new condition();
		if(str_replace("'","",$txt_job_no) !=''){
			$condition->job_no("='$txt_job_no'");
		}
		$condition->init();
		$GmtsitemRatioArr=$condition->getGmtsitemRatioArr();
		$cost_per_qty_arr=$condition->getCostingPerArr();
		//print_r($cost_per_qty_arr);
		$fabric= new fabric($condition);
		$fabric_costing_arr=$fabric->getQtyArray_by_FabriccostidAndGmtscolor_knitAndwoven_greyAndfinish();
		$TotalGreyreq=array_sum($fabric_costing_arr['knit']['grey'][$fabric_cost_id][$cbo_color_name]);
		$fabric_color=array(); $color_type_id=0; $fab_des=''; $plan_cut_qnty=0;
		
		$sql_data=sql_select("SELECT a.job_no, b.id ,c.item_number_id ,c.country_id ,c.color_number_id ,c.size_number_id ,c.order_quantity ,c.plan_cut_qnty  ,d.id as pre_cost_dtls_id , d.item_number_id as cbogmtsitem, d.body_part_id ,d.fab_nature_id ,d.fabric_source ,d.color_type_id, d.fabric_description,d.color_size_sensitive,d.rate, d.uom,e.cons ,e.requirment,f.contrast_color_id  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e left join wo_pre_cos_fab_co_color_dtls f on e.pre_cost_fabric_cost_dtls_id=f.pre_cost_fabric_cost_dtls_id and e.color_number_id=f.gmts_color_id  where 1=1 and d.id=$fabric_cost_id and c.color_number_id=$cbo_color_name and a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no  and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and  c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes  and e.cons !=0   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 order by b.id,d.id");//
		foreach($sql_data as $row){
			$plan_cut_qnty+=$row[csf('plan_cut_qnty')];
			$fab_des=$body_part[$row[csf("body_part_id")]].', '.$color_type[$row[csf("color_type_id")]].', '.$row[csf("fabric_description")];
			$color_type_id=$row[csf("color_type_id")];
			$fabric_uom = $row[csf("uom")];
			if($row[csf('color_size_sensitive')]==1){
				$fabric_color[$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			}else{
				$fabric_color[$row[csf('color_number_id')]]=$row[csf('contrast_color_id')];
			}
			$cbogmtsitem=$row[csf('cbogmtsitem')];
		}
		$GmtsitemRatio=$GmtsitemRatioArr[$txt_job_no][$cbogmtsitem];
		$cons_txt="";
		$cons_txt=$costing_per[$costing_per_arr[$txt_job_no]];
		
		$sql_data=sql_select("select stripe_color, measurement, uom, totfidder, fabreq, fabreqtotkg, yarn_dyed, stripe_type from wo_pre_stripe_color where pre_cost_fabric_cost_dtls_id=$fabric_cost_id and color_number_id=$cbo_color_name and status_active=1 and is_deleted=0");
		if(count($sql_data)>0) $stripeType=$sql_data[0][csf('stripe_type')]; else $stripeType=0;
		
		//echo $color_type_id;
		//echo $plan_cut_qnty.'--'.$cost_per_qty_arr[$txt_job_no].'--'.$GmtsitemRatio; die;
	?>
    <table width="460" cellspacing="0" class="rpt_table" border="1" rules="all">
        <tr>
            <td width="80" bgcolor="#CCFFFF"><strong>Cons/<?=$cons_txt; ?></strong></td>
            <td width="80" align="right" bgcolor="#CCFFFF">
                <input type="hidden" id="TotalGreyreq" value="<?=$TotalGreyreq;?> "/>
                <input type="hidden" id="consdzn" value="<?=number_format(($TotalGreyreq/$plan_cut_qnty)*$cost_per_qty_arr[$txt_job_no]*$GmtsitemRatio,4);?> "/>
                <? echo number_format(($TotalGreyreq/$plan_cut_qnty)*$cost_per_qty_arr[$txt_job_no]*$GmtsitemRatio,4); ?> <strong> <?= $unit_of_measurement[$fabric_uom] ?></strong>
            </td>
            <td width="80" align="center" bgcolor="#CC99FF"><strong>Body Color</strong></td>
            <td style="word-break:break-all" bgcolor="#CC99FF"><?=$color_library[$cbo_color_name]; ?></td>
        </tr>
        <tr bgcolor="#CCFF99">
            <td><strong>Fabric Desc.</strong></td>
            <td colspan="3" style="word-break:break-all"><?=$fab_des; ?></td>
        </tr>
        <tr  bgcolor="#CCCCCC">
            <td><strong>Stripe Type</strong></td>
            <td colspan="2"><?= $stripeType; ?></td>
            <td>&nbsp;</td>
        </tr>
    </table>
	 <br/>
	 <table width="680" cellspacing="0" class="rpt_table" border="0" id="tbl_set_details" rules="all">
		<thead>
			<tr>
				<th width="150">Stripe Color</th>
				<th width="80">Measurement</th>
				<th width="60">UOM</th>
				<th width="80">Total Feeder</th>
				<th width="70">Fab Req. Qty (<?= $unit_of_measurement[$fabric_uom] ?>)</th>
				<th width="70">Yarn Dyed</th>
			</tr>
		</thead>
		<tbody>
		<?
		$color_from_library=return_field_value("color_from_library", "variable_order_tracking", "company_name=$cbo_company_name  and variable_list=23  and status_active=1 and is_deleted=0");
		if($color_from_library==1)
		{
		   $readonly="readonly='readonly'";
		   $plachoder="placeholder='Click'";
		   $onClick="onClick='color_select_popup($cbo_buyer_name,this.id)'";
		}
		else
		{
		   $readonly=""; $plachoder=""; $onClick="";
		}
		$save_update=1;
		if(count($sql_data)>0)
		{
			$i=1;
			$totmeasurement=0; $totfidder=0; $fabreq=0;
			foreach($sql_data as $row)
			{
				$totmeasurement+=$row[csf('measurement')]; $totfidder+=$row[csf('totfidder')]; $fabreq+=$row[csf('fabreq')];
				?>
				<tr>
                    <th><input type="text" id="stcolor_<? echo $i; ?>" name="stcolor_<? echo $i; ?>" style="width:140px" class="text_boxes" value="<? echo $color_library[$row[csf('stripe_color')]]; ?>" <? echo $onClick." ".$readonly." ".$plachoder; ?> disabled/></th>
                    <th><input type="text" id="measurement_<?=$i; ?>" name="measurement_<?=$i; ?>" style="width:70px" class="text_boxes_numeric" value="<?=$row[csf('measurement')]; ?>" onBlur="fnc_measurementcopy(<?=$i; ?>,this.value);" onChange="calculate_fidder(<?=$i; ?>);" disabled/></th>
                    <th><? echo create_drop_down( "cboorderuom_".$i,60, $unit_of_measurement, "",1, "-Select-", $row[csf('uom')],"",1,"25,26,29,79" ); ?></th>
                    <th><input type="text" id="totfidder_<? echo $i; ?>" name="totfidder_<? echo $i; ?>" style="width:70px" class="text_boxes_numeric" value="<? echo $row[csf('totfidder')]; ?>" onChange="calculate_fidder(<? echo $i;?>);" disabled/></th>
                    <th>
                        <input type="text" id="fabreq_<? echo $i; ?>" name="fabreq_<? echo $i; ?>" style="width:70px" class="text_boxes_numeric" value="<? echo $row[csf('fabreq')]; ?>" readonly/>
                        <input type="hidden" id="fabreqtotkg_<? echo $i; ?>" name="fabreqtotkg_<? echo $i; ?>" style="width:70px" class="text_boxes_numeric" value="<? echo $row[csf('fabreqtotkg')]; ?>" readonly/>
                    </th>
                    <th><? echo create_drop_down( "yarndyed_".$i,60, $yes_no, "",0, "", $row[csf('yarn_dyed')],"",1,"" ); ?></th>
				</tr>
				<?
				$i++;
			}
		}
	   ?>
        </tbody>
        <tfoot>
            <tr>
                <th style=" width:150px">&nbsp;</th>
                <th><input type="text" id="tottalmeasurement" name="tottalmeasurement" style="width:70px" class="text_boxes_numeric" value="<? echo number_format($totmeasurement,4); ?>" readonly/> </th>
                <th style="width:80px"></th>
                <th><input type="text" id="totaltotfidder" name="totaltotfidder" style="width:80px" class="text_boxes_numeric" value="<? echo number_format($totfidder,4); ?>" readonly/> </th>
                <th><input type="text" id="totalfabreq" name="totalfabreq" style="width:70px" class="text_boxes_numeric" value="<? echo number_format($fabreq,4); ?>" readonly/></th>
                <th style=" width:70px">&nbsp;</th>
            </tr>
        </tfoot>
	</table>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="show_fabric_booking_report_print_booking_3")
{
	extract($_REQUEST);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
    $marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	//$job_no_arr=return_library_array( "SELECT id,job_no from wo_po_details_master",'id','job_no');
	$po_qnty_tot=return_field_value( "sum(po_quantity)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	$sample_name_name_arr=return_library_array( "select id,sample_name from    lib_sample",'id','sample_name');
	?>
	<div style="width:1330px" align="center">
    										<!--    Header Company Information         -->
    <?php
	$nameArray_approved = sql_select("select max(b.approved_no) as approved_no, a.is_approved from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=13 group by a.is_approved");

	list($nameArray_approved_row) = $nameArray_approved;
	$nameArray_approved_date = sql_select("select b.approved_date as approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=13 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "'");
	list($nameArray_approved_date_row) = $nameArray_approved_date;
	$nameArray_approved_comments = sql_select("select b.comments as comments from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=13 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "'");
	list($nameArray_approved_comments_row) = $nameArray_approved_comments;
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
									Province No: <?php echo $result[csf('province')]; ?>
									Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
									Email Address: <? echo $result[csf('email')];?>
									Website No: <? echo $result[csf('website')];
								}
                            ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:20px">
                            	<strong><? echo $report_title;?> &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:#F00"><? if($nameArray_approved_row[csf('is_approved')]==1){ echo "(Approved)";} else if($nameArray_approved_row[csf('is_approved')]==3){ echo "(Partial Approved)";}else{echo "";}; ?> </font></strong><!--//ISD-22-05701 by kausar-->
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
        $nameArray=sql_select( "SELECT a.id,a.booking_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.po_break_down_id,a.colar_excess_percent,a.cuff_excess_percent,a.fabric_composition,a.delivery_date,a.is_apply_last_update,a.pay_mode,a.fabric_source,b.job_no,b.buyer_name, b.style_ref_no ,b.gmts_item_id,b.order_uom,b.total_set_qnty,b.style_description,b.season,b.product_dept,b.product_code,b.pro_sub_dep,b.dealing_marchant from wo_booking_mst a, wo_po_details_master b where  a.job_no=b.job_no and a.booking_no=$txt_booking_no and a.company_id='$cbo_company_name' and b.company_name='$cbo_company_name'"); //2 compnay check for sample job is FAL-15-00586 in development
        foreach ($nameArray as $result)
        {
			$total_set_qnty=$result[csf('total_set_qnty')];
			$order_uom=$result[csf('order_uom')];
			$colar_excess_percent=$result[csf('colar_excess_percent')];
			$cuff_excess_percent=$result[csf('cuff_excess_percent')];
			$po_no="";$file_no="";$ref_no="";
			$shipment_date="";
			 $sql_po= "SELECT po_number,grouping,file_no,MIN(pub_shipment_date) pub_shipment_date from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].") group by po_number,grouping,file_no";
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
				$sql_lead_time= "SELECT DATEDIFF(pub_shipment_date,po_received_date) date_diff from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")";
			}

			if($db_type==2)
			{
				$sql_lead_time= "SELECT (pub_shipment_date-po_received_date) date_diff from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")";
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
                    <td width="100"><span style="font-size:18px"><b>Job No</b></span></td>
                    <td width="110">:&nbsp;<span style="font-size:18px"><b><? echo $result[csf('job_no')]; ?></b></span></td>
                    <td width="100"><span style="font-size:12px"><b>Dept.</b></span></td>
                    <td width="110">:&nbsp;<? echo $product_dept[$result[csf('product_dept')]] ; if($result[csf('product_code')] !=""){ echo " (".$result[csf('product_code')].")";} if($result[csf('pro_sub_dep')] !=0){ echo " (".$pro_sub_dept_array[$result[csf('pro_sub_dep')]].")";}?></td>
                    
                </tr>
                <tr>
                	<td width="100"><span style="font-size:12px"><b>Order Qnty</b></span></td>
                    <td width="110">:&nbsp;<? echo $po_qnty_tot." ".$unit_of_measurement[$result[csf('order_uom')]] ; ?></td>
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
                    
                </tr>
                <tr>
                	<td width="100" style="font-size:18px"><b>Style Ref.</b>   </td>
                    <td width="110" style="font-size:18px">:&nbsp;<b><? echo $result[csf('style_ref_no')];?></b></td>
                    <td  width="100" style="font-size:12px"><b>Style Des.</b></td>
                    <td  width="110" >:&nbsp;<? echo $result[csf('style_description')]; $job_no= $result[csf('job_no')];?></td>
                    <td width="100" style="font-size:12px"><b>Lead Time </b>   </td>
                    <td width="110">:&nbsp;<?  echo rtrim($lead_time,",");;?> </td>
                    
                </tr>
                <tr>
                	<td width="100" style="font-size:12px"><b>Dealing Merchant</b></td>
                    <td width="110">:&nbsp;<? echo $marchentrArr[$result[csf('dealing_marchant')]]; ?></td>
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
                    
                </tr>
                <tr>
                	<td width="100" style="font-size:18px"><b>Booking No </b>   </td>
                    <td width="150" style="font-size:18px">:&nbsp;<b><? echo $result[csf('booking_no')];?></b><? echo " (".$fabric_source[$result[csf('fabric_source')]].")"?></td>
                    <td width="100" style="font-size:12px"><b>Season</b></td>
                    <td width="110">:&nbsp;<? echo $result[csf('season')]; ?></td>
                    <td width="100" style="font-size:12px"><b>Attention</b></td>
                    <td width="110" >:&nbsp;<? echo $result[csf('attention')]; ?></td>
                    
                </tr>
                <tr>
                	<td width="100" style="font-size:12px"><b>Po Received Date</b></td>
                    <td width="110" >:&nbsp;<? echo $po_received_date; ?></td>
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
		$nameArray_fabric_description= sql_select("SELECT a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,min(b.uom) as uom,b.dia_width,b.process_loss_percent FROM wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls b where b.booking_no =$txt_booking_no and a.id=b.pre_cost_fabric_cost_dtls_id  and b.status_active=1 and
	b.is_deleted=0  group by a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width,b.process_loss_percent order by a.body_part_id");
	    ?>
        <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
            <tr align="center"><th colspan="3" align="left">Body Part</th>
				<?
                foreach($nameArray_fabric_description  as $result_fabric_description)
                {
                    if( $result_fabric_description[csf('body_part_id')] == "")  echo "<td  colspan='2'>&nbsp</td>";
                    else echo "<td  colspan='2'>". $body_part[$result_fabric_description[csf('body_part_id')]]."</td>";
					
					$uom_id=$result_fabric_description[csf('uom')];
                }
                ?>
            	<td rowspan="8" width="50"><p>Total  Finish Fabric (<? //if(trim($cbo_fabric_natu ,"'")==2){echo "(KG)";}else{echo "(Yds)";}
				echo $unit_of_measurement[$uom_id];
				 ?>)</p></td> <td  rowspan="8" width="50"><p>Total Grey Fabric (<? //if(trim($cbo_fabric_natu ,"'")==2){echo "(KG)";}else{echo "(Yds)";}
				 echo $unit_of_measurement[$uom_id];
				  ?>)</p></td>
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

			$color_wise_wo_sql=sql_select("select job_no, fabric_color_id, po_break_down_id, $sample_type_id as sample_type
										  FROM
										  wo_booking_dtls
										  WHERE
										  booking_no =$txt_booking_no and
										  status_active=1 and
                                          is_deleted=0
										  group by job_no, fabric_color_id, po_break_down_id");
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
                        $lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='".$color_wise_wo_result[csf('job_no')]."' and approval_status=3 and color_name_id=".$color_wise_wo_result[csf('fabric_color_id')]." and po_break_down_id=".$color_wise_wo_result[csf('po_break_down_id')]."","lapdip_no");
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
															  b.is_deleted=0 and b.po_break_down_id=".$color_wise_wo_result[csf('po_break_down_id')]."");
							
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
		$nameArray_fabric_description= sql_select("SELECT a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,min(b.uom) as uom,b.dia_width,b.process_loss_percent FROM wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls b where b.booking_no =$txt_booking_no and a.id=b.pre_cost_fabric_cost_dtls_id  and b.status_active=1 and
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
                <td rowspan="8" width="50"><p>Total  Fabric (<? //if(trim($cbo_fabric_natu ,"'")==2){echo "(KG)";}else{echo "(Yds)";}
				echo $unit_of_measurement[$result_fabric_description[csf('uom')] ];
				 ?>)</p></td>
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
	}?>
	<!-- start  -->
	<div style="width:1330px; float:left">
	
	<?
	// Body Part type used only Cuff and Flat Knit
	$colar_percent_size_wise_array=return_library_array( "select a.colar_cuff_per,b.gmts_sizes from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b,wo_pre_cost_fabric_cost_dtls c  where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.pre_cost_fabric_cost_dtls_id=c.id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.color_size_table_id and a.booking_no=$txt_booking_no and a.booking_type=1 and c.body_part_type in(40,50) and a.status_active=1 and a.is_deleted=0", "gmts_sizes", "colar_cuff_per");
	
	//	echo  "select a.body_part_type,a.body_part_id,sum(d.rmg_qty) as rmg_qty,d.gmts_size,d.gmts_color_id FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls d WHERE a.job_no=d.job_no and d.booking_no =$txt_booking_no and  a.id=d.pre_cost_fabric_cost_dtls_id  and d.status_active=1 and d.is_deleted=0 and d.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and a.body_part_type in(40,50) group by a.body_part_type,a.body_part_id,d.gmts_size,d.gmts_color_id order by a.body_part_id";
	
	$nameArray_body_part=sql_select( "select a.body_part_type,a.body_part_id,sum(d.bh_qty) as rmg_qty,d.gmts_size,d.gmts_color_id FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls d WHERE a.job_no=d.job_no and d.booking_no =$txt_booking_no and  a.id=d.pre_cost_fabric_cost_dtls_id  and d.status_active=1 and d.is_deleted=0 and d.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and a.body_part_type in(40,50) group by a.body_part_type,a.body_part_id,d.gmts_size,d.gmts_color_id order by a.body_part_id");
	
	
	
	$row_count=count($nameArray_body_part);
	//if($row_count==0) echo " <p style='color:#f00; text-align:center; font-size:15px;'> Body part type is  used only Flat Knit and Cuff.</p> ";
	foreach($nameArray_body_part as $row)
	{
		$body_part_arr[$row[csf('body_part_id')]]['bpart_type']=$row[csf('body_part_type')];
		$body_part_rmg_qty_arr[$row[csf('body_part_id')]][$row[csf('gmts_size')]][$row[csf('gmts_color_id')]]['rmg_qty']+=$row[csf('rmg_qty')];
	}
	// print_r($body_part_arr);
	$tbl_row_count=count($body_part_arr);
	//echo $tbl_row_count.'Dx';
	?>
	
	
	<?
	
	$k=1;
	foreach($body_part_arr as $body_id=>$val)
	{
		$k++;
	
		$bpart_type_id=$val['bpart_type'];
	$nameArray_item_size=sql_select( "select min(c.id) as id,b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no  and a.body_part_id=$body_id  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id  and d.status_active=1 and d.is_deleted=0 and a.body_part_type in(40,50) group by b.item_size,c.size_number_id order by id");
	
	
	
	
	?>
	
	<div style="max-height:1330px; width:660px; overflow:auto; float:left; padding-top:20px; margin-left:5px; margin-bottom:5px; position: relative;">
	<table  width="100%" align="left"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
	<tr>
	<td colspan="<? echo count($nameArray_size)+4;?>" align="center"><b><? echo $body_part[$body_id];?> -  Colour Size Breakedown in Pcs</b></td>
	</tr>
	<tr>
	<td width="70">Size</td>
	
	<?
	
	/*$nameArray_item_size=sql_select( "select b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no  and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 group by c.size_number_id,b.item_size order by c.size_number_id");*/
	foreach($nameArray_item_size  as $result_size)
	{
	?>
	<td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
	<?
	}
	?>
	<td rowspan="2" align="center"><strong>Total</strong></td>
	
	</tr>
	<tr>
	<td>Collar Size</td>
	
	<?
	foreach($nameArray_item_size  as $result_item_size)
	{
	?>
	<td align="center" style="border:1px solid black"><strong><? echo $result_item_size[csf('item_size')];?></strong></td>
	<?
	}
	?>
	 <?
		$color_library=return_library_array( "select id,color_name from lib_color where status_active =1 and is_deleted=0", "id", "color_name");
		$color_wise_wo_sql=sql_select("select a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method,c.color_number_id,sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no and a.body_part_id=$body_id  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 and a.body_part_type in(40,50) group by c.color_number_id,a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method order by a.id
	");
	foreach($color_wise_wo_sql as $color_wise_wo_result)
	{
		$color_total_collar=0;
		$color_total_collar_order_qnty=0;
		$process_loss_method=$color_wise_wo_result[csf("process_loss_method")];
		$constrast_color_arr=array();
		if($color_wise_wo_result[csf("color_size_sensitive")]==3)
		{
			$constrast_color=explode('__',$color_wise_wo_result[csf("color_break_down")]);
			for($i=0;$i<count($constrast_color);$i++)
			{
				$constrast_color2=explode('_',$constrast_color[$i]);
				$constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
			}
		}
	?>
		<tr>
		<td>
		<?
		if($color_wise_wo_result[csf("color_size_sensitive")]==3)
		{
			echo strtoupper ($constrast_color_arr[$color_wise_wo_result[csf('color_number_id')]]) ;
			$lab_dip_color_id=return_field_value("contrast_color_id","wo_pre_cos_fab_co_color_dtls","job_no='".$color_wise_wo_result[csf('job_no')]."' and pre_cost_fabric_cost_dtls_id=".$color_wise_wo_result[csf('id')]." and gmts_color_id=".$color_wise_wo_result[csf('color_number_id')]."");
		}
		else
		{
			echo $color_library[$color_wise_wo_result[csf('color_number_id')]];
			$lab_dip_color_id=$color_wise_wo_result[csf('color_number_id')];
		}
		?>
	
		</td>
		<?
		foreach($nameArray_item_size  as $result_size)
		{
			?>
			<td align="center" style="border:1px solid black">
	
			<?
			$rmg_qty=$body_part_rmg_qty_arr[$body_id][$result_size[csf('size_number_id')]][$color_wise_wo_result[csf('color_number_id')]]['rmg_qty'];
			//echo $bpart_type_id.'=';
			if($bpart_type_id==50)//Cuff
			{
				$fab_rmg_qty=$rmg_qty*2;
			}
			else //Flat Knit
			{
				$fab_rmg_qty=$rmg_qty;
			}
			//$color_wise_wo_sql_qnty=sql_select("select c.color_number_id,sum(c.order_quantity) as order_quantity, sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 and c.color_number_id='".$color_wise_wo_result['color_number_id']."' and c.size_number_id='".$result_size['size_number_id']."'");
			/*$color_wise_wo_sql_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$color_wise_wo_result[csf('color_number_id')]."   and  status_active=1 and is_deleted =0");
	
			list($plan_cut_qnty)=$color_wise_wo_sql_qnty;*/
			//$plan_cut=$plan_cut_qnty[csf('plan_cut_qnty')];
			//$colar_excess_per=($plan_cut*$colar_excess_percent)/100;
			echo number_format($fab_rmg_qty,0);
			$color_total_collar+=$fab_rmg_qty;
			$color_total_collar_order_qnty+=$fab_rmg_qty;
			$grand_total_collar+=$fab_rmg_qty;
			$grand_total_collar_order_qnty+=$fab_rmg_qty;
	
			$size_tatal[$result_size[csf('size_number_id')]]+=$fab_rmg_qty;
			?>
			</td>
			<?
		}
		?>
	
		<td align="center"><? echo number_format($color_total_collar,0); ?></td>
	
		</tr>
		<?
		}
		?>
		<tr>
			<td>Size Total</td>
	
			<?
			foreach($nameArray_item_size  as $result_size)
			{
				//$colar_excess_pers=($size_tatal[$result_size[csf('size_number_id')]]*$colar_excess_percent)/100;
				$tot_size_tatal=$size_tatal[$result_size[csf('size_number_id')]];
				//$size_tatal[$result_size[csf('size_number_id')]]=0;
			?>
			<td style="border:1px solid black;  text-align:center"><?  echo number_format($size_tatal[$result_size[csf('size_number_id')]],0);$size_tatal[$result_size[csf('size_number_id')]]=0; ?></td>
			<?
			}
			?>
			<td  style="border:1px solid black;  text-align:center"><?  echo number_format($grand_total_collar,0);$grand_total_collar=0; ?></td>
	
		</tr>
	</table>
	  <br/>
	</div>
	
		
	  <!--End here-->	
	<!-- end -->
	<?
	}
	
	
	




	if(str_replace("'","",$cbo_fabric_source)==1)
	{
		$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');
		//$yarn_sql_array=sql_select("SELECT min(id) as id ,count_id, copm_one_id, percent_one,copm_two_id, percent_two, type_id, sum(cons_qnty) as yarn_required, AVG(rate) as rate from wo_pre_cost_fab_yarn_cost_dtls where job_no='$job_no' and  status_active=1 and is_deleted=0 group by count_id,copm_one_id,percent_one, copm_two_id,percent_two,type_id order by id");
		//echo "SELECT min(a.id) as id ,a.count_id, a.copm_one_id, a.percent_one,a.copm_two_id, a.percent_two, a.color, a.type_id, sum(a.cons_qnty) as yarn_required, AVG(a.rate) as rate,b.po_break_down_id from wo_pre_cost_fab_yarn_cost_dtls a, wo_booking_dtls b where a.job_no=b.job_no and a.fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.job_no='$job_no' and b.booking_no=$txt_booking_no and  a.status_active=1 and a.is_deleted=0 group by a.count_id,a.copm_one_id,a.percent_one, a.copm_two_id,a.percent_two,a.color,a.type_id,b.po_break_down_id order by po_break_down_id";

		$po_number_arr=return_library_array( "select b.id,b.po_number from wo_po_break_down b,wo_booking_dtls a where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no ", "id", "po_number"  );
		$yarn_sql_array=sql_select("SELECT a.fabric_cost_dtls_id,min(a.id) as id ,a.count_id, a.copm_one_id, a.percent_one,a.copm_two_id, a.percent_two, a.color, a.type_id, sum(a.cons_qnty) as yarn_required, AVG(a.rate) as rate,b.po_break_down_id,sum(b.grey_fab_qnty) as grey_fab_qnty, a.cons_ratio from wo_pre_cost_fab_yarn_cost_dtls a, wo_booking_dtls b where a.job_no=b.job_no and a.fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.job_no='$job_no' and b.booking_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.count_id,a.copm_one_id,a.percent_one, a.copm_two_id,a.percent_two,a.color,a.type_id,b.po_break_down_id,a.fabric_cost_dtls_id, a.cons_ratio order by po_break_down_id ");
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
                                 <td><? echo $po_number_arr[$row[csf('po_break_down_id')]]; ?></td>
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
					// $is_yarn_allocated=return_field_value("allocation","variable_settings_inventory","company_name=$cbo_company_name and variable_list=18 and item_category_id=1");
					// if($is_yarn_allocated==1)
					// {

						$sql = "select booking_no,qty,item_group_id,description, uom,remarks from wo_booking_accessories_dtls where booking_no=".$txt_booking_no."";

						$accessories_data=sql_select($sql);
			
						if ( count($accessories_data)>0)
						{
						?>
						<!-- <font style=" font-size:30px"><b>Draft</b></font> -->

						<table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                        <tr align="center">
                        	<td colspan="7"><b>Accessories Details</b></td>
                        </tr>
                        <tr align="center">
                            <td>Sl</td>
                            <td>Item Name</td>
                            <td>Item Description</td>
                            <td>UOM</td>
                            <td>Qty</td>
                        </tr>
                        <?
                        $total_qty=0;
                         $item=return_library_array( "select id, item_name from   lib_item_group where item_category=4 and is_deleted=0  and  status_active=1 order by item_name",'id','item_name');
                   
				
                        $i=0;
                        $total_yarn=0;
                        foreach($accessories_data  as $row)
                        {
							$i++;
							?>
							<tr align="center">
                                <td><? echo $i; ?></td>
                                <td><? echo $item[$row[csf('item_group_id')]]; ?></td>
                                <td><? echo $row[csf('description')]; ?></td>
                                <td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                                <td align="right"><? echo number_format($row[csf('qty')],4); $total_qty+= $row[csf('qty')];?></td>
							</tr>
							<?
                        }
                        ?>
                        <tr align="center">
                            <td>Total</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td align="right"><? echo number_format($total_qty,4); ?></td>
                        </tr>
					</table>
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
	<?
	$txt_req_no=$dataArray[0][csf("requisition_number")];
	$color_name_arr=return_library_array( "SELECT id,color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
	$mst_style_id=rtrim($style_id,',');

	?>
	    <table width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" style="font-family:Arial Narrow;">
	    		<caption> <strong style="float:left"> Stripe Details</strong></caption>
	    		<?
	    		$color_name_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	    		$sql_stripe=("select c.id,c.composition,c.construction,c.body_part_id,c.fabric_description,c.gsm_weight,c.color_type_id,b.grey_fab_qnty as fab_qty,b.dia_width,d.color_number_id as color_number_id,d.id as did,d.stripe_color,d.fabreqtotkg as fabreqtotkg ,d.measurement as measurement ,d.yarn_dyed,d.uom  from wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c,wo_pre_stripe_color d where c.id=b.pre_cost_fabric_cost_dtls_id and c.job_no=b.job_no and d.pre_cost_fabric_cost_dtls_id=c.id and d.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and b.job_no=d.job_no and b.job_no=$txt_job_no  and d.job_no=$txt_job_no and b.booking_no=$txt_booking_no  and c.color_type_id in (2,3,4,6,32,33,34)  and b.status_active=1  and c.is_deleted=0 and c.status_active=1  and d.is_deleted=0 and d.status_active=1  and 	b.is_deleted=0  group by c.id,c.body_part_id,c.fabric_description,c.gsm_weight,c.color_type_id,b.grey_fab_qnty,d.color_number_id,d.id,d.stripe_color,d.yarn_dyed,d.fabreqtotkg ,d.measurement,d.uom,c.composition,c.construction,b.dia_width order by d.id ");
	    		$result_data=sql_select($sql_stripe);
	    		$bodypart_wise_grey_qnty=array();
	    		foreach($result_data as $row)
	    		{
	    			$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['stripe_color'][$row[csf('did')]]=$row[csf('stripe_color')];
	    			$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['measurement'][$row[csf('did')]]=$row[csf('measurement')];
	    			$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['uom'][$row[csf('did')]]=$row[csf('uom')];
	    			$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['fabreqtotkg'][$row[csf('did')]]=$row[csf('fabreqtotkg')];
	    			$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['fab_qty'][$row[csf('did')]]=$row[csf('fab_qty')];
	    			$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['yarn_dyed'][$row[csf('did')]]=$row[csf('yarn_dyed')];
	    			$bodypart_wise_grey_qnty[$row[csf('body_part_id')]][$row[csf('color_number_id')]]=$row[csf('fab_qty')];

	    			$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['composition']=$row[csf('composition')];
	    			$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['construction']=$row[csf('construction')];
	    			$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['gsm_weight']=$row[csf('gsm_weight')];
	    			$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['color_type_id']=$row[csf('color_type_id')];
	    			$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['dia_width']=$row[csf('dia_width')];
	    			$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['fabreqtotkg']+=$row[csf('fabreqtotkg')];
	    		}
	    		?>

	    			<tr>
	    				<th width="30"> SL</th>
	    				<th width="100"> Body Part</th>
	    				<th width="80"> Fabric Color</th>
	    				<th width="70"> Fabric Qty(KG)</th>
	    				<th width="70"> Stripe Color</th>
	    				<th width="70"> Stripe Measurement</th>
	    				<th width="70"> Stripe Uom</th>
	    				<th  width="70"> Qty.(KG)</th>
	    				<th  width="70"> Y/D Req.</th>
	    			</tr>
	    			<?
	    			//if($db_type==0) $color_cond="d.fabric_color_id='".$color_id."'";
	    			//else if($db_type==2) $color_cond="nvl(d.fabric_color_id,0)=nvl('".$color_id."',0)";
	    				//else if($db_type==2) $color_cond="nvl(d.fabric_color_id,0)=nvl('".$color_id."',0)";


	    			$i=1;$total_fab_qty=0;$total_fabreqtotkg=0;$fab_data_array=array();
	    			foreach($stripe_arr as $body_id=>$body_data)
	    			{
	    				foreach($body_data as $color_id=>$color_val)
	    				{
	    					$rowspan=count($color_val['stripe_color']);
	    					$composition=$stripe_arr2[$body_id][$color_id]['composition'];
	    					$construction=$stripe_arr2[$body_id][$color_id]['construction'];
	    					$gsm_weight=$stripe_arr2[$body_id][$color_id]['gsm_weight'];
	    					$color_type_id=$stripe_arr2[$body_id][$color_id]['color_type_id'];
	    					$dia_width=$stripe_arr2[$body_id][$color_id]['dia_width'];

	    					?>
	    					<tr>
	    					<?
	    					$color_qty= array_sum($stripe_arr[$body_id][$color_id]['fabreqtotkg']);
	    					$grey_fab=$bodypart_wise_grey_qnty[$body_id][$color_id];
	    					?>
	    					<td rowspan="<? echo $rowspan;?>"> <? echo $i; ?></td>
	    					<td rowspan="<? echo $rowspan;?>"> <? echo $body_part[$body_id]; ?></td>
	    					<td rowspan="<? echo $rowspan;?>"> <? echo $color_name_arr[$color_id]; ?></td>
	    					<td rowspan="<? echo $rowspan;?>" align="right"> <? echo number_format($grey_fab,2) //number_format($color_qty,2); ?></td>
	    					<?
	    					$total_fab_qty+=$grey_fab;
	    					
	    					$measure=0;
	    					foreach($color_val['stripe_color'] as $strip_color_id=>$s_color_val)
	    					{
	    						$measurement=$color_val['measurement'][$strip_color_id];
	    						$measure+=fn_number_format($measurement,2,".","");
	    					}
	    					foreach($color_val['stripe_color'] as $strip_color_id=>$s_color_val)
	    					{
	    						$measurement=$color_val['measurement'][$strip_color_id];
	    						$uom=$color_val['uom'][$strip_color_id];
	    						$fabreqtotkg=$color_val['fabreqtotkg'][$strip_color_id];
	    						$yarn_dyed=$color_val['yarn_dyed'][$strip_color_id];
	    						?>
	    						<td><?  echo  $color_name_arr[$s_color_val]; ?></td>
	    						<td align="right"> <? echo  number_format($measurement,2); ?></td>
	    						<td> <? echo  $unit_of_measurement[$uom]; ?></td>
	    						<td align="right"> <? echo number_format(($grey_fab/$measure)*$measurement,2)  //number_format($fabreqtotkg,2); ?></td>
	    						
	    						<td> <? echo  $yes_no[$yarn_dyed]; ?></td>
	    						</tr>
	    						<?
	    						//$total_fabreqtotkg+=$fabreqtotkg;
	    						$total_fabreqtotkg+=(($grey_fab/$measure)*$measurement);
	    					}
	    					$i++;
	    				}
	    			}
	    			?>
	    			<tfoot>
	    				<tr>
	    					<td colspan="3">Total </td>
	    					<td align="right">  <? echo  number_format($total_fab_qty,2); ?> </td>
	    					<td></td>
	    					<td></td>
	    					<td>   </td>
	    					<td align="right"><? echo  number_format($total_fabreqtotkg,2); ?> </td>
	    				</tr>
	    			</tfoot>
	    </table>

        <br/>


         <?
	$lib_designation_arr=return_library_array(" select id,custom_designation from lib_designation","id","custom_designation");
	$user_lib_designation_arr=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
	$user_lib_name_arr=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");

	$mst_id=return_field_value("id as mst_id","wo_booking_mst","booking_no=$txt_booking_no","mst_id");
	//echo $mst_id.'ssD';
	//and b.un_approved_date is null
	 $approve_data_array=sql_select("select b.approved_by,min(b.approved_date) as approved_date from   approval_history b where b.mst_id=$mst_id and b.entry_form=13  group by  b.approved_by order by b.approved_by asc");
	 //echo "select b.approved_by,min(b.approved_date) as approved_date from   approval_history b where b.mst_id=$mst_id and b.entry_form=13  group by  b.approved_by order by b.approved_by asc";
	 $unapprove_data_array=sql_select("select b.approved_by,b.approved_date,b.un_approved_reason,b.un_approved_date,b.approved_no from   approval_history b where b.mst_id=$mst_id and b.entry_form=13  order by b.approved_date,b.approved_by");

	?>
    <td width="49%" valign="top">
	<?
          if(count($approve_data_array)>0)
			{
 		?>
       <table  width="850" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
            <tr style="border:1px solid black;">
                <th colspan="5" style="border:1px solid black;">Approval Status</th>
                </tr>
                <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th>
				<th width="40%" style="border:1px solid black;">Name</th>
				<th width="30%" style="border:1px solid black;">Designation</th>
				<th width="27%" style="border:1px solid black;">Approval Date</th>

                </tr>
            </thead>
            <tbody>
            <?
			$i=1;
			foreach($approve_data_array as $row){


			?>
            <tr style="border:1px solid black;">
                <td width="3%" style="border:1px solid black;"><? echo $i;?></td>
				<td width="40%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]];?></td>
				<td width="30%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]];?></td>
				<td width="27%" style="border:1px solid black;text-align:center"><? echo date ("d-m-Y h:i:s",strtotime($row[csf('approved_date')]));?></td>

                </tr>
                <?
				$i++;
			}
				?>
            </tbody>
        </table>
		<?
		}
		?>
		<br>
		<?
		if(count($unapprove_data_array)>0)
		{
			//and approval_type=2
			$sql_unapproved=sql_select("select booking_id,approval_cause from fabric_booking_approval_cause where  entry_form=13 and is_deleted=0 and status_active=1 and booking_id=$mst_id");
			//echo "select booking_id,approval_cause from fabric_booking_approval_cause where  entry_form=13 and approval_type=2 and is_deleted=0 and status_active=1 and booking_id=$mst_id";
			$unapproved_request_arr=array();
			foreach($sql_unapproved as $rowu)
			{
			$unapproved_request_arr[$rowu[csf('booking_id')]]=$rowu[csf('approval_cause')];
			}
 		?>
       <table  width="850" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
            <tr style="border:1px solid black;">
                <th colspan="6" style="border:1px solid black;">Approval/Un Approval History</th>
                </tr>
                <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th>
				<th width="30%" style="border:1px solid black;">Name</th>
				<th width="20%" style="border:1px solid black;">Designation</th>
				<th width="5%" style="border:1px solid black;">Approval Status</th>
				<th width="20%" style="border:1px solid black;">Reason For Un Approval</th>
				<th width="22%" style="border:1px solid black;"> Date</th>

                </tr>
            </thead>
            <tbody>
            <?
			$i=1;
			foreach($unapprove_data_array as $row){

			?>
            <tr style="border:1px solid black;">
                <td width="3%" style="border:1px solid black;"><? echo $i;?></td>
				<td width="30%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]];?></td>
				<td width="20%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]];?></td>
				<td width="5%" style="border:1px solid black; text-align:center"><? echo 'Yes';?></td>
				<td width="20%" style="border:1px solid black;"><? echo '';?></td>
				<td width="22%" style="border:1px solid black;text-align:center"><? if($row[csf('approved_date')]!="") echo date ("d-m-Y h:i:s",strtotime($row[csf('approved_date')])); else echo "";?></td>
             </tr>
				<?
				$i++;
				$un_approved_date= explode(" ",$row[csf('un_approved_date')]);
				$un_approved_date=$un_approved_date[0];
				if($db_type==0) //Mysql
				{
					if($un_approved_date=="" || $un_approved_date=="0000-00-00") $un_approved_date="";else $un_approved_date=$un_approved_date;
				}
				else
				{
					if($un_approved_date=="") $un_approved_date="";else $un_approved_date=$un_approved_date;
				}

				if($un_approved_date!="")
				{
				?>
			<tr style="border:1px solid black;">
                <td width="3%" style="border:1px solid black;"><? echo $i;?></td>
				<td width="30%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]];?></td>
				<td width="20%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]];?></td>
				<td width="5%" style="border:1px solid black;text-align:center;"><? echo 'No';?></td>
				<td width="20%" style="border:1px solid black;text-align:center"><? echo $unapproved_request_arr[$mst_id];?></td>
				<td width="22%" style="border:1px solid black;text-align:center"><? if($row[csf('un_approved_date')]!="") echo date ("d-m-Y h:i:s",strtotime($row[csf('un_approved_date')])); else echo "";?></td>
              </tr>

                <?
				$i++;
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

}

if($action=="show_fabric_booking_report4") 
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$txt_requisition=str_replace("'","",$txt_requisition);
	$txt_supplier_name=str_replace("'","",$txt_supplier_name); 
	

	$process_loss_method=return_field_value("process_loss_method", "variable_order_tracking", "company_name='$cbo_company_name'  and variable_list=18 and item_category_id=2 and status_active=1 and is_deleted=0");


	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	if ($db_type == 0) $select_group_row = " order by master_tble_id desc limit 0,3";
	else if ($db_type == 2) $select_group_row = " and  rownum<=4 order by id desc";
	$imge_arr_for_book=sql_select( "select master_tble_id,image_location,real_file_name from   common_photo_library where  master_tble_id=$txt_booking_no and file_type=1  $select_group_row ");
	
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$com_supplier_id=return_field_value( "supplier_id as supplier_id", "wo_non_ord_samp_booking_mst","booking_no=$txt_booking_no","supplier_id");

	$nameArray_approved=sql_select( "select max(b.approved_no) as approved_no from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and a.booking_no=$txt_booking_no and b.entry_form=9"); 
	list($nameArray_approved_row)=$nameArray_approved;
	$nameArray_approved_date=sql_select( "select b.approved_date as approved_date from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and a.booking_no=$txt_booking_no and b.entry_form=9 and b.approved_no='".$nameArray_approved_row[csf('approved_no')]."'");
	list($nameArray_approved_date_row)=$nameArray_approved_date;
	 $fabric_source_new=array(1=>"Production",2=>"Purchase",3=>"Buyer Supplied",4=>"Stock");

	?>
	<div style="width:1330px; font-family:'Arial Narrow';font-style: normal;font-variant: normal;font-weight: 400;
	line-height: 20px;" align="center">       
    										<!--    Header Company Information         --> 
       <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black" >
           <tr>
               <td width="100"> 
               <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               </td>
               <td width="1000">                                     
                    <table width="100%" cellpadding="0" cellspacing="0" >
                        <tr>
                            <td align="center" style="font-size:24px;">
                            <strong>
                              <?php      
                                    echo $company_library[$cbo_company_name];
                              ?>
                              </strong>
                            
						   </td>
                            
                            <td rowspan="3" width="">
							<?
                             if($nameArray_approved_row[csf('approved_no')]>1)
                             {
                             ?>
                             <b> Revised No: <? echo $nameArray_approved_row[csf('approved_no')]-1; ?></b>
                              <br/>
                              Approved Date: <? echo $nameArray_approved_date_row[csf('approved_date')]; ?>
                              <?
                             }
                            ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px">  
                            <?
                            $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name"); 
                            foreach ($nameArray as $result)
                            { 
                            ?>
                                           <? echo $result[csf('plot_no')].'&nbsp;'; ?> 
                                            <? echo $result[csf('level_no')].'&nbsp;' ?>
                                            <? echo $result[csf('road_no')].'&nbsp;'; ?> 
                                            <? echo $result[csf('block_no')].'&nbsp;';?> 
                                            <? echo $result[csf('city')].'&nbsp;';?> 
                                            <? echo $result[csf('zip_code')].'&nbsp;'; ?> 
                                             <?php echo $result[csf('province')].'&nbsp;';?> 
                                           <? echo $country_arr[$result[csf('country_id')]].'&nbsp;'; ?><br> 
                                            <? echo $result[csf('email')];?> 
                                             <? echo $result[csf('website')];
                            }
                                            ?>   
                          
                               </td> 
                            </tr>
                            <tr>
                             
                            <td align="center" style="font-size:20px;"> <strong style="margin-left:77px;"><? if($report_title !=""){echo $report_title;} else {echo "Sample Fabric Booking -Without order";}?> &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="r:#F00"><? if(str_replace("'","",$id_approved_id) ==1){ echo "(Approved)";}else{echo "";}; ?> </font></strong>
                             <div style="margin-left:854px; margin-top:-75px; position: absolute; float:right;  ">
	                            <?
								foreach($imge_arr_for_book as $row)
								{
									?>
	                                <img  src='../../<? echo $row[csf('image_location')]; ?>' height='80' width='80' />
									<?
								}
	
								?>
                            </div>
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
				$season="";
				$req_no="";
				$buyer_req_no="";$bh_merchant="";$style_ref_no="";$product_code="";$product_department="";
				
				$nameseason=sql_select( "SELECT a.season as season_buyer_wise, b.buyer_req_no,a.bh_merchant,a.style_ref_no,a.product_code,a.product_dept,a.requisition_number_prefix_num  from  sample_development_mst a, sample_development_dtls b, wo_booking_dtls c  where  a.id=b.sample_mst_id and a.id=c.style_id and c.booking_no=$txt_booking_no and b.status_active=1 and   b.is_deleted=0 and c.status_active=1 and   c.is_deleted=0 ");
				foreach ($nameseason as $season_row)
				{
					$season=$season_row[csf('season_buyer_wise')];
					$buyer_req_no=$season_row[csf('buyer_req_no')];
					$bh_merchant=$season_row[csf('bh_merchant')];
					$product_code=$season_row[csf('product_code')];
					$product_department=$product_dept[$season_row[csf('product_dept')]];
					$req_no=$season_row[csf('requisition_number_prefix_num')];	
				}
				unset($nameseason);

				$nameStyleArray=sql_select( "SELECT  b.style_ref_no  from wo_booking_mst a, wo_po_details_master b where  a.job_no=b.job_no and a.booking_no=$txt_booking_no and a.company_id='$cbo_company_name' and b.company_name='$cbo_company_name' and a.status_active=1 and   a.is_deleted=0 and b.status_active=1 and   b.is_deleted=0"); 
				foreach ($nameStyleArray as $style_row)
				{
					$style_ref_no=$style_row[csf('style_ref_no')];
				}
				unset($nameStyleArray);

				$fabric_source='';
				$season_library=return_library_array( "SELECT id,season_name from lib_buyer_season", "id", "season_name");
                $nameArray=sql_select( "SELECT buyer_id, fabric_source, booking_no, job_no,  pay_mode, booking_date, internal_ref_no, supplier_id, currency_id, exchange_rate, attention, delivery_date, fabric_source, team_leader, dealing_marchant from wo_booking_mst  where  booking_no=$txt_booking_no");
				foreach ($nameArray as $result)
				{
					$fabric_source_id=$result[csf('fabric_source')];
					$varcode_booking_no=$result[csf('booking_no')];
				?>
       <table width="100%" style="border:1px solid black">                    	
            <tr>
                <td colspan="6" valign="top"></td>                             
            </tr>                                                
            <tr>
                <td width="100" style="font-size:18px"><b>Booking No</b></td>
                <td width="110" style="font-size:18px">:&nbsp;<b><? echo $result[csf('booking_no')];?></b> </td>
                <td width="100" style="font-size:12px"><b>Booking Date</b></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('booking_date')],'dd-mm-yyyy','-');?>&nbsp;&nbsp;&nbsp;</td>		
                <td width="100"><span style="font-size:12px"><b>Delivery Date</b></span></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>
                
                <td width="100"><span style="font-size:12px"><b>Job No :</b></span></td>
                <td width="110">&nbsp;<b><? echo $result[csf('job_no')];$job_no= $result[csf('job_no')];?></b> </td>
            </tr>
            <tr>
                <td width="100"><span style="font-size:12px"><b>Buyer/Agent Name</b></span></td>
                <td width="110">:&nbsp;<? echo $buyer_name_arr[$result[csf('buyer_id')]]; ?></td>
                <td width="100" style="font-size:12px"><b>Supplier Name</b>   </td>
                <td width="110" style="font-size:12px">:&nbsp; <b><? 
				echo $txt_supplier_name;?></b></td>
                <td width="100" style="font-size:12px"><b>Supplier Address</b></td>
               	<td width="110">:&nbsp;
                
				<? 
				if($result[csf('pay_mode')]==5 || $result[csf('pay_mode')]==3 ){
				$comAdd=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=".$result[csf('supplier_id')]); 
					foreach ($comAdd as $comAddRow){ 
						echo $comAddRow[csf('plot_no')].'&nbsp;'; 
						echo $comAddRow[csf('level_no')].'&nbsp;' ;
						echo $comAddRow[csf('road_no')].'&nbsp;'; 
						echo $comAddRow[csf('block_no')].'&nbsp;';
						echo $comAddRow[csf('city')].'&nbsp;';
						 
					}
				}
				else{
					echo $supplier_address_arr[$result[csf('supplier_id')]];
				}
				?>
                </td> 
                <td width="100" style="font-size:12px"><b>Department Name</b></td>
               	<td width="110">:&nbsp;<? echo $product_department;?></td> 
            </tr>
             <tr>
                <td width="100" style="font-size:12px"><b>Currency</b></td>
                <td width="110">:&nbsp;<? echo $currency[$result[csf('currency_id')]]; ?></td>
                <td  width="100" style="font-size:12px"><b>Conversion Rate</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('exchange_rate')]; ?></td>
                <td  width="100" style="font-size:12px"><b>Attention</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('attention')]; ?></td>
                <td  width="100" style="font-size:12px"><b>Department No</b></td>
                <td  width="110" >:&nbsp;<? echo $product_code; ?></td>
            </tr> 
            <tr>
                <td width="100" style="font-size:12px"><b>Season</b></td>
                <td width="110">:&nbsp;<? echo $season_library[$season]; ?></td>
                <td  width="100" style="font-size:12px"><b>Buyer Req. No</b></td>
                <td  width="110" >:&nbsp;<? echo $buyer_req_no; ?></td>
                <td  width="100" style="font-size:12px"><b>Dealing Merchant</b></td>
                <td  width="110" >:&nbsp;<? echo $marchentrArr[$result[csf('dealing_marchant')]]; ?></td>
                <td  width="100" style="font-size:12px"><b>Buying Merchant Name</b></td>
                <td  width="110" >:&nbsp;<? echo $bh_merchant; ?></td>
            </tr> 
             <tr>
             	<td width="100" style="font-size:12px"><b>Fabric Source</b></td>
             	<td width="110">:&nbsp;<? echo $fabric_source_new[$fabric_source_id]; ?></td>
             	<td  width="100" style="font-size:12px"><b>Req. No</b></td>
             	<td  width="110" >:&nbsp;<? echo $txt_requisition; ?></td>
             	<td  width="100" style="font-size:12px"><b>Style Ref.</b></td>
             	<td  width="110" >:&nbsp;<? echo $style_ref_no ; ?></td>
             	<td  width="100" style="font-size:12px"><b>Internal Ref.</b></td>
             	<td  width="110" >:&nbsp;<strong><? echo $result[csf('internal_ref_no')] ; ?></strong></td>
              </tr>
        </table>  
        <?
			}
		?>
      <br/>
      <? 
    $sample_library=return_library_array( "select id,sample_name from lib_sample", "id", "sample_name"  );
	$color_lib=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$size_lib=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );	 
	
	$po_no_arr=return_library_array( "SELECT a.id,a.po_number from wo_po_break_down a,wo_booking_dtls b where a.id=b.po_break_down_id and b.booking_no=$txt_booking_no", "id", "po_number" );
	 $sql_dtls="select a.po_break_down_id, b.body_part_id,a.style_id,a.sample_type,a.body_part, b.color_type_id, b.construction, b.composition, b.gsm_weight,a.gmts_color_id, a.fabric_color_id, a.item_size, a.dia_width, a.fin_fab_qnty, a.process_loss_percent, a.uom, a.grey_fab_qnty, a.rate, a.amount, a.id, a.pre_cost_fabric_cost_dtls_id,a.remark,a.additional_process,a.fabric_source,a.fabric_description,a.gmt_item as gmts_item_id,b.width_dia_type FROM wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b WHERE a.pre_cost_fabric_cost_dtls_id=b.id and a.booking_no=$txt_booking_no and a.is_short=2 and a.status_active=1 and	a.is_deleted=0";
	 //echo $sql_dtls;//die;
	$sample_result=sql_select($sql_dtls);$style_id='';
	foreach($sample_result as $row)
	{
		$data_array_color_wise[$row[csf("uom")]][$row[csf("po_break_down_id")]][$row[csf("sample_type")]][$row[csf("gmts_item_id")]][$row[csf("gmts_color_id")]][$row[csf("body_part_id")]][$row[csf("fabric_description")]][$row[csf("fabric_color_id")]][$row[csf("gsm_weight")]]["finish_fabric"]+=$row[csf("fin_fab_qnty")];
		$data_array_color_wise[$row[csf("uom")]][$row[csf("po_break_down_id")]][$row[csf("sample_type")]][$row[csf("gmts_item_id")]][$row[csf("gmts_color_id")]][$row[csf("body_part_id")]][$row[csf("fabric_description")]][$row[csf("fabric_color_id")]][$row[csf("gsm_weight")]]["uom"]=$row[csf("uom")];
		$data_array_color_wise[$row[csf("uom")]][$row[csf("po_break_down_id")]][$row[csf("sample_type")]][$row[csf("gmts_item_id")]][$row[csf("gmts_color_id")]][$row[csf("body_part_id")]][$row[csf("fabric_description")]][$row[csf("fabric_color_id")]][$row[csf("gsm_weight")]]["req_dzn"]+=$row[csf("req_dzn")];
		$data_array_color_wise[$row[csf("uom")]][$row[csf("po_break_down_id")]][$row[csf("sample_type")]][$row[csf("gmts_item_id")]][$row[csf("gmts_color_id")]][$row[csf("body_part_id")]][$row[csf("fabric_description")]][$row[csf("fabric_color_id")]][$row[csf("gsm_weight")]]["fabric_description"]=$row[csf("fabric_description")];
		$data_array_color_wise[$row[csf("uom")]][$row[csf("po_break_down_id")]][$row[csf("sample_type")]][$row[csf("gmts_item_id")]][$row[csf("gmts_color_id")]][$row[csf("body_part_id")]][$row[csf("fabric_description")]][$row[csf("fabric_color_id")]][$row[csf("gsm_weight")]]["composition"]=$row[csf("composition")];
		$data_array_color_wise[$row[csf("uom")]][$row[csf("po_break_down_id")]][$row[csf("sample_type")]][$row[csf("gmts_item_id")]][$row[csf("gmts_color_id")]][$row[csf("body_part_id")]][$row[csf("fabric_description")]][$row[csf("fabric_color_id")]][$row[csf("gsm_weight")]]["construction"]=$row[csf("construction")];
		$data_array_color_wise[$row[csf("uom")]][$row[csf("po_break_down_id")]][$row[csf("sample_type")]][$row[csf("gmts_item_id")]][$row[csf("gmts_color_id")]][$row[csf("body_part_id")]][$row[csf("fabric_description")]][$row[csf("fabric_color_id")]][$row[csf("gsm_weight")]]["fabdec"]=$row[csf("construction")].','.$row[csf("composition")];
		$data_array_color_wise[$row[csf("uom")]][$row[csf("po_break_down_id")]][$row[csf("sample_type")]][$row[csf("gmts_item_id")]][$row[csf("gmts_color_id")]][$row[csf("body_part_id")]][$row[csf("fabric_description")]][$row[csf("fabric_color_id")]][$row[csf("gsm_weight")]]["color_type_id"]=$row[csf("color_type_id")];
		$data_array_color_wise[$row[csf("uom")]][$row[csf("po_break_down_id")]][$row[csf("sample_type")]][$row[csf("gmts_item_id")]][$row[csf("gmts_color_id")]][$row[csf("body_part_id")]][$row[csf("fabric_description")]][$row[csf("fabric_color_id")]][$row[csf("gsm_weight")]]["dia_width"]=$row[csf("dia_width")];
		$data_array_color_wise[$row[csf("uom")]][$row[csf("po_break_down_id")]][$row[csf("sample_type")]][$row[csf("gmts_item_id")]][$row[csf("gmts_color_id")]][$row[csf("body_part_id")]][$row[csf("fabric_description")]][$row[csf("fabric_color_id")]][$row[csf("gsm_weight")]]["dia"]=$row[csf("dia")];
		$data_array_color_wise[$row[csf("uom")]][$row[csf("po_break_down_id")]][$row[csf("sample_type")]][$row[csf("gmts_item_id")]][$row[csf("gmts_color_id")]][$row[csf("body_part_id")]][$row[csf("fabric_description")]][$row[csf("fabric_color_id")]][$row[csf("gsm_weight")]]["width_dia_type"]=$row[csf("width_dia_type")];
		$data_array_color_wise[$row[csf("uom")]][$row[csf("po_break_down_id")]][$row[csf("sample_type")]][$row[csf("gmts_item_id")]][$row[csf("gmts_color_id")]][$row[csf("body_part_id")]][$row[csf("fabric_description")]][$row[csf("fabric_color_id")]][$row[csf("gsm_weight")]]["gsm_weight"]=$row[csf("gsm_weight")];
		$data_array_color_wise[$row[csf("uom")]][$row[csf("po_break_down_id")]][$row[csf("sample_type")]][$row[csf("gmts_item_id")]][$row[csf("gmts_color_id")]][$row[csf("body_part_id")]][$row[csf("fabric_description")]][$row[csf("fabric_color_id")]][$row[csf("gsm_weight")]]["process_loss_percent"]=$row[csf("process_loss_percent")];
		$data_array_color_wise[$row[csf("uom")]][$row[csf("po_break_down_id")]][$row[csf("sample_type")]][$row[csf("gmts_item_id")]][$row[csf("gmts_color_id")]][$row[csf("body_part_id")]][$row[csf("fabric_description")]][$row[csf("fabric_color_id")]][$row[csf("gsm_weight")]]["remark"]=$row[csf("remark")];
		$data_array_color_wise[$row[csf("uom")]][$row[csf("po_break_down_id")]][$row[csf("sample_type")]][$row[csf("gmts_item_id")]][$row[csf("gmts_color_id")]][$row[csf("body_part_id")]][$row[csf("fabric_description")]][$row[csf("fabric_color_id")]][$row[csf("gsm_weight")]]["additional_process"]=$row[csf("additional_process")];
		$data_array_color_wise[$row[csf("uom")]][$row[csf("po_break_down_id")]][$row[csf("sample_type")]][$row[csf("gmts_item_id")]][$row[csf("gmts_color_id")]][$row[csf("body_part_id")]][$row[csf("fabric_description")]][$row[csf("fabric_color_id")]][$row[csf("gsm_weight")]]["fabric_color"]=$row[csf("fabric_color_id")];
		$data_array_color_wise[$row[csf("uom")]][$row[csf("po_break_down_id")]][$row[csf("sample_type")]][$row[csf("gmts_item_id")]][$row[csf("gmts_color_id")]][$row[csf("body_part_id")]][$row[csf("fabric_description")]][$row[csf("fabric_color_id")]][$row[csf("gsm_weight")]]["po_id"]=$row[csf("po_break_down_id")];
		$data_array_color_wise[$row[csf("uom")]][$row[csf("po_break_down_id")]][$row[csf("sample_type")]][$row[csf("gmts_item_id")]][$row[csf("gmts_color_id")]][$row[csf("body_part_id")]][$row[csf("fabric_description")]][$row[csf("fabric_color_id")]][$row[csf("gsm_weight")]]["item_size"]=$row[csf("item_size")];
		$data_array_color_wise[$row[csf("uom")]][$row[csf("po_break_down_id")]][$row[csf("sample_type")]][$row[csf("gmts_item_id")]][$row[csf("gmts_color_id")]][$row[csf("body_part_id")]][$row[csf("fabric_description")]][$row[csf("fabric_color_id")]][$row[csf("gsm_weight")]]["amount"]+=$row[csf("amount")];
		$data_array_color_wise[$row[csf("uom")]][$row[csf("po_break_down_id")]][$row[csf("sample_type")]][$row[csf("gmts_item_id")]][$row[csf("gmts_color_id")]][$row[csf("body_part_id")]][$row[csf("fabric_description")]][$row[csf("fabric_color_id")]][$row[csf("gsm_weight")]]["rate"]=$row[csf("rate")];
		$data_uom_wise[$row[csf("uom")]]=$row[csf("uom")];
		$style_id.=$row[csf("style_id")].',';
		
	}

//print_r($data_uom_wise);
	  
	foreach($data_array_color_wise as $uom_id=>$uom_data)
	{
		foreach($uom_data as $po_id=>$po_data)
	{
	 foreach($po_data as $sample_type=>$gmts_data)
	 {
		
		foreach($gmts_data as $gmts_item_id=>$gmts_color_data)
		{	
			
			foreach($gmts_color_data as $gmts_color_id=>$body_part_data)
			{	
				$sample_span=0;
				foreach($body_part_data as $body_part_id=>$dtm_data)
				{
					
					foreach($dtm_data as $dtm_id=>$fabric_data)
					{
						foreach($fabric_data as $fabric_id=>$gsm_data)
						{
							foreach($gsm_data as $gsm_id=>$row)
							{
								
								$sample_span++;
							}
						}	
					}
					$sample_item_wise_span[$uom_id][$po_id][$sample_type][$gmts_item_id][$gmts_color_id]=$sample_span;
					
				}
				
				
			}
			
		  }

		}
	}
	}
	/*echo "<pre>";
	print_r($sample_item_wise_span);die;*/
	
	 

	$sample_mst_id=$sample_result[0][csf("style_id")];
	
	$sql_sample_dtls= "SELECT a.sample_name,a.article_no,b.color_name  from sample_development_dtls a , lib_color b  where a.status_active=1 and a.is_deleted=0 and a.entry_form_id=13  and sample_mst_id=$sample_mst_id and b.status_active=1 and b.id=a.sample_color  group by a.sample_name,a.article_no,b.color_name";
  //echo $sql_sample_dtls;//die;
	foreach(sql_select($sql_sample_dtls) as $key=>$value)
	{
		if($sample_wise_article_no[$value[csf("sample_name")]][$value[csf("color_name")]]=="")
		{
			$sample_wise_article_no[$value[csf("sample_name")]][$value[csf("color_name")]]=$value[csf("article_no")];
		}
		else
		{
			if(!in_array($value[csf("article_no")], $sample_wise_article_no))
			{
				$sample_wise_article_no[$value[csf("sample_name")]][$value[csf("color_name")]].= ','.$value[csf("article_no")];
			}
			
		}
		
	}

	$sample_dtls_sql="SELECT sample_name,gmts_item_id,sample_color,sample_prod_qty from sample_development_dtls where status_active=1 and is_deleted=0 and sample_mst_id=$sample_mst_id and entry_form_id=117";
	 foreach(sql_select($sample_dtls_sql) as $vals)
	 {
	 	$sample_dtls_array[$vals[csf("sample_name")]][$vals[csf("gmts_item_id")]][$vals[csf("sample_color")]]+=$vals[csf("sample_prod_qty")];

	 }

	
	foreach($data_array_color_wise as $uom_id=>$data_array_color_wise)
	{
	?>
    <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
    <caption><? echo $unit_of_measurement[$uom_id];?> </caption>
        <thead>
            <tr>
				<th width="30">Sl</th>               
				<th width="90">PO No</th>
				<th width="90">Article No</th>
				<th width="110">Sample</th>
				<th width="80"> Gmts Color</th>               
				<th width="120">Body Part</th>
				<th width="200">Fabric Details and Composition</th>
				<th width="80">Color Type</th>
				<th width="80">Fab.Color</th>
				<th width="40">Item Size</th>
				<th width="40">GSM</th>
				<th width="55">Dia</th>
				<th width="80">Dia Type</th>
				<th width="80">Fin Fab Qnty</th>
				<th width="40">P. Loss</th>
				<th width="60">Grey Qty</th>
				<th width="40">UOM</th>
                <?
                if($show_comment==1)
				{
				?>
                <th width="40">Rate</th>
                <th width="50">Amount</th>
                <?
				}
				?>
				<th>Additional Process</th>
				<th>Remarks</th>                 
            </tr>
        </thead>
        <tbody>
        <?
        $p=1;
        $total_finish=0;
        $total_grey=0;
        $total_process=0;
		
        foreach($data_array_color_wise as $po_id=>$po_data)
        {
			foreach($po_data as $sample_type=>$gmts_data)
        {

        	foreach($gmts_data as $gmts_item_id=>$gmts_color_data)
        	{	
        		foreach($gmts_color_data as $gmts_color_id=>$body_part_data)
        		{
        			$i=0;
        			foreach($body_part_data as $body_part_id=>$dtm_data)
        			{	
        				
        				foreach($dtm_data as $dtm_id=>$fabric_data)
        				{
							foreach($fabric_data as $fabric_id=>$gsm_data)
        				{
        					
        					foreach($gsm_data as $gsm_id=>$value)
        					{
        						$txt_finish_qnty=$value["finish_fabric"];//($value["req_dzn"]/12)*$sample_dtls_array[$sample_type][$gmts_item_id][$value["gmts_color_id"]];
        						$processloss=$value["process_loss_percent"]; 
        						$WastageQty='';
        						if($process_loss_method==1)
        						{
        							$WastageQty=$txt_finish_qnty+$txt_finish_qnty*($processloss/100);
        						}
        						else if($process_loss_method==2)
        						{
        							$devided_val = 1-($processloss/100);
        							$WastageQty=$txt_finish_qnty/$devided_val;
        						}
        						else
        						{
        							$WastageQty=0;
        						}
								if($uom_id==$value["uom"])
								{
        						?>
        						<tr>

        							<?
        							if($i==0)
        							{
        								?>
        								<td width="30" rowspan="<? echo $sample_item_wise_span[$uom_id][$po_id][$sample_type][$gmts_item_id][$gmts_color_id];?>" align="center" style="word-wrap: break-word;word-break: break-all;"><? echo $p;$p++;?></td>
										<td width="90" rowspan="<? echo $sample_item_wise_span[$uom_id][$po_id][$sample_type][$gmts_item_id][$gmts_color_id];?>"  align="center"><? echo $po_no_arr[$value["po_id"]];?></td>
        								<td width="90" rowspan="<? echo $sample_item_wise_span[$uom_id][$po_id][$sample_type][$gmts_item_id][$gmts_color_id];?>"  align="center"><? echo $sample_wise_article_no[$sample_type][$gmts_color_id];?></td>
        								<td width="110" rowspan="<? echo $sample_item_wise_span[$uom_id][$po_id][$sample_type][$gmts_item_id][$gmts_color_id];?>"  align="center"><? echo $sample_library[$sample_type]; ?></td>
        								<td width="80"  align="center" rowspan="<? echo $sample_item_wise_span[$uom_id][$po_id][$sample_type][$gmts_item_id][$gmts_color_id];?>"><? echo $color_lib[$gmts_color_id];?> </td>
        								<?
        							}	
        							$i++;
        							?>          						             
        						<td width="120"     align="center"><? echo $body_part[$body_part_id];?></td>
        						<td width="200"  align="center"><? echo $value["fabdec"]. ",GSM ".$value["gsm_weight"];?></td>
        						<td width="80"  align="center"> <? echo $color_type[$value["color_type_id"]]; ?></td>
        						<td width="80"  align="center"><? echo $color_lib[$value["fabric_color"]]; ?></td>
        						<td width="40"  align="center"><? echo $value["item_size"]; ?></td>
        						<td width="40"  align="center"><? echo $value["gsm_weight"]; ?></td>
        						<td width="55"  align="center"><? echo $value["dia_width"]; ?></td>
        						<td width="80"  align="center"><? echo $fabric_typee[$value["width_dia_type"]]; ?></td>
        						<td width="80" align="right"><? echo number_format($txt_finish_qnty,2);?></td>
        						<td width="40" align="right"><? echo $value["process_loss_percent"];?></td>
        						<td width="60" align="right"><? echo number_format($WastageQty,2);?></td>
        						<td width="40"  align="center"><? echo $unit_of_measurement[$value["uom"]];?></td>
                                 <?
								if($show_comment==1)
								{
								?>
                                <td width="40"  align="right"><? echo $value["rate"]; ?></td>
                                <td width="50"  align="right"><? echo number_format($value["amount"],4); ?></td>
                                <?
								}
								?>
								<td><p><? echo  $value["additional_process"];?> </p></td> 
        						<td><p><? echo  $value["remark"];?> </p></td>    
        					</tr>
        					<?
        					//$i++;
        					$total_finish +=$txt_finish_qnty;
							$total_amount +=$value["amount"];
							//$total_finish +=$txt_finish_qnty;
        					$total_grey +=$WastageQty;
        					$total_process +=$value["process_loss"];
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

       			<tr>
					<th colspan="13" align="right"><b>Total</b></th>
					<th width="80" align="right"><? echo number_format($total_finish,2);?></th>					
					<th width="40" align="right">&nbsp;</th>
					<th width="60" align="right"><? echo number_format($total_grey,2);?></th>
                    <th width="40" align="right">&nbsp;</th>
                     <?
					if($show_comment==1)
					{
					?>
                    <th width="40" align="right">&nbsp;</th>					
					<th width="50" align="right"> <? echo number_format($total_amount,4);?></th>
                    <?
					}
					?>
					<th width=""> </th>
                    <th width=""> </th>
					                  
	            </tr>
		
        </tbody>
        
           
        
    </table>
     <br/>
    <?
	}
	?>
    <br/>

	<table width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" style="font-family:Arial Narrow;">
	    		<caption> <strong style="float:left"> Stripe Details</strong></caption>
	    		<?
	    		$color_name_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	    		$sql_stripe=("select c.id,c.composition,c.construction,c.body_part_id,c.fabric_description,c.gsm_weight,c.color_type_id,b.grey_fab_qnty as fab_qty,b.dia_width,d.color_number_id as color_number_id,d.id as did,d.stripe_color,d.fabreqtotkg as fabreqtotkg ,d.measurement as measurement ,d.yarn_dyed,d.uom  from wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c,wo_pre_stripe_color d where c.id=b.pre_cost_fabric_cost_dtls_id and c.job_no=b.job_no and d.pre_cost_fabric_cost_dtls_id=c.id and d.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and b.job_no=d.job_no and b.job_no=$txt_job_no  and d.job_no=$txt_job_no and b.booking_no=$txt_booking_no  and c.color_type_id in (2,3,4,6,32,33,34)  and b.status_active=1  and c.is_deleted=0 and c.status_active=1  and d.is_deleted=0 and d.status_active=1  and 	b.is_deleted=0  group by c.id,
				c.composition,c.construction,c.body_part_id,c.fabric_description,c.gsm_weight,c.color_type_id,b.grey_fab_qnty,b.dia_width,d.color_number_id,d.id,d.stripe_color,d.fabreqtotkg,d.measurement,d.yarn_dyed,d.uom");
	    		$result_data=sql_select($sql_stripe);
	    		$bodypart_wise_grey_qnty=array();
	    		foreach($result_data as $row)
	    		{
	    			$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['stripe_color'][$row[csf('did')]]=$row[csf('stripe_color')];
	    			$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['measurement'][$row[csf('did')]]=$row[csf('measurement')];
	    			$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['uom'][$row[csf('did')]]=$row[csf('uom')];
	    			$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['fabreqtotkg'][$row[csf('did')]]=$row[csf('fabreqtotkg')];
	    			$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['fab_qty'][$row[csf('did')]]=$row[csf('fab_qty')];
	    			$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['yarn_dyed'][$row[csf('did')]]=$row[csf('yarn_dyed')];
	    			$bodypart_wise_grey_qnty[$row[csf('body_part_id')]][$row[csf('color_number_id')]]=$row[csf('fab_qty')];

	    			$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['composition']=$row[csf('composition')];
	    			$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['construction']=$row[csf('construction')];
	    			$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['gsm_weight']=$row[csf('gsm_weight')];
	    			$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['color_type_id']=$row[csf('color_type_id')];
	    			$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['dia_width']=$row[csf('dia_width')];
	    			$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['fabreqtotkg']+=$row[csf('fabreqtotkg')];
	    		}
	    		?>

	    			<tr>
	    				<th width="30"> SL</th>
	    				<th width="100"> Body Part</th>
	    				<th width="80"> Fabric Color</th>
	    				<th width="70"> Fabric Qty(KG)</th>
	    				<th width="70"> Stripe Color</th>
	    				<th width="70"> Stripe Measurement</th>
	    				<th width="70"> Stripe Uom</th>
	    				<th  width="70"> Qty.(KG)</th>
	    				<th  width="70"> Y/D Req.</th>
	    			</tr>
	    			<?	    			
	    			$i=1;$total_fab_qty=0;$total_fabreqtotkg=0;$fab_data_array=array();
	    			foreach($stripe_arr as $body_id=>$body_data)
	    			{
	    				foreach($body_data as $color_id=>$color_val)
	    				{
	    					$rowspan=count($color_val['stripe_color']);
	    					$composition=$stripe_arr2[$body_id][$color_id]['composition'];
	    					$construction=$stripe_arr2[$body_id][$color_id]['construction'];
	    					$gsm_weight=$stripe_arr2[$body_id][$color_id]['gsm_weight'];
	    					$color_type_id=$stripe_arr2[$body_id][$color_id]['color_type_id'];
	    					$dia_width=$stripe_arr2[$body_id][$color_id]['dia_width'];

	    					?>
	    					<tr>
	    					<?
	    					$color_qty= array_sum($stripe_arr[$body_id][$color_id]['fabreqtotkg']);
	    					$grey_fab=$bodypart_wise_grey_qnty[$body_id][$color_id];
	    					?>
	    					<td rowspan="<? echo $rowspan;?>"> <? echo $i; ?></td>
	    					<td rowspan="<? echo $rowspan;?>"> <? echo $body_part[$body_id]; ?></td>
	    					<td rowspan="<? echo $rowspan;?>"> <? echo $color_name_arr[$color_id]; ?></td>
	    					<td rowspan="<? echo $rowspan;?>" align="right"> <? echo number_format($grey_fab,2) //number_format($color_qty,2); ?></td>
	    					<?
	    					$total_fab_qty+=$grey_fab;
	    					
	    					$measure=0;
	    					foreach($color_val['stripe_color'] as $strip_color_id=>$s_color_val)
	    					{
	    						$measurement=$color_val['measurement'][$strip_color_id];
	    						$measure+=fn_number_format($measurement,2,".","");
	    					}
	    					foreach($color_val['stripe_color'] as $strip_color_id=>$s_color_val)
	    					{
	    						$measurement=$color_val['measurement'][$strip_color_id];
	    						$uom=$color_val['uom'][$strip_color_id];
	    						$fabreqtotkg=$color_val['fabreqtotkg'][$strip_color_id];
	    						$yarn_dyed=$color_val['yarn_dyed'][$strip_color_id];
	    						?>
	    						<td><?  echo  $color_name_arr[$s_color_val]; ?></td>
	    						<td align="right"> <? echo  number_format($measurement,2); ?></td>
	    						<td> <? echo  $unit_of_measurement[$uom]; ?></td>
	    						<td align="right"> <? echo number_format(($grey_fab/$measure)*$measurement,2)  //number_format($fabreqtotkg,2); ?></td>
	    						
	    						<td> <? echo  $yes_no[$yarn_dyed]; ?></td>
	    						</tr>
	    						<?
	    						//$total_fabreqtotkg+=$fabreqtotkg;
	    						$total_fabreqtotkg+=(($grey_fab/$measure)*$measurement);
	    					}
	    					$i++;
	    				}
	    			}
	    			?>
	    			<tfoot>
	    				<tr>
	    					<td colspan="3">Total </td>
	    					<td align="right">  <? echo  number_format($total_fab_qty,2); ?> </td>
	    					<td></td>
	    					<td></td>
	    					<td>   </td>
	    					<td align="right"><? echo  number_format($total_fabreqtotkg,2); ?> </td>
	    				</tr>
	    			</tfoot>
	    </table>
	
	<?
	$lib_item_group_arr=array();
    		$itemArray=sql_select( "select item_name,trim_uom,id from lib_item_group where item_category=4 and is_deleted=0  and  status_active=1 order by item_name" );
    		foreach ($itemArray as $row)
    		{    			 
    			$lib_item_group_arr[$row[csf('id')]]=$row[csf('item_name')];
    		}

    		$data_array=sql_select("SELECT booking_no, item_group_id, description, uom, qty,   remarks from wo_booking_accessories_dtls where  booking_no=$txt_booking_no"); 
    		if ( count($data_array)>0)
    		{
				?>
    <table class="rpt_table" width="800" align="left" border="1" cellpadding="0" cellspacing="0" rules="all">
    	<thead>
    		<tr>
    			<th align="center" width="40">Sl</th>
    			<th align="center" width="130" >Item Group</th>
    			<th align="center" width="100" >Description</th>
    			<th align="center" width="100" >UOM</th>
    			<th align="center" width="100" >Qty</th>
    			<th align="center"   >Remarks</th>
    		</tr>
    	</thead>
    	<tbody>
    		<?  
    		
    			$l=1;
    			$tot_qnty=0;
    			foreach( $data_array as $key=>$row )
    			{

    				?>
    				<tr>
    					<td  align="center"> <? echo $l;?> </td>
    					<td  align="center"> <? echo $lib_item_group_arr[$row[csf("item_group_id")]]; ?> </td>
    					<td  align="center"> <? echo $row[csf("description")]; ?> </td>
    					<td  align="center"> <? echo $unit_of_measurement[$row[csf("uom")]]; ?> </td>
    					<td  align="center"> <? echo $qnty=$row[csf("qty")]; ?> </td>
    					<td  align="center"> <? echo $row[csf("remarks")]; ?> </td>
    				</tr>
    				<?
    				$l++;
    				$tot_qnty+=$qnty;
    			}
    			?>
    			<tr>   
    				<td colspan="4" align="right"><strong>Grand Total</strong> </td> 					
    				<td  align="center"> <? echo $tot_qnty; ?> </td>
    				<td  align="center">  </td>
    			</tr>
    				<?
    		}

    		?>
    	</tbody>
    </table>
    <table class="rpt_table"  style="margin-top: 10px;" width="800" align="left" border="1" cellpadding="0" cellspacing="0" rules="all">
    	<thead>
    		<tr>
    			<th align="center" width="40">Sl</th>
    			<th align="center" width="120" >Size Name</th>
    			<th align="center" width="100" >BH Qty</th>
    			<th align="center" width="100" >RH Qty</th>
    			<th align="center" width="100" >Dyeing </th>
				<th align="center" width="100" >Test </th>
				<th align="center" width="100" >Self </th>
    			<th align="center"   >Total</th>
    		</tr>
    	</thead>
    	<tbody>
    		<?
    		//sample_development_mst
			 $sql_size="select a.id,a.gmts_size,a.bh_qty,a.rf_qty from wo_booking_dtls a where a.booking_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0";
			$size_result=sql_select($sql_size);

    		if ( count($size_result)>0)
    		{
    			$l=1;
    			$tot_plan_qnty=$tot_bh_qty=$tot_dyeing_qty=$tot_test_qty=$tot_self_qty=$tot_rf_qty=$tot_total_qty=0;
    			foreach( $size_result as $row )
    			{

    				?>
    				<tr>
    					<td  align="center"> <? echo $l;?> </td>
    					<td  align="center"> <? echo $size_library[$row[csf("gmts_size")]]; ?> </td>
    					<td  align="right"> <? echo $row[csf("bh_qty")]; ?> </td>
    					<td  align="right"> <? echo $row[csf("rf_qty")]; ?> </td>
    					<td  align="right"> <? echo $row[csf("dyeing_qty")]; ?> </td>
    					<td  align="right"> <? echo $row[csf("test_qty")]; ?> </td>
						<td  align="right"> <? echo $row[csf("self_qty")]; ?> </td>
						<td  align="right"> <? echo $row[csf("total_qty")]; ?> </td>
    				</tr>
    				<?
    				$l++;
    				$tot_rf_qty+=$row[csf("rf_qty")];
					$tot_bh_qty+=$row[csf("bh_qty")];
					$tot_dyeing_qty+=$row[csf("dyeing_qty")];
					$tot_test_qty+=$row[csf("test_qty")];
					$tot_self_qty+=$row[csf("self_qty")];
					$tot_total_qty+=$row[csf("total_qty")];
    			}
    			?>
    			<tr>
    				<td colspan="2" align="right"><strong>Grand Total</strong> </td>
    				<td  align="right"> <? echo number_format($tot_bh_qty,0); ?> </td>
					<td  align="right"> <? echo number_format($tot_rf_qty,0); ?> </td>
    				<td  align="right"> <? echo number_format($tot_dyeing_qty,0); ?> </td>
					<td  align="right"> <? echo number_format($tot_test_qty,0); ?> </td>
					<td  align="right"> <? echo number_format($tot_self_qty,0); ?> </td>
					<td  align="right"> <? echo number_format($tot_total_qty,0); ?> </td>
    			</tr>
    				<?
    		}

    		?>
    	</tbody>
    </table>
	<?
		//$po_break_down_arr 
		if(str_replace("'","",$cbo_fabric_source)==1)
		{
			$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');
	
			$po_number_arr=return_library_array( "select b.id,b.po_number from wo_po_break_down b,wo_booking_dtls a where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no ", "id", "po_number"  );
			$yarn_sql_array=sql_select("SELECT a.fabric_cost_dtls_id,min(a.id) as id ,a.count_id, a.copm_one_id, a.percent_one,a.copm_two_id, a.percent_two, a.color, a.type_id, sum(a.cons_qnty) as yarn_required, AVG(a.rate) as rate,b.po_break_down_id,sum(b.grey_fab_qnty) as grey_fab_qnty, a.cons_ratio from wo_pre_cost_fab_yarn_cost_dtls a, wo_booking_dtls b where a.job_no=b.job_no and a.fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.job_no='$job_no' and b.booking_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.count_id,a.copm_one_id,a.percent_one, a.copm_two_id,a.percent_two,a.color,a.type_id,b.po_break_down_id,a.fabric_cost_dtls_id, a.cons_ratio order by po_break_down_id ");
			?>
			<table  width="100%"  border="0" cellpadding="0" cellspacing="0" >
				<tr>
					<td width="49%" valign="top">
						<table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
							<tr align="center">
								<td colspan="7"><b>Yarn Summary</b></td>
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
									 <td><? echo $po_number_arr[$row[csf('po_break_down_id')]]; ?></td>
									<td>
										<?
										$yarn_des=$yarn_count_arr[$row[csf('count_id')]]." ".$composition[$row[csf('copm_one_id')]]." ".$row[csf('percent_one')]."%  ";
										if($row['copm_two_id'] !=0)
										{
											$yarn_des.=$composition[$row[csf('copm_two_id')]]." ".$row[csf('percent_two')]."%";
										}
										$yarn_des.=$yarn_type[$row[csf('type_id')]];
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
						// $is_yarn_allocated=return_field_value("allocation","variable_settings_inventory","company_name=$cbo_company_name and variable_list=18 and item_category_id=1");
						// if($is_yarn_allocated==1)
						// {
	
							$sql = "select booking_no,qty,item_group_id,description, uom,remarks from wo_booking_accessories_dtls where booking_no=".$txt_booking_no."";
	
							$accessories_data=sql_select($sql);
				
							if ( count($accessories_data)>0)
							{
							?>
							<!-- <font style=" font-size:30px"><b>Draft</b></font> -->
	
							<table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
							<tr align="center">
								<td colspan="7"><b>Accessories Details</b></td>
							</tr>
							<tr align="center">
								<td>Sl</td>
								<td>Item Name</td>
								<td>Item Description</td>
								<td>UOM</td>
								<td>Qty</td>
							</tr>
							<?
							$total_qty=0;
							 $item=return_library_array( "select id, item_name from   lib_item_group where item_category=4 and is_deleted=0  and  status_active=1 order by item_name",'id','item_name');
					   
					
							$i=0;
							$total_yarn=0;
							foreach($accessories_data  as $row)
							{
								$i++;
								?>
								<tr align="center">
									<td><? echo $i; ?></td>
									<td><? echo $item[$row[csf('item_group_id')]]; ?></td>
									<td><? echo $row[csf('description')]; ?></td>
									<td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
									<td align="right"><? echo number_format($row[csf('qty')],4); $total_qty+= $row[csf('qty')];?></td>
								</tr>
								<?
							}
							?>
							<tr align="center">
								<td>Total</td>
								<td></td>
								<td></td>
								<td></td>
								<td align="right"><? echo number_format($total_qty,4); ?></td>
							</tr>
						</table>
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
        <br/> <br/>
    		<table style="margin-top: 10px;" class="rpt_table" width="800" align="left" border="1" cellpadding="0" cellspacing="0" rules="all">
                	<thead>
                    	<tr>
                        	<th align="left" width="40">Sl</th>
                        	<th align="left" >Special Instruction</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?   
					$data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no"); 
					if ( count($data_array)>0)
					{
						$l=1;
						foreach( $data_array as $key=>$row )
						{
							
							?>
                            	<tr  align="">
                                    <td> <? echo $l;?> </td>
                                    <td> <? echo $row[csf("terms")]; ?> </td>
                                </tr>
                            <?
                            $l++;
						}
					}
					 
					?>
                </tbody>
            </table>
             </br>  

			 <? $booking_mst_info=sql_select("SELECT a.id as mst_id, a.insert_date, b.user_full_name, c.custom_designation,a.company_id from wo_booking_mst a join user_passwd b on b.id = a.inserted_by join lib_designation c on c.id = b.designation where a.status_active=1 and a.is_deleted=0 and a.BOOKING_NO=$txt_booking_no");
	$mst_arr=array('insert_date','user_full_name','custom_designation','mst_id','company_id');
	foreach($booking_mst_info as $row){
		foreach($mst_arr as $data){
			$$data=$row[csf($data)];
		}
	}

	$electronic_sequence_arr = [];
    $get_electronic_sequence_sql = sql_select("select sequence_no as sequence_no from electronic_approval_setup where page_id = 411 and company_id=$company_id and is_deleted = 0 order by sequence_no asc");
    foreach ($get_electronic_sequence_sql as $sequence){
        $electronic_sequence_arr[] = $sequence['SEQUENCE_NO'];
    }

    $sql_get_checked_user = sql_select("select user_passwd.user_full_name as USER_FULL_NAME, lib_designation.custom_designation as CUSTOM_DESIGNATION, to_char(approval_history.approved_date, 'DD-MM-YYYY HH:MI:SS AM') as APPROVED_DATE from approval_history left join user_passwd on user_passwd.id = approval_history.approved_by left join lib_designation on lib_designation.id = user_passwd.designation where approval_history.entry_form = 13 and approval_history.mst_id = $mst_id and approval_history.sequence_no in(".implode(',', $electronic_sequence_arr).") and approval_history.id = (select max(id) from approval_history where entry_form = 13 and mst_id = $mst_id and sequence_no = ".min($electronic_sequence_arr).") and rownum = 1 and (select max(CURRENT_APPROVAL_STATUS) from approval_history where entry_form = 13 and mst_id = $mst_id) = 1 order by approval_history.approved_no asc");
	
    $sql_get_approved_user = sql_select("select user_passwd.user_full_name as USER_FULL_NAME, lib_designation.custom_designation as CUSTOM_DESIGNATION, to_char(approval_history.approved_date, 'dd-mm-yyyy hh:mi:ss am') as APPROVED_DATE from approval_history inner join wo_booking_mst a on a.id = approval_history.mst_id left join user_passwd on user_passwd.id = approval_history.approved_by left join lib_designation on lib_designation.id = user_passwd.designation where approval_history.mst_id = $mst_id and approval_history.entry_form = 13 and a.is_approved = 1 and approval_history.current_approval_status = 1 ");
	// and approval_history.sequence_no =".max($electronic_sequence_arr)
	?>

	<table id="signatureTblId" width="901.5" style="padding-top:70px;">
		<tr>
			<td style="text-align: center; font-size: 18px;" width="230">
				<strong><?=$user_full_name?></strong>
				<br>
				<strong><?=$custom_designation?></strong>
				<br>
				<?=$insert_date?>
			</td>
			<!-- <td width="95"></td>
			<td style="text-align: center; font-size: 18px;" width="230">
				<strong><?=isset($sql_get_checked_user[0]) ? $sql_get_checked_user[0]['USER_FULL_NAME'] : ''?></strong>
				<br>
				<strong><?=$sql_get_checked_user[0]['CUSTOM_DESIGNATION']?></strong>
				<br>
				<?= isset($sql_get_checked_user[0]) ? $sql_get_checked_user[0]['APPROVED_DATE'] : ""?>
			</td> -->
			<td width="75"></td>
			<td width="95"></td>
			<td style="text-align: center; font-size: 18px;" width="230">
				<strong><?=isset($sql_get_approved_user[0]) ? $sql_get_approved_user[0]['USER_FULL_NAME'] : ''?></strong>
				<br>
				<strong><?=$sql_get_approved_user[0]['CUSTOM_DESIGNATION']?></strong>
				<br>
				<?= isset($sql_get_approved_user[0]) ? $sql_get_approved_user[0]['APPROVED_DATE'] : ""?>
			</td>
		</tr>
		<tr>
			<td style="text-align: center; font-size:18px; border-top:1px solid;"><strong>Prepared by</strong></td>
			<!-- <td width="75"></td>
			<td style="text-align: center; font-size:18px; border-top:1px solid;"><strong>Checked by</strong></td> -->
			<td width="75"></td>
			<td width="95"></td>
			<td style="text-align: center; font-size:18px; border-top:1px solid;"><strong>Approved by</strong></td>
		</tr>
	</table>
 
          <?
		 	//echo signature_table(5, $cbo_company_name, "1330px");
			//echo "****".custom_file_name($varcode_booking_no,$style_sting,'');
		  ?>
       </div>
		<script type="text/javascript" src="../../js/jquery.js"></script>
        <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
        <script>
        fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
        </script>
       <?
      
}

if($action=="show_fabric_booking_report4-bk") 
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$txt_requisition=str_replace("'","",$txt_requisition);
	$txt_supplier_name=str_replace("'","",$txt_supplier_name); 
	

	$process_loss_method=return_field_value("process_loss_method", "variable_order_tracking", "company_name='$cbo_company_name'  and variable_list=18 and item_category_id=2 and status_active=1 and is_deleted=0");


	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	if ($db_type == 0) $select_group_row = " order by master_tble_id desc limit 0,3";
	else if ($db_type == 2) $select_group_row = " and  rownum<=4 order by id desc";
	$imge_arr_for_book=sql_select( "select master_tble_id,image_location,real_file_name from   common_photo_library where  master_tble_id=$txt_booking_no and file_type=1  $select_group_row ");
	
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$com_supplier_id=return_field_value( "supplier_id as supplier_id", "wo_non_ord_samp_booking_mst","booking_no=$txt_booking_no","supplier_id");

	$nameArray_approved=sql_select( "select max(b.approved_no) as approved_no from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and a.booking_no=$txt_booking_no and b.entry_form=9"); 
	list($nameArray_approved_row)=$nameArray_approved;
	$nameArray_approved_date=sql_select( "select b.approved_date as approved_date from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and a.booking_no=$txt_booking_no and b.entry_form=9 and b.approved_no='".$nameArray_approved_row[csf('approved_no')]."'");
	list($nameArray_approved_date_row)=$nameArray_approved_date;
	 $fabric_source_new=array(1=>"Production",2=>"Purchase",3=>"Buyer Supplied",4=>"Stock");

	?>
	<div style="width:1330px; font-family:'Arial Narrow';font-style: normal;font-variant: normal;font-weight: 400;
	line-height: 20px;" align="center">       
    										<!--    Header Company Information         --> 
       <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black" >
           <tr>
               <td width="100"> 
               <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               </td>
               <td width="1000">                                     
                    <table width="100%" cellpadding="0" cellspacing="0" >
                        <tr>
                            <td align="center" style="font-size:24px;">
                            <strong>
                              <?php      
                                    echo $company_library[$cbo_company_name];
                              ?>
                              </strong>
                            
						   </td>
                            
                            <td rowspan="3" width="">
							<?
                             if($nameArray_approved_row[csf('approved_no')]>1)
                             {
                             ?>
                             <b> Revised No: <? echo $nameArray_approved_row[csf('approved_no')]-1; ?></b>
                              <br/>
                              Approved Date: <? echo $nameArray_approved_date_row[csf('approved_date')]; ?>
                              <?
                             }
                            ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px">  
                            <?
                            $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name"); 
                            foreach ($nameArray as $result)
                            { 
                            ?>
                                           <? echo $result[csf('plot_no')].'&nbsp;'; ?> 
                                            <? echo $result[csf('level_no')].'&nbsp;' ?>
                                            <? echo $result[csf('road_no')].'&nbsp;'; ?> 
                                            <? echo $result[csf('block_no')].'&nbsp;';?> 
                                            <? echo $result[csf('city')].'&nbsp;';?> 
                                            <? echo $result[csf('zip_code')].'&nbsp;'; ?> 
                                             <?php echo $result[csf('province')].'&nbsp;';?> 
                                           <? echo $country_arr[$result[csf('country_id')]].'&nbsp;'; ?><br> 
                                            <? echo $result[csf('email')];?> 
                                             <? echo $result[csf('website')];
                            }
                                            ?>   
                          
                               </td> 
                            </tr>
                            <tr>
                             
                            <td align="center" style="font-size:20px;"> <strong style="margin-left:77px;"><? if($report_title !=""){echo $report_title;} else {echo "Sample Fabric Booking -Without order";}?> &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="r:#F00"><? if(str_replace("'","",$id_approved_id) ==1){ echo "(Approved)";}else{echo "";}; ?> </font></strong>
                             <div style="margin-left:854px; margin-top:-75px; position: absolute; float:right;  ">
	                            <?
								foreach($imge_arr_for_book as $row)
								{
									?>
	                                <img  src='../../<? echo $row[csf('image_location')]; ?>' height='80' width='80' />
									<?
								}
	
								?>
                            </div>
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
				$season="";
				$req_no="";
				$buyer_req_no="";$bh_merchant="";$style_ref_no="";$product_code="";$product_department="";
				
				$nameseason=sql_select( "SELECT a.season as season_buyer_wise, b.buyer_req_no,a.bh_merchant,a.style_ref_no,a.product_code,a.product_dept,a.requisition_number_prefix_num  from  sample_development_mst a, sample_development_dtls b, wo_booking_dtls c  where  a.id=b.sample_mst_id and a.id=c.style_id and c.booking_no=$txt_booking_no and b.status_active=1 and   b.is_deleted=0 and c.status_active=1 and   c.is_deleted=0 ");
				foreach ($nameseason as $season_row)
				{
					$season=$season_row[csf('season_buyer_wise')];
					$buyer_req_no=$season_row[csf('buyer_req_no')];
					$bh_merchant=$season_row[csf('bh_merchant')];
					$product_code=$season_row[csf('product_code')];
					$product_department=$product_dept[$season_row[csf('product_dept')]];
					$req_no=$season_row[csf('requisition_number_prefix_num')];	
				}
				unset($nameseason);

				$nameStyleArray=sql_select( "SELECT  b.style_ref_no  from wo_booking_mst a, wo_po_details_master b where  a.job_no=b.job_no and a.booking_no=$txt_booking_no and a.company_id='$cbo_company_name' and b.company_name='$cbo_company_name' and a.status_active=1 and   a.is_deleted=0 and b.status_active=1 and   b.is_deleted=0"); 
				foreach ($nameStyleArray as $style_row)
				{
					$style_ref_no=$style_row[csf('style_ref_no')];
				}
				unset($nameStyleArray);

				$fabric_source='';
				$season_library=return_library_array( "SELECT id,season_name from lib_buyer_season", "id", "season_name");
                $nameArray=sql_select( "SELECT buyer_id, fabric_source, booking_no, job_no,  pay_mode, booking_date, internal_ref_no, supplier_id, currency_id, exchange_rate, attention, delivery_date, fabric_source, team_leader, dealing_marchant from wo_booking_mst  where  booking_no=$txt_booking_no");
				foreach ($nameArray as $result)
				{
					$fabric_source_id=$result[csf('fabric_source')];
					$varcode_booking_no=$result[csf('booking_no')];
				?>
       <table width="100%" style="border:1px solid black">                    	
            <tr>
                <td colspan="6" valign="top"></td>                             
            </tr>                                                
            <tr>
                <td width="100" style="font-size:18px"><b>Booking No</b></td>
                <td width="110" style="font-size:18px">:&nbsp;<b><? echo $result[csf('booking_no')];?></b> </td>
                <td width="100" style="font-size:12px"><b>Booking Date</b></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('booking_date')],'dd-mm-yyyy','-');?>&nbsp;&nbsp;&nbsp;</td>		
                <td width="100"><span style="font-size:12px"><b>Delivery Date</b></span></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>
                
                <td width="100"><span style="font-size:12px"><b>Job No :</b></span></td>
                <td width="110">&nbsp;<b><? echo $result[csf('job_no')];$job_no= $result[csf('job_no')];?></b> </td>
            </tr>
            <tr>
                <td width="100"><span style="font-size:12px"><b>Buyer/Agent Name</b></span></td>
                <td width="110">:&nbsp;<? echo $buyer_name_arr[$result[csf('buyer_id')]]; ?></td>
                <td width="100" style="font-size:12px"><b>Supplier Name</b>   </td>
                <td width="110" style="font-size:12px">:&nbsp; <b><? 
				echo $txt_supplier_name;?></b></td>
                <td width="100" style="font-size:12px"><b>Supplier Address</b></td>
               	<td width="110">:&nbsp;
                
				<? 
				if($result[csf('pay_mode')]==5 || $result[csf('pay_mode')]==3 ){
				$comAdd=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=".$result[csf('supplier_id')]); 
					foreach ($comAdd as $comAddRow){ 
						echo $comAddRow[csf('plot_no')].'&nbsp;'; 
						echo $comAddRow[csf('level_no')].'&nbsp;' ;
						echo $comAddRow[csf('road_no')].'&nbsp;'; 
						echo $comAddRow[csf('block_no')].'&nbsp;';
						echo $comAddRow[csf('city')].'&nbsp;';
						 
					}
				}
				else{
					echo $supplier_address_arr[$result[csf('supplier_id')]];
				}
				?>
                </td> 
                <td width="100" style="font-size:12px"><b>Department Name</b></td>
               	<td width="110">:&nbsp;<? echo $product_department;?></td> 
            </tr>
             <tr>
                <td width="100" style="font-size:12px"><b>Currency</b></td>
                <td width="110">:&nbsp;<? echo $currency[$result[csf('currency_id')]]; ?></td>
                <td  width="100" style="font-size:12px"><b>Conversion Rate</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('exchange_rate')]; ?></td>
                <td  width="100" style="font-size:12px"><b>Attention</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('attention')]; ?></td>
                <td  width="100" style="font-size:12px"><b>Department No</b></td>
                <td  width="110" >:&nbsp;<? echo $product_code; ?></td>
            </tr> 
            <tr>
                <td width="100" style="font-size:12px"><b>Season</b></td>
                <td width="110">:&nbsp;<? echo $season_library[$season]; ?></td>
                <td  width="100" style="font-size:12px"><b>Buyer Req. No</b></td>
                <td  width="110" >:&nbsp;<? echo $buyer_req_no; ?></td>
                <td  width="100" style="font-size:12px"><b>Dealing Merchant</b></td>
                <td  width="110" >:&nbsp;<? echo $marchentrArr[$result[csf('dealing_marchant')]]; ?></td>
                <td  width="100" style="font-size:12px"><b>Buying Merchant Name</b></td>
                <td  width="110" >:&nbsp;<? echo $bh_merchant; ?></td>
            </tr> 
             <tr>
             	<td width="100" style="font-size:12px"><b>Fabric Source</b></td>
             	<td width="110">:&nbsp;<? echo $fabric_source_new[$fabric_source_id]; ?></td>
             	<td  width="100" style="font-size:12px"><b>Req. No</b></td>
             	<td  width="110" >:&nbsp;<? echo $txt_requisition; ?></td>
             	<td  width="100" style="font-size:12px"><b>Style Ref.</b></td>
             	<td  width="110" >:&nbsp;<? echo $style_ref_no ; ?></td>
             	<td  width="100" style="font-size:12px"><b>Internal Ref.</b></td>
             	<td  width="110" >:&nbsp;<strong><? echo $result[csf('internal_ref_no')] ; ?></strong></td>
              </tr>
        </table>  
        <?
			}
		?>
      <br/>
      <? 
    $sample_library=return_library_array( "select id,sample_name from lib_sample", "id", "sample_name"  );
	$color_lib=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$size_lib=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );	 
	
	$po_no_arr=return_library_array( "SELECT a.id,a.po_number from wo_po_break_down a,wo_booking_dtls b where a.id=b.po_break_down_id and b.booking_no=$txt_booking_no", "id", "po_number" );
	 $sql_dtls="select a.po_break_down_id, b.body_part_id,a.style_id,a.sample_type,a.body_part, b.color_type_id, b.construction, b.composition, b.gsm_weight,a.gmts_color_id, a.fabric_color_id, a.item_size, a.dia_width, a.fin_fab_qnty, a.process_loss_percent, a.uom, a.grey_fab_qnty, a.rate, a.amount, a.id, a.pre_cost_fabric_cost_dtls_id,a.remark,a.additional_process,a.fabric_source,a.fabric_description,a.gmt_item as gmts_item_id,b.width_dia_type FROM wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b WHERE a.pre_cost_fabric_cost_dtls_id=b.id and a.booking_no=$txt_booking_no and a.is_short=2 and a.status_active=1 and	a.is_deleted=0";
	 //echo $sql_dtls;//die;
	$sample_result=sql_select($sql_dtls);$style_id='';
	foreach($sample_result as $row)
	{
		$data_array_color_wise[$row[csf("uom")]][$row[csf("sample_type")]][$row[csf("gmts_item_id")]][$row[csf("gmts_color_id")]][$row[csf("body_part_id")]][$row[csf("fabric_description")]][$row[csf("gsm_weight")]]["finish_fabric"]+=$row[csf("fin_fab_qnty")];
		$data_array_color_wise[$row[csf("uom")]][$row[csf("sample_type")]][$row[csf("gmts_item_id")]][$row[csf("gmts_color_id")]][$row[csf("body_part_id")]][$row[csf("fabric_description")]][$row[csf("gsm_weight")]]["uom"]=$row[csf("uom")];
		$data_array_color_wise[$row[csf("uom")]][$row[csf("sample_type")]][$row[csf("gmts_item_id")]][$row[csf("gmts_color_id")]][$row[csf("body_part_id")]][$row[csf("fabric_description")]][$row[csf("gsm_weight")]]["req_dzn"]+=$row[csf("req_dzn")];
		$data_array_color_wise[$row[csf("uom")]][$row[csf("sample_type")]][$row[csf("gmts_item_id")]][$row[csf("gmts_color_id")]][$row[csf("body_part_id")]][$row[csf("fabric_description")]][$row[csf("gsm_weight")]]["fabric_description"]=$row[csf("fabric_description")];
		$data_array_color_wise[$row[csf("uom")]][$row[csf("sample_type")]][$row[csf("gmts_item_id")]][$row[csf("gmts_color_id")]][$row[csf("body_part_id")]][$row[csf("fabric_description")]][$row[csf("gsm_weight")]]["composition"]=$row[csf("composition")];
		$data_array_color_wise[$row[csf("uom")]][$row[csf("sample_type")]][$row[csf("gmts_item_id")]][$row[csf("gmts_color_id")]][$row[csf("body_part_id")]][$row[csf("fabric_description")]][$row[csf("gsm_weight")]]["construction"]=$row[csf("construction")];
		$data_array_color_wise[$row[csf("uom")]][$row[csf("sample_type")]][$row[csf("gmts_item_id")]][$row[csf("gmts_color_id")]][$row[csf("body_part_id")]][$row[csf("fabric_description")]][$row[csf("gsm_weight")]]["fabdec"]=$row[csf("construction")].','.$row[csf("composition")];
		$data_array_color_wise[$row[csf("uom")]][$row[csf("sample_type")]][$row[csf("gmts_item_id")]][$row[csf("gmts_color_id")]][$row[csf("body_part_id")]][$row[csf("fabric_description")]][$row[csf("gsm_weight")]]["color_type_id"]=$row[csf("color_type_id")];
		$data_array_color_wise[$row[csf("uom")]][$row[csf("sample_type")]][$row[csf("gmts_item_id")]][$row[csf("gmts_color_id")]][$row[csf("body_part_id")]][$row[csf("fabric_description")]][$row[csf("gsm_weight")]]["dia_width"]=$row[csf("dia_width")];
		$data_array_color_wise[$row[csf("uom")]][$row[csf("sample_type")]][$row[csf("gmts_item_id")]][$row[csf("gmts_color_id")]][$row[csf("body_part_id")]][$row[csf("fabric_description")]][$row[csf("gsm_weight")]]["dia"]=$row[csf("dia")];
		$data_array_color_wise[$row[csf("uom")]][$row[csf("sample_type")]][$row[csf("gmts_item_id")]][$row[csf("gmts_color_id")]][$row[csf("body_part_id")]][$row[csf("fabric_description")]][$row[csf("gsm_weight")]]["width_dia_type"]=$row[csf("width_dia_type")];
		$data_array_color_wise[$row[csf("uom")]][$row[csf("sample_type")]][$row[csf("gmts_item_id")]][$row[csf("gmts_color_id")]][$row[csf("body_part_id")]][$row[csf("fabric_description")]][$row[csf("gsm_weight")]]["gsm_weight"]=$row[csf("gsm_weight")];
		$data_array_color_wise[$row[csf("uom")]][$row[csf("sample_type")]][$row[csf("gmts_item_id")]][$row[csf("gmts_color_id")]][$row[csf("body_part_id")]][$row[csf("fabric_description")]][$row[csf("gsm_weight")]]["process_loss_percent"]=$row[csf("process_loss_percent")];
		$data_array_color_wise[$row[csf("uom")]][$row[csf("sample_type")]][$row[csf("gmts_item_id")]][$row[csf("gmts_color_id")]][$row[csf("body_part_id")]][$row[csf("fabric_description")]][$row[csf("gsm_weight")]]["remark"]=$row[csf("remark")];
		$data_array_color_wise[$row[csf("uom")]][$row[csf("sample_type")]][$row[csf("gmts_item_id")]][$row[csf("gmts_color_id")]][$row[csf("body_part_id")]][$row[csf("fabric_description")]][$row[csf("gsm_weight")]]["additional_process"]=$row[csf("additional_process")];
		$data_array_color_wise[$row[csf("uom")]][$row[csf("sample_type")]][$row[csf("gmts_item_id")]][$row[csf("gmts_color_id")]][$row[csf("body_part_id")]][$row[csf("fabric_description")]][$row[csf("gsm_weight")]]["fabric_color"]=$row[csf("fabric_color_id")];
		$data_array_color_wise[$row[csf("uom")]][$row[csf("sample_type")]][$row[csf("gmts_item_id")]][$row[csf("gmts_color_id")]][$row[csf("body_part_id")]][$row[csf("fabric_description")]][$row[csf("gsm_weight")]]["po_id"]=$row[csf("po_break_down_id")]
		;$data_array_color_wise[$row[csf("uom")]][$row[csf("sample_type")]][$row[csf("gmts_item_id")]][$row[csf("gmts_color_id")]][$row[csf("body_part_id")]][$row[csf("fabric_description")]][$row[csf("gsm_weight")]]["item_size"]=$row[csf("item_size")];
		$data_array_color_wise[$row[csf("uom")]][$row[csf("sample_type")]][$row[csf("gmts_item_id")]][$row[csf("gmts_color_id")]][$row[csf("body_part_id")]][$row[csf("fabric_description")]][$row[csf("gsm_weight")]]["amount"]+=$row[csf("amount")];
		$data_array_color_wise[$row[csf("uom")]][$row[csf("sample_type")]][$row[csf("gmts_item_id")]][$row[csf("gmts_color_id")]][$row[csf("body_part_id")]][$row[csf("fabric_description")]][$row[csf("gsm_weight")]]["rate"]=$row[csf("rate")];
		$data_uom_wise[$row[csf("uom")]]=$row[csf("uom")];
		$style_id.=$row[csf("style_id")].',';
		
	}

//print_r($data_uom_wise);
	  
	foreach($data_array_color_wise as $uom_id=>$uom_data)
	{
	 foreach($uom_data as $sample_type=>$gmts_data)
	 {
		
		foreach($gmts_data as $gmts_item_id=>$gmts_color_data)
		{	
			
			foreach($gmts_color_data as $gmts_color_id=>$body_part_data)
			{	
				$sample_span=0;
				foreach($body_part_data as $body_part_id=>$dtm_data)
				{
					
					foreach($dtm_data as $dtm_id=>$gsm_data)
					{
						
						foreach($gsm_data as $gsm_id=>$row)
						{
							
							$sample_span++;
						}
						
					}
					$sample_item_wise_span[$uom_id][$sample_type][$gmts_item_id][$gmts_color_id]=$sample_span;
					
				}
				
				
			}
			
		  }

		}
	}
	/*echo "<pre>";
	print_r($sample_item_wise_span);die;*/
	
	 

	$sample_mst_id=$sample_result[0][csf("style_id")];
	
	$sql_sample_dtls= "SELECT a.sample_name,a.article_no,b.color_name  from sample_development_dtls a , lib_color b  where a.status_active=1 and a.is_deleted=0 and a.entry_form_id=13  and sample_mst_id=$sample_mst_id and b.status_active=1 and b.id=a.sample_color  group by a.sample_name,a.article_no,b.color_name";
  //echo $sql_sample_dtls;//die;
	foreach(sql_select($sql_sample_dtls) as $key=>$value)
	{
		if($sample_wise_article_no[$value[csf("sample_name")]][$value[csf("color_name")]]=="")
		{
			$sample_wise_article_no[$value[csf("sample_name")]][$value[csf("color_name")]]=$value[csf("article_no")];
		}
		else
		{
			if(!in_array($value[csf("article_no")], $sample_wise_article_no))
			{
				$sample_wise_article_no[$value[csf("sample_name")]][$value[csf("color_name")]].= ','.$value[csf("article_no")];
			}
			
		}
		
	}

	$sample_dtls_sql="SELECT sample_name,gmts_item_id,sample_color,sample_prod_qty from sample_development_dtls where status_active=1 and is_deleted=0 and sample_mst_id=$sample_mst_id and entry_form_id=117";
	 foreach(sql_select($sample_dtls_sql) as $vals)
	 {
	 	$sample_dtls_array[$vals[csf("sample_name")]][$vals[csf("gmts_item_id")]][$vals[csf("sample_color")]]+=$vals[csf("sample_prod_qty")];

	 }

	
	foreach($data_array_color_wise as $uom_id=>$data_array_color_wise)
	{
	?>
    <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
    <caption><? echo $unit_of_measurement[$uom_id];?> </caption>
        <thead>
            <tr>
				<th width="30">Sl</th>               
				<th width="90">PO No</th>
				<th width="90">Article No</th>
				<th width="110">Sample</th>
				<th width="80"> Gmts Color</th>               
				<th width="120">Body Part</th>
				<th width="200">Fabric Details and Composition</th>
				<th width="80">Color Type</th>
				<th width="80">Fab.Color</th>
				<th width="40">Item Size</th>
				<th width="40">GSM</th>
				<th width="55">Dia</th>
				<th width="80">Dia Type</th>
				<th width="80">Fin Fab Qnty</th>
				<th width="40">P. Loss</th>
				<th width="60">Grey Qty</th>
				<th width="40">UOM</th>
                <?
                if($show_comment==1)
				{
				?>
                <th width="40">Rate</th>
                <th width="50">Amount</th>
                <?
				}
				?>
				<th>Additional Process</th>
				<th>Remarks</th>                 
            </tr>
        </thead>
        <tbody>
        <?
        $p=1;
        $total_finish=0;
        $total_grey=0;
        $total_process=0;

        foreach($data_array_color_wise as $sample_type=>$gmts_data)
        {

        	foreach($gmts_data as $gmts_item_id=>$gmts_color_data)
        	{	
        		foreach($gmts_color_data as $gmts_color_id=>$body_part_data)
        		{
        			$i=0;
        			foreach($body_part_data as $body_part_id=>$dtm_data)
        			{	
        				
        				foreach($dtm_data as $dtm_id=>$gsm_data)
        				{
        					
        					foreach($gsm_data as $gsm_id=>$value)
        					{
        						$txt_finish_qnty=$value["finish_fabric"];//($value["req_dzn"]/12)*$sample_dtls_array[$sample_type][$gmts_item_id][$value["gmts_color_id"]];
        						$processloss=$value["process_loss_percent"]; 
        						$WastageQty='';
        						if($process_loss_method==1)
        						{
        							$WastageQty=$txt_finish_qnty+$txt_finish_qnty*($processloss/100);
        						}
        						else if($process_loss_method==2)
        						{
        							$devided_val = 1-($processloss/100);
        							$WastageQty=$txt_finish_qnty/$devided_val;
        						}
        						else
        						{
        							$WastageQty=0;
        						}
								if($uom_id==$value["uom"])
								{
        						?>
        						<tr>

        							<?
        							if($i==0)
        							{
        								?>
        								<td width="30" rowspan="<? echo $sample_item_wise_span[$uom_id][$sample_type][$gmts_item_id][$gmts_color_id];?>" align="center" style="word-wrap: break-word;word-break: break-all;"><? echo $p;$p++;?></td>
										<td width="90" rowspan="<? echo $sample_item_wise_span[$uom_id][$sample_type][$gmts_item_id][$gmts_color_id];?>"  align="center"><? echo $po_no_arr[$value["po_id"]];?></td>
        								<td width="90" rowspan="<? echo $sample_item_wise_span[$uom_id][$sample_type][$gmts_item_id][$gmts_color_id];?>"  align="center"><? echo $sample_wise_article_no[$sample_type][$gmts_color_id];?></td>
        								<td width="110" rowspan="<? echo $sample_item_wise_span[$uom_id][$sample_type][$gmts_item_id][$gmts_color_id];?>"  align="center"><? echo $sample_library[$sample_type]; ?></td>
        								<td width="80"  align="center" rowspan="<? echo $sample_item_wise_span[$uom_id][$sample_type][$gmts_item_id][$gmts_color_id];?>"><? echo $color_lib[$gmts_color_id];?> </td>
        								<?
        							}	
        							$i++;
        							?>          						             
        						<td width="120"     align="center"><? echo $body_part[$body_part_id];?></td>
        						<td width="200"  align="center"><? echo $value["fabdec"]. ",GSM ".$value["gsm_weight"];?></td>
        						<td width="80"  align="center"> <? echo $color_type[$value["color_type_id"]]; ?></td>
        						<td width="80"  align="center"><? echo $color_lib[$value["fabric_color"]]; ?></td>
        						<td width="40"  align="center"><? echo $value["item_size"]; ?></td>
        						<td width="40"  align="center"><? echo $value["gsm_weight"]; ?></td>
        						<td width="55"  align="center"><? echo $value["dia_width"]; ?></td>
        						<td width="80"  align="center"><? echo $fabric_typee[$value["width_dia_type"]]; ?></td>
        						<td width="80" align="right"><? echo number_format($txt_finish_qnty,2);?></td>
        						<td width="40" align="right"><? echo $value["process_loss_percent"];?></td>
        						<td width="60" align="right"><? echo number_format($WastageQty,2);?></td>
        						<td width="40"  align="center"><? echo $unit_of_measurement[$value["uom"]];?></td>
                                 <?
								if($show_comment==1)
								{
								?>
                                <td width="40"  align="right"><? echo $value["rate"]; ?></td>
                                <td width="50"  align="right"><? echo number_format($value["amount"],4); ?></td>
                                <?
								}
								?>
								<td><p><? echo  $value["additional_process"];?> </p></td> 
        						<td><p><? echo  $value["remark"];?> </p></td>    
        					</tr>
        					<?
        					//$i++;
        					$total_finish +=$txt_finish_qnty;
							$total_amount +=$value["amount"];
							//$total_finish +=$txt_finish_qnty;
        					$total_grey +=$WastageQty;
        					$total_process +=$value["process_loss"];
								}
        				}
        			}
        		}
        	}
        }

    }        

        ?>

       			<tr>
					<th colspan="13" align="right"><b>Total</b></th>
					<th width="80" align="right"><? echo number_format($total_finish,2);?></th>					
					<th width="40" align="right">&nbsp;</th>
					<th width="60" align="right"><? echo number_format($total_grey,2);?></th>
                    <th width="40" align="right">&nbsp;</th>
                     <?
					if($show_comment==1)
					{
					?>
                    <th width="40" align="right">&nbsp;</th>					
					<th width="50" align="right"> <? echo number_format($total_amount,4);?></th>
                    <?
					}
					?>
					<th width=""> </th>
                    <th width=""> </th>
					                  
	            </tr>
		
        </tbody>
        
           
        
    </table>
     <br/>
    <?
	}
	?>
    <br/>

	<table width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" style="font-family:Arial Narrow;">
	    		<caption> <strong style="float:left"> Stripe Details</strong></caption>
	    		<?
	    		$color_name_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	    		$sql_stripe=("select c.id,c.composition,c.construction,c.body_part_id,c.fabric_description,c.gsm_weight,c.color_type_id,b.grey_fab_qnty as fab_qty,b.dia_width,d.color_number_id as color_number_id,d.id as did,d.stripe_color,d.fabreqtotkg as fabreqtotkg ,d.measurement as measurement ,d.yarn_dyed,d.uom  from wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c,wo_pre_stripe_color d where c.id=b.pre_cost_fabric_cost_dtls_id and c.job_no=b.job_no and d.pre_cost_fabric_cost_dtls_id=c.id and d.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and b.job_no=d.job_no and b.job_no=$txt_job_no  and d.job_no=$txt_job_no and b.booking_no=$txt_booking_no  and c.color_type_id in (2,3,4,6,32,33,34)  and b.status_active=1  and c.is_deleted=0 and c.status_active=1  and d.is_deleted=0 and d.status_active=1  and 	b.is_deleted=0  group by c.id,
				c.composition,c.construction,c.body_part_id,c.fabric_description,c.gsm_weight,c.color_type_id,b.grey_fab_qnty,b.dia_width,d.color_number_id,d.id,d.stripe_color,d.fabreqtotkg,d.measurement,d.yarn_dyed,d.uom");
	    		$result_data=sql_select($sql_stripe);
	    		$bodypart_wise_grey_qnty=array();
	    		foreach($result_data as $row)
	    		{
	    			$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['stripe_color'][$row[csf('did')]]=$row[csf('stripe_color')];
	    			$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['measurement'][$row[csf('did')]]=$row[csf('measurement')];
	    			$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['uom'][$row[csf('did')]]=$row[csf('uom')];
	    			$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['fabreqtotkg'][$row[csf('did')]]=$row[csf('fabreqtotkg')];
	    			$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['fab_qty'][$row[csf('did')]]=$row[csf('fab_qty')];
	    			$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['yarn_dyed'][$row[csf('did')]]=$row[csf('yarn_dyed')];
	    			$bodypart_wise_grey_qnty[$row[csf('body_part_id')]][$row[csf('color_number_id')]]=$row[csf('fab_qty')];

	    			$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['composition']=$row[csf('composition')];
	    			$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['construction']=$row[csf('construction')];
	    			$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['gsm_weight']=$row[csf('gsm_weight')];
	    			$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['color_type_id']=$row[csf('color_type_id')];
	    			$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['dia_width']=$row[csf('dia_width')];
	    			$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['fabreqtotkg']+=$row[csf('fabreqtotkg')];
	    		}
	    		?>

	    			<tr>
	    				<th width="30"> SL</th>
	    				<th width="100"> Body Part</th>
	    				<th width="80"> Fabric Color</th>
	    				<th width="70"> Fabric Qty(KG)</th>
	    				<th width="70"> Stripe Color</th>
	    				<th width="70"> Stripe Measurement</th>
	    				<th width="70"> Stripe Uom</th>
	    				<th  width="70"> Qty.(KG)</th>
	    				<th  width="70"> Y/D Req.</th>
	    			</tr>
	    			<?	    			
	    			$i=1;$total_fab_qty=0;$total_fabreqtotkg=0;$fab_data_array=array();
	    			foreach($stripe_arr as $body_id=>$body_data)
	    			{
	    				foreach($body_data as $color_id=>$color_val)
	    				{
	    					$rowspan=count($color_val['stripe_color']);
	    					$composition=$stripe_arr2[$body_id][$color_id]['composition'];
	    					$construction=$stripe_arr2[$body_id][$color_id]['construction'];
	    					$gsm_weight=$stripe_arr2[$body_id][$color_id]['gsm_weight'];
	    					$color_type_id=$stripe_arr2[$body_id][$color_id]['color_type_id'];
	    					$dia_width=$stripe_arr2[$body_id][$color_id]['dia_width'];

	    					?>
	    					<tr>
	    					<?
	    					$color_qty= array_sum($stripe_arr[$body_id][$color_id]['fabreqtotkg']);
	    					$grey_fab=$bodypart_wise_grey_qnty[$body_id][$color_id];
	    					?>
	    					<td rowspan="<? echo $rowspan;?>"> <? echo $i; ?></td>
	    					<td rowspan="<? echo $rowspan;?>"> <? echo $body_part[$body_id]; ?></td>
	    					<td rowspan="<? echo $rowspan;?>"> <? echo $color_name_arr[$color_id]; ?></td>
	    					<td rowspan="<? echo $rowspan;?>" align="right"> <? echo number_format($grey_fab,2) //number_format($color_qty,2); ?></td>
	    					<?
	    					$total_fab_qty+=$grey_fab;
	    					
	    					$measure=0;
	    					foreach($color_val['stripe_color'] as $strip_color_id=>$s_color_val)
	    					{
	    						$measurement=$color_val['measurement'][$strip_color_id];
	    						$measure+=fn_number_format($measurement,2,".","");
	    					}
	    					foreach($color_val['stripe_color'] as $strip_color_id=>$s_color_val)
	    					{
	    						$measurement=$color_val['measurement'][$strip_color_id];
	    						$uom=$color_val['uom'][$strip_color_id];
	    						$fabreqtotkg=$color_val['fabreqtotkg'][$strip_color_id];
	    						$yarn_dyed=$color_val['yarn_dyed'][$strip_color_id];
	    						?>
	    						<td><?  echo  $color_name_arr[$s_color_val]; ?></td>
	    						<td align="right"> <? echo  number_format($measurement,2); ?></td>
	    						<td> <? echo  $unit_of_measurement[$uom]; ?></td>
	    						<td align="right"> <? echo number_format(($grey_fab/$measure)*$measurement,2)  //number_format($fabreqtotkg,2); ?></td>
	    						
	    						<td> <? echo  $yes_no[$yarn_dyed]; ?></td>
	    						</tr>
	    						<?
	    						//$total_fabreqtotkg+=$fabreqtotkg;
	    						$total_fabreqtotkg+=(($grey_fab/$measure)*$measurement);
	    					}
	    					$i++;
	    				}
	    			}
	    			?>
	    			<tfoot>
	    				<tr>
	    					<td colspan="3">Total </td>
	    					<td align="right">  <? echo  number_format($total_fab_qty,2); ?> </td>
	    					<td></td>
	    					<td></td>
	    					<td>   </td>
	    					<td align="right"><? echo  number_format($total_fabreqtotkg,2); ?> </td>
	    				</tr>
	    			</tfoot>
	    </table>
	
	<?
	$lib_item_group_arr=array();
    		$itemArray=sql_select( "select item_name,trim_uom,id from lib_item_group where item_category=4 and is_deleted=0  and  status_active=1 order by item_name" );
    		foreach ($itemArray as $row)
    		{    			 
    			$lib_item_group_arr[$row[csf('id')]]=$row[csf('item_name')];
    		}

    		$data_array=sql_select("SELECT booking_no, item_group_id, description, uom, qty,   remarks from wo_booking_accessories_dtls where  booking_no=$txt_booking_no"); 
    		if ( count($data_array)>0)
    		{
				?>
    <table class="rpt_table" width="800" align="left" border="1" cellpadding="0" cellspacing="0" rules="all">
    	<thead>
    		<tr>
    			<th align="center" width="40">Sl</th>
    			<th align="center" width="130" >Item Group</th>
    			<th align="center" width="100" >Description</th>
    			<th align="center" width="100" >UOM</th>
    			<th align="center" width="100" >Qty</th>
    			<th align="center"   >Remarks</th>
    		</tr>
    	</thead>
    	<tbody>
    		<?  
    		
    			$l=1;
    			$tot_qnty=0;
    			foreach( $data_array as $key=>$row )
    			{

    				?>
    				<tr>
    					<td  align="center"> <? echo $l;?> </td>
    					<td  align="center"> <? echo $lib_item_group_arr[$row[csf("item_group_id")]]; ?> </td>
    					<td  align="center"> <? echo $row[csf("description")]; ?> </td>
    					<td  align="center"> <? echo $unit_of_measurement[$row[csf("uom")]]; ?> </td>
    					<td  align="center"> <? echo $qnty=$row[csf("qty")]; ?> </td>
    					<td  align="center"> <? echo $row[csf("remarks")]; ?> </td>
    				</tr>
    				<?
    				$l++;
    				$tot_qnty+=$qnty;
    			}
    			?>
    			<tr>   
    				<td colspan="4" align="right"><strong>Grand Total</strong> </td> 					
    				<td  align="center"> <? echo $tot_qnty; ?> </td>
    				<td  align="center">  </td>
    			</tr>
    				<?
    		}

    		?>
    	</tbody>
    </table>
    <table class="rpt_table"  style="margin-top: 10px;" width="800" align="left" border="1" cellpadding="0" cellspacing="0" rules="all">
    	<thead>
    		<tr>
    			<th align="center" width="40">Sl</th>
    			<th align="center" width="120" >Size Name</th>
    			<th align="center" width="100" >BH Qty</th>
    			<th align="center" width="100" >RH Qty</th>
    			<th align="center" width="100" >Dyeing </th>
				<th align="center" width="100" >Test </th>
				<th align="center" width="100" >Self </th>
    			<th align="center"   >Total</th>
    		</tr>
    	</thead>
    	<tbody>
    		<?
    		//sample_development_mst
			 $sql_size="select a.id,a.gmts_size,a.bh_qty,a.rf_qty from wo_booking_dtls a where a.booking_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0";
			$size_result=sql_select($sql_size);

    		if ( count($size_result)>0)
    		{
    			$l=1;
    			$tot_plan_qnty=$tot_bh_qty=$tot_dyeing_qty=$tot_test_qty=$tot_self_qty=$tot_rf_qty=$tot_total_qty=0;
    			foreach( $size_result as $row )
    			{

    				?>
    				<tr>
    					<td  align="center"> <? echo $l;?> </td>
    					<td  align="center"> <? echo $size_library[$row[csf("gmts_size")]]; ?> </td>
    					<td  align="right"> <? echo $row[csf("bh_qty")]; ?> </td>
    					<td  align="right"> <? echo $row[csf("rf_qty")]; ?> </td>
    					<td  align="right"> <? echo $row[csf("dyeing_qty")]; ?> </td>
    					<td  align="right"> <? echo $row[csf("test_qty")]; ?> </td>
						<td  align="right"> <? echo $row[csf("self_qty")]; ?> </td>
						<td  align="right"> <? echo $row[csf("total_qty")]; ?> </td>
    				</tr>
    				<?
    				$l++;
    				$tot_rf_qty+=$row[csf("rf_qty")];
					$tot_bh_qty+=$row[csf("bh_qty")];
					$tot_dyeing_qty+=$row[csf("dyeing_qty")];
					$tot_test_qty+=$row[csf("test_qty")];
					$tot_self_qty+=$row[csf("self_qty")];
					$tot_total_qty+=$row[csf("total_qty")];
    			}
    			?>
    			<tr>
    				<td colspan="2" align="right"><strong>Grand Total</strong> </td>
    				<td  align="right"> <? echo number_format($tot_bh_qty,0); ?> </td>
					<td  align="right"> <? echo number_format($tot_rf_qty,0); ?> </td>
    				<td  align="right"> <? echo number_format($tot_dyeing_qty,0); ?> </td>
					<td  align="right"> <? echo number_format($tot_test_qty,0); ?> </td>
					<td  align="right"> <? echo number_format($tot_self_qty,0); ?> </td>
					<td  align="right"> <? echo number_format($tot_total_qty,0); ?> </td>
    			</tr>
    				<?
    		}

    		?>
    	</tbody>
    </table>
	<?
		//$po_break_down_arr 
		if(str_replace("'","",$cbo_fabric_source)==1)
		{
			$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');
	
			$po_number_arr=return_library_array( "select b.id,b.po_number from wo_po_break_down b,wo_booking_dtls a where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no ", "id", "po_number"  );
			$yarn_sql_array=sql_select("SELECT a.fabric_cost_dtls_id,min(a.id) as id ,a.count_id, a.copm_one_id, a.percent_one,a.copm_two_id, a.percent_two, a.color, a.type_id, sum(a.cons_qnty) as yarn_required, AVG(a.rate) as rate,b.po_break_down_id,sum(b.grey_fab_qnty) as grey_fab_qnty, a.cons_ratio from wo_pre_cost_fab_yarn_cost_dtls a, wo_booking_dtls b where a.job_no=b.job_no and a.fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.job_no='$job_no' and b.booking_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.count_id,a.copm_one_id,a.percent_one, a.copm_two_id,a.percent_two,a.color,a.type_id,b.po_break_down_id,a.fabric_cost_dtls_id, a.cons_ratio order by po_break_down_id ");
			?>
			<table  width="100%"  border="0" cellpadding="0" cellspacing="0" >
				<tr>
					<td width="49%" valign="top">
						<table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
							<tr align="center">
								<td colspan="7"><b>Yarn Summary</b></td>
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
									 <td><? echo $po_number_arr[$row[csf('po_break_down_id')]]; ?></td>
									<td>
										<?
										$yarn_des=$yarn_count_arr[$row[csf('count_id')]]." ".$composition[$row[csf('copm_one_id')]]." ".$row[csf('percent_one')]."%  ";
										if($row['copm_two_id'] !=0)
										{
											$yarn_des.=$composition[$row[csf('copm_two_id')]]." ".$row[csf('percent_two')]."%";
										}
										$yarn_des.=$yarn_type[$row[csf('type_id')]];
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
						// $is_yarn_allocated=return_field_value("allocation","variable_settings_inventory","company_name=$cbo_company_name and variable_list=18 and item_category_id=1");
						// if($is_yarn_allocated==1)
						// {
	
							$sql = "select booking_no,qty,item_group_id,description, uom,remarks from wo_booking_accessories_dtls where booking_no=".$txt_booking_no."";
	
							$accessories_data=sql_select($sql);
				
							if ( count($accessories_data)>0)
							{
							?>
							<!-- <font style=" font-size:30px"><b>Draft</b></font> -->
	
							<table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
							<tr align="center">
								<td colspan="7"><b>Accessories Details</b></td>
							</tr>
							<tr align="center">
								<td>Sl</td>
								<td>Item Name</td>
								<td>Item Description</td>
								<td>UOM</td>
								<td>Qty</td>
							</tr>
							<?
							$total_qty=0;
							 $item=return_library_array( "select id, item_name from   lib_item_group where item_category=4 and is_deleted=0  and  status_active=1 order by item_name",'id','item_name');
					   
					
							$i=0;
							$total_yarn=0;
							foreach($accessories_data  as $row)
							{
								$i++;
								?>
								<tr align="center">
									<td><? echo $i; ?></td>
									<td><? echo $item[$row[csf('item_group_id')]]; ?></td>
									<td><? echo $row[csf('description')]; ?></td>
									<td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
									<td align="right"><? echo number_format($row[csf('qty')],4); $total_qty+= $row[csf('qty')];?></td>
								</tr>
								<?
							}
							?>
							<tr align="center">
								<td>Total</td>
								<td></td>
								<td></td>
								<td></td>
								<td align="right"><? echo number_format($total_qty,4); ?></td>
							</tr>
						</table>
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
        <br/> <br/>
    		<table style="margin-top: 10px;" class="rpt_table" width="800" align="left" border="1" cellpadding="0" cellspacing="0" rules="all">
                	<thead>
                    	<tr>
                        	<th align="left" width="40">Sl</th>
                        	<th align="left" >Special Instruction</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?   
					$data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no"); 
					if ( count($data_array)>0)
					{
						$l=1;
						foreach( $data_array as $key=>$row )
						{
							
							?>
                            	<tr  align="">
                                    <td> <? echo $l;?> </td>
                                    <td> <? echo $row[csf("terms")]; ?> </td>
                                </tr>
                            <?
                            $l++;
						}
					}
					 
					?>
                </tbody>
            </table>
             </br>  

			 <? $booking_mst_info=sql_select("SELECT a.id as mst_id, a.insert_date, b.user_full_name, c.custom_designation,a.company_id from wo_booking_mst a join user_passwd b on b.id = a.inserted_by join lib_designation c on c.id = b.designation where a.status_active=1 and a.is_deleted=0 and a.BOOKING_NO=$txt_booking_no");
	$mst_arr=array('insert_date','user_full_name','custom_designation','mst_id','company_id');
	foreach($booking_mst_info as $row){
		foreach($mst_arr as $data){
			$$data=$row[csf($data)];
		}
	}

	$electronic_sequence_arr = [];
    $get_electronic_sequence_sql = sql_select("select sequence_no as sequence_no from electronic_approval_setup where page_id = 411 and company_id=$company_id and is_deleted = 0 order by sequence_no asc");
    foreach ($get_electronic_sequence_sql as $sequence){
        $electronic_sequence_arr[] = $sequence['SEQUENCE_NO'];
    }

    $sql_get_checked_user = sql_select("select user_passwd.user_full_name as USER_FULL_NAME, lib_designation.custom_designation as CUSTOM_DESIGNATION, to_char(approval_history.approved_date, 'DD-MM-YYYY HH:MI:SS AM') as APPROVED_DATE from approval_history left join user_passwd on user_passwd.id = approval_history.approved_by left join lib_designation on lib_designation.id = user_passwd.designation where approval_history.entry_form = 13 and approval_history.mst_id = $mst_id and approval_history.sequence_no in(".implode(',', $electronic_sequence_arr).") and approval_history.id = (select max(id) from approval_history where entry_form = 13 and mst_id = $mst_id and sequence_no = ".min($electronic_sequence_arr).") and rownum = 1 and (select max(CURRENT_APPROVAL_STATUS) from approval_history where entry_form = 13 and mst_id = $mst_id) = 1 order by approval_history.approved_no asc");
	
    $sql_get_approved_user = sql_select("select user_passwd.user_full_name as USER_FULL_NAME, lib_designation.custom_designation as CUSTOM_DESIGNATION, to_char(approval_history.approved_date, 'dd-mm-yyyy hh:mi:ss am') as APPROVED_DATE from approval_history inner join wo_booking_mst a on a.id = approval_history.mst_id left join user_passwd on user_passwd.id = approval_history.approved_by left join lib_designation on lib_designation.id = user_passwd.designation where approval_history.mst_id = $mst_id and approval_history.entry_form = 13 and a.is_approved = 1 and approval_history.current_approval_status = 1 ");
	// and approval_history.sequence_no =".max($electronic_sequence_arr)
	?>

	<table id="signatureTblId" width="901.5" style="padding-top:70px;">
		<tr>
			<td style="text-align: center; font-size: 18px;" width="230">
				<strong><?=$user_full_name?></strong>
				<br>
				<strong><?=$custom_designation?></strong>
				<br>
				<?=$insert_date?>
			</td>
			<!-- <td width="95"></td>
			<td style="text-align: center; font-size: 18px;" width="230">
				<strong><?=isset($sql_get_checked_user[0]) ? $sql_get_checked_user[0]['USER_FULL_NAME'] : ''?></strong>
				<br>
				<strong><?=$sql_get_checked_user[0]['CUSTOM_DESIGNATION']?></strong>
				<br>
				<?= isset($sql_get_checked_user[0]) ? $sql_get_checked_user[0]['APPROVED_DATE'] : ""?>
			</td> -->
			<td width="75"></td>
			<td width="95"></td>
			<td style="text-align: center; font-size: 18px;" width="230">
				<strong><?=isset($sql_get_approved_user[0]) ? $sql_get_approved_user[0]['USER_FULL_NAME'] : ''?></strong>
				<br>
				<strong><?=$sql_get_approved_user[0]['CUSTOM_DESIGNATION']?></strong>
				<br>
				<?= isset($sql_get_approved_user[0]) ? $sql_get_approved_user[0]['APPROVED_DATE'] : ""?>
			</td>
		</tr>
		<tr>
			<td style="text-align: center; font-size:18px; border-top:1px solid;"><strong>Prepared by</strong></td>
			<!-- <td width="75"></td>
			<td style="text-align: center; font-size:18px; border-top:1px solid;"><strong>Checked by</strong></td> -->
			<td width="75"></td>
			<td width="95"></td>
			<td style="text-align: center; font-size:18px; border-top:1px solid;"><strong>Approved by</strong></td>
		</tr>
	</table>
 
          <?
		 	//echo signature_table(5, $cbo_company_name, "1330px");
			//echo "****".custom_file_name($varcode_booking_no,$style_sting,'');
		  ?>
       </div>
		<script type="text/javascript" src="../../js/jquery.js"></script>
        <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
        <script>
        fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
        </script>
       <?
      
}

?>