<?
session_start();
include('../../includes/common.php');
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];
//************************************ Start *************************************************

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 152, "select id, location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/trims_issue_requisition_controller', this.value+'__'+document.getElementById('cbo_company_name').value, 'load_drop_down_store', 'store_id' );" );
	exit();
}
if ($action=="load_drop_down_store")
{
	$data_ref=explode("__",$data);
	$location_id=$data_ref[0];
	$company_id=$data_ref[1];
	echo create_drop_down( "cbo_store_name", 152, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id=$company_id and a.location_id=$location_id and b.category_type=4 and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", $selected, "" );
	exit();
}
if ($action=="load_drop_down_working_location")
{
	echo create_drop_down( "cbo_working_location", 152, "select id, location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected,"load_drop_down( 'requires/trims_issue_requisition_controller', this.value+'__'+document.getElementById('cbo_working_company').value, 'load_drop_down_floor', 'floor_td' );" );
	exit();
}

if ($action=="load_drop_down_floor")
{
	$data_ref=explode("__",$data);
	$location_id=$data_ref[0];
	$company_id=$data_ref[1];
	echo create_drop_down( "cbo_floor_name", 152, "SELECT id,floor_name from lib_prod_floor where company_id=$company_id and location_id=$location_id and production_process=5 and status_active=1 and is_deleted=0 ","id,floor_name", 1, "-- Select Floor --", $selected, "load_drop_down( 'requires/trims_issue_requisition_controller', this.value+'__'+document.getElementById('cbo_working_company').value+'__'+document.getElementById('cbo_working_location').value, 'load_drop_down_sewing_line', 'sewing_td' );" );
	exit();
}
if($action=="load_drop_down_sewing_line")
{
	$explode_data = explode("__",$data);
	$location = $explode_data[2];
	$company = $explode_data[1];
	$floor = $explode_data[0]; 

		$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
		$line_array=array();

		$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.company_id='$company' and a.location_id='$location' and a.floor_id='$floor' and a.is_deleted=0 and b.is_deleted=0  group by a.id, a.line_number");
		//echo $line_data;die;

		$line_merge=9999;
		foreach($line_data as $row)
		{
			$line='';
			$line_number=explode(",",$row[csf('line_number')]);
			foreach($line_number as $val)
			{
				if(count($line_number)>1)
				{
					$line_merge++;
					$new_arr[$line_merge]=$row[csf('id')];
				}
				else
					$new_arr[$line_library[$val]]=$row[csf('id')];
				if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
			}
			$line_array[$row[csf('id')]]=$line;
		}
		//print_r($new_arr);
		sort($new_arr);
		foreach($new_arr as $key=>$v)
		{
			$line_array_new[$v]=$line_array[$v];
		}
		echo create_drop_down( "cbo_sewing_line", 110,$line_array_new,"", 1, "--- Select ---", $selected, "",0,0 );
}

if ($action=="po_search_popup")
{
	echo load_html_head_contents("PO Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo date("Y");die;
	?> 

	<script>
		var update_id='<? echo $update_id; ?>';
		//alert(update_id);
		/*function js_set_value(po_id,product_id)
		{
			if(update_id!="")
			{
				var response = trim(return_global_ajax_value(update_id, 'duplication_check', '', 'trims_receive_multi_ref_entry_v2_controller'));
				if(response!="")
				{
					var curr_data=data.split("**");
					var curr_supplier_id=curr_data[0];
					var curr_currency_id=curr_data[1];
					var curr_source=curr_data[2];
					var curr_lc_id=curr_data[4];
					
					var prev_data=response.split("**");
					var prev_supplier_id=prev_data[0];
					var prev_currency_id=prev_data[1];
					var prev_source=prev_data[2];
					var prev_lc_id=prev_data[3];
					
					if(!(curr_supplier_id==prev_supplier_id && curr_currency_id==prev_currency_id && curr_source==prev_source))
					{
						alert("Supplier, Currency and Source Mix not allow in Same Received ID \n");
						//alert("Supplier, Currency and Source Mix not allow in Same Received ID \n"+curr_supplier_id+"=="+prev_supplier_id+"=="+curr_currency_id+"=="+prev_currency_id+"=="+curr_source+"=="+prev_source);
						return;
					}
				}
			}
			//alert("Fuad");return;
			$('#hidden_po_id').val(po_id);
			$('#hidden_product_id').val(product_id);
			//$('#booking_without_order').val(type);
			//$('#hidden_data').val(data);
			//$('#receive_basis').val(receive_basis);
			//alert(receive_basis);
			parent.emailwindow.hide();
		}*/
		
		var selected_id = new Array(); var selected_name = new Array(); var buyer_id=''; var buyer_name='';
		function check_all_data() 
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length; 
			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) 
			{
				if($('#search'+i).css('display')!='none')
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
			if(update_id!="")
			{
				var response = trim(return_global_ajax_value(update_id, 'duplication_check', '', 'trims_issue_requisition_controller'));
				
				if(response!="")
				{
					var current_buyer=$('#tdBuyer_'+str).attr('title');
					alert(response+"="+current_buyer);
					if(current_buyer!=response)
					{
						alert("Buyer Mix not allow in Same Received ID \n");
						return;
					}
				}
			}
			
			var color=document.getElementById('search' + str ).style.backgroundColor;
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			if( jQuery.inArray( $('#txt_individual_id' + str).val()+"__"+$('#txt_individual' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val()+"__"+$('#txt_individual' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );
				
			}
			else 
			{
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val()+"__"+$('#txt_individual' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			
			var id = ''; var name = ''; var po_and_prod = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				var selected_data=selected_id[i].split("__");
				id += selected_data[0] + ',';
				name += selected_data[1] + ',';
				po_and_prod += selected_data[0] + '_' +selected_data[1] + ',';
			}
			
			//buyer_id=$('#txt_buyer' + str).val();

			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			//alert(id+"="+name);
			$('#hidden_po_id').val(id);
			$('#hidden_product_id').val(name);
			$('#hidden_data').val(po_and_prod);
			//$('#hide_buyer').val(buyer_id);
			
		}
	
    </script>

	</head>

	<body onLoad="set_hotkey()">
	<div align="center" style="width:1110px;">
		<form name="searchwofrm"  id="searchwofrm">
			<fieldset style="width:1110px;">
			<legend>Enter search words</legend>           
				<table cellpadding="0" cellspacing="0" border="1" rules="all" width="900" class="rpt_table">
					<thead>
						<th width="200">Buyer</th>
						<th width="150">Job Year</th>
						<th width="200">Search By</th>
						<th width="200">Search</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
							<input type="hidden" name="txt_company_id" id="txt_company_id" value="<? echo $cbo_company_id; ?>">
							<input type="hidden" name="hidden_po_id" id="hidden_po_id" value="">
							<input type="hidden" name="hidden_product_id" id="hidden_product_id" value="">  
							<input type="hidden" name="hidden_data" id="hidden_data" value="">  
						</th> 
					</thead>
					<tr class="general">
						<td align="center">	
							<?
							echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90))  group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $buyer_name, "","" ); 
							?> 
						</td>
						<td align="center"><? echo create_drop_down( "cbo_job_year", 120, $year,"", 1, "-- Select Year --", date("Y"), "" );?></td>
						<td align="center">	
							<?
								$search_by_arr=array(1=>"PO No",2=>"Job No",3=>"Style Ref.",4=>"Internal Ref.");
								echo create_drop_down( "cbo_search_by", 170, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
						</td>                 
						<td align="center">				
							<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
						</td> 						
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_job_year').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $cbo_company_id ?>+'_'+<? echo $cbo_trim_type ?>, 'create_po_search_list_view', 'search_div', 'trims_issue_requisition_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
						</td>
					</tr>
			</table>
				<div style="margin-top:10px;" id="search_div" align="left"></div> 
			</fieldset>
		</form>
	</div>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="create_po_search_list_view")
{
	$data = explode("_",$data);
	$cbo_buyer_name=trim(str_replace("'","",$data[0]));
	$cbo_job_year=trim(str_replace("'","",$data[1]));
	$cbo_search_by =trim(str_replace("'","",$data[2]));
	$txt_search_common=trim(str_replace("'","",$data[3]));
	$cbo_company_id =trim(str_replace("'","",$data[4]));
	$cbo_trim_type =trim(str_replace("'","",$data[5]));
	if($cbo_buyer_name==0 && $txt_search_common=="") { echo "Please Select Specific Reference.";die;}
	$sql_cond="";
	if($cbo_buyer_name>0) $sql_cond.=" and a.BUYER_NAME=$cbo_buyer_name";
	if($cbo_trim_type>0) $sql_cond.=" and e.trim_type=$cbo_trim_type ";
	if($txt_search_common!="")
	{
		if($cbo_search_by==1) $sql_cond.=" and b.PO_NUMBER='$txt_search_common'";
		else if($cbo_search_by==2) $sql_cond.=" and a.JOB_NO like '%$txt_search_common'";
		else if($cbo_search_by==3) $sql_cond.=" and a.STYLE_REF_NO ='$txt_search_common'";
		else $sql_cond.=" and b.GROUPING ='$txt_search_common'";
	}
	
	if($cbo_job_year>0)
	{
		if($db_type==0) $sql_cond.=" and year(a.insert_date)='$cbo_job_year'";
		else $sql_cond.=" and to_char(a.insert_date,'YYYY')='$cbo_job_year'";
	}
	
	$po_sql="SELECT a.JOB_NO, a.STYLE_REF_NO, a.BUYER_NAME, b.ID as PO_ID, b.PO_NUMBER, b.PO_QUANTITY, (a.TOTAL_SET_QNTY*b.PO_QUANTITY) as PO_QNTY_IN_PCS, d.ID as PRODUCT_ID, d.ITEM_GROUP_ID, d.ITEM_DESCRIPTION, d.COLOR, d.ITEM_COLOR, d.ITEM_SIZE, d.UNIT_OF_MEASURE,
	sum(case when c.TRANS_TYPE in(1) then c.QUANTITY else 0 end ) as RCV_QNTY, 
	sum((case when c.TRANS_TYPE in(1,4,5) then c.QUANTITY else 0 end )-(case when c.TRANS_TYPE in(2,3,6) then c.QUANTITY else 0 end )) as STOCK_QNTY
	from wo_po_details_master a, wo_po_break_down b, order_wise_pro_details c, product_details_master d, lib_item_group e 
	where a.job_no=b.job_no_mst and b.id=c.PO_BREAKDOWN_ID and c.prod_id=d.id and d.item_group_id=e.id and c.entry_form in (24,25,49,73,78, 112) and b.shiping_status!=3 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.COMPANY_NAME=$cbo_company_id and d.company_id=$cbo_company_id $sql_cond
	group by a.JOB_NO, a.STYLE_REF_NO, a.BUYER_NAME, b.ID, b.PO_NUMBER, b.PO_QUANTITY, a.TOTAL_SET_QNTY, d.ID, d.ITEM_GROUP_ID, d.ITEM_DESCRIPTION, d.COLOR, d.ITEM_COLOR, d.ITEM_SIZE, d.UNIT_OF_MEASURE
	order by a.STYLE_REF_NO";
	//echo $po_sql;// die;
	$buyer_arr = return_library_array("SELECT buy.id, buy.short_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.short_name",'id','short_name');
	$item_group_arr = return_library_array("SELECT id, item_name from lib_item_group",'id','item_name');
	$color_arr = return_library_array("SELECT id, color_name from lib_color",'id','color_name');
	
	$conversion_arr = return_library_array("SELECT a.id, b.conversion_factor from product_details_master a, lib_item_group b where a.item_group_id=b.id and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0",'id','conversion_factor' );

	$result = sql_select($po_sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1090" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="100">Job No</th>
			<th width="80">Buyer</th>
			<th width="100">Style Ref. No.</th>
			<th width="100">Order No</th>
			<th width="80">Order Qty</th>               
			<th width="100">Item Group</th>
			<th width="120">Item Des.</th>
			<th width="70">Gmts. Color</th>
			<th width="70">Item Color</th>
			<th width="60">Size</th>
			<th width="50">Uom</th>
            <th width="60">Recv. Qty</th>
			<th>In Hand Qty.</th>
		</thead>
	</table>
	<div style="width:1110px; max-height:250px; overflow-y:scroll" id="list_container_batch" align="left">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1090" class="rpt_table" id="tbl_list_search">  
		<?
		$i=1;
		foreach ($result as $row)
		{  
			if ($i%2==0)  
				$bgcolor="#E9F3FF";
			else
				$bgcolor="#FFFFFF";	
			if(in_array($selectResult[csf('id')],$hidden_po_id)) 
			{
				if($po_row_id=="") $po_row_id=$i; else $po_row_id.=",".$i;
			}
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"  id="search<? echo $i;?>" onClick="js_set_value(<? echo $i ?>);"> 
				<td width="30" align="center"><? echo $i; ?>
                <input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $i ?>" value="<? echo $row['PO_ID']; ?>"/>
                <input type="hidden" name="txt_individual" id="txt_individual<? echo $i ?>" value="<? echo $row['PRODUCT_ID']; ?>"/>
                </td>
				<td width="100"><p><? echo $row['JOB_NO']; ?>&nbsp;</p></td>
				<td width="80" id="tdBuyer_<?= $i;?>" title="<?= $row['BUYER_NAME'];?>"><p><? echo $buyer_arr[$row['BUYER_NAME']]; ?>&nbsp;</p></td>
				<td width="100"><p><? echo $row['STYLE_REF_NO']; ?>&nbsp;</p></td>
				<td width="100"><p><? echo $row['PO_NUMBER'] ?>&nbsp;</p></td>
				<td width="80" align="right"><? echo $row['PO_QNTY_IN_PCS']; ?></td>               
				<td width="100" align="center"><p><? echo $item_group_arr[$row['ITEM_GROUP_ID']]; ?>&nbsp;</p></td>
				<td width="120"><p><? echo $row['ITEM_DESCRIPTION']; ?>&nbsp;</p></td>
				<td width="70"><p><? echo $color_arr[$row['COLOR']]; ?>&nbsp;</p></td>
				<td width="70"><p><? echo $color_arr[$row['ITEM_COLOR']]; ?>&nbsp;</p></td>
				<td width="60" align="center"><p><? echo $row['ITEM_SIZE']; ?>&nbsp;</p></td>
				<td width="50" align="center"><p><? echo $unit_of_measurement[$row['UNIT_OF_MEASURE']]; ?>&nbsp;</p></td>
				<td width="60" align="right"><? echo number_format($row['RCV_QNTY']*$conversion_arr[$row['PRODUCT_ID']],2); ?></td>
                <td align="right"><? echo number_format($row['STOCK_QNTY']*$conversion_arr[$row['PRODUCT_ID']],2); ?></td>
			</tr>
			<?
			$i++;
		}
		?>
        <input type="hidden" name="txt_po_row_id" id="txt_po_row_id" value="<? echo $po_row_id; ?>"/>
		</table>
	</div>
    <table width="1090" cellspacing="0" cellpadding="0" style="border:none" align="center">
        <tr>
            <td align="center" height="30" valign="bottom">
                <div style="width:100%"> 
                    <div style="width:50%; float:left" align="left">&nbsp;
                       <!-- <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All-->
                    </div>
                    <div style="width:50%; float:left" align="left">
                        <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                    </div>
                </div>
            </td>
        </tr>
    </table>
	<?
	exit();
}
if($action=="duplication_check")
{
	$prev_req_data=sql_select("select c.BUYER_NAME from READY_TO_SEWING_REQSN A, WO_PO_BREAK_DOWN B , WO_PO_DETAILS_MASTER C
	where a.po_id=b.id and b.job_no_mst=c.job_no and a.mst_id=$data and a.entry_form=357 and a.status_active=1 and a.is_deleted=0");
	echo $prev_req_data[0][csf("BUYER_NAME")];
}


if($action=="product_details")
{
	$data_ref=explode("__",$data);
	$po_id=$data_ref[0];
	$product_id=$data_ref[1];
	$dtls_tbl_length=$data_ref[2];
	$po_prod_id=chop(trim($data_ref[3]),',');
	//echo $po_prod_id."<br>";
	$po_prod_ids=explode(',',$po_prod_id); $chk_po_and_prod_arr=array();
	foreach ($po_prod_ids as $key => $value) {
		$chk_po_and_prod_arr[]=$value;
		/*$single_po_prod_id=explode('_',$value);
		foreach ($single_po_prod_id as $ind => $val) {
			
		}*/
	}
	
	/*$po_sql="SELECT a.JOB_NO, a.STYLE_REF_NO, a.BUYER_NAME, b.ID as PO_ID, b.PO_NUMBER, b.PO_QUANTITY, (a.TOTAL_SET_QNTY*b.PO_QUANTITY) as PO_QNTY_IN_PCS, d.ID as PRODUCT_ID, d.ITEM_GROUP_ID, d.ITEM_DESCRIPTION, d.ITEM_COLOR, d.ITEM_SIZE, d.UNIT_OF_MEASURE,
	sum(case when c.TRANS_TYPE in(1) then c.QUANTITY else 0 end ) as RCV_QNTY, 
	sum((case when c.TRANS_TYPE in(1,4,5) then c.QUANTITY else 0 end )-(case when c.TRANS_TYPE in(2,3,6) then c.QUANTITY else 0 end )) as STOCK_QNTY, e.CONS_DZN_GMTS
	from wo_po_details_master a, wo_po_break_down b, order_wise_pro_details c, product_details_master d, wo_pre_cost_trim_cost_dtls e 
	where a.job_no=b.job_no_mst and b.id=c.PO_BREAKDOWN_ID and c.prod_id=d.id and c.entry_form in(24,25,49,73,78) and a.job_no=e.job_no and d.item_group_id=e.trim_group and nvl(d.item_description,0)=nvl(e.description,0) and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and b.ID in($po_id) and d.id in($product_id)
	group by a.JOB_NO, a.STYLE_REF_NO, a.BUYER_NAME, b.ID, b.PO_NUMBER, b.PO_QUANTITY, a.TOTAL_SET_QNTY, d.ID, d.ITEM_GROUP_ID, d.ITEM_DESCRIPTION, d.ITEM_COLOR, d.ITEM_SIZE, d.UNIT_OF_MEASURE,e.CONS_DZN_GMTS";*/
	
	$po_sql="SELECT a.JOB_NO, a.STYLE_REF_NO, a.BUYER_NAME, b.ID as PO_ID, b.PO_NUMBER, b.PO_QUANTITY, (a.TOTAL_SET_QNTY*b.PO_QUANTITY) as PO_QNTY_IN_PCS, d.ID as PRODUCT_ID, d.ITEM_GROUP_ID, d.ITEM_DESCRIPTION, d.COLOR, d.ITEM_COLOR, d.ITEM_SIZE, d.UNIT_OF_MEASURE,
	sum(case when c.TRANS_TYPE in(1) then c.QUANTITY else 0 end ) as RCV_QNTY, 
	sum((case when c.TRANS_TYPE in(1,4,5) then c.QUANTITY else 0 end )-(case when c.TRANS_TYPE in(2,3,6) then c.QUANTITY else 0 end )) as STOCK_QNTY
	from wo_po_details_master a, wo_po_break_down b, order_wise_pro_details c, product_details_master d
	where a.job_no=b.job_no_mst and b.id=c.PO_BREAKDOWN_ID and c.prod_id=d.id and c.entry_form in(24,25,49,73,78,112) and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.ID in($po_id) and d.id in($product_id)
	group by a.JOB_NO, a.STYLE_REF_NO, a.BUYER_NAME, b.ID, b.PO_NUMBER, b.PO_QUANTITY, a.TOTAL_SET_QNTY, d.ID, d.ITEM_GROUP_ID, d.ITEM_DESCRIPTION, d.COLOR, d.ITEM_COLOR, d.ITEM_SIZE, d.UNIT_OF_MEASURE";
	
	// echo $po_sql; die;
	$result = sql_select($po_sql); $po_data_arr=array();
	foreach($result as $row)
	{
		$key=$row['PO_ID'].'_'.$row['PRODUCT_ID'];
		$po_data_arr[$key]['job_no']=$row['JOB_NO'];
		$po_data_arr[$key]['buyer_name']=$row['BUYER_NAME'];
		$po_data_arr[$key]['style_ref_no']=$row['STYLE_REF_NO'];
		$po_data_arr[$key]['po_number']=$row['PO_NUMBER'];
		$po_data_arr[$key]['po_id']=$row['PO_ID'];
		$po_data_arr[$key]['product_id']=$row['PRODUCT_ID'];
		$po_data_arr[$key]['po_qnty_in_pcs']=$row['PO_QNTY_IN_PCS'];
		$po_data_arr[$key]['item_group_id']=$row['ITEM_GROUP_ID'];
		$po_data_arr[$key]['item_description']=$row['ITEM_DESCRIPTION'];
		$po_data_arr[$key]['gmts_color']=$row['COLOR'];
		$po_data_arr[$key]['item_color']=$row['ITEM_COLOR'];
		$po_data_arr[$key]['item_size']=$row['ITEM_SIZE'];
		$po_data_arr[$key]['unit_of_measure']=$row['UNIT_OF_MEASURE'];
		$po_data_arr[$key]['rcv_qnty']=$row['RCV_QNTY'];
		$po_data_arr[$key]['stock_qnty']=$row['STOCK_QNTY'];
		//$po_data_arr[$key]['cons_dzn_gmts']=$row['CONS_DZN_GMTS'];
		$po_id_all[$row['PO_ID']]=$row['PO_ID'];
		
		if($job_check[$row['JOB_NO']]=="" && $row['JOB_NO']!="")
		{
			$job_check[$row['JOB_NO']]=$row['JOB_NO'];
			$all_job_no.="'".$row['JOB_NO']."',";
		}
	}
	
	$all_job_no=chop($all_job_no,",");
	$job_cond="";
	if($all_job_no!="") $job_cond=" and b.job_no in($all_job_no)";
	
	$budget_sql="select a.JOB_NO, b.TRIM_GROUP, b.DESCRIPTION, a.COSTING_PER, b.CONS_DZN_GMTS from WO_PRE_COST_MST a, WO_PRE_COST_TRIM_COST_DTLS b where a.job_no=b.job_no and a.status_active=1 and b.status_active=1 $job_cond";
	$budget_sql_result=sql_select($budget_sql);
	$budget_data=array();
	foreach($budget_sql_result as $val)
	{
		$budget_data[$val["JOB_NO"]][$val["TRIM_GROUP"]][$val["DESCRIPTION"]]["COSTING_PER"]=$val["COSTING_PER"];
		$budget_data[$val["JOB_NO"]][$val["TRIM_GROUP"]][$val["DESCRIPTION"]]["CONS_DZN_GMTS"]=$val["CONS_DZN_GMTS"];
	}
	
	//echo "<pre>";print_r($chk_po_and_prod_arr);
	//echo "<pre>";print_r($po_data_arr); die;
	//echo $po_sql;die;
	$conversion_arr = return_library_array("SELECT a.id, b.conversion_factor from product_details_master a, lib_item_group b where a.item_group_id=b.id and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0",'id','conversion_factor' );
	$buyer_arr = return_library_array("SELECT buy.id, buy.short_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.short_name",'id','short_name');
	$item_group_arr = return_library_array("SELECT id, item_name from lib_item_group",'id','item_name');
	$color_arr = return_library_array("SELECT id, color_name from lib_color",'id','color_name');

	$po_id_in=where_con_using_array($po_id_all,0,'b.po_id');
	$prv_req_sql="SELECT a.id as REQ_ID from ready_to_sewing_reqsn_mst a,ready_to_sewing_reqsn b where a.id=b.mst_id and a.entry_form=357 and a.status_active =1 and b.status_active =1 $po_id_in";
	// echo $prv_req_sql;die;
	$prv_req_result=sql_select($prv_req_sql);
	$pre_issue_data=array();
	if(count($prv_req_result)>0)
	{
		foreach($prv_req_result as $row)
		{
			$prv_req_id[$row['REQ_ID']]=$row['REQ_ID'];
		}

		$prv_req_id_in=where_con_using_array($prv_req_id,0,'a.booking_id');
		$prv_issue_sql="SELECT b.PROD_ID, b.SAVE_STRING from inv_issue_master a,inv_trims_issue_dtls b where a.id=b.mst_id and a.issue_basis=3 and a.entry_form=25 and a.status_active =1 and b.status_active =1 $prv_req_id_in";
		// echo $prv_issue_sql;die;
		$prv_issue_result=sql_select($prv_issue_sql);

		foreach($prv_issue_result as $val)
		{
			$save_string_info=explode(",",$val['SAVE_STRING']);
			foreach($save_string_info as $ord_row)
			{
				$ord_val=explode("_",$ord_row);
				$pre_issue_data[$ord_val[0]][$val['PROD_ID']]+=$ord_val[1]*$conversion_arr[$val['PROD_ID']];
			}
		}
	}
	
	$i=$dtls_tbl_length;
	// echo "<pre>"; print_r($po_data_arr); die;
	foreach($po_data_arr as $key=> $row)
	{
		if (in_array($key, $chk_po_and_prod_arr))
  		{
  			$i++;
			$cons_dzn_gmts=$budget_data[$row['job_no']][$row['item_group_id']][$row['item_description']]["CONS_DZN_GMTS"];
			$costing_par_id=$budget_data[$row['job_no']][$row['item_group_id']][$row['item_description']]["COSTING_PER"];
			if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"> 
	            <td align="center" id="slTd_<?= $i;?>" title="<?= $key; ?>"><? echo $i; ?></td>
	            <td id="tdJob_<?= $i;?>"><? echo $row['job_no']; ?></td>
	            <td id="tdBuyer_<?= $i;?>" title="<?= $row['buyer_name'];?>"><? echo $buyer_arr[$row['buyer_name']]; ?></td>
	            <td id="tdStyle_<?= $i;?>"><? echo $row['style_ref_no']; ?></td>
	            <td id="tdOrder_<?= $i;?>" title="<?= $row['po_id'];?>"><? echo $row['po_number'] ?></td>
	            <td align="right" id="tdOrderQnty_<?= $i;?>"><? echo $row['po_qnty_in_pcs']; ?></td>
	            <td align="center" id="tdItemGroup_<?= $i;?>" title="<?= $row['item_group_id'];?>"><? echo $item_group_arr[$row['item_group_id']]; ?></td>
	            <td id="tdItemDescrip_<?= $i;?>" title="<?= $row['product_id'];?>"><? echo $row['item_description']; ?></td>
	            <td id="tdGmtsColor_<?= $i;?>" title="<?= $row['gmts_color'];?>"><? echo $color_arr[$row['gmts_color']]; ?></td>
	            <td id="tdColor_<?= $i;?>" title="<?= $row['item_color'];?>"><? echo $color_arr[$row['item_color']]; ?></td>
	            <td id="tdSize_<?= $i;?>"><? echo $row['item_size']; ?></td>
	            <td align="right" id="tdBudget_<?= $i;?>"><? echo $cons_dzn_gmts; ?></td>
                <td align="right" id="tdBudCostingPer_<?= $i;?>"><? echo $costing_per[$costing_par_id]; ?></td>
	            <td align="center" id="tdUom_<?= $i;?>" title="<?= $row['unit_of_measure'];?>"><? echo $unit_of_measurement[$row['unit_of_measure']]; ?></td>
	            <td align="right" id="tdRcvQnty_<?= $i;?>"><? echo number_format($row['rcv_qnty']*$conversion_arr[$row['product_id']],4,'.',''); ?></td>
	            <td align="right" id="tdInhand_<?= $i;?>"><? echo number_format($row['stock_qnty']*$conversion_arr[$row['product_id']],4,'.',''); ?></td>
	            <td align="right" id="tdIssue_<?= $i;?>"><? echo number_format($pre_issue_data[$row['po_id']][$row['product_id']],4,'.',''); ?></td>
	            <td align="center" id="tdReqQnty_<?= $i;?>">
	            <input type="text" name="txtReqQnty[]" id="txtReqQnty_<?= $i;?>" class="text_boxes_numeric" placeholder="<? echo number_format($row['stock_qnty']*$conversion_arr[$row['product_id']],4,'.',''); ?>" value="<? echo number_format($row['stock_qnty']*$conversion_arr[$row['product_id']],4,'.',''); ?>" onKeyUp="chk_stock(<? echo $i; ?>)" style="width:80px" />
	            <input type="hidden" id="hdnUpdateDtlsId_<?= $i;?>" name="hdnUpdateDtlsId[]" />
	            </td>
			</tr>
			<?
  		}
	}
	die;
}

if($action=="product_details_update")
{
	$field_array_dtls="id, mst_id, po_id, trim_group, product_id, item_description, color_id, item_size, entry_form, store_id, cons_uom, cons, stock_qnty, reqsn_qty, status_active, is_deleted, inserted_by, insert_date";
	$po_sql="SELECT a.JOB_NO, a.STYLE_REF_NO, a.BUYER_NAME, b.ID as PO_ID, b.PO_NUMBER, b.PO_QUANTITY, (a.TOTAL_SET_QNTY*b.PO_QUANTITY) as PO_QNTY_IN_PCS, d.ID as PRODUCT_ID, d.ITEM_GROUP_ID, d.ITEM_DESCRIPTION, d.COLOR, d.ITEM_COLOR, d.ITEM_SIZE, d.UNIT_OF_MEASURE, c.ID as DTLS_ID, c.CONS as RCV_QNTY, c.STOCK_QNTY as STOCK_QNTY, c.REQSN_QTY
	from wo_po_details_master a, wo_po_break_down b, ready_to_sewing_reqsn c, product_details_master d  
	where a.job_no=b.job_no_mst and b.id=c.po_id and c.product_id=d.id and c.entry_form in(357) and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.mst_id=$data";
	// echo $po_sql; die;
	$result = sql_select($po_sql);
	$job_check=array();
	foreach($result as $row)
	{
		if($job_check[$row['JOB_NO']]=="" && $row['JOB_NO']!="")
		{
			$job_check[$row['JOB_NO']]=$row['JOB_NO'];
			$all_job_no.="'".$row['JOB_NO']."',";
		}
	}
	
	$all_job_no=chop($all_job_no,",");
	$job_cond="";
	if($all_job_no!="") $job_cond=" and b.job_no in($all_job_no)";
	
	$budget_sql="select a.JOB_NO, b.TRIM_GROUP, b.DESCRIPTION, a.COSTING_PER, b.CONS_DZN_GMTS from WO_PRE_COST_MST a, WO_PRE_COST_TRIM_COST_DTLS b where a.job_no=b.job_no and a.status_active=1 and b.status_active=1 $job_cond";
	$budget_sql_result=sql_select($budget_sql);
	$budget_data=array();
	foreach($budget_sql_result as $val)
	{
		$budget_data[$val["JOB_NO"]][$val["TRIM_GROUP"]][$val["DESCRIPTION"]]["COSTING_PER"]=$val["COSTING_PER"];
		$budget_data[$val["JOB_NO"]][$val["TRIM_GROUP"]][$val["DESCRIPTION"]]["CONS_DZN_GMTS"]=$val["CONS_DZN_GMTS"];
	}

	$buyer_arr = return_library_array("SELECT buy.id, buy.short_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.short_name",'id','short_name');
	$item_group_arr = return_library_array("SELECT id, item_name from lib_item_group",'id','item_name');
	$color_arr = return_library_array("SELECT id, color_name from lib_color",'id','color_name');
	$conversion_arr = return_library_array("SELECT a.id, b.conversion_factor from product_details_master a, lib_item_group b where a.item_group_id=b.id and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0",'id','conversion_factor' );
	
	foreach($result as $row)
	{
		$po_id_all[$row['PO_ID']]=$row['PO_ID'];
	}
	$po_id_in=where_con_using_array($po_id_all,0,'b.po_id');
	$prv_req_sql="SELECT a.id as REQ_ID from ready_to_sewing_reqsn_mst a,ready_to_sewing_reqsn b where a.id=b.mst_id and a.entry_form=357 and a.status_active =1 and b.status_active =1 $po_id_in";
	// echo $prv_req_sql;die;
	$prv_req_result=sql_select($prv_req_sql);

	foreach($prv_req_result as $row)
	{
		$prv_req_id[$row['REQ_ID']]=$row['REQ_ID'];
	}
	$prv_req_id_in=where_con_using_array($prv_req_id,0,'a.booking_id');

	$prv_issue_sql="SELECT b.PROD_ID, b.SAVE_STRING from inv_issue_master a,inv_trims_issue_dtls b where a.id=b.mst_id and a.issue_basis=3 and a.entry_form=25 and a.status_active =1 and b.status_active =1 $prv_req_id_in";
	// echo $prv_issue_sql;die;
	$prv_issue_result=sql_select($prv_issue_sql);
	$pre_issue_data=array();
	if(count($prv_issue_result)>0)
	{
		foreach($prv_issue_result as $val)
		{
			$save_string_info=explode(",",$val['SAVE_STRING']);
			foreach($save_string_info as $ord_row)
			{
				$ord_val=explode("_",$ord_row);
				$pre_issue_data[$ord_val[0]][$val['PROD_ID']]+=$ord_val[1]*$conversion_arr[$val['PROD_ID']];
			}
		}
	}
	$i=$dtls_tbl_length;
	foreach($result as $row)
	{
		$i++;
		$cons_dzn_gmts=$budget_data[$row['JOB_NO']][$row['ITEM_GROUP_ID']][$row['ITEM_DESCRIPTION']]["CONS_DZN_GMTS"];
		$costing_par_id=$budget_data[$row['JOB_NO']][$row['ITEM_GROUP_ID']][$row['ITEM_DESCRIPTION']]["COSTING_PER"];
		
		if ($i%2==0)  
			$bgcolor="#E9F3FF";
		else
			$bgcolor="#FFFFFF";	
		?>
		<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"> 
            <td align="center" id="slTd_<?= $i;?>"><? echo $i; ?></td>
            <td id="tdJob_<?= $i;?>"><? echo $row['JOB_NO']; ?></td>
            <td id="tdBuyer_<?= $i;?>" title="<?= $row['BUYER_NAME'];?>"><? echo $buyer_arr[$row['BUYER_NAME']]; ?></td>
            <td id="tdStyle_<?= $i;?>"><? echo $row['STYLE_REF_NO']; ?></td>
            <td id="tdOrder_<?= $i;?>" title="<?= $row['PO_ID'];?>"><? echo $row['PO_NUMBER'] ?></td>
            <td align="right" id="tdOrderQnty_<?= $i;?>"><? echo $row['PO_QNTY_IN_PCS']; ?></td>
            <td align="center" id="tdItemGroup_<?= $i;?>" title="<?= $row['ITEM_GROUP_ID'];?>"><? echo $item_group_arr[$row['ITEM_GROUP_ID']]; ?></td>
            <td id="tdItemDescrip_<?= $i;?>" title="<?= $row['PRODUCT_ID'];?>"><? echo $row['ITEM_DESCRIPTION']; ?></td>
            <td id="tdGmtsColor_<?= $i;?>" title="<?= $row['COLOR'];?>"><? echo $color_arr[$row['COLOR']]; ?></td>
            <td id="tdColor_<?= $i;?>" title="<?= $row['ITEM_COLOR'];?>"><? echo $color_arr[$row['ITEM_COLOR']]; ?></td>
            <td id="tdSize_<?= $i;?>"><? echo $row['ITEM_SIZE']; ?></td>
			<td align="right" id="tdBudget_<?= $i;?>"><? echo $cons_dzn_gmts; ?></td>
            <td align="right" id="tdBudCostingPer_<?= $i;?>"><? echo $costing_per[$costing_par_id]; ?></td>
            <td align="center" id="tdUom_<?= $i;?>" title="<?= $row['UNIT_OF_MEASURE'];?>"><? echo $unit_of_measurement[$row['UNIT_OF_MEASURE']]; ?></td>
            <td align="right" id="tdRcvQnty_<?= $i;?>"><? echo number_format($row['RCV_QNTY'],4,'.',''); ?></td>
            <td align="right" id="tdInhand_<?= $i;?>"><? echo number_format($row['STOCK_QNTY'],4,'.',''); ?></td>
			<td align="right" id="tdIssue_<?= $i;?>"><? echo number_format($pre_issue_data[$row['PO_ID']][$row['PRODUCT_ID']],4,'.',''); ?></td>
            <td align="center" id="tdReqQnty_<?= $i;?>">
            <input type="text" name="txtReqQnty[]" id="txtReqQnty_<?= $i;?>" class="text_boxes_numeric" placeholder="<? echo number_format($row['STOCK_QNTY'],4,'.',''); ?>"  value="<?= number_format($row['REQSN_QTY'],4,'.','');?>" onKeyUp="chk_stock(<? echo $i; ?>)" style="width:80px" />
            <input type="hidden" id="hdnUpdateDtlsId_<?= $i;?>" name="hdnUpdateDtlsId[]" value="<?= $row['DTLS_ID'];?>" />
            </td>
		</tr>
		<?
		
	}
	die;
}



if($action=="delivery_system_popup") //System PopUp
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	?>
		<script>
		function js_set_value(str)
		{
	 		$("#hidden_return_id").val(str);
	    	parent.emailwindow.hide();
	 	}
	    </script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	        <table width="1030" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
	             <thead>
	                <th width="150">Company</th>
					<th width="150">Working Company</th>
	                <th width="150">Buyer Name</th>
	                <th width="100">Req No</th>
	                <th width="100">Order No</th>
					<th width="100">Style Ref</th>
	                <th width="200">Req Date</th>
	                <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:80px;" /></th>
	            </thead>
	            <tr align="center">
	                <td>
	                <?
	                echo create_drop_down( "cbo_trans_com", 150, "SELECT id, company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select --", str_replace("'","",$company), "",0 );
	                ?>
	                </td>
					<td>
	                <?
	                echo create_drop_down( "cbo_trans_working", 150, "SELECT id, company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select --", 0, "",0 );
	                ?>
	                </td>
	                <td>
	                <?
						echo create_drop_down( "cbo_buyer_name", 150, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and buy.id in (SELECT  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0 );
					?>
	                </td>
	                <td align="center" >
	                    <input type="text" style="width:100px" class="text_boxes"  name="txt_challan_no" id="txt_challan_no" />
	                </td>
					 <td align="center" >
	                    <input type="text" style="width:100px" class="text_boxes"  name="txt_challan_no" id="txt_challan_no" />
	                </td>
	                <td align="center" >
	                    <input type="text" style="width:100px" class="text_boxes"  name="txt_po_no" id="txt_po_no" />
	                </td>
	                <td align="center">
	                    <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" readonly> To
	                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px" readonly>
	                </td>
	                <td align="center">
	                    <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_trans_com').value+'_'+document.getElementById('txt_challan_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_po_no').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_trans_working').value,'create_return_search_list', 'search_div_delivery', 'trims_issue_requisition_controller','setFilterGrid(\'tbl_invoice_list\',-1)')" style="width:80px;" />
	                </td>
	            </tr>
	            <tr>
	                <td align="center" height="40" colspan="8" valign="middle">
	                    <? echo load_month_buttons(1);  ?>
	                    <input type="hidden" id="hidden_return_id" >
	                </td>
	            </tr>
	        </table>
	        <div id="search_div_delivery" style="margin-top:20px;"></div>
	    </form>
	</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}
//$order_num_arr=return_library_array("select id, po_number from wo_po_break_down", "id", "po_number");

if($action=="create_return_search_list")
{

 	$ex_data = explode("_",$data);
	$company = $ex_data[0];
	$txt_challan_no = $ex_data[1];
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	//$company = $ex_data[4];
	$po_no = str_replace("'","",$ex_data[4]);
	$buyer_id = str_replace("'","",$ex_data[5]);
	$work_company = str_replace("'","",$ex_data[6]);
	//echo $trans_com;die;
	$sql_cond="";
	if($txt_date_from!="" || $txt_date_to!="")
	{
		if($db_type==0){$sql_cond .= " and a.REQUISITION_DATE between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";}
		if($db_type==2 || $db_type==1){ $sql_cond .= " and a.REQUISITION_DATE between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";}
	}

	if(trim($company)!=0) {
		$sql_cond .= " and a.COMPANY_ID='$company'";
	}else {
		echo "PLEASE SELECT THE COMPANY";
		return ;
	}
	if(trim($work_company)!=0){
		$sql_cond .= " and a.WORKING_COMPANY_ID  ='$work_company'";
	}
	//if(trim($buyer_id)!=0) $sql_cond .= " and a.buyer_id='$buyer_id'";

	if(trim($txt_challan_no)!="") $sql_cond .= " and a.REQ_NO_PREFIX_NUM='$txt_challan_no'";
	//if(trim($trans_com)!=0) $sql_cond .= " and a.transport_supplier='$trans_com'";
	$po_cond="";
	if(trim($po_no)!="")
	{
		if($db_type==2) $po_concat="listagg(CAST(id as VARCHAR(4000)),',') within group (order by id) as po_id";
		else if($db_type==0) $po_concat="group_concat(id) as po_id";
		$po_no_id = return_field_value("$po_concat","wo_po_break_down","po_number='$po_no'","po_id");
		$po_cond="and b.PO_ID in($po_no_id)";
	}
	if($db_type==0) $select_year="year(a.INSERT_DATE)"; else $select_year="to_char(a.insert_date,'YYYY')";
	if(trim($buyer_id)!=0)
	{
		$sql = "SELECT a.ID, a.REQ_NO_PREFIX_NUM, $select_year as REQ_YEAR, a.REQ_NO, a.COMPANY_ID, a.LOCATION_ID, a.REQUISITION_DATE, a.DELIVERY_DATE, a.STORE_ID,a.WORKING_COMPANY_ID, a.FLOOR_ID, a.SEWING_LINE, listagg(CAST(e.ISSUE_NUMBER_PREFIX_NUM as VARCHAR(4000)),',') within group (order by e.ISSUE_NUMBER_PREFIX_NUM) as ISSUE_NUMBER
		from  READY_TO_SEWING_REQSN b, WO_PO_BREAK_DOWN c, WO_PO_DETAILS_MASTER d, READY_TO_SEWING_REQSN_MST a
		left join INV_ISSUE_MASTER e on e.BOOKING_ID=a.ID
		where a.ID=b.MST_ID and b.PO_ID=c.ID and c.JOB_NO_MST=d.JOB_NO and a.ENTRY_FORM=357 and a.REQUISITION_VERSION=0 and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and d.BUYER_NAME=$buyer_id $sql_cond $po_cond
		group by  a.ID, a.REQ_NO_PREFIX_NUM, a.INSERT_DATE, a.REQ_NO, a.COMPANY_ID, a.LOCATION_ID, a.REQUISITION_DATE, a.DELIVERY_DATE, a.STORE_ID,a.WORKING_COMPANY_ID, a.FLOOR_ID, a.SEWING_LINE
		order by a.ID desc";
	}
	else
	{
		$sql = "SELECT a.ID, a.REQ_NO_PREFIX_NUM, $select_year as REQ_YEAR, a.REQ_NO, a.COMPANY_ID, a.LOCATION_ID, a.REQUISITION_DATE, 
		a.DELIVERY_DATE, a.STORE_ID,a.WORKING_COMPANY_ID, a.FLOOR_ID, a.SEWING_LINE, 
		listagg(CAST(e.ISSUE_NUMBER_PREFIX_NUM as VARCHAR(4000)),',') within group (order by e.ISSUE_NUMBER_PREFIX_NUM) as ISSUE_NUMBER
		from READY_TO_SEWING_REQSN b, READY_TO_SEWING_REQSN_MST a
		left join INV_ISSUE_MASTER e on e.BOOKING_ID = a.ID and e.ENTRY_FORM=25 and e.STATUS_ACTIVE=1 and e.IS_DELETED=0
		where a.ID=b.MST_ID and a.ENTRY_FORM=357 and a.REQUISITION_VERSION=0 and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 $sql_cond $po_cond
		group by a.ID, a.REQ_NO_PREFIX_NUM, a.INSERT_DATE, a.REQ_NO, a.COMPANY_ID, a.LOCATION_ID, 
		a.REQUISITION_DATE, a.DELIVERY_DATE, a.STORE_ID,a.WORKING_COMPANY_ID, a.FLOOR_ID, a.SEWING_LINE order by a.ID desc";
	}
	//  echo $sql;die;
	$result = sql_select($sql);
	// echo "<pre>";print_r($result);die;
	$company_arr=return_library_array( "SELECT ID, COMPANY_NAME from LIB_COMPANY",'ID','COMPANY_NAME');
	$location_arr=return_library_array( "SELECT ID, LOCATION_NAME from LIB_LOCATION",'ID','LOCATION_NAME');
 	$store_arr=return_library_array( "SELECT ID, STORE_NAME from LIB_STORE_LOCATION",'ID','STORE_NAME');
	$floor_arr=return_library_array( "SELECT ID, FLOOR_NAME from LIB_PROD_FLOOR",'ID','FLOOR_NAME');
	$sewing_line_arr=return_library_array( "SELECT ID, LINE_NAME from LIB_SEWING_LINE",'ID','LINE_NAME');


      

	/// edit

	$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
	$line_array=array();

	$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.company_id=$company and a.is_deleted=0 and b.is_deleted=0  group by a.id, a.line_number");
	//echo $line_data;die;
	// echo "select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.company_id='$company' and a.is_deleted=0 and b.is_deleted=0  group by a.id, a.line_number"; die;
	// and a.location_id='$location' and a.floor_id='$floor'

	$line_merge=9999;
	foreach($line_data as $row)
	{
		$line='';
		$line_number=explode(",",$row[csf('line_number')]);
		foreach($line_number as $val)
		{
			if(count($line_number)>1)
			{
				$line_merge++;
				$new_arr[$line_merge]=$row[csf('id')];
			}
			else
				$new_arr[$line_library[$val]]=$row[csf('id')];
			if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
		}
		$line_array[$row[csf('id')]]=$line;
	}
	//print_r($new_arr);
	sort($new_arr);
	foreach($new_arr as $key=>$v)
	{
		$line_array_new[$v]=$line_array[$v];
	}
// 	echo "<pre>";
//    print_r($line_array_new); die;
	///edit ends here
   ?>
     	<table cellspacing="0" width="1020" style="margin-right:18px;" class="rpt_table" cellpadding="0" border="1" rules="all">
            <thead>
                <th width="30">SL</th>
                <th width="40">Sys Num</th>
                <th width="50">Year</th>
                <th width="130">Company</th>
				<th width="130">Working Company</th>
                <th width="70">Requisition Date</th>
                <th width="70">Delivery Date</th>
                <th width="120">Location Name</th>
                <th width="120">Store Name</th>
                <th width="80">Floor</th>
                <th width="70">Sweing Line</th>
                <th>Issue ID</th>
            </thead>
     	</table>
	<div style="width:1040px; max-height:220px;overflow-y:scroll;" >
        <table cellspacing="0" width="1020" class="rpt_table" cellpadding="0" border="1" rules="all" id="tbl_invoice_list">
			<?
			$i=1;
            foreach( $result as $row )
            {
                if ($i%2==0)  $bgcolor="#E9F3FF";
                else $bgcolor="#FFFFFF";
				//$buyer_id=return_field_value("buyer_id","pro_ex_factory_delivery_mst","sys_number='".$row[csf('challan_no')]."' and entry_form!=85 ","buyer_id");
				?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value(<? echo $row['ID'];?>);" >
                    <td width="30" align="center"><? echo $i; ?></td>
                    <td width="40" align="center"><p><? echo $row["REQ_NO_PREFIX_NUM"]; ?></p></td>
                    <td width="50" align="center"><p><? echo $row["REQ_YEAR"]; ?></p></td>
                    <td width="130"><p><? echo $company_arr[$row["COMPANY_ID"]];?>&nbsp;</p></td>
					 <td width="130"><p><? echo $company_arr[$row["WORKING_COMPANY_ID"]];?>&nbsp;</p></td>
                    <td width="70" align="center"><p><? echo change_date_format($row["REQUISITION_DATE"]); ?>&nbsp;</p></td>
                    <td width="70" align="center"><p><? echo change_date_format($row["DELIVERY_DATE"]); ?>&nbsp;</p></td>
                    <td width="120"><p><? echo $location_arr[$row["LOCATION_ID"]]; ?>&nbsp;</p></td>
                    <td width="120"><p><? echo $store_arr[$row["STORE_ID"]];?>&nbsp;</p></td>
                    <td width="80"><p><? echo $floor_arr[$row["FLOOR_ID"]]; ?>&nbsp;</p></td>
                    <td width="70"><p><? echo $line_array_new[trim($row["SEWING_LINE"])];?>&nbsp;</p></td>
                    <td><p><? echo implode(",",array_unique(explode(",",$row["ISSUE_NUMBER"]))); ?>&nbsp;</p></td>
                </tr>
				<?
				$i++;
             }
   			?>
		</table>
	</div>
	<?
	exit();

}

if($action=="populate_master_from_data") //Master Part
{
	
	$sql_mst=sql_select("select ID, REQ_NO, COMPANY_ID, LOCATION_ID, REQUISITION_DATE, DELIVERY_DATE, STORE_ID,WORKING_COMPANY_ID,WORKING_LOCATION_ID,FLOOR_ID,TRIM_TYPE,REMARKS,SEWING_LINE
	from  READY_TO_SEWING_REQSN_MST where ID=$data");
	foreach($sql_mst as $row)
	{
		echo "$('#txt_req_no').val('".$row['REQ_NO']."');\n";
		echo "$('#update_id').val('".$row['ID']."');\n";
		echo "$('#cbo_company_name').val(".$row['COMPANY_ID'].");\n";
		echo "$('#cbo_company_name').attr('disabled',true);\n";
        
		echo  "load_drop_down( 'requires/trims_issue_requisition_controller', ".$row['COMPANY_ID'].", 'load_drop_down_location', 'location_td' );\n";

		echo "$('#cbo_location_name').val(".$row['LOCATION_ID'].");\n";
		echo "load_drop_down( 'requires/trims_issue_requisition_controller', ".$row['LOCATION_ID']."+'__'+".$row['COMPANY_ID'].",'load_drop_down_store', 'store_id' );\n";
		echo "$('#cbo_store_name').val('".$row['STORE_ID']."');\n";
		echo "$('#txt_req_date').val('".change_date_format($row['REQUISITION_DATE'])."');\n";
		echo "$('#txt_delivery_date').val('".change_date_format($row['DELIVERY_DATE'])."');\n";


		echo "$('#cbo_working_company').val(".$row['WORKING_COMPANY_ID'].");\n";
		
		echo  "load_drop_down( 'requires/trims_issue_requisition_controller', ".$row['WORKING_COMPANY_ID'].", 'load_drop_down_working_location', 'working_location_td' );\n";
        echo "$('#cbo_working_location').val(".$row['WORKING_LOCATION_ID'].");\n";
        echo "load_drop_down('requires/trims_issue_requisition_controller', ".$row['WORKING_LOCATION_ID']."+'__'+".$row['WORKING_COMPANY_ID'].",'load_drop_down_floor','floor_td' );\n";
		echo "$('#cbo_floor_name').val('".$row['FLOOR_ID']."');\n";

		echo "load_drop_down('requires/trims_issue_requisition_controller', ".$row['FLOOR_ID']."+'__'+".$row['WORKING_COMPANY_ID'].
		"+'__'+".$row['WORKING_LOCATION_ID'].",'load_drop_down_sewing_line','sewing_td' );\n";

		echo "$('#cbo_sewing_line').val('".$row['SEWING_LINE']."');\n";




		echo "$('#cbo_trim_type').val('".$row['TRIM_TYPE']."');\n";
		echo "$('#txt_remarks').val('".$row['REMARKS']."');\n";
		echo "$('#cbo_trim_type').attr('disabled',true);\n";
	}
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$con = connect();
	if($db_type==0)	{ mysql_query("BEGIN"); }
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name =$cbo_company_name and variable_list=23 and is_deleted=0 and status_active=1");
	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		if(str_replace("'","",$update_id)=="")
		{
			$return_mst_id=return_next_id("id", "ready_to_sewing_reqsn_mst", 1);
			if($db_type==2) $mrr_cond=" and TO_CHAR(insert_date,'YYYY')=".date('Y',time()); else if($db_type==0) $mrr_cond=" and year(insert_date)=".date('Y',time());
			$new_sys_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'TIRE', date("Y",time()), 5, "select REQ_NO_PREFIX,REQ_NO_PREFIX_NUM from ready_to_sewing_reqsn_mst where COMPANY_ID=$cbo_company_name and REQUISITION_VERSION=0 and entry_form=357 $mrr_cond order by id DESC ", "REQ_NO_PREFIX", "REQ_NO_PREFIX_NUM" ));

			$field_array_delivery="id, req_no_prefix, req_no_prefix_num, req_no, company_id, entry_form, requisition_date, delivery_date, location_id, store_id,working_company_id,working_location_id,floor_id,sewing_line,prod_reso_allo,trim_type,remarks,inserted_by,insert_date";
			$data_array_delivery="(".$return_mst_id.",'".$new_sys_number[1]."','".$new_sys_number[2]."','".$new_sys_number[0]."',".$cbo_company_name.",357,".$txt_req_date.",".$txt_delivery_date.",".$cbo_location_name.",".$cbo_store_name.",".$cbo_working_company.",".$cbo_working_location.",".$cbo_floor_name.",".$cbo_sewing_line.",".$prod_reso_allo.",".$cbo_trim_type.",".$txt_remarks.",".$user_id.",'".$pc_date_time."')";
			//$mrr_no=$new_sys_number[0];
			$mrr_no=$new_sys_number[0];

		}
		else
		{
			$return_mst_id=str_replace("'","",$update_id);
			$mrr_no=str_replace("'","",$txt_req_no);
			$field_array_delivery="requisition_date*delivery_date*location_id*store_id*working_company_id*working_location_id*floor_id*sewing_line*prod_reso_allo*trim_type*remarks*updated_by*update_date";
			$data_array_delivery="".$txt_req_date."*".$txt_delivery_date."*".$cbo_location_name."*".$cbo_store_name."*".$cbo_working_company."*".$cbo_working_location."*".$cbo_floor_name."*".$cbo_sewing_line."*".$prod_reso_allo."*".$cbo_trim_type."*".$txt_remarks."*".$user_id."*'".$pc_date_time."'";
		}
		$field_array_dtls="id, mst_id, po_id, trim_group, product_id, item_description, gmts_color_id, color_id, item_size, entry_form, store_id, cons_uom, cons, stock_qnty, reqsn_qty, status_active, is_deleted, inserted_by, insert_date";
		
		$dtls_id=return_next_id("id", "ready_to_sewing_reqsn", 1);
		$data_array_dtls="";
		for($i=1;$i<=$row_num;$i++)
		{
			$job_no="job_no".$i;
			$buyer_id="buyer_id".$i;
			$styleref="styleref".$i;
			$order_id="order_id".$i;
			$orderQnty="orderQnty".$i;
			$item_group="item_group".$i;
			$itemdescription="itemdescription".$i;
			$product_id="product_id".$i;
			$gmts_color_id = "gmts_color_id".$i;
			$itemcolorid="itemcolorid".$i;
			$itemsizeid="itemsizeid".$i;
			$cbouom="cbouom".$i;
			$rcv_qnty="rcv_qnty".$i;
			$stock_qnty="stock_qnty".$i;
			$reqQnty="reqQnty".$i;
			$updateId="updateId".$i;
			if($$reqQnty>$$stock_qnty)
			{
				echo "20**Requisition Quantity Not Allow Over Then In Hand Qty.";die;
			}
			
			if($data_array_dtls !="") $data_array_dtls .=",";
			$data_array_dtls.="(".$dtls_id.",".$return_mst_id.",'".$$order_id."','".$$item_group."','".$$product_id."','".$$itemdescription."','".$$gmts_color_id."','".$$itemcolorid."','".$$itemsizeid."',357,".$cbo_store_name.",'".$$cbouom."','".$$rcv_qnty."','".$$stock_qnty."','".$$reqQnty."','1','0',".$user_id.",'".$pc_date_time."')";
			$dtls_id=$dtls_id+1;

		}
		//echo "10**".$data_array_dtls;die;
		$reqID=$reqDtlsID=true;
		if(str_replace("'","",$txt_return_id)=="")
		{
			$reqID=sql_insert("ready_to_sewing_reqsn_mst",$field_array_delivery,$data_array_delivery,1);
		}
		else
		{
			$reqID=sql_update("ready_to_sewing_reqsn_mst",$field_array_delivery,$data_array_delivery,"id",$return_mst_id,1);
		}
		$reqDtlsID=sql_insert("ready_to_sewing_reqsn",$field_array_dtls,$data_array_dtls,1);
		//echo "10**".$reqID."**".$reqDtlsID;die;
		//release lock table
		//check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($reqID && $reqDtlsID )
			{
				mysql_query("COMMIT");
				echo "0**".$return_mst_id."**".$mrr_no;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".$return_mst_id."**".$mrr_no;
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($reqID && $reqDtlsID )
			{
				oci_commit($con);
				echo "0**".$return_mst_id."**".$mrr_no;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$return_mst_id."**".$mrr_no;
			}
		}
		disconnect($con);
		die;
	}
  	else if ($operation==1) // Update Here End------------------------------------------------------
	{
		$return_mst_id=str_replace("'","",$update_id);
		$mrr_no=str_replace("'","",$txt_req_no);
		$field_array_delivery="requisition_date*delivery_date*location_id*store_id*working_company_id*working_location_id*floor_id*sewing_line*prod_reso_allo*trim_type*remarks*updated_by*update_date";
		$data_array_delivery="".$txt_req_date."*".$txt_delivery_date."*".$cbo_location_name."*".$cbo_store_name."*".$cbo_working_company."*".$cbo_working_location."*".$cbo_floor_name."*".$cbo_sewing_line."*".$prod_reso_allo."*".$cbo_trim_type."*".$txt_remarks."*".$user_id."*'".$pc_date_time."'";
		$field_array_dtls="id, mst_id, po_id, trim_group, product_id, item_description, gmts_color_id, color_id, item_size, entry_form, store_id, cons_uom, cons, stock_qnty, reqsn_qty, status_active, is_deleted, inserted_by, insert_date";
		$field_array_dtls_up="reqsn_qty*updated_by*update_date";
		$data_array_dtls="";
		$dtls_id=return_next_id("id", "ready_to_sewing_reqsn", 1);
		for($i=1;$i<=$row_num;$i++)
		{
			$job_no="job_no".$i;
			$buyer_id="buyer_id".$i;
			$styleref="styleref".$i;
			$order_id="order_id".$i;
			$orderQnty="orderQnty".$i;
			$item_group="item_group".$i;
			$itemdescription="itemdescription".$i;
			$product_id="product_id".$i;
			$gmts_color_id = "gmts_color_id".$i;
			$itemcolorid="itemcolorid".$i;
			$itemsizeid="itemsizeid".$i;
			$cbouom="cbouom".$i;
			$rcv_qnty="rcv_qnty".$i;
			$stock_qnty="stock_qnty".$i;
			$reqQnty="reqQnty".$i;
			$updateId="updateId".$i;
			if($$reqQnty>$$stock_qnty)
			{
				echo "20**Requisition Quantity Not Allow Over Then In Hand Qty.";die;
			}
			if(str_replace("'","",$$updateId) !="")
			{
				$updateID_array[]=str_replace("'","",$$updateId);
				$data_array_dtls_up[str_replace("'","",$$updateId)]=explode("*",("".$$reqQnty."*".$user_id."*'".$pc_date_time."'"));
			}
			else
			{
				if($data_array_dtls !="") $data_array_dtls .=",";
				$data_array_dtls.="(".$dtls_id.",".$return_mst_id.",'".$$order_id."','".$$item_group."','".$$product_id."','".$$itemdescription."','".$$gmts_color_id."','".$$itemcolorid."','".$$itemsizeid."',357,".$cbo_store_name.",'".$$cbouom."','".$$rcv_qnty."','".$$stock_qnty."','".$$reqQnty."','1','0',".$user_id.",'".$pc_date_time."')";
				$dtls_id=$dtls_id+1;
			}
		}
		$reqID=$dtlsrID=$reqDtlsID=true;
		$reqID=sql_update("ready_to_sewing_reqsn_mst",$field_array_delivery,$data_array_delivery,"id",$return_mst_id,1);
		$dtlsrID=execute_query(bulk_update_sql_statement("ready_to_sewing_reqsn","id",$field_array_dtls_up,$data_array_dtls_up,$updateID_array),1);
		if($data_array_dtls !="")
		{
			$reqDtlsID=sql_insert("ready_to_sewing_reqsn",$field_array_dtls,$data_array_dtls,1);
		}
		//echo "10** $reqID && $dtlsrID && $reqDtlsID";oci_rollback($con);die;	
		$return_mst_id=str_replace("'","",$update_id);
		$mrr_no=str_replace("'","",$txt_req_no);
		if($db_type==0)
		{
			if($reqID && $dtlsrID && $reqDtlsID)
			{
				mysql_query("COMMIT");
				echo "1**".$return_mst_id."**".$mrr_no."**".$mrr_no;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".$return_mst_id."**".$mrr_no."**".$mrr_no;
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($reqID && $dtlsrID && $reqDtlsID)
			{
				oci_commit($con);
				echo "1**".$return_mst_id."**".$mrr_no."**".$mrr_no;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$return_mst_id."**".$mrr_no."**".$mrr_no;
			}
		}
		disconnect($con);
		die;
	}

	else if ($operation==2)  // Delete Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		$return_mst_id=str_replace("'","",$update_id);
		$issue_sql=sql_select("select ID, ISSUE_NUMBER from INV_ISSUE_MASTER where status_active=1 and entry_form=25 and issue_basis=3 and booking_id=$return_mst_id");
		if(count($issue_sql)>0)
		{
			echo "20**Issue Found. Delete Not Allow";oci_rollback($con);disconnect($con);die;
		}
		$rID = execute_query("update ready_to_sewing_reqsn_mst set updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."', status_active=0, is_deleted=1 where id=$return_mst_id");
		$dtlsrID = execute_query("update ready_to_sewing_reqsn set updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."', status_active=0, is_deleted=1 where mst_id=$return_mst_id and status_active=1");

 		if($db_type==0)
		{
			if($rID && $dtlsrID)
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$txt_mst_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_mst_id);
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $dtlsrID)
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$txt_mst_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_mst_id);
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="garments_exfactory_print")
{
	//$start = microtime(true);
	extract($_REQUEST);
	$data=explode('*',$data);
	$company=$data[0];
	$location=$data[1];
	$update_id=$data[2];
	echo load_html_head_contents("Garments Delivery Info","../", 1, 1, $unicode,'','');
	$com_dtls = fnc_company_location_address($company, $location, 2);
	$store = return_library_array("select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0  group by a.id,a.store_name order by a.store_name", 'id', 'store_name');
	$location_arr = return_library_array("select id, location_name from lib_location", "id", "location_name");
	$mst_sql=sql_select("SELECT a.ID, a.REQ_NO, a.COMPANY_ID, a.LOCATION_ID, a.REQUISITION_DATE, a.DELIVERY_DATE, a.STORE_ID, a.REMARKS,a.WORKING_COMPANY_ID,a.WORKING_LOCATION_ID,a.FLOOR_ID,a.SEWING_LINE
		from  READY_TO_SEWING_REQSN_MST a WHERE a.ID=$update_id and a.COMPANY_ID=$company  and a.LOCATION_ID='$location' and a.ENTRY_FORM=357 and a.STATUS_ACTIVE=1 and a.IS_DELETED=0");
		$working_company=$mst_sql[0]['WORKING_COMPANY_ID'];
		$working_location=$mst_sql[0]['WORKING_LOCATION_ID'];
		$floor=$mst_sql[0]['FLOOR_ID'];

	$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
	$line_array=array();

	$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.company_id='$working_company' and a.location_id='$working_location' and a.floor_id='$floor' and a.is_deleted=0 and b.is_deleted=0  group by a.id, a.line_number");
	//echo $line_data;die;

	$line_merge=9999;
	foreach($line_data as $row)
	{
		$line='';
		$line_number=explode(",",$row[csf('line_number')]);
		foreach($line_number as $val)
		{
			if(count($line_number)>1)
			{
				$line_merge++;
				$new_arr[$line_merge]=$row[csf('id')];
			}
			else
				$new_arr[$line_library[$val]]=$row[csf('id')];
			if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
		}
		$line_array[$row[csf('id')]]=$line;
	}
	//print_r($new_arr);
	sort($new_arr);
	foreach($new_arr as $key=>$v)
	{
		$line_array_new[$v]=$line_array[$v];
	}
	//print_r($line_array);
	?>
	<div style="width:1320px; margin-top:5px;">
		<table width="1320" cellspacing="0" align="right" border="0" style="margin-right:-10px;">
            <tr class="form_caption">
                <td rowspan="2" align="left" width="50"> <img src="../<? echo $com_dtls[2]; ?>" height='70' width='200' align="middle"/></td>
                <td colspan="6" align="center" style="font-size:18px"> <strong><? echo $com_dtls[0]; ?></strong></td>
                <td rowspan="2">&nbsp;</td>
            </tr>
            <tr>
            	<td colspan="6" align="center" style="font-size:14px"><? echo $com_dtls[1]; ?></td>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="10" align="center" style="font-size:16px"><strong><u><? echo $data[3]; ?></u></strong></center></td>
               
            </tr>
            <br>
            <tr>
                <td width="160"><strong>Requisition No:</strong></td>
                <td width="160px"><? echo $mst_sql[0]['REQ_NO']; ?></td>
                <td width="160"><strong>Requisition Date:</strong></td>
                <td width="160px"><? echo change_date_format($mst_sql[0]['REQUISITION_DATE']); ?></td>
                <td width="160"><strong>Delivery Date:</strong></td>
                <td width="160px"><? echo change_date_format($mst_sql[0]['DELIVERY_DATE']); ?></td>
            </tr>
            <tr>
                <td width="180"><strong>Location:</strong></td>
                <td width="160px"><? echo $location_arr[$mst_sql[0]['LOCATION_ID']]; ?></td>
                <td width="180"><strong>Store Name:</strong></td>
                <td width="160px"><? echo $store[$mst_sql[0]['STORE_ID']]; ?></td>
				<td width="180"><strong>Sewing Line</strong></td>
				<td width="160px"><? echo $line_array_new[$mst_sql[0]['SEWING_LINE']]; ?></td>
				<td width="180"><strong>Remarks :</strong></td>
                <td width="160px"><? echo $mst_sql[0]['REMARKS']; ?></td>
                <td colspan="2">&nbsp;</td>
            </tr>
        </table>
	    <br>
        <?
		$po_sql="SELECT a.id as JOB_ID,a.JOB_NO, a.STYLE_REF_NO, a.BUYER_NAME, b.ID as PO_ID, b.PO_NUMBER, b.PO_QUANTITY, (a.TOTAL_SET_QNTY*b.PO_QUANTITY) as PO_QNTY_IN_PCS, d.ID as PRODUCT_ID, d.ITEM_GROUP_ID, d.ITEM_DESCRIPTION, d.COLOR, d.ITEM_COLOR, d.ITEM_SIZE, d.UNIT_OF_MEASURE, c.ID as DTLS_ID, c.CONS as RCV_QNTY, c.STOCK_QNTY as STOCK_QNTY, c.REQSN_QTY,e.SEWING_LINE,e.PROD_RESO_ALLO
		from wo_po_details_master a, wo_po_break_down b, ready_to_sewing_reqsn c, product_details_master d,READY_TO_SEWING_REQSN_MST e
		where a.id=b.job_id and b.id=c.po_id and c.product_id=d.id and e.id=c.mst_id and c.entry_form in(357)  and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.mst_id=$update_id";
		//echo $po_sql;die;
		$buyer_arr = return_library_array("SELECT buy.id, buy.short_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) group by buy.id, buy.short_name order by buy.short_name",'id','short_name');
		$item_group_arr = return_library_array("SELECT id, item_name from lib_item_group",'id','item_name');
		$color_arr = return_library_array("SELECT id, color_name from lib_color",'id','color_name');
	
		
		$result = sql_select($po_sql);
		foreach($result as $row)
		{
			$job_id_arr[$row['JOB_ID']]=$row['JOB_ID'];
			$po_id_arr[$row['PO_ID']]=$row['PO_ID'];
			$all_data_info[$row['PO_ID']]['JOB_NO']=$row['JOB_NO'];
			$all_data_info[$row['PO_ID']]['STYLE_REF_NO']=$row['STYLE_REF_NO'];
			$all_data_info[$row['PO_ID']]['BUYER_NAME']=$row['BUYER_NAME'];
			$all_data_info[$row['PO_ID']]['PO_NUMBER']=$row['PO_NUMBER'];
			$all_data_info[$row['PO_ID']]['PO_QNTY_IN_PCS']=$row['PO_QNTY_IN_PCS'];
			$all_data_info[$row['PO_ID']]['PO_QNTY_IN_PCS']=$row['PO_QNTY_IN_PCS'];
			
		}
		$job_id_all=where_con_using_array($job_id_arr,0,'b.job_id');
		$prvs_po_sql="SELECT b.JOB_ID,c.TRIM_GROUP, sum(c.reqsn_qty) AS REQSN_QTY from wo_po_break_down b, ready_to_sewing_reqsn c
		where b.id=c.po_id and c.entry_form in(357) $job_id_all and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.mst_id<$update_id group by b.JOB_ID,c.trim_group";
		// echo $prvs_po_sql;
		$prvs_po_result = sql_select($prvs_po_sql);
		foreach($prvs_po_result as $row)
		{
			$prvs_po_data[$row['JOB_ID']][$row['TRIM_GROUP']]=$row['REQSN_QTY'];
		}
		$issue_sql="SELECT b.PROD_ID,sum(b.cons_quantity) as QUANTITY from inv_issue_master a, inv_transaction b
		where a.id=b.mst_id and a.booking_id=$update_id and a.issue_basis=3 and a.entry_form=25 and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.prod_id";
		// echo $issue_sql;
		$issue_result = sql_select($issue_sql);
		foreach($issue_result as $row)
		{
			$issue_data[$row['PROD_ID']]=$row['QUANTITY'];
		}
        $job_id_all_budget=where_con_using_array($job_id_arr,0,'b.job_id');
        $budget_sql="select a.JOB_NO, b.TRIM_GROUP, b.DESCRIPTION, a.COSTING_PER, b.CONS_DZN_GMTS from WO_PRE_COST_MST a, WO_PRE_COST_TRIM_COST_DTLS b where a.job_no=b.job_no and a.status_active=1 and b.status_active=1 $job_id_all_budget";
        $budget_sql_result=sql_select($budget_sql);
        $budget_data=array();
        foreach($budget_sql_result as $val)
        {
			//$budget_data[$val["JOB_NO"]][$val["TRIM_GROUP"]][$val["DESCRIPTION"]]["COSTING_PER"]=$val["COSTING_PER"];
            $budget_data[$val["JOB_NO"]][$val["TRIM_GROUP"]][$val["DESCRIPTION"]]["CONS_DZN_GMTS"]=$val["CONS_DZN_GMTS"];
        }
        $i=$dtls_tbl_length;
		$table_width=1340;
		?>
		<div style="width:<? echo $table_width;?>px;">
	    <table align="right" cellspacing="0" width="<? echo $table_width;?>"  border="1" rules="all" class="rpt_table" >
	        <thead bgcolor="#dddddd" align="center">
	            <th width="20">SL</th>
                <th width="90">Job No</th>
                <th width="80">Buyer</th>
                <th width="90">Style Ref. No.</th>
                <th width="80">Order No </th>
                <th width="70">Order Qty</th>
                <th width="90">Item Group</th>
                <th width="80">Item Des. </th>
                <th width="80">Gmts. Color</th>
                <th width="80">Item Color</th>
                <th width="70">Size</th>
                <th width="40">Uom</th>
                <th width="80">Consumption [Budget]</th>
			
                <th width="70">Recv. Qty</th>
                <th width="70">In Hand Qty.</th>
                <th width="80">Cumulative Req Qty</th>
                <th width="80">Cumulative Issue Qty</th>
                <th>Req Qty</th>
	        </thead>
	        <tbody>
			<?
	        $i=1;
	        $tot_qnty=$tot_carton_qnty=0;
	        foreach($result as $row)
			{
				
				if ($i%2==0)  
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";	
				?>
				<tr bgcolor="<? echo $bgcolor; ?>"> 
		            <td valign="middle" align="center" id="slTd_<?= $i;?>"><? echo $i; ?></td>
		            <td valign="middle" id="tdJob_<?= $i;?>"><? echo $row['JOB_NO']; ?></td>
		            <td valign="middle" id="tdBuyer_<?= $i;?>" title="<?= $row['BUYER_NAME'];?>"><? echo $buyer_arr[$row['BUYER_NAME']]; ?></td>
		            <td valign="middle" id="tdStyle_<?= $i;?>"><? echo $row['STYLE_REF_NO']; ?></td>
		            <td valign="middle" id="tdOrder_<?= $i;?>" title="<?= $row['PO_ID'];?>"><? echo $row['PO_NUMBER'] ?></td>
		            <td valign="middle" align="right" id="tdOrderQnty_<?= $i;?>"><? echo $row['PO_QNTY_IN_PCS']; ?></td>
		            <td valign="middle" align="center" id="tdItemGroup_<?= $i;?>" title="<?= $row['ITEM_GROUP_ID'];?>"><? echo $item_group_arr[$row['ITEM_GROUP_ID']]; ?></td>
		            <td valign="middle" id="tdItemDescrip_<?= $i;?>" title="<?= $row['PRODUCT_ID'];?>"><? echo $row['ITEM_DESCRIPTION']; ?></td>
		            <td valign="middle" id="tdGmtsColor_<?= $i;?>" title="<?= $row['COLOR'];?>"><? echo $color_arr[$row['COLOR']]; ?></td>
		            <td valign="middle" id="tdColor_<?= $i;?>" title="<?= $row['ITEM_COLOR'];?>"><? echo $color_arr[$row['ITEM_COLOR']]; ?></td>
		            <td valign="middle" id="tdSize_<?= $i;?>"><? echo $row['ITEM_SIZE']; ?></td>
		            <td valign="middle" align="center" id="tdUom_<?= $i;?>" title="<?= $row['UNIT_OF_MEASURE'];?>"><? echo $unit_of_measurement[$row['UNIT_OF_MEASURE']]; ?></td>
		            <td valign="middle" align="right"><? echo number_format($budget_data[$row['JOB_NO']][$row["ITEM_GROUP_ID"]][$row['ITEM_DESCRIPTION']]['CONS_DZN_GMTS'],4,'.',''); ?></td>
					
		            <td valign="middle" align="right" id="tdRcvQnty_<?= $i;?>"><? echo number_format($row['RCV_QNTY'],4,'.',''); ?></td>
		            <td valign="middle" align="right" id="tdInhand_<?= $i;?>"><? echo number_format($row['STOCK_QNTY'],4,'.',''); ?></td>
		            <td valign="middle" align="right" ><? echo number_format($prvs_po_data[$row['JOB_ID']][$row['ITEM_GROUP_ID']],4,'.',''); ?></td>
		            <td valign="middle" align="right" ><? echo number_format($issue_data[$row['PRODUCT_ID']],4,'.',''); ?></td>
		            <td valign="middle" align="right" id="tdReqQnty_<?= $i;?>"><? echo number_format($row['REQSN_QTY'],4,'.',''); ?></td>
				</tr>
				<?
				$tot_rcv_qnty+=$row['RCV_QNTY'];
				$tot_stock_qnty+=$row['STOCK_QNTY'];
				$tot_prvs_po_data+=$prvs_po_data[$row['JOB_ID']][$row['ITEM_GROUP_ID']];
				$tot_issue_data+=$issue_data[$row['PRODUCT_ID']];
				$tot_carton_qnty+=$row['REQSN_QTY'];
			}
	        ?>
	        </tbody>
	        <tr>
	            <td colspan="13" align="right" style="font-size:12px;"><strong>Grand Total :</strong></td>
	            <td align="right" style="font-size:12px;"><? echo number_format($tot_rcv_qnty,4,".",""); ?></td>
	            <td align="right" style="font-size:12px;"><? echo number_format($tot_stock_qnty,4,".",""); ?></td>
	            <td align="right" style="font-size:12px;"><? echo number_format($tot_prvs_po_data,4,".",""); ?></td>
	            <td align="right" style="font-size:12px;"><? echo number_format($tot_issue_data,4,".",""); ?></td>
	            <td align="right" style="font-size:12px;"><? echo number_format($tot_carton_qnty,4,".",""); ?></td>
	        </tr>
	    </table>
		<br>
		<?
			$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
			$po_id_in=where_con_using_array($po_id_arr,0,'c.order_id');
			$cut_lay_bundle_sql="SELECT a.CUTTING_NO, b.COLOR_ID, c.ORDER_ID, c.SIZE_ID, sum(c.size_qty) as SIZE_QTY
			from ppl_cut_lay_mst a, ppl_cut_lay_dtls b, ppl_cut_lay_bundle c
			where a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id and a.entry_form in (289,99) and a.status_active=1 and b.status_active=1 and c.status_active=1 $po_id_in group by a.CUTTING_NO, b.COLOR_ID, c.ORDER_ID, c.SIZE_ID order by c.SIZE_ID";
			$cut_lay_bundle_sql_result = sql_select($cut_lay_bundle_sql);

			foreach($cut_lay_bundle_sql_result as $row)
			{
				$size_id_arr[$row['SIZE_ID']]=$row['SIZE_ID'];
				$size_id_info[$row['ORDER_ID']][$row['COLOR_ID']][$row['SIZE_ID']]=$row['SIZE_QTY'];
				$all_data_rslt[$row['ORDER_ID']][$row['COLOR_ID']]['CUTTING_NO']=$row['CUTTING_NO'];
				$all_data_rslt[$row['ORDER_ID']][$row['COLOR_ID']]['COLOR_ID']=$row['COLOR_ID'];
				$all_data_rslt[$row['ORDER_ID']][$row['COLOR_ID']]['ORDER_ID']=$row['ORDER_ID'];
			}
			$size_span=count($size_id_arr);

		?>
	    <table align="left" cellspacing="0"  border="1" rules="all" class="rpt_table" >
	        <thead bgcolor="#dddddd" align="center">
				<tr>
					<th rowspan="2" width="20">SL</th>
					<th rowspan="2" width="100">Job No</th>
					<th rowspan="2" width="90">Buyer</th>
					<th rowspan="2" width="100">Style Ref. No.</th>
					<th rowspan="2" width="100">Order No </th>
					<th rowspan="2" width="70">Order Qty</th>
					<th rowspan="2" width="90">Cutting Number</th>
					<th rowspan="2" width="100">Color</th>
					<th colspan="<?=$size_span;?>" >Cum. Cutting Qty.</th>
					<th rowspan="2" width="80">Total</th>
				</tr>
				<tr>
					<?
						foreach($size_id_arr as $key=>$val)
						{
							?>
								<th width="80"><?=$size_arr[$val];?></th>
							<?
						}
					?>
				</tr>
	        </thead>
	        <tbody>
			<?
			// var_dump($all_data_info);
	        $i=1;
	        $tot_qnty=$tot_carton_qnty=0;
	        foreach($all_data_rslt as $po_id=>$po_val)
			{
				foreach($po_val as $row)
				{
					if($i%2==0){ $bgcolor="#E9F3FF"; }else{ $bgcolor="#FFFFFF"; }	
					?>
					<tr bgcolor="<? echo $bgcolor; ?>"> 
						<td align="center" ><? echo $i; ?></td>
						<td ><? echo $all_data_info[$row['ORDER_ID']]['JOB_NO']; ?></td>
						<td ><? echo $buyer_arr[$all_data_info[$row['ORDER_ID']]['BUYER_NAME']]; ?></td>
						<td ><? echo $all_data_info[$row['ORDER_ID']]['STYLE_REF_NO']; ?></td>
						<td ><? echo $all_data_info[$row['ORDER_ID']]['PO_NUMBER'];?></td>
						<td align="right" ><? echo $all_data_info[$row['ORDER_ID']]['PO_QNTY_IN_PCS'];?></td>
						<td align="center" ><? echo $row['CUTTING_NO']; ?></td>
						<td ><? echo $color_arr[$row['COLOR_ID']]; ?></td>
						<?
							$tot_size_qnty+=0;
							foreach($size_id_arr as $key=>$val)
							{
								?>
									<th width="80"><?=$size_id_info[$po_id][$row['COLOR_ID']][$val];?></th>
								<?
								$tot_size_qnty+=$size_id_info[$po_id][$row['COLOR_ID']][$val];
							}
						?>
						<td align="right" ><? echo number_format($tot_size_qnty,4,'.',''); ?></td>
					</tr>
					<?
					$i++;
				}
			}
	        ?>
	        </tbody>
	    </table>
	    <script type="text/javascript" src="../js/jquery.js"></script>
	    <script type="text/javascript" src="../js/jquerybarcode.js"></script>
	    <script>
			//fnc_generate_Barcode('<? echo $system_num; ?>','barcode_img_id');
		</script>
		</div>
		 <?
            echo signature_table(319, $data[0], $table_width."px");
         ?>
	</div>
	<?
	//echo "Start Time: " . date("Y-m-d H:i:s.u",$start) . "<br/>";
	//echo "End Time: " . date("Y-m-d H:i:s.u", microtime(true)) . "<br/>";
	//$duration = microtime(true) - $start;
	//echo "Printing Time: =" . date("s.u", $duration) ;

exit();
}

if($action=="garments_exfactory_print_old")
{
	//$start = microtime(true);
	extract($_REQUEST);
	$data=explode('*',$data);
	echo load_html_head_contents("Garments Delivery Info","../", 1, 1, $unicode,'','');
	//print_r ($data); die;
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$buyer_library=return_library_array( "select id, short_name from   lib_buyer", "id", "short_name"  );
	$invoice_library=return_library_array( "select id, invoice_no from  com_export_invoice_ship_mst", "id", "invoice_no"  );
	$order_sql=sql_select("select a.id, a.po_number, b.buyer_name, b.gmts_item_id, b.style_ref_no from  wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.status_active=1 and b.status_active=1");
	foreach($order_sql as $row)
	{
		$order_job_arr[$row[csf("id")]]['po_number']=$row[csf("po_number")];
		$order_job_arr[$row[csf("id")]]['buyer_name']=$row[csf("buyer_name")];
		$order_job_arr[$row[csf("id")]]['gmts_item_id']=$row[csf("gmts_item_id")];
		$order_job_arr[$row[csf("id")]]['style_ref_no']=$row[csf("style_ref_no")];
	}

	//echo "select id, sys_number, transport_supplier, driver_name, truck_no, dl_no, lock_no, destination_place,challan_no,sys_number_prefix_num from pro_ex_factory_delivery_mst where id=$data[1]";die;
	$delivery_mst_sql=sql_select("select id, sys_number, transport_supplier, driver_name, truck_no, dl_no, lock_no, destination_place,challan_no,sys_number_prefix_num from pro_ex_factory_delivery_mst where id=$data[1]");
	foreach($delivery_mst_sql as $row)
	{
		$supplier_name=$row[csf("transport_supplier")];
		$driver_name=$row[csf("driver_name")];
		$truck_no=$row[csf("truck_no")];
		$dl_no=$row[csf("dl_no")];
		$lock_no=$row[csf("lock_no")];
		$destination_place=$row[csf("destination_place")];
		$challan_no=$row[csf("challan_no")];
		$sys_number_prefix_num=$row[csf("sys_number_prefix_num")];
		$system_num=$row[csf("sys_number")];
	}
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");

	?>
	<div style="width:810px; margin-top:5px;">
	    <table width="800" cellspacing="0" align="right" style="margin-bottom:20px;">
	        <tr>
	            <td rowspan="2" align="center"><img src="../<? echo $image_location; ?>" height="50" width="100"></td>
	            <td colspan="4" align="center"  style="font-size:xx-large;"><strong><? echo $company_library[$data[0]]; ?></strong></td>
	            <td rowspan="2" id="barcode_img_id"></td>
	        </tr>
	        <tr class="form_caption">
	        	<td colspan="4" align="center" style="font-size:14px;" valign="top">
					<?
						$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
						$company_address="";
						foreach ($nameArray as $result)
						{
						?>
							<? if($result[csf('plot_no')]!="") $company_address.= $result[csf('plot_no')].", "; ?>
							<? if($result[csf('level_no')]!="") $company_address.= $result[csf('level_no')].", ";?>
							<? if($result[csf('road_no')]!="") $company_address.= $result[csf('road_no')].", "; ?>
							<? if($result[csf('block_no')]!="") $company_address.= $result[csf('block_no')].", ";?>
							<? if($result[csf('city')]!="") $company_address.= $result[csf('city')].", ";?>
							<? if($result[csf('zip_code')]!="") $company_address.= $result[csf('zip_code')].", "; ?>
							<? if($result[csf('province')]!="") $company_address.= $result[csf('province')];?>
							<? if($result[csf('country_id')]!=0) $company_address.= $country_arr[$result[csf('country_id')]].", "; ?><br>
							<? if($result[csf('email')]!="") $company_address.= $result[csf('email')].", ";?>
							<? if($result[csf('website')]!="") $company_address.= $result[csf('website')];
						}
						$company_address=chop($company_address," , ");
						echo $company_address;
	                ?>
	            </td>
	        </tr>
	        	<?
					$supplier_sql=sql_select("select id, supplier_name, contact_person, contact_no, designation, email, address_1, address_2, address_3, address_4 from  lib_supplier where id=$supplier_name");
					foreach($supplier_sql as $row)
					{

					$address_1=$row[csf("address_1")];
					$address_2=$row[csf("address_2")];
					$address_3=$row[csf("address_3")];
					$address_4=$row[csf("address_4")];
					$contact_no=$row[csf("contact_no")];
					}
					//echo $supplier_sql;die;

	            ?>
	        <tr>
	            <td colspan="5" style="font-size:x-large; padding-left:252px;"><strong><? echo $data[6]; ?></strong></td>
	            <td style="font-size:16px;">Date : <? echo change_date_format($data[2]); ?></td>
	        </tr>
	        <tr >
	        	<td width="100" valign="top" style="font-size:16px;"><strong>Name:</strong></td>
	            <td width="200" valign="top" style="font-size:16px;"><? echo $supplier_library[$supplier_name]; ?></td>
	            <td width="100" valign="top" style="font-size:16px;"><strong>Challan No :</strong></td>
	            <td width="120" valign="top" style="font-size:16px;"><? echo $challan_no; ?> </td>
	            <td width="80" valign="top" style="font-size:16px;"><strong>DL/NO:</strong></td>
	            <td valign="top" style="font-size:16px;"><? echo $dl_no; ?> </td>
	        </tr>

	        <tr>
	            <td valign="top" style="font-size:16px;"><strong>Address:</strong></td>
	            <td colspan="3" valign="top" style="font-size:16px;"><? echo $address_1."<br>"; if($contact_no!="") echo "Phone : ".$contact_no; ?> </td>
	            <td style="font-size:16px;"><strong>Truck No:</strong></td>
	            <td style="font-size:16px;"><? echo $truck_no; ?> </td>
	        </tr>
	        <tr >
	            <td style="font-size:16px;"><strong>Destination :</strong></td>
	            <td style="font-size:16px;"><? echo $destination_place; ?> </td>
	            <td  valign="top" style="font-size:16px;"><strong >Driver Name :</strong></td>
	            <td  valign="top" style="font-size:16px;"><? echo $driver_name; ?> </td>
	            <td style="font-size:16px;"><strong >Lock No :</strong></td>
	            <td style="font-size:16px;"><? echo $lock_no; ?> </td>
	        </tr>
	    </table><br>
	        <?
			//listagg(CAST(b.po_breakdown_id as VARCHAR(4000)),',') within group (order by b.po_breakdown_id) as po_id
			if($db_type==2)
			{
				$sql="SELECT po_break_down_id, listagg(CAST(invoice_no as VARCHAR(4000)),',') within group (order by invoice_no) as invoice_no, sum(ex_factory_qnty) as ex_factory_qnty, sum(total_carton_qnty) as total_carton_qnty, sum(ex_factory_qnty) as total_qnty, listagg(CAST(remarks as VARCHAR(4000)),',') within group (order by remarks) as remarks from pro_ex_factory_mst where delivery_mst_id=$data[1]  and status_active=1 and is_deleted=0 group by po_break_down_id";
			}
			else if($db_type==0)
			{
				$sql="SELECT po_break_down_id, group_concat(invoice_no) as invoice_no, sum(ex_factory_qnty) as ex_factory_qnty, sum(total_carton_qnty) as total_carton_qnty , sum(ex_factory_qnty) as total_qnty,group_concat(remarks) as remarks from pro_ex_factory_mst where delivery_mst_id=$data[1] and status_active=1 and is_deleted=0 group by po_break_down_id";
			}
			//echo $sql;die;
			$result=sql_select($sql);
			$table_width=800;
			$col_span=5;
			?>

		<div style="width:<? echo $table_width;?>px;">
	    <table align="right" cellspacing="0" width="<? echo $table_width;?>"  border="1" rules="all" class="rpt_table" >
	        <thead bgcolor="#dddddd" align="center">
	            <th width="30">SL</th>
	            <th width="120">Style Ref.</th>
	            <th width="120" >Order No</th>
	            <th width="100" >Buyer</th>
	            <th width="200" >Invoice No</th>
	            <th width="50">NO Of Carton</th>
	            <th>Quantity</th>
	        </thead>
	        <tbody>
			<?
	        $i=1;
	        $tot_qnty=$tot_carton_qnty=0;
	        foreach($result as $row)
	        {
	            if ($i%2==0)
	                $bgcolor="#E9F3FF";
	            else
	                $bgcolor="#FFFFFF";
	            $color_count=count($cid);
	            ?>
	            <tr bgcolor="<? echo $bgcolor; ?>">
	                <td style="font-size:12px;"><? echo $i;  ?></td>
	                <td style="font-size:12px;"><p><? echo $order_job_arr[$row[csf("po_break_down_id")]]['style_ref_no']; ?>&nbsp;</p></td>
	                <td style="font-size:12px;"><p><? echo $order_job_arr[$row[csf("po_break_down_id")]]['po_number']; ?>&nbsp;</p></td>
	                <td style="font-size:12px;"><p><? echo $buyer_library[$order_job_arr[$row[csf("po_break_down_id")]]['buyer_name']]; ?>&nbsp;</p></td>
	                <td style="font-size:12px;"><p>
					<?
					 $invoice_id="";
					 $invoice_id_arr=array_unique(explode(",",$row[csf("invoice_no")]));
					 foreach($invoice_id_arr as $inv_id)
					 {
						 if($invoice_id=="") $invoice_id=$invoice_library[$inv_id]; else $invoice_id=$invoice_id.",".$invoice_library[$inv_id];

					 }
					 echo $invoice_id;
					?>&nbsp;</p></td>
	                <td align="right" style="font-size:12px;"><p><? echo number_format($row[csf("total_carton_qnty")],0,"",""); $tot_carton_qnty +=$row[csf("total_carton_qnty")]; ?></p></td>
	                <td align="right" style="font-size:12px;"><p><? echo number_format($row[csf("total_qnty")],0); $tot_qnty +=$row[csf("total_qnty")]; ?></p></td>
	            </tr>
	            <?
	            $i++;
	        }
	        ?>
	        </tbody>

	        <tr>
	            <td colspan="<? echo $col_span; ?>" align="right" style="font-size:12px;"><strong>Grand Total :</strong></td>
	            <td align="right" style="font-size:12px;"><? echo number_format($tot_carton_qnty,0,"",""); ?></td>
	            <td align="right" style="font-size:12px;"><? echo number_format($tot_qnty,0,"",""); ?></td>
	        </tr>
	    </table>
	    <script type="text/javascript" src="../js/jquery.js"></script>
	    <script type="text/javascript" src="../js/jquerybarcode.js"></script>
	    <script>
			fnc_generate_Barcode('<? echo $system_num; ?>','barcode_img_id');
		</script>
		</div>
		 <?
            echo signature_table(63, $data[0], $table_width."px");
         ?>
	</div>
<?
//echo "Start Time: " . date("Y-m-d H:i:s.u",$start) . "<br/>";
//echo "End Time: " . date("Y-m-d H:i:s.u", microtime(true)) . "<br/>";
//$duration = microtime(true) - $start;
//echo "Printing Time: =" . date("s.u", $duration) ;

exit();
}

if($action=="ex_factory_print_new")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$id_ref=str_replace("'","",$data[4]);
	echo load_html_head_contents("Garments Delivery Info","../", 1, 1, $unicode,'','');
	//print_r ($data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$buyer_library=return_library_array( "select id, short_name from   lib_buyer", "id", "short_name"  );
	$invoice_library=return_library_array( "select id, invoice_no from  com_export_invoice_ship_mst", "id", "invoice_no"  );
	$order_sql=sql_select("select a.id, a.po_number, b.buyer_name, b.gmts_item_id, b.style_ref_no from  wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.status_active=1 and b.status_active=1");
	foreach($order_sql as $row)
	{
		$order_job_arr[$row[csf("id")]]['po_number']=$row[csf("po_number")];
		$order_job_arr[$row[csf("id")]]['buyer_name']=$row[csf("buyer_name")];
		$order_job_arr[$row[csf("id")]]['gmts_item_id']=$row[csf("gmts_item_id")];
		$order_job_arr[$row[csf("id")]]['style_ref_no']=$row[csf("style_ref_no")];
	}

	//echo "select transport_supplier from pro_ex_factory_delivery_mst where id=$data[1]";die;
	$delivery_mst_sql=sql_select("select id, sys_number, transport_supplier, driver_name, truck_no, dl_no, lock_no, destination_place,challan_no,sys_number_prefix_num,mobile_no,do_no,gp_no,forwarder from pro_ex_factory_delivery_mst where id=$data[1]");
	foreach($delivery_mst_sql as $row)
	{
		$supplier_name=$row[csf("transport_supplier")];
		$driver_name=$row[csf("driver_name")];
		$truck_no=$row[csf("truck_no")];
		$dl_no=$row[csf("dl_no")];
		$lock_no=$row[csf("lock_no")];
		$destination_place=$row[csf("destination_place")];
		$challan_no=$row[csf("challan_no")];
		$sys_number_prefix_num=$row[csf("sys_number_prefix_num")];
		$mobile_no=$row[csf("mobile_no")];
		$do_no=$row[csf("do_no")];
		$gp_no=$row[csf("gp_no")];
		$forwarder=$row[csf("forwarder")];
		$system_num=$row[csf("sys_number")];
	}
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	//echo "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]";
	?>
	<div style="width:800px; margin-top:10px;">
	    <table width="800" cellspacing="0" align="right" style="margin-bottom:20px;">
	        <tr>
	            <td rowspan="2" align="center"><img src="../<? echo $image_location; ?>" height="55" width="65"></td>
	            <td colspan="4" align="center"  style="font-size:xx-large;"><strong><? echo $company_library[$data[0]]; ?></strong></td>
	            <td rowspan="2" id="barcode_img_id"></td>
	        </tr>
	        <tr class="form_caption">
	        	<td colspan="4" align="center" style="font-size:12px;">
					<?

						$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
						$company_address="";
						foreach ($nameArray as $result)
						{

							 if($result[csf('plot_no')]!="") $company_address.=$result[csf('plot_no')].", ";
							 if($result[csf('level_no')]!="") $company_address.= $result[csf('level_no')].", ";
							 if($result[csf('road_no')]!="") $company_address.= $result[csf('road_no')].", ";
							 if($result[csf('block_no')]!="") $company_address.= $result[csf('block_no')].", ";
							 if($result[csf('city')]!="") $company_address.= $result[csf('city')]."<br>";
							 if($result[csf('zip_code')]!="") $company_address.= $result[csf('zip_code')].", ";
							 if($result[csf('province')]!="") $company_address.= $result[csf('province')].", ";
							 if($result[csf('country_id')]!=0 && $result[csf('country_id')]!=""){ if($country_library[$result[csf('country_id')]]!="") $company_address.= $country_library[$result[csf('country_id')]].", ";}
							 if($result[csf('email')]!="") $company_address.= $result[csf('email')].", ";
							 if($result[csf('website')]!="") $company_address.= $result[csf('website')];
						}
						$company_address=chop($company_address," , ");
						echo $company_address;
	                ?> <br>
	                <span style="font-size:16px;">100% Export Oriented</span><br>
	                <span style="font-size:22px;">Delivery Challan</span>
	            </td>
	        </tr>
	        	<?
				  	//echo "select id, supplier_name, contact_person, contact_no, designation, email, address_1, address_2, address_3, address_4 from  lib_supplier where id=$forwarder";
					$supplier_sql=sql_select("select id, supplier_name, contact_person, contact_no, designation, email, address_1, address_2, address_3, address_4 from  lib_supplier where id=$forwarder");
					foreach($supplier_sql as $row)
					{

					$address_1=$row[csf("address_1")];
					$address_2=$row[csf("address_2")];
					$address_3=$row[csf("address_3")];
					$address_4=$row[csf("address_4")];
					$contact_no=$row[csf("contact_no")];
					}
					//echo $supplier_sql;die;

	            ?>
	         <tr>
	         	<td>&nbsp;</td>
	            <td>&nbsp;</td>
	            <td>&nbsp;</td>
	            <td>&nbsp;</td>
	            <td>&nbsp;</td>
	            <td>&nbsp;</td>

	         </tr>
	         <tr >
	        	<td width="100" valign="top" style="font-size:16px;"><strong>Challan No :</strong></td>
	            <td width="200" valign="top" style="font-size:16px;"><? echo $challan_no; ?></td>
	            <td width="100" valign="top" style="font-size:16px;"><strong>Driver Name :</strong></td>
	            <td width="120" valign="top" style="font-size:16px;"><? echo $driver_name; ?> </td>
	            <td width="80" valign="top" style="font-size:16px;"><strong>Date:</strong></td>
	            <td valign="top" style="font-size:16px;"><? echo change_date_format($data[2]); ?> </td>
	        </tr>
	        <tr >
	        	<td valign="top" style="font-size:16px;"><strong>C&F Name:</strong></td>
	            <td valign="top" style="font-size:16px;"><? echo $supplier_library[$forwarder]; ?></td>
	            <td valign="top" style="font-size:16px;"><strong>Mobile Num :</strong></td>
	            <td valign="top" style="font-size:16px;"><? echo $mobile_no; ?> </td>
	            <td valign="top" style="font-size:16px;"><strong>DO NO:</strong></td>
	            <td valign="top" style="font-size:16px;"><? echo $do_no; ?> </td>
	        </tr>
			<tr>
	            <td valign="top" style="font-size:16px;"><strong>Address:</strong></td>
	            <td valign="top" style="font-size:16px;"><? echo $address_1."<br>"; if($contact_no!="") echo "Phone : ".$contact_no; ?> </td>
	            <td style="font-size:16px;"><strong>DL No:</strong></td>
	            <td style="font-size:16px;"><? echo $dl_no; ?> </td>
	            <td style="font-size:16px;"><strong>GP No:</strong></td>
	            <td style="font-size:16px;"><? echo $gp_no; ?> </td>
	        </tr>
	        <tr>
	            <td valign="top" style="font-size:16px;"><strong>Trns. Comp:</strong></td>
	            <td valign="top" style="font-size:16px;"><? echo $supplier_library[$supplier_name]; ?> </td>
	            <td style="font-size:16px;"><strong>Truck No:</strong></td>
	            <td style="font-size:16px;"><? echo $truck_no; ?> </td>
	             <td style="font-size:16px;"><strong>Lock No:</strong></td>
	            <td style="font-size:16px;"><? echo $lock_no; ?> </td>
	        </tr>

	    </table><br>
	        <?
			//listagg(CAST(b.po_breakdown_id as VARCHAR(4000)),',') within group (order by b.po_breakdown_id) as po_id
			if($db_type==2)
			{
				$sql="SELECT po_break_down_id, country_id, listagg(CAST(invoice_no as VARCHAR(4000)),',') within group (order by invoice_no) as invoice_no, sum(total_carton_qnty) as total_carton_qnty, sum(ex_factory_qnty) as total_qnty, listagg(CAST(remarks as VARCHAR(4000)),',') within group (order by remarks) as remarks from pro_ex_factory_mst where delivery_mst_id=$data[1]  and status_active=1 and is_deleted=0 group by po_break_down_id, country_id";
			}
			else if($db_type==0)
			{
				$sql="SELECT po_break_down_id, country_id, group_concat(invoice_no) as invoice_no, sum(total_carton_qnty) as total_carton_qnty , sum(ex_factory_qnty) as total_qnty,group_concat(remarks) as remarks from pro_ex_factory_mst where delivery_mst_id=$data[1] and status_active=1 and is_deleted=0 group by po_break_down_id, country_id";
			}
			//echo $sql;die;
			$result=sql_select($sql);
			$table_width=800;
			$col_span=7;
			?>

		<div style="width:<? echo $table_width;?>px;">
	    <table align="right" cellspacing="0" width="<? echo $table_width;?>"  border="1" rules="all" class="rpt_table" >
	        <thead bgcolor="#dddddd" align="center">
	            <th width="20">SL</th>
	            <th width="60" >Buyer</th>
	            <th width="100" >Style Ref.</th>
	            <th width="100" >Order No</th>
	            <th width="60" >Country</th>
	            <th width="130" >Item Name</th>
	            <th width="150" >Invoice No</th>
	            <th width="50">Delivery Qnty</th>
	            <th width="50">NO Of Carton</th>
	            <th >Remarks</th>
	        </thead>
	        <tbody>
			<?
	        $i=1;
	        $tot_qnty=$tot_carton_qnty=0;
	        foreach($result as $row)
	        {
	            if ($i%2==0)
	                $bgcolor="#E9F3FF";
	            else
	                $bgcolor="#FFFFFF";
	            $color_count=count($cid);
	            ?>
	            <tr bgcolor="<? echo $bgcolor; ?>">
	                <td style="font-size:12px;"><? echo $i;  ?></td>
	                <td style="font-size:12px;"><p><? echo $buyer_library[$order_job_arr[$row[csf("po_break_down_id")]]['buyer_name']]; ?>&nbsp;</p></td>
	                <td style="font-size:12px;"><p><? echo $order_job_arr[$row[csf("po_break_down_id")]]['style_ref_no']; ?>&nbsp;</p></td>
	                <td style="font-size:12px;"><p><? echo $order_job_arr[$row[csf("po_break_down_id")]]['po_number']; ?>&nbsp;</p></td>
	                <td style="font-size:12px;"><p><? echo $country_library[$row[csf("country_id")]]; ?>&nbsp;</p></td>
	                <td style="font-size:12px;"><p>
	                <?
	                 $garments_item_arr=explode(",",$order_job_arr[$row[csf("po_break_down_id")]]['gmts_item_id']);
	                 $garments_item_all="";
	                 foreach($garments_item_arr as $item_id)
	                 {
	                     $garments_item_all .=$garments_item[$item_id].",";
	                 }
	                 $garments_item_all=substr($garments_item_all,0,-1);
	                 echo $garments_item_all;
	                ?>
	                 &nbsp;</p></td>
	                <td style="font-size:12px;"><p>
					<?
					 $invoice_id="";
					 $invoice_id_arr=array_unique(explode(",",$row[csf("invoice_no")]));
					 foreach($invoice_id_arr as $inv_id)
					 {
						 if($invoice_id=="") $invoice_id=$invoice_library[$inv_id]; else $invoice_id=$invoice_id.",".$invoice_library[$inv_id];

					 }
					 echo $invoice_id;
					?>&nbsp;</p></td>
	                <td align="right" style="font-size:12px;"><p><? echo number_format($row[csf("total_qnty")],0); $tot_qnty +=$row[csf("total_qnty")]; ?></p></td>
	                <td align="right" style="font-size:12px;"><p><? echo number_format($row[csf("total_carton_qnty")],0,"",""); $tot_carton_qnty +=$row[csf("total_carton_qnty")]; ?></p></td>
	                <td style="font-size:12px;"><p><? echo implode(",",array_unique(explode(",",$row[csf("remarks")]))); ?>&nbsp;</p></td>
	            </tr>
	            <?
	            $i++;
	        }
	        ?>
	        </tbody>

	        <tr>
	            <td colspan="<? echo $col_span; ?>" align="right" style="font-size:14px;"><strong>Grand Total :</strong></td>
	            <td align="right" style="font-size:12px;"><? echo number_format($tot_qnty,0,"",""); ?></td>
	            <td align="right" style="font-size:12px;"><? echo number_format($tot_carton_qnty,0,"",""); ?></td>
	            <td align="right" style="font-size:12px;">&nbsp;</td>
	        </tr>
	    </table>
		</div>
		<?
			echo signature_table(63, $data[0], $table_width."px");
		?>
	    <script type="text/javascript" src="../js/jquery.js"></script>
	    <script type="text/javascript" src="../js/jquerybarcode.js"></script>
	    <script>
			fnc_generate_Barcode('<? echo $system_num; ?>','barcode_img_id');
		</script>
		</div>
	<?
	exit();
}

?>
