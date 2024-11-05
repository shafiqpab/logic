<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_to_company")
{
	$data=explode("_",$data);
	$company_cond="";
	$company_id=$data[0];
	$transfer_criteria=$data[1];

	if ($transfer_criteria==1){
		if ($company_id != 0) $company_cond=" and id <> $company_id";
	}
	
	echo create_drop_down( "cbo_company_id_to", 160, "select id, company_name from lib_company where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "--Select Company--", 0, "load_drop_down('requires/general_transfer_requisition_controller',this.value,'load_drop_down_location_to','to_location_td' );","" );
	exit();
}

if ($action=="load_drop_down_location_from")
{
	echo create_drop_down( "cbo_location_name_from", 160,"select id,location_name from lib_location where company_id=$data and is_deleted=0  and status_active=1","id,location_name", 1, "-- Select Location--", $selected, "load_drop_down('requires/general_transfer_requisition_controller',this.value+'**'+document.getElementById('cbo_company_id').value, 'load_drop_down_store_from','from_store_td');" );
	exit();
}

if ($action=="load_drop_down_location_to")
{
	echo create_drop_down( "cbo_location_name_to", 160,"select id,location_name from lib_location where company_id=$data and is_deleted=0  and status_active=1","id,location_name", 1, "-- Select Location--", $selected, "load_drop_down('requires/general_transfer_requisition_controller',this.value+'**'+document.getElementById('cbo_transfer_criteria').value+'**'+document.getElementById('cbo_company_id').value+'**'+document.getElementById('cbo_company_id_to').value, 'load_drop_down_store_to','to_store_td');" );
	exit();
}

if ($action=="load_drop_down_store_from")
{
	list($location_id,$company)=explode('**',$data);
	echo create_drop_down( "cbo_store_name_from", 160, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and a.company_id=$company and a.location_id=$location_id and a.status_active=1 and a.is_deleted=0 and b.category_type in(".implode(",",array_keys($general_item_category)).") group by a.id, a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", 0, "" );
	exit();
}

if ($action=="load_drop_down_store_to")
{
	list($location_id,$transfer_criteria,$company,$company_to)=explode('**',$data);
	if($transfer_criteria==1)
	{
		echo create_drop_down( "cbo_store_name_to", 160, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and a.company_id=$company_to and a.location_id=$location_id and a.status_active=1 and a.is_deleted=0 and b.category_type in(".implode(",",array_keys($general_item_category)).") group by a.id, a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", 0, "" );
	}
	if($transfer_criteria==2)
	{
		echo create_drop_down( "cbo_store_name_to", 160, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and a.company_id=$company and a.location_id=$location_id and a.status_active=1 and a.is_deleted=0 and b.category_type in(".implode(",",array_keys($general_item_category)).") group by a.id, a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", 0, "" );
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
	list($company,$store_id)=explode('_',$data);
	$store_item_cat=return_field_value("item_category_id","lib_store_location","company_id=$company and id=$store_id","item_category_id")
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
            <table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                	<tr>
                        <th colspan="5" align="center"><? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                    </tr>
                	<tr>
                    	<th width="180" class="must_entry_caption">Item Category</th>
                        <th width="160">Item Group</th>
                        <th width="140">Item Code</th>
                        <th width="180">Item Description</th>
                        <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:80px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>	
                    </tr>
                    
                </thead>
                <tbody>
                	<tr class="general">
                    	<td>
                        <?
							echo create_drop_down( "cbo_item_category_id", 150,$general_item_category,"", 1, "-- Select --", $selected, "load_drop_down( 'general_transfer_requisition_controller', this.value, 'load_drop_down_group','group_td');","",$store_item_cat,"","");
						?>
                        </td>
                        <td align="center" id="group_td">
                    		<?
                    			echo create_drop_down("cbo_item_group",130,$blank_array,"",1,"-- Select --",$selected, "" );
                    		?>
                        </td>
                        <td align="center">
                    		<input type="text" style="width:90px" class="text_boxes" name="txt_item_code" id="txt_item_code" />
                        </td>
                        <td align="center">
                        	<input type="text" style="width: 130px" class="text_boxes" name="txt_item_description" id="txt_item_description" />
                        </td>
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+document.getElementById('cbo_item_category_id').value+'**'+document.getElementById('txt_item_description').value+'**'+document.getElementById('txt_item_code').value+'**'+document.getElementById('cbo_item_group').value+'**'+'<? echo $store_id;?>'+'**'+'<? echo $store_item_cat;?>'+'**'+document.getElementById('cbo_string_search_type').value, 'account_order_popup_list_view', 'search_div', 'general_transfer_requisition_controller','setFilterGrid(\'list_view\',-1)')" style="width:80px;" />
                    	</td>
                    </tr>
            	</tbody>
           	</table>
		</fieldset>
        <div style="margin-top:15px" id="search_div"></div>
	</form>
    </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if ($action=="account_order_popup_list_view")
{
	echo load_html_head_contents("Item Creation popup", "../../../", 1, 1,'','1','');
	extract($_REQUEST);
	list($company_name,$item_category_id,$item_description,$item_code,$item_group,$store_id,$store_item_cat,$cbo_string_search_type)=explode('**',$data);
	//echo $cbo_string_search_type.test;die;
    $search_con ="";
    // $item_description_lower=strtolower($item_description);
	// if($cbo_string_search_type>0)
	// {
	// 	if($cbo_string_search_type==1)
	// 	{
	// 		if($item_description != "") {$search_con =" and lower(a.item_description)='%$item_description_lower%'=";}
	// 		if($item_code!=""){$search_con .= " and a.item_code = '%$item_code'";}
	// 	}
	// 	else if($cbo_string_search_type==2)
	// 	{
	// 		if($item_description != "") {$search_con =" and lower(a.item_description) like ('$item_description_lower%')";}
	// 		if($item_code!=""){$search_con .= " and a.item_code like('$item_code%')";}
	// 	}
	// 	else if($cbo_string_search_type==3)
	// 	{
	// 		if($item_description != "") {$search_con =" and lower(a.item_description) like ('%$item_description_lower')";}
	// 		if($item_code!=""){$search_con .= " and a.item_code like('%$item_code')";}
	// 	}
	// 	else if($cbo_string_search_type==4)
	// 	{
	// 		if($item_description != "") {$search_con =" and lower(a.item_description) like ('%$item_description_lower%')";}
	// 		if($item_code!=""){$search_con .= " and a.item_code like('%$item_code%')";}
	// 	}
	// }
	// else
	// {
	// 	if($item_description != "") {$search_con =" and lower(a.item_description) like ('%$item_description_lower%')";}
	// 	if($item_code!=""){$search_con .= " and a.item_code like('%$item_code%')";}
	// }
	$item_description_upper = strtoupper(trim($item_description));
	$item_code_upper = strtoupper(trim($item_code));

	if($cbo_string_search_type>0)
	{
		$item_description_like = "like";
		$item_code_like = "like";
		if($cbo_string_search_type==1)
		{
			$item_description_like = "=";
			$item_code_like = "=";
		}
		
		if($item_description_upper!="") $search_con = " AND upper(a.item_description) $item_description_like '%$item_description_upper%' ";
		if($item_description_upper!="") $search_con .= " AND upper(a.item_code) $item_code_like '%$item_code_upper%' ";
	}
	else
	{
		if($item_description_upper!="") $search_con = " AND upper(a.item_description) like '%$item_description_upper%' ";
		if($item_description_upper!="") $search_con .= " AND upper(a.item_code) like '%$item_code_upper%' ";
	}
    

	if ($company_name!=0) $company=" and a.company_id='$company_name'"; 
	if ($item_category_id!=0) $item_category_list=" and a.item_category_id='$item_category_id'"; else $item_category_list=" and a.item_category_id in(".implode(",",array_flip($general_item_category)).")";	
	if($item_group !=0){$search_con .= " and a.item_group_id = '$item_group'";}

	$entry_cond="";
	if(str_replace("'","",$item_category_id)==4) $entry_cond="and a.entry_form=20";

	/*-------------additional code---------------------------*/
	$stor_item_cond="";
	// if($store_item_cat!="") $stor_item_cond=" and a.item_category_id in($store_item_cat)";
	$sql="SELECT a.id as ID, a.item_account as ITEM_ACCOUNT, a.sub_group_name as SUB_GROUP_NAME, a.item_category_id as ITEM_CATEGORY_ID, a.item_description as PRODUCT_NAME_DETAILS, a.item_size as ITEM_SIZE, a.item_code as ITEM_CODE, a.item_group_id as ITEM_GROUP_ID, a.unit_of_measure as UNIT_OF_MEASURE, a.current_stock as CURRENT_STOCK, a.status_active as status_active, b.item_name as ITEM_NAME, a.order_uom as ORDER_UOM, a.unit_of_measure as CONS_UOM, sum((case when c.transaction_type in(1,4,5) and c.status_active=1 and c.is_deleted=0 then c.cons_quantity else 0 end)- (case when c.transaction_type in(2,3,6) and c.status_active=1 and c.is_deleted=0 then c.cons_quantity else 0 end)) as BALANCE_STOCK
	from lib_item_group b, product_details_master a, inv_transaction c  
	where a.item_group_id=b.id and a.id=c.prod_id and a.item_group_id>0 and a.entry_form<>24 $company $search_con $item_category_list $entry_cond $stor_item_cond and c.store_id=$store_id and c.status_active=1 and a.status_active=1 
	group by a.id, a.item_account, a.sub_group_name, a.item_category_id, a.item_description, a.item_size, a.item_code, a.item_group_id, a.unit_of_measure, a.current_stock, a.status_active, b.item_name, a.order_uom
	having sum((case when c.transaction_type in(1,4,5) then c.cons_quantity else 0 end)-(case when c.transaction_type in(2,3,6) then c.cons_quantity else 0 end))>0 order by a.id";
	// echo $sql;die;
	$sql_res=sql_select($sql);
	
	?>
    <div><input type="hidden" id="item_1" /> <input type="hidden" id="re_order_lebel" />
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="860" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="90">Item Account</th>
                <th width="60">Item Number</th>
                <th width="90">Item Category</th>
                <th width="130">Item Description</th>
                <th width="60">Item Code</th>
                <th width="60">Item Size</th>
                <th width="100">Item Group</th>
                <th width="60">Cons UOM</th>
                <th width="80">Stock</th>
                <th>Product ID</th>
            </thead>
     	</table>
     </div>
     <div style="width:860px; max-height:270px;overflow-y:scroll;" >
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="840" class="rpt_table" id="list_view">
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
						<td width="90"><p><?php echo $val["ITEM_ACCOUNT"]; ?></p></td>
						<td width="60"><p><?php echo $val["ITEM_NUMBER"]; ?></p></td>
						<td width="90"><p><?php echo $item_category[$val["ITEM_CATEGORY_ID"]]; ?></p></td>
						<td width="130"><p><?php echo $val["PRODUCT_NAME_DETAILS"]; ?></p></td>
						<td width="60" align="center"><p><?php echo $val["ITEM_CODE"]; ?></p></td>
						<td width="60" align="center"><p><?php echo $val["ITEM_SIZE"]; ?></p></td>
						<td width="100"><p><?php echo $val["ITEM_NAME"]; ?></p></td>
						<td width="60" align="center"><p><?php echo $unit_of_measurement[$val["UNIT_OF_MEASURE"]]; ?></p></td>
						<td width="80" align="right"><p><?php echo number_format($stock, 2); ?></p></td>
						<td align="center"><p><?php echo $prod_id; ?></p></td>
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
        /*$lastRcvRate = sql_select("select z.cons_rate,z.prod_id, z.id
        from inv_transaction z where z.transaction_type in (1,4,5) and z.prod_id in ($data) and z.status_active = 1 and z.is_deleted = 0
        order by id desc");
        $lastRcvRateArr = array(); $prodIdChk = array();
        foreach($lastRcvRate as $row)
        {
            if($prodIdChk[$row[csf("prod_id")]] == "")
            {
                $prodIdChk[$row[csf("prod_id")]] = $row[csf("prod_id")];
                $lastRcvRateArr[$row[csf("prod_id")]] = $row[csf("cons_rate")];
            }
        }*/
		$sql_req="SELECT a.id as ID, a.item_account as ITEM_ACCOUNT, a.sub_group_name as SUB_GROUP_NAME, a.item_category_id as ITEM_CATEGORY_ID, a.item_description as PRODUCT_NAME_DETAILS, a.item_size as ITEM_SIZE, a.item_code as ITEM_CODE, a.item_group_id as ITEM_GROUP_ID, a.unit_of_measure as UNIT_OF_MEASURE, a.current_stock as CURRENT_STOCK, a.status_active as status_active, b.item_name as ITEM_NAME, a.order_uom as ORDER_UOM, a.unit_of_measure as CONS_UOM, sum((case when c.transaction_type in(1,4,5) and c.status_active=1 and c.is_deleted=0 then c.cons_quantity else 0 end)- (case when c.transaction_type in(2,3,6) and c.status_active=1 and c.is_deleted=0 then c.cons_quantity else 0 end)) as BALANCE_STOCK
		from lib_item_group b, product_details_master a, inv_transaction c 
		where a.id=$data and a.item_group_id=b.id and a.id=c.prod_id and c.store_id=$store_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0
		group by a.id, a.item_account, a.sub_group_name, a.item_category_id, a.item_description, a.item_size, a.item_code, a.item_group_id, a.unit_of_measure, a.current_stock, a.status_active, b.item_name, a.order_uom order by a.id";
		//echo $sql_req;
		$dataArray=sql_select($sql_req);
		foreach ($dataArray as $row)
		{
			$table_row++;
			// $req_rate=$lastRcvRateArr[$row[csf("id")]];
			?>
			<tr class="general" id="tr_<? echo $table_row; ?>">
				<td width="100">
					<input type="text" name="txtItemAccount_<? echo $table_row; ?>" id="txtItemAccount_<? echo $table_row; ?>" class="text_boxes" value="<? echo $row["ITEM_ACCOUNT"]; ?>" style="width:85px;" placeholder="Double click"  onDblClick="openmypage()" readonly />
				</td>
                <td width="100">
					<? echo create_drop_down( "cboItemCategory_".$table_row, 95,$general_item_category,"", 1, "-- Select --", $row["ITEM_CATEGORY_ID"], "",1,"");?>
                </td>
				<td width="100">
					<input type="text" name="txtItemGroupName_<? echo $table_row; ?>" id="txtItemGroupName_<? echo $table_row; ?>" class="text_boxes" value="<? echo $row["ITEM_NAME"];?>" style="width:85px;"/>
				</td>
                <td width="100">
					<input type="text" name="txtSubGroup_<? echo $table_row; ?>" id="txtSubGroup_<? echo $table_row; ?>" class="text_boxes" value="<? echo $row["SUB_GROUP_NAME"];?>" style="width:85px;" readonly />
				</td>
				<td id="group_td" width="280">
					<input type="text" name="txtItemDescription_<? echo $table_row; ?>" id="txtItemDescription_<? echo $table_row; ?>" class="text_boxes" value="<? echo $row["PRODUCT_NAME_DETAILS"];?>" style="width:265px;" readonly />
				</td>
                <td width="150">
					<input type="text" name="txtItemCode_<? echo $table_row; ?>" id="txtItemCode_<? echo $table_row; ?>" class="text_boxes" value="<? echo $row["ITEM_CODE"];?>" style="width:135px;" readonly />
				</td>
                <td width="70">
					<input type="text" name="txtItemSize_<? echo $table_row; ?>" id="txtItemSize_<? echo $table_row; ?>" class="text_boxes" value="<? echo $row["ITEM_SIZE"];?>" style="width:55px;" readonly />
				</td>
				<td width="70" id="tduom_<? echo $table_row; ?>">
					<input type="text" name="txtUom_<? echo $table_row; ?>" id="txtUom_<? echo $table_row; ?>" class="text_boxes" value="<? echo $unit_of_measurement[$row["UNIT_OF_MEASURE"]];?>" style="width:55px;" readonly />
				</td>
				<td width="70">
					<input type="text" name="txtReqQnty_<? echo $table_row; ?>" id="txtReqQnty_<? echo $table_row; ?>" class="text_boxes_numeric" autocomplete="off" style="width:55px;" onKeyUp="calculate_value(<? echo $table_row; ?>)" value=""/>
				</td>
				<td width="70">
					<input type="text" name="txtStock_<? echo $table_row; ?>" id="txtStock_<? echo $table_row; ?>" class="text_boxes_numeric" value="<? echo $row["BALANCE_STOCK"];?>" style="width:55px;" readonly />
				</td>
                <td>
                	<input type="text" name="txtRemarks_<? echo $table_row; ?>" id="txtRemarks_<? echo $table_row; ?>" class="text_boxes" value="" style="width:65px;"/>
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
					    <th>Year</th>
	                    <th>Search By</th>
	                    <th width="150" id="search_by_td_up">Please Enter Requisition ID</th>
	                    <th width="190">Date Range</th>
	                    <th>
	                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
	                        <input type="hidden" name="transfer_id" id="transfer_id" class="text_boxes" value="">
	                        <input type="hidden" name="to_company_id" id="to_company_id" class="text_boxes" value="">
	                    </th>
	                </thead>
	                <tr class="general">
					<td align="center"><? echo create_drop_down( "cbo_year", 100, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
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
	                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+<? echo $cbo_transfer_criteria; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company_to; ?>+'_'+document.getElementById('cbo_year').value, 'create_transfer_search_list_view', 'search_div', 'general_transfer_requisition_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
	                    </td>
	                </tr>
	                <tr>
	                    <td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
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
	$to_company =$data[6];
	$selectyear =$data[7];
	


	
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
	
	if($db_type==0){
		 $year_field="YEAR(insert_date) as year,"; 
		 $year_cond=" and YEAR(insert_date)=$selectyear";
	}
	else if($db_type==2){
	     $year_field="to_char(insert_date,'YYYY') as year,";
		 $year_cond=" and to_char(insert_date,'YYYY')=$selectyear";
    }
    else{
		$year_field="";//defined Later
		$year_cond="";
	} 

	if($to_company!=0){
		$company_to=" and to_company= $to_company";
	} 
	
	
	if($db_type==0)
	{
		$sql="SELECT id, transfer_prefix_number, $year_field transfer_system_id, challan_no, company_id, transfer_date, transfer_criteria, item_category, to_company,inserted_by,is_approved,ready_to_approve
		from inv_item_transfer_requ_mst where company_id=$company_id $company_to $year_cond and $search_field like '$search_string' and transfer_criteria=$transfer_criteria_id $transfer_date and entry_form in(494) and status_active=1 and is_deleted=0 order by id desc";
	}
	else
	{
		$sql="SELECT id, transfer_prefix_number, $year_field transfer_system_id, challan_no, company_id, transfer_date, transfer_criteria, item_category, to_company,inserted_by,is_approved,ready_to_approve 
		from inv_item_transfer_requ_mst where  company_id=$company_id $company_to $year_cond and $search_field like '$search_string' and transfer_criteria=$transfer_criteria_id $transfer_date and entry_form in(494) and status_active=1 and is_deleted=0 order by id desc";
	}

	//  echo $sql;die;
	$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$username_arr=return_library_array( "select id, user_name from user_passwd",'id','user_name');
	$yes_no_arr=array(0=>"No",1=>"Yes",2=>"No",3 => "Yes");
	$arr=array(3=>$company_arr,5=>$item_transfer_criteria,6=>$company_arr,7=>$username_arr,8=>$yes_no_arr,9=>$yes_no_arr);

	echo  create_list_view("tbl_list_search", "Requisition ID,Year,Challan No,Company,Requisition Date,Transfer Criteria,To Company,Insert user,Ready To Approve,Approval Status", "80,70,70,70,70,120,100,80,80","880","250",0, $sql, "js_set_value", "id,to_company", "", 1, "0,0,0,company_id,0,transfer_criteria,to_company,inserted_by,ready_to_approve,is_approved", $arr, "transfer_prefix_number,year,challan_no,company_id,transfer_date,transfer_criteria,to_company,inserted_by,ready_to_approve,is_approved", '','','0,0,0,0,3,0,0,0,0,0');
	
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
			echo "load_drop_down('requires/general_transfer_requisition_controller',".$row[csf('TO_COMPANY')].",'load_drop_down_location_to','to_location_td' );";
		}
		echo "document.getElementById('cbo_location_name_from').value 		= '".$row["LOCATION_ID"]."';\n";
		echo "document.getElementById('cbo_location_name_to').value 		= '".$row["TO_LOCATION_ID"]."';\n";
		echo "document.getElementById('txt_challan_no').value 				= '".$row["CHALLAN_NO"]."';\n";

		echo "load_drop_down('requires/general_transfer_requisition_controller',".$row['LOCATION_ID']."+'**'+".$row['COMPANY_ID'].", 'load_drop_down_store_from','from_store_td');";
		echo "load_drop_down('requires/general_transfer_requisition_controller',".$row['TO_LOCATION_ID']."+'**'+".$row['TRANSFER_CRITERIA']."+'**'+".$row['COMPANY_ID']."+'**'+".$row['TO_COMPANY'].", 'load_drop_down_store_to','to_store_td');" ;

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

		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_general_transfer_requisition',1,1);\n"; 
		exit();
	}
}

if($action=="show_transfer_listview")
{
	$sql="SELECT  c.id, c.from_prod_id, c.transfer_qnty, c.item_category, c.remarks, a.item_account, a.sub_group_name, a.item_description as product_name_details, a.item_size, a.item_group_id, a.unit_of_measure, a.current_stock, a.item_code, b.item_name, a.order_uom, c.from_store, sum((case when d.transaction_type in(1,4,5) and d.status_active=1 and d.is_deleted=0 then d.cons_quantity else 0 end)- (case when d.transaction_type in(2,3,6) and d.status_active=1 and d.is_deleted=0 then d.cons_quantity else 0 end)) as balance_stock
	from product_details_master a, lib_item_group b, inv_item_transfer_requ_dtls c, inv_transaction d 
	where c.mst_id in ($data) and c.entry_form=494 and c.from_prod_id=a.id and a.item_group_id=b.id and c.from_prod_id=d.prod_id and d.store_id=c.from_store and c.status_active=1 and a.status_active=1 and d.status_active=1 
	group by c.id, c.from_prod_id, c.transfer_qnty, c.item_category, c.remarks, a.item_account, a.sub_group_name, a.item_description, a.item_size, a.item_group_id, a.unit_of_measure, a.current_stock, a.item_code, b.item_name, a.order_uom, c.from_store order by c.id";
	
	$arr=array(1=>$general_item_category,7=>$unit_of_measurement);

	echo create_list_view("list_view", "Item Account,Item Category,Item Group,Item Sub. Group,Item Description,Item Code,Item Size,Cons. UOM,Req. Qty,Stock,Remarks", "100,100,100,100,120,70,70,70,70,70,100","970","100",0, $sql, "get_php_form_data", "id,from_store", "'populate_transfer_details_form_data'", 100, "0,item_category,0,0,0,0,0,unit_of_measure,0,0,0", $arr, "item_account,item_category,item_name,sub_group_name,product_name_details,item_code,item_size,unit_of_measure,transfer_qnty,balance_stock,remarks", "requires/general_transfer_requisition_controller",'','0,0,0,0,0,0,0,0,0,0,0');
	
	exit();
}
//End System ID here------------------------------//

if($action=='populate_transfer_details_form_data')
{
	$data_ref=explode("_",$data);
	//print_r($data_ref);die;
	$sql="SELECT  c.id as ID, c.from_prod_id as FROM_PROD_ID, c.transfer_qnty as TRANSFER_QNTY, c.item_category as ITEM_CATEGORY, c.remarks as REMARKS, a.item_account as ITEM_ACCOUNT, a.sub_group_name as SUB_GROUP_NAME, a.item_description as PRODUCT_NAME_DETAILS, a.item_size as ITEM_SIZE, a.item_group_id as ITEM_GROUP_ID, a.unit_of_measure as UNIT_OF_MEASURE, a.current_stock as current_stock, a.item_code as ITEM_CODE, b.item_name as ITEM_NAME, a.order_uom as ORDER_UOM, sum((case when d.transaction_type in(1,4,5) and d.status_active=1 and d.is_deleted=0 then d.cons_quantity else 0 end)- (case when d.transaction_type in(2,3,6) and d.status_active=1 and d.is_deleted=0 then d.cons_quantity else 0 end)) as BALANCE_STOCK
	from product_details_master a, lib_item_group b, inv_item_transfer_requ_dtls c, inv_transaction d
	where c.id in ($data_ref[0]) and c.entry_form=494 and c.from_prod_id=a.id and a.item_group_id=b.id and c.from_prod_id=d.prod_id and d.store_id=c.from_store and c.status_active=1 and a.status_active=1 and d.status_active=1 and d.store_id=$data_ref[1]
	group by c.id, c.from_prod_id, c.transfer_qnty, c.item_category, c.remarks, a.item_account, a.sub_group_name, a.item_description, a.item_size, a.item_group_id, a.unit_of_measure, a.current_stock, a.item_code, b.item_name, a.order_uom order by c.id";

	//echo $sql;die;
	$data_array = sql_select($sql);

	foreach ($data_array as $row) 
	{ 	
		echo "document.getElementById('updateDtlsId_1').value 			= '".$row["ID"]."';\n";
		echo "document.getElementById('txtItemAccount_1').value 		= '".$row["ITEM_ACCOUNT"]."';\n";
		if($row["ITEM_ACCOUNT"]!="") echo "$('#txtItemAccount_1').attr('title','".$row["ITEM_ACCOUNT"]."');\n";
		echo "document.getElementById('cboItemCategory_1').value 		= '".$row["ITEM_CATEGORY"]."';\n";
		if($row["ITEM_CATEGORY"]>0) echo "$('#cboItemCategory_1').attr('title','".$general_item_category[$row["ITEM_CATEGORY"]]."');\n";
		echo "document.getElementById('txtItemGroupName_1').value 		= '".$row["ITEM_NAME"]."';\n";
		echo "document.getElementById('txtSubGroup_1').value 			= '".$row["SUB_GROUP_NAME"]."';\n";
		echo "document.getElementById('txtItemDescription_1').value 	= '".$row["PRODUCT_NAME_DETAILS"]."';\n";
		if($row["PRODUCT_NAME_DETAILS"]!="") echo "$('#txtItemDescription_1').attr('title','".$row["PRODUCT_NAME_DETAILS"]."');\n";
		echo "document.getElementById('txtItemCode_1').value 			= '".$row["ITEM_CODE"]."';\n";
		if($row["ITEM_CODE"]!="") echo "$('#txtItemCode_1').attr('title','".$row["ITEM_CODE"]."');\n";
		echo "document.getElementById('txtItemSize_1').value 			= '".$row["ITEM_SIZE"]."';\n";
		echo "document.getElementById('txtUom_1').value 				= '".$unit_of_measurement[$row["UNIT_OF_MEASURE"]]."';\n";
		echo "document.getElementById('txtReqQnty_1').value 			= '".$row["TRANSFER_QNTY"]."';\n";
		echo "document.getElementById('txtStock_1').value 				= '".$row["BALANCE_STOCK"]."';\n";
		echo "document.getElementById('txtRemarks_1').value 			= '".$row["REMARKS"]."';\n";
		echo "document.getElementById('prodId_1').value 				= '".$row["FROM_PROD_ID"]."';\n";
		echo "document.getElementById('updateDtlsId_1').value 			= '".$row["ID"]."';\n";
		
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_general_transfer_requisition',1,1);\n"; 
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
		
		$transfer_requisition_num=''; $transfer_update_id=''; $entry_form_no=494; $short_prefix_name="GTR";

		if(str_replace("'","",$update_id)=="")
		{

			if($db_type==0) $year_cond="YEAR(insert_date)"; 
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later
		 	
			//$id=return_next_id( "id", "inv_item_transfer_requ_mst", 1 ) ;
			
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

		$field_array_dtls="id, mst_id, item_category, from_prod_id, transfer_qnty, remarks, from_store, to_store,  entry_form, inserted_by, insert_date,status_active,is_deleted";
			
		for($i=1; $i<=$tot_row; $i++)
		{
			$cboItemCategory="cboItemCategory_".$i;
			$prod_id="prodId_".$i;
			$txtReqQnty="txtReqQnty_".$i;
			$txtRemarks="txtRemarks_".$i;

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
				$data_array_dtls .="(".$id_dtls.",".$transfer_update_id.",".$$cboItemCategory.",".$prod_id.",".$quantity.",".$$txtRemarks.",".$cbo_store_name_from.",".$cbo_store_name_to.",".$entry_form_no.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
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

		// echo "insert into inv_item_transfer_requ_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		$rID3=sql_insert("inv_item_transfer_requ_dtls",$field_array_dtls,$data_array_dtls,0);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		}
		// echo "10**".$rID.'##'.$rID3;die;

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
		
		for($i=1; $i<=$tot_row; $i++)
		{
			$cboItemCategory="cboItemCategory_".$i;
			$prod_id="prodId_".$i;
			$txtReqQnty="txtReqQnty_".$i;
			$txtRemarks="txtRemarks_".$i;
			$updateDtlsId="updateDtlsId_".$i;

			$quantity=str_replace("'", "", $$txtReqQnty);
			$prod_id=str_replace("'", "", $$prod_id);
			$updateDtlsIds=str_replace("'", "", $$updateDtlsId);
			$current_up_data[$updateDtlsIds]+=$quantity;
		}
		
		$chk_next_transaction=sql_select("select REQUISITION_DTLS_ID, sum(TRANSFER_QNTY) as TRANSFER_QNTY from inv_item_transfer_dtls where status_active=1 and is_deleted=0 and requisition_dtls_id in ($updateDtlsIds) group by REQUISITION_DTLS_ID");
		if(count($chk_next_transaction) > 0)
		{
			foreach($chk_next_transaction as $row)
			{
				if($current_up_data[$row["REQUISITION_DTLS_ID"]]<$row["TRANSFER_QNTY"])
				{
					echo "20**Requisition Qnty Not Allow Less Then Transfer Qnty"; disconnect($con);die;
				}
			}
		}

		$entry_form_no=494;

        $is_approved = return_field_value("b.id id", "inv_item_transfer_requ_mst a, approval_history b", "a.id=b.mst_id and  a.id=$update_id and a.status_active=1 and a.is_approved=1", "id");
		if($is_approved != "" )
		{
			echo "20**Update not allowed. This Requisition is already Approved.";
			disconnect($con);die;
		}

		$field_array_update="challan_no*transfer_date*ready_to_approve*remarks*updated_by*update_date";
		$data_array_update=$txt_challan_no."*".$txt_requisition_date."*".$cbo_ready_to_approved."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$field_array_dtls_new="id, mst_id, item_category, from_prod_id, transfer_qnty, remarks, from_store, to_store, entry_form, inserted_by, insert_date,status_active,is_deleted";

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

			$quantity=str_replace("'", "", $$txtReqQnty);
			$prod_id=str_replace("'", "", $$prod_id);
			$updateDtlsId=str_replace("'", "", $$updateDtlsId);

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
					$data_array_dtls_new .="(".$id_dtls.",".$update_id.",".$$cboItemCategory.",".$prod_id.",".$quantity.",".$$txtRemarks.",".$cbo_store_name_from.",".$cbo_store_name_to.",".$entry_form_no.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
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
			$cboItemCategory="cboItemCategory_".$i;
			$prod_id="prodId_".$i;
			$txtReqQnty="txtReqQnty_".$i;
			$txtRemarks="txtRemarks_".$i;
			$updateDtlsId="updateDtlsId_".$i;

			$quantity=str_replace("'", "", $$txtReqQnty);
			$prod_id=str_replace("'", "", $$prod_id);
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

			$sql_mst = sql_select("select id from inv_item_transfer_requ_dtls where status_active=1 and is_deleted=0 and mst_id=$update_id");
			$field_array="updated_by*update_date*status_active*is_deleted";
			$data_array="".$user_id."*'".$pc_date_time."'*0*1";	
			if(count($sql_mst)==1)
			{
				$rID1=sql_update("inv_item_transfer_requ_mst",$field_array,$data_array,"id",$update_id,1);
			}
			$rID2=sql_update("inv_item_transfer_requ_dtls",$field_array,$data_array,"id",$updateDtlsId,1);
			
			// echo "10**".$rID."**".$rID1."**".$rID2."**".$rID3."**".$statusChange."**".$rID4;oci_rollback($con); die;
			if($db_type==0)
			{
				if($rID1 && $rID2)
				{
					mysql_query("COMMIT");  
					//echo "2**".$update_id."**".str_replace("'","",$updateDtlsId);
					echo "2**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id)."**0";
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
					echo "2**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id)."**0";
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

	$sql_dtls="SELECT  c.transfer_qnty as TRANSFER_QNTY, c.item_category as ITEM_CATEGORY, c.remarks as REMARKS, a.item_account as ITEM_ACCOUNT, a.sub_group_name as SUB_GROUP_NAME, a.item_description as PRODUCT_NAME_DETAILS, a.unit_of_measure as UNIT_OF_MEASURE, a.item_size as ITEM_SIZE, a.item_group_id as ITEM_GROUP_ID, a.current_stock as current_stock, a.item_code as ITEM_CODE, b.item_name as ITEM_NAME, a.order_uom as ORDER_UOM, sum((case when d.transaction_type in(1,4,5) and d.status_active=1 and d.is_deleted=0 then d.cons_quantity else 0 end)- (case when d.transaction_type in(2,3,6) and d.status_active=1 and d.is_deleted=0 then d.cons_quantity else 0 end)) as BALANCE_STOCK
	from product_details_master a, lib_item_group b, inv_item_transfer_requ_dtls c, inv_transaction d 
	where  c.mst_id=$data[1] and c.from_prod_id=a.id and a.item_group_id=b.id and c.from_prod_id=d.prod_id and d.store_id=c.from_store and c.status_active=1 and a.status_active=1 and d.status_active=1
	group by c.transfer_qnty, c.item_category, c.remarks, a.item_account, a.sub_group_name, a.item_description, a.item_size, a.item_group_id,  a.current_stock, a.item_code, b.item_name, a.order_uom, a.unit_of_measure ";
	$sql_result= sql_select($sql_dtls);
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
        	<td width="125"><strong>Criteria:</strong></td>
			<td width="175px"><? echo $item_transfer_criteria[$dataArray[0]['TRANSFER_CRITERIA']]; ?></td>
            <td width="125"><strong>Requisition No:	</strong></td>
			<td width="175px"><? echo $dataArray[0]['TRANSFER_SYSTEM_ID']; ?></td>
            <td width="125"><strong>Requisition Date :</strong></td>
			<td width="175px"><? echo change_date_format($dataArray[0]['TRANSFER_DATE']); ?></td>
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
            <td><strong>Man. Challan No:</strong></td> 
			<td><? echo $dataArray[0]['CHALLAN_NO']; ?></td>
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
				<th width="100">Sub. Group</th>
				<th width="100">Item Code</th>
				<th width="200">Item Description</th>
				<th width="80">Item Size</th>
				<th width="80"> UOM</th>
				<th width="80">Req. Qty.</th>
				<th width="80">Stock</th>
				<th >Remarks</th>
			</thead>
			<tbody>
				<?
				$i=1;
				foreach($sql_result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF";else$bgcolor="#FFFFFF";
					$requisition_qnty_sum+=$row["TRANSFER_QNTY"];
					?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td align="center"><? echo $i; ?></td>
							<td><? echo $general_item_category[$row["ITEM_CATEGORY"]]; ?></td>
							<td align="center"><? echo $row["ITEM_NAME"]; ?></td>
							<td align="center"><? echo $row["SUB_GROUP_NAME"]; ?></td>
							<td align="center"><? echo $row["ITEM_CODE"]; ?></td>
							<td align="center"><? echo $row["PRODUCT_NAME_DETAILS"]; ?></td>
							<td align="center"><? echo $row["ITEM_SIZE"]; ?></td>
							<td align="center"><? echo $unit_of_measurement[$row["UNIT_OF_MEASURE"]]; ?></td>
							<td align="right"><? echo $row["TRANSFER_QNTY"]; ?></td>
							<td align="right"><? echo $row["BALANCE_STOCK"]; ?></td>
							<td><? echo $row["REMARKS"]; ?></td>
						</tr>
					<? $i++; 
				} ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="8" align="right"><strong>Total :</strong></td>
					<td align="right"><?php echo $requisition_qnty_sum; ?></td>
					<td></td>
					<td></td>
				</tr>                           
			</tfoot>
		</table>
			<br>
    </div>

	<?
	$user_lib_name_arr=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
	
	$nameArray_approved=sql_select("SELECT approved_by as APPROVED_BY,min(approved_date) as APPROVED_DATE from approval_history  where mst_id=$data[1] and entry_form=52 group by mst_id, approved_by order by approved_date");

	if(count($nameArray_approved)>0)
	{
 		?>
		 <table  width="1000" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
       
            <thead>
            	<tr style="border:1px solid black;">
                	<th colspan="3" style="border:1px solid black;">Approval Status</th>
                </tr>
                <tr style="border:1px solid black;">
                <th width="10%" style="border:1px solid black;">Sl</th>
				<th width="60%" style="border:1px solid black;">Name</th>
				<th width="30%" style="border:1px solid black;">Approval Date</th>
                </tr>
            </thead>
            <tbody>
            <?
			$i=1;
			foreach($nameArray_approved as $row)
			{
				?>
				<tr style="border:1px solid black;">
					<td width="10%" style="border:1px solid black;"><? echo $i;?></td>
					<td width="60%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('APPROVED_BY')]];?></td>
					<td width="30%" style="border:1px solid black;text-align:center"><? echo date ("d-m-Y h:i:s",strtotime($row[csf('APPROVED_DATE')]));?></td>
				
				</tr>
                <?
				$i++;
			}
				?>
            </tbody>
        </table>
		<br/>
		<?
	}

	?>	
	<table>
        <tr height="21">
			<?
			echo signature_table(253, $data[0], "1000px",'',70,$user_lib_name_arr[$inserted_by]);
			?>
        </tr>
    </table>

   </div>   
 <?	
 exit();
}

?>
