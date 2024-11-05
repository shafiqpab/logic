<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');
$user_id = $_SESSION['logic_erp']['user_id'];
if ($_SESSION['logic_erp']['user_id'] == "")
{
	header("location:login.php");
	die;
}

$permission = $_SESSION['page_permission'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

/*
|--------------------------------------------------------------------------
| for company_popup
|--------------------------------------------------------------------------
|
*/
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
<!--<fieldset style="width:320px">
	<legend>Item Details</legend>-->
	<input type="hidden" name="selected_name" id="selected_name" value="">
	<input type="hidden" name="selected_id" id="selected_id" value="">
	<table width="320" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
		<thead>
			<tr>
				<th width="40">SL</th>
                <th width="">Company Name</th>
			</tr>
		</thead>
	</table>
	<div style="width:320px; overflow-y:scroll; max-height:300px" id="scroll_body">
		<table width="300" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
			<?
			$i = 1;
			$cbo_company_name=str_replace("'","",$cbo_company_name);

			$sql="SELECT ID, COMPANY_NAME FROM LIB_COMPANY WHERE STATUS_ACTIVE=1 AND IS_DELETED=0 ORDER BY COMPANY_NAME";
			//echo $sql;
			$result=sql_select($sql);
			$selected_id_arr=explode(",",$cbo_company);
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
					<td width="40" align="center">
						<? echo $i; ?>
						<input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $i ?>" value="<? echo $row['ID']; ?>"/>
						<input type="hidden" name="txt_individual" id="txt_individual<? echo $i ?>" value="<? echo $row['COMPANY_NAME']; ?>"/>



					</td>
                    <td width=""><p><? echo $row['COMPANY_NAME']; ?></p></td>
				</tr>
				<?
				$i++;
			}
			?>
			<input type="hidden" name="selected_ids" id="selected_ids" value="<? echo $selected_ids; ?>"/>
		</table>
	</div>
	<table width="320" cellspacing="0" cellpadding="0" style="border:none" align="center">
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
<!--</fieldset>-->
<script type="text/javascript">
	setFilterGrid('table_body',-1);
	set_all();
</script>
<?
exit();
}

/*
|--------------------------------------------------------------------------
| for supplier_popup
|--------------------------------------------------------------------------
|
*/
if($action == "supplier_popup")
{
	echo load_html_head_contents("Supplier Info","../../../../", 1, 1, '','','');
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
<!--<fieldset style="width:320px">
	<legend>Item Details</legend>-->
	<input type="hidden" name="selected_name" id="selected_name" value="">
	<input type="hidden" name="selected_id" id="selected_id" value="">
	<table width="320" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
		<thead>
			<tr>
				<th width="40">SL</th>
                <th>Supplier Name</th>
			</tr>
		</thead>
	</table>
	<div style="width:320px; overflow-y:scroll; max-height:300px" id="scroll_body">
		<table width="300" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
			<?
			$i = 1;
			$sql = "SELECT C.ID, C.SUPPLIER_NAME FROM LIB_SUPPLIER_TAG_COMPANY A, LIB_SUPPLIER_PARTY_TYPE B, LIB_SUPPLIER C WHERE C.ID=B.SUPPLIER_ID AND A.SUPPLIER_ID = B.SUPPLIER_ID AND A.TAG_COMPANY IN('".$company_id."') AND B.PARTY_TYPE =2 AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 GROUP BY C.ID, C.SUPPLIER_NAME ORDER BY SUPPLIER_NAME";
			//echo $sql;
			$sql_rslt = sql_select($sql);
			$selected_id_arr=explode(",",$cbo_supplier);
			foreach ($sql_rslt as $row)
			{
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";

				if(in_array($row['ID'], $selected_id_arr))
				{
					if($selected_ids=="")
						$selected_ids=$i;
					else
						$selected_ids.=",".$i;
				}
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value(<? echo $i; ?>)" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
					<td width="40" align="center">
						<? echo $i; ?>
						<input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $i ?>" value="<? echo $row['ID']; ?>"/>
						<input type="hidden" name="txt_individual" id="txt_individual<? echo $i ?>" value="<? echo $row['SUPPLIER_NAME']; ?>"/>
					</td>
                    <td><p><? echo $row['SUPPLIER_NAME']; ?></p></td>
				</tr>
				<?
				$i++;
			}
			?>
			<input type="hidden" name="selected_ids" id="selected_ids" value="<? echo $selected_ids; ?>"/>
		</table>
	</div>
	<table width="320" cellspacing="0" cellpadding="0" style="border:none" align="center">
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
<!--</fieldset>-->
<script type="text/javascript">
	setFilterGrid('table_body',-1);
	set_all();
</script>
<?
exit();
}

/*
|--------------------------------------------------------------------------
| for yarn_type_popup
|--------------------------------------------------------------------------
|
*/
if($action == "yarn_type_popup")
{
	echo load_html_head_contents("Yarn Type","../../../../", 1, 1, '','','');
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
<!--<fieldset style="width:320px">
	<legend>Item Details</legend>-->
	<input type="hidden" name="selected_name" id="selected_name" value="">
	<input type="hidden" name="selected_id" id="selected_id" value="">
	<table width="320" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
		<thead>
			<tr>
				<th width="40">SL</th>
                <th>Yarn Type</th>
			</tr>
		</thead>
	</table>
	<div style="width:320px; overflow-y:scroll; max-height:300px" id="scroll_body">
		<table width="300" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
			<?
			$i = 1;
			$selected_id_arr=explode(",",$cbo_yarn_type);
			foreach ($yarn_type as $key=>$val)
			{
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";

				if(in_array($key, $selected_id_arr))
				{
					if($selected_ids=="")
						$selected_ids=$i;
					else
						$selected_ids.=",".$i;
				}
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value(<? echo $i; ?>)" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
					<td width="40" align="center">
						<? echo $i; ?>
						<input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $i ?>" value="<? echo $key; ?>"/>
						<input type="hidden" name="txt_individual" id="txt_individual<? echo $i ?>" value="<? echo $val; ?>"/>
					</td>
                    <td><p><? echo $val; ?></p></td>
				</tr>
				<?
				$i++;
			}
			?>
			<input type="hidden" name="selected_ids" id="selected_ids" value="<? echo $selected_ids; ?>"/>
		</table>
	</div>
	<table width="320" cellspacing="0" cellpadding="0" style="border:none" align="center">
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
<!--</fieldset>-->
<script type="text/javascript">
	setFilterGrid('table_body',-1);
	set_all();
</script>
<?
exit();
}

/*
|--------------------------------------------------------------------------
| for yarn_count_popup
|--------------------------------------------------------------------------
|
*/
if($action == "yarn_count_popup")
{
	echo load_html_head_contents("Yarn Count","../../../../", 1, 1, '','','');
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
<!--<fieldset style="width:320px">
	<legend>Item Details</legend>-->
	<input type="hidden" name="selected_name" id="selected_name" value="">
	<input type="hidden" name="selected_id" id="selected_id" value="">
	<table width="320" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
		<thead>
			<tr>
				<th width="40">SL</th>
                <th>Supplier Name</th>
			</tr>
		</thead>
	</table>
	<div style="width:320px; overflow-y:scroll; max-height:300px" id="scroll_body">
		<table width="300" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
			<?
			$i = 1;
			$sql = "SELECT ID, YARN_COUNT FROM LIB_YARN_COUNT WHERE IS_DELETED=0 AND STATUS_ACTIVE=1 ORDER BY YARN_COUNT";
			//echo $sql;
			$sql_rslt = sql_select($sql);
			$selected_id_arr=explode(",",$cbo_yarn_count);
			foreach ($sql_rslt as $row)
			{
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";

				if(in_array($row['ID'], $selected_id_arr))
				{
					if($selected_ids=="")
						$selected_ids=$i;
					else
						$selected_ids.=",".$i;
				}
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value(<? echo $i; ?>)" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
					<td width="40" align="center">
						<? echo $i; ?>
						<input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $i ?>" value="<? echo $row['ID']; ?>"/>
						<input type="hidden" name="txt_individual" id="txt_individual<? echo $i ?>" value="<? echo $row['YARN_COUNT']; ?>"/>
					</td>
                    <td><p><? echo $row['YARN_COUNT']; ?></p></td>
				</tr>
				<?
				$i++;
			}
			?>
			<input type="hidden" name="selected_ids" id="selected_ids" value="<? echo $selected_ids; ?>"/>
		</table>
	</div>
	<table width="320" cellspacing="0" cellpadding="0" style="border:none" align="center">
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
<!--</fieldset>-->
<script type="text/javascript">
	setFilterGrid('table_body',-1);
	set_all();
</script>
<?
exit();
}

