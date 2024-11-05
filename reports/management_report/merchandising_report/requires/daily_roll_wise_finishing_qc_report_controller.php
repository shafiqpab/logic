<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../../includes/common.php');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );

if ($action == "load_drop_down_knitting_com") {
	$data = explode("_", $data);
	//print_r($data);
	$company_id = $data[1];
	//$company_id
	if ($data[0] == 1) {
		echo create_drop_down("cbo_knitting_company", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "--Select Knit Company--", "", "load_location();load_drop_down( 'requires/daily_roll_wise_finishing_qc_report_controller', this.value, 'load_drop_down_floor', 'cbo_del_floor' );", "");
	} else if ($data[0] == 3) {
		echo create_drop_down("cbo_knitting_company", 120, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--Select Knit Company--", 0, "load_location();");
	} else {
		echo create_drop_down("cbo_knitting_company", 120, $blank_array, "", 1, "--Select Knit Company--", 0, "load_location();");
	}
	exit();
}

if ($action=="load_drop_down_buyer")
{
	
	if($data!=0)
	{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond  group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0); 
	}
	else
	{
		echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0  $buyer_cond  group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",0,"" );
	}
	exit();
}
if ($action == "load_drop_down_location") {
	echo create_drop_down("cbo_location_name", 120, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name", "id,location_name", 1, "-- Select Location --", 0, "load_floor();");
exit();
}

if ($action == "load_drop_down_floor") {
	$data = explode("_", $data);
	$company_id = $data[0];
	$location_id = $data[1];
	if ($location_id == 0 || $location_id == "") $location_cond = ""; else $location_cond = " and b.location_id=$location_id";

	echo create_drop_down("cbo_del_floor", 120, "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=1 and b.company_id=$company_id and b.status_active=1 and b.is_deleted=0 and a.production_process=2 $location_cond group by a.id, a.floor_name order by a.floor_name", "id,floor_name", 1, "-- Select Floor --", 0, "", "");
	exit();
}

/*if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 172, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/daily_roll_wise_finishing_qc_report_controller', $data+'**'+this.value, 'load_drop_down_del_floor', 'del_floor_td' );" );		 
}
if ($action=="load_drop_down_del_floor")
{ 
	$data=explode('**',$data);
	echo create_drop_down( "cbo_del_floor", 120, "select id,floor_name from lib_prod_floor where company_id='$data[0]' and location_id='$data[1]' and status_active =1 and is_deleted=0 and production_process=11 order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "" );
	exit();	 
}*/

if($action=="order_no_search_popup")
{
	echo load_html_head_contents("Order No Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array; var selected_name = new Array;
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
		
		function js_set_value( str ) 
		{
			if (str!="") str=str.split("_");
			 
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			 
			if( jQuery.inArray( str[1], selected_id ) == -1 ) {
				selected_id.push( str[1] );
				selected_name.push( str[2] );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hide_order_id').val( id );
			$('#hide_order_no').val( name );
		}
    </script>
</head>

<body>
<div align="center">
	<form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:780px;">
            <table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Order No</th>
                    <th>Shipment Date</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
                    <input type="hidden" name="hide_order_no" id="hide_order_no" value="" />
                    <input type="hidden" name="hide_order_id" id="hide_order_id" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",0,"",0 );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Order No",2=>"Style Ref",3=>"Job No");
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_order_no_search_list_view', 'search_div', 'daily_roll_wise_finishing_qc_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    	</td>
                    </tr>
                    <tr>
                        <td colspan="5" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
	exit(); 
}
if($action=="create_order_no_search_list_view")
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
	$search_string="%".trim($data[3])."%";

	if($search_by==1) 
		$search_field="b.po_number"; 
	else if($search_by==2) 
		$search_field="a.style_ref_no"; 	
	else 
		$search_field="a.job_no";
		
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
	else
	{
		$date_cond="";
	}
	
	$company_short_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$arr=array (0=>$company_short_arr,1=>$buyer_arr);
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	
	$sql= "select b.id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $date_cond order by b.id, b.pub_shipment_date";
		
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Po No, Shipment Date", "70,70,50,70,150,180","760","220",0, $sql , "js_set_value", "id,po_number","",1,"company_name,buyer_name,0,0,0,0,0",$arr,"company_name,buyer_name,year,job_no_prefix_num,style_ref_no,po_number,pub_shipment_date","",'','0,0,0,0,0,0,3','',1) ;
   exit(); 
}

$defect_id_value_arr[1]="hole_defect_count";
$defect_id_value_arr[5]="dye_defect_count";
$defect_id_value_arr[10]="insect_defect_count";
$defect_id_value_arr[15]="yellowSpot_defect_count";
$defect_id_value_arr[20]="poly_defect_count";
$defect_id_value_arr[25]="dust_defect_count";
$defect_id_value_arr[30]="oilspot_defect_count";
$defect_id_value_arr[35]="flyconta_defect_count";
$defect_id_value_arr[40]="slub_defect_count";
$defect_id_value_arr[45]="patta_defect_count";
$defect_id_value_arr[50]="cut_defect_count";
$defect_id_value_arr[55]="sinker_defect_count";
$defect_id_value_arr[60]="print_mis_defect_count";
$defect_id_value_arr[65]="yarn_conta_defect_count";
$defect_id_value_arr[70]="slub_hole_defect_count";
$defect_id_value_arr[75]="softener_Spot_defect_count";
$defect_id_value_arr[95]="dirty_stain_defect_count";
$defect_id_value_arr[100]="neps_defect_count";
$defect_id_value_arr[105]="needle_drop_defect_count";
$defect_id_value_arr[110]="chem_defect_count";
$defect_id_value_arr[115]="cotton_seeds_defect_count";
$defect_id_value_arr[120]="Loop_hole_defect_count";
$defect_id_value_arr[125]="dead_cotton_defect_count";
$defect_id_value_arr[130]="thick_thin_defect_count";
$defect_id_value_arr[135]="rust_spot_defect_count";
$defect_id_value_arr[140]="needle_broken_mark_defect_count";
$defect_id_value_arr[145]="dirty_spot_defect_count";
$defect_id_value_arr[150]="side_center_shade_defect_count";
$defect_id_value_arr[155]="bowing_defect_count";
$defect_id_value_arr[160]="uneven_defect_count";
$defect_id_value_arr[165]="yellow_writing_defect_count";
$defect_id_value_arr[170]="fabric_missing_defect_count";
$defect_id_value_arr[175]="dia_mark_defect_count";
$defect_id_value_arr[180]="miss_print_defect_count";
$defect_id_value_arr[185]="hairy_defect_count";
$defect_id_value_arr[190]="gsm_hole_defect_count";
$defect_id_value_arr[195]="compacting_mark_defect_count";
$defect_id_value_arr[200]="rib_body_shade_defect_count";
$defect_id_value_arr[205]="running_shade_defect_count";
$defect_id_value_arr[210]="plastic_conta_defect_count";
$defect_id_value_arr[215]="crease_mark_defect_count";
$defect_id_value_arr[220]="patches_defect_count";
$defect_id_value_arr[225]="mc_toppage_defect_count";
$defect_id_value_arr[230]="needle_line_defect_count";
$defect_id_value_arr[235]="crample_mark_defect_count";
$defect_id_value_arr[240]="shite_specks_defect_count";
$defect_id_value_arr[245]="mellange_effect_defect_count";
$defect_id_value_arr[250]="line_mark_defect_count";
$defect_id_value_arr[255]="loop_out_defect_count";
$defect_id_value_arr[260]="needle_broken_defect_count";
$defect_id_value_arr[261]="loop_defect_count";
$defect_id_value_arr[262]="DEFECT_COUNT";
$defect_id_value_arr[263]="lycra_out_drop_defect_count";
$defect_id_value_arr[264]="miss_yarn_defect_count";
$defect_id_value_arr[265]="color_contra_defect_count";
$defect_id_value_arr[266]="friction_mark_defect_count";
$defect_id_value_arr[267]="pin_out_defect_count";
$defect_id_value_arr[268]="rust_stain_defect_count";
$defect_id_value_arr[269]="stop_mark_defect_count";
$defect_id_value_arr[270]="compacting_broken_defect_count";
$defect_id_value_arr[271]="grease_spot_defect_count";
$defect_id_value_arr[272]="cut_hole_defect_count";
$defect_id_value_arr[273]="snagging_pull_out_defect_count";
$defect_id_value_arr[274]="press_off_defect_count";
$defect_id_value_arr[275]="wheel_free_defect_count";
$defect_id_value_arr[276]="count_mix_defect_count";
$defect_id_value_arr[277]="black_spot_defect_count";
$defect_id_value_arr[278]="set_up_defect_count";
$defect_id_value_arr[279]="pin_ole_defect_count";



if($action=="report_generate")
{ 
	$started= microtime(true);
	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	 
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$cbo_knitting_source=str_replace("'","",$cbo_knitting_source);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_knitting_company=str_replace("'","",$cbo_knitting_company);
	$cbo_location_name=str_replace("'","",$cbo_location_name);
	$cbo_delivery_floor=str_replace("'","",$cbo_del_floor);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_year=str_replace("'","",$cbo_year);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$hide_order_id=str_replace("'","",$hide_order_id);
	$txt_barcode_no=str_replace("'","",$txt_barcode_no);
	$txt_batch_no=str_replace("'","",$txt_batch_no);
	$txt_date_from_qc=str_replace("'","",$txt_date_from_qc);
	$txt_date_to_qc=str_replace("'","",$txt_date_to_qc);
	$cbo_roll_status=str_replace("'","",$cbo_roll_status);
	$cbo_with_defect=str_replace("'","",$cbo_with_defect);
	$cbo_date_range_type=str_replace("'","",$cbo_date_range_type);
	
	if($cbo_knitting_source>0){$knitting_source_cond="and a.knitting_source=$cbo_knitting_source";}else{$knitting_source_cond="";}
	if($cbo_delivery_floor>0){$del_floor_cond="and b.floor_id=$cbo_delivery_floor";}else{$del_floor_cond="";}
	if($hide_order_id!=""){$hide_order_cond="and c.po_breakdown_id in($hide_order_id)";}else{$hide_order_cond="";}
	if($cbo_buyer_name>0){$buyer_name_cond="and a.buyer_id =$cbo_buyer_name";}else{$buyer_name_cond="";}
	if($cbo_company_name>0){$company_name_cond="and a.company_id =$cbo_company_name";}else{$company_name_cond="";}

	$job_no_cond="and c.po_breakdown_id="."'".$orderID."'";
	//if($cbo_knitting_source>0){$knitting_source_cond="and a.knitting_source=$cbo_knitting_source";}else{$knitting_source_cond="";}

	if($txt_job_no!="")
	{
		$jobs=trim($txt_job_no);
		 $txt_jobNo="'".$jobs."'";
		 // change this query
		$po_id_arr = return_library_array("select job_no_mst, id from wo_po_break_down where job_no_mst=$txt_jobNo", 'job_no_mst', 'id');
		
		
		$sqls=sql_select("select job_no_mst, id from wo_po_break_down where job_no_mst=$txt_jobNo");
		$orderID="";
		foreach($sqls as $k=>$val)
		{
			$j=$val[csf("job_no_mst")];
			$job="'".$j."'";
			$orderID=$po_id_arr[$job]=$val[csf("id")];
		}
		
		$job_no_cond="and c.po_breakdown_id="."'".$orderID."'";
		//$job_no_cond=$orderID;
	}
	else
	{
		  $job_no_cond="";
	}

  	$cbo_year=str_replace("'","",$cbo_year);
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0)
		{
			$year_cond=" and YEAR(a.insert_date)=$cbo_year";
		}
		else
		{
			$year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";	
		}
	}
	
	//if(trim($txt_job_no)!="") $job_no_cond="%".trim($txt_job_no); else $job_no_cond="%%";//."%"
	if($cbo_date_range_type==2)
	{
		if($txt_date_from!="" && $txt_date_to!="")
		{
			$str_cond_date="and a.qc_date between '$txt_date_from' and  '$txt_date_to ' ";
		}
		else
		{
			$str_cond_date="";
		}
	}
	else
	{
		if($txt_date_from!="" && $txt_date_to!="")
		{
			$str_cond_date="and a.receive_date between '$txt_date_from' and  '$txt_date_to ' ";
		}
		else
		{
			$str_cond_date="";
		}
	}
		
	/*if($txt_date_from_qc!="" && $txt_date_to_qc!="")
	{
		$qc_date_cond="and d.qc_date between '$txt_date_from_qc' and  '$txt_date_to_qc ' ";
	}
	else
	{
		$qc_date_cond="";
	}*/
	if($cbo_knitting_company>0)
	{
		 $knit_comp_cond="and a.knitting_company='$cbo_knitting_company' ";
	}
	else
	{
		 $knit_comp_cond="";
	}
	if($cbo_location_name>0)
	{
		 $del_location_cond="and a.location_id='$cbo_location_name' ";
	}
	else
	{
		 $del_location_cond="";
	} 
	
	if($cbo_delivery_floor>0)
	{
		 $del_floor_cond="and b.delivery_floor_id='$cbo_delivery_floor' ";
	}
	else
	{
		 $del_floor_cond="";
	} 
	
	if($txt_barcode_no!="")
	{
		$barcode_cond="and c.barcode_no='$txt_barcode_no'";
	}
	else
	{
		$barcode_cond="";
	}
	if($txt_batch_no!="")
	{
		$batch_cond="and d.batch_no='$txt_batch_no'";
	}
	else
	{
		$program_cond="";
	}
	
	if($txt_booking_no!="")
	{
		$booking_cond=" and a.booking_without_order=1 and a.booking_no like '%$txt_booking_no%'";
	}
	else
	{
		$booking_cond="";
	}
	
	$details_report="";
	$master_data=array();
	$current_date=date("Y-m-d");
	$date=date("Y-m-d");$break_id=0;$sc_lc_id=0;
	$sy = date('Y',strtotime($txt_date_from));
	
	ob_start();
	?>
    <style type="text/css">
            .block_div { 
				width:auto;
				height:auto;
				text-wrap:normal;
				vertical-align:bottom;
				display: block;
				position: !important; 
				-webkit-transform: rotate(-90deg);
				-moz-transform: rotate(-90deg);					
            }
    </style>    
  
	<? 	
	if($report_format==1) // Show
	{
  		$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
		$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
		$machine_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");				
		$yarn_count_lib_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
		$operator_lib_arr = return_library_array("select id, first_name from lib_employee", 'id', 'first_name');
		$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
		//need customize style_library
		
		$knitting_company_library = return_library_array("select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id", "supplier_name");


		if($cbo_date_range_type==2)
		{	
			$sql_qc_barcode="SELECT a.barcode_no as BARCODE_NO from pro_qc_result_mst a  where  a.status_active=1 and a.is_deleted=0 $str_cond_date";
			$sql_qc_barcodes=sql_select($sql_qc_barcode);
			$con = connect();
			if(!empty($sql_qc_barcodes))	
		 	{
	            $r_id_barcode=execute_query("delete from tmp_barcode_no where userid=$user_id");
				if($r_id_barcode)
				{
				    oci_commit($con);
				}
			}
			$qc_barcode_check=array();
			foreach($sql_qc_barcodes as $row)
			{
				if(!$qc_barcode_check[$row['BARCODE_NO']])
				{
					$qc_barcode_check[$row['BARCODE_NO']]=$row['BARCODE_NO'];
					$qc_barcodes = $row['BARCODE_NO'];
					$rID_barcode=execute_query("insert into tmp_barcode_no (userid, barcode_no) values ($user_id,$qc_barcodes)"); 
				}
			}
			if($rID_barcode)
			{
				oci_commit($con);
			}
		}

		$add_table_cond=$add_where_cond="";
		if ($cbo_roll_status!=0) 
		{
			$add_table_cond= ", pro_qc_result_mst e";
			$add_where_cond = " and c.barcode_no=e.barcode_no and e.roll_status=$cbo_roll_status ";
		}
		if($cbo_date_range_type==2)
		{
			$sql_finish_prod_entry="SELECT a.id as ID,a.receive_date as RECEIVE_DATE, a.knitting_source as KNITTING_SOURCE,a.knitting_company as KNITTING_COMPANY, a.recv_number as RECV_NUMBER, a.buyer_id as BUYER_ID, a.company_id as COMPANY_ID, a.receive_basis as RECEIVE_BASIS, d.booking_no_id as BOOKING_ID, d.booking_no as BOOKING_NO,  a.booking_without_order as BOOKING_WITHOUT_ORDER,d.booking_without_order as WITHOUTORDER, a.store_id as STORE_ID, a.location_id as LOCATION_ID,a.sub_contract as SUB_CONTRACT , a.challan_no as CHALLAN_NO, a.yarn_issue_challan_no as YARN_ISSUE_CHALLAN_NO,d.batch_against as BATCH_AGAINST, a.remarks as REMARKS,a.roll_maintained as ROLL_MAINTAINED ,a.service_booking_no as SERVICE_BOOKING_NO,a.service_booking_without_order as SERVICE_BOOKING_WITHOUT_ORDER, a.within_group as WITHIN_GROUP, b.id as PRO_DTLS_ID,b.machine_no_id as MACHINE_NO_ID,b.no_of_roll as NO_OF_ROLL,b.shift_name as SHIFT_NAME,b.color_id as COLOR_ID,b.gsm as GSM,b.prod_id as  PROD_ID,b.width as WIDTH,b.fabric_description_id as FABRIC_DESCRIPTION_ID,b.rate as RATE,b.batch_id as BATCH_ID,d.batch_no as BATCH_NO,sum(d.batch_weight) as BATCH_QNTY, c.po_breakdown_id as ORDER_ID, c.barcode_no as BARCODE_NO, c.id as ROLL_ID, c.roll_no as ROLL_NO, sum(c.qnty) as ROLL_WEIGHT, sum(c.qnty) as QC_PASS_QNTY
			from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c, pro_batch_create_mst d,tmp_barcode_no e $add_table_cond 
			where  a.id=b.mst_id and b.id=c.dtls_id and b.batch_id=d.id and c.barcode_no=e.barcode_no and a.entry_form in(66) and c.entry_form in(66) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.userid=$user_id $knit_comp_cond $company_name_cond $del_floor_cond $job_no_cond $hide_order_cond $knitting_source_cond $year_cond $del_location_cond $buyer_name_cond $barcode_cond $booking_cond $batch_cond  $add_where_cond
			group by a.id,a.receive_date, a.knitting_source,a.knitting_company, a.recv_number, a.buyer_id, a.company_id, a.receive_basis, d.booking_no_id, d.booking_no, a.booking_without_order,d.booking_without_order, a.store_id, a.location_id,a.sub_contract, a.challan_no, a.yarn_issue_challan_no, a.remarks,a.roll_maintained,a.service_booking_no,a.service_booking_without_order, a.within_group,b.id,b.machine_no_id,b.no_of_roll,b.shift_name,b.color_id,b.gsm,c.po_breakdown_id,b.prod_id,b.width,b.fabric_description_id,b.rate,b.batch_id,c.barcode_no, c.id, c.roll_no, c.po_breakdown_id,d.batch_no,d.batch_against
			order by c.barcode_no";
		}
		else
		{
			$sql_finish_prod_entry="SELECT a.id as ID,a.receive_date as RECEIVE_DATE, a.knitting_source as KNITTING_SOURCE,a.knitting_company as KNITTING_COMPANY, a.recv_number as RECV_NUMBER, a.buyer_id as BUYER_ID, a.company_id as COMPANY_ID, a.receive_basis as RECEIVE_BASIS, d.booking_no_id as BOOKING_ID, d.booking_no as BOOKING_NO,  a.booking_without_order as BOOKING_WITHOUT_ORDER,d.booking_without_order as WITHOUTORDER, a.store_id as STORE_ID, a.location_id as LOCATION_ID,a.sub_contract as SUB_CONTRACT , a.challan_no as CHALLAN_NO, a.yarn_issue_challan_no as YARN_ISSUE_CHALLAN_NO,d.batch_against as BATCH_AGAINST, a.remarks as REMARKS,a.roll_maintained as ROLL_MAINTAINED ,a.service_booking_no as SERVICE_BOOKING_NO,a.service_booking_without_order as SERVICE_BOOKING_WITHOUT_ORDER, a.within_group as WITHIN_GROUP, b.id as PRO_DTLS_ID,b.machine_no_id as MACHINE_NO_ID,b.no_of_roll as NO_OF_ROLL,b.shift_name as SHIFT_NAME,b.color_id as COLOR_ID,b.gsm as GSM,b.prod_id as  PROD_ID,b.width as WIDTH,b.fabric_description_id as FABRIC_DESCRIPTION_ID,b.rate as RATE,b.batch_id as BATCH_ID,d.batch_no as BATCH_NO,sum(d.batch_weight) as BATCH_QNTY, c.po_breakdown_id as ORDER_ID, c.barcode_no as BARCODE_NO, c.id as ROLL_ID, c.roll_no as ROLL_NO, sum(c.qnty) as ROLL_WEIGHT, sum(c.qnty) as QC_PASS_QNTY
			from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c, pro_batch_create_mst d $add_table_cond 
			where  a.id=b.mst_id and b.id=c.dtls_id and b.batch_id=d.id and a.entry_form in(66) and c.entry_form in(66) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $knit_comp_cond $company_name_cond $del_floor_cond $job_no_cond $hide_order_cond $knitting_source_cond $year_cond $del_location_cond $buyer_name_cond $str_cond_date $barcode_cond $booking_cond $batch_cond $add_where_cond
			group by a.id,a.receive_date, a.knitting_source,a.knitting_company, a.recv_number, a.buyer_id, a.company_id, a.receive_basis, d.booking_no_id, d.booking_no, a.booking_without_order,d.booking_without_order, a.store_id, a.location_id,a.sub_contract, a.challan_no, a.yarn_issue_challan_no, a.remarks,a.roll_maintained,a.service_booking_no,a.service_booking_without_order, a.within_group,b.id,b.machine_no_id,b.no_of_roll,b.shift_name,b.color_id,b.gsm,c.po_breakdown_id,b.prod_id,b.width,b.fabric_description_id,b.rate,b.batch_id,c.barcode_no, c.id, c.roll_no, c.po_breakdown_id,d.batch_no,d.batch_against
			order by c.barcode_no";
		}

		//b.yarn_lot,b.color_range_id,b.yarn_count, b.machine_gg, b.stitch_length, b.machine_dia, b.operator_name, b.yarn_prod_id
		// echo $sql_finish_prod_entry;

		$sql_finish_prod_entry=sql_select($sql_finish_prod_entry);					

		$i=1;
		$con = connect();
		if(!empty($sql_finish_prod_entry))	
	 	{
	 		$r_id=execute_query("delete from tmp_recv_dtls where userid=$user_id");
            $r_id2=execute_query("delete from tmp_barcode_no where userid=$user_id");
            $r_id3=execute_query("delete from tmp_poid where userid=$user_id");
			if($r_id && $r_id2 && $r_id3)
			{
			    oci_commit($con);
			}
		}
		
		$finish_dtls_id_check=array();
		$production_barcode_check=array();
		foreach($sql_finish_prod_entry as $row)
		{
			if(!$finish_dtls_id_check[$row['PRO_DTLS_ID']])
			{
				$finish_dtls_id_check[$row['PRO_DTLS_ID']]=$row['PRO_DTLS_ID'];
				$pro_dtls_IDs = $row['PRO_DTLS_ID'];
				$rID=execute_query("insert into tmp_recv_dtls (userid, dtls_id) values ($user_id,$pro_dtls_IDs)");
			}

			/*if($rID)
			{
				oci_commit($con);
			}*/

			if(!$production_barcode_check[$row['BARCODE_NO']])
			{
				$production_barcode_check[$row['BARCODE_NO']]=$row['BARCODE_NO'];
				$pro_barcodes = $row['BARCODE_NO'];
				$rID2=execute_query("insert into tmp_barcode_no (userid, barcode_no) values ($user_id,$pro_barcodes)"); 
			}

			/*if($rID2)
			{
				oci_commit($con);
			}*/


			if(!$order_id_arr[$row["ORDER_ID"]])
			{
				$order_id_arr[$row['ORDER_ID']]=$row['ORDER_ID'];
				$pro_orders = $row['ORDER_ID'];
				$rID3=execute_query("insert into tmp_poid (userid, poid) values ($user_id,$pro_orders)"); 
			}

			/*if($rID3)
			{
				oci_commit($con);
			}*/


			$pro_dtls_id_arr[$row['PRO_DTLS_ID']]=$row['PRO_DTLS_ID'];
			//$order_id_arr[$row["ORDER_ID"]]=$row["ORDER_ID"];
			/*if ($row[csf("receive_basis")]==2) 
			{
				$booking_no = return_field_value("booking_no as booking_no", " ppl_planning_entry_plan_dtls ", "dtls_id='" . $row[csf("booking_id")] . "' group by booking_no", "booking_no");
			}
			else
			{							
				$booking_id_arr[$row[csf("booking_id")]]=$row[csf("booking_id")];
			}*/
			$batch_id_arr[$row["BATCH_ID"]]=$row["BATCH_ID"];
			$barcode_no_arr[$row["BARCODE_NO"]]=$row["BARCODE_NO"];

			$booking_id_arr[$row["BOOKING_ID"]]=$row["BOOKING_ID"];
			if(trim($row["COLOR_ID"])){
			$color_id_arr[$row["COLOR_ID"]]=chop($row["COLOR_ID"],",");
			}
			$prod_id_arr[$row["PROD_ID"]]=$row["PROD_ID"];
			$febric_description_id_arr[$row["FABRIC_DESCRIPTION_ID"]]=$row["FABRIC_DESCRIPTION_ID"];
		}

		if($rID && $rID2 && $rID3)
		{
			oci_commit($con);
		}


		//echo count($order_id_arr);die;
		//$all_po_ids= array_chunk($order_id_arr, 999);
		$all_booking_ids= array_chunk($booking_id_arr, 999);
		$pro_dtls_ids= array_chunk($pro_dtls_id_arr, 999);
		$color_Ids= array_chunk($color_id_arr, 800);
		$all_prod_ids= array_chunk($prod_id_arr, 999);
		$all_febric_description_ids= array_chunk($febric_description_id_arr, 999);
		$all_batch_ids= array_chunk($batch_id_arr, 999);
		$all_barcode_nos= array_chunk($barcode_no_arr, 999);


		$color_Ids_cond=" and(";
		foreach($color_Ids as $colorIds)
		{
			if($color_Ids_cond==" and(") $color_Ids_cond.=" id in(". implode(',', $colorIds).")"; else $color_Ids_cond.="  or id in(". implode(',', $colorIds).")";
		}
		$color_Ids_cond.=")";

		$all_prod_ids_cond=" and(";
		foreach($all_prod_ids as $prod_ids)
		{
			if($all_prod_ids_cond==" and(") $all_prod_ids_cond.=" a.id in(". implode(',', $prod_ids).")"; else $all_prod_ids_cond.="  or a.id in(". implode(',', $prod_ids).")";
		}
		$all_prod_ids_cond.=")";

		$all_febric_description_cond=" and(";
		foreach($all_prod_ids as $description_ids)
		{
			if($all_febric_description_cond==" and(") $all_febric_description_cond.=" a.id in(". implode(',', $description_ids).")"; else $all_febric_description_cond.="  or a.id in(". implode(',', $description_ids).")";
		}
		$all_febric_description_cond.=")";


		/*$po_ids_cond=" and("; $order_ids_cond=" and(";
		foreach($all_po_ids as $po_ids)
		{
			if($po_ids_cond==" and(") $po_ids_cond.=" a.id in(". implode(',', $po_ids).")"; else $po_ids_cond.="  or a.id in(". implode(',', $po_ids).")";


			if($order_ids_cond==" and(") $order_ids_cond.=" b.order_id in(". implode(',', $po_ids).")"; else $order_ids_cond.="  or b.order_id in(". implode(',', $po_ids).")";

		}
		$po_ids_cond.=")";
		$order_ids_cond.=")";*/

		$booking_ids_cond=" and(";$booking_ids_cond_2=" and(";
		foreach($all_booking_ids as $booking_ids)
		{
			if($booking_ids_cond==" and(") $booking_ids_cond.=" a.id in(". implode(',', $booking_ids).")"; else $booking_ids_cond.="  or a.id in(". implode(',', $booking_ids).")";
			if($booking_ids_cond_2==" and(") $booking_ids_cond_2.=" a.dtls_id in(". implode(',', $booking_ids).")"; else $booking_ids_cond_2.="  or a.dtls_id in(". implode(',', $booking_ids).")";
		}
		$booking_ids_cond.=")";
		$booking_ids_cond_2.=")";

		$pro_dtls_ids_cond=" and(";
		foreach($pro_dtls_ids as $pro_dtls_ids)
		{
			if($pro_dtls_ids_cond==" and(") $pro_dtls_ids_cond.=" d.pro_dtls_id in(". implode(',', $pro_dtls_ids).")"; else $pro_dtls_ids_cond.="  or d.pro_dtls_id in(". implode(',', $pro_dtls_ids).")";
		}
		$pro_dtls_ids_cond.=")";

		$all_batch_ids_cond=" and(";
		foreach($all_batch_ids as $all_batch_ids)
		{
			if($all_batch_ids_cond==" and(") $all_batch_ids_cond.=" b.mst_id in(". implode(',', $all_batch_ids).")"; else $all_batch_ids_cond.="  or b.mst_id in(". implode(',', $all_batch_ids).")";
		}
		$all_batch_ids_cond.=")";

		$all_barcode_nos_cond=" and(";
		foreach($all_barcode_nos as $all_barcode_nos)
		{
			if($all_barcode_nos_cond==" and(") $all_barcode_nos_cond.=" b.barcode_no in(". implode(',', $all_barcode_nos).")"; else $all_barcode_nos_cond.="  or b.barcode_no in(". implode(',', $all_barcode_nos).")";
		}
		$all_barcode_nos_cond.=")";

		
		$batch_data_array = sql_select("SELECT a.id, b.barcode_no,c.gsm_weight,d.dia_width from pro_batch_create_mst a, pro_batch_create_dtls b, wo_pre_cost_fabric_cost_dtls c,wo_pre_cos_fab_co_avg_con_dtls d where a.id=b.mst_id and b.po_id=d.po_break_down_id and d.pre_cost_fabric_cost_dtls_id = c.id and b.body_part_id=c.body_part_id $all_batch_ids_cond $all_barcode_nos_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by a.id, b.barcode_no,c.gsm_weight,d.dia_width");

		$req_dia_gsm=array();
		foreach($batch_data_array as $row)
		{
			$req_dia_gsm[$row[csf("id")]][$row[csf("barcode_no")]]["gsm"]=$row[csf("gsm_weight")];
			$req_dia_gsm[$row[csf("id")]][$row[csf("barcode_no")]]["dia"]=$row[csf("dia_width")];
			
		}
		//var_dump($batch_data);

		//echo $pro_dtls_ids_cond;die;
		if(!empty($color_Ids)){
			$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 $color_Ids_cond", 'id', 'color_name');
		}
		if(!empty($all_prod_ids)){
			$fabric_desc_arr = return_library_array("select a.id, a.item_description from product_details_master a where a.item_category_id=2 $all_prod_ids_cond", "id", "item_description");
		}
		/*$sql_pro_grey_prod_del = sql_select("select a.id, a.sys_number, a.delevery_date,b.product_id,b.order_id,b.barcode_num from pro_grey_prod_delivery_mst a,pro_grey_prod_delivery_dtls b where a.id=b.mst_id  and a.status_active=1 and a.is_deleted=0 $knit_comp_cond $knitting_source_cond $buyer_name_cond $del_location_cond $order_ids_cond order by id");*///


		$sql_pro_grey_prod_del = sql_select("select a.id, a.sys_number, a.delevery_date,b.product_id,b.order_id,b.barcode_num from pro_grey_prod_delivery_mst a,pro_grey_prod_delivery_dtls b, tmp_barcode_no c where a.id=b.mst_id and b.barcode_num=c.barcode_no and c.userid=$userid and a.entry_form=54 and a.status_active=1 and a.is_deleted=0 $knit_comp_cond $knitting_source_cond $buyer_name_cond $del_location_cond order by id");
		//$order_ids_cond

		
		$pro_grey_arr_data=array();
		foreach($sql_pro_grey_prod_del as $row_prod)
		{
			$pro_grey_arr_data[$row_prod[csf("order_id")]][$row_prod[csf("product_id")]][$row_prod[csf("barcode_num")]]["sys_number"]=$row_prod[csf("sys_number")];
			$pro_grey_arr_data[$row_prod[csf("order_id")]][$row_prod[csf("product_id")]][$row_prod[csf("barcode_num")]]["delevery_date"]=$row_prod[csf("delevery_date")];
		}
		

		$composition_arr = array();
		$composition_arr_new = array();
		if(!empty($all_prod_ids)){
			$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
		}
		$data_array = sql_select($sql_deter);
		if (count($data_array) > 0) {
			foreach ($data_array as $row) {
				if (array_key_exists($row[csf('id')], $composition_arr)) {
					$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
					$composition_arr_new[$row[csf('id')]] = $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";

				} else {
					//$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
					$composition_arr[$row[csf('id')]] = $row[csf('construction')];
					$composition_arr_new[$row[csf('id')]] = $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";

				}
			}
		}
		if(!empty($all_prod_ids)){
			$sql_supplier = sql_select("select a.id as product_id,a.supplier_id,a.lot,a.yarn_type,a.yarn_comp_type1st,a.yarn_comp_percent1st,yarn_count_id from product_details_master a where item_category_id=1 $company_name_cond ");
		}
		//$composition_string = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%";
		$supplier_arr_data=array();
		$yarn_composition_arr_data=array();
		foreach($sql_supplier as $row_spl)
		{
			$supplier_arr_data[$row_spl[csf("lot")]][$row_spl[csf("product_id")]]["supplier_id"]=$row_spl[csf("supplier_id")];
			$supplier_arr_data[$row_spl[csf("lot")]][$row_spl[csf("product_id")]]["yarn_type"]=$row_spl[csf("yarn_type")];

			$yarn_composition_arr_data[$row_spl[csf("yarn_count_id")]][$row_spl[csf("product_id")]]["yarn_conmposition"]=$composition[$row_spl[csf('yarn_comp_type1st')]] . " " . $row_spl[csf('yarn_comp_percent1st')] . "%";

			$yarn_composition_arr_data[$row_spl[csf("lot")]][$row_spl[csf("product_id")]]["yarn_conmposition_lot"]=$composition[$row_spl[csf('yarn_comp_type1st')]] . " " . $row_spl[csf('yarn_comp_percent1st')] . "%";
		}
		//print_r($yarn_composition_arr_data);die;
		

		$style_ref_arr=array();
		
		if(!empty($order_id_arr)){
			$style_ref_query = "select a.id,b.style_ref_no,a.job_no_mst,a.po_number from wo_po_break_down a,wo_po_details_master b, tmp_poid c where a.job_no_mst=b.job_no and a.id=c.poid and c.userid=$user_id  and a.is_deleted=0  and a.status_active=1";//die;
		}
		//and a.status_active=3
		$sql_style_ref=sql_select($style_ref_query);
		foreach($sql_style_ref as $rows)
		{
			$style_ref_arr[$rows[csf("id")]]["style_ref_no"]=$rows[csf("style_ref_no")];
			$job_no_arr[$rows[csf("id")]]["job_no_mst"]=$rows[csf("job_no_mst")];
			$po_number_arr[$rows[csf("id")]]=$rows[csf("po_number")];
		}
		

		if(!empty($all_booking_ids)){ // without program
			$sql_booking_query = "select a.booking_type, a.booking_no, a.is_short, a.buyer_id from wo_booking_mst a where status_active=1 $booking_ids_cond 
			union all select a.booking_type, a.booking_no, a.is_short, a.buyer_id from wo_non_ord_samp_booking_mst a where status_active=1 $booking_ids_cond 
			";//die;
		}

		//echo $sql_booking_query;die;
		$booking_type_result=sql_select($sql_booking_query);
		foreach($booking_type_result as $rows) 
		{
			$booking_type_arr[$rows[csf("booking_no")]]=$rows[csf("booking_type")];
			$booking_is_short_arr[$rows[csf("booking_no")]]=$rows[csf("is_short")];
			$booking_buyer_arr[$rows[csf("booking_no")]]=$rows[csf("buyer_id")];
		}

	
		$sql_machine_query = "select a.barcode_no, b.machine_dia, b.machine_gg from tmp_barcode_no d, pro_roll_details a,pro_grey_prod_entry_dtls b where d.barcode_no=a.barcode_no and d.userid=$user_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.dtls_id = b.id and a.barcode_no<>0 and a.entry_form=2";//die;
		
		//echo $sql_machine_query;die;
		$sql_machine_query_result=sql_select($sql_machine_query);
		foreach($sql_machine_query_result as $rows) 
		{
			$machine_data_arr[$rows[csf("barcode_no")]]=$rows[csf("machine_dia")];
			$gauge_data_arr[$rows[csf("barcode_no")]]=$rows[csf("machine_gg")];
		}

		if(!empty($all_booking_ids)){ // for program
			$prog_booking_id_sql="select b.id, a.booking_no, b.booking_type, b.is_short from ppl_planning_entry_plan_dtls a, wo_booking_mst b 
			where a.booking_no=b.booking_no and a.status_active=1 and b.status_active=1 $booking_ids_cond_2 group by b.id, a.booking_no, b.booking_type, b.is_short";
		}
		$prog_booking_type_result=sql_select($prog_booking_id_sql);
		foreach($prog_booking_type_result as $row)
		{
			$prog_booking_type_arr[$row[csf("booking_no")]]=$row[csf("booking_type")];
			$prog_booking_is_short_arr[$row[csf("booking_no")]]=$row[csf("is_short")];
		}
		// echo "<pre>"; print_r($prog_booking_type_arr);die;

		//$non_order_style_id = return_field_value("b.style_id as style_id", "wo_non_ord_samp_booking_dtls b,wo_non_ord_samp_booking_mst a", "a.booking_no=b.booking_no  $booking_ids_cond","style_id");				

		if(!empty($all_booking_ids)){
			$non_order_style_id = return_library_array("select a.id,b.style_id from wo_non_ord_samp_booking_dtls b,wo_non_ord_samp_booking_mst a where a.status_active=1 and a.booking_no=b.booking_no  $booking_ids_cond", 'id', 'style_id');
		}

		//$booking_no = return_field_value("booking_no as booking_no", " ppl_planning_entry_plan_dtls ", "dtls_id='" . $row[csf("booking_id")] . "' group by booking_no", "booking_no");
		if(!empty($all_booking_ids)){
			$booking_no_disp_arr = return_library_array("select a.dtls_id,a.booking_no from ppl_planning_entry_plan_dtls a where a.status_active=1  $booking_ids_cond_2", 'dtls_id', 'booking_no');
		}

		// echo "<pre>"; print_r($booking_is_short_arr);die;
		//a.recv_number='D n C-GPE-17-00549' and 
		

		$sql_qc_dtls="SELECT d.id as ID, d.pro_dtls_id as PRO_DTLS_ID, d.roll_maintain as ROLL_MAINTAIN, d.barcode_no as BARCODE_NO, d.roll_id as ROLL_ID, d.roll_no as ROLL_NO, d.qc_name as QC_NAME, d.roll_status as ROLL_STATUS, d.roll_width as ROLL_WIDTH, d.roll_weight as ROLL_WEIGHT, d.roll_length as ROLL_LENGTH, d.reject_qnty as REJECT_QNTY, d.qc_date as QC_DATE, d.total_penalty_point as TOTAL_PENALTY_POINT, d.total_point as TOTAL_POINT,d.fabric_grade as FABRIC_GRADE, d.comments as COMMENTS,e.defect_name as DEFECT_NAME,e.defect_count as DEFECT_COUNT,e.found_in_inch as FOUND_IN_INCH,e.penalty_point as PENALTY_POINT,d.insert_date as INSERT_DATE
		from tmp_recv_dtls a, pro_qc_result_mst d, pro_qc_result_dtls e  
		where a.dtls_id=d.pro_dtls_id and d.id=e.mst_id and a.userid=$user_id and d.status_active=1 and d.is_deleted=0 and  e.status_active=1 and e.is_deleted=0  order by d.barcode_no";
		
		//echo $sql_qc_dtls;
		$sql_qc_dtls_data=sql_select($sql_qc_dtls);
		
		
		// echo $sql_qc_dtls;die; 
		//print_r($sql_qc_dtls_data);die("with jj");
		$sql_qc_data_arr=array(); 
		foreach($sql_qc_dtls_data as $dataRow)
		{
			//&& ($dataRow["DEFECT_NAME"] ==60 || $dataRow["DEFECT_NAME"] == 145)
			
			if ($cbo_with_defect==1) // Yes
			{
				$all_defect = array('1' => '1', '5' => '5', '10' => '10', '15' => '15', '20' => '20', '25' => '25', '30' => '30', '35' => '35', '40' => '40', '45' => '45', '50' => '50', '55' => '55', '60' => '60', '65' => '65', '70' => '70', '75' => '75', '95' => '95', '100' => '100', '105' => '105', '110' => '110', '115' => '115', '120' => '120', '125' => '125', '130' => '130', '135' => '135', '140' => '140', '145' => '145', '150' => '150', '155' => '155', '160' => '160', '165' => '165', '170' => '170', '175' => '175', '180' => '180', '185' => '185', '190' => '190', '195' => '195', '200' => '200', '205' => '205', '210' => '210', '215' => '215', '220' => '220', '225' => '225', '230' => '230', '235' => '235', '240' => '240', '245' => '245', '250' => '250', '255' => '255', '260' => '260', '261' => '261', '262' => '262', '263' => '263', '264' => '264', '265' => '265', '266' => '266', '267' => '267', '268' => '268', '269' => '269', '270' => '270', '271' => '271', '272' => '272', '273' => '273', '274' => '274', '275' => '275', '276' => '276', '277' => '277', '278' => '278', '279' => '279');
			}
			else
			{
				if($defect_id_value_arr[$dataRow["DEFECT_NAME"]] )
				{
					$all_defect[$dataRow["DEFECT_NAME"]]=$dataRow["DEFECT_NAME"];
				}
			}
			

			if($dataRow["DEFECT_NAME"] == 1)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["hole_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 5)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["dye_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 10)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["insect_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 15)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["yellowSpot_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 20)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["poly_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 25)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["dust_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 30)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["oilspot_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 35)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["flyconta_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 40)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["slub_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 45)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["patta_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 50)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["cut_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 55)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["sinker_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 60)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["print_mis_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 65)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["yarn_conta_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 70)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["slub_hole_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 75)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["softener_Spot_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 95)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["dirty_stain_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 100)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["neps_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 105)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["needle_drop_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 110)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["chem_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 115)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["cotton_seeds_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 120)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["Loop_hole_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 125)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["dead_cotton_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 130)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["thick_thin_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 135)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["rust_spot_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 140)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["needle_broken_mark_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 145)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["dirty_spot_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 150)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["side_center_shade_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 155)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["bowing_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 160)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["uneven_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 165)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["yellow_writing_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 170)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["fabric_missing_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 175)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["dia_mark_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 180)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["miss_print_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 185)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["hairy_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 190)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["gsm_hole_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 195)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["compacting_mark_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 200)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["rib_body_shade_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 205)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["running_shade_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 210)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["plastic_conta_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 215)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["crease_mark_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 220)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["patches_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 225)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["mc_toppage_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 230)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["needle_line_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 235)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["crample_mark_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 240)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["shite_specks_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 245)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["mellange_effect_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 250)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["line_mark_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 255)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["loop_out_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 260)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["needle_broken_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 261)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["loop_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 262)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["oil_spot_line_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 263)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["lycra_out_drop_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 264)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["miss_yarn_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 265)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["color_contra_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 266)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["friction_mark_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 267)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["pin_out_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 268)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["rust_stain_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 269)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["stop_mark_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 270)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["compacting_broken_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 271)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["grease_spot_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 272)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["cut_hole_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 273)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["snagging_pull_out_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 274)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["press_off_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 275)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["wheel_free_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 276)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["count_mix_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 277)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["black_spot_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 278)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["set_up_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}
			else if($dataRow["DEFECT_NAME"] == 279)
			{
				$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["pin_ole_defect_count"]+=$dataRow["DEFECT_COUNT"];
			}


			$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["comments"]=$dataRow["COMMENTS"];
			$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["reject_qnty"]=$dataRow["REJECT_QNTY"];
			$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["roll_weight"]=$dataRow["ROLL_WEIGHT"];
			$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["roll_status"]=$dataRow["ROLL_STATUS"];
			$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["insert_date"]=$dataRow["INSERT_DATE"];
			$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["qc_name"]=$dataRow["QC_NAME"];
			$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["roll_width"]=$dataRow["ROLL_WIDTH"];
			$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["pro_dtls_id"]=$dataRow["PRO_DTLS_ID"];
			

			$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["fabric_grade"]=$dataRow["FABRIC_GRADE"];
			$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["roll_length"]=$dataRow["ROLL_LENGTH"];
			$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["total_point"]=$dataRow["TOTAL_POINT"];
			
			$sql_qc_data_arr[$dataRow["PRO_DTLS_ID"]][$dataRow["BARCODE_NO"]]["tr_color"]=$dataRow["BARCODE_NO"];

			$pro_dtls_id_arr[$dataRow["PRO_DTLS_ID"]]=$dataRow["PRO_DTLS_ID"];
		}
		
		unset($sql_qc_dtls_data);


		$pro_dtls_ids= array_chunk($pro_dtls_id_arr, 999);

		$pro_dtls_ids_cond=" and(";
		foreach($pro_dtls_ids as $pro_dtls_ids)
		{
			if($pro_dtls_ids_cond==" and(") $pro_dtls_ids_cond.=" id in(". implode(',', $pro_dtls_ids).")"; else $pro_dtls_ids_cond.="  or id in(". implode(',', $pro_dtls_ids).")";
		}
		$pro_dtls_ids_cond.=")";
	
		$gsm_data_array = sql_select("SELECT id as ID, gsm as GSM FROM pro_finish_fabric_rcv_dtls WHERE status_active=1 and is_deleted=0 $pro_dtls_ids_cond");	
		$act_gsm=array();
		foreach ($gsm_data_array as $row) 
		{
			$act_gsm[$row["ID"]]["gsm"]=$row["GSM"];
		}
		//var_dump($act_gsm);


		asort($all_defect);

		$r_id=execute_query("delete from tmp_recv_dtls where userid=$user_id");
        if($r_id) $flag=1; else $flag=0;
        $r_id2=execute_query("delete from tmp_barcode_no where userid=$user_id");
        if($r_id2) $flag=1; else $flag=0;
        $r_id3=execute_query("delete from tmp_poid where userid=$user_id");
        if($r_id3) $flag=1; else $flag=0;
        if($flag==1)
        {
            oci_commit($con);
        }else{
        	echo "Failed to clear temporary table";
        	die;
        }
        disconnect($con);

        //echo count($all_defect);die;
  		?>
		<fieldset style="width:<? 3970 + count($all_defect)*80 + 38?>px;">
			<table width="1200">
				<tr>
					<td align="center" width="100%" colspan="12" class="form_caption" style="font-size:18px;">Daily Roll wise Finishing QC Report</td>
				</tr>
				<tr>
					<td align="center" width="100%" colspan="12" class="form_caption"><? echo $company_library[str_replace("'","",$cbo_company_name)]; ?></td>
				</tr>
				<tr>
				   <td align="center" width="100%" colspan="12" class="form_caption" style="font-size:12px;"><? echo   show_company($cbo_knitting_company,'',''); ?></td>
				</tr>
			</table>
			<div style="width:<? 3970 + count($all_defect)*80 + 18?>px;">
				<table width="<? echo 3970 + count($all_defect)*80; ?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" style="" align="left">
					<thead>
						<tr height="100">
							<th width="40">SL</th>
							<th width="110">Source</th>
							<th width="150">Company</th>
							<th width="100">System Id / Production Id</th>
							<th width="100">Production Date</th>
							<th width="100">Batch Id</th>
							<th width="100">Barcode</th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">MC NO#</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">Roll No</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">SHIFT</div></th>
							<th width="130">Scan Time</th>
							<th width="100">Batch No</th>
							<th width="100">BUYER</th>
							<th width="100">Job No</th>
							<th width="200">ORDER NO</th>
							<th width="100">Style Ref.</th>
							<th width="120">Booking Type</th>
							<th width="100">Booking No</th>
							<th width="60" style="vertical-align:middle"><div class="block_div" >ROLL WEIGHT</div></th>
							<th width="100">Fabric Color</th>
							<th width="100">M/C DIA</th>
							<th width="100">REQ. Finish Dia</th>
							<th width="100">Actual. Finish Dia</th>
							<th width="100">Dia Variance</th>
							<th width="100">GAUGE</th>
							<th width="100">FABRIC TYPE</th>
							<th width="100">REQ.GSM</th>
							<th width="100">Actual GSM</th>
							<th width="100">GSM Variance</th>
							<th width="100">Batch Qty</th>
							<th width="70">Qc Pass Qty</th>
							<th width="130">Delivery Challan No</th>
							<th width="100">Delivary Date</th>

							<? 
								foreach ($all_defect as $defId => $value) {
									?>
									<th width="80" style="vertical-align:middle"><div class="rotate_90_deg"><? echo $finish_qc_defect_array[$defId]; ?></div></th>
									<?
								}
							?>

							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">TTL POINTS</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">Reject Qty</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">GRADE</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">DEFECT %</div></th>
							<th width="60" style="vertical-align:middle"><div class="rotate_90_deg">LENGTH YDS</div></th>
							
							<th width="100">Operator Name</th>
							<th width="100">Defective Length</th>
							<th width="" style="vertical-align:middle">REMARKS</th>
													
						</tr>
					</thead>
				</table>
			  	<style> 
					.breakAll{
						word-break:break-all;
						word-wrap: break-word;
					}
				</style>
				<div style="width:<? 3970 + count($all_defect)*80 + 18?>px; float:left; max-height:400px; overflow-y:scroll" id="scroll_body">
					<table width="<? echo 3970 + count($all_defect)*80;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body" style="float:left;">
						<tbody>
							<?
							
							
							$i=1;
							
							foreach($sql_finish_prod_entry as $row)
							{
								//echo $bgcolor;die("sumon");	break;
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								if ($row['FEBRIC_DASCRIPTION_ID'] == 0 || $row['FABRIC_DESCRIPTION_ID'] == "")
								{
									$fabric_desc = $fabric_desc_arr[$row['PROD_ID']];
									//$fabric_desc = $row['PROD_ID'];
								}
								else
								{
									$fabric_desc = $composition_arr[$row['FABRIC_DESCRIPTION_ID']];
								}
								$booking_type_string='';$is_short=0;$booking_type_id=0;
								if ($row["RECEIVE_BASIS"] == 1 && $row["BOOKING_WITHOUT_ORDER"] != 1) 
								{
									$booking_no = $row["BOOKING_NO"] ;
									$booking_type_id=$booking_type_arr[$booking_no];
									if($booking_type_id==1)
									{
										$is_short=$booking_is_short_arr[$booking_no];
										if($is_short==1)  $booking_type_string="Short Fabric";
										else if($is_short==2)  $booking_type_string="Main Fabric";
									}
									
									else if($booking_type_id==4) $booking_type_string="Sample Booking";
									//echo "set_auto_complete(2);\n";
								}
								else if ($row["RECEIVE_BASIS"] == 1 && $row["BOOKING_WITHOUT_ORDER"] == 1) 
								{
									$booking_type_string="Sample Without Order";
									$booking_no = $row["BOOKING_NO"] ;
								}
								else if ($row["RECEIVE_BASIS"] == 2) 
								{
									//$booking_no = return_field_value("booking_no as booking_no", " ppl_planning_entry_plan_dtls ", "dtls_id='" . $row[csf("booking_id")] . "' group by booking_no", "booking_no");

									$booking_no=$booking_no_disp_arr[$row["BOOKING_ID"]];
									$booking_type_id=$prog_booking_type_arr[$booking_no];
									if($booking_type_id==1)
									{
										$is_short=$prog_booking_is_short_arr[$booking_no];
										if($is_short==1)  $booking_type_string="Short Fabric";
										else if($is_short==2)  $booking_type_string="Main Fabric";
									}
									else if($booking_type_id==4) $booking_type_string="Sample Booking";
									else $booking_type_string="Sample Without Order";
									
									//echo "set_auto_complete(2);\n";
								} 
								else {
									//echo "set_auto_complete(1);\n";
									$booking_no = '';
								}
				
								if($sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["tr_color"]!="")
								{
									$bgcolor='#3CB371';
								}
									$lot_arr = array_unique(explode(",", $row["YARN_LOT"]));
									$yarn_prod_arr = explode(',',$row["YARN_PROD_ID"]);
								?>
								
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>');" id="tr_2nd<? echo $i; ?>">
									<td width="40"><? echo $i; ?></td>
									<td width="110" align="center"><p><? echo $knitting_source[$row["KNITTING_SOURCE"]]; ?></p></td> 
									<td width="150" align="center" ><p><? 
									if($row["KNITTING_SOURCE"]==1){echo $company_library[$row["KNITTING_COMPANY"]]; }else{ echo $knitting_company_library[$row["KNITTING_COMPANY"]]; }
									?></p></td>
									<td width="100"><p><? echo $row["RECV_NUMBER"]; ?></p></td>
									<td width="100" align="center"><p><? echo change_date_format($row["RECEIVE_DATE"]); ?></p></td>
									<td width="100" align="center"><p><? echo $row["BATCH_ID"];?></p></td>
									<td width="100" align="center"><p><? echo $row["BARCODE_NO"]//$row[csf("challan_no")]; ?></p></td>
									<td width="60" align="center"><p><? echo $machine_arr[$row["MACHINE_NO_ID"]]; ?></p></td>
									<td width="60" align="center"><p><? echo $row["ROLL_NO"]; ?></p></td>
									<td width="60" align="center"><p><? echo $shift_name[$row["SHIFT_NAME"]]; ?></p></td>
									<td width="130" align="center" class="breakAll"><p><? echo $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["insert_date"]; ?></p></td>
									<td width="100" align="center" class="breakAll"><p><? echo $row["BATCH_NO"]; ?></p></td>
									<td width="100" align="center" class="breakAll"><p><? echo  $buyer_library[$booking_buyer_arr[$row["BOOKING_NO"]]];?></p></td>
									<?php 
									if($row[csf("BOOKING_WITHOUT_ORDER")]==1)
									{
										?>
										
										<td width="100" align="center" class="breakAll"><? //echo $style_library[$non_order_style_id[$row[csf("booking_id")]]]; ?></td>
										<?php
									}
									else
									{
										?>
										<td width="100" align="center" ><p><? echo $job_no_arr[$row["ORDER_ID"]]["job_no_mst"]; ?></p></td>
										<td width="200" align="center" class="breakAll"></p><? echo $po_number_arr[$row["ORDER_ID"]]; ?><p></td>
										<td width="100" align="center" class="breakAll"><p><? echo $style_ref_arr[$row["ORDER_ID"]]["style_ref_no"]; ?></p></td>
										<?php 
									}
									?>
									
									<td width="120" align="center" title="receive_basis: <? echo $row["RECEIVE_BASIS"]; ?>"><p>
									<? echo $booking_type[$booking_type_arr[$row["BOOKING_NO"]]];
									//echo $booking_type_string; ?></p>
									</td>
									<td width="100" align="center"><p><? //echo $row["BOOKING_NO"]; ?></p></td>
									<td width="60" align="center"><p><? echo number_format($row["ROLL_WEIGHT"],2) ; ?></p></td>
									
									<td width="100" align="center" class="breakAll"><p><? 
									$colorName="";
									$colorID_arr = array_unique(explode(",", $row["COLOR_ID"]));
									foreach ($colorID_arr as $colID) 
									{
										$colorName .= $color_arr[$colID] . ",";
									}
									echo chop($colorName,",");
									//echo $row[csf("color_id")];//echo $color_arr[$row[csf("color_id")]]; 
									
									
									?></p></td>
									<td width="100" align="center" ><p><? echo $machine_data_arr[$row["BARCODE_NO"]]; ?></p></td>
									<td width="100" align="center" ><p><? echo $total_req_dia = $req_dia_gsm[$row["BATCH_ID"]][$row["BARCODE_NO"]]["dia"]; ?></p></td>
									<td width="100" align="center" ><p><? echo $total_act_dia=$sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["roll_width"]; ?></p></td>
									<td width="100" align="center" ><p><? echo ($total_req_dia-$total_act_dia); ?></p></td>
									<td width="100" align="center" ><p><? echo $gauge_data_arr[$row["BARCODE_NO"]]; ?></p></td>
									<td width="100" align="center" class="breakAll"><p><? echo $fabric_desc; ?></p></td>
									<td width="100" align="center" ><p><? echo $total_req_gsm=$req_dia_gsm[$row["BATCH_ID"]][$row["BARCODE_NO"]]["gsm"]; ?></p></td>
								
									<td width="100" align="center" ><p><? echo $total_act_gsm=$act_gsm[$sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["pro_dtls_id"]]["gsm"]; ?></p></td>
									<td width="100" align="center" ><p><? echo ($total_req_gsm-$total_act_gsm); ?></p></td>
									<td width="100" align="right" ><p><? echo $row["BATCH_QNTY"]; ?></p></td>

									<td width="70" align="right" class="breakAll"><p>
									<? 
									if($sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["roll_status"] != 3){

										echo $qc_pass_qnty =  $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["roll_weight"]-$sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["reject_qnty"];

										
									}
									 ?></p>
									</td>

									<td width="130" align="center"><p><? echo $pro_grey_arr_data[$row["ORDER_ID"]][$row["PROD_ID"]][$row["BARCODE_NO"]]["sys_number"]; ?></p></td>
									<td width="100" align="center" ><p><? echo change_date_format($pro_grey_arr_data[$row["ORDER_ID"]][$row["PROD_ID"]][$row["BARCODE_NO"]]["delevery_date"]); ?></p></td>
									
									<? 
										foreach ($all_defect as $defId => $defVal) 
										{

											?>
											<td width="80" align="right" title="<? echo $row["PRO_DTLS_ID"].'='.$row["BARCODE_NO"].'='.$defId.'='.$defect_id_value_arr[$defId];?>"><p><? echo $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]][$defect_id_value_arr[$defId]]; ?></p></td>
											<?
										}
									?>



									<td width="60" align="right" ><p><? echo number_format($sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["total_point"],2); ?></p></td>
									<td width="60" align="right" ><p><? echo $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["reject_qnty"]; ?></p></td>
									<td width="60" align="center" ><p><? echo $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["fabric_grade"]; ?></p></td>
									<td width="60" align="center" ><p><? $defect_percent=($sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["total_point"]*36*100)/($row["WIDTH"]*$sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["roll_length"]); echo number_format($defect_percent,2).'%'; ?></p></td>
									<td width="60" align="center" class="breakAll"><p><? echo number_format($sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["roll_length"],2); ?></p></td>
									
								
									
									<td width="100" align="center" ><p><? echo $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["qc_name"]; ?></p></td>
									<td width="100" align="center" ><p><? echo number_format($defect_percent*$sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["roll_length"],2); ?></p></td>
									<td width="" class="breakAll"><p><? echo $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["comments"]; ?></p></td>
															
									
								</tr>
								<?
								
								$total_batch_qnty+=$row["BATCH_QNTY"];
								$total_qc_pass_qty+=$qc_pass_qnty;
								
								/*
									$total_hole_defect+=  $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["hole_defect_count"];
									$total_dye_defect_count+=  $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["dye_defect_count"];
									$total_insect_defect_count+=  $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["insect_defect_count"];
									$total_yellowSpot_defect_count+=  $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["yellowSpot_defect_count"];
									$total_poly_defect_count+=  $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["poly_defect_count"];
									$total_dust_defect_count+=  $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["dust_defect_count"];
									$total_oilspot_defect_count+=  $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["oilspot_defect_count"];
									$total_flyconta_defect_count+=  $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["flyconta_defect_count"];
									$total_slub_defect_count+=  $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["slub_defect_count"];
									$total_patta_defect_count+=  $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["patta_defect_count"];
									$total_cut_defect_count+=  $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["cut_defect_count"];
									$total_sinker_defect_count+=  $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["sinker_defect_count"];
									$total_print_mis_defect_count+=  $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["print_mis_defect_count"];
									$total_yarn_conta_defect_count+=  $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["yarn_conta_defect_count"];
									$total_slub_hole_defect_count+=  $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["slub_hole_defect_count"];
									$total_softener_Spot_defect_count+=  $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["softener_Spot_defect_count"];
									$total_dirty_stain_defect_count+=  $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["dirty_stain_defect_count"];
									$total_neps_defect_count+=  $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["neps_defect_count"];
									$total_needle_drop_defect_count+=  $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["needle_drop_defect_count"];
									$total_chem_defect_count+=  $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["chem_defect_count"];
									$total_cotton_seeds_defect_count+=  $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["cotton_seeds_defect_count"];
									$total_Loop_hole_defect_count+=  $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["Loop_hole_defect_count"];
									$total_dead_cotton_defect_count+=  $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["dead_cotton_defect_count"];
									$total_thick_thin_defect_count+=  $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["thick_thin_defect_count"];
									$total_rust_spot_defect_count+= $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["rust_spot_defect_count"];
									$total_needle_broken_mark_defect_count+= $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["needle_broken_mark_defect_count"];
									$total_dirty_spot_defect_count+= $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["dirty_spot_defect_count"];
									$total_side_center_shade_defect_count+= $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["side_center_shade_defect_count"];
									$total_bowing_defect_count+= $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["bowing_defect_count"];
									$total_uneven_defect_count+= $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["uneven_defect_count"];
									$total_yellow_writing_defect_count+= $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["yellow_writing_defect_count"];
									$total_fabric_missing_defect_count+= $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["fabric_missing_defect_count"];
									$total_dia_mark_defect_count+= $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["dia_mark_defect_count"];
									$total_miss_print_defect_count+= $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["miss_print_defect_count"];
									$total_hairy_defect_count+= $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["hairy_defect_count"];
									$total_gsm_hole_defect_count+= $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["gsm_hole_defect_count"];
									$total_compacting_mark_defect_count+= $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["compacting_mark_defect_count"];
									$total_rib_body_shade_defect_count+= $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["rib_body_shade_defect_count"];
									$total_running_shade_defect_count+=  $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["running_shade_defect_count"];
									$total_plastic_conta_defect_count+=  $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["plastic_conta_defect_count"];
									$total_crease_mark_defect_count+=  $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["crease_mark_defect_count"];
									$total_patches_defect_count+=  $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["patches_defect_count"];
									$total_mc_toppage_defect_count+=  $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["mc_toppage_defect_count"];
									$total_needle_line_defect_count+=  $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["needle_line_defect_count"];

									$total_crample_mark_defect_count+=  $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["crample_mark_defect_count"];
									$total_shite_specks_defect_count+=  $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["shite_specks_defect_count"];
									$total_mellange_effect_defect_count+=  $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["mellange_effect_defect_count"];
									$total_line_mark_defect_count+=  $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["line_mark_defect_count"];
									$total_loop_out_defect_count+=  $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["loop_out_defect_count"];

									$total_needle_broken_defect_count+=  $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["needle_broken_defect_count"];
									$total_loop_defect_count+= $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["loop_defect_count"];
									$total_oil_spot_line_defect_count+= $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["oil_spot_line_defect_count"];
									$total_lycra_out_drop_defect_count+= $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["lycra_out_drop_defect_count"];

									$total_miss_yarn_defect_count+= $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["miss_yarn_defect_count"];
									$total_color_contra_defect_count+= $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["color_contra_defect_count"];
									$total_friction_mark_defect_count+= $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["friction_mark_defect_count"];
									$total_pin_out_defect_count+= $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["pin_out_defect_count"];
									$total_rust_stain_defect_count+=  $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["rust_stain_defect_count"]; 
									$total_stop_mark_defect_count+=  $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["stop_mark_defect_count"]; 
									$total_compacting_broken_defect_count+=  $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["compacting_broken_defect_count"]; 
									$total_grease_spot_defect_count+=  $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["grease_spot_defect_count"];
									$total_cut_hole_defect_count+=  $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["cut_hole_defect_count"];
									$total_snagging_pull_out_defect_count+=  $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["snagging_pull_out_defect_count"];
									$total_press_off_defect_count+=  $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["press_off_defect_count"];
									$total_wheel_free_defect_count+=  $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["wheel_free_defect_count"];
									$total_count_mix_defect_count+=  $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["count_mix_defect_count"];
									$total_black_spot_defect_count+=  $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["black_spot_defect_count"];
									$total_set_up_defect_count+=  $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["set_up_defect_count"];
									$total_pin_ole_defect_count+=  $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["pin_ole_defect_count"];

								*/
								

								$total_totalDefect_point+=		$sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["total_point"];
								$total_reject_qty+=				$sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]]["reject_qnty"];
								$total_taka+=					$row["QC_PASS_QNTY"]*$row["RATE"];
								$i++;


								foreach ($all_defect as $defId => $defVal) 
								{
									$tot_defect_id_value_arr[$defId] += $sql_qc_data_arr[$row["PRO_DTLS_ID"]][$row["BARCODE_NO"]][$defect_id_value_arr[$defId]];;
								}
							}
							?>
						</tbody>
					</table>
				</div>

				<!-- <table width="7785" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body" style="float:left;"> -->
				<table class="rpt_table" width="<? echo 3970 + count($all_defect)*80;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
					<tfoot>
						<tr style="background-color:#CCCCCC;">
							<th width="40"></th>
							<th width="110"></th>
							<th width="150"></th>
							<th width="100"></th>
							<th width="100"> </th>
							<th width="100"> </th>
							<th width="100"></th>
							<th width="60" ></th>
							<th width="60" ></th>
							<th width="60" ></th>
							<th width="130"> </th>
							<th width="100"> </th>
							<th width="100"></th>
							<th width="100"> </th>
							<th width="200"> </th>
							<th width="100"> </th>
							<th width="120"> </th>
							<th width="100"> </th>
							<th width="60" ></th>
							<th width="100"> </th>
							<th width="100"></th>
							<th width="100"> </th>
							<th width="100"> </th>
							<th width="100"> </th>
							<th width="100"> </th>
							<th width="100"> </th>
							<th width="100"></th>
							<th width="100"> </th>
							<th width="100"><strong>Total :</strong> </th>
							<th width="100" align="right" id="total_batch_qty"><strong><? echo $total_batch_qnty; ?></strong> </th>
							<th width="70" align="right" id="total_qc_pass_qty" class="breakAll"><strong><? echo $total_qc_pass_qty; ?></strong></th>
							<th width="130" ><strong></strong></th>
							<th width="100"> <strong></strong></th>

							<? 
								foreach ($all_defect as $defId => $defVal) 
								{

									?>
									<th width="80" align="right" id="total_<? echo $defect_id_value_arr[$defId];?>"><strong><? echo $tot_defect_id_value_arr[$defId] ; ?></strong></th>
									<?
									$filter_str .= 'total_'.$defect_id_value_arr[$defId].',';
								}
							?>

							<th width="60" align="right" id="total_totalDefect_point"><strong><? echo number_format($total_totalDefect_point,2); ?></strong></th>
							<th width="60" align="right" id="total_reject_qty"><strong><? echo $total_reject_qty; ?></strong></th>
							<th width="60" align="right" id=""><strong></strong></th>
							<th width="60" align="right" id=""><strong></strong></th>
							<th width="60" align="right" id=""><strong></strong></th>
							<th width="100" align="right" id=""><strong></strong></th>
							<th width="100" align="right" id=""><strong></strong></th>
							<th width="" align="right" id=""><strong></strong></th>

							
						</tr>
					</tfoot>
				</table>
			</div>
		</fieldset>
		<?
		$filter_str = chop($filter_str,",");

		echo "Execution Time: " . (microtime(true) - $started) . " S ";
	}
	
	
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename####$reportType####$filter_str";
	exit();
}

