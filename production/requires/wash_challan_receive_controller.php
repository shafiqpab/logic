<?
session_start();
include('../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//------------------------------------------------------------------------------------------------------
$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name" );
$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
$floor_arr=return_library_array( "select id,floor_name from lib_prod_floor",'id','floor_name');
$location_arr=return_library_array( "select id,location_name from lib_location",'id','location_name');
$supplier_arr=return_library_array( "select id, supplier_name from  lib_supplier",'id','supplier_name');

if($action=="get_challan_id")
{
	echo return_field_value("id","pro_gmts_delivery_mst","sys_number='$data'");
	exit();
}
  
if ($action=="load_variable_settings")
{
	echo "$('#sewing_production_variable').val(0);\n";
	$sql_result = sql_select("select printing_emb_production,production_entry from variable_settings_production where company_name=$data and variable_list=1 and status_active=1");
 	foreach($sql_result as $result)
	{
		echo "$('#sewing_production_variable').val(".$result[csf("printing_emb_production")].");\n";
		echo "$('#styleOrOrderWisw').val(".$result[csf("production_entry")].");\n";
	}
	
	echo "$('#delivery_basis').val(0);\n";
	$delivery_basis=return_field_value("cut_panel_delevery","variable_settings_production","company_name=$data and variable_list=32 and status_active=1 and is_deleted=0");
	if($delivery_basis==3 || $delivery_basis==2) $delivery_basis=3; else $delivery_basis=1;
	echo "$('#delivery_basis').val(".$delivery_basis.");\n";
	
	echo "$('#embro_production_variable').val(0);\n";

	$sql_result = sql_select("select printing_emb_production from variable_settings_production where company_name=$data and variable_list=28 and status_active=1");
 	foreach($sql_result as $result)
	{
		echo "$('#embro_production_variable').val(".$result[csf("printing_emb_production")].");\n";
	}
 	exit();
}

if ($action=="load_variable_settings_for_working_company")
{
	$sql_result = sql_select("select working_company_mandatory from variable_settings_production where company_name=$data and variable_list=41 and status_active=1");
	
	$working_company="";
 	foreach($sql_result as $row)
	{
		$working_company=$row[csf("working_company_mandatory")];
	}
	echo $working_company;
	
 	exit();
}

 if ($action=="load_drop_down_working_location")
{
	echo create_drop_down( "cbo_working_location", 180, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "",0 );
	exit();   
}
/*if($action=="populate_data_from_search_popup")
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
}*/

if($action=="bundle_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
?>
	<script>
	
	
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
		
		function js_set_value( str) 
		{
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
		<legend>Enter search words <input type="checkbox" value="1" name="is_exact" id="is_exact" checked > is exact</legend>           
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
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('txt_order_no').value+'_'+<? echo $company; ?>+'_'+document.getElementById('bundle_no').value+'_'+'<? echo trim($bundleNo,','); ?>'+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_cut_no').value+'_'+document.getElementById('cbo_cut_year').value+'_'+$('#is_exact').is(':checked'), 'create_bundle_search_list_view', 'search_div', 'wash_challan_receive_controller', 'setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')" style="width:100px;" />
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
	if(trim($cut_no)=='')
	{
		echo "<h2 style='color:#D00; text-align:center;'>Please Select-Cut No</h2>";exit();
	}
 	$company_short_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	//list($short_name)=explode('-',$company_short_arr[$company]);
	$cutConvertToInt= convertToInt('c.cut_no',array($company_short_arr[$company],'-'),'cut_no');
	$bundleConvertToInt = convertToInt('c.bundle_no',array($company_short_arr[$company],'-',"/"),'order_bundle_no');
	
	
	$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	
	
	
	if ($cut_no != '') {
		if($is_exact=='true')
		{
			$cutCon = " and c.cut_no = '".trim($company_short_arr[$company].'-'.$syear.'-'.str_pad($cut_no,6,"0",STR_PAD_LEFT))."'";
			$cutCon_a = " and a.cut_no = '".trim($company_short_arr[$company].'-'.$syear.'-'.str_pad($cut_no,6,"0",STR_PAD_LEFT))."'";
		}
		else
		{
			$cutCon = " and c.cut_no like '%".$cut_no."'";
			$cutCon_a = " and a.cut_no like '%".$cut_no."'";
		}
    }
	if($job_no!=''){$jobCon=" and f.job_no_prefix_num = $job_no";}

	$scanned_bundle_arr=return_library_array( "select b.bundle_no, b.bundle_no from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and b.production_type=3 and a.embel_name=3 $cutCon_a and b.status_active=1 and b.is_deleted=0",'bundle_no','bundle_no');
	
	foreach(explode(",",$selectedBuldle) as $bn)
	{
		$scanned_bundle_arr[$bn]=$bn;	
	}


	
	
	$sql="select $cutConvertToInt, $bundleConvertToInt ,d.po_break_down_id, d.job_no_mst, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.bundle_no, c.production_qnty as qty, e.po_number from pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e ,wo_po_details_master f where d.job_no_mst=f.job_no and  a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and a.company_id=$company and e.po_number like '$txt_order_no' and c.bundle_no like '$bundle_no' $jobCon $cutCon  and c.production_type=2 and a.embel_name=3 and a.status_active=1 and a.is_deleted=0 order by  length(c.bundle_no) asc, c.bundle_no asc";
	
	
	
	
	
	$result = sql_select($sql);

	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="830" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="50">Year</th>
            <th width="50">Job No</th>
            <th width="90">Order No</th>
            <th width="130">Gmts Item</th>
            <th width="110">Country</th>
            <th width="100">Color</th>
            <th width="50">Size</th>
            <th width="50">Cut No</th>
            <th width="80">Bundle No</th>
            <th>Bundle Qty.</th>
        </thead>
	</table>
	<div style="width:850px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="830" class="rpt_table" id="tbl_list_search">  
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
							 <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>" value="<?php echo $row[csf('bundle_no')]; ?>"/>
						</td>
						<td width="50" align="center"><p><? echo $year; ?></p></td>
						<td width="50" align="center"><p><? echo $job*1; ?></p></td>
						<td width="90"><p><? echo $row[csf('po_number')]; ?></p></td>
						<td width="130"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
                        <td width="110"><p><? echo $country_arr[$row[csf('country_id')]]; ?></p></td>
                        <td width="100"><p><? echo $color_arr[$row[csf('color_number_id')]]; ?></p></td>
                        <td width="50"><p><? echo $size_arr[$row[csf('size_number_id')]]; ?></p></td>
						<td width="50"><? echo $row[csf('cut_no')]; ?></td>
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

		$result=sql_select("select a.challan_no,b.bundle_no from pro_garments_production_mst a,pro_garments_production_dtls b where a.delivery_mst_id=b.delivery_mst_id and b.bundle_no='$data' and b.production_type=3 and a.embel_name=3 and a.status_active=1 and
		a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.challan_no,b.bundle_no");
	
	foreach ($result as $row)
	{ 
		echo "alert('Bundle No ".$row[csf('bundle_no')]." Found in Challan No ".$row[csf('challan_no')].".');";

	}
exit();
}




if($action=="populate_bundle_data")
{
 	$ex_data = explode("**",$data);
	$bundle=explode(",",$ex_data[0]);
	$mst_id=explode(",",$ex_data[2]);
	$bundle_nos="'".trim(implode("','",$bundle))."'";
	
	$bundle_count=count(explode(",",$bundle_nos)); $bundle_nos_cond="";
	if($db_type==2 && $bundle_count>400)
	{
		$bundle_nos_cond=" and (";
		$bundleArr=array_chunk(explode(",",$bundle_nos),399);
		foreach($bundleArr as $bundleNos)
		{
			$bundleNos=implode(",",$bundleNos);
			$bundle_nos_cond.=" c.bundle_no in($bundleNos) or ";
		}
		$bundle_nos_cond=chop($bundle_nos_cond,'or ');
		$bundle_nos_cond.=")";
	}
	else
	{
		$bundle_nos_cond=" and c.bundle_no in ($bundle_nos)";
	}
	
	$scanned_bundle_arr=return_library_array( "select b.bundle_no, b.bundle_no from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and b.production_type=3 and a.embel_name=3 and b.status_active=1 and b.is_deleted=0",'bundle_no','bundle_no');
	
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
	 
	$sql="SELECT d.id as colorsizeid, e.id as po_id, f.job_no_prefix_num, MAX($year_field) as year, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id,c.cut_no, c.bundle_no, c.production_qnty as qty, e.po_number from pro_gmts_delivery_mst a,pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f where  a.id=c.delivery_mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_no_mst=f.job_no and c.production_type=2 and a.embel_name=3 and c.status_active=1 and c.is_deleted=0 $bundle_nos_cond group by d.id, e.id, f.job_no_prefix_num, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id,c.cut_no, c.bundle_no, c.production_qnty, e.po_number order by  length(c.bundle_no) asc, c.bundle_no asc";
	
	$result = sql_select($sql);
	$count=count($result);
	$i=$ex_data[1]+$count;
	foreach ($result as $row)
	{ 
		if($scanned_bundle_arr[$row[csf('bundle_no')]]=="" || $mst_id[0]!="")
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i;?>"> 
            	<td width="30"><? echo $i; ?></td>
                <td width="80" id="bundle_<? echo $i; ?>"><? echo $row[csf('bundle_no')]; ?></td>
                <td width="45" align="center"><? echo $row[csf('year')]; ?></td>
                <td width="50" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                <td width="65"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
                <td width="90" style="word-break:break-all;" align="left"><? echo $row[csf('po_number')]; ?></td>
                <td width="100" style="word-break:break-all;" align="left"><? echo $garments_item[$row[csf('item_number_id')]]; ?></td>
                <td width="80" style="word-break:break-all;" align="left"><? echo $country_arr[$row[csf('country_id')]]; ?></td>
                <td width="80" style="word-break:break-all;" align="left"><? echo $color_arr[$row[csf('color_number_id')]]; ?></td>
                <td width="60" style="word-break:break-all;" align="left">&nbsp;<? echo $size_arr[$row[csf('size_number_id')]]; ?></td>
                <td width="50" id="prodQty_<? echo $i; ?>" align="right"><? echo $row[csf('qty')]; ?>&nbsp;</td>
                <td width="50" id="RejQty_<? echo $i; ?>" align="right"><input type="text" class="text_boxes_numeric" name="rejectQty[]" id="rejectQty_<? echo $i; ?>" style="width:40px" onBlur="calculate_qcpasss(<? echo $i; ?>)"/></td>
                <td width="50" id="QcQty_<? echo $i; ?>" align="right"><? echo $row[csf('qty')]; ?>&nbsp;</td>
                <td id="button_1" align="center">
                    <input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
                    
                    <input type="hidden" name="cutNo[]" id="cutNo_<? echo $i; ?>" value="<? echo $row[csf('cut_no')]; ?>"/>
                    
                    <input type="hidden" name="colorSizeId[]" id="colorSizeId_<? echo $i; ?>" value="<? echo $row[csf('colorsizeid')]; ?>"/>
                    <input type="hidden" name="orderId[]" id="orderId_<? echo $i; ?>" value="<? echo $row[csf('po_id')]; ?>"/>
                    <input type="hidden" name="gmtsitemId[]" id="gmtsitemId_<? echo $i; ?>" value="<? echo $row[csf('item_number_id')]; ?>"/>
                    <input type="hidden" name="countryId[]" id="countryId_<? echo $i; ?>" value="<? echo $row[csf('country_id')]; ?>"/>
                    <input type="hidden" name="colorId[]" id="colorId_<? echo $i; ?>" value="<? echo $row[csf('color_number_id')]; ?>"/>
                    <input type="hidden" name="sizeId[]" id="sizeId_<? echo $i; ?>" value="<? echo $row[csf('size_number_id')]; ?>"/>
                    <input type="hidden" name="qty[]" id="qty_<? echo $i; ?>" value="<? echo $row[csf('qty')]; ?>"/>
                    <input type="hidden" name="prod_qty[]" id="prod_qty_<? echo $i; ?>" value="<? echo $row[csf('qty')]; ?>"/>
                    <input type="hidden" name="dtlsId[]" id="dtlsId_<? echo $i; ?>"/>
                </td>
			</tr>
		<?
        	$i--;
		}
	}
	exit();	
}

// new need
if($action=="populate_bundle_data_update")
{
 	$ex_data = explode("**",$data);
	$bundle=explode(",",$ex_data[0]);
	$mst_id=explode(",",$ex_data[2]);
	$company_id=$ex_data[3];
	$bundle_nos="'".implode("','",$bundle)."'";
	
	$bundle_count=count(explode(",",$bundle_nos)); $bundle_nos_cond="";
	if($db_type==2 && $bundle_count>400)
	{
		$bundle_nos_cond=" and (";
		$bundleArr=array_chunk(explode(",",$bundle_nos),399);
		foreach($bundleArr as $bundleNos)
		{
			$bundleNos=implode(",",$bundleNos);
			$bundle_nos_cond.=" c.bundle_no in($bundleNos) or ";
		}
		$bundle_nos_cond=chop($bundle_nos_cond,'or ');
		$bundle_nos_cond.=")";
	}
	else
	{
		$bundle_nos_cond=" and c.bundle_no in ($bundle_nos)";
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
	 
	$issue_sql="select d.id as colorsizeid, e.id as po_id, f.job_no_prefix_num, $year_field as year, f.buyer_name, d.item_number_id, d.country_id,c.reject_qty,c.alter_qty,c.spot_qty,c.replace_qty, d.size_number_id, d.color_number_id, c.bundle_no, c.production_qnty as qty, e.po_number from pro_garments_production_mst a,pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f where a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_no_mst=f.job_no and c.production_type=3 and a.embel_name=2 and c.status_active=1 and c.is_deleted=0 $bundle_nos_cond";
	$issue_result = sql_select($issue_sql);
	foreach ($issue_result as $row){
		$issue_qty_arr[$row[csf('bundle_no')]]=$row[csf('qty')];	
	}
	
	
	$company_short_arr=return_library_array( "select id, company_short_name from lib_company where id=$company_id",'id','company_short_name');
	$bundleConvertToInt = convertToInt('c.bundle_no',array($company_short_arr[$company_id],'-',"/"),'order_bundle_no');
	$sql="select $bundleConvertToInt,d.id as colorsizeid, e.id as po_id, f.job_no_prefix_num, $year_field as year, f.buyer_name, d.item_number_id, d.country_id,c.reject_qty,c.alter_qty,c.spot_qty,c.replace_qty, d.size_number_id, d.color_number_id,c.cut_no, c.bundle_no, c.production_qnty as qty, e.po_number from pro_garments_production_mst a,pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f where a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_no_mst=f.job_no and c.production_type=3 and a.embel_name=3 and c.status_active=1 and c.is_deleted=0 $bundle_nos_cond order by order_bundle_no";
	
	
	
	// echo $sql;//die;
	$result = sql_select($sql);
	$count=count($result);
	$i=$ex_data[1]+$count;
	foreach ($result as $row)
	{ 
		
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i;?>"> 
            	<td width="30"><? echo $i; ?></td>
                <td width="80" id="bundle_<? echo $i; ?>"><? echo $row[csf('bundle_no')]; ?></td>
                <td width="45" align="center"><? echo $row[csf('year')]; ?></td>
                <td width="50" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                <td width="65"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
                <td width="90" style="word-break:break-all;" align="left"><? echo $row[csf('po_number')]; ?></td>
                <td width="100" style="word-break:break-all;" align="left"><? echo $garments_item[$row[csf('item_number_id')]]; ?></td>
                <td width="80" style="word-break:break-all;" align="left"><? echo $country_arr[$row[csf('country_id')]]; ?></td>
                <td width="80" style="word-break:break-all;" align="left"><? echo $color_arr[$row[csf('color_number_id')]]; ?></td>
                <td width="60" style="word-break:break-all;" align="left">&nbsp;<? echo $size_arr[$row[csf('size_number_id')]]; ?></td>
                <td width="50" id="prodQty_<? echo $i; ?>" align="right"><? echo $issue_qty_arr[$row[csf('bundle_no')]]//$row[csf('qty')]; ?>&nbsp;</td>
                <td width="50" id="RejQty_<? echo $i; ?>" align="right"><input type="text" class="text_boxes_numeric" name="rejectQty[]" id="rejectQty_<? echo $i; ?>" style="width:40px" value="<? echo $row[csf('reject_qty')];?>" onBlur="calculate_qcpasss(<? echo $i; ?>)"/></td>
                <td width="50" id="QcQty_<? echo $i; ?>" align="right"><? echo $row[csf('qty')]; ?>&nbsp;</td>
                <td id="button_1" align="center">
                    <input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
                   
                    <input type="hidden" name="cutNo[]" id="cutNo_<? echo $i; ?>" value="<? echo $row[csf('cut_no')]; ?>"/>
                    <input type="hidden" name="colorSizeId[]" id="colorSizeId_<? echo $i; ?>" value="<? echo $row[csf('colorsizeid')]; ?>"/>
                    <input type="hidden" name="orderId[]" id="orderId_<? echo $i; ?>" value="<? echo $row[csf('po_id')]; ?>"/>
                    <input type="hidden" name="gmtsitemId[]" id="gmtsitemId_<? echo $i; ?>" value="<? echo $row[csf('item_number_id')]; ?>"/>
                    <input type="hidden" name="countryId[]" id="countryId_<? echo $i; ?>" value="<? echo $row[csf('country_id')]; ?>"/>
                    <input type="hidden" name="colorId[]" id="colorId_<? echo $i; ?>" value="<? echo $row[csf('color_number_id')]; ?>"/>
                    <input type="hidden" name="sizeId[]" id="sizeId_<? echo $i; ?>" value="<? echo $row[csf('size_number_id')]; ?>"/>
                    <input type="hidden" name="qty[]" id="qty_<? echo $i; ?>" value="<? echo $row[csf('qty')]; ?>"/>
                    <input type="hidden" name="prod_qty[]" id="prod_qty_<? echo $i; ?>" value="<? echo $issue_qty_arr[$row[csf('bundle_no')]]//$row[csf('qty')]; ?>"/>
                    <input type="hidden" name="dtlsId[]" id="dtlsId_<? echo $i; ?>"/>
                </td>
			</tr>
		<?
        	$i--;
		
	}
	exit();	
}

// new need ********************************************************************************************************************************
if($action=="bundle_nos")
{
	
/*	if($db_type==0) 
	{
		$bundle_nos=return_field_value("group_concat(b.bundle_no order by b.id desc) as bundle_no", "pro_garments_production_mst a, pro_garments_production_dtls b","a.id=b.mst_id and a.delivery_mst_id=$data and b.production_type=2 and a.embel_name=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0",'bundle_no');
	}
	else if($db_type==2) 
	{
		$bundle_nos=return_field_value("LISTAGG(b.bundle_no, ',') WITHIN GROUP (ORDER BY b.id desc) as bundle_no", "pro_garments_production_mst a, pro_garments_production_dtls b","a.id=b.mst_id and a.delivery_mst_id=$data and b.production_type=2 and a.embel_name=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0",'bundle_no');
	}*/
$bundle_nos=return_library_array( "select b.bundle_no, b.bundle_no from pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id and a.delivery_mst_id=$data and b.production_type=2 and a.embel_name=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","bundle_no","bundle_no");
$bundle_nos=implode(",",$bundle_nos);
	
	
	echo $bundle_nos;
	exit();
}


if($action=="bundle_nos_update")
{
	
	if($db_type==0) 
	{
		$bundle_nos=return_field_value("group_concat(b.bundle_no order by b.id desc) as bundle_no", "pro_garments_production_mst a, pro_garments_production_dtls b","a.id=b.mst_id and a.delivery_mst_id=$data and b.production_type=3 and a.embel_name=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0",'bundle_no');
	}
	else if($db_type==2) 
	{
		$bundle_nos=return_field_value("LISTAGG(b.bundle_no, ',') WITHIN GROUP (ORDER BY b.id desc) as bundle_no", "pro_garments_production_mst a, pro_garments_production_dtls b","a.id=b.mst_id and a.delivery_mst_id=$data and b.production_type=3 and a.embel_name=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0",'bundle_no');
	}
	
	
	echo $bundle_nos;
	exit();
}

if($action=="color_and_size_level")
{
	$dataArr = explode("**",$data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	$variableSettings = $dataArr[2];
	$styleOrOrderWisw = $dataArr[3];
	$embelName = $dataArr[4];
	$country_id = $dataArr[5];
	
	$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');
	
	//#############################################################################################//
	//order wise - color level, color and size level
	
	//$variableSettings=2;
	
	if( $variableSettings==2 ) // color level
	{
		if($db_type==0)
		{
			$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=1 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur, pro_garments_production_mst mst where mst.id=cur.mst_id and mst.embel_name='$embelName' and cur.production_type=2 and cur.is_deleted=0 ) as cur_production_qnty 
			from wo_po_color_size_breakdown
			where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active in(1,2,3) group by color_number_id";
		}
		else
		{
			$sql = "select a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
					sum(CASE WHEN c.production_type=1 then b.production_qnty ELSE 0 END) as production_qnty,
					sum(CASE WHEN c.production_type=2 and c.embel_name='$embelName' then b.production_qnty ELSE 0 END) as cur_production_qnty
					from wo_po_color_size_breakdown a 
					left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id
					left join pro_garments_production_mst c on c.id=b.mst_id
					where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active in(1,2,3) group by a.item_number_id, a.color_number_id";	
			
		}
		
		$colorResult = sql_select($sql);
	}
	else if( $variableSettings==3 ) //color and size level
	{
		
		/*$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=1 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur, pro_garments_production_mst mst where mst.id=cur.mst_id and mst.embel_name='$embelName' and cur.production_type=2 and cur.is_deleted=0 ) as cur_production_qnty 
			from wo_po_color_size_breakdown
			where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1";*/
			
			
			
		$dtlsData = sql_select("select a.color_size_break_down_id,
										sum(CASE WHEN a.production_type=1 then a.production_qnty ELSE 0 END) as production_qnty,
										sum(CASE WHEN a.production_type=2 and b.embel_name='$embelName' then a.production_qnty ELSE 0 END) as cur_production_qnty 
										from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(1,2) group by a.color_size_break_down_id");	
										
		foreach($dtlsData as $row)
		{				  
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
		}  
		//print_r($color_size_qnty_array);
			
		$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty 
			from wo_po_color_size_breakdown
			where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active in(1,2,3) order by color_number_id, id";
			
		$colorResult = sql_select($sql);
	}
/*	else // by default color and size level
	{
		$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=1 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur, pro_garments_production_mst mst where  mst.id=cur.mst_id and mst.embel_name='$embelName' and cur.production_type=2 and cur.is_deleted=0 ) as cur_production_qnty 
			from wo_po_color_size_breakdown
			where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1";// order by color_number_id,size_number_id
	}
*/	
	//$colorResult = sql_select($sql);		
	//print_r($sql);
	$colorHTML="";
	$colorID='';
	$chkColor = array(); 
	$i=0;$totalQnty=0;
	foreach($colorResult as $color)
	{
		if( $variableSettings==2 ) // color level
		{ 
			$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:80px"  class="text_boxes_numeric" placeholder="'.($color[csf("production_qnty")]-$color[csf("cur_production_qnty")]).'" onblur="fn_colorlevel_total('.($i+1).')"></td></tr>';				
			$totalQnty += $color[csf("production_qnty")]-$color[csf("cur_production_qnty")];
			$colorID .= $color[csf("color_number_id")].",";
		}
		else //color and size level
		{
			if( !in_array( $color[csf("color_number_id")], $chkColor ) )
			{
				if( $i!=0 ) $colorHTML .= "</table></div>";
				$i=0;
				$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_number_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_number_id")].'\', \'\',1)"> <span id="accordion_h'.$color[csf("color_number_id")].'span">+</span>'.$color_library[$color[csf("color_number_id")]].' : <span id="total_'.$color[csf("color_number_id")].'"></span> </h3>';
				$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_number_id")].'">';
				$chkColor[] = $color[csf("color_number_id")];					
			}
			//$index = $color[csf("size_number_id")].$color[csf("color_number_id")];
			$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";
			
			$iss_qnty=$color_size_qnty_array[$color[csf('id')]]['iss'];
			$rcv_qnty=$color_size_qnty_array[$color[csf('id')]]['rcv'];
			
			
			
			$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" placeholder="'.($iss_qnty-$rcv_qnty).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')"></td></tr>';				
		}
		$i++; 
	}
	//echo $colorHTML;die; 
	if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="100">Color</th><th width="80">Quantity</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" class="text_boxes_numeric" style="width:80px" ></th></tr></tfoot></table>'; }
	echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
	$colorList = substr($colorID,0,-1);
	echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
	//#############################################################################################//
	exit();
}

if($action=="show_dtls_listview")
{
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	
	
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
			$i=1;	
			$total_production_qnty=0;
			$sqlResult =sql_select("select id,po_break_down_id,item_number_id,country_id,production_date,production_quantity,production_source,serving_company,location,challan_no from pro_garments_production_mst where delivery_mst_id='$data' and status_active=1 and is_deleted=0 order by id");
			foreach($sqlResult as $selectResult)
			{
				if ($i%2==0)  $bgcolor="#E9F3FF";
                else $bgcolor="#FFFFFF";
               	$total_production_qnty+=$selectResult[csf('production_quantity')]; 	
 			?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data(<? echo $selectResult[csf('id')]; ?>,'populate_receive_form_data','requires/wash_challan_receive_controller');" > 
                    <td width="50" align="center"><? echo $i; ?></td>
                    <td width="150" align="center"><p><? echo $garments_item[$selectResult[csf('item_number_id')]]; ?></p></td>
                    <td width="120" align="center"><p><? echo $country_library[$selectResult[csf('country_id')]]; ?></p></td>
                    <td width="80" align="center"><?php echo change_date_format($selectResult[csf('production_date')]); ?></td>
                    <td width="80" align="center"><?php  echo $selectResult[csf('production_quantity')]; ?></td>
                    	<?php
                    		$source= $selectResult[csf('production_source')];
                            if($source==3) $serving_company= $supplier_arr[$selectResult[csf('serving_company')]];
                            else $serving_company= $company_arr[$selectResult[csf('serving_company')]];
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

if($action=="show_country_listview")
{
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
		$i=1;
		
		$sqlResult =sql_select("select id,po_break_down_id,item_number_id,country_id,production_date,production_quantity,production_source,serving_company,location,challan_no from pro_garments_production_mst where delivery_mst_id='$data' and status_active=1 and is_deleted=0 order by id");
		foreach($sqlResult as $row)
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data(<? echo $row[csf('id')]; ?>,'populate_issue_form_data','requires/wash_challan_receive_controller');"> 
				<td width="30"><? echo $i; ?></td>
				<td width="110"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
				<td width="80"><p><? echo $country_library[$row[csf('country_id')]]; ?>&nbsp;</p></td>
				<td width="75" align="center"><? if($row[csf('production_date')]!="0000-00-00") echo change_date_format($row[csf('production_date')]); ?>&nbsp;</td>
				<td align="right"><?php  echo $row[csf('production_quantity')]; ?></td>
			</tr>
		<?	
			$i++;
		}
		?>
	</table>
	<?
	exit();
}

if($action=="populate_issue_form_data")
{
	//production type=2 come from array
	$poNumber_arr=return_library_array( "select id, po_number from   wo_po_break_down",'id','po_number');
	$sqlResult =sql_select("select id,garments_nature,challan_no,po_break_down_id,item_number_id,country_id,production_source,serving_company,location,embel_name,embel_type,production_date,production_quantity,production_source,production_type,entry_break_down_type,production_hour,sewing_line,supervisor,carton_qty,remarks,floor_id,alter_qnty,reject_qnty,total_produced,yet_to_produced from pro_garments_production_mst where id='$data' and production_type='2' and status_active=1 and is_deleted=0 order by id");
  	//echo "sdfds".$sqlResult;die;
	foreach($sqlResult as $result)
	{ 
  		echo "$('#txt_receive_qty').val('".$result[csf('production_quantity')]."');\n";
  		echo "$('#txt_remark').val('".$result[csf('remarks')]."');\n";
		
		$dataArray=sql_select("select SUM(CASE WHEN production_type=2 and embel_name=3 THEN production_quantity END) as totalcutting,SUM(CASE WHEN production_type=3 and embel_name=3 THEN production_quantity ELSE 0 END) as totalprinting from pro_garments_production_mst WHERE po_break_down_id=".$result[csf('po_break_down_id')]." and item_number_id=".$result[csf('item_number_id')]."  and country_id=".$result[csf('country_id')]." and is_deleted=0");
 		foreach($dataArray as $row)
		{ 
 			echo "$('#txt_issue_qty').val('".$row[csf('totalcutting')]."');\n";
			echo "$('#txt_cumul_receive_qty').attr('placeholder','".$row[csf('totalprinting')]."');\n";
			echo "$('#txt_cumul_receive_qty').val('".$row[csf('totalprinting')]."');\n";
			$yet_to_produced = $row[csf('totalcutting')]-$row[csf('totalprinting')];
			echo "$('#txt_yet_to_receive').attr('placeholder','".$yet_to_produced."');\n";
			echo "$('#txt_yet_to_receive').val('".$yet_to_produced."');\n";
		}
		
		
		echo "$('#cbo_item_name').val('".$result[csf('item_number_id')]."');\n";
		echo "$('#cbo_country_name').val('".$result[csf('country_id')]."');\n";
		echo "$('#hidden_po_break_down_id').val('".$result[csf('po_break_down_id')]."');\n";
		echo "$('#txt_order_no').val('".$poNumber_arr[$result[csf('po_break_down_id')]]."');\n";
		
		//break down of color and size------------------------------------------
 		//#############################################################################################//
		// order wise - color level, color and size level
		$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
		$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');

		$variableSettings = $result[csf('entry_break_down_type')];
		
	}
	$sql_order=sql_select("SELECT a.buyer_name,a.style_ref_no,b.po_quantity from wo_po_break_down b,wo_po_details_master a where a.job_no=b.job_no_mst and b.id=".$result[csf('po_break_down_id')]." and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0");
	foreach($sql_order as $inf)
	{
		echo "$('#cbo_buyer_name').val(".$inf[csf('buyer_name')].");\n";
		echo "$('#txt_order_qty').val(".$inf[csf('po_quantity')].");\n";
		echo "$('#txt_style_no').val('".$inf[csf('style_ref_no')]."');\n";	
	}
		
		if( $variableSettings==2 ) // color level
		{
			$po_id = $result[csf('po_break_down_id')];
			$item_id = $result[csf('item_number_id')];
			$country_id = $result[csf('country_id')];
			if($db_type==0)
			{
			
				$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls, pro_garments_production_mst mst where mst.id=pdtls.mst_id and mst.embel_name='$embelName' and pdtls.production_type=2 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur, pro_garments_production_mst mst where mst.id=cur.mst_id and mst.embel_name='$embelName' and cur.production_type=3 and cur.is_deleted=0 ) as cur_production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.reject_qty ELSE 0 END) from pro_garments_production_dtls cur, pro_garments_production_mst mst where mst.id=cur.mst_id and mst.embel_name='$embelName' and cur.production_type=3 and cur.is_deleted=0 ) as reject_qty 
				from wo_po_color_size_breakdown
				where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active in(1,2,3) group by color_number_id";
			}
			else
			{
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
				
		}
		else if( $variableSettings==3 ) //color and size level
		{
			$po_id = $result[csf('po_break_down_id')];
			$item_id = $result[csf('item_number_id')];
			$country_id = $result[csf('country_id')];
			
			$dtlsData = sql_select("select a.color_size_break_down_id,
										sum(CASE WHEN a.production_type=2 then a.production_qnty ELSE 0 END) as production_qnty,
										sum(CASE WHEN a.production_type=3 then a.production_qnty ELSE 0 END) as cur_production_qnty,
										sum(CASE WHEN a.production_type=3 then a.reject_qty ELSE 0 END) as reject_qty 
										from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.embel_name=3 and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(2,3) group by a.color_size_break_down_id");	
										
			foreach($dtlsData as $row)
			{				  
				$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
				$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
				$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rej']= $row[csf('reject_qty')];
			}  
			//print_r($color_size_qnty_array);
				
			$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty 
				from wo_po_color_size_breakdown
				where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active in(1,2,3) order by color_number_id, id";
			//echo $sql;	
				
			$colorResult = sql_select($sql);	
		}
		else // by default color and size level
		{
		
		}
		
	if($variableSettingsRej!=1)
		{
			$disable="";
		}
		else
		{
			$disable="disabled";
		}
  		$colorHTML="";
		$colorID='';
		$chkColor = array(); 
		$i=0;$totalQnty=0;
 		foreach($colorResult as $color)
		{
			if( $variableSettings==2 ) // color level
			{ 
				$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="'.($color[csf("production_qnty")]-$color[csf("cur_production_qnty")]).'" onblur="fn_colorlevel_total('.($i+1).')"></td><td><input type="text" name="txtColSizeRej" id="colSizeRej_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="Rej." onblur="fn_colorRej_total('.($i+1).')  '.$disable.'"></td></tr>';				
				$totalQnty += $color[csf("production_qnty")]-$color[csf("cur_production_qnty")];
				$colorID .= $color[csf("color_number_id")].",";
			}
			else //color and size level
			{
				if( !in_array( $color[csf("color_number_id")], $chkColor ) )
				{
					if( $i!=0 ) $colorHTML .= "</table></div>";
					$i=0;
					$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_number_id")].'" style="width:250px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_number_id")].'\', \'\',1)"> <span id="accordion_h'.$color[csf("color_number_id")].'span">+</span>'.$color_library[$color[csf("color_number_id")]].' : <span id="total_'.$color[csf("color_number_id")].'"></span> </h3>';
					$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_number_id")].'">';
					$chkColor[] = $color[csf("color_number_id")];					
				}
 				//$index = $color[csf("size_number_id")].$color[csf("color_number_id")];
				$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";
				
				$iss_qnty=$color_size_qnty_array[$color[csf('id')]]['iss'];
				$rcv_qnty=$color_size_qnty_array[$color[csf('id')]]['rcv'];
				
				
 				$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:80px" placeholder="'.($iss_qnty-$rcv_qnty).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')"><input type="text" name="colorSizeRej" id="colSizeRej_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" placeholder="Rej. Qty" onblur="fn_total_rej('.$color[csf("color_number_id")].','.($i+1).')" '.$disable.'></td></tr>';				
			}
			
			$i++; 
		}
		//echo $colorHTML;die; 
		if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="70">Color</th><th width="60">Quantity</th><th width="60">Rej.</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" class="text_boxes_numeric" style="width:60px" ></th><th><input type="text" id="total_color_rej" placeholder="Rej." class="text_boxes_numeric" style="width:60px" '.$disable.' ></th></tr></tfoot></table>'; }
		echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
		$colorList = substr($colorID,0,-1);
		echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
		//#############################################################################################//
		exit();		
		
}


