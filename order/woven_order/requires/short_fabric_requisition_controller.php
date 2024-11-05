<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
include('../../../includes/class4/class.conditions.php');
include('../../../includes/class4/class.reports.php');
//include('../../../includes/class4/class.fabrics.php');
include('../../../includes/class4/class.yarns.php');
include('../../../includes/class4/class.fabrics.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
$user_id=$_SESSION['logic_erp']['user_id'];
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');

//---------------------------------------------------- Start---------------------------------------------------------------------------
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");
$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name");
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
if ($action=="load_drop_down_buyer")
{
	$data_arr = explode("*", $data);
	if($data_arr[2]=="") $data_arr[2]=$selected;
	if($data_arr[1]==1){
		$load="load_drop_down( 'short_fabric_requisition_controller', this.value, 'load_drop_down_season', 'season_td'); load_drop_down( 'short_fabric_requisition_controller', this.value, 'load_drop_down_brand', 'brand_td');";
	}else{
		$load="load_drop_down( 'requires/short_fabric_requisition_controller', this.value, 'load_drop_down_season', 'season_td'); load_drop_down( 'requires/short_fabric_requisition_controller', this.value, 'load_drop_down_brand', 'brand_td');";
	}
	
	if($data_arr[0] != 0)
	{
		echo create_drop_down( "cbo_buyer_name", 130, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 $buyer_cond and b.buyer_id=buy.id and b.tag_company='$data_arr[0]' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $data_arr[2], $load );
	}
	else{
		echo create_drop_down( "cbo_buyer_name", 130, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $data_arr[2], "" );
	}
	exit();
}

if ($action=="load_drop_down_brand")
{
	$data_arr = explode("*", $data);
	if($data_arr[1] == 1) $width=70; else $width=130;
	echo create_drop_down( "cbo_brand_id", $width, "select id, brand_name from lib_buyer_brand brand where buyer_id='$data_arr[0]' and status_active =1 and is_deleted=0 $brand_cond order by brand_name ASC","id,brand_name", 1, "--Brand--", $data_arr[2], "" );
	
	exit();
}

if ($action=="load_drop_down_season")
{
	$data_arr = explode("*", $data);
	if($data_arr[1] == 1) $width=70; else $width=130;
	echo create_drop_down( "cbo_season_id", $width, "select id, season_name from lib_buyer_season where buyer_id='$data_arr[0]' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-- Select Season--", $data_arr[2], "" );
	exit();
}

if ($action=="load_drop_down_buyer_popup")
{
	$data_arr = explode("*", $data);
	if($data_arr[1]==1){
		$load="load_drop_down( 'short_fabric_requisition_controller', this.value, 'load_drop_down_season', 'season_td'); load_drop_down( 'short_fabric_requisition_controller', this.value, 'load_drop_down_brand', 'brand_td');";
	}else{
		$load="load_drop_down( 'requires/short_fabric_requisition_controller', this.value, 'load_drop_down_season', 'season_td'); load_drop_down( 'requires/short_fabric_requisition_controller', this.value, 'load_drop_down_brand', 'brand_td');";
	}

	echo create_drop_down( "cbo_buyer_name", 130, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 $buyer_cond and b.buyer_id=buy.id and b.tag_company='$data_arr[0]' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, $load,"0","" );
	exit();
}

if($action=="get_po_config"){
	$action($data);
}

function get_po_config($data)
{
	$exdata=explode("_",$data);
	
	$po_id=$exdata[0];
	$fabricnature=$exdata[1];
	$fabricsource=$exdata[2];
	$fabricuom=$exdata[3];
	
	global $body_part;
	global $color_type;
	global $color_library;
	global $size_library;
	
	$fabricnature_cond=""; $fabricsource_cond=""; $fabricuom_cond="";
	
	if ($fabricnature!=0) $fabricnature_cond="and d.fab_nature_id='$fabricnature'";
	if ($fabricsource!=0) $fabricsource_cond="and d.fabric_source='$fabricsource'";
	if ($fabricuom!=0) $fabricuom_cond="and d.uom='$fabricuom'";
	
	//PO Drop Down
	$sqlPo="select a.COMPANY_NAME, A.JOB_NO, b.ID, b.PO_NUMBER, C.COLOR_NUMBER_ID, c.SIZE_NUMBER_ID, d.ID AS BOMFABRICID, d.BODY_PART_ID, d.COLOR_TYPE_ID, d.GSM_WEIGHT, d.CONSTRUCTION, d.COMPOSITION, d.COLOR_SIZE_SENSITIVE, d.COLOR, d.COLOR_BREAK_DOWN from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_fabric_cost_dtls d where a.id=b.job_id and b.id=c.po_break_down_id and a.id=c.job_id and a.id=d.job_id and b.job_id=d.job_id and c.job_id=d.job_id and b.id in($po_id) and a.is_deleted=0 and b.is_deleted=0 $fabricnature_cond $fabricsource_cond $fabricuom_cond";
	//echo $sqlPo; die;
	$sqlPoRes=sql_select($sqlPo);
	$company_name=0; $job_no="";
	$company_name=$sqlPoRes[0]['COMPANY_NAME'];
	
	$job_no=$sqlPoRes[0]['JOB_NO'];
	
	$contrastColorArr=array();
	
	$contrastColorSql=sql_select("select PRE_COST_FABRIC_COST_DTLS_ID, GMTS_COLOR_ID, CONTRAST_COLOR_ID from wo_pre_cos_fab_co_color_dtls where job_no='".$job_no."' and status_active=1 and is_deleted=0");
	//echo "select PRE_COST_FABRIC_COST_DTLS_ID, GMTS_COLOR_ID, CONTRAST_COLOR_ID from wo_pre_cos_fab_co_color_dtls where job_no='".$job_no."' and status_active=1 and is_deleted=0"; die;
	
	foreach($contrastColorSql as $controw)
	{
		$contrastColorArr[$controw['PRE_COST_FABRIC_COST_DTLS_ID']][$controw['GMTS_COLOR_ID']]=$controw['CONTRAST_COLOR_ID'];
	}
	//print_r($contrastColorArr); die;
	//echo $company_name.'-'.$job_no.'-'.$po_id; die;
	$condition= new condition();
	$condition->company_name("='$company_name'");
	if(str_replace("'","",$po_id)!=''){
		$condition->po_id("in($po_id)");
	}
	$condition->init();
	$fabric= new fabric($condition);
	//echo $fabric->getQuery(); die;
	$fabric_qty_arr=$fabric->getQtyArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
	//print_r($fabric_qty_arr); die;
	$short_fabric_booking_befr_main=sql_select("select S_F_BOOKING_BEFOR_M_F from variable_order_tracking where company_name='$company_name' and variable_list=38 and s_f_booking_befor_m_f=2 and status_active=1 and is_deleted=0");
	//echo "select S_F_BOOKING_BEFOR_M_F from variable_order_tracking where company_name='$company_name' and variable_list=38 and s_f_booking_befor_m_f=2 and status_active=1 and is_deleted=0"; die;
	
	$sf_be_mf_va=0;
	$sf_be_mf_va=$short_fabric_booking_befr_main[0]['S_F_BOOKING_BEFOR_M_F'];
	
	$booking_qty_arr = array(); 
	if($sf_be_mf_va==2) //******NO********S.F. Booking Before M.F.//
	{
		$booking_sql='SELECT A.PO_BREAK_DOWN_ID, A.FIN_FAB_QNTY, A.GREY_FAB_QNTY, C.ID AS PRE_COSTID from wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls c where    a.po_break_down_id in('.$po_id.') and a.pre_cost_fabric_cost_dtls_id=c.id and a.booking_type=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0';
		$result_booking_main=sql_select($booking_sql);
		
		foreach ($result_booking_main as $row) 
		{
			$booking_qty_arr[$row['PRE_COSTID']] +=$row['GREY_FAB_QNTY'];
		}
		unset($result_booking_main);
	} //Variable End
	$po_number_arr=array(); $fabric_description_array=array(); $fabric_color_array=array(); $gmtsColorArr=array(); $size_library_order=array();
	$all_pre_fabric_dtls_id=""; $tot_fab_qty_knit=$book_reqQty=0;
	
	//print_r($sqlPoRes); echo "dfsdf"; die;
	foreach($sqlPoRes as $row)
	{
		$po_number_arr[$row['ID']]=$row['PO_NUMBER'];
		//echo "kk";
		$fab_qty_knit=array_sum($fabric_qty_arr['knit']['grey'][$row['BOMFABRICID']]);
		$fab_qty_woven=array_sum($fabric_qty_arr['woven']['grey'][$row['BOMFABRICID']]);
		$tot_fab_qty_knit=number_format($fab_qty_knit+$fab_qty_woven,2,'.','');
		$book_reqQty=0;//number_format($booking_qty_arr[$row['BOMFABRICID']],2,'.','');
	
		if($book_reqQty>=$tot_fab_qty_knit) //Full Fab 100% done
		{
			$all_pre_fabric_dtls_id.= $row['BOMFABRICID'].',';
		}
		$fabric_description_array[$row['BOMFABRICID']]=$body_part[$row["BODY_PART_ID"]].', '.$color_type[$row["COLOR_TYPE_ID"]].', '.$row["CONSTRUCTION"].', '.$row["COMPOSITION"].', '.$row["GSM_WEIGHT"];
		
		$constrast_color_arr=array();
		if($row["COLOR_SIZE_SENSITIVE"]==3)
		{
			$constrast_color=explode('__',$row["COLOR_BREAK_DOWN"]);
			for($i=0;$i<count($constrast_color);$i++)
			{
				$constrast_color2=explode('_',$constrast_color[$i]);
				$constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
			}
		}
		
		if($row["COLOR_SIZE_SENSITIVE"]==3)
		{
			$contrastcolorid=$contrastColorArr[$row['BOMFABRICID']][$row['COLOR_NUMBER_ID']];
			$fabric_color_array[$contrastcolorid]=$color_library[$contrastcolorid];//$constrast_color_arr[$row["COLOR_NUMBER_ID"]];
		}
		else
		{
			$fabric_color_array[$row["COLOR_NUMBER_ID"]]=$color_library[$row["COLOR_NUMBER_ID"]];
		}
		
		$gmtsColorArr[$row["COLOR_NUMBER_ID"]]=$color_library[$row["COLOR_NUMBER_ID"]];
		$size_library_order[$row["SIZE_NUMBER_ID"]]=$size_library[$row["SIZE_NUMBER_ID"]];
	}
	//print_r($po_number_arr); die;
	$cbo_order_id=create_drop_down( "cbo_order_id",130, $po_number_arr,"", 1, "--Select--", "", "","",$po_id,"","","","" );
	
	//Fabric Drop Down
	//$all_pre_fabric_dtls_id=rtrim($all_pre_fabric_dtls_id,',');
	//$all_pre_fabric_dtls_ids=implode(",",array_unique(explode(",",$all_pre_fabric_dtls_id)));
	$cbo_fabricdescription_id=create_drop_down( "cbo_fabricdescription_id", 382, $fabric_description_array,"", 1, "--Select--", "", "set_process_loss( this.value );","","$all_pre_fabric_dtls_ids","","","","" );
	
	//Fabric Color
	$cbo_fabriccolor_id=create_drop_down( "cbo_fabriccolor_id", 130, $fabric_color_array,"", 1, "-Select-", "", "","","","","","","" );
	
	//Gmts Color
	$cbo_garmentscolor_id=create_drop_down( "cbo_garmentscolor_id", 130, $gmtsColorArr,"", 1, "-- Select Color --", $selected, "" );
	
	//Item Size
	$nameArray=sql_select("select b.ITEM_SIZE FROM wo_pre_cost_fabric_cost_dtls d, wo_pre_cos_fab_co_avg_con_dtls b WHERE d.job_no=b.job_no and d.id=b.pre_cost_fabric_cost_dtls_id and b.po_break_down_id in (".$po_id.") $fabricnature_cond $fabricsource_cond $fabricuom_cond order by b.item_size");
	//echo "select b.ITEM_SIZE FROM wo_pre_cost_fabric_cost_dtls d, wo_pre_cos_fab_co_avg_con_dtls b WHERE d.job_no=b.job_no and d.id=b.pre_cost_fabric_cost_dtls_id and b.po_break_down_id in (".$po_id.") $fabricnature_cond $fabricsource_cond $fabricuom_cond order by b.item_size"; die;
	$item_size_array= array();
	foreach ($nameArray as $result)
	{
		if (count($nameArray)>0 )
		{
		    $item_size_array[$result["ITEM_SIZE"]]=$result["ITEM_SIZE"];
		}
	}

	$cbo_itemsize_id=create_drop_down( "cbo_itemsize_id", 130, $item_size_array,"", 1, "-Select-", "", "","","","","","","" );
	
	//GMTS Size
	$cbo_garmentssize_id=create_drop_down( "cbo_garmentssize_id", 130, $size_library_order,"", 1, "-- Select Size --", $selected, "" );
	
	echo "document.getElementById('order_drop_down_td').innerHTML = '".$cbo_order_id."';\n";
	echo "document.getElementById('fabricdescription_id_td').innerHTML = '".$cbo_fabricdescription_id."';\n";
	echo "document.getElementById('fabriccolor_id_id_td').innerHTML = '".$cbo_fabriccolor_id."';\n";
	echo "document.getElementById('garmentscolor_id_id_td').innerHTML = '".$cbo_garmentscolor_id."';\n";
	echo "document.getElementById('itemsize_id_td').innerHTML = '".$cbo_itemsize_id."';\n";
	echo "document.getElementById('garmentssize_id_td').innerHTML = '".$cbo_garmentssize_id."';\n";
}

if ($action=="order_search_popup")
{
	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array, selected_name = new Array();
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count;
			for( var i = 1; i <= tbl_row_count; i++ )
			{
				js_set_value( i );
			}
		}

		function toggle( x, origColor )
		{
			//alert(x)
			var newColor = 'yellow';
			document.getElementById(x).style.backgroundColor = ( newColor == document.getElementById(x).style.backgroundColor )? origColor : newColor;
		}

		function js_set_value( str_data,tr_id )
		{
			var str_all=str_data.split("_");
			var str_po=str_all[1];
			var str=str_all[0];
			if ( document.getElementById('job_no').value!="" && document.getElementById('job_no').value!=str_all[2] )
			{
				alert('No Job Mix Allowed')
				return;
			}
			toggle( tr_id, '#FFFFCC');
			document.getElementById('job_no').value=str_all[2];

			if( jQuery.inArray( str , selected_id ) == -1 )
			{
				selected_id.push( str );
				selected_name.push( str_po );
			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == str ) break;
				}

				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
					//alert(selected_id.length)
				if(selected_id.length==0)
				{
					document.getElementById('job_no').value="";
				}
			}
			var id = '' ; var name = '';
			for( var i = 0; i < selected_id.length; i++ )
			{
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			$('#po_number_id').val( id );
			$('#po_number').val( name );
		}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
            <form name="searchpofrm_1" id="searchpofrm_1">
            <table width="1100" class="rpt_table" align="center" rules="all">
                <thead>
                    <tr>
                        <th colspan="13"><?=create_drop_down( "cbo_string_search_type", 100, $string_search_type,'', 1, "--Searching Type--" ); ?></th>
                    </tr>
                    <tr>
                        <th width="130">Company Name</th>
                        <th width="130">Buyer Name</th>
						<th width="70">Brand</th>
						<th width="70">Season</th>
						<th width="50">Season Year</th>
                        <th width="80">Job No</th>
                        <th width="100">Style Ref </th>
                        <th width="80">Internal Ref No</th>
                        <th width="80">File No</th>
                        <th width="100">Order No</th>
                        <th width="120" colspan="2">Pub.Ship Date Range</th>
                        <th>
                            <input type="hidden" id="po_number_id">
                            <input type="hidden" id="job_no">
                        </th>
                    </tr>
                </thead>
                <tr class="general">
                    <td><?=create_drop_down( "cbo_company_mst", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "- Select Company -", str_replace("'","",$cbo_company_name), "load_drop_down( 'short_fabric_requisition_controller', this.value+'*'+1, 'load_drop_down_buyer_popup', 'buyer_td' );"); ?></td>
                    <td id="buyer_td"><?=create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 $buyer_cond and b.buyer_id=buy.id and b.tag_company=$cbo_company_name and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", str_replace("'","",$cbo_buyer_name), "load_drop_down( 'short_fabric_requisition_controller', this.value, 'load_drop_down_season', 'season_td'); load_drop_down( 'short_fabric_requisition_controller', this.value, 'load_drop_down_brand', 'brand_td');" ) ?></td>
					<? $buyer=str_replace("'","",$cbo_buyer_name)?>

					<td id="brand_td"><? echo create_drop_down( "cbo_brand_id", 70, "select id, brand_name from lib_buyer_brand brand where buyer_id='$buyer' and status_active =1 and is_deleted=0 $brand_cond order by brand_name ASC","id,brand_name", 1, "--Select Brand--",str_replace("'","",$cbo_brand_id), "" ); ?>
					<td id="season_td"><? echo create_drop_down( "cbo_season_id", 70, "select id, season_name from lib_buyer_season where buyer_id='$buyer' and status_active =1 and is_deleted=0 order by season_name ASC",'id,season_name', 1, "--Select Season--",str_replace("'","",$cbo_season_id), "" ); ?></td>
					<td><?=create_drop_down( "cbo_season_year", 50, create_year_array(),"", 1,"-Year-", 1, "",0,"" ); ?></td>
                    <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:70px"></td>
                    <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:90px"></td>
                    <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:70px"></td>
                    <td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:70px"></td>
                    <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:90px"></td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" value="<?=$start_date; ?>"/></td>
                    <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px" value="<?=$end_date; ?>"/>
                        <input class="datepicker" type="hidden" style="width:130px" name="txt_reqsn_date" id="txt_reqsn_date"  value="<? echo str_replace("'","",$txt_reqsn_date)?>" disabled />
                    </td>
                    <td>
                    <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_reqsn_date').value+'_'+document.getElementById('cbo_brand_id').value+'_'+document.getElementById('cbo_season_id').value+'_'+document.getElementById('cbo_season_year').value+'_'+document.getElementById('cbo_year_selection').value, 'create_po_search_list_view', 'search_div', 'short_fabric_requisition_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:80px;" />
                    </td>
                </tr>
                <tr>
                <td align="center"><? echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
                    <td colspan="12" align="center">
                        <strong>Selected PO Number:</strong> &nbsp;<input type="text" class="text_boxes" readonly style="width:550px" id="po_number">
                    </td>
                </tr>
                <tr>
                    <td colspan="13" align="center" >
                        <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                    </td>
                </tr>
            </table>
        <div id="search_div" align="center"></div>
        </form>
        </div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_po_search_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and a.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.buyer_name='$data[1]'"; else $buyer=""; //{ echo "Please Select Buyer First."; die; }

	$txt_reqsn_date=str_replace("'","",$data[10]);

	$job_cond=""; $order_cond=""; $style_cond="";
	if($data[7]==1)
	{
		if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num='$data[4]'"; //else  $job_cond="";
		if (str_replace("'","",$data[5])!="") $order_cond=" and b.po_number = '$data[5]'  "; //else  $order_cond="";
		if (trim($data[6])!="") $style_cond=" and a.style_ref_no ='$data[6]'"; //else  $style_cond="";
	}
	else if($data[7]==2)
	{
		if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num like '$data[4]%'"; //else  $job_cond="";
		if (str_replace("'","",$data[5])!="") $order_cond=" and b.po_number like '$data[5]%'  "; //else  $order_cond="";
		if (trim($data[6])!="") $style_cond=" and a.style_ref_no like '$data[6]%'  "; //else  $style_cond="";
	}
	else if($data[7]==3)
	{
		if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num like '%$data[4]'"; //else  $job_cond="";
		if (str_replace("'","",$data[5])!="") $order_cond=" and b.po_number like '%$data[5]'  "; //else  $order_cond="";
		if (trim($data[6])!="") $style_cond=" and a.style_ref_no like '%$data[6]'"; //else  $style_cond="";
	}
	else if($data[7]==4 || $data[7]==0)
	{
		if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num like '%$data[4]%'"; //else  $job_cond="";
		if (str_replace("'","",$data[5])!="") $order_cond=" and b.po_number like '%$data[5]%'  "; //else  $order_cond="";
		if (trim($data[6])!="") $style_cond=" and a.style_ref_no like '%$data[6]%'"; //else  $style_cond="";
	}
	$internal_ref = str_replace("'","",$data[8]);
	$file_no = str_replace("'","",$data[9]);
	
	if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='".trim($file_no)."' ";
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping='".trim($internal_ref)."' ";

	if ($data[2]!="" &&  $data[3]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	
	$short_fabric_booking_befr_main=sql_select("select s_f_booking_befor_m_f from variable_order_tracking where company_name='$data[0]' and variable_list=38 and status_active=1 and is_deleted=0");
	$budget_on=sql_select("select embellishment_budget_id as budget_id from variable_order_tracking where company_name='$data[0]' and variable_list=75 and embellishment_id=3 and status_active=1 and is_deleted=0");
	list($sf_be_mf)= $short_fabric_booking_befr_main;
	$sf_be_mf_va=$sf_be_mf[csf('s_f_booking_befor_m_f')];
   //and to_char(a.insert_date,'YYYY')=2022
	$po_id_arr=array();
	$sql_job=sql_select("SELECT a.job_no,b.id, e.uom,e.item_number_id,e.body_part_id,e.lib_yarn_count_deter_id,e.width_dia_type  from wo_po_details_master a, wo_po_break_down b, wo_booking_mst c, wo_booking_dtls d , wo_pre_cost_fabric_cost_dtls e where a.garments_nature=2 and a.job_no=b.job_no_mst and a.job_no=d.job_no and b.id=d.po_break_down_id and c.booking_no=d.booking_no and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.job_no = e.job_no and to_char(a.insert_date,'YYYY')='$data[14]' $shipment_date $company $buyer $job_cond $order_cond $file_no_cond $internal_ref_cond $style_cond ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." group by b.id,e.uom,e.item_number_id,e.body_part_id,e.lib_yarn_count_deter_id,e.width_dia_type,a.job_no order by b.id DESC");

	$bk_string="";
	
	foreach($sql_job as $row_job )
	{
		$selected_po_id[$row_job[csf('id')]]= $row_job[csf('id')];
	}
	$txt_order_no_id = implode(",", $selected_po_id);
	
	$req_qty_data=sql_select("SELECT b.id, sum(c.order_quantity) as order_quantity, sum(c.plan_cut_qnty) as plan_cut_qnty, (sum (c.plan_cut_qnty)/12)* d.avg_cons as plun_req_qnty, (sum (c.order_quantity)/12)*d.avg_cons as order_req_qnty, d.id as pre_cost_dtls_id, d.body_part_id, d.body_part_type, d.fab_nature_id, d.fabric_source, d.construction, d.lib_yarn_count_deter_id, d.gsm_weight, d.width_dia_type, d.avg_cons, d.item_number_id
		from wo_po_details_master a join wo_po_break_down b on a.id = b.job_id join wo_po_color_size_breakdown c on a.id = c.job_id and b.id = c.po_break_down_id join wo_pre_cost_fabric_cost_dtls d on a.id = d.job_id and c.item_number_id = d.item_number_id and d.is_deleted = 0 and d.status_active = 1
		where a.garments_nature=2 and a.status_active = 1 and b.is_deleted = 0 and b.status_activ= 1 and c.is_deleted = 0 and c.status_active= 1 and to_char(a.insert_date,'YYYY')='$data[14]' $shipment_date $company $buyer $job_cond $order_cond $file_no_cond $internal_ref_cond $style_cond
		group by b.id, d.id, d.body_part_id, d.body_part_type, d.fab_nature_id, d.fabric_source, d.construction, d.lib_yarn_count_deter_id, d.gsm_weight, d.width_dia_type, d.avg_cons, d.item_number_id");

	$reqQtyArr=array();
	foreach($req_qty_data as $val){
		if($budget_on[0][csf('budget_id')]==1){
			$reqQtyArr[$val[csf('id')]][$val[csf('item_number_id')]][$val[csf('body_part_id')]][$val[csf('lib_yarn_count_deter_id')]][$val[csf('width_dia_type')]]+=$val[csf('order_req_qnty')];
		}else{
			$reqQtyArr[$val[csf('id')]][$val[csf('item_number_id')]][$val[csf('body_part_id')]][$val[csf('lib_yarn_count_deter_id')]][$val[csf('width_dia_type')]]+=$val[csf('plun_req_qnty')];
		}
	}
	//echo "<pre>";
	//print_r($reqQtyArr);
	//echo  $data[11]."*".$data[12]."*".$data[13];
	if($data[11] !=0) $brand_cond = " and a.brand_id='$data[11]'"; else $brand_cond="";
	if($data[12] !=0) $season_cond = " and a.season_buyer_wise='$data[12]'"; else $season_cond="";
	if($data[13] !=0) $season_year_cond = " and a.season_year='$data[13]'"; else $season_year_cond="";
	$booking_sql='SELECT b.po_break_down_id, a.fin_fab_qnty, a.grey_fab_qnty, c.body_part_id, c.body_part_type, c.lib_yarn_count_deter_id, c.gsm_weight, c.width_dia_type, c.avg_cons, c.item_number_id from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls c where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.po_break_down_id=b.po_break_down_id and a.gmts_color_id=b.color_number_id  and a.dia_width=b.dia_width and  a.po_break_down_id in('.$txt_order_no_id.') and b.pre_cost_fabric_cost_dtls_id=c.id and b.cons>0  and a.booking_type=1 and a.status_active=1 and a.is_deleted=0 group by b.po_break_down_id, a.id, a.fin_fab_qnty, a.grey_fab_qnty, c.body_part_id, c.body_part_type, c.lib_yarn_count_deter_id, c.gsm_weight, c.width_dia_type, c.avg_cons, c.item_number_id';
	$booking_date=sql_select($booking_sql);
	$booking_qty_arr = array();$bookingQtyArr= array();
	foreach ($booking_date as $row) {
		$booking_qty_arr[$row[csf('po_break_down_id')]] +=$row[csf('grey_fab_qnty')];
		$bookingQtyArr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('width_dia_type')]]+=$row[csf('grey_fab_qnty')];
	}

	$bookingQnty=0;$reqQnty=0;
	foreach($sql_job as $row_job )
	{
		// echo $row_job[csf('id')].'='.number_format($booking_qty_arr[$row_job[csf('id')]],2,'.','').'=='.number_format($req_qty_arr['woven']['grey'][$row_job[csf('id')]][$row_job[csf('uom')]],2,'.','').'<br>';
		//  echo $row_job[csf('job_no')]."==>po no=".$row_job[csf('id')].'===>'.number_format($bookingQtyArr[$row_job[csf('id')]][$row_job[csf('item_number_id')]][$row_job[csf('body_part_id')]][$row_job[csf('lib_yarn_count_deter_id')]][$row_job[csf('width_dia_type')]],2,'.','').'=='.number_format($reqQtyArr[$row_job[csf('id')]][$row_job[csf('item_number_id')]][$row_job[csf('body_part_id')]][$row_job[csf('lib_yarn_count_deter_id')]][$row_job[csf('width_dia_type')]],2,'.','').'<br>';
		$bookingQnty=number_format($bookingQtyArr[$row_job[csf('id')]][$row_job[csf('item_number_id')]][$row_job[csf('body_part_id')]][$row_job[csf('lib_yarn_count_deter_id')]][$row_job[csf('width_dia_type')]],2,'.','');
		$reqQnty=number_format($reqQtyArr[$row_job[csf('id')]][$row_job[csf('item_number_id')]][$row_job[csf('body_part_id')]][$row_job[csf('lib_yarn_count_deter_id')]][$row_job[csf('width_dia_type')]],2,'.','');

		if(number_format($bookingQtyArr[$row_job[csf('id')]][$row_job[csf('item_number_id')]][$row_job[csf('body_part_id')]][$row_job[csf('lib_yarn_count_deter_id')]][$row_job[csf('width_dia_type')]],0,'.','') >= number_format($reqQtyArr[$row_job[csf('id')]][$row_job[csf('item_number_id')]][$row_job[csf('body_part_id')]][$row_job[csf('lib_yarn_count_deter_id')]][$row_job[csf('width_dia_type')]],0,'.',''))
		{
			$po_id_arr[] = $row_job[csf('id')];
			 
		}
	}
	 //echo implode(",",$po_id_arr);
	$sql_approval_status="select id,approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data[0]' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format($txt_reqsn_date, "", "",1)."' and company_id='$data[0]')) and page_id=37 and status_active=1 and is_deleted=0 order by id";
	
	$approval_statusRes=sql_select($sql_approval_status);
	if($approval_statusRes[0][csf('approval_need')]==1) $approval_statusCond="1"; else $approval_statusCond="1,2,3";
	//echo $approval_statusRes[0][csf('approval_need')].'d';
	
	$sql=sql_select("select b.approval_need, b.validate_page, b.allow_partial from approval_setup_mst a, approval_setup_dtls b where a.id=b.mst_id and a.company_id='$data[0]' and b.page_id=36 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.setup_date");
	$app_nessity=2; $validate_page=0; $allow_partial=2;
	foreach($sql as $row){
		$app_nessity=$row[csf('approval_need')];
		$validate_page=$row[csf('validate_page')];
		$allow_partial=$row[csf('allow_partial')];
	}
	
	$sourcingAppCond="";//Dont HIde Issue id ISD-21-04460
	if($app_nessity==1)
	{
		 if($allow_partial==1) $sourcingAppCond=" and c.sourcing_approved in (1,3)";
		 else $sourcingAppCond=" and c.sourcing_approved=1";
	}

	// print_r($po_id_arr);
	//echo count($po_id_arr)."and".$sf_be_mf_va;
	if(count($po_id_arr)>0 and $sf_be_mf_va==2)
	{
		$po_id=array_chunk($po_id_arr,1000, true);
		$po_cond_in="";
		
		$ji=0;
		foreach($po_id as $key=> $value)
		{
			if($ji==0) $po_cond_in="and b.id in(".implode(",",$value).")";
			else $po_cond_in.=" or b.id in(".implode(",",$value).")";
			$ji++;
		}

		$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
		$season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");
		$brand_arr=return_library_array("select id,brand_name from lib_buyer_brand","id","brand_name");

		$arr=array (1=>$buyer_arr,2=>$brand_arr,3=>$season_arr);
		$sql= "select a.job_no_prefix_num, a.company_name, a.buyer_name, a.brand_id, a.season_buyer_wise, a.season_year, a.style_ref_no, a.job_quantity, b.po_number, b.grouping, b.file_no, b.id, b.po_quantity, b.shipment_date, a.job_no  from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c where a.garments_nature=2 and a.job_no=b.job_no_mst and a.job_no=c.job_no and a.status_active=1 and b.status_active=1 and c.approved in ($approval_statusCond) and to_char(a.insert_date,'YYYY')='$data[14]' $po_cond_in  $internal_ref_cond $file_no_cond $sourcingAppCond $brand_cond $season_cond $season_year_cond order by a.id desc"; //$shipment_date $company $buyer $job_cond  $style_cond
	 	// echo  $sql;
		echo  create_list_view("list_view", "Job No,Buyer,Brand,Season,Season Year,Style Ref. No,Job Qty,PO No,Internal Ref No,File No.,PO Qty,Shipment Date", "60,50,50,50,50,100,60,80,120,80,60,150","1050","320",0, $sql , "js_set_value", "id,po_number,job_no", "this.id", 1, "0,buyer_name,brand_id,season_buyer_wise,0,0,0,0,0,0,0,0,0", $arr , "job_no_prefix_num,buyer_name,brand_id,season_buyer_wise,season_year,style_ref_no,job_quantity,po_number,grouping,file_no,po_quantity,shipment_date", '','','0,0,0,0,0,0,0,0,0,0,1,3','','');
	}
	else if($sf_be_mf_va==1)
	{
		$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
		$season=return_library_array("select id,season_name from lib_buyer_season","id","season_name");
		$brand=return_library_array("select id,brand_name from lib_buyer_brand","id","brand_name");
		$arr=array(1=>$buyer_arr,2=>$brand,3=>$season);
		$sql= "select a.job_no_prefix_num, a.company_name, a.buyer_name, a.brand_id, a.season_buyer_wise, a.season_year, a.style_ref_no, a.job_quantity, b.id, b.file_no, b.grouping, b.po_number, b.po_quantity, b.shipment_date, a.job_no  from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c where a.garments_nature=2 and a.job_no=b.job_no_mst and a.job_no=c.job_no and a.status_active=1 and b.status_active=1 and c.approved in ($approval_statusCond) and to_char(a.insert_date,'YYYY')='$data[14]' $shipment_date $company $buyer $job_cond $order_cond $style_cond $internal_ref_cond $file_no_cond $sourcingAppCond $brand_cond $season_cond $season_year_cond order by a.id desc"; //$shipment_date $company $buyer $job_cond  $style_cond
		
		echo create_list_view("list_view", "Job No,Buyer,Brand,Season,Season Year,Style Ref. No,Job Qty.,PO number,Internal Ref No,File No.,PO Qty,Shipment Date", "60,50,50,50,50,100,70,150,100,100,80,80","1150","320",0, $sql , "js_set_value", "id,po_number,job_no", "this.id", 1, "0,buyer_name,brand_id,season_buyer_wise,season_year,0,0,0,0,0,0,0,0", $arr , "job_no_prefix_num,buyer_name,a.brand_id,a.season_buyer_wise,a.season_year,style_ref_no,job_quantity,po_number,grouping,file_no,po_quantity,shipment_date", '','','0,0,0,0,0,0,0,0,0,0,1,3','','');
	}
	else
	{
		?>
        <strong style="color:#F00"> <? echo "100% Requisition againts budget not done. Please Check". rtrim($bk_string,", ")."";?></strong>
        <?
	}
	exit(); 
}

if($action=="process_loss_method_id")
{
	$data=explode("_",$data);
	$process_loss_method=return_field_value("process_loss_method", "variable_order_tracking", "company_name=$data[0]  and variable_list=18 and item_category_id=$data[1] and status_active=1 and is_deleted=0");
	echo $process_loss_method;
	exit();
}

if($action=="prosess_loss_set")
{
	$process_loss=0;
	$nameArray=sql_select("select  process_loss from lib_yarn_count_determina_mst a, wo_pre_cost_fabric_cost_dtls b where a.id=b.lib_yarn_count_deter_id and b.id=$data limit 1");

	foreach ($nameArray as $result)
	{
		$process_loss=$result[csf("process_loss")];
	}
	echo $process_loss;
	exit();
}

if($action=="prosess_loss_set_2")
{
	$data_arr=sql_select("SELECT a.rd_no, a.construction, b.gsm_weight from lib_yarn_count_determina_mst a,  wo_pre_cost_fabric_cost_dtls b  where a.id=b.lib_yarn_count_deter_id and b.id=$data");
	
	//  print_r($data_arr);
	echo "document.getElementById('txt_rd_no').value = '".$data_arr[0][csf("rd_no")]."';\n";	
	echo "document.getElementById('txt_fabric_ref').value = '".$data_arr[0][csf("construction")]."';\n";
	echo "document.getElementById('txt_fabric_weight').value = '".$data_arr[0][csf("gsm_weight")]."';\n";
	exit();
}

if($action=="show_fabric_requisition")
{
	extract($_REQUEST);
	$po_number_arr=return_library_array( "select id,po_number from wo_po_break_down", "id", "po_number");
	$arr=array (0=>$po_number_arr,1=>$body_part,2=>$color_type,6=>$color_library);
	$txt_reqsn_no=str_replace("'","",$txt_reqsn_no);
	$sql= "select a.po_break_down_id, b.body_part_id, b.color_type_id, b.construction, b.composition, b.gsm_weight, a.fabric_color_id, a.item_size, a.dia_width, a.fin_fab_qnty, a.process_loss_percent, a.grey_fab_qnty, a.rate, a.amount, a.id, a.pre_cost_fabric_cost_dtls_id FROM wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b  WHERE a.pre_cost_fabric_cost_dtls_id=b.id and a.booking_no ='".$data."' and a.is_short=1 and a.status_active=1 and a.is_deleted=0";

	echo create_list_view("list_view", "PO NO,Body Part,Color Type,Construction,Composition,GSM,Fab.Color,Item Size,Dia/ Width,Fin Fab Qty,Process Loss,Gray Qty", "100,100,80,130,170,40,100,50,50,40,40,60","1110","220",0, $sql , "get_php_form_data", "id", "'populate_details_data_from_for_update'", 1, "po_break_down_id,body_part_id,color_type_id,0,0,0,fabric_color_id,0,0,0,0,0,0", $arr , "po_break_down_id,body_part_id,color_type_id,construction,composition,gsm_weight,fabric_color_id,item_size,dia_width,fin_fab_qnty,process_loss_percent,grey_fab_qnty", "requires/short_fabric_requisition_controller",'','0,0,0,0,0,0,0,0,0,2,2,2','0,0,0,0,0,0,0,0,0,0,fin_fab_qnty,0,grey_fab_qnty');
	exit();
}

if($action=="check_is_requisition_used")
{
	$work_order_no=return_field_value("work_order_no","com_pi_item_details","work_order_no='$data' and status_active =1 and is_deleted=0");
	echo $work_order_no;
	die;
}

if ($action=="save_update_delete"){
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$str_rep=array("/", "&", "*", "(", ")", "=","'",",",'"','#');
	$txt_remark=str_replace($str_rep,' ',str_replace("'","",$txt_remark));
	if ($operation==0){
		$con = connect();
		
		$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'KSFR', date("Y",time()), 5, "select booking_no_prefix, booking_no_prefix_num from wo_booking_mst where company_id=$cbo_company_name and booking_type=14 and entry_form=730 and to_char(insert_date,'YYYY')=".date('Y',time())." order by id desc", "booking_no_prefix", "booking_no_prefix_num"));
		
		$id=return_next_id("id", "wo_booking_mst", 1);
		$field_array="id, booking_type, is_short, entry_form, booking_no_prefix, booking_no_prefix_num, booking_no, company_id, buyer_id, job_no, po_break_down_id, item_category, fabric_source, booking_date, delivery_date, ready_to_approved, short_booking_type, uom, season_year, season_id, brand_id, remarks, inserted_by, insert_date, status_active, is_deleted";
		$data_array ="(".$id.",14,1,730,'".$new_booking_no[1]."',".$new_booking_no[2].",'".$new_booking_no[0]."',".$cbo_company_name.",".$cbo_buyer_name.",".$txt_job_no.",".$txt_order_no_id.",".$cbo_fabric_natu.",".$cbo_fabric_source.",".$txt_reqsn_date.",".$txt_delivery_date.",".$cbo_ready_to_approved.",".$cbo_short_booking_type.",".$cbouom.",".$cbo_season_year.",".$cbo_season_id.",".$cbo_brand_id.",'".$txt_remark."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		$rID=sql_insert("wo_booking_mst",$field_array,$data_array,0);
		//echo "10**".$rID; oci_rollback($con); disconnect($con); die;
		
		if($db_type==2 || $db_type==1 ){
			if($rID){
				oci_commit($con);
				echo "0**".$new_booking_no[0]."**".$id;
			}
			else{
				oci_rollback($con);
				echo "10**".$new_booking_no[0];
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1){
		$con = connect();

		$is_approved=0;
		$sql=sql_select("select is_approved from wo_booking_mst where booking_no=$txt_reqsn_no");
		foreach($sql as $row){
			$is_approved=$row[csf('is_approved')];
		}
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_reqsn_no);
			 disconnect($con);die;
		}
		
		$shortBookingno=return_field_value( "booking_no", "wo_booking_mst","requisition_no=$txt_reqsn_no and status_active=1 and is_deleted=0");
		if($shortBookingno){
			echo "shortBookingno**".str_replace("'","",$txt_reqsn_no)."**".$shortBookingno;
			disconnect($con);die;
		}

		$field_array="buyer_id*job_no*po_break_down_id*item_category*fabric_source*booking_date*delivery_date*ready_to_approved*short_booking_type*uom*season_year*season_id*brand_id*remarks*updated_by*update_date";
		 $data_array ="".$cbo_buyer_name."*".$txt_job_no."*".$txt_order_no_id."*".$cbo_fabric_natu."*".$cbo_fabric_source."*".$txt_reqsn_date."*".$txt_delivery_date."*".$cbo_ready_to_approved."*".$cbo_short_booking_type."*".$cbouom."*".$cbo_season_year."*".$cbo_season_id."*".$cbo_brand_id."*'".$txt_remark."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID=sql_update("wo_booking_mst",$field_array,$data_array,"id","".$update_id."",0);
		
		if($db_type==2 || $db_type==1 ){
			if($rID ){
				oci_commit($con);
				echo "1**".str_replace("'","",$txt_reqsn_no)."**".str_replace("'","",$update_id);
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_reqsn_no);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2){
		$con = connect();
		if($db_type==0){
			mysql_query("BEGIN");
		}
		$is_approved=0;
		$sql=sql_select("select is_approved from wo_booking_mst where booking_no=$txt_reqsn_no");
		foreach($sql as $row){
			$is_approved=$row[csf('is_approved')];
		}
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_reqsn_no);
			 disconnect($con);die;
		}
		
		$shortBookingno=return_field_value( "booking_no", "wo_booking_mst","requisition_no=$txt_reqsn_no and status_active=1 and is_deleted=0");
		if($shortBookingno){
			echo "shortBookingno**".str_replace("'","",$txt_reqsn_no)."**".$shortBookingno;
			disconnect($con);die;
		}

		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$rID=sql_delete("wo_booking_mst",$field_array,$data_array,"id","".$update_id."",1);
		
		if($db_type==2 || $db_type==1 ){
			if($rID ){
				oci_commit($con);
				echo "2**".str_replace("'","",$txt_reqsn_no);
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_reqsn_no);
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="save_update_delete_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0)  // Insert Here
	{
		$con = connect();

		if ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con); die;}
		$id=return_next_id( "id", "wo_booking_dtls", 1 ) ;
		
		$field_array="id, job_no, booking_mst_id, po_break_down_id, pre_cost_fabric_cost_dtls_id, booking_no, booking_type, is_short, entry_form_id, fabric_color_id, gmts_color_id, item_size, gmts_size, dia_width, fin_fab_qnty, process_loss_percent, grey_fab_qnty, rmg_qty, responsible_dept, responsible_person, reason, pre_cost_remarks, gsm_weight, inserted_by, insert_date, status_active, is_deleted";
		
		$pre_cost_remarks=return_field_value("remarks","wo_pre_cos_fab_co_avg_con_dtls","pre_cost_fabric_cost_dtls_id=$cbo_fabricdescription_id and po_break_down_id=$cbo_order_id and color_number_id =$cbo_garmentscolor_id and gmts_sizes=$cbo_garmentssize_id");
		
		$data_array="(".$id.",".$txt_job_no.",".$update_id.",".$cbo_order_id.",".$cbo_fabricdescription_id.",".$txt_reqsn_no.",14,1,730,".$cbo_fabriccolor_id.",".$cbo_garmentscolor_id.",".$cbo_itemsize_id.",".$cbo_garmentssize_id.",".$txt_dia_width.",".$txt_finish_qnty.",".$txt_process_loss.",".$txt_grey_qnty.",".$txt_rmg_qty.",".$cbo_responsible_dept.",".$cbo_responsible_person.",".$txt_reason.",'".$pre_cost_remarks."',".$txt_fabric_weight.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		//$id=$id+1;
		
		$rID=sql_insert("wo_booking_dtls",$field_array,$data_array,1);
		
		//echo "10**".$rID; oci_rollback($con); check_table_status( $_SESSION['menu_id'],0); disconnect($con); die;
		check_table_status( $_SESSION['menu_id'],0);
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "0**".str_replace("'","",$txt_reqsn_no);
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_reqsn_no);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)  // Insert Here
	{
		$con = connect();
		$is_approved=0;
		$sql=sql_select("select is_approved from wo_booking_mst where booking_no=$txt_reqsn_no");
		foreach($sql as $row){
			$is_approved=$row[csf('is_approved')];
		}
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_reqsn_no);
			disconnect($con);die;
		}
		
		$shortBookingno=return_field_value( "booking_no", "wo_booking_mst","requisition_no=$txt_reqsn_no and status_active=1 and is_deleted=0");
		if($shortBookingno){
			echo "shortBookingno**".str_replace("'","",$txt_reqsn_no)."**".$shortBookingno;
			disconnect($con);die;
		}

	    if(check_table_status( $_SESSION['menu_id'], 1)==0) {
			echo "15**1";
			disconnect($con);die;
		}
		
	    $field_array_up="job_no*po_break_down_id*pre_cost_fabric_cost_dtls_id*fabric_color_id*gmts_color_id*item_size*gmts_size*dia_width*fin_fab_qnty*process_loss_percent*grey_fab_qnty*rmg_qty*responsible_dept*responsible_person*reason*pre_cost_remarks*gsm_weight*updated_by*update_date";
		
		$pre_cost_remarks=return_field_value("remarks","wo_pre_cos_fab_co_avg_con_dtls","pre_cost_fabric_cost_dtls_id=$cbo_fabricdescription_id and po_break_down_id=$cbo_order_id and color_number_id =$cbo_garmentscolor_id and gmts_sizes=$cbo_garmentssize_id");

	    $data_array_up ="".$txt_job_no."*".$cbo_order_id."*".$cbo_fabricdescription_id."*".$cbo_fabriccolor_id."*".$cbo_garmentscolor_id."*".$cbo_itemsize_id."*".$cbo_garmentssize_id."*".$txt_dia_width."*".$txt_finish_qnty."*".$txt_process_loss."*".$txt_grey_qnty."*".$txt_rmg_qty."*".$cbo_responsible_dept."*".$cbo_responsible_person."*".$txt_reason."*'".$pre_cost_remarks."'*".$txt_fabric_weight."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
	    $rID=sql_update("wo_booking_dtls",$field_array_up,$data_array_up,"id","".$update_id_details."",0);
		
	    check_table_status( $_SESSION['menu_id'],0);
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "1**".str_replace("'","",$txt_reqsn_no)."**".$ss;
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_reqsn_no);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)  // Insert Here
	{
		$con = connect();
		$is_approved=0;
		$sql=sql_select("select is_approved from wo_booking_mst where booking_no=$txt_reqsn_no");
		foreach($sql as $row){
			$is_approved=$row[csf('is_approved')];
		}
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_reqsn_no);
			 disconnect($con);die;
		}
		
		$shortBookingno=return_field_value( "booking_no", "wo_booking_mst","requisition_no=$txt_reqsn_no and status_active=1 and is_deleted=0");
		if($shortBookingno){
			echo "shortBookingno**".str_replace("'","",$txt_reqsn_no)."**".$shortBookingno;
			disconnect($con);die;
		}
		$recv_number=return_field_value( "recv_number", "inv_receive_master"," booking_no=$txt_reqsn_no  and status_active=1 and  is_deleted=0");
		if($recv_number){
			echo "recv1**".str_replace("'","",$txt_reqsn_no)."**".$recv_number;
			disconnect($con);die;
		}
		
		//$rID=execute_query( "update wo_booking_dtls set status_active=0,is_deleted =1 where  id =$update_id_details",0);
		$rID = sql_delete("wo_booking_dtls","status_active*is_deleted","0*1",'id',$update_id_details,1);
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "2**".str_replace("'","",$txt_reqsn_no);
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_reqsn_no);
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="delete_requisition_item")
{
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
   $rID_de1=execute_query( "update wo_booking_dtls set status_active=0,is_deleted =1 where booking_no ='$data' and status_active=1 and is_deleted =0",0);
   //$rID_de1=execute_query( "delete from  wo_booking_dtls where  booking_no ='$data'",0);
   
	if($db_type==2 || $db_type==1 )
	{
		if($rID_de1)
		{
			oci_commit($con);
			//echo "0**".$new_job_no[0]."**".$rID;
		}
		else
		{
			oci_rollback($con);
			//echo "10**".$new_job_no[0]."**".$rID;
		}
	}
	disconnect($con);
}

if ($action=="fabric_requisition_popup")
{
  	echo load_html_head_contents("Requisition Search","../../../", 1, 1, $unicode);
  	extract($_REQUEST);
	//  print_r($_SESSION['logic_erp']['mandatory_field'][88]);
?>
	<script>
 	var company="<? echo $company; ?>";
	$('#cbo_company_mst').val(company);
	function set_checkvalue()
	{
		if(document.getElementById('chk_job_wo_po').value==0) document.getElementById('chk_job_wo_po').value=1;
		else document.getElementById('chk_job_wo_po').value=0;
	}

	function js_set_value(booking_no)
	{
		document.getElementById('selected_booking').value=booking_no;
		parent.emailwindow.hide();
	}
    </script>
</head>
<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
             <thead>
                <th colspan="12">
                  <?
                   echo create_drop_down( "cbo_search_category", 130, $string_search_type,'', 1, "-- Search Catagory --" );
                  ?>
                </th>
             </thead>
            <thead>
                <th width="130" class="must_entry_caption">Company Name</th>
                <th width="130" class="must_entry_caption">Buyer Name</th>
				<th width="70">Brand</th>
                <th width="70">Season</th>
                <th width="50">Season Year</th>
                <th width="80">Booking No</th>
                <th width="80">Job No</th>
                <th width="80">Internal Ref No</th>
                <th width="80">File No</th>
                <th width="120" colspan="2">Reqsn. Date Range</th>
                <th><input type="checkbox" value="0" onClick="set_checkvalue();" id="chk_job_wo_po">Orphan Reqsn.</th>
            </thead>
            <tr class="general">
                <td> <input type="hidden" id="selected_booking">
                	<script>
					    var buyer_name = <?php echo $buyer_id; ?>;
					</script>
                    <?
                        echo create_drop_down( "cbo_company_mst", 130, "select id,company_name from lib_company comp where status_active=1 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", $company, "load_drop_down( 'short_fabric_requisition_controller', this.value+'*'+1+'*'+buyer_name, 'load_drop_down_buyer', 'buyer_td' );");
                    ?>
                </td>
            <td id="buyer_td">
             <?
                echo create_drop_down( "cbo_buyer_name", 130, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --",$buyer_id,"load_drop_down( 'short_fabric_requisition_controller', this.value, 'load_drop_down_season', 'season_td'); load_drop_down( 'short_fabric_requisition_controller', this.value, 'load_drop_down_brand', 'brand_td');" );
				
            ?>	</td>
			<td id="brand_td"><? echo create_drop_down( "cbo_brand_id", 70, "select id, brand_name from lib_buyer_brand brand where buyer_id='$buyer_id' and status_active =1 and is_deleted=0 $brand_cond order by brand_name ASC","id,brand_name", 1, "--Select Brand--",str_replace("'","",$cbo_brand_id), "" ); ?>
					<td id="season_td"><? echo create_drop_down( "cbo_season_id", 70, "select id, season_name from lib_buyer_season where buyer_id='$buyer_id' and status_active =1 and is_deleted=0 order by season_name ASC",'id,season_name', 1, "--Select Season--",str_replace("'","",$cbo_season_id), "" ); ?></td>
			<td><?=create_drop_down( "cbo_season_year", 50, create_year_array(),"", 1,"-Year-", 1, "",0,"" ); ?></td>
            <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:70px"></td>
            <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:70px"></td>
            <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:70px"></td>
            <td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:70px"></td>
            <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px"></td>
            <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px"></td>
             <td align="center">
                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('cbo_brand_id').value+'_'+document.getElementById('cbo_season_id').value+'_'+document.getElementById('cbo_season_year').value, 'create_requisition_search_list_view', 'search_div', 'short_fabric_requisition_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
        </tr>
        <tr>
            <td align="center" colspan="12" valign="middle">
            	<? echo load_month_buttons(1);  ?>
            </td>
        </tr>
     </table>
    <div id="search_div"></div>
    </form>
   </div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action=="create_requisition_search_list_view")
{
	$data=explode('_',$data);
	if($data[0]==0 && $data[1]==0)
	{
		echo "<span style='color:red; font-weight:bold; font-size:20px; text-align:center'>Please select company or buyer first.";
		die;
	}
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else $company="";
	if ($data[1]!=0) $buyer=" and a.buyer_id='$data[1]'"; else $buyer="";
	$booking_year_cond=" ";
	if($db_type==0)
	 {
	 	if($data[5] !=0)
	 	{
	 		$booking_year_cond=" and year(a.booking_date)=$data[5]";
	 	}
		  
		  $year_cond=" and SUBSTRING_INDEX(b.insert_date, '-', 1)=$data[5]";
		  if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."'
		  and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
     }
	if($db_type==2)
	 {
	 	if($data[5] !=0)
	 	{
		  $booking_year_cond=" and to_char(a.booking_date,'YYYY')=$data[5]";
		}
		  $year_cond=" and to_char(b.insert_date,'YYYY')=$data[5]";
		  if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."'
		  and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	 }
	if($data[7]==4 || $data[7]==0)
	{
		if (str_replace("'","",$data[4])!="") $job_cond=" and b.job_no_prefix_num like '%$data[4]%' "; else  $job_cond="";
		if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[6]%' "; else $booking_cond="";
	}
	//echo $booking_cond; $booking_year_cond
    if($data[7]==1)
	{
		if (str_replace("'","",$data[4])!="") $job_cond=" and b.job_no_prefix_num ='$data[4]' "; else  $job_cond="";
		if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num ='$data[6]'   "; else $booking_cond="";
	}
   if($data[7]==2)
	{
		if (str_replace("'","",$data[4])!="") $job_cond=" and b.job_no_prefix_num like '$data[4]%' "; else  $job_cond="";
		if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num like '$data[6]%' "; else $booking_cond="";
	}//$year_cond $booking_year_cond
	if($data[7]==3)
	{
		if (str_replace("'","",$data[4])!="") $job_cond=" and b.job_no_prefix_num like '%$data[4]' "; else  $job_cond="";
		if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[6]' "; else $booking_cond="";
	}

	$internal_ref = str_replace("'","",$data[8]);
	$file_no = str_replace("'","",$data[9]);
	$chk_job_wo_po = str_replace("'","",$data[10]);

	//echo  $data[11]."*".$data[12]."*".$data[13];
	if($data[11] !=0) $brand_cond = " and b.brand_id='$data[11]'"; else $brand_cond="";
	if($data[12] !=0) $season_cond = " and b.season_buyer_wise='$data[12]'"; else $season_cond="";
	if($data[13] !=0) $season_year_cond = " and b.season_year='$data[13]'"; else $season_year_cond="";

	//echo $chk_job_wo_po.'ddd';die;
	if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and c.file_no='".trim($file_no)."' ";
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and c.grouping='".trim($internal_ref)."' ";

	$po_array=array();
	$sql_po= sql_select("select a.booking_no,a.po_break_down_id from wo_booking_mst  a where a.booking_type=1 $company $buyer $booking_date  and a.is_short=1 and   a.status_active=1  and 	a.is_deleted=0 order by a.booking_no");
	foreach($sql_po as $row)
	{
		$po_id=explode(",",$row[csf("po_break_down_id")]);
		$po_number_string="";$po_internal="";
		foreach($po_id as $key=> $value )
		{
			$po_number_string.=$po_number[$value].",";
		}
		//print_r( $po_internal);
		$po_array[$row[csf("po_break_down_id")]]=rtrim($po_number_string,",");
	}
	//print_r($po_array);die;
	 $approved=array(0=>"No",1=>"Yes",3=>"Yes");
	 $is_ready=array(0=>"No",1=>"Yes",2=>"No");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');

	if($chk_job_wo_po==0)
	{
		$sql= "SELECT a.id, a.booking_no_prefix_num, b.job_no_prefix_num, c.po_number, c.file_no, c.grouping, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.is_approved, a.ready_to_approved, b.brand_id, b.season_buyer_wise, b.season_year from wo_booking_mst a, wo_po_details_master b, wo_po_break_down c, wo_booking_dtls d where a.entry_form=730 and a.job_no=b.job_no and b.id=c.job_id and c.id=d.po_break_down_id and a.id=d.booking_mst_id $company $buyer $booking_date $job_cond $booking_cond $file_no_cond $internal_ref_cond".set_user_lavel_filtering(' and a.buyer_id','buyer_id')."  and a.booking_type=14 and a.is_short=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $booking_year_cond $brand_cond $season_cond $season_year_cond group by  a.id, a.booking_no_prefix_num, b.job_no_prefix_num, c.po_number, c.file_no, c.grouping, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.is_approved, a.ready_to_approved, b.brand_id ,b.season_buyer_wise, b.season_year order by a.id DESC";
	}
	else
	{
		$sql= "SELECT  a.id, a.booking_no_prefix_num, b.job_no_prefix_num, '' as po_number, '' as file_no, '' as grouping, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.is_approved, a.ready_to_approved, b.brand_id, b.season_year, b.season_buyer_wise from wo_booking_mst a, wo_po_details_master b where a.entry_form=730 and a.job_no=b.job_no $company $buyer $booking_date $job_cond $booking_cond $brand_cond $season_cond $season_year_cond ".set_user_lavel_filtering(' and a.buyer_id','buyer_id')." and a.booking_type=14 and a.is_short=1 and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and NOT EXISTS (SELECT booking_no FROM wo_booking_dtls c WHERE a.id=c.booking_mst_id) group by  a.id, a.booking_no_prefix_num, b.job_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.is_approved, a.ready_to_approved, b.brand_id, b.season_buyer_wise, b.season_year order by a.id DESC";
	}
	//echo $sql; die;
	?>
    <div style="1050px; margin-left:10px;">
    <table class="rpt_table" width="1050" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
        <thead>
            <th width="20">SL</th>
            <th width="50">Reqsn. No</th>
            <th width="60">Reqsn. Date</th>
            <th width="90">Buyer</th>
			<th width="60">Brand</th>
			<th width="60">Season</th>
			<th width="50">Season Year</th>

            <th width="90">Job No.</th>
            <th width="120">PO No.</th>
            <th width="80">File No</th>
            <th width="80">Internal Ref No</th>

            <th width="90">Fabric Nature</th>
            <th width="80">Fabric Source</th>

            <th width="50">Is App.</th>
            <th>Is-Ready</th>
        </thead>
        </table>
    <div style="max-height:300px; overflow-y:scroll; width:1050px" >
    <table width="1030" class="rpt_table" id="list_view" border="1" rules="all">
        <tbody>
        <?
		$i=1;
		$sql_data=sql_select($sql);
		$season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");
		$brand_arr=return_library_array("select id,brand_name from lib_buyer_brand","id","brand_name");
		foreach($sql_data as $row)
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
			<tr bgcolor="<? echo $bgcolor;?>" onClick="js_set_value('<? echo $row[csf('booking_no')]  ?>')" style="cursor:pointer">
                <td width="20" align="center"><? echo $i;?></td>
                <td width="50" align="center"><? echo $row[csf('booking_no_prefix_num')];?></td>
                <td width="60"><? echo date("d-m-Y",strtotime($row[csf('booking_date')]));?></td>
                <td width="90" style="word-break:break-all"><? echo $buyer_arr[$row[csf('buyer_id')]];?></td>
				<td width="60" style="word-break:break-all"><? echo $brand_arr[$row[csf('brand_id')]];?></td>
				<td width="60" style="word-break:break-all"><? echo $season_arr[$row[csf('season_buyer_wise')]];?></td>
				<td width="50" style="word-break:break-all"><? echo $row[csf('season_year')];?></td>
                <td width="90" style="word-break:break-all"><? echo $row[csf('job_no')];?></td>
                <td width="120" style="word-break:break-all"><? echo $row[csf('po_number')];?></td>
                <td width="80" style="word-break:break-all"><? echo $row[csf('file_no')];?></td>
                <td width="80" style="word-break:break-all"><? echo $row[csf('grouping')];?></td>
                <td width="90" style="word-break:break-all"><? echo $item_category[$row[csf('item_category')]];?></td>
                <td width="80" style="word-break:break-all"><? echo $fabric_source[$row[csf('fabric_source')]];?></td>

                <td width="50"><? echo $approved[$row[csf('is_approved')]];?></td>
                <td><? echo $is_ready[$row[csf('ready_to_approved')]];?></td>
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

if ($action=="populate_order_data_from_search_popup")
{
	// $user_id
	if($db_type==2) $group_concat_all=" listagg(cast(b.grouping as varchar2(4000)),',') within group (order by b.grouping) as grouping,
	                                    listagg(cast(b.file_no as varchar2(4000)),',') within group (order by b.file_no) as file_no  ";
	else { $group_concat_all="group_concat(b.grouping) as grouping, group_concat(b.file_no) as file_no";}

	$data_array=sql_select("select a.job_no, a.company_name, $group_concat_all, a.buyer_name, a.brand_id, a.season_buyer_wise, a.season_year from wo_po_details_master a, wo_po_break_down b where b.id in (".$data.") and a.job_no=b.job_no_mst group by a.job_no, a.company_name, a.buyer_name, a.brand_id, a.season_buyer_wise, a.season_year, a.body_wash_color");

	foreach ($data_array as $row)
	{
		$print_report_format2=return_field_value("format_id"," lib_report_template","template_name ='".$row[csf("company_name")]."'  and module_id=2 and report_id=92 and is_deleted=0 and status_active=1");
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_name")]."';\n";
		//echo "document.getElementById('report_ids').value = '".$print_report_format2."';\n";
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_name")]."';\n";
		echo "document.getElementById('cbo_season_year').value = '".$row[csf("season_year")]."';\n";
		echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n";
		$grouping=implode(",",array_unique(explode(",",$row[csf("grouping")])));
		$file_no=implode(",",array_unique(explode(",",$row[csf("file_no")])));
		echo "document.getElementById('txt_intarnal_ref').value = '".$grouping."';\n";
		echo "document.getElementById('txt_file_no').value = '".$file_no."';\n";
		
		echo "load_drop_down( 'requires/short_fabric_requisition_controller', '".$row[csf("buyer_name")]."'+'*'+0+'*'+'".$row[csf("season_buyer_wise")]."', 'load_drop_down_season', 'season_td' ) ;\n";
		echo "load_drop_down( 'requires/short_fabric_requisition_controller', '".$row[csf("buyer_name")]."'+'*'+0+'*'+'".$row[csf("brand_id")]."', 'load_drop_down_brand', 'brand_td' ) ;\n";
		
		echo "$( '#cbo_brand_id' ).prop( 'disabled', true );";
		echo "$( '#cbo_season_id' ).prop( 'disabled', true );";
		echo "$( '#cbo_season_year' ).prop( 'disabled', true );";
		echo "$( '#txt_rd_no' ).prop( 'disabled', true );";
		echo "$( '#txt_fabric_ref' ).prop( 'disabled', true );";
	}
	exit();
}

if ($action=="populate_data_from_search_popup")
{
	$company2=return_field_value("company_id","wo_booking_mst","booking_no ='".$data."' and is_deleted=0 and status_active=1");
	$print_report_format1=return_field_value("format_id","lib_report_template","template_name ='".$company2."' and module_id=2 and report_id=92 and is_deleted=0 and status_active=1");
	 $sql= "select id, booking_no, booking_date, company_id, buyer_id, season_year, season_id, brand_id, job_no, po_break_down_id, item_category, fabric_source, booking_date, delivery_date, is_approved, ready_to_approved, short_booking_type, uom, remarks, delivery_address from wo_booking_mst where booking_no='$data'";
	 
	 //echo $sql;
	 $data_array=sql_select($sql);
	 foreach ($data_array as $row)
	 {
		 $job_no=$row[csf("job_no")];
		//echo "document.getElementById('report_ids').value = '".$print_report_format1."';\n";
		echo "document.getElementById('txt_order_no_id').value = '".$row[csf("po_break_down_id")]."';\n";
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('txt_reqsn_no').value = '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('update_id').value = '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_fabric_natu').value = '".$row[csf("item_category")]."';\n";
		echo "document.getElementById('cbo_fabric_source').value = '".$row[csf("fabric_source")]."';\n";
		echo "document.getElementById('txt_reqsn_date').value = '".change_date_format($row[csf("booking_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('txt_delivery_date').value = '".change_date_format($row[csf("delivery_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('id_approved_id').value = '".$row[csf("is_approved")]."';\n";
		echo "document.getElementById('cbo_ready_to_approved').value = '".$row[csf("ready_to_approved")]."';\n";
		echo "document.getElementById('cbo_short_booking_type').value = '".$row[csf("short_booking_type")]."';\n";
		echo "document.getElementById('cbouom').value = '".$row[csf("uom")]."';\n";
		echo "document.getElementById('txt_remark').value = '".$row[csf("remarks")]."';\n";
		
		get_po_config($row[csf("po_break_down_id")].'_'.$row[csf("item_category")].'_'.$row[csf("fabric_source")].'_'.$row[csf("uom")]);
		
		$dataarr=sql_select("select a.brand_id, a.season_buyer_wise, a.season_year from wo_po_details_master a where job_no='$job_no'");
		echo "document.getElementById('cbo_season_year').value = '".$dataarr[0][csf("season_year")]."';\n";
		echo "load_drop_down( 'requires/short_fabric_requisition_controller', '".$row[csf("buyer_id")]."'+'*'+0+'*'+'".$dataarr[0][csf("season_buyer_wise")]."', 'load_drop_down_season', 'season_td' ) ;\n";
		echo "load_drop_down( 'requires/short_fabric_requisition_controller', '".$row[csf("buyer_id")]."'+'*'+0+'*'+'".$dataarr[0][csf("brand_id")]."', 'load_drop_down_brand', 'brand_td' ) ;\n";
		echo "$( '#cbo_buyer_name' ).prop( 'disabled', true );";
		echo "$( '#cbo_brand_id' ).prop( 'disabled', true );";
		echo "$( '#cbo_season_id' ).prop( 'disabled', true );";
		echo "$( '#cbo_season_year' ).prop( 'disabled', true );";
		echo "$( '#txt_rd_no' ).prop( 'disabled', true );";
		echo "$( '#txt_fabric_ref' ).prop( 'disabled', true );";
		if($db_type==2) $group_concat_all=" listagg(cast(b.grouping as varchar2(4000)),',') within group (order by b.grouping) as grouping,
	                                    listagg(cast(b.file_no as varchar2(4000)),',') within group (order by b.file_no) as file_no  ";
		else { $group_concat_all="group_concat(b.grouping) as grouping, group_concat(b.file_no) as file_no";}

		$data_array3=sql_select("select a.job_no,a.company_name,a.buyer_name,$group_concat_all from wo_po_details_master a, wo_po_break_down b where b.id in (".$row[csf("po_break_down_id")].") and a.job_no=b.job_no_mst group by a.job_no,a.company_name,a.buyer_name");
		foreach($data_array3 as $inv)
		{
			$grouping=implode(",",array_unique(explode(",",$inv[csf("grouping")])));
			$file_no=implode(",",array_unique(explode(",",$inv[csf("file_no")])));
			echo "document.getElementById('txt_file_no').value = '".$file_no."';\n";
			echo "document.getElementById('txt_intarnal_ref').value = '".$grouping."';\n";
		}

		if($row[csf("is_approved")]==1)
		{
			echo "document.getElementById('app_sms2').innerHTML = 'This booking is approved';\n";
		}
		if($row[csf("is_approved")]==3)
		{
			echo "document.getElementById('app_sms2').innerHTML = 'This booking is partial approved';\n";
		}
		$po_no="";
		$sql_po= "select po_number from  wo_po_break_down  where id in(".$row[csf('po_break_down_id')].")";
		$data_array_po=sql_select($sql_po);
		foreach ($data_array_po as $row_po)
		{
			$po_no.=$row_po[csf('po_number')].",";
		}
		echo "document.getElementById('txt_order_no').value = '".substr($po_no, 0, -1)."';\n";
	 }
	 exit();
}

if($action=="populate_details_data_from_for_update")
{
	$data_array=sql_select("select a.id, a.pre_cost_fabric_cost_dtls_id, a.po_break_down_id, a.fabric_color_id, a.gmts_color_id, a.item_size, a.gmts_size, a.dia_width, a.fin_fab_qnty, a.process_loss_percent, a.grey_fab_qnty, a.rmg_qty, a.responsible_dept, a.responsible_person, a.reason, b.company_id, b.gsm_weight, c.rd_no, c.construction FROM wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b, lib_yarn_count_determina_mst c WHERE a.pre_cost_fabric_cost_dtls_id=b.id and b.lib_yarn_count_deter_id=c.id and a.id ='".$data."' and a.is_short=1 and a.status_active=1 and a.is_deleted=0");
	$company_id=$data_array[0][csf('company_id')];
	
	foreach ($data_array as $row)
	{
		echo "document.getElementById('cbo_order_id').value = '".$row[csf("po_break_down_id")]."';\n";
		echo "document.getElementById('cbo_fabricdescription_id').value = '".$row[csf("pre_cost_fabric_cost_dtls_id")]."';\n";
		echo "document.getElementById('cbo_fabriccolor_id').value = '".$row[csf("fabric_color_id")]."';\n";
		echo "document.getElementById('cbo_garmentscolor_id').value = '".$row[csf("gmts_color_id")]."';\n";
		echo "document.getElementById('cbo_itemsize_id').value = '".$row[csf("item_size")]."';\n";
		echo "document.getElementById('cbo_garmentssize_id').value = '".$row[csf("gmts_size")]."';\n";
		echo "document.getElementById('txt_dia_width').value = '".$row[csf("dia_width")]."';\n";
		echo "document.getElementById('txt_finish_qnty').value = '".$row[csf("fin_fab_qnty")]."';\n";
		echo "document.getElementById('txt_process_loss').value = '".$row[csf("process_loss_percent")]."';\n";
		echo "document.getElementById('txt_grey_qnty').value = '".$row[csf("grey_fab_qnty")]."';\n";
		
		echo "document.getElementById('txt_rd_no').value = '".$row[csf("rd_no")]."';\n";
		echo "document.getElementById('txt_fabric_weight').value = '".$row[csf("gsm_weight")]."';\n";
		echo "document.getElementById('txt_fabric_ref').value = '".$row[csf("construction")]."';\n";
		echo "document.getElementById('txt_rmg_qty').value = '".$row[csf("rmg_qty")]."';\n";
		echo "set_multiselect('cbo_responsible_dept','0','1','".$row[csf("responsible_dept")]."','0');\n";
		echo "document.getElementById('txt_reason').value = '".$row[csf("reason")]."';\n";
		echo "document.getElementById('cbo_responsible_person').value = '".$row[csf("responsible_person")]."';\n";
		echo "document.getElementById('update_id_details').value = '".$row[csf("id")]."';\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_short_fabric_requisition_dtls',2);\n";
	}
	exit();
}

if($action=="print_booking_3")//unused
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);

	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' or form_name='knit_order_entry'  and file_type=1",'master_tble_id','image_location');

	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name");
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');

	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$location_name_arr=return_library_array( "select id,location_name from lib_location",'id','location_name');

	$pro_sub_dept_array=return_library_array( "select id,sub_department_name from lib_pro_sub_deparatment",'id','sub_department_name');
	$season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");
	$brand_arr=return_library_array("select id,brand_name from lib_buyer_brand","id","brand_name");
	
	$uom=0;
	$joball=array();
	$nameArray_per_job=sql_select( "select  a.job_no,a.style_ref_no  from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no=$txt_reqsn_no and b.status_active =1 and b.is_deleted=0  group by a.job_no,a.style_ref_no");
	foreach ($nameArray_per_job as $row_per_job){
	$joball['job_no'][$row_per_job[csf('job_no')]]=$row_per_job[csf('job_no')];
	$joball['style_ref_no'][$row_per_job[csf('job_no')]]=$row_per_job[csf('style_ref_no')];

	$uom=0;
	$job_data_arr=array();
	$nameArray_buyer=sql_select("select a.style_ref_no, a.style_description, a.job_no, a.style_owner, a.buyer_name, b.responsible_dept, b.responsible_person, b.reason, a.dealing_marchant, a.season, a.season_matrix, a.total_set_qnty, a.product_dept, a.product_code, a.pro_sub_dep, a.gmts_item_id, a.order_repeat_no, a.qlty_label from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no=$txt_reqsn_no and b.status_active =1 and b.is_deleted=0 and a.job_no='".$row_per_job[csf('job_no')]."'");
	foreach ($nameArray_buyer as $result_buy){
		$job_data_arr['job_no'][$result_buy[csf('job_no')]]=$result_buy[csf('job_no')];
		$job_data_arr['job_no_in'][$result_buy[csf('job_no')]]="'".$result_buy[csf('job_no')]."'";
		$job_data_arr['total_set_qnty'][$result_buy[csf('job_no')]]=$result_buy[csf('total_set_qnty')];
		$job_data_arr['product_dept'][$result_buy[csf('job_no')]]=$product_dept[$result_buy[csf('product_dept')]];
		$job_data_arr['product_code'][$result_buy[csf('job_no')]]=$result_buy[csf('product_code')];
		$job_data_arr['pro_sub_dep'][$result_buy[csf('job_no')]]=$pro_sub_dept_array[$result_buy[csf('pro_sub_dep')]];
		$job_data_arr['gmts_item_id'][$result_buy[csf('job_no')]]=$result_buy[csf('gmts_item_id')];
		$job_data_arr['style_ref_no'][$result_buy[csf('job_no')]]=$result_buy[csf('style_ref_no')];
		$job_data_arr['style_description'][$result_buy[csf('job_no')]]=$result_buy[csf('style_description')];
		$job_data_arr['dealing_marchant'][$result_buy[csf('job_no')]]=$marchentrArr[$result_buy[csf('dealing_marchant')]];
		$job_data_arr['season_matrix'][$result_buy[csf('job_no')]]=$season_arr[$result_buy[csf('season_matrix')]];
		$job_data_arr['order_repeat_no'][$result_buy[csf('job_no')]]=$result_buy[csf('order_repeat_no')];
		$job_data_arr['qlty_label'][$result_buy[csf('job_no')]]=$quality_label[$result_buy[csf('qlty_label')]];
	
		$job_data_arr['responsible_person'][$result_buy[csf('responsible_person')]]=$result_buy[csf('responsible_person')];
		$job_data_arr['responsible_dept'][$result_buy[csf('responsible_dept')]]=$result_buy[csf('responsible_dept')];
		$job_data_arr['reason'][$result_buy[csf('reason')]]=$result_buy[csf('reason')];
	}

	$job_no= implode(",",array_unique($job_data_arr['job_no']));
	$job_no_in= implode(",",array_unique($job_data_arr['job_no_in']));
	$product_depertment=implode(",",array_unique($job_data_arr['product_dept']));
	$product_code=implode(",",array_unique($job_data_arr['product_code']));
	$pro_sub_dep=implode(",",array_unique($job_data_arr['pro_sub_dep']));
	$gmts_item_id=implode(",",array_unique($job_data_arr['gmts_item_id']));
	$style_sting=implode(",",array_unique($job_data_arr['style_ref_no']));
	$style_description=implode(",",array_unique($job_data_arr['style_description']));
	$dealing_marchant=implode(",",array_unique($job_data_arr['dealing_marchant']));
	$season_matrix=implode(",",array_unique($job_data_arr['season_matrix']));
	$order_repeat_no= implode(",",array_unique($job_data_arr['order_repeat_no']));
	$qlty_label= implode(",",array_unique($job_data_arr['qlty_label']));
	?>
    <style>
@media print {
    .gg {page-break-after: always;}
}


@media print
{
     .page-break { height:0; page-break-before:always; margin:0; border-top:none; }
}

     body, p, span, td, a {font-size:10pt;font-family: Arial;}
     body{margin-left:2em; margin-right:2em; font-family: "Arial Narrow", Arial, sans-serif;}
    .page{
    height:947px;
    padding-top:5px;
    page-break-after : always;
    font-family: Arial, Helvetica, sans-serif;
    position:relative;
   border-bottom:1px solid #000;
   		  }



</style>
	<div style="width:1330px" align="center">

	<?php
$nameArray_approved = sql_select("select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_reqsn_no and b.entry_form=12 and a.status_active =1 and a.is_deleted=0");
list($nameArray_approved_row) = $nameArray_approved;
$nameArray_approved_date = sql_select("select b.approved_date as approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_reqsn_no and b.entry_form=12 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "' and a.status_active =1 and a.is_deleted=0");
list($nameArray_approved_date_row) = $nameArray_approved_date;
$nameArray_approved_comments = sql_select("select b.comments as comments from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_reqsn_no and b.entry_form=12 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "' and a.status_active =1 and a.is_deleted=0");
list($nameArray_approved_comments_row) = $nameArray_approved_comments;
$path = str_replace("'", "", $path);
if ($path != "") {
	$path = $path;
} else {
	$path = "../../";
}

?>										<!--    Header Company Information         -->
	<table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black" >
	<tr>
	<td width="100">
	<img  src='<? echo $path.$imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
	</td>
	<td width="1250">
	<table width="100%" cellpadding="0" cellspacing="0"  >
	<tr>
	<td align="center">
	<?php
echo $company_library[$cbo_company_name];
?>
	</td>
	<td rowspan="3" width="250">

	<span><b> Job No:&nbsp;&nbsp;<? echo trim($job_no,"'"); ?></b></span><br/>
	<?
	if($nameArray_approved_row[csf('approved_no')]>1)
	{
	?>
	<b> Revised No: <? echo $nameArray_approved_row[csf('approved_no')]-1; ?></b>
	<br/>
	Approved Date: <? echo $nameArray_approved_date_row[csf('approved_date')]; ?>
	<?
	}
	?>


	</td>
	</tr>
	<tr>
	<td align="center">
	<?
	$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
	if($txt_job_no!="")
	{
	$location=return_field_value( "location_name", "wo_po_details_master","job_no=$txt_job_no");
	}
	else
	{
	$location="";
	}

	foreach ($nameArray as $result)
	{
	echo  $location_name_arr[$location];
	?>

	Email Address: <? echo $result[csf('email')];?>
	Website No: <? echo $result[csf('website')]; ?>

	<?

	}

	?>
	</td>
	</tr>
	<tr>
	<td align="center">
	<strong><? if($report_title !=""){ echo $report_title;} else { echo "General Work Order";}?> &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:#F00"><? if(str_replace("'","",$id_approved_id) ==1){ echo "(Approved)";}else{echo "";}; ?> </font></strong>
	</td>
	</tr>
	</table>
	</td>
	</tr>
	</table>
	<?


	$po_data=array();
	if($db_type==0){
	$nameArray_job=sql_select( "select b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no, b.po_quantity,DATEDIFF(pub_shipment_date,po_received_date) date_diff,MIN(po_received_date) as po_received_date ,MIN(pub_shipment_date) as pub_shipment_date,MIN(b.insert_date) as insert_date,b.shiping_status  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_reqsn_no and a.status_active =1 and a.is_deleted=0 and a.job_no='".$row_per_job[csf('job_no')]."'  group by b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no, b.po_quantity,pub_shipment_date,po_received_date,b.insert_date,b.shiping_status ");
	}
	if($db_type==2){
	$nameArray_job=sql_select( "select b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no, b.po_quantity,(pub_shipment_date-po_received_date) date_diff,MIN(po_received_date) as po_received_date,MIN(pub_shipment_date) as pub_shipment_date,MIN(b.insert_date) as insert_date,b.shiping_status   from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_reqsn_no and a.status_active =1 and a.is_deleted=0 and a.job_no='".$row_per_job[csf('job_no')]."' group by b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no, b.po_quantity,pub_shipment_date,po_received_date,b.insert_date,b.shiping_status ");
	}
	foreach ($nameArray_job as $result_job){
		$po_data['po_id'][$result_job[csf('id')]]=$result_job[csf('id')];
		$po_data['po_number'][$result_job[csf('id')]]=$result_job[csf('po_number')];
		$po_data['leadtime'][$result_job[csf('id')]]=$result_job[csf('date_diff')];
		$po_data['po_quantity'][$result_job[csf('id')]]=$result_job[csf('po_quantity')];
		$po_data['po_received_date'][$result_job[csf('id')]]=change_date_format($result_job[csf('po_received_date')],'dd-mm-yyyy','-');
		$ddd=strtotime($result_job[csf('pub_shipment_date')]);
		$po_data['pub_shipment_date'][$ddd]=$ddd;

		$po_data['insert_date'][$result_job[csf('id')]]=$result_job[csf('insert_date')];

		if($result_job[csf('shiping_status')]==1){
		$shiping_status= "FP";
		}
		else if($result_job[csf('shiping_status')]==2){
		$shiping_status= "PS";
		}
		else if($result_job[csf('shiping_status')]==3){
		$shiping_status= "FS";
		}
		$po_data['shiping_status'][$result_job[csf('id')]]=$shiping_status;
		$po_data['file_no'][$result_job[csf('id')]]=$result_job[csf('file_no')];
		$po_data['grouping'][$result_job[csf('id')]]=$result_job[csf('grouping')];
	}
	$txt_order_no_id=implode(",",array_unique($po_data['po_id']));
	$leadtime=implode(",",array_unique($po_data['leadtime']));
	$po_quantity=array_sum($po_data['po_quantity']);
	$po_received_date=implode(",",array_unique($po_data['po_received_date']));
	$po_number=implode(",",array_unique($po_data['po_number']));
	$shipment_date=date('d-m-Y',min($po_data['pub_shipment_date']));
	$maxshipment_date=date('d-m-Y',max($po_data['pub_shipment_date']));
	//$shipment_date=implode(",",array_unique($po_data['pub_shipment_date']));
	$shiping_status=implode(",",array_unique($po_data['shiping_status']));
	$file_no=implode(",",array_unique($po_data['file_no']));
	$grouping=implode(",",array_unique($po_data['grouping']));


	$colar_excess_percent=0;
	$cuff_excess_percent=0;
	$rmg_process_breakdown=0;
	$nameArray=sql_select( "select a.buyer_id,a.booking_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.po_break_down_id,a.colar_excess_percent,a.cuff_excess_percent,a.delivery_date,a.is_apply_last_update,a.fabric_source,a.rmg_process_breakdown,a.insert_date,a.update_date,a.uom,a.remarks,a.pay_mode,a.fabric_composition from wo_booking_mst a  where   a.booking_no=$txt_reqsn_no and a.status_active =1 and a.is_deleted=0");
	foreach ($nameArray as $result)
	{
		$total_set_qnty=$result[csf('total_set_qnty')];
		$colar_excess_percent=$result[csf('colar_excess_percent')];
		$cuff_excess_percent=$result[csf('cuff_excess_percent')];
		$rmg_process_breakdown=$result[csf('rmg_process_breakdown')];
		foreach ($po_data['po_id'] as $po_id=>$po_val){
			$daysInHand.=(datediff('d',date('d-m-Y',time()),$po_data['pub_shipment_date'][$po_id])-1).",";
			$booking_date=$result[csf('update_date')];
			if($booking_date=="" || $booking_date=="0000-00-00 00:00:00"){
			$booking_date=$result[csf('insert_date')];
			}
			$WOPreparedAfter.=(datediff('d',$po_data['insert_date'][$po_id],$booking_date)-1).",";
		}
	?>
	<table width="100%" style="border:1px solid black;table-layout: fixed;" >
	<tr>
	<td colspan="6" valign="top" style="color:#F00"><? if($result[csf('is_apply_last_update')]==2){echo "Booking Info not synchronized with order entry and pre-costing. order entry or pre-costing has updated after booking entry.  Contact to ".$marchentrArr[$result[csf('dealing_marchant')]]; } else{ echo "";} ?></td>
	</tr>
	<tr>
	<td width="200"><span><b>Buyer/Agent Name</b></span></td>
	<td width="220">:&nbsp;<span><b><? echo $buyer_name_arr[$result[csf('buyer_id')]]; ?></b></span></td>
	<td width="200"><span><b>Dept.</b></span></td>
	<td width="220">:&nbsp;
	<?
	echo $product_depertment ;
	if($product_code !=""){
	echo " (".$product_code.")";
	}
	if($pro_sub_dep != ""){
	echo " (".$pro_sub_dep.")";
	}
	?>
	</td>
	<td width="200"><span><b>Order Qnty</b></span></td>
	<td>:&nbsp; <?  echo $po_quantity;//." ".$unit_of_measurement[$result[csf('order_uom')]] ; ?> </td>
	</tr>
	<tr>

	<td><b>Garments Item</b></td>
	<td>:&nbsp;
	<?
	$gmts_item_name="";
	$gmts_item=explode(',',$gmts_item_id);
	for($g=0;$g<=count($gmts_item); $g++)
	{
	$gmts_item_name.= $garments_item[$gmts_item[$g]].",";
	}
	echo rtrim($gmts_item_name,',');
	?>
	</td>
	<td><b>Booking Release Date</b></td>
	<td>:&nbsp;
	<?
	$booking_date=$result[csf('update_date')];
	if($booking_date=="" || $booking_date=="0000-00-00 00:00:00")
	{
	$booking_date=$result[csf('insert_date')];
	}
	echo change_date_format($booking_date,'dd-mm-yyyy','-','');
	?>&nbsp;&nbsp;&nbsp;</td>
	<td><b>Style Ref.</b>   </td>
	<td>:&nbsp;<b>
	<?
	echo $style_sting;
	?>
	</b>
	</td>
	</tr>
	<tr>
	<td><b>Style Des.</b></td>
	<td>:&nbsp;<? echo $style_description;?></td>
	<td><b>Season</b></td>
	<td>:&nbsp;<? echo $season_matrix; ?></td>
	<td><b>Dealing Merchant</b></td>
	<td>:&nbsp;<? echo $dealing_marchant; ?></td>
	</tr>

	<tr>
	<td><b>Supplier Name</b>   </td>
	<td>:&nbsp;
	<?
	if($result[csf('pay_mode')]==5 || $result[csf('pay_mode')]==3){
	echo $company_library[$result[csf('supplier_id')]];
	}
	else{
	echo $supplier_name_arr[$result[csf('supplier_id')]];
	}
	?>    </td>
	<td><b>Delivery Date</b></td>
	<td>:&nbsp;<? echo change_date_format( $result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>
	<td style="font-size:14px"><b>Booking No </b>   </td>
	<td style="font-size:15px">:&nbsp;<b><? echo $result[csf('booking_no')];?></b><? echo "(".$fabric_source[$result[csf('fabric_source')]].")"?> <? //echo "(".$unit_of_measurement[$result[csf('uom')]].")"; $uom=$result[csf('uom')];?></td>
	</tr>
	<tr>
	<td><b>Attention</b></td>
	<td  >:&nbsp;<? echo $result[csf('attention')]; ?></td>
	<td><b>Lead Time </b>   </td>
	<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;">:&nbsp;
	<?
	echo $leadtime;
	?>
	</td>
	<td><b>Po Received Date</b></td>
	<td  >:&nbsp;<? echo $po_received_date; ?></td>
	</tr>
	<tr>
	<td><b>Order No</b></td>
	<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;" colspan="3">:&nbsp;<? echo $po_number; ?></td>
	<td><b>Repeat No</b></td>
	<td  >:&nbsp;<? echo $order_repeat_no; ?></td>
	</tr>
	<tr>
	<td><b>Shipment Date</b></td>
<td colspan="3" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"> : First:&nbsp;<? echo rtrim($shipment_date,", "); //echo $max_pub_shipment_date; ?>, Last: <? echo $maxshipment_date; ?></td>
	<td><b>Quality Label</b></td>
	<td  >:&nbsp;<? echo $qlty_label; ?></td>
	</tr>
	</tr>
	<tr>
	<td><b>WO Prepared After</b></td>
	<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"> :&nbsp;
	<?
	$WOPreparedAfter=implode(",",array_unique(explode(",",chop($WOPreparedAfter,","))));
	echo $WOPreparedAfter.' Days' ;
	?></td>
 	<td><b>Ex-factory status</b></td>
	<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"> :&nbsp;
	<?
	echo $shiping_status;
	?></td>

	<td><b>Internal Ref No</b></td>
	<td> :&nbsp;<b><? echo $grouping; ?></b></td>



	</tr>
	<tr>

	<td><b>File no</b></td>
	<td> :&nbsp;<b><? echo  $file_no;?></b></td>
	<td><b>Currency</b></td>
	<td> :&nbsp;<b><? echo  $currency[$result[csf("currency_id")]];?></b></td>

	<td><b>Remarks</b></td>
	<td> :<? echo $result[csf('remarks')]?></td>


	</tr>

	<tr>
	<td><b>Fabric Composition</b></td>
	<td colspan="5"> :<? echo $result[csf('fabric_composition')]?></td>
	</tr>

	</table>
	<?
	}


	?>
	<br/>
	<!--  Here will be the main portion  -->
	<?
	$costing_per="";
	$costing_per_qnty=0;
	$costing_per_id=return_field_value( "costing_per", "wo_pre_cost_mst","job_no in($job_no_in)");
	if($costing_per_id==1)
	{
	$costing_per="1 Dzn";
	$costing_per_qnty=12;

	}
	if($costing_per_id==2)
	{
	$costing_per="1 Pcs";
	$costing_per_qnty=1;

	}
	if($costing_per_id==3)
	{
	$costing_per="2 Dzn";
	$costing_per_qnty=24;

	}
	if($costing_per_id==4)
	{
	$costing_per="3 Dzn";
	$costing_per_qnty=36;

	}
	if($costing_per_id==5)
	{
	$costing_per="4 Dzn";
	$costing_per_qnty=48;
	}
	$process_loss_method=return_field_value( "process_loss_method", "wo_pre_cost_fabric_cost_dtls","job_no in($job_no_in)");
 	$uom_arr=array(1=>"Pcs",12=>"Kg",23=>"Mtr",27=>"Yds");
	$p=1;
	/*foreach($uom_arr as $uom_id=>$uom_val)
	{ */
	if($cbo_fabric_source==1 or $cbo_fabric_source==2){
	$nameArray_fabric_description= sql_select("select a.id as fabric_cost_dtls_id, a.item_number_id, a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight,a.width_dia_type as width_dia_type , d.dia_width,d.pre_cost_remarks,a.uom,sum(d.fin_fab_qnty) as fin_fab_qntys,sum(d.grey_fab_qnty) as grey_fab_qntys,avg(d.rate) as rates,sum(d.amount) as amounts ,d.fabric_color_id  FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d
	WHERE a.job_no=b.job_no and
	a.id=b.pre_cost_fabric_cost_dtls_id and
	c.job_no_mst=a.job_no and
	c.id=b.color_size_table_id and
	b.po_break_down_id=d.po_break_down_id and
	b.color_number_id=d.gmts_color_id and

	b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
	a.job_no=d.job_no and
	a.id=d.pre_cost_fabric_cost_dtls_id
	and
	d.booking_no =$txt_reqsn_no and
	d.job_no='".$row_per_job[csf('job_no')]."' and

	d.status_active=1 and
	d.is_deleted=0 and
	b.cons>0
	group by a.id,a.item_number_id,a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,a.width_dia_type,d.dia_width,d.pre_cost_remarks,a.uom ,d.fabric_color_id order by a.body_part_id,d.fabric_color_id,a.uom  ");
	/*echo "<pre>";
	print_r($nameArray_fabric_description);*/

	if(count($nameArray_fabric_description)>0){

	?>

	<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
	<caption> <strong>Fabric Booking Details </strong> </caption>

	<tr>
	<th  width="30" align="center">SL</th>
	<th  width="440" align="left">Item Description</th>
	<th  width="110" align="center">Fabric Color</th>
 	<th  width="120" align="center">UOM</th>
	<th width='120' align="center">Finish Fab. Qty</th>
	<th width='120' align="center">Grey Fab. Qty</th>
	<th width='120' align="center">Avg Rate</th>
	<th width='100' align="center">Amount</th>

	</tr>
	<?
	$color_wise_process_loss=sql_select("select  a.body_part_id,b.fabric_color_id,avg(b.process_loss_percent) as loss FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls  b 	WHERE a.job_no=b.job_no  and  a.job_no='".$row_per_job[csf('job_no')]."' and b.is_short=1 and b.booking_no=$txt_reqsn_no   group by a.body_part_id,b.fabric_color_id
	");
	foreach($color_wise_process_loss as $val)
	{
		$loss_arr[$val[csf("body_part_id")]][$val[csf("fabric_color_id")]]=$val[csf("loss")];
	}


	foreach($nameArray_fabric_description as $val)
	{

		$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty,avg(d.rate) as rate,sum(d.fin_fab_qnty*d.rate) as amount FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls d
	WHERE
	a.job_no=d.job_no and
	a.id=d.pre_cost_fabric_cost_dtls_id and
	d.booking_no =$txt_reqsn_no and
	d.job_no='".$row_per_job[csf('job_no')]."' and
 	a.item_number_id='".$val[csf('item_number_id')]."' and
	a.body_part_id='".$val[csf('body_part_id')]."' and
	a.color_type_id='".$val[csf('color_type_id')]."' and
	a.construction='".$val[csf('construction')]."' and
	a.composition='".$val[csf('composition')]."' and
	a.gsm_weight='".$val[csf('gsm_weight')]."' and
 	d.pre_cost_remarks='".$val[csf('pre_cost_remarks')]."' and
	d.fabric_color_id='".$val[csf('fabric_color_id')]."' and
	d.status_active=1 and
	d.is_deleted=0
	");


	?>
	<tr>
	<td align="center"> <? echo $p; $p++;?></td>
 	<td align="left"> <? echo $body_part[$val[csf('body_part_id')]].','. $val[csf('construction')].','.$val[csf('composition')].','.$val[csf('gsm_weight')].',' .$val[csf('dia_width')].',' .$fabric_typee[$val[csf('width_dia_type')]].',' .$color_type[$val[csf('color_type_id')]]; ?> </td>
	<td  align="center">
	<?
	echo $color_library[$val[csf('fabric_color_id')]];
	?>
	</td>


	<td align="center"> <? echo $unit_of_measurement[$val[csf('uom')]]; ?></td>

	<td align="center"> <?   $greys= $color_wise_wo_sql_qnty[0][csf("fin_fab_qnty")];
	echo def_number_format($greys,2);
 	?> </td>



	<td align="center"> <? echo def_number_format($color_wise_wo_sql_qnty[0][csf("grey_fab_qnty")],2); ?> </td>
	<td align="center"> <? echo def_number_format($color_wise_wo_sql_qnty[0][csf("rate")],2); ?> </td>
 <td align="center"> <? echo def_number_format($color_wise_wo_sql_qnty[0][csf("amount")],2); ?> </td>


	<?

	$total_fin_fab_qnty +=str_replace(",", "", def_number_format($greys,2));
	$total_amount +=str_replace(",", "",def_number_format($color_wise_wo_sql_qnty[0][csf("amount")],2));
	$total_grey_fab_qnty +=str_replace(",", "",def_number_format($color_wise_wo_sql_qnty[0][csf("grey_fab_qnty")],2));
	$total_rate +=str_replace(",", "",def_number_format($color_wise_wo_sql_qnty[0][csf("rate")],2));


	?>



	</tr>
	<?

	}
	?>
	<tr style=" font-weight:bold">


	<td  align="right" colspan="4"><strong>Total</strong></td>
	<td align="center"><? echo def_number_format($total_fin_fab_qnty,2);?></td>
	<td align="center"><? echo def_number_format($total_grey_fab_qnty,2);?></td>
	<td align="center"><? echo def_number_format($total_rate,2);?></td>
	<td align="center"><? echo def_number_format($total_amount,2);?></td>

	</tr>

	</table>
	<br/>
	<?
	}
	}

		?>

      <br/>
       <br/>
        <!--New Start here-->
        <div style="width:1330px; float:left">
        <?
		// Body Part type used only Cuff and Flat Knit
        $colar_percent_size_wise_array=return_library_array( "select a.colar_cuff_per,b.gmts_sizes from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b,wo_pre_cost_fabric_cost_dtls c  where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.pre_cost_fabric_cost_dtls_id=c.id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.color_size_table_id and a.booking_no=$txt_reqsn_no and a.booking_type=1 and a.body_part_type in(40,50) and a.status_active=1 and a.is_deleted=0", "gmts_sizes", "colar_cuff_per");

		//echo  "select a.body_part_type,a.body_part_id,sum(d.rmg_qty) as rmg_qty,d.gmts_size,d.gmts_color_id FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls d WHERE a.job_no=d.job_no and d.booking_no =$txt_reqsn_no and  a.id=d.pre_cost_fabric_cost_dtls_id  and d.status_active=1 and d.is_deleted=0 and d.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") group by a.body_part_type,a.body_part_id,d.gmts_size,d.gmts_color_id order by a.body_part_id";

		$nameArray_body_part=sql_select( "select a.body_part_type,a.body_part_id,sum(d.rmg_qty) as rmg_qty,d.gmts_size,d.gmts_color_id FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls d WHERE a.job_no=d.job_no and d.booking_no =$txt_reqsn_no and  a.id=d.pre_cost_fabric_cost_dtls_id  and d.status_active=1 and d.is_deleted=0 and d.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and a.body_part_type in(40,50) group by a.body_part_type,a.body_part_id,d.gmts_size,d.gmts_color_id order by a.body_part_id");
			$row_count=count($nameArray_body_part);
		//if($row_count==0) echo " <p style='color:#f00; text-align:center; font-size:15px;'> Body part type is  used only Flat Knit and Cuff.</p> ";
		foreach($nameArray_body_part as $row)
		{
			$body_part_arr[$row[csf('body_part_id')]]['bpart_type']=$row[csf('body_part_type')];
			$body_part_rmg_qty_arr[$row[csf('body_part_id')]][$row[csf('gmts_size')]][$row[csf('gmts_color_id')]]['rmg_qty']+=$row[csf('rmg_qty')];
		}
		//print_r($body_part_arr);

		$k=1;
		foreach($body_part_arr as $body_id=>$val)
		{
			$k++;

			$bpart_type_id=$val['bpart_type'];
		$nameArray_item_size=sql_select( "select min(c.id) as id,b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_reqsn_no  and a.body_part_id=$body_id  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id  and d.status_active=1 and d.is_deleted=0 and a.body_part_type in(40,50) group by b.item_size,c.size_number_id order by id");

		/*$nameArray_item_size=sql_select( "select b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_reqsn_no  and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 group by c.size_number_id,b.item_size order by c.size_number_id");*/
		?>
         <div style="max-height:1330px; width:660px; overflow:auto; float:left; padding-top:20px; margin-left:5px; margin-bottom:5px; position: relative;">
        <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
        <tr>
        <td colspan="<? echo count($nameArray_size)+4;?>" align="center"><b><? echo $body_part[$body_id];?> -  Colour Size Breakedown in Pcs</b></td>
        </tr>
        <tr>
        <td width="70">Size</td>
        <?
		foreach($nameArray_item_size  as $result_size)
		{
		?>
		<td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
		<?
        }
        ?>
        <td rowspan="2" align="center"><strong>Total</strong></td>

        </tr>
        <tr>
        <td>Collar Size</td>

        <?
        foreach($nameArray_item_size  as $result_item_size)
		{
		?>
		<td align="center" style="border:1px solid black"><strong><? echo $result_item_size[csf('item_size')];?></strong></td>
		<?
        }
        ?>
         <?

			$color_wise_wo_sql=sql_select("select a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method,c.color_number_id,sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_reqsn_no and a.body_part_id=$body_id  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 and a.body_part_type in(40,50) group by c.color_number_id,a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method order by a.id
");
		foreach($color_wise_wo_sql as $color_wise_wo_result)
	    {
			$color_total_collar=0;
			$color_total_collar_order_qnty=0;
			$process_loss_method=$color_wise_wo_result[csf("process_loss_method")];
			$constrast_color_arr=array();
			if($color_wise_wo_result[csf("color_size_sensitive")]==3)
			{
				$constrast_color=explode('__',$color_wise_wo_result[csf("color_break_down")]);
				for($i=0;$i<count($constrast_color);$i++)
				{
					$constrast_color2=explode('_',$constrast_color[$i]);
					$constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
				}
			}
		?>
			<tr>
            <td>
            <?
			if($color_wise_wo_result[csf("color_size_sensitive")]==3)
			{
				echo strtoupper ($constrast_color_arr[$color_wise_wo_result[csf('color_number_id')]]) ;
				$lab_dip_color_id=return_field_value("contrast_color_id","wo_pre_cos_fab_co_color_dtls","job_no='".$color_wise_wo_result[csf('job_no')]."' and pre_cost_fabric_cost_dtls_id=".$color_wise_wo_result[csf('id')]." and gmts_color_id=".$color_wise_wo_result[csf('color_number_id')]."");
			}
			else
			{
				echo $color_library[$color_wise_wo_result[csf('color_number_id')]];
				$lab_dip_color_id=$color_wise_wo_result[csf('color_number_id')];
			}
			?>

            </td>
            <?
            foreach($nameArray_item_size  as $result_size)
			{
				?>
				<td align="center" style="border:1px solid black">

				<?
				$rmg_qty=$body_part_rmg_qty_arr[$body_id][$result_size[csf('size_number_id')]][$color_wise_wo_result[csf('color_number_id')]]['rmg_qty'];
				//echo $bpart_type_id.'=';
				if($bpart_type_id==50)//Cuff
				{
					$tot_rmg_qty=$rmg_qty*2;
				}
				else //Flat Knit
				{
					$tot_rmg_qty=$rmg_qty;
				}
				//$color_wise_wo_sql_qnty=sql_select("select c.color_number_id,sum(c.order_quantity) as order_quantity, sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_reqsn_no and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 and c.color_number_id='".$color_wise_wo_result['color_number_id']."' and c.size_number_id='".$result_size['size_number_id']."'");
				/*$color_wise_wo_sql_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$color_wise_wo_result[csf('color_number_id')]."   and  status_active=1 and is_deleted =0");

				list($plan_cut_qnty)=$color_wise_wo_sql_qnty;*/
				//$plan_cut=$plan_cut_qnty[csf('plan_cut_qnty')];
				//$colar_excess_per=($plan_cut*$colar_excess_percent)/100;
				echo number_format($tot_rmg_qty,0);
				$color_total_collar+=$tot_rmg_qty;
				$color_total_collar_order_qnty+=$tot_rmg_qty;
				$grand_total_collar+=$tot_rmg_qty;
				$grand_total_collar_order_qnty+=$tot_rmg_qty;

				$size_tatal[$result_size[csf('size_number_id')]]+=$tot_rmg_qty;
				?>
                </td>
				<?
			}
			?>

            <td align="center"><? echo number_format($color_total_collar,0); ?></td>

            </tr>
            <?
		    }
			?>
            <tr>
                <td>Size Total</td>

                <?
                foreach($nameArray_item_size  as $result_size)
                {
					//$colar_excess_pers=($size_tatal[$result_size[csf('size_number_id')]]*$colar_excess_percent)/100;
					$tot_size_tatal=$size_tatal[$result_size[csf('size_number_id')]];
					//$size_tatal[$result_size[csf('size_number_id')]]=0;
                ?>
                <td style="border:1px solid black;  text-align:center"><?  echo number_format($size_tatal[$result_size[csf('size_number_id')]],0);$size_tatal[$result_size[csf('size_number_id')]]=0; ?></td>
                <?
                }
                ?>
                <td  style="border:1px solid black;  text-align:center"><?  echo number_format($grand_total_collar,0);$grand_total_collar=0; ?></td>

            </tr>
        </table>
          <br/>
          </div>

        <?
		}

		?>
          <!--End here-->
       </div>
        <table style="display:none"  width="100%"  border="0" cellpadding="0" cellspacing="0" >
        <tr>
        <?
		//echo "select a.colar_cuff_per,b.gmts_sizes from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b,wo_pre_cost_fabric_cost_dtls c  where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.pre_cost_fabric_cost_dtls_id=c.id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.color_size_table_id    and a.booking_no=$txt_reqsn_no and a.booking_type=1 c.body_part_id in(2) and a.status_active=1 and a.is_deleted=0";
		$colar_percent_size_wise_array=return_library_array( "select a.colar_cuff_per,b.gmts_sizes from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b,wo_pre_cost_fabric_cost_dtls c  where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.pre_cost_fabric_cost_dtls_id=c.id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.color_size_table_id    and a.booking_no=$txt_reqsn_no and a.booking_type=1 and c.body_part_id in(2) and a.status_active=1 and a.is_deleted=0", "gmts_sizes", "colar_cuff_per");
		//print_r($colar_percent_size_wise_array);
		$nameArray_item_size=sql_select( "select min(c.id) as id,b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_reqsn_no  and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id  and d.status_active=1 and d.is_deleted=0 group by b.item_size,c.size_number_id order by id");
		if(count($nameArray_item_size)>0)
		{
        ?>
        <td width="49%">
        <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
        <tr>
        <td colspan="<? echo count($nameArray_size)+4;?>" align="center"><b>Collar -  Colour Size Brakedown in Pcs</b></td>
        </tr>
        <tr>
        <td width="70">Size</td>

        <?

		/*$nameArray_item_size=sql_select( "select b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_reqsn_no  and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 group by c.size_number_id,b.item_size order by c.size_number_id");*/
		foreach($nameArray_item_size  as $result_size)
		{
		?>
		<td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
		<?
        }
        ?>
        <td rowspan="2" align="center"><strong>Total</strong></td>
        <td width="60" rowspan="2" align="center"><strong>Extra %</strong></td>
        </tr>
        <tr>
        <td>Collar Size</td>

        <?
        foreach($nameArray_item_size  as $result_item_size)
		{
		?>
		<td align="center" style="border:1px solid black"><strong><? echo $result_item_size[csf('item_size')];?></strong></td>
		<?
        }
        ?>
         <?

			$color_wise_wo_sql=sql_select("select a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method,c.color_number_id,sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_reqsn_no and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 group by c.color_number_id,a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method order by a.id
");
		foreach($color_wise_wo_sql as $color_wise_wo_result)
	    {
			$color_total_collar=0;
			$color_total_collar_order_qnty=0;
			$process_loss_method=$color_wise_wo_result[csf("process_loss_method")];
			$constrast_color_arr=array();
			if($color_wise_wo_result[csf("color_size_sensitive")]==3)
			{
				$constrast_color=explode('__',$color_wise_wo_result[csf("color_break_down")]);
				for($i=0;$i<count($constrast_color);$i++)
				{
					$constrast_color2=explode('_',$constrast_color[$i]);
					$constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
				}
			}
		?>
			<tr>
            <td>
            <?
			if($color_wise_wo_result[csf("color_size_sensitive")]==3)
			{
				echo strtoupper ($constrast_color_arr[$color_wise_wo_result[csf('color_number_id')]]) ;
				$lab_dip_color_id=return_field_value("contrast_color_id","wo_pre_cos_fab_co_color_dtls","job_no='".$color_wise_wo_result[csf('job_no')]."' and pre_cost_fabric_cost_dtls_id=".$color_wise_wo_result[csf('id')]." and gmts_color_id=".$color_wise_wo_result[csf('color_number_id')]."");
			}
			else
			{
				echo $color_library[$color_wise_wo_result[csf('color_number_id')]];
				$lab_dip_color_id=$color_wise_wo_result[csf('color_number_id')];
			}
			?>

            </td>
            <?
            foreach($nameArray_item_size  as $result_size)
			{
				?>
				<td align="center" style="border:1px solid black">
				<?
				//$color_wise_wo_sql_qnty=sql_select("select c.color_number_id,sum(c.order_quantity) as order_quantity, sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_reqsn_no and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 and c.color_number_id='".$color_wise_wo_result['color_number_id']."' and c.size_number_id='".$result_size['size_number_id']."'");
				$color_wise_wo_sql_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$color_wise_wo_result[csf('color_number_id')]."   and  status_active=1 and is_deleted =0");

				list($plan_cut_qnty)=$color_wise_wo_sql_qnty;
				$plan_cut=$plan_cut_qnty[csf('plan_cut_qnty')];
				$colar_excess_per=($plan_cut*$colar_excess_percent)/100;
				echo number_format($plan_cut+$colar_excess_per,0);
				$color_total_collar+=$plan_cut+$colar_excess_per;
				$color_total_collar_order_qnty+=$plan_cut;
				$grand_total_collar+=$plan_cut+$colar_excess_per;
				$grand_total_collar_order_qnty+=$plan_cut;

				$size_tatal[$result_size[csf('size_number_id')]]+=$plan_cut+$colar_excess_per;
				?>
                </td>
				<?
			}
			?>

            <td align="center"><? echo number_format($color_total_collar,0); ?></td>
            <td align="center"><? echo number_format((($color_total_collar-$color_total_collar_order_qnty)/$color_total_collar_order_qnty)*100,2); ?></td>
            </tr>
            <?
		    }
			?>
            <tr>
                <td>Size Total</td>

                <?
                foreach($nameArray_item_size  as $result_size)
                {
                ?>
                <td style="border:1px solid black;  text-align:center"><? $colar_excess_pers=($size_tatal[$result_size[csf('size_number_id')]]*$colar_excess_percent)/100; echo number_format($size_tatal[$result_size[csf('size_number_id')]]+$colar_excess_pers,0); ?></td>
                <?
                }
                ?>
                <td  style="border:1px solid black;  text-align:center"><?  echo number_format($grand_total_collar,0); ?></td>
                <td align="center" style="border:1px solid black"> <? echo number_format((($grand_total_collar-$grand_total_collar_order_qnty)/$grand_total_collar_order_qnty)*100,2); ?></td>
            </tr>
        </table>
        </td>
        <td width="2%">
        </td>
        <?
        }
		?>

        <?
		$cuff_percent_size_wise_array=return_library_array( "select a.colar_cuff_per,b.gmts_sizes from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b,wo_pre_cost_fabric_cost_dtls c  where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.pre_cost_fabric_cost_dtls_id=c.id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.color_size_table_id    and a.booking_no=$txt_reqsn_no and a.booking_type=1 and c.body_part_id in(3) and a.status_active=1 and a.is_deleted=0", "gmts_sizes", "colar_cuff_per");
		//print_r($cuff_percent_size_wise_array);
		$nameArray_item_size=sql_select( "select min(c.id) as id, b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_reqsn_no  and a.body_part_id=3  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 group by b.item_size,c.size_number_id order by id");

		if(count($nameArray_item_size)>0)
		{
        ?>
        <td width="49%">
        <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
        <tr>
        <td colspan="<? echo count($nameArray_size)+4;?>" align="center"><b>Cuff -  Colour Size Brakedown in Pcs</b></td>
        </tr>
        <tr>
        <td width="70">Size</td>

        <?
		foreach($nameArray_item_size  as $result_size)
		{
		?>
		<td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
		<?
        }
        ?>
        <td rowspan="2" align="center"><strong>Total</strong></td>
        <td width="60" rowspan="2" align="center"><strong>Extra %</strong></td>
        </tr>
        <tr>
        <td>Cuff Size</td>

        <?
        foreach($nameArray_item_size  as $result_item_size)
		{
		?>
		<td align="center" style="border:1px solid black"><strong><? echo $result_item_size[csf('item_size')];?></strong></td>
		<?
        }
        ?>
         <?

			$color_wise_wo_sql=sql_select("select a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method,c.color_number_id,sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_reqsn_no and a.body_part_id=3  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id and  d.status_active=1 and d.is_deleted=0 group by c.color_number_id ,a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method order by a.id
");
		foreach($color_wise_wo_sql as $color_wise_wo_result)
	    {
			$color_total_cuff=0;
			$color_total_cuff_order_qnty=0;
			$process_loss_method=$color_wise_wo_result[csf("process_loss_method")];
			$constrast_color_arr=array();
			if($color_wise_wo_result[csf("color_size_sensitive")]==3)
			{
				$constrast_color=explode('__',$color_wise_wo_result[csf("color_break_down")]);
				for($i=0;$i<count($constrast_color);$i++)
				{
					$constrast_color2=explode('_',$constrast_color[$i]);
					$constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
				}
			}
			//print_r($constrast_color_arr);
		?>
			<tr>
            <td>
            <?
			if($color_wise_wo_result[csf("color_size_sensitive")]==3)
			{
				///echo $color_wise_wo_result[csf('color_number_id')];
				echo strtoupper ($constrast_color_arr[$color_wise_wo_result[csf('color_number_id')]]);
				$lab_dip_color_id=return_field_value("contrast_color_id","wo_pre_cos_fab_co_color_dtls","job_no='".$color_wise_wo_result[csf('job_no')]."' and pre_cost_fabric_cost_dtls_id=".$color_wise_wo_result[csf('id')]." and gmts_color_id=".$color_wise_wo_result[csf('color_number_id')]."");
			}
			else
			{
				echo $color_library[$color_wise_wo_result[csf('color_number_id')]];
				$lab_dip_color_id=$color_wise_wo_result[csf('color_number_id')];
			}
			?>

            </td>
            <?
            foreach($nameArray_item_size  as $result_size)
			{
				?>
				<td align="center" style="border:1px solid black">

				<?
				/*$color_wise_wo_sql_qnty=sql_select("select c.color_number_id,sum(c.order_quantity) as order_quantity, sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_reqsn_no and a.body_part_id=3 and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 and c.color_number_id='".$color_wise_wo_result['color_number_id']."' and c.size_number_id='".$result_size['size_number_id']."'");*/
				$color_wise_wo_sql_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$color_wise_wo_result[csf('color_number_id')]."   and  status_active=1 and is_deleted =0");

				list($plan_cut_qnty)=$color_wise_wo_sql_qnty;
				$plan_cut=$plan_cut_qnty[csf('plan_cut_qnty')];
				$cuff_excess_per=(($plan_cut*2)*$cuff_excess_percent)/100;
				echo number_format($plan_cut*2+$cuff_excess_per,0);
				$color_total_cuff+=$plan_cut*2+$cuff_excess_per;
				$color_total_cuff_order_qnty+=$plan_cut*2;
				$grand_total_cuff+=$plan_cut*2+$cuff_excess_per;
				$grand_total_cuff_order_qnty+=$plan_cut*2;
				$size_tatal[$result_size[csf('size_number_id')]]+=($plan_cut*2+$cuff_excess_per);
				/*$cuff_excess_per=(($plan_cut_qnty[csf('plan_cut_qnty')]*2)*$cuff_excess_percent)/100;
				echo number_format($plan_cut_qnty[csf('plan_cut_qnty')]*2+$cuff_excess_per,0);
				$color_total_cuff+=$plan_cut_qnty[csf('plan_cut_qnty')]*2+$cuff_excess_per;
				$color_total_cuff_order_qnty+=$plan_cut_qnty[csf('order_quantity')]*2;
				$grand_total_cuff+=$plan_cut_qnty[csf('plan_cut_qnty')]*2+$cuff_excess_per;
				$grand_total_cuff_order_qnty+=$plan_cut_qnty[csf('order_quantity')]*2;*/

				?>

                </td>
				<?
			}
			?>

            <td align="center"><? echo number_format($color_total_cuff,0); ?></td>
            <td align="center"><? echo number_format((($color_total_cuff-$color_total_cuff_order_qnty)/$color_total_cuff_order_qnty)*100,2); ?></td>
            </tr>
            <?
		    }
			?>
            <tr>
                <td>Size Total</td>

                <?
                foreach($nameArray_item_size  as $result_size)
                {
                   /* $nameArray_size_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =$result_size[size_number_id]  and status_active=1 and is_deleted =0 order by id");
                foreach($nameArray_size_qnty as $result_size_qnty)
                {*/
                ?>
                <td style="border:1px solid black;  text-align:center"><? $cuff_excess_pers=(($size_tatal[$result_size[csf('size_number_id')]]*2)*$cuff_excess_percent)/100; echo number_format($size_tatal[$result_size[csf('size_number_id')]]*2+$cuff_excess_pers,0); ?></td>
                <?
                //}
                }
                ?>
                <td  style="border:1px solid black;  text-align:center"><?  echo number_format($grand_total_cuff,0); ?></td>
                <td align="center" style="border:1px solid black"> <? echo number_format((($grand_total_cuff-$grand_total_cuff_order_qnty)/$grand_total_cuff_order_qnty)*100,2); ?></td>
            </tr>
        </table>
        </td>
        <?
				}
		?>
        </tr>
        </table>
        <br/>
        <table  width="100%"  border="0" cellpadding="0" cellspacing="0" >
        <tr>
        <?

		//echo "select a.colar_cuff_per,b.gmts_sizes from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b,wo_pre_cost_fabric_cost_dtls c  where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.pre_cost_fabric_cost_dtls_id=c.id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.color_size_table_id    and a.booking_no=$txt_reqsn_no and a.booking_type=1 c.body_part_id in(2) and a.status_active=1 and a.is_deleted=0";
		//$colar_percent_size_wise_array=return_library_array( "select a.colar_cuff_per,b.gmts_sizes from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b,wo_pre_cost_fabric_cost_dtls c  where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.pre_cost_fabric_cost_dtls_id=c.id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.color_size_table_id    and a.booking_no=$txt_reqsn_no and a.booking_type=1 and c.body_part_id in(2) and a.status_active=1 and a.is_deleted=0", "gmts_sizes", "colar_cuff_per");


		$colar_percent_size_wise_array=array();
		$colar_tipping_percent_size_wise_sql=sql_select( "select a.colar_cuff_per,b.color_number_id,b.gmts_sizes from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b,wo_pre_cost_fabric_cost_dtls c  where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.pre_cost_fabric_cost_dtls_id=c.id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.color_size_table_id    and a.booking_no=$txt_reqsn_no and a.booking_type=1 and c.body_part_id in(172) and a.status_active=1 and a.is_deleted=0");
		$colar_tipping_excess_percent_arr=array();
		foreach($colar_tipping_percent_size_wise_sql as $colar_percent_size_wise_row)
		{
			$colar_tipping_excess_percent_arr[$colar_percent_size_wise_row[csf('color_number_id')]][$colar_percent_size_wise_row[csf('gmts_sizes')]]=$colar_percent_size_wise_row[csf('colar_cuff_per')];
			//$colar_excess_percent_arr[$colar_percent_size_wise_row[csf('gmts_sizes')]]+=$colar_percent_size_wise_row[csf('colar_cuff_per')];

		}

		//print_r($colar_tipping_excess_percent_arr);
		$nameArray_item_size=sql_select( "select min(c.id) as id,b.item_size,c.size_number_id,	min(c.size_order) as size_order FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_reqsn_no  and a.body_part_id=172  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id  and d.status_active=1 and d.is_deleted=0 group by b.item_size,c.size_number_id order by size_order");


		if(count($nameArray_item_size)>0)
		{
        ?>
        <td width="49%">
        <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
        <tr>
        <td colspan="<? echo count($nameArray_size)+4;?>" align="center"><b>Collar Tipping -  Colour Size Brakedown in Pcs</b></td>
        </tr>
        <tr>
        <td width="70">Size</td>

        <?

		/*$nameArray_item_size=sql_select( "select b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_reqsn_no  and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 group by c.size_number_id,b.item_size order by c.size_number_id");*/
		foreach($nameArray_item_size  as $result_size)
		{
		?>
		<td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
		<?
        }
        ?>
        <td rowspan="2" align="center"><strong>Total</strong></td>
        <td width="60" rowspan="2" align="center"><strong>Extra %</strong></td>
        </tr>
        <tr>
        <td>Collar Size</td>

        <?
        foreach($nameArray_item_size  as $result_item_size)
		{
		?>
		<td align="center" style="border:1px solid black"><strong><? echo $result_item_size[csf('item_size')];?></strong></td>
		<?
        }

		$color_wise_wo_sql=sql_select("select a.id, a.job_no, a.color_size_sensitive, a.color_break_down, a.process_loss_method, c.color_number_id , c.color_order, sum(c.plan_cut_qnty) as plan_cut_qnty
		FROM wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c, wo_booking_dtls d
		WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_reqsn_no and a.body_part_id=172 and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0
		group by c.color_number_id, c.color_order, a.id, a.job_no, a.color_size_sensitive,a.color_break_down, a.process_loss_method
		order by c.color_order ");

		foreach($color_wise_wo_sql as $color_wise_wo_result)
	    {
			$color_total_collar=0;
			$color_total_collar_order_qnty=0;
			$process_loss_method=$color_wise_wo_result[csf("process_loss_method")];
			$constrast_color_arr=array();
			if($color_wise_wo_result[csf("color_size_sensitive")]==3)
			{
				$constrast_color=explode('__',$color_wise_wo_result[csf("color_break_down")]);
				for($i=0;$i<count($constrast_color);$i++)
				{
					$constrast_color2=explode('_',$constrast_color[$i]);
					$constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
				}
			}
		?>
			<tr>
            <td>
            <?
			if($color_wise_wo_result[csf("color_size_sensitive")]==3)
			{
				echo strtoupper ($constrast_color_arr[$color_wise_wo_result[csf('color_number_id')]]) ;
				$lab_dip_color_id=return_field_value("contrast_color_id","wo_pre_cos_fab_co_color_dtls","job_no='".$color_wise_wo_result[csf('job_no')]."' and pre_cost_fabric_cost_dtls_id=".$color_wise_wo_result[csf('id')]." and gmts_color_id=".$color_wise_wo_result[csf('color_number_id')]."");
			}
			else
			{
				echo $color_library[$color_wise_wo_result[csf('color_number_id')]];
				$lab_dip_color_id=$color_wise_wo_result[csf('color_number_id')];
			}
			?>

            </td>
            <?
            foreach($nameArray_item_size  as $result_size)
			{
				?>
				<td align="center" style="border:1px solid black">
				<?

				$color_wise_wo_sql_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$color_wise_wo_result[csf('color_number_id')]."   and  status_active=1 and is_deleted =0 ");




				list($plan_cut_qnty)=$color_wise_wo_sql_qnty;
				$plan_cut=$plan_cut_qnty[csf('plan_cut_qnty')];
				$colar_tipping_excess_per=($plan_cut*$colar_tipping_excess_percent_arr[$color_wise_wo_result[csf('color_number_id')]][$result_size[csf('size_number_id')]])/100;
				echo number_format($plan_cut+$colar_tipping_excess_per,0);
				$collar_tiff_size_total_arr[$result_size[csf('size_number_id')]]+=number_format($plan_cut+$colar_tipping_excess_per,0,'','');
				$color_tipping_total_collar+=$plan_cut+$colar_tipping_excess_per;
				$color_tipping_total_collar_order_qnty+=$plan_cut;
				$grand_total_collar_tipping+=$plan_cut+$colar_tipping_excess_per;
				$grand_total_collar_tipping_order_qnty+=$plan_cut;



				?>
                </td>
				<?
			}
			?>

            <td align="center"><? echo number_format($color_tipping_total_collar,0); ?></td>
            <td align="center"><? echo number_format((($color_tipping_total_collar-$color_tipping_total_collar_order_qnty)/$color_tipping_total_collar_order_qnty)*100,2); ?></td>
            </tr>
            <?
		    }
			?>
            <tr>
                <td>Size Total</td>

                <?
                foreach($nameArray_item_size  as $result_size)
                {
                ?>
                <td style="border:1px solid black;  text-align:center">
				<?
				//$colar_excess_pers=($size_tatal[$result_size[csf('size_number_id')]]*$colar_excess_percent)/100; echo number_format($size_tatal[$result_size[csf('size_number_id')]]+$colar_excess_pers,0);
				echo number_format($collar_tiff_size_total_arr[$result_size[csf('size_number_id')]],0);

				?></td>
                <?
                }
                ?>
                <td  style="border:1px solid black;  text-align:center"><?  echo number_format($grand_total_collar_tipping,0); ?></td>
                <td align="center" style="border:1px solid black"> <? echo number_format((($grand_total_collar_tipping-$grand_total_collar_tipping_order_qnty)/$grand_total_collar_tipping_order_qnty)*100,2); ?></td>
            </tr>
        </table>
        </td>
        <td width="2%">
        </td>
        <?
        }

		$cuff_tipping_percent_size_wise_array=$cuff_size_total=array();
		$cuff_tipping_percent_size_wise_sql=sql_select( "select a.colar_cuff_per,b.color_number_id,b.gmts_sizes from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b,wo_pre_cost_fabric_cost_dtls c  where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.pre_cost_fabric_cost_dtls_id=c.id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.color_size_table_id  and a.booking_no=$txt_reqsn_no and a.booking_type=1 and c.body_part_id in(214) and a.status_active=1 and a.is_deleted=0");
		foreach($cuff_tipping_percent_size_wise_sql as $cuff_percent_size_wise_row)
		{
			$cuff_tipping_percent_size_wise_array[$cuff_percent_size_wise_row[csf('color_number_id')]][$cuff_percent_size_wise_row[csf('gmts_sizes')]]=$cuff_percent_size_wise_row[csf('colar_cuff_per')];
		}


		$nameArray_item_size=sql_select( "select min(c.id) as id, b.item_size,c.size_number_id,	min(c.size_order) as size_order FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_reqsn_no  and a.body_part_id=214  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 group by b.item_size,c.size_number_id order by size_order");


		if(count($nameArray_item_size)>0)
		{
        ?>
        <td width="49%">
        <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
        <tr>
        <td colspan="<? echo count($nameArray_size)+4;?>" align="center"><b>Cuff Tipping-  Colour Size Brakedown in Pcs</b></td>
        </tr>
        <tr>
        <td width="70">Size</td>

        <?
		foreach($nameArray_item_size  as $result_size)
		{
		?>
		<td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
		<?
        }
        ?>
        <td rowspan="2" align="center"><strong>Total</strong></td>
        <td width="60" rowspan="2" align="center"><strong>Extra %</strong></td>
        </tr>
        <tr>
        <td>Cuff Size</td>

        <?
        foreach($nameArray_item_size  as $result_item_size)
		{
		?>
		<td align="center" style="border:1px solid black"><strong><? echo $result_item_size[csf('item_size')];?></strong></td>
		<?
        }
        ?>
         <?

			$color_wise_wo_sql=sql_select("select a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method,c.color_number_id, c.color_order, sum(c.plan_cut_qnty) as plan_cut_qnty
			FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d
			WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_reqsn_no and a.body_part_id=214  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id and  d.status_active=1 and d.is_deleted=0
			group by c.color_number_id, c.color_order ,a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method
			order by c.color_order");
		foreach($color_wise_wo_sql as $color_wise_wo_result)
	    {
			$color_total_cuff=0;
			$color_total_cuff_order_qnty=0;
			$process_loss_method=$color_wise_wo_result[csf("process_loss_method")];
			$constrast_color_arr=array();
			if($color_wise_wo_result[csf("color_size_sensitive")]==3)
			{
				$constrast_color=explode('__',$color_wise_wo_result[csf("color_break_down")]);
				for($i=0;$i<count($constrast_color);$i++)
				{
					$constrast_color2=explode('_',$constrast_color[$i]);
					$constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
				}
			}
		?>
			<tr>
            <td>
            <?
			if($color_wise_wo_result[csf("color_size_sensitive")]==3)
			{
				echo strtoupper ($constrast_color_arr[$color_wise_wo_result[csf('color_number_id')]]);
				$lab_dip_color_id=return_field_value("contrast_color_id","wo_pre_cos_fab_co_color_dtls","job_no='".$color_wise_wo_result[csf('job_no')]."' and pre_cost_fabric_cost_dtls_id=".$color_wise_wo_result[csf('id')]." and gmts_color_id=".$color_wise_wo_result[csf('color_number_id')]."");
			}
			else
			{
				echo $color_library[$color_wise_wo_result[csf('color_number_id')]];
				$lab_dip_color_id=$color_wise_wo_result[csf('color_number_id')];
			}
			?>

            </td>
            <?
            foreach($nameArray_item_size  as $result_size)
			{
				?>
				<td align="center" style="border:1px solid black">

				<?


				$color_wise_wo_sql_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$color_wise_wo_result[csf('color_number_id')]."   and  status_active=1 and is_deleted =0");



				list($plan_cut_qnty)=$color_wise_wo_sql_qnty;
				$plan_cut=$plan_cut_qnty[csf('plan_cut_qnty')];
				$cuff_tipping_excess_per=(($plan_cut*2)*$cuff_tipping_percent_size_wise_array[$color_wise_wo_result[csf('color_number_id')]][$result_size[csf('size_number_id')]])/100;
				echo number_format($plan_cut*2+$cuff_tipping_excess_per,0);
				$cuff_tiffing_size_total[$result_size[csf('size_number_id')]]+=number_format($plan_cut*2+$cuff_tipping_excess_per,0,'','');
				//echo $cuff_tipping_percent_size_wise_array[$color_wise_wo_result[csf('color_number_id')]][$result_size[csf('size_number_id')]];
				$color_total_cuff_tiffing+=$plan_cut*2+$cuff_tipping_excess_per;
				$color_total_cuff_tiffing_order_qnty+=$plan_cut*2;
				$grand_total_cuff_tiffing+=$plan_cut*2+$cuff_tipping_excess_per;
				$grand_total_cuff_tiffing_order_qnty+=$plan_cut*2;
				?>

                </td>
				<?
			}
			?>

            <td align="center"><? echo number_format($color_total_cuff_tiffing,0); ?></td>
            <td align="center"><? echo number_format((($color_total_cuff_tiffing-$color_total_cuff_tiffing_order_qnty)/$color_total_cuff_tiffing_order_qnty)*100,2);  ?></td>
            </tr>
            <?
		    }
			?>
            <tr>
                <td>Size Total</td>

                <?
                foreach($nameArray_item_size  as $result_size)
                {
                   /* $nameArray_size_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =$result_size[size_number_id]  and status_active=1 and is_deleted =0 order by id");
                foreach($nameArray_size_qnty as $result_size_qnty)
                {*/
                ?>
                <td style="border:1px solid black;  text-align:center">
				<?
				//$cuff_excess_pers=(($size_tatal[$result_size[csf('size_number_id')]]*2)*$cuff_excess_percent)/100; echo number_format($size_tatal[$result_size[csf('size_number_id')]]*2+$cuff_excess_pers,0);
				echo number_format($cuff_tiffing_size_total[$result_size[csf('size_number_id')]],0);
				?></td>
                <?
                //}
                }
                ?>
                <td  style="border:1px solid black;  text-align:center"><?  echo number_format($grand_total_cuff_tiffing,0); ?></td>
                <td align="center" style="border:1px solid black"> <? echo number_format((($grand_total_cuff_tiffing-$grand_total_cuff_tiffing_order_qnty)/$grand_total_cuff_tiffing_order_qnty)*100,2); ?></td>
            </tr>
        </table>
        </td>
        <?
				}
		?>
        </tr>
        </table>
 <br> <br>
        <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">

        <tr>
        <td colspan="10" align="center">
        <strong>Comments</strong>
        </td>

        </tr>

        <tr>
	        <td align="center"> SL </td>
	        <td align="center"> PO NO </td>
	        <td align="center"> Ship Date </td>
	        <td align="center"> BOM Qty</td>
	        <td align="center"> Booking Qty </td>
	        <td align="center"> Short Booking Qty </td>
	        <td align="center"> Total Booking Qty </td>
	        <td align="center"> Balance </td>
	        <td align="center"> Comments </td>
         </tr>
        <?
        $is_short_data=sql_select("select a.id, sum(b.grey_fab_qnty) as booking_qty from wo_po_break_down a,wo_booking_dtls b  where a.job_no_mst =b.job_no and  a.id=b.po_break_down_id   and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   and b.is_short =1 group by  a.id ");
        foreach($is_short_data as $vals)
        {
        	$short_qty_arr[$vals[csf("id")]]=$vals[csf("booking_qty")];
        }

        $booking_data=sql_select("select a.id, sum(b.grey_fab_qnty) as booking_qty from wo_po_break_down a,wo_booking_dtls b  where a.job_no_mst =b.job_no and  a.id=b.po_break_down_id   and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and b.is_short !=1 group by  a.id ");
        foreach($booking_data as $vals)
        {
        	$booking_arr[$vals[csf("id")]]=$vals[csf("booking_qty")];
        }
        $po_num_arr=return_library_array("select id,po_number from wo_po_break_down where status_active=1 and is_deleted=0",'id','po_number');

        $po_date=return_library_array("select id,shipment_date from wo_po_break_down where status_active=1 and is_deleted=0",'id','shipment_date');

    $comments_data=sql_select("SELECT min(a.id) as ids,b.po_break_down_id as po_number,sum(d.fin_fab_qnty) as fin_fab_qntys,sum(d.grey_fab_qnty) as grey_fab_qntys,SUM(b.requirment) as precost_grey_qty FROM wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d
	WHERE a.job_no=b.job_no and
	a.id=b.pre_cost_fabric_cost_dtls_id
	and
	b.po_break_down_id=d.po_break_down_id and
	b.color_number_id=d.gmts_color_id and
	b.dia_width=d.dia_width and
	b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
	a.job_no=d.job_no and
	a.id=d.pre_cost_fabric_cost_dtls_id
	and
	d.booking_no =$txt_reqsn_no and
	d.job_no='".$row_per_job[csf('job_no')]."' and

	d.status_active=1 and
	d.is_deleted=0 and
	b.cons>0
	group by b.po_break_down_id");


	$job_no=$row_per_job[csf('job_no')];
 	$condition= new condition();
	if(str_replace("'","",$job_no) !='')
	{
	  $condition->job_no("='$job_no'");
 	}
	 $condition->init();
	$fabric= new fabric($condition);
	$fabric_costing_qty_arr=$fabric->getQtyArray_by_order_knitAndwoven_greyAndfinish();

        $j=1;
        $total_bom=0;
        $total_book=0;
        $total_short=0;
        $total_short_full=0;
        $total_balance=0;

		foreach($comments_data as $val)
		{
				$po_id=$val[csf('po_number')];
			 	$woven_qty=array_sum($fabric_costing_qty_arr['woven']['grey'][$po_id]);
			 	$knit_qty=array_sum($fabric_costing_qty_arr['knit']['grey'][$po_id]);
			 	$sum_woven_knit=$woven_qty + $knit_qty;


			?>
          <tr>
        <td align="center"><? echo $j;?></td>

        <td align="center"> <? echo $po_num_arr[$val[csf("po_number")]] ;?> </td>
        <td align="center"> <? echo change_date_format($po_date[$val[csf("po_number")]], "yyyy-mm-dd", "-");?> </td>
        <td align="center"><?  echo $pre= def_number_format($sum_woven_knit,2);  ?> </td>
         <td align="center"><?  echo $bookings= def_number_format($booking_arr[$val[csf("po_number")]],2);  ?> </td>
 		<td align="center"> <? echo $short=def_number_format($short_qty_arr[$val[csf("po_number")]],2); ?> </td>
		<td align="center"> <? $tot_short_book= str_replace(',','',$bookings) +  str_replace(',','',$short); echo number_format($tot_short_book,4); ?>  </td>
		<td align="center"> <? $bal =str_replace(',','',$pre)-str_replace(',','',$tot_short_book); echo number_format($bal,4); ?> </td>
		<td align="center"> <? if($bal!=0){ if(str_replace(',','',$pre)>$tot_short_book){echo "Less ";} else{ echo "Over";} }?> </td>


        </tr>
        <?
        $total_bom +=str_replace(',','',$pre);
        $total_book +=str_replace(',','',$bookings);
        $total_short +=str_replace(',','',$short);
        $total_short_full += str_replace(',','',$tot_short_book);
        $total_balance += str_replace(',','',$bal);

        $j++;
		}
		?>
		<tr>
			<td colspan="3" align="right"> <b> Total </b></td>
			<td align="center"><strong><? echo number_format($total_bom,4); ?> </strong> </td>
			<td align="center"><strong><? echo number_format($total_book,4); ?> </strong> </td>
			<td align="center"><strong><? echo number_format($total_short,4); ?> </strong> </td>
			<td align="center"><strong><? echo number_format($total_short_full,4); ?> </strong> </td>
			<td align="center"><strong><? echo number_format($total_balance,4); ?> </strong> </td>
			<td>&nbsp;</td>
		</tr>
        </table>
<br> <br>
        <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
        	<tr>
        		<th>Responsible Dept.</th>
        		<th>Responsible Person</th>
        		<th>Reason</th>
        	</tr>
        	<tr>
        		<td>
        			<?
        				$all_dept_name="";
        				$department_name_library=return_library_array( "select id,department_name from  lib_department where status_active=1 and is_deleted=0 order by  department_name", "id", "department_name"  );
        				$all_dept=array_unique(explode(",", implode(",",$job_data_arr['responsible_dept'])));
        				foreach ($all_dept as $value)
        				{
        					$all_dept_name.=$department_name_library[$value].",";
        				}
        				echo chop($all_dept_name, ",");
        			?>
        		</td>
        		<td><? echo implode(",", array_unique($job_data_arr['responsible_person'])); ?></td>
        		<td><? echo implode(",", array_unique($job_data_arr['reason'])); ?></td>
        	</tr>
        </table>

        <br>
	<table width="100%"   cellpadding="2" cellspacing="0" rules="all" style="border-left: none;border-right: none;border-top: none;border-bottom: none;">
		<tr>
		 	<td align="left" style="border-left: none;border-right: none;border-top: none;border-bottom: none;">
		 		<img  src='<? echo $path.$imge_arr[$job_no]; ?>' height='180' width='250' />
		 	</td>
		 	<td align="right" width="650" style="border-left: none;border-right: none;border-top: none;border-bottom: none;">
		 		<?php echo get_spacial_instruction($txt_reqsn_no); ?>
		 	</td>
		</tr>
	</table>
	<br>



	<?
	echo signature_table(1, $cbo_company_name, "1330px");
	?>
    <p class="
    "></p>

    <?
	}
$job_no_all= implode(",",array_unique($joball['job_no']));
	$style_sting_all=implode(",",array_unique($joball['style_ref_no']));

	echo "****".custom_file_name($txt_reqsn_no,$style_sting_all,$job_no_all);
	?>
	</div>
	<?

}

if($action == "fabric_booking_report")//unused
{
    extract($_REQUEST);
    $cbo_company_name=str_replace("'","",$cbo_company_name);
    $cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
    $cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
    $imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
    $company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
    $pro_sub_dept_array=return_library_array( "select id,sub_department_name from lib_pro_sub_deparatment",'id','sub_department_name');
    $season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");
    $marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
    $marchentr_email = return_library_array("select id,team_member_email from lib_mkt_team_member_info ","id","team_member_email");
    $buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
    $supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
    $color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");

    $company_info=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website from lib_company where id=$cbo_company_name");
    if($company_info[0][csf('plot_no')] != ''){
		$address.=$company_info[0][csf('plot_no')].',';
	}
	if($company_info[0][csf('level_no')] != ''){
		$address.=" ".$company_info[0][csf('level_no')].',';
	}
	if($company_info[0][csf('road_no')] != ''){
		$address.=" ".$company_info[0][csf('road_no')].',';
	}
	if($company_info[0][csf('block_no')] != ''){
		$address.=" ".$company_info[0][csf('block_no')].'<br>';
	}
	if($company_info[0][csf('city')] != ''){
		$address.=$company_info[0][csf('city')].',';
	}
	if($company_info[0][csf('zip_code')] != 0 && $company_info[0][csf('zip_code')] != ''){
		$address.='-'.$company_info[0][csf('zip_code')].',';
	}
	if($company_info[0][csf('province')] != ''){
		$address.=$company_info[0][csf('province')].','.$country_arr[$company_info[0][csf('country_id')]];
	}
	if($company_info[0][csf('email')] != ''){
		$add_info.='Email: '.$company_info[0][csf('email')];
	}
	if($company_info[0][csf('website')] != ''){
		$add_info.=', Website: '.$company_info[0][csf('website')];
	}

    $po_booking_info=sql_select( "select  a.style_ref_no,a.style_description, a.job_no, a.style_owner, a.buyer_name, a.client_id, a.dealing_marchant, a.season, a.season_matrix, a.total_set_qnty, a.product_dept, a.product_code, a.pro_sub_dep, a.gmts_item_id, a.order_repeat_no, a.qlty_label from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no=$txt_reqsn_no and b.status_active =1 and b.is_deleted=0");
    $path=str_replace("'","",$path);
    if($path!="") $path=$path; else $path="../../";
    $nameArray_approved=sql_select( "select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_reqsn_no and b.entry_form=7 and a.status_active =1 and a.is_deleted=0 ");
    list($nameArray_approved_row)=$nameArray_approved;
    $nameArray_approved_date=sql_select( "select b.approved_date as approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_reqsn_no and b.entry_form=7 and b.approved_no='".$nameArray_approved_row[csf('approved_no')]."' and a.status_active =1 and a.is_deleted=0 ");
    list($nameArray_approved_date_row)=$nameArray_approved_date;
    $nameArray_approved_comments=sql_select( "select b.comments as comments from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_reqsn_no and b.entry_form=7 and b.approved_no='".$nameArray_approved_row[csf('approved_no')]."' and a.status_active =1 and a.is_deleted=0 ");
    list($nameArray_approved_comments_row)=$nameArray_approved_comments;
    $uom=0;
    $job_data_arr=array();
    foreach ($po_booking_info as $result_buy){
    $job_data_arr['job_no'][$result_buy[csf('job_no')]]=$result_buy[csf('job_no')];
    $job_data_arr['job_no_in'][$result_buy[csf('job_no')]]="'".$result_buy[csf('job_no')]."'";
    $job_data_arr['total_set_qnty'][$result_buy[csf('job_no')]]=$result_buy[csf('total_set_qnty')];
    $job_data_arr['product_dept'][$result_buy[csf('job_no')]]=$product_dept[$result_buy[csf('product_dept')]];
    $job_data_arr['product_code'][$result_buy[csf('job_no')]]=$result_buy[csf('product_code')];
    $job_data_arr['pro_sub_dep'][$result_buy[csf('job_no')]]=$pro_sub_dept_array[$result_buy[csf('pro_sub_dep')]];
    $job_data_arr['gmts_item_id'][$result_buy[csf('job_no')]]=$result_buy[csf('gmts_item_id')];
    $job_data_arr['style_ref_no'][$result_buy[csf('job_no')]]=$result_buy[csf('style_ref_no')];
    $job_data_arr['style_description'][$result_buy[csf('job_no')]]=$result_buy[csf('style_description')];
    $job_data_arr['dealing_marchant'][$result_buy[csf('job_no')]]=$marchentrArr[$result_buy[csf('dealing_marchant')]];
    $job_data_arr['dealing_marchant_email'][$result_buy[csf('job_no')]]=$marchentr_email[$result_buy[csf('dealing_marchant')]];
    $job_data_arr['season_matrix'][$result_buy[csf('job_no')]]=$season_arr[$result_buy[csf('season_matrix')]];
    $job_data_arr['order_repeat_no'][$result_buy[csf('job_no')]]=$result_buy[csf('order_repeat_no')];
    $job_data_arr['qlty_label'][$result_buy[csf('job_no')]]=$quality_label[$result_buy[csf('qlty_label')]];
    $job_data_arr['client'][$result_buy[csf('job_no')]]=$result_buy[csf('client_id')];
    }
    $job_no= implode(",",array_unique($job_data_arr['job_no']));
    $job_no_in= implode(",",array_unique($job_data_arr['job_no_in']));
    $product_depertment=implode(",",array_unique($job_data_arr['product_dept']));
    $product_code=implode(",",array_unique($job_data_arr['product_code']));
    $pro_sub_dep=implode(",",array_unique($job_data_arr['pro_sub_dep']));
    $gmts_item_id=implode(",",array_unique($job_data_arr['gmts_item_id']));
    $style_sting=implode(",",array_unique($job_data_arr['style_ref_no']));
    $style_description=implode(",",array_unique($job_data_arr['style_description']));
    $dealing_marchant=implode(",",array_unique($job_data_arr['dealing_marchant']));
    $dealing_marchant_email=implode(",",array_unique($job_data_arr['dealing_marchant_email']));
    $season_matrix=implode(",",array_unique($job_data_arr['season_matrix']));
    $order_repeat_no= implode(",",array_unique($job_data_arr['order_repeat_no']));
    $qlty_label= implode(",",array_unique($job_data_arr['qlty_label']));
    $client_id= implode(",",array_unique($job_data_arr['client']));

    $po_data=array();
    if($db_type==0){
    $nameArray_job=sql_select( "select b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no, b.po_quantity,DATEDIFF(pub_shipment_date,po_received_date) date_diff,MIN(po_received_date) as po_received_date ,MIN(pub_shipment_date) as pub_shipment_date,MIN(b.insert_date) as insert_date,b.shiping_status  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_reqsn_no and a.status_active =1 and a.is_deleted=0   group by b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no, b.po_quantity,pub_shipment_date,po_received_date,b.insert_date,b.shiping_status ");
    }
    if($db_type==2){
    $nameArray_job=sql_select( "select b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no, b.po_quantity,(pub_shipment_date-po_received_date) date_diff,MIN(po_received_date) as po_received_date,MIN(pub_shipment_date) as pub_shipment_date,MIN(b.insert_date) as insert_date,b.shiping_status   from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_reqsn_no and a.status_active =1 and a.is_deleted=0  group by b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no, b.po_quantity,pub_shipment_date,po_received_date,b.insert_date,b.shiping_status ");
    }
    foreach ($nameArray_job as $result_job){
        $po_data['grouping'][$result_job[csf('id')]]=$result_job[csf('grouping')];
    }
    $grouping=implode(",",array_unique(array_filter($po_data['grouping'])));

    $nameArray=sql_select( "select a.buyer_id,a.booking_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.po_break_down_id,a.colar_excess_percent,a.cuff_excess_percent,a.delivery_date,a.is_apply_last_update,a.fabric_source,a.rmg_process_breakdown,a.insert_date,a.update_date,a.uom,a.remarks,a.pay_mode,a.fabric_composition,a.delivery_address, a.currency_id from wo_booking_mst a  where   a.booking_no=$txt_reqsn_no and a.status_active =1 and a.is_deleted=0 ");

    ?>
    <table style="border:1px solid black;table-layout: fixed; " width="100%">
        <tr>
            <td><img  src='<? echo $path.$imge_arr[$cbo_company_name]; ?>' height='100' width='100' /></td>
            <td style="text-align: center;">
                <span style=" font-size:20px; font-weight:bold"><? echo $company_library[$cbo_company_name]; ?></span><br>
                <span><? echo $address; ?></span><br>
                <span><? echo $add_info ?></span><br>
                <span style="font-size:16px; font-weight:bold">Short Fabric Purchase Order</span>
            </td>
            <td style="text-align: right; padding-right: 30px"><span style="font-size:16px; font-weight:bold;">Booking No: <? echo $nameArray[0][csf('booking_no')];?><? echo "(".$fabric_source[$nameArray[0][csf('fabric_source')]].")"?></span></td>
        </tr>
    </table>
    <? foreach ($nameArray as $result) {
        $currency_id=$result[csf('currency_id')];
        $booking_date=$result[csf('update_date')];
            if($booking_date=="" || $booking_date=="0000-00-00 00:00:00"){
            $booking_date=$result[csf('insert_date')];
            }
     ?>
    <table style="margin-top: 5px" class="rpt_table" border="1" cellpadding="1" cellspacing="1" rules="all" width="100%">
        <tr>
            <th width="175" style="text-align: left">Supplier Name </th>
            <td width="175"><?
                if($result[csf('pay_mode')]==5 || $result[csf('pay_mode')]==3){
                echo $company_library[$result[csf('supplier_id')]];
                }
                else{
                echo $supplier_name_arr[$result[csf('supplier_id')]];
                }
            ?>
            </td>
            <th width="175" style="text-align: left">Dealing Merchant </th>
            <td width="175" ><? echo $dealing_marchant; ?></td>
            <th width="175" style="text-align: left">Buyer/Agent Name</th>
            <td width="175"><? $buyer_name_str=""; if($client_id!=0) $buyer_name_str=$buyer_name_arr[$result[csf('buyer_id')]]."-".$buyer_name_arr[$client_id]; else $buyer_name_str=$buyer_name_arr[$result[csf('buyer_id')]]; echo $buyer_name_str; ?></td>

        </tr>
        <tr>
            <th style="text-align: left">Attention </th>
            <td><? echo $result[csf('attention')]; ?></td>
            <th style="text-align: left">Merchant E-Mail id </th>
            <td><? echo $dealing_marchant_email ?></td>
            <th style="text-align: left">Garments Item </th>
            <td><?
                $gmts_item_name="";
                $gmts_item=explode(',',$gmts_item_id);
                for($g=0;$g<=count($gmts_item); $g++)
                {
                $gmts_item_name.= $garments_item[$gmts_item[$g]].",";
                }
                echo rtrim($gmts_item_name,',');
            ?>
            </td>
        </tr>
        <tr>
            <th width="175" style="text-align: left">Requisition Date </th>
            <td width="175"><? echo change_date_format($booking_date,'dd-mm-yyyy','-','');?></td>
            <th style="text-align: left">Fabric ETD </th>
            <td><? echo change_date_format( $result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>
            <th style="text-align: left">Internal Ref No. </th>
            <td><?echo $grouping ?></td>
        </tr>
        <tr>
        	<?
        	$delivery_address=explode("\n",$result[csf('delivery_address')]);
        	?>
        	<th style="text-align: left">Delivery Address </th>
            <td><? if(count($delivery_address)>0){
            	foreach ($delivery_address as $key => $value) { ?>
            	<? echo $value ?><br>
            	<? }
            } ?></td>
            <th style="text-align: left">Pay Mode</th>
            <td><? echo $pay_mode[$result[csf('pay_mode')]]?></td>
            <th style="text-align: left">Currency </th>
            <td><? echo $currency[$result[csf('currency_id')]]?></td>
        </tr>
        <tr>
            <th style="text-align: left">Remarks </th>
            <td colspan="5"><? echo $result[csf('remarks')]?></td>
        </tr>
    </table>
    <? } ?>
    <?
    //$nameArray_fabric_description= "select  a.job_no,a.id as fabric_cost_dtls_id, a.body_part_id,a.color_type_id as c_type, a.construction, a.composition, a.gsm_weight as gsm, d.dia_width as dia,a.width_dia_type as dia_type,a.uom,sum(d.fin_fab_qnty) as fin_fab_qntys,sum(d.grey_fab_qnty) as grey_fab_qntys,avg(d.rate) as rates,sum(d.amount) as amounts ,c.style_ref_no,c.job_no_prefix_num,d.fabric_color_id as fab_color,d.gmts_color_id as gmt_color FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls d,wo_po_details_master c WHERE a.job_no=d.job_no and a.id = d.pre_cost_fabric_cost_dtls_id and a.job_no = c.job_no  and d.job_no=c.job_no and d.booking_no = $txt_reqsn_no and d.job_no in(".$job_no_in.") and d.status_active = 1 and d.is_deleted=0 group by a.job_no,a.id, a.body_part_id, a.color_type_id, a.construction, a.composition,d.fabric_color_id,d.gmts_color_id, a.gsm_weight, d.dia_width,a.uom,c.style_ref_no,c.job_no_prefix_num,a.width_dia_type order by a.job_no,d.fabric_color_id ";
    $nameArray_fabric_description = "select a.job_no,a.id as fabric_cost_dtls_id, a.body_part_id, a.color_type_id as c_type, a.construction, a.composition, a.gsm_weight as gsm, d.dia_width as dia, a.width_dia_type as dia_type,a.uom,sum(d.fin_fab_qnty) as fin_fab_qntys, sum(grey_fab_qnty) as grey_fab_qntys,sum(d.grey_fab_qnty) as grey_fab_qntys,avg(d.rate) as rates,sum(d.amount) as amounts ,c.style_ref_no, c.style_description,  c.job_no_prefix_num, d.fabric_color_id as fab_color,d.gmts_color_id as gmt_color, b.po_number FROM wo_pre_cost_fabric_cost_dtls a, wo_po_break_down b, wo_po_details_master c,  wo_booking_dtls d WHERE a.job_no=d.job_no and a.id = d.pre_cost_fabric_cost_dtls_id and a.job_no = c.job_no and d.job_no=c.job_no and d.booking_no = $txt_reqsn_no and d.job_no in(".$job_no_in.") and d.status_active = 1 and d.is_deleted=0 and b.job_no_mst=d.job_no and b.id=d.po_break_down_id and b.is_deleted=0 and b.status_active=1 group by a.job_no,a.id, a.body_part_id, a.color_type_id, a.construction, a.composition,d.fabric_color_id,d.gmts_color_id, a.gsm_weight, d.dia_width,a.uom,c.style_ref_no,c.job_no_prefix_num,a.width_dia_type , b.po_number, c.style_description order by a.job_no,d.fabric_color_id";
        //echo $nameArray_fabric_description; die;
        $result_set=sql_select($nameArray_fabric_description);
         foreach( $result_set as $row)
         {
            $uom_data_arr[$row[csf("uom")]]=$unit_of_measurement[$row[csf("uom")]];
            $main_data=array('style_ref_no','style_description');
            foreach ($main_data as $mainAttr) {
                $fabric_detail_arr[$row[csf("job_no")]][$mainAttr] = $row[csf($mainAttr)];
            }
            $fabric_attr = array('uom','construction','composition','c_type','gsm','dia','dia_type','gmt_color','style_ref_no','po_number','style_description');
            foreach ($fabric_attr as $attr) {
                $fabric_detail_arr[$row[csf("job_no")]][$row[csf('uom')]][$row[csf("construction")]][$row[csf("body_part_id")]][$row[csf("gmt_color")]][$attr][] = $row[csf($attr)];
            }

            $color_attr = array('rates');
            foreach ($color_attr as $attr) {
                $fabric_detail_arr[$row[csf("job_no")]][$row[csf('uom')]][$row[csf("construction")]][$row[csf("body_part_id")]][$row[csf("gmt_color")]]['fab_color'][$row[csf("fab_color")]][$attr] = $row[csf($attr)];
            }
            $fabric_detail_arr[$row[csf("job_no")]][$row[csf('uom')]][$row[csf("construction")]][$row[csf("body_part_id")]][$row[csf("gmt_color")]]['fab_color'][$row[csf("fab_color")]]['amounts'] += $row[csf('amounts')];
            $fabric_detail_arr[$row[csf("job_no")]][$row[csf('uom')]][$row[csf("construction")]][$row[csf("body_part_id")]][$row[csf("gmt_color")]]['fab_color'][$row[csf("fab_color")]]['grey_fab_qntys'] += $row[csf('grey_fab_qntys')];

            $summery_attr = array('body_part_id','construction','composition','po_number','rates','amounts','grey_fab_qntys','c_type','gsm','dia','dia_type','fab_color');
            foreach ($summery_attr as $attr) {
                $string = $row[csf("body_part_id")].'**'.$row[csf("construction")].'**'.$row[csf("composition")];
                if($attr == 'fab_color'){
                    $fabric_detail_summery[$row[csf("uom")]][$string][$attr][] = $color_library[$row[csf($attr)]];
                }
                else{
                    $fabric_detail_summery[$row[csf("uom")]][$string][$attr][] = $row[csf($attr)];
                }
            }


         }

            /*echo '<pre>';
            print_r($fabric_detail_arr); die;*/
            //$uom_val='';
            $grand_fin_fab_qty_sum =0;
            $grand_amount_sum =0;
            foreach($uom_data_arr as $uom_id=>$uom_val){?>  
                <div style="margin-top:15px">
                    <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" width="100%" style="text-align:center;" rules="all">
                    <caption><b style="float:left"> <? echo 'Job No. : '.$job_no.', Style Ref. : '. $style_sting.', Style Des. : '.$style_description; ?></b> </caption>
                        <tr style="font-weight:bold">
                            <td width="150">Po. No.</td>
                            <td width="150">Body Part</td>
                            <td width="200">Fabric Construction</td>
                            <td width="200">Fabric Composition</td>
                            <td width="100">Color Type</td>
                            <td width="50">GSM</td>
                            <td width="100">Dia/C-Width</td>
                            <td width="100">Gmts. Color</td>
                            <td width="100">Fabric Color</td>
                            <td width="150">Lab Dip No/Mill Ref. No</td>
                            <td width="100">Fin Fab Qty(<? echo $uom_val ?>)</td>
                            <td width="50">Rate(<? echo $currency[$currency_id] ?>)</td>
                            <td width="50">Amount(<? echo $currency[$currency_id] ?>)</td>
                        </tr>
    <?
        $fab_color_row = '';
        foreach ($fabric_detail_arr as $job_no => $uom_data_arr) {

            $job_fin_fab_qty_sum =0;
            $job_amount_sum =0;
            foreach ($uom_data_arr as $uom_key => $construction_arr) {
                if($uom_id == $uom_key){
                   
                    foreach ($construction_arr as $cons_key => $body_part_arr) {
                        foreach ($body_part_arr as $body_part_key=>$gmt_color_data) {
                            foreach ($gmt_color_data as $gmt_color_key => $body_part_dtls){
                        $color = 1;
                        $fin_fab_qty_sum = 0;
                        $amount_sum = 0;
                        $fab_color_row = count($body_part_dtls['fab_color']);
                        foreach ($body_part_dtls['fab_color'] as $fab_color_key => $fab_color_dtls) {
                            if($color == 1){
                                $fin_fab_qty_sum += $fab_color_dtls['grey_fab_qntys'];
                                $amount_sum += $fab_color_dtls['amounts'];
                                ?>
                                <tr>
                                    <td rowspan="<? echo $fab_color_row ?>"><span style="overflow-wrap: break-word "><? echo implode(", ",array_unique($body_part_dtls['po_number'])) ?></span></td>
                                    <td rowspan="<? echo $fab_color_row ?>"><? echo $body_part[$body_part_key] ?></td>
                                    <td rowspan="<? echo $fab_color_row ?>"><? echo implode(",",array_unique($body_part_dtls['construction'])) ?></td>
                                    <td rowspan="<? echo $fab_color_row ?>"><? echo implode(",",array_unique($body_part_dtls['composition'])) ?></td>
                                    <td rowspan="<? echo $fab_color_row ?>"><? echo $color_type[implode(",",array_unique($body_part_dtls['c_type']))] ?></td>
                                    <td rowspan="<? echo $fab_color_row ?>"><? echo implode(",",array_unique($body_part_dtls['gsm'])) ?></td>
                                    <td rowspan="<? echo $fab_color_row ?>"><? echo implode(",",array_unique($body_part_dtls['dia'])).','.$fabric_typee[implode(",",array_unique($body_part_dtls['dia_type']))] ?></td>
                                    <td><? echo $color_library[$gmt_color_key] ?></td>
                                    <td><? echo $color_library[$fab_color_key] ?></td>
                                    <td><? $lapdip_no="";
                                    $lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='".$job_no."' and approval_status=3 and color_name_id=".$fab_color_key."");
                                    if($lapdip_no=="") echo "&nbsp;"; echo $lapdip_no; ?></td>
                                    <td><? echo number_format($fab_color_dtls['grey_fab_qntys'],3) ?></td>
                                    <td><? echo number_format($fab_color_dtls['rates'],2)?></td>
                                    <td><? echo number_format($fab_color_dtls['amounts'],3) ?></td>
                                </tr>
                            <? } else{
                            $fin_fab_qty_sum += $fab_color_dtls['grey_fab_qntys'];
                            $amount_sum += $fab_color_dtls['amounts'];
                            ?>
                                <tr>
                                    <td><? echo $color_library[$fab_color_key] ?></td>
                                    <td><? $lapdip_no="";
                                    $lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='".$job_no."' and approval_status=3 and color_name_id=".$fab_color_key."");
                                    if($lapdip_no=="") echo "&nbsp;"; echo $lapdip_no; ?></td>
                                    <td><? echo number_format($fab_color_dtls['grey_fab_qntys'],3) ?></td>
                                    <td><? echo number_format($fab_color_dtls['rates'],2)?></td>
                                    <td><? echo number_format($fab_color_dtls['amounts'],3) ?></td>
                                </tr>
                        <? }
                            $color++;
                        }?>
                                <!-- <tr>
                                    <th colspan="9">&nbsp</th>
                                    <th>Sub Total</th>
                                    <th><? echo def_number_format($fin_fab_qty_sum,2); ?></th>
                                    <th>&nbsp</th>
                                    <th><? echo def_number_format($amount_sum,2); ?></th>
                                </tr> -->
                    <?
                        $job_fin_fab_qty_sum += $fin_fab_qty_sum;
                        $job_amount_sum += $amount_sum;

                     }
                        }


                    }
                    $grand_fin_fab_qty_sum +=$job_fin_fab_qty_sum;
                    $grand_amount_sum += $job_amount_sum;
                    ?>
                            <tr>
                                <th colspan="9">&nbsp</th>
                                <th>Job Total</th>
                                <th><? echo def_number_format($job_fin_fab_qty_sum,2); ?></th>
                                <th>&nbsp</th>
                                <th><? echo def_number_format($job_amount_sum,2); ?></th>
                            </tr>

                    <?


                 }
            }
        }

    ?>

        </table>
    </div>
    <?
    $mcurrency="";
   $dcurrency="";
   if($currency_id==1)
   {
	$mcurrency='Taka';
	$dcurrency='Paisa';
   }
   if($currency_id==2)
   {
	$mcurrency='USD';
	$dcurrency='CENTS';
   }
   if($currency_id==3)
   {
	$mcurrency='EURO';
	$dcurrency='CENTS';
   }
       } ?>
    <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" width="100%" style="text-align:center;" rules="all">
        <tr>
            <th colspan="9" width="350">&nbsp</th>
            <th width="106">Grand Total</th>
            <th width="86"><? echo def_number_format($grand_fin_fab_qty_sum,2); ?></th>
            <th width="74">&nbsp</th>
            <th width="99"><? echo def_number_format($grand_amount_sum,2); ?></th>
        </tr>
    </table>
    <div style="margin-top:15px">
        <? foreach ($fabric_detail_summery as $uom_id => $summery_data_arr) {
        ?>
        <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" width="100%" style="text-align:center; margin-top: 15px" rules="all">
        	<tr><td colspan="11" style="font-weight:bold; font-size: 16px">Report Summery</td></tr>
            <tr style="font-weight:bold">
                <td>Fabric Type</td>
                <td>Construction</td>
                <td>Composition</td>
                <td>Color Type</td>
                <td>GSM</td>
                <td>Dia/C-Width</td>
                <td>Fabric Color</td>
                <td>No. of PO</td>
                <td>Consumption(<? echo $unit_of_measurement[$uom_id] ?>)</td>
                <td>Rate(<? echo $currency[$currency_id] ?>)</td>
                <td>Amount(<? echo $currency[$currency_id] ?>)</td>
            </tr>
        <?
        foreach ($summery_data_arr as $summery_data) { ?>
            <tr>
                <td><? echo $body_part[implode(",",array_unique($summery_data['body_part_id']))] ?></td>
                <td><? echo implode(",",array_unique($summery_data['construction']))?></td>
                <td><? echo implode(",",array_unique($summery_data['composition']))?></td>
                <td><? echo $color_type[implode(",",array_unique($summery_data['c_type']))] ?></td>
                <td><? echo implode(",",array_unique($summery_data['gsm']))?></td>
                <td><? echo implode(",",array_unique($summery_data['dia'])).','.$fabric_typee[implode(",",array_unique($summery_data['dia_type']))] ?></td>
                <td><? echo implode(", ",array_unique($summery_data['fab_color'])) ?></td>
                <td><? echo count($summery_data['po_number']) ?></td>
                <td><? echo array_sum($summery_data['grey_fab_qntys']) ?></td>
                <td><? echo number_format(array_sum($summery_data['rates']),2)?></td>
                <td><? echo array_sum($summery_data['amounts']) ?></td>
            </tr>

        <? }
        }
        ?>
        </table>
    </div>

    <div style="margin-top: 10px;">
		<table width="100%" class="rpt_table"  border="1" cellpadding="0" cellspacing="0" rules="all">
            <tr style="border:1px solid black;">
                <td width="30%" style="border:1px solid black; text-align:left">Total Booking Amount</td>
                <td width="70%" style="border:1px solid black; text-align:left"><? echo number_format($grand_amount_sum,2);?></td>
            </tr>
            <tr style="border:1px solid black;">
                <td width="30%" style="border:1px solid black; text-align:left">Total Booking Amount (in word)</td>
                <td width="70%" style="border:1px solid black;"><? echo number_to_words(def_number_format($grand_amount_sum,2,""),$mcurrency, $dcurrency);?></td>
            </tr>
       </table>
	</div>


    <table  width="100%"  border="0" cellpadding="0" cellspacing="0" style="margin-top: 10px; ">
        <tr>
            <td width="50%" style="border:1px solid; border-color:#000;" valign="top">
                <table  width="100%"  border="0" cellpadding="0" cellspacing="0">
                    <thead>
                        <tr>
                        <th width="97%" align="left" height="30" colspan="2">Terms &amp; Condition</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
                    $data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no=$txt_reqsn_no");
                    if ( count($data_array)>0)
                        {
                        $i=0;
                        foreach( $data_array as $row )
                        {
                        $i++;
                        ?>
                        <tr id="settr_1" valign="top">
                            <td><? echo $i;?>)&nbsp</td>
                            <td><? echo $row[csf('terms')]; ?></td>
                        </tr>
                        <?
                        }
                        }
                    ?>
                    </tbody>
                </table>
            </td>
            <td width="50%" valign="top" style="border:1px solid; border-color:#000;">
			    <?
			     $desg_name=return_library_array( "select id, custom_designation from lib_designation", "id", "custom_designation"  );
				 $data_array=sql_select("select b.approved_by,b.approved_no, b.approved_date, c.user_full_name,c.designation from  wo_booking_mst a , approval_history b, user_passwd c where a.id=b.mst_id and b.approved_by=c.id and a.booking_no=$txt_reqsn_no and b.entry_form=12 and is_approved=1 order by b.id asc");
				?>
				<table  width="100%" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
				    <thead>
				    <tr style="border:1px solid black;">
				        <th colspan="3" style="border:1px solid black;">Approval Status</th>
				        </tr>

				    </thead>
				    <tbody>
				    <?
					$s=1;
					if(count($data_array) > 0){ ?>
						<tr style="border:1px solid black;">
				        <th width="3%" style="border:1px solid black;">Sl</th><th width="25%" style="border:1px solid black;">Name</th><th width="25%" style="border:1px solid black;">Designation</th><th width="27%" style="border:1px solid black;">Approval Date</th>
				        </tr>
					<?
						foreach($data_array as $row){
						?>
				        <tr style="border:1px solid black;">
				            <td width="3%" style="border:1px solid black;"><? echo $s;?></td><td width="25%" style="border:1px solid black;"><? echo $row[csf('user_full_name')];?></td><td width="25%" style="border:1px solid black;"><? echo $desg_name[$row[csf('designation')]]; ?></td><td width="27%" style="border:1px solid black;"><? echo date ("d-m-Y h:i:s",strtotime($row[csf('approved_date')]));?></td>
				            </tr>
				            <?
							$s++;
						}
					}
					else{?>
				       <tr style="border:1px solid black;">
				       	<td colspan="3" Style="font-weight:bold; text-align:center; font-size:24px;">Draft</td>
				       </tr>
				    <? }
				    ?>
				    </tbody>
				</table>
            </td>
        </tr>
    </table>
    <? echo signature_table(121, $cbo_company_name, "1330px", 1); ?>


<? 
}

if($action == "fabric_booking_report_2")//unused
{
    extract($_REQUEST);
    $cbo_company_name=str_replace("'","",$cbo_company_name);
    $cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
    $cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
    $imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
    $company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
    $pro_sub_dept_array=return_library_array( "select id,sub_department_name from lib_pro_sub_deparatment",'id','sub_department_name');
    $season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");
    $marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
    $marchentr_email = return_library_array("select id,team_member_email from lib_mkt_team_member_info ","id","team_member_email");
    $buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
    $supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
    $color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");
	$brand_arr=return_library_array("select id,brand_name from lib_buyer_brand","id","brand_name");
    $company_info=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website from lib_company where id=$cbo_company_name");
    if($company_info[0][csf('plot_no')] != ''){
		$address.=$company_info[0][csf('plot_no')].',';
	}
	if($company_info[0][csf('level_no')] != ''){
		$address.=" ".$company_info[0][csf('level_no')].',';
	}
	if($company_info[0][csf('road_no')] != ''){
		$address.=" ".$company_info[0][csf('road_no')].',';
	}
	if($company_info[0][csf('block_no')] != ''){
		$address.=" ".$company_info[0][csf('block_no')].'<br>';
	}
	if($company_info[0][csf('city')] != ''){
		$address.=$company_info[0][csf('city')].',';
	}
	if($company_info[0][csf('zip_code')] != 0 && $company_info[0][csf('zip_code')] != ''){
		$address.='-'.$company_info[0][csf('zip_code')].',';
	}
	if($company_info[0][csf('province')] != ''){
		$address.=$company_info[0][csf('province')].','.$country_arr[$company_info[0][csf('country_id')]];
	}
	if($company_info[0][csf('email')] != ''){
		$add_info.='Email: '.$company_info[0][csf('email')];
	}
	if($company_info[0][csf('website')] != ''){
		$add_info.=', Website: '.$company_info[0][csf('website')];
	}


    $po_booking_info=sql_select( "select  a.style_ref_no,a.style_description, a.job_no, a.style_owner, a.buyer_name, a.client_id, a.dealing_marchant, a.season, a.season_matrix, a.total_set_qnty, a.product_dept, a.product_code, a.pro_sub_dep, a.gmts_item_id, a.order_repeat_no, a.qlty_label,a.brand_id ,a.season_buyer_wise ,a.season_year from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no=$txt_reqsn_no and b.status_active =1 and b.is_deleted=0");
    $path=str_replace("'","",$path);
    if($path!="") $path=$path; else $path="../../";
    $nameArray_approved=sql_select( "select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_reqsn_no and b.entry_form=7 and a.status_active =1 and a.is_deleted=0 ");
    list($nameArray_approved_row)=$nameArray_approved;
    $nameArray_approved_date=sql_select( "select b.approved_date as approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_reqsn_no and b.entry_form=7 and b.approved_no='".$nameArray_approved_row[csf('approved_no')]."' and a.status_active =1 and a.is_deleted=0 ");
    list($nameArray_approved_date_row)=$nameArray_approved_date;
    $nameArray_approved_comments=sql_select( "select b.comments as comments from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_reqsn_no and b.entry_form=7 and b.approved_no='".$nameArray_approved_row[csf('approved_no')]."' and a.status_active =1 and a.is_deleted=0 ");
    list($nameArray_approved_comments_row)=$nameArray_approved_comments;
    $uom=0;
    $job_data_arr=array();
	$brand_id='';
	$season_id='';
	$season_year='';
    foreach ($po_booking_info as $result_buy){
    $job_data_arr['job_no'][$result_buy[csf('job_no')]]=$result_buy[csf('job_no')];
    $job_data_arr['job_no_in'][$result_buy[csf('job_no')]]="'".$result_buy[csf('job_no')]."'";
    $job_data_arr['total_set_qnty'][$result_buy[csf('job_no')]]=$result_buy[csf('total_set_qnty')];
    $job_data_arr['product_dept'][$result_buy[csf('job_no')]]=$product_dept[$result_buy[csf('product_dept')]];
    $job_data_arr['product_code'][$result_buy[csf('job_no')]]=$result_buy[csf('product_code')];
    $job_data_arr['pro_sub_dep'][$result_buy[csf('job_no')]]=$pro_sub_dept_array[$result_buy[csf('pro_sub_dep')]];
    $job_data_arr['gmts_item_id'][$result_buy[csf('job_no')]]=$result_buy[csf('gmts_item_id')];
    $job_data_arr['style_ref_no'][$result_buy[csf('job_no')]]=$result_buy[csf('style_ref_no')];
    $job_data_arr['style_description'][$result_buy[csf('job_no')]]=$result_buy[csf('style_description')];
    $job_data_arr['dealing_marchant'][$result_buy[csf('job_no')]]=$marchentrArr[$result_buy[csf('dealing_marchant')]];
    $job_data_arr['dealing_marchant_email'][$result_buy[csf('job_no')]]=$marchentr_email[$result_buy[csf('dealing_marchant')]];
    $job_data_arr['season_matrix'][$result_buy[csf('job_no')]]=$season_arr[$result_buy[csf('season_matrix')]];
    $job_data_arr['order_repeat_no'][$result_buy[csf('job_no')]]=$result_buy[csf('order_repeat_no')];
    $job_data_arr['qlty_label'][$result_buy[csf('job_no')]]=$quality_label[$result_buy[csf('qlty_label')]];
    $job_data_arr['client'][$result_buy[csf('job_no')]]=$result_buy[csf('client_id')];
	$brand_id=$result_buy[csf('brand_id')];
	$season_id=$result_buy[csf('season_buyer_wise')];
	$season_year=$result_buy[csf('season_year')];
    }
    $job_no= implode(",",array_unique($job_data_arr['job_no']));
    $job_no_in= implode(",",array_unique($job_data_arr['job_no_in']));
    $product_depertment=implode(",",array_unique($job_data_arr['product_dept']));
    $product_code=implode(",",array_unique($job_data_arr['product_code']));
    $pro_sub_dep=implode(",",array_unique($job_data_arr['pro_sub_dep']));
    $gmts_item_id=implode(",",array_unique($job_data_arr['gmts_item_id']));
    $style_sting=implode(",",array_unique($job_data_arr['style_ref_no']));
    $style_description=implode(",",array_unique($job_data_arr['style_description']));
    $dealing_marchant=implode(",",array_unique($job_data_arr['dealing_marchant']));
    $dealing_marchant_email=implode(",",array_unique($job_data_arr['dealing_marchant_email']));
    $season_matrix=implode(",",array_unique($job_data_arr['season_matrix']));
    $order_repeat_no= implode(",",array_unique($job_data_arr['order_repeat_no']));
    $qlty_label= implode(",",array_unique($job_data_arr['qlty_label']));
    $client_id= implode(",",array_unique($job_data_arr['client']));

    $po_data=array();
    if($db_type==0){
    $nameArray_job=sql_select( "select b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no, b.po_quantity,DATEDIFF(pub_shipment_date,po_received_date) date_diff,MIN(po_received_date) as po_received_date ,MIN(pub_shipment_date) as pub_shipment_date,MIN(b.insert_date) as insert_date,b.shiping_status  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_reqsn_no and a.status_active =1 and a.is_deleted=0   group by b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no, b.po_quantity,pub_shipment_date,po_received_date,b.insert_date,b.shiping_status ");
    }
    if($db_type==2){
    $nameArray_job=sql_select( "select b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no, b.po_quantity,(pub_shipment_date-po_received_date) date_diff,MIN(po_received_date) as po_received_date,MIN(pub_shipment_date) as pub_shipment_date,MIN(b.insert_date) as insert_date,b.shiping_status   from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_reqsn_no and a.status_active =1 and a.is_deleted=0  group by b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no, b.po_quantity,pub_shipment_date,po_received_date,b.insert_date,b.shiping_status ");
    }
    foreach ($nameArray_job as $result_job){
        $po_data['grouping'][$result_job[csf('id')]]=$result_job[csf('grouping')];
    }
    $grouping=implode(",",array_unique(array_filter($po_data['grouping'])));

    $nameArray=sql_select( "select a.buyer_id,a.booking_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.po_break_down_id,a.colar_excess_percent,a.cuff_excess_percent,a.delivery_date,a.is_apply_last_update,a.fabric_source,a.rmg_process_breakdown,a.insert_date,a.update_date,a.uom,a.remarks,a.pay_mode,a.fabric_composition,a.delivery_address, a.currency_id from wo_booking_mst a  where   a.booking_no=$txt_reqsn_no and a.status_active =1 and a.is_deleted=0 ");

    ?>
    <table style="border:1px solid black;table-layout: fixed; " width="100%">
        <tr>
            <td><img  src='<? echo $path.$imge_arr[$cbo_company_name]; ?>' height='100' width='100' /></td>
            <td style="text-align: center;">
                <span style=" font-size:20px; font-weight:bold"><? echo $company_library[$cbo_company_name]; ?></span><br>
                <span><? echo $address; ?></span><br>
                <span><? echo $add_info ?></span><br>
                <span style="font-size:16px; font-weight:bold">Short Fabric Purchase Order</span>
            </td>
            <td style="text-align: right; padding-right: 30px"><span style="font-size:16px; font-weight:bold;">Booking No: <? echo $nameArray[0][csf('booking_no')];?><? echo "(".$fabric_source[$nameArray[0][csf('fabric_source')]].")"?></span></td>
        </tr>
    </table>
    <? foreach ($nameArray as $result) {
        $currency_id=$result[csf('currency_id')];
        $booking_date=$result[csf('update_date')];
            if($booking_date=="" || $booking_date=="0000-00-00 00:00:00"){
            $booking_date=$result[csf('insert_date')];
            }
     ?>
    <table style="margin-top: 5px" class="rpt_table" border="1" cellpadding="1" cellspacing="1" rules="all" width="100%">
        <tr>
            <th width="175" style="text-align: left">Supplier Name </th>
            <td width="175"><?
                if($result[csf('pay_mode')]==5 || $result[csf('pay_mode')]==3){
                echo $company_library[$result[csf('supplier_id')]];
                }
                else{
                echo $supplier_name_arr[$result[csf('supplier_id')]];
                }
            ?>
            </td>
            <th width="175" style="text-align: left">Dealing Merchant </th>
            <td width="175" ><? echo $dealing_marchant; ?></td>
            <th width="175" style="text-align: left">Buyer/Agent Name</th>
            <td width="175"><? $buyer_name_str=""; if($client_id!=0) $buyer_name_str=$buyer_name_arr[$result[csf('buyer_id')]]."-".$buyer_name_arr[$client_id]; else $buyer_name_str=$buyer_name_arr[$result[csf('buyer_id')]]; echo $buyer_name_str; ?></td>

        </tr>
        <tr>
            <th style="text-align: left">Brand </th>
            <td><? echo $brand_arr[$brand_id]; ?></td>
            <th style="text-align: left">Season </th>
            <td><? echo $season_arr[$season_id]."-".$season_year ?></td>
            <th style="text-align: left">Garments Item </th>
            <td><?
                $gmts_item_name="";
                $gmts_item=explode(',',$gmts_item_id);
                for($g=0;$g<=count($gmts_item); $g++)
                {
                $gmts_item_name.= $garments_item[$gmts_item[$g]].",";
                }
                echo rtrim($gmts_item_name,',');
            ?>
            </td>
        </tr>
        <tr>
            <th width="175" style="text-align: left">Requisition Date </th>
            <td width="175"><? echo change_date_format($booking_date,'dd-mm-yyyy','-','');?></td>
            <th style="text-align: left">Fabric ETD </th>
            <td><? echo change_date_format( $result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>
            <th style="text-align: left">Master Style. </th>
            <td><?echo $grouping ?></td>
        </tr>
        <tr>
        	<?
        	$delivery_address=explode("\n",$result[csf('delivery_address')]);
        	?>
        	<th style="text-align: left">Delivery Address </th>
            <td><? if(count($delivery_address)>0){
            	foreach ($delivery_address as $key => $value) { ?>
            	<? echo $value ?><br>
            	<? }
            } ?></td>
            <th style="text-align: left">Pay Mode</th>
            <td><? echo $pay_mode[$result[csf('pay_mode')]]?></td>
            <th style="text-align: left">Currency </th>
            <td><? echo $currency[$result[csf('currency_id')]]?></td>
        </tr>
        <tr>
            <th style="text-align: left">Remarks </th>
            <td colspan="5"><? echo $result[csf('remarks')]?></td>
        </tr>
    </table>
    <? } ?>
    <?
    //$nameArray_fabric_description= "select  a.job_no,a.id as fabric_cost_dtls_id, a.body_part_id,a.color_type_id as c_type, a.construction, a.composition, a.gsm_weight as gsm, d.dia_width as dia,a.width_dia_type as dia_type,a.uom,sum(d.fin_fab_qnty) as fin_fab_qntys,sum(d.grey_fab_qnty) as grey_fab_qntys,avg(d.rate) as rates,sum(d.amount) as amounts ,c.style_ref_no,c.job_no_prefix_num,d.fabric_color_id as fab_color,d.gmts_color_id as gmt_color FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls d,wo_po_details_master c WHERE a.job_no=d.job_no and a.id = d.pre_cost_fabric_cost_dtls_id and a.job_no = c.job_no  and d.job_no=c.job_no and d.booking_no = $txt_reqsn_no and d.job_no in(".$job_no_in.") and d.status_active = 1 and d.is_deleted=0 group by a.job_no,a.id, a.body_part_id, a.color_type_id, a.construction, a.composition,d.fabric_color_id,d.gmts_color_id, a.gsm_weight, d.dia_width,a.uom,c.style_ref_no,c.job_no_prefix_num,a.width_dia_type order by a.job_no,d.fabric_color_id ";
    $nameArray_fabric_description = "select a.job_no,a.id as fabric_cost_dtls_id, a.body_part_id, a.color_type_id as c_type, a.construction, a.composition, a.gsm_weight as gsm, d.dia_width as dia, a.width_dia_type as dia_type,a.uom,sum(d.fin_fab_qnty) as fin_fab_qntys, sum(grey_fab_qnty) as grey_fab_qntys,sum(d.grey_fab_qnty) as grey_fab_qntys,avg(d.rate) as rates,sum(d.amount) as amounts ,c.style_ref_no, c.style_description,  c.job_no_prefix_num, d.fabric_color_id as fab_color,d.gmts_color_id as gmt_color, b.po_number,e.rd_no,e.fabric_ref FROM wo_pre_cost_fabric_cost_dtls a, wo_po_break_down b, wo_po_details_master c,  wo_booking_dtls d,lib_yarn_count_determina_mst e WHERE a.job_no=d.job_no and a.id = d.pre_cost_fabric_cost_dtls_id and e.id=a.lib_yarn_count_deter_id and a.job_no = c.job_no and d.job_no=c.job_no and d.booking_no = $txt_reqsn_no and d.job_no in(".$job_no_in.") and d.status_active = 1 and d.is_deleted=0 and b.job_no_mst=d.job_no and b.id=d.po_break_down_id and b.is_deleted=0 and b.status_active=1 group by a.job_no,a.id, a.body_part_id, a.color_type_id, a.construction, a.composition,d.fabric_color_id,d.gmts_color_id, a.gsm_weight, d.dia_width,a.uom,c.style_ref_no,c.job_no_prefix_num,a.width_dia_type , b.po_number, c.style_description,e.rd_no,e.fabric_ref order by a.job_no,d.fabric_color_id";
       // echo $nameArray_fabric_description; die;
        $result_set=sql_select($nameArray_fabric_description);
         foreach( $result_set as $row)
         {
            $uom_data_arr[$row[csf("uom")]]=$unit_of_measurement[$row[csf("uom")]];
            $main_data=array('style_ref_no','style_description');
            foreach ($main_data as $mainAttr) {
                $fabric_detail_arr[$row[csf("job_no")]][$mainAttr] = $row[csf($mainAttr)];
            }
            $fabric_attr = array('uom','construction','composition','c_type','gsm','dia','dia_type','gmt_color','style_ref_no','po_number','style_description','rd_no','fabric_ref');
            foreach ($fabric_attr as $attr) {
                $fabric_detail_arr[$row[csf("job_no")]][$row[csf('uom')]][$row[csf("construction")]][$row[csf("body_part_id")]][$row[csf("gmt_color")]][$attr][] = $row[csf($attr)];
            }

            $color_attr = array('rates');
            foreach ($color_attr as $attr) {
                $fabric_detail_arr[$row[csf("job_no")]][$row[csf('uom')]][$row[csf("construction")]][$row[csf("body_part_id")]][$row[csf("gmt_color")]]['fab_color'][$row[csf("fab_color")]][$attr] = $row[csf($attr)];
            }
            $fabric_detail_arr[$row[csf("job_no")]][$row[csf('uom')]][$row[csf("construction")]][$row[csf("body_part_id")]][$row[csf("gmt_color")]]['fab_color'][$row[csf("fab_color")]]['amounts'] += $row[csf('amounts')];	
            $fabric_detail_arr[$row[csf("job_no")]][$row[csf('uom')]][$row[csf("construction")]][$row[csf("body_part_id")]][$row[csf("gmt_color")]]['fab_color'][$row[csf("fab_color")]]['grey_fab_qntys'] += $row[csf('grey_fab_qntys')];

            $summery_attr = array('body_part_id','construction','composition','po_number','rates','amounts','grey_fab_qntys','c_type','gsm','dia','dia_type','fab_color','rd_no','fabric_ref');
            foreach ($summery_attr as $attr) {
                $string = $row[csf("body_part_id")].'**'.$row[csf("construction")].'**'.$row[csf("composition")];
                if($attr == 'fab_color'){
                    $fabric_detail_summery[$row[csf("uom")]][$string][$attr][] = $color_library[$row[csf($attr)]];
                }
                else{
                    $fabric_detail_summery[$row[csf("uom")]][$string][$attr][] = $row[csf($attr)];
                }
            }


         }
		//  echo '<pre>';
		//  print_r($fabric_detail_arr);
            /*echo '<pre>';
            print_r($fabric_detail_arr); die;*/
            //$uom_val='';
            $grand_fin_fab_qty_sum =0;
            $grand_amount_sum =0;
            foreach($uom_data_arr as $uom_id=>$uom_val){?>  
                <div style="margin-top:15px">
                    <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" width="100%" style="text-align:center;" rules="all">
                    <caption><b style="float:left"> <? echo 'Job No. : '.$job_no.', Style Ref. : '. $style_sting.', Style Des. : '.$style_description; ?></b> </caption>
                        <tr style="font-weight:bold">
                            <td width="150">Po. No.</td>
                            <td width="150">Body Part</td>
                            <td width="200">Fabric Construction</td>
                            <td width="200">Fabric Composition</td>
                            <td width="100">Color Type</td>
                            <td width="50">Ounce</td>
                            <td width="100">Cutable Width</td>
							<td width="100">RD NO</td>
							<td width="100">Fabric Ref</td>
                            <td width="100">Gmts. Color</td>
                            <td width="100">Fabric Color</td>                           
                            <td width="100">Fin Fab Qty(<? echo $uom_val ?>)</td>
                            <td width="50">Rate(<? echo $currency[$currency_id] ?>)</td>
                            <td width="50">Amount(<? echo $currency[$currency_id] ?>)</td>
                        </tr>
    <?
        $fab_color_row = '';
        foreach ($fabric_detail_arr as $job_no => $uom_data_arr) {

            $job_fin_fab_qty_sum =0;
            $job_amount_sum =0;
            foreach ($uom_data_arr as $uom_key => $construction_arr) {
                if($uom_id == $uom_key){
                   
                    foreach ($construction_arr as $cons_key => $body_part_arr) {
                        foreach ($body_part_arr as $body_part_key=>$gmt_color_data) {
                            foreach ($gmt_color_data as $gmt_color_key => $body_part_dtls){
                        $color = 1;
                        $fin_fab_qty_sum = 0;
                        $amount_sum = 0;
                        $fab_color_row = count($body_part_dtls['fab_color']);
                        foreach ($body_part_dtls['fab_color'] as $fab_color_key => $fab_color_dtls) {
                            if($color == 1){
                                $fin_fab_qty_sum += $fab_color_dtls['grey_fab_qntys'];
                                $amount_sum += $fab_color_dtls['amounts'];
                                ?>
                                <tr>
                                    <td rowspan="<? echo $fab_color_row ?>"><span style="overflow-wrap: break-word "><? echo implode(", ",array_unique($body_part_dtls['po_number'])) ?></span></td>
                                    <td rowspan="<? echo $fab_color_row ?>"><? echo $body_part[$body_part_key] ?></td>
                                    <td rowspan="<? echo $fab_color_row ?>"><? echo implode(",",array_unique($body_part_dtls['construction'])) ?></td>
                                    <td rowspan="<? echo $fab_color_row ?>"><? echo implode(",",array_unique($body_part_dtls['composition'])) ?></td>
                                    <td rowspan="<? echo $fab_color_row ?>"><? echo $color_type[implode(",",array_unique($body_part_dtls['c_type']))] ?></td>
                                    <td rowspan="<? echo $fab_color_row ?>"><? echo implode(",",array_unique($body_part_dtls['gsm'])) ?></td>
                                    <td rowspan="<? echo $fab_color_row ?>"><? echo implode(",",array_unique($body_part_dtls['dia'])).','.$fabric_typee[implode(",",array_unique($body_part_dtls['dia_type']))] ?></td>
									<td rowspan="<? echo $fab_color_row ?>"><? echo implode(",",array_unique($body_part_dtls['rd_no'])); ?></td>
									<td rowspan="<? echo $fab_color_row ?>"><? echo implode(",",array_unique($body_part_dtls['fabric_ref'])); ?></td>
                                    <td><? echo $color_library[$gmt_color_key] ?></td>
                                    <td><? echo $color_library[$fab_color_key] ?></td>
                                   
                                    <td><? echo number_format($fab_color_dtls['grey_fab_qntys'],3) ?></td>
                                    <td><? echo number_format($fab_color_dtls['rates'],2)?></td>
                                    <td><? echo number_format($fab_color_dtls['amounts'],3) ?></td>
                                </tr>
                            <? } else{
                            $fin_fab_qty_sum += $fab_color_dtls['grey_fab_qntys'];
                            $amount_sum += $fab_color_dtls['amounts'];
                            ?>
                                <tr>
                                    <td><? echo $color_library[$fab_color_key] ?></td>
                                    <td><? $lapdip_no="";
                                    $lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='".$job_no."' and approval_status=3 and color_name_id=".$fab_color_key."");
                                    if($lapdip_no=="") echo "&nbsp;"; echo $lapdip_no; ?></td>
                                    <td><? echo number_format($fab_color_dtls['grey_fab_qntys'],3) ?></td>
                                    <td><? echo number_format($fab_color_dtls['rates'],2)?></td>
                                    <td><? echo number_format($fab_color_dtls['amounts'],3) ?></td>
                                </tr>
                        <? }
                            $color++;
                        }?>
                                <!-- <tr>
                                    <th colspan="9">&nbsp</th>
                                    <th>Sub Total</th>
                                    <th><? echo def_number_format($fin_fab_qty_sum,2); ?></th>
                                    <th>&nbsp</th>
                                    <th><? echo def_number_format($amount_sum,2); ?></th>
                                </tr> -->
                    <?
                        $job_fin_fab_qty_sum += $fin_fab_qty_sum;
                        $job_amount_sum += $amount_sum;

                     }
                        }


                    }
                    $grand_fin_fab_qty_sum +=$job_fin_fab_qty_sum;
                    $grand_amount_sum += $job_amount_sum;
                    ?>
                            <tr>
                                <th colspan="9">&nbsp</th>
                                <th>Job Total</th>
                                <th><? echo def_number_format($job_fin_fab_qty_sum,2); ?></th>
                                <th>&nbsp</th>
                                <th><? echo def_number_format($job_amount_sum,2); ?></th>
                            </tr>

                    <?


                 }
            }
        }

    ?>

        </table>
    </div>
    <?
    $mcurrency="";
   $dcurrency="";
   if($currency_id==1)
   {
	$mcurrency='Taka';
	$dcurrency='Paisa';
   }
   if($currency_id==2)
   {
	$mcurrency='USD';
	$dcurrency='CENTS';
   }
   if($currency_id==3)
   {
	$mcurrency='EURO';
	$dcurrency='CENTS';
   }
       } ?>
    <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" width="100%" style="text-align:center;" rules="all">
        <tr>
            <th colspan="9" width="350">&nbsp</th>
            <th width="106">Grand Total</th>
            <th width="86"><? echo def_number_format($grand_fin_fab_qty_sum,2); ?></th>
            <th width="74">&nbsp</th>
            <th width="99"><? echo def_number_format($grand_amount_sum,2); ?></th>
        </tr>
    </table>
    <div style="margin-top:15px">
        <? foreach ($fabric_detail_summery as $uom_id => $summery_data_arr) {
        ?>
        <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" width="100%" style="text-align:center; margin-top: 15px" rules="all">
        	<tr><td colspan="11" style="font-weight:bold; font-size: 16px">Report Summery</td></tr>
            <tr style="font-weight:bold">
                <td>Fabric Type</td>
                <td>Construction</td>
                <td>Composition</td>
                <td>Color Type</td>
                <td>Ounce</td>
                <td>Cutable Width</td>
				<td>RD No</td>
				<td>Fabric Ref</td>             
                <td>No. of PO</td>
                <td>Consumption(<? echo $unit_of_measurement[$uom_id] ?>)</td>
                <td>Rate(<? echo $currency[$currency_id] ?>)</td>
                <td>Amount(<? echo $currency[$currency_id] ?>)</td>
            </tr>
        <?
        foreach ($summery_data_arr as $summery_data) { ?>
            <tr>
                <td><? echo $body_part[implode(",",array_unique($summery_data['body_part_id']))] ?></td>
                <td><? echo implode(",",array_unique($summery_data['construction']))?></td>
                <td><? echo implode(",",array_unique($summery_data['composition']))?></td>
                <td><? echo $color_type[implode(",",array_unique($summery_data['c_type']))] ?></td>
                <td><? echo implode(",",array_unique($summery_data['gsm']))?></td>
                <td><? echo implode(",",array_unique($summery_data['dia'])).','.$fabric_typee[implode(",",array_unique($summery_data['dia_type']))] ?></td>
                <td><?  echo implode(",",array_unique($summery_data['rd_no'])) ?></td>
				<td><?  echo implode(",",array_unique($summery_data['fabric_ref'])) ?></td>
                <td><? echo count($summery_data['po_number']) ?></td>
                <td><? echo array_sum($summery_data['grey_fab_qntys']) ?></td>
                <td><? echo number_format(array_sum($summery_data['rates']),2)?></td>
                <td><? echo array_sum($summery_data['amounts']) ?></td>
            </tr>

        <? }
        }
        ?>
        </table>
    </div>

    <div style="margin-top: 10px;">
		<table width="100%" class="rpt_table"  border="1" cellpadding="0" cellspacing="0" rules="all">
            <tr style="border:1px solid black;">
                <td width="30%" style="border:1px solid black; text-align:left">Total Booking Amount</td>
                <td width="70%" style="border:1px solid black; text-align:left"><? echo number_format($grand_amount_sum,2);?></td>
            </tr>
            <tr style="border:1px solid black;">
                <td width="30%" style="border:1px solid black; text-align:left">Total Booking Amount (in word)</td>
                <td width="70%" style="border:1px solid black;"><? echo number_to_words(def_number_format($grand_amount_sum,2,""),$mcurrency, $dcurrency);?></td>
            </tr>
       </table>
	</div>


    <table  width="100%"  border="0" cellpadding="0" cellspacing="0" style="margin-top: 10px; ">
        <tr>
            <td width="50%" style="border:1px solid; border-color:#000;" valign="top">
                <table  width="100%"  border="0" cellpadding="0" cellspacing="0">
                    <thead>
                        <tr>
                        <th width="97%" align="left" height="30" colspan="2">Terms &amp; Condition</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
                    $data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no=$txt_reqsn_no");
                    if ( count($data_array)>0)
                        {
                        $i=0;
                        foreach( $data_array as $row )
                        {
                        $i++;
                        ?>
                        <tr id="settr_1" valign="top">
                            <td><? echo $i;?>)&nbsp</td>
                            <td><? echo $row[csf('terms')]; ?></td>
                        </tr>
                        <?
                        }
                        }
                    ?>
                    </tbody>
                </table>
            </td>
            <td width="50%" valign="top" style="border:1px solid; border-color:#000;">
			    <?
			     $desg_name=return_library_array( "select id, custom_designation from lib_designation", "id", "custom_designation"  );
				 $data_array=sql_select("select b.approved_by,b.approved_no, b.approved_date, c.user_full_name,c.designation from  wo_booking_mst a , approval_history b, user_passwd c where a.id=b.mst_id and b.approved_by=c.id and a.booking_no=$txt_reqsn_no and b.entry_form=12 and is_approved=1 order by b.id asc");
				?>
				<table  width="100%" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
				    <thead>
				    <tr style="border:1px solid black;">
				        <th colspan="3" style="border:1px solid black;">Approval Status</th>
				        </tr>

				    </thead>
				    <tbody>
				    <?
					$s=1;
					if(count($data_array) > 0){ ?>
						<tr style="border:1px solid black;">
				        <th width="3%" style="border:1px solid black;">Sl</th><th width="25%" style="border:1px solid black;">Name</th><th width="25%" style="border:1px solid black;">Designation</th><th width="27%" style="border:1px solid black;">Approval Date</th>
				        </tr>
					<?
						foreach($data_array as $row){
						?>
				        <tr style="border:1px solid black;">
				            <td width="3%" style="border:1px solid black;"><? echo $s;?></td><td width="25%" style="border:1px solid black;"><? echo $row[csf('user_full_name')];?></td><td width="25%" style="border:1px solid black;"><? echo $desg_name[$row[csf('designation')]]; ?></td><td width="27%" style="border:1px solid black;"><? echo date ("d-m-Y h:i:s",strtotime($row[csf('approved_date')]));?></td>
				            </tr>
				            <?
							$s++;
						}
					}
					else{?>
				       <tr style="border:1px solid black;">
				       	<td colspan="3" Style="font-weight:bold; text-align:center; font-size:24px;">Draft</td>
				       </tr>
				    <? }
				    ?>
				    </tbody>
				</table>
            </td>
        </tr>
    </table>
    <? echo signature_table(121, $cbo_company_name, "1330px", 1); ?>


<? 
}

if($action=="show_fabric_requisition_report")//unused
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$report_type=str_replace("'","",$report_type);
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	//$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	$po_qnty_tot1=return_field_value( "sum(po_quantity)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	$pro_sub_dept_array=return_library_array( "select id,sub_department_name from lib_pro_sub_deparatment",'id','sub_department_name');
	?>

	<div style="width:1330px" align="center">
    										<!--    Header Company Information         -->
        <?php
$nameArray_approved = sql_select("select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_reqsn_no and b.entry_form=12");
list($nameArray_approved_row) = $nameArray_approved;
$nameArray_approved_date = sql_select("select b.approved_date as approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_reqsn_no and b.entry_form=12 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "'");
list($nameArray_approved_date_row) = $nameArray_approved_date;
$nameArray_approved_comments = sql_select("select b.comments as comments from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_reqsn_no and b.entry_form=12 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "'");
list($nameArray_approved_comments_row) = $nameArray_approved_comments;
$path = str_replace("'", "", $path);
if ($path == "") {
	$path = '../../';
}
?>
       <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black">
           <tr>
               <td width="100">
               <?
               if($report_type==1)
			   {
			   ?>
               <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               <?
			   }
			  else if($report_type==2)
			   {
			   ?>
               <img  src='../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               <?
			   }
			   else
			   {
				  ?>
              	 <img  src='<? echo $path.$imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
              	 <?
			   }
			   ?>
               </td>
               <td width="1000">
                    <table width="100%" cellpadding="0" cellspacing="0" >
                        <tr>

                            <td align="center" style="font-size:20px;">
                              <?php
echo $company_library[$cbo_company_name];
?>
                            </td>
                            <td rowspan="3"  width="240">

                                <!--<a href="requires/fabric_booking_controller.php?filename=welcome.html&action=download_file" style="text-transform:none">Download</a>-->
                               <span style="font-size:18px"> &nbsp;&nbsp;&nbsp;&nbsp;<b> Job No:&nbsp;<? echo trim($txt_job_no,"'"); ?></b><br>
                                <?
								 if($nameArray_approved_row[csf('approved_no')]>1)
								 {
								 ?>
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  <strong> Revised No: &nbsp;<? echo $nameArray_approved_row[csf('approved_no')]-1; ?></strong>
								  <?
								 }
							  	?>
                               </span>


                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px">
                            <?
                            $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
                            foreach ($nameArray as $result)
                            {
                            ?>
                                Plot No: <? echo $result[csf('plot_no')]; ?>
                                Level No: <? echo $result[csf('level_no')]?>
                                Road No: <? echo $result[csf('road_no')]; ?>
                                Block No: <? echo $result[csf('block_no')];?>
                                City No: <? echo $result[csf('city')];?>
                                Zip Code: <? echo $result[csf('zip_code')]; ?>
                                Province No: <?php echo $result[csf('province')]; ?>
                                Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
                                Email Address: <? echo $result[csf('email')];?>
                                Website No: <? echo $result[csf('website')];?>
                            <?
                            }
                            ?>

                               </td>
                            </tr>
                            <tr>
                            <td align="center" style="font-size:20px">
                            <strong><?
							if($report_type==1) echo str_replace("'","",$report_title);else echo 'Short Fabric Booking';?> &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:#F00"><? if(str_replace("'","",$id_approved_id) ==1){ echo "(Approved)";}if(str_replace("'","",$id_approved_id) ==3){ echo "(Partial Approved)";}; ?> </font></strong>
                             </td>
                             <td  width="150"style="font-size:20px">

                             </td>
                            </tr>
                      </table>
                </td>
                <td width="250" id="barcode_img_id">

               </td>
            </tr>
       </table>

                   <?
					$job_no='';
					$total_set_qnty=0;
					$colar_excess_percent=0;
					$cuff_excess_percent=0;
					$nameArray=sql_select( "select a.booking_no,a.booking_date,a.supplier_id,a.pay_mode,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.po_break_down_id,a.colar_excess_percent,a.cuff_excess_percent,a.delivery_date,a.is_apply_last_update,a.fabric_source,b.job_no,b.buyer_name, b.style_ref_no ,b.gmts_item_id,b.order_uom,b.total_set_qnty,b.style_description,b.season,b.product_dept,b.product_code,b.pro_sub_dep,b.dealing_marchant,b.factory_marchant from wo_booking_mst a, wo_po_details_master b where  a.job_no=b.job_no and a.booking_no=$txt_reqsn_no");
					foreach ($nameArray as $result)
					{
						$total_set_qnty=$result[csf('total_set_qnty')];
						$colar_excess_percent=$result[csf('colar_excess_percent')];
						$cuff_excess_percent=$result[csf('cuff_excess_percent')];
						$po_no="";
						$shipment_date="";	$internal_ref="";	$file_no="";
						$sql_po= "select po_number,grouping,file_no,MIN(pub_shipment_date) pub_shipment_date from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].") group by po_number,grouping,file_no";
						$data_array_po=sql_select($sql_po);
						foreach ($data_array_po as $row_po)
						{
							$po_no.=$row_po[csf('po_number')].", ";
							$shipment_date.=change_date_format($row_po[csf('pub_shipment_date')],'dd-mm-yyyy','-').", ";
							$internal_ref.=$row_po[csf('grouping')].", ";
							$file_no.=$row_po[csf('file_no')].", ";
						}

						$lead_time="";
						if($db_type==0)
						{
						$sql_lead_time= "select DATEDIFF(pub_shipment_date,po_received_date) date_diff from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")";
						}

						if($db_type==2)
						{
						$sql_lead_time= "select (pub_shipment_date-po_received_date) date_diff from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")";
						}
						$data_array_lead_time=sql_select($sql_lead_time);
						foreach ($data_array_lead_time as $row_lead_time)
						{
							$lead_time.=$row_lead_time[csf('date_diff')].",";
							//$shipment_date.=change_date_format($row_po['pub_shipment_date'],'dd-mm-yyyy','-').",";
						}

						$po_received_date="";
						$sql_po_received_date= "select MIN(po_received_date) as po_received_date from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")";
						$data_array_po_received_date=sql_select($sql_po_received_date);
						foreach ($data_array_po_received_date as $row_po_received_date)
						{
							$po_received_date=change_date_format($row_po_received_date[csf('po_received_date')],'dd-mm-yyyy','-');
							//$shipment_date.=change_date_format($row_po['pub_shipment_date'],'dd-mm-yyyy','-').",";
						}
						$sql_po= "select po_number,MIN(pub_shipment_date) pub_shipment_date, MIN(insert_date) as insert_date,shiping_status from wo_po_break_down  where id in(".$result[csf('po_break_down_id')].") group by po_number,shiping_status";
						$data_array_po=sql_select($sql_po);
						foreach ($data_array_po as $rows)
						{
							$daysInHand.=(datediff('d',date('d-m-Y',time()),$rows[csf('pub_shipment_date')])-1).",";
							$booking_date=$result[csf('update_date')];
							if($booking_date=="" || $booking_date=="0000-00-00 00:00:00")
							{
								$booking_date=$result[csf('insert_date')];
							}
							$WOPreparedAfter.=(datediff('d',$rows[csf('insert_date')],$booking_date)-1).",";

							if($rows[csf('shiping_status')]==1)
							{
							$shiping_status.= "FP".",";
							}
							else if($rows[csf('shiping_status')]==2)
							{
							$shiping_status.= "PS".",";
							}
							else if($rows[csf('shiping_status')]==3)
							{
							$shiping_status.= "FS".",";
							}
						}

					$file=rtrim($file_no,", ");//rtrim($po_no,", ")
					$file_all=array_unique(explode(",",$file));

					$file='';
					foreach($file_all as $file_id)
					{
						if($file=="") $file_cond=$file_id; else $file_cond.=", ".$file_id;
					}

					$varcode_booking_no=$result[csf('booking_no')];

				?>
       <table width="100%" style="border:1px solid black" >
            <tr>
                <td colspan="6" valign="top" style="font-size:18px; color:#F00"><? if($result[csf('is_apply_last_update')]==2){echo "Booking Info not synchronized with order entry and pre-costing. order entry or pre-costing has updated after booking entry.  Contact to ".$marchentrArr[$result[csf('dealing_marchant')]]; } else{ echo "";} ?></td>
            </tr>
            <tr>
                <td width="100"><span style="font-size:18px"><b>Buyer/Agent Name</b></span></td>
                <td width="110">:&nbsp;<span style="font-size:18px"><b><? echo $buyer_name_arr[$result[csf('buyer_name')]]; ?></b></span></td>
                <td width="100"><span style="font-size:12px"><b>Dept.</b></span></td>
                <td width="110">:&nbsp;<? echo $product_dept[$result[csf('product_dept')]] ; if($result[csf('product_code')] !=""){ echo " (".$result[csf('product_code')].")";} if($result[csf('pro_sub_dep')] !=0){ echo " (".$pro_sub_dept_array[$result[csf('pro_sub_dep')]].")";}?></td>
                <td width="100"><span style="font-size:12px"><b>Order Qnty</b></span></td>
                <td width="110">:&nbsp;
				<?  echo $po_qnty_tot1." ".$unit_of_measurement[$result[csf('order_uom')]] ; ?>
                </td>
            </tr>
            <tr>

                <td width="100" style="font-size:12px"><b>Garments Item</b></td>
                <td width="110">:&nbsp;
				<?
				$gmts_item_name="";
				$gmts_item=explode(',',$result[csf('gmts_item_id')]);
				for($g=0;$g<=count($gmts_item); $g++)
				{
					$gmts_item_name.= $garments_item[$gmts_item[$g]].",";
				}
				echo rtrim($gmts_item_name,',');
				?>
                </td>
                <td width="100" style="font-size:12px"><b>Requisition Date</b></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('booking_date')],'dd-mm-yyyy','-');?>&nbsp;&nbsp;&nbsp;</td>
                <td width="100" style="font-size:18px"><b>Style Ref.</b>   </td>
                <td width="110" style="font-size:18px">:&nbsp;<b><? echo $style_sting=$result[csf('style_ref_no')];?> </b>   </td>
            </tr>
             <tr>
                <td  width="100" style="font-size:12px"><b>Style Des.</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('style_description')]; $job_no= $result[csf('job_no')];?></td>
                <td width="100" style="font-size:12px"><b>Lead Time </b>   </td>
                <td width="110">:&nbsp;<?  echo rtrim($lead_time,",");;?> </td>
                <td width="100" style="font-size:12px"><b>Factory Merchant</b></td>
                <td width="110">:&nbsp;<? echo $marchentrArr[$result[csf('factory_marchant')]]; ?></td>
            </tr>

            <tr>
                <td width="100" style="font-size:18px"><b>Supplier Name</b>   </td>
                <td width="110" style="font-size:18px">:&nbsp; <b><?
				if($result[csf('pay_mode')]==5 || $result[csf('pay_mode')]==3){
					echo $company_library[$result[csf('supplier_id')]];
					}
					else{
					echo $supplier_name_arr[$result[csf('supplier_id')]];
					}
				//echo $supplier_name_arr[$result[csf('supplier_id')]];?></b>    </td>
                <td width="100" style="font-size:12px"><b>Delivery Date</b></td>
               	<td width="110">:&nbsp;<? echo change_date_format( $result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>
                <td width="100" style="font-size:18px"><b>Booking No </b>   </td>
                <td width="110" style="font-size:18px">:&nbsp;<b><? echo $result[csf('booking_no')];?></b><? echo "(".$fabric_source[$result[csf('fabric_source')]].")"?></td>
            </tr>
            <tr>
                <td width="100" style="font-size:12px"><b>Season</b></td>
                <td width="110">:&nbsp;<? echo $result[csf('season')]; ?></td>
                <td  width="100" style="font-size:12px"><b>Attention</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('attention')]; ?></td>
                <td  width="100" style="font-size:12px"><b>Po Received Date</b></td>
                <td  width="110" >:&nbsp;<? echo $po_received_date; ?></td>
            </tr>
           <tr>
               <td width="100" style="font-size:18px"><b>Order No</b></td>
                <td width="110" style="font-size:18px" colspan="3">:&nbsp;<b><? echo rtrim($po_no,", "); ?></b></td>
                <td width="100" style="font-size:17px"><b>Internal Ref</b></td>
                <td width="110" style="font-size:16px">:&nbsp;<b><? echo rtrim($internal_ref,", "); ?></b></td>

            </tr>
            <tr>
               <td width="100" style="font-size:12px"><b>Shipment Date</b></td>
                <td width="110" colspan="3"> :&nbsp;<? echo rtrim($shipment_date,", "); ?></td>
                <td width="100" style="font-size:17px"><b>File No</b></td>
                <td width="110" style="font-size:16px">:&nbsp;<b><? echo rtrim($file_cond,", "); ?></b></td>
            </tr>
             <tr>
               <td width="110" style="font-size:12px"><b>WO Prepared After</b></td>
               <td width="300"> :&nbsp;<? echo rtrim($WOPreparedAfter,',').' Days' ?></td>

               <td width="100" style="font-size:12px"><b>Ship.days in Hand</b></td>
               <td width="300"> :&nbsp;<? echo rtrim($daysInHand,',').' Days'?></td>

               <td width="100" style="font-size:12px"><b>Ex-factory status</b></td>
               <td> :&nbsp;<? echo rtrim($shiping_status,','); ?></td>
            </tr>
        </table>
        <?
			}
		?>

      <br/>   									 <!--  Here will be the main portion  -->

     <?
	 $costing_per="";
	 $costing_per_qnty=0;
	 $costing_per_id=return_field_value( "costing_per", "wo_pre_cost_mst","job_no ='$job_no'");
	 if($costing_per_id==1)
			{
				$costing_per="1 Dzn";
				$costing_per_qnty=12;

			}
			if($costing_per_id==2)
			{
				$costing_per="1 Pcs";
				$costing_per_qnty=1;

			}
			if($costing_per_id==3)
			{
				$costing_per="2 Dzn";
				$costing_per_qnty=24;

			}
			if($costing_per_id==4)
			{
				$costing_per="3 Dzn";
				$costing_per_qnty=36;

			}
			if($costing_per_id==5)
			{
				$costing_per="4 Dzn";
				$costing_per_qnty=48;

			}
			$process_loss_method=return_field_value( "process_loss_method", "wo_pre_cost_fabric_cost_dtls","job_no='$job_no'");;
//echo $process_loss_method."gggg";
	 ?>
     <?
	if(str_replace("'","",$cbo_fabric_source)==1 || str_replace("'","",$cbo_fabric_source)==3 )
	  {
	$nameArray_fabric_description= sql_select("SELECT  a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width,avg(b.process_loss_percent) as process_loss_percent,avg(rmg_qty) as rmg_qty FROM wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls b where b.booking_no =$txt_reqsn_no and a.id=b.pre_cost_fabric_cost_dtls_id  and b.status_active=1 and
b.is_deleted=0  and b.grey_fab_qnty>0 group by a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width order by a.body_part_id");
	 ?>
     <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
     <tr align="center"><th colspan="3" align="left">Body Part</th>
        <?
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('body_part_id')] == "")  echo "<td  colspan='2'>&nbsp</td>";
			else         		               echo "<td  colspan='2'>". $body_part[$result_fabric_description[csf('body_part_id')]]."</td>";
		}
		?>
        <td  rowspan="9" width="50"><p>Total  Finish Fabric <? if(trim($cbo_fabric_natu ,"'")==2){echo "(KG)";}else{echo "(Yds)";} ?></p></td>
        <td  rowspan="9" width="50"><p>Total Grey Fabric <? if(trim($cbo_fabric_natu ,"'")==2){echo "(KG)";}else{echo "(Yds)";} ?></p></td>
        <td  rowspan="9" width="50"><p>Process Loss % </p></td>
       </tr>
     <tr align="center"><th colspan="3" align="left">Color Type</th>
        <?
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('color_type_id')] == "")  echo "<td  colspan='2'>&nbsp</td>";
			else         		               echo "<td  colspan='2'>". $color_type[$result_fabric_description[csf('color_type_id')]]."</td>";
		}
		?>
       </tr>
        <tr align="center"><th colspan="3" align="left">Construction</th>
        <?
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('construction')] == "")  echo "<td  colspan='2'>&nbsp</td>";
			else         		               echo "<td  colspan='2'>". $result_fabric_description[csf('construction')]."</td>";
		}
		?>


       </tr>
        <tr align="center"><th   colspan="3" align="left">Composition</th>
        <?
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('composition')] == "")   echo "<td colspan='2' >&nbsp</td>";
			else         		               echo "<td colspan='2' >".$result_fabric_description[csf('composition')]."</td>";
		}
		?>

       </tr>
       <tr align="center"><th  colspan="3" align="left">GSM</th>
        <?
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('gsm_weight')] == "")   echo "<td colspan='2'>&nbsp</td>";
			else         		       echo "<td colspan='2' align='center'>". $result_fabric_description[csf('gsm_weight')]."</td>";
		}
		?>

       </tr>
       <tr align="center"><th   colspan="3" align="left">Dia/Width</th>
        <?
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('dia_width')] == "")   echo "<td colspan='2'>&nbsp</td>";
			else         		              echo "<td colspan='2' align='center'>". $result_fabric_description[csf('dia_width')]."</td>";
		}
		?>

       </tr>
       <tr align="center"><th   colspan="3" align="left">RMG Qty</th>
        <?
		$tot_rmg_qty=0;
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			$tot_rmg_qty+=$result_fabric_description[csf('rmg_qty')];
			if( $result_fabric_description[csf('rmg_qty')] == "")   echo "<td colspan='2'>&nbsp</td>";
			else         		              echo "<td colspan='2' align='center'>". $result_fabric_description[csf('rmg_qty')]."</td>";
		}
		?>

       </tr>
       <tr>
       <th  colspan="<? echo  count($nameArray_fabric_description)*2+3; ?>" align="left" style="height:30px">&nbsp;</th>
       </tr>
       <tr>
            <th  width="120" align="left">Fabric Color</th>
            <th  width="120" align="left"> Body Color</th>
            <th  width="120" align="left">Lab Dip No</th>
        <?
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			  echo "<th width='50'>Finish</th><th width='50' >Gray</th>";
		}
		?>

       </tr>
       <?
	      /*$gmt_color_library=return_library_array( "select gmts_color_id,fabric_color_id
		  FROM
		  wo_booking_dtls
		  WHERE
		  job_no ='$job_no'
		  group by gmts_color_id", "fabric_color_id", "gmts_color_id");*/

		  $gmt_color_library=array();
		  $gmt_color_data=sql_select("select gmts_color_id,fabric_color_id
		  FROM
		  wo_booking_dtls
		  WHERE
		  booking_no =$txt_reqsn_no");
		  foreach( $gmt_color_data as $gmt_color_row)
		  {
			$gmt_color_library[$gmt_color_row[csf("fabric_color_id")]].=$color_library[$gmt_color_row[csf("gmts_color_id")]]."," ;
		  }

	        $grand_total_fin_fab_qnty=0;
			$grand_total_grey_fab_qnty=0;
			$grand_totalcons_per_finish=0;
			$grand_totalcons_per_grey=0;
			$color_wise_wo_sql=sql_select("select fabric_color_id ,gmts_color_id
										  FROM
										  wo_booking_dtls
										  WHERE
										  booking_no =$txt_reqsn_no and
										  status_active=1 and
                                          is_deleted=0
										  group by fabric_color_id,gmts_color_id");
		foreach($color_wise_wo_sql as $color_wise_wo_result)
	    {
		?>
			<tr>
           <td  width="120" align="left">
			<?

			echo $color_library[$color_wise_wo_result[csf('fabric_color_id')]];

			?></td>
            <td  width="120" align="left">
			<?
			//echo $gmt_color_library[$color_wise_wo_result[csf('fabric_color_id')]];
			echo  $color_library[$color_wise_wo_result[csf('gmts_color_id')]];
			?>
            </td>
            <td  width="120" align="left">
			<?
			$lapdip_no="";
			$lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='".$job_no."' and approval_status=3 and color_name_id=".$color_wise_wo_result[csf('fabric_color_id')]."");
			if($lapdip_no=="") echo "&nbsp;"; echo $lapdip_no;
			?>
            </td>
            <?
			$total_fin_fab_qnty=0;
			$total_grey_fab_qnty=0;

			foreach($nameArray_fabric_description as $result_fabric_description)
		    {



				if($db_type==2)
				{
				$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty
												  FROM
												  wo_pre_cost_fabric_cost_dtls a,
												  wo_booking_dtls b
												  WHERE
												  b.booking_no =$txt_reqsn_no  and
												  a.id=b.pre_cost_fabric_cost_dtls_id and
												   nvl(a.body_part_id,0)=nvl('".$result_fabric_description[csf('body_part_id')]."',0) and
												   nvl(a.color_type_id,0)=nvl('".$result_fabric_description[csf('color_type_id')]."',0) and
												   nvl(a.construction,0)=nvl('".$result_fabric_description[csf('construction')]."',0) and
												   nvl(a.composition,0)=nvl('".$result_fabric_description[csf('composition')]."',0) and
												   nvl(a.gsm_weight,0)=nvl('".$result_fabric_description[csf('gsm_weight')]."',0) and
												   nvl(b.dia_width,0)=nvl('".$result_fabric_description[csf('dia_width')]."',0) and
												   nvl(b.fabric_color_id,0)=nvl(".$color_wise_wo_result[csf('fabric_color_id')].",0) and
												   nvl(b.gmts_color_id,0)=nvl(".$color_wise_wo_result[csf('gmts_color_id')].",0) and

												  b.status_active=1 and
												  b.is_deleted=0 and b.grey_fab_qnty>0");
				}

				if($db_type==0)
				{
				$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty
												  FROM
												  wo_pre_cost_fabric_cost_dtls a,
												  wo_booking_dtls b
												  WHERE
												  b.booking_no =$txt_reqsn_no  and
												  a.id=b.pre_cost_fabric_cost_dtls_id and
												   a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
												   a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
												   a.construction='".$result_fabric_description[csf('construction')]."' and
												   a.composition='".$result_fabric_description[csf('composition')]."' and
												   a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
												   b.dia_width='".$result_fabric_description[csf('dia_width')]."' and
												   b.fabric_color_id=".$color_wise_wo_result[csf('fabric_color_id')]." and
												   b.gmts_color_id=".$color_wise_wo_result[csf('gmts_color_id')]." and
												  b.status_active=1 and
												  b.is_deleted=0 and b.grey_fab_qnty>0");
				}


				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty

			?>
			<td width='50' align='right'>
			<?
			if($color_wise_wo_result_qnty[csf('fin_fab_qnty')]!="")
			{
			echo number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')],2) ;
			$total_fin_fab_qnty+=$color_wise_wo_result_qnty[csf('fin_fab_qnty')];
			}
			?>
            </td>
            <td width='50' align='right' >
			<?
			if($color_wise_wo_result_qnty[csf('grey_fab_qnty')]!="")
			{
			echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],2);
			$total_grey_fab_qnty+=$color_wise_wo_result_qnty[csf('grey_fab_qnty')];
			}
			?>
            </td>
            <?
			}
			?>
            <td align="right"><? echo number_format($total_fin_fab_qnty,2); $grand_total_fin_fab_qnty+=$total_fin_fab_qnty;?></td>
            <td align="right"><? echo number_format($total_grey_fab_qnty,2); $grand_total_grey_fab_qnty+=$total_grey_fab_qnty;?></td>

            <td align="right">
            <?
			if($process_loss_method==1)
			{
				$process_percent=(($total_grey_fab_qnty-$total_fin_fab_qnty)/$total_fin_fab_qnty)*100;
			}

			if($process_loss_method==2)
			{
				$process_percent=(($total_grey_fab_qnty-$total_fin_fab_qnty)/$total_grey_fab_qnty)*100;
			}
			echo number_format($process_percent,2);

			?>
            </td>
            </tr>
         <?
		}
		?>
        <tr>
        <td  width="120" align="left">&nbsp;</td>
        <td  width="120" align="left">&nbsp;</td>
        <td  width="120" align="left"><strong>Total</strong></td>
        <?
			foreach($nameArray_fabric_description as $result_fabric_description)
		    {
				if($db_type==2)
				{
				$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty
												  FROM
												  wo_pre_cost_fabric_cost_dtls a,
												  wo_booking_dtls b
												  WHERE
												  b.booking_no =$txt_reqsn_no  and
												  a.id=b.pre_cost_fabric_cost_dtls_id and
												  nvl(a.body_part_id,0)=nvl('".$result_fabric_description[csf('body_part_id')]."',0) and
												  nvl(a.color_type_id,0)=nvl('".$result_fabric_description[csf('color_type_id')]."',0) and
												  nvl(a.construction,0)=nvl('".$result_fabric_description[csf('construction')]."',0) and
												  nvl(a.composition,0)=nvl('".$result_fabric_description[csf('composition')]."',0) and
												  nvl(a.gsm_weight,0)=nvl('".$result_fabric_description[csf('gsm_weight')]."',0) and
												  nvl(b.dia_width,0)=nvl('".$result_fabric_description[csf('dia_width')]."',0) and
												  b.status_active=1 and
												  b.is_deleted=0 and b.grey_fab_qnty>0
												  ");
				}

				if($db_type==0)
				{
				$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty
												  FROM
												  wo_pre_cost_fabric_cost_dtls a,
												  wo_booking_dtls b
												  WHERE
												  b.booking_no =$txt_reqsn_no  and
												  a.id=b.pre_cost_fabric_cost_dtls_id and
												  a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
												  a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
												  a.construction='".$result_fabric_description[csf('construction')]."' and
												  a.composition='".$result_fabric_description[csf('composition')]."' and
												  a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
												  b.dia_width='".$result_fabric_description[csf('dia_width')]."' and
												  b.status_active=1 and
												  b.is_deleted=0 and b.grey_fab_qnty>0
												  ");
				}
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
			?>
			<td width='50' align='right'><?  echo number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')],2) ;?></td><td width='50' align='right' > <? echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],2);?></td>
            <?
			}
			?>
            <td align="right"><? echo number_format($grand_total_fin_fab_qnty,2);?></td>
            <td align="right"><? echo number_format($grand_total_grey_fab_qnty,2);?></td>
            <td align="right">
            <?
            if($process_loss_method==1)
			{
				$totalprocess_percent=(($grand_total_grey_fab_qnty-$grand_total_fin_fab_qnty)/$grand_total_grey_fab_qnty)*100;
			}

			if($process_loss_method==2)
			{
				$totalprocess_percent=(($grand_total_grey_fab_qnty-$grand_total_fin_fab_qnty)/$grand_total_grey_fab_qnty)*100;
			}
			echo number_format($totalprocess_percent,2);
			?>
            </td>
            </tr>
    </table>

    <br/>
    <?
	  }
    ?>





        <?

	 if(str_replace("'","",$cbo_fabric_source)==2)
	  {

	$nameArray_fabric_description= sql_select("SELECT  a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width,avg(b.process_loss_percent) as process_loss_percent,avg(rmg_qty) as rmg_qty FROM wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls b where b.booking_no =$txt_reqsn_no and a.id=b.pre_cost_fabric_cost_dtls_id  and b.status_active=1 and
b.is_deleted=0 and b.grey_fab_qnty>0 group by a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width order by a.body_part_id");
	 ?>
     <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
     <tr align="center"><th colspan="3" align="left">Body Part</th>
        <?
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('body_part_id')] == "")  echo "<td  colspan='3'>&nbsp</td>";
			else         		               echo "<td  colspan='3'>". $body_part[$result_fabric_description[csf('body_part_id')]]."</td>";
		}
		?>
        <td  rowspan="9" width="50"><p>Total Fabric <? if(trim($cbo_fabric_natu ,"'")==2){echo "(KG)";}else{echo "(Yds)";} ?></p></td>
        <td  rowspan="9" width="50"><p>Rate</p></td>
        <td  rowspan="9" width="50"><p>Amount </p></td>
       </tr>
     <tr align="center"><th colspan="3" align="left">Color Type</th>
        <?
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('color_type_id')] == "")  echo "<td  colspan='3'>&nbsp</td>";
			else         		               echo "<td  colspan='3'>". $color_type[$result_fabric_description[csf('color_type_id')]]."</td>";
		}
		?>
       </tr>
        <tr align="center"><th colspan="3" align="left">Construction</th>
        <?
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('construction')] == "")  echo "<td  colspan='3'>&nbsp</td>";
			else         		               echo "<td  colspan='3'>". $result_fabric_description[csf('construction')]."</td>";
		}
		?>


       </tr>
        <tr align="center"><th   colspan="3" align="left">Composition</th>
        <?
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('composition')] == "")   echo "<td colspan='3' >&nbsp</td>";
			else         		               echo "<td colspan='3' >".$result_fabric_description[csf('composition')]."</td>";
		}
		?>

       </tr>
       <tr align="center"><th  colspan="3" align="left">GSM</th>
        <?
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('gsm_weight')] == "")   echo "<td colspan='3'>&nbsp</td>";
			else         		       echo "<td colspan='3' align='center'>". $result_fabric_description[csf('gsm_weight')]."</td>";
		}
		?>

       </tr>
       <tr align="center"><th   colspan="3" align="left">Dia/Width</th>
        <?
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('dia_width')] == "")   echo "<td colspan='3'>&nbsp</td>";
			else         		              echo "<td colspan='3' align='center'>". $result_fabric_description[csf('dia_width')]."</td>";
		}
		?>

       </tr>
       <tr align="center"><th   colspan="3" align="left">RMG Qty</th>
        <?
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('rmg_qty')] == "")   echo "<td colspan='3'>&nbsp</td>";
			else         		              echo "<td colspan='3' align='center'>". $result_fabric_description[csf('rmg_qty')]."</td>";
		}
		?>

       </tr>
       <tr>
       <th  colspan="<? echo  count($nameArray_fabric_description)*3+3; ?>" align="left" style="height:30px">&nbsp;</th>
       </tr>
       <tr>
            <th  width="120" align="left">Fabric Color</th>
            <th  width="120" align="left"> Body Color</th>
            <th  width="120" align="left">Lab Dip No</th>
        <?
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			  echo "<th width='50'>Fab Qty</th><th width='50' >Rate</th><th width='50' >Amount</th>";
		}
		?>

       </tr>
       <?
	      /*$gmt_color_library=return_library_array( "select gmts_color_id,fabric_color_id
		  FROM
		  wo_booking_dtls
		  WHERE
		  job_no ='$job_no'
		  group by gmts_color_id", "fabric_color_id", "gmts_color_id");*/

		  $gmt_color_library=array();
		  $gmt_color_data=sql_select("select gmts_color_id,fabric_color_id
		  FROM
		  wo_booking_dtls
		  WHERE
		  booking_no =$txt_reqsn_no");
		  foreach( $gmt_color_data as $gmt_color_row)
		  {
			$gmt_color_library[$gmt_color_row[csf("fabric_color_id")]].=$color_library[$gmt_color_row[csf("gmts_color_id")]]."," ;
		  }

	        $grand_total_fin_fab_qnty=0;
			$grand_total_grey_fab_qnty=0;
			$grand_totalcons_per_finish=0;
			$grand_totalcons_per_grey=0;
			$color_wise_wo_sql=sql_select("select fabric_color_id
										  FROM
										  wo_booking_dtls
										  WHERE
										  booking_no =$txt_reqsn_no and
										  status_active=1 and
                                          is_deleted=0
										  group by fabric_color_id");
		foreach($color_wise_wo_sql as $color_wise_wo_result)
	    {
		?>
			<tr>
           <td  width="120" align="left">
			<?

			echo $color_library[$color_wise_wo_result[csf('fabric_color_id')]];

			?></td>
            <td  width="120" align="left">
			<?
			//echo $gmt_color_library[$color_wise_wo_result[csf('fabric_color_id')]];
			echo rtrim($gmt_color_library[$color_wise_wo_result['fabric_color_id']],",");
			?>
            </td>
            <td  width="120" align="left">
			<?
			$lapdip_no="";
			$lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='".$job_no."' and approval_status=3 and color_name_id=".$color_wise_wo_result[csf('fabric_color_id')]."");
			if($lapdip_no=="") echo "&nbsp;"; echo $lapdip_no;
			?>
            </td>
            <?
			$total_fin_fab_qnty=0;
			$total_grey_fab_qnty=0;
			$total_amount=0;
			foreach($nameArray_fabric_description as $result_fabric_description)
		    {



				if($db_type==2)
				{
				$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty, avg(b.rate) as rate
												  FROM
												  wo_pre_cost_fabric_cost_dtls a,
												  wo_booking_dtls b
												  WHERE
												  b.booking_no =$txt_reqsn_no  and
												  a.id=b.pre_cost_fabric_cost_dtls_id and
												   nvl(a.body_part_id,0)=nvl('".$result_fabric_description[csf('body_part_id')]."',0) and
												   nvl(a.color_type_id,0)=nvl('".$result_fabric_description[csf('color_type_id')]."',0) and
												   nvl(a.construction,0)=nvl('".$result_fabric_description[csf('construction')]."',0) and
												   nvl(a.composition,0)=nvl('".$result_fabric_description[csf('composition')]."',0) and
												   nvl(a.gsm_weight,0)=nvl('".$result_fabric_description[csf('gsm_weight')]."',0) and
												   nvl(b.dia_width,0)=nvl('".$result_fabric_description[csf('dia_width')]."',0) and
												   nvl(b.fabric_color_id,0)=nvl(".$color_wise_wo_result[csf('fabric_color_id')].",0) and
												  b.status_active=1 and
												  b.is_deleted=0 and b.fin_fab_qnty>0");
				}

				if($db_type==0)
				{
				$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty, avg(b.rate) as rate
												  FROM
												  wo_pre_cost_fabric_cost_dtls a,
												  wo_booking_dtls b
												  WHERE
												  b.booking_no =$txt_reqsn_no  and
												  a.id=b.pre_cost_fabric_cost_dtls_id and
												  a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
												  a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
												  a.construction='".$result_fabric_description[csf('construction')]."' and
												  a.composition='".$result_fabric_description[csf('composition')]."' and
												  a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
												  b.dia_width='".$result_fabric_description[csf('dia_width')]."' and
												  b.fabric_color_id=".$color_wise_wo_result[csf('fabric_color_id')]." and
												  b.status_active=1 and
												  b.is_deleted=0 and b.fin_fab_qnty>0");
				}


				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty

			?>
			<td width='50' align='right'>
			<?
			if($color_wise_wo_result_qnty[csf('grey_fab_qnty')]!="")
			{
			echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],2) ;
			$total_fin_fab_qnty+=$color_wise_wo_result_qnty[csf('grey_fab_qnty')];
			}
			?>
            </td>
            <td width='50' align='right' >
			<?
			if($color_wise_wo_result_qnty[csf('grey_fab_qnty')]!="")
			{
			echo number_format($color_wise_wo_result_qnty[csf('rate')],2);
			//$total_grey_fab_qnty+=$color_wise_wo_result_qnty[csf('grey_fab_qnty')];
			}
			?>
            </td>
            <td width='50' align='right' >
			<?
			$amount=$color_wise_wo_result_qnty[csf('grey_fab_qnty')]*$color_wise_wo_result_qnty[csf('rate')];
			if($amount!="")
			{
			echo number_format($amount,2);
			$total_amount+=$amount;
			}

			/*if($color_wise_wo_result_qnty[csf('grey_fab_qnty')]!="")
			{
			echo number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')]*$color_wise_wo_result_qnty[csf('rate')],2);
			}*/
			?>
            </td>
            <?
			}
			?>
            <td align="right"><? echo number_format($total_fin_fab_qnty,2); $grand_total_fin_fab_qnty+=$total_fin_fab_qnty;?></td>
            <td align="right"><? echo number_format($total_amount/$total_fin_fab_qnty,2); $grand_total_amount+=$total_amount;?></td>
            <td align="right"><? echo number_format($total_amount,2);?></td>
            </tr>
         <?
		}
		?>
        <tr>
        <td  width="120" align="left">&nbsp;</td>
        <td  width="120" align="left">&nbsp;</td>
        <td  width="120" align="left"><strong>Total</strong></td>
        <?
			foreach($nameArray_fabric_description as $result_fabric_description)
		    {
				if($db_type==2)
				{
				$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty, avg(b.rate) as rate
												  FROM
												  wo_pre_cost_fabric_cost_dtls a,
												  wo_booking_dtls b
												  WHERE
												  b.booking_no =$txt_reqsn_no  and
												  a.id=b.pre_cost_fabric_cost_dtls_id and
												  nvl(a.body_part_id,0)=nvl('".$result_fabric_description[csf('body_part_id')]."',0) and
												  nvl(a.color_type_id,0)=nvl('".$result_fabric_description[csf('color_type_id')]."',0) and
												  nvl(a.construction,0)=nvl('".$result_fabric_description[csf('construction')]."',0) and
												  nvl(a.composition,0)=nvl('".$result_fabric_description[csf('composition')]."',0) and
												  nvl(a.gsm_weight,0)=nvl('".$result_fabric_description[csf('gsm_weight')]."',0) and
												  nvl(b.dia_width,0)=nvl('".$result_fabric_description[csf('dia_width')]."',0) and
												  b.status_active=1 and
												  b.is_deleted=0 and b.fin_fab_qnty>0
												  ");
				}

				if($db_type==0)
				{
				$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty, avg(b.rate) as rate
												  FROM
												  wo_pre_cost_fabric_cost_dtls a,
												  wo_booking_dtls b
												  WHERE
												  b.booking_no =$txt_reqsn_no  and
												  a.id=b.pre_cost_fabric_cost_dtls_id and
												  a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
												  a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
												  a.construction='".$result_fabric_description[csf('construction')]."' and
												  a.composition='".$result_fabric_description[csf('composition')]."' and
												  a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
												  b.dia_width='".$result_fabric_description[csf('dia_width')]."' and
												  b.status_active=1 and
												  b.is_deleted=0 and b.fin_fab_qnty>0
												  ");
				}
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
			?>
			<td width='50' align='right'><?  echo number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')],2) ;?></td>
            <td width='50' align='right' > <? //echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],2);?></td>
             <td width='50' align='right' > <? //echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],2);?></td>
            <?
			}
			?>
            <td align="right"><? echo number_format($grand_total_fin_fab_qnty,2);?></td>
            <td align="right"><? echo number_format($grand_total_amount/$grand_total_fin_fab_qnty,2);?></td>
            <td align="right">
           <?
			echo number_format($grand_total_amount,2);
			?>
            </td>
            </tr>
    </table>

    <br/>
    <?
	  }
    ?>



       <?
		if(str_replace("'","",$cbo_fabric_source)==1 || str_replace("'","",$cbo_fabric_source)==3 )
		{
		$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');
		$yarn_sql_array=sql_select("SELECT min(id) as id,count_id, copm_one_id, percent_one,copm_two_id, percent_two, type_id, sum(cons_qnty) as yarn_required, AVG(rate) as rate from wo_pre_cost_fab_yarn_cost_dtls where job_no='$job_no' and  status_active=1 and is_deleted=0 group by count_id,copm_one_id,percent_one, copm_two_id,percent_two,type_id order by id");
		?>
        <table  width="100%"  border="0" cellpadding="0" cellspacing="0" >
            <tr>
                <td width="49%" valign="top">
                    <!--<table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                    <tr align="center">
                    <td colspan="7"><b>Yarn Required Summary (Pre Cost)</b></td>

                    </tr>
                    <tr align="center">
                    <td>Sl</td>
                    <td>Yarn Description</td>
                    <td>Brand</td>
                    <td>Lot</td>
                    <?
					//if($show_yarn_rate==1)
					//{
					?>
                    <td>Rate</td>
                    <?
					//}
					?>
                    <td>Cons for <? //echo $costing_per; ?> Gmts</td>
                    <td>Total (KG)</td>
                    </tr>
                    <?
					//$i=0;
					//$total_yarn=0;
					//foreach($yarn_sql_array  as $row)
                    //{

						//$i++;
					?>
                    <tr align="center">
                    <td><? //echo $i; ?></td>
                    <td>
					<?
					//$yarn_des=$yarn_count_arr[$row[csf('count_id')]]." ".$composition[$row[csf('copm_one_id')]]." ".$row[csf('percent_one')]."%  ";
					//if($row['copm_two_id'] !=0)
					//{
						//$yarn_des.=$composition[$row[csf('copm_two_id')]]." ".$row[csf('percent_two')]."%";
					//}
					//$yarn_des.=$yarn_type[$row[csf('type_id')]];
					//echo $yarn_count_arr[$row['count_id']]." ".$composition[$row['copm_one_id']]." ".$row['percent_one']."%  ".$composition[$row['copm_two_id']]." ".$row['percent_two']."%  ".$yarn_type[$row['type_id']];
					//echo $yarn_des;
					?>
                    </td>
                    <td></td>
                    <td></td>
                    <?
					//if($show_yarn_rate==1)
					//{
					?>
                     <td><? //echo number_format($row[csf('rate')],4); ?></td>
                     <?
					//}
					 ?>
                    <td><? //echo number_format($row[csf('yarn_required')],4); ?></td>

                    <td align="right"><? //echo number_format($row['yarn_required'],2); $total_yarn+=$row['yarn_required']; ?></td>
                    </tr>
                    <?
					//}
					?>
                    <tr align="center">
                    <td>Total</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <?
					//if($show_yarn_rate==1)
					//{
					?>
                    <td></td>
                    <?
                    //}
					?>
                    <td></td>
                    <td align="right"><? //echo number_format($total_yarn,2); ?></td>
                    </tr>
                    </table>-->
                </td>
                <td width="2%">
                </td>
                <td width="49%" valign="top" align="center">
                <?
				$yarn_sql_array=sql_select("SELECT min(a.id) as id, a.item_id, sum(a.qnty) as qnty ,min(b.supplier_id) as supplier_id,min(b.lot) as lot from inv_material_allocation_dtls a,product_details_master b where a.item_id=b.id and a.booking_no=$txt_reqsn_no and  a.status_active=1 and a.is_deleted=0 group by a.item_id order by a.id");
				if(count($yarn_sql_array)>0)
				{
				?>
                   <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                    <tr align="center">
                    <td colspan="7"><b>Allocated Yarn</b></td>

                    </tr>
                    <tr align="center">
                    <td>Sl</td>
                    <td>Yarn Description</td>
                    <td>Brand</td>
                    <td>Lot</td>


                    <td>Allocated Qty (Kg)</td>
                    </tr>
                    <?
					$total_allo=0;
					$item=return_library_array( "select id, product_name_details from   product_details_master",'id','product_name_details');
					$supplier=return_library_array( "select id, short_name from   lib_supplier",'id','short_name');
					//$yarn_sql_array=sql_select("SELECT a.item_id, a.qnty,b.supplier_id,b.lot from inv_material_allocation_dtls a,product_details_master b where a.item_id=b.id and a.booking_no=$txt_reqsn_no and  a.status_active=1 and a.is_deleted=0");
					$i=0;
					$total_yarn=0;
					foreach($yarn_sql_array  as $row)
                    {

						$i++;
					?>
                    <tr align="center">
                    <td><? echo $i; ?></td>
                    <td>
					<?

					echo $item[$row[csf('item_id')]];
					?>
                    </td>
                    <td>
                    <?

					echo $supplier[$row[csf('supplier_id')]];
					?>
                    </td>
                    <td>
					<?

					echo $row[csf('lot')];
					?>
                    </td>
                    <td align="right"><? echo number_format($row[csf('qnty')],4); $total_allo+= $row[csf('qnty')];?></td>
                    </tr>
                    <?
					}
					?>
                    <tr align="center">
                    <td>Total</td>
                    <td></td>


                    <td></td>
                    <td></td>
                    <td align="right"><? echo number_format($total_allo,4); ?></td>
                    </tr>
                    </table>
                    <?
				}
				else
				{
					$is_yarn_allocated=return_field_value("allocation", "variable_settings_inventory", "company_name=$cbo_company_name and variable_list=18 and item_category_id=1");
					if($is_yarn_allocated==1)
					{
					?>
					<font style=" font-size:30px"><b> Draft</b></font>
                    <?
					}
					else
					{
						echo "";
					}
				}
					?>
                </td>
            </tr>
        </table>
        <br/>
        <?
		}
		?>
        <?
		$sql_embelishment=sql_select("select emb_name,emb_type,cons_dzn_gmts,rate,amount from wo_pre_cost_embe_cost_dtls where job_no='$job_no' and status_active=1 and 	is_deleted=0");
		?>
        <table  width="100%"  border="0" cellpadding="0" cellspacing="0" >
            <tr>
                <td width="49%" valign="top">
                <?
				if(count($sql_embelishment)>0)
				{
				?>
                    <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                    <tr align="center">
                    <td colspan="7"><b>Embelishment (Pre Cost)</b></td>

                    </tr>
                    <tr align="center">
                    <td>Sl</td>
                    <td>Embelishment Name</td>
                    <td>Embelishment Type</td>
                    <td>Cons <? echo $costing_per; ?> Gmts</td>
                    <td>Rate</td>
                    <td>Amount</td>

                    </tr>
                    <?
					$sql_embelishment=sql_select("select emb_name,emb_type,cons_dzn_gmts,rate,amount from wo_pre_cost_embe_cost_dtls where job_no='$job_no' and status_active=1 and 	is_deleted=0");
					$i=0;
					//$total_yarn=0;
					foreach($sql_embelishment  as $row_embelishment)
                    {

						$i++;
					?>
                    <tr align="center">
                    <td><? echo $i; ?></td>
                    <td>
					<?
					echo $emblishment_name_array[$row_embelishment[csf('emb_name')]];
					?>
                    </td>
                    <td>
                    <?
					if($row_embelishment[csf('emb_name')]==1)
					{
					echo $emblishment_print_type[$row_embelishment[csf('emb_type')]];
					}
					if($row_embelishment[csf('emb_name')]==2)
					{
					echo $emblishment_embroy_type[$row_embelishment[csf('emb_type')]];
					}
					if($row_embelishment[csf('emb_name')]==3)
					{
					echo $emblishment_wash_type[$row_embelishment[csf('emb_type')]];
					}
					if($row_embelishment[csf('emb_name')]==4)
					{
					echo $emblishment_spwork_type[$row_embelishment[csf('emb_type')]];
					}
					if($row_embelishment[csf('emb_name')]==5)
					{
					echo $row_embelishment[csf('emb_type')];
					}
					?>

                    </td>
                    <td>
                    <?
					echo $row_embelishment[csf('cons_dzn_gmts')];
					?>
                    </td>
                    <td>
					<?
					echo $row_embelishment[csf('rate')];
					?>
                    </td>

                    <td>
					<?
					echo $row_embelishment[csf('amount')];
					?>
                    </td>


                    </tr>
                    <?
					}
					?>
                 <!--   <tr align="center">
                    <td>Total</td>
                    <td></td>
                    <td></td>
                    <td></td>

                    <td></td>

                    <td></td>

                    </tr>-->
                    </table>
                    <?
				}
					?>
                </td>
                <td width="2%">
                </td>
                <td width="49%" valign="top" align="center">
                <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                    <tr align="center">
                    <td><b>Approved Instructions</b></td>

                    </tr>
                    <tr>
                    <td>
                <?  echo $nameArray_approved_comments_row[csf('comments')];  ?>
                </td>
                </tr>
                </table>

                </td>
            </tr>
        </table>
        <br/>
        <!--New Start here-->
        <div style="width:1330px; float:left">

        <?
		// Body Part type used only Cuff and Flat Knit
        $colar_percent_size_wise_array=return_library_array( "select a.colar_cuff_per,b.gmts_sizes from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b,wo_pre_cost_fabric_cost_dtls c  where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.pre_cost_fabric_cost_dtls_id=c.id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.color_size_table_id and a.booking_no=$txt_reqsn_no and a.booking_type=1 and a.body_part_type in(40,50) and a.status_active=1 and a.is_deleted=0", "gmts_sizes", "colar_cuff_per");

	//	echo  "select a.body_part_type,a.body_part_id,sum(d.rmg_qty) as rmg_qty,d.gmts_size,d.gmts_color_id FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls d WHERE a.job_no=d.job_no and d.booking_no =$txt_reqsn_no and  a.id=d.pre_cost_fabric_cost_dtls_id  and d.status_active=1 and d.is_deleted=0 and d.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and a.body_part_type in(40,50) group by a.body_part_type,a.body_part_id,d.gmts_size,d.gmts_color_id order by a.body_part_id";

		$nameArray_body_part=sql_select( "select a.body_part_type,a.body_part_id,sum(d.rmg_qty) as rmg_qty,d.gmts_size,d.gmts_color_id FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls d WHERE a.job_no=d.job_no and d.booking_no =$txt_reqsn_no and  a.id=d.pre_cost_fabric_cost_dtls_id  and d.status_active=1 and d.is_deleted=0 and d.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and a.body_part_type in(40,50) group by a.body_part_type,a.body_part_id,d.gmts_size,d.gmts_color_id order by a.body_part_id");
		$row_count=count($nameArray_body_part);
		//if($row_count==0) echo " <p style='color:#f00; text-align:center; font-size:15px;'> Body part type is  used only Flat Knit and Cuff.</p> ";
		foreach($nameArray_body_part as $row)
		{
			$body_part_arr[$row[csf('body_part_id')]]['bpart_type']=$row[csf('body_part_type')];
			$body_part_rmg_qty_arr[$row[csf('body_part_id')]][$row[csf('gmts_size')]][$row[csf('gmts_color_id')]]['rmg_qty']+=$row[csf('rmg_qty')];
		}
		//print_r($body_part_arr);
		$tbl_row_count=count($body_part_arr);
		//echo $tbl_row_count.'Dx';
		?>


        <?
		$k=1;
		foreach($body_part_arr as $body_id=>$val)
		{
			$k++;

			$bpart_type_id=$val['bpart_type'];
		$nameArray_item_size=sql_select( "select min(c.id) as id,b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_reqsn_no  and a.body_part_id=$body_id  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id  and d.status_active=1 and d.is_deleted=0 and a.body_part_type in(40,50) group by b.item_size,c.size_number_id order by id");
		?>

        <div style="max-height:1330px; width:660px; overflow:auto; float:left; padding-top:20px; margin-left:5px; margin-bottom:5px; position: relative;">
        <table  width="100%" align="left"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
        <tr>
        <td colspan="<? echo count($nameArray_size)+4;?>" align="center"><b><? echo $body_part[$body_id];?> -  Colour Size Breakedown in Pcs</b></td>
        </tr>
        <tr>
        <td width="70">Size</td>

        <?

		/*$nameArray_item_size=sql_select( "select b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_reqsn_no  and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 group by c.size_number_id,b.item_size order by c.size_number_id");*/
		foreach($nameArray_item_size  as $result_size)
		{
		?>
		<td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
		<?
        }
        ?>
        <td rowspan="2" align="center"><strong>Total</strong></td>

        </tr>
        <tr>
        <td>Collar Size</td>

        <?
        foreach($nameArray_item_size  as $result_item_size)
		{
		?>
		<td align="center" style="border:1px solid black"><strong><? echo $result_item_size[csf('item_size')];?></strong></td>
		<?
        }
        ?>
         <?

			$color_wise_wo_sql=sql_select("select a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method,c.color_number_id,sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_reqsn_no and a.body_part_id=$body_id  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 and a.body_part_type in(40,50) group by c.color_number_id,a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method order by a.id
");
		foreach($color_wise_wo_sql as $color_wise_wo_result)
	    {
			$color_total_collar=0;
			$color_total_collar_order_qnty=0;
			$process_loss_method=$color_wise_wo_result[csf("process_loss_method")];
			$constrast_color_arr=array();
			if($color_wise_wo_result[csf("color_size_sensitive")]==3)
			{
				$constrast_color=explode('__',$color_wise_wo_result[csf("color_break_down")]);
				for($i=0;$i<count($constrast_color);$i++)
				{
					$constrast_color2=explode('_',$constrast_color[$i]);
					$constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
				}
			}
		?>
			<tr>
            <td>
            <?
			if($color_wise_wo_result[csf("color_size_sensitive")]==3)
			{
				echo strtoupper ($constrast_color_arr[$color_wise_wo_result[csf('color_number_id')]]) ;
				$lab_dip_color_id=return_field_value("contrast_color_id","wo_pre_cos_fab_co_color_dtls","job_no='".$color_wise_wo_result[csf('job_no')]."' and pre_cost_fabric_cost_dtls_id=".$color_wise_wo_result[csf('id')]." and gmts_color_id=".$color_wise_wo_result[csf('color_number_id')]."");
			}
			else
			{
				echo $color_library[$color_wise_wo_result[csf('color_number_id')]];
				$lab_dip_color_id=$color_wise_wo_result[csf('color_number_id')];
			}
			?>

            </td>
            <?
            foreach($nameArray_item_size  as $result_size)
			{
				?>
				<td align="center" style="border:1px solid black">

				<?
				$rmg_qty=$body_part_rmg_qty_arr[$body_id][$result_size[csf('size_number_id')]][$color_wise_wo_result[csf('color_number_id')]]['rmg_qty'];
				//echo $bpart_type_id.'=';
				if($bpart_type_id==50)//Cuff
				{
					$fab_rmg_qty=$rmg_qty*2;
				}
				else //Flat Knit
				{
					$fab_rmg_qty=$rmg_qty;
				}
				//$color_wise_wo_sql_qnty=sql_select("select c.color_number_id,sum(c.order_quantity) as order_quantity, sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_reqsn_no and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 and c.color_number_id='".$color_wise_wo_result['color_number_id']."' and c.size_number_id='".$result_size['size_number_id']."'");
				/*$color_wise_wo_sql_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$color_wise_wo_result[csf('color_number_id')]."   and  status_active=1 and is_deleted =0");

				list($plan_cut_qnty)=$color_wise_wo_sql_qnty;*/
				//$plan_cut=$plan_cut_qnty[csf('plan_cut_qnty')];
				//$colar_excess_per=($plan_cut*$colar_excess_percent)/100;
				echo number_format($fab_rmg_qty,0);
				$color_total_collar+=$fab_rmg_qty;
				$color_total_collar_order_qnty+=$fab_rmg_qty;
				$grand_total_collar+=$fab_rmg_qty;
				$grand_total_collar_order_qnty+=$fab_rmg_qty;

				$size_tatal[$result_size[csf('size_number_id')]]+=$fab_rmg_qty;
				?>
                </td>
				<?
			}
			?>

            <td align="center"><? echo number_format($color_total_collar,0); ?></td>

            </tr>
            <?
		    }
			?>
            <tr>
                <td>Size Total</td>

                <?
                foreach($nameArray_item_size  as $result_size)
                {
					//$colar_excess_pers=($size_tatal[$result_size[csf('size_number_id')]]*$colar_excess_percent)/100;
					$tot_size_tatal=$size_tatal[$result_size[csf('size_number_id')]];
					//$size_tatal[$result_size[csf('size_number_id')]]=0;
                ?>
                <td style="border:1px solid black;  text-align:center"><?  echo number_format($size_tatal[$result_size[csf('size_number_id')]],0);$size_tatal[$result_size[csf('size_number_id')]]=0; ?></td>
                <?
                }
                ?>
                <td  style="border:1px solid black;  text-align:center"><?  echo number_format($grand_total_collar,0);$grand_total_collar=0; ?></td>

            </tr>
        </table>
          <br/>
        </div>


        <?
		}

		?>


          <!--End here-->
       </div>
       <?
     // die;
	   ?>
        <br>
        <table style="display:none"  ‍  width="100%"  border="0" cellpadding="0" cellspacing="0" >
        <tr>
        <?
		//echo "select min(c.id) as id,b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_reqsn_no  and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id  and d.status_active=1 and d.is_deleted=0 group by b.item_size,c.size_number_id order by id";
		$colar_percent_size_wise_array=return_library_array( "select a.colar_cuff_per,b.gmts_sizes from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b,wo_pre_cost_fabric_cost_dtls c  where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.pre_cost_fabric_cost_dtls_id=c.id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.color_size_table_id    and a.booking_no=$txt_reqsn_no and a.booking_type=1 and c.body_part_id in(2) and a.status_active=1 and a.is_deleted=0", "gmts_sizes", "colar_cuff_per");
		//print_r($colar_percent_size_wise_array);
		$nameArray_item_size=sql_select( "select min(c.id) as id,b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_reqsn_no  and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id  and d.status_active=1 and d.is_deleted=0 group by b.item_size,c.size_number_id order by id");
		if(count($nameArray_item_size)>0)
		{
        ?>
        <td width="49%">
        <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
        <tr>
        <td colspan="<? echo count($nameArray_size)+4;?>" align="center"><b>Collar -  Colour Size Brakedown in Pcs</b></td>
        </tr>
        <tr>
        <td width="70">Size</td>

        <?

		/*$nameArray_item_size=sql_select( "select b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_reqsn_no  and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 group by c.size_number_id,b.item_size order by c.size_number_id");*/
		foreach($nameArray_item_size  as $result_size)
		{
		?>
		<td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
		<?
        }
        ?>
        <td rowspan="2" align="center"><strong>Total</strong></td>
        <td width="60" rowspan="2" align="center"><strong>Extra %</strong></td>
        </tr>
        <tr>
        <td>Collar Size</td>

        <?
        foreach($nameArray_item_size  as $result_item_size)
		{
		?>
		<td align="center" style="border:1px solid black"><strong><? echo $result_item_size[csf('item_size')];?></strong></td>
		<?
        }
        ?>
         <?

			$color_wise_wo_sql=sql_select("select a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method,c.color_number_id,sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_reqsn_no and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 group by c.color_number_id,a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method order by a.id
");
		foreach($color_wise_wo_sql as $color_wise_wo_result)
	    {
			$color_total_collar=0;
			$color_total_collar_order_qnty=0;
			$process_loss_method=$color_wise_wo_result[csf("process_loss_method")];
			$constrast_color_arr=array();
			if($color_wise_wo_result[csf("color_size_sensitive")]==3)
			{
				$constrast_color=explode('__',$color_wise_wo_result[csf("color_break_down")]);
				for($i=0;$i<count($constrast_color);$i++)
				{
					$constrast_color2=explode('_',$constrast_color[$i]);
					$constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
				}
			}
		?>
			<tr>
            <td>
            <?
			if($color_wise_wo_result[csf("color_size_sensitive")]==3)
			{
				echo strtoupper ($constrast_color_arr[$color_wise_wo_result[csf('color_number_id')]]) ;
				$lab_dip_color_id=return_field_value("contrast_color_id","wo_pre_cos_fab_co_color_dtls","job_no='".$color_wise_wo_result[csf('job_no')]."' and pre_cost_fabric_cost_dtls_id=".$color_wise_wo_result[csf('id')]." and gmts_color_id=".$color_wise_wo_result[csf('color_number_id')]."");
			}
			else
			{
				echo $color_library[$color_wise_wo_result[csf('color_number_id')]];
				$lab_dip_color_id=$color_wise_wo_result[csf('color_number_id')];
			}
			?>

            </td>
            <?
            foreach($nameArray_item_size  as $result_size)
			{
				?>
				<td align="center" style="border:1px solid black">

				<?
				//$color_wise_wo_sql_qnty=sql_select("select c.color_number_id,sum(c.order_quantity) as order_quantity, sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_reqsn_no and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 and c.color_number_id='".$color_wise_wo_result['color_number_id']."' and c.size_number_id='".$result_size['size_number_id']."'");
				$color_wise_wo_sql_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$color_wise_wo_result[csf('color_number_id')]."   and  status_active=1 and is_deleted =0");

				list($plan_cut_qnty)=$color_wise_wo_sql_qnty;
				$plan_cut=$plan_cut_qnty[csf('plan_cut_qnty')];
				$colar_excess_per=($plan_cut*$colar_excess_percent)/100;
				echo number_format($plan_cut+$colar_excess_per,0);
				$color_total_collar+=$plan_cut+$colar_excess_per;
				$color_total_collar_order_qnty+=$plan_cut;
				$grand_total_collar+=$plan_cut+$colar_excess_per;
				$grand_total_collar_order_qnty+=$plan_cut;

				$size_tatal[$result_size[csf('size_number_id')]]+=$plan_cut+$colar_excess_per;
				?>
                </td>
				<?
			}
			?>

            <td align="center"><? echo number_format($color_total_collar,0); ?></td>
            <td align="center"><? echo number_format((($color_total_collar-$color_total_collar_order_qnty)/$color_total_collar_order_qnty)*100,2); ?></td>
            </tr>
            <?
		    }
			?>
            <tr>
                <td>Size Total</td>

                <?
                foreach($nameArray_item_size  as $result_size)
                {
                ?>
                <td style="border:1px solid black;  text-align:center"><? $colar_excess_pers=($size_tatal[$result_size[csf('size_number_id')]]*$colar_excess_percent)/100; echo number_format($size_tatal[$result_size[csf('size_number_id')]]+$colar_excess_pers,0); ?></td>
                <?
                }
                ?>
                <td  style="border:1px solid black;  text-align:center"><?  echo number_format($grand_total_collar,0); ?></td>
                <td align="center" style="border:1px solid black"> <? echo number_format((($grand_total_collar-$grand_total_collar_order_qnty)/$grand_total_collar_order_qnty)*100,2); ?></td>
            </tr>
        </table>
        </td>
        <td width="2%">
        </td>
        <?
        }
		?>

        <?
		$cuff_percent_size_wise_array=return_library_array( "select a.colar_cuff_per,b.gmts_sizes from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b,wo_pre_cost_fabric_cost_dtls c  where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.pre_cost_fabric_cost_dtls_id=c.id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.color_size_table_id    and a.booking_no=$txt_reqsn_no and a.booking_type=1 and c.body_part_id in(3) and a.status_active=1 and a.is_deleted=0", "gmts_sizes", "colar_cuff_per");
		//print_r($cuff_percent_size_wise_array);
		$nameArray_item_size=sql_select( "select min(c.id) as id, b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_reqsn_no  and a.body_part_id=3  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 group by b.item_size,c.size_number_id order by id");

		if(count($nameArray_item_size)>0)
		{
        ?>
        <td width="49%">
        <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
        <tr>
        <td colspan="<? echo count($nameArray_size)+4;?>" align="center"><b>Cuff -  Colour Size Brakedown in Pcs</b></td>
        </tr>
        <tr>
        <td width="70">Size</td>

        <?
		foreach($nameArray_item_size  as $result_size)
		{
		?>
		<td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
		<?
        }
        ?>
        <td rowspan="2" align="center"><strong>Total</strong></td>
        <td width="60" rowspan="2" align="center"><strong>Extra %</strong></td>
        </tr>
        <tr>
        <td>Cuff Size</td>

        <?
        foreach($nameArray_item_size  as $result_item_size)
		{
		?>
		<td align="center" style="border:1px solid black"><strong><? echo $result_item_size[csf('item_size')];?></strong></td>
		<?
        }
        ?>
         <?

			$color_wise_wo_sql=sql_select("select a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method,c.color_number_id,sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_reqsn_no and a.body_part_id=3  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id and  d.status_active=1 and d.is_deleted=0 group by c.color_number_id ,a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method order by a.id
");
		foreach($color_wise_wo_sql as $color_wise_wo_result)
	    {
			$color_total_cuff=0;
			$color_total_cuff_order_qnty=0;
			$process_loss_method=$color_wise_wo_result[csf("process_loss_method")];
			$constrast_color_arr=array();
			if($color_wise_wo_result[csf("color_size_sensitive")]==3)
			{
				$constrast_color=explode('__',$color_wise_wo_result[csf("color_break_down")]);
				for($i=0;$i<count($constrast_color);$i++)
				{
					$constrast_color2=explode('_',$constrast_color[$i]);
					$constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
				}
			}
			//print_r($constrast_color_arr);
		?>
			<tr>
            <td>
            <?
			if($color_wise_wo_result[csf("color_size_sensitive")]==3)
			{
				///echo $color_wise_wo_result[csf('color_number_id')];
				echo strtoupper ($constrast_color_arr[$color_wise_wo_result[csf('color_number_id')]]);
				$lab_dip_color_id=return_field_value("contrast_color_id","wo_pre_cos_fab_co_color_dtls","job_no='".$color_wise_wo_result[csf('job_no')]."' and pre_cost_fabric_cost_dtls_id=".$color_wise_wo_result[csf('id')]." and gmts_color_id=".$color_wise_wo_result[csf('color_number_id')]."");
			}
			else
			{
				echo $color_library[$color_wise_wo_result[csf('color_number_id')]];
				$lab_dip_color_id=$color_wise_wo_result[csf('color_number_id')];
			}
			?>

            </td>
            <?
            foreach($nameArray_item_size  as $result_size)
			{
				?>
				<td align="center" style="border:1px solid black">

				<?
				/*$color_wise_wo_sql_qnty=sql_select("select c.color_number_id,sum(c.order_quantity) as order_quantity, sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_reqsn_no and a.body_part_id=3 and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 and c.color_number_id='".$color_wise_wo_result['color_number_id']."' and c.size_number_id='".$result_size['size_number_id']."'");*/
				$color_wise_wo_sql_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$color_wise_wo_result[csf('color_number_id')]."   and  status_active=1 and is_deleted =0");

				list($plan_cut_qnty)=$color_wise_wo_sql_qnty;
				$plan_cut=$plan_cut_qnty[csf('plan_cut_qnty')];
				$cuff_excess_per=(($plan_cut*2)*$cuff_excess_percent)/100;
				echo number_format($plan_cut*2+$cuff_excess_per,0);
				$color_total_cuff+=$plan_cut*2+$cuff_excess_per;
				$color_total_cuff_order_qnty+=$plan_cut*2;
				$grand_total_cuff+=$plan_cut*2+$cuff_excess_per;
				$grand_total_cuff_order_qnty+=$plan_cut*2;
				$size_tatal[$result_size[csf('size_number_id')]]+=$plan_cut*2+$cuff_excess_per;
				/*$cuff_excess_per=(($plan_cut_qnty[csf('plan_cut_qnty')]*2)*$cuff_excess_percent)/100;
				echo number_format($plan_cut_qnty[csf('plan_cut_qnty')]*2+$cuff_excess_per,0);
				$color_total_cuff+=$plan_cut_qnty[csf('plan_cut_qnty')]*2+$cuff_excess_per;
				$color_total_cuff_order_qnty+=$plan_cut_qnty[csf('order_quantity')]*2;
				$grand_total_cuff+=$plan_cut_qnty[csf('plan_cut_qnty')]*2+$cuff_excess_per;
				$grand_total_cuff_order_qnty+=$plan_cut_qnty[csf('order_quantity')]*2;*/

				?>

                </td>
				<?
			}
			?>

            <td align="center"><? echo number_format($color_total_cuff,0); ?></td>
            <td align="center"><? echo number_format((($color_total_cuff-$color_total_cuff_order_qnty)/$color_total_cuff_order_qnty)*100,2); ?></td>
            </tr>
            <?
		    }
			?>
            <tr>
                <td>Size Total</td>

                <?
                foreach($nameArray_item_size  as $result_size)
                {
                   /* $nameArray_size_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =$result_size[size_number_id]  and status_active=1 and is_deleted =0 order by id");
                foreach($nameArray_size_qnty as $result_size_qnty)
                {*/
                ?>
                <td style="border:1px solid black;  text-align:center"><? $cuff_excess_pers=(($size_tatal[$result_size[csf('size_number_id')]]*2)*$cuff_excess_percent)/100; echo number_format($size_tatal[$result_size[csf('size_number_id')]]*2+$cuff_excess_pers,0); ?></td>
                <?
                //}
                }
                ?>
                <td  style="border:1px solid black;  text-align:center"><?  echo number_format($grand_total_cuff,0); ?></td>
                <td align="center" style="border:1px solid black"> <? echo number_format((($grand_total_cuff-$grand_total_cuff_order_qnty)/$grand_total_cuff_order_qnty)*100,2); ?></td>
            </tr>
        </table>
        </td>
        <?
				}
		?>
        </tr>
        </table>
        <br/>
        <table  width="100%"  border="0" cellpadding="0" cellspacing="0" >
        <tr>
        <?

		//echo "select a.colar_cuff_per,b.gmts_sizes from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b,wo_pre_cost_fabric_cost_dtls c  where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.pre_cost_fabric_cost_dtls_id=c.id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.color_size_table_id    and a.booking_no=$txt_reqsn_no and a.booking_type=1 c.body_part_id in(2) and a.status_active=1 and a.is_deleted=0";
		//$colar_percent_size_wise_array=return_library_array( "select a.colar_cuff_per,b.gmts_sizes from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b,wo_pre_cost_fabric_cost_dtls c  where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.pre_cost_fabric_cost_dtls_id=c.id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.color_size_table_id    and a.booking_no=$txt_reqsn_no and a.booking_type=1 and c.body_part_id in(2) and a.status_active=1 and a.is_deleted=0", "gmts_sizes", "colar_cuff_per");


		$colar_percent_size_wise_array=array();
		$colar_tipping_percent_size_wise_sql=sql_select( "select a.colar_cuff_per,b.color_number_id,b.gmts_sizes from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b,wo_pre_cost_fabric_cost_dtls c  where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.pre_cost_fabric_cost_dtls_id=c.id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.color_size_table_id    and a.booking_no=$txt_reqsn_no and a.booking_type=1 and c.body_part_id in(172) and a.status_active=1 and a.is_deleted=0");

		$colar_tipping_excess_percent_arr=array();
		foreach($colar_tipping_percent_size_wise_sql as $colar_percent_size_wise_row)
		{
			$colar_tipping_excess_percent_arr[$colar_percent_size_wise_row[csf('color_number_id')]][$colar_percent_size_wise_row[csf('gmts_sizes')]]=$colar_percent_size_wise_row[csf('colar_cuff_per')];
			//$colar_excess_percent_arr[$colar_percent_size_wise_row[csf('gmts_sizes')]]+=$colar_percent_size_wise_row[csf('colar_cuff_per')];

		}

		//print_r($colar_tipping_excess_percent_arr);
		$nameArray_item_size=sql_select( "select min(c.id) as id,b.item_size,c.size_number_id,	min(c.size_order) as size_order FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_reqsn_no  and a.body_part_id=172  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id  and d.status_active=1 and d.is_deleted=0 group by b.item_size,c.size_number_id order by size_order");


		if(count($nameArray_item_size)>0)
		{
        ?>
        <td width="49%">
        <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
        <tr>
        <td colspan="<? echo count($nameArray_size)+4;?>" align="center"><b>Collar Tipping -  Colour Size Brakedown in Pcs</b></td>
        </tr>
        <tr>
        <td width="70">Size</td>

        <?

		/*$nameArray_item_size=sql_select( "select b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_reqsn_no  and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 group by c.size_number_id,b.item_size order by c.size_number_id");*/
		foreach($nameArray_item_size  as $result_size)
		{
		?>
		<td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
		<?
        }
        ?>
        <td rowspan="2" align="center"><strong>Total</strong></td>
        <td width="60" rowspan="2" align="center"><strong>Extra %</strong></td>
        </tr>
        <tr>
        <td>Collar Size</td>

        <?
        foreach($nameArray_item_size  as $result_item_size)
		{
		?>
		<td align="center" style="border:1px solid black"><strong><? echo $result_item_size[csf('item_size')];?></strong></td>
		<?
        }

		$color_wise_wo_sql=sql_select("select a.id, a.job_no, a.color_size_sensitive, a.color_break_down, a.process_loss_method, c.color_number_id , c.color_order, sum(c.plan_cut_qnty) as plan_cut_qnty
		FROM wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c, wo_booking_dtls d
		WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_reqsn_no and a.body_part_id=172 and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0
		group by c.color_number_id, c.color_order, a.id, a.job_no, a.color_size_sensitive,a.color_break_down, a.process_loss_method
		order by c.color_order ");

		foreach($color_wise_wo_sql as $color_wise_wo_result)
	    {
			$color_total_collar=0;
			$color_total_collar_order_qnty=0;
			$process_loss_method=$color_wise_wo_result[csf("process_loss_method")];
			$constrast_color_arr=array();
			if($color_wise_wo_result[csf("color_size_sensitive")]==3)
			{
				$constrast_color=explode('__',$color_wise_wo_result[csf("color_break_down")]);
				for($i=0;$i<count($constrast_color);$i++)
				{
					$constrast_color2=explode('_',$constrast_color[$i]);
					$constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
				}
			}
		?>
			<tr>
            <td>
            <?
			if($color_wise_wo_result[csf("color_size_sensitive")]==3)
			{
				echo strtoupper ($constrast_color_arr[$color_wise_wo_result[csf('color_number_id')]]) ;
				$lab_dip_color_id=return_field_value("contrast_color_id","wo_pre_cos_fab_co_color_dtls","job_no='".$color_wise_wo_result[csf('job_no')]."' and pre_cost_fabric_cost_dtls_id=".$color_wise_wo_result[csf('id')]." and gmts_color_id=".$color_wise_wo_result[csf('color_number_id')]."");
			}
			else
			{
				echo $color_library[$color_wise_wo_result[csf('color_number_id')]];
				$lab_dip_color_id=$color_wise_wo_result[csf('color_number_id')];
			}
			?>

            </td>
            <?
            foreach($nameArray_item_size  as $result_size)
			{
				?>
				<td align="center" style="border:1px solid black">
				<?

				$color_wise_wo_sql_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$color_wise_wo_result[csf('color_number_id')]."   and  status_active=1 and is_deleted =0 ");




				list($plan_cut_qnty)=$color_wise_wo_sql_qnty;
				$plan_cut=$plan_cut_qnty[csf('plan_cut_qnty')];
				$colar_tipping_excess_per=($plan_cut*$colar_tipping_excess_percent_arr[$color_wise_wo_result[csf('color_number_id')]][$result_size[csf('size_number_id')]])/100;
				echo number_format($plan_cut+$colar_tipping_excess_per,0);
				$collar_tiff_size_total_arr[$result_size[csf('size_number_id')]]+=number_format($plan_cut+$colar_tipping_excess_per,0,'','');
				$color_tipping_total_collar+=$plan_cut+$colar_tipping_excess_per;
				$color_tipping_total_collar_order_qnty+=$plan_cut;
				$grand_total_collar_tipping+=$plan_cut+$colar_tipping_excess_per;
				$grand_total_collar_tipping_order_qnty+=$plan_cut;



				?>
                </td>
				<?
			}
			?>

            <td align="center"><? echo number_format($color_tipping_total_collar,0); ?></td>
            <td align="center"><? echo number_format((($color_tipping_total_collar-$color_tipping_total_collar_order_qnty)/$color_tipping_total_collar_order_qnty)*100,2); ?></td>
            </tr>
            <?
		    }
			?>
            <tr>
                <td>Size Total</td>

                <?
                foreach($nameArray_item_size  as $result_size)
                {
                ?>
                <td style="border:1px solid black;  text-align:center">
				<?
				//$colar_excess_pers=($size_tatal[$result_size[csf('size_number_id')]]*$colar_excess_percent)/100; echo number_format($size_tatal[$result_size[csf('size_number_id')]]+$colar_excess_pers,0);
				echo number_format($collar_tiff_size_total_arr[$result_size[csf('size_number_id')]],0);

				?></td>
                <?
                }
                ?>
                <td  style="border:1px solid black;  text-align:center"><?  echo number_format($grand_total_collar_tipping,0); ?></td>
                <td align="center" style="border:1px solid black"> <? echo number_format((($grand_total_collar_tipping-$grand_total_collar_tipping_order_qnty)/$grand_total_collar_tipping_order_qnty)*100,2); ?></td>
            </tr>
        </table>
        </td>
        <td width="2%">
        </td>
        <?
        }

		$cuff_tipping_percent_size_wise_array=$cuff_size_total=array();
		$cuff_tipping_percent_size_wise_sql=sql_select( "select a.colar_cuff_per,b.color_number_id,b.gmts_sizes from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b,wo_pre_cost_fabric_cost_dtls c  where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.pre_cost_fabric_cost_dtls_id=c.id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.color_size_table_id  and a.booking_no=$txt_reqsn_no and a.booking_type=1 and c.body_part_id in(214) and a.status_active=1 and a.is_deleted=0");
		foreach($cuff_tipping_percent_size_wise_sql as $cuff_percent_size_wise_row)
		{
			$cuff_tipping_percent_size_wise_array[$cuff_percent_size_wise_row[csf('color_number_id')]][$cuff_percent_size_wise_row[csf('gmts_sizes')]]=$cuff_percent_size_wise_row[csf('colar_cuff_per')];
		}


		$nameArray_item_size=sql_select( "select min(c.id) as id, b.item_size,c.size_number_id,	min(c.size_order) as size_order FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_reqsn_no  and a.body_part_id=214  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 group by b.item_size,c.size_number_id order by size_order");


		if(count($nameArray_item_size)>0)
		{
        ?>
        <td width="49%">
        <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
        <tr>
        <td colspan="<? echo count($nameArray_size)+4;?>" align="center"><b>Cuff Tipping-  Colour Size Brakedown in Pcs</b></td>
        </tr>
        <tr>
        <td width="70">Size</td>

        <?
		foreach($nameArray_item_size  as $result_size)
		{
		?>
		<td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
		<?
        }
        ?>
        <td rowspan="2" align="center"><strong>Total</strong></td>
        <td width="60" rowspan="2" align="center"><strong>Extra %</strong></td>
        </tr>
        <tr>
        <td>Cuff Size</td>

        <?
        foreach($nameArray_item_size  as $result_item_size)
		{
		?>
		<td align="center" style="border:1px solid black"><strong><? echo $result_item_size[csf('item_size')];?></strong></td>
		<?
        }
        ?>
         <?

			$color_wise_wo_sql=sql_select("select a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method,c.color_number_id, c.color_order, sum(c.plan_cut_qnty) as plan_cut_qnty
			FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d
			WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_reqsn_no and a.body_part_id=214  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id and  d.status_active=1 and d.is_deleted=0
			group by c.color_number_id, c.color_order ,a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method
			order by c.color_order");
		foreach($color_wise_wo_sql as $color_wise_wo_result)
	    {
			$color_total_cuff=0;
			$color_total_cuff_order_qnty=0;
			$process_loss_method=$color_wise_wo_result[csf("process_loss_method")];
			$constrast_color_arr=array();
			if($color_wise_wo_result[csf("color_size_sensitive")]==3)
			{
				$constrast_color=explode('__',$color_wise_wo_result[csf("color_break_down")]);
				for($i=0;$i<count($constrast_color);$i++)
				{
					$constrast_color2=explode('_',$constrast_color[$i]);
					$constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
				}
			}
		?>
			<tr>
            <td>
            <?
			if($color_wise_wo_result[csf("color_size_sensitive")]==3)
			{
				echo strtoupper ($constrast_color_arr[$color_wise_wo_result[csf('color_number_id')]]);
				$lab_dip_color_id=return_field_value("contrast_color_id","wo_pre_cos_fab_co_color_dtls","job_no='".$color_wise_wo_result[csf('job_no')]."' and pre_cost_fabric_cost_dtls_id=".$color_wise_wo_result[csf('id')]." and gmts_color_id=".$color_wise_wo_result[csf('color_number_id')]."");
			}
			else
			{
				echo $color_library[$color_wise_wo_result[csf('color_number_id')]];
				$lab_dip_color_id=$color_wise_wo_result[csf('color_number_id')];
			}
			?>

            </td>
            <?
            foreach($nameArray_item_size  as $result_size)
			{
				?>
				<td align="center" style="border:1px solid black">

				<?


				$color_wise_wo_sql_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$color_wise_wo_result[csf('color_number_id')]."   and  status_active=1 and is_deleted =0");



				list($plan_cut_qnty)=$color_wise_wo_sql_qnty;
				$plan_cut=$plan_cut_qnty[csf('plan_cut_qnty')];
				$cuff_tipping_excess_per=(($plan_cut*2)*$cuff_tipping_percent_size_wise_array[$color_wise_wo_result[csf('color_number_id')]][$result_size[csf('size_number_id')]])/100;
				echo number_format($plan_cut*2+$cuff_tipping_excess_per,0);
				$cuff_tiffing_size_total[$result_size[csf('size_number_id')]]+=number_format($plan_cut*2+$cuff_tipping_excess_per,0,'','');
				//echo $cuff_tipping_percent_size_wise_array[$color_wise_wo_result[csf('color_number_id')]][$result_size[csf('size_number_id')]];
				$color_total_cuff_tiffing+=$plan_cut*2+$cuff_tipping_excess_per;
				$color_total_cuff_tiffing_order_qnty+=$plan_cut*2;
				$grand_total_cuff_tiffing+=$plan_cut*2+$cuff_tipping_excess_per;
				$grand_total_cuff_tiffing_order_qnty+=$plan_cut*2;
				?>

                </td>
				<?
			}
			?>

            <td align="center"><? echo number_format($color_total_cuff_tiffing,0); ?></td>
            <td align="center"><? echo number_format((($color_total_cuff_tiffing-$color_total_cuff_tiffing_order_qnty)/$color_total_cuff_tiffing_order_qnty)*100,2);  ?></td>
            </tr>
            <?
		    }
			?>
            <tr>
                <td>Size Total</td>

                <?
                foreach($nameArray_item_size  as $result_size)
                {
                   /* $nameArray_size_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =$result_size[size_number_id]  and status_active=1 and is_deleted =0 order by id");
                foreach($nameArray_size_qnty as $result_size_qnty)
                {*/
                ?>
                <td style="border:1px solid black;  text-align:center">
				<?
				//$cuff_excess_pers=(($size_tatal[$result_size[csf('size_number_id')]]*2)*$cuff_excess_percent)/100; echo number_format($size_tatal[$result_size[csf('size_number_id')]]*2+$cuff_excess_pers,0);
				echo number_format($cuff_tiffing_size_total[$result_size[csf('size_number_id')]],0);
				?></td>
                <?
                //}
                }
                ?>
                <td  style="border:1px solid black;  text-align:center"><?  echo number_format($grand_total_cuff_tiffing,0); ?></td>
                <td align="center" style="border:1px solid black"> <? echo number_format((($grand_total_cuff_tiffing-$grand_total_cuff_tiffing_order_qnty)/$grand_total_cuff_tiffing_order_qnty)*100,2); ?></td>
            </tr>
        </table>
        </td>
        <?
				}
		?>
        </tr>
        </table>

        <table  width="100%"  border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td width="49%" style="border:solid; border-color:#000; border-width:thin" valign="top">
                    <?php echo get_spacial_instruction($txt_reqsn_no); ?>
                </td>
                <td width="2%">
                </td>
                <td width="49%" valign="top">
                <?
		if(str_replace("'","",$cbo_fabric_source)==1 || str_replace("'","",$cbo_fabric_source)==2 || str_replace("'","",$cbo_fabric_source)==3 )
	    {
		?>
                   <table width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                   <tr align="center">
                    <td colspan="10"><b>Comments</b></td>

                    </tr>
                    <tr align="center">
                    <td>Sl</td>
                    <td>Po NO</td>
                    <td>Ship Date</td>
                    <td>Pre-Cost Qty</td>
                    <td>Mn.Book Qty</td>
                    <td>Sht.Book Qty</td>
                    <td>Smp.Book Qty</td>
                    <td>Tot.Book Qty</td>
                    <td>Balance</td>
                    <td>Comments</td>
                    </tr>
                    <?
	$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	if ($cbo_fabric_natu!=0) $cbo_fabric_natu="and a.fab_nature_id='$cbo_fabric_natu'";
	if ($cbo_fabric_source!=0) $cbo_fabric_source_cond="and a.fabric_source='$cbo_fabric_source'";
	$paln_cut_qnty_array=return_library_array( "select min(id) as id,sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown  where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and is_deleted=0 and status_active=1 group by color_mst_id,size_mst_id,item_mst_id", "id", "plan_cut_qnty");

	$item_ratio_array=return_library_array( "select gmts_item_id,set_item_ratio from wo_po_details_mas_set_details  where job_no =$txt_job_no", "gmts_item_id", "set_item_ratio");
	$nameArray=sql_select("
	select
	a.id,
	a.item_number_id,
	a.costing_per,
	b.po_break_down_id,
	b.color_size_table_id,
	b.requirment,
	c.po_number
FROM
	wo_pre_cost_fabric_cost_dtls a,
	wo_pre_cos_fab_co_avg_con_dtls b,
	wo_po_break_down c
WHERE
	a.job_no=b.job_no and
	a.job_no=c.job_no_mst and
    a.id=b.pre_cost_fabric_cost_dtls_id and
	b.po_break_down_id=c.id and
	b.po_break_down_id in (".str_replace("'","",$txt_order_no_id).")  $cbo_fabric_natu $cbo_fabric_source_cond and a.status_active=1 and a.is_deleted=0
	order by a.id");
	$count=0;
	$tot_grey_req_as_pre_cost_arr=array();
	foreach ($nameArray as $result)
	{
		if (count($nameArray)>0 )
		{
            if($result[csf("costing_per")]==1)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(12*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
			}
			if($result[csf("costing_per")]==2)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(1*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
			}
			if($result[csf("costing_per")]==3)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(24*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
			}
			if($result[csf("costing_per")]==4)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(36*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
			}
			if($result[csf("costing_per")]==5)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(48*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
			}
			$tot_grey_req_as_pre_cost_arr[$result[csf("po_number")]]+=$tot_grey_req_as_pre_cost;
        }
    }
	                $total_pre_cost=0;
					$total_booking_qnty_main=0;
					$total_booking_qnty_short=0;
					$total_booking_qnty_sample=0;
					$total_tot_bok_qty=0;
					$tot_balance=0;
					/*$booking_qnty=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and booking_type in(1,4)  and a.status_active=1 and a.is_deleted=0 group by b.po_number order by a.po_break_down_id", "po_break_down_id", "grey_fab_qnty");*/
					$booking_qnty_main=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b, wo_booking_mst c where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no  and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and a.booking_type =1 and c.fabric_source=$cbo_fabric_source and a.is_short=2 and c.item_category in(2,3) and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");

					$booking_qnty_short=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b , wo_booking_mst c where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and a.booking_type =1 and c.fabric_source=$cbo_fabric_source and c.item_category in(2,3) and a.is_short=1 and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");
					$booking_qnty_sample=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b , wo_booking_mst c  where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and a.booking_type =4 and c.fabric_source=$cbo_fabric_source and c.item_category in(2,3)  and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");

					$sql_data=sql_select( "select max(a.id) as id,  a.po_number,max(a.pub_shipment_date) as pub_shipment_date,sum(a.plan_cut) as plan_cut  from wo_po_break_down a,wo_pre_cost_sum_dtls b,wo_pre_cost_mst c where a.job_no_mst=b.job_no and a.job_no_mst=c.job_no and a.id in(".str_replace("'","",$txt_order_no_id).") group by a.po_number order by id");
					foreach($sql_data  as $row)
                    {
					$col++;
					?>
                    <tr align="center">
                    <td><? echo $col; ?></td>
                    <td><? echo $row[csf("po_number")]; ?></td>
                     <td><? echo change_date_format($row[csf("pub_shipment_date")],"dd-mm-yyyy",'-'); ?></td>
                    <td align="right"><? echo number_format($tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]],2); $total_pre_cost+=$tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]]; ?></td>
                    <td align="right"><? echo number_format($booking_qnty_main[$row[csf("id")]],2); $total_booking_qnty_main+=$booking_qnty_main[$row[csf("id")]];?></td>
                    <td align="right"><? echo number_format($booking_qnty_short[$row[csf("id")]],2); $total_booking_qnty_short+=$booking_qnty_short[$row[csf("id")]];?></td>
                    <td align="right"><? echo number_format($booking_qnty_sample[$row[csf("id")]],2); $total_booking_qnty_sample+=$booking_qnty_sample[$row[csf("id")]];?></td>
                    <td align="right"><? $tot_bok_qty=$booking_qnty_main[$row[csf("id")]]+$booking_qnty_short[$row[csf("id")]]+$booking_qnty_sample[$row[csf("id")]]; echo number_format($tot_bok_qty,2); $total_tot_bok_qty+=$tot_bok_qty;?></td>
                    <td align="right">
					<? $balance= def_number_format($tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]]-$tot_bok_qty,2,""); echo number_format($balance,2); $tot_balance+= $balance?>
                    </td>
                    <td>
					<?
					if( $balance>0)
					{
						echo "Less Booking";
					}
					else if ($balance<0)
					{
						echo "Over Booking";
					}
					else
					{
						echo "";
					}
					?>
                    </td>
                    </tr>
                    <?
					}
					?>
                    <tfoot>

                    <tr>
                    <td colspan="3">Total:</td>

                    <td align="right"><? echo number_format($total_pre_cost,2); ?></td>
                    <td align="right"><? echo number_format($total_booking_qnty_main,2); ?></td>
                    <td align="right"><? echo number_format($total_booking_qnty_short,2); ?></td>
                    <td align="right"><? echo number_format($total_booking_qnty_sample,2); ?></td>
                     <td align="right"><? echo number_format($total_tot_bok_qty,2); ?></td>
                    <td align="right"><? echo number_format($tot_balance,2); ?></td>
                    <td></td>
                    </tr>
                    </tfoot>
                    </table>
                    <?
					 }
					?>
                </td>

            </tr>
        </table>
        <br/>
        <?
		//echo "select responsible_dept,	responsible_person,	reason from wo_booking_dtls where booking_no =$txt_reqsn_no";
		//"select id,department_name from  lib_department where status_active=1 and is_deleted=0 order by  department_name"
		$department_name_library=return_library_array( "select id,department_name from  lib_department where status_active=1 and is_deleted=0 order by  department_name", "id", "department_name"  );

		$sql_responsible= sql_select("select responsible_dept,	responsible_person,	reason from wo_booking_dtls where booking_no =$txt_reqsn_no and fin_fab_qnty>0");
		if(count($sql_responsible)>0)
		{
		?>
         <table width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
         <tr>
         <td>
          #
         </td>
          <td>
         Responsible Dept.
         </td>
         <td>
         Responsible person
         </td>
         <td>
         Reason
         </td>
         </tr>
         <?
		 $ir=1;
		foreach($sql_responsible as $sql_responsible_row)
		{
			?>
             <tr>
             <td>
             <?  echo $ir; ?>
             </td>
              <td>
             <?
			 $responsible_dept_st="";
			 $responsible_dept_arr=explode( ",",$sql_responsible_row[csf('responsible_dept')]);
			 foreach($responsible_dept_arr as $key => $value)
			 {
				 $responsible_dept_st.= $department_name_library[$value].", ";
			 }
			 echo rtrim($responsible_dept_st,", ");
			 ?>
             </td>
             <td>
            <?  echo $sql_responsible_row[csf('responsible_person')]; ?>
             </td>
             <td>
              <?  echo $sql_responsible_row[csf('reason')]; ?>
             </td>
             </tr>
            <?
			$ir++;

		}
		 ?>
         </table>
         <?
		}
		 ?>


         <?
		 	echo signature_table(4, $cbo_company_name, "1330px");
			echo "****".custom_file_name($txt_reqsn_no,$style_sting,$txt_job_no);
		 ?>
       </div>
	<script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
    fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
    </script>
       <?

}
// new print button for urmi
if($action=="show_fabric_requisition_report_urmi")//unused
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	$report_type=str_replace("'","",$report_type);

	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	//$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$location_name_arr=return_library_array( "select id,location_name from lib_location",'id','location_name');
	$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	$po_qnty_tot1=return_field_value( "sum(po_quantity)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	$pro_sub_dept_array=return_library_array( "select id,sub_department_name from lib_pro_sub_deparatment",'id','sub_department_name');
	//
	?>
	<div style="width:1330px" align="center">
    <?php
$nameArray_approved = sql_select("select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_reqsn_no and b.entry_form=12");
list($nameArray_approved_row) = $nameArray_approved;
$nameArray_approved_date = sql_select("select b.approved_date as approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_reqsn_no and b.entry_form=12 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "'");
list($nameArray_approved_date_row) = $nameArray_approved_date;
$nameArray_approved_comments = sql_select("select b.comments as comments from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_reqsn_no and b.entry_form=12 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "'");
list($nameArray_approved_comments_row) = $nameArray_approved_comments;
$path = str_replace("'", "", $path);
if ($path == "") {
	$path = '../../';
}
?>										<!--    Header Company Information         -->
       <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black" >
           <tr>
               <td width="220">
               <?
			   if($report_type==1)
			   {
			   ?>
               <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               <?
			   }
			   else if($report_type==2)
			   {
			   ?>
               <img  src='../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               <?
			   }
			   else
			   {
			   ?>
               <img  src='<? echo $path.$imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               <?
			   }
			   ?>
               </td>
               <td width="1250">
                    <table width="100%" cellpadding="0" cellspacing="0"  >
                        <tr>
                            <td align="center" style="font-size:20px;">
                              <?php
echo $company_library[$cbo_company_name];
?>
                            </td>
                            <td rowspan="3" width="250">

                               <span style="font-size:18px"><b> Job No:&nbsp;&nbsp;<? echo trim($txt_job_no,"'"); ?></b></span>
                               <br/>
                                <?
								 if($nameArray_approved_row[csf('approved_no')]>1)
								 {
								 ?>
								 <b> Revised No: <? echo $nameArray_approved_row[csf('approved_no')]-1; ?></b>
                                  <br/>
								  Approved Date: <? echo $nameArray_approved_date_row[csf('approved_date')]; ?>
								  <?
								 }
							  	?>


                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px">
                            <?
                            $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
							//$location=return_field_value("location_name","lib_location","id=$data[3]" );
							//$nameArray=sql_select("select location_name from wo_po_details_master where job_no=$txt_job_no");
							if($txt_job_no!="")
							{
							 $location=return_field_value( "location_name", "wo_po_details_master","job_no='$txt_job_no'");
							}
							else
							{
							$location="";
							}

							foreach ($nameArray as $result)
                            {
							 echo  $location_name_arr[$location];
                            ?>

                               <!-- Plot No: <? //echo $result[csf('plot_no')]; ?>
                                Level No: <? //echo $result[csf('level_no')]?>
                                Road No: <? //echo $result[csf('road_no')]; ?>
                                Block No: <? //echo $result[csf('block_no')];?>
                                City No: <? //echo $result[csf('city')];?>
                                Zip Code: <? //echo $result[csf('zip_code')]; ?>
                                Province No: <?php //echo $result[csf('province')];?>
                                Country: <? //echo $country_arr[$result[csf('country_id')]]; ?> --><br>
                                Email Address: <? echo $result[csf('email')];?>
                                Website No: <? echo $result[csf('website')]; ?>

                                <?

                            }

                            ?>
                               </td>
                            </tr>
                            <tr>
                            <td align="center" style="font-size:20px">
                                <strong><? //if($report_type==1) echo str_replace("'","",$report_title);else echo 'Short Fabric Booking';
								if($report_title !=""){ echo str_replace("'","",$report_title);} else { echo "General Work Order";}?> &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:#F00"><? if(str_replace("'","",$id_approved_id) ==1){ echo "(Approved)";}else{echo "";}; ?> </font></strong>
                             </td>
                            </tr>
                      </table>
                </td>
            </tr>
       </table>
                <?
				$uom=0;
				$job_no='';
				$total_set_qnty=0;
				$colar_excess_percent=0;
				$cuff_excess_percent=0;
				$rmg_process_breakdown=0;
				$poid=str_replace("'","",$txt_order_no_id);
				$po_date_arr=array();
				$sql_po_date= "select job_no_mst,MIN(pub_shipment_date) pub_shipment_date,MAX(pub_shipment_date) pub_shipment_date_max from  wo_po_break_down  where job_no_mst=$txt_job_no and id in($poid)  group by job_no_mst order by pub_shipment_date";
				 $poArray=sql_select($sql_po_date);
				foreach($poArray as $row)
				{
					$po_date_arr[$row[csf('job_no_mst')]]=change_date_format($row[csf('pub_shipment_date')]).','.change_date_format($row[csf('pub_shipment_date_max')]);
				}
				//print_r($po_date_arr);
                $nameArray=sql_select( "select a.booking_no, a.booking_date, a.supplier_id, a.currency_id, a.exchange_rate, a.attention, a.delivery_date, a.po_break_down_id, a.colar_excess_percent, a.cuff_excess_percent, a.delivery_date, a.is_apply_last_update, a.fabric_source, a.pay_mode, a.rmg_process_breakdown, a.insert_date, a.update_date, a.uom, a.remarks, a.short_booking_type, b.job_no, b.buyer_name, b.style_ref_no, b.gmts_item_id, b.order_uom, b.total_set_qnty, b.style_description, b.season, b.season_matrix, b.order_repeat_no, b.product_dept, b.product_code, b.pro_sub_dep, b.dealing_marchant, b.client_id, b.qlty_label from wo_booking_mst a, wo_po_details_master b where a.job_no=b.job_no and a.booking_no=$txt_reqsn_no");
				foreach ($nameArray as $result)
				{
					$total_set_qnty=$result[csf('total_set_qnty')];
					$colar_excess_percent=$result[csf('colar_excess_percent')];
				    $cuff_excess_percent=$result[csf('cuff_excess_percent')];
					$rmg_process_breakdown=$result[csf('rmg_process_breakdown')];
					$po_no_arr=array();
					$shipment_date="";$shipment_date_max="";
					$sql_po= "select po_number,MIN(pub_shipment_date) pub_shipment_date,MAX(pub_shipment_date) pub_shipment_date_max from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].") group by po_number";
					//echo "select po_number,MIN(pub_shipment_date) pub_shipment_date,MAX(pub_shipment_date) pub_shipment_date_max from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].") group by po_number";
					$data_array_po=sql_select($sql_po);
					foreach ($data_array_po as $row_po)
					{
						$po_no_arr[$row_po[csf('po_number')]]=$row_po[csf('po_number')];
						//$shipment_date.=change_date_format($row_po[csf('pub_shipment_date')],'dd-mm-yyyy','-').",";
						//$shipment_date_max.=change_date_format($row_po[csf('pub_shipment_date_max')],'dd-mm-yyyy','-').",";
						//$shipment_date_con=change_date_format($row_po[csf('pub_shipment_date')],'dd-mm-yyyy','-').",".change_date_format($row_po[csf('pub_shipment_date_max')],'dd-mm-yyyy','-');
					}
					$po_no=implode(",",$po_no_arr);
					$shipment_date=$po_date_arr[$result[csf('job_no')]];

					$lead_time_arr=array();
					if($db_type==0)
					{
					$sql_lead_time= "select DATEDIFF(pub_shipment_date,po_received_date) date_diff from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")";
					}
					if($db_type==2)
					{
					$sql_lead_time= "select (pub_shipment_date-po_received_date) date_diff from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")";
					}
					$data_array_lead_time=sql_select($sql_lead_time);
					foreach ($data_array_lead_time as $row_lead_time)
					{
						$lead_time_arr[]=$row_lead_time[csf('date_diff')];
						//$shipment_date.=change_date_format($row_po['pub_shipment_date'],'dd-mm-yyyy','-').",";
					}
					$lead_time=implode(",",array_unique($lead_time_arr));
					$po_received_date="";
					$sql_po_received_date= "select MIN(po_received_date) as po_received_date from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")";
					$data_array_po_received_date=sql_select($sql_po_received_date);
					foreach ($data_array_po_received_date as $row_po_received_date)
					{
						$po_received_date=change_date_format($row_po_received_date[csf('po_received_date')],'dd-mm-yyyy','-');
						//$shipment_date.=change_date_format($row_po['pub_shipment_date'],'dd-mm-yyyy','-').",";
					}


				   $daysInHand_arr=array();
				   $WOPreparedAfter_arr=array();
				   $shiping_status_arr=array();
					$sql_po= "select po_number,MIN(pub_shipment_date) pub_shipment_date, MIN(insert_date) as insert_date,shiping_status from wo_po_break_down  where id in(".$result[csf('po_break_down_id')].") group by po_number,shiping_status";
					$data_array_po=sql_select($sql_po);
					foreach ($data_array_po as $rows)
					{
						$daysInHand_arr[]=(datediff('d',date('d-m-Y',time()),$rows[csf('pub_shipment_date')])-1);
						//$daysInHand=(datediff('d',date('d-m-Y',time()),$rows[csf('pub_shipment_date')])-1).",";
						$booking_date=$result[csf('update_date')];
						if($booking_date=="" || $booking_date=="0000-00-00 00:00:00")
						{
							$booking_date=$result[csf('insert_date')];
						}
						//$WOPreparedAfter.=(datediff('d',$rows[csf('insert_date')],$booking_date)-1).",";
						$WOPreparedAfter_arr[]=(datediff('d',$rows[csf('insert_date')],$booking_date)-1);

						if($rows[csf('shiping_status')]==1)
						{
						//$shiping_status.= "FP".",";
						$shiping_status_arr[]="FP";
						}
						else if($rows[csf('shiping_status')]==2)
						{
						//$shiping_status.= "PS".",";
						$shiping_status_arr[]="PS";

						}
						else if($rows[csf('shiping_status')]==3)
						{
						//$shiping_status.= "FS".",";
						$shiping_status_arr[]="FS";
						}

					}
					$daysInHand=implode(",",array_unique($daysInHand_arr));
					$WOPreparedAfter=implode(",",array_unique($WOPreparedAfter_arr));
					$shiping_status=implode(",",array_unique($shiping_status_arr));

				if($db_type==2) $group_concat_all=" listagg(cast(b.grouping as varchar2(4000)),',') within group (order by b.grouping) as grouping,
	                                    listagg(cast(b.file_no as varchar2(4000)),',') within group (order by b.file_no) as file_no  ";
				else { $group_concat_all="group_concat(b.grouping) as grouping, group_concat(b.file_no) as file_no";}
				$data_array3=sql_select("select a.job_no,a.company_name,a.buyer_name,$group_concat_all from wo_po_details_master a, wo_po_break_down b where b.id in (".$result[csf('po_break_down_id')].") and a.job_no=b.job_no_mst group by a.job_no,a.company_name,a.buyer_name");
				//echo $sql_grouping= "select booking_no  from wo_booking_dtls  where  po_break_down_id in(".$result[csf('po_break_down_id')].") and booking_type=1 and is_short=2 ";
				/*if($db_type==2) $group_concat_book="listagg(cast(booking_no as varchar2(4000)),',') within group (order by booking_no) as booking_no";
				else  $group_concat_book="group_concat(distinct booking_no) as booking_no ";

				$main_fab_booking_no=return_field_value( "$group_concat_book", "wo_booking_dtls","po_break_down_id in(".$result[csf('po_break_down_id')].") and booking_type=1 and is_short=2","booking_no");
				$main_fab_booking_no=implode(",",array_unique(explode(",",$main_fab_booking_no)));*/
				$mb_arr=array();
				$sql_mb= sql_select("select booking_no  from wo_booking_dtls  where  po_break_down_id in(".$result[csf('po_break_down_id')].") and booking_type=1 and is_short=2 ");                foreach($sql_mb as $row_mb){
					$mb_arr[$row_mb[csf('booking_no')]]=$row_mb[csf('booking_no')];

				}
				$main_fab_booking_no=implode(",",$mb_arr);
				?>
       <table width="100%" style="border:1px solid black;table-layout: fixed;" >
            <tr>
                <td colspan="6" valign="top" style="font-size:18px; color:#F00"><? if($result[csf('is_apply_last_update')]==2){echo "Booking Info not synchronized with order entry and pre-costing. order entry or pre-costing has updated after booking entry.  Contact to ".$marchentrArr[$result[csf('dealing_marchant')]]; } else{ echo "";} ?></td>
            </tr>
            <tr>
                <td width="200"><span style="font-size:18px"><b>Buyer/Agent Name</b></span></td>
                <td width="220">:&nbsp;<span style="font-size:18px"><b><? $buyer_name_str=""; if($result[csf('client_id')]!=0) $buyer_name_str=$buyer_name_arr[$result[csf('buyer_name')]]."-".$buyer_name_arr[$result[csf('client_id')]]; else $buyer_name_str=$buyer_name_arr[$result[csf('buyer_name')]]; echo $buyer_name_str; ?></b></span></td>
                <td width="200"><span style="font-size:12px"><b>Dept.</b></span></td>
                <td width="220">:&nbsp;<? echo $product_dept[$result[csf('product_dept')]] ; if($result[csf('product_code')] !=""){ echo " (".$result[csf('product_code')].")";} if($result[csf('pro_sub_dep')] !=0){ echo " (".$pro_sub_dept_array[$result[csf('pro_sub_dep')]].")";}?></td>
                <td width="200"><span style="font-size:12px"><b>Order Qnty</b></span></td>
                <td>:&nbsp; <?  echo $po_qnty_tot1." ".$unit_of_measurement[$result[csf('order_uom')]] ; ?> </td>
            </tr>
            <tr>

                <td style="font-size:12px"><b>Garments Item</b></td>
                <td>:&nbsp;
				<?
				$gmts_item_name="";
				$gmts_item=explode(',',$result[csf('gmts_item_id')]);
				for($g=0;$g<=count($gmts_item); $g++)
				{
					$gmts_item_name.= $garments_item[$gmts_item[$g]].",";
				}
				echo rtrim($gmts_item_name,',');
				?>
                </td>
                <td style="font-size:12px"><b>Booking Release Date</b></td>
                <td>:&nbsp;
				<?
				$booking_date=$result[csf('update_date')];
				if($booking_date=="" || $booking_date=="0000-00-00 00:00:00")
				{
					$booking_date=$result[csf('insert_date')];
				}
				echo change_date_format($booking_date,'dd-mm-yyyy','-','');
				?>&nbsp;&nbsp;&nbsp;</td>
                <td style="font-size:18px"><b>Style Ref.</b>   </td>
                <td style="font-size:18px">:&nbsp;<b><? echo $style_sting=$result[csf('style_ref_no')];?> </b>   </td>
            </tr>
             <tr>
                <td style="font-size:12px"><b>Style Des.</b></td>
                <td>:&nbsp;<? echo $result[csf('style_description')]; $job_no= $result[csf('job_no')];?></td>
                <td style="font-size:12px"><b>Lead Time </b>   </td>
                <td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;">:&nbsp;<?  echo rtrim($lead_time,",");?> </td>
                <td style="font-size:12px"><b>Dealing Merchant</b></td>
                <td>:&nbsp;<? echo $marchentrArr[$result[csf('dealing_marchant')]]; ?></td>
            </tr>

            <tr>
                <td style="font-size:12px"><b>Supplier Name</b>   </td>
                <td>:&nbsp;<?
				if($result[csf('pay_mode')]==5 || $result[csf('pay_mode')]==3){
					echo $company_library[$result[csf('supplier_id')]];
					}
					else{
					echo $supplier_name_arr[$result[csf('supplier_id')]];
					}
				//echo $supplier_name_arr[$result[csf('supplier_id')]];?>    </td>
                <td style="font-size:12px"><b>Delivery Date</b></td>
               	<td>:&nbsp;<? echo change_date_format( $result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>
                <td style="font-size:18px"><b>Booking No </b>   </td>
                <td style="font-size:18px">:&nbsp;<b><? echo $result[csf('booking_no')];?></b><? echo "(".$fabric_source[$result[csf('fabric_source')]].")"?> <? echo "(".$unit_of_measurement[$result[csf('uom')]].")"; $uom=$result[csf('uom')];?></td>

              <?
              //$shipment_date=implode(",",array_unique(explode(",",rtrim($shipment_date,","))));
			  // $shipment_date_max=implode(",",array_unique(explode(",",rtrim($shipment_date_max,","))));//rtrim($shipment_date_max,",");
			 //  if($shipment_date!='' && $shipment_date_max!='') $ship_date= $shipment_date.','.$shipment_date;
			 //  else if($shipment_date!='')  $ship_date= $shipment_date;
			   //else if($shipment_date_max!='')  $ship_date= $shipment_date_max;
			   //echo shipment_date;
			  ?>

            </tr>
            <tr>
                <td style="font-size:12px"><b>Season</b></td>
                <td>:&nbsp;<? $season_matrix=return_field_value("season_name","lib_buyer_season","id=".$result[csf('season_matrix')],"season_name");echo $season_matrix; ?></td>
                <td  style="font-size:12px"><b>Attention</b></td>
                <td  >:&nbsp;<? echo $result[csf('attention')]; ?></td>
                <td  style="font-size:12px"><b>Po Received Date</b></td>
                <td  >:&nbsp;<? echo $po_received_date; ?></td>



            </tr>
           <tr>
               <td style="font-size:18px"><b>Order No</b></td>
               <td style="font-size:18px;overflow:hidden;text-overflow: ellipsis;white-space: nowrap;" colspan="3">:&nbsp;<b><? echo rtrim($po_no,", "); ?></b></td>
               <td  style="font-size:12px"><b>Repeat No</b></td>
               <td  >:&nbsp;<? echo $result[csf('order_repeat_no')]; ?></td>
            </tr>
            <tr>
               <td style="font-size:12px"><b>Shipment Date</b></td>
                <td colspan="3"> :&nbsp;<? echo $shipment_date; ?></td>
                <td  style="font-size:12px"><b>Quality Label</b></td>
               <td  >:&nbsp;<? echo $quality_label[$result[csf('qlty_label')]]; ?></td>

            </tr>

            </tr>
            <tr>
               <td style="font-size:12px"><b>WO Prepared After</b></td>
               <td  style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"> :&nbsp;<? echo rtrim($WOPreparedAfter,',').' Days' ?></td>

               <td style="font-size:12px"><b>Ship.days in Hand</b></td>
               <td  style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"> :&nbsp;<? echo rtrim($daysInHand,',').' Days'?></td>

               <td style="font-size:12px"><b>Ex-factory status</b></td>
               <td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"> :&nbsp;<? echo rtrim($shiping_status,','); ?></td>

            </tr>
           <tr>
               <td style="font-size:18px"><b>Internal Ref No</b></td>
               <td style="font-size:18px"> :&nbsp;<b><? echo implode(",",array_unique(explode(",",$data_array3[0][csf("grouping")]))); ?></b></td>
               <td style="font-size:18px"><b>File no</b></td>
               <td style="font-size:18px"> :&nbsp;<b><? echo  implode(",",array_unique(explode(",",$data_array3[0][csf("file_no")])));?></b></td>
               <td style="font-size:18px"><b>Currency</b></td>
               <td style="font-size:18px"> :&nbsp;<b><? echo  $currency[$result[csf("currency_id")]];?></b></td>
            </tr>
            <tr>
                <td style="font-size:18px"><b>Main Fab. Booking No</b></td>
               <td style="font-size:18px" colspan="5"> : <? echo $main_fab_booking_no?></td>
          </tr>
          <tr>
               <td style="font-size:18px"><b>Remarks</b></td>
               <td style="font-size:18px" colspan="3"> :<? echo $result[csf('remarks')]?></td>
               <td style="font-size:18px"><b>Short Booking Type</b></td>
               <td style="font-size:18px"> :<? echo $short_booking_type[$result[csf('short_booking_type')]]?></td>
          </tr>
        </table>
           <?
			}
			if($cbo_fabric_source==1 || $cbo_fabric_source==2)
			{
			//$nameArray_size=sql_select( "select distinct size_number_id from wo_po_color_size_breakdown where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  	is_deleted=0 and status_active=1 order by size_number_id");
			//$nameArray_size=sql_select( "select  size_number_id,min(id) as id,	min(size_order) as size_order from wo_po_color_size_breakdown where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  	is_deleted=0 and status_active=1 group by size_number_id order by id");
			$nameArray_size=sql_select( "select  size_number_id,min(id) as id,	min(size_order) as size_order from wo_po_color_size_breakdown where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  	is_deleted=0 and status_active=1 group by size_number_id order by size_order");

		   ?>
            <table width="100%" >
		    <tr>
            <td width="800">
                <div id="div_size_color_matrix" style="float:left; max-width:1000;">
            	<fieldset id="div_size_color_matrix" style="max-width:1000;">
 				<legend>Size and Color Breakdown</legend>
 				<table  class="rpt_table"  border="1" align="left" cellpadding="0" width="750" cellspacing="0" rules="all" >
                    <tr>
                        <td style="border:1px solid black"><strong>Color/Size</strong></td>
                    <?
						foreach($nameArray_size  as $result_size)
                        {	     ?>
                        <td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
                    <?	}    ?>
                        <td style="border:1px solid black; width:130px" align="center"><strong> Total Order Qty(Pcs)</strong></td>
                        <td style="border:1px solid black; width:80px" align="center"><strong> Excess %</strong></td>
                        <td style="border:1px solid black; width:130px" align="center"><strong> Total Plan Cut Qty(Pcs)</strong></td>
                    </tr>
                    <?
					$color_size_order_qnty_array=array();
					$color_size_qnty_array=array();
					$size_tatal=array();
					$size_tatal_order=array();
					for($c=0;$c<count($gmts_item); $c++)
				    {
					$item_size_tatal=array();
					$item_size_tatal_order=array();
					$item_grand_total=0;
					$item_grand_total_order=0;
					//$nameArray_color=sql_select( "select distinct color_number_id from wo_po_color_size_breakdown where  item_number_id=$gmts_item[$c] and po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and is_deleted=0 and status_active=1 order by color_number_id");
					//$nameArray_color=sql_select( "select  color_number_id,min(id) as id,min(color_order) as color_order from wo_po_color_size_breakdown where  item_number_id=$gmts_item[$c] and po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and is_deleted=0 and status_active=1 group by color_number_id  order by id");
					$nameArray_color=sql_select( "select  color_number_id,min(id) as id,min(color_order) as color_order from wo_po_color_size_breakdown where  item_number_id=$gmts_item[$c] and po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and is_deleted=0 and status_active=1 group by color_number_id  order by color_order");
					?>
                    <tr>
                    <td style="border:1px solid black" colspan="<? echo count($nameArray_size)+3;?>"><strong><? echo $garments_item[$gmts_item[$c]];?></strong></td>

                    </tr>
                    <?
					foreach($nameArray_color as $result_color)
                    {
                    ?>
                    <tr>
                        <td align="center" style="border:1px solid black"><? echo $color_library[$result_color[csf('color_number_id')]]; // echo $row_num_tr; ?></td>
                        <?
						$color_total=0;
						$color_total_order=0;

						foreach($nameArray_size  as $result_size)
						{
						$nameArray_color_size_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as  order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$result_color[csf('color_number_id')]."  and item_number_id=$gmts_item[$c] and  status_active=1 and is_deleted =0");
						foreach($nameArray_color_size_qnty as $result_color_size_qnty)
                        {
                        ?>
                            <td style="border:1px solid black; text-align:right">
							<?
								if($result_color_size_qnty[csf('plan_cut_qnty')]!= "")
								{
									 echo number_format($result_color_size_qnty[csf('order_quantity')],0);
									 $color_total += $result_color_size_qnty[csf('plan_cut_qnty')] ;
									 $color_total_order += $result_color_size_qnty[csf('order_quantity')] ;
									 $item_grand_total+=$result_color_size_qnty[csf('plan_cut_qnty')];
									 $item_grand_total_order+=$result_color_size_qnty[csf('order_quantity')];
								     $grand_total +=$result_color_size_qnty[csf('plan_cut_qnty')];
									 $grand_total_order +=$result_color_size_qnty[csf('order_quantity')];

									 $color_size_qnty_array[$result_size[csf('size_number_id')]][$result_color[csf('color_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')];
									 $color_size_order_qnty_array[$result_size[csf('size_number_id')]][$result_color[csf('color_number_id')]]=$result_color_size_qnty[csf('order_quantity')];
									 if (array_key_exists($result_size[csf('size_number_id')], $size_tatal))
									 {
											$size_tatal[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('plan_cut_qnty')];
											$size_tatal_order[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('order_quantity')];
									 }
									 else
									 {
										$size_tatal[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')];
										$size_tatal_order[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('order_quantity')];
									 }
									 if (array_key_exists($result_size[csf('size_number_id')], $item_size_tatal))
									 {
											$item_size_tatal[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('plan_cut_qnty')];
											$item_size_tatal_order[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('order_quantity')];
									 }
									 else
									 {
										$item_size_tatal[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')];
										$item_size_tatal_order[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('order_quantity')];
									 }
								}
								else echo "0";
							 ?>
							</td>

                    <?
						}
                        }
                        ?>
                          <td style="border:1px solid black; text-align:right"><?  echo number_format(round($color_total_order),0); ?></td>

                         <td style="border:1px solid black; text-align:right"><? $excexss_per=($color_total-$color_total_order)/$color_total_order*100; echo number_format($excexss_per,2)." %"; ?>
                         </td>
                        <td style="border:1px solid black; text-align:right"><? echo number_format(round($color_total),0); ?></td>
                    </tr>
                    <?
                    }
					?>

                        <td align="center" style="border:1px solid black"><strong>Sub Total</strong></td>
                        <?
						foreach($nameArray_size  as $result_size)
                        {
                        ?>
                        <td style="border:1px solid black;  text-align:right"><? echo $item_size_tatal_order[$result_size[csf('size_number_id')]];  ?></td>
                        <?
                        }
                        ?>
                        <td  style="border:1px solid black;  text-align:right"><?  echo number_format(round($item_grand_total_order),0); ?></td>
                        <td  style="border:1px solid black;  text-align:right"><? $excess_item_gra_tot=($item_grand_total-$item_grand_total_order)/$item_grand_total_order*100; echo number_format($excess_item_gra_tot,2)." %"; ?></td>
                        <td  style="border:1px solid black;  text-align:right"><?  echo number_format(round($item_grand_total),0); ?></td>
                    </tr>
                    <?
					}
                    ?>
                     <tr>
                        <td style="border:1px solid black" align="center" colspan="<? echo count($nameArray_size)+3; ?>"><strong>&nbsp;</strong></td>
                        </tr>
                    <tr>
                    <tr>
                        <td align="center" style="border:1px solid black"><strong>Grand Total</strong></td>
                        <?
						foreach($nameArray_size  as $result_size)
                        {
                        ?>
                        <td style="border:1px solid black;  text-align:right"><? echo $size_tatal_order[$result_size[csf('size_number_id')]];  ?></td>
                        <?
                        }
                        ?>
                        <td  style="border:1px solid black;  text-align:right"><?  echo number_format(round($grand_total_order),0); ?></td>
                        <td  style="border:1px solid black;  text-align:right"><? $excess_gra_tot= ($grand_total-$grand_total_order)/$grand_total_order*100; echo number_format($excess_gra_tot,2)." %"; ?></td>
                        <td  style="border:1px solid black;  text-align:right"><?  echo number_format(round($grand_total),0); ?></td>
                    </tr>
                </table>
                </fieldset>
                </div>
                </td>
                <td width="200" valign="top" align="left">
                <div id="div_size_color_matrix" style="float:left;">
                <?
				$rmg_process_breakdown_arr=explode('_',$rmg_process_breakdown)
				?>
            	 	<fieldset id="" >
 				<legend>RMG Process Loss % </legend>
            	<table width="180" class="rpt_table" border="1" rules="all">
                <?
				if(number_format($rmg_process_breakdown_arr[8],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Cut Panel rejection <!-- Extra Cutting % breack Down 8-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[8],2);
				?>
                </td>
                </tr>
                <?
                }
				if(number_format($rmg_process_breakdown_arr[2],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Chest Printing <!-- Printing % breack Down 2-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[2],2);
				?>
                </td>
                </tr>
                <?
                }
				if(number_format($rmg_process_breakdown_arr[10],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Neck/Sleeve Printing <!-- New breack Down 10-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[10],2);
				?>
                </td>
                </tr>
                <?
                }
				if(number_format($rmg_process_breakdown_arr[1],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Embroidery   <!-- Embroidery  % breack Down 1-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[1],2);
				?>
                </td>
                </tr>
                <?
                }
				if(number_format($rmg_process_breakdown_arr[4],2)>0)
				{
				?>
                <tr>
                <td width="130">
                 Sewing /Input<!-- Sewing % breack Down 4-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[4],2);
				?>
                </td>
                </tr>
                <?
                }
				if(number_format($rmg_process_breakdown_arr[3],2)>0)
				{
				?>
                <tr>
                <td width="130">
                 Garments Wash <!-- Washing %breack Down 3-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[3],2);
				?>
                </td>
                </tr>
                <?
                }
				if(number_format($rmg_process_breakdown_arr[15],2)>0)
				{
				?>
                <tr>
                <td width="130">
                 Gmts Finishing <!-- Washing %breack Down 3-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[15],2);
				?>
                </td>
                </tr>
                <?
                }
				if(number_format($rmg_process_breakdown_arr[11],2)>0)
				{
				?>
                <tr>
                <td width="130">
                 Others <!-- New breack Down 11-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[11],2);
				?>
                </td>
                </tr>
                <?
                }
				$gmts_pro_sub_tot=$rmg_process_breakdown_arr[8]+$rmg_process_breakdown_arr[2]+$rmg_process_breakdown_arr[10]+$rmg_process_breakdown_arr[1]+$rmg_process_breakdown_arr[4]+$rmg_process_breakdown_arr[3]+$rmg_process_breakdown_arr[11]+$rmg_process_breakdown_arr[15];
				if($gmts_pro_sub_tot>0)
				{
				?>
                <tr>
                <td width="130">
                Sub Total <!-- New breack Down 11-->
                </td>
                <td align="right">
                <?

				echo number_format($gmts_pro_sub_tot,2);
				?>
                </td>
                </tr>
                <?
				}
				?>
                </table>
                </fieldset>


                <fieldset id="" >
 				<legend>Fabric Process Loss % </legend>
            	<table width="180" class="rpt_table" border="1" rules="all">
                 <?
				if(number_format($rmg_process_breakdown_arr[6],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Knitting  <!--  Knitting % breack Down 6-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[6],2);
				?>
                </td>
                </tr>
                <?
				}
				if(number_format($rmg_process_breakdown_arr[12],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Yarn Dyeing  <!--  New breack Down 12-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[12],2);
				?>
                </td>
                </tr>
                <?
				}
				if(number_format($rmg_process_breakdown_arr[5],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Dyeing & Finishing  <!-- Finishing % breack Down 5-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[5],2);
				?>
                </td>
                </tr>
                <?
				}
				if(number_format($rmg_process_breakdown_arr[13],2)>0)
				{
				?>
                <tr>
                <td width="130">
                All Over Print <!-- new  breack Down 13-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[13],2);
				?>
                </td>
                </tr>
                <?
				}
				if(number_format($rmg_process_breakdown_arr[14],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Lay Wash (Fabric) <!-- new  breack Down 14-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[14],2);
				?>
                </td>
                </tr>
                <?
				}
				if(number_format($rmg_process_breakdown_arr[7],2)>0)
				{
				?>
                 <tr>
                <td width="130">
                Dying   <!-- breack Down 7-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[7],2);
				?>
                </td>
                </tr>
                <?
				}
				if(number_format($rmg_process_breakdown_arr[0],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Cutting (Fabric) <!-- Cutting % breack Down 0-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[0],2);
				?>
                </td>
                </tr>
                <?
				}
				if(number_format($rmg_process_breakdown_arr[9],2)>0)
				{
				?>
                <tr>
                <td width="130">
               Others  <!-- Others% breack Down 9-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[9],2);
				?>
                </td>
                </tr>
                <?
				}
				$fab_proce_sub_tot=$rmg_process_breakdown_arr[6]+$rmg_process_breakdown_arr[12]+$rmg_process_breakdown_arr[5]+$rmg_process_breakdown_arr[13]+$rmg_process_breakdown_arr[14]+$rmg_process_breakdown_arr[7]+$rmg_process_breakdown_arr[0]+$rmg_process_breakdown_arr[9];
				if(fab_proce_sub_tot>0)
				{
				?>
                <tr>
                <td width="130">
                Sub Total  <!-- Others% breack Down 9-->
                </td>
                <td align="right">
                <?

				echo number_format($fab_proce_sub_tot,2);
				?>
                </td>
                </tr>
                <?
				}
				if($gmts_pro_sub_tot+$fab_proce_sub_tot>0)
				{
				?>
                 <tr>
                <td width="130">
                Grand Total  <!-- Others% breack Down 9-->
                </td>
                <td align="right">
                <?
				echo number_format($gmts_pro_sub_tot+$fab_proce_sub_tot,2);
				?>
                </td>
                </tr>
                <?
				}
				?>
           </table>
           </fieldset>
           </div>
                </td>
            <td width="330" valign="top" align="left">
            <?
			$nameArray_imge =sql_select("SELECT image_location FROM common_photo_library where master_tble_id='$job_no' and file_type=1");
			?>
            <div id="div_size_color_matrix" style="float:left;">
            	<fieldset id="" >
 				<legend>Image </legend>
            	<table width="310">
                <tr>
                <?
				$img_counter = 0;
                foreach($nameArray_imge as $result_imge)
				{
				    if($path=="")
                    {
                   	 $path='../../';
                    }

					?>
					<td>
						<!--<img src="../../<? //echo $result_imge[csf('image_location')]; ?>" width="90" height="100" border="2" />-->
                        <img src="<? echo $path.$result_imge[csf('image_location')]; ?>" width="90" height="100" border="2" />
					</td>
					<?

					$img_counter++;
				}
				?>
                </tr>
           </table>
           </fieldset>
           </div>
          </td>
        </tr>
       </table>
        <?
			}// if($cbo_fabric_source==1) end

	  ?>
      <br/>

      <!--  Here will be the main portion  -->
     <?
	 $costing_per="";
	 $costing_per_qnty=0;
	 $costing_per_id=return_field_value( "costing_per", "wo_pre_cost_mst","job_no ='$job_no'");
	 if($costing_per_id==1)
			{
				$costing_per="1 Dzn";
				$costing_per_qnty=12;

			}
			if($costing_per_id==2)
			{
				$costing_per="1 Pcs";
				$costing_per_qnty=1;

			}
			if($costing_per_id==3)
			{
				$costing_per="2 Dzn";
				$costing_per_qnty=24;

			}
			if($costing_per_id==4)
			{
				$costing_per="3 Dzn";
				$costing_per_qnty=36;

			}
			if($costing_per_id==5)
			{
				$costing_per="4 Dzn";
				$costing_per_qnty=48;

			}
			$process_loss_method=return_field_value( "process_loss_method", "wo_pre_cost_fabric_cost_dtls","job_no='$job_no'");;

$uom_arr=array(1=>"Pcs",12=>"Kg",23=>"Mtr",27=>"Yds");
foreach($uom_arr as $uom_id=>$uom_val){
	if($cbo_fabric_source==1)
	{
	$nameArray_fabric_description= sql_select("select a.id as fabric_cost_dtls_id, a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight,a.width_dia_type as width_dia_type , d.dia_width,b.remarks, avg(b.cons) as cons  , avg(b.process_loss_percent) as process_loss_percent, avg(b.requirment) as requirment  FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d
	WHERE a.job_no=b.job_no and
	a.id=b.pre_cost_fabric_cost_dtls_id and
	c.job_no_mst=a.job_no and
	c.id=b.color_size_table_id and
	b.po_break_down_id=d.po_break_down_id and
	b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
	d.is_short=1 and
	a.uom=$uom_id and
	b.cons>0 and
	d.booking_no =$txt_reqsn_no and
	d.status_active=1 and
	d.is_deleted=0
	group by a.id,a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,a.width_dia_type,d.dia_width,b.remarks order by fabric_cost_dtls_id,a.body_part_id,d.dia_width");
	if(count($nameArray_fabric_description)>0){
	 ?>

     <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
     <caption>Fabric Details in <? echo $uom_val ?></caption>
     <tr align="center">
     <th colspan="3" align="left">Body Part</th>
        <?
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('body_part_id')] == "")  echo "<td  colspan='3'>&nbsp</td>";
			else         		               echo "<td  colspan='3'>". $body_part[$result_fabric_description[csf('body_part_id')]]."</td>";
		}
		?>
        <td  rowspan="10" width="50"><p>Total Finish Fabric <? //echo "(".$unit_of_measurement[$uom].")"; //if(trim($cbo_fabric_natu ,"'")==2){echo "(KG)";}else{echo "(Yds)";} ?></p></td>

            <td  rowspan="10" width="50"><p>Avg Rate <? //echo "(".$unit_of_measurement[$uom].")"; //if(trim($cbo_fabric_natu ,"'")==2){echo "(KG)";}else{echo "(Yds)";} ?></p></td>
            <td  rowspan="10" width="50"><p>Amount </p></td>

       </tr>
     <tr align="center"><th colspan="3" align="left">Color Type</th>
        <?
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('color_type_id')] == "")  echo "<td  colspan='3'>&nbsp</td>";
			else         		               echo "<td  colspan='3'>". $color_type[$result_fabric_description[csf('color_type_id')]]."</td>";
		}
		?>
       </tr>
        <tr align="center"><th colspan="3" align="left">Fabric Construction</th>
        <?
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('construction')] == "")  echo "<td  colspan='3'>&nbsp</td>";
			else         		               echo "<td  colspan='3'>". $result_fabric_description[csf('construction')]."</td>";
		}
		?>


       </tr>
        <tr align="center"><th   colspan="3" align="left">Yarn Composition</th>
        <?
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('composition')] == "")   echo "<td colspan='3' >&nbsp</td>";
			else         		               echo "<td colspan='3' >".$result_fabric_description[csf('composition')]."</td>";
		}
		?>

       </tr>
       <tr align="center"><th  colspan="3" align="left">GSM</th>
        <?
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('gsm_weight')] == "")   echo "<td colspan='3'>&nbsp</td>";
			else         		       echo "<td colspan='3' align='center'>". $result_fabric_description[csf('gsm_weight')]."</td>";
		}
		?>

       </tr>
       <tr align="center"><th   colspan="3" align="left">Dia/Width (Inch)</th>
        <?
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('dia_width')] == "")   echo "<td colspan='3'>&nbsp</td>";
			else         		              echo "<td colspan='3' align='center'>".$result_fabric_description[csf('dia_width')].",".$fabric_typee[$result_fabric_description[csf('width_dia_type')]]."</td>";
		}
		?>

       </tr>
       <tr align="center"><th   colspan="3" align="left">Consumption For <? echo $costing_per; ?></th>
        <?
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('cons')] == "")   echo "<td colspan='3'>&nbsp</td>";
			else         		              echo "<td colspan='3' align='center'>Fin: ".number_format($result_fabric_description[csf('cons')],2)."</td>";
		}
		?>

       </tr>
       <tr align="center"><th   colspan="3" align="left">Remarks</th>
        <?
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('remarks')] == "")   echo "<td colspan='3'>&nbsp</td>";
			else         		              echo "<td colspan='3' align='center'>".$result_fabric_description[csf('remarks')]."</td>";
		}
		?>

       </tr>
       <tr>
       <th  colspan="<? echo  count($nameArray_fabric_description)*3+3; ?>" align="left" style="height:30px">&nbsp;</th>
       </tr>

       <tr>
            <!--<th  width="120" align="left">Gmts. Color</th>-->
            <th  width="120" align="left">Fabric Color</th>
            <th  width="120" align="left">Body Color</th>
            <th  width="120" align="left">Lab Dip No</th>
        <?
			foreach($nameArray_fabric_description as $result_fabric_description)
			{
				  echo "<th width='50'>Fab. Qty</th><th width='50' >Rate</th><th width='50' >Amount</th>";
			}


		?>

       </tr>
       <?

		  /*$gmt_color_library=return_library_array( "select gmts_color_id,contrast_color_id
		  FROM
		  wo_pre_cos_fab_co_color_dtls
		  WHERE
		  job_no ='$job_no'", "contrast_color_id", "gmts_color_id");*/
		  $gmt_color_library=array();
		  $gmt_color_data=sql_select("select b.gmts_color_id, b.contrast_color_id
		  FROM
		  wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_color_dtls b
		  WHERE a.id=b.pre_cost_fabric_cost_dtls_id and a.fab_nature_id=$cbo_fabric_natu and a.uom=$uom_id and a.fabric_source =$cbo_fabric_source and
		  a.job_no ='$job_no'");
		  foreach( $gmt_color_data as $gmt_color_row)
		  {
			//$gmt_color_library[$gmt_color_row[csf("contrast_color_id")]].=$color_library[$gmt_color_row[csf("gmts_color_id")]]."," ;
			$gmt_color_library[$gmt_color_row[csf("contrast_color_id")]][$gmt_color_row[csf("gmts_color_id")]]=$color_library[$gmt_color_row[csf("gmts_color_id")]];
		  }

	        $grand_total_fin_fab_qnty=0;
			$grand_total_amount=0;
			//$grand_totalcons_per_finish=0;
			//$grand_totalcons_per_grey=0;
			//$color_wise_wo_sql=sql_select("select fabric_color_id FROM wo_booking_dtls WHERE  booking_no =$txt_reqsn_no and status_active=1 and is_deleted=0 group by fabric_color_id");
			$color_wise_wo_sql=sql_select("select b.fabric_color_id  FROM  wo_pre_cost_fabric_cost_dtls a ,wo_booking_dtls b  WHERE a.id=b.pre_cost_fabric_cost_dtls_id and a.uom=$uom_id and b.booking_no = $txt_reqsn_no and b.status_active=1 and  b.is_deleted=0 group by b.fabric_color_id");

		foreach($color_wise_wo_sql as $color_wise_wo_result)
	    {
		?>
			<tr>
            <td  width="120" align="left">
			<?
			echo $color_library[$color_wise_wo_result[csf('fabric_color_id')]];
			?>
            </td>
            <td>
            <?
			//echo $color_library[$gmt_color_library[$color_wise_wo_result['fabric_color_id']]];
			//echo rtrim($gmt_color_library[$color_wise_wo_result[csf('fabric_color_id')]],",");
			echo implode(",",$gmt_color_library[$color_wise_wo_result[csf('fabric_color_id')]]);
			?>
            </td>
            <td  width="120" align="left">
			<?
			$lapdip_no="";
			$lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='".$job_no."' and approval_status=3 and color_name_id=".$color_wise_wo_result[csf('fabric_color_id')]."");
			//echo "lapdip_no from wo_po_lapdip_approval_info where job_no_mst='".$job_no."' and approval_status=3 and color_name_id=".$color_wise_wo_result['fabric_color_id']."";
			if($lapdip_no=="") echo "&nbsp;"; echo $lapdip_no;
			?>
            </td>
            <?
			$total_fin_fab_qnty=0;
			$total_amount=0;

			foreach($nameArray_fabric_description as $result_fabric_description)
		    {


				if($db_type==0)
				{
					$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty, sum(d.grey_fab_qnty) as grey_fab_qnty, avg(d.rate) as rate
					FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls d
					WHERE
					a.job_no=d.job_no and
					d.is_short=1 and
					a.id=d.pre_cost_fabric_cost_dtls_id and
					d.booking_no =$txt_reqsn_no and
					a.uom=$uom_id and
					a.id='".$result_fabric_description[csf('fabric_cost_dtls_id')]."' and
					a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
					a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
					a.construction='".$result_fabric_description[csf('construction')]."' and
					a.composition='".$result_fabric_description[csf('composition')]."' and
					a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
					d.dia_width='".$result_fabric_description[csf('dia_width')]."' and
					d.fabric_color_id=".$color_wise_wo_result[csf('fabric_color_id')]." and
					d.pre_cost_remarks='".$result_fabric_description[csf('remarks')]."' and
					d.status_active=1 and
					d.is_deleted=0");
				}
				if($db_type==2)
				{
					 $color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty, sum(d.grey_fab_qnty) as grey_fab_qnty, avg(d.rate) as rate
					FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls d
					WHERE
					a.job_no=d.job_no and
					d.is_short=1 and
					a.id=d.pre_cost_fabric_cost_dtls_id and
					d.booking_no =$txt_reqsn_no and
					a.uom=$uom_id and
					a.id='".$result_fabric_description[csf('fabric_cost_dtls_id')]."' and
					a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
					a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
					a.construction='".$result_fabric_description[csf('construction')]."' and
					a.composition='".$result_fabric_description[csf('composition')]."' and
					a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
					d.dia_width='".$result_fabric_description[csf('dia_width')]."' and
					d.pre_cost_remarks='".$result_fabric_description[csf('remarks')]."' and

					nvl(d.fabric_color_id,0)=nvl('".$color_wise_wo_result[csf('fabric_color_id')]."',0) and
					d.status_active=1 and
					d.is_deleted=0");
				}

				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
			?>
			<td width='50' align='right'>
			<?
			if($color_wise_wo_result_qnty[csf('grey_fab_qnty')]!="")
			{
				echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],0) ;
				$total_fin_fab_qnty+=$color_wise_wo_result_qnty[csf('grey_fab_qnty')];
			}
			?>
            </td>
            <td width='50' align='right' >
			<?
			if($color_wise_wo_result_qnty[csf('rate')]!="")
			{
			echo number_format($color_wise_wo_result_qnty[csf('rate')],4);
			//$total_grey_fab_qnty+=$color_wise_wo_result_qnty['grey_fab_qnty'];
			}
			?>
            </td>
            <td width='50' align='right' >
			<?
			$amount=$color_wise_wo_result_qnty[csf('grey_fab_qnty')]*$color_wise_wo_result_qnty[csf('rate')];
			if($amount!="")
			{
			echo number_format($amount,0);
			$total_amount+=$amount;
			}
			?>
            </td>
            <?
			}
			?>
            <td align="right"><? echo number_format($total_fin_fab_qnty,0); $grand_total_fin_fab_qnty+=$total_fin_fab_qnty;?></td>
            <td align="right"><? echo number_format($total_amount/$total_fin_fab_qnty,2); $grand_total_amount+=$total_amount;?></td>
            <td align="right">
            <?
			echo number_format($total_amount,0);

			?>
            </td>
            </tr>
         <?
		}
		?>
        <tr style=" font-weight:bold">
        <!--<td  width="120" align="left">&nbsp;</td>-->
        <th  width="120" align="left">&nbsp;</th>
        <td  width="120" align="left">&nbsp;</td>
        <td  width="120" align="left"><strong>Total</strong></td>
        <?
		foreach($nameArray_fabric_description as $result_fabric_description)
		{

			$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty
											FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls d
											WHERE
											a.job_no=d.job_no and
											d.is_short=1 and
											a.id=d.pre_cost_fabric_cost_dtls_id and
											d.booking_no =$txt_reqsn_no and
											a.uom=$uom_id and
											a.id='".$result_fabric_description[csf('fabric_cost_dtls_id')]."' and
											a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
											a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
											a.construction='".$result_fabric_description[csf('construction')]."' and
											a.composition='".$result_fabric_description[csf('composition')]."' and
											a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
											d.dia_width='".$result_fabric_description[csf('dia_width')]."' and
											d.pre_cost_remarks='".$result_fabric_description[csf('remarks')]."' and
											d.status_active=1 and
											d.is_deleted=0 ");
			list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
		?>
		<td width='50' align='right'><?  echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],0) ;?></td>
		<td width='50' align='right' > <? //echo number_format($color_wise_wo_result_qnty['grey_fab_qnty'],2);?></td>
		<td width='50' align='right' > <? //echo number_format($color_wise_wo_result_qnty['grey_fab_qnty'],2);?></td>
		<?
		}
		?>
		<td align="right"><? echo number_format($grand_total_fin_fab_qnty,0);?></td>
		<td align="right"><? echo number_format($grand_total_amount/$grand_total_fin_fab_qnty,2);?></td>
		<td align="right">
		<?
		echo number_format($grand_total_amount,0);
		?>
		</td>
		</tr>
		<tr style="font-weight:bold">
        <!--<td  width="120" align="left">&nbsp;</td>-->
        <th  width="120" align="left">&nbsp;</th>
        <td  width="120" align="left">&nbsp;</td>
        <td  width="120" align="left"><strong>Consumption For <? echo $costing_per; ?></strong></td>
        <?
			foreach($nameArray_fabric_description as $result_fabric_description)
		    {
				/*$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty
				  FROM
				  view_wo_fabric_booking_data_park a,
				  wo_booking_dtls b
				  WHERE
				  b.booking_no =$txt_reqsn_no  and
				  a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and
				  a.po_break_down_id=b.po_break_down_id and
				  a.color_size_table_id=b.color_size_table_id and
				  a.color_type_id='".$result_fabric_description['color_type_id']."' and
				  a.construction='".$result_fabric_description['construction']."' and
				  a.composition='".$result_fabric_description['composition']."' and
				  a.gsm_weight='".$result_fabric_description['gsm_weight']."' and
				  a.dia_width='".$result_fabric_description['dia_width']."' and
				  a.process_loss_percent='".$result_fabric_description['process_loss_percent']."' and
				  b.status_active=1 and
				  b.is_deleted=0
				  ");*/

			?>
			<td width='50' align='right'><?  //echo number_format(($color_wise_wo_result_qnty['fin_fab_qnty']/$grand_total)*($total_set_qnty*$costing_per_qnty),4) ;?></td>
            <td width='50' align='right' > <? //echo number_format(($color_wise_wo_result_qnty['grey_fab_qnty']/$grand_total)*($total_set_qnty*$costing_per_qnty),4);?></td>
            <td width='50' align='right' > <? //echo number_format(($color_wise_wo_result_qnty['grey_fab_qnty']/$grand_total)*($total_set_qnty*$costing_per_qnty),4);?></td>
            <?
			}
			?>
            <td align="right">
			<?
			$consumption_per_unit_fab=($grand_total_fin_fab_qnty/$po_qnty_tot)*($total_set_qnty*$costing_per_qnty);
			echo number_format($consumption_per_unit_fab,2);
			?>
            </td>
            <td align="right">
			<?
			$consumption_per_unit_amuont=($grand_total_amount/$po_qnty_tot)*($total_set_qnty*$costing_per_qnty);
			echo number_format(($consumption_per_unit_amuont/$consumption_per_unit_fab),2);
			?>
            </td>
            <td align="right" title="Only Allow Round Figer">
            <?
			echo number_format($consumption_per_unit_amuont,2);
			?>
            </td>
            </tr>
    </table>
    <?
	}
	}
    }


    foreach($uom_arr as $uom_id=>$uom_val){
	if($cbo_fabric_source==2)
	{
	$nameArray_fabric_description= sql_select("select a.id as fabric_cost_dtls_id,a.item_number_id, a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight,a.width_dia_type as width_dia_type , d.dia_width,b.remarks, avg(b.cons) as cons  , avg(b.process_loss_percent) as process_loss_percent, avg(b.requirment) as requirment  FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d
	WHERE a.job_no=b.job_no and
	a.id=b.pre_cost_fabric_cost_dtls_id and
	c.job_no_mst=a.job_no and
	c.id=b.color_size_table_id and
	b.po_break_down_id=d.po_break_down_id and
	b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
	d.is_short=1 and
	a.uom=$uom_id and
	b.cons>0 and
	d.booking_no =$txt_reqsn_no and
	d.status_active=1 and
	d.is_deleted=0
	group by a.id,a.body_part_id,a.item_number_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,a.width_dia_type,d.dia_width,b.remarks order by fabric_cost_dtls_id,a.body_part_id,d.dia_width");
	if(count($nameArray_fabric_description)>0){
	 ?>

     <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
     <caption>Fabric Details in <? echo $uom_val ?></caption>
     <tr align="center">
     <th colspan="3" align="left">Body Part</th>
        <?
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('body_part_id')] == "")  echo "<td  colspan='3'>&nbsp</td>";
			else         		               echo "<td  colspan='3'>". $body_part[$result_fabric_description[csf('body_part_id')]]."</td>";
		}
		?>
        <td  rowspan="10" width="50"><p>Total Finish Fabric  <? //echo "(".$unit_of_measurement[$uom].")"; //if(trim($cbo_fabric_natu ,"'")==2){echo "(KG)";}else{echo "(Yds)";} ?></p></td>

            <td  rowspan="10" width="50"><p>Avg Rate  <? //echo "(".$unit_of_measurement[$uom].")"; //if(trim($cbo_fabric_natu ,"'")==2){echo "(KG)";}else{echo "(Yds)";} ?></p></td>
            <td  rowspan="10" width="50"><p>Amount </p></td>

       </tr>
     <tr align="center"><th colspan="3" align="left">Color Type</th>
        <?
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('color_type_id')] == "")  echo "<td  colspan='3'>&nbsp</td>";
			else         		               echo "<td  colspan='3'>". $color_type[$result_fabric_description[csf('color_type_id')]]."</td>";
		}
		?>
       </tr>
        <tr align="center"><th colspan="3" align="left">Fabric Construction</th>
        <?
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('construction')] == "")  echo "<td  colspan='3'>&nbsp</td>";
			else         		               echo "<td  colspan='3'>". $result_fabric_description[csf('construction')]."</td>";
		}
		?>


       </tr>
        <tr align="center"><th   colspan="3" align="left">Yarn Composition</th>
        <?
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('composition')] == "")   echo "<td colspan='3' >&nbsp</td>";
			else         		               echo "<td colspan='3' >".$result_fabric_description[csf('composition')]."</td>";
		}
		?>

       </tr>
       <tr align="center"><th  colspan="3" align="left">GSM</th>
        <?
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('gsm_weight')] == "")   echo "<td colspan='3'>&nbsp</td>";
			else         		       echo "<td colspan='3' align='center'>". $result_fabric_description[csf('gsm_weight')]."</td>";
		}
		?>

       </tr>
       <tr align="center"><th   colspan="3" align="left">Dia/Width (Inch)</th>
        <?
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('dia_width')] == "")   echo "<td colspan='3'>&nbsp</td>";
			else         		              echo "<td colspan='3' align='center'>".$result_fabric_description[csf('dia_width')].",".$fabric_typee[$result_fabric_description[csf('width_dia_type')]]."</td>";
		}
		?>

       </tr>
       <tr align="center"><th   colspan="3" align="left">Consumption For <? echo $costing_per; ?></th>
        <?
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('cons')] == "")   echo "<td colspan='3'>&nbsp</td>";
			else         		              echo "<td colspan='3' align='center'>Fin: ".number_format($result_fabric_description[csf('cons')],2)."</td>";
		}
		?>

       </tr>
       <tr align="center"><th   colspan="3" align="left">Remarks</th>
        <?
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('remarks')] == "")   echo "<td colspan='3'>&nbsp</td>";
			else         		              echo "<td colspan='3' align='center'>".$result_fabric_description[csf('remarks')]."</td>";
		}
		?>

       </tr>
       <tr>
       <th  colspan="<? echo  count($nameArray_fabric_description)*3+3; ?>" align="left" style="height:30px">&nbsp;</th>
       </tr>

       <tr>
            <!--<th  width="120" align="left">Gmts. Color</th>-->
            <th  width="120" align="left">Fabric Color</th>
            <th  width="120" align="left">Body Color</th>
            <th  width="120" align="left">Lab Dip No</th>
        <?
			foreach($nameArray_fabric_description as $result_fabric_description)
			{
				  echo "<th width='50'>Fab. Qty</th><th width='50' >Rate</th><th width='50' >Amount</th>";
			}


		?>

       </tr>
       <?

		  /*$gmt_color_library=return_library_array( "select gmts_color_id,contrast_color_id
		  FROM
		  wo_pre_cos_fab_co_color_dtls
		  WHERE
		  job_no ='$job_no'", "contrast_color_id", "gmts_color_id");*/
		  $gmt_color_library=array();
		  $gmt_color_data=sql_select("select b.gmts_color_id, b.contrast_color_id
		  FROM
		  wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_color_dtls b
		  WHERE a.id=b.pre_cost_fabric_cost_dtls_id and a.fab_nature_id=$cbo_fabric_natu and a.uom=$uom_id and a.fabric_source =$cbo_fabric_source and
		  a.job_no ='$job_no'");
		  foreach( $gmt_color_data as $gmt_color_row)
		  {
			//$gmt_color_library[$gmt_color_row[csf("contrast_color_id")]].=$color_library[$gmt_color_row[csf("gmts_color_id")]]."," ;
			$gmt_color_library[$gmt_color_row[csf("contrast_color_id")]][$gmt_color_row[csf("gmts_color_id")]]=$color_library[$gmt_color_row[csf("gmts_color_id")]];
		  }

	        $grand_total_fin_fab_qnty=0;
			$grand_total_amount=0;
			//$grand_totalcons_per_finish=0;
			//$grand_totalcons_per_grey=0;
			//$color_wise_wo_sql=sql_select("select fabric_color_id FROM wo_booking_dtls WHERE  booking_no =$txt_reqsn_no and status_active=1 and is_deleted=0 group by fabric_color_id");
			$color_wise_wo_sql=sql_select("select b.fabric_color_id  FROM  wo_pre_cost_fabric_cost_dtls a ,wo_booking_dtls b  WHERE a.id=b.pre_cost_fabric_cost_dtls_id and a.uom=$uom_id and b.booking_no = $txt_reqsn_no and b.status_active=1 and  b.is_deleted=0 group by b.fabric_color_id");

		foreach($color_wise_wo_sql as $color_wise_wo_result)
	    {
		?>
			<tr>
           <!-- <td  width="120" align="left">
			<?

			//echo $color_library[$color_wise_wo_result['fabric_color_id']];

			?></td>-->
            <td  width="120" align="left">
			<?
			echo $color_library[$color_wise_wo_result[csf('fabric_color_id')]];


			?>
            </td>
            <td>
            <?
			//echo $color_library[$gmt_color_library[$color_wise_wo_result['fabric_color_id']]];
			//echo rtrim($gmt_color_library[$color_wise_wo_result[csf('fabric_color_id')]],",");
			echo implode(",",$gmt_color_library[$color_wise_wo_result[csf('fabric_color_id')]]);
			?>
            </td>
            <td  width="120" align="left">
			<?
			$lapdip_no="";
			$lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='".$job_no."' and approval_status=3 and color_name_id=".$color_wise_wo_result[csf('fabric_color_id')]."");
			//echo "lapdip_no from wo_po_lapdip_approval_info where job_no_mst='".$job_no."' and approval_status=3 and color_name_id=".$color_wise_wo_result['fabric_color_id']."";
			if($lapdip_no=="") echo "&nbsp;"; echo $lapdip_no;
			?>
            </td>
            <?
			$total_fin_fab_qnty=0;
			$total_amount=0;

			foreach($nameArray_fabric_description as $result_fabric_description)
		    {


				if($db_type==0)
				{
					$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty, sum(d.grey_fab_qnty) as grey_fab_qnty, avg(d.rate) as rate
					FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls d
					WHERE
					a.job_no=d.job_no and
					d.is_short=1 and
					a.id=d.pre_cost_fabric_cost_dtls_id and
					d.booking_no =$txt_reqsn_no and
					a.uom=$uom_id and
					a.id='".$result_fabric_description[csf('fabric_cost_dtls_id')]."' and
					a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
					a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
					a.construction='".$result_fabric_description[csf('construction')]."' and
					a.composition='".$result_fabric_description[csf('composition')]."' and
					a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
					d.dia_width='".$result_fabric_description[csf('dia_width')]."' and
					d.fabric_color_id=".$color_wise_wo_result[csf('fabric_color_id')]." and
					d.pre_cost_remarks='".$result_fabric_description[csf('remarks')]."' and
					d.status_active=1 and
					d.is_deleted=0");
				}
				if($db_type==2)
				{

					 $color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty, sum(d.grey_fab_qnty) as grey_fab_qnty, avg(d.rate) as rate
					FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls d
					WHERE
					a.job_no=d.job_no and
					d.is_short=1 and
					a.id=d.pre_cost_fabric_cost_dtls_id and
					d.booking_no =$txt_reqsn_no and
					a.uom=$uom_id and

					a.id='".$result_fabric_description[csf('fabric_cost_dtls_id')]."' and
					a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
					a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
					a.construction='".$result_fabric_description[csf('construction')]."' and
					a.composition='".$result_fabric_description[csf('composition')]."' and
					d.dia_width='".$result_fabric_description[csf('dia_width')]."' and
					a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
					d.pre_cost_remarks='".$result_fabric_description[csf('remarks')]."' and
					nvl(d.fabric_color_id,0)=nvl('".$color_wise_wo_result[csf('fabric_color_id')]."',0) and

					d.status_active=1 and
					d.is_deleted=0");
				}

				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
			?>
			<td width='50' align='right'>
			<?
			if($color_wise_wo_result_qnty[csf('grey_fab_qnty')]!="")
			{
				echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],0) ;
				$total_fin_fab_qnty+=$color_wise_wo_result_qnty[csf('grey_fab_qnty')];
			}
			?>
            </td>
            <td width='50' align='right' >
			<?
			if($color_wise_wo_result_qnty[csf('rate')]!="")
			{
			echo number_format($color_wise_wo_result_qnty[csf('rate')],4);
			//$total_grey_fab_qnty+=$color_wise_wo_result_qnty['grey_fab_qnty'];
			}
			?>
            </td>
            <td width='50' align='right' >
			<?
			$amount=$color_wise_wo_result_qnty[csf('grey_fab_qnty')]*$color_wise_wo_result_qnty[csf('rate')];
			if($amount!="")
			{
			echo number_format($amount,0);
			$total_amount+=$amount;
			}
			?>
            </td>
            <?
			}
			?>
            <td align="right"><? echo number_format($total_fin_fab_qnty,0); $grand_total_fin_fab_qnty+=$total_fin_fab_qnty;?></td>
            <td align="right"><? echo number_format($total_amount/$total_fin_fab_qnty,2); $grand_total_amount+=$total_amount;?></td>
            <td align="right">
            <?
			echo number_format($total_amount,0);

			?>
            </td>
            </tr>
         <?
		}
		?>
        <tr style=" font-weight:bold">
        <!--<td  width="120" align="left">&nbsp;</td>-->
        <th  width="120" align="left">&nbsp;</th>
        <td  width="120" align="left">&nbsp;</td>
        <td  width="120" align="left"><strong>Total</strong></td>
        <?
		foreach($nameArray_fabric_description as $result_fabric_description)
		{

			$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty
											FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls d
											WHERE
											a.job_no=d.job_no and
											d.is_short=1 and
											a.id=d.pre_cost_fabric_cost_dtls_id and
											d.booking_no =$txt_reqsn_no and
											a.uom=$uom_id and
											a.id='".$result_fabric_description[csf('fabric_cost_dtls_id')]."' and
											a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
											a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
											a.construction='".$result_fabric_description[csf('construction')]."' and
											a.composition='".$result_fabric_description[csf('composition')]."' and
											a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
											d.dia_width='".$result_fabric_description[csf('dia_width')]."' and
											d.pre_cost_remarks='".$result_fabric_description[csf('remarks')]."' and
											d.status_active=1 and
											d.is_deleted=0 ");
			list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
		?>
		<td width='50' align='right'><?  echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],0) ;?></td>
		<td width='50' align='right' > <? //echo number_format($color_wise_wo_result_qnty['grey_fab_qnty'],2);?></td>
		<td width='50' align='right' > <? //echo number_format($color_wise_wo_result_qnty['grey_fab_qnty'],2);?></td>
		<?
		}
		?>
		<td align="right"><? echo number_format($grand_total_fin_fab_qnty,0);?></td>
		<td align="right"><? echo number_format($grand_total_amount/$grand_total_fin_fab_qnty,2);?></td>
		<td align="right">
		<?
		echo number_format($grand_total_amount,0);
		?>
		</td>
		</tr>
		<tr style="font-weight:bold">
        <!--<td  width="120" align="left">&nbsp;</td>-->
        <th  width="120" align="left">&nbsp;</th>
        <td  width="120" align="left">&nbsp;</td>
        <td  width="120" align="left"><strong>Consumption For <? echo $costing_per; ?></strong></td>
        <?
			foreach($nameArray_fabric_description as $result_fabric_description)
		    {
				/*$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty
				  FROM
				  view_wo_fabric_booking_data_park a,
				  wo_booking_dtls b
				  WHERE
				  b.booking_no =$txt_reqsn_no  and
				  a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and
				  a.po_break_down_id=b.po_break_down_id and
				  a.color_size_table_id=b.color_size_table_id and
				  a.color_type_id='".$result_fabric_description['color_type_id']."' and
				  a.construction='".$result_fabric_description['construction']."' and
				  a.composition='".$result_fabric_description['composition']."' and
				  a.gsm_weight='".$result_fabric_description['gsm_weight']."' and
				  a.dia_width='".$result_fabric_description['dia_width']."' and
				  a.process_loss_percent='".$result_fabric_description['process_loss_percent']."' and
				  b.status_active=1 and
				  b.is_deleted=0
				  ");*/

			?>
			<td width='50' align='right'><?  //echo number_format(($color_wise_wo_result_qnty['fin_fab_qnty']/$grand_total)*($total_set_qnty*$costing_per_qnty),4) ;?></td>
            <td width='50' align='right' > <? //echo number_format(($color_wise_wo_result_qnty['grey_fab_qnty']/$grand_total)*($total_set_qnty*$costing_per_qnty),4);?></td>
            <td width='50' align='right' > <? //echo number_format(($color_wise_wo_result_qnty['grey_fab_qnty']/$grand_total)*($total_set_qnty*$costing_per_qnty),4);?></td>
            <?
			}
			?>
            <td align="right">
			<?
			$consumption_per_unit_fab=($grand_total_fin_fab_qnty/$po_qnty_tot)*($total_set_qnty*$costing_per_qnty);
			echo number_format($consumption_per_unit_fab,2);
			//$grand_total_fin_fab_qnty_dzn=number_format(($grand_total_fin_fab_qnty/$po_qnty_tot)*($total_set_qnty*$costing_per_qnty),4)
			?>
            </td>
            <td align="right">
			<?
			$consumption_per_unit_amuont=($grand_total_amount/$po_qnty_tot)*($total_set_qnty*$costing_per_qnty);
			echo number_format(($consumption_per_unit_amuont/$consumption_per_unit_fab),2);
			//$grand_total_grey_fab_qnty_dzn=number_format(($grand_total_grey_fab_qnty/$grand_total)*($total_set_qnty*$costing_per_qnty),4)
			?>
            </td>
            <td align="right" title="Only Allow Round Figer">
            <?
			echo number_format($consumption_per_unit_amuont,2);
			?>
            </td>
            </tr>
    </table>
    <?
	}
	}
    }
	?>
        <br/>

        <?

		if($cbo_fabric_source==1 || $cbo_fabric_source==2){
		?>
        <table  width="100%"  border="0" cellpadding="0" cellspacing="0" >
        <tr>
        <?
		//echo "select a.colar_cuff_per,b.gmts_sizes from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b,wo_pre_cost_fabric_cost_dtls c  where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.pre_cost_fabric_cost_dtls_id=c.id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.color_size_table_id    and a.booking_no=$txt_reqsn_no and a.booking_type=1 c.body_part_id in(2) and a.status_active=1 and a.is_deleted=0";
		$colar_percent_size_wise_array=return_library_array( "select a.colar_cuff_per,b.gmts_sizes from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b,wo_pre_cost_fabric_cost_dtls c  where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.pre_cost_fabric_cost_dtls_id=c.id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.color_size_table_id    and a.booking_no=$txt_reqsn_no and a.booking_type=1 and c.body_part_id in(2) and a.status_active=1 and a.is_deleted=0", "gmts_sizes", "colar_cuff_per");
		//print_r($colar_percent_size_wise_array);
		$nameArray_item_size=sql_select( "select min(c.id) as id,b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_reqsn_no  and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id  and d.status_active=1 and d.is_deleted=0 group by b.item_size,c.size_number_id order by id");
		if(count($nameArray_item_size)>0)
		{
        ?>
        <td width="49%">
        <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
        <tr>
        <td colspan="<? echo count($nameArray_size)+4;?>" align="center"><b>Collar -  Colour Size Brakedown in Pcs</b></td>
        </tr>
        <tr>
        <td width="70">Size</td>

        <?

		/*$nameArray_item_size=sql_select( "select b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_reqsn_no  and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 group by c.size_number_id,b.item_size order by c.size_number_id");*/
		foreach($nameArray_item_size  as $result_size)
		{
		?>
		<td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
		<?
        }
        ?>
        <td rowspan="2" align="center"><strong>Total</strong></td>
        <td width="60" rowspan="2" align="center"><strong>Extra %</strong></td>
        </tr>
        <tr>
        <td>Collar Size</td>

        <?
        foreach($nameArray_item_size  as $result_item_size)
		{
		?>
		<td align="center" style="border:1px solid black"><strong><? echo $result_item_size[csf('item_size')];?></strong></td>
		<?
        }
        ?>
         <?

			$color_wise_wo_sql=sql_select("select a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method,c.color_number_id,sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_reqsn_no and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 group by c.color_number_id,a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method order by a.id
");
		foreach($color_wise_wo_sql as $color_wise_wo_result)
	    {
			$color_total_collar=0;
			$color_total_collar_order_qnty=0;
			$process_loss_method=$color_wise_wo_result[csf("process_loss_method")];
			$constrast_color_arr=array();
			if($color_wise_wo_result[csf("color_size_sensitive")]==3)
			{
				$constrast_color=explode('__',$color_wise_wo_result[csf("color_break_down")]);
				for($i=0;$i<count($constrast_color);$i++)
				{
					$constrast_color2=explode('_',$constrast_color[$i]);
					$constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
				}
			}
		?>
			<tr>
            <td>
            <?
			if($color_wise_wo_result[csf("color_size_sensitive")]==3)
			{
				echo strtoupper ($constrast_color_arr[$color_wise_wo_result[csf('color_number_id')]]) ;
				$lab_dip_color_id=return_field_value("contrast_color_id","wo_pre_cos_fab_co_color_dtls","job_no='".$color_wise_wo_result[csf('job_no')]."' and pre_cost_fabric_cost_dtls_id=".$color_wise_wo_result[csf('id')]." and gmts_color_id=".$color_wise_wo_result[csf('color_number_id')]."");
			}
			else
			{
				echo $color_library[$color_wise_wo_result[csf('color_number_id')]];
				$lab_dip_color_id=$color_wise_wo_result[csf('color_number_id')];
			}
			?>

            </td>
            <?
            foreach($nameArray_item_size  as $result_size)
			{
				?>
				<td align="center" style="border:1px solid black">
				<?
				//$color_wise_wo_sql_qnty=sql_select("select c.color_number_id,sum(c.order_quantity) as order_quantity, sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_reqsn_no and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 and c.color_number_id='".$color_wise_wo_result['color_number_id']."' and c.size_number_id='".$result_size['size_number_id']."'");
				$color_wise_wo_sql_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$color_wise_wo_result[csf('color_number_id')]."   and  status_active=1 and is_deleted =0");

				list($plan_cut_qnty)=$color_wise_wo_sql_qnty;
				$plan_cut=$plan_cut_qnty[csf('plan_cut_qnty')];
				$colar_excess_per=($plan_cut*$colar_excess_percent)/100;
				echo number_format($plan_cut+$colar_excess_per,0);
				$color_total_collar+=$plan_cut+$colar_excess_per;
				$color_total_collar_order_qnty+=$plan_cut;
				$grand_total_collar+=$plan_cut+$colar_excess_per;
				$grand_total_collar_order_qnty+=$plan_cut;
				?>
                </td>
				<?
			}
			?>

            <td align="center"><? echo number_format($color_total_collar,0); ?></td>
            <td align="center"><? echo number_format((($color_total_collar-$color_total_collar_order_qnty)/$color_total_collar_order_qnty)*100,2); ?></td>
            </tr>
            <?
		    }
			?>
            <tr>
                <td>Size Total</td>

                <?
                foreach($nameArray_item_size  as $result_size)
                {
                ?>
                <td style="border:1px solid black;  text-align:center"><? $colar_excess_pers=($size_tatal[$result_size[csf('size_number_id')]]*$colar_excess_percent)/100; echo number_format($size_tatal[$result_size[csf('size_number_id')]]+$colar_excess_pers,0); ?></td>
                <?
                }
                ?>
                <td  style="border:1px solid black;  text-align:center"><?  echo number_format($grand_total_collar,0); ?></td>
                <td align="center" style="border:1px solid black"> <? echo number_format((($grand_total_collar-$grand_total_collar_order_qnty)/$grand_total_collar_order_qnty)*100,2); ?></td>
            </tr>
        </table>
        </td>
        <td width="2%">
        </td>
        <?
        }
		?>

        <?
		$cuff_percent_size_wise_array=return_library_array( "select a.colar_cuff_per,b.gmts_sizes from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b,wo_pre_cost_fabric_cost_dtls c  where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.pre_cost_fabric_cost_dtls_id=c.id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.color_size_table_id    and a.booking_no=$txt_reqsn_no and a.booking_type=1 and c.body_part_id in(3) and a.status_active=1 and a.is_deleted=0", "gmts_sizes", "colar_cuff_per");
		//print_r($cuff_percent_size_wise_array);
		$nameArray_item_size=sql_select( "select min(c.id) as id, b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_reqsn_no  and a.body_part_id=3  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 group by b.item_size,c.size_number_id order by id");

		if(count($nameArray_item_size)>0)
		{
        ?>
        <td width="49%">
        <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
        <tr>
        <td colspan="<? echo count($nameArray_size)+4;?>" align="center"><b>Cuff -  Colour Size Brakedown in Pcs</b></td>
        </tr>
        <tr>
        <td width="70">Size</td>

        <?
		foreach($nameArray_item_size  as $result_size)
		{
		?>
		<td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
		<?
        }
        ?>
        <td rowspan="2" align="center"><strong>Total</strong></td>
        <td width="60" rowspan="2" align="center"><strong>Extra %</strong></td>
        </tr>
        <tr>
        <td>Cuff Size</td>

        <?
        foreach($nameArray_item_size  as $result_item_size)
		{
		?>
		<td align="center" style="border:1px solid black"><strong><? echo $result_item_size[csf('item_size')];?></strong></td>
		<?
        }
        ?>
         <?

			$color_wise_wo_sql=sql_select("select a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method,c.color_number_id,sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_reqsn_no and a.body_part_id=3  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id and  d.status_active=1 and d.is_deleted=0 group by c.color_number_id ,a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method order by a.id
");
		foreach($color_wise_wo_sql as $color_wise_wo_result)
	    {
			$color_total_cuff=0;
			$color_total_cuff_order_qnty=0;
			$process_loss_method=$color_wise_wo_result[csf("process_loss_method")];
			$constrast_color_arr=array();
			if($color_wise_wo_result[csf("color_size_sensitive")]==3)
			{
				$constrast_color=explode('__',$color_wise_wo_result[csf("color_break_down")]);
				for($i=0;$i<count($constrast_color);$i++)
				{
					$constrast_color2=explode('_',$constrast_color[$i]);
					$constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
				}
			}
			//print_r($constrast_color_arr);
		?>
			<tr>
            <td>
            <?
			if($color_wise_wo_result[csf("color_size_sensitive")]==3)
			{
				///echo $color_wise_wo_result[csf('color_number_id')];
				echo strtoupper ($constrast_color_arr[$color_wise_wo_result[csf('color_number_id')]]);
				$lab_dip_color_id=return_field_value("contrast_color_id","wo_pre_cos_fab_co_color_dtls","job_no='".$color_wise_wo_result[csf('job_no')]."' and pre_cost_fabric_cost_dtls_id=".$color_wise_wo_result[csf('id')]." and gmts_color_id=".$color_wise_wo_result[csf('color_number_id')]."");
			}
			else
			{
				echo $color_library[$color_wise_wo_result[csf('color_number_id')]];
				$lab_dip_color_id=$color_wise_wo_result[csf('color_number_id')];
			}
			?>

            </td>
            <?
            foreach($nameArray_item_size  as $result_size)
			{
				?>
				<td align="center" style="border:1px solid black">

				<?
				/*$color_wise_wo_sql_qnty=sql_select("select c.color_number_id,sum(c.order_quantity) as order_quantity, sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_reqsn_no and a.body_part_id=3 and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 and c.color_number_id='".$color_wise_wo_result['color_number_id']."' and c.size_number_id='".$result_size['size_number_id']."'");*/
				$color_wise_wo_sql_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$color_wise_wo_result[csf('color_number_id')]."   and  status_active=1 and is_deleted =0");

				list($plan_cut_qnty)=$color_wise_wo_sql_qnty;
				$plan_cut=$plan_cut_qnty[csf('plan_cut_qnty')];
				$cuff_excess_per=(($plan_cut*2)*$cuff_excess_percent)/100;
				echo number_format($plan_cut*2+$cuff_excess_per,0);
				$color_total_cuff+=$plan_cut*2+$cuff_excess_per;
				$color_total_cuff_order_qnty+=$plan_cut*2;
				$grand_total_cuff+=$plan_cut*2+$cuff_excess_per;
				$grand_total_cuff_order_qnty+=$plan_cut*2;

				/*$cuff_excess_per=(($plan_cut_qnty[csf('plan_cut_qnty')]*2)*$cuff_excess_percent)/100;
				echo number_format($plan_cut_qnty[csf('plan_cut_qnty')]*2+$cuff_excess_per,0);
				$color_total_cuff+=$plan_cut_qnty[csf('plan_cut_qnty')]*2+$cuff_excess_per;
				$color_total_cuff_order_qnty+=$plan_cut_qnty[csf('order_quantity')]*2;
				$grand_total_cuff+=$plan_cut_qnty[csf('plan_cut_qnty')]*2+$cuff_excess_per;
				$grand_total_cuff_order_qnty+=$plan_cut_qnty[csf('order_quantity')]*2;*/

				?>

                </td>
				<?
			}
			?>

            <td align="center"><? echo number_format($color_total_cuff,0); ?></td>
            <td align="center"><? echo number_format((($color_total_cuff-$color_total_cuff_order_qnty)/$color_total_cuff_order_qnty)*100,2); ?></td>
            </tr>
            <?
		    }
			?>
            <tr>
                <td>Size Total</td>

                <?
                foreach($nameArray_item_size  as $result_size)
                {
                   /* $nameArray_size_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =$result_size[size_number_id]  and status_active=1 and is_deleted =0 order by id");
                foreach($nameArray_size_qnty as $result_size_qnty)
                {*/
                ?>
                <td style="border:1px solid black;  text-align:center"><? $cuff_excess_pers=(($size_tatal[$result_size[csf('size_number_id')]]*2)*$cuff_excess_percent)/100; echo number_format($size_tatal[$result_size[csf('size_number_id')]]*2+$cuff_excess_pers,0); ?></td>
                <?
                //}
                }
                ?>
                <td  style="border:1px solid black;  text-align:center"><?  echo number_format($grand_total_cuff,0); ?></td>
                <td align="center" style="border:1px solid black"> <? echo number_format((($grand_total_cuff-$grand_total_cuff_order_qnty)/$grand_total_cuff_order_qnty)*100,2); ?></td>
            </tr>
        </table>
        </td>
        <?
				}
		?>
        </tr>
        </table>
        <br/>
        <br/>
        <table  width="100%"  border="0" cellpadding="0" cellspacing="0" >
        <tr>
        <?

		//echo "select a.colar_cuff_per,b.gmts_sizes from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b,wo_pre_cost_fabric_cost_dtls c  where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.pre_cost_fabric_cost_dtls_id=c.id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.color_size_table_id    and a.booking_no=$txt_reqsn_no and a.booking_type=1 c.body_part_id in(2) and a.status_active=1 and a.is_deleted=0";
		//$colar_percent_size_wise_array=return_library_array( "select a.colar_cuff_per,b.gmts_sizes from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b,wo_pre_cost_fabric_cost_dtls c  where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.pre_cost_fabric_cost_dtls_id=c.id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.color_size_table_id    and a.booking_no=$txt_reqsn_no and a.booking_type=1 and c.body_part_id in(2) and a.status_active=1 and a.is_deleted=0", "gmts_sizes", "colar_cuff_per");


		$colar_percent_size_wise_array=array();
		$colar_tipping_percent_size_wise_sql=sql_select( "select a.colar_cuff_per,b.color_number_id,b.gmts_sizes from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b,wo_pre_cost_fabric_cost_dtls c  where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.pre_cost_fabric_cost_dtls_id=c.id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.color_size_table_id    and a.booking_no=$txt_reqsn_no and a.booking_type=1 and c.body_part_id in(172) and a.status_active=1 and a.is_deleted=0");
		$colar_tipping_excess_percent_arr=array();
		foreach($colar_tipping_percent_size_wise_sql as $colar_percent_size_wise_row)
		{
			$colar_tipping_excess_percent_arr[$colar_percent_size_wise_row[csf('color_number_id')]][$colar_percent_size_wise_row[csf('gmts_sizes')]]=$colar_percent_size_wise_row[csf('colar_cuff_per')];
			//$colar_excess_percent_arr[$colar_percent_size_wise_row[csf('gmts_sizes')]]+=$colar_percent_size_wise_row[csf('colar_cuff_per')];

		}

		//print_r($colar_tipping_excess_percent_arr);
		$nameArray_item_size=sql_select( "select min(c.id) as id,b.item_size,c.size_number_id,	min(c.size_order) as size_order FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_reqsn_no  and a.body_part_id=172  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id  and d.status_active=1 and d.is_deleted=0 group by b.item_size,c.size_number_id order by size_order");


		if(count($nameArray_item_size)>0)
		{
        ?>
        <td width="49%">
        <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
        <tr>
        <td colspan="<? echo count($nameArray_size)+4;?>" align="center"><b>Collar Tipping -  Colour Size Brakedown in Pcs</b></td>
        </tr>
        <tr>
        <td width="70">Size</td>

        <?

		/*$nameArray_item_size=sql_select( "select b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_reqsn_no  and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 group by c.size_number_id,b.item_size order by c.size_number_id");*/
		foreach($nameArray_item_size  as $result_size)
		{
		?>
		<td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
		<?
        }
        ?>
        <td rowspan="2" align="center"><strong>Total</strong></td>
        <td width="60" rowspan="2" align="center"><strong>Extra %</strong></td>
        </tr>
        <tr>
        <td>Collar Size</td>

        <?
        foreach($nameArray_item_size  as $result_item_size)
		{
		?>
		<td align="center" style="border:1px solid black"><strong><? echo $result_item_size[csf('item_size')];?></strong></td>
		<?
        }

		$color_wise_wo_sql=sql_select("select a.id, a.job_no, a.color_size_sensitive, a.color_break_down, a.process_loss_method, c.color_number_id , c.color_order, sum(c.plan_cut_qnty) as plan_cut_qnty
		FROM wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c, wo_booking_dtls d
		WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_reqsn_no and a.body_part_id=172 and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0
		group by c.color_number_id, c.color_order, a.id, a.job_no, a.color_size_sensitive,a.color_break_down, a.process_loss_method
		order by c.color_order ");

		foreach($color_wise_wo_sql as $color_wise_wo_result)
	    {
			$color_total_collar=0;
			$color_total_collar_order_qnty=0;
			$process_loss_method=$color_wise_wo_result[csf("process_loss_method")];
			$constrast_color_arr=array();
			if($color_wise_wo_result[csf("color_size_sensitive")]==3)
			{
				$constrast_color=explode('__',$color_wise_wo_result[csf("color_break_down")]);
				for($i=0;$i<count($constrast_color);$i++)
				{
					$constrast_color2=explode('_',$constrast_color[$i]);
					$constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
				}
			}
		?>
			<tr>
            <td>
            <?
			if($color_wise_wo_result[csf("color_size_sensitive")]==3)
			{
				echo strtoupper ($constrast_color_arr[$color_wise_wo_result[csf('color_number_id')]]) ;
				$lab_dip_color_id=return_field_value("contrast_color_id","wo_pre_cos_fab_co_color_dtls","job_no='".$color_wise_wo_result[csf('job_no')]."' and pre_cost_fabric_cost_dtls_id=".$color_wise_wo_result[csf('id')]." and gmts_color_id=".$color_wise_wo_result[csf('color_number_id')]."");
			}
			else
			{
				echo $color_library[$color_wise_wo_result[csf('color_number_id')]];
				$lab_dip_color_id=$color_wise_wo_result[csf('color_number_id')];
			}
			?>

            </td>
            <?
            foreach($nameArray_item_size  as $result_size)
			{
				?>
				<td align="center" style="border:1px solid black">
				<?

				$color_wise_wo_sql_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$color_wise_wo_result[csf('color_number_id')]."   and  status_active=1 and is_deleted =0 ");




				list($plan_cut_qnty)=$color_wise_wo_sql_qnty;
				$plan_cut=$plan_cut_qnty[csf('plan_cut_qnty')];
				$colar_tipping_excess_per=($plan_cut*$colar_tipping_excess_percent_arr[$color_wise_wo_result[csf('color_number_id')]][$result_size[csf('size_number_id')]])/100;
				echo number_format($plan_cut+$colar_tipping_excess_per,0);
				$collar_tiff_size_total_arr[$result_size[csf('size_number_id')]]+=number_format($plan_cut+$colar_tipping_excess_per,0,'','');
				$color_tipping_total_collar+=$plan_cut+$colar_tipping_excess_per;
				$color_tipping_total_collar_order_qnty+=$plan_cut;
				$grand_total_collar_tipping+=$plan_cut+$colar_tipping_excess_per;
				$grand_total_collar_tipping_order_qnty+=$plan_cut;



				?>
                </td>
				<?
			}
			?>

            <td align="center"><? echo number_format($color_tipping_total_collar,0); ?></td>
            <td align="center"><? echo number_format((($color_tipping_total_collar-$color_tipping_total_collar_order_qnty)/$color_tipping_total_collar_order_qnty)*100,2); ?></td>
            </tr>
            <?
		    }
			?>
            <tr>
                <td>Size Total</td>

                <?
                foreach($nameArray_item_size  as $result_size)
                {
                ?>
                <td style="border:1px solid black;  text-align:center">
				<?
				//$colar_excess_pers=($size_tatal[$result_size[csf('size_number_id')]]*$colar_excess_percent)/100; echo number_format($size_tatal[$result_size[csf('size_number_id')]]+$colar_excess_pers,0);
				echo number_format($collar_tiff_size_total_arr[$result_size[csf('size_number_id')]],0);

				?></td>
                <?
                }
                ?>
                <td  style="border:1px solid black;  text-align:center"><?  echo number_format($grand_total_collar_tipping,0); ?></td>
                <td align="center" style="border:1px solid black"> <? echo number_format((($grand_total_collar_tipping-$grand_total_collar_tipping_order_qnty)/$grand_total_collar_tipping_order_qnty)*100,2); ?></td>
            </tr>
        </table>
        </td>
        <td width="2%">
        </td>
        <?
        }

		$cuff_tipping_percent_size_wise_array=$cuff_size_total=array();
		$cuff_tipping_percent_size_wise_sql=sql_select( "select a.colar_cuff_per,b.color_number_id,b.gmts_sizes from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b,wo_pre_cost_fabric_cost_dtls c  where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.pre_cost_fabric_cost_dtls_id=c.id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.color_size_table_id  and a.booking_no=$txt_reqsn_no and a.booking_type=1 and c.body_part_id in(214) and a.status_active=1 and a.is_deleted=0");
		foreach($cuff_tipping_percent_size_wise_sql as $cuff_percent_size_wise_row)
		{
			$cuff_tipping_percent_size_wise_array[$cuff_percent_size_wise_row[csf('color_number_id')]][$cuff_percent_size_wise_row[csf('gmts_sizes')]]=$cuff_percent_size_wise_row[csf('colar_cuff_per')];
		}


		$nameArray_item_size=sql_select( "select min(c.id) as id, b.item_size,c.size_number_id,	min(c.size_order) as size_order FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_reqsn_no  and a.body_part_id=214  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 group by b.item_size,c.size_number_id order by size_order");


		if(count($nameArray_item_size)>0)
		{
        ?>
        <td width="49%">
        <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
        <tr>
        <td colspan="<? echo count($nameArray_size)+4;?>" align="center"><b>Cuff Tipping-  Colour Size Brakedown in Pcs</b></td>
        </tr>
        <tr>
        <td width="70">Size</td>

        <?
		foreach($nameArray_item_size  as $result_size)
		{
		?>
		<td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
		<?
        }
        ?>
        <td rowspan="2" align="center"><strong>Total</strong></td>
        <td width="60" rowspan="2" align="center"><strong>Extra %</strong></td>
        </tr>
        <tr>
        <td>Cuff Size</td>

        <?
        foreach($nameArray_item_size  as $result_item_size)
		{
		?>
		<td align="center" style="border:1px solid black"><strong><? echo $result_item_size[csf('item_size')];?></strong></td>
		<?
        }
        ?>
         <?

			$color_wise_wo_sql=sql_select("select a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method,c.color_number_id, c.color_order, sum(c.plan_cut_qnty) as plan_cut_qnty
			FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d
			WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_reqsn_no and a.body_part_id=214  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id and  d.status_active=1 and d.is_deleted=0
			group by c.color_number_id, c.color_order ,a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method
			order by c.color_order");
		foreach($color_wise_wo_sql as $color_wise_wo_result)
	    {
			$color_total_cuff=0;
			$color_total_cuff_order_qnty=0;
			$process_loss_method=$color_wise_wo_result[csf("process_loss_method")];
			$constrast_color_arr=array();
			if($color_wise_wo_result[csf("color_size_sensitive")]==3)
			{
				$constrast_color=explode('__',$color_wise_wo_result[csf("color_break_down")]);
				for($i=0;$i<count($constrast_color);$i++)
				{
					$constrast_color2=explode('_',$constrast_color[$i]);
					$constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
				}
			}
		?>
			<tr>
            <td>
            <?
			if($color_wise_wo_result[csf("color_size_sensitive")]==3)
			{
				echo strtoupper ($constrast_color_arr[$color_wise_wo_result[csf('color_number_id')]]);
				$lab_dip_color_id=return_field_value("contrast_color_id","wo_pre_cos_fab_co_color_dtls","job_no='".$color_wise_wo_result[csf('job_no')]."' and pre_cost_fabric_cost_dtls_id=".$color_wise_wo_result[csf('id')]." and gmts_color_id=".$color_wise_wo_result[csf('color_number_id')]."");
			}
			else
			{
				echo $color_library[$color_wise_wo_result[csf('color_number_id')]];
				$lab_dip_color_id=$color_wise_wo_result[csf('color_number_id')];
			}
			?>

            </td>
            <?
            foreach($nameArray_item_size  as $result_size)
			{
				?>
				<td align="center" style="border:1px solid black">

				<?


				$color_wise_wo_sql_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$color_wise_wo_result[csf('color_number_id')]."   and  status_active=1 and is_deleted =0");



				list($plan_cut_qnty)=$color_wise_wo_sql_qnty;
				$plan_cut=$plan_cut_qnty[csf('plan_cut_qnty')];
				$cuff_tipping_excess_per=(($plan_cut*2)*$cuff_tipping_percent_size_wise_array[$color_wise_wo_result[csf('color_number_id')]][$result_size[csf('size_number_id')]])/100;
				echo number_format($plan_cut*2+$cuff_tipping_excess_per,0);
				$cuff_tiffing_size_total[$result_size[csf('size_number_id')]]+=number_format($plan_cut*2+$cuff_tipping_excess_per,0,'','');
				//echo $cuff_tipping_percent_size_wise_array[$color_wise_wo_result[csf('color_number_id')]][$result_size[csf('size_number_id')]];
				$color_total_cuff_tiffing+=$plan_cut*2+$cuff_tipping_excess_per;
				$color_total_cuff_tiffing_order_qnty+=$plan_cut*2;
				$grand_total_cuff_tiffing+=$plan_cut*2+$cuff_tipping_excess_per;
				$grand_total_cuff_tiffing_order_qnty+=$plan_cut*2;
				?>

                </td>
				<?
			}
			?>

            <td align="center"><? echo number_format($color_total_cuff_tiffing,0); ?></td>
            <td align="center"><? echo number_format((($color_total_cuff_tiffing-$color_total_cuff_tiffing_order_qnty)/$color_total_cuff_tiffing_order_qnty)*100,2);  ?></td>
            </tr>
            <?
		    }
			?>
            <tr>
                <td>Size Total</td>

                <?
                foreach($nameArray_item_size  as $result_size)
                {
                   /* $nameArray_size_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =$result_size[size_number_id]  and status_active=1 and is_deleted =0 order by id");
                foreach($nameArray_size_qnty as $result_size_qnty)
                {*/
                ?>
                <td style="border:1px solid black;  text-align:center">
				<?
				//$cuff_excess_pers=(($size_tatal[$result_size[csf('size_number_id')]]*2)*$cuff_excess_percent)/100; echo number_format($size_tatal[$result_size[csf('size_number_id')]]*2+$cuff_excess_pers,0);
				echo number_format($cuff_tiffing_size_total[$result_size[csf('size_number_id')]],0);
				?></td>
                <?
                //}
                }
                ?>
                <td  style="border:1px solid black;  text-align:center"><?  echo number_format($grand_total_cuff_tiffing,0); ?></td>
                <td align="center" style="border:1px solid black"> <? echo number_format((($grand_total_cuff_tiffing-$grand_total_cuff_tiffing_order_qnty)/$grand_total_cuff_tiffing_order_qnty)*100,2); ?></td>
            </tr>
        </table>
        </td>
        <?
				}
		?>
        </tr>
        </table>
        <br/>

        <table  width="100%"  border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td width="49%" style="border:solid; border-color:#000; border-width:thin" valign="top">
                    <?php echo get_spacial_instruction($txt_reqsn_no); ?>
                </td>
                <td width="2%">
                </td>
                <td width="49%" valign="top">
                    <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                        <tr align="center">
                        <td><b>Approved Instructions</b></td>

                        </tr>
                        <tr>
                        <td>
                    <?  echo $nameArray_approved_comments_row[csf('comments')];  ?>
                    </td>
                    </tr>
                    </table>
                	<br />

                </td>

            </tr>
        </table>
       <br>

  <?
       //------------------------------ Query for TNA start-----------------------------------
				$po_id_all=str_replace("'","",$txt_order_no_id);
				$po_num_arr=return_library_array("select id,po_number from wo_po_break_down where id in($po_id_all)",'id','po_number');
				$tna_start_sql=sql_select( "select id,po_number_id,
								(case when task_number=31 then task_start_date else null end) as fab_booking_start_date,
								(case when task_number=31 then task_finish_date else null end) as fab_booking_end_date,
								(case when task_number=60 then task_start_date else null end) as knitting_start_date,
								(case when task_number=60 then task_finish_date else null end) as knitting_end_date,
								(case when task_number=61 then task_start_date else null end) as dying_start_date,
								(case when task_number=61 then task_finish_date else null end) as dying_end_date,
								(case when task_number=73 then task_start_date else null end) as finishing_start_date,
								(case when task_number=73 then task_finish_date else null end) as finishing_end_date,
								(case when task_number=84 then task_start_date else null end) as cutting_start_date,
								(case when task_number=84 then task_finish_date else null end) as cutting_end_date,
								(case when task_number=86 then task_start_date else null end) as sewing_start_date,
								(case when task_number=86 then task_finish_date else null end) as sewing_end_date,
								(case when task_number=110 then task_start_date else null end) as exfact_start_date,
								(case when task_number=110 then task_finish_date else null end) as exfact_end_date,
								(case when task_number=47 then task_start_date else null end) as yarn_rec_start_date,
								(case when task_number=47 then task_finish_date else null end) as yarn_rec_end_date
								from tna_process_mst
								where status_active=1 and po_number_id in($po_id_all)");
				$tna_fab_start=$tna_knit_start=$tna_dyeing_start=$tna_fin_start=$tna_cut_start=$tna_sewin_start=$tna_exfact_start="";
				$tna_date_task_arr=array();
				foreach($tna_start_sql as $row)
				{
					if($row[csf("fab_booking_start_date")]!="" && $row[csf("fab_booking_start_date")]!="0000-00-00")
					{
						if($tna_fab_start=="")
						{
							$tna_fab_start=$row[csf("fab_booking_start_date")];
						}
					}


					if($row[csf("knitting_start_date")]!="" && $row[csf("knitting_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['knitting_start_date']=$row[csf("knitting_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['knitting_end_date']=$row[csf("knitting_end_date")];
					}
					if($row[csf("dying_start_date")]!="" && $row[csf("dying_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['dying_start_date']=$row[csf("dying_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['dying_end_date']=$row[csf("dying_end_date")];
					}
					if($row[csf("finishing_start_date")]!="" && $row[csf("finishing_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['finishing_start_date']=$row[csf("finishing_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['finishing_end_date']=$row[csf("finishing_end_date")];
					}
					if($row[csf("cutting_start_date")]!="" && $row[csf("cutting_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['cutting_start_date']=$row[csf("cutting_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['cutting_end_date']=$row[csf("cutting_end_date")];
					}
					if($row[csf("sewing_start_date")]!="" && $row[csf("sewing_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['sewing_start_date']=$row[csf("sewing_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['sewing_end_date']=$row[csf("sewing_end_date")];
					}
					if($row[csf("exfact_start_date")]!="" && $row[csf("exfact_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['exfact_start_date']=$row[csf("exfact_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['exfact_end_date']=$row[csf("exfact_end_date")];
					}
					if($row[csf("yarn_rec_start_date")]!="" && $row[csf("yarn_rec_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['yarn_rec_start_date']=$row[csf("yarn_rec_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['yarn_rec_end_date']=$row[csf("yarn_rec_end_date")];
					}
				}

	//------------------------------ Query for TNA end-----------------------------------
	?>

    <fieldset id="div_size_color_matrix" style="max-width:1000; display:none">
        <legend>TNA Information</legend>
        <!--<span style="font-size:180; font-weight:bold;"></span>-->
        <table width="100%" style="border:1px solid black;font-size:12px" border="1" cellpadding="2" cellspacing="0" rules="all">
            <tr>
            	<td rowspan="2" align="center" valign="top">SL</td>
            	<td width="180" rowspan="2"  align="center" valign="top"><b>Order No</b></td>
                <td colspan="2" align="center" valign="top"><b>Yarn Receive</b></td>
                <td colspan="2" align="center" valign="top"><b>Knitting</b></td>
                <td colspan="2" align="center" valign="top"><b>Dyeing</b></td>
                <td colspan="2" align="center" valign="top"><b>Finishing Fabric</b></td>
                <td colspan="2" align="center" valign="top"><b>Cutting </b></td>
                <td colspan="2" align="center" valign="top"><b>Sewing </b></td>
                <td colspan="2"  align="center" valign="top"><b>Ex-factory </b></td>
            </tr>
            <tr>
            	<td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>
                <td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>
                <td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>
                <td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>
                <td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>
                <td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>
                <td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>

            </tr>
            <?
			$i=1;
			foreach($tna_date_task_arr as $order_id=>$row)
			{
				 //$tna_date_task_arr//knitting_start_date dying_start_date finishing_start_date cutting_start_date sewing_start_date exfact_start_date
				?>
                <tr>
                	<td><? echo $i; ?></td>
                    <td><? echo $po_num_arr[$order_id]; ?></td>
                    <td align="center"><? echo change_date_format($row['yarn_rec_start_date']); ?></td>
                    <td  align="center"><? echo change_date_format($row['yarn_rec_end_date']); ?></td>
                	<td align="center"><? echo change_date_format($row['knitting_start_date']); ?></td>
                    <td  align="center"><? echo change_date_format($row['knitting_end_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['dying_start_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['dying_end_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['finishing_start_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['finishing_end_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['cutting_start_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['cutting_end_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['sewing_start_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['sewing_end_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['exfact_start_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['exfact_end_date']); ?></td>
                </tr>
                <?
				$i++;
			}
			?>

        </table>
        </fieldset>



        <?
		}// fabric Source End
		?>
        <br/>
        <?
		//echo "select responsible_dept,	responsible_person,	reason from wo_booking_dtls where booking_no =$txt_reqsn_no";
		//"select id,department_name from  lib_department where status_active=1 and is_deleted=0 order by  department_name"
		$department_name_library=return_library_array( "select id,department_name from  lib_department where status_active=1 and is_deleted=0 order by  department_name", "id", "department_name"  );

		//$sql_responsible= sql_select("select responsible_dept,	responsible_person,	reason from wo_booking_dtls where booking_no =$txt_reqsn_no and fin_fab_qnty>0");
		/*echo "SELECT  a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width,avg(b.process_loss_percent) as process_loss_percent,avg(rmg_qty) as rmg_qty, b.responsible_dept,	b.responsible_person,	b.reason FROM wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls b where b.booking_no =$txt_reqsn_no and a.id=b.pre_cost_fabric_cost_dtls_id  and b.status_active=1 and
b.is_deleted=0 and b.grey_fab_qnty>0 group by a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width,b.responsible_dept,	b.responsible_person,	b.reason order by a.body_part_id";*/
		$sql_responsible= sql_select("SELECT  a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,a.width_dia_type,b.dia_width,avg(b.process_loss_percent) as process_loss_percent,avg(rmg_qty) as rmg_qty, b.responsible_dept,	b.responsible_person,	b.reason FROM wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls b where b.booking_no =$txt_reqsn_no and a.id=b.pre_cost_fabric_cost_dtls_id  and b.status_active=1 and
b.is_deleted=0 and b.grey_fab_qnty>0 group by a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,a.width_dia_type,b.dia_width,b.responsible_dept,	b.responsible_person,	b.reason order by a.body_part_id");
		if(count($sql_responsible)>0)
		{
		?>
         <table width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
         <tr>
         <td>
          #
         </td>
          <td>
         Fabric Description
         </td>
          <td>
         Responsible Dept.
         </td>
         <td>
         Responsible person
         </td>
         <td>
         Reason
         </td>
         </tr>
         <?
		 $ir=1;
		foreach($sql_responsible as $sql_responsible_row)
		{
			?>
             <tr>
             <td>
             <?  echo $ir; ?>
             </td>
             <td>
             <?  echo $body_part[$sql_responsible_row[csf('body_part_id')]].','.$color_type[$sql_responsible_row[csf('color_type_id')]].','.$sql_responsible_row[csf('construction')].','.$sql_responsible_row[csf('composition')].','.$sql_responsible_row[csf('gsm_weight')].','.$sql_responsible_row[csf('dia_width')].','.$fabric_typee[$sql_responsible_row[csf('width_dia_type')]]; ?>
             </td>
              <td>
             <?
			 $responsible_dept_st="";
			 $responsible_dept_arr=explode( ",",$sql_responsible_row[csf('responsible_dept')]);
			 foreach($responsible_dept_arr as $key => $value)
			 {
				 $responsible_dept_st.= $department_name_library[$value].", ";
			 }
			 echo rtrim($responsible_dept_st,", ");
			 ?>
             </td>
             <td>
            <?  echo $sql_responsible_row[csf('responsible_person')]; ?>
             </td>
             <td>
              <?  echo $sql_responsible_row[csf('reason')]; ?>
             </td>
             </tr>
            <?
			$ir++;

		}
		 ?>
         </table>
         <?
		}
		 ?>

         <!--<br><br><br><br>-->
         <?
		 	echo signature_table(4, $cbo_company_name, "1330px");
			echo "****".custom_file_name($txt_reqsn_no,$style_sting,$txt_job_no);
		 ?>

       </div>
       <?

}

if($action=="show_fabric_requisition_report3")//unused
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$report_type=str_replace("'","",$report_type);
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	//$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	$po_qnty_tot1=return_field_value( "sum(po_quantity)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");

	$po_number_arr=return_library_array( "select id,po_number from   wo_po_break_down",'id','po_number');
	$po_ship_date_arr=return_library_array( "select id,pub_shipment_date from   wo_po_break_down ",'id','pub_shipment_date');
	?>
	<div style="width:1330px" align="center">
    <?php
$nameArray_approved = sql_select("select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_reqsn_no and b.entry_form=12");
list($nameArray_approved_row) = $nameArray_approved;
$nameArray_approved_date = sql_select("select b.approved_date as approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_reqsn_no and b.entry_form=12 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "'");
list($nameArray_approved_date_row) = $nameArray_approved_date;
$nameArray_approved_comments = sql_select("select b.comments as comments from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_reqsn_no and b.entry_form=12 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "'");
list($nameArray_approved_comments_row) = $nameArray_approved_comments;
$path = str_replace("'", "", $path);
if ($path == "") {
	$path = '../../';
}
?>										<!--    Header Company Information         -->
       <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black" >
           <tr>
               <td width="100">
               <?
               if($report_type==1)
			   {
			   ?>
               <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               <?
			   }
			   else if($report_type==1)
			   {
			   ?>
               <img  src='../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               <?
			   }
			   else
			   {
			   ?>
               <img  src='<? echo $path.$imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               <?
			   }
			   ?>
               </td>
               <td width="1250">
                    <table width="100%" cellpadding="0" cellspacing="0"  >
                        <tr>
                            <td align="center" style="font-size:20px;">
                              <?php
echo $company_library[$cbo_company_name];
?>
                            </td>
                            <td rowspan="3"  width="270">

                                <!--<a href="requires/fabric_booking_controller.php?filename=welcome.html&action=download_file" style="text-transform:none">Download</a>-->
                               <span style="font-size:15px"><b> Job No:&nbsp;&nbsp;<? echo trim($txt_job_no,"'"); ?></b></span>
                               <br>
                               <?
								 if($nameArray_approved_row[csf('approved_no')]>1)
								 {
								 ?>
								 <b> Revised No: <? echo $nameArray_approved_row[csf('approved_no')]-1; ?></b>
                                  <br/>
								 <b> Approved Date: <? echo $nameArray_approved_date_row[csf('approved_date')]; ?>
								  <?
								 }
							  	?>
                            	<b>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px">
                            <?
                            $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
                            foreach ($nameArray as $result)
                            {
                            ?>
                                Plot No: <? echo $result[csf('plot_no')]; ?>
                                Level No: <? echo $result[csf('level_no')]?>
                                Road No: <? echo $result[csf('road_no')]; ?>
                                Block No: <? echo $result[csf('block_no')];?>
                                City No: <? echo $result[csf('city')];?>
                                Zip Code: <? echo $result[csf('zip_code')]; ?>
                                Province No: <?php echo $result[csf('province')]; ?>
                                Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
                                Email Address: <? echo $result[csf('email')];?>
                                Website No: <? echo $result[csf('website')];
                            }
                            ?>
                               </td>
                            </tr>
                            <tr>
                            <td align="center" style="font-size:20px">
                                <strong><? if($report_type==1) echo str_replace("'","",$report_title);else echo 'Short Fabric Booking';?> &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:#F00"><? if(str_replace("'","",$id_approved_id) ==1){ echo "(Approved)";}else{echo "";}; ?> </font></strong>
                             </td>
                              <td>

                             </td>
                            </tr>
                      </table>
                </td>
            </tr>
       </table>
                <?

				$job_no='';
				$total_set_qnty=0;
				$colar_excess_percent=0;
				$cuff_excess_percent=0;
                $nameArray=sql_select( "select a.booking_no,a.booking_date,a.supplier_id,a.pay_mode,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.po_break_down_id,a.colar_excess_percent,a.cuff_excess_percent,a.delivery_date,a.is_apply_last_update,a.fabric_source,b.job_no,b.buyer_name, b.style_ref_no ,b.gmts_item_id,b.order_uom,b.total_set_qnty,b.style_description,b.season,b.product_dept,b.product_code,b.pro_sub_dep,b.dealing_marchant from wo_booking_mst a, wo_po_details_master b where  a.job_no=b.job_no and a.booking_no=$txt_reqsn_no");
				foreach ($nameArray as $result)
				{
					$total_set_qnty=$result[csf('total_set_qnty')];
					$colar_excess_percent=$result[csf('colar_excess_percent')];
				    $cuff_excess_percent=$result[csf('cuff_excess_percent')];
					$po_no="";
					$shipment_date="";$internal_ref="";	$file_no="";
					$sql_po= "select po_number,grouping,file_no,MIN(pub_shipment_date) pub_shipment_date from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].") group by po_number,grouping,file_no";
					$data_array_po=sql_select($sql_po);
					foreach ($data_array_po as $row_po)
					{
						$po_no.=$row_po[csf('po_number')].", ";
						$shipment_date.=change_date_format($row_po[csf('pub_shipment_date')],'dd-mm-yyyy','-').", ";
						$internal_ref.=$row_po[csf('grouping')].", ";
						$file_no.=$row_po[csf('file_no')].", ";
					}

					$lead_time="";
					if($db_type==0)
					{
					$sql_lead_time= "select DATEDIFF(pub_shipment_date,po_received_date) date_diff from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")";
					}

					if($db_type==2)
					{
					$sql_lead_time= "select (pub_shipment_date-po_received_date) date_diff from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")";
					}
					$data_array_lead_time=sql_select($sql_lead_time);
					foreach ($data_array_lead_time as $row_lead_time)
					{
						$lead_time.=$row_lead_time[csf('date_diff')].",";
						//$shipment_date.=change_date_format($row_po['pub_shipment_date'],'dd-mm-yyyy','-').",";
					}

					$po_received_date="";
					$sql_po_received_date= "select MIN(po_received_date) as po_received_date from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")";
					$data_array_po_received_date=sql_select($sql_po_received_date);
					foreach ($data_array_po_received_date as $row_po_received_date)
					{
						$po_received_date=change_date_format($row_po_received_date[csf('po_received_date')],'dd-mm-yyyy','-');
						//$shipment_date.=change_date_format($row_po['pub_shipment_date'],'dd-mm-yyyy','-').",";
					}
					$file=rtrim($file_no,", ");//rtrim($po_no,", ")
					$file_all=array_unique(explode(",",$file));

					$file='';
					foreach($file_all as $file_id)
					{
						if($file=="") $file_cond=$file_id; else $file_cond.=", ".$file_id;
					}

				?>
       <table width="100%" style="border:1px solid black" >
            <tr>
                <td colspan="6" valign="top" style="font-size:18px; color:#F00"><? if($result[csf('is_apply_last_update')]==2){echo "Booking Info not synchronized with order entry and pre-costing. order entry or pre-costing has updated after booking entry.  Contact to ".$marchentrArr[$result[csf('dealing_marchant')]]; } else{ echo "";} ?></td>
            </tr>
            <tr>
                <td width="100"><span style="font-size:18px"><b>Buyer/Agent Name</b></span></td>
                <td width="110">:&nbsp;<span style="font-size:18px"><b><? echo $buyer_name_arr[$result[csf('buyer_name')]]; ?></b></span></td>
                <td width="100"><span style="font-size:12px"><b>Dept.</b></span></td>
                <td width="110">:&nbsp;<? echo $product_dept[$result[csf('product_dept')]] ; if($result[csf('product_code')] !=""){ echo " (".$result[csf('product_code')].")";} if($result[csf('pro_sub_dep')] !=0){ echo " (".$pro_sub_dept_array[$result[csf('pro_sub_dep')]].")";}?></td>
                <td width="100"><span style="font-size:12px"><b>Order Qnty</b></span></td>
                <td width="110">:&nbsp;
				<?  echo $po_qnty_tot1." ".$unit_of_measurement[$result[csf('order_uom')]] ; ?>
                </td>
            </tr>
            <tr>

                <td width="100" style="font-size:12px"><b>Garments Item</b></td>
                <td width="110">:&nbsp;
				<?
				$gmts_item_name="";
				$gmts_item=explode(',',$result[csf('gmts_item_id')]);
				for($g=0;$g<=count($gmts_item); $g++)
				{
					$gmts_item_name.= $garments_item[$gmts_item[$g]].",";
				}
				echo rtrim($gmts_item_name,',');
				?>
                </td>
                <td width="100" style="font-size:12px"><b>Requisition Date</b></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('booking_date')],'dd-mm-yyyy','-');?>&nbsp;&nbsp;&nbsp;</td>
                <td width="100" style="font-size:18px"><b>Style Ref.</b>   </td>
                <td width="110" style="font-size:18px">:&nbsp;<b><? echo $style_sting=$result[csf('style_ref_no')];?> </b>   </td>

            </tr>
             <tr>



                <td  width="100" style="font-size:12px"><b>Style Des.</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('style_description')]; $job_no= $result[csf('job_no')];?></td>
                <td width="100" style="font-size:12px"><b>Lead Time </b>   </td>
                <td width="110">:&nbsp;<?  echo rtrim($lead_time,",");;?> </td>
                <td width="100" style="font-size:12px"><b>Dealing Merchant</b></td>
                <td width="110">:&nbsp;<? echo $marchentrArr[$result[csf('dealing_marchant')]]; ?></td>



            </tr>

            <tr>
                <td width="100" style="font-size:12px"><b>Supplier Name</b>   </td>
                <td width="110">:&nbsp;<?
				if($result[csf('pay_mode')]==5 || $result[csf('pay_mode')]==3){
					echo $company_library[$result[csf('supplier_id')]];
					}
					else{
					echo $supplier_name_arr[$result[csf('supplier_id')]];
					}
				//echo $supplier_name_arr[$result[csf('supplier_id')]];?>    </td>
                <td width="100" style="font-size:12px"><b>Delivery Date</b></td>
               	<td width="110">:&nbsp;<? echo change_date_format( $result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>
                <td width="100" style="font-size:18px"><b>Booking No </b>   </td>
                <td width="110" style="font-size:18px">:&nbsp;<b><? echo $result[csf('booking_no')];?></b><? echo "(".$fabric_source[$result[csf('fabric_source')]].")"?></td>



            </tr>
            <tr>
                <td width="100" style="font-size:12px"><b>Season</b></td>
                <td width="110">:&nbsp;<? echo $result[csf('season')]; ?></td>
                <td  width="100" style="font-size:12px"><b>Attention</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('attention')]; ?></td>
                <td  width="100" style="font-size:12px"><b>Po Received Date</b></td>
                <td  width="110" >:&nbsp;<? echo $po_received_date; ?></td>



            </tr>
           <tr>
               <td width="100" style="font-size:18px"><b>Order No</b></td>
                <td width="110" style="font-size:18px" colspan="3">:&nbsp;<b><? echo rtrim($po_no,", "); ?></b></td>
                <td width="100" style="font-size:17px"><b>Internal Ref</b></td>
                <td width="100" style="font-size:16px">:&nbsp;<b><? echo rtrim($internal_ref,", "); ?></b></td>

            </tr>
            <tr>
               <td width="100" style="font-size:12px"><b>Shipment Date</b></td>
               <td width="110" colspan="3"> :&nbsp;<? echo rtrim($shipment_date,", "); ?></td>
               <td width="100" style="font-size:17px"><b>File No</b></td>
               <td width="100" style="font-size:16px"> :&nbsp;<b><? echo $file_cond; ?></b></td>

            </tr>

        </table>
           <?
			}
		   ?>
          <br/>     									 <!--  Here will be the main portion  -->

     <?
	 $costing_per="";
	 $costing_per_qnty=0;
	 $costing_per_id=return_field_value( "costing_per", "wo_pre_cost_mst","job_no ='$job_no'");
	 if($costing_per_id==1)
			{
				$costing_per="1 Dzn";
				$costing_per_qnty=12;

			}
			if($costing_per_id==2)
			{
				$costing_per="1 Pcs";
				$costing_per_qnty=1;

			}
			if($costing_per_id==3)
			{
				$costing_per="2 Dzn";
				$costing_per_qnty=24;

			}
			if($costing_per_id==4)
			{
				$costing_per="3 Dzn";
				$costing_per_qnty=36;

			}
			if($costing_per_id==5)
			{
				$costing_per="4 Dzn";
				$costing_per_qnty=48;

			}
			$process_loss_method=return_field_value( "process_loss_method", "wo_pre_cost_fabric_cost_dtls","job_no='$job_no'");;

	 ?>

     <?
	 if(str_replace("'","",$cbo_fabric_source)==1)
	  {
	$nameArray_fabric_description= sql_select("SELECT  a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width, avg(b.process_loss_percent) as process_loss_percent,avg(rmg_qty) as rmg_qty FROM wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls b where b.booking_no =$txt_reqsn_no and a.id=b.pre_cost_fabric_cost_dtls_id  and b.status_active=1 and
b.is_deleted=0  group by a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width order by a.body_part_id,dia_width");


	 ?>

     <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
     <tr align="center">
     <th colspan="5" align="left">Body Part</th>
        <?
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('body_part_id')] == "")  echo "<td  colspan='2'>&nbsp</td>";
			else         		               echo "<td  colspan='2'>". $body_part[$result_fabric_description[csf('body_part_id')]]."</td>";
		}
		?>
        <td  rowspan="9" width="50"><p>Total  Finish Fabric <? if(trim($cbo_fabric_natu ,"'")==2){echo "(KG)";}else{echo "(Yds)";} ?></p></td>
        <td  rowspan="9" width="50"><p>Total Grey Fabric <? if(trim($cbo_fabric_natu ,"'")==2){echo "(KG)";}else{echo "(Yds)";} ?></p></td>
        <td  rowspan="9" width="50"><p>Process Loss % </p></td>
       </tr>
     <tr align="center"><th colspan="5" align="left">Color Type</th>
        <?
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('color_type_id')] == "")  echo "<td  colspan='2'>&nbsp</td>";
			else         		               echo "<td  colspan='2'>". $color_type[$result_fabric_description[csf('color_type_id')]]."</td>";
		}
		?>
        <!--<td  rowspan="8" width="50"><p>Total  Finish Fabric (KG)</p></td> <td  rowspan="8" width="50"><p>Total Grey Fabric (KG)</p></td>-->
             <!--<td  rowspan="7" width="50"><p>Process Loss % </p></td>-->
       </tr>
        <tr align="center"><th colspan="5" align="left">Fabric Construction</th>
        <?
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('construction')] == "")  echo "<td  colspan='2'>&nbsp</td>";
			else         		               echo "<td  colspan='2'>". $result_fabric_description[csf('construction')]."</td>";
		}
		?>


       </tr>
        <tr align="center"><th   colspan="5" align="left">Fabric Composition</th>
        <?
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('composition')] == "")   echo "<td colspan='2' >&nbsp</td>";
			else         		               echo "<td colspan='2' >".$result_fabric_description[csf('composition')]."</td>";
		}
		?>

       </tr>
       <tr align="center"><th  colspan="5" align="left">GSM</th>
        <?
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('gsm_weight')] == "")   echo "<td colspan='2'>&nbsp</td>";
			else         		       echo "<td colspan='2' align='center'>". $result_fabric_description[csf('gsm_weight')]."</td>";
		}
		?>

       </tr>
       <tr align="center"><th   colspan="5" align="left">Dia/Width (Inch)</th>
        <?
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('dia_width')] == "")   echo "<td colspan='2'>&nbsp</td>";
			else         		              echo "<td colspan='2' align='center'>".$result_fabric_description[csf('dia_width')]."</td>";
		}
		?>

       </tr>
       <tr align="center"><th   colspan="5" align="left">RMG Qty</th>
        <?
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('rmg_qty')] == "")   echo "<td colspan='2'>&nbsp</td>";
			else         		              echo "<td colspan='2' align='center'>". $result_fabric_description[csf('rmg_qty')]."</td>";
		}
		?>

       </tr>
       <tr>
       <th  colspan="<? echo  count($nameArray_fabric_description)*2+5; ?>" align="left" style="height:30px">&nbsp;</th>
       </tr>

       <tr>
            <th  width="120" align="left">PO Number</th>
            <th  width="120" align="left">Ship Date</th>
            <th  width="120" align="left">Fabric Color</th>
            <th  width="120" align="left">Body Color</th>
            <th  width="120" align="left">Lab Dip No</th>
        <?
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			  echo "<th width='50'>Finish</th><th width='50' >Gray</th>";
		}
		?>

       </tr>
       <?
		  $gmt_color_library=array();
		  $gmt_color_data=sql_select("select gmts_color_id,fabric_color_id
		  FROM
		  wo_booking_dtls
		  WHERE
		  booking_no =$txt_reqsn_no");
		  foreach( $gmt_color_data as $gmt_color_row)
		  {
			$gmt_color_library[$gmt_color_row[csf("fabric_color_id")]].=$color_library[$gmt_color_row[csf("gmts_color_id")]]."," ;
		  }

	        $grand_total_fin_fab_qnty=0;
			$grand_total_grey_fab_qnty=0;
			$grand_totalcons_per_finish=0;
			$grand_totalcons_per_grey=0;
			$color_wise_wo_sql=sql_select("select fabric_color_id ,po_break_down_id,gmts_color_id
										  FROM
										  wo_booking_dtls
										  WHERE
										  booking_no =$txt_reqsn_no and
										  status_active=1 and
                                          is_deleted=0
										  group by po_break_down_id,gmts_color_id,fabric_color_id");
		foreach($color_wise_wo_sql as $color_wise_wo_result)
	    {
		?>
			<tr>
          <td  width="120" align="left">
			<?
			echo $po_number_arr[$color_wise_wo_result[csf('po_break_down_id')]];
			?></td>
             <td  width="120" align="left">
			<?

			echo change_date_format($po_ship_date_arr[$color_wise_wo_result[csf('po_break_down_id')]],"dd-mm-yyyy","-");

			?></td>
            <td  width="120" align="left">
			<?
			echo $color_library[$color_wise_wo_result[csf('fabric_color_id')]];


			?>
            </td>
            <td>
            <?
			echo $color_library[$color_wise_wo_result[csf("gmts_color_id")]];//rtrim($gmt_color_library[$color_wise_wo_result['fabric_color_id']],",");
			?>
            </td>
            <td  width="120" align="left">
			<?
			$lapdip_no="";
			$lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='".$job_no."' and approval_status=3 and color_name_id=".$color_wise_wo_result[csf('fabric_color_id')]."");
			//echo "lapdip_no from wo_po_lapdip_approval_info where job_no_mst='".$job_no."' and approval_status=3 and color_name_id=".$color_wise_wo_result['fabric_color_id']."";
			if($lapdip_no=="") echo "&nbsp;"; echo $lapdip_no;
			?>
            </td>
            <?
			$total_fin_fab_qnty=0;
			$total_grey_fab_qnty=0;

			foreach($nameArray_fabric_description as $result_fabric_description)
		    {

				/*$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d
				WHERE a.job_no=b.job_no and
				a.id=b.pre_cost_fabric_cost_dtls_id and
				c.job_no_mst=a.job_no and
				c.id=b.color_size_table_id and
				b.po_break_down_id=d.po_break_down_id and
				b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
				d.booking_no =$txt_reqsn_no and
				a.body_part_id='".$result_fabric_description['body_part_id']."' and
				a.color_type_id='".$result_fabric_description['color_type_id']."' and
				a.construction='".$result_fabric_description['construction']."' and
				a.composition='".$result_fabric_description['composition']."' and
				a.gsm_weight='".$result_fabric_description['gsm_weight']."' and
				b.dia_width='".$result_fabric_description['dia_width']."' and
				d.fabric_color_id=".$color_wise_wo_result['fabric_color_id']." and
				b.po_break_down_id=".$color_wise_wo_result['po_break_down_id']." and
				d.status_active=1 and
				d.is_deleted=0 and
				c.status_active=1 and
				c.is_deleted=0 and
				a.status_active=1 and
				a.is_deleted=0
				");*/
				if($db_type==0)
				{
				$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty
												  FROM
												  wo_pre_cost_fabric_cost_dtls a,
												  wo_booking_dtls b
												  WHERE
												  b.booking_no =$txt_reqsn_no  and
												  a.id=b.pre_cost_fabric_cost_dtls_id and
												  a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
												  a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
												  a.construction='".$result_fabric_description[csf('construction')]."' and
												  a.composition='".$result_fabric_description[csf('composition')]."' and
												  a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
												  b.dia_width='".$result_fabric_description[csf('dia_width')]."' and
												  b.gmts_color_id=".$color_wise_wo_result[csf('gmts_color_id')]." and
												  b.po_break_down_id=".$color_wise_wo_result[csf('po_break_down_id')]." and
												  b.status_active=1 and
												  b.is_deleted=0");
				}
				if($db_type==2)
				{
				$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty
												  FROM
												  wo_pre_cost_fabric_cost_dtls a,
												  wo_booking_dtls b
												  WHERE
												  b.booking_no =$txt_reqsn_no  and
												  a.id=b.pre_cost_fabric_cost_dtls_id and
												  nvl(a.body_part_id,0)=nvl('".$result_fabric_description[csf('body_part_id')]."',0) and
												  nvl( a.color_type_id,0)=nvl('".$result_fabric_description[csf('color_type_id')]."',0) and
												  nvl(a.construction,0)=nvl('".$result_fabric_description[csf('construction')]."',0) and
												  nvl(a.composition,0)=nvl('".$result_fabric_description[csf('composition')]."',0) and
												  nvl(a.gsm_weight,0)=nvl('".$result_fabric_description[csf('gsm_weight')]."',0) and
												  nvl( b.dia_width,0)=nvl('".$result_fabric_description[csf('dia_width')]."',0) and
												  nvl(b.gmts_color_id,0)=nvl(".$color_wise_wo_result[csf('gmts_color_id')].",0) and
												  b.po_break_down_id=".$color_wise_wo_result[csf('po_break_down_id')]." and
												  b.status_active=1 and
												  b.is_deleted=0");
				}
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
			?>
			<td width='50' align='right'>
			<?
			if($color_wise_wo_result_qnty[csf('fin_fab_qnty')]!="")
			{
			echo number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')],2) ;
			$total_fin_fab_qnty+=$color_wise_wo_result_qnty[csf('fin_fab_qnty')];
			}
			?>
            </td>
            <td width='50' align='right' >
			<?
			if($color_wise_wo_result_qnty[csf('grey_fab_qnty')]!="")
			{
			echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],2);
			$total_grey_fab_qnty+=$color_wise_wo_result_qnty[csf('grey_fab_qnty')];
			}
			?>
            </td>
            <?
			}
			?>
            <td align="right"><? echo number_format($total_fin_fab_qnty,2); $grand_total_fin_fab_qnty+=$total_fin_fab_qnty;?></td>
            <td align="right"><? echo number_format($total_grey_fab_qnty,2); $grand_total_grey_fab_qnty+=$total_grey_fab_qnty;?></td>

            <td align="right">
            <?
			if($process_loss_method==1)
			{
				$process_percent=(($total_grey_fab_qnty-$total_fin_fab_qnty)/$total_fin_fab_qnty)*100;
			}

			if($process_loss_method==2)
			{
				$process_percent=(($total_grey_fab_qnty-$total_fin_fab_qnty)/$total_grey_fab_qnty)*100;
			}
			echo number_format($process_percent,2);

			?>
            </td>
            </tr>
         <?
		}
		?>
        <tr style=" font-weight:bold">
        <td  width="120" align="left">&nbsp;</td>
        <td  width="120" align="left">&nbsp;</td>
        <th  width="120" align="left">&nbsp;</th>
        <td  width="120" align="left">&nbsp;</td>
        <td  width="120" align="left"><strong>Total</strong></td>
        <?
			foreach($nameArray_fabric_description as $result_fabric_description)
		    {

				if($db_type==0)
				{
				$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty
												  FROM
												  wo_pre_cost_fabric_cost_dtls a,
												  wo_booking_dtls b
												  WHERE
												  b.booking_no =$txt_reqsn_no  and
												  a.id=b.pre_cost_fabric_cost_dtls_id and
												  a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
												  a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
												  a.construction='".$result_fabric_description[csf('construction')]."' and
												  a.composition='".$result_fabric_description[csf('composition')]."' and
												  a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
												  b.dia_width='".$result_fabric_description[csf('dia_width')]."' and
												  b.status_active=1 and
												  b.is_deleted=0");
				}

				if($db_type==2)
				{
				$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty
												  FROM
												  wo_pre_cost_fabric_cost_dtls a,
												  wo_booking_dtls b
												  WHERE
												  b.booking_no =$txt_reqsn_no  and
												  a.id=b.pre_cost_fabric_cost_dtls_id and
												  nvl( a.body_part_id,0)=nvl('".$result_fabric_description[csf('body_part_id')]."',0) and
												  nvl(a.color_type_id,0)=nvl('".$result_fabric_description[csf('color_type_id')]."',0) and
												  nvl(a.construction,0)=nvl('".$result_fabric_description[csf('construction')]."',0) and
												  nvl(a.composition,0)=nvl('".$result_fabric_description[csf('composition')]."',0) and
												  nvl(a.gsm_weight,0)=nvl('".$result_fabric_description[csf('gsm_weight')]."',0) and
												  nvl(b.dia_width,0)=nvl('".$result_fabric_description[csf('dia_width')]."',0) and
												  b.status_active=1 and
												  b.is_deleted=0");
				}
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
			?>
			<td width='50' align='right'><?  echo number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')],2) ;?></td><td width='50' align='right' > <? echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],2);?></td>
            <?
			}
			?>
            <td align="right"><? echo number_format($grand_total_fin_fab_qnty,2);?></td>
            <td align="right"><? echo number_format($grand_total_grey_fab_qnty,2);?></td>
            <td align="right">
            <?
            if($process_loss_method==1)// markup
			{
				$totalprocess_percent=(($grand_total_grey_fab_qnty-$grand_total_fin_fab_qnty)/$grand_total_fin_fab_qnty)*100;
			}

			if($process_loss_method==2) //margin
			{
				$totalprocess_percent=(($grand_total_grey_fab_qnty-$grand_total_fin_fab_qnty)/$grand_total_grey_fab_qnty)*100;
			}
			echo number_format($totalprocess_percent,2);
			?>
            </td>
            </tr>

    </table>

        <br/>
        <?
	  }
		?>



        <?
	 if(str_replace("'","",$cbo_fabric_source)==2)
	  {
	$nameArray_fabric_description= sql_select("SELECT  a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width, avg(b.process_loss_percent) as process_loss_percent,avg(rmg_qty) as rmg_qty FROM wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls b where b.booking_no =$txt_reqsn_no and a.id=b.pre_cost_fabric_cost_dtls_id  and b.status_active=1 and
b.is_deleted=0  group by a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width order by a.body_part_id,dia_width");


	 ?>

     <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
     <tr align="center">
     <th colspan="5" align="left">Body Part</th>
        <?
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('body_part_id')] == "")  echo "<td  colspan='3'>&nbsp</td>";
			else         		               echo "<td  colspan='3'>". $body_part[$result_fabric_description[csf('body_part_id')]]."</td>";
		}
		?>
        <td  rowspan="9" width="50"><p>Total   Fabric <? if(trim($cbo_fabric_natu ,"'")==2){echo "(KG)";}else{echo "(Yds)";} ?></p></td>
        <td  rowspan="9" width="50"><p>Rate <? if(trim($cbo_fabric_natu ,"'")==2){echo "(KG)";}else{echo "(Yds)";} ?></p></td>
        <td  rowspan="9" width="50"><p>Amount</p></td>
       </tr>
     <tr align="center"><th colspan="5" align="left">Color Type</th>
        <?
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('color_type_id')] == "")  echo "<td  colspan='3'>&nbsp</td>";
			else         		               echo "<td  colspan='3'>". $color_type[$result_fabric_description[csf('color_type_id')]]."</td>";
		}
		?>
        <!--<td  rowspan="8" width="50"><p>Total  Finish Fabric (KG)</p></td> <td  rowspan="8" width="50"><p>Total Grey Fabric (KG)</p></td>-->
             <!--<td  rowspan="7" width="50"><p>Process Loss % </p></td>-->
       </tr>
        <tr align="center"><th colspan="5" align="left">Fabric Construction</th>
        <?
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('construction')] == "")  echo "<td  colspan='3'>&nbsp</td>";
			else         		               echo "<td  colspan='3'>". $result_fabric_description[csf('construction')]."</td>";
		}
		?>


       </tr>
        <tr align="center"><th   colspan="5" align="left">Fabric Composition</th>
        <?
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('composition')] == "")   echo "<td colspan='3' >&nbsp</td>";
			else         		               echo "<td colspan='3' >".$result_fabric_description[csf('composition')]."</td>";
		}
		?>

       </tr>
       <tr align="center"><th  colspan="5" align="left">GSM</th>
        <?
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('gsm_weight')] == "")   echo "<td colspan='3'>&nbsp</td>";
			else         		       echo "<td colspan='3' align='center'>". $result_fabric_description[csf('gsm_weight')]."</td>";
		}
		?>

       </tr>
       <tr align="center"><th   colspan="5" align="left">Dia/Width (Inch)</th>
        <?
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('dia_width')] == "")   echo "<td colspan='3'>&nbsp</td>";
			else         		              echo "<td colspan='3' align='center'>".$result_fabric_description[csf('dia_width')]."</td>";
		}
		?>

       </tr>
       <tr align="center"><th   colspan="5" align="left">RMG Qty</th>
        <?
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('rmg_qty')] == "")   echo "<td colspan='3'>&nbsp</td>";

			else         		              echo "<td colspan='3' align='center'>". $result_fabric_description[csf('rmg_qty')]."</td>";
		}
		?>

       </tr>
       <tr>
       <th  colspan="<? echo  count($nameArray_fabric_description)*3+5; ?>" align="left" style="height:30px">&nbsp;</th>
       </tr>

       <tr>
            <th  width="120" align="left">PO Number</th>
            <th  width="120" align="left">Ship Date</th>
            <th  width="120" align="left">Fabric Color</th>
            <th  width="120" align="left">Body Color</th>
            <th  width="120" align="left">Lab Dip No</th>
        <?
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			  echo "<th width='50'>Fab Qty</th><th width='50' >Rate</th><th width='50' >Amount</th>";
		}
		?>

       </tr>
       <?
		  $gmt_color_library=array();
		  $gmt_color_data=sql_select("select gmts_color_id,fabric_color_id
		  FROM
		  wo_booking_dtls
		  WHERE
		  booking_no =$txt_reqsn_no");
		  foreach( $gmt_color_data as $gmt_color_row)
		  {
			$gmt_color_library[$gmt_color_row[csf("fabric_color_id")]].=$color_library[$gmt_color_row[csf("gmts_color_id")]]."," ;
		  }

	        $grand_total_fin_fab_qnty=0;
			$grand_total_grey_fab_qnty=0;
			$grand_totalcons_per_finish=0;
			$grand_totalcons_per_grey=0;
			$color_wise_wo_sql=sql_select("select fabric_color_id ,po_break_down_id
										  FROM
										  wo_booking_dtls
										  WHERE
										  booking_no =$txt_reqsn_no and
										  status_active=1 and
                                          is_deleted=0
										  group by po_break_down_id,fabric_color_id");
		foreach($color_wise_wo_sql as $color_wise_wo_result)
	    {
		?>
			<tr>
          <td  width="120" align="left">
			<?
			echo $po_number_arr[$color_wise_wo_result[csf('po_break_down_id')]];
			?></td>
             <td  width="120" align="left">
			<?

			echo change_date_format($po_ship_date_arr[$color_wise_wo_result[csf('po_break_down_id')]],"dd-mm-yyyy","-");

			?></td>
            <td  width="120" align="left">
			<?
			echo $color_library[$color_wise_wo_result[csf('fabric_color_id')]];


			?>
            </td>
            <td>
            <?
			echo rtrim($gmt_color_library[$color_wise_wo_result['fabric_color_id']],",");
			?>
            </td>
            <td  width="120" align="left">
			<?
			$lapdip_no="";
			$lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='".$job_no."' and approval_status=3 and color_name_id=".$color_wise_wo_result[csf('fabric_color_id')]."");
			//echo "lapdip_no from wo_po_lapdip_approval_info where job_no_mst='".$job_no."' and approval_status=3 and color_name_id=".$color_wise_wo_result['fabric_color_id']."";
			if($lapdip_no=="") echo "&nbsp;"; echo $lapdip_no;
			?>
            </td>
            <?
			$total_fin_fab_qnty=0;
			$total_grey_fab_qnty=0;
			$total_amount=0;

			foreach($nameArray_fabric_description as $result_fabric_description)
		    {
				if($db_type==0)
				{
				$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty,avg(b.rate) as rate
												  FROM
												  wo_pre_cost_fabric_cost_dtls a,
												  wo_booking_dtls b
												  WHERE
												  b.booking_no =$txt_reqsn_no  and
												  a.id=b.pre_cost_fabric_cost_dtls_id and
												  a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
												  a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
												  a.construction='".$result_fabric_description[csf('construction')]."' and
												  a.composition='".$result_fabric_description[csf('composition')]."' and
												  a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
												  b.dia_width='".$result_fabric_description[csf('dia_width')]."' and
												  b.fabric_color_id=".$color_wise_wo_result[csf('fabric_color_id')]." and
												  b.po_break_down_id=".$color_wise_wo_result[csf('po_break_down_id')]." and
												  b.status_active=1 and
												  b.is_deleted=0");
				}
				if($db_type==2)
				{
				$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty,avg(b.rate) as rate
												  FROM
												  wo_pre_cost_fabric_cost_dtls a,
												  wo_booking_dtls b
												  WHERE
												  b.booking_no =$txt_reqsn_no  and
												  a.id=b.pre_cost_fabric_cost_dtls_id and
												  nvl(a.body_part_id,0)=nvl('".$result_fabric_description[csf('body_part_id')]."',0) and
												  nvl( a.color_type_id,0)=nvl('".$result_fabric_description[csf('color_type_id')]."',0) and
												  nvl(a.construction,0)=nvl('".$result_fabric_description[csf('construction')]."',0) and
												  nvl(a.composition,0)=nvl('".$result_fabric_description[csf('composition')]."',0) and
												  nvl(a.gsm_weight,0)=nvl('".$result_fabric_description[csf('gsm_weight')]."',0) and
												  nvl( b.dia_width,0)=nvl('".$result_fabric_description[csf('dia_width')]."',0) and
												  nvl(b.fabric_color_id,0)=nvl(".$color_wise_wo_result[csf('fabric_color_id')].",0) and
												  b.po_break_down_id=".$color_wise_wo_result[csf('po_break_down_id')]." and
												  b.status_active=1 and
												  b.is_deleted=0");
				}
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
			?>
			<td width='50' align='right'>
			<?
			if($color_wise_wo_result_qnty[csf('grey_fab_qnty')]!="")
			{
			echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],2) ;
			$total_fin_fab_qnty+=$color_wise_wo_result_qnty[csf('grey_fab_qnty')];
			}
			?>
            </td>
            <td width='50' align='right' >
			<?
			if($color_wise_wo_result_qnty[csf('grey_fab_qnty')]!="")
			{
			echo number_format($color_wise_wo_result_qnty[csf('rate')],2);
			//$total_grey_fab_qnty+=$color_wise_wo_result_qnty[csf('grey_fab_qnty')];
			}
			?>
            </td>
            <td width='50' align='right' >
            <?
		    $amount=$color_wise_wo_result_qnty[csf('grey_fab_qnty')]*$color_wise_wo_result_qnty[csf('rate')];
			if($amount!="")
			{
			echo number_format($amount,2);
			$total_amount+=$amount;
			}
			?>
            </td>
            <?
			}
			?>
            <td align="right"><? echo number_format($total_fin_fab_qnty,2); $grand_total_fin_fab_qnty+=$total_fin_fab_qnty;?></td>
            <td align="right"><? echo number_format($total_amount/$total_fin_fab_qnty,2); $grand_total_amount+=$total_amount;?></td>

            <td align="right">
            <?
			echo number_format($total_amount,2);
			?>
            </td>
            </tr>
         <?
		}
		?>
        <tr style=" font-weight:bold">
        <td  width="120" align="left">&nbsp;</td>
        <td  width="120" align="left">&nbsp;</td>
        <th  width="120" align="left">&nbsp;</th>
        <td  width="120" align="left">&nbsp;</td>
        <td  width="120" align="left"><strong>Total</strong></td>
        <?
			foreach($nameArray_fabric_description as $result_fabric_description)
		    {

				if($db_type==0)
				{
				$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty,avg(b.rate) as rate
												  FROM
												  wo_pre_cost_fabric_cost_dtls a,
												  wo_booking_dtls b
												  WHERE
												  b.booking_no =$txt_reqsn_no  and
												  a.id=b.pre_cost_fabric_cost_dtls_id and
												  a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
												  a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
												  a.construction='".$result_fabric_description[csf('construction')]."' and
												  a.composition='".$result_fabric_description[csf('composition')]."' and
												  a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
												  b.dia_width='".$result_fabric_description[csf('dia_width')]."' and
												  b.status_active=1 and
												  b.is_deleted=0");
				}

				if($db_type==2)
				{
				$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty,avg(b.rate) as rate
												  FROM
												  wo_pre_cost_fabric_cost_dtls a,
												  wo_booking_dtls b
												  WHERE
												  b.booking_no =$txt_reqsn_no  and
												  a.id=b.pre_cost_fabric_cost_dtls_id and
												 nvl( a.body_part_id,0)=nvl('".$result_fabric_description[csf('body_part_id')]."',0) and
												  nvl(a.color_type_id,0)=nvl('".$result_fabric_description[csf('color_type_id')]."',0) and
												  nvl(a.construction,0)=nvl('".$result_fabric_description[csf('construction')]."',0) and
												  nvl(a.composition,0)=nvl('".$result_fabric_description[csf('composition')]."',0) and
												  nvl(a.gsm_weight,0)=nvl('".$result_fabric_description[csf('gsm_weight')]."',0) and
												  nvl(b.dia_width,0)=nvl('".$result_fabric_description[csf('dia_width')]."',0) and
												  b.status_active=1 and
												  b.is_deleted=0");
				}
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
			?>
			<td width='50' align='right'><?  echo number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')],2) ;?></td>
            <td width='50' align='right' > <? //echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],2);?></td>
            <td width='50' align='right' > <? //echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],2);?></td>
            <?
			}
			?>
            <td align="right"><? echo number_format($grand_total_fin_fab_qnty,2);?></td>
            <td align="right"><? echo number_format($grand_total_amount/$grand_total_fin_fab_qnty,2);?></td>
            <td align="right">
            <?
			echo number_format($grand_total_amount,2);
			?>
            </td>
            </tr>

    </table>

        <br/>
        <?
	  }
		?>




        <?
		if(str_replace("'","",$cbo_fabric_source)==1)
	    {
		$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');
		//$yarn_sql_array=sql_select("SELECT min(id) as id ,count_id, copm_one_id, percent_one,copm_two_id, percent_two, type_id, sum(cons_qnty) as yarn_required, AVG(rate) as rate from wo_pre_cost_fab_yarn_cost_dtls where job_no='$job_no' and  status_active=1 and is_deleted=0 group by count_id,copm_one_id,percent_one, copm_two_id,percent_two,type_id order by id");
		$condition= new condition();
		if(str_replace("'","",$job_no) !=''){
			$condition->job_no("='$job_no'");
		}
		$condition->init();
		$yarn= new yarn($condition);
		$yarn_data_array=$yarn->getCountCompositionPercentTypeColorAndRateWiseYarnQtyAndAmountArray();

		$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');
		if($db_type==2 || $db_type==1)
		{
			$yarn_sql_array=sql_select("SELECT min(id) as id ,count_id, copm_one_id, percent_one,copm_two_id, percent_two, type_id, sum(cons_qnty) as yarn_required, AVG(rate) as rate,listagg(cast(fabric_cost_dtls_id as varchar2(4000)),',') within group (order by fabric_cost_dtls_id) as cost_dtls,listagg(cast(cons_ratio as varchar2(4000)),',') within group (order by cons_ratio) as cons_ratio from wo_pre_cost_fab_yarn_cost_dtls where job_no='$job_no' and  status_active=1 and is_deleted=0 group by count_id,copm_one_id,percent_one, copm_two_id,percent_two,type_id order by id");
		}
		else
		{
		  $yarn_sql_array=sql_select("SELECT min(id) as id ,count_id, copm_one_id, percent_one,copm_two_id, percent_two, type_id, sum(cons_qnty) as yarn_required, AVG(rate) as rate,group_concat(fabric_cost_dtls_id) as cost_dtls,group_concat(cons_ratio) as cons_ratio from wo_pre_cost_fab_yarn_cost_dtls where job_no='$job_no' and  status_active=1 and is_deleted=0 group by count_id,copm_one_id,percent_one, copm_two_id,percent_two,type_id order by id");
		}


		?>
        <table  width="100%"  border="0" cellpadding="0" cellspacing="0" >
            <tr>
                <td width="49%" valign="top">
                    <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                    <tr align="center">
                    	<td colspan="5"><b>Yarn Required Summary (Pre Cost) </b></td>
                    </tr>
                    <tr align="center">
                        <td>Sl</td>
                        <td>Yarn Description</td>
                        <?
                        if($show_yarn_rate==1)
                        {
                        ?>
                        <td>Rate</td>
                        <?
                        }
                        ?>
                        <td>Cons for <? echo $costing_per; ?> Gmts</td>
                        <td>Total (KG)</td>
                    </tr>
                    <?
					$i=0;
					$total_yarn=0;
					foreach($yarn_sql_array  as $row)
                    {
						$i++;
						$fabric_cost_id=$row[csf('cost_dtls')];
						$tot_booking_grey=sql_select("select sum(grey_fab_qnty) as grey_qty  from wo_booking_dtls where status_active=1 and is_deleted=0 and booking_type=1 and is_short=1 and job_no='$job_no' and booking_no=$txt_reqsn_no and pre_cost_fabric_cost_dtls_id in($fabric_cost_id)");

						$rowcons_qnty = $yarn_data_array[$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]][$row[csf("rate")]]['qty'];
						?>
						<tr align="center">
                            <td><? echo $i; ?></td>
                            <td>
                            <?
                            $yarn_des=$yarn_count_arr[$row[csf('count_id')]]." ".$composition[$row[csf('copm_one_id')]]." ".$row[csf('percent_one')]."%  ";
                            if($row['copm_two_id'] !=0)
                            {
                                $yarn_des.=$composition[$row[csf('copm_two_id')]]." ".$row[csf('percent_two')]."%";
                            }
                            $yarn_des.=$color_library[$row[csf('color')]]." ";
                            $yarn_des.=$yarn_type[$row[csf('type_id')]];
                            //echo $yarn_count_arr[$row['count_id']]." ".$composition[$row['copm_one_id']]." ".$row['percent_one']."%  ".$composition[$row['copm_two_id']]." ".$row['percent_two']."%  ".$yarn_type[$row['type_id']];
                            echo $yarn_des;
                            ?>
                            </td>
                            <?
                            if($show_yarn_rate==1)
                            {
                            ?>
                             <td><? echo number_format($row[csf('rate')],4); ?></td>
                             <?
                            }
                             ?>
                            <td><? echo number_format($row[csf('yarn_required')],4); ?></td>

                            <!--<td><? //echo number_format(($row['yarn_required']/$po_qnty_tot)*$costing_per_qnty,2); ?></td>-->
                          <!--   <td align="right"><? //echo number_format($rowcons_qnty,2); $total_yarn+=$rowcons_qnty; ?></td> -->
                          <td align="right"><? $total_kg=$tot_booking_grey[0][csf("grey_qty")] * ($row[csf("cons_ratio")]/100); echo number_format($total_kg,2); ?> </td>
						</tr>
						<?
						$total_yarn += $total_kg;
					}
					?>
                    <tr align="center">
                        <td></td>
                        <td></td>
                        <?
                        if($show_yarn_rate==1)
                        {
                        ?>
                        <td></td>
                        <?
                        }
                        ?>
                        <td align="right">Total : </td>
                        <td align="right"><? echo number_format($total_yarn,2); ?></td>
                    </tr>
                    </table>
                </td>
                <td width="2%">
                </td>
                <td width="49%" valign="top" align="center">
                <?
				$yarn_sql_array=sql_select("SELECT min(a.id) as id, a.item_id, sum(a.qnty) as qnty ,min(b.supplier_id) as supplier_id,min(b.lot) as lot from inv_material_allocation_dtls a,product_details_master b where a.item_id=b.id and a.booking_no=$txt_reqsn_no and  a.status_active=1 and a.is_deleted=0 group by a.item_id order by a.id");
				if(count($yarn_sql_array)>0)
				{
				?>
                   <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                    <tr align="center">
                    <td colspan="7"><b>Allocated Yarn</b></td>

                    </tr>
                    <tr align="center">
                    <td>Sl</td>
                    <td>Yarn Description</td>
                    <td>Brand</td>
                    <td>Lot</td>


                    <td>Allocated Qty (Kg)</td>
                    </tr>
                    <?
					$total_allo=0;
					$item=return_library_array( "select id, product_name_details from   product_details_master",'id','product_name_details');
					$supplier=return_library_array( "select id, short_name from   lib_supplier",'id','short_name');
					//$yarn_sql_array=sql_select("SELECT a.item_id, a.qnty,b.supplier_id,b.lot from inv_material_allocation_dtls a,product_details_master b where a.item_id=b.id and a.booking_no=$txt_reqsn_no and  a.status_active=1 and a.is_deleted=0");
					$i=0;
					$total_yarn=0;
					foreach($yarn_sql_array  as $row)
                    {

						$i++;
					?>
                    <tr align="center">
                    <td><? echo $i; ?></td>
                    <td>
					<?

					echo $item[$row[csf('item_id')]];
					?>
                    </td>
                    <td>
                    <?

					echo $supplier[$row[csf('supplier_id')]];
					?>
                    </td>
                    <td>
					<?

					echo $row[csf('lot')];
					?>
                    </td>
                    <td align="right"><? echo number_format($row[csf('qnty')],4); $total_allo+= $row[csf('qnty')];?></td>
                    </tr>
                    <?
					}
					?>
                    <tr align="center">
                    <td>Total</td>
                    <td></td>


                    <td></td>
                    <td></td>
                    <td align="right"><? echo number_format($total_allo,4); ?></td>
                    </tr>
                    </table>
                    <?
				}
				else
				{
					$is_yarn_allocated=return_field_value("allocation", "variable_settings_inventory", "company_name=$cbo_company_name and variable_list=18 and item_category_id=1");
					if($is_yarn_allocated==1)
					{
					?>
					<font style=" font-size:30px"><b> Draft</b></font>
                    <?
					}
					else
					{
						echo "";
					}
				}
					?>
                </td>
            </tr>
        </table>
        <br/>
        <?
		}
		?>
        <?
		$sql_embelishment=sql_select("select emb_name,emb_type,cons_dzn_gmts,rate,amount from wo_pre_cost_embe_cost_dtls where job_no='$job_no' and status_active=1 and 	is_deleted=0");
		?>
        <table  width="100%"  border="0" cellpadding="0" cellspacing="0" >
            <tr>
                <td width="49%" valign="top">
                <?
				if(count($sql_embelishment)>0)
				{
				?>
                    <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                    <tr align="center">
                    <td colspan="7"><b>Embelishment (Pre Cost)</b></td>

                    </tr>
                    <tr align="center">
                    <td>Sl</td>
                    <td>Embelishment Name</td>
                    <td>Embelishment Type</td>
                    <td>Cons <? echo $costing_per; ?> Gmts</td>
                    <td>Rate</td>
                    <td>Amount</td>

                    </tr>
                    <?
					$sql_embelishment=sql_select("select emb_name,emb_type,cons_dzn_gmts,rate,amount from wo_pre_cost_embe_cost_dtls where job_no='$job_no' and status_active=1 and 	is_deleted=0");
					$i=0;
					//$total_yarn=0;
					foreach($sql_embelishment  as $row_embelishment)
                    {

						$i++;
					?>
                    <tr align="center">
                    <td><? echo $i; ?></td>
                    <td>
					<?
					echo $emblishment_name_array[$row_embelishment[csf('emb_name')]];
					?>
                    </td>
                    <td>
                    <?
					if($row_embelishment[csf('emb_name')]==1)
					{
					echo $emblishment_print_type[$row_embelishment[csf('emb_type')]];
					}
					if($row_embelishment[csf('emb_name')]==2)
					{
					echo $emblishment_embroy_type[$row_embelishment[csf('emb_type')]];
					}
					if($row_embelishment[csf('emb_name')]==3)
					{
					echo $emblishment_wash_type[$row_embelishment[csf('emb_type')]];
					}
					if($row_embelishment[csf('emb_name')]==4)
					{
					echo $emblishment_spwork_type[$row_embelishment[csf('emb_type')]];
					}
					if($row_embelishment[csf('emb_name')]==5)
					{
					echo $row_embelishment[csf('emb_type')];
					}
					?>

                    </td>
                    <td>
                    <?
					echo $row_embelishment[csf('cons_dzn_gmts')];
					?>
                    </td>
                    <td>
					<?
					echo $row_embelishment[csf('rate')];
					?>
                    </td>

                    <td>
					<?
					echo $row_embelishment[csf('amount')];
					?>
                    </td>


                    </tr>
                    <?
					}
					?>
                 <!--   <tr align="center">
                    <td>Total</td>
                    <td></td>
                    <td></td>
                    <td></td>

                    <td></td>

                    <td></td>

                    </tr>-->
                    </table>
                    <?
				}
					?>
                </td>
                <td width="2%">
                </td>
                <td width="49%" valign="top" align="center">
                <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                    <tr align="center">
                    <td><b>Approved Instructions</b></td>

                    </tr>
                    <tr>
                    <td>
                <?  echo $nameArray_approved_comments_row[csf('comments')];  ?>
                </td>
                </tr>
                </table>

                </td>
            </tr>
        </table>
        <br/>

         <br>
        <?
     $desg_name=return_library_array( "select id, custom_designation from lib_designation", "id", "custom_designation"  );
	 $data_array=sql_select("select b.approved_by,b.approved_no, b.approved_date, c.user_full_name,c.designation from  wo_booking_mst a , approval_history b, user_passwd c where a.id=b.mst_id and b.approved_by=c.id and a.booking_no=$txt_reqsn_no and b.entry_form=12 order by b.id asc");
	?>
       <table  width="100%" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
            <tr style="border:1px solid black;">
                <th colspan="3" style="border:1px solid black;">Approval Status</th>
                </tr>
                <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th><th width="50%" style="border:1px solid black;">Name/Designation</th><th width="27%" style="border:1px solid black;">Approval Date</th><th width="20%" style="border:1px solid black;">Approval No</th>
                </tr>
            </thead>
            <tbody>
            <?
			$s=1;
			foreach($data_array as $row){
			?>
            <tr style="border:1px solid black;">
                <td width="3%" style="border:1px solid black;"><? echo $s;?></td><td width="50%" style="border:1px solid black;"><? echo $row[csf('user_full_name')].'/'.$desg_name[$row[csf('designation')]];?></td><td width="27%" style="border:1px solid black;"><? echo date ("d-m-Y h:i:s",strtotime($row[csf('approved_date')])); //echo change_date_format($row[csf('approved_date')],"dd-mm-yyyy","-");?></td><td width="20%" style="border:1px solid black;"><? echo $row[csf('approved_no')];?></td>
                </tr>
                <?
				$s++;
			}
				?>
            </tbody>
        </table>
        </br>


        <table  width="100%"  border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td width="49%" style="border:solid; border-color:#000; border-width:thin" valign="top">
                    <?php echo get_spacial_instruction($txt_reqsn_no); ?>
                </td>
                <td width="2%">
                </td>
                <td width="49%" valign="top">
                <?
				if(str_replace("'","",$cbo_fabric_source)==1 || str_replace("'","",$cbo_fabric_source)==2)
				{
				?>
                   <table width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                   <tr align="center">
                    <td colspan="10"><b>Comments</b></td>

                    </tr>
                    <tr align="center">
                    <td>Sl</td>
                    <td>Po NO</td>
                    <td>Ship Date</td>
                    <td>Pre-Cost Qty</td>
                    <td>Mn.Book Qty</td>
                    <td>Sht.Book Qty</td>
                    <td>Smp.Book Qty</td>
                    <td>Tot.Book Qty</td>
                    <td>Balance</td>
                    <td>Comments</td>
                    </tr>
                    <?
	$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	if ($cbo_fabric_natu!=0) $cbo_fabric_natu="and a.fab_nature_id='$cbo_fabric_natu'";
	if ($cbo_fabric_source!=0) $cbo_fabric_source_cond="and a.fabric_source='$cbo_fabric_source'";
	$paln_cut_qnty_array=return_library_array( "select min(id) as id,sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown  where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and is_deleted=0 and status_active=1 group by color_mst_id,size_mst_id,item_mst_id", "id", "plan_cut_qnty");

	$item_ratio_array=return_library_array( "select gmts_item_id,set_item_ratio from wo_po_details_mas_set_details  where job_no =$txt_job_no", "gmts_item_id", "set_item_ratio");
	$nameArray=sql_select("
	select
	a.id,
	a.item_number_id,
	a.costing_per,
	b.po_break_down_id,
	b.color_size_table_id,
	b.requirment,
	c.po_number
FROM
	wo_pre_cost_fabric_cost_dtls a,
	wo_pre_cos_fab_co_avg_con_dtls b,
	wo_po_break_down c
WHERE
	a.job_no=b.job_no and
	a.job_no=c.job_no_mst and
    a.id=b.pre_cost_fabric_cost_dtls_id and
	b.po_break_down_id=c.id and
	b.po_break_down_id in (".str_replace("'","",$txt_order_no_id).")  $cbo_fabric_natu $cbo_fabric_source_cond and a.status_active=1 and a.is_deleted=0
	order by a.id");
	$count=0;
	$tot_grey_req_as_pre_cost_arr=array();
	foreach ($nameArray as $result)
	{
		if (count($nameArray)>0 )
		{
            if($result[csf("costing_per")]==1)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(12*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
			}
			if($result[csf("costing_per")]==2)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(1*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
			}
			if($result[csf("costing_per")]==3)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(24*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
			}
			if($result[csf("costing_per")]==4)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(36*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
			}
			if($result[csf("costing_per")]==5)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(48*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
			}
			$tot_grey_req_as_pre_cost_arr[$result[csf("po_number")]]+=$tot_grey_req_as_pre_cost;
        }
    }
	                $total_pre_cost=0;
					$total_booking_qnty_main=0;
					$total_booking_qnty_short=0;
					$total_booking_qnty_sample=0;
					$total_tot_bok_qty=0;
					$tot_balance=0;
					/*$booking_qnty=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and booking_type in(1,4)  and a.status_active=1 and a.is_deleted=0 group by b.po_number order by a.po_break_down_id", "po_break_down_id", "grey_fab_qnty");*/
					$booking_qnty_main=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b, wo_booking_mst c where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no  and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and a.booking_type =1 and c.fabric_source=$cbo_fabric_source and a.is_short=2 and c.item_category=2 and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");

					$booking_qnty_short=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b , wo_booking_mst c where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and a.booking_type =1 and c.fabric_source=$cbo_fabric_source and c.item_category=2 and a.is_short=1 and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");
					$booking_qnty_sample=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b , wo_booking_mst c  where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and a.booking_type =4 and c.fabric_source=$cbo_fabric_source and c.item_category=2  and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");

					$sql_data=sql_select( "select max(a.id) as id,a.po_number,max(a.pub_shipment_date) as pub_shipment_date,sum(a.plan_cut) as plan_cut  from wo_po_break_down a,wo_pre_cost_sum_dtls b,wo_pre_cost_mst c where a.job_no_mst=b.job_no and a.job_no_mst=c.job_no and a.id in(".str_replace("'","",$txt_order_no_id).") group by a.po_number order by id");
					//print_r($booking_qnty_short);
					//echo "select max(a.id) as id,a.po_number,max(a.pub_shipment_date) as pub_shipment_date,sum(a.plan_cut) as plan_cut  from wo_po_break_down a,wo_pre_cost_sum_dtls b,wo_pre_cost_mst c where a.job_no_mst=b.job_no and a.job_no_mst=c.job_no and a.id in(".str_replace("'","",$txt_order_no_id).") group by a.po_number order by a.id";
					foreach($sql_data  as $row)
                    {
					$col++;
					?>
                    <tr align="center">
                    <td><? echo $col; ?></td>
                    <td><? echo $row[csf("po_number")]; ?></td>
                     <td><? echo change_date_format($row[csf("pub_shipment_date")],"dd-mm-yyyy",'-'); ?></td>
                    <td align="right"><? echo number_format($tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]],2); $total_pre_cost+=$tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]]; ?></td>
                    <td align="right"><? echo number_format($booking_qnty_main[$row[csf("id")]],2); $total_booking_qnty_main+=$booking_qnty_main[$row[csf("id")]];?></td>
                    <td align="right"><? echo number_format($booking_qnty_short[$row[csf("id")]],2); $total_booking_qnty_short+=$booking_qnty_short[$row[csf("id")]];?></td>
                    <td align="right"><? echo number_format($booking_qnty_sample[$row[csf("id")]],2); $total_booking_qnty_sample+=$booking_qnty_sample[$row[csf("id")]];?></td>
                    <td align="right"><? $tot_bok_qty=$booking_qnty_main[$row[csf("id")]]+$booking_qnty_short[$row[csf("id")]]+$booking_qnty_sample[$row[csf("id")]]; echo number_format($tot_bok_qty,2); $total_tot_bok_qty+=$tot_bok_qty;?></td>
                    <td align="right">
					<? $balance= def_number_format($tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]]-$tot_bok_qty,2,""); echo number_format($balance,2); $tot_balance+= $balance?>
                    </td>
                    <td>
					<?
					if( $balance>0)
					{
						echo "Less Booking";
					}
					else if ($balance<0)
					{
						echo "Over Booking";
					}
					else
					{
						echo "";
					}
					?>
                    </td>
                    </tr>
                    <?
					}
					?>
                    <tfoot>

                    <tr>
                    <td colspan="3">Total:</td>

                    <td align="right"><? echo number_format($total_pre_cost,2); ?></td>
                    <td align="right"><? echo number_format($total_booking_qnty_main,2); ?></td>
                    <td align="right"><? echo number_format($total_booking_qnty_short,2); ?></td>
                    <td align="right"><? echo number_format($total_booking_qnty_sample,2); ?></td>
                     <td align="right"><? echo number_format($total_tot_bok_qty,2); ?></td>
                    <td align="right"><? echo number_format($tot_balance,2); ?></td>
                    <td></td>
                    </tr>
                    </tfoot>
                    </table>
                    <?
				}
				?>
                </td>

            </tr>
        </table>


        <br/>
        <?
		//echo "select responsible_dept,	responsible_person,	reason from wo_booking_dtls where booking_no =$txt_reqsn_no";
		//"select id,department_name from  lib_department where status_active=1 and is_deleted=0 order by  department_name"
		$department_name_library=return_library_array( "select id,department_name from  lib_department where status_active=1 and is_deleted=0 order by  department_name", "id", "department_name"  );

		$sql_responsible= sql_select("select responsible_dept,	responsible_person,	reason from wo_booking_dtls where booking_no =$txt_reqsn_no");
		if(count($sql_responsible)>0)
		{
		?>
         <table width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
         <tr>
         <td>
          #
         </td>
          <td>
         Responsible Dept.
         </td>
         <td>
         Responsible person
         </td>
         <td>
         Reason
         </td>
         </tr>
         <?
		 $ir=1;
		foreach($sql_responsible as $sql_responsible_row)
		{
			?>
             <tr>
             <td>
             <?  echo $ir; ?>
             </td>
              <td>
             <?
			 $responsible_dept_st="";
			 $responsible_dept_arr=explode( ",",$sql_responsible_row[csf('responsible_dept')]);
			 foreach($responsible_dept_arr as $key => $value)
			 {
				 $responsible_dept_st.= $department_name_library[$value].", ";
			 }
			 echo rtrim($responsible_dept_st,", ");
			 ?>
             </td>
             <td>
            <?  echo $sql_responsible_row[csf('responsible_person')]; ?>
             </td>
             <td>
              <?  echo $sql_responsible_row[csf('reason')]; ?>
             </td>
             </tr>
            <?
			$ir++;

		}
		 ?>
         </table>
         <?
		}
		 ?>
         <!--<br><br><br><br>-->
         <?
		 	echo signature_table(4, $cbo_company_name, "1330px");
			echo "****".custom_file_name($txt_reqsn_no,$style_sting,$txt_job_no);
		 ?>

       </div>
       <?

}

if($action=="show_fabric_requisition_report4")//unused
{

	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	$report_type=str_replace("'","",$report_type);
	if ($cbo_fabric_natu!=0) $cbo_fabric_natu="and a.fab_nature_id='$cbo_fabric_natu'";
	if ($cbo_fabric_source!=0) $cbo_fabric_source_cond="and a.fabric_source='$cbo_fabric_source'";
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	//$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$location_name_arr=return_library_array( "select id,location_name from lib_location",'id','location_name');
	$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	$po_qnty_tot1=return_field_value( "sum(po_quantity)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");

	$po_number_arr=return_library_array( "select id,po_number from   wo_po_break_down",'id','po_number');
	$po_ship_date_arr=return_library_array( "select id,pub_shipment_date from   wo_po_break_down ",'id','pub_shipment_date');
	$user_arr=return_library_array( "select id,user_name from   user_passwd ",'id','user_name');

	?>
	<div style="width:1330px" align="left">
    	<?php

$nameArray_approved = sql_select("select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_reqsn_no and b.entry_form=12");
list($nameArray_approved_row) = $nameArray_approved;
$nameArray_approved_date = sql_select("select b.approved_date as approved_date,approved_by from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_reqsn_no and b.entry_form=12 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "'");
list($nameArray_approved_date_row) = $nameArray_approved_date;
$nameArray_approved_comments = sql_select("select b.comments as comments from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_reqsn_no and b.entry_form=12 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "'");
list($nameArray_approved_comments_row) = $nameArray_approved_comments;
$path = str_replace("'", "", $path);
if ($path == "") {
	$path = '../../';
}

?>									<!--    Header Company Information         -->
       <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black" >
           <tr>
               <td width="100">
               <?
               if($report_type==1)
			   {
			   ?>
               <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               <?
			   }
			   else if($report_type==2)
			   {
			   ?>
               <img  src='../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               <?
			   }
			   else
			   {
			   ?>
               <img  src='<? echo $path.$imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               <?
			   }
			   ?>
               </td>
               <td width="1250">
                    <table width="100%" cellpadding="0" cellspacing="0"  >
                        <tr>
                            <td align="center" style="font-size:20px;">
                              <?php
echo $company_library[$cbo_company_name];
?>
                            </td>
                            <td rowspan="3" width="250">
                               <span style="font-size:18px"><b> Job No:&nbsp;&nbsp;<? echo trim($txt_job_no,"'"); ?></b></span>
                                <br/>
                               <?
								 if($nameArray_approved_row[csf('approved_no')]>1)
								 {
								 ?>
								 <b> Revised No: <? echo $nameArray_approved_row[csf('approved_no')]-1; ?></b>
                                  <br/>
								  Approved Date: <? echo $nameArray_approved_date_row[csf('approved_date')]; ?>
                                   <?
								 }
							  	?>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px">
                            <?
                          $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
						   if($txt_job_no!="")
							{
							 $location=return_field_value( "location_name", "wo_po_details_master","job_no=$txt_job_no");
							}
							else
							{
							$location="";
							}


                            foreach ($nameArray as $result)
                            {
								echo $location_name_arr[$location];

                            ?>
                               <!-- Plot No: <? //echo $result[csf('plot_no')]; ?>
                                Level No: <? //echo $result[csf('level_no')]?>
                                Road No: <? //echo $result[csf('road_no')]; ?>
                                Block No: <? //echo $result[csf('block_no')];?>
                                City No: <? //echo $result[csf('city')];?>
                                Zip Code: <? //echo $result[csf('zip_code')]; ?>
                                Province No: <?php //echo $result[csf('province')];?>
                                Country: <? //echo $country_arr[$result[csf('country_id')]]; ?>--> <br>
                                Email Address: <? echo $result[csf('email')];?>
                                Website No:<? echo $result[csf('website')];

                            }
                            ?>
                               </td>
                            </tr>
                            <tr>
                            <td align="center" style="font-size:20px">
                                <strong><? if($report_type==1) echo str_replace("'","",$report_title);else echo 'Short Fabric Booking';//echo str_replace("'","",$report_title);?> &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:#F00"><? if(str_replace("'","",$id_approved_id) ==1){ echo "(Approved)";}else{echo "";}; ?> </font></strong>
                             </td>
                            </tr>
                      </table>
                </td>
            </tr>
       </table>
                <?
				$job_no='';
				$total_set_qnty=0;
				$colar_excess_percent=0;
				$cuff_excess_percent=0;
				$rmg_process_breakdown=0;
				$style_ref_no="";
				$inserted_by="";
                $nameArray=sql_select( "select a.booking_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.po_break_down_id,a.colar_excess_percent,a.cuff_excess_percent,a.delivery_date,a.is_apply_last_update,a.fabric_source,a.pay_mode,a.rmg_process_breakdown,a.insert_date,a.inserted_by,a.update_date,b.job_no,b.buyer_name, b.style_ref_no ,b.gmts_item_id,b.order_uom,b.total_set_qnty,b.style_description,b.season,b.product_dept,b.product_code,b.pro_sub_dep,b.dealing_marchant from wo_booking_mst a, wo_po_details_master b where  a.job_no=b.job_no and a.booking_no=$txt_reqsn_no");
				foreach ($nameArray as $result)
				{
					$total_set_qnty=$result[csf('total_set_qnty')];
					$colar_excess_percent=$result[csf('colar_excess_percent')];
				    $cuff_excess_percent=$result[csf('cuff_excess_percent')];
					$rmg_process_breakdown=$result[csf('rmg_process_breakdown')];
					$inserted_by=$result[csf('inserted_by')];
					$po_no="";
					$shipment_date="";$internal_ref="";	$file_no="";
					$sql_po= "select po_number,grouping,file_no,MIN(pub_shipment_date) pub_shipment_date from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].") group by po_number,grouping,file_no";
					$data_array_po=sql_select($sql_po);
					foreach ($data_array_po as $row_po)
					{
						$po_no.=$row_po[csf('po_number')].", ";
						$shipment_date.=change_date_format($row_po[csf('pub_shipment_date')],'dd-mm-yyyy','-').", ";
						$internal_ref.=$row_po[csf('grouping')].", ";
						$file_no.=$row_po[csf('file_no')].", ";
					}
					$lead_time=="";
					if($db_type==0)
					{
					$sql_lead_time= "select DATEDIFF(pub_shipment_date,po_received_date) date_diff from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")";
					}
					if($db_type==2)
					{
					$sql_lead_time= "select (pub_shipment_date-po_received_date) date_diff from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")";
					}

					$data_array_lead_time=sql_select($sql_lead_time);
					foreach ($data_array_lead_time as $row_lead_time)
					{
						$lead_time.=$row_lead_time[csf('date_diff')].",";
						//$shipment_date.=change_date_format($row_po['pub_shipment_date'],'dd-mm-yyyy','-').",";
					}
					$po_received_date="";
					$sql_po_received_date= "select MIN(po_received_date) as po_received_date from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")";
					$data_array_po_received_date=sql_select($sql_po_received_date);
					foreach ($data_array_po_received_date as $row_po_received_date)
					{
						$po_received_date=change_date_format($row_po_received_date[csf('po_received_date')],'dd-mm-yyyy','-');
						//$shipment_date.=change_date_format($row_po['pub_shipment_date'],'dd-mm-yyyy','-').",";
					}
					$file=rtrim($file_no,", ");//rtrim($po_no,", ")
					$file_all=array_unique(explode(",",$file));

					$file='';
					foreach($file_all as $file_id)
					{
						if($file=="") $file_cond=$file_id; else $file_cond.=", ".$file_id;
					}
				?>
       <table width="100%" style="border:1px solid black" >
            <tr>
                <td colspan="6" valign="top" style="font-size:18px; color:#F00"><? if($result[csf('is_apply_last_update')]==2){echo "Booking Info not synchronized with order entry and pre-costing. order entry or pre-costing has updated after booking entry.  Contact to ".$marchentrArr[$result[csf('dealing_marchant')]]; } else{ echo "";} ?></td>
            </tr>
            <tr>
                <td width="100"><span style="font-size:18px"><b>Buyer/Agent Name</b></span></td>
                <td width="110">:&nbsp;<span style="font-size:18px"><b><? echo $buyer_name_arr[$result[csf('buyer_name')]]; ?></b></span></td>
                <td width="100"><span style="font-size:12px"><b>Dept.</b></span></td>
                <td width="110">:&nbsp;<? echo $product_dept[$result[csf('product_dept')]] ; if($result[csf('product_code')] !=""){ echo " (".$result[csf('product_code')].")";} if($result[csf('pro_sub_dep')] !=0){ echo " (".$pro_sub_dept_array[$result[csf('pro_sub_dep')]].")";}?></td>
                <td width="100"><span style="font-size:12px"><b>Order Qnty</b></span></td>
                <td width="110">:&nbsp;
				<?  echo $po_qnty_tot1." ".$unit_of_measurement[$result[csf('order_uom')]] ; ?>
                </td>
            </tr>
            <tr>

                <td width="100" style="font-size:12px"><b>Garments Item</b></td>
                <td width="110">:&nbsp;
				<?
				$gmts_item_name="";
				$gmts_item=explode(',',$result[csf('gmts_item_id')]);
				for($g=0;$g<=count($gmts_item); $g++)
				{
					$gmts_item_name.= $garments_item[$gmts_item[$g]].",";
				}
				echo rtrim($gmts_item_name,',');
				?>
                </td>
                <td width="100" style="font-size:12px"><b>Booking Release Date</b></td>
                <td width="110">:&nbsp;
				<?
				$booking_date=$result[csf('update_date')];
				if($booking_date=="" || $booking_date=="0000-00-00 00:00:00")
				{
					$booking_date=$result[csf('insert_date')];
				}
				echo change_date_format($booking_date,'dd-mm-yyyy','-','');
				?>&nbsp;&nbsp;&nbsp;
                </td>
                <td width="100" style="font-size:18px"><b>Style Ref.</b>   </td>
                <td width="110" style="font-size:18px">:&nbsp;
                <b>
				<?
				echo $result[csf('style_ref_no')];
				$style_ref_no=$result[csf('style_ref_no')];
				?>
                </b>
                </td>

            </tr>
             <tr>



                <td  width="100" style="font-size:12px"><b>Style Des.</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('style_description')]; $job_no= $result[csf('job_no')];?></td>
                <td width="100" style="font-size:12px"><b>Lead Time </b>   </td>
                <td width="110">:&nbsp;<?  echo rtrim($lead_time,",");?> </td>
                <td width="100" style="font-size:12px"><b>Dealing Merchant</b></td>
                <td width="110">:&nbsp;<? echo $marchentrArr[$result[csf('dealing_marchant')]]; ?></td>



            </tr>

            <tr>
                <td width="100" style="font-size:12px"><b>Supplier Name</b>   </td>
                <td width="110">:&nbsp;<?
				if($result[csf('pay_mode')]==5 || $result[csf('pay_mode')]==3){
					echo $company_library[$result[csf('supplier_id')]];
					}
					else{
					echo $supplier_name_arr[$result[csf('supplier_id')]];
					}
				//echo $supplier_name_arr[$result[csf('supplier_id')]];?>    </td>
                <td width="100" style="font-size:12px"><b>Delivery Date</b></td>
               	<td width="110">:&nbsp;<? echo change_date_format( $result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>
                <td width="100" style="font-size:18px"><b>Booking No </b>   </td>
                <td width="110" style="font-size:18px">:&nbsp;<b><? echo $result[csf('booking_no')];?></b><? echo "(".$fabric_source[$result[csf('fabric_source')]].")"?></td>



            </tr>
            <tr>
                <td width="100" style="font-size:12px"><b>Season</b></td>
                <td width="110">:&nbsp;<? echo $result[csf('season')]; ?></td>
                <td  width="100" style="font-size:12px"><b>Attention</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('attention')]; ?></td>
                <td  width="100" style="font-size:12px"><b>Po Received Date</b></td>
                <td  width="110" >:&nbsp;<? echo $po_received_date; ?></td>
            </tr>
             <tr>
                <td width="100" style="font-size:17px"><b>Internal Ref</b></td>
                <td width="110" colspan="3" style="font-size:16px">:&nbsp;<b><? echo rtrim($internal_ref,", "); ?></b></td>
                <td  width="100" style="font-size:17px"><b>File No</b></td>
                <td  width="110" colspan="2" style="font-size:16px">:&nbsp;<b><? echo $file_cond; ?></b></td>

            </tr>
        </table>
           <?
			}
			//echo "select distinct size_number_id from wo_po_color_size_breakdown where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  	is_deleted=0 and status_active=1 order by size_number_id";
			//$nameArray_size=sql_select( "select distinct size_number_id from wo_po_color_size_breakdown where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  	is_deleted=0 and status_active=1 order by size_number_id");
			$nameArray_size=sql_select( "select  size_number_id,min(id) as id from wo_po_color_size_breakdown where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  	is_deleted=0 and status_active=1 group by size_number_id order by id");
		   ?>
            <table width="100%" >
		    <tr>
            <td width="800">
                <div id="div_size_color_matrix" style="float:left; max-width:1000;">
            	<fieldset id="div_size_color_matrix" style="max-width:1000;">
 				<legend>Size and Color Breakdown                </legend>
 				<table  class="rpt_table"  border="1" align="left" cellpadding="0" width="750" cellspacing="0" rules="all" >
                    <tr>
                        <td style="border:1px solid black"><strong>PO Namber</strong></td>
                        <td style="border:1px solid black"><strong>Ship Date</strong></td>
                        <td style="border:1px solid black"><strong>Gmts Item</strong></td>
                        <td style="border:1px solid black"><strong>Style Ref</strong></td>
                        <td style="border:1px solid black"><strong>Color/Size</strong></td>
                    <?
						foreach($nameArray_size  as $result_size)
                        {	     ?>
                        <td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
                    <?	}    ?>
                        <td style="border:1px solid black; width:130px" align="center"><strong> Total Order Qty(Pcs)</strong></td>
                        <td style="border:1px solid black; width:80px" align="center"><strong> Excess %</strong></td>
                        <td style="border:1px solid black; width:130px" align="center"><strong> Total Plan Cut Qty(Pcs)</strong></td>
                    </tr>
                    <?
					$color_size_order_qnty_array=array();
					$color_size_qnty_array=array();
					$size_tatal=array();
					$size_tatal_order=array();
					$order_id=explode(",",str_replace("'","",$txt_order_no_id));
					for($or=0;$or<count($order_id); $or++)
				    {
					for($c=0;$c<count($gmts_item); $c++)
				    {
					$item_size_tatal=array();
					$item_size_tatal_order=array();
					$item_grand_total=0;
					$item_grand_total_order=0;
					$nameArray_color=sql_select( "select  color_number_id, min(id) as id from wo_po_color_size_breakdown where  item_number_id=$gmts_item[$c] and po_break_down_id =$order_id[$or] and is_deleted=0 and status_active=1 group by color_number_id  order by id");
					?>
                   <!-- <tr>
                    <td style="border:1px solid black" colspan="<? echo count($nameArray_size)+3;?>"><strong><? echo $garments_item[$gmts_item[$c]];?></strong></td>

                    </tr>-->
                    <?
					foreach($nameArray_color as $result_color)
                    {
                    ?>
                    <tr>
                        <td align="center" style="border:1px solid black"><? echo $po_number_arr[$order_id[$or]]; // echo $row_num_tr; ?></td>
                         <td align="center" style="border:1px solid black"><? echo change_date_format($po_ship_date_arr[$order_id[$or]],"dd-mm-yyyy","-"); // echo $row_num_tr; ?></td>
                          <td align="center" style="border:1px solid black"><? echo $garments_item[$gmts_item[$c]]; // echo $row_num_tr; ?></td>
                          <td align="center" style="border:1px solid black"><? echo $style_ref_no; // echo $row_num_tr; ?></td>
                        <td align="center" style="border:1px solid black"><? echo $color_library[$result_color[csf('color_number_id')]]; // echo $row_num_tr; ?></td>
                        <?
						$color_total=0;
						$color_total_order=0;

						foreach($nameArray_size  as $result_size)
						{
						$nameArray_color_size_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id =$order_id[$or] and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$result_color[csf('color_number_id')]."  and item_number_id=$gmts_item[$c] and  status_active=1 and is_deleted =0");
						foreach($nameArray_color_size_qnty as $result_color_size_qnty)
                        {

                        ?>
                            <td style="border:1px solid black; text-align:right">
							<?
								if($result_color_size_qnty[csf('plan_cut_qnty')]!= "")
								{
									 echo number_format($result_color_size_qnty[csf('order_quantity')],0);
									 $color_total += $result_color_size_qnty[csf('plan_cut_qnty')] ;
									 $color_total_order += $result_color_size_qnty[csf('order_quantity')] ;
									 $item_grand_total+=$result_color_size_qnty[csf('plan_cut_qnty')];
									 $item_grand_total_order+=$result_color_size_qnty[csf('order_quantity')];
								     $grand_total +=$result_color_size_qnty[csf('plan_cut_qnty')];
									 $grand_total_order +=$result_color_size_qnty[csf('order_quantity')];
									 $color_size_qnty_array[$result_size[csf('size_number_id')]][$result_color['color_number_id']]=$result_color_size_qnty[csf('plan_cut_qnty')];
									 $color_size_order_qnty_array[$result_size[csf('size_number_id')]][$result_color[csf('color_number_id')]]=$result_color_size_qnty[csf('order_quantity')];
									 if (array_key_exists($result_size[csf('size_number_id')], $size_tatal))
									 {
											$size_tatal[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('plan_cut_qnty')];
											$size_tatal_order[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('order_quantity')];
									 }
									 else
									 {
										$size_tatal[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')];
										$size_tatal_order[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('order_quantity')];
									 }
									 if (array_key_exists($result_size[csf('size_number_id')], $item_size_tatal))
									 {
											$item_size_tatal[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('plan_cut_qnty')];
											$item_size_tatal_order[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('order_quantity')];
									 }
									 else
									 {
										$item_size_tatal[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')];
										$item_size_tatal_order[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('order_quantity')];
									 }
								}
								else echo "0";
							 ?>
							</td>

                    <?
						}
                        }
                        ?>
                        <td style="border:1px solid black; text-align:right"><? echo number_format(round($color_total_order),0); ?></td>
                         <td style="border:1px solid black; text-align:right"><? $excexss_per=($color_total-$color_total_order)/$color_total_order*100; echo number_format($excexss_per,2)." %"; ?></td>
                         <td style="border:1px solid black; text-align:right"><? echo number_format(round($color_total),0); ?></td>
                    </tr>
                    <?
                    }
					?>
                      <td align="center" style="border:1px solid black"><strong></strong></td>
                      <td align="center" style="border:1px solid black"><strong></strong></td>
                      <td align="center" style="border:1px solid black"><strong></strong></td>
                      <td align="center" style="border:1px solid black"><strong></strong></td>
                      <td align="center" style="border:1px solid black"><strong>Sub Total</strong></td>
                        <?
						foreach($nameArray_size  as $result_size)
                        {
                        ?>
                        <td style="border:1px solid black;  text-align:right"><? echo $item_size_tatal_order[$result_size[csf('size_number_id')]];  ?></td>
                        <?
                        }
                        ?>
                        <td  style="border:1px solid black;  text-align:right"><?  echo number_format(round($item_grand_total_order),0); ?></td>
                        <td  style="border:1px solid black;  text-align:right"><? $excess_item_gra_tot=($item_grand_total-$item_grand_total_order)/$item_grand_total_order*100; echo number_format($excess_item_gra_tot,2)." %"; ?></td>
                        <td  style="border:1px solid black;  text-align:right"><?  echo number_format(round($item_grand_total),0); ?></td>
                    </tr>
                    <?
					}
					}
                    ?>
                     <tr>
                        <td style="border:1px solid black" align="center" colspan="<? echo count($nameArray_size)+8; ?>"><strong>&nbsp;</strong></td>
                        </tr>
                    <tr>
                    <tr>
                    <td align="center" style="border:1px solid black"><strong></strong></td>
                    <td align="center" style="border:1px solid black"><strong></strong></td>
                    <td align="center" style="border:1px solid black"><strong></strong></td>
                    <td align="center" style="border:1px solid black"><strong></strong></td>
                        <td align="center" style="border:1px solid black"><strong>Grand Total</strong></td>
                        <?
						foreach($nameArray_size  as $result_size)
                        {
                        ?>
                        <td style="border:1px solid black;  text-align:right"><? echo $size_tatal_order[$result_size[csf('size_number_id')]];  ?></td>
                        <?
                        }
                        ?>
                        <td  style="border:1px solid black;  text-align:right"><?  echo number_format(round($grand_total_order),0); ?></td>
                        <td  style="border:1px solid black;  text-align:right"><? $excess_gra_tot= ($grand_total-$grand_total_order)/$grand_total_order*100; echo number_format($excess_gra_tot,2)." %"; ?></td>
                        <td  style="border:1px solid black;  text-align:right"><?  echo number_format(round($grand_total),0); ?></td>
                    </tr>
                </table>
                </fieldset>
                </div>
                </td>
                <td width="200" valign="top" align="left">
                <div id="div_size_color_matrix" style="float:left;">
            	<?
				$rmg_process_breakdown_arr=explode('_',$rmg_process_breakdown)
				?>
            	 	<fieldset id="" >
 				<legend>RMG Process Loss % </legend>
            	<table width="180" class="rpt_table" border="1" rules="all">
                <?
				if(number_format($rmg_process_breakdown_arr[8],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Cut Panel rejection <!-- Extra Cutting % breack Down 8-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[8],2);
				?>
                </td>
                </tr>
                <?
                }
				if(number_format($rmg_process_breakdown_arr[2],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Chest Printing <!-- Printing % breack Down 2-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[2],2);
				?>
                </td>
                </tr>
                <?
                }
				if(number_format($rmg_process_breakdown_arr[10],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Neck/Sleeve Printing <!-- New breack Down 10-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[10],2);
				?>
                </td>
                </tr>
                <?
                }
				if(number_format($rmg_process_breakdown_arr[1],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Embroidery   <!-- Embroidery  % breack Down 1-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[1],2);
				?>
                </td>
                </tr>
                <?
                }
				if(number_format($rmg_process_breakdown_arr[4],2)>0)
				{
				?>
                <tr>
                <td width="130">
                 Sewing /Input<!-- Sewing % breack Down 4-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[4],2);
				?>
                </td>
                </tr>
                <?
                }
				if(number_format($rmg_process_breakdown_arr[3],2)>0)
				{
				?>
                <tr>
                <td width="130">
                 Garments Wash <!-- Washing %breack Down 3-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[3],2);
				?>
                </td>
                </tr>
                <?
                }
				if(number_format($rmg_process_breakdown_arr[15],2)>0)
				{
				?>
                <tr>
                <td width="130">
                 Gmts Finishing <!-- Washing %breack Down 3-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[15],2);
				?>
                </td>
                </tr>
                <?
                }
				if(number_format($rmg_process_breakdown_arr[11],2)>0)
				{
				?>
                <tr>
                <td width="130">
                 Others <!-- New breack Down 11-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[11],2);
				?>
                </td>
                </tr>
                <?
                }
				$gmts_pro_sub_tot=$rmg_process_breakdown_arr[8]+$rmg_process_breakdown_arr[2]+$rmg_process_breakdown_arr[10]+$rmg_process_breakdown_arr[1]+$rmg_process_breakdown_arr[4]+$rmg_process_breakdown_arr[3]+$rmg_process_breakdown_arr[11]+$rmg_process_breakdown_arr[15];
				if($gmts_pro_sub_tot>0)
				{
				?>
                <tr>
                <td width="130">
                Sub Total <!-- New breack Down 11-->
                </td>
                <td align="right">
                <?

				echo number_format($gmts_pro_sub_tot,2);
				?>
                </td>
                </tr>
                <?
				}
				?>
                </table>
                </fieldset>


                <fieldset id="" >
 				<legend>Fabric Process Loss % </legend>
            	<table width="180" class="rpt_table" border="1" rules="all">
                 <?
				if(number_format($rmg_process_breakdown_arr[6],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Knitting  <!--  Knitting % breack Down 6-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[6],2);
				?>
                </td>
                </tr>
                <?
				}
				if(number_format($rmg_process_breakdown_arr[12],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Yarn Dyeing  <!--  New breack Down 12-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[12],2);
				?>
                </td>
                </tr>
                <?
				}
				if(number_format($rmg_process_breakdown_arr[5],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Dyeing & Finishing  <!-- Finishing % breack Down 5-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[5],2);
				?>
                </td>
                </tr>
                <?
				}
				if(number_format($rmg_process_breakdown_arr[13],2)>0)
				{
				?>
                <tr>
                <td width="130">
                All Over Print <!-- new  breack Down 13-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[13],2);
				?>
                </td>
                </tr>
                <?
				}
				if(number_format($rmg_process_breakdown_arr[14],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Lay Wash (Fabric) <!-- new  breack Down 14-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[14],2);
				?>
                </td>
                </tr>
                <?
				}
				if(number_format($rmg_process_breakdown_arr[7],2)>0)
				{
				?>
                 <tr>
                <td width="130">
                Dying   <!-- breack Down 7-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[7],2);
				?>
                </td>
                </tr>
                <?
				}
				if(number_format($rmg_process_breakdown_arr[0],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Cutting (Fabric) <!-- Cutting % breack Down 0-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[0],2);
				?>
                </td>
                </tr>
                <?
				}
				if(number_format($rmg_process_breakdown_arr[9],2)>0)
				{
				?>
                <tr>
                <td width="130">
               Others  <!-- Others% breack Down 9-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[9],2);
				?>
                </td>
                </tr>
                <?
				}
				$fab_proce_sub_tot=$rmg_process_breakdown_arr[6]+$rmg_process_breakdown_arr[12]+$rmg_process_breakdown_arr[5]+$rmg_process_breakdown_arr[13]+$rmg_process_breakdown_arr[14]+$rmg_process_breakdown_arr[7]+$rmg_process_breakdown_arr[0]+$rmg_process_breakdown_arr[9];
				if(fab_proce_sub_tot>0)
				{
				?>
                <tr>
                <td width="130">
                Sub Total  <!-- Others% breack Down 9-->
                </td>
                <td align="right">
                <?

				echo number_format($fab_proce_sub_tot,2);
				?>
                </td>
                </tr>
                <?
				}
				if($gmts_pro_sub_tot+$fab_proce_sub_tot>0)
				{
				?>
                 <tr>
                <td width="130">
                Grand Total  <!-- Others% breack Down 9-->
                </td>
                <td align="right">
                <?
				echo number_format($gmts_pro_sub_tot+$fab_proce_sub_tot,2);
				?>
                </td>
                </tr>
                <?
				}
				?>
           </table>
           </fieldset>
           </div>
                </td>
            <td width="330" valign="top" align="left">
            <?
			$nameArray_imge =sql_select("SELECT image_location FROM common_photo_library where master_tble_id='$job_no'");
			?>
            <div id="div_size_color_matrix" style="float:left;">
            	<fieldset id="" >
 				<legend>Image </legend>
            	<table width="310">
                <tr>
                <?
				$img_counter = 0;
                foreach($nameArray_imge as $result_imge)
				{

					?>
					<td>
						<img src="../../<? echo $result_imge[csf('image_location')]; ?>" width="90" height="100" border="2" />
					</td>
					<?

					$img_counter++;
				}
				?>
                </tr>
           </table>
           </fieldset>
           </div>
          </td>
        </tr>
       </table>
      <br/>   									 <!--  Here will be the main portion  -->
    <strong>Grey Fabric Details</strong>
    <table  width="100%"  border="0" cellpadding="0" cellspacing="0" >
    <tr>
    <td width="49%">
     <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
       <tr>
            <td  width="120" align="left">Fabric Color</td>
            <td  width="120" align="left">Fabric</td>
            <td  width="120" align="left">Composition</td>
            <td  width="120" align="left">GSM</td>
            <td  width="120" align="left">Process Loss</td>
			<? foreach($nameArray_size  as $result_size){?>
            <td align="center"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
            <? } ?>
            <td   width="50">Total (Kg)</td>
       </tr>
       <?

		$costing_per="";
		$costing_per_qnty=0;
		$costing_per_id=return_field_value( "costing_per", "wo_pre_cost_mst","job_no ='$job_no'");
		if($costing_per_id==1)
		{
		$costing_per="1 Dzn";
		$costing_per_qnty=12;

		}
		if($costing_per_id==2)
		{
		$costing_per="1 Pcs";
		$costing_per_qnty=1;

		}
		if($costing_per_id==3)
		{
		$costing_per="2 Dzn";
		$costing_per_qnty=24;

		}
		if($costing_per_id==4)
		{
		$costing_per="3 Dzn";
		$costing_per_qnty=36;

		}
		if($costing_per_id==5)
		{
		$costing_per="4 Dzn";
		$costing_per_qnty=48;

		}
		$process_loss_method=return_field_value( "process_loss_method", "wo_pre_cost_fabric_cost_dtls","job_no='$job_no'");

		    $wo_pre_cost_fabric_cost_dtls_id=array();
	        $grand_total_fin_fab_qnty=0;
			$grand_total_grey_fab_qnty=0;
			$grand_totalcons_per_finish=0;
			$grand_totalcons_per_grey=0;

				/*echo "select d.fabric_color_id, a.id,a.body_part_id, a.composition, a.construction, a.gsm_weight,a.width_dia_type as width_dia_type,a.color_size_sensitive, avg(b.cons) as cons  , avg(b.process_loss_percent) as process_loss_percent , avg(b.requirment) as requirment ,min(c.id) as cid FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d
				WHERE a.job_no=b.job_no and
				a.id=b.pre_cost_fabric_cost_dtls_id and
				c.job_no_mst=a.job_no and
				c.id=b.color_size_table_id and
				b.po_break_down_id=d.po_break_down_id and
				b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
				a.body_part_id in(1,20) and
				d.booking_no =$txt_reqsn_no and
				d.status_active=1 and
				d.is_deleted=0
				group by a.id,a.body_part_id, a.composition, a.construction, a.gsm_weight,a.width_dia_type,a.color_size_sensitive,d.fabric_color_id order by cid ";*/
			$color_wise_wo_sql=sql_select("select d.fabric_color_id, a.id,a.body_part_id, a.composition, a.construction, a.gsm_weight,a.width_dia_type as width_dia_type,a.color_size_sensitive, avg(b.cons) as cons  , avg(b.process_loss_percent) as process_loss_percent , avg(b.requirment) as requirment ,min(c.id) as cid FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d
				WHERE a.job_no=b.job_no and
				a.id=b.pre_cost_fabric_cost_dtls_id and
				c.job_no_mst=a.job_no and
				c.id=b.color_size_table_id and
				b.po_break_down_id=d.po_break_down_id and
				b.color_number_id=d.gmts_color_id and
				b.gmts_sizes=d.gmts_size and
				b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
				a.body_part_id in(1,20) and
				d.booking_no =$txt_reqsn_no and
				d.status_active=1 and
				d.is_deleted=0
				group by a.id,a.body_part_id, a.composition, a.construction, a.gsm_weight,a.width_dia_type,a.color_size_sensitive,d.fabric_color_id order by cid ");
		foreach($color_wise_wo_sql as $color_wise_wo_result)
	    {

			$wo_pre_cost_fabric_cost_dtls_id[$color_wise_wo_result[csf('id')]]=$color_wise_wo_result[csf('id')];
			$fabric_color_id="";
			if($color_wise_wo_result[csf('color_size_sensitive')]==3)
			{
			$fabric_color_id=return_field_value( "gmts_color_id", "wo_pre_cos_fab_co_color_dtls","pre_cost_fabric_cost_dtls_id='".$color_wise_wo_result[csf('id')]."' and contrast_color_id='".$color_wise_wo_result[csf('fabric_color_id')]."'");
			}
			else
			{
			$fabric_color_id=$color_wise_wo_result[csf('fabric_color_id')];
			}


			$sql_dia_array=array();
		  // $sql_dia=sql_select("Select dia_width,gmts_sizes from wo_pre_cos_fab_co_avg_con_dtls where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and pre_cost_fabric_cost_dtls_id='".$color_wise_wo_result[csf('id')]."' and color_number_id='".$fabric_color_id."'");
		   $sql_dia=sql_select("Select dia_width,gmts_size from wo_booking_dtls where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and pre_cost_fabric_cost_dtls_id='".$color_wise_wo_result[csf('id')]."' and fabric_color_id='".$fabric_color_id."' and booking_no =$txt_reqsn_no" );

		   foreach($sql_dia as $sql_dia_row)
		   {
			$sql_dia_array[$sql_dia_row[csf("gmts_size")]][$sql_dia_row[csf("dia_width")]]= $sql_dia_row[csf("dia_width")];
		   }
		?>
			<tr>
            <td  width="120" align="left">
			<?
			echo $fabric_typee[$color_wise_wo_result[csf('width_dia_type')]];
			?>
            </td>
            <td  width="120" align="left">&nbsp;

            </td>
            <td>&nbsp;

            </td>
            <td  width="120" align="left">&nbsp;

            </td>

            <td  width="120" align="left">&nbsp;

            </td>
            <?
			$total_fin_fab_qnty=0;
			$total_grey_fab_qnty=0;

			foreach($nameArray_size  as $result_size)
		    {
			?>

            <td width='50' align='' >&nbsp;
            <?
			$dia=implode(",", $sql_dia_array[$result_size[csf('size_number_id')]]);
			echo $dia;
			?>
            </td>
            <?
			}
			?>

            <td align="right">&nbsp;

            </td>
            </tr>

            <tr>
            <td  width="120" align="left">
			<?
			echo $color_library[$color_wise_wo_result[csf('fabric_color_id')]];
			//echo $color_library[$fabric_color_id];

			?>
            </td>
            <td  width="120" align="left">
			<?
			echo $color_wise_wo_result[csf('construction')];
			?>
            </td>
            <td>
            <?
			echo $color_wise_wo_result[csf('composition')];
			?>
            </td>
            <td  width="120" align="left">
			<?
			echo $color_wise_wo_result[csf('gsm_weight')];
			?>
            </td>

            <td  width="120" align="left">
			<?
			echo $color_wise_wo_result[csf('process_loss_percent')];
			?>
            </td>
            <?
			$total_fin_fab_qnty=0;
			$total_grey_fab_qnty=0;

			foreach($nameArray_size  as $result_size)
		    {
				$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d
				WHERE a.job_no=b.job_no and
				a.id=b.pre_cost_fabric_cost_dtls_id and
				c.job_no_mst=a.job_no and
				c.id=b.color_size_table_id and
				b.po_break_down_id=d.po_break_down_id and
				b.color_number_id=d.gmts_color_id and
				b.gmts_sizes=d.gmts_size and
				b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
				d.booking_no =$txt_reqsn_no and
				a.id='".$color_wise_wo_result[csf('id')]."' and
				c.size_number_id='".$result_size[csf('size_number_id')]."' and
				d.fabric_color_id=".$color_wise_wo_result[csf('fabric_color_id')]." and
				d.status_active=1 and
				d.is_deleted=0
				");
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
			?>

            <td width='50' align='right' >
			<?
			if($color_wise_wo_result_qnty[csf('grey_fab_qnty')]!="")
			{
			echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],4);
			$total_grey_fab_qnty+=$color_wise_wo_result_qnty[csf('grey_fab_qnty')];
			}
			?>
            </td>
            <?
			}
			?>

            <td align="right"><? echo number_format($total_grey_fab_qnty,4); $grand_total_grey_fab_qnty+=$total_grey_fab_qnty;?></td>


            </tr>
         <?
		}
		?>
        <tr style=" font-weight:bold">
        <!--<td  width="120" align="left">&nbsp;</td>-->
        <th  width="120" align="left">&nbsp;</th>
        <td  width="120" align="left">&nbsp;</td>
        <td  width="120" align="left">&nbsp;</td>
        <td  width="120" align="left">&nbsp;</td>
        <td  width="120" align="left"><strong>Total</strong></td>
        <?
			foreach($nameArray_size  as $result_size)
		    {
				$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d
												WHERE a.job_no=b.job_no and
												a.id=b.pre_cost_fabric_cost_dtls_id and
												c.job_no_mst=a.job_no and
												c.id=b.color_size_table_id and
												b.po_break_down_id=d.po_break_down_id and
												b.color_number_id=d.gmts_color_id and
				                                b.gmts_sizes=d.gmts_size and
												b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
												d.booking_no =$txt_reqsn_no and
												a.body_part_id in(1,20) and
												c.size_number_id='".$result_size[csf('size_number_id')]."' and
												d.status_active=1 and
												d.is_deleted=0
												");
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
			?>
			<td width='50' align='right' > <? echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],4);?></td>
            <?
			}
			?>

            <td align="right"><? echo number_format($grand_total_grey_fab_qnty,4);?></td>

            </tr>
    </table>
 </td>
 <td width="2%"></td>
 <td width="49%" valign="top">
 <?
 $wo_pre_cost_fabric_cost_dtls_id_main_fabric=implode(",", $wo_pre_cost_fabric_cost_dtls_id);
 if($wo_pre_cost_fabric_cost_dtls_id_main_fabric=="")
 {
	 $wo_pre_cost_fabric_cost_dtls_id_main_fabric=0;
 }
     $nameArray_fabric_description= sql_select("select a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight,min(a.width_dia_type) as width_dia_type, b.dia_width, avg(b.cons) as cons  , avg(b.process_loss_percent) as process_loss_percent , avg(b.requirment) as requirment,min(c.id) as cid FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d
WHERE a.job_no=b.job_no and
a.id=b.pre_cost_fabric_cost_dtls_id and
c.job_no_mst=a.job_no and
c.id=b.color_size_table_id and
b.po_break_down_id=d.po_break_down_id and
b.color_number_id=d.gmts_color_id and
b.gmts_sizes=d.gmts_size and
b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
d.booking_no =$txt_reqsn_no and
a.body_part_id not in(1,20) and
d.status_active=1 and
d.is_deleted=0
group by a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width order by a.body_part_id,b.dia_width,cid");
	 ?>

     <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
     <tr align="center">
     <td width='50'></td>
        <?
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('body_part_id')] == "")  echo "<td  colspan='2'>&nbsp</td>";
			else         		               echo "<td  colspan='2'>". $body_part[$result_fabric_description[csf('body_part_id')]].",".$result_fabric_description[csf('construction')]."</td>";
		}
		?>



       </tr>


        <tr align="center">
        <td width='50'></td>
        <?
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('composition')] == "")   echo "<td colspan='2' >&nbsp</td>";
			else         		               echo "<td colspan='2' >".$result_fabric_description[csf('composition')].",". $color_type[$result_fabric_description[csf('color_type_id')]].",". $result_fabric_description[csf('gsm_weight')]."</td>";
		}
		?>

       </tr>

       <tr align="center">
       <td width='50'></td>
        <?
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('dia_width')] == "")   echo "<td colspan='2'>&nbsp</td>";
			else         		              echo "<td colspan='2' align='center'>".$fabric_typee[$result_fabric_description[csf('width_dia_type')]].",". $result_fabric_description[csf('dia_width')].",".number_format($result_fabric_description[csf('requirment')],4)."</td>";
		}
		?>

       </tr>
       <tr>
       <th width='50'>Gmt Color</th>
        <?
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			  echo "<th width='50'>Color</th><th width='50' >Qty</th>";
		}
		?>

       </tr>
       <?

		  $gmt_color_library=array();
		  $gmt_color_data=sql_select("select gmts_color_id,contrast_color_id
		  FROM
		  wo_pre_cos_fab_co_color_dtls
		  WHERE
		  job_no ='$job_no'");
		  foreach( $gmt_color_data as $gmt_color_row)
		  {
			$gmt_color_library[$gmt_color_row[csf("contrast_color_id")]].=$color_library[$gmt_color_row[csf("gmts_color_id")]]."," ;
		  }

	        $grand_total_fin_fab_qnty=0;
			$grand_total_grey_fab_qnty=0;
			$grand_totalcons_per_finish=0;
			$grand_totalcons_per_grey=0;
			$color_wise_wo_sql=sql_select("select fabric_color_id
										  FROM
										  wo_booking_dtls
										  WHERE
										  booking_no =$txt_reqsn_no and
										  pre_cost_fabric_cost_dtls_id not in($wo_pre_cost_fabric_cost_dtls_id_main_fabric) and
										  status_active=1 and
                                          is_deleted=0
										  group by fabric_color_id");
		foreach($color_wise_wo_sql as $color_wise_wo_result)
	    {
		?>
			<tr>
            <td width='50' align='right'>
			<?
			//if($color_wise_wo_result_qnty[csf('fin_fab_qnty')]!="")
			//{
				if($gmt_color_library[$color_wise_wo_result[csf('fabric_color_id')]]!="")
				{
					echo rtrim($gmt_color_library[$color_wise_wo_result[csf('fabric_color_id')]],",");
				}
				else
				{
					echo $color_library[$color_wise_wo_result[csf('fabric_color_id')]];
				}
			//}
			?>
            </td>
            <?
			$total_fin_fab_qnty=0;
			$total_grey_fab_qnty=0;

			foreach($nameArray_fabric_description as $result_fabric_description)
		    {
				$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d
				WHERE a.job_no=b.job_no and
				a.id=b.pre_cost_fabric_cost_dtls_id and
				c.job_no_mst=a.job_no and
				c.id=b.color_size_table_id and
				b.po_break_down_id=d.po_break_down_id and
				b.color_number_id=d.gmts_color_id and
				b.gmts_sizes=d.gmts_size and
				b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
				d.booking_no =$txt_reqsn_no and
				a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
				a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
				a.construction='".$result_fabric_description[csf('construction')]."' and
				a.composition='".$result_fabric_description[csf('composition')]."' and
				a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
				b.dia_width='".$result_fabric_description[csf('dia_width')]."' and
				d.fabric_color_id=".$color_wise_wo_result[csf('fabric_color_id')]." and
				d.status_active=1 and
				d.is_deleted=0
				");
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
			?>

			<td width='50' align='right'>
			<?
			if($color_wise_wo_result_qnty[csf('fin_fab_qnty')]!="")
			{
			echo $color_library[$color_wise_wo_result[csf('fabric_color_id')]];
			}
			?>
            </td>
            <td width='50' align='right' >
			<?
			if($color_wise_wo_result_qnty[csf('grey_fab_qnty')]!="")
			{
			echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],4);
			$total_grey_fab_qnty+=$color_wise_wo_result_qnty[csf('grey_fab_qnty')];
			}
			?>
            </td>
            <?
			}
			?>
            </tr>
         <?
		}
		?>
        <tr style=" font-weight:bold">

        <td width='50' align='right'></td>
        <?
			foreach($nameArray_fabric_description as $result_fabric_description)
		    {
				$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d
												WHERE a.job_no=b.job_no and
												a.id=b.pre_cost_fabric_cost_dtls_id and
												c.job_no_mst=a.job_no and
												c.id=b.color_size_table_id and
												b.po_break_down_id=d.po_break_down_id and
												b.color_number_id=d.gmts_color_id and
				                                b.gmts_sizes=d.gmts_size and
												b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
												d.booking_no =$txt_reqsn_no and
												a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
												a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
												a.construction='".$result_fabric_description[csf('construction')]."' and
												a.composition='".$result_fabric_description[csf('composition')]."' and
												a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
												b.dia_width='".$result_fabric_description[csf('dia_width')]."' and
												d.status_active=1 and
												d.is_deleted=0
												");
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
			?>
			<td width='50' align='right'></td><td width='50' align='right' > <? echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],4);?></td>
            <?
			}
			?>



            </tr>
    </table>
 </td>
 </tr>
 </table>
    <br/>   									 <!--  Here will be the main portion  -->
    <strong>Finish Fabric Details</strong>
    <table  width="100%"  border="0" cellpadding="0" cellspacing="0" >
    <tr>
    <td width="49%">
     <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
       <tr>
            <td  width="120" align="left">Fabric Color</td>
            <td  width="120" align="left">Fabric</td>
            <td  width="120" align="left">Composition</td>
            <td  width="120" align="left">GSM</td>
            <td  width="120" align="left">Process Loss</td>
			<?
            foreach($nameArray_size  as $result_size)
            {?>
            <td align="center"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
            <? } ?>
            <td   width="50">Total (Kg)</td>
       </tr>
       <?
	   $wo_pre_cost_fabric_cost_dtls_id=array();
	        $grand_total_fin_fab_qnty=0;
			$grand_total_grey_fab_qnty=0;
			$grand_totalcons_per_finish=0;
			$grand_totalcons_per_grey=0;
			$color_wise_wo_sql=sql_select("select d.fabric_color_id, a.id,a.body_part_id, a.composition, a.construction, a.gsm_weight,a.width_dia_type as width_dia_type,a.color_size_sensitive,  avg(b.cons) as cons, avg(b.process_loss_percent) as process_loss_percent , avg(b.requirment) as requirment,min(c.id) as cid  FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d
				WHERE a.job_no=b.job_no and
				a.id=b.pre_cost_fabric_cost_dtls_id and
				c.job_no_mst=a.job_no and
				c.id=b.color_size_table_id and
				b.po_break_down_id=d.po_break_down_id and
				b.color_number_id=d.gmts_color_id and
				b.gmts_sizes=d.gmts_size and
				b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
				a.body_part_id in(1,20) and
				d.booking_no =$txt_reqsn_no and
				d.status_active=1 and
				d.is_deleted=0
				group by a.id,a.body_part_id, a.composition, a.construction, a.gsm_weight,a.width_dia_type,a.color_size_sensitive,d.fabric_color_id order by cid");
		foreach($color_wise_wo_sql as $color_wise_wo_result)
	    {
			$wo_pre_cost_fabric_cost_dtls_id[$color_wise_wo_result[csf('id')]]=$color_wise_wo_result[csf('id')];
			$fabric_color_id="";
			if($color_wise_wo_result[csf('color_size_sensitive')]==3)
			{
			$fabric_color_id=return_field_value( "gmts_color_id", "wo_pre_cos_fab_co_color_dtls","pre_cost_fabric_cost_dtls_id='".$color_wise_wo_result[csf('id')]."' and contrast_color_id='".$color_wise_wo_result[csf('fabric_color_id')]."'");
			}
			else
			{
			$fabric_color_id=$color_wise_wo_result[csf('fabric_color_id')];
			}
			//echo "Select dia_width,gmts_sizes from wo_pre_cos_fab_co_avg_con_dtls where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and pre_cost_fabric_cost_dtls_id='".$color_wise_wo_result[csf('id')]."' and color_number_id='".$fabric_color_id."'";
			$sql_dia_array=array();
		  // $sql_dia=sql_select("Select dia_width,gmts_sizes from wo_pre_cos_fab_co_avg_con_dtls where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and pre_cost_fabric_cost_dtls_id='".$color_wise_wo_result[csf('id')]."' and color_number_id='".$fabric_color_id."'");
		  	$sql_dia=sql_select("Select dia_width,gmts_size from wo_booking_dtls where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and pre_cost_fabric_cost_dtls_id='".$color_wise_wo_result[csf('id')]."' and fabric_color_id='".$fabric_color_id."' and booking_no =$txt_reqsn_no" );

		   foreach($sql_dia as $sql_dia_row)
		   {
			$sql_dia_array[$sql_dia_row[csf("gmts_size")]][$sql_dia_row[csf("dia_width")]]= $sql_dia_row[csf("dia_width")];
		   }
		?>
			<tr>
            <td  width="120" align="left">
			<?
			echo $fabric_typee[$color_wise_wo_result[csf('width_dia_type')]];
			?>
            </td>
            <td  width="120" align="left">&nbsp;

            </td>
            <td>&nbsp;

            </td>
            <td  width="120" align="left">&nbsp;

            </td>

            <td  width="120" align="left">&nbsp;

            </td>
            <?
			$total_fin_fab_qnty=0;
			$total_grey_fab_qnty=0;

			foreach($nameArray_size  as $result_size)
		    {
			?>

            <td width='50' align='' >&nbsp;
			<?
			$dia=implode(",", $sql_dia_array[$result_size[csf('size_number_id')]]);
			echo $dia;
			?>
            </td>
            <?
			}
			?>

            <td align="right">&nbsp;

            </td>


            </tr>



            <tr>
            <td  width="120" align="left">
			<?
			echo $color_library[$color_wise_wo_result[csf('fabric_color_id')]];
			//echo $color_library[$fabric_color_id];
			?>
            </td>
            <td  width="120" align="left">
			<?
			echo $color_wise_wo_result[csf('construction')];
			?>
            </td>
            <td>
            <?
			echo $color_wise_wo_result[csf('composition')];
			?>
            </td>
            <td  width="120" align="left">
			<?
			echo $color_wise_wo_result[csf('gsm_weight')];
			?>
            </td>

            <td  width="120" align="left">
			<?
			echo $color_wise_wo_result[csf('process_loss_percent')];
			?>
            </td>
            <?
			$total_fin_fab_qnty=0;
			$total_grey_fab_qnty=0;

			foreach($nameArray_size  as $result_size)
		    {
				$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d
				WHERE a.job_no=b.job_no and
				a.id=b.pre_cost_fabric_cost_dtls_id and
				c.job_no_mst=a.job_no and
				c.id=b.color_size_table_id and
				b.po_break_down_id=d.po_break_down_id and
				b.color_number_id=d.gmts_color_id and
				b.gmts_sizes=d.gmts_size and
				b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
				d.booking_no =$txt_reqsn_no and
				a.id='".$color_wise_wo_result[csf('id')]."' and
				c.size_number_id='".$result_size[csf('size_number_id')]."' and
				d.fabric_color_id=".$color_wise_wo_result[csf('fabric_color_id')]." and
				d.status_active=1 and
				d.is_deleted=0
				");
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
			?>

            <td width='50' align='right' >
			<?
			if($color_wise_wo_result_qnty[csf('fin_fab_qnty')]!="")
			{
			echo number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')],4);
			$total_fin_fab_qnty+=$color_wise_wo_result_qnty[csf('fin_fab_qnty')];
			}
			?>
            </td>
            <?
			}
			?>

            <td align="right"><? echo number_format($total_fin_fab_qnty,2); $grand_total_fin_fab_qnty+=$total_fin_fab_qnty;?></td>


            </tr>
         <?
		}
		?>
        <tr style=" font-weight:bold">
        <!--<td  width="120" align="left">&nbsp;</td>-->
        <th  width="120" align="left">&nbsp;</th>
        <td  width="120" align="left">&nbsp;</td>
        <td  width="120" align="left">&nbsp;</td>
        <td  width="120" align="left">&nbsp;</td>
        <td  width="120" align="left"><strong>Total</strong></td>
        <?
			foreach($nameArray_size  as $result_size)
		    {
				$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty,avg(b.cons) as avg_cons FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d
												WHERE a.job_no=b.job_no and
												a.id=b.pre_cost_fabric_cost_dtls_id and
												c.job_no_mst=a.job_no and
												c.id=b.color_size_table_id and
												b.po_break_down_id=d.po_break_down_id and
												b.color_number_id=d.gmts_color_id and
				                                b.gmts_sizes=d.gmts_size and
												b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
												d.booking_no =$txt_reqsn_no and
												a.body_part_id in(1,20) and
												c.size_number_id='".$result_size[csf('size_number_id')]."' and
												d.status_active=1 and
												d.is_deleted=0
												");
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
			?>
			<td width='50' align='right' > <? echo number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')],4);?></td>
            <?
			}
			?>
            <td align="right"><? echo number_format($grand_total_fin_fab_qnty,4);?></td>

            </tr>
            <tr style=" font-weight:bold">
        <!--<td  width="120" align="left">&nbsp;</td>-->
        <th  width="120" align="left">&nbsp;</th>
        <td  width="120" align="left">&nbsp;</td>
        <td  width="120" align="left">&nbsp;</td>
        <td  width="120" align="left">&nbsp;</td>
        <td  width="120" align="left"><strong>Consumption</strong></td>
        <?
			foreach($nameArray_size  as $result_size)
		    {
				$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty,avg(b.cons) as avg_cons FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d
												WHERE a.job_no=b.job_no and
												a.id=b.pre_cost_fabric_cost_dtls_id and
												c.job_no_mst=a.job_no and
												c.id=b.color_size_table_id and
												b.po_break_down_id=d.po_break_down_id and
												b.color_number_id=d.gmts_color_id and
				                                b.gmts_sizes=d.gmts_size and
												b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
												d.booking_no =$txt_reqsn_no and
												a.body_part_id in(1,20) and
												c.size_number_id='".$result_size[csf('size_number_id')]."' and
												d.status_active=1 and
												d.is_deleted=0
												");
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
			?>
			<td width='50' align='right' > <? echo number_format($color_wise_wo_result_qnty[csf('avg_cons')],4);?></td>
            <?
			}
			?>
            <td align="right"><? //echo number_format($grand_total_fin_fab_qnty,2);?></td>

            </tr>


    </table>
    </td>
    <td width="2%"></td>
    <td width="49%" valign="top">
 <?
// print_r($wo_pre_cost_fabric_cost_dtls_id);
 $wo_pre_cost_fabric_cost_dtls_id_main_fabric=implode(",", $wo_pre_cost_fabric_cost_dtls_id);
 if($wo_pre_cost_fabric_cost_dtls_id_main_fabric=="")
 {
	 $wo_pre_cost_fabric_cost_dtls_id_main_fabric=0;
 }

 $nameArray_fabric_description= sql_select("select a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight,min(a.width_dia_type) as width_dia_type, b.dia_width, avg(b.cons) as cons  , avg(b.process_loss_percent) as process_loss_percent , avg(b.requirment) as requirment,min(c.id) as cid FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d
WHERE a.job_no=b.job_no and
a.id=b.pre_cost_fabric_cost_dtls_id and
c.job_no_mst=a.job_no and
c.id=b.color_size_table_id and
b.po_break_down_id=d.po_break_down_id and
b.color_number_id=d.gmts_color_id and
b.gmts_sizes=d.gmts_size and
b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
d.booking_no =$txt_reqsn_no and
a.body_part_id not in(1,20) and
d.status_active=1 and
d.is_deleted=0
group by a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width order by a.body_part_id,b.dia_width,cid");
	 ?>

     <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
     <tr align="center">
     <td width='50'></td>
        <?
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('body_part_id')] == "")  echo "<td  colspan='2'>&nbsp</td>";
			else         		               echo "<td  colspan='2'>". $body_part[$result_fabric_description[csf('body_part_id')]].",".$result_fabric_description[csf('construction')]."</td>";
		}
		?>



       </tr>

       <tr align="center">
       <td width='50'></td>
        <?
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('composition')] == "")   echo "<td colspan='2' >&nbsp</td>";
			else         		               echo "<td colspan='2' >".$result_fabric_description[csf('composition')].",". $color_type[$result_fabric_description[csf('color_type_id')]].",". $result_fabric_description[csf('gsm_weight')]."</td>";
		}
		?>

       </tr>

       <tr align="center">
       <td width='50'></td>
        <?
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('dia_width')] == "")   echo "<td colspan='2'>&nbsp</td>";
			else         		              echo "<td colspan='2' align='center'>".$fabric_typee[$result_fabric_description[csf('width_dia_type')]].",". $result_fabric_description[csf('dia_width')].",".number_format($result_fabric_description[csf('cons')],4)."</td>";
		}
		?>

       </tr>
       <tr>
       <th width='50'>Gmt Color</th>
        <?
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			  echo "<th width='50'>Color</th><th width='50' >Qty</th>";
		}
		?>

       </tr>
       <?

		  $gmt_color_library=array();
		  $gmt_color_data=sql_select("select gmts_color_id,contrast_color_id
		  FROM
		  wo_pre_cos_fab_co_color_dtls
		  WHERE
		  job_no ='$job_no'");
		  foreach( $gmt_color_data as $gmt_color_row)
		  {
			$gmt_color_library[$gmt_color_row[csf("contrast_color_id")]].=$color_library[$gmt_color_row[csf("gmts_color_id")]]."," ;
		  }

	        $grand_total_fin_fab_qnty=0;
			$grand_total_grey_fab_qnty=0;
			$grand_totalcons_per_finish=0;
			$grand_totalcons_per_grey=0;
			$color_wise_wo_sql=sql_select("select fabric_color_id
										  FROM
										  wo_booking_dtls
										  WHERE
										  booking_no =$txt_reqsn_no and
										  pre_cost_fabric_cost_dtls_id not in($wo_pre_cost_fabric_cost_dtls_id_main_fabric) and
										  status_active=1 and
                                          is_deleted=0
										  group by fabric_color_id");
		foreach($color_wise_wo_sql as $color_wise_wo_result)
	    {
		?>
			<tr>
            <td width='50' align='right'>
			<?
			//if($color_wise_wo_result_qnty[csf('fin_fab_qnty')]!="")
			//{
				if($gmt_color_library[$color_wise_wo_result[csf('fabric_color_id')]]!="")
				{

				echo rtrim($gmt_color_library[$color_wise_wo_result[csf('fabric_color_id')]],",");
				}
				else
				{
					echo $color_library[$color_wise_wo_result[csf('fabric_color_id')]];
				}
			//}
			?>
            </td>
            <?
			$total_fin_fab_qnty=0;
			$total_grey_fab_qnty=0;

			foreach($nameArray_fabric_description as $result_fabric_description)
		    {
				$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d
				WHERE a.job_no=b.job_no and
				a.id=b.pre_cost_fabric_cost_dtls_id and
				c.job_no_mst=a.job_no and
				c.id=b.color_size_table_id and
				b.po_break_down_id=d.po_break_down_id and
				b.color_number_id=d.gmts_color_id and
				b.gmts_sizes=d.gmts_size and
				b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
				d.booking_no =$txt_reqsn_no and
				a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
				a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
				a.construction='".$result_fabric_description[csf('construction')]."' and
				a.composition='".$result_fabric_description[csf('composition')]."' and
				a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
				b.dia_width='".$result_fabric_description[csf('dia_width')]."' and
				d.fabric_color_id=".$color_wise_wo_result[csf('fabric_color_id')]." and
				d.status_active=1 and
				d.is_deleted=0
				");
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
			?>

			<td width='50' align='right'>
			<?
			if($color_wise_wo_result_qnty[csf('fin_fab_qnty')]!="")
			{

			echo $color_library[$color_wise_wo_result[csf('fabric_color_id')]];
			}
			?>
            </td>
            <td width='50' align='right' >
			<?
			if($color_wise_wo_result_qnty[csf('fin_fab_qnty')]!="")
			{
			echo number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')],4);
			//$total_grey_fab_qnty+=$color_wise_wo_result_qnty[csf('fin_fab_qnty')];
			}
			?>
            </td>
            <?
			}
			?>




            </tr>
         <?
		}
		?>
        <tr style=" font-weight:bold">
        <td width='50' align='right'></td>

        <?
			foreach($nameArray_fabric_description as $result_fabric_description)
		    {
				$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d
												WHERE a.job_no=b.job_no and
												a.id=b.pre_cost_fabric_cost_dtls_id and
												c.job_no_mst=a.job_no and
												c.id=b.color_size_table_id and
												b.po_break_down_id=d.po_break_down_id and
												b.color_number_id=d.gmts_color_id and
				                                b.gmts_sizes=d.gmts_size and
												b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
												d.booking_no =$txt_reqsn_no and
												a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
												a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
												a.construction='".$result_fabric_description[csf('construction')]."' and
												a.composition='".$result_fabric_description[csf('composition')]."' and
												a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
												b.dia_width='".$result_fabric_description[csf('dia_width')]."' and
												d.status_active=1 and
												d.is_deleted=0
												");
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
			?>
			<td width='50' align='right'></td><td width='50' align='right' > <? echo number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')],4);?></td>
            <?
			}
			?>



            </tr>
    </table>
 </td>
    </tr>
    </table>

   <br/>
   <table  width="100%"  border="0" cellpadding="0" cellspacing="0" >
        <tr>
        <?

		$nameArray_item_size=sql_select( "select b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_reqsn_no  and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id  and d.status_active=1 and d.is_deleted=0 group by b.item_size,c.size_number_id order by c.id");

		if(count($nameArray_item_size)>0)
		{
        ?>
        <td width="49%">
        <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
        <tr>
        <td colspan="<? echo count($nameArray_size)+4;?>" align="center"><b>Collar -  Colour Size Brakedown in Pcs</b></td>
        </tr>
        <tr>
        <td width="70">Size</td>

        <?

		/*$nameArray_item_size=sql_select( "select b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_reqsn_no  and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 group by c.size_number_id,b.item_size order by c.size_number_id");*/
		foreach($nameArray_item_size  as $result_size)
		{
		?>
		<td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
		<?
        }
        ?>
        <td rowspan="2" align="center"><strong>Total</strong></td>
        <td width="60" rowspan="2" align="center"><strong>Extra %</strong></td>
        </tr>
        <tr>
        <td>Collar Size</td>

        <?
        foreach($nameArray_item_size  as $result_item_size)
		{
		?>
		<td align="center" style="border:1px solid black"><strong><? echo $result_item_size[csf('item_size')];?></strong></td>
		<?
        }
        ?>
         <?

			$color_wise_wo_sql=sql_select("select a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method,c.color_number_id,sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_reqsn_no and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 group by c.color_number_id,a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method order by a.id,c.id
");
		foreach($color_wise_wo_sql as $color_wise_wo_result)
	    {
			$color_total_collar=0;
			$color_total_collar_order_qnty=0;
			$process_loss_method=$color_wise_wo_result[csf("process_loss_method")];
			$constrast_color_arr=array();
			if($color_wise_wo_result[csf("color_size_sensitive")]==3)
			{
				$constrast_color=explode('__',$color_wise_wo_result[csf("color_break_down")]);
				for($i=0;$i<count($constrast_color);$i++)
				{
					$constrast_color2=explode('_',$constrast_color[$i]);
					$constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
				}
			}
		?>
			<tr>
            <td>
            <?
			if($color_wise_wo_result[csf("color_size_sensitive")]==3)
			{
				echo strtoupper ($constrast_color_arr[$color_wise_wo_result[csf('color_number_id')]]) ;
				$lab_dip_color_id=return_field_value("contrast_color_id","wo_pre_cos_fab_co_color_dtls","job_no='".$color_wise_wo_result[csf('job_no')]."' and pre_cost_fabric_cost_dtls_id=".$color_wise_wo_result[csf('id')]." and gmts_color_id=".$color_wise_wo_result[csf('color_number_id')]."");
			}
			else
			{
				echo $color_library[$color_wise_wo_result[csf('color_number_id')]];
				$lab_dip_color_id=$color_wise_wo_result[csf('color_number_id')];
			}
			?>

            </td>
            <?
            foreach($nameArray_item_size  as $result_size)
			{
				?>
				<td align="center" style="border:1px solid black">
				<?
				//$color_wise_wo_sql_qnty=sql_select("select c.color_number_id,sum(c.order_quantity) as order_quantity, sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_reqsn_no and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 and c.color_number_id='".$color_wise_wo_result['color_number_id']."' and c.size_number_id='".$result_size['size_number_id']."'");
				$color_wise_wo_sql_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$color_wise_wo_result[csf('color_number_id')]."   and  status_active=1 and is_deleted =0");

				list($plan_cut_qnty)=$color_wise_wo_sql_qnty;
				$colar_excess_per=def_number_format(($plan_cut_qnty[csf('plan_cut_qnty')]*$colar_excess_percent)/100,6,0);
				echo number_format($plan_cut_qnty[csf('plan_cut_qnty')]+$colar_excess_per,0);
				$color_total_collar+=$plan_cut_qnty[csf('plan_cut_qnty')]+$colar_excess_per;
				$color_total_collar_order_qnty+=$plan_cut_qnty[csf('order_quantity')];
				$grand_total_collar+=$plan_cut_qnty[csf('plan_cut_qnty')]+$colar_excess_per;
				$grand_total_collar_order_qnty+=$plan_cut_qnty[csf('order_quantity')];
				?>
                </td>
				<?
			}
			?>

            <td align="center"><? echo number_format($color_total_collar,0); ?></td>
            <td align="center"><? echo number_format((($color_total_collar-$color_total_collar_order_qnty)/$color_total_collar_order_qnty)*100,2); ?></td>
            </tr>
            <?
		    }
			?>
            <tr>
                <td>Size Total</td>

                <?
                foreach($nameArray_item_size  as $result_size)
                {
                ?>
                <td style="border:1px solid black;  text-align:center"><? $colar_excess_pers=($size_tatal[$result_size[csf('size_number_id')]]*$colar_excess_percent)/100; echo number_format($size_tatal[$result_size[csf('size_number_id')]]+$colar_excess_pers,0); ?></td>
                <?
                }
                ?>
                <td  style="border:1px solid black;  text-align:center"><?  echo number_format($grand_total_collar,0); ?></td>
                <td align="center" style="border:1px solid black"> <? echo number_format((($grand_total_collar-$grand_total_collar_order_qnty)/$grand_total_collar_order_qnty)*100,2); ?></td>
            </tr>
        </table>
        </td>
        <td width="2%">
        </td>
        <?
        }
		?>

        <?
		$nameArray_item_size=sql_select( "select  b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_reqsn_no  and a.body_part_id=3  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 group by b.item_size,c.size_number_id order by c.id");

		if(count($nameArray_item_size)>0)
		{
        ?>
        <td width="49%">
        <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
        <tr>
        <td colspan="<? echo count($nameArray_size)+4;?>" align="center"><b>Cuff -  Colour Size Brakedown in Pcs</b></td>
        </tr>
        <tr>
        <td width="70">Size</td>

        <?
		foreach($nameArray_item_size  as $result_size)
		{
		?>
		<td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
		<?
        }
        ?>
        <td rowspan="2" align="center"><strong>Total</strong></td>
        <td width="60" rowspan="2" align="center"><strong>Extra %</strong></td>
        </tr>
        <tr>
        <td>Cuff Size</td>

        <?
        foreach($nameArray_item_size  as $result_item_size)
		{
		?>
		<td align="center" style="border:1px solid black"><strong><? echo $result_item_size[csf('item_size')];?></strong></td>
		<?
        }
        ?>
         <?

			$color_wise_wo_sql=sql_select("select a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method,c.color_number_id,sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_reqsn_no and a.body_part_id=3  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id and  d.status_active=1 and d.is_deleted=0 group by c.color_number_id ,a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method order by a.id ,c.id
");
		foreach($color_wise_wo_sql as $color_wise_wo_result)
	    {
			$color_total_cuff=0;
			$color_total_cuff_order_qnty=0;
			$process_loss_method=$color_wise_wo_result[csf("process_loss_method")];
			$constrast_color_arr=array();
			if($color_wise_wo_result[csf("color_size_sensitive")]==3)
			{
				$constrast_color=explode('__',$color_wise_wo_result[csf("color_break_down")]);
				for($i=0;$i<count($constrast_color);$i++)
				{
					$constrast_color2=explode('_',$constrast_color[$i]);
					$constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
				}
			}
		?>
			<tr>
            <td>
            <?
			if($color_wise_wo_result[csf("color_size_sensitive")]==3)
			{
				echo strtoupper ($constrast_color_arr[$color_wise_wo_result['color_number_id']]) ;
				$lab_dip_color_id=return_field_value("contrast_color_id","wo_pre_cos_fab_co_color_dtls","job_no='".$color_wise_wo_result[csf('job_no')]."' and pre_cost_fabric_cost_dtls_id=".$color_wise_wo_result[csf('id')]." and gmts_color_id=".$color_wise_wo_result[csf('color_number_id')]."");
			}
			else
			{
				echo $color_library[$color_wise_wo_result[csf('color_number_id')]];
				$lab_dip_color_id=$color_wise_wo_result[csf('color_number_id')];
			}
			?>

            </td>
            <?
            foreach($nameArray_item_size  as $result_size)
			{
				?>
				<td align="center" style="border:1px solid black">

				<?
				/*$color_wise_wo_sql_qnty=sql_select("select c.color_number_id,sum(c.order_quantity) as order_quantity, sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_reqsn_no and a.body_part_id=3 and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 and c.color_number_id='".$color_wise_wo_result['color_number_id']."' and c.size_number_id='".$result_size['size_number_id']."'");*/
				$color_wise_wo_sql_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$color_wise_wo_result[csf('color_number_id')]."   and  status_active=1 and is_deleted =0");

				list($plan_cut_qnty)=$color_wise_wo_sql_qnty;
				$cuff_excess_per=def_number_format((($plan_cut_qnty[csf('plan_cut_qnty')]*2)*$cuff_excess_percent)/100,6,"");
				echo number_format($plan_cut_qnty[csf('plan_cut_qnty')]*2+$cuff_excess_per,0);
				$color_total_cuff+=$plan_cut_qnty[csf('plan_cut_qnty')]*2+$cuff_excess_per;
				$color_total_cuff_order_qnty+=$plan_cut_qnty[csf('order_quantity')]*2;
				$grand_total_cuff+=$plan_cut_qnty[csf('plan_cut_qnty')]*2+$cuff_excess_per;
				$grand_total_cuff_order_qnty+=$plan_cut_qnty[csf('order_quantity')]*2;
				/*echo $color_size_qnty_array[$result_size[size_number_id]][$color_wise_wo_result[color_number_id]]*2;
				$color_total_cuff+=$color_size_qnty_array[$result_size[size_number_id]][$color_wise_wo_result[color_number_id]]*2;
				$color_total_cuff_order_qnty+=$color_size_order_qnty_array[$result_size[size_number_id]][$color_wise_wo_result[color_number_id]]*2;
				$grand_total_cuff+=$color_size_qnty_array[$result_size[size_number_id]][$color_wise_wo_result[color_number_id]]*2;
				$grand_total_cuff_order_qnty+=$color_size_order_qnty_array[$result_size[size_number_id]][$color_wise_wo_result[color_number_id]]*2;*/
				?>

                </td>
				<?
			}
			?>

            <td align="center"><? echo number_format($color_total_cuff,0); ?></td>
            <td align="center"><? echo number_format((($color_total_cuff-$color_total_cuff_order_qnty)/$color_total_cuff_order_qnty)*100,2); ?></td>
            </tr>
            <?
		    }
			?>
            <tr>
                <td>Size Total</td>

                <?
                foreach($nameArray_item_size  as $result_size)
                {
                   /* $nameArray_size_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =$result_size[size_number_id]  and status_active=1 and is_deleted =0 order by id");
                foreach($nameArray_size_qnty as $result_size_qnty)
                {*/
                ?>
                <td style="border:1px solid black;  text-align:center"><? $cuff_excess_pers=(($size_tatal[$result_size[csf('size_number_id')]]*2)*$cuff_excess_percent)/100; echo number_format($size_tatal[$result_size[csf('size_number_id')]]*2+$cuff_excess_pers,0); ?></td>
                <?
                //}
                }
                ?>
                <td  style="border:1px solid black;  text-align:center"><?  echo number_format($grand_total_cuff,0); ?></td>
                <td align="center" style="border:1px solid black"> <? echo number_format((($grand_total_cuff-$grand_total_cuff_order_qnty)/$grand_total_cuff_order_qnty)*100,2); ?></td>
            </tr>
        </table>
        </td>
        <?
				}
		?>
        </tr>
        </table>
        <br/>
        <?
		$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');
		$yarn_sql_array=sql_select("SELECT min(id) as id ,count_id, copm_one_id, percent_one,copm_two_id, percent_two, type_id, sum(cons_qnty) as yarn_required, AVG(rate) as rate from wo_pre_cost_fab_yarn_cost_dtls where job_no='$job_no' and  status_active=1 and is_deleted=0 group by count_id,copm_one_id,percent_one, copm_two_id,percent_two,type_id order by id");
		?>
        <table  width="100%"  border="0" cellpadding="0" cellspacing="0" >
            <tr>
                <td width="49%" style="border:solid; border-color:#000; border-width:thin" valign="top">
                    <?php echo get_spacial_instruction($txt_reqsn_no); ?>
                </td>
                <td width="2%">
                </td>
                <td width="49%" valign="top" align="center">
                <?
				$yarn_sql_array=sql_select("SELECT min(a.id) as id, a.item_id, sum(a.qnty) as qnty ,min(b.supplier_id) as supplier_id,min(b.lot) as lot from inv_material_allocation_dtls a,product_details_master b where a.item_id=b.id and a.booking_no=$txt_reqsn_no and  a.status_active=1 and a.is_deleted=0 group by a.item_id order by a.id");
				if(count($yarn_sql_array)>0)
				{
				?>
                   <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                    <tr align="center">
                    <td colspan="7"><b>Allocated Yarn</b></td>

                    </tr>
                    <tr align="center">
                    <td>Sl</td>
                    <td>Yarn Description</td>
                    <td>Brand</td>
                    <td>Lot</td>


                    <td>Allocated Qty (Kg)</td>
                    </tr>
                    <?
					$total_allo=0;
					$item=return_library_array( "select id, product_name_details from   product_details_master",'id','product_name_details');
					$supplier=return_library_array( "select id, short_name from   lib_supplier",'id','short_name');
					//$yarn_sql_array=sql_select("SELECT a.item_id, a.qnty,b.supplier_id,b.lot from inv_material_allocation_dtls a,product_details_master b where a.item_id=b.id and a.booking_no=$txt_reqsn_no and  a.status_active=1 and a.is_deleted=0");
					$i=0;
					$total_yarn=0;
					foreach($yarn_sql_array  as $row)
                    {

						$i++;
					?>
                    <tr align="center">
                    <td><? echo $i; ?></td>
                    <td>
					<?

					echo $item[$row[csf('item_id')]];
					?>
                    </td>
                    <td>
                    <?

					echo $supplier[$row[csf('supplier_id')]];
					?>
                    </td>
                    <td>
					<?

					echo $row[csf('lot')];
					?>
                    </td>
                    <td align="right"><? echo number_format($row[csf('qnty')],4); $total_allo+= $row[csf('qnty')];?></td>
                    </tr>
                    <?
					}
					?>
                    <tr align="center">
                    <td>Total</td>
                    <td></td>


                    <td></td>
                    <td></td>
                    <td align="right"><? echo number_format($total_allo,4); ?></td>
                    </tr>
                    </table>
                    <?
				}
				else
				{
					$is_yarn_allocated=return_field_value("allocation", "variable_settings_inventory", "company_name=$cbo_company_name and variable_list=18 and item_category_id=1");
					if($is_yarn_allocated==1)
					{
					?>
					<font style=" font-size:30px"><b> Draft</b></font>
                    <?
					}
					else
					{
						echo "";
					}
				}
					?>
                </td>
            </tr>
        </table>
        <br/>

        <table  width="100%"  border="0" cellpadding="0" cellspacing="0">
            <tr>


                <td width="49%" valign="top">
                <?
				if($cbo_fabric_source==1)
				{
                ?>
                   <table width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                   <tr align="center">
                    <td colspan="10"><b>Comments</b></td>

                    </tr>
                    <tr align="center">
                    <td>Sl</td>
                    <td>Po NO</td>
                    <td>Ship Date</td>
                    <td>Pre-Cost Qty</td>
                    <td>Mn.Book Qty</td>
                    <td>Sht.Book Qty</td>
                    <td>Smp.Book Qty</td>
                    <td>Tot.Book Qty</td>
                    <td>Balance</td>
                    <td>Comments</td>
                    </tr>
                    <?
	$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	if ($cbo_fabric_natu!=0) $cbo_fabric_natu="and a.fab_nature_id='$cbo_fabric_natu'";
	if ($cbo_fabric_source!=0) $cbo_fabric_source_cond="and a.fabric_source='$cbo_fabric_source'";
	$paln_cut_qnty_array=return_library_array( "select min(id) as id,sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown  where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and is_deleted=0 and status_active=1 group by color_number_id,size_number_id,item_number_id", "id", "plan_cut_qnty");

	$item_ratio_array=return_library_array( "select gmts_item_id,set_item_ratio from wo_po_details_mas_set_details  where job_no =$txt_job_no", "gmts_item_id", "set_item_ratio");
	$nameArray=sql_select("
	select
	a.id,
	a.item_number_id,
	a.costing_per,
	b.po_break_down_id,
	b.color_size_table_id,
	b.requirment,
	c.po_number
FROM
	wo_pre_cost_fabric_cost_dtls a,
	wo_pre_cos_fab_co_avg_con_dtls b,
	wo_po_break_down c
WHERE
	a.job_no=b.job_no and
	a.job_no=c.job_no_mst and
    a.id=b.pre_cost_fabric_cost_dtls_id and
	b.po_break_down_id=c.id and
	b.po_break_down_id in (".str_replace("'","",$txt_order_no_id).")  $cbo_fabric_natu $cbo_fabric_source_cond and a.status_active=1 and a.is_deleted=0
	order by a.id");
	$count=0;
	$tot_grey_req_as_pre_cost_arr=array();
	foreach ($nameArray as $result)
	{
		if (count($nameArray)>0 )
		{
            if($result[csf("costing_per")]==1)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(12*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
			}
			if($result[csf("costing_per")]==2)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(1*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
			}
			if($result[csf("costing_per")]==3)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(24*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
			}
			if($result[csf("costing_per")]==4)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(36*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
			}
			if($result[csf("costing_per")]==5)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(48*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
			}
			$tot_grey_req_as_pre_cost_arr[$result[csf("po_number")]]+=$tot_grey_req_as_pre_cost;
        }
    }
	                $total_pre_cost=0;
					$total_booking_qnty_main=0;
					$total_booking_qnty_short=0;
					$total_booking_qnty_sample=0;
					$total_tot_bok_qty=0;
					$tot_balance=0;
					/*$booking_qnty=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and booking_type in(1,4)  and a.status_active=1 and a.is_deleted=0 group by b.po_number order by a.po_break_down_id", "po_break_down_id", "grey_fab_qnty");*/
					//$booking_qnty_main=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and booking_type =1 and is_short=2 and a.status_active=1 and a.is_deleted=0 group by b.po_number order by a.po_break_down_id", "po_break_down_id", "grey_fab_qnty");
					$booking_qnty_main=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b, wo_booking_mst c where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no  and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and a.booking_type =1 and c.fabric_source=$cbo_fabric_source and a.is_short=2 and c.item_category=2 and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");

					$booking_qnty_short=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b , wo_booking_mst c where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and a.booking_type =1 and c.fabric_source=$cbo_fabric_source and c.item_category=2 and a.is_short=1 and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");
					$booking_qnty_sample=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b , wo_booking_mst c  where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and a.booking_type =4 and c.fabric_source=$cbo_fabric_source and c.item_category=2  and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");

					$sql_data=sql_select( "select max(a.id) as id,  a.po_number,max(a.pub_shipment_date) as pub_shipment_date,sum(a.plan_cut) as plan_cut  from wo_po_break_down a,wo_pre_cost_sum_dtls b,wo_pre_cost_mst c where a.job_no_mst=b.job_no and a.job_no_mst=c.job_no and a.id in(".str_replace("'","",$txt_order_no_id).") group by a.po_number order by a.id");
					foreach($sql_data  as $row)
                    {
					$col++;
					?>
                    <tr align="center">
                    <td><? echo $col; ?></td>
                    <td><? echo $row[csf("po_number")]; ?></td>
                     <td><? echo change_date_format($row[csf("pub_shipment_date")],"dd-mm-yyyy",'-'); ?></td>
                    <td align="right"><? echo number_format($tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]],2); $total_pre_cost+=$tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]]; ?></td>
                    <td align="right"><? echo number_format($booking_qnty_main[$row[csf("id")]],2); $total_booking_qnty_main+=$booking_qnty_main[$row[csf("id")]];?></td>
                    <td align="right"><? echo number_format($booking_qnty_short[$row[csf("id")]],2); $total_booking_qnty_short+=$booking_qnty_short[$row[csf("id")]];?></td>
                    <td align="right"><? echo number_format($booking_qnty_sample[$row[csf("id")]],2); $total_booking_qnty_sample+=$booking_qnty_sample[$row[csf("id")]];?></td>
                    <td align="right"><? $tot_bok_qty=$booking_qnty_main[$row[csf("id")]]+$booking_qnty_short[$row[csf("id")]]+$booking_qnty_sample[$row[csf("id")]]; echo number_format($tot_bok_qty,2); $total_tot_bok_qty+=$tot_bok_qty;?></td>
                    <td align="right">
					<? $balance= def_number_format($tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]]-$tot_bok_qty,2,""); echo number_format($balance,2); $tot_balance+= $balance?>
                    </td>
                    <td>
					<?
					if( $balance>0)
					{
						echo "Less Booking";
					}
					else if ($balance<0)
					{
						echo "Over Booking";
					}
					else
					{
						echo "";
					}
					?>
                    </td>
                    </tr>
                    <?
					}
					?>
                    <tfoot>

                    <tr>
                    <td colspan="3">Total:</td>

                    <td align="right"><? echo number_format($total_pre_cost,2); ?></td>
                    <td align="right"><? echo number_format($total_booking_qnty_main,2); ?></td>
                    <td align="right"><? echo number_format($total_booking_qnty_short,2); ?></td>
                    <td align="right"><? echo number_format($total_booking_qnty_sample,2); ?></td>
                     <td align="right"><? echo number_format($total_tot_bok_qty,2); ?></td>
                    <td align="right"><? echo number_format($tot_balance,2); ?></td>
                    <td></td>
                    </tr>
                    </tfoot>
                    </table>
                    <?
				}
					?>
                </td>

            </tr>
        </table>

         <br/>
        <?
		//echo "select responsible_dept,	responsible_person,	reason from wo_booking_dtls where booking_no =$txt_reqsn_no";
		//"select id,department_name from  lib_department where status_active=1 and is_deleted=0 order by  department_name"
		$department_name_library=return_library_array( "select id,department_name from  lib_department where status_active=1 and is_deleted=0 order by  department_name", "id", "department_name"  );

		$sql_responsible= sql_select("select responsible_dept,	responsible_person,	reason from wo_booking_dtls where booking_no =$txt_reqsn_no");
		if(count($sql_responsible)>0)
		{
		?>
         <table width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
         <tr>
         <td>
          #
         </td>
          <td>
         Responsible Dept.
         </td>
         <td>
         Responsible person
         </td>
         <td>
         Reason
         </td>
         </tr>
         <?
		 $ir=1;
		foreach($sql_responsible as $sql_responsible_row)
		{
			?>
             <tr>
             <td>
             <?  echo $ir; ?>
             </td>
              <td>
             <?
			 $responsible_dept_st="";
			 $responsible_dept_arr=explode( ",",$sql_responsible_row[csf('responsible_dept')]);
			 foreach($responsible_dept_arr as $key => $value)
			 {
				 $responsible_dept_st.= $department_name_library[$value].", ";
			 }
			 echo rtrim($responsible_dept_st,", ");
			 ?>
             </td>
             <td>
            <?  echo $sql_responsible_row[csf('responsible_person')]; ?>
             </td>
             <td>
              <?  echo $sql_responsible_row[csf('reason')]; ?>
             </td>
             </tr>
            <?
			$ir++;

		}
		 ?>
         </table>
         <?
		}
		 ?>

         <!--<br><br><br><br>-->
         <?
		 	//echo signature_table(1, $cbo_company_name, "1330px");
		 ?>

         <?
		 $sql = sql_select("select designation,name from variable_settings_signature where report_id=1 and company_id=$cbo_company_name order by sequence_no" );
	     $count=count($sql);

	$width=1330;
	$td_width=floor($width/$count);

	$standard_width=$count*150;

	if($standard_width>$width) $td_width=150;

	$no_coloumn_per_tr=floor($width/$td_width);
	$col=$count-2;
	$i=1;
	echo '<table width="'.$width.'"><tr><td width="'.$td_width.'" align="center" valign="bottom">'.$user_arr[$inserted_by].'</td><td height="70" colspan="'.$col.'"></td><td width="'.$td_width.'" align="center" valign="bottom">'.$user_arr[$nameArray_approved_date_row[csf('approved_by')]].'</td></tr><tr>';
	foreach($sql as $row)
	{
		echo '<td width="'.$td_width.'" align="center" valign="top"><strong style="text-decoration:overline">'.$row[csf("designation")]."</strong><br>".$row[csf("name")].'</td>';

		if($i%$no_coloumn_per_tr==0) echo '</tr><tr><td height="70" colspan="'.$no_coloumn_per_tr.'"></td><tr>';
		$i++;
	}
	echo '</tr></table>';
		 ?>
       </div>
<?


}

