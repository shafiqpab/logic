<?
session_start();
include('../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if ($_SESSION['logic_erp']['user_id'] == "") {
    header("location:login.php");
    die;
}

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
//------------------------------------------------------------------------------------------------------
$country_library = return_library_array("select id,country_name from lib_country", "id", "country_name");
$company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
$floor_arr = return_library_array("select id,floor_name from lib_prod_floor", 'id', 'floor_name');
$location_arr = return_library_array("select id,location_name from lib_location", 'id', 'location_name');
$supplier_arr = return_library_array("select id, supplier_name from  lib_supplier", 'id', 'supplier_name');

if ($action == "get_challan_id") {
    echo return_field_value("id", "pro_gmts_delivery_mst", "sys_number='$data'");
    exit();
}

if ($action == "load_variable_settings") {
    echo "$('#sewing_production_variable').val(0);\n";
    $sql_result = sql_select("select printing_emb_production,production_entry from variable_settings_production where company_name=$data and variable_list=1 and status_active=1");
    foreach ($sql_result as $result) {
        echo "$('#sewing_production_variable').val(" . $result[csf("printing_emb_production")] . ");\n";
        echo "$('#styleOrOrderWisw').val(" . $result[csf("production_entry")] . ");\n";
    }

    echo "$('#delivery_basis').val(0);\n";
    $delivery_basis = return_field_value("cut_panel_delevery", "variable_settings_production", "company_name=$data and variable_list=32 and status_active=1 and is_deleted=0");
    if ($delivery_basis == 3 || $delivery_basis == 2)
        $delivery_basis = 3;
    else
        $delivery_basis = 1;
    echo "$('#delivery_basis').val(" . $delivery_basis . ");\n";

    echo "$('#embro_production_variable').val(0);\n";

    $sql_result = sql_select("select printing_emb_production from variable_settings_production where company_name=$data and variable_list=28 and status_active=1");
    foreach ($sql_result as $result) {
        echo "$('#embro_production_variable').val(" . $result[csf("printing_emb_production")] . ");\n";
    }
    exit();
}

if ($action == "load_variable_settings_for_working_company") {
    $sql_result = sql_select("select working_company_mandatory from variable_settings_production where company_name=$data and variable_list=41 and status_active=1");

    $working_company = "";
    foreach ($sql_result as $row) {
        $working_company = $row[csf("working_company_mandatory")];
    }
    echo $working_company;

    exit();
}


if ($action == "load_drop_down_body_part") {
    $sql = "select id,bundle_use_for from ppl_bundle_title where company_id=$data";
    echo create_drop_down("cbo_body_part", 180, $sql, "id,bundle_use_for", 1, "-- Select --", $selected, "");
    exit();
}

