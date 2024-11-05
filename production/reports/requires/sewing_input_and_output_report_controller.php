<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  );

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.id=b.buyer_id and b.tag_company=$data and a.status_active=1 and a.is_deleted=0 order by a.buyer_name","id,buyer_name", 1, "-- Select buyer --", 0, "","" );//load_drop_down( 'requires/daily_knitting_production_report_controller',document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_machine', 'machine_td' );$location_cond
  exit();
}
if($db_type==0) $insert_year="SUBSTRING_INDEX(a.insert_date, '-', 1)";
if($db_type==2) $insert_year="extract( year from b.insert_date)";
//item style------------------------------//

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 130, "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id=$data group by id,location_name  order by location_name","id,location_name", 1, "-- Select location --", $selected, "" );
	exit();
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor_name", 130, "SELECT id,floor_name from lib_prod_floor where location_id=$data and status_active =1 and is_deleted=0 and production_process in(4,5) group by id,floor_name order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "" );
	exit();
}

if($action=="job_popup")
{
	echo load_html_head_contents("Search Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

	<script>

		var selected_id = new Array; var selected_name = new Array;var selected_style = new Array;var selected_id_arr = new Array;

		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click');
			}
		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( str ) {

			if (str!="") str=str.split("_");

			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );

			if( jQuery.inArray( str[1], selected_id ) == -1 ) {
				selected_id_arr.push( str[0] );
				selected_id.push( str[1] );
				selected_name.push( str[2] );
				selected_style.push( str[3] );

			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				selected_style.splice( i, 1 );
			}
			// alert(selected_id.join(','));

			var id = ''; var name = '';var style = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
				style += selected_style[i] + '*';
			}

			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			style = style.substr( 0, style.length - 1 );


			$('#hide_job_id').val( id );
			$('#hide_job_no').val( name );
			$('#hide_style_no').val( style );
		}

    </script>

	</head>

	<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:710px;">
	            <table width="700" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
	            	<thead>
	                    <th>Buyer</th>
	                    <th>Year</th>
	                    <th>Search By</th>
	                    <th id="search_by_td_up" width="100">Job No</th>
	                    <th>
                            <input type="reset" name="button" class="formbutton" value="Reset"  style="width:80px;">
                            <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                            <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                            <input type="hidden" name="hide_style_no" id="hide_style_no" value="" />
                        </th>
	                </thead>
	                <tbody>
	                	<tr>
	                        <td align="center">
	                        	 <?
									echo create_drop_down( "cbo_buyer_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company_name $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",0,"",0 );
								?>
	                        </td>
	                        <td align="center">
	                    	<?
								echo create_drop_down( "cbo_year", 110, $year,"",1, "--Select--", "",'',0 );
							?>
	                        </td>
	                        <td align="center">
	                    	<?
	                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref");
								$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
								echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
	                        </td>
	                        <td align="center" id="search_by_td">
	                            <input type="text" style="width:100px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
	                        </td>
	                        <td align="center">
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company_name; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('cbo_year').value, 'search_list_view', 'search_div', 'sewing_input_and_output_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');check_all_data();" style="width:80px;" />
	                    	</td>
	                    </tr>
	            	</tbody>
	           	</table>
	            <div style="margin-top:15px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
	</body>
	<script type="text/javascript">
		$("#cbo_year").val('<?=$cbo_year;?>');
	</script>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];

	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$data[1]";
	}

	$search_by=$data[2];
	if(str_replace("'", "", $data[3])!="")
	{
		$search_string="".trim($data[3])."";
	}
	else
	{

		//<div class="alert alert-danger">Please enter job or style no to search.</div>

		//die;
	}

	if($search_by==1)
		$search_field="a.job_no_prefix_num";
	else if($search_by==2)
		$search_field="a.style_ref_no";
	$search_cond="";
	if($search_string!="")	{$search_cond=" and $search_field='$search_string'";}
	$job_year =$data[4];

	if($job_year!=0)
	{
		if($db_type==0)
		{
			$job_year_cond=" and year(a.insert_date)='$job_year'";
		}
		else
		{
			$job_year_cond=" and to_char(a.insert_date,'YYYY')='$job_year'";
		}
	}
	else
	{
		$job_year_cond="";
	}
	$company_library=return_library_array( "SELECT id, company_name from lib_company", "id", "company_name"  );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$arr=array (0=>$company_library,1=>$buyer_arr);

	if($db_type==0) $year_field="YEAR(a.insert_date) as year";
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";


	$sql= "SELECT b.id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,b.po_number from wo_po_details_master a,wo_po_break_down b where a.id=b.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id $search_cond $buyer_id_cond $job_no_cond $job_year_cond group by b.id,
         a.job_no, a.insert_date, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,b.po_number order by a.job_no desc";
    // echo $sql;
    $result = sql_select($sql);

	// echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No,Order No", "100,100,50,100,100","650","220",0, $sql , "js_set_value", "id,job_no,style_ref_no","",1,"company_name,buyer_name,0,0,0,0,0",$arr,"company_name,buyer_name,year,job_no,style_ref_no,po_number","",'','0,0,0,0,0,0','',1) ;
	?>
	<table class="rpt_table" id="rpt_tabletbl_list_search" rules="all" width="660" cellspacing="0" cellpadding="0" border="0">
		<thead>
			<tr>
				<th width="50">SL No</th>
				<th width="100">Company</th>
				<th width="100">Buyer Name</th>
				<th width="50">Year</th>
				<th width="100">Job No</th>
				<th width="100">Style Ref. No</th>
				<th width="100">Order No</th>
			</tr>
		</thead>
	</table>
	<div style="max-height:220px; width:668px; overflow-y:scroll" id="">
		<table style="word-break: break-all;" class="rpt_table" id="tbl_list_search" rules="all" width="660" cellpadding="0" border="1">
			<tbody>
				<?
				$i = 1;
				foreach ($result as $val)
				{
					$set_data = $i."_".$val['ID']."_".$val['JOB_NO']."_".$val['STYLE_REF_NO'];
					?>
					<tr onClick="js_set_value('<?=$set_data;?>')" style="cursor: pointer;" id="tr_<?=$i;?>" height="20" bgcolor="#FFFFFF">

						<td width="50"><?=$i;?></td>
						<td width="100"><?=$company_library[$val['COMPANY_NAME']];?></td>
						<td width="100"><?=$buyer_arr[$val['BUYER_NAME']];?></td>
						<td width="50"><?=$val['YEAR'];?></td>
						<td width="100"><?=$val['JOB_NO'];?></td>
						<td width="100"><?=$val['STYLE_REF_NO'];?></td>
						<td width="100"><?=$val['PO_NUMBER'];?></td>
					</tr>
					<?
					$i++;
				}
				?>
			</tbody>
		</table>
	</div>
	<div class="check_all_container">
		<div style="width:100%">
			<div style="width:50%; float:left" align="left">
				<input type="checkbox" name="check_all" id="check_all" checked="true" onClick="check_all_data()"> Check / Uncheck All
			</div>
			<div style="width:50%; float:left" align="left">
				<input type="button" name="close" id="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px">
			</div>
		</div>
	</div>
	<?
   exit();
}

if($action=="color_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	?>
    <script>
    	// var txt_order_id = $("#txt_order_id").val(); alert(txt_order_id);

		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
			}
		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( strCon )
		{
			// alert(strCon);
			var splitSTR = strCon.split("_");
			var str_or = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];
			//$('#txt_individual_id' + str).val(splitSTR[1]);
			//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');

			toggle( document.getElementById( 'tr_' + str_or ), '#FFFFCC' );

			if( jQuery.inArray( selectID, selected_id ) == -1 ) {
				selected_id.push( selectID );
				selected_name.push( selectDESC );
				selected_no.push( str_or );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == selectID ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				selected_no.splice( i, 1 );
			}
			var id = ''; var name = ''; var job = ''; var num='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
				num += selected_no[i] + ',';
			}
			id 		= id.substr( 0, id.length - 1 );
			name 	= name.substr( 0, name.length - 1 );
			num 	= num.substr( 0, num.length - 1 );
			//alert(num);
			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name );
			// $('#txt_selected_no').val( num );
		}
    </script>
    <?
	$job_id=str_replace("'","",$txt_job_id);

	$job_id_arr = explode(",", $job_id);
	if(count($job_id_arr)>999 && $db_type==2)
    {
     	$po_chunk=array_chunk($job_id_arr, 999);
     	$job_ids_cond= "";
     	foreach($po_chunk as $vals)
     	{
     		$imp_ids=implode(",", $vals);
     		if($job_ids_cond=="")
     		{
     			$job_ids_cond.=" and ( c.po_break_down_id in ($imp_ids) ";
     		}
     		else
     		{
     			$job_ids_cond.=" or c.po_break_down_id in ($imp_ids) ";
     		}
     	}
     	 $job_ids_cond.=" )";
    }
    else
    {
     	$job_ids_cond= " and c.po_break_down_id in($job_id) ";
    }

    $company_library=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
    $arr=array(0=>$color_arr);
    // print_r($arr);

	$sql = "SELECT c.color_number_id,d.color_name from wo_po_color_size_breakdown c,lib_color d  where d.id=c.color_number_id and c.status_active in(1,2,3) $job_ids_cond group by c.color_number_id,d.color_name";
	// echo $sql; die;
	echo create_list_view("list_view", "Color Name","200","230","310",0, $sql , "js_set_value", "color_number_id,color_name", "", 1, "color_number_id", $arr, "color_number_id", "","setFilterGrid('list_view',-1)","0","",1) ;

	// echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Style Ref. No, Po No, Cut No.", "120,100,100,100,140,140","740","290",0, $sql , "js_set_value", "job_no,style_ref_no,po_number,cut_no","",1,"company_name,buyer_name,0,0,0,0,0",$arr,"company_name,buyer_name,job_no,style_ref_no,po_number,cut_no","",'','0,0,0,0,0,0','',1) ;

	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";

	?>
    <script language="javascript" type="text/javascript">
	/*var style_no='<? echo $txt_order_id_no;?>';
	var style_id='<? echo $txt_order_id;?>';
	var style_des='<? echo $txt_order;?>';
	//alert(style_id);
	if(style_no!="")
	{
		style_no_arr=style_no.split(",");
		style_id_arr=style_id.split(",");
		style_des_arr=style_des.split(",");
		var str_ref="";
		for(var k=0;k<style_no_arr.length; k++)
		{
			str_ref=style_no_arr[k]+'_'+style_id_arr[k]+'_'+style_des_arr[k];
			js_set_value(str_ref);
		}
	}*/
	</script>

    <?
	exit();
}

if($action=="floor_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	?>
    <script>
    	// var txt_order_id = $("#txt_order_id").val(); alert(txt_order_id);

		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
			}
		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( strCon )
		{
			// alert(strCon);
			var splitSTR = strCon.split("_");
			var str_or = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];
			//$('#txt_individual_id' + str).val(splitSTR[1]);
			//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');

			toggle( document.getElementById( 'tr_' + str_or ), '#FFFFCC' );

			if( jQuery.inArray( selectID, selected_id ) == -1 ) {
				selected_id.push( selectID );
				selected_name.push( selectDESC );
				selected_no.push( str_or );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == selectID ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				selected_no.splice( i, 1 );
			}
			var id = ''; var name = ''; var job = ''; var num='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
				num += selected_no[i] + ',';
			}
			id 		= id.substr( 0, id.length - 1 );
			name 	= name.substr( 0, name.length - 1 );
			num 	= num.substr( 0, num.length - 1 );
			//alert(num);
			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name );
			// $('#txt_selected_no').val( num );
		}
    </script>
    <?
	$sql = "SELECT id,floor_name from lib_prod_floor where status_active in(1) and location_id=$location_name and production_process in(5) group by id,floor_name order by floor_name";
	// echo $sql; die;
	echo create_list_view("list_view", "Floor Name","200","230","310",0, $sql , "js_set_value", "id,floor_name", "", 1, "",
		"", "floor_name", "","setFilterGrid('list_view',-1)","0","",1) ;

	// echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Style Ref. No, Po No, Cut No.", "120,100,100,100,140,140","740","290",0, $sql , "js_set_value", "job_no,style_ref_no,po_number,cut_no","",1,"company_name,buyer_name,0,0,0,0,0",$arr,"company_name,buyer_name,job_no,style_ref_no,po_number,cut_no","",'','0,0,0,0,0,0','',1) ;

	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";

	?>
    <?
	exit();
}

