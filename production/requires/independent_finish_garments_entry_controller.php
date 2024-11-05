<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//echo $action;
//die;
if(!function_exists('pre'))
{
    function pre($array)
    {
        echo "<pre>";
        print_r($array);
        echo "</pre>";
    }
}
/*
|--------------------------------------------------------------------------
| Library Array
|--------------------------------------------------------------------------
|
*/
$buyer_arr		  = return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
$company_arr 	  = return_library_array( "select id,company_name  from  lib_company", "id", "company_name"  ); 
$location_arr	  = return_library_array( "select id,location_name  from  lib_location", "id", "location_name"  );  

if ($action=="load_drop_down_buyer")
{  
	echo create_drop_down( "cbo_buyer_name", 150, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "- All Buyer -", $selected, "" );     	 
	exit();
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 150, "select id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id in('$data') 
	order by location_name","id,location_name", 1, "-- Select Location --", $selected,"",0 );    	 
}
/*
|--------------------------------------------------------------------------
| create_job_no_search_list_view
|--------------------------------------------------------------------------
|
*/
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

	if($search_by == 1)
	{
		$search_field = "a.job_no";
	}
	if($search_by == 2)
	{
		$search_field = "a.style_ref_no";
	}
	else
	{
		$search_field = "b.po_number";
	}

	if($year_id != 0)
		$year_search_cond = " AND TO_CHAR(a.insert_date,'YYYY') = ".$year_id."";
	else
		$year_search_cond="";
	$year_cond = "TO_CHAR(a.insert_date,'YYYY') AS year";
	
	
	$company_arr=return_library_array( "SELECT id, company_name FROM lib_company WHERE id IN(".$company_id.")", "id", "company_name" );
	$buyer_arr=return_library_array( "SELECT buy.id, buy.buyer_name FROM lib_buyer buy, lib_buyer_tag_company b WHERE buy.status_active =1 AND buy.is_deleted=0 AND b.buyer_id=buy.id AND b.tag_company IN (".$company_id.") AND buy.id IN (SELECT buyer_id FROM lib_buyer_party_type WHERE party_type IN (".$party.")) GROUP BY buy.id, buy.buyer_name ORDER BY buy.buyer_name","id", "buyer_name");
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	
	$sql= "SELECT a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, ".$year_cond." FROM wo_po_details_master a,wo_po_break_down b WHERE a.id=b.job_id AND a.status_active=1 AND a.is_deleted=0  AND b.status_active=1 AND b.is_deleted=0 AND a.company_name IN(".$company_id.") AND ".$search_field." LIKE '".$search_string."' ".$buyer_id_cond." ".$year_search_cond." ".$month_cond." ORDER BY a.job_no DESC";
	// echo $sql;
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,80,80,60","620","270",0, $sql , "js_set_value", "id,$set_column_data", "$popupFor", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','',1) ;
	exit();
} 
/*
|--------------------------------------------------------------------------
| job_no_popup
|--------------------------------------------------------------------------
|
*/
if($action	==	"job_style_popup")
{
	echo load_html_head_contents("Job Info", "../../", 1, 1,'','','');
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
									$search_by_arr=array(1=>"Job No",2=>"Style Ref",3=>"Order no"); 
                                    $dd="change_search_event(this.value, '0*0*0', '0*0*0', '../') ";
                                    echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", $popupFor,$dd,0 );
                                    ?>
                                </td>
                                <td align="center" id="search_by_td">
									
                                    <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
                                </td>
                                <td align="center">
                                    <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<?php echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_job_year').value+'**'+'<? echo $cbo_month_id; ?>'+'**'+'<? echo $party; ?>'+'**'+'<?= $popupFor?>', 'create_job_no_search_list_view', 'search_div', 'independent_finish_garments_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div style="margin-top:15px" id="search_div"></div>
                </fieldset>
            </form>
        </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
} 

