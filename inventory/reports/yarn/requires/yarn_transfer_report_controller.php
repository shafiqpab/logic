<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if ($_SESSION['logic_erp']['user_id'] == "")
{
	header("location:login.php");
	die;
}
$permission = $_SESSION['page_permission'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

//--------------------------------------------------------------------------------------------
//for company_popup
if($action == "company_popup")
{
	echo load_html_head_contents("Company Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array();
		var selected_name = new Array();

		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'table_body' ).rows.length;
			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ )
			{
				js_set_value( i );
			}
		}

		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			if ( x.style )
			{
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function set_all()
		{
			var old=document.getElementById('selected_ids').value;
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
			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 )
			{
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );

			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == $('#txt_individual_id' + str).val() )
						break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}

			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ )
			{
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}

			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );

			$('#selected_id').val(id);
			$('#selected_name').val(name);
		}
	</script>
</head>
<fieldset style="width:390px">
	<legend>Item Details</legend>
	<input type="hidden" name="selected_name" id="selected_name" value="">
	<input type="hidden" name="selected_id" id="selected_id" value="">
	<table width="390" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
		<thead>
			<tr>
				<th width="50">SL</th>
                <th width="">Company Name</th>
			</tr>
		</thead>
	</table>
	<div style="width:390px; overflow-y:scroll; max-height:300px" id="scroll_body">
		<table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
			<?
			$i = 1;
			$cbo_company_name=str_replace("'","",$cbo_company_name);
			
			$sql="select id, company_name from lib_company where status_active=1 and is_deleted=0";
			//echo $sql;
			$result=sql_select($sql);
			$selected_id_arr=explode(",",$cbo_company_name);
			foreach ($result as $row)
			{
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";

				if(in_array($row[csf("id")],$selected_id_arr))
				{
					if($selected_ids=="") $selected_ids=$i; else $selected_ids.=",".$i;
				}
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value(<? echo $i; ?>)" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
					<td width="50" align="center">
						<? echo $i; ?>
						<input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $i ?>" value="<? echo $row[csf("id")]; ?>"/>
						<input type="hidden" name="txt_individual" id="txt_individual<? echo $i ?>" value="<? echo $row[csf("company_name")]; ?>"/>
					</td>
                    <td width=""><p><? echo $row[csf("company_name")]; ?></p></td>
				</tr>
				<?
				$i++;
			}
			?>
			<input type="hidden" name="selected_ids" id="selected_ids" value="<? echo $selected_ids; ?>"/>
		</table>
	</div>
	<table width="390" cellspacing="0" cellpadding="0" style="border:none" align="center">
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
</fieldset>
<script type="text/javascript">
	setFilterGrid('table_body',-1);
	set_all();
</script>
<?
exit();
}

//for store_popup
if($action == "store_popup")
{
	echo load_html_head_contents("Store Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array();
		var selected_name = new Array();

		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'table_body' ).rows.length;
			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ )
			{
				js_set_value( i );
			}
		}

		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function set_all()
		{
			var old=document.getElementById('selected_ids').value;
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
			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 )
			{
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );

			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}

			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ )
			{
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}

			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );

			$('#selected_id').val(id);
			$('#selected_name').val(name);
		}
	</script>
</head>
<fieldset style="width:390px">
	<legend>Item Details</legend>
	<input type="hidden" name="selected_name" id="selected_name" value="">
	<input type="hidden" name="selected_id" id="selected_id" value="">
	<table width="390" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
		<thead>
			<tr>
				<th width="30">SL</th>
                <th width="">Store Name</th>
			</tr>
		</thead>
	</table>
	<div style="width:390px; overflow-y:scroll; max-height:300px" id="scroll_body">
		<table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
			<?
			$i = 1;
			$sql="select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id in(".$cbo_company_name.") and b.category_type in(1) group by a.id, a.store_name order by a.store_name";
			//echo $sql;
			$result=sql_select($sql);
			$selected_id_arr=explode(",",$cbo_store);
			foreach ($result as $row)
			{
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";

				if(in_array($row[csf("id")],$selected_id_arr))
				{
					if($selected_ids=="")
						$selected_ids=$i;
					else
						$selected_ids.=",".$i;
				}
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value(<? echo $i; ?>)" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
					<td width="30" align="center">
						<? echo $i; ?>
						<input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $i ?>" value="<? echo $row[csf("id")]; ?>"/>
						<input type="hidden" name="txt_individual" id="txt_individual<? echo $i ?>" value="<? echo $row[csf("store_name")]; ?>"/>
					</td>
                    <td width=""><p><? echo $row[csf("store_name")]; ?></p></td>
				</tr>
				<?
				$i++;
			}
			?>
			<input type="hidden" name="selected_ids" id="selected_ids" value="<? echo $selected_ids; ?>"/>
		</table>
	</div>
	<table width="390" cellspacing="0" cellpadding="0" style="border:none" align="center">
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
</fieldset>
<script type="text/javascript">
	setFilterGrid('table_body',-1);
	set_all();
