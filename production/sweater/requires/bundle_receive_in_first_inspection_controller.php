<?
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_location")
{
	$data=explode("_",$data);
	if($data[1]==1) { $dropdown_name="cbo_location"; $load_fnc=""; }
	else { $dropdown_name="cbo_working_location";  $load_fnc="load_drop_down('requires/bundle_receive_in_first_inspection_controller', this.value, 'load_drop_down_floor', 'floor_td' );"; }
	
	echo create_drop_down( $dropdown_name, 110, "select id, location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, $load_fnc );	
	exit();
}

if ($action=="load_drop_down_working_com")
{
	$data=explode("_",$data);

	if($data[1]==1) $load_function="fnc_load_party(2)";
	else $load_function="";
	
	if($data[1]==1)
	{
		echo create_drop_down( "cbo_working_company", 110, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $data[2], "$load_function");
	}
	else
	{
		echo create_drop_down( "cbo_working_company", 110, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $data[2], "" );
	}	
	exit();	 
} 

if ($action == "load_drop_down_floor") 
{
    echo create_drop_down("cbo_floor", 110, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and production_process=5 order by floor_name", "id,floor_name", 1, "-- Select Floor --", $selected, "load_drop_down( 'requires/bundle_receive_in_first_inspection_controller', $('#cbo_working_company').val()+'_'+$('#cbo_working_location').val()+'_'+this.value+'_'+$('#txt_issue_date').val(), 'load_drop_down_line', 'line_td' );", 0);
    exit();
}

if ($action == "load_drop_down_line")
{
    list($wcompany_id, $wlocation, $floor,$input_date) = explode("_", $data);

    $nameArray = sql_select("select id, auto_update from variable_settings_production where company_name='$wcompany_id' and variable_list=23 and status_active=1 and is_deleted=0");
    $prod_reso_allocation = $nameArray[0][csf('auto_update')];
    $cond = "";
    
    if ($prod_reso_allocation == 1)
	{
        $line_library = return_library_array("select id,line_name from lib_sewing_line", "id", "line_name");
        $line_array = array();
        if ($floor == 0 && $location != 0) $cond = " and a.location_id= $location";
        if ($floor != 0) $cond = " and a.floor_id= $floor";
		
        if($db_type==0) $input_date = date("Y-m-d",strtotime($input_date)); else $input_date = change_date_format(date("Y-m-d",strtotime($input_date)),'','',1);
        
        $cond.=" and b.pr_date='".$input_date."'";

        if ($db_type == 0) {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number,a.prod_resource_num  order by a.prod_resource_num asc, a.id asc");
        } else if ($db_type == 2 || $db_type == 1) {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number,a.prod_resource_num order by a.prod_resource_num asc, a.id asc");
        }
        
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
        ksort($new_arr);
        foreach($new_arr as $key=>$v)
        {
            $line_array_new[$v]=$line_array[$v];
        }
        echo create_drop_down( "cbo_line_no", 110,$line_array_new,"", 1, "--Select Line--", $selected, "",0,0 );
        
    } else {
        if ($floor == 0 && $location != 0) $cond = " and location_name= $location";
        if ($floor != 0) $cond = " and floor_name= $floor"; else  $cond = " and floor_name like('%%')";

        echo create_drop_down("cbo_line_no", 110, "select id,line_name from lib_sewing_line where  is_deleted=0 and status_active=1 and floor_name!=0 $cond order by line_name", "id,line_name", 1, "--Select Line--", $selected, "", 0, 0);
    }
    exit();
}

if ($action == "populate_bundle_data") 
{
    $ex_data = explode("**", $data);
    $bundle = explode(",", $ex_data[0]);
    $mst_id = explode(",", $ex_data[2]);
    $bundle_nos = "'" . implode("','", $bundle) . "'";
    $vscan=$ex_data[4];
    $source_cond=$ex_data[5];
	
	/*if($vscan==0)
	{
		$mst_sql=sql_select("select a.sys_number, a.id from pro_gmts_delivery_mst a where a.sys_number='$ex_data[0]' and a.production_type=76 and a.status_active=1 and a.is_deleted=0  group by a.sys_number, a.id");
		//echo "select a.sys_number, a.id from pro_gmts_delivery_mst a where a.sys_number='$ex_data[0]' and a.production_type=76 and a.status_active=1 and a.is_deleted=0  group by a.sys_number, a.id";
		foreach ($mst_sql as $row)
        {           
            $ex_data[5]=$row[csf('id')];
        }
	}*/
	
	if($ex_data[5]!="")
	{
		$sql_bundle="select barcode_no from pro_garments_production_dtls where delivery_mst_id in ($ex_data[5]) and production_type=76 and status_active=1 and is_deleted=0";
	}
	else
	{
		$sql_bundle="select b.barcode_no from pro_gmts_delivery_mst a, pro_garments_production_dtls b where a.id=b.delivery_mst_id and a.sys_number='$ex_data[0]' and b.production_type=76 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.barcode_no";
	}
	$sql_bundle_res=sql_select($sql_bundle); $bundleNo=""; $bundle_count=0;
	foreach($sql_bundle_res as $row)
	{
		$bundle_count++;
		$bundleNo.="'".$row[csf("barcode_no")]."',";
	}
	unset($sql_bundle_res);
	
	$bundleNo=implode(",",array_filter(explode(",",$bundleNo)));
	$bundle_count=count(explode(",",$bundleNo)); $bundle_nosCond=""; $scnbundle_nos_cond=""; $cutbundle_nos_cond="";
	if($db_type==2 && $bundle_count>400)
	{
		$bundle_nosCond=" and (";
		$scnbundle_nos_cond=" and (";
		$cutbundle_nos_cond=" and (";
		$bundleArr=array_chunk(explode(",",$bundleNo),399);
		foreach($bundleArr as $bundleNos)
		{
			$bundleNos=implode(",",$bundleNos);
			$bundle_nosCond.=" c.barcode_no in($bundleNos) or ";
			$scnbundle_nos_cond.=" b.barcode_no in($bundleNos) or ";
			$cutbundle_nos_cond.=" barcode_no in($bundleNos) or ";
		}
		$bundle_nosCond=chop($bundle_nosCond,'or ');
		$bundle_nosCond.=")";
		
		$scnbundle_nos_cond=chop($scnbundle_nos_cond,'or ');
		$scnbundle_nos_cond.=")";
		
		$cutbundle_nos_cond=chop($cutbundle_nos_cond,'or ');
		$cutbundle_nos_cond.=")";
	}
	else
	{
		$bundle_nosCond=" and c.barcode_no in ($bundleNo)";
		$scnbundle_nos_cond=" and b.barcode_no in ($bundleNo)";
		$cutbundle_nos_cond=" and barcode_no in ($bundleNo)";
	}
	//echo "select b.bundle_no, b.bundle_no from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and b.production_type=52 and b.status_active=1 and b.is_deleted=0 $scnbundle_nos_cond";
	
	/*$rmg_no_sql="select barcode_no, number_start, number_end from ppl_cut_lay_bundle where status_active=1 and is_deleted=0 $cutbundle_nos_cond ";
	$rmg_no_arr=array();
	$rmg_no_sql_res = sql_select($rmg_no_sql);
	foreach($rmg_no_sql_res as $row)
	{
		$rmg_no_arr[$row[csf('barcode_no')]]['from']=$row[csf('number_start')];
		$rmg_no_arr[$row[csf('barcode_no')]]['to']=$row[csf('number_end')];
	}
	unset($rmg_no_sql_res);*/
	
	$knitComFloorArr=array();
	$sql_knit="select c.barcode_no, a.serving_company, a.floor_id from pro_garments_production_mst a, pro_garments_production_dtls c where a.id=c.mst_id and a.production_type=51 and c.production_type=51 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $bundle_nos_cond";
	$sql_knit_res=sql_select($sql_knit);
	
	foreach($sql_knit_res as $row)
	{
		$knitComFloorArr[$row[csf('barcode_no')]]['kcomp']=$row[csf('serving_company')];
		$knitComFloorArr[$row[csf('barcode_no')]]['kfloor']=$row[csf('floor_id')];
	}
	
    $scanned_bundle_arr = return_library_array("select b.bundle_no, b.bundle_no from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and b.production_type=77 and b.status_active=1 and b.is_deleted=0 $scnbundle_nos_cond", 'bundle_no', 'bundle_no');
    $size_arr = return_library_array("select id, size_name from lib_size", 'id', 'size_name');
    $color_arr = return_library_array("select id, color_name from lib_color", "id", "color_name");
    $country_arr = return_library_array("select id, country_name from lib_country", 'id', 'country_name');
    $buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	$floorArr = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name');
	$company_short_arr = return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');
    
    $year_field = "";
    if ($db_type == 0) $year_field = "YEAR(f.insert_date)"; else if ($db_type == 2) $year_field = "to_char(f.insert_date,'YYYY')";
     
    $last_operation=array();
    //$last_operation=gmt_production_validation_script( 4, 1);

    $cutting_no=return_field_value("cut_no", "pro_garments_production_dtls", "1=1 $cutbundle_nos_cond");
    //$cutting_no =trim($company_short_arr[$company].'-'.$syear.'-'.str_pad($cut_no,6,"0",STR_PAD_LEFT));
	 
	$sql="SELECT a.serving_company, a.floor_id, max(c.id) as prdid, d.id as colorsizeid, e.id as po_id, f.job_no_prefix_num, MAX($year_field) as year, f.buyer_name, f.style_ref_no, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.cut_no, c.bundle_no, c.barcode_no, sum(c.production_qnty) as production_qnty, e.po_number,c.barcode_no from pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f where f.company_name='$ex_data[3]' and a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_no_mst=f.job_no and a.production_type=76 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and e.status_active in(1,2,3) and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $bundle_nosCond group by a.serving_company, a.floor_id, d.id, e.id, f.job_no_prefix_num, f.buyer_name, f.style_ref_no, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id,c.cut_no, c.bundle_no, c.barcode_no, e.po_number order by c.cut_no, length(c.bundle_no) asc, c.bundle_no asc";
	// echo $sql;// die;
	$result = sql_select($sql);
	$count=count($result);
	$i=$ex_data[1]+$count;
	foreach ($result as $row)
	{ 
		//echo $scanned_bundle_arr[$row[csf('bundle_no')]];
		if(trim($scanned_bundle_arr[$row[csf('bundle_no')]])=="")
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$qty = ($row[csf('production_qnty')] ) - ($reject_qty[$row[csf('bundle_no')]] + $alter_qty[$row[csf('bundle_no')]]+ $spt_qty[$row[csf('bundle_no')]]);
			?>
			<tr bgcolor="<?=$bgcolor; ?>" id="tr_<?=$i;?>"> 
				<td width="30" align="center"><?=$i; ?></td>
				<td width="80" id="bundle_<?=$i; ?>" title="<?=$row[csf('barcode_no')]; ?>"><?=$row[csf('bundle_no')]; ?></td>
				<td width="90" id="barcode_<?=$i; ?>" title="<?=$row[csf('barcode_no')]; ?>"><?=$row[csf('barcode_no')]; ?></td>
                
                <td width="100" style="word-break:break-all;" align="left"><?=$color_arr[$row[csf('color_number_id')]]; ?></td>
				<td width="60" style="word-break:break-all;" align="center">&nbsp;<?=$size_arr[$row[csf('size_number_id')]]; ?></td>
				<td width="65" id="prodQty_<?=$i; ?>" align="right"><?=$qty; ?>&nbsp;</td>
                <td width="70" align="center"><?=$company_short_arr[$knitComFloorArr[$row[csf('barcode_no')]]['kcomp']]; ?></td>
				<td width="90" align="center"><?=$floorArr[$knitComFloorArr[$row[csf('barcode_no')]]['kfloor']]; ?></td>
                
                <td width="50" align="center"><?=$row[csf('year')]; ?></td>
				<td width="60" align="center"><?=$row[csf('job_no_prefix_num')]; ?></td>
                <td width="65" style="word-break:break-all;" align="left"><?=$buyer_arr[$row[csf('buyer_name')]]; ?></td>
				<td width="90" style="word-break:break-all;" align="left"><?=$row[csf('style_ref_no')]; ?></td>
				<td width="90" style="word-break:break-all;" align="left"><?=$row[csf('po_number')]; ?></td>
				<td width="100" style="word-break:break-all;" align="left"><?=$garments_item[$row[csf('item_number_id')]]; ?></td>
				<td width="80" style="word-break:break-all;" align="left"><?=$country_arr[$row[csf('country_id')]]; ?></td>
				
				<td id="button_1" align="center">
					<input type="button" id="decrease_<?=$i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<?=$i; ?>);" />
					
					<input type="hidden" name="txtcutNo[]" id="txtcutNo_<?=$i; ?>" value="<?=$row[csf('cut_no')]; ?>"/>
					<input type="hidden" name="txtcolorSizeId[]" id="txtcolorSizeId_<?=$i; ?>" value="<?=$row[csf('colorsizeid')]; ?>"/>
					<input type="hidden" name="txtorderId[]" id="txtorderId_<?=$i; ?>" value="<?=$row[csf('po_id')]; ?>"/>
					<input type="hidden" name="txtgmtsitemId[]" id="txtgmtsitemId_<?=$i; ?>" value="<?=$row[csf('item_number_id')]; ?>"/>
					<input type="hidden" name="txtcountryId[]" id="txtcountryId_<?=$i; ?>" value="<?=$row[csf('country_id')]; ?>"/>
					<input type="hidden" name="txtcolorId[]" id="txtcolorId_<?=$i; ?>" value="<?=$row[csf('color_number_id')]; ?>"/>
					<input type="hidden" name="txtsizeId[]" id="txtsizeId_<?=$i; ?>" value="<?=$row[csf('size_number_id')]; ?>"/>
					<input type="hidden" name="txtqty[]" id="txtqty_<?=$i; ?>" value="<?=$qty; ?>"/>
					<input type="hidden" name="dtlsId[]" id="dtlsId_<?=$i; ?>" value="<?=$row[csf('prdid')]; ?>"/> 
				</td>
			</tr>
			<?
			$i--;
		}
	}
    exit(); 
}

if ($action == "challan_duplicate_check") 
{
    $data=explode("__",$data);
    $result=sql_select("select a.sys_number, b.bundle_no from pro_gmts_delivery_mst a, pro_garments_production_dtls b where a.id=b.delivery_mst_id and b.barcode_no='$data[0]' and b.production_type=77 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.sys_number, b.bundle_no");
    foreach ($result as $row) 
    {
       echo "Bundle No " . $row[csf('bundle_no')] . " Found in Challan No " . $row[csf('sys_number')] . ".";
      //echo "2_".$row[csf('bundle_no')]."**".$row[csf('sys_number')];
      // die;
    }
    exit();
}

if($action=="populate_mst_data")
{
	$data=explode("_",$data);
	$sql_pop="SELECT a.id, a.sys_number_prefix_num, a.sys_number, a.company_id, a.location_id, a.production_source, a.working_company_id, a.working_location_id, a.floor_id
    FROM pro_gmts_delivery_mst a, pro_garments_production_dtls b
    where a.entry_form=376 and b.entry_form=376 and a.id=b.delivery_mst_id and b.barcode_no='$data[0]' and a.company_id='$data[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$sql_pop_res=sql_select($sql_pop);
	foreach($sql_pop_res as $row)
    {
		echo $row[csf('company_id')].'_'.$row[csf('production_source')];
	}
	exit();
}

if($action=="issue_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	list($shortName,$ryear,$ratio_prifix)=explode('-',$lot_ratio);
	if($ryear=="") 	$ryear=date("Y",time());
	else 			$ryear=("20$ryear")*1;
	//echo $ryear;die;
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
		
		function js_set_value(id) 
		{			
			$('#hidden_system_id').val( id );
			parent.emailwindow.hide();
		}
		
    </script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
		<form name="searchwofrm"  id="searchwofrm">
			<fieldset style="width:810px;">
			<legend></legend>           
	            <table cellpadding="0" cellspacing="0" width="550" border="1" rules="all" class="rpt_table">
	                <thead>
	                	<th width="140">Company</th>
	                    <th width="60">Lot Ratio Year</th>
	                    <th width="100">Job No</th>
	                    <th width="100" class="must_entry_caption">Ratio No</th>
	                    <th width="100">Enter Challan No</th>
                        <th width="100">QR Code</th>
	                    <th>
	                    	<input type="reset"name="reset"id="reset"value="Reset"style="width:100px"class="formbutton" />
	                        <input type="hidden"name="hidden_system_id"id="hidden_system_id"value="" />
	                    </th>
	                </thead>
	                <tr class="general">
	                	<td><? echo create_drop_down( "cbo_company_name", 140, "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name", "id,company_name", 1, "-- Select --", $company_id, "",0 ); ?></td>
	                    <td><? echo create_drop_down( "cbo_lot_year", 60, $year, '', "", '-- Select --', $ryear, "" ); ?></td> 				
	                    <td><input type="text"style="width:90px"class="text_boxes"name="txt_job_no"id="txt_job_no" /></td> 				 				
	                    <td><input type="text"name="txt_lot_no"id="txt_lot_no"style="width:90px"class="text_boxes"value="<?php if($ratio_prifix) echo $ratio_prifix*1; ?>" /></td>  		
	                    <td><input type="text"name="txt_challan_no"id="txt_challan_no"style="width:90px"class="text_boxes" /> </td> 
                        <td><input type="text" name="txt_qr_code" id="txt_qr_code" style="width:90px" class="text_boxes"/> </td> 		
	            		<td align="center">
	                     	<input type="button"name="button2"class="formbutton"value="Show"onClick="show_list_view (document.getElementById('cbo_company_name').value+'_'+ document.getElementById('txt_challan_no').value+'_'+ document.getElementById('txt_job_no').value+'_'+ document.getElementById('txt_lot_no').value+'_'+ document.getElementById('cbo_lot_year').value+'_'+ document.getElementById('txt_qr_code').value, 'create_issue_callan_search_list_view', 'search_div', 'bundle_receive_in_first_inspection_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"style="width:100px;" />
	                     </td>
	                </tr>
	           </table>
	           <div style="width:100%; margin-top:5px; margin-left:10px"id="search_div"align="left"> </div>
			</fieldset>
		</form>
	</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	
	</html>
	<?
	exit();
}

if($action=="create_issue_callan_search_list_view")
{
 	$ex_data 				= explode("_",$data);
	$company 				= $ex_data[0];
	$job_no					=$ex_data[2];
	$challan_no				=$ex_data[1];
	$lot_no					=$ex_data[3];
	$syear 					= substr($ex_data[4],2);
	$qrCode					=$ex_data[5];

	if(trim($ex_data[1]))	$challan_no = "".trim($ex_data[1])."";
	
	if( trim($ex_data[0])==0)
	{
		echo "<h2 style='color:#D00; text-align:center;'>Please Select Company First. </h2>";
		exit();
	}


	/*if( trim($ex_data[3])=='' && trim($ex_data[2])=='' && trim($ex_data[1])=='')
	{
		echo "<h2 style='color:#D00; text-align:center;'>Please Select Lot No or Job No or Challan No </h2>";
		exit();
	}*/
	
	if ($lot_no != '') $cutCon = " and c.cut_num_prefix_no=".$lot_no."";
		
	if($job_no!='') $jobCon=" and c.job_no like '%$job_no%' "; else $jobCon="";

	if($challan_no!='') $challanCon=" and a.sys_number_prefix_num like '%$challan_no%' "; else $challanCon="";
	if($qrCode!="") $qrCond=" and b.barcode_no='".trim($qrCode)."'";
	
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="630" class="rpt_table">
        
        <thead>
            <th width="40">SL</th>
            <th width="100">Job No</th>
            <th width="120">Issue No</th>
            <th width="110">Lot Ratio No</th>
            <th>Issue Qty.</th>
        </thead>
	</table>
	<div style=" width:650px; max-height:210px; overflow-y:scroll"id="list_container_batch"align="left">
        	 
        <table cellspacing="0"cellpadding="0"border="1"rules="all"width="630"class="rpt_table"id="tbl_list_search"> 
        	<?
			$i=1;
			$sql="  select a.sys_number, a.id, a.operator_id, a.body_part_ids, a.body_part_type, c.cutting_no, c.job_no, sum(b.production_qnty) as production_qty from pro_garments_production_dtls b, ppl_cut_lay_mst c, pro_gmts_delivery_mst a where a.id=b.delivery_mst_id $cutCon $jobCon $challanCon $qrCond and b.cut_no=c.cutting_no and a.production_type=76 and b.production_type=76 and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.is_deleted=0 group by a.sys_number, a.id, a.operator_id, a.body_part_ids, a.body_part_type, c.cutting_no, c.job_no order by a.id DESC";
			// echo $sql;
			$result = sql_select($sql);
			$challan_id_arr=array();
			foreach ($result as $val)
			{ 
				$challan_id_arr[$val[csf('id')]]=$val[csf('id')];
			}

			$receive_data_arr=return_library_array( "select id, issue_challan_id from pro_gmts_delivery_mst where issue_challan_id in (".implode(",", $challan_id_arr).") and status_active=1 and is_deleted=0 ", "issue_challan_id", "id"  );

			foreach ($result as $row)
			{  
				if(!$receive_data_arr[$row[csf('id')]])	
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
					?>
					<tr bgcolor="<? echo $bgcolor; ?>"style="text-decoration:none; cursor:pointer"id="search<? echo $i;?>"onClick="js_set_value('<? echo $row[csf('id')].'_'.$row[csf('sys_number')]; ?>')">
						<td width="40"><? echo $i; ?></td>
						<td width="100" align="center"><p><? echo $row[csf('job_no')]; ?></p></td>
						<td width="120" align="center"><p><? echo $row[csf('sys_number')]; ?></p></td>
						<td width="110"><p><? echo $row[csf('cutting_no')]; ?></p></td>
						<td align="right"><? echo $row[csf('production_qty')]; ?></td>
					</tr>
					<?
					$i++;
				}
			}
        	?>
        </table>
    </div>
	<?	
	exit();	
}


