<?
session_start();
include('../../../includes/common.php');
 
$user_id = $_SESSION['logic_erp']["user_id"];
$user_level = $_SESSION['logic_erp']['user_level'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


//========== user credential start ========
$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];
$item_cate_id = $userCredential[0][csf('item_cate_id')];
$location_id = $userCredential[0][csf('location_id')];

$company_credential_cond = "";

if ($company_id >0) {
    $company_credential_cond = " and id in($company_id)";
}

if (!empty($store_location_id)) {
    $store_location_credential_cond = " and a.id in($store_location_id)";
}

if ($location_id !='') {
    $location_credential_cond = " and id in($location_id)";
}

if($item_cate_id !='') {
    $item_cate_credential_cond = $item_cate_id ;
}

 $user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");


//========== user credential end ==========

//------------------------------------------------------------------------------------------------------
$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );
$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
$country_short_name=return_library_array( "select id,short_name from lib_country", "id", "short_name"  );

if($db_type==0) $select_field="group"; 
else if($db_type==2) $select_field="wm";
else $select_field="";//defined Later	

if ($action=="load_variable_settings")
{
	echo "$('#sewing_production_variable').val(0);\n";
	$sql_result = sql_select("select finishing_update,production_entry from variable_settings_production where company_name=$data and variable_list=1 and status_active=1");
 	foreach($sql_result as $result)
	{
		$finishing_update = ($result[csf("finishing_update")]==0) ? 3 : $result[csf("finishing_update")];
		echo "$('#sewing_production_variable').val(".$finishing_update.");\n";
		echo "$('#styleOrOrderWisw').val(".$result[csf("production_entry")].");\n";
	}
	
	echo "$('#finish_production_variable_rej').val(0);\n";
	$sql_result_rej = sql_select("select finishing_update from variable_settings_production where company_name=$data and variable_list=28 and status_active=1");
 	foreach($sql_result_rej as $result)
	{
		echo "$('#finish_production_variable_rej').val(".$result[csf("finishing_update")].");\n";
		
		
		if($result[csf("finishing_update")]==3) //Color and Size
		{
				echo "$('#txt_reject_qnty').attr('readonly','readonly');\n";
		}
		else
		{
			echo "$('#txt_reject_qnty').removeAttr('readonly','readonly');\n";	
		}
	}
	$variable_is_control=return_field_value("is_control","variable_settings_production","company_name=$data and variable_list=50 and page_category_id=8","is_control");
	echo "document.getElementById('variable_is_controll').value='".$variable_is_control."';\n";
	
	$variable_qty_source_packing=return_field_value("qty_source_packing","variable_settings_production","company_name=$data and variable_list=43","qty_source_packing");
	if($variable_qty_source_packing=='') $variable_qty_source_packing=1;
	
	echo "document.getElementById('txt_qty_source').value=".$variable_qty_source_packing.";\n";
	
 	exit();
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 167, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' $location_credential_cond order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/finishing_entry_controller', this.value, 'load_drop_down_floor', 'floor_td' );",0 );     	 
}

if ($action=="load_drop_down_floor")
{
	//echo "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and production_process in (11) order by floor_name";die;
	echo create_drop_down( "cbo_floor", 170, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and production_process in (11) order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0 );     	 
}
 
if($action=="load_drop_down_source")
{
	$explode_data = explode("**",$data);
	$data = $explode_data[0];
	$selected_company = $explode_data[1];
	
	if($data==3)
	{
		if($db_type==0)
		{
		echo create_drop_down( "cbo_finish_company", 170, "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 and find_in_set(22,party_type) order by supplier_name","id,supplier_name", 1, "--- Select ---", $selected, "",0,0 );
		}
		else
		{
			echo create_drop_down( "cbo_finish_company", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=22 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--Select--", $selected, "" );
		}
	}
	else if($data==1)
 		echo create_drop_down( "cbo_finish_company", 170, "select id,company_name from lib_company where is_deleted=0 and status_active=1 $company_credential_cond order by company_name","id,company_name", 1, "--- Select ---", "", "load_drop_down( 'requires/finishing_entry_controller', this.value, 'load_drop_down_location', 'location_td' );fnc_company_check(document.getElementById('cbo_source').value);",0,0 ); 
 	else
		echo create_drop_down( "cbo_finish_company", 170, $blank_array,"", 1, "--- Select ---", $selected, "",0,0 );	
			
	exit();
}

if($action=="order_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	?>
	<script>
		$(document).ready(function(e) {
            $("#txt_search_common").focus();
        });
		
		function search_populate(str)
		{
			//alert(str); 
			if(str==0) 
			{		
				document.getElementById('search_by_th_up').innerHTML="Order No";
				document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:230px " class="text_boxes" id="txt_search_common"	value=""  />';		 
			}
			else if(str==1) 
			{
				document.getElementById('search_by_th_up').innerHTML="Style Ref. Number";
				document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:230px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==3)
			{
				document.getElementById('search_by_th_up').innerHTML="Job No";
				document.getElementById('search_by_td').innerHTML='<input type="text" name="txt_search_common" style="width:230px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==4)
			{
				document.getElementById('search_by_th_up').innerHTML="Actual PO No";
				document.getElementById('search_by_td').innerHTML='<input type="text" name="txt_search_common" style="width:230px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==5)
			{
				document.getElementById('search_by_th_up').innerHTML="File No";
				document.getElementById('search_by_td').innerHTML='<input type="text" name="txt_search_common" style="width:230px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==6)
			{
				document.getElementById('search_by_th_up').innerHTML="Internal Ref";
				document.getElementById('search_by_td').innerHTML='<input type="text" name="txt_search_common" style="width:230px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else //if(str==2)
			{
				var buyer_name = '<option value="0">--- Select Buyer ---</option>';
				<?php 
				if($db_type==0)
				{
					$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer where find_in_set($company,tag_company) and status_active=1 and is_deleted=0 order by buyer_name",'id','buyer_name');
				}
				else
				{
					$buyer_arr=return_library_array( "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond order by buy.buyer_name",'id','buyer_name');
				}				
				foreach($buyer_arr as $key=>$val)
				{
					echo "buyer_name += '<option value=\"$key\">".($val)."</option>';";
				} 
				?>
				document.getElementById('search_by_th_up').innerHTML="Select Buyer Name";
				document.getElementById('search_by_td').innerHTML='<select	name="txt_search_common" style="width:230px " class="combo_boxes" id="txt_search_common">'+ buyer_name +'</select>';
			}																																													
		}
	
	function js_set_value(id,item_id,po_qnty,plan_qnty,country_id,pack_type)
	{
		$("#hidden_mst_id").val(id);
		$("#hidden_grmtItem_id").val(item_id); 
		$("#hidden_po_qnty").val(po_qnty);
		$("#hidden_country_id").val(country_id);
		$("#hidden_pack_type").val(pack_type);
  		parent.emailwindow.hide();
 	}
    </script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
	    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	        <table width="780" cellspacing="0" cellpadding="0" class="rpt_table" align="center" border="1" rules="all">
	    		<tr>
	        		<td align="center" width="100%">
	                    <table ellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
	                   		 <thead>                	 
	                        	<th width="130">Search By</th>
	                        	<th  width="180" align="center" id="search_by_th_up">Enter Order Number</th>
	                        	<th width="200">Date Range</th>
	                        	<th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
	                    	</thead>
	        				<tr>
	                    		<td width="130">  
									<? 
										//$searchby_arr=array(0=>"Order No",1=>"Style Ref. Number",2=>"Buyer Name");
										$searchby_arr=array(0=>"Order No",1=>"Style Ref. Number",2=>"Buyer Name",3=>"Job No",4=>"Actual PO No",5=>"File No",6=>"Internal Ref");
										echo create_drop_down( "txt_search_by", 130, $searchby_arr,"", 1, "-- Select --", $selected, "search_populate(this.value)",0 );
	                                ?>
	                    		</td>
	                   			<td width="180" align="center" id="search_by_td">				
									<input type="text" style="width:230px" class="text_boxes"  name="txt_search_common" id="txt_search_common" onKeyDown="if (event.keyCode == 13) document.getElementById('btn_show').click()" />			
	            				</td>
	                    		<td align="center">
	                            	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"> To
						  			<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
						 		</td> 
	            		 		<td align="center">
	                     			<input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+<? echo $garments_nature; ?>+'_'+<? echo $production_company; ?>+'_'+<? echo $hidden_variable_cntl; ?>+'_'+<? echo $hidden_preceding_process; ?>, 'create_po_search_list_view', 'search_div', 'finishing_entry_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:100px;" />
	                            </td>
	        				</tr>
	             		</table>
	          		</td>
	        	</tr>
	        	<tr>
	            	<td  align="center" height="40" valign="middle">
						<? echo load_month_buttons(1);  ?>
	                    <input type="hidden" id="hidden_mst_id">
	                    <input type="hidden" id="hidden_grmtItem_id">
	                    <input type="hidden" id="hidden_po_qnty">
	                    <input type="hidden" id="hidden_country_id">
	                    <input type="hidden" id="hidden_pack_type">
	          		</td>
	            </tr>
	    	</table>   
	        <div style="margin-top:10px" id="search_div"></div>  
	    </form>
	</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="create_po_search_list_view")
{
 	$ex_data = explode("_",$data);
	$txt_search_by = $ex_data[0];
	$txt_search_common = $ex_data[1];
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company = $ex_data[4];
 	$garments_nature = $ex_data[5];
 	$production_company = $ex_data[6];
 	$variable_cntl = $ex_data[7];
	$preceding_process = $ex_data[8];
	$qty_source=0;
	if($preceding_process==3) $qty_source=3; //Wash Complete
	else if($preceding_process==5) $qty_source=5;//Sewing Output
	else if($preceding_process==67) $qty_source=67;//Iron Output
	
	$variable_qty_source_packing=return_field_value("qty_source_packing","variable_settings_production","company_name=$company and variable_list=43","qty_source_packing");
	
	$sql_cond="";
	if(trim($txt_search_common)!="")
	{
		if(trim($txt_search_by)==0)
			$sql_cond = " and b.po_number like '%".trim($txt_search_common)."%'";
		else if(trim($txt_search_by)==1)
			$sql_cond = " and a.style_ref_no like '%".trim($txt_search_common)."%'";
		else if(trim($txt_search_by)==2)
			$sql_cond = " and a.buyer_name=trim('$txt_search_common')";		
		else if(trim($txt_search_by)==3)
			$sql_cond = " and a.job_no like '%".trim($txt_search_common)."'";	
		else if(trim($txt_search_by)==4)
			$sql_cond = " and c.acc_po_no like '%".trim($txt_search_common)."%'";
		else if(trim($txt_search_by)==5)
			$sql_cond = " and b.file_no like ".trim($txt_search_common)."";
		else if(trim($txt_search_by)==6)
			$sql_cond = " and b.grouping like '%".trim($txt_search_common)."%'";		
 	}
	if($txt_date_from!="" || $txt_date_to!="") 
	{
		if($db_type==0){$sql_cond .= " and b.shipment_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";}
		if($db_type==2 || $db_type==1){ $sql_cond .= " and b.shipment_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";}
	}
	
	if(trim($company)!="") $sql_cond .= " and a.company_name='$company'";

	$is_projected_po_allow=return_field_value("production_entry","variable_settings_production","variable_list=58 and company_name=$company");
    $projected_po_cond = ($is_projected_po_allow==2) ? " and b.is_confirmed=1" : "";
    
	/*$qty_source_cond="";
	if($qty_source!=0)
	{
		$qty_source_cond="and b.id in(select po_break_down_id from pro_garments_production_mst where production_type='$qty_source' and status_active=1 and is_deleted=0)";
	}*/

	if(trim($txt_search_by)==4 && trim($txt_search_common)!="")
	{
		$sql = "SELECT b.id, a.order_uom, a.buyer_name, a.company_name, a.total_set_qnty, a.set_break_down, a.job_no, a.style_ref_no, a.gmts_item_id, a.location_name, b.shipment_date, b.po_number,b.file_no,b.grouping, b.po_quantity, b.plan_cut
			from wo_po_details_master a, wo_po_break_down b, wo_po_acc_po_info c
			where a.id = b.job_id and b.id=c.po_break_down_id and a.status_active=1 and  a.is_deleted=0 and b.status_active in(1)  and  b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.garments_nature=$garments_nature $sql_cond $projected_po_cond   group by b.id, a.order_uom, a.buyer_name, a.company_name, a.total_set_qnty, a.set_break_down, a.job_no, a.style_ref_no, a.gmts_item_id, a.location_name, b.shipment_date, b.po_number, b.file_no, b.grouping, b.po_quantity, b.plan_cut order by b.shipment_date DESC";
	}
	else
	{
 		$sql = "SELECT b.id, a.order_uom, a.buyer_name, a.company_name, a.total_set_qnty, a.set_break_down, a.job_no, a.style_ref_no, a.gmts_item_id, a.location_name, b.shipment_date, b.po_number,b.file_no,b.grouping, b.po_quantity, b.plan_cut
		 from wo_po_details_master a, wo_po_break_down b 
		 where a.id = b.job_id and a.status_active=1 and b.status_active in(1) and a.is_deleted=0  and  b.is_deleted=0 and a.garments_nature=$garments_nature 
		 $sql_cond $projected_po_cond order by b.shipment_date DESC"; 
	}
	
	   //echo $sql;die;
	$result = sql_select($sql);
	$poIdArr = array();
	foreach ($result as $val) 
	{
		$poIdArr[$val[csf('id')]] = $val[csf('id')];
	}

	$poIds = implode(",", $poIdArr);
	if($poIds !="")
	{
		$po_cond="";
		if(count($poIdArr)>999)
		{
			$chunk_arr=array_chunk($poIdArr,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",", $val);
				if($po_cond=="") $po_cond.=" and ( po_break_down_id in ($ids) ";
				else
					$po_cond.=" or   po_break_down_id in ($ids) "; 
			}
			$po_cond.=") ";

		}
		else
		{
			$po_cond.=" and po_break_down_id in ($poIds) ";
		}
	}

 	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

	//$po_country_arr=return_library_array( "select po_break_down_id, $select_field"."_concat(distinct(country_id)) as country from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 group by po_break_down_id",'po_break_down_id','country');
	
	$po_country_data_arr=array();
	$country_sql=sql_select( "SELECT po_break_down_id, item_number_id, country_id, pack_type, sum(order_quantity) as po_qty, sum(plan_cut_qnty) as plan_cut_qty from wo_po_color_size_breakdown where status_active=1 and  is_deleted=0 $po_cond group by po_break_down_id, item_number_id, country_id, pack_type"); 
	
	foreach($country_sql as $row)
	{
		//$po_country_arr[$row[csf('po_break_down_id')]].=$row[csf('country_id')].',';
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('pack_type')]]['po_qty']=$row[csf('po_qty')];
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('pack_type')]]['plan_cut_qty']=$row[csf('plan_cut_qty')];
	}
	unset($country_sql);
	
	$qty_source=0;
	if($variable_qty_source_packing==1) $qty_source=7; //Iron Output
	else if($variable_qty_source_packing==2) $qty_source=11;//Poly Output
	
	// $total_entry_qty_data_arr=array();
	// $total_entry_qty=sql_select( "select po_break_down_id, item_number_id, country_id, sum(production_quantity) as production_quantity, pack_type from pro_garments_production_mst where status_active=1 and is_deleted=0 and production_type=8 $po_cond group by po_break_down_id, item_number_id, country_id, pack_type");
	
	// // die('go to hell');
	// foreach($total_entry_qty as $row)
	// {
	// 	$total_entry_qty_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('pack_type')]]=$row[csf('production_quantity')];
	// }
	$total_entry_qty_data_arr=array();
	$total_entry_qty=sql_select( "select po_break_down_id, item_number_id, country_id, 
	SUM (CASE WHEN production_type = 8 THEN production_quantity ELSE 0 END) AS finishing_quantity,
	SUM (CASE WHEN production_type = 7 THEN production_quantity ELSE 0 END) AS iron_qty, 
	pack_type from pro_garments_production_mst where status_active=1 and is_deleted=0 and production_type IN (7, 8) $po_cond group by po_break_down_id, item_number_id, country_id, pack_type");
	
	// die('go to hell');
	foreach($total_entry_qty as $row)
	{
		$total_entry_qty_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('pack_type')]]['finishing_quantity']=$row[csf('finishing_quantity')];
		$total_entry_qty_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('pack_type')]]['iron_qty']=$row[csf('iron_qty')];
	}
	?>
	<div style="width:1270px;">
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="60">Shipment Date</th>
                <th width="100">Order No</th>
                <th width="100">Job</th>
                <th width="90">Buyer</th>
                <th width="100">Style</th>
                <th width="80">File no</th>
                <th width="80">Internal Ref</th>
                <th width="120">Item</th>
                <th width="100">Country</th>
                <th width="80">Order Qty</th>
                <th width="80">Iron Qty</th>
                <th width="80">Total finish Qty</th>
                <th width="80">Balance</th>
                <th>Pack Type</th>
            </thead>
     	</table>
     </div>
     <div style="width:1270px; max-height:240px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1252" class="rpt_table" id="tbl_po_list">
			<?
			$i=1;
            foreach( $result as $row )
            {
				foreach($po_country_data_arr[$row[csf('id')]] as $grmts_item=>$item_data)
				{
					foreach($item_data as $country_id=>$country_data)
					{
						foreach($country_data as $pack_type=>$val)
						{
							if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							
							$po_qnty=$val['po_qty'];
							$plan_cut_qnty=$val['plan_cut_qty'];
							
							?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value(<? echo $row[csf("id")];?>,'<? echo $grmts_item;?>','<? echo $po_qnty;?>','<? echo $plan_cut_qnty;?>','<? echo $country_id;?>','<? echo $pack_type;?>');" > 
									<td width="30" align="center"><?php echo $i; ?></td>
									<td width="60"><p><?php echo change_date_format($row[csf("shipment_date")]);?></p></td>
                                    <td width="100"><p><?php echo $row[csf("po_number")]; ?></p></td>
									<td width="100"><p><?php echo $row[csf("job_no")]; ?></p></td>		
									<td width="90"><p><?php echo $buyer_arr[$row[csf("buyer_name")]]; ?></p></td>	
									<td width="100"><p><?php echo $row[csf("style_ref_no")]; ?></p></td>
									<td width="80"><p><?php echo $row[csf("file_no")]; ?></p></td>
									<td width="80"><p><?php echo $row[csf("grouping")]; ?></p></td>
									<td width="120"><p><?php echo $garments_item[$grmts_item];?></p></td>	
									<td width="100"><p><?php echo $country_library[$country_id]; ?>&nbsp;</p></td>
									<td width="80" align="right"><?php echo $po_qnty; //$po_qnty*$set_qty;?>&nbsp;</td>
									<td width="80" align="right"><?php echo $total_entry_qty_data_arr[$row[csf('id')]][$grmts_item][$country_id][$pack_type]['iron_qty'];?>&nbsp;</td>
									<td width="80" align="right"><?php 
									//echo $total_in_qty=$total_in_qty_data_arr[$row[csf('id')]][$grmts_item][$country_id][$pack_type]; 
									echo $total_in_qty=$total_entry_qty_data_arr[$row[csf('id')]][$grmts_item][$country_id][$pack_type]['finishing_quantity'];
									?> &nbsp;</td>
								   <td width="80" align="right"><?php $balance=$po_qnty-$total_in_qty; echo $balance; ?>&nbsp;</td>
								   <td><?php echo $pack_type;?>&nbsp;</td> 	
								</tr>
							<? 
							$i++;
						}
					}
				}
            }
   		?>
        </table>
    </div>
	<?	
	unset($result);
	exit();	
}