</script>
<?
exit();
}

//composition_popup
if($action == "composition_popup")
{
	echo load_html_head_contents("Composition Info","../../../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
	<script>
	var selected_id = new Array();
	var selected_name = new Array();

	function check_all_data()
	{
		var tbl_row_count = document.getElementById( 'table_body' ).rows.length;
		tbl_row_count = tbl_row_count-1;
		for( var i = 1; i <= tbl_row_count; i++ )
		{
			js_set_value( i );
		}
	}

	function toggle( x, origColor ) 
	{
		var newColor = 'yellow';
		if ( x.style )
		{
			x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}

	function set_all()
	{
		var old=document.getElementById('txt_pre_composition_row_id').value;
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
		if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 )
		{
			selected_id.push( $('#txt_individual_id' + str).val() );
			selected_name.push( $('#txt_individual' + str).val() );

		}
		else
		{
			for( var i = 0; i < selected_id.length; i++ )
			{
				if( selected_id[i] == $('#txt_individual_id' + str).val() )
					break;
			}
			selected_id.splice( i, 1 );
			selected_name.splice( i, 1 );
		}

		var id = ''; var name = '';
		for( var i = 0; i < selected_id.length; i++ )
		{
			id += selected_id[i] + ',';
			name += selected_name[i] + ',';
		}

		id = id.substr( 0, id.length - 1 );
		name = name.substr( 0, name.length - 1 );

		$('#hidden_composition_id').val(id);
		$('#hidden_composition').val(name);
	}
	</script>
	</head>
	<fieldset style="width:390px">
		<legend>Yarn Receive Details</legend>
		<input type="hidden" name="hidden_composition" id="hidden_composition" value="">
		<input type="hidden" name="hidden_composition_id" id="hidden_composition_id" value="">
		<table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
			<thead>
				<tr>
					<th colspan="2">
						<? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --" ); ?>
					</th>
				</tr>
				<tr>
					<th width="50">SL</th>
					<th width="">Composition Name</th>
				</tr>
			</thead>
		</table>
		<div style="width:390px; overflow-y:scroll; max-height:300px" id="scroll_body">
			<table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
			<?
			$i = 1;
			$result=sql_select("select id,composition_name from  lib_composition_array where status_active=1 and is_deleted=0 order by composition_name");
			$pre_composition_id_arr=explode(",",$pre_composition_id);
			foreach ($result as $row)
			{
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";
	
	
				if(in_array($row[csf("id")],$pre_composition_id_arr))
				{
					if($pre_composition_ids=="") $pre_composition_ids=$i; else $pre_composition_ids.=",".$i;
				}
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value(<? echo $i; ?>)" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
					<td width="50">
						<? echo $i; ?>
						<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $row[csf("id")]; ?>"/>
						<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $row[csf("composition_name")]; ?>"/>
					</td>
					<td width=""><p><? echo $row[csf("composition_name")]; ?></p></td>
				</tr>
				<?
				$i++;
			}
			?>
			<input type="hidden" name="txt_pre_composition_row_id" id="txt_pre_composition_row_id" value="<?php echo $pre_composition_ids; ?>"/>
		</table>
		</div>
		<table width="390" cellspacing="0" cellpadding="0" style="border:none" align="center">
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
	</fieldset>
	<script type="text/javascript">
		setFilterGrid('table_body',-1);
		set_all();
	</script>
	<?
	exit();
}

//for generate_report
if ($action == "generate_report")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	//for from_company
	$from_company_cond = '';
	if($cbo_from_company != '')
	{
		$from_company_cond = " AND A.COMPANY_ID IN(".$cbo_from_company.")";
	}
	
	//for to_company
	$to_company_cond = '';
	if($cbo_to_company != '')
	{
		$to_company_cond = " AND A.TO_COMPANY IN(".$cbo_to_company.")";
	}
	
	//for supplier
	$supplier_cond = '';
	if($cbo_supplier != '')
	{
		$supplier_cond = " AND C.SUPPLIER_ID IN(".$cbo_supplier.")";
	}
	
	//for dyed_type
	$dyed_type_cond = '';
	if($cbo_dyed_type != '')
	{
		$dyed_type_cond = " AND C.DYED_TYPE IN(".$cbo_dyed_type.")";
	}
	
	//for yarn_type
	$yarn_type_cond = '';
	if($cbo_yarn_type != '')
	{
		$yarn_type_cond = " AND C.YARN_TYPE IN(".$cbo_yarn_type.")";
	}
	
	//for count
	$count_cond = '';
	if($txt_count != '')
	{
		$count_cond = " AND C.YARN_COUNT_ID IN(".$txt_count.")";
	}
	
	//for count
	$composition_cond = '';
	if($txt_composition_id != '')
	{
		$composition_cond = " AND C.YARN_COMP_TYPE1ST IN(".$txt_composition_id.")";
	}

	//for lot
	$lot_no_cond = '';
	if($txt_lot_no != '')
	{
		$lot_no_cond = " AND B.YARN_LOT = '".$txt_lot_no."'";
	}
	
	//for from_store
	$from_store_cond = '';
	if($cbo_from_store != '')
	{
		$from_store_cond = " AND B.FROM_STORE IN(".$cbo_from_store.")";
	}

	//for to_store
	$to_store_cond = '';
	if($cbo_to_store != '')
	{
		$to_store_cond = " AND B.TO_STORE IN(".$cbo_to_store.")";
	}
	
	//for transfer_criteria
	$transfer_criteria_cond = '';
	if($cbo_transfer_criteria != '')
	{
		$transfer_criteria_cond = " AND A.TRANSFER_CRITERIA IN(".$cbo_transfer_criteria.")";
	}
	else
	{
		$transfer_criteria_cond = " AND A.TRANSFER_CRITERIA IN(1,2)";
	}
	
	//for date
	$date_cond = '';
	$from_date = change_date_format($from_date, '', '', 1);
	$to_date = change_date_format($to_date, '', '', 1);
	if($from_date != '' && $to_date != '')
	{
		$date_cond = " AND A.TRANSFER_DATE BETWEEN '".$from_date."' AND '".$to_date."'";
	}
	
	$companyArr = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$locationArr = return_library_array("select id,location_name from lib_location", "id", "location_name");
	$supplierArr = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$storeArr = return_library_array("select id, store_name from lib_store_location", "id", "store_name");
	$yarncountArr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$colorArr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	
	$sql = "SELECT A.TRANSFER_SYSTEM_ID, A.COMPANY_ID, A.TO_COMPANY, A.TRANSFER_DATE, A.TRANSFER_CRITERIA, A.REMARKS, A.LOCATION_ID, A.TO_LOCATION_ID, B.ID AS DTLS_ID, B.TRANSFER_QNTY, B.RATE, B.TRANSFER_VALUE, B.FROM_STORE, B.TO_STORE, C.ID, C.YARN_COMP_TYPE1ST, C.YARN_COMP_PERCENT1ST, C.YARN_COMP_TYPE2ND, C.YARN_COMP_PERCENT2ND, C.COLOR, C.YARN_TYPE, C.SUPPLIER_ID, C.YARN_COUNT_ID, C.LOT, C.IS_WITHIN_GROUP FROM INV_ITEM_TRANSFER_MST A, INV_ITEM_TRANSFER_DTLS B, PRODUCT_DETAILS_MASTER C WHERE A.ID = B.MST_ID AND B.FROM_PROD_ID = C.ID AND A.ITEM_CATEGORY = 1 AND B.ITEM_CATEGORY = 1 AND C.ITEM_CATEGORY_ID = 1 AND A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0 AND C.STATUS_ACTIVE = 1 AND C.IS_DELETED = 0 ".$from_company_cond.$to_company_cond.$supplier_cond.$dyed_type_cond.$yarn_type_cond.$count_cond.$composition_cond.$lot_no_cond.$from_store_cond.$to_store_cond.$transfer_criteria_cond.$date_cond;
	//echo $sql;
	$sql_rslt = sql_select($sql);
	$data_arr = array();
	foreach($sql_rslt as $row)
	{
		$compos = '';
		if ($row['YARN_COMP_PERCENT2ND'] != 0)
		{
			$compos = $composition[$row['YARN_COMP_TYPE1ST']] . " " . $row['YARN_COMP_PERCENT1ST'] . "%" . " " . $composition[$row['YARN_COMP_TYPE2ND']] . " " . $row['YARN_COMP_PERCENT2ND'] . "%";
		}
		else
		{
			$compos = $composition[$row['YARN_COMP_TYPE1ST']] . " " . $row['YARN_COMP_PERCENT1ST'] . "%" . " " . $composition[$row['YARN_COMP_TYPE2ND']];
		}

		$data_arr[$row['TRANSFER_SYSTEM_ID']][$row['ID']]['TRANSFER_CRITERIA'] = $item_transfer_criteria[$row['TRANSFER_CRITERIA']];
		$data_arr[$row['TRANSFER_SYSTEM_ID']][$row['ID']]['TRANSFER_DATE'] = date('d-m-Y', strtotime($row['TRANSFER_DATE']));
		$data_arr[$row['TRANSFER_SYSTEM_ID']][$row['ID']]['COMPANY_ID'] = $companyArr[$row['COMPANY_ID']];
		$data_arr[$row['TRANSFER_SYSTEM_ID']][$row['ID']]['TO_COMPANY'] = $companyArr[$row['TO_COMPANY']];
		$data_arr[$row['TRANSFER_SYSTEM_ID']][$row['ID']]['LOCATION_ID'] = $locationArr[$row['LOCATION_ID']];
		$data_arr[$row['TRANSFER_SYSTEM_ID']][$row['ID']]['TO_LOCATION_ID'] = $locationArr[$row['TO_LOCATION_ID']];
		$data_arr[$row['TRANSFER_SYSTEM_ID']][$row['ID']]['FROM_STORE'] = $storeArr[$row['FROM_STORE']];
		$data_arr[$row['TRANSFER_SYSTEM_ID']][$row['ID']]['TO_STORE'] = $storeArr[$row['TO_STORE']];
		$data_arr[$row['TRANSFER_SYSTEM_ID']][$row['ID']]['YARN_COMPOSITION'] = $compos;
		$data_arr[$row['TRANSFER_SYSTEM_ID']][$row['ID']]['COLOR'] = $colorArr[$row['COLOR']];
		$data_arr[$row['TRANSFER_SYSTEM_ID']][$row['ID']]['YARN_TYPE'] = $yarn_type[$row['YARN_TYPE']];
		$data_arr[$row['TRANSFER_SYSTEM_ID']][$row['ID']]['YARN_COUNT_ID'] = $yarncountArr[$row['YARN_COUNT_ID']];
		$data_arr[$row['TRANSFER_SYSTEM_ID']][$row['ID']]['LOT'] = $row['LOT'];
		$data_arr[$row['TRANSFER_SYSTEM_ID']][$row['ID']]['TRANSFER_QNTY'] = $row['TRANSFER_QNTY'];
		$data_arr[$row['TRANSFER_SYSTEM_ID']][$row['ID']]['RATE'] = $row['RATE'];
		$data_arr[$row['TRANSFER_SYSTEM_ID']][$row['ID']]['TRANSFER_VALUE'] = $row['TRANSFER_VALUE'];
		$data_arr[$row['TRANSFER_SYSTEM_ID']][$row['ID']]['REMARKS'] = $row['REMARKS'];
		
		//for supplier
		if($row['TRANSFER_SYSTEM_ID'] == 1)
		{
			$data_arr[$row['TRANSFER_SYSTEM_ID']][$row['ID']]['SUPPLIER_ID'] = $companyArr[$row['SUPPLIER_ID']];
		}
		else
		{
			$data_arr[$row['TRANSFER_SYSTEM_ID']][$row['ID']]['SUPPLIER_ID'] = $supplierArr[$row['SUPPLIER_ID']];
		}
	}
	
	ob_start();
	?>
    <div>
    <table width="2100" border="1" cellpadding="2" cellspacing="0" class="rpt_table" style="font:'Arial Narrow'; font-size:14px" rules="all">
        <thead>
            <tr class="form_caption" style="border:none;">
                <td colspan="20" align="center" style="border:none;font-size:16px; font-weight:bold">Yarn Transfer Report</td>
            </tr>
            <tr>
                <th width="40">SL</th>
                <th width="120">Transfer Criteria</th>
                <th width="120">Transection Ref</th>
                <th width="70">Transection Date</th>
                <th width="120">From Company</th>
                <th width="120">To Company</th>
                <th width="120">From Location</th>
                <th width="120">To Location</th>
                <th width="120">From Store</th>
                <th width="120">To Store</th>
                <th width="120">Yarn Composition</th>
                <th width="100">Yarn Color</th>
                <th width="100">Yarn Type</th>
                <th width="100">Yarn Supplier</th>
                <th width="60">Yarn Count</th>
                <th width="100">Lot/Batch</th>
                <th width="100">Transfer Qty</th>
                <th width="100">Avg Rate(TK)</th>
                <th width="100">Value(TK)</th>
                <th>Remarks</th>
            </tr>
        </thead>
    </table>
    <div style="width:2120px; overflow-y:scroll; max-height:450px" id="scroll_body" >    
        <table width="2100" border="1" cellpadding="2" cellspacing="0" class="rpt_table" style="font:'Arial Narrow'; font-size:14px" rules="all" id="table_body">
            <tbody>
            <?
            $sl = 0;
            $TRANSFER_QNTY = 0;
            $TRANSFER_VALUE = 0;
            foreach($data_arr as $transfer_id=>$transfer_id_arr)
            {
                foreach($transfer_id_arr as $prod_id=>$row)
                {
                    $sl++;
					if ($sl % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $sl; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
                        <td width="40" align="center"><? echo $sl; ?></td>
                        <td width="120" style="word-break:break-all"><? echo $row['TRANSFER_CRITERIA']; ?></td>
                        <td width="120" style="word-break:break-all"><? echo $transfer_id; ?></td>
                        <td width="70" align="center" style="word-break:break-all"><? echo $row['TRANSFER_DATE']; ?></td>
                        <td width="120" style="word-break:break-all"><? echo $row['COMPANY_ID']; ?></td>
                        <td width="120" style="word-break:break-all"><? echo $row['TO_COMPANY']; ?></td>
                        <td width="120" style="word-break:break-all"><? echo $row['LOCATION_ID']; ?></td>
                        <td width="120" style="word-break:break-all"><? echo $row['TO_LOCATION_ID']; ?></td>
                        <td width="120" style="word-break:break-all"><? echo $row['FROM_STORE']; ?></td>
                        <td width="120" style="word-break:break-all"><? echo $row['TO_STORE']; ?></td>
                        <td width="120" style="word-break:break-all"><? echo $row['YARN_COMPOSITION']; ?></td>
                        <td width="100" style="word-break:break-all"><? echo $row['COLOR']; ?></td>
                        <td width="100" style="word-break:break-all"><? echo $row['YARN_TYPE']; ?></td>
                        <td width="100" style="word-break:break-all"><? echo $row['SUPPLIER_ID']; ?></td>
                        <td width="60" style="word-break:break-all"><? echo $row['YARN_COUNT_ID']; ?></td>
                        <td width="100" style="word-break:break-all"><? echo $row['LOT']; ?></td>
                        <td width="100" style="word-break:break-all" align="right"><? echo number_format($row['TRANSFER_QNTY'],2,'.',','); ?></td>
                        <td width="100" style="word-break:break-all" align="right"><? echo number_format($row['RATE'],4); ?></td>
                        <td width="100" style="word-break:break-all" align="right"><? echo number_format($row['TRANSFER_VALUE'],2,'.',','); ?></td>
                        <td style="word-break:break-all"><? echo $row['REMARKS']; ?></td>
                    </tr>
                    <?
                    $TRANSFER_QNTY += $row['TRANSFER_QNTY'];
                    $TRANSFER_VALUE += $row['TRANSFER_VALUE'];
                }
            }
            ?>
            </tbody>
        </table>
    </div>
    <table width="2100" border="1" cellpadding="2" cellspacing="0" class="rpt_table" style="font:'Arial Narrow'; font-size:14px" rules="all">
        <tfoot>
        	<tr>
                <th width="40"></th>
                <th width="120"></th>
                <th width="120"></th>
                <th width="70"></th>
                <th width="120"></th>
                <th width="120"></th>
                <th width="120"></th>
                <th width="120"></th>
                <th width="120"></th>
                <th width="120"></th>
                <th width="120"></th>
                <th width="100"></th>
                <th width="100"></th>
                <th width="100"></th>
                <th width="60"></th>
                <th width="100"></th>
            	<th width="100" align="right" id="value_total_transfer_qty"><? echo number_format($TRANSFER_QNTY,2); ?></th>
            	<th width="100"></th>
            	<th width="100" align="right" id="value_total_transfer_amount"><? echo number_format($TRANSFER_VALUE,2); ?></th>
                <th>&nbsp;</th>
            </tr>
        </tfoot>
    </table>
    </div>
	<?
	$html = ob_get_contents();
	ob_clean();
	foreach (glob("*.xls") as $filename) 
	{		
		@unlink($filename);
	}
	//---------end------------//
	$name = time();
	$filename = $user_id . "_" . $name . ".xls";
	$create_new_doc = fopen($filename, 'w+');
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename";
	exit();
}
?>