if($action=="booking_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	
	<script>
	function js_set_value(str)
	{
		$("#hide_booking_no").val(str);
		parent.emailwindow.hide(); 
	}
	</script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:980px;">
            <table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th width="100">Booking No</th>
                    <th width="80">Style Desc.</th>
                    <th width="200">Date Range</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 				
                    <input type="hidden" name="hide_booking_no" id="hide_booking_no" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company_id $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>                 
                        <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:100px"></td>
                        <td><input name="txt_style_desc" id="txt_style_desc" class="text_boxes" style="width:80px"></td>
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
                          <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
                         </td> 	
                         <td align="center">
                 			<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company_id; ?>'+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('txt_style_desc').value, 'create_booking_search_list_view', 'search_div', 'daily_roll_wise_finishing_qc_report_controller','setFilterGrid(\'table_body_booking\',1)')" style="width:100px;" />              
                        </td>
                    </tr>
                    <tr>
                        <td  align="center" height="40" valign="middle" colspan="6">
                        <? 
                        echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );		
                        ?>
                        <? echo load_month_buttons();  ?>
                        </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}

if ($action=="create_booking_search_list_view")
{
	$data=explode('_',$data);
	$style_desc=$data[7];
	if ($data[0]!=0) $company="  a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	
	if ($data[1]!=0){$buyer=" and a.buyer_id='$data[1]'";}
	else{$buyer="";}
	
	if($db_type==0)
	 {
		  // $booking_year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[4]";
		  $booking_year_cond=" and YEAR(a.insert_date)=$data[4]";
		  if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' 
		  and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date =""; 
     }
	if($db_type==2)
	 {
		  $booking_year_cond=" and to_char(a.insert_date,'YYYY')=$data[4]";
		  if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."'
		  and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	 }
	if($data[6]==4 || $data[6]==0)
	{
		if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[5]%'  $booking_year_cond  "; else $booking_cond="";
		if (str_replace("'","",$data[7])!="") $style_des_cond=" and b.style_des like '%$data[7]%' "; else $style_des_cond="";
	}
 
	
	

	/*$po_array=array();
	$sql_po= sql_select("select a.booking_no_prefix_num, a.booking_no,a.po_break_down_id from wo_non_ord_samp_booking_mst a  where $company $buyer $booking_date and booking_type=4  and   status_active=1  and 	is_deleted=0 order by booking_no");
	foreach($sql_po as $row)
	{
		$po_id=explode(",",$row[csf("po_break_down_id")]);
		$po_number_string="";
		foreach($po_id as $key=> $value )
		{
			$po_number_string.=$po_number[$value].",";
		}
		$po_array[$row[csf("po_break_down_id")]]=rtrim($po_number_string,",");
	}*/
	$style_library=return_library_array( "select id,style_ref_no from sample_development_mst", "id", "style_ref_no"  );
    $approved=array(0=>"No",1=>"Yes");
    $is_ready=array(0=>"No",1=>"Yes",2=>"No"); 
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$arr=array (2=>$comp,3=>$buyer_arr,4=>$item_category,5=>$fabric_source,6=>$suplier,7=>$style_library,9=>$approved,10=>$is_ready);
	 $sql= "select a.booking_no_prefix_num, a.booking_no,a.booking_date,a.company_id,a.buyer_id,a.item_category,a.fabric_source,a.supplier_id,a.is_approved,a.ready_to_approved,a.pay_mode,b.style_id,b.style_des from wo_non_ord_samp_booking_mst  a left join wo_non_ord_samp_booking_dtls b on a.booking_no=b.booking_no  and b.status_active=1 and b.is_deleted=0  where   $company". set_user_lavel_filtering(' and a.buyer_id','buyer_id')." $buyer $booking_date $booking_cond $style_des_cond and a.booking_type=4 and  a.status_active=1 and a.is_deleted=0 group by a.booking_no_prefix_num, a.booking_no,a.booking_date,a.company_id,a.buyer_id,a.item_category,a.fabric_source,a.supplier_id,a.is_approved,a.ready_to_approved,a.pay_mode,b.style_id,b.style_des order by booking_no"; 
	//echo $sql;
	//echo create_list_view("list_view", "Booking No,Booking Date,Company,Buyer,Fabric Nature,Fabric Source,Supplier,Style,Style Desc.,Approved,Is-Ready", "100,80,100,100,80,80,80,50,80,50","950","320",0, $sql , "js_set_value", "booking_no", "", 1, "0,0,company_id,buyer_id,item_category,fabric_source,supplier_id,style_id,0,is_approved,ready_to_approved", $arr , "booking_no_prefix_num,booking_date,company_id,buyer_id,item_category,fabric_source,supplier_id,style_id,style_des,is_approved,ready_to_approved", '','','0,3,0,0,0,0,0,0,0,0,0,0','','');
	?>
   <table class="rpt_table scroll" width="970" cellpadding="0" cellspacing="0" border="1" rules="all" id="">
       <thead>
            <th width="40">Sl</th> 
            <th width="80">Booking No</th>  
            <th width="80">Booking Date</th>           	 
            <th width="100">Buyer</th>
            <th width="120">Fabric Nature</th>
            <th width="80">Fabric Source</th>
            <th width="80">Pay Mode</th>
            <th width="100">Supplier</th>
            <th width="80">Style</th>
            <th width="200">Style Desc.</th>
        </thead>
     </table>
		<div style="width:970px; max-height:280px; overflow-y:scroll" id="list_container_batch" align="left">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="952" class="rpt_table" id="table_body_booking">
                <tbody>
                    <? 
                    $i=1;
                    $sql_data=sql_select($sql);
                    foreach($sql_data as $row){
                        if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";    
                    ?>
                    <tr bgcolor="<? echo $bgcolor;?>" onClick="js_set_value('<? echo $row[csf('booking_no')]  ?>')" style="cursor:pointer">
                        <td width="40"><? echo $i;?></td> 
                        <td width="80"><? echo $row[csf('booking_no_prefix_num')];?></td>  
                        <td width="80"><? echo date("d-m-Y",strtotime($row[csf('booking_date')]));?></td>           	 
                        <td width="100"><? echo $buyer_arr[$row[csf('buyer_id')]];?></td>
                        <td width="120"><? echo $item_category[$row[csf('item_category')]];?></td>
                        <td width="80"><? echo $fabric_source[$row[csf('fabric_source')]];?></td>
                        <td width="80">
                        <? echo $pay_mode[$row[csf('pay_mode')]];?>
                        </td>
                        <td width="100">
                        <? 
                        if($row[csf('pay_mode')]==3 || $row[csf('pay_mode')]==5){
                            echo $comp[$row[csf('supplier_id')]];
                        }
                        else{
                            echo $suplier[$row[csf('supplier_id')]];
                        }
                        ?>
                        </td>
                        <td width="80" style="word-wrap: break-word;word-break: break-all;"><? echo $style_library[$row[csf('style_id')]];?></td>
                        <td width="" style="word-wrap: break-word;word-break: break-all;"><? echo $row[csf('style_des')];?></td>

                    </tr>
                    <?
                    $i++;
                     }
                    ?>
                </tbody>
            </table>
        </div>
    <?
}
if($action=="job_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	
	<script>
	function js_set_value(str)
	{
		$("#hide_job_no").val(str);
		parent.emailwindow.hide(); 
	}
	</script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:580px;">
            <table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Job No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                    <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company_id $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company_id; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year; ?>', 'job_popup_search_list_view', 'search_div', 'daily_roll_wise_finishing_qc_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}