if($action=="wo_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		function set_checkvalue()
		{
			if(document.getElementById('chk_job_wo_po').value==0)
				document.getElementById('chk_job_wo_po').value=1;
			else
				document.getElementById('chk_job_wo_po').value=0;
		}
		function js_set_value(val)
		{
			$("#hidden_sys_data").val(val);
			//$("#hidden_id").val(id);
			parent.emailwindow.hide();
		}
</script>
</head>
<body>
<div style="width:850px;" align="center" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
		<table width="850" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
			<thead>
				<tr>
					<th colspan="6">
						<? echo create_drop_down( "cbo_search_category", 130, $string_search_type,'', 1, "-- Search Catagory --" ); ?>
					</th>
					<th colspan="2" style="text-align:right"><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">WO Without Job</th>
				</tr>
				<tr>
					<th width="120">Buyer Name</th>
					<th width="130">Supplier Name</th>
					<th width="100">WO No</th>
					<th width="100">Job No</th>
                    <th width="100">Style Ref.</th>
					<th width="130" colspan="2"> WO Date Range</th>
					<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('searchorderfrm_1','search_div','','','','');"  /></th>
				</tr>
			</thead>
			<tbody>
				<tr class="general">
				<td><?=create_drop_down( "cbo_buyer_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", "", "",0); ?></td>
				<td><?=create_drop_down( "cbo_supplier_name", 130, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in (2,21) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select Supplier --", $service_company_id, "",0 ); 
				//echo "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.tag_company=$company_id and b.party_type in (2,21) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name";
				
				?></td>
                <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:90px"></td>
                
                <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:90px"></td>
                <td><input name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:90px"></td>
                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date"/></td>
                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date" /> </td>
                <td>
                    <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company_id; ?>+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_style_ref').value+'_'+'<? echo $txt_job_no; ?>', 'create_wo_search_list_view', 'search_div', 'finishing_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:100px;" />
                </td>
            </tr>
            <tr>
                <td align="center" valign="middle" colspan="8">
                    <?=load_month_buttons(1);  ?>
                    <input type="hidden" id="hidden_sys_data" value="hidden_sys_data" />
                </td>
            </tr>
        </tbody>
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

if($action=="create_wo_search_list_view")
{
	$ex_data = explode("_",$data);
	$supplier = $ex_data[0];
	$fromDate = $ex_data[1];
	$toDate = $ex_data[2];
	$company = $ex_data[3];
	$buyer_val=$ex_data[4];
	$search_category=$ex_data[5];
	$booking_prifix=$ex_data[6];
	$job_prifix=$ex_data[7];
	$year_selection=$ex_data[8];
	$chk_job_wo_po=trim($ex_data[9]);
	$style_ref=$ex_data[10];
	$jobno=$ex_data[11];
		
	if( $supplier!=0 )  $supplier="and a.supplier_id='$supplier'"; else  $supplier="";
	if( $company!=0 )  $company=" and a.company_id='$company'"; else  $company="";
	if( $buyer_val!=0 )  $buyer_cond="and d.buyer_name='$buyer_val'"; else  $buyer_cond="";
	
	$booking_year_cond=" and to_char(a.insert_date,'YYYY')=$ex_data[8]";
	$year_cond=" and to_char(d.insert_date,'YYYY')=$ex_data[8]";
	if( $fromDate!=0 && $toDate!=0 ) $sql_cond= "and a.booking_date  between '".change_date_format($fromDate,'mm-dd-yyyy','/',1)."' and '".change_date_format($toDate,'mm-dd-yyyy','/',1)."'";

	if($search_category==0 || $search_category==4)
	{
		if (str_replace("'","",$job_prifix)!="") $job_cond=" and d.job_no_prefix_num like '%$job_prifix%' $year_cond "; else  $job_cond="";
		if (str_replace("'","",$booking_prifix)!="") $booking_cond=" and a.subcon_wo_suffix_num like '%$booking_prifix%'  $booking_year_cond  "; else $booking_cond="";
	}
	else if($search_category==1)
	{
		if (str_replace("'","",$job_prifix)!="") $job_cond=" and d.job_no_prefix_num ='$job_prifix' "; else  $job_cond="";
		if (str_replace("'","",$booking_prifix)!="") $booking_cond=" and a.subcon_wo_suffix_num ='$booking_prifix'   "; else $booking_cond="";
	}
	else if($search_category==2)
	{
		if (str_replace("'","",$job_prifix)!="") $job_cond=" and d.job_no_prefix_num like '$job_prifix%'  $year_cond"; else  $job_cond="";
		if (str_replace("'","",$booking_prifix)!="") $booking_cond=" and a.subcon_wo_suffix_num like '$booking_prifix%'  $booking_year_cond  "; else $booking_cond="";
	}
	else if($search_category==3)
	{
		if (str_replace("'","",$job_prifix)!="") $job_cond=" and d.job_no_prefix_num like '%$job_prifix'  $year_cond"; else  $job_cond="";
		if (str_replace("'","",$booking_prifix)!="") $booking_cond=" and a.subcon_wo_suffix_num like '%$booking_prifix'  $booking_year_cond  "; else $booking_cond="";
	}

	if($db_type==0) $select_year="year(a.insert_date) as year"; else $select_year="to_char(a.insert_date,'YYYY') as year";
	if($chk_job_wo_po==1)
	{
		$sql = "select a.id, a.subcon_wo_suffix_num, a.SUCON_WO_NO, a.company_id, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode, a.source, a.attention, $select_year, 0 as job_no_id, null as job_no, 0 as buyer_name, null as po_number
		from subcon_wo_mst a
		where a.status_active=1 and a.is_deleted=0 and a.entry_form=643 and a.id not in(select mst_id from subcon_wo_dtls where job_no_id>0 and entry_form=643 and status_active=1 and  is_deleted=0) $company $supplier  $sql_cond  $booking_cond order by a.id DESC";
	}
	else
	{
		$sql = "select a.id, a.subcon_wo_suffix_num, a.SUCON_WO_NO, a.supplier_id, a.booking_date, a.CLOSING_DATE, a.currency, a.service_sweater, TO_CHAR(a.insert_date,'YYYY') as year, d.buyer_name, LISTAGG(CAST(b.job_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.job_no) as job_no, LISTAGG(CAST(d.style_ref_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY d.style_ref_no) as style_ref_no from subcon_wo_mst a, subcon_wo_dtls b, wo_po_details_master d where a.id=b.mst_id and b.job_no = d.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.pay_mode in (1,2,4) and a.entry_form=643 and b.entry_form=643 and d.job_no='$jobno' $company $supplier $sql_cond $buyer_cond $job_cond $booking_cond $job_ids_cond group by a.id, a.subcon_wo_suffix_num, a.SUCON_WO_NO, a.supplier_id, a.booking_date, a.CLOSING_DATE, a.currency, a.service_sweater, a.insert_date, d.buyer_name order by a.id DESC";
	}
	//echo $sql;
	?>
	<div style="width:850px;" align="center">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="850" class="rpt_table" >
			<thead>
				<th width="30">SL</th>
				<th width="100">WO no</th>
                <th width="50">WO Year</th>
                <th width="70">WO Date</th>
                <th width="140">Service Company</th>
                <th width="140">Buyer Name</th>
				<th width="100">Job No</th>
                <th width="120">Style Ref.</th>
				<th >Closing Date</th>
			</thead>
		</table>
		<div style="width:850px; overflow-y:scroll; max-height:270px;" id="buyer_list_view">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="830" class="rpt_table" id="tbl_list_search" >
				<?
				$supplier_arr=return_library_array("select id, supplier_name from lib_supplier",'id','supplier_name');
				$buyer_arr=return_library_array("select id, buyer_name from lib_buyer",'id','buyer_name');
				$i=1;
				$nameArray=sql_select( $sql );
				$linkingWoArr=array();
				foreach($nameArray as $row)
				{
					$typeofservice=explode(",",$row[csf("service_sweater")]);
					if (in_array(10, $typeofservice)) {
						$linkingWoArr[$row[csf('id')]]=$row[csf('SUCON_WO_NO')];
					}
				}
				//var_dump($nameArray);die;
				foreach ($nameArray as $selectResult)
				{
					if($linkingWoArr[$selectResult[csf('id')]]!="")
					{
						$job_no=implode(",",array_unique(explode(",",$selectResult[csf("job_no")])));
						$style_ref_no=implode(",",array_unique(explode(",",$selectResult[csf("style_ref_no")])));
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$supplier=$supplier_arr[$selectResult[csf('supplier_id')]];
						
						$ref_no=implode(",",array_unique(explode(",",chop($po_ref_arr[$selectResult[csf("id")]],","))));
						?>
						<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i;?>" onClick="js_set_value('<?=$selectResult[csf('id')].'_'.$selectResult[csf('SUCON_WO_NO')]; ?>'); ">
							<td width="30" align="center"><?=$i; ?></td>
							<td width="100" align="center" style="word-break:break-all"><?=$selectResult[csf('SUCON_WO_NO')]; ?></td>
							<td width="50" align="center"><?=$selectResult[csf('year')]; ?></td>
							<td width="70"><?=change_date_format($selectResult[csf('booking_date')]); ?></td>
							<td width="140" style="word-break:break-all"><?=$supplier; ?></td>
							<td width="140" style="word-break:break-all"><?=$buyer_arr[$selectResult[csf('buyer_name')]]; ?></td>
							<td width="100" style="word-break:break-all"><?=$job_no; ?></td>
							<td width="120" style="word-break:break-all"><?=$style_ref_no; ?></td>
							<td><?=change_date_format($selectResult[csf('CLOSING_DATE')]); ?></td>
						</tr>
							<?
						$i++;
					}
				}
				?>
			</table>
		</div>
	</div>
		<?
	exit();
}

if ($action=="service_booking_popup_old")
{
	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);


	$preBookingNos = 0;
	?>

	<script>
		
		function js_set_value(booking_no)
		{
			// alert(booking_no);
			document.getElementById('selected_booking').value=booking_no; //return;
	 	 	parent.emailwindow.hide();
		}
		
	</script>

	</head>
	<body>
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
			<table width="1300" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
				<tr>
					<td align="center" width="100%">
						<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                        	 <input type="text" id="selected_batchDtls" class="text_boxes" style="width:70px" value="<? echo $txt_batch_dtls;?>">
                              <input type="text" id="booking_no" class="text_boxes" style="width:70px" value="">
                              <input type="text" id="booking_id" class="text_boxes" style="width:70px">
                             
                             
							<thead>
								<th  colspan="11">
									<?
									echo create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --",1 );
									?>
								</th>
							</thead>
							<thead>                  
								<th width="150">Company Name</th>
								<th width="150">Supplier Name</th>
								<th width="150">Buyer  Name</th>
								<th width="100">Job  No</th>
								<th width="100">Order No</th>
								<th width="100">Internal Ref.</th>
								<th width="100">File No</th>
								<th width="100">Style No.</th>
								<th width="100">WO No</th>
								<th width="200">Date Range</th>
								<th></th>           
							</thead>
							<tr>
								<td> <input type="hidden" id="selected_booking">
									<? 
									echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name",1, "-- Select Company --", "".$company_id."", "load_drop_down( 'fabric_issue_to_finishing_process_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );",1);
									?>
								</td>
								<td>
									<?php 
									if($cbo_service_source==3)
									{
										echo create_drop_down( "cbo_supplier_name", 152, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 and b.party_type in (21,24,25) group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", "".$supplier_id."", "",1 );
									}
									else
									{
										echo create_drop_down( "cbo_supplier_name", 152, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name", 1, "-- Select --", "".$supplier_id."", "",1 );
									}
									?>
								</td>
								<td id="buyer_td">
									<? 
									echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
									?>
								</td>
								<td>
									<input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:70px">
								</td> 


								<td>
									<input name="txt_order_number" id="txt_order_number" class="text_boxes" style="width:70px">
								</td> 
								<td>
									<input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:70px">
								</td> 
								<td>
									<input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:70px">
								</td> 



								<td>
									<input name="txt_style" id="txt_style" class="text_boxes" style="width:70px">
								</td>
								<td>
									<input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:70px">
								</td> 
								<td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px">
									<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px">
								</td> 
								<td align="center">
									<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('txt_style').value+'_'+<? echo $preBookingNos;?>+'_'+document.getElementById('txt_order_number').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file_no').value+'_'+<? echo $cbo_service_source;?>, 'create_booking_search_list_view', 'search_div', 'finishing_entry_controller','setFilterGrid(\'tbl_list_search\',-1)')" style="width:100px;" /></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td  align="center" height="40" valign="middle"><? echo load_month_buttons(1);  ?>
						</td>
					</tr>
         
   </table>    
   <div style="width:100%; margin-top:5px" id="search_div" align="left"></div>
   
	</form>
	</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=="create_booking_search_list_view_13092023")
{

	$data=explode('_',$data);
	// echo "<pre>";print_r($data);
	if ($data[0]!=0) $company=" and c.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }
    if ($data[1]!=0) $buyer=" and c.buyer_name='$data[1]'"; else $buyer="";
    
    if($db_type==0)
    {
    	if ($data[2]!="" &&  $data[3]!="") $wo_date  = "and a.wo_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $wo_date ="";
    }
    
    if($db_type==2)
    {
    	if ($data[2]!="" &&  $data[3]!="") $wo_date  = "and a.wo_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $wo_date ="";
    }
    //echo $data[8];
    if($data[6]==1)
    {
    	if (str_replace("'","",$data[5])!="") $wo_cond=" and a.sys_number_prefix_num='$data[5]'    "; else  $wo_cond="";
    	if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num='$data[4]'  "; else  $job_cond=""; 
    	if (str_replace("'","",$data[8])!="") $style_cond=" and c.style_ref_no='$data[8]'  "; else  $style_cond=""; 
    }
    if($data[6]==4 || $data[6]==0)
    {
    	if (str_replace("'","",$data[5])!="") $wo_cond=" and a.sys_number_prefix_num like '%$data[5]%'  $booking_year_cond  "; else  $wo_cond="";
    	if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num like '%$data[4]%'  $year_cond  "; else  $job_cond=""; 
    	if (str_replace("'","",$data[8])!="") $style_cond=" and c.style_ref_no like '%$data[8]%'  "; else  $style_cond=""; 
    }
    
    if($data[6]==2)
    {
    	if (str_replace("'","",$data[5])!="") $wo_cond=" and a.sys_number_prefix_num like '$data[5]%'  $booking_year_cond  "; else  $wo_cond="";
    	if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num like '$data[4]%'  $year_cond  "; else  $job_cond=""; 
    	if (str_replace("'","",$data[8])!="") $style_cond=" and c.style_ref_no like '$data[8]%'  "; else  $style_cond=""; 
    }
    
    if($data[6]==3)
    {
    	if (str_replace("'","",$data[5])!="") $wo_cond=" and a.sys_number_prefix_num like '%$data[5]'  $booking_year_cond  "; else  $wo_cond="";
    	if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num like '%$data[4]'  $year_cond  "; else  $job_cond="";
    	if (str_replace("'","",$data[8])!="") $style_cond=" and c.style_ref_no like '%$data[8]'  "; else  $style_cond="";  
    } 

    if ($data[9]!="")
    {
    	foreach(explode(",", $data[9]) as $bok){
    		$bookingnos .= "'".$bok."',";
    	}
    	$bookingnos = chop($bookingnos,",");
		if( $service_source!=1)
		{
    	$preBookingNos_1 = " and a.booking_no not in (".$bookingnos.")";
    	$preBookingNos_2 = " and a.wo_no not in (".$bookingnos.")";
		}
    }
    if ($data[10]!="")
    {
    	$po_number_cond = " and d.po_number = '$data[10]'";  	
    }
    if ($data[11]!="")
    {    	
    	$internal_ref_cond = " and d.grouping = '$data[11]'";
    }
    if ($data[12]!="")
    {
    	$file_cond = " and d.file_no = '$data[12]'";
    }


    $buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
    $comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
    $suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
    // $po_no=return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');
    
    $arr=array (2=>$comp,3=>$conversion_cost_head_array,4=>$buyer_arr,7=>$po_no,8=>$item_category,9=>$fabric_source,10=>$suplier);
         
	$sql= "SELECT a.id, a.sys_number_prefix_num, a.sys_number, a.wo_date, c.company_name, c.buyer_name, c.job_no_prefix_num,c.style_ref_no, c.job_no, b.po_id, d.file_no,d.po_number,d.grouping  
	from garments_service_wo_mst a, garments_service_wo_dtls b, wo_po_details_master c ,wo_po_break_down d 
	where a.id = b.mst_id  and b.po_id=d.id and c.id=d.job_id  $company $buyer $wo_date $wo_cond $style_cond $file_cond $po_number_cond $internal_ref_cond  and  a.status_active=1 and a.is_deleted=0 and b.rate_for=40 $job_cond
	group by a.id, a.sys_number_prefix_num, a.sys_number, a.wo_date, c.company_name, c.buyer_name, c.job_no_prefix_num,c.style_ref_no, c.job_no, b.po_id, d.file_no,d.po_number,d.grouping";    
   	// echo $sql;	
    ?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="770" class="rpt_table">
    	<thead>
    		<tr>
    			<th width="20">SL No.</th>
    			<th width="120">WO No</th>
    			<th width="60">WO Date</th>
    			<th width="80">Company</th>
    			<th width="100">Buyer</th>
    			<th width="50">Job No</th>

    			<th width="70">Internal Ref.</th>
    			<th width="70">File No</th>


    			<th width="100">Style No.</th>
    			<th width="100">PO number</th>
    		</tr>
    	</thead>
    </table>
    <div style="width:1288px; max-height:400px; overflow-y:scroll;" >	 
    	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="770" class="rpt_table" id="tbl_list_search" >  
    		<tbody>
    			<?
    			$result = sql_select($sql);	        
	    		$i=1; 
	            foreach($result as $row)
	            { 					
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
                     <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]."_".$row[csf('sys_number')]; ?>');"> 
                    
						<td width="20"><? echo $i; ?></td>
						<td width="120"><p><? echo $row[csf('sys_number')]; ?></p></td>
						<td width="60"><p><? echo change_date_format($row[csf('wo_date')]); ?></p></td>
						<td width="80"><p><? echo $comp[$row[csf('company_name')]]; ?></p></td>

						<td width="100"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></p></td>
						<td width="50"><p><? echo $row[csf('job_no_prefix_num')]; ?></p></td>


						<td width="70"><p><? echo $row[csf('grouping')]; ?></p></td>
						<td width="70"><p><? echo $row[csf('file_no')]; ?></p></td>

						<td width="100"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
						<td width="100"><p><? echo $row[csf('po_number')]; ?></p></td>
						
					</tr>
					<?
					$i++;    				
    			}
    			?>
    		</tbody>
    	</table>
    </div>
    <script type="text/javascript">
    	setFilterGrid("tbl_list_search",-1);
    </script>
    <?	

    exit();
}

if($action=="populate_data_from_search_popup")
{
	$dataArr = explode("**",$data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	$country_id = $dataArr[2];
	$pack_type = $dataArr[4];
	//echo $dataArr[3];die;
	if( $pack_type=='') $pack_type_cond=''; else $pack_type_cond=" and pack_type='$pack_type'";
	$qty_source=0;
	if($dataArr[3]==3) $qty_source=3; //Sewing Input
	else if($dataArr[3]==5) $qty_source=5;//Sewing Output
	else if($dataArr[3]==67) $qty_source=67;//Iron Output
	$res = sql_select("SELECT a.id, a.po_quantity, a.plan_cut, a.po_number, a.po_quantity, b.company_name, b.buyer_name, b.style_ref_no,b.gmts_item_id, b.order_uom, b.job_no, b.location_name from wo_po_break_down a, wo_po_details_master b where a.job_id=b.id and a.id=$po_id"); 
 
  	foreach($res as $result)
	{
		echo "$('#txt_order_no').val('".$result[csf('po_number')]."');\n";
		echo "$('#hidden_po_break_down_id').val('".$result[csf('id')]."');\n";
		echo "$('#cbo_buyer_name').val('".$result[csf('buyer_name')]."');\n";
		echo "$('#txt_job_no').val('".$result[csf('job_no')]."');\n";
		echo "$('#txt_style_no').val('".$result[csf('style_ref_no')]."');\n";
  		if($qty_source!=0)
   		{
   			$dataArray=sql_select("SELECT SUM(CASE WHEN production_type='$qty_source' THEN production_quantity END) as totalinput,SUM(CASE WHEN production_type=8 THEN production_quantity ELSE 0 END) as totalsewing from pro_garments_production_mst WHERE po_break_down_id=".$result[csf('id')]."  and item_number_id='$item_id' and country_id='$country_id' $pack_type_cond and status_active=1 and is_deleted=0");
		
	 		foreach($dataArray as $row)
			{  
				echo "$('#txt_finish_input_qty').val('".$row[csf('totalinput')]."');\n";
				echo "$('#txt_cumul_finish_qty').val('".$row[csf('totalsewing')]."');\n";
				$yet_to_produced = $row[csf('totalinput')]-$row[csf('totalsewing')];
				echo "$('#txt_yet_to_finish').val('".$yet_to_produced."');\n";
			}
	    }

	    if($qty_source==0)
		{
			$plan_cut_qnty=return_field_value("sum(plan_cut_qnty)","wo_po_color_size_breakdown","po_break_down_id=".$result[csf('id')]." and item_number_id='$item_id' and country_id='$country_id'   and is_deleted=0");
		
			$total_produced = return_field_value("sum(production_quantity)","pro_garments_production_mst","po_break_down_id=".$result[csf('id')]." and item_number_id='$item_id' and country_id='$country_id' and production_type=8 and is_deleted=0");
			echo "$('#txt_finish_input_qty').val('".$plan_cut_qnty."');\n";		
			echo "$('#txt_cumul_finish_qty').attr('placeholder','".$total_produced."');\n";
			echo "$('#txt_cumul_finish_qty').val('".$total_produced."');\n";
			$yet_to_produced = $plan_cut_qnty - $total_produced;
			echo "$('#txt_yet_to_finish').attr('placeholder','".$yet_to_produced."');\n";
			echo "$('#txt_yet_to_finish').val('".$yet_to_produced."');\n";
		}
  	}
	
	$sql_finish=sql_select("select d.prod_id from order_wise_pro_details  d where d.po_breakdown_id=".$po_id." and d.entry_form=25");
	
	
	foreach($sql_finish as $t_value)
	{
		$trimsids[$t_value[csf('prod_id')]]=$t_value[csf('prod_id')];	
	}
	
	if( count($trimsids)<1) $trimsids[0]=0;
	
	//$sql_finish=sql_select("select b.prod_id,c.item_group_id,c.item_description, sum(b.cons_amount)/sum(b.cons_quantity) as avg_rage from inv_receive_master a,inv_transaction b,product_details_master  c where 
	//a.id=b.mst_id and b.prod_id=c.id and  a.entry_form=24 and b.transaction_type=1 and b.prod_id in (select d.prod_id from order_wise_pro_details  d where d.po_breakdown_id=".$po_id." and d.entry_form=25) group by b.prod_id,c.item_group_id,c.item_description");
	
	$sql_finish=sql_select("select b.prod_id,c.item_group_id,c.item_description, sum(b.cons_amount)/sum(b.cons_quantity) as avg_rage from inv_receive_master a,inv_transaction b,product_details_master  c where 
	a.id=b.mst_id and b.prod_id=c.id and a.entry_form=24 and b.transaction_type=1 and b.prod_id in (".implode(",",$trimsids).") group by b.prod_id,c.item_group_id,c.item_description");
	
	$trims_receive_rate_arr=array();
	foreach($sql_finish as $t_rate)
	{
		$trims_receive_rate_arr[$t_rate[csf('item_group_id')]]=$t_rate[csf('avg_rage')];	
	}
	$costing_per_sql=sql_select("select job_no,costing_per,exchange_rate from wo_pre_cost_mst where job_no='".$result[csf('job_no')]."'");
	$exchange_rate=$costing_per_sql[0][csf('exchange_rate')];
	$costing_per=$costing_per_sql[0][csf('costing_per')];
	if($costing_per==1)
	{
		$costing_per_qty=12;
	}
	else if($costing_per==2)
	{
		$costing_per_qty=1;
	}
	else if($costing_per==3)
	{
		$costing_per_qty=24;
	}
	else if($costing_per==4)
	{
		$costing_per_qty=36;
	}
	else if($costing_per==5)
	{
		$costing_per_qty=48;
	}	

	$sql_trims=sql_select("select trim_group,description,cons_dzn_gmts,rate,amount from wo_pre_cost_trim_cost_dtls  where  job_no='".$result[csf('job_no')]."' and status_active=1 and is_deleted=0");
	
	$trims_data='';
	$trim_data_arr=array();
	foreach($sql_trims as $trim_val)
	{
		$trims_item_cons=$trim_val[csf('cons_dzn_gmts')]/$costing_per_qty;
		if($trims_receive_rate_arr[$trim_val[csf('trim_group')]]!="")
		{
			$trims_item_rate=$trims_receive_rate_arr[$trim_val[csf('trim_group')]];
		}
		else
		{
			$trims_item_rate=($trim_val[csf('rate')])*$exchange_rate;
		}
		$trims_item_amount=$trims_item_rate*$trims_item_cons;
		$total_trims_amount+=$trims_item_amount;
		$trims_data.=$trims_item_cons."**".$trims_item_rate."**".$trims_item_amount."##";	
		$trim_data_arr[$trim_val[csf("trim_group")]]['concs']=$trims_item_cons;
		$trim_data_arr[$trim_val[csf("trim_group")]]['rate']=$trims_item_rate;
		$trim_data_arr[$trim_val[csf("trim_group")]]['amount']=$trims_item_amount;
	}
	//print_r($trim_data_arr);die;
	echo "$('#accessoric_data').val('".$total_trims_amount."');\n";
	
	$sql_embl=sql_select("select emb_name,emb_type,cons_dzn_gmts,rate,amount from wo_pre_cost_embe_cost_dtls  where  job_no='".$result[csf('job_no')]."' and status_active=1 and is_deleted=0");
	foreach($sql_embl as $embl_val)
	{
		$embl_item_amount=($embl_val[csf('amount')]/$costing_per_qty)*$exchange_rate;
		$embl_data.=$embl_val[csf('emb_name')]."**".$embl_val[csf('emb_type')]."**".$embl_item_amount."__";
	}
	echo "$('#emblishment_data').val('".$embl_data."');\n";

	$pre_cost_dtls=sql_select("select job_no,comm_cost,commission,lab_test,inspection,cm_cost,freight,currier_pre_cost,certificate_pre_cost 	,common_oh,depr_amor_pre_cost from wo_pre_cost_dtls where job_no='".$result[csf('job_no')]."'");
	foreach($pre_cost_dtls as $pre_val)
	{
		$commercial_cost=($pre_val[csf('comm_cost')]/$costing_per_qty)*$exchange_rate;
		$commision_cost=($pre_val[csf('commission')]/$costing_per_qty)*$exchange_rate;
		$lab_test_cost=($pre_val[csf('lab_test')]/$costing_per_qty)*$exchange_rate;
		$inspection_cost=($pre_val[csf('inspection')]/$costing_per_qty)*$exchange_rate;
		$cm_cost=($pre_val[csf('cm_cost')]/$costing_per_qty)*$exchange_rate;
		$freight_cost=($pre_val[csf('freight')]/$costing_per_qty)*$exchange_rate;
		$currier_cost=($pre_val[csf('currier_pre_cost')]/$costing_per_qty)*$exchange_rate;
		$cirtificate_cost=($pre_val[csf('certificate_pre_cost')]/$costing_per_qty)*$exchange_rate;
		$commision_cost=($pre_val[csf('commission')]/$costing_per_qty)*$exchange_rate;
		$operating_cost=($pre_val[csf('common_oh')]/$costing_per_qty)*$exchange_rate;
		$depriciation_cost=($pre_val[csf('depr_amor_pre_cost')]/$costing_per_qty)*$exchange_rate;
		
		$precost_data=$commercial_cost."**".$commision_cost."**".$lab_test_cost."**".$inspection_cost."**".$cm_cost."**".$freight_cost."**".$currier_cost."**".$cirtificate_cost."**".$operating_cost."**".$depriciation_cost;
	}
	echo "$('#precost_data').val('".$precost_data."');\n";
 	exit();	
}

if($action=="gross_level_entry")
{
	$dataArr = explode("**",$data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	$job_no= $dataArr[2];
	
	//############################## Knit Finish Fabric Rate #########################################################################
	if($db_type==2)
	{
		$sql_finish_issue=sql_select("select listagg(a.body_part_id,',') within group(order by a.body_part_id) as body_part, listagg(a.prod_id,',') within group(order by a.prod_id) as product_id  from inv_finish_fabric_issue_dtls a,order_wise_pro_details b where a.trans_id=b.trans_id and b.po_breakdown_id=$po_id and a.gmt_item_id=$item_id and b.entry_form in (18,71)");
	}
	else
	{
		$sql_finish_issue=sql_select("select group_concat(a.body_part_id) as body_part, group_concat(a.prod_id) as product_id  from inv_finish_fabric_issue_dtls a,order_wise_pro_details b where a.trans_id=b.trans_id and b.po_breakdown_id=$po_id and a.gmt_item_id=$item_id and b.entry_form in (18,71)");
		
	}
	foreach($sql_finish_issue as $issue_val)
	{
		if($issue_val[csf('body_part')]=="") $issue_val[csf('body_part')]=0; else $issue_val[csf('body_part')]=$issue_val[csf('body_part')];
		if($issue_val[csf('product_id')]=="") $issue_val[csf('product_id')]=0; else $issue_val[csf('product_id')]=$issue_val[csf('product_id')];
		$body_part_id=$issue_val[csf('body_part')];
		$product_id=$issue_val[csf('product_id')];
	}
	
	$sql_finish_receive=sql_select("select a.fabric_description_id,a.body_part_id,sum(a.amount)/sum(a.receive_qnty) as ave_rate  from pro_finish_fabric_rcv_dtls a, order_wise_pro_details b where a.trans_id=b.trans_id and b.po_breakdown_id=$po_id and a.prod_id in($product_id) and a.body_part_id in($body_part_id) and b.entry_form in (37,68) group by a.fabric_description_id,a.body_part_id");
	$finish_rate=array();
	foreach($sql_finish_receive as $f_val)
	{
		$finish_rate[$f_val[csf('body_part_id')]][$f_val[csf('fabric_description_id')]]=number_format($f_val[csf('ave_rate')],4,".","");
	}
	
	//############################## Woven Finish Fabric Rate #########################################################################
	if($db_type==2)
	{
		$sql_woven_finish_issue=sql_select("select listagg(a.body_part_id,',') within group(order by a.body_part_id) as body_part, listagg(a.prod_id,',') within group(order by a.prod_id) as product_id  from inv_transaction a,order_wise_pro_details b where a.id=b.trans_id and b.po_breakdown_id=$po_id and a.gmt_item_id=$item_id and b.entry_form=19");
	}
	else
	{
		$sql_woven_finish_issue=sql_select("select group_concat(a.body_part_id) as body_part, group_concat(a.prod_id) as product_id  from inv_transaction a,order_wise_pro_details b where a.id=b.trans_id and b.po_breakdown_id=$po_id and a.gmt_item_id=$item_id and b.entry_form=19");
	}
	
	foreach($sql_woven_finish_issue as $woven_val)
	{
		if($woven_val[csf('body_part')]=="") $woven_val[csf('body_part')]=0; else $woven_val[csf('body_part')]=$woven_val[csf('body_part')];
		if($woven_val[csf('product_id')]=="") $woven_val[csf('product_id')]=0; else $woven_val[csf('product_id')]=$woven_val[csf('product_id')];
		
		$woven_body_part_id=$woven_val[csf('body_part')];
		$woven_product_id=$woven_val[csf('product_id')];
	}

	$sql_woven_finish_receive=sql_select( "select c.detarmination_id,a.body_part_id,sum(a.cons_amount)/sum(a.cons_quantity) as ave_rate  from inv_transaction a, order_wise_pro_details b,product_details_master  c where a.id=b.trans_id and b.po_breakdown_id=$po_id and a.prod_id in($woven_product_id) and a.body_part_id in($woven_body_part_id) and b.entry_form=17 and b.prod_id =c.id group by c.detarmination_id,a.body_part_id");
	$woven_finish_rate=array();
	foreach($sql_woven_finish_receive as $w_val)
	{
		$woven_finish_rate[$w_val[csf('body_part_id')]][$w_val[csf('detarmination_id')]]=number_format($w_val[csf('ave_rate')],4,".","");
	}
	
	// ################################ Other Process loss ######################################################################################
	
	$processloss_sql=sql_select("select sum(process_loss) as process_loss,mst_id from conversion_process_loss   where  process_id in(120,121,122,123,124,130,131) group by mst_id having (sum(process_loss)>0) ");
	$proceloss_arr=array();
	foreach($processloss_sql as $value)
	{
		$proceloss_arr[$value[csf('mst_id')]]=$value[csf('process_loss')];
	}
	
	//##########################################################################################################################################
	
	
	$costing_per=return_field_value("costing_per","wo_pre_cost_mst","job_no='".$job_no."'","costing_per");
	if($costing_per==1)
	{
		$costing_per_qty=12;
	}
	else if($costing_per==2)
	{
		$costing_per_qty=1;
	}
	else if($costing_per==3)
	{
		$costing_per_qty=24;
	}
	else if($costing_per==4)
	{
		$costing_per_qty=36;
	}
	else if($costing_per==5)
	{
		$costing_per_qty=48;
	}
	
	$color_size_qty_arr=array();
	$color_size_sql=sql_select ("SELECT  po_break_down_id,item_number_id,size_number_id,color_number_id,sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where  is_deleted=0   and  po_break_down_id in (".str_replace("'","",$po_id).") and item_number_id=$item_id
	group by po_break_down_id,item_number_id,size_number_id,color_number_id");
	foreach($color_size_sql as $s_id)
	{
		$color_size_qty_arr[$s_id[csf('color_number_id')]][$s_id[csf('size_number_id')]]+=$s_id[csf('plan_cut_qnty')]; 
	}
	
	$sql_sewing=sql_select("SELECT a.fab_nature_id,a.lib_yarn_count_deter_id ,b.po_break_down_id,a.item_number_id,a.body_part_id, b.color_number_id, b.gmts_sizes,sum(b.cons) AS conjumction FROM wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls a WHERE a.id = b.pre_cost_fabric_cost_dtls_id  AND b.po_break_down_id in (".str_replace("'","",$po_id).") and a.item_number_id=$item_id
	and b.cons!=0 GROUP BY a.fab_nature_id,a.lib_yarn_count_deter_id ,b.po_break_down_id,a.item_number_id,a.body_part_id,b.color_number_id,b.gmts_sizes");
	
	$con_per_dzn=0;
	$po_item_qty_arr=array();
	$color_size_conjumtion=array();
	$fabric_nature_arr=array();
	foreach($sql_sewing as $row_sew)
	{
		$fabric_nature_arr[$row_sew[csf('color_number_id')]][$row_sew[csf('body_part_id')]][$row_sew[csf('lib_yarn_count_deter_id')]]=$row_sew[csf('fab_nature_id')];
		$color_size_conjumtion[$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]][$row_sew[csf('body_part_id')]][$row_sew[csf('lib_yarn_count_deter_id')]]['conjum']=str_replace("'","",$row_sew[csf("conjumction")]);
			
		$color_size_conjumtion[$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]][$row_sew[csf('body_part_id')]][$row_sew[csf('lib_yarn_count_deter_id')]]['plan_cut_qty']=$color_size_qty_arr[$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]]; 
		$po_item_qty_arr[$row_sew[csf('color_number_id')]][$row_sew[csf('body_part_id')]][$row_sew[csf('lib_yarn_count_deter_id')]]['plan_cut_qty']+=$color_size_qty_arr[$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]];   
	}
			
	
	  foreach($color_size_conjumtion as $c_id=>$c_value)
	  {
		 foreach($c_value as $s_id=>$s_value)
		 {
			 foreach($s_value as $b_id=>$b_value)
			 {
				foreach($b_value as $deter_id=>$deter_value)
			 	{
					 $order_color_size_qty=$deter_value['plan_cut_qty'];
					 $order_qty=$po_item_qty_arr[$c_id][$b_id][$deter_id]['plan_cut_qty'];
					 $order_color_size_qty_per= ($order_color_size_qty/$order_qty)*100;
					 $conjunction_per= ($deter_value['conjum']*$order_color_size_qty_per/100);
					 $fabric_nature=$fabric_nature_arr[$c_id][$b_id][$deter_id];
					 $process_loss=$proceloss_arr[$deter_id];
					 $fabric_used=($conjunction_per*100)/(100-$process_loss);
					 if($fabric_nature==3)
					{
						$con_per_dzn+=$fabric_used*$woven_finish_rate[$b_id][$deter_id];//*$finish_rate[$b_id]
					}
					else
					{
						$con_per_dzn+=$fabric_used*$finish_rate[$b_id][$deter_id];//*$finish_rate[$b_id]
					}
				}
			 }
		 }
	  }
	echo "$('#fabric_data').val('".($con_per_dzn/$costing_per_qty)."');\n";	
}

if($action=="color_and_size_level")
{
	$dataArr = explode("**",$data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	$variableSettings = $dataArr[2];
	$styleOrOrderWisw = $dataArr[3];
	$country_id = $dataArr[4];
	$variableSettingsRej = $dataArr[5];
	$job_no= $dataArr[6];
	$pack_type=$dataArr[8];
	$qty_source=0;
	if($dataArr[7]==3) $qty_source=3; //Wash Complete
	else if($dataArr[7]==5) $qty_source=5;//Sewing Output
	else if($dataArr[7]==67) $qty_source=67;//Iron Output
	
	if( $pack_type=='') $pack_type_cond=''; else $pack_type_cond=" and pack_type='$pack_type'";
	if( $pack_type=='') $pack_typeCond=''; else $pack_typeCond=" and b.pack_type='$pack_type'";
 	$color_library=return_library_array( "SELECT id, color_name from lib_color",'id','color_name');
	$size_library=return_library_array( "SELECT id, size_name from lib_size",'id','size_name');
	if( $pack_type=='') $pack_type_cond=''; else $pack_type_cond=" and pack_type='$pack_type'";
	// ==================== set mst form data ===============================
	$cumulQty_arr=array();
	if($qty_source!=0)
   	{
		
		$dataArray=sql_select("SELECT item_number_id, country_id, SUM(CASE WHEN production_type='$qty_source' THEN production_quantity END) as totalinput,SUM(CASE WHEN production_type=8 THEN production_quantity ELSE 0 END) as totalsewing from pro_garments_production_mst WHERE po_break_down_id=$po_id and status_active=1 and is_deleted=0 group by item_number_id, country_id");
		foreach($dataArray as $row)
		{ 
			$cumulQty_arr[$row[csf('item_number_id')]][$row[csf('country_id')]]['input']=$row[csf('totalinput')];
			$cumulQty_arr[$row[csf('item_number_id')]][$row[csf('country_id')]]['totalsewing']=$row[csf('totalsewing')];
		}
		unset($dataArray);
	}
	
	$plan_cut_qnty=return_field_value("sum(plan_cut_qnty)","wo_po_color_size_breakdown","po_break_down_id=$po_id and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0");
	
	$total_produced = return_field_value("sum(production_quantity)","pro_garments_production_mst","po_break_down_id=$po_id and item_number_id='$item_id' and country_id='$country_id' and production_type=8 and is_deleted=0");
	
	$res = sql_select("SELECT a.id, a.po_quantity, a.plan_cut, a.po_number, a.po_quantity, b.company_name, b.buyer_name, b.style_ref_no,b.gmts_item_id, b.order_uom, b.job_no, b.location_name from wo_po_break_down a, wo_po_details_master b where b.id=a.job_id and a.id=$po_id");  
  	foreach($res as $result)
	{
		echo "$('#txt_order_no').val('".$result[csf('po_number')]."');\n";
		echo "$('#hidden_po_break_down_id').val('".$result[csf('id')]."');\n";
		echo "$('#cbo_buyer_name').val('".$result[csf('buyer_name')]."');\n";
		echo "$('#txt_job_no').val('".$result[csf('job_no')]."');\n";
		echo "$('#txt_style_no').val('".$result[csf('style_ref_no')]."');\n";
  		if($qty_source!=0)
   		{
   			/*$dataArray=sql_select("select SUM(CASE WHEN production_type='$qty_source' THEN production_quantity END) as totalinput,SUM(CASE WHEN production_type=8 THEN production_quantity ELSE 0 END) as totalsewing from pro_garments_production_mst WHERE po_break_down_id=".$result[csf('id')]."  and item_number_id='$item_id' and country_id='$country_id' $pack_type_cond and status_active=1 and is_deleted=0");
		
	 		foreach($dataArray as $row)
			{*/ 
			$totalinput=$cumulQty_arr[$item_id][$country_id]['input'] ;
			$totalsewing=$cumulQty_arr[$item_id][$country_id]['totalsewing'];
			
				echo "$('#txt_finish_input_qty').val('".$totalinput."');\n";
				echo "$('#txt_cumul_finish_qty').val('".$totalsewing."');\n";
				$yet_to_produced = $totalinput-$totalsewing;
				echo "$('#txt_yet_to_finish').val('".$yet_to_produced."');\n";
			//}
	    }

	    if($qty_source==0)
		{
			//$plan_cut_qnty=return_field_value("sum(plan_cut_qnty)","wo_po_color_size_breakdown","po_break_down_id=".$result[csf('id')]." and item_number_id='$item_id' and country_id='$country_id'   and is_deleted=0");
		
			//$total_produced = return_field_value("sum(production_quantity)","pro_garments_production_mst","po_break_down_id=".$result[csf('id')]." and item_number_id='$item_id' and country_id='$country_id' and production_type=8 and is_deleted=0");
			echo "$('#txt_finish_input_qty').val('".$plan_cut_qnty."');\n";		
			echo "$('#txt_cumul_finish_qty').attr('placeholder','".$total_produced."');\n";
			echo "$('#txt_cumul_finish_qty').val('".$total_produced."');\n";
			$yet_to_produced = $plan_cut_qnty - $total_produced;
			echo "$('#txt_yet_to_finish').attr('placeholder','".$yet_to_produced."');\n";
			echo "$('#txt_yet_to_finish').val('".$yet_to_produced."');\n";
		}
  	}
	
	$sql_finish=sql_select("SELECT d.prod_id from order_wise_pro_details  d where d.po_breakdown_id=".$po_id." and d.entry_form=25");
	
	
	foreach($sql_finish as $t_value)
	{
		$trimsids[$t_value[csf('prod_id')]]=$t_value[csf('prod_id')];	
	}
	
	if( count($trimsids)<1) $trimsids[0]=0;
	
	//$sql_finish=sql_select("select b.prod_id,c.item_group_id,c.item_description, sum(b.cons_amount)/sum(b.cons_quantity) as avg_rage from inv_receive_master a,inv_transaction b,product_details_master  c where 
	//a.id=b.mst_id and b.prod_id=c.id and  a.entry_form=24 and b.transaction_type=1 and b.prod_id in (select d.prod_id from order_wise_pro_details  d where d.po_breakdown_id=".$po_id." and d.entry_form=25) group by b.prod_id,c.item_group_id,c.item_description");
	
	$sql_finish=sql_select("SELECT b.prod_id,c.item_group_id,c.item_description, sum(b.cons_amount)/sum(b.cons_quantity) as avg_rage from inv_receive_master a,inv_transaction b,product_details_master  c where 
	a.id=b.mst_id and b.prod_id=c.id and a.entry_form=24 and b.transaction_type=1 and b.prod_id in (".implode(",",$trimsids).") group by b.prod_id,c.item_group_id,c.item_description");
	
	$trims_receive_rate_arr=array();
	foreach($sql_finish as $t_rate)
	{
		$trims_receive_rate_arr[$t_rate[csf('item_group_id')]]=$t_rate[csf('avg_rage')];	
	}
	$costing_per_sql=sql_select("SELECT job_no,costing_per,exchange_rate from wo_pre_cost_mst where job_no='".$result[csf('job_no')]."'");
	$exchange_rate=$costing_per_sql[0][csf('exchange_rate')];
	$costing_per=$costing_per_sql[0][csf('costing_per')];
	if($costing_per==1)
	{
		$costing_per_qty=12;
	}
	else if($costing_per==2)
	{
		$costing_per_qty=1;
	}
	else if($costing_per==3)
	{
		$costing_per_qty=24;
	}
	else if($costing_per==4)
	{
		$costing_per_qty=36;
	}
	else if($costing_per==5)
	{
		$costing_per_qty=48;
	}	

	$sql_trims=sql_select("SELECT trim_group,description,cons_dzn_gmts,rate,amount from wo_pre_cost_trim_cost_dtls  where  job_no='".$result[csf('job_no')]."' and status_active=1 and is_deleted=0");
	
	$trims_data='';
	$trim_data_arr=array();
	foreach($sql_trims as $trim_val)
	{
		$trims_item_cons=$trim_val[csf('cons_dzn_gmts')]/$costing_per_qty;
		if($trims_receive_rate_arr[$trim_val[csf('trim_group')]]!="")
		{
			$trims_item_rate=$trims_receive_rate_arr[$trim_val[csf('trim_group')]];
		}
		else
		{
			$trims_item_rate=($trim_val[csf('rate')])*$exchange_rate;
		}
		$trims_item_amount=$trims_item_rate*$trims_item_cons;
		$total_trims_amount+=$trims_item_amount;
		$trims_data.=$trims_item_cons."**".$trims_item_rate."**".$trims_item_amount."##";	
		$trim_data_arr[$trim_val[csf("trim_group")]]['concs']=$trims_item_cons;
		$trim_data_arr[$trim_val[csf("trim_group")]]['rate']=$trims_item_rate;
		$trim_data_arr[$trim_val[csf("trim_group")]]['amount']=$trims_item_amount;
	}
	//print_r($trim_data_arr);die;
	echo "$('#accessoric_data').val('".$total_trims_amount."');\n";
	
	$sql_embl=sql_select("SELECT emb_name,emb_type,cons_dzn_gmts,rate,amount from wo_pre_cost_embe_cost_dtls  where  job_no='".$result[csf('job_no')]."' and status_active=1 and is_deleted=0");
	foreach($sql_embl as $embl_val)
	{
		$embl_item_amount=($embl_val[csf('amount')]/$costing_per_qty)*$exchange_rate;
		$embl_data.=$embl_val[csf('emb_name')]."**".$embl_val[csf('emb_type')]."**".$embl_item_amount."__";
	}
	echo "$('#emblishment_data').val('".$embl_data."');\n";

	$pre_cost_dtls=sql_select("SELECT job_no,comm_cost,commission,lab_test,inspection,cm_cost,freight,currier_pre_cost,certificate_pre_cost 	,common_oh,depr_amor_pre_cost from wo_pre_cost_dtls where job_no='".$result[csf('job_no')]."'");
	foreach($pre_cost_dtls as $pre_val)
	{
		$commercial_cost=($pre_val[csf('comm_cost')]/$costing_per_qty)*$exchange_rate;
		$commision_cost=($pre_val[csf('commission')]/$costing_per_qty)*$exchange_rate;
		$lab_test_cost=($pre_val[csf('lab_test')]/$costing_per_qty)*$exchange_rate;
		$inspection_cost=($pre_val[csf('inspection')]/$costing_per_qty)*$exchange_rate;
		$cm_cost=($pre_val[csf('cm_cost')]/$costing_per_qty)*$exchange_rate;
		$freight_cost=($pre_val[csf('freight')]/$costing_per_qty)*$exchange_rate;
		$currier_cost=($pre_val[csf('currier_pre_cost')]/$costing_per_qty)*$exchange_rate;
		$cirtificate_cost=($pre_val[csf('certificate_pre_cost')]/$costing_per_qty)*$exchange_rate;
		$commision_cost=($pre_val[csf('commission')]/$costing_per_qty)*$exchange_rate;
		$operating_cost=($pre_val[csf('common_oh')]/$costing_per_qty)*$exchange_rate;
		$depriciation_cost=($pre_val[csf('depr_amor_pre_cost')]/$costing_per_qty)*$exchange_rate;
		
		$precost_data=$commercial_cost."**".$commision_cost."**".$lab_test_cost."**".$inspection_cost."**".$cm_cost."**".$freight_cost."**".$currier_cost."**".$cirtificate_cost."**".$operating_cost."**".$depriciation_cost;
	}
	echo "$('#precost_data').val('".$precost_data."');\n";
	// ===================== set mst form data end ======================================
	
	
	if($db_type==2)
	{
	
		$sql_finish_issue=sql_select("SELECT listagg(a.body_part_id,',') within group(order by a.body_part_id) as body_part, listagg(a.prod_id,',') within group(order by a.prod_id) as product_id  from inv_finish_fabric_issue_dtls a,order_wise_pro_details b where a.id=b.dtls_id and a.trans_id=b.trans_id and b.po_breakdown_id=$po_id and a.gmt_item_id=$item_id and b.entry_form in (18,71)");
	}
	else
	{
		$sql_finish_issue=sql_select("SELECT group_concat(a.body_part_id) as body_part, group_concat(a.prod_id) as product_id  from inv_finish_fabric_issue_dtls a,order_wise_pro_details b where a.id=b.dtls_id and a.trans_id=b.trans_id and b.po_breakdown_id=$po_id and a.gmt_item_id=$item_id and b.entry_form in (18,71)");
	}
	
	foreach($sql_finish_issue as $issue_val)
	{
		$body_part_id=$issue_val[csf('body_part')];
		$product_id=$issue_val[csf('product_id')];
	}
	
	if($body_part_id=='') $body_part_id=0;
	if($product_id=='') $product_id=0;
	$product_id=implode(",",array_unique(explode(",",$product_id)));
	$body_part_id=implode(",",array_unique(explode(",",$body_part_id)));
	
	$sql_finish_receive=sql_select("SELECT a.fabric_description_id,a.body_part_id,sum(a.amount)/sum(a.receive_qnty) as ave_rate  from pro_finish_fabric_rcv_dtls a, order_wise_pro_details b where a.trans_id=b.trans_id and b.po_breakdown_id=$po_id and a.prod_id=b.prod_id and b.dtls_id=a.id  and a.prod_id in($product_id) and a.body_part_id in($body_part_id) and b.entry_form in (37,68) group by a.fabric_description_id,a.body_part_id");
	$finish_rate=array();
	foreach($sql_finish_receive as $f_val)
	{
		$finish_rate[$f_val[csf('body_part_id')]][$f_val[csf('fabric_description_id')]]=number_format($f_val[csf('ave_rate')],4,".","");
	}
	
	 
	if($db_type==2)
	{
		$sql_woven_finish_issue=sql_select("SELECT listagg(a.body_part_id,',') within group(order by a.body_part_id) as body_part, listagg(a.prod_id,',') within group(order by a.prod_id) as product_id  from inv_transaction a,order_wise_pro_details b where a.id=b.trans_id and b.po_breakdown_id=$po_id and a.gmt_item_id=$item_id and b.entry_form=19");
	}
	else
	{
		$sql_woven_finish_issue=sql_select("SELECT group_concat(a.body_part_id) as body_part, group_concat(a.prod_id) as product_id  from inv_transaction a,order_wise_pro_details b where a.id=b.trans_id and b.po_breakdown_id=$po_id and a.gmt_item_id=$item_id and b.entry_form=19");
	}
	
	foreach($sql_woven_finish_issue as $woven_val)
	{
		$woven_body_part_id=$woven_val[csf('body_part')];
		$woven_product_id=$woven_val[csf('product_id')];
	}
	
	if($woven_body_part_id=='') $woven_body_part_id=0;
	if($woven_product_id=='') $woven_product_id=0;
	
	$woven_product_id=implode(",",array_unique(explode(",",$woven_product_id)));
	$woven_body_part_id=implode(",",array_unique(explode(",",$woven_body_part_id)));
	
	$sql_woven_finish_receive=sql_select( "SELECT c.detarmination_id,a.body_part_id,sum(a.cons_amount)/sum(a.cons_quantity) as ave_rate  from inv_transaction a, order_wise_pro_details b,product_details_master  c where a.id=b.trans_id and b.po_breakdown_id=$po_id and a.prod_id in($woven_product_id) and a.body_part_id in($woven_body_part_id) and b.entry_form=17 and b.prod_id =c.id group by c.detarmination_id,a.body_part_id");
	$woven_finish_rate=array();
	foreach($sql_woven_finish_receive as $w_val)
	{
		$woven_finish_rate[$w_val[csf('body_part_id')]][$w_val[csf('detarmination_id')]]=number_format($w_val[csf('ave_rate')],4,".","");
	}
	
	 
	
	/*$processloss_sql=sql_select("SELECT sum(process_loss) as process_loss,mst_id from conversion_process_loss   where  process_id in(120,121,122,123,124,130,131) group by mst_id having (sum(process_loss)>0) ");
	$proceloss_arr=array();
	foreach($processloss_sql as $value)
	{
		$proceloss_arr[$value[csf('mst_id')]]=$value[csf('process_loss')];
	}*/
	
	 
	$costing_per=return_field_value("costing_per","wo_pre_cost_mst","job_no='".$job_no."'","costing_per");
	
	if($costing_per==1)
	{
		$costing_per_qty=12;
	}
	else if($costing_per==2)
	{
		$costing_per_qty=1;
	}
	else if($costing_per==3)
	{
		$costing_per_qty=24;
	}
	else if($costing_per==4)
	{
		$costing_per_qty=36;
	}
	else if($costing_per==5)
	{
		$costing_per_qty=48;
	}
	if($qty_source !=0)
	{
		 
		if( $variableSettings==2 ) // color level
		{
			$color_size_qty_arr=array();
			$color_size_sql=sql_select ("SELECT  po_break_down_id,item_number_id,size_number_id,color_number_id,sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where status_active in(1,2,3) and   is_deleted=0   and  po_break_down_id in (".str_replace("'","",$po_id).") and item_number_id=$item_id $pack_type_cond group by po_break_down_id,item_number_id,size_number_id,color_number_id");
			foreach($color_size_sql as $s_id)
			{
				$color_size_qty_arr[$s_id[csf('color_number_id')]][$s_id[csf('size_number_id')]]+=$s_id[csf('plan_cut_qnty')]; 
			}
			
			$sql_sewing=sql_select("SELECT a.fab_nature_id,a.lib_yarn_count_deter_id ,b.po_break_down_id,a.item_number_id,a.body_part_id, b.color_number_id, 
			b.gmts_sizes,sum(b.cons) AS conjumction FROM wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls a WHERE a.id = b.pre_cost_fabric_cost_dtls_id  AND b.po_break_down_id in (".str_replace("'","",$po_id).") and a.item_number_id=$item_id and b.cons!=0 GROUP BY a.fab_nature_id,a.lib_yarn_count_deter_id ,b.po_break_down_id,a.item_number_id,a.body_part_id,b.color_number_id,b.gmts_sizes");

			$con_per_dzn=array();
			$po_item_qty_arr=array();
			$color_size_conjumtion=array();
			$fabric_nature_arr=array();
			$deter_id_arr=array();
			foreach($sql_sewing as $row_sew)
			{
				$fabric_nature_arr[$row_sew[csf('color_number_id')]][$row_sew[csf('body_part_id')]][$row_sew[csf('lib_yarn_count_deter_id')]]=$row_sew[csf('fab_nature_id')];
				$color_size_conjumtion[$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]][$row_sew[csf('body_part_id')]][$row_sew[csf('lib_yarn_count_deter_id')]]['conjum']=str_replace("'","",$row_sew[csf("conjumction")]);
					
				$color_size_conjumtion[$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]][$row_sew[csf('body_part_id')]][$row_sew[csf('lib_yarn_count_deter_id')]]['plan_cut_qty']=$color_size_qty_arr[$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]]; 
				$po_item_qty_arr[$row_sew[csf('color_number_id')]][$row_sew[csf('body_part_id')]][$row_sew[csf('lib_yarn_count_deter_id')]]['plan_cut_qty']+=$color_size_qty_arr[$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]];   
				$deter_id_arr[$row_sew[csf('lib_yarn_count_deter_id')]] = $row_sew[csf('lib_yarn_count_deter_id')];
			}
			
			// ==============================================
			$deter_id_cond = where_con_using_array($deter_id_arr,0,'mst_id');
			$processloss_sql=sql_select("SELECT sum(process_loss) as process_loss,mst_id from conversion_process_loss   where  process_id in(120,121,122,123,124,130,131) $deter_id_cond group by mst_id having (sum(process_loss)>0) ");
			$proceloss_arr=array();
			foreach($processloss_sql as $value)
			{
				$proceloss_arr[$value[csf('mst_id')]]=$value[csf('process_loss')];
			}
			// end processloss====================

			foreach($color_size_conjumtion as $c_id=>$c_value)
		  	{
				foreach($c_value as $s_id=>$s_value)
				{
					 foreach($s_value as $b_id=>$b_value)
					 {
						foreach($b_value as $deter_id=>$deter_value)
						{
							 $fabric_nature=$fabric_nature_arr[$c_id][$b_id][$deter_id];
							 $order_color_size_qty=$deter_value['plan_cut_qty'];
							 $order_qty=$po_item_qty_arr[$c_id][$b_id][$deter_id]['plan_cut_qty'];
							 $order_color_size_qty_per= ($order_color_size_qty/$order_qty)*100;
							 
							 $conjunction_per= ($deter_value['conjum']*$order_color_size_qty_per/100);
							 $process_loss=$proceloss_arr[$deter_id];
							 $fabric_used=($conjunction_per*100)/(100-$process_loss);
							  
							 if($fabric_nature==3)
							 {
								 $con_per_dzn[$c_id]+=$fabric_used*$woven_finish_rate[$b_id][$deter_id];//*$finish_rate[$b_id]
							 }
							 else
							 {	
								$con_per_dzn[$c_id]+=$fabric_used*$finish_rate[$b_id][$deter_id];//*$finish_rate[$b_id]
							 }
						}
					}
				}
		  	}
			
			if($db_type==0)
			{
				$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type='$qty_source' and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=8 and cur.is_deleted=0 ) as cur_production_qnty  from wo_po_color_size_breakdown where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' $pack_type_cond and status_active in(1,2,3) and is_deleted=0  group by color_number_id";
			}
			else
			{	if( $pack_type=='') $packTypeCond=''; else $packTypeCond=" and a.pack_type='$pack_type'";
				$sql = "SELECT a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty, sum(CASE WHEN c.production_type='$qty_source' then b.production_qnty ELSE 0 END) as production_qnty,sum(CASE WHEN c.production_type=8 then b.production_qnty ELSE 0 END) as cur_production_qnty,sum(CASE WHEN c.production_type=8 then b.reject_qty ELSE 0 END) as reject_qty from wo_po_color_size_breakdown a left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id left join pro_garments_production_mst c on c.id=b.mst_id where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' $packTypeCond and a.is_deleted=0 and a.status_active in(1,2,3)  group by a.item_number_id, a.color_number_id";	
				
			}
		}
		else if( $variableSettings==3 ) //color and size level
		{
			
			$color_size_qty_arr=array();
			$color_size_sql=sql_select ("SELECT  po_break_down_id,item_number_id,size_number_id,color_number_id,sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where status_active in(1,2,3) and  is_deleted=0  and  po_break_down_id in (".str_replace("'","",$po_id).") and item_number_id=$item_id $pack_type_cond group by po_break_down_id,item_number_id,size_number_id,color_number_id");
			foreach($color_size_sql as $s_id)
			{
				$color_size_qty_arr[$s_id[csf('color_number_id')]][$s_id[csf('size_number_id')]]+=$s_id[csf('plan_cut_qnty')]; 
			}
			
			$sql_sewing=sql_select("SELECT a.fab_nature_id,a.lib_yarn_count_deter_id ,b.po_break_down_id,a.item_number_id,a.body_part_id, b.color_number_id, b.gmts_sizes,sum(b.cons) AS conjumction	FROM wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls a WHERE a.id = b.pre_cost_fabric_cost_dtls_id  AND b.po_break_down_id in (".str_replace("'","",$po_id).") and a.item_number_id=$item_id and b.cons!=0 GROUP BY a.fab_nature_id,a.lib_yarn_count_deter_id ,b.po_break_down_id,a.item_number_id,a.body_part_id,b.color_number_id,b.gmts_sizes");

			$con_per_dzn=array();
			$po_item_qty_arr=array();
			$color_size_conjumtion=array();
			$deter_id_arr=array();
			foreach($sql_sewing as $row_sew)
			{
				$fabric_nature_arr[$row_sew[csf('color_number_id')]][$row_sew[csf('body_part_id')]][$row_sew[csf('lib_yarn_count_deter_id')]]=$row_sew[csf('fab_nature_id')];
				$color_size_conjumtion[$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]][$row_sew[csf('body_part_id')]][$row_sew[csf('lib_yarn_count_deter_id')]]['conjum']=str_replace("'","",$row_sew[csf("conjumction")]);
				$color_size_conjumtion[$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]][$row_sew[csf('body_part_id')]][$row_sew[csf('lib_yarn_count_deter_id')]]['plan_cut_qty']=$color_size_qty_arr[$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]]; 
				$po_item_qty_arr[$row_sew[csf('color_number_id')]][$row_sew[csf('body_part_id')]][$row_sew[csf('lib_yarn_count_deter_id')]]['plan_cut_qty']+=$color_size_qty_arr[$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]];   
				$deter_id_arr[$row_sew[csf('lib_yarn_count_deter_id')]] = $row_sew[csf('lib_yarn_count_deter_id')];
			}
			// ==============================================
			$deter_id_cond = where_con_using_array($deter_id_arr,0,'mst_id');
			$processloss_sql=sql_select("SELECT sum(process_loss) as process_loss,mst_id from conversion_process_loss   where  process_id in(120,121,122,123,124,130,131) $deter_id_cond group by mst_id having (sum(process_loss)>0) ");
			$proceloss_arr=array();
			foreach($processloss_sql as $value)
			{
				$proceloss_arr[$value[csf('mst_id')]]=$value[csf('process_loss')];
			}
			// end processloss====================
			foreach($color_size_conjumtion as $c_id=>$c_value)
		  	{
				foreach($c_value as $s_id=>$s_value)
				{
					foreach($s_value as $b_id=>$b_value)
					{
						foreach($b_value as $deter_id=>$deter_value)
						{
							// $order_color_size_qty=$deter_value['plan_cut_qty'];
							// $order_qty=$po_item_qty_arr[$c_id][$b_id][$deter_id]['plan_cut_qty'];
							// $order_color_size_qty_per= ($order_color_size_qty/$order_qty)*100;
							 $conjunction_per= ($deter_value['conjum']);//*$order_color_size_qty_per/100);
							 $process_loss=$proceloss_arr[$deter_id];
							 $fabric_used=($conjunction_per*100)/(100-$process_loss);
							 
							 $fabric_nature=$fabric_nature_arr[$c_id][$b_id][$deter_id];
							 if($fabric_nature==3)
							 {
								 $con_per_dzn[$c_id][$s_id]+=$fabric_used*$woven_finish_rate[$b_id][$deter_id];//*$finish_rate[$b_id]
							 }
							 else
							 {
								 $con_per_dzn[$c_id][$s_id]+=$fabric_used*$finish_rate[$b_id][$deter_id];//*$finish_rate[$b_id] 
							 }
						}
					}
				}
		  	}
		
			//print_r($con_per_dzn);die;		
			$dtlsData = sql_select("SELECT a.color_size_break_down_id,sum(CASE WHEN a.production_type='$qty_source' then a.production_qnty ELSE 0 END) as production_qnty,sum(CASE WHEN a.production_type=8 then a.production_qnty ELSE 0 END) as cur_production_qnty,sum(CASE WHEN a.production_type=8 $pack_typeCond then a.reject_qty ELSE 0 END) as reject_qty  from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in($qty_source,8)  group by a.color_size_break_down_id");
										
			foreach($dtlsData as $row)
			{				  
				$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
				$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
				$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rej']= $row[csf('reject_qty')];
			} 
						
			$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty from wo_po_color_size_breakdown
			where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' $pack_type_cond and is_deleted=0 and status_active in(1,2,3) order by color_number_id, size_order";
		}
		else // by default color and size level
		{
			$dtlsData = sql_select("SELECT a.color_size_break_down_id,sum(CASE WHEN a.production_type='$qty_source' then a.production_qnty ELSE 0 END) as production_qnty,sum(CASE WHEN a.production_type=8 then a.production_qnty ELSE 0 END) as cur_production_qnty,sum(CASE WHEN a.production_type=8 then a.reject_qty ELSE 0 END) as reject_qty	from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in($qty_source,8) $pack_typeCond group by a.color_size_break_down_id");
										
			foreach($dtlsData as $row)
			{				  
				$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
				$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
				$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rej']= $row[csf('reject_qty')];
			} 
			$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty from wo_po_color_size_breakdown
			where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' $pack_type_cond and is_deleted=0 and status_active in(1,2,3) order by color_number_id, size_order";
		}

    }

    else // if preceding process =0 in variable setting then plan cut quantity will show
	{  
		if( $variableSettings==2 ) // color level
		{
			$color_size_qty_arr=array();
			$color_size_sql=sql_select ("SELECT  po_break_down_id,item_number_id,size_number_id,color_number_id,sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where status_active in(1,2,3) and   is_deleted=0   and  po_break_down_id in (".str_replace("'","",$po_id).") and item_number_id=$item_id $pack_type_cond group by po_break_down_id,item_number_id,size_number_id,color_number_id");
			foreach($color_size_sql as $s_id)
			{
				$color_size_qty_arr[$s_id[csf('color_number_id')]][$s_id[csf('size_number_id')]]+=$s_id[csf('plan_cut_qnty')]; 
			}
			
			$sql_sewing=sql_select("SELECT a.fab_nature_id,a.lib_yarn_count_deter_id ,b.po_break_down_id,a.item_number_id,a.body_part_id, b.color_number_id, 
			b.gmts_sizes,sum(b.cons) AS conjumction FROM wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls a WHERE a.id = b.pre_cost_fabric_cost_dtls_id  AND b.po_break_down_id in (".str_replace("'","",$po_id).") and a.item_number_id=$item_id and b.cons!=0 GROUP BY a.fab_nature_id,a.lib_yarn_count_deter_id ,b.po_break_down_id,a.item_number_id,a.body_part_id,b.color_number_id,b.gmts_sizes");

			$con_per_dzn=array();
			$po_item_qty_arr=array();
			$color_size_conjumtion=array();
			$fabric_nature_arr=array();
			$deter_id_arr=array();
			foreach($sql_sewing as $row_sew)
			{
				$fabric_nature_arr[$row_sew[csf('color_number_id')]][$row_sew[csf('body_part_id')]][$row_sew[csf('lib_yarn_count_deter_id')]]=$row_sew[csf('fab_nature_id')];
				$color_size_conjumtion[$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]][$row_sew[csf('body_part_id')]][$row_sew[csf('lib_yarn_count_deter_id')]]['conjum']=str_replace("'","",$row_sew[csf("conjumction")]);
					
				$color_size_conjumtion[$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]][$row_sew[csf('body_part_id')]][$row_sew[csf('lib_yarn_count_deter_id')]]['plan_cut_qty']=$color_size_qty_arr[$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]]; 
				$po_item_qty_arr[$row_sew[csf('color_number_id')]][$row_sew[csf('body_part_id')]][$row_sew[csf('lib_yarn_count_deter_id')]]['plan_cut_qty']+=$color_size_qty_arr[$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]];  
				$deter_id_arr[$row_sew[csf('lib_yarn_count_deter_id')]] = $row_sew[csf('lib_yarn_count_deter_id')];
			}
			// ==============================================
			$deter_id_cond = where_con_using_array($deter_id_arr,0,'mst_id');
			$processloss_sql=sql_select("SELECT sum(process_loss) as process_loss,mst_id from conversion_process_loss   where  process_id in(120,121,122,123,124,130,131) $deter_id_cond group by mst_id having (sum(process_loss)>0) ");
			$proceloss_arr=array();
			foreach($processloss_sql as $value)
			{
				$proceloss_arr[$value[csf('mst_id')]]=$value[csf('process_loss')];
			}
			// end processloss====================
			foreach($color_size_conjumtion as $c_id=>$c_value)
		  	{
				foreach($c_value as $s_id=>$s_value)
				{
					 foreach($s_value as $b_id=>$b_value)
					 {
						foreach($b_value as $deter_id=>$deter_value)
						{
							 $fabric_nature=$fabric_nature_arr[$c_id][$b_id][$deter_id];
							 $order_color_size_qty=$deter_value['plan_cut_qty'];
							 $order_qty=$po_item_qty_arr[$c_id][$b_id][$deter_id]['plan_cut_qty'];
							 $order_color_size_qty_per= ($order_color_size_qty/$order_qty)*100;
							 
							 $conjunction_per= ($deter_value['conjum']*$order_color_size_qty_per/100);
							 $process_loss=$proceloss_arr[$deter_id];
							 $fabric_used=($conjunction_per*100)/(100-$process_loss);
							  
							 if($fabric_nature==3)
							 {
								 $con_per_dzn[$c_id]+=$fabric_used*$woven_finish_rate[$b_id][$deter_id];//*$finish_rate[$b_id]
							 }
							 else
							 {	
								$con_per_dzn[$c_id]+=$fabric_used*$finish_rate[$b_id][$deter_id];//*$finish_rate[$b_id]
							 }
						}
					}
				}
		  	}
			
			$sql = "SELECT a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,sum(b.production_qnty) as production_qnty
				from wo_po_color_size_breakdown a left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id and b.production_type=8
				where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active in(1,2,3)  group by a.item_number_id, a.color_number_id";	

			$sql_plan_cut="SELECT color_number_id, sum(plan_cut_qnty) as quantity from wo_po_color_size_breakdown where po_break_down_id='$po_id' and item_number_id='$item_id' and  country_id='$country_id' and status_active in(1,2,3) and is_deleted=0 group by color_number_id";
			 	foreach(sql_select($sql_plan_cut) as $key=>$value)
			 	{
			 		$plan_cut_arr[$value[csf("color_number_id")]] +=$value[csf("quantity")];
			 	}
				
			
		}
		else if( $variableSettings==3 ) //color and size level
		{				
			
			$color_size_qty_arr=array();
			$color_size_sql=sql_select ("SELECT  po_break_down_id,item_number_id,size_number_id,color_number_id,sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where status_active in(1,2,3) and  is_deleted=0  and  po_break_down_id in (".str_replace("'","",$po_id).") and item_number_id=$item_id $pack_type_cond group by po_break_down_id,item_number_id,size_number_id,color_number_id");
			foreach($color_size_sql as $s_id)
			{
				$color_size_qty_arr[$s_id[csf('color_number_id')]][$s_id[csf('size_number_id')]]+=$s_id[csf('plan_cut_qnty')]; 
			}
			
			$sql_sewing=sql_select("SELECT a.fab_nature_id,a.lib_yarn_count_deter_id ,b.po_break_down_id,a.item_number_id,a.body_part_id, b.color_number_id, b.gmts_sizes,sum(b.cons) AS conjumction	FROM wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls a WHERE a.id = b.pre_cost_fabric_cost_dtls_id  AND b.po_break_down_id in (".str_replace("'","",$po_id).") and a.item_number_id=$item_id and b.cons!=0 GROUP BY a.fab_nature_id,a.lib_yarn_count_deter_id ,b.po_break_down_id,a.item_number_id,a.body_part_id,b.color_number_id,b.gmts_sizes");

			$con_per_dzn=array();
			$po_item_qty_arr=array();
			$color_size_conjumtion=array();
			$deter_id_arr=array();
			foreach($sql_sewing as $row_sew)
			{
				$fabric_nature_arr[$row_sew[csf('color_number_id')]][$row_sew[csf('body_part_id')]][$row_sew[csf('lib_yarn_count_deter_id')]]=$row_sew[csf('fab_nature_id')];
				$color_size_conjumtion[$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]][$row_sew[csf('body_part_id')]][$row_sew[csf('lib_yarn_count_deter_id')]]['conjum']=str_replace("'","",$row_sew[csf("conjumction")]);
				$color_size_conjumtion[$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]][$row_sew[csf('body_part_id')]][$row_sew[csf('lib_yarn_count_deter_id')]]['plan_cut_qty']=$color_size_qty_arr[$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]]; 
				$po_item_qty_arr[$row_sew[csf('color_number_id')]][$row_sew[csf('body_part_id')]][$row_sew[csf('lib_yarn_count_deter_id')]]['plan_cut_qty']+=$color_size_qty_arr[$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]];   
				$deter_id_arr[$row_sew[csf('lib_yarn_count_deter_id')]] = $row_sew[csf('lib_yarn_count_deter_id')];
			}
			// ==============================================
			$deter_id_cond = where_con_using_array($deter_id_arr,0,'mst_id');
			$processloss_sql=sql_select("SELECT sum(process_loss) as process_loss,mst_id from conversion_process_loss   where  process_id in(120,121,122,123,124,130,131) $deter_id_cond group by mst_id having (sum(process_loss)>0) ");
			$proceloss_arr=array();
			foreach($processloss_sql as $value)
			{
				$proceloss_arr[$value[csf('mst_id')]]=$value[csf('process_loss')];
			}
			// end processloss====================
			foreach($color_size_conjumtion as $c_id=>$c_value)
		  	{
				foreach($c_value as $s_id=>$s_value)
				{
					foreach($s_value as $b_id=>$b_value)
					{
						foreach($b_value as $deter_id=>$deter_value)
						{
							// $order_color_size_qty=$deter_value['plan_cut_qty'];
							// $order_qty=$po_item_qty_arr[$c_id][$b_id][$deter_id]['plan_cut_qty'];
							// $order_color_size_qty_per= ($order_color_size_qty/$order_qty)*100;
							 $conjunction_per= ($deter_value['conjum']);//*$order_color_size_qty_per/100);
							 $process_loss=$proceloss_arr[$deter_id];
							 $fabric_used=($conjunction_per*100)/(100-$process_loss);
							 
							 $fabric_nature=$fabric_nature_arr[$c_id][$b_id][$deter_id];
							 if($fabric_nature==3)
							 {
								 $con_per_dzn[$c_id][$s_id]+=$fabric_used*$woven_finish_rate[$b_id][$deter_id];//*$finish_rate[$b_id]
							 }
							 else
							 {
								 $con_per_dzn[$c_id][$s_id]+=$fabric_used*$finish_rate[$b_id][$deter_id];//*$finish_rate[$b_id] 
							 }
						}
					}
				}
		  	}
			
				
			$dtlsData = sql_select("SELECT a.color_size_break_down_id,
										sum(CASE WHEN a.production_type=8 then a.production_qnty ELSE 0 END) as production_qnty
										from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(8) group by a.color_size_break_down_id");
					
			foreach($dtlsData as $row)
			{				  
				$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['cut']= $row[csf('production_qnty')];
			} 
			
			$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty 
			from wo_po_color_size_breakdown
			where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and status_active in(1,2,3) and is_deleted=0  order by color_number_id,size_order"; //color_number_id, id
			
			
		}
		else // by default color and size level
		{
			
				
			$dtlsData = sql_select("SELECT a.color_size_break_down_id,
										sum(CASE WHEN a.production_type=8 then a.production_qnty ELSE 0 END) as production_qnty
										from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(8) group by a.color_size_break_down_id");
					
			foreach($dtlsData as $row)
			{				  
				$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['cut']= $row[csf('production_qnty')];
			} 
			
			$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty 
			from wo_po_color_size_breakdown
			where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and status_active in(1,2,3) and is_deleted=0  order by color_number_id,size_order";//color_number_id, id 
		}
	}	


	
	if($variableSettingsRej!=1)
	{
		$disable="";
	}
	else
	{
		$disable="disabled";
	}
	 
	$colorResult = sql_select($sql);		
	$colorHTML="";
	$colorID='';
	$chkColor = array(); 
	$i=0;$totalQnty=0;
	if($qty_source!=0)
	{
		foreach($colorResult as $color)
		{
			if( $variableSettings==2 ) // color level
			{ 
				$order_rate=$con_per_dzn[$color[csf("color_number_id")]]/$costing_per_qty;
				$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="'.($color[csf("production_qnty")]-$color[csf("cur_production_qnty")]).'" onkeyup="fn_colorlevel_total('.($i+1).')"></td><td><input type="text" name="txtColSizeRej" id="colSizeRej_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="Rej." onkeyup="fn_colorRej_total('.($i+1).') '.$disable.'"><input type="hidden" name="colorSizefabricRate" id="colorSizefabricRate_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" value="'.$order_rate.'"></td></tr>';				
				$totalQnty += $color[csf("production_qnty")]-$color[csf("cur_production_qnty")];
				$colorID .= $color[csf("color_number_id")].",";
			}
			else //color and size level
			{
				if( !in_array( $color[csf("color_number_id")], $chkColor ) )
				{
					if( $i!=0 ) $colorHTML .= "</table></div>";
					$i=0;
					$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_number_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_number_id")].'\', \'\',1)"> <span id="accordion_h'.$color[csf("color_number_id")].'span">+</span>'.$color_library[$color[csf("color_number_id")]].' : <span id="total_'.$color[csf("color_number_id")].'"></span> </h3>';
					$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_number_id")].'">';
					$chkColor[] = $color[csf("color_number_id")];					
				}
				//$index = $color[csf("size_number_id")].$color[csf("color_number_id")];
				$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";
				
				$iss_qnty=$color_size_qnty_array[$color[csf('id')]]['iss'];
				$rcv_qnty=$color_size_qnty_array[$color[csf('id')]]['rcv'];
				$rej_qnty=$color_size_qnty_array[$color[csf('id')]]['rej'];
				//echo 
	
				$order_rate="";
				if( $con_per_dzn[$color[csf("color_number_id")]][$color[csf("size_number_id")]] && $costing_per_qty)
				{
					$order_rate=$con_per_dzn[$color[csf("color_number_id")]][$color[csf("size_number_id")]]/$costing_per_qty;
				}
				$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:80px" placeholder="'.($iss_qnty-$rcv_qnty).'" onkeyup="fn_total('.$color[csf("color_number_id")].','.($i+1).')"><input type="text" name="colorSizeRej" id="colSizeRej_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" placeholder="Rej. Qty" onkeyup="fn_total_rej('.$color[csf("color_number_id")].','.($i+1).')" '.$disable.'><input type="text" name="colorSizefabricRate" id="colorSizefabricRate_'.$color[csf("color_number_id")].($i+1).'" value="'.$order_rate.'" class="text_boxes_numeric" style="width:50px" '.$disable.'><input type="text" name="colorSizePOQnty" id="colorSizePOQnty_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" value="'.$color[csf("order_quantity")].'" readonly disabled></td></tr>';				
			}
			$i++; 
		}

	}
	
	//echo $colorHTML;die; 
	if($qty_source==0)
	{
		foreach($colorResult as $color)
		{
			if( $variableSettings==2 ) // color level
			{ 
				$production_quantity=$plan_cut_arr[$color[csf("color_number_id")]];
				$order_rate=$con_per_dzn[$color[csf("color_number_id")]]/$costing_per_qty;
				$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="'.($production_quantity-$color[csf("production_qnty")]).'" onkeyup="fn_colorlevel_total('.($i+1).')"></td><td><input type="text" name="txtColSizeRej" id="colSizeRej_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="Rej." onkeyup="fn_colorRej_total('.($i+1).') '.$disable.'"><input type="hidden" name="colorSizefabricRate" id="colorSizefabricRate_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" value="'.$order_rate.'"></td></tr>';				
				$totalQnty += $production_quantity-$color[csf("production_qnty")];
				$colorID .= $color[csf("color_number_id")].",";
			}
			else //color and size level
			{
				if( !in_array( $color[csf("color_number_id")], $chkColor ) )
				{
					if( $i!=0 ) $colorHTML .= "</table></div>";
					$i=0;
					$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_number_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_number_id")].'\', \'\',1)">  <span id="accordion_h'.$color[csf("color_number_id")].'span">+</span>'.$color_library[$color[csf("color_number_id")]].' : <span id="total_'.$color[csf("color_number_id")].'"></span></h3>';
					$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_number_id")].'">';
					$chkColor[] = $color[csf("color_number_id")];					
				}
						 $bundle_mst_data="";
						 $bundle_dtls_data="";
					 $tmp_col_size="'".$color_library[$color[csf("color_number_id")]]."__".$size_library[$color[csf("size_number_id")]]."'";
 				//$index = $color[csf("size_number_id")].$color[csf("color_number_id")];
				$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";
				$cut_qnty=$color_size_qnty_array[$color[csf('id')]]['cut'];
				$order_rate="";
				if( $con_per_dzn[$color[csf("color_number_id")]][$color[csf("size_number_id")]] && $costing_per_qty)
				{
					$order_rate=$con_per_dzn[$color[csf("color_number_id")]][$color[csf("size_number_id")]]/$costing_per_qty;
				}
				
 				$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="hidden" name="bundlemst" id="bundle_mst_'.$color[csf("color_number_id")].($i+1).'" value="'.$bundle_mst_data.'"  class="text_boxes_numeric" style="width:100px"  ><input type="hidden" name="bundledtls" id="bundle_dtls_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" value="'.$bundle_dtls_data.'" ><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" placeholder="'.($color[csf("plan_cut_qnty")]-$cut_qnty).'" onkeyup="fn_total('.$color[csf("color_number_id")].','.($i+1).')"><input type="text" name="colorSizeRej" id="colSizeRej_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" placeholder="Rej. Qty" onkeyup="fn_total_rej('.$color[csf("color_number_id")].','.($i+1).')" '.$disable.'><input type="text" name="colorSizefabricRate" id="colorSizefabricRate_'.$color[csf("color_number_id")].($i+1).'" value="'.$order_rate.'" class="text_boxes_numeric" style="width:50px" '.$disable.'></td><td><input type="button" name="button" id="button_'.$color[csf("color_number_id")].($i+1).'" value="Click For Bundle" class="formbutton" style="size:30px;" onclick="openmypage_bandle('.$color[csf("id")].','.$color[csf("color_number_id")].($i+1).','.$tmp_col_size.');" /></td></tr>';				
			}
			
			$i++; 
		}
	}
	if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="70">Color</th><th width="60">Quantity</th><th width="60">Reject</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" class="text_boxes_numeric" style="width:60px" ></th><th><input type="text" id="total_color_rej" class="text_boxes_numeric" style="width:60px" ></th></tr></tfoot></table>'; }
	echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
	$colorList = substr($colorID,0,-1);
	echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
	//#############################################################################################//
	exit();
}

