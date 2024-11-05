<? 
header('Content-type:text/html; charset=utf-8');
session_start();

if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
$user_id = $_SESSION['logic_erp']['user_id'];

require_once('../../../includes/common.php');
if (!function_exists('pre')) 
{
	function pre($array){
		echo "<pre>";
		print_r($array);
		echo "</pre>";
	} 	 
}

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
 
   
//--------------------------------------------------------------------------------------------------------------------
if ($action=="load_drop_down_buyer")
{  
	echo create_drop_down( "cbo_buyer_name", 150, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "- All Buyer -", $selected, "" );     	 
	exit();
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 150, "select id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id in('$data') 
	order by location_name","id,location_name", 1, "-- Select Location --", $selected,"",0 );    	 
}
if ($action=="load_drop_down_color")
{ 
	$color_id_arr = return_library_array( "SELECT color_number_id from wo_po_color_size_breakdown a, wo_po_details_master b where a.job_id=b.id and b.style_ref_no='$data' and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 ",'color_number_id','color_number_id'); 
	$color_id_str = implode(',',$color_id_arr);

	$color_library= return_library_array( "select id,color_name from lib_color where id in ($color_id_str)", "id", "color_name"  );  

	echo create_drop_down( "cbo_color_id", 150, $color_library,"", 1, "-- Select Color --", $selected,"",0 );    	 
}

//Buyer Style search.......................................................

if ($action=="style_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	list($company_id,$buyer_id)=explode("_",$data);
	?>
	<script>
		function js_set_value(id)
		{ 
			document.getElementById('selected_id').value=id;
			parent.emailwindow.hide();
		} 
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
            <form name="searchjobfrm_1"  id="searchjobfrm_1" autocomplete="off">
                <table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead> 
                    	<tr>               	 
                            <th width="140">Company Name</th>
                            <th width="140">Party Name</th>
                            <th width="170">Date Range</th>
                            <th width="100">Search By</th>
                            <th width="100" id="search_by_td">W/O No</th>
                            <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                        </tr>         
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="hidden" id="selected_id">  
								<?   
									echo create_drop_down( "cbo_company_id", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $data[0],"",1 );
                                ?>
                            </td>
                            <td id="party_td">
								<? echo create_drop_down( "cbo_party_id", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select Party --", $selected,  "" );
								 ?>
								
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                            </td> 
                            <td>
								<?
									$search_by_arr=array(1=>"W/O No",2=>"Buyer Job",3=>"Buyer Po",4=>"Buyer Style");
									echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,"$('#search_by_td').html($(this).find('option:selected').text());$('#txt_search_string').val('');",0 );
								?>
                            </td>
                            <td align="center">
                                <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                            </td>
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_party_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value, 'style_no_search_list_view', 'search_div', 'printing_material_receive_report_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6" align="center" height="40" valign="middle">
								<? echo load_month_buttons(1);  ?>
                                <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
                            </td>
                        </tr>
                    </tbody>
                </table> 
                <br>
                <div id="search_div"></div>   
            </form>
        </div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}


