<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$userCredential = sql_select("SELECT item_cate_id FROM user_passwd where id=$user_id");
$item_cate_id = $userCredential[0][csf('item_cate_id')];
if(trim($item_cate_id) !='') {
	$cre_cat_arr=explode(",",$item_cate_id);
	$selected_category=array( '5', '6', '7', '23' );
	$filteredArr = array_intersect( $cre_cat_arr, $selected_category );
    $item_cate_credential_cond = implode(",",$filteredArr); ;
}
else
{
	$item_cate_credential_cond="5,6,7,23";
}

if ($action=="load_drop_down_to_company")
{
	$data=explode("_",$data);
	$company_cond="";
	$company_id=$data[0];
	$transfer_criteria=$data[1];

	if ($transfer_criteria==1){
		if ($company_id != 0) $company_cond=" and id <> $company_id";
	}
	
	echo create_drop_down( "cbo_company_id_to", 160, "select id, company_name from lib_company where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "--Select Company--", 0, "load_drop_down('requires/chemical_dyes_transfer_requisition_controller',this.value,'load_drop_down_location_to','to_location_td' );lib_variable_check(2);","" );
	exit();
}

if ($action=="load_drop_down_location_from")
{
	echo create_drop_down( "cbo_location_name_from", 160,"select id,location_name from lib_location where company_id=$data and is_deleted=0  and status_active=1","id,location_name", 1, "-- Select Location--", $selected, "load_drop_down('requires/chemical_dyes_transfer_requisition_controller',this.value+'**'+document.getElementById('cbo_company_id').value, 'load_drop_down_store_from','from_store_td');" );
	exit();
}

if ($action=="load_drop_down_location_to")
{
	echo create_drop_down( "cbo_location_name_to", 160,"select id,location_name from lib_location where company_id=$data and is_deleted=0  and status_active=1","id,location_name", 1, "-- Select Location--", $selected, "load_drop_down('requires/chemical_dyes_transfer_requisition_controller',this.value+'**'+document.getElementById('cbo_transfer_criteria').value+'**'+document.getElementById('cbo_company_id').value+'**'+document.getElementById('cbo_company_id_to').value, 'load_drop_down_store_to','to_store_td');" );
	exit();
}

if ($action=="load_drop_down_store_from")
{
	list($location_id,$company)=explode('**',$data);
	echo create_drop_down( "cbo_store_name_from", 160, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and a.company_id=$company and a.location_id=$location_id and a.status_active=1 and a.is_deleted=0 and b.category_type in(5,6,7,23) group by a.id, a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", 0, "" );
	exit();
}

if ($action=="load_drop_down_store_to")
{
	list($location_id,$transfer_criteria,$company,$company_to)=explode('**',$data);
	if($transfer_criteria==1)
	{
		echo create_drop_down( "cbo_store_name_to", 160, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and a.company_id=$company_to and a.location_id=$location_id and a.status_active=1 and a.is_deleted=0 and b.category_type in(5,6,7,23) group by a.id, a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", 0, "" );
	}
	if($transfer_criteria==2)
	{
		echo create_drop_down( "cbo_store_name_to", 160, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and a.company_id=$company and a.location_id=$location_id and a.status_active=1 and a.is_deleted=0 and b.category_type in(5,6,7,23) group by a.id, a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", 0, "" );
	}
	exit();
}

//Start Item Account here------------------------------//
if($action == "load_drop_down_group")
{
	echo create_drop_down( "cbo_item_group", 130,"select a.item_name,a.id from lib_item_group a where a.item_category = $data and a.status_active = 1 and a.is_deleted  = 0 group by a.item_name, a.id order by a.id","id,item_name", 1, "-- Select --", $selected, "" );
	exit();
}

if($action=="account_order_popup")
{
	echo load_html_head_contents("Item Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	list($company,$store_id,$variable_lot)=explode('_',$data);
	?>
	<script>

	 var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();

	 function check_all_data()
    {
        var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
        tbl_row_count = tbl_row_count - 1;

        for( var i = 1; i <= tbl_row_count; i++ ) {
                eval($('#tr_'+i).attr("onclick"));
        }
    }

	function toggle( x, origColor ) 
    {
        var newColor = 'yellow';
        if ( x.style ) {
            x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
        }
    }

	function js_set_value(id)
	{
		var str=id.split("_");
		$('#re_order_lebel').val(str[2]);
		toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
		str=str[1];
		if( jQuery.inArray(  str , selected_id ) == -1 ) {
			selected_id.push( str );
		}
		else {
			for( var i = 0; i < selected_id.length; i++ ) {
				if( selected_id[i] == str  ) break;
			}
			selected_id.splice( i, 1 );
		}
		var id = '';
		for( var i = 0; i < selected_id.length; i++ ) {
			id += selected_id[i] + ',';
		}
		id = id.substr( 0, id.length - 1 );

		$('#item_1').val( id );
	}

    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:780px;">
            <table width="670" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th width="155">Item Category</th>
                    <th width="135">Item Group</th>
                    <th width="220">Item Description</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:80px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                </thead>
                <tbody>
                	<tr class="general">
                    	<td>
                        <?
							echo create_drop_down( "cbo_item_category_id", 150,$item_category,"", 1, "-- Select --", $selected, "load_drop_down( 'chemical_dyes_transfer_requisition_controller', this.value, 'load_drop_down_group','group_td');","",$item_cate_credential_cond,"","");
						?>
                        </td>
                        <td align="center" id="group_td">
                    		<?
                    			echo create_drop_down("cbo_item_group",130,$blank_array,"",1,"-- Select --",$selected, "" );
                    		?>
                        </td>

                        <td align="center">
                        	<input type="text" style="width: 200px" class="text_boxes" name="txt_item_description" id="txt_item_description" />
                        </td>
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+document.getElementById('cbo_item_category_id').value+'**'+document.getElementById('txt_item_description').value+'**'+document.getElementById('cbo_item_group').value+'**'+'<? echo $store_id;?>'+'**'+'<? echo $variable_lot;?>', 'account_order_popup_list_view', 'search_div', 'chemical_dyes_transfer_requisition_controller','setFilterGrid(\'list_view\',-1)')" style="width:80px;" />
                    	</td>
                    </tr>
            	</tbody>
           	</table>
		</fieldset>
        <div style="margin-top:15px" id="search_div"></div>
	</form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if ($action=="account_order_popup_list_view")
{
	echo load_html_head_contents("Item Creation popup", "../../../", 1, 1,'','1','');
	extract($_REQUEST);
	list($company_name,$item_category_id,$item_description,$item_group,$store_id,$variable_lot)=explode('**',$data);
    $search_con ="";
    $item_description_cond="";
    $item_description_lower=strtolower($item_description);
    if($item_description != "") {$item_description_cond =" and lower(a.product_name_details) like ('%$item_description_lower%')";}

	if ($company_name!=0) $company=" and a.company_id='$company_name'"; 	
	if($item_group !=0){$search_con .= " and a.item_group_id = '$item_group'";}
	if ($item_category_id!=0){$item_category_list=" and a.item_category_id='$item_category_id'"; }
	else{$item_category_list=" and a.item_category_id in($item_cate_credential_cond)"; }
	$lot_cond="";
	if($variable_lot==1) $lot_cond=" and a.lot is not null";
	
	$sql="SELECT a.id as ID, a.item_category_id as ITEM_CATEGORY_ID, a.item_description as PRODUCT_NAME_DETAILS, a.item_group_id as ITEM_GROUP_ID, a.current_stock as CURRENT_STOCK, a.status_active as status_active, b.item_name as ITEM_NAME, a.lot as LOT, a.order_uom as ORDER_UOM, a.unit_of_measure as CONS_UOM, sum((case when c.transaction_type in(1,4,5) and c.status_active=1 and c.is_deleted=0 then c.cons_quantity else 0 end)- (case when c.transaction_type in(2,3,6) and c.status_active=1 and c.is_deleted=0 then c.cons_quantity else 0 end)) as BALANCE_STOCK
	from lib_item_group b, product_details_master a, inv_transaction c  
	where a.item_group_id=b.id and a.id=c.prod_id and a.item_group_id>0 $company $search_con $item_category_list $item_description_cond and c.store_id=$store_id and c.status_active=1 and a.status_active=1 $lot_cond 
	group by a.id, a.item_category_id, a.item_description, a.item_group_id, a.unit_of_measure, a.current_stock, a.status_active, b.item_name, a.lot, a.order_uom
	having sum((case when c.transaction_type in(1,4,5) then c.cons_quantity else 0 end)-(case when c.transaction_type in(2,3,6) then c.cons_quantity else 0 end))>0 
	order by a.id";
	// echo $sql;
	$sql_res=sql_select($sql);
	
	?>
    <div><input type="hidden" id="item_1" /> <input type="hidden" id="re_order_lebel" />
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="760" class="rpt_table">
            <thead>
                <th width="30">SL</th>
				<th width="80">Product ID</th>
				<th width="100">Item Category</th>
				<th width="100">Item Group</th>
				<th width="150">Item Description</th>
                <th width="100">Lot</th>
				<th width="60">Order UOM</th>
                <th >Stock</th>
            </thead>
     	</table>
    </div>
    <div style="width:760px; max-height:270px;overflow-y:scroll;" >
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="740" class="rpt_table" id="list_view">
			<?
			$i=1;
            foreach( $sql_res as $val )
            {
                if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$stock=$val["BALANCE_STOCK"];
				$prod_id=$val["ID"];
				if($stock>0)
				{
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i;?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $i.'_'.$prod_id; ?>');" >
						<td width="30" align="center"><p><?php echo $i; ?></p></td>
						<td width="80" align="center"><p><?php echo $prod_id; ?></p></td>
						<td width="100"><p><?php echo $item_category[$val["ITEM_CATEGORY_ID"]]; ?></p></td>
						<td width="100"><p><?php echo $val["ITEM_NAME"]; ?></p></td>
						<td width="150"><p><?php echo $val["PRODUCT_NAME_DETAILS"]; ?></p></td>
						<td width="100"><p><?php echo $val["LOT"]; ?></p></td>
						<td width="60" align="center"><p><?php echo $unit_of_measurement[$val["ORDER_UOM"]]; ?></p></td>
						<td align="right"><p><?php echo number_format($stock, 2); ?></p></td>					
					</tr>
					<?
					$i++;
				}	
            }
			?>
		</table>
	</div>
	<table width="840" cellspacing="0" cellpadding="0" style="border:none" align="center">
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
	<?
	exit();
}

if ($action=="load_php_popup_to_form")
{
	$explode_data = explode("**",$data);
	$data=$explode_data[0];
	$table_row=$explode_data[1];
	$store_id=$explode_data[2];
	//echo  $data.test;die;
    if($data!="")
	{
		$sql_req="SELECT a.id as ID, a.item_account as ITEM_ACCOUNT, a.item_category_id as ITEM_CATEGORY_ID, a.ITEM_DESCRIPTION as ITEM_DESCRIPTION, a.item_group_id as ITEM_GROUP_ID, a.current_stock as CURRENT_STOCK, a.status_active as status_active, b.item_name as ITEM_NAME, a.lot as LOT, a.order_uom as ORDER_UOM, a.unit_of_measure as CONS_UOM, sum((case when c.transaction_type in(1,4,5) and c.status_active=1 and c.is_deleted=0 then c.cons_quantity else 0 end)- (case when c.transaction_type in(2,3,6) and c.status_active=1 and c.is_deleted=0 then c.cons_quantity else 0 end)) as BALANCE_STOCK
		from lib_item_group b, product_details_master a, inv_transaction c 
		where a.id=$data and a.item_group_id=b.id and a.id=c.prod_id and c.store_id=$store_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0
		group by a.id, a.item_account, a.item_category_id, a.ITEM_DESCRIPTION, a.item_group_id, a.unit_of_measure, a.current_stock, a.status_active, b.item_name, a.lot, a.order_uom order by a.id";
		// echo $sql_req;
		$dataArray=sql_select($sql_req);
		foreach ($dataArray as $row)
		{
			$table_row++;
			?>
			<tr class="general" id="tr_<? echo $table_row; ?>">
				<td>
					<input type="text" name="txtItemAccount_<? echo $table_row; ?>" id="txtItemAccount_<? echo $table_row; ?>" class="text_boxes" value="<? echo $row["ITEM_ACCOUNT"]; ?>" style="width:80px;" placeholder="Double click"  onDblClick="openmypage()" readonly />
				</td>
                <td>
					<? echo create_drop_down( "cboItemCategory_".$table_row, 90,$item_category,"", 1, "-- Select --", $row["ITEM_CATEGORY_ID"], "",1,"");?>
                </td>
				<td>
					<input type="text" name="txtItemGroupName_<? echo $table_row; ?>" id="txtItemGroupName_<? echo $table_row; ?>" class="text_boxes" value="<? echo $row["ITEM_NAME"];?>" style="width:80px;" readonly/>
				</td>
                <td>
				<td id="group_td">
					<input type="text" name="txtItemDescription_<? echo $table_row; ?>" id="txtItemDescription_<? echo $table_row; ?>" class="text_boxes" value="<? echo $row["ITEM_DESCRIPTION"];?>" style="width:100px;" readonly />
				</td>
                <td>
					<input type="text" name="txtLot_<? echo $table_row; ?>" id="txtLot_<? echo $table_row; ?>" class="text_boxes" value="<? echo $row["LOT"];?>" style="width:50px;" readonly />
				</td>
				<td id="tduom_<? echo $table_row; ?>">
					<input type="text" name="txtUom_<? echo $table_row; ?>" id="txtUom_<? echo $table_row; ?>" class="text_boxes" value="<? echo $unit_of_measurement[$row["ORDER_UOM"]];?>" style="width:50px;" readonly />
				</td>
				<td>
					<input type="text" name="txtReqQnty_<? echo $table_row; ?>" id="txtReqQnty_<? echo $table_row; ?>" class="text_boxes_numeric" autocomplete="off" style="width:50px;" onKeyUp="calculate_value(<? echo $table_row; ?>)" value=""/>
				</td>
				<td>
					<input type="text" name="txtStock_<? echo $table_row; ?>" id="txtStock_<? echo $table_row; ?>" class="text_boxes_numeric" value="<? echo $row["BALANCE_STOCK"];?>" style="width:50px;" readonly />
				</td>
                <td>
                	<input type="text" name="txtRemarks_<? echo $table_row; ?>" id="txtRemarks_<? echo $table_row; ?>" class="text_boxes" value="" style="width:50px;"/>
					<input type="hidden" name="prodId_<? echo $table_row; ?>" id="prodId_<? echo $table_row; ?>" value="<? echo $row["ID"];?>" />
					<input type="hidden" name="updateDtlsId_<? echo $table_row; ?>" id="updateDtlsId_<? echo $table_row; ?>" />
				</td>
			</tr>
			<?
		}
	}
	exit();
}
//End Item Account here------------------------------//

//Start System ID here------------------------------//
if ($action=="system_popup")
{
	echo load_html_head_contents("Order To Order Transfer Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?> 
	<script>
		function js_set_value(data)
		{
		    var receive_data = data.split("_");
		    //alert(receive_data[0]+"***"+receive_data[1]);return;
			$('#transfer_id').val(receive_data[0]);
			$('#to_company_id').val();
			parent.emailwindow.hide();
		}
    </script>
	</head>
	<body>
	<div align="center" style="width:900px; margin: 0 auto;">
		<form name="searchdescfrm"  id="searchdescfrm">
			<fieldset style="width:760px;margin-left:10px">
	        <legend>Enter search words</legend>
	            <table cellpadding="0" cellspacing="0" width="750" class="rpt_table">
	                <thead>
	                    <th width="180">Search By</th>
	                    <th id="search_by_td_up">Please Enter Requisition ID</th>
	                    <th width="210">Date Range</th>
	                    <th>
	                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
	                        <input type="hidden" name="transfer_id" id="transfer_id" class="text_boxes" value="">
	                        <input type="hidden" name="to_company_id" id="to_company_id" class="text_boxes" value="">
	                    </th>
	                </thead>
	                <tr class="general">
	                    <td>
							<?
								$search_by_arr=array(1=>"Transfer ID",2=>"Challan No.");
								$dd="change_search_event(this.value, '0*0', '0*0', '../../../') ";
								echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
	                        ?>
	                    </td>
	                    <td id="search_by_td">
	                        <input type="text" style="width:130px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
	                    </td>
	                    <td>
	                    	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" readonly>
	                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" readonly>
	                    </td>
	                    <td>
	                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+<? echo $cbo_transfer_criteria; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_transfer_search_list_view', 'search_div', 'chemical_dyes_transfer_requisition_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
	                    </td>
	                </tr>
	                <tr>
	                    <td colspan="4" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
	                </tr>
	            </table>	        	
				<div style="margin-top: 10px">
					<div style="margin-top:10px" id="search_div"></div> 
				</div>
			</fieldset>
		</form>
	</div>    
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=='create_transfer_search_list_view')
{
	$data = explode("_",$data);
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$company_id =$data[2];
	$transfer_criteria_id =$data[3];
	
	if($search_by==1)
		$search_field="transfer_system_id";	
	else
		$search_field="challan_no";

	if ($data[4]!="" &&  $data[5]!="") 
	{
		if($db_type==0)
		{
			$transfer_date = "and transfer_date between '".change_date_format($data[4], "yyyy-mm-dd", "-")."' and '".change_date_format($data[5], "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$transfer_date = "and transfer_date between '".change_date_format($data[4],'','',1)."' and '".change_date_format($data[5],'','',1)."'";
		}
	}
	else 
		$transfer_date ="";
	
	if($db_type==0) $year_field="YEAR(insert_date) as year,"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year,";
	else $year_field="";//defined Later
	
	if($db_type==0)
	{
		$sql="SELECT id, transfer_prefix_number, $year_field transfer_system_id, challan_no, company_id, transfer_date, transfer_criteria,  to_company, from_store_id, to_store_id
		from inv_item_transfer_requ_mst where company_id=$company_id and $search_field like '$search_string' and transfer_criteria=$transfer_criteria_id $transfer_date and entry_form in(516) and status_active=1 and is_deleted=0 order by id";
	}
	else
	{
		$sql="SELECT id, transfer_prefix_number, $year_field transfer_system_id, challan_no, company_id, transfer_date, transfer_criteria,  to_company, from_store_id, to_store_id
		from inv_item_transfer_requ_mst where  company_id=$company_id and $search_field like '$search_string' and transfer_criteria=$transfer_criteria_id $transfer_date and entry_form in(516) and status_active=1 and is_deleted=0 order by id";
	}

	// echo $sql;die;
	$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$lib_store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	$arr=array(3=>$company_arr,4=>$company_arr,6=>$item_transfer_criteria,7=>$lib_store_arr,8=>$lib_store_arr);

	echo  create_list_view("tbl_list_search", "Req ID,Year,Challan No,Company,To Company,Requisition Date,Transfer Criteria,From Store,To Store", "70,70,80,80,80,80,120,100,100","860","250",0, $sql, "js_set_value", "id,to_company", "", 1, "0,0,0,company_id,to_company,0,transfer_criteria,from_store_id,to_store_id", $arr, "transfer_prefix_number,year,challan_no,company_id,to_company,transfer_date,transfer_criteria,from_store_id,to_store_id", '','','0,0,0,0,0,3,0,0,0');
	
	exit();
}

if($action=='populate_data_from_transfer_master')
{
	$data_array=sql_select("SELECT transfer_system_id as TRANSFER_SYSTEM_ID, challan_no as CHALLAN_NO,transfer_criteria as TRANSFER_CRITERIA, company_id as COMPANY_ID, transfer_date as TRANSFER_DATE, to_company as TO_COMPANY, location_id as LOCATION_ID, to_location_id as TO_LOCATION_ID, from_store_id as FROM_STORE_ID, to_store_id as TO_STORE_ID, remarks as REMARKS, ready_to_approve as READY_TO_APPROVE, is_approved as IS_APPROVED from inv_item_transfer_requ_mst where id='$data'");
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('update_id').value 					= '".$data."';\n";
		echo "document.getElementById('txt_system_id').value 				= '".$row["TRANSFER_SYSTEM_ID"]."';\n";
		echo "document.getElementById('cbo_transfer_criteria').value 		= '".$row["TRANSFER_CRITERIA"]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row["COMPANY_ID"]."';\n";
		echo "document.getElementById('cbo_company_id_to').value 			= '".$row["TO_COMPANY"]."';\n";
		echo "document.getElementById('txt_requisition_date').value 		= '".change_date_format($row["TRANSFER_DATE"])."';\n";
		if($row[csf("to_company")]!=0)
		{
			echo "load_drop_down('requires/chemical_dyes_transfer_requisition_controller',".$row[csf('TO_COMPANY')].",'load_drop_down_location_to','to_location_td' );";
		}
		echo "document.getElementById('cbo_location_name_from').value 		= '".$row["LOCATION_ID"]."';\n";
		echo "document.getElementById('cbo_location_name_to').value 		= '".$row["TO_LOCATION_ID"]."';\n";
		echo "document.getElementById('txt_challan_no').value 				= '".$row["CHALLAN_NO"]."';\n";

		echo "load_drop_down('requires/chemical_dyes_transfer_requisition_controller',".$row['LOCATION_ID']."+'**'+".$row['COMPANY_ID'].", 'load_drop_down_store_from','from_store_td');";
		echo "load_drop_down('requires/chemical_dyes_transfer_requisition_controller',".$row['TO_LOCATION_ID']."+'**'+".$row['TRANSFER_CRITERIA']."+'**'+".$row['COMPANY_ID']."+'**'+".$row['TO_COMPANY'].", 'load_drop_down_store_to','to_store_td');" ;

		echo "document.getElementById('cbo_store_name_from').value 			= '".$row["FROM_STORE_ID"]."';\n";
		echo "document.getElementById('cbo_store_name_to').value 			= '".$row["TO_STORE_ID"]."';\n";
		echo "document.getElementById('txt_remarks').value 					= '".$row["REMARKS"]."';\n";
		echo "document.getElementById('cbo_ready_to_approved').value 		= '".$row["READY_TO_APPROVE"]."';\n";
		echo "$('#is_approved').val(".$row["IS_APPROVED"].");\n";
		if($row["IS_APPROVED"] == 1)	
		{
			echo "$('#approved').text('Approved');\n";
		}
		elseif($row["IS_APPROVED"] == 3)	
		{
			echo "$('#approved').text('Partial Approved');\n";
		}
		else
		{
			echo "$('#approved').text('');\n";
	  	}

		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_chemical_dyes_transfer_requisition',1,1);\n"; 
		exit();
	}
}

if($action=="show_transfer_listview")
{	
	$sql="SELECT  c.id, c.from_prod_id, c.transfer_qnty, c.item_category, c.remarks, a.item_account, a.sub_group_name, a.item_description, a.item_size, a.item_group_id, a.unit_of_measure, a.current_stock, a.lot, b.item_name, a.order_uom, sum((case when d.transaction_type in(1,4,5) and d.status_active=1 and d.is_deleted=0 then d.cons_quantity else 0 end)- (case when d.transaction_type in(2,3,6) and d.status_active=1 and d.is_deleted=0 then d.cons_quantity else 0 end)) as balance_stock
	from product_details_master a, lib_item_group b, inv_item_transfer_requ_dtls c, inv_transaction d 
	where c.mst_id in ($data) and c.entry_form=516 and c.from_prod_id=a.id and a.item_group_id=b.id and c.from_prod_id=d.prod_id and d.store_id=c.from_store and c.status_active=1 and a.status_active=1 and d.status_active=1 
	group by c.id, c.from_prod_id, c.transfer_qnty, c.item_category, c.remarks, a.item_account, a.sub_group_name, a.item_description, a.item_size, a.item_group_id, a.unit_of_measure, a.current_stock, a.lot, b.item_name, a.order_uom order by c.id";
	
	$arr=array(0=>$item_category,4=>$unit_of_measurement);

	echo create_list_view("list_view", "Item Category,Item Group,Item Description,Lot,Order UOM,Req. Qty,Stock,Remarks", "100,100,120,70,70,70,70,100","750","100",0, $sql, "get_php_form_data", "id", "'populate_transfer_details_form_data'", 100, "item_category,0,0,0,order_uom,0,0,0", $arr, "item_category,item_name,item_description,lot,order_uom,transfer_qnty,balance_stock,remarks", "requires/chemical_dyes_transfer_requisition_controller",'','0,0,0,0,0,0,0,0');
	
	exit();
}
//End System ID here------------------------------//

if($action=='populate_transfer_details_form_data')
{
	$sql="SELECT  c.id as ID, c.from_prod_id as FROM_PROD_ID, c.transfer_qnty as TRANSFER_QNTY, c.item_category as ITEM_CATEGORY, c.remarks as REMARKS, a.item_account as ITEM_ACCOUNT, a.product_name_details as PRODUCT_NAME_DETAILS, a.item_group_id as ITEM_GROUP_ID, a.unit_of_measure as UNIT_OF_MEASURE, a.current_stock as current_stock, a.lot as LOT, b.item_name as ITEM_NAME, a.order_uom as ORDER_UOM, sum((case when d.transaction_type in(1,4,5) and d.status_active=1 and d.is_deleted=0 then d.cons_quantity else 0 end)- (case when d.transaction_type in(2,3,6) and d.status_active=1 and d.is_deleted=0 then d.cons_quantity else 0 end)) as BALANCE_STOCK
	from product_details_master a, lib_item_group b, inv_item_transfer_requ_dtls c, inv_transaction d
	where c.id in ($data) and c.entry_form=516 and c.from_prod_id=a.id and a.item_group_id=b.id and c.from_prod_id=d.prod_id and d.store_id=c.from_store and c.status_active=1 and a.status_active=1 and d.status_active=1
	group by c.id, c.from_prod_id, c.transfer_qnty, c.item_category, c.remarks, a.item_account, a.product_name_details, a.item_group_id, a.unit_of_measure, a.current_stock, a.lot, b.item_name, a.order_uom order by c.id";

	// echo $sql;die;
	$data_array = sql_select($sql);

	foreach ($data_array as $row) 
	{ 	
		echo "document.getElementById('updateDtlsId_1').value 			= '".$row["ID"]."';\n";
		echo "document.getElementById('txtItemAccount_1').value 		= '".$row["ITEM_ACCOUNT"]."';\n";
		echo "document.getElementById('cboItemCategory_1').value 		= '".$row["ITEM_CATEGORY"]."';\n";
		echo "document.getElementById('txtItemGroupName_1').value 		= '".$row["ITEM_NAME"]."';\n";
		echo "document.getElementById('txtItemDescription_1').value 	= '".$row["PRODUCT_NAME_DETAILS"]."';\n";
		echo "document.getElementById('txtLot_1').value 				= '".$row["LOT"]."';\n";
		echo "document.getElementById('txtUom_1').value 				= '".$unit_of_measurement[$row["ORDER_UOM"]]."';\n";
		echo "document.getElementById('txtReqQnty_1').value 			= '".$row["TRANSFER_QNTY"]."';\n";
		echo "document.getElementById('txtStock_1').value 				= '".$row["BALANCE_STOCK"]."';\n";
		echo "document.getElementById('txtRemarks_1').value 			= '".$row["REMARKS"]."';\n";
		echo "document.getElementById('prodId_1').value 				= '".$row["FROM_PROD_ID"]."';\n";
		
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_chemical_dyes_transfer_requisition',1,1);\n"; 
		exit();
	}
}

//data save update delete here------------------------------//
if($action=="save_update_delete")
{	 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
    
    $max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$cbo_item_desc and transaction_type in (1,4,5)", "max_date");      
	if($max_recv_date != "")
    {
        $max_recv_date = date("Y-m-d", strtotime($max_recv_date));
        $transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_requisition_date)));
        if ($transfer_date < $max_recv_date) 
        {
            echo "20**Transfer Date Can not Be Less Than Last Receive Date Of This Lot";
            die;
        }
    }
	
	for($i=1; $i<=$tot_row; $i++)
	{
		$prod_id="prodId_".$i;
		$all_pord_id[str_replace("'", "", $$prod_id)]=str_replace("'", "", $$prod_id);
		$updateDtlsId="updateDtlsId_".$i;
		$update_dtls_id=$$updateDtlsId ;
	}
	
	//$update_dtls_id $update_id 
	$mst_id=str_replace("'","",$update_id);
	$up_dtls_id=str_replace("'","",$update_dtls_id);
	$up_cond="";
	if($up_dtls_id) $up_cond=" and id <> $up_dtls_id";
	
	if($mst_id>0 && count($all_pord_id)>0)
	{
		if(is_duplicate_field( "id", "inv_item_transfer_requ_dtls", "mst_id=$mst_id and from_prod_id in(".implode(",",$all_pord_id).") and status_active=1 and is_deleted=0 $up_cond") == 1)
		{
			echo "11**Duplicate Item Not Allow In Same Requisition"; disconnect($con);die;
		}
	}
	
	    
	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		$transfer_requisition_num=''; $transfer_update_id=''; $entry_form_no=516; $short_prefix_name="CTR";

		if(str_replace("'","",$update_id)=="")
		{

			if($db_type==0) $year_cond="YEAR(insert_date)"; 
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later
		 			
			$id = return_next_id_by_sequence("ITEM_TRAN_REQU_MST_PK_SEQ", "inv_item_transfer_requ_mst", $con);
			$new_transfer_system_id = explode("*", return_next_id_by_sequence("ITEM_TRAN_REQU_MST_PK_SEQ", "inv_item_transfer_requ_mst",$con,1,$cbo_company_id,$short_prefix_name,$entry_form_no,date("Y",time()) ));
					 
			$field_array="id, transfer_prefix, transfer_prefix_number, transfer_system_id, company_id, challan_no, transfer_date, entry_form, transfer_criteria, to_company, location_id, to_location_id, from_store_id, to_store_id, ready_to_approve, remarks, inserted_by, insert_date,status_active,is_deleted";
			
			$data_array="(".$id.",'".$new_transfer_system_id[1]."',".$new_transfer_system_id[2].",'".$new_transfer_system_id[0]."',".$cbo_company_id.",".$txt_challan_no.",".$txt_requisition_date.",".$entry_form_no.",".$cbo_transfer_criteria.",".$cbo_company_id_to.",".$cbo_location_name_from.",".$cbo_location_name_to.",".$cbo_store_name_from.",".$cbo_store_name_to.",".$cbo_ready_to_approved.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			
			$transfer_requisition_num=$new_transfer_system_id[0];
			$transfer_update_id=$id;
		}
		else
		{
			$is_approved = return_field_value("b.id id", "inv_item_transfer_requ_mst a, approval_history b", "a.id=b.mst_id and  a.id=$update_id and a.status_active=1 and a.is_approved=1", "id");
			if($is_approved != "" )
			{
				echo "20**Update not allowed. This Requisition is already Approved.";
				disconnect($con);die;
			}

			$field_array_update="challan_no*transfer_date*ready_to_approve*remarks*updated_by*update_date";
			$data_array_update=$txt_challan_no."*".$txt_requisition_date."*".$cbo_ready_to_approved."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			$transfer_requisition_num=str_replace("'","",$txt_system_id);
			$transfer_update_id=str_replace("'","",$update_id);
		}

		$field_array_dtls="id, mst_id, item_category, from_prod_id, transfer_qnty, remarks, from_store, to_store, yarn_lot, entry_form, inserted_by, insert_date,status_active,is_deleted";
			
		for($i=1; $i<=$tot_row; $i++)
		{
			$cboItemCategory="cboItemCategory_".$i;
			$prod_id="prodId_".$i;
			$txtReqQnty="txtReqQnty_".$i;
			$txtRemarks="txtRemarks_".$i;
			$txtLot="txtLot_".$i;

			$quantity=str_replace("'", "", $$txtReqQnty);
			$prod_id=str_replace("'", "", $$prod_id);

            //-----------------------------Check Transfer date with Last Receive Date  for Trasfer In-----------------
			$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$prod_id and transaction_type in (1,4,5)", "max_date");      
            if($max_recv_date != "")
            {
                $max_recv_date = date("Y-m-d", strtotime($max_recv_date));
                $transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_requisition_date)));
                if ($transfer_date < $max_recv_date) 
                {
                    echo "20**Transfer Out Date Can not Be Less Than Last Receive Date Of This Lot";
                    if($db_type == 0)
                    {
                        mysql_query("ROLLBACK"); 
                    }else{
                        oci_rollback($con);
                    }
                    disconnect($con);
                    die;
                }
            }
            //-----------------------------Check Transfer date with Last Issue Date  for Trasfer In-----------------
            $max_issue_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$prod_id and transaction_type in (2,3,6)", "max_date");      
            if($max_issue_date != "")
            {
                $max_issue_date = date("Y-m-d", strtotime($max_issue_date));
                $transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_requisition_date)));
                if ($transfer_date < $max_issue_date) 
                {
                    echo "20**Transfer In Date Can not Be Less Than Last Issue Date Of This Lot";
                    if($db_type == 0)
                    {
                        mysql_query("ROLLBACK"); 
                    }else{
                        oci_rollback($con);
                    }
                    disconnect($con);
                    die;
                }
            }

			if($quantity!="")
			{
				if ($i!=1) $data_array_dtls .=",";
				$id_dtls = return_next_id_by_sequence("ITEM_TRAN_REQU_DTLS_PK_SEQ", "inv_item_transfer_requ_dtls", $con);	
				$data_array_dtls .="(".$id_dtls.",".$transfer_update_id.",".$$cboItemCategory.",".$prod_id.",".$quantity.",".$$txtRemarks.",".$cbo_store_name_from.",".$cbo_store_name_to.",".$$txtLot.",".$entry_form_no.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			}
		}
		// echo "insert into inv_item_transfer_requ_mst (".$field_array.") values ".$data_array;die;
		if(str_replace("'","",$update_id)=="") 
		{
			$rID=sql_insert("inv_item_transfer_requ_mst",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0;
		}
		else
		{
			$rID=sql_update("inv_item_transfer_requ_mst",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID) $flag=1; else $flag=0; 
		} 

		//echo "10**insert into inv_item_transfer_requ_dtls (".$field_array_dtls.") values ".$data_array_dtls;oci_rollback($con);disconnect($con);die;
		$rID3=sql_insert("inv_item_transfer_requ_dtls",$field_array_dtls,$data_array_dtls,0);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		}
		//echo "10**".$rID.'##'.$rID3.'##'.$flag;oci_rollback($con);disconnect($con);die;

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".$transfer_update_id."**".$transfer_requisition_num."**0";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**"."&nbsp;"."**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);  
				echo "0**".$transfer_update_id."**".$transfer_requisition_num."**0";
			}
			else
			{
				oci_rollback($con);
				echo "5**0**"."&nbsp;"."**0";
			}
		}
		disconnect($con);
		die;
	}	
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();		
		if($db_type==0)	{ mysql_query("BEGIN"); }

		$entry_form_no=516;

        $is_approved = return_field_value("b.id id", "inv_item_transfer_requ_mst a, approval_history b", "a.id=b.mst_id and  a.id=$update_id and a.status_active=1 and a.is_approved=1", "id");
		if($is_approved != "" )
		{
			echo "20**Update not allowed. This Requisition is already Approved.";
			disconnect($con);die;
		}

		$field_array_update="challan_no*transfer_date*ready_to_approve*remarks*updated_by*update_date";
		$data_array_update=$txt_challan_no."*".$txt_requisition_date."*".$cbo_ready_to_approved."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$field_array_dtls_new="id, mst_id, item_category, from_prod_id, transfer_qnty, remarks, from_store, to_store, yarn_lot, entry_form, inserted_by, insert_date,status_active,is_deleted";

		$field_array_dtls="transfer_qnty*remarks*updated_by*update_date";
		$data_array_dtls_new="";
		$j=1;
		for($i=1; $i<=$tot_row; $i++)
		{
			$cboItemCategory="cboItemCategory_".$i;
			$prod_id="prodId_".$i;
			$txtReqQnty="txtReqQnty_".$i;
			$txtRemarks="txtRemarks_".$i;
			$updateDtlsId="updateDtlsId_".$i;
			$txtLot="txtLot_".$i;

			$quantity=str_replace("'", "", $$txtReqQnty);
			$prod_id=str_replace("'", "", $$prod_id);
			$updateDtlsId=str_replace("'", "", $$updateDtlsId);
			
			$trans_id = return_field_value("id", "inv_item_transfer_dtls", "requisition_dtls_id=$updateDtlsId", "id"); 
			if($trans_id)
			{
				echo "20**Transfer Found So Update Not Allow";oci_rollback($con);disconnect($con);die;
			}

            //-----------------------------Check Transfer date with Last Receive Date  for Trasfer In-----------------
			$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$prod_id and transaction_type in (1,4,5)", "max_date");      
            if($max_recv_date != "")
            {
                $max_recv_date = date("Y-m-d", strtotime($max_recv_date));
                $transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_requisition_date)));
                if ($transfer_date < $max_recv_date) 
                {
                    echo "20**Transfer Out Date Can not Be Less Than Last Receive Date Of This Lot";
                    if($db_type == 0)
                    {
                        mysql_query("ROLLBACK"); 
                    }else{
                        oci_rollback($con);
                    }
                    disconnect($con);
                    die;
                }
            }
            //-----------------------------Check Transfer date with Last Issue Date  for Trasfer In-----------------
            $max_issue_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$prod_id and transaction_type in (2,3,6)", "max_date");      
            if($max_issue_date != "")
            {
                $max_issue_date = date("Y-m-d", strtotime($max_issue_date));
                $transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_requisition_date)));
                if ($transfer_date < $max_issue_date) 
                {
                    echo "20**Transfer In Date Can not Be Less Than Last Issue Date Of This Lot";
                    if($db_type == 0)
                    {
                        mysql_query("ROLLBACK"); 
                    }else{
                        oci_rollback($con);
                    }
                    disconnect($con);
                    die;
                }
            }

			if($quantity!="")
			{
				if($updateDtlsId!="")
				{
					$update_dtls_id=$updateDtlsId;
					$data_array_dtls ="".$quantity."*".$$txtRemarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				}
				else
				{
					if ($j!=1) $data_array_dtls_new .=",";
					$id_dtls = return_next_id_by_sequence("ITEM_TRAN_REQU_DTLS_PK_SEQ", "inv_item_transfer_requ_dtls", $con);	
					$data_array_dtls_new .="(".$id_dtls.",".$update_id.",".$$cboItemCategory.",".$prod_id.",".$quantity.",".$$txtRemarks.",".$cbo_store_name_from.",".$cbo_store_name_to.",".$$txtLot.",".$entry_form_no.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					$j++;
				}
			}
		}
		
		$rID=sql_update("inv_item_transfer_requ_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		$rID2=sql_update("inv_item_transfer_requ_dtls",$field_array_dtls,$data_array_dtls,"id",$update_dtls_id,1);
		$rID3=1;
		if($data_array_dtls_new!="")
		{
			$rID3=sql_insert("inv_item_transfer_requ_dtls",$field_array_dtls_new,$data_array_dtls_new,0);
		}
		// echo "10**".$rID.'**'.$rID2.'**'.$rID3;die;

		if($db_type==0)
		{
			if($rID==1 && $rID2==1 && $rID3==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id)."**0";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**0**"."&nbsp;"."**1";
			}
		}
		else
		{
			if($rID==1 && $rID2==1 && $rID3==1)
			{
				oci_commit($con);  
				echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id)."**0";
			}
			else
			{
				oci_rollback($con);
				echo "6**0**"."&nbsp;"."**1";
			}
		}
		disconnect($con);
		die;
 	}
	else if ($operation==2) //Delete Here----------------------------------------------------------
	{
		$con = connect(); 
		if($db_type==0)	{ mysql_query("BEGIN"); }
		$update_id = str_replace("'","",$update_id);
		for($i=1; $i<=$tot_row; $i++)
		{
			$updateDtlsId="updateDtlsId_".$i;
			$updateDtlsId=str_replace("'", "", $$updateDtlsId);
		}
		
		$chk_next_transaction=return_field_value("id","inv_item_transfer_dtls"," status_active=1 and is_deleted=0 and requisition_dtls_id in ($updateDtlsId)","id");
		if($chk_next_transaction !="")
		{ 
			echo "20**Delete not allowed.This item is used in another transaction"; disconnect($con);die;
		}
		else 
		{
			$rID1=$rID2=true;
			$restLoad=1;
			$sql_mst = sql_select("select id from inv_item_transfer_requ_dtls where status_active=1 and is_deleted=0 and mst_id=$update_id");
			$field_array="updated_by*update_date*status_active*is_deleted";
			$data_array="".$user_id."*'".$pc_date_time."'*0*1";	
			if(count($sql_mst)==1)
			{
				$rID1=sql_update("inv_item_transfer_requ_mst",$field_array,$data_array,"id",$update_id,1);
				$restLoad=2;
			}
			$rID2=sql_update("inv_item_transfer_requ_dtls",$field_array,$data_array,"id",$updateDtlsId,1);
			
			// echo "10**".$rID1."**".$rID2."**".$restLoad;oci_rollback($con); die;
			if($db_type==0)
			{
				if($rID1 && $rID2)
				{
					mysql_query("COMMIT");  
					//echo "2**".$update_id."**".str_replace("'","",$updateDtlsId);
					echo "2**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id)."**".$restLoad;
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".$update_id."**".str_replace("'","",$txt_system_id);
				}
			}
			if($db_type==2 || $db_type==1 )
			{
				if($rID1 && $rID2)
				{
					oci_commit($con);
					echo "2**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id)."**".$restLoad;
				}
				else
				{
					oci_rollback($con); 
					echo "10**".$update_id."**".str_replace("'","",$txt_system_id);
				}
			}
			disconnect($con);
			die;
		}
	}
}