if($action=="show_all_listview")
{

	$location_arr=return_library_array( "SELECT id, location_name from lib_location",'id','location_name');
	$company_arr=return_library_array( "SELECT id, company_name from lib_company",'id','company_name');
	$supplier_arr=return_library_array( "SELECT id, supplier_name from  lib_supplier",'id','supplier_name');
	$sewing_line_arr=return_library_array( "SELECT id, line_name from  lib_sewing_line",'id','line_name');
	
	$dataArr = explode("**",$data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	$country_id = $dataArr[2];
	$job_no= $dataArr[3];	
	?>	 
	<div style="width:100%;">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="100" align="center">Item Name</th>
                <th width="100" align="center">Country</th>
                <th width="75" align="center">Production Date</th>
                <th width="70" align="center">Production Qty</th> 
                <th width="70" align="center">Reject Qty</th>                   
                <th width="80" align="center">Reporting Hour</th>
                <th width="100" align="center">Serving Company</th>
                <th width="100" align="center">Location</th>
                <th width="70" align="center">Floor</th>
				<th width="70" align="center">Wo No.</th>
            </thead>
		</table>
	</div>
	<div style="width:100%;max-height:180px; overflow:y-scroll" id="iron_list_view" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table" id="tbl_list_search">
		<?php  
			$i=1;
			$total_production_qnty=0;
			if($db_type==0)
			{
				$sqlResult =sql_select("SELECT id,po_break_down_id,item_number_id, country_id, production_date, production_quantity, reject_qnty, production_source, TIME_FORMAT( production_hour, '%H:%i' ) as production_hour,serving_company, location,floor_id,wo_order_no from pro_garments_production_mst where po_break_down_id='$po_id' and item_number_id='$item_id' and production_type='8' and status_active=1 and is_deleted=0 order by production_date");
			}
			else
			{
				$sqlResult =sql_select("SELECT id,po_break_down_id,item_number_id, country_id, production_date, production_quantity, reject_qnty, production_source, TO_CHAR(production_hour,'HH24:MI') as production_hour,serving_company, location,floor_id,wo_order_no from pro_garments_production_mst where po_break_down_id='$po_id' and item_number_id='$item_id' and production_type='8' and status_active=1 and is_deleted=0 order by production_date");
			}

			foreach($sqlResult as $selectResult){
				
				if ($i%2==0)  $bgcolor="#E9F3FF";
                else $bgcolor="#FFFFFF";
				$total_production_qnty+=$selectResult[csf('production_quantity')];
				$material_cost_id= return_field_value("id","pro_garments_material_cost","mst_id='".$selectResult[csf('id')]."'");
 		?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data('<? echo $selectResult[csf('id')]."_".$job_no."_".$material_cost_id; ?>','populate_input_form_data','requires/finishing_entry_controller');" > 
				<td width="30" align="center"><? echo $i; ?></td>
                <td width="100" align="center"><p><? echo $garments_item[$selectResult[csf('item_number_id')]]; ?></p></td>
                <td width="100" align="center"><p>
                	<? 
                		echo $country_library[$selectResult[csf('country_id')]]."</br>"; 
                		echo "(".$country_short_name[$selectResult[csf('country_id')]].")";
                	?>        		
                	</p></td>
                <td width="75" align="center"><?php echo change_date_format($selectResult[csf('production_date')]); ?></td>
                <td width="70" align="center"><?php  echo $selectResult[csf('production_quantity')]; ?></td>
                <td width="70" align="center"><?php  echo $selectResult[csf('reject_qnty')]; ?></td>
 			
                <td width="80" align="center"><?php echo $selectResult[csf('production_hour')]; ?></td>
				<?php
                $source= $selectResult[csf('production_source')];
                if($source==3)
                $serving_company= return_field_value("supplier_name","lib_supplier","id='".$selectResult[csf('serving_company')]."'");
                else
                $serving_company= return_field_value("company_name","lib_company","id='".$selectResult[csf('serving_company')]."'");
                ?>	
                <td width="100" align="center"><p><?php echo $serving_company; ?></p></td>
                <?php 
                $location_name= return_field_value("location_name","lib_location","id='".$selectResult[csf('location')]."'");
                $floor_name= return_field_value("floor_name","lib_prod_floor","id='".$selectResult[csf('floor_id')]."'");
                ?>
                <td width="100" align="center"><? echo $location_name; ?></td>
                <td width="70" align="center"><? echo $floor_name; ?></td>
				<td width="70" align="center"><? echo $selectResult['WO_ORDER_NO']; ?></td>
			</tr>
			<?php
			$i++;
			}
			?>
             <!--<tfoot>
            	<tr>
                	<th colspan="3"></th>
                    <th><!? echo $total_production_qnty; ?></th>
                    <th colspan="3"></th>
                </tr>
            </tfoot>-->
		</table>
	</div>

	<!-- ========================== dtls list view end and country list view start ======================== -->
	<? echo "******";?>
		
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="380" class="rpt_table">
        <thead>
            <th width="20">SL</th>
            <th width="90">Item Name</th>
            <th width="80">Country</th>
            <th width="55">Shipment Date</th>
            <th width="45">Order Qty.</th>
            <th width="45">Sew.Out</th>
            <th>Finishing Qty.</th>                    
        </thead>
		<?
		$issue_qnty_arr=sql_select("SELECT a.production_type,a.po_break_down_id, a.item_number_id, a.country_id, b.production_qnty from pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id and a.po_break_down_id='$po_id' and a.production_type in(5,8) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		$issue_data_arr=array();
		foreach($issue_qnty_arr as $row)
		{
			$issue_data_arr[$row[csf("production_type")]][$row[csf("po_break_down_id")]][$row[csf("country_id")]][$row[csf("item_number_id")]]+=$row[csf("production_qnty")];
		}  
		$i=1;
		$sqlResult =sql_select("SELECT po_break_down_id, item_number_id, country_id, max(country_ship_date) as country_ship_date, sum(order_quantity) as order_qnty, sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where po_break_down_id='$po_id' and status_active=1  and is_deleted=0 group by po_break_down_id, item_number_id, country_id order by country_ship_date");
		foreach($sqlResult as $row)
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$out_qnty=$issue_data_arr[5][$row[csf("po_break_down_id")]][$row[csf("country_id")]][$row[csf("item_number_id")]];
			$issue_qnty=$issue_data_arr[8][$row[csf("po_break_down_id")]][$row[csf("country_id")]][$row[csf("item_number_id")]];
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="put_country_data(<? echo $row[csf('po_break_down_id')].",".$row[csf('item_number_id')].",".$row[csf('country_id')].",".$row[csf('order_qnty')].",".$row[csf('plan_cut_qnty')]; ?>);"> 
				<td width="20"><? echo $i; ?></td>
				<td width="100"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
				<td width="80" align="center"><p>
					<? 
						echo $country_library[$row[csf('country_id')]]."</br>";
						echo "(".$country_short_name[$row[csf('country_id')]].")"; 
					?>
				</p></td>
				<td width="55" align="center"><? if($row[csf('country_ship_date')]!="0000-00-00") echo change_date_format($row[csf('country_ship_date')]); ?>&nbsp;</td>
				<td align="right" width="65"><?  echo $row[csf('order_qnty')]; ?></td>
				<td align="right" width="65"><?  echo $out_qnty; ?></td>
                <td align="right"><?  echo $issue_qnty; ?></td>
			</tr>
			<?	
			$i++;
		}
		?>
	</table>
	<?
	exit();
}

if($action=="show_dtls_listview")
{
	$location_arr=return_library_array( "SELECT id, location_name from lib_location",'id','location_name');
	$company_arr=return_library_array( "SELECT id, company_name from lib_company",'id','company_name');
	$supplier_arr=return_library_array( "SELECT id, supplier_name from  lib_supplier",'id','supplier_name');
	$sewing_line_arr=return_library_array( "SELECT id, line_name from  lib_sewing_line",'id','line_name');
	
	$dataArr = explode("**",$data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	$country_id = $dataArr[2];
	$job_no= $dataArr[3];	
	?>	 
	<div style="width:100%;">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="150" align="center">Item Name</th>
                <th width="110" align="center">Country</th>
                <th width="75" align="center">Production Date</th>
                <th width="80" align="center">Production Qty</th> 
                <th width="80" align="center">Reject Qty</th>                   
                <th width="80" align="center">Reporting Hour</th>
                <th width="100" align="center">Serving Company</th>
                <th width="100" align="center">Location</th>
				<th width="100" align="center">Floor</th>
				<th width="100" align="center">Wo No</th>
            </thead>
		</table>
	</div>
	<div style="width:100%;max-height:180px; overflow:y-scroll" id="iron_list_view" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table" id="tbl_list_search">
		<?php  
			$i=1;
			$total_production_qnty=0;
			if($db_type==0)
			{
				$sqlResult =sql_select("SELECT id,po_break_down_id,item_number_id, country_id, production_date, production_quantity, reject_qnty, production_source, TIME_FORMAT( production_hour, '%H:%i' ) as production_hour,serving_company, location,floor_id,wo_order_no from pro_garments_production_mst where po_break_down_id='$po_id' and item_number_id='$item_id' and production_type='8' and status_active=1 and is_deleted=0 order by production_date");
			}
			else
			{
				$sqlResult =sql_select("SELECT id,po_break_down_id,item_number_id, country_id, production_date, production_quantity, reject_qnty, production_source, TO_CHAR(production_hour,'HH24:MI') as production_hour,serving_company, location,floor_id,wo_order_no from pro_garments_production_mst where po_break_down_id='$po_id' and item_number_id='$item_id' and production_type='8' and status_active=1 and is_deleted=0 order by production_date");
			}

			foreach($sqlResult as $selectResult){
				
				if ($i%2==0)  $bgcolor="#E9F3FF";
                else $bgcolor="#FFFFFF";
				$total_production_qnty+=$selectResult[csf('production_quantity')];
				$material_cost_id= return_field_value("id","pro_garments_material_cost","mst_id='".$selectResult[csf('id')]."'");
 		?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data('<? echo $selectResult[csf('id')]."_".$job_no."_".$material_cost_id; ?>','populate_input_form_data','requires/finishing_entry_controller');" > 
				<td width="30" align="center"><? echo $i; ?></td>
                <td width="150" align="center"><p><? echo $garments_item[$selectResult[csf('item_number_id')]]; ?></p></td>
                <td width="110" align="center"><p>
                	<? 
                		echo $country_library[$selectResult[csf('country_id')]]."</br>"; 
                		echo "(".$country_short_name[$selectResult[csf('country_id')]].")";
                	?>        		
                	</p></td>
                <td width="75" align="center"><?php echo change_date_format($selectResult[csf('production_date')]); ?></td>
                <td width="80" align="center"><?php  echo $selectResult[csf('production_quantity')]; ?></td>
                <td width="80" align="center"><?php  echo $selectResult[csf('reject_qnty')]; ?></td>
 			
                <td width="80" align="center"><?php echo $selectResult[csf('production_hour')]; ?></td>
				<?php
                $source= $selectResult[csf('production_source')];
                if($source==3)
                $serving_company= return_field_value("supplier_name","lib_supplier","id='".$selectResult[csf('serving_company')]."'");
                else
                $serving_company= return_field_value("company_name","lib_company","id='".$selectResult[csf('serving_company')]."'");
                ?>	
                <td width="100" align="center"><p><?php echo $serving_company; ?></p></td>
                <?php 
                $location_name= return_field_value("location_name","lib_location","id='".$selectResult[csf('location')]."'");
                ?>
                <td width="100" align="center"><? echo $location_name; ?></td>
				<td width="100" align="center"><? echo $floor_name= return_field_value("floor_name","lib_prod_floor","id='".$selectResult[csf('floor_id')]."'");; ?></td>
				<td width="100" align="center"><? echo $selectResult[csf('wo_order_no')] ?></td>
				
			</tr>
			<?php
			$i++;
			}
			?>
             <!--<tfoot>
            	<tr>
                	<th colspan="3"></th>
                    <th><!? echo $total_production_qnty; ?></th>
                    <th colspan="3"></th>
                </tr>
            </tfoot>-->
		</table>
	</div>
	<?
	exit();
}

if($action=="show_country_listview")
{
	?>	
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="380" class="rpt_table">
        <thead>
            <th width="20">SL</th>
            <th width="90">Item Name</th>
            <th width="80">Country</th>
            <th width="55">Shipment Date</th>
            <th width="45">Order Qty.</th>
            <th width="45">Sew.Out</th>
            <th>Finishing Qty.</th>                    
        </thead>
		<?
		$issue_qnty_arr=sql_select("SELECT a.production_type,a.po_break_down_id, a.item_number_id, a.country_id, b.production_qnty from pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id and a.po_break_down_id='$data' and a.production_type in(5,8) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		$issue_data_arr=array();
		foreach($issue_qnty_arr as $row)
		{
			$issue_data_arr[$row[csf("production_type")]][$row[csf("po_break_down_id")]][$row[csf("country_id")]][$row[csf("item_number_id")]]+=$row[csf("production_qnty")];
		}  
		$i=1;
		$sqlResult =sql_select("SELECT po_break_down_id, item_number_id, country_id, max(country_ship_date) as country_ship_date, sum(order_quantity) as order_qnty, sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where po_break_down_id='$data' and status_active=1  and is_deleted=0 group by po_break_down_id, item_number_id, country_id order by country_ship_date");
		foreach($sqlResult as $row)
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$out_qnty=$issue_data_arr[5][$row[csf("po_break_down_id")]][$row[csf("country_id")]][$row[csf("item_number_id")]];
			$issue_qnty=$issue_data_arr[8][$row[csf("po_break_down_id")]][$row[csf("country_id")]][$row[csf("item_number_id")]];
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="put_country_data(<? echo $row[csf('po_break_down_id')].",".$row[csf('item_number_id')].",".$row[csf('country_id')].",".$row[csf('order_qnty')].",".$row[csf('plan_cut_qnty')]; ?>);"> 
				<td width="20"><? echo $i; ?></td>
				<td width="100"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
				<td width="80" align="center"><p>
					<? 
						echo $country_library[$row[csf('country_id')]]."</br>";
						echo "(".$country_short_name[$row[csf('country_id')]].")"; 
					?>
				</p></td>
				<td width="55" align="center"><? if($row[csf('country_ship_date')]!="0000-00-00") echo change_date_format($row[csf('country_ship_date')]); ?>&nbsp;</td>
				<td align="right" width="65"><?  echo $row[csf('order_qnty')]; ?></td>
				<td align="right" width="65"><?  echo $out_qnty; ?></td>
                <td align="right"><?  echo $issue_qnty; ?></td>
			</tr>
			<?	
			$i++;
		}
		?>
	</table>
	<?
	exit();
}

if($action=="populate_input_form_data")
{
	$data = explode("_",$data);
	if($db_type==0)
	{
		$sqlResult =sql_select("SELECT id, company_id, garments_nature, po_break_down_id, item_number_id, challan_no, country_id,  pack_type, production_source, serving_company, sewing_line, location, produced_by, embel_name, embel_type, production_date, production_quantity, production_source, production_type, entry_break_down_type, break_down_type_rej, TIME_FORMAT( production_hour, '%H:%i' ) as production_hour, sewing_line, supervisor, carton_qty, remarks, floor_id, alter_qnty, spot_qnty, reject_qnty, total_produced, yet_to_produced,wo_order_id,wo_order_no  from pro_garments_production_mst where id='$data[0]' and production_type='8' and status_active=1 and is_deleted=0 order by id");
	}
	else
	{
		$sqlResult =sql_select("SELECT id, company_id, garments_nature, po_break_down_id, item_number_id, challan_no, country_id,  pack_type, production_source, serving_company, sewing_line, location, produced_by, embel_name, embel_type, production_date, production_quantity, production_source, production_type, entry_break_down_type, break_down_type_rej, TO_CHAR(production_hour,'HH24:MI') as production_hour, sewing_line, supervisor, carton_qty, remarks, floor_id, alter_qnty, spot_qnty, reject_qnty, total_produced, yet_to_produced,wo_order_id,wo_order_no  from pro_garments_production_mst where id='$data[0]' and production_type='8' and status_active=1 and is_deleted=0 order by id");	
	}
  	echo "$('#txt_material_id').val('".$data[2]."');\n";
	$is_posted_account=return_field_value("is_posted_account ","pro_garments_material_cost ","id='".$data[2]."'","is_posted_account");
	$dissable='';	
	$company_id=$sqlResult[0][csf('company_id')];
	if($sqlResult[0][csf('production_source')]==1)
	{
		$company=$sqlResult[0][csf('serving_company')];
	}
	else
	{
		$company=$sqlResult[0][csf('company_id')];
	}		 
	$control_and_preceding=sql_select("SELECT is_control,preceding_page_id from variable_settings_production where status_active=1 and is_deleted=0 and variable_list=50 and page_category_id=8 and company_name='$company'");  
	$preceding_process= $control_and_preceding[0][csf("preceding_page_id")];
	echo "$('#hidden_variable_cntl').val('".$control_and_preceding[0][csf('is_control')]."');\n";
	$qty_source=0;
	if($preceding_process==3) $qty_source=3; //Wash Complete
	else if($preceding_process==5) $qty_source=5;//Sewing Output
	else if($preceding_process==67) $qty_source=67;//Iron Output

	
	foreach($sqlResult as $result)
	{ 
		echo "$('#txt_finishing_date').val('".change_date_format($result[csf('production_date')])."');\n";
		echo "$('#cbo_source').val('".$result[csf('production_source')]."');\n";
		echo "load_drop_down( 'requires/finishing_entry_controller', ".$result[csf('production_source')].", 'load_drop_down_source', 'finishing_td' );\n";
		echo "$('#cbo_finish_company').val('".$result[csf('serving_company')]."');\n";
		echo "load_drop_down( 'requires/finishing_entry_controller',".$result[csf('serving_company')].", 'load_drop_down_location', 'location_td' );";
		echo "$('#cbo_location').val('".$result[csf('location')]."');\n";
		echo "load_drop_down( 'requires/finishing_entry_controller', ".$result[csf('location')].", 'load_drop_down_floor', 'floor_td' );\n";
		echo "$('#cbo_floor').val('".$result[csf('floor_id')]."');\n";
		echo "$('#cbo_country_name').val('".$result[csf('country_id')]."');\n";

		if($result[csf('production_source')]==3)
		{
			$company=$sqlResult[0][csf('company_id')];
			$control_and_preceding=sql_select("select is_control,preceding_page_id from variable_settings_production where status_active=1 and is_deleted=0 and variable_list=50 and page_category_id=8 and company_name='$company'");  
			$preceding_process= $control_and_preceding[0][csf("preceding_page_id")];
			echo "$('#hidden_variable_cntl').val('".$control_and_preceding[0][csf('is_control')]."');\n";
			 
		}
 		echo "$('#cbo_produced_by').val('".$result[csf('produced_by')]."');\n";
		echo "$('#txt_reporting_hour').val('".$result[csf('production_hour')]."');\n";
 		echo "$('#txt_finishing_qty').val('".$result[csf('production_quantity')]."');\n";
		echo "$('#txt_carton_qty').val('".$result[csf('carton_qty')]."');\n";
		echo "$('#txt_alter_qnty').val('".$result[csf('alter_qnty')]."');\n";
		echo "$('#txt_spot_qnty').val('".$result[csf('spot_qnty')]."');\n";
		echo "$('#txt_reject_qnty').val('".$result[csf('reject_qnty')]."');\n";
		echo "$('#txt_challan').val('".$result[csf('challan_no')]."');\n";
  		echo "$('#txt_remark').val('".$result[csf('remarks')]."');\n";
  		echo "$('#txt_wo_id').val('".$result[csf('wo_order_id')]."');\n";
  		echo "$('#txt_wo_no').val('".$result[csf('wo_order_no')]."');\n";
		
		if($is_posted_account==1)
		{
			 $disable_for_posted="disabled";
			 echo "$('#txt_finishing_date').attr('disabled','disabled');\n";
			 echo "$('#txt_finishing_qty').attr('disabled','disabled');\n";
			 $msg="Already Posted in Accounts.";
		}
		else
		{
			echo "$('#txt_finishing_date').removeAttr('disabled');\n";
			echo "$('#txt_finishing_qty').removeAttr('disabled');\n";
			$msg="";	
		}
		echo "$('#posted_account_td').text('".$msg."');\n";
		
		$pack_type=$result[csf('pack_type')];
		if($pack_type=='') $pack_typeCond=""; else $pack_typeCond="and pack_type='$pack_type'";
		if($qty_source)
		{
			$dataSql="SELECT SUM(CASE WHEN production_type='$qty_source' THEN production_quantity END) as totalinput,SUM(CASE WHEN production_type=8 THEN production_quantity ELSE 0 END) as totalsewing from pro_garments_production_mst WHERE po_break_down_id=".$result[csf('po_break_down_id')]."  and item_number_id=".$result[csf('item_number_id')]." and country_id=".$result[csf('country_id')]." $pack_typeCond and status_active=1 and is_deleted=0";
		}
		else
		{
			 $dataSql="SELECT SUM(CASE WHEN production_type=8 THEN production_quantity ELSE 0 END) as totalsewing   from pro_garments_production_mst WHERE po_break_down_id=".$result[csf('po_break_down_id')]."  and item_number_id=".$result[csf('item_number_id')]." and country_id=".$result[csf('country_id')]." $pack_typeCond and status_active=1 and is_deleted=0";

		    $dataSql_plan_cut="SELECT  sum(plan_cut_qnty) as totalinput from wo_po_color_size_breakdown  WHERE po_break_down_id=".$result[csf('po_break_down_id')]."  and item_number_id=".$result[csf('item_number_id')]." and country_id=".$result[csf('country_id')]."  and status_active in(1,2,3) and is_deleted=0";
			$dataSql_plan_cut=sql_select($dataSql_plan_cut);
		}
		$dataArray=sql_select($dataSql);
 		foreach($dataArray as $row)
		{  
			echo "$('#txt_finish_input_qty').val('".$row[csf('totalinput')]."');\n";
			echo "$('#txt_cumul_finish_qty').val('".$row[csf('totalsewing')]."');\n";			
			$yet_to_produced = $row[csf('totalinput')]-$row[csf('totalsewing')];
			if($qty_source==0)
			{
				echo "$('#txt_finish_input_qty').val('".$dataSql_plan_cut[0][csf('totalinput')]."');\n";
				$yet_to_produced = $dataSql_plan_cut[0][csf('totalinput')]-$row[csf('totalsewing')];
			}
			echo "$('#txt_yet_to_finish').val('".$yet_to_produced."');\n";
		}
		
		$dft_id=""; $alt_save_data=""; $spt_save_data=""; $altType_id=""; $sptType_id=""; $altpoint_id=""; $sptpoint_id="";$rej_save_data=""; $rejType_id="";$rejpoint_id="";
		$defect_sql=sql_select("select id, po_break_down_id, defect_type_id, defect_point_id, defect_qty from pro_gmts_prod_dft where mst_id='".$result[csf('id')]."' and status_active=1 and is_deleted=0  and production_type='8'");
		foreach($defect_sql as $dft_row)
		{
			if($dft_row[csf('defect_type_id')]==1)
			{
				if($alt_save_data=="") $alt_save_data=$dft_row[csf('id')].'**'.$dft_row[csf('defect_point_id')].'**'.$dft_row[csf('defect_qty')]; else $alt_save_data.=','.$dft_row[csf('id')].'**'.$dft_row[csf('defect_point_id')].'**'.$dft_row[csf('defect_qty')];
				if($altpoint_id=="") $altpoint_id=$dft_row[csf('defect_point_id')]; else $altpoint_id.=','.$dft_row[csf('defect_point_id')];
				$altType_id=$dft_row[csf('defect_type_id')];
			}
			
			if($dft_row[csf('defect_type_id')]==2)
			{
				if($spt_save_data=="") $spt_save_data=$dft_row[csf('id')].'**'.$dft_row[csf('defect_point_id')].'**'.$dft_row[csf('defect_qty')]; else $spt_save_data.=','.$dft_row[csf('id')].'**'.$dft_row[csf('defect_point_id')].'**'.$dft_row[csf('defect_qty')];
				if($sptpoint_id=="") $sptpoint_id=$dft_row[csf('defect_point_id')]; else $sptpoint_id.=','.$dft_row[csf('defect_point_id')];
				$sptType_id=$dft_row[csf('defect_type_id')];
			}
			if($dft_row[csf('defect_type_id')]==3)
			{
				if($rej_save_data=="") $rej_save_data=$dft_row[csf('id')].'**'.$dft_row[csf('defect_point_id')].'**'.$dft_row[csf('defect_qty')]; else $rej_save_data.=','.$dft_row[csf('id')].'**'.$dft_row[csf('defect_point_id')].'**'.$dft_row[csf('defect_qty')];
				if($rejpoint_id=="") $rejpoint_id=$dft_row[csf('defect_point_id')]; else $rejpoint_id.=','.$dft_row[csf('defect_point_id')];
				$rejType_id=$dft_row[csf('defect_type_id')];
			}
		}
		echo "$('#save_data').val('".$alt_save_data."');\n";
		echo "$('#all_defect_id').val('".$altpoint_id."');\n";
		echo "$('#defect_type_id').val('".$altType_id."');\n";
		
		echo "$('#save_dataSpot').val('".$spt_save_data."');\n";
		echo "$('#allSpot_defect_id').val('".$sptpoint_id."');\n";
		echo "$('#defectSpot_type_id').val('".$sptType_id."');\n";

		echo "$('#save_dataRej').val('".$rej_save_data."');\n";
		echo "$('#allRej_defect_id').val('".$rejpoint_id."');\n";
		echo "$('#defectRej_type_id').val('".$rejType_id."');\n";
		
		echo "$('#txt_mst_id').val('".$result[csf('id')]."');\n";
		echo "$('#txt_sys_chln').val('".$result[csf('id')]."');\n";
 		echo "set_button_status(1, permission, 'fnc_finishing_entry',1,1);\n";
		
		 
		$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
		$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');

		$variableSettings = $result[csf('entry_break_down_type')];
		$variableSettingsRej = $result[csf('break_down_type_rej')];
		
		 
		if($pack_type=='') $pack_typeCond=""; else $pack_typeCond="and pack_type='$pack_type'";
		if($pack_type!='') $pack_cond="and b.pack_type='$pack_type'"; else $pack_cond="";
		if($pack_type=='') $packTypecond=""; else $packTypecond="and a.pack_type='$pack_type'";

		if( $variableSettings!=1 ) // gross level
		{ 
			$po_id = $result[csf('po_break_down_id')];
			$item_id = $result[csf('item_number_id')];
			$country_id = $result[csf('country_id')];
			
			$sql_dtls = sql_select("SELECT color_size_break_down_id, production_qnty, reject_qty, size_number_id, color_number_id from  pro_garments_production_dtls a,wo_po_color_size_breakdown b where a.mst_id=$data[0] and a.status_active=1 and a.color_size_break_down_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.status_active in(1,2,3) and b.is_deleted=0 and country_id='$country_id' $pack_cond");	
			foreach($sql_dtls as $row)
			{				  
				if( $variableSettings==2 ) $index = $row[csf('color_number_id')]; else $index = $row[csf('size_number_id')].$color_arr[$row[csf("color_number_id")]].$row[csf('color_number_id')];
			  	$amountArr[$index] = $row[csf('production_qnty')];
				$rejectArr[$index] = $row[csf('reject_qty')];
			}  
			// print_r($amountArr);
			
			 if($db_type==2) 
			 {
			 	$group_con="listagg(a.body_part_id,',') within group(order by a.body_part_id) as body_part, listagg(a.prod_id,',') within group(order by a.prod_id) as product_id";
			 }
			 else
			 {
				$group_con="group_concat(a.body_part_id) as body_part,group_concat(a.prod_id) as product_id"; 
			 }
			 $sql_finish_issue=sql_select("select $group_con  from inv_finish_fabric_issue_dtls a,order_wise_pro_details b where a.trans_id=b.trans_id and b.po_breakdown_id=$po_id and a.gmt_item_id=$item_id and b.entry_form=18");
				
			foreach($sql_finish_issue as $issue_val)
			{
				if($issue_val[csf('body_part')]=="") $issue_val[csf('body_part')]==0;else $issue_val[csf('body_part')]=$issue_val[csf('body_part')];
				if($issue_val[csf('product_id')]=="") $issue_val[csf('product_id')]==0;else $issue_val[csf('product_id')]=$issue_val[csf('product_id')];
				$body_part_id=$issue_val[csf('body_part')];
				$product_id=$issue_val[csf('product_id')];
				
			}
			
			if($body_part_id=='') $body_part_id=0;
			if($product_id=='') $product_id=0;
			
			$sql_finish_receive=sql_select("select a.fabric_description_id,a.body_part_id,sum(a.amount)/sum(a.receive_qnty) as ave_rate  from pro_finish_fabric_rcv_dtls a, order_wise_pro_details b where a.trans_id=b.trans_id and b.po_breakdown_id=$po_id  and a.prod_id=b.prod_id and b.dtls_id=a.id and a.prod_id in($product_id) and a.body_part_id in($body_part_id) and b.entry_form=37 group by a.fabric_description_id,a.body_part_id");
			$finish_rate=array();
			foreach($sql_finish_receive as $f_val)
			{
				$finish_rate[$f_val[csf('body_part_id')]][$f_val[csf('fabric_description_id')]]=number_format($f_val[csf('ave_rate')],4,".","");
			}
			
			$sql_po_id = sql_select("select po_break_down_id from pro_garments_production_mst where id='".$data[0]."'");
			$job_number = sql_select("select job_no_mst from wo_po_break_down where id='".$sql_po_id[0]['PO_BREAK_DOWN_ID']."'");
			// echo $job_number[0]['JOB_NO_MST'];
			$costing_per=return_field_value("costing_per","wo_pre_cost_mst","job_no='".$job_number[0]['JOB_NO_MST']."'","costing_per");
			// $costing_per=return_field_value("costing_per","wo_pre_cost_mst","job_no='".$data[1]."'","costing_per");

			if($costing_per==1)
			{
				$costing_per_qty=12;
			}
			else if($costing_per==2)
			{
				$costing_per_qty=1;
			}
			else if($costing_per==3)
			{
				$costing_per_qty=24;
			}
			else if($costing_per==4)
			{
				$costing_per_qty=36;
			}
			else if($costing_per==5)
			{
				$costing_per_qty=48;
			}

			$color_size_qty_arr=array();
			$color_size_sql=sql_select ("SELECT  po_break_down_id,item_number_id,size_number_id,color_number_id,sum(plan_cut_qnty) as plan_cut_qnty  from wo_po_color_size_breakdown where status_active in(1,2,3) and  is_deleted=0    and  po_break_down_id in (".str_replace("'","",$po_id).") and item_number_id=$item_id $pack_typeCond group by po_break_down_id,item_number_id,size_number_id,color_number_id");
			foreach($color_size_sql as $s_id)
			{
				$color_size_qty_arr[$s_id[csf('color_number_id')]][$s_id[csf('size_number_id')]]+=$s_id[csf('plan_cut_qnty')]; 
			}
			
			if( $variableSettings==2 ) // color level
			{
				$sql_sewing=sql_select("SELECT a.lib_yarn_count_deter_id ,b.po_break_down_id,a.item_number_id,a.body_part_id, b.color_number_id, b.gmts_sizes,sum(b.cons) AS conjumction FROM wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls a WHERE a.id = b.pre_cost_fabric_cost_dtls_id  AND b.po_break_down_id in (".str_replace("'","",$po_id).") and a.item_number_id=$item_id and b.cons!=0 GROUP BY a.lib_yarn_count_deter_id ,b.po_break_down_id,a.item_number_id,a.body_part_id,b.color_number_id,b.gmts_sizes");
				$con_per_dzn=array();
				$po_item_qty_arr=array();
				$color_size_conjumtion=array();
				foreach($sql_sewing as $row_sew)
				{
					$color_size_conjumtion[$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]][$row_sew[csf('body_part_id')]][$row_sew[csf('lib_yarn_count_deter_id')]]['conjum']=str_replace("'","",$row_sew[csf("conjumction")]);
						
					$color_size_conjumtion[$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]][$row_sew[csf('body_part_id')]][$row_sew[csf('lib_yarn_count_deter_id')]]['plan_cut_qty']=$color_size_qty_arr[$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]]; 
					$po_item_qty_arr[$row_sew[csf('color_number_id')]][$row_sew[csf('body_part_id')]][$row_sew[csf('lib_yarn_count_deter_id')]]['plan_cut_qty']+=$color_size_qty_arr[$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]];   
				}
						
				
			  	foreach($color_size_conjumtion as $c_id=>$c_value)
			  	{
					foreach($c_value as $s_id=>$s_value)
					{
						foreach($s_value as $b_id=>$b_value)
						{
							foreach($b_value as $deter_id=>$deter_value)
							{
								 $order_color_size_qty=$deter_value['plan_cut_qty'];
								 $order_qty=$po_item_qty_arr[$c_id][$b_id][$deter_id]['plan_cut_qty'];
								 $order_color_size_qty_per= ($order_color_size_qty/$order_qty)*100;
								 $conjunction_per= ($deter_value['conjum']*$order_color_size_qty_per/100);
								 $con_per_dzn[$c_id]+=$conjunction_per*$finish_rate[$b_id][$deter_id];//*$finish_rate[$b_id]
							}
						}
					}
			 	}
			 
			 	$sql = "SELECT a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,sum(CASE WHEN c.production_type='$qty_source' then b.production_qnty ELSE 0 END) as production_qnty,sum(CASE WHEN c.production_type=8 then b.production_qnty ELSE 0 END) as cur_production_qnty,sum(CASE WHEN c.production_type=8 then b.reject_qty ELSE 0 END) as reject_qty from wo_po_color_size_breakdown a left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id left join pro_garments_production_mst c on c.id=b.mst_id where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' $packTypecond and a.is_deleted=0 and a.status_active in(1,2,3)   group by a.item_number_id, a.color_number_id";	
			 	$sql_plan_cut="SELECT color_number_id, sum(plan_cut_qnty) as quantity from wo_po_color_size_breakdown where po_break_down_id='$po_id' and item_number_id='$item_id' and  country_id='$country_id' and status_active in(1,2,3) and is_deleted=0 group by color_number_id";
			 	foreach(sql_select($sql_plan_cut) as $key=>$value)
			 	{
			 		$plan_cut_arr[$value[csf("color_number_id")]] +=$value[csf("quantity")];
			 	}
			 	
			}
			else if( $variableSettings==3 ) //color and size level
			{
				$sql_sewing=sql_select("SELECT a.lib_yarn_count_deter_id ,b.po_break_down_id,a.item_number_id,a.body_part_id, b.color_number_id, b.gmts_sizes,sum(b.cons) AS conjumction  FROM wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls a
				WHERE a.id = b.pre_cost_fabric_cost_dtls_id  AND b.po_break_down_id in (".str_replace("'","",$po_id).") and a.item_number_id=$item_id and b.cons!=0 GROUP BY a.lib_yarn_count_deter_id ,b.po_break_down_id,a.item_number_id,a.body_part_id,b.color_number_id,b.gmts_sizes");
				//print_r($sql_sewing);die;
				$con_per_dzn=array();
				$po_item_qty_arr=array();
				$color_size_conjumtion=array();
				foreach($sql_sewing as $row_sew)
				{
					$color_size_conjumtion[$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]][$row_sew[csf('body_part_id')]][$row_sew[csf('lib_yarn_count_deter_id')]]['conjum']=str_replace("'","",$row_sew[csf("conjumction")]);
						
					$color_size_conjumtion[$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]][$row_sew[csf('body_part_id')]][$row_sew[csf('lib_yarn_count_deter_id')]]['plan_cut_qty']=$color_size_qty_arr[$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]]; 
					$po_item_qty_arr[$row_sew[csf('color_number_id')]][$row_sew[csf('body_part_id')]][$row_sew[csf('lib_yarn_count_deter_id')]]['plan_cut_qty']+=$color_size_qty_arr[$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]];   
				}
					
			
			  	foreach($color_size_conjumtion as $c_id=>$c_value)
			 	{
					foreach($c_value as $s_id=>$s_value)
					{
						foreach($s_value as $b_id=>$b_value)
						{
							foreach($b_value as $deter_id=>$deter_value)
							{
								// $order_color_size_qty=$deter_value['plan_cut_qty'];
								 //$order_qty=$po_item_qty_arr[$c_id][$b_id][$deter_id]['plan_cut_qty'];
								// $order_color_size_qty_per= ($order_color_size_qty/$order_qty)*100;
								 $conjunction_per= ($deter_value['conjum']);//*$order_color_size_qty_per/100);
								 $con_per_dzn[$c_id][$s_id]+=$conjunction_per*$finish_rate[$b_id][$deter_id];//*$finish_rate[$b_id]
							}
						}
					}
			  	}
				
				if($qty_source)
				{
					$dtlsData = "SELECT a.color_size_break_down_id,sum(CASE WHEN a.production_type='$qty_source' then a.production_qnty ELSE 0 END) as production_qnty,sum(CASE WHEN a.production_type=8 then a.production_qnty ELSE 0 END) as cur_production_qnty,sum(CASE WHEN a.production_type=8 and b.id=$data[0] then a.reject_qty ELSE 0 END) as reject_qty  from pro_garments_production_dtls a, pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' $pack_cond and a.color_size_break_down_id!=0 and a.production_type in($qty_source,8) group by a.color_size_break_down_id";
					 

				}
				else
				{
					 $dtlsData = "SELECT b.color_size_break_down_id,sum(CASE WHEN a.production_type=8 then b.production_qnty ELSE 0 END) as cur_production_qnty,sum(CASE WHEN a.production_type=8 and a.id='$data[0]' then b.reject_qty ELSE 0 END) as reject_qty from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_type =8 and a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' $pack_cond group by b.color_size_break_down_id ";

					$dtlsData_colsize =sql_select("SELECT id as color_size_break_down_id,sum( plan_cut_qnty) as production_qnty  from wo_po_color_size_breakdown  where status_active in(1,2,3) and is_deleted=0  and po_break_down_id='$po_id' and item_number_id='$item_id' and  country_id='$country_id'   group by id");

					 
				}

				$dtlsData=sql_select($dtlsData);
									
				foreach($dtlsData as $row)
				{				  
					if($qty_source)
					{
						$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
					}
					else
					{
						foreach($dtlsData_colsize as $rows)
						{
							$color_size_qnty_array[$rows[csf('color_size_break_down_id')]]['iss']= $rows[csf('production_qnty')];
						}
					}
					
 					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rej']= $row[csf('reject_qty')];
				} 
				
				$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty from wo_po_color_size_breakdown where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' $pack_typeCond and is_deleted=0 and status_active in(1,2,3) order by color_number_id,size_order";
			}
			else // by default color and size level
			{
				$dtlsData = sql_select("select a.color_size_break_down_id,sum(CASE WHEN a.production_type='$qty_source' then a.production_qnty ELSE 0 END) as production_qnty,sum(CASE WHEN a.production_type=8 then a.production_qnty ELSE 0 END) as cur_production_qnty,sum(CASE WHEN a.production_type=8 then a.reject_qty ELSE 0 END) as reject_qty from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in($qty_source,8) group by a.color_size_break_down_id");
									
				foreach($dtlsData as $row)
				{				  
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rej']= $row[csf('reject_qty')];
				} 
				
				$sql="SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty from wo_po_color_size_breakdown where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' $pack_cond and is_deleted=0 and status_active in(1,2,3)  order by color_number_id,size_order";
			}
 			//echo $sql;die;
			
			if($variableSettingsRej!=1)
			{
				$disable="";
			}
			else
			{
				$disable="disabled";
			}

 			$colorResult = sql_select($sql);
 			//print_r($sql);die;
			$colorHTML="";
			$colorID='';
			$chkColor = array(); 
			$i=0;$totalQnty=0;$colorWiseTotal=0;
			$fabric_amount_total=0;
			foreach($colorResult as $color)
			{
				if( $variableSettings==2 ) // color level
				{  
					if($qty_source)
					{
						$production_quantity=$color[csf("production_qnty")];
					}
					else
					{
						$production_quantity=$plan_cut_arr[$color[csf("color_number_id")]];
					}
					$amount = $amountArr[$color[csf("color_number_id")]];
					$rejectAmt = $rejectArr[$color[csf("color_number_id")]];
					$order_rate=$con_per_dzn[$color[csf("color_number_id")]]/$costing_per_qty;
					$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="'.($production_quantity-$color[csf("cur_production_qnty")]+$amount).'" value="'.$amount.'" onkeyup="fn_colorlevel_total('.($i+1).')" '.$disable_for_posted.'></td><td><input type="text" name="txtColSizeRej" id="colSizeRej_'.($i+1).'" style="width:60px" class="text_boxes_numeric" placeholder="Rej." value="'.$rejectAmt.'" onkeyup="fn_colorRej_total('.($i+1).') '.$disable.'"><input type="hidden" name="colorSizefabricRate" id="colorSizefabricRate_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" value="'.$order_rate.'"></td></tr>';					$fabric_amount_total+=$amount*$order_rate;
					$totalQnty += $amount;
					$totalRejQnty += $rejectAmt;
					$colorID .= $color[csf("color_number_id")].",";
				}
				else //color and size level
				{
					$index = $color[csf("size_number_id")].$color_arr[$color[csf("color_number_id")]].$color[csf("color_number_id")];
					$amount = $amountArr[$index];
					if( !in_array( $color[csf("color_number_id")], $chkColor ) )
					{
						if( $i!=0 ) $colorHTML .= "</table></div>";
						$i=0;$colorWiseTotal=0;
						$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_number_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_number_id")].'\', \'\',1)"> <span id="accordion_h'.$color[csf("color_number_id")].'span">+</span>'.$color_library[$color[csf("color_number_id")]].' : <span id="total_'.$color[csf("color_number_id")].'"></span> </h3>';
						$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_number_id")].'">';
						$chkColor[] = $color[csf("color_number_id")];
						$totalFn .= "fn_total(".$color[csf("color_number_id")].");";
					}
 					$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";
					
					$iss_qnty=$color_size_qnty_array[$color[csf('id')]]['iss'];
					$rcv_qnty=$color_size_qnty_array[$color[csf('id')]]['rcv'];
					$rej_qnty=$color_size_qnty_array[$color[csf('id')]]['rej'];
					$order_rate=$con_per_dzn[$color[csf("color_number_id")]][$color[csf("size_number_id")]]/$costing_per_qty;
					$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:80px" placeholder="'.($iss_qnty-$rcv_qnty+$amount).'" onkeyup="fn_total('.$color[csf("color_number_id")].','.($i+1).')" value="'.$amount.'" '.$disable_for_posted.' ><input type="text" name="colorSizeRej" id="colSizeRej_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" placeholder="Rej. Qty" onkeyup="fn_total_rej('.$color[csf("color_number_id")].','.($i+1).')" value="'.$rej_qnty.'" '.$disable.'><input type="text" name="colorSizefabricRate" id="colorSizefabricRate_'.$color[csf("color_number_id")].($i+1).'" value="'.$order_rate.'" class="text_boxes_numeric" style="width:50px" '.$disable.'><input type="text" name="colorSizePOQnty" id="colorSizePOQnty_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" value="'.$color[csf("order_quantity")].'" readonly disabled></td></tr>';				
					$colorWiseTotal += $amount;
					$fabric_amount_total+=$amount*$order_rate;
				}
				$i++; 
			}
			//echo $colorHTML;die; 
			if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="70">Color</th><th width="60">Quantity</th><th width="60">Reject</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" value="'.$totalQnty.'" class="text_boxes_numeric" style="width:60px" ></th><th><input type="text" id="total_color_rej" placeholder="'.$totalRejQnty.'" value="'.$totalRejQnty.'" class="text_boxes_numeric" style="width:60px" ></th></tr></tfoot></table>'; }
			echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
			if( $variableSettings==3 )echo "$totalFn;\n";
			$colorList = substr($colorID,0,-1);
			echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
			echo "$('#fabric_data').val('".($fabric_amount_total)."');\n";
		}
		else
		{
			$po_id = $result[csf('po_break_down_id')];
			$item_id = $result[csf('item_number_id')];
			$country_id = $result[csf('country_id')];
			if($db_type==2)
			{
				$sql_finish_issue=sql_select("select listagg(a.body_part_id,',') within group(order by a.body_part_id) as body_part, listagg(a.prod_id,',') within group(order by a.prod_id) as product_id  from inv_finish_fabric_issue_dtls a,order_wise_pro_details b where a.trans_id=b.trans_id and b.po_breakdown_id=$po_id and a.gmt_item_id=$item_id and b.entry_form=18");
			}
			else
			{$sql_finish_issue=sql_select("select group_concat(a.body_part_id) as body_part,group_concat(a.prod_id) as product_id  from inv_finish_fabric_issue_dtls a,order_wise_pro_details b where a.trans_id=b.trans_id and b.po_breakdown_id=$po_id and a.gmt_item_id=$item_id and b.entry_form=18");
				
			}
			foreach($sql_finish_issue as $issue_val)
			{
				if($issue_val[csf('body_part')]=="") $issue_val[csf('body_part')]=0;else $issue_val[csf('body_part')]=$issue_val[csf('body_part')];
				if($issue_val[csf('product_id')]=="") $issue_val[csf('product_id')]=0;else $issue_val[csf('product_id')]=$issue_val[csf('product_id')];
				$body_part_id=$issue_val[csf('body_part')];
				$product_id=$issue_val[csf('product_id')];
			}
			
			$sql_finish_receive=sql_select("select a.fabric_description_id,a.body_part_id,sum(a.amount)/sum(a.receive_qnty) as ave_rate  from pro_finish_fabric_rcv_dtls a, order_wise_pro_details b where a.trans_id=b.trans_id and b.po_breakdown_id=$po_id and a.prod_id=b.prod_id and b.dtls_id=a.id and a.prod_id in($product_id) and a.body_part_id in($body_part_id) and b.entry_form=37 group by a.fabric_description_id,a.body_part_id");
			$finish_rate=array();
			foreach($sql_finish_receive as $f_val)
			{
				$finish_rate[$f_val[csf('body_part_id')]][$f_val[csf('fabric_description_id')]]=number_format($f_val[csf('ave_rate')],4,".","");
			}
			
			$costing_per=return_field_value("costing_per","wo_pre_cost_mst","job_no='".$data[1]."'","costing_per");
			if($costing_per==1)
			{
				$costing_per_qty=12;
			}
			else if($costing_per==2)
			{
				$costing_per_qty=1;
			}
			else if($costing_per==3)
			{
				$costing_per_qty=24;
			}
			else if($costing_per==4)
			{
				$costing_per_qty=36;
			}
			else if($costing_per==5)
			{
				$costing_per_qty=48;
			}
			
			$color_size_qty_arr=array();
			$color_size_sql=sql_select ("SELECT  po_break_down_id,item_number_id,size_number_id,color_number_id,sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where  is_deleted=0 and status_active in(1,2,3)    and  po_break_down_id in (".str_replace("'","",$po_id).") and item_number_id=$item_id group by po_break_down_id,item_number_id,size_number_id,color_number_id");
			foreach($color_size_sql as $s_id)
			{
				$color_size_qty_arr[$s_id[csf('color_number_id')]][$s_id[csf('size_number_id')]]+=$s_id[csf('plan_cut_qnty')]; 
			}
			
			$sql_sewing=sql_select("SELECT a.lib_yarn_count_deter_id ,b.po_break_down_id,a.item_number_id,a.body_part_id, b.color_number_id,	b.gmts_sizes,sum(b.cons) AS conjumction FROM wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls a WHERE a.id = b.pre_cost_fabric_cost_dtls_id  AND b.po_break_down_id in (".str_replace("'","",$po_id).") and  a.item_number_id=$item_id and b.cons!=0 GROUP BY a.lib_yarn_count_deter_id ,b.po_break_down_id,a.item_number_id,a.body_part_id,b.color_number_id,b.gmts_sizes");
			$con_per_dzn=0;
			$po_item_qty_arr=array();
			$color_size_conjumtion=array();
			foreach($sql_sewing as $row_sew)
			{
				$color_size_conjumtion[$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]][$row_sew[csf('body_part_id')]][$row_sew[csf('lib_yarn_count_deter_id')]]['conjum']=str_replace("'","",$row_sew[csf("conjumction")]);
					
				$color_size_conjumtion[$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]][$row_sew[csf('body_part_id')]][$row_sew[csf('lib_yarn_count_deter_id')]]['plan_cut_qty']=$color_size_qty_arr[$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]]; 
				$po_item_qty_arr[$row_sew[csf('color_number_id')]][$row_sew[csf('body_part_id')]][$row_sew[csf('lib_yarn_count_deter_id')]]['plan_cut_qty']+=$color_size_qty_arr[$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]];   
			}
			
			foreach($color_size_conjumtion as $c_id=>$c_value)
			{
				foreach($c_value as $s_id=>$s_value)
				{
					foreach($s_value as $b_id=>$b_value)
					{
						foreach($b_value as $deter_id=>$deter_value)
						{
							 $order_color_size_qty=$deter_value['plan_cut_qty'];
							 $order_qty=$po_item_qty_arr[$c_id][$b_id][$deter_id]['plan_cut_qty'];
							 $order_color_size_qty_per= ($order_color_size_qty/$order_qty)*100;
							 $conjunction_per= ($deter_value['conjum']*$order_color_size_qty_per/100);
							 $con_per_dzn+=$conjunction_per*$finish_rate[$b_id][$deter_id];//*$finish_rate[$b_id]
						}
					}
				}
			}
			echo "$('#fabric_data').val('".($con_per_dzn/$costing_per_qty)."');\n";
			
		}
		//end if condtion
		//#############################################################################################//
	}
 	exit();		
}


