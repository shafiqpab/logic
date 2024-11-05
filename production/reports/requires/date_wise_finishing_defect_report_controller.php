<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action==="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select a.id, a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.id=b.buyer_id and b.tag_company=$data and a.status_active=1 and a.is_deleted=0 order by a.buyer_name","id,buyer_name", 1, "-- Select buyer --", 0, "","" );
    exit();	 
}

if ($action==="load_drop_down_location")
{
    extract($_REQUEST);
    $choosenCompany = $data;  
	//echo "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id in($choosenCompany) group by id,location_name  order by location_name";
	echo create_drop_down( "cbo_lc_location", 150, "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id in($choosenCompany) group by id,location_name  order by location_name","id,location_name", 0, "-- Select location --", $selected, "" );
	exit();
}
if ($action==="load_drop_down_location_wk")
{
    extract($_REQUEST);
    $choosenCompany = $data;  
	//echo "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id in($choosenCompany) group by id,location_name  order by location_name";
	echo create_drop_down( "cbo_wk_location", 150, "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id in($choosenCompany) group by id,location_name  order by location_name","id,location_name", 0, "-- Select location --", $selected, "" );
	exit();
}

if ($action==="load_drop_down_floor")
{
    extract($_REQUEST);
    $choosenLocation = $data;  
	echo create_drop_down( "cbo_floor_name", 150, "SELECT id,floor_name from lib_prod_floor where location_id in($choosenLocation) and production_process in(11) and status_active =1 and is_deleted=0 group by id,floor_name order by floor_name","id,floor_name", 0, "-- Select Floor --", $selected, "" );
	exit();
}
if ($action==="load_drop_down_floor_line")
{
    extract($_REQUEST);
    $floor_id = $data;  
	echo create_drop_down( "cbo_line_id", 120, "select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1 and floor_name!=0 and floor_name in($floor_id) group by id,line_name order by line_name","id,line_name", 0, "-- Select Floor --", $selected, "" );
	exit();
}

if($action=="order_no_search_popup")
{
	echo load_html_head_contents("Ref No Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	echo $company_name;
	?>
	<script type="text/javascript">
	
		function js_set_value(str)
		{
			var splitData = str.split("_");
		//	alert (splitData[0]);
			$("#hide_order_id").val(splitData[0]); 
			$("#hide_ref_no").val(splitData[1]); 
			parent.emailwindow.hide();
		}
    </script>
<body>
<div align="center">
	<form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:780px;">
            <table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer<? //echo $companyID.'fff';
					 
                       		if($type==1)
							{
							 $msg="Order No";
							  $msg_date="Shipment";
							}
							else {
							  $msg="Order No";
							  $msg_date="Shipment";
							}
								?>
					 </th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter <? echo $msg;?></th>
                    <th><? echo $msg_date;?> Date</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
                    <input type="hidden" name="hide_ref_no" id="hide_ref_no" value="" />
                    <input type="hidden" name="hide_order_id" id="hide_order_id" value="" />
					<input type="hidden" name="txt_job_id" id="txt_job_id" value="<? echo $txt_job_id;?>" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($company_name) $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--","","",0 );//$buyerID
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		if($type==1)
							{
								$search_by_arr=array(1=>"Order No",2=>"Master Style");
							}
							else
							{
								$search_by_arr=array(1=>"Order No",2=>"Style Ref",3=>"Job No");
							}
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 110, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
                        </td>	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company_name; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+document.getElementById('txt_job_id').value+'**'+'<? echo $lc_location; ?>'+'**'+'<? echo $type; ?>'+'**'+'<? echo $style_owner; ?>', 'create_order_no_search_list_view', 'search_div', 'date_wise_finishing_defect_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    	</td>
                    </tr>
                    <tr>
                        <td colspan="5" valign="middle"><? echo load_month_buttons(1); ?></td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:5px" id="search_div"></div>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
	exit(); 
}

if($action=="create_order_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$txt_job_id=$data[6];
	$locationID=$data[7];
	$type_id=$data[8];
	$style_owner=$data[9];
	 
	if($locationID>0) $loc_id_cond=" and a.location_name=$data[1]";else $loc_id_cond="";
	
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";
//	if($style_owner>0) $style_owner_cond="and a.style_owner=$style_owner";else $style_owner_cond="";
	if($company_id>0) $comp_cond="and a.company_name in($company_id)";else $comp_cond="";
	if($type_id==1)
	{
	if($search_by==1) $search_field="b.po_number"; //grouping
	else if($search_by==2) $search_field="b.grouping"; 	
	else $search_field="a.job_no";
	}
	else
	{
		if($search_by==1) $search_field="b.po_number"; //grouping
	else if($search_by==2) $search_field="a.style_ref_no"; 	
	else $search_field="a.job_no";
	}
		
	$start_date =$data[4];
	$end_date =$data[5];	
	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and b.pub_shipment_date between '".change_date_format(trim($start_date),"yyyy-mm-dd")."' and '".change_date_format(trim($end_date),"yyyy-mm-dd")."'";
		}
		else
		{
			$date_cond="and b.pub_shipment_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";	
		}
	}
	else $date_cond="";
	
	$company_short_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$arr=array (0=>$company_short_arr,1=>$buyer_arr);
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	//if($pre_cost_ver_id==1) $entry_form_cond="and c.entry_from=111";
	//else $entry_form_cond="and c.entry_from=158";
	if($type_id==1)
	{
	$js_select="job_id,grouping";
	} else $js_select="job_id,style_ref_no";
	
	if($type_id==1)
	{
   $sql="select b.id,a.id as job_id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.grouping, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.grouping is not null and $search_field like '$search_string' $loc_id_cond $date_cond $entry_form_cond $style_owner_cond $comp_cond order by b.id, b.pub_shipment_date";
 echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Master Style, Shipment Date", "70,70,50,70,150,180","760","210",0, $sql , "js_set_value", "$js_select","",1,"company_name,buyer_name,0,0,0,0,0",$arr,"company_name,buyer_name,year,job_no_prefix_num,style_ref_no,grouping,pub_shipment_date","",'','0,0,0,0,0,0,3','',0) ;
   exit(); 
	}
	else
	{
		$sql="select b.id,a.id as job_id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.grouping, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_mst c where a.job_no=b.job_no_mst and a.job_no=c.job_no and  b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.grouping is not null and $search_field like '$search_string' $loc_id_cond $date_cond $entry_form_cond $style_owner_cond $comp_cond order by b.id, b.pub_shipment_date";
 echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Master Style, Shipment Date", "70,70,50,70,150,180","760","210",0, $sql , "js_set_value", "$js_select","",1,"company_name,buyer_name,0,0,0,0,0",$arr,"company_name,buyer_name,year,job_no_prefix_num,style_ref_no,grouping,pub_shipment_date","",'','0,0,0,0,0,0,3','',0) ;
   exit(); 
	}
		
	
}