if ($action == "load_drop_down_working_location") {
    echo create_drop_down("cbo_working_location", 180, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name", "id,location_name", 1, "-- Select Location --", $selected, "", 0);
    exit();
}
/* if($action=="populate_data_from_search_popup")
  {
  $dataArr = explode("**",$data);
  $po_id = $dataArr[0];
  $item_id = $dataArr[1];
  if($dataArr[2]==0) $embel_name = "%%"; else $embel_name = $dataArr[2];
  $country_id = $dataArr[3];

  //echo "shajjad".$po_id;

  $res = sql_select("select a.id,a.po_quantity,a.plan_cut, a.po_number,a.po_quantity,b.company_name, b.buyer_name, b.style_ref_no,b.gmts_item_id, b.order_uom, b.job_no,b.location_name
  from wo_po_break_down a, wo_po_details_master b
  where a.job_no_mst=b.job_no and a.id=$po_id");
  //print_r($res);die;
  foreach($res as $result)
  {
  echo "$('#txt_order_no').val('".$result[csf('po_number')]."');\n";
  echo "$('#hidden_po_break_down_id').val('".$result[csf('id')]."');\n";
  echo "$('#cbo_buyer_name').val('".$result[csf('buyer_name')]."');\n";
  echo "$('#txt_style_no').val('".$result[csf('style_ref_no')]."');\n";

  $dataArray=sql_select("select SUM(CASE WHEN production_type=2 THEN production_quantity END) as totalcutting,SUM(CASE WHEN production_type=3 THEN production_quantity ELSE 0 END) as totalprinting from pro_garments_production_mst WHERE po_break_down_id=".$result[csf('id')]." and item_number_id='$item_id' and embel_name like '$embel_name' and country_id='$country_id' and is_deleted=0");
  foreach($dataArray as $row)
  {
  echo "$('#txt_issue_qty').val('".$row[csf('totalcutting')]."');\n";
  echo "$('#txt_cumul_receive_qty').attr('placeholder','".$row[csf('totalprinting')]."');\n";
  echo "$('#txt_cumul_receive_qty').val('".$row[csf('totalprinting')]."');\n";
  $yet_to_produced = $row[csf('totalcutting')]-$row[csf('totalprinting')];
  echo "$('#txt_yet_to_receive').attr('placeholder','".$yet_to_produced."');\n";
  echo "$('#txt_yet_to_receive').val('".$yet_to_produced."');\n";
  }
  }
  exit();
  } */


if($action=="load_mst_data_bundle_scan")
{
	//echo $data;die;
 	$ex_data = explode("_",$data);
	$bundle_no = trim("'".implode("','",explode(",",$ex_data[0]))."'");
	//$bundle_no=$ex_data[0];
	$challan_id=$ex_data[1];
	$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');

	$bundle_count=count(explode(",",$bundle_no)); $bundle_nos_cond="";
	if($db_type==2 && $bundle_count>400)
	{
		$bundle_nos_cond=" and (";
		$bundleArr=array_chunk(explode(",",$bundle_no),399);
		foreach($bundleArr as $bundleNos)
		{
			$bundleNos=implode(",",$bundleNos);
			$bundle_nos_cond.=" c.barcode_no in($bundleNos) or ";
		}
		$bundle_nos_cond=chop($bundle_nos_cond,'or ');
		$bundle_nos_cond.=")";
	}
	else
	{
		$bundle_nos_cond=" and c.barcode_no in ($bundle_no)";
	}

	$sql_mst_data=sql_select("select a.sys_number, a.company_id,a.location_id, a.embel_name, a.embel_type, a.serving_company,a.floor_id,a.organic,
	a.production_source, a.working_company_id,a.working_location_id
	from  pro_gmts_delivery_mst a,pro_garments_production_dtls c where a.id=c.delivery_mst_id and c.production_type=2
	and a.embel_name=4 and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and a.id=".$challan_id." $bundle_nos_cond
	group by a.sys_number, a.company_id,a.location_id, a.embel_name, a.embel_type, a.serving_company,a.floor_id,a.organic,a.production_source, a.working_company_id,a.working_location_id");

	//print_r($sql_mst_data);die;
	if(count($sql_mst_data)>0)
	{
		foreach($sql_mst_data as $val)
		{
			if($val[csf('production_source')]==1) {$serv_comp=$company_arr[$val[csf('serving_company')]]; }
			else { $serv_comp=$supplier_arr[$val[csf('serving_company')]];}
			$location=$location_arr[$val[csf('location_id')]];
			$floor=$floor_arr[$val[csf('floor_id')]];
			echo "$('#txt_issue_challan_scan').val('".$val[csf('sys_number')]."');\n";
			echo "$('#txt_issue_challan_id').val('".$challan_id."');\n";
			echo "$('#cbo_embel_type').val(".$val[csf('embel_type')].");\n";
			echo "$('#cbo_source').val('".$val[csf('production_source')]."');\n";
			echo "$('#txt_embl_company').val('".$serv_comp."');\n";
			echo "$('#txt_embl_company_id').val(".$val[csf('serving_company')].");\n";
			echo "$('#txt_location_name').val('".$location."');\n";
			echo "$('#txt_floor_name').val('".$floor."');\n";
			echo "$('#txt_organic').val('".$val[csf('organic')]."');\n";
			echo "$('#txt_floor_id').val(".$val[csf('floor_id')].");\n";
			echo "$('#txt_location_id').val(".$val[csf('location_id')].");\n";
			echo "$('#cbo_working_company_name').val(".$val[csf('working_company_id')].");\n";
			echo "load_drop_down( 'requires/embrodary_challan_receive_controller', ".$val[csf('working_company_id')].", 'load_drop_down_working_location', 'working_location_td' );\n" ;
			echo "$('#cbo_working_location').val(".$val[csf('working_location_id')].");\n";

			echo "disable_enable_fields('cbo_company_name*txt_location_name*txt_floor_name',1);\n";
		}
	}
	else
		echo "alert('All Bundle must be under Selected Embel. Company, Location, Floor. Please Check');\n";

	exit();
}

if($action=="bundle_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	//echo $issue_challan_id;die;
?>
	<script>


		var challan_id='<? echo $issue_challan_id; ?>';

		function check_all_data() {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				if($("#search"+i).css("display") !='none'){
				 js_set_value( i );
				}
			}
		}


		var selected_id = new Array();

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		var previous_challan_id='<?php echo $issue_challan_id;?>';
		function js_set_value( str)
		{

			var cur_challan_id=$('#txt_challan_id' + str).val();

			if((previous_challan_id=="" || selected_id.length==0) && challan_id=="")
			{
				previous_challan_id=cur_challan_id;
			}
			else
			{
				if(trim(previous_challan_id)!=trim(cur_challan_id))
				{
					alert("Issue Challan Mix not Allowed");
					return;
				}
			}

			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

			if( jQuery.inArray( $('#txt_individual' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual' + str).val() );

			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );

			$('#hidden_bundle_nos').val( id );
			$('#hidden_issue_challan_id').val(previous_challan_id);
		}

		function fnc_close()
		{
			parent.emailwindow.hide();
		}

		function reset_hide_field()
		{
			$('#hidden_bundle_nos').val( '' );
			selected_id = new Array();
		}

    </script>
</head>
<body>
<div align="center" style="width:100%;" >
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:810px;">
		<legend>Enter search words <input type="checkbox" value="1" name="is_exact" id="is_exact" checked> is exact</legend>
            <table cellpadding="0" cellspacing="0" width="550" border="1" rules="all" class="rpt_table">
                <thead>
                    <th>Cut Year</th>
                    <th>Job No</th>
                    <th>Order No</th>
                    <th class="must_entry_caption">Cut No</th>
                    <th>Bundle No</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        <input type="hidden" name="hidden_bundle_nos" id="hidden_bundle_nos">
                        <input type="hidden" name="hidden_issue_challan_id" id="hidden_issue_challan_id">
                    </th>
                </thead>
                <tr class="general">
                    <td align="center">
                    <?
						echo create_drop_down( "cbo_cut_year", 60, $year,'', "", '-- Select --',date("Y",time()), "",'','','','' );
					?>
                    </td>
                    <td align="center">
                        <input type="text" style="width:130px" class="text_boxes" name="txt_job_no" id="txt_job_no" />
                    </td>
                    <td align="center" id="search_by_td">
                        <input type="text" style="width:130px" class="text_boxes" name="txt_order_no" id="txt_order_no" />
                    </td>
                    <td><input type="text" name="txt_cut_no" id="txt_cut_no" style="width:120px" class="text_boxes" /></td>
                    <td><input type="text" name="bundle_no" id="bundle_no" style="width:120px" class="text_boxes" /></td>
            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('txt_order_no').value+'_'+<? echo $company; ?>+'_'+document.getElementById('bundle_no').value+'_'+'<? echo trim($bundleNo,','); ?>'+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_cut_no').value+'_'+document.getElementById('cbo_cut_year').value+'_'+$('#is_exact').is(':checked')+'_'+'<? echo trim($issue_challan_id,','); ?>', 'create_bundle_search_list_view', 'search_div', 'special_work_challan_receive_controller', 'setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')" style="width:100px;" />
                     </td>
                </tr>
           </table>
           <div style="width:100%; margin-top:5px; margin-left:10px" id="search_div" align="left"></div>
		</fieldset>
	</form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_bundle_search_list_view")
{
 	$ex_data = explode("_",$data);
	$txt_order_no = "%".trim($ex_data[0])."%";
	$company = $ex_data[1];
	//$bundle_no = "%".trim($ex_data[2])."%";

	if(trim($ex_data[2])){$bundle_no = "".trim($ex_data[2])."";}
	else{$bundle_no = "%".trim($ex_data[2])."%";}

	$selectedBuldle=$ex_data[3];
	$job_no=$ex_data[4];
	$cut_no=$ex_data[5];
	$syear = substr($ex_data[6],2);
	$is_exact=$ex_data[7];
	$previous_issue_id=$ex_data[8];

	$company_short_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	if(trim($ex_data[5])==''){echo "<h2 style='color:#D00; text-align:center;'>Please Select-Cut No</h2>";exit();}

	//list($short_name)=explode('-',$company_short_arr[$company]);
	$cutConvertToInt= convertToInt('c.cut_no',array($company_short_arr[$company],'-'),'cut_no');
	$bundleConvertToInt = convertToInt('c.bundle_no',array($company_short_arr[$company],'-',"/"),'order_bundle_no');

	$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	$challan_arr=return_library_array( "select id, sys_number from pro_gmts_delivery_mst where status_active=1 and is_deleted=0 ",'id','sys_number');



	if ($cut_no != '')
	{
		if($is_exact=='true')
		{
			$cutCon = " and c.cut_no = '".trim($company_short_arr[$company].'-'.$syear.'-'.str_pad($cut_no,6,"0",STR_PAD_LEFT))."'";
			$cutCon_a = " and b.cut_no = '".trim($company_short_arr[$company].'-'.$syear.'-'.str_pad($cut_no,6,"0",STR_PAD_LEFT))."'";
		}
		else
		{
			$cutCon = " and c.cut_no like '%".$cut_no."'";
			$cutCon_a = " and b.cut_no like '%".$cut_no."'";
		}
    }
	if($job_no!='') $jobCon=" and f.job_no_prefix_num = $job_no"; else $jobCon="";

	$scanned_bundle_arr=return_library_array( "select b.bundle_no, b.bundle_no from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and b.production_type=3 and a.embel_name=4  $cutCon_a  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0",'bundle_no','bundle_no');

	foreach(explode(",",$selectedBuldle) as $bn)
	{
		$scanned_bundle_arr[$bn]=$bn;
	}


	$sql="SELECT c.cut_no, c.bundle_no, d.po_break_down_id, d.job_no_mst, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.barcode_no, c.production_qnty as qty, e.po_number,c.delivery_mst_id from pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e ,wo_po_details_master f where d.job_no_mst=f.job_no and  a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and a.company_id=$company and e.po_number like '$txt_order_no' and c.bundle_no like '$bundle_no' $jobCon $cutCon and c.production_type=2 and a.embel_name=4 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and e.status_active in(1,2,3) and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0  order by c.cut_no, length(c.bundle_no) asc, c.bundle_no asc";
	//echo $sql;
	//and c.is_rescan=0
	 //echo $sql;
	//print_r($scanned_bundle_sql[]);die;
	$result = sql_select($sql);

	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="930" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="40">Year</th>
            <th width="50">Job No</th>
            <th width="90">Order No</th>
            <th width="100">Challan No</th>
            <th width="130">Gmts Item</th>
            <th width="110">Country</th>
            <th width="100">Color</th>
            <th width="50">Size</th>
            <th width="70">Cut No</th>
            <th width="80">Bundle No</th>
            <th>Bundle Qty.</th>
        </thead>
	</table>
	<div style="width:950px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="930" class="rpt_table" id="tbl_list_search">
        <?
            $i=1;
            foreach ($result as $row)
            {
				if($scanned_bundle_arr[$row[csf('bundle_no')]]=="")
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					list($shortName,$year,$job)=explode('-',$row[csf('job_no_mst')]);
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)">
						<td width="40">
							<? echo $i; ?>
							 <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
                             <input type="hidden" name="txt_challan_id" id="txt_challan_id<?php echo $i; ?>" value="<?php echo $row[csf('delivery_mst_id')]; ?>"/>
						</td>
						<td width="40" align="center"><p><? echo $year; ?></p></td>
						<td width="50" align="center"><p><? echo $job*1; ?></p></td>
						<td width="90"><p><? echo $row[csf('po_number')]; ?></p></td>
                        <td width="100"><p><? echo $challan_arr[$row[csf('delivery_mst_id')]]; ?></p></td>
						<td width="130"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
                        <td width="110"><p><? echo $country_arr[$row[csf('country_id')]]; ?></p></td>
                        <td width="100"><p><? echo $color_arr[$row[csf('color_number_id')]]; ?></p></td>
                        <td width="50"><p><? echo $size_arr[$row[csf('size_number_id')]]; ?></p></td>
						<td width="70"><? echo $row[csf('cut_no')]; ?></td>
						<td width="80"><? echo $row[csf('bundle_no')]; ?></td>
						<td align="right"><? echo $row[csf('qty')]; ?>&nbsp;&nbsp;</td>
					</tr>
				<?
					$i++;
				}
			}
        	?>
        </table>
    </div>
    <table width="830">
        <tr>
            <td align="center" >
                <span  style="float:left;"> <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All</span>
                <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
            </td>
        </tr>
    </table>
<?
	exit();
}

if($action=="challan_duplicate_check")
{
	$data=explode("__",$data);
	$bundle_no="'".implode("','",explode(",",$data[0]))."'";
	$msg=1;
	$bundle_count=count(explode(",",$bundle_no)); $bundle_nos_cond="";
	if($db_type==2 && $bundle_count>400)
	{
		$bundle_nos_cond=" and (";
		$bundleArr=array_chunk(explode(",",$bundle_no),399);
		foreach($bundleArr as $bundleNos)
		{
			$bundleNos=implode(",",$bundleNos);
			$bundle_nos_cond.=" b.barcode_no in($bundleNos) or ";
		}
		$bundle_nos_cond=chop($bundle_nos_cond,'or ');
		$bundle_nos_cond.=")";
	}
	else
	{
		$bundle_nos_cond=" and b.barcode_no in ($bundle_no)";
	}

	$result=sql_select("select a.sys_number,b.barcode_no,b.bundle_no,a.issue_challan_id from pro_gmts_delivery_mst a,pro_garments_production_dtls b where a.id=b.delivery_mst_id  and b.production_type=3 and a.embel_name=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bundle_nos_cond
	 group by a.sys_number, b.barcode_no,b.bundle_no,a.issue_challan_id");

	 //and b.is_rescan=0
	$datastr="";
	//$receive_bundle=array();
	$issed_bundle_challan='';
	if(count($result)>0)
	{
		foreach ($result as $row)
		{
			$msg=2;
			if($datastr!="") $datastr.=",";
			$datastr.=$row[csf('bundle_no')]."*".$row[csf('sys_number')]."*".$row[csf('barcode_no')];
			//$receive_bundle[$row[csf('bundle_no')]][]=$row[csf('issue_challan_id')];
			if($issed_bundle_challan!="") $issed_bundle_challan.=",";
			$issed_bundle_challan.=$row[csf('issue_challan_id')];
		}
	}
	if($issed_bundle_challan!="") $issue_challan_cond=" and a.id not in(".$issed_bundle_challan.")"; else $issue_challan_cond='';

	$issue_result=sql_select("select a.id,a.sys_number,b.bundle_no,b.barcode_no from pro_gmts_delivery_mst a,pro_garments_production_dtls b where a.id=b.delivery_mst_id and b.production_type=2 and a.embel_name=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bundle_nos_cond $issue_challan_cond group by a.sys_number, b.bundle_no,b.barcode_no,a.id");
	if(count($issue_result)>0)
	{
		$datastr='';
		foreach ($issue_result as $row)
		{
			$msg=1;
			if($datastr!="") $datastr.=",";
			$datastr.=$row[csf('id')]."*".$row[csf('sys_number')]."*".$row[csf('bundle_no')]."*".$row[csf('barcode_no')];
		}
	}

	echo rtrim($msg)."_".rtrim($datastr);
	exit();
}




if($action=="populate_bundle_data")
{
 	$ex_data = explode("**",$data);
	$bundle=explode(",",$ex_data[0]);
	$mst_id=$ex_data[2];
	$bundle_nos="'".trim(implode("','",$bundle))."'";

	$bundle_count=count(explode(",",$bundle_nos)); $bundle_nos_cond=""; $recbundle_nos_cond="";
	if($db_type==2 && $bundle_count>400)
	{
		$recbundle_nos_cond=" and (";
		$bundle_nos_cond=" and (";
		$bundleArr=array_chunk(explode(",",$bundle_nos),399);
		foreach($bundleArr as $bundleNos)
		{
			$bundleNos=implode(",",$bundleNos);
			$recbundle_nos_cond.=" c.barcode_no in($bundleNos) or ";
			$bundle_nos_cond.=" b.barcode_no in($bundleNos) or ";
		}
		$recbundle_nos_cond=chop($recbundle_nos_cond,'or ');
		$recbundle_nos_cond.=")";

		$bundle_nos_cond=chop($bundle_nos_cond,'or ');
		$bundle_nos_cond.=")";
	}
	else
	{
		$recbundle_nos_cond=" and c.barcode_no in ($bundle_nos)";
		$bundle_nos_cond=" and b.barcode_no in ($bundle_nos)";
	}

	$scanned_bundle_arr=return_library_array( "select b.bundle_no, b.bundle_no from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and b.production_type=3 and a.embel_name=4 and b.status_active=1 and b.is_deleted=0 $bundle_nos_cond",'bundle_no','bundle_no');
	//print_r($scanned_bundle_arr);
	$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');

	$year_field="";
	if($db_type==0)
	{
		$year_field="YEAR(f.insert_date)";
	}
	else if($db_type==2)
	{
		$year_field="to_char(f.insert_date,'YYYY')";
	}

	 if($mst_id!="") $challan_cond=" and a.id=".$mst_id.""; else $challan_cond=" and c.is_rescan=0";

	$sql="SELECT max(c.id) as prdid , d.id as colorsizeid, e.id as po_id, f.job_no_prefix_num, MAX($year_field) as year, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id,c.cut_no, c.bundle_no,c.barcode_no, c.production_qnty as qty, e.po_number from pro_gmts_delivery_mst a,pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f where f.company_name=$ex_data[3] and a.id=c.delivery_mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_no_mst=f.job_no and c.production_type=2 and a.embel_name=4 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and e.status_active in(1,2,3) and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $recbundle_nos_cond $challan_cond group by d.id, e.id, f.job_no_prefix_num, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id,c.cut_no, c.bundle_no,c.barcode_no, c.production_qnty, e.po_number order by c.cut_no, length(c.bundle_no) asc, c.bundle_no asc";


	$result = sql_select($sql);
	$count=count($result);
	$i=$ex_data[1]+$count;
	foreach ($result as $row)
	{
		if($scanned_bundle_arr[$row[csf('bundle_no')]]=="" ) //|| $mst_id[0]!=""
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i;?>">
            	<td width="30"><? echo $i; ?></td>
                <td width="80" id="bundle_<? echo $i; ?>" title="<? echo $row[csf('barcode_no')]; ?>"><? echo $row[csf('bundle_no')]; ?></td>
                <td width="45" align="center"><? echo $row[csf('year')]; ?></td>
                <td width="50" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                <td width="65"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
                <td width="90" style="word-break:break-all;" align="left"><? echo $row[csf('po_number')]; ?></td>
                <td width="100" style="word-break:break-all;" align="left"><? echo $garments_item[$row[csf('item_number_id')]]; ?></td>
                <td width="80" style="word-break:break-all;" align="left"><? echo $country_arr[$row[csf('country_id')]]; ?></td>
                <td width="80" style="word-break:break-all;" align="left"><? echo $color_arr[$row[csf('color_number_id')]]; ?></td>
                <td width="60" style="word-break:break-all;" align="left">&nbsp;<? echo $size_arr[$row[csf('size_number_id')]]; ?></td>
                <td width="50" id="prodQty_<? echo $i; ?>" align="right"><? echo $row[csf('qty')]; ?>&nbsp;</td>

                <td width="50" id="RejQty_<? echo $i; ?>" align="right">
                <input type="text" class="text_boxes_numeric" name="rejectQty[]" id="rejectQty_<? echo $i; ?>" style="width:40px" onBlur="calculate_qcpasss(<? echo $i; ?>)" onDblClick="pop_entry_reject(<? echo $i; ?>)"/>

				<input type="hidden" name="actual_reject[]"  id="actual_reject_<? echo $i; ?>"  value="" />
                </td>

                <td width="50" id="RepQty_<? echo $i; ?>" align="right"><input type="text" class="text_boxes_numeric" name="replaceQty[]" id="replaceQty_<? echo $i; ?>" style="width:40px" onBlur="calculate_qcpasss_rescan(<? echo $i; ?>)" /></td>
                <td width="50" id="QcQty_<? echo $i; ?>" align="right"><? echo $row[csf('qty')]; ?>&nbsp;</td>
                <td id="button_1" align="center">

                    <input type="hidden" name="cutNo[]" id="cutNo_<? echo $i; ?>" value="<? echo $row[csf('cut_no')]; ?>"/>

                    <input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
                    <input type="hidden" name="colorSizeId[]" id="colorSizeId_<? echo $i; ?>" value="<? echo $row[csf('colorsizeid')]; ?>"/>
                    <input type="hidden" name="orderId[]" id="orderId_<? echo $i; ?>" value="<? echo $row[csf('po_id')]; ?>"/>
                    <input type="hidden" name="gmtsitemId[]" id="gmtsitemId_<? echo $i; ?>" value="<? echo $row[csf('item_number_id')]; ?>"/>
                    <input type="hidden" name="countryId[]" id="countryId_<? echo $i; ?>" value="<? echo $row[csf('country_id')]; ?>"/>
                    <input type="hidden" name="colorId[]" id="colorId_<? echo $i; ?>" value="<? echo $row[csf('color_number_id')]; ?>"/>
                    <input type="hidden" name="sizeId[]" id="sizeId_<? echo $i; ?>" value="<? echo $row[csf('size_number_id')]; ?>"/>
                    <input type="hidden" name="qty[]" id="qty_<? echo $i; ?>" value="<? echo $row[csf('qty')]; ?>"/>
                    <input type="hidden" name="prod_qty[]" id="prod_qty_<? echo $i; ?>" value="<? echo $row[csf('qty')]; ?>"/>
                    <input type="hidden" name="dtlsId[]" id="dtlsId_<? echo $i; ?>" value="<? echo $row[csf('prdid')]; ?>" />
                    <input type="hidden" name="isRescan[]" id="isRescan_<? echo $i; ?>" value="0"/>
                </td>
			</tr>
			<?
        	$i--;
		}
	}
	exit();
}

// new need
if ($action == "populate_bundle_data_update")
{
 	$ex_data = explode("**",$data);
	$bundle=explode(",",$ex_data[0]);
	//$mst_id=explode(",",$ex_data[2]);
	$mst_id=$ex_data[2];
	$company_id=$ex_data[3];
	$issue_challan_id=$ex_data[4];
	$bundle_nos="'".implode("','",$bundle)."'";

	$bundle_count=count(explode(",",$bundle_nos)); $bundle_nos_cond="";
	if($db_type==2 && $bundle_count>400)
	{
		$bundle_nos_cond=" and (";
		$bundleArr=array_chunk(explode(",",$bundle_nos),399);
		foreach($bundleArr as $bundleNos)
		{
			$bundleNos=implode(",",$bundleNos);
			$bundle_nos_cond.=" c.barcode_no in($bundleNos) or ";
		}
		$bundle_nos_cond=chop($bundle_nos_cond,'or ');
		$bundle_nos_cond.=")";
	}
	else
	{
		$bundle_nos_cond=" and c.barcode_no in ($bundle_nos)";
	}

	$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');

	$year_field="";
	if($db_type==0)
	{
		$year_field="YEAR(f.insert_date)";
	}
	else if($db_type==2)
	{
		$year_field="to_char(f.insert_date,'YYYY')";
	}


	$issue_sql="select c.bundle_no,c.barcode_no, c.production_qnty as qty from pro_garments_production_mst a,pro_garments_production_dtls c where a.id=c.mst_id $bundle_nos_cond and c.delivery_mst_id=$issue_challan_id and c.production_type=2 and a.embel_name=4 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0";

	$issue_result = sql_select($issue_sql);
	foreach ($issue_result as $row){
		$issue_qty_arr[$row[csf('barcode_no')]]+=$row[csf('qty')];
	}

	$receive_sql="select c.barcode_no, sum(c.production_qnty) as qty from pro_gmts_delivery_mst a,pro_garments_production_dtls c where a.id=c.delivery_mst_id  $bundle_nos_cond and c.production_type=3 and a.embel_name=4 and a.issue_challan_id=$issue_challan_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.barcode_no";

	$issue_result = sql_select($receive_sql);
	foreach ($issue_result as $row){
		$pre_receive_qty_arr[$row[csf('barcode_no')]]+=$row[csf('qty')];
	}

	//print_r($issue_qty_arr);die;
	$company_short_arr=return_library_array( "select id, company_short_name from lib_company where id=$company_id",'id','company_short_name');
	//$bundleConvertToInt = convertToInt('c.bundle_no',array($company_short_arr[$company_id],'-',"/"),'order_bundle_no');
	$sql="SELECT (c.id) as prdid,c.bundle_no,d.id as colorsizeid, e.id as po_id, f.job_no_prefix_num, $year_field as year, f.buyer_name, d.item_number_id, d.country_id,c.reject_qty, d.size_number_id, d.color_number_id,c.cut_no, c.barcode_no, c.production_qnty as qty,c.replace_qty, e.po_number, c.is_rescan from pro_garments_production_mst a,pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f where a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_no_mst=f.job_no $bundle_nos_cond and c.production_type=3 and a.embel_name=4 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and e.status_active in(1,2,3) and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and c.delivery_mst_id=".$mst_id." order by c.cut_no, length(c.bundle_no) asc, c.bundle_no asc";
	  // echo $sql; die;
	$result = sql_select($sql);
	$count=count($result);
	$i=$ex_data[1]+$count;
	foreach ($result as $row)
	{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			if($row[csf("replace_qty")]>0)
			{
				 $production_qty=($issue_qty_arr[$row[csf('barcode_no')]]-$pre_receive_qty_arr[$row[csf('barcode_no')]]+$row[csf("replace_qty")]);
				 $disble_rej="disabled";
				 $disable_replace="";
			}
			else if($row[csf("reject_qty")]>0)
			{
				$production_qty=$row[csf('qty')]+$row[csf('reject_qty')];
				$disble_rej="";
				$disable_replace="disabled";
			}
			else
			{
				$production_qty=$row[csf('qty')];
				$disble_rej="";
				$disable_replace="disabled";
			}
		?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i;?>">
            	<td width="30"><? echo $i; ?></td>
                <td width="80" id="bundle_<? echo $i; ?>" title="<? echo $row[csf('barcode_no')]; ?>"><? echo $row[csf('bundle_no')]; ?></td>
                <td width="45" align="center"><? echo $row[csf('year')]; ?></td>
                <td width="50" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                <td width="65"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
                <td width="90" style="word-break:break-all;" align="left"><? echo $row[csf('po_number')]; ?></td>
                <td width="100" style="word-break:break-all;" align="left"><? echo $garments_item[$row[csf('item_number_id')]]; ?></td>
                <td width="80" style="word-break:break-all;" align="left"><? echo $country_arr[$row[csf('country_id')]]; ?></td>
                <td width="80" style="word-break:break-all;" align="left"><? echo $color_arr[$row[csf('color_number_id')]]; ?></td>
                <td width="60" style="word-break:break-all;" align="left">&nbsp;<? echo $size_arr[$row[csf('size_number_id')]]; ?></td>
                <td width="50" id="prodQty_<? echo $i; ?>" align="right">
				<?
					 echo $production_qty;//$row[csf('qty')];
				?>
                &nbsp;</td>
                <td width="50" id="RejQty_<? echo $i; ?>" align="right">
                <input type="text" class="text_boxes_numeric" name="rejectQty[]" id="rejectQty_<? echo $i; ?>" style="width:40px" value="<? echo $row[csf('reject_qty')];?>" onBlur="calculate_qcpasss(<? echo $i; ?>)" <?php echo $disble_rej; ?> onDblClick="pop_entry_reject(<? echo $i; ?>)"/>

				<input type="hidden" name="actual_reject[]"  id="actual_reject_<? echo $i; ?>"  value="" />
                </td>

                <td width="50" id="RepQty_<? echo $i; ?>" align="right"><input type="text" class="text_boxes_numeric" name="replaceQty[]" id="replaceQty_<? echo $i; ?>" style="width:40px" value="<? echo $row[csf('replace_qty')];?>" onBlur="calculate_qcpasss_rescan(<? echo $i; ?>)" <?php echo $disable_replace; ?>/></td>
                <td width="50" id="QcQty_<? echo $i; ?>" align="right"><? echo $row[csf('qty')]; ?>&nbsp;</td>
                <td id="button_<? echo $i; ?>" align="center">

                    <input type="hidden" name="cutNo[]" id="cutNo_<? echo $i; ?>" value="<? echo $row[csf('cut_no')]; ?>"/>

                    <input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
                    <input type="hidden" name="colorSizeId[]" id="colorSizeId_<? echo $i; ?>" value="<? echo $row[csf('colorsizeid')]; ?>"/>
                    <input type="hidden" name="orderId[]" id="orderId_<? echo $i; ?>" value="<? echo $row[csf('po_id')]; ?>"/>
                    <input type="hidden" name="gmtsitemId[]" id="gmtsitemId_<? echo $i; ?>" value="<? echo $row[csf('item_number_id')]; ?>"/>
                    <input type="hidden" name="countryId[]" id="countryId_<? echo $i; ?>" value="<? echo $row[csf('country_id')]; ?>"/>
                    <input type="hidden" name="colorId[]" id="colorId_<? echo $i; ?>" value="<? echo $row[csf('color_number_id')]; ?>"/>
                    <input type="hidden" name="sizeId[]" id="sizeId_<? echo $i; ?>" value="<? echo $row[csf('size_number_id')]; ?>"/>
                    <input type="hidden" name="qty[]" id="qty_<? echo $i; ?>" value="<? echo $row[csf('qty')]; ?>"/>
                    <input type="hidden" name="prod_qty[]" id="prod_qty_<? echo $i; ?>" value="<? echo $production_qty;//$row[csf('qty')]; ?>"/>
                    <input type="hidden" name="dtlsId[]" id="dtlsId_<? echo $i; ?>" value="<? echo $row[csf('prdid')]; ?>"/>
                    <input type="hidden" name="isRescan[]" id="isRescan_<? echo $i; ?>" value="<? echo $row[csf('is_rescan')]; ?>"/>
                </td>
			</tr>
		<?
        	$i--;

	}
	exit();
}

// new need ********************************************************************************************************************************
if ($action == "bundle_nos")
{
    $bundle_nos = return_library_array("select b.barcode_no, b.barcode_no from pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id and a.delivery_mst_id=$data and b.production_type=2 and a.embel_name=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0", "barcode_no", "barcode_no");
    $bundle_nos = implode(",", $bundle_nos);
    echo $bundle_nos;
    exit();
}

if ($action == "bundle_nos_update")
{
   $bundle_sql="SELECT b.barcode_no from pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id and a.delivery_mst_id=$data and b.production_type=3 and a.embel_name=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
	foreach(sql_select($bundle_sql) as $vals)
	{
		$bundle_no_arr[$vals[csf("barcode_no")]]=$vals[csf("barcode_no")];
	}
	$bundle_nos=implode(",",$bundle_no_arr);
	echo $bundle_nos;
	exit();
}


if($action=="bundle_popup_rescan")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
?>
	<script>


		var challan_id='<? echo $issue_challan_id; ?>';
		function check_all_data() {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				if($("#search"+i).css("display") !='none'){
				 js_set_value( i );
				}
			}
		}


		var selected_id = new Array();

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		var previous_challan_id='<?php echo trim($issue_challan_id);?>';
		//alert(previous_challan_id);
		function js_set_value( str)
		{
			var cur_challan_id=$('#txt_challan_id' + str).val();

			if((previous_challan_id=="" || selected_id.length==0) && challan_id=="")
			{
				previous_challan_id=cur_challan_id;
			}
			else
			{
				if(previous_challan_id!=cur_challan_id && cur_challan_id!=undefined)
				{
					alert("Issue Challan Mix not Allowed");
					return;
				}
			}

			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

			if( jQuery.inArray( $('#txt_individual' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual' + str).val() );

			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );

			$('#hidden_bundle_nos').val( id );
			$('#hidden_issue_challan_id').val(previous_challan_id);
			//alert(previous_challan_id);
		}

		function fnc_close()
		{
			parent.emailwindow.hide();
		}

		function reset_hide_field()
		{
			$('#hidden_bundle_nos').val( '' );
			selected_id = new Array();
		}

    </script>
</head>
<body>
<div align="center" style="width:100%;" >
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:810px;">
		<legend>Enter search words <input type="checkbox" value="1" name="is_exact" id="is_exact" checked> is exact</legend>
            <table cellpadding="0" cellspacing="0" width="550" border="1" rules="all" class="rpt_table">
                <thead>
                    <th>Cut Year</th>
                    <th>Job No</th>
                    <th>Order No</th>
                    <th class="must_entry_caption">Cut No</th>
                    <th>Bundle No</th>
                    <th>Issue Challan No</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        <input type="hidden" name="hidden_bundle_nos" id="hidden_bundle_nos">
                        <input type="hidden" name="hidden_issue_challan_id" id="hidden_issue_challan_id">
                    </th>
                </thead>
                <tr class="general">
                    <td align="center">
                    <?
						echo create_drop_down( "cbo_cut_year", 60, $year,'', "", '-- Select --',date("Y",time()), "",'','','','' );
					?>
                    </td>
                    <td align="center">
                        <input type="text" style="width:130px" class="text_boxes" name="txt_job_no" id="txt_job_no" />
                    </td>
                    <td align="center" id="search_by_td">
                        <input type="text" style="width:130px" class="text_boxes" name="txt_order_no" id="txt_order_no" />
                    </td>
                    <td><input type="text" name="txt_cut_no" id="txt_cut_no" style="width:120px" class="text_boxes" /></td>
                    <td><input type="text" name="bundle_no" id="bundle_no" style="width:120px" class="text_boxes" /></td>
                    <td><input type="text" name="issue_challan_search" id="issue_challan_search" style="width:120px" class="text_boxes" /></td>
            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('txt_order_no').value+'_'+<? echo $company; ?>+'_'+document.getElementById('bundle_no').value+'_'+'<? echo trim($bundleNo,','); ?>'+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_cut_no').value+'_'+document.getElementById('cbo_cut_year').value+'_'+$('#is_exact').is(':checked')+'_'+document.getElementById('issue_challan_search').value, 'create_rescan_bundle_search_list_view', 'search_div', 'special_work_challan_receive_controller', 'setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')" style="width:100px;" />
                     </td>
                </tr>
           </table>
           <div style="width:100%; margin-top:5px; margin-left:10px" id="search_div" align="left"></div>
		</fieldset>
	</form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_rescan_bundle_search_list_view")
{
 	$ex_data = explode("_",$data);
	$txt_order_no = "%".trim($ex_data[0])."%";
	$company = $ex_data[1];
	//$bundle_no = "%".trim($ex_data[2])."%";

	if(trim($ex_data[2])){$bundle_no = "".trim($ex_data[2])."";}
	else{$bundle_no = "%".trim($ex_data[2])."%";}

	$selectedBuldle=$ex_data[3];
	$job_no=$ex_data[4];
	$cut_no=$ex_data[5];
	$syear = substr($ex_data[6],2);
	$is_exact=$ex_data[7];

	$company_short_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	if(trim($ex_data[5])==''){echo "<h2 style='color:#D00; text-align:center;'>Please Select-Cut No</h2>";exit();}
	foreach(explode(",",$selectedBuldle) as $bn)
	{
		$scanned_bundle_arr[$bn]=$bn;
	}
	//list($short_name)=explode('-',$company_short_arr[$company]);
	$cutConvertToInt= convertToInt('c.cut_no',array($company_short_arr[$company],'-'),'cut_no');
	$bundleConvertToInt = convertToInt('c.bundle_no',array($company_short_arr[$company],'-',"/"),'order_bundle_no');

	$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');

	if ($cut_no != '') {
		if($is_exact=='true') $cutCon = " and c.cut_no = '".trim($company_short_arr[$company].'-'.$syear.'-'.str_pad($cut_no,6,"0",STR_PAD_LEFT))."'";
		else $cutCon = " and c.cut_no like '%".$cut_no."'";
    }
	if($job_no!='') $jobCon=" and f.job_no_prefix_num = $job_no"; else $jobCon="";

	$sql=" SELECT c.cut_no, c.bundle_no,c.barcode_no, d.po_break_down_id, d.job_no_mst, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id,
 sum(c.production_qnty) as qty, e.po_number,de.id from pro_gmts_delivery_mst de, pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e ,wo_po_details_master f where d.job_no_mst=f.job_no and de.id=a.delivery_mst_id and de.id=c.delivery_mst_id and  a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and a.company_id=$company and e.po_number like '$txt_order_no' and c.bundle_no like '$bundle_no' $jobCon $cutCon and c.production_type=2 and a.embel_name=4 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and d.status_active in(1,2,3) and c.is_deleted=0 and e.status_active in(1,2,3)  and e.is_deleted=0  group by c.cut_no, c.bundle_no,c.barcode_no, d.po_break_down_id, d.job_no_mst, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id,e.po_number,de.id  order by c.cut_no, length(c.bundle_no) asc, c.bundle_no asc";
	//echo $sql;
	//having sum(c.reject_qty-c.replace_qty)>0
	$result = sql_select($sql);
	foreach($result as $value)
	{
		if(!empty($value[csf('id')]))
		{
			$issue_qty_arr[$value[csf('id')]][$value[csf('barcode_no')]]=$value[csf('qty')];
			$issue_barcode_arr[]=$value[csf('barcode_no')];
			$issue_challan_arr[]=$value[csf('id')];
		}
	}
	//print_r($issue_qty_arr);die;
	$issue_id_all=implode(",",array_unique($issue_challan_arr));
	//$ex_data = implode("','", explode(",", $data));
	$issue_barcode_all="'".implode("','",array_unique($issue_barcode_arr))."'";

	$bundle_count=count(explode(",",$issue_barcode_all)); $bundle_nos_cond="";
	if($db_type==2 && $bundle_count>400)
	{
		$bundle_nos_cond=" and (";
		$bundleArr=array_chunk(explode(",",$issue_barcode_all),399);
		foreach($bundleArr as $bundleNos)
		{
			$bundleNos=implode(",",$bundleNos);
			$bundle_nos_cond.=" c.barcode_no in($bundleNos) or ";
		}
		$bundle_nos_cond=chop($bundle_nos_cond,'or ');
		$bundle_nos_cond.=")";
	}
	else
	{
		$bundle_nos_cond=" and c.barcode_no in ($issue_barcode_all)";
	}
	$receive_sql="select c.barcode_no,a.issue_challan_id , sum(c.production_qnty) as qty from pro_gmts_delivery_mst  a,pro_garments_production_dtls c where  a.id=c.delivery_mst_id $bundle_nos_cond and c.production_type=3 and a.embel_name=4 and c.status_active=1 and c.is_deleted=0 group by c.barcode_no,a.issue_challan_id";
	//echo $receive_sql;die;
	$receive_result = sql_select($receive_sql);
	foreach ($receive_result as $row){
		$receive_qty_arr[$row[csf('issue_challan_id')]][$row[csf('barcode_no')]]=$row[csf('qty')];
		$receive_barcode_arr[]=$row[csf('barcode_no')];
	}
	$challan_arr=return_library_array( "select id, sys_number from pro_gmts_delivery_mst where id in ($issue_id_all) and status_active=1 and is_deleted=0 ",'id','sys_number');
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="950" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="50">Year</th>
            <th width="50">Job No</th>
            <th width="90">Order No</th>
            <th width="100">Challan No</th>
            <th width="130">Gmts Item</th>
            <th width="110">Country</th>
            <th width="100">Color</th>
            <th width="50">Size</th>
            <th width="80">Cut No</th>
            <th width="80">Bundle No</th>
            <th>Bundle Qty.</th>
        </thead>
	</table>
	<div style="width:950px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="930" class="rpt_table" id="tbl_list_search">
        <?
            $i=1;
            foreach ($result as $row)
            {
				$rescan_qty=$issue_qty_arr[$row[csf('id')]][$row[csf('barcode_no')]]-$receive_qty_arr[$row[csf('id')]][$row[csf('barcode_no')]];
				if($scanned_bundle_arr[$row[csf('bundle_no')]]=="" && $rescan_qty>0 && in_array($row[csf('barcode_no')],$receive_barcode_arr))
				{

					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					list($shortName,$year,$job)=explode('-',$row[csf('job_no_mst')]);
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)">
						<td width="40">
							<? echo $i; ?>
							 <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
							 <input type="hidden" name="txt_challan_id" id="txt_challan_id<?php echo $i; ?>" value="<?php echo $row[csf('id')]; ?>"/>
						</td>
						<td width="50" align="center"><p><? echo $year; ?></p></td>
						<td width="50" align="center"><p><? echo $job*1; ?></p></td>
						<td width="90"><p><? echo $row[csf('po_number')]; ?></p></td>
                        <td width="100"><p><? echo $challan_arr[$row[csf('id')]]; ?></p></td>
						<td width="130"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
						<td width="110"><p><? echo $country_arr[$row[csf('country_id')]]; ?></p></td>
						<td width="100"><p><? echo $color_arr[$row[csf('color_number_id')]]; ?></p></td>
						<td width="50"><p><? echo $size_arr[$row[csf('size_number_id')]]; ?></p></td>
						<td width="80"><? echo $row[csf('cut_no')]; ?></td>
						<td width="80"><? echo $row[csf('bundle_no')]; ?></td>
						<td align="right"><? echo $rescan_qty; ?>&nbsp;&nbsp;</td>
					</tr>
					<?
					$i++;
				}
			}
        	?>
        </table>
    </div>
    <table width="830">
        <tr>
            <td align="center" >
                <span  style="float:left;"> <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All</span>
                <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
            </td>
        </tr>
    </table>
<?
	exit();
}

if($action=="populate_bundle_data_rescan")
{
 	$ex_data = explode("**",$data);
	$bundle=explode(",",$ex_data[0]);
	$mst_id=$ex_data[2];
	$bundle_nos="'".trim(implode("','",$bundle))."'";
	$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');

	$bundle_count=count(explode(",",$bundle_nos)); $bundle_nos_cond="";
	if($db_type==2 && $bundle_count>400)
	{
		$bundle_nos_cond=" and (";
		$bundleArr=array_chunk(explode(",",$bundle_nos),399);
		foreach($bundleArr as $bundleNos)
		{
			$bundleNos=implode(",",$bundleNos);
			$bundle_nos_cond.=" c.barcode_no in($bundleNos) or ";
		}
		$bundle_nos_cond=chop($bundle_nos_cond,'or ');
		$bundle_nos_cond.=")";
	}
	else
	{
		$bundle_nos_cond=" and c.barcode_no in ($bundle_nos)";
	}

	$year_field="";
	if($db_type==0)
	{
		$year_field="YEAR(f.insert_date)";
	}
	else if($db_type==2)
	{
		$year_field="to_char(f.insert_date,'YYYY')";
	}

	 $receive_sql="select c.barcode_no, c.production_qnty as qty from pro_gmts_delivery_mst a,pro_garments_production_dtls c where a.id=c.delivery_mst_id
	 and c.production_type=3 and a.embel_name=4 and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0
	  and a.issue_challan_id=".$mst_id." $bundle_nos_cond";

	//echo $issue_sql;die;
	$receive_result = sql_select($receive_sql);
	foreach ($receive_result as $row){
		$receive_qty_arr[$row[csf('barcode_no')]]+=$row[csf('qty')];
	}

	// print_r($receive_qty_arr);die;
	$sql="SELECT max(c.id) as prdid, d.id as colorsizeid, e.id as po_id, f.job_no_prefix_num, MAX($year_field) as year, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id,c.cut_no, c.bundle_no,c.barcode_no,  sum(c.production_qnty) as pre_qty, e.po_number from pro_gmts_delivery_mst a,pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f where f.company_name=$ex_data[3] and a.id=c.delivery_mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_no_mst=f.job_no and c.production_type=2 and a.embel_name=4 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and e.status_active in(1,2,3) and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and a.id=".$mst_id." $bundle_nos_cond group by d.id, e.id, f.job_no_prefix_num, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id,c.cut_no, c.bundle_no,c.barcode_no, e.po_number order by c.cut_no, length(c.bundle_no) asc, c.bundle_no asc";


	    //echo $sql; die;
	$result = sql_select($sql);
	//print_r($result);die;
	$count=count($result);
	$i=$ex_data[1]+$count;
	foreach ($result as $row)
	{
		$production_qty=$row[csf('pre_qty')]-$receive_qty_arr[$row[csf('barcode_no')]];
		if($production_qty>0)
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i;?>">
            	<td width="30"><? echo $i; ?></td>
                <td width="80" id="bundle_<? echo $i; ?>" title="<? echo $row[csf('barcode_no')]; ?>"><? echo $row[csf('bundle_no')]; ?></td>
                <td width="45" align="center"><? echo $row[csf('year')]; ?></td>
                <td width="50" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                <td width="65"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
                <td width="90" style="word-break:break-all;" align="left"><? echo $row[csf('po_number')]; ?></td>
                <td width="100" style="word-break:break-all;" align="left"><? echo $garments_item[$row[csf('item_number_id')]]; ?></td>
                <td width="80" style="word-break:break-all;" align="left"><? echo $country_arr[$row[csf('country_id')]]; ?></td>
                <td width="80" style="word-break:break-all;" align="left"><? echo $color_arr[$row[csf('color_number_id')]]; ?></td>
                <td width="60" style="word-break:break-all;" align="left">&nbsp;<? echo $size_arr[$row[csf('size_number_id')]]; ?></td>
                <td width="50" id="prodQty_<? echo $i; ?>" align="right"><? echo $production_qty; ?>&nbsp;</td>

                <td width="50" id="RejQty_<? echo $i; ?>" align="right">
                <input type="text" class="text_boxes_numeric" name="rejectQty[]" id="rejectQty_<? echo $i; ?>" style="width:40px" onBlur="calculate_qcpasss(<? echo $i; ?>)" disabled onDblClick="pop_entry_reject(<? echo $i; ?>)"/>

				<input type="hidden" name="actual_reject[]"  id="actual_reject_<? echo $i; ?>"  value="" />
                </td>

                <td width="50" id="RepQty_<? echo $i; ?>" align="right"><input type="text" class="text_boxes_numeric" name="replaceQty[]" id="replaceQty_<? echo $i; ?>" style="width:40px" onBlur="calculate_qcpasss_rescan(<? echo $i; ?>)" value="<? echo $production_qty; ?>" /></td>
                <td width="50" id="QcQty_<? echo $i; ?>" align="right"><? echo $production_qty; ?>&nbsp;</td>
                <td id="button_1" align="center">

                    <input type="hidden" name="cutNo[]" id="cutNo_<? echo $i; ?>" value="<? echo $row[csf('cut_no')]; ?>"/>

                    <input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
                    <input type="hidden" name="colorSizeId[]" id="colorSizeId_<? echo $i; ?>" value="<? echo $row[csf('colorsizeid')]; ?>"/>
                    <input type="hidden" name="orderId[]" id="orderId_<? echo $i; ?>" value="<? echo $row[csf('po_id')]; ?>"/>
                    <input type="hidden" name="gmtsitemId[]" id="gmtsitemId_<? echo $i; ?>" value="<? echo $row[csf('item_number_id')]; ?>"/>
                    <input type="hidden" name="countryId[]" id="countryId_<? echo $i; ?>" value="<? echo $row[csf('country_id')]; ?>"/>
                    <input type="hidden" name="colorId[]" id="colorId_<? echo $i; ?>" value="<? echo $row[csf('color_number_id')]; ?>"/>
                    <input type="hidden" name="sizeId[]" id="sizeId_<? echo $i; ?>" value="<? echo $row[csf('size_number_id')]; ?>"/>
                    <input type="hidden" name="qty[]" id="qty_<? echo $i; ?>" value="<? echo $production_qty; ?>"/>
                    <input type="hidden" name="prod_qty[]" id="prod_qty_<? echo $i; ?>" value="<? echo $production_qty; ?>"/>
                    <input type="hidden" name="dtlsId[]" id="dtlsId_<? echo $i; ?>" value="<? echo $row[csf('prdid')]; ?>" />
                    <input type="hidden" name="isRescan[]" id="isRescan_<? echo $i; ?>" value="1"/>
                </td>
			</tr>
		<?
        	$i--;
		}
	}
	exit();
}

if($action=="challan_duplicate_check_resacn")
{
	$data=explode("__",$data);
	$bundle_no="'".implode("','",explode(",",$data[0]))."'";
	$msg=1;
	$bundle_count=count(explode(",",$bundle_no)); $bundle_nos_cond=""; $recbundle_nos_cond="";
	if($db_type==2 && $bundle_count>400)
	{
		$recbundle_nos_cond=" and (";
		$bundle_nos_cond=" and (";
		$bundleArr=array_chunk(explode(",",$bundle_no),399);
		foreach($bundleArr as $bundleNos)
		{
			$bundleNos=implode(",",$bundleNos);
			$recbundle_nos_cond.=" c.barcode_no in($bundleNos) or ";
			$bundle_nos_cond.=" b.barcode_no in($bundleNos) or ";
		}
		$recbundle_nos_cond=chop($recbundle_nos_cond,'or ');
		$recbundle_nos_cond.=")";

		$bundle_nos_cond=chop($bundle_nos_cond,'or ');
		$bundle_nos_cond.=")";
	}
	else
	{
		$recbundle_nos_cond=" and c.barcode_no in ($bundle_no)";
		$bundle_nos_cond=" and b.barcode_no in ($bundle_no)";
	}

	//$rescanable_barcode=return_field_value("b.barcode_no as barcode_no","pro_gmts_delivery_mst a,pro_garments_production_dtls b"," a.id=b.delivery_mst_id and b.barcode_no in ($bundle_no) and b.production_type=3 and a.embel_name=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","barcode_no");

	$receive_sql="select a.issue_challan_id,c.barcode_no, sum(c.production_qnty) as qty from pro_gmts_delivery_mst a,pro_garments_production_dtls c where a.id=c.delivery_mst_id
	 $recbundle_nos_cond and c.production_type=3 and a.embel_name=4 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.issue_challan_id,c.barcode_no";

	//echo $receive_sql;die;
	$receive_result = sql_select($receive_sql);
	foreach ($receive_result as $row){
		$receive_qty_arr[$row[csf('issue_challan_id')]][$row[csf('barcode_no')]]=$row[csf('qty')];
	}

	//print_r($receive_qty_arr);die;
	//echo $rescanable_barcode;die;
	if(!empty($receive_qty_arr))
	{
		$result=sql_select("select a.sys_number,b.bundle_no,b.barcode_no,a.id,sum(b.production_qnty) as qty from pro_gmts_delivery_mst a,pro_garments_production_dtls b where a.id=b.delivery_mst_id $bundle_nos_cond and b.production_type=2 and a.embel_name=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.sys_number, b.bundle_no,b.barcode_no,a.id");

		$datastr="";
		$issed_bundle_challan='';
		if(count($result)>0)
		{
			foreach ($result as $row)
			{
				$production_qty=$row[csf('qty')]-$receive_qty_arr[$row[csf('id')]][$row[csf('barcode_no')]]."*".$production_qty;
				if($production_qty>0)
				{
					$msg=2;
					if($datastr!="") $datastr.=",";
					$datastr.=$row[csf('id')]."*".$row[csf('sys_number')]."*".$row[csf('bundle_no')]."*".$production_qty;
				}
			}
		}
	}

	echo rtrim($msg)."_".rtrim($datastr);
	exit();
}

if($action=="bundle_popup_single")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	//echo $bundle_info;die;
?>
	<script>
		function js_set_value( str)
		{
			$('#hidden_bundle_info').val( str );
			parent.emailwindow.hide();
		}

    </script>
</head>
<body>
<div align="center" style="width:100%;" >
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:370px;">
		<legend>Select Only One Challan </legend>
        	<input type="hidden" name="hidden_bundle_info" id="hidden_bundle_info">
            <table cellpadding="0" cellspacing="0" width="350" border="1" rules="all" class="rpt_table">
                <thead>
                    <th width="150">Bundle No</th>
                    <th width="200">Issue Challan No</th>
                </thead>
                <?php
				$bundle_issue_arr=explode(",",$bundle_info);
				$i=1;
				foreach($bundle_issue_arr as $value)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$single_bundle=explode("*",$value);
				?>
                	<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value('<? echo $value; ?>')">
                    <td align="center"><?php echo $single_bundle[2];?> </td>
                    <td align="center">	 <?php echo $single_bundle[1];?> </td>
                </tr>
                <?php
				$i++;
				}

				?>
           </table>

		</fieldset>
	</form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if ($action == "color_and_size_level") {
    $dataArr = explode("**", $data);
    $po_id = $dataArr[0];
    $item_id = $dataArr[1];
    $variableSettings = $dataArr[2];
    $styleOrOrderWisw = $dataArr[3];
    $embelName = $dataArr[4];
    $country_id = $dataArr[5];

    $color_library = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
    $size_library = return_library_array("select id, size_name from lib_size", 'id', 'size_name');

    //#############################################################################################//
    //order wise - color level, color and size level
    //$variableSettings=2;

    if ($variableSettings == 2) { // color level
        if ($db_type == 0) {
            $sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=1 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur, pro_garments_production_mst mst where mst.id=cur.mst_id and mst.embel_name='$embelName' and cur.production_type=2 and cur.is_deleted=0 ) as cur_production_qnty
			from wo_po_color_size_breakdown
			where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active in(1,2,3) group by color_number_id";
        } else {
            $sql = "SELECT a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
					sum(CASE WHEN c.production_type=1 then b.production_qnty ELSE 0 END) as production_qnty,
					sum(CASE WHEN c.production_type=2 and c.embel_name='$embelName' then b.production_qnty ELSE 0 END) as cur_production_qnty
					from wo_po_color_size_breakdown a
					left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id
					left join pro_garments_production_mst c on c.id=b.mst_id
					where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active in(1,2,3) group by a.item_number_id, a.color_number_id";
        }

        $colorResult = sql_select($sql);
    } else if ($variableSettings == 3) { //color and size level

        /* $sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=1 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur, pro_garments_production_mst mst where mst.id=cur.mst_id and mst.embel_name='$embelName' and cur.production_type=2 and cur.is_deleted=0 ) as cur_production_qnty
          from wo_po_color_size_breakdown
          where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1"; */



        $dtlsData = sql_select("select a.color_size_break_down_id,
										sum(CASE WHEN a.production_type=1 then a.production_qnty ELSE 0 END) as production_qnty,
										sum(CASE WHEN a.production_type=2 and b.embel_name='$embelName' then a.production_qnty ELSE 0 END) as cur_production_qnty
										from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(1,2) group by a.color_size_break_down_id");

        foreach ($dtlsData as $row) {
            $color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss'] = $row[csf('production_qnty')];
            $color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv'] = $row[csf('cur_production_qnty')];
        }
        //print_r($color_size_qnty_array);

        $sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
			from wo_po_color_size_breakdown
			where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active in(1,2,3) order by color_number_id, id";

        $colorResult = sql_select($sql);
    }
    /* 	else // by default color and size level
      {
      $sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=1 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur, pro_garments_production_mst mst where  mst.id=cur.mst_id and mst.embel_name='$embelName' and cur.production_type=2 and cur.is_deleted=0 ) as cur_production_qnty
      from wo_po_color_size_breakdown
      where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1";// order by color_number_id,size_number_id
      }
     */
    //$colorResult = sql_select($sql);
    //print_r($sql);
    $colorHTML = "";
    $colorID = '';
    $chkColor = array();
    $i = 0;
    $totalQnty = 0;
    foreach ($colorResult as $color) {
        if ($variableSettings == 2) { // color level
            $colorHTML .= '<tr><td>' . $color_library[$color[csf("color_number_id")]] . '</td><td><input type="text" name="txt_color" id="colSize_' . ($i + 1) . '" style="width:80px"  class="text_boxes_numeric" placeholder="' . ($color[csf("production_qnty")] - $color[csf("cur_production_qnty")]) . '" onblur="fn_colorlevel_total(' . ($i + 1) . ')"></td></tr>';
            $totalQnty += $color[csf("production_qnty")] - $color[csf("cur_production_qnty")];
            $colorID .= $color[csf("color_number_id")] . ",";
        } else { //color and size level
            if (!in_array($color[csf("color_number_id")], $chkColor)) {
                if ($i != 0)
                    $colorHTML .= "</table></div>";
                $i = 0;
                $colorHTML .= '<h3 align="left" id="accordion_h' . $color[csf("color_number_id")] . '" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_' . $color[csf("color_number_id")] . '\', \'\',1)"> <span id="accordion_h' . $color[csf("color_number_id")] . 'span">+</span>' . $color_library[$color[csf("color_number_id")]] . ' : <span id="total_' . $color[csf("color_number_id")] . '"></span> </h3>';
                $colorHTML .= '<div id="content_search_panel_' . $color[csf("color_number_id")] . '" style="display:none" class="accord_close"><table id="table_' . $color[csf("color_number_id")] . '">';
                $chkColor[] = $color[csf("color_number_id")];
            }
            //$index = $color[csf("size_number_id")].$color[csf("color_number_id")];
            $colorID .= $color[csf("size_number_id")] . "*" . $color[csf("color_number_id")] . ",";

            $iss_qnty = $color_size_qnty_array[$color[csf('id')]]['iss'];
            $rcv_qnty = $color_size_qnty_array[$color[csf('id')]]['rcv'];



            $colorHTML .= '<tr><td>' . $size_library[$color[csf("size_number_id")]] . '</td><td><input type="text" name="colorSize" id="colSize_' . $color[csf("color_number_id")] . ($i + 1) . '"  class="text_boxes_numeric" style="width:100px" placeholder="' . ($iss_qnty - $rcv_qnty) . '" onblur="fn_total(' . $color[csf("color_number_id")] . ',' . ($i + 1) . ')"></td></tr>';
        }
        $i++;
    }
    //echo $colorHTML;die;
    if ($variableSettings == 2) {
        $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="100">Color</th><th width="80">Quantity</th></thead><tbody>' . $colorHTML . '<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="' . $totalQnty . '" class="text_boxes_numeric" style="width:80px" ></th></tr></tfoot></table>';
    }
    echo "$('#breakdown_td_id').html('" . addslashes($colorHTML) . "');\n";
    $colorList = substr($colorID, 0, -1);
    echo "$('#hidden_colorSizeID').val('" . $colorList . "');\n";
    //#############################################################################################//
    exit();
}