if($action=="populate_receive_form_data")
{
	//production type=2 come from array
	$poNumber_arr=return_library_array( "select id, po_number from   wo_po_break_down",'id','po_number');
	$sqlResult =sql_select("select id, garments_nature, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, embel_name, embel_type, production_date, production_quantity, production_source, production_type, entry_break_down_type, break_down_type_rej, production_hour, sewing_line, supervisor, carton_qty, remarks, floor_id, alter_qnty, reject_qnty, total_produced, yet_to_produced from pro_garments_production_mst where id='$data' and production_type='3' and status_active=1 and is_deleted=0 order by id");
  	//echo "sdfds".$sqlResult;die;
	foreach($sqlResult as $result)
	{ 
 		echo "$('#txt_receive_qty').val('".$result[csf('production_quantity')]."');\n";
		echo "$('#txt_reject_qty').val('".$result[csf('reject_qnty')]."');\n";
		echo "$('#txt_challan').val('".$result[csf('challan_no')]."');\n";
  		echo "$('#txt_remark').val('".$result[csf('remarks')]."');\n";
		
		echo "$('#cbo_item_name').val('".$result[csf('item_number_id')]."');\n";
		echo "$('#cbo_country_name').val('".$result[csf('country_id')]."');\n";
		echo "$('#hidden_po_break_down_id').val('".$result[csf('po_break_down_id')]."');\n";
		echo "$('#txt_order_no').val('".$poNumber_arr[$result[csf('po_break_down_id')]]."');\n";
		
		$dataArray=sql_select("select SUM(CASE WHEN production_type=2 THEN production_quantity END) as totalCutting,SUM(CASE WHEN production_type=3 THEN production_quantity ELSE 0 END) as totalPrinting from pro_garments_production_mst WHERE po_break_down_id=".$result[csf('po_break_down_id')]." and item_number_id=".$result[csf('item_number_id')]." and embel_name=".$result[csf('embel_name')]." and country_id=".$result[csf('country_id')]." and is_deleted=0");
 		foreach($dataArray as $row)
		{ 
			echo "$('#txt_issue_qty').attr('placeholder','".$row[csf('totalCutting')]."');\n";
 			echo "$('#txt_issue_qty').val('".$row[csf('totalCutting')]."');\n";
			echo "$('#txt_cumul_receive_qty').val('".$row[csf('totalPrinting')]."');\n";
			$yet_to_produced = $row[csf('totalCutting')]-$row[csf('totalPrinting')];
			echo "$('#txt_yet_to_receive').val('".$yet_to_produced."');\n";
		}

		$sql_order=sql_select("select a.buyer_name,a.style_ref_no,b.po_quantity from wo_po_break_down b,wo_po_details_master a where a.job_no=b.job_no_mst and b.id=".$result[csf('po_break_down_id')]." and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0");
		foreach($sql_order as $inf)
		{
			echo "$('#cbo_buyer_name').val(".$inf[csf('buyer_name')].");\n";
			echo "$('#txt_order_qty').val(".$inf[csf('po_quantity')].");\n";
			echo "$('#txt_style_no').val('".$inf[csf('style_ref_no')]."');\n";	
		}
	
		echo "$('#txt_mst_id').val('".$result[csf('id')]."');\n";	
		//echo "$('#txt_mst_id_all').val('".$result[csf('id')]."');\n";
 		echo "set_button_status(1, permission, 'fnc_issue_print_embroidery_entry',1,1);\n";
		 
		//break down of color and size------------------------------------------
 		//#############################################################################################//
		// order wise - color level, color and size level
		$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
		$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');

		$variableSettings = $result[csf('entry_break_down_type')];
		$variableSettingsRej = $result[csf('break_down_type_rej')];
		if( $variableSettings!=1 ) // gross level
		{ 
			$po_id = $result[csf('po_break_down_id')];
			$item_id = $result[csf('item_number_id')];
			$country_id = $result[csf('country_id')];
			
			$sql_dtls = sql_select("select color_size_break_down_id, production_qnty, reject_qty, size_number_id, color_number_id from  pro_garments_production_dtls a,wo_po_color_size_breakdown b where a.mst_id=$data and a.status_active=1 and a.color_size_break_down_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id'");	
			foreach($sql_dtls as $row)
			{				  
				if( $variableSettings==2 ) $index = $row[csf('color_number_id')]; else $index = $row[csf('size_number_id')].$row[csf('color_number_id')];
			  	$amountArr[$index] = $row[csf('production_qnty')];
				$rejectArr[$index] = $row[csf('reject_qty')];
			}  
			 
			if( $variableSettings==2 ) // color level
			{
				if($db_type==0)
				{
				
					$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=2 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=3 and cur.is_deleted=0 ) as cur_production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.reject_qty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=3 and cur.is_deleted=0 ) as reject_qty 
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active in(1,2,3) group by color_number_id";
				}
				else
				{
					$sql = "select a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
							sum(CASE WHEN c.production_type=2 then b.production_qnty ELSE 0 END) as production_qnty,
							sum(CASE WHEN c.production_type=3 then b.production_qnty ELSE 0 END) as cur_production_qnty,
							sum(CASE WHEN c.production_type=3 then b.reject_qty ELSE 0 END) as reject_qty
							from wo_po_color_size_breakdown a 
							left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id
							left join pro_garments_production_mst c on c.id=b.mst_id
							where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active in(1,2,3) group by a.item_number_id, a.color_number_id";	
					
				}
				
			}
			else if( $variableSettings==3 ) //color and size level
			{
				/*$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=2 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=3 and cur.is_deleted=0 ) as cur_production_qnty 
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1";*/
					
					
				$dtlsData = sql_select("select a.color_size_break_down_id,
										sum(CASE WHEN a.production_type=2 then a.production_qnty ELSE 0 END) as production_qnty,
										sum(CASE WHEN a.production_type=3 then a.production_qnty ELSE 0 END) as cur_production_qnty,
										sum(CASE WHEN a.production_type=3 then a.reject_qty ELSE 0 END) as reject_qty 
										from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and a.mst_id=$data and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(2,3) group by a.color_size_break_down_id");
										
				foreach($dtlsData as $row)
				{				  
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rej']= $row[csf('reject_qty')];
				} 
				
				$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty 
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active in(1,2,3) order by color_number_id";	
					
					
			}
			else // by default color and size level
			{
			
					
					
				$dtlsData = sql_select("select a.color_size_break_down_id,
										sum(CASE WHEN a.production_type=2 then a.production_qnty ELSE 0 END) as production_qnty,
										sum(CASE WHEN a.production_type=3 then a.production_qnty ELSE 0 END) as cur_production_qnty,
										sum(CASE WHEN a.production_type=3 then a.reject_qty ELSE 0 END) as reject_qty 
										from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(2,3) group by a.color_size_break_down_id");
									
				foreach($dtlsData as $row)
				{				  
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rej']= $row[csf('reject_qty')];
				}  
				//print_r($color_size_qnty_array);
				
				$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty 
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active in(1,2,3) order by color_number_id";
					
					
			}
			if($variableSettingsRej!=1)
			{
				$disable="";
			}
			else
			{
				$disable="disabled";
			}
 
 			$colorResult = sql_select($sql);
 			//print_r($sql);die;
			$colorHTML="";
			$colorID='';
			$chkColor = array(); 
			$i=0;$totalQnty=0;$colorWiseTotal=0;
			foreach($colorResult as $color)
			{
				if( $variableSettings==2 ) // color level
				{  
					$amount = $amountArr[$color[csf("color_number_id")]];
					$rejectAmt = $rejectArr[$color[csf("color_number_id")]];
					$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="'.($color[csf("production_qnty")]-$color[csf("cur_production_qnty")]+$amount).'" value="'.$amount.'" onblur="fn_colorlevel_total('.($i+1).')"></td><td><input type="text" name="txtColSizeRej" id="colSizeRej_'.($i+1).'" style="width:60px" class="text_boxes_numeric" placeholder="Rej." value="'.$rejectAmt.'" onblur="fn_colorRej_total('.($i+1).')"></td></tr>';				
					$totalQnty += $amount;
					$totalRejQnty += $rejectAmt;
					$colorID .= $color[csf("color_number_id")].",";
				}
				else //color and size level
				{
					$index = $color[csf("size_number_id")].$color[csf("color_number_id")];
					$amount = $amountArr[$index];
					if( !in_array( $color[csf("color_number_id")], $chkColor ) )
					{
						if( $i!=0 ) $colorHTML .= "</table></div>";
						$i=0;$colorWiseTotal=0;
						$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_number_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_number_id")].'\', \'\',1)"> <span id="accordion_h'.$color[csf("color_number_id")].'span">+</span>'.$color_library[$color[csf("color_number_id")]].' : <span id="total_'.$color[csf("color_number_id")].'"></span> </h3>';
						$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_number_id")].'">';
						$chkColor[] = $color[csf("color_number_id")];
						$totalFn .= "fn_total(".$color[csf("color_number_id")].");";
					}
 					$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";
					
					$iss_qnty=$color_size_qnty_array[$color[csf('id')]]['iss'];
					$rcv_qnty=$color_size_qnty_array[$color[csf('id')]]['rcv'];
					$rej_qnty=$color_size_qnty_array[$color[csf('id')]]['rej'];
					
					$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" placeholder="'.($iss_qnty-$rcv_qnty+$amount).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')" value="'.$amount.'" ><input type="text" name="colorSizeRej" id="colSizeRej_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" placeholder="Rej. Qty" onblur="fn_total_rej('.$color[csf("color_number_id")].','.($i+1).')" value="'.$rej_qnty.'" '.$disable.'></td></tr>';				
					$colorWiseTotal += $amount;
				}
				$i++; 
			}
			//echo $colorHTML;die; 
			if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="70">Color</th><th width="60">Quantity</th><th width="60">Reject</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" value="'.$totalQnty.'" class="text_boxes_numeric" style="width:60px" ></th><th><input type="text" id="total_color_rej" placeholder="'.$totalRejQnty.'" value="'.$totalRejQnty.'" class="text_boxes_numeric" style="width:60px" ></th></tr></tfoot></table>'; }
			echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
			if( $variableSettings==3 )echo "$totalFn;\n";
			$colorList = substr($colorID,0,-1);
			echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
		}//end if condtion
		//#############################################################################################//
	}
 	exit();		
}

