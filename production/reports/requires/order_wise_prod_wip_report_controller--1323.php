<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
require_once('../../../includes/class4/class.conditions.php');
require_once('../../../includes/class4/class.reports.php');
require_once('../../../includes/class4/class.fabrics.php');
require_once('../../../includes/class4/class.others.php');

$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.id=b.buyer_id and b.tag_company=$data and a.status_active=1 and a.is_deleted=0 order by a.buyer_name","id,buyer_name", 0, "-- Select buyer --", 0, "","" );//load_drop_down( 'requires/daily_knitting_production_report_controller',document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_machine', 'machine_td' );$location_cond
  exit();	 
}

if ($action == "eval_multi_select") {
    echo "set_multiselect('cbo_buyer_name','0','0','','0');\n";
    // echo "setTimeout[($('#floor_td a').attr('onclick','disappear_list(cbo_floor,0);getFloorId();') ,3000)];\n";
    exit();
}

if($db_type==0) $insert_year="SUBSTRING_INDEX(a.insert_date, '-', 1)";
if($db_type==2) $insert_year="extract( year from b.insert_date)";
//item style------------------------------//

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 130, "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id=$data group by id,location_name  order by location_name","id,location_name", 1, "-- Select location --", $selected, "load_drop_down( 'requires/order_wise_prod_wip_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );" );
	exit();
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor_name", 130, "SELECT id,floor_name from lib_prod_floor where location_id=$data and status_active =1 and is_deleted=0 and production_process in(1) group by id,floor_name order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "" );
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
			 
			if( jQuery.inArray( str[0], selected_id_arr ) == -1 ) {
				selected_id_arr.push( str[0] );
				selected_id.push( str[1] );
				selected_name.push( str[2] );
				selected_style.push( str[3] );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				selected_style.splice( i, 1 );
			}
			var id = ''; var name = '';var style = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
				style += selected_style[i] + ',';
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
			<fieldset style="width:760px;">
	            <table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
	            	<thead>
	                    <th class="must_entry_caption">Company Name</th>
	                    <th>Year</th>
	                    <th>Search By</th>
	                    <th id="search_by_td_up" width="100">Job No</th>
	                    <th>Closing Date Range</th>
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
									echo create_drop_down( "cbo_company_name", 130, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 order by company_name","id,company_name", 1, "-- Select Company --", $company_name, "" );
								?>
	                        </td>                
	                        <td align="center">	
	                    	<?						
								echo create_drop_down( "cbo_year", 60, $year,"",1, "--Select--", date('Y'),'',0 );
							?>
	                        </td>                 
	                        <td align="center">	
	                    	<?
	                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref");
								$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
								echo create_drop_down( "cbo_search_by", 80, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
	                        </td>     
	                        <td align="center" id="search_by_td">				
	                            <input type="text" style="width:80px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
	                        </td> 
	                        <td>	                        	
	                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date">&nbsp;
	                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px"  placeholder="To Date">
	                        </td>
	                        <td align="center">
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('cbo_year').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**<?=$buyer_name;?>'+'**<?=$type_id;?>', 'search_list_view', 'search_div', 'order_wise_prod_wip_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:80px;" />
	                    	</td>
	                    </tr>
	                    <tr>
	                    	<td colspan="6"><? echo load_month_buttons(1); ?></td>
	                    </tr>
	            	</tbody>
	           	</table>
	            <div style="margin-top:15px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
	</body>        
	<script type="text/javascript">
		$("#cbo_company_name").attr('disabled',true);		
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
	$type_id=$data[7];
	
	if($data[6]=="")
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
		$buyer_id_cond=" and a.buyer_name in($data[6])";
	}
	//echo $type_id.'DT';
	$search_by=$data[1];
	if(str_replace("'", "", $data[2])!="")
	{
		$search_string="".trim($data[2])."";
	}

	if($search_by==1) 
		$search_field="a.job_no_prefix_num"; 
	else if($search_by==2) 
		$search_field="a.style_ref_no";
	$search_cond="";
	if($search_string!="")	{$search_cond=" and $search_field='$search_string'";}
	$job_year =$data[3];
	
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

    if(str_replace("'", "", $data[4]) !="" && str_replace("'", "", $data[5]) !="")
    {   
    	// ===========================
    	$date_from = date('d-M-Y',strtotime($data[4]));
    	$date_to = date('d-M-Y',strtotime($data[5]));
    	$date_cond = " and c.production_date between '$date_from' and '$date_to'";
    }
    // ============================= get closing job ===============================
	$sql_input=sql_select( "SELECT a.id as job_id,b.job_no_mst, b.id from wo_po_details_master a, wo_po_break_down b,pro_garments_production_mst c where a.id=b.job_id and  b.id=c.po_break_down_id and a.status_active=1   and a.company_name=$company_id and c.production_type=4 $search_cond $buyer_id_cond $job_no_cond $job_year_cond  $date_cond" ); 
	
	if(count($sql_input)==0)
	{
		die('<div style="color:red;font-size:18px;">Data not found!');
	}
	$shipment_po_arr = array();
	$job_no_arr = array();
	foreach ($sql_input as $val) 
	{
		$shipment_po_arr[$val['JOB_ID']][$val['ID']] = $val['ID'];
		$job_no_arr[$val['JOB_ID']] = $val['JOB_ID'];
	}

	/*$job_no_cond = where_con_using_array($job_no_arr,1,"a.id");
	$sqlJob=sql_select( "SELECT a.id as job_id ,a.job_no, b.id from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.status_active=1 and b.status_active=1 $job_no_cond");
	//echo "SELECT a.job_no, b.id from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.status_active=1 and b.status_active=1 $job_no_cond";  
	$all_po_arr = array();
	foreach ($sqlJob as $v) 
	{
		$all_po_arr[$v['JOB_ID']][$v['JOB_ID']] = $v['JOB_ID'];
	}
	// echo "<pre>";print_r($shipment_po_arr);die();
	$closing_job_arr = array();
	foreach ($all_po_arr as $job => $job_data) 
	{
		if(count($all_po_arr[$job])==count($shipment_po_arr[$job]))
		{
			$closing_job_arr[$job] = $job;
		}
	}*/

	// print_r($closing_job_arr);
 	$job_no_cond = where_con_using_array($job_no_arr,0,"a.id");
 	// ============================= end closing job ===============================

	$company_library=return_library_array( "SELECT id, company_name from lib_company", "id", "company_name"  );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$arr=array (0=>$company_library,1=>$buyer_arr);
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";
	
	if($type_id==2) //PO Wise
	{
		//$po_cond="";
		$sql= "SELECT b.id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,b.id,b.po_number from wo_po_details_master a,wo_po_break_down b where a.id=b.job_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id $search_cond $buyer_id_cond $job_no_cond $job_year_cond  $job_no_cond   order by b.id desc"; 
			echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No,PO NO", "100,100,50,100,100","650","220",0, $sql , "js_set_value", "id,job_no_prefix_num,po_number","",1,"company_name,buyer_name,0,0,0,0,0",$arr,"company_name,buyer_name,year,job_no,style_ref_no,po_number","",'','0,0,0,0,0,0','',1) ;
	}
	else
	{
			$sql= "SELECT a.id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no from wo_po_details_master a where a.status_active=1 and a.is_deleted=0 and a.company_name=$company_id $search_cond $buyer_id_cond $job_no_cond $job_year_cond  $job_no_cond group by a.id,
         a.job_no, a.insert_date, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no order by a.id desc"; 
		 echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No", "100,100,50,100","550","220",0, $sql , "js_set_value", "id,job_no_prefix_num,style_ref_no","",1,"company_name,buyer_name,0,0,0,0",$arr,"company_name,buyer_name,year,job_no,style_ref_no","",'','0,0,0,0,0','',1) ;
	}
	
	/*$sql= "SELECT a.id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no from wo_po_details_master a where a.status_active=1 and a.is_deleted=0 and a.company_name=$company_id $search_cond $buyer_id_cond $job_no_cond $job_year_cond $buyer_cond $job_no_cond group by a.id,
         a.job_no, a.insert_date, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no order by a.id desc"; */
    // echo $sql;
		
	
   exit(); 
}