if($action=="line_search_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1,$unicode,1);
	?>
	<script>
		var selected_id = new Array;
		var selected_name = new Array;

    	function check_all_data() {

			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ )
			 {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
			 }
		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( strCon )
		{
			//alert(strCon)
			var splitSTR = strCon.split("_");
			var str = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];
			//$('#txt_individual_id' + str).val(splitSTR[1]);
			//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');

			toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );

			if( jQuery.inArray( selectID, selected_id ) == -1 ) {
				selected_id.push( selectID );
				selected_name.push( selectDESC );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == selectID ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = ''; var job = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id 		= id.substr( 0, id.length - 1 );
			name 	= name.substr( 0, name.length - 1 );

			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name );
		}

		function set_all_data() {

			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ )
			 {

				if(($('#hidden_old_id' + i).val()*1)==1)
				{
					var onclickString = $('#tr_' + i).attr('onclick');
					var paramArr = onclickString.split("'");
					var functionParam = paramArr[1];
					js_set_value( functionParam );
				}
			 }
		}


		function fn_onClosed()
		{
			parent.emailwindow.hide();
		}
    </script>
	<?
	extract($_REQUEST);
	//echo $company;die;
	$line_library=return_library_array( "SELECT id,line_name from lib_sewing_line", "id", "line_name");
	if($company==0) $company_name=""; else $company_name=" and b.company_name in($wo_company_name)";//job_no

		$line_array=array();
		if($date_from=="" && $date_to=="")
		{
			$data_format="";
		}
		else
		{
			if($db_type==0)	$data_format=" and b.pr_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
			if($db_type==2)	$data_format=" and b.pr_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
		}
		//if( $location_name!=0 ) $cond .= " and a.location_id in($location_name)";
		//if( $floor_name!=0 ) $cond.= " and a.floor_id in($floor_name)";

		$variable_line_setting=return_field_value("auto_update","variable_settings_production","variable_list=23 and company_name=$wo_company_name");

		if($variable_line_setting==1){

			if( $location_name!=0 ) $cond .= " and a.location_id in($location_name)";

			if( $floor_name!=0 ) $cond.= " and a.floor_id in($floor_name)";

			$line_sql="SELECT a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id $data_format and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number";
		}
		else{

			if( $location_name!=0 ) $cond .= " and a.location_name in($location_name)";

			if( $floor_name!=0 ) $cond.= " and a.floor_name in($floor_name)";

			$line_sql="SELECT a.id, a.line_name from lib_sewing_line a, lib_company b, lib_location c where a.company_name=b.id and a.location_name=c.id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_name order by a.line_name";
		}
		// echo $line_sql;
		$line_sql_result=sql_select($line_sql);

		?>
            <input type='hidden' id='txt_selected_id' />
            <input type='hidden' id='txt_selected' />
            <table cellspacing="0" width="230"  border="1" rules="all" class="rpt_table" >
            	<thead>
                	<th width="30"></th>
                    <th width="200">Line Name</th>
                </thead>
            </table>
            <?php
            	if($variable_line_setting==1){

            ?>
            <div style="width:250px; max-height:300px; overflow-y:auto;" id="scroll_body" >
        		<table cellspacing="0" width="230"  border="1" rules="all" class="rpt_table" id="list_view" >
                <?

				$i=1;
				$previous_line_arr=explode(",",$line_id);
				 foreach($line_sql_result as $row)
				 {
        			 $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					 $flag=0;
					 if(in_array($row[csf('id')],$previous_line_arr))
					 {
						 $flag=1;
					 }

					$line_val='';
					$line_id=explode(",",$row[csf('line_number')]);
					foreach($line_id as $line_id)
					{
						if($line_val=="") $line_val=$line_library[$line_id]; else $line_val.=','.$line_library[$line_id];
					}
					?>
                	<tr bgcolor="<? echo $bgcolor ; ?>" id="tr_<? echo $i; ?>" onClick="js_set_value('<? echo $i.'_'.$row[csf('id')].'_'.$line_val; ?>')" style="cursor:pointer;">
                    	<td width="30"><? echo $i;?></td>
                        <td width="200">
						<? echo $line_val;?>
                        <input type="hidden" id="hidden_old_id<? echo $i; ?>" name="hidden_old_id<? echo $i; ?>" value="<?php echo $flag; ?>" />
                        </td>
                    </tr>
                 <?
				 $i++;
				 }
				 ?>
              </table>
           </div>
	       <?php
	   			}
	   			else{
	       ?>
		       <div style="width:250px; max-height:300px; overflow-y:auto;" id="scroll_body" >
	        		<table cellspacing="0" width="230"  border="1" rules="all" class="rpt_table" id="list_view" >
	                <?

					$i=1;
					$previous_line_arr=explode(",",$line_id);
					 foreach($line_sql_result as $row)
					 {
	        			 $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
						 $flag=0;
						 if(in_array($row[csf('id')],$previous_line_arr))
						 {
							 $flag=1;
						 }

						$line_val='';
						$line_id=explode(",",$row[csf('id')]);
						foreach($line_id as $line_id)
						{
							if($line_val=="") $line_val=$line_library[$line_id]; else $line_val.=','.$line_library[$line_id];
						}
						?>
	                	<tr bgcolor="<? echo $bgcolor ; ?>" id="tr_<? echo $i; ?>" onClick="js_set_value('<? echo $i.'_'.$row[csf('id')].'_'.$line_val; ?>')" style="cursor:pointer;">
	                    	<td width="30"><? echo $i;?></td>
	                        <td width="200">
							<? echo $line_val;?>
	                        <input type="hidden" id="hidden_old_id<? echo $i; ?>" name="hidden_old_id<? echo $i; ?>" value="<?php echo $flag; ?>" />
	                        </td>
	                    </tr>
	                 <?
					 $i++;
					 }
					 ?>
	              </table>
	           </div>
	       	<?php } ?>
        <table width="230">
            <tr align="center">
                <td>
            		<div align="left" style="width:50%; float:left">
                        <input id="check_all" type="checkbox" onClick="check_all_data()" name="check_all">
                            Check / Uncheck All
                    </div>
                    <div align="left" style="width:50%; float:left">
                        <input type="button" name="btn_close" class="formbutton" style="width:100px" value="Close" onClick="fn_onClosed()" />
                    </div>
               </td>
            </tr>
        </table>
         <script>
			set_all_data();
			setFilterGrid("list_view",-1);
		</script>
        <?

	exit();
}