if ($action == "show_dtls_listview") {
    $location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');
    $company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
    ?>
    <div style="width:100%;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
            <th width="50">SL</th>
            <th width="150" align="center">Item Name</th>
            <th width="120" align="center">Country</th>
            <th width="80" align="center">Production Date</th>
            <th width="80" align="center">Production Qnty</th>
            <th width="150" align="center">Serving Company</th>
            <th width="120" align="center">Location</th>
            <th align="center">Challan No</th>
            </thead>
        </table>
    </div>
    <div style="width:100%;max-height:180px; overflow-y:scroll" id="sewing_production_list_view" align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
    <?php
    $i = 1;
    $total_production_qnty = 0;
    $sqlResult = sql_select("select id,po_break_down_id,item_number_id,country_id,production_date,production_quantity,production_source,serving_company,location,challan_no from pro_garments_production_mst where delivery_mst_id='$data' and status_active=1 and is_deleted=0 order by id");
    foreach ($sqlResult as $selectResult) {
        if ($i % 2 == 0)
            $bgcolor = "#E9F3FF";
        else
            $bgcolor = "#FFFFFF";
        $total_production_qnty += $selectResult[csf('production_quantity')];
        ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data(<? echo $selectResult[csf('id')]; ?>, 'populate_receive_form_data', 'requires/special_work_challan_receive_controller');" >
                    <td width="50" align="center"><? echo $i; ?></td>
                    <td width="150" align="center"><p><? echo $garments_item[$selectResult[csf('item_number_id')]]; ?></p></td>
                    <td width="120" align="center"><p><? echo $country_library[$selectResult[csf('country_id')]]; ?></p></td>
                    <td width="80" align="center"><?php echo change_date_format($selectResult[csf('production_date')]); ?></td>
                    <td width="80" align="center"><?php echo $selectResult[csf('production_quantity')]; ?></td>
        <?php
        $source = $selectResult[csf('production_source')];
        if ($source == 3)
            $serving_company = $supplier_arr[$selectResult[csf('serving_company')]];
        else
            $serving_company = $company_arr[$selectResult[csf('serving_company')]];
        ?>
                    <td width="150" align="center"><p><?php echo $serving_company; ?></p></td>
                    <td width="120" align="center"><p><? echo $location_arr[$selectResult[csf('location')]]; ?></p></td>
                    <td align="center"><p><?php echo $selectResult[csf('challan_no')]; ?></p></td>
                </tr>
                <?php
                $i++;
            }
            ?>
        </table>
    </div>
            <?
            exit();
        }

        if ($action == "show_country_listview") {
            ?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="370" class="rpt_table">
        <thead>
        <th width="30">SL</th>
        <th width="110">Item Name</th>
        <th width="80">Country</th>
        <th width="75">Prod. Date</th>
        <th>Prod. Qty.</th>
    </thead>
                <?
                $i = 1;

                $sqlResult = sql_select("select id,po_break_down_id,item_number_id,country_id,production_date,production_quantity,production_source,serving_company,location,challan_no from pro_garments_production_mst where delivery_mst_id='$data' and status_active=1 and is_deleted=0 order by id");
                foreach ($sqlResult as $row) {
                    if ($i % 2 == 0)
                        $bgcolor = "#E9F3FF";
                    else
                        $bgcolor = "#FFFFFF";
                    ?>
        <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data(<? echo $row[csf('id')]; ?>, 'populate_issue_form_data', 'requires/special_work_challan_receive_controller');">
            <td width="30"><? echo $i; ?></td>
            <td width="110"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
            <td width="80"><p><? echo $country_library[$row[csf('country_id')]]; ?>&nbsp;</p></td>
            <td width="75" align="center"><? if ($row[csf('production_date')] != "0000-00-00") echo change_date_format($row[csf('production_date')]); ?>&nbsp;</td>
            <td align="right"><?php echo $row[csf('production_quantity')]; ?></td>
        </tr>
        <?
        $i++;
    }
    ?>
    </table>
    <?
    exit();
}

if ($action == "populate_issue_form_data") {
    //production type=2 come from array
    $poNumber_arr = return_library_array("select id, po_number from   wo_po_break_down", 'id', 'po_number');
    $sqlResult = sql_select("select id,garments_nature,challan_no,po_break_down_id,item_number_id,country_id,production_source,serving_company,location,embel_name,embel_type,production_date,production_quantity,production_source,production_type,entry_break_down_type,production_hour,sewing_line,supervisor,carton_qty,remarks,floor_id,alter_qnty,reject_qnty,total_produced,yet_to_produced from pro_garments_production_mst where id='$data' and production_type='2' and status_active=1 and is_deleted=0 order by id");
    //echo "sdfds".$sqlResult;die;
    foreach ($sqlResult as $result) {
        echo "$('#txt_receive_qty').val('" . $result[csf('production_quantity')] . "');\n";
        echo "$('#txt_remark').val('" . $result[csf('remarks')] . "');\n";

        $dataArray = sql_select("select SUM(CASE WHEN production_type=2 and embel_name=4 THEN production_quantity END) as totalcutting,SUM(CASE WHEN production_type=3 and embel_name=4 THEN production_quantity ELSE 0 END) as totalprinting from pro_garments_production_mst WHERE po_break_down_id=" . $result[csf('po_break_down_id')] . " and item_number_id=" . $result[csf('item_number_id')] . "  and country_id=" . $result[csf('country_id')] . " and is_deleted=0");
        foreach ($dataArray as $row) {
            echo "$('#txt_issue_qty').val('" . $row[csf('totalcutting')] . "');\n";
            echo "$('#txt_cumul_receive_qty').attr('placeholder','" . $row[csf('totalprinting')] . "');\n";
            echo "$('#txt_cumul_receive_qty').val('" . $row[csf('totalprinting')] . "');\n";
            $yet_to_produced = $row[csf('totalcutting')] - $row[csf('totalprinting')];
            echo "$('#txt_yet_to_receive').attr('placeholder','" . $yet_to_produced . "');\n";
            echo "$('#txt_yet_to_receive').val('" . $yet_to_produced . "');\n";
        }


        echo "$('#cbo_item_name').val('" . $result[csf('item_number_id')] . "');\n";
        echo "$('#cbo_country_name').val('" . $result[csf('country_id')] . "');\n";
        echo "$('#hidden_po_break_down_id').val('" . $result[csf('po_break_down_id')] . "');\n";
        echo "$('#txt_order_no').val('" . $poNumber_arr[$result[csf('po_break_down_id')]] . "');\n";

        //break down of color and size------------------------------------------
        //#############################################################################################//
        // order wise - color level, color and size level
        $color_library = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
        $size_library = return_library_array("select id, size_name from lib_size", 'id', 'size_name');

        $variableSettings = $result[csf('entry_break_down_type')];
    }
    $sql_order = sql_select("SELECT a.buyer_name,a.style_ref_no,b.po_quantity from wo_po_break_down b,wo_po_details_master a where a.job_no=b.job_no_mst and b.id=" . $result[csf('po_break_down_id')] . " and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0");
    foreach ($sql_order as $inf) {
        echo "$('#cbo_buyer_name').val(" . $inf[csf('buyer_name')] . ");\n";
        echo "$('#txt_order_qty').val(" . $inf[csf('po_quantity')] . ");\n";
        echo "$('#txt_style_no').val('" . $inf[csf('style_ref_no')] . "');\n";
    }

    if ($variableSettings == 2) { // color level
        $po_id = $result[csf('po_break_down_id')];
        $item_id = $result[csf('item_number_id')];
        $country_id = $result[csf('country_id')];
        if ($db_type == 0) {

            $sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls, pro_garments_production_mst mst where mst.id=pdtls.mst_id and mst.embel_name='$embelName' and pdtls.production_type=2 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur, pro_garments_production_mst mst where mst.id=cur.mst_id and mst.embel_name='$embelName' and cur.production_type=3 and cur.is_deleted=0 ) as cur_production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.reject_qty ELSE 0 END) from pro_garments_production_dtls cur, pro_garments_production_mst mst where mst.id=cur.mst_id and mst.embel_name='$embelName' and cur.production_type=3 and cur.is_deleted=0 ) as reject_qty
				from wo_po_color_size_breakdown
				where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active in(1,2,3) group by color_number_id";
        } else {
            $sql = "select a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
						sum(CASE WHEN c.production_type=2 then b.production_qnty ELSE 0 END) as production_qnty,
						sum(CASE WHEN c.production_type=3 and c.embel_name='$embelName' then b.production_qnty ELSE 0 END) as cur_production_qnty,
						sum(CASE WHEN c.production_type=3 then b.reject_qty ELSE 0 END) as reject_qty
						from wo_po_color_size_breakdown a
						left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id
						left join pro_garments_production_mst c on c.id=b.mst_id
						where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active in(1,2,3) group by a.item_number_id, a.color_number_id";
        }

        $colorResult = sql_select($sql);
    } else if ($variableSettings == 3) { //color and size level
        $po_id = $result[csf('po_break_down_id')];
        $item_id = $result[csf('item_number_id')];
        $country_id = $result[csf('country_id')];

        $dtlsData = sql_select("select a.color_size_break_down_id,
										sum(CASE WHEN a.production_type=2 then a.production_qnty ELSE 0 END) as production_qnty,
										sum(CASE WHEN a.production_type=3 then a.production_qnty ELSE 0 END) as cur_production_qnty,
										sum(CASE WHEN a.production_type=3 then a.reject_qty ELSE 0 END) as reject_qty
										from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.embel_name=4 and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(2,3) group by a.color_size_break_down_id");

        foreach ($dtlsData as $row) {
            $color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss'] = $row[csf('production_qnty')];
            $color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv'] = $row[csf('cur_production_qnty')];
            $color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rej'] = $row[csf('reject_qty')];
        }
        //print_r($color_size_qnty_array);

        $sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
				from wo_po_color_size_breakdown
				where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active in(1,2,3) order by color_number_id, id";
        //echo $sql;

        $colorResult = sql_select($sql);
    } else { // by default color and size level

    }

    if ($variableSettingsRej != 1) {
        $disable = "";
    } else {
        $disable = "disabled";
    }
    $colorHTML = "";
    $colorID = '';
    $chkColor = array();
    $i = 0;
    $totalQnty = 0;
    foreach ($colorResult as $color) {
        if ($variableSettings == 2) { // color level
            $colorHTML .= '<tr><td>' . $color_library[$color[csf("color_number_id")]] . '</td><td><input type="text" name="txt_color" id="colSize_' . ($i + 1) . '" style="width:60px"  class="text_boxes_numeric" placeholder="' . ($color[csf("production_qnty")] - $color[csf("cur_production_qnty")]) . '" onblur="fn_colorlevel_total(' . ($i + 1) . ')"></td><td><input type="text" name="txtColSizeRej" id="colSizeRej_' . ($i + 1) . '" style="width:60px"  class="text_boxes_numeric" placeholder="Rej." onblur="fn_colorRej_total(' . ($i + 1) . ')  ' . $disable . '"></td></tr>';
            $totalQnty += $color[csf("production_qnty")] - $color[csf("cur_production_qnty")];
            $colorID .= $color[csf("color_number_id")] . ",";
        } else { //color and size level
            if (!in_array($color[csf("color_number_id")], $chkColor)) {
                if ($i != 0)
                    $colorHTML .= "</table></div>";
                $i = 0;
                $colorHTML .= '<h3 align="left" id="accordion_h' . $color[csf("color_number_id")] . '" style="width:250px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_' . $color[csf("color_number_id")] . '\', \'\',1)"> <span id="accordion_h' . $color[csf("color_number_id")] . 'span">+</span>' . $color_library[$color[csf("color_number_id")]] . ' : <span id="total_' . $color[csf("color_number_id")] . '"></span> </h3>';
                $colorHTML .= '<div id="content_search_panel_' . $color[csf("color_number_id")] . '" style="display:none" class="accord_close"><table id="table_' . $color[csf("color_number_id")] . '">';
                $chkColor[] = $color[csf("color_number_id")];
            }
            //$index = $color[csf("size_number_id")].$color[csf("color_number_id")];
            $colorID .= $color[csf("size_number_id")] . "*" . $color[csf("color_number_id")] . ",";

            $iss_qnty = $color_size_qnty_array[$color[csf('id')]]['iss'];
            $rcv_qnty = $color_size_qnty_array[$color[csf('id')]]['rcv'];


            $colorHTML .= '<tr><td>' . $size_library[$color[csf("size_number_id")]] . '</td><td><input type="text" name="colorSize" id="colSize_' . $color[csf("color_number_id")] . ($i + 1) . '"  class="text_boxes_numeric" style="width:80px" placeholder="' . ($iss_qnty - $rcv_qnty) . '" onblur="fn_total(' . $color[csf("color_number_id")] . ',' . ($i + 1) . ')"><input type="text" name="colorSizeRej" id="colSizeRej_' . $color[csf("color_number_id")] . ($i + 1) . '"  class="text_boxes_numeric" style="width:50px" placeholder="Rej. Qty" onblur="fn_total_rej(' . $color[csf("color_number_id")] . ',' . ($i + 1) . ')" ' . $disable . '></td></tr>';
        }

        $i++;
    }
    //echo $colorHTML;die;
    if ($variableSettings == 2) {
        $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="70">Color</th><th width="60">Quantity</th><th width="60">Rej.</th></thead><tbody>' . $colorHTML . '<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="' . $totalQnty . '" class="text_boxes_numeric" style="width:60px" ></th><th><input type="text" id="total_color_rej" placeholder="Rej." class="text_boxes_numeric" style="width:60px" ' . $disable . ' ></th></tr></tfoot></table>';
    }
    echo "$('#breakdown_td_id').html('" . addslashes($colorHTML) . "');\n";
    $colorList = substr($colorID, 0, -1);
    echo "$('#hidden_colorSizeID').val('" . $colorList . "');\n";
    //#############################################################################################//
    exit();
}


