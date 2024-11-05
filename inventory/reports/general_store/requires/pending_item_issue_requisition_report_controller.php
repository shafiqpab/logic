<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];


if ($action=="item_group_popup")
{
	echo load_html_head_contents("Item Group popup", "../../../../", 1, 1,'','1','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(str)
		{
			$("#item_id").val(str);
			parent.emailwindow.hide(); 
		}
	</script>
	</head>
	<body>
	<div  style="width:930px" >
	<fieldset style="width:930px">
		<form name="order_popup_1"  id="order_popup_1">
			<?
			if ($category!='') {$item_category_list=" and item_category='$category'";}
			$sql="SELECT id,item_category,item_group_code,item_name,trim_type,order_uom,trim_uom,conversion_factor,cal_parameter from lib_item_group where is_deleted=0 and status_active=1 and item_category in (".implode(',',array_keys($general_item_category)).") $item_category_list";
			$arr=array (0=>$item_category,3=>$trim_type,4=>$unit_of_measurement,5=>$unit_of_measurement,7=>$cal_parameter);
			echo create_list_view ( "list_view", "Item Catagory,Group Code,Item Group Name,Item Type,Order UOM,Cons. UOM,Conv. Factor,Cal Parameter", "150,100,200,80,50,50,50","900","320",0, $sql, "js_set_value", "id,item_name", "", 1, "item_category,0,0,trim_type,order_uom,trim_uom,0,cal_parameter", $arr , "item_category,item_group_code,item_name,trim_type,order_uom,trim_uom,conversion_factor,cal_parameter", "", '','0,0,0,0,0,0,0,0' );
			?>
		<input type="hidden" id="item_id" />
		</form>
	</fieldset>
	</div>
	</body>
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<? 	
	exit();																																	
}


if ($action == "item_description_popup") 
{
	echo load_html_head_contents("Item Details Info", "../../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
    <script>
        function js_set_value(str)
		{
			$("#txt_selected_id").val(str);
			parent.emailwindow.hide(); 
		}
    </script>
    </head>
    <body>
    <div align="center">
        <form name="item_detailsfrm" id="item_detailsfrm">
            <fieldset style="width:580px;">
                <table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center"
                       class="rpt_table" id="tbl_list_search">
                    <thead>
                    <th>Item Category</th>
                    <th>Item Group</th>
                    <th>Item Code</th>
                    <th>Item Description</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;"
                            onClick="reset_form('item_detailsfrm','search_div','','','','');"></th>
                    </thead>
                    <tbody>
                    <tr>
                        <td align="center">
							<?php
							echo create_drop_down("cbo_item_category_id", 160, $general_item_category, "", 1, "-- Select --", $selected, "load_drop_down( 'item_wise_stock_report_controller', this.value, 'load_drop_down_group','group_td');", "", $cred_item_cate_id_cond, "", "", "");
							?>
                        </td> 
                        <td align="center" id="group_td">
                            <?
                    			echo create_drop_down("txt_item_group",130,$blank_array,"",1,"-- Select --",$selected, "" );
                    		?> 
                    	</td>
						<td align="center"><input type="text" style="width:130px" class="text_boxes" name="txt_item_code" id="txt_item_code"/></td>
                        <td align="center"><input type="text" style="width:130px" class="text_boxes" name="txt_item_description" id="txt_item_description"/></td>
                        <td align="center"><input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_item_category_id').value+'**'+document.getElementById('txt_item_group').value+'**'+document.getElementById('txt_item_description').value+'**'+document.getElementById('txt_item_code').value+'**'+'<? echo $cbo_company_name; ?>', 'item_description_popup_list_view', 'search_div', 'pending_item_issue_requisition_report_controller', 'setFilterGrid(\'tbl_list\',-1,\'tableFilters\')');" style="width:100px;"/>
                        <input type="hidden" name="txt_selected_id" id="txt_selected_id" /></td>
                    </tr>
                    </tbody>
                </table>
            </fieldset>
            <div style="margin-top:15px" id="search_div"></div>
        </form>
    </div>
    </body>
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    
    </html>
	<?
	exit();
}

if ($action == "item_description_popup_list_view") 
{
	echo load_html_head_contents("Item Creation popup", "../../../../", 1, 1,'','1','');
	$data = explode('**', $data);
	
	$group = trim($data[1]);
	$description = trim($data[2]);
	$code = trim($data[3]);
	$company = str_replace("'","",$data[4]);

	$item_category_id = $item_group = $item_description = $company_cond = "";
	if ($data[0] != 0) $item_category_id = " and a.item_category_id='$data[0]'";
	if ($data[1] != 0) $item_group = " and a.item_group_id ='$group'";
	if ($data[2] != "") $item_description = " and upper(a.item_description) LIKE upper('%$description%')";
	if ($data[3] != "") $item_code = " and a.item_code like '%$code%' ";
	if ($company != '') $company_cond = " and a.company_id ='$company'";
	
	$sql = "SELECT a.id, a.item_account, a.item_category_id, a.item_description,a.item_code, a.item_size, a.item_group_id, a.unit_of_measure, a.current_stock, b.item_name
	from lib_item_group b, product_details_master a
	where a.item_group_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category_id in (".implode(',',array_keys($general_item_category)).") $company_cond $item_category_id $item_group $item_description $item_code 
	";
	$arr=array(2=>$general_item_category,6=>$unit_of_measurement);
	echo create_list_view("list_view", "Item Account,Item Code,Item Category,Item Description,Item Size,Item Group,UOM","100,90,120,150,100,100","800","260",0, $sql , "js_set_value", "id,item_description", "", 1, "0,0,item_category_id,0,0,0,unit_of_measure", $arr, "item_account,item_code,item_category_id,item_description,item_size,item_name,unit_of_measure", '',"",'0,0,0,0,0,0,0');
	// echo $sql;			
	?>
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	<?
	exit(); 
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$lib_department=return_library_array( "select id, department_name from lib_department",'id','department_name');
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	$user_arr=return_library_array( "select id, user_name from user_passwd",'id','user_name');

	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_category_id=str_replace("'","", $cbo_category_id);
	$txt_item_group=str_replace("'","", $txt_item_group);
	$txt_item_group_id=str_replace("'","", $txt_item_group_id);
	$txt_item_code=str_replace("'","", $txt_item_code);
	$txt_description=str_replace("'","", $txt_description);
	$txt_requisition_no=str_replace("'","", $txt_requisition_no);
	$cbo_req_status=str_replace("'","", $cbo_req_status);
	$date_from=str_replace("'","", $txt_date_from);
	$date_to=str_replace("'","", $txt_date_to);
	
	$search_cond='';
	if ($cbo_company_id!='') $search_cond.=" and a.company_id in($cbo_company_id)";
	if ($cbo_category_id!='') $search_cond.=" and c.item_category_id in($cbo_category_id)";
	if ($txt_item_group_id!='') $search_cond.=" and c.item_group_id=$txt_item_group_id";
	if ($txt_item_code != '') $search_cond.=" and LOWER(c.item_code) like LOWER('%$txt_item_code%')";
	if ($txt_description != '') $search_cond.=" and LOWER(c.item_description) like LOWER('%$txt_description%')";
	if ($txt_requisition_no != '') $search_cond.=" and a.itemissue_req_sys_id like '%$txt_requisition_no'";

	if($date_from != '' && $date_to != '')
	{
		if ($db_type==0){$search_cond.= " and a.indent_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";}
		else{$search_cond.= " and a.indent_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";}
	}

	$sql_requ="SELECT a.id as REQU_ID, a.company_id as COMPANY_ID, a.itemissue_req_prefix_num as REQ_NUM, a.indent_date as REQ_DATE, a.department_id as DEPARTMENT_ID, a.store_id as STORE_ID, a.inserted_by as INSERTED_BY, a.ready_to_approved as READY_TO_APPROVED, a.is_approved as IS_APPROVED, b.req_qty as REQ_QTY, b.remarks as REMARKS, c.id as PROD_ID, c.item_category_id as ITEM_CATEGORY_ID, c.unit_of_measure as UNIT_OF_MEASURE, c.item_description as ITEM_DESCRIPTION, c.item_code as ITEM_CODE, c.item_group_id as ITEM_GROUP_ID, sum(e.cons_quantity) as ISSUE_QNTY
	from inv_item_issue_requisition_mst a, product_details_master c, inv_itemissue_requisition_dtls b
	left join inv_issue_master d on b.mst_id=d.req_id and d.status_active=1 
	left join inv_transaction e on d.id=e.mst_id and b.product_id=e.prod_id and e.transaction_type=2 and e.status_active=1 
	where a.id=b.mst_id and b.product_id=c.id and a.status_active=1 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 $search_cond 
	group by  a.id,a.company_id,a.itemissue_req_prefix_num,a.indent_date,a.department_id,a.store_id,a.inserted_by,a.ready_to_approved,a.is_approved,b.req_qty,b.remarks,c.id, c.item_category_id,c.unit_of_measure, c.item_description, c.item_code, c.item_group_id order by a.indent_date desc";
	// echo $sql_requ;die;

	$sql_requ_res=sql_select($sql_requ);
	foreach ($sql_requ_res as $row) 
	{
		$requ_Ids.=$row['REQU_ID'].',';
		$prod_Ids.=$row['PROD_ID'].',';
	}
	$requ_Ids=array_unique(explode(',', rtrim($requ_Ids,',')));
	$prod_Ids=array_unique(explode(',', rtrim($prod_Ids,',')));

    // Approval Part
	$requ_id_in=where_con_using_array($requ_Ids,0,'mst_id');
    $sql_approval_sql="select mst_id as REQU_ID, APPROVED_DATE, APPROVED_BY from approval_history where entry_form in(26,56) $requ_id_in and current_approval_status=1";
	// echo $sql_approval_sql;die;
    $sql_approval_res=sql_select($sql_approval_sql);
    $approval_arr=array();
    foreach ($sql_approval_res as $val) {
    	$approval_arr[$val['REQU_ID']]['APPROVED_DATE']=$val['APPROVED_DATE'];
    	$approval_arr[$val['REQU_ID']]['APPROVED_BY']=$user_arr[$val['APPROVED_BY']];
    }

	$prod_id_in=where_con_using_array($prod_Ids,0,'prod_id');
	$sql_stock = "SELECT PROD_ID, sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in (2,3,6) then cons_quantity else 0 end)) as BALANCE_QNTY
	from inv_transaction 
	where status_active=1 $prod_id_in and is_deleted=0 group by prod_id";
	// echo $sql_stock;die;
	$sql_stock_res=sql_select($sql_stock);
	$stock_arr=array();
	foreach ($sql_stock_res as $row) 
	{
		$stock_arr[$row['PROD_ID']]['stock']=$row['BALANCE_QNTY'];
	}

	$is_approved_arr=array(0=>'No', 1=>'Yes', 2=>'No', 3=>'Partial Approved');

	$table_width=1940;
	ob_start();
	
	?>
	<style>
		.wrd_brk{word-break: break-all;}
		.left{text-align: left;}
		.center{text-align: center;}
		.right{text-align: right;}
	</style>

    <div style="width:<? echo $table_width+30; ?>px; margin-left:5px">
		<table width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" align="left">
			<tr>
				<td width="100%" class="center" colspan="18" style="font-size:20px"><strong>Pending Item Issue Requisition Report</strong></td>
			</tr>
			<tr>
				<td width="100%" class="center" colspan="18" style="font-size:14px"><strong>Date Range <?echo change_date_format($date_from);?> to <?echo change_date_format($date_to);?></strong></td>
			</tr>
		</table>
		<table width="<? echo $table_width; ?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
			<thead>
				<tr>
					<th colspan="21"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
				</tr>
				<tr>
					<th width="30">SL</th>
					<th width="80">Company</th>
					<th width="80">Req No</th>
					<th width="80">Req Date</th>
					<th width="100">Item Code</th>
					<th width="100">Category</th>                  
					<th width="100">Item Group</th>
					<th width="150">Item Descriptions</th>
					<th width="100">Remarks</th>
					<th width="80">Req Qty</th>
					<th width="80">Issue Qty</th>
					<th width="80">Req. Balance</th>
					<th width="100">Department</th>
					<th width="100">Store</th>
					<th width="80">In-Hand</th>
					<th width="80">UOM</th>
					<th width="100">Req. Insert User</th>
					<th width="80">Req. Ready To Approve</th>
					<th width="80">Req. Approval Status</th>
					<th width="120">Req. Approve Date</th>
					<th >Req. Approved By</th>
				</tr>
			</thead>
		</table>

		<div style="width:<? echo $table_width+18; ?>px; max-height:300px; overflow-y:scroll" id="scroll_body">
			<table width="<? echo $table_width; ?>" class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all" id="table_body"> 
				<?
				$i=1;
				foreach ($sql_requ_res as $row)
				{
					$issue_qnty=$row['ISSUE_QNTY'];
					$req_balance=$row['REQ_QTY']-$issue_qnty;

					if((($cbo_req_status==0 || $cbo_req_status==3) && $req_balance!=0) || ($cbo_req_status==1 && $issue_qnty=='') || ($cbo_req_status==2 && $issue_qnty!='' && $req_balance!=0) || ($cbo_req_status==4 && $req_balance==0) || ($cbo_req_status==5))
					{	
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30" class="wrd_brk center"><? echo $i; ?></td>
							<td width="80" class="wrd_brk center"><p><? echo $company_arr[$row['COMPANY_ID']]; ?>&nbsp;</p></td>
							<td width="80" class="wrd_brk "><p><? echo $row['REQ_NUM']; ?>&nbsp;</p></td>
							<td width="80" class="wrd_brk center"><p>&nbsp;<? echo change_date_format($row['REQ_DATE']); ?></p></td>
							<td width="100" class="wrd_brk "><p><? echo $row['ITEM_CODE']; ?>&nbsp;</p></td>
							<td width="100" class="wrd_brk"><p><? echo $general_item_category[$row['ITEM_CATEGORY_ID']]; ?>&nbsp;</p></td>
							<td width="100" class="wrd_brk"><p><? echo $item_group_arr[$row['ITEM_GROUP_ID']]; ?>&nbsp;</p></td>
							<td width="150" class="wrd_brk"><p><? echo $row['ITEM_DESCRIPTION']; ?>&nbsp;</p></td>
							<td width="100" class="wrd_brk"><p><? echo $row['REMARKS']; ?>&nbsp;</p></td>
							<td width="80" class="wrd_brk right"><p><? echo number_format($row['REQ_QTY'],2); ?>&nbsp;</p></td>
							<td width="80" class="wrd_brk right"><p><? echo number_format($issue_qnty,2); ?>&nbsp;</p></td>
							<td width="80" class="wrd_brk right"><p><? echo number_format($req_balance,2); ?>&nbsp;</p></td>
							<td width="100" class="wrd_brk"><p><? echo $lib_department[$row['DEPARTMENT_ID']]; ?>&nbsp;</p></td>
							<td width="100" class="wrd_brk"><p><? echo $store_arr[$row['STORE_ID']]; ?>&nbsp;</p></td>
							<td width="80" class="wrd_brk right"><p><? echo number_format($stock_arr[$row['PROD_ID']]['stock'],2); ?>&nbsp;</p></td>
							<td width="80" class="wrd_brk center"><p><? echo $unit_of_measurement[$row['UNIT_OF_MEASURE']]; ?>&nbsp;</p></td>
							<td width="100" class="wrd_brk"><p><? echo $user_arr[$row['INSERTED_BY']]; ?>&nbsp;</p></td>
							<td width="80" class="wrd_brk"><p><? if ($row['READY_TO_APPROVED']==1) echo 'Yes'; else echo 'No'; ?>&nbsp;</p></td>
							<td width="80" class="wrd_brk"><p><? echo $is_approved_arr[$row['IS_APPROVED']];?></p></td>
							<td width="120" class="wrd_brk"><p><? echo $approval_arr[$row['REQU_ID']]['APPROVED_DATE'];?></p></td>
							<td class="wrd_brk"><p><? echo $approval_arr[$row['REQU_ID']]['APPROVED_BY'];?></p></td>
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
	foreach (glob("$user_id*.xls") as $filename) 
	{
		@unlink($filename);
	}
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data****$filename";
	exit();
}

?>