if($action=="generate_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_name 		= str_replace("'","",$cbo_company_name);
	$year 				= str_replace("'","",$cbo_year);
	$hidden_po_id 		= str_replace("'","",$hidden_job_id);
	$hidden_color_id 	= str_replace("'","",$hidden_color_id);
	$wo_company_name 	= str_replace("'","",$cbo_wo_company_name);
	$location_name 		= str_replace("'","",$cbo_location_name);
	$hidden_floor_id	= str_replace("'","",$hidden_floor_id);
	$hidden_line_id 	= str_replace("'","",$hidden_line_id);
	$int_ref 			= str_replace("'","",$txt_int_ref);
	// echo "Hello- ". $int_ref; die;

	$sql_cond .= ($company_name != 0) 		? " and a.company_name=$company_name" : "";
	$sql_cond .= ($hidden_po_id != "") 		? " and b.id in($hidden_po_id)" : "";
	$sql_cond .= ($hidden_color_id != "") 	? " and c.color_number_id in($hidden_color_id)" : "";
	$sql_cond .= ($wo_company_name != 0) 	? " and d.serving_company=$wo_company_name" : "";
	$sql_cond .= ($location_name != 0) 		? " and d.location=$location_name" : "";
	$sql_cond .= ($hidden_floor_id != "") 	? " and d.floor_id in($hidden_floor_id)" : "";
	$sql_cond .= ($hidden_line_id != "") 	? " and d.sewing_line in($hidden_line_id)" : "";
	$sql_cond .= ($year != 0) 				? " and to_char(a.insert_date,'YYYY')='$year'" : "";
	$sql_cond .= ($int_ref != "") 			? " and b.grouping = '$int_ref'" : "";
	// echo $sql_cond;


	$lineArr = return_library_array("select a.id,a.line_name from lib_sewing_line a","id","line_name");
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
    $season_library = return_library_array("select id, season_name from lib_buyer_season where status_active =1 and is_deleted=0", "id", "season_name");
    $buyer_library  = return_library_array("select id, buyer_name from  lib_buyer", "id", "buyer_name");

	/*==========================================================================================/
	/									getting gmts prod data 									/
	/==========================================================================================*/
if($type==1)
{
	$sql=" SELECT a.id as job_id, a.job_no, a.buyer_name, a.style_ref_no, a.season_buyer_wise, (a.job_quantity*a.total_set_qnty) as job_quantity, a.total_set_qnty, b.id as po_id, b.po_number, b.grouping, b.shipment_date, b.pub_shipment_date, c.size_order, c.item_number_id as item_id, c.color_number_id as color_id, c.size_number_id as size_id, c.order_quantity, d.floor_id, d.prod_reso_allo, d.sewing_line, d.production_type, d.production_date, e.production_qnty,e.reject_qty

		from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e
		where a.id=b.job_id and b.id=c.po_break_down_id and a.id=c.job_id and b.id=d.po_break_down_id and c.id=e.color_size_break_down_id and d.id=e.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and d.production_type in(4,5) and e.production_qnty>0 $sql_cond order by c.size_order,d.production_date";
	//   echo $sql;die;
	$sql_res = sql_select($sql);
    if(count($sql_res)==0)
    {
        ?>
        <center>
            <div style="width: 80%;" class="alert alert-danger">Data not found.Please try again.</div>
        </center>
        <?
        die();
    }

	$data_array = array();
	$size_arr = array();
	$job_size_qty_arr = array();
	$job_color_size_qty_arr = array();
	$color_wise_order_arr = array();
	$size_chk_arr = array();
	$color_chk_arr = array();
	$job_data_arr = array();
	$job_id_array = array();
	$job_no_array = array();
	$reject_array=array();
	foreach ($sql_res as $val)
	{
		$job_data_arr[$val['JOB_NO']]['style'] = $val['STYLE_REF_NO'];
		$job_data_arr[$val['JOB_NO']]['buyer'] = $val['BUYER_NAME'];
		$job_data_arr[$val['JOB_NO']]['season'] = $val['SEASON_BUYER_WISE'];
		$job_data_arr[$val['JOB_NO']]['item_id'] = $val['ITEM_ID'];
		$job_data_arr[$val['JOB_NO']]['grouping'] = $val['GROUPING'];
		$job_data_arr[$val['JOB_NO']]['job_quantity'] = $val['JOB_QUANTITY'];
		$job_data_arr[$val['JOB_NO']]['total_set_qty'] = $val['TOTAL_SET_QNTY'];
		$job_data_arr[$val['JOB_NO']]['pub_shipment_date'].=','.$val['PUB_SHIPMENT_DATE'];

		// $size_arr[$val['SIZE_ID']] = $val['SIZE_ID'];
		/*if($size_chk_arr[$val['PO_ID']][$val['ITEM_ID']][$val['COLOR_ID']][$val['SIZE_ID']]=="")
		{
			$job_size_qty_arr[$val['SIZE_ID']] += $val['ORDER_QUANTITY'];
			$size_chk_arr[$val['PO_ID']][$val['ITEM_ID']][$val['COLOR_ID']][$val['SIZE_ID']] = $val['SIZE_ID'];
		}*/

		if($color_chk_arr[$val['PO_ID']][$val['COLOR_ID']]=="")
		{
			$color_wise_order_arr[$val['COLOR_ID']] .= $val['PO_NUMBER']." (".change_date_format($val['SHIPMENT_DATE'])."),";
			$color_chk_arr[$val['PO_ID']][$val['COLOR_ID']] = $val['COLOR_ID'];
		}
		if($val["PROD_RESO_ALLO"]==1)
		{
			$line_resource_mst_arr=explode(",",$prod_reso_arr[$val['SEWING_LINE']]);
			$line_name="";
			foreach($line_resource_mst_arr as $resource_id)
			{
				$line_name .= ($line_name == "") ? $lineArr[$resource_id] : ",".$lineArr[$resource_id];
			}
		}
		else
		{
			$line_name=$lineArr[$val['SEWING_LINE']];
		}
		$data_array[$val['JOB_NO']][$val['COLOR_ID']][$floor_library[$val['FLOOR_ID']]][$val['ITEM_ID']][$line_name][$val['PRODUCTION_TYPE']][$val['SIZE_ID']]['qty'] += $val['PRODUCTION_QNTY'];
		$date_data_array[$val['JOB_NO']][$val['COLOR_ID']][$floor_library[$val['FLOOR_ID']]][$val['ITEM_ID']][$line_name][strtotime($val['PRODUCTION_DATE'])][$val['PRODUCTION_TYPE']][$val['SIZE_ID']]['qty'] += $val['PRODUCTION_QNTY'];
		$data_array[$val['JOB_NO']][$val['COLOR_ID']][$floor_library[$val['FLOOR_ID']]][$val['ITEM_ID']][$line_name]['line_id'] = $val['SEWING_LINE'];
		$data_array[$val['JOB_NO']][$val['COLOR_ID']][$floor_library[$val['FLOOR_ID']]][$val['ITEM_ID']][$line_name]['floor_id'] = $val['FLOOR_ID'];
		// $reject_array[$line_name]['reject_qty'] += $val['REJECT_QTY'];
		$data_array[$val['JOB_NO']][$val['COLOR_ID']][$floor_library[$val['FLOOR_ID']]][$val['ITEM_ID']][$line_name]['reject_qty'] += $val['REJECT_QTY'];

		$job_id_array[$val['JOB_ID']] = $val['JOB_ID'];
		$job_no_array[$val['JOB_NO']] = $val['JOB_NO'];
	}

	//  echo "<pre>";print_r($data_array);echo "</pre>";
	//  echo "<pre>";print_r($reject_array);echo "</pre>";


	// ============================= size qty ======================
	$job_id_cond = where_con_using_array($job_id_array,0,"job_id");
	$sql = "SELECT id,job_no_mst,po_break_down_id,order_quantity,plan_cut_qnty,color_number_id as color_id,size_number_id FROM wo_po_color_size_breakdown where status_active in(1) and is_deleted=0 $job_id_cond order by id";
	// echo $sql;
	$res = sql_select($sql);

	$plan_cut_qty_arr = array();
	$po_id_array = array();
	$job_po_arr = array();
	$po_wise_job_array = array();
	foreach ($res as $val)
	{
		$size_arr[$val['SIZE_NUMBER_ID']] = $val['SIZE_NUMBER_ID'];
		$job_size_qty_arr[$val['JOB_NO_MST']][$val['SIZE_NUMBER_ID']] += $val['ORDER_QUANTITY'];
		$job_color_size_qty_arr[$val['JOB_NO_MST']][$val['COLOR_ID']][$val['SIZE_NUMBER_ID']] += $val['ORDER_QUANTITY'];

		$plan_cut_qty_arr[$val['JOB_NO_MST']] += $val['PLAN_CUT_QNTY'];
		$job_po_arr[$val['JOB_NO_MST']][$val['PO_BREAK_DOWN_ID']] = $val['PO_BREAK_DOWN_ID'];
		$po_id_array[$val['PO_BREAK_DOWN_ID']] = $val['PO_BREAK_DOWN_ID'];
		$po_wise_job_array[$val['PO_BREAK_DOWN_ID']] = $val['JOB_NO_MST'];
	}
	// echo "<pre>";print_r($job_po_arr);echo "</pre>";
	// =============================== image lib =============================
	$job_no_cond = where_con_using_array($job_no_array,1,"master_tble_id");
	//echo "select master_tble_id,image_location from common_photo_library where file_type=1 $job_no_cond";
	$imge_arr = return_library_array("select master_tble_id,image_location from common_photo_library where file_type=1 $job_no_cond", 'master_tble_id', 'image_location');

	// ===========================QC Pass Qty=============================
	$job_no_cond_1 = where_con_using_array($job_no_array,1,"a.job_no");
	$sql_qc="SELECT a.job_no,b.qc_pass_qty,b.color_id,b.size_id from pro_gmts_cutting_qc_mst a,pro_gmts_cutting_qc_dtls b
	where a.id=b.mst_id and a.status_active=1 and b.status_active=1 $job_no_cond_1 ";
	//echo $sql_qc;
	$res_qc = sql_select($sql_qc);
	$qc_pass_arr=array();
	foreach ($res_qc as $val) {
		$qc_pass_arr[$val[csf("job_no")]][$val[csf("color_id")]][$val[csf("size_id")]]+=$val[csf("qc_pass_qty")];
	}
	// echo "<pre>";
	// print_r($qc_pass_arr);
	// echo "</pre>";


	// ============================ cutting info ===============================
	$po_no_cond = where_con_using_array($po_id_array,0,"a.po_break_down_id");
	$sql = "SELECT a.po_break_down_id as po_id, max(a.production_date) as last_cutting_date,sum(b.production_qnty) as cut_qty from pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id and a.production_type=1 and a.status_active=1 and b.status_active=1 $po_no_cond group by a.po_break_down_id";
	// echo $sql;
	$res = sql_select($sql);
	$cutting_data = array();
	foreach ($res as $val)
	{
		$cutting_data[$po_wise_job_array[$val['PO_ID']]]['qty'] += $val['CUT_QTY'];
		$cutting_data[$po_wise_job_array[$val['PO_ID']]]['date'] = $val['LAST_CUTTING_DATE'];
	}

	// ============================ shipment info ===============================
	$po_no_cond = where_con_using_array($po_id_array,0,"a.po_break_down_id");
	$sql = "SELECT a.po_break_down_id as po_id, max(a.ex_factory_date) as last_ex_date,sum(b.production_qnty) as ex_qty from pro_ex_factory_mst a, pro_ex_factory_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 $po_no_cond group by a.po_break_down_id";
	// echo $sql;
	$res = sql_select($sql);
	$shipment_data = array();
	foreach ($res as $val)
	{
		$shipment_data[$po_wise_job_array[$val['PO_ID']]]['qty'] += $val['EX_QTY'];
		$shipment_data[$po_wise_job_array[$val['PO_ID']]]['date'] = $val['LAST_EX_DATE'];
	}
	// echo "<pre>";print_r($shipment_data);echo "</pre>";


	// ============================ reference closing ===============================
	$po_no_cond = where_con_using_array($po_id_array,0,"inv_pur_req_mst_id");
	$sql = "SELECT inv_pur_req_mst_id as po_id, max(closing_date) as last_ex_date from inv_reference_closing where status_active=1 $po_no_cond and closing_status=1 and reference_type=163 group by inv_pur_req_mst_id";
	// echo $sql;
	$res = sql_select($sql);
	$reference_closing_data = array();
	$closing_po_data = array();
	foreach ($res as $val)
	{
		$reference_closing_data[$po_wise_job_array[$val['PO_ID']]]['date'] = $val['LAST_EX_DATE'];
		$closing_po_data[$po_wise_job_array[$val['PO_ID']]][$val['PO_ID']] = $val['PO_ID'];
	}
	// echo "<pre>";print_r($closing_po_data);echo "</pre>";

	$rowspan = array();
	foreach ($date_data_array as $j_key => $j_value)
	{
		foreach ($j_value as $c_key => $c_value)
		{
			foreach ($c_value as $f_key => $f_value)
			{
				foreach ($f_value as $itm_key => $itm_value)
				{
					foreach ($itm_value as $l_key => $l_value)
					{
						foreach ($l_value as $d_key => $val)
						{
							$rowspan[$j_key][$c_key][$f_key]['tot']++;
							$rowspan[$j_key][$c_key][$f_key][$itm_key][$l_key]['tot']++;
						}
					}
				}
			}
		}
	}

	$tbl_width = 580+(count($size_arr)*40*2);
	ob_start();
	?>
	<style type="text/css">
		.rpt_table tfoot th{
			color: #000000;
		}
		.mst-part{font-weight: bold;}
	</style>
	<fieldset style="width:<?=$tbl_width+20;?>px;">

		<div style="width:<?=$tbl_width+20;?>px;">
			<table width="<?=$tbl_width;?>"  cellspacing="0">
				<tr class="form_caption" style="border:none;">
					<td colspan="25" align="center" style="border:none;font-size:14px; font-weight:bold" >Sewing Input and Output Report</td>
				</tr>
			</table>
			<br />
			<?
			$i=1;
			$grand_tot_arr = array();
			foreach ($data_array as $job_no => $job_data)
			{
				?>
				<div style="margin-bottom: 10px;">
					<table cellspacing="0" cellpadding="0"  border="1" style="border-collapse: collapse; width:800px" rules="all" class="rpt_table mst-part" align="left">
						<tbody>
							<tr>
								<td width="160px" rowspan="5" valign="middle">
									<img class="zoom" src='../../<?= $imge_arr[$job_no]; ?>' height="100" width="150" style="border: 3px solid #dccdcd;border-radius: 10px;"/>
								</td>
								<td width="120px">Job No :</td><td width="200px"><?=$job_no;?></td>
								<td width="120px">Season :</td><td><?=$season_library[$job_data_arr[$job_no]['season']];?></td>
							</tr>
							<tr>
								<td>Style :</td><td><?=$job_data_arr[$job_no]['style'];?></td>
								<td>Int. Ref.</td><td><?=$job_data_arr[$job_no]['grouping'];?></td>
							</tr>
							<tr>
								<td>Buyer :</td><td><?=$buyer_library[$job_data_arr[$job_no]['buyer']];?></td>
								<td>Item :</td><td><?=$garments_item[$job_data_arr[$job_no]['item_id']];?></td>
							</tr>
							<tr>
								<?
								$excess = $plan_cut_qty_arr[$job_no] - $job_data_arr[$job_no]['job_quantity'];
								$excess_prsnt = ($excess*100)/$job_data_arr[$job_no]['job_quantity'];
								?>
								<td>Job Qty : <br> Last Pub. Ship Date</td><td>
									<a href="javascript:void(0)" onClick="open_job_qty_popup('<?=$company_name;?>','<?=$job_no;?>')">
										<?=number_format($job_data_arr[$job_no]['job_quantity'],0);
										//if($job_data_arr[$job_no]['total_set_qty']==1) echo " (Pcs)"; else echo " (Set)"; ?> (Pcs)
									</a><br>
                                    <?
									echo $lastshipdate=change_date_format(max(array_filter(array_unique(explode(",",$job_data_arr[$job_no]['pub_shipment_date'])))));
									?>
								</td>
								<td>Plan Cut Qty [Pcs]  <br> Cut Percentage</td><td><?=number_format($plan_cut_qty_arr[$job_no],0);?><br><?=number_format($excess_prsnt,2);?>%</td>
							</tr>
							<tr>
								<td>Cutting Close :</td>
								<td>
									<?
									// echo $cutting_data[$job_no]['qty'].">=".$plan_cut_qty_arr[$job_no];
									if($cutting_data[$job_no]['qty']>=$job_data_arr[$job_no]['job_quantity'])
									{
										echo change_date_format($cutting_data[$job_no]['date']);
									}
									?>
								</td>
								<td>Job Close</td>
								<td>
									<?
									$job_close_date = "";
									if($shipment_data[$job_no]['qty']>=$plan_cut_qty_arr[$job_no])
									{
										$job_close_date = $shipment_data[$job_no]['date'];
									}
									if($job_close_date=="")
									{
										if(count($job_po_arr[$job_no]) == count($closing_po_data[$job_no]))
										{
											$job_close_date = $reference_closing_data[$job_no]['date'];
										}
									}
									echo change_date_format($job_close_date);
									?>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<br clear="all"><br clear="all">
				<div style="width:<?=$tbl_width+20;?>px; float:left;">
					<table cellspacing="0" cellpadding="0"  border="1" style="border-collapse: collapse;" rules="all"  width="<?=$tbl_width;?>" class="rpt_table" align="left">
						<thead>
							<tr>
								<th width="<?=(count($size_arr)*40)+350;?>" colspan="<?=5+count($size_arr);?>">Input</th>
								<th width="<?=(count($size_arr)*40)+230;?>" colspan="<?=4+count($size_arr);?>">Output</th>
							</tr>
							<tr>
								<th width="100" rowspan="2">Unit</th>
								<th width="100" rowspan="2">Item</th>
								<th width="50" rowspan="2">Line</th>

								<th width="50">Size</th>
								<?
								foreach ($size_arr as $key => $val)
								{
									?>
									<th width="40"><?=$size_library[$key]; ?></th>
									<?
								}
								?>
								<th width="50">Total</th>
								<?
								foreach ($size_arr as $key => $val)
								{
									?>
									<th width="40"><?=$size_library[$key]; ?></th>
									<?
								}
								?>
								<th width="50">Total</th>
								<th width="50" rowspan="2">Line WIP</th>
								<th width="50" rowspan="2">Sewing Rejection</th>
								<th width="80" rowspan="2">Remarks</th>
							</tr>
							<tr>
								<th width="50">Job Qty</th>
								<?
								$size_tot = 0;
								foreach ($size_arr as $key => $val)
								{
									?>
									<th width="40"><?=number_format($job_size_qty_arr[$job_no][$key],0); ?></th>
									<?
									$size_tot += $job_size_qty_arr[$job_no][$key];
								}
								?>
								<th width="50"><?=number_format($size_tot); ?></th>
								<?
								$size_tot = 0;
								foreach ($size_arr as $key => $val)
								{
									?>
									<th width="40"><?=number_format($job_size_qty_arr[$job_no][$key],0); ?></th>
									<?
									$size_tot += $job_size_qty_arr[$job_no][$key];
								}
								?>
								<th width="50"><?=number_format($size_tot); ?></th>
							</tr>
						</thead>
							<tbody>
								<?
								$job_tot_arr = array();
								foreach ($job_data as $color_id => $color_data)
								{
									?>
									<tr>
										<td colspan="<?=count($size_arr)*2+9?>">
											<strong> PO NO=</strong><?=chop($color_wise_order_arr[$color_id],","); ?>
										</td>
									</tr>


									<tr>
										<td valign="middle" colspan="3" width="100" rowspan="3"><strong><?=$color_library[$color_id]; ?></strong></td>
										<!-- <td width="50" rowspan="2">Line</td> -->

										<td width="50"><b>Size</b></td>
										<?
										foreach ($size_arr as $key => $val)
										{
											?>
											<td align="center" width="40"><b><?=$size_library[$key]; ?></b></td>
											<?
										}
										?>
										<td align="center" width="50"><b>Total</b></td>
										<?
										foreach ($size_arr as $key => $val)
										{
											?>
											<td align="center" width="40"><b><?=$size_library[$key]; ?></b></td>
											<?
										}
										?>
										<td align="center" width="50"><b>Total</b></td>
										<td width="50" rowspan="3"></td>
										<td width="50" rowspan="3"></td>
										<td width="80" rowspan="3"></td>
									</tr>
									<tr>
										<td width="50"><b>Color Qty</b></td>
										<?
										$size_tot = 0;
										foreach ($size_arr as $key => $val)
										{
											?>
											<td align="right" width="40"><b><?=number_format($job_color_size_qty_arr[$job_no][$color_id][$key],0); ?></b></td>
											<?
											$size_tot += $job_color_size_qty_arr[$job_no][$color_id][$key];
										}
										?>
										<td align="right" width="50"><b><?=number_format($size_tot); ?></b></td>
										<?
										$size_tot = 0;
										foreach ($size_arr as $key => $val)
										{
											?>
											<td align="right" width="40"><b><?=number_format($job_color_size_qty_arr[$job_no][$color_id][$key],0); ?></b></td>
											<?
											$size_tot += $job_color_size_qty_arr[$job_no][$color_id][$key];
										}
										?>
										<td align="right" width="50"><b><?=number_format($size_tot); ?></b></td>
									</tr>
									<tr>
										<td width="50"><b>Qc Pass Qty</b></td>
										<?
										$qc_pass_tot = 0;
										foreach ($size_arr as $key => $val)
										{
											?>
											<td align="right" width="40"><b>
												<?=number_format($qc_pass_arr[$job_no][$color_id][$key],0); ?></b></td>
											<?
											$qc_pass_tot += $qc_pass_arr[$job_no][$color_id][$key];
										}
										?>
										<td align="right" width="50"><b><?=number_format($qc_pass_tot); ?></b></td>
										<?
										// $qc_pass_tot = 0;
										// foreach ($size_arr as $key => $val)
										// {
										// 	?>
										<!--<td align="right" width="40"><b><?//=number_format($qc_pass_arr[$job_no][$color_id][$key],0); ?></b></td> -->
										 	 <?
										// 	$qc_pass_tot += $qc_pass_arr[$job_no][$color_id][$key];
										// }
										// ?>
										<!-- <td align="right" width="50"><b><?//=number_format($qc_pass_tot); ?></b></td> -->
									</tr>
									<?
									$color_tot_arr = array();
									ksort($color_data);
									foreach ($color_data as $floor_id => $floor_data)
									{
										$fl=0;

										foreach ($floor_data as $item_id => $item_data)
										{
											// ksort($floor_data);
											foreach ($item_data as $line_name => $row)
											{
												$li=0;
												?>
												<tr id="accordion_<?=$i;?>" onClick="accordion_menu( this.id,'panel_<?=$i;?>', '')" class="accordion" style="cursor: pointer;" title="click to expand">
													<td width="100"><p><?=$floor_id; ?></p></td>
													<td width="100"><p><?=$garments_item[$item_id]; ?></p></td>
													<td width="50">
														<p>
															<a href="##" ondbclick="fn_generate_details_report('<?=$color_id;?>','<?=$row['floor_id'];?>','<?=$row['line_id'];?>')">
																<?=$line_name;?>
															</a>
														</p>
													</td>
													<td width="50"></td>
													<?
													$size_tot_in = 0;
													foreach ($size_arr as $s_key => $val)
													{
														?>
														<td width="40" align="right"><?=number_format($row[4][$s_key]['qty'],0); ?></td>
														<?
														$size_tot_in += $row[4][$s_key]['qty'];
														$color_tot_arr[4][$color_id][$s_key] += $row[4][$s_key]['qty'];
														$job_tot_arr[$job_no][4][$s_key] += $row[4][$s_key]['qty'];
														$grand_tot_arr[4][$s_key] += $row[4][$s_key]['qty'];
													}
													?>
													<td style="border-right: 2px solid red;" align="right" width="50"><strong><?=number_format($size_tot_in,0);?></strong></td>
													<?
													$size_tot_out = 0;
													foreach ($size_arr as $s_key => $val)
													{
														?>
														<td width="40" align="right"><?=number_format($row[5][$s_key]['qty'],0); ?></td>
														<?
														$size_tot_out += $row[5][$s_key]['qty'];
														$color_tot_arr[5][$color_id][$s_key] += $row[5][$s_key]['qty'];
														$job_tot_arr[$job_no][5][$s_key] += $row[5][$s_key]['qty'];
														$grand_tot_arr[5][$s_key] += $row[5][$s_key]['qty'];
													}
													$wip =($size_tot_in -$size_tot_out)-$row['reject_qty'];
													?>
													<td align="right" width="50"><strong><?=number_format($size_tot_out,0);?></strong></td>
													<td align="right" width="50"><?=number_format($wip,0); $gr_wip+=$wip;?></td>
													<td align="right" width="50"><? echo number_format($row['reject_qty'],0); $color_tot_arr[$color_id]['reject_qty']+=$row['reject_qty']; $job_tot_arr[$job_no]['reject_qty']+=$row['reject_qty'];$gr_total_reject+=$row['reject_qty'];?></td>

													<td width="80"></td>
												</tr>
												<!-- =================== start date wise data ================ -->
												<tr id="panel_<?=$i;?>" class="panel" style="background: #E0C568FF;">
													<td width="<?=$tbl_width;?>" colspan="<?=count($size_arr)*2+8;?>">
														<div>
															<table width="<?=$tbl_width;?>" border="1" style="border-collapse: collapse;" cellpadding="0" cellspacing="0"  align="left">
																<?
																ksort($date_data_array[$job_no][$color_id][$floor_id][$item_id][$line_name]);
																foreach ($date_data_array[$job_no][$color_id][$floor_id][$item_id][$line_name] as $pdate => $value)
																{
																	?>
																	<tr>
																		<? if($li==0){?>
																		<td valign="middle" rowspan="<?=$rowspan[$job_no][$color_id][$floor_id][$item_id][$line_name]['tot'];?>" width="100"><p><?=$floor_id; ?></p></td>
																		<? $fl++;}?>

																		<td valign="middle" width="100"><p></p></td>
																		<? if($li==0){?>
																		<td valign="middle" width="50" rowspan="<?=$rowspan[$job_no][$color_id][$floor_id][$item_id][$line_name]['tot'];?>">
																			<p><?=$line_name;?></p>
																		</td>
																		<? $li++;} ?>
																		<td width="50"><?=date('d-m-Y',$pdate);?></td>
																		<?
																		$size_tot_in = 0;
																		foreach ($size_arr as $s_key => $val)
																		{
																			?>
																			<td width="40" align="right"><?=number_format($value[4][$s_key]['qty'],0); ?></td>
																			<?
																			$size_tot_in += $value[4][$s_key]['qty'];
																		}
																		?>
																		<td style="border-right: 2px solid red;" align="right" width="50"><strong><?=number_format($size_tot_in,0);?></strong></td>
																		<?
																		$size_tot_out = 0;
																		foreach ($size_arr as $s_key => $val)
																		{
																			?>
																			<td width="40" align="right"><?=number_format($value[5][$s_key]['qty'],0); ?></td>
																			<?
																			$size_tot_out += $value[5][$s_key]['qty'];
																		}
																		$wip = ($size_tot_in - $size_tot_out)-$reject_array[$line_name]['reject_qty'];
																		?>
																		<td align="right" width="50"><strong><?=number_format($size_tot_out,0);?></strong></td>
																		<td align="right" width="50"><?=number_format($wip,0);?></td>
																		<td width="50"><? echo $reject_array[$line_name]['reject_qty'];?></td>
																		<td width="80"></td>
																	</tr>
																	<?
																}
																?>
															</table>
														</div>
													</td>
												</tr>
												<!-- =================== end date wise data ================ -->
												<?
												$i++;
											}
										}
									}
									?>
									<tr style="background: #cddcdc;font-weight: bold;text-align: right;">
										<td colspan="4">Color Total</td>
										<?
										$color_tot_in = 0;
										foreach ($size_arr as $s_key => $val)
										{
											?>
											<td width="40" align="right"><?=number_format($color_tot_arr[4][$color_id][$s_key],0); ?></td>
											<?
											$color_tot_in += $color_tot_arr[4][$color_id][$s_key];
										}
										?>
										<td style="border-right: 2px solid red;" align="right" width="50"><?=number_format($color_tot_in,0);?></td>
										<?
										$color_tot_out = 0;
										foreach ($size_arr as $s_key => $val)
										{
											?>
											<td width="40" align="right"><?=number_format($color_tot_arr[5][$color_id][$s_key],0); ?></td>
											<?
											$color_tot_out += $color_tot_arr[5][$color_id][$s_key];
										}
										$wip = ($color_tot_in -$color_tot_out)-$color_tot_arr[$color_id]['reject_qty'];
										?>
										<td align="right" width="50"><?=number_format($color_tot_out,0);?></td>
										<td width="50" ><?=number_format($wip,0);?></td>
										<td width="50"><? echo $color_tot_arr[$color_id]['reject_qty'];?></td>
										<td width="80"></td>
									</tr>
									<?
								}
								?>
								<tr style="background: #dccdcd;font-weight: bold;text-align: right;">
									<td width="100"></td>
									<td width="100"></td>
									<td colspan="2" width="100">Job Total</td>
									<?
									$grnd_tot_in = 0;
									foreach ($size_arr as $s_key => $val)
									{
										?>
										<td width="40" align="right"><?=number_format($job_tot_arr[$job_no][4][$s_key],0); ?></td>
										<?
										$grnd_tot_in += $job_tot_arr[$job_no][4][$s_key];
									}
									?>
									<td style="border-right: 2px solid red;" align="right" width="50"><?=number_format($grnd_tot_in,0);?></td>
									<?
									$grnd_tot_out = 0;
									foreach ($size_arr as $s_key => $val)
									{
										?>
										<td width="40" align="right"><?=number_format($job_tot_arr[$job_no][5][$s_key],0); ?></td>
										<?
										$grnd_tot_out += $job_tot_arr[$job_no][5][$s_key];
									}
									$wip = ($grnd_tot_in-$grnd_tot_out)-$job_tot_arr[$job_no]['reject_qty'];
									?>
									<td align="right" width="50"><?=number_format($grnd_tot_out,0);?></td>
									<td width="50"><?=number_format($wip,0);?></td>
									<td width="50"><? echo $job_tot_arr[$job_no]['reject_qty'];?></td>
									<td width="80"></td>
								</tr>
							</tbody>
						</table>
					</div>
					<table style="border-collapse: collapse;"  border="1" class="rpt_table"  width="<?=$tbl_width;?>" rules="all" align="left" >
						<tfoot>
							<tr>
								<th width="100"></th>
								 <th width="100"></th>
								<!-- <th width="50"></th>  -->
								<th width="100" colspan="2">G.Total</th>
								<?
								$grnd_tot_in = 0;
								foreach ($size_arr as $s_key => $val)
								{
									?>
									<th width="40" align="right"><?=number_format($grand_tot_arr[4][$s_key],0); ?></th>
									<?
									$grnd_tot_in += $grand_tot_arr[4][$s_key];
								}
								?>
								<th style="border-right: 2px solid red;" align="right" width="50"><?=number_format($grnd_tot_in,0);?></th>
								<?
								$grnd_tot_out = 0;
								foreach ($size_arr as $s_key => $val)
								{
									?>
									<th width="40" align="right"><?=number_format($grand_tot_arr[5][$s_key],0); ?></th>
									<?
									$grnd_tot_out += $grand_tot_arr[5][$s_key];
								}
								$wip = $grnd_tot_in - $grnd_tot_out-$reject_total;
								?>
								<th align="right" width="50"><?=number_format($grnd_tot_out,0);?></th>
								<th width="50"><?=number_format($gr_wip,0);?></th>
								<th width="50"><? echo $gr_total_reject;?></th>
								<th width="80"></th>
							</tr>
						</tfoot>
					</table>
				</div>
				<?
			}
			?>
		</div>
	</fieldset>

	<!-- ======================= for line wise excel ====================== -->
	<?
	$cl_span = 7;
	$cl_span2 = 2;
	$html = '<fieldset id="line_wise" style="width:'.($tbl_width+20).'px;">

		<div >
			<table   cellspacing="0">
				<tr class="form_caption" style="border:none;">
					<td colspan="25" align="center" style="border:none;font-size:14px; font-weight:bold" >Sewing Input and Output Report</td>
				</tr>
			</table>
			<br />';

			$i=1;
			$grand_tot_arr = array();
			foreach ($data_array as $job_no => $job_data)
			{
				$html.='
				<div style="margin-bottom: : 10px;">
					<table cellspacing="0" cellpadding="0"  border="1" style="border-collapse: collapse;" rules="all" class="rpt_table" align="left" width="800">
						<tbody>
							<tr>
								<td width="20%" rowspan="5" valign="middle">
									<img class="zoom" src="../../'.$imge_arr[$job_no].'" height="100" width="150" style="border: 3px solid #dccdcd;border-radius: 10px;"/>
								</td>
								<td width="15%">Job No :</td><td width="25%">'.$job_no.'</td>
								<td width="15%">Season :</td><td width="25%">'.$season_library[$job_data_arr[$job_no]['season']].'</td>
							</tr>
							<tr>
								<td width="15%">Style :</td><td width="25%">'.$job_data_arr[$job_no]['style'].'</td>
								<td width="15%">Int. Ref.</td><td width="25%">'.$job_data_arr[$job_no]['grouping'].'</td>
							</tr>
							<tr>
								<td width="15%">Buyer :</td><td width="25%">'.$buyer_library[$job_data_arr[$job_no]['buyer']].'</td>
								<td width="15%">Item :</td><td width="25%">'.$garments_item[$job_data_arr[$job_no]['item_id']].'</td>
							</tr>';
							$excess = $plan_cut_qty_arr[$job_no] - $job_data_arr[$job_no]['job_quantity'];
							$excess_prsnt = ($excess*100)/$job_data_arr[$job_no]['job_quantity'];
							$html.='<tr>
								<td width="15%">Job Qty :</td><td width="25%">'.number_format($job_data_arr[$job_no]['job_quantity'],0);
								if($job_data_arr[$job_no]['total_set_qty']==1) $html .= " (Pcs)"; else $html .= " (Pcs)";$html.='</td>
								<td width="15%">Plan Cut Qty [Pcs]  <br> Cut Percentage</td><td width="25%">'.number_format($plan_cut_qty_arr[$job_no],0).'<br>'.$excess_prsnt.'%</td>
							</tr>
							<tr>
								<td width="15%">Cutting Close :</td>
								<td width="25%">';

									if($cutting_data[$job_no]['qty']>=$plan_cut_qty_arr[$job_no])
									{
										$html.=change_date_format($cutting_data[$job_no]['date']);
									}

								$html.='</td>
								<td width="15%">Job Close</td>
								<td width="25%">';

									if($shipment_data[$job_no]['qty']>=$plan_cut_qty_arr[$job_no])
									{
										$html.=change_date_format($shipment_data[$job_no]['date']);
									}

								$html.='</td>
							</tr>
						</tbody>
					</table>
				</div>
				<br clear="all"><br clear="all">
				<div style="width:'.($tbl_width+20).'px; float:left;">
					<table cellspacing="0" cellpadding="0"  border="1" style="border-collapse: collapse;" rules="all"  width="'.$tbl_width.'" class="rpt_table" align="left">
						<thead>
							<tr>
								<th  colspan="'.(4+count($size_arr)).'">Input</th>
								<th  colspan="'.(3+count($size_arr)).'">Output</th>
							</tr>
							<tr>
								<th width="100" rowspan="2">Unit</th>
								<th width="50" rowspan="2">Line</th>

								<th width="50">Size</th>';

								foreach ($size_arr as $key => $val)
								{

									$html.='<th width="40">'.$size_library[$key].'</th>';

								}

								$html.='<th width="50">Total</th>';

								foreach ($size_arr as $key => $val)
								{

									$html.='<th width="40">'.$size_library[$key].'</th>';

								}

								$html.='<th width="50">Total</th>
								<th width="50" rowspan="2">Line WIP</th>
								<th width="80" rowspan="2">Remarks</th>
							</tr>
							<tr>
								<th width="50">Job Qty</th>';

								$size_tot = 0;
								foreach ($size_arr as $key => $val)
								{

									$html.='<th width="40">'.number_format($job_size_qty_arr[$job_no][$key],0).'</th>';

									$size_tot += $job_size_qty_arr[$job_no][$key];
								}

								$html.='<th width="50">'.number_format($size_tot).'</th>';

								$size_tot = 0;
								foreach ($size_arr as $key => $val)
								{
									$html.='<th width="40">'.number_format($job_size_qty_arr[$job_no][$key],0).'</th>';

									$size_tot += $job_size_qty_arr[$job_no][$key];
								}

								$html.='<th width="50">'.number_format($size_tot).'</th>
							</tr>

						</thead>
					</table>

					<div style="max-height:425px;float:left; overflow-y:scroll; width:'.($tbl_width+20).'+px;" id="scroll_body2">
						<table  border="1" style="border-collapse: collapse;" class="rpt_table"  width="'.$tbl_width.'" rules="all" id="table_bodys" align="left" >
							<tbody>';

								$job_tot_arr = array();
								foreach ($job_data as $color_id => $color_data)
								{
									$html.='<tr>
										<td colspan="'.(count($size_arr)*2+7).'">
											<strong> PO NO=</strong>'.chop($color_wise_order_arr[$color_id],",").'
										</td>
									</tr>


									<tr>
										<td valign="middle" colspan="2" width="150" rowspan="2"><b>'.$color_library[$color_id].'</b></td>
										<!-- <td width="50" rowspan="2">Line</td> -->

										<td width="50"><b>Size</b></td>';

										foreach ($size_arr as $key => $val)
										{
											$html.='<td align="center" width="40"><b>'.$size_library[$key].'</b></td>';

										}

										$html.='<td align="center" width="50"><b>Total</b></td>';

										foreach ($size_arr as $key => $val)
										{
											$html.='<td align="center" width="40"><b>'.$size_library[$key].'</b></td>';

										}

										$html.='<td align="center" width="50"><b>Total</b></td>
										<td width="50" rowspan="2"></td>
										<td width="80" rowspan="2"></td>
									</tr>
									<tr>
										<td width="50"><b>Color Qty</b></td>';

										$size_tot = 0;
										foreach ($size_arr as $key => $val)
										{
											$html.='<td align="right" width="40"><b>'.number_format($job_color_size_qty_arr[$job_no][$color_id][$key],0).'</b></td>';

											$size_tot += $job_color_size_qty_arr[$job_no][$color_id][$key];
										}

										$html.='<td align="right" width="50"><b>'.number_format($size_tot).'</b></td>';

										$size_tot = 0;
										foreach ($size_arr as $key => $val)
										{
											$html.='<td align="right" width="40"><b>'.number_format($job_color_size_qty_arr[$job_no][$color_id][$key],0).'</b></td>';

											$size_tot += $job_color_size_qty_arr[$job_no][$color_id][$key];
										}

										$html.='<td align="right" width="50"><b>'.number_format($size_tot).'</b></td>
									</tr>';

									$color_tot_arr = array();
									ksort($color_data);
                                    foreach ($color_data as $floor_id => $floor_data) {
                                        $fl = 0;

                                        foreach ($floor_data as $item_id => $item_data) {
                                            // ksort($floor_data);
                                            foreach ($item_data as $line_name => $row) {
                                                $html .= '<tr>
												<td width="100"><p>&nbsp;' . $floor_id . '</p></td>
												<td width="50">&nbsp;' . $line_name . '</td>
												<td width="50"></td>';

                                                $size_tot_in = 0;
                                                foreach ($size_arr as $s_key => $val) {
                                                    $html .= '<td width="40" align="right">' . number_format($row[4][$s_key]['qty'], 0) . '</td>';

                                                    $size_tot_in += $row[4][$s_key]['qty'];
                                                    $color_tot_arr[4][$color_id][$s_key] += $row[4][$s_key]['qty'];
                                                    $job_tot_arr[$job_no][4][$s_key] += $row[4][$s_key]['qty'];
                                                    $grand_tot_arr[4][$s_key] += $row[4][$s_key]['qty'];
                                                }

                                                $html .= '<td style="border-right: 2px solid red;" align="right" width="50"><strong>' . number_format($size_tot_in, 0) . '</strong></td>';

                                                $size_tot_out = 0;
                                                foreach ($size_arr as $s_key => $val) {
                                                    $html .= '<td width="40" align="right">' . number_format($row[5][$s_key]['qty'], 0) . '</td>';

                                                    $size_tot_out += $row[5][$s_key]['qty'];
                                                    $color_tot_arr[5][$color_id][$s_key] += $row[5][$s_key]['qty'];
                                                    $job_tot_arr[$job_no][5][$s_key] += $row[5][$s_key]['qty'];
                                                    $grand_tot_arr[5][$s_key] += $row[5][$s_key]['qty'];
                                                }
                                                $wip = $size_tot_in - $size_tot_out;

                                                $html .= '<td align="right" width="50"><strong>' . number_format($size_tot_out, 0) . '</strong></td>
												<td align="right" width="50">' . number_format($wip, 0) . '</td>
												<td width="80"></td>
											</tr>';

                                                $i++;
                                            }
                                        }
                                    }

									$html.='<tr style="background: #cddcdc;font-weight: bold;text-align: right;">
										<td></td>
										<td></td>
										<td>Color Total</td>';

										$color_tot_in = 0;
										foreach ($size_arr as $s_key => $val)
										{
											$html.='<td width="40" align="right">'.number_format($color_tot_arr[4][$color_id][$s_key],0).'</td>';

											$color_tot_in += $color_tot_arr[4][$color_id][$s_key];
										}

										$html.='<td style="border-right: 2px solid red;" align="right" width="50">'.number_format($color_tot_in,0).'</td>';

										$color_tot_out = 0;
										foreach ($size_arr as $s_key => $val)
										{
											$html.='<td width="40" align="right">'.number_format($color_tot_arr[5][$color_id][$s_key],0).'</td>';

											$color_tot_out += $color_tot_arr[5][$color_id][$s_key];
										}
										$wip = $color_tot_in - $color_tot_out;

										$html.='<td align="right" width="50">'.number_format($color_tot_out,0).'</td>
										<td>'.number_format($wip,0).'</td>
										<td></td>
									</tr>';

								}

								$html.='<tr style="background: #dccdcd;font-weight: bold;text-align: right;">
									<td width="100"></td>
									<td width="50"></td>
									<td width="50">Job Total</td>';

									$grnd_tot_in = 0;
									foreach ($size_arr as $s_key => $val)
									{
										$html.='<td width="40" align="right">'.number_format($job_tot_arr[$job_no][4][$s_key],0).'</td>';

										$grnd_tot_in += $job_tot_arr[$job_no][4][$s_key];
									}

									$html.='<td style="border-right: 2px solid red;" align="right" width="50">'.number_format($grnd_tot_in,0).'</td>';

									$grnd_tot_out = 0;
									foreach ($size_arr as $s_key => $val)
									{
										$html.='<td width="40" align="right">'.number_format($job_tot_arr[$job_no][5][$s_key],0).'</td>';

										$grnd_tot_out += $job_tot_arr[$job_no][5][$s_key];
									}
									$wip = $grnd_tot_in - $grnd_tot_out;

									$html.='<td align="right" width="50">'.number_format($grnd_tot_out,0).'</td>
									<td width="50">'.number_format($wip,0).'</td>
									<td width="80"></td>
								</tr>
							</tbody>
						</table>
					</div>
					<table style="border-collapse: collapse;"  border="1" class="rpt_table"  width="'.$tbl_width.'" rules="all" align="left" >
						<tfoot>
							<tr>
								<th width="100"></th>
								<th width="50"></th>
								<th width="50">Grand Total</th>';

								$grnd_tot_in = 0;
								foreach ($size_arr as $s_key => $val)
								{
									$html.='<th width="40" align="right">'.number_format($grand_tot_arr[4][$s_key],0).'</th>';

									$grnd_tot_in += $grand_tot_arr[4][$s_key];
								}

								$html.='<th style="border-right: 2px solid red;" align="right" width="50">'.number_format($grnd_tot_in,0).'</th>';

								$grnd_tot_out = 0;
								foreach ($size_arr as $s_key => $val)
								{
									$html.='<th width="40" align="right">'.number_format($grand_tot_arr[5][$s_key],0).'</th>';

									$grnd_tot_out += $grand_tot_arr[5][$s_key];
								}
								$wip = $grnd_tot_in - $grnd_tot_out;

								$html.='<th align="right" width="50">'.number_format($grnd_tot_out,0).'</th>
								<th width="50">'.number_format($wip,0).'</th>
								<th width="80"></th>
							</tr>
						</tfoot>
					</table>
				</div>';

			}

		$html.='</div>
	</fieldset>';

	// echo $html;
	foreach (glob("$user_id*.xls") as $filename)
	{
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	//$filename=$user_id."_".$name.".xls";

	// =================================

	$name=time()."_sm";
	$filename_line_wise=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename_line_wise, 'w');
	$is_created = fwrite($create_new_doc,$html);
	//$filename=$user_id."_".$name.".xls";

	echo "$total_data####$filename####$filename_line_wise####$html";
	exit();
}
if($type==2)
{

	    $plan_sql=" SELECT  a.job_no, a.buyer_name, a.style_ref_no, a.season_buyer_wise,a.total_set_qnty, (a.job_quantity*a.total_set_qnty) as job_quantity, b.grouping,b.pub_shipment_date, c.order_quantity,c.plan_cut_qnty,c.item_number_id as item_id,c.po_break_down_id
		from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c
		where a.id=b.job_id and b.id=c.po_break_down_id and a.id=c.job_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sql_cond ";
		// echo $plan_sql;

		$plan_data=sql_select($plan_sql);
        $plan_arr=array();
		$po_id_arr=array();
		$po_wise_job_array=array();
		$job_qty_arr=array();

		foreach($plan_data as $val)
		{
			$plan_arr[$val[csf('job_no')]]['buyer_name']=$val[csf('buyer_name')];
			$plan_arr[$val[csf('job_no')]]['season_buyer_wise']=$val[csf('season_buyer_wise')];
			$plan_arr[$val[csf('job_no')]]['grouping']=$val[csf('grouping')];
			$plan_arr[$val[csf('job_no')]]['item_id']=$val[csf('item_id')];
			$plan_arr[$val[csf('job_no')]]['pub_shipment_date']=$val[csf('pub_shipment_date')];
			$plan_arr[$val[csf('job_no')]]['job_quantity']=$val[csf('job_quantity')];
			$plan_arr[$val[csf('job_no')]]['plan_cut_qnty']+=$val[csf('plan_cut_qnty')];
			$po_id_arr[$val[csf('po_break_down_id')]]=$val[csf('po_break_down_id')];
			$po_wise_job_array[$val['PO_BREAK_DOWN_ID']] = $val['JOB_NO'];

		}

	    // ============================ cutting info ===============================//
		$po_no_cond = where_con_using_array($po_id_array,0,"a.po_break_down_id");
		$sql = "SELECT a.po_break_down_id as po_id, max(a.production_date) as last_cutting_date,sum(b.production_qnty) as cut_qty from pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id and a.production_type=1 and a.status_active=1 and b.status_active=1 $po_no_cond group by a.po_break_down_id";
		//  echo $sql;
		$res = sql_select($sql);
		$cutting_data = array();
		foreach ($res as $val)
		{
			$cutting_data[$po_wise_job_array[$val['PO_ID']]]['qty'] += $val['CUT_QTY'];
			$cutting_data[$po_wise_job_array[$val['PO_ID']]]['date'] = $val['LAST_CUTTING_DATE'];
		}

		  // ============================ shipment info ===============================//
			$po_no_cond = where_con_using_array($po_id_array,0,"a.po_break_down_id");
			$sql = "SELECT a.po_break_down_id as po_id, max(a.ex_factory_date) as last_ex_date,sum(b.production_qnty) as ex_qty from pro_ex_factory_mst a, pro_ex_factory_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 $po_no_cond group by a.po_break_down_id";
			// echo $sql;
			$res = sql_select($sql);
			$shipment_data = array();
			foreach ($res as $val)
			{
				$shipment_data[$po_wise_job_array[$val['PO_ID']]]['qty'] += $val['EX_QTY'];
				$shipment_data[$po_wise_job_array[$val['PO_ID']]]['date'] = $val['LAST_EX_DATE'];
			}
			// echo "<pre>";print_r($shipment_data);echo "</pre>";


	   // ============================ reference closing ===============================//
		$po_no_cond = where_con_using_array($po_id_array,0,"inv_pur_req_mst_id");
		$sql = "SELECT inv_pur_req_mst_id as po_id, max(closing_date) as last_ex_date from inv_reference_closing where status_active=1 $po_no_cond and closing_status=1 and reference_type=163 group by inv_pur_req_mst_id";
		// echo $sql;
		$res = sql_select($sql);
		$reference_closing_data = array();
		$closing_po_data = array();
		foreach ($res as $val)
		{
			$reference_closing_data[$po_wise_job_array[$val['PO_ID']]]['date'] = $val['LAST_EX_DATE'];
			$closing_po_data[$po_wise_job_array[$val['PO_ID']]][$val['PO_ID']] = $val['PO_ID'];
		}

		$production_sql="SELECT a.id as job_id, c.order_quantity,c.size_number_id,d.production_type,TO_CHAR(d.production_date,'DD-MM-YYYY') as prod_date,e.production_qnty,c.job_no_mst,a.job_quantity,e.reject_qty
		from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e where a.id=b.job_id and b.id=c.po_break_down_id and a.id=c.job_id and c.po_break_down_id=d.po_break_down_id and c.id=e.color_size_break_down_id and d.id=e.mst_id and d.production_type IN(4,5) and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $sql_cond  order by d.production_date";
	    //   echo $production_sql;
		 $main_production_sql=sql_select($production_sql);
		 $main_prod_arr=array();
		 $size_arr=array();
		 $job_id_arr=array();
		 $gr_total_size=array();
		 foreach($main_production_sql as $row)
		 {
			$size_arr[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			$main_prod_arr[$row[csf('prod_date')]][$row[csf('production_type')]][$row[csf('size_number_id')]]['qty']+=$row[csf('production_qnty')];
			$main_prod_arr[$row[csf('prod_date')]]['reject_qty']+=$row[csf('reject_qty')];
			$gr_total_size[$row[csf('production_type')]][$row[csf('size_number_id')]]+=$row[csf('production_qnty')];
			$job_id_arr[$row[csf('job_id')]]=$row[csf('job_id')];



		 }
        //   echo '<pre>'; print_r($main_prod_arr); echo '</pre>';
		//   echo '<pre>'; print_r($job_id_arr); echo '</pre>';
		//   echo '<pre>'; print_r($gr_total_size); echo '</pre>';

		// ============================= size qty ======================
		$job_id_cond = where_con_using_array($job_id_arr,0,"job_id");
		$sql = "SELECT a.id,a.job_no_mst,a.order_quantity,a.size_number_id FROM wo_po_color_size_breakdown a where a.status_active in(1) and a.is_deleted=0 $job_id_cond order by a.id";
		// echo $sql;
		$res = sql_select($sql);
		$job_size_arr=array();
		foreach($res as $val)
		{
            $job_size_arr[$val[csf('job_no_mst')]][$val[csf('size_number_id')]]+=$val[csf('order_quantity')];
		}
		//   echo '<pre>'; print_r($job_size_arr); echo '</pre>';
		$tbl_width = 400+(count($size_arr)*40*2);
		?>
		<?
		  $i=1;
		  foreach($plan_arr as $job_no=>$job_data)
		  {

		?>
		<br clear="all">
          	<div style="margin-bottom: 10px;">
			<table width="500px" align="center" border="1" rules="all" class="rpt_table" >
            <tbody>
				<tr>
				<td width="120px" valign="top" style="font-size:16px;"><strong>Job No:</strong></td>
				<td width="200px" valign="top" style="font-size:16px;" ><?=$job_no;?></td>
				<td width="20px" valign="top" style="font-size:16px;"><strong>Season:</strong></td>
				<td width="20px" valign="top" style="font-size:16px;"><?=$season_library[$plan_arr[$job_no]['season_buyer_wise']];?></td>
				</tr>
				<tr>
					<td></td>
					<td><??></td>
					<td width="20px" valign="top" style="font-size:16px;"><strong>Int Ref.</strong></td>
					<td width="20px" valign="top" style="font-size:16px;"><?=$plan_arr[$job_no]['grouping'];?></td>
				</tr>
				<tr>
					<td  valign="top" style="font-size:16px;"><strong>Buyer:</strong></td>
					<td valign="top" style="font-size:16px;"><?=$buyer_library[$plan_arr[$job_no]['buyer_name']];?></td>
					<td valign="top" style="font-size:16px;"><strong>Item:</strong></td>
					<td valign="top" style="font-size:16px;"><?=$garments_item[$plan_arr[$job_no]['item_id']];;?></td>
				</tr>
				<tr>
					<td width="25px" valign="top" style="font-size:16px;"><strong>Job Qty:</strong></td>
					<td width="25px" valign="top" style="font-size:16px;"><a href="javascript:void(0)" onClick="open_job_qty_popup('<?=$company_name;?>','<?=$job_no;?>')"><?=number_format($plan_arr[$job_no]['job_quantity'],0)?></a></td>
					<td width="25px" valign="top" style="font-size:16px;"><strong>Plan Cut Qty[Pcs]:</strong></td>
					<td width="25px" valign="top" style="font-size:16px;"><?=$plan_arr[$job_no]['plan_cut_qnty'];?></td>
				</tr>
                <tr>
					<td  valign="top" style="font-size:16px;"><strong>Last Pub. Ship Date:</strong></td>
					<td valign="top" style="font-size:16px;"><?=$plan_arr[$job_no]['pub_shipment_date'];?></td>
					<td valign="top" style="font-size:16px;"><strong>Cut%:</strong></td>
					<td valign="top" style="font-size:16px;"><?
						$excess = $plan_arr[$job_no]['plan_cut_qnty'] - $plan_arr[$job_no]['job_quantity'];
						$excess_prsnt = ($excess*100)/$plan_arr[$job_no]['job_quantity'];
						echo number_format($excess_prsnt,2);
					?>%</td>
				</tr>
				<tr>
				    <td valign="top" style="font-size:16px;" >Cutting Close :</td>
					<td>
						<?
						if($cutting_data[$job_no]['qty']>=$plan_arr[$job_no]['job_quantity'])
						{
							echo change_date_format($cutting_data[$job_no]['date']);
						}
						?>
					</td>
					<td valign="top" style="font-size:16px;">Job Close:</td>
					<td >
						<?
						$job_close_date = "";
						if($shipment_data[$job_no]['qty']>=$plan_arr[$job_no])
						{
							$job_close_date = $shipment_data[$job_no]['date'];
						}
						if($job_close_date=="")
						{
							if(count($job_po_arr[$job_no]) == count($closing_po_data[$job_no]))
							{
								$job_close_date = $reference_closing_data[$job_no]['date'];
							}
						}
						echo change_date_format($job_close_date);
						?>
					</td>
			    </tr>

			</tbody>

			</table>
			</div>
             <br clear="all">
			 <div style="width:<?=$tbl_width+20;?>px; float:center;">
					<table cellspacing="0" cellpadding="0"  border="1" style="border-collapse: collapse;" rules="all"  width="<?=$tbl_width;?>" class="rpt_table" align="left">
						<thead>
							<tr>
								<th width="50"></th>
								<th width="<?=(count($size_arr)*40)+100;?>" colspan="<?=1+count($size_arr);?>">Input</th>
								<th width="<?=(count($size_arr)*40)+150;?>" colspan="<?=1+count($size_arr);?>">Output</th>
								<th width="50"></th>
								<th width="50"></th>

								<th width="50"></th>

							</tr>
							<tr>
								<th width="50">Size</th>
								<?
								foreach ($size_arr as $key => $val)
								{
									?>
									<th width="40"><?=$size_library[$key]; ?></th>
									<?
								}
								?>
								<th width="50">Total</th>
								<?
								foreach ($size_arr as $key => $val)
								{
									?>
									<th width="40"><?=$size_library[$key]; ?></th>
									<?
								}
								?>
								<th width="50">Total</th>
								<th width="50">Line Wip</th>
								<th width="50">Reject Qty</th>
								<th width="50">Remarks</th>
							</tr>
							<tr>
								<th width="50">Job Qty</th>
								<?
								$size_tot = 0;
								foreach ($size_arr as $key => $val)
								{
									?>
									<th width="40"><?=number_format($job_size_arr[$job_no][$key],0); ?></th>
									<?
									$size_tot += $job_size_arr[$job_no][$key];
								}
								?>
								<th width="50"><?=number_format($size_tot); ?></th>
								<?
								$size_tot = 0;
								foreach ($size_arr as $key => $val)
								{
									?>
									<th width="40"><?=number_format($job_size_arr[$job_no][$key],0); ?></th>
									<?
									$size_tot += $job_size_arr[$job_no][$key];
								}
								?>
								<th width="50"><?=number_format($size_tot); ?></th>
								<th width="50"><? ?></th>
								<th width="50"><? ?></th>
								<th width="50"><? ?></th>

							</tr>

						</thead>
					<tbody>
					<?
					   $i=1;
					   $total_rej =0 ;
					   $sew_prod_input_arr=array();
					   $sew_prod_out_arr=array();
                       foreach($main_prod_arr as $prod_date=>$prod_val)
					   {
								if ($i%2==0)
								$bgcolor="#E9F3FF";
								else
								$bgcolor="#FFFFFF";

					?>
						<tr bgcolor="<?=$bgcolor;?>">
						<td width="50" ><? echo $prod_date;?></td>
						<?
						$sew_input_tot=0;
						foreach($size_arr as $size_id=>$size_data)
						{

						?>
						<td width="40" align="right"><?=$prod_val[4][$size_id]['qty'];?></td>
						<?
						    $sew_input_tot+=$prod_val[4][$size_id]['qty'];
							}
						?>
						<td width="50" align="right"><? echo $sew_input_tot; $gr_sew_input_tot+=$sew_input_tot; ?></td>
						<?
						    $sew_out_total=0;
							foreach($size_arr as $size_id=>$size_data)
							{

						?>
							<td width="40" align="right"><?=$prod_val[5][$size_id]['qty'];?></td>
						<?
						    $sew_out_total+=$prod_val[5][$size_id]['qty'];
							}
						?>
						<td width="50" align="right"><? echo $sew_out_total;  $gr_sew_out_total+=$sew_out_total; ?></td>
						<td width="50" align="right"><? 
						 $line_wip = (($prev_in_qty + $sew_input_tot) - ($prev_out_qty + $sew_out_total)) - ($prev_rej_qty+$prod_val['reject_qty']);
						 echo $line_wip ;
						/* if($i==1)
						    { 
							 $sew_today_input_tot+=$sew_input_tot;
							  $sew_today_output_tot+=$sew_out_total;
							  $learn_wip= ($sew_today_input_tot-$sew_today_output_tot);
							   echo $learn_wip ;
							}
						else{
                          
							$sew_input_other_day_tot+=$sew_input_tot;
							$sew_output_other_day_tot+=$sew_out_total;
							$sew_input_other_tot=($sew_today_input_tot+$sew_input_tot); 
							$sew_output_other_tot=($sew_today_output_tot+$sew_output_other_day_tot); 
							$other_line_wip=($sew_input_other_tot-$sew_output_other_tot)   ; 

							echo $other_line_wip  ;
							}
							$total_other_line_wip+=$other_line_wip; */ ?></td>
						<td width="50" align="right"><? echo $prod_val['reject_qty']; $gr_rejecttotal+=$prod_val['reject_qty'];?></td>
						<td width="50"><? ?></td>
						</tr>
						<?
						$i++;
						$prev_in_qty += $sew_input_tot;
						$prev_out_qty += $sew_out_total;
						$prev_rej_qty += $prod_val['reject_qty'] ;

					    }
					?>
				   </tbody>
				   <tfoot>
                     <tr>
                        <th width="50">G.Total</th>
						<?
						foreach($size_arr as $size_id=>$size_data)
						{

						?>
						<th width="40" align="right"><?=$gr_total_size[4][$size_id];?></th>
						<?

							}
						?>
                        <th width="50"><?=$gr_sew_input_tot;?></th>
						<?
						foreach($size_arr as $size_id=>$size_data)
						{

						?>
						<th width="40" align="right"><?=$gr_total_size[5][$size_id];?></th>
						<?

							}
						?>
						 <th width="50"><?=$gr_sew_out_total;?></th>
						 <th width="50"><?  $gr_line_wip=$gr_sew_input_tot-$gr_sew_out_total; echo $gr_line_wip - $gr_rejecttotal;?></th>
						 <th width="50"><? echo number_format($gr_rejecttotal,0); ?></th>
						 <th width="50"><??></th>


					 </tr>
				   </tfoot>
				   </table>
				  </div>
		<?
		  }
		?>

<?


}

}

if($action=="job_qty_popup")
{
	echo load_html_head_contents("Job Qty Popup","../../../", 1, 1,$unicode,1);

	extract($_REQUEST);

	$sql="SELECT a.id,a.po_number, b.color_number_id as color_id,b.size_number_id as size_id,b.order_quantity from wo_po_break_down a, wo_po_color_size_breakdown b where a.id=b.po_break_down_id and a.status_active=1 and b.status_active=1 and a.job_no_mst='$job_no'";
	// echo $sql;
	$res = sql_select($sql);
	$data_array = array();
	$size_array = array();
	$po_id_array = array();
	foreach ($res as $val)
	{
		$data_array[$val['ID']][$val['COLOR_ID']][$val['SIZE_ID']]['qty'] += $val['ORDER_QUANTITY'];
		$data_array[$val['ID']][$val['COLOR_ID']]['po_number'] = $val['PO_NUMBER'];
		$size_array[$val['SIZE_ID']] = $val['SIZE_ID'];
		$po_id_array[$val['ID']] = $val['ID'];
	}

	// ============================ prod Data ========================
	$po_id_cond = where_con_using_array($po_id_array,0,"a.po_break_down_id");
	$sql = "SELECT c.color_number_id as color_id,c.size_number_id as size_id, a.po_break_down_id as po_id,a.production_type,a.embel_name,b.production_qnty from pro_garments_production_mst a, pro_garments_production_dtls b,wo_po_color_size_breakdown c where a.id=b.mst_id and c.id=b.color_size_break_down_id and c.po_break_down_id=a.po_break_down_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.production_type in(1,2,3,4,5,7,8,15) $po_id_cond";
	// echo $sql;
	$res = sql_select($sql);
	foreach ($res as $val)
	{
		$data_array[$val['PO_ID']][$val['COLOR_ID']][$val['SIZE_ID']][$val['PRODUCTION_TYPE']][$val['EMBEL_NAME']]['qty'] += $val['PRODUCTION_QNTY'];
	}

	// ====================== ex-factory =======================
	$sql = "SELECT c.color_number_id as color_id,c.size_number_id as size_id, a.po_break_down_id as po_id,b.production_qnty from pro_ex_factory_mst a, pro_ex_factory_dtls b, wo_po_color_size_breakdown c where a.id=b.mst_id and c.id=b.color_size_break_down_id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and b.status_active=1 and c.status_active=1 $po_id_cond";
	// echo $sql;
	$res = sql_select($sql);
	foreach ($res as $val)
	{
		$data_array[$val['PO_ID']][$val['COLOR_ID']][$val['SIZE_ID']][0][0]['qty'] += $val['PRODUCTION_QNTY'];
	}
	// echo "<pre>";print_r($data_array);echo "</pre>";

	// ============================
	$rowspan = array();
	foreach($data_array as $po_id=>$po_data)
	{
		foreach ($po_data as $color_id => $row)
		{
			$rowspan[$po_id]++;
		}
	}
	// echo "<pre>";print_r($rowspan);die();

	$tbl_width = 380+count($size_array)*60;

	?>
	<div id="data_panel" align="center" style="width:100%">
        <script>
            function new_window()
            {
            	document.getElementById('scroll_body').style.overflow="auto";
				document.getElementById('scroll_body').style.maxHeight="none";
                var w = window.open("Surprise", "#");
                var d = w.document.open();
                // d.write(document.getElementById('details_reports').innerHTML);
                d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="all" /><title></title></head><body>'+document.getElementById('details_reports').innerHTML+'</body</html>');
                d.close();
                document.getElementById('scroll_body').style.overflowY="scroll";
				document.getElementById('scroll_body').style.maxHeight="415px";
            }
        </script>
        <a id="excel"><input type="button" value="Excel Preview" class="formbutton" style="width:100px;margin: 5px;"/></a>&nbsp;&nbsp;
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;margin: 5px;" onClick="new_window()" />
    </div>
    <?
    ob_start();
    ?>
	<fieldset id="details_reports">
		<table cellspacing="0" width="<?=$tbl_width;?>"  border="1" rules="all" class="rpt_table">
			<thead>
				<tr>
					<th width="100">Order Number</th>
					<th width="100">Color</th>
					<th width="100">Production Type</th>
					<?
                	foreach ($size_array as $s_key => $val)
                	{
                		?>
                		<th width="60"><?=$size_library[$s_key];?></th>
                		<?
                	}
                	?>
					<th width="80">Total</th>
				</tr>
			</thead>
		</table>
	    <div style="width:<?=$tbl_width+20;?>px; max-height:415px; overflow-y:auto;" id="scroll_body" >
			<table cellspacing="0" width="<?=$tbl_width;?>"  border="1" rules="all" class="rpt_table">
	        <?
			$i=1;
			foreach($data_array as $po_id=>$po_data)
			{
				$p=0;
				// echo count($po_data);echo "<br>";
				foreach ($po_data as $color_id => $row)
				{
				 	$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					?>
					<!-- ==================== order qty ====================== -->
	        		<tr bgcolor="<? echo $bgcolor ; ?>" id="tr_<? echo $i; ?>" style="cursor:pointer;">
	        			<? if($p==0){?>
	                	<td rowspan="<?=count($po_data)*18;?>" valign="middle" width="100"><b><?=$row['po_number'];?></b></td>
	                	<? $p++;}?>
	                	<td rowspan="18" valign="middle" width="100"><?=$color_library[$color_id];?></td>
	                	<td width="100">Order Qty</td>
	                	<?
	                	$tot = 0;
	                	foreach ($size_array as $s_key => $val)
	                	{
	                		?>
	                		<td width="60" align="right"><?=number_format($row[$s_key]['qty'],0);?></td>
	                		<?
	                		$tot += $row[$s_key]['qty'];
	                	}
	                	?>
	                	<td width="80" align="right"><b><?=number_format($tot,0);?></b></td>
	            	</tr>

					<!-- ==================== plan cut qty ====================== -->
	            	<tr bgcolor="<? echo $bgcolor ; ?>" id="tr_<? echo $i; ?>" style="cursor:pointer;">
	                	<td width="100">Plan To Cut (AVG 3.00)% </td>
	                	<?
	                	$tot = 0;
	                	foreach ($size_array as $s_key => $val)
	                	{
	                		?>
	                		<td width="60" align="right"><?=number_format($row[$s_key]['qty'],0);?></td>
	                		<?
	                		$tot += $row[$s_key]['qty'];
	                	}
	                	?>
	                	<td width="80" align="right"><b><?=number_format($tot,0);?></b></td>
	            	</tr>

					<!-- ==================== Cutting qty ====================== -->
	            	<tr bgcolor="<? echo $bgcolor ; ?>" id="tr_<? echo $i; ?>" style="cursor:pointer;">

	                	<td width="100">Cutting </td>
	                	<?
	                	$tot = 0;
	                	foreach ($size_array as $s_key => $val)
	                	{
		                	$td_color = "";
		                	if($row[$s_key]['qty']<1)
		                	{
		                		$td_color = "";
		                	}
		                	elseif($row[$s_key]['qty']<=$row[$s_key][1][0]['qty'])
		                	{
		                		$td_color="green";
		                	}
		                	elseif ($row[$s_key]['qty']>$row[$s_key][1][0]['qty'] && $row[$s_key][1][0]['qty']>0)
		                	{
		                		$td_color="yellow";
		                	}
		                	else
		                	{
		                		$td_color="red";
		                	}
	                		?>
	                		<td bgcolor="<?=$td_color;?>" width="60" align="right"><?=number_format($row[$s_key][1][0]['qty'],0);?></td>
	                		<?
	                		$tot += $row[$s_key][1][0]['qty'];
	                	}
	                	?>
	                	<td width="80" align="right"><b><?=number_format($tot,0);?></b></td>
	            	</tr>

					<!-- ==================== Print Issue qty ====================== -->
	            	<tr bgcolor="<? echo $bgcolor ; ?>" id="tr_<? echo $i; ?>" style="cursor:pointer;">

	                	<td width="100">Print Issue </td>
	                	<?
	                	$tot = 0;
	                	foreach ($size_array as $s_key => $val)
	                	{
		                	$td_color = "";
		                	if($row[$s_key]['qty']<1)
		                	{
		                		$td_color = "";
		                	}
		                	elseif($row[$s_key]['qty']<=$row[$s_key][2][1]['qty'])
		                	{
		                		$td_color="green";
		                	}
		                	elseif ($row[$s_key]['qty']>$row[$s_key][2][1]['qty'] && $row[$s_key][2][1]['qty']>0)
		                	{
		                		$td_color="yellow";
		                	}
		                	else
		                	{
		                		$td_color="red";
		                	}
	                		?>
	                		<td bgcolor="<?=$td_color;?>" width="60" align="right"><?=number_format($row[$s_key][2][1]['qty'],0);?></td>
	                		<?
	                		$tot += $row[$s_key][2][1]['qty'];
	                	}
	                	?>
	                	<td width="80" align="right"><b><?=number_format($tot,0);?></b></td>
	            	</tr>

					<!-- ==================== Print Received qty ====================== -->
	            	<tr bgcolor="<? echo $bgcolor ; ?>" id="tr_<? echo $i; ?>" style="cursor:pointer;">

	                	<td width="100">Print Received </td>
	                	<?
	                	$tot = 0;
	                	foreach ($size_array as $s_key => $val)
	                	{
		                	$td_color = "";
		                	if($row[$s_key]['qty']<1)
		                	{
		                		$td_color = "";
		                	}
		                	elseif($row[$s_key]['qty']<=$row[$s_key][3][1]['qty'])
		                	{
		                		$td_color="green";
		                	}
		                	elseif ($row[$s_key]['qty']>$row[$s_key][3][1]['qty'] && $row[$s_key][3][1]['qty']>0)
		                	{
		                		$td_color="yellow";
		                	}
		                	else
		                	{
		                		$td_color="red";
		                	}
	                		?>
	                		<td bgcolor="<?=$td_color;?>" width="60" align="right"><?=number_format($row[$s_key][3][1]['qty'],0);?></td>
	                		<?
	                		$tot += $row[$s_key][3][1]['qty'];
	                	}
	                	?>
	                	<td width="80" align="right"><b><?=number_format($tot,0);?></b></td>
	            	</tr>

					<!-- ==================== Embro Issue qty ====================== -->
	            	<tr bgcolor="<? echo $bgcolor ; ?>" id="tr_<? echo $i; ?>" style="cursor:pointer;">

	                	<td width="100">Embro Issue </td>
	                	<?
	                	$tot = 0;
	                	foreach ($size_array as $s_key => $val)
	                	{
		                	$td_color = "";
		                	if($row[$s_key]['qty']<1)
		                	{
		                		$td_color = "";
		                	}
		                	elseif($row[$s_key]['qty']<=$row[$s_key][2][2]['qty'])
		                	{
		                		$td_color="green";
		                	}
		                	elseif ($row[$s_key]['qty']>$row[$s_key][2][2]['qty'] && $row[$s_key][2][2]['qty']>0)
		                	{
		                		$td_color="yellow";
		                	}
		                	else
		                	{
		                		$td_color="red";
		                	}
	                		?>
	                		<td bgcolor="<?=$td_color;?>" width="60" align="right"><?=number_format($row[$s_key][2][2]['qty'],0);?></td>
	                		<?
	                		$tot += $row[$s_key][2][2]['qty'];
	                	}
	                	?>
	                	<td width="80" align="right"><b><?=number_format($tot,0);?></b></td>
	            	</tr>

					<!-- ==================== Embro Received qty ====================== -->
	            	<tr bgcolor="<? echo $bgcolor ; ?>" id="tr_<? echo $i; ?>" style="cursor:pointer;">

	                	<td width="100">Embro Received </td>
	                	<?
	                	$tot = 0;
	                	foreach ($size_array as $s_key => $val)
	                	{
		                	$td_color = "";
		                	if($row[$s_key]['qty']<1)
		                	{
		                		$td_color = "";
		                	}
		                	elseif($row[$s_key]['qty']<=$row[$s_key][3][2]['qty'])
		                	{
		                		$td_color="green";
		                	}
		                	elseif ($row[$s_key]['qty']>$row[$s_key][3][2]['qty'] && $row[$s_key][3][2]['qty']>0)
		                	{
		                		$td_color="yellow";
		                	}
		                	else
		                	{
		                		$td_color="red";
		                	}
	                		?>
	                		<td bgcolor="<?=$td_color;?>" width="60" align="right"><?=number_format($row[$s_key][3][2]['qty'],0);?></td>
	                		<?
	                		$tot += $row[$s_key][3][2]['qty'];
	                	}
	                	?>
	                	<td width="80" align="right"><b><?=number_format($tot,0);?></b></td>
	            	</tr>

					<!-- ==================== Issue For Special Works qty ====================== -->
	            	<tr bgcolor="<? echo $bgcolor ; ?>" id="tr_<? echo $i; ?>" style="cursor:pointer;">

	                	<td width="100">Issue For Special Works </td>
	                	<?
	                	$tot = 0;
	                	foreach ($size_array as $s_key => $val)
	                	{
		                	$td_color = "";
		                	if($row[$s_key]['qty']<1)
		                	{
		                		$td_color = "";
		                	}
		                	elseif($row[$s_key]['qty']<=$row[$s_key][2][4]['qty'])
		                	{
		                		$td_color="green";
		                	}
		                	elseif ($row[$s_key]['qty']>$row[$s_key][2][4]['qty'] && $row[$s_key][2][4]['qty']>0)
		                	{
		                		$td_color="yellow";
		                	}
		                	else
		                	{
		                		$td_color="red";
		                	}
	                		?>
	                		<td bgcolor="<?=$td_color;?>" width="60" align="right"><?=number_format($row[$s_key][2][4]['qty'],0);?></td>
	                		<?
	                		$tot += $row[$s_key][2][4]['qty'];
	                	}
	                	?>
	                	<td width="80" align="right"><b><?=number_format($tot,0);?></b></td>
	            	</tr>

					<!-- ==================== Recv. From Special Works qty ====================== -->
	            	<tr bgcolor="<? echo $bgcolor ; ?>" id="tr_<? echo $i; ?>" style="cursor:pointer;">

	                	<td width="100">Recv. From Special Works </td>
	                	<?
	                	$tot = 0;
	                	foreach ($size_array as $s_key => $val)
	                	{
		                	$td_color = "";
		                	if($row[$s_key]['qty']<1)
		                	{
		                		$td_color = "";
		                	}
		                	elseif($row[$s_key]['qty']<=$row[$s_key][3][4]['qty'])
		                	{
		                		$td_color="green";
		                	}
		                	elseif ($row[$s_key]['qty']>$row[$s_key][3][4]['qty'] && $row[$s_key][3][4]['qty']>0)
		                	{
		                		$td_color="yellow";
		                	}
		                	else
		                	{
		                		$td_color="red";
		                	}
	                		?>
	                		<td bgcolor="<?=$td_color;?>" width="60" align="right"><?=number_format($row[$s_key][3][4]['qty'],0);?></td>
	                		<?
	                		$tot += $row[$s_key][3][4]['qty'];
	                	}
	                	?>
	                	<td width="80" align="right"><b><?=number_format($tot,0);?></b></td>
	            	</tr>

					<!-- ==================== Sewing Input qty ====================== -->
	            	<tr bgcolor="<? echo $bgcolor ; ?>" id="tr_<? echo $i; ?>" style="cursor:pointer;">

	                	<td width="100">Sewing Input </td>
	                	<?
	                	$tot = 0;
	                	foreach ($size_array as $s_key => $val)
	                	{
		                	$td_color = "";
		                	if($row[$s_key]['qty']<1)
		                	{
		                		$td_color = "";
		                	}
		                	elseif($row[$s_key]['qty']<=$row[$s_key][4][0]['qty'])
		                	{
		                		$td_color="green";
		                	}
		                	elseif ($row[$s_key]['qty']>$row[$s_key][4][0]['qty'] && $row[$s_key][4][0]['qty']>0)
		                	{
		                		$td_color="yellow";
		                	}
		                	else
		                	{
		                		$td_color="red";
		                	}
	                		?>
	                		<td bgcolor="<?=$td_color;?>" width="60" align="right"><?=number_format($row[$s_key][4][0]['qty'],0);?></td>
	                		<?
	                		$tot += $row[$s_key][4][0]['qty'];
	                	}
	                	?>
	                	<td width="80" align="right"><b><?=number_format($tot,0);?></b></td>
	            	</tr>

					<!-- ==================== Sewing Output qty ====================== -->
	            	<tr bgcolor="<? echo $bgcolor ; ?>" id="tr_<? echo $i; ?>" style="cursor:pointer;">

	                	<td width="100">Sewing Output </td>
	                	<?
	                	$tot = 0;
	                	foreach ($size_array as $s_key => $val)
	                	{
		                	$td_color = "";
		                	if($row[$s_key]['qty']<1)
		                	{
		                		$td_color = "";
		                	}
		                	elseif($row[$s_key]['qty']<=$row[$s_key][5][0]['qty'])
		                	{
		                		$td_color="green";
		                	}
		                	elseif ($row[$s_key]['qty']>$row[$s_key][5][0]['qty'] && $row[$s_key][5][0]['qty']>0)
		                	{
		                		$td_color="yellow";
		                	}
		                	else
		                	{
		                		$td_color="red";
		                	}
	                		?>
	                		<td bgcolor="<?=$td_color;?>" width="60" align="right"><?=number_format($row[$s_key][5][0]['qty'],0);?></td>
	                		<?
	                		$tot += $row[$s_key][5][0]['qty'];
	                	}
	                	?>
	                	<td width="80" align="right"><b><?=number_format($tot,0);?></b></td>
	            	</tr>

					<!-- ==================== Issue For Wash qty ====================== -->
	            	<tr bgcolor="<? echo $bgcolor ; ?>" id="tr_<? echo $i; ?>" style="cursor:pointer;">

	                	<td width="100">Issue For Wash </td>
	                	<?
	                	$tot = 0;
	                	foreach ($size_array as $s_key => $val)
	                	{
		                	$td_color = "";
		                	if($row[$s_key]['qty']<1)
		                	{
		                		$td_color = "";
		                	}
		                	elseif($row[$s_key]['qty']<=$row[$s_key][2][3]['qty'])
		                	{
		                		$td_color="green";
		                	}
		                	elseif ($row[$s_key]['qty']>$row[$s_key][2][3]['qty'] && $row[$s_key][2][3]['qty']>0)
		                	{
		                		$td_color="yellow";
		                	}
		                	else
		                	{
		                		$td_color="red";
		                	}
	                		?>
	                		<td bgcolor="<?=$td_color;?>" width="60" align="right"><?=number_format($row[$s_key][2][3]['qty'],0);?></td>
	                		<?
	                		$tot += $row[$s_key][2][3]['qty'];
	                	}
	                	?>
	                	<td width="80" align="right"><b><?=number_format($tot,0);?></b></td>
	            	</tr>

					<!-- ==================== Recv. From Wash qty ====================== -->
	            	<tr bgcolor="<? echo $bgcolor ; ?>" id="tr_<? echo $i; ?>" style="cursor:pointer;">

	                	<td width="100">Recv. From Wash </td>
	                	<?
	                	$tot = 0;
	                	foreach ($size_array as $s_key => $val)
	                	{
		                	$td_color = "";
		                	if($row[$s_key]['qty']<1)
		                	{
		                		$td_color = "";
		                	}
		                	elseif($row[$s_key]['qty']<=$row[$s_key][3][3]['qty'])
		                	{
		                		$td_color="green";
		                	}
		                	elseif ($row[$s_key]['qty']>$row[$s_key][3][3]['qty'] && $row[$s_key][3][3]['qty']>0)
		                	{
		                		$td_color="yellow";
		                	}
		                	else
		                	{
		                		$td_color="red";
		                	}
	                		?>
	                		<td bgcolor="<?=$td_color;?>" width="60" align="right"><?=number_format($row[$s_key][3][3]['qty'],0);?></td>
	                		<?
	                		$tot += $row[$s_key][3][3]['qty'];
	                	}
	                	?>
	                	<td width="80" align="right"><b><?=number_format($tot,0);?></b></td>
	            	</tr>

					<!-- ==================== Iron qty ====================== -->
	            	<tr bgcolor="<? echo $bgcolor ; ?>" id="tr_<? echo $i; ?>" style="cursor:pointer;">

	                	<td width="100">Iron</td>
	                	<?
	                	$tot = 0;
	                	foreach ($size_array as $s_key => $val)
	                	{
		                	$td_color = "";
		                	if($row[$s_key]['qty']<1)
		                	{
		                		$td_color = "";
		                	}
		                	elseif($row[$s_key]['qty']<=$row[$s_key][7][0]['qty'])
		                	{
		                		$td_color="green";
		                	}
		                	elseif ($row[$s_key]['qty']>$row[$s_key][7][0]['qty'] && $row[$s_key][7][0]['qty']>0)
		                	{
		                		$td_color="yellow";
		                	}
		                	else
		                	{
		                		$td_color="red";
		                	}
	                		?>
	                		<td bgcolor="<?=$td_color;?>" width="60" align="right"><?=number_format($row[$s_key][7][0]['qty'],0);?></td>
	                		<?
	                		$tot += $row[$s_key][7][0]['qty'];
	                	}
	                	?>
	                	<td width="80" align="right"><b><?=number_format($tot,0);?></b></td>
	            	</tr>

					<!-- ==================== Hangtag qty ====================== -->
	            	<tr bgcolor="<? echo $bgcolor ; ?>" id="tr_<? echo $i; ?>" style="cursor:pointer;">

	                	<td width="100">Hangtag </td>
	                	<?
	                	$tot = 0;
	                	foreach ($size_array as $s_key => $val)
	                	{
		                	$td_color = "";
		                	if($row[$s_key]['qty']<1)
		                	{
		                		$td_color = "";
		                	}
		                	elseif($row[$s_key]['qty']<=$row[$s_key][15][0]['qty'])
		                	{
		                		$td_color="green";
		                	}
		                	elseif ($row[$s_key]['qty']>$row[$s_key][15][0]['qty'] && $row[$s_key][15][0]['qty']>0)
		                	{
		                		$td_color="yellow";
		                	}
		                	else
		                	{
		                		$td_color="red";
		                	}
	                		?>
	                		<td bgcolor="<?=$td_color;?>" width="60" align="right"><?=number_format($row[$s_key][15][0]['qty'],0);?></td>
	                		<?
	                		$tot += $row[$s_key][15][0]['qty'];
	                	}
	                	?>
	                	<td width="80" align="right"><b><?=number_format($tot,0);?></b></td>
	            	</tr>

					<!-- ==================== Finishing qty ====================== -->
	            	<tr bgcolor="<? echo $bgcolor ; ?>" id="tr_<? echo $i; ?>" style="cursor:pointer;">

	                	<td width="100">Finishing</td>
	                	<?
	                	$tot = 0;
	                	foreach ($size_array as $s_key => $val)
	                	{
		                	$td_color = "";
		                	if($row[$s_key]['qty']<1)
		                	{
		                		$td_color = "";
		                	}
		                	elseif($row[$s_key]['qty']<=$row[$s_key][8][0]['qty'])
		                	{
		                		$td_color="green";
		                	}
		                	elseif ($row[$s_key]['qty']>$row[$s_key][8][0]['qty'] && $row[$s_key][8][0]['qty']>0)
		                	{
		                		$td_color="yellow";
		                	}
		                	else
		                	{
		                		$td_color="red";
		                	}
	                		?>
	                		<td bgcolor="<?=$td_color;?>" width="60" align="right"><?=number_format($row[$s_key][8][0]['qty'],0);?></td>
	                		<?
	                		$tot += $row[$s_key][8][0]['qty'];
	                	}
	                	?>
	                	<td width="80" align="right"><b><?=number_format($tot,0);?></b></td>
	            	</tr>

					<!-- ==================== Ex-Factory qty ====================== -->
	            	<tr bgcolor="<? echo $bgcolor ; ?>" id="tr_<? echo $i; ?>" style="cursor:pointer;">

	                	<td width="100">Ex-Factory Qty. </td>
	                	<?
	                	$tot = 0;
	                	foreach ($size_array as $s_key => $val)
	                	{
		                	$td_color = "";
		                	if($row[$s_key]['qty']<1)
		                	{
		                		$td_color = "";
		                	}
		                	elseif($row[$s_key]['qty']<=$row[$s_key][0][0]['qty'])
		                	{
		                		$td_color="green";
		                	}
		                	elseif ($row[$s_key]['qty']>$row[$s_key][0][0]['qty'] && $row[$s_key][0][0]['qty']>0)
		                	{
		                		$td_color="yellow";
		                	}
		                	else
		                	{
		                		$td_color="red";
		                	}
	                		?>
	                		<td bgcolor="<?=$td_color;?>" width="60" align="right"><?=number_format($row[$s_key][0][0]['qty'],0);?></td>
	                		<?
	                		$tot += $row[$s_key][0][0]['qty'];
	                	}
	                	?>
	                	<td width="80" align="right"><b><?=number_format($tot,0);?></b></td>
	            	</tr>

					<!-- ==================== Ex-Factory bal ====================== -->
	            	<tr bgcolor="<? echo $bgcolor ; ?>" id="tr_<? echo $i; ?>" style="cursor:pointer;">

	                	<td width="100">Ex-Factory Bal. </td>
	                	<?
	                	$tot = 0;
	                	foreach ($size_array as $s_key => $val)
	                	{
	                		$ballance = $row[$s_key]['qty'] - $row[$s_key][0][0]['qty'];
	                		$td_color = "";
		                	if($row[$s_key]['qty']<1)
		                	{
		                		$td_color = "";
		                	}
		                	elseif($row[$s_key]['qty']<=$ballance)
		                	{
		                		$td_color="green";
		                	}
		                	elseif ($row[$s_key]['qty']>$ballance && $ballance>0)
		                	{
		                		$td_color="yellow";
		                	}
		                	else
		                	{
		                		$td_color="red";
		                	}

	                		?>
	                		<td bgcolor="<?=$td_color;?>" width="60" align="right"><?=number_format($ballance,0);?></td>
	                		<?
	                		$tot += $ballance;
	                	}
	                	?>
	                	<td width="80" align="right"><b><?=number_format($tot,0);?></b></td>
	            	</tr>
	         		<?
			 	$i++;
			 	}
			}
			?>
	    	</table>
		</div>
	</fieldset>

    <?
    foreach (glob("$user_id*.xls") as $filename)
	{
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	?>
	<script type="text/javascript">
		document.getElementById('excel').href = "../requires/<?=$filename;?>";
	</script>
	<?
	exit();
}
?>