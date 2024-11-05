<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

include('../../../includes/common.php');

$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

//========== start ========




if($action=="company_wise_report_button_setting")
{
	extract($_REQUEST);

	$print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$data."'  and module_id=19 and report_id=147 and is_deleted=0 and status_active=1");
	
	//echo $print_report_format;  die;
	
	$print_report_format_arr=explode(",",$print_report_format);
	//print_r($print_report_format_arr);
	echo "$('#generate_cs').hide();\n";
	
	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{
			
			if($id==739){echo "$('#generate_cs').show();\n";}
			
			
		}
	}
	exit();	
}

//========== start CS Number ========
if ($action=="system_popup")
{

	echo load_html_head_contents("Popup Info", "../../../", 1, 1,$unicode,1,'');
	extract($_REQUEST);
	?>
	<script>
		
	
		function js_set_value( id )
		{
			document.getElementById('selected_id').value=id;
			parent.emailwindow.hide();
		}
		function fn_show()
		{
			/*if(form_validation('cs_no','CS No')==false && form_validation('txt_date_from*txt_date_to','CS Date Range')==false )
			{
				return;
			}*/
			var cs_no=$("#cs_no").val();
			if(cs_no=="")
			{
				if(form_validation('txt_date_from*txt_date_to','CS Date Range')==false )
				{
					return;
				}
			}
			show_list_view ( document.getElementById('cs_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value, 'create_cs_search_list_view', 'search_div', 'comparative_statement_controller', 'setFilterGrid(\'search_div\',-1)');
			setFilterGrid('tbl_list_search',-1);
		}
    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
                <tr>
                    <th>CS No</th>
                    <th colspan="2">CS Date Range</th>
					<th><input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
					<input type="hidden" name="id_field" id="id_field" value="" /></th>
                </tr>        
            </thead>
            <tr class="general">
                <td> 
                    <input type="hidden" id="selected_id">
					<input name="cs_no" id="cs_no" class="text_boxes" style="width:120px">
                </td>
                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:120px" placeholder="From Date"></td>
                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:120px" placeholder="To Date"></td> 
                <td align="center">
                	<input type="button" name="button2" class="formbutton" value="Show" onClick="fn_show()" style="width:100px;" /></td>
        	</tr>
            <tr>
                <td align="center" colspan="4"><? echo load_month_buttons(1); ?></td>
            </tr>
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

if($action=="create_cs_search_list_view")
{
	//echo $data;die;
	$cs_num="";$date_cond ="";$year_cond="";
	list($cs_no,$cs_start_date,$cs_end_date,$cbo_year_selection) = explode('_', $data);
	if ($cs_no!='') {$cs_num=" and sys_number like '%$cs_no'";}
	if ($cs_start_date != '' && $cs_end_date != '') 
	{
        if ($db_type == 0) {
            $date_cond = "and cs_date '" . change_date_format($cs_start_date, 'yyyy-mm-dd') . "' and '" . change_date_format($cs_end_date, 'yyyy-mm-dd') . "'";
        } else if ($db_type == 2) {
            $date_cond = "and cs_date between '" . change_date_format($cs_start_date, '', '', 1) . "' and '" . change_date_format($cs_end_date, '', '', 1) . "'";
		}

    }
	
	if($cbo_year_selection>0)
	{
		if($db_type==0)
		{
			$year_cond=" and YEAR(cs_date) =$cbo_year_selection ";
		}
		else
		{	
			$year_cond=" and to_char(cs_date,'YYYY') =$cbo_year_selection ";
		}
	}

	$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$sql= "select id, sys_number, sys_number_prefix_num, cs_date, req_item_no, company_id from req_comparative_mst where status_active=1 and is_deleted=0 and entry_form=481 $cs_num $date_cond $year_cond order by id DESC";
	//echo $sql;//die;
	$sql_result= sql_select($sql);
	
	?>
	<table width="700" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all">
	<thead>
		<th width="40">SL</th>
		<th width="80">CS No</th>
		<th width="50">CS Suffix</th>
		<th width="80">CS Date</th>
		<th width="150">Requisition/Item No</th>
		<th width="150">Applicable Company</th>
	</thead>
	</table>
	<table width="700" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" id="tbl_list_search">
		<tbody>
		<div style="width:700px; overflow-y:scroll; max-height:280px">
		<?			
            $i = 1;
            foreach($sql_result as $row)
            {
                if ($i%2==0) {$bgcolor="#FFFFFF";} else{ $bgcolor="#E9F3FF";}
                $company_mult=$row[csf('company_id')];
                $company_mult_arr=explode(',',$company_mult);
                $com_short_nam='';
                foreach($company_mult_arr as $com){
                    if($com_short_nam !='')	{$com_short_nam .= ", ".$company_arr[$com];}else{$com_short_nam =$company_arr[$com];}
                }
                ?>
                <tr bgcolor="<? echo $bgcolor;?>" style="text-decoration:none; cursor:pointer;" id="search<? echo $i;?>" onClick="js_set_value( '<? echo $row[csf('id')]; ?>')" >  
                    <td align="center" width="40"><? echo $i; ?></td>
                    <td align="center" width="80"><p><? echo $row[csf('sys_number')]; ?></p></td>
                    <td align="center"  width="50"><p><? echo $row[csf('sys_number_prefix_num')]; ?></p></td>
                    <td align="center" width="80"><p><? echo change_date_format($row[csf('cs_date')]); ?></td>
                    <td align="center" width="150"><p><? echo $row[csf('req_item_no')]; ?></p></td>
                    <td align="center" width="150"><p> <?  echo $com_short_nam; ?></p></td>

                </tr>
                <?
                $i++;
            }
            ?>
        </div>
		</tbody>
	</table>
	<?
	exit();
} 

if ($action=="populate_data_from_search_popup")
{
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	$country_arr = return_library_array("select id,country_name from lib_country where is_deleted=0","id","country_name");
	$data_array=sql_select("select id, sys_number, sys_number_prefix, sys_number_prefix_num, basis_id, req_item_no,req_item_mst_id,req_item_dtls_id, rec_date, cs_date, supp_id, currency_id, cs_valid_date, source, approved, ready_to_approved, company_id, comments from req_comparative_mst where id='$data' and is_deleted=0 and status_active=1");
	$supp_mult_arr='';
	$basis='';
	
	foreach ($data_array as $row)
	{ 
		$supp_mult=$row[csf('supp_id')];
		$basis_id=$row[csf('basis_id')];
		$supp_mult_arr=explode(',',$supp_mult);
		$supp_nam='';
		foreach($supp_mult_arr as $supp){
			if($supp_nam !='')	{$supp_nam .= ", ".$supplier_arr[$supp];}else{$supp_nam =$supplier_arr[$supp];}
		}
		echo "document.getElementById('txt_system_id').value = '".$row[csf("sys_number")]."';\n";  
		echo "document.getElementById('cbo_basis_name').value = '".$row[csf("basis_id")]."';\n";  
		echo "document.getElementById('txt_requisition').value = '".$row[csf("req_item_no")]."';\n";  
		echo "document.getElementById('txt_requisition_mst').value = '".$row[csf("req_item_mst_id")]."';\n";  
		echo "document.getElementById('txt_requisition_dtls').value = '".$row[csf("req_item_dtls_id")]."';\n";  
		echo "document.getElementById('prev_req_dtls_id').value = '".$row[csf("req_item_dtls_id")]."';\n";  
		echo "document.getElementById('txt_rcvd_date').value = '".change_date_format($row[csf("rec_date")])."';\n";
		echo "document.getElementById('txt_cs_date').value = '".change_date_format($row[csf("cs_date")])."';\n";
		echo "document.getElementById('supplier_id').value = '".$row[csf("supp_id")]."';\n";  
		echo "document.getElementById('cbo_currency_name').value = '".$row[csf("currency_id")]."';\n"; 
		echo "document.getElementById('txt_validity_date').value = '".change_date_format($row[csf("cs_valid_date")])."';\n"; 
		// echo "document.getElementById('cbo_cs_type').value = '".$row[csf("cs_type")]."';\n";  
		echo "document.getElementById('cbo_source').value = '".$row[csf("source")]."';\n";  
		echo "document.getElementById('cbo_ready_to_approved').value = '".$row[csf("ready_to_approved")]."';\n";  
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";
		echo "set_multiselect('cbo_company_name','0','1','".$row[csf('company_id')]."','0');\n";   
		echo "document.getElementById('txt_comments').value = '".$row[csf("comments")]."';\n";  

		if ($row[csf("approved")] == 1) echo "$('#approved').text('Approved');\n";
		else if($row[csf("approved")] == 3) echo "$('#approved').text('Partial Approved');\n";
		else echo "$('#approved').text('');\n";
		
		echo "document.getElementById('update_id').value = '".$row[csf("id")]."';\n"; 

		echo "document.getElementById('txt_supplier_name').value = '".$supp_nam."';\n"; 
	}

	$dtls_sql="select a.id as ID, a.prod_id as PROD_ID, a.item_group_id as ITEM_GROUP_ID, a.req_qty as REQ_QTY, a.brand as BRAND, a.model as MODEL, a.origin as ORIGIN
	from req_comparative_dtls a 
	where a.mst_id='$data' and a.is_deleted=0 and a.status_active=1
	order by a.id asc";
	$dtls_sql_result=sql_select($dtls_sql);
	$selected_prod_id="";
	foreach($dtls_sql_result as $row)
	{
		$selected_prod_id.=$row["PROD_ID"].",";
	}
	$selected_prod_id=chop($selected_prod_id,",");

	//$supp_rate_sql="select from req_comparative_supp_dtls a, lib_supplier_wise_rate b where a.prod_id=b.prod_id and b.prod_id in($selected_prod_id) and b.entry_form=481 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0";

	$item_des_sql="select b.id as PROD_ID, b.order_uom as ORDER_UOM, b.item_category_id as ITEM_CATEGORY_ID, b.item_description as ITEM_DESCRIPTION, b.item_code as ITEM_CODE, b.item_size as ITEM_SIZE
	from product_details_master b, lib_item_group c 
	where b.id in($selected_prod_id) and b.item_group_id=c.id and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1";
	$item_des_sql_result=sql_select($item_des_sql);
	$item_des_sql_arr=array();
	foreach($item_des_sql_result as $row)
	{
		$item_des_sql_arr[$row["PROD_ID"]]['PROD_ID']=$row["PROD_ID"];
		$item_des_sql_arr[$row["PROD_ID"]]['ITEM_CATEGORY_ID']=$row["ITEM_CATEGORY_ID"];
		$item_des_sql_arr[$row["PROD_ID"]]['ITEM_DESCRIPTION']=$row["ITEM_DESCRIPTION"];
		$item_des_sql_arr[$row["PROD_ID"]]['ITEM_CODE']=$row["ITEM_CODE"];
		$item_des_sql_arr[$row["PROD_ID"]]['ORDER_UOM']=$row["ORDER_UOM"];
		$item_des_sql_arr[$row["PROD_ID"]]['ITEM_SIZE']=$row["ITEM_SIZE"];
	}

	// $dtls_arr=sql_select("select a.id, a.prod_id, a.item_group, a.req_qty, a.brand, a.model, a.origin, b.order_uom,b.item_category_id, b.item_description,b.item_size,b.item_code from req_comparative_dtls a ,product_details_master b, lib_item_group c where a.mst_id='$data' and a.prod_id=b.id and b.item_group_id=c.id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 order by a.id asc");

	$supp_dtls_arr=sql_select("select id, mst_id,dtls_id, supp_id,prod_id, item_group_id,quoted_price, neg_price, con_price, brand,model,origin,pay_term,tenor, incoterm, warranty, specification from req_comparative_supp_dtls where mst_id='$data' and is_deleted=0 and status_active=1 order by id asc");
	$supplier_count=count($supp_mult_arr);
	$tbl_width=800+($supplier_count*660);

	$data_tbl.='<div width="'.$tbl_width.'" class="tableContainer" style="width: 1345px;text-align: left;">';
	$data_tbl.='<table width="'.$tbl_width.'" id="gvMain" style="width: 100%; border-collapse: collapse;" class="rpt_table" cellpadding="0" border="1">';
	$data_tbl.='<thead><tr class="GridViewScrollHeader">';
	$data_tbl.='<th colspan="10" style="border-right: 2px solid red;">Item Information</th>';
	foreach($supp_mult_arr as $row)
	{
		$data_tbl.='<th colspan="11" style="border-right: 2px solid green;">'.$supplier_arr[$row].'</th>';
	}
	$data_tbl.='</tr>';
	$data_tbl.='<tr class="GridViewScrollHeader">';
	$data_tbl.='<th>SL</th>';
	$data_tbl.='<th>Item Category</th>';
	$data_tbl.='<th>Items Group</th>';
	$data_tbl.='<th>Items Code</th>';
	$data_tbl.='<th>Items Description</th>';
	$data_tbl.='<th>Req. Qty.</th>';
	$data_tbl.='<th>UOM</th>';
	$data_tbl.='<th>Brand</th>';
	$data_tbl.='<th>Model</th>';
	$data_tbl.='<th style="border-right: 2px solid red;">Origin</th>';
	foreach($supp_mult_arr as $row)
	{
		$data_tbl.='<th>Quoted Price</th>';
		$data_tbl.='<th>Last Price</th>';
		$data_tbl.='<th>Con. Price</th>';
		$data_tbl.='<th>Brand</th>';
		$data_tbl.='<th>Specification</th>';
		$data_tbl.='<th>Warranty</th>';
		$data_tbl.='<th>Incoterm </th>';
		$data_tbl.='<th>Model</th>';
		$data_tbl.='<th>Pay Term</th>';
		$data_tbl.='<th>Tenor</th>';
		$data_tbl.='<th style="border-right: 2px solid green;">Origin</th>';
	}

	$data_tbl.='</tr></thead><tbody>';
	$i=1;
	foreach($dtls_sql_result as $row)
	{
		$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ;
		$pro_id=explode(",",$row['PROD_ID']);
		$pro_id_one='';
		foreach($pro_id as $value){
			$pro_id_one=$value;
		}

		if(!empty($item_des_sql_arr[$pro_id_one]['ITEM_SIZE'])){ $item=$item_des_sql_arr[$pro_id_one]['ITEM_DESCRIPTION'].", ".$item_des_sql_arr[$pro_id_one]['ITEM_SIZE'];}else{ $item=$item_des_sql_arr[$pro_id_one]['ITEM_DESCRIPTION'];}
		$data_tbl.='<tr class="GridViewScrollItem" id=trs_'.$i.' bgcolor='.$bgcolor.'>';
		$data_tbl.='<td align="center" >'.$i.'<input type="hidden" name="" id="txtprod_'.$i.'" value="'.$row['PROD_ID'].'" ></td>';
		$data_tbl.='<td align="center" >'.create_drop_down( "txtcategory_$i", 100,$item_category,"", 1, "-- Select --", $item_des_sql_arr[$pro_id_one]['ITEM_CATEGORY_ID'], "",1).'</td>';
		$data_tbl.='<td align="center" ><input type="text" style="width:60px" name="" id="txtgroup_'.$i.'" class="text_boxes" title="'.$row['ITEM_GROUP_ID'].'" value="'.$item_group_arr[$row['ITEM_GROUP_ID']].'" readonly></td>';
		$data_tbl.='<td align="center" ><input type="text" style="width:60px" name="" id="" class="text_boxes" value="'.$item_des_sql_arr[$pro_id_one]['ITEM_CODE'].'" readonly></td>';
		$data_tbl.='<td align="center" ><input type="text" style="width:100px" name="" id="" class="text_boxes" value="'.$item.'" readonly></td>';
		if($basis_id==1)
		{
			$data_tbl.='<td align="center" ><input type="text" style="width:60px" name="" id="txtqty_'.$i.'" class="text_boxes_numeric" value="'.$row['REQ_QTY'].'" readonly></td>';
		}
		else if($basis_id==2)
		{
			$data_tbl.='<td align="center" ><input type="text" style="width:60px" name="" id="txtqty_'.$i.'" class="text_boxes_numeric" value="'.$row['REQ_QTY'].'" ></td>';
		}
		$data_tbl.='<td align="center" ><input type="text" style="width:50px" name="" id="" class="text_boxes" value="'.$unit_of_measurement[$item_des_sql_arr[$pro_id_one]['ORDER_UOM']].'" readonly></td>';
		$data_tbl.='<td align="center" ><input type="text" style="width:60px" name="" id="txtcsbrand_'.$i.'" class="text_boxes" value="'.$row['BRAND'].'" readonly></td>';
		$data_tbl.='<td align="center" ><input type="text" style="width:60px" name="" id="txtcsmodel_'.$i.'" class="text_boxes" value="'.$row['MODEL'].'" readonly></td>';
		$data_tbl.='<td align="center" style="border-right: 2px solid red;"><input type="text" style="width:60px" name="" id="txtcsorigin_'.$i.'" class="text_boxes" value="'.$row['ORIGIN'].'" readonly></td>';
		$row_id=$row['ID'];
		foreach($supp_mult_arr as $supp)
		{
			foreach($supp_dtls_arr as $rows)
			{
				$dtls_row_id=$rows[csf('dtls_id')];
				$supp_id=$rows[csf('supp_id')];
				if($row_id==$dtls_row_id)
				{
					if($supp==$supp_id)
					{
						$data_tbl.='<td align="center" ><input type="text" style="width:50px" name="" id="txtquoted_'.$i.'_'.$supp_id.'" class="text_boxes_numeric" value="'.$rows[csf('quoted_price')].'"></td>';
						$data_tbl.='<td align="center" ><input type="text" style="width:50px" name="" id="txtneg_'.$i.'_'.$supp_id.'"class="text_boxes_numeric" placeholder="Display" value="'.$rows[csf('neg_price')].'" readonly></td>';
						$data_tbl.='<td align="center" ><input type="text" style="width:50px" name="" id="txtcon_'.$i.'_'.$supp_id.'" class="text_boxes_numeric" value="'.$rows[csf('con_price')].'"></td>';
						$data_tbl.='<td align="center" ><input type="text" style="width:50px" name="" id="txtbrand_'.$i.'_'.$supp_id.'" class="text_boxes" value="'.$rows[csf('brand')].'"></td>';
						$data_tbl.='<td align="center" ><input type="text" style="width:50px" name="" id="txtspecification_'.$i.'_'.$supp_id.'" class="text_boxes" value="'.$rows[csf('specification')].'"></td>'; 
						$data_tbl.='<td align="center" ><input type="text" style="width:50px" name="" id="txtWorranty_'.$i.'_'.$supp_id.'" class="text_boxes" value="'.$rows[csf('warranty')].'"></td>'; 
						$data_tbl.='<td align="center" >'.create_drop_down("txtIncoTram_".$i.'_'.$supp_id, 70, $incoterm, "", 1, "-- Select --", $rows[csf('incoterm')] , "", "").'</td>';
						$data_tbl.='<td align="center" ><input type="text" style="width:50px" name="" id="txtmodel_'.$i.'_'.$supp_id.'" class="text_boxes" value="'.$rows[csf('model')].'"></td>';
						$data_tbl.='<td align="center" >'.create_drop_down("txtPayTerm_".$i.'_'.$supp_id, 70, $pay_term, "", 1, "-- Select --", $rows[csf('pay_term')] , "", "").'</td>';
						$data_tbl.='<td align="center" ><input type="text" style="width:50px" name="" id="txtTenor_'.$i.'_'.$supp_id.'" class="text_boxes_numeric" value="'.$rows[csf('tenor')].'"></td>';
						$data_tbl.='<td align="center" style="border-right: 2px solid green;"><input type="text" style="width:50px" name="" id="txtorigin_'.$i.'_'.$supp_id.'" class="text_boxes" value="'.$rows[csf('origin')].'"></td>';
					}
				}
			}
		}
		$data_tbl.='</tr>';
		$i++;
	}

	$data_tbl.='</tbody></table></div>';
	echo "document.getElementById('cs_tbl').innerHTML = '".$data_tbl."';\n"; 

	exit();
}
//========== End CS Number ========

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	//echo "10**5=$operation=";die;

	if($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
	
		$mst_id=return_next_id("id", "req_comparative_mst", 1);
		
		if($db_type==0) $insert_date_con=" and YEAR(insert_date)=".date('Y',time())."";
		else if($db_type==2) $insert_date_con=" and TO_CHAR(insert_date,'YYYY')=".date('Y',time())."";
		
		$new_sys_no=explode("*",return_mrr_number( '', '', '', date("Y",time()), 5, "select sys_number_prefix,sys_number_prefix_num from req_comparative_mst where entry_form=481 $insert_date_con order by id desc ", "sys_number_prefix", "sys_number_prefix_num" ));

		$field_array_mst="id, sys_number, sys_number_prefix, sys_number_prefix_num, entry_form, basis_id, req_item_no,req_item_mst_id,req_item_dtls_id, rec_date, cs_date, supp_id, currency_id, cs_valid_date, source, ready_to_approved, approved, company_id, comments, inserted_by, insert_date, status_active, is_deleted";

		$txt_requisition_mst=implode(",",array_unique(explode(",",chop(str_replace("'","",$txt_requisition_mst),","))));
		$data_array_mst="(".$mst_id.",'".$new_sys_no[0]."','".$new_sys_no[1]."','".$new_sys_no[2]."',481,".$cbo_basis_name.",".$txt_requisition.",'".$txt_requisition_mst."',".$txt_requisition_dtls.",".$txt_rcvd_date.",".$txt_cs_date.",".$supplier_id.",".$cbo_currency_name.",".$txt_validity_date.",".$cbo_source.",".$cbo_ready_to_approved.",0,".$cbo_company_name.",".$txt_comments.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		//  echo "10**INSERT INTO req_comparative_mst (".$field_array_mst.") VALUES ".$data_array_mst; 
		// die;

		$field_array_dtls="id, mst_id, item_category_id,prod_id, item_group_id, req_qty, brand, model, origin, supp_data, inserted_by, insert_date, is_deleted, status_active";
		$field_array_supp_dtls="id, mst_id, dtls_id, supp_id, prod_id, item_group_id, quoted_price, neg_price, con_price, last_approval_rate, brand, model, origin, pay_term, tenor, warranty, incoterm, specification, inserted_by, insert_date, is_deleted, status_active";

		$id_dtls=return_next_id("id", "req_comparative_dtls", 1);
		$id_supp_dtls=return_next_id("id", "req_comparative_supp_dtls", 1);
		$col_num_arr = explode(',',$supplier_id);
		$data_array_dtls='';
		$data_array_supp_dtls='';
		for($i=1; $i<=$row_num; $i++)
		{
			$supp_data='';
			$txtprod="txtprod_".$i;
			$txtcategory="txtcategory_".$i;
			$txtgroup="txtgroup_".$i;
			$txtqty="txtqty_".$i;
			$txtcsbrand ="txtcsbrand_".$i;
			$txtcsmodel="txtcsmodel_".$i;
			$txtcsorigin="txtcsorigin_".$i;

				for($m=0; $m<$col_num; $m++)
				{
					$mm=str_replace("'","",$col_num_arr[$m]);
					$txtsuppier= "txtsuppier_".$i."_".$mm;
					$txtquoted="txtquoted_".$i."_".$mm;
					$txtneg="txtneg_".$i."_".$mm;
					$txtcon="txtcon_".$i."_".$mm;
					$txtbrand="txtbrand_".$i."_".$mm;
					$txtmodel="txtmodel_".$i."_".$mm;
					$txtorigin="txtorigin_".$i."_".$mm;
					$txtPayTerm="txtPayTerm_".$i."_".$mm;
					$txtTenor="txtTenor_".$i."_".$mm;
					$txtWorranty="txtWorranty_".$i."_".$mm;
					$txtIncoTram="txtIncoTram_".$i."_".$mm;
					$txtspecification="txtspecification_".$i."_".$mm;
					if($supp_data!=''){
						$supp_data.= "*".$$txtsuppier."_".$$txtquoted."_".$$txtneg."_".$$txtcon."_".$$txtbrand."_".$$txtmodel."_".$$txtorigin."_".$$txtPayTerm."_".$$txtTenor."_".$$txtWorranty."_".$$txtIncoTram."_".$$txtspecification;
					}else{
						$supp_data = $$txtsuppier."_".$$txtquoted."_".$$txtneg."_".$$txtcon."_".$$txtbrand."_".$$txtmodel."_".$$txtorigin."_".$$txtPayTerm."_".$$txtTenor."_".$$txtWorranty."_".$$txtIncoTram."_".$$txtspecification;
					}

					if ($data_array_supp_dtls!='') {$data_array_supp_dtls .=",";}
					$data_array_supp_dtls .="(".$id_supp_dtls.",".$mst_id.",".$id_dtls.",'".$$txtsuppier."','".$$txtprod."','".$$txtGroup."','".$$txtquoted."','".$$txtneg."','".$$txtcon."','".$$txtcon."','".$$txtbrand."','".$$txtmodel."','".$$txtorigin."','".$$txtPayTerm."','".$$txtTenor."','".$$txtWorranty."','".$$txtIncoTram."','".$$txtspecification."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,1)";
					$id_supp_dtls++;
				}

			if ($data_array_dtls!='') {$data_array_dtls .=",";}
			$data_array_dtls .="(".$id_dtls.",".$mst_id.",'".$$txtcategory."','".$$txtprod."','".$$txtgroup."','".$$txtqty."','".$$txtcsbrand."','".$$txtcsmodel."','".$$txtcsorigin."','".$supp_data."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,1)";

			$id_dtls++;
		}
		//echo "</br>100**INSERT INTO req_comparative_dtls (".$field_array_dtls.") VALUES ".$data_array_dtls; die;
		//echo "</br>1000**INSERT INTO req_comparative_dtls (".$field_array_dtls.") VALUES ".$data_array_dtls; die;
		
		$rID=sql_insert("req_comparative_mst",$field_array_mst,$data_array_mst,0);
		$rID1=sql_insert("req_comparative_dtls",$field_array_dtls,$data_array_dtls,0);
		$rID2=sql_insert("req_comparative_supp_dtls",$field_array_supp_dtls,$data_array_supp_dtls,0);	
		// echo '</br>10**'.$rID.'**'.$rID1.'**'.$rID2;oci_rollback($con);die;
		
		if($db_type==0)
		{
			if($rID==1 && $rID1==1 && $rID2==1)
			{
				mysql_query("COMMIT");  
				echo "0**".$new_sys_no[0]."**".$mst_id."**".str_replace("'","",$txt_requisition_dtls);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID==1 && $rID1==1 && $rID2==1)
			{
				oci_commit($con);  
				echo "0**".$new_sys_no[0]."**".$mst_id."**".str_replace("'","",$txt_requisition_dtls);
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
	elseif($operation==1) // Update Here----------------------------------------------------------
	{
		
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$cs_approved=return_field_value("approved","req_comparative_mst","id=$update_id","approved");
		if($cs_approved==1 || $cs_approved==3)
		{
			echo "11**CS Approved, Update Not Allow";disconnect($con);oci_rollback($con);die;
		}
		
		$field_array_mst="basis_id*req_item_no*req_item_mst_id*req_item_dtls_id*rec_date*cs_date*supp_id*currency_id*cs_valid_date*source*ready_to_approved*company_id*comments*updated_by*update_date";
		$txt_requisition_mst=implode(",",array_unique(explode(",",chop(str_replace("'","",$txt_requisition_mst),","))));
		$data_array_mst="".$cbo_basis_name."*".$txt_requisition."*'".$txt_requisition_mst."'*".$txt_requisition_dtls."*".$txt_rcvd_date."*".$txt_cs_date."*".$supplier_id."*".$cbo_currency_name."*".$txt_validity_date."*".$cbo_source."*".$cbo_ready_to_approved."*".$cbo_company_name."*".$txt_comments."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$field_array_dtls="id, mst_id, item_category_id, prod_id, item_group_id, req_qty, brand, model, origin, supp_data, inserted_by, insert_date, is_deleted, status_active";

		$field_array_supp_dtls="id, mst_id,dtls_id, supp_id,prod_id, item_group_id, quoted_price, neg_price, con_price, last_approval_rate, brand,model,origin, pay_term, tenor, warranty, incoterm, specification, inserted_by, insert_date, is_deleted, status_active";
		
		$id_dtls=return_next_id("id", "req_comparative_dtls", 1);
		$id_supp_dtls=return_next_id("id", "req_comparative_supp_dtls", 1);
		$col_num_arr = explode(',',$supplier_id);
		$data_array_dtls='';
		$data_array_supp_dtls='';
		
		for($i=1; $i<=$row_num; $i++)
		{
			$supp_data='';
			$txtprod="txtprod_".$i;
			$txtcategory="txtcategory_".$i;
			$txtgroup="txtgroup_".$i;
			$txtqty="txtqty_".$i;
			$txtcsbrand ="txtcsbrand_".$i;
			$txtcsmodel="txtcsmodel_".$i;
			$txtcsorigin="txtcsorigin_".$i;

				for($m=0; $m<$col_num; $m++)
				{
					$mm=str_replace("'","",$col_num_arr[$m]);
					$txtsuppier= "txtsuppier_".$i."_".$mm;
					$txtquoted="txtquoted_".$i."_".$mm;
					$txtneg="txtneg_".$i."_".$mm;
					$txtcon="txtcon_".$i."_".$mm;
					$txtbrand="txtbrand_".$i."_".$mm;
					$txtmodel="txtmodel_".$i."_".$mm;
					$txtorigin="txtorigin_".$i."_".$mm;
					$txtPayTerm="txtPayTerm_".$i."_".$mm;
					$txtTenor="txtTenor_".$i."_".$mm;
					$txtWorranty="txtWorranty_".$i."_".$mm;
					$txtIncoTram="txtIncoTram_".$i."_".$mm;
					$txtspecification="txtspecification_".$i."_".$mm;

					if($supp_data!=''){
						$supp_data.= "*".$$txtsuppier."_".$$txtquoted."_".$$txtneg."_".$$txtcon."_".$$txtbrand."_".$$txtmodel."_".$$txtorigin."_".$$txtPayTerm."_".$$txtTenor."_".$$txtWorranty."_".$$txtIncoTram."_".$$txtspecification;
					}else{
						$supp_data = $$txtsuppier."_".$$txtquoted."_".$$txtneg."_".$$txtcon."_".$$txtbrand."_".$$txtmodel."_".$$txtorigin."_".$$txtPayTerm."_".$$txtTenor."_".$$txtWorranty."_".$$txtIncoTram."_".$$txtspecification;
					}

					if ($data_array_supp_dtls!='') {$data_array_supp_dtls .=",";}
					$data_array_supp_dtls .="(".$id_supp_dtls.",".$update_id.",".$id_dtls.",'".$$txtsuppier."','".$$txtprod."','".$$txtgroup."','".$$txtquoted."','".$$txtneg."','".$$txtcon."','".$$txtcon."','".$$txtbrand."','".$$txtmodel."','".$$txtorigin."','".$$txtPayTerm."','".$$txtTenor."','".$$txtWorranty."','".$$txtIncoTram."','".$$txtspecification."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,1)";
					$id_supp_dtls++;
				}

			if ($data_array_dtls!='') {$data_array_dtls .=",";}
			$data_array_dtls .="(".$id_dtls.",".$update_id.",'".$$txtcategory."','".$$txtprod."','".$$txtgroup."','".$$txtqty."','".$$txtcsbrand."','".$$txtcsmodel."','".$$txtcsorigin."','".$supp_data."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,1)";

			$id_dtls++;
		}

		$rID=sql_update("req_comparative_mst",$field_array_mst,$data_array_mst,"id","".$update_id."",0);
		$rID1=execute_query("delete from req_comparative_dtls where mst_id =".$update_id."",0);
		$rID2=execute_query("delete from req_comparative_supp_dtls where mst_id =".$update_id."",0);
		$rID3=sql_insert("req_comparative_dtls",$field_array_dtls,$data_array_dtls,0);	
		$rID4=sql_insert("req_comparative_supp_dtls",$field_array_supp_dtls,$data_array_supp_dtls,0);	
		// echo "10**INSERT INTO req_comparative_supp_dtls (".$field_array_supp_dtls.") VALUES ".$data_array_supp_dtls; disconnect($con); die;
		// echo "</br>10**".$rID.'='.$rID1.'='.$rID2.'='.$rID3.'='.$rID4."</br>";disconnect($con); die;

		// echo "10**".$field_array_supp_dtls;disconnect($con);die;
		if($db_type==0)
		{
			if($rID==1 && $rID1==1 && $rID2==1 && $rID3==1 && $rID4==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$txt_system_id)."**".str_replace("'",'',$update_id)."**".str_replace("'","",$txt_requisition_dtls);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID==1 && $rID1==1 && $rID2==1 && $rID3==1 && $rID4==1)
			{
				oci_commit($con);  
				echo "1**".str_replace("'",'',$txt_system_id)."**".str_replace("'",'',$update_id)."**".str_replace("'","",$txt_requisition_dtls);
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
	
		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;
	}
	else if ($operation==2) // Delete Here----------------------------------------------------------  
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$cs_approved=return_field_value("approved","req_comparative_mst","id=$update_id","approved");
		if($cs_approved==1 || $cs_approved==3)
		{
			echo "11**CS Approved, Delete Not Allow";disconnect($con);oci_rollback($con);die;
		}
		

		$field_array="status_active*is_deleted*updated_by*update_date";
		$data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$rID=sql_delete("req_comparative_mst",$field_array,$data_array,"id","".$update_id."",0);
		$rID1=sql_delete("req_comparative_dtls",$field_array,$data_array,"mst_id","".$update_id."",0);
		$rID2=sql_delete("req_comparative_supp_dtls",$field_array,$data_array,"mst_id","".$update_id."",0);
		// echo "10**".$rID.'='.$rID1.'='.$rID2."</br>"; die;
		if($db_type==0)
		{
			if($rID==1 && $rID1==1 && $rID2==1)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'",'',$txt_system_id)."**".str_replace("'",'',$update_id);;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID==1 && $rID1==1 && $rID2==1)
			{
				oci_commit($con);  
				echo "2**".str_replace("'",'',$txt_system_id)."**".str_replace("'",'',$update_id);;
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		
		disconnect($con);
	}// Delete Here End ----------------------------------------------------------
	
}

//========== Generate CS ========
if($action=="load_cs_table")
{
	list($basis, $req_mst, $req_dtls, $supplier_id, $update_id, $txt_requisition_dtls) = explode('**', $data);
	//echo $data."<br>";
	//echo $txt_requisition_dtls."<br>";die;
	$supplier=explode(',', $supplier_id);
	$supplier_count=count($supplier);
	$tbl_width=800+($supplier_count*660);
	$supplier_arr=return_library_array( "select supplier_name,id from lib_supplier where status_active=1 and is_deleted=0 order by supplier_name",'id','supplier_name');
	$country_arr = return_library_array("select id,country_name from lib_country where is_deleted=0","id","country_name");
	if($basis==1)
	{
		if($db_type==0)
		{
			$sql = "select group_concat( b.id) as PROD_IDS, group_concat( a.brand_name) as BRAND_NAME, group_concat( a.model) as MODEL ,group_concat( a.origin) as ORIGIN, sum(a.quantity) as REQ_QTY, b.order_uom as ORDER_UOM, b.item_category_id as ITEM_CATEGORY_ID, b.item_description as ITEM_DESCRIPTION, b.item_group_id as ITEM_GROUP_ID, b.item_size as ITEM_SIZE, c.item_name as ITEM_NAME, b.item_code as ITEM_CODE, max(a.product_id) as PROD_ID 
			from inv_purchase_requisition_dtls a, product_details_master b, lib_item_group c 
			where a.product_id=b.id and b.item_group_id=c.id and a.id in($req_dtls) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
			group by b.order_uom,b.item_category_id, b.item_description,b.item_size,c.item_name, b.item_code, b.item_group_id";
		}
		else
		{
			$sql = "select a.brand_name as BRAND_NAME, a.model as MODEL, a.origin as ORIGIN, b.order_uom as ORDER_UOM, b.item_category_id as ITEM_CATEGORY_ID, b.item_description as ITEM_DESCRIPTION, b.item_group_id as ITEM_GROUP_ID, b.item_size as ITEM_SIZE, c.item_name as ITEM_NAME, b.item_code as ITEM_CODE, sum(a.quantity) as REQ_QTY, listagg(cast(b.id as varchar(4000)),',') within group(order by b.id) as PROD_IDS, max(a.product_id) as PROD_ID 
			from inv_purchase_requisition_dtls a, product_details_master b, lib_item_group c 
			where a.product_id=b.id and b.item_group_id=c.id and a.id in($req_dtls) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
			group by a.brand_name, a.model, a.origin, b.order_uom, b.item_category_id, b.item_description,b.item_size,c.item_name, b.item_code, b.item_group_id
			order by b.item_group_id, b.item_description";
		}
		
		$supp_prev_entry_data=array();

		if($update_id)
		{
			$prev_sup_sql="select b.id, b.mst_id, b.dtls_id, b.supp_id, b.prod_id, b.quoted_price, b.neg_price, b.con_price, b.brand, b.model, b.origin, b.pay_term, b.tenor 
			from req_comparative_mst a, req_comparative_supp_dtls b 
			where a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.basis_id=1 and b.mst_id=$update_id and a.req_item_dtls_id='$txt_requisition_dtls'";
			//echo $prev_sup_sql;die;
			$prev_sup_sql_result=sql_select($prev_sup_sql);
			foreach($prev_sup_sql_result as $row)
			{
				$max_prod_id=max(explode(",",$row[csf("prod_id")]));
				$supp_prev_entry_data[$max_prod_id][$row[csf("supp_id")]]["quoted_price"]=$row[csf("quoted_price")];
				$supp_prev_entry_data[$max_prod_id][$row[csf("supp_id")]]["neg_price"]=$row[csf("neg_price")];
				$supp_prev_entry_data[$max_prod_id][$row[csf("supp_id")]]["con_price"]=$row[csf("con_price")];
				$supp_prev_entry_data[$max_prod_id][$row[csf("supp_id")]]["brand"]=$row[csf("brand")];
				$supp_prev_entry_data[$max_prod_id][$row[csf("supp_id")]]["model"]=$row[csf("model")];
				$supp_prev_entry_data[$max_prod_id][$row[csf("supp_id")]]["pay_term"]=$row[csf("pay_term")];
				$supp_prev_entry_data[$max_prod_id][$row[csf("supp_id")]]["tenor"]=$row[csf("tenor")];
				$supp_prev_entry_data[$max_prod_id][$row[csf("supp_id")]]["origin"]=$row[csf("origin")];
			}
		}
	}
	if($basis==2)
	{
		if($db_type==0)
		{
			$sql = "select group_concat( a.id) as PROD_IDS ,a.item_category_id as ITEM_CATEGORY_ID,a.brand_name as BRAND_NAME,a.model as MODEL,a.origin as ORIGIN,a.order_uom as ORDER_UOM, a.item_description as ITEM_DESCRIPTION, a.item_group_id as ITEM_GROUP_ID, a.item_size as ITEM_SIZE, a.item_code as ITEM_CODE,b.item_name as ITEM_NAME, max(a.id) as PROD_ID 
			from product_details_master a, lib_item_group b 
			where a.item_group_id=b.id and a.id in($req_mst) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
			group by a.item_category_id,a.brand_name,a.model,a.origin,a.order_uom, a.item_description,a.item_size,a.item_code,b.item_name,a.item_group_id";
		}
		else
		{
			$sql = "select listagg(cast(a.id as varchar(4000)),',') within group(order by a.id) as PROD_IDS ,a.item_category_id as ITEM_CATEGORY_ID,a.order_uom as ORDER_UOM,a.brand_name as BRAND_NAME,a.model as MODEL,a.origin as ORIGIN, a.item_description as ITEM_DESCRIPTION, a.item_group_id as ITEM_GROUP_ID, a.item_size as ITEM_SIZE, a.item_code as ITEM_CODE, b.item_name as ITEM_NAME, max(a.id) as PROD_ID  
			from product_details_master a, lib_item_group b 
			where a.item_group_id=b.id and a.id in($req_mst) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
			group by a.item_category_id,a.order_uom,a.brand_name,a.model,a.origin, a.item_description,a.item_size,a.item_code,b.item_name,a.item_group_id";
		}
		//echo $sql;
		//
		//
		
		$supp_prev_entry_data=array();
		if($update_id)
		{
			$prev_sup_sql="select b.id, b.mst_id, b.dtls_id, b.supp_id, b.prod_id, b.quoted_price, b.neg_price, b.con_price, b.brand, b.model, b.origin, b.item_group_id, b.pay_term, b.tenor 
			from req_comparative_mst a, req_comparative_supp_dtls b 
			where a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.basis_id=2 and b.mst_id=$update_id";
			//echo $prev_sup_sql;die;
			$prev_sup_sql_result=sql_select($prev_sup_sql);
			foreach($prev_sup_sql_result as $row)
			{
				$max_prod_id=max(explode(",",$row[csf("prod_id")]));
				$supp_prev_entry_data[$max_prod_id][$row[csf("supp_id")]]["quoted_price"]=$row[csf("quoted_price")];
				$supp_prev_entry_data[$max_prod_id][$row[csf("supp_id")]]["neg_price"]=$row[csf("neg_price")];
				$supp_prev_entry_data[$max_prod_id][$row[csf("supp_id")]]["con_price"]=$row[csf("con_price")];
				$supp_prev_entry_data[$max_prod_id][$row[csf("supp_id")]]["brand"]=$row[csf("brand")];
				$supp_prev_entry_data[$max_prod_id][$row[csf("supp_id")]]["model"]=$row[csf("model")];
				$supp_prev_entry_data[$max_prod_id][$row[csf("supp_id")]]["pay_term"]=$row[csf("pay_term")];
				$supp_prev_entry_data[$max_prod_id][$row[csf("supp_id")]]["tenor"]=$row[csf("tenor")];
				$supp_prev_entry_data[$max_prod_id][$row[csf("supp_id")]]["origin"]=$row[csf("origin")];
			}
		}
	}
	
	//echo "<pre>";print_r($supp_prev_entry_data);die;

	$sql_rate="select max(a.effective_from) as effective_from, a.supplier_id, a.rate, a.item_category_id, a.item_group_id, a.prod_id, b.item_description, b.unit_of_measure, b.brand_name, b.item_code from lib_supplier_wise_rate a, product_details_master b where a.prod_id=b.id and a.entry_form=481 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.supplier_id, a.rate, a.item_category_id, a.item_group_id, a.prod_id, b.item_description, b.unit_of_measure, b.brand_name, b.item_code";
	$sql_rate_res=sql_select($sql_rate);
	$product_id_array=array();
	foreach ($sql_rate_res as $row) {
		$key=$row[csf("item_category_id")].'**'.$row[csf("item_group_id")].'**'.$row[csf("item_code")].'**'.$row[csf("item_description")].'**'.$row[csf("unit_of_measure")].'**'.$row[csf("brand_name")];
		//$product_id_array[$key][$row[csf("supplier_id")]]=$row[csf("prod_id")];
		$product_id_array[$key][$row[csf("supplier_id")]]=$row[csf("rate")];
	}
	//echo '<pre>';print_r($product_id_array);

	//echo $sql;die;
	$data_array=sql_select($sql);
	?>
	<style type="text/css">        
    .GridViewScrollHeader TH, .GridViewScrollHeader TD 
    {
        padding: 2px 4px;
        font-weight: normal;
        white-space: nowrap;
        border-right: 1px solid #e6e6e6;
        border-bottom: 1px solid #e6e6e6;
        /*background-color: #F4F4F4;*/
        color: #000000;
        text-align: left;
        vertical-align: middle;
    }

    .GridViewScrollHeader TH{
        background-image: -webkit-linear-gradient( rgb(194,220,255) 10%, rgb(136,170,214) 96%);
        border: 1px solid #8DAFDA;
        color:#444;
        font-size: 13px;
        font-weight: bold;
        text-align: center;
        line-height: 12px;
        height: 25px;
    }

    .GridViewScrollItem TD {
            padding: 2px 4px;
        white-space: nowrap;
        border-right: 1px solid #e6e6e6;
        border-bottom: 1px solid #e6e6e6;
        /* background-color: #FFFFFF;*/
        color: #000000;
        vertical-align: middle;
    }

    .GridViewScrollItemFreeze TD {
            padding: 2px 4px;
        white-space: nowrap;
        border-right: 1px solid #e6e6e6;
        border-bottom: 1px solid #e6e6e6;
        /*background-color: #E9F3FF;*/
        color: #000000;
        vertical-align: middle;
    }

    .GridViewScrollFooterFreeze TD {
            padding: 2px 4px;
        white-space: nowrap;
        border-right: 1px solid #e6e6e6;
        border-top: 1px solid #e6e6e6;
        border-bottom: 1px solid #e6e6e6;
        /*background-color: #F4F4F4;*/
        color: #000000;
        vertical-align: middle;
        font-weight: 700;
        background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #F0F0F0), color-stop(100%, #DBDBDB));
    }
    tr.GridViewScrollItemFreeze:last-child TD{ background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #F0F0F0), color-stop(100%, #DBDBDB)); }
    tr.footerTr:last-child TD{ background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #F0F0F0), color-stop(100%, #DBDBDB)); }
    html {
        --scrollbarBG: #CFD8DC;
        --thumbBG: #8ec5fc;
    }
    body::-webkit-scrollbar {
        width: 11px;
    }
    body {
        scrollbar-width: thin;
        scrollbar-color: var(--thumbBG) var(--scrollbarBG);
    }
    body::-webkit-scrollbar-track {
        background: var(--scrollbarBG);
    }
    body::-webkit-scrollbar-thumb {
        background-color: var(--thumbBG) ;
        border-radius: 6px;
        border: 3px solid var(--scrollbarBG);
    }
	</style>
	<div class="tableContainer" style="width: 1345px;text-align: left;">
	<table cellspacing="0" id="gvMain" style="width: 100%; border-collapse: collapse;" class="rpt_table" cellpadding="0" border="1">
		<thead>
			<tr class="GridViewScrollHeader">
				<th colspan="10" style="border-right: 2px solid red;">Item Information</th>
				<?
					foreach($supplier as $row)
					{
						?>
							<th colspan="11" style="border-right: 2px solid green;"><? echo $supplier_arr[$row]; ?></th>
						<?
					}
				?>
			</tr>
			<tr class="GridViewScrollHeader">
				<th>SL</th>
				<th>Item Category</th>
				<th>Items Group</th>
				<th>Items Code</th>
				<th>Items Description</th>
				<th>Req. Qty.</th>
				<th>UOM</th>
				<th>Brand</th>
				<th>Model</th>
				<th style="border-right: 2px solid red;">Origin</th>
				<?
					foreach($supplier as $row)
					{
						?>
							<th>Quoted Price</th>
							<th>Last Price</th>
							<th>Con. Price</th>
							<th>Brand</th>
							<th>Model</th>
							<th>Specification</th>
							<th>Warranty</th>
							<th>Incoterm </th>
							<th>Pay Term</th>
							<th>Tenor</th>
							<th style="border-right: 2px solid green;">Origin</th>
						<?
					}
				?>
			</tr>
		</thead>
		<tbody>
		<?
		$i=1;
		foreach($data_array as $row)
		{
			$key=$row['ITEM_CATEGORY_ID'].'**'.$row['ITEM_GROUP_ID'].'**'.$row['ITEM_CODE'].'**'.$row['ITEM_DESCRIPTION'].'**'.$row['ORDER_UOM'].'**'.$row['BRAND_NAME'];
			$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ;
			?>
			<tr class="GridViewScrollItem" id="trs_<? echo $i;?>" bgcolor="<? echo $bgcolor; ?>">
				<td align="center" ><?= $i;?>
				<input type="hidden" name="" id="txtprod_<?= $i;?>" value="<? echo $row['PROD_IDS'] ;?>" >
				</td>
				<td align="center" >
					<? echo create_drop_down( "txtcategory_$i", 100,$item_category,"", 1, "-- Select --", $row['ITEM_CATEGORY_ID'], "",1);?>
				</td>
				<td align="center" >
					<input type="text" style="width:60px" name="" id="txtgroup_<?= $i;?>" class="text_boxes" title="<? echo $row['ITEM_GROUP_ID'] ;?>" value="<? echo $row['ITEM_NAME'] ;?>" readonly>
				</td>
				<td align="center" >
					<input type="text" style="width:60px" name="" id="" class="text_boxes" value="<? echo $row['ITEM_CODE'] ;?>" readonly>
				</td>
				<td align="center" >
					<input type="text" style="width:100px" name="" id="" class="text_boxes" value="<?
					if(!empty($row['ITEM_SIZE'])){echo $row['ITEM_DESCRIPTION'].", ".$row['ITEM_SIZE'];}else{echo $row['ITEM_DESCRIPTION'];} ?>" readonly>
				</td>
				<td align="center" >
					<?if($basis==1)
					{				
					?>
						<input type="text" style="width:60px" name="" id="txtqty_<?= $i;?>" class="text_boxes_numeric" value="<?echo $row['REQ_QTY'];?>" readonly>
					<?
					}
					if($basis==2)
					{
					?>
						<input type="text" style="width:60px" name="" id="txtqty_<?= $i;?>" class="text_boxes_numeric" value="" >
					<?
					}?>
					
				</td>
				<td align="center" >
					<input type="text" style="width:60px" name="" id="" class="text_boxes" value="<? echo $unit_of_measurement[$row['ORDER_UOM']] ;?>" readonly>
				</td>
				<td align="center" >
					<input type="text" style="width:60px" name="" id="txtcsbrand_<?= $i;?>" class="text_boxes" value="<? echo $row['BRAND_NAME'] ;?>" readonly>
				</td>
				<td align="center" >
					<input type="text" style="width:60px" name="" id="txtcsmodel_<?= $i;?>" class="text_boxes" value="<? echo $row['MODEL'] ;?>" readonly>
				</td>
				<td align="center" style="border-right: 2px solid red;">
					<? 
					$origing_id_arr=array_unique(explode(",",$row['ORIGIN'])); 
					$origin_name="";
					foreach($origing_id_arr as $ori_id)
					{
						$origin_name.=$country_arr[$ori_id].",";
					}
					$origin_name=chop($origin_name,",");
					?>
					<input type="text" style="width:60px" name="" id="txtcsorigin_<?= $i;?>" class="text_boxes" value="<? echo $origin_name;?>" readonly>
				</td>
			    <?
				foreach($supplier as $sup_id)
				{
					?>
						<td align="center" ><input type="text" style="width:50px" name="" id="txtquoted_<?= $i.'_'.$sup_id;?>" value="<?= $supp_prev_entry_data[$row["PROD_ID"]][$sup_id]["quoted_price"];?>" class="text_boxes_numeric"></td>
						<td align="center" ><input type="text" style="width:50px" name="" id="txtneg_<?= $i.'_'.$sup_id;?>"  value="<?= $product_id_array[$key][$sup_id];?>" class="text_boxes_numeric" placeholder="Display" readonly></td>
						<td align="center" ><input type="text" style="width:50px" name="" id="txtcon_<?= $i.'_'.$sup_id;?>"  value="<?= $supp_prev_entry_data[$row["PROD_ID"]][$sup_id]["con_price"];?>" class="text_boxes_numeric"></td>
						<td align="center" ><input type="text" style="width:50px" name="" id="txtbrand_<?= $i.'_'.$sup_id;?>"  value="<?= $supp_prev_entry_data[$row["PROD_ID"]][$sup_id]["brand"];?>" class="text_boxes" ></td>
						<td align="center" ><input type="text" style="width:50px" name="" id="txtmodel_<?= $i.'_'.$sup_id;?>"  value="<?= $supp_prev_entry_data[$row["PROD_ID"]][$sup_id]["model"];?>" class="text_boxes" ></td>
						
						<td align="center" ><input type="text" style="width:50px" name="" id="txtspecification_<?= $i.'_'.$sup_id;?>"  value="<??>" class="text_boxes" ></td>
						
						<td align="center" ><input type="text" style="width:50px" name="" id="txtWorranty_<?= $i.'_'.$sup_id;?>"  value="<??>" class="text_boxes" ></td>
						<td align="center" >
							<? echo create_drop_down("txtIncoTram_".$i.'_'.$sup_id, 70, $incoterm, "", 1, "-- Select --", "" , "", ""); ?>
						</td>
						<td align="center" >
							<? echo create_drop_down("txtPayTerm_".$i.'_'.$sup_id, 70, $pay_term, "", 1, "-- Select --", $supp_prev_entry_data[$row["PROD_ID"]][$sup_id]["pay_term"] , "", ""); ?>
						</td>
						<td align="center" ><input type="text" style="width:50px" name="" id="txtTenor_<?= $i.'_'.$sup_id;?>"  value="<?= $supp_prev_entry_data[$row["PROD_ID"]][$sup_id]["tenor"];?>" class="text_boxes_numeric" ></td>
						<td align="center" style="border-right: 2px solid green;"><input type="text" style="width:50px" name="" id="txtorigin_<?= $i.'_'.$sup_id;?>"  value="<?= $supp_prev_entry_data[$row["PROD_ID"]][$sup_id]["origin"];?>" class="text_boxes" ></td>
					<? 
				}
			?>
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

//========== Statment ========
if($action=="load_statment_table")
{
	list($basis, $req_mst, $req_dtls, $supplier_id) = explode('**', $data);
	// $supplier=explode(',', $supplier_id);
	// $supplier_count=count($supplier);
	// $tbl_width=800+($supplier_count*500);
	$supplier_arr=return_library_array( "select supplier_name,id from lib_supplier where status_active=1 and is_deleted=0 order by supplier_name",'id','supplier_name');
	// $country_arr = return_library_array("select id,country_name from lib_country where is_deleted=0","id","country_name");
	if($basis==1)
	{
		if($db_type==0)
		{
		// sum(a.quantity) as REQ_QTY, group_concat( a.brand_name) as BRAND_NAME, group_concat( a.model) as MODEL ,group_concat( a.origin) as ORIGIN, b.order_uom as ORDER_UOM,
		$sql = "select group_concat( b.id) as PROD_IDS, b.item_category_id as ITEM_CATEGORY_ID, b.item_description as ITEM_DESCRIPTION,b.item_size  as ITEM_SIZE,c.item_name as ITEM_NAME,b.item_code as ITEM_CODE 
		from inv_purchase_requisition_dtls a,product_details_master b, lib_item_group c 
		where a.product_id=b.id and b.item_group_id=c.id and a.id in($req_dtls) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
		group by b.order_uom,b.item_category_id, b.item_description,b.item_size,c.item_name, b.item_code";
		}
		else
		{
		// listagg(cast(a.brand_name as varchar(4000)),',') within group(order by a.brand_name) as BRAND_NAME ,listagg(cast(a.model as varchar(4000)),',') within group(order by a.model) as MODEL ,listagg(cast(a.origin as varchar(4000)),',') within group(order by a.origin) as ORIGIN ,sum(a.quantity) as REQ_QTY,
		$sql = "select listagg(cast(b.id as varchar(4000)),',') within group(order by b.id) as PROD_IDS ,b.order_uom as ORDER_UOM,b.item_category_id as ITEM_CATEGORY_ID, b.item_description as ITEM_DESCRIPTION,b.item_size as ITEM_SIZE,c.item_name as ITEM_NAME,b.item_code as ITEM_CODE 
		from inv_purchase_requisition_dtls a,product_details_master b, lib_item_group c 
		where a.product_id=b.id and b.item_group_id=c.id and a.id in($req_dtls) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
		group by a.brand_name,a.model,a.origin, b.order_uom,b.item_category_id, b.item_description,b.item_size,c.item_name, b.item_code";
		}		
	}
	if($basis==2)
	{
		if($db_type==0)
		{
		$sql = "select group_concat( a.id) as PROD_IDS ,a.item_category_id as ITEM_CATEGORY_ID,a.brand_name as BRAND_NAME,a.model as MODEL,a.origin as ORIGIN,a.order_uom as ORDER_UOM, a.item_description as ITEM_DESCRIPTION,a.item_size as ITEM_SIZE,a.item_code as ITEM_CODE,b.item_name as ITEM_NAME 
		from product_details_master a, lib_item_group b 
		where a.item_group_id=b.id and a.id in($req_mst) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.item_category_id,a.brand_name,a.model,a.origin,a.order_uom, a.item_description,a.item_size,a.item_code,b.item_name";
		}
		else
		{
		$sql = "select listagg(cast(a.id as varchar(4000)),',') within group(order by a.id) as PROD_IDS ,a.item_category_id as ITEM_CATEGORY_ID,a.brand_name as BRAND_NAME,a.model as MODEL,a.origin as ,a.order_uom as ORDER_UOM, a.item_description as ITEM_DESCRIPTION,a.item_size as ITEM_SIZE,a.item_code as ITEM_CODE,b.item_name as ITEM_NAME 
		from product_details_master a, lib_item_group b 
		where a.item_group_id=b.id and a.id in($req_mst) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
		group by a.item_category_id,a.brand_name,a.model,a.origin,a.order_uom, a.item_description,a.item_size,a.item_code,b.item_name";
		}
	}
	// echo $sql;die;
	$data_array=sql_select($sql);
	?>

	<table width="500" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all">
		<thead>
			<tr>
				<th width="40">SL</th>
				<th width="150">Item Description</th>
				<th width="80">Last CS Rate</th>
				<th width="80">Price Validity Date</th>
				<th width="150">Last Supplier Name</th>
			</tr>
		</thead>
		<tbody>
		<?
		$i=1;
		foreach($data_array as $row)
		{
			if ($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
			?>
				<tr bgcolor="<?= $bgcolor; ?>">
				<td align="center" ><?= $i;?>
				<td><? if(!empty($row['ITEM_SIZE'])){echo $row['ITEM_DESCRIPTION'].", ".$row['ITEM_SIZE'];}else{echo $row['ITEM_DESCRIPTION'];} ?></td>
				<td><?  ?></td>
				<td><?  ?></td>
				<td><?  ?></td>
				</tr>
			<?
			$i++;
		}
		?>
		</tbody>
	</table>

	<?
	exit();
}

//========== start Requisition No/Item ========
if($action=="requisition_popup")
{
    echo load_html_head_contents("Requisition Popup", "../../../", 1, 1,$unicode,'1','');
    extract($_REQUEST);
	$txt_requisition_mst=str_replace("'","",$txt_requisition_mst);
	$txt_requisition_dtls=str_replace("'","",$txt_requisition_dtls);
	$update_id=str_replace("'","",$update_id);
    $userCredential = sql_select("SELECT item_cate_id FROM user_passwd where id=$user_id");
    $item_cate_id = $userCredential[0][csf('item_cate_id')];
    if($item_cate_id !='') {
        $item_cate_credential_cond = $item_cate_id ;
    }
    else
    {
        $cre_cat_arr=array_keys($general_item_category);
        array_push($cre_cat_arr, 5, 6, 7, 23  );
        $item_cate_credential_cond = implode(",",$cre_cat_arr);
    }
    ?>
    <script>
		var permission='<? echo $permission; ?>';
		function set_all_old_data()
		{
			var old=document.getElementById('old_data_row_color').value;
			if(old!="")
			{ 
				old=old.split(",");
				for(var i=0; i<old.length; i++)
				{
					js_set_value( old[i] ) ;
				}
			}
		}
		
    	function fn_show_list()
		{
			var txt_req_no=$("#txt_req_no").val();
			var cbo_item_category=trim($("#cbo_item_category").val());
			//alert(cbo_item_category);
			if(txt_req_no=="" && cbo_item_category==0)
			{
				if(form_validation('txt_date_from*txt_date_to','From Date*To Date')==false )
				{
					return;
				}
			}
			
            show_list_view ( document.getElementById('cbo_item_category').value+'**'+document.getElementById('txt_req_no').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+document.getElementById('cbo_year_selection').value+'**'+'<? echo $txt_requisition_mst; ?>'+'**'+'<? echo $txt_requisition_dtls; ?>'+'**'+'<? echo $update_id; ?>','requisition_list_view', 'search_div', 'comparative_statement_controller', 'setFilterGrid(\'list_view\',-1)');
			setFilterGrid('tbl_list_view_req',-1);
			set_all_old_data();
        };

		var selected_no = new Array();var selected_id = new Array(); var selected_dtls = new Array();
		function check_all_data() 
		 {
			var tbl_row_count = document.getElementById( 'tbl_list_view_req' ).rows.length; 
			tbl_row_count = tbl_row_count ;
			for( var i = 1; i <= tbl_row_count; i++ ) 
			{
				if($("#search"+i).is(':visible'))
				{
					js_set_value( i );
				}
			}
		}
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		// function set_all()
		// {
		// 	var old=document.getElementById('txt_req_row_id').value; 
		// 	if(old!="")
		// 	{   
		// 		old=old.split(",");
		// 		for(var k=0; k<old.length; k++)
		// 		{   
		// 			js_set_value( old[k] ) 
		// 		} 
		// 	}
		// }
		function js_set_value( str ) 
		{
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_dtls' + str).val(), selected_dtls ) == -1 ) {
				selected_no.push( $('#txt_mst_no' + str).val() );
				selected_id.push( $('#txt_mst_id' + str).val() );
				selected_dtls.push( $('#txt_dtls' + str).val() );
			}
			else {
				for( var i = 0; i < selected_dtls.length; i++ ) {
					if( selected_dtls[i] == $('#txt_dtls' + str).val() ) break;
				}
				selected_no.splice( i, 1 );
				selected_id.splice( i, 1 );
				selected_dtls.splice( i, 1 );
			}
			var req_no = '';var req_id = ''; var req_dtls_id = '';
			for( var i = 0; i < selected_dtls.length; i++ ) {
				req_no += selected_no[i] + ',';
				req_id += selected_id[i] + ',';
				req_dtls_id += selected_dtls[i] + ',';
			}
			req_no = req_no.substr( 0, req_no.length - 1 );
			req_id = req_id.substr( 0, req_id.length - 1 );
			req_dtls_id = req_dtls_id.substr( 0, req_dtls_id.length - 1 );
			$('#hidden_req_no').val(req_no);
			$('#hidden_req_id').val(req_id);
			$('#hidden_req_dtls_id').val(req_dtls_id);
		}
		
		function req_reset()
		{
			reset_form('searchexportinformationfrm','search_div','cbo_item_category','','');
			//alert($("#cbo_item_category").val());
		}
    </script>
	</head>
        <body>
            <div align="center" style="width:1110px;">
                <form name="searchexportinformationfrm"  id="searchexportinformationfrm">
                <input type="hidden" name="hidden_req_no" id="hidden_req_no" class="text_boxes" value="">
                <input type="hidden" name="hidden_req_id" id="hidden_req_id" class="text_boxes" value="">
                <input type="hidden" name="hidden_req_dtls_id" id="hidden_req_dtls_id" class="text_boxes" value="">
                    <fieldset style="width:1110px;">
                        <table cellpadding="0" cellspacing="0" width="800" class="rpt_table" border="1" rules="all" align="center">
                            <thead>
                                <tr>
                                    <th class="must_entry_caption" >Item Category</th>
                                    <th>Requisition No</th>
                                    <th class="must_entry_caption" >Date Range</th>
                                    <th>
                                        <input type="button" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" onClick="req_reset()" />
                                    </th>
                                </tr>
                            </thead>
                            <tr class="general">
                                <td>
                                    <? 
                                    echo create_drop_down( "cbo_item_category", 150,"select category_id, short_name from  lib_item_category_list where status_active=1 and is_deleted=0 and category_id in ($item_cate_credential_cond) order by short_name","category_id,short_name", 1, "-- Select --", $selected, "",'',"","","","");
                                    ?>
                                </td>
                                <td >
                                <input type="text" name="txt_req_no" id="txt_req_no" class="text_boxes" style="width:140px;" />
                                </td>
                                <td>
                                    <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px;" placeholder="From Date"/>To
                                    <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px;" placeholder="To Date"/>
                                </td>
                                <td>
                                    <input type="button" id="search_button" class="formbutton" value="Show" onClick="fn_show_list();" style="width:100px;" />
                                </td>
                            </tr> 
                            <tr>
                                <td align="center" colspan="10"><? echo load_month_buttons(1); ?></td>
                            </tr>
                        </table>
                        <div style="width:100%; margin-top:10px" id="search_div" align="left"></div>
                        
                    </fieldset>
                </form>
            </div>
        </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>set_multiselect('cbo_item_category','0','0','','0');</script>
	<!-- <script>
	set_all();
	</script> -->
    </html>
    <?
    exit();
}

if($action==='requisition_list_view')
{
	list($category_id, $req_no, $req_start_date, $req_end_date, $year, $txt_requisition_mst, $txt_requisition_dtls, $update_id) = explode('**', $data);
	//echo $req_no;die;
	$requisition_dtlsArr = explode(",",$txt_requisition_dtls);
	$lib_company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$country_arr = return_library_array("select id,country_name from lib_country where is_deleted=0","id","country_name");
	$category_cond ='';$req_num ='';$year_cond="";
	if($category_id !=0) {$category_cond = "and b.item_category in ($category_id)";}
	if($req_no !='') {$req_num = "and a.requ_prefix_num in ($req_no)";}

	if ($req_start_date != '' && $req_end_date != '') 
	{
        if ($db_type == 0) {
            $date_cond = "and a.requisition_date between '" . change_date_format($req_start_date, 'yyyy-mm-dd') . "' and '" . change_date_format($req_end_date, 'yyyy-mm-dd') . "'";
        } else if ($db_type == 2) {
            $date_cond = "and a.requisition_date between '" . change_date_format($req_start_date, '', '', 1) . "' and '" . change_date_format($req_end_date, '', '', 1) . "'";
        }
    } 
    else 
    {
        $date_cond = '';
		if($year!=""){
			if($db_type==0)
			{
				$year_cond=" and YEAR(a.requisition_date) =$year ";
			}
			else
			{	
				$year_cond=" and to_char(a.requisition_date,'YYYY') =$year ";
			}
		}
    }
	$mst_cond="";
	if($update_id) $mst_cond=" and id<>$update_id ";
	$duplicat_sql="select req_item_dtls_id from req_comparative_mst where basis_id=1 and status_active=1 and is_deleted=0 $mst_cond";
	//echo $duplicat_sql."<br>";
	$duplicat_data=sql_select($duplicat_sql);
	$req_dtls_id_arr=array();
	foreach($duplicat_data as $value)
	{
		$req_id_arr=explode(",",$value[csf('req_item_dtls_id')]);
		foreach($req_id_arr as $req_id)
		{
			$req_dtls_id_arr[$req_id]=$req_id;
		}
	}
	unset($duplicat_data);
	
	$sql = "select a.id, a.requ_prefix_num, a.requisition_date, a.company_id, b.id as dtls_id, b.item_category, c.item_description, c.item_size, d.item_name, c.item_group_id, c.item_code, e.short_name as category_name, b.brand_name, b.model, b.origin
	from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c, lib_item_group d, lib_item_category_list e 
	where a.id=b.mst_id and b.product_id=c.id and c.item_group_id=d.id and b.item_category=e.category_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $category_cond $req_num $date_cond $year_cond 
	order by a.id desc";
	//echo $sql;//die;
	unset($req_dtls_id);
	$data_array=sql_select($sql);		
	?>
	<table width="1090" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all">
        <thead>
            <th width="40">SL No</th>
            <th width="60">Requisition No</th>
            <th width="60">Company</th>
            <th width="70">Requisition Date</th>
            <th width="150">Item Category</th>
            <th width="100">Item Group</th>
            <th width="100">Item Code</th>
            <th width="150">Item Description</th>
            <th width="80">Item Size</th>
            <th width="80">Brand</th>
            <th width="80">Model</th>
            <th>Origin</th>
        </thead>
     </table>
     <div style="width:1110px; overflow-y:scroll; max-height:280px">
     	<table width="1090" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" id="tbl_list_view_req">
		<?			
            $i = 1;$oldDataRow="";
            foreach($data_array as $row)
            {
				if( in_array($row[csf('dtls_id')], $requisition_dtlsArr) )
				{
					if($oldDataRow=="") $oldDataRow = $i; else $oldDataRow .= ",".$i;
				}
				
				//if($req_dtls_id_arr[$row[csf('dtls_id')]]=="")
				//{
					if ($i%2==0)
						$bgcolor="#FFFFFF";
					else
						$bgcolor="#E9F3FF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer;" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)" >  
						<td width="40" align="center"><? echo $i; ?>
						<input type="hidden" name="txt_mst_no" id="txt_mst_no<?php echo $i ?>" value="<? echo $row[csf('requ_prefix_num')]; ?>"/>
						<input type="hidden" name="txt_mst_id" id="txt_mst_id<?php echo $i ?>" value="<? echo $row[csf('id')]; ?>"/>	
						<input type="hidden" name="txt_dtls" id="txt_dtls<?php echo $i ?>" value="<? echo $row[csf('dtls_id')]; ?>"/>
						</td>
						<td width="60" align="center"><p><? echo $row[csf('requ_prefix_num')]; ?></p></td>
						<td width="60"><p><? echo $lib_company_arr[$row[csf('company_id')]]; ?></p></td>
						<td width="70" align="center"><p><? echo change_date_format($row[csf('requisition_date')]); ?></p></td>
						<td width="150"><p><? echo $row[csf('category_name')]; ?></p></td>
						<td width="100"><p><? echo $item_group_arr[$row[csf('item_group_id')]]; ?></td>
						<td width="100" align="center"><p><? echo $row[csf('item_code')]; ?></p></td>
						<td width="150"><p><? echo $row[csf('item_description')];; ?></p></td>
						<td align="center" width="80"><p><? echo $row[csf('item_size')];?></p></td>
						<td width="80"><p><? echo $row[csf('brand_name')]; ?></td>
						<td width="80"><p><? echo $row[csf('model')]; ?></td>
						<td><p><? echo $country_arr[$row[csf('origin')]]; ?></p></td>
					</tr>
					<?
					$i++;
				//}
            }
			?>
		</table>
    </div>
		<table width="1090" cellspacing="0" cellpadding="0" style="border:none" align="center">
			<tr>
				<td align="center" height="30" valign="bottom">
					<div style="width:100%"> 
						<div style="width:50%; float:left" align="left">
							<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
						</div>
						<div style="width:50%; float:left" align="left">
							<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                            <input type="hidden" name="old_data_row_color" id="old_data_row_color" value="<? echo $oldDataRow; ?>"/>
						</div>
					</div>
				</td>
			</tr>
		</table>
	</body>           
	</html>
	<?
	exit();
}

if($action == "load_drop_down_group")
{
	echo create_drop_down( "cbo_item_group", 130,"select item_name,id from lib_item_group where item_category in ($data) and status_active= 1 and is_deleted= 0 order by item_name","id,item_name", 1, "-- Select --", $selected, "" );
	// die;
}

if($action=="item_popup")
{
    echo load_html_head_contents("Requisition Popup", "../../../", 1, 1,$unicode,'1','');
    extract($_REQUEST);
    $userCredential = sql_select("SELECT item_cate_id FROM user_passwd where id=$user_id");
    $item_cate_id = $userCredential[0][csf('item_cate_id')];
    if($item_cate_id !='') {
        $item_cate_credential_cond = $item_cate_id ;
    }
    else
    {
        $cre_cat_arr=array_keys($general_item_category);
        array_push($cre_cat_arr, 5, 6, 7, 23  );
        $item_cate_credential_cond = implode(",",$cre_cat_arr);
    }
    ?>
    <script>
		var permission='<? echo $permission; ?>';
    	function fn_show_list(){
        if(form_validation('cbo_item_category','Item Category')==false){
				document.getElementById('search_div').innerHTML="Please Select Category First";
                return;
            }
            show_list_view ( document.getElementById('cbo_item_category').value+'**'+document.getElementById('cbo_item_group').value+'**'+document.getElementById('txt_code').value+'**'+'<? echo $txt_requisition_mst; ?>'+'**'+'<? echo $txt_requisition_dtls; ?>'+'**'+'<? echo $update_id; ?>','item_list_view', 'search_div', 'comparative_statement_controller', 'setFilterGrid(\'list_view\',-1)');
			setFilterGrid('tbl_list_view_req',-1);
        }

		var selected_no = new Array();var selected_id = new Array(); var selected_dtls = new Array();
		function check_all_data() 
		 {
			var tbl_row_count = document.getElementById( 'tbl_list_view_req' ).rows.length; 
			tbl_row_count = tbl_row_count ;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		// function set_all()
		// {
		// 	var old=document.getElementById('txt_req_row_id').value; 
		// 	if(old!="")
		// 	{   
		// 		old=old.split(",");
		// 		for(var k=0; k<old.length; k++)
		// 		{   
		// 			js_set_value( old[k] ) 
		// 		} 
		// 	}
		// }
		function js_set_value( str ) 
		{
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_mst_id' + str).val(), selected_id ) == -1 ) {
				selected_no.push( $('#txt_mst_no' + str).val() );
				selected_id.push( $('#txt_mst_id' + str).val() );
				selected_dtls.push( $('#txt_dtls' + str).val() );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_mst_id' + str).val() ) break;
				}
				selected_no.splice( i, 1 );
				selected_id.splice( i, 1 );
				selected_dtls.splice( i, 1 );
			}
			var item_no = '';var item_id = ''; var item_dtls_id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				item_no += selected_no[i] + ',';
				item_id += selected_id[i] + ',';
				item_dtls_id += selected_dtls[i] + ',';
			}
			item_no = item_no.substr( 0, item_no.length - 1 );
			item_id = item_id.substr( 0, item_id.length - 1 );
			item_dtls_id = item_dtls_id.substr( 0, item_dtls_id.length - 1 );
			$('#hidden_item_no').val(item_no);
			$('#hidden_item_id').val(item_id);
			$('#hidden_item_dtls_id').val(item_dtls_id);
		}
		
		function req_reset()
		{
			reset_form('searchexportinformationfrm','search_div','cbo_item_category','','');
			//alert($("#cbo_item_category").val());
		}
    </script>
	</head>
        <body>
            <div align="center" style="width:900px;">
                <form name="searchexportinformationfrm"  id="searchexportinformationfrm">
                <input type="hidden" name="hidden_item_no" id="hidden_item_no" class="text_boxes" value="">
                <input type="hidden" name="hidden_item_id" id="hidden_item_id" class="text_boxes" value="">
                <input type="hidden" name="hidden_item_dtls_id" id="hidden_item_dtls_id" class="text_boxes" value="">
                    <fieldset style="width:850px;">
                        <table cellpadding="0" cellspacing="0" width="800" class="rpt_table" border="1" rules="all">
                            <thead>
                                <tr>
                                    <th class="must_entry_caption" >Item Category</th>
                                    <th>Item Group</th>
                                    <th >Item Code</th>
                                    <th>
                                        <input type="button" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" onClick="req_reset()" />
                                    </th>
                                </tr>
                            </thead>
                            <tr class="general">
                                <td>
                                    <? 
                                    echo create_drop_down( "cbo_item_category", 150,"select category_id, short_name from  lib_item_category_list where status_active=1 and is_deleted=0 and category_id in ($item_cate_credential_cond) order by short_name","category_id,short_name", 1, "-- Select --", $selected, "",'',"","","","");
                                    ?>
                                </td>
                                <td id="group_td">
									<?	echo create_drop_down("cbo_item_group",130,$blank_array,"",1,"-- Select --",$selected, "" );?>
                                </td>
                                <td >
                                <input type="text" name="txt_code" id="txt_code" class="text_boxes" style="width:140px;" />
                                </td>
                                <td>
                                    <input type="button" id="search_button" class="formbutton" value="Show" onClick="fn_show_list()" style="width:100px;" />
                                </td>
                            </tr> 
                        </table>
                        <div style="width:100%; margin-top:10px" id="search_div" align="left"></div>
                        
                    </fieldset>
                </form>
            </div>
        </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>set_multiselect('cbo_item_category','0','0','','',"load_drop_down('comparative_statement_controller', $('#cbo_item_category').val(), 'load_drop_down_group','group_td')");</script>

	<!-- <script>
	set_all();
	</script> -->
    </html>
    <?
    exit();
}

if($action==='item_list_view')
{
	list($category_id, $group_id, $item_code, $txt_requisition_mst, $txt_requisition_dtls, $update_id) = explode('**', $data);
	
	$category_cond ='';$group_num ='';$item_name ='';
	if($category_id !=0) {$category_cond = "and a.item_category_id in ($category_id)";}
	if($group_id !=0) {$group_num = "and b.id=$group_id";}
	if($item_code !='') {$item_name = "and a.item_code like '%$item_code%'";}

	$sql = "select a.item_category_id, a.item_description, a.item_size, a.order_uom, b.id as item_group_id, b.item_name, a.item_code, c.short_name as category_name,
	listagg(cast(a.id as varchar(4000)),',') within group (order by a.id) as id 
	from product_details_master a, lib_item_group b,lib_item_category_list c 
	where a.item_group_id=b.id and a.item_category_id=c.category_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $category_cond $group_num $item_name
	group by a.item_category_id, a.item_description, a.item_size, a.order_uom, b.id, b.item_name, a.item_code, c.short_name
	order by a.item_description";
	//echo $sql;
	$data_array=sql_select($sql);		
	?>
	<table width="880" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all">
        <thead>
            <th width="40">SL No</th>
            <th width="150">Item Category</th>
            <th width="100">Item Group</th>
            <th width="100">Item Code</th>
            <th width="150">Item Description</th>
            <th width="100">Item Size</th>
            <th width="50">UOM</th>
        </thead>
     </table>
     <div style="width:900px; overflow-y:scroll; max-height:280px">
     	<table width="880" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" id="tbl_list_view_req">
		<?			
            $i = 1;
            foreach($data_array as $row)
            {
                if ($i%2==0)
                    $bgcolor="#FFFFFF";
                else
                    $bgcolor="#E9F3FF";

				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer;" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)" >  
					<td width="40" align="center"><? echo $i; ?>
					<input type="hidden" name="txt_mst_no" id="txt_mst_no<?php echo $i ?>" value="<? echo $row[csf('item_description')]; ?>"/>
					<input type="hidden" name="txt_mst_id" id="txt_mst_id<?php echo $i ?>" value="<? echo $row[csf('id')]; ?>"/>	
					<input type="hidden" name="txt_dtls" id="txt_dtls<?php echo $i ?>" value="<? echo $row[csf('item_group_id')]; ?>"/>
					</td>
                    <td width="150" align="center"><p><? echo $row[csf('category_name')]; ?></p></td>
					<td width="100" align="center"><p><? echo $row[csf('item_name')]; ?></td>
                    <td width="100" align="center"><p><? echo $row[csf('item_code')];; ?></p></td>
                    <td width="150" align="center"><p><? echo $row[csf('item_description')];; ?></p></td>
					<td width="100" align="center"><p><? echo $row[csf('item_size')];;?></p></td>
					<td width="50" align="center"><p><? echo $unit_of_measurement[$row[csf('order_uom')]];;?></p></td>
				</tr>
            <?
			$i++;
            }
			?>
		</table>
    </div>
		<table width="350" cellspacing="0" cellpadding="0" style="border:none" align="center">
			<tr>
				<td align="center" height="30" valign="bottom">
					<div style="width:100%"> 
						<div style="width:50%; float:left" align="left">
							<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
						</div>
						<div style="width:50%; float:left" align="left">
							<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
						</div>
					</div>
				</td>
			</tr>
		</table>
	</body>           
	</html>
	<?
	exit();
}
//========== End Requisition No/Item ========

if($action=="supplier_name_popup")
{
	echo load_html_head_contents("Supplier Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $party_type_id;
	?>
	<script>
		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1);
		});
		var selected_id = new Array(); var selected_name = new Array();
		function check_all_data() 
		 {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length; 

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		function set_all()
		{
			var old=document.getElementById('txt_supplier_row_id').value; 
			if(old!="")
			{   
				old=old.split(",");
				for(var k=0; k<old.length; k++)
				{   
					js_set_value( old[k] ) 
				} 
			}
		}
		function js_set_value( str ) 
		{
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			$('#hidden_supplier_id').val(id);
			$('#hidden_supplier_name').val(name);
		}
    </script>
	</head>
	<body>
		<div align="center">
			<fieldset style="width:370px;margin-left:10px">
		    	<input type="hidden" name="hidden_supplier_id" id="hidden_supplier_id" class="text_boxes" value="">
		        <input type="hidden" name="hidden_supplier_name" id="hidden_supplier_name" class="text_boxes" value="">
		        <form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
		            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table" >
		                <thead>
		                    <th width="50">SL</th>
		                    <th>Supplier Name</th>
		                </thead>
		            </table>
		            <div style="width:350px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
		                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="332" class="rpt_table" id="tbl_list_search" >
		                <?
                            $data_sql=sql_select("select c.supplier_name,c.id from lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and b.party_type in(3,7) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name");
                            // var_dump($data_sql);die;
		                    $i=1; $supplier_row_id=''; 
							$hidden_supplier_id=explode(",",$supplier_id);
		                    foreach($data_sql as $row)
		                    {
								
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$id=$row[('ID')];
								if(in_array($id,$hidden_supplier_id)) 
								{ 
									if($supplier_row_id=="") $supplier_row_id=$i; else $supplier_row_id.=",".$i;
								}
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)"> 
									<td width="50" align="center"><?php echo "$i"; ?>
										<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $row[('ID')]; ?>"/>	
										<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $row[('SUPPLIER_NAME')]; ?>"/>
									</td>	
									<td><p><? echo $row[('SUPPLIER_NAME')]; ?></p></td>
								</tr>
								<?
								$i++;

		                    }
		                ?>
		                <input type="hidden" name="txt_supplier_row_id" id="txt_supplier_row_id" value="<?php echo $supplier_row_id; ?>"/>
		                </table>
		            </div>
		             <table width="350" cellspacing="0" cellpadding="0" style="border:none" align="center">
		                <tr>
		                    <td align="center" height="30" valign="bottom">
		                        <div style="width:100%"> 
		                            <div style="width:50%; float:left" align="left">
		                                <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
		                            </div>
		                            <div style="width:50%; float:left" align="left">
		                                <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
		                            </div>
		                        </div>
		                    </td>
		                </tr>
		            </table>
		        </form>
		    </fieldset>
		</div>    
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		set_all();
	</script>
	</html>
	<?
	exit();
}

if ($action=='estimated_price_popup')
{
	echo load_html_head_contents("Estimated Price Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	?>
	<script>
		var selected_str = new Array();
		var selected_dtls_id = new Array();
		var selected_supp_id = new Array();
		var selected_supp_data = new Array();
		function check_spplier_rate(str,id)
		{			
			selected_str=str.split('_');
			var dtls_id=selected_str[0];
			var supp_id=selected_str[1];
			var supp_data=selected_str[1]+'_'+selected_str[2]+'_'+selected_str[3]+'_'+selected_str[0];
			
			if ($("#"+id).is(":checked"))
			{
				if(selected_dtls_id.indexOf(dtls_id) == -1)
				{  
					selected_dtls_id.push(dtls_id);
					selected_supp_id.push(supp_id);
					selected_supp_data.push(supp_data);
				}
				else
				{
					for( var i = 0; i < selected_dtls_id.length; i++ ) {
						if( selected_dtls_id[i] == dtls_id ) {
							alert('Only One Price Selected of One Item');
							$("#"+id).removeAttr('checked');
							break;
						}
					}					
				}
			}
			else
			{
				for( var i = 0; i < selected_dtls_id.length; i++ ) {
					if( selected_dtls_id[i] == dtls_id ) {
						selected_dtls_id.splice( i, 1 );
						selected_supp_id.splice( i, 1 );
						selected_supp_data.splice( i, 1 );
						break;
					}
				}				
			}
			var dtls_ids=selected_dtls_id.join();
			var supp_ids=selected_supp_id.join();
			var supp_datass=selected_supp_data.join();
			$('#hidden_dtlsid').val(dtls_ids);
			$('#hidden_suppid').val(supp_ids);
			$('#hidden_supp_data').val(supp_datass);
		}
    </script>
	</head>
	<body>
		<div align="center">
			<input type="hidden" name="hidden_dtlsid" id="hidden_dtlsid">
	        <input type="hidden" name="hidden_suppid" id="hidden_suppid">
			<input type="hidden" name="hidden_supp_data" id="hidden_supp_data">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="700" class="rpt_table" >
				<thead bgcolor="#dddddd" align="center">
					<tr>
						<th width="30">SL</th>
						<th width="100">Item Category</th>
						<th width="100">Item Group</th>
						<th width="150">Item Description</th>
						<th width="100">Supplier Name</th>							
						<th width="80">Price</th>
						<th width="60">UOM</th>
						<th width="60">Select</th>
					</tr>				
				</thead>
			</table>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="700" class="rpt_table" >
				
					<?
					$sql_supp_dtls="select c.dtls_id as DTLS_ID, c.last_approval_rate as MINIMUM_APPROVAL_RATE, c.supp_id as SUPP_ID
					from req_comparative_supp_dtls c
					where c.mst_id=$mst_id and c.status_active=1 and c.is_deleted=0";
					$sql_supp_dtls_res=sql_select($sql_supp_dtls);
					$rate_supplier_arr=array();
					foreach ($sql_supp_dtls_res as $row)
					{
						$rate_supplier_arr[$row['MINIMUM_APPROVAL_RATE']][$row['DTLS_ID']].=$row['SUPP_ID'].',';
					}
					
					$sql_dtls="select a.order_uom as UOM, a.item_description as ITEM_DESCRIPTION, b.id as DTLS_ID, b.item_category_id as ITEM_CATEGORY_ID, b.item_group_id as ITEM_GROUP_ID, b.req_qty as REQ_QTY, min(c.last_approval_rate) as MINIMUM_APPROVAL_RATE
					from product_details_master a, req_comparative_dtls b, req_comparative_supp_dtls c
					where a.id=b.prod_id and b.id=c.dtls_id and b.mst_id=$mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.order_uom, a.item_description, b.id, b.item_category_id, b.item_group_id, b.req_qty order by b.id asc";
					$sql_dtls_res=sql_select($sql_dtls);
					
					$i=1;					
					foreach($sql_dtls_res as $row)
					{
						$j=1;
						$rowspan="";
						$all_supp_arr=explode(',',rtrim($rate_supplier_arr[$row['MINIMUM_APPROVAL_RATE']][$row['DTLS_ID']],','));
						$rowspan = count($all_supp_arr);

						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>"> 
							<td width="30" rowspan="<? echo $rowspan; ?>"><? echo $i; ?></td>
							<td width="100" rowspan="<? echo $rowspan; ?>"><? echo $item_category[$row['ITEM_CATEGORY_ID']]; ?></td>
							<td width="100" rowspan="<? echo $rowspan; ?>"><? echo $item_group_arr[$row['ITEM_GROUP_ID']]; ?></td>
							<td width="150" rowspan="<? echo $rowspan; ?>"><? echo $row['ITEM_DESCRIPTION']; ?></td>
							<?
							
							$flag = 0;
							$j=1;
							foreach ($all_supp_arr as $supp_val)
							{
								if($flag == 1){ ?><tr bgcolor="<? echo $bgcolor; ?>"><? } ?>
								<td width="100"><? echo $supplier_arr[$supp_val]; ?></td>
								<td width="80" align="right"><? echo $row['MINIMUM_APPROVAL_RATE']; ?></td>
								<td width="60" align="center"><? echo $unit_of_measurement[$row['UOM']]; ?></td>
								<td width="60" align="center"><input type="checkbox" id="txtCheck_<? echo $row['DTLS_ID'].'_'.$supp_val.'_'.$j; ?>" value="<? echo $row['DTLS_ID'].'_'.$supp_val.'_'.$row['MINIMUM_APPROVAL_RATE'].'_'.$row['REQ_QTY']; ?>" onClick="check_spplier_rate(this.value,this.id)"/></td>
								<? if($flag == 1) { ?></tr><? }
								$flag = 1;
								$j++;
							}
							?>
						</tr>
						<?
						$i++;
					}
					?>					
				
			</table>			
		</div>
		<div style="width:100%; margin-top: 10px;" align="center">
			<input type="button" name="close" class="formbutton" onClick="parent.emailwindow.hide();" value="Close" style="width:100px">
		</div>   
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

//========== Print Button ========
if($action=="comparative_statement_print")
{
	extract($_REQUEST);
    $data=explode('*',$data);
    $mst_id=$data[0];
    $template_id=$data[3];
    echo load_html_head_contents($data[1],"../../../", 1, 1, $unicode,'','');
	//   print_r ($data); die;
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$supplier_contact_arr=return_library_array( "select id, contact_no from lib_supplier",'id','contact_no');
	$lib_company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$country_arr = return_library_array("select id,country_name from lib_country where is_deleted=0","id","country_name");
	$data_array=sql_select("select id, sys_number, sys_number_prefix, sys_number_prefix_num, basis_id, req_item_no,req_item_mst_id,req_item_dtls_id, rec_date, cs_date, supp_id, currency_id, cs_valid_date, source, approved, company_id, comments,INSERTED_BY from req_comparative_mst where id='$mst_id' and is_deleted=0 and status_active=1");
	$group_name=return_field_value("group_name","lib_group","is_deleted= 0 order by id desc","group_name");
	$group_address=return_field_value("address","lib_group","is_deleted= 0 order by id desc","address");
	$supp_mult_arr='';
	$basis='';
	foreach ($data_array as $row)
	{ 
		$supp_mult=$row[csf('supp_id')];
		$com_mult=$row[csf('company_id')];
		$basis_id=$row[csf('basis_id')];
		$inserted_by = $row['INSERTED_BY'];
		$supp_mult_arr=explode(',',$supp_mult);
		$supp_nam='';
		foreach($supp_mult_arr as $supp){


			if($supp_nam !='')	{$supp_nam .= ", ".$supplier_arr[$supp];}else{$supp_nam =$supplier_arr[$supp];}
		}
		$com_mult_arr=explode(',',$com_mult);
		$company_nam='';
		if(count($com_mult_arr)==count($lib_company_arr))
		{
			$company_nam .="All Company";
		}
		else
		{
			foreach($com_mult_arr as $value){
				if($company_nam !='')	{$company_nam .= ", ".$lib_company_arr[$value];}else{$company_nam =$lib_company_arr[$value];}
			}
		}
		
		?>
        <table cellspacing="0" width="1000" >
            <tr>
                <td colspan="7" style="font-size:xx-large;" align="center"><strong><? echo $group_name; ?></strong></td>
            </tr>
            <tr>
                <td colspan="7" style="font-size:large;" align="center"><strong><? echo $group_address; ?></strong></td>
            </tr>
            <tr>
                <td align="left" width='150'><strong>Req. No.</strong></td>
                <td align="left" width='10'><strong>:</strong></td>
                <td align="left" width='500'><? echo $row[csf('req_item_no')]; ?></td>
				<td align="left" width='80'><strong>CS No.</strong></td>
                <td align="left" width='10'><strong>:</strong></td>
                <td align="left" width='80'><? echo $row[csf('sys_number')]; ?></td>
				<td align="left"></td>
            </tr>
            <tr>
                <td align="left" valign="top"><strong>Req. Received Date</strong></td>
                <td align="left" valign="top"><strong>:</strong></td>
                <td align="left" ><? echo change_date_format($row[csf('rec_date')]); ?></td>
				<td align="left"><strong>Date</strong></td>
                <td align="left"><strong>:</strong></td>
                <td align="left"><? echo change_date_format($row[csf('cs_date')]); ?></td>
				<td align="left"></td>
            </tr>
            <tr>
                <td align="left" valign="top"><strong>Unit</strong></td>
                <td align="left" valign="top"><strong>:</strong></td>
                <td align="left"><? echo $company_nam; ?></td>
                <td colspan="5" align="left"></td>
            </tr>
		</table>
		<?
	}

	$dtls_sql="select a.id as ID, a.prod_id as PROD_ID, a.item_group_id as ITEM_GROUP_ID, a.req_qty as REQ_QTY, a.brand as BRAND, a.model as MODEL, a.origin as ORIGIN
	from req_comparative_dtls a 
	where a.mst_id='$mst_id' and a.is_deleted=0 and a.status_active=1
	order by a.id asc";
	$dtls_sql_result=sql_select($dtls_sql);
	$selected_prod_id="";
	foreach($dtls_sql_result as $row)
	{
		$selected_prod_id.=$row["PROD_ID"].",";
	}
	$selected_prod_id=chop($selected_prod_id,",");
	$item_des_sql="select b.id as PROD_ID, b.order_uom as ORDER_UOM, b.item_category_id as ITEM_CATEGORY_ID, b.item_description as ITEM_DESCRIPTION, b.item_code as ITEM_CODE, b.item_size as ITEM_SIZE
	from product_details_master b, lib_item_group c 
	where b.id in($selected_prod_id) and b.item_group_id=c.id and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1";
	$item_des_sql_result=sql_select($item_des_sql);
	$item_des_sql_arr=array();	$item_cat_sql_arr=array();
	foreach($item_des_sql_result as $row)
	{
		$item_des_sql_arr[$row["PROD_ID"]]['PROD_ID']=$row["PROD_ID"];
		$item_des_sql_arr[$row["PROD_ID"]]['ITEM_CATEGORY_ID']=$row["ITEM_CATEGORY_ID"];
		$item_des_sql_arr[$row["PROD_ID"]]['ITEM_DESCRIPTION']=$row["ITEM_DESCRIPTION"];
		$item_des_sql_arr[$row["PROD_ID"]]['ITEM_CODE']=$row["ITEM_CODE"];
		$item_des_sql_arr[$row["PROD_ID"]]['ORDER_UOM']=$row["ORDER_UOM"];
		$item_des_sql_arr[$row["PROD_ID"]]['ITEM_SIZE']=$row["ITEM_SIZE"];
		$item_cat_sql_arr[$row["ITEM_CATEGORY_ID"]]['ITEM_CATEGORY_ID']=$row["ITEM_CATEGORY_ID"];
	}
	
	$lastRcvRate_sql = "select prod_id as PROD_ID, cons_rate as CONS_RATE from inv_transaction where prod_id in ($selected_prod_id) and status_active = 1 and is_deleted = 0 and transaction_type in(1) and id=(select max(id) as id from inv_transaction where status_active=1 and transaction_type in(1) and prod_id in($selected_prod_id))";
	$lastRcvRate_data=sql_select($lastRcvRate_sql);
	$lastRcvRateArr = array();
	foreach($lastRcvRate_data as $value)
	{
		$lastRcvRateArr[$value['PROD_ID']] = $value['CONS_RATE'];
	}
	
	$category_nam='';
	foreach($item_cat_sql_arr as $value){
		if(	$category_nam !=''){$category_nam .=', '.$item_category[$value['ITEM_CATEGORY_ID']];}else{$category_nam=$item_category[$value['ITEM_CATEGORY_ID']];}
	}

	$supp_dtls_arr=sql_select("select id, mst_id,dtls_id, supp_id,prod_id, quoted_price, neg_price, con_price, brand,model,origin,pay_term, tenor, incoterm, warranty from req_comparative_supp_dtls where mst_id='$mst_id' and is_deleted=0 and status_active=1 order by id asc");
	$supplier_count=count($supp_mult_arr);
	$tbl_width=550+($supplier_count*270);
	$tbl_col=6+($supplier_count*3);
	$grand_amount=array();
	?>
    <table cellspacing="0" width="1000" >
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td style="font-size:large;" align="center"><strong><? echo "Comparative Statement of Supply of ".$category_nam." Materials"; ?></strong></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
    </table>
        
        
	<table width="<?=$tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
		<thead bgcolor="#dddddd" align="center">
			<tr>
				<th rowspan="3" width="30"  align="center">SL No</th>
				<th rowspan="3" width="150" align="center">Description of Items</th>
				<th rowspan="3" width="130" align="center">Specification</th>
				<th rowspan="3" width="80" align="center">Qty</th>
				<th rowspan="3" width="80" align="center">Unit</th>
				<?
					foreach($supp_mult_arr as $row)
					{
						?>
						<th colspan="6" width="260"><?= $supplier_contact_arr[$row];?></th>
						<?
					}
				?>
				<th rowspan="3" width="80" align="center">Last Price/ Approx. Price</th>
			</tr>
			<tr>
				<?
					foreach($supp_mult_arr as $row)
					{
						?>
						<th colspan="6" width="340"><?= $supplier_arr[$row];?></th>
						<?
					}
				?>
			</tr>
			<tr>
				<?
					foreach($supp_mult_arr as $row)
					{
						?>
						<th width="80">Brand/ Model/ Origin</th>
						<th width="80">Warranty</th>
						<th width="80">Incoterm</th>
						<th width="80">Pay Term/ Tenor</th>
						<th width="80">Quoted Price</th>
						<th width="100">Total Price</th>
						<?
					}
				?>
			</tr>
		</thead>
		<tbody>
			<?
				$i=1;
				foreach($dtls_sql_result as $row)
				{
					$pro_id=explode(",",$row['PROD_ID']);
					$pro_id_one=max($pro_id);
					$lastRcvRate= $lastRcvRateArr[$pro_id_one];
					$pro_id_all=$row['PROD_ID'];
					if ($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
					?>
						<tr bgcolor="<?= $bgcolor; ?>">
							<td align="center"><? echo $i; ?></td>
							<td ><? echo $item_des_sql_arr[$pro_id_one]['ITEM_DESCRIPTION'];  ?></td>
							<td align="center" title="<?= $pro_id_one;?>"><? echo $item_des_sql_arr[$pro_id_one]['ITEM_SIZE']; ?></td>
							<td align="center"><? echo $row['REQ_QTY']; ?></td>
							<td align="center"><? echo $unit_of_measurement[$item_des_sql_arr[$pro_id_one]['ORDER_UOM']]; ?></td>
							<?
							$row_id=$row['ID'];
							foreach($supp_mult_arr as $supp)
							{
								foreach($supp_dtls_arr as $rows)
								{
									$dtls_row_id=$rows[csf('dtls_id')];
									$supp_id=$rows[csf('supp_id')];
									if($row_id==$dtls_row_id){
										$supp_brand_model_origin='';
										if($supp==$supp_id){
											$brand=$rows[csf('brand')];
											$model=$rows[csf('model')];
											$origin=$rows[csf('origin')];
											$payTerm=$rows[csf('pay_term')];
											$tenor=$rows[csf('tenor')];
											$incoterms=$rows[csf('incoterm')];
											$warranty=$rows[csf('warranty')];
											if(empty($brand) && empty($model) && empty($origin)){
												$supp_brand_model_origin="N/A";
											}else{
												if(!empty($brand)){
													$supp_brand_model_origin .=$brand;
												}
												if(!empty($model) && !empty($supp_brand_model_origin)){
													$supp_brand_model_origin .=", ".$model;
												}elseif(!empty($model)){
													$supp_brand_model_origin .=$model;
												}
												if(!empty($origin) && !empty($supp_brand_model_origin)){
													$supp_brand_model_origin .=", ".$origin;
												}elseif(!empty($origin)){
													$supp_brand_model_origin.=$origin;
												}
											}

											if(empty($payTerm) && empty($tenor) ){
												$payterm_tenor="N/A";
											}else{
												$payterm_tenor =$pay_term[$payTerm].", ".$tenor;
											}
											?>
												<td align="center" ><? echo $supp_brand_model_origin;?></td>
												<td align="center" ><? echo $warranty;?></td>
												<td align="center" ><? echo $incoterm[$incoterms];?></td>
												<td align="center" ><? echo $payterm_tenor;?></td>
												<td align="right" ><? echo number_format($rows[csf('quoted_price')],2); ?></td>
												<td align="right" >
												<? 
												$total_amt=$rows[csf('quoted_price')]*$row['REQ_QTY']; $grand_amount[$rows[csf('supp_id')]]+=$total_amt; 
												if($total_amt!=0){echo number_format($total_amt,2);} 
												?>
                                                </td>
											<?
										}
									}
								}
							}
							?>
							<td align="right"><? echo $lastRcvRate; ?></td>
						</tr>
					<?
					$i++;
				}
			?>
			<tr>
				<td colspan='5' align="right"><strong>Total Amount</strong></td>
				<?
				foreach($supp_mult_arr as $supp)
				{
					?>
					<td colspan="6" align="right" ><? echo number_format($grand_amount[$supp],2); ?></td>
					<?
				}
				?>
				<td></td>
			</tr>
		</tbody>
	</table>
	<div>
	Note: The above price including VAT, AIT & delivery charge upto site.
	</div>
	<br>
	<?
	$data_array = sql_select("select id, terms from  wo_booking_terms_condition where booking_no='" . str_replace("'", "", $data[2]) . "' and entry_form=481  order by id");
	?>
	<table width='1200' cellpadding="0" cellspacing="0">
		<tbody>
			<tr>
				<td width='150'style="text-decoration-line: underline;font-size:22px;"><strong>Approval Note:</strong></td>
				<td width='30'></td>
				<td ></td>
			</tr>
			<?
				$i=1;
				if (count($data_array) > 0) {
					foreach ($data_array as $row) {
						?>
						<tr>
							<td></td>
							<td style='font-size:22px;' align="center" valign="top"><strong><?=$i.'.';?></strong></td>
							<td style='font-size:22px;word-break: break-all;'><? echo $row[csf('terms')]; ?></td>
						</tr>
						<?
						$i++;
					}
				}
			?>
		</tbody>
	</table>
	<?


		$user_sign_arr = array();
	  	$appSqlRes =sql_select("select a.USER_ID,b.USER_FULL_NAME from ELECTRONIC_APPROVAL_SETUP a,USER_PASSWD b where b.id = a.user_id and (a.ENTRY_FORM=49 or b.id=$inserted_by)");

		$sig_img_arr=return_library_array( "select MASTER_TBLE_ID,IMAGE_LOCATION from COMMON_PHOTO_LIBRARY where FORM_NAME='user_signature' and MASTER_TBLE_ID in(select a.USER_ID from ELECTRONIC_APPROVAL_SETUP a,USER_PASSWD b where b.id = a.user_id and (a.ENTRY_FORM=49 or b.id=$inserted_by))",'MASTER_TBLE_ID','IMAGE_LOCATION');

		

		foreach($appSqlRes as $appRow){
			$user_sign_arr[$appRow['USER_ID']] = '<img height="25" src="../../'.$sig_img_arr[$appRow['USER_ID']].'" alt=""><br>'.$appRow['USER_FULL_NAME'];
		}

	  	echo get_app_signature(481, 0, $tbl_width, $template_id, 40, $inserted_by,$user_sign_arr,8); 


	exit();
}

//========== Print Button ========
if($action=="comparative_statement_print2")
{
	extract($_REQUEST);
    $data=explode('*',$data);
    $mst_id=$data[0];
    $template_id=$data[3];
    echo load_html_head_contents($data[1],"../../../", 1, 1, $unicode,'','');
	//   print_r ($data); die;
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$supplier_contact_arr=return_library_array( "select id, contact_no from lib_supplier",'id','contact_no');
	$lib_company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$country_arr = return_library_array("select id,country_name from lib_country where is_deleted=0","id","country_name");
	$data_array=sql_select("select id, sys_number, sys_number_prefix, sys_number_prefix_num, basis_id, req_item_no,req_item_mst_id,req_item_dtls_id, rec_date, cs_date, supp_id, currency_id, cs_valid_date, source, approved, company_id, comments,INSERTED_BY from req_comparative_mst where id='$mst_id' and is_deleted=0 and status_active=1");
	$group_name=return_field_value("group_name","lib_group","is_deleted= 0 order by id desc","group_name");
	$group_address=return_field_value("address","lib_group","is_deleted= 0 order by id desc","address");
	$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	$supp_mult_arr='';
	$basis='';
	foreach ($data_array as $row)
	{ 
		$supp_mult=$row[csf('supp_id')];
		$com_mult=$row[csf('company_id')];
		$basis_id=$row[csf('basis_id')];
		$inserted_by = $row['INSERTED_BY'];
		$supp_mult_arr=explode(',',$supp_mult);
		$supp_nam='';
		foreach($supp_mult_arr as $supp){


			if($supp_nam !='')	{$supp_nam .= ", ".$supplier_arr[$supp];}else{$supp_nam =$supplier_arr[$supp];}
		}
		$com_mult_arr=explode(',',$com_mult);
		$company_nam='';
		if(count($com_mult_arr)==count($lib_company_arr))
		{
			$company_nam .="All Company";
		}
		else
		{
			foreach($com_mult_arr as $value){
				if($company_nam !='')	{$company_nam .= ", ".$lib_company_arr[$value];}else{$company_nam =$lib_company_arr[$value];}
			}
		}
		
		?>
        <table cellspacing="0" width="1000" >
            <tr>
                <td colspan="7" style="font-size:xx-large;" align="center"><strong><? echo $group_name; ?></strong></td>
            </tr>
            <tr>
                <td colspan="7" style="font-size:large;" align="center"><strong><? echo $group_address; ?></strong></td>
            </tr>
            <tr>
                <td align="left" width='150'><strong>Req. No.</strong></td>
                <td align="left" width='10'><strong>:</strong></td>
                <td align="left" width='500'><? echo $row[csf('req_item_no')]; ?></td>
				<td align="left" width='80'><strong>CS No.</strong></td>
                <td align="left" width='10'><strong>:</strong></td>
                <td align="left" width='80'><? echo $row[csf('sys_number')]; ?></td>
				<td align="left"></td>
            </tr>
            <tr>
                <td align="left" valign="top"><strong>Req. Received Date</strong></td>
                <td align="left" valign="top"><strong>:</strong></td>
                <td align="left" ><? echo change_date_format($row[csf('rec_date')]); ?></td>
				<td align="left"><strong>Date</strong></td>
                <td align="left"><strong>:</strong></td>
                <td align="left"><? echo change_date_format($row[csf('cs_date')]); ?></td>
				<td align="left"></td>
            </tr>
            <tr>
                <td align="left" valign="top"><strong>Unit</strong></td>
                <td align="left" valign="top"><strong>:</strong></td>
                <td align="left"><? echo $company_nam; ?></td>
                <td colspan="5" align="left"></td>
            </tr>
		</table>
		<?
	}

	$dtls_sql="select a.id as ID, a.prod_id as PROD_ID, a.item_group_id as ITEM_GROUP_ID, a.req_qty as REQ_QTY, a.brand as BRAND, a.model as MODEL, a.origin as ORIGIN,a.ITEM_CATEGORY_ID
	from req_comparative_dtls a 
	where a.mst_id='$mst_id' and a.is_deleted=0 and a.status_active=1
	order by a.id asc";
	$dtls_sql_result=sql_select($dtls_sql);
	$selected_prod_id="";
	foreach($dtls_sql_result as $row)
	{
		$selected_prod_id.=$row["PROD_ID"].",";
	}
	$selected_prod_id=chop($selected_prod_id,",");
	$item_des_sql="select b.id as PROD_ID, b.order_uom as ORDER_UOM, b.item_category_id as ITEM_CATEGORY_ID, b.item_description as ITEM_DESCRIPTION, b.item_code as ITEM_CODE, b.item_size as ITEM_SIZE
	from product_details_master b, lib_item_group c 
	where b.id in($selected_prod_id) and b.item_group_id=c.id and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1";
	$item_des_sql_result=sql_select($item_des_sql);
	$item_des_sql_arr=array();	$item_cat_sql_arr=array();
	foreach($item_des_sql_result as $row)
	{
		$item_des_sql_arr[$row["PROD_ID"]]['PROD_ID']=$row["PROD_ID"];
		$item_des_sql_arr[$row["PROD_ID"]]['ITEM_CATEGORY_ID']=$row["ITEM_CATEGORY_ID"];
		$item_des_sql_arr[$row["PROD_ID"]]['ITEM_DESCRIPTION']=$row["ITEM_DESCRIPTION"];
		$item_des_sql_arr[$row["PROD_ID"]]['ITEM_CODE']=$row["ITEM_CODE"];
		$item_des_sql_arr[$row["PROD_ID"]]['ORDER_UOM']=$row["ORDER_UOM"];
		$item_des_sql_arr[$row["PROD_ID"]]['ITEM_SIZE']=$row["ITEM_SIZE"];
		$item_cat_sql_arr[$row["ITEM_CATEGORY_ID"]]['ITEM_CATEGORY_ID']=$row["ITEM_CATEGORY_ID"];
	}
	
	$lastRcvRate_sql = "select prod_id as PROD_ID, cons_rate as CONS_RATE from inv_transaction where prod_id in ($selected_prod_id) and status_active = 1 and is_deleted = 0 and transaction_type in(1) and id=(select max(id) as id from inv_transaction where status_active=1 and transaction_type in(1) and prod_id in($selected_prod_id))";
	$lastRcvRate_data=sql_select($lastRcvRate_sql);
	$lastRcvRateArr = array();
	foreach($lastRcvRate_data as $value)
	{
		$lastRcvRateArr[$value['PROD_ID']] = $value['CONS_RATE'];
	}
	
	$category_nam='';
	foreach($item_cat_sql_arr as $value){
		if(	$category_nam !=''){$category_nam .=', '.$item_category[$value['ITEM_CATEGORY_ID']];}else{$category_nam=$item_category[$value['ITEM_CATEGORY_ID']];}
	}

	$supp_dtls_arr=sql_select("select id, mst_id,dtls_id, supp_id,prod_id, quoted_price, neg_price, con_price, brand,model,origin,pay_term, tenor, incoterm, warranty,specification from req_comparative_supp_dtls where mst_id='$mst_id' and is_deleted=0 and status_active=1 order by id asc");
	$supplier_count=count($supp_mult_arr);
	$tbl_width=790+($supplier_count*270);
	$tbl_col=6+($supplier_count*3);
	$grand_amount=array();
	?>
    <table cellspacing="0" width="1000" >
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td style="font-size:large;" align="center"><strong><? echo "Comparative Statement of Supply of ".$category_nam." Materials"; ?></strong></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
    </table>
        
        
	<table width="<?=$tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
		<thead bgcolor="#dddddd" align="center">
			<tr>
				<th rowspan="3" width="30"  align="center">SL No</th>
				<th rowspan="3" width="120"  align="center">Item Category</th>
				<th rowspan="3" width="120"  align="center">Items Group</th>
				<th rowspan="3" width="150" align="center">Description of Items</th>
				<th rowspan="3" width="80" align="center">Qty</th>
				<th rowspan="3" width="80" align="center">Unit</th>
				<?
					foreach($supp_mult_arr as $row)
					{
						?>
						<th colspan="7" width="260"><?= $supplier_contact_arr[$row];?></th>
						<?
					}
				?>
				<th rowspan="3" width="80" align="center">Last Price/ Approx. Price</th>
			</tr>
			<tr>
				<?
					foreach($supp_mult_arr as $row)
					{
						?>
						<th colspan="7" width="340"><?= $supplier_arr[$row];?></th>
						<?
					}
				?>
			</tr>
			<tr>
				<?
					foreach($supp_mult_arr as $row)
					{
						?>
						<th width="80">Specification</th>
						<th width="80">Brand/ Model/ Origin</th>
						<th width="80">Warranty</th>
						<th width="80">Incoterm</th>
						<th width="80">Pay Term/ Tenor</th>
						<th width="80">Quoted Price</th>
						<th width="80">Confirm Price</th>
						<th width="100">Total Price</th>
						<?
					}
				?>
			</tr>
		</thead>
		<tbody>
			<?
				$i=1;
				foreach($dtls_sql_result as $row)
				{
					$pro_id=explode(",",$row['PROD_ID']);
					$pro_id_one=max($pro_id);
					$lastRcvRate= $lastRcvRateArr[$pro_id_one];
					$pro_id_all=$row['PROD_ID'];
					if ($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
					?>
						<tr bgcolor="<?= $bgcolor; ?>">
							<td align="center"><? echo $i; ?></td>
							<td align="center"><? echo $item_category[$row['ITEM_CATEGORY_ID']]; ?></td>
							<td align="center"><? echo $item_group_arr[$row['ITEM_GROUP_ID']]; ?></td>
							<td ><? echo $item_des_sql_arr[$pro_id_one]['ITEM_DESCRIPTION'];  ?></td>
							<td align="center"><? echo $row['REQ_QTY']; ?></td>
							<td align="center"><? echo $unit_of_measurement[$item_des_sql_arr[$pro_id_one]['ORDER_UOM']]; ?></td>
							<?
							$row_id=$row['ID'];
							foreach($supp_mult_arr as $supp)
							{
								foreach($supp_dtls_arr as $rows)
								{
									$dtls_row_id=$rows[csf('dtls_id')];
									$supp_id=$rows[csf('supp_id')];
									if($row_id==$dtls_row_id){
										$supp_brand_model_origin='';
										if($supp==$supp_id){
											$brand=$rows[csf('brand')];
											$model=$rows[csf('model')];
											$origin=$rows[csf('origin')];
											$payTerm=$rows[csf('pay_term')];
											$tenor=$rows[csf('tenor')];
											$incoterms=$rows[csf('incoterm')];
											$warranty=$rows[csf('warranty')];
											$specification=$rows[csf('specification')];
											if(empty($brand) && empty($model) && empty($origin)){
												$supp_brand_model_origin="N/A";
											}else{
												if(!empty($brand)){
													$supp_brand_model_origin .=$brand;
												}
												if(!empty($model) && !empty($supp_brand_model_origin)){
													$supp_brand_model_origin .=", ".$model;
												}elseif(!empty($model)){
													$supp_brand_model_origin .=$model;
												}
												if(!empty($origin) && !empty($supp_brand_model_origin)){
													$supp_brand_model_origin .=", ".$origin;
												}elseif(!empty($origin)){
													$supp_brand_model_origin.=$origin;
												}
											}

											if(empty($payTerm) && empty($tenor) ){
												$payterm_tenor="N/A";
											}else{
												$payterm_tenor =$pay_term[$payTerm].", ".$tenor;
											}
											?>
												<td align="center" ><? echo $specification;?></td>
												<td align="center" ><? echo $supp_brand_model_origin;?></td>
												<td align="center" ><? echo $warranty;?></td>
												<td align="center" ><? echo $incoterm[$incoterms];?></td>
												<td align="center" ><? echo $payterm_tenor;?></td> 
												<td align="right" ><? echo number_format($rows[csf('quoted_price')],2); ?></td>
												<td align="right" ><? echo number_format($rows[csf('con_price')],2); ?></td>
												<td align="right" >
												<? 
												$total_amt=$rows[csf('quoted_price')]*$row['REQ_QTY']; $grand_amount[$rows[csf('supp_id')]]+=$total_amt; 
												if($total_amt!=0){echo number_format($total_amt,2);} 
												?>
                                                </td>
											<?
										}
									}
								}
							}
							?>
							<td align="right"><? echo $lastRcvRate; ?></td>
						</tr>
					<?
					$i++;
				}
			?>
			<tr>
				<td colspan='6' align="right"><strong>Total Amount</strong></td>
				<?
				foreach($supp_mult_arr as $supp)
				{
					?>
					<td colspan="7" align="right" ><? echo number_format($grand_amount[$supp],2); ?></td>
					<?
				}
				?>
				<td></td>
			</tr>
		</tbody>
	</table>
	<div>
	Note: The above price including VAT, AIT & delivery charge upto site.
	</div>
	<br>
	<?
	$data_array = sql_select("select id, terms from  wo_booking_terms_condition where booking_no='" . str_replace("'", "", $data[2]) . "' and entry_form=481  order by id");
	?>
	<table width='1200' cellpadding="0" cellspacing="0">
		<tbody>
			<tr>
				<td width='150'style="text-decoration-line: underline;font-size:22px;"><strong>Terms and Conditions Notes:</strong></td>
				<td width='30'></td>
				<td ></td>
			</tr>
			<?
				$i=1;
				if (count($data_array) > 0) {
					foreach ($data_array as $row) {
						?>
						<tr>
							<td></td>
							<td style='font-size:22px;' align="center" valign="top"><strong><?=$i.'.';?></strong></td>
							<td style='font-size:22px;word-break: break-all;'><? echo $row[csf('terms')]; ?></td>
						</tr>
						<?
						$i++;
					}
				}
			?>
		</tbody>
	</table>
	<?


		$user_sign_arr = array();
	  	$appSqlRes =sql_select("select a.USER_ID,b.USER_FULL_NAME from ELECTRONIC_APPROVAL_SETUP a,USER_PASSWD b where b.id = a.user_id and (a.ENTRY_FORM=49 or b.id=$inserted_by)");

		$sig_img_arr=return_library_array( "select MASTER_TBLE_ID,IMAGE_LOCATION from COMMON_PHOTO_LIBRARY where FORM_NAME='user_signature' and MASTER_TBLE_ID in(select a.USER_ID from ELECTRONIC_APPROVAL_SETUP a,USER_PASSWD b where b.id = a.user_id and (a.ENTRY_FORM=49 or b.id=$inserted_by))",'MASTER_TBLE_ID','IMAGE_LOCATION');

		

		foreach($appSqlRes as $appRow){
			$user_sign_arr[$appRow['USER_ID']] = '<img height="25" src="../../'.$sig_img_arr[$appRow['USER_ID']].'" alt=""><br>'.$appRow['USER_FULL_NAME'];
		}

	  	echo get_app_signature(481, 0, $tbl_width, $template_id, 40, $inserted_by,$user_sign_arr,8); 


	exit();
}

//========== CHEM CS ========
if($action=="print_report_generate")
{
	extract($_REQUEST);
    $data=explode('*',$data);
    $mst_id=$data[0];
    $zero_value=$data[3];
     $temp_id=$data[4];
    echo load_html_head_contents($data[1],"../../../", 1, 1, $unicode,'','');
	//   print_r ($data); die;
	$basis_arr=array(1=>"Requisition",2=>"Item");
	$currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$lib_company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$data_array=sql_select("SELECT id, sys_number, sys_number_prefix, sys_number_prefix_num, basis_id, req_item_no,req_item_mst_id, rec_date, cs_date, supp_id, currency_id, cs_valid_date, source, approved, company_id, comments,inserted_by from req_comparative_mst where id='$mst_id' and is_deleted=0 and status_active=1");
	$remarks_info=$data_array[0][csf('comments')];
	$inserted_by=$data_array[0][csf('inserted_by')];
	$currency_icon=$currency_sign_arr[$data_array[0][csf('currency_id')]];
	$group_name=return_field_value("group_name","lib_group","is_deleted= 0 order by id desc","group_name");
	$image_location=return_field_value("image_location","common_photo_library","is_deleted= 0 and form_name='group_logo' order by id desc","image_location");
	$supp_mult_arr='';
	$basis='';

	$dtls_sql="SELECT a.id as ID, a.prod_id as PROD_ID, a.item_group_id as ITEM_GROUP_ID, a.req_qty as REQ_QTY
	from req_comparative_dtls a 
	where a.mst_id='$mst_id' and a.is_deleted=0 and a.status_active=1
	order by a.item_group_id,a.id asc";
	$dtls_sql_result=sql_select($dtls_sql);
	$selected_prod_id="";
	foreach($dtls_sql_result as $row)
	{
		$selected_prod_id.=$row["PROD_ID"].",";
		$selected_group_id.=$row["ITEM_GROUP_ID"].",";
		$rowspan_arr[$row['ITEM_GROUP_ID']]++;
		$rowspan_arr1[$row['REQ_QTY']]++;
	}
	$selected_prod_id=chop($selected_prod_id,",");
	$selected_group_id=implode(",",array_unique(explode(",",chop($selected_group_id,','))));
	$item_des_sql="SELECT b.id as PROD_ID, b.item_description as ITEM_DESCRIPTION,b.order_uom as ORDER_UOM, c.item_name as ITEM_NAME
	from product_details_master b, lib_item_group c 
	where b.id in($selected_prod_id) and b.item_group_id=c.id and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1";
	$item_des_sql_result=sql_select($item_des_sql);
	$item_des_sql_arr=array();	$item_cat_sql_arr=array();
	foreach($item_des_sql_result as $row)
	{
		$item_des_sql_arr[$row["PROD_ID"]]['ITEM_DESCRIPTION']=$row["ITEM_DESCRIPTION"];
		$item_des_sql_arr[$row["PROD_ID"]]['ITEM_NAME']=$row["ITEM_NAME"];
		$item_des_sql_arr[$row["PROD_ID"]]['ORDER_UOM']=$row["ORDER_UOM"];
	}
	if($zero_value!=1)
	{
		$req_item_dtls_id=$data_array[0][csf('req_item_dtls_id')];
		$lastWoRate_sql = "SELECT a.wo_date as WO_DATE,a.currency_id as CURRENCY_ID, b.supplier_order_quantity as SUPPLIER_ORDER_QUANTITY, b.rate as RATE 
		from wo_non_order_info_mst a,wo_non_order_info_dtls b,inv_purchase_requisition_dtls c,product_details_master d where a.id=b.mst_id and b.requisition_dtls_id=c.id and c.product_id=d.id and d.item_group_id in ($selected_group_id) ORDER BY b.id DESC FETCH FIRST 3 ROWS ONLY";
		// echo $lastWoRate_sql;
		$lastWoRate_data=sql_select($lastWoRate_sql);
	}

	$supp_dtls_arr=sql_select("SELECT id, mst_id,dtls_id, supp_id,prod_id, quoted_price, neg_price, con_price, pay_term,tenor from req_comparative_supp_dtls where mst_id='$mst_id' and is_deleted=0 and status_active=1 order by id asc");
	foreach ($data_array as $row)
	{ 
		$supp_mult=$row[csf('supp_id')];
		$com_mult=$row[csf('company_id')];
		$basis_id=$row[csf('basis_id')];
		$supp_mult_arr=explode(',',$supp_mult);
		$com_mult_arr=explode(',',$com_mult);
		$company_nam='';
		if(count($com_mult_arr)==count($lib_company_arr))
		{
			$company_nam .="All Company";
		}
		else
		{
			foreach($com_mult_arr as $value){
				if($company_nam !='')	{$company_nam .= ", ".$lib_company_arr[$value];}else{$company_nam =$lib_company_arr[$value];}
			}
		}
		$supplier_count=count($supp_mult_arr);
		$tbl_width=550+($supplier_count*300);
		$tbl_col=6+($supplier_count*3);
		$tbl_remarks_col=($supplier_count*3)+3;
		$grand_amount=array();
		
		?>
        <table cellspacing="0" width="<?=$tbl_width;?>">
            <tr>
                <td align="center" width="80"><img src="../../<? echo $image_location; ?>" height="50" width="70" alt='Group Logo'></td>
                <td colspan="6" style="font-size:30px;" align="center"><strong><? echo $group_name; ?></strong></td>
            </tr>
            <tr>
				<td></td>
                <td colspan="6" style="font-size:26px;" align="center"><strong><? echo $company_nam; ?></strong></td>
            </tr>
            <tr>
				<td></td>
                <td colspan="6" style="font-size:26px;" align="center"><strong><? echo 'Competitive Price Comparison'; ?></strong></td>
            </tr>
            <tr>
				<td colspan="3" width='400'>
					<table width='400' style="font-size:23px;" cellpadding="0" cellspacing="0" border="1" rules="all">
						<tr>
							<td width='200'><strong>CS No.</strong></td>
							<td><strong><? echo $row[csf('sys_number')]; ?></strong></td>
						</tr>
						<tr>
							<td><strong>CS Date</strong></td>
							<td ><strong><? echo date("d-M-Y", strtotime($row[csf('cs_date')])); ?></strong></td>
						</tr>
						<tr>
							<td><strong>CS Validity Date</strong></td>
							<td><strong><? echo date("d-M-Y", strtotime($row[csf('cs_valid_date')])); ?></strong></td>
						</tr>
						<tr>
							<td><strong>Materials Type</strong></td>
							<td><strong><? 
							if($row[csf('source')]==1){echo "Import"; }
							if($row[csf('source')]==2 || $row[csf('source')]==3 ){echo "Local"; }
							?></strong></td>
						</tr>
						<tr>
							<td><strong>Currency</strong></td>
							<td><strong><? echo $currency[$row[csf('currency_id')]]; ?></strong></td>
						</tr>
					</table>
				</td>
				<td ></td>
                <td colspan="3" width='370' align="right">
					<?
						if($zero_value!=1)
						{
							?>
								<table width='360' style="font-size:23px;" cellpadding="0" cellspacing="0" border="1" rules="all">
									<tr>
										<td align="center" colspan='3' style='font-size:110%;'><strong>Last Purchase History</strong></td>
									</tr>
									<tr>
										<td align="center"><strong>Date</strong></td>
										<td align="center"><strong>Qty</strong></td>
										<td align="center"><strong>Price</strong></td>
									</tr>
									<?
										foreach($lastWoRate_data as $val)
										{
											?>
												<tr>
													<td align="center" width="100"><? echo date("d-M-Y", strtotime($val['WO_DATE']));?></td>
													<td align="right" width="80"><? echo $val['SUPPLIER_ORDER_QUANTITY'];?></td>
													<td align="right" width="80"><? echo $currency_sign_arr[$val['CURRENCY_ID']].' '.$val['RATE'];?></td>
												</tr>
											<?
										}
									?>
								</table>
							<?
						}
					?>
					
				</td>
            </tr>
		</table>
		<?
	}

	?>       
    <br>
	<table width="<?=$tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" >
		<thead bgcolor="#dddddd" align="center" style="font-size:22px;"> 
			<tr >
				<th rowspan="2" width="50"  align="center">SL No</th>
				<th rowspan="2" width="100" align="center">Items Name</th>
				<th rowspan="2" width="130" align="center">Product Name</th>
				<th rowspan="2" width="80" align="center">Req. Qty</th>
				<th rowspan="2" width="80" align="center">Unit</th>
				<?
					foreach($supp_mult_arr as $row)
					{
						?>
						<th colspan="3" width="300"><?= $supplier_arr[$row];?></th>
						<?
					}
				?>
			</tr>
			<tr>
				<?
					foreach($supp_mult_arr as $row)
					{
						?>
						<th width="80">Quoted Price</th>
						<th width="80">Neg Price</th>
						<th width="140">T. Value</th>
						<?
					}
				?>
			</tr>
		</thead>
		<tbody>
			<?
				$i=1;
				$arr_chk=array();$arr_chk1=array();
				$row_count=count($dtls_sql_result);
				foreach($dtls_sql_result as $row)
				{
					$pro_id=explode(",",$row['PROD_ID']);
					$pro_id_one=max($pro_id);
					$lastRcvRate= $lastRcvRateArr[$pro_id_one];
					$pro_id_all=$row['PROD_ID'];
					if ($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
					$rowspan=$rowspan_arr[$row["ITEM_GROUP_ID"]];
					$rowspan1=$rowspan_arr1[$row["REQ_QTY"]];

					?>
						<tr bgcolor="<?= $bgcolor; ?>">
							<td align="center" style="font-size:20px;"><strong><? echo $i; ?></strong></td>
							<?
								if(!in_array($row["ITEM_GROUP_ID"],$arr_chk))
								{
									$arr_chk[]=$row["ITEM_GROUP_ID"];
									?>
										<td style="font-size:20px;" rowspan="<? echo $rowspan; ?>"><strong><? echo $item_des_sql_arr[$pro_id_one]['ITEM_NAME'];?></strong></td>
									<?
								}
							?>
							<td title="<?= $pro_id_one;?>" style="font-size:20px;"><strong><? echo $item_des_sql_arr[$pro_id_one]['ITEM_DESCRIPTION']; ?></strong></td>
							<?
								if (!in_array($row["REQ_QTY"],$arr_chk1))
								{
									$arr_chk1[]=$row["REQ_QTY"];
									?>
										<td style="font-size:20px;" align="right" valign="middle" rowspan="<? echo $rowspan1; ?>"><? echo $row['REQ_QTY']; ?></td>
										<td align="center" style="font-size:20px;"  rowspan="<? echo $rowspan1; ?>"><? echo $unit_of_measurement[$item_des_sql_arr[$pro_id_one]['ORDER_UOM']]; ?></td>
									<?
								}
							$row_id=$row['ID'];
							foreach($supp_mult_arr as $supp)
							{
								foreach($supp_dtls_arr as $rows)
								{
									$dtls_row_id=$rows[csf('dtls_id')];
									$supp_id=$rows[csf('supp_id')];
									if($row_id==$dtls_row_id){
										if($supp==$supp_id){																				
											?>
												<td align="right" style="font-size:20px;" ><? if($rows[csf('quoted_price')]!=''){echo $currency_icon.' '.number_format($rows[csf('quoted_price')],2);}?></td>
												<td align="right" style="font-size:20px;" ><? if($rows[csf('con_price')]!=''){echo $currency_icon.' '.number_format($rows[csf('con_price')],2);} ?></td>
												<td align="right" style="font-size:20px;">
												<? 
												$total_amt=$rows[csf('con_price')]*$row['REQ_QTY']; $grand_amount[$rows[csf('supp_id')]]+=$total_amt; 
												if($total_amt!=0 && $total_amt!=''){echo $currency_icon.' '.number_format($total_amt,2);} 
												?>
                                                </td>
											<?
											$pay_id=$rows[csf('pay_term')];
											if($pay_id!=0 && $pay_id!='')
											{
												if($pay_id==2)
												{
													$pay_dtls[$rows[csf('supp_id')]]="LC at ".$rows[csf('tenor')]." days";
												}
												else
												{
													$pay_dtls[$rows[csf('supp_id')]]=$pay_term[$rows[csf('pay_term')]];
												}
											}		
										}
									}
								}
							}
							?>
						</tr>
					<?
					$i++;
				}
			?>
			<tr height="40">
				<td colspan='5' style="font-size:22px;"><strong>Grand Total</strong></td>
				<?
				foreach($supp_mult_arr as $supp)
				{
					?>
					<td></td>
					<td></td>
					<td align="right" style="font-size:22px;"><span style="border-bottom: 3px double;"><strong><? echo $currency_icon.' '.number_format($grand_amount[$supp],2); ?></strong></span></td>
					<?
				}
				?>
			</tr>
			<tr>
				<td colspan='5' style="font-size:22px;"><strong>Payment Terms</strong></td>
				<?
					foreach($supp_mult_arr as $supp)
					{
						?>
						<td colspan="3" align="center" style="font-size:22px;"><span ><strong><? echo $pay_dtls[$supp]; ?></strong></span></td>
						<?
					}
				?>
			</tr>
			<tr>
				<td colspan='2' style="font-size:22px;"><strong>Remarks</strong></td>
				<td colspan='<?=$tbl_remarks_col;?>' style="font-size:22px;"><? echo $remarks_info;?></td>
			</tr>
		</tbody>
	</table>
	<br>
	<?
	$data_array = sql_select("select id, terms from  wo_booking_terms_condition where booking_no='" . str_replace("'", "", $data[2]) . "' and entry_form=481  order by id");
	?>
	<table width='1200' cellpadding="0" cellspacing="0">
		<tbody>
			<tr>
				<td width='150'style="text-decoration-line: underline;font-size:22px;"><strong>Approval Note:</strong></td>
				<td width='30'></td>
				<td ></td>
			</tr>
			<?
				$i=1;
				if (count($data_array) > 0) {
					foreach ($data_array as $row) {
						?>
						<tr>
							<td></td>
							<td style='font-size:22px;' align="center" valign="top"><strong><?=$i.'.';?></strong></td>
							<td style='font-size:22px;word-break: break-all;'><? echo $row[csf('terms')]; ?></td>
						</tr>
						<?
						$i++;
					}
				}
			?> 
		</tbody>
	</table>

	  <? 
	  	$user_sign_arr = array();
	  	$appSqlRes =sql_select("select a.USER_ID,b.USER_FULL_NAME from ELECTRONIC_APPROVAL_SETUP a,USER_PASSWD b where b.id = a.user_id and (a.ENTRY_FORM=49 or b.id=$inserted_by)");

		$sig_img_arr=return_library_array( "select MASTER_TBLE_ID,IMAGE_LOCATION from COMMON_PHOTO_LIBRARY where FORM_NAME='user_signature' and MASTER_TBLE_ID in(select a.USER_ID from ELECTRONIC_APPROVAL_SETUP a,USER_PASSWD b where b.id = a.user_id and (a.ENTRY_FORM=49 or b.id=$inserted_by))",'MASTER_TBLE_ID','IMAGE_LOCATION');

		

		foreach($appSqlRes as $appRow){
			$user_sign_arr[$appRow['USER_ID']] = '<img height="25" src="../../'.$sig_img_arr[$appRow['USER_ID']].'" alt=""><br>'.$appRow['USER_FULL_NAME'];
		}
	  	echo get_app_signature(481, 0, 1200, $temp_id, 40, $inserted_by,$user_sign_arr,8); 
	  ?>
	<!-- <table width='1200' cellpadding="0" cellspacing="0" align="center">
		<tbody>
			<tr>
				<td colspan='7' height='100' ></td>
			</tr>
			<tr>
				<td width='170' style='border-top-style: solid;text-align: center;font-size:22px;' valign='top'><strong>Prepared By</strong></td>
				<td width='120'></td>
				<td width='220' style='border-top-style: solid;text-align: center;font-size:22px;'><strong>AGM- Sourcing & Sustainability</strong></td>
				<td width='120'></td>
				<td width='200' style='border-top-style: solid;text-align: center;font-size:22px;'><strong>Director- Sourcing & SCM</strong></td>
				<td width='120'></td>
				<td style='border-top-style: solid;text-align: center;font-size:22px;' valign='top'><strong>M.D Sir</strong></td>
			</tr>
		</tbody>
	</table> -->
	<?
	exit();
}

//========== Estimated Price ========
if($action=="print_report_estimated_price")
{ 
	extract($_REQUEST);
    $data=explode('*',$data);
	//print_r($data);
    $mst_id=$data[0];
	$cs_number=$data[2];
	$cbo_template_id=$data[3];
	$dtls_ids=$data[4];
	$supp_ids=$data[5];
	$supp_datas=$data[6];
	
    echo load_html_head_contents($data[1],"../../../", 1, 1, $unicode,'','');
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	//$lib_company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$lib_company_arr=array();
	$sql_company=sql_select("select id as ID, company_name as COMPANY_NAME, plot_no as PLOT_NO, level_no as LEVEL_NO, road_no as ROAD_NO, block_no as BLOCK_NO, city as CITY, zip_code as ZIP_CODE, contact_no as CONTACT_NO from lib_company");

	foreach($sql_company as $row){
		$lib_company_arr[$row['ID']]['company_name']=$row['COMPANY_NAME'];
		$com_address="";
		if($row['PLOT_NO'] !=''){ $com_address.=$row['PLOT_NO'];}
		if($row['LEVEL_NO']!=''){ $com_address.=", ".$row['LEVEL_NO'];}
		if($row['ROAD_NO'] !=''){ $com_address.=", ".$row['ROAD_NO'];}
		if($row['BLOCK_NO'] !=''){ $com_address.=", ".$row['BLOCK_NO'];}
		if($row['CITY'] !=''){ $com_address.=", ".$row['CITY'];}
		if($row['ZIP_CODE'] !=''){ $com_address.=", ".$row['ZIP_CODE'];}
		$lib_company_arr[$row['ID']]['address']=$com_address;
	}

	$data_array=sql_select("select id, sys_number, sys_number_prefix, sys_number_prefix_num, basis_id, req_item_no, req_item_mst_id,req_item_dtls_id, rec_date, cs_date, supp_id, currency_id, cs_valid_date, source, approved, company_id, comments from req_comparative_mst where id='$mst_id' and is_deleted=0 and status_active=1");

	$req_item_mst_id=$data_array[0][csf('req_item_mst_id')];
	$req_item_no=$data_array[0][csf('req_item_no')];
	$cs_date=$data_array[0][csf('cs_date')];
	$currency_id=$data_array[0][csf('currency_id')];

	$sql_requ=sql_select("select id, company_id from inv_purchase_requisition_mst where id in($req_item_mst_id) and is_deleted=0 and status_active=1");
	$requ_com_arr=array();
	$req_company_id='';
	foreach($sql_requ as $value){
		$requ_com_arr[$value[csf('company_id')]]=$value[csf('company_id')];
		$req_company_id=$value[csf('company_id')];
	}

	if (count($requ_com_arr)>1){
		echo '<div style="color:red; font-size:20px;">Multiple Company Not Allowed This Button!!</div>';die;
	}

	$sql_supp=sql_select("select c.last_approval_rate as LAST_APPROVAL_RATE, c.supp_id as SUPP_ID, c.dtls_id as DTLS_ID, c.pay_term as PAY_TERM
	from req_comparative_supp_dtls c
	where c.mst_id=$mst_id and c.status_active=1 and c.is_deleted=0");
	$supp_payterm_arr=array();
	foreach ($sql_supp as $row){
		$supp_payterm_arr[$row['DTLS_ID']][$row['LAST_APPROVAL_RATE']]['pay_term']=$row['PAY_TERM'];
		$supp_payterm_arr[$row['DTLS_ID']][$row['LAST_APPROVAL_RATE']]['supp_id']=$row['SUPP_ID'];
	}
	//echo '<pre>';print_r($supp_payterm_arr);

	$sql_dtls="select a.order_uom as UOM, a.item_description as ITEM_DESCRIPTION, b.id as DTLS_ID, b.item_category_id as ITEM_CATEGORY_ID, b.item_group_id as ITEM_GROUP_ID, b.req_qty as REQ_QTY, min(c.last_approval_rate) as MINIMUM_APPROVAL_RATE
	from product_details_master a, req_comparative_dtls b, req_comparative_supp_dtls c
	where a.id=b.prod_id and b.id=c.dtls_id and b.mst_id=$mst_id and b.id in($dtls_ids) and c.supp_id in($supp_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.order_uom, a.item_description, b.id, b.item_category_id, b.item_group_id, b.req_qty order by b.id asc";
	$sql_dtls_res=sql_select($sql_dtls);
	$rate_supplier_arr=array();
	foreach ($sql_dtls_res as $row)
	{
		$rate_supplier_arr[$row['MINIMUM_APPROVAL_RATE']][$row['DTLS_ID']]=$row['REQ_QTY']*$row['MINIMUM_APPROVAL_RATE'];		
	}

	$supp_dtls_ar=explode(',',$supp_datas);	
	foreach($supp_dtls_ar as $supp_info)
	{
		$suppInfo=explode('_',$supp_info);
		//print_r($suppInfo);
		$supp_Arr[$suppInfo[1]][$suppInfo[2]][$suppInfo[3]] = $suppInfo[0];
		//print_r($supp_Arr);
	}


	?>
	<table cellspacing="0" width="900">
		<tr>
			<td colspan="7" style="font-size:xx-large;" align="center"><strong><? echo $lib_company_arr[$req_company_id]['company_name']; ?></strong></td>
		</tr>
		<tr>
			<td colspan="7" style="font-size:24px;" align="center"><strong><? echo $lib_company_arr[$req_company_id]['address']; ?></strong></td>
		</tr>
		<tr>
			<td colspan="7" style="font-size: 24px;" align="center"><strong>Estimated Price</strong></td>
		</tr>
		<tr>
			<td colspan="4" align="left"><strong>Purchase Requsition NO:&nbsp;<? echo $req_item_no; ?></strong></td>
			<td colspan="3" align="right"><strong>CS Date:&nbsp;<? echo change_date_format($cs_date); ?></strong></td>                
		</tr>
		<tr>
			<td colspan="7" style="font-size: 20px;" align="center"><strong>Note - Money Requisition</strong></td>
		</tr>
		<tr>
			<td colspan="7">As per management decision we need to purchase items for factory out of these items serial<br>number is to be procured now, as below :<br>Purchase + Accounts + Audit</td>
		</tr>
	</table>
	<table width="900" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
		<thead bgcolor="#dddddd" align="center">
			<tr>
				<th width="50">SL</th>
				<th width="200">Item Categorey</th>
				<th width="250">Particular</th>
				<th width="150">Supplier Name</th>
				<th width="100">Require Qty</th>
				<th width="100">UOM</th>
				<th width="100">Price</th>
				<th width="100">Total Amount</th>
			</tr>
		</thead>
		<tbody>
			<?
			$i=1;
			$totul_amount=0;
			$grand_totul_amount=0;
			foreach($sql_dtls_res as $row)
			{
				if ($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
				$totul_amount=$row['REQ_QTY']*$row['MINIMUM_APPROVAL_RATE'];
				?>
				<tr bgcolor="<?= $bgcolor; ?>">
					<td align="center"><? echo $i; ?></td>
					<td align="left"><? echo $item_category[$row['ITEM_CATEGORY_ID']]; ?></td>
					<td align="left"><? echo $row['ITEM_DESCRIPTION']; ?></td>
					<td align="left"><? echo $supplier_arr[$supp_Arr[$row['MINIMUM_APPROVAL_RATE']][$row['REQ_QTY']][$row['DTLS_ID']]]; ?></td>
					<td align="right"><? echo $row['REQ_QTY']; ?>&nbsp;</td>
					<td align="center"><? echo $unit_of_measurement[$row['UOM']]; ?></td>
					<td align="right"><? echo $row['MINIMUM_APPROVAL_RATE']; ?>&nbsp;</td>
					<td align="right"><? echo number_format($totul_amount,2); ?>&nbsp;</td>
				</tr>
				<?
				$i++;
				$grand_totul_amount+=$totul_amount;
			}
			?>
		</tbody>
		<tfoot>
			<tr bgcolor="#dddddd">
				<td align="right" colspan="6"><strong>Total Amount:</strong></td>
				<td align="right"><strong><? echo number_format($grand_totul_amount,2); ?>&nbsp;</strong></td>
			</tr>
		</tfoot>
	</table>
	<br>
	<table width="900" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
		<thead bgcolor="#dddddd" align="center">
			<tr>
				<th width="50">SL</th>
				<th width="550">Supplier Name</th>
				<th width="300">Total Amount</th>
			</tr>
		</thead>
		<tbody>
			<?
			$i=1;
			$grand_tot_amount=0;
			$supp_info_arr=array();
			$supp_datas_arr=explode(',',$supp_datas);	
			foreach($supp_datas_arr as $supp_info)
			{
				$suppInfo=explode('_',$supp_info);
				$supp_id=$suppInfo[0];
				$supp_rate=$suppInfo[1];
				$supp_qty=$suppInfo[2];
				$value=$supp_rate*$supp_qty;
				$supp_info_arr[$supp_id]+=$value;
			}

			foreach($supp_info_arr as $supp_id => $value)
			{
				if ($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}

				?>
				<tr bgcolor="<?= $bgcolor; ?>">
					<td align="center"><? echo $i; ?></td>
					<td align="left"><? echo $supplier_arr[$supp_id]; ?></td>
					<td align="right"><? echo number_format($value,2); ?>&nbsp;</td>
				</tr>
				<?
				$i++;
				$grand_tot_amount+=$value;							
			}
			?>
		</tbody>
		<tfoot>
			<tr bgcolor="#dddddd">
				<td align="right" colspan="2"><strong>Total Amount:</strong></td>
				<td align="right"><strong><? echo number_format($grand_tot_amount,2); ?>&nbsp;</strong></td>
			</tr>
		</tfoot>	
	</table>
	<br>
	<table width="900" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
		<tr>
			<td align="left" ><b><? echo "Innn World: ".$currency[$currency_id]." ".number_to_words($grand_tot_amount); ?> Only</b></td>
		</tr>
	</table>
	<?
	$user_sign_arr = array();
	$appSqlRes =sql_select("select a.USER_ID,b.USER_FULL_NAME from ELECTRONIC_APPROVAL_SETUP a,USER_PASSWD b where b.id = a.user_id and (a.ENTRY_FORM=49 or b.id=$inserted_by)");

	$sig_img_arr=return_library_array( "select MASTER_TBLE_ID,IMAGE_LOCATION from COMMON_PHOTO_LIBRARY where FORM_NAME='user_signature' and MASTER_TBLE_ID in(select a.USER_ID from ELECTRONIC_APPROVAL_SETUP a,USER_PASSWD b where b.id = a.user_id and (a.ENTRY_FORM=49 or b.id=$inserted_by))",'MASTER_TBLE_ID','IMAGE_LOCATION');

	

	foreach($appSqlRes as $appRow){
		$user_sign_arr[$appRow['USER_ID']] = '<img height="25" src="../../'.$sig_img_arr[$appRow['USER_ID']].'" alt=""><br>'.$appRow['USER_FULL_NAME'];
	}
	echo get_app_signature(481, 0, '900px', $cbo_template_id, 20, $inserted_by,$user_sign_arr,7); 
	//echo signature_table(481, 0, "900px",$cbo_template_id,20);
	?>
	<?
	exit();
}

if($action=='file_upload')
{
	extract($_REQUEST);
	$data_array="";
	$id=return_next_id( "id","common_photo_library", 1 ) ;
	for($i=0;$i<count($_FILES['file']);$i++)
	{
		$filename = time(). $_FILES['file'][name][$i]; 
		$location = "../../../file_upload/".$filename;
		if(move_uploaded_file( $_FILES['file']['tmp_name'][$i], $location))
		{ 
			if($data_array!="") $data_array.=",";
			$data_array .="(".$id.",".$mst_id.",'comparative_statement','file_upload/".$filename."','2','".$filename."')";
		}
		else
		{ 
			echo 0; 
		}
		$id++; 
	}
		
		
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	
	$field_array="id,master_tble_id,form_name,image_location,file_type,real_file_name";
	$rID=sql_insert("common_photo_library",$field_array,$data_array,1);
	if($db_type==0)
	{
		if($rID)
		{
			mysql_query("COMMIT");
			echo "0**".$new_system_id[0]."**".$id_mst;
		}
		else
		{
			mysql_query("ROLLBACK");
			echo "10**".$id_mst;
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($rID)
		{
			oci_commit($con);
			echo "0**".$new_system_id[0]."**".$id_mst;
		}
		else
		{
			oci_rollback($con);
			echo "10**".$id_mst;
		}
	}
	disconnect($con);
	die;
}

if($action == 'deny_cause_list'){
	extract($_REQUEST);
    echo load_html_head_contents($data[1],"../../../", 1, 1, $unicode,'','');
 
	$sql = "select a.ID,a.COMMENTS,a.UN_APPROVED_REASON,a.APPROVED,a.APPROVED_BY,a.APPROVED_DATE,b.USER_FULL_NAME from APPROVAL_HISTORY a,USER_PASSWD b where a.ENTRY_FORM=49 and a.APPROVED_BY=b.id AND a.MST_ID= $update_id order by a.ID desc";
	//echo $sql;
	$sql_res = sql_select($sql);

	?>
		<table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
			<thead>
				<th>Sl</th>
				<th>Authoroty</th>
				<th>Type</th>
				<th>Date & Time</th>
				<th>Comments</th>
			</thead>
			<tbody>
				<?
				$i=1;
				$app_type_arr = array(0 => 'Unapprove', 1=>'Full Approved',2=>'Deny',3=>'Partial Approved');
				foreach($sql_res as $rows){
				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
				?>
				<tr bgcolor="<?= $bgcolor; ?>">
					<td align="center"><?= $i;?></td>
					<td><?= $rows['USER_FULL_NAME'];?></td>
					<td><?= $app_type_arr[$rows['APPROVED']];?></td>
					<td align="center"><?= $rows['APPROVED_DATE'];?></td>
					<td><?= $rows['COMMENTS'];?></td>
				</tr>
				<?
				$i++;
				}
				?>
			</tbody>
		</table>


	<?

 


}

