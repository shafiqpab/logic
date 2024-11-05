<?
/*-------------------------------------------- Comments -----------------------
Version (MySql)          :  V2
Version (Oracle)         :  V1
Converted by             :  MONZU
Converted Date           :  24-05-2014
Purpose			         : 	This Form Will Create Woven Garments Price Quotation Entry.
Functionality	         :	
JS Functions	         :
Created by		         :	Monzu 
Creation date 	         : 	18-10-2012
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
Comments		         :
-------------------------------------------------------------------------------*/
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$type=$_REQUEST['type'];
$permission=$_SESSION['page_permission'];
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
//----------------------------------------------------Start---------------------------------------------------------
//*************************************************Master Form Start************************************************
if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 160, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );     	 
} 

if ($action=="order_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>
	<script>
	function js_set_value( job_no )
	{
		document.getElementById('selected_job').value=job_no;
		parent.emailwindow.hide();
	}
    </script>
</head>
<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="900" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
    	<tr>
        	<td align="center" width="100%">
            	<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                    <thead>                	 
                        <th width="150">Company Name</th>
                        <th width="150">Buyer Name</th>
                        <th width="100">Job No</th>
                        <th width="150">Order No</th>
                        <th width="200">Date Range</th><th></th>           
                    </thead>
        			<tr>
                    	<td> <input type="hidden" id="selected_job">
							<? 
								echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", '',"load_drop_down( 'copy_job_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
							?>
                    </td>
                   	<td id="buyer_td">
                     <? 
						echo create_drop_down( "cbo_buyer_name", 172, $blank_array,'', 1, "-- Select Buyer --" );
					?>	
                    </td>
                    <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:100px"></td>
                     <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:150px"></td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
					  <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					 </td> 
            		 <td align="center">
                     <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_order_search').value, 'create_po_search_list_view', 'search_div', 'copy_job_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
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
            <td align="center" valign="top" id="search_div"> 
	
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

if($action=="create_po_search_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and a.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.buyer_name='$data[1]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	if($db_type==0) $year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[5]";
	if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$data[5]";
	if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num='$data[4]'  $year_cond"; else  $job_cond=""; 
	if (str_replace("'","",$data[6])!="") $order_cond=" and b.po_number like '%$data[6]%'  "; else  $order_cond=""; 
	if($db_type==0)
	{
	if ($data[2]!="" &&  $data[3]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}
	if($db_type==2)
	{
	if ($data[2]!="" &&  $data[3]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (2=>$comp,3=>$buyer_arr);
	if($db_type==0)
	{
	$sql= "select YEAR(a.insert_date) as year, a.job_no_prefix_num,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity, b.po_number,b.po_quantity,b.shipment_date,a.job_no,c.id as pre_id from wo_po_details_master  a, wo_po_break_down b left join wo_pre_cost_mst c on b.job_no_mst=c.job_no and c.status_active=1 and c.is_deleted=0 where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 $shipment_date $company $buyer $job_cond $order_cond order by a.job_no";  
	}
	if($db_type==2)
	{
	$sql= "select to_char(a.insert_date,'YYYY') as year, a.job_no_prefix_num,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity, b.po_number,b.po_quantity,b.shipment_date,a.job_no,c.id as pre_id from wo_po_details_master  a, wo_po_break_down b left join wo_pre_cost_mst c on b.job_no_mst=c.job_no and c.status_active=1 and c.is_deleted=0 where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 $shipment_date $company $buyer $job_cond $order_cond order by a.job_no";  
	}
	echo  create_list_view("list_view", "Year,Job No,Company,Buyer Name,Style Ref. No,Job Qty.,PO number,PO Quantity,Shipment Date, Precost id", "60,60,120,100,100,90,140,90,80,100","1080","320",0, $sql , "js_set_value", "job_no", "", 1, "0,0,company_name,buyer_name,0,0,0,0,0", $arr , "year,job_no_prefix_num,company_name,buyer_name,style_ref_no,job_quantity,po_number,po_quantity,shipment_date,pre_id", "",'','0,0,0,0,0,1,0,1,3,0') ;
}

if ($action=="populate_data_from_job_table")
{
	$data_array=sql_select("select job_no,company_name,buyer_name,style_ref_no from wo_po_details_master where job_no='$data' and is_deleted=0 and status_active=1");
	foreach ($data_array as $row)
	{
		echo "load_drop_down( 'requires/copy_job_controller', '".$row[csf("company_name")]."', 'load_drop_down_buyer', 'buyer_td' );\n";
		echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n"; 
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_name")]."';\n";  
		echo "document.getElementById('txt_style_ref').value = '".$row[csf("style_ref_no")]."';\n";
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_name")]."';\n";  
		echo "document.getElementById('update_id').value = '".$row[csf("job_no")]."';\n"; 
		echo "$('#cbo_buyer_name').attr('disabled','true')".";\n";
		echo "$('#cbo_company_name').attr('disabled','true')".";\n";
	}
}



if($action=="save_update_delete_copy_job")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	
	$id=return_next_id( "id", "wo_po_details_master", 1 ) ;
	if($db_type==0)
	{
		$new_job_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', '', date("Y",time()), 5, "select job_no_prefix,job_no_prefix_num from wo_po_details_master where company_name=$cbo_company_name and YEAR(insert_date)=".date('Y',time())." order by job_no_prefix_num desc ", "job_no_prefix", "job_no_prefix_num" ));
	}
	if($db_type==2)
	{
		$new_job_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', '', date("Y",time()), 5, "select job_no_prefix,job_no_prefix_num from wo_po_details_master where company_name=$cbo_company_name and to_char(insert_date,'YYYY')=".date('Y',time())." order by job_no_prefix_num desc ", "job_no_prefix", "job_no_prefix_num" ));
	}
	
	$sql_insert="insert into wo_po_details_master(id,garments_nature,job_no_prefix,job_no_prefix_num,job_no,copy_from,quotation_id,order_repeat_no,company_name,buyer_name,style_ref_no,product_dept,product_code,pro_sub_dep,location_name,style_description,ship_mode,region,team_leader,dealing_marchant,remarks,job_quantity,avg_unit_price,currency_id,total_price,packing,agent_name,client_id,product_category,order_uom,gmts_item_id,set_break_down,total_set_qnty,set_smv,season,is_deleted,status_active,inserted_by,insert_date) 
	select	
	$id,garments_nature,'".$new_job_no[1]."','".$new_job_no[2]."','".$new_job_no[0]."',job_no,quotation_id,order_repeat_no,company_name,buyer_name,style_ref_no,product_dept,product_code,pro_sub_dep,location_name,style_description,ship_mode,region,team_leader,dealing_marchant,remarks,job_quantity,avg_unit_price,currency_id,total_price,packing,agent_name,client_id,product_category,order_uom,gmts_item_id,set_break_down,total_set_qnty,set_smv,season,is_deleted,status_active,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."' from wo_po_details_master where job_no=$txt_job_no";
	
	$rID=execute_query($sql_insert,0);
	
	if($db_type==0)
	{
		if($rID){
		mysql_query("COMMIT");  
		}
	}
	if($db_type==2 || $db_type==1 )
	{
		if($rID){
		oci_commit($con);  
		}
	}

	$sql_se_set=sql_select("select id from wo_po_details_mas_set_details  where job_no=$txt_job_no");
	foreach($sql_se_set as $row_se_set)
	{
		$id_set=return_next_id( "id", "wo_po_details_mas_set_details", 1 ) ;
		$sql_insert_set="insert into  wo_po_details_mas_set_details(id,job_no,gmts_item_id,set_item_ratio,smv_pcs,smv_set,smv_pcs_precost,smv_set_precost) 
		select $id_set,'".$new_job_no[0]."',gmts_item_id,set_item_ratio,smv_pcs,smv_set,smv_pcs_precost,smv_set_precost from  wo_po_details_mas_set_details where job_no=$txt_job_no and id=".$row_se_set[csf('id')]."";
		$rID1=execute_query($sql_insert_set,0);
	}
	
	if($db_type==0)
	{
		if($rID1){
		mysql_query("COMMIT");  
		}
	}
	if($db_type==2 || $db_type==1 )
	{
		if($rID1){
		oci_commit($con);  
		}
	}
	
	$po_id_maping_array=array();
	$sql_se_po=sql_select("select id from wo_po_break_down  where job_no_mst=$txt_job_no");
	foreach($sql_se_po as $row_se_po)
	{
		$id_po=return_next_id( "id", "wo_po_break_down", 1 ) ;
$sql_insert_po="insert into  wo_po_break_down(id,job_no_mst,po_number,pub_shipment_date,excess_cut,po_received_date,po_quantity,unit_price,plan_cut,country_name,po_total_price, 	shipment_date,pp_meeting_date,factory_received_date,original_avg_price,	t_year,	t_month, is_deleted, is_confirmed, details_remarks, delay_for, packing, grouping,projected_po_id,tna_task_from_upto,original_po_qty,inserted_by,insert_date,status_active,shiping_status) 
		select $id_po,'".$new_job_no[0]."',po_number,pub_shipment_date,excess_cut,po_received_date,po_quantity,unit_price,plan_cut,country_name,po_total_price, 	shipment_date,pp_meeting_date,factory_received_date,original_avg_price,	t_year,	t_month, is_deleted, is_confirmed, details_remarks, delay_for, packing, grouping,projected_po_id,tna_task_from_upto,original_po_qty,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',status_active,shiping_status from  wo_po_break_down where job_no_mst=$txt_job_no and id=".$row_se_po[csf('id')]."";
		$rID2=execute_query($sql_insert_po,0);
		$po_id_maping_array[$row_se_po[csf('id')]]=$id_po;
		//=========================================================================PO END======================================
		 $color_mst=return_library_array( "select color_mst_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=".$id_po." and status_active=1 and is_deleted=0 and color_mst_id !=0", "color_number_id", "color_mst_id"  );
		 $size_mst=return_library_array( "select size_mst_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=".$id_po." and status_active=1 and is_deleted=0 and size_mst_id !=0", "size_number_id", "size_mst_id"  );
		 $item_mst=return_library_array( "select item_mst_id,item_number_id from wo_po_color_size_breakdown where po_break_down_id=".$id_po." and status_active=1 and is_deleted=0 and item_mst_id !=0", "item_number_id", "item_mst_id"  );
		 $i=1;
		 $data_array="";
		 $id_co=return_next_id( "id", "wo_po_color_size_breakdown", 1 ) ;
		 $field_array="id,po_break_down_id,job_no_mst,color_mst_id, size_mst_id, item_mst_id, article_number, item_number_id,country_id,cutup_date,cutup,country_ship_date, size_number_id, color_number_id, order_quantity, order_rate, order_total,excess_cut_perc,plan_cut_qnty,country_remarks,is_deleted,status_active,inserted_by,insert_date";
		
		$sql_se_co=sql_select("select id, po_break_down_id, job_no_mst,color_mst_id,size_mst_id,item_mst_id,country_mst_id,article_number,item_number_id,country_id,cutup_date,cutup,country_ship_date,size_number_id, 	color_number_id,order_quantity,order_rate,order_total,excess_cut_perc,plan_cut_qnty,shiping_status,is_deleted,is_used,inserted_by,insert_date,updated_by,update_date,status_active,	is_locked,country_remarks from wo_po_color_size_breakdown  where job_no_mst=$txt_job_no and po_break_down_id=".$row_se_po[csf('id')]."");
		foreach($sql_se_co as $row_se_co)
		{
			if (array_key_exists($row_se_co[csf('item_number_id')],$item_mst))
			  {
				 $item_mst_id=$item_mst[$row_se_co[csf('item_number_id')]];
			  }
			else
			  {
			     $item_mst[$row_se_co[csf('item_number_id')]]=$id_co;
				 $item_mst_id=$id_co;
			  }
			  
			  
			  
			 if(array_key_exists($row_se_co[csf('color_number_id')],$color_mst))
			 {
				  $color_mst_id=$color_mst[$row_se_co[csf('color_number_id')]];	
			 }
			 
			 else
			 {
			  
			   $color_mst[$row_se_co[csf('color_number_id')]]=$id_co;
			   $color_mst_id=$id_co;
			 }
			 
			 if(array_key_exists($row_se_co[csf('size_number_id')],$size_mst))
			 {
				 $size_mst_id=$size_mst[$row_se_co[csf('size_number_id')]];	 
			 }
		     else
			 {
				  $size_mst[$row_se_co[csf('size_number_id')]]=$id_co;
				  $size_mst_id=$id_co;
			   
			 }
			 
			 if ($i!=1) $data_array .=",";
			$data_array .="(".$id_co.",".$id_po.",'".$new_job_no[0]."','".$color_mst_id."','".$size_mst_id."','".$item_mst_id."','".$row_se_co[csf('article_number')]."',".$row_se_co[csf('item_number_id')].",".$row_se_co[csf('country_id')].",'".$row_se_co[csf('cutup_date')]."',".$row_se_co[csf('cutup')].",'".$row_se_co[csf('country_ship_date')]."',".$row_se_co[csf('size_number_id')].",".$row_se_co[csf('color_number_id')].",".$row_se_co[csf('order_quantity')].",".$row_se_co[csf('order_rate')].",".$row_se_co[csf('order_total')].",".$row_se_co[csf('excess_cut_perc')].",".$row_se_co[csf('plan_cut_qnty')].",'".$row_se_co[csf('country_remarks')]."',0,".$row_se_co[csf('status_active')].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$id_co=$id_co+1;
			$i++;
		}
		$rID3=sql_insert("wo_po_color_size_breakdown",$field_array,$data_array,0);
		if($db_type==0)
		{
			if($rID3){
			mysql_query("COMMIT");  
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID3){
			oci_commit($con);  
			}
		}
		//=========================================================Color Size Break Down End==============================
		$data_array_sm="";
		 $sam=1;
		 $id_sm=return_next_id( "id", "wo_po_sample_approval_info", 1 ) ;
		 $cbosampletype=return_field_value( 'id', 'lib_sample', 'sample_type=2 and status_active=1 and is_deleted=0' );
		 $field_array_sm="id,job_no_mst,po_break_down_id,color_number_id,sample_type_id,status_active,is_deleted"; 		
		 $data_array_sample=sql_select("select a.id as po_id, b.color_number_id, min(b.id) as color_size_table_id from  wo_po_break_down a, wo_po_color_size_breakdown b where a.job_no_mst=b.job_no_mst and a.job_no_mst='".$new_job_no[0]."' and b.color_mst_id !=0 and a.id=b.po_break_down_id and  b.po_break_down_id='$id_po' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.id,b.color_number_id order by a.id");
		 foreach ( $data_array_sample as $row_sam1 )
		 {
			 if ($sam!=1) $data_array_sm .=",";
			  $data_array_sm .="(".$id_sm.",'".$new_job_no[0]."',".$row_sam1[csf('po_id')].",".$row_sam1[csf('color_size_table_id')].",'".$cbosampletype."',1,0)";
			  $id_sm=$id_sm+1;
			  $sam=$sam+1;
		 }
		 $rID4=sql_insert("wo_po_sample_approval_info",$field_array_sm,$data_array_sm,1);
	}
	
	if($db_type==0)
	{
		if($rID4){
		mysql_query("COMMIT");  
		}
	}
	if($db_type==2 || $db_type==1 )
	{
		if($rID4){
		oci_commit($con);  
		}
	}
	
	
	     
	
	
	
	$id_pre_mst=return_next_id( "id", "wo_pre_cost_mst", 1 ) ;
	$sql_insert_pre_mst="insert into wo_pre_cost_mst(id,garments_nature,job_no,costing_date,incoterm,incoterm_place,machine_line,prod_line_hr,costing_per,copy_quatation,cm_cost_predefined_method_id,exchange_rate,sew_smv,cut_smv,sew_effi_percent,cut_effi_percent,efficiency_wastage_percent,remarks,ready_to_approved,inserted_by,insert_date,status_active,is_deleted) 
	select	
$id_pre_mst,garments_nature,'".$new_job_no[0]."',costing_date,incoterm,incoterm_place,machine_line,prod_line_hr,costing_per,copy_quatation,cm_cost_predefined_method_id,exchange_rate,sew_smv,cut_smv,sew_effi_percent,cut_effi_percent,efficiency_wastage_percent,remarks,ready_to_approved,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',status_active,is_deleted from wo_pre_cost_mst where job_no=$txt_job_no";
	$rID5=execute_query($sql_insert_pre_mst,0);
	if($db_type==0)
	{
		if($rID5){
		mysql_query("COMMIT");  
		}
	}
	if($db_type==2 || $db_type==1 )
	{
		if($rID5){
		oci_commit($con);  
		}
	}
	
	$id_pre_dtls=return_next_id( "id", "wo_pre_cost_dtls", 1 ) ;
	$sql_insert_pre_dtls="insert into wo_pre_cost_dtls(id,job_no,costing_per_id,order_uom_id,fabric_cost,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,wash_cost, 	wash_cost_percent,comm_cost,comm_cost_percent,commission,commission_percent,lab_test,lab_test_percent,inspection,inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost, currier_percent, certificate_pre_cost, certificate_percent,common_oh,common_oh_percent,depr_amor_pre_cost,depr_amor_po_price,total_cost,total_cost_percent,price_dzn,price_dzn_percent,margin_dzn,margin_dzn_percent,cost_pcs_set,cost_pcs_set_percent,price_pcs_or_set,price_pcs_or_set_percent,margin_pcs_set,margin_pcs_set_percent,cm_for_sipment_sche,inserted_by,insert_date,status_active,is_deleted) 
	select	
$id_pre_dtls,'".$new_job_no[0]."',costing_per_id,order_uom_id,fabric_cost,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,wash_cost, 	wash_cost_percent,comm_cost,comm_cost_percent,commission,commission_percent,lab_test,lab_test_percent,inspection,inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost, currier_percent, certificate_pre_cost, certificate_percent,common_oh,common_oh_percent,depr_amor_pre_cost,depr_amor_po_price,total_cost,total_cost_percent,price_dzn,price_dzn_percent,margin_dzn,margin_dzn_percent,cost_pcs_set,cost_pcs_set_percent,price_pcs_or_set,price_pcs_or_set_percent,margin_pcs_set,margin_pcs_set_percent,cm_for_sipment_sche,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',status_active,is_deleted from wo_pre_cost_dtls where job_no=$txt_job_no";
	$rID6=execute_query($sql_insert_pre_dtls,0);
	
	if($db_type==0)
	{
		if($rID6){
		mysql_query("COMMIT");  
		}
	}
	if($db_type==2 || $db_type==1 )
	{
		if($rID6){
		oci_commit($con);  
		}
	}
	
	
	$fabric_cost_id_maping=array();
	$sql_se_fabric=sql_select("select id from wo_pre_cost_fabric_cost_dtls  where job_no=$txt_job_no");
	foreach($sql_se_fabric as $row_se_fabric)
	{
		$wo_pre_cost_fabric_cost_dtls_id=return_next_id( "id", "wo_pre_cost_fabric_cost_dtls", 1 ) ;
$sql_insert_fabric="insert into  wo_pre_cost_fabric_cost_dtls(id, job_no, item_number_id, body_part_id, fab_nature_id, color_type_id,lib_yarn_count_deter_id,construction,composition, fabric_description, gsm_weight,color_size_sensitive,	color, avg_cons, fabric_source, rate, amount,avg_finish_cons,	avg_process_loss, inserted_by, insert_date, status_active, is_deleted, company_id, costing_per,consumption_basis,process_loss_method,cons_breack_down,msmnt_break_down,color_break_down,yarn_breack_down,marker_break_down,width_dia_type,avg_cons_yarn,gsm_weight_yarn,plan_cut_qty,job_plan_cut_qty) 
		select $wo_pre_cost_fabric_cost_dtls_id, '".$new_job_no[0]."', item_number_id, body_part_id, fab_nature_id, color_type_id,lib_yarn_count_deter_id,construction,composition, fabric_description, gsm_weight,color_size_sensitive,	color, avg_cons, fabric_source, rate, amount,avg_finish_cons,	avg_process_loss, ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."', status_active, is_deleted, company_id, costing_per,consumption_basis,process_loss_method,cons_breack_down,msmnt_break_down,color_break_down,yarn_breack_down,marker_break_down,width_dia_type,avg_cons_yarn,gsm_weight_yarn,plan_cut_qty,job_plan_cut_qty from  wo_pre_cost_fabric_cost_dtls where job_no=$txt_job_no and id=".$row_se_fabric[csf('id')]."";
		$rID7=execute_query($sql_insert_fabric,0);
		$fabric_cost_id_maping[$row_se_fabric[csf('id')]]=$wo_pre_cost_fabric_cost_dtls_id;
	}
	
	if($db_type==0)
	{
		if($rID7){
		mysql_query("COMMIT");  
		}
	}
	if($db_type==2 || $db_type==1 )
	{
		if($rID7){
		oci_commit($con);  
		}
	}
	
	
	
	
	$array_color_size_table_id=array();
	$sql_color_size_table_id=sql_select("select b.id, c.item_number_id,c.color_number_id,c.size_number_id,min(c.id) as color_size_table_id from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c  where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id  and b.job_no_mst='".$new_job_no[0]."'  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id, c.item_number_id,c.color_number_id,c.size_number_id order by b.id,color_size_table_id");
	foreach($sql_color_size_table_id as $row_color_size_table_id)
	{
	$array_color_size_table_id[$row_color_size_table_id[csf('id')]][$row_color_size_table_id[csf('item_number_id')]][$row_color_size_table_id[csf('color_number_id')]][$row_color_size_table_id[csf('size_number_id')]]=$row_color_size_table_id[csf('color_size_table_id')];	
	}
	
	//print_r($array_color_size_table_id);
	
	$sql_se_fabric_avg=sql_select("select a.item_number_id,b.id,b.pre_cost_fabric_cost_dtls_id,b.po_break_down_id,b.color_number_id,b.gmts_sizes from wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b  where a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and a.job_no=$txt_job_no");
	foreach($sql_se_fabric_avg as $row_se_fabric_avg)
	{
		$color_size_table_id=$array_color_size_table_id[$po_id_maping_array[$row_se_fabric_avg[csf('po_break_down_id')]]][$row_se_fabric_avg[csf('item_number_id')]][$row_se_fabric_avg[csf('color_number_id')]][$row_se_fabric_avg[csf('gmts_sizes')]];
		
		$wo_pre_cos_fab_co_avg_con_dtls_id=return_next_id( "id", "wo_pre_cos_fab_co_avg_con_dtls", 1 ) ;
  $sql_insert_fabric_avg="insert into  wo_pre_cos_fab_co_avg_con_dtls(id, pre_cost_fabric_cost_dtls_id, job_no, po_break_down_id,color_number_id, gmts_sizes, dia_width,item_size, cons, process_loss_percent, requirment, pcs,color_size_table_id, body_length, body_sewing_margin,	body_hem_margin, sleeve_length, sleeve_sewing_margin, sleeve_hem_margin, half_chest_length, half_chest_sewing_margin,front_rise_length,	front_rise_sewing_margin, west_band_length, west_band_sewing_margin, in_seam_length, in_seam_sewing_margin, in_seam_hem_margin, half_thai_length, half_thai_sewing_margin,total,marker_dia,marker_yds,marker_inch,gmts_pcs,marker_length,net_fab_cons) 
		select $wo_pre_cos_fab_co_avg_con_dtls_id, ".$fabric_cost_id_maping[$row_se_fabric_avg[csf('pre_cost_fabric_cost_dtls_id')]].", '".$new_job_no[0]."', ".$po_id_maping_array[$row_se_fabric_avg[csf('po_break_down_id')]].",color_number_id, gmts_sizes, dia_width,item_size, cons, process_loss_percent, requirment, pcs,".$color_size_table_id.", body_length, body_sewing_margin,	body_hem_margin, sleeve_length, sleeve_sewing_margin, sleeve_hem_margin, half_chest_length, half_chest_sewing_margin,front_rise_length,	front_rise_sewing_margin, west_band_length, west_band_sewing_margin, in_seam_length, in_seam_sewing_margin, in_seam_hem_margin, half_thai_length, half_thai_sewing_margin,total,marker_dia,marker_yds,marker_inch,gmts_pcs,marker_length,net_fab_cons from  wo_pre_cos_fab_co_avg_con_dtls where job_no=$txt_job_no and id=".$row_se_fabric_avg[csf('id')]."";
		$rID8=execute_query($sql_insert_fabric_avg,0);
	}
	
	if($db_type==0)
	{
		if($rID8){
		mysql_query("COMMIT");  
		}
	}
	if($db_type==2 || $db_type==1 )
	{
		if($rID8){
		oci_commit($con);  
		}
	}
	
	$sql_se_fabric_color=sql_select("select id,pre_cost_fabric_cost_dtls_id from wo_pre_cos_fab_co_color_dtls  where job_no=$txt_job_no");
	foreach($sql_se_fabric_color as $row_se_fabric_color)
	{
		
		$wo_pre_cos_fab_co_color_dtls_id=return_next_id( "id", "wo_pre_cos_fab_co_color_dtls", 1 ) ;
  $sql_insert_fabric_color="insert into  wo_pre_cos_fab_co_color_dtls(id,pre_cost_fabric_cost_dtls_id,job_no,gmts_color_id,gmts_color,contrast_color_id) 
		select $wo_pre_cos_fab_co_color_dtls_id,".$fabric_cost_id_maping[$row_se_fabric_color[csf('pre_cost_fabric_cost_dtls_id')]].",'".$new_job_no[0]."',gmts_color_id,gmts_color,contrast_color_id from  wo_pre_cos_fab_co_color_dtls where job_no=$txt_job_no and id=".$row_se_fabric_color[csf('id')]."";
		$rID9=execute_query($sql_insert_fabric_color,0);
	}

    if($db_type==0)
	{
		if($rID9){
		mysql_query("COMMIT"); 
		}
	}
	if($db_type==2 || $db_type==1 )
	{
		if($rID9){
		oci_commit($con);  
		}
	}
	
	
	$sql_se_fabric_yarn=sql_select("select id, 	fabric_cost_dtls_id from wo_pre_cost_fab_yarn_cost_dtls  where job_no=$txt_job_no");
	foreach($sql_se_fabric_yarn as $row_se_fabric_yarn)
	{
		
		$wo_pre_cost_fab_yarn_cost_dtls_id=return_next_id( "id", "wo_pre_cost_fab_yarn_cost_dtls", 1 ) ;
		
  $sql_insert_fabric_yarn="insert into  wo_pre_cost_fab_yarn_cost_dtls(id,fabric_cost_dtls_id,job_no,count_id,copm_one_id,percent_one,type_id,cons_ratio,cons_qnty,avg_cons_qnty,inserted_by,insert_date,status_active,is_deleted) 
		select $wo_pre_cost_fab_yarn_cost_dtls_id,".$fabric_cost_id_maping[$row_se_fabric_yarn[csf('fabric_cost_dtls_id')]].",'".$new_job_no[0]."',count_id,copm_one_id,percent_one,type_id,cons_ratio,cons_qnty,avg_cons_qnty,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',status_active,is_deleted from  wo_pre_cost_fab_yarn_cost_dtls where job_no=$txt_job_no and id=".$row_se_fabric_yarn[csf('id')]."";
		$rID10=execute_query($sql_insert_fabric_yarn,0);
	}

    if($db_type==0)
	{
		if($rID10){
		mysql_query("COMMIT"); 
		}
	}
	if($db_type==2 || $db_type==1 )
	{
		if($rID10){
		oci_commit($con);  
		}
	}
	
	$sql_se_fabric_yarn_b=sql_select("select id, fabric_cost_dtls_id from wo_pre_cost_fab_yarnbreakdown  where job_no=$txt_job_no");
	foreach($sql_se_fabric_yarn_b as $row_se_fabric_yarn_b)
	{
		
		$wo_pre_cost_fab_yarnbreakdown_id=return_next_id( "id", "wo_pre_cost_fab_yarnbreakdown", 1 ) ;
		
  $sql_insert_fabric_yarn_b="insert into  wo_pre_cost_fab_yarnbreakdown(id,fabric_cost_dtls_id,job_no,count_id,copm_one_id,percent_one,type_id,cons_ratio,cons_qnty,avg_cons_qnty,inserted_by,insert_date,status_active,is_deleted) 
		select $wo_pre_cost_fab_yarnbreakdown_id,".$fabric_cost_id_maping[$row_se_fabric_yarn_b[csf('fabric_cost_dtls_id')]].",'".$new_job_no[0]."',count_id,copm_one_id,percent_one,type_id,cons_ratio,cons_qnty,avg_cons_qnty,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',status_active,is_deleted from  wo_pre_cost_fab_yarnbreakdown where job_no=$txt_job_no and id=".$row_se_fabric_yarn_b[csf('id')]."";
		$rID11=execute_query($sql_insert_fabric_yarn_b,0);
	}

    if($db_type==0)
	{
		if($rID11){
		mysql_query("COMMIT"); 
		}
	}
	if($db_type==2 || $db_type==1 )
	{
		if($rID11){
		oci_commit($con);  
		}
	}
	
	
	$sql_se_fabric_conver=sql_select("select id, fabric_description from wo_pre_cost_fab_conv_cost_dtls  where job_no=$txt_job_no");
	foreach($sql_se_fabric_conver as $row_se_fabric_conver)
	{
		
		$wo_pre_cost_fab_conv_cost_dtls_id=return_next_id( "id", "wo_pre_cost_fab_conv_cost_dtls", 1 ) ;
		
  $sql_insert_fabric_conver="insert into  wo_pre_cost_fab_conv_cost_dtls(id,job_no,fabric_description,cons_process,req_qnty,avg_req_qnty,charge_unit,amount,color_break_down,charge_lib_id,inserted_by,insert_date,status_active,is_deleted) 
		select $wo_pre_cost_fab_conv_cost_dtls_id,'".$new_job_no[0]."',".$fabric_cost_id_maping[$row_se_fabric_conver[csf('fabric_description')]].",cons_process,req_qnty,avg_req_qnty,charge_unit,amount,color_break_down,charge_lib_id,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',status_active,is_deleted from  wo_pre_cost_fab_conv_cost_dtls where job_no=$txt_job_no and id=".$row_se_fabric_conver[csf('id')]."";
		$rID12=execute_query($sql_insert_fabric_conver,0);
	}

    if($db_type==0)
	{
		if($rID12){
		mysql_query("COMMIT"); 
		}
	}
	if($db_type==2 || $db_type==1 )
	{
		if($rID12){
		oci_commit($con);  
		}
	}
	
	$trim_table_id_maping=array();
	$sql_se_trim=sql_select("select id from wo_pre_cost_trim_cost_dtls  where job_no=$txt_job_no");
	foreach($sql_se_trim as $row_se_trim)
	{
		$wo_pre_cost_trim_cost_dtls_id=return_next_id( "id", "wo_pre_cost_trim_cost_dtls", 1 ) ;
		
        $sql_insert_fabric_trim="insert into  wo_pre_cost_trim_cost_dtls(id, job_no, trim_group,description,brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,cons_breack_down, inserted_by, insert_date, status_active,	is_deleted) 
		select $wo_pre_cost_trim_cost_dtls_id, '".$new_job_no[0]."', trim_group,description,brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,cons_breack_down, ".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."', status_active,	is_deleted from  wo_pre_cost_trim_cost_dtls where job_no=$txt_job_no and id=".$row_se_trim[csf('id')]."";
		$rID13=execute_query($sql_insert_fabric_trim,0);
		$trim_table_id_maping[$row_se_trim[csf('id')]]=$wo_pre_cost_trim_cost_dtls_id;
	}
	
	
	$sql_se_trim_cons=sql_select("select id,wo_pre_cost_trim_cost_dtls_id,po_break_down_id from wo_pre_cost_trim_co_cons_dtls  where job_no=$txt_job_no");
	foreach($sql_se_trim_cons as $row_se_trim_cons)
	{
		$wo_pre_cost_trim_co_cons_dtls_id=return_next_id( "id", "wo_pre_cost_trim_co_cons_dtls", 1 ) ;
		
         $sql_insert_fabric_trim_avg="insert into  wo_pre_cost_trim_co_cons_dtls(id, wo_pre_cost_trim_cost_dtls_id, job_no, po_break_down_id,item_size, cons, place, pcs,country_id) 
		select $wo_pre_cost_trim_co_cons_dtls_id, ".$trim_table_id_maping[$row_se_trim_cons[csf('wo_pre_cost_trim_cost_dtls_id')]].", '".$new_job_no[0]."', ".$po_id_maping_array[$row_se_trim_cons[csf('po_break_down_id')]].",item_size, cons, place, pcs,country_id from  wo_pre_cost_trim_co_cons_dtls where job_no=$txt_job_no and id=".$row_se_trim_cons[csf('id')]."";
		
		$rID14=execute_query($sql_insert_fabric_trim_avg,0);
	}
	
	
	$sql_se_embe_wash=sql_select("select id from wo_pre_cost_embe_cost_dtls  where job_no=$txt_job_no");
	foreach($sql_se_embe_wash as $row_se_embe_wash)
	{
		$wo_pre_cost_embe_cost_dtls_id=return_next_id( "id", "wo_pre_cost_embe_cost_dtls", 1 ) ;
		
         $sql_insert_embe_wash="insert into  wo_pre_cost_embe_cost_dtls(id,job_no,emb_name,emb_type,cons_dzn_gmts,rate,amount,inserted_by,insert_date,status_active,is_deleted) select $wo_pre_cost_embe_cost_dtls_id,'".$new_job_no[0]."',emb_name,emb_type,cons_dzn_gmts,rate,amount,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',status_active,is_deleted from  wo_pre_cost_embe_cost_dtls where job_no=$txt_job_no and id=".$row_se_embe_wash[csf('id')]."";
		$rID15=execute_query($sql_insert_embe_wash,0);
	}

    if($db_type==0)
	{
		if($rID15){
		mysql_query("COMMIT"); 
		}
	}
	if($db_type==2 || $db_type==1 )
	{
		if($rID15){
		oci_commit($con);  
		}
	}
	
	
	$sql_se_commercial=sql_select("select id from wo_pre_cost_comarci_cost_dtls  where job_no=$txt_job_no");
	foreach($sql_se_commercial as $row_se_commercial)
	{
		$wo_pre_cost_comarci_cost_dtls_id=return_next_id( "id", "wo_pre_cost_comarci_cost_dtls", 1 ) ;
        $sql_insert_commercial="insert into  wo_pre_cost_comarci_cost_dtls(id,job_no,item_id,rate,amount,inserted_by,insert_date,status_active,is_deleted) select $wo_pre_cost_comarci_cost_dtls_id,'".$new_job_no[0]."',item_id,rate,amount,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',status_active,is_deleted from  wo_pre_cost_comarci_cost_dtls where job_no=$txt_job_no and id=".$row_se_commercial[csf('id')]."";
		$rID16=execute_query($sql_insert_commercial,0);
	}

    if($db_type==0)
	{
		if($rID16){
		mysql_query("COMMIT"); 
		}
	}
	if($db_type==2 || $db_type==1 )
	{
		if($rID16){
		oci_commit($con);  
		}
	}
	
	
	$sql_se_commision=sql_select("select id from wo_pre_cost_commiss_cost_dtls  where job_no=$txt_job_no");
	foreach($sql_se_commision as $row_se_commision)
	{
		$wo_pre_cost_commiss_cost_dtls_id=return_next_id( "id", "wo_pre_cost_commiss_cost_dtls", 1 ) ;
        $sql_insert_commision="insert into  wo_pre_cost_commiss_cost_dtls(id,job_no,particulars_id,commission_base_id,commision_rate,commission_amount,inserted_by,insert_date,status_active,is_deleted) 
		select $wo_pre_cost_commiss_cost_dtls_id,'".$new_job_no[0]."',particulars_id,commission_base_id,commision_rate,commission_amount,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',status_active,is_deleted from  wo_pre_cost_commiss_cost_dtls where job_no=$txt_job_no and id=".$row_se_commision[csf('id')]."";
		$rID17=execute_query($sql_insert_commision,0);
	}

    if($db_type==0)
	{
		if($rID17){
		mysql_query("COMMIT"); 
		}
	}
	if($db_type==2 || $db_type==1 )
	{
		if($rID17){
		oci_commit($con);  
		}
	}
	
	$wo_pre_cost_sum_dtls_id=return_next_id( "id", "wo_pre_cost_sum_dtls", 1 ) ;
	$sql_insert_sum="insert into  wo_pre_cost_sum_dtls(id, job_no, fab_yarn_req_kg, fab_woven_req_yds, fab_knit_req_kg, fab_woven_fin_req_yds, fab_knit_fin_req_kg, fab_amount, avg, yarn_cons_qnty, yarn_amount, conv_req_qnty, conv_charge_unit, conv_amount, trim_cons, trim_rate, trim_amount, emb_amount, comar_rate, comar_amount, commis_rate, commis_amount, inserted_by, insert_date, status_active, is_deleted, pro_woven_grey_fab_req_yds, pro_knit_grey_fab_req_kg, pro_woven_fin_fab_req_yds, pro_knit_fin_fab_req_kg, pur_woven_grey_fab_req_yds, pur_knit_grey_fab_req_kg, pur_woven_fin_fab_req_yds, pur_knit_fin_fab_req_kg, woven_amount, knit_amount) 
		select $wo_pre_cost_sum_dtls_id, '".$new_job_no[0]."', fab_yarn_req_kg, fab_woven_req_yds, fab_knit_req_kg, fab_woven_fin_req_yds, fab_knit_fin_req_kg, fab_amount, avg, yarn_cons_qnty, yarn_amount, conv_req_qnty, conv_charge_unit, conv_amount, trim_cons, trim_rate, trim_amount, emb_amount, comar_rate, comar_amount, commis_rate, commis_amount, ".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."', status_active, is_deleted, pro_woven_grey_fab_req_yds, pro_knit_grey_fab_req_kg, pro_woven_fin_fab_req_yds, pro_knit_fin_fab_req_kg, pur_woven_grey_fab_req_yds, pur_knit_grey_fab_req_kg, pur_woven_fin_fab_req_yds, pur_knit_fin_fab_req_kg, woven_amount, knit_amount from  wo_pre_cost_sum_dtls where job_no=$txt_job_no";
		$rID18=execute_query($sql_insert_sum,0);
		
	if($db_type==0)
	{
		if($rID18){
		mysql_query("COMMIT"); 
		}
	}
	if($db_type==2 || $db_type==1 )
	{
		if($rID18){
		oci_commit($con);  
		}
	}
	echo "0**".$new_job_no[0];
	disconnect($con);
	die;
}
?>