//pro_garments_production_mst
if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//table lock here 
		//if ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**2"; die;}
		//if ( check_table_status( 160, 1 )==0 ) { echo "15**0"; die;} 
		if(str_replace("'","",$txt_system_id)=="")
		{
			//$mst_id=return_next_id("id", "pro_gmts_delivery_mst", 1);
			  $mst_id = return_next_id_by_sequence(  "pro_gmts_delivery_mst_seq",   "pro_gmts_delivery_mst", $con );
			
			if($db_type==0) $year_cond="YEAR(insert_date)"; 
			else if($db_type==2) $year_cond="extract(year from insert_date)";
			else $year_cond="";//defined Later
			$new_sys_number = explode("*", return_next_id_by_sequence("", "pro_gmts_delivery_mst",$con,1,$cbo_company_name,'BRW',0,date("Y",time()),0,0,3,$cbo_embel_name,0 ));


			$field_array_delivery="id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, location_id, delivery_basis, embel_name, embel_type, production_source, serving_company, floor_id, organic, delivery_date,issue_challan_id,working_company_id,working_location_id, inserted_by, insert_date";
			$data_array_delivery="(".$mst_id.",'".$new_sys_number[1]."','".(int)$new_sys_number[2]."','".$new_sys_number[0]."', ".$cbo_company_name.",3,".$txt_location_id.",".$delivery_basis.",".$cbo_embel_name.",".$cbo_embel_type.",".$cbo_source.",".$txt_embl_company_id.",".$txt_location_id.",".$txt_organic.",".$txt_issue_date.",".$txt_issue_challan_id.",".$cbo_working_company_name.",".$cbo_working_location.",".$user_id.",'".$pc_date_time."')";
			$challan_no=(int)$new_sys_number[2];
			$txt_challan_no=$new_sys_number[0];
		}
		else
		{
			$mst_id=str_replace("'","",$txt_system_id);
			$txt_chal_no=explode("-",str_replace("'","",$txt_challan_no));
			$challan_no=(int) $txt_chal_no[3];
			$field_array_delivery="company_id*location_id*delivery_basis*embel_name*embel_type*production_source*serving_company*floor_id*organic*delivery_date*working_company_id*working_location_id*updated_by*update_date";
			$data_array_delivery="".$cbo_company_name."*".$txt_location_id."*".$delivery_basis."*".$cbo_embel_name."*".$cbo_embel_type."*".$cbo_source."*".$txt_embl_company_id."*".$txt_floor_id."*".$txt_organic."*".$txt_issue_date."*".$cbo_working_company_name."*".$cbo_working_location."*".$user_id."*'".$pc_date_time."'";
			
		}
 		
		if(str_replace("'","",$delivery_basis)==3)
		{
			//$id=return_next_id("id", "pro_garments_production_mst", 1);
			$field_array_mst="id, delivery_mst_id,cut_no, company_id, garments_nature, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, embel_name, embel_type, production_date, production_quantity,reject_qnty, production_type, entry_break_down_type, floor_id, inserted_by, insert_date";

			$mstArr=array(); $dtlsArr=array(); $colorSizeArr=array(); $mstIdArr=array(); $colorSizeIdArr=array(); $mstArrRej=array(); $dtlsArrRej=array();
			for($j=1;$j<=$tot_row;$j++)
			{ 	
				$cutNo="cutNo_".$j;
				$bundleNo="bundleNo_".$j;
				$orderId="orderId_".$j;
				$gmtsitemId="gmtsitemId_".$j;
				$countryId="countryId_".$j;
				$colorId="colorId_".$j;
				$sizeId="sizeId_".$j;
				$colorSizeId="colorSizeId_".$j;
				$qty="qty_".$j;
				$rejectQty="rejectQty_".$j;
				
				$bundleCutArr[$$bundleNo]=$$cutNo;
				$cutArr[$$orderId][$$gmtsitemId][$$countryId]=$$cutNo;
				$mstArr[$$orderId][$$gmtsitemId][$$countryId]+=$$qty;
				$mstArrRej[$$orderId][$$gmtsitemId][$$countryId]+=$$rejectQty;
				$colorSizeArr[$$bundleNo]=$$orderId."**".$$gmtsitemId."**".$$countryId;
				$dtlsArr[$$bundleNo]+=$$qty;
				$dtlsArrRej[$$bundleNo]+=$$rejectQty;
				$dtlsArrColorSize[$$bundleNo]=$$colorSizeId;
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
			
			foreach($mstArr as $orderId=>$orderData)
			{
				foreach($orderData as $gmtsItemId=>$gmtsItemIdData)
				{
					foreach($gmtsItemIdData as $countryId=>$qty)
					{
						 $id= return_next_id_by_sequence(  "pro_gar_production_mst_seq", "pro_garments_production_mst", $con );
						if($data_array_mst!="") $data_array_mst.=",";
						$data_array_mst.="(".$id.",".$mst_id.",'".$cutArr[$orderId][$gmtsItemId][$countryId]."',".$cbo_company_name.",".$garments_nature.",'".$challan_no."',".$orderId.", ".$gmtsItemId.",".$countryId.", ".$cbo_source.",".$txt_embl_company_id.",".$txt_location_id.",".$cbo_embel_name.",".$cbo_embel_type.",".$txt_issue_date.",".$qty.",'".$mstArrRej[$orderId][$gmtsItemId][$countryId]."',3,".$sewing_production_variable.",".$txt_floor_id.",".$user_id.",'".$pc_date_time."')";
						$mstIdArr[$orderId][$gmtsItemId][$countryId]=$id;
						//$id = $id+1;
					}
				}
			}
			
			//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
			$field_array_dtls="id,delivery_mst_id,mst_id,production_type,color_size_break_down_id,production_qnty,reject_qty,cut_no,bundle_no,color_type_id";
			
			foreach($dtlsArr as $bundle_no=>$qty)
			{
				$colorSizedData=explode("**",$colorSizeArr[$bundle_no]);
				$gmtsMstId=$mstIdArr[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[2]];
				$cut_no=$bundleCutArr[$bundle_no];
				$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq","pro_garments_production_dtls", $con );

				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls.= "(".$dtls_id.",".$mst_id.",".$gmtsMstId.",3,'".$dtlsArrColorSize[$bundle_no]."','".$qty."','".$dtlsArrRej[$bundle_no]."','".$cut_no."','".$bundle_no."','".$bundle_wise_type_array[$bundle_no]."')";
				//$colorSizeIdArr[$colorSizeId]=$dtls_id;
				//$dtls_id = $dtls_id+1;
			}
			
			
			
			if(str_replace("'","",$txt_system_id)=="")
			{
				$challanrID=sql_insert("pro_gmts_delivery_mst",$field_array_delivery,$data_array_delivery,1);
			}
			else
			{
				$challanrID=sql_update("pro_gmts_delivery_mst",$field_array_delivery,$data_array_delivery,"id",$txt_system_id,1);
			}
			
			$rID=sql_insert("pro_garments_production_mst",$field_array_mst,$data_array_mst,1);
			$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array_dtls,$data_array_dtls,1);
			//$bundlerID=sql_insert("pro_cut_delivery_color_dtls",$field_array_bundle,$data_array_bundle,1);
		
			//echo "10**insert into pro_cut_delivery_color_dtls (".$field_array_bundle.") values ".$data_array_bundle;die;
			//echo "10**".$challanrID ."&&". $rID ."&&". $dtlsrID ."&&". $bundlerID;die;
			//release lock table
			
		
			if($db_type==0)
			{  
				if($challanrID && $rID && $dtlsrID)
				{
					mysql_query("COMMIT");  
					echo "0**".$mst_id."**".str_replace("'","",$txt_challan_no);
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**";
				}
			}
			else if($db_type==1 || $db_type==2 )
			{
				if($challanrID && $rID && $dtlsrID)
				{
					oci_commit($con); 
					echo "0**".$mst_id."**".str_replace("'","",$txt_challan_no);
				}
				else
				{
					oci_rollback($con);
					echo "10**";
				}
			}
		}
		else
		{
			//$id=return_next_id("id", "pro_garments_production_mst", 1);
			 $id= return_next_id_by_sequence(  "pro_gar_production_mst_seq",  "pro_garments_production_mst", $con );

			$field_array1="id, delivery_mst_id, company_id, garments_nature, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, embel_name, embel_type, production_date, production_quantity,reject_qnty, production_type, entry_break_down_type, remarks, floor_id, inserted_by, insert_date";
			
			$data_array1="(".$id.",".$mst_id.",".$cbo_company_name.",".$garments_nature.",'".$challan_no."',".$hidden_po_break_down_id.", ".$cbo_item_name.",".$cbo_country_name.", ".$cbo_source.",".$txt_embl_company_id.",".$txt_location_id.",".$cbo_embel_name.",".$cbo_embel_type.",".$txt_issue_date.",".$txt_receive_qty.",".$txt_reject_qty.",3,".$sewing_production_variable.",".$txt_remark.",".$txt_floor_id.",".$user_id.",'".$pc_date_time."')";
			
			//echo "INSERT INTO pro_garments_production_mst (".$field_array1.") VALUES ".$data_array1;die;
			// pro_garments_production_dtls table entry here ----------------------------------///
			$field_array="id,delivery_mst_id,mst_id,production_type,color_size_break_down_id,production_qnty,reject_qty";
			$dtlsrID=true;
			if(str_replace("'","",$sewing_production_variable)==2)//color level wise
			{		
				$color_sizeID_arr=sql_select( "select id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and color_mst_id!=0 order by id" );
				$colSizeID_arr=array(); 
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("color_number_id")];
					$colSizeID_arr[$index]=$val[csf("id")];
				}
				
				//**********************Reject Qty ********************************************************************
				$rowExRej = explode("**",$colorIDvalueRej);
				foreach($rowExRej as $rowR=>$valR)
				{
					$colorSizeRejIDArr = explode("*",$valR);
					//echo $colorSizeRejIDArr[0]; die;
					$rejQtyArr[$colorSizeRejIDArr[0]]=$colorSizeRejIDArr[1];
				}
					
				//**********************Reject Qty Finish **************************************************************
					
				// $colorIDvalue concate as colorID*Value**colorID*Value -------------------------//
				$rowEx = explode("**",$colorIDvalue); 
				//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
				$data_array="";$j=0;
				foreach($rowEx as $rowE=>$val)
				{
					$colorSizeNumberIDArr = explode("*",$val);
					$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );
					if($j==0)$data_array = "(".$dtls_id.",".$mst_id.",".$id.",3,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."')";
					else $data_array .= ",(".$dtls_id.",".$mst_id.",".$id.",3,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."')";
					//$dtls_id=$dtls_id+1;							
					$j++;								
				}
			}
			
			if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
			{		
				$color_sizeID_arr=sql_select( "select id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name order by size_number_id,color_number_id" );
				$colSizeID_arr=array(); 
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("size_number_id")].$val[csf("color_number_id")];
					$colSizeID_arr[$index]=$val[csf('id')];
				}
					
				//	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------// 
				$rowExRej = explode("***",$colorIDvalueRej);
				foreach($rowExRej as $rowR=>$valR)
				{
					$colorAndSizeRej_arr = explode("*",$valR);
					$sizeID = $colorAndSizeRej_arr[0];
					$colorID = $colorAndSizeRej_arr[1];				
					$colorSizeRej = $colorAndSizeRej_arr[2];
					$index = $sizeID.$colorID;
					$rejQtyArr[$index]=$colorSizeRej;
				}
			
				$rowEx = explode("***",$colorIDvalue); 
				//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
				$data_array="";$j=0;
				foreach($rowEx as $rowE=>$valE)
				{
					$colorAndSizeAndValue_arr = explode("*",$valE);
					$sizeID = $colorAndSizeAndValue_arr[0];
					$colorID = $colorAndSizeAndValue_arr[1];				
					$colorSizeValue = $colorAndSizeAndValue_arr[2];
					$index = $sizeID.$colorID;
					
					//2 for Issue to Print / Emb Entry
					$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );

					if($j==0)$data_array = "(".$dtls_id.",".$mst_id.",".$id.",3,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$rejQtyArr[$index]."')";
					else $data_array .= ",(".$dtls_id.",".$mst_id.",".$id.",3,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$rejQtyArr[$index]."')";
					//$dtls_id=$dtls_id+1;
					$j++;
				}
			}	
		
			$rID=sql_insert("pro_garments_production_mst",$field_array1,$data_array1,1);
			if(str_replace("'","",$txt_system_id)=="")
			{
				$challanrID=sql_insert("pro_gmts_delivery_mst",$field_array_delivery,$data_array_delivery,1);
			}
			else
			{
				$challanrID=sql_update("pro_gmts_delivery_mst",$field_array_delivery,$data_array_delivery,"id",$txt_system_id,1);
			}
			
			$dtlsrID=true;
			if(str_replace("'","",$sewing_production_variable)==2 || str_replace("'","",$sewing_production_variable)==3)
			{	
			//echo "10** insert into pro_gmts_delivery_mst($field_array_delivery)values".$data_array_delivery;die; 
				$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array,$data_array,1);
			} 
		
			
		//echo "10**".$rID."**".$challanrID."**".$dtlsrID."**".$challanrID."**".$txt_challan_no;die;
			if($db_type==0)
			{  
				if($rID && $challanrID && $dtlsrID)
				{
					mysql_query("COMMIT");  
					echo "0**".$mst_id."**".str_replace("'","",$txt_challan_no);
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**";
				}
			}
			else if($db_type==1 || $db_type==2 )
			{
				if($rID && $challanrID && $dtlsrID)
				{
					oci_commit($con); 
					echo "0**".$mst_id."**".str_replace("'","",$txt_challan_no);
				}
				else
				{
					oci_rollback($con);
					echo "10**";
				}
			}
		}
		//check_table_status( 160,0);
		disconnect($con);
		die;
		
	}
  	else if ($operation==1) // Update Here End------------------------------------------------------
	{
 		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//table lock here 
		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**2"; die;}
		//if  ( check_table_status( 160, 1 )==0 ) { echo "15**1"; die;}   
		$mst_id=str_replace("'","",$txt_system_id);
		$txt_chal_no=str_replace("'","",$txt_challan_no);
		$challan_no=(int) $txt_chal_no[3];

		$field_array_delivery="company_id*location_id*embel_name*embel_type*production_source*serving_company*floor_id*organic*delivery_date*working_company_id*working_location_id*updated_by*update_date";
		$data_array_delivery="".$cbo_company_name."*".$txt_location_id."*".$cbo_embel_name."*".$cbo_embel_type."*".$cbo_source."*".$txt_embl_company_id."*".$txt_floor_id."*".$txt_organic."*".$txt_issue_date."*".$cbo_working_company_name."*".$cbo_working_location."*".$user_id."*'".$pc_date_time."'";
		
		if(str_replace("'","",$delivery_basis)==3)
		{
			
			
			//$id=return_next_id("id", "pro_garments_production_mst", 1);
			//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);

			$field_array_mst="id, delivery_mst_id,cut_no, company_id, garments_nature, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, embel_name, embel_type, production_date, production_quantity,reject_qnty, production_type, entry_break_down_type, remarks, floor_id, inserted_by, insert_date";

			$mstArr=array(); $dtlsArr=array(); $colorSizeArr=array(); $mstIdArr=array(); $colorSizeIdArr=array();$mstArrRej=array(); $dtlsArrRej=array();
			for($j=1;$j<=$tot_row;$j++)
			{ 	
				$cutNo="cutNo_".$j;
				$bundleNo="bundleNo_".$j;
				$orderId="orderId_".$j;
				$gmtsitemId="gmtsitemId_".$j;
				$countryId="countryId_".$j;
				$colorId="colorId_".$j;
				$sizeId="sizeId_".$j;
				$colorSizeId="colorSizeId_".$j;
				$qty="qty_".$j;
				$rejectQty="rejectQty_".$j;
				$cutArr[$$orderId][$$gmtsitemId][$$countryId]=$$cutNo;
				
				$mstArr[$$orderId][$$gmtsitemId][$$countryId]+=$$qty;
				$mstArrRej[$$orderId][$$gmtsitemId][$$countryId]+=$$rejectQty;
				$colorSizeArr[$$bundleNo]=$$orderId."**".$$gmtsitemId."**".$$countryId;
				$dtlsArr[$$bundleNo]+=$$qty;
				$dtlsArrRej[$$bundleNo]+=$$rejectQty;
				$dtlsArrColorSize[$$bundleNo]=$$colorSizeId;
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
			
			foreach($mstArr as $orderId=>$orderData)
			{
				foreach($orderData as $gmtsItemId=>$gmtsItemIdData)
				{
					foreach($gmtsItemIdData as $countryId=>$qty)
					{
						 $id= return_next_id_by_sequence(  "pro_gar_production_mst_seq", "pro_garments_production_mst", $con );
						if($data_array_mst!="") $data_array_mst.=",";
						$data_array_mst.="(".$id.",".$mst_id.",'".$cutArr[$orderId][$gmtsItemId][$countryId]."',".$cbo_company_name.",".$garments_nature.",'".$challan_no."',".$orderId.", ".$gmtsItemId.",".$countryId.", ".$cbo_source.",".$txt_embl_company_id.",".$txt_location_id.",".$cbo_embel_name.",".$cbo_embel_type.",".$txt_issue_date.",".$qty.",'".$mstArrRej[$orderId][$gmtsItemId][$countryId]."',3,".$sewing_production_variable.",'".$txt_remark."',".$txt_floor_id.",".$user_id.",'".$pc_date_time."')";
						$mstIdArr[$orderId][$gmtsItemId][$countryId]=$id;
					//	$id = $id+1;
					}
				}
			}
			
			$field_array_dtls="id,delivery_mst_id,mst_id,production_type,color_size_break_down_id,production_qnty,reject_qty,cut_no,bundle_no,color_type_id";
			
			foreach($dtlsArr as $bundle_no=>$qty)
			{
				
				$colorSizedData=explode("**",$colorSizeArr[$bundle_no]);
				$gmtsMstId=$mstIdArr[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[2]];
				$cut_no=$bundleCutArr[$bundle_no];
				$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq", "pro_garments_production_dtls", $con );
				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls.= "(".$dtls_id.",".$mst_id.",".$gmtsMstId.",3,'".$dtlsArrColorSize[$bundle_no]."','".$qty."','".$dtlsArrRej[$bundle_no]."','".$cut_no."','".$bundle_no."','".$bundle_wise_type_array[$bundle_no]."')";
				//$colorSizeIdArr[$colorSizeId]=$dtls_id;
				//$dtls_id = $dtls_id+1;
			}
			
			
			$delete = execute_query("DELETE FROM pro_garments_production_mst WHERE delivery_mst_id=$txt_system_id and production_type=3 and embel_name=3");
			$delete_dtls = execute_query("DELETE FROM pro_garments_production_dtls WHERE delivery_mst_id=$txt_system_id and production_type=3");
			$delete_bundle = execute_query("DELETE FROM pro_cut_delivery_color_dtls WHERE delivery_mst_id=$txt_system_id and production_type=3 and embel_name=3");
			$challanrID=sql_update("pro_gmts_delivery_mst",$field_array_delivery,$data_array_delivery,"id",$txt_system_id,1);
			$rID=sql_insert("pro_garments_production_mst",$field_array_mst,$data_array_mst,1);
			$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array_dtls,$data_array_dtls,1);
			//$bundlerID=sql_insert("pro_cut_delivery_color_dtls",$field_array_bundle,$data_array_bundle,1);
		
			//echo "10**";//insert into pro_garments_production_mst (".$field_array_mst.") values ".$data_array_mst;die;
			//echo $challanrID ."&&". $rID ."&&". $dtlsrID ."&&". $bundlerID ."&&". $delete ."&&". $delete_dtls ."&&". $delete_bundle;die;
			//release lock table
		
			if($db_type==0)
			{  
				if($challanrID && $rID && $dtlsrID && $delete && $delete_dtls)
				{
					mysql_query("COMMIT");  
					echo "1**".$mst_id."**".str_replace("'","",$txt_challan_no);
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**";
				}
			}
			else if($db_type==1 || $db_type==2 )
			{
				if($challanrID && $rID && $dtlsrID && $delete && $delete_dtls)
				{
					oci_commit($con); 
					echo "1**".$mst_id."**".str_replace("'","",$txt_challan_no);
				}
				else
				{
					oci_rollback($con);
					echo "10**";
				}
			}
		}
		else
		{
			// pro_garments_production_mst table data entry here 
			$field_array1="production_source*serving_company*location*embel_name*embel_type*production_date*production_quantity*reject_qnty*production_type*entry_break_down_type*remarks*floor_id*total_produced*yet_to_produced*updated_by*update_date";
			
			$data_array1="".$cbo_source."*".$txt_embl_company_id."*".$txt_location_id."*".$cbo_embel_name."*".$cbo_embel_type."*".$txt_issue_date."*".$txt_receive_qty."*".$txt_reject_qty."*3*".$sewing_production_variable."*".$txt_remark."*".$txt_floor_id."*".$txt_cumul_receive_qty."*".$txt_yet_to_receive."*".$user_id."*'".$pc_date_time."'";
			// pro_garments_production_dtls table data entry here 
			if(str_replace("'","",$sewing_production_variable)!=1 && str_replace("'","",$txt_mst_id)!='' )// check is not gross level
			{
				
				$field_array="id, mst_id, production_type, color_size_break_down_id, production_qnty,reject_qty";
				
				if(str_replace("'","",$sewing_production_variable)==2)//color level wise
				{		
					$color_sizeID_arr=sql_select( "select id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and color_mst_id!=0 order by id" );
					$colSizeID_arr=array(); 
					foreach($color_sizeID_arr as $val){
						$index = $val[csf("color_number_id")];
						$colSizeID_arr[$index]=$val[csf("id")];
					}	
					
					// $colorIDvalue concate as colorID*Value**colorID*Value -------------------------//
					$rowExRej = explode("**",$colorIDvalueRej);
					foreach($rowExRej as $rowR=>$valR)
					{
						$colorSizeRejIDArr = explode("*",$valR);
						//echo $colorSizeRejIDArr[0]; die;
						$rejQtyArr[$colorSizeRejIDArr[0]]=$colorSizeRejIDArr[1];
					}
				
					$rowEx = explode("**",$colorIDvalue); 
					//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
					$data_array="";$j=0;
					foreach($rowEx as $rowE=>$val)
					{
						$colorSizeNumberIDArr = explode("*",$val);
						//2 for Issue to Print / Emb Entry
						$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );

						if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",3,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."')";
						else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",3,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."')";
						//$dtls_id=$dtls_id+1;							
						$j++;								
					}
				}
				
				if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
				{		
					$color_sizeID_arr=sql_select( "select id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name order by size_number_id,color_number_id" );
					$colSizeID_arr=array(); 
					foreach($color_sizeID_arr as $val){
						$index = $val[csf("size_number_id")].$val[csf("color_number_id")];
						$colSizeID_arr[$index]=$val[csf('id')];
					}	
					
					//	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------// 
					$rowExRej = explode("***",$colorIDvalueRej);
					foreach($rowExRej as $rowR=>$valR)
					{
						$colorAndSizeRej_arr = explode("*",$valR);
						$sizeID = $colorAndSizeRej_arr[0];
						$colorID = $colorAndSizeRej_arr[1];				
						$colorSizeRej = $colorAndSizeRej_arr[2];
						$index = $sizeID.$colorID;
						$rejQtyArr[$index]=$colorSizeRej;
					}
				
				
					$rowEx = explode("***",$colorIDvalue); 
					//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
					$data_array="";$j=0;
					foreach($rowEx as $rowE=>$valE)
					{
						$colorAndSizeAndValue_arr = explode("*",$valE);
						$sizeID = $colorAndSizeAndValue_arr[0];
						$colorID = $colorAndSizeAndValue_arr[1];				
						$colorSizeValue = $colorAndSizeAndValue_arr[2];
						$index = $sizeID.$colorID;
						//2 for Issue to Print / Emb Entry
						$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );

						if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",3,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$rejQtyArr[$index]."')";
						else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",3,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$rejQtyArr[$index]."')";
						//$dtls_id=$dtls_id+1;
						$j++;
					}
				}
				 
				//$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array,$data_array,1);
			}//end cond
	

	
			$challanrID=sql_update("pro_gmts_delivery_mst",$field_array_delivery,$data_array_delivery,"id",$txt_system_id,1);
			$rID=sql_update("pro_garments_production_mst",$field_array1,$data_array1,"id","".$txt_mst_id."",1);//echo $rID;die;
			$dtlsrID=true; $dtlsrDelete=true;
			if(str_replace("'","",$sewing_production_variable)!=1 && str_replace("'","",$txt_mst_id)!='')// check is not gross level
			{
				$dtlsrDelete = execute_query("delete from pro_garments_production_dtls where mst_id=$txt_mst_id",1);
				$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array,$data_array,1);
			}
			//echo "10**insert into pro_garments_production_dtls (".$field_array.") values ".$data_array;die;
			//echo "10**". $challanrID ."&&". $rID ."&&". $dtlsrID;die;
  			
			if($db_type==0)
			{
				if($rID && $challanrID && $dtlsrID && $dtlsrDelete)
				{
					mysql_query("COMMIT");  
					echo "1**".$mst_id."**".str_replace("'","",$txt_challan_no);
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**";
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($rID && $challanrID && $dtlsrID && $dtlsrDelete)
				{
					oci_commit($con); 
					echo "1**".$mst_id."**".str_replace("'","",$txt_challan_no);
				}
				else
				{
					oci_rollback($con);
					echo "10**";
				}
			}
		}
		//check_table_status( 160,0);
		disconnect($con);
		die;
		
		
	}
 
	else if ($operation==2)  // Delete Here---------------------------------------------------------- 
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		$rID = sql_delete("pro_garments_production_mst","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'id ',$txt_mst_id,1);
		$dtlsrID = sql_delete("pro_garments_production_dtls","status_active*is_deleted","0*1",'mst_id',$txt_mst_id,1);
		$mst_id=str_replace("'","",$txt_system_id);
		
 		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "2**".$mst_id."**".str_replace("'","",$txt_challan_no); 
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$mst_id."**".str_replace("'","",$txt_challan_no); 
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);   
				echo "2**".$mst_id."**".str_replace("'","",$txt_challan_no); 
			}
			else
			{
				oci_rollback($con);
				echo "10**".$mst_id."**".str_replace("'","",$txt_challan_no); 
			}
		}
		disconnect($con);
		die;
	}
}