if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));  
	$is_projected_po_allow=return_field_value("production_entry","variable_settings_production","variable_list=58 and company_name=$cbo_company_name");
	if($is_projected_po_allow ==2)
	{
		$is_projected_po=return_field_value("is_confirmed","wo_po_break_down","status_active in(1,2,3) and id=$hidden_po_break_down_id");
		if($is_projected_po==2)
		{			
			echo "786**Projected PO is not allowed to production. Please check variable settings";die();
		}
	}
	
	if(!str_replace("'","",$sewing_production_variable)) $sewing_production_variable=3;
 	$control_and_preceding=sql_select("select is_control,preceding_page_id from variable_settings_production where status_active=1 and is_deleted=0 and variable_list=50 and page_category_id=8 and company_name=$cbo_company_name");  
    $is_control=$control_and_preceding[0][csf("is_control")];
    $preceding_process=$control_and_preceding[0][csf("preceding_page_id")];
	$qty_source=67;
	$source_txt="Iron Input";
	if($preceding_process==3) 
	{
		$qty_source=3;//Sewing Input
		$source_txt="Wash Complete";
	} 
	else if($preceding_process==5) 
	{
		$qty_source=5;//Sewing Output
		$source_txt="Sewing Output";
	}
	else if($preceding_process==67)
	{
		$qty_source=67;//Iron Output
		$source_txt="Iron Output";
	}
	
	 
	if($variable_qty_source_packing==1) $qty_source=7; //Iron Output
	else if($variable_qty_source_packing==2) $qty_source=11;//Poly Output
	
	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//table lock here 
		//if  ( check_table_status( 160, 1 )==0 ) { echo "15**0"; die;}
 		
		
		//----------Compare by finishing qty and iron qty qty for validation----------------
		$txt_finishing_qty=str_replace("'","",$txt_finishing_qty);
		if($txt_finishing_qty=='')$txt_finishing_qty=0;
		$is_fullshipment=return_field_value("shiping_status","wo_po_break_down","id=$hidden_po_break_down_id");
		if($is_fullshipment==3)
		{
			echo "505";disconnect($con);die;
		}
		
		if($is_control==1 && $user_level!=2)
		{
			if(str_replace("'","",$txt_pack_type)=="") $packType_cond=""; else $packType_cond=" and pack_type=$txt_pack_type";
			$country_iron_qty=return_field_value("sum(production_quantity)","pro_garments_production_mst","po_break_down_id=$hidden_po_break_down_id and production_type='$qty_source' and country_id=$cbo_country_name $packType_cond and status_active=1 and is_deleted=0");
			
			$country_finishing_qty=return_field_value("sum(production_quantity)","pro_garments_production_mst","po_break_down_id=$hidden_po_break_down_id and production_type=8 and country_id=$cbo_country_name $packType_cond and status_active=1 and is_deleted=0");
		
			if($country_iron_qty < $country_finishing_qty+$txt_finishing_qty)
			{
				echo "25**0";
				//check_table_status( 160,0);
				disconnect($con);
				die;
			}
		}
		//--------------------------------------------------------------Compare end;
		//$id=return_next_id("id", "pro_garments_production_mst", 1);
		 $id= return_next_id_by_sequence(  "pro_gar_production_mst_seq",  "pro_garments_production_mst", $con );
		if(str_replace("'","",$cbo_time)==1)$reportTime = $txt_reporting_hour;else $reportTime = 12+str_replace("'","",$txt_reporting_hour);
		//production_type array	
  		$field_array1="id, garments_nature, company_id, challan_no, po_break_down_id, item_number_id, country_id, pack_type, production_source, serving_company, location, produced_by, production_date, production_quantity, production_type, entry_break_down_type, break_down_type_rej, production_hour, carton_qty,wo_order_id,wo_order_no, remarks, floor_id, alter_qnty, spot_qnty, reject_qnty, total_produced, yet_to_produced, inserted_by, insert_date"; 
		if($db_type==0)
		{
			$data_array1="(".$id.",".$garments_nature.",".$cbo_company_name.",".$txt_challan.",".$hidden_po_break_down_id.",".$cbo_item_name.",".$cbo_country_name.",".$txt_pack_type.",".$cbo_source.",".$cbo_finish_company.",".$cbo_location.",".$cbo_produced_by.",".$txt_finishing_date.",".$txt_finishing_qty.",8,".$sewing_production_variable.",".$finish_production_variable_rej.",".$txt_reporting_hour.",".$txt_carton_qty.",".$txt_wo_id.",".$txt_wo_no.",".$txt_remark.",".$cbo_floor.",".$txt_alter_qnty.",".$txt_spot_qnty.",".$txt_reject_qnty.",".$txt_cumul_finish_qty.",".$txt_yet_to_finish.",".$user_id.",'".$pc_date_time."')";
		}
	  	else if($db_type==2)
		{
			$txt_reporting_hour=str_replace("'","",$txt_finishing_date)." ".str_replace("'","",$txt_reporting_hour);
			$txt_reporting_hour="to_date('".$txt_reporting_hour."','DD MONTH YYYY HH24:MI:SS')";
			$data_array1="INSERT INTO pro_garments_production_mst (".$field_array1.") values(".$id.",".$garments_nature.",".$cbo_company_name.",".$txt_challan.",".$hidden_po_break_down_id.",".$cbo_item_name.",".$cbo_country_name.",".$txt_pack_type.",".$cbo_source.",".$cbo_finish_company.",".$cbo_location.",".$cbo_produced_by.",".$txt_finishing_date.",".$txt_finishing_qty.",8,".$sewing_production_variable.",".$finish_production_variable_rej.",".$txt_reporting_hour.",".$txt_carton_qty.",".$txt_wo_id.",".$txt_wo_no.",".$txt_remark.",".$cbo_floor.",".$txt_alter_qnty.",".$txt_spot_qnty.",".$txt_reject_qnty.",".$txt_cumul_finish_qty.",".$txt_yet_to_finish.",".$user_id.",'".$pc_date_time."')";
		}
 		//$rID=sql_insert("pro_garments_production_mst",$field_array1,$data_array1,1);
		//echo $data_array;die;
		
		// pro_garments_production_dtls table entry here ----------------------------------///
		$field_array="id, mst_id, production_type, color_size_break_down_id, production_qnty, reject_qty";
		if(str_replace("'","",$txt_pack_type)=="") $pack_type_cond=""; else $pack_type_cond=" and b.pack_type=$txt_pack_type";
		$dtlsData = sql_select("SELECT a.color_size_break_down_id,
										sum(CASE WHEN a.production_type='$qty_source' then a.production_qnty ELSE 0 END) as production_qnty,
										sum(CASE WHEN a.production_type=8 then a.production_qnty ELSE 0 END) as cur_production_qnty 
										from pro_garments_production_dtls a,pro_garments_production_mst b 
										where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name and a.color_size_break_down_id<>0 and a.production_type in($qty_source,8) $pack_type_cond
										group by a.color_size_break_down_id");
		$color_pord_data=array();							
		foreach($dtlsData as $row)
		{				  
			$color_pord_data[$row[csf("color_size_break_down_id")]]=$row[csf('production_qnty')]-$row[csf("cur_production_qnty")];
		}
  		if(str_replace("'","",$txt_pack_type)=="") $packType_cond=""; else $packType_cond=" and pack_type=$txt_pack_type"; 
		if(str_replace("'","",$sewing_production_variable)==2)//color level wise
		{		
			$color_sizeID_arr=sql_select( "SELECT id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name  and status_active in(1,2,3) and is_deleted=0  and country_id=$cbo_country_name  $packType_cond order by id" );
			$colSizeID_arr=array(); 
			foreach($color_sizeID_arr as $val){
				$index = $val[csf("color_number_id")];
				$colSizeID_arr[$index]=$val[csf("id")];
			}	
			// $colorIDvalue concate as colorID*Value**colorID*Value -------------------------//
			
			$rowExRej = array_filter(explode("**",$colorIDvalueRej));
			foreach($rowExRej as $rowR=>$valR)
			{
				$colorSizeRejIDArr = explode("*",$valR);
				//echo $colorSizeRejIDArr[0]; die;
				$rejQtyArr[$colorSizeRejIDArr[0]]=$colorSizeRejIDArr[1];
			}
			
 			$rowEx = array_filter(explode("**",$colorIDvalue)); 
 			//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
			$data_array="";$j=0;
			foreach($rowEx as $rowE=>$val)
			{
				$colorSizeNumberIDArr = explode("*",$val);
				if($is_control==1 && $user_level!=2)
				{
					if($colorSizeNumberIDArr[1]>0)
					{
						if(($colorSizeNumberIDArr[1] + $rejQtyArr[$colSizeID_arr[$colorSizeNumberIDArr[0]]])>($color_pord_data[$colSizeID_arr[$colorSizeNumberIDArr[0]]]*1))
						{
							echo "35**Production Quantity Not Over $source_txt Qnty";
							//check_table_status( 160,0);
							disconnect($con);
							die;
						}
					}
				}
				
				if($colSizeID_arr[$colorSizeNumberIDArr[0]]!="")
				{
					//8 for Garments Finishing Entry
					$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );
					if($j==0)$data_array = "(".$dtls_id.",".$id.",8,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."')";
					else $data_array .= ",(".$dtls_id.",".$id.",8,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."')";
					//$dtls_id=$dtls_id+1;							
	 				$j++;
	 			}								
	 			else
	 			{
	 				echo "420**";die();
	 			}
			}
 		}//color level wise
		
		if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
		{		
			$color_sizeID_arr=sql_select( "SELECT id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name  and status_active in(1,2,3) and is_deleted=0  and country_id=$cbo_country_name $packType_cond order by size_number_id,color_number_id" );
			$colSizeID_arr=array(); 
			foreach($color_sizeID_arr as $val){
				$index = $val[csf("size_number_id")].$color_arr[$val[csf("color_number_id")]].$val[csf("color_number_id")];
				$colSizeID_arr[$index]=$val[csf("id")];
			}	
			//	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------// 
			$rowExRej = array_filter(explode("***",$colorIDvalueRej));
			foreach($rowExRej as $rowR=>$valR)
			{
				$colorAndSizeRej_arr = explode("*",$valR);
				$sizeID = $colorAndSizeRej_arr[0];
				$colorID = $colorAndSizeRej_arr[1];				
				$colorSizeRej = $colorAndSizeRej_arr[2];
				$index = $sizeID.$color_arr[$colorID].$colorID;
				$rejQtyArr[$index]=$colorSizeRej;
			}
			
 			$rowEx = array_filter(explode("***",$colorIDvalue)); 
			//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
			$data_array="";$j=0;
			foreach($rowEx as $rowE=>$valE)
			{
				$colorAndSizeAndValue_arr = explode("*",$valE);
				$sizeID = $colorAndSizeAndValue_arr[0];
				$colorID = $colorAndSizeAndValue_arr[1];				
				$colorSizeValue = $colorAndSizeAndValue_arr[2];
				$index = $sizeID.$color_arr[$colorID].$colorID;

				if($is_control==1 && $user_level!=2)
				{
					if($colorSizeValue>0)
					{
						if(($colorSizeValue + $rejQtyArr[$colSizeID_arr[$index]])>($color_pord_data[$colSizeID_arr[$index]]*1))
						{
							echo "35**Production Quantity Not Over $source_txt Qnty";
							//check_table_status( 160,0);
							disconnect($con);
							die;
						}
					}
				}
				
 				if($colSizeID_arr[$index]!="")
 				{
					//8 for Garments Finishing Entry
					$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );
					if($j==0)$data_array = "(".$dtls_id.",".$id.",8,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$rejQtyArr[$index]."')";
					else $data_array .= ",(".$dtls_id.",".$id.",8,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$rejQtyArr[$index]."')";
					//$dtls_id=$dtls_id+1;
	 				$j++;
	 			}
	 			else
	 			{
	 				echo "420**";die();
	 			}
			}
		}//color and size wise
		
		$defectQ=true; 
		$data_array_defect="";
		$save_string=explode(",",str_replace("'","",$save_data));
		
		if(count($save_string)>0 && str_replace("'","",$save_data)!="")  
		{		 
 			//order_wise_pro_details table data insert START-----//  
			$field_array_defect="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty, inserted_by, insert_date";
 			$defect_array=array(); 
			for($i=0;$i<count($save_string);$i++)
			{
				$order_dtls=explode("**",$save_string[$i]);			
				$defect_update_id=$order_dtls[0];
				$defect_point_id=$order_dtls[1];
				$defect_qnty=$order_dtls[2];
				
				if( array_key_exists($defect_point_id,$defect_array) )
				{
					$defect_array[$defect_point_id]=$defect_qnty;
				}
				else
				{
					$defect_array[$defect_point_id]=$defect_qnty;
				}
			}
			$i=0; 
			foreach($defect_array as $key=>$val)
			{
				if( $i>0 ) $data_array_defect.=",";
				if( $dft_id=="" ) 
				{
					//$dft_id=return_next_id("id", "pro_gmts_prod_dft", 1);
					$dft_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq", "pro_gmts_prod_dft", $con ); 

					
				} 
				else
				{
					$dft_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",   "pro_gmts_prod_dft", $con ); 

				}  
				$defectPointId=$key;
				$defect_qty=$val;
				$data_array_defect.="(".$dft_id.",".$id.",8,".$hidden_po_break_down_id.",".$defect_type_id.",".$defectPointId.",'".$defect_qty."',".$user_id.",'".$pc_date_time."')";
				$i++;
			} 
		}
		if($data_array_defect!="")
		{
			//echo "10**INSERT INTO pro_gmts_prod_dft (".$field_array_defect.") VALUES ".$data_array_defect."";// die;
			$defectQ=sql_insert("pro_gmts_prod_dft",$field_array_defect,$data_array_defect,1);
		}
		$defectSpot=true;
		$data_array_defectsp="";
		$save_dataSpot=explode(",",str_replace("'","",$save_dataSpot));
		if(count($save_dataSpot)>0 && str_replace("'","",$save_dataSpot)!="")  
		{		 
 			//order_wise_pro_details table data insert START-----//  
			$field_array_defectsp="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty, inserted_by, insert_date";
 			$defectSpt_array=array(); 
			for($i=0;$i<count($save_dataSpot);$i++)
			{
				$order_dtls=explode("**",$save_dataSpot[$i]);		
				$defect_update_id=$order_dtls[0];
				$defectsp_point_id=$order_dtls[1];
				$defect_qnty=$order_dtls[2];
				if($defectsp_point_id !="")
				{
					if( array_key_exists($defectsp_point_id,$defectSpt_array) )
					{
						$defectSpt_array[$defectsp_point_id]=$defect_qnty;
					}
					else
					{
						$defectSpt_array[$defectsp_point_id]=$defect_qnty;
					}
				}
				
			}
			$i=0; 
			foreach($defectSpt_array as $keysp=>$valsp)
			{
				//  echo "<pre>";
			    //  print_r($valsp); die;	
				if( $i>0 ) $data_array_defectsp.=",";
				 
				if( $dftSp_id=="" ) 
				{
					//$dft_id=return_next_id("id", "pro_gmts_prod_dft", 1);
					$dftSp_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq", "pro_gmts_prod_dft", $con ); 

					
				} 
				else
				{
					$dftSp_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",   "pro_gmts_prod_dft", $con ); 

				}  

				$defectspPointId=$keysp;
				$defectsp_qty=$valsp;
				$data_array_defectsp.="(".$dftSp_id.",".$id.",8,".$hidden_po_break_down_id.",".$defectSpot_type_id.",".$defectspPointId.",'".$defectsp_qty."',".$user_id.",'".$pc_date_time."')";
				$i++;
			} 
		}

		$defectRej=true;
		$data_array_defectre="";
		$save_dataRej=explode(",",str_replace("'","",$save_dataRej));
		// var_dump($save_dataRej); die;
		if(count($save_dataRej)>0 && str_replace("'","",$save_dataRej)!="")  
		{		 
			// echo "rej";die;
			$field_array_defectrej="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty, inserted_by, insert_date";
 			$datarej_array=array(); 
			for($i=0;$i<count($save_dataRej);$i++)
			{
				$order_dtls=explode("**",$save_dataRej[$i]);	
				// echo "<pre>";
			    // print_r($order_dtls); die;		
				$defect_update_id=$order_dtls[0];
				$defectre_point_id=$order_dtls[1];
				$defect_qnty=$order_dtls[2];
				if($defectre_point_id !="")
				{
					if( array_key_exists($defectre_point_id,$datarej_array) )
					{
						$datarej_array[$defectre_point_id]=$defect_qnty;
					}
					else
					{
						$datarej_array[$defectre_point_id]=$defect_qnty;
					}
				}
				
			}
			
			$i=0; 
			foreach($datarej_array as $keyre=>$valre)
			{
				if( $i>0 ) $data_array_defectre.=",";
				 
				if( $dftRe_id=="" ) 
				{
					//$dft_id=return_next_id("id", "pro_gmts_prod_dft", 1);
					$dftRe_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq", "pro_gmts_prod_dft", $con ); 

					
				} 
				else
				{
					$dftRe_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",   "pro_gmts_prod_dft", $con ); 

				}  

				$defectrePointId=$keyre;
				$defectre_qty=$valre;
				$data_array_defectre.="(".$dftRe_id.",".$id.",8,".$hidden_po_break_down_id.",".$defectRej_type_id.",".$defectrePointId.",'".$defectre_qty."',".$user_id.",'".$pc_date_time."')";
				$i++;
			} 
		}

		if($data_array_defectre!="")
		{
			// echo "10**INSERT INTO pro_gmts_prod_dft (".$field_array_defectrej.") VALUES ".$data_array_defectre.""; die;
			$defectRej=sql_insert("pro_gmts_prod_dft",$field_array_defectrej,$data_array_defectre,0);
		}
		//$data_array_fabric="";
		//$data_array_fabric=explode(",",str_replace("'","",$fabric_data));
		$gmt_material_id=return_next_id("id", "pro_garments_material_cost", 1);
		$field_array_material="id,mst_id,company_id, po_break_down_id, item_number_id, production_date, fabric_amount,accessories_amount, printing_amount, embl_amount, wash_amount,sp_work_amount,other_amount,commercial_cost,lab_test_cost,insfaction_cost,cm_cost, freight_cost ,currier_cost,certificate_cost,operating_expenses,commission,depreciation_amortization,quantity,inserted_by,insert_date,status_active,is_deleted ";
		
		$printtin_cost=0;$embodary_cost=0;$wiah_cost=0;$other_cost=0;$special_cost=0;$fabric_cost=0;$trims_cost=0;$commcl_cost=0;
		$lab_test_cost=0;$inspaction_cost=0;$cm_cost=0;$freight_cost=0;$currier_cost=0;$certificate_cost=0;$commision_cost=0;
		$operation_expense_cost=0; $deprication=0; 
		$data_array_fabric="";
		$data_array_fabric=str_replace("'","",$fabric_data);
		$data_array_trims="";
		$data_array_trims=str_replace("'","",$accessoric_data);
		$data_array_embelishment="";
		$data_array_embelishment=explode("__",str_replace("'","",$emblishment_data));
		if($data_array_fabric!="" && $data_array_fabric!="NaN") $fabric_cost=number_format($data_array_fabric,4,".","");  
		if($data_array_trims!="" && $data_array_trims!="NaN")  $trims_cost=number_format($data_array_trims*$txt_finishing_qty,4,".",""); 
		
		//number_format($f_val[csf('ave_rate')],4,".","");
		foreach($data_array_embelishment as $embl_val)
		{
			$embel_row=explode("**",$embl_val);
			if($embel_row[0]==1)      $printtin_cost=number_format($embel_row[2]*$txt_finishing_qty,4,".","");
			else if($embel_row[0]==2) $embodary_cost=number_format($embel_row[2]*$txt_finishing_qty,4,".","");
			else if($embel_row[0]==3) $wiah_cost=number_format($embel_row[2]*$txt_finishing_qty,4,".","");
			else if($embel_row[0]==4) $special_cost=number_format($embel_row[2]*$txt_finishing_qty,4,".","");
			else if($embel_row[0]==5) $other_cost=number_format($embel_row[2]*$txt_finishing_qty,4,".","");
		}
		$data_array_precost="";
		//echo "10**".$precost_data;
		$data_array_precost=explode("**",str_replace("'","",$precost_data));
		$commcl_cost=number_format($data_array_precost[0]*$txt_finishing_qty,4,".","");
		$commision_cost=number_format($data_array_precost[1]*$txt_finishing_qty,4,".","");
		$lab_test_cost=number_format($data_array_precost[2]*$txt_finishing_qty,4,".","");
		$inspaction_cost=number_format($data_array_precost[3]*$txt_finishing_qty,4,".","");
		$cm_cost=number_format($data_array_precost[4]*$txt_finishing_qty,4,".","");
		$freight_cost=number_format($data_array_precost[5]*$txt_finishing_qty,4,".","");
		$currier_cost=number_format($data_array_precost[6]*$txt_finishing_qty,4,".","");
		$certificate_cost=number_format($data_array_precost[7]*$txt_finishing_qty,4,".","");
		$operation_expense_cost=number_format($data_array_precost[8]*$txt_finishing_qty,4,".","");
		$deprication=number_format($data_array_precost[9]*$txt_finishing_qty,4,".","");
		
		$data_array_material="(".$gmt_material_id.",".$id.",".$cbo_company_name.",".$hidden_po_break_down_id.",".$cbo_item_name.",".$txt_finishing_date.",".$fabric_cost.",".$trims_cost.",".$printtin_cost.",".$embodary_cost.",".$wiah_cost.",".$special_cost.",".$other_cost.",".$commcl_cost.",".$lab_test_cost.",".$inspaction_cost.",".$cm_cost.",".$freight_cost.",".$currier_cost.",".$certificate_cost.",".$operation_expense_cost.",".$commision_cost.",".$deprication.",".$txt_finishing_qty.",".$user_id.",'".$pc_date_time."',1,0)";
		//echo "10**".$defectSpot;die;
		//echo "10**INSERT INTO pro_garments_material_cost (".$field_array_material.") VALUES ".$data_array_material.""; die;
		$defectSpot=$rID=$dtlsrID=$rID_material=true;
		if($data_array_defectsp!="")
		{
			//echo "10**INSERT INTO pro_gmts_prod_dft (".$field_array_defectsp.") VALUES ".$data_array_defectsp.""; die;
			$defectSpot=sql_insert("pro_gmts_prod_dft",$field_array_defectsp,$data_array_defectsp,0);
		}
		if($db_type==0)
		{
			$rID=sql_insert("pro_garments_production_mst",$field_array1,$data_array1,1);
		}
		else
		{
			$rID=execute_query($data_array1);	
		}
		if(str_replace("'","",$sewing_production_variable)==2 || str_replace("'","",$sewing_production_variable)==3)
		{ 
 			$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array,$data_array,1);
		}
		$rID_material=1;
		if($data_array_material!="")
		{
			//echo "10**INSERT INTO pro_garments_material_cost (".$field_array_material.") VALUES ".$data_array_material.""; die;
			$rID_material=sql_insert("pro_garments_material_cost",$field_array_material,$data_array_material,0);
		}
		
		//echo "10**$rID && $dtlsrID && $rID_material && $defectSpot";die;
		
		//release lock table
		//check_table_status( 160,0);

		// oci_rollback($con);
		// echo "10**".str_replace("'","",$hidden_po_break_down_id)."**".$rID ."**". $dtlsrID ."**". $rID_material ."**". $defectSpot ."**". $defectRej."**insert into pro_gmts_prod_dft (".$field_array_defectrej.") values ".$data_array_defectre; die;

		if($db_type==0)
		{  
			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID && $dtlsrID && $rID_material && $defectSpot && $defectRej)
				{
					mysql_query("COMMIT");  
					echo "0**".str_replace("'","",$hidden_po_break_down_id);
				}
				else
				{
					mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$hidden_po_break_down_id)."**".$rID ."**". $dtlsrID ."**". $rID_material ."**". $defectSpot ."**". $defectRej ."**insert into pro_gmts_prod_dft (".$field_array_defectrej.") values ".$data_array_defectre;
				}
			}
			else
			{
				if($rID && $rID_material && $defectSpot && $defectRej)
				{
					mysql_query("COMMIT");  
					echo "0**".str_replace("'","",$hidden_po_break_down_id);
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**".str_replace("'","",$hidden_po_break_down_id)."**".$rID ."**". $dtlsrID ."**". $rID_material ."**". $defectSpot ."**". $defectRej."**insert into pro_gmts_prod_dft (".$field_array_defectrej.") values ".$data_array_defectre;
				}
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID && $dtlsrID && $rID_material && $defectSpot && $defectRej)
				{
					oci_commit($con); 
					echo "0**".str_replace("'","",$hidden_po_break_down_id);
				}
				else
				{
					oci_rollback($con);
					echo "10**".str_replace("'","",$hidden_po_break_down_id)."**".$rID ."**". $dtlsrID ."**". $rID_material ."**". $defectSpot ."**". $defectRej."**insert into pro_gmts_prod_dft (".$field_array_defectrej.") values ".$data_array_defectre;
				}
			}
			else
			{
				if($rID && $dtlsrID && $rID_material && $defectSpot && $defectRej)
				{
					oci_commit($con);  
					echo "0**".str_replace("'","",$hidden_po_break_down_id);
				}
				else
				{
					oci_rollback($con);
					echo "10**".str_replace("'","",$hidden_po_break_down_id)."**".$rID ."**". $dtlsrID ."**". $rID_material ."**". $defectSpot ."**". $defectRej."**insert into pro_gmts_prod_dft (".$field_array_defectrej.") values ".$data_array_defectre;
				}
			}
		}
		//check_table_status( 160,0);
		disconnect($con);
		die;
	}
  	else if ($operation==1) // Update Here End------------------------------------------------------
	{
		
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }		
		$txt_finishing_qty=str_replace("'","",$txt_finishing_qty);
		if($txt_finishing_qty=='') $txt_finishing_qty=0;
		$txt_mst_id=str_replace("'","",$txt_mst_id);
		$txt_material_id=str_replace("'","",$txt_material_id);
		$is_fullshipment=return_field_value("shiping_status","wo_po_break_down","id=$hidden_po_break_down_id");
		if($is_fullshipment==3)
		{
			echo "505";disconnect($con);die;
		}
		/*if($is_control==1 && $user_level!=2)
		{
			if(str_replace("'","",$txt_pack_type)=="") $packType_cond=""; else $packType_cond=" and pack_type=$txt_pack_type";
			$country_iron_qty=return_field_value("sum(production_quantity)","pro_garments_production_mst","po_break_down_id=$hidden_po_break_down_id and production_type='$qty_source' and country_id=$cbo_country_name $packType_cond and status_active=1 and is_deleted=0");
			
			$country_finishing_qty=return_field_value("sum(production_quantity)","pro_garments_production_mst","po_break_down_id=$hidden_po_break_down_id and production_type=8 and country_id=$cbo_country_name $packType_cond and status_active=1 and is_deleted=0 and id <> $txt_mst_id");
		
		
			if($country_iron_qty < $country_finishing_qty+$txt_finishing_qty)
			{
				echo "25**".str_replace("'","",$hidden_po_break_down_id);
				//check_table_status( 160,0);
				disconnect($con);
				die;
			}
		
		}*/
		//--------------------------------------------------------------Compare end;
		
		
		
		// pro_garments_production_mst table data entry here 
		if(str_replace("'","",$cbo_time)==1)$reportTime = $txt_reporting_hour;else $reportTime = 12+str_replace("'","",$txt_reporting_hour);
 		$field_array1="pack_type*production_source*serving_company*location*produced_by*production_date*production_quantity*production_type*entry_break_down_type*break_down_type_rej*production_hour*carton_qty*challan_no*wo_order_id*wo_order_no*remarks*floor_id*alter_qnty*spot_qnty*reject_qnty*total_produced*yet_to_produced*updated_by*update_date";
		if($db_type==2)
		{
			$txt_reporting_hour=str_replace("'","",$txt_finishing_date)." ".str_replace("'","",$txt_reporting_hour);
			$txt_reporting_hour="to_date('".$txt_reporting_hour."','DD MONTH YYYY HH24:MI:SS')";
		}
		$data_array1="".$txt_pack_type."*".$cbo_source."*".$cbo_finish_company."*".$cbo_location."*".$cbo_produced_by."*".$txt_finishing_date."*".$txt_finishing_qty."*8*".$sewing_production_variable."*".$finish_production_variable_rej."*".$txt_reporting_hour."*".$txt_carton_qty."*".$txt_challan."*".$txt_wo_id."*".$txt_wo_no."*".$txt_remark."*".$cbo_floor."*".$txt_alter_qnty."*".$txt_spot_qnty."*".$txt_reject_qnty."*".$txt_cumul_finish_qty."*".$txt_yet_to_finish."*".$user_id."*'".$pc_date_time."'";
		
 		
		if(str_replace("'","",$txt_pack_type)=="") $pack_type_cond=""; else $pack_type_cond=" and b.pack_type=$txt_pack_type";
		if(str_replace("'","",$sewing_production_variable)!=1 && str_replace("'","",$txt_mst_id)!='' ) //  not gross level
		{
			
				$dtlsData = sql_select("select a.color_size_break_down_id,
										sum(CASE WHEN a.production_type='$qty_source' then a.production_qnty ELSE 0 END) as production_qnty,
										sum(CASE WHEN a.production_type=8 then a.production_qnty ELSE 0 END) as cur_production_qnty 
										from pro_garments_production_dtls a,pro_garments_production_mst b 
										where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name and a.color_size_break_down_id<>0 and a.production_type in($qty_source,8) and b.id !=$txt_mst_id $pack_type_cond
										group by a.color_size_break_down_id");
			$color_pord_data=array();							
			foreach($dtlsData as $row)
			{				  
				$color_pord_data[$row[csf("color_size_break_down_id")]]=$row[csf('production_qnty')]-$row[csf("cur_production_qnty")];
			}
			
			
 			$field_array="id, mst_id, production_type, color_size_break_down_id, production_qnty, reject_qty";
			if(str_replace("'","",$txt_pack_type)=="") $packType_cond=""; else $packType_cond=" and pack_type=$txt_pack_type";
			if(str_replace("'","",$sewing_production_variable)==2)//color level wise
			{		
				$color_sizeID_arr=sql_select( "SELECT id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name  $packType_cond and status_active in(1,2,3) and is_deleted=0  order by id" );
				$colSizeID_arr=array(); 
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("color_number_id")];
					$colSizeID_arr[$index]=$val[csf("id")];
				}	
				
				// $colorIDvalue concate as colorID*Value**colorID*Value -------------------------//
				$rowExRej = array_filter(explode("**",$colorIDvalueRej));
				foreach($rowExRej as $rowR=>$valR)
				{
					$colorSizeRejIDArr = explode("*",$valR);
					//echo $colorSizeRejIDArr[0]; die;
					$rejQtyArr[$colorSizeRejIDArr[0]]=$colorSizeRejIDArr[1];
				}
				
				$rowEx = array_filter(explode("**",$colorIDvalue)); 
				//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
				$data_array="";$j=0;
				foreach($rowEx as $rowE=>$val)
				{
					$colorSizeNumberIDArr = explode("*",$val);

					if($colSizeID_arr[$colorSizeNumberIDArr[0]]!="")
					{
						$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );
						if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",8,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."')";
						else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",8,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."')";
						$j++;	
					}
					else
					{
						echo "420**";die();
					}							
				}
			}
			
			if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
			{		
				$color_sizeID_arr=sql_select( "select id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name $packType_cond and status_active in(1,2,3) and is_deleted=0 order by size_number_id,color_number_id" );
				$colSizeID_arr=array(); 
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("size_number_id")].$color_arr[$val[csf("color_number_id")]].$val[csf("color_number_id")];
					$colSizeID_arr[$index]=$val[csf("id")];
				}	
				
				//	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------// 
				$rowExRej = array_filter(explode("***",$colorIDvalueRej));
				foreach($rowExRej as $rowR=>$valR)
				{
					$colorAndSizeRej_arr = explode("*",$valR);
					$sizeID = $colorAndSizeRej_arr[0];
					$colorID = $colorAndSizeRej_arr[1];				
					$colorSizeRej = $colorAndSizeRej_arr[2];
					$index = $sizeID.$color_arr[$colorID].$colorID;
					$rejQtyArr[$index]=$colorSizeRej;
				}
				
				$rowEx = array_filter(explode("***",$colorIDvalue)); 
				//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
				$data_array="";$j=0;
				foreach($rowEx as $rowE=>$valE)
				{
					$colorAndSizeAndValue_arr = explode("*",$valE);
					$sizeID = $colorAndSizeAndValue_arr[0];
					$colorID = $colorAndSizeAndValue_arr[1];				
					$colorSizeValue = $colorAndSizeAndValue_arr[2];
					$index = $sizeID.$color_arr[$colorID].$colorID;
					/*if($is_control==1 && $user_level!=2)
					{
						if($colorSizeValue>0)
						{
							if(($colorSizeValue*1)>($color_pord_data[$colSizeID_arr[$index]]*1))
							{
								echo "35**Production Quantity Not Over Iron Qnty";
								//check_table_status( 160,0);
								disconnect($con);
								die;
							}
						}
					}*/
					
					if($colSizeID_arr[$index]!="")
					{
						//8 for Garments Finishing Entry
						$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );
						if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",8,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$rejQtyArr[$index]."')";
						else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",8,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$rejQtyArr[$index]."')";
						//$dtls_id=$dtls_id+1;
						$j++;
					}
					else
					{
						echo "420**";die();
					}
				}
			}
		}
		
		$defectQ=true; 
		$data_array_defect="";
		$save_string=explode(",",str_replace("'","",$save_data));
		
		if(count($save_string)>0 && str_replace("'","",$save_data)!="")  
		{		 
 			//order_wise_pro_details table data insert START-----//  
			$field_array_defect="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty, inserted_by, insert_date";
 			$defect_array=array(); 
			for($i=0;$i<count($save_string);$i++)
			{
				$order_dtls=explode("**",$save_string[$i]);			
				$defect_update_id=$order_dtls[0];
				$defect_point_id=$order_dtls[1];
				$defect_qnty=$order_dtls[2];
				if($defect_point_id !="")
				{
					if( array_key_exists($defect_point_id,$defect_array) )
					{
						$defect_array[$defect_point_id]=$defect_qnty;
					}
					else
					{
						$defect_array[$defect_point_id]=$defect_qnty;
					}
				}
				
			}
			$i=0; 
			foreach($defect_array as $key=>$val)
			{
				if( $i>0 ) $data_array_defect.=",";
				 
				if( $dft_id=="" ) 
				{
					//$dft_id=return_next_id("id", "pro_gmts_prod_dft", 1);
					$dft_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con ); 

					
				} 
				else
				{
					$dft_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq", "pro_gmts_prod_dft", $con ); 

				}  
				$defectPointId=$key;
				$defect_qty=$val;
				$data_array_defect.="(".$dft_id.",".$txt_mst_id.",8,".$hidden_po_break_down_id.",".$defect_type_id.",".$defectPointId.",'".$defect_qty."',".$user_id.",'".$pc_date_time."')";
				$i++;
			} 
		}
		if($data_array_defect!="")
		{
			//echo "10**INSERT INTO pro_gmts_prod_dft (".$field_array_defect.") VALUES ".$data_array_defect."";// die;
			//echo "5**DELETE FROM pro_gmts_prod_dft WHERE mst_id='$txt_mst_id' and defect_type_id=1";die;
			$query3=execute_query("DELETE FROM pro_gmts_prod_dft WHERE mst_id=$txt_mst_id and defect_type_id=1 and production_type=8");
			$defectQ=sql_insert("pro_gmts_prod_dft",$field_array_defect,$data_array_defect,1);
		}
		$defectSpot=true;
		$data_array_defectsp="";
		$save_dataSpot=explode(",",str_replace("'","",$save_dataSpot));
		if(count($save_dataSpot)>0 && str_replace("'","",$save_dataSpot)!="")  
		{		 
 			//order_wise_pro_details table data insert START-----//  
			$field_array_defectsp="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty, inserted_by, insert_date";
 			$defectSpt_array=array(); 
			for($i=0;$i<count($save_dataSpot);$i++)
			{
				$order_dtls=explode("**",$save_dataSpot[$i]);			
				$defect_update_id=$order_dtls[0];
				$defectsp_point_id=$order_dtls[1];
				$defect_qnty=$order_dtls[2];
				
				if( array_key_exists($defectsp_point_id,$defectSpt_array) )
				{
					$defectSpt_array[$defectsp_point_id]=$defect_qnty;
				}
				else
				{
					$defectSpt_array[$defectsp_point_id]=$defect_qnty;
				}
			}
			$i=0; 
			foreach($defectSpt_array as $keysp=>$valsp)
			{
				if( $i>0 ) $data_array_defectsp.=",";
				 
				if( $dftSp_id=="" ) 
				{
					//$dft_id=return_next_id("id", "pro_gmts_prod_dft", 1);
					$dftSp_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq", "pro_gmts_prod_dft", $con ); 

					
				} 
				else
				{
					$dftSp_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con ); 

				}  

				$defectspPointId=$keysp;
				$defectsp_qty=$valsp;
				$data_array_defectsp.="(".$dftSp_id.",".$txt_mst_id.",8,".$hidden_po_break_down_id.",".$defectSpot_type_id.",'".$defectspPointId."','".$defectsp_qty."',".$user_id.",'".$pc_date_time."')";
				$i++;
			} 
		}

		$defectRej=true;
		$data_array_defectre="";
		$save_dataRej=explode(",",str_replace("'","",$save_dataRej));
		// echo "10** "; print_r($save_dataRej); die;
		if(count($save_dataRej)>0 && str_replace("'","",$save_dataRej)!="")  
		{		 
			// echo "rej";die;
			$field_array_defectrej="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty, inserted_by, insert_date";
 			$datarej_array=array(); 
			for($i=0;$i<count($save_dataRej);$i++)
			{
				$order_dtls=explode("**",$save_dataRej[$i]);	
				// echo "<pre>";
			    // print_r($order_dtls); die;		
				$defect_update_id=$order_dtls[0];
				$defectre_point_id=$order_dtls[1];
				$defect_qnty=$order_dtls[2];
				if($defectre_point_id !="")
				{
					if( array_key_exists($defectre_point_id,$datarej_array) )
					{
						$datarej_array[$defectre_point_id]=$defect_qnty;
					}
					else
					{
						$datarej_array[$defectre_point_id]=$defect_qnty;
					}
				}
			}
			
			$i=0; 
			foreach($datarej_array as $keyre=>$valre)
			{
				if( $i>0 ) $data_array_defectre.=",";
				 
				if( $dftRe_id=="" ) 
				{
					//$dft_id=return_next_id("id", "pro_gmts_prod_dft", 1);
					$dftRe_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq", "pro_gmts_prod_dft", $con ); 

					
				} 
				else
				{
					$dftRe_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",   "pro_gmts_prod_dft", $con ); 

				}  

				$defectrePointId=$keyre;
				$defectre_qty=$valre;
				$data_array_defectre.="(".$dftRe_id.",".$txt_mst_id.",8,".$hidden_po_break_down_id.",".$defectRej_type_id.",".$defectrePointId.",'".$defectre_qty."',".$user_id.",'".$pc_date_time."')";
				$i++;
			} 
		}
        
		if($data_array_defectre!="")
		{
			// echo "10** $txt_mst_id";die;
			// echo "10**INSERT INTO pro_gmts_prod_dft (".$field_array_defectrej.") VALUES ".$data_array_defectre.""; die;
			//echo "5**DELETE FROM pro_gmts_prod_dft WHERE mst_id='$txt_mst_id' and defect_type_id=3";die;
			$query5=execute_query("DELETE FROM pro_gmts_prod_dft WHERE mst_id=$txt_mst_id and defect_type_id=3 and production_type=8");
			$defectRej=sql_insert("pro_gmts_prod_dft",$field_array_defectrej,$data_array_defectre,0);
		}
		
		$printtin_cost=0;$embodary_cost=0;$wiah_cost=0;$other_cost=0;$special_cost=0;$fabric_cost=0;$trims_cost=0;$commcl_cost=0;
		$lab_test_cost=0;$inspaction_cost=0;$cm_cost=0;$freight_cost=0;$currier_cost=0;$certificate_cost=0;$commision_cost=0;
		$operation_expense_cost=0; $deprication=0; 
		$data_array_fabric="";
		$data_array_fabric=str_replace("'","",$fabric_data);
		$data_array_trims="";
		$data_array_trims=str_replace("'","",$accessoric_data);
		$data_array_embelishment="";
		$data_array_embelishment=explode("__",str_replace("'","",$emblishment_data));
		if($data_array_fabric!="") $fabric_cost=number_format($data_array_fabric,4,".","");  
		if($data_array_trims!="")  $trims_cost=number_format($data_array_trims*$txt_finishing_qty,4,".",""); 
		
		foreach($data_array_embelishment as $embl_val)
		{
			$embel_row=explode("**",$embl_val);
			if($embel_row[0]==1)      $printtin_cost=number_format($embel_row[2]*$txt_finishing_qty,4,".","");
			else if($embel_row[0]==2) $embodary_cost=number_format($embel_row[2]*$txt_finishing_qty,4,".","");
			else if($embel_row[0]==3) $wiah_cost=number_format($embel_row[2]*$txt_finishing_qty,4,".","");
			else if($embel_row[0]==4) $special_cost=number_format($embel_row[2]*$txt_finishing_qty,4,".","");
			else if($embel_row[0]==5) $other_cost=number_format($embel_row[2]*$txt_finishing_qty,4,".","");
		}
		$data_array_precost="";
		$data_array_precost=explode("**",str_replace("'","",$precost_data));
		$commcl_cost=number_format($data_array_precost[0]*$txt_finishing_qty,4,".","");
		$commision_cost=number_format($data_array_precost[1]*$txt_finishing_qty,4,".","");
		$lab_test_cost=number_format($data_array_precost[2]*$txt_finishing_qty,4,".","");
		$inspaction_cost=number_format($data_array_precost[3]*$txt_finishing_qty,4,".","");
		$cm_cost=number_format($data_array_precost[4]*$txt_finishing_qty,4,".","");
		$freight_cost=number_format($data_array_precost[5]*$txt_finishing_qty,4,".","");
		$currier_cost=number_format($data_array_precost[6]*$txt_finishing_qty,4,".","");
		$certificate_cost=number_format($data_array_precost[7]*$txt_finishing_qty,4,".","");
		$operation_expense_cost=number_format($data_array_precost[8]*$txt_finishing_qty,4,".","");
		$deprication=number_format($data_array_precost[9]*$txt_finishing_qty,4,".","");
		if($fabric_cost=="")
		{
			$fabric_cost=0;
		}
		if(str_replace("'","",$txt_material_id)!="")
		{
			$field_material_update="production_date*fabric_amount*accessories_amount* printing_amount* embl_amount* wash_amount*sp_work_amount *other_amount*commercial_cost*lab_test_cost*insfaction_cost*cm_cost*freight_cost *currier_cost*certificate_cost*operating_expenses *commission*depreciation_amortization*quantity*updated_by*update_date";
			$data__material_update="".$txt_finishing_date."*".$fabric_cost."*".$trims_cost."*".$printtin_cost."*".$embodary_cost."*".$wiah_cost."*".$special_cost."*".$other_cost."*".$commcl_cost."*".$lab_test_cost."*".$inspaction_cost."*".$cm_cost."*".$freight_cost."*".$currier_cost."*".$certificate_cost."*".$operation_expense_cost."*".$commision_cost."*".$deprication."*".$txt_finishing_qty."*".$user_id."*'".$pc_date_time."'";
		}
		else
		{
			$gmt_material_id=return_next_id("id", "pro_garments_material_cost", 1);
			$field_array_material="id,mst_id,company_id, po_break_down_id, item_number_id, production_date, fabric_amount,accessories_amount, printing_amount, embl_amount, wash_amount,sp_work_amount,other_amount,commercial_cost,lab_test_cost,insfaction_cost,cm_cost, freight_cost ,currier_cost,certificate_cost,operating_expenses,commission,depreciation_amortization,quantity,inserted_by,insert_date,status_active,is_deleted ";
			$data_array_material="(".$gmt_material_id.",".$txt_mst_id.",".$cbo_company_name.",".$hidden_po_break_down_id.",".$cbo_item_name.",".$txt_finishing_date.",".$fabric_cost.",".$trims_cost.",".$printtin_cost.",".$embodary_cost.",".$wiah_cost.",".$special_cost.",".$other_cost.",".$commcl_cost.",".$lab_test_cost.",".$inspaction_cost.",".$cm_cost.",".$freight_cost.",".$currier_cost.",".$certificate_cost.",".$operation_expense_cost.",".$commision_cost.",".$deprication.",".$txt_finishing_qty.",".$user_id.",'".$pc_date_time."',1,0)";
		}
		//echo "10**".$data__material_update;die;
		$dtlsrDelete = execute_query("delete from pro_garments_production_dtls where mst_id=$txt_mst_id",1);
		$rID = $dtlsrDelete = $dtlsrID = $rIDmaterial=$defectSpot=true;
		if($data_array_defectsp!="")
		{
			$query4=execute_query("DELETE FROM pro_gmts_prod_dft WHERE mst_id=$txt_mst_id and defect_type_id=2 and production_type=8");
			$defectSpot=sql_insert("pro_gmts_prod_dft",$field_array_defectsp,$data_array_defectsp,0);
		}

		$rID=sql_update("pro_garments_production_mst",$field_array1,$data_array1,"id","".$txt_mst_id."",1);
		
		if(str_replace("'","",$sewing_production_variable)!=1 && str_replace("'","",$txt_mst_id)!='')// check is not gross level
		{
			$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array,$data_array,1);
		}
		$rIDmaterial=1;
	

		if($txt_material_id!="")
		{
			$rIDmaterial=sql_update("pro_garments_material_cost",$field_material_update,$data__material_update,"id","".$txt_material_id."",1);
		}
		else if(str_replace("'","",$data_array_material)!="")
		{
			$rID_material=sql_insert("pro_garments_material_cost",$field_array_material,$data_array_material,0);
		}
		
		//echo "10**".$rID."**".$dtlsrDelete."**".$dtlsrID."**".$rIDmaterial;die;
		
		//release lock table
		//check_table_status( 160,0);
		
		if($db_type==0)
		{
			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID && $dtlsrDelete && $dtlsrID && $rIDmaterial && $defectSpot && $defectRej)
				{
					mysql_query("COMMIT");  
					echo "1**".str_replace("'","",$hidden_po_break_down_id);
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**".str_replace("'","",$hidden_po_break_down_id)."**".$rID ."**". $dtlsrID ."**". $rID_material ."**". $defectSpot ."**". $defectRej."**insert into pro_gmts_prod_dft (".$field_array_defectrej.") values ".$data_array_defectre;
				}
			}
			else
			{
				if($rID)
				{
					mysql_query("COMMIT");  
					echo "1**".str_replace("'","",$hidden_po_break_down_id);
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**".str_replace("'","",$hidden_po_break_down_id)."**".$rID ."**". $dtlsrID ."**". $rID_material ."**". $defectSpot ."**". $defectRej."**insert into pro_gmts_prod_dft (".$field_array_defectrej.") values ".$data_array_defectre;
				}
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID && $dtlsrDelete && $dtlsrID && $rIDmaterial && $defectSpot && $defectRej)
				{
					oci_commit($con); 
					echo "1**".str_replace("'","",$hidden_po_break_down_id);
				}
				else
				{
					oci_rollback($con);
					echo "10**".str_replace("'","",$hidden_po_break_down_id)."**".$rID ."**". $dtlsrID ."**". $rID_material ."**". $defectSpot ."**". $defectRej."**insert into pro_gmts_prod_dft (".$field_array_defectrej.") values ".$data_array_defectre;
				}
			}
			else
			{
				if($rID)
				{
					oci_commit($con);  
					echo "1**".str_replace("'","",$hidden_po_break_down_id);
				}
				else
				{
					oci_rollback($con);
					echo "10**".str_replace("'","",$hidden_po_break_down_id)."**".$rID ."**". $dtlsrID ."**". $rID_material ."**". $defectSpot ."**". $defectRej."**insert into pro_gmts_prod_dft (".$field_array_defectrej.") values ".$data_array_defectre;
				}
			}
		}
		//check_table_status( 160,0);
		disconnect($con);
		die;
	}
	else if ($operation==2)  // Delete Here---------------------------------------------------------- 
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		 
 		$rID = sql_delete("pro_garments_production_mst","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'id ',$txt_mst_id,1);
		$dtlsrID = sql_delete("pro_garments_production_dtls","status_active*is_deleted","0*1",'mst_id',$txt_mst_id,1);
		
 		if($db_type==0)
		{
			if($rID && $dtlsrID)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$hidden_po_break_down_id); 
			}
			else
			{
				mysql_query("ROLLBACK"); 
				//echo "10**".str_replace("'","",$hidden_po_break_down_id); 
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);   
				echo "2**".str_replace("'","",$hidden_po_break_down_id); 
			}
			else
			{
				oci_rollback($con);
				//echo "10**".str_replace("'","",$hidden_po_break_down_id); 
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="finishing_entry_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$buyer_library=return_library_array( "select id, short_name from   lib_buyer", "id", "short_name"  );
	$sewing_library=return_library_array( "select id, line_name from  lib_sewing_line", "id", "line_name"  );
	
	$sql="SELECT id, company_id, challan_no, sewing_line, po_break_down_id, item_number_id, break_down_type_rej, entry_break_down_type, country_id, production_source, serving_company, location, embel_name, embel_type, production_date, TO_CHAR(production_hour,'HH24:MI') as production_hour, reject_qnty, production_quantity, carton_qty, production_type, remarks, floor_id, sewing_line,wo_order_no from pro_garments_production_mst where production_type=8 and id='$data[1]' and status_active=1 and is_deleted=0 ";
	// echo $sql;
	$dataArray=sql_select($sql);
	$break_down_type_reject=$dataArray[0][csf('break_down_type_rej')];
	$entry_break_down_type=$dataArray[0][csf('entry_break_down_type')];
	$wo_no = "";
	foreach ($dataArray as $val) 
	{
		$wo_no .= ($wo_no=="") ? $val['WO_ORDER_NO'] : ", ".$val['WO_ORDER_NO'];
	}


	$order_library=return_library_array( "select id, po_number from  wo_po_break_down where id=".$dataArray[0]['PO_BREAK_DOWN_ID']."", "id", "po_number"  );

?>
<div style="width:930px;">
    <table width="900" cellspacing="0" align="right">
        <tr>
            <td colspan="6" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="6" align="center" style="font-size:14px">  
				<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
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
            <td colspan="6" align="center" style="font-size:x-large"><strong><? echo $data[2];  ?> Challan</strong></td>
        </tr>
        <tr>
			<?
                $supp_add=$dataArray[0][csf('serving_company')];
                $nameArray=sql_select( "select address_1,web_site,email,country_id from lib_supplier where id=$supp_add"); 
                foreach ($nameArray as $result)
                { 
                    $address="";
                    if($result!="") $address=$result[csf('address_1')];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
                }
				//echo $address;
				foreach($dataArray as $row)
				{
					$job_no=return_field_value("h.job_no"," wo_po_break_down f, wo_po_details_master h","f.job_no_mst=h.job_no and f.id=".$row[csf("po_break_down_id")],"job_no");
					$buyer_val=return_field_value("h.buyer_name"," wo_po_break_down f, wo_po_details_master h","f.job_no_mst=h.job_no and f.id=".$row[csf("po_break_down_id")],"buyer_name");
					$style_val=return_field_value("h.style_ref_no"," wo_po_break_down f, wo_po_details_master h","f.job_no_mst=h.job_no and f.id=".$row[csf("po_break_down_id")],"style_ref_no");
				}
            ?> 
        	<td width="270" rowspan="5" valign="top" colspan="2"><strong>Issue To : <? if($dataArray[0][csf('production_source')]==1) echo $company_library[$dataArray[0][csf('serving_company')]]; else if($dataArray[0][csf('production_source')]==3) echo $supplier_library[$dataArray[0][csf('serving_company')]].'<br>'.$address;  ?></strong></td>
            <td width="125"><strong>Order No :</strong></td><td width="175px"><? echo $order_library[$dataArray[0][csf('po_break_down_id')]]; ?></td>
            <td width="125"><strong>Buyer:</strong></td><td width="175px"><? echo $buyer_library[$buyer_val]; ?></td>
        </tr>
        <tr>
            <td><strong>Job No :</strong></td><td width="175px"><? echo $job_no; ?></td>
            <td><strong>Style Ref.:</strong></td> <td width="175px"><? echo $style_val; ?></td>
        </tr>
        <tr>
        	<td><strong>Item:</strong></td> <td width="175px"><? echo $garments_item[$dataArray[0][csf('item_number_id')]]; ?></td>
            <td><strong>Order Qnty:</strong></td><td width="175px"><? echo $dataArray[0][csf('production_quantity')]; ?></td>
        </tr>
        <tr>
            <td><strong>Source:</strong></td><td width="175px"><? echo $knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
            <td><strong>Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('production_date')]); ?></td>
        </tr>
        <tr>
            <td><strong>Reporting Hour:</strong></td> <td width="175px"><? echo $dataArray[0][csf('production_hour')]; ?></td>
            <td><strong>Challan No:</strong></td> <td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
            <td><strong>Produced By: </strong></td><td><? echo $worker_type[$dataArray[0][csf('produced_by')]]; ?></td>
        </tr>
        <tr>
        	<td><strong>Carton Qty:</strong></td> <td width="175px"><? echo $dataArray[0][csf('carton_qty')]; ?></td>
        	<td><strong>WO No:</strong></td> <td width="175px"><? echo $wo_no; ?></td>
            <td><strong><p>Remarks:  <? echo $dataArray[0][csf('remarks')]; ?></p></strong></td>
        </tr>
        <tr>
         <?
		if($entry_break_down_type==1)
		{
		?>

        	<td colspan="3" ><strong>Receive Qnty :  <? echo $dataArray[0][csf('production_quantity')]; ?></strong></td>
       <? 
		}
		 if($break_down_type_reject==1)
		{
  ?>
            <td colspan="3" ><strong>Reject Qnty:  <? echo $dataArray[0][csf('reject_qnty')]; ?></strong></td>  
       <? }
		   else
		 { ?>
			   <td colspan="6">&nbsp;</td>
	<?   }
	 ?>
        </tr>
    </table>
        <?
		if($entry_break_down_type!=1)
		{
			$mst_id=$dataArray[0][csf('id')];
			$po_break_id=$dataArray[0][csf('po_break_down_id')];
			$sql="SELECT sum(a.production_qnty) as production_qnty, b.color_number_id, b.size_number_id from pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.mst_id='$mst_id' and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 group by  b.size_number_id, b.color_number_id ";
			//echo $sql;
			$result=sql_select($sql);
			$size_array=array ();
			$qun_array=array ();
			foreach ( $result as $row )
			{
				$size_array[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$qun_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]=$row[csf('production_qnty')];
			}
			
			$sql="SELECT sum(a.production_qnty) as production_qnty, b.color_number_id from pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.mst_id='$mst_id' and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 group by b.color_number_id ";
			//echo $sql; and a.production_date='$production_date'
			$result=sql_select($sql);
			$color_array=array ();
			foreach ( $result as $row )
			{
				$color_array[$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			}
			
			$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
			$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
		?> 
         	<div style="width:100%;">
            <div style="margin-left:30px;"><strong>Goods Qty.</strong></div>
    <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="80" align="center">Color/Size</th>
				<?
                foreach ($size_array as $sizid)
                {
					//$size_count=count($sizid);
                    ?>
                        <th width="150"><strong><? echo  $sizearr[$sizid];  ?></strong></th>
                    <?
                }
                ?>
            <th width="80" align="center">Total Issue Qnty.</th>
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
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td><? echo $i;  ?></td>
                        <td><? echo $colorarr[$cid]; ?></td>
                        <?
                        foreach ($size_array as $sizval)
                        {
							$size_count=count($sizval);
                            ?>
                            <td align="right"><? echo $qun_array[$cid][$sizval]; ?></td>
                            <?
                            $tot_qnty[$cid]+=$qun_array[$cid][$sizval];
							$tot_qnty_size[$sizval]+=$qun_array[$cid][$sizval];
                        }
                        ?>
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
                    <td align="right"><?php echo $tot_qnty_size[$sizval]; ?></td>
                    <?
				}
			?>
            <td align="right"><?php echo $production_quantity; ?></td>
        </tr>                           
    </table>
		 <?
		}
		if($break_down_type_reject!=1)
		{ 
        
        	$mst_id=$dataArray[0][csf('id')];
			$po_break_id=$dataArray[0][csf('po_break_down_id')];
			$sql="SELECT sum(a.production_qnty) as production_qnty, sum(reject_qty) as reject_qty,b.color_number_id, b.size_number_id from pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.mst_id='$mst_id' and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 group by  b.size_number_id, b.color_number_id ";
			//echo $sql;
			$result=sql_select($sql);
			$size_array=array();
			$reject_qun_array=array();
			foreach ( $result as $row )
			{
				$size_array[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				//$qun_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]=$row[csf('production_qnty')];
				$reject_qun_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]=$row[csf('reject_qty')];

			}
			
			$sql="SELECT sum(a.production_qnty) as production_qnty, b.color_number_id from pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.mst_id='$mst_id' and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 group by b.color_number_id ";
			//echo $sql; and a.production_date='$production_date'
			$result=sql_select($sql);
			$color_array=array ();
			foreach ( $result as $row )
			{
				$color_array[$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			}
			
			$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
			$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
		?> 
         	<div style="width:100%;">
            <div style="margin-left:30px;"><strong> Reject Qty.</strong></div>
    <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="80" align="center">Color/Size</th>
				<?
                foreach ($size_array as $sizid)
                {
					//$size_count=count($sizid);
                    ?>
                        <th width="150"><strong><? echo  $sizearr[$sizid];  ?></strong></th>
                    <?
                }
                ?>
            <th width="80" align="center">Total Issue Qnty.</th>
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
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td><? echo $i;  ?></td>
                        <td><? echo $colorarr[$cid]; ?></td>
                        <?
                        foreach ($size_array as $sizval)
                        {
							$size_count=count($sizval);
                            ?>
                            <td align="right"><? echo $reject_qun_array[$cid][$sizval]; ?></td>
                            <?
                            $tot_reject_qnty[$cid]+=$reject_qun_array[$cid][$sizval];
							$tot_reject_qnty_size[$sizval]+=$reject_qun_array[$cid][$sizval];
                        }
                        ?>
                        <td align="right"><? echo $tot_reject_qnty[$cid]; ?></td>
                    </tr>
                    <?
					$reject_production_quantity+=$tot_reject_qnty[$cid];
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
                    <td align="right"><?php echo $tot_reject_qnty_size[$sizval]; ?></td>
                    <?
				}
			?>
            <td align="right"><?php echo $reject_production_quantity; ?></td>
        </tr>                           
    </table>
        <? 
		}
            echo signature_table(31, $data[0], "900px");
         ?>
	</div>
	</div>
