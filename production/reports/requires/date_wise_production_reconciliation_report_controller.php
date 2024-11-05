<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
$user_id = $_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$actions = explode("__",$_REQUEST['actions']);
function pre($array){
	echo "<pre>";
	print_r($array);	
	echo "</pre>";
}

//--------------------------------------------------------------------------------------------------------------------
 
if ($action=="load_drop_down_location")
{
    extract($_REQUEST);
    $choosenCompany = $choosenCompany;  
	echo create_drop_down( "cbo_location", 110, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id in($choosenCompany) order by location_name","id,location_name", 0, "-- Select --", $selected, "",0 );
	exit();
}

 
if ($action=="load_drop_down_buyer")
{
	extract($_REQUEST); 
	if ($company_id) 
	{
		echo create_drop_down( "cbo_buyer_name", 110, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company_id and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/date_wise_production_reconciliation_report_controller', this.value, 'load_drop_down_season', 'season_td');load_drop_down( 'requires/date_wise_production_reconciliation_report_controller', this.value, 'load_drop_down_brand', 'brand_td');" );
	}
	else
	{
		echo create_drop_down( "cbo_buyer_name", 110, $blank_array,"", 1, "-- All --", $selected, "",1,"" );
	}   	 
	exit();
}


if ($action=="load_drop_down_season")
{
	echo create_drop_down( "cbo_season", 110, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-Season-", "", "" );
	exit();
}
if ($action=="load_drop_down_brand")
{
	echo create_drop_down( "cbo_brand", 110, "select id, brand_name from lib_buyer_brand brand where buyer_id='$data' and status_active =1 and is_deleted=0 $brand_cond order by brand_name ASC","id,brand_name", 1, "--Brand--", $selected, "" );
	exit();
}

if($action	==	"job_style_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);  
	?>

	<script>
		var selected_id = new Array();
		var selected_name = new Array();
		var selected_attach_id = new Array();
		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			if ( x.style )
			{
				x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
			}
		}

		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click');
			}
		}

		function js_set_value(id,popupFor)
		{ 
			var str=id.split("_");
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
			var strdt=str[2];
			str=str[1];

			if( jQuery.inArray(  str , selected_id ) == -1 ) {
				selected_id.push( str );
				selected_name.push( strdt );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str  ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i,1 );
			}
			var id = '';
			var ddd='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				ddd += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			ddd = ddd.substr( 0, ddd.length - 1 );
			$('#hide_job_id').val( id );
			$('#hide_job_no').val( ddd );
			$('#hide_popup_for').val(popupFor);
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
                            <th>Job Year</th>
                            <th>Search By</th>
                            <th id="search_by_td_up" width="170">Please Enter <?= $popupFor == 1 ? "Job No" : "Style Ref" ?></th>
                            <th>
                                <input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');">
                                <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                                <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                                <input type="hidden" name="hide_popup_for" id="hide_popup_for" value="" />
                            </th>
                        </thead>
                        <tbody>
                            <tr>
                                <td align="center">
                                    <?php
                                    $type = 1;
                                    if($type == 1)
                                        $party="1,3,21,90";
                                    else
                                        $party="80";
										
									//is_disabled
									$is_disabled = ($buyer_name != 0 ? '1' : '0');

                                    echo create_drop_down( "cbo_buyer_name", 140, "SELECT buy.id, buy.buyer_name FROM lib_buyer buy, lib_buyer_tag_company b WHERE buy.status_active =1 AND buy.is_deleted=0 AND b.buyer_id=buy.id AND b.tag_company IN (".$companyID.") AND buy.id IN (SELECT buyer_id FROM lib_buyer_party_type WHERE party_type IN (".$party.")) GROUP BY buy.id, buy.buyer_name ORDER BY buy.buyer_name","id,buyer_name", "1", "-- All Buyer--",$buyer_name, "", $is_disabled);
                                    ?>
                                </td>
                                <td>
                                    <?
                                        echo create_drop_down( "txt_job_year", 80, $year,"", 1, "-- Select year --", date('Y'), "","");
                                    ?>
                                </td> 
                                <td align="center">
                                    <? 
									$search_by_arr=array(1=>"Job No",2=>"Style Ref"); 
                                    $dd="change_search_event(this.value, '0*0', '0*0', '../') ";
                                    echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", $popupFor,$dd,0 );
                                    ?>
                                </td>
                                <td align="center" id="search_by_td">
									
                                    <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
                                </td>
                                <td align="center">
                                    <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<?php echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_job_year').value+'**'+'<? echo $cbo_month_id; ?>'+'**'+'<? echo $party; ?>'+'**'+'<?= $popupFor?>', 'create_job_no_search_list_view', 'search_div', 'date_wise_production_reconciliation_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div style="margin-top:15px" id="search_div"></div>
                </fieldset>
            </form>
        </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
} 
if($action	==	"create_job_no_search_list_view")
{
	$data = explode('**',$data);
	$company_id = $data[0];
	$year_id = $data[4];
	$month_id = $data[5];
	$party = $data[6];
	$popupFor = $data[7]; 
	if ($popupFor == 1) //For job
	{
		$set_column_data = 'job_no_prefix_num'; 
	} 
	if ($popupFor == 2) //For Style
	{
		$set_column_data = 'style_ref_no'; 
	}

	/*
	|--------------------------------------------------------------------------
	| buyer checking
	|--------------------------------------------------------------------------
	|
	*/
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
				$buyer_id_cond=" AND buyer_name IN (".$_SESSION['logic_erp']["buyer_id"].")";
			else
				$buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" AND buyer_name = ".$data[1]."";
	}

	$search_by = $data[2];
	$search_string = "%".trim($data[3])."%";

	if($search_by == 2)
		$search_field = "style_ref_no";
	else
		$search_field = "job_no";

	if($db_type == 0)
	{
		if($year_id != 0)
			$year_search_cond = " AND year(insert_date) = ".$year_id."";
		else
			$year_search_cond = "";
		$year_cond = "year(insert_date) AS year";
	}
	else if($db_type==2)
	{
		if($year_id != 0)
			$year_search_cond = " AND TO_CHAR(insert_date,'YYYY') = ".$year_id."";
		else
			$year_search_cond="";
		$year_cond = "TO_CHAR(insert_date,'YYYY') AS year";
	}
	
	$company_arr=return_library_array( "SELECT id, company_name FROM lib_company WHERE id IN(".$company_id.")", "id", "company_name" );
	$buyer_arr=return_library_array( "SELECT buy.id, buy.buyer_name FROM lib_buyer buy, lib_buyer_tag_company b WHERE buy.status_active =1 AND buy.is_deleted=0 AND b.buyer_id=buy.id AND b.tag_company IN (".$company_id.") AND buy.id IN (SELECT buyer_id FROM lib_buyer_party_type WHERE party_type IN (".$party.")) GROUP BY buy.id, buy.buyer_name ORDER BY buy.buyer_name","id", "buyer_name");
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	
	$sql= "SELECT id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, ".$year_cond." FROM wo_po_details_master WHERE status_active=1 AND is_deleted=0 AND company_name IN(".$company_id.") AND ".$search_field." LIKE '".$search_string."' ".$buyer_id_cond." ".$year_search_cond." ".$month_cond." ORDER BY job_no DESC";
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,80,80,60","620","270",0, $sql , "js_set_value", "id,$set_column_data", "$popupFor", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','',1) ;
	exit();
}  