if ($action=="job_popup_search_list_view")
{
  	//echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	list($company_id,$buyer_id,$search_type,$search_value,$cbo_year)=explode('**',$data);

	if($search_type==1 && $search_value!=''){
		$search_con=" and a.job_no like('%$search_value')";	
	}
	else if($search_type==2 && $search_value!=''){
		$search_con=" and a.style_ref_no like('%$search_value%')";	
	}

	if($buyer_id==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_cond="";
		}
		else
		{
			$buyer_cond="";
		}
	}
	else
	{
		$buyer_cond=" and a.buyer_name=$buyer_id";
	}
	
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0)
		{
			$year_cond=" and YEAR(insert_date)=$cbo_year";
			$year_field="YEAR(insert_date)";
		}
		else
		{
			$year_cond=" and to_char(insert_date,'YYYY')=$cbo_year";	
			$year_field="to_char(insert_date,'YYYY')";
		}
	}
	else $year_cond="";

	$arr=array (2=>$company_library,3=>$buyer_arr);
	$sql= "select a.job_no_prefix_num, a.job_no, a.company_name,a.buyer_name,a.style_ref_no,$year_field as year from wo_po_details_master a where a.company_name=$company_id $buyer_cond $year_cond $search_con order by a.id DESC";
	//echo $sql;
	echo  create_list_view("list_view", "Job No,Year,Company,Buyer Name,Style Ref. No", "70,70,120,100,100","570","230",0, $sql , "js_set_value", "year,job_no", "", 1, "0,0,company_name,buyer_name,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no", "","setFilterGrid('list_view',-1)",'0,0,0,0,0');
	echo "<input type='hidden' id='hide_job_no' />";
	
	exit();
}
disconnect($con);
?>