if ($action=="challan_no_popup")
{
	echo load_html_head_contents("Challan Info", "../../", 1, 1,'','','');
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
                    <th>Wash Type</th>
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
							echo create_drop_down( "cbo_embel_type", 160, $emblishment_wash_type,"", 1, "--- Select Printing ---", $selected, "" );  
						?>       
                    </td>
                    <td align="center" id="search_by_td">				
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td> 	
            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_embel_type').value+'_'+document.getElementById('txt_company_id').value, 'create_challan_search_list_view', 'search_div', 'wash_challan_receive_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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

if($action=="create_challan_search_list_view")
{
	$data = explode("_",$data);
	
	$search_string="%".trim($data[0])."";
	if($data[1]==0) $print_type_cond=""; else $print_type_cond=" and a.embel_type=$data[1]";
	$company_id =$data[2];
	$search_field_cond=" and a.sys_number like '$search_string'";
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	
	$sql = "select a.id, $year_field, a.sys_number_prefix_num, a.sys_number, a.embel_type, a.production_source, a.serving_company, a.location_id, a.floor_id, a.organic,a.issue_challan_id from pro_gmts_delivery_mst a where a.production_type=3 and a.embel_name=3 and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $search_field_cond $print_type_cond order by a.id"; 
	//echo $sql;//die;
	$result = sql_select($sql);
	$floor_arr=return_library_array( "select id,floor_name from lib_prod_floor",'id','floor_name');
	$location_arr=return_library_array( "select id,location_name from lib_location",'id','location_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	
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
            $i=1;
            foreach ($result as $row)
            {  
                if ($i%2==0)  
                    $bgcolor="#E9F3FF";
                else
                    $bgcolor="#FFFFFF";	
					 
                if($row[csf('production_source')]==1)
					$serv_comp=$company_arr[$row[csf('serving_company')]]; 
				else
					$serv_comp=$supplier_arr[$row[csf('serving_company')]];
        	?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]."**".$row[csf('issue_challan_id')]; ?>');"> 
                    <td width="40"><? echo $i; ?></td>
                    <td width="70"><p>&nbsp;<? echo $row[csf('sys_number_prefix_num')]; ?></p></td>
                    <td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
                    <td width="80"><p><? echo $emblishment_wash_type[$row[csf('embel_type')]]; ?></p></td>               
                    <td width="100"><p><? echo $knitting_source[$row[csf('production_source')]]; ?></p></td>
                    <td width="110"><p><? echo $serv_comp; ?></p></td>
                    <td width="110"><p><? echo $location_arr[$row[csf('location_id')]]; ?></p></td>
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

if($action=='populate_data_from_challan_popup')
{
	
	
	$data_array=sql_select("select id, company_id, sys_number, embel_type, embel_name, production_source, serving_company, location_id,delivery_basis, floor_id, organic, delivery_date,issue_challan_id,working_company_id,working_location_id from pro_gmts_delivery_mst where id='$data'");
	foreach ($data_array as $row)
	{ 
	
	 	if($row[csf('production_source')]==1) {	$serv_comp=$company_arr[$row[csf('serving_company')]]; }
		else {	$serv_comp=$supplier_arr[$row[csf('serving_company')]];}
					
		$location=$location_arr[$row[csf('location_id')]];
		$floor=$floor_arr[$row[csf('floor_id')]];
		echo "document.getElementById('txt_challan_no').value = '".$row[csf("sys_number")]."';\n";
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";
		echo "$('#cbo_company_name').attr('disabled','true')".";\n";
		echo "$('#cbo_source').val('".$row[csf('production_source')]."');\n";
		//echo "load_drop_down( 'requires/wash_challan_receive_controller', ".$row[csf('production_source')].", 'load_drop_down_embro_issue_source', 'emb_company_td' );\n";
		echo "$('#txt_embl_company').val('".$serv_comp."');\n";
		
		echo "$('#txt_embl_company_id').val('".$row[csf('serving_company')]."');\n";
		echo "$('#txt_location_name').val('".$location."');\n";
		echo "$('#txt_location_id').val('".$row[csf('location_id')]."');\n";
		echo "load_drop_down( 'requires/wash_challan_receive_controller','".$row[csf('working_company_id')]."', 'load_drop_down_working_location', 'working_location_td' );\n";
		//echo "load_drop_down( 'requires/wash_challan_receive_controller', ".$row[csf('location_id')].", 'load_drop_down_floor', 'floor_td' );\n";
		echo "$('#txt_issue_challan_id').val('".$row[csf('issue_challan_id')]."');\n";
		echo "$('#txt_floor_id').val('".$row[csf('floor_id')]."');\n";
		echo "$('#txt_floor_name').val('".$floor."');\n";
		echo "$('#cbo_embel_name').val('".$row[csf('embel_name')]."');\n";
		echo "$('#cbo_embel_type').val('".$row[csf('embel_type')]."');\n";
		echo "$('#txt_organic').val('".$row[csf('organic')]."');\n";
		echo "$('#txt_system_id').val('".$row[csf('id')]."');\n";
		echo "$('#txt_issue_date').val('".change_date_format($row[csf('delivery_date')])."');\n";
		
		echo "$('#cbo_working_company_name').val('".$row[csf('working_company_id')]."');\n";
		echo "$('#cbo_working_location').val('".$row[csf('working_location_id')]."');\n";
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_issue_print_embroidery_entry',1,1);\n";  
		exit();
	}
}

if($action=="emblishment_issue_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name");
	$location_library=return_library_array( "select id, location_name from  lib_location", "id", "location_name");
	$floor_library=return_library_array( "select id, floor_name from  lib_prod_floor", "id", "floor_name");
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$order_array=array();
	$order_sql="select a.job_no, a.buyer_name,a.style_ref_no, a.style_description,b.id, b.po_number, b.po_quantity from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3)  and b.is_deleted=0";
	$order_sql_result=sql_select($order_sql);
	foreach ($order_sql_result as $row)
	{
		$order_array[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
		$order_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
		$order_array[$row[csf('id')]]['po_number']=$row[csf('po_number')];
		$order_array[$row[csf('id')]]['po_quantity']=$row[csf('po_quantity')];
		$order_array[$row[csf('id')]]['style_ref']=$row[csf('style_ref_no')];
		$order_array[$row[csf('id')]]['style_des']=$row[csf('style_description')];
	}
	
	
	$sql="select id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, location_id, delivery_basis, embel_name, embel_type,
	production_source, serving_company, floor_id, organic, delivery_date,working_company_id,working_location_id from pro_gmts_delivery_mst where production_type=3 and id='$data[1]' and 
	status_active=1 and is_deleted=0 ";
	
	
	$dataArray=sql_select($sql);

?>
<div style="width:930px;">
    <table width="900" cellspacing="0" align="right">
        <tr>
            <td colspan="6" align="center" style="font-size:24px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="6" align="center" style="font-size:14px"> 
				<?
				
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0] and status_active=1 and is_deleted=0"); 
					foreach ($nameArray as $result)
					{ 
					?>
						Plot No: <? echo $result[csf('plot_no')]; ?> 
						Level No: <? echo $result[csf('level_no')]?>
						Road No: <? echo $result[csf('road_no')]; ?> 
						Block No: <? echo $result[csf('block_no')];?> 
						City No: <? echo $result[csf('city')];?> 
						Zip Code: <? echo $result[csf('zip_code')]; ?> 
						Province No: <?php echo $result[csf('province')];?> 
						Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
						Email Address: <? echo $result[csf('email')];?> 
						Website No: <? echo $result[csf('website')];
						
					}
                ?> 
            </td>  
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:20px"><u><strong>Challan/Gate Pass</strong></u></td>
        </tr>
        <tr>
            <td width="125"><strong>Challan No:</strong></td> <td width="175px"><? echo $dataArray[0][csf('sys_number')]; ?></td>
            <td width="125"><strong>Embel. Name :</strong></td><td width="175px"><? echo $emblishment_name_array[$dataArray[0][csf('embel_name')]]; ?></td>
            <td width="125"><strong>Emb. Type:</strong></td><td width="175px">
			<? 
				if ($dataArray[0][csf('embel_name')]==1) echo $emblishment_print_type[$dataArray[0][csf('embel_type')]];
				elseif ($dataArray[0][csf('embel_name')]==2) echo $emblishment_embroy_type[$dataArray[0][csf('embel_type')]];
				elseif ($dataArray[0][csf('embel_name')]==3) echo $emblishment_wash_type[$dataArray[0][csf('embel_type')]]; 
				elseif ($dataArray[0][csf('embel_name')]==4) echo $emblishment_spwork_type[$dataArray[0][csf('embel_type')]];
			 ?>
            </td>
        </tr>
        <tr>
            <td><strong>Emb. Source:</strong></td><td><? echo $knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
            <td><strong>Emb. Company:</strong></td><td>
				<? 
					if($dataArray[0][csf('production_source')]==1) echo $company_library[$dataArray[0][csf('serving_company')]];
					else										   echo $supplier_library[$dataArray[0][csf('serving_company')]];
				 
                ?>
            </td>
            <td><strong>Location:</strong></td><td><? echo $location_library[$dataArray[0][csf('location_id')]]; ?></td>
        </tr>
        <tr>
        	<td><strong>Floor :</strong></td><td><? echo $floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
        	<td><strong>Organic :</strong></td><td><? echo $dataArray[0][csf('organic')]; ?></td>
            <td><strong>Delivery Date :</strong></td><td><? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
        </tr>
         <tr>
         <td width="126"><strong>Working Company</strong></td><td>: <? echo $company_library[$dataArray[0][csf('working_company_id')]]; ?></td>
            <td><strong>Working Location </strong></td><td>: <? echo $location_library[$dataArray[0][csf('working_location_id')]]; ?></td>
        </tr>
        <tr>
        	<td  colspan="6" id="barcode_img_id"></td>
        	
        </tr>
       
    </table>
         <br>
        <?
		
			$delivery_mst_id =$dataArray[0][csf('id')];
			if($data[2]==3)
			{
				
				$sql="SELECT sum(b.production_qnty) as production_qnty,a.po_break_down_id,a.item_number_id,a.country_id, d.color_number_id,
				count(b.id) as 	num_of_bundle 
				from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown d 
				where a.delivery_mst_id ='$data[1]' 
				and a.id=b.mst_id and b.color_size_break_down_id=d.id and b.production_type=3 and b.status_active=1 and b.is_deleted=0 and d.status_active in(1,2,3) 
				and d.is_deleted=0 
				group by a.po_break_down_id,a.item_number_id,a.country_id,d.color_number_id
				order by a.po_break_down_id,d.color_number_id ";
			}
			else
			{
				$sql="SELECT sum(a.production_qnty) as production_qnty,c.po_break_down_id,c.item_number_id,c.country_id, b.color_number_id 
				from pro_garments_production_dtls a, wo_po_color_size_breakdown b,pro_garments_production_mst c where c.delivery_mst_id ='$data[1]' 
				and c.id=a.mst_id  and a.color_size_break_down_id=b.id and b.production_type=3 and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 
				group by c.po_break_down_id,c.item_number_id,c.country_id,b.color_number_id ";
			}

			$result=sql_select($sql);
		?> 
         
	<div style="width:100%;">
    <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" style=" margin-top:20px;" >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="80" align="center">Buyer</th>
            <th width="80" align="center">Job</th>
            <th width="80" align="center">Style Ref</th>
            <th width="80" align="center">Style Des</th>
            <th width="80" align="center">Order No.</th>
            <th width="80" align="center">Gmt. Item</th>
            <th width="80" align="center">Country</th>
            <th width="80" align="center">Color</th>
            <th width="80" align="center">Gmt. Qty</th>
            <? if($data[2]==3)  {  ?>
            <th align="center">No of Bundle</th>
            <? }   ?>
        </thead>
        <tbody>
			<?
            
            $i=1;
            $tot_qnty=array();
                foreach($result as $val)
                {
                    if ($i%2==0)  
                        $bgcolor="#E9F3FF";
                    else
                        $bgcolor="#FFFFFF";
					$color_count=count($cid);
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>"  style="font-size:12px">
                        <td><? echo $i;  ?></td>
                        <td align="center"><? echo $buyer_arr[$order_array[$val[csf('po_break_down_id')]]['buyer_name']]; ?></td>
                        <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['job_no']; ?></td>
                        <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['style_ref']; ?></td>
                        <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['style_des']; ?></td>
                        <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['po_number']; ?></td>
                        <td align="center"><? echo $garments_item[$val[csf('item_number_id')]]; ?></td>
                        <td align="center"><? echo $country_library[$val[csf('country_id')]]; ?></td>
                        <td align="center"><? echo $color_library[$val[csf('color_number_id')]];?></td>
                        <td align="right"><?  echo $val[csf('production_qnty')]; ?></td>
                        <? if($data[2]==3) 
						 {  ?>
                        <td  align="center"> <?  echo $val[csf('num_of_bundle')]; ?></td>
                        <? 
						$total_bundle+=$val[csf('num_of_bundle')];
						}   
						?>
                        
                    </tr>
                    <?
					$production_quantity+=$val[csf('production_qnty')];
					$i++;
                }
            ?>
        </tbody>
        <tr>
        <? if($data[3]==3) $colspan=8 ; else $colspan=7; ?>
            <td colspan="9" align="right"><strong>Grand Total :</strong></td>
            <td align="right"><?  echo $production_quantity; ?></td>
             <? if($data[2]==3)  {  ?>
            <td  align="center"> <?  echo $total_bundle; ?></td>
            <? }   ?>
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
	function generateBarcode( valuess ){
			var value = valuess;//$("#barcodeValue").val();
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();
			var settings = {
			  output:renderer,
			  bgColor: '#FFFFFF',
			  color: '#000000',
			  barWidth: 1,
			  barHeight: 30,
			  moduleSize:5,
			  posX: 10,
			  posY: 20,
			  addQuietZone: 1
			};
		
			 value = {code:value, rect: false};
			$("#barcode_img_id").show().barcode(value, btype, settings);
		} 
	    generateBarcode('<? echo $dataArray[0][csf('sys_number')]; ?>');
	 </script>
<?
exit();	
}
// new**************new**************new**************new**************new**************new**************new**************

if($action=="load_mst_data")
{
	//echo $data;die;
 	$ex_data = "'".implode("','",explode(",",$data))."'";
	
	$txt_order_no = "%".trim($ex_data[0])."%";
	$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	
	$bundle_count=count(explode(",",$ex_data)); $bundle_nos_cond="";
	if($db_type==2 && $bundle_count>400)
	{
		$bundle_nos_cond=" and (";
		$bundleArr=array_chunk(explode(",",$ex_data),399);
		foreach($bundleArr as $bundleNos)
		{
			$bundleNos=implode(",",$bundleNos);
			$bundle_nos_cond.=" c.bundle_no in($bundleNos) or ";
		}
		$bundle_nos_cond=chop($bundle_nos_cond,'or ');
		$bundle_nos_cond.=")";
	}
	else
	{
		$bundle_nos_cond=" and c.bundle_no in ($ex_data)";
	}
	
	$sql_mst_data=sql_select("select a.sys_number, a.company_id,a.location_id, a.embel_name, a.embel_type, a.serving_company,a.floor_id,a.organic,
	a.production_source
	from  pro_gmts_delivery_mst a,pro_garments_production_dtls c where  a.id=c.delivery_mst_id  and c.production_type=2
	and a.embel_name=3 and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0 $bundle_nos_cond
	group by a.sys_number, a.company_id,a.location_id, a.embel_name, a.embel_type, a.serving_company,a.floor_id,a.organic,a.production_source");
	
	
	//print_r($sql_mst_data);die;
	foreach($sql_mst_data as $val)
	{
		
		 	if($val[csf('production_source')]==1) {$serv_comp=$company_arr[$val[csf('serving_company')]]; }
			else { $serv_comp=$supplier_arr[$val[csf('serving_company')]];}
			$location=$location_arr[$val[csf('location_id')]];
			$floor=$floor_arr[$val[csf('floor_id')]];
			echo "$('#txt_issue_challan_scan').val('".$val[csf('sys_number')]."');\n";
			echo "$('#cbo_embel_type').val(".$val[csf('embel_type')].");\n";
			echo "$('#cbo_source').val('".$val[csf('production_source')]."');\n";
			echo "$('#txt_embl_company').val('".$serv_comp."');\n";
			echo "$('#txt_embl_company_id').val(".$val[csf('serving_company')].");\n";
			echo "$('#txt_location_name').val('".$location."');\n";
			echo "$('#txt_floor_name').val('".$floor."');\n";
			echo "$('#txt_organic').val('".$val[csf('organic')]."');\n";
			echo "$('#txt_floor_id').val(".$val[csf('floor_id')].");\n";
			echo "$('#txt_location_id').val(".$val[csf('location_id')].");\n";
	}
}


if ($action=="isssue_challan_no_popup")
{
	echo load_html_head_contents("Challan Info", "../../", 1, 1,'','','');
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
						<th>Print Type</th>
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
									echo create_drop_down( "cbo_company_id", 180, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select --", $cbo_company_name, "",1 );
								?>      
						</td>
						<td align="center">
							<?
								echo create_drop_down( "cbo_embel_type", 160, $emblishment_print_type,"", 1, "--- Select Printing ---", $selected, "" );  
							?>       
						</td>
						<td align="center" id="search_by_td">				
							<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
						</td> 	
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_embel_type').value+'_'+document.getElementById('cbo_company_id').value+'_'+<? echo $delivery_basis; ?>, 'create_issue_challan_search_list_view', 'search_div', 'wash_challan_receive_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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
if($action=="create_bundle_challan_search_list_view")
{
 	$ex_data = explode("_",$data);
	$txt_order_no = "%".trim($ex_data[0])."%";
	$company = $ex_data[1];
	$challan_no = "%".trim($ex_data[2])."%";
	
	$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	$scanned_bundle_arr=return_library_array( "select bundle_no, bundle_no from pro_cut_delivery_color_dtls where production_type=3 and embel_name=3 and status_active=1 and is_deleted=0",'bundle_no','bundle_no');
	
	$sql="select d.po_break_down_id, d.job_no_mst, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.bundle_no, c.production_qnty as qty, e.po_number from  pro_gmts_delivery_mst a,pro_cut_delivery_color_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e where   a.id=c.delivery_mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and a.company_id=$company and e.po_number like '$txt_order_no' and a.sys_number_prefix_num like '$challan_no' and c.production_type=2 and c.embel_name=3 and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0";
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
            $i=1;
            foreach ($result as $row)
            {  
				if($scanned_bundle_arr[$row[csf('bundle_no')]]=="")
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)"> 
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


// new need
if($action=="create_bundle_search_list_view")
{
 	$ex_data = explode("_",$data);
	$txt_order_no = "%".trim($ex_data[0])."%";
	$company = $ex_data[1];
	$bundle_no = "%".trim($ex_data[2])."%";
	$selectedBuldle=$ex_data[3];
	
	$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	
	$scanned_bundle_arr=return_library_array( "select b.bundle_no, b.bundle_no from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and b.production_type=3 and a.embel_name=3 and b.status_active=1 and b.is_deleted=0",'bundle_no','bundle_no');
	
	foreach(explode(",",$selectedBuldle) as $bn)
	{
		$scanned_bundle_arr[$bn]=$bn;	
	}

	
	
	
	$sql="select d.po_break_down_id, d.job_no_mst, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.bundle_no, c.production_qnty as qty, e.po_number from  pro_gmts_delivery_mst a,pro_cut_delivery_color_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e where   a.id=c.delivery_mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and a.company_id=$company and e.po_number like '$txt_order_no' and c.bundle_no like '$bundle_no' and c.production_type=2 and c.embel_name=3 and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0";
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
            $i=1;
            foreach ($result as $row)
            {  
				if($scanned_bundle_arr[$row[csf('bundle_no')]]=="")
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)"> 
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

if($action=="create_issue_challan_search_list_view")
{
	$data = explode("_",$data);
	$supplier_arr=return_library_array( "select id, supplier_name from  lib_supplier",'id','supplier_name');
	$search_string="%".trim($data[0])."%";
	if($data[1]==0) $print_type_cond=""; else $print_type_cond=" and a.embel_type=$data[1]";
	$company_id =$data[2];
	$search_field_cond=" and a.sys_number like '$search_string'";
	$actual_delivery_basis=$data[3];
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	
	if(str_replace("'","",$company_id)==0) { echo "Please Select Company first";die;}
	
	$delivery_basis=return_field_value("cut_panel_delevery","variable_settings_production","company_name=$company_id and variable_list=32 and 
	status_active=1 and is_deleted=0");
    if($actual_delivery_basis!=$delivery_basis) { echo "Receive Basis ".$cut_panel_basis[$actual_delivery_basis]." is not applicable in your setup.";die;}
	
	
	$sql = "select a.id, $year_field, a.sys_number_prefix_num,a.company_id, a.sys_number, a.embel_type, a.production_source, a.serving_company, a.location_id, a.floor_id, a.organic from pro_gmts_delivery_mst a where a.production_type=2 and a.embel_name=3 and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $search_field_cond $print_type_cond order by  a.sys_number_prefix_num"; 
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
            $i=1;
            foreach ($result as $row)
            {  
                if ($i%2==0)  
                    $bgcolor="#E9F3FF";
                else
                    $bgcolor="#FFFFFF";	
					 
                if($row[csf('production_source')]==1) {$serv_comp=$company_arr[$row[csf('serving_company')]]; }
				else  {
						$serv_comp=$supplier_arr[$row[csf('serving_company')]];
					  }
					
					$location=$location_arr[$row[csf('location_id')]];
					$floor=$floor_arr[$row[csf('floor_id')]];
					//print_r($supplier_arr);
					//echo $serv_comp;die;
        	?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]."_".$row[csf('company_id')]."_".$row[csf('production_source')]."_".$row[csf('serving_company')]."_".$row[csf('location_id')]."_".$row[csf('floor_id')]."_".$row[csf('sys_number')]."_".$row[csf('organic')]."_".$row[csf('embel_type')]."_".$serv_comp."_".$location."_".$floor; ?>');"> 
                    <td width="40"><? echo $i; ?></td>
                    <td width="70"><p>&nbsp;<? echo $row[csf('sys_number_prefix_num')]; ?></p></td>
                    <td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
                    <td width="80"><p><? echo $emblishment_print_type[$row[csf('embel_type')]]; ?></p></td>               
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


if($action=="embrodary_color_wise_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name");
	$location_library=return_library_array( "select id, location_name from  lib_location", "id", "location_name");
	$floor_library=return_library_array( "select id, floor_name from  lib_prod_floor", "id", "floor_name");
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

	$sql="select id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, location_id, delivery_basis, embel_name, embel_type,
	production_source, serving_company, floor_id, organic, delivery_date,working_company_id,working_location_id from pro_gmts_delivery_mst where production_type=3 and id='$data[1]' and 
	status_active=1 and is_deleted=0 ";
	$dataArray=sql_select($sql);

?>
<div style="width:1100px;">
    <table width="900" cellspacing="0" align="left">
        <tr>
            <td colspan="6" align="center" style="font-size:24px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="6" align="center" style="font-size:14px"> 
				<?
				
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0] and status_active=1 and is_deleted=0"); 
					foreach ($nameArray as $result)
					{ 
					?>
						Plot No: <? echo $result[csf('plot_no')]; ?> 
						Level No: <? echo $result[csf('level_no')]?>
						Road No: <? echo $result[csf('road_no')]; ?> 
						Block No: <? echo $result[csf('block_no')];?> 
						City No: <? echo $result[csf('city')];?> 
						Zip Code: <? echo $result[csf('zip_code')]; ?> 
						Province No: <?php echo $result[csf('province')];?> 
						Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
						Email Address: <? echo $result[csf('email')];?> 
						Website No: <? echo $result[csf('website')];
						
					}
                ?> 
            </td>  
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:20px"><u><strong>Challan/Gate Pass</strong></u></td>
        </tr>
        <tr>
            <td width="125"><strong>Challan No:</strong></td> <td width="175px"><? echo $dataArray[0][csf('sys_number')]; ?></td>
            <td width="125"><strong>Embel. Name :</strong></td><td width="175px"><? echo $emblishment_name_array[$dataArray[0][csf('embel_name')]]; ?></td>
            <td width="125"><strong>Emb. Type:</strong></td><td width="175px">
			<? 
				if ($dataArray[0][csf('embel_name')]==1) echo $emblishment_print_type[$dataArray[0][csf('embel_type')]];
				elseif ($dataArray[0][csf('embel_name')]==2) echo $emblishment_embroy_type[$dataArray[0][csf('embel_type')]];
				elseif ($dataArray[0][csf('embel_name')]==3) echo $emblishment_wash_type[$dataArray[0][csf('embel_type')]]; 
				elseif ($dataArray[0][csf('embel_name')]==4) echo $emblishment_spwork_type[$dataArray[0][csf('embel_type')]];
			 ?>
            </td>
        </tr>
        <tr>
            <td><strong>Emb. Source:</strong></td><td><? echo $knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
            <td><strong>Emb. Company:</strong></td><td>
				<? 
					if($dataArray[0][csf('production_source')]==1) echo $company_library[$dataArray[0][csf('serving_company')]];
					else										   echo $supplier_library[$dataArray[0][csf('serving_company')]];
				 
                ?>
            </td>
            <td><strong>Location:</strong></td><td><? echo $location_library[$dataArray[0][csf('location_id')]]; ?></td>
        </tr>
        <tr>
        	<td><strong>Floor :</strong></td><td><? echo $floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
        	<td><strong>Organic :</strong></td><td><? echo $dataArray[0][csf('organic')]; ?></td>
            <td><strong>Delivery Date :</strong></td><td><? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
        </tr>
        <tr>
         <td width="126"><strong>Working Company</strong></td><td>: <? echo $company_library[$dataArray[0][csf('working_company_id')]]; ?></td>
            <td><strong>Working Location </strong></td><td>: <? echo $location_library[$dataArray[0][csf('working_location_id')]]; ?></td>
        </tr>
        <tr>
        	<td  colspan="6" id="barcode_img_id"></td>
        	
        </tr>
       
    </table>
         <br>
        <?
		
	if($db_type==2) $group_concat="  listagg(cast(a.cut_no AS VARCHAR2(4000)),',') within group (order by a.cut_no) as cut_no" ;
	else if($db_type==0) $group_concat=" group_concat(a.cut_no) as cut_no" ;

			
			$delivery_mst_id =$dataArray[0][csf('id')];
			if($data[2]==3)
			{
				
				
				$sql="SELECT $group_concat,e.buyer_name,sum(b.production_qnty) as production_qnty,a.country_id, d.color_number_id,d.size_number_id,e.style_ref_no,e.style_description,
				count(b.id) as 	num_of_bundle,sum(b.reject_qty) as reject_qty 
				from pro_garments_production_mst a, pro_garments_production_dtls b,wo_po_color_size_breakdown d,
				 wo_po_details_master e, wo_po_break_down f 
				where a.delivery_mst_id ='$data[1]' 
				and e.job_no=f.job_no_mst and f.id=a.po_break_down_id and a.id=b.mst_id and b.color_size_break_down_id=d.id and b.status_active=1 and b.is_deleted=0 and d.status_active in(1,2,3) 
				and d.is_deleted=0 
				group by e.buyer_name,a.country_id, d.color_number_id,d.size_number_id,e.style_ref_no,e.style_description
				order by e.buyer_name ";
				
				
			 //echo $sql;	
				
				$result=sql_select($sql);
				$bundle_color_size_data=array();
				$bundle_color_data=array();
				$bundle_size_arr=array();
				$grand_total_arr=array();
				
				$grand_total_size_arr=array();
				foreach($result as $fs)
				{
					$bundle_size_arr[$fs[csf('size_number_id')]]=$fs[csf('size_number_id')];
					$bundle_color_data[$fs[csf('buyer_name')]][$fs[csf('country_id')]][$fs[csf('color_number_id')]]['reject_qty']+=$fs[csf('reject_qty')];
					$bundle_color_data[$fs[csf('buyer_name')]][$fs[csf('country_id')]][$fs[csf('color_number_id')]]['qty']+=$fs[csf('production_qnty')];
					$bundle_color_data[$fs[csf('buyer_name')]][$fs[csf('country_id')]][$fs[csf('color_number_id')]]['bundle_num']+=$fs[csf('num_of_bundle')];
					$bundle_color_size_data[$fs[csf('buyer_name')]][$fs[csf('country_id')]][$fs[csf('color_number_id')]][$fs[csf('size_number_id')]]['qty']=$fs[csf('production_qnty')];
					
					$bundle_cut_data[$fs[csf('buyer_name')]][$fs[csf('country_id')]][$fs[csf('color_number_id')]]['cut_no']=implode(',',array_unique(explode(',',$fs[csf('cut_no')])));
					
					$bundle_cut_data[$fs[csf('buyer_name')]][$fs[csf('country_id')]][$fs[csf('color_number_id')]]['style_ref']=$fs[csf('style_ref_no')];
					$bundle_cut_data[$fs[csf('buyer_name')]][$fs[csf('country_id')]][$fs[csf('color_number_id')]]['style_des']=$fs[csf('style_description')];

					$grand_total_arr['qty']+=$fs[csf('production_qnty')];
					$grand_total_arr['bundle_num']+=$fs[csf('num_of_bundle')];
					$grand_total_arr['reject_qty']+=$fs[csf('reject_qty')];
					$grand_total_size_arr[$fs[csf('size_number_id')]]+=$fs[csf('production_qnty')];
				}
				
				
				
			// print_r($bundle_cut_data);die;
			$table_width=900+(count($bundle_size_arr)*50);
		?> 
         
	<div style="width:100%;">
    <table align="left" cellspacing="0" width="<? echo $table_width; ?>"  border="1" rules="all" class="rpt_table" style=" margin-top:20px;" >
        <thead bgcolor="#dddddd" align="center">
            <tr>
                <th width="30" rowspan="2">SL</th>
                <th width="80" align="center" rowspan="2">Buyer</th>
                <th width="80" align="center" rowspan="2">Style Ref</th>
                <th width="100" align="center" rowspan="2">Style Des</th>
                <th width="80" align="center" rowspan="2">Country</th>
                <th width="80" align="center" rowspan="2">Color</th>
                <th width="80" align="center" rowspan="2">Cutting No</th>
             
                <th align="center" width="50" colspan="<? echo count($bundle_size_arr); ?>">Size</th>
              
                <th width="80" align="center" rowspan="2">Total Issue Qty</th>
                <th width="80" align="center" rowspan="2">No of Bundle</th>
                <th width="80" align="center" rowspan="2">Reject Qty</th>
                <th width= align="center" rowspan="2">Remarks</th>
              </tr>
              <tr>
                <?
                $i=0;
                foreach($bundle_size_arr as $inf)
                {
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
            $i=1;
            $tot_qnty=array();
			foreach($bundle_color_data as $buyer_id=>$buy_value)
			{
				foreach($buy_value as $county_id=>$county_value)
				{
					foreach($county_value as $color_id=>$color_value)
					{
                    if ($i%2==0)  
                        $bgcolor="#E9F3FF";
                    else
                        $bgcolor="#FFFFFF";
					$color_count=count($cid);
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>"  style="font-size:12px">
                        <td><? echo $i;  ?></td>
                        <td align="center"><? echo $buyer_arr[$buyer_id]; ?></td>
                        <td align="center"><? echo $bundle_cut_data[$buyer_id][$county_id][$color_id]['style_ref']; ?></td>
                        <td align="center"><? echo $bundle_cut_data[$buyer_id][$county_id][$color_id]['style_des']; ?></td>
                        <td align="center"><? echo $country_library[$county_id]; ?></td>
                        <td align="center"><? echo $color_library[$color_id]; ?></td>
                        <td align="center"><? echo $bundle_cut_data[$buyer_id][$county_id][$color_id]['cut_no']; ?></td>
                        <?
                        foreach($bundle_size_arr as $inf)
						{
						?>
                        <td align="center" width="50"><? echo $bundle_color_size_data[$buyer_id][$county_id][$color_id][$inf]['qty']; ?></td>
                        <?	
						}
						?>
                        <td align="center"><? echo $bundle_color_data[$buyer_id][$county_id][$color_id]['qty']; ?></td>
                        <td align="center"><? echo $bundle_color_data[$buyer_id][$county_id][$color_id]['bundle_num']; ?></td>
                        <td align="right"><?  echo $bundle_color_data[$buyer_id][$county_id][$color_id]['reject_qty']; ?></td>
                        <td  align="center"></td>
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
			foreach($bundle_size_arr as $inf)
			{
			?>
			<td align="center" width="50"><? echo $grand_total_size_arr[$inf]; ?></td>
			<?	
			}
			?>
             <td align="center"><? echo $grand_total_arr['qty']; ?></td>
            <td align="center"><? echo $grand_total_arr['bundle_num']; ?></td>
            <td align="right"><?  echo $grand_total_arr['reject_qty']; ?></td>
            <td  align="center"> <?  //echo $val[csf('num_of_bundle')]; ?></td>
        </tr>                           
    </table>
</div>  
    <?
		}
		else
		{
			$sql="SELECT d.buyer_name,sum(b.production_qnty) as production_qnty,a.country_id, c.color_number_id,c.size_number_id,sum(b.reject_qty) as reject_qty 
				
			from pro_garments_production_dtls b, wo_po_color_size_breakdown c,pro_garments_production_mst a,wo_po_details_master d, wo_po_break_down e
			where d.job_no=e.job_no_mst and e.id=a.po_break_down_id and a.delivery_mst_id ='$data[1]' 
			and a.id=b.mst_id  and b.color_size_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 
			group by d.buyer_name,a.country_id, c.color_number_id,c.size_number_id";
			$result=sql_select($sql);
			$bundle_color_size_data=array();
			$bundle_color_data=array();
			$bundle_size_arr=array();
			$grand_total_arr=array();
			
			$grand_total_size_arr=array();
			foreach($result as $fs)
			{
				$bundle_size_arr[$fs[csf('size_number_id')]]=$fs[csf('size_number_id')];
				$bundle_color_data[$fs[csf('buyer_name')]][$fs[csf('country_id')]][$fs[csf('color_number_id')]]['reject_qty']+=$fs[csf('reject_qty')];
				$bundle_color_data[$fs[csf('buyer_name')]][$fs[csf('country_id')]][$fs[csf('color_number_id')]]['qty']+=$fs[csf('production_qnty')];
				$bundle_color_size_data[$fs[csf('buyer_name')]][$fs[csf('country_id')]][$fs[csf('color_number_id')]][$fs[csf('size_number_id')]]['qty']=$fs[csf('production_qnty')];
				$grand_total_arr['qty']+=$fs[csf('production_qnty')];
				$grand_total_arr['reject_qty']+=$fs[csf('reject_qty')];
				$grand_total_size_arr[$fs[csf('size_number_id')]]+=$fs[csf('production_qnty')];
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
                $i=0;
                foreach($bundle_size_arr as $inf)
                {
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
            $i=1;
            $tot_qnty=array();
			foreach($bundle_color_data as $buyer_id=>$buy_value)
			{
				foreach($buy_value as $county_id=>$county_value)
				{
					foreach($county_value as $color_id=>$color_value)
					{
                    if ($i%2==0)  
                        $bgcolor="#E9F3FF";
                    else
                        $bgcolor="#FFFFFF";
					$color_count=count($cid);
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>"  style="font-size:12px">
                        <td><? echo $i;  ?></td>
                        <td align="center"><? echo $buyer_arr[$buyer_id]; ?></td>
                        <td align="center"><? echo $country_library[$county_id]; ?></td>
                        <td align="center"><? echo $color_library[$color_id]; ?></td>
                        <?
                        foreach($bundle_size_arr as $inf)
						{
						?>
                        <td align="center" width="50"><? echo $bundle_color_size_data[$buyer_id][$county_id][$color_id][$inf]['qty']; ?></td>
                        <?	
						}
						?>
                        <td align="center"><? echo $bundle_color_data[$buyer_id][$county_id][$color_id]['qty']; ?></td>
                        <td align="right"><?  echo $bundle_color_data[$buyer_id][$county_id][$color_id]['reject_qty']; ?></td>
                        <td  align="center"> <?  //echo $val[csf('num_of_bundle')]; ?></td>
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
			foreach($bundle_size_arr as $inf)
			{
			?>
			<td align="center" width="50"><? echo $grand_total_size_arr[$inf]; ?></td>
			<?	
			}
			?>
            <td align="center"><? echo $grand_total_arr['qty']; ?></td>
            <td align="right"><?  echo $grand_total_arr['reject_qty']; ?></td>
            <td  align="center"> <?  //echo $val[csf('num_of_bundle')]; ?></td>
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
	function generateBarcode( valuess ){
			var value = valuess;//$("#barcodeValue").val();
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();
			var settings = {
			  output:renderer,
			  bgColor: '#FFFFFF',
			  color: '#000000',
			  barWidth: 1,
			  barHeight: 30,
			  moduleSize:5,
			  posX: 10,
			  posY: 20,
			  addQuietZone: 1
			};
		
			 value = {code:value, rect: false};
			$("#barcode_img_id").show().barcode(value, btype, settings);
		} 
	    generateBarcode('<? echo $dataArray[0][csf('sys_number')]; ?>');
	 </script>
<?
exit();	
}

if($action=="wash_receive_challan_print")
	{
		
		extract($_REQUEST);
		$data=explode('*',$data);
		//print_r ($data);
		$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
		$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name");
		$location_library=return_library_array( "select id, location_name from  lib_location", "id", "location_name");
		$floor_library=return_library_array( "select id, floor_name from  lib_prod_floor", "id", "floor_name");
		$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
		$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	
		$sql="select id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, location_id, delivery_basis, embel_name, embel_type,
		production_source, serving_company, floor_id, body_part,organic, delivery_date,working_company_id,working_location_id from pro_gmts_delivery_mst where production_type=3 and id='$data[1]' and 
		status_active=1 and is_deleted=0 ";
		$dataArray=sql_select($sql);
	
	
		$cut_lay_arr=array();
		$lay_sql="select a.cutting_no, b.order_id, b.order_cut_no, b.color_id, b.gmt_item_id from ppl_cut_lay_mst a, ppl_cut_lay_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
		$lay_sql_data=sql_select($lay_sql);
		foreach($lay_sql_data as $row)
		{
			$cut_lay_arr[$row[csf('cutting_no')]][$row[csf('color_id')]]=$row[csf('order_cut_no')];
		}
	
	
	?>
	<div style="width:1100px;">
		<table width="900" cellspacing="0">
			<tr>
				<td colspan="6" align="center" style="font-size:24px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
			</tr>
			<tr class="form_caption">
				<td colspan="6" align="center" style="font-size:14px"> 
					<?
					
						$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0] and status_active=1 and is_deleted=0"); 
						foreach ($nameArray as $result)
						{ 
						?>
							Plot No: <? echo $result[csf('plot_no')]; ?> 
							Level No: <? echo $result[csf('level_no')]?>
							Road No: <? echo $result[csf('road_no')]; ?> 
							Block No: <? echo $result[csf('block_no')];?> 
							City No: <? echo $result[csf('city')];?> 
							Zip Code: <? echo $result[csf('zip_code')]; ?> 
							Province No: <?php echo $result[csf('province')];?> 
							Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
							Email Address: <? echo $result[csf('email')];?> 
							Website No: <? echo $result[csf('website')];
							
						}
					?> 
				</td>  
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:20px"><u><strong>Challan/Gate Pass</strong></u></td>
			</tr>
			<tr>
				<td width="125"><strong>Challan No:</strong></td> <td width="175px"><? echo $dataArray[0][csf('sys_number')]; ?></td>
				<td width="125"><strong>Embel. Name :</strong></td><td width="175px"><? echo $emblishment_name_array[$dataArray[0][csf('embel_name')]]; ?></td>
				<td width="125"><strong>Emb. Type:</strong></td><td width="175px">
				<? 
					if ($dataArray[0][csf('embel_name')]==1) echo $emblishment_print_type[$dataArray[0][csf('embel_type')]];
					elseif ($dataArray[0][csf('embel_name')]==2) echo $emblishment_embroy_type[$dataArray[0][csf('embel_type')]];
					elseif ($dataArray[0][csf('embel_name')]==3) echo $emblishment_wash_type[$dataArray[0][csf('embel_type')]]; 
					elseif ($dataArray[0][csf('embel_name')]==4) echo $emblishment_spwork_type[$dataArray[0][csf('embel_type')]];
				 ?>
				</td>
			</tr>
			<tr>
				<td><strong>Emb. Source:</strong></td><td><? echo $knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
				<td><strong>Emb. Company:</strong></td><td>
					<? 
						if($dataArray[0][csf('production_source')]==1) echo $company_library[$dataArray[0][csf('serving_company')]];
						else										   echo $supplier_library[$dataArray[0][csf('serving_company')]];
					 
					?>
				</td>
				<td><strong>Location:</strong></td><td><? echo $location_library[$dataArray[0][csf('location_id')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Floor :</strong></td><td><? echo $floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
				<td><strong>Organic :</strong></td><td><? echo $dataArray[0][csf('organic')]; ?></td>
				<td><strong>Delivery Date :</strong></td><td><? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
			</tr>
			<tr>
            	<td  colspan="2" id="barcode_img_id"></td>
				<td width="126"><strong>Working Company</strong></td><td>: <? echo $company_library[$dataArray[0][csf('working_company_id')]]; ?></td>
				<td><strong>Body Part</strong></td><td>: <? echo $body_part_arr[$dataArray[0][csf('body_part')]]; ?></td>
			</tr>
			
		</table><br>
			

        <?
			$delivery_mst_id =$dataArray[0][csf('id')];

				$sql="SELECT b.cut_no, b.production_qnty, a.country_id, d.color_number_id, d.size_number_id, e.style_ref_no, e.style_description, e.job_no, e.buyer_name,f.po_number,f.id as po_id, f.po_quantity,h.order_cut_no,b.bundle_no
				from 
					pro_garments_production_mst a, 
					pro_garments_production_dtls b,
					wo_po_color_size_breakdown d, 
					wo_po_details_master e, 
					wo_po_break_down f,
					ppl_cut_lay_mst g,
					ppl_cut_lay_dtls h,
					ppl_cut_lay_bundle i
				where 
					a.delivery_mst_id ='$data[1]' 
					and e.job_no=f.job_no_mst 
					and f.id=a.po_break_down_id 
					and a.id=b.mst_id 
					and b.color_size_break_down_id=d.id 
					and b.status_active=1 
					and b.is_deleted=0 
					and d.status_active in(1,2,3) 
					and d.is_deleted=0 
					and g.id=h.mst_id
					and i.mst_id=g.id
					and i.dtls_id=h.id
					and i.bundle_no=b.bundle_no
					and g.cutting_no=b.cut_no
					and h.color_id=d.color_number_id
					and g.status_active=1 and g.is_deleted=0 and h.status_active=1 and h.is_deleted=0
				order by e.job_no,d.size_order";
				  //echo $sql;
				
				$result=sql_select($sql);
				
				foreach($result as $rows)
				{
					//$key=$rows[csf('country_id')].$rows[csf('buyer_name')].$rows[csf('job_no')].$rows[csf('po_id')].$rows[csf('color_number_id')].$rows[csf('style_ref_no')].$rows[csf('style_description')].$rows[csf('cut_no')].$rows[csf('order_cut_no')];
					
					$key=$rows[csf('country_id')].$rows[csf('buyer_name')].$rows[csf('job_no')].$rows[csf('color_number_id')].$rows[csf('style_ref_no')].$rows[csf('style_description')].$rows[csf('cut_no')];
					
					$bundle_size_arr[$rows[csf('size_number_id')]]=$rows[csf('size_number_id')];
					$dataArr[$key]=array(
						country_id=>$rows[csf('country_id')],
						buyer_name=>$rows[csf('buyer_name')],
						po_id=>$rows[csf('po_id')],
						po_number=>$rows[csf('po_number')],
						color_number_id=>$rows[csf('color_number_id')],
						size_number_id=>$rows[csf('size_number_id')],
						style_ref_no=>$rows[csf('style_ref_no')],
						style_description=>$rows[csf('style_description')],
						job_no=>$rows[csf('job_no')],
						cut_no=>$rows[csf('cut_no')],
						order_cut_no=>$rows[csf('order_cut_no')]
					);
					$orderCutArr[$key][$rows[csf('order_cut_no')]]=$rows[csf('order_cut_no')];
					$productionQtyArr[$key]+=$rows[csf('production_qnty')];
					$sizeQtyArr[$key][$rows[csf('size_number_id')]]+=$rows[csf('production_qnty')];
					$bundleArr[$key][$rows[csf('bundle_no')]]=$rows[csf('bundle_no')];
					
				}



			unset($result); 
		?> 
         
    
    <div style="width:100%;">
    <table cellspacing="0" width="900" border="1" rules="all" class="rpt_table" style="font: 12px tahoma;">
    
    <tr bgcolor="#dddddd" align="center">
            <th colspan="9"></th>
            <th  colspan="<? echo count($bundle_size_arr); ?>" align="center">Size</th>
            <th align="center" rowspan="2"  >Total Rcv. Qty </th>
            <th align="center" rowspan="2">No of Bundle</th>
            <th width="100" rowspan="2" align="center">Remarks </th>
        </tr>
        
        <tr bgcolor="#dddddd" align="center">
            <th width="40">SL No</th>
            <th width="80" align="center">Buyer</th>
            <th width="80" align="center">Job No</th>
            <th width="80" align="center">Style Ref</th>
            <th width="100" align="center">Style Des</th>
            <th width="80" align="center">Country</th>
            <th width="80" align="center">Color</th>
            <th width="80" align="center">Cutting No</th>
            <th width="80" align="center">Order Cut</th>
                <?
                $i=0;
                foreach($bundle_size_arr as $inf)
                {
                ?>
                <th align="center" width="50"><? echo $size_library[$inf]; ?></th>
                <?	
                }
                ?>
        </tr>
        <tbody>
			<?
            $i=1;
            $tot_qnty=array();
			foreach($dataArr as $key=>$row)
			{
                $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF"; 
				?>
				<tr bgcolor="<? echo $bgcolor; ?>"  style="font-size:12px">
					<td align="center"><? echo $i;  ?></td>
					<td align="center"><p><? echo $buyer_arr[$row[buyer_name]]; ?></p></td>
					<td align="center"><p><? echo $row['job_no']; ?></p></td>
					<td align="center"><p><? echo $row['style_ref_no']; ?></p></td>
					<td align="center"><p><? echo $row['style_description']; ?></p></td>
					<td align="center"><p><? echo $country_library[$row[country_id]]; ?></p></td>
					<td align="center"><p><? echo $color_library[$row[color_number_id]]; ?></p></td>
					<td align="center"><p><? echo $row['cut_no']; ?></td>
					<td align="center"><p><? echo implode(',',$orderCutArr[$key]); ?></p></td>
                        <?
                        foreach($bundle_size_arr as $size_id)
						{
							$size_qty=0;
							$size_qty=$sizeQtyArr[$key][$size_id];
							?>
							<td align="center" width="50"><? echo $size_qty; ?></td>
							<?	
							$grand_total_size_arr[$size_id]+=$size_qty;
						}
						?>
                    <td align="center"><? echo $productionQtyArr[$key]; ?></td>
                    <td align="center"><? echo count($bundleArr[$key]); ?></td>
					<? 
					$color_qty_arr[$color] += $cdata['val'];
					$color_wise_bundle_no_arr[$color] += $cdata['count'];
					?>
					<td align="right"> </td>
					
				</tr>
				<?
					$grand_total_qty+=$productionQtyArr[$key];
					$grand_total_bundle_num+=count($bundleArr[$key]);
					$grand_total_reject_qty+=$val['reject_qty'];
				$i++;
			 }
					
                ?>
        </tbody>
        <tr>
            <td colspan="9" align="right"><strong>Grand Total </strong></td>
				<?
                foreach($bundle_size_arr as $size_id)
                {
                    ?>
                    <td align="center" width="50"><? echo $grand_total_size_arr[$size_id]; ?></td>
                    <?	
                }
                ?>
            <td align="center">  <? echo $grand_total_qty;  ?></td>
            <td align="center"><?  echo $grand_total_bundle_num; ?></td>
        </tr>                           
    </table> 


 <br><br>

 <table cellspacing="0" rules="all" style="font: 12px tahoma; margin-left:110px;" >
            <tr >
            
                <td width="90" style="border:1px solid white;"><strong>Transport No</strong></td>    <td width="200px" style="border:1px solid white;">: <?  ?></td>
                <td width="80" style="border:1px solid white;"><strong>Driver Name</strong></td>    <td width="190px" style="border:1px solid white;"> : <? ?></td>
                <td width="55" style="border:1px solid white;"><strong>D/L No</strong></td>         <td width="155px" style="border:1px solid white;">: <? ?> </td>
            </tr>
            </table>
            <br><br>
            
		 <?
            echo signature_table(26, $data[0], "900px");
         ?>
         <br>

        
        
        
        
        <br>

		</div>
		<script type="text/javascript" src="../../js/jquery.js"></script>
		<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode( valuess ){
				var value = valuess;//$("#barcodeValue").val();
				var btype = 'code39';//$("input[name=btype]:checked").val();
				var renderer ='bmp';// $("input[name=renderer]:checked").val();
				var settings = {
				  output:renderer,
				  bgColor: '#FFFFFF',
				  color: '#000000',
				  barWidth: 1,
				  barHeight: 30,
				  moduleSize:5,
				  posX: 10,
				  posY: 20,
				  addQuietZone: 1
				};
			
				 value = {code:value, rect: false};
				$("#barcode_img_id").show().barcode(value, btype, settings);
			} 
			generateBarcode('<? echo $dataArray[0][csf('sys_number')]; ?>');
		 </script>
	<?
	exit();	
	
}

?>