//Print here------------------------------//
if ($action=="general_transfer_requisition_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
		
	$sql="SELECT transfer_system_id as TRANSFER_SYSTEM_ID, challan_no as CHALLAN_NO, transfer_criteria as TRANSFER_CRITERIA, company_id as  COMPANY_ID, transfer_date as TRANSFER_DATE, to_company as TO_COMPANY, location_id as LOCATION_ID, to_location_id as TO_LOCATION_ID, from_store_id as FROM_STORE_ID, to_store_id as TO_STORE_ID, remarks as REMARKS,inserted_by as INSERTED_BY from inv_item_transfer_requ_mst a where id='$data[1]' and company_id='$data[0]'";
	//echo $sql;die;
	$dataArray=sql_select($sql);
	$inserted_by=$dataArray[0]['INSERTED_BY'];
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name"  );

	$sql_dtls="SELECT c.id as DTLS_ID, c.transfer_qnty as TRANSFER_QNTY, c.item_category as ITEM_CATEGORY, c.remarks as REMARKS, a.product_name_details as PRODUCT_NAME_DETAILS, a.lot as LOT, b.item_name as ITEM_NAME, a.order_uom as ORDER_UOM, sum((case when d.transaction_type in(1,4,5) and d.status_active=1 and d.is_deleted=0 then d.cons_quantity else 0 end)- (case when d.transaction_type in(2,3,6) and d.status_active=1 and d.is_deleted=0 then d.cons_quantity else 0 end)) as BALANCE_STOCK
	from product_details_master a, lib_item_group b, inv_item_transfer_requ_dtls c, inv_transaction d 
	where  c.mst_id=$data[1] and c.from_prod_id=a.id and a.item_group_id=b.id and c.from_prod_id=d.prod_id and d.store_id=c.from_store and c.status_active=1 and a.status_active=1 and d.status_active=1
	group by c.id,c.transfer_qnty, c.item_category, c.remarks, a.product_name_details, a.lot, b.item_name, a.order_uom ";
	$sql_result= sql_select($sql_dtls);
	$dtls_arr=array();
	foreach($sql_result as $row)
	{
		$dtls_arr[$row['ORDER_UOM']][$row['DTLS_ID']]['transfer_qnty']=$row['TRANSFER_QNTY'];
		$dtls_arr[$row['ORDER_UOM']][$row['DTLS_ID']]['item_category']=$row['ITEM_CATEGORY'];
		$dtls_arr[$row['ORDER_UOM']][$row['DTLS_ID']]['remarks']=$row['REMARKS'];
		$dtls_arr[$row['ORDER_UOM']][$row['DTLS_ID']]['product_name_details']=$row['PRODUCT_NAME_DETAILS'];
		$dtls_arr[$row['ORDER_UOM']][$row['DTLS_ID']]['lot']=$row['LOT'];
		$dtls_arr[$row['ORDER_UOM']][$row['DTLS_ID']]['item_name']=$row['ITEM_NAME'];
		$dtls_arr[$row['ORDER_UOM']][$row['DTLS_ID']]['order_uom']=$row['ORDER_UOM'];
		$dtls_arr[$row['ORDER_UOM']][$row['DTLS_ID']]['balance_stock']=$row['BALANCE_STOCK'];
	}
	?>
	<div style="width:930px;">
    <table width="900" cellspacing="0" align="right">
        <tr>
            <td colspan="6" align="center" style="font-size:xx-large"><strong><? echo $company_library[$dataArray[0]['TO_COMPANY']]; ?></strong></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="6" align="center" style="font-size:14px">  
				<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
					foreach ($nameArray as $result)
					{ 
						?>
						Plot No: <? echo $result['plot_no']; ?> 
						Level No: <? echo $result['level_no']?>
						Road No: <? echo $result['road_no']; ?> 
						Block No: <? echo $result['block_no'];?> 
						City No: <? echo $result['city'];?> 
						Zip Code: <? echo $result['zip_code']; ?> 
						Province No: <?php echo $result['province'];?> 
						Country: <? echo $country_arr[$result['country_id']]; ?><br> 
						Email Address: <? echo $result['email'];?> 
						Website No: <? echo $result['website'];
					}
                ?> 
            </td>  
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:x-large"><strong><u><? echo $data[2]; ?></u></strong></td>
        </tr>
        <tr>
        	<td width="100"><strong>Criteria:</strong></td>
			<td width="175px"><? echo $item_transfer_criteria[$dataArray[0]['TRANSFER_CRITERIA']]; ?></td>
            <td width="125"><strong>Challan No:</strong></td>
			<td width="175px"><? echo $dataArray[0]['TRANSFER_SYSTEM_ID']; ?></td>
            <td width="125"><strong>Requisition Date:</strong></td>
			<td ><? echo change_date_format($dataArray[0]['TRANSFER_DATE']); ?></td>
        </tr>
        <tr>
            <td><strong>From Store:</strong></td> 
			<td><? echo $store_library[$dataArray[0]['FROM_STORE_ID']]; ?></td>
            <td><strong>From Location:</strong></td> 
			<td><? echo $location_library[$dataArray[0]['LOCATION_ID']]; ?></td>
            <td valign='top'><strong>From Company:</strong></td> 
			<td><? echo $company_library[$data[0]]; ?></td>
        </tr>
        <tr>
            <td><strong>To Store:</strong></td> 
			<td><? echo $store_library[$dataArray[0]['TO_STORE_ID']]; ?></td>
            <td><strong>To Location:</strong></td> 
			<td><? echo $location_library[$dataArray[0]['TO_LOCATION_ID']]; ?></td>
            <td><strong>To Company:</strong></td> 
			<td><? echo $company_library[$dataArray[0]['TO_COMPANY']]; ?></td>
        </tr>
		<tr>
            <td valign='top'><strong>Remarks:</strong></td> 
			<td colspan="5" ><? echo $dataArray[0]['REMARKS']; ?></td>
        </tr>
    </table>
    <br>
    <div style="width:100%;">
		<table  cellspacing="0" width="1000"  border="1" rules="all" class="rpt_table" >
			<thead bgcolor="#dddddd" align="center">
				<th width="30">SL</th>
				<th width="100">Item Category</th>
				<th width="100">Item Group</th>
				<th width="200">Item Description</th>
				<th width="100"> Lot</th>
				<th width="80"> UOM</th>
				<th width="80">Req. Qty.</th>
				<th width="80">Stock</th>
				<th >Remarks</th>
			</thead>
			<tbody>
				<?
				foreach($dtls_arr as $uomID=>$uomVal)
				{
					$i=1;
					$requisition_qnty_sum=0;
					foreach($uomVal as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF";else$bgcolor="#FFFFFF";
						$requisition_qnty_sum+=$row["transfer_qnty"];
						?>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<td align="center"><? echo $i; ?></td>
								<td><? echo $item_category[$row["item_category"]]; ?></td>
								<td align="center"><? echo $row["item_name"]; ?></td>
								<td align="center"><? echo $row["product_name_details"]; ?></td>
								<td align="center"><? echo $row["lot"]; ?></td>
								<td align="center"><? echo $unit_of_measurement[$row["order_uom"]]; ?></td>
								<td align="right"><? echo number_format($row["transfer_qnty"],2); ?></td>
								<td align="right"><? echo $row["balance_stock"]; ?></td>
								<td><? echo $row["remarks"]; ?></td>
							</tr>
						<? $i++; 
					}
					?>
						<tr>
							<td colspan="6" align="right"><strong>Total :</strong></td>
							<td align="right"><?php echo number_format($requisition_qnty_sum,2); ?></td>
							<td></td>
							<td></td>
						</tr> 
					<?
			 	} 
				?>
			</tbody>
		</table>
			<br>
    </div>	
	<table>
        <tr height="21">
			<td>Note: This is Software Generated Copy , Signature is not Required.</td>
        </tr>
    </table>

   </div>   
 <?	
 exit();
}