if($action=="report_generate")
{ 
	
	
	$process = array( &$_POST );
	// pre($_POST); die;
	extract(check_magic_quote_gpc( $process ));
	
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );  
	
	// pre($buyer_library); die;
	
	// ================================= GETTING FORM DATA ====================================
	$company_id 		=str_replace("'","",$cbo_company_name);
	$wo_company_id 		=str_replace("'","",$cbo_com_fac_name);
	$location_id 		=str_replace("'","",$cbo_location);
	$buyer_id 			=str_replace("'","",$cbo_buyer_name); 
	$brand_id 			=str_replace("'","",$cbo_brand);
	$season_id 			=str_replace("'","",$cbo_season);
	$season_year 		=str_replace("'","",$cbo_season_year);
	$style_ref 			=str_replace("'","",$txt_style_ref); 
	$date_type		 	=str_replace("'","",$cbo_search_by); 
	$date_from 			=str_replace("'","",$txt_date_from);
	$date_to 			=str_replace("'","",$txt_date_to);	
	$delivery_status 	=str_replace("'","",$delivery_status); 

	if($type==1) // show button 
	{ 
		
		//******************************************* MAKE QUERY CONDITION ************************************************
		$sql_cond  = "";
		$cut_cond  = ""; 
		$prod_cond = "";  
		$sql_cond .= ($company_id==0) 			? "" : " and a.company_name in($company_id) ";
		$sql_cond .= ($wo_company_id==0) 		? "" : " and d.delivery_company_id in ($wo_company_id) ";
		$cut_cond .= ($wo_company_id==0) 		? "" : " and b.working_company_id in ($wo_company_id) ";
		$prod_cond.= ($wo_company_id==0) 		? "" : " and a.serving_company in ($wo_company_id) ";
		$sql_cond .= ($location_id==0) 			? "" : " and a.location_name in ($location_id) ";
		$sql_cond .= ($buyer_id==0) 			? "" : " and a.buyer_name=$buyer_id ";
		$sql_cond .= ($brand_id==0) 			? "" : " and a.brand_id=$brand_id ";
		$sql_cond .= ($season_id==0) 			? "" : " and a.season=$season_id ";
		$sql_cond .= ($season_year==0) 			? "" : " and a.season_year=$season_year ";
		$sql_cond .= ($style_ref=='') 			? "" : " and a.style_ref_no in($txt_style_ref) ";
		$sql_cond .= ($delivery_status=='') 	? "" : " and c.shiping_status=$delivery_status ";
		if ($date_type == 1) 
		{
			$sql_cond .=  ($date_from && $date_to) 	?" and b.pub_shipment_date between '".change_date_format($date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($date_to,'dd-mm-yyyy','-',1)."'"  : "";
		}
		else if ($date_type == 2) 
		{
			$sql_cond .=  ($date_from && $date_to) 	?" and c.ex_factory_date between '".change_date_format($date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($date_to,'dd-mm-yyyy','-',1)."'"  : "";
		}

		// echo $sql_cond;die();
			
		// ================================================ MAIN / EX-FACTORY  QUERY==================================================
		$sql="select a.buyer_name,a.season_buyer_wise as season,a.style_ref_no as style,a.job_no,a.ship_mode,b.id as po_id,b.po_number,b.po_quantity,b.sc_lc,c.ex_factory_qnty,c.ex_factory_date,c.remarks from wo_po_details_master a, wo_po_break_down b,pro_ex_factory_mst c,pro_ex_factory_delivery_mst d where a.id=b.job_id and b.id=c.po_break_down_id and d.id=c.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $sql_cond order by c.ex_factory_date desc";
		// echo $sql; die;
		$sql_res = sql_select($sql);
		if (count($sql_res) == 0 ) {
			echo "<h1 style='color:red; font-size: 17px;'> ** Data Not Found ** </h1>" ;
			die();
		}
		// pre($sql_res); die;
		$data_array = array();
		$po_id_arr = array();
		$buyer_id_arr = array();
		foreach ($sql_res as $v) 
		{
			$po_id_arr[$v['PO_ID']] = $v['PO_ID'];
			$buyer_id_arr[$v['BUYER_NAME']] = $v['BUYER_NAME'];

			$data_array[$v['PO_ID']]['BUYER'] = $v['BUYER_NAME'];
			$data_array[$v['PO_ID']]['JOB_NO'] = $v['JOB_NO'];
			$data_array[$v['PO_ID']]['STYLE'] = $v['STYLE'];
			$data_array[$v['PO_ID']]['SEASON'] = $v['SEASON'];
			$data_array[$v['PO_ID']]['PO_NUMBER'] = $v['PO_NUMBER'];
			$data_array[$v['PO_ID']]['PO_QUANTITY'] = $v['PO_QUANTITY'];
			$data_array[$v['PO_ID']]['SHIPMENT_QTY'] += $v['EX_FACTORY_QNTY'];
			$data_array[$v['PO_ID']]['SHIP_MODE'] = $v['SHIP_MODE'];
			$data_array[$v['PO_ID']]['SC_LC'] = $v['SC_LC'];
			$data_array[$v['PO_ID']]['REMARKS'] = $v['REMARKS'];
			if(!$data_array[$v['PO_ID']]['SHIP_DATE']){
				$data_array[$v['PO_ID']]['SHIP_DATE'] = $v['EX_FACTORY_DATE'];
			}
		} 
		// pre($po_id_arr); die;
			
		//=================================== DELETE PO ID FROM TEMP ENGINE ====================================
		$con = connect();
		execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form = 80 and ref_from in (1,2)");
    	oci_commit($con);   
		//=================================== INSERT PO ID INTO TEMP ENGINE ====================================

		fnc_tempengine("gbl_temp_engine", $user_id, 80, 1,$po_id_arr, $empty_arr);  
		fnc_tempengine("gbl_temp_engine", $user_id, 80, 2,$buyer_id_arr, $empty_arr);  

		//=================================== SEASON LIBRARY ARRAY ====================================
		$season_library=return_library_array( "select a.id,a.season_name from lib_buyer_season a, gbl_temp_engine tmp  where buyer_id=tmp.ref_val and a.status_active =1 and a.is_deleted=0 and tmp.entry_form=80 and tmp.ref_from=2 and tmp.user_id=$user_id", "id", "season_name"  );  

	/* 	//=================================== CUT LAY QUERY ====================================
		$cut_lay_sql="select a.order_id,a.size_qty from ppl_cut_lay_bundle a, ppl_cut_lay_mst b, gbl_temp_engine tmp where a.order_id=tmp.ref_val and a.mst_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and tmp.entry_form=80 and tmp.ref_from=1 and tmp.user_id=$user_id $cut_cond";
		// echo $cut_lay_sql;die();
		$cut_lay_res = sql_select($cut_lay_sql);
		$cutting_array = array();
		foreach ($cut_lay_res as $v) 
		{
			$cutting_array[$v['ORDER_ID']] += $v['SIZE_QTY'];
		} */
		// pre($cutting_array); die;
		//=================================== PRODUCTION QUERY ====================================
		$prod_sql="select a.po_break_down_id as po_id, b.production_qnty as prod_qty,a.production_type as prod_type,a.embel_name,b.reject_qty from pro_garments_production_mst a, pro_garments_production_dtls b, gbl_temp_engine tmp where a.id=b.mst_id and a.po_break_down_id=tmp.ref_val and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_type in(1,2,3,5,11,80) and tmp.entry_form=80 and tmp.ref_from=1 and tmp.user_id=$user_id $prod_cond";
		// and a.entry_form=121
		// echo $prod_sql;die();
		$prod_sql_res = sql_select($prod_sql);
		$prod_array = array();
		foreach ($prod_sql_res as $v) 
		{
			// $cutting_array[$v['PO_ID']] += $v['CUT_QTY'];

			$prod_array[$v['PO_ID']][$v['PROD_TYPE']][$v['EMBEL_NAME']] += $v['PROD_QTY'];
			$prod_array[$v['PO_ID']][$v['PROD_TYPE']]['REJECT_QNTY'] += $v['REJECT_QTY'];
		}
		// echo $prod_array[7180][1][0]; die;
		// pre($prod_array); die;
		//=================================== REJECT REASON QUERY ====================================
		$reject_sql = "select a.id,a.po_break_down_id as po_id,b.reject_qnty,b.reject_type from independent_finish_mst a,independent_finish_dtls b, gbl_temp_engine tmp where a.id=b.mst_id and a.po_break_down_id=tmp.ref_val and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and tmp.entry_form=80 and tmp.ref_from=1 and tmp.user_id=$user_id";
		// echo $reject_sql;die();
		$reject_sql_res = sql_select($reject_sql);
		$reject_arr = array();
		$gt_reject_arr = array();
		foreach ($reject_sql_res as $v) { 
			$reject_arr[$v['PO_ID']][$v['REJECT_TYPE']]['REJECT_QTY'] = $v['REJECT_QNTY']; 
			$gt_reject_arr[$v['REJECT_TYPE']]['REJECT_QTY'] += $v['REJECT_QNTY'];  
		}
		// pre($sew_fin_reject_type_for_arr); die('**');
		//=================================== DELETE PO ID FROM TEMP ENGINE ====================================
		execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form=80 and ref_from in(1,2)");
    	oci_commit($con);  
		disconnect($con);


		ob_start();
		$width = 2070+ (70* count($independent_fin_gmts_reject_reason_array));
		$width1 = 580;
		?> 
		
		<div>
			<style> 
				.success {color:#42ba96;}
				.danger {color:#FF0000;}
				table td,th{
					word-break: break-word;
				}
				.signature_table{
					margin-top: 100px;
				}
				.signature_table th{
					padding: 5px 35px; 
				}
				.signature_table th p{
					border-top: 1px solid black;
				}

			</style>
			<div>
				<table width="<? echo $width;?>" style="margin: 20px 0;" cellpadding="0" cellspacing="0" id="tbl_caption" align="center">
					<thead class="form_caption" >
						<tr>
						<td colspan="24" align="center" style="font-size:20px; font-weight:bold;" >RECONCILIATION REPORT </td>
						</tr>  
						<tr>
							<td colspan="24" align="center" style="font-size:18px; font-weight:bold;"><?=  $company_library[$company_id]; ?></td>
						</tr>  
						<? 
						if($date_from && $date_to) 
						{
							?>
							<tr>
								<td colspan="24" align="center" style="font-size:18px; font-weight:bold;">
									Form: <?= $txt_date_from ?> To <?= $txt_date_to ?>
								</td>
							</tr> 
							<? 
						} ?>
					</thead>
				</table>
			</div>
			
			<fieldset>   
				<div align="center" style="height:auto; width:<? echo $width+20;?>px; margin:0 auto; padding:0;">  
					<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $width;?>" rules="all" id="rpt_table_header" align="left">
						<thead class="form_caption" >	  
							<tr>
								<th width="30" rowspan="2">Sl.</th>
								<th width="720" colspan="8">BUYER & ORDER DETAILS </th>
								<th width="180" colspan="3">CUTTING STATUS </th>
								<th width="240" colspan="4">PRODUCTION STATUS </th>
								<th width="630" colspan="7">SHIPMENT STATUS </th>
								<th width="1140" colspan="19">AFTER SHIPMET BALANCE QTY STATUS</th>
								<th width="90" rowspan="2">MERCHANDISING REMARKS</th>
							</tr>
							<tr> 
								<th width="90">BUYER </th>
								<th width="90">L/C / CONTRACT</th>
								<th width="90">SEASON </th> 
								<th width="90">JOB</th> 
								<th width="90">STYLE</th> 
								<th width="90">PO</th> 
								<th width="90">ORDER QTY</th> 
								<th width="90">WASH OR NON WASH</th> 

								<th width="60">CUTTING QTY</th> 
								<th width="60" style="border: 1px solid #8DAFDA00 !important;">+/- FM ORD QTY</th> 
								<th width="60">&nbsp;</th> 

								<th width="60">SEWING QTY</th> 
								<th width="60">+/- FM CUT</th> 
								<th width="60">FINISH QTY</th> 
								<th width="60">+/- FM SEWING</th> 
								
								<th width="90">SHIPMENT QTY</th> 
								<th width="90" style="border: 1px solid #8DAFDA00 !important;">ORD TO SHIP</th>    
								<th width="90">&nbsp;</th>
								<th width="90" style="border: 1px solid #8DAFDA00 !important;">CUT TO SHIP</th>    
								<th width="90">&nbsp;</th>
								<th width="90">SHIP MOOD</th>  
								<th width="90">FAC CLOSING DATE</th>  
								
								<th width="60">BAL QTY</th> 
								<?
									foreach ($independent_fin_gmts_reject_reason_array as $key => $val) 
									{
										?>
											<th width="70"><?= $val ?></th>
										<?
									}
								?> 
								<th width="60">TTL </th> 
								<th width="60">MISSING QTY </th>
							</tr>
							<tr></tr>
						</thead>
					</table>
					<div style="width:<?= $width+20;?>px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
						<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body" width="<?= $width; ?>" rules="all" align="left">
							<tbody>
								<?
								$i=$gt_po_qty=$gt_cut_qty=$gt_wash_issue=$gt_wash_rcve=$gt_sew_out=$gt_finish_qty=$gt_ship_qty=$gt_poly_in_hand=$gt_wash_no_rtrn=$gt_wash_rej=$gt_sew_rej=$gt_fin_rej=$gt_oil_spot=$gt_dirty_mark=$gt_fab_fault=$gt_sew_in_complete=$gt_shading=$gt_meas=$gt_rep_damage=$gt_others_1=$gt_others_2=$gt_reason_ttl=0 ; 
								foreach ($data_array as $po_id => $v) 
								{
									$i++;
									$po_qty			= $v['PO_QUANTITY'];
									$cut_qty		= $prod_array[$po_id][1][0] ?? 0;
									$cut_blnc		= $cut_qty - $po_qty;
									$cut_per		= ($cut_blnc/$po_qty)*100;	
									$wash_issue		= $prod_array[$po_id][2][3] ?? 0;
									$wash_rcve		= $prod_array[$po_id][3][3] ?? 0;
									$wash_status	= $wash_rcve ? 'Yes' : 'No';
									$sew_out		= $prod_array[$po_id][5][0] ?? 0;
									$fm_cut			= $sew_out-$cut_qty;
									$finish_qty		= $prod_array[$po_id][80][0] ?? 0;
									$fm_sew			= $finish_qty-$sew_out;
									$ship_qty		= $v['SHIPMENT_QTY'];
									$ord_to_ship	= $ship_qty-$po_qty;
									$ord_to_ship_per= ($ord_to_ship/$cut_qty)*100;
									$cut_to_ship	= $ship_qty-$cut_qty;
									$cut_to_ship_per= ($ship_qty*100)/$cut_qty;
									$bal_qty		= $cut_qty-$ship_qty;
									$wash_no_rtrn	= $wash_issue-$wash_rcve;  

									// Grand Total
									$gt_po_qty 			+= $po_qty;
									$gt_cut_qty 		+= $cut_qty;  
									$gt_wash_issue		+= $wash_issue;
									$gt_wash_rcve		+= $wash_rcve;
									$gt_sew_out			+= $sew_out;
									$gt_finish_qty		+= $finish_qty; 
									$gt_ship_qty		+=$ship_qty;
									if ($i % 2 == 0)  $bgcolor = "#E9F3FF";  else $bgcolor = "#FFFFFF";
									?>
										<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
											<td width="30" align='center' ><?= $i ?></td>

											<td width="90"><?= $buyer_library[$v['BUYER']] ?></td>
											<td width="90" align='right' ><?= $v['SC_LC'] ?></td>
											<td width="90" align='left' ><?= $season_library[$v['SEASON']] ?></td>  
											<td width="90"><?= $v['JOB_NO'] ?></td>  
											<td width="90"><?= $v['STYLE'] ?></td>  
											<td width="90"><?= $v['PO_NUMBER'] ?></td> 
											<td width="90" align='right' ><?= $po_qty ?></td> 
											<td width="90" align='center' ><?= $wash_status ?></td>

											<td width="60" align='right' title="QC PASS + CUTTING ENTRY"><?= $cut_qty ?></td>
											<td width="60" align='right' title="CUTTING QTY - ORDER QTY"><?= $cut_blnc ?></td>
											<td width="60" align='right' title="(FM ORD QTY / ORDER QTY)*100"><?= number_format($cut_per,2) ?>%</td>

											<td width="60" align='right' ><?= $sew_out ?></td>
											<td width="60" align='right' ><?= $fm_cut ?></td>
											<td width="60" align='right' ><?= $finish_qty ?></td>
											<td width="60" align='right' title="FINISH QTY - SEWING QTY"><?= $fm_sew ?></td>

											<td width="90" align='right' ><?= $ship_qty ?></td>
											<td width="90" align='right' title="SHIPMENT QTY - ORDER QTY"><?= $ord_to_ship ?></td>
											<td width="90" align='right' title="(ORD TO SHIP / CUTTING QTY) * 100"><?= number_format($ord_to_ship_per,2) ?>%</td>
											<td width="90" align='right' title="SHIPMENT QTY - CUTTING QTY"><?= $cut_to_ship ?></td>
											<td width="90" align='right' title="(SHIPMENT QTY * 100) / CUTTING QTY"><?= number_format($cut_to_ship_per,2) ?>%</td>
											<td width="90" align='center' ><?= $shipment_mode[$v['SHIP_MODE']] ?></td> 
											<td width="90" align='left' ><?= $v['SHIP_DATE'] ?></td> 

											<td width="60" align='right' title="CUTTING QTY - SHIPMENT QTY"><?= $bal_qty ?></td>
											<?
												$po_rej_ttl = 0;
												foreach ($independent_fin_gmts_reject_reason_array as $reason_id => $val) 
												{
													$reject_qty = $reject_arr[$po_id][$reason_id]['REJECT_QTY'];
													$po_rej_ttl	+=$reject_qty;
													$gt_po_rej_ttl +=$reject_qty;

													?>
														<td width="70" align='right' ><?=$reject_qty ?? 0?></td> 
													<?
												}
											?> 
											<td width="60" align='right' ><?= $po_rej_ttl ?></td>
											<td width="60" align='right' title="BAL QTY - TTL"><?= $bal_qty -$po_rej_ttl ?></td>
											
											<td width="90" align='right' ><?= $v['REMARKS'] ?></td>
										</tr> 
									<?
								}
								?>
							</tbody>
						</table> 
					</div> 
					<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $width;?>" rules="all" id="rpt_table_header" align="left">
						<footer>
							<?
								$gt_cut_blnc		= $gt_cut_qty - $gt_po_qty;
								$gt_fm_sew			= $gt_finish_qty-$gt_sew_out;
								$gt_fm_cut			= $gt_sew_out-$gt_cut_qty;
								$gt_cut_per			= ($gt_cut_blnc/$gt_po_qty)*100;	
								$gt_ord_to_ship		= $gt_ship_qty-$gt_po_qty;
								$gt_ord_to_ship_per	= ($gt_ord_to_ship/$gt_cut_qty)*100;
								$gt_cut_to_ship		= $gt_ship_qty-$gt_cut_qty;
								$gt_cut_to_ship_per	= ($gt_ship_qty*100)/$gt_cut_qty;
								$gt_bal_qty			= $gt_cut_qty-$gt_ship_qty; 
								$gt_missing_qty		= $gt_bal_qty - $gt_po_rej_ttl;
							?>	  
							<tr bgcolor="#eed8d8">
								<th width="30"> </th>

								<th width="90"></th>
								<th width="90"></th>
								<th width="90"></th>
								<th width="90">TOTAL</th>  
								<th width="90"></th>  
								<th width="90"></th> 
								<th width="90" align='right' ><?= $gt_po_qty ?></th> 
								<th width="90" align='center' ></th>

								<th width="60" align='right' ><?= $gt_cut_qty ?></th>
								<th width="60" align='right' ><?= $gt_cut_blnc ?></th>
								<th width="60" align='right' ><?= number_format($gt_cut_per,2) ?>%</th>

								<th width="60" align='right' ><?= $gt_sew_out ?></th>
								<th width="60" align='right' ><?= $gt_fm_cut ?></th>
								<th width="60" align='right' ><?= $gt_finish_qty ?></th>
								<th width="60" align='right' ><?= $gt_fm_sew ?></th>

								<th width="90" align='right' ><?= $gt_ship_qty ?></th>
								<th width="90" align='right' ><?= $gt_ord_to_ship ?></th>
								<th width="90" align='right' ><?= number_format($gt_ord_to_ship_per,2) ?>%</th>
								<th width="90" align='right' ><?= $gt_cut_to_ship ?></th>
								<th width="90" align='right' ><?= number_format($gt_cut_to_ship_per,2) ?>%</th>
								<th width="90" ></th> 
								<th width="90" ></th> 

								<th width="60" align='right' ><?= $gt_bal_qty ?></th>
								<?
									foreach ($independent_fin_gmts_reject_reason_array as $reason_id => $val) 
									{
										$reject_qty = $gt_reject_arr[$reason_id]['REJECT_QTY']; 
										?>
											<td width="70" align='right' ><?=$reject_qty ?? 0?></td> 
										<?
									}
								?>
								<th width="60" align='right' ><?= $gt_po_rej_ttl ?></th>
								<th width="60" align='right' ><?= $gt_missing_qty ?></th>
												
								<th width="90" align='right' ></th>
							</tr> 
						</footer>
					</table>
				</div>
			</fieldset>
			<!-- Summary Part -->
			<div style="display: flex; justify-content:left;">
				<fieldset style="margin-top: 20px; width:<? echo $width1+20;?>px;" align="left">  
					<div align="left" style="height:auto; width:<? echo $width1+20;?>px; margin-top:20px; padding:0;">   
						<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body" width="<?= $width1; ?>" rules="all" align="left">
							<thead class="form_caption" >	  
								<tr>
									<th  colspan="5"style="text-align:left;color: #0172BE;" >SUMMERY:</th>   
								</tr>	  
								<tr>
									<th width="30">Sl.</th>
									<th align="center" width="280">DESCRIPTION</th>
									<th width="80">QTY</th>
									<th width="80">%</th>
									<th width="130">REMARKS</th>  
								</tr>  
							</thead>			
							<tbody>
								<tr>
									<td align="center">1</td>
									<td align="center">ORDER QTY</td>
									<td align='right'><?= $gt_po_qty ?></td>
									<td></td>
									<td></td>  
								</tr>  
								<tr>
									<td align="center">2</td>
									<td align="center">CUTTING QTY</td>
									<td align='right'><?= $gt_cut_qty ?></td>
									<td></td>
									<td></td>  
								</tr>  
								<tr>
									<td align="center">3</td>
									<td align="center">% On Order Qty</td>
									<td align='right'><?= $gt_cut_blnc ?></td>
									<td align='right'><?= number_format($gt_cut_per,2) ?>%</td>
									<td></td>  
								</tr>  
								<tr>
									<td align="center">4</td>
									<td align="center">SHIPMENT QTY</td>
									<td align='right'><?= $gt_ship_qty ?></td>
									<td></td>
									<td></td>  
								</tr>  
								<tr>
									<td align="center">5</td>
									<td align="center">ORD TO SHIP</td>
									<td align='right'><?= $gt_ship_qty ?></td>
									<td align='right'><?= number_format($gt_ord_to_ship_per,2) ?>%</td>
									<td></td>  
								</tr>  
								<tr>
									<td align="center">6</td>
									<td align="center">CUT TO SHIP</td>
									<td align='right'><?= $gt_cut_to_ship ?></td>
									<td align='right'><?= number_format($gt_cut_to_ship_per,2) ?>%</td>
									<td align="center">% OF CUT TO SHIP</td>  
								</tr>  
								<tr>
									<td align="center">7</td>
									<td align="center">BAL QTY</td>
									<td align='right'><?= $gt_bal_qty ?></td>
									<td align='right'><?= number_format(($gt_bal_qty*100)/$gt_cut_qty,2) ?></td>
									<td align="center">% On Cutting Qty</td>  
								</tr>  
							</tbody>
						</table>  
					</div>
				</fieldset>
			</div>
			<!-- TOTAL REJECT SUMMERY -->
			<div style="display: flex; justify-content:left;">
				<fieldset style="margin-top: 20px; width:<? echo $width1+20;?>px;" align="left">	
					<div align="left" style="height:auto; width:<? echo $width1+20;?>px; margin-top:20px; padding:0;">   
						<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body" width="<?= $width1; ?>" rules="all" align="left">
							<thead class="form_caption" >
								<tr>
									<th  colspan="5"style="text-align:left;color: #0172BE;" >TOTAL REJECT SUMMERY:</th>   
								</tr>	  
								<tr>
									<th width="30">Sl.</th>
									<th align="center" width="280"></th>
									<th width="80">QTY</th>
									<th width="80">%</th>
									<th width="130">REMARKS</th>  
								</tr>  
							</thead>			
							<tbody>
								<?

									$gt_poly_in_hand= $gt_reject_arr[2]['REJECT_QTY'] ?? 0;
									$gt_wash_rej	= $gt_reject_arr[5]['REJECT_QTY'] ?? 0;
									$gt_wash_no_rtrn= $gt_reject_arr[4]['REJECT_QTY'] ?? 0;
									$gt_sew_rej 	= $gt_reject_arr[6]['REJECT_QTY'] ?? 0;
									$gt_fin_rej 	= $gt_reject_arr[7]['REJECT_QTY'] ?? 0;

									$gt_poly_per		= ($gt_poly_in_hand*100)/ $gt_cut_qty;
									$gt_wash_rej_per	= ($gt_wash_rej*100)/ $gt_cut_qty;
									$gt_wash_no_rtrn_per= ($gt_wash_no_rtrn*100)/ $gt_cut_qty;
									$gt_sew_rej_per		= ($gt_sew_rej*100)/ $gt_cut_qty;
									$gt_fin_rej_per		= ($gt_fin_rej*100)/ $gt_cut_qty;
								?>
								<tr>
									<td align="center">1</td>
									<td align="center">Sample</td>
									<td ></td>
									<td ></td>
									<td align="center">% On Total Cutting Qty</td>  
								</tr>  
								<tr>
									<td align="center">2</td>
									<td align="center">POLY IN HAND</td>
									<td align='right'><?= $gt_poly_in_hand ?>  
									<td align='right'><?= number_format($gt_poly_per,2) ?>%</td> 
									<td align="center">% On Total Cutting Qty</td>  
								</tr>  
								<tr>
									<td align="center">3</td>
									<td align="center">WASH REJECT</td>
									<td align='right'><?= $gt_wash_rej ?>
									<td align='right' style="<?= ($gt_wash_rej_per>0.5)?"color:red;":"" ?>"><?= number_format($gt_wash_rej_per,2) ?>%</td>
									<td align="center" style="<?= ($gt_wash_rej_per>0.5)?"color:red;":"" ?>"><?= ($gt_wash_rej_per>0.5)?"DN To Wash":"OK" ?></td>  
								</tr>  
								<tr>
									<td align="center">4</td>
									<td align="center">WASH NO RETURN</td>
									<td align='right' ><?= $gt_wash_no_rtrn ?> 
									<td align='right' style="<?= ($gt_wash_no_rtrn_per>0.5)?"color:red;":"" ?>"><?= number_format($gt_wash_no_rtrn_per,2) ?>%</td>
									<td align="center" style="<?= ($gt_wash_no_rtrn_per>0.5)?"color:red;":"" ?>"><?= ($gt_wash_no_rtrn_per>0.5)?"DN To Wash":"OK" ?></td>  
								</tr>  
								<tr>
									<td align="center">5</td>
									<td align="center">SEW REJECT</td>
									<td align='right'><?= $gt_sew_rej ?></td> 
									<td align='right'><?= number_format($gt_sew_rej_per,2) ?>%</td> 
									<td align="center">% On Total Cutting Qty</td>  
								</tr>  
								<tr>
									<td align="center">6</td>
									<td align="center">FIN REJECT</td>
									<td align='right'><?= $gt_fin_rej ?></td> 
									<td align='right'><?= number_format($gt_fin_rej_per,2) ?>%</td> 
									<td align="center">% On Total Cutting Qty</td>  
								</tr>   
							</tbody>
						</table>  
					</div>
				</fieldset>
			</div>
			<!-- SIGNATURE -->
			<div style="display: flex; justify-content:left;">
				<table class="signature_table">
					<tr>
						<th class="text-center"> <p> CUTTING APM </p></th>
						<th class="text-center"> <p> SEWING APM </p></th>
						<th class="text-center"> <p> FINISHING APM (2ND) </p></th>
						<th class="text-center"> <p> FINISHING APM (6TH) </p></th>
						<th class="text-center"> <p> ASST. MAN. QUALITY </p></th>
						<th class="text-center"> <p> DGM PRODUCTION </p></th>
						<th class="text-center"> <p> GM PRODUCTION </p></th>
					</tr>
				</table>
			</div>
		</div>
	   <? 
	}	


	$html = ob_get_contents();
	ob_clean(); 

	// echo "$html####";
	foreach (glob($user_id."_*.xls") as $filename)
	{		
		@unlink($filename);
	}
	$name=$user_id."_".time().".xls";
	$create_new_excel = fopen($name, 'w');	
	$is_created = fwrite($create_new_excel,$html);
	echo $html."####".$name;
	exit();	
}

?>

