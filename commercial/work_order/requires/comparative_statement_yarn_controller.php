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
			show_list_view ( document.getElementById('cs_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value, 'create_cs_search_list_view', 'search_div', 'comparative_statement_yarn_controller', 'setFilterGrid(\'search_div\',-1)');
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
	$sql= "select id, sys_number, sys_number_prefix_num, cs_date, req_item_no, company_id from req_comparative_mst where status_active=1 and is_deleted=0 and entry_form=523 $cs_num $date_cond $year_cond order by id DESC";
	//echo $sql;//die;
	$sql_result= sql_select($sql);
	
	?>
	<table width="700" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all">
	<thead>
		<th width="40">SL</th>
		<th width="80">CS No</th>
		<th width="50">CS Suffix</th>
		<th width="80">CS Date</th>
		<th width="150">Requisition</th>
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
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
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

	$dtls_sql="select a.id as ID, a.prod_id as PROD_ID, a.item_category_id as ITEM_CATEGORY_ID, a.color_id as COLOR_ID, a.count_id as COUNT_ID,	a.composition_id as COMPOSITION_ID, a.YARN_TYPE_ID as yarn_type_id, a.req_qty as REQ_QTY, a.fabric_cost_dtls_id as REQU_DTLS_ID
	from req_comparative_dtls a 
	where a.mst_id='$data' and a.is_deleted=0 and a.status_active=1
	order by a.id asc";
	$dtls_sql_result=sql_select($dtls_sql);


	//$supp_rate_sql="select from req_comparative_supp_dtls a, lib_supplier_wise_rate b where a.prod_id=b.prod_id and b.prod_id in($selected_prod_id) and b.entry_form=481 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0";

	// $dtls_arr=sql_select("select a.id, a.prod_id, a.item_group, a.req_qty, a.brand, a.model, a.origin, b.order_uom,b.item_category_id, b.item_description,b.item_size,b.item_code from req_comparative_dtls a ,product_details_master b, lib_item_group c where a.mst_id='$data' and a.prod_id=b.id and b.item_group_id=c.id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 order by a.id asc");

	$supp_dtls_arr=sql_select("select id, mst_id, dtls_id, supp_id, prod_id, quoted_price, neg_price, con_price, pay_term, tenor, pilling,csp,ipi, del_start, del_close, remarks from req_comparative_supp_dtls where mst_id='$data' and is_deleted=0 and status_active=1 order by id asc");
	$supplier_count=count($supp_mult_arr);
	$tbl_width=600+($supplier_count*880);

	$data_tbl.='<table width="'.$tbl_width.'" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all"  id="tbl_details">';
	$data_tbl.='<thead><tr>';
	$data_tbl.='<th rowspan="2" width="40">SL</th>';
	$data_tbl.='<th rowspan="2" width="120">Yarn Color</th>';
	$data_tbl.='<th rowspan="2" width="80">Count</th>';
	$data_tbl.='<th rowspan="2" width="150">Composition</th>';
	$data_tbl.='<th rowspan="2" width="130">Yarn Type</th>';
	$data_tbl.='<th rowspan="2" width="80">Req. Qty.</th>';
	foreach($supp_mult_arr as $row)
	{
		$data_tbl.='<th colspan="11" width="880">'.$supplier_arr[$row].'</th>';
	}
	$data_tbl.='</tr><tr>';
	foreach($supp_mult_arr as $row)
	{
		$data_tbl.='<th width="80">Quoted Price</th>';
		$data_tbl.='<th width="80">Last Price</th>';
		$data_tbl.='<th width="80">Con. Price</th>';
		$data_tbl.='<th width="80">Pay Term</th>';
		$data_tbl.='<th width="80">Tenor</th>';
		$data_tbl.='<th width="80">Pilling</th>';
		$data_tbl.='<th width="80">CSP</th>';
		$data_tbl.='<th width="80">IPI</th>';
		$data_tbl.='<th width="80">Del.Start</th>';
		$data_tbl.='<th width="80">Del.Close</th>';
		$data_tbl.='<th width="80">Remarks</th>';
	}

	$data_tbl.='</tr></thead><tbody>';
	$i=1;
	foreach($dtls_sql_result as $row)
	{		
		$data_tbl.='<tr class="general" id='.$i.'>';
		$data_tbl.='<td align="center" >'.$i.'<input type="hidden" name="" id="txtprod_'.$i.'" value="'.$row['PROD_ID'].'" ><input type="hidden" name="" id="txtcategory_'.$i.'" value="'.$row['ITEM_CATEGORY_ID'].'"><input type="hidden" name="" id="txtreqdtlsid_'.$i.'" value="'.$row['REQU_DTLS_ID'].'" ></td>';
		$data_tbl.='<td align="center" ><input type="text" style="width:100px" name="" id="txtyarncolor_'.$i.'" title="'.$row['COLOR_ID'].'" class="text_boxes" value="'.$color_arr[$row['COLOR_ID']].'" readonly></td>';
		$data_tbl.='<td align="center" ><input type="text" style="width:70px" name="" id="txtcount_'.$i.'" class="text_boxes" title="'.$row['COUNT_ID'].'" value="'.$count_arr[$row['COUNT_ID']].'" readonly></td>';
		$data_tbl.='<td align="center" ><input type="text" style="width:130px" name="" id="txtcomposition_'.$i.'" class="text_boxes" title="'.$row['COMPOSITION_ID'].'" value="'.$composition[$row['COMPOSITION_ID']].'" readonly></td>';
		$data_tbl.='<td align="center" ><input type="text" style="width:110px" name="" id="txtyarntype_'.$i.'" class="text_boxes" title="'.$row['YARN_TYPE_ID'].'" value="'.$yarn_type[$row['YARN_TYPE_ID']].'" readonly></td>';
		$data_tbl.='<td align="center" ><input type="text" style="width:70px" name="" id="txtqty_'.$i.'" class="text_boxes_numeric" value="'.$row['REQ_QTY'].'" readonly></td>';
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
						$data_tbl.='<td align="center" ><input type="text" style="width:60px" name="" id="txtquoted_'.$i.'_'.$supp_id.'" class="text_boxes_numeric" value="'.$rows[csf('quoted_price')].'"></td>';
						$data_tbl.='<td align="center" ><input type="text" style="width:60px" name="" id="txtneg_'.$i.'_'.$supp_id.'"class="text_boxes_numeric" placeholder="Display" value="'.$rows[csf('neg_price')].'" readonly></td>';
						$data_tbl.='<td align="center" ><input type="text" style="width:60px" name="" id="txtcon_'.$i.'_'.$supp_id.'" class="text_boxes_numeric" value="'.$rows[csf('con_price')].'"></td>';
						$data_tbl.='<td align="center" >'.create_drop_down("txtPayTerm_".$i.'_'.$supp_id, 80, $pay_term, "", 1, "-- Select --", $rows[csf('pay_term')] , "", "").'</td>';
						$data_tbl.='<td align="center" ><input type="text" style="width:60px" name="" id="txtTenor_'.$i.'_'.$supp_id.'" class="text_boxes_numeric" value="'.$rows[csf('tenor')].'"></td>';
						$data_tbl.='<td align="center" ><input type="text" style="width:60px" name="" id="txtpilling_'.$i.'_'.$supp_id.'" class="text_boxes" value="'.$rows[csf('PILLING')].'"></td>';
						$data_tbl.='<td align="center" ><input type="text" style="width:60px" name="" id="txtcsp_'.$i.'_'.$supp_id.'" class="text_boxes" value="'.$rows[csf('CSP')].'"></td>';
						$data_tbl.='<td align="center" ><input type="text" style="width:60px" name="" id="txtipi_'.$i.'_'.$supp_id.'" class="text_boxes" value="'.$rows[csf('IPI')].'"></td>';
						$data_tbl.='<td align="center" ><input style="width:60px" name="" id="txtdelstart_'.$i.'_'.$supp_id.'" class="datepicker" value="'.$rows[csf('DEL_START')].'"></td>';
						$data_tbl.='<td align="center" ><input style="width:60px" name="" id="txtdelclose_'.$i.'_'.$supp_id.'" class="datepicker" value="'.$rows[csf('DEL_CLOSE')].'"></td>';
						$data_tbl.='<td align="center" ><input type="text" style="width:60px" name="" id="txtremarks_'.$i.'_'.$supp_id.'" class="text_boxes" value="'.$rows[csf('REMARKS')].'"></td>';
					}
				}
			}
		}
		$data_tbl.='</tr>';
		$i++;
	}

	$data_tbl.='</tbody></table>';
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
		
		$new_sys_no=explode("*",return_mrr_number( '', '', '', date("Y",time()), 5, "select sys_number_prefix,sys_number_prefix_num from req_comparative_mst where entry_form=523 $insert_date_con order by id desc ", "sys_number_prefix", "sys_number_prefix_num" ));

		$field_array_mst="id, sys_number, sys_number_prefix, sys_number_prefix_num, entry_form, basis_id, req_item_no,req_item_mst_id,req_item_dtls_id, rec_date, cs_date, supp_id, currency_id, cs_valid_date, source, ready_to_approved, approved, company_id, comments, inserted_by, insert_date, status_active, is_deleted";

		$txt_requisition_mst=implode(",",array_unique(explode(",",chop(str_replace("'","",$txt_requisition_mst),","))));
		$data_array_mst="(".$mst_id.",'".$new_sys_no[0]."','".$new_sys_no[1]."','".$new_sys_no[2]."',523,".$cbo_basis_name.",".$txt_requisition.",'".$txt_requisition_mst."',".$txt_requisition_dtls.",".$txt_rcvd_date.",".$txt_cs_date.",".$supplier_id.",".$cbo_currency_name.",".$txt_validity_date.",".$cbo_source.",".$cbo_ready_to_approved.",0,".$cbo_company_name.",".$txt_comments.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		//  echo "10**INSERT INTO req_comparative_mst (".$field_array_mst.") VALUES ".$data_array_mst; 
		// die;

		$field_array_dtls="id, mst_id, item_category_id, prod_id, fabric_cost_dtls_id, color_id, count_id, composition_id, yarn_type_id, req_qty, supp_data, inserted_by, insert_date, is_deleted, status_active";
		$field_array_supp_dtls="id, mst_id, dtls_id, supp_id, prod_id, quoted_price, neg_price, con_price, last_approval_rate, pay_term, tenor, pilling, csp, ipi, del_start, del_close, remarks, inserted_by, insert_date, is_deleted, status_active";

		$id_dtls=return_next_id("id", "req_comparative_dtls", 1);
		$id_supp_dtls=return_next_id("id", "req_comparative_supp_dtls", 1);
		$col_num_arr = explode(',',$supplier_id);
		$data_array_dtls='';
		$data_array_supp_dtls='';
		for($i=1; $i<=$row_num; $i++)
		{
			$supp_data='';			
			$txtcategory="txtcategory_".$i;
			$txtprod="txtprod_".$i;
			$txtreqdtlsid="txtreqdtlsid_".$i;
			$txtyarncolor ="txtyarncolor_".$i;
			$txtcount="txtcount_".$i;
			$txtcomposition="txtcomposition_".$i;
			$txtyarntype="txtyarntype_".$i;
			$txtqty="txtqty_".$i;	

			for($m=0; $m<$col_num; $m++)
			{
				$mm=str_replace("'","",$col_num_arr[$m]);
				$txtsuppier= "txtsuppier_".$i."_".$mm;
				$txtquoted="txtquoted_".$i."_".$mm;
				$txtneg="txtneg_".$i."_".$mm;
				$txtcon="txtcon_".$i."_".$mm;
				$txtPayTerm="txtPayTerm_".$i."_".$mm;
				$txtTenor="txtTenor_".$i."_".$mm;
				$txtpilling="txtpilling_".$i."_".$mm;
				$txtcsp="txtcsp_".$i."_".$mm;
				$txtipi="txtipi_".$i."_".$mm;
				$txtdelstart="txtdelstart_".$i."_".$mm;
				$txtdelclose="txtdelclose_".$i."_".$mm;
				$txtremarks="txtremarks_".$i."_".$mm;
				if($supp_data!=''){
					$supp_data.= "*".$$txtsuppier."_".$$txtquoted."_".$$txtneg."_".$$txtcon."_".$$txtPayTerm."_".$$txtTenor."_".$$txtpilling."_".$$txtcsp."_".$$txtipi."_".date("d-M-Y", strtotime(str_replace("'", "",  $$txtdelstart)))."_".date("d-M-Y", strtotime(str_replace("'", "",  $$txtdelclose)))."_".$$txtremarks;
				}else{
					$supp_data = $$txtsuppier."_".$$txtquoted."_".$$txtneg."_".$$txtcon."_".$$txtPayTerm."_".$$txtTenor."_".$$txtpilling."_".$$txtcsp."_".$$txtipi."_".date("d-M-Y", strtotime(str_replace("'", "",  $$txtdelstart)))."_".date("d-M-Y", strtotime(str_replace("'", "",  $$txtdelclose)))."_".$$txtremarks;
				}

				if ($data_array_supp_dtls!='') {$data_array_supp_dtls .=",";}
				$data_array_supp_dtls .="(".$id_supp_dtls.",".$mst_id.",".$id_dtls.",'".$$txtsuppier."','".$$txtprod."','".$$txtquoted."','".$$txtneg."','".$$txtcon."','".$$txtcon."','".$$txtPayTerm."','".$$txtTenor."','".$$txtpilling."','".$$txtcsp."','".$$txtipi."','".date("d-M-Y", strtotime(str_replace("'", "",  $$txtdelstart)))."','".date("d-M-Y", strtotime(str_replace("'", "",  $$txtdelclose)))."','".$$txtremarks."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,1)";
				$id_supp_dtls++;
			}

			if ($data_array_dtls!='') {$data_array_dtls .=",";}
			$data_array_dtls .="(".$id_dtls.",".$mst_id.",'".$$txtcategory."','".$$txtprod."','".$$txtreqdtlsid."','".$$txtyarncolor."','".$$txtcount."','".$$txtcomposition."','".$$txtyarntype."','".$$txtqty."','".$supp_data."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,1)";

			$id_dtls++;
		}
		//echo "</br>100**INSERT INTO req_comparative_supp_dtls (".$field_array_supp_dtls.") VALUES ".$data_array_supp_dtls;oci_rollback($con);die;
		//echo "</br>1000**INSERT INTO req_comparative_dtls (".$field_array_dtls.") VALUES ".$data_array_dtls; die;
		
		$rID=sql_insert("req_comparative_mst",$field_array_mst,$data_array_mst,0);
		$rID1=sql_insert("req_comparative_dtls",$field_array_dtls,$data_array_dtls,0);
		$rID2=sql_insert("req_comparative_supp_dtls",$field_array_supp_dtls,$data_array_supp_dtls,0);	
		//echo '</br>10**'.$rID.'**'.$rID1.'**'.$rID2;oci_rollback($con);die;
		
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

		$field_array_dtls="id, mst_id, item_category_id, prod_id, fabric_cost_dtls_id, color_id, count_id, composition_id, yarn_type_id, req_qty, supp_data, inserted_by, insert_date, is_deleted, status_active";
		$field_array_supp_dtls="id, mst_id, dtls_id, supp_id, prod_id, quoted_price, neg_price, con_price, last_approval_rate, pay_term, tenor, pilling, csp, ipi, del_start, del_close, remarks, inserted_by, insert_date, is_deleted, status_active";

		$id_dtls=return_next_id("id", "req_comparative_dtls", 1);
		$id_supp_dtls=return_next_id("id", "req_comparative_supp_dtls", 1);
		$col_num_arr = explode(',',$supplier_id);
		$data_array_dtls='';
		$data_array_supp_dtls='';
		
		for($i=1; $i<=$row_num; $i++)
		{
			$supp_data='';			
			$txtcategory="txtcategory_".$i;
			$txtprod="txtprod_".$i;
			$txtreqdtlsid="txtreqdtlsid_".$i;
			$txtyarncolor ="txtyarncolor_".$i;
			$txtcount="txtcount_".$i;
			$txtcomposition="txtcomposition_".$i;
			$txtyarntype="txtyarntype_".$i;
			$txtqty="txtqty_".$i;

			for($m=0; $m<$col_num; $m++)
			{
				$mm=str_replace("'","",$col_num_arr[$m]);
				$txtsuppier= "txtsuppier_".$i."_".$mm;
				$txtquoted="txtquoted_".$i."_".$mm;
				$txtneg="txtneg_".$i."_".$mm;
				$txtcon="txtcon_".$i."_".$mm;
				$txtPayTerm="txtPayTerm_".$i."_".$mm;
				$txtTenor="txtTenor_".$i."_".$mm;
				$txtpilling="txtpilling_".$i."_".$mm;
				$txtcsp="txtcsp_".$i."_".$mm;
				$txtipi="txtipi_".$i."_".$mm;
				$txtdelstart="txtdelstart_".$i."_".$mm;
				$txtdelclose="txtdelclose_".$i."_".$mm;
				$txtremarks="txtremarks_".$i."_".$mm;

				if($supp_data!=''){
					$supp_data.= "*".$$txtsuppier."_".$$txtquoted."_".$$txtneg."_".$$txtcon."_".$$txtPayTerm."_".$$txtTenor."_".$$txtpilling."_".$$txtcsp."_".$$txtipi."_".date("d-M-Y", strtotime(str_replace("'", "",  $$txtdelstart)))."_".date("d-M-Y", strtotime(str_replace("'", "",  $$txtdelclose)))."_".$$txtremarks;
				}else{
					$supp_data = $$txtsuppier."_".$$txtquoted."_".$$txtneg."_".$$txtcon."_".$$txtPayTerm."_".$$txtTenor."_".$$txtpilling."_".$$txtcsp."_".$$txtipi."_".date("d-M-Y", strtotime(str_replace("'", "",  $$txtdelstart)))."_".date("d-M-Y", strtotime(str_replace("'", "",  $$txtdelclose)))."_".$$txtremarks;
				}

				if ($data_array_supp_dtls!='') {$data_array_supp_dtls .=",";}
				$data_array_supp_dtls .="(".$id_supp_dtls.",".$update_id.",".$id_dtls.",'".$$txtsuppier."','".$$txtprod."','".$$txtquoted."','".$$txtneg."','".$$txtcon."','".$$txtcon."','".$$txtPayTerm."','".$$txtTenor."','".$$txtpilling."','".$$txtcsp."','".$$txtipi."','".date("d-M-Y", strtotime(str_replace("'", "",  $$txtdelstart)))."','".date("d-M-Y", strtotime(str_replace("'", "",  $$txtdelclose)))."','".$$txtremarks."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,1)";
				$id_supp_dtls++;

			}

			if ($data_array_dtls!='') {$data_array_dtls .=",";}
			$data_array_dtls .="(".$id_dtls.",".$update_id.",'".$$txtcategory."','".$$txtprod."','".$$txtreqdtlsid."','".$$txtyarncolor."','".$$txtcount."','".$$txtcomposition."','".$$txtyarntype."','".$$txtqty."','".$supp_data."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,1)";

			$id_dtls++;
		}

		$rID=sql_update("req_comparative_mst",$field_array_mst,$data_array_mst,"id","".$update_id."",0);
		$rID1=execute_query("delete from req_comparative_dtls where mst_id =".$update_id."",0);
		$rID2=execute_query("delete from req_comparative_supp_dtls where mst_id =".$update_id."",0);
		$rID3=sql_insert("req_comparative_dtls",$field_array_dtls,$data_array_dtls,0);	
		$rID4=sql_insert("req_comparative_supp_dtls",$field_array_supp_dtls,$data_array_supp_dtls,0);	
		//echo "10**INSERT INTO req_comparative_dtls (".$field_array_dtls.") VALUES ".$data_array_dtls; die;
		//echo "</br>10**".$rID.'='.$rID1.'='.$rID2.'='.$rID3.'='.$rID4."</br>"; die;
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
	$tbl_width=600+($supplier_count*880);
	$supplier_arr=return_library_array( "select supplier_name,id from lib_supplier where status_active=1 and is_deleted=0 order by supplier_name",'id','supplier_name');
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$country_arr = return_library_array("select id,country_name from lib_country where is_deleted=0","id","country_name");
	if($basis==1)
	{
		$sql = "select a.id as ID, a.item_category_id as ITEM_CATEGORY_ID, b.id as DTLS_ID, b.color_id as COLOR_ID, b.count_id as COUNT_ID, b.composition_id as COMPOSITION_ID, b.yarn_type_id as YARN_TYPE_ID, b.yarn_inhouse_date as YARN_INHOUSE_DATE, sum(b.quantity) as REQ_QTY
		from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b 
		where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=70 and b.id in($req_dtls)
		group by a.id, a.item_category_id, b.id, b.color_id, b.count_id, b.composition_id, b.yarn_type_id, b.yarn_inhouse_date
		order by a.id desc";
		
		$supp_prev_entry_data=array();

		/* if($update_id)
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
		} */
	}
	//echo $sql;die;
	$data_array=sql_select($sql);
	?>

	<table width="<?=$tbl_width;?>" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all"  id="tbl_details">
		<thead>
			<tr>
				<th rowspan="2" width="40">SL</th>
				<th rowspan="2" width="120">Yarn Color</th>
				<th rowspan="2" width="80">Count</th>
				<th rowspan="2" width="150">Composition</th>
				<th rowspan="2" width="130">Yarn Type</th>
				<th rowspan="2" width="80">Req. Qty.</th>
				<?
				foreach($supplier as $row)
				{
					?>
					<th colspan="11" width="880"><? echo $supplier_arr[$row]; ?></th>
					<?
				}
				?>
			</tr>
			<tr>
				<?
				foreach($supplier as $row)
				{
					?>
					<th width="80">Quoted Price</th>
					<th width="80">Last Price</th>
					<th width="80">Con. Price</th>
					<th width="80">Pay Term</th>
					<th width="80">Tenor</th>
					<th width="80">Pilling</th>
					<th width="80">CSP</th>
					<th width="80">IPI</th>
					<th width="80">Del.Start</th>
					<th width="80">Del.Close</th>
					<th width="80">Remarks</th>
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
			?>
			<tr class="general" id="<? echo $i;?>">
				<td align="center" ><?= $i;?>
				<input type="hidden" name="" id="txtprod_<?= $i;?>" value="<? echo $row['PROD_IDS'] ;?>" >
				<input type="hidden" name="" id="txtcategory_<?= $i;?>" value="<? echo $row['ITEM_CATEGORY_ID'] ;?>" >
				<input type="hidden" name="" id="txtreqdtlsid_<?= $i;?>" value="<? echo $row['DTLS_ID']; ?>">
				</td>
				<td align="center" >
					<input type="text" style="width:100px" name="" id="txtyarncolor_<?= $i;?>" class="text_boxes" title="<? echo $row['COLOR_ID'] ;?>" value="<? echo $color_arr[$row['COLOR_ID']]; ?>" readonly>
				</td>
				<td align="center" >
					<input type="text" style="width:70px" name="" id="txtcount_<?= $i;?>" class="text_boxes" title="<? echo $row['COUNT_ID'] ;?>" value="<? echo $count_arr[$row['COUNT_ID']]; ?>" readonly>
				</td>
				<td align="center" >
					<input type="text" style="width:130px" name="" id="txtcomposition_<?= $i;?>" class="text_boxes" title="<? echo $row['COMPOSITION_ID'] ;?>" value="<? echo $composition[$row['COMPOSITION_ID']]; ?>" readonly>
				</td>
				<td align="center" >
					<input type="text" style="width:110px" name="" id="txtyarntype_<?= $i;?>" class="text_boxes" title="<? echo $row['YARN_TYPE_ID'] ;?>" value="<? echo $yarn_type[$row['YARN_TYPE_ID']]; ?>" readonly>
				</td>
				<td align="center" >
					<input type="text" style="width:70px" name="" id="txtqty_<?= $i;?>" class="text_boxes_numeric" value="<? echo $row['REQ_QTY']; ?>" readonly>				
				</td>				
			    <?
				foreach($supplier as $sup_id)
				{
					?>
					<td align="center" ><input type="text" style="width:60px" name="" id="txtquoted_<?= $i.'_'.$sup_id;?>" value="<?= $supp_prev_entry_data[$row["PROD_ID"]][$sup_id]["quoted_price"];?>" class="text_boxes_numeric"></td>
					<td align="center" ><input type="text" style="width:60px" name="" id="txtneg_<?= $i.'_'.$sup_id;?>"  value="<?= $product_id_array[$key][$sup_id];?>" class="text_boxes_numeric" placeholder="Display" readonly></td>
					<td align="center" ><input type="text" style="width:60px" name="" id="txtcon_<?= $i.'_'.$sup_id;?>"  value="<?= $supp_prev_entry_data[$row["PROD_ID"]][$sup_id]["con_price"];?>" class="text_boxes_numeric"></td>
					<td align="center" >
						<? echo create_drop_down("txtPayTerm_".$i.'_'.$sup_id, 80, $pay_term, "", 1, "-- Select --", $supp_prev_entry_data[$row["PROD_ID"]][$sup_id]["pay_term"] , "", ""); ?>
					</td>
					<td align="center" ><input type="text" style="width:60px" name="" id="txtTenor_<?= $i.'_'.$sup_id;?>"  value="<?= $supp_prev_entry_data[$row["PROD_ID"]][$sup_id]["tenor"];?>" class="text_boxes_numeric" ></td>
					<td align="center" ><input type="text" style="width:60px" name="" id="txtpilling_<?= $i.'_'.$sup_id;?>"  value="<?= $supp_prev_entry_data[$row["PROD_ID"]][$sup_id]["brand"];?>" class="text_boxes" ></td>
					<td align="center" ><input type="text" style="width:60px" name="" id="txtcsp_<?= $i.'_'.$sup_id;?>"  value="<?= $supp_prev_entry_data[$row["PROD_ID"]][$sup_id]["model"];?>" class="text_boxes" ></td>						
					<td align="center" ><input type="text" style="width:60px" name="" id="txtipi_<?= $i.'_'.$sup_id;?>"  value="<?= $supp_prev_entry_data[$row["PROD_ID"]][$sup_id]["origin"];?>" class="text_boxes" ></td>
					<td align="center" ><input style="width:60px" name="" id="txtdelstart_<?= $i.'_'.$sup_id;?>"  value="<?= $row["YARN_INHOUSE_DATE"]; ?>" class="datepicker" ></td>
					<td align="center" ><input style="width:60px" name="" id="txtdelclose_<?= $i.'_'.$sup_id;?>"  value="<?= $row["YARN_INHOUSE_DATE"]; ?>" class="datepicker" ></td>
					<td align="center" ><input type="text" style="width:60px" name="" id="txtremarks_<?= $i.'_'.$sup_id;?>"  value="<?= $supp_prev_entry_data[$row["PROD_ID"]][$sup_id]["origin"];?>" class="text_boxes" ></td>
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

	$update_id_cond='';
	if ($update_id != '') $update_id_cond=" and a.id <> $update_id";

	if($basis==1)
	{
		$sql = "select a.id as ID, a.cs_valid_date as CS_VALID_DATE, b.id as DTLS_ID, b.item_category_id as ITEM_CATEGORY_ID, b.color_id as COLOR_ID, b.count_id as COUNT_ID, b.composition_id as COMPOSITION_ID, b.yarn_type_id as YARN_TYPE_ID, b.req_qty as REQ_QTY
		from req_comparative_mst a, req_comparative_dtls b 
		where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=523 and a.basis_id=$basis $update_id_cond
		order by a.id desc";
		$sql_items_res=sql_select($sql);
		$item_names_arr=array();
		foreach ($sql_items_res as $row) 
		{
			$items=$row['ITEM_CATEGORY_ID'].'**'.$row['COLOR_ID'].'**'.$row['COUNT_ID'].'**'.$row['COMPOSITION_ID'].'**'.$row['YARN_TYPE_ID'];
			if ($check_item[$items] == "")
			{
				$check_item[$items]=$items;
				$item_names_arr[$items]['ITEMS']=$items;
				$item_names_arr[$items]['DTLS_ID']=$row['DTLS_ID'];
				$item_names_arr[$items]['CS_VALID_DATE']=$row['CS_VALID_DATE'];
			}		
		}

		$sql = "select a.id as ID, b.id as DTLS_ID, b.item_category_id as ITEM_CATEGORY_ID, b.color_id as COLOR_ID, b.count_id as COUNT_ID, b.composition_id as COMPOSITION_ID, b.yarn_type_id as YARN_TYPE_ID, b.req_qty as REQ_QTY
		from req_comparative_mst a, req_comparative_dtls b 
		where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=523 and a.basis_id=$basis and a.id in($req_mst)		
		order by a.id desc";

		$dtls_data=sql_select($sql);
		$data_array=array(); 
		foreach($dtls_data as $row)
		{				
			$data_ref=$row['ITEM_CATEGORY_ID'].'**'.$row['COLOR_ID'].'**'.$row['COUNT_ID'].'**'.$row['COMPOSITION_ID'].'**'.$row['YARN_TYPE_ID'];
			$data_array[$data_ref]["ITEM_CATEGORY_ID"]=$row["ITEM_CATEGORY_ID"];
			$data_array[$data_ref]["COLOR_ID"]=$row["COLOR_ID"];
			$data_array[$data_ref]["COUNT_ID"]=$row["COUNT_ID"];
			$data_array[$data_ref]["COMPOSITION_ID"]=$row["COMPOSITION_ID"];			
			$data_array[$data_ref]["YARN_TYPE_ID"]=$row["YARN_TYPE_ID"];				
		}
			
	}
	// echo $sql;die;
	$data_array=sql_select($sql);
	?>

	<table width="700" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all">
		<thead>
			<tr>
				<th width="40">SL</th>
				<th width="150">Yarn Composition</th>
				<th width="100">Yarn Count</th>
				<th width="100">Yarn Type</th>
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
			if ($items==$item_names_arr[$items]['ITEMS'])
			{
				$dtls_id=$item_names_arr[$items]['DTLS_ID'];
				$supp_dtls_arr=sql_select("select supp_id as SUPP_ID, supp_type as SUPP_TYPE, last_approval_rate as LAST_APPROVAL_RATE from req_comparative_supp_dtls where dtls_id in($dtls_id) and last_approval_rate is not null and status_active=1 and is_deleted=0");
				$row_num=count($supp_dtls_arr);
				$cs_valid_date=change_date_format($item_names_arr[$items]['CS_VALID_DATE']);
			}
			if ($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
			?>
				<tr>
					<td width="40" align="center" rowspan="<?= $row_num; ?>"><?= $i;?>
					<td width="300" rowspan="<?= $row_num; ?>"><? echo $composition[$row['COMPOSITION_ID']]; ?></td>
					<td width="300" rowspan="<?= $row_num; ?>"><? echo $count_arr[$row['COUNT_ID']]; ?></td>
					<td width="300" rowspan="<?= $row_num; ?>"><? echo $yarn_type[$row['YARN_TYPE_ID']]; ?></td>
					<?
					if ($row_num > 0)
					{	
						foreach ($supp_dtls_arr as $val) 
						{
							?>
							<td width="150">
								<?
								$supp_name='';
								if ($val['SUPP_TYPE']==1) $supp_name=$supplier_arr[$val['SUPP_ID']];
								else if ($val['SUPP_TYPE']==2) $supp_name=$company_arr[$val['SUPP_ID']];
								echo $supp_name; 
								?>									
							</td>
							<td width="80" align="right"><? echo $val['LAST_APPROVAL_RATE']; ?></td>
							<td width="80" align="center"><? echo $cs_valid_date; ?></td>
							</tr>
							<?
						}
					}
					else
					{
						?>
						<td width="150"></td>
						<td width="100"></td>
						<td width="100"></td>
						<td width="80"></td>
						<td width="80"></td>
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
			
            show_list_view ( document.getElementById('cbo_item_category').value+'**'+document.getElementById('txt_req_no').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+document.getElementById('cbo_year_selection').value+'**'+'<? echo $txt_requisition_mst; ?>'+'**'+'<? echo $txt_requisition_dtls; ?>'+'**'+'<? echo $update_id; ?>','requisition_list_view', 'search_div', 'comparative_statement_yarn_controller', 'setFilterGrid(\'list_view\',-1)');
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
                                    echo create_drop_down( "cbo_item_category", 150,"select category_id, short_name from  lib_item_category_list where status_active=1 and is_deleted=0 and category_id in (1) order by short_name","category_id,short_name", 1, "-- Select --", $selected, "",'',"","","","");
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
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');

	$category_cond ='';$req_num ='';$year_cond="";
	if($category_id !=0) {$category_cond = "and a.item_category_id in ($category_id)";}
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
	
	$sql = "select a.id, a.requ_prefix_num, a.requisition_date, a.company_id, a.item_category_id, b.id as dtls_id, b.color_id, b.count_id, b.composition_id, b.yarn_type_id
	from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b 
	where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=70 $category_cond $req_num $date_cond $year_cond 
	order by a.id desc";
	//echo $sql;//die;
	unset($req_dtls_id);
	$data_array=sql_select($sql);		
	?>
	<table width="1050" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all">
        <thead>
            <th width="40">SL No</th>
            <th width="60">Requisition No</th>
            <th width="60">Company</th>
            <th width="70">Requisition Date</th>
            <th width="100">Yarn Color</th>
            <th width="80">Count</th>
            <th width="150">Composition</th>
            <th width="150">Yarn Type</th>
        </thead>
     </table>
     <div style="width:1070px; overflow-y:scroll; max-height:260px">
     	<table width="1050" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" id="tbl_list_view_req">
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
						<td width="60" align="center"><p><? echo $lib_company_arr[$row[csf('company_id')]]; ?></p></td>
						<td width="70" align="center"><p><? echo change_date_format($row[csf('requisition_date')]); ?></p></td>
						<td width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
						<td width="80"><p><? echo $count_arr[$row[csf("count_id")]]; ?></td>
						<td width="150"><p><? echo $composition[$row[csf("composition_id")]]; ?></p></td>
						<td width="150"><p><? echo $yarn_type[$row[csf("yarn_type_id")]]; ?></p></td>			
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
//========== End Requisition No ========

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
                            $data_sql=sql_select("select c.supplier_name,c.id from lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and b.party_type=2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name");
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

//========== Print Button ========
if($action=="comparative_statement_print")
{
	extract($_REQUEST);
    $data=explode('*',$data);
    $mst_id=$data[0];
	$company_ids=$data[1];
	$cbo_template_id=$data[2];
    echo load_html_head_contents($data[1],"../../../", 1, 1, $unicode,'','');
	//   print_r ($data); die; 
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$supplier_contact_arr=return_library_array( "select id, contact_no from lib_supplier",'id','contact_no');
	$lib_company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$country_arr = return_library_array("select id,country_name from lib_country where is_deleted=0","id","country_name");
	$data_array=sql_select("select id, sys_number, sys_number_prefix, sys_number_prefix_num, basis_id, req_item_no,req_item_mst_id,req_item_dtls_id, rec_date, cs_date, supp_id, currency_id, cs_valid_date, source, approved, company_id, comments from req_comparative_mst where id='$mst_id' and is_deleted=0 and status_active=1");
	$req_mst_ids=$data_array[0][csf("req_item_mst_id")];
	$req_dtls_ids=$data_array[0][csf("req_item_dtls_id")];

	$sql_group=sql_select("select id, group_name, address from lib_group where id in($company_ids) and is_deleted=0 and status_active=1");
	$group_name=$sql_group[0][csf("group_name")];
	$group_id=$sql_group[0][csf("id")];
	$group_address=$sql_group[0][csf("address")];

	$group_logo = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='group_logo' and is_deleted=0 and master_tble_id='$group_id'", "image_location");

	$sql_requ="select a.id as ID, a.requ_no as REQU_NO, a.requisition_date as REQUISITION_DATE, a.basis as BASIS from inv_purchase_requisition_mst a where a.id in($req_mst_ids) and a.entry_form=70 and a.is_deleted=0 and a.status_active=1";
	$sql_requ_res=sql_select($sql_requ);
	$requisition_arr=array();
	foreach($sql_requ_res as $row)
	{
		$requisition_arr[$row['ID']]['REQU_NO']=$row['REQU_NO'];
		$requisition_arr[$row['ID']]['REQUISITION_DATE']=$row['REQUISITION_DATE'];
		$basis_array[$row['BASIS']]=$row['BASIS'];
	}

	$tna_date_task_arr=array();
	foreach($basis_array as $basis_id)
	{
		if ($basis_id==1)
		{
			$sql="select f.JOB_QUANTITY as JOB_QUANTITY, f.BUYER_NAME as BUYER_ID, e.ID as ORDER_ID, e.JOB_NO_MST as JOB_NO, e.IS_CONFIRMED as ORDER_STATUS, e.PUB_SHIPMENT_DATE as SHIPMENT_DATE, c.BOOKING_NO as BOOKING_NO, c.BOOKING_DATE as BOOKING_DATE, d.CONSTRUCTION as CONSTRUCTION, d.GSM_WEIGHT as GSM, a.ID as REQUISITION_ID, a.REQU_NO as REQUISITION_NO, a.REQUISITION_DATE as REQUISITION_DATE, b.id as REQUISITION_DTLS_ID from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, wo_booking_mst c, wo_booking_dtls d, wo_po_break_down e, wo_po_details_master f where a.id=b.mst_id and b.booking_no=c.booking_no and c.booking_no=d.booking_no and d.po_break_down_id=e.id and e.job_id=f.id and a.entry_form=70 and a.id in($req_mst_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 order by e.pub_shipment_date desc";//min ship date
			$sql_res=sql_select($sql);
			$all_data_arr=array();
			$requisition_dtls_job_arr=array();
			foreach($sql_res as $row)
			{
				if ($check_buyer_id[$row['BUYER_ID']]==''){
					$check_buyer_id[$row['BUYER_ID']]=$row['BUYER_ID'];
					$all_data_arr[$row['REQUISITION_ID']]['BUYER_NAME'].=$buyer_arr[$row['BUYER_ID']].',';					
				}

				if ($check_order_status[$row['ORDER_STATUS']]==''){
					$check_order_status[$row['ORDER_STATUS']]=$row['ORDER_STATUS'];
					$all_data_arr[$row['REQUISITION_ID']]['ORDER_STATUS'].=$order_status[$row['ORDER_STATUS']].',';					
				}

				$all_data_arr[$row['REQUISITION_ID']]['SHIPMENT_DATE']=$row['SHIPMENT_DATE'];
				$all_data_arr[$row['REQUISITION_ID']]['ORDER_ID']=$row['ORDER_ID'];
				$order_id=$row['ORDER_ID'];

				if ($check_booking_no[$row['BOOKING_NO']]==''){
					$check_booking_no[$row['BOOKING_NO']]=$row['BOOKING_NO'];
					$all_data_arr[$row['REQUISITION_ID']]['BOOKING_NO'].=$row['BOOKING_NO'].',';					
				}

				if ($check_booking_date[$row['BOOKING_DATE']]==''){
					$check_booking_date[$row['BOOKING_DATE']]=$row['BOOKING_DATE'];
					$all_data_arr[$row['REQUISITION_ID']]['BOOKING_DATE'].=change_date_format($row['BOOKING_DATE']).',';					
				}

				if ($check_job_no[$row['JOB_NO']]==''){
					$check_job_no[$row['JOB_NO']]=$row['JOB_NO'];
					$all_data_arr[$row['REQUISITION_ID']]['JOB_NO'].=$row['JOB_NO'].',';
					$all_data_arr[$row['REQUISITION_ID']]['JOB_QUANTITY']+=$row['JOB_QUANTITY'];
					$job_no="'".$row['JOB_NO']."'";	
					$job_nos.=$job_no.',';
				}				

				$all_data_arr[$row['REQUISITION_ID']]['CONSTRUCTION']=$row['CONSTRUCTION'];
				$all_data_arr[$row['REQUISITION_ID']]['GSM']=$row['GSM'];
				$all_data_arr[$row['REQUISITION_ID']]['REQUISITION_NO']=$row['REQUISITION_NO'];
				$all_data_arr[$row['REQUISITION_ID']]['REQUISITION_DATE']=$row['REQUISITION_DATE'];
				$requisition_dtls_job_arr[$row['REQUISITION_DTLS_ID']]=$row['JOB_NO'];
			}

			$job_nos=rtrim($job_nos,',');

			if ($job_nos != "")
			{
				$sql_precost_yarn="select job_no as JOB_NO, rate as RATE from wo_pre_cost_fab_yarn_cost_dtls where job_no in($job_nos) and status_active=1 and is_deleted=0 order by rate asc";
				$sql_precost_yarn_res=sql_select($sql_precost_yarn);
				$precost_rate_arr=array();
				foreach($sql_precost_yarn_res as $row)
				{
					$precost_rate_arr[$row['JOB_NO']]=$row['RATE'];
				}
			}

			if ($order_id != "")
			{
				$tna_start_sql=sql_select( "select id, po_number_id, 
				(case when task_number=48 then task_start_date else null end) as yarn_allocation_start_date,
				(case when task_number=48 then task_finish_date else null end) as yarn_allocation_end_date
				from tna_process_mst
				where status_active=1 and po_number_id in($order_id)");
				foreach($tna_start_sql as $row)
				{
					if($row[csf("yarn_allocation_start_date")]!="" && $row[csf("yarn_allocation_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['yarn_allocation_start_date']=$row[csf("yarn_allocation_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['yarn_allocation_end_date']=$row[csf("yarn_allocation_end_date")];
					}
				}
			}
			


		}
	}
	//echo '<pre>';print_r($all_data_arr2);

	$sql_dtls="select a.id as ID, a.sys_number as SYS_NUMBER, a.supp_id as SUPP_ID, b.id as DTLS_ID, b.req_qty as REQ_QTY, b.count_id as COUNT_ID, b.composition_id as COMPOSITION_ID, b.yarn_type_id as YARN_TYPE_ID, b.fabric_cost_dtls_id as REQU_DTLS_ID from req_comparative_mst a, req_comparative_dtls b, inv_purchase_requisition_dtls c where a.id=b.mst_id and b.fabric_cost_dtls_id=c.id and c.id in($req_dtls_ids) and a.entry_form=523 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.status_active=1 and c.is_deleted=0";
	$sql_dtls_res=sql_select($sql_dtls);
	$dtls_data_arr=array();
	foreach($sql_dtls_res as $row)
	{
		$supp_ids=$row['SUPP_ID'];
		$dtls_id.=$row['DTLS_ID'].',';
		$dtls_data_arr[$row['REQU_DTLS_ID']]['DTLS_ID']=$row['DTLS_ID'];
		$dtls_data_arr[$row['REQU_DTLS_ID']]['REQ_QTY']=$row['REQ_QTY'];
		$dtls_data_arr[$row['REQU_DTLS_ID']]['COUNT_ID']=$row['COUNT_ID'];
		$dtls_data_arr[$row['REQU_DTLS_ID']]['COMPOSITION_ID']=$row['COMPOSITION_ID'];
		$dtls_data_arr[$row['REQU_DTLS_ID']]['YARN_TYPE_ID']=$row['YARN_TYPE_ID'];
	}
	$rowspan_supp=count(explode(',',$supp_ids));
	$dtls_id=rtrim($dtls_id,',');

	$sql_supp_dtls="select dtls_id as DTLS_ID, supp_id as SUPP_ID, con_price as CON_PRICE, tenor as TENOR, del_start as DEL_START, del_close as DEL_CLOSE, pilling as PILLING, csp as CSP, ipi as IPI, remarks as REMARKS from req_comparative_supp_dtls where dtls_id in($dtls_id) and is_deleted=0 and status_active=1";
	$sql_supp_dtls_res=sql_select($sql_supp_dtls);
	$supp_dtls_arr=array();
	foreach($sql_supp_dtls_res as $row)
	{
		$supp_dtls_arr[$row['DTLS_ID']][$row['SUPP_ID']]['CON_PRICE']=$row['CON_PRICE'];
		$supp_dtls_arr[$row['DTLS_ID']][$row['SUPP_ID']]['TENOR']=$row['TENOR'];
		$supp_dtls_arr[$row['DTLS_ID']][$row['SUPP_ID']]['DEL_START']=$row['DEL_START'];
		$supp_dtls_arr[$row['DTLS_ID']][$row['SUPP_ID']]['DEL_CLOSE']=$row['DEL_CLOSE'];
		$supp_dtls_arr[$row['DTLS_ID']][$row['SUPP_ID']]['PILLING']=$row['PILLING'];
		$supp_dtls_arr[$row['DTLS_ID']][$row['SUPP_ID']]['CSP']=$row['CSP'];
		$supp_dtls_arr[$row['DTLS_ID']][$row['SUPP_ID']]['IPI']=$row['IPI'];
		$supp_dtls_arr[$row['DTLS_ID']][$row['SUPP_ID']]['REMARKS']=$row['REMARKS'];
	}
	//echo '<pre>';print_r($supp_dtls_arr);
	
	$sql_rec="select a.requisition_dtls_id as REQUISITION_DTLS_ID, b.receive_date as RECEIVE_DATE, b.supplier_id as SUPPLIER_ID, d.lot as LOT from wo_non_order_info_dtls a, inv_receive_master b, inv_transaction c, product_details_master d where a.mst_id=b.booking_id and b.id=c.mst_id and c.prod_id=d.id and a.item_category_id=1 and b.entry_form=1 and b.item_category=1 and c.item_category=1 and c.transaction_type=1 and d.item_category_id=1 and a.requisition_dtls_id in($req_dtls_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 order by b.receive_date";
	$sql_rec_res=sql_select($sql_rec);
	foreach($sql_rec_res as $row)
	{
		$receive_data_arr[$row['REQUISITION_DTLS_ID']]['RECEIVE_DATE']=$row['RECEIVE_DATE'];
		$receive_data_arr[$row['REQUISITION_DTLS_ID']]['SUPPLIER_ID']=$row['SUPPLIER_ID'];
		$receive_data_arr[$row['REQUISITION_DTLS_ID']]['LOT']=$row['LOT'];
	}
	?>        
    <table width="1200" cellspacing="0" align="left">
		<tr>
            <td rowspan="3" width="40" align="left">
                <img src="../../<? echo $group_logo; ?>" height="70" width="100">
			</td>
            <td style="font-size:22px;" align="center"><strong><? echo $group_name; ?></strong></td>            
        </tr>	
		<tr>
            <td align="center" ><strong>Comparative Statement Yarn</strong></td>
	    </tr>
		<tr>
            <td align="center" ><strong>Item: Yarn</strong></td>
	    </tr>		
	</table>
	<br>
	<table width="1200" cellspacing="0" align="left">
		<tr>
            <td width="400" align="left" ><strong>CS Number:&nbsp;<? echo $data_array[0][csf('sys_number')]; ?></strong></td>            
            <td width="400" ><strong>CS Date:&nbsp;<? echo change_date_format($data_array[0][csf('cs_date')]); ?></strong></td>
			<td width="400" >Print Date:&nbsp;<? echo date("d-m-Y h:i:sa"); ?></td>			
        </tr>
	</table>
	<br>	   
	<table width="1200" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
		<thead bgcolor="#dddddd" align="center">
			<tr>
				<th width="720" colspan="9" align="center">Order Specification</th>
				<th width="320" colspan="4" align="center">Fabric Specification</th>
				<th width="160" colspan="2" align="center">TNA</th>					
			</tr>
			<tr>
				<th width="80" align="center">Buyer</th>
				<th width="80" align="center">Order Status</th>
				<th width="80" align="center">Booking No</th>
				<th width="80" align="center">Booking Date</th>
				<th width="80" align="center">Job No</th>
				<th width="80" align="center">Requ. No.</th>
				<th width="80" align="center">Requ. Date</th>
				<th width="80" align="center">Garments Qty</th>
				<th width="80" align="center">SC/LC Number</th>
				<th width="80" align="center">Shipment Date</th>
				<th width="80" align="center">Construction</th>
				<th width="80" align="center">Gsm</th>
				<th width="80" align="center">Embel. Application</th>
				<th width="80" align="center">TNA Start</th>
				<th width="80" align="center">TNA Close</th>					
			</tr>			
		</thead>
		<tbody>
			<?
			foreach($all_data_arr as $requisition_id => $row)
			{
				?>
				<tr>
					<td width="80" align="center"><? echo rtrim($row['BUYER_NAME'],','); ?></td>
					<td width="80" align="center"><? echo rtrim($row['ORDER_STATUS'],','); ?></td>
					<td width="80" align="center"><? echo rtrim($row['BOOKING_NO'],','); ?></td>
					<td width="80" align="center"><? echo rtrim($row['BOOKING_DATE'],','); ?></td>
					<td width="80" align="center"><? echo rtrim($row['JOB_NO'],','); ?></td>
					<td width="80" align="center"><? echo $row['REQUISITION_NO']; ?></td>
					<td width="80" align="center"><? echo change_date_format($row['REQUISITION_DATE']); ?></td>
					<td width="80" align="right"><? echo $row['JOB_QUANTITY']; ?></td>
					<td width="80" align="center"></td>
					<td width="80" align="center"><? echo change_date_format($row['SHIPMENT_DATE']); ?></td>
					<td width="80" align="center"><? echo $row['CONSTRUCTION']; ?></td>
					<td width="80" align="center"><? echo $row['GSM']; ?></td>
					<td width="80" align="center"></td>
					<td width="80" align="center"><? echo change_date_format($tna_date_task_arr[$row['ORDER_ID']]['yarn_allocation_start_date']); ?></td>
					<td width="80" align="center"><? echo change_date_format($tna_date_task_arr[$row['ORDER_ID']]['yarn_allocation_start_date']); ?></td>
				</tr>
				<?
			}
			?>	
		</tbody>
	</table>
	<br>
	<table width="1880" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
		<thead bgcolor="#dddddd" align="center">
			<tr>
				<th width="300" colspan="3" align="center">Required Item Details</th>
				<th width="160" colspan="2" align="center">Inventory Status</th>
				<th width="1260" colspan="14" align="center">Comparative Statement</th>
				<th width="80"></th>
				<th width="80"></th>						
			</tr>
			<tr>
				<th width="100" rowspan="2" align="center">Count</th>
				<th width="100" rowspan="2" align="center">Composition</th>
				<th width="100" rowspan="2" align="center">Type</th>
				<th width="80" rowspan="2" align="center">Required Qnty</th>
				<th width="80" rowspan="2" align="center">Un-allocate Yarn stock</th>
				<th width="80" rowspan="2" align="center">Last Purchse Price</th>
				<th width="80" rowspan="2" align="center">Last Lot No</th>
				<th width="80" rowspan="2" align="center">Last Purchse Supplier</th>
				<th width="80" rowspan="2" align="center">Last Purchse Date</th>
				<th width="200" rowspan="2" align="center">Supplier</th>
				<th width="80" rowspan="2" align="center">Price</th>
				<th width="80" rowspan="2" align="center">Payment Terms</th>
				<th width="90" rowspan="2" align="center">Del.Start</th>
				<th width="90" rowspan="2" align="center">Del.Close</th>
				<th width="240" colspan="3" align="center">Quality Peramiter</th>	
				<th width="80" rowspan="2" align="center">Note</th>
				<th width="80" rowspan="2" align="center">Budgeted Price ($)</th>
				<th width="80" rowspan="2" align="center">Impact Per Unit Price ($)</th>
				<th width="80" rowspan="2" align="center">Total Impact Value ($)</th>
			</tr>
			<tr>
				<th width="80" align="center">Pilling</th>
				<th width="80" align="center">CSP</th>
				<th width="80" align="center">IPI</th>
			</tr>				
		</thead>
		<tbody>
			<?
			foreach($dtls_data_arr as $requisition_dtls_id => $row)
			{
				?>
				<tr>
					<td width="100" rowspan="<? echo $rowspan_supp; ?>"><? echo $count_arr[$row['COUNT_ID']]; ?></td>
					<td width="100" rowspan="<? echo $rowspan_supp; ?>"><? echo $composition[$row['COMPOSITION_ID']]; ?></td>
					<td width="100" rowspan="<? echo $rowspan_supp; ?>"><? echo $yarn_type[$row['YARN_TYPE_ID']]; ?></td>
					<td width="80" rowspan="<? echo $rowspan_supp; ?>" align="right"><? echo $row['REQ_QTY']; ?></td>
					<td width="80" rowspan="<? echo $rowspan_supp; ?>" align="center"><?  ?></td>
					<td width="80" rowspan="<? echo $rowspan_supp; ?>" align="center"><?  ?></td>
					<td width="80" rowspan="<? echo $rowspan_supp; ?>" align="center"><? echo $receive_data_arr[$requisition_dtls_id]['LOT']; ?></td>
					<td width="80" rowspan="<? echo $rowspan_supp; ?>" align="center"><? echo $supplier_arr[$receive_data_arr[$requisition_dtls_id]['SUPPLIER_ID']]; ?></td>
					<td width="80" rowspan="<? echo $rowspan_supp; ?>" align="center"><? echo change_date_format($receive_data_arr[$requisition_dtls_id]['RECEIVE_DATE']); ?></td>

					<?
					$m=1;
					foreach($supp_dtls_arr as $dtls_id => $supp_data)
					{
						foreach($supp_data as $supp_id => $val)
						{							
							if ($dtls_id==$row['DTLS_ID'])
							{
								?>								
								<td width="200" align="left"><? echo $supplier_arr[$supp_id]; ?></td>
								<td width="80" align="center"><? echo $val['CON_PRICE']; ?></td>
								<td width="80" align="center"><? echo $val['TENOR']; ?></td>
								<td width="90" align="center"><? echo change_date_format($val['DEL_START']); ?></td>
								<td width="90" align="center"><? echo change_date_format($val['DEL_CLOSE']); ?></td>
								<td width="80" align="left"><? echo $val['PILLING']; ?></td>
								<td width="80" align="left"><? echo $val['CSP']; ?></td>
								<td width="80" align="left"><? echo $val['IPI']; ?></td>
								<td width="80" align="left"><? echo $val['REMARKS']; ?></td>
								<? if($m==1){  ?>
								<td width="80" rowspan="<? echo $rowspan_supp; ?>" align="center"><? echo $precost_rate_arr[$requisition_dtls_job_arr[$requisition_dtls_id]]; ?></td>
								<td width="80" rowspan="<? echo $rowspan_supp; ?>" align="center"><?  ?></td>
								<td width="80" rowspan="<? echo $rowspan_supp; ?>" align="center"><?  ?></td>
								</tr>
								<? } ?>								
								<?
								$m++;
							}
						}
					}
					?>				
				</tr>
				<?
			}
			?>
		</tbody>
	</table>
	<div>
	Note: The above price including VAT, AIT & delivery charge upto site.
	</div>
	<?
	echo signature_table(523, 0,"900px",$cbo_template_id,20);
	?>
	<?
	exit();
}