if($action==="generate_report")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_name      = str_replace("'","",$cbo_company_name);
	$cbo_work_company = str_replace("'","",$cbo_work_company);
	$cbo_floor_name        = str_replace("'","",$cbo_floor_name);
	$cbo_lc_location     = str_replace("'","",$cbo_lc_location);
	$cbo_wk_location       = trim(str_replace("'","",$cbo_wk_location));
	$cbo_floor_name        = trim(str_replace("'","",$cbo_floor_name));
	
	$cbo_line_id         = str_replace("'","",$cbo_line_id);
	$txt_ref_no          = str_replace("'","",$txt_ref_no);
	$hiddenre_ref_id       = str_replace("'","",$hidden_order_id);
	$txt_style_ref         = str_replace("'","",$txt_style_ref);
	$txt_style_id           = str_replace("'","",$txt_style_id);
	$txt_date_from           = str_replace("'","",$txt_date_from);
	$type_id           = str_replace("'","",$type);
	// echo $type_id;die;
	if($txt_ref_no=="") $int_ref_cond=""; else $int_ref_cond=" and e.grouping='$txt_ref_no' ";
	if($txt_style_ref=="") $style_ref_cond=""; else $style_ref_cond=" and f.style_ref_no='$txt_style_ref' ";

	//echo $cbo_work_company_name.'system';die;
	$lineArr = return_library_array("select id,line_name from lib_sewing_line","id","line_name");
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$company_arr=return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0", 'id','company_name');
	$company_short_arr=return_library_array("select id,company_short_name from lib_company where status_active=1 and is_deleted=0", 'id','company_short_name');
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	//$season_arr=return_library_array("select id,season_name from lib_buyer_season where status_active=1 and is_deleted=0",'id','season_name');
	$floor_name_arr=return_library_array("select id,floor_name from lib_prod_floor where status_active=1 and is_deleted=0", "id", "floor_name");

	 


	$company_id_cond=$location_id_cond=$wk_location_id_cond=$floor_cond=$sewing_line_cond='';
	if ($cbo_company_name !="") 
	{
		//$company_cond=" and e.company_name in($cbo_company_name)";
		$company_id_cond=" and a.company_id in($cbo_company_name)";
	}

	if ($cbo_lc_location != "") $location_id_cond=" and f.location_name in($cbo_lc_location)";
	if ($cbo_wk_location != "") $wk_location_id_cond=" and a.location in($cbo_wk_location)";
	if ($cbo_work_company != '')
	{
		//$work_company_cond=" and a.delivery_company_id in($cbo_work_company)";
		$work_company_cond=" and a.serving_company in($cbo_work_company)";
		//$work_company_cond3=" and a.working_company_id in($cbo_work_company)";
	} 
	
	if ($cbo_line_id != '') $sewing_line_cond=" and a.sewing_line in($cbo_line_id)";		
	if ($cbo_floor_name != '') $floor_cond=" and a.floor_id in($cbo_floor_name)";

	$date_cond = '';
	if($txt_date_from!="")
	{
		if ($db_type==0)
		{
			$txt_date_from = date("Y-m-d", strtotime(str_replace("'", "",  $txt_date_from)));
			//$txt_date_to = date("Y-m-d", strtotime(str_replace("'", "",  $txt_date_to)));
			$date_cond = " and a.production_date between '".$txt_date_from."' and '".$txt_date_from."'";
		}
		else
		{
			$txt_date_from = date("d-M-Y", strtotime(str_replace("'", "",  $txt_date_from)));
			//$txt_date_to = date("d-M-Y", strtotime(str_replace("'", "",  $txt_date_to)));
			$date_cond = " and a.production_date between '".$txt_date_from."' and '".$txt_date_from."'";
		}
	}
	

	 $sql_sew="SELECT a.floor_id,a.location as wk_location,a.prod_reso_allo,a.sewing_line,a.serving_company,a.production_quantity, e.id as po_id,f.style_ref_no,f.buyer_name, b.bundle_no,b.defect_type_id, b.defect_point_id, b.defect_qty from pro_garments_production_mst a,pro_gmts_prod_dft b, wo_po_break_down e, wo_po_details_master f where a.id=b.mst_id and a.po_break_down_id=e.id and e.job_id=f.id and e.id=b.po_break_down_id  and a.production_type=80 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.status_active in(1,2,3) and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and b.defect_qty>0 $date_cond $work_company_cond $company_id_cond $location_id_cond $wk_location_id_cond $floor_cond $sewing_line_cond $int_ref_cond $style_ref_cond order by a.sewing_line";  
	
	// echo $sql_sew; die;
	$sew_sql_result=sql_select($sql_sew);
	if(count($sew_sql_result)<=0)
	{
		echo "<div style='color:red;'> NO Data Found</div>";die;
	}

	$sew_fin_woven_defect_array_new = array();
	$sew_fin_measurment_check_array_new = array();
	$sew_fin_wash_check_array_new = array();

	foreach($sew_sql_result as $row)
	{
		if($row[csf('prod_reso_allo')]==1)
		{
			$line_resource_mst_arr=explode(",",$prod_reso_arr[$row[csf('sewing_line')]]);
			$line_name="";
			foreach($line_resource_mst_arr as $resource_id)
			{
				$line_name .= ($line_name == "") ? $resource_id : ",".$resource_id;
			}
			$sewing_line=$line_name;
			//echo $sewing_line.'FF';
		}
		else $sewing_line=$row[csf('sewing_line')];
		
		$floor_name=$floor_nameArr[$row[csf('floor_id')]];
		 
		
		
		$sew_line_arr[$row[csf('serving_company')]][$row[csf('floor_id')]][$sewing_line][$row[csf('style_ref_no')]]['buyer_name']=$row[csf('buyer_name')];
		$sew_line_arr[$row[csf('serving_company')]][$row[csf('floor_id')]][$sewing_line][$row[csf('style_ref_no')]]['production_quantity']+=$row[csf('production_quantity')];
		$sew_line_qty_arr[$row[csf('serving_company')]][$row[csf('floor_id')]][$sewing_line][$row[csf('style_ref_no')]][$row[csf('defect_type_id')]][$row[csf('defect_point_id')]]['defect_qty']+=$row[csf('defect_qty')];
		
		if($row[csf('defect_type_id')]==1 || $row[csf('defect_type_id')]=2 || $row[csf('defect_type_id')]==3 || $row[csf('defect_type_id')]==6)
		{
			$sew_fin_woven_defect_array_new[$row[csf('defect_point_id')]] = $sew_fin_woven_defect_array[$row[csf('defect_point_id')]];
		}

		if($row[csf('defect_type_id')]==5)
		{
			$sew_fin_measurment_check_array_new[$row[csf('defect_point_id')]] = $sew_fin_woven_defect_array[$row[csf('defect_point_id')]];
		}

		if($row[csf('defect_type_id')]==4)
		{
			$sew_fin_wash_check_array_new[$row[csf('defect_point_id')]] = $sew_fin_woven_defect_array[$row[csf('defect_point_id')]];
		}
		
		
		$line_fin_arr[$sewing_line]+=$row[csf('production_quantity')];
	}

	// echo "<pre>";print_r($sew_line_qty_arr);die;
	 
	$sql_prod_fin="SELECT a.floor_id,a.location as wk_location,a.prod_reso_allo,a.sewing_line,a.serving_company,a.production_quantity, e.id as po_id,f.style_ref_no,f.buyer_name from pro_garments_production_mst a, wo_po_break_down e, wo_po_details_master f where  a.po_break_down_id=e.id and e.job_id=f.id  and a.production_type=80 and a.status_active=1 and a.is_deleted=0 and e.status_active in(1,2,3) and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0  $date_cond $work_company_cond $company_id_cond $location_id_cond $wk_location_id_cond $floor_cond $sewing_line_cond $int_ref_cond $style_ref_cond order by a.sewing_line";
	$fin_prod_sql_result=sql_select($sql_prod_fin);
	foreach($fin_prod_sql_result as $row)
	{
		if($row[csf('prod_reso_allo')]==1)
		{
		$line_resource_mst_arr=explode(",",$prod_reso_arr[$row[csf('sewing_line')]]);
			$line_name="";
			foreach($line_resource_mst_arr as $resource_id)
			{
				$line_name .= ($line_name == "") ? $resource_id : ",".$resource_id;
			}
			$sewing_line=$line_name;
		}
		else $sewing_line=$row[csf('sewing_line')];
		$line_fin_qty_arr[$sewing_line]+=$row[csf('production_quantity')];
		$sew_line_prod_qty_arr[$row[csf('serving_company')]][$row[csf('floor_id')]][$sewing_line][$row[csf('style_ref_no')]]['production_quantity']+=$row[csf('production_quantity')];
	}
	unset($fin_prod_sql_result);
	//print_r($line_fin_arr);
	
	
	if($type_id==1)
	{
		//sew_fin_wash_check_array
		$tot_rows=count($sew_fin_woven_defect_array_new)+count($sew_fin_measurment_check_array)+count($sew_fin_wash_check_array);
		$td_width=750+(40*$tot_rows);
		$row_span=6+count($sew_fin_woven_defect_array_new);	
		// asort($sew_fin_woven_defect_array_new);
		// asort($sew_fin_measurment_check_array);
		//echo $row_span.'DDDDDDDDDD';;
	}
	 
	 asort($sew_fin_woven_defect_array_new);
	 asort($sew_fin_measurment_check_array);
	//  echo '<pre>';print_r($sew_fin_woven_defect_array_new);
    ob_start();
	//echo $td_width;
	?>
	<div width="<?= $td_width; ?>">
     <style type="text/css">
            .block_div { 
                    width:auto;
                    height:auto; font-size:11px;
                    text-wrap:normal; 
                    vertical-align:bottom;
                    display: block;
                    position: !important; 
                    -webkit-transform: rotate(-90deg);
                    -moz-transform: rotate(-90deg);
					padding:0px !important; margin:0px !important;
					z-index:-999;
					
            }
			.multiselect_dropdown_table{
				z-index:9999;
			}
            .break_all
            {
            	word-wrap: break-word;
            	word-break: break-all;padding:0px !important; margin:0px !important;
            }
			.bg_colo_th
            {
			   background-color: #9C6;
            }
			.bg_color_th
            {
				 background-color: #C96;
            }
        </style> 
		<table cellpadding="0" cellspacing="0" width="<?= $td_width ?>">
			<tr> 
			   <td align="center" width="100%"><strong style="font-size:16px">Date:&nbsp;<?= change_date_format($txt_date_from); ?>&nbsp;<?= change_date_format($txt_date_to); ?></strong></td>
			</tr>
		</table>
	    
		<table width="<?= $td_width; ?>" class="rpt_table" border="1" rules="all" cellpadding="0" cellspacing="0" align="left">
			<thead>
				 <tr>
                 <th colspan="<? echo $row_span;?>">QUALITY DEFECT(Front/Back/Westband)</th>
                  <th colspan="3"  class=""><b>DEFECT Wash<b/></th>
                  <th colspan="4" class=""><b>DEFECT Mesurement<b/></th>
                   <th width="70">&nbsp;</th>
                   <th width="70">&nbsp;</th>
                   <th width="70">&nbsp;</th>
                   <th width="70">&nbsp;</th>
               </tr>
                <tr height="110">
                    <th   width="30">SL</th>
                    <th   width="75">Company</th>
                    <th   width="80">Unit</th>
                    <th   width="70">LINE NO</th>
                    <th   width="100">BUYER</th>
                    <th   width="100">STYLE</th>
                    <? //sew_fin_spot_defect_type
					$id=1;
                    foreach($sew_fin_woven_defect_array_new as $type_id=>$defect_type)
                    {
						
						?>
                    	<th  width="40"    style="vertical-align:middle" title="<? echo $type_id;?>"><div class="block_div" style=""><p><?  echo $defect_type;   ?></p></div></th>
                     	<?
					 	$id++;
					}
					foreach($sew_fin_wash_check_array as $wash_type_id=>$wash_discrep_type)
                    {
						?>
                    	<th   width="40"  style="vertical-align:middle;"  title="<? echo $wash_type_id;?>"><div class="block_div"  style=""><?  echo $wash_discrep_type;   ?></div></th>
                     	<?
					  	$id++;
					}
					foreach($sew_fin_measurment_check_array as $measure_type_id=>$measurement_discrep_type)
                    {
						?>
                    	<th   width="40"  style="vertical-align:middle" title="<? echo $measure_type_id;?>"><div class="block_div"  style=""><?  echo $measurement_discrep_type;   ?></div></th>
                     	<?
					  	$id++;
					}
					?>
                    <th  style="vertical-align:middle"    width="70"><div class="block_div">TOTAL-DEFECT-QTY</div></th>
                    <th  style="vertical-align:middle"  width="70"><div class="block_div">TOTAL AUDIT QTY</div></th>
                    <th  style="vertical-align:middle"    width="70">%</th>
                    <th  style="vertical-align:middle"   width=""><div class="block_div" style="">RESPONSEBLE PERSON</div></th>
                   
                </tr>
			</thead>
		</table>
		<div style="width:<?= $td_width+20; ?>px; overflow-y:auto; max-height:300px; text-align:center;float:left;" id="scroll_body_summary">
		    <table width="<?= $td_width; ?>" class="rpt_table" border="1" rules="all" cellpadding="0" align="left" cellspacing="0" id="table_body_summary">
		         <?
				$i=1;
				$total_defect_qty=$total_adult_qty=0;
				$tot_po_qty=$tot_plan_cut_qty=$tot_shipout_qty=0; $line_insideTopGet_defect_qty_arr=array(); 
				// asort($sew_line_arr);
				$wash_line_insideTopGet_defect_qty_arr=array();
				$mesurement_defect_qty_arr=array(); 
				$summary_line_top_wise_defect_arr=array(); 
				$summary_inside_line_wise_defect_arr=array();
				$summary_getup_line_wise_defect_arr=array();
				$summary_line_final_wise_defect_arr=array(); 
				// echo "<pre>"; print_r($sew_line_arr); die;
				// $tot_delectQty=0;
				foreach($sew_line_arr as $company_id=>$company_data)
				{
					foreach($company_data as $unit_id=>$unit_data)
					{
						foreach($unit_data as $line_id=>$line_data)
						{
							foreach($line_data as $style_ref=>$row)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$audit_qty=$sew_line_prod_qty_arr[$company_id][$unit_id][$line_id][$style_ref]['production_quantity'];					
								?>
								<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
									<td width="30"><p><? echo $i; ?></p></td>
									<td width="75"><div style="word-wrap:break-word;"><? echo $company_short_arr[$company_id]; ?></div></td>
									<td width="80"><div style="word-wrap:break-word;"><? echo $floor_name_arr[$unit_id]; ?></div></td>
									<td width="70" title="<? echo $line_id;?>"><div style="word-wrap:break-word;"><? echo $lineArr[$line_id]; ?></div></td>
									<td width="100"><div style="word-wrap:break-word;"><? echo $buyer_library[$row[('buyer_name')]]; ?></div></td>
									<td width="100"><div style="word-wrap:break-word;"><? echo $style_ref; ?></div></td>
									<? //sew_fin_spot_defect_type
									// $line_insideTopGet_defect_qty_arr=array();
									// echo "<pre>"; print_r($sew_fin_woven_defect_array_new); die;
									$line_frontbackWest_defect_qty_arr=array();

									foreach($sew_fin_woven_defect_array_new as $type_id=>$defect_type)
									{
										$in_defect_qty= $sew_line_qty_arr[$company_id][$unit_id][$line_id][$style_ref][1][$type_id]['defect_qty'];
										$top_defect_qty= $sew_line_qty_arr[$company_id][$unit_id][$line_id][$style_ref][2][$type_id]['defect_qty'];
										$get_defect_qty= $sew_line_qty_arr[$company_id][$unit_id][$line_id][$style_ref][3][$type_id]['defect_qty'];
										
										
										//$wash_defect_qty= $sew_line_qty_arr[$company_id][$unit_id][$line_id][$style_ref][4][$type_id]['defect_qty'];
										//$me_defect_qty= $sew_line_qty_arr[$company_id][$unit_id][$line_id][$style_ref][5][$type_id]['defect_qty'];
										$final_defect_qty= $sew_line_qty_arr[$company_id][$unit_id][$line_id][$style_ref][6][$type_id]['defect_qty'];
							
										$tot_fin_defect_qty=$in_defect_qty+$top_defect_qty+$get_defect_qty+$final_defect_qty;
										?>
									
									
										<td width="40" style=""align="right"><? echo $tot_fin_defect_qty;?></td>
										<?
										$line_insideTopGet_defect_qty_arr[$type_id]+=$tot_fin_defect_qty;
										$line_frontbackWest_defect_qty_arr[$line_id][$style_ref]+=$tot_fin_defect_qty;
									
										$summary_line_top_wise_defect_arr[$line_id][$type_id]+=$top_defect_qty;
										$summary_inside_line_wise_defect_arr[$line_id][$type_id]+=$in_defect_qty;
										$summary_getup_line_wise_defect_arr[$line_id][$type_id]+=$get_defect_qty;
										$summary_line_final_wise_defect_arr[$line_id][$type_id]+=$final_defect_qty;

									
									}				
								
									$mm=4;$line_wash_defect_qty_arr=array();
									// echo "<pre>"; print_r($sew_fin_wash_check_array); die;
									foreach($sew_fin_wash_check_array as $wash_type_id=>$wash_discrep_type)
									{
										$wash_qty=$sew_line_qty_arr[$company_id][$unit_id][$line_id][$style_ref][4][$wash_type_id]['defect_qty'];;
										?>
										<td  width="40" class="" align="right" style=""><?  echo $wash_qty;   ?></td>

										<?
										$wash_line_insideTopGet_defect_qty_arr[$wash_type_id]+=$wash_qty;
										$line_wash_defect_qty_arr[$line_id][$style_ref]+=$wash_qty;
										// $line_wash_defect_qty_arr[$line_id]+=$mesurement_qty;
										//line_alter_defect_qty_arr
										$mm++;
									}
									$mm=5;$line_measure_defect_qty_arr=array();
									// echo "<pre>"; print_r($sew_fin_measurment_check_array); die;
									foreach($sew_fin_measurment_check_array as $measure_type_id=>$measurement_discrep_type)
									{
										$mesurement_qty=$sew_line_qty_arr[$company_id][$unit_id][$line_id][$style_ref][5][$measure_type_id]['defect_qty'];
										?>
										<td  width="40" class="" align="right" style=""><?  echo $mesurement_qty;   ?></td>

										<?
										$mesurement_defect_qty_arr[$measure_type_id]+=$mesurement_qty;
										$line_measure_defect_qty_arr[$line_id][$style_ref]+=$mesurement_qty;
										//line_alter_defect_qty_arr
										$mm++;
									}
									
									?>
								
									<td width="70" align="right"><? $tot_delectQty=$line_frontbackWest_defect_qty_arr[$line_id][$style_ref]+$line_measure_defect_qty_arr[$line_id][$style_ref]+$line_wash_defect_qty_arr[$line_id][$style_ref];
									echo $tot_delectQty;?></td>
									<td width="70" align="right"> <? echo $audit_qty;?></td>
									<td width="70" align="right" title="Tot Defect Qty/Audit qty*100"><? 
									$line_percent=$tot_delectQty/$audit_qty*100;
									//Graph here
									$line_graph_percent_arr[$lineArr[$line_id]]=$line_percent;
									$line_graph_name_arr[$lineArr[$line_id]]=$lineArr[$line_id];
									echo number_format($line_percent,2);?></td>
									<td width="70" align="right"></td>                       
								</tr>
								<?
								$total_defect_qty+=$tot_delectQty;
								$total_adult_qty+=$audit_qty;
								$i++; 
							}
						}
					}
				}
				?>
                <tr class="tbl_bottom">
					<td colspan="6" width="450" align="right">Total</td>
                          <? //sew_fin_spot_defect_type
					$sew_defect_top6_arr=array();
					// echo "<pre>"; print_r($sew_fin_woven_defect_array_new); die;
                    foreach($sew_fin_woven_defect_array_new as $type_id=>$defect_type)
                    {
						$insideTopGet_defect_qty=$line_insideTopGet_defect_qty_arr[$type_id];
					?>
                    <td  width="40" style="" title="DefectType=<? echo $type_id; ?>"  align="right"><?  echo $insideTopGet_defect_qty;  ?></td>
                     <?
					 $sew_defect_top6_arr[$sew_fin_woven_defect_array_new[$type_id]]=$line_insideTopGet_defect_qty_arr[$type_id];
					 $summary_line_wise_defect_arr[$line_id][$type_id]+=$line_insideTopGet_defect_qty_arr[$type_id];
					}
					 $wash_me_sew_defect_top6_arr=array();
					 foreach($sew_fin_wash_check_array as $wash_type_id=>$wash_discrep_type)
                     {
					?>
                    <td  width="40" class="" align="right" style=""><?  echo $wash_line_insideTopGet_defect_qty_arr[$wash_type_id];   ?></td>
                     <?
					 $wash_me_sew_defect_top6_arr[$sew_fin_wash_check_array[$wash_type_id]]=$wash_line_insideTopGet_defect_qty_arr[$wash_type_id];
					 }
					  foreach($sew_fin_measurment_check_array as $measure_type_id=>$measurement_discrep_type)
                     {
					?>
                    <td  width="40" class="" align="right" style=""><?  echo $mesurement_defect_qty_arr[$measure_type_id];   ?></td>
                     <?
					 $wash_me_sew_defect_top6_arr[$sew_fin_measurment_check_array[$measure_type_id]]=$mesurement_defect_qty_arr[$measure_type_id];
					 }
					 ?>
                    
                    <td  align="right" width="70"><? echo $total_defect_qty;?></td>
                    <td  align="right" width="70"><? echo $total_adult_qty;?></td>
                    <td  align="right" width="70"><? echo number_format(($total_defect_qty/$total_adult_qty)*100,2);?></td>
                    <td  align="right">&nbsp;</td>
				</tr>
		    </table>
            
	    </div>
		<br>
        <?
		//print_r($summary_inside_line_wise_defect_arr);
			// ksort($line_graph_percent_arr);
			//print_r($line_graph_percent_arr);
			//ksort($sew_defect_top6_arr);
			foreach($sew_defect_top6_arr as $key=>$val)
			{ 
				if($val>0)
				{
				$sew_defect_top6_arr2[$key]=$val;
				}
			}
			  arsort($sew_defect_top6_arr2) ;
			  
			foreach($wash_me_sew_defect_top6_arr as $key=>$val)
			{ 
				if($val>0)
				{
				$wash_me_sew_defect_top6_arr2[$key]=$val;
				}
			}
			  arsort($wash_me_sew_defect_top6_arr2) ;
	 ?>
      <table>
             <tr>
                 <td>
                   <table border="1" class="rpt_table" rules="all" width="420" cellpadding="0" cellspacing="0" >
                   <caption><b> Major Six Defects On  (In Side - Top Side - Getup) </b> </caption>
                   <thead>
                   
                   <tr>
                     <th>SL</th>
                     <th width="150">Defect description</th>
                     <th width="100">Total Audit qty</th>
                     <th width="100">Top 6 Defect</th>
                     <th>%</th>
                   </tr>
                   
                     </thead>
                     <tbody>
                     <?
                     $t=1;
                     foreach($sew_defect_top6_arr2 as $key_type=>$top_defect_qty)
                     {
                      
                        if ($t%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                    
                        if($t<=6)
                        {
                            $top_six_percent_graph_arr[$key_type]=($top_defect_qty/$total_adult_qty)*100;
                        ?>
                        <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('trtop_<? echo $t; ?>','<? echo $bgcolor;?>')" id="trtop_<? echo $t;?>">
                        <td  align="center"><? echo $t;?></td>
                        <td  align="center"><? echo $key_type;?></td>
                        <td  align="right"><? echo $total_adult_qty;?></td>
                        <td  align="right"><? echo $top_defect_qty;?></td>
                        <td  align="right" title="Defect Qty/Audit Qty*100"><? echo number_format((($top_defect_qty/$total_adult_qty)*100),2); ?></td>
                        </tr>
                        <?
                        $t++;
                        $total_top_defect_qty+=$top_defect_qty;
                        }
                     }
                        ?>
                     </tbody>
                     <tfoot>
                     <tr>
                     <td> <? ?></td>
                     </tr>
                     </tfoot>
                     </table>
                     </td>
                     <td>
                 	 <table border="1" class="rpt_table" rules="all" width="420" cellpadding="0" cellspacing="0" >
                       <caption><b> Major Six Defects On (Wash & Measurement) </b> </caption>
                       <thead>
                       
                       <tr>
                         <th>SL</th>
                         <th width="150">DEFECT DESCRIPTION</th>
                         <th width="100">Total Audit qty</th>
                         <th width="100">NO OF DEFECT</th>
                         <th>%</th>
                       </tr>
                       
                         </thead>
                         <tbody>
                         <?
						 $m=1; $mm=1;//$mesurement_defect_qty_arr[$measure_type_id]
						 $measurement_defect_gr_arr=array();
                          foreach($wash_me_sew_defect_top6_arr2 as $measure_type_id=>$measurement_val)
						  {
							  	if ($m%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
							if($mm<=6)
                       		 {
								$measurement_qty=$mesurement_defect_qty_arr[$measure_type_id];
								$top_six_wash_me_percent_graph_arr[$measure_type_id]=($measurement_val/$total_adult_qty)*100;
						 ?>
                          	<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('trmeasure_<? echo $m; ?>','<? echo $bgcolor;?>')" id="trmeasure_<? echo $m;?>">
                            <td  align="center"><? echo $m;?></td>
                            <td  align="center"><? echo $measure_type_id;?></td>
                            <td  align="right"><? echo $total_adult_qty;?></td>
                            <td  align="right"><? echo $measurement_val;?></td>
                            <td  align="right" title="Defect Qty/Audit Qty*100"><? echo number_format((($measurement_val/$total_adult_qty)*100),2); ?></td>
                            </tr>
                            <?
							//$measurement_defect_gr_arr[$sew_defect_topMeasure_arr[$measure_type_id]]=($measurement_val/$total_adult_qty)*100;
							$m++;$mm++;
							 }
						  }
						 // print_r($measurement_defect_gr_arr);
							$caption="Defective rates of work (Finish)";
						  $line=0;	$lineName_data="[";	$linePercent_data="[";	
						  $tt=count($line_graph_name_arr);
						  foreach($line_graph_name_arr as $lname=>$val)
						  {
							//$linePercent=$linePercent_data[$lname];
							//if($line==0)  $lineName_data.="'".$lname."']"; else $lineName_data.="'".$lname."']".",";
							$line_name_Graph_arr[]=$lname;
							$line_per_Graph_arr[]=$line_graph_percent_arr[$lname];
							//if($line!=$tt)  $linePercent_data.="'".$linePercent."'".",";else   $linePercent_data.="'".$linePercent."']";
						  }
						// echo $lineName_data;
						 
						 $linePercent_dataArr= json_encode($line_per_Graph_arr); 
						$line_graph_name_Arr= json_encode($line_name_Graph_arr); 
						//$month_array= json_encode($month_array); 
							?>
                         </tbody>
                       </table>
                 </td>
           </tr>
           <tr>
                 <td width="300">
                 	   
                       <div style="width:100%; height:400px; position:relative; margin-left:10px; border:solid 1px">
                        <table style="margin-left:60px; font-size:12px" align="center">
                            <tr>
                                <td colspan="4" align="center"><b style="margin-left:220px;">Defective rates of work (Finish)</b></td>
                            </tr>
                        </table>
                        <canvas id="canvas2" height="245" width="700"></canvas>
                    </div>
        
                  </td>
                   <td  width="600">
                   <?
                   //$top_six_percent_graph_arr[$key_type]
				  if(count($top_six_percent_graph_arr)>0)
				  {
				 
					 $lineSixData='[';
				    foreach($top_six_percent_graph_arr as $top_six_defect=>$val)
				    {
					  // $top_six_defect = wordwrap($top_six_defect, 10, "\n", 1);
					 
					$lineSixData.="{name: '".$top_six_defect.':'.number_format($val,2,'.','').'%'."',y: ".$val."},";
				    }
				  	$lineSixData=rtrim($lineSixData,',');
					$lineSixData.=']';
				  // echo  $chart_data_qntyArr.'M';
				  $m=1;
				 
				   ?>
                  <div style="width:650px; height:400px; position:relative; margin-left:10px; border:solid 1px">   
                   
                          <div id="container<? echo $m;?>"></div>
						 <script>hs_chart(<? echo $m;?>,<? echo $lineSixData;?>,'Value');</script>
                    
                    </div>
       			 <?
				  }
					?>
                  </td>
                  <td width="10">&nbsp;
                  
                  </td>
                  <td  width="500">
                   <?
				  if(count($top_six_wash_me_percent_graph_arr)>0)
				  {
                   //$top_six_percent_graph_arr[$key_type]
				// print_r($measurement_defect_gr_arr);
					$mm_defectData='[';
				   foreach($top_six_wash_me_percent_graph_arr as $mm_defect=>$mmval)
				   {
					  // $top_six_defect = wordwrap($top_six_defect, 10, "\n", 1);
					// echo $mmval.'m';
					$mm_defectData.="{name: '".$mm_defect.':'.number_format($mmval,2,'.','').'%'."',y: ".$mmval."},";
				   }
				  	$mm_defectData=rtrim($mm_defectData,',');
					$mm_defectData.=']';
				  // echo  $chart_data_qntyArr.'M';
				  $mmm=3;
				 
				   ?>
                  <div style="width:500px; height:400px; position:relative; margin-left:10px; border:solid 1px">   
                   
                          <div id="container<? echo $mmm;?>"></div>
						<script>hs_chart_mm(<? echo $mmm;?>,<? echo $mm_defectData;?>,'Value');</script>
                    
                    </div>
                    <?
				  }
					?>
        
                  </td>
                  
                   <td  width="100">
                   </td>
                    <td  width="650">
                      <table border="1" class="rpt_table" rules="all" width="600" cellpadding="0" cellspacing="0" >
                       
                       <thead>
                       <tr>
                         <th rowspan="2" width="100"><div class=""  style=" word-break:break-all">LINE NO</div></th>
                         <th colspan="4" width="">IN SIDE</th>
                         <th colspan="4" width="">Top SIDE</th>
                         <th colspan="4" width="">GetUp</th>
                         </tr>
                         <tr>
                         <th width="100">AUDIT</th> 
                         <th width="100">DEFECT</th>
                         <th width="100">PASS</th>
                         <th>%</th>
                          <th width="100">AUDIT</th> 
                         <th width="100">DEFECT</th>
                         <th width="100">PASS</th>
                         <th>%</th>
                          <th width="100">AUDIT</th> 
                         <th width="100">DEFECT</th>
                         <th width="100">PASS</th>
                         <th>%</th>
                        </tr>
                        </thead>
                        <?
						$s=1;$total_audit_inside_qty=$total_inside_qty=$total_inside_pass_qty=$total_topside_qty=$total_topside_pass_qty=$total_getup_qty=$total_inside_dif_qty=$total_inside_dif_qty=$total_getup_pass_qty=0;
						//print_r($line_fin_arr);
                        foreach($line_fin_arr as $lineid=>$audit_qty)
						{
							 $audit_qty=0;
							 $audit_qty=$line_fin_qty_arr[$lineid];
							 if ($s%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	//summary_inside_line_wise_defect_arr
						?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('trsum_<? echo $s; ?>','<? echo $bgcolor;?>')" id="trsum_<? echo $s;?>">
                        <td  width="70" class="" align="right" style=""><?  echo $lineArr[$lineid];   ?></td>
                        
                        <td  width="70" class="" align="right" style=""><?  echo $audit_qty;  ?></td>
                        <td  width="70" class="" align="right" style=""><?   echo $summary_inside_line_wise_defect_arr[$lineid][1];   ?></td>
                        <td  width="70" class="" align="right" style=""><?   $inside_dif_qty=$audit_qty-$summary_inside_line_wise_defect_arr[$lineid][1]; 
																				echo number_format($inside_dif_qty,2);   ?></td> 
                        <td  width="" class="" align="right" style="" title="Defect/Audit*100"><?  echo number_format(($summary_inside_line_wise_defect_arr[$lineid][1]/$audit_qty)*100,2);   ?></td>
                        
                        <td  width="70" class="" align="right" style=""><?  echo $audit_qty;//$line_audit_qty_arr[$lineid];   ?></td>
                        <td  width="70" class="" align="right" style=""><?  echo  $summary_line_top_wise_defect_arr[$lineid][2];   ?></td>
                        <td  width="70" class="" align="right" style=""><?   $topside_dif_qty=$audit_qty-$summary_line_top_wise_defect_arr[$lineid][2]; 
																				echo number_format($topside_dif_qty,2); ;   ?></td>
                        <td  width="" class="" align="right" style=""><?  echo number_format(($summary_line_top_wise_defect_arr[$lineid][2]/$audit_qty)*100,2);    ?></td>
                        
                        <td  width="70" class="" align="right" style=""><?  echo $audit_qty;   ?></td>
                        <td  width="70" class="" align="right" style=""><?  echo $summary_getup_line_wise_defect_arr[$lineid][3];;   ?></td>
                        <td  width="70" class="" align="right" style=""><?  $getup_dif_qty=$audit_qty-$summary_getup_line_wise_defect_arr[$lineid][3]; 
																				echo number_format($getup_dif_qty,2);echo $getup_dif_qty;   ?></td>
                        <td  width="" class="" align="right" style=""><?    echo number_format(($summary_getup_line_wise_defect_arr[$lineid][3]/$audit_qty)*100,2);;   ?></td>
                        
                       </tr>
                        <?
						$s++;
						 $total_audit_inside_qty+=$audit_qty;
						  $total_inside_qty+=$summary_inside_line_wise_defect_arr[$lineid][1];
						 $total_topside_qty+=$summary_line_top_wise_defect_arr[$lineid][2];
						 $total_getup_qty+=$summary_getup_line_wise_defect_arr[$lineid][3];
						  $total_inside_pass_qty+=$inside_dif_qty;
						  $total_topside_pass_qty+=$topside_dif_qty;
						  $total_getup_pass_qty+=$getup_dif_qty;
							
						}
						?>
                        <tfoot>
                        <tr>
                        <td> PROCESS TOTAL</td>
                        <td align="right"> <? echo number_format($total_audit_inside_qty,2); ?></td>
                        <td align="right"> <? echo number_format($total_inside_qty,2); ?></td>
                        <td align="right"> <? echo number_format($total_inside_pass_qty,2); ?></td>
                         <td align="right"> <? echo number_format(($total_inside_qty/$total_audit_inside_qty*100),2); ?></td>
                         
                         <td align="right"> <? echo number_format($total_audit_inside_qty,2); ?></td>
                         <td align="right"> <? echo number_format($total_topside_qty,2); ?></td>
                          <td align="right"> <? echo number_format($total_topside_pass_qty,2); ?></td>
                         <td align="right"> <? echo number_format(($total_topside_qty/$total_audit_inside_qty*100),2); ?></td>
                        
                          <td> <? echo number_format($total_audit_inside_qty,2); ?></td>
                         <td align="right"> <? echo number_format($total_getup_qty,2); ?></td>
                          <td align="right"> <? echo number_format($total_getup_pass_qty,2); ?></td>
                         <td align="right"> <? echo number_format(($total_getup_qty/$total_audit_inside_qty*100),2); ?></td>
                         
                        </tr>
                         <tr>
                        <td> Grand TOTAL</td>
                        <td colspan="4" align="center" title="Audit Qty Inside+TopSide+GetUp"> <? $tot_grd_audit_inside=$total_audit_inside_qty+$total_audit_inside_qty+$total_audit_inside_qty;echo number_format($tot_grd_audit_inside,2); ?></td>
                        <td colspan="4" title="InsideQty+TopSide+GetUpQty" align="center"> <? $tot_grd_audit_top=$total_inside_qty+$total_topside_qty+$total_getup_qty;echo number_format($tot_grd_audit_top,2); ?></td>
                        <td colspan="3" align="center"> <? echo number_format($total_topside_pass_qty+$total_inside_pass_qty+$total_getup_pass_qty,2); ?></td>
                        
                         <td align="right"> <? echo number_format(($tot_grd_audit_top/$tot_grd_audit_inside)*100,2); ?></td>
                       
                        
                         
                        </tr>
                        </tfoot>
                          
                         
                         </table>
                    </td>
                    
                 </tr>
                 
          </table>
		<?
        if(count($line_graph_name_Arr)>0)
		{
		?>
		<script>
        
        var barChartData2 = {
            labels : <? echo $line_graph_name_Arr; ?>,
            datasets : [
                    {
                        fillColor : "green",
                        //strokeColor : "rgba(220,220,220,0.8)",
                        //highlightFill: "rgba(220,220,220,0.75)",
                        //highlightStroke: "rgba(220,220,220,1)",
                        data : <? echo $linePercent_dataArr; ?>
                    }
                ]
            }
            
            var ctx2 = document.getElementById("canvas2").getContext("2d");
            window.myBar = new Chart(ctx2).Bar(barChartData2, {
                responsive : true
            });
        </script>
        <?
		}
		?>
	</div>
	<?
    foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}

	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data####$filename";
	exit(); 
}

if ($action==="challan_popup")
{
	echo load_html_head_contents("Challan Info","../../../", 1, 1, '','','');
	extract($_REQUEST);
	list($company_id, $work_comp_ids, $order_id, $job_no, $buyer_id, $location_ids, $floor_ids, $txt_date_from, $txt_date_to) = explode('**', $data);
	//echo $company_id;die;
	$company_arr=return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0", 'id','company_name');

	$company_cond=$work_comp_cond=$buyer_cond='';
	$job_no_cond=$location_cond=$floor_cond='';
		
	if ($company_id != 0) $company_cond=" and d.company_name=$company_id";	
	if ($work_comp_ids != '') $work_comp_cond=" and a.delivery_company_id in($work_comp_ids)";
	if ($buyer_id != 0) $buyer_cond=" and d.buyer_name=$buyer_id";
	if ($job_no != '') $job_no_cond=" and d.job_no='".$job_no."'";
	if ($location_ids != '') $location_cond=" and a.delivery_location_id in($location_ids)";		
	if ($floor_ids != '') $floor_cond=" and a.delivery_floor_id in($floor_ids)";

	$date_cond = '';
	if ($txt_date_from != '' && $txt_date_to != '') {
		$date_cond = " and b.ex_factory_date between '".$txt_date_from."' and '".$txt_date_to."'";
	}
		

	$sql_challan_dtls = "SELECT  a.sys_number as CHALLAN_NO, a.DELIVERY_COMPANY_ID, b.EX_FACTORY_DATE, b.EX_FACTORY_QNTY, b.SHIPING_MODE 		
	from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b, wo_po_break_down c, wo_po_details_master d
	where a.id=b.delivery_mst_id and b.po_break_down_id=c.id and c.job_no_mst=d.job_no and c.id=$order_id $company_cond $work_comp_cond $location_cond $floor_cond $buyer_cond $job_no_cond $date_cond and b.entry_form!=85 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 
	order by b.ex_factory_date ASC";

	$sql_challan_dtls_res=sql_select($sql_challan_dtls);	
	$table_width = 600;
	?>
	<div style="width:100%" id="report_container">
		<table width="<? echo $table_width; ?>" cellspacing="0" cellpadding="0">
			<thead>
				<tr>
					<td colspan="6" style="font-size:16px" width="100%" align="center"><strong>Challan Info</strong>
					</td>
				</tr>
			</thead>
		</table>
			<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_header">				
				<thead>
					<tr>
	                    <th width="50">SL</th>
	                    <th width="100">Date</th>
	                    <th width="120">Delivery Company</th>
	                    <th width="120">Challan No</th>
	                    <th width="100">Quantity</th>
	                    <th width="100">Ship Mode</th>
                    </tr>             	
				</thead>				
            </table>
            <div style="width:<? echo $table_width+20; ?>px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_body">
	                <tbody>
	                <?	               		               			
               		$i=1;
               		$total_quantity=0;
               		foreach ($sql_challan_dtls_res as $row) 
               		{
	               		if($i%2==0) $bgcolor="#E9F3FF"; 
	               		else $bgcolor="#FFFFFF";  		
	               		?>
						<tr bgcolor="<?= $bgcolor; ?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')" id="tr_<?= $i; ?>">
							<td width="50" align="center"><?= $i; ?></td>
							<td width="100"><p><?= change_date_format($row['EX_FACTORY_DATE']); ?></p></td>
                            <td width="120"><p><?= $company_arr[$row['DELIVERY_COMPANY_ID']]; ?></p></td>
                            <td width="120"><p><?= $row['CHALLAN_NO']; ?></p></td>
                            <td width="100" align="right"><p><?= number_format($row['EX_FACTORY_QNTY'],0); ?></p></td>
                            <td width="100" align="center"><p><?= $shipment_mode[$row['SHIPING_MODE']]; ?></p></td>
                        </tr>
	                    <?
	                    $i++;
	                    $total_quantity += $row['EX_FACTORY_QNTY'];
                    }
                    ?>						
	                </tbody>
	            </table>

	            <table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="report_table_footer">
					<tfoot>
						<th width="50">&nbsp;</th>
	                    <th width="100">&nbsp;</th>
	                    <th width="120">&nbsp;</th>
	                    <th width="120">Total:</th>
	                    <th width="100"><?= number_format($total_quantity,0); ?></th>
	                    <th width="100">&nbsp;</th>	                   
					</tfoot>
	            </table>            	
            </div>           
        </div>
    <?
	exit();
}