if($action=="generate_report")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_name 		= str_replace("'","",$cbo_company_name);
	$buyer_name 		= str_replace("'","",$cbo_buyer_name);
	$year 				= str_replace("'","",$cbo_year);
	$hidden_job_id 		= str_replace("'","",$hidden_job_id);
	$txt_job_no 		= str_replace("'","",$txt_job_no);
	$txt_style_no 		= str_replace("'","",$txt_style_no);
	$date_from 			= str_replace("'","",$txt_date_from);
	$date_to 			= str_replace("'","",$txt_date_to);	
	$type 				= str_replace("'","",$type);
	$txt_order_no 		= str_replace("'","",$txt_order_no);
	$txt_order_no_id = str_replace("'","",$txt_order_no_id);
	$cbo_shipping_status = str_replace("'","",$cbo_shipping_status);	
	
	$sql_cond .= ($buyer_name != "") 		? " and a.buyer_name in($buyer_name)" : "";
	$sql_cond .= ($year != 0) 		? " and to_char(a.insert_date,'YYYY')=$year" : "";
	$sql_cond .= ($hidden_job_id != "") 	? " and a.id in($hidden_job_id)" : "";
	$sql_cond .= ($txt_order_no_id != "") 	? " and b.id in($txt_order_no_id)" : "";
	$sql_cond .= ($company_name != 0) 	? " and a.company_name=$company_name" : "";
	//$sql_cond .= ($cbo_shipping_status != 0) 	? " and b.shiping_status=$cbo_shipping_status" : "";
	if($cbo_shipping_status!= 0 && $cbo_shipping_status!= 4)
	{
		$sql_cond .=" and b.shiping_status=$cbo_shipping_status";
	}
	else
	{
		if($cbo_shipping_status==4)
			{
				$sql_cond .=" and b.shiping_status in(1,2)";
			}
			else
			{
				$sql_cond .=" and b.shiping_status in(0,1,2,3)";
			}
	}
	if($hidden_job_id=="")
	{
		$sql_cond .= ($txt_job_no != "") 	? " and a.job_no_prefix_num in($txt_job_no)" : "";
		// $sql_cond .= ($txt_style_no != "") 	? " and a.style_ref_no like '%$txt_style_no%'" : "";
	}
	if($txt_order_no_id=="")
	{
		$sql_cond .= ($txt_order_no != "") 	? " and b.po_number='$txt_order_no' " : "";
		// $sql_cond .= ($txt_style_no != "") 	? " and a.style_ref_no like '%$txt_style_no%'" : "";
	}
	$fin_recv_date_cond = "";$prod_date_cond = "";$exfact_prod_date_cond = "";$prod_reso_pr_date_cond = ""; $cutlay_recv_date_cond="";
	if($date_from !="" && $date_to !="")
    {
        if($db_type==0)
        {
            $start_date=change_date_format($date_from,"yyyy-mm-dd","");
            $end_date=change_date_format($date_to,"yyyy-mm-dd","");
        }
        else
        {
            $start_date=date("j-M-Y",strtotime($date_from));
            $end_date=date("j-M-Y",strtotime($date_to));
        }//receive_date
       $date_cond = " and d.production_date between '$start_date' and '$end_date'";
	$fin_recv_date_cond = "  b.transaction_date between '$start_date' and '$end_date'";
	 $prod_date_cond = "  d.production_date between '$start_date' and '$end_date'"; 
	 $exfact_prod_date_cond = "  d.ex_factory_date between '$start_date' and '$end_date'";
	 $prod_reso_pr_date_cond = "  b.pr_date between '$start_date' and '$end_date'"; 
	 $cutlay_recv_date_cond = "  d.entry_date between '$start_date' and '$end_date'";
    }
	 // echo $fin_recv_date_cond;entry_date
	$company_library=return_library_array( "SELECT id, company_name from lib_company", "id", "company_name"  );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	if($type==2)
	{	

	   	$sql_input=" SELECT b.id as po_id,a.id as job_id,d.production_type,d.embel_name,e.production_qnty,e.reject_qty,e.alter_qty,e.spot_qty
			from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e where a.id=b.job_id and b.id=c.po_break_down_id and a.id=c.job_id and b.id=d.po_break_down_id and c.id=e.color_size_break_down_id and d.id=e.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and d.production_type in(4)   $sql_cond $search_cond $buyer_id_cond $job_no_cond $job_year_cond  $date_cond";
			$sql_input_result=sql_select($sql_input);
		$job_no_arr = array();
		foreach ($sql_input_result as $val) 
		{
			//$shipment_po_arr[$val['JOB_NO_MST']][$val['ID']] = $val['ID'];
			$job_no_arr[$val['JOB_ID']] = $val['JOB_ID'];
			$sewing_inputQty_arr[$val['PO_ID']]['sew_in']+=$val['PRODUCTION_QNTY'];
			$po_id_arr[$val['PO_ID']]= $val['PO_ID'];	
		}
		if(count($sql_input_result)==0)
		{
			echo '<div style="color:#FF0000; font-size:24px; font-weight:bold; float:left; width:1030px;text-align:center">No Data Found.</div>'; die;
		}
	 	$job_no_cond = where_con_using_array($job_no_arr,0,"a.id");
		
		$poId_no_cond = where_con_using_array($po_id_arr,0,"e.order_id");
		/*==========================================================================================/
		/											lay data 										/
		/==========================================================================================*/ 	
		//  $sqlLay="SELECT d.job_no,b.id,b.po_quantity, e.size_qty as lay_qty,d.fabric_width,d.other_fabric_weight from wo_po_details_master a,wo_po_break_down b,ppl_cut_lay_mst d,ppl_cut_lay_bundle e where a.job_no=d.job_no and a.id=b.job_id  and d.id=e.mst_id and b.id=e.order_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0  and e.size_qty>0  $sql_cond $job_no_cond";	
		//d.entry_date
		  
		 /* $sqlLay="SELECT d.job_no,
		 (case when   d.entry_date<'$start_date'    then d.fabric_width end) as opening_cutlay,
		(case when   $cutlay_recv_date_cond   then d.other_fabric_weight end) as cutlay_qty,
		(case when   d.entry_date<'$start_date'    then d.other_fabric_weight end) as other_opening_cutlay,
		(case when   $cutlay_recv_date_cond   then d.other_fabric_weight end) as other_cutlay_qty
		  from wo_po_details_master a,ppl_cut_lay_mst d where a.job_no=d.job_no  and a.status_active=1 and a.is_deleted=0  and d.status_active=1 and d.is_deleted=0  $job_no_cond";*/	
		   $sqlLay="SELECT d.job_no,
		 (case when   d.entry_date<'$start_date'    then d.fabric_width end) as opening_cutlay,
		(case when   $cutlay_recv_date_cond   then d.other_fabric_weight end) as cutlay_qty,
		(case when   d.entry_date<'$start_date'    then d.other_fabric_weight end) as other_opening_cutlay,
		(case when   $cutlay_recv_date_cond   then d.other_fabric_weight end) as other_cutlay_qty
		  from wo_po_details_master a,ppl_cut_lay_mst d,ppl_cut_lay_bundle e where a.job_no=d.job_no  and d.id=e.mst_id and a.status_active=1 and a.is_deleted=0  and d.status_active=1 and d.is_deleted=0   and e.status_active=1 and e.is_deleted=0  and d.other_fabric_weight is not null $poId_no_cond";		
		// echo $sqlLay;//die;other_fabric_weight
		$layData = sql_select($sqlLay);
		$tot_po_qty = 0;
		$tot_lay_qty = 0;$chk_layArr=array();
		foreach ($layData as  $val) 
		{
			 $job_no=$val['JOB_NO'];
			 if($chk_layArr[$job_no]=='')
			 {
			$Cut_lay_data_arr[$val['JOB_NO']]['opening_fab_wgt']+= $val['OTHER_OPENING_CUTLAY'];
			$Cut_lay_data_arr[$val['JOB_NO']]['fab_wgt']+=$val['OTHER_CUTLAY_QTY'];
			$chk_layArr[$job_no]=$job_no;
			 }
		}
		//print_r($Cut_lay_data_arr);
		unset($layData);
		/*==========================================================================================/
													Main query for gmts Input data 										/
		/==========================================================================================*/
			if($db_type==0) $year_field="YEAR(a.insert_date) as year"; //pub_shipment_date,unit_price,sew_effi_percent,po_total_price
		else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";	
		 $sql_po=" SELECT $year_field, a.id as job_id,a.job_no,a.order_uom,a.set_smv,a.gmts_item_id, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,b.id,b.po_number,b.pub_shipment_date,b.unit_price,d.order_total as po_total_price,d.plan_cut_qnty,d.order_quantity as po_pcs_qty,d.item_number_id,d.color_number_id,d.size_number_id,c.sew_effi_percent,c.costing_per,c.exchange_rate
			from wo_po_details_master a,wo_po_break_down b,wo_pre_cost_mst c,wo_po_color_size_breakdown d where a.id=b.job_id  and  a.id=c.job_id and  b.job_id=c.job_id  and  d.job_id=c.job_id and b.id=d.po_break_down_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   $sql_cond $job_no_cond";		
		//  echo $sql_po;die;
		$sql_res_po= sql_select($sql_po);
		$prod_data_arr = array();
		foreach ($sql_res_po as $val) 
		{
			$prod_data_arr[$val['ID']]['po_value'] += $val['PO_TOTAL_PRICE'];			
			$prod_data_arr[$val['ID']]['po_qty'] += $val['PO_PCS_QTY'];	
			$prod_data_arr[$val['ID']]['set_smv'] = $val['SET_SMV'];
			$prod_data_arr[$val['ID']]['sew_eff'] = $val['SEW_EFFI_PERCENT'];
			$prod_data_arr[$val['ID']]['costing_per'] = $val['COSTING_PER'];
			$prod_data_arr[$val['ID']]['uom'] = $val['ORDER_UOM'];
			$prod_data_arr[$val['ID']]['ship_date'] = $val['PUB_SHIPMENT_DATE'];
			$prod_data_arr[$val['ID']]['rate'] =$val['PO_TOTAL_PRICE']/$val['PO_PCS_QTY'];
			$prod_data_arr[$val['ID']]['job_no'] = $val['JOB_NO'];
			$prod_data_arr[$val['ID']]['job_id'] = $val['JOB_ID'];
			//echo $val['JOB_ID'].'d';
			$prod_data_arr[$val['ID']]['year'] = $val['YEAR'];
			$prod_data_arr[$val['ID']]['buyer'] = $val['BUYER_NAME'];
			$prod_data_arr[$val['ID']]['style'] = $val['STYLE_REF_NO'];
			$prod_data_arr[$val['ID']]['po_no'] = $val['PO_NUMBER'];		
			$prod_data_arr[$val['ID']]['item_id'] = $val['GMTS_ITEM_ID'];
			
			$job_qty_data_arr[$val['JOB_NO']]['job_qty']+=$val['PO_PCS_QTY'];
			$job_qty_data_arr[$val['JOB_NO']]['set_smv']=$val['SET_SMV'];
			$job_qty_data_arr[$val['JOB_NO']]['sew_eff']=$val['SEW_EFFI_PERCENT'];
			$job_qty_data_arr[$val['JOB_NO']]['item_id']=$val['GMTS_ITEM_ID'];
			$job_qty_data_arr[$val['JOB_NO']]['job_id']=$val['JOB_ID'];
			$job_qty_data_arr[$val['JOB_NO']]['year']=$val['YEAR'];
			$job_qty_data_arr[$val['JOB_NO']]['buyer']=$val['BUYER_NAME'];
			$job_qty_data_arr[$val['JOB_NO']]['style']=$val['STYLE_REF_NO'];
			$job_qty_data_arr[$val['JOB_NO']]['po_no'].=$val['PO_NUMBER'].',';
			$job_qty_data_arr[$val['JOB_NO']]['po_ids'].=$val['ID'].",";
			
			$job_qty_data_arr[$val['JOB_NO']]['ex_rate']=$val['EXCHANGE_RATE'];
			$job_Id_data_arr[$val['JOB_ID']]['ex_rate']=$val['EXCHANGE_RATE'];
			$po_arr[$val['JOB_ID']][$val['ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']][$val['SIZE_NUMBER_ID']]['poqty']=$val['PLAN_CUT_QNTY'];
			$po_arr[$val['JOB_ID']][$val['ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']][$val['SIZE_NUMBER_ID']]['planqty']=$val['PLAN_CUT_QNTY'];
			$po_id_arr[$val['ID']]= $val['ID'];	
		}
		//print_r($prod_data_arr);
		//========================Main Query End============***********************
		
		//==========************Aditional Po Qty Job Level================
		 
		$sql_po_job=" SELECT a.id as job_id,a.job_no,d.order_total as po_total_price,d.plan_cut_qnty,d.order_quantity as po_pcs_qty,d.item_number_id,d.color_number_id,c.costing_per
			from wo_po_details_master a,wo_po_break_down b,wo_pre_cost_mst c,wo_po_color_size_breakdown d where a.id=b.job_id  and  a.id=c.job_id and  b.job_id=c.job_id  and  d.job_id=c.job_id and b.id=d.po_break_down_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   $job_no_cond";		
		  // echo $sql_po_job;
		$sql_po_job_result= sql_select($sql_po_job);
		foreach( $sql_po_job_result as $row )
		{
			$job_data_for_lay_arr[$row['JOB_NO']]['job_qty']+=$row['PO_PCS_QTY'];
		}
		
		// =============CM Cost=================
   $sql_cm_new = "select b.id as po_id,(b.po_quantity*a.total_set_qnty) as pcs_qty,c.job_no,c.cm_cost, c.cm_cost_percent from wo_pre_cost_dtls c,wo_po_details_master a,wo_po_break_down b where a.id=c.job_id and a.id=b.job_id  and b.job_id=c.job_id  and c.status_active=1 and c.is_deleted=0  and b.status_active=1 and b.is_deleted=0  and a.status_active=1 and a.is_deleted=0 $job_no_cond";
	$data_array_cm=sql_select($sql_cm_new);
	foreach( $data_array_cm as $row_new )
	{
		$costingPer=$prod_data_arr[$row_new['PO_ID']]['costing_per'];
		$po_pcs_qty=$row_new['PCS_QTY'];
		$costingPerQty=12;
		if($costingPer==1){
		$costingPerQty=12;
		}
		elseif($costingPer==2){
		$costingPerQty=1;	
		}
		elseif($costingPer==3){
		$costingPerQty=24;
		}
		elseif($costingPer==4){
		$costingPerQty=36;
		}
		elseif($costingPer==5){
		$costingPerQty=48;
		}
		else{
		$costingPerQty=12;
		}
		$po_wise_cm_cost_arr[$row_new["PO_ID"]]=($row_new["CM_COST"]/$costingPerQty);
		$job_cost_per_arr[$row_new["PO_ID"]]=$costingPerQty;
	}
	unset($data_array_cm);
	//print_r($po_wise_cm_cost_arr);
		/*==========================================================================================/
		/										Finnacial Parameter  data 									/
		/==========================================================================================*/
		//order_wise_pro_details ,inv_transaction ,inv_receive_master	 receive_date and a.company_name=$company_name
		 $sql_std_para=sql_select("select ASKING_AVG_RATE, COST_PER_MINUTE, APPLYING_PERIOD_DATE, APPLYING_PERIOD_TO_DATE from lib_standard_cm_entry where company_id=$company_name and status_active=1 and is_deleted=0 order by id desc");
		 $financial_paraArr=array();
		foreach($sql_std_para as $row )
		{
			$applying_period_date=change_date_format($row['APPLYING_PERIOD_DATE'],'','',1);
			$applying_period_to_date=change_date_format($row['APPLYING_PERIOD_TO_DATE'],'','',1);
			$diff=datediff('d',$applying_period_date,$applying_period_to_date);
			for($j=0;$j<$diff;$j++)
			{
				$date_all=add_date(str_replace("'","",$applying_period_date),$j);
				$newdate =change_date_format($date_all,'','',1);
				$newdate=date('m-Y',strtotime($newdate));
				//echo $newdate.'D';
				if($row['COST_PER_MINUTE']>0)
				{
				$financial_paraArr[$newdate]['cost_per_minute']=$row['COST_PER_MINUTE'];
				}
				//$cost_per_minute=$row[csf('cost_per_minute')];
			}
		} 
		$start_date_cal=date('m-Y',strtotime($start_date));
		$asking_avg_rate=$financial_paraArr[$start_date_cal]['cost_per_minute'];
		//start_date
		  
		   unset($sql_std_para);
		//print_r($financial_paraArr);
		 // echo $asking_avg_rate.'=T';
		//============$job_no_cond_pre = where_con_using_array($job_no_arr,0,"a.job_id");
		  $job_no_cond_pre = where_con_using_array($job_no_arr,0,"a.job_id");
		  $po_id_cond = where_con_using_array($po_id_arr,0,"b.po_break_down_id");
		  $po_id_cond_prod = where_con_using_array($po_id_arr,0,"d.po_break_down_id");
		  
		 $gmtsitemRatioSql="select a.job_id AS JOB_ID, a.gmts_item_id AS GMTS_ITEM_ID, a.set_item_ratio AS SET_ITEM_RATIO from wo_po_details_mas_set_details a where 1=1 $job_no_cond_pre ";
		//echo $gmtsitemRatioSql; die;
		$gmtsitemRatioSqlRes = sql_select($gmtsitemRatioSql);
		$jobItemRatioArr=array();
		foreach($gmtsitemRatioSqlRes as $row)
		{
		$jobItemRatioArr[$row['JOB_ID']][$row['GMTS_ITEM_ID']]=$row['SET_ITEM_RATIO'];
		}
		unset($gmtsitemRatioSqlRes);

		 
		$sqlfab="select a.job_id AS JOB_ID, a.id AS ID, a.item_number_id AS ITEM_NUMBER_ID, a.fab_nature_id AS FAB_NATURE_ID, a.color_type_id AS COLOR_TYPE_ID, a.fabric_source as FABRIC_SOURCE, a.color_size_sensitive AS COLOR_SIZE_SENSITIVE, a.construction AS CONSTRUCTION, a.gsm_weight AS GSM_WEIGHT, a.uom AS UOM, b.po_break_down_id AS POID, b.color_number_id AS COLOR_NUMBER_ID, b.gmts_sizes AS SIZE_NUMBER_ID, b.cons AS CONS, b.requirment AS REQUIRMENT, b.rate as RATE
		from wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b
		where 1=1 and a.id=b.pre_cost_fabric_cost_dtls_id and b.cons!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $job_no_cond_pre $po_id_cond";
		//echo $sqlfab; die;
		$sqlfabRes = sql_select($sqlfab);
		$fabIdWiseGmtsDataArr=array();$ttt_gery=0;
		foreach($sqlfabRes as $row)
		{
		$poQty=$planQty=$costingPer=$itemRatio=$finReq=$greyReq=$finAmt=$greyAmt=0;
		$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
		$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
		
		$costingPer=$job_cost_per_arr[$row["POID"]];//$prod_data_arr[$row['POID']]['costing_per'];
		$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$row['ITEM_NUMBER_ID']];
		$uomArr[$row["POID"]]=$row["UOM"];
		//echo $poQty.'='.$planQty.'='.$itemRatio.'<br>';
		$finReq=($planQty/$itemRatio)*($row['CONS']/$costingPer);
		$greyReq=($planQty/$itemRatio)*($row['REQUIRMENT']/$costingPer);
		//echo $greyReq.'='.$finReq.'='.$planQty.'='.$itemRatio.'='.$row['CONS'].'='.$costingPer.'<br>';
		$finAmt=$finReq*$row['RATE'];
		$greyAmt=$greyReq*$row['RATE'];
		//echo $planQty.'='.$itemRatio.'='.$row['CONS'].'='.$row['REQUIRMENT'].'='.$costingPer.'='.$finReq.'='.$greyReq.'<br>';
		$ex_rate=$job_Id_data_arr[$row['JOB_ID']]['ex_rate'];
		if($row['FABRIC_SOURCE']==1)
		{
		$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['prodfin_qty']+=$finReq;
		$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['prodgrey_qty']+=$greyReq;
		$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['prodfin_amt']+=$finAmt*$ex_rate;
		$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['prodgrey_amt']+=$greyAmt*$ex_rate;
		}
		else if($row['FABRIC_SOURCE']==2)
		{
			//echo $finReq.'<br>';
		$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['purchfin_qty']+=$finReq;
		$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['purchgrey_qty']+=$greyReq;
		$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['purchfin_amt']+=$finAmt*$ex_rate;
		$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['purchgrey_amt']+=$greyAmt*$ex_rate;
		$ttt_gery+=$greyReq;
		}
		}
		unset($sqlfabRes);
	 //	print_r($reqQtyAmtArr);
	// echo $ttt_gery.'D';
		
		$po_id_cond = where_con_using_array($po_id_arr,0,"d.po_break_down_id");
		 $sql_cutting_qc=" SELECT  d.production_date,d.po_break_down_id as po_id,
		(case when   d.production_date<'$start_date'  then e.production_qnty end) as opening_prod_qty,
		(case when   $prod_date_cond  then e.production_qnty end) as prod_qty
		from pro_garments_production_mst d,pro_garments_production_dtls e  where  d.id=e.mst_id and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and d.production_type in(1) and e.production_type in(1)   $po_id_cond";		
		//  echo $sql_cutting_qc;
		$sql_res_cutting_qc = sql_select($sql_cutting_qc);
		$opening_cuting_qc_data_array = array();
		foreach ($sql_res_cutting_qc as $val) 
		{//cons_rate
		$opening_cuting_qc_data_array[$val['PO_ID']]['opening_cut_qty']+= $val['OPENING_PROD_QTY'];
	//	$cuting_qc_data_array[$val['PO_ID']]['cut_qty'] +=$val['PROD_QTY'];
		}
		unset($sql_res_cutting_qc);
		//=========Fabric Req from Budget===================

		$po_id_cond = where_con_using_array($po_id_arr,0,"c.po_breakdown_id");
		 /* $sql_fin_recv=" SELECT  a.receive_date,(c.quantity) as quantity,b.cons_rate,c.po_breakdown_id as po_id,
		(case when   a.receive_date<'$start_date'  then c.quantity end) as opening_quantity,
		(case when   $fin_recv_date_cond  then c.quantity end) as quantity_pcs
		from inv_receive_master a,inv_transaction b,order_wise_pro_details c where a.id=b.mst_id and b.id=c.trans_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form in(17) and c.entry_form in(17)    $po_id_cond";	*/
		 
		 $sql_fin_recv=" SELECT  b.transaction_date,(c.quantity) as quantity,b.cons_rate,c.po_breakdown_id as po_id,
		(case when   b.transaction_date<'$start_date' and  c.entry_form in(17)   then c.quantity end) as opening_quantity,
		(case when   $fin_recv_date_cond and  c.entry_form in(17)  then c.quantity end) as quantity_pcs,
		(case when   b.transaction_date<'$start_date' and  c.entry_form in(19)   then c.quantity end) as opening_issue_quantity,
		(case when   $fin_recv_date_cond and  c.entry_form in(19)  then c.quantity end) as issue_quantity_pcs
		from inv_transaction b,order_wise_pro_details c where b.id=c.trans_id  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and c.entry_form in(17,19)    $po_id_cond";		
		//  echo $sql_fin_recv;die;
		$sql_res_fin_recv = sql_select($sql_fin_recv);
		$opening_fin_rcv_data_array = array();
		foreach ($sql_res_fin_recv as $val) 
		{//cons_rate
		// ===========Issue =====================
		$opening_fin_rcv_data_array[$val['PO_ID']]['fin_qty']+= $val['OPENING_ISSUE_QUANTITY'];
		if($val['OPENING_ISSUE_QUANTITY'])
		{
		$opening_fin_rcv_data_array[$val['PO_ID']]['fin_val'] +=$val['OPENING_ISSUE_QUANTITY']*$val['CONS_RATE'];
		}
		$fin_rcv_data_array[$val['PO_ID']]['fin_qty'] +=$val['ISSUE_QUANTITY_PCS'];
		$fin_rcv_data_array[$val['PO_ID']]['fin_val'] +=$val['ISSUE_QUANTITY_PCS']*$val['CONS_RATE'];
		 //echo $val['OPENING_QUANTITY']*$val['CONS_RATE'].'d';
		 //===========Issue =====================
		/* $opening_fin_issue_data_array[$val['PO_ID']]['fin_qty']+= $val['OPENING_QUANTITY'];
		if($val['OPENING_QUANTITY'])
		{
		$opening_fin_issue_data_array[$val['PO_ID']]['fin_val'] +=$val['OPENING_QUANTITY']*$val['CONS_RATE'];
		}
		$fin_issue_data_array[$val['PO_ID']]['fin_qty'] +=$val['QUANTITY_PCS'];
		$fin_issue_data_array[$val['PO_ID']]['fin_val'] +=$val['QUANTITY_PCS']*$val['CONS_RATE'];*/
		}
		unset($sql_res_fin_recv );
		//print_r($opening_fin_rcv_data_array2);
		/*==========================================================================================/
		/										shipment data 										/
		/==========================================================================================*/	
		  $sqlExFact=" SELECT  d.po_break_down_id as po_id,
		 (case when d.entry_form !=85 then e.production_qnty else 0 end) as production_qnty,
		 (case when d.entry_form =85 then e.production_qnty else 0 end) as return_qnty,
		 (case when   d.ex_factory_date<'$start_date' and  d.entry_form !=85   then e.production_qnty end) as opening_exfact_qty,
		 (case when   $exfact_prod_date_cond and  d.entry_form !=85  then e.production_qnty end) as exfact_qty,
		  (case when   d.ex_factory_date<'$start_date' and  d.entry_form =85   then e.production_qnty end) as ret_opening_exfact_qty,
		 (case when   $exfact_prod_date_cond and  d.entry_form =85  then e.production_qnty end) as ret_exfact_qty
					 
			from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c,pro_ex_factory_mst d,pro_ex_factory_dtls e where a.id=b.job_id and b.id=c.po_break_down_id and a.id=c.job_id and b.id=d.po_break_down_id and c.id=e.color_size_break_down_id and d.id=e.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0  $sql_cond $job_no_cond";		
		// echo $sqlExFact;die;
		$sql_resExfact = sql_select($sqlExFact);
		$ship_qty = 0;
		$ship_rtn_qty = 0;
		foreach ($sql_resExfact as $val) 
		{
			$exfact_data_array[$val['PO_ID']]['opening_ex_qty']+=$val['OPENING_EXFACT_QTY']-$val['RET_OPENING_EXFACT_QTY'];
			$exfact_data_array[$val['PO_ID']]['ex_qty']+=$val['EXFACT_QTY']-$val['RET_EXFACT_QTY'];
		}
		unset($sql_resExfact);
	//	print_r($exfact_data_array);

		
		 $po_id_cond_wo = where_con_using_array($po_id_arr,0,"b.po_break_down_id");
		 $sql_trim_wo=" SELECT a.currency_id, b.job_no,b.wo_qnty,b.amount,b.po_break_down_id as po_id,c.trim_type
		from wo_booking_mst a,wo_booking_dtls b,lib_item_group c  where  a.booking_no=b.booking_no and c.id=trim_group and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_type in(2) and b.booking_type in(2)  $po_id_cond_wo";		
		$sql_res_trim = sql_select($sql_trim_wo);
		foreach ($sql_res_trim as $val) 
		{ 
			$currency_id=$val['CURRENCY_ID'];
			$JOB_NO=$val['JOB_NO'];
			$trim_type=$val['TRIM_TYPE'];
			$trim_wo_data_array[$val['PO_ID']]['currency_id']= $currency_id;
			if($trim_type==1) //Sewing 
			{
				$trim_wo_data_array[$val['PO_ID']]['sew_amt']+= $val['AMOUNT'];
				$trim_wo_data_array[$val['PO_ID']]['sew_qty']+= $val['WO_QNTY'];
			}
			if($trim_type==2) //Fin
			{
				if($currency_id==2) //USD
				{
				$ex_rate=$job_qty_data_arr[$JOB_NO]['ex_rate'];
				$trim_wo_data_array[$val['PO_ID']]['fin_rate']+= ($val['AMOUNT']/$val['WO_QNTY'])*$ex_rate;
				}
				else
				{
				$trim_wo_data_array[$val['PO_ID']]['fin_rate']+= $val['AMOUNT']/$val['WO_QNTY'];
				}
				$trim_wo_data_array[$val['PO_ID']]['fin_amt']+= $val['AMOUNT'];
				$trim_wo_data_array[$val['PO_ID']]['fin_qty']+= $val['WO_QNTY'];
			}
		
		}
		unset($sql_res_trim);
		//Fabric
		 $sql_fab_wo=" SELECT a.currency_id, b.job_no,b.grey_fab_qnty as wo_qnty,b.amount,b.po_break_down_id as po_id
		from wo_booking_mst a,wo_booking_dtls b where  a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_type in(1) and b.booking_type in(1)  $po_id_cond_wo";		
		$sql_res_fab = sql_select($sql_fab_wo);
		foreach ($sql_res_fab as $val) 
		{ 
			$currency_id=$val['CURRENCY_ID'];
			$JOB_NO=$val['JOB_NO'];
			$trim_type=$val['TRIM_TYPE'];
			$fab_wo_data_array[$val['PO_ID']]['grey_qty']+= $val['WO_QNTY'];
			$fab_wo_data_array[$val['PO_ID']]['grey_amt']+= $val['AMOUNT'];
			$fab_wo_data_array[$val['PO_ID']]['fab_wo_currency']= $currency_id;
		}
		unset($sql_res_fab);
		//print_r($fab_wo_data_array);
		//-----------
		  $sql_embl=" SELECT a.currency_id, b.job_no,b.wo_qnty,b.amount,b.po_break_down_id as po_id,c.emb_name
		from wo_booking_mst a,wo_booking_dtls b,wo_pre_cost_embe_cost_dtls c  where  a.booking_no=b.booking_no and c.id=b.pre_cost_fabric_cost_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_type in(6) and b.booking_type in(6)  and a.entry_form in(574,201)   $po_id_cond_wo";		
		//   echo $sql_embl;
		$sql_res_embl = sql_select($sql_embl);
		//	$opening_cuting_qc_data_array = array();
		foreach ($sql_res_embl as $val) 
		{ 
		$currency_id=$val['CURRENCY_ID'];
		$JOB_NO=$val['JOB_NO'];$emb_name=$val['EMB_NAME'];
		if($emb_name==1)
		{
			if($currency_id==2) //USD
			{
			$ex_rate=$job_qty_data_arr[$JOB_NO]['ex_rate'];
			$embl_print_data_array[$val['PO_ID']]['print_rate']+= ($val['AMOUNT']/$val['WO_QNTY'])*$ex_rate;
			}
			else
			{
			$embl_print_data_array[$val['PO_ID']]['print_rate']+= $val['AMOUNT']/$val['WO_QNTY'];
			}
		}
		if($emb_name==2) //Embroidery
		{
		if($currency_id==2) //USD
		{
		$ex_rate=$job_qty_data_arr[$JOB_NO]['ex_rate'];
		$embl_embroidery_data_array[$val['PO_ID']]['embro_rate']+= ($val['AMOUNT']/$val['WO_QNTY'])*$ex_rate;
		}
		else
		{
		$embl_embroidery_data_array[$val['PO_ID']]['embro_rate']+= $val['AMOUNT']/$val['WO_QNTY'];
		}
		}
		if($emb_name==3) //Wash
		{
		if($currency_id==2) //USD
		{
		$ex_rate=$job_qty_data_arr[$JOB_NO]['ex_rate'];
		$embl_embroidery_data_array[$val['PO_ID']]['wash_rate']+= ($val['AMOUNT']/$val['WO_QNTY'])*$ex_rate;
		}
		else
		{
		$embl_embroidery_data_array[$val['PO_ID']]['wash_rate']+= $val['AMOUNT']/$val['WO_QNTY'];
		}
		}
		}
		unset($sql_res_embl);
		//print_r($embl_print_data_array);
		////////============Sew Rate=======Resource allocation====================
		 $po_id_cond_reso = where_con_using_array($po_id_arr,0,"d.po_id");
		 $date_cond_prod = " and b.pr_date between '$start_date' and '$end_date'";
		 
	/*	$dataArray_sql=sql_select( "SELECT A.ID, A.LOCATION_ID, A.FLOOR_ID, A.LINE_NUMBER, B.ACTIVE_MACHINE, B.PR_DATE, B.MAN_POWER, B.OPERATOR, B.HELPER,B.SMV_ADJUST,B.SMV_ADJUST_TYPE, B.LINE_CHIEF, B.TARGET_PER_HOUR, B.WORKING_HOUR,C.FROM_DATE,C.TO_DATE,C.CAPACITY, D.PO_ID, D.GMTS_ITEM_ID ,D.TARGET_PER_LINE from prod_resource_mst a ,prod_resource_dtls b,prod_resource_dtls_mast c  left join prod_resource_color_size d on c.id=d.dtls_id and d.po_id>0 where a.id=c.mst_id and c.id=b.mast_dtl_id and a.company_id=$company_name   and b.is_deleted=0 and c.is_deleted=0  $date_cond_prod  $po_id_cond_reso order by a.location_id,a.floor_id");*/
	
		$min_shif_start=return_field_value("min(TO_CHAR(d.prod_start_time,'HH24:MI')) as line_start_time","prod_resource_mst a,prod_resource_dtls b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and  a.company_id=$company_name and shift_id=1 $date_cond_prod  and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0","line_start_time");
		$start_time_data_arr=sql_select("select company_name, shift_id, TO_CHAR(prod_start_time,'HH24:MI') as prod_start_time,TO_CHAR(lunch_start_time,'HH24:MI') as lunch_start_time from variable_settings_production where  company_name in($company_name) and  shift_id=1 and variable_list=26 and status_active=1 and is_deleted=0");
		foreach($start_time_data_arr as $row)
		{
			$start_time_arr[$row[csf('shift_id')]]['pst']=$row[csf('prod_start_time')];
			$start_time_arr[$row[csf('shift_id')]]['lst']=$row[csf('lunch_start_time')];
		}
		$prod_start_hour=$start_time_arr[1]['pst'];
		$global_start_lanch=$start_time_arr[1]['lst'];
		if($prod_start_hour=="") $prod_start_hour="08:00";
		$start_time=explode(":",$prod_start_hour);
		$hour=$start_time[0]*1; 
		$minutes=$start_time[1]; 
		$last_hour=23;
		$lineWiseProd_arr=array(); $prod_arr=array(); $start_hour_arr=array();
		$start_hour=$prod_start_hour;
		$start_hour_arr[$hour]=$start_hour;
		for($j=$hour;$j<$last_hour;$j++)
		{
			$start_hour=add_time($start_hour,60);
			$start_hour_arr[$j+1]=substr($start_hour,0,5);
		}
		//echo $pc_date_time;die;
		$start_hour_arr[$j+1]='23:59';
		if($prod_start_hour>$min_shif_start)  $prod_start_hour=$min_shif_start;
		$actual_date=date("Y-m-d");
		$actual_production_date=date("Y-m-d",strtotime(str_replace("'","",$date_from)));
		$actual_time=substr(date("Y-m-d H:i:s",strtotime($pc_date_time)),11,2);	
		$acturl_hour_minute=date("H:i",strtotime($pc_date_time));	
		$generated_hourarr=array();
		$first_hour_time=explode(":",$min_shif_start);
		$hour_line=$first_hour_time[0]*1; $minutes_one=$start_time[1];
		$line_start_hour_arr[$hour_line]=$min_shif_start;
		for($l=$hour_line;$l<$last_hour;$l++)
		{
			$min_shif_start=add_time($min_shif_start,60);
			$line_start_hour_arr[$l+1]=substr($min_shif_start,0,5);
		}
		$line_start_hour_arr[$j+1]='23:59';
	
		/*===================================================================================== /
		/								get actual resource variable							/
		/===================================================================================== */
		$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$company_name and variable_list=23 and is_deleted=0 
		and status_active=1");
		
	if($prod_reso_allo[0]==1)
	{
		$prod_resource_array=array();
		$prod_resource_array2=array();
		$prod_resource_smv_array = array();

		$dataArray_sql=sql_select("SELECT a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper,b.smv_adjust,b.smv_adjust_type, b.line_chief, b.target_per_hour, b.working_hour,c.from_date,c.to_date,c.capacity from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c where a.id=c.mst_id and c.id=b.mast_dtl_id and a.company_id=$company_name  and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $date_cond_prod");
		 
		
		foreach($dataArray_sql as $val)
		{
			$prod_resource_array[$val['ID']][$val['PR_DATE']]['man_power']=$val['MAN_POWER'];
			$prod_resource_array[$val['ID']][$val['PR_DATE']]['operator']=$val['OPERATOR'];
			$prod_resource_array[$val['ID']][$val['PR_DATE']]['helper']=$val['HELPER'];
			$prod_resource_array[$val['ID']][$val['PR_DATE']]['terget_hour']=$val['TARGET_PER_HOUR'];
			$prod_resource_array[$val['ID']][$val['PR_DATE']]['working_hour']=$val['WORKING_HOUR'];
			$prod_resource_array[$val['ID']][$val['PR_DATE']]['tpd']=$val['TARGET_PER_HOUR']*$val['WORKING_HOUR'];
			$prod_resource_array[$val['ID']][$val['PR_DATE']]['day_start']=$val['FROM_DATE'];
			$prod_resource_array[$val['ID']][$val['PR_DATE']]['day_end']=$val['TO_DATE'];
			$prod_resource_array[$val['ID']][$val['PR_DATE']]['capacity']=$val['CAPACITY'];
			$prod_resource_array[$val['ID']][$val['PR_DATE']]['smv_adjust']=$val['SMV_ADJUST'];
			$prod_resource_array[$val['ID']][$val['PR_DATE']]['smv_adjust_type']=$val['SMV_ADJUST_TYPE'];		
		}
		// echo "<pre>";print_r($prod_resource_array);die();

		// =======================================================
		$sqlRes="SELECT a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power,b.smv_adjust,b.smv_adjust_type, b.line_chief, b.target_per_hour, c.from_date,c.to_date,c.capacity,d.po_id,d.target_per_line,d.operator, d.helper,d.working_hour,d.actual_smv,d.gmts_item_id from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c,prod_resource_color_size d where a.id=c.mst_id and c.id=b.mast_dtl_id and d.mst_id=a.id and d.dtls_id=c.id and a.company_id=$company_name  and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $date_cond_prod $po_id_cond_reso";
		// echo $sqlRes;die();
		$sql_res=sql_select($sqlRes);
		/*$poIds_arr = array();
		foreach($sql_res as $vals)
		{
			$poIds_arr[$vals[csf('po_id')]] = $vals[csf('po_id')];
		}
		$poIds = implode(",", $poIds_arr);*/
		//$style_arr = return_library_array("SELECT a.style_ref_no,b.id from wo_po_details_master a,wo_po_break_down b where a.id=b.job_id and a.status_active=1 and b.status_active=1 and b.id in($poIds)","id","style_ref_no");
		foreach($sql_res as $val)
		{
			$style_ref=$prod_data_arr[$val['PO_ID']]['style'];
			//echo $style_arr[$val[csf('po_id')]].'D';
			$prod_resource_array2[$val['ID']][$style_ref][$val['PO_ID']][$val['PR_DATE']]['man_power']=$val['OPERATOR']+$val['HELPER'];
			$prod_resource_array2[$val['ID']][$style_ref][$val['PO_ID']][$val['PR_DATE']]['operator']=$val['OPERATOR'];
			$prod_resource_array2[$val['ID']][$style_ref][$val['PO_ID']][$val['PR_DATE']]['helper']=$val['HELPER'];
			$prod_resource_array2[$val['ID']][$style_ref][$val['PO_ID']][$val['PR_DATE']]['terget_hour']=$val['TARGET_PER_HOUR'];
			$prod_resource_array2[$val['ID']][$style_ref][$val['PO_ID']][$val['PR_DATE']]['working_hour']=$val['WORKING_HOUR'];
			$prod_resource_array2[$val['ID']][$style_ref][$val['PO_ID']][$val['PR_DATE']]['tpd']=$val['TARGET_PER_HOUR']*$val['WORKING_HOUR'];
			$prod_resource_array2[$val['ID']][$style_ref][$val['PO_ID']][$val['PR_DATE']]['day_start']=$val['FROM_DATE'];
			$prod_resource_array2[$val['ID']][$style_ref][$val['PO_ID']][$val['PR_DATE']]['day_end']=$val['TO_DATE'];
			$prod_resource_array2[$val['ID']][$style_ref][$val['PO_ID']][$val['PR_DATE']]['capacity']=$val['CAPACITY'];
			$prod_resource_array2[$val['ID']][$style_ref][$val['PO_ID']][$val['PR_DATE']]['smv_adjust']=$val['SMV_ADJUST'];
			$prod_resource_array2[$val['ID']][$style_ref][$val['PO_ID']][$val['PR_DATE']]['smv_adjust_type']=$val['SMV_ADJUST_TYPE'];
			$prod_resource_smv_array[$val['ID']][$style_ref][$val['PO_ID']][$val['GMTS_ITEM_ID']][$val['PR_DATE']]['actual_smv']=$val['ACTUAL_SMV'];
		}
		if($db_type==0)
		{
			$dataArray=sql_select("SELECT a.id,b.pr_date,d.shift_id,TIME_FORMAT( d.prod_start_time, '%H:%i' ) as prod_start_time,TIME_FORMAT( d.lunch_start_time, '%H:%i' ) as lunch_start_time from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$company_name and shift_id=1 and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 $date_cond_prod"); 
		}
		else
		{
			$dataArray=sql_select("SELECT a.id,b.pr_date,d.shift_id,TO_CHAR(d.prod_start_time,'HH24:MI') as prod_start_time, TO_CHAR( d.lunch_start_time,'HH24:MI') as lunch_start_time from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$company_name and shift_id=1 and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 $date_cond_prod");
		}
		$line_number_arr=array();
		foreach($dataArray as $val)
		{
			$line_number_arr[$val['ID']][$val['PR_DATE']]['shift_id']=$val['SHIFT_ID'];
			$line_number_arr[$val['id']][$val['PR_DATE']]['prod_start_time']=$val['PROD_START_TIME'];
			$line_number_arr[$val['id']][$val['PR_DATE']]['lunch_start_time']=$val['LUNCH_START_TIME'];
		}
		$sqlExtraHour="SELECT a.FLOOR_ID, b.MST_ID,b.TOTAL_SMV, b.PR_DATE FROM prod_resource_mst a, prod_resource_smv_adj b WHERE  a.id = b.mst_id AND b.STATUS_ACTIVE = 1 AND b.IS_DELETED = 0 AND b.ADJUSTMENT_SOURCE = 1 $date_cond_prod";
		// echo $sqlExtraHour;
		$sqlExtraHourResultArr=sql_select($sqlExtraHour);
		$extra_minute_production_arr=array();
		$extra_minute_resource_arr=array();
		foreach($sqlExtraHourResultArr as $ex_row)
		{
			$extra_minute_production_arr[$ex_row[FLOOR_ID]][$ex_row[MST_ID]]+=$ex_row[TOTAL_SMV];
			$extra_minute_resource_arr[$ex_row[MST_ID]][$ex_row[PR_DATE]]+=$ex_row[TOTAL_SMV];
		}
		/*===============================================================================/
		/							Actual resource SMV data							 /
		/============================================================================== */
		$prod_resource_smv_adj_array = array();
		$sql_query="SELECT b.mst_id, b.pr_date,b.number_of_emp ,b.adjust_hour,b.total_smv,b.adjustment_source from prod_resource_mst a,prod_resource_smv_adj b  where a.id=b.mst_id  and a.company_id=$company_name  and a.is_deleted=0 and b.is_deleted=0 and b.is_deleted=0 and b.status_active=1  $date_cond_prod";
		// echo $sql_query;
		$sql_query_res=sql_select($sql_query);
		foreach($sql_query_res as $val)
		{
			$val['PR_DATE']=date("d-M-Y",strtotime($val['PR_DATE']));
			$prod_resource_smv_adj_array[$val['MST_ID']][$val['PR_DATE']][$val['ADJUSTMENT_SOURCE']]['number_of_emp']+=$val['NUMBER_OF_EMP'];
			$prod_resource_smv_adj_array[$val['MST_ID']][$val['PR_DATE']][$val['ADJUSTMENT_SOURCE']]['adjust_hour']+=$val['ADJUST_HOUR'];
			$prod_resource_smv_adj_array[$val['MST_ID']][$val['PR_DATE']][$val['ADJUSTMENT_SOURCE']]['total_smv']+=$val['TOTAL_SMV'];
		}
		// echo "<pre>";print_r($prod_resource_smv_adj_array);die();
	}
	$manufacturing_company=return_field_value("listagg(comp.id,',') within group (order by comp.id) as company_id","lib_company comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 and id=$company_name","company_id");
	if($db_type==0) $prod_start_cond="prod_start_time";
	else if($db_type==2) $prod_start_cond="TO_CHAR(prod_start_time,'DD-MON-YYYY HH24:MI')";
	$variable_start_time_arr='';
	$prod_start_time=sql_select("select $prod_start_cond as prod_start_time from variable_settings_production where company_name=$company_name and variable_list=26 and status_active=1 and is_deleted=0 and shift_id=1");
	//echo "select company_name, prod_start_time from variable_settings_production where company_name=$cbo_company_id and variable_list=26 and status_active=1 and is_deleted=0 and shift_id=1";
	foreach($prod_start_time as $row)
	{
		$ex_time=explode(" ",$row['PROD_START_TIME']);
		if($db_type==0) $variable_start_time_arr=$row['PROD_START_TIME'];
		else if($db_type==2) $variable_start_time_arr=$ex_time[1];
	}//die;
	//echo $variable_start_time_arr;
	unset($prod_start_time);
	$current_date_time=date('d-m-Y H:i');
	$variable_date=change_date_format(str_replace("'","",$date_from)).' '.$variable_start_time_arr;
	//echo $variable_date.'='.$current_date_time;
	$datediff=datediff("n",$variable_date,$current_date_time);
	
	$ex_date_time=explode(" ",$current_date_time);
	$current_date=$ex_date_time[0];
	$current_time=$ex_date_time[1];
	$ex_time=explode(":",$current_time);
	
	$search_prod_date=change_date_format(str_replace("'","",$date_from));
	$current_eff_min=($ex_time[0]*60)+$ex_time[1];
	//echo $current_date.'='.$search_prod_date;
	$variable_time= explode(":",$variable_start_time_arr);
	$vari_min=($variable_time[0]*60)+$variable_time[1];
	$difa_time=explode(".",number_format(($current_eff_min-$vari_min)/60,2));//datediff("",$ctime,$variable_start_time_arr);
	$dif_time=number_format($datediff/60,2);
	$dif_hour_min=date("H", strtotime($dif_time));
	/*===================================================================================== /
	/										smv sorce 										/
	/===================================================================================== */
   	$smv_source=return_field_value("smv_source","variable_settings_production","company_name in ($manufacturing_company) and variable_list=25 and   status_active=1 and is_deleted=0");
	// echo $smv_source;
    if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;
	if($db_type==2)
	{
		$pr_date=str_replace("'","",$txt_date);
		$pr_date_old=explode("-",str_replace("'","",$txt_date));
		$month=strtoupper($pr_date_old[1]);
		$year=substr($pr_date_old[2],2);
		$pr_date=$pr_date_old[0]."-".$month."-".$year;
	}
	 $check_arr=array();
	/*===================================================================================== /
	/								get inhouse production data								/
	/===================================================================================== */
	//echo $last_hour.'D';die;
	$po_id_cond_po = where_con_using_array($po_id_arr,0,"c.id");
	 $date_cond_prod_sewout = " and a.production_date between '$start_date' and '$end_date'";
	$sqlSew="SELECT  a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line,b.buyer_name  as buyer_name,b.style_ref_no,b.job_no, a.po_break_down_id, a.item_number_id,c.po_number as po_number,c.file_no,c.unit_price,c.grouping as ref,d.color_type_id,a.remarks,e.floor_serial_no,sum(d.production_qnty) as good_qnty,"; 
		$first=1;
		for($h=$hour;$h<$last_hour;$h++)
		{
			$bg=$start_hour_arr[$h];
			$end=substr(add_time($start_hour_arr[$h],60),0,5);
			$prod_hour="prod_hour".substr($bg,0,2);
			if($first==1)
			{
				$sqlSew.="sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN production_qnty else 0 END) AS $prod_hour,";
			}
			else
			{
				$sqlSew.="sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 
				THEN production_qnty else 0 END) AS $prod_hour,";
			}
			$first++;
		}
		$sqlSew.="sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN production_qnty else 0 END) AS prod_hour23 
		FROM  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a ,pro_garments_production_dtls d,lib_prod_floor e
		WHERE a.production_type=5 and a.po_break_down_id=c.id and c.job_id=b.id and a.id=d.mst_id and e.id=a.floor_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.company_id=$company_name and b.job_no='BFL-22-00301'  $date_cond_prod_sewout $po_id_cond_po
		GROUP BY b.job_no, a.company_id, a.location, a.floor_id,a.po_break_down_id,a.prod_reso_allo, a.production_date, a.sewing_line,b.buyer_name,b.style_ref_no,a.item_number_id,c.po_number,c.unit_price,c.file_no,c.grouping ,d.color_type_id,a.remarks,e.floor_serial_no
		ORDER BY a.location,e.floor_serial_no";
	 	//echo $sqlSew; 
		$sql_resqlt=sql_select($sqlSew);
		$production_data_arr=array(); $style_chane_arr=array(); $production_po_data_arr=array(); $production_serial_arr=array(); $reso_line_ids=''; $all_po_id=""; $active_days_arr=array(); $duplicate_date_arr=array(); $poIdArr=array(); $jobArr=array(); $prod_line_array = array(); $line_style_chk_array = array(); $date_wise_line_chk_array = array();
		$reso_line_ids=''; 
		$all_po_id="";
		$active_days_arr=array();
		$duplicate_date_arr=array();
		$poIdArr=array();
		$prod_line_array = array();
		$line_style_chk_array = array();
		$line_wise_style_array = array();
		foreach($sql_resqlt as $val)
		{	
		$prod_line_array[$val['SEWING_LINE']] = $val['SEWING_LINE'];
		$poIdArr[$val['PO_BREAK_DOWN_ID']] = $val['PO_BREAK_DOWN_ID'];	 
		if($val['PROD_RESO_ALLO']==1)
		{
			$sewing_line_ids=$prod_reso_arr[$val['SEWING_LINE']];
			$sl_ids_arr = explode(",", $sewing_line_ids);
			$sewing_line_id = $sl_ids_arr[0]; // always 1st line id will take

			$reso_line_ids.=$val['SEWING_LINE'].',';
		}
		else
		{
			$sewing_line_id=$val['SEWING_LINE'];
		}
		
		if($lineSerialArr[$sewing_line_id]=="")
		{
			$lastSlNo++;
			$slNo=$lastSlNo;
			$lineSerialArr[$sewing_line_id]=$slNo;
		}
		else 
		{
			$slNo=$lineSerialArr[$sewing_line_id];
		}
		$production_serial_arr[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$slNo][$val['SEWING_LINE']][$val['STYLE_REF_NO']]=$val['SEWING_LINE'];
		
		$line_start=$line_number_arr[$val['SEWING_LINE']][$val['PRODUCTION_DATE']]['prod_start_time'];
		if($line_start!="") 
		{ 
			$line_start_hour=substr($line_start,0,2); 
			if(substr($line_start_hour,0,1)==0)  $line_start_hour=substr($line_start_hour,1,1);	
		}
		else
		{
			$line_start_hour=$hour; 
		}
		
	 	for($h=$hour;$h<$last_hour;$h++)
		{
			$prod_hour="prod_hour".substr($start_hour_arr[$h],0,2)."";
			$production_data_arr[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']][$val['STYLE_REF_NO']][$prod_hour]+=$val[csf($prod_hour)]; 
			
			if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date)) 
			{
				if( $h>=$line_start_hour && $h<=$actual_time)
				{
					$production_po_data_arr[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']][$val['PO_BREAK_DOWN_ID']]+=$val[csf($prod_hour)]; 
				} 	
			}
			
			if(str_replace("'","",$actual_production_date)<str_replace("'","",$actual_date)) 
			{	
				$production_po_data_arr[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']][$val['PO_BREAK_DOWN_ID']]+=$val[csf($prod_hour)];
			}
		}
		
		if(str_replace("'","",$actual_production_date)>=str_replace("'","",$actual_date)) 
		{	
			if( $h>=$line_start_hour && $h<=$actual_time)
			{
				$production_po_data_arr[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']][$val['PO_BREAK_DOWN_ID']]+=$val['PROD_HOUR23'];     
			} 	
		}
		else
		{
			$production_po_data_arr[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']][$val['PO_BREAK_DOWN_ID']]+=$val['PROD_HOUR23'];     
		}
		
	 	$production_data_arr[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']][$val['STYLE_REF_NO']]['prod_hour23']+=$val['PROD_HOUR23'];  
		$production_data_arr[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']][$val['STYLE_REF_NO']]['prod_reso_allo']=$val['PROD_RESO_ALLO']; 
		
	 	if($production_data_arr[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']][$val['STYLE_REF_NO']]['buyer_name']!="")
		{
			$production_data_arr[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']][$val['STYLE_REF_NO']]['buyer_name'].=",".$val['BUYER_NAME']; 
		}
	 	else
		{
			$production_data_arr[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']][$val['STYLE_REF_NO']]['buyer_name']=$val['BUYER_NAME']; 
		}

		if($line_style_chk_array[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']][$val['STYLE_REF_NO']] =="")
		{
			$line_wise_style_count_arr[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']]++;
			$line_wise_style_count_arr2[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']]++;
			$line_style_chk_array[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']][$val['STYLE_REF_NO']] = $val['STYLE_REF_NO'];
		}

		/*if($line_wise_style_count_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]!="")
		{
			$line_wise_style_count_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]].=",".$val[csf('buyer_name')]; 
		}
	 	else
		{
			$line_wise_style_count_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]=$val[csf('buyer_name')]; 
		}*/


		if($line_wise_style_array[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']]['style']!="")
		{
			$line_wise_style_array[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']]['style'].="##".$val['STYLE_REF_NO'];
		}
	 	else
		{
			$line_wise_style_array[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']]['style']=$val['STYLE_REF_NO']; 
		}

	
	 	if($production_data_arr[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']][$val['STYLE_REF_NO']]['po_number']!="")
		{
			//$production_data_arr[$val['FLOOR_ID']][$val['SEWING_LINE']][$val['STYLE_REF_NO']]['po_number'].=",".$val[csf('po_number')];
			//$production_data_arr[$val['FLOOR_ID']][$val['SEWING_LINE']][$val['STYLE_REF_NO']]['job_no'].=",".$val[csf('job_no')];
			$production_data_arr[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']][$val['STYLE_REF_NO']]['po_id'].=",".$val['PO_BREAK_DOWN_ID'];
			$production_data_arr[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']][$val['STYLE_REF_NO']]['style'].="##".$val['STYLE_REF_NO'];
			//$production_data_arr[$val['FLOOR_ID']][$val['SEWING_LINE']][$val['STYLE_REF_NO']]['file'].=",".$val[csf('file_no')];
			//$production_data_arr[$val['FLOOR_ID']][$val['SEWING_LINE']][$val['STYLE_REF_NO']]['ref'].=",".$val[csf('ref')]; 
		}
	 	else
		{
			//$production_data_arr[$val['FLOOR_ID']][$val['SEWING_LINE']][$val['STYLE_REF_NO']]['po_number']=$val[csf('po_number')];
			//$production_data_arr[$val['FLOOR_ID']][$val['SEWING_LINE']][$val['STYLE_REF_NO']]['job_no']=$val[csf('job_no')];
			$production_data_arr[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']][$val['STYLE_REF_NO']]['po_id']=$val['PO_BREAK_DOWN_ID']; 
			$production_data_arr[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']][$val['STYLE_REF_NO']]['style']=$val['STYLE_REF_NO']; 
			//$production_data_arr[$val['FLOOR_ID']][$val['SEWING_LINE']][$val['STYLE_REF_NO']]['file']=$val[csf('file_no')]; 
			//$production_data_arr[$val['FLOOR_ID']][$val['SEWING_LINE']][$val['STYLE_REF_NO']]['ref']=$val[csf('ref')]; 
		}
		if($production_data_arr[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']][$val['STYLE_REF_NO']]['item_number_id']!="")
		{
			$production_data_arr[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']][$val['STYLE_REF_NO']]['item_number_id'].="****".$val['PO_BREAK_DOWN_ID']."**".$val['ITEM_NUMBER_ID']."**".$val['UNIT_PRICE']; 
		}
		else
		{
			 $production_data_arr[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']][$val['STYLE_REF_NO']]['item_number_id']=$val['PO_BREAK_DOWN_ID']."**".$val['ITEM_NUMBER_ID']."**".$val['UNIT_PRICE']; 
		}
		$production_data_arr[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']][$val['STYLE_REF_NO']]['quantity']+=$val['GOOD_QNTY'];
		$production_data_arr_qty[$val['PRODUCTION_DATE']][$val['FLOOR_ID']][$val['SEWING_LINE']][$val['PO_BREAK_DOWN_ID']][$val['ITEM_NUMBER_ID']]['quantity']+=$val['GOOD_QNTY'];
		
		//if($all_po_id=="") $all_po_id=$val['PO_BREAK_DOWN_ID']; else $all_po_id.=",".$val['PO_BREAK_DOWN_ID'];
	}
	/*===================================================================================== /
	/										po item wise smv 								/
	/===================================================================================== */
	$po_id_cond_item = where_con_using_array($po_id_arr,0,"b.id");
	$po_id_cond_sew = where_con_using_array($po_id_arr,0,"a.po_break_down_id");
	$sql_item="SELECT b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, c.smv_pcs, c.smv_pcs_precost from wo_po_details_master a, wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no    and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,2,3) $po_id_cond_item";
	// echo $smv_source."===".$sql_item;die;
	$resultItem=sql_select($sql_item);
	foreach($resultItem as $itemData)
	{
		if($smv_source==1) //GMTS_ITEM_ID,SMV_PCS_PRECOST
		{
			$item_smv_array[$itemData['ID']][$itemData['GMTS_ITEM_ID']]=$itemData['SMV_PCS'];
		}
		else if($smv_source==2)
		{
			$item_smv_array[$itemData['ID']][$itemData['GMTS_ITEM_ID']]=$itemData['SMV_PCS_PRECOST'];
		}
	}
	/*===================================================================================== /
	/										po active days									/
	/===================================================================================== */
    $po_active_sql="SELECT a.floor_id,a.sewing_line,a.production_date,a.po_break_down_id,a.item_number_id from  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and  a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and a.company_id=$company_name  $po_id_cond_sew group by  a.floor_id,a.sewing_line,a.production_date ,a.po_break_down_id,a.item_number_id";
    //echo $po_active_sql;die;
	foreach(sql_select($po_active_sql) as $vals)
	{
		$prod_dates=$vals['PRODUCTION_DATE'];
		if($duplicate_date_arr[$vals['PO_BREAK_DOWN_ID']][$vals['ITEM_NUMBER_ID']][$prod_dates]=="")
		{
			$active_days_arr[$vals['floor_id']][$vals['SEWING_LINE']]++;
			$active_days_arr_powise[$vals['PO_BREAK_DOWN_ID']][$vals['ITEM_NUMBER_ID']]+=1;
			$duplicate_date_arr[$vals['PO_BREAK_DOWN_ID']][$vals['ITEM_NUMBER_ID']][$prod_dates]=$prod_dates;
		}
	}
	if($prod_reso_allo[0]==1)
	{
		  $date_cond_prod = " and b.pr_date between '$start_date' and '$end_date'";
		$dataArray_sql=sql_select("SELECT a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper,b.smv_adjust,b.smv_adjust_type, b.line_chief, b.target_per_hour, b.working_hour,c.from_date,c.to_date,c.capacity,d.floor_serial_no from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c,lib_prod_floor d where a.id=c.mst_id and c.id=b.mast_dtl_id and d.id=a.floor_id and a.company_id=$company_name  and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 $date_cond_prod  order by d.floor_serial_no ");
		 
		 
		
		foreach($dataArray_sql as $val)
		{
			// $sewing_line_id=$prod_reso_arr[$val[csf('id')]];

			$sewing_line_ids=$prod_reso_arr[$val['ID']];
			$sl_ids_arr = explode(",", $sewing_line_ids);
			$sewing_line_id = $sl_ids_arr[0]; // always 1st line id will take			
			
			if($lineSerialArr[$sewing_line_id]=="")
			{
				$lastSlNo++;
				$slNo=$lastSlNo;
				$lineSerialArr[$sewing_line_id]=$slNo;
			}
			else $slNo=$lineSerialArr[$sewing_line_id];
			if($date_wise_line_chk_array[$val['PR_DATE']][$val['FLOOR_ID']][$val['ID']]=="")
			{
			$production_serial_arr[$val['PR_DATE']][$val['FLOOR_ID']][$slNo][$val['ID']][]=$val['ID'];				
			$production_serial_arr2[$val['PR_DATE']][$val['FLOOR_ID']][$slNo][$val['ID']]=$val['ID'];	
			}
		}		
	}
	 $dataArray_sum=sql_select("SELECT a.id, a.floor_id,1 as prod_reso_allo,2 as type_line, a.line_number as line_no, b.man_power, b.operator, b.helper, b.working_hour,b.target_per_hour,b.smv_adjust,b.smv_adjust_type,d.prod_start_time from  prod_resource_dtls b,prod_resource_dtls_time d,prod_resource_mst a  where a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and a.company_id=$company_name   and d.shift_id=1 and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 and a.id not in($res_line_cond)  $date_cond_prod  group by a.id, a.floor_id, a.line_number, d.prod_start_time,b.man_power, b.operator, b.helper, b.working_hour,b.target_per_hour,b.smv_adjust,b.smv_adjust_type order by a.floor_id");
		 $no_prod_line_arr=array();
		 foreach( $dataArray_sum as $row)
		 { 			 
			$sewing_line_id=$row['LINE_NO'];
		
			if($lineSerialArr[$sewing_line_id]=="")
			{
				$lastSlNo++;
				$slNo=$lastSlNo;
				$lineSerialArr[$sewing_line_id]=$slNo;
			}
			else $slNo=$lineSerialArr[$sewing_line_id];
			
			// $production_serial_arr[$row[csf('floor_id')]][$slNo][$row[csf('id')]]=$row[csf('id')]; 
			$production_data_arr[$row['FLOOR_ID']][$row['ID']]['type_line']=$row['TYPE_LINE'];
			$production_data_arr[$row['FLOOR_ID']][$row['ID']]['prod_reso_allo']=$row['PROD_RESO_ALLO']; 
			$production_data_arr[$row['FLOOR_ID']][$row['ID']]['man_power']=$row['MAN_POWER']; 
			$production_data_arr[$row['FLOOR_ID']][$row['ID']]['operator']=$row['OPERATOR']; 
			$production_data_arr[$row['FLOOR_ID']][$row['ID']]['helper']=$row['HELPER']; 
			$production_data_arr[$row['FLOOR_ID']][$row['ID']]['working_hour']=$row['WORKING_HOUR'];						
			$production_data_arr[$row['FLOOR_ID']][$row['ID']]['terget_hour']=$row['TARGET_PER_HOUR'];
			$production_data_arr[$row['FLOOR_ID']][$row['ID']]['total_line_hour']=$row['MAN_POWER']*$row['WORKING_HOUR']; 
			$production_data_arr[$row['FLOOR_ID']][$row['ID']]['smv_adjust']=$row['SMV_ADJUST']; 
			$production_data_arr[$row['FLOOR_ID']][$row['ID']]['smv_adjust_type']=$row['SMV_ADJUST_TYPE']; 
			$production_data_arr[$row['FLOOR_ID']][$row['ID']]['prod_start_time']=$row['PROD_START_TIME'];
		 }
		// print_r($production_serial_arr);
		$efficiency_min=0;$style_wise_avilable_minArr=array();
	foreach($production_serial_arr as $pr_date=>$dateData)
	{
	foreach($dateData as $f_id=>$fname)
	{
		ksort($fname);
		foreach($fname as $sl=>$s_data)
		{			
			foreach($s_data as $l_id=>$ldata)
			{
				$l=0;
				$pp = 0;
				$lc=0;
				foreach ($ldata as $style_key => $style_data) 
				{
					$germents_item=array_unique(explode('****',$production_data_arr[$pr_date][$f_id][$l_id][$style_key]['item_number_id']));
					   	foreach($germents_item as $g_val)
						{
							
							$po_garment_item=explode('**',$g_val);
							if($garment_itemname!='') $garment_itemname.=',';
							$garment_itemname.=$garments_item[$po_garment_item[1]];
							if($item_ids=='') $item_ids=$po_garment_item[1];else $item_ids.=",".$po_garment_item[1];
							if($active_days=="")$active_days=$active_days_arr_powise[$po_garment_item[0]][$po_garment_item[1]];
							else $active_days.=','.$active_days_arr_powise[$po_garment_item[0]][$po_garment_item[1]];
							
							
							//echo $item_po_array[$po_garment_item[0]][$po_garment_item[1]]['amt'].'<br>';
							$tot_po_qty+=$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['qty'];
							$tot_po_amt+=$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['amt'];
							if($item_smv!='') $item_smv.='/';
							//echo $po_garment_item[0].'='.$po_garment_item[1];
							$item_smv.=$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
							// $item_smv.=$prod_resource_smv_array[$l_id][$style_key][$po_garment_item[1]][$pr_date]['actual_smv'];
							if($order_no_total!="") $order_no_total.=",";
							$order_no_total.=$po_garment_item[0];
							if($smv_for_item!="") $smv_for_item.="****".$po_garment_item[0]."**".$item_smv;
							else
							$smv_for_item=$po_garment_item[0]."**".$item_smv;	
							$produce_minit+=$production_po_data_arr[$pr_date][$f_id][$l_id][$po_garment_item[0]]*$item_smv_array[$po_garment_item[0]][$po_garment_item[1]];
							// echo $production_po_data_arr[$f_id][$l_id][$po_garment_item[0]]."*".$prod_resource_smv_array[$l_id][$style_key][$po_garment_item[1]][$pr_date]['actual_smv']."<br>";
							$fob_rate=$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['amt']/$item_po_array[$po_garment_item[0]][$po_garment_item[1]]['qty'];
							$prod_qty=$production_data_arr_qty[$pr_date][$f_id][$l_id][$po_garment_item[0]][$po_garment_item[1]]['quantity'];
							//echo $prod_qty.'<br>';
							if(is_nan($fob_rate)){ $fob_rate=0; }
							$fob_val+=$prod_qty*$fob_rate;
							
						} //==========Gmt End
						 $type_line=$production_data_arr[$pr_date][$f_id][$l_id][$style_key]['type_line'];
						$prod_reso_allo=$production_data_arr[$pr_date][$f_id][$l_id][$style_key]['prod_reso_allo'];
						$sewing_line='';
						if($production_data_arr[$pr_date][$f_id][$l_id][$style_key]['prod_reso_allo']!="")
						{
							if($production_data_arr[$pr_date][$f_id][$l_id][$style_key]['prod_reso_allo']==1)
							{
								$line_number=explode(",",$prod_reso_arr[$l_id]);
								foreach($line_number as $val)
								{
									// echo $l_id."<br>";
									if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
								}
							}
							else $sewing_line=$lineArr[$l_id];
						}
						else
						{
							// echo $l_id."kakku<br>";
							$line_number=explode(",",$prod_reso_arr[$l_id]);
							foreach($line_number as $val)
							{
								// echo $val."kakku<br>";
								if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
							}
							
						}
						// echo $sewing_line."==".$production_data_arr[$f_id][$l_id][$style_key]['prod_reso_allo']."=kakku<br>";
				 		// 	die();

						$lunch_start="";
						$lunch_start=$line_number_arr[$l_id][$pr_date]['lunch_start_time'];  
						$lunch_hour=$start_time_arr[$row[1]]['lst']; 
						if($lunch_start!="") 
						{ 
						$lunch_start_hour=$lunch_start; 
						}
						else
						{
						$lunch_start_hour=$lunch_hour; 
						}
					  	  
						$production_hour=array();
						$prod_hours = 0;
						for($h=$hour;$h<=$last_hour;$h++)
						{
							 $prod_hour="prod_hour".substr($line_start_hour_arr[$h],0,2).""; 
							 $production_hour[$prod_hour]=$production_data_arr[$pr_date][$f_id][$l_id][$style_key][$prod_hour];
							 $floor_production[$prod_hour]+=$production_data_arr[$pr_date][$f_id][$l_id][$style_key][$prod_hour];
							 $total_production[$prod_hour]+=$production_data_arr[$pr_date][$f_id][$l_id][$style_key][$prod_hour];
							 if($production_data_arr[$pr_date][$f_id][$l_id][$style_key][$prod_hour]>0)
							 {
							 	$prod_hours++;
							 }
						}				
						
		 				$floor_production['prod_hour24']+=$production_data_arr[$pr_date][$f_id][$l_id][$style_key]['prod_hour23'];
						$total_production['prod_hour24']+=$production_data_arr[$pr_date][$f_id][$l_id][$style_key]['prod_hour23'];
						$production_hour['prod_hour24']=$production_data_arr[$pr_date][$f_id][$l_id][$style_key]['prod_hour23']; 
						$line_production_hour=0;
						// echo $actual_production_date.'='.$actual_date.'X';;
						 
						
						if(str_replace("'","",$actual_production_date)>str_replace("'","",$actual_date)) 
						{
						//	echo "A";
							if($type_line==2) //No Profuction Line
							{
								$line_start=$production_data_arr[$pr_date][$f_id][$l_id][$style_key]['prod_start_time'];
							}
							else
							{
								$line_start=$line_number_arr[$l_id][$pr_date]['prod_start_time'];
							}
							if($line_start!="") 
							{ 
								$line_start_hour=substr($line_start,0,2); 
								if(substr($line_start_hour,0,1)==0)  $line_start_hour=substr($line_start_hour,1,1);	
							}
							else
							{
								$line_start_hour=$hour; 
							}
							$actual_time_hour=0;
							$total_eff_hour=0;
							for($lh=$line_start_hour;$lh<=$last_hour;$lh++)
							{
							
								$bg=$start_hour_arr[$lh];
								if($lh<$actual_time)
								{
								$total_eff_hour=$total_eff_hour+1;;	
								$line_hour="prod_hour".substr($bg,0,2)."";
								$line_production_hour+=$production_data_arr[$pr_date][$f_id][$l_id][$style_key][$line_hour];
								$line_floor_production+=$production_data_arr[$pr_date][$f_id][$l_id][$style_key][$line_hour];
								$line_total_production+=$production_data_arr[$pr_date][$f_id][$l_id][$style_key][$line_hour];
								$actual_time_hour=$start_hour_arr[$lh+1];
								}
							}
		 					if($start_hour_arr[$actual_time]>$lunch_start_hour) $total_eff_hour=$total_eff_hour-1;
							
							if($type_line==2)
							{
								if($total_eff_hour>$production_data_arr[$pr_date][$f_id][$l_id][$style_key]['working_hour'])
								{
									 $total_eff_hour=$production_data_arr[$pr_date][$f_id][$l_id][$style_key]['working_hour'];
								}
							}
							else
							{
								if($line_wise_style_count_arr[$pr_date][$f_id][$l_id]>1)
								{
									if($total_eff_hour>$prod_resource_array2[$l_id][$style_key][$pr_date]['working_hour'])
									{
										$total_eff_hour=$prod_resource_array2[$l_id][$style_key][$pr_date]['working_hour'];
									}
								}
								else
								{
									if($total_eff_hour>$prod_resource_array[$l_id][$pr_date]['working_hour'])
									{
										$total_eff_hour=$prod_resource_array[$l_id][$pr_date]['working_hour'];
									}
								}
							}
							
						}
						
						//echo $actual_production_date.'='.$actual_date.'<br>';;
						if(str_replace("'","",$actual_production_date)<=str_replace("'","",$actual_date)) 
						{
							
							for($ah=$hour;$ah<=$last_hour;$ah++)
							{
								$prod_hour="prod_hour".substr($start_hour_arr[$ah],0,2).""; 
								$line_production_hour+=$production_data_arr[$pr_date][$f_id][$l_id][$style_key][$prod_hour];
								$line_floor_production+=$production_data_arr[$pr_date][$f_id][$l_id][$style_key][$prod_hour];
								$line_total_production+=$production_data_arr[$pr_date][$f_id][$l_id][$style_key][$prod_hour];
							}
							if($type_line==2)
							{
								$total_eff_hour=$production_data_arr[$pr_date][$f_id][$l_id][$style_key]['working_hour'];
							}
							else
							{
								
								if($line_wise_style_count_arr[$pr_date][$f_id][$l_id]>1)
								{
									$total_eff_hour=$prod_resource_array2[$l_id][$style_key][$pr_date]['working_hour'];	
									//echo $total_eff_hour."A,";
								}
								else
								{
									$total_eff_hour=$prod_resource_array[$l_id][$pr_date]['working_hour'];
									//echo $total_eff_hour."B,";
								}
								//echo $total_eff_hour."=,";
							}
						}
						$days_run=0;
						$days_run= $active_days_arr[$f_id][$l_id];
						$current_wo_time=0;
						// echo $current_date.'='.$search_prod_date.',';;
						if($current_date==$search_prod_date)
						{
							$prod_wo_hour=$total_eff_hour;
							
							if ($dif_time<$prod_wo_hour)//
							{
								$current_wo_time=$dif_hour_min;
								$cla_cur_time=$dif_time;
							}
							else
							{
								$current_wo_time=$prod_wo_hour;
								$cla_cur_time=$prod_wo_hour;
							}
						}
						else
						{
							$current_wo_time=$total_eff_hour;
							$cla_cur_time=$total_eff_hour;
						}
						$total_adjustment=0;
						//echo $type_line.'D====';
						if($type_line==2) //No Production Line
						{
							$smv_adjustmet_type=$production_data_arrpr_date[$pr_date][$f_id][$l_id][$style_key]['smv_adjust_type'];
							$eff_target=($production_data_arr[$pr_date][$f_id][$l_id][$style_key]['terget_hour']*$total_eff_hour);

							if($total_eff_hour>=$production_data_arr[$pr_date][$f_id][$l_id][$style_key]['working_hour'])
							{
								if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$production_data_arr[$pr_date][$f_id][$l_id][$style_key]['smv_adjust'];
								if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($production_data_arr[$pr_date][$f_id][$l_id][$style_key]['smv_adjust'])*(-1);
							}
							
							if($production_data_arr[$pr_date][$f_id][$l_id][$style_key]['man_power']>0)
							{
								$efficiency_min=$total_adjustment+($production_data_arr[$pr_date][$f_id][$l_id][$style_key]['man_power'])*$cla_cur_time*60;
							//$style_wise_avilable_minArr[$style_key]+=$total_adjustment+($production_data_arr[$pr_date][$f_id][$l_id][$style_key]['man_power'])*$cla_cur_time*60;
							}
							$extra_minute_production_arr=$efficiency_min+$extra_minute_arr[$f_id][$l_id];
							
							$line_efficiency=(($produce_minit)*100)/$efficiency_min;
							
							
						}
						else
						{
							if($line_wise_style_count_arr[$pr_date][$f_id][$l_id]>1)
							{
								$smv_adjustmet_type=$prod_resource_array2[$l_id][$style_key][$pr_date]['smv_adjust_type'];
								$eff_target=($prod_resource_array2[$l_id][$style_key][$pr_date]['terget_hour']*$total_eff_hour);
								
								if($total_eff_hour>=$prod_resource_array2[$l_id][$style_key][$pr_date]['working_hour'])
								{
									if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$prod_resource_array2[$l_id][$style_key][$pr_date]['smv_adjust'];
									if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($prod_resource_array2[$l_id][$style_key][$pr_date]['smv_adjust'])*(-1);
								}						
								// echo "A";
								// $efficiency_min+=$total_adjustment+($prod_resource_array2[$l_id][$style_key][$pr_date]['man_power'])*$cla_cur_time*60;
								
								if($prod_resource_array2[$l_id][$style_key][$pr_date]['man_power']>0)
								{
									$efficiency_min=($prod_resource_array2[$l_id][$style_key][$pr_date]['man_power'])*$cla_cur_time*60;
								//$style_wise_avilable_minArr[$style_key]+=($prod_resource_array2[$l_id][$style_key][$pr_date]['man_power'])*$cla_cur_time*60;
								}
								// echo "string".$total_adjustment."+(".$prod_resource_array2[$l_id][$style_key][$pr_date]['man_power'].")*".$cla_cur_time."*60<br>";
								$extra_minute_resource_arr=$efficiency_min+$extra_minute_arr[$l_id][$pr_date];
								
								$line_efficiency=(($produce_minit)*100)/$efficiency_min;
							}
							else
							{
								$smv_adjustmet_type=$prod_resource_array[$l_id][$pr_date]['smv_adjust_type'];
								$eff_target=($prod_resource_array[$l_id][$pr_date]['terget_hour']*$total_eff_hour);
								
								if($total_eff_hour>=$prod_resource_array[$l_id][$pr_date]['working_hour'])
								{
									if(str_replace("'","",$smv_adjustmet_type)==1) $total_adjustment=$prod_resource_array[$l_id][$pr_date]['smv_adjust'];
									if(str_replace("'","",$smv_adjustmet_type)==2) $total_adjustment=($prod_resource_array[$l_id][$pr_date]['smv_adjust'])*(-1);
								 	 
								}	
								//echo $style_key.'='.$prod_resource_array[$l_id][$pr_date]['man_power'].'='.$cla_cur_time."*60<br>";					
								
								if($prod_resource_array[$l_id][$pr_date]['man_power']>0)
								{
									$efficiency_min=($prod_resource_array[$l_id][$pr_date]['man_power'])*$cla_cur_time*60;
								//$style_wise_avilable_minArr[$style_key]+=($prod_resource_array[$l_id][$pr_date]['man_power'])*$cla_cur_time*60;
								}
								
							//	echo "B=".$prod_resource_array[$l_id][$pr_date]['man_power'].'='.$cla_cur_time.'<br>';
								$extra_minute_resource_arr=$efficiency_min+$extra_minute_arr[$l_id][$pr_date];
								$line_efficiency=(($produce_minit)*100)/$efficiency_min;
							}
						 }
						 // adjustment extra hour when multiple style running in a single line =========================
						$txtDate = str_replace("'", "", $pr_date);
						// echo "string==$l_id".$txtDate."<br>";
						// echo $extra_hr = $prod_resource_smv_adj_array[47]['02-Mar-2021'][1]['total_smv']."<br>";
						  //echo $line_wise_style_count_arr[$pr_date][$f_id][$l_id]."Y<br>";
						if($line_wise_style_count_arr[$pr_date][$f_id][$l_id]>1)
						{
							$mn_power = $prod_resource_smv_adj_array[$l_id][$txtDate][1]['number_of_emp'];
							if($line_wise_style_count_arr2[$pr_date][$f_id][$l_id]>1)
							{
								$late_hr = $prod_resource_smv_adj_array[$l_id][$txtDate][5]['total_smv'];

								
								if($pp==0)
								{
									$efficiency_min -= $late_hr;
									$pp++;
								}
								$line_wise_style_count_arr2[$pr_date][$f_id][$l_id]--;
								// echo $efficiency_min.'='.$adjust_hr."kakku <br>";
							}
							else
							{
								
								$extra_hr = $prod_resource_smv_adj_array[$l_id][$txtDate][1]['total_smv'];
								$lunch_hr = $prod_resource_smv_adj_array[$l_id][$txtDate][2]['total_smv'];
								$sick_hr = $prod_resource_smv_adj_array[$l_id][$txtDate][3]['total_smv'];
								$leave_hr = $prod_resource_smv_adj_array[$l_id][$txtDate][4]['total_smv'];
								$adjust_hr = $extra_hr - ($lunch_hr+$sick_hr+$leave_hr);

								$efficiency_min += $adjust_hr;
								//  echo $efficiency_min.'='.$adjust_hr."kakku <br>";
								
							}

						}
						else // for single line
						{
							$extra_hr = $prod_resource_smv_adj_array[$l_id][$txtDate][1]['total_smv'];
							$lunch_hr = $prod_resource_smv_adj_array[$l_id][$txtDate][2]['total_smv'];
							$sick_hr = $prod_resource_smv_adj_array[$l_id][$txtDate][3]['total_smv'];
							$leave_hr = $prod_resource_smv_adj_array[$l_id][$txtDate][4]['total_smv'];
							$adjust_hr = $extra_hr - ($lunch_hr+$sick_hr+$leave_hr);

							$efficiency_min += $adjust_hr;
							// echo $efficiency_min.'='.$adjust_hr."TTT <br>";
						}
						$style_wise_avilable_minArr[$style_key]+=$efficiency_min;
						
						
						
				} //=======**********End
			}
		}
	}
	}//===Prod Date End loop
	//echo $efficiency_min.'T=';
 	//print_r($style_wise_avilable_minArr);
	
		
	
	//=====================End=====================================
		
		  $sql_prod=" SELECT  d.production_date,d.po_break_down_id as po_id,
		(case when   d.production_date<'$start_date' and  d.production_type in(1)  then e.production_qnty end) as opening_cut_prod_qty,
		(case when   $prod_date_cond and  d.production_type in(1) then e.production_qnty end) as cut_prod_qty,
		(case when   d.production_date<'$start_date' and  d.production_type in(2) and  d.embel_name=1  then e.production_qnty end) as opening_emb_issue_prod_qty,
		
		(case when   d.production_date<'$start_date' and  d.production_type in(3) and  d.embel_name=1  then e.production_qnty end) as opening_print_issue_prod_qty,
		(case when   $prod_date_cond and  d.production_type in(2) and d.embel_name=1 then e.production_qnty end) as emb_issue_prod_qty,
		(case when   $prod_date_cond and  d.production_type in(3) and d.embel_name=1 then e.production_qnty end) as emb_print_recv_prod_qty,
		(case when   $prod_date_cond and  d.production_type in(3) and d.embel_name=2 then e.production_qnty end) as embroi_recv_prod_qty,
		(case when   d.production_date<'$start_date' and  d.production_type in(3) and  d.embel_name=2  then e.production_qnty end) as opening_embroidery_recv_prod_qty,
		
		(case when   d.production_date<'$start_date' and  d.production_type in(2) and  d.embel_name=2  then e.production_qnty end) as opening_embroidery_prod_qty,
		(case when   $prod_date_cond and  d.production_type in(2) and d.embel_name=2 then e.production_qnty end) as embroidery_prod_qty,
		(case when   d.production_date<'$start_date' and  d.production_type in(3) and  d.embel_name=3  then e.production_qnty end) as opening_wash_recv_prod_qty,
		(case when   d.production_date<'$start_date' and  d.production_type in(2) and  d.embel_name=3  then e.production_qnty end) as opening_wash_issue_prod_qty,
		(case when   $prod_date_cond and  d.production_type in(2) and d.embel_name=3 then e.production_qnty end) as wash_issue_prod_qty,
		(case when   $prod_date_cond and  d.production_type in(3) and d.embel_name=3 then e.production_qnty end) as wash_recv_prod_qty,
		(case when   d.production_date<'$start_date' and  d.production_type in(8)  then e.production_qnty end) as opening_fin_prod_qty,
		(case when   $prod_date_cond and  d.production_type in(8) then e.production_qnty end) as fin_prod_qty,
		(case when   d.production_date<'$start_date' and  d.production_type in(5)  then e.production_qnty end) as opening_sew_out_prod_qty,
		(case when   $prod_date_cond and  d.production_type in(4) then e.production_qnty end) as sew_in_prod_qty,
		(case when   $prod_date_cond and  d.production_type in(5) then e.production_qnty end) as sew_out_prod_qty,
		(case when   d.production_date<'$start_date' and  d.production_type in(4)  then e.production_qnty end) as opening_sew_in_prod_qty
		from pro_garments_production_mst d,pro_garments_production_dtls e  where  d.id=e.mst_id and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and d.production_type in(1,2,3,4,5,8) and e.production_type in(1,2,3,4,5,8)   $po_id_cond_prod";		
		//  echo $sql_prod;
		$sql_res_prod = sql_select($sql_prod);
		
		foreach ($sql_res_prod as $val) 
		{ 
		//$opening_cuting_qc_data_array[$val['PO_ID']]['opening_cut_qty']+= $val['OPENING_CUT_PROD_QTY'];
		$cuting_qc_data_array[$val['PO_ID']]['cut_qty'] +=$val['CUT_PROD_QTY'];
		$opening_emb_issue_data_array[$val['PO_ID']]['opening_issue_print_qty']+= $val['OPENING_EMB_ISSUE_PROD_QTY']-$val['OPENING_PRINT_ISSUE_PROD_QTY'];
		$emb_issue_data_array[$val['PO_ID']]['print_issue_qty'] +=$val['EMB_ISSUE_PROD_QTY'];
		$emb_issue_data_array[$val['PO_ID']]['print_recv_qty'] +=$val['EMB_PRINT_RECV_PROD_QTY'];
		$emb_issue_data_array[$val['PO_ID']]['embroi_recv_prod_qty'] +=$val['EMBROI_RECV_PROD_QTY'];
		$opening_embroidery_issue_data_array[$val['PO_ID']]['opening_embro_issue_qty']+= $val['OPENING_EMBROIDERY_PROD_QTY']- $val['OPENING_EMBROIDERY_RECV_PROD_QTY'];//OPENING_EMBROIDERY_RECV_PROD_QTY
		$emb_embroidery_data_array[$val['PO_ID']]['embro_issue_qty'] +=$val['EMBROIDERY_PROD_QTY'];
		//$opening_sew_data_array[$val['PO_ID']]['opening_sew_in_qty']+= $val['OPENING_SEW_IN_PROD_QTY'];
		//echo $val['OPENING_SEW_IN_PROD_QTY'].'='.$val['OPENING_SEW_OUT_PROD_QTY'].'<br>';
		$sew_data_array[$val['PO_ID']]['sew_in_qty'] +=$val['SEW_IN_PROD_QTY'];
		$sew_data_array[$val['PO_ID']]['sew_out_qty'] +=$val['SEW_OUT_PROD_QTY'];
		$opening_sew_data_array[$val['PO_ID']]['opening_sew_in_qty'] +=$val['OPENING_SEW_IN_PROD_QTY']-$val['OPENING_SEW_OUT_PROD_QTY'];
		$opening_wash_data_array[$val['PO_ID']]['opening_issue_wash_qty']+= $val['OPENING_WASH_ISSUE_PROD_QTY']-$val['OPENING_WASH_RECV_PROD_QTY'];
		$wash_data_array[$val['PO_ID']]['issue_wash_qty'] +=$val['WASH_ISSUE_PROD_QTY'];
		$wash_data_array[$val['PO_ID']]['recv_wash_qty'] +=$val['WASH_RECV_PROD_QTY'];
		
		if($val['OPENING_WASH_RECV_PROD_QTY']) $opeing_wash_sew_out=$val['OPENING_WASH_RECV_PROD_QTY'];
		else $opeing_wash_sew_out=$val['OPENING_SEW_OUT_PROD_QTY'];
		
		if($val['WASH_RECV_PROD_QTY']) $wash_sew_out=$val['WASH_RECV_PROD_QTY'];
		else $wash_sew_out=$val['SEW_OUT_PROD_QTY'];
		//echo $val['WASH_RECV_PROD_QTY'].'='.$val['SEW_OUT_PROD_QTY'].'<br>';
		if($val['FIN_PROD_QTY'])
		{
	//	echo $val['FIN_PROD_QTY'].'<br>';
		}
		
		$opening_fin_data_array[$val['PO_ID']]['opening_fin_qty']+=$opeing_wash_sew_out-$val['OPENING_FIN_PROD_QTY'];
		$opening_ex_qty=$exfact_data_array[$val['PO_ID']]['opening_ex_qty'];
		$opening_fin_data_array[$val['PO_ID']]['opening_goods_fin_qty']+=$val['OPENING_FIN_PROD_QTY'];
		if($val['FIN_PROD_QTY'])
		{
		$fin_data_array[$val['PO_ID']]['fin_qty'] +=$val['FIN_PROD_QTY'];
		}
		//$fin_data_array[$val['PO_ID']]['fin_sew_qty'] +=$wash_sew_out;
		
		$job_no=$prod_data_arr[$val['PO_ID']]['job_no'];
		$style_wiseSewingOutArr[$job_no]+=$val['SEW_OUT_PROD_QTY'];
		//echo $val['FIN_PROD_QTY'].'d';
		}
		unset($sql_res_prod);
					//==========================
	
		//=============Summary part for Job Wise================================
		$gbl_rate_arr=array();$summary_prod_wip_wipArr=array();$gbl_rate3_arr=array();$gbl_rate3_arr=array();$gbl_rate4_arr=array();$gbl_rate5_arr=array();$gbl_rate6_arr=array();
		foreach($job_qty_data_arr as $job_key=>$row) 
		{
	 
			 if ($ff%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			 
			   $fab_wgt=0; $cutting_used_fab_wgt=0; $opening_used_fab_wgt=0;$opening_used_fab_val=0;
			  $fab_wgt=$Cut_lay_data_arr[$job_key]['fab_wgt'];
			  $opening_layQty=$Cut_lay_data_arr[$job_key]['opening_fab_wgt'];
			
			 // $job_qty=$job_qty_data_arr[$job_key]['job_qty'];
			  $job_qty=$job_data_for_lay_arr[$job_key]['job_qty'];
			  if($fab_wgt>0)
			  {
			  $cutting_used_fab_wgt=($fab_wgt/$job_qty)*$row['job_qty'];
			  }
			  $opening_used_fab_wgt=0;
			  if($opening_layQty>0)
			  {
				   // echo  $opening_layQty.'T';
			  $opening_used_fab_wgt=($opening_layQty/$job_qty)*$row['job_qty'];
			  }
			 
			 //$opening_fin_issue=$opening_fin_issue_data_array[$val['PO_ID']]['fin_qty'];
			 
			   $po_ids=rtrim($row['po_ids'],',');
			   $po_idArr= array_unique(explode(",",$po_ids)); 
			   $po_qty=$po_value=$po_wise_cm_cost=$opening_fin_rcv=$fin_rcv=$fin_rcv_val=$tot_fab_fin_req_qty=$tot_fab_fin_req_qty=$opening_fin_val=0;
			   $shipDate="";
			  foreach($po_idArr as $poId)
			  {
			   //$po_value+= $prod_data_arr[$poId]['po_value'];			
				//$po_qty+=$prod_data_arr[$poId]['po_qty'];	
				//$po_wise_cm_cost+=$po_wise_cm_cost_arr[$poId];
				$opening_fin_rcv+=$opening_fin_rcv_data_array[$poId]['fin_qty'];
				$opening_fin_val+=$opening_fin_rcv_data_array[$poId]['fin_val'];
				$fin_rcv+=$fin_rcv_data_array[$poId]['fin_qty'];
				$fin_rcv_val+=$fin_rcv_data_array[$poId]['fin_val'];
				
				 $tot_fab_fin_req_qty+=$reqQtyAmtArr[$row['job_id']][$poId]['prodfin_qty']+$reqQtyAmtArr[$row['job_id']][$poId]['purchfin_qty'];
				 $tot_fab_fin_req_val+=$reqQtyAmtArr[$row['job_id']][$poId]['prodfin_amt']+$reqQtyAmtArr[$row['job_id']][$poId]['purchfin_amt'];
			  }
			  //echo $shipDate.'D';
			  
				  if($fin_rcv_val>0)
				  {
					 $fin_rcv_avg_rate=$fin_rcv_val/$fin_rcv; 
				  }
			  
				 if($fin_rcv_avg_rate>0)
				  {
				  $cutting_used_fab_val= $cutting_used_fab_wgt*$fin_rcv_avg_rate;
				  }
				  else $cutting_used_fab_val=0;
				  
				  if($fin_rcv_avg_rate>0)
				  {
				  $opening_used_fab_val= $opening_used_fab_wgt*$fin_rcv_avg_rate;
				  }
				  else $opening_used_fab_val=0;
				  
			 $fab_clsoing_qty=(($opening_fin_rcv-$opening_used_fab_wgt)+$fin_rcv)-$cutting_used_fab_wgt;
			 $fab_clsoing_val=(($opening_fin_val-$opening_used_fab_val)+$fin_rcv_val)-$cutting_used_fab_val;
			 $fab_clsoing_qty=(($opening_fin_rcv-$opening_used_fab_wgt)+$fin_rcv)-$cutting_used_fab_wgt;
			 $fab_clsoing_val=(($opening_fin_val-$opening_used_fab_val)+$fin_rcv_val)-$cutting_used_fab_val;
			
			$summary_prod_wip_wipArr[1]['cutting_stock_opening_qty']+=$opening_fin_rcv-$opening_used_fab_wgt;
			$summary_prod_wip_wipArr[1]['cutting_stock_opening_val']+=$opening_fin_val-$opening_used_fab_val;
			$summary_prod_wip_wipArr[1]['cutting_stock_recv_qty']+=$fin_rcv;
			$summary_prod_wip_wipArr[1]['cutting_stock_recv_val']+=$fin_rcv_val;
			$summary_prod_wip_wipArr[1]['cutting_stock_issue_qty']+=$cutting_used_fab_wgt;
			$summary_prod_wip_wipArr[1]['cutting_stock_issue_val']+=$cutting_used_fab_val;
			$summary_prod_wip_wipArr[1]['cutting_stock_clsoing_qty']+=$fab_clsoing_qty;
			$summary_prod_wip_wipArr[1]['cutting_stock_clsoing_val']+=$fab_clsoing_val;
}
// Summary For Cutting Stock (Finished Fabric) WIP

		
		foreach($prod_data_arr as $poId=>$row)
		{
			//=========Cutting Stock (Finished Fabric)============== 
			/* $opening_used_fab_wgt=0; $fin_rcv_avg_rate=0;
			 $fab_wgt=$Cut_lay_data_arr[$row['job_no']]['fab_wgt'];
			  $opening_layQty=$Cut_lay_data_arr[$row['job_no']]['opening_fab_wgt'];
			  $job_qty=$job_data_for_lay_arr[$row['job_no']]['job_qty'];
			//  echo $fab_wgt.'='.$job_qty.'<br>';
			  
			  if($fab_wgt>0)
			  {
			  $cutting_used_fab_wgt= ($fab_wgt/$job_qty)*$row['po_qty'];
			  }
			  
			   if($opening_layQty>0)
			   {
			   $opening_used_fab_wgt= ($opening_layQty/$job_qty)*$row['po_qty'];
			   }
			   if($fin_rcv_data_array[$poId]['fin_val']>0)
			   {
			    $fin_rcv_avg_rate=$fin_rcv_data_array[$poId]['fin_val']/$fin_rcv_data_array[$poId]['fin_qty'];
			   }
			  // echo $cutting_used_fab_wgt.'='.$fin_rcv_avg_rate.'<br>';
			  if($fin_rcv_avg_rate>0)
			  {
			  $cutting_used_fab_val= $cutting_used_fab_wgt*$fin_rcv_avg_rate;
			  }
			  else $cutting_used_fab_val=0;
			  
			   if($fin_rcv_avg_rate>0)
			  {
			  $opening_used_fab_val= $opening_used_fab_wgt*$fin_rcv_avg_rate;
			  }
			  else $opening_used_fab_val=0;
			 // echo $cutting_used_fab_wgt.'TT';
			  $tot_fab_fin_req_qty=$reqQtyAmtArr[$row['job_id']][$poId]['prodfin_qty']+$reqQtyAmtArr[$row['job_id']][$poId]['purchfin_qty'];
			  $tot_fab_fin_req_val=$reqQtyAmtArr[$row['job_id']][$poId]['prodfin_amt']+$reqQtyAmtArr[$row['job_id']][$poId]['purchfin_amt'];
							   
			 $fab_clsoing_qty=(($opening_fin_rcv_data_array[$poId]['fin_qty']-$opening_used_fab_wgt)+$fin_rcv_data_array[$poId]['fin_qty'])-$cutting_used_fab_wgt;
			 $fab_clsoing_val=(($opening_fin_rcv_data_array[$poId]['fin_val']-$opening_used_fab_val)+$fin_rcv_data_array[$poId]['fin_val'])-$cutting_used_fab_val;
			$summary_prod_wip_wipArr[1]['cutting_stock_opening_qty']+=$opening_fin_rcv_data_array[$poId]['fin_qty']-$opening_used_fab_wgt;
			$summary_prod_wip_wipArr[1]['cutting_stock_opening_val']+=$opening_fin_rcv_data_array[$poId]['fin_val']-$opening_used_fab_val;
			$summary_prod_wip_wipArr[1]['cutting_stock_recv_qty']+=$fin_rcv_data_array[$poId]['fin_qty'];
			$summary_prod_wip_wipArr[1]['cutting_stock_recv_val']+=$fin_rcv_data_array[$poId]['fin_val'];
			$summary_prod_wip_wipArr[1]['cutting_stock_issue_qty']+=$cutting_used_fab_wgt;
			$summary_prod_wip_wipArr[1]['cutting_stock_issue_val']+=$cutting_used_fab_val;
			$summary_prod_wip_wipArr[1]['cutting_stock_clsoing_qty']+=$fab_clsoing_qty;
			$summary_prod_wip_wipArr[1]['cutting_stock_clsoing_val']+=$fab_clsoing_val;*/
			//=========End Cutting Stock (Finished Fabric)==============
			//==============Cutting Stock (Cut Pannel)=========
			$opening_sew_in_sum=$opening_sew_data_array[$poId]['opening_sew_in_qty'];
			 $opening_cut_qty=$opening_cuting_qc_data_array[$poId]['opening_cut_qty']-$opening_sew_in_sum;
			  $job_qty=$job_qty_data_arr[$row['job_no']]['job_qty'];
			  $cuting_qcQty= $cuting_qc_data_array[$poId]['cut_qty'];
			 //  echo $row['job_id'].'='.$poId.',';
			   $grey_fab_avg_rate=0;$fab_rate=0;  $grey_fab_Qty=0;$grey_fab_amt=0;$avg_fab_cons=0; $job_ex_rate=0; $cut_panel_rate=0;
			 $grey_fab_Qty=$fab_wo_data_array[$poId]['grey_qty'];
			 $grey_fab_amt=$fab_wo_data_array[$poId]['grey_amt'];
			
			  if($grey_fab_amt>0)
			  {
				 $grey_fab_avg_rate=$grey_fab_amt/$grey_fab_Qty;
			  }
			 $job_ex_rate=$job_qty_data_arr[$row['job_no']]['ex_rate'];
			// echo $grey_fab_avg_rate.'d';
			
			//echo $fab_rate.'d';
							 
			 $tot_fab_req_qty=$reqQtyAmtArr[$row['job_id']][$poId]['prodfin_qty']+$reqQtyAmtArr[$row['job_id']][$poId]['purchfin_qty'];
			 $tot_fab_req_val=$reqQtyAmtArr[$row['job_id']][$poId]['prodfin_amt']+$reqQtyAmtArr[$row['job_id']][$poId]['purchfin_amt'];
			 if($tot_fab_req_qty>0)
			 {
			 $avg_fab_cons=$tot_fab_req_qty/$row['po_qty'];
			 }
			 $currency_id=$fab_wo_data_array[$poId]['fab_wo_currency'];
			if($currency_id==2 && $grey_fab_avg_rate>0) //USD
			{
				$fab_rate=$grey_fab_avg_rate*$job_ex_rate;
			}
			else
			{
				$fab_rate=$grey_fab_avg_rate;
			}
			//echo $fab_rate.'='.$avg_fab_cons.'<br>';
			
			  $cut_panel_rate=$fab_rate*$avg_fab_cons;
			  $cutting_in_hand=($opening_cut_qty+$cuting_qcQty)-$sewing_inputQty_arr[$poId]['sew_in'];
			  if($cut_panel_rate>0)
			  {
			  $gbl_rate_arr[$poId]=$cut_panel_rate;
			  }
			$summary_prod_wip_wipArr[2]['cutting_stock_opening_qty']+=$opening_cut_qty;
			if($opening_cut_qty>0 && $cut_panel_rate>0)
			{
			$summary_prod_wip_wipArr[2]['cutting_stock_opening_val']+=$opening_cut_qty*$cut_panel_rate;
			}
			$summary_prod_wip_wipArr[2]['cutting_stock_recv_qty']+=$cuting_qcQty;
			if($cuting_qcQty>0 && $cut_panel_rate>0)
			{
			$summary_prod_wip_wipArr[2]['cutting_stock_recv_val']+=$cuting_qcQty*$cut_panel_rate;
			}
			$summary_prod_wip_wipArr[2]['cutting_stock_issue_qty']+=$sewing_inputQty_arr[$poId]['sew_in'];
			if($sewing_inputQty_arr[$poId]['sew_in']>0 && $cut_panel_rate>0)
			{
			$summary_prod_wip_wipArr[2]['cutting_stock_issue_val']+=$sewing_inputQty_arr[$poId]['sew_in']*$cut_panel_rate;
			}
			
			$summary_prod_wip_wipArr[2]['cutting_stock_clsoing_qty']+=$cutting_in_hand;
			if($cutting_in_hand>0 && $cut_panel_rate>0)
			{
			$summary_prod_wip_wipArr[2]['cutting_stock_clsoing_val']+=$cutting_in_hand*$cut_panel_rate;
			}
			//==============End Cutting Stock (Cut Pannel)=========
			
			//==============Stock in Sub Contract Printing Factory=========
			 $opening_issue_print_qty=$opening_emb_issue_data_array[$poId]['opening_issue_print_qty'];
			$job_qty=$job_qty_data_arr[$row['job_no']]['job_qty'];
			$cuting_qcQty= $cuting_qc_data_array[$poId]['cut_qty'];
			$gbl_rate=$gbl_rate_arr[$poId];
			$print_rate= $embl_print_data_array[$poId]['print_rate']+$gbl_rate;
			$gbl_rate2_arr[$poId]=$print_rate;
			//  echo $gbl_rate.'<br>';
			$print_issue_qty= $emb_issue_data_array[$poId]['print_issue_qty'];
			$print_recv_qty= $emb_issue_data_array[$poId]['print_recv_qty'];
			$emb_stock_qty= ($opening_issue_print_qty+$print_issue_qty)-$print_recv_qty;
			$summary_prod_wip_wipArr[3]['cutting_stock_opening_qty']+=$opening_issue_print_qty;
			if($opening_issue_print_qty>0 && $print_rate>0)
			{
			$summary_prod_wip_wipArr[3]['cutting_stock_opening_val']+=$opening_issue_print_qty*$print_rate;
			}
			$summary_prod_wip_wipArr[3]['cutting_stock_recv_qty']+=$print_recv_qty;
			if($print_recv_qty>0 && $print_rate>0)
			{
			$summary_prod_wip_wipArr[3]['cutting_stock_recv_val']+=$print_recv_qty*$print_rate;
			}
			$summary_prod_wip_wipArr[3]['cutting_stock_issue_qty']+=$print_issue_qty;
			if($print_issue_qty>0 && $print_rate>0)
			{
			$summary_prod_wip_wipArr[3]['cutting_stock_issue_val']+=$print_issue_qty*$print_rate;
			}
			$summary_prod_wip_wipArr[3]['cutting_stock_clsoing_qty']+=$emb_stock_qty;
			if($emb_stock_qty>0 && $print_rate>0)
			{
			$summary_prod_wip_wipArr[3]['cutting_stock_clsoing_val']+=$emb_stock_qty*$print_rate;
			}
			//==============End Stock in Sub Contract Printing Factory=========
			//==============Stock in  Sub Contract Embroidery Factory=========
			 $opening_embro_issue_qty=$opening_embroidery_issue_data_array[$poId]['opening_embro_issue_qty'];
			// $job_qty=$job_qty_data_arr[$row['job_no']]['job_qty'];
			$gbl_rate2=0;$embroi_recv_prod_qty=0;
			 $cuting_qcQty= $cuting_qc_data_array[$poId]['cut_qty'];
			 $gbl_rate2= $gbl_rate2_arr[$poId];
			 $embro_rate= $embl_embroidery_data_array[$poId]['embro_rate']+$gbl_rate2;
			 $gbl_rate3_arr[$poId]=$embro_rate;
			   // echo $embl_embroidery_data_array[$poId]['embro_rate'].'='.$gbl_rate2.'<br>';
			$embro_issue_qty= $emb_embroidery_data_array[$poId]['embro_issue_qty'];
			$embroi_recv_prod_qty=$emb_issue_data_array[$poId]['embroi_recv_prod_qty'];
			$emb_stock_qty= ($opening_embro_issue_qty+$embro_issue_qty)-$embroi_recv_prod_qty;
			$summary_prod_wip_wipArr[4]['cutting_stock_opening_qty']+=$opening_embro_issue_qty;
			if($opening_embro_issue_qty>0 && $embro_rate>0)
			{
			$summary_prod_wip_wipArr[4]['cutting_stock_opening_val']+=$opening_embro_issue_qty*$embro_rate;
			}
			$summary_prod_wip_wipArr[4]['cutting_stock_recv_qty']+=$embroi_recv_prod_qty;
			if($embroi_recv_prod_qty>0 && $embro_rate>0)
			{
			$summary_prod_wip_wipArr[4]['cutting_stock_recv_val']+=$embroi_recv_prod_qty*$embro_rate;
			}
			$summary_prod_wip_wipArr[4]['cutting_stock_issue_qty']+=$embro_issue_qty;
			if($embro_issue_qty>0 && $embro_rate>0)
			{
			$summary_prod_wip_wipArr[4]['cutting_stock_issue_val']+=$embro_issue_qty*$embro_rate;
			}
			$summary_prod_wip_wipArr[4]['cutting_stock_clsoing_qty']+=$emb_stock_qty;
			if($emb_stock_qty>0 && $embro_rate>0)
			{
			$summary_prod_wip_wipArr[4]['cutting_stock_clsoing_val']+=$emb_stock_qty*$embro_rate;
			}
			//==============End Stock in  Sub Contract Embroidery Factory=========
			//=========Sewing Stock=================
			$gbl_rate3=0;$sew_amt=0;$trim_rate_sew=0;$ex_rate=0;$sew_qty=0;$style_wiseSewingOut=0;$avg_avilable_min=0;$avg_avilable_min=0;$sum_sew_rate=0;	$sew_stock_qty=0;$opening_sew_in_qty=0;$currency_id=0;$job_qty=0;
			$opening_sew_in_qty=$opening_sew_data_array[$poId]['opening_sew_in_qty'];
			$job_qty=$job_qty_data_arr[$row['job_no']]['job_qty'];
			$cuting_qcQty= $cuting_qc_data_array[$poId]['cut_qty'];
			
			$gbl_rate3= $gbl_rate3_arr[$poId];
			// echo $trim_wo_data_array[$poId]['sew_rate'].'d';
			$sew_in_qty=$sew_out_qty=$trim_rate_sew=$sew_rate=$sew_amt=$sew_qty=0;
			$sew_in_qty=$sew_data_array[$poId]['sew_in_qty'];
			$sew_out_qty=$sew_data_array[$poId]['sew_out_qty'];
			$ex_rate=$job_qty_data_arr[$row['job_no']]['ex_rate'];
			$currency_id=$trim_wo_data_array[$poId]['currency_id'];
			$sew_amt=$trim_wo_data_array[$poId]['sew_amt'];
			$sew_qty=$trim_wo_data_array[$poId]['sew_qty'];
			//$trim_rate_sew=$sew_amt/$sew_qty;
			if($currency_id==2 && $sew_amt>0)
			{
				$sum_trim_rate_sew=($sew_amt/$sew_qty)*$ex_rate;
			}
			else {
				$sum_trim_rate_sew=$sew_amt/$sew_qty;
				}
			//	echo $sew_amt.'='. $sew_qty.'A<br>';
			$style_wiseSewingOut=$style_wiseSewingOutArr[$row['job_no']];
			if($style_wiseSewingOut>0)
			{
			$avg_avilable_min=($style_wise_avilable_minArr[$row['style']]/$style_wiseSewingOut)*$sew_out_qty;
			}
			//echo $avg_avilable_min.'d';
				if($sew_out_qty>0)
				{
		 		$used_min=$avg_avilable_min/$sew_out_qty;
				}
			
			//$used_min=$po_wise_used_minArr[$poId]/$sew_out_qty;
			if($gbl_rate3>0 && $sum_trim_rate_sew>0)
			{
			$sum_sew_rate=($asking_avg_rate*$used_min)+$sum_trim_rate_sew+$gbl_rate3;
			}
							 
			//$sew_rate= $cost_per_minute+$trim_wo_data_array[$poId]['sew_rate']+$gbl_rate3;
			$gbl_rate4_arr[$poId]=$sum_sew_rate;
		
			$sew_stock_qty= ($opening_sew_in_qty+$sew_in_qty)-$sew_out_qty;
			// echo $sew_in_qty.'='.$sew_out_qty.'='.$opening_sew_in_qty.'<br>';
			
			$summary_prod_wip_wipArr[5]['cutting_stock_opening_qty']+=$opening_sew_in_qty;
			if($opening_sew_in_qty>0 && $sum_sew_rate>0)
			{
				//echo $opening_sew_in_qty.'='.$sum_sew_rate.'<br>';
			$summary_prod_wip_wipArr[5]['cutting_stock_opening_val']+=$opening_sew_in_qty*$sum_sew_rate;
			}
			$summary_prod_wip_wipArr[5]['cutting_stock_recv_qty']+=$sew_in_qty;
			if($sew_in_qty>0 && $sum_sew_rate>0)
			{
			$summary_prod_wip_wipArr[5]['cutting_stock_recv_val']+=$sew_in_qty*$sum_sew_rate;
			}
			$summary_prod_wip_wipArr[5]['cutting_stock_issue_qty']+=$sew_out_qty;
			if($sew_out_qty>0 && $sum_sew_rate>0)
			{
			$summary_prod_wip_wipArr[5]['cutting_stock_issue_val']+=$sew_out_qty*$sum_sew_rate;
			}
			$summary_prod_wip_wipArr[5]['cutting_stock_clsoing_qty']+=$sew_stock_qty;
			if($sew_stock_qty>0 && $sum_sew_rate>0)
			{
			//	echo $sew_stock_qty.'='.$sew_rate.'<br>';
			$summary_prod_wip_wipArr[5]['cutting_stock_clsoing_val']+=$sew_stock_qty*$sum_sew_rate;
			}
			//=========End Sewing Stock=================
			//==========Stock in Sub Contract Washing Factory====================
			$wash_rate=$sew_out_qty=$job_qty=$sew_in_qty=$sew_out_qty=0;
			
			$opening_issue_wash_qty=$opening_wash_data_array[$poId]['opening_issue_wash_qty'];
			$issue_wash_qty=$wash_data_array[$poId]['issue_wash_qty']; 
			$recv_wash_qty=$wash_data_array[$poId]['recv_wash_qty'];
			$job_qty=$job_qty_data_arr[$row['job_no']]['job_qty'];
			$cuting_qcQty= $cuting_qc_data_array[$poId]['cut_qty'];
			$gbl_rate4= $gbl_rate4_arr[$poId];
			$wash_rate= $embl_embroidery_data_array[$poId]['wash_rate']+$gbl_rate4;
			$gbl_rate5_arr[$poId]=$wash_rate;
		
			$sew_in_qty=$sew_data_array[$poId]['sew_in_qty'];
			$sew_out_qty=$sew_data_array[$poId]['sew_out_qty'];
			$wash_stock_qty= ($opening_issue_wash_qty+$issue_wash_qty)-$recv_wash_qty;
			
			$summary_prod_wip_wipArr[6]['cutting_stock_opening_qty']+=$opening_issue_wash_qty;
			if($opening_issue_wash_qty>0 && $wash_rate>0)
			{
			$summary_prod_wip_wipArr[6]['cutting_stock_opening_val']+=$opening_issue_wash_qty*$wash_rate;
			}
			$summary_prod_wip_wipArr[6]['cutting_stock_recv_qty']+=$recv_wash_qty;
			if($recv_wash_qty>0 && $wash_rate>0)
			{
			$summary_prod_wip_wipArr[6]['cutting_stock_recv_val']+=$recv_wash_qty*$wash_rate;
			}
			$summary_prod_wip_wipArr[6]['cutting_stock_issue_qty']+=$issue_wash_qty;
			if($issue_wash_qty>0 && $wash_rate>0)
			{
			$summary_prod_wip_wipArr[6]['cutting_stock_issue_val']+=$issue_wash_qty*$wash_rate;
			}
			$summary_prod_wip_wipArr[6]['cutting_stock_clsoing_qty']+=$wash_stock_qty;
			if($wash_stock_qty>0 && $wash_rate>0)
			{
				//echo $wash_stock_qty.'='.$wash_rate.'<br>';
			$summary_prod_wip_wipArr[6]['cutting_stock_clsoing_val']+=$wash_stock_qty*$wash_rate;
			}
			//==========end Stock in Sub Contract Washing Factory====================
			//==========Gmts Finishing Stock====================
			$fin_rate=0;$gbl_rate5=0;$wo_fin_amt=0;$wo_fin_qty=0;$trim_rate_fin=0;$currency_id=0;$ex_rate=0;$job_qty=0;
			
			$opening_fin_qty=$opening_fin_data_array[$poId]['opening_fin_qty'];
			$fin_qty=$fin_data_array[$poId]['fin_qty']; 
			$recv_wash_qty=$wash_data_array[$poId]['recv_wash_qty'];
			$sew_out_qty=$wash_data_array[$poId]['sew_out_qty'];
			if($recv_wash_qty) $wash_sew_qty=$recv_wash_qty;else $wash_sew_qty=$sew_out_qty;
			//$wash_data_array[$val['PO_ID']]['recv_wash_qty']
			//$sew_data_array[$val['PO_ID']]['sew_out_qty']
			$fin_sew_out_qty=$wash_sew_qty;//$fin_data_array[$poId]['fin_sew_qty'];
			$job_qty=$job_qty_data_arr[$row['job_no']]['job_qty'];
			$cuting_qcQty= $cuting_qc_data_array[$poId]['cut_qty'];
			$ex_rate=$job_qty_data_arr[$row['job_no']]['ex_rate'];
			$gbl_rate5= $gbl_rate5_arr[$poId];
			$wo_fin_amt=$trim_wo_data_array[$poId]['fin_amt'];
			$wo_fin_qty=$trim_wo_data_array[$poId]['fin_qty'];
			$currency_id=$trim_wo_data_array[$poId]['currency_id'];
			if($currency_id==2) 
			{
				$trim_rate_fin=($wo_fin_amt/$wo_fin_qty)*$ex_rate;
			}
			else {
				if($wo_fin_amt>0)
					{
					$trim_rate_fin=$wo_fin_amt/$wo_fin_qty;
					}
				}
			
			$fin_rate= $trim_rate_fin+$gbl_rate5;
			$gbl_rate6_arr[$poId]=$fin_rate;
			//  echo $gbl_rate5.'='.$trim_wo_data_array[$poId]['fin_rate'].'<br>';
			// $opening_sew_in_qty=$opening_sew_data_array[$val['PO_ID']]['opening_sew_in_qty'];
			$sew_in_qty=$sew_out_qty=0;
			$sew_in_qty=$sew_data_array[$poId]['sew_in_qty'];
			$sew_out_qty=$sew_data_array[$poId]['sew_out_qty'];
			$fin_stock_qty= ($opening_fin_qty+$fin_sew_out_qty)-$fin_qty; 
			$summary_prod_wip_wipArr[7]['cutting_stock_opening_qty']+=$opening_fin_qty;
			if($opening_fin_qty>0  && $fin_rate>0)
			{
			$summary_prod_wip_wipArr[7]['cutting_stock_opening_val']+=$opening_fin_qty*$fin_rate;
			}
			$summary_prod_wip_wipArr[7]['cutting_stock_recv_qty']+=$fin_sew_out_qty;
			if($fin_sew_out_qty>0 && $fin_rate>0)
			{
			$summary_prod_wip_wipArr[7]['cutting_stock_recv_val']+=$fin_sew_out_qty*$fin_rate;
			}
			$summary_prod_wip_wipArr[7]['cutting_stock_issue_qty']+=$fin_qty;
			if($fin_qty>0 && $fin_rate>0)
			{
			$summary_prod_wip_wipArr[7]['cutting_stock_issue_val']+=$fin_qty*$fin_rate;
			}
			$summary_prod_wip_wipArr[7]['cutting_stock_clsoing_qty']+=$fin_stock_qty;
			if($fin_stock_qty>0 && $fin_rate>0)
			{
				//echo $fin_stock_qty.'='.$fin_rate.'<br>';
			$summary_prod_wip_wipArr[7]['cutting_stock_clsoing_val']+=$fin_stock_qty*$fin_rate;
			}
			//==========End Gmts Finishing Stock====================
			//========== Gmts Finished Goods====================
			$ex_rate=0;$gfin_rate=0;$gbl_rate7=0;$wo_gfin_amt=0;$wo_gfin_qty=0;$sew_in_qty=$sew_out_qty=0;$job_qty=0;$currency_id=0;
			$opening_fin_qty=$opening_fin_data_array[$poId]['opening_goods_fin_qty'];
			$exfact_fin_qty=$exfact_data_array[$poId]['opening_ex_qty'];
			$opening_fin_bal_qty=$opening_fin_qty-$exfact_fin_qty;
			$fin_qty=$fin_data_array[$poId]['fin_qty']; 
			$exfact_qty=$exfact_data_array[$poId]['ex_qty'];
			$job_qty=$job_qty_data_arr[$row['job_no']]['job_qty'];
			$cuting_qcQty= $cuting_qc_data_array[$poId]['cut_qty'];
			$gbl_rate7= $gbl_rate6_arr[$poId];
			
			$ex_rate=$job_qty_data_arr[$row['job_no']]['ex_rate'];
			$currency_id=$trim_wo_data_array[$poId]['currency_id'];
			$wo_gfin_amt=$trim_wo_data_array[$poId]['fin_amt'];
			$wo_gfin_qty=$trim_wo_data_array[$poId]['fin_qty'];
			//$trim_rate_sew=$fin_amt/$fin_qty;
			if($currency_id==2)
			{
				$trim_rate_fin=($wo_gfin_amt/$wo_gfin_qty)*$ex_rate;
			}
			else {
				$trim_rate_fin=$wo_gfin_amt/$wo_gfin_qty;
				}
				
			$gfin_rate= $trim_rate_fin+$gbl_rate7;
			$gbl_rate6_arr[$poId]=$gfin_rate;
			//  echo $gbl_rate5.'='.$trim_wo_data_array[$poId]['fin_rate'].'<br>';
			// $opening_sew_in_qty=$opening_sew_data_array[$val['PO_ID']]['opening_sew_in_qty'];
			
			$sew_in_qty=$sew_data_array[$poId]['sew_in_qty'];
			$sew_out_qty=$sew_data_array[$poId]['sew_out_qty'];
			$clsoing_stock_qty= ($opening_fin_bal_qty+$fin_qty)-$exfact_qty;
			$summary_prod_wip_wipArr[8]['cutting_stock_opening_qty']+=$opening_fin_bal_qty;
			if($opening_fin_bal_qty>0 && $gfin_rate>0)
			{
			$summary_prod_wip_wipArr[8]['cutting_stock_opening_val']+=$opening_fin_bal_qty*$gfin_rate;
			}
			$summary_prod_wip_wipArr[8]['cutting_stock_recv_qty']+=$fin_qty;
			if($fin_qty>0 && $gfin_rate>0)
			{
			$summary_prod_wip_wipArr[8]['cutting_stock_recv_val']+=$fin_qty*$gfin_rate;
			}
			$summary_prod_wip_wipArr[8]['cutting_stock_issue_qty']+=$exfact_qty;
			if($exfact_qty>0 && $gfin_rate>0)
			{
			$summary_prod_wip_wipArr[8]['cutting_stock_issue_val']+=$exfact_qty*$gfin_rate;
			}
			if($clsoing_stock_qty>0 && $gfin_rate>0)
			{
			$summary_prod_wip_wipArr[8]['cutting_stock_clsoing_qty']+=$clsoing_stock_qty;
			$summary_prod_wip_wipArr[8]['cutting_stock_clsoing_val']+=$clsoing_stock_qty*$gfin_rate;
			}
			//=====================End=============
		}
		$prodWipTypeArr=array(1=>"Cutting Stock (Finished Fabric)",2=>"Cutting Stock (Cut Pannel)",3=>"Stock in Sub Contract Printing Factory",4=>"Stock in Sub Contract Embroidery Factory",5=>"Sewing Stock",6=>"Stock in Sub Contract Washing Factory",7=>"Gmts Finishing Stock",8=>"Finished Goods");
		$summary_tbl_width=950;
		$tbl_width = 1840;	
		ob_start();
		?>
			<div style="width:<?=$tbl_width;?>px; margin-left:5px;">
				<table width="<?=$tbl_width;?>"  cellspacing="0">
					<tr class="form_caption" style="border:none;">
						<td colspan="15" align="center" style="border:none;font-size:18px; font-weight:bold" ><?=$report_title;?></td>
					</tr>
					<tr class="form_caption" style="border:none;">
						<td colspan="15" align="center" style="border:none;font-size:16px; font-weight:bold" ><?=$company_library[$company_name];?></td>
					</tr>
					<tr class="form_caption" style="border:none;">
						<td colspan="15" align="center" style="border:none;font-size:15px; font-weight:bold" >From <?=$date_from;?> To <?=$date_to;?></td>
					</tr>
				</table>	
                <br>
                <div style="width:<?=$summary_tbl_width;?>px; float:left;">
					<table cellspacing="0" cellpadding="0"  border="1" style="border-collapse: collapse;" rules="all"  width="<?=$summary_tbl_width;?>" class="rpt_table" align="left">
                    <caption> <b style="float:left;font-size:18px;">Summary</b></caption>
						<thead>
							<tr>
                           		<th colspan="4" >&nbsp;</th>								
								<th colspan="2">Openning</th>
                                <th colspan="2">Purchase/Receive</th>		
                                <th colspan="2">Issue</th>		
                                <th colspan="2">Closing</th>								
								 
                            </tr>
                            <tr>
								<th width="30">&nbsp;</th>	
                                <th width="100">&nbsp;</th>	
                                <th width="340">&nbsp;</th>	
                                <th width="100">&nbsp;</th>	
                                
                                <th width="100">Qty</th>								
								<th width="100">Value</th>
                                <th width="100">Qty</th>								
								<th width="100">Value</th>
                                <th width="100">Qty</th>								
								<th width="100">Value</th>
                                <th width="100">Qty</th>								
								<th width="100">Value</th>								
							</tr>								
							</thead>
                            <tbody>
                            <?
							$kk=1;$total_sum_openingQty=$total_sum_openingVal=$total_sum_recvQty=$total_sum_recvVal=$total_sum_issueQty=$total_sum_issueVal=$total_sum_clsoingQty=$total_sum_clsoingVal=0;
                            foreach($summary_prod_wip_wipArr as $type=>$row)
							{
								if ($kk%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
                            
                                  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_summary<? echo $kk; ?>','<? echo $bgcolor; ?>')" id="tr_summary<? echo $kk; ?>">
                                  <td width="30">  <? echo $kk;?></td> 
                                  <td width="150">  <?  if($kk==1) echo "<b>Work in Progress</b>";
											elseif($kk==8) echo "<b>Finished Goods</b>"; 
											else echo "";?> </td>
                                  <td width="340"><b> <? echo $prodWipTypeArr[$type];?></b></td>
                                  <td><?  if($kk==1) echo "All";else echo "Pcs"; ?></td>
                                  <td width="100" align="right"><? echo number_format($row['cutting_stock_opening_qty'],2);?></td>
                                  <td width="100" align="right"> <? echo number_format($row['cutting_stock_opening_val'],2);?></td>
                                  <td width="100" align="right"><? echo number_format( $row['cutting_stock_recv_qty'],2);?></td>
                              	  <td width="100" align="right"><? echo number_format( $row['cutting_stock_recv_val'],2);?></td>
                                  <td width="100" align="right"><? echo number_format( $row['cutting_stock_issue_qty'],2);?></td>
                                  <td width="100" align="right"><? echo number_format( $row['cutting_stock_issue_val'],2);?></td>
                                  <td width="100" align="right"><? echo number_format( $row['cutting_stock_clsoing_qty'],2);?></td>
                                  <td width="100" align="right"><? echo number_format( $row['cutting_stock_clsoing_val'],2);?></td>
                            </tr>
                            <?
							$kk++;
							$total_sum_openingQty+=$row['cutting_stock_opening_qty'];
							$total_sum_openingVal+=$row['cutting_stock_opening_val'];
							$total_sum_recvQty+=$row['cutting_stock_recv_qty'];
							$total_sum_recvVal+=$row['cutting_stock_recv_val'];
							
							$total_sum_issueQty+=$row['cutting_stock_issue_qty'];
							$total_sum_issueVal+=$row['cutting_stock_issue_val'];
							$total_sum_clsoingQty+=$row['cutting_stock_clsoing_qty'];
							
							$total_sum_clsoingVal+=$row['cutting_stock_clsoing_val'];
							
							
							}
							?>
                            </tbody>
                            <tfoot>
                            	<tr>
								<th colspan="4">Total</th>
							   <th><?=number_format($total_sum_openingQty,2);?></th>
                               <th><?=number_format($total_sum_openingVal,2);?></th>
								<th><?=number_format($total_sum_recvQty,2);?></th>
								<th><?=number_format($total_sum_recvVal,2);?></th>
								<th><?=number_format($total_sum_issueQty,2);?></th>
								<th><?=number_format($total_sum_issueVal,2);?></th>
                                <th><?=number_format($total_sum_clsoingQty,2);?></th>
								<th><?=number_format($total_sum_clsoingVal,2);?></th>
                                </tr>
                                
                            </tfoot>
                        </table>
                        
         </div>	

				<div style="width:<?=$tbl_width-80;?>px; float:left;">
                <div> <b style="float:left; font-size:18px;center;width:<?=$tbl_width-80;?>">Details </b> </div>
					<table cellspacing="0" cellpadding="0"  border="1" style="border-collapse: collapse;" rules="all"  width="<?=$tbl_width-80;?>" class="rpt_table" align="left">
                    <caption> <b style="float:left;">Cutting Stock (Finished Fabric) WIP </b></caption>
						<thead>
							<tr>
								<th width="30">SL#</th>								
								<th width="100">Buyer</th>								
								<th width="100" title="">Style</th>
								<th width="100">Order No</th>
								<th width="100" title="">Job No</th>
								<th width="60">Job Year</th>
								<th width="100">Gmt. Item</th>
								<th width="80">Ship Date</th>
								<th width="80">Order Qty.</th>
								<th width="60">Avg. FOB</th>
								<th width="80" title="">Order Value</th>
								<th width="60">SMV</th>
								<th width="60">Effi%</th>
								<th width="80" title="">CM PCS <br>Pre-cost</th>
								<th width="80" title="">Fin.Fabric<br>OpenningQty</th>
							 
								<th width="80" title="">Fin.Fabric <br>Openning <br>Value (Tk)</th>
								<th width="80">Fabric <br>Receive Qty.</th>
								<th width="80" title="Woven Fin Recv"> Fabric Recv.<br>Value Tk.</th>
								<th width="80" title=""> Cutting Use<br> Fabric Qty.</th>
								<th width="80" title="Finishing Qty - Shipment Qty">Cutting Use <br>Fab.Value<br>Tk.</th>
								<th width="80" title="">Fabric <br>Closing Qty</th>
								<th width="" title="Cutting+Print+Emb+Sewing+Finishing">Fab.Closing<br>Value Tk.</th>
							</tr>								
							</thead>
                        </table>
                     
                   <div style=" max-height:380px; width:<?=($tbl_width-80)+20;?>px; overflow-y:scroll;" id="scroll_body">
                    <table class="rpt_table" id="table_body" width="<?=($tbl_width-80);?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="">
                     <tbody>
                        <?
						//$job_qty_data_arr[$val['JOB_NO']]['job_qty']+=$val['PO_PCS_QTY'];
			//$job_qty_data_arr[$val['JOB_NO']]['po_ids'].=$val['ID'].",";
						$ff=1;$total_po_value=$total_po_qty_pcs=$total_cm_cost=$total_opening_fin_rcv_val=$total_opening_fin_rcv_qty=$total_cutting_used_fab_wgt=$total_fin_rcv_qty=$total_fin_rcv_val=$total_fab_clsoing_val=$total_fab_clsoing_qty=$total_cutting_used_fab_val=0;
						//print_r($job_qty_data_arr);
						foreach($job_qty_data_arr as $job_key=>$row) 
						{
					 
							 if ($ff%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							 
							  $itemArr= explode(",",$row['item_id']);
							  $item_name="";
							  foreach($itemArr as $item)
							  {
								 if($item_name=="") $item_name=$garments_item[$item]; else $item_name.=",".$garments_item[$item];
							  }
							   $fab_wgt=0; $cutting_used_fab_wgt=0;
							  $fab_wgt=$Cut_lay_data_arr[$job_key]['fab_wgt'];
							  $opening_layQty=$Cut_lay_data_arr[$job_key]['opening_fab_wgt'];
							
							 // $job_qty=$job_qty_data_arr[$job_key]['job_qty'];
							  $job_qty=$job_data_for_lay_arr[$job_key]['job_qty'];
							  if($fab_wgt>0)
							  {
							  $cutting_used_fab_wgt=($fab_wgt/$job_qty)*$row['job_qty'];
							  }
							  $opening_used_fab_wgt=0;
							  if($opening_layQty>0)
							  {
								   // echo  $opening_layQty.'T';
							  $opening_used_fab_wgt=($opening_layQty/$job_qty)*$row['job_qty'];
							  }
							 
							 //$opening_fin_issue=$opening_fin_issue_data_array[$val['PO_ID']]['fin_qty'];
							 
							   $po_ids=rtrim($row['po_ids'],',');
							   $po_idArr= array_unique(explode(",",$po_ids)); 
							   $po_qty=$po_value=$po_wise_cm_cost=$opening_fin_rcv=$fin_rcv=$fin_rcv_val=$tot_fab_fin_req_qty=$tot_fab_fin_req_qty=$opening_fin_val=0;
							   $shipDate="";
							  foreach($po_idArr as $poId)
							  {
								  
							  
							  if($shipDate=="") $shipDate=$prod_data_arr[$poId]['ship_date'];else $shipDate.=",".$prod_data_arr[$poId]['ship_date'];
							  
							   $po_value+= $prod_data_arr[$poId]['po_value'];			
								$po_qty+=$prod_data_arr[$poId]['po_qty'];	
								$po_wise_cm_cost+=$po_wise_cm_cost_arr[$poId];
								$opening_fin_rcv+=$opening_fin_rcv_data_array[$poId]['fin_qty'];
								$opening_fin_val+=$opening_fin_rcv_data_array[$poId]['fin_val'];
								$fin_rcv+=$fin_rcv_data_array[$poId]['fin_qty'];
								$fin_rcv_val+=$fin_rcv_data_array[$poId]['fin_val'];
								
								 $tot_fab_fin_req_qty+=$reqQtyAmtArr[$row['job_id']][$poId]['prodfin_qty']+$reqQtyAmtArr[$row['job_id']][$poId]['purchfin_qty'];
								 $tot_fab_fin_req_val+=$reqQtyAmtArr[$row['job_id']][$poId]['prodfin_amt']+$reqQtyAmtArr[$row['job_id']][$poId]['purchfin_amt'];
							  }
							  //echo $shipDate.'D';
							  
							   	  if($fin_rcv_val>0)
								  {
									 $fin_rcv_avg_rate=$fin_rcv_val/$fin_rcv; 
								  }
							  
							 	 if($fin_rcv_avg_rate>0)
								  {
								  $cutting_used_fab_val= $cutting_used_fab_wgt*$fin_rcv_avg_rate;
								  }
								  else $cutting_used_fab_val=0;
								  
								  if($fin_rcv_avg_rate>0)
								  {
								  $opening_used_fab_val= $opening_used_fab_wgt*$fin_rcv_avg_rate;
								  }
								  else $opening_used_fab_val=0;
								  
							 $fab_clsoing_qty=(($opening_fin_rcv-$opening_used_fab_wgt)+$fin_rcv)-$cutting_used_fab_wgt;
							 $fab_clsoing_val=(($opening_fin_val-$opening_used_fab_val)+$fin_rcv_val)-$cutting_used_fab_val;
							  
							  
							   //$opening_fin_rcv_data_array[$poId]['fin_qty']
							  //$fab_clsoing_qty=$opening_fin_rcv_data_array[$poId]['fin_qty'];
							  $po_no=rtrim($row['po_no'],',');
							  $po_nos= implode(",",array_unique(explode(",",$po_no)));
							  $po_idss= implode(",",$po_idArr); 
							  
							   $shipDates=rtrim($shipDate,',');
							  $shipDatesArr= array_unique(explode(",",$shipDates)); 
							   $ship_Dates=max($shipDatesArr);
							   
												?>
				            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_csff<? echo $ff; ?>','<? echo $bgcolor; ?>')" id="tr_csff<? echo $ff; ?>">
                            <td style="word-wrap: break-word;word-break: break-all;" width="30"><? echo $ff;?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $buyer_arr[$row['buyer']]; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $row['style']; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $po_nos; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" width="100"><a href="##" onClick="openmypage_job_popup(<? echo "'".$company_name."','".$job_key."','".$po_idss."'"; ?>,'JobopowisePopup')"><? echo $job_key; ?></a></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" width="60"><? echo $row['year']; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $item_name; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" width="80"><? echo change_date_format($ship_Dates); ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="80"><? echo $po_qty; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="60"><? echo number_format($po_value/$po_qty,2); ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="80"><? echo number_format($po_value,2); ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right"  width="60"><? echo $row['set_smv']; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="60"><? echo $row['sew_eff']; ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="80"><?  echo number_format($po_wise_cm_cost,2); ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" title="PreviousIssueQty(<?=$opening_fin_rcv;?>)-PreviousCuttingUSEDQty(<?=$opening_used_fab_wgt;?>)" align="right" width="80"><? echo number_format($opening_fin_rcv-$opening_used_fab_wgt,2); ?></td> 
                             
                            <td style="word-wrap: break-word;word-break: break-all;"title="PreviousIssueAmt(<?=$opening_fin_val;?>)-CuttingUSEDAmt(<?=$opening_used_fab_val;?>)"  align="right" width="80"><? echo number_format($opening_fin_val-$opening_used_fab_val,2); ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="80"><?php echo number_format($fin_rcv,2);?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right"  title="" width="80"><?php echo number_format($fin_rcv_val,2);?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" title="Cut Lay Fabric Width" align="right" width="80"><? echo number_format($cutting_used_fab_wgt,2); ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" title="AvgRate=<?=$fin_rcv_avg_rate;?>" align="right"  width="80"><? echo number_format($cutting_used_fab_val,2); ?></td> 
                            <td  style="word-wrap: break-word;word-break: break-all;" title="OpeingFinQty+FabRecvQty-CuttingUsedWgt" align="right"  width="80"><? echo number_format($fab_clsoing_qty,2); ?></td> 
                            
                            <td style="word-wrap: break-word;word-break: break-all;" title="OpeingFinValue+FabRecvVlaue-CuttingUsedValue" align="right"  width=""><? echo number_format($fab_clsoing_val,2); ?></td> 
                            </tr>
                            <?
							$ff++;
							$total_po_qty_pcs+=$po_qty;
							$total_po_value+=$po_value;
							$total_cm_cost+=$po_wise_cm_cost;
							$total_opening_fin_rcv_val+=$opening_fin_val-$opening_used_fab_val;
							$total_opening_fin_rcv_qty+=$opening_fin_rcv-$opening_used_fab_wgt;
							$total_cutting_used_fab_wgt+=$cutting_used_fab_wgt;
							$total_fin_rcv_qty+=$fin_rcv;
							$total_fin_rcv_val+=$fin_rcv_val;
							$total_fab_clsoing_val+=$fab_clsoing_val;
							$total_fab_clsoing_qty+=$fab_clsoing_qty;
							$total_cutting_used_fab_val+=$cutting_used_fab_val;
							//$total_po_value=$total_po_qty_pcs=$total_cm_cost=$total_opening_fin_rcv_val=$total_opening_fin_rcv_qty=0;
						 }
							?>
                            </tbody>
						  <tfoot>
							<tr>
								<th colspan="8">Total</th>
								<th><?=number_format($total_po_qty_pcs,0);?></th>
                                <th>&nbsp;</th>
								<th><?=number_format($total_po_value,2);?></th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
								<th><?=number_format($total_cm_cost,2);?></th>
								<th><?=number_format($total_opening_fin_rcv_qty,2);?></th>
                              
                                <th><?=number_format($total_opening_fin_rcv_val,2);?></th>
                             	<th><?=number_format($total_fin_rcv_qty,2);?></th>
								<th><?=number_format($total_fin_rcv_val,2);?></th>
								<th><?=number_format($total_cutting_used_fab_wgt,2);?></th>
								<th><?=number_format($total_cutting_used_fab_val,2);?></th>
								<th><?=number_format($total_fab_clsoing_qty,2);?></th>
								<th><?=number_format($total_fab_clsoing_val,2);?></th>
								 
							</tr>
                              </tfoot>
					</table>					
				</div> 
			</div>  
            <br>
            <div style="width:<?=$tbl_width;?>px; float:left;">
					<table cellspacing="0" cellpadding="0"  border="1" style="border-collapse: collapse;" rules="all"  width="<?=$tbl_width;?>" class="rpt_table" align="left">
                   	 <caption> <b style="float:left;">Cutting Stock (Cut Pannel) WIP </b></caption>
						<thead>
							<tr>
								<th width="30">SL#</th>								
								<th width="100">Buyer</th>								
								<th width="100" title="">Style</th>
								<th width="100">Order No</th>
								<th width="100" title="">Job No</th>
								<th width="60">Job Year</th>
								<th width="100">Gmt. Item</th>
								<th width="80">Ship Date</th>
								<th width="80">Order Qty.</th>
								<th width="60">Avg. FOB</th>
								<th width="80" title="">Order Value</th>
								<th width="60">SMV</th>
								<th width="60">Effi%</th>
								<th width="80" title="">CM PCS <br>Pre-costing</th>
								<th width="80" title="">Avg <br>Consumption</th>
								<th width="80">Rate<br>(Cut Pannel)</th>
								<th width="80" title="">Openning<br> Cut Qty.</th>
								<th width="80">UOM</th>
								<th width="80" title=""> Openning Cut<br> Value Tk.</th>
								<th width="80" title=""> Cutting Qty.</th>
								<th width="80" title="">Sewing <br>Input Qty.</th>
								<th width="80" title="">Cutting <br> In Hand</th>
								<th width="" title="">Cutting In<br> Hand Value</th>
								 
							</tr>								
							</thead>
                        </table>
                     
                      <div style=" max-height:390px; width:<?=$tbl_width+20;?>px; overflow-y:scroll;" id="scroll_body2">
                    <table class="rpt_table" id="table_body2" width="<?=$tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="">
                     <tbody>
                        <?
						$gbl_rate_arr=array();
						$cp=1;$total_po_value=$total_po_qty_pcs=$total_cm_cost=$total_avg_fab_cons=$total_opening_cut_qty=$total_opening_cut_val=$total_inputQty=$total_cutting_in_hand=$total_cutting_in_hand_val=0;
						foreach($prod_data_arr as $poId=>$row)
						{
					 
							 if ($cp%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							  $itemArr= explode(",",$row['item_id']);
							  $item_name="";
							  foreach($itemArr as $item)
							  {
								 if($item_name=="") $item_name=$garments_item[$item]; else $item_name.=",".$garments_item[$item];
							  }
							   $opening_sew_in=$opening_sew_data_array[$poId]['opening_sew_in_qty'];
							  $opening_cut_qty=$opening_cuting_qc_data_array[$poId]['opening_cut_qty']-$opening_sew_in;
							  $job_qty=$job_qty_data_arr[$row['job_no']]['job_qty'];
							  $cuting_qcQty= $cuting_qc_data_array[$poId]['cut_qty'];
							 //  echo $row['job_id'].'='.$poId.',';
							  $tot_fab_req_qty=0;  $tot_fab_req_val=0;$grey_fab_amt=0;  $grey_fab_Qty=0;$job_ex_rate=0;$cut_panel_rate=0;
							 $tot_fab_req_qty=$reqQtyAmtArr[$row['job_id']][$poId]['prodfin_qty']+$reqQtyAmtArr[$row['job_id']][$poId]['purchfin_qty'];
							 $tot_fab_req_val=$reqQtyAmtArr[$row['job_id']][$poId]['prodfin_amt']+$reqQtyAmtArr[$row['job_id']][$poId]['purchfin_amt'];
							   $grey_fab_avg_rate=0;$avg_fab_cons=0;$fab_rate=0;
							  if($tot_fab_req_qty>0)
							  {
							  $avg_fab_cons=$tot_fab_req_qty/$row['po_qty'];
							  }
							  $grey_fab_Qty=$fab_wo_data_array[$poId]['grey_qty']; 
							 $grey_fab_amt=$fab_wo_data_array[$poId]['grey_amt'];
							 if($grey_fab_amt>0)
							 {
							 $grey_fab_avg_rate=$grey_fab_amt/$grey_fab_Qty;
							 }
							  
							 $job_ex_rate=$job_qty_data_arr[$row['job_no']]['ex_rate'];
							 $currency_id=$fab_wo_data_array[$poId]['fab_wo_currency'];
							 if($currency_id==2 && $grey_fab_avg_rate>0) //USD
							 {
								$fab_rate=$grey_fab_avg_rate*$job_ex_rate;
							 }
							 else
							 {
								$fab_rate=$grey_fab_avg_rate;
							 }
							
							 // echo $fab_rate.'d='.$avg_fab_cons.'<br>';
							  if($fab_rate>0 && $avg_fab_cons>0)
							  {
							  $cut_panel_rate=$fab_rate*$avg_fab_cons;
							  }
							  
							  $cutting_in_hand=($opening_cut_qty+$cuting_qcQty)-$sewing_inputQty_arr[$poId]['sew_in'];
							  if($cut_panel_rate>0)
							  {
							  $gbl_rate_arr[$poId]=$cut_panel_rate;
							  }
												?>
				            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_cscp<? echo $cp; ?>','<? echo $bgcolor; ?>')" id="tr_cscp<? echo $cp; ?>">
                            <td style="word-wrap: break-word;word-break: break-all;" width="30"><? echo $cp;?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $buyer_arr[$row['buyer']]; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $row['style']; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $row['po_no']; ?></td>
                             <td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $row['job_no']; ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" width="60"><? echo $row['year']; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $item_name; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" width="80"><? echo change_date_format($row['ship_date']); ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="80"><? echo $row['po_qty']; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="60"><? echo $row['rate']; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="80"><? echo $row['po_value']; ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right"  width="60"><? echo $row['set_smv']; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="60"><? echo $row['sew_eff']; ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="80"><?  echo number_format($po_wise_cm_cost_arr[$poId],2); ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" title="Tot FabReqQty(<?=$tot_fab_req_qty;?>)/PO Qty(<?=$row['po_qty'];?>)" align="right" width="80"><? echo number_format($avg_fab_cons,4); ?></td> 
                             <td style="word-wrap: break-word;word-break: break-all;" align="right" title="Fab Wo avgRate*Ex Rate" width="80"><? echo number_format($cut_panel_rate,4);//$unit_of_measurement[$row['uom']]; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" title="PreviousCuttingQc(<?=$opening_cuting_qc_data_array[$poId]['opening_cut_qty'];?>)- Previous SewingInput(<?=$opening_sew_in;?>)"  align="right" width="80"><? echo number_format($opening_cut_qty,2); ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="80"><?php echo $unit_of_measurement[$row['uom']];?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right"  title="Rate(Cut Panel)*PreviousCutQty" width="80"><?php  if($opening_cut_qty) echo number_format($opening_cut_qty*$cut_panel_rate,2);else echo "";?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="80"><? echo number_format($cuting_qcQty,2); ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right"  width="80"><? echo number_format($sewing_inputQty_arr[$poId]['sew_in'],2); ?></td> 
                            <td  style="word-wrap: break-word;word-break: break-all;" title="Opening Cut+CutingQty-Cutting in Hand" align="right"  width="80"><? echo number_format($cutting_in_hand,2); ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right"  width=""><? if($cutting_in_hand) echo number_format($cutting_in_hand*$cut_panel_rate,2);else echo ""; ?></td> 
                            </tr>
                            <?
							$cp++;
							$total_po_qty_pcs+=$row['po_qty'];
							$total_po_value+=$row['po_value'];
							$total_cm_cost+=$po_wise_cm_cost_arr[$poId];
							$total_avg_fab_cons+=$avg_fab_cons;
							$total_opening_cut_qty+=$opening_cut_qty;
							$total_opening_cut_val+=$opening_cut_qty*$cut_panel_rate;
							$total_cuting_qcQty+=$cuting_qcQty;
							$total_inputQty+=$sewing_inputQty_arr[$poId]['sew_in'];
							$total_cutting_in_hand+=$cutting_in_hand;
							$total_cutting_in_hand_val+=$cutting_in_hand*$cut_panel_rate;
						 }
							?>
                            </tbody>
						  <tfoot>
							<tr>
								<th colspan="8">Total</th>
								<th><?=number_format($total_po_qty_pcs,0);?></th>
                                <th>&nbsp;</th>
								<th><?=number_format($total_po_value,2);?></th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
								<th><?=number_format($total_cm_cost,2);?></th>
								<th><?=number_format($total_avg_fab_cons,4);?></th>
                                <th> </th>
                                <th><?=number_format($total_opening_cut_qty,2);?></th>
                             	<th>&nbsp;</th>
								<th><?=number_format($total_opening_cut_val,2);?></th>
								<th><?=number_format($total_cuting_qcQty,2);?></th>
								<th><?=number_format($total_inputQty,2);?></th>
								<th><?=number_format($total_cutting_in_hand,2);?></th>
								<th><?=number_format($total_cutting_in_hand_val,2);?></th>
							</tr>
                              </tfoot>
						 
					</table>					
				</div> 
			</div>
            <br>
            <div style="width:<?=$tbl_width;?>px; float:left;">
					<table cellspacing="0" cellpadding="0"  border="1" style="border-collapse: collapse;" rules="all"  width="<?=$tbl_width;?>" class="rpt_table" align="left">
                    <caption> <b style="float:left;">Sub Contract Print  Factory-WIP Stock Report </b></caption>
						<thead>
							<tr>
								<th width="30">SL#</th>								
								<th width="100">Buyer</th>								
								<th width="100" title="">Style</th>
								<th width="100">Order No</th>
								<th width="100" title="">Job No</th>
								<th width="60">Job Year</th>
								<th width="100">Gmt. Item</th>
								<th width="80">Ship Date</th>
								<th width="80">Order Qty.</th>
								<th width="60">Avg. FOB</th>
								<th width="80" title="">Order Value</th>
								<th width="60">SMV</th>
								<th width="60">Effi%</th>
								<th width="80" title="">CM PCS <br>Pre-costing</th>
								<th width="80" title="">Rate<br>(Printing WIP)</th>
								<th width="80">Order <br>Cutting Qty)</th>
								<th width="80" title="">UOM</th>
								<th width="80">Print <br>Openning<br> Qty (Pcs)</th>
								<th width="80" title=""> Print <br>Openning<br> Value (Pcs)</th>
								<th width="80" title=""> Emb Issue<br> Qty Pcs</th>
								<th width="80" title="">EMB Receive <br>Qty Pcs</th>
								<th width="80" title="">EMB Stock<br> Qty (Pcs)</th>
								<th width="" title="">EMB Stock<br> Value (Pcs)</th>
							</tr>								
							</thead>
                        </table>
                     
                      <div style=" max-height:390px; width:<?=$tbl_width+20;?>px; overflow-y:scroll;" id="scroll_body3">
                    <table class="rpt_table" id="table_body3" width="<?=$tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="">
                     <tbody>
                        <?
					
						$sub=1;$total_po_value=$total_po_qty_pcs=$total_cm_cost=$total_opening_issue_print_val=$total_opening_issue_print_qty=$total_print_issue_qty=$total_print_recv_qty=$total_emb_stock_qty=$total_emb_stock_val=$total_cuting_qcQty=0;
						$gbl_rate2_arr=array();
						foreach($prod_data_arr as $poId=>$row)
						{
					 
							 if ($sub%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							  $itemArr= explode(",",$row['item_id']);
							  $item_name="";
							  foreach($itemArr as $item)
							  {
								 if($item_name=="") $item_name=$garments_item[$item]; else $item_name.=",".$garments_item[$item];
							  }
							  $opening_issue_print_qty=$opening_emb_issue_data_array[$poId]['opening_issue_print_qty'];
							   $gbl_rate=0;$print_rate=0;$print_issue_qty=0;
							  $job_qty=$job_qty_data_arr[$row['job_no']]['job_qty'];
							  $cuting_qcQty= $cuting_qc_data_array[$poId]['cut_qty'];
							  $gbl_rate=$gbl_rate_arr[$poId];
							 $print_rate= $embl_print_data_array[$poId]['print_rate']+$gbl_rate;
							  $gbl_rate2_arr[$poId]=$print_rate;
							 //   echo  $embl_print_data_array[$poId]['print_rate'].'='.$gbl_rate.'<br>';
							$print_issue_qty= $emb_issue_data_array[$poId]['print_issue_qty'];
							$print_recv_qty= $emb_issue_data_array[$poId]['print_recv_qty'];
							
							$emb_stock_qty= ($opening_issue_print_qty+$print_issue_qty)-$print_recv_qty;
							
							$print_rate_ttl= $embl_print_data_array[$poId]['print_rate'];
												?>
				            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_sub<? echo $sub; ?>','<? echo $bgcolor; ?>')" id="tr_sub<? echo $sub; ?>">
                            <td style="word-wrap: break-word;word-break: break-all;" width="30"><? echo $sub;?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $buyer_arr[$row['buyer']]; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $row['style']; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $row['po_no']; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $row['job_no']; ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" width="60"><? echo $row['year']; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $item_name; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" width="80"><? echo change_date_format($row['ship_date']); ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="80"><? echo $row['po_qty']; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="60"><? echo $row['rate']; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="80"><? echo $row['po_value']; ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right"  width="60"><? echo $row['set_smv']; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="60"><? echo $row['sew_eff']; ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="80"><?  echo number_format($po_wise_cm_cost_arr[$poId],2); ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" title="Previous Rate(<?=$gbl_rate;?>)+Print Avg rate(<?=$print_rate_ttl;?>)" align="right" width="80"><? echo number_format($print_rate,4); ?></td> 
                             <td style="word-wrap: break-word;word-break: break-all;" align="right" width="80"><? echo number_format($cuting_qcQty,2);//$unit_of_measurement[$row['uom']]; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" title="CuttingQc"  align="right" width="80"><? echo $unit_of_measurement[$row['uom']]; ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="80"><?php echo number_format($opening_issue_print_qty,2);?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right"  title="Rate(Print WIP)*OpeningIssueQty" width="80"><?php echo number_format($opening_issue_print_qty*$print_rate,0);?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="80"><? echo number_format($print_issue_qty,2); ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right"  width="80"><? echo number_format($print_recv_qty,2); ?></td> 
                            <td  style="word-wrap: break-word;word-break: break-all;" title="Opening Print_issue+Print_issue-RecvQty" align="right"  width="80"><? echo number_format($emb_stock_qty,0); ?></td> 
                            
                            <td style="word-wrap: break-word;word-break: break-all;" align="right"  width=""><? if($emb_stock_qty) echo number_format($emb_stock_qty*$print_rate,2); ?></td> 
                              
                            </tr>
                            <?
							$sub++;
							$total_po_qty_pcs+=$row['po_qty'];
							$total_po_value+=$row['po_value'];
							$total_cm_cost+=$po_wise_cm_cost_arr[$poId];
							$total_opening_issue_print_qty+=$opening_issue_print_qty;
							$total_print_issue_qty+=$print_issue_qty;
							if($opening_issue_print_qty)
							{
							$total_opening_issue_print_val+=$opening_issue_print_qty*$print_rate;
							}
							$total_cuting_qcQty+=$cuting_qcQty;
							$total_print_recv_qty+=$print_recv_qty;
							$total_cutting_in_hand+=$cutting_in_hand;
							$total_emb_stock_qty+=$emb_stock_qty;
							if($emb_stock_qty)
							{
							$total_emb_stock_val+=$emb_stock_qty*$print_rate;
							}
							//$total_po_value=$total_po_qty_pcs=$total_cm_cost=$total_avg_fab_cons=$total_opening_fin_rcv_qty=0;
						 }
							?>
                            </tbody>
						  <tfoot>
							<tr>
								 
								<th colspan="8">Total</th>
								<th><?=number_format($total_po_qty_pcs,0);?></th>
                                <th>&nbsp;</th>
								<th><?=number_format($total_po_value,2);?></th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
								<th><?=number_format($total_cm_cost,2);?></th>
								<th><? //number_format($total_avg_fab_cons,0);?></th>
                                 <th><?=number_format($total_cuting_qcQty,2);?></th>
                                <th> </th>
                             	<th><?=number_format($total_opening_issue_print_qty,2);?></th>
								<th><?=number_format($total_opening_issue_print_val,2);?></th>
								<th><?=number_format($total_print_issue_qty,2);?></th>
								<th><?=number_format($total_print_recv_qty,2);?></th>
								<th><?=number_format($total_emb_stock_qty,2);?></th>
								<th><?=number_format($total_emb_stock_val,2);?></th>
								 
							</tr>
                              </tfoot>
						 
					</table>					
				</div> 
			</div>
             <br>
             <?
             $tbl_width_emb=$tbl_width-60;
			 ?>
            <div style="width:<?=$tbl_width_emb;?>px; float:left;">
					<table cellspacing="0" cellpadding="0"  border="1" style="border-collapse: collapse;" rules="all"  width="<?=$tbl_width_emb;?>" class="rpt_table" align="left">
                    <caption> <b style="float:left;">Sub Contract Embroidary  Factory-WIP Stock Report </b></caption>
						<thead>
							<tr>
								<th width="30">SL#</th>								
								<th width="100">Buyer</th>								
								<th width="100" title="">Style</th>
								<th width="100">Order No</th>
								<th width="100" title="">Job No</th>
								<th width="60">Job Year</th>
								<th width="100">Gmt. Item</th>
								<th width="80">Ship Date</th>
								<th width="80">Order Qty.</th>
								<th width="60">Avg. FOB</th>
								<th width="80" title="">Order Value</th>
								<th width="60">SMV</th>
								<th width="60">Effi%</th>
								<th width="80" title="">CM PCS <br>Pre-costing</th>
                                <th width="80" title="">Cutting Qty</th>
								<th width="80" title="">Rate<br>(Embroidery WIP)</th>
								<th width="80">EMB Openning<br> Qty (Pcs)</th>
								<th width="80" title="">EMB Openning<br> Value (Pcs)</th>
								<th width="80">Emb Issue<br> Qty Pcs</th>
								<th width="80" title=""> EMB Receive<br> Qty Pcs</th>
								<th width="80" title=""> EMB Stock<br> Qty (Pcs)</th>
								<th width="" title="">EMB Stock <br>Value (Pcs)</th>
								 
							</tr>								
							</thead>
                        </table>
                     
                      <div style=" max-height:390px; width:<?=$tbl_width_emb+20;?>px; overflow-y:scroll;" id="scroll_body4">
                    <table class="rpt_table" id="table_body4" width="<?=$tbl_width_emb;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="">
                     <tbody>
                        <?
						$sub_f=1;$total_po_value=$total_po_qty_pcs=$total_cm_cost=$total_opening_embro_val=$total_opening_embro_issue_qty=$total_cuting_qcQty=$total_embro_issue_qty=$total_embro_recv_qty=$total_emb_stock_qty=$total_emb_stock_val=0;
						$gbl_rate3_arr=array();
						foreach($prod_data_arr as $poId=>$row)
						{
					 
							 if ($sub_f%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							  $itemArr= explode(",",$row['item_id']);
							  $item_name="";
							  foreach($itemArr as $item)
							  {
								 if($item_name=="") $item_name=$garments_item[$item]; else $item_name.=",".$garments_item[$item];
							  }
							   $gbl_rate2=0;$embro_rate=0;$opening_embro_issue_qty=0;$embro_issue_qty=0;
							 $opening_embro_issue_qty=$opening_embroidery_issue_data_array[$poId]['opening_embro_issue_qty'];
							 $job_qty=$job_qty_data_arr[$row['job_no']]['job_qty'];
							 $cuting_qcQty= $cuting_qc_data_array[$poId]['cut_qty'];
							 $gbl_rate2= $gbl_rate2_arr[$poId];
							 $embro_rate= $embl_embroidery_data_array[$poId]['embro_rate']+$gbl_rate2;
							 $gbl_rate3_arr[$poId]=$embro_rate;
							   //  echo $embl_embroidery_data_array[$poId]['embro_rate'].'='.$gbl_rate2.'<br>';
							$embro_issue_qty= $emb_embroidery_data_array[$poId]['embro_issue_qty'];
							$embroi_recv_prod_qty=$emb_issue_data_array[$poId]['embroi_recv_prod_qty'];
							$emb_stock_qty= ($opening_embro_issue_qty+$embro_issue_qty)-$embroi_recv_prod_qty;
							
							$embro_rate_ttl=$embl_embroidery_data_array[$poId]['embro_rate'];
												?>
				            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_sub_ef<? echo $sub_f; ?>','<? echo $bgcolor; ?>')" id="tr_sub_ef<? echo $sub_f; ?>">
                            <td style="word-wrap: break-word;word-break: break-all;" width="30"><? echo $sub_f;?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $buyer_arr[$row['buyer']]; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $row['style']; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $row['po_no']; ?></td>
                             <td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $row['job_no']; ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" width="60"><? echo $row['year']; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $item_name; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" width="80"><? echo change_date_format($row['ship_date']); ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="80"><? echo $row['po_qty']; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="60"><? echo $row['rate']; ?></td>
                            
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="80"><? echo $row['po_value']; ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right"  width="60"><? echo $row['set_smv']; ?></td>
                            
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="60"><? echo $row['sew_eff']; ?></td> 
                            
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="80"><?  echo number_format($po_wise_cm_cost_arr[$poId],2); ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="80"><?  echo number_format($cuting_qcQty,2); ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" title="Previous Rate(<?=$gbl_rate2;?>)+Embro AvgRate(<?=$embro_rate_ttl;?>)" align="right" width="80"><? echo number_format($embro_rate,4); ?></td> 
                             <td style="word-wrap: break-word;word-break: break-all;" align="right" width="80"><? echo number_format($opening_embro_issue_qty,2);//$unit_of_measurement[$row['uom']]; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" title=""  align="right" width="80"><? if($opening_embro_issue_qty) echo number_format($opening_embro_issue_qty*$embro_rate,2); ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="80"><?php echo number_format($embro_issue_qty,2);?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right"  title="" width="80"><?php echo number_format($embroi_recv_prod_qty,2);?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" title="Opening EmbroQty+Embro Issue Qty-EmbroRecv" width="80"><? echo number_format($emb_stock_qty,2); ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right"   width=""><? if($emb_stock_qty)  echo number_format($emb_stock_qty*$embro_rate,2); ?></td> 
                              
                            </tr>
                            <?
							$sub_f++;
							$total_po_qty_pcs+=$row['po_qty'];
							$total_po_value+=$row['po_value'];
							$total_cm_cost+=$po_wise_cm_cost_arr[$poId];
							$total_opening_embro_issue_qty+=$opening_embro_issue_qty;
							$total_embro_issue_qty+=$embro_issue_qty;
							if($opening_embro_issue_qty)
							{
							$total_opening_embro_val+=$opening_embro_issue_qty*$embro_rate;
							}
							$total_cuting_qcQty+=$cuting_qcQty;
							$total_embro_recv_qty+=$embroi_recv_prod_qty;
							//$total_cutting_in_hand+=$cutting_in_hand;
							$total_emb_stock_qty+=$emb_stock_qty;
							if($emb_stock_qty)
							{
								$total_emb_stock_val+=$emb_stock_qty*$embro_rate;
							}
							//$total_po_value=$total_po_qty_pcs=$total_cm_cost=$total_avg_fab_cons=$total_opening_fin_rcv_qty=0;
						 }
							?>
                            </tbody>
						  <tfoot>
							<tr>
								 
								<th colspan="8">Total</th>
								<th><?=number_format($total_po_qty_pcs,0);?></th>
                                <th>&nbsp;</th>
								<th><?=number_format($total_po_value,2);?></th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
								<th><?=number_format($total_cm_cost,2);?></th>
								<th><?=number_format($total_cuting_qcQty,2);?></th>
                                <th><? //number_format($total_avg_fab_cons,0);?></th>
                                <th><?=number_format($total_opening_embro_issue_qty,2);?></th>
                                 <th><?=number_format($total_opening_embro_val,2);?></th>
                             	<th><?=number_format($total_embro_issue_qty,2);?></th>
								<th><?=number_format($total_embro_recv_qty,2);?></th>
								<th><?=number_format($total_emb_stock_qty,2);?></th>
								<th><?=number_format($total_emb_stock_val,2);?></th>
								 
								 
							</tr>
                              </tfoot>
						 
					</table>					
				</div> 
			</div>
             <br>
             <?
            // $tbl_width_emb=$tbl_width-80;
			 ?>
            <div style="width:<?=$tbl_width_emb;?>px; float:left;">
					<table cellspacing="0" cellpadding="0"  border="1" style="border-collapse: collapse;" rules="all"  width="<?=$tbl_width_emb;?>" class="rpt_table" align="left">
                    <caption> <b style="float:left;">Sewing-WIP Stock Report </b></caption>
						<thead>
							<tr>
								<th width="30">SL#</th>								
								<th width="100">Buyer</th>								
								<th width="100" title="">Style</th>
								<th width="100">Order No</th>
								<th width="100" title="">Job No</th>
								<th width="60">Job Year</th>
								<th width="100">Gmt. Item</th>
								<th width="80">Ship Date</th>
								<th width="80">Order Qty.</th>
								<th width="60">Avg. FOB</th>
								<th width="80" title="">Order Value</th>
								<th width="60">SMV</th>
								<th width="60">Effi%</th>
								<th width="80" title="">CM PCS <br>Pre-costing</th>
                                <th width="80" title="">Cutting Qty</th>
								<th width="80" title="">Rate<br>(Sewing WIP))</th>
								<th width="80">Openning <br>Qty(Pcs)</th>
								<th width="80" title="">Openning <br>Value (Pcs)</th>
								<th width="80">Sewing Input<br> Qty Pcs</th>
								<th width="80" title=""> Sewing Output<br> Qty Pcs </th>
								<th width="80" title=""> Sewing Stock<br> Qty (Pcs)</th>
								<th width="" title="">Sewing Stock <br>Value (Pcs)</th>
							</tr>								
							</thead>
                        </table>
                     
                   <div style=" max-height:390px; width:<?=$tbl_width_emb+20;?>px; overflow-y:scroll;" id="scroll_body5">
                    <table class="rpt_table" id="table_body5" width="<?=$tbl_width_emb;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="">
                     <tbody>
                        <?
						$sew_w=1;$total_po_value=$total_po_qty_pcs=$total_cm_cost=$total_opening_sew_in_val=$total_opening_sew_in_qty=$total_cuting_qcQty=$total_sew_in_qty=$total_sew_out_qty=$total_sew_stock_val=$total_sew_stock_qty=0;
						$gbl_rate4_arr=array();
						foreach($prod_data_arr as $poId=>$row)
						{
					 
							 if ($sew_w%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							  $itemArr= explode(",",$row['item_id']);
							  $item_name="";
							  foreach($itemArr as $item)
							  {
								 if($item_name=="") $item_name=$garments_item[$item]; else $item_name.=",".$garments_item[$item];
							  }
							  $gbl_rate3=0;$sew_in_qty=0;$ex_rate=0;$sew_out_qty=0;$trim_rate_sew=0;$avg_avilable_min=0;$avg_avilable_min=0;$sew_rate=0;$sew_amt=0;$sew_qty=0;$style_wiseSewingOut=0;$opening_sew_in_qty=0;$currency_id=0;$job_qty=0;
							  $opening_sew_in_qty=$opening_sew_data_array[$poId]['opening_sew_in_qty'];
							  $job_qty=$job_qty_data_arr[$row['job_no']]['job_qty'];
							  $cuting_qcQty= $cuting_qc_data_array[$poId]['cut_qty'];
							 $gbl_rate3= $gbl_rate3_arr[$poId];
							// echo $trim_wo_data_array[$poId]['sew_rate'].'d';
							$sew_in_qty=$sew_data_array[$poId]['sew_in_qty'];
							$sew_out_qty=$sew_data_array[$poId]['sew_out_qty'];
						//	echo $po_wise_used_minArr[$poId].'D=<br>';
							  $ex_rate=$job_qty_data_arr[$row['job_no']]['ex_rate'];
							$currency_id=$trim_wo_data_array[$poId]['currency_id'];
							$sew_amt=$trim_wo_data_array[$poId]['sew_amt'];
							$sew_qty=$trim_wo_data_array[$poId]['sew_qty'];
							//$trim_rate_sew=$sew_amt/$sew_qty;
							if($currency_id==2 && $sew_amt>0)
							{
								$trim_rate_sew=($sew_amt/$sew_qty)*$ex_rate;
							}
							else {
							$trim_rate_sew=$sew_amt/$sew_qty;
							}
							//echo $style_wise_avilable_minArr[$row['style']].'d';;
							$style_wiseSewingOut=$style_wiseSewingOutArr[$row['job_no']];
							if($style_wiseSewingOut>0)
							{
							$avg_avilable_min=($style_wise_avilable_minArr[$row['style']]/$style_wiseSewingOut)*$sew_out_qty;
							}
							//echo $avg_avilable_min.'d';
							if($sew_out_qty>0)
							{
							$used_min=$avg_avilable_min/$sew_out_qty;
							}
							
							if($gbl_rate3>0 && $trim_rate_sew>0)
							{
							$sew_rate=($asking_avg_rate*$used_min)+$trim_rate_sew+$gbl_rate3;
							}
							$gbl_rate4_arr[$poId]=$sew_rate;
							$sew_stock_qty= ($opening_sew_in_qty+$sew_in_qty)-$sew_out_qty;
							//$sewRate_cal="Previous Rate+AskingAvgRate*UsedMin/SewOut+Wo AvgRate=".$asking_avg_rate.'*'.$used_min.'/'.$sew_out_qty.'+'.$trim_wo_data_array[$poId]['sew_rate'].'+'.$gbl_rate3;
							$sewRate_cal="Previous Rate+CPM*UsdMin/SewOut+Wo AvgRate=".$gbl_rate3.'+'.$asking_avg_rate.'*'.$style_wise_avilable_minArr[$row['style']].'/'.$sew_out_qty.'+'.$trim_rate_sew;
												?>
				            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_sew_w<? echo $sew_w; ?>','<? echo $bgcolor; ?>')" id="tr_sew_w<? echo $sew_w; ?>">
                            <td style="word-wrap: break-word;word-break: break-all;" width="30"><? echo $sew_w;?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $buyer_arr[$row['buyer']]; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $row['style']; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $row['po_no']; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $row['job_no']; ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" width="60"><? echo $row['year']; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $item_name; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" width="80"><? echo change_date_format($row['ship_date']); ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="80"><? echo $row['po_qty']; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="60"><? echo $row['rate']; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="80"><? echo $row['po_value']; ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right"  width="60"><? echo $row['set_smv']; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="60"><? echo $row['sew_eff']; ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="80"><?  echo number_format($po_wise_cm_cost_arr[$poId],2); ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="80"><?  echo number_format($cuting_qcQty,2); ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" title="Rate=<?=$sewRate_cal;?>" align="right" width="80"><? echo number_format($sew_rate,2); ?></td> 
                             <td style="word-wrap: break-word;word-break: break-all;" align="right" width="80"><? echo number_format($opening_sew_in_qty,2);//$unit_of_measurement[$row['uom']]; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" title="SewIn*Rate"  align="right" width="80"><? 
							if($opening_sew_in_qty>0 && $sew_rate>0)
							{ echo number_format($opening_sew_in_qty*$sew_rate,2);
							}?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="80"><?php echo number_format($sew_in_qty,2);?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right"  title="" width="80"><?php echo number_format($sew_out_qty,2);?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" title="Opening SewIn+Sew in Qty-Sew Out" width="80"><? echo number_format($sew_stock_qty,2); ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right"   width=""><? if($sew_stock_qty>0) echo number_format($sew_stock_qty*$sew_rate,2); ?></td> 
                            </tr>
                            <?
							$sew_w++;
							$total_po_qty_pcs+=$row['po_qty'];
							$total_po_value+=$row['po_value'];
							$total_cm_cost+=$po_wise_cm_cost_arr[$poId];
							$total_opening_sew_in_qty+=$opening_sew_in_qty;
							$total_sew_in_qty+=$sew_in_qty;
							 if($opening_sew_in_qty>0 && $sew_rate>0)
							 {
							$total_opening_sew_in_val+=$opening_sew_in_qty*$sew_rate;
							 }
							$total_cuting_qcQty+=$cuting_qcQty;
							$total_sew_out_qty+=$sew_out_qty;
							//$total_cutting_in_hand+=$cutting_in_hand;
							$total_sew_stock_qty+=$sew_stock_qty;
							 if($sew_stock_qty>0)
							 {
							$total_sew_stock_val+=$sew_stock_qty*$sew_rate;
							 }
							//$total_po_value=$total_po_qty_pcs=$total_cm_cost=$total_avg_fab_cons=$total_opening_fin_rcv_qty=0;
						 }
							?>
                            </tbody>
						  <tfoot>
							<tr>
								 
								<th colspan="8">Total</th>
								<th><?=number_format($total_po_qty_pcs,0);?></th>
                                <th>&nbsp;</th>
								<th><?=number_format($total_po_value,2);?></th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
								<th><?=number_format($total_cm_cost,2);?></th>
								<th><?=number_format($total_cuting_qcQty,2);?></th>
                                <th><? //number_format($total_avg_fab_cons,0);?></th>
                                <th><?=number_format($total_opening_sew_in_qty,2);?></th>
                                 <th><?=number_format($total_opening_sew_in_val,2);?></th>
                             	<th><?=number_format($total_sew_in_qty,2);?></th>
								<th><?=number_format($total_sew_out_qty,2);?></th>
								<th><?=number_format($total_sew_stock_qty,2);?></th>
								<th><?=number_format($total_sew_stock_val,2);?></th>
								 
								 
							</tr>
                              </tfoot>
						 
					</table>					
				</div> 
			</div>
             <br>
             <?
             $tbl_width_wash=$tbl_width+120;
			 ?>
            <div style="width:<?=$tbl_width_wash;?>px; float:left;">
					<table cellspacing="0" cellpadding="0"  border="1" style="border-collapse: collapse;" rules="all"  width="<?=$tbl_width_wash;?>" class="rpt_table" align="left">
                    <caption> <b style="float:left;">Sub Contract Washing Factory-WIP Stock Report </b></caption>
						<thead>
							<tr>
								<th width="30">SL#</th>								
								<th width="100">Buyer</th>								
								<th width="100" title="">Style</th>
								<th width="100">Order No</th>
								<th width="100" title="">Job No</th>
								<th width="60">Job Year</th>
								<th width="100">Gmt. Item</th>
								<th width="80">Ship Date</th>
								<th width="80">Order Qty.</th>
								<th width="60">Avg. FOB</th>
								<th width="80" title="">Order Value</th>
								<th width="60">SMV</th>
								<th width="60">Effi%</th>
								<th width="80" title="">CM PCS <br>Pre-costing</th>
                                <th width="80" title="">Cutting Qty</th>
								<th width="80" title="">Sewing <br>Input Qty</th>
								<th width="80">Sewing <br>Output Qty</th>
								<th width="80" title="">Rate<br>(Washing WIP)</th>
								<th width="80">Openning <br>Qty (Pcs)</th>
								<th width="80" title=""> Openning <br>Value(Pcs)</th>
                                <th width="80" title=""> Wash Issue<br> Qty Pcs</th>
                                <th width="80" title=""> Wash Recv.<br>Qty Pcs</th> 
                                <th width="80" title=""> Wash Stock<br> Qty (Pcs)</th>
								<th width="" title="">Wash Stock<br> Value (Pcs)</th>
							</tr>								
							</thead>
                        </table>
                     
                      <div style=" max-height:390px; width:<?=$tbl_width_wash+20;?>px; overflow-y:scroll;" id="scroll_body6">
                    <table class="rpt_table" id="table_body6" width="<?=$tbl_width_wash;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="">
                     <tbody>
                        <?
						$ww=1;$total_po_value=$total_po_qty_pcs=$total_cm_cost=$total_opening_wash_val=$total_cuting_qcQty=$total_opening_wash_qty=$total_recv_wash_qty=$total_sew_in_qty=$total_sew_out_qty=$total_wash_stock_qty=$total_wash_stock_val=$total_issue_wash_qty=0;
						$gbl_rate5_arr=array();
						foreach($prod_data_arr as $poId=>$row)
						{
					 
							 if ($ww%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							  $itemArr= explode(",",$row['item_id']);
							  $item_name="";
							  foreach($itemArr as $item)
							  {
								 if($item_name=="") $item_name=$garments_item[$item]; else $item_name.=",".$garments_item[$item];
							  }
							$opening_issue_wash_qty=$opening_wash_data_array[$poId]['opening_issue_wash_qty'];
							$issue_wash_qty=$wash_data_array[$poId]['issue_wash_qty']; 
							$recv_wash_qty=$wash_data_array[$poId]['recv_wash_qty'];
							$job_qty=$job_qty_data_arr[$row['job_no']]['job_qty'];
							$cuting_qcQty= $cuting_qc_data_array[$poId]['cut_qty'];
							$gbl_rate4= $gbl_rate4_arr[$poId];
							$wash_rate= $embl_embroidery_data_array[$poId]['wash_rate']+$gbl_rate4;
							$gbl_rate5_arr[$poId]=$wash_rate;
							$sew_in_qty=$sew_data_array[$poId]['sew_in_qty'];
							$sew_out_qty=$sew_data_array[$poId]['sew_out_qty'];
							$wash_stock_qty= ($opening_issue_wash_qty+$issue_wash_qty)-$recv_wash_qty;
							?>
				            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_ww<? echo $ww; ?>','<? echo $bgcolor; ?>')" id="tr_ww<? echo $ww; ?>">
                            <td style="word-wrap: break-word;word-break: break-all;" width="30"><? echo $ww;?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $buyer_arr[$row['buyer']]; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $row['style']; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $row['po_no']; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $row['job_no']; ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" width="60"><? echo $row['year']; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $item_name; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" width="80"><? echo change_date_format($row['ship_date']); ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="80"><? echo $row['po_qty']; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="60"><? echo $row['rate']; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="80"><? echo $row['po_value']; ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right"  width="60"><? echo $row['set_smv']; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="60"><? echo $row['sew_eff']; ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="80"><?  echo number_format($po_wise_cm_cost_arr[$poId],2); ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="80"><?  echo number_format($cuting_qcQty,2); ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" title="" align="right" width="80"><? echo number_format($sew_in_qty,2); ?></td> 
                             <td style="word-wrap: break-word;word-break: break-all;" align="right" width="80"><? echo number_format($sew_out_qty,2);//$unit_of_measurement[$row['uom']]; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" title="Previous Rate(<?=$gbl_rate4;?>)+Wash Avg Rate"  align="right" width="80"><? echo number_format($wash_rate,4); ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="80"><?php echo number_format($opening_issue_wash_qty,2);?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right"  title="" width="80"><?php  if($opening_issue_wash_qty>0) echo number_format($opening_issue_wash_qty*$wash_rate,2);?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" title="" width="80"><? echo number_format($issue_wash_qty,2); ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" title="" width="80"><? echo number_format($recv_wash_qty,2); ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" title="Opening Wash Issue+Wash Issue Qty-Wash Recv" width="80"><? echo number_format($wash_stock_qty,2); ?></td> 
                            
                            <td style="word-wrap: break-word;word-break: break-all;" align="right"   width=""><? if($wash_stock_qty>0) echo number_format($wash_stock_qty*$wash_rate,2); ?></td> 
                            </tr>
                            <?
							$ww++;
							$total_po_qty_pcs+=$row['po_qty'];
							$total_po_value+=$row['po_value'];
							$total_cm_cost+=$po_wise_cm_cost_arr[$poId];
							$total_opening_wash_qty+=$opening_issue_wash_qty;
							if($opening_issue_wash_qty>0)
							{
							$total_opening_wash_val+=$opening_issue_wash_qty*$wash_rate;
							}
							$total_sew_in_qty+=$sew_in_qty;
							$total_sew_out_qty+=$sew_out_qty;
							$total_cuting_qcQty+=$cuting_qcQty;
							$total_issue_wash_qty+=$issue_wash_qty;
							$total_recv_wash_qty+=$recv_wash_qty;
							$total_wash_stock_qty+=$wash_stock_qty;
							if($wash_stock_qty>0)
							{
							$total_wash_stock_val+=$wash_stock_qty*$wash_rate;
							}
							//$total_po_value=$total_po_qty_pcs=$total_cm_cost=$total_avg_fab_cons=$total_opening_fin_rcv_qty=0;
						 }
							?>
                            </tbody>
						  <tfoot>
							<tr>
								<th colspan="8">Total</th>
								<th><?=number_format($total_po_qty_pcs,0);?></th>
                                <th>&nbsp;</th>
								<th><?=number_format($total_po_value,2);?></th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
								<th><?=number_format($total_cm_cost,2);?></th>
								<th><?=number_format($total_cuting_qcQty,2);?></th>
                                <th><?=number_format($total_sew_in_qty,2);?></th>
                                <th><?=number_format($total_sew_out_qty,2);?></th>
                                <th><? //number_format($total_avg_fab_cons,0);?></th>
                             	<th><?=number_format($total_opening_wash_qty,2);?></th>
								<th><?=number_format($total_opening_wash_val,2);?></th>
								<th><?=number_format($total_issue_wash_qty,2);?></th>
								<th><?=number_format($total_recv_wash_qty,2);?></th>
                                <th><?=number_format($total_wash_stock_qty,2);?></th>
                                <th><?=number_format($total_wash_stock_val,2);?></th>
							</tr>
                              </tfoot>
						 
					</table>					
				</div> 
			</div>
             <br>
             <?
             $tbl_width_wash=$tbl_width+120;
			 ?>
            <div style="width:<?=$tbl_width_wash;?>px; float:left;">
					<table cellspacing="0" cellpadding="0"  border="1" style="border-collapse: collapse;" rules="all"  width="<?=$tbl_width_wash;?>" class="rpt_table" align="left">
                    <caption> <b style="float:left;">Gmts Finishing Stock-WIP</b></caption>
						<thead>
							<tr>
								<th width="30">SL#</th>								
								<th width="100">Buyer</th>								
								<th width="100" title="">Style</th>
								<th width="100">Order No</th>
								<th width="100" title="">Job No</th>
								<th width="60">Job Year</th>
								<th width="100">Gmt. Item</th>
								<th width="80">Ship Date</th>
								<th width="80">Order Qty.</th>
								<th width="60">Avg. FOB</th>
								<th width="80" title="">Order Value</th>
								<th width="60">SMV</th>
								<th width="60">Effi%</th>
								<th width="80" title="">CM PCS <br>Pre-costing</th>
                                <th width="80" title="">Cutting Qty</th>
								<th width="80" title="">Sewing <br>Input Qty</th>
								<th width="80">Sewing<br> Output Qty</th>
								<th width="80" title="">Rate<br>(Finish WIP)</th>
								<th width="80">Openning<br> Qty(Pcs)</th>
								<th width="80" title=""> Openning <br>Value(Pcs)</th>
                                <th width="80" title=""> Sewing Output/<br>Wash Rcv Qty Pcs</th>
                                <th width="80" title=""> Finishing<br> Qty Pcs</th> 
                                <th width="80" title=""> Finishing <br>Stock<br> Qty (Pcs)</th>
								<th width="" title="">Finishing <br>Stock<br> Value</th>
							</tr>								
							</thead>
                        </table>
                     
                      <div style=" max-height:390px; width:<?=$tbl_width_wash+20;?>px; overflow-y:scroll;" id="scroll_body7">
                    <table class="rpt_table" id="table_body7" width="<?=$tbl_width_wash;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="">
                     <tbody>
                        <?
						$gf=1;$total_po_value=$total_po_qty_pcs=$total_cm_cost=$total_opening_fin_qty=$total_cuting_qcQty=$total_opening_fin_val=$total_fin_qty=$total_sew_in_qty=$total_sew_out_qty=$total_recv_wash_qty=$total_sew_stock_val=$total_sew_stock_qty=0;
						$gbl_rate6_arr=array();
						foreach($prod_data_arr as $poId=>$row)
						{
					 
							 if ($gf%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							  $itemArr= explode(",",$row['item_id']);
							  $item_name="";
							  foreach($itemArr as $item)
							  {
								 if($item_name=="") $item_name=$garments_item[$item]; else $item_name.=",".$garments_item[$item];
							  }
							 
							  $opening_fin_qty=$opening_fin_data_array[$poId]['opening_fin_qty'];
							  $fin_qty=$fin_data_array[$poId]['fin_qty']; 
							//  $recv_wash_qty=$wash_data_array[$poId]['recv_wash_qty'];
							$gbl_rate5=0;$ex_rate=0;$trim_rate_fin=0;$currency_id=0;
							$recv_wash_qty=$wash_data_array[$poId]['recv_wash_qty'];
							$sew_out_qty=$wash_data_array[$poId]['sew_out_qty'];
							if($recv_wash_qty) $wash_sew_qty=$recv_wash_qty;else $wash_sew_qty=$sew_out_qty;
							  $fin_sew_out_qty=$wash_sew_qty;//$fin_data_array[$poId]['fin_sew_qty'];
							  $job_qty=$job_qty_data_arr[$row['job_no']]['job_qty'];
							  $cuting_qcQty= $cuting_qc_data_array[$poId]['cut_qty'];
							 $gbl_rate5= $gbl_rate5_arr[$poId];
							  $ex_rate=$job_qty_data_arr[$row['job_no']]['ex_rate'];
							$currency_id=$trim_wo_data_array[$poId]['currency_id'];
							$wo_fin_amt=$trim_wo_data_array[$poId]['fin_amt'];
							$wo_fin_qty=$trim_wo_data_array[$poId]['fin_qty'];
							//$trim_rate_fin=$fin_amt/$fin_qty;
							if($currency_id==2)
							{
								if($wo_fin_amt>0)
								{
									 $trim_rate_fin=($wo_fin_amt/$wo_fin_qty)*$ex_rate;
								}
							}
							else {
									if($wo_fin_amt>0)
									{
									$trim_rate_fin=$wo_fin_amt/$wo_fin_qty;
									}
								}
								
							 $fin_rate= $trim_rate_fin+$gbl_rate5;
							 $gbl_rate6_arr[$poId]=$fin_rate;
							 //  echo $gbl_rate5.'='.$trim_wo_data_array[$poId]['fin_rate'].'<br>';
							// $opening_sew_in_qty=$opening_sew_data_array[$val['PO_ID']]['opening_sew_in_qty'];
							$sew_in_qty=$sew_data_array[$poId]['sew_in_qty'];
							$sew_out_qty=$sew_data_array[$poId]['sew_out_qty'];
							$sew_stock_qty= ($opening_fin_qty+$fin_sew_out_qty)-$fin_qty;
												?>
				            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_gf<? echo $gf; ?>','<? echo $bgcolor; ?>')" id="tr_gf<? echo $gf; ?>">
                            <td style="word-wrap: break-word;word-break: break-all;" width="30"><? echo $gf;?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $buyer_arr[$row['buyer']]; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $row['style']; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $row['po_no']; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $row['job_no']; ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" width="60"><? echo $row['year']; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $item_name; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" width="80"><? echo change_date_format($row['ship_date']); ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="80"><? echo $row['po_qty']; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="60"><? echo $row['rate']; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="80"><? echo $row['po_value']; ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right"  width="60"><? echo $row['set_smv']; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="60"><? echo $row['sew_eff']; ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="80"><?  echo number_format($po_wise_cm_cost_arr[$poId],2); ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="80"><?  echo number_format($cuting_qcQty,2); ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" title="" align="right" width="80"><? echo number_format($sew_in_qty,2); ?></td> 
                             <td style="word-wrap: break-word;word-break: break-all;" align="right" width="80"><? echo number_format($sew_out_qty,2);//$unit_of_measurement[$row['uom']]; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" title="Previous Rate(<?=$gbl_rate5;?>)+Gmts Fin Avg Rate"  align="right" width="80"><? echo number_format($fin_rate,4); ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="80"><?php echo number_format($opening_fin_qty,2);?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right"  title="" width="80"><?php if($opening_fin_qty>0) echo number_format($opening_fin_qty*$fin_rate,2);?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" title="" width="80"><? echo number_format($fin_sew_out_qty,2); ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" title="" width="80"><? echo number_format($fin_qty,2); ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" title="Opening Fin +Wash/SewOut-Fin Qty" width="80"><? echo number_format($sew_stock_qty,0); ?></td> 
                            
                            <td style="word-wrap: break-word;word-break: break-all;" align="right"   width=""><? if($sew_stock_qty>0 && $fin_rate>0)  echo number_format($sew_stock_qty*$fin_rate,2);//else $sew_stock_qty=0;  ?></td> 
                            </tr>
                            <?
							$gf++;
							$total_po_qty_pcs+=$row['po_qty'];
							$total_po_value+=$row['po_value'];
							$total_cm_cost+=$po_wise_cm_cost_arr[$poId];
							$total_opening_fin_qty+=$opening_fin_qty;
							if($opening_fin_qty>0 && $fin_rate>0)
							{
							$total_opening_fin_val+=$opening_fin_qty*$fin_rate;
							}
							$total_sew_in_qty+=$sew_in_qty;
							$total_sew_out_qty+=$sew_out_qty;
							$total_cuting_qcQty+=$cuting_qcQty;
							$total_fin_qty+=$fin_qty;
							$total_recv_wash_qty+=$fin_sew_out_qty;
							//echo $sew_stock_qty.'TYY';;
							if($sew_stock_qty>0)
							{
							$total_sew_stock_qty+=$sew_stock_qty;
							}
							if($sew_stock_qty>0 && $fin_rate>0) 
							{
							$total_sew_stock_val+=$sew_stock_qty*$fin_rate;
							}
							//$total_po_value=$total_po_qty_pcs=$total_cm_cost=$total_avg_fab_cons=$total_opening_fin_rcv_qty=0;
						 }
							?>
                            </tbody>
						  <tfoot>
							<tr>
								<th colspan="8">Total</th>
								<th><?=number_format($total_po_qty_pcs,0);?></th>
                                <th>&nbsp;</th>
								<th><?=number_format($total_po_value,2);?></th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
								<th><?=number_format($total_cm_cost,2);?></th>
								<th><?=number_format($total_cuting_qcQty,2);?></th>
                                <th><?=number_format($total_sew_in_qty,2);?></th>
                                <th><?=number_format($total_sew_out_qty,2);?></th>
                                <th><? //number_format($total_avg_fab_cons,0);?></th>
                             	<th><?=number_format($total_opening_fin_qty,2);?></th>
								<th><?=number_format($total_opening_fin_val,2);?></th>
								<th><?=number_format($total_recv_wash_qty,2);?></th>
								<th><?=number_format($total_fin_qty,2);?></th>
                                <th><?=number_format($total_sew_stock_qty,2);?></th>
                                <th><?=number_format($total_sew_stock_val,2);?></th>
								 
							</tr>
                              </tfoot>
						 
					</table>					
				</div> 
			</div>
            
            <br>
             <?
             $tbl_width_wash=$tbl_width+120;
			 ?>
            <div style="width:<?=$tbl_width_wash;?>px; float:left;">
					<table cellspacing="0" cellpadding="0"  border="1" style="border-collapse: collapse;" rules="all"  width="<?=$tbl_width_wash;?>" class="rpt_table" align="left">
                    <caption> <b style="float:left;">Gmts Finished Goods</b></caption>
						<thead>
							<tr>
								<th width="30">SL#</th>								
								<th width="100">Buyer</th>								
								<th width="100" title="">Style</th>
								<th width="100">Order No</th>
								<th width="100" title="">Job No</th>
								<th width="60">Job Year</th>
								<th width="100">Gmt. Item</th>
								<th width="80">Ship Date</th>
								<th width="80">Order Qty.</th>
								<th width="60">Avg. FOB</th>
								<th width="80" title="">Order Value</th>
								<th width="60">SMV</th>
								<th width="60">Effi%</th>
								<th width="80" title="">CM PCS <br>Pre-costing</th>
                                <th width="80" title="">Cutting Qty</th>
								<th width="80" title="">Sewing<br> Input Qty</th>
								<th width="80">Sewing <br>Output Qty</th>
								<th width="80" title="">Rate<br>(Finish Goods)</th>
								<th width="80">Openning <br>Qty(Pcs)</th>
								<th width="80" title=""> Openning<br> Value(Pcs)</th>
                                <th width="80" title=""> Finish<br> Qty Pcs</th>
                                <th width="80" title=""> Ex-Factory<br> Qty Pcs</th> 
                                <th width="80" title=""> Closing <br>Stock Qty<br> (Pcs)</th>
								<th width="" title="">Closing<br> Stock<br> Value</th>
							</tr>								
							</thead>
                        </table>
                     
                      <div style=" max-height:390px; width:<?=$tbl_width_wash+20;?>px; overflow-y:scroll;" id="scroll_body8">
                    <table class="rpt_table" id="table_body8" width="<?=$tbl_width_wash;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="">
                     <tbody>
                        <?
						$gfg=1;$total_po_value=$total_po_qty_pcs=$total_cm_cost=$total_opening_fin_qty=$total_cuting_qcQty=$total_opening_fin_val=$total_fin_qty=$total_sew_in_qty=$total_sew_out_qty=$total_exfact_qty=$total_closing_stock_qty=$total_closing_stock_val=0;
						//$gbl_rate6_arr=array();
						foreach($prod_data_arr as $poId=>$row)
						{
					 
							 if ($gfg%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							  $itemArr= explode(",",$row['item_id']);
							  $item_name="";
							  foreach($itemArr as $item)
							  {
								 if($item_name=="") $item_name=$garments_item[$item]; else $item_name.=",".$garments_item[$item];
							  }
							// $opening_fin_data_array[$val['PO_ID']]['opening_goods_fin_qty']
							  $opening_fin_qty=$opening_fin_data_array[$poId]['opening_goods_fin_qty'];
							  $exfact_fin_qty=$exfact_data_array[$poId]['opening_ex_qty'];
							   $opening_fin_bal_qty=$opening_fin_qty-$exfact_fin_qty;
							  $fin_qty=$fin_data_array[$poId]['fin_qty']; 
							  $exfact_qty=$exfact_data_array[$poId]['ex_qty'];
							  $job_qty=$job_qty_data_arr[$row['job_no']]['job_qty'];
							  $cuting_qcQty= $cuting_qc_data_array[$poId]['cut_qty'];
							 $gbl_rate7= $gbl_rate6_arr[$poId];
							  $ex_rate=$job_qty_data_arr[$row['job_no']]['ex_rate'];
							$currency_id=$trim_wo_data_array[$poId]['currency_id'];
							$wo_g_fin_amt=$trim_wo_data_array[$poId]['fin_amt'];
							$wo_g_fin_qty=$trim_wo_data_array[$poId]['fin_qty'];
							//$trim_rate_fin=$fin_amt/$fin_qty;
							if($currency_id==2)
							{
								if($wo_g_fin_amt>0)
								{
								$trim_rate_fin=($wo_g_fin_amt/$wo_g_fin_qty)*$ex_rate;
								}
							}
							else {
									if($wo_g_fin_amt>0)
									{
									$trim_rate_fin=$wo_g_fin_amt/$wo_g_fin_qty;
									}
								}
								
							 $gfin_rate= $trim_rate_fin+$gbl_rate7;
							 $gbl_rate7_arr[$poId]=$gfin_rate;
							 //  echo $gbl_rate5.'='.$trim_wo_data_array[$poId]['fin_rate'].'<br>';
							// $opening_sew_in_qty=$opening_sew_data_array[$val['PO_ID']]['opening_sew_in_qty'];
							$sew_in_qty=$sew_data_array[$poId]['sew_in_qty'];
							$sew_out_qty=$sew_data_array[$poId]['sew_out_qty'];
							$clsoing_stock_qty= $opening_fin_bal_qty+$fin_qty-$exfact_qty;
												?>
				            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_gfg<? echo $gfg; ?>','<? echo $bgcolor; ?>')" id="tr_gfg<? echo $gfg; ?>">
                            <td style="word-wrap: break-word;word-break: break-all;" width="30"><? echo $gfg;?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $buyer_arr[$row['buyer']]; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $row['style']; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $row['po_no']; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $row['job_no']; ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" width="60"><? echo $row['year']; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" width="100"><? echo $item_name; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" width="80"><? echo change_date_format($row['ship_date']); ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="80"><? echo $row['po_qty']; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="60"><? echo $row['rate']; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="80"><? echo $row['po_value']; ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right"  width="60"><? echo $row['set_smv']; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="60"><? echo $row['sew_eff']; ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="80"><?  echo number_format($po_wise_cm_cost_arr[$poId],2); ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" width="80"><?  echo number_format($cuting_qcQty,2); ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" title="" align="right" width="80"><? echo number_format($sew_in_qty,2); ?></td> 
                             <td style="word-wrap: break-word;word-break: break-all;" align="right" width="80"><? echo number_format($sew_out_qty,2);//$unit_of_measurement[$row['uom']]; ?></td>
                            <td style="word-wrap: break-word;word-break: break-all;" title="Previous Rate(<?=$gbl_rate7;?>)+Gmts Fin Goods Avg Rate"  align="right" width="80"><? echo number_format($gfin_rate,4); ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" title="FinOpeing-Ex-Fact OpeingQty" align="right" width="80"><?php echo number_format($opening_fin_bal_qty,2);?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right"  title="" width="80"><?php if($opening_fin_bal_qty>0) echo number_format($opening_fin_bal_qty*$gfin_rate,2);?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" title="" width="80"><? echo number_format($fin_qty,2); ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" title="" width="80"><? echo number_format($exfact_qty,2); ?></td> 
                            <td style="word-wrap: break-word;word-break: break-all;" align="right" title="Opening Qty+Fin Qty-ExFact Qty" width="80"><? echo number_format($clsoing_stock_qty,02); ?></td> 
                            
                            <td style="word-wrap: break-word;word-break: break-all;" title="ClosingQty*Rate" align="right"   width=""><? if($clsoing_stock_qty>0)  echo number_format($clsoing_stock_qty*$gfin_rate,2); ?></td> 
                            </tr>
                            <?
							$gfg++;
							$total_po_qty_pcs+=$row['po_qty'];
							$total_po_value+=$row['po_value'];
							$total_cm_cost+=$po_wise_cm_cost_arr[$poId];
							$total_opening_fin_qty+=$opening_fin_bal_qty;
							if($opening_fin_bal_qty>0)
							{
							$total_opening_fin_val+=$opening_fin_bal_qty*$gfin_rate;
							}
							$total_sew_in_qty+=$sew_in_qty;
							$total_sew_out_qty+=$sew_out_qty;
							$total_cuting_qcQty+=$cuting_qcQty;
							$total_fin_qty+=$fin_qty;
							$total_exfact_qty+=$exfact_qty;
							$total_closing_stock_qty+=$clsoing_stock_qty;
							if($clsoing_stock_qty>0)
							{
							$total_closing_stock_val+=$clsoing_stock_qty*$gfin_rate;
							}
							//$total_po_value=$total_po_qty_pcs=$total_cm_cost=$total_avg_fab_cons=$total_opening_fin_rcv_qty=0;
						 }
							?>
                            </tbody>
						  <tfoot>
							<tr>
								 
								<th colspan="8">Total</th>
								<th><?=number_format($total_po_qty_pcs,0);?></th>
                                <th>&nbsp;</th>
								<th><?=number_format($total_po_value,2);?></th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
								<th><?=number_format($total_cm_cost,2);?></th>
								<th><?=number_format($total_cuting_qcQty,0);?></th>
                                <th><?=number_format($total_sew_in_qty,2);?></th>
                                <th><?=number_format($total_sew_out_qty,2);?></th>
                                <th><? //number_format($total_avg_fab_cons,0);?></th>
                             	<th><?=number_format($total_opening_fin_qty,2);?></th>
								<th><?=number_format($total_opening_fin_val,2);?></th>
								<th><?=number_format($total_fin_qty,2);?></th>
								<th><?=number_format($total_exfact_qty,2);?></th>
                                <th><?=number_format($total_closing_stock_qty,2);?></th>
                                <th><?=number_format($total_closing_stock_val,2);?></th>
								 
							</tr>
                              </tfoot>
						 
					</table>					
				</div> 
			</div>
            
		
		
		<?
	}
	/*foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	//$filename=$user_id."_".$name.".xls";*/

//	echo "$total_data####$filename";
	$html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html####$filename"; 
    exit();
	
	exit(); 
}

if($action=="production_popup")
{ 
	echo load_html_head_contents("Production Popup", "../../../", 1, 1, $unicode, '', '');
	// $process = array( &$_POST );
	// extract(check_magic_quote_gpc( $process ));
	extract($_REQUEST);
	list($wo_company_name,$location_name,$floor_name,$buyer_name,$style,$season,$item_id,$color_id,$start_date,$end_date,$job_no) = explode("**", $search_string);		
	
	

	$company_lib=return_library_array( "SELECT id, company_name from lib_company", "id", "company_name"  );
	$location_lib=return_library_array( "SELECT id, location_name from lib_location", "id", "location_name"  );
	$floor_lib=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  );
    $season_lib = return_library_array("select id, season_name from lib_buyer_season where status_active =1 and is_deleted=0", "id", "season_name");
    $buyer_lib  = return_library_array("select id, buyer_name from  lib_buyer", "id", "buyer_name");
	$color_lib=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$size_lib=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
	$lineArr = return_library_array("select a.id,a.line_name from lib_sewing_line a","id","line_name"); 
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
		
	
	$sql_cond = "";
	ob_start();
	if($type==1)
	{
		// $sql_cond .= ($hidden_job_id != "") ? " and a.id in($hidden_job_id)" : "";
		// $sql_cond .= ($buyer_name != 0) 	? " and a.buyer_name=$buyer_name" : "";
		// $sql_cond .= ($style != "") 		? " and a.style_ref_no='$style'" : "";
		$sql_cond .= ($job_no != "") 		? " and a.job_no='$job_no'" : "";
		$sql_cond .= ($item_id != "") 		? " and b.gmt_item_id in($item_id)" : "";
		$sql_cond .= ($color_id != "") 		? " and b.color_id in($color_id)" : "";
		
		$sql_cond .= ($wo_company_name != 0) 	? " and a.working_company_id=$wo_company_name" : "";
		$sql_cond .= ($location_name != 0) 		? " and a.location_id=$location_name" : "";
		if($type==1)
		{
			$sql_cond .= ($floor_name != 0) 	? " and a.floor_id in($floor_name)" : "";
		}	

		if($start_date !="" && $end_date !="")
	    {
	        $sql_cond.= " and a.entry_date between '$start_date' and '$end_date'";
	    }
	    /*==========================================================================================/
		/									getting gmts prod data 									/
		/==========================================================================================*/ 	
			
		$sql=" SELECT b.color_id as color_id,c.size_id as size_id,a.entry_date,a.location_id as location,a.working_company_id as serving_company,a.floor_id,a.cutting_no,c.size_qty

			from ppl_cut_lay_mst a,ppl_cut_lay_dtls b,ppl_cut_lay_bundle c where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sql_cond order by c.size_id";		
		// echo $sql;die;
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
		foreach ($sql_res as $val) 
		{
			$data_array[$val['SERVING_COMPANY']][$val['ENTRY_DATE']][$val['CUTTING_NO']][$val['FLOOR_ID']][$val['COLOR_ID']][$val['SIZE_ID']] += $val['SIZE_QTY'];
			
			$size_arr[$val['SIZE_ID']] = $val['SIZE_ID'];
			
		}
		// echo "<pre>";print_r($data_array);echo "</pre>";

		// echo $sql_cond;
		$tbl_width = 560+(count($size_arr)*60);	
		?>
		<fieldset style="width:<?=$tbl_width+20;?>px;">
			
			<div style="width:<?=$tbl_width+20;?>px;">			
					
					<div style="width:<?=$tbl_width+20;?>px; float:left;">
						<table cellspacing="0" cellpadding="0"  border="1" style="border-collapse: collapse;" rules="all"  width="<?=$tbl_width;?>" class="rpt_table" align="left">
							<thead>
								<tr>
									<th width="20">Sl</th>								
									<th width="120">Working Company</th>								
									<th width="60">Cutting Date</th>
									<th width="80">Sys. Cut No</th>
									<th width="100">Cutting Floor</th>
									<th width="100">Color</th>
									<?
									foreach ($size_arr as $key => $val) 
									{
										?>
										<th width="60"><?=$size_lib[$key];?></th>
										<?
									}
									?>
									<th width="80">Total</th>
								</tr>								
							</thead>
						</table>
						
						<div style="max-height:425px; overflow-y:scroll; width:<?=$tbl_width+20;?>px;" id="scroll_body">
							<table  border="1" style="border-collapse: collapse;" class="rpt_table"  width="<?=$tbl_width;?>" rules="all" id="table_bodys"  align="left">
								<tbody>
									<?
									$i=1;
									$gr_tot_arr = array();
									foreach ($data_array as $wo_name => $wo_data) 
									{	
										foreach ($wo_data as $pdate => $date_data) 
										{
											foreach ($date_data as $cut_no => $cut_data) 
											{
												foreach ($cut_data as $flr_name => $flr_data) 
												{
													foreach ($flr_data as $color_id => $row) 
													{			
														$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";	
														?>
														<tr bgcolor="<? echo $bgcolor;?>" id="tr_<?= $i;?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')">
															<td width="20"><p><?=$i;?></p></td>
															<td width="120"><p><?=$company_lib[$wo_name];?></p></td>
															<td width="60"><p><?=$pdate;?></p></td>							
															<td width="80"><p><?=$cut_no;?></p></td>
															<td width="100"><p><?=$floor_lib[$flr_name];?></p></td>
															<td width="100"><p><?=$color_lib[$color_id];?></p></td>
															<?
															$tot = 0;
															foreach ($size_arr as $key => $val) 
															{
																?>
																<td width="60" align="right"><?=number_format($row[$key],0);?></td>
																<?
																$tot += $row[$key];
																$gr_tot_arr[$key] += $row[$key];
															}
															?>
															<td align="right" width="80"><?=number_format($tot,0);?></td>
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
							</table> 
						</div> 
						<table style="border-collapse: collapse;"  border="1" class="rpt_table"  width="<?=$tbl_width;?>" rules="all" align="left" >
							<tfoot>
								<tr>
									<th width="20"></th>								
									<th width="120"></th>								
									<th width="60"></th>
									<th width="80"></th>
									<th width="100"></th>
									<th width="100">Total</th>
									<?
									$tot = 0;
									foreach ($size_arr as $key => $val) 
									{
										?>
										<th width="60"><?=$gr_tot_arr[$key];?></th>
										<?
										$tot += $gr_tot_arr[$key];
									}
									?>
									<th width="80"><?=number_format($tot,0);?></th>
								</tr>
							</tfoot>
						</table>
					</div> 
			</div>   
		</fieldset>
		
		<?
	}
	else
	{
		// $sql_cond .= ($hidden_job_id != "") ? " and a.id in($hidden_job_id)" : "";
		$sql_cond .= ($buyer_name != 0) 	? " and a.buyer_name=$buyer_name" : "";
		$sql_cond .= ($style != "") 		? " and a.style_ref_no='$style'" : "";
		$sql_cond .= ($season != "") 		? " and a.season_buyer_wise='$season'" : "";
		$sql_cond .= ($job_no != "") 		? " and a.job_no='$job_no'" : "";
		$sql_cond .= ($item_id != "") 		? " and c.item_number_id in($item_id)" : "";
		$sql_cond .= ($color_id != "") 		? " and c.color_number_id in($color_id)" : "";
		
		$sql_cond .= ($wo_company_name != 0) 	? " and d.serving_company=$wo_company_name" : "";
		$sql_cond .= ($location_name != 0) 		? " and d.location=$location_name" : "";
		if($type==1)
		{
			$sql_cond .= ($floor_name != 0) 	? " and d.floor_id in($floor_name)" : "";
		}	

		if($start_date !="" && $end_date !="")
	    {
	        $sql_cond.= " and d.production_date between '$start_date' and '$end_date'";
	    }
	    /*==========================================================================================/
		/									getting gmts prod data 									/
		/==========================================================================================*/ 	
			
		$sql=" SELECT c.color_number_id as color_id,c.size_number_id as size_id,c.order_quantity,d.production_date,d.location,d.serving_company,d.floor_id,d.production_type,d.prod_reso_allo,d.sewing_line,e.cut_no,e.production_qnty

			from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e where a.id=b.job_id and b.id=c.po_break_down_id and a.id=c.job_id and b.id=d.po_break_down_id and c.id=e.color_size_break_down_id and d.id=e.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and d.production_type=4 and e.production_qnty>0 $sql_cond order by c.size_order,d.production_date";		
		// echo $sql;die;
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
		foreach ($sql_res as $val) 
		{
			$sewing_line = "";
			if($val['PROD_RESO_ALLO']==1)
			{
				$line_number=explode(",",$prod_reso_arr[$val['SEWING_LINE']]);
				foreach($line_number as $vals)
				{
					if($sewing_line=='') $sewing_line=$lineArr[$vals]; else $sewing_line.=",".$lineArr[$vals];
				}
			}
			else
			{
				$sewing_line=$lineArr[$val['SEWING_LINE']];
			}

			$data_array[$val['SERVING_COMPANY']][$val['PRODUCTION_DATE']][$val['CUT_NO']][$val['FLOOR_ID']][$sewing_line][$val['COLOR_ID']][$val['SIZE_ID']]['qty'] += $val['PRODUCTION_QNTY'];
			$data_array[$val['SERVING_COMPANY']][$val['PRODUCTION_DATE']][$val['CUT_NO']][$val['FLOOR_ID']][$sewing_line][$val['COLOR_ID']][$val['SIZE_ID']]['prod_reso_allo'] = $val['PROD_RESO_ALLO'];
			
			$size_arr[$val['SIZE_ID']] = $val['SIZE_ID'];
			
		}
		// echo "<pre>";print_r($data_array);echo "</pre>";

		// echo $sql_cond;
		$tbl_width = 660+(count($size_arr)*60);	
		?>
		<fieldset style="width:<?=$tbl_width+20;?>px;">
			
			<div style="width:<?=$tbl_width+20;?>px;">			
					
					<div style="width:<?=$tbl_width+20;?>px; float:left;">
						<table cellspacing="0" cellpadding="0"  border="1" style="border-collapse: collapse;" rules="all"  width="<?=$tbl_width;?>" class="rpt_table" align="left">
							<thead>
								<tr>
									<th width="20">Sl</th>								
									<th width="120">Working Company</th>								
									<th width="60">Input Date</th>
									<th width="80">Input Challan No</th>
									<th width="100">Sewing Floor</th>
									<th width="100">Line No</th>
									<th width="100">Color</th>
									<?
									foreach ($size_arr as $key => $val) 
									{
										?>
										<th width="60"><?=$size_lib[$key];?></th>
										<?
									}
									?>
									<th width="80">Total</th>
								</tr>								
							</thead>
						</table>
						
						<div style="max-height:425px; overflow-y:scroll; width:<?=$tbl_width+20;?>px;" id="scroll_body">
							<table  border="1" style="border-collapse: collapse;" class="rpt_table"  width="<?=$tbl_width;?>" rules="all" id="table_bodys"  align="left">
								<tbody>
									<?
									$i=1;
									$gr_tot_arr = array();
									foreach ($data_array as $wo_name => $wo_data) 
									{	
										foreach ($wo_data as $pdate => $date_data) 
										{
											foreach ($date_data as $cut_no => $cut_data) 
											{
												foreach ($cut_data as $flr_name => $flr_data) 
												{
													foreach ($flr_data as $line_name => $line_data) 
													{
														foreach ($line_data as $color_id => $row) 
														{			
															$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";	
															?>
															<tr bgcolor="<? echo $bgcolor;?>" id="tr_<?= $i;?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')">
																<td width="20"><p><?=$i;?></p></td>
																<td width="120"><p><?=$company_lib[$wo_name];?></p></td>
																<td width="60"><p><?=$pdate;?></p></td>							
																<td width="80"><p><?=$cut_no;?></p></td>
																<td width="100"><p><?=$floor_lib[$flr_name];?></p></td>
																<td width="100"><p><?=$line_name;?></p></td>
																<td width="100"><p><?=$color_lib[$color_id];?></p></td>
																<?
																$tot = 0;
																foreach ($size_arr as $key => $val) 
																{
																	?>
																	<td width="60" align="right"><?=number_format($row[$key]['qty'],0);?></td>
																	<?
																	$tot += $row[$key]['qty'];
																	$gr_tot_arr[$key] += $row[$key]['qty'];
																}
																?>
																<td align="right" width="80"><?=number_format($tot,0);?></td>
															</tr>
															<?
															$i++;															
														}
													}
												}
											}
										}
									}
									?>
								</tbody>
							</table> 
						</div> 
						<table style="border-collapse: collapse;"  border="1" class="rpt_table"  width="<?=$tbl_width;?>" rules="all" align="left" >
							<tfoot>
								<tr>
									<th width="20"></th>								
									<th width="120"></th>								
									<th width="60"></th>
									<th width="80"></th>
									<th width="100"></th>
									<th width="100"></th>
									<th width="100">Total</th>
									<?
									$tot = 0;
									foreach ($size_arr as $key => $val) 
									{
										?>
										<th width="60"><?=$gr_tot_arr[$key];?></th>
										<?
										$tot += $gr_tot_arr[$key];
									}
									?>
									<th width="80"><?=number_format($tot,0);?></th>
								</tr>
							</tfoot>
						</table>
					</div> 
			</div>   
		</fieldset>
		<?
	}
	$user_id = $user_id."_pop";
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
	//$filename=$user_id."_".$name.".xls";

	// echo "$total_data####$filename";
	echo '<a target="_blank" href="'.$filename.'"><input type="button" value="Excel Preview" name="excel" id="exportBtn" class="formbutton" style="width:100px"/></a>';
	exit(); 
}
if($action=="JobopowisePopup")
{
	echo load_html_head_contents("Po Order Dtls Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	//$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	
	if($type_id==1)
	{
		$td_width=710;
		$row_span=5;	
	}
	else if($type_id==2)
	{
		$td_width=710+80;
		$row_span=5;	
	}
	$td_width=750;
	?>
	<script>
		function print_window()
		{
			$("#table_body_popup tr:first").hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
			$("#table_body_popup tr:first").show();
		}	
	</script>	
	<fieldset style="width:<? echo $td_width?>px; margin-left:3px">
        <div style="width:<? echo $td_width?>px;" align="center">
        	<input  type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
        </div>
        <div id="report_div" align="center">
            <table rules="all" width="<? echo $td_width?>" cellpadding="0" cellspacing="0" align="center">
                <tr> 
                	<td colspan="5" align="center"><strong> Order Details </strong></td>
                </tr>
                
            </table>
            <table border="1" class="rpt_table" rules="all" width="<? echo $td_width?>" cellpadding="0" cellspacing="0" align="center" >
                <thead>
                    <th width="30">SL</th>
                    <th width="100">Buyer</th>
                    <th width="100">Job No</th>
                    <th width="100">Style</th>
                    <th width="80">PO NO</th>
                    <th width="80">Order Qty</th>
                    <th width="80">FOB</th>
                    <th width="80">Value</th>
                    <th>Pub Ship Date</th>
                </thead>
                </table>
                 <table border="1" class="rpt_table" rules="all" width="<? echo $td_width?>" cellpadding="0" cellspacing="0" align="center" id="table_body_popup">
                <?
				$po_sql="select a.job_no,a.buyer_name,a.style_ref_no,b.po_total_price,b.pub_shipment_date,b.po_number,b.pub_shipment_date,b.po_quantity as po_qty, b.unit_price as unit_price from wo_po_break_down b,wo_po_details_master a   where a.id=b.job_id and  b.id in($po_id) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0";
				$po_sql_result=sql_select($po_sql); $i=1;
				foreach($po_sql_result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					
					
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
						<td width="30"><p><? echo $i; ?></p></td>
						<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></div></td>
                        <td width="100" align="center"><p><? echo $row[csf('job_no')]; ?></p></td>
                        <td width="100" align="center"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                        
                          <td width="80" align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                         <td width="80" align="right"><p><? echo number_format($row[csf('po_qty')]); ?></p></td>
                         <td width="80" align="right"><p><? echo number_format($row[csf('unit_price')],2); ?></p></td>
                         <td width="80" align="right"><p><? echo number_format($row[csf('po_total_price')],2); ?></p></td>
						<td width=""><div style="word-wrap:break-word; width:80px"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></div></td>
                         
					</tr>
					<?
					$tot_po_qty+=$row[csf('po_qty')];
					//$tot_plan_cut_qty+=$row[csf('plan_cut_qty')];
					$tot_order_value+=$row[csf('po_total_price')];
					$i++;
				}
				?>
				
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="5" align="right">Total</td>
                        
						<td align="right"><? echo number_format($tot_po_qty,2); ?></td>
                        <td align="right">&nbsp;</td>
                       
                        <td align="right"><? echo number_format($tot_order_value,2); ?></td>
                         <td align="right">&nbsp;</td>
					</tr>
				</tfoot>
			</table>
         <script>   setFilterGrid("table_body_popup",-1);</script>
		</div>
	</fieldset>
	<?
	exit();
} //Po wise button end

?>