if ($action == "bundle_popup") 
{
    extract($_REQUEST);
    echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode);
    ?>
    <script>

        function check_all_data() {
            var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
            for (var i = 1; i <= tbl_row_count; i++) {
                if ($("#search" + i).css("display") != 'none') {
                    js_set_value(i);
                }
            }
        }
        var selected_id = new Array();

        function toggle(x, origColor) {
            var newColor = 'yellow';
            if (x.style) {
                x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
            }
        }

        function js_set_value(str) {
            toggle(document.getElementById('search' + str), '#FFFFCC');

            if (jQuery.inArray($('#txt_individual' + str).val(), selected_id) == -1) {
                selected_id.push($('#txt_individual' + str).val());

            }
            else {
                for (var i = 0; i < selected_id.length; i++) {
                    if (selected_id[i] == $('#txt_individual' + str).val()) break;
                }
                selected_id.splice(i, 1);
            }
            var id = '';
            for (var i = 0; i < selected_id.length; i++) {
                id += selected_id[i] + ',';
            }
            id = id.substr(0, id.length - 1);

            $('#hidden_bundle_nos').val(id);
        }

        function fnc_close() {
            document.getElementById('hidden_source_cond').value=document.getElementById('source_cond').value;
            parent.emailwindow.hide();
        }

        function reset_hide_field() {
            $('#hidden_bundle_nos').val('');
            selected_id = new Array();
        }

    </script>
    </head>
    <body>
    <div align="center" style="width:100%;">
        <form name="searchwofrm" id="searchwofrm">
            <fieldset style="width:810px;">
                <legend>Enter search words <input type="checkbox" value="1" name="is_exact" id="is_exact" >is exact</legend>
                <table cellpadding="0" cellspacing="0" width="550" border="1" rules="all" class="rpt_table">
                    <thead>
                        <th width="100">Bundle No</th>
                        <th width="100">QR Code No</th>
                        <th width="60">Job Year</th>
                        <th width="80">Job No</th>
                        <th width="100">Style</th>
                        <th width="100">Order No</th>
                        <th width="100" class="must_entry_caption">Lot Ratio No</th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:70px" class="formbutton"/>
                            <input type="hidden" name="hidden_bundle_nos" id="hidden_bundle_nos">
                            <input type="hidden" name="hidden_source_cond" id="hidden_source_cond"> 
                        </th>
                    </thead>
                    <tr class="general">
                    	<td><input type="text" name="txtbundle_no" id="txtbundle_no" style="width:90px" class="text_boxes"/> </td>
                        <td><input type="text" name="txt_qr_code" id="txt_qr_code" style="width:90px" class="text_boxes"/> </td>
                        <td><? echo create_drop_down( "cbo_job_year", 60, $year,'', "", '-- Select --',date("Y",time()), "",'','','','' ); ?></td>               
                        <td><input type="text" style="width:70px" class="text_boxes" name="txt_job_no" id="txt_job_no"/></td>
                        <td><input type="text" style="width:90px" class="text_boxes" name="txt_style_ref" id="txt_style_ref"/></td>
                        <td><input type="text" style="width:90px" class="text_boxes" name="txt_order_no" id="txt_order_no"/></td>
                        <td><input type="text" name="txt_lot_ratio_no" id="txt_lot_ratio_no" style="width:90px" class="text_boxes"/></td>
                        <td>
                        <input type="button" name="button2" class="formbutton" value="Show"
                        onClick="show_list_view(document.getElementById('txt_order_no').value+'_'+'<? echo $company_id; ?>'+'_'+document.getElementById('txtbundle_no').value+'_'+'<? echo $bundleNo; ?>'+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('cbo_job_year').value+'_'+document.getElementById('txt_style_ref').value+'_'+document.getElementById('txt_lot_ratio_no').value+'_'+document.getElementById('txt_qr_code').value+'_'+$('#is_exact').is(':checked'), 'create_bundle_search_list_view', 'search_div', 'bundle_receive_in_first_inspection_controller', 'setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();');" style="width:100px;"/>
                        </td>
                    </tr>
                </table>
                <div style="width:100%; margin-top:5px; margin-left:10px" id="search_div" align="left"></div>
            </fieldset>
        </form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if ($action == "create_bundle_search_list_view") 
{
    $ex_data = explode("_", $data);
    $txt_order_no = "%" . trim($ex_data[0]) . "%";
    $company = $ex_data[1];
    //$bundle_no = "%".trim($ex_data[2])."%";
    if (trim($ex_data[2])) $bundle_no= "".trim($ex_data[2]).""; else $bundle_no="%".trim($ex_data[2])."%";

    $selectedBuldle = $ex_data[3];
    $job_no = $ex_data[4];
    $order_no =str_replace("'","", $ex_data[0]);
    $bndl_no =str_replace("'","", $ex_data[2]);
    $cut_no = $ex_data[7];
    $jyear = $ex_data[5]; 
	$style = $ex_data[6];
	$qr_code = $ex_data[8];
    $is_exact=$ex_data[9];
    
    $company_short_arr = return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');
    $cutConvertToInt = convertToInt('c.cut_no', array($company_short_arr[$company], '-'), 'cut_no');
    $bundleConvertToInt = convertToInt('c.bundle_no', array($company_short_arr[$company], '-', "/"), 'order_bundle_no');

    $size_arr = return_library_array("select id, size_name from lib_size", 'id', 'size_name');
    $color_arr = return_library_array("select id, color_name from lib_color", "id", "color_name");
    $country_arr = return_library_array("select id, country_name from lib_country", 'id', 'country_name');

   // $scanned_bundle_arr = return_library_array("select b.bundle_no, b.bundle_no from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and b.production_type=55 and  b.status_active=1 and b.is_deleted=0", 'bundle_no', 'bundle_no');
    $cutting_no =trim($company_short_arr[$company].'-'.$syear.'-'.str_pad($cut_no,6,"0",STR_PAD_LEFT));
    $where_con = '';
    if($ex_data[2]) $where_con .= " and c.bundle_no like '%" . trim($ex_data[2]) . "'";
	if($qr_code!="") $qrCond= " and c.barcode_n='".trim($qr_code)."'";
	
	$bndlCon=""; $cutCon=""; $cutCon_a=""; $jobCon=""; $styleCond=""; $orderCon="";
	if($is_exact=='true')
	{
		if($ex_data[0]!="") $orderCon= "and e.po_number='".trim($ex_data[0])."'";
		if($cut_no!= '')
		{
			$cutCon=" and c.cut_no = '$cut_no'";
            $cutCon_a=" and b.cut_no = '$cut_no'";
		}
		if($job_no!='') $jobCon=" and f.job_no = '$job_no'";
		if($bndl_no!='') $bndlCon=" and c.bundle_no = '$bndl_no'";
		if($style!='') $styleCond=" and f.style_ref_no = '$style'";
	}
	else
	{
		if($ex_data[0]!="") $orderCon= " and e.po_number like  '%" . trim($ex_data[0]) . "%'";
		if($cut_no!= '')
		{
			$cutCon=" and c.cut_no like '%".$cut_no."%'";
            $cutCon_a=" and b.cut_no like '%".$cut_no."%'";
		}
		if($job_no!='') $jobCon=" and f.job_no like '%$job_no%'";
		if($bndl_no!='') $bndlCon=" and c.bundle_no like '%$bndl_no%'";
		if($style!='') $styleCond=" and f.style_ref_no like '%$style%'";
	}
    $tmp_cut=trim($company_short_arr[$company].'-'.$syear.'-'.str_pad($cut_no,6,"0",STR_PAD_LEFT));
	
	$yearCond="";
	
	if($jyear!=0 && ($job_no!='' || $styleCond!='' || $ex_data[0]!=''))
	{
		if($db_type==0) $yearCond="YEAR(f.insert_date)=$jyear"; else if($db_type==2) $yearCond=" to_char(f.insert_date,'YYYY')=$jyear";
	}
    
    $scanned_bundle_arr = return_library_array("select bundle_no, bundle_no from pro_garments_production_dtls where production_type=77 and cut_no='".$tmp_cut."' and status_active=1 and is_deleted=0", 'bundle_no', 'bundle_no');
    foreach (explode(",", $selectedBuldle) as $bn) {
        $scanned_bundle_arr[$bn] = $bn;
    }

    $scanne=sql_select( "select b.bundle_no, sum(b.production_qnty) as production_qnty,a.sewing_line from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and b.production_type=77  and b.status_active=1 and b.is_deleted=0 $cutCon_a group by b.bundle_no,a.sewing_line");
    foreach($scanne as $row)
    {
        $duplicate_bundle[$row[csf("bundle_no")]] +=$row[csf("production_qnty")];
    }
    //print_r($scanned_bundle_arr);
    //$cutting_no=return_field_value("cut_no", "pro_garments_production_dtls", "barcode_no in ($bundle_nos)");
     
    // echo $cutting_no;
    $last_operation=gmt_production_validation_script( 77, 1,'', $cutting_no, $production_squence);
    //$last_operation=gmt_production_validation_script( 4, 1 );
    // print_r($last_operation);

    ?>
     <table cellspacing="0" cellpadding="0" border="1" rules="all" width="830" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="50">Job Year</th>
            <th width="50">Job No</th>
            <th width="90">Order No</th>
            <th width="130">Gmts Item</th>
            <th width="110">Country</th>
            <th width="100">Color</th>
            <th width="50">Size</th>
            <th width="70">Cut No</th>
            <th width="80">Bundle No</th>
            <th>Bundle Qty.</th>
        </thead>
    </table>
    <div style="width:850px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">    
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="830" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            $last_operation_string='';  
            //foreach($last_operation as  $item_id=>$operation_cond)
            foreach($last_operation as  $item_id=>$operation_cond)
            {
				//echo $operation_cond;
                if( $operation_cond!=0 ) $operation_conds=" and d.id in (".ltrim($operation_cond,",").") ";
                else
                {
                    $sqld = sql_select( "SELECT c.bundle_no, SUM(c.reject_qty) as reject_qty, SUM(c.alter_qty) as alter_qty, SUM(c.spot_qty) as spot_qty, SUM(c.replace_qty) as replace_qty  from pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f where d.job_no_mst=f.job_no and a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and a.company_id=$company  and c.production_type in (76)  
					$qrCond $orderCon $bndlCon $yearCond $jobCon $cutCon $styleCond 
					and a.status_active=1 and a.is_deleted=0 group by  c.bundle_no order by  length(c.bundle_no) asc, c.bundle_no asc" );
                    foreach($sqld as $arows)
                    {
                        $reject_qty[$arows[csf('bundle_no')]]+=$arows[csf('reject_qty')];
                        $alter_qty[$arows[csf('bundle_no')]]+=$arows[csf('alter_qty')];
                        $spt_qty[$arows[csf('bundle_no')]]+=$arows[csf('spot_qty')];
                        $replace_qty[$arows[csf('bundle_no')]]+=$arows[csf('replace_qty')];
                    }
                }
                
                //echo $last_operation_string;
                $sql="SELECT c.cut_no, d.po_break_down_id, d.job_no_mst, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.bundle_no, sum(c.production_qnty) as qty, sum(c.replace_qty) as replace_qty, e.po_number, c.barcode_no from pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f where d.job_no_mst=f.job_no and a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and a.company_id=$company 
				$qrCond $orderCon $bndlCon $yearCond $jobCon $cutCon $styleCond
				and a.status_active=1 and a.is_deleted=0 and a.production_type=76 group by c.cut_no, c.bundle_no, d.po_break_down_id, d.job_no_mst, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.bundle_no, e.po_number,c.barcode_no order by c.cut_no, length(c.bundle_no) asc, c.bundle_no asc";
                
                //echo $sql;
                $result = sql_select($sql); 
                foreach ($result as $row)
                {  
                	// echo $row[csf('qty')]."=".$row[csf('bundle_no')]."*";  -$row[csf('replace_qty')]
                    $row[csf('qty')] = (($row[csf('qty')]) ) - ($reject_qty[$row[csf('bundle_no')]] + $alter_qty[$row[csf('bundle_no')]]+ $spt_qty[$row[csf('bundle_no')]]);
                    $balance_qnty=$row[csf('qty')]-$duplicate_bundle[$row[csf("bundle_no")]];
                    if($scanned_bundle_arr[$row[csf('bundle_no')]]=="" && $row[csf('qty')]>0 && $balance_qnty>0)
                    {
                        if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        list($shortName,$year,$job)=explode('-',$row[csf('job_no_mst')]);   
                    ?>
                        <tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i;?>" onClick="js_set_value(<?=$i; ?>)"> 
                            <td width="40"><?=$i; ?>
                                 <input type="hidden" name="txt_individual" id="txt_individual<?=$i; ?>" value="<?=$row[csf('barcode_no')]; ?>"/>
                            </td>
                            <td width="50" align="center" style="word-break:break-all"><?=$year; ?></td>
                            <td width="50" align="center" style="word-break:break-all"><?=$job*1; ?></td>
                            <td width="90" style="word-break:break-all"><?=$row[csf('po_number')]; ?></td>
                            <td width="130" style="word-break:break-all"><?=$garments_item[$row[csf('item_number_id')]]; ?></td>
                            <td width="110" style="word-break:break-all"><?=$country_arr[$row[csf('country_id')]]; ?></td>
                            <td width="100" style="word-break:break-all"><?=$color_arr[$row[csf('color_number_id')]]; ?></td>
                            <td width="50" align="center" style="word-break:break-all"><?=$size_arr[$row[csf('size_number_id')]]; ?></td>
                            <td width="70" style="word-break:break-all"><?=$row[csf('cut_no')]; ?></td>
                            <td width="80" style="word-break:break-all"><?=$row[csf('bundle_no')]; ?></td>
                            <td align="center"><?=$row[csf('qty')]; ?>&nbsp;</td>
                        </tr>
                        <?
                        $i++;
                    }
                }
            }
            if(empty($last_operation))
            {
                die;
                $sql="SELECT c.cut_no,d.po_break_down_id, d.job_no_mst, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.bundle_no, sum(c.production_qnty) as qty, e.po_number,c.barcode_no from pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e,wo_po_details_master f where d.job_no_mst=f.job_no and a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and a.company_id=$company and e.po_number like '$txt_order_no' and c.bundle_no like '$bundle_no' and  c.production_type=1 $jobCon $cutCon  and a.status_active=1 and a.is_deleted=0 group by c.cut_no, c.bundle_no, d.po_break_down_id, d.job_no_mst, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.bundle_no, e.po_number,c.barcode_no order by c.cut_no, length(c.bundle_no) asc, c.bundle_no asc";
                
                //order by c.cut_no, c.bundle_no DESC
                $last_operation_string='';
                $result = sql_select($sql); 
                foreach ($result as $row)
                {  
                    if($scanned_bundle_arr[$row[csf('bundle_no')]]=="")
                    {
                        if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        list($shortName,$year,$job)=explode('-',$row[csf('job_no_mst')]);   
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)"> 
                            <td width="40"><? echo $i; ?>
                                 <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
                            </td>
                            <td width="50" align="center"><p><? echo $year; ?></p></td>
                            <td width="50" align="center"><p><? echo $job*1; ?></p></td>
                            <td width="90"><p><? echo $row[csf('po_number')]; ?></p></td>
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
            }
            ?>
            <input type="hidden" name="source_cond"  value="<?php echo $last_operation_string; ?>" id="source_cond"  />
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

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if (str_replace("'", "", $txt_update_id) != "")
	{
		$rec_number=return_field_value("sys_number", "pro_gmts_delivery_mst"," issue_challan_id=$txt_update_id and status_active=1 and is_deleted=0 and production_type=77 and entry_form=376");
		if($iss_number){
			echo "insRec**".str_replace("'","",$txt_update_id)."**".$rec_number;
			die;
		}
	}
	
	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con= connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
			
		if($db_type==0) $year_cond="YEAR(insert_date)"; else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')"; else $year_cond="";	

		for($j=1;$j<=$tot_row;$j++)
        {   
            $bundleCheck="bundleNo_".$j;       
            $bundleCheckArr[$$bundleCheck]=$$bundleCheck;       
        }
        $bundle ="'".implode("','",$bundleCheckArr)."'";
        $receive_sql="select c.barcode_no, c.bundle_no from pro_garments_production_mst a, pro_garments_production_dtls c where a.id=c.mst_id and a.production_type=77 and c.bundle_no in ($bundle) and c.production_type=77 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 "; //and (c.is_rescan=0 or c.is_rescan is null)
        $receive_result = sql_select($receive_sql);
        foreach ($receive_result as $row)
        {           
            $duplicate_bundle[$row[csf('barcode_no')]]=$row[csf('bundle_no')];
        }
		
		if (str_replace("'", "", $txt_update_id) == "")
		{
			if ($db_type == 0) $year_cond = "YEAR(insert_date)"; else if ($db_type == 2) $year_cond="to_char(insert_date,'YYYY')"; else $year_cond = "";//defined Later
			
			$new_sys_number = explode("*", return_next_id_by_sequence("", "pro_gmts_delivery_mst",$con,1,$cbo_company_name,'BRFI',0,date("Y",time()),0,0,77,0,0 ));
			$field_array_delivery = "id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, issue_challan_id, production_type, production_source, delivery_date, challan_no, remarks, entry_form, inserted_by, insert_date, status_active, is_deleted";
			$mst_id = return_next_id_by_sequence(  "pro_gmts_delivery_mst_seq", "pro_gmts_delivery_mst", $con );
			$challanNo='';
			
			if(str_replace("'","",$txt_challan_no)=="") $challanNo=(int) $new_sys_number[2]; else $challanNo=str_replace("'","",$txt_challan_no);
			
			$data_array_delivery="(".$mst_id.",'".$new_sys_number[1]."','".(int)$new_sys_number[2]."','".$new_sys_number[0]."',".$cbo_company_name.",".$hidden_issue_id.",77,".$cbo_source.",".$txt_issue_date.",'".$challanNo."',".$txt_remarks.",376,".$user_id.",'".$pc_date_time."',1,0)";
			$challan_no =$challanNo;
		} 
		else 
		{
			$mst_id = str_replace("'", "", $txt_update_id);
			$txt_chal_no=explode("-",str_replace("'","",$txt_challan_no));
			
			if(str_replace("'","",$txt_challan_no)=="") $challanNo=(int) $txt_chal_no[3]; else $challanNo=str_replace("'","",$txt_challan_no);
			
			$field_array_delivery = "production_source*delivery_date*challan_no*remarks*updated_by*update_date";
			$data_array_delivery = "".$cbo_source."*".$txt_issue_date."*'".$challanNo."'*".$txt_remarks."*".$user_id."*'".$pc_date_time."'";
			$challan_no=str_replace("'","",$txt_challan_no);
		}
		//echo "10**";				
		$mstArr=array(); $dtlsArr=array(); $colorSizeArr=array(); $mstIdArr=array(); $cutNoArr=array(); 
		for($j=1;$j<=$tot_row;$j++)
		{ 	
			$bundleNo 		="bundleNo_".$j;
			$barcodeNo 		="barcodeNo_".$j;			
			$colorId 		="colorId_".$j;
			$sizeId		 	="sizeId_".$j;
			$colorSizeId	="colorSizeId_".$j;
			$qty	 		="qty_".$j;
			$orderId 		="orderId_".$j;
			$countryId	 	="countryId_".$j;
			$gmtsitemId 	="gmtsitemId_".$j;
			$txtcutNo		="txtcutNo_".$j;
			$dtlsId			="dtlsId_".$j;
			
			//echo $duplicate_bundle[$$bundleNo];
			if($duplicate_bundle[$$bundleNo]=='')
            {
				$mstArr[$$orderId][$$gmtsitemId][$$countryId]+=$$qty;
				$colorSizeArr[$$bundleNo] 					=$$orderId."**".$$gmtsitemId."**".$$countryId;
				$dtlsArr[$$bundleNo]			 			+=$$qty;
				$dtlsArrColorSize[$$bundleNo] 				=$$colorSizeId;
				$bundleRescanArr[$$bundleNo]				=0;
				$bundleBarcodeArr[$$bundleNo] 				=$$barcodeNo;
				$cutNoArr[$$bundleNo] 						=$$txtcutNo;
			}
		}
		//print_r($mstArr);
		$field_array_mst="id, delivery_mst_id, garments_nature, company_id, production_source, production_date, challan_no, remarks, po_break_down_id, item_number_id, country_id, production_quantity, production_type, entry_break_down_type, entry_form, inserted_by, insert_date, status_active, is_deleted";
		$data_array_mst="";		
		foreach($mstArr as $orderId=>$orderData)
		{
			foreach($orderData as $gmtsItemId=>$gmtsItemIdData)
			{
				foreach($gmtsItemIdData as $countryId=>$qty)
				{
					$id= return_next_id_by_sequence("pro_gar_production_mst_seq", "pro_garments_production_mst", $con );
					if($data_array_mst!="") $data_array_mst.=",";
					$data_array_mst.="(".$id.",".$mst_id.",".$garments_nature.",".$cbo_company_name.",".$cbo_source.",".$txt_issue_date.",'".$challan_no."',".$txt_remarks.",".$orderId.", ".$gmtsItemId.",".$countryId.",".$qty.",77,3,376,".$user_id.",'".$pc_date_time."',1,0)";
					$mstIdArr[$orderId][$gmtsItemId][$countryId]=$id;
				}
			}
		}
		
		$field_array_dtls ="id, delivery_mst_id, mst_id, production_type, color_size_break_down_id, production_qnty, cut_no, bundle_no, entry_form, barcode_no, status_active, is_deleted";
		
		$data_array_dtls="";
		foreach($dtlsArr as $bundle_no=>$qty)
		{
			$colorSizedData 		=explode("**",$colorSizeArr[$bundle_no]);
			$gmtsMstId 				=$mstIdArr[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[2]];
			if($data_array_dtls!="") $data_array_dtls.=",";

			$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );

			$data_array_dtls.= "(".$dtls_id.",".$mst_id.",".$gmtsMstId.",77,'".$dtlsArrColorSize[$bundle_no]."','".$qty."','".$cutNoArr[$bundle_no]."','".$bundle_no."',376,'".$bundleBarcodeArr[$bundle_no]."',1,0)"; 
		}
		$flag=1;
		if (str_replace("'", "", $txt_update_id) == "")
		{
			$rID_mst=sql_insert("pro_gmts_delivery_mst",$field_array_delivery,$data_array_delivery,1);
			if($rID_mst==1 && $flag==1) $flag=1; else $flag=0;
		}
		else
		{
			$rID_mst = sql_update("pro_gmts_delivery_mst", $field_array_delivery, $data_array_delivery, "id", $mst_id, 1);
			if($rID_mst==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		$rID=sql_insert("pro_garments_production_mst",$field_array_mst,$data_array_mst,1);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		
		$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array_dtls,$data_array_dtls,1);
		if($dtlsrID==1 && $flag==1) $flag=1; else $flag=0;
		
		//echo "10**insert into pro_garments_production_mst($field_array_mst)values".$data_array_mst;die;
		//$bundlerID=sql_insert("pro_cut_delivery_color_dtls",$field_array_bundle,$data_array_bundle,1);
		//echo "10**".$rID_mst."**".$rID."**".$dtlsrID."**".$flag;die;
	
		if($db_type==0)
		{  
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".$mst_id."**".str_replace("'","",$new_sys_number[0])."**".$challan_no;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==1 || $db_type==2 )
		{
			if($flag==1)
			{
				oci_commit($con); 
				echo "0**".$mst_id."**".str_replace("'","",$new_sys_number[0])."**".$challan_no;
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
  	else if ($operation==1) // Update Here End------------------------------------------------------
	{
 		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
	
		$mst_id = str_replace("'", "", $txt_update_id);
		$txt_chal_no=explode("-",str_replace("'","",$txt_challan_no));
		
		if(str_replace("'","",$txt_challan_no)=="") $challanNo=(int) $txt_chal_no[3]; else $challanNo=str_replace("'","",$txt_challan_no);
		
		$field_array_delivery = "production_source*delivery_date*challan_no*remarks*updated_by*update_date";
		$data_array_delivery = "".$cbo_source."*".$txt_issue_date."*'".$challanNo."'*".$txt_remarks."*".$user_id."*'".$pc_date_time."'";
		$challan_no=str_replace("'","",$txt_challan_no);
	
		for($j=1; $j<=$tot_row; $j++)
        {   
            $bundleCheck="bundleNo_".$j;       
            $bundleCheckArr[$$bundleCheck]=$$bundleCheck;       
        }
 
        $bundle="'".implode("','",$bundleCheckArr)."'";
        $receive_sql="select c.barcode_no, c.bundle_no from pro_garments_production_mst a, pro_garments_production_dtls c where a.id=c.mst_id and a.production_type=77 and c.bundle_no in ($bundle) and c.production_type=77 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.delivery_mst_id!=$mst_id and c.delivery_mst_id!=$mst_id)"; 
        $receive_result = sql_select($receive_sql);
        foreach ($receive_result as $row)
        {           
            $duplicate_bundle[$row[csf('bundle_no')]]=$row[csf('bundle_no')];
        }
		//$non_delete_arr=production_validation($mst_id,77);
		$issue_data_arr=production_data($mst_id,77);

		$mstArr=array(); $dtlsArr=array(); $colorSizeArr=array(); $mstIdArr=array(); $cutNoArr=array();  
		for($j=1;$j<=$tot_row;$j++)
		{ 	
			$bundleNo 		="bundleNo_".$j;
			$barcodeNo 		="barcodeNo_".$j;			
			$colorId 		="colorId_".$j;
			$sizeId		 	="sizeId_".$j;
			$colorSizeId	="colorSizeId_".$j;
			$qty	 		="qty_".$j;
			$orderId 		="orderId_".$j;
			$countryId	 	="countryId_".$j;
			$gmtsitemId 	="gmtsitemId_".$j;
			$txtcutNo		="txtcutNo_".$j;
			$dtlsId			="dtlsId_".$j;
			//echo $$qty;

			if($non_delete_arr[$$bundleNo]=="" && $duplicate_bundle[trim($$bundleNo)]=='')
			{
				$mstArr[$$orderId][$$gmtsitemId][$$countryId]+=$$qty;
				$colorSizeArr[$$bundleNo] 					=$$orderId."**".$$gmtsitemId."**".$$countryId;
				$dtlsArr[$$bundleNo]			 			+=$$qty;
				$dtlsArrColorSize[$$bundleNo] 				=$$colorSizeId;
				$bundleBarcodeArr[$$bundleNo] 				=$$barcodeNo;
				$cutNoArr[$$bundleNo] 						=$$txtcutNo;
			}
		}
		//print_r($dtlsArr);die;
		//Not Delete Data...............................start;
		foreach($non_delete_arr as $bi)
		{
			if($duplicate_bundle[trim($bi)]=='')
			{
				$bundleCutArr[$issue_data_arr[trim($bi)][csf('bundle_no')]]=$issue_data_arr[trim($bi)][csf('cut_no')];
				$bundleCutArr[$issue_data_arr[trim($bi)][csf('bundle_no')]]=$issue_data_arr[trim($bi)][csf('cut_no')];
				$cutArr[$issue_data_arr[trim($bi)][csf('po_break_down_id')]][$issue_data_arr[trim($bi)][csf('item_number_id')]][$issue_data_arr[trim($bi)][csf('country_id')]]=$issue_data_arr[trim($bi)][csf('cut_no')];
				$mstArr[$issue_data_arr[trim($bi)][csf('po_break_down_id')]][$issue_data_arr[trim($bi)][csf('item_number_id')]][$issue_data_arr[trim($bi)][csf('country_id')]]+=$issue_data_arr[trim($bi)][csf('production_qnty')];
				$colorSizeArr[$issue_data_arr[trim($bi)][csf('bundle_no')]]=$issue_data_arr[trim($bi)][csf('po_break_down_id')]."**".$issue_data_arr[trim($bi)][csf('item_number_id')]."**".$issue_data_arr[trim($bi)][csf('country_id')];
				
				$dtlsDataArr[$issue_data_arr[trim($bi)][csf('bundle_no')]]+=$issue_data_arr[trim($bi)][csf('production_qnty')];
				//$issue_data_arr[trim($bi)][csf('bundle_no')]
				$dtlsArrColorSize[$issue_data_arr[trim($bi)][csf('bundle_no')]]=$issue_data_arr[trim($bi)][csf('color_size_break_down_id')];
				$bundleBarcodeArr[$issue_data_arr[trim($bi)][csf('bundle_no')]]=$issue_data_arr[trim($bi)][csf('barcode_no')];
				$bundleBarcodeArr[$issue_data_arr[trim($bi)][csf('bundle_no')]]=$issue_data_arr[trim($bi)][csf('barcode_no')];
				$bundleRescanArr[$issue_data_arr[trim($bi)][csf('bundle_no')]]=$issue_data_arr[trim($bi)][csf('is_rescan')];
			}
		} 
		
		$field_array_mst="id, delivery_mst_id, garments_nature, company_id, production_source, production_date, challan_no, remarks, po_break_down_id, item_number_id, country_id, production_quantity, production_type, entry_break_down_type, entry_form, inserted_by, insert_date, status_active, is_deleted";
		$data_array_mst= "";
		foreach ($mstArr as $orderId => $orderData)
		{
			if($orderId)
			{
				foreach ($orderData as $gmtsItemId => $gmtsItemIdData)
				{
					foreach ($gmtsItemIdData as $countryId => $qty) 
					{
						$id= return_next_id_by_sequence(  "pro_gar_production_mst_seq",  "pro_garments_production_mst", $con );
						if ($data_array_mst != "") $data_array_mst .= ",";
						$data_array_mst .= "(" . $id . "," . $mst_id . ",".$garments_nature.",".$cbo_company_name.",".$cbo_source.",".$txt_issue_date.",'".$challan_no."',".$txt_remarks.",".$orderId.", ".$gmtsItemId.",".$countryId.",".$qty.",77,3,376,".$user_id.",'".$pc_date_time."',1,0)";
						$mstIdArr[$orderId][$gmtsItemId][$countryId]=$id;
					}
				}
			}
		}
		
		$field_array_dtls ="id, delivery_mst_id, mst_id, production_type, color_size_break_down_id, production_qnty, cut_no, bundle_no, entry_form, barcode_no, status_active, is_deleted";
		
		foreach ($dtlsArr as $bundle_no => $qty)
		{
			if($bundle_no)
			{
				$colorSizedData 		=explode("**",$colorSizeArr[$bundle_no]);
				$gmtsMstId 				=$mstIdArr[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[2]];
				if($data_array_dtls!="") $data_array_dtls.=",";
	
				$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );
	
				$data_array_dtls.= "(".$dtls_id.",".$mst_id.",".$gmtsMstId.",77,'".$dtlsArrColorSize[$bundle_no]."','".$qty."','".$cutNoArr[$bundle_no]."','".$bundle_no."',376,'".$bundleBarcodeArr[$bundle_no]."',1,0)"; 
			}
		} 
		$flag=1;
		$delete = execute_query("update pro_garments_production_mst set status_active=0,is_deleted=1,updated_by=$user_id,update_date='".$pc_date_time."' WHERE delivery_mst_id=$mst_id and production_type=77 and status_active=1 and is_deleted=0");
		if($delete==1 && $flag==1) $flag=1; else $flag=0;
		$delete_dtls = execute_query("update pro_garments_production_dtls set status_active=0, is_deleted=1 WHERE delivery_mst_id=$mst_id and production_type=77 and status_active=1 and is_deleted=0");
		if($delete_dtls==1 && $flag==1) $flag=1; else $flag=0;
	
		$rID_mst=sql_update("pro_gmts_delivery_mst",$field_array_delivery,$data_array_delivery,"id",$mst_id,1);
		if($rID_mst==1 && $flag==1) $flag=1; else $flag=0;
		
		$rID=sql_insert("pro_garments_production_mst",$field_array_mst,$data_array_mst,1);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		
		$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array_dtls,$data_array_dtls,1);
		if($dtlsrID==1 && $flag==1) $flag=1; else $flag=0;
		
		//echo "10**".bulk_update_sql_statement( "pro_gmts_knitting_issue_dtls", "id", $field_array_color_dtls, $data_array_color_dtls, $color_dtls_id_arr );die;	
		//echo "10**insert into pro_garments_production_dtls($field_array_dtls)values".$data_array_dtls;die;
		 //echo "10**".$delete .'=='. $delete_dtls .'=='. $rID_mst .'=='. $rID .'=='. $dtlsrID."==".$flag; oci_rollback($con);die;
		//echo "10**".$dtlsrID;oci_rollback($con);die;
		
		if($db_type==0)
		{  
			if($flag==1)
			{ 
				mysql_query("COMMIT");  
				echo "1**".$mst_id."**".str_replace("'","",$txt_system_no)."**".$challan_no;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==1 || $db_type==2 )
		{
			if($flag==1)
			{
				oci_commit($con); 
				echo "1**".$mst_id."**".str_replace("'","",$txt_system_no)."**".$challan_no;
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
	else if ($operation==2)  // Delete Here---------------------------------------------------------- 
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		$mst_id = str_replace("'", "", $txt_update_id);
		
		$flag=1;
		$rID = sql_delete("pro_gmts_delivery_mst","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'id ',$txt_mst_id,1);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		
		$delete = execute_query("update pro_garments_production_mst set status_active=0,is_deleted=1,updated_by=$user_id,update_date='".$pc_date_time."' WHERE delivery_mst_id=$mst_id and production_type=77 and status_active=1 and is_deleted=0");
		if($delete==1 && $flag==1) $flag=1; else $flag=0;
		$delete_dtls = execute_query("update pro_garments_production_dtls set status_active=0, is_deleted=1 WHERE delivery_mst_id=$mst_id and production_type=77 and status_active=1 and is_deleted=0");
		if($delete_dtls==1 && $flag==1) $flag=1; else $flag=0;
		
 		if($db_type==0)
		{
			if($flag==1)
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
			if($flag==1)
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

if($action=="system_number_popup")
{
  	echo load_html_head_contents("Bundle Receive In First Inspection Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value( strCon ) 
		{
			document.getElementById('hidd_str_data').value=strCon;
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center" style="width:100%; overflow-y:hidden;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
    <table width="850" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
        <thead>
            <tr>                	 
                <th width="130" class="must_entry_caption">Company name</th>
                <th width="80">Rec. No</th>
                <th width="60">Job Year</th>
                <th width="80">Job No</th>
                <th width="100">Style</th>
                <th width="100">Order No</th>
                <th width="130" colspan="2">Rec. Date Range</th>
                <th width="100">QR Code</th>
                <th>
                    <input name="hidd_str_data" id="hidd_str_data" class="text_boxes" style="width:70px" type="hidden"/>
                    <input type="reset" name="re_button" id="re_button" value="Reset" style="width:70px" class="formbutton"  />
                </th>           
            </tr>
        </thead>
        <tbody>
            <tr class="general">                    
                <td><? echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --",$company_id, ""); ?></td>
                <td><input name="txt_issue_no" id="txt_issue_no" class="text_boxes" style="width:70px"  placeholder="Write"/></td>
                <td><? echo create_drop_down( "cbo_job_year", 60, $year,'', "", '-- Select --',date("Y",time()), "",'','','','' ); ?></td>  
                <td><input name="txt_job_search" id="txt_job_search" class="text_boxes" style="width:70px"  placeholder="Write"/></td>
                <td><input name="txt_style_search" id="txt_style_search" class="text_boxes" style="width:90px" placeholder="Write" /></td>
                <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:90px" placeholder="Write" /></td>
                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" /></td>
                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date" /></td>
                <td><input type="text" name="txt_qr_code" id="txt_qr_code" style="width:90px" class="text_boxes"/> </td> 
                <td>
                <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_issue_no').value+'_'+document.getElementById('txt_job_search').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('cbo_job_year').value+'_'+document.getElementById('txt_style_search').value+'_'+document.getElementById('txt_qr_code').value, 'create_system_search_list_view', 'search_div', 'bundle_receive_in_first_inspection_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />				
                </td>
            </tr>
            <tr>                  
                 <td align="center" valign="middle" colspan="10"><? echo load_month_buttons(1); ?></td>
            </tr>   
        </tbody>
  	</table> 
    <div align="center" valign="top" id="search_div"> </div>  
    </form>
    </div>    
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_system_search_list_view")
{
    $ex_data = explode("_",$data);
	$company = $ex_data[0];	
	$issue_no = $ex_data[1];
	$job_no = $ex_data[2];
	$from_date = $ex_data[3];
	$to_date = $ex_data[4];
	$cut_year= $ex_data[5];
	$order_no= $ex_data[6];
	$jobyear= $ex_data[7];
	$style= $ex_data[8];
	$qrno= $ex_data[9];
	
	if(str_replace("'","",$company)==0) { echo "Please select company First"; die;}
    if($db_type==2)
	{ 
		$year_cond=" and extract(year from a.insert_date)=$cut_year"; 
		$year=" extract(year from a.insert_date) as year";
		$barcodeCond="rtrim(xmlagg(xmlelement(e,e.barcode_no,',').extract('//text()') order by e.barcode_no).GetClobVal(),',')";
		$jyearCond=" and to_char(c.insert_date,'YYYY')=$jobyear";
	}
    else  if($db_type==0)
	{ 
		$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$cut_year"; 
		$year=" SUBSTRING_INDEX(a.insert_date, '-', 1) as year";
		$barcodeCond="group_concat(e.barcode_no)";
		$jyearCond=" and YEAR(c.insert_date)=$jobyear";
	}
	
	if(str_replace("'","",$company)==0) $conpany_cond=""; else $conpany_cond="and a.company_id=".str_replace("'","",$company)."";
	if(str_replace("'","",$job_no)=="") $job_cond=""; else $job_cond="and c.job_no_prefix_num='".str_replace("'","",$job_no)."'";
	if(str_replace("'","",$system_no)=="") $system_cond=""; else $system_cond="and a.sys_number_prefix_num=".trim($issue_no)." $year_cond";
	if(str_replace("'","",$order_no)=="") $order_cond=""; else $order_cond=" and d.po_number='".str_replace("'","",$order_no)."'";
	if(str_replace("'","",$style)=="") $stylecond=""; else $stylecond=" and c.style_ref_no='".str_replace("'","",$style)."'";
	if($qrno!="") $qrCond= " and e.barcode_no='".trim($qrno)."'"; else $qrCond="";
	
	$company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
	$location_arr = return_library_array("select id,location_name from lib_location", 'id', 'location_name');
	$floor_arr = return_library_array("select id,floor_name from lib_prod_floor", 'id', 'floor_name');
	$issueSys_arr = return_library_array("select id,sys_number from pro_gmts_delivery_mst where entry_form=375 and status_active=1 and is_deleted=0", 'id', 'sys_number');
	
	if( $from_date!="" && $to_date!="" )
	{
		if($db_type==0)
		{
			$sql_cond= " and a.delivery_date between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
		}
		if($db_type==2)
		{
			$sql_cond= " and a.delivery_date between '".change_date_format($from_date,'yyyy-mm-dd','-',1)."' and '".change_date_format($to_date,'yyyy-mm-dd','-',1)."'";
		}
	}
	
	/*$sql_order="SELECT a.id, a.cutting_no, a.cut_qc_prefix_no,a.cutting_qc_no, a.table_no, a.job_no, a.cutting_qc_date,c.job_no_prefix_num,b.cut_num_prefix_no,$year, d.po_number
    FROM pro_gmts_cutting_qc_mst a, ppl_cut_lay_mst b, ppl_cut_lay_dtls e, wo_po_details_master c, wo_po_break_down d
    where a.garments_nature=100 and a.cutting_no=b.cutting_no and  b.job_no=c.job_no and c.job_no=d.job_no_mst $conpany_cond  $cut_cond $job_cond $sql_cond $order_cond $system_cond and a.status_active=1 and a.is_deleted=0 and b.id=e.mst_id  order by a.id";*/
	
	
	$sql_pop="SELECT a.id, a.sys_number_prefix_num, a.sys_number, a.company_id, a.location_id, a.production_source, a.delivery_date, a.challan_no, a.remarks, a.issue_challan_id, c.job_no, c.style_ref_no, $year, d.po_number
    FROM pro_gmts_delivery_mst a, pro_garments_production_mst b, wo_po_details_master c, wo_po_break_down d, pro_garments_production_dtls e
    where c.garments_nature=100 and a.entry_form=376 and b.entry_form=376 and a.id=b.delivery_mst_id and b.po_break_down_id=d.id and c.job_no=d.job_no_mst and b.id=e.mst_id $conpany_cond $job_cond $sql_cond $order_cond $system_cond $stylecond $jyearCond $qrCond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.status_active=1 and e.is_deleted=0 group by a.id, a.sys_number_prefix_num, a.sys_number, a.company_id, a.location_id, a.production_source, a.delivery_date, a.challan_no, a.remarks, a.issue_challan_id, c.job_no, c.style_ref_no, a.insert_date, d.po_number order by a.id DESC";
	//echo $sql_pop;
	
	$sql_pop_res=sql_select($sql_pop);
	?>
     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="650" >
        <thead>
            <th width="30">SL</th>
            <th width="60">Rec. Year</th>
            <th width="60">Rec. No</th>
            <th width="70">Rec. Date</th>
            <th width="110">Job No</th>
            <th width="110">Style Ref.</th>
            <th>Po No.</th>
        </thead>
    </table>
    <div style="width:650px; max-height:270px;overflow-y:scroll;" >	 
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="630" class="rpt_table" id="list_view">
        <tbody>
            <? 
            $i=1;
            foreach($sql_pop_res as $row)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                ?>
                <tr bgcolor="<?=$bgcolor; ?>" onClick="js_set_value('<?=$row[csf('id')].'_'.$row[csf('sys_number')].'_'.$row[csf('company_id')].'_'.$row[csf('production_source')].'_'.change_date_format($row[csf('delivery_date')]).'_'.$row[csf('challan_no')].'_'.$row[csf('remarks')].'_'.$row[csf('issue_challan_id')].'_'.$issueSys_arr[$row[csf('issue_challan_id')]]; ?>')" style="cursor:pointer" >
                    <td width="30" align="center"><?=$i; ?></td>
                    <td width="60" align="center"><?=$row[csf('year')]; ?></td>
                    <td width="60" align="center"><?=$row[csf('sys_number_prefix_num')]; ?></td>
                    <td width="70" style="text-align:center;"><?=change_date_format($row[csf('delivery_date')]); ?></td>
                    <td width="110" style="word-break:break-all"><?=$row[csf('job_no')]; ?></td>
                    <td width="110" style="word-break:break-all"><?=$row[csf('style_ref_no')]; ?></td>	
                    <td style="word-break:break-all"><?=$row[csf('po_number')]; ?></td>
                </tr>
				<? 
                $i++; 
            } 
            ?>
        </tbody>
    </table>
    </div>
	<?    
	exit();
}

if($action=="populate_bundle_data_update")
{
    $ex_data = explode("**",$data);
    $mst_id=$ex_data[0];
	$company_id=$ex_data[1];
	
	$sql_bundle="select barcode_no from pro_garments_production_dtls where delivery_mst_id='$mst_id' and production_type=77 and status_active=1 and is_deleted=0";
	$sql_bundle_res=sql_select($sql_bundle); $bundleNo=""; $bundle_count=0;
	foreach($sql_bundle_res as $row)
	{
		$bundle_count++;
		$bundleNo.="'".$row[csf("barcode_no")]."',";
	}
	unset($sql_bundle_res);
	
    $bundleNo=chop($bundleNo,',');
    $bundle_nos_cond=""; $cutbundle_nos_cond='';
    if($db_type==2 && $bundle_count>400)
    {
        $bundle_nos_cond=" and (";
		$cutbundle_nos_cond=" and (";
        $bundleArr=array_chunk(explode(",",trim($bundleNo)),399);
        foreach($bundleArr as $bundleNos)
        {
            $bundleNos=implode(",",$bundleNos);
            $bundle_nos_cond.=" c.barcode_no in($bundleNos) or ";
			$cutbundle_nos_cond.=" barcode_no in($bundleNos) or ";
        }
        $bundle_nos_cond=chop($bundle_nos_cond,'or ');
        $bundle_nos_cond.=")";
		
		$cutbundle_nos_cond=chop($cutbundle_nos_cond,'or ');
        $cutbundle_nos_cond.=")";
    }
    else
    {
        $bundle_nos_cond=" and c.barcode_no in ($bundleNo)";
		$cutbundle_nos_cond=" and barcode_no in ($bundleNo)";
    }
	
	$floorArr = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name');
	$company_short_arr = return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');
	$knitComFloorArr=array();
	$sql_knit="select c.barcode_no, a.serving_company, a.floor_id from pro_garments_production_mst a, pro_garments_production_dtls c where a.id=c.mst_id and a.production_type=51 and c.production_type=51 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $bundle_nos_cond";
	$sql_knit_res=sql_select($sql_knit);
	
	foreach($sql_knit_res as $row)
	{
		$knitComFloorArr[$row[csf('barcode_no')]]['kcomp']=$row[csf('serving_company')];
		$knitComFloorArr[$row[csf('barcode_no')]]['kfloor']=$row[csf('floor_id')];
	}
	
    $size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
    $color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
    $country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
    $buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
    
    $output_bundles=return_library_array( "select b.bundle_no, sum(b.production_qnty) as issue_qty from pro_gmts_delivery_mst a,pro_garments_production_dtls b where  a.id=b.delivery_mst_id and b.production_type=77 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bundle_nos_cond group by b.bundle_no",'bundle_no','issue_qty');
    $year_field="";
    if($db_type==0) $year_field="YEAR(f.insert_date)"; else if($db_type==2)$year_field="to_char(f.insert_date,'YYYY')";
	
	$sql="SELECT a.serving_company, a.floor_id, c.id as prdid, d.id as colorsizeid, e.id as po_id, f.job_no_prefix_num, $year_field as year, f.buyer_name, f.style_ref_no, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.cut_no, c.bundle_no, c.barcode_no, c.production_qnty as production_qnty, e.po_number, c.barcode_no 
	from pro_garments_production_mst a,pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f where f.company_name='$company_id' and a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_no_mst=f.job_no and c.delivery_mst_id='$mst_id' and a.production_type=77 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and e.status_active in(1,2,3) and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $bundle_nosCond  order by c.cut_no, length(c.bundle_no) asc, c.bundle_no asc";
     //echo $sql;// die;
	$result = sql_select($sql);
	
	$count=count($result);
	$i=$count;
	foreach ($result as $row)
	{ 
		if(trim($scanned_bundle_arr[$row[csf('bundle_no')]])=="" || $mst_id!="")
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$qty = ($row[csf('production_qnty')] ) - ($reject_qty[$row[csf('bundle_no')]] + $alter_qty[$row[csf('bundle_no')]]+ $spt_qty[$row[csf('bundle_no')]]);
			?>
			<tr bgcolor="<?=$bgcolor; ?>" id="tr_<?=$i;?>"> 
				<td width="30" align="center"><?=$i; ?></td>
				<td width="80" id="bundle_<?=$i; ?>" title="<?=$row[csf('barcode_no')]; ?>"><?=$row[csf('bundle_no')]; ?></td>
				<td width="90" id="barcode_<?=$i; ?>" title="<?=$row[csf('barcode_no')]; ?>"><?=$row[csf('barcode_no')]; ?></td>
                <td width="100" style="word-break:break-all;" align="left"><?=$color_arr[$row[csf('color_number_id')]]; ?></td>
				<td width="60" style="word-break:break-all;" align="center">&nbsp;<?=$size_arr[$row[csf('size_number_id')]]; ?></td>
				<td width="65" id="prodQty_<?=$i; ?>" align="right"><?=$qty; ?>&nbsp;</td>
                <td width="70" align="center"><?=$company_short_arr[$knitComFloorArr[$row[csf('barcode_no')]]['kcomp']]; ?></td>
				<td width="90" align="center"><?=$floorArr[$knitComFloorArr[$row[csf('barcode_no')]]['kfloor']]; ?></td>
                
                <td width="50" align="center"><?=$row[csf('year')]; ?></td>
				<td width="60" align="center"><?=$row[csf('job_no_prefix_num')]; ?></td>
                <td width="65"><?=$buyer_arr[$row[csf('buyer_name')]]; ?></td>
				<td width="90" style="word-break:break-all;"><?=$row[csf('style_ref_no')]; ?></td>
				<td width="90" style="word-break:break-all;" align="left"><?=$row[csf('po_number')]; ?></td>
				<td width="100" style="word-break:break-all;" align="left"><?=$garments_item[$row[csf('item_number_id')]]; ?></td>
				<td width="80" style="word-break:break-all;" align="left"><?=$country_arr[$row[csf('country_id')]]; ?></td>
				<td id="button_1" align="center">
					<input type="button" id="decrease_<?=$i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<?=$i; ?>);" />
					
					<input type="hidden" name="txtcutNo[]" id="txtcutNo_<?=$i; ?>" value="<?=$row[csf('cut_no')]; ?>"/>
					<input type="hidden" name="txtcolorSizeId[]" id="txtcolorSizeId_<?=$i; ?>" value="<?=$row[csf('colorsizeid')]; ?>"/>
					<input type="hidden" name="txtorderId[]" id="txtorderId_<?=$i; ?>" value="<?=$row[csf('po_id')]; ?>"/>
					<input type="hidden" name="txtgmtsitemId[]" id="txtgmtsitemId_<?=$i; ?>" value="<?=$row[csf('item_number_id')]; ?>"/>
					<input type="hidden" name="txtcountryId[]" id="txtcountryId_<?=$i; ?>" value="<?=$row[csf('country_id')]; ?>"/>
					<input type="hidden" name="txtcolorId[]" id="txtcolorId_<?=$i; ?>" value="<?=$row[csf('color_number_id')]; ?>"/>
					<input type="hidden" name="txtsizeId[]" id="txtsizeId_<?=$i; ?>" value="<?=$row[csf('size_number_id')]; ?>"/>
					<input type="hidden" name="txtqty[]" id="txtqty_<?=$i; ?>" value="<?=$qty; ?>"/>
					<input type="hidden" name="dtlsId[]" id="dtlsId_<?=$i; ?>" value="<?=$row[csf('prdid')]; ?>"/> 
				</td>
			</tr>
			<?
			$i--;
		}
	}
    exit(); 
}

if($action=="receive_in_1st_inspection_print")
{
    extract($_REQUEST);
    $data=explode('*',$data);
    //print_r ($data);
    $company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
    $floor_library=return_library_array( "select id, floor_name from  lib_prod_floor", "id", "floor_name");
    $color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
    $size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$location_library=return_library_array( "select id, location_name from  lib_location", "id", "location_name");
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
    
    $sql="select id, sys_number_prefix, sys_number_prefix_num, sys_number, delivery_date, company_id, location_id, production_source, serving_company, floor_id, working_company_id, working_location_id, remarks from pro_gmts_delivery_mst where id='$data[2]' and production_type=77 and status_active=1 and is_deleted=0"; //echo $sql; 
	$dataArray=sql_select($sql); 
    $image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
    //echo $image_location;die;
?>
<div style="width:1030px;">
    <table width="1000" cellspacing="0" align="left">
        <tr>
            <td colspan="2" align="left"><img src="../../../<?=$image_location; ?>" height="70" width="100"></td>
            <td colspan="4" align="center" style="font-size:20px;"><strong ><?=$company_library[$data[0]]; ?></strong></td>
            <td colspan="2" align="right" style="text-align:right;"><div id="qrcode"></div></td>
        </tr>
        <tr>
            <td colspan="8" align="center" style="font-size:18px"><u><strong><?=$data[3]; ?></strong></u></td>
        </tr>
        <tr>
            <td width="110"><strong>Receive NO:</strong></td>
            <td width="140px"><?=$dataArray[0][csf('sys_number')]; ?></td>
            <td width="110"><strong>Receive Date:</strong></td>
            <td width="140px"><?=change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
            <td width="110"><strong>Source:</strong></td>
            <td width="140"><?=$knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Remarks:</strong></td>
            <td colspan="3"><?=$dataArray[0][csf('remarks')]; ?></td>
        </tr>
    </table>
    <br>
    
    <table align="left" cellspacing="0" width="1000" border="1" rules="all" class="rpt_table" style=" margin-top:20px;" >
        <thead bgcolor="#dddddd">
        	<tr>
                <th width="30" rowspan="2">SL</th>
                <th width="100" rowspan="2">Job No</th>
                <th width="80" rowspan="2">Job Year</th>
                <th width="90" rowspan="2">Buyer</th>
                <th width="80" rowspan="2">Style No</th>
                <th width="80" rowspan="2">Order No</th>
                <th width="90" rowspan="2">Gmts. Item</th>
                <th width="80" rowspan="2">Country</th>
                <th width="90" rowspan="2">Bundle No</th>
                <th width="80" rowspan="2">Barcode No</th>
                <th colspan="2">RMG No.</th>
                <th width="90" rowspan="2">Gmts. Color</th>
                <th width="80" rowspan="2">Size</th>
                <th rowspan="2">Bundle Qty. (Pcs)</th>
            </tr>
            <tr>
            	<th width="50">From</th>
                <th width="50">To</th>
            </tr>
        </thead>
        <tbody>
        <?php
		 $year_field="";
			if($db_type==0) $year_field="YEAR(f.insert_date)"; else if($db_type==2) $year_field="to_char(f.insert_date,'YYYY')";
		
			$sql="SELECT c.id as prdid, d.id as colorsizeid, e.id as po_id, f.job_no_prefix_num, $year_field as year, f.buyer_name, f.style_ref_no, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.cut_no, c.bundle_no, c.barcode_no, c.production_qnty as production_qnty, e.po_number, c.barcode_no 
   from pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f where f.company_name='$data[0]' and a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_no_mst=f.job_no and c.delivery_mst_id='$data[2]' and a.production_type=77 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and e.status_active in(1,2,3) and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0
			order by c.cut_no, length(c.bundle_no) asc, c.bundle_no asc";
			//echo $sql;// die;
			$result = sql_select($sql); $barcodeNo="";
			foreach($result as $val)
			{
				$barcodeNo.="'".$val[csf("barcode_no")]."',";
			}
			
			$barcodeNos=chop($barcodeNo,','); $barcodeNos_cond="";
			$barcodeNosCount=count(array_filter(array_unique(explode(",",$barcodeNos))));
			if($db_type==2 && $barcodeNosCount>400)
			{
				$barcodeNos_cond=" and (";
				$barcodeNosArr=array_chunk(explode(",",trim($barcodeNos)),399);
				foreach($barcodeNosArr as $ids)
				{
					$barcode_nos=implode(",",$ids);
					$barcodeNos_cond.=" barcode_no in($barcode_nos) or ";
				}
				$barcodeNos_cond=chop($barcodeNos_cond,'or ');
				$barcodeNos_cond.=")";
			}
			else
			{
				$barcodeNos_cond=" and barcode_no in ($barcodeNos)";
			}
			
			$rmg_no_sql="select barcode_no, number_start, number_end from ppl_cut_lay_bundle where status_active=1 and is_deleted=0 $barcodeNos_cond";
			$rmg_no_arr=array();
			$rmg_no_sql_res = sql_select($rmg_no_sql);
			foreach($rmg_no_sql_res as $row)
			{
				$rmg_no_arr[$row[csf('barcode_no')]]['from']=$row[csf('number_start')];
				$rmg_no_arr[$row[csf('barcode_no')]]['to']=$row[csf('number_end')];
			}
			unset($rmg_no_sql_res);
            $i=1;
            foreach($result as $val)
            {
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>"  style="font-size:12px">
                    <td align="center"><?=$i;?></td>
                    <td align="center"><?=$val[csf('job_no_prefix_num')];?></td>
                    <td align="center"><?=$val[csf('year')]; ?></td>
                    <td style="word-break:break-all"><?=$buyer_arr[$val[csf('buyer_name')]]; ?></td>
                    <td style="word-break:break-all"><?=$val[csf('style_ref_no')]; ?></td>
                    <td style="word-break:break-all"><?=$val[csf('po_number')]; ?></td>
                    <td style="word-break:break-all"><?=$garments_item[$val[csf('item_number_id')]]; ?></td>
                    <td style="word-break:break-all"><?=$country_arr[$val[csf('country_id')]]; ?></td>
                    <td><?=$val[csf('bundle_no')]; ?></td>
                    <td><?=$val[csf('barcode_no')]; ?></td>
                    <td align="center"><?=$rmg_no_arr[$val[csf('barcode_no')]]['from']; ?></td>
                    <td align="center"><?=$rmg_no_arr[$val[csf('barcode_no')]]['to']; ?></td>
                    <td style="word-break:break-all"><?=$color_library[$val[csf('color_number_id')]]; ?></td>
                    <td style="word-break:break-all"><?=$size_library[$val[csf('size_number_id')]]; ?></td>
                    <td align="right"><?=number_format($val[csf('production_qnty')]); ?></td>
                </tr>
                <?
                $totProdQty+=$val[csf('production_qnty')];
                $i++;
            }

        ?>
        </tbody>
        <tfoot bgcolor="#eee" align="left">
            <th align="center" colspan="14">Total</th>
            <th align="right"><?=number_format($totProdQty); ?></th>
        </tfoot>
    </table>
    <?=signature_table(189, $data[0], "1000px"); ?>  
    </div>
    <script type="text/javascript" src="../../../js/jquery.js"></script>
    <script type="text/javascript" src="../../../js/jquery.qrcode.min.js"></script>
    <script>
        var main_value='<?=$dataArray[0][csf('sys_number')]; ?>';
        $('#qrcode').qrcode(main_value);
    </script>
	<?
    exit(); 
}

?>