if ($action == "populate_receive_form_data") {
    //production type=2 come from array
    $poNumber_arr = return_library_array("select id, po_number from   wo_po_break_down", 'id', 'po_number');
    $sqlResult = sql_select("select id, garments_nature, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, embel_name, embel_type, production_date, production_quantity, production_source, production_type, entry_break_down_type, break_down_type_rej, production_hour, sewing_line, supervisor, carton_qty, remarks, floor_id, alter_qnty, reject_qnty, total_produced, yet_to_produced from pro_garments_production_mst where id='$data' and production_type='3' and status_active=1 and is_deleted=0 order by id");
    //echo "sdfds".$sqlResult;die;
    foreach ($sqlResult as $result) {
        echo "$('#txt_receive_qty').val('" . $result[csf('production_quantity')] . "');\n";
        echo "$('#txt_reject_qty').val('" . $result[csf('reject_qnty')] . "');\n";
        echo "$('#txt_challan').val('" . $result[csf('challan_no')] . "');\n";
        echo "$('#txt_remark').val('" . $result[csf('remarks')] . "');\n";

        echo "$('#cbo_item_name').val('" . $result[csf('item_number_id')] . "');\n";
        echo "$('#cbo_country_name').val('" . $result[csf('country_id')] . "');\n";
        echo "$('#hidden_po_break_down_id').val('" . $result[csf('po_break_down_id')] . "');\n";
        echo "$('#txt_order_no').val('" . $poNumber_arr[$result[csf('po_break_down_id')]] . "');\n";

        $dataArray = sql_select("select SUM(CASE WHEN production_type=2 THEN production_quantity END) as totalCutting,SUM(CASE WHEN production_type=3 THEN production_quantity ELSE 0 END) as totalPrinting from pro_garments_production_mst WHERE po_break_down_id=" . $result[csf('po_break_down_id')] . " and item_number_id=" . $result[csf('item_number_id')] . " and embel_name=" . $result[csf('embel_name')] . " and country_id=" . $result[csf('country_id')] . " and is_deleted=0");
        foreach ($dataArray as $row) {
            echo "$('#txt_issue_qty').attr('placeholder','" . $row[csf('totalCutting')] . "');\n";
            echo "$('#txt_issue_qty').val('" . $row[csf('totalCutting')] . "');\n";
            echo "$('#txt_cumul_receive_qty').val('" . $row[csf('totalPrinting')] . "');\n";
            $yet_to_produced = $row[csf('totalCutting')] - $row[csf('totalPrinting')];
            echo "$('#txt_yet_to_receive').val('" . $yet_to_produced . "');\n";
        }

        $sql_order = sql_select("SELECT a.buyer_name,a.style_ref_no,b.po_quantity from wo_po_break_down b,wo_po_details_master a where a.job_no=b.job_no_mst and b.id=" . $result[csf('po_break_down_id')] . " and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0");
        foreach ($sql_order as $inf) {
            echo "$('#cbo_buyer_name').val(" . $inf[csf('buyer_name')] . ");\n";
            echo "$('#txt_order_qty').val(" . $inf[csf('po_quantity')] . ");\n";
            echo "$('#txt_style_no').val('" . $inf[csf('style_ref_no')] . "');\n";
        }

        echo "$('#txt_mst_id').val('" . $result[csf('id')] . "');\n";
        //echo "$('#txt_mst_id_all').val('".$result[csf('id')]."');\n";
        echo "set_button_status(1, permission, 'fnc_issue_print_embroidery_entry',1,1);\n";

        //break down of color and size------------------------------------------
        //#############################################################################################//
        // order wise - color level, color and size level
        $color_library = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
        $size_library = return_library_array("select id, size_name from lib_size", 'id', 'size_name');

        $variableSettings = $result[csf('entry_break_down_type')];
        $variableSettingsRej = $result[csf('break_down_type_rej')];
        if ($variableSettings != 1) { // gross level
            $po_id = $result[csf('po_break_down_id')];
            $item_id = $result[csf('item_number_id')];
            $country_id = $result[csf('country_id')];

            $sql_dtls = sql_select("select color_size_break_down_id, production_qnty, reject_qty, size_number_id, color_number_id from  pro_garments_production_dtls a,wo_po_color_size_breakdown b where a.mst_id=$data and a.status_active=1 and a.color_size_break_down_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id'");
            foreach ($sql_dtls as $row) {
                if ($variableSettings == 2)
                    $index = $row[csf('color_number_id')];
                else
                    $index = $row[csf('size_number_id')] . $row[csf('color_number_id')];
                $amountArr[$index] = $row[csf('production_qnty')];
                $rejectArr[$index] = $row[csf('reject_qty')];
            }

            if ($variableSettings == 2) { // color level
                if ($db_type == 0) {

                    $sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=2 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=3 and cur.is_deleted=0 ) as cur_production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.reject_qty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=3 and cur.is_deleted=0 ) as reject_qty
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active in(1,2,3) group by color_number_id";
                } else {
                    $sql = "select a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
							sum(CASE WHEN c.production_type=2 then b.production_qnty ELSE 0 END) as production_qnty,
							sum(CASE WHEN c.production_type=3 then b.production_qnty ELSE 0 END) as cur_production_qnty,
							sum(CASE WHEN c.production_type=3 then b.reject_qty ELSE 0 END) as reject_qty
							from wo_po_color_size_breakdown a
							left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id
							left join pro_garments_production_mst c on c.id=b.mst_id
							where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active in(1,2,3) group by a.item_number_id, a.color_number_id";
                }
            } else if ($variableSettings == 3) { //color and size level
                /* $sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=2 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=3 and cur.is_deleted=0 ) as cur_production_qnty
                  from wo_po_color_size_breakdown
                  where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1"; */


                $dtlsData = sql_select("select a.color_size_break_down_id,
										sum(CASE WHEN a.production_type=2 then a.production_qnty ELSE 0 END) as production_qnty,
										sum(CASE WHEN a.production_type=3 then a.production_qnty ELSE 0 END) as cur_production_qnty,
										sum(CASE WHEN a.production_type=3 then a.reject_qty ELSE 0 END) as reject_qty
										from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and a.mst_id=$data and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(2,3) group by a.color_size_break_down_id");

                foreach ($dtlsData as $row) {
                    $color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss'] = $row[csf('production_qnty')];
                    $color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv'] = $row[csf('cur_production_qnty')];
                    $color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rej'] = $row[csf('reject_qty')];
                }

                $sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active in(1,2,3) order by color_number_id";
            } else { // by default color and size level



                $dtlsData = sql_select("select a.color_size_break_down_id,
										sum(CASE WHEN a.production_type=2 then a.production_qnty ELSE 0 END) as production_qnty,
										sum(CASE WHEN a.production_type=3 then a.production_qnty ELSE 0 END) as cur_production_qnty,
										sum(CASE WHEN a.production_type=3 then a.reject_qty ELSE 0 END) as reject_qty
										from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(2,3) group by a.color_size_break_down_id");

                foreach ($dtlsData as $row) {
                    $color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss'] = $row[csf('production_qnty')];
                    $color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv'] = $row[csf('cur_production_qnty')];
                    $color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rej'] = $row[csf('reject_qty')];
                }
                //print_r($color_size_qnty_array);

                $sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active in(1,2,3) order by color_number_id";
            }
            if ($variableSettingsRej != 1) {
                $disable = "";
            } else {
                $disable = "disabled";
            }

            $colorResult = sql_select($sql);
            //print_r($sql);die;
            $colorHTML = "";
            $colorID = '';
            $chkColor = array();
            $i = 0;
            $totalQnty = 0;
            $colorWiseTotal = 0;
            foreach ($colorResult as $color) {
                if ($variableSettings == 2) { // color level
                    $amount = $amountArr[$color[csf("color_number_id")]];
                    $rejectAmt = $rejectArr[$color[csf("color_number_id")]];
                    $colorHTML .= '<tr><td>' . $color_library[$color[csf("color_number_id")]] . '</td><td><input type="text" name="txt_color" id="colSize_' . ($i + 1) . '" style="width:60px"  class="text_boxes_numeric" placeholder="' . ($color[csf("production_qnty")] - $color[csf("cur_production_qnty")] + $amount) . '" value="' . $amount . '" onblur="fn_colorlevel_total(' . ($i + 1) . ')"></td><td><input type="text" name="txtColSizeRej" id="colSizeRej_' . ($i + 1) . '" style="width:60px" class="text_boxes_numeric" placeholder="Rej." value="' . $rejectAmt . '" onblur="fn_colorRej_total(' . ($i + 1) . ')"></td></tr>';
                    $totalQnty += $amount;
                    $totalRejQnty += $rejectAmt;
                    $colorID .= $color[csf("color_number_id")] . ",";
                } else { //color and size level
                    $index = $color[csf("size_number_id")] . $color[csf("color_number_id")];
                    $amount = $amountArr[$index];
                    if (!in_array($color[csf("color_number_id")], $chkColor)) {
                        if ($i != 0)
                            $colorHTML .= "</table></div>";
                        $i = 0;
                        $colorWiseTotal = 0;
                        $colorHTML .= '<h3 align="left" id="accordion_h' . $color[csf("color_number_id")] . '" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_' . $color[csf("color_number_id")] . '\', \'\',1)"> <span id="accordion_h' . $color[csf("color_number_id")] . 'span">+</span>' . $color_library[$color[csf("color_number_id")]] . ' : <span id="total_' . $color[csf("color_number_id")] . '"></span> </h3>';
                        $colorHTML .= '<div id="content_search_panel_' . $color[csf("color_number_id")] . '" style="display:none" class="accord_close"><table id="table_' . $color[csf("color_number_id")] . '">';
                        $chkColor[] = $color[csf("color_number_id")];
                        $totalFn .= "fn_total(" . $color[csf("color_number_id")] . ");";
                    }
                    $colorID .= $color[csf("size_number_id")] . "*" . $color[csf("color_number_id")] . ",";

                    $iss_qnty = $color_size_qnty_array[$color[csf('id')]]['iss'];
                    $rcv_qnty = $color_size_qnty_array[$color[csf('id')]]['rcv'];
                    $rej_qnty = $color_size_qnty_array[$color[csf('id')]]['rej'];

                    $colorHTML .= '<tr><td>' . $size_library[$color[csf("size_number_id")]] . '</td><td><input type="text" name="colorSize" id="colSize_' . $color[csf("color_number_id")] . ($i + 1) . '"  class="text_boxes_numeric" style="width:100px" placeholder="' . ($iss_qnty - $rcv_qnty + $amount) . '" onblur="fn_total(' . $color[csf("color_number_id")] . ',' . ($i + 1) . ')" value="' . $amount . '" ><input type="text" name="colorSizeRej" id="colSizeRej_' . $color[csf("color_number_id")] . ($i + 1) . '"  class="text_boxes_numeric" style="width:50px" placeholder="Rej. Qty" onblur="fn_total_rej(' . $color[csf("color_number_id")] . ',' . ($i + 1) . ')" value="' . $rej_qnty . '" ' . $disable . '></td></tr>';
                    $colorWiseTotal += $amount;
                }
                $i++;
            }
            //echo $colorHTML;die;
            if ($variableSettings == 2) {
                $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="70">Color</th><th width="60">Quantity</th><th width="60">Reject</th></thead><tbody>' . $colorHTML . '<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="' . $totalQnty . '" value="' . $totalQnty . '" class="text_boxes_numeric" style="width:60px" ></th><th><input type="text" id="total_color_rej" placeholder="' . $totalRejQnty . '" value="' . $totalRejQnty . '" class="text_boxes_numeric" style="width:60px" ></th></tr></tfoot></table>';
            }
            echo "$('#breakdown_td_id').html('" . addslashes($colorHTML) . "');\n";
            if ($variableSettings == 3)
                echo "$totalFn;\n";
            $colorList = substr($colorID, 0, -1);
            echo "$('#hidden_colorSizeID').val('" . $colorList . "');\n";
        }//end if condtion
        //#############################################################################################//
    }
    exit();
}