if($action=="style_no_search_list_view")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST); 
	?>
	<script type="text/javascript">
		function js_set_value(id)
		{ 
			document.getElementById('selected_id').value=id;
			parent.emailwindow.hide();
		}
	</script>
	<input type="hidden" id="selected_id" name="selected_id" /> 
	<?
	$data=explode('_',$data);  
	$emb_company_id	= str_replace("'","",$data[0]);
	$lc_company_id	= str_replace("'","",$data[1]); 
	$form_date		= str_replace("'","",$data[2]); 
	$to_date		= str_replace("'","",$data[3]);   
	$search_by		= str_replace("'","",$data[4]);
	$search_str		= trim(str_replace("'","",$data[5]));

	$cond_sql ="";
	$cond_sql .= $emb_company_id    ? " and a.serving_company =$emb_company_id " :""; 
	$cond_sql .= $lc_company_id     ? " and c.company_name=$lc_company_id" :""; 
	$cond_sql .= ($form_date && $to_date) ?" and a.production_date between '".change_date_format($form_date,'dd-mm-yyyy','-',1)."' and '".change_date_format($to_date,'dd-mm-yyyy','-',1)."'"  : ""; 
	
	if($search_by==1) 		$search_com_cond=" and a.wo_order_no like '%$search_str%'";  
	else if ($search_by==2) $search_com_cond=" and c.job_no_prefix_num = $search_str";  
	else if ($search_by==3) $search_com_cond=" and b.po_number like '%$search_str%'";  
	else if ($search_by==4) $search_com_cond=" and c.style_ref_no like '%$search_str%'"; 	
	 
	$comp=return_library_array( "SELECT id, company_name from lib_company where status_active =1 and is_deleted=0",'id','company_name'); 
	
	$sql = "select a.wo_order_no,b.po_number,c.style_ref_no as style,c.job_no,c.buyer_name,to_char(c.insert_date,'YYYY') as year from pro_garments_production_mst a,wo_po_break_down b, wo_po_details_master c where a.po_break_down_id=b.id and b.job_id=c.id  $cond_sql $search_com_cond and a.embel_name=1 and a.production_type in(2) and a.wo_order_no IS NOT NULL and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 "; 
	// echo $sql;die;
	$sql_res=sql_select($sql);
	
	$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	?>
	
    <div style="max-height:340px; overflow:auto;">
        <table id="table_body2" width="700" border="1" rules="all" class="rpt_table">
			<thead>
				<tr>
					<th width="35">SL</th>
					<th width="100">Work Order</th>
					<th width="120">Job no</th>
					<th width="120">Style Ref</th>
					<th width="100">Buyer</th>
					<th width="100">Buyer PO</th>
					<th>Year</th>
				</tr>
			</thead>
			<tbody>
				<? 
				$i=1;
				foreach($sql_res as $v)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<?= $bgcolor; ?>" onClick="js_set_value('<?= $v['STYLE']; ?>')" style="cursor:pointer;">
						<td width="35"><?= $i; ?></td>
						<td width="100"><p><?= $v['WO_ORDER_NO']; ?></p></td>
						<td width="120"><p><?= $v['JOB_NO']; ?></p></td>
						<td width="120"><p><?= $v['STYLE']; ?></p></td>
						<td width="100"><p><?= $buyer_arr[$v['BUYER_NAME']]; ?></p></td>
						<td width="100"><p><?= $v['PO_NUMBER']; ?></p></td>
						<td align="center"><p><?= $v['YEAR']; ?></p></td>
					</tr>
					<? $i++; 
				} ?>
			</tbody>	
		</table>
	</div>
	<script> setFilterGrid("table_body2",-1); </script>
	<?
	exit();
}
 
 
if($action=="report_generate")
{
	$process = array(&$_POST);
	// pre($process);die;
	extract(check_magic_quote_gpc( $process ));

	$company_id		= str_replace("'","",$cbo_company_name);
	$location_id	= str_replace("'","",$cbo_location);
	$buyer_id		= str_replace("'","",$cbo_buyer_name); 
	$style_no		= str_replace("'","",$txt_style_no); 
	$challan_no		= str_replace("'","",$txt_challan_no); 
	$color_id		= str_replace("'","",$cbo_color_id); 
	$form_date		= str_replace("'","",$txt_date_from); 
	$to_date		= str_replace("'","",$txt_date_to);  
	$type			= str_replace("'","",$type);   
	$report_type 	= $type;

	if($type==1)//SHOW BUTTON    ###  GBL REF_FROM (1,2)
	{ 
		// ============================================================================================================
		//											LIBRARY ARRAY
		// ============================================================================================================ 
		$company_library 	= return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
		$buyer_library		= return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  ); 
		$color_library		= return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
		$body_part_library	= return_library_array( "select id,bundle_use_for from ppl_bundle_title ", "id", "bundle_use_for"  );
		$locationArray		= return_library_array( "select id,location_name from lib_location ", "id", "location_name"  );
		$user_arr 			= return_library_array( "select id, user_name from user_passwd where status_active=1 and is_deleted=0", "id", "user_name"  );
		 
	
	  
		// ============================================================================================================
		//												CONDITIONS 
		// ============================================================================================================
		$cond_sql ="";
        $cond_sql .= $company_id    ? " and a.serving_company =$company_id " :""; 
        $cond_sql .= $buyer_id    	? " and e.buyer_name=$buyer_id" :"";   
        $cond_sql .= $location_id 	? " and a.location=$location_id " :""; 
        $cond_sql .= $style_no 		? " and e.style_ref_no like '%$style_no%' " :""; 
		$cond_sql .= $color_id		? " and f.color_number_id in($color_id)" :"";
        $cond_sql .= $challan_no	? " and c.sys_number like '%$challan_no%' " :"";
        $cond_sql .= ($form_date && $to_date) ?" and a.production_date between '".change_date_format($form_date,'dd-mm-yyyy','-',1)."' and '".change_date_format($to_date,'dd-mm-yyyy','-',1)."'"  : "";

	
		// ============================================================================================================
		//												BUNDLE ISSUED TO PRINT DATA
		// ============================================================================================================	
		$main_sql = "select a.wo_order_no,a.production_date as prod_date,c.remarks,e.buyer_name,d.po_number,e.style_ref_no as style,f.item_number_id as item,f.color_number_id as color,c.body_part,c.working_company_id,b.production_qnty as prod_qty,c.id as sys_id,c.sys_number,d.id as po_id,e.id as job_id,b.bundle_no,b.barcode_no,a.production_type,b.print_receive_status as mtrl_rcve_status,c.sys_number_prefix_num as challan from pro_garments_production_mst a, pro_garments_production_dtls b,pro_gmts_delivery_mst c,wo_po_break_down d, wo_po_details_master e,wo_po_color_size_breakdown f where a.id=b.mst_id and c.id=a.delivery_mst_id and a.po_break_down_id=d.id and d.job_id=e.id and d.id=f.po_break_down_id and f.id=b.color_size_break_down_id $cond_sql and a.embel_name=1 and a.production_type in(2) and a.wo_order_no IS NOT NULL and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0  and f.status_active=1 and f.is_deleted=0 order by c.id asc"; 
		// echo $main_sql; die;
		$main_sql_res = sql_select($main_sql);  
		if (count($main_sql_res) == 0 ) 
		{
			echo "<h1 style='color:red; font-size: 17px;text-align:center;margin-top:20px;'> ** Data Not Found ** </h1>" ;
			die();
		}
		$main_data_array = $po_id_array = $barcode_arr =$barcode_array = array();
		foreach ($main_sql_res as  $v) 
		{
			$po_id_array	[$v['PO_ID']] 		     = $v['PO_ID'];
			$barcode_array	[$v['BARCODE_NO']] 	 	 = $v['BARCODE_NO'];
			$challan_arr	[$v['CHALLAN']] 		 = $v['CHALLAN'];
			$challan_wise_sys_arr[$v['CHALLAN']]     = $v['SYS_ID'];

			$barcode_arr[$v['SYS_ID']][$v['BARCODE_NO']] 	= $v['BARCODE_NO'];
			$main_data_array[$v['SYS_ID']]['WO_ORDER_NO'] 	= $v['WO_ORDER_NO'];
			$main_data_array[$v['SYS_ID']]['PROD_DATE'] 	= $v['PROD_DATE'];
			$main_data_array[$v['SYS_ID']]['SYS_NUMBER'] 	= $v['SYS_NUMBER'];
			$main_data_array[$v['SYS_ID']]['BUYER_NAME'] 	= $v['BUYER_NAME']; 
			$main_data_array[$v['SYS_ID']]['ITEM'] 			= $v['ITEM'];
			$main_data_array[$v['SYS_ID']]['BODY_PART']		= $v['BODY_PART'];
			$main_data_array[$v['SYS_ID']]['REMARKS']		= $v['REMARKS'];
			$main_data_array[$v['SYS_ID']]['WO_COM_ID']		= $v['WORKING_COMPANY_ID'];
			$main_data_array[$v['SYS_ID']]['PROD_QTY'] 		+= $v['PROD_QTY'];
			$main_data_array[$v['SYS_ID']]['NO_OF_BUNDLE']++;

			if (!$main_data_array[$v['SYS_ID']]['MTRL_RCVE_STATUS']) 
			{
				$main_data_array[$v['SYS_ID']]['MTRL_RCVE_STATUS'] = $v['MTRL_RCVE_STATUS'];
			}

			
			if (!$sys_wise_po_arr[$v['SYS_ID']][$v['PO_ID']]) 
			{
				$main_data_array[$v['SYS_ID']]['PO_NUMBER'] .= $v['PO_NUMBER'].','; 
			}
			if (!$sys_wise_color_arr[$v['SYS_ID']][$v['COLOR']]) 
			{ 
				$main_data_array[$v['SYS_ID']]['COLOR'] .= $color_library[$v['COLOR']].',';
			}

			if (!$sys_wise_job_arr[$v['SYS_ID']][$v['JOB_ID']]) 
			{
				$main_data_array[$v['SYS_ID']]['STYLE'] .= $v['STYLE'].','; 
			}

			$sys_wise_po_arr[$v['SYS_ID']][$v['PO_ID']] 	= $v['PO_ID'];
			$sys_wise_job_arr[$v['SYS_ID']][$v['JOB_ID']] 	= $v['JOB_ID'];
			$sys_wise_color_arr[$v['SYS_ID']][$v['COLOR']] = $v['COLOR']; 
		}
		unset($main_sql_res);
		unset($sys_wise_po_arr);
		unset($sys_wise_job_arr);
		unset($sys_wise_color_arr);
		// pre($challan_wise_sys_arr);die;
	 
		
		//=========================================================================================================
		//												CLEAR TEMP ENGINE
		// ==========================================================================================================
		$con = connect();
		execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form = 137 and ref_from in(1,2)");
		oci_commit($con);   
		// =========================================================================================================
		//												INSERT DATA INTO TEMP ENGINE
		// =========================================================================================================
		fnc_tempengine("gbl_temp_engine", $user_id, 137, 1,$po_id_array, $empty_arr); 
		fnc_tempengine("gbl_temp_engine", $user_id, 137, 2,$barcode_array, $empty_arr);
		unset($po_id_array);
		unset($challan_arr);
		
		//========================================================================================================
		//												CUTTING AND RCVE FROM PRINT
		// ========================================================================================================
		$cutting_sql = "select a.serving_company,a.location, a.po_break_down_id as po_id,a.production_type,b.barcode_no from pro_garments_production_mst a,pro_garments_production_dtls b,gbl_temp_engine tmp where a.id=b.mst_id and a.po_break_down_id=tmp.ref_val and a.production_type in (1,3) and a.embel_name in (0,1) and a.status_active=1 and a.is_deleted=0 and tmp.entry_form=137 and tmp.ref_from=1 and tmp.user_id=$user_id";
		
		// echo $cutting_sql; die;
		$cutting_sql_res = sql_select($cutting_sql);
		$po_wise_cut_arr = $barcode_rcv_arr = array();
		foreach ($cutting_sql_res as $key => $v) 
		{
			$barcode_array2 [$v['BARCODE_NO']] = $v['BARCODE_NO'];
			if ($barcode_array [$v['BARCODE_NO']]) 
			{
				if ( $v['PRODUCTION_TYPE'] == 1) 
				{
					$barcode_wise_cut_arr[$v['BARCODE_NO']]['CUT_COMPANY']  = $v['SERVING_COMPANY']; 
					$barcode_wise_cut_arr[$v['BARCODE_NO']]['CUT_LOCATION'] = $v['LOCATION']; 
				}
				else{
					$barcode_rcv_arr[$v['BARCODE_NO']] = $v['BARCODE_NO'];
				} 
			} 
		} 
		unset($cutting_sql_res);
		unset($barcode_array);
		
		// BUNDLE RECEIVE FROM PRINT CHECK
		foreach ($barcode_arr as $sys_id => $sys_arr) 
		{
			 foreach ($sys_arr as  $barcode) 
			 {
				$company_id  = $barcode_wise_cut_arr[$barcode]['CUT_COMPANY'];
				$location_id = $barcode_wise_cut_arr[$barcode]['CUT_LOCATION'];

				$main_data_array[$sys_id]['CUT_COMPANY'][$company_id]    = $company_library[$company_id];
				$main_data_array[$sys_id]['CUT_LOCATION'][$location_id]  = $locationArray[$location_id];

				if ($barcode_rcv_arr[$barcode]) 
				{
					$main_data_array[$sys_id]['RCVE_FROM_PRINT'] = 1; 
				}
			 }
		}
		unset($barcode_wise_cut_arr);
		unset($barcode_rcv_arr);
		
		// pre($barcode_rcv_arr);die;
		// ============================================================================================================
		//												Printing Material Receive [Bundle]
		// ============================================================================================================	
		
		$printing_sql = "SELECT a.inserted_by,b.challan_no  from printing_bundle_receive_mst a, printing_bundle_receive_dtls b,gbl_temp_engine tmp where a.id=b.mst_id and a.entry_form=614 and b.barcode_no=tmp.ref_val and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and tmp.entry_form=137 and tmp.ref_from=2 and tmp.user_id=$user_id";
		// echo $printing_sql ; die; 
		$printing_sql_res = sql_select($printing_sql);
		foreach ($printing_sql_res as  $v) 
		{
			$sys_id = $challan_wise_sys_arr[$v['CHALLAN_NO']];
			if ($sys_id) 
			{
				$main_data_array[$sys_id]['INSERTED_BY'] = $user_arr[$v['INSERTED_BY']];
				 
			}
		}
		unset($printing_sql_res);
		unset($challan_wise_sys_arr);
		
		// ============================================================================================================
		//												CLEAR TEMP ENGINE
		// ============================================================================================================
		$con = connect();
		execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form = 137 and ref_from in (1,2)");
		oci_commit($con);  
		disconnect($con);

		// Print Button
		$print_report_format=return_field_value("format_id","lib_report_template","template_name =".$cbo_company_name." and module_id=7 and report_id =50 and is_deleted=0 and status_active=1");
    	$format_ids=explode(",",$print_report_format);
		// pre($format_ids); die;

		if ($format_ids[0]==84) 	$type=1; // Print 2
		elseif($format_ids[0]==85)  $type=2; // Print 3
		elseif($format_ids[0]==86) 	$type=3; // Print
		elseif($format_ids[0]==89) 	$type=4; // Print4
		elseif($format_ids[0]==129) $type=5; // Print 5
		elseif($format_ids[0]==161) $type=6; // Print 6
		elseif($format_ids[0]==191) $type=7; // Print 7
		elseif($format_ids[0]==220) $type=8; // Print 8
		elseif($format_ids[0]==235) $type=9; // Print 9
		// pre($main_data_array); die;
		ob_start();
		$width = 1670;
		?> 
		<style>
			.tableFixHead { max-height: 400px !important; overflow: auto; margin: 20px 0;}
			.tableFixHead thead th { position: sticky; top: -2px; z-index: 1;}
			.success {color:#42ba96;}
			.danger {color:#FF0000;}
		</style>
		<fieldset> 
			<table width="100%" cellspacing="0"> 
			<table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="tbl_caption" align="center">
				<thead class="form_caption" > 
					<tr>
						<td colspan="24" align="center" style="font-size:14px; font-weight:bold" >Printing Material Receive Report</td>
					</tr>  
				</thead>
			</table>	
			<div align="center" style="height:auto; width:<? echo $width+20;?>px; margin:0 auto; padding:0;">  
				<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $width;?>" rules="all" id="rpt_table_header" align="left">
					<thead class="form_caption" >	  
						<tr>
							<th width="30">Sl.</th>
							<th width="120">Cutting Company </th>
							<th width="120">Cutting Location </th>
							<th width="100">Work Order </th>
							<th width="80">Challan Issue Date </th> 
							<th width="100">Challan No </th>
							<th width="80">Buyer </th>
							<th width="100">Buyer PO </th>
							<th width="120">Style </th>
							<th width="80">Item </th>
							<th width="80">Body Color </th>
							<th width="80">Body Part </th> 
							<th width="100">Mtrl Recv Challan From Cutting </th>
							<th width="100">Bundle challn Rcv from Print </th>
							<th width="80">Bundle qty </th>
							<th width="80">Challan Qty </th> 
							<th width="120">Remarks </th> 
							<th width="100">Insert User </th> 
						</tr>
					</thead>
				</table>
				<div style="width:<?= $width+20;?>px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
					<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body" width="<?= $width; ?>" rules="all" align="left">
						<tbody>
							<?
							$i = 0 ; 
							

							foreach ($main_data_array as $sys_id => $v) 
							{ 
								$cut_company = implode(',',$v['CUT_COMPANY']) ;
								$cut_location = implode(',',$v['CUT_LOCATION']) ;
								
								if ($i % 2 == 0)  $bgcolor = "#E9F3FF";  else $bgcolor = "#FFFFFF";
								?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
										<td width="30"> <?= ++$i; ?> </td>
										<td width="120"> <p> <?=  $cut_company;?> </p> </td>
										<td width="120"> <p> <?=  $cut_location; ?> </p> </td>
										<td width="100" > <p> <?= $v['WO_ORDER_NO'] ?> </p> </td>
										<td width="80"> <p> <?= $v['PROD_DATE'] ?> </p> </td>
										<td width="100"> <a href='#'  onclick="fnc_issue_print_embroidery(<?= $type.','. $sys_id.','. $v['WO_COM_ID'].','.$v['BODY_PART']?>)"> <?= $v['SYS_NUMBER'] ?> </a> </td> 
										<td width="80"> <p> <?= $buyer_library[$v['BUYER_NAME']] ?> </p> </td>
										<td width="100"> <p> <?= trim($v['PO_NUMBER'],',') ?> </p> </td>  
										<td width="120"> <p> <?= trim($v['STYLE'],',') ?> </p> </td>  
										<td width="80"> <p> <?= $garments_item[$v['ITEM']] ?> </p> </td> 
										<td width="80"> <p> <?= trim($v['COLOR'],',') ?> </p> </td> 
										<td width="80"> <p> <?= $body_part_library[$v['BODY_PART']] ?> </p> </td>   
										<td width="100" align="center"> <p> <?= $v['MTRL_RCVE_STATUS'] ? "YES" : "NO" ?> </p> </td>
										<td width="100" align="center"> <p> <?= $v['RCVE_FROM_PRINT'] ? "YES" : "NO" ?> </p> </td>
										<td width="80" align="right"> <p> <?= $v['NO_OF_BUNDLE'] ?> </p> </td>
										<td width="80" align="right"> <p> <?= $v['PROD_QTY'] ?> </p> </td>   
										<td width="120" > <p> <?= $v['REMARKS'] ?> </p> </td>
										<td width="100" > <p> <?= $v['INSERTED_BY'] ?> </p></td>   
									</tr> 
								<? 
								$total_issue_qty += $v['PROD_QTY'];
							}
							?>
						</tbody> 
					</table> 
				</div>
				<div style="width:<?= $width+20;?>px;float:left;">
					<table style="float:left;" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" width="<?= $width;?>">
						<tfoot>
							<tr>
								<th width="30"></th>
								<th width="120"></th>
								<th width="120"></th>
								<th width="100"></th>
								<th width="80">Challan Qty</th>
								<th width="100" id="row_count"><?=$i ?></th>
								<th width="80"></th>
								<th width="100"></th>
								<th width="120"></th>
								<th width="80"></th>
								<th width="80"></th>
								<th width="80"></th>
								<th width="100"></th>
								<th width="100"></th>
								<th width="80">Total Qty</th>
								<th width="80" id="total_prod_qty" align="right"><?= $total_issue_qty ?> </th> 
								<th width="120"></th>
								<th width="100"></th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</fieldset>
	   <?  
	   unset($main_data_array); 
	   unset($company_library);
	   unset($buyer_library);
	   unset($body_part_library);
	   unset($color_library);
	   unset($locationArray);
	   unset($user_arr); 
	}  
	
	else if($type==2) //DELIVERY BUTTON   ###  GBL REF_FROM (3,4)
	{ 
		// ============================================================================================================
		//											LIBRARY ARRAY
		// ============================================================================================================ 
		$company_library 	= return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
		$buyer_library		= return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  ); 
		$color_library		= return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
		$body_part_library	= return_library_array( "select id,bundle_use_for from ppl_bundle_title ", "id", "bundle_use_for"  );
		$locationArray		= return_library_array( "select id,location_name from lib_location ", "id", "location_name"  );
		$user_arr 			= return_library_array( "select id, user_name from user_passwd where status_active=1 and is_deleted=0", "id", "user_name"  );
	
	  
		// ============================================================================================================
		//												CONDITIONS 
		// ============================================================================================================
		$cond_sql ="";
        $cond_sql .= $company_id    ? " and a.company_id =$company_id " :""; 
        $cond_sql .= $buyer_id    	? " and c.buyer_buyer='$buyer_id'" :"";   
        $cond_sql .= $location_id 	? " and f.location=$location_id " :""; 
        $cond_sql .= $style_no 		? " and c.style_ref_no like '%$style_no%' " :""; 
		$cond_sql .= $color_id		? " and e.color_number_id in($color_id)" :"";
        $cond_sql .= $challan_no	? " and a.issue_number like '%$challan_no%' " :"";
        $cond_sql .= ($form_date && $to_date) ?" and a.issue_date between '".change_date_format($form_date,'dd-mm-yyyy','-',1)."' and '".change_date_format($to_date,'dd-mm-yyyy','-',1)."'"  : "";

	
		// ============================================================================================================
		//												Printing Delivery Entry [Bundle]
		// ============================================================================================================	
		$main_sql = "SELECT b.id,a.id as issue_id,a.entry_form,a.issue_date as delivery_date,a.issue_number as del_challan,a.company_id,a.remarks,a.inserted_by,b.quantity as del_qc_qty,b.wo_break_id,f.delivery_mst_id,f.location from printing_bundle_issue_mst a,printing_bundle_issue_dtls b,wo_po_details_master c,wo_po_break_down d,wo_po_color_size_breakdown e,pro_garments_production_mst f where a.id=b.mst_id and c.id=d.job_id and e.po_break_down_id=d.id and e.id = b.wo_break_id and b.bundle_mst_id=f.id and a.entry_form=499 $cond_sql and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 order by a.id"; 
		// echo $main_sql; die;

		$main_sql_res = sql_select($main_sql);  
		if (count($main_sql_res) == 0 ) 
		{
			echo "<h1 style='color:red; font-size: 17px;text-align:center;margin-top:20px;'> ** Data Not Found ** </h1>" ;
			die();
		}
		$main_data_array = $delivery_mst_id_array = array();
		foreach ($main_sql_res as  $v) 
		{ 
				$main_data_array[$v['DELIVERY_MST_ID']]['DEL_QC_QTY'] 	 += $v['DEL_QC_QTY'];
				$main_data_array[$v['DELIVERY_MST_ID']]['DELIVERY_DATE'] = $v['DELIVERY_DATE'];
				$main_data_array[$v['DELIVERY_MST_ID']]['DEL_CHALLAN'] 	 = $v['DEL_CHALLAN'];
				$main_data_array[$v['DELIVERY_MST_ID']]['ISSUE_ID'] 	 = $v['ISSUE_ID'];
				$main_data_array[$v['DELIVERY_MST_ID']]['LOCATION'] 	 = $v['LOCATION'];
				$main_data_array[$v['DELIVERY_MST_ID']]['COMPANY_ID'] 	 = $v['COMPANY_ID'];
				$main_data_array[$v['DELIVERY_MST_ID']]['INSERTED_BY'] 	 = $user_arr[$v['INSERTED_BY']];
				$main_data_array[$v['DELIVERY_MST_ID']]['REMARKS'] 	 	 = $v['REMARKS'];

				$delivery_mst_id_array[$v['DELIVERY_MST_ID']] = $v['DELIVERY_MST_ID'];
				$color_size_id_array[$v['WO_BREAK_ID']] 	  = $v['WO_BREAK_ID'];
			 
		}
		unset($main_sql_res);
		// pre($main_data_array); die;
		
		// =====================================================================================================
		//												CLEAR TEMP ENGINE
		// =====================================================================================================
		$con = connect();
		execute_query("DELETE from gbl_temp_engine where user_id=$user_id and entry_form = 137 and ref_from in(3,4)");
		oci_commit($con);  

		// =====================================================================================================
		//												INSERT DELIVERY_MST_ID INTO TEMP ENGINE
		// =====================================================================================================
		fnc_tempengine("gbl_temp_engine", $user_id, 137, 3,$delivery_mst_id_array, $empty_arr);  
		oci_commit($con);  
		unset($delivery_mst_id_array);

		// ============================================================================================================
		//												 Printing Production [Bundle]
		// ============================================================================================================	
		$color_size_id_cond = $color_size_id_cond2 = '';
		if (count($color_size_id_array)) 
		{
			$color_size_id_cond  = where_con_using_array($color_size_id_array,0,'b.wo_break_id');
			$color_size_id_cond2 = where_con_using_array($color_size_id_array,0,'f.id');
		}
		$print_prod_sql = "SELECT d.delivery_mst_id,b.quantity from printing_bundle_issue_mst a,printing_bundle_issue_dtls b,subcon_ord_dtls c,pro_garments_production_mst d,gbl_temp_engine tmp where a.id=b.mst_id and c.id=b.wo_dtls_id and b.bundle_mst_id=d.id and d.delivery_mst_id=tmp.ref_val and tmp.entry_form=137 and tmp.ref_from=3 and tmp.user_id=$user_id $color_size_id_cond  and a.entry_form=497 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 order by a.id"; 
		// echo $main_sql; die;

		$print_prod_sql_res = sql_select($print_prod_sql);   
		foreach ($print_prod_sql_res as  $v) 
		{ 
				$main_data_array[$v['DELIVERY_MST_ID']]['PRINT_PROD'] 	 += $v['QUANTITY']; 
			 
		}
		unset($print_prod_sql_res);
		// echo $main_sql; die;
		// ============================================================================================================
		//												BUNDLE ISSUED TO PRINT DATA
		// ============================================================================================================	
		$issue_sql = "SELECT a.wo_order_no,a.production_date as prod_date,e.buyer_name,d.po_number,e.style_ref_no as style,f.item_number_id as item,f.color_number_id as color,c.body_part,c.working_company_id,b.production_qnty as prod_qty,c.id as sys_id,c.sys_number,d.id as po_id,e.id as job_id,b.bundle_no,b.barcode_no,a.production_type,b.print_receive_status as mtrl_rcve_status from pro_garments_production_mst a, pro_garments_production_dtls b,pro_gmts_delivery_mst c,wo_po_break_down d, wo_po_details_master e,wo_po_color_size_breakdown f,gbl_temp_engine tmp where a.id=b.mst_id and c.id=a.delivery_mst_id and a.po_break_down_id=d.id and d.job_id=e.id and d.id=f.po_break_down_id and f.id=b.color_size_break_down_id and c.id=tmp.ref_val and a.embel_name=1 and a.production_type in(2) and a.wo_order_no IS NOT NULL and tmp.entry_form=137 and tmp.ref_from=3 and tmp.user_id=$user_id $color_size_id_cond2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0  and f.status_active=1 and f.is_deleted=0"; 
		// echo $issue_sql;die;
		 
		$po_id_array = $barcode_arr = $barcode_array = array();
		$issue_sql_res = sql_select($issue_sql);  
		foreach ($issue_sql_res as  $v) 
		{
			$po_id_array[$v['PO_ID']] 		  	 = $v['PO_ID'];
			$barcode_array	[$v['BARCODE_NO']] 	 = $v['BARCODE_NO'];
			$barcode_arr[$v['SYS_ID']][$v['BARCODE_NO']] 	= $v['BARCODE_NO'];
			// echo $v['BARCODE_NO']; die;
			$main_data_array[$v['SYS_ID']]['WO_ORDER_NO'] 	= $v['WO_ORDER_NO'];
			$main_data_array[$v['SYS_ID']]['PROD_DATE'] 	= $v['PROD_DATE'];
			$main_data_array[$v['SYS_ID']]['SYS_NUMBER'] 	= $v['SYS_NUMBER'];
			$main_data_array[$v['SYS_ID']]['BUYER_NAME'] 	= $v['BUYER_NAME']; 
			$main_data_array[$v['SYS_ID']]['ITEM'] 			= $v['ITEM'];
			$main_data_array[$v['SYS_ID']]['BODY_PART']		= $v['BODY_PART'];
			$main_data_array[$v['SYS_ID']]['WO_COM_ID']		= $v['WORKING_COMPANY_ID'];
			$main_data_array[$v['SYS_ID']]['PROD_QTY'] 		+= $v['PROD_QTY'];
			$main_data_array[$v['SYS_ID']]['NO_OF_BUNDLE']++;

			if (!$main_data_array[$v['SYS_ID']]['MTRL_RCVE_STATUS']) 
			{
				$main_data_array[$v['SYS_ID']]['MTRL_RCVE_STATUS'] = $v['MTRL_RCVE_STATUS'];
			}

			
			if (!$sys_wise_po_arr[$v['SYS_ID']][$v['PO_ID']]) 
			{
				$main_data_array[$v['SYS_ID']]['PO_NUMBER'] .= $v['PO_NUMBER'].','; 
			}
			if (!$sys_wise_color_arr[$v['SYS_ID']][$v['COLOR']]) 
			{ 
				$main_data_array[$v['SYS_ID']]['COLOR'] .= $color_library[$v['COLOR']].',';
			}

			if (!$sys_wise_job_arr[$v['SYS_ID']][$v['JOB_ID']]) 
			{
				$main_data_array[$v['SYS_ID']]['STYLE'] .= $v['STYLE'].','; 
			}

			$sys_wise_po_arr[$v['SYS_ID']][$v['PO_ID']] 	= $v['PO_ID'];
			$sys_wise_job_arr[$v['SYS_ID']][$v['JOB_ID']] 	= $v['JOB_ID'];
			$sys_wise_color_arr[$v['SYS_ID']][$v['COLOR']] = $v['COLOR'];
			// $barcode_wise_sys_array[$v['BARCODE_NO']]= $v['SYS_ID'];
		}
		unset($issue_sql_res);
		// pre($sys_wise_color_arr);die;
	 
		 
		// =========================================================================================================
		//												INSERT PO ID INTO TEMP ENGINE
		// =========================================================================================================
		fnc_tempengine("gbl_temp_engine", $user_id, 137, 4,$po_id_array, $empty_arr);  
		
		//========================================================================================================
		//												CUTTING AND RCVE FROM PRINT
		// ========================================================================================================
		$cutting_sql = "SELECT a.serving_company, a.location, a.po_break_down_id as po_id,a.production_type,b.barcode_no from pro_garments_production_mst a,pro_garments_production_dtls b,gbl_temp_engine tmp where a.id=b.mst_id and a.po_break_down_id=tmp.ref_val and a.production_type in (1,3) and a.embel_name in (0,1) and a.status_active=1 and a.is_deleted=0 and tmp.entry_form=137 and tmp.ref_from=4 and tmp.user_id=$user_id";
		// echo $cutting_sql; die;
		$cutting_sql_res = sql_select($cutting_sql);
		$po_wise_cut_arr = $barcode_rcv_arr = array();
		foreach ($cutting_sql_res as $key => $v) 
		{
			if ($barcode_array[$v['BARCODE_NO']]) 
			{
				if ( $v['PRODUCTION_TYPE'] == 1) 
				{
					$barcode_wise_cut_arr[$v['BARCODE_NO']]['CUT_COMPANY']  = $v['SERVING_COMPANY']; 
					$barcode_wise_cut_arr[$v['BARCODE_NO']]['CUT_LOCATION'] = $v['LOCATION']; 
				}
				else{
					$barcode_rcv_arr[$v['BARCODE_NO']] = $v['BARCODE_NO'];
				} 
			} 
		} 
		unset($cutting_sql_res);

		foreach ($barcode_arr as $sys_id => $sys_arr) 
		{
			 foreach ($sys_arr as  $barcode) 
			 {
				$company_id  = $barcode_wise_cut_arr[$barcode]['CUT_COMPANY'];
				$location_id = $barcode_wise_cut_arr[$barcode]['CUT_LOCATION'];

				$main_data_array[$sys_id]['CUT_COMPANY'][$company_id]    = $company_library[$company_id];
				$main_data_array[$sys_id]['CUT_LOCATION'][$location_id]  = $locationArray[$location_id];

				if ($barcode_rcv_arr[$v['BARCODE_NO']]) 
				{
					$main_data_array[$sys_id]['RCVE_FROM_PRINT'] = 1; 
				}
			 }
		}
		unset($barcode_wise_cut_arr);
		unset($barcode_rcv_arr);
		unset($barcode_arr);
		// ============================================================================================================
		//												CLEAR TEMP ENGINE
		// ============================================================================================================
		$con = connect();
		execute_query("DELETE from gbl_temp_engine where user_id=$user_id and entry_form = 137 and ref_from in(3,4)");
		oci_commit($con);  
		disconnect($con);

		// Print Button
		$print_report_format=return_field_value("format_id","lib_report_template","template_name =$cbo_company_name  and module_id=15 and report_id=276 and is_deleted=0 and status_active=1");
    	$format_ids=explode(",",$print_report_format);
		// pre($format_ids); die;

		if($format_ids[0]==86)  	 $type=1; // Print
		else if($format_ids[0]==110) $type=2; // Print 2
		else if($format_ids[0]==85)  $type=3; // Print 3
		ob_start();
		$width = 1630;
		?> 
		<style>
			.tableFixHead { max-height: 400px !important; overflow: auto; margin: 20px 0;}
			.tableFixHead thead th { position: sticky; top: -2px; z-index: 1;}
			.success {color:#42ba96;}
			.danger {color:#FF0000;}
		</style>
		<fieldset> 
			<table width="100%" cellspacing="0"> 
			<table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="tbl_caption" align="center">
				<thead class="form_caption" > 
					<tr>
						<td colspan="24" align="center" style="font-size:14px; font-weight:bold" >Printing Material Receive Report</td>
					</tr>  
				</thead>
			</table>	
			<div align="center" style="height:auto; width:<? echo $width+20;?>px; margin:0 auto; padding:0;">  
				<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $width;?>" rules="all" id="rpt_table_header" align="left">
					<thead class="form_caption" >	  
						<tr>
							<th width="30">Sl.</th>
							<th width="120">Cutting Company </th>
							<th width="120">Cutting Location </th>
							<th width="100">Work Order </th>
							<th width="80">Challan Delivery Date </th> 
							<th width="100">Issue Challan No </th>
							<th width="120">Delivery Challan No </th>
							<th width="80">Buyer </th>
							<th width="100">Buyer PO </th>
							<th width="120">Style </th>
							<th width="80">Item </th>
							<th width="80">Body Color </th>
							<th width="80">Body Part </th> 
							<th width="80">Print Delivery</th> 
							<th width="100">Bundle challn Rcv from Print </th> 
							<th width="80">Challan Qty </th> 
							<th width="80">Rej. Qty </th> 
							<th width="100">Insert User </th> 
						</tr>
					</thead>
				</table>
				<div style="width:<?= $width+20;?>px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
					<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body" width="<?= $width; ?>" rules="all" align="left">
						<tbody>
							<?
							$i = 0 ; 
							$total_qc_qty = 0 ; 

							foreach ($main_data_array as $sys_id => $v) 
							{  
								$cut_company  = implode(',',$v['CUT_COMPANY']) ;
								$cut_location = implode(',',$v['CUT_LOCATION']) ;
								
								
								$reject_qty 		= $v['PRINT_PROD'] - $v['DEL_QC_QTY'];
								$total_qc_qty 		+= $v['DEL_QC_QTY'];
								$total_reject_qty 	+= $reject_qty;

								if ($i % 2 == 0)  $bgcolor = "#E9F3FF";  else $bgcolor = "#FFFFFF";
								?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
										<td width="30"> <?= ++$i; ?> </td>
										<td width="120"> <p> <?= trim($cut_company,','); ?> </p> </td>
										<td width="120"> <p> <?= trim($cut_location,','); ?> </p> </td>
										<td width="100" > <p> <?= $v['WO_ORDER_NO'] ?> </p> </td>
										<td width="80"> <p> <?= $v['DELIVERY_DATE'] ?> </p> </td>
										<td width="100"> <?= $v['SYS_NUMBER'] ?> </td> 
										<td width="120"> <a href='#'  onclick="fnc_embl_delivery(<?= $type.','. $v['COMPANY_ID'].','.$v['ISSUE_ID'].','.$v['LOCATION']?>)"> <?= $v['DEL_CHALLAN'] ?> </a> </td> 
										<td width="80"> <p> <?= $buyer_library[$v['BUYER_NAME']] ?> </p> </td>
										<td width="100"> <p> <?= trim($v['PO_NUMBER'],',') ?> </p> </td>  
										<td width="120"> <p> <?= trim($v['STYLE'],',') ?> </p> </td>  
										<td width="80"> <p> <?= $garments_item[$v['ITEM']] ?> </p> </td> 
										<td width="80"> <p> <?= trim($v['COLOR'],',') ?> </p> </td> 
										<td width="80"> <p> <?= $body_part_library[$v['BODY_PART']] ?> </p> </td>   
										<td width="80" align="center"> <p> <?= $v['DEL_QC_QTY'] ? "YES" : "NO" ?> </p> </td>
										<td width="100" align="center"> <p> <?= $v['RCVE_FROM_PRINT'] ? "YES" : "NO" ?> </p> </td>
										<td width="80" align="right"> <p> <?= $v['DEL_QC_QTY'] ?> </p> </td> 
										<td width="80" align="right"><p> <?= $reject_qty  ?> </p> </td> 
										<td width="100"><p> <?= $v['INSERTED_BY'] ?> </p> </td>    
									</tr> 
								<? 
							}
							?>
						</tbody> 
					</table> 
				</div>
				<div style="width:<?= $width+20;?>px;float:left;">
					<table style="float:left;" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" width="<?= $width;?>">
						<tfoot>
							<tr>
								<th width="30"></th>
								<th width="120"></th>
								<th width="120"></th>
								<th width="100"></th>
								<th width="80"></th>
								<th width="100">Challan Qty</th>
								<th width="120" id="row_count" ><?=$i ?></th>
								<th width="80"></th>
								<th width="100"></th>
								<th width="120"></th>
								<th width="80"></th>
								<th width="80"></th>
								<th width="80"></th>
								<th width="80"></th>
								<th width="100">Total Qty</th>
								<th width="80" id="total_prod_qty" align="right"> <?= $total_qc_qty ?></th>
								<th width="80" id="total_reject_qty" align="right"> <?= $total_reject_qty ?></th> 
								<th width="100"></th>  
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</fieldset>
	   <?  
	   unset($main_data_array); 
	   unset($company_library);
	   unset($buyer_library);
	   unset($body_part_library);
	   unset($color_library);
	   unset($locationArray);
	   unset($user_arr);  
	} 
	else if($type==3)//SUMMARY BUTTON    ###  GBL REF_FROM (5,6) 
	{ 
		// ============================================================================================================
		//											LIBRARY ARRAY
		// ============================================================================================================ 
		$company_library 	= return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
		$locationArray		= return_library_array( "select id,location_name from lib_location ", "id", "location_name"  );
		 
	
	  
		// ============================================================================================================
		//												CONDITIONS 
		// ============================================================================================================
		// FOR PRINT RECEIVED
		$cond_sql1 ="";
        $cond_sql1 .= $company_id   ? " and a.serving_company =$company_id " :""; 
        $cond_sql1 .= $buyer_id    	? " and e.buyer_name=$buyer_id" :"";   
        $cond_sql1 .= $location_id 	? " and a.location=$location_id " :""; 
        $cond_sql1 .= $style_no 	? " and e.style_ref_no like '%$style_no%' " :""; 
		$cond_sql1 .= $color_id		? " and f.color_number_id in($color_id)" :"";
        $cond_sql1 .= $challan_no	? " and c.sys_number like '%$challan_no%' " :"";
        $cond_sql1 .= ($form_date && $to_date) ?" and a.production_date between '".change_date_format($form_date,'dd-mm-yyyy','-',1)."' and '".change_date_format($to_date,'dd-mm-yyyy','-',1)."'"  : "";

		// FOR PRINT DELEVARY
		$cond_sql3 ="";
        $cond_sql3 .= $company_id   ? " and a.company_id =$company_id " :""; 
        $cond_sql3 .= $buyer_id    	? " and c.buyer_buyer='$buyer_id'" :"";   
        $cond_sql3 .= $location_id 	? " and f.location=$location_id " :""; 
        $cond_sql3 .= $style_no 	? " and c.style_ref_no like '%$style_no%' " :""; 
		$cond_sql3 .= $color_id		? " and e.color_number_id in($color_id)" :"";
        $cond_sql3 .= $challan_no	? " and a.issue_number like '%$challan_no%' " :"";
        $cond_sql3 .= ($form_date && $to_date) ?" and a.issue_date between '".change_date_format($form_date,'dd-mm-yyyy','-',1)."' and '".change_date_format($to_date,'dd-mm-yyyy','-',1)."'"  : "";

	
		// ============================================================================================================
		//												BUNDLE ISSUED TO PRINT DATA
		// ============================================================================================================	
		$rcve_sql = "SELECT b.production_qnty as prod_qty,c.id as sys_id,c.sys_number,d.id as po_id,b.bundle_no,b.barcode_no,b.color_size_break_down_id,a.production_type,c.sys_number_prefix_num as challan from pro_garments_production_mst a, pro_garments_production_dtls b,pro_gmts_delivery_mst c,wo_po_break_down d, wo_po_details_master e,wo_po_color_size_breakdown f where a.id=b.mst_id and c.id=a.delivery_mst_id and a.po_break_down_id=d.id and d.job_id=e.id and d.id=f.po_break_down_id and f.id=b.color_size_break_down_id $cond_sql1  and b.print_receive_status=1 and a.embel_name=1 and a.production_type in(2) and a.wo_order_no IS NOT NULL and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0  and f.status_active=1 and f.is_deleted=0"; 
		// echo $rcve_sql; die;
		$rcve_sql_res = sql_select($rcve_sql);  
		  
		$received_data_array = $po_id_array = $barcode_arr =$barcode_array =$color_size_id_array = array();
		foreach ($rcve_sql_res as  $v) 
		{
			$po_id_array	[$v['PO_ID']] 		     = $v['PO_ID'];
			$barcode_array	[$v['BARCODE_NO']] 	 	 = $v['BARCODE_NO'];
			$challan_arr	[$v['CHALLAN']] 		 = $v['CHALLAN'];
			$challan_wise_sys_arr[$v['CHALLAN']]     = $v['SYS_ID'];
			$color_size_id_array[$v['COLOR_SIZE_BREAK_DOWN_ID']] = $v['COLOR_SIZE_BREAK_DOWN_ID'];
			
			$barcode_arr[$v['SYS_ID']][$v['BARCODE_NO']] 	     = $v['BARCODE_NO']; 
			$received_data_array[$v['SYS_ID']]['CHALLAN_NO'] 	 = $v['CHALLAN']; 
			$received_data_array[$v['SYS_ID']]['PROD_QTY'] 		+= $v['PROD_QTY'];  
		}
		unset($main_sql_res);
		unset($sys_wise_po_arr);
		unset($sys_wise_job_arr);
		unset($sys_wise_color_arr);
		// pre($received_data_array);die;
	 

		//============================================================================================================
		//												Printing Delivery Entry [Bundle]
		// ============================================================================================================	
		$delivery_sql = "SELECT b.id as dtls_id,f.delivery_mst_id as sys_id,b.quantity as del_qc_qty,b.barcode_no,d.id as po_id,f.challan_no,b.wo_break_id from printing_bundle_issue_mst a,printing_bundle_issue_dtls b,wo_po_details_master c,wo_po_break_down d,wo_po_color_size_breakdown e,pro_garments_production_mst f where a.id=b.mst_id and c.id=d.job_id and e.po_break_down_id=d.id and e.id = b.wo_break_id and b.bundle_mst_id=f.id and a.entry_form=499 $cond_sql3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 order by a.id"; 
		// echo $delivery_sql; die;

		$delivery_sql_res = sql_select($delivery_sql);  
		if (count($delivery_sql_res) == 0 && count($rcve_sql_res) == 0 ) 
		{
			echo "<h1 style='color:red; font-size: 17px;text-align:center;margin-top:20px;'> ** Data Not Found ** </h1>" ;
			die();
		}

		$delivery_data_array = $delivery_mst_id_array = $bundle_issue_dtls_array = array();
		foreach ($delivery_sql_res as  $v) 
		{ 
			$po_id_array	[$v['PO_ID']] 		 = $v['PO_ID'];
			$barcode_array	[$v['BARCODE_NO']] 	 = $v['BARCODE_NO'];
			$delivery_mst_id_array[$v['SYS_ID']] = $v['SYS_ID'];

			if (!$bundle_issue_dtls_array[$v['DTLS_ID']]) // NOT REPEAT QTY
			{
				$delivery_data_array[$v['SYS_ID']]['DEL_QC_QTY'] 	 += $v['DEL_QC_QTY']; 
				$delivery_data_array[$v['SYS_ID']]['CHALLAN_NO'] 	  = $v['CHALLAN_NO'];  

				$barcode_arr[$v['SYS_ID']][$v['BARCODE_NO']] 		  = $v['BARCODE_NO'];  
			} 
			$bundle_issue_dtls_array[$v['DTLS_ID']] = $v['DTLS_ID'];
			$color_size_id_array[$v['WO_BREAK_ID']] 	  = $v['WO_BREAK_ID'];
		}
		unset($delivery_sql_res);  
		// echo count($delivery_data_array); die;
		//=========================================================================================================
		//												CLEAR TEMP ENGINE
		// ==========================================================================================================
		$con = connect();
		execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form = 137 and ref_from in(5,6)");
		oci_commit($con);


		// =========================================================================================================
		//												INSERT DATA INTO TEMP ENGINE
		// =========================================================================================================
		fnc_tempengine("gbl_temp_engine", $user_id, 137, 5,$po_id_array, $empty_arr);  
		unset($po_id_array);
		unset($challan_arr);
		fnc_tempengine("gbl_temp_engine", $user_id, 137, 6,$delivery_mst_id_array, $empty_arr);  
		oci_commit($con);  
		unset($delivery_mst_id_array);

		// ============================================================================================================
		//												 Printing Production [Bundle]
		// ============================================================================================================$color_size_id_cond = '';
		if (count($color_size_id_array)) 
		{ 
			$color_size_id_cond = where_con_using_array($color_size_id_array,0,'b.wo_break_id'); 
			$cond_sql2 .= where_con_using_array($color_size_id_array,0,'b.wo_break_id');
		}	
		$print_prod_sql = "select d.delivery_mst_id as sys_id,b.quantity from printing_bundle_issue_mst a,printing_bundle_issue_dtls b,subcon_ord_dtls c,pro_garments_production_mst d,gbl_temp_engine tmp where a.id=b.mst_id and c.id=b.wo_dtls_id and b.bundle_mst_id=d.id and d.delivery_mst_id=tmp.ref_val and tmp.entry_form=137 and tmp.ref_from=6 and tmp.user_id=$user_id $color_size_id_cond and a.entry_form=497 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 order by a.id"; 
		// echo $main_sql; die;

		$print_prod_sql_res = sql_select($print_prod_sql);   
		foreach ($print_prod_sql_res as  $v) 
		{ 
				$delivery_data_array[$v['SYS_ID']]['PRINT_PROD'] 	 += $v['QUANTITY']; 
			 
		}
		unset($print_prod_sql_res);
		// echo count($delivery_data_array); die;
		//========================================================================================================
		//												CUTTING AND RCVE FROM PRINT
		// ========================================================================================================
		$cutting_sql = "select a.serving_company,a.location, a.po_break_down_id as po_id,a.production_type,b.barcode_no from pro_garments_production_mst a,pro_garments_production_dtls b,gbl_temp_engine tmp where a.id=b.mst_id and a.po_break_down_id=tmp.ref_val and a.production_type=1 and a.status_active=1 and a.is_deleted=0 and tmp.entry_form=137 and tmp.ref_from=5 and tmp.user_id=$user_id";
		
		// echo $cutting_sql; die;
		$cutting_sql_res = sql_select($cutting_sql);
		$po_wise_cut_arr = $barcode_rcv_arr = array();
		foreach ($cutting_sql_res as $key => $v) 
		{
			$barcode_array2 [$v['BARCODE_NO']] = $v['BARCODE_NO'];
			if ($barcode_array [$v['BARCODE_NO']]) 
			{ 
				$barcode_wise_cut_arr[$v['BARCODE_NO']]['CUT_COMPANY']  = $v['SERVING_COMPANY']; 
				$barcode_wise_cut_arr[$v['BARCODE_NO']]['CUT_LOCATION'] = $v['LOCATION'];  
			} 
		} 
		unset($cutting_sql_res);
		unset($barcode_array);
		
		//========================================================================================================
		//									CALLAN WISE CUTTING	COMPANY TAGED
		// ========================================================================================================
		// 
		foreach ($barcode_arr as $sys_id => $sys_arr) 
		{
			 foreach ($sys_arr as  $barcode) 
			 {
				$company_id  = $barcode_wise_cut_arr[$barcode]['CUT_COMPANY'];
				$location_id = $barcode_wise_cut_arr[$barcode]['CUT_LOCATION'];

				$sys_wise_cut_company[$sys_id]['CUT_COMPANY']   = $company_id;
				$sys_wise_cut_company[$sys_id]['CUT_LOCATION']  = $location_id;
			 }
		}
		
		unset($barcode_wise_cut_arr);
		unset($barcode_rcv_arr);  

		//========================================================================================================
		//											DATA MAKING 
		// ========================================================================================================
		$main_data_array = array(); 
		// RECEIVED DATA
		foreach ($received_data_array as $sys_id => $v) 
		{ 
			$company_id  = $sys_wise_cut_company[$sys_id]['CUT_COMPANY'];
			$location_id = $sys_wise_cut_company[$sys_id]['CUT_LOCATION']; 
			$main_data_array[$company_id][$location_id]['RCV_CHALLAN_QTY']   += $v['PROD_QTY'];  
			$main_data_array[$company_id][$location_id]['RCVE_SYS_ID']    	 .= $sys_id.',';  
			$main_data_array[$company_id][$location_id]['REV_CHALLAN_NO']    .= ($v['CHALLAN_NO'].'='. $v['PROD_QTY'].',');  
			$main_data_array[$company_id][$location_id]['NO_OF_RCV_CHALLAN'] ++; 
		} 
		
		// DELIVERY DATA
		foreach ($delivery_data_array as $sys_id => $v) 
		{
			$company_id  = $sys_wise_cut_company[$sys_id]['CUT_COMPANY'];
			$location_id = $sys_wise_cut_company[$sys_id]['CUT_LOCATION'];  

			$main_data_array[$company_id][$location_id]['DEL_QC_QTY'] 		+= $v['DEL_QC_QTY']; 
			$main_data_array[$company_id][$location_id]['PRINT_PROD'] 		+= $v['PRINT_PROD']; 
			$main_data_array[$company_id][$location_id]['DEL_CHALLAN_NO']   .= ($v['CHALLAN_NO'].'='. $v['DEL_QC_QTY'].',');   
			$main_data_array[$company_id][$location_id]['DEL_SYS_ID']    	 .= $sys_id.','; 
			$main_data_array[$company_id][$location_id]['NO_OF_DEL_CHALLAN'] ++; 
		} 

		// ============================================================================================================
		//												CLEAR TEMP ENGINE
		// ============================================================================================================
		$con = connect();
		execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form = 137 and ref_from in (5,6)");
		oci_commit($con);  
		disconnect($con);
 
		ob_start();
		$width = 750;
		?> 
			<style>
				.tableFixHead { max-height: 400px !important; overflow: auto; margin: 20px 0;}
				.tableFixHead thead th { position: sticky; top: -2px; z-index: 1;}
				.success {color:#42ba96;}
				.danger {color:#FF0000;}
			</style>
			<fieldset>   
				<div align="center" style="height:auto; width:<? echo $width+20;?>px; margin:0 auto; padding:10px 0 10px 0;"> 
					<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $width;?>" rules="all" id="rpt_table_header" align="left">
						<thead class="form_caption" >	  
							<tr>
								<th width="30">Sl.</th>
								<th width="120">Cutting Company </th>
								<th width="120">Cutting Location </th>
								<th width="100">Rcv Challan Qty </th>
								<th width="80">No of RCV challan </th> 
								<th width="100">Delivery Qty </th> 
								<th width="100">Reject Qty </th> 
								<th width="100">Number Of Delivery Challan </th> 
							</tr>
						</thead>
					</table>
					<div style="width:<?= $width+20;?>px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
						<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body" width="<?= $width; ?>" rules="all" align="left">
							<tbody>
								<?
								$i = $ttl_rcv_challan_qty = $ttl_no_of_rcv_challan = $ttl_del_qc_qty = $ttl_del_reject_qty = $ttl_no_of_del_challan = 0;  
								foreach ($main_data_array as $company_id => $location_data_arr) 
								{  
									foreach ($location_data_arr as $location_id => $v) 
									{ 
										$rcv_challan_qty 	= $v['RCV_CHALLAN_QTY'] ;
										$no_of_rcv_challan 	= $v['NO_OF_RCV_CHALLAN'];
										$del_qc_qty 		= $v['DEL_QC_QTY'];
										$PRINT_PROD 		= $v['PRINT_PROD'];
										$del_reject_qty 	= $v['PRINT_PROD'] - $v['DEL_QC_QTY'];
										$no_of_del_challan 	= $v['NO_OF_DEL_CHALLAN'];
										$print_rcve_sys_id	= trim($v['RCVE_SYS_ID'] ,',');
										$print_delv_sys_id	= trim($v['DEL_SYS_ID'] ,',');

										$ttl_rcv_challan_qty 	+= $rcv_challan_qty;
										$ttl_no_of_rcv_challan 	+= $no_of_rcv_challan;
										$ttl_del_qc_qty 		+= $del_qc_qty;
										$ttl_del_reject_qty 	+= $del_reject_qty;
										$ttl_no_of_del_challan 	+= $no_of_del_challan;


										if ($i % 2 == 0)  $bgcolor = "#E9F3FF";  else $bgcolor = "#FFFFFF";
										?>
											<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
												<td width="30" align="center" > <?= ++$i; ?> </td>
												<td width="120" align="center"> <p> <?=  $company_library[$company_id];?> </p> </td>
												<td width="120" align="center"> <p> <?=  $locationArray[$location_id]; ?> </p> </td>
												<td width="100" align="right" > <a href='#'  onclick="details_data_popup(1,'<?= $print_rcve_sys_id ?>')">  <?= $rcv_challan_qty ?> </a> </td>
												<td width="80"  align="right" title="<?= trim($v['REV_CHALLAN_NO'],',') ?>" > <?= $no_of_rcv_challan ?> </td>
												<td width="100" align="right" > <a href='#'  onclick="details_data_popup(2,'<?= $print_delv_sys_id ?>')"> <?= $del_qc_qty ?> </a> </td> 
												<td width="100" align="right" > <?= $del_reject_qty ?> </td> 
												<td width="100" align="right"  title="<?= trim($v['DEL_CHALLAN_NO'],',') ?>"> <?= $no_of_del_challan ?> </td> 
											</tr> 
										<?
									}
								}
								?>
							</tbody> 
						</table> 
					</div>
					<div style="width:<?= $width+20;?>px;float:left;">
						<table style="float:left;" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" width="<?= $width;?>">
							<tfoot>
								<tr>
									<th width="30"></th>
									<th width="120"></th>
									<th width="120"> G-Total= </th>
									<th width="100" align="right"> <?= $ttl_rcv_challan_qty ?> </th> 
									<th width="80"  align="right"> <?= $ttl_no_of_rcv_challan ?>  </th>
									<th width="100" align="right"> <?= $ttl_del_qc_qty ?> </th>
									<th width="100" align="right"> <?= $ttl_del_reject_qty ?> </th>
									<th width="100" align="right"> <?= $ttl_no_of_del_challan ?> </th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div> 
			</fieldset>   
	    <?  
		unset($main_data_array); 
		unset($company_library);
		unset($buyer_library);
		unset($body_part_library);
		unset($color_library);
		unset($locationArray);
		unset($user_arr); 
	}   
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename,'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data####$filename####$report_type";
	exit();	    
}

if ($action=='details_popup') 
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode); 
	extract($_REQUEST); 
  	if($type==1)  //PRINTING MATERIAL RECEIVE DETAILS ###  GBL REF_FROM (7,8)  
	{ 
		// ============================================================================================================
		//											LIBRARY ARRAY
		// ============================================================================================================ 
		$company_library 	= return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
		$buyer_library		= return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  ); 
		$color_library		= return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
		$body_part_library	= return_library_array( "select id,bundle_use_for from ppl_bundle_title ", "id", "bundle_use_for"  );
		$locationArray		= return_library_array( "select id,location_name from lib_location ", "id", "location_name"  );
		$user_arr 			= return_library_array( "select id, user_name from user_passwd where status_active=1 and is_deleted=0", "id", "user_name"  );
			

		
		// ============================================================================================================
		//												CONDITIONS 
		// ============================================================================================================
		$cond_sql ="";
		$cond_sql .= $company_id    ? " and a.serving_company =$company_id " :""; 
		$cond_sql .= $buyer_id    	? " and e.buyer_name=$buyer_id" :"";   
		$cond_sql .= $location_id 	? " and a.location=$location_id " :""; 
		$cond_sql .= $style_no 		? " and e.style_ref_no like '%$style_no%' " :""; 
		$cond_sql .= $color_id		? " and f.color_number_id in($color_id)" :"";
		$cond_sql .= $challan_no	? " and c.sys_number like '%$challan_no%' " :"";
		$cond_sql .= $delivery_ids	? " and c.id in ($delivery_ids) " :"";
		$cond_sql .= ($form_date && $to_date) ?" and a.production_date between '".change_date_format($form_date,'dd-mm-yyyy','-',1)."' and '".change_date_format($to_date,'dd-mm-yyyy','-',1)."'"  : "";


		// ============================================================================================================
		//												BUNDLE ISSUED TO PRINT DATA
		// ============================================================================================================	
		$main_sql = "select a.wo_order_no,a.location,a.production_date as prod_date,c.remarks,e.buyer_name,d.po_number,e.style_ref_no as style,f.item_number_id as item,f.color_number_id as color,c.body_part,c.working_company_id,b.production_qnty as prod_qty,c.id as sys_id,c.sys_number,d.id as po_id,e.id as job_id,b.bundle_no,b.barcode_no,a.production_type,b.print_receive_status as mtrl_rcve_status,c.sys_number_prefix_num as challan from pro_garments_production_mst a, pro_garments_production_dtls b,pro_gmts_delivery_mst c,wo_po_break_down d, wo_po_details_master e,wo_po_color_size_breakdown f where a.id=b.mst_id and c.id=a.delivery_mst_id and a.po_break_down_id=d.id and d.job_id=e.id and d.id=f.po_break_down_id and f.id=b.color_size_break_down_id $cond_sql and b.print_receive_status=1 and a.embel_name=1 and a.production_type in(2) and a.wo_order_no IS NOT NULL and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0  and f.status_active=1 and f.is_deleted=0 order by c.id asc"; 
		// echo $main_sql; die;
		$main_sql_res = sql_select($main_sql);  
		if (count($main_sql_res) == 0 ) 
		{
			echo "<h1 style='color:red; font-size: 17px;text-align:center;margin-top:20px;'> ** Data Not Found ** </h1>" ;
			die();
		}
		$main_data_array = $po_id_array = $barcode_arr =$barcode_array = array();
		foreach ($main_sql_res as  $v) 
		{
			$po_id_array	[$v['PO_ID']] 		     = $v['PO_ID'];
			$barcode_array	[$v['BARCODE_NO']] 	 	 = $v['BARCODE_NO'];
			$challan_arr	[$v['CHALLAN']] 		 = $v['CHALLAN'];
			$challan_wise_sys_arr[$v['CHALLAN']]     = $v['SYS_ID'];

			$barcode_arr[$v['SYS_ID']][$v['BARCODE_NO']] 	= $v['BARCODE_NO'];
			$main_data_array[$v['SYS_ID']]['WO_ORDER_NO'] 	= $v['WO_ORDER_NO'];
			$main_data_array[$v['SYS_ID']]['PROD_DATE'] 	= $v['PROD_DATE'];
			$main_data_array[$v['SYS_ID']]['SYS_NUMBER'] 	= $v['SYS_NUMBER'];
			$main_data_array[$v['SYS_ID']]['BUYER_NAME'] 	= $v['BUYER_NAME']; 
			$main_data_array[$v['SYS_ID']]['LOCATION'] 		= $v['LOCATION']; 
			$main_data_array[$v['SYS_ID']]['ITEM'] 			= $v['ITEM'];
			$main_data_array[$v['SYS_ID']]['BODY_PART']		= $v['BODY_PART'];
			$main_data_array[$v['SYS_ID']]['REMARKS']		= $v['REMARKS'];
			$main_data_array[$v['SYS_ID']]['WO_COM_ID']		= $v['WORKING_COMPANY_ID'];
			$main_data_array[$v['SYS_ID']]['PROD_QTY'] 		+= $v['PROD_QTY'];
			$main_data_array[$v['SYS_ID']]['NO_OF_BUNDLE']++;

			if (!$main_data_array[$v['SYS_ID']]['MTRL_RCVE_STATUS']) 
			{
				$main_data_array[$v['SYS_ID']]['MTRL_RCVE_STATUS'] = $v['MTRL_RCVE_STATUS'];
			}

			
			if (!$sys_wise_po_arr[$v['SYS_ID']][$v['PO_ID']]) 
			{
				$main_data_array[$v['SYS_ID']]['PO_NUMBER'] .= $v['PO_NUMBER'].','; 
			}
			if (!$sys_wise_color_arr[$v['SYS_ID']][$v['COLOR']]) 
			{ 
				$main_data_array[$v['SYS_ID']]['COLOR'] .= $color_library[$v['COLOR']].',';
			}

			if (!$sys_wise_job_arr[$v['SYS_ID']][$v['JOB_ID']]) 
			{
				$main_data_array[$v['SYS_ID']]['STYLE'] .= $v['STYLE'].','; 
			}

			$sys_wise_po_arr[$v['SYS_ID']][$v['PO_ID']] 	= $v['PO_ID'];
			$sys_wise_job_arr[$v['SYS_ID']][$v['JOB_ID']] 	= $v['JOB_ID'];
			$sys_wise_color_arr[$v['SYS_ID']][$v['COLOR']] = $v['COLOR']; 
		}
		unset($main_sql_res);
		unset($sys_wise_po_arr);
		unset($sys_wise_job_arr);
		unset($sys_wise_color_arr);
		// pre($challan_wise_sys_arr);die;
		
		
		//=========================================================================================================
		//												CLEAR TEMP ENGINE
		// ==========================================================================================================
		$con = connect();
		execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form = 137 and ref_from in(7,8)");
		oci_commit($con);   
		// =========================================================================================================
		//												INSERT DATA INTO TEMP ENGINE
		// =========================================================================================================
		fnc_tempengine("gbl_temp_engine", $user_id, 137, 7,$po_id_array, $empty_arr); 
		fnc_tempengine("gbl_temp_engine", $user_id, 137, 8,$challan_arr, $empty_arr);
		unset($po_id_array);
		unset($challan_arr);
		
		//========================================================================================================
		//												CUTTING AND RCVE FROM PRINT
		// ========================================================================================================
		$cutting_sql = "select a.serving_company,a.location, a.po_break_down_id as po_id,a.production_type,b.barcode_no from pro_garments_production_mst a,pro_garments_production_dtls b,gbl_temp_engine tmp where a.id=b.mst_id and a.po_break_down_id=tmp.ref_val and a.production_type in (1,3) and a.embel_name in (0,1) and a.status_active=1 and a.is_deleted=0 and tmp.entry_form=137 and tmp.ref_from=7 and tmp.user_id=$user_id";
		
		// echo $cutting_sql; die;
		$cutting_sql_res = sql_select($cutting_sql);
		$po_wise_cut_arr = $barcode_rcv_arr = array();
		foreach ($cutting_sql_res as $key => $v) 
		{
			$barcode_array2 [$v['BARCODE_NO']] = $v['BARCODE_NO'];
			if ($barcode_array [$v['BARCODE_NO']]) 
			{
				if ( $v['PRODUCTION_TYPE'] == 1) 
				{
					$barcode_wise_cut_arr[$v['BARCODE_NO']]['CUT_COMPANY']  = $v['SERVING_COMPANY']; 
					$barcode_wise_cut_arr[$v['BARCODE_NO']]['CUT_LOCATION'] = $v['LOCATION']; 
				}
				else{
					$barcode_rcv_arr[$v['BARCODE_NO']] = $v['BARCODE_NO'];
				} 
			} 
		} 
		unset($cutting_sql_res);
		unset($barcode_array);
		
		// BUNDLE RECEIVE FROM PRINT CHECK
		foreach ($barcode_arr as $sys_id => $sys_arr) 
		{
			foreach ($sys_arr as  $barcode) 
			{
			$company_id  = $barcode_wise_cut_arr[$barcode]['CUT_COMPANY'];
			$location_id = $barcode_wise_cut_arr[$barcode]['CUT_LOCATION'];

			$main_data_array[$sys_id]['CUT_COMPANY'][$company_id]    = $company_library[$company_id];
			$main_data_array[$sys_id]['CUT_LOCATION'][$location_id]  = $locationArray[$location_id];

			if ($barcode_rcv_arr[$barcode]) 
			{
				$main_data_array[$sys_id]['RCVE_FROM_PRINT'] = 1; 
			}
			}
		}
		unset($barcode_wise_cut_arr);
		unset($barcode_rcv_arr);
		
		//1 pre($main_data_array);die;
		// ============================================================================================================
		//												Printing Delivery Entry [Bundle]
		// ============================================================================================================	
		
		$printing_sql = "SELECT a.inserted_by,b.challan_no  from printing_bundle_receive_mst a, printing_bundle_receive_dtls b,gbl_temp_engine tmp where a.id=b.mst_id and a.entry_form=614 and b.challan_no=tmp.ref_val and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and tmp.entry_form=137 and tmp.ref_from=8 and tmp.user_id=$user_id";
		// echo $printing_sql ; die; 
		$printing_sql_res = sql_select($printing_sql);
		foreach ($printing_sql_res as  $v) 
		{
			$sys_id = $challan_wise_sys_arr[$v['CHALLAN_NO']];
			if ($sys_id) 
			{
				$main_data_array[$sys_id]['INSERTED_BY'] = $user_arr[$v['INSERTED_BY']];
					
			}
		}
		unset($printing_sql_res);
		unset($challan_wise_sys_arr);
		
		// ============================================================================================================
		//												CLEAR TEMP ENGINE
		// ============================================================================================================
		$con = connect();
		execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form = 137 and ref_from in (7,8)");
		oci_commit($con);  
		disconnect($con);

		// Print Button
		$print_report_format=return_field_value("format_id","lib_report_template","template_name =".$company_id." and module_id=7 and report_id =50 and is_deleted=0 and status_active=1");
		$format_ids=explode(",",$print_report_format);
		// pre($format_ids); die;

		if ($format_ids[0]==84) 	$type=1; // Print 2
		elseif($format_ids[0]==85)  $type=2; // Print 3
		elseif($format_ids[0]==86) 	$type=3; // Print
		elseif($format_ids[0]==89) 	$type=4; // Print4
		elseif($format_ids[0]==129) $type=5; // Print 5
		elseif($format_ids[0]==161) $type=6; // Print 6
		elseif($format_ids[0]==191) $type=7; // Print 7
		elseif($format_ids[0]==220) $type=8; // Print 8
		elseif($format_ids[0]==235) $type=9; // Print 9
		// pre($main_data_array); die;
		ob_start();
		$width = 1790;
		?> 
		<style>
			.tableFixHead { max-height: 400px !important; overflow: auto; margin: 20px 0;}
			.tableFixHead thead th { position: sticky; top: -2px; z-index: 1;}
			.success {color:#42ba96;}
			.danger {color:#FF0000;}
		</style>
		<fieldset> 
			<div width="100%" id="report_container3"> 
				<table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="tbl_caption" align="center">
					<thead class="form_caption" > 
						<tr>
							<td colspan="24" align="center" style="font-size:14px; font-weight:bold" > <h3> Receive Challan Details </h3></td>
						</tr>  
					</thead>
				</table>	
				<div align="center" style="height:auto; width:<? echo $width+20;?>px; margin:0 auto; padding:0;">  
					<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $width;?>" rules="all" id="rpt_table_header" align="left">
						<thead class="form_caption" >	  
							<tr>
								<th width="30">Sl.</th>
								<th width="120">Cutting Company </th>
								<th width="120">Cutting Location </th>
								<th width="120">Emb Location </th>
								<th width="100">Work Order </th>
								<th width="80">Challan Issue Date </th> 
								<th width="100">Challan No </th>
								<th width="80">Buyer </th>
								<th width="100">Buyer PO </th>
								<th width="120">Style </th>
								<th width="80">Item </th>
								<th width="80">Body Color </th>
								<th width="80">Body Part </th> 
								<th width="100">Mtrl Recv Challan From Cutting </th>
								<th width="100">Bundle challn Rcv from Print </th>
								<th width="80">Bundle qty </th>
								<th width="80">Challan Qty </th> 
								<th width="120">Remarks </th> 
								<th width="100">Insert User </th> 
							</tr>
						</thead>
					</table>
					<div style="width:<?= $width+20;?>px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
						<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body" width="<?= $width; ?>" rules="all" align="left">
							<tbody>
								<?
								$i = 0 ; 
								

								foreach ($main_data_array as $sys_id => $v) 
								{ 
									$cut_company = implode(',',$v['CUT_COMPANY']) ;
									$cut_location = implode(',',$v['CUT_LOCATION']) ;
									
									if ($i % 2 == 0)  $bgcolor = "#E9F3FF";  else $bgcolor = "#FFFFFF";
									?>
										<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
											<td width="30"> <?= ++$i; ?> </td>
											<td width="120"> <p> <?=  $cut_company;?> </p> </td>
											<td width="120"> <p> <?=  $cut_location; ?> </p> </td>
											<td width="120"> <p> <?=  $locationArray[$v['LOCATION']]; ?> </p> </td>
											<td width="100" > <p> <?= $v['WO_ORDER_NO'] ?> </p> </td>
											<td width="80"> <p> <?= $v['PROD_DATE'] ?> </p> </td>
											<td width="100"> <a href='#'  onclick="parent.window.fnc_issue_print_embroidery(<?= $type.','. $sys_id.','. $v['WO_COM_ID'].','.$v['BODY_PART'].',1'?>)"> <?= $v['SYS_NUMBER'] ?> </a> </td> 
											<td width="80"> <p> <?= $buyer_library[$v['BUYER_NAME']] ?> </p> </td>
											<td width="100"> <p> <?= trim($v['PO_NUMBER'],',') ?> </p> </td>  
											<td width="120"> <p> <?= trim($v['STYLE'],',') ?> </p> </td>  
											<td width="80"> <p> <?= $garments_item[$v['ITEM']] ?> </p> </td> 
											<td width="80"> <p> <?= trim($v['COLOR'],',') ?> </p> </td> 
											<td width="80"> <p> <?= $body_part_library[$v['BODY_PART']] ?> </p> </td>   
											<td width="100" align="center"> <p> <?= $v['MTRL_RCVE_STATUS'] ? "YES" : "NO" ?> </p> </td>
											<td width="100" align="center"> <p> <?= $v['RCVE_FROM_PRINT'] ? "YES" : "NO" ?> </p> </td>
											<td width="80" align="right"> <p> <?= $v['NO_OF_BUNDLE'] ?> </p> </td>
											<td width="80" align="right"> <p> <?= $v['PROD_QTY'] ?> </p> </td>   
											<td width="120" > <p> <?= $v['REMARKS'] ?> </p> </td>
											<td width="100" > <p> <?= $v['INSERTED_BY'] ?> </p> </td>   
										</tr> 
									<? 
									$total_issue_qty += $v['PROD_QTY'];
								}
								?>
							</tbody> 
						</table> 
					</div>
					<div style="width:<?= $width+20;?>px;float:left;">
						<table style="float:left;" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" width="<?= $width;?>">
							<tfoot>
								<tr>
									<th width="30"></th>
									<th width="120"></th>
									<th width="120"></th>
									<th width="120"></th>
									<th width="100"></th>
									<th width="80">Challan Qty</th>
									<th width="100" id="row_count"><?=$i ?></th>
									<th width="80"></th>
									<th width="100"></th>
									<th width="120"></th>
									<th width="80"></th>
									<th width="80"></th>
									<th width="80"></th>
									<th width="100"></th>
									<th width="100"></th>
									<th width="80">Total Qty</th>
									<th width="80" id="total_prod_qty" align="right"><?= $total_issue_qty ?> </th> 
									<th width="120"></th>
									<th width="100"></th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</fieldset>
		<script>
			let tableFilters1 =
			{ 
				col_operation: {
					id: ["total_prod_qty"],
					col: [16],
					operation: ["sum"],
					write_method: ["innerHTML"]
				}
			} 
			setFilterGrid("table_body",-1,tableFilters1); 
		</script>
		<?  
		unset($main_data_array); 
		unset($company_library);
		unset($buyer_library);
		unset($body_part_library);
		unset($color_library);
		unset($locationArray);
		unset($user_arr);  
	}   
	else if($type==2) //PRINTING DELIVERY DETAILS   ###  GBL REF_FROM (9,10)
	{ 
		// ============================================================================================================
		//											LIBRARY ARRAY
		// ============================================================================================================ 
		$company_library 	= return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
		$buyer_library		= return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  ); 
		$color_library		= return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
		$body_part_library	= return_library_array( "select id,bundle_use_for from ppl_bundle_title ", "id", "bundle_use_for"  );
		$locationArray		= return_library_array( "select id,location_name from lib_location ", "id", "location_name"  );
		$user_arr 			= return_library_array( "select id, user_name from user_passwd where status_active=1 and is_deleted=0", "id", "user_name"  );

		
		// ============================================================================================================
		//												CONDITIONS 
		// ============================================================================================================
		$cond_sql ="";
		$cond_sql .= $company_id    ? " and a.company_id =$company_id " :""; 
		$cond_sql .= $challan_no	? " and a.issue_number like '%$challan_no%' " :"";
		$cond_sql .= $buyer_id    	? " and c.buyer_buyer='$buyer_id'" :"";   
		$cond_sql .= $style_no 		? " and c.style_ref_no like '%$style_no%' " :""; 
		$cond_sql .= $color_id		? " and e.color_number_id in($color_id)" :"";
		$cond_sql .= $location_id 	? " and f.location=$location_id " :""; 
		$cond_sql .= $delivery_ids	? " and f.delivery_mst_id in ($delivery_ids) " :"";
		$cond_sql .= ($form_date && $to_date) ?" and a.issue_date between '".change_date_format($form_date,'dd-mm-yyyy','-',1)."' and '".change_date_format($to_date,'dd-mm-yyyy','-',1)."'"  : "";


		// ============================================================================================================
		//												Printing Delivery Entry [Bundle]
		// ============================================================================================================	
		$main_sql = "SELECT b.id,a.id as issue_id,a.entry_form,a.issue_date as delivery_date,a.issue_number as del_challan,a.company_id,a.remarks,a.inserted_by,b.quantity as del_qc_qty,b.wo_break_id,f.delivery_mst_id,f.location from printing_bundle_issue_mst a,printing_bundle_issue_dtls b,wo_po_details_master c,wo_po_break_down d,wo_po_color_size_breakdown e,pro_garments_production_mst f where a.id=b.mst_id and c.id=d.job_id and e.po_break_down_id=d.id and e.id = b.wo_break_id and b.bundle_mst_id=f.id and a.entry_form=499 $cond_sql and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and e.status_active=1 and e.is_deleted=0  and f.status_active=1 and f.is_deleted=0 order by a.id"; 
		// echo $main_sql; die;

		$main_sql_res = sql_select($main_sql);  
		if (count($main_sql_res) == 0 ) 
		{
			echo "<h1 style='color:red; font-size: 17px;text-align:center;margin-top:20px;'> ** Data Not Found ** </h1>" ;
			die();
		}
		$main_data_array = $delivery_mst_id_array = $color_size_id_array = array();
		foreach ($main_sql_res as  $v) 
		{ 
			$main_data_array[$v['DELIVERY_MST_ID']]['DEL_QC_QTY'] 	 += $v['DEL_QC_QTY'];
			$main_data_array[$v['DELIVERY_MST_ID']]['DELIVERY_DATE'] = $v['DELIVERY_DATE'];
			$main_data_array[$v['DELIVERY_MST_ID']]['DEL_CHALLAN'] 	 = $v['DEL_CHALLAN'];
			$main_data_array[$v['DELIVERY_MST_ID']]['ISSUE_ID'] 	 = $v['ISSUE_ID'];
			$main_data_array[$v['DELIVERY_MST_ID']]['LOCATION'] 	 = $v['LOCATION'];
			$main_data_array[$v['DELIVERY_MST_ID']]['COMPANY_ID'] 	 = $v['COMPANY_ID'];
			$main_data_array[$v['DELIVERY_MST_ID']]['INSERTED_BY'] 	 = $user_arr[$v['INSERTED_BY']];
			$main_data_array[$v['DELIVERY_MST_ID']]['REMARKS'] 	 	 = $v['REMARKS'];

			$delivery_mst_id_array[$v['DELIVERY_MST_ID']] = $v['DELIVERY_MST_ID'];
			$color_size_id_array[$v['WO_BREAK_ID']] = $v['WO_BREAK_ID'];
				
		}
		unset($main_sql_res);
		// pre($color_size_id_array); die;
		
		// =====================================================================================================
		//												CLEAR TEMP ENGINE
		// =====================================================================================================
		$con = connect();
		execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form = 137 and ref_from in(9,10)");
		oci_commit($con);  

		// =====================================================================================================
		//												INSERT DELIVERY_MST_ID INTO TEMP ENGINE
		// =====================================================================================================
		fnc_tempengine("gbl_temp_engine", $user_id, 137, 9,$delivery_mst_id_array, $empty_arr);  
		oci_commit($con);  
		unset($delivery_mst_id_array);

		// ============================================================================================================
		//												 Printing Production [Bundle]
		// ============================================================================================================	
		$color_size_id_cond = $color_size_id_cond2 = '';
		if (count($color_size_id_array)) 
		{
			$color_size_id_cond = where_con_using_array($color_size_id_array,0,'b.wo_break_id');
			$color_size_id_cond2 = where_con_using_array($color_size_id_array,0,'f.id');
		}
		$print_prod_sql = "select d.delivery_mst_id,b.quantity from printing_bundle_issue_mst a,printing_bundle_issue_dtls b,subcon_ord_dtls c,pro_garments_production_mst d,gbl_temp_engine tmp where a.id=b.mst_id and c.id=b.wo_dtls_id and b.bundle_mst_id=d.id and d.delivery_mst_id=tmp.ref_val and tmp.entry_form=137 and tmp.ref_from=9 and tmp.user_id=$user_id  and a.entry_form=497 $color_size_id_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 order by a.id"; 
		// echo $print_prod_sql; die;

		$print_prod_sql_res = sql_select($print_prod_sql);   
		foreach ($print_prod_sql_res as  $v) 
		{ 
			$main_data_array[$v['DELIVERY_MST_ID']]['PRINT_PROD'] 	 += $v['QUANTITY']; 	
		}
		unset($print_prod_sql_res); 
		// pre($main_data_array);die;
		// ============================================================================================================
		//												BUNDLE ISSUED TO PRINT DATA
		// ============================================================================================================	
		$issue_sql = "select a.wo_order_no,a.production_date as prod_date,e.buyer_name,d.po_number,e.style_ref_no as style,f.item_number_id as item,f.color_number_id as color,c.body_part,c.working_company_id,b.production_qnty as prod_qty,c.id as sys_id,c.sys_number,d.id as po_id,e.id as job_id,b.bundle_no,b.barcode_no,a.production_type,b.print_receive_status as mtrl_rcve_status from pro_garments_production_mst a, pro_garments_production_dtls b,pro_gmts_delivery_mst c,wo_po_break_down d, wo_po_details_master e,wo_po_color_size_breakdown f,gbl_temp_engine tmp where a.id=b.mst_id and c.id=a.delivery_mst_id and a.po_break_down_id=d.id and d.job_id=e.id and d.id=f.po_break_down_id and f.id=b.color_size_break_down_id and c.id=tmp.ref_val and a.embel_name=1 and a.production_type in(2) and a.wo_order_no IS NOT NULL and tmp.entry_form=137 and tmp.ref_from=9 and tmp.user_id=$user_id $color_size_id_cond2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0  and f.status_active=1 and f.is_deleted=0"; 
		// echo $issue_sql;die;
			
		$po_id_array = $barcode_arr = $barcode_array = array();
		$issue_sql_res = sql_select($issue_sql);  
		foreach ($issue_sql_res as  $v) 
		{
			$po_id_array[$v['PO_ID']] 		  	 = $v['PO_ID'];
			$barcode_array	[$v['BARCODE_NO']] 	 = $v['BARCODE_NO'];
			$barcode_arr[$v['SYS_ID']][$v['BARCODE_NO']] 	= $v['BARCODE_NO'];
			// echo $v['BARCODE_NO']; die;
			$main_data_array[$v['SYS_ID']]['WO_ORDER_NO'] 	= $v['WO_ORDER_NO'];
			$main_data_array[$v['SYS_ID']]['PROD_DATE'] 	= $v['PROD_DATE'];
			$main_data_array[$v['SYS_ID']]['SYS_NUMBER'] 	= $v['SYS_NUMBER'];
			$main_data_array[$v['SYS_ID']]['BUYER_NAME'] 	= $v['BUYER_NAME']; 
			$main_data_array[$v['SYS_ID']]['ITEM'] 			= $v['ITEM'];
			$main_data_array[$v['SYS_ID']]['BODY_PART']		= $v['BODY_PART'];
			$main_data_array[$v['SYS_ID']]['WO_COM_ID']		= $v['WORKING_COMPANY_ID'];
			$main_data_array[$v['SYS_ID']]['PROD_QTY'] 		+= $v['PROD_QTY'];
			$main_data_array[$v['SYS_ID']]['NO_OF_BUNDLE']++;

			if (!$main_data_array[$v['SYS_ID']]['MTRL_RCVE_STATUS']) 
			{
				$main_data_array[$v['SYS_ID']]['MTRL_RCVE_STATUS'] = $v['MTRL_RCVE_STATUS'];
			}

			
			if (!$sys_wise_po_arr[$v['SYS_ID']][$v['PO_ID']]) 
			{
				$main_data_array[$v['SYS_ID']]['PO_NUMBER'] .= $v['PO_NUMBER'].','; 
			}
			if (!$sys_wise_color_arr[$v['SYS_ID']][$v['COLOR']]) 
			{ 
				$main_data_array[$v['SYS_ID']]['COLOR'] .= $color_library[$v['COLOR']].',';
			}

			if (!$sys_wise_job_arr[$v['SYS_ID']][$v['JOB_ID']]) 
			{
				$main_data_array[$v['SYS_ID']]['STYLE'] .= $v['STYLE'].','; 
			}

			$sys_wise_po_arr[$v['SYS_ID']][$v['PO_ID']] 	= $v['PO_ID'];
			$sys_wise_job_arr[$v['SYS_ID']][$v['JOB_ID']] 	= $v['JOB_ID'];
			$sys_wise_color_arr[$v['SYS_ID']][$v['COLOR']] = $v['COLOR'];
			// $barcode_wise_sys_array[$v['BARCODE_NO']]= $v['SYS_ID'];
		}
		unset($issue_sql_res);
		// pre($sys_wise_color_arr);die;
		
			
		// =========================================================================================================
		//												INSERT PO ID INTO TEMP ENGINE
		// =========================================================================================================
		fnc_tempengine("gbl_temp_engine", $user_id, 137, 10,$po_id_array, $empty_arr);  
		
		//========================================================================================================
		//												CUTTING AND RCVE FROM PRINT
		// ========================================================================================================
		$cutting_sql = "select a.serving_company, a.location, a.po_break_down_id as po_id,a.production_type,b.barcode_no from pro_garments_production_mst a,pro_garments_production_dtls b,gbl_temp_engine tmp where a.id=b.mst_id and a.po_break_down_id=tmp.ref_val and a.production_type in (1,3) and a.embel_name in (0,1) and a.status_active=1 and a.is_deleted=0 and tmp.entry_form=137 and tmp.ref_from=10 and tmp.user_id=$user_id";
		// echo $cutting_sql; die;
		$cutting_sql_res = sql_select($cutting_sql);
		$po_wise_cut_arr = $barcode_rcv_arr = array();
		foreach ($cutting_sql_res as $key => $v) 
		{
			if ($barcode_array[$v['BARCODE_NO']]) 
			{
				if ( $v['PRODUCTION_TYPE'] == 1) 
				{
					$barcode_wise_cut_arr[$v['BARCODE_NO']]['CUT_COMPANY']  = $v['SERVING_COMPANY']; 
					$barcode_wise_cut_arr[$v['BARCODE_NO']]['CUT_LOCATION'] = $v['LOCATION']; 
				}
				else{
					$barcode_rcv_arr[$v['BARCODE_NO']] = $v['BARCODE_NO'];
				} 
			} 
		} 
		unset($cutting_sql_res);

		foreach ($barcode_arr as $sys_id => $sys_arr) 
		{
			foreach ($sys_arr as  $barcode) 
			{
			$company_id  = $barcode_wise_cut_arr[$barcode]['CUT_COMPANY'];
			$location_id = $barcode_wise_cut_arr[$barcode]['CUT_LOCATION'];

			$main_data_array[$sys_id]['CUT_COMPANY'][$company_id]    = $company_library[$company_id];
			$main_data_array[$sys_id]['CUT_LOCATION'][$location_id]  = $locationArray[$location_id];

			if ($barcode_rcv_arr[$v['BARCODE_NO']]) 
			{
				$main_data_array[$sys_id]['RCVE_FROM_PRINT'] = 1; 
			}
			}
		}
		unset($barcode_wise_cut_arr);
		unset($barcode_rcv_arr);
		unset($barcode_arr);
		// ============================================================================================================
		//												CLEAR TEMP ENGINE
		// ============================================================================================================
		$con = connect();
		execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form = 137 and ref_from in(9,10)");
		oci_commit($con);  
		disconnect($con);

		// Print Button
		$print_report_format=return_field_value("format_id","lib_report_template","template_name =$company_id  and module_id=15 and report_id=276 and is_deleted=0 and status_active=1");
		$format_ids=explode(",",$print_report_format);
		// pre($format_ids); die;

		if($format_ids[0]==86)  	 $type=1; // Print
		else if($format_ids[0]==110) $type=2; // Print 2
		else if($format_ids[0]==85)  $type=3; // Print 3
		ob_start();
		$width = 1750;
		?> 
		<style>
			.tableFixHead { max-height: 400px !important; overflow: auto; margin: 20px 0;}
			.tableFixHead thead th { position: sticky; top: -2px; z-index: 1;}
			.success {color:#42ba96;}
			.danger {color:#FF0000;}
		</style>
		<fieldset> 
			<div width="100%" id="report_container3"> 
				<table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="tbl_caption" align="center">
					<thead class="form_caption" > 
						<tr>
							<td colspan="24" align="center" style="font-size:14px; font-weight:bold" > <h3> Delivery Challan Details </h3> </td>
						</tr>  
					</thead>
				</table>	
				<div align="center" style="height:auto; width:<? echo $width+20;?>px; margin:0 auto; padding:0;">  
					<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $width;?>" rules="all" id="rpt_table_header" align="left">
						<thead class="form_caption" >	  
							<tr>
								<th width="30">Sl.</th>
								<th width="120">Cutting Company </th>
								<th width="120">Cutting Location </th>
								<th width="120">Emb Location </th>
								<th width="100">Work Order </th>
								<th width="80">Challan Delivery Date </th> 
								<th width="100">Issue Challan No </th>
								<th width="120">Delivery Challan No </th>
								<th width="80">Buyer </th>
								<th width="100">Buyer PO </th>
								<th width="120">Style </th>
								<th width="80">Item </th>
								<th width="80">Body Color </th>
								<th width="80">Body Part </th> 
								<th width="80">Print Delivery</th> 
								<th width="100">Bundle challn Rcv from Print </th> 
								<th width="80">Challan Qty </th> 
								<th width="80">Rej. Qty </th> 
								<th width="100">Insert User </th> 
							</tr>
						</thead>
					</table>
					<div style="width:<?= $width+20;?>px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
						<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body" width="<?= $width; ?>" rules="all" align="left">
							<tbody>
								<?
								$i = 0 ; 
								$total_qc_qty = 0 ; 

								foreach ($main_data_array as $sys_id => $v) 
								{  
									$cut_company  = implode(',',$v['CUT_COMPANY']) ;
									$cut_location = implode(',',$v['CUT_LOCATION']) ;
									
									
									$reject_qty 		= $v['PRINT_PROD'] - $v['DEL_QC_QTY'];
									$total_qc_qty 		+= $v['DEL_QC_QTY'];
									$total_reject_qty 	+= $reject_qty;

									if ($i % 2 == 0)  $bgcolor = "#E9F3FF";  else $bgcolor = "#FFFFFF";
									?>
										<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
											<td width="30"> <?= ++$i; ?> </td>
											<td width="120"> <p> <?= trim($cut_company,','); ?> </p> </td>
											<td width="120"> <p> <?= trim($cut_location,','); ?> </p> </td>
											<td width="120"> <p> <?= $locationArray[$v['LOCATION']]; ?> </p> </td>
											<td width="100" > <p> <?= $v['WO_ORDER_NO'] ?> </p> </td>
											<td width="80"> <p> <?= $v['DELIVERY_DATE'] ?> </p> </td>
											<td width="100"> <?= $v['SYS_NUMBER'] ?> </td> 
											<td width="120"> <a href='#'  onclick="parent.window.fnc_embl_delivery(<?= $type.','. $v['COMPANY_ID'].','.$v['ISSUE_ID'].','.$v['LOCATION']?>)"> <?= $v['DEL_CHALLAN'] ?> </a> </td> 
											<td width="80"> <p> <?= $buyer_library[$v['BUYER_NAME']] ?> </p> </td>
											<td width="100"> <p> <?= trim($v['PO_NUMBER'],',') ?> </p> </td>  
											<td width="120"> <p> <?= trim($v['STYLE'],',') ?> </p> </td>  
											<td width="80"> <p> <?= $garments_item[$v['ITEM']] ?> </p> </td> 
											<td width="80"> <p> <?= trim($v['COLOR'],',') ?> </p> </td> 
											<td width="80"> <p> <?= $body_part_library[$v['BODY_PART']] ?> </p> </td>   
											<td width="80" align="center"> <p> <?= $v['DEL_QC_QTY'] ? "YES" : "NO" ?> </p> </td>
											<td width="100" align="center"> <p> <?= $v['RCVE_FROM_PRINT'] ? "YES" : "NO" ?> </p> </td>
											<td width="80" align="right"> <p> <?= $v['DEL_QC_QTY'] ?> </p> </td> 
											<td width="80" align="right"><p> <?= $reject_qty  ?> </p> </td> 
											<td width="100"><p> <?= $v['INSERTED_BY'] ?> </p> </td>    
										</tr> 
									<? 
								}
								?>
							</tbody> 
						</table> 
					</div>
					<div style="width:<?= $width+20;?>px;float:left;">
						<table style="float:left;" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" width="<?= $width;?>">
							<tfoot>
								<tr>
									<th width="30"></th>
									<th width="120"></th>
									<th width="120"></th>
									<th width="120"></th>
									<th width="100"></th>
									<th width="80"></th>
									<th width="100">Challan Qty</th>
									<th width="120" id="row_count" ><?=$i ?></th>
									<th width="80"></th>
									<th width="100"></th>
									<th width="120"></th>
									<th width="80"></th>
									<th width="80"></th>
									<th width="80"></th>
									<th width="80"></th>
									<th width="100">Total Qty</th>
									<th width="80" id="total_prod_qty" align="right"> <?= $total_qc_qty ?></th>
									<th width="80" id="total_reject_qty" align="right"> <?= $total_reject_qty ?></th> 
									<th width="100"></th>  
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</fieldset>
		<script> 
			let tableFilters2 =
			{ 
				col_operation: {
					id: ["total_prod_qty","total_reject_qty"],
					col: [16,17],
					operation: ["sum","sum"],
					write_method: ["innerHTML","innerHTML"]
				}
			}
			setFilterGrid("table_body",-1,tableFilters2); 
		</script>
		<?  
		unset($main_data_array); 
		unset($company_library);
		unset($buyer_library);
		unset($body_part_library);
		unset($color_library);
		unset($locationArray);
		unset($user_arr);  
	}  
	?>
		<div style="display: flex; justify-content: center; margin:10px 0 10px 0">
			<input type="button" onclick="new_window2()" value="Print" name="Print" class="formbutton" style="width:100px">
		</div>
		<script>
			 function new_window2() {
				var filter = 0;
				if ($("#table_body tbody tr:first").attr('class')=='fltrow')
				{
					filter = 1;
					$("#table_body tbody tr:first").hide();
				}
				document.getElementById('scroll_body').style.overflow='auto';
				document.getElementById('scroll_body').style.maxHeight='none'; 
				var w = window.open("Surprise", "#");
				var d = w.document.open(); 
				d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container3').innerHTML+'</body</html>'); 
				d.close();
				document.getElementById('scroll_body').style.overflowY='scroll';
				document.getElementById('scroll_body').style.maxHeight='300px';
				if(filter == 1)
				{
					$("#table_body tbody tr:first").show();
				}
			}
			// Count Visable Row 
			var documentElement = document.documentElement;
			// Add a keypress event listener
			documentElement.addEventListener("keypress", function(event) 
			{
				// Get the event target
				var target = event.target;
				try 
				{
					// Check if the target has the class flt
					if (target.classList.contains("flt"))
					{
						// Check if the key is Enter
						if (event.key === "Enter" )
						{
							var count = $('#table_body tr:not([style*="display: none"])').length;
							$('#row_count').text(count - 1);
						}
					}
				} catch (error) {
					console.log(error)
				}
			});
		</script>
	<?
}  
?>