/*
|--------------------------------------------------------------------------
| Order List
|--------------------------------------------------------------------------
|
*/
if ($action == 'order_list') 
{ 
	extract($_REQUEST);
	$company_id = 	str_replace("'",'',$cbo_company_name); 
    $location_id=str_replace("'","",$cbo_location);
	$buyer_id 	= 	str_replace("'",'',$cbo_buyer_name);  
	$job 		= 	str_replace("'",'',$txt_job_no); 
	$style 		= 	str_replace("'",'',$txt_style_no); 
    $form_date=str_replace("'","",$txt_date_from); 
	$to_date=str_replace("'","",$txt_date_to); 


	
	// ============================================================================================================
	//												Conditions 
	// ============================================================================================================
	$cond  = ""; 
	$prod_sql_cond  = ""; 
	$cond .= ($company_id)  ? " and a.company_name=$company_id" : "";
	$cond .= ($location_id) ? " and a.location_name=$location_id" : "";
	$cond .= ($job)         ? " and a.job_no_prefix_num in($job)" : "";
	$cond .= ($style)       ? " and a.style_ref_no in($txt_style_no)" : "";
	$cond .= ($buyer_id)    ? " and a.buyer_name=$buyer_id" : ""; 
	$cond .=  ($form_date && $to_date) ?" and b.pub_shipment_date between '".change_date_format($form_date,'dd-mm-yyyy','-',1)."' and '".change_date_format($to_date,'dd-mm-yyyy','-',1)."'"  : ""; 
	
	$sql = "select a.buyer_name,a.job_no,a.id as job_id,a.style_ref_no,b.id as po_id,b.po_number,b.pub_shipment_date,c.order_quantity from wo_po_details_master a,wo_po_break_down b, wo_po_color_size_breakdown c where a.id=b.job_id and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $cond"; 
	// echo $sql;die;
	$sql_res = sql_select($sql);
	$data_arr = array();
	$po_id_arr = array();
	foreach ($sql_res as $v) 
	{
		$po_id_arr [$v['PO_ID']]= $v['PO_ID'];
		$data_arr[$v['PO_ID']]['BUYER_NAME'] 	= $v['BUYER_NAME'];
		$data_arr[$v['PO_ID']]['JOB_NO'] 		= $v['JOB_NO'];
		$data_arr[$v['PO_ID']]['STYLE_REF_NO']	= $v['STYLE_REF_NO'];
		$data_arr[$v['PO_ID']]['PO_NUMBER']		= $v['PO_NUMBER'];
		$data_arr[$v['PO_ID']]['SHIP_DATE']		= $v['PUB_SHIPMENT_DATE'];
		$data_arr[$v['PO_ID']]['PO_QTY']        += $v['ORDER_QUANTITY'];
	} 
	// pre($data_arr);die;

    // ============================================================================================================
	//												CLEAR TEMP ENGINE
	// ============================================================================================================
	$con = connect();
	execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form = 130 and ref_from =1");
	oci_commit($con);  

	// ============================================================================================================
	//												Insert order_id into TEMP ENGINE
	// ============================================================================================================
	fnc_tempengine("gbl_temp_engine", $user_id, 130, 1,$po_id_arr, $empty_arr);  

	// ============================================================================================================
	//												PRODUCTION QUERY
	// ============================================================================================================
    $prod_sql="select a.po_break_down_id as po_id, b.production_qnty as prod_qty,a.production_type as prod_type from pro_garments_production_mst a, pro_garments_production_dtls b, gbl_temp_engine tmp where a.id=b.mst_id and a.po_break_down_id=tmp.ref_val and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_type=1 and tmp.entry_form=130 and tmp.ref_from=1 and tmp.user_id=$user_id"; //a.production_type in(1,80) 
    $prod_sql_res = sql_select($prod_sql);
    $prod_array = array();
    foreach ($prod_sql_res as $v) 
    {  
        $prod_array[$v['PO_ID']][$v['PROD_TYPE']] += $v['PROD_QTY'];
    }

	// ============================================================================================================
	//												GARMENTS DELIVERY ENTRY QUERY
	// ============================================================================================================
    $gmts_del_sql="select a.po_break_down_id as po_id, b.production_qnty as prod_qty from pro_ex_factory_mst a, pro_ex_factory_dtls b, gbl_temp_engine tmp where a.id=b.mst_id and a.po_break_down_id=tmp.ref_val and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and tmp.entry_form=130 and tmp.ref_from=1 and tmp.user_id=$user_id";  
	
    $gmts_del_sql_res = sql_select($gmts_del_sql);
    $gmts_del_array = array();
    foreach ($gmts_del_sql_res as $v) 
    {  
        $gmts_del_array[$v['PO_ID']] += $v['PROD_QTY'];
    }
	// pre( $gmts_del_array); die;

	// ====================================== Existing Reject Data =========================================

	$reject_sql = "select a.id,a.po_break_down_id as po_id,b.reject_qnty,b.reject_type from independent_finish_mst a,independent_finish_dtls b, gbl_temp_engine tmp where a.id=b.mst_id and a.po_break_down_id=tmp.ref_val and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and tmp.entry_form=130 and tmp.ref_from=1 and tmp.user_id=$user_id";
	// echo $reject_sql ;die;
	$reject_sql_res = sql_select($reject_sql);
	$reject_arr = array();
	foreach ($reject_sql_res as $v) 
	{ 
		$reject_arr[$v['PO_ID']][$v['REJECT_TYPE']]['REJECT_QTY'] = $v['REJECT_QNTY']; 
		$data_arr[$v['PO_ID']]['MST_ID'] = $v['ID']; 
		$data_arr[$v['PO_ID']]['TOTAL_REJECT'] += $v['REJECT_QNTY'];  
	}
	// pre($reject_arr); die;
	// ============================================================================================================
	//												CLEAR TEMP ENGINE
	// ============================================================================================================
	execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form = 130 and ref_from =1 ");
	oci_commit($con);  
	disconnect($con);  
	$width =700+ (56* count($independent_fin_gmts_reject_reason_array));
	?>
	<body>
		<fieldset> 
	    	<div align="center" style="height:auto; width:<? echo $width+20;?>px; margin:0 auto; padding:0;">  
				<table	table border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $width;?>" rules="all" id="rpt_table_header" align="left">
					<thead>
						<th width="30"> <p> SL</p></th>
						<th width="80"> <p> Buyer</p></th>
						<th width="80"> <p> Job No</p></th>
						<th width="100"><p> Style Ref</p>.</th>
						<th width="80"> <p> PO</p></th>
						<th width="60"> <p> Shipment Date</p></th>
						<th width="60"> <p> PO Qty</p></th>
						<th width="60"> <p> Cutting Qty.</p></th>
						<th width="60"> <p> Gmts.Del. Qty</p></th>
						<th width="60"> <p> Gmts.Del. Balance</p></th>
						<?
							foreach ($independent_fin_gmts_reject_reason_array as $key => $val) 
							{
								?>
									<th style="width:50px;"><p> <?= $val ?></p></th>
								<?
							}
						?>
						<th style="width:50px;"><p>Total</p></th>
						<th width="40" ><p>Check Box</p></th>
					</thead>
				</table>
				<div style="width:<?= $width+20;?>px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
					<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body" width="<?= $width; ?>" rules="all" align="left">	
						<tbody>
							<form id="fin_gmts_entry_form">
								<?
									$i = 0;
									$is_update = 0;
									foreach ($data_arr as $po_id => $v) 
									{   
										$i++; 
										$body_part_names = '';
										foreach (explode(',', $v['BODY_PART_IDS']) as $k)
										{
											$body_part_names .= $body_part_lib[$k] .',';
											if ($v['MST_ID']) $is_update= 1; 
										}
										if ($i % 2 == 0)  $bgcolor = "#E9F3FF";  else $bgcolor = "#FFFFFF";

										$cutting_qty = $prod_array[$po_id][1]    ?? 0;
										$gmts_del_qty = $gmts_del_array[$po_id] ?? 0;
										// $fnish_qty  = $prod_array[$po_id][80]   ?? 0;
										$fnish_blnce= $cutting_qty - $gmts_del_qty  ;
										?>
											<tr  onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>'); " id="tr_1nd<? echo $i; ?>">
												<td width="30"><p><?= $i ?></p></td>
												<td width="80"><p><?= $buyer_arr[$v['BUYER_NAME']] ?></p></td> 
												<td width="80"><p><?= $v['JOB_NO'] ?></p></td>
												<td width="100"><p><?= $v['STYLE_REF_NO'] ?></p></td> 
												<td width="80"><p><?=$v['PO_NUMBER'] ?></p></td> 
												<td width="60"><p><?=$v['SHIP_DATE'] ?></p></td> 
												<td width="60" align="right"><?=$v['PO_QTY'] ?></td>  
												<td width="60" align="right"><?=$cutting_qty ?></td> 
												<td width="60" align="right"><?=$gmts_del_qty ?></td> 
												<td width="60" align="right" title="<?="Cutting Qty($cutting_qty) - Gmts.Del. Qty.($gmts_del_qty)"?>" class="balance_qty"> <?=$fnish_blnce ?> </td>
												<?
													foreach ($independent_fin_gmts_reject_reason_array as $reason_id => $val) 
													{
														$reject_qty = $reject_arr[$po_id][$reason_id]['REJECT_QTY'];
														?>
															<td style="width:50px;">
																<input  style="width:37px;" type="text"   class="text_boxes_numeric reject_qty" autocomplete="off" name="reject_qty" id="reject_<?= $po_id."_".$reason_id ?>" onkeyup="calculateTotal(this)" value="<?=$reject_qty?>"/>
															</td>
														<?
													}
												?>  
												<td style="width:50px;">
													<input disabled  style="width:37px;" type="text"   class="text_boxes_numeric total_reject" name="total_reject" id="total_reject<?= $i ?>" value="<?= $v['TOTAL_REJECT']?>"/>
												</td>
												<td width="40"  align="center">
													<input type="checkbox" class="checkSingle" value="<?=$po_id?>" id="checkitem_<?=$i;?>">  
													<input style="width:45px;" type="hidden"   class="hidden_mst_id" name="hidden_mst_id" id="hidden_mst_id<?= $i ?>"  value="<?= $v['MST_ID']?>"/>
													</td>
											</tr>
										<? 
										
									}
								?>
							</form>	
							
						</tbody>
					</table>
				</div>	  
			</div>
		</fieldset>	
		<table>
			<tr>
				<td align="center" valign="middle" class="button_container"><?=load_submit_buttons( $permission, "fnc_reject_data_entry", $is_update,0,"refresh_page()",1); ?></td>
			</tr>
		</table>
	</body>  
    </html>
    <?
    exit(); 
} 
/*
|--------------------------------------------------------------------------
| Order List
|--------------------------------------------------------------------------
|
*/
if ($action == 'populate_data_from_search_popup') 
{ 
	extract($_REQUEST);
	$dataArr = explode("**",$data);
	$company_id =$dataArr[0]; 
    $location_id=$dataArr[1];
	$buyer_id 	=$dataArr[2];  
	$job 		=$dataArr[3]; 
	$style 		=$dataArr[4]; 
    $form_date	=$dataArr[5]; 
	$to_date	=$dataArr[6]; 

	
	// ============================================================================================================
	//												Conditions 
	// ============================================================================================================
	$cond  = ""; 
	$prod_sql_cond  = ""; 
	$cond .= ($company_id)  ? " and a.company_name=$company_id" : "";
	$cond .= ($location_id) ? " and a.location_name=$location_id" : "";
	$cond .= ($job)         ? " and a.job_no_prefix_num in($job)" : "";
	$cond .= ($style)       ? " and a.style_ref_no in('$style')" : "";
	$cond .= ($buyer_id)    ? " and a.buyer_name=$buyer_id" : ""; 
	$cond .=  ($form_date && $to_date) ?" and b.pub_shipment_date between '".change_date_format("$form_date",'dd-mm-yyyy','-',1)."' and '".change_date_format("$to_date",'dd-mm-yyyy','-',1)."'"  : ""; 
	
	$sql = "select a.buyer_name,a.job_no,a.id as job_id,a.style_ref_no,b.id as po_id,b.po_number,b.pub_shipment_date,c.order_quantity from wo_po_details_master a,wo_po_break_down b, wo_po_color_size_breakdown c where a.id=b.job_id and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $cond"; 
	// echo $sql;die;
	$sql_res = sql_select($sql);
	$data_arr = array();
	$po_id_arr = array();
	foreach ($sql_res as $v) 
	{
		$po_id_arr [$v['PO_ID']]= $v['PO_ID'];
		$data_arr[$v['PO_ID']]['BUYER_NAME'] 	= $v['BUYER_NAME'];
		$data_arr[$v['PO_ID']]['JOB_NO'] 		= $v['JOB_NO'];
		$data_arr[$v['PO_ID']]['STYLE_REF_NO']	= $v['STYLE_REF_NO'];
		$data_arr[$v['PO_ID']]['PO_NUMBER']		= $v['PO_NUMBER'];
		$data_arr[$v['PO_ID']]['SHIP_DATE']		= $v['PUB_SHIPMENT_DATE'];
		$data_arr[$v['PO_ID']]['PO_QTY']        += $v['ORDER_QUANTITY'];
	} 
	// pre($data_arr);die;

    // ============================================================================================================
	//												CLEAR TEMP ENGINE
	// ============================================================================================================
	$con = connect();
	execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form = 130 and ref_from =1");
	oci_commit($con);  

	// ============================================================================================================
	//												Insert order_id into TEMP ENGINE
	// ============================================================================================================
	fnc_tempengine("gbl_temp_engine", $user_id, 130, 1,$po_id_arr, $empty_arr);  

	// ============================================================================================================
	//												PRODUCTION QUERY
	// ============================================================================================================
    $prod_sql="select a.po_break_down_id as po_id, b.production_qnty as prod_qty,a.production_type as prod_type from pro_garments_production_mst a, pro_garments_production_dtls b, gbl_temp_engine tmp where a.id=b.mst_id and a.po_break_down_id=tmp.ref_val and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_type=1 and tmp.entry_form=130 and tmp.ref_from=1 and tmp.user_id=$user_id"; // a.production_type in(1,80)
    $prod_sql_res = sql_select($prod_sql);
    $prod_array = array();
    foreach ($prod_sql_res as $v) 
    {  
        $prod_array[$v['PO_ID']][$v['PROD_TYPE']] += $v['PROD_QTY'];
    }

	// ============================================================================================================
	//												GARMENTS DELIVERY ENTRY QUERY
	// ============================================================================================================
    $gmts_del_sql="select a.po_break_down_id as po_id, b.production_qnty as prod_qty from pro_ex_factory_mst a, pro_ex_factory_dtls b, gbl_temp_engine tmp where a.id=b.mst_id and a.po_break_down_id=tmp.ref_val and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and tmp.entry_form=130 and tmp.ref_from=1 and tmp.user_id=$user_id";  
	
    $gmts_del_sql_res = sql_select($gmts_del_sql);
    $gmts_del_array = array();
    foreach ($gmts_del_sql_res as $v) 
    {  
        $gmts_del_array[$v['PO_ID']] += $v['PROD_QTY'];
    }
	// pre( $gmts_del_array);die;
	// ====================================== Existing Reject Data =========================================

	$reject_sql = "select a.id,a.po_break_down_id as po_id,b.reject_qnty,b.reject_type from independent_finish_mst a,independent_finish_dtls b, gbl_temp_engine tmp where a.id=b.mst_id and a.po_break_down_id=tmp.ref_val and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and tmp.entry_form=130 and tmp.ref_from=1 and tmp.user_id=$user_id";
	// echo $reject_sql ;die;
	$reject_sql_res = sql_select($reject_sql);
	$reject_arr = array();
	foreach ($reject_sql_res as $v) 
	{ 
		$reject_arr[$v['PO_ID']][$v['REJECT_TYPE']]['REJECT_QTY'] = $v['REJECT_QNTY']; 
		$data_arr[$v['PO_ID']]['MST_ID'] = $v['ID']; 
		$data_arr[$v['PO_ID']]['TOTAL_REJECT'] += $v['REJECT_QNTY'];  
	}
	// pre($reject_arr); die;
	// ============================================================================================================
	//												CLEAR TEMP ENGINE
	// ============================================================================================================
	execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form = 130 and ref_from =1 ");
	oci_commit($con);  
	disconnect($con);  
	$width =700+ (56* count($independent_fin_gmts_reject_reason_array));
	?>
	
	<body>
		<fieldset style="width:1470px;" class="tableFixHead"> 
			<div align="center" style="height:auto; width:<? echo $width+20;?>px; margin:0 auto; padding:0;">  
				<table	table border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $width;?>" rules="all" id="rpt_table_header" align="left">
					<thead>
						<th width="30"> <p> SL</p></th>
						<th width="80"> <p> Buyer</p></th>
						<th width="80"> <p> Job No</p></th>
						<th width="100"><p> Style Ref</p>.</th>
						<th width="80"> <p> PO</p></th>
						<th width="60"> <p> Shipment Date</p></th>
						<th width="60"> <p> PO Qty</p></th>
						<th width="60"> <p> Cutting Qty.</p></th>
						<th width="60"> <p> Gmts.Del. Qty</p></th>
						<th width="60"> <p> Gmts.Del. Balance</p></th>
						<?
							foreach ($independent_fin_gmts_reject_reason_array as $key => $val) 
							{
								?>
									<th style="width:50px;"><p> <?= $val ?></p></th>
								<?
							}
						?>
						<th style="width:50px;"><p>Total</p></th>
						<th width="40" ><p>Check Box</p></th>
					</thead>
				</table>
				<div style="width:<?= $width+20;?>px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
					<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body" width="<?= $width; ?>" rules="all" align="left">
					<tbody>
						<form id="fin_gmts_entry_form">
							<?
								$i = 0;
								$is_update = 0;
								foreach ($data_arr as $po_id => $v) 
								{   
                                    $i++; 
                                    $body_part_names = '';
                                    foreach (explode(',', $v['BODY_PART_IDS']) as $k) {
                                        $body_part_names .= $body_part_lib[$k] .',';
                                        if ($v['MST_ID']) $is_update= 1; 
                                    }
                                    if ($i % 2 == 0)  $bgcolor = "#E9F3FF";  else $bgcolor = "#FFFFFF";

                                    $cutting_qty = $prod_array[$po_id][1]    ?? 0;
                                    $gmts_del_qty = $gmts_del_array[$po_id] ?? 0;
                                    // $fnish_qty  = $prod_array[$po_id][80]   ?? 0;
                                    $fnish_blnce= $cutting_qty - $gmts_del_qty ;
                                    ?>
                                        <tr  onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>'); " id="tr_1nd<? echo $i; ?>">
										    <td width="30"><p><?= $i ?></p></td>
											<td width="80"><p><?= $buyer_arr[$v['BUYER_NAME']] ?></p></td> 
											<td width="80"><p><?= $v['JOB_NO'] ?></p></td>
											<td width="100"><p><?= $v['STYLE_REF_NO'] ?></p></td> 
											<td width="80"><p><?=$v['PO_NUMBER'] ?></p></td> 
											<td width="60"><p><?=$v['SHIP_DATE'] ?></p></td> 
											<td width="60" align="right"><?=$v['PO_QTY'] ?></td>  
											<td width="60" align="right"><?=$cutting_qty ?></td> 
											<td width="60" align="right"><?=$gmts_del_qty ?></td> 
											<td width="60" align="right" title="<?="Cutting Qty($cutting_qty) - Gmts.Del. Qty.($gmts_del_qty)"?>" class="balance_qty"> <?=$fnish_blnce ?> </td>
											<?
												foreach ($independent_fin_gmts_reject_reason_array as $reason_id => $val) 
												{
													$reject_qty = $reject_arr[$po_id][$reason_id]['REJECT_QTY'];
													?>
														<td style="width:50px;">
															<input  style="width:37px;" type="text"   class="text_boxes_numeric reject_qty" autocomplete="off" name="reject_qty" id="reject_<?= $po_id."_".$reason_id ?>" onkeyup="calculateTotal(this)" value="<?=$reject_qty?>"/>
														</td>
													<?
												}
											?>  
											<td style="width:50px;">
												<input disabled  style="width:37px;" type="text"   class="text_boxes_numeric total_reject" name="total_reject" id="total_reject<?= $i ?>" value="<?= $v['TOTAL_REJECT']?>"/>
											</td>
											<td width="40"  align="center">
											    <input type="checkbox" class="checkSingle" value="<?=$po_id?>" id="checkitem_<?=$i;?>">  
												<input style="width:45px;" type="hidden"   class="hidden_mst_id" name="hidden_mst_id" id="hidden_mst_id<?= $i ?>"  value="<?= $v['MST_ID']?>"/>
                                            </td>
                                        </tr>
                                    <? 
									
								}
							?>
						</form>	
						
					</tbody> 
				   </table>  
		</fieldset>
		<table>
			<tr>
				<td align="center" valign="middle" class="button_container"><?=load_submit_buttons( $permission, "fnc_reject_data_entry", $is_update,0,"refresh_page()",1); ?></td>
			</tr>
		</table>
	</body>  
    </html>
    <?
    exit(); 
}
/*
|--------------------------------------------------------------------------
| Save Update Delete
|--------------------------------------------------------------------------
|
*/
if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$data = trim($data,"##");
	$row_array = explode('##',$data);
	// echo $action;die;
	// print_r($process);die;
	
	if ($operation==0)   // Insert Here===========================================================================
	{		
		$con = connect(); 
		
		$field_array		=	"id,company_id, po_break_down_id, set_break_down, reject_qnty,inserted_by, insert_date,status_active,is_deleted";  
		$field_array_dtls	=	"id, mst_id, company_id, po_break_down_id,reject_type,reject_qnty,inserted_by, insert_date,status_active,is_deleted"; 
		$data_array		 	= 	"";
		$data_array_dtls 	= 	"";

		foreach ($row_array as  $row_data) 
		{
			$data_arr 		= explode('@@',$row_data);
			$po_mst_data 	= $data_arr[0];
			$reject_data 	= $data_arr[1];
			$po_mst_arr 	= explode('_',$po_mst_data);
			$po_id 			= $po_mst_arr[0];
			$mst_id 		= $po_mst_arr[1];
			$total_reject 	= $po_mst_arr[2];
			$reject_arr 	= explode('@',$reject_data);
			// =======================================================================
			//								MST DATA
			// =======================================================================
			$id = return_next_id_by_sequence("independent_finish_mst_seq", "independent_finish_mst", $con);
						
			if($data_array!="") $data_array.=",";
			$data_array .="(".$id.",".$cbo_company_name.",".$po_id . ",'".$reject_data . "','".$total_reject . "',".$user_id.",'".$pc_date_time."',1,0)";

			// =======================================================================
			//								DTLS DATA
			// =======================================================================
			foreach ($reject_arr as $single_rej) 
			{
				$rej_arr = explode('_',$single_rej);
				$reject_type = $rej_arr[0];
				$reject_qty = $rej_arr[1];

				$dtls_id = return_next_id_by_sequence("independent_finish_dtls_seq", "independent_finish_dtls", $con);
				
				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls .="(".$dtls_id.",".$id.",".$cbo_company_name.",".$po_id . ",'".$reject_type . "','".$reject_qty . "',".$user_id.",'".$pc_date_time."',1,0)";
			}
		}
		// echo "rID***insert into independent_finish_dtls (".$field_array_dtls.") values ".$data_array_dtls;
		if($field_array!="")
		{
			$rID=sql_insert("independent_finish_mst",$field_array,$data_array);
		}
	    if($data_array_dtls!="")
		{
			
			$dtls_id=sql_insert("independent_finish_dtls",$field_array_dtls,$data_array_dtls);
		}	
	   	
		if($rID && $dtls_id)
		{
			oci_commit($con);  
			echo "0**".$id."**".$mst_id; 
		}
		else
		{
			oci_rollback($con);
			echo "6**".$id."**".$mst_id."**MST".$rID."**DTLS".$dtls_id;
			 
		} 
		disconnect($con);
		die;	
		
	}
	else if ($operation==1)   // Update Here
	{		
		$con = connect(); 

		$field_array		=	"id,company_id, po_break_down_id, set_break_down, reject_qnty,inserted_by, insert_date,status_active,is_deleted";  
		$field_array_dtls	=	"id, mst_id, company_id, po_break_down_id,reject_type,reject_qnty,inserted_by, insert_date,status_active,is_deleted";  
		$data_array		 	= 	"";
		$data_array_dtls 	= 	"";
		$field_array_up	=	"company_id*po_break_down_id*set_break_down*reject_qnty*updated_by*update_date*status_active*is_deleted";
		// print_r( $field_array_up);die;	
	
		
		$data_array_up = array();
		$mst_id_array  = array();
		
		foreach ($row_array as  $row_data) 
		{
			$data_arr 		= explode('@@',$row_data);
			$po_mst_data 	= $data_arr[0];
			$reject_data 	= $data_arr[1];
			$po_mst_arr 	= explode('_',$po_mst_data);
			$po_id 			= $po_mst_arr[0];
			$mst_id 		= $po_mst_arr[1];
			$total_reject 	= $po_mst_arr[2];
			$reject_arr 	= explode('@',$reject_data);
			
			if ($mst_id) 
			{
				// =======================================================================
				//								MST DATA UPDATE
				// =======================================================================
				$mst_id_array[]=$mst_id;  
				$id = $mst_id;

				$data_array_up[$mst_id]=explode("*",("".$cbo_company_name."*".$po_id."*'".$reject_data."'*'".$total_reject."'*".$user_id."*'".$pc_date_time."'*1*0")); 
				 
			}
			else
			{
				// =======================================================================
				//								MST DATA INSERT
				// =======================================================================
				$id = return_next_id_by_sequence("independent_finish_mst_seq", "independent_finish_mst", $con);
						
				if($data_array!="") $data_array.=",";
				$data_array .="(".$id.",".$cbo_company_name.",".$po_id . ",'".$reject_data . "','".$total_reject . "',".$user_id.",'".$pc_date_time."',1,0)";
			}
			

			// =======================================================================
			//								DTLS DATA
			// =======================================================================
			foreach ($reject_arr as $single_rej) 
			{
				$rej_arr = explode('_',$single_rej);
				$reject_type = $rej_arr[0];
				$reject_qty = $rej_arr[1];

				$dtls_id = return_next_id_by_sequence("independent_finish_dtls_seq", "independent_finish_dtls", $con);
				
				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls .="(".$dtls_id.",".$id.",".$cbo_company_name.",".$po_id . ",'".$reject_type . "','".$reject_qty . "',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			}
		}
		
		// echo bulk_update_sql_statement( "independent_finish_mst", "id", $field_array_up, $data_array_up, $mst_id_array ); die; 
		// echo "rID2***insert into independent_finish_mst (".$field_array.") values ".$data_array;
		$mst_insert=$mst_update=$dtls_insert=$dtlsDelete=true;
		// =======================================================================
		//								MST EXECUTION
		// =======================================================================
		if(count($data_array_up)>0) 
		{
			$mst_update=execute_query(bulk_update_sql_statement( "independent_finish_mst", "id", $field_array_up, $data_array_up, $mst_id_array ));
		}
		if($data_array!="")
		{
			$mst_insert =sql_insert("independent_finish_mst",$field_array,$data_array);
		} 
		 
		// =======================================================================
		//								DTLS EXECUTION
		// =======================================================================
		 
		$deleted_id = implode(",",$mst_id_array);
		if($deleted_id!="")
		{
			$dtlsDelete = execute_query("UPDATE independent_finish_dtls SET status_active=0,is_deleted=1,updated_by=$user_id ,update_date='$pc_date_time' where mst_id in($deleted_id)",0); 
		} 

		if($data_array_dtls!="")
		{
			$dtls_insert=sql_insert("independent_finish_dtls",$field_array_dtls,$data_array_dtls);
		}	
		
		// echo "6**$rID1 = $rID2 = $rID3  = $rID4 <br>";
		// die; 
		if($mst_insert && $mst_update && $dtls_insert && $dtlsDelete)
		{
			oci_commit($con);  
			echo "1**".$id."**".$mst_id ;
			// echo "rID***insert into independent_finish_dtls (".$field_array_dtls.") values ".$data_array_dtls;
		}
		else
		{
			oci_rollback($con);
			echo "6**".$id."**mst_insert".$mst_insert."**mst_update".$mst_update."**dtls_insert".$dtls_insert."**dtls_delete".$dtlsDelete;
		} 
		disconnect($con);
		die;
		
	}
	else if ($operation==2)  //Delete here======================================================================================
	{		
		$con = connect(); 
		$data_array_up = array();
		$mst_id_array  = array(); 
		
		foreach ($row_array as  $row_data) 
		{
			$data_arr 		= explode('@@',$row_data);
			$po_mst_data 	= $data_arr[0];
			$reject_data 	= $data_arr[1];
			$po_mst_arr 	= explode('_',$po_mst_data);
			$po_id 			= $po_mst_arr[0];
			$mst_id 		= $po_mst_arr[1];
			$total_reject 	= $po_mst_arr[2];
			$reject_arr 	= explode('@',$reject_data);
			
			if ($mst_id) 
			{
				$mst_id_array[]=$mst_id;
			}	
		}
		 
		// print_r($mst_id_array); die;
		$deleted_id = implode(",",$mst_id_array);
		if($deleted_id!="")
		{

			$rID1=execute_query( "update independent_finish_mst SET status_active =0, is_deleted = 1,updated_by='$user_id' , update_date='$pc_date_time' where id in ($deleted_id)",0); 
			$rID2=execute_query( "update independent_finish_dtls SET status_active =0, is_deleted = 1,updated_by='$user_id' , update_date='$pc_date_time' where mst_id in ($deleted_id)",0); 
		} 
		// die;
		if($rID1 && $rID2)
		{
			oci_commit($con);  
			echo "2**".$id."**".$mst_id;
		}
		else
		{
			oci_rollback($con);
			echo "6**".$id."**".$mst_id."**mst".$rID1."**dtls".$rID2;
		} 
		disconnect($con);
		die;	
		
	}
}
?>