//pro_garments_production_mst
if ($action == "save_update_delete") {
    $process = array(&$_POST);
    extract(check_magic_quote_gpc($process));

    if ($operation == 0)
    { // Insert Here----------------------------------------------------------
        $con = connect();
        if ($db_type == 0) {
            mysql_query("BEGIN");
        }
        //table lock here
        //if ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**2"; die;}
       // if ( check_table_status( 160, 1 )==0 ) { echo "15**0"; die;}

        if (str_replace("'", "", $txt_system_id) == "") {
           // $mst_id = return_next_id("id", "pro_gmts_delivery_mst", 1);

            if ($db_type == 0) $year_cond = "YEAR(insert_date)";
            else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
            else
                $year_cond = ""; //defined Later
             $new_sys_number = explode("*", return_next_id_by_sequence("", "pro_gmts_delivery_mst",$con,1,$cbo_company_name,'BRS',0,date("Y",time()),0,0,3,$cbo_embel_name,0 ));


            $field_array_delivery = "id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, location_id, delivery_basis, embel_name, embel_type, production_source, serving_company, floor_id, organic, delivery_date,issue_challan_id,body_part,working_company_id,working_location_id, inserted_by, insert_date";
            $mst_id = return_next_id_by_sequence(  "pro_gmts_delivery_mst_seq",  "pro_gmts_delivery_mst", $con );

            $data_array_delivery="(".$mst_id.",'".$new_sys_number[1]."','".(int)$new_sys_number[2]."','".$new_sys_number[0]."',".$cbo_company_name.",3,".$txt_location_id.",".$delivery_basis.",".$cbo_embel_name.",".$cbo_embel_type.",".$cbo_source.",".$txt_embl_company_id.",".$txt_location_id.",".$txt_organic.",".$txt_issue_date.",".$txt_issue_challan_id.",".$cbo_body_part.",".$cbo_working_company_name.",".$cbo_working_location.",".$user_id.",'".$pc_date_time."')";
            $challan_no = (int)$new_sys_number[2];
            $txt_challan_no = $new_sys_number[0];
        }
        else
		{
            $mst_id = str_replace("'", "", $txt_system_id);
            $txt_chal_no = explode("-", str_replace("'", "", $txt_challan_no));
            $challan_no = (int) $txt_chal_no[3];
            $field_array_delivery = "location_id*delivery_basis*embel_name*embel_type*production_source*serving_company*floor_id*organic*delivery_date*body_part*working_company_id*working_location_id*updated_by*update_date";
            $data_array_delivery="".$txt_location_id."*".$delivery_basis."*".$cbo_embel_name."*".$cbo_embel_type."*".$cbo_source."*".$txt_embl_company_id."*".$txt_floor_id."*".$txt_organic."*".$txt_issue_date."*".$cbo_body_part."*".$cbo_working_company_name."*".$cbo_working_location."*".$user_id."*'".$pc_date_time."'";
       }

       for($j=1;$j<=$tot_row;$j++)
            {
                $bundleCheck="bundleNo_".$j;
                $is_rescan="isRescan_".$j;
                if($$is_rescan!=1)
                {
                  $bundleCheckArr[$$bundleCheck]=$$bundleCheck;
                }
                $cutNo="cutNo_".$j;
                $all_cut_no_arr[$$cutNo]=$$cutNo;
            }
            $cut_nums="'".implode("','", $all_cut_no_arr)."'";
            $bundle_wise_type_sql="SELECT b.bundle_no ,b.color_type_id from ppl_cut_lay_mst a,ppl_cut_lay_bundle b where a.id=b.mst_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.cutting_no in ($cut_nums) ";
            $bundle_wise_type_array=array();
            $bundle_wise_data=sql_select($bundle_wise_type_sql);
            foreach($bundle_wise_data as $vals)
            {
                $bundle_wise_type_array[$vals[csf("bundle_no")]]=$vals[csf("color_type_id")];
            }

            $bundle="'".implode("','",$bundleCheckArr)."'";
            $receive_sql="select c.barcode_no,c.bundle_no from pro_garments_production_mst a,pro_garments_production_dtls c where a.id=c.mst_id and a.production_type=3 and  a.embel_name=4 and c.bundle_no  in ($bundle)  and c.production_type=3 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and (c.is_rescan=0 or c.is_rescan is null)";
            $receive_result = sql_select($receive_sql);
            foreach ($receive_result as $row)
            {

                $duplicate_bundle[$row[csf('bundle_no')]]=$row[csf('bundle_no')];
            }

        if (str_replace("'", "", $delivery_basis) == 3) {
            //$id = return_next_id("id", "pro_garments_production_mst", 1);
            $field_array_mst = "id, delivery_mst_id,cut_no, company_id, garments_nature, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, embel_name, embel_type, production_date, production_quantity,reject_qnty,replace_qty, production_type, entry_break_down_type, floor_id, inserted_by, insert_date";

            $mstArr = array();
            $dtlsArr = array();
            $colorSizeArr = array();
            $mstIdArr = array();
            $colorSizeIdArr = array();
            $mstArrRej = array();
            $dtlsArrRej = array();
            for ($j = 1; $j <= $tot_row; $j++) {

				$cutNo="cutNo_".$j;
				$bundleNo = "bundleNo_" . $j;
				$barcodeNo="barcodeNo_".$j;
                $orderId = "orderId_" . $j;
                $gmtsitemId = "gmtsitemId_" . $j;
                $countryId = "countryId_" . $j;
                $colorId = "colorId_" . $j;
                $sizeId = "sizeId_" . $j;
                $colorSizeId = "colorSizeId_" . $j;
                $qty = "qty_" . $j;
                $rejectQty = "rejectQty_" . $j;
				$replaceQty="replaceQty_".$j;
				$checkRescan="isRescan_".$j;
				if($duplicate_bundle[$$bundleNo]=='')
                {

					$bundleCutArr[$$bundleNo]=$$cutNo;
					$bundleBarcodeArr[$$bundleNo]=$$barcodeNo;
					$cutArr[$$orderId][$$gmtsitemId][$$countryId]=$$cutNo;
	                $mstArr[$$orderId][$$gmtsitemId][$$countryId] += $$qty;
	                $mstArrRej[$$orderId][$$gmtsitemId][$$countryId] += $$rejectQty;
					$mstArrReplace[$$orderId][$$gmtsitemId][$$countryId]+=$$replaceQty;
	                $colorSizeArr[$$bundleNo] = $$orderId . "**" . $$gmtsitemId . "**" . $$countryId;
	                $dtlsArr[$$bundleNo] += $$qty;
	                $dtlsArrRej[$$bundleNo] += $$rejectQty;
					$dtlsArrReplace[$$bundleNo]+=$$replaceQty;
					$bundleRescanArr[$$bundleNo]=$$checkRescan;
	                $dtlsArrColorSize[$$bundleNo] = $$colorSizeId;
	            }
            }

            foreach ($mstArr as $orderId => $orderData) {
                foreach ($orderData as $gmtsItemId => $gmtsItemIdData) {
                    foreach ($gmtsItemIdData as $countryId => $qty) {
                        if ($data_array_mst != "")
                            $data_array_mst .= ",";
                        $id= return_next_id_by_sequence(  "pro_gar_production_mst_seq", "pro_garments_production_mst", $con );
                        $data_array_mst.="(".$id.",".$mst_id.",'".$cutArr[$orderId][$gmtsItemId][$countryId]."',".$cbo_company_name.",".$garments_nature.",'".$challan_no."',".$orderId.",".$gmtsItemId.",".$countryId.",".$cbo_source.",".$txt_embl_company_id.",".$txt_location_id.",".$cbo_embel_name.",".$cbo_embel_type.",".$txt_issue_date.",".$qty.",'".$mstArrRej[$orderId][$gmtsItemId][$countryId]."','".$mstArrReplace[$orderId][$gmtsItemId][$countryId]."',3,".$sewing_production_variable.",".$txt_floor_id.",".$user_id.",'".$pc_date_time."')";
                        $mstIdArr[$orderId][$gmtsItemId][$countryId] = $id;
                        //$id = $id + 1;
                    }
                }
            }

            //$dtls_id = return_next_id("id", "pro_garments_production_dtls", 1);
            $field_array_dtls = "id,delivery_mst_id,mst_id,production_type,color_size_break_down_id,production_qnty,reject_qty,replace_qty,cut_no,bundle_no,barcode_no,is_rescan,color_type_id";

            foreach ($dtlsArr as $bundle_no => $qty) {

                $colorSizedData = explode("**", $colorSizeArr[$bundle_no]);
                $gmtsMstId = $mstIdArr[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[2]];
				$cut_no=$bundleCutArr[$bundle_no];
                if ($data_array_dtls != "")
                    $data_array_dtls .= ",";
                $dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq", "pro_garments_production_dtls", $con );
                $data_array_dtls.="(".$dtls_id.",".$mst_id.",".$gmtsMstId.",3,'".$dtlsArrColorSize[$bundle_no]."','".$qty."','".$dtlsArrRej[$bundle_no]."','".$dtlsArrReplace[$bundle_no]."','".$cut_no."','".$bundle_no."','".$bundleBarcodeArr[$bundle_no]."','".$bundleRescanArr[$bundle_no]."','".$bundle_wise_type_array[$bundle_no]."')";
                //$colorSizeIdArr[$colorSizeId]=$dtls_id;
               // $dtls_id = $dtls_id + 1;
            }

            // ========================= start reject (defect) entry ==================================

			$dft_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq", "pro_gmts_prod_dft", $con );
			$field_array_defect="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty,bundle_no,color_size_break_down_id,embel_name,inserted_by, insert_date";
			$data_array_defect = "";
			for($j=1;$j<=$tot_row;$j++)
			{
				$bundleNo="bundleNo_".$j;
				$barcodeNo="barcodeNo_".$j;
				$orderId="orderId_".$j;
				$gmtsitemId="gmtsitemId_".$j;
				$countryId="countryId_".$j;
				$colorSizeId="colorSizeId_".$j;
				$actualReject="actualReject_".$j;

				if($$actualReject!="")
				{
					$gmtsMstId=$mstIdArr[$$orderId][$$gmtsitemId][$$countryId];

					$actual_reject_info=explode("**",$$actualReject);
					for($rls=0;$rls<count($actual_reject_info); $rls++)
					{
						$bundle_reject_info=explode("*",$actual_reject_info[$rls]);
						if( trim($data_array_defect)!="") $data_array_defect.=",";

						$defectPointId=$bundle_reject_info[0];
						$defect_qty=$bundle_reject_info[1];

						//$field_array_defect="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty,bundle_no,embel_name, inserted_by, insert_date";

						$data_array_defect.="(".$dft_id.",".$gmtsMstId.",3,".$$orderId.",3,".$defectPointId.",'".$defect_qty."','".$$barcodeNo."',".$$colorSizeId.",1,".$user_id.",'".$pc_date_time."')";
						//$dft_id++;
						$dft_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con );
					}
				}
			}

			$rIdDft=1;
			if($data_array_defect!="")
			{
				//echo $data_array_defect;die;
				$rIdDft=sql_insert("pro_gmts_prod_dft",$field_array_defect,$data_array_defect,1);
			}
			// =========================== end reject (defect) entry ==============================



            if (str_replace("'", "", $txt_system_id) == "") {
                $challanrID = sql_insert("pro_gmts_delivery_mst", $field_array_delivery, $data_array_delivery, 1);
            } else {
                $challanrID = sql_update("pro_gmts_delivery_mst", $field_array_delivery, $data_array_delivery, "id", $txt_system_id, 1);
            }

            $rID = sql_insert("pro_garments_production_mst", $field_array_mst, $data_array_mst, 1);
            $dtlsrID = sql_insert("pro_garments_production_dtls", $field_array_dtls, $data_array_dtls, 1);
            //$bundlerID=sql_insert("pro_cut_delivery_color_dtls",$field_array_bundle,$data_array_bundle,1);
            //echo "10**insert into pro_cut_delivery_color_dtls (".$field_array_bundle.") values ".$data_array_bundle;die;
            //echo "10**".$challanrID ."&&". $rID ."&&". $dtlsrID ."&&". $bundlerID;die;
            //release lock table
            //check_table_status(160, 0);

            if ($db_type == 0) {
                if ($challanrID && $rID && $dtlsrID && $rIdDft) {
                    mysql_query("COMMIT");
                    echo "0**" . $mst_id . "**" . str_replace("'", "", $txt_challan_no);
                } else {
                    mysql_query("ROLLBACK");
                    echo "10**";
                }
            } else if ($db_type == 1 || $db_type == 2) {
                if ($challanrID && $rID && $dtlsrID && $rIdDft) {
                    oci_commit($con);
                    echo "0**" . $mst_id . "**" . str_replace("'", "", $txt_challan_no);
                } else {
                    oci_rollback($con);
                    echo "10**";
                }
            }
        } else {
           // $id = return_next_id("id", "pro_garments_production_mst", 1);
            $field_array1 = "id, delivery_mst_id, company_id, garments_nature, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, embel_name, embel_type, production_date, production_quantity,reject_qnty, production_type, entry_break_down_type, remarks, floor_id, inserted_by, insert_date";
            $id= return_next_id_by_sequence(  "pro_gar_production_mst_seq",  "pro_garments_production_mst", $con );

            $data_array1="(".$id.",".$mst_id.",".$cbo_company_name.",".$garments_nature.",'".$challan_no."',".$hidden_po_break_down_id.",".$cbo_item_name.",".$cbo_country_name.",".$cbo_source.",".$txt_embl_company_id.",".$txt_location_id.",".$cbo_embel_name.",".$cbo_embel_type.",".$txt_issue_date.",".$txt_receive_qty.",".$txt_reject_qty.",3,".$sewing_production_variable.",".$txt_remark.",".$txt_floor_id.",".$user_id.",'".$pc_date_time."')";

            //echo "INSERT INTO pro_garments_production_mst (".$field_array1.") VALUES ".$data_array1;die;
            // pro_garments_production_dtls table entry here ----------------------------------///
            $field_array = "id,delivery_mst_id,mst_id,production_type,color_size_break_down_id,production_qnty,reject_qty";
            $dtlsrID = true;
            if (str_replace("'", "", $sewing_production_variable) == 2) {//color level wise
                $color_sizeID_arr = sql_select("select id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and color_mst_id!=0 order by id");
                $colSizeID_arr = array();
                foreach ($color_sizeID_arr as $val) {
                    $index = $val[csf("color_number_id")];
                    $colSizeID_arr[$index] = $val[csf("id")];
                }

                //**********************Reject Qty ********************************************************************
                $rowExRej = explode("**", $colorIDvalueRej);
                foreach ($rowExRej as $rowR => $valR) {
                    $colorSizeRejIDArr = explode("*", $valR);
                    //echo $colorSizeRejIDArr[0]; die;
                    $rejQtyArr[$colorSizeRejIDArr[0]] = $colorSizeRejIDArr[1];
                }

                //**********************Reject Qty Finish **************************************************************
                // $colorIDvalue concate as colorID*Value**colorID*Value -------------------------//
                $rowEx = explode("**", $colorIDvalue);
                //$dtls_id = return_next_id("id", "pro_garments_production_dtls", 1);
                $data_array = "";
                $j = 0;
                foreach ($rowEx as $rowE => $val) {
                    $colorSizeNumberIDArr = explode("*", $val);
                    $dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );
                    if ($j == 0)
                        $data_array="(".$dtls_id.",".$mst_id.",".$id.",3,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."')";
                    else
                        $data_array.=",(".$dtls_id.",".$mst_id.",".$id.",3,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."')";
                    //$dtls_id = $dtls_id + 1;
                    $j++;
                }
            }

            if (str_replace("'", "", $sewing_production_variable) == 3) {//color and size wise
                $color_sizeID_arr = sql_select("select id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name order by size_number_id,color_number_id");
                $colSizeID_arr = array();
                foreach ($color_sizeID_arr as $val) {
                    $index = $val[csf("size_number_id")] . $val[csf("color_number_id")];
                    $colSizeID_arr[$index] = $val[csf('id')];
                }

                //	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------//
                $rowExRej = explode("***", $colorIDvalueRej);
                foreach ($rowExRej as $rowR => $valR) {
                    $colorAndSizeRej_arr = explode("*", $valR);
                    $sizeID = $colorAndSizeRej_arr[0];
                    $colorID = $colorAndSizeRej_arr[1];
                    $colorSizeRej = $colorAndSizeRej_arr[2];
                    $index = $sizeID . $colorID;
                    $rejQtyArr[$index] = $colorSizeRej;
                }

                $rowEx = explode("***", $colorIDvalue);
               // $dtls_id = return_next_id("id", "pro_garments_production_dtls", 1);
                $data_array = "";
                $j = 0;
                foreach ($rowEx as $rowE => $valE) {
                    $colorAndSizeAndValue_arr = explode("*", $valE);
                    $sizeID = $colorAndSizeAndValue_arr[0];
                    $colorID = $colorAndSizeAndValue_arr[1];
                    $colorSizeValue = $colorAndSizeAndValue_arr[2];
                    $index = $sizeID . $colorID;

                    //2 for Issue to Print / Emb Entry
                    $dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq", "pro_garments_production_dtls", $con );
                    if ($j == 0)
                        $data_array="(".$dtls_id.",".$mst_id.",".$id.",3,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$rejQtyArr[$index]."')";
                    else
                        $data_array.=",(".$dtls_id.",".$mst_id.",".$id.",3,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$rejQtyArr[$index]."')";
                  //  $dtls_id = $dtls_id + 1;
                    $j++;
                }
            }

            $rID = sql_insert("pro_garments_production_mst", $field_array1, $data_array1, 1);
            if (str_replace("'", "", $txt_system_id) == "") {
                $challanrID = sql_insert("pro_gmts_delivery_mst", $field_array_delivery, $data_array_delivery, 1);
            } else {
                $challanrID = sql_update("pro_gmts_delivery_mst", $field_array_delivery, $data_array_delivery, "id", $txt_system_id, 1);
            }

            $dtlsrID = true;
            if (str_replace("'", "", $sewing_production_variable) == 2 || str_replace("'", "", $sewing_production_variable) == 3) {
                //echo "10** insert into pro_gmts_delivery_mst($field_array_delivery)values".$data_array_delivery;die;
                $dtlsrID = sql_insert("pro_garments_production_dtls", $field_array, $data_array, 1);
            }


            //echo "10**".$rID."**".$challanrID."**".$dtlsrID."**".$challanrID."**".$txt_challan_no;die;
            if ($db_type == 0) {
                if ($rID && $challanrID && $dtlsrID) {
                    mysql_query("COMMIT");
                    echo "0**" . $mst_id . "**" . str_replace("'", "", $txt_challan_no);
                } else {
                    mysql_query("ROLLBACK");
                    echo "10**";
                }
            } else if ($db_type == 1 || $db_type == 2) {
                if ($rID && $challanrID && $dtlsrID) {
                    oci_commit($con);
                    echo "0**" . $mst_id . "**" . str_replace("'", "", $txt_challan_no);
                } else {
                    oci_rollback($con);
                    echo "10**";
                }
            }
        }
      //  check_table_status(160, 0);
        disconnect($con);
        die;
    } else if ($operation == 1) { // Update Here End------------------------------------------------------
        $con = connect();
        if ($db_type == 0) {
            mysql_query("BEGIN");
        }
        //table lock here
        //if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**2"; die;}
        //if  ( check_table_status( 160, 1 )==0 ) { echo "15**1"; die;}
        $mst_id = str_replace("'", "", $txt_system_id);
        $txt_chal_no = str_replace("'", "", $txt_challan_no);
        $txt_chal_no=explode("-",str_replace("'","",$txt_challan_no));
        $challan_no = (int) $txt_chal_no[3];

        $field_array_delivery = "location_id*embel_name*embel_type*production_source*serving_company*floor_id*organic*delivery_date*body_part*working_company_id*working_location_id*updated_by*update_date";
        $data_array_delivery="".$txt_location_id."*".$cbo_embel_name."*".$cbo_embel_type."*".$cbo_source."*".$txt_embl_company_id."*".$txt_floor_id."*".$txt_organic."*".$txt_issue_date."*".$cbo_body_part."*".$cbo_working_company_name."*".$cbo_working_location."*".$user_id."*'".$pc_date_time."'";

        for($j=1;$j<=$tot_row;$j++)
            {
                $bundleCheck="bundleNo_".$j;
                $is_rescan="isRescan_".$j;
                if($$is_rescan!=1)
                {
                  $bundleCheckArr[$$bundleCheck]=$$bundleCheck;
                }
                $cutNo="cutNo_".$j;
                $all_cut_no_arr[$$cutNo]=$$cutNo;
            }

            $cut_nums="'".implode("','", $all_cut_no_arr)."'";
            $bundle_wise_type_sql="SELECT b.bundle_no ,b.color_type_id from ppl_cut_lay_mst a,ppl_cut_lay_bundle b where a.id=b.mst_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.cutting_no in ($cut_nums) ";
            $bundle_wise_type_array=array();
            $bundle_wise_data=sql_select($bundle_wise_type_sql);
            foreach($bundle_wise_data as $vals)
            {
                $bundle_wise_type_array[$vals[csf("bundle_no")]]=$vals[csf("color_type_id")];
            }

            $bundle="'".implode("','",$bundleCheckArr)."'";
            $receive_sql="select c.barcode_no,c.bundle_no from pro_garments_production_mst a,pro_garments_production_dtls c where a.id=c.mst_id and a.production_type=3 and  a.embel_name=4 and c.bundle_no  in ($bundle)  and c.production_type=3 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.delivery_mst_id!=$mst_id and c.delivery_mst_id!=$mst_id and a.is_deleted=0 and (c.is_rescan=0 or c.is_rescan is null)";
            $receive_result = sql_select($receive_sql);
            foreach ($receive_result as $row)
            {

                $duplicate_bundle[$row[csf('bundle_no')]]=$row[csf('bundle_no')];
            }


        if (str_replace("'", "", $delivery_basis) == 3) {
          //  $id = return_next_id("id", "pro_garments_production_mst", 1);
            $field_array_mst = "id, delivery_mst_id,cut_no, company_id, garments_nature, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, embel_name, embel_type, production_date, production_quantity,reject_qnty,replace_qty, production_type, entry_break_down_type, remarks, floor_id, inserted_by, insert_date";

			$non_delete_arr=production_validation($mst_id,'3_4');
			$issue_data_arr=production_data($mst_id,'3_4');

            $mstArr = array();
            $dtlsArr = array();
            $colorSizeArr = array();
            $mstIdArr = array();
            $colorSizeIdArr = array();
            $mstArrRej = array();
            $dtlsArrRej = array();
            for ($j = 1; $j <= $tot_row; $j++) {

				$cutNo="cutNo_".$j;
				$bundleNo = "bundleNo_" . $j;
				$barcodeNo="barcodeNo_".$j;

                $orderId = "orderId_" . $j;
                $gmtsitemId = "gmtsitemId_" . $j;
                $countryId = "countryId_" . $j;
                $colorId = "colorId_" . $j;
                $sizeId = "sizeId_" . $j;
                $colorSizeId = "colorSizeId_" . $j;
                $qty = "qty_" . $j;
                $rejectQty = "rejectQty_" . $j;
                $replaceQty="replaceQty_".$j;
				$checkRescan="isRescan_".$j;
				unset($non_delete_arr[$$bundleNo]);

				$dtlsId="dtlsId_".$j;

				$last_operation=array();
				$last_operation=gmt_production_validation_script( 94, 2, $$colorSizeId, $$cutNo,$production_squence);

				if( gmt_production_validate_qnty( $last_operation, $$bundleNo, $$qty )!='' && $duplicate_bundle[$$bundleNo]=='')
				{
					//echo "select production_qnty,reject_qty,replace_qty from pro_garments_production_dtls where id=".$$dtlsId."";die;
					$sql_va=sql_select("select production_qnty,reject_qty,replace_qty from pro_garments_production_dtls where id='".$$dtlsId."'" );
					foreach($sql_va as $vrows)
					{
						$bundleCutArr[$$bundleNo]=$$cutNo;
						$bundleBarcodeArr[$$bundleNo]=$$barcodeNo;
						$cutArr[$$orderId][$$gmtsitemId][$$countryId]=$$cutNo;
						$mstArr[$$orderId][$$gmtsitemId][$$countryId]+=$vrows[csf("production_qnty")];
						$mstArrRej[$$orderId][$$gmtsitemId][$$countryId]+=$vrows[csf("reject_qty")];
						$mstArrReplace[$$orderId][$$gmtsitemId][$$countryId]+=$vrows[csf("replace_qty")];

						$colorSizeArr[$$bundleNo]=$$orderId."**".$$gmtsitemId."**".$$countryId;
						$dtlsArr[$$bundleNo]+=$vrows[csf("production_qnty")];
						$dtlsArrRej[$$bundleNo]+=$vrows[csf("reject_qty")];
						$dtlsArrReplace[$$bundleNo]+=$vrows[csf("replace_qty")];
						$dtlsArrColorSize[$$bundleNo]=$$colorSizeId;
						$bundleRescanArr[$$bundleNo]=$$checkRescan;
					}
					$mathced[$$bundleNo]=$$bundleNo;
				}
				else //if($chk==0)
				{
					if($duplicate_bundle[$$bundleNo]=='')
					{
						$bundleCutArr[$$bundleNo]=$$cutNo;
						$cutArr[$$orderId][$$gmtsitemId][$$countryId]=$$cutNo;
						$bundleBarcodeArr[$$bundleNo]=$$barcodeNo;

						$mstArr[$$orderId][$$gmtsitemId][$$countryId] += $$qty;
						$mstArrRej[$$orderId][$$gmtsitemId][$$countryId] += $$rejectQty;
						$mstArrReplace[$$orderId][$$gmtsitemId][$$countryId]+=$$replaceQty;
						$colorSizeArr[$$bundleNo] = $$orderId . "**" . $$gmtsitemId . "**" . $$countryId;
						$dtlsArr[$$bundleNo] += $$qty;
						$dtlsArrRej[$$bundleNo] += $$rejectQty;
						$dtlsArrReplace[$$bundleNo]+=$$replaceQty;
						$dtlsArrColorSize[$$bundleNo] = $$colorSizeId;
						$bundleRescanArr[$$bundleNo]=$$checkRescan;
					}

				}
            }
			foreach($non_delete_arr as $bi)
			{
				$last_operation=array();
				$last_operation=gmt_production_validation_script( 94, 2, $issue_data_arr[trim($bi)][csf('color_size_break_down_id')], $issue_data_arr[trim($bi)][csf('cut_no')],$production_squence);
				if( gmt_production_validate_qnty( $last_operation, $issue_data_arr[trim($bi)][csf('bundle_no')], $issue_data_arr[trim($bi)][csf('production_qnty')] )!='' )
				{
					$bundleCutArr[$issue_data_arr[trim($bi)][csf('bundle_no')]]=$issue_data_arr[trim($bi)][csf('cut_no')];
					$cutArr[$issue_data_arr[trim($bi)][csf('po_break_down_id')]][$issue_data_arr[trim($bi)][csf('item_number_id')]][$issue_data_arr[trim($bi)][csf('country_id')]]=$issue_data_arr[trim($bi)][csf('cut_no')];
					$mstArr[$issue_data_arr[trim($bi)][csf('po_break_down_id')]][$issue_data_arr[trim($bi)][csf('item_number_id')]][$issue_data_arr[trim($bi)][csf('country_id')]]+=$issue_data_arr[trim($bi)][csf('production_qnty')];
					$colorSizeArr[$issue_data_arr[trim($bi)][csf('bundle_no')]]=$issue_data_arr[trim($bi)][csf('po_break_down_id')]."**".$issue_data_arr[trim($bi)][csf('item_number_id')]."**".$issue_data_arr[trim($bi)][csf('country_id')];
					$dtlsArr[$issue_data_arr[trim($bi)][csf('bundle_no')]]+=$issue_data_arr[trim($bi)][csf('production_qnty')];
					$dtlsArrColorSize[$issue_data_arr[trim($bi)][csf('bundle_no')]]=$issue_data_arr[trim($bi)][csf('color_size_break_down_id')];
					$bundleBarcodeArr[$issue_data_arr[trim($bi)][csf('bundle_no')]]=$issue_data_arr[trim($bi)][csf('barcode_no')];
					$bundleRescanArr[$issue_data_arr[trim($bi)][csf('bundle_no')]]=$issue_data_arr[trim($bi)][csf('is_rescan')];
					$mathced[$issue_data_arr[trim($bi)][csf('bundle_no')]]=$issue_data_arr[trim($bi)][csf('bundle_no')];
				}
			}

            foreach ($mstArr as $orderId => $orderData) {
                foreach ($orderData as $gmtsItemId => $gmtsItemIdData) {
                    foreach ($gmtsItemIdData as $countryId => $qty) {

 					$id= return_next_id_by_sequence(  "pro_gar_production_mst_seq",  "pro_garments_production_mst", $con );
                        if ($data_array_mst != "")
                            $data_array_mst .= ",";
                        $data_array_mst.="(".$id.",".$mst_id.",'".$cutArr[$orderId][$gmtsItemId][$countryId]."',".$cbo_company_name.",".$garments_nature.",'".$challan_no."',".$orderId.",".$gmtsItemId.",".$countryId.",".$cbo_source.",".$txt_embl_company_id.",".$txt_location_id.",".$cbo_embel_name.",".$cbo_embel_type.",".$txt_issue_date.",".$qty.",'".$mstArrRej[$orderId][$gmtsItemId][$countryId]."','".$mstArrReplace[$orderId][$gmtsItemId][$countryId]."',3,".$sewing_production_variable.",'".$txt_remark."',".$txt_floor_id.",".$user_id.",'".$pc_date_time."')";
                        $mstIdArr[$orderId][$gmtsItemId][$countryId] = $id;
                       // $id = $id + 1;
                    }
                }
            }

         //   $dtls_id = return_next_id("id", "pro_garments_production_dtls", 1);
            $field_array_dtls = "id,delivery_mst_id,mst_id,production_type,color_size_break_down_id,production_qnty,reject_qty,replace_qty,cut_no,bundle_no,barcode_no,is_rescan,color_type_id";

            foreach ($dtlsArr as $bundle_no => $qty) {
            	$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );

                $colorSizedData = explode("**", $colorSizeArr[$bundle_no]);
                $gmtsMstId = $mstIdArr[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[2]];
				$cut_no=$bundleCutArr[$bundle_no];

                if ($data_array_dtls != "")
                    $data_array_dtls .= ",";
                $data_array_dtls.="(".$dtls_id.",".$mst_id.",".$gmtsMstId.",3,'".$dtlsArrColorSize[$bundle_no]."','".$qty."','".$dtlsArrRej[$bundle_no]."','".$dtlsArrReplace[$bundle_no]."','".$cut_no."','".$bundle_no."','".$bundleBarcodeArr[$bundle_no]."','".$bundleRescanArr[$bundle_no]."','".$bundle_wise_type_array[$bundle_no]."')";
                //$colorSizeIdArr[$colorSizeId]=$dtls_id;
               // $dtls_id = $dtls_id + 1;
            }
            // ========================= start reject (defect) entry ==================================

			$dft_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq", "pro_gmts_prod_dft", $con );
			$field_array_defect="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty,bundle_no,color_size_break_down_id,embel_name,inserted_by, insert_date";
			$data_array_defect = "";
			for($j=1;$j<=$tot_row;$j++)
			{
				$bundleNo="bundleNo_".$j;
				$barcodeNo="barcodeNo_".$j;
				$orderId="orderId_".$j;
				$gmtsitemId="gmtsitemId_".$j;
				$countryId="countryId_".$j;
				$colorSizeId="colorSizeId_".$j;
				$actualReject="actualReject_".$j;

				if($$actualReject!="")
				{
					$gmtsMstId=$mstIdArr[$$orderId][$$gmtsitemId][$$countryId];

					$actual_reject_info=explode("**",$$actualReject);
					for($rls=0;$rls<count($actual_reject_info); $rls++)
					{
						$bundle_reject_info=explode("*",$actual_reject_info[$rls]);
						if( trim($data_array_defect)!="") $data_array_defect.=",";

						$defectPointId=$bundle_reject_info[0];
						$defect_qty=$bundle_reject_info[1];

						//$field_array_defect="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty,bundle_no,embel_name, inserted_by, insert_date";

						$data_array_defect.="(".$dft_id.",".$gmtsMstId.",3,".$$orderId.",3,".$defectPointId.",'".$defect_qty."','".$$barcodeNo."',".$$colorSizeId.",1,".$user_id.",'".$pc_date_time."')";
						//$dft_id++;
						$dft_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con );
					}
				}
			}
			// =========================== end reject (defect) entry ==============================




            $delete = execute_query("DELETE FROM pro_garments_production_mst WHERE delivery_mst_id=$txt_system_id and production_type=3 and embel_name=4");
            $delete_dtls = execute_query("DELETE FROM pro_garments_production_dtls WHERE delivery_mst_id=$txt_system_id and production_type=3");
            //$delete_bundle = execute_query("DELETE FROM pro_cut_delivery_color_dtls WHERE delivery_mst_id=$txt_system_id and production_type=3 and embel_name=4");
            $challanrID = sql_update("pro_gmts_delivery_mst", $field_array_delivery, $data_array_delivery, "id", $txt_system_id, 1);
            $rID = sql_insert("pro_garments_production_mst", $field_array_mst, $data_array_mst, 1);
            $dtlsrID = sql_insert("pro_garments_production_dtls", $field_array_dtls, $data_array_dtls, 1);
            $rIdDft=1;
			if($data_array_defect!="")
			{
				//echo $data_array_defect;die;
				$rIdDft=sql_insert("pro_gmts_prod_dft",$field_array_defect,$data_array_defect,1);
			}
            //$bundlerID=sql_insert("pro_cut_delivery_color_dtls",$field_array_bundle,$data_array_bundle,1);
            //echo "10**";//insert into pro_garments_production_mst (".$field_array_mst.") values ".$data_array_mst;die;
            //echo $challanrID ."&&". $rID ."&&". $dtlsrID ."&&". $bundlerID ."&&". $delete ."&&". $delete_dtls ."&&". $delete_bundle;die;
            //release lock table


            if ($db_type == 0) {
                if ($challanrID && $rID && $dtlsrID && $delete && $delete_dtls && $rIdDft) {
                    mysql_query("COMMIT");
                    echo "1**" . $mst_id . "**" . str_replace("'", "", $txt_challan_no)."**".implode(",",$mathced);
                } else {
                    mysql_query("ROLLBACK");
                    echo "10**";
                }
            } else if ($db_type == 1 || $db_type == 2) {
                if ($challanrID && $rID && $dtlsrID && $delete && $delete_dtls && $rIdDft) {
                    oci_commit($con);
                    echo "1**" . $mst_id . "**" . str_replace("'", "", $txt_challan_no)."**".implode(",",$mathced);
                } else {
                    oci_rollback($con);
                    echo "10**";
                }
            }
        } else {
            // pro_garments_production_mst table data entry here
            $field_array1 = "production_source*serving_company*location*embel_name*embel_type*production_date*production_quantity*reject_qnty*production_type*entry_break_down_type*remarks*floor_id*total_produced*yet_to_produced*updated_by*update_date";

            $data_array1="".$cbo_source."*".$txt_embl_company_id."*".$txt_location_id."*".$cbo_embel_name."*".$cbo_embel_type."*".$txt_issue_date."*".$txt_receive_qty."*".$txt_reject_qty."*3*".$sewing_production_variable."*".$txt_remark."*".$txt_floor_id."*".$txt_cumul_receive_qty."*".$txt_yet_to_receive."*".$user_id."*'".$pc_date_time."'";
          // pro_garments_production_dtls table data entry here
            if (str_replace("'", "", $sewing_production_variable) != 1 && str_replace("'", "", $txt_mst_id) != '') {// check is not gross level

                $field_array = "id, mst_id, production_type, color_size_break_down_id, production_qnty,reject_qty";

                if (str_replace("'", "", $sewing_production_variable) == 2) {//color level wise
                    $color_sizeID_arr = sql_select("select id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and color_mst_id!=0 order by id");
                    $colSizeID_arr = array();
                    foreach ($color_sizeID_arr as $val) {
                        $index = $val[csf("color_number_id")];
                        $colSizeID_arr[$index] = $val[csf("id")];
                    }

                    // $colorIDvalue concate as colorID*Value**colorID*Value -------------------------//
                    $rowExRej = explode("**", $colorIDvalueRej);
                    foreach ($rowExRej as $rowR => $valR) {
                        $colorSizeRejIDArr = explode("*", $valR);
                        //echo $colorSizeRejIDArr[0]; die;
                        $rejQtyArr[$colorSizeRejIDArr[0]] = $colorSizeRejIDArr[1];
                    }

                    $rowEx = explode("**", $colorIDvalue);
                 //   $dtls_id = return_next_id("id", "pro_garments_production_dtls", 1);
                    $data_array = "";
                    $j = 0;
                    foreach ($rowEx as $rowE => $val) {
                        $colorSizeNumberIDArr = explode("*", $val);
                        //2 for Issue to Print / Emb Entry
                        $dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );
                        if ($j == 0)
                            $data_array = "(" . $dtls_id . "," . $txt_mst_id . ",3,'" . $colSizeID_arr[$colorSizeNumberIDArr[0]] . "','" . $colorSizeNumberIDArr[1] . "','" . $rejQtyArr[$colorSizeNumberIDArr[0]] . "')";
                        else
                            $data_array .= ",(" . $dtls_id . "," . $txt_mst_id . ",3,'" . $colSizeID_arr[$colorSizeNumberIDArr[0]] . "','" . $colorSizeNumberIDArr[1] . "','" . $rejQtyArr[$colorSizeNumberIDArr[0]] . "')";
                      //  $dtls_id = $dtls_id + 1;
                        $j++;
                    }
                }

                if (str_replace("'", "", $sewing_production_variable) == 3) {//color and size wise
                    $color_sizeID_arr = sql_select("select id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name order by size_number_id,color_number_id");
                    $colSizeID_arr = array();
                    foreach ($color_sizeID_arr as $val) {
                        $index = $val[csf("size_number_id")] . $val[csf("color_number_id")];
                        $colSizeID_arr[$index] = $val[csf('id')];
                    }

                    //	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------//
                    $rowExRej = explode("***", $colorIDvalueRej);
                    foreach ($rowExRej as $rowR => $valR) {
                        $colorAndSizeRej_arr = explode("*", $valR);
                        $sizeID = $colorAndSizeRej_arr[0];
                        $colorID = $colorAndSizeRej_arr[1];
                        $colorSizeRej = $colorAndSizeRej_arr[2];
                        $index = $sizeID . $colorID;
                        $rejQtyArr[$index] = $colorSizeRej;
                    }


                    $rowEx = explode("***", $colorIDvalue);
                   // $dtls_id = return_next_id("id", "pro_garments_production_dtls", 1);
                    $data_array = "";
                    $j = 0;
                    foreach ($rowEx as $rowE => $valE) {
                        $colorAndSizeAndValue_arr = explode("*", $valE);
                        $sizeID = $colorAndSizeAndValue_arr[0];
                        $colorID = $colorAndSizeAndValue_arr[1];
                        $colorSizeValue = $colorAndSizeAndValue_arr[2];
                        $index = $sizeID . $colorID;
                        //2 for Issue to Print / Emb Entry
                        $dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );
                        if ($j == 0)
                            $data_array = "(" . $dtls_id . "," . $txt_mst_id . ",3,'" . $colSizeID_arr[$index] . "','" . $colorSizeValue . "','" . $rejQtyArr[$index] . "')";
                        else
                            $data_array .= ",(" . $dtls_id . "," . $txt_mst_id . ",3,'" . $colSizeID_arr[$index] . "','" . $colorSizeValue . "','" . $rejQtyArr[$index] . "')";
                        //$dtls_id = $dtls_id + 1;
                        $j++;
                    }
                }

                //$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array,$data_array,1);
            }//end cond



            $challanrID = sql_update("pro_gmts_delivery_mst", $field_array_delivery, $data_array_delivery, "id", $txt_system_id, 1);
            $rID = sql_update("pro_garments_production_mst", $field_array1, $data_array1, "id", "" . $txt_mst_id . "", 1); //echo $rID;die;
            $dtlsrID = true;
            $dtlsrDelete = true;
            if (str_replace("'", "", $sewing_production_variable) != 1 && str_replace("'", "", $txt_mst_id) != '') {// check is not gross level
                $dtlsrDelete = execute_query("delete from pro_garments_production_dtls where mst_id=$txt_mst_id", 1);
                $dtlsrID = sql_insert("pro_garments_production_dtls", $field_array, $data_array, 1);
            }
            //echo "10**insert into pro_garments_production_dtls (".$field_array.") values ".$data_array;die;
            //echo "10**". $challanrID ."&&". $rID ."&&". $dtlsrID;die;
            //release lock table


            if ($db_type == 0) {
                if ($rID && $challanrID && $dtlsrID && $dtlsrDelete) {
                    mysql_query("COMMIT");
                    echo "1**" . $mst_id . "**" . str_replace("'", "", $txt_challan_no);
                } else {
                    mysql_query("ROLLBACK");
                    echo "10**";
                }
            } else if ($db_type == 2 || $db_type == 1) {
                if ($rID && $challanrID && $dtlsrID && $dtlsrDelete) {
                    oci_commit($con);
                    echo "1**" . $mst_id . "**" . str_replace("'", "", $txt_challan_no);
                } else {
                    oci_rollback($con);
                    echo "10**";
                }
            }
        }

		//check_table_status(160, 0);
        disconnect($con);
        die;
    } else if ($operation == 2) {  // Delete Here----------------------------------------------------------
        $con = connect();
        if ($db_type == 0) {
            mysql_query("BEGIN");
        }

        $rID = sql_delete("pro_garments_production_mst", "updated_by*update_date*status_active*is_deleted", "" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*0*1", 'id ', $txt_mst_id, 1);
        $dtlsrID = sql_delete("pro_garments_production_dtls", "status_active*is_deleted", "0*1", 'mst_id', $txt_mst_id, 1);
        $mst_id = str_replace("'", "", $txt_system_id);

        if ($db_type == 0) {
            if ($rID) {
                mysql_query("COMMIT");
                echo "2**" . $mst_id . "**" . str_replace("'", "", $txt_challan_no);
            } else {
                mysql_query("ROLLBACK");
                echo "10**" . $mst_id . "**" . str_replace("'", "", $txt_challan_no);
            }
        } else if ($db_type == 2 || $db_type == 1) {
            if ($rID) {
                oci_commit($con);
                echo "2**" . $mst_id . "**" . str_replace("'", "", $txt_challan_no);
            } else {
                oci_rollback($con);
                echo "10**" . $mst_id . "**" . str_replace("'", "", $txt_challan_no);
            }
        }
        disconnect($con);
        die;
    }
}

