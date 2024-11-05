<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
$user_id = $_SESSION['logic_erp']["user_id"];
//---------------------------------------------------- Start------------------------------------

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 $buyer_cond and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	exit();
}

if ($action=="load_drop_down_item")
{
    echo create_drop_down( "cbogmtsitem", 170, $garments_item,"", 0, "","", "","",$data);
	exit();
}

if ($action=="order_popup")
{
  	echo load_html_head_contents("PO Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		function js_set_value( po_id )
		{
			//alert(po_id)
			document.getElementById('po_id').value=po_id;
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="1000" cellspacing="0" cellpadding="0" border="0">
            <tr>
                <td align="center" width="100%">
                    <table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
                        <thead>
                            <tr>
                                <th colspan="7" align="center"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                            </tr>
                            <tr>
                                <th width="150" class="must_entry_caption">Company Name</th>
                                <th width="150">Buyer Name</th>
                                <th width="80">Job No</th>
                                <th width="100">Style Ref</th>
                                <th width="80">Internal Ref</th>
                                <th width="120">Order No</th>
                                <th width="200">Ex-factory Date</th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tr class="general">
                            <td>
                                <input type="hidden" id="selected_job"/> <input type="hidden" id="po_id">
                                <input type="hidden" id="garments_nature" value="<? echo $garments_nature; ?>">
                                <? echo create_drop_down( "cbo_company_mst", 150, "select id, company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name",1, "--Select Company--", '', "load_drop_down( 'buyer_claims_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );"); ?>
                            </td>
                            <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- Select Buyer --" ); ?></td>
                            <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:80px"></td>
                            <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:100px"></td>
                            <td>
                            	<input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:80px">
                            </td>
                            <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:120px"></td>
                            <td>
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:65px">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:65px">
                            </td>
                            <td align="center">
                            	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('garments_nature').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_internal_ref').value, 'create_po_search_list_view', 'search_div', 'buyer_claims_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
                        </tr>
                        <tr>
                            <td align="center" valign="middle" colspan="7"><? echo load_month_buttons(1); ?></td>
                        </tr>
                	</table>
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

if($action=="create_po_search_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and a.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.buyer_name='$data[1]'"; else { $buyer=""; }

	if($db_type==0)
	{
		$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[6]";
		if ($data[2]!="" &&  $data[3]!="") $ex_factory_date = "and c.ex_factory_date between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $ex_factory_date ="";
		$year_val_cond="SUBSTRING_INDEX(a.`insert_date`, '-', 1)";
	}
	else if($db_type==2)
	{
		$year_cond=" and to_char(a.insert_date,'YYYY')=$data[6]";
		if ($data[2]!="" &&  $data[3]!="") $ex_factory_date = "and c.ex_factory_date between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $ex_factory_date ="";
		$year_val_cond="to_char(a.insert_date,'YYYY')";
	}

	$order_cond=""; $job_cond=""; $style_cond=""; $internal_ref_cond="";
	if($data[7]==1)
	{
		if (str_replace("'","",$data[5])!="") $job_cond=" and a.job_no_prefix_num='$data[5]'  $year_cond";
		if (trim($data[8])!="") $order_cond=" and b.po_number='$data[8]'  "; //else  $order_cond="";
		if (trim($data[9])!="") $style_cond=" and a.style_ref_no='$data[9]'  "; //else  $style_cond="";
		if (trim($data[10])!="") $internal_ref_cond=" and b.grouping = '$data[10]' ";
	}
	else if($data[7]==4 || $data[7]==0)
	{
		if (str_replace("'","",$data[5])!="") $job_cond=" and a.job_no_prefix_num like '%$data[5]%'  $year_cond"; //else  $job_cond="";
		if (trim($data[8])!="") $order_cond=" and b.po_number like '%$data[8]%'  ";
		if (trim($data[9])!="") $style_cond=" and a.style_ref_no like '%$data[9]%'  "; //else  $style_cond="";
		if (trim($data[10])!="") $internal_ref_cond=" and b.grouping like '%$data[10]%' ";
	}
	else if($data[7]==2)
	{
		if (str_replace("'","",$data[5])!="") $job_cond=" and a.job_no_prefix_num like '$data[5]%'  $year_cond"; //else  $job_cond="";
		if (trim($data[8])!="") $order_cond=" and b.po_number like '$data[8]%'  ";
		if (trim($data[9])!="") $style_cond=" and a.style_ref_no like '$data[9]%'  "; //else  $style_cond="";
		if (trim($data[10])!="") $internal_ref_cond=" and b.grouping like '$data[10]%' ";
	}
	else if($data[7]==3)
	{
		if (str_replace("'","",$data[5])!="") $job_cond=" and a.job_no_prefix_num like '%$data[5]'  $year_cond"; //else  $job_cond="";
		if (trim($data[8])!="") $order_cond=" and b.po_number like '%$data[8]'  ";
		if (trim($data[9])!="") $style_cond=" and a.style_ref_no like '%$data[9]'  "; //else  $style_cond="";
		if (trim($data[10])!="") $internal_ref_cond=" and b.grouping like '%$data[10]' ";
	}

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');

	$arr=array (2=>$comp,3=>$buyer_arr,9=>$item_category);
	$sql="select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.grouping, a.garments_nature, $year_val_cond as year, a.job_quantity, b.id, b.po_number, b.po_quantity, b.po_total_price, b.plan_cut, b.pub_shipment_date, MAX(c.ex_factory_date) AS ex_factory_date, sum(CASE WHEN c.entry_form!=85 THEN c.ex_factory_qnty ELSE 0 END)-sum(CASE WHEN c.entry_form=85 THEN c.ex_factory_qnty ELSE 0 END) AS ex_factory_qnty from wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.garments_nature=$data[4] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $ex_factory_date $company $buyer $job_cond $order_cond $style_cond $internal_ref_cond
	group by a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.garments_nature, a.insert_date, a.job_quantity, b.id, b.po_number, b.po_quantity,  b.po_total_price, b.plan_cut, b.pub_shipment_date, b.grouping
	order by b.id DESC";
	// echo $sql;die;

	echo create_list_view("list_view", "Job No,Year,Company,Buyer Name,Style Ref. No,Internal Ref No,Job Qty.,PO number,PO Quantity,Shipment Date,Gmts Nature", "90,60,120,100,80,90,90,90,80,80","1000","320",0, $sql , "js_set_value", "id", "", 1, "0,0,company_name,buyer_name,0,0,0,0,0,0,garments_nature", $arr, "job_no_prefix_num,year,company_name,buyer_name,style_ref_no,grouping,job_quantity,po_number,po_quantity,pub_shipment_date,garments_nature", "",'','0,0,0,0,3,0,1,0,1,3,0');

	/*
		$location_lib_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
		 	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
		$arr=array(1=>$location_lib_arr,4=>$supplier_arr,5=>$currency,6=>$source,7=>$item_category,8=>$appr_status);


		function create_list_view($table_id, $tbl_header_arr, $td_width_arr, $tbl_width, $tbl_height, $tbl_border, $query, $onclick_fnc_name, $onclick_fnc_param_db_arr, $onclick_fnc_param_sttc_arr, $show_sl, $field_printed_from_array_arr, $data_array_name_arr, $qry_field_list_array, $controller_file_path, $filter_grid_fnc, $fld_type_arr, $summary_flds, $check_box_all, $new_conn)

		/*echo  create_list_view("list_view", "WO/PI No,Location, LC ,Date, Supplier, Currency, Source,Item Category,Approval Status","50,100,100,100,150,100,120,120,50","1000","260",0, $sql , "js_set_value", "id,wopi_number","", 1, "0,location_id,0,0,supplier_id,currency_id,source,item_category,id", $arr, "wopi_number,location_id,lc_number,wopi_date,supplier_id,currency_id,source,item_category,id", "",'','0,0,0,0,0,0,0,0,0') ;*/
	exit();
}

if ($action=="populate_data_from_search_popup")
{
	/*$data_array=sql_select("select a.garments_nature,a.id as job_id,a.job_no,a.company_name,a.buyer_name,a.location_name,a.style_ref_no,a.style_description,a.product_dept,a.currency_id,a.agent_name,a.order_repeat_no,a.region,team_leader,a.dealing_marchant,a.packing,remarks,a.ship_mode,a.order_uom,a.gmts_item_id,a.set_break_down,a.total_set_qnty,b.id,b.po_number,b.po_quantity,b.plan_cut,b.shipment_date,b.pub_shipment_date,b.packing from wo_po_details_master a, wo_po_break_down b where  a.job_no=b.job_no_mst and  b.id='$data' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1");*/
	
	$sql="select a.id, a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.garments_nature, a.job_quantity, a.team_leader, a.dealing_marchant, a.order_uom, b.id, b.po_number, b.po_quantity, b.unit_price, b.po_total_price, b.plan_cut, b.pub_shipment_date, MAX(c.ex_factory_date) AS ex_factory_date, sum(CASE WHEN c.entry_form!=85 THEN c.ex_factory_qnty ELSE 0 END)-sum(CASE WHEN c.entry_form=85 THEN c.ex_factory_qnty ELSE 0 END) AS ex_factory_qnty 
	
	from wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c where a.job_no=b.job_no_mst and b.id='$data' and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
	group by a.id, a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.garments_nature, a.job_quantity, a.team_leader, a.dealing_marchant, a.order_uom, b.id, b.po_number, b.po_quantity, b.unit_price, b.po_total_price, b.plan_cut, b.pub_shipment_date
	order by b.id DESC";
	//echo $sql;
	$data_array=sql_select($sql);
	foreach ($data_array as $row)
	{
		$po_id=$row[csf("id")];
		$exFactoryValue=$row[csf("ex_factory_qnty")]*$row[csf("unit_price")];
		echo "document.getElementById('garments_nature').value = '".$row[csf("garments_nature")]."';\n";
		echo "document.getElementById('txt_order_no').value = '".$row[csf("po_number")]."';\n";
		echo "document.getElementById('order_id').value = '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_name")]."';\n";
		echo "document.getElementById('cbo_location_name').value = '".$row[csf("location_name")]."';\n";
		echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('hidd_job_id').value = '".$row[csf("job_id")]."';\n";
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_name")]."';\n";
		echo "document.getElementById('txt_style_ref').value = '".$row[csf("style_ref_no")]."';\n";
		echo "document.getElementById('txt_style_description').value = '".$row[csf("style_description")]."';\n";
		echo "document.getElementById('txt_ship_date').value = '".change_date_format($row[csf("pub_shipment_date")],"dd-mm-yyyy","-")."';\n";
		echo "document.getElementById('txt_exfactory_date').value = '".change_date_format($row[csf("ex_factory_date")],"dd-mm-yyyy","-")."';\n";
		echo "document.getElementById('txt_po_qnty').value = '".$row[csf("po_quantity")]."';\n";
		echo "document.getElementById('txt_po_value').value = '".$row[csf("po_total_price")]."';\n";
		echo "document.getElementById('txt_plan_cut_qnty').value = '".$row[csf("plan_cut")]."';\n";
		echo "document.getElementById('txt_exfactory_qty').value = '".$row[csf("ex_factory_qnty")]."';\n";
		echo "document.getElementById('txt_exfactory_val').value = '".$exFactoryValue."';\n";
		echo "document.getElementById('cbo_team_leader').value = '".$row[csf("team_leader")]."';\n";
		echo "document.getElementById('cbo_dealing_merchant').value = '".$row[csf("dealing_marchant")]."';\n";
		echo "document.getElementById('cbo_order_uom').value = '".$row[csf("order_uom")]."';\n";
	}
	$sql_mst="select id, claim_entry_date, claim_amount_per, base_on_ex_val, air_freight, sea_freight, discount, inspected_by, inspected_company, comments, responsible_dept, claim_validated_by from wo_buyer_claim_mst where po_id='$po_id' and status_active=1 and is_deleted=0";
	$sql_mst_arr=sql_select($sql_mst);
	if(count($sql_mst_arr)>0)
	{
		foreach ($sql_mst_arr as $row)
		{
			$mst_id=$row[csf("id")];
			echo "$('#txt_update_id').val('".$row[csf("id")]."');\n";
			echo "$('#txt_claimentry_date').val('".change_date_format($row[csf("claim_entry_date")])."');\n";
			echo "$('#txt_claimentry_per').val('".$row[csf("claim_amount_per")]."');\n";
			echo "$('#txt_base_exfactory_val').val('".$row[csf("base_on_ex_val")]."');\n";
			
			echo "$('#txt_air_freight').val('".$row[csf("air_freight")]."');\n";
			echo "$('#txt_sea_freight').val('".$row[csf("sea_freight")]."');\n";
			echo "$('#txt_discount').val('".$row[csf("discount")]."');\n";
			
			echo "$('#cbo_inspected_by').val('".$row[csf("inspected_by")]."');\n";
			echo "$('#txt_inspected_comp').val('".$row[csf("inspected_company")]."');\n";
			echo "$('#txt_comments').val('".$row[csf("comments")]."');\n";
			echo "$('#txt_responsible_dept').val('".$row[csf("responsible_dept")]."');\n";
			echo "$('#txt_claim_validated').val('".$row[csf("claim_validated_by")]."');\n";
			
			echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_buyer_claim_entry',1,1);\n";
		}
	}
	
	$sql_dtls="select id, claim_id, is_check, remarks from wo_buyer_claim_dtls where mst_id='$mst_id' and status_active=1 and is_deleted=0";
	$sql_dtls_arr=sql_select($sql_dtls);
	if(count($sql_dtls_arr)>0)
	{	
		$i=1;
		foreach ($sql_dtls_arr as $row)
		{
			$cid=$row[csf("claim_id")];
			
			if($row[csf("is_check")]==1)
			{
				echo "$('#chkRemark_".$cid."').prop('checked', true);\n";
				echo "fnc_checkbox('".$cid."');\n"; 
			}
			else
			{
				echo "$('#chkRemark_".$cid."').prop('checked', false);\n";
				echo "fnc_checkbox('".$cid."');\n"; 
			}
			
			echo "$('#txtDtlsUpId_".$cid."').val('".$row[csf("id")]."');\n";
			echo "$('#chkRemark_".$cid."').val('".$row[csf("is_check")]."');\n";
			echo "$('#txtremarks_".$cid."').val('".$row[csf("remarks")]."');\n";
			$i++;
		}
	}
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	//echo "10**";
	if ($operation==0)  //Insert Here
	{
		$con = connect();
		if($db_type==0) mysql_query("BEGIN");
		 
		$field_array_mst = "id, po_id, claim_entry_date, claim_amount_per, base_on_ex_val, air_freight, sea_freight, discount, inspected_by, inspected_company, comments, responsible_dept, claim_validated_by, inserted_by, insert_date, status_active, is_deleted";
		//$mst_id = return_next_id_by_sequence("wo_buyer_claim_mst_seq", "wo_buyer_claim_mst", $con );
		$mst_id=return_next_id( "id", "wo_buyer_claim_mst", 1) ;
			
		$data_array_mst="(".$mst_id.",".$order_id.",".$txt_claimentry_date.",".$txt_claimentry_per.",".$txt_base_exfactory_val.",".$txt_air_freight.",".$txt_sea_freight.",".$txt_discount.",".$cbo_inspected_by.",".$txt_inspected_comp.",".$txt_comments.",".$txt_responsible_dept.",".$txt_claim_validated.",".$user_id.",'".$pc_date_time."',1,0)";
		
		$field_array_dtls="id, mst_id, claim_id, is_check, remarks, inserted_by, insert_date, status_active, is_deleted";
		$data_array_dtls="";
		$dtlsid=return_next_id( "id", "wo_buyer_claim_dtls", 1) ;
		
		for($j=1;$j<=$tot_row;$j++)
		{
			$chkRemark 		="chkRemark_".$j;
			$txtremarks 	="txtremarks_".$j;
			$hiddnclaimid	="hiddnclaimid_".$j;
			$txtDtlsUpId 	="txtDtlsUpId_".$j;
			
			//$dtlsid= return_next_id_by_sequence("wo_buyer_claim_dtls_seq", "wo_buyer_claim_dtls", $con );
					
			if($data_array_dtls!="") $data_array_dtls.=",";
			$data_array_dtls.="(".$dtlsid.",".$mst_id.",".$$hiddnclaimid.",".$$chkRemark.",".$$txtremarks.",".$user_id.",'".$pc_date_time."',1,0)";
			$dtlsid++;
		}
		
		$flag=1;
		
		$rID=sql_insert("wo_buyer_claim_mst",$field_array_mst,$data_array_mst,1);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		
		$dtlsrID=sql_insert("wo_buyer_claim_dtls",$field_array_dtls,$data_array_dtls,1);
		if($dtlsrID==1 && $flag==1) $flag=1; else $flag=0;
		//============================================================================================
		//echo "10**insert into wo_buyer_claim_mst($field_array_mst)values".$data_array_mst;die;
		//echo "10**".$rID."**".$dtlsrID."**".$flag; die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "0**".$mst_id."**".str_replace("'","",$order_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".$mst_id."**".str_replace("'","",$order_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**".$mst_id."**".str_replace("'","",$order_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".$mst_id."**".str_replace("'","",$order_id);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0) mysql_query("BEGIN");
		
		$mst_id = str_replace("'", "", $txt_update_id);
		$field_array_mst="claim_entry_date*claim_amount_per*base_on_ex_val*air_freight*sea_freight*discount*inspected_by*inspected_company*comments*responsible_dept*claim_validated_by*updated_by*update_date";
		$data_array_mst = "".$txt_claimentry_date."*".$txt_claimentry_per."*".$txt_base_exfactory_val."*".$txt_air_freight."*".$txt_sea_freight."*".$txt_discount."*".$cbo_inspected_by."*".$txt_inspected_comp."*".$txt_comments."*".$txt_responsible_dept."*".$txt_claim_validated."*".$user_id."*'".$pc_date_time."'";
		
		$field_array_dtls="id, mst_id, claim_id, is_check, remarks, inserted_by, insert_date, status_active, is_deleted";
		$data_array_dtls="";
		
		$field_array_dtlsup="claim_id*is_check*remarks*updated_by*update_date";
		$dtlsid=return_next_id( "id", "wo_buyer_claim_dtls", 1) ;
		
		for($j=1;$j<=$tot_row;$j++)
		{
			$chkRemark 		="chkRemark_".$j;
			$txtremarks 	="txtremarks_".$j;
			$hiddnclaimid	="hiddnclaimid_".$j;
			$txtDtlsUpId 	="txtDtlsUpId_".$j;
			
			if(str_replace("'", "", $$txtDtlsUpId)=="")
			{
				//$dtlsid= return_next_id_by_sequence("wo_buyer_claim_dtls_seq", "wo_buyer_claim_dtls", $con );
						
				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls.="(".$dtlsid.",".$mst_id.",".$$hiddnclaimid.",".$$chkRemark.",".$$txtremarks.",".$user_id.",'".$pc_date_time."',1,0)";
				$dtlsid++;
			}
			else
			{
				$data_array_dtlsup[str_replace("'", "", $$txtDtlsUpId)] =explode("*",("".$$hiddnclaimid."*".$$chkRemark."*".$$txtremarks."*'".$user_id."'*'".$pc_date_time."'"));
				$dtlsid=str_replace("'", "", $$txtDtlsUpId);
				$id_arr[]=str_replace("'", "", $$txtDtlsUpId);
			}
		}
		
		$flag=1;
		$rID_mst = sql_update("wo_buyer_claim_mst", $field_array_mst, $data_array_mst, "id", $mst_id, 1);
		if($rID_mst==1 && $flag==1) $flag=1; else $flag=0;
		
		if($data_array_dtlsup!="")
		{
			$rIDup=execute_query(bulk_update_sql_statement("wo_buyer_claim_dtls", "id",$field_array_dtlsup,$data_array_dtlsup,$id_arr ));
			if($rIDup==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		if($data_array_dtls!="")
		{
			$rIDins=sql_insert("wo_buyer_claim_dtls",$field_array_dtls,$data_array_dtls,1);
			if($rIDins==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		//echo "10**".$rID_mst."**".$rIDup."**".$rIDins."**".$flag; die;
		
		if($db_type==0)
		{
			if($flag==1){
				mysql_query("COMMIT");
				echo "1**".$mst_id."**".str_replace("'","",$order_id);
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$mst_id."**".str_replace("'","",$order_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1){
				oci_commit($con);
				echo "1**".$mst_id."**".str_replace("'","",$order_id);
			}
			else{
				oci_rollback($con);
				echo "10**".$mst_id."**".str_replace("'","",$order_id);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		$mst_id = str_replace("'", "", $txt_update_id);
		
		$flag=1;
		$rID = sql_delete("wo_buyer_claim_mst","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'id ',$txt_update_id,1);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		$rID1 = sql_delete("wo_buyer_claim_dtls","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'mst_id ',$txt_update_id,1);
		if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		
		//echo "10**".$rID."**".$rID1."**".$flag; die;
		
		if($db_type==0)
		{
			if($flag==1){
				mysql_query("COMMIT");
				echo "2**".$mst_id."**".str_replace("'","",$order_id);
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$mst_id."**".str_replace("'","",$order_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1){
				oci_commit($con);
				echo "2**".$mst_id."**".str_replace("'","",$order_id);
			}
			else{
				oci_rollback($con);
				echo "10**".$mst_id."**".str_replace("'","",$order_id);
			}
		}
		disconnect($con);
		//echo "2****".$rID;
	}
}
?>