if($action=="populate_data_lib_data")
{
	$data_ref=explode("__",$data);
	$from_com_id=$data_ref[0];
	$to_com_id=$data_ref[1];
	
	$sql = sql_select("select AUTO_TRANSFER_RCV, ID from variable_settings_inventory where company_name = $from_com_id and variable_list = 29 and is_deleted = 0 and status_active = 1");
	$sql_to = sql_select("select AUTO_TRANSFER_RCV, ID from variable_settings_inventory where company_name = $to_com_id and variable_list = 29 and is_deleted = 0 and status_active = 1");
	//$variable_inventory=return_field_value("store_method","variable_settings_inventory","company_name='$data' and item_category_id in (5,6,7,22,23) and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	
	//$sql_variable_requisition =  sql_select("select user_given_code_status as USER_GIVEN_CODE_STATUS,id from variable_settings_inventory where company_name = $data and variable_list =30 and item_category_id=5 and is_deleted = 0 and status_active = 1");
	if($sql[0]["AUTO_TRANSFER_RCV"]=="") $sql[0]["AUTO_TRANSFER_RCV"]=0;
	if($sql_to[0]["AUTO_TRANSFER_RCV"]=="") $sql_to[0]["AUTO_TRANSFER_RCV"]=0;
	echo $sql[0]["AUTO_TRANSFER_RCV"]."__".$sql_to[0]["AUTO_TRANSFER_RCV"];
	exit();
}

?>