if ($action == "challan_no_popup") {
    echo load_html_head_contents("Challan Info", "../../", 1, 1, '', '', '');
    extract($_REQUEST);
    ?>

    <script>

        function js_set_value(id)
        {
            $('#hidden_mst_id').val(id);
            parent.emailwindow.hide();
        }

    </script>

    </head>

    <body>
        <div align="center" style="width:830px;">
            <form name="searchwofrm"  id="searchwofrm">
                <fieldset style="width:820px;">
                    <legend>Enter search words</legend>
                    <table cellpadding="0" cellspacing="0" width="700" border="1" rules="all" class="rpt_table">
                        <thead>
                        <th>Embel. Type</th>
                        <th>Cutting No</th>
                        <th>Enter Challan No</th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                            <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_name; ?>">
                            <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="text_boxes" value="">
                        </th>
                        </thead>
                        <tr class="general">
                            <td align="center">
                                <?
                                echo create_drop_down("cbo_embel_type", 160, $emblishment_spwork_type, "", 1, "---  Select Embel.Name ---", $selected, "");
                                ?>
                            </td>

                            <td align="center" id="search_by">
                                <input type="text" style="width:130px" class="text_boxes"  name="txt_cutt_no" id="txt_cutt_no" />
                           </td>
                            <td align="center" id="search_by_td">
                                <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                            </td>
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view(document.getElementById('txt_search_common').value + '_' + document.getElementById('cbo_embel_type').value + '_' + document.getElementById('txt_company_id').value+'_'+document.getElementById('txt_cutt_no').value, 'create_challan_search_list_view', 'search_div', 'special_work_challan_receive_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                            </td>

                        </tr>
                    </table>
                    <div style="width:100%; margin-top:10px; margin-left:3px" id="search_div" align="left"></div>
                </fieldset>
            </form>
        </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
}

if ($action == "create_challan_search_list_view") {
    $data = explode("_", $data);

    $search_string = "%" . trim($data[0]) . "";
    if ($data[1] == 0)
        $print_type_cond = "";
    else
        $print_type_cond = " and a.embel_type=$data[1]";
    $company_id = $data[2];
    $search_field_cond = " and a.sys_number like '$search_string'";

    if($data[3]!=""){ $cut_no=" and b.cut_no like '%$data[3]%' ";} else {$cut_no="";}

    if ($db_type == 0)
        $year_field = "YEAR(a.insert_date) as year";
    else if ($db_type == 2)
        $year_field = "to_char(a.insert_date,'YYYY') as year";
    else
        $year_field = ""; //defined Later

   $sql = "select a.id, $year_field, a.sys_number_prefix_num, a.sys_number,a.delivery_date,b.cut_no,b.production_quantity, a.embel_type, a.production_source, a.serving_company, a.location_id, a.floor_id, a.organic,a.issue_challan_id from pro_gmts_delivery_mst a, pro_garments_production_mst b where a.id=b.delivery_mst_id and  a.production_type=3 and a.embel_name=4 and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $search_field_cond $print_type_cond $cut_no order by a.id DESC";

    $result = sql_select($sql);
    $floor_arr = return_library_array("select id,floor_name from lib_prod_floor", 'id', 'floor_name');
    $location_arr = return_library_array("select id,location_name from lib_location", 'id', 'location_name');
    $company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
    ?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="60">Challan</th>
            <th width="80">RCV Date</th>
            <th width="45">Year</th>
            <th width="80">Embel. Type</th>
            <th width="100">Source</th>
            <th width="110">Embel. Company</th>
            <th width="85">Location</th>
            <th width="80">Floor</th>
            <th width="75">Cutting No</th>
            <th width="60">Organic</th>
            <th>Challan Qnty</th>
        </thead>
    </table>
    <div style="width:900px; max-height:240px; overflow-y:scroll" id="list_container_batch" align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="880" class="rpt_table" id="tbl_list_search">
    <?
    $i = 1;
    foreach ($result as $row) {
        if ($i % 2 == 0)
            $bgcolor = "#E9F3FF";
        else
            $bgcolor = "#FFFFFF";

        if ($row[csf('production_source')] == 1)
            $serv_comp = $company_arr[$row[csf('serving_company')]];
        else
            $serv_comp = $supplier_arr[$row[csf('serving_company')]];
        ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')] . "**" . $row[csf('issue_challan_id')]; ?>');">
                    <td width="40"><? echo $i; ?></td>
                    <td width="60"><p>&nbsp;<? echo $row[csf('sys_number_prefix_num')]; ?></p></td>
                    <td width="80"><p>&nbsp;<? echo change_date_format($row[csf('delivery_date')]); ?></p></td>
                    <td width="45" align="center"><p><? echo $row[csf('year')]; ?></p></td>
                    <td width="80"><p><? echo $emblishment_spwork_type[$row[csf('embel_type')]]; ?></p></td>
                    <td width="100"><p><? echo $knitting_source[$row[csf('production_source')]]; ?></p></td>
                    <td width="110"><p><? echo $serv_comp; ?></p></td>
                    <td width="85"><p><? echo $location_arr[$row[csf('location_id')]]; ?></p></td>
                    <td width="80"><p><? echo $floor_arr[$row[csf('floor_id')]]; ?></p></td>
                    <td width="75"><p><? echo $row[csf('cut_no')]; ?></p></td>
                    <td width="60"><p><? echo $row[csf('organic')]; ?></p></td>
                    <td><p><? echo  $row[csf('production_quantity')]; ?></p></td>
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

if ($action == 'populate_data_from_challan_popup') {
    $data_array = sql_select("select id, company_id, sys_number, embel_type, embel_name, production_source, serving_company, location_id,delivery_basis, floor_id, organic, delivery_date,issue_challan_id,body_part,working_company_id,working_location_id from pro_gmts_delivery_mst where id='$data'");
    foreach ($data_array as $row) {

		$issue_challan=return_field_value("sys_number","pro_gmts_delivery_mst"," id=".$row[csf('issue_challan_id')]." and status_active=1 and is_deleted=0");

        if ($row[csf('production_source')] == 1) {
            $serv_comp = $company_arr[$row[csf('serving_company')]];
        } else {
            $serv_comp = $supplier_arr[$row[csf('serving_company')]];
        }

        $location = $location_arr[$row[csf('location_id')]];
        $floor = $floor_arr[$row[csf('floor_id')]];
        echo "document.getElementById('txt_challan_no').value 				= '" . $row[csf("sys_number")] . "';\n";
        echo "document.getElementById('cbo_company_name').value 			= '" . $row[csf("company_id")] . "';\n";
        echo "$('#cbo_company_name').attr('disabled','true')" . ";\n";
        echo "$('#cbo_source').val('" . $row[csf('production_source')] . "');\n";
        //echo "load_drop_down( 'requires/special_work_challan_receive_controller', ".$row[csf('production_source')].", 'load_drop_down_embro_issue_source', 'emb_company_td' );\n";
        echo "load_drop_down( 'requires/special_work_challan_receive_controller','" . $row[csf('working_company_id')] . "', 'load_drop_down_working_location', 'working_location_td' );\n";
        echo "$('#txt_embl_company').val('" . $serv_comp . "');\n";

        echo "$('#txt_embl_company_id').val('" . $row[csf('serving_company')] . "');\n";
        echo "$('#txt_location_name').val('" . $location . "');\n";
        echo "$('#txt_location_id').val('" . $row[csf('location_id')] . "');\n";
        //echo "load_drop_down( 'requires/special_work_challan_receive_controller', ".$row[csf('location_id')].", 'load_drop_down_floor', 'floor_td' );\n";
        echo "$('#txt_issue_challan_id').val('" . $row[csf('issue_challan_id')] . "');\n";
		echo "$('#txt_issue_challan_scan').val('".$issue_challan."');\n";
        echo "$('#txt_floor_id').val('" . $row[csf('floor_id')] . "');\n";
        echo "$('#txt_floor_name').val('" . $floor . "');\n";
        echo "$('#cbo_embel_name').val('" . $row[csf('embel_name')] . "');\n";
        echo "$('#cbo_embel_type').val('" . $row[csf('embel_type')] . "');\n";
        echo "$('#txt_organic').val('" . $row[csf('organic')] . "');\n";
        echo "$('#txt_system_id').val('" . $row[csf('id')] . "');\n";
        echo "$('#txt_issue_date').val('" . change_date_format($row[csf('delivery_date')]) . "');\n";
        echo "$('#cbo_body_part').val('" . $row[csf('body_part')] . "');\n";
        echo "$('#cbo_working_company_name').val('" . $row[csf('working_company_id')] . "');\n";
        echo "$('#cbo_working_location').val('" . $row[csf('working_location_id')] . "');\n";
        echo "set_button_status(0, '" . $_SESSION['page_permission'] . "', 'fnc_issue_print_embroidery_entry',1,1);\n";
        exit();
    }
}

if ($action == "emblishment_issue_print") {
    extract($_REQUEST);
    $data = explode('*', $data);
    //print_r ($data);
    $company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
    $supplier_library = return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
    $location_library = return_library_array("select id, location_name from  lib_location", "id", "location_name");
    $floor_library = return_library_array("select id, floor_name from  lib_prod_floor", "id", "floor_name");
    $color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");
    $buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
    $body_part_arr = return_library_array("select id,bundle_use_for from ppl_bundle_title where company_id=$data[0]", 'id', 'bundle_use_for');
    $order_array = array();
    $order_sql = "SELECT a.job_no, a.buyer_name,a.style_ref_no,a.style_description, b.id, b.po_number, b.po_quantity from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0";
    $order_sql_result = sql_select($order_sql);
    foreach ($order_sql_result as $row) {
        $order_array[$row[csf('id')]]['buyer_name'] = $row[csf('buyer_name')];
        $order_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
        $order_array[$row[csf('id')]]['po_number'] = $row[csf('po_number')];
        $order_array[$row[csf('id')]]['po_quantity'] = $row[csf('po_quantity')];
        $order_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
        $order_array[$row[csf('id')]]['style_des'] = $row[csf('style_description')];
    }

    $sql = "select id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, location_id, delivery_basis, embel_name, embel_type,
	production_source, serving_company, floor_id, organic, delivery_date,body_part,working_company_id,working_location_id from pro_gmts_delivery_mst where production_type=3 and id='$data[1]' and
	status_active=1 and is_deleted=0 ";

    $dataArray = sql_select($sql);
    ?>
    <div style="width:930px;">
        <table width="900" cellspacing="0" align="right">
            <tr>
                <td colspan="6" align="center" style="font-size:24px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
            </tr>
            <tr class="form_caption">
                <td colspan="6" align="center" style="font-size:14px">
    <?
    $nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0] and status_active=1 and is_deleted=0");
    foreach ($nameArray as $result) {
        ?>
                        Plot No: <? echo $result[csf('plot_no')]; ?>
                        Level No: <? echo $result[csf('level_no')] ?>
                        Road No: <? echo $result[csf('road_no')]; ?>
                        Block No: <? echo $result[csf('block_no')]; ?>
                        City No: <? echo $result[csf('city')]; ?>
                        Zip Code: <? echo $result[csf('zip_code')]; ?>
                        Province No: <?php echo $result[csf('province')]; ?>
                        Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
                        Email Address: <? echo $result[csf('email')]; ?>
                        Website No: <?
                echo $result[csf('website')];
            }
            ?>
                </td>
            </tr>
            <tr>
                <td colspan="6" align="center" style="font-size:20px"><u><strong>Challan/Gate Pass</strong></u></td>
            </tr>
            <tr>
                <td width="125"><strong>Challan No</strong></td> <td width="175px">: <? echo $dataArray[0][csf('sys_number')]; ?></td>
                <td width="125"><strong>Embel. Name </strong></td><td width="175px">: <? echo $emblishment_name_array[$dataArray[0][csf('embel_name')]]; ?></td>
                <td width="125"><strong>Emb. Type</strong></td><td width="175px">:
    <?
    if ($dataArray[0][csf('embel_name')] == 1)
        echo $emblishment_print_type[$dataArray[0][csf('embel_type')]];
    elseif ($dataArray[0][csf('embel_name')] == 2)
        echo $emblishment_embroy_type[$dataArray[0][csf('embel_type')]];
    elseif ($dataArray[0][csf('embel_name')] == 3)
        echo $emblishment_wash_type[$dataArray[0][csf('embel_type')]];
    elseif ($dataArray[0][csf('embel_name')] == 4)
        echo $emblishment_spwork_type[$dataArray[0][csf('embel_type')]];
    ?>
                </td>
            </tr>
            <tr>
                <td><strong>Emb. Source</strong></td><td>: <? echo $knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
                <td><strong>Emb. Company</strong></td><td>:
    <?
    if ($dataArray[0][csf('production_source')] == 1)
        echo $company_library[$dataArray[0][csf('serving_company')]];
    else
        echo $supplier_library[$dataArray[0][csf('serving_company')]];
    ?>
                </td>
                <td><strong>Location</strong></td><td>: <? echo $location_library[$dataArray[0][csf('location_id')]]; ?></td>
            </tr>
            <tr>
                <td><strong>Floor </strong></td><td>: <? echo $floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
                <td><strong>Organic </strong></td><td>: <? echo $dataArray[0][csf('organic')]; ?></td>
                <td><strong>Delivery Date </strong></td><td>: <? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
            </tr>
            <tr>

                <td width="125"><strong>Working Company</strong></td><td>: <? echo $company_library[$dataArray[0][csf('working_company_id')]]; ?></td>
                <td><strong>Working Location </strong></td><td>: <? echo $location_library[$dataArray[0][csf('working_location_id')]]; ?></td>
                <td><strong>Body Part  </strong></td><td>: <? echo $body_part_arr[$dataArray[0][csf('body_part')]]; ?></td>
            </tr>
            <tr>
                <td  colspan="4" id="barcode_img_id"></td>
            </tr>
        </table>
        <br>
    <?
    $delivery_mst_id = $dataArray[0][csf('id')];
    if ($data[2] == 3) {

        $sql = "SELECT sum(b.production_qnty) as production_qnty,a.po_break_down_id,a.item_number_id,a.country_id, d.color_number_id,
				count(b.id) as 	num_of_bundle
				from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown d
				where a.delivery_mst_id ='$data[1]'
				and a.id=b.mst_id and b.color_size_break_down_id=d.id and b.production_type=3 and b.status_active=1 and b.is_deleted=0 and d.status_active in(1,2,3)
				and d.is_deleted=0
				group by a.po_break_down_id,a.item_number_id,a.country_id,d.color_number_id
				order by a.po_break_down_id,d.color_number_id ";
    } else {
        $sql = "SELECT sum(a.production_qnty) as production_qnty,c.po_break_down_id,c.item_number_id,c.country_id, b.color_number_id
				from pro_garments_production_dtls a, wo_po_color_size_breakdown b,pro_garments_production_mst c where c.delivery_mst_id ='$data[1]'
				and c.id=a.mst_id  and a.color_size_break_down_id=b.id and b.production_type=3 and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0
				group by c.po_break_down_id,c.item_number_id,c.country_id,b.color_number_id ";
    }


    $result = sql_select($sql);
    ?>

        <div style="width:100%;">
            <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" style=" margin-top:20px;" >
                <thead bgcolor="#dddddd" align="center">
                <th width="30">SL</th>
                <th width="80" align="center">Buyer</th>
                <th width="80" align="center">Job</th>
                <th width="80" align="center">Style Ref</th>
                <th width="100" align="center">Style Des</th>
                <th width="80" align="center">Order No.</th>
                <th width="80" align="center">Gmt. Item</th>
                <th width="80" align="center">Country</th>
                <th width="80" align="center">Color</th>
                <th width="80" align="center">Gmt. Qty</th>
    <? if ($data[2] == 3) { ?>
                    <th align="center">No of Bundle</th>
    <? } ?>
                </thead>
                <tbody>
    <?
    $i = 1;
    $tot_qnty = array();
    foreach ($result as $val) {
        if ($i % 2 == 0)
            $bgcolor = "#E9F3FF";
        else
            $bgcolor = "#FFFFFF";
        $color_count = count($cid);
        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>"  style="font-size:12px">
                            <td><? echo $i; ?></td>
                            <td align="center"><? echo $buyer_arr[$order_array[$val[csf('po_break_down_id')]]['buyer_name']]; ?></td>
                            <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['job_no']; ?></td>
                            <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['style_ref']; ?></td>
                            <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['style_des']; ?></td>
                            <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['po_number']; ?></td>
                            <td align="center"><? echo $garments_item[$val[csf('item_number_id')]]; ?></td>
                            <td align="center"><? echo $country_library[$val[csf('country_id')]]; ?></td>
                            <td align="center"><? echo $color_library[$val[csf('color_number_id')]]; ?></td>
                            <td align="right"><? echo $val[csf('production_qnty')]; ?></td>
                        <? if ($data[2] == 3) {
                            ?>
                                <td  align="center"> <? echo $val[csf('num_of_bundle')]; ?></td>
            <?
            $total_bundle += $val[csf('num_of_bundle')];
        }
        ?>

                        </tr>
        <?
        $production_quantity += $val[csf('production_qnty')];
        $i++;
    }
    ?>
                </tbody>
                <tr>
    <? if ($data[3] == 3) $colspan = 8;
    else $colspan = 7; ?>
                    <td colspan="9" align="right"><strong>Grand Total :</strong></td>
                    <td align="right"><? echo $production_quantity; ?></td>
    <? if ($data[2] == 3) { ?>
                        <td  align="center"> <? echo $total_bundle; ?></td>
                    <? } ?>
                </tr>
            </table>
            <br>
                    <?
                    // echo signature_table(26, $data[0], "900px");
                    ?>
        </div>
    </div>
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
                                function generateBarcode(valuess) {
                                    var value = valuess;//$("#barcodeValue").val();
                                    var btype = 'code39';//$("input[name=btype]:checked").val();
                                    var renderer = 'bmp';// $("input[name=renderer]:checked").val();
                                    var settings = {
                                        output: renderer,
                                        bgColor: '#FFFFFF',
                                        color: '#000000',
                                        barWidth: 1,
                                        barHeight: 30,
                                        moduleSize: 5,
                                        posX: 10,
                                        posY: 20,
                                        addQuietZone: 1
                                    };

                                    value = {code: value, rect: false};
                                    $("#barcode_img_id").show().barcode(value, btype, settings);
                                }
                                generateBarcode('<? echo $dataArray[0][csf('sys_number')]; ?>');
    </script>
    <?
    exit();
}
// new**************new**************new**************new**************new**************new**************new**************

if ($action == "load_mst_data") {
    //echo $data;die;
    $ex_data = "'".implode("','", explode(",", trim($data) ))."'";

    $txt_order_no = "%" . trim($ex_data[0]) . "%";
    $size_arr = return_library_array("select id, size_name from lib_size", 'id', 'size_name');
    $color_arr = return_library_array("select id, color_name from lib_color", "id", "color_name");
    $country_arr = return_library_array("select id, country_name from lib_country", 'id', 'country_name');

    $query="select a.sys_number, a.company_id,a.location_id, a.embel_name, a.embel_type, a.serving_company,a.floor_id,a.organic,a.production_source,a.body_part,a.working_company_id,a.working_location_id from  pro_gmts_delivery_mst a,pro_garments_production_dtls c where   a.id=c.delivery_mst_id and c.barcode_no in (".$ex_data.")  and c.production_type=2	and a.embel_name=4 and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0	group by a.sys_number, a.company_id,a.location_id, a.embel_name, a.embel_type, a.serving_company,a.floor_id,a.organic,a.production_source,a.body_part,a.working_company_id,a.working_location_id ";
    //  echo $query;
    $sql_mst_data = sql_select($query);


    foreach ($sql_mst_data as $val) {

        if ($val[csf('production_source')] == 1) {
            $serv_comp = $company_arr[$val[csf('serving_company')]];
        } else {
            $serv_comp = $supplier_arr[$val[csf('serving_company')]];
        }
		$location = $location_arr[$val[csf('location_id')]];
		$floor = $floor_arr[$val[csf('floor_id')]];
 		echo "$('#cbo_embel_type').val(" . $val[csf('embel_type')] . ");\n";
		echo "$('#cbo_source').val('" . $val[csf('production_source')] . "');\n";
		echo "$('#txt_embl_company').val('" . $serv_comp . "');\n";
		// echo "$('#txt_embl_company_id').val(" . $val[csf('serving_company')] . ");\n";
		echo "$('#txt_location_name').val('" . $location . "');\n";
		echo "$('#txt_floor_name').val('" . $floor . "');\n";
		echo "$('#txt_organic').val('" . $val[csf('organic')] . "');\n";
		echo "$('#txt_issue_challan_scan').val('" . $val[csf('sys_number')] . "');\n";
		echo "$('#txt_floor_id').val(" . $val[csf('floor_id')] . ");\n";
		echo "$('#txt_location_id').val(" . $val[csf('location_id')] . ");\n";
        echo "$('#cbo_body_part').val('".$val[csf('body_part')]."');\n";
        echo "$('#cbo_working_company_name').val(" . $val[csf('working_company_id')] . ");\n";
        echo "load_drop_down( 'requires/special_work_challan_receive_controller', " . $val[csf('working_company_id')] . ", 'load_drop_down_working_location', 'working_location_td' );\n";
        echo "$('#cbo_working_location').val(".$val[csf('working_location_id')].");\n";
    }
}


if ($action == "isssue_challan_no_popup") {
    echo load_html_head_contents("Challan Info", "../../", 1, 1, '', '', '');
    extract($_REQUEST);
    //if($delivery_basis==1)
    //{
    ?>

    <script>

        function js_set_value(id)
        {
            $('#hidden_mst_id').val(id);
            parent.emailwindow.hide();
        }

    </script>

    </head>

    <body>
        <div align="center" style="width:830px;">
            <form name="searchwofrm"  id="searchwofrm">
                <fieldset style="width:820px;">
                    <legend>Enter search words</legend>
                    <table cellpadding="0" cellspacing="0" width="700" border="1" rules="all" class="rpt_table">
                        <thead>
                        <th>Company Name</th>
                        <th>Embel. Type</th>
                        <th>Enter Challan No</th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                            <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_name; ?>">
                            <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="text_boxes" value="">
                        </th>
                        </thead>
                        <tr class="general">
                            <td align="center">
                    <?
                    echo create_drop_down("cbo_company_id", 180, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name", "id,company_name", 1, "-- Select --", $cbo_company_name, "", 1);
                    ?>
                            </td>
                            <td align="center">
                    <?
                    echo create_drop_down("cbo_embel_type", 160, $emblishment_spwork_type, "", 1, "--- Select Printing ---", $selected, "");
                    ?>
                            </td>
                            <td align="center" id="search_by_td">
                                <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                            </td>
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view(document.getElementById('txt_search_common').value + '_' + document.getElementById('cbo_embel_type').value + '_' + document.getElementById('cbo_company_id').value + '_' +<? echo $delivery_basis; ?>, 'create_issue_challan_search_list_view', 'search_div', 'special_work_challan_receive_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                            </td>

                        </tr>
                    </table>
                    <div style="width:100%; margin-top:10px; margin-left:3px" id="search_div" align="left"></div>
                </fieldset>
            </form>
        </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
}
// new need
if ($action == "create_bundle_challan_search_list_view") {
    $ex_data = explode("_", $data);
    $txt_order_no = "%" . trim($ex_data[0]) . "%";
    $company = $ex_data[1];
    $challan_no = "%" . trim($ex_data[2]) . "%";

    $size_arr = return_library_array("select id, size_name from lib_size", 'id', 'size_name');
    $color_arr = return_library_array("select id, color_name from lib_color", "id", "color_name");
    $country_arr = return_library_array("select id, country_name from lib_country", 'id', 'country_name');
    $scanned_bundle_arr = return_library_array("select bundle_no, bundle_no from pro_cut_delivery_color_dtls where production_type=3 and embel_name=4 and status_active=1 and is_deleted=0", 'bundle_no', 'bundle_no');

    $sql = "SELECT d.po_break_down_id, d.job_no_mst, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.bundle_no, c.production_qnty as qty, e.po_number from  pro_gmts_delivery_mst a,pro_cut_delivery_color_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e where   a.id=c.delivery_mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and a.company_id=$company and e.po_number like '$txt_order_no' and a.sys_number_prefix_num like '$challan_no' and c.production_type=2 and c.embel_name=4 and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and d.status_active in(1,2,3)";
    //echo $sql;die;
    $result = sql_select($sql);
    ?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table">
        <thead>
        <th width="40">SL</th>
        <th width="80">Job No</th>
        <th width="90">Order No</th>
        <th width="130">Gmts Item</th>
        <th width="110">Country</th>
        <th width="100">Color</th>
        <th width="70">Size</th>
        <th width="80">Bundle No</th>
        <th>Bundle Qty.</th>
    </thead>
    </table>
    <div style="width:800px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table" id="tbl_list_search">
    <?
    $i = 1;
    foreach ($result as $row) {
        if ($scanned_bundle_arr[$row[csf('bundle_no')]] == "") {
            if ($i % 2 == 0)
                $bgcolor = "#E9F3FF";
            else
                $bgcolor = "#FFFFFF";
            ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)">
                        <td width="40">
            <? echo $i; ?>
                            <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>" value="<?php echo $row[csf('bundle_no')]; ?>"/>
                        </td>
                        <td width="80"><p><? echo $row[csf('job_no_mst')]; ?></p></td>
                        <td width="90"><p><? echo $row[csf('po_number')]; ?></p></td>
                        <td width="130"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
                        <td width="110"><p><? echo $country_arr[$row[csf('country_id')]]; ?></p></td>
                        <td width="100"><p><? echo $color_arr[$row[csf('color_number_id')]]; ?></p></td>
                        <td width="70"><p><? echo $size_arr[$row[csf('size_number_id')]]; ?></p></td>
                        <td width="80"><? echo $row[csf('bundle_no')]; ?></td>
                        <td align="right"><? echo $row[csf('qty')]; ?>&nbsp;&nbsp;</td>
                    </tr>
            <?
            $i++;
        }
    }
    ?>
        </table>
    </div>
    <table width="720">
        <tr>
            <td align="center" >
                <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
            </td>
        </tr>
    </table>
    <?
    exit();
}




if ($action == "create_issue_challan_search_list_view") {
    $data = explode("_", $data);
    $supplier_arr = return_library_array("select id, supplier_name from  lib_supplier", 'id', 'supplier_name');
    $search_string = "%" . trim($data[0]) . "%";
    if ($data[1] == 0)
        $print_type_cond = "";
    else
        $print_type_cond = " and a.embel_type=$data[1]";
    $company_id = $data[2];
    $search_field_cond = " and a.sys_number like '$search_string'";
    $actual_delivery_basis = $data[3];
    if ($db_type == 0)
        $year_field = "YEAR(a.insert_date) as year";
    else if ($db_type == 2)
        $year_field = "to_char(a.insert_date,'YYYY') as year";
    else
        $year_field = ""; //defined Later

    if (str_replace("'", "", $company_id) == 0) {
        echo "Please Select Company first";
        die;
    }

    $delivery_basis = return_field_value("cut_panel_delevery", "variable_settings_production", "company_name=$company_id and variable_list=32 and
	status_active=1 and is_deleted=0");
    if ($actual_delivery_basis != $delivery_basis) {
        echo "Receive Basis " . $cut_panel_basis[$actual_delivery_basis] . " is not applicable in your setup.";
        die;
    }


    $sql = "select a.id, $year_field, a.sys_number_prefix_num,a.company_id, a.sys_number, a.embel_type, a.production_source, a.serving_company, a.location_id, a.floor_id, a.organic from pro_gmts_delivery_mst a where a.production_type=2 and a.embel_name=4 and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $search_field_cond $print_type_cond";
    //echo $sql;//die;
    $result = sql_select($sql);
    ?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table">
        <thead>
        <th width="40">SL</th>
        <th width="70">Challan</th>
        <th width="60">Year</th>
        <th width="80">Embel. Type</th>
        <th width="100">Source</th>
        <th width="110">Embel. Company</th>
        <th width="110">Location</th>
        <th width="100">Floor</th>
        <th>Organic</th>
    </thead>
    </table>
    <div style="width:800px; max-height:240px; overflow-y:scroll" id="list_container_batch" align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table" id="tbl_list_search">
    <?
    $i = 1;
    foreach ($result as $row) {
        if ($i % 2 == 0)
            $bgcolor = "#E9F3FF";
        else
            $bgcolor = "#FFFFFF";

        if ($row[csf('production_source')] == 1) {
            $serv_comp = $company_arr[$row[csf('serving_company')]];
        } else {
            $serv_comp = $supplier_arr[$row[csf('serving_company')]];
        }

        $location = $location_arr[$row[csf('location_id')]];
        $floor = $floor_arr[$row[csf('floor_id')]];
        //print_r($supplier_arr);
        //echo $serv_comp;die;
        ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')] . "_" . $row[csf('company_id')] . "_" . $row[csf('production_source')] . "_" . $row[csf('serving_company')] . "_" . $row[csf('location_id')] . "_" . $row[csf('floor_id')] . "_" . $row[csf('sys_number')] . "_" . $row[csf('organic')] . "_" . $row[csf('embel_type')] . "_" . $serv_comp . "_" . $location . "_" . $floor; ?>');">
                    <td width="40"><? echo $i; ?></td>
                    <td width="70"><p>&nbsp;<? echo $row[csf('sys_number_prefix_num')]; ?></p></td>
                    <td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
                    <td width="80"><p><? echo $emblishment_spwork_type[$row[csf('embel_type')]]; ?></p></td>
                    <td width="100"><p><? echo $knitting_source[$row[csf('production_source')]]; ?></p></td>
                    <td width="110"><p><? echo $serv_comp; ?></p></td>
                    <td width="110"><p><? echo $location; ?></p></td>
                    <td width="100"><p><? echo $floor_arr[$row[csf('floor_id')]]; ?></p></td>
                    <td><p><? echo $row[csf('organic')]; ?></p></td>
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

if ($action == "bundle_receive_special_work") {

    extract($_REQUEST);
    $data = explode('*', $data);
    //print_r ($data);
    $company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
    $supplier_library = return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
    $location_library = return_library_array("select id, location_name from  lib_location", "id", "location_name");
    $floor_library = return_library_array("select id, floor_name from  lib_prod_floor", "id", "floor_name");
    $color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");
    $size_library = return_library_array("select id,size_name from lib_size", "id", "size_name");
    $buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
    $body_part_arr = return_library_array("select id,bundle_use_for from ppl_bundle_title where company_id=$data[0]", 'id', 'bundle_use_for');

    //$order_array=array();
    $order_sql = "SELECT a.job_no, a.buyer_name, b.id, b.po_number, b.po_quantity from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst "
            . "and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0";
    $order_sql_result = sql_select($order_sql);
    foreach ($order_sql_result as $row) {
        $order_array[$row[csf('id')]]['buyer_name'] = $row[csf('buyer_name')];
        $order_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
        $order_array[$row[csf('id')]]['po_number'] = $row[csf('po_number')];
        $order_array[$row[csf('id')]]['po_quantity'] = $row[csf('po_quantity')];
    }
    // $sql will fetch header data from database

    $sql = "select id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, location_id, delivery_basis, embel_name, embel_type,	production_source, serving_company, floor_id, organic, delivery_date,body_part,working_company_id,working_location_id from pro_gmts_delivery_mst where production_type=3 and id='$data[1]' and status_active=1 and is_deleted=0 ";
    $dataArray = sql_select($sql);
    ?>
    <div style="width:1100px;">
        <table width="900" cellspacing="0" align="left">
            <tr>
                <td colspan="6" align="center" style="font-size:24px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
            </tr>
            <tr class="form_caption">
                <td colspan="6" align="center" style="font-size:14px">
    <?
    $nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0] and status_active=1 and is_deleted=0");
    foreach ($nameArray as $result) {
        ?>
                        Plot No: <? echo $result[csf('plot_no')]; ?>
                        Level No: <? echo $result[csf('level_no')] ?>
                        Road No: <? echo $result[csf('road_no')]; ?>
                        Block No: <? echo $result[csf('block_no')]; ?>
                        City No: <? echo $result[csf('city')]; ?>
                        Zip Code: <? echo $result[csf('zip_code')]; ?>
                        Province No: <?php echo $result[csf('province')]; ?>
                        Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
                        Email Address: <? echo $result[csf('email')]; ?>
                        Website No: <?
        echo $result[csf('website')];
    }
    ?>
                </td>
            </tr>
            <tr>
                <td colspan="6" align="center" style="font-size:20px"><u><strong>Challan/Gate Pass</strong></u></td>
            </tr>
            <tr>
                <td width="125"><strong>Challan No</strong></td> <td width="175px">: <? echo $dataArray[0][csf('sys_number')]; ?></td>
                <td width="125"><strong>Embel. Name </strong></td><td width="175px">: <? echo $emblishment_name_array[$dataArray[0][csf('embel_name')]]; ?></td>
                <td width="125"><strong>Emb. Type</strong></td>
                <td width="175px">:
    <?
    if ($dataArray[0][csf('embel_name')] == 1)
        echo $emblishment_print_type[$dataArray[0][csf('embel_type')]];
    elseif ($dataArray[0][csf('embel_name')] == 2)
        echo $emblishment_embroy_type[$dataArray[0][csf('embel_type')]];
    elseif ($dataArray[0][csf('embel_name')] == 3)
        echo $emblishment_wash_type[$dataArray[0][csf('embel_type')]];
    elseif ($dataArray[0][csf('embel_name')] == 4)
        echo $emblishment_spwork_type[$dataArray[0][csf('embel_type')]];
    ?>
                </td>
            </tr>
            <tr>
                <td><strong>Emb. Source</strong></td><td>: <? echo $knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
                <td><strong>Emb. Company</strong></td><td>:
            <?
            if ($dataArray[0][csf('production_source')] == 1)
                echo $company_library[$dataArray[0][csf('serving_company')]];
            else
                echo $supplier_library[$dataArray[0][csf('serving_company')]];
            ?>
                </td>
                <td><strong>Location</strong></td><td>: <? echo $location_library[$dataArray[0][csf('location_id')]]; ?></td>
            </tr>
            <tr>
                <td><strong>Floor</strong></td><td> : <? echo $floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
                <td><strong>Organic</strong></td><td> : <? echo $dataArray[0][csf('organic')]; ?></td>
                <td><strong>Delivery Date </strong></td><td>: <? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
            </tr>
            <tr>

                <td width="200"><strong>Working Company</strong></td><td>: <? echo $company_library[$dataArray[0][csf('working_company_id')]]; ?></td>
                <td width="200"><strong>Working Location </strong></td><td>: <? echo $location_library[$dataArray[0][csf('working_location_id')]]; ?></td>
                <td><strong>Body Part  </strong></td><td>: <? echo $body_part_arr[$dataArray[0][csf('body_part')]]; ?></td>
            </tr>
            <tr>
                <td  colspan="4" id="barcode_img_id"></td>
            </tr>


        </table>
        <br>
    <?
    if ($db_type == 2)
        $group_concat = "  listagg(cast(a.cut_no AS VARCHAR2(4000)),',') within group (order by a.cut_no) as cut_no";
    else if ($db_type == 0)
        $group_concat = " group_concat(a.cut_no) as cut_no";

    $delivery_mst_id = $dataArray[0][csf('id')];
    if ($data[2] == 3) {
        /*
          $sql="SELECT $group_concat, e.buyer_name, sum(b.production_qnty) as production_qnty, a.country_id,  d.color_number_id, d.size_number_id, e.style_ref_no, e.style_description,
          count(b.id) as 	num_of_bundle, sum(b.reject_qty) as reject_qty
          from pro_garments_production_mst a, pro_garments_production_dtls b,wo_po_color_size_breakdown d,
          wo_po_details_master e, wo_po_break_down f
          where a.delivery_mst_id ='$data[1]'
          and e.job_no=f.job_no_mst and f.id=a.po_break_down_id and a.id=b.mst_id and b.color_size_break_down_id=d.id and b.status_active=1 and b.is_deleted=0 and d.status_active=1
          and d.is_deleted=0
          group by e.buyer_name,a.country_id, d.color_number_id, d.size_number_id, e.style_ref_no, e.style_description
          order by e.buyer_name "; */

        $order_id_sql = "select a.po_break_down_id, b.order_cut_no from  pro_garments_production_mst a, ppl_cut_lay_dtls b where a.po_break_down_id=b.order_id and a.delivery_mst_id='$data[1]'";

        $order_id_sql_result = sql_select($order_id_sql);
        $order_cut_data = array();
        foreach ($order_id_sql_result as $row) {
            $order_cut_data[$row[csf("po_break_down_id")]] = $row[csf("order_cut_no")];
        }


        $sql = "SELECT a.cut_no, a.country_id, b.id, b.reject_qty, b.production_qnty, e.buyer_name,  d.color_number_id, d.size_number_id, e.job_no, e.style_ref_no, e.style_description, f.id as po_id
				from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown d, wo_po_details_master e, wo_po_break_down f
				where a.id=b.mst_id and b.color_size_break_down_id=d.id  and d.po_break_down_id=f.id and f.job_no_mst=e.job_no and f.id=a.po_break_down_id and a.delivery_mst_id ='$data[1]' and b.status_active=1 and b.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0";
        //echo $sql;
        $result = sql_select($sql);
        $bundle_color_size_data = array();
        $bundle_color_data = array();
        $bundle_size_arr = array();
        $grand_total_arr = array();
        $grand_total_size_arr = array();
        $bundle_check = array();
        foreach ($result as $fs) {
            $bundle_size_arr[$fs[csf('size_number_id')]] = $fs[csf('size_number_id')];
            $bundle_color_data[$fs[csf('job_no')]][$fs[csf('country_id')]][$fs[csf('color_number_id')]][$fs[csf('cut_no')]][$order_cut_data[$row[csf("po_id")]]]['style_ref'] = $fs[csf('style_ref_no')];
            $bundle_color_data[$fs[csf('job_no')]][$fs[csf('country_id')]][$fs[csf('color_number_id')]][$fs[csf('cut_no')]][$order_cut_data[$row[csf("po_id")]]]['style_des'] = $fs[csf('style_description')];
            $bundle_color_data[$fs[csf('job_no')]][$fs[csf('country_id')]][$fs[csf('color_number_id')]][$fs[csf('cut_no')]][$order_cut_data[$row[csf("po_id")]]]['tot_rcv_qnty'] += $fs[csf('production_qnty')];
            $bundle_color_data[$fs[csf('job_no')]][$fs[csf('country_id')]][$fs[csf('color_number_id')]][$fs[csf('cut_no')]][$order_cut_data[$row[csf("po_id")]]]['tot_reject_qty'] += $fs[csf('reject_qty')];

            $bundle_color_size_data[$fs[csf('job_no')]][$fs[csf('country_id')]][$fs[csf('color_number_id')]][$fs[csf('cut_no')]][$order_cut_data[$row[csf("po_id")]]][$fs[csf('size_number_id')]]['size_qnty'] += $fs[csf('production_qnty')];

            if ($bundle_check[$fs[csf('id')]] == "") {
                $bundle_check[$fs[csf('id')]] = $fs[csf('id')];
                $bundle_color_data[$fs[csf('job_no')]][$fs[csf('country_id')]][$fs[csf('color_number_id')]][$fs[csf('cut_no')]][$order_cut_data[$row[csf("po_id")]]]['no_of_bandle'] ++;
            }
        }





        //print_r($bundle_color_size_data[1]);die;
        $table_width = 900 + (count($bundle_size_arr) * 50);
        ?>

            <div style="width:100%;">
                <table align="left" cellspacing="0" width="<? echo $table_width; ?>"  border="1" rules="all" class="rpt_table" style=" margin-top:20px;" >
                    <thead bgcolor="#dddddd" align="center">
                        <tr>
                            <th width="30" rowspan="2">SL</th>
                            <th width="80" align="center" rowspan="2">Job No</th>
                            <th width="80" align="center" rowspan="2">Style Ref</th>
                            <th width="100" align="center" rowspan="2">Style Des</th>
                            <th width="80" align="center" rowspan="2">Country</th>
                            <th width="80" align="center" rowspan="2">Color</th>
                            <th width="80" align="center" rowspan="2">Cutting No</th>
                            <th width="80" align="center" rowspan="2">Order Cut</th>
                            <th align="center" width="50" colspan="<? echo count($bundle_size_arr); ?>">Size</th>
                            <th width="80" align="center" rowspan="2">Total Receive Qty</th>
                            <th width="80" align="center" rowspan="2">No of Bundle</th>
                            <th width="80" align="center" rowspan="2">Total Reject Qty</th>
                            <th width="80" align="center" rowspan="2">Remarks</th>
                        </tr>
                        <tr>
        <?
        $i = 0;
        foreach ($bundle_size_arr as $inf) {
            ?>
                                <th align="center" width="50"><? echo $size_library[$inf]; ?></th>
                            <?
                        }
                        ?>
                        </tr>
                    </thead>
                    <tbody>
        <?
        // print_r($bundle_color_size_data);die;
        $i = 1;
        $tot_qnty = array();
        $bundle_color_data[$fs[csf('job_no')]][$fs[csf('country_id')]][$fs[csf('color_number_id')]][$fs[csf('cut_no')]][$order_cut_data[$row[csf("po_id")]]]['style_ref'] = $fs[csf('style_ref_no')];
        foreach ($bundle_color_data as $job_no => $job_value) {
            foreach ($job_value as $county_id => $county_value) {
                foreach ($county_value as $color_id => $color_value) {
                    foreach ($color_value as $cut_num => $cut_value) {
                        foreach ($cut_value as $ord_cut_num => $ord_cut_value) {
                            if ($i % 2 == 0)
                                $bgcolor = "#E9F3FF";
                            else
                                $bgcolor = "#FFFFFF";
                            //$color_count=count($cid);
                            ?>
                                            <tr bgcolor="<? echo $bgcolor; ?>"  style="font-size:12px">
                                                <td align="center"><? echo $i; ?></td>
                                                <td align="center"><? echo $job_no; ?></td>
                                                <td align="center"><? echo $ord_cut_value["style_ref"]; ?></td>
                                                <td align="center"><? echo $ord_cut_value["style_des"]; ?></td>
                                                <td align="center"><? echo $country_library[$county_id]; ?></td>
                                                <td align="center"><? echo $color_library[$color_id]; ?></td>
                                                <td align="center"><? echo $cut_num; ?></td>
                                                <td align="center"><? echo $ord_cut_num; ?></td>
                            <?
                            foreach ($bundle_size_arr as $inf) {
                                ?>
                                                    <td align="right"><? echo $bundle_color_size_data[$job_no][$county_id][$color_id][$cut_num][$ord_cut_num][$inf]['size_qnty']; ?></td>
                                    <?
                                    $grand_total_size[$inf] += $bundle_color_size_data[$job_no][$county_id][$color_id][$cut_num][$ord_cut_num][$inf]['size_qnty'];
                                    $grand_total_aize = $grand_total_size . $bundle_color_size_data[$job_no][$county_id][$color_id][$cut_num][$ord_cut_num][$inf]['size_qnty'];
                                }
                                ?>

                                                <td align="center"><? echo $ord_cut_value["tot_rcv_qnty"];
                            $grand_rcv_qnty += $ord_cut_value["tot_rcv_qnty"]; ?></td>
                                                <td  align="center"><? echo $bundle_color_data[$job_no][$county_id][$color_id][$cut_num][$ord_cut_num]['no_of_bandle']; ?></td>
                                                <td align="right"><? echo $ord_cut_value["tot_reject_qty"];
                            $grand_reject_qnty += $ord_cut_value["tot_reject_qty"]; ?></td>
                                                <td  align="center">&nbsp;</td>
                                            </tr>
                                <?
                                $i++;
                            }
                        }
                    }
                }
            }
            ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="8" align="right"><strong>Grand Total :</strong></th>
            <?
            foreach ($bundle_size_arr as $inf) {
                ?>
                                <th align="center"><? echo $grand_total_size[$inf]; ?></th>
                <?
            }
            ?>
                            <th align="center"><? echo $grand_rcv_qnty; ?></th>
                            <th align="center">&nbsp;</th>
                            <th align="right"><? echo $grand_reject_qnty; ?></th>
                            <th  align="center">&nbsp;</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <?
        } else {
            $sql = "SELECT d.buyer_name,sum(b.production_qnty) as production_qnty,a.country_id, c.color_number_id,c.size_number_id,sum(b.reject_qty) as reject_qty

				from pro_garments_production_dtls b, wo_po_color_size_breakdown c,pro_garments_production_mst a,wo_po_details_master d, wo_po_break_down e
				where d.job_no=e.job_no_mst and e.id=a.po_break_down_id and a.delivery_mst_id ='$data[1]'
				and a.id=b.mst_id  and b.color_size_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0
				group by d.buyer_name,a.country_id, c.color_number_id,c.size_number_id";
            $result = sql_select($sql);
            $bundle_color_size_data = array();
            $bundle_color_data = array();
            $bundle_size_arr = array();
            $grand_total_arr = array();

            $grand_total_size_arr = array();
            foreach ($result as $fs) {
                $bundle_size_arr[$fs[csf('size_number_id')]] = $fs[csf('size_number_id')];
                $bundle_color_data[$fs[csf('buyer_name')]][$fs[csf('country_id')]][$fs[csf('color_number_id')]]['reject_qty'] += $fs[csf('reject_qty')];
                $bundle_color_data[$fs[csf('buyer_name')]][$fs[csf('country_id')]][$fs[csf('color_number_id')]]['qty'] += $fs[csf('production_qnty')];
                $bundle_color_size_data[$fs[csf('buyer_name')]][$fs[csf('country_id')]][$fs[csf('color_number_id')]][$fs[csf('size_number_id')]]['qty'] = $fs[csf('production_qnty')];
                $grand_total_arr['qty'] += $fs[csf('production_qnty')];
                $grand_total_arr['reject_qty'] += $fs[csf('reject_qty')];
                $grand_total_size_arr[$fs[csf('size_number_id')]] += $fs[csf('production_qnty')];
            }
            ?>

            <div style="width:100%;">
                <table align="left" cellspacing="0" width="<? echo $table_width; ?>"  border="1" rules="all" class="rpt_table"  style=" margin-top:20px;">
                    <thead bgcolor="#dddddd" align="center">
                        <tr>
                            <th width="30" rowspan="2">SL</th>
                            <th width="80" align="center" rowspan="2">Buyer</th>
                            <th width="80" align="center" rowspan="2">Country</th>
                            <th width="80" align="center" rowspan="2">Color</th>
                            <th align="center" width="50" colspan="<? echo count($bundle_size_arr); ?>">Size</th>
                            <th width="80" align="center" rowspan="2">Total Issue Qty</th>
                            <th width="80" align="center" rowspan="2">Reject Qty</th>
                            <th width= align="center" rowspan="2">Remarks</th>
                        </tr>
                        <tr>
                            <?
                            $i = 0;
                            foreach ($bundle_size_arr as $inf) {
                                ?>
                                <th align="center" width="50" rowspan="2"><? echo $size_library[$inf]; ?></th>
                                <?
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
        <?
        // print_r($bundle_color_size_data);die;
        $i = 1;
        $tot_qnty = array();
        foreach ($bundle_color_data as $buyer_id => $buy_value) {
            foreach ($buy_value as $county_id => $county_value) {
                foreach ($county_value as $color_id => $color_value) {
                    if ($i % 2 == 0)
                        $bgcolor = "#E9F3FF";
                    else
                        $bgcolor = "#FFFFFF";
                    $color_count = count($cid);
                    ?>
                                    <tr bgcolor="<? echo $bgcolor; ?>"  style="font-size:12px">
                                        <td><? echo $i; ?></td>
                                        <td align="center"><? echo $buyer_arr[$buyer_id]; ?></td>
                                        <td align="center"><? echo $country_library[$county_id]; ?></td>
                                        <td align="center"><? echo $color_library[$color_id]; ?></td>
                                    <?
                                    foreach ($bundle_size_arr as $inf) {
                                        ?>
                                            <td align="center" width="50"><? echo $bundle_color_size_data[$buyer_id][$county_id][$color_id][$inf]['qty']; ?></td>
                                        <?
                                    }
                                    ?>
                                        <td align="center"><? echo $bundle_color_data[$buyer_id][$county_id][$color_id]['qty']; ?></td>
                                        <td align="right"><? echo $bundle_color_data[$buyer_id][$county_id][$color_id]['reject_qty']; ?></td>
                                        <td  align="center"> <? //echo $val[csf('num_of_bundle')];  ?></td>
                                    </tr>
                    <?
                    $i++;
                }
            }
        }
        ?>
                    </tbody>
                    <tr>

                        <td colspan="4" align="right"><strong>Grand Total :</strong></td>
                            <?
                            foreach ($bundle_size_arr as $inf) {
                                ?>
                            <td align="center" width="50"><? echo $grand_total_size_arr[$inf]; ?></td>
            <?
        }
        ?>
                        <td align="center"><? echo $grand_total_arr['qty']; ?></td>
                        <td align="right"><? echo $grand_total_arr['reject_qty']; ?></td>
                        <td  align="center"> <? //echo $val[csf('num_of_bundle')]; ?></td>
                    </tr>
                </table>
            </div>
                        <?
                    }
                    ?>
        <br>
                    <?
                    // echo signature_table(26, $data[0], "900px");
                    ?>
    </div>
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
                        function generateBarcode(valuess) {
                            var value = valuess;//$("#barcodeValue").val();
                            var btype = 'code39';//$("input[name=btype]:checked").val();
                            var renderer = 'bmp';// $("input[name=renderer]:checked").val();
                            var settings = {
                                output: renderer,
                                bgColor: '#FFFFFF',
                                color: '#000000',
                                barWidth: 1,
                                barHeight: 30,
                                moduleSize: 5,
                                posX: 10,
                                posY: 20,
                                addQuietZone: 1
                            };

                            value = {code: value, rect: false};
                            $("#barcode_img_id").show().barcode(value, btype, settings);
                        }
                        generateBarcode('<? echo $dataArray[0][csf('sys_number')]; ?>');
    </script>
        <?
        exit();
    }

    if ($action == "embrodary_color_wise_print") {
        extract($_REQUEST);
        $data = explode('*', $data);
        //print_r ($data);
        $company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
        $supplier_library = return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
        $location_library = return_library_array("select id, location_name from  lib_location", "id", "location_name");
        $floor_library = return_library_array("select id, floor_name from  lib_prod_floor", "id", "floor_name");
        $color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");
        $size_library = return_library_array("select id,size_name from lib_size", "id", "size_name");
        $buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
        $body_part_arr = return_library_array("select id,bundle_use_for from ppl_bundle_title where company_id=$data[0]", 'id', 'bundle_use_for');

        $order_array = array();
        $order_sql = "SELECT a.job_no, a.buyer_name, b.id, b.po_number, b.po_quantity from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0";
        $order_sql_result = sql_select($order_sql);
        foreach ($order_sql_result as $row) {
            $order_array[$row[csf('id')]]['buyer_name'] = $row[csf('buyer_name')];
            $order_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
            $order_array[$row[csf('id')]]['po_number'] = $row[csf('po_number')];
            $order_array[$row[csf('id')]]['po_quantity'] = $row[csf('po_quantity')];
        }

        $sql = "select id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, location_id, delivery_basis, embel_name, embel_type,production_source, serving_company, floor_id, organic, delivery_date,body_part,working_company_id,working_location_id from pro_gmts_delivery_mst where production_type=3 and id='$data[1]' and
	status_active=1 and is_deleted=0 ";
        $dataArray = sql_select($sql);
        ?>
    <div style="width:1100px;">
        <table width="900" cellspacing="0" align="left">
            <tr>
                <td colspan="6" align="center" style="font-size:24px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
            </tr>
            <tr class="form_caption">
                <td colspan="6" align="center" style="font-size:14px">
                        <?
                        $nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0] and status_active=1 and is_deleted=0");
                        foreach ($nameArray as $result) {
                            ?>
                        Plot No: <? echo $result[csf('plot_no')]; ?>
                        Level No: <? echo $result[csf('level_no')] ?>
                        Road No: <? echo $result[csf('road_no')]; ?>
                        Block No: <? echo $result[csf('block_no')]; ?>
                        City No: <? echo $result[csf('city')]; ?>
                        Zip Code: <? echo $result[csf('zip_code')]; ?>
                        Province No: <?php echo $result[csf('province')]; ?>
                        Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
                        Email Address: <? echo $result[csf('email')]; ?>
                        Website No: <?
                        echo $result[csf('website')];
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td colspan="6" align="center" style="font-size:20px"><u><strong>Challan/Gate Pass</strong></u></td>
            </tr>
            <tr>
                <td width="125"><strong>Challan No</strong></td> <td width="175px">: <? echo $dataArray[0][csf('sys_number')]; ?></td>
                <td width="125"><strong>Embel. Name </strong></td><td width="175px">: <? echo $emblishment_name_array[$dataArray[0][csf('embel_name')]]; ?></td>
                <td width="125"><strong>Emb. Type</strong></td>
                <td width="175px">:
                    <?
                    if ($dataArray[0][csf('embel_name')] == 1)
                        echo $emblishment_print_type[$dataArray[0][csf('embel_type')]];
                    elseif ($dataArray[0][csf('embel_name')] == 2)
                        echo $emblishment_embroy_type[$dataArray[0][csf('embel_type')]];
                    elseif ($dataArray[0][csf('embel_name')] == 3)
                        echo $emblishment_wash_type[$dataArray[0][csf('embel_type')]];
                    elseif ($dataArray[0][csf('embel_name')] == 4)
                        echo $emblishment_spwork_type[$dataArray[0][csf('embel_type')]];
                    ?>
                </td>
            </tr>
            <tr>
                <td><strong>Emb. Source</strong></td><td>: <? echo $knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
                <td><strong>Emb. Company</strong></td><td>:
    <?
    if ($dataArray[0][csf('production_source')] == 1)
        echo $company_library[$dataArray[0][csf('serving_company')]];
    else
        echo $supplier_library[$dataArray[0][csf('serving_company')]];
    ?>
                </td>
                <td><strong>Location</strong></td><td>: <? echo $location_library[$dataArray[0][csf('location_id')]]; ?></td>
            </tr>
            <tr>
                <td><strong>Floor</strong></td><td> : <? echo $floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
                <td><strong>Organic</strong></td><td> : <? echo $dataArray[0][csf('organic')]; ?></td>
                <td><strong>Delivery Date </strong></td><td>: <? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
            </tr>
            <tr>

                <td width="125"><strong>Working Company</strong></td><td>: <? echo $company_library[$dataArray[0][csf('working_company_id')]]; ?></td>
                <td><strong>Working Location </strong></td><td>: <? echo $location_library[$dataArray[0][csf('working_location_id')]]; ?></td>
                <td><strong>Body Part  </strong></td><td>: <? echo $body_part_arr[$dataArray[0][csf('body_part')]]; ?></td>
            </tr>
            <tr>
                <td  colspan="4" id="barcode_img_id"></td>
            </tr>

        </table>
        <br>
        <?
        if ($db_type == 2)
            $group_concat = "  listagg(cast(a.cut_no AS VARCHAR2(4000)),',') within group (order by a.cut_no) as cut_no";
        else if ($db_type == 0)
            $group_concat = " group_concat(a.cut_no) as cut_no";

        $delivery_mst_id = $dataArray[0][csf('id')];
        if ($data[2] == 3) {

            $sql = "SELECT $group_concat,e.buyer_name,sum(b.production_qnty) as production_qnty,a.country_id, d.color_number_id,d.size_number_id,e.style_ref_no,e.style_description,
				count(b.id) as 	num_of_bundle,sum(b.reject_qty) as reject_qty
				from pro_garments_production_mst a, pro_garments_production_dtls b,wo_po_color_size_breakdown d,
				 wo_po_details_master e, wo_po_break_down f
				where a.delivery_mst_id ='$data[1]'
				and e.job_no=f.job_no_mst and f.id=a.po_break_down_id and a.id=b.mst_id and b.color_size_break_down_id=d.id and b.status_active=1 and b.is_deleted=0 and d.status_active in(1,2,3)
				and d.is_deleted=0
				group by e.buyer_name,a.country_id, d.color_number_id,d.size_number_id,e.style_ref_no,e.style_description
				order by e.buyer_name ";

            $result = sql_select($sql);
            $bundle_color_size_data = array();
            $bundle_color_data = array();
            $bundle_size_arr = array();
            $grand_total_arr = array();

            $grand_total_size_arr = array();
            foreach ($result as $fs) {
                $bundle_size_arr[$fs[csf('size_number_id')]] = $fs[csf('size_number_id')];
                $bundle_color_data[$fs[csf('buyer_name')]][$fs[csf('country_id')]][$fs[csf('color_number_id')]]['reject_qty'] += $fs[csf('reject_qty')];
                $bundle_color_data[$fs[csf('buyer_name')]][$fs[csf('country_id')]][$fs[csf('color_number_id')]]['qty'] += $fs[csf('production_qnty')];
                $bundle_color_data[$fs[csf('buyer_name')]][$fs[csf('country_id')]][$fs[csf('color_number_id')]]['bundle_num'] += $fs[csf('num_of_bundle')];
                $bundle_color_size_data[$fs[csf('buyer_name')]][$fs[csf('country_id')]][$fs[csf('color_number_id')]][$fs[csf('size_number_id')]]['qty'] = $fs[csf('production_qnty')];
                $grand_total_arr['qty'] += $fs[csf('production_qnty')];
                $grand_total_arr['bundle_num'] += $fs[csf('num_of_bundle')];
                $grand_total_arr['reject_qty'] += $fs[csf('reject_qty')];
                $grand_total_size_arr[$fs[csf('size_number_id')]] += $fs[csf('production_qnty')];

                $bundle_cut_data[$fs[csf('buyer_name')]][$fs[csf('country_id')]][$fs[csf('color_number_id')]]['cut_no'] = implode(',', array_unique(explode(',', $fs[csf('cut_no')])));

                $bundle_cut_data[$fs[csf('buyer_name')]][$fs[csf('country_id')]][$fs[csf('color_number_id')]]['style_ref'] = $fs[csf('style_ref_no')];
                $bundle_cut_data[$fs[csf('buyer_name')]][$fs[csf('country_id')]][$fs[csf('color_number_id')]]['style_des'] = $fs[csf('style_description')];
            }



            //print_r($bundle_color_size_data[1]);die;
            $table_width = 900 + (count($bundle_size_arr) * 50);
            ?>

            <div style="width:100%;">
                <table align="left" cellspacing="0" width="<? echo $table_width; ?>"  border="1" rules="all" class="rpt_table" style=" margin-top:20px;" >
                    <thead bgcolor="#dddddd" align="center">
                        <tr>
                            <th width="30" rowspan="2">SL</th>
                            <th width="80" align="center" rowspan="2">Buyer</th>
                            <th width="80" align="center" rowspan="2">Job No</th>
                            <th width="80" align="center" rowspan="2">Style Ref</th>
                            <th width="100" align="center" rowspan="2">Style Des</th>
                            <th width="80" align="center" rowspan="2">Country</th>
                            <th width="80" align="center" rowspan="2">Color</th>
                            <th width="80" align="center" rowspan="2">Cutting No</th>
                            <th width="80" align="center" rowspan="2">Order Cut</th>
                            <th width="80" align="center" rowspan="2">Size</th>
                            <th width="80" align="center" rowspan="2">Total Receive Qty</th>
                            <th width="80" align="center" rowspan="2">No of Bundle</th>
                            <th width="80" align="center" rowspan="2">Total Reject Qty</th>
                            <th width="80" align="center" rowspan="2">Remarks</th>

                            <th align="center" width="50" colspan="<? echo count($bundle_size_arr); ?>">Size</th>

                            <th width="80" align="center" rowspan="2">Total Issue Qty</th>
                            <th width="80" align="center" rowspan="2">No of Bundle</th>
                            <th width="80" align="center" rowspan="2">Reject Qty</th>
                            <th width= align="center" rowspan="2">Remarks</th>
                        </tr>
                        <tr>
        <?
        $i = 0;
        foreach ($bundle_size_arr as $inf) {
            ?>
                                <th align="center" width="50" rowspan="2"><? echo $size_library[$inf]; ?></th>
                            <?
                        }
                        ?>
                        </tr>
                    </thead>
                    <tbody>
        <?
        // print_r($bundle_color_size_data);die;
        $i = 1;
        $tot_qnty = array();
        foreach ($bundle_color_data as $buyer_id => $buy_value) {
            foreach ($buy_value as $county_id => $county_value) {
                foreach ($county_value as $color_id => $color_value) {
                    if ($i % 2 == 0)
                        $bgcolor = "#E9F3FF";
                    else
                        $bgcolor = "#FFFFFF";
                    $color_count = count($cid);
                    ?>
                                    <tr bgcolor="<? echo $bgcolor; ?>"  style="font-size:12px">
                                        <td><? echo $i; ?></td>
                                        <td align="center"><? echo $buyer_arr[$buyer_id]; ?></td>
                                        <td align="center"><? echo $bundle_cut_data[$buyer_id][$county_id][$color_id]['style_ref']; ?></td>
                                        <td align="center"><? echo $bundle_cut_data[$buyer_id][$county_id][$color_id]['style_des']; ?></td>
                                        <td align="center"><? echo $country_library[$county_id]; ?></td>
                                        <td align="center"><? echo $color_library[$color_id]; ?></td>
                                        <td align="center"><? echo $bundle_cut_data[$buyer_id][$county_id][$color_id]['cut_no']; ?></td>
                                    <?
                                    foreach ($bundle_size_arr as $inf) {
                                        ?>
                                            <td align="center" width="50"><? echo $bundle_color_size_data[$buyer_id][$county_id][$color_id][$inf]['qty']; ?></td>
                                        <?
                                    }
                                    ?>
                                        <td align="center"><? echo $bundle_color_data[$buyer_id][$county_id][$color_id]['qty']; ?></td>
                                        <td align="center"><? echo $bundle_color_data[$buyer_id][$county_id][$color_id]['bundle_num']; ?></td>
                                        <td align="right"><? echo $bundle_color_data[$buyer_id][$county_id][$color_id]['reject_qty']; ?></td>
                                        <td  align="center"> <? //echo $val[csf('num_of_bundle')]; ?></td>
                                    </tr>
                                    <?
                                    $i++;
                                }
                            }
                        }
                        ?>
                    </tbody>
                    <tr>

                        <td colspan="7" align="right"><strong>Grand Total :</strong></td>
        <?
        foreach ($bundle_size_arr as $inf) {
            ?>
                            <td align="center" width="50"><? echo $grand_total_size_arr[$inf]; ?></td>
            <?
        }
        ?>
                        <td align="center"><? echo $grand_total_arr['qty']; ?></td>
                        <td align="center"><? echo $grand_total_arr['bundle_num']; ?></td>
                        <td align="right"><? echo $grand_total_arr['reject_qty']; ?></td>
                        <td  align="center"> <? //echo $val[csf('num_of_bundle')];  ?></td>
                    </tr>
                </table>
            </div>
            <?
        } else {
            $sql = "SELECT d.buyer_name,sum(b.production_qnty) as production_qnty,a.country_id, c.color_number_id,c.size_number_id,sum(b.reject_qty) as reject_qty

			from pro_garments_production_dtls b, wo_po_color_size_breakdown c,pro_garments_production_mst a,wo_po_details_master d, wo_po_break_down e
			where d.job_no=e.job_no_mst and e.id=a.po_break_down_id and a.delivery_mst_id ='$data[1]'
			and a.id=b.mst_id  and b.color_size_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0
			group by d.buyer_name,a.country_id, c.color_number_id,c.size_number_id";
            $result = sql_select($sql);
            $bundle_color_size_data = array();
            $bundle_color_data = array();
            $bundle_size_arr = array();
            $grand_total_arr = array();

            $grand_total_size_arr = array();
            foreach ($result as $fs) {
                $bundle_size_arr[$fs[csf('size_number_id')]] = $fs[csf('size_number_id')];
                $bundle_color_data[$fs[csf('buyer_name')]][$fs[csf('country_id')]][$fs[csf('color_number_id')]]['reject_qty'] += $fs[csf('reject_qty')];
                $bundle_color_data[$fs[csf('buyer_name')]][$fs[csf('country_id')]][$fs[csf('color_number_id')]]['qty'] += $fs[csf('production_qnty')];
                $bundle_color_size_data[$fs[csf('buyer_name')]][$fs[csf('country_id')]][$fs[csf('color_number_id')]][$fs[csf('size_number_id')]]['qty'] = $fs[csf('production_qnty')];
                $grand_total_arr['qty'] += $fs[csf('production_qnty')];
                $grand_total_arr['reject_qty'] += $fs[csf('reject_qty')];
                $grand_total_size_arr[$fs[csf('size_number_id')]] += $fs[csf('production_qnty')];
            }
            ?>

            <div style="width:100%;">
                <table align="left" cellspacing="0" width="<? echo $table_width; ?>"  border="1" rules="all" class="rpt_table"  style=" margin-top:20px;">
                    <thead bgcolor="#dddddd" align="center">
                        <tr>
                            <th width="30" rowspan="2">SL</th>
                            <th width="80" align="center" rowspan="2">Buyer</th>
                            <th width="80" align="center" rowspan="2">Country</th>
                            <th width="80" align="center" rowspan="2">Color</th>
                            <th align="center" width="50" colspan="<? echo count($bundle_size_arr); ?>">Size</th>
                            <th width="80" align="center" rowspan="2">Total Issue Qty</th>
                            <th width="80" align="center" rowspan="2">Reject Qty</th>
                            <th width= align="center" rowspan="2">Remarks</th>
                        </tr>
                        <tr>
            <?
            $i = 0;
            foreach ($bundle_size_arr as $inf) {
                ?>
                                <th align="center" width="50" rowspan="2"><? echo $size_library[$inf]; ?></th>
                <?
            }
            ?>
                        </tr>
                    </thead>
                    <tbody>
        <?
        // print_r($bundle_color_size_data);die;
        $i = 1;
        $tot_qnty = array();
        foreach ($bundle_color_data as $buyer_id => $buy_value) {
            foreach ($buy_value as $county_id => $county_value) {
                foreach ($county_value as $color_id => $color_value) {
                    if ($i % 2 == 0)
                        $bgcolor = "#E9F3FF";
                    else
                        $bgcolor = "#FFFFFF";
                    $color_count = count($cid);
                    ?>
                                    <tr bgcolor="<? echo $bgcolor; ?>"  style="font-size:12px">
                                        <td><? echo $i; ?></td>
                                        <td align="center"><? echo $buyer_arr[$buyer_id]; ?></td>
                                        <td align="center"><? echo $country_library[$county_id]; ?></td>
                                        <td align="center"><? echo $color_library[$color_id]; ?></td>
                    <?
                    foreach ($bundle_size_arr as $inf) {
                        ?>
                                            <td align="center" width="50"><? echo $bundle_color_size_data[$buyer_id][$county_id][$color_id][$inf]['qty']; ?></td>
                        <?
                    }
                    ?>
                                        <td align="center"><? echo $bundle_color_data[$buyer_id][$county_id][$color_id]['qty']; ?></td>
                                        <td align="right"><? echo $bundle_color_data[$buyer_id][$county_id][$color_id]['reject_qty']; ?></td>
                                        <td  align="center"> <? //echo $val[csf('num_of_bundle')]; ?></td>
                                    </tr>
                                        <?
                                        $i++;
                                    }
                                }
                            }
                            ?>
                    </tbody>
                    <tr>

                        <td colspan="4" align="right"><strong>Grand Total :</strong></td>
                        <?
                        foreach ($bundle_size_arr as $inf) {
                            ?>
                            <td align="center" width="50"><? echo $grand_total_size_arr[$inf]; ?></td>
                            <?
                        }
                        ?>
                        <td align="center"><? echo $grand_total_arr['qty']; ?></td>
                        <td align="right"><? echo $grand_total_arr['reject_qty']; ?></td>
                        <td  align="center"> <? //echo $val[csf('num_of_bundle')]; ?></td>
                    </tr>
                </table>
            </div>
                        <?
                    }
                    ?>
        <br>

    <?
    // echo signature_table(26, $data[0], "900px");
    ?>
    </div>
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
                        function generateBarcode(valuess) {
                            var value = valuess;//$("#barcodeValue").val();
                            var btype = 'code39';//$("input[name=btype]:checked").val();
                            var renderer = 'bmp';// $("input[name=renderer]:checked").val();
                            var settings = {
                                output: renderer,
                                bgColor: '#FFFFFF',
                                color: '#000000',
                                barWidth: 1,
                                barHeight: 30,
                                moduleSize: 5,
                                posX: 10,
                                posY: 20,
                                addQuietZone: 1
                            };

                            value = {code: value, rect: false};
                            $("#barcode_img_id").show().barcode(value, btype, settings);
                        }
                        generateBarcode('<? echo $dataArray[0][csf('sys_number')]; ?>');
    </script>
                    <?
                    exit();
                }
    if($action=="reject_qty_popup")
    {
        extract($_REQUEST);
        echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
        $caption_name="";
        //print_r($sew_fin_alter_defect_type);die;

        ?>
        <script>
            function fnc_close()
            {
                var save_string='';
                var total_qty=0;
                $("#tbl_list_search").find('tr').each(function()
                {

                    var txtDefectQnty=$(this).find('input[name="txtDefectQnty[]"]').val();
                    var txtDefectId=$(this).find('input[name="txtDefectId[]"]').val();
                    //var txtDefectUpdateId=$(this).find('input[name="txtDefectUpdateId[]"]').val();

                    if(txtDefectQnty*1>0)
                    {
                        if(save_string=="")
                        {
                            save_string=txtDefectId+"*"+txtDefectQnty;
                            total_qty+=txtDefectQnty*1;
                        }
                        else
                        {
                            save_string+="**"+txtDefectId+"*"+txtDefectQnty;
                            total_qty+=txtDefectQnty*1;
                        }
                    }
                });

                $('#actual_reject_infos').val( save_string );
                $('#actual_reject_qty').val(total_qty);

                parent.emailwindow.hide();
            }
            function calculate_reject()
            {
                var reject_qty=0;
                $("#tbl_list_search").find('tbody tr').each(function()
                    {
                        //alert(4);
                        var qty =$(this).find('input[name="txtDefectQnty[]"]').val()*1;
                        // console.log(Number(qty));
                        // reject_qty+=$(this).find('input[name="txtDefectQnty[]"]').val()*1;
                        if(!Number.isNaN(qty)){
                            reject_qty+=Number(qty);
                    }

                    });
                $("#reject_qty_td").text(reject_qty);
            }

        </script>
        </head>
        <body>
            <div align="center" style="width:100%;" >
            <form name="defect_1"  id="defect_1" autocomplete="off">
                <? //echo load_freeze_divs ("../../",$permission,1); ?>
                <fieldset style="width:360px;">

                <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="350">
                    <thead>
                        <tr><th colspan="3">Reject Record</th></tr>
                        <tr><th width="40">SL</th><th width="150">Reject Name</th><th>No. of Defect</th></tr>
                    </thead>
                </table>
                <div style="width:350px; max-height:320px; overflow-y:scroll" id="list_container" align="left">
                    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="320" id="tbl_list_search">
                    <tbody>
                        <?

                            $explSaveData = explode("**",$actual_infos);

                            $defect_dataArray=array();
                            foreach($explSaveData as $val)
                            {
                                $difectVal = explode("*",$val);
                                //$defect_dataArray['up_id']=$difectVal[0];
                                $defect_dataArray[$difectVal[0]]['defectid']=$difectVal[0];
                                $defect_dataArray[$difectVal[0]]['defectQnty']=$difectVal[1];
                            }

                            $i=1;
                            $total_reject=0;
                            $cutting_qc_reject_type=array(53=> 'Fabric Hole',58 => "Shiny mark",2 => "Dirty Spot",59 => "Uneven Cut Panel",60 => "Color Shade Deviation",61 => "Wong Artwork",62 => "Pin Hole",63 => "Design Mistake",64 => "Color Mistake",65 => "Color Passing",66 => "Print Position Deviation", 67 => "Uneven print", 68 => "Puff Print High & Low",49=> 'Color Spot');
                            //1.fabric hole 2.shiny mark 3.darty spot 4.uneven cut panel 5.color shade deviation 6.wong artwork 7.pin hole 8. design mistake 9.color mistake 10. color passing 11. print position deviation 12. uneven print 13.puff print high

                            foreach($cutting_qc_reject_type as $id=>$val)
                            {
                                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                $total_reject+=$defect_dataArray[$id]['defectQnty'];
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
                                    <td width="40"><? echo $i; ?></td>
                                    <td width="150"><? echo $val; ?></td>
                                    <td align="center">
                                        <input type="text" name="txtDefectQnty[]" id="txtDefectQnty_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" value="<? echo $defect_dataArray[$id]['defectQnty']; ?>"  onKeyUp="calculate_reject()">
                                        <input type="hidden" name="txtDefectId[]" id="txtDefectId_<? echo $i; ?>" style="width:40px" value="<? echo $id; ?>">
                                        <input type="hidden" name="txtDefectUpdateId[]" id="txtDefectUpdateId_<? echo $i; ?>" style="width:40px" value="<? echo $defect_dataArray[$id]['up_id']; ?>">
                                    </td>
                                </tr>
                                <?
                                $i++;
                            }
                        ?>
                        </tbody>
                        <tfoot>
                            <tr class="tbl_bottom">
                                <td align="right" colspan="2">Total</td>

                                <td align="right"  id="reject_qty_td" style="padding-right:20px"> <? echo $total_reject; ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <table width="320" id="table_id">
                    <tr>
                        <td align="center" colspan="3">
                            <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
                            <input type="hidden" id="actual_reject_infos" />
                            <input type="hidden" id="actual_reject_qty" />
                        </td>
                    </tr>
                </table>
                </fieldset>
            </form>
            </div>
            <script>
            setFilterGrid('tbl_list_search',-1);
            </script>
        </body>
        <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
        </html>
        <?
    }

?>