<?
exit();	
}

if($action=="defect_data")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	$caption_name="";
	if($type==1) $caption_name="Alter Qty";
	else if($type==2) $caption_name="Spot Qty";
	else if($type==3) $caption_name="Reject Qty";
	?>
    <script>
		function fnc_close()
		{
			var save_string='';	var tot_defect_qnty='';
			var defect_id_array = new Array();
			$("#tbl_list_search").find('tr').each(function()
			{
				var txtDefectId=$(this).find('input[name="txtDefectId[]"]').val();
				var txtDefectQnty=$(this).find('input[name="txtDefectQnty[]"]').val();
				var txtDefectUpdateId=$(this).find('input[name="txtDefectUpdateId[]"]').val();		
				tot_defect_qnty=tot_defect_qnty*1+txtDefectQnty*1;
				//				
				if(txtDefectQnty*1>0)
				{
					if(save_string=="")
					{
						save_string=txtDefectUpdateId+"**"+txtDefectId+"**"+txtDefectQnty;
					}
					else
					{
						save_string+=","+txtDefectUpdateId+"**"+txtDefectId+"**"+txtDefectQnty;
					}
					
					if( jQuery.inArray( txtDefectId, defect_id_array) == -1 ) 
					{
						defect_id_array.push(txtDefectId);
					}
				}
			});
			//alert (save_string);
			//var defect_type_id=
			$('#defect_type_id').val();
			$('#save_string').val( save_string );
			$('#tot_defectQnty').val( tot_defect_qnty );
			$('#all_defect_id').val( defect_id_array );
			parent.emailwindow.hide();
		}
	</script>
    </head>
    <body>
        <div align="center" style="width:100%;" >
        <form name="defect_1"  id="defect_1" autocomplete="off">
			<? //echo load_freeze_divs ("../../../",$permission,1); ?>
            <fieldset style="width:350px;">
                <input type="hidden" name="save_string" id="save_string" class="text_boxes" value="<? echo $save_data; ?>">
                <input type="hidden" name="tot_defectQnty" id="tot_defectQnty" class="text_boxes" value="<? echo $defect_qty; ?>">
                <input type="hidden" name="all_defect_id" id="all_defect_id" class="text_boxes" value="<? echo $all_defect_id; ?>">
                <input type="hidden" name="defect_type_id" id="defect_type_id" class="text_boxes" value="<? echo $type; ?>">
			<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="340">  
            	<thead>
                	<tr><th colspan="3"><? echo $caption_name; ?></th></tr>
                	<tr><th width="40">SL</th><th width="150">Defect Name</th><th>Defect Qty</th></tr>
                </thead>
            </table>
            <div style="width:340px; max-height:300px; overflow-y:scroll" id="list_container" align="left"> 
                <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="320" id="tbl_list_search">  
                    <?
					if($type==1)
					{
						if($save_string=="") //$save_data=$prevQnty;
						$explSaveData = explode(",",$save_data);
						
						$defect_dataArray=array();
						foreach($explSaveData as $val)
						{
							$difectVal = explode("**",$val);
							$defect_dataArray[$difectVal[1]]['up_id']=$difectVal[0];
							$defect_dataArray[$difectVal[1]]['defectid']=$difectVal[1];
							$defect_dataArray[$difectVal[1]]['defectQnty']=$difectVal[2];
						}
                        $i=1;
                        foreach($sew_fin_alter_defect_type as $id=>$val)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
                                <td width="40"><? echo $i; ?></td>
                                <td width="150"><? echo $val; ?></td>
                                <td align="center">
                                    <input type="text" name="txtDefectQnty[]" id="txtDefectQnty_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" value="<? echo $defect_dataArray[$id]['defectQnty']; ?>">
                                    <input type="hidden" name="txtDefectId[]" id="txtDefectId_<? echo $i; ?>" style="width:40px" value="<? echo $id; ?>">
                                    <input type="hidden" name="txtDefectUpdateId[]" id="txtDefectUpdateId_<? echo $i; ?>" style="width:40px" value="<? echo $defect_dataArray[$id]['up_id']; ?>">
                                </td>
                            </tr>
                            <?
                            $i++;
                        }
					}
					else if($type==2)
					{
						if($save_string=="") //$save_data=$prevQnty;
						$explSaveData = explode(",",$save_data);
						
						$defect_dataArray=array();
						foreach($explSaveData as $val)
						{
							$difectVal = explode("**",$val);
							$defect_dataArray[$difectVal[1]]['up_id']=$difectVal[0];
							$defect_dataArray[$difectVal[1]]['defectid']=$difectVal[1];
							$defect_dataArray[$difectVal[1]]['defectQnty']=$difectVal[2];
						}
                        $i=1;
                        foreach($sew_fin_spot_defect_type as $id=>$val)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
                                <td width="40"><? echo $i; ?></td>
                                <td width="150"><? echo $val; ?></td>
                                <td align="center">
                                    <input type="text" name="txtDefectQnty[]" id="txtDefectQnty_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" value="<? echo $defect_dataArray[$id]['defectQnty']; ?>">
                                    <input type="hidden" name="txtDefectId[]" id="txtDefectId_<? echo $i; ?>" style="width:40px" value="<? echo $id; ?>">
                                    <input type="hidden" name="txtDefectUpdateId[]" id="txtDefectUpdateId_<? echo $i; ?>" style="width:40px" value="<? echo $defect_dataArray[$id]['up_id']; ?>">
                                </td>
                            </tr>
                            <?
                            $i++;
                        }
					}
					else if($type==3)
					{
						if($save_string=="") //$save_data=$prevQnty;
						$explSaveData = explode(",",$save_data);
						
						$defect_dataArray=array();
						foreach($explSaveData as $val)
						{
							$difectVal = explode("**",$val);
							$defect_dataArray[$difectVal[1]]['up_id']=$difectVal[0];
							$defect_dataArray[$difectVal[1]]['defectid']=$difectVal[1];
							$defect_dataArray[$difectVal[1]]['defectQnty']=$difectVal[2];
						}
                        $i=1;
                        foreach($sew_fin_reject_defect_type as $id=>$val)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
                                <td width="40"><? echo $i; ?></td>
                                <td width="150"><? echo $val; ?></td>
                                <td align="center">
                                    <input type="text" name="txtDefectQnty[]" id="txtDefectQnty_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" value="<? echo $defect_dataArray[$id]['defectQnty']; ?>">
                                    <input type="hidden" name="txtDefectId[]" id="txtDefectId_<? echo $i; ?>" style="width:40px" value="<? echo $id; ?>">
                                    <input type="hidden" name="txtDefectUpdateId[]" id="txtDefectUpdateId_<? echo $i; ?>" style="width:40px" value="<? echo $defect_dataArray[$id]['up_id']; ?>">
                                </td>
                            </tr>
                            <?
                            $i++;
                        }
					}
                    ?>
                </table>
            </div>
			<table width="320" id="table_id">
				 <tr>
					<td align="center" colspan="3">
						<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
					</td>
				</tr>
			</table>
            </fieldset>
        </form>
        </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
  <?	
}