/*
|--------------------------------------------------------------------------
| for composition_popup
|--------------------------------------------------------------------------
|
*/
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
			$result=sql_select("SELECT ID, COMPOSITION_NAME FROM LIB_COMPOSITION_ARRAY WHERE STATUS_ACTIVE=1 AND IS_DELETED=0 ORDER BY COMPOSITION_NAME");
			$pre_composition_id_arr=explode(",",$pre_composition_id);
			foreach ($result as $row)
			{
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";

				if(in_array($row['ID'],$pre_composition_id_arr))
				{
					if($pre_composition_ids=="") $pre_composition_ids=$i;
					else $pre_composition_ids.=",".$i;
				}
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value(<? echo $i; ?>)" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
					<td width="50">
						<? echo $i; ?>
						<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $row['ID']; ?>"/>
						<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $row['COMPOSITION_NAME']; ?>"/>
					</td>
					<td width=""><p><? echo $row['COMPOSITION_NAME']; ?></p></td>
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

/*
|--------------------------------------------------------------------------
| for store_popup
|--------------------------------------------------------------------------
|
*/
if ($action=="store_popup")
{
	echo load_html_head_contents("Store","../../../../", 1, 1, '','','');
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
<!--<fieldset style="width:320px">
	<legend>Item Details</legend>-->
	<input type="hidden" name="selected_name" id="selected_name" value="">
	<input type="hidden" name="selected_id" id="selected_id" value="">
	<table width="320" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
		<thead>
			<tr>
				<th width="40">SL</th>
                <th width="70">Company</th>
                <th>Store</th>
			</tr>
		</thead>
	</table>
	<div style="width:320px; overflow-y:scroll; max-height:300px" id="scroll_body">
		<table width="300" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
			<?
			$companyArr = return_library_array( "SELECT id, company_short_name FROM lib_company WHERE id IN (".$company_id.")", "id", "company_short_name" );
			$company_cond = '';
			if($company_id != '')
			{
				$company_cond = "  AND A.COMPANY_ID IN(".$company_id.")";
			}

			$sql = "SELECT A.ID, A.STORE_NAME, COMPANY_ID FROM LIB_STORE_LOCATION A, LIB_STORE_LOCATION_CATEGORY B WHERE A.ID = B.STORE_LOCATION_ID AND  A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND B.CATEGORY_TYPE IN(1)".$company_cond." ORDER BY A.STORE_NAME";
			//echo $sql;
			$sql_rslt = sql_select($sql);
			$selected_id_arr=explode(",",$cbo_store);
			$i = 1;
			foreach ($sql_rslt as $row)
			{
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";

				if(in_array($row['ID'], $selected_id_arr))
				{
					if($selected_ids=="")
						$selected_ids=$i;
					else
						$selected_ids.=",".$i;
				}
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value(<? echo $i; ?>)" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
					<td width="40" align="center">
						<? echo $i; ?>
						<input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $i ?>" value="<? echo $row['ID']; ?>"/>
						<input type="hidden" name="txt_individual" id="txt_individual<? echo $i ?>" value="<? echo $row['STORE_NAME']; ?>"/>
					</td>
                    <td width="70"><p><? echo $companyArr[$row['COMPANY_ID']]; ?></p></td>
                    <td><p><? echo $row['STORE_NAME']; ?></p></td>
				</tr>
				<?
				$i++;
			}
			?>
			<input type="hidden" name="selected_ids" id="selected_ids" value="<? echo $selected_ids; ?>"/>
		</table>
	</div>
	<table width="320" cellspacing="0" cellpadding="0" style="border:none" align="center">
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
<!--</fieldset>-->
<script type="text/javascript">
	setFilterGrid('table_body',-1);
	set_all();
</script>
<?
exit();
}

/*

|--------------------------------------------------------------------------
| for floor_popup
|--------------------------------------------------------------------------
|
*/
if ($action=="floor_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
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
<!--<fieldset style="width:320px">
	<legend>Item Details</legend>-->
	<input type="hidden" name="selected_name" id="selected_name" value="">
	<input type="hidden" name="selected_id" id="selected_id" value="">
	<table width="470" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
		<thead>
			<tr>
				<th width="30">SL</th>
                <th width="60">Company</th>
                <th width="120">Location</th>
                <th width="120">Store</th>
                <th>Floor</th>
			</tr>
		</thead>
	</table>
	<div style="width:470px; overflow-y:scroll; max-height:300px" id="scroll_body">
		<table width="450" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
			<?
			$store_cond = '';
			if($cbo_store != '')
			{
				$store_cond = " AND B.STORE_ID IN(".$cbo_store.")";
			}

			$companyArr = return_library_array( "SELECT id, company_short_name FROM lib_company WHERE id IN (".$company_id.")", "id", "company_short_name" );
			$locationArr = return_library_array( "SELECT id, location_name FROM lib_location WHERE status_active = 1 AND is_deleted = 0 AND company_id IN (SELECT id FROM lib_company WHERE id IN (".$company_id.")) ORDER BY location_name","id","location_name" );
			$storeArr = return_library_array("SELECT id, store_name FROM lib_store_location WHERE status_active = 1 AND is_deleted=0 ORDER BY store_name","id","store_name");
			$arr=array(0=>$companyArr,1=>$locationArr,2=>$storeArr);
			$i = 1;
			$sql = "SELECT A.FLOOR_ROOM_RACK_ID, A.FLOOR_ROOM_RACK_NAME, A.COMPANY_ID, B.LOCATION_ID, B.STORE_ID FROM LIB_FLOOR_ROOM_RACK_MST A INNER JOIN LIB_FLOOR_ROOM_RACK_DTLS B ON A.FLOOR_ROOM_RACK_ID = B.FLOOR_ID WHERE A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0 AND A.COMPANY_ID IN(".$company_id.")".$store_cond." GROUP BY A.FLOOR_ROOM_RACK_ID, A.FLOOR_ROOM_RACK_NAME, A.COMPANY_ID, B.LOCATION_ID, B.STORE_ID ORDER BY A.FLOOR_ROOM_RACK_NAME";
			//echo $sql;
			$sql_rslt = sql_select($sql);
			$selected_id_arr=explode(",",$cbo_floor);
			foreach ($sql_rslt as $row)
			{
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";

				if(in_array($row['FLOOR_ROOM_RACK_ID'], $selected_id_arr))
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
						<input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $i ?>" value="<? echo $row['FLOOR_ROOM_RACK_ID']; ?>"/>
						<input type="hidden" name="txt_individual" id="txt_individual<? echo $i ?>" value="<? echo $row['FLOOR_ROOM_RACK_NAME']; ?>"/>
					</td>
                    <td width="60" align="center"><p><? echo $companyArr[$row['COMPANY_ID']]; ?></p></td>
                    <td width="120"><p><? echo $locationArr[$row['LOCATION_ID']]; ?></p></td>
                    <td width="120"><p><? echo $storeArr[$row['STORE_ID']]; ?></p></td>
                    <td><p><? echo $row['FLOOR_ROOM_RACK_NAME']; ?></p></td>
				</tr>
				<?
				$i++;
			}
			?>
			<input type="hidden" name="selected_ids" id="selected_ids" value="<? echo $selected_ids; ?>"/>
		</table>
	</div>
	<table width="470" cellspacing="0" cellpadding="0" style="border:none" align="center">
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
<!--</fieldset>-->
<script type="text/javascript">
	setFilterGrid('table_body',-1);
	set_all();
</script>
<?
exit();
}

/*
|--------------------------------------------------------------------------
| for room_popup
|--------------------------------------------------------------------------
|
*/
if ($action=="room_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
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
<!--<fieldset style="width:320px">
	<legend>Item Details</legend>-->
	<input type="hidden" name="selected_name" id="selected_name" value="">
	<input type="hidden" name="selected_id" id="selected_id" value="">
	<table width="590" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
		<thead>
			<tr>
				<th width="30">SL</th>
                <th width="60">Company</th>

                <th width="120">Location</th>
                <th width="120">Store</th>
                <th width="120">Floor</th>
                <th>Room</th>
			</tr>
		</thead>
	</table>
	<div style="width:590px; overflow-y:scroll; max-height:300px" id="scroll_body">
		<table width="570" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
			<?
			//for store
			$store_cond = '';
			if($cbo_store != '')
			{
				$store_cond = " AND B.STORE_ID IN(".$cbo_store.")";
			}

			//for floor
			$floor_cond = '';
			if ($cbo_floor != '')
			{
				$floor_cond = " AND B.FLOOR_ID IN(".$cbo_floor.")";
			}

			$companyArr = return_library_array( "SELECT id, company_short_name FROM lib_company WHERE id IN (".$company_id.")", "id", "company_short_name" );
			$locationArr = return_library_array( "SELECT id, location_name FROM lib_location WHERE status_active = 1 AND is_deleted = 0 AND company_id IN (SELECT id FROM lib_company WHERE id IN (".$company_id.")) ORDER BY location_name","id","location_name");
			$storeArr = return_library_array("SELECT id, store_name FROM lib_store_location WHERE status_active = 1 AND is_deleted=0 ORDER BY store_name","id","store_name");
			$floorArr = return_library_array("SELECT a.floor_room_rack_name, a.floor_room_rack_id FROM lib_floor_room_rack_mst a INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.floor_id WHERE a.company_id in(".$company_id.") ".$floor_cond." AND a.is_deleted = 0 AND a.status_active = 1 GROUP BY a.floor_room_rack_name, a.floor_room_rack_id","floor_room_rack_id","floor_room_rack_name");

			$i = 1;
			$sql = "SELECT A.FLOOR_ROOM_RACK_ID, A.FLOOR_ROOM_RACK_NAME, A.COMPANY_ID, B.LOCATION_ID, B.STORE_ID, B.FLOOR_ID FROM LIB_FLOOR_ROOM_RACK_MST A INNER JOIN LIB_FLOOR_ROOM_RACK_DTLS B ON A.FLOOR_ROOM_RACK_ID = B.ROOM_ID WHERE A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0 AND A.COMPANY_ID IN(".$company_id.") ".$store_cond.$floor_cond." GROUP BY A.FLOOR_ROOM_RACK_ID, A.FLOOR_ROOM_RACK_NAME, A.COMPANY_ID, B.LOCATION_ID, B.STORE_ID, B.FLOOR_ID ORDER BY A.FLOOR_ROOM_RACK_NAME";
			//echo $sql;
			$sql_rslt = sql_select($sql);
			$selected_id_arr=explode(",",$cbo_room);
			foreach ($sql_rslt as $row)
			{
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";

				if(in_array($row['FLOOR_ROOM_RACK_ID'], $selected_id_arr))
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
						<input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $i ?>" value="<? echo $row['FLOOR_ROOM_RACK_ID']; ?>"/>
						<input type="hidden" name="txt_individual" id="txt_individual<? echo $i ?>" value="<? echo $row['FLOOR_ROOM_RACK_NAME']; ?>"/>
					</td>
                    <td width="60" align="center"><p><? echo $companyArr[$row['COMPANY_ID']]; ?></p></td>
                    <td width="120"><p><? echo $locationArr[$row['LOCATION_ID']]; ?></p></td>
                    <td width="120"><p><? echo $storeArr[$row['STORE_ID']]; ?></p></td>
                    <td width="120"><p><? echo $floorArr[$row['FLOOR_ID']]; ?></p></td>
                    <td><p><? echo $row['FLOOR_ROOM_RACK_NAME']; ?></p></td>
				</tr>
				<?
				$i++;
			}
			?>
			<input type="hidden" name="selected_ids" id="selected_ids" value="<? echo $selected_ids; ?>"/>
		</table>
	</div>
	<table width="590" cellspacing="0" cellpadding="0" style="border:none" align="center">
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
<!--</fieldset>-->
<script type="text/javascript">
	setFilterGrid('table_body',-1);
	set_all();
</script>
<?
exit();
}

/*
|--------------------------------------------------------------------------
| for generate_report
|--------------------------------------------------------------------------
|
*/
if ($action == "generate_report")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$type = str_replace("'","",$type);
	$value_with = str_replace("'","",$value_with);
	$comp_id = str_replace("'","",$cbo_company_name);
	//echo $value_with.test;die;

	//for library dtls
	$companyArr 	= return_library_array("select id, company_name from lib_company where status_active=1", "id", "company_name");
	$supplierArr 	= return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$color_name_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$storre_name_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
	$brand_name_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$buyer_dtls = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	//for company
	$company_cond = '';
	$comp_cond = '';
	if($cbo_company_name != '')
	{
		$comp_cond = " AND C.ID IN(".$cbo_company_name.")";
		$company_cond = " AND A.COMPANY_ID IN(".$cbo_company_name.")";
	}

	$search_cond .= ($txt_lot_no!='')?" AND A.LOT = '".$txt_lot_no."'":'';
	$search_cond .= ($cbo_supplier!='')?" AND A.SUPPLIER_ID IN(".$cbo_supplier.")":"";
	$search_cond .= ($cbo_dyed_type==0)?"":(($cbo_dyed_type==1)?" AND A.DYED_TYPE = ".$cbo_dyed_type:" AND A.DYED_TYPE <> 1");
	//$search_cond .= ($cbo_composition!='')?" AND (A.YARN_COMP_TYPE1ST IN(".$cbo_composition.") OR A.YARN_COMP_TYPE2ND IN(".$cbo_composition."))":"";
	$search_cond .= ($cbo_composition!='')?" AND A.YARN_COMP_TYPE1ST IN(".$cbo_composition.")":"";
	$search_cond .= ($cbo_yarn_count!='')?" AND A.YARN_COUNT_ID IN(".$cbo_yarn_count.")":"";
	$search_cond .= ($cbo_yarn_type!='')?" AND A.YARN_TYPE IN(.".$cbo_yarn_type.")":"";
	//$search_cond .= ($value_with!=0)?" AND A.CURRENT_STOCK > 0":"";

	//for store
	$store_cond = '';
	$store_cond1 = '';
	$cbo_store = str_replace("'","",$cbo_store);
	if($cbo_store != '')
	{
		//$store_cond = " AND B.STORE_ID IN(".$cbo_store.")";
		$store_cond1 = " AND A.ID IN(".$cbo_store.")";
		$search_cond .= " AND B.STORE_ID IN(".$cbo_store.")";
	}

	//for floor
	$floor_cond = '';
	$cbo_floor = str_replace("'","",$cbo_floor);
	if($cbo_floor != '')
	{
		$floor_cond = " AND B.FLOOR_ID IN(".$cbo_floor.")";
		$search_cond .= " AND B.FLOOR_ID IN(".$cbo_floor.")";
	}

	//for room
	$room_cond = '';
	$cbo_room = str_replace("'","",$cbo_room);
	if($cbo_room != '')
	{
		$room_cond = " AND B.ROOM_ID IN(".$cbo_room.")";
		$search_cond .= " AND B.ROOM IN(".$cbo_room.")";
	}

	//for date
	$from_date = change_date_format($from_date, '', '', 1);
	if ($from_date != "")
	{
		$date_cond = " AND A.TRANSACTION_DATE <= '".$from_date."'";
	}

	//echo $num_of_store;
	if($type==1)
	{
		//for company
		$sql_comp = "SELECT A.ID AS STORE_ID, C.ID, C.COMPANY_NAME FROM LIB_STORE_LOCATION A, LIB_STORE_LOCATION_CATEGORY B, LIB_COMPANY C WHERE A.ID=B.STORE_LOCATION_ID AND A.COMPANY_ID=C.ID AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.CATEGORY_TYPE IN(1)".$comp_cond.$store_cond1." ORDER BY C.ID ASC";
		//echo $sql_comp;
		$sql_comp_rslt = sql_select($sql_comp);
		$company_id_arr = array();
		$store_id_arr = array();
		foreach ($sql_comp_rslt as $row)
		{
			$company_id_arr[$row['ID']] = $row['ID'];
			$store_id_arr[$row['STORE_ID']] = $row['STORE_ID'];
		}
		unset($sql_comp_rslt);
		$cbo_company_name = implode(',',$company_id_arr);
		$cbo_store = implode(',',$store_id_arr);

		//for floor
		/*$sql_floor = "SELECT A.FLOOR_ROOM_RACK_ID, A.FLOOR_ROOM_RACK_NAME, A.COMPANY_ID, B.LOCATION_ID, B.STORE_ID FROM LIB_FLOOR_ROOM_RACK_MST A INNER JOIN LIB_FLOOR_ROOM_RACK_DTLS B ON A.FLOOR_ROOM_RACK_ID = B.FLOOR_ID WHERE A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0 AND A.COMPANY_ID IN(".$cbo_company_name.") AND B.STORE_ID IN(".$cbo_store.")".$floor_cond.$room_cond." GROUP BY A.FLOOR_ROOM_RACK_ID, A.FLOOR_ROOM_RACK_NAME, A.COMPANY_ID, B.LOCATION_ID, B.STORE_ID ORDER BY A.FLOOR_ROOM_RACK_NAME";*/
		$sql_floor = "SELECT A.FLOOR_ROOM_RACK_ID, A.FLOOR_ROOM_RACK_NAME, A.COMPANY_ID, B.LOCATION_ID, B.STORE_ID FROM LIB_FLOOR_ROOM_RACK_MST A INNER JOIN LIB_FLOOR_ROOM_RACK_DTLS B ON A.FLOOR_ROOM_RACK_ID = B.FLOOR_ID WHERE A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0 AND A.COMPANY_ID IN(".$cbo_company_name.") AND B.STORE_ID IN(".$cbo_store.")".$floor_cond.$room_cond;
		//echo $sql_floor;
		$sql_floor_rslt = sql_select($sql_floor);
		$floor_data = array();
		$floor_column_count=0;
		$floor_id_arr = array();
		$count_id_arr = array();
		$count_id = 1;
		foreach($sql_floor_rslt as $row)
		{
			$count_id_arr[$row['COMPANY_ID']]=$count_id++;
			$floor_data[$row['COMPANY_ID']][$row['FLOOR_ROOM_RACK_ID']] = $row['FLOOR_ROOM_RACK_NAME'];
			$floor_id_arr[$row['FLOOR_ROOM_RACK_ID']] = $row['FLOOR_ROOM_RACK_ID'];
			$floor_column_count++;
		}

		//print_r($count_id_arr);
		unset($sql_floor_rslt);
		$cbo_floor = implode(',',$floor_id_arr);

		//for room
		/*$sql_room = "SELECT A.FLOOR_ROOM_RACK_ID, A.FLOOR_ROOM_RACK_NAME, A.COMPANY_ID, B.LOCATION_ID, B.STORE_ID, B.FLOOR_ID FROM LIB_FLOOR_ROOM_RACK_MST A INNER JOIN LIB_FLOOR_ROOM_RACK_DTLS B ON A.FLOOR_ROOM_RACK_ID = B.ROOM_ID WHERE A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0 AND A.COMPANY_ID IN(".$cbo_company_name.") AND B.STORE_ID IN(".$cbo_store.") AND B.FLOOR_ID IN(".$cbo_floor.")".$room_cond." GROUP BY A.FLOOR_ROOM_RACK_ID, A.FLOOR_ROOM_RACK_NAME, A.COMPANY_ID, B.LOCATION_ID, B.STORE_ID, B.FLOOR_ID ORDER BY A.FLOOR_ROOM_RACK_NAME";*/
		$sql_room = "SELECT A.FLOOR_ROOM_RACK_ID, A.FLOOR_ROOM_RACK_NAME, A.COMPANY_ID, B.LOCATION_ID, B.STORE_ID, B.FLOOR_ID FROM LIB_FLOOR_ROOM_RACK_MST A INNER JOIN LIB_FLOOR_ROOM_RACK_DTLS B ON A.FLOOR_ROOM_RACK_ID = B.ROOM_ID WHERE A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0 AND A.COMPANY_ID IN(".$cbo_company_name.") AND B.STORE_ID IN(".$cbo_store.") AND B.FLOOR_ID IN(".$cbo_floor.")".$room_cond;
		//echo $sql_room;
		$sql_room_rslt = sql_select($sql_room);
		$room_data = array();
		$company_colspan = array();
		$floor_colspan = array();
		$total_room_count=0;
		$td_width = 0;
		foreach($sql_room_rslt as $row)
		{
			$room_data[$row['COMPANY_ID']][$row['FLOOR_ID']][$row['FLOOR_ROOM_RACK_ID']] = $row['FLOOR_ROOM_RACK_NAME'];
			$company_colspan[$row['COMPANY_ID']]++;
			$floor_colspan[$row['COMPANY_ID']][$row['FLOOR_ID']]++;
			$td_width = $td_width+100;
			$total_room_count++;
		}
		unset($sql_floor_rslt);
		// echo "<pre>";
		// print_r($company_colspan);
		// echo "</pre>";

		//for main query
		/*$sql_receive = "SELECT A.COMPANY_ID, A.SUPPLIER_ID, A.YARN_COUNT_ID, A.YARN_COMP_TYPE1ST, A.YARN_COMP_PERCENT1ST, A.YARN_COMP_TYPE2ND, A.YARN_COMP_PERCENT2ND, A.YARN_TYPE, A.COLOR, A.LOT, A.BRAND, B.STORE_ID, B.FLOOR_ID, B.ROOM,
		SUM(CASE WHEN B.TRANSACTION_TYPE IN (1,4,5) AND B.TRANSACTION_DATE <= '".$from_date."' THEN B.CONS_QUANTITY ELSE 0 END) AS RCV_TOTAL,
		SUM(CASE WHEN B.TRANSACTION_TYPE IN (1,4,5) AND B.TRANSACTION_DATE <= '".$from_date."' THEN B.CONS_AMOUNT ELSE 0 END) AS RCV_TOTAL_AMT,
		SUM(CASE WHEN B.TRANSACTION_TYPE IN (2,3,6) AND B.TRANSACTION_DATE <= '".$from_date."' THEN B.CONS_QUANTITY ELSE 0 END) AS ISSUE_TOTAL,
		SUM(CASE WHEN B.TRANSACTION_TYPE IN (2,3,6) AND B.TRANSACTION_DATE <= '".$from_date."' THEN B.CONS_AMOUNT ELSE 0 END) AS ISSUE_TOTAL_AMT
		FROM PRODUCT_DETAILS_MASTER A, INV_TRANSACTION B
		WHERE A.ID = B.PROD_ID AND B.TRANSACTION_TYPE IN (1,2,3,4,5,6) AND B.ITEM_CATEGORY = 1 AND A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0".$company_cond.$search_cond." AND B.CONS_QUANTITY > 0
		GROUP BY A.COMPANY_ID, A.SUPPLIER_ID, A.YARN_COUNT_ID, A.YARN_COMP_TYPE1ST, A.YARN_COMP_PERCENT1ST, A.YARN_COMP_TYPE2ND, A.YARN_COMP_PERCENT2ND, A.YARN_TYPE, A.COLOR, A.LOT, A.BRAND, B.STORE_ID, B.FLOOR_ID, B.ROOM ORDER BY A.YARN_COUNT_ID ASC";*/

		$sql_receive = "SELECT A.COMPANY_ID, A.SUPPLIER_ID, A.YARN_COUNT_ID, A.YARN_COMP_TYPE1ST, A.YARN_COMP_PERCENT1ST, A.YARN_COMP_TYPE2ND, A.YARN_COMP_PERCENT2ND, A.YARN_TYPE, A.COLOR, A.LOT, A.BRAND, B.TRANSACTION_TYPE, B.TRANSACTION_DATE, B.STORE_ID, B.FLOOR_ID, B.ROOM, B.CONS_QUANTITY, B.CONS_AMOUNT, C.PAY_MODE
		FROM PRODUCT_DETAILS_MASTER A, INV_TRANSACTION B left join WO_YARN_DYEING_MST C ON B.PI_WO_BATCH_NO=C.ID
		WHERE A.ID = B.PROD_ID AND B.TRANSACTION_TYPE IN (1,2,3,4,5,6) AND B.ITEM_CATEGORY = 1 AND A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0 and b.TRANSACTION_DATE <= '".$from_date."'".$company_cond.$search_cond." AND B.CONS_QUANTITY > 0";
		//echo $sql_receive; die;
		$result_sql_receive = sql_select($sql_receive);
		$data_arr = array();
		foreach ($result_sql_receive as $row)
		{
			$compositionDetails = $composition[$row['YARN_COMP_TYPE1ST']] . " " . $row['YARN_COMP_PERCENT1ST'];

			$data_arr[$row['YARN_COUNT_ID']][$compositionDetails][$row['YARN_TYPE']][$row['COLOR']][$row['LOT']][$row['SUPPLIER_ID']][$row['STORE_ID']][$row['COMPANY_ID']][$row['FLOOR_ID']][$row['ROOM']]['BRAND'] = $row['BRAND'];

			/*$data_arr[$row['YARN_COUNT_ID']][$compositionDetails][$row['YARN_TYPE']][$row['COLOR']][$row['LOT']][$row['SUPPLIER_ID']][$row['STORE_ID']][$row['COMPANY_ID']][$row['FLOOR_ID']][$row['ROOM']]['RCV_TOTAL'] += $row['RCV_TOTAL'];
			$data_arr[$row['YARN_COUNT_ID']][$compositionDetails][$row['YARN_TYPE']][$row['COLOR']][$row['LOT']][$row['SUPPLIER_ID']][$row['STORE_ID']][$row['COMPANY_ID']][$row['FLOOR_ID']][$row['ROOM']]['ISSUE_TOTAL'] += $row['ISSUE_TOTAL'];

			$data_arr[$row['YARN_COUNT_ID']][$compositionDetails][$row['YARN_TYPE']][$row['COLOR']][$row['LOT']][$row['SUPPLIER_ID']][$row['STORE_ID']][$row['COMPANY_ID']][$row['FLOOR_ID']][$row['ROOM']]['RCV_TOTAL_AMT'] += $row['RCV_TOTAL_AMT'];
			$data_arr[$row['YARN_COUNT_ID']][$compositionDetails][$row['YARN_TYPE']][$row['COLOR']][$row['LOT']][$row['SUPPLIER_ID']][$row['STORE_ID']][$row['COMPANY_ID']][$row['FLOOR_ID']][$row['ROOM']]['ISSUE_TOTAL_AMT'] += $row['ISSUE_TOTAL_AMT'];*/

			if (($row['TRANSACTION_TYPE']==1 || $row['TRANSACTION_TYPE']==4 || $row['TRANSACTION_TYPE']==5) && strtotime($row['TRANSACTION_DATE']) <= strtotime($from_date))
			{
				$data_arr[$row['YARN_COUNT_ID']][$compositionDetails][$row['YARN_TYPE']][$row['COLOR']][$row['LOT']][$row['SUPPLIER_ID']][$row['STORE_ID']][$row['COMPANY_ID']][$row['FLOOR_ID']][$row['ROOM']]['RCV_TOTAL'] += $row['CONS_QUANTITY'];
				$data_arr[$row['YARN_COUNT_ID']][$compositionDetails][$row['YARN_TYPE']][$row['COLOR']][$row['LOT']][$row['SUPPLIER_ID']][$row['STORE_ID']][$row['COMPANY_ID']][$row['FLOOR_ID']][$row['ROOM']]['RCV_TOTAL_AMT'] += $row['CONS_AMOUNT'];
			}

			if (($row['TRANSACTION_TYPE']==2 || $row['TRANSACTION_TYPE']==3 || $row['TRANSACTION_TYPE']==6) && strtotime($row['TRANSACTION_DATE']) <= strtotime($from_date))
			{
				$data_arr[$row['YARN_COUNT_ID']][$compositionDetails][$row['YARN_TYPE']][$row['COLOR']][$row['LOT']][$row['SUPPLIER_ID']][$row['STORE_ID']][$row['COMPANY_ID']][$row['FLOOR_ID']][$row['ROOM']]['ISSUE_TOTAL'] += $row['CONS_QUANTITY'];
				$data_arr[$row['YARN_COUNT_ID']][$compositionDetails][$row['YARN_TYPE']][$row['COLOR']][$row['LOT']][$row['SUPPLIER_ID']][$row['STORE_ID']][$row['COMPANY_ID']][$row['FLOOR_ID']][$row['ROOM']]['ISSUE_TOTAL_AMT'] += $row['CONS_AMOUNT'];
			}
		}
		unset($result_sql_receive);
		/*echo "<pre>";
		print_r($data_arr);
		echo "</pre>";*/

		// foreach ($company_id_arr as $company_id=>$company_row)
		// {
		// 	if(!empty($floor_data[$company_id]))
		// 	{
		// 		foreach ($floor_data[$company_id] as $flr=>$flr_arr)
		// 		{
		// 			if(!empty($room_data[$company_id][$flr]))
		// 			{
		// 				foreach ($room_data[$company_id][$flr] as $key=>$val)
		// 				{
		// 					$company_colspan[$company_id]++;
		// 				}
		// 			}
		// 			else
		// 			{
		// 				$company_colspan[$company_id]++;
		// 			}
		// 		}
		// 	}
		// 	else
		// 	{
		// 		$company_colspan[$company_id]++;
		// 	}
		// }

		$width = (1900+($td_width));

		ob_start();
		?>
		<fieldset style="width:<? echo $width+20; ?>px;margin:5px auto;" id="fieldsetId">
			<table width="<? echo $width+20; ?>" cellspacing="0" class="rpt_table" rules="all" id="table_header_1">
				<thead>
					<tr class="form_caption" style="border:none;">
						<td colspan="17" align="center" style="border:none;font-size:16px; font-weight:bold">Location Wise Yarn Stock</td>
					</tr>
					<!--<tr style="border:none;">
						<td colspan="16" align="center" style="border:none; font-size:14px;">
							<? echo ($cbo_company_name == '')?"All Company":$companyArr[str_replace("'", "", $cbo_company_name)]; ?>
						</td>
					</tr>-->
					<tr style="border:none;">
						<td colspan="17" align="center" style="border:none;font-size:12px; font-weight:bold">
							<? if ($txt_date_from != "" && $txt_date_from != "") echo "From " . change_date_format($txt_date_from, 'dd-mm-yyyy') . " To " . change_date_format($txt_date_from, 'dd-mm-yyyy') . ""; ?>
						</td>
					</tr>
					<tr>
						<th rowspan="3" width="40">SL</th>
						<th colspan="8">Description</th>
                        <!--for company print-->
						<?
						//$count_id_arr
						//$company_colspan[$company_id];
						$room_count = '';
						foreach ($company_id_arr as $company_id=>$company_row)
						{
							if(!empty($floor_data[$company_id]))
							{
								foreach ($floor_data[$company_id] as $flr=>$flr_arr)
								{

									if(!empty($room_data[$company_id][$flr]))
									{
										foreach ($room_data[$company_id][$flr] as $key=>$val)
										{
											$room_count++;
											$count_id_arr[$company_id]=$room_count;
										}
									}
									else
									{
										$room_count++;
										$count_id_arr[$company_id]=$room_count;
									}
								}
							}

						}

						foreach ($company_id_arr as $company_id=>$company_row)
						{
							?>
							<th colspan="<? echo $count_id_arr[$company_id]; ?>"><? echo $companyArr[$company_row];?></th>
							<?
						}
						?>
						<th rowspan="3" width="100">Group Total Qnty.</th>
						<th rowspan="3">Group Total Value</th>
					</tr>
					<tr>
						<th rowspan="2" width="70">Count</th>
						<th rowspan="2" width="180">Composition</th>
						<th rowspan="2" width="80">Yarn Type</th>
						<th rowspan="2" width="100">Color</th>
						<th rowspan="2" width="80">Lot</th>
						<th rowspan="2" width="60">Supplier</th>
						<th rowspan="2" width="90">Brand</th>
						<th rowspan="2" width="150">Store</th>
                        <!--for floor print-->
						<?
						foreach ($company_id_arr as $company_id=>$company_row)
						{
							if(!empty($floor_data[$company_id]))
							{
								foreach ($floor_data[$company_id] as $key=>$val)
								{
									?>
									<th colspan="<? echo $floor_colspan[$company_id][$key]; ?>" width="100" style="word-break:break-all;"><? echo $val; ?></th>
									<?
								}
							}
							else
							{
								?>
								<th width="100" style="word-break:break-all;"></th>
								<?
							}
						}
						?>
					</tr>
					<tr>
						<!--for room print-->
						<?

						foreach ($company_id_arr as $company_id=>$company_row)
						{
							if(!empty($floor_data[$company_id]))
							{
								foreach ($floor_data[$company_id] as $flr=>$flr_arr)
								{

									if(!empty($room_data[$company_id][$flr]))
									{
										foreach ($room_data[$company_id][$flr] as $key=>$val)
										{

											?>

											<th width="100" style="word-break:break-all;"><? echo $val; ?></th>
											<?
										}
									}
									else
									{

										?>
										<th width="100" style="word-break:break-all;"></th>
										<?
									}
								}
							}
							else
							{
								?>
								<th width="100" style="word-break:break-all;"></th>
								<?
							}
						}


						?>
					</tr>
				</thead>
			</table>
			<div style="max-height:425px; overflow-y:auto; width:<? echo $width+20; ?>px;" id="scroll_body">
				<table class="rpt_table" border="1" width="<? echo $width; ?>" rules="all" id="table_body">
					<tbody>
						<?
						$i = 0;
						$print_data_arr = array();
						$grand_total_arr = array();
						foreach($data_arr as $count_id=>$count_data)
						{
							foreach($count_data as $comp_dtls=>$comp_data)
							{
								foreach($comp_data as $y_type=>$type_data)
								{
									foreach($type_data as $color_id=>$color_data)
									{
										foreach($color_data as $lot_no=>$lot_data)
										{
											foreach($lot_data as $supl_id=>$supl_data)
											{
												foreach($supl_data as $store_id=>$store_data)
												{
													$i++;
													$print_data_arr[$i]['count_id'] = $yarn_count_arr[$count_id];
													$print_data_arr[$i]['comp_dtls'] = $comp_dtls;
													$print_data_arr[$i]['y_type'] = $yarn_type[$y_type];
													$print_data_arr[$i]['color_id'] = $color_name_arr[$color_id];
													$print_data_arr[$i]['supplier_id'] = $supplierArr[$supl_id];
													$print_data_arr[$i]['lot_no'] = $lot_no;
													$print_data_arr[$i]['store_id'] = $storre_name_arr[$store_id];

													foreach ($store_data as $company_id=>$company_row)
													{
														//for brand
														foreach($company_row as $k_flr=>$v_flr)
														{
															foreach($v_flr as $k_rm=>$v_rm)
															{
																$print_data_arr[$i]['brand'] = $brand_name_arr[$v_rm['BRAND']];
															}
														}
														//end for brand

														if(!empty($floor_data[$company_id]))
														{
															foreach ($floor_data[$company_id] as $flr=>$flr_arr)
															{
																if(!empty($room_data[$company_id][$flr]))
																{
																	foreach($room_data[$company_id][$flr] as $rm=>$rmv)
																	{
																		if(!empty($store_data[$company_id][$flr][$rm]))
																		{
																			$rm_balance_qty = 0;
																			$rm_balance_amt = 0;
																			$rm_balance_qty = $store_data[$company_id][$flr][$rm]['RCV_TOTAL']-$store_data[$company_id][$flr][$rm]['ISSUE_TOTAL'];
																			$rm_balance_amt = $store_data[$company_id][$flr][$rm]['RCV_TOTAL_AMT']-$store_data[$company_id][$flr][$rm]['ISSUE_TOTAL_AMT'];
																			$print_data_arr[$i]['balance_qty'][] = number_format($rm_balance_qty, 2, '.', '');
																			$print_data_arr[$i]['group_sub_total'] += number_format($rm_balance_qty, 2, '.', '');
																			$print_data_arr[$i]['group_sub_total_amt'] += number_format($rm_balance_amt, 2, '.', '');

																			//for grand total
																			$grand_total_arr[$company_id][$flr][$rm]['balance_qty'] += number_format($rm_balance_qty, 2, '.', '');
																			$grand_group_sub_total += number_format($rm_balance_qty, 2, '.', '');
																			$grand_group_sub_total_amt += number_format($rm_balance_amt, 2, '.', '');

																		}
																		else
																		{
																			$print_data_arr[$i]['balance_qty'][] = 0.00;
																			$print_data_arr[$i]['group_sub_total'] += 0;
																			$print_data_arr[$i]['group_sub_total_amt'] += 0;
																		}
																	}
																}
																else
																{
																	$print_data_arr[$i]['balance_qty'][] = 0.00;
																	$print_data_arr[$i]['group_sub_total'] += 0;
																	$print_data_arr[$i]['group_sub_total_amt'] += 0;
																}
															}
														}
														else
														{
															$print_data_arr[$i]['balance_qty'][] = 0.00;
															$print_data_arr[$i]['group_sub_total'] += 0;
															$print_data_arr[$i]['group_sub_total_amt'] += 0;
														}
													}
												}
											}
										}
									}
								}
							}
						}

						/*
						|----------------------------------------------------------
						| checking with and without zero value
						|----------------------------------------------------------
						*/
						//var_dump($print_data_arr);
						$zs = 0;
						$p_data_arr = array();
						foreach($print_data_arr as $key=>$val)
						{
							if($value_with == 1)
							{
								if($val['group_sub_total'] > 0)
								{
									$zs++;
									$p_data_arr[$zs] = $val;
								}
							}
							else
							{
								$zs++;
								$p_data_arr[$zs] = $val;
							}
						}
						//end checking with and without zero value

						$sl = 0;
						foreach($p_data_arr as $row)
						{
							$sl++;
							if($sl%2==0) $bgcolor="#E9F3FF";
							else $bgcolor="#FFFFFF";
							if ((($get_upto_qnty == 1 && $row['group_sub_total'] > $txt_qnty) || ($row['group_sub_total'] == 2 && $row['group_sub_total'] < $txt_qnty) || ($get_upto_qnty == 3 && $row['group_sub_total'] >= $txt_qnty) || ($get_upto_qnty == 4 && $row['group_sub_total'] <= $txt_qnty) || ($get_upto_qnty == 5 && $row['group_sub_total'] == $txt_qnty) || $get_upto_qnty == 0)) {


								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
									<td align="center" width="40"><? echo $sl; ?></td>
									<td width="70" style="word-break:break-all;"><p><? echo $row['count_id']; ?></p></td>
									<td width="180" style="word-break:break-all;"><p><? echo $row['comp_dtls']; ?></p></td>
									<td width="80" style="word-break:break-all;"><p><? echo $row['y_type']; ?></p></td>
									<td width="100" style="word-break:break-all;"><p><? echo $row['color_id']; ?></p></td>
									<td width="80" style="word-break:break-all;"><p><? echo $row['lot_no']; ?></p></td>
									<td width="60" style="word-break:break-all;"><? echo $row['supplier_id']; ?></td>
									<td width="90" style="word-break:break-all;"><? echo $row['brand']; ?></td>
									<td width="150" style="word-break:break-all;"><? echo $row['store_id']; ?></td>
									<?

									foreach($row['balance_qty'] as $rm_qty)
									{

										?>
										<td width="100" align="right" style="word-break:break-all;"><? echo number_format($rm_qty, 2); ?></td>
										<?
									}

									?>
									<td width="100" align="right" style="word-break: break-all;"><? echo number_format($row['group_sub_total'],2,".",""); ?></td>
									<td align="right" style="word-break: break-all;"><? echo number_format($row['group_sub_total_amt'],2,".",""); ?></td>
								</tr>
								<?
							}
						}
						//echo 'test_data'.$col_data;
						?>
					</tbody>
                    <tfoot>
                    	<tr>
                        	<th colspan="9">Total</th>
                            <?
							foreach ($company_id_arr as $company_id=>$company_row)
							{
								if(!empty($floor_data[$company_id]))
								{
									foreach ($floor_data[$company_id] as $flr=>$flr_arr)
									{
										if(!empty($room_data[$company_id][$flr]))
										{
											foreach($room_data[$company_id][$flr] as $rm=>$rmv)
											{
												if(!empty($grand_total_arr[$company_id][$flr][$rm]['balance_qty']))
												{
													?>
                                                    <th style="word-break: break-all;"><? echo number_format($grand_total_arr[$company_id][$flr][$rm]['balance_qty'], 2, '.', ''); ?></th>
                                                    <?
												}
												else
												{
													?>
                                                    <th >0.00</th>
                                                    <?
												}
											}
										}
										else
										{
											?>
											<th>0.00</th>
											<?
										}
									}
								}
								else
								{
									?>
									<th>0.00</th>
									<?
								}
							}
							?>
                            <th style="word-break: break-all;"><? echo number_format($grand_group_sub_total, 2, '.', ''); ?></th>
                            <th style="word-break: break-all;"><? echo number_format($grand_group_sub_total_amt, 2, '.', ''); ?></th>
                        </tr>
                    </tfoot>
				</table>
			</div>
		</fieldset>
		<?
	}

	if($type==2)
	{
		//for company
		$sql_comp = "SELECT A.ID AS STORE_ID, C.ID, C.COMPANY_NAME FROM LIB_STORE_LOCATION A, LIB_STORE_LOCATION_CATEGORY B, LIB_COMPANY C WHERE A.ID=B.STORE_LOCATION_ID AND A.COMPANY_ID=C.ID AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.CATEGORY_TYPE IN(1)".$comp_cond.$store_cond1." ORDER BY C.ID ASC";
		//echo $sql_comp;
		$sql_comp_rslt = sql_select($sql_comp);
		$company_id_arr = array();
		$store_id_arr = array();
		foreach ($sql_comp_rslt as $row)
		{
			$company_id_arr[$row['ID']] = $row['ID'];
			$store_id_arr[$row['STORE_ID']] = $row['STORE_ID'];
		}
		unset($sql_comp_rslt);
		$cbo_company_name = implode(',',$company_id_arr);
		$cbo_store = implode(',',$store_id_arr);

		$floor_room_rack_arr = return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst where company_id IN(".$cbo_company_name.") and status_active=1 and is_deleted=0");

		//for main query
		/*$sql_receive = "SELECT A.ID,A.COMPANY_ID, A.SUPPLIER_ID, A.YARN_COUNT_ID, A.YARN_COMP_TYPE1ST, A.YARN_COMP_PERCENT1ST, A.YARN_COMP_TYPE2ND, A.YARN_COMP_PERCENT2ND, A.YARN_TYPE, A.COLOR, A.LOT, A.BRAND, B.STORE_ID, B.FLOOR_ID, B.ROOM,B.RACK,B.SELF,B.BUYER_ID,B.REMARKS,
		SUM(CASE WHEN B.TRANSACTION_TYPE IN (1,4,5) AND B.TRANSACTION_DATE <= '".$from_date."' THEN B.CONS_QUANTITY ELSE 0 END) AS RCV_TOTAL,
		SUM(CASE WHEN B.TRANSACTION_TYPE IN (1,4,5) AND B.TRANSACTION_DATE <= '".$from_date."' THEN B.CONS_AMOUNT ELSE 0 END) AS RCV_TOTAL_AMT,
		SUM(CASE WHEN B.TRANSACTION_TYPE IN (2,3,6) AND B.TRANSACTION_DATE <= '".$from_date."' THEN B.CONS_QUANTITY ELSE 0 END) AS ISSUE_TOTAL,
		SUM(CASE WHEN B.TRANSACTION_TYPE IN (2,3,6) AND B.TRANSACTION_DATE <= '".$from_date."' THEN B.CONS_AMOUNT ELSE 0 END) AS ISSUE_TOTAL_AMT
		FROM PRODUCT_DETAILS_MASTER A, INV_TRANSACTION B
		WHERE A.ID = B.PROD_ID AND B.TRANSACTION_TYPE IN (1,2,3,4,5,6) AND B.ITEM_CATEGORY = 1 AND A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0".$company_cond.$search_cond." AND B.CONS_QUANTITY > 0
		GROUP BY A.ID,A.COMPANY_ID, A.SUPPLIER_ID, A.YARN_COUNT_ID, A.YARN_COMP_TYPE1ST, A.YARN_COMP_PERCENT1ST, A.YARN_COMP_TYPE2ND, A.YARN_COMP_PERCENT2ND, A.YARN_TYPE, A.COLOR, A.LOT, A.BRAND, B.STORE_ID, B.FLOOR_ID, B.ROOM,B.RACK,B.SELF,B.BUYER_ID,B.REMARKS ORDER BY A.YARN_COUNT_ID ASC"; //and a.id=18462*/

		$sql_receive = "SELECT A.ID,A.COMPANY_ID, A.SUPPLIER_ID, A.YARN_COUNT_ID, A.YARN_COMP_TYPE1ST, A.YARN_COMP_PERCENT1ST, A.YARN_COMP_TYPE2ND, A.YARN_COMP_PERCENT2ND, A.YARN_TYPE, A.COLOR, A.LOT, A.BRAND, B.STORE_ID, B.FLOOR_ID, B.ROOM,B.RACK,B.SELF,B.BUYER_ID,B.REMARKS, B.TRANSACTION_TYPE, B.TRANSACTION_DATE, B.CONS_QUANTITY, B.CONS_AMOUNT
		FROM PRODUCT_DETAILS_MASTER A, INV_TRANSACTION B
		WHERE A.ID = B.PROD_ID AND B.TRANSACTION_TYPE IN (1,2,3,4,5,6) AND B.ITEM_CATEGORY = 1 AND A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0 and b.TRANSACTION_DATE <= '".$from_date."'".$company_cond.$search_cond." AND B.CONS_QUANTITY > 0"; 
		//and a.id=18462
		//echo $sql_receive; die;
		$result_sql_receive = sql_select($sql_receive);
		$data_arr = array();
		foreach ($result_sql_receive as $row)
		{
			//$compositionDetails = $composition[$row['YARN_COMP_TYPE1ST']] . " " . $row['YARN_COMP_PERCENT1ST'];
			$compositionDetails = $composition[$row[csf("yarn_comp_type1st")]] . " " . $row[csf("yarn_comp_percent1st")] . "%\n";
			if ($row[csf("yarn_comp_type2nd")] != 0)
				$compositionDetails .= $composition[$row[csf("yarn_comp_type2nd")]] . " " . $row[csf("yarn_comp_percent2nd")] . "%";

			$data_arr[$row['COMPANY_ID']][$row['ID']][$row['STORE_ID']][$row['FLOOR_ID']][$row['ROOM']][$row['RACK']][$row['SELF']]['yarn_count_id'] = $row['YARN_COUNT_ID'];
			$data_arr[$row['COMPANY_ID']][$row['ID']][$row['STORE_ID']][$row['FLOOR_ID']][$row['ROOM']][$row['RACK']][$row['SELF']]['yarn_comp'] = $compositionDetails;
			$data_arr[$row['COMPANY_ID']][$row['ID']][$row['STORE_ID']][$row['FLOOR_ID']][$row['ROOM']][$row['RACK']][$row['SELF']]['yarn_type'] = $row['YARN_TYPE'];
			$data_arr[$row['COMPANY_ID']][$row['ID']][$row['STORE_ID']][$row['FLOOR_ID']][$row['ROOM']][$row['RACK']][$row['SELF']]['color'] = $row['COLOR'];
			$data_arr[$row['COMPANY_ID']][$row['ID']][$row['STORE_ID']][$row['FLOOR_ID']][$row['ROOM']][$row['RACK']][$row['SELF']]['lot'] = $row['LOT'];
			$data_arr[$row['COMPANY_ID']][$row['ID']][$row['STORE_ID']][$row['FLOOR_ID']][$row['ROOM']][$row['RACK']][$row['SELF']]['supplier_id'] = $row['SUPPLIER_ID'];
			$data_arr[$row['COMPANY_ID']][$row['ID']][$row['STORE_ID']][$row['FLOOR_ID']][$row['ROOM']][$row['RACK']][$row['SELF']]['brand'] = $row['BRAND'];

			if (($row['TRANSACTION_TYPE']==1 || $row['TRANSACTION_TYPE']==4 || $row['TRANSACTION_TYPE']==5) && strtotime($row['TRANSACTION_DATE']) <= strtotime($from_date))
			{
				$data_arr[$row['COMPANY_ID']][$row['ID']][$row['STORE_ID']][$row['FLOOR_ID']][$row['ROOM']][$row['RACK']][$row['SELF']]['rcv_total'] += $row['CONS_QUANTITY'];
				$data_arr[$row['YARN_COUNT_ID']][$compositionDetails][$row['YARN_TYPE']][$row['COLOR']][$row['LOT']][$row['SUPPLIER_ID']][$row['STORE_ID']][$row['COMPANY_ID']][$row['FLOOR_ID']][$row['ROOM']]['RCV_TOTAL_AMT'] += $row['CONS_AMOUNT'];
				
			}
			if (($row['TRANSACTION_TYPE']==2 || $row['TRANSACTION_TYPE']==3 || $row['TRANSACTION_TYPE']==6) && strtotime($row['TRANSACTION_DATE']) <= strtotime($from_date))
			{
				$data_arr[$row['COMPANY_ID']][$row['ID']][$row['STORE_ID']][$row['FLOOR_ID']][$row['ROOM']][$row['RACK']][$row['SELF']]['issue_total'] += $row['CONS_QUANTITY'];
				$data_arr[$row['YARN_COUNT_ID']][$compositionDetails][$row['YARN_TYPE']][$row['COLOR']][$row['LOT']][$row['SUPPLIER_ID']][$row['STORE_ID']][$row['COMPANY_ID']][$row['FLOOR_ID']][$row['ROOM']]['ISSUE_TOTAL_AMT'] += $row['CONS_AMOUNT'];
			}

			$data_arr[$row['COMPANY_ID']][$row['ID']][$row['STORE_ID']][$row['FLOOR_ID']][$row['ROOM']][$row['RACK']][$row['SELF']]['buyer_id'] = $row['BUYER_ID'];
			$data_arr[$row['COMPANY_ID']][$row['ID']][$row['STORE_ID']][$row['FLOOR_ID']][$row['ROOM']][$row['RACK']][$row['SELF']]['remarks'] .= $row['REMARKS'].",";
		}
		unset($result_sql_receive);
		// echo "<pre>";
		// print_r($data_arr);
		// echo "</pre>";


		ob_start();
		?>
		<fieldset style="width:1740px;margin:5px auto;" id="fieldsetId">
			<table width="1740" cellspacing="0" class="rpt_table" rules="all" id="table_header_1">
				<thead>
					<tr class="form_caption" style="border:none;">
						<td colspan="17" align="center" style="border:none;font-size:16px; font-weight:bold">Location Wise Yarn Stock</td>
					</tr>
					<!--<tr style="border:none;">
						<td colspan="16" align="center" style="border:none; font-size:14px;">
							<? echo ($cbo_company_name == '')?"All Company":$companyArr[str_replace("'", "", $cbo_company_name)]; ?>
						</td>
					</tr>-->
					<tr style="border:none;">
						<td colspan="17" align="center" style="border:none;font-size:12px; font-weight:bold">
							<? if ($txt_date_from != "" && $txt_date_from != "") echo "From " . change_date_format($txt_date_from, 'dd-mm-yyyy') . " To " . change_date_format($txt_date_from, 'dd-mm-yyyy') . ""; ?>
						</td>
					</tr>

					<tr>
						<th width="40">SL</th>
						<th width="100">Product ID</th>
						<th width="100">Count</th>
						<th width="180">Composition</th>
						<th width="100">Yarn Type</th>
						<th width="100">Color</th>
						<th width="100">Lot</th>
						<th width="100">Supplier</th>
						<th width="100">Buyer</th>
						<th width="100">Brand</th>
						<th width="100">Store</th>
						<th width="100">Floor</th>
						<th width="100">Room</th>
						<th width="100">Rack No</th>
						<th width="100">Self No</th>
						<th width="100">Stock QTY</th>
						<th width="">Remarks</th>
					</tr>

				</thead>
			</table>
			<div style="max-height:425px; overflow-y:auto; width:1740px;" id="scroll_body">
				<table class="rpt_table" border="1" width="1720" rules="all" id="table_body">
					<tbody>
						<?
						$i = 0;
						$$tot_stock_qty = 0;
						//echo "<pre>";print_r($data_arr);
						$sl = 0;
						foreach($data_arr as $com_id=>$com_data)
						{
							foreach($com_data as $prod_id=>$prod_data)
							{
								foreach($prod_data as $store_id=>$store_data)
								{
									foreach($store_data as $floor_id=>$floor_data)
									{
										foreach($floor_data as $room_id=>$room_data)
										{
											foreach($room_data as $rack_id=>$rack_data)
											{
												foreach($rack_data as $self_id=>$row)
												{
													//var_dump($row);
													$sl++;
													if($sl%2==0) $bgcolor="#E9F3FF";
													else $bgcolor="#FFFFFF";

													$stockInHand = $row['rcv_total'] - $row['issue_total'];

													if ((($get_upto_qnty == 1 && $stockInHand > $txt_qnty) || ($get_upto_qnty == 2 && $stockInHand < $txt_qnty) || ($get_upto_qnty == 3 && $stockInHand >= $txt_qnty) || ($get_upto_qnty == 4 && $stockInHand <= $txt_qnty) || ($get_upto_qnty == 5 && $stockInHand == $txt_qnty) || $get_upto_qnty == 0)) {

														if($value_with == 1)
														{
															if (number_format($stockInHand, 2) > 0.00)
															{
																?>
																<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
																	<td align="center" width="40"><? echo $sl; ?></td>
																	<td width="100" style="word-break:break-all;"><p><? echo $prod_id; ?></p></td>
																	<td width="100" style="word-break:break-all;"><p><? echo $yarn_count_arr[$row['yarn_count_id']]; ?></p></td>
																	<td width="180" style="word-break:break-all;"><p><? echo $row['yarn_comp']; ?></p></td>
																	<td width="100" style="word-break:break-all;"><p><? echo $yarn_type[$row['yarn_type']]; ?></p></td>
																	<td width="100" style="word-break:break-all;"><p><? echo $color_name_arr[$row['color']]; ?></p></td>
																	<td width="100" style="word-break:break-all;"><? echo $row['lot']; ?></td>
																	<td width="100" style="word-break:break-all;"><? echo $supplierArr[$row['supplier_id']]; ?></td>
																	<td width="100" style="word-break:break-all;"><? echo $buyer_dtls[$row['buyer_id']]; ?></td>
																	<td width="100" style="word-break:break-all;"><? echo $brand_name_arr[$row['brand']]; ?></td>
																	<td width="100" style="word-break:break-all;"><? echo $storre_name_arr[$store_id]; ?></td>
																	<td width="100" style="word-break:break-all;"><? echo $floor_room_rack_arr[$floor_id]; ?></td>
																	<td width="100" style="word-break:break-all;"><? echo $floor_room_rack_arr[$room_id]; ?></td>
																	<td width="100" style="word-break:break-all;"><? echo $floor_room_rack_arr[$rack_id]; ?></td>
																	<td width="100" style="word-break:break-all;"><? echo $floor_room_rack_arr[$self_id]; ?></td>
																	<td width="100" align="right" style="word-break: break-all;"><? echo number_format($stockInHand, 2); ?></td>
																	<td align="right" style="word-break: break-all;">
																	<? echo chop($row['remarks'],",",); ?>
																	</td>
																</tr>
																<?
																$tot_stock_qty += $stockInHand;
															}
														}
														else
														{
															$stockInHand = (int)$stockInHand;
															if(number_format($stockInHand, 2) <= 0.00)
															{
																?>
																<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
																	<td align="center" width="40"><? echo $sl; ?></td>
																	<td width="100" style="word-break:break-all;"><p><? echo $prod_id; ?></p></td>
																	<td width="100" style="word-break:break-all;"><p><? echo $yarn_count_arr[$row['yarn_count_id']]; ?></p></td>
																	<td width="180" style="word-break:break-all;"><p><? echo $row['yarn_comp']; ?></p></td>
																	<td width="100" style="word-break:break-all;"><p><? echo $yarn_type[$row['yarn_type']]; ?></p></td>
																	<td width="100" style="word-break:break-all;"><p><? echo $color_name_arr[$row['color']]; ?></p></td>
																	<td width="100" style="word-break:break-all;"><? echo $row['lot']; ?></td>
																	<td width="100" style="word-break:break-all;"><? echo $supplierArr[$row['supplier_id']]; ?></td>
																	<td width="100" style="word-break:break-all;"><? echo $buyer_dtls[$row['buyer_id']]; ?></td>
																	<td width="100" style="word-break:break-all;"><? echo $brand_name_arr[$row['brand']]; ?></td>
																	<td width="100" style="word-break:break-all;"><? echo $storre_name_arr[$store_id]; ?></td>
																	<td width="100" style="word-break:break-all;"><? echo $floor_room_rack_arr[$floor_id]; ?></td>
																	<td width="100" style="word-break:break-all;"><? echo $floor_room_rack_arr[$room_id]; ?></td>
																	<td width="100" style="word-break:break-all;"><? echo $floor_room_rack_arr[$rack_id]; ?></td>
																	<td width="100" style="word-break:break-all;"><? echo $floor_room_rack_arr[$self_id]; ?></td>
																	<td width="100" align="right" style="word-break: break-all;"><? echo number_format($stockInHand, 2); ?></td>
																	<td align="right" style="word-break: break-all;">
																	<? echo chop($row['remarks'],",",); ?>
																	</td>
																</tr>
																<?
																$tot_stock_qty += $stockInHand;
															}
														}
													}

												}
											}
										}
									}
								}
							}
						}
						//echo 'test_data'.$col_data;
						?>
					</tbody>
                    <tfoot>
                    	<tr>
                        	<th colspan="15">Total</th>
                            <th style="word-break: break-all;"><? echo number_format($tot_stock_qty, 2, '.', ''); ?></th>
                            <th style="word-break: break-all;"></th>
                        </tr>
                    </tfoot>
				</table>
			</div>
		</fieldset>
		<?
	}


	$html = ob_get_contents();
	ob_clean();
	//foreach (glob("*.xls") as $filename)
	foreach (glob("$user_id*.xls") as $filename)
	{
		@unlink($filename);
	}
	//---------end------------//
	$name = time();
	$filename = $user_id . "_" . $name . ".xls";
	$create_new_doc = fopen($filename, 'w+');
	$is_created = fwrite($create_new_doc, $html);
	echo "$html####$filename####$type";
	exit();
}
if($action=="report_generate_exel_only")
{
	$started = microtime(true);
	session_start();
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	//$type = str_replace("'","",$type);
	$value_with = str_replace("'","",$value_with);
	$comp_id = str_replace("'","",$cbo_company_name);
	//echo $value_with.test;die;

	//for library dtls
	$companyArr 	= return_library_array("select id, company_name from lib_company where status_active=1", "id", "company_name");
	$supplierArr 	= return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$color_name_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$storre_name_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');

	//for company
	$company_cond = '';
	$comp_cond = '';
	if($cbo_company_name != '')
	{
		$comp_cond = " AND C.ID IN(".$cbo_company_name.")";
		$company_cond = " AND A.COMPANY_ID IN(".$cbo_company_name.")";
	}

	$search_cond .= ($txt_lot_no!='')?" AND A.LOT = '".$txt_lot_no."'":'';
	$search_cond .= ($cbo_supplier!='')?" AND A.SUPPLIER_ID IN(".$cbo_supplier.")":"";
	$search_cond .= ($cbo_dyed_type==0)?"":(($cbo_dyed_type==1)?" AND A.DYED_TYPE = ".$cbo_dyed_type:" AND A.DYED_TYPE <> 1");
	//$search_cond .= ($cbo_composition!='')?" AND (A.YARN_COMP_TYPE1ST IN(".$cbo_composition.") OR A.YARN_COMP_TYPE2ND IN(".$cbo_composition."))":"";
	$search_cond .= ($cbo_composition!='')?" AND A.YARN_COMP_TYPE1ST IN(".$cbo_composition.")":"";
	$search_cond .= ($cbo_yarn_count!='')?" AND A.YARN_COUNT_ID IN(".$cbo_yarn_count.")":"";
	$search_cond .= ($cbo_yarn_type!='')?" AND A.YARN_TYPE IN(.".$cbo_yarn_type.")":"";
	//$search_cond .= ($value_with!=0)?" AND A.CURRENT_STOCK > 0":"";

	//for store
	$store_cond = '';
	$store_cond1 = '';
	$cbo_store = str_replace("'","",$cbo_store);
	if($cbo_store != '')
	{
		//$store_cond = " AND B.STORE_ID IN(".$cbo_store.")";
		$store_cond1 = " AND A.ID IN(".$cbo_store.")";
		$search_cond .= " AND B.STORE_ID IN(".$cbo_store.")";
	}

	//for floor
	$floor_cond = '';
	$cbo_floor = str_replace("'","",$cbo_floor);
	if($cbo_floor != '')
	{
		$floor_cond = " AND B.FLOOR_ID IN(".$cbo_floor.")";
		$search_cond .= " AND B.FLOOR_ID IN(".$cbo_floor.")";
	}

	//for room
	$room_cond = '';
	$cbo_room = str_replace("'","",$cbo_room);
	if($cbo_room != '')
	{
		$room_cond = " AND B.ROOM_ID IN(".$cbo_room.")";
		$search_cond .= " AND B.ROOM IN(".$cbo_room.")";
	}

	//for date
	$from_date = change_date_format($from_date, '', '', 1);
	if ($from_date != "")
	{
		$date_cond = " AND A.TRANSACTION_DATE <= '".$from_date."'";
	}


	//for company
		$sql_comp = "SELECT A.ID AS STORE_ID, C.ID, C.COMPANY_NAME FROM LIB_STORE_LOCATION A, LIB_STORE_LOCATION_CATEGORY B, LIB_COMPANY C WHERE A.ID=B.STORE_LOCATION_ID AND A.COMPANY_ID=C.ID AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.CATEGORY_TYPE IN(1)".$comp_cond.$store_cond1." ORDER BY C.ID ASC";
		//echo $sql_comp;
		$sql_comp_rslt = sql_select($sql_comp);
		$company_id_arr = array();
		$company_id_arr_count = array();
		$store_id_arr = array();
		foreach ($sql_comp_rslt as $row)
		{
			$company_id_arr[$row['ID']] = $row['ID'];
			$store_id_arr[$row['STORE_ID']] = $row['STORE_ID'];

			if($company_id_arr_count[$row['ID']]=="")
			{
				if($row['ID']==4){
					$company_id_arr_count[$row['ID']]+=300;
				}
				else
				{
					$company_id_arr_count[$row['ID']]+=150;
				}

			}

		}

		unset($sql_comp_rslt);
		$cbo_company_name = implode(',',$company_id_arr);
		$cbo_store = implode(',',$store_id_arr);

		//for floor
		$sql_floor = "SELECT A.FLOOR_ROOM_RACK_ID, A.FLOOR_ROOM_RACK_NAME, A.COMPANY_ID, B.LOCATION_ID, B.STORE_ID FROM LIB_FLOOR_ROOM_RACK_MST A INNER JOIN LIB_FLOOR_ROOM_RACK_DTLS B ON A.FLOOR_ROOM_RACK_ID = B.FLOOR_ID WHERE A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0 AND A.COMPANY_ID IN(".$cbo_company_name.") AND B.STORE_ID IN(".$cbo_store.")".$floor_cond.$room_cond." GROUP BY A.FLOOR_ROOM_RACK_ID, A.FLOOR_ROOM_RACK_NAME, A.COMPANY_ID, B.LOCATION_ID, B.STORE_ID ORDER BY A.FLOOR_ROOM_RACK_NAME";
		//echo $sql_floor;
		$sql_floor_rslt = sql_select($sql_floor);
		$floor_data = array();
		$floor_id_arr = array();
		foreach($sql_floor_rslt as $row)
		{
			$floor_data[$row['COMPANY_ID']][$row['FLOOR_ROOM_RACK_ID']] = $row['FLOOR_ROOM_RACK_NAME'];
			$floor_id_arr[$row['FLOOR_ROOM_RACK_ID']] = $row['FLOOR_ROOM_RACK_ID'];
		}
		unset($sql_floor_rslt);
		$cbo_floor = implode(',',$floor_id_arr);

		//for room
		$sql_room = "SELECT A.FLOOR_ROOM_RACK_ID, A.FLOOR_ROOM_RACK_NAME, A.COMPANY_ID, B.LOCATION_ID, B.STORE_ID, B.FLOOR_ID FROM LIB_FLOOR_ROOM_RACK_MST A INNER JOIN LIB_FLOOR_ROOM_RACK_DTLS B ON A.FLOOR_ROOM_RACK_ID = B.ROOM_ID WHERE A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0 AND A.COMPANY_ID IN(".$cbo_company_name.") AND B.STORE_ID IN(".$cbo_store.") AND B.FLOOR_ID IN(".$cbo_floor.")".$room_cond." GROUP BY A.FLOOR_ROOM_RACK_ID, A.FLOOR_ROOM_RACK_NAME, A.COMPANY_ID, B.LOCATION_ID, B.STORE_ID, B.FLOOR_ID ORDER BY A.FLOOR_ROOM_RACK_NAME";
		//echo $sql_room;
		$sql_room_rslt = sql_select($sql_room);
		$room_data = array();
		$company_colspan = array();
		$floor_colspan = array();
		$td_width = 0;
		foreach($sql_room_rslt as $row)
		{
			$room_data[$row['COMPANY_ID']][$row['FLOOR_ID']][$row['FLOOR_ROOM_RACK_ID']] = $row['FLOOR_ROOM_RACK_NAME'];
			$company_colspan[$row['COMPANY_ID']]++;
			$floor_colspan[$row['COMPANY_ID']][$row['FLOOR_ID']]++;
			$td_width = $td_width+100;
		}
		unset($sql_floor_rslt);
		/*echo "<pre>";
		print_r($floor_colspan);
		echo "</pre>";*/

		//for main query
		$sql_receive = "SELECT A.COMPANY_ID, A.SUPPLIER_ID, A.YARN_COUNT_ID, A.YARN_COMP_TYPE1ST, A.YARN_COMP_PERCENT1ST, A.YARN_COMP_TYPE2ND, A.YARN_COMP_PERCENT2ND, A.YARN_TYPE, A.COLOR, A.LOT, B.STORE_ID, B.FLOOR_ID, B.ROOM,
		SUM(CASE WHEN B.TRANSACTION_TYPE IN (1,4,5) AND B.TRANSACTION_DATE <= '".$from_date."' THEN B.CONS_QUANTITY ELSE 0 END) AS RCV_TOTAL,
		SUM(CASE WHEN B.TRANSACTION_TYPE IN (1,4,5) AND B.TRANSACTION_DATE <= '".$from_date."' THEN B.CONS_AMOUNT ELSE 0 END) AS RCV_TOTAL_AMT,
		SUM(CASE WHEN B.TRANSACTION_TYPE IN (2,3,6) AND B.TRANSACTION_DATE <= '".$from_date."' THEN B.CONS_QUANTITY ELSE 0 END) AS ISSUE_TOTAL,
		SUM(CASE WHEN B.TRANSACTION_TYPE IN (2,3,6) AND B.TRANSACTION_DATE <= '".$from_date."' THEN B.CONS_AMOUNT ELSE 0 END) AS ISSUE_TOTAL_AMT
		FROM PRODUCT_DETAILS_MASTER A, INV_TRANSACTION B
		WHERE A.ID = B.PROD_ID AND B.TRANSACTION_TYPE IN (1,2,3,4,5,6) AND B.ITEM_CATEGORY = 1 AND A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0".$company_cond.$search_cond." AND B.CONS_QUANTITY > 0
		GROUP BY A.COMPANY_ID, A.SUPPLIER_ID, A.YARN_COUNT_ID, A.YARN_COMP_TYPE1ST, A.YARN_COMP_PERCENT1ST, A.YARN_COMP_TYPE2ND, A.YARN_COMP_PERCENT2ND, A.YARN_TYPE, A.COLOR, A.LOT, B.STORE_ID, B.FLOOR_ID, B.ROOM";
		//echo $sql_receive; die;
		$result_sql_receive = sql_select($sql_receive);
		$data_arr = array();
		foreach ($result_sql_receive as $row)
		{
			$compositionDetails = $composition[$row['YARN_COMP_TYPE1ST']] . " " . $row['YARN_COMP_PERCENT1ST'];

			$data_arr[$row['YARN_COUNT_ID']][$compositionDetails][$row['YARN_TYPE']][$row['COLOR']][$row['LOT']][$row['SUPPLIER_ID']][$row['STORE_ID']][$row['COMPANY_ID']][$row['FLOOR_ID']][$row['ROOM']]['RCV_TOTAL'] += $row['RCV_TOTAL'];
			$data_arr[$row['YARN_COUNT_ID']][$compositionDetails][$row['YARN_TYPE']][$row['COLOR']][$row['LOT']][$row['SUPPLIER_ID']][$row['STORE_ID']][$row['COMPANY_ID']][$row['FLOOR_ID']][$row['ROOM']]['ISSUE_TOTAL'] += $row['ISSUE_TOTAL'];

			$data_arr[$row['YARN_COUNT_ID']][$compositionDetails][$row['YARN_TYPE']][$row['COLOR']][$row['LOT']][$row['SUPPLIER_ID']][$row['STORE_ID']][$row['COMPANY_ID']][$row['FLOOR_ID']][$row['ROOM']]['RCV_TOTAL_AMT'] += $row['RCV_TOTAL_AMT'];
			$data_arr[$row['YARN_COUNT_ID']][$compositionDetails][$row['YARN_TYPE']][$row['COLOR']][$row['LOT']][$row['SUPPLIER_ID']][$row['STORE_ID']][$row['COMPANY_ID']][$row['FLOOR_ID']][$row['ROOM']]['ISSUE_TOTAL_AMT'] += $row['ISSUE_TOTAL_AMT'];
		}
		unset($result_sql_receive);
		/*echo "<pre>";
		print_r($data_arr);
		echo "</pre>";*/
		$comCountWidth=0;
		if(count($company_id_arr_count)>1)
		{
			foreach ($company_id_arr_count as $values) {
				$comCountWidth+=$values;
			}
		}

		//echo $comCountWidth; die;
		$width = (1400+$comCountWidth+($td_width));
		//ob_start();

		//echo "hello";die;

		 if ($txt_date_from != "" && $txt_date_from != "") $dateTitle= "From " . change_date_format($txt_date_from, 'dd-mm-yyyy') . " To " . change_date_format($txt_date_from, 'dd-mm-yyyy');
		$html = "";


		$html .= '
			<table  >
				<thead>
					<tr class="form_caption" style="border:none;">
						<td colspan="16" align="center" style="border:none;font-size:16px; font-weight:bold">Location Wise Yarn Stock</td>
					</tr>

					<tr style="border:none;">
						<td colspan="16" align="center" style="border:none;font-size:12px; font-weight:bold">
							'.$dateTitle.'
						</td>
					</tr>




					<tr>
						<th rowspan="3" width="40">SL</th>
						<th colspan="7">Description</th>';


						$room_count = '';
						$count_id_arr=array();
						foreach ($company_id_arr as $company_id=>$company_row)
						{
							if(!empty($floor_data[$company_id]))
							{
								foreach ($floor_data[$company_id] as $flr=>$flr_arr)
								{

									if(!empty($room_data[$company_id][$flr]))
									{
										foreach ($room_data[$company_id][$flr] as $key=>$val)
										{
											$room_count++;
											$count_id_arr[$company_id]=$room_count;
										}
									}
									else
									{
										$room_count++;
										$count_id_arr[$company_id]=$room_count;
									}
								}
							}
							$room_count=0;
						}
						/*echo "<pre>";
							print_r($count_id_arr);
						echo "</pre>";*/
						//$count_id_arr=array(2 => 100,3 => 99,4 => 94);

						foreach ($company_id_arr as $company_id=>$company_row)
						{

							$html .='<th colspan="'. $count_id_arr[$company_id].'" >'.$companyArr[$company_row].'</th>';

						}

						$html .='<th rowspan="3" width="100">Group Total Qnty</th>
						<th rowspan="3">Group Total Value</th>
					</tr>
					<tr>
						<th rowspan="2" width="70">Count</th>
						<th rowspan="2" width="180">Composition</th>
						<th rowspan="2" width="80">Yarn Type</th>
						<th rowspan="2" width="100">Color</th>
						<th rowspan="2" width="80">Lot</th>
						<th rowspan="2" width="150">Supplier</th>
						<th rowspan="2" width="150">Store</th>';

						foreach ($company_id_arr as $company_id=>$company_row)
						{
							if(!empty($floor_data[$company_id]))
							{
								foreach ($floor_data[$company_id] as $key=>$val)
								{

									$html .='<th colspan="'. $floor_colspan[$company_id][$key].'" width="100" >'. $val.'</th>';

								}
							}
							else
							{

								$html .='<th width="100" ></th>';

							}
						}

					$html .='</tr>
					<tr>';

						foreach ($company_id_arr as $company_id=>$company_row)
						{
							if(!empty($floor_data[$company_id]))
							{
								foreach ($floor_data[$company_id] as $flr=>$flr_arr)
								{
									if(!empty($room_data[$company_id][$flr]))
									{
										foreach ($room_data[$company_id][$flr] as $key=>$val)
										{

											$html .='<th width="100" >'.$val.'</th>';

										}
									}
									else
									{

										$html .='<th width="100" ></th>';

									}
								}
							}
							else
							{

								$html .='<th width="100" ></th>';

							}
						}

					$html .='</tr>
				</thead>
			</table>

				<table  border="1">
					<tbody>';

						$i = 0;
						$print_data_arr = array();
						$grand_total_arr = array();
						foreach($data_arr as $count_id=>$count_data)
						{
							foreach($count_data as $comp_dtls=>$comp_data)
							{
								foreach($comp_data as $y_type=>$type_data)
								{
									foreach($type_data as $color_id=>$color_data)
									{
										foreach($color_data as $lot_no=>$lot_data)
										{
											foreach($lot_data as $supl_id=>$supl_data)
											{
												foreach($supl_data as $store_id=>$store_data)
												{
													$i++;
													$print_data_arr[$i]['count_id'] = $yarn_count_arr[$count_id];
													$print_data_arr[$i]['comp_dtls'] = $comp_dtls;
													$print_data_arr[$i]['y_type'] = $yarn_type[$y_type];
													$print_data_arr[$i]['color_id'] = $color_name_arr[$color_id];
													$print_data_arr[$i]['supplier_id'] = $supplierArr[$supl_id];
													$print_data_arr[$i]['lot_no'] = $lot_no;
													$print_data_arr[$i]['store_id'] = $storre_name_arr[$store_id];

													foreach ($company_id_arr as $company_id=>$company_row)
													{
														if(!empty($floor_data[$company_id]))
														{
															foreach ($floor_data[$company_id] as $flr=>$flr_arr)
															{
																if(!empty($room_data[$company_id][$flr]))
																{
																	foreach($room_data[$company_id][$flr] as $rm=>$rmv)
																	{
																		if(!empty($store_data[$company_id][$flr][$rm]))
																		{


																			$rm_balance_qty = 0;
																			$rm_balance_amt = 0;
																			$rm_balance_qty = $store_data[$company_id][$flr][$rm]['RCV_TOTAL']-$store_data[$company_id][$flr][$rm]['ISSUE_TOTAL'];

																			/*$rm_balance_chk_qty = $store_data[$company_id][$flr][$rm]['RCV_TOTAL']-$store_data[$company_id][$flr][$rm]['ISSUE_TOTAL'];

																			if ((($get_upto_qnty == 1 && $rm_balance_chk_qty > $txt_qnty) || ($rm_balance_chk_qty == 2 && $rm_balance_chk_qty < $txt_qnty) || ($get_upto_qnty == 3 && $rm_balance_chk_qty >= $txt_qnty) || ($get_upto_qnty == 4 && $rm_balance_chk_qty <= $txt_qnty) || ($get_upto_qnty == 5 && $rm_balance_chk_qty == $txt_qnty) || $get_upto_qnty == 0)) {*/

																					$rm_balance_amt = $store_data[$company_id][$flr][$rm]['RCV_TOTAL_AMT']-$store_data[$company_id][$flr][$rm]['ISSUE_TOTAL_AMT'];
																					$print_data_arr[$i]['balance_qty'][] = number_format($rm_balance_qty, 2, '.', '');
																					$print_data_arr[$i]['group_sub_total'] += number_format($rm_balance_qty, 2, '.', '');
																					$print_data_arr[$i]['group_sub_total_amt'] += number_format($rm_balance_amt, 2, '.', '');

																					//for grand total
																					$grand_total_arr[$company_id][$flr][$rm]['balance_qty'] += number_format($rm_balance_qty, 2, '.', '');
																					$grand_group_sub_total += number_format($rm_balance_qty, 2, '.', '');
																					$grand_group_sub_total_amt += number_format($rm_balance_amt, 2, '.', '');
																			//}

																		}
																		else
																		{
																			$print_data_arr[$i]['balance_qty'][] = 0.00;
																			$print_data_arr[$i]['group_sub_total'] += 0;
																			$print_data_arr[$i]['group_sub_total_amt'] += 0;
																		}
																	}
																}
																else
																{
																	$print_data_arr[$i]['balance_qty'][] = 0.00;
																	$print_data_arr[$i]['group_sub_total'] += 0;
																	$print_data_arr[$i]['group_sub_total_amt'] += 0;
																}
															}
														}
														else
														{
															$print_data_arr[$i]['balance_qty'][] = 0.00;
															$print_data_arr[$i]['group_sub_total'] += 0;
															$print_data_arr[$i]['group_sub_total_amt'] += 0;
														}
													}
												}
											}
										}
									}
								}
							}
						}

						/*
						|----------------------------------------------------------
						| checking with and without zero value
						|----------------------------------------------------------
						*/
						$zs = 0;
						$p_data_arr = array();
						foreach($print_data_arr as $key=>$val)
						{
							if($value_with == 1)
							{
								if($val['group_sub_total'] > 0)
								{
									$zs++;
									$p_data_arr[$zs] = $val;
								}
							}
							else
							{
								$zs++;
								$p_data_arr[$zs] = $val;
							}
						}
						//end checking with and without zero value

						$ii = 1;




						foreach($p_data_arr as $row)
						{
							if ((($get_upto_qnty == 1 && $row['group_sub_total'] > $txt_qnty) || ($get_upto_qnty == 2 && $row['group_sub_total'] < $txt_qnty) || ($get_upto_qnty == 3 && $row['group_sub_total'] >= $txt_qnty) || ($get_upto_qnty == 4 && $row['group_sub_total'] <= $txt_qnty) || ($get_upto_qnty == 5 && $row['group_sub_total'] == $txt_qnty) || $get_upto_qnty == 0)) {

								$html .='<tr>
									<td>' .$ii.'</td>
									<td>'.$row['count_id'].'&nbsp;</td>
									<td>'.$row['comp_dtls'].'</td>
									<td>'.$row['y_type'].'</td>
									<td>'.$row['color_id'].'</td>
									<td>'.$row['lot_no'].'&nbsp;</td>
									<td>'.$row['supplier_id'].'</td>
									<td>'.$row['store_id'].'</td>';

									foreach($row['balance_qty'] as $rm_qty)
									{

										$html .='<td>'. number_format($rm_qty, 2).'</td>';

									}

									$html .='<td >'. number_format($row['group_sub_total'],2,".","").'</td>
									<td>'. number_format($row['group_sub_total_amt'],2,".","").'000</td>
								</tr>';


								$grand_group_sub_totalx+=$row['group_sub_total'];
								$grand_group_sub_total_amtx+=$row['group_sub_total_amt'];

								$ii++;
							}
						}


					$html .='</tbody>
                    <tfoot>
                    	<tr>
                        	<th colspan="8">Total</th>';

							foreach ($company_id_arr as $company_id=>$company_row)
							{
								if(!empty($floor_data[$company_id]))
								{
									foreach ($floor_data[$company_id] as $flr=>$flr_arr)
									{
										if(!empty($room_data[$company_id][$flr]))
										{
											foreach($room_data[$company_id][$flr] as $rm=>$rmv)
											{
												if(!empty($grand_total_arr[$company_id][$flr][$rm]['balance_qty']))
												{

                                                    $html .='<th>'.number_format($grand_total_arr[$company_id][$flr][$rm]['balance_qty'], 2, '.', '').'</th>';

												}
												else
												{

                                                    $html .='<th>0.00</th>';

												}
											}
										}
										else
										{

											$html .='<th>0.00</th>';

										}
									}
								}
								else
								{

									$html .='<th>0.00</th>';

								}
							}

                            $html .='<th>'. number_format($grand_group_sub_totalx, 2, '.', ''). '</th>
                            <th>'. number_format($grand_group_sub_total_amtx, 2, '.', '').'</th>
                        </tr>
                    </tfoot>
				</table>

		';

	//echo "<br />Execution Time: " . (microtime(true) - $started) . "s";


		foreach (glob("sswgfsc_*.xls") as $filename) {
		@unlink($filename);
	}
	$name=time();
	$filename="sswgfsc_".$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$filename####$report_button";
	exit();





	/*$html = ob_get_contents();
	ob_clean();

	foreach (glob("sswgfsc_*.xls") as $filename)
	{
		@unlink($filename);
	}
	$name=time();
	$filename="sswgfsc_".$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$filename####$report_button";
	//echo "$html####$filename####$report_button";
	exit;*/


}
if ($action == "generate_report_10112021")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$type = str_replace("'","",$type);
	$value_with = str_replace("'","",$value_with);
	$comp_id = str_replace("'","",$cbo_company_name);
	//echo $value_with.test;die;

	//for library dtls
	$companyArr 	= return_library_array("select id, company_name from lib_company where status_active=1", "id", "company_name");
	$supplierArr 	= return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$color_name_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$storre_name_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');

	//for company
	$company_cond = '';
	$comp_cond = '';
	if($cbo_company_name != '')
	{
		$comp_cond = " AND C.ID IN(".$cbo_company_name.")";
		$company_cond = " AND A.COMPANY_ID IN(".$cbo_company_name.")";
	}

	$search_cond .= ($txt_lot_no!='')?" AND A.LOT = '".$txt_lot_no."'":'';
	$search_cond .= ($cbo_supplier!='')?" AND A.SUPPLIER_ID IN(".$cbo_supplier.")":"";
	$search_cond .= ($cbo_dyed_type==0)?"":(($cbo_dyed_type==1)?" AND A.DYED_TYPE = ".$cbo_dyed_type:" AND A.DYED_TYPE <> 1");
	//$search_cond .= ($cbo_composition!='')?" AND (A.YARN_COMP_TYPE1ST IN(".$cbo_composition.") OR A.YARN_COMP_TYPE2ND IN(".$cbo_composition."))":"";
	$search_cond .= ($cbo_composition!='')?" AND A.YARN_COMP_TYPE1ST IN(".$cbo_composition.")":"";
	$search_cond .= ($cbo_yarn_count!='')?" AND A.YARN_COUNT_ID IN(".$cbo_yarn_count.")":"";
	$search_cond .= ($cbo_yarn_type!='')?" AND A.YARN_TYPE IN(.".$cbo_yarn_type.")":"";
	//$search_cond .= ($value_with!=0)?" AND A.CURRENT_STOCK > 0":"";

	//for store
	$store_cond = '';
	$store_cond1 = '';
	$cbo_store = str_replace("'","",$cbo_store);
	if($cbo_store != '')
	{
		//$store_cond = " AND B.STORE_ID IN(".$cbo_store.")";
		$store_cond1 = " AND A.ID IN(".$cbo_store.")";
		$search_cond .= " AND B.STORE_ID IN(".$cbo_store.")";
	}

	//for floor
	$floor_cond = '';
	$cbo_floor = str_replace("'","",$cbo_floor);
	if($cbo_floor != '')
	{
		$floor_cond = " AND B.FLOOR_ID IN(".$cbo_floor.")";
		$search_cond .= " AND B.FLOOR_ID IN(".$cbo_floor.")";
	}

	//for room
	$room_cond = '';
	$cbo_room = str_replace("'","",$cbo_room);
	if($cbo_room != '')
	{
		$room_cond = " AND B.ROOM_ID IN(".$cbo_room.")";
		$search_cond .= " AND B.ROOM IN(".$cbo_room.")";
	}

	//for date
	$from_date = change_date_format($from_date, '', '', 1);
	if ($from_date != "")
	{
		$date_cond = " AND A.TRANSACTION_DATE <= '".$from_date."'";
	}

	//echo $num_of_store;
	if($type==1)
	{
		//for company
		$sql_comp = "SELECT A.ID AS STORE_ID, C.ID, C.COMPANY_NAME FROM LIB_STORE_LOCATION A, LIB_STORE_LOCATION_CATEGORY B, LIB_COMPANY C WHERE A.ID=B.STORE_LOCATION_ID AND A.COMPANY_ID=C.ID AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.CATEGORY_TYPE IN(1)".$comp_cond.$store_cond1." ORDER BY C.ID ASC";
		//echo $sql_comp;
		$sql_comp_rslt = sql_select($sql_comp);
		$company_id_arr = array();
		$store_id_arr = array();
		foreach ($sql_comp_rslt as $row)
		{
			$company_id_arr[$row['ID']] = $row['ID'];
			$store_id_arr[$row['STORE_ID']] = $row['STORE_ID'];
		}
		unset($sql_comp_rslt);
		$cbo_company_name = implode(',',$company_id_arr);
		$cbo_store = implode(',',$store_id_arr);

		//for floor
		$sql_floor = "SELECT A.FLOOR_ROOM_RACK_ID, A.FLOOR_ROOM_RACK_NAME, A.COMPANY_ID, B.LOCATION_ID, B.STORE_ID FROM LIB_FLOOR_ROOM_RACK_MST A INNER JOIN LIB_FLOOR_ROOM_RACK_DTLS B ON A.FLOOR_ROOM_RACK_ID = B.FLOOR_ID WHERE A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0 AND A.COMPANY_ID IN(".$cbo_company_name.") AND B.STORE_ID IN(".$cbo_store.")".$floor_cond.$room_cond." GROUP BY A.FLOOR_ROOM_RACK_ID, A.FLOOR_ROOM_RACK_NAME, A.COMPANY_ID, B.LOCATION_ID, B.STORE_ID ORDER BY A.FLOOR_ROOM_RACK_NAME";
		//echo $sql_floor;
		$sql_floor_rslt = sql_select($sql_floor);
		$floor_data = array();
		$floor_id_arr = array();
		foreach($sql_floor_rslt as $row)
		{
			$floor_data[$row['COMPANY_ID']][$row['FLOOR_ROOM_RACK_ID']] = $row['FLOOR_ROOM_RACK_NAME'];
			$floor_id_arr[$row['FLOOR_ROOM_RACK_ID']] = $row['FLOOR_ROOM_RACK_ID'];
		}
		unset($sql_floor_rslt);
		$cbo_floor = implode(',',$floor_id_arr);

		//for room
		$sql_room = "SELECT A.FLOOR_ROOM_RACK_ID, A.FLOOR_ROOM_RACK_NAME, A.COMPANY_ID, B.LOCATION_ID, B.STORE_ID, B.FLOOR_ID FROM LIB_FLOOR_ROOM_RACK_MST A INNER JOIN LIB_FLOOR_ROOM_RACK_DTLS B ON A.FLOOR_ROOM_RACK_ID = B.ROOM_ID WHERE A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0 AND A.COMPANY_ID IN(".$cbo_company_name.") AND B.STORE_ID IN(".$cbo_store.") AND B.FLOOR_ID IN(".$cbo_floor.")".$room_cond." GROUP BY A.FLOOR_ROOM_RACK_ID, A.FLOOR_ROOM_RACK_NAME, A.COMPANY_ID, B.LOCATION_ID, B.STORE_ID, B.FLOOR_ID ORDER BY A.FLOOR_ROOM_RACK_NAME";
		//echo $sql_room;
		$sql_room_rslt = sql_select($sql_room);
		$room_data = array();
		$company_colspan = array();
		$floor_colspan = array();
		$td_width = 0;
		foreach($sql_room_rslt as $row)
		{
			$room_data[$row['COMPANY_ID']][$row['FLOOR_ID']][$row['FLOOR_ROOM_RACK_ID']] = $row['FLOOR_ROOM_RACK_NAME'];
			$company_colspan[$row['COMPANY_ID']]++;
			$floor_colspan[$row['COMPANY_ID']][$row['FLOOR_ID']]++;
			$td_width = $td_width+100;
		}
		unset($sql_floor_rslt);
		/*echo "<pre>";
		print_r($floor_colspan);
		echo "</pre>";*/

		//for main query
		$sql_receive = "SELECT A.COMPANY_ID, A.SUPPLIER_ID, A.YARN_COUNT_ID, A.YARN_COMP_TYPE1ST, A.YARN_COMP_PERCENT1ST, A.YARN_COMP_TYPE2ND, A.YARN_COMP_PERCENT2ND, A.YARN_TYPE, A.COLOR, A.LOT, B.STORE_ID, B.FLOOR_ID, B.ROOM,
		SUM(CASE WHEN B.TRANSACTION_TYPE IN (1,4,5) AND B.TRANSACTION_DATE <= '".$from_date."' THEN B.CONS_QUANTITY ELSE 0 END) AS RCV_TOTAL,
		SUM(CASE WHEN B.TRANSACTION_TYPE IN (1,4,5) AND B.TRANSACTION_DATE <= '".$from_date."' THEN B.CONS_AMOUNT ELSE 0 END) AS RCV_TOTAL_AMT,
		SUM(CASE WHEN B.TRANSACTION_TYPE IN (2,3,6) AND B.TRANSACTION_DATE <= '".$from_date."' THEN B.CONS_QUANTITY ELSE 0 END) AS ISSUE_TOTAL,
		SUM(CASE WHEN B.TRANSACTION_TYPE IN (2,3,6) AND B.TRANSACTION_DATE <= '".$from_date."' THEN B.CONS_AMOUNT ELSE 0 END) AS ISSUE_TOTAL_AMT
		FROM PRODUCT_DETAILS_MASTER A, INV_TRANSACTION B
		WHERE A.ID = B.PROD_ID AND B.TRANSACTION_TYPE IN (1,2,3,4,5,6) AND B.ITEM_CATEGORY = 1 AND A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0".$company_cond.$search_cond." AND B.CONS_QUANTITY > 0
		GROUP BY A.COMPANY_ID, A.SUPPLIER_ID, A.YARN_COUNT_ID, A.YARN_COMP_TYPE1ST, A.YARN_COMP_PERCENT1ST, A.YARN_COMP_TYPE2ND, A.YARN_COMP_PERCENT2ND, A.YARN_TYPE, A.COLOR, A.LOT, B.STORE_ID, B.FLOOR_ID, B.ROOM";
		//echo $sql_receive; die;
		$result_sql_receive = sql_select($sql_receive);
		$data_arr = array();
		foreach ($result_sql_receive as $row)
		{
			$compositionDetails = $composition[$row['YARN_COMP_TYPE1ST']] . " " . $row['YARN_COMP_PERCENT1ST'];

			$data_arr[$row['YARN_COUNT_ID']][$compositionDetails][$row['YARN_TYPE']][$row['COLOR']][$row['LOT']][$row['SUPPLIER_ID']][$row['STORE_ID']][$row['COMPANY_ID']][$row['FLOOR_ID']][$row['ROOM']]['RCV_TOTAL'] += $row['RCV_TOTAL'];
			$data_arr[$row['YARN_COUNT_ID']][$compositionDetails][$row['YARN_TYPE']][$row['COLOR']][$row['LOT']][$row['SUPPLIER_ID']][$row['STORE_ID']][$row['COMPANY_ID']][$row['FLOOR_ID']][$row['ROOM']]['ISSUE_TOTAL'] += $row['ISSUE_TOTAL'];

			$data_arr[$row['YARN_COUNT_ID']][$compositionDetails][$row['YARN_TYPE']][$row['COLOR']][$row['LOT']][$row['SUPPLIER_ID']][$row['STORE_ID']][$row['COMPANY_ID']][$row['FLOOR_ID']][$row['ROOM']]['RCV_TOTAL_AMT'] += $row['RCV_TOTAL_AMT'];
			$data_arr[$row['YARN_COUNT_ID']][$compositionDetails][$row['YARN_TYPE']][$row['COLOR']][$row['LOT']][$row['SUPPLIER_ID']][$row['STORE_ID']][$row['COMPANY_ID']][$row['FLOOR_ID']][$row['ROOM']]['ISSUE_TOTAL_AMT'] += $row['ISSUE_TOTAL_AMT'];
		}
		unset($result_sql_receive);
		/*echo "<pre>";
		print_r($data_arr);
		echo "</pre>";*/

		$width = (1100+($td_width));
		ob_start();
		?>
		<fieldset style="width:<? echo $width+20; ?>px;margin:5px auto;" id="fieldsetId">
			<table width="<? echo $width+20; ?>" cellspacing="0" class="rpt_table" rules="all" id="table_header_1">
				<thead>
					<tr class="form_caption" style="border:none;">
						<td colspan="16" align="center" style="border:none;font-size:16px; font-weight:bold">Location Wise Yarn Stock</td>
					</tr>
					<!--<tr style="border:none;">
						<td colspan="16" align="center" style="border:none; font-size:14px;">
							<? echo ($cbo_company_name == '')?"All Company":$companyArr[str_replace("'", "", $cbo_company_name)]; ?>
						</td>
					</tr>-->
					<tr style="border:none;">
						<td colspan="16" align="center" style="border:none;font-size:12px; font-weight:bold">
							<? if ($txt_date_from != "" && $txt_date_from != "") echo "From " . change_date_format($txt_date_from, 'dd-mm-yyyy') . " To " . change_date_format($txt_date_from, 'dd-mm-yyyy') . ""; ?>
						</td>
					</tr>
					<tr>
						<th rowspan="3" width="40">SL</th>
						<th colspan="7">Description</th>
                        <!--for company print-->
						<?
						foreach ($company_id_arr as $company_id=>$company_row)
						{
							?>
							<th colspan="<? echo $company_colspan[$company_id]; ?>"><? echo $companyArr[$company_row];?></th>
							<?
						}
						?>
						<th rowspan="3" width="100">Group Total Qnty.</th>
						<th rowspan="3">Group Total Value</th>
					</tr>
					<tr>
						<th rowspan="2" width="70">Count</th>
						<th rowspan="2" width="180">Composition</th>
						<th rowspan="2" width="80">Yarn Type</th>
						<th rowspan="2" width="100">Color</th>
						<th rowspan="2" width="80">Lot</th>
						<th rowspan="2" width="150">Supplier</th>
						<th rowspan="2" width="150">Store</th>
                        <!--for floor print-->
						<?
						foreach ($company_id_arr as $company_id=>$company_row)
						{
							if(!empty($floor_data[$company_id]))
							{
								foreach ($floor_data[$company_id] as $key=>$val)
								{
									?>
									<th colspan="<? echo $floor_colspan[$company_id][$key]; ?>" width="100" style="word-break:break-all;"><? echo $val; ?></th>
									<?
								}
							}
							else
							{
								?>
								<th width="100" style="word-break:break-all;"></th>
								<?
							}
						}
						?>
					</tr>
					<tr>
						<!--for room print-->
						<?
						foreach ($company_id_arr as $company_id=>$company_row)
						{
							if(!empty($floor_data[$company_id]))
							{
								foreach ($floor_data[$company_id] as $flr=>$flr_arr)
								{
									if(!empty($room_data[$company_id][$flr]))
									{
										foreach ($room_data[$company_id][$flr] as $key=>$val)
										{
											?>
											<th width="100" style="word-break:break-all;"><? echo $val; ?></th>
											<?
										}
									}
									else
									{
										?>
										<th width="100" style="word-break:break-all;"></th>
										<?
									}
								}
							}
							else
							{
								?>
								<th width="100" style="word-break:break-all;"></th>
								<?
							}
						}
						?>
					</tr>
				</thead>
			</table>
			<div style="max-height:425px; overflow-y:auto; width:<? echo $width+20; ?>px;" id="scroll_body">
				<table class="rpt_table" border="1" width="<? echo $width; ?>" rules="all" id="table_body">
					<tbody>
						<?
						$i = 0;
						$print_data_arr = array();
						$grand_total_arr = array();
						foreach($data_arr as $count_id=>$count_data)
						{
							foreach($count_data as $comp_dtls=>$comp_data)
							{
								foreach($comp_data as $y_type=>$type_data)
								{
									foreach($type_data as $color_id=>$color_data)
									{
										foreach($color_data as $lot_no=>$lot_data)
										{
											foreach($lot_data as $supl_id=>$supl_data)
											{
												foreach($supl_data as $store_id=>$store_data)
												{
													$i++;
													$print_data_arr[$i]['count_id'] = $yarn_count_arr[$count_id];
													$print_data_arr[$i]['comp_dtls'] = $comp_dtls;
													$print_data_arr[$i]['y_type'] = $yarn_type[$y_type];
													$print_data_arr[$i]['color_id'] = $color_name_arr[$color_id];
													$print_data_arr[$i]['supplier_id'] = $supplierArr[$supl_id];
													$print_data_arr[$i]['lot_no'] = $lot_no;
													$print_data_arr[$i]['store_id'] = $storre_name_arr[$store_id];

													foreach ($company_id_arr as $company_id=>$company_row)
													{
														if(!empty($floor_data[$company_id]))
														{
															foreach ($floor_data[$company_id] as $flr=>$flr_arr)
															{
																if(!empty($room_data[$company_id][$flr]))
																{
																	foreach($room_data[$company_id][$flr] as $rm=>$rmv)
																	{
																		if(!empty($store_data[$company_id][$flr][$rm]))
																		{
																			$rm_balance_qty = 0;
																			$rm_balance_amt = 0;
																			$rm_balance_qty = $store_data[$company_id][$flr][$rm]['RCV_TOTAL']-$store_data[$company_id][$flr][$rm]['ISSUE_TOTAL'];
																			$rm_balance_amt = $store_data[$company_id][$flr][$rm]['RCV_TOTAL_AMT']-$store_data[$company_id][$flr][$rm]['ISSUE_TOTAL_AMT'];
																			$print_data_arr[$i]['balance_qty'][] = number_format($rm_balance_qty, 2, '.', '');
																			$print_data_arr[$i]['group_sub_total'] += number_format($rm_balance_qty, 2, '.', '');
																			$print_data_arr[$i]['group_sub_total_amt'] += number_format($rm_balance_amt, 2, '.', '');

																			//for grand total
																			$grand_total_arr[$company_id][$flr][$rm]['balance_qty'] += number_format($rm_balance_qty, 2, '.', '');
																			$grand_group_sub_total += number_format($rm_balance_qty, 2, '.', '');
																			$grand_group_sub_total_amt += number_format($rm_balance_amt, 2, '.', '');

																		}
																		else
																		{
																			$print_data_arr[$i]['balance_qty'][] = 0.00;
																			$print_data_arr[$i]['group_sub_total'] += 0;
																			$print_data_arr[$i]['group_sub_total_amt'] += 0;
																		}
																	}
																}
																else
																{
																	$print_data_arr[$i]['balance_qty'][] = 0.00;
																	$print_data_arr[$i]['group_sub_total'] += 0;
																	$print_data_arr[$i]['group_sub_total_amt'] += 0;
																}
															}
														}
														else
														{
															$print_data_arr[$i]['balance_qty'][] = 0.00;
															$print_data_arr[$i]['group_sub_total'] += 0;
															$print_data_arr[$i]['group_sub_total_amt'] += 0;
														}
													}
												}
											}
										}
									}
								}
							}
						}

						/*
						|----------------------------------------------------------
						| checking with and without zero value
						|----------------------------------------------------------
						*/
						$zs = 0;
						$p_data_arr = array();
						foreach($print_data_arr as $key=>$val)
						{
							if($value_with == 1)
							{
								if($val['group_sub_total'] > 0)
								{
									$zs++;
									$p_data_arr[$zs] = $val;
								}
							}
							else
							{
								$zs++;
								$p_data_arr[$zs] = $val;
							}
						}
						//end checking with and without zero value

						$sl = 0;
						foreach($p_data_arr as $row)
						{
							$sl++;
							if($sl%2==0) $bgcolor="#E9F3FF";
							else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
								<td align="center" width="40"><? echo $sl; ?></td>
								<td width="70" style="word-break:break-all;"><p><? echo $row['count_id']; ?></p></td>
								<td width="180" style="word-break:break-all;"><p><? echo $row['comp_dtls']; ?></p></td>
								<td width="80" style="word-break:break-all;"><p><? echo $row['y_type']; ?></p></td>
								<td width="100" style="word-break:break-all;"><p><? echo $row['color_id']; ?></p></td>
								<td width="80" style="word-break:break-all;"><p><? echo $row['lot_no']; ?></p></td>
								<td width="150" style="word-break:break-all;"><? echo $row['supplier_id']; ?></td>
								<td width="150" style="word-break:break-all;"><? echo $row['store_id']; ?></td>
								<?
								foreach($row['balance_qty'] as $rm_qty)
								{
									?>
									<td width="100" align="right" style="word-break:break-all;"><? echo number_format($rm_qty, 2); ?></td>
									<?
								}
								?>
								<td width="100" align="right"><? echo number_format($row['group_sub_total'],2,".",""); ?></td>
								<td align="right"><? echo number_format($row['group_sub_total_amt'],2,".",""); ?></td>
							</tr>
							<?
						}
						?>
					</tbody>
				</table>
			</div>
            <table class="rpt_table" border="1" width="<? echo $width; ?>" rules="all" id="table_body">
                <tfoot>
                    <tr>
                        <th width="40"></th>
                        <th width="70"></th>
                        <th width="180"></th>
                        <th width="80"></th>
                        <th width="100"></th>
                        <th width="80"></th>
                        <th width="150"></th>
                        <th width="150">Toral</th>
                        <?
                        foreach ($company_id_arr as $company_id=>$company_row)
                        {
                            if(!empty($floor_data[$company_id]))
                            {
                                foreach ($floor_data[$company_id] as $flr=>$flr_arr)
                                {
                                    if(!empty($room_data[$company_id][$flr]))
                                    {
                                        foreach($room_data[$company_id][$flr] as $rm=>$rmv)
                                        {
                                            if(!empty($grand_total_arr[$company_id][$flr][$rm]['balance_qty']))
                                            {
                                                ?>
                                                <th width="100"><? echo number_format($grand_total_arr[$company_id][$flr][$rm]['balance_qty'], 2, '.', ''); ?></th>
                                                <?
                                            }
                                            else
                                            {
                                                ?>
                                                <th width="100">0.00</th>
                                                <?
                                            }
                                        }
                                    }
                                    else
                                    {
                                        ?>
                                        <th width="100">0.00</th>
                                        <?
                                    }
                                }
                            }
                            else
                            {
                                ?>
                                <th width="100">0.00</th>
                                <?
                            }
                        }
                        ?>
                        <th width="100"><? echo number_format($grand_group_sub_total, 2, '.', ''); ?></th>
                        <th><? echo number_format($grand_group_sub_total_amt, 2, '.', ''); ?></th>
                    </tr>
                </tfoot>
            </table>
		</fieldset>
		<?

	}
	exit();
}
?>