if($action=="production_process_control")
{
	echo "$('#hidden_variable_cntl').val('0');\n";
	echo "$('#hidden_preceding_process').val('0');\n";
    $control_and_preceding=sql_select("select is_control,preceding_page_id from variable_settings_production where status_active=1 and is_deleted=0 and variable_list=50 and page_category_id=8 and company_name='$data'");  
    if(count($control_and_preceding)>0)
    {
      echo "$('#hidden_variable_cntl').val('".$control_and_preceding[0][csf("is_control")]."');\n";
	  echo "$('#hidden_preceding_process').val('".$control_and_preceding[0][csf("preceding_page_id")]."');\n";
    }
	
	exit();	
}


if($action=="color_size_missing_api")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	$sqls=sql_select("SELECT d.color_name, production_qnty,size_name  from PRO_GARMENTS_PRODUCTION_DTLS b,wo_po_color_size_breakdown a,lib_size c ,lib_color d  where b.COLOR_SIZE_BREAK_DOWN_ID=a.id and a.size_number_id=c.id  and a.color_number_id=d.id  and b.mst_id='$id'"); 
	?>
	<table width="500" border="2" cellpadding="0" cellspacing="0" class="rpt_table"  style="margin: 0px auto;margin-top: 50px;">
	<thead>
		<tr> 
			<th>Color</th>
			<th>Size</th>
			<th>Qnty.</th>
		</tr>
	</thead>
	<tbody>
	<?
	foreach($sqls as $vals)
	{
		?>
		<tr>
			<td align="center"><? echo $vals[csf("color_name")];?></td>
			<td  align="center"><? echo $vals[csf("size_name")];?></td>
			<td  align="center"><? echo $vals[csf("production_qnty")];?></td>
			
		</tr>

		<?

	}

	 ?>
		
	</tbody>
		
	</table>


	<?
  
}

?>