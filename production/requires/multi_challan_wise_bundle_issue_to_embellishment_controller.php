<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_id", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" );
	exit();
}

if ($action=="load_drop_down_party")
{
	echo create_drop_down("cbo_party_id", 120, "SELECT a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(22,23) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--Select--", $selected, "");
	exit();
}

if($action=="order_no_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	?>
    <script>

		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
    	function check_all_data()
    	{
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				if($('#tr_' + i).is(':visible'))
				{
					var onclickString = $('#tr_' + i).attr('onclick');
					var paramArr = onclickString.split("'");
					var functionParam = paramArr[1];
					js_set_value( functionParam );
				}

			}
		}

		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( strCon )
		{
			//alert(strCon);
				var splitSTR = strCon.split("_");
				var str_or = splitSTR[0];
				var selectID = splitSTR[1];
				var selectDESC = splitSTR[2];
				//$('#txt_individual_id' + str).val(splitSTR[1]);
				//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');

				toggle( document.getElementById( 'tr_' + str_or ), '#FFFFCC' );

				if( jQuery.inArray( str_or, selected_id ) == -1 ) {
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
				$('#txt_selected_po').val( id );
				$('#txt_selected_style').val( name );
				$('#txt_selected_no').val( num );
		}
    </script>
    <?
	$buyer=str_replace("'","",$buyer);
	$w_company=str_replace("'","",$w_company);
	$lc_company=str_replace("'","",$lc_company);
	$job_year=str_replace("'","",$job_year);
	$txt_style_ref_id=str_replace("'","",$txt_style_ref_id);

	if($lc_company!=0) $lc_company_cond="and b.company_name=$lc_company"; else $lc_company_cond="";
	if($buyer!=0) $buyer_cond="and b.buyer_name=$buyer"; else $buyer_cond="";
	if($db_type==0)
	{
		$select_date=" year(b.insert_date)";
	}
	else if($db_type==2)
	{
		$select_date=" to_char(b.insert_date,'YYYY')";
	}

	$sql = "SELECT a.id,a.po_number,a.job_no_mst,b.style_ref_no,b.job_no_prefix_num,$select_date as year from wo_po_break_down a, wo_po_details_master b,pro_garments_production_mst c where a.job_id=b.id and a.id=c.po_break_down_id $lc_company_cond $buyer_cond and a.status_active=1 and b.status_active=1 and c.production_type=2 and c.embel_name in(1,2) order by b.id desc";
	//echo $sql; die;
	echo create_list_view("list_view", "Year,Job No,Style Ref No,Order NO","70,50,150,150","500","310",0, $sql , "js_set_value", "id,po_number", "", 1, "0", $arr, "year,job_no_prefix_num,style_ref_no,po_number", "","setFilterGrid('list_view',-1)","0","",1) ;

	echo "<input type='hidden' id='txt_selected_po' />";
	echo "<input type='hidden' id='txt_selected_style' />";
	echo "<input type='hidden' id='txt_selected_no' />";
	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$companyArr 	= return_library_array("select id,company_name from lib_company","id","company_name");
	$buyerArr 		= return_library_array("select id,buyer_name from lib_buyer","id","buyer_name");
	$supplierArr 	= return_library_array("select id,supplier_name from lib_supplier","id","supplier_name");
	$locationArr 	= return_library_array("select id,location_name from lib_location","id","location_name");
	$colorArr 		= return_library_array("select id,color_name from lib_color","id","color_name");
	$floorArr       = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");
	// ================================= GETTING FORM DATA ====================================
	$company_id 		= str_replace("'","",$cbo_company_id);
	$buyer_id 			= str_replace("'","",$cbo_buyer_id);
	$party_id 			= str_replace("'","",$cbo_party_id);
	$emb_type 			= str_replace("'","",$cbo_emb_type);
	$order_no 			= str_replace("'","",$txt_order_no);
	$order_id 			= str_replace("'","",$hiden_order_id);
	$txt_date_from 		= str_replace("'","",$txt_date_from);
	$txt_date_to 		= str_replace("'","",$txt_date_to);

	//******************************************* MAKE QUERY CONDITION ************************************************
	$sql_cond = "";
	$sql_cond .= ($company_id==0) 		? "" : " and a.company_name=$company_id";
	$sql_cond .= ($buyer_id==0) 			? "" : " and a.buyer_name=$buyer_id";
	$sql_cond .= ($party_id==0) 			? "" : " and d.serving_company=$party_id";
	$sql_cond .= ($emb_type==0) 			? "" : " and d.embel_name=$emb_type";

	if($order_id !="")
	{
		$sql_cond .= " and b.id in($order_id)";
	}
	else
	{
		if($order_no !=""){ $sql_cond .= " and b.po_number ='$order_no'";}
	}

	if($txt_date_from!="" && $txt_date_to!="")
	{
		if($db_type==0)
		{
			$txt_datefrom=change_date_format($txt_date_from,'yyyy-mm-dd');
			$txt_dateto=change_date_format($txt_date_to,'yyyy-mm-dd');
		}
		else if($db_type==2)
		{
			$txt_datefrom=change_date_format($txt_date_from,'','',-1);
			$txt_dateto=change_date_format($txt_date_to,'','',-1);
		}


		$sql_cond.=" and d.production_date BETWEEN '$txt_datefrom' and '$txt_dateto'";

	}

	// ================================================ MAIN QUERY ==================================================
	$sql="SELECT a.id, a.job_no,a.buyer_name, a.style_ref_no as style,a.style_description as style_desc,b.po_number,b.grouping,c.color_number_id as color_id,d.embel_type,d.embel_name,d.serving_company,d.location,d.production_date,e.cut_no,e.production_qnty as qty,e.bundle_no,g.sys_number,g.id as sys_id,g.body_part,d.production_source
	from  wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,pro_garments_production_mst d, pro_garments_production_dtls e,pro_gmts_delivery_mst g
	where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and d.id=e.mst_id and b.id=d.po_break_down_id and c.id=e.color_size_break_down_id and g.id=d.delivery_mst_id  $sql_cond $sql_cond2 $po_ids_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and d.status_active=1 and e.status_active=1 and g.status_active=1 and d.production_type = 2 and e.cut_no is not null order by d.embel_name,g.sys_number";
	//  echo $sql;die();
	$sql_res = sql_select($sql);
	if (count($sql_res) < 1)
	{
		?>
		<style type="text/css">
			.alert
			{
				padding: 12px 35px 12px 14px;
				margin-bottom: 18px;
				text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
				background-color: #fcf8e3;
				border: 1px solid #fbeed5;
				-webkit-border-radius: 4px;
				-moz-border-radius: 4px;
				border-radius: 4px;
				color: #c09853;
				font-size: 16px;
			}
			.alert strong{font-size: 18px;}
			.alert-danger,
			.alert-error
			{
			  	background-color: #f2dede;
			  	border-color: #eed3d7;
			  	color: #b94a48;
			}
		</style>
		<div style="margin:20px auto; width: 90%">
			<div class="alert alert-error">
			  <strong>Sorry!</strong> Data not available. Please try again after something change.
			</div>
		</div>
		<?
		die();
	}

	$data_array = array();
	$chk_arr = array();
	$cutting_no_arr = array();
	$job_id_arr = array();
	foreach ($sql_res as $val)
	{
		$data_array[$val['SERVING_COMPANY']][$val['SYS_ID']]['job_no'] = $val['JOB_NO'];
		$data_array[$val['SERVING_COMPANY']][$val['SYS_ID']]['buyer_name'] = $val['BUYER_NAME'];
		$data_array[$val['SERVING_COMPANY']][$val['SYS_ID']]['style'] = $val['STYLE'];
		$data_array[$val['SERVING_COMPANY']][$val['SYS_ID']]['style_desc'] = $val['STYLE_DESC'];
		$data_array[$val['SERVING_COMPANY']][$val['SYS_ID']]['po_number'] .= $val['PO_NUMBER'].",";
		$data_array[$val['SERVING_COMPANY']][$val['SYS_ID']]['grouping'] = $val['GROUPING'];
		$data_array[$val['SERVING_COMPANY']][$val['SYS_ID']]['color_name'] .= $colorArr[$val['COLOR_ID']].",";
		$data_array[$val['SERVING_COMPANY']][$val['SYS_ID']]['sys_number'] = $val['SYS_NUMBER'];
		$data_array[$val['SERVING_COMPANY']][$val['SYS_ID']]['embel_type'] = $val['EMBEL_TYPE'];
		$data_array[$val['SERVING_COMPANY']][$val['SYS_ID']]['embel_name'] = $val['EMBEL_NAME'];
		$data_array[$val['SERVING_COMPANY']][$val['SYS_ID']]['location'] = $val['LOCATION'];
		$data_array[$val['SERVING_COMPANY']][$val['SYS_ID']]['body_part'] = $val['BODY_PART'];
		$data_array[$val['SERVING_COMPANY']][$val['SYS_ID']]['production_date'] = $val['PRODUCTION_DATE'];
		$data_array[$val['SERVING_COMPANY']][$val['SYS_ID']]['cut_no'] = $val['CUT_NO'];
		$data_array[$val['SERVING_COMPANY']][$val['SYS_ID']]['production_source'] = $val['PRODUCTION_SOURCE'];
		$data_array[$val['SERVING_COMPANY']][$val['SYS_ID']]['qty'] += $val['QTY'];
		if($chk_arr[$val['SERVING_COMPANY']][$val['SYS_ID']][$val['BUNDLE_NO']]=="")
		{
			$data_array[$val['SERVING_COMPANY']][$val['SYS_ID']]['total_bundle']++;
			$chk_arr[$val['SERVING_COMPANY']][$val['SYS_ID']][$val['BUNDLE_NO']] = $val['BUNDLE_NO'];
		}
		$cutting_no_arr[$val['CUT_NO']] = $val['CUT_NO'];
		$job_id_arr[$val['ID']] = $val['CUT_NO'];
	}
	// echo "<pre>";print_r($data_array);die();

	$cutting_no_cond = where_con_using_array($cutting_no_arr,1,"a.cutting_no");
	$sql = "SELECT a.cutting_no,b.order_cut_no,a.floor_id from ppl_cut_lay_mst a,ppl_cut_lay_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 $cutting_no_cond";
	$res = sql_select($sql);
	$order_cut_no_arr = array();
	$floor_array=array();
	foreach ($res as $val)
	{
		$order_cut_no_arr[$val['CUTTING_NO']] .= $val['ORDER_CUT_NO'].",";
		$floor_array[$val['CUTTING_NO']] = $val['FLOOR_ID'];
	}
	$table_width = 1370;
	ob_start();
	?>
	<style type="text/css">
		table tr th, table tr td{ word-wrap: break-word;word-break: break-all; }
	</style>
	<fieldset class="main" style="margin: 0 auto; padding: 10px;  width: <? echo $table_width;?>px">
		<table width="<? echo $table_width;?>" cellspacing="0">
	        <tr class="form_caption" style="border:none;">
	            <td colspan="17" align="center" ><strong style="font-size: 19px">Multi Challan Wise Bundle Issue to Embellishment</strong></td>
	        </tr>
	    </table>
	    <!-- ================================================ DETAIS PART ================================================ -->
	    <div>
	    	<table class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all" id="" width="<? echo $table_width;?>">
	    		<thead>
		    		<tr>
		    			<th width="30">Sl.</th>
		    			<th width="100">Party</th>
		    			<th width="100">Location</th>
		    			<th width="100">Challan No</th>
		    			<th width="60">Challan Date</th>
		    			<th width="80">Buyer</th>
		    			<th width="100">Job No</th>
		    			<th width="80">Order No</th>
		    			<th width="80">Int. Ref.</th>
		    			<th width="80">Style ref</th>
		    			<th width="80">Style Des</th>
		    			<th width="80">Color</th>
		    			<th width="80">Cutting No</th>
		    			<th width="100">Cutting Floor</th>
		    			<th width="40">Order Cut No</th>
		    			<th width="80">Emb. Type</th>
		    			<th width="60">Total Issue Qty</th>
		    			<th width="40">No of Bundle</th>
	    			</tr>
	    		</thead>
	    	</table>

	    	<div style="width: <? echo $table_width+20;?>px; overflow-y: scroll; max-height: 400px" id="scroll_body">
	    		<table cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all" width="<? echo $table_width;?>" id="table_body_id">
	    			<?
	    			$i=1;
	    			$floor_total_array = array();
    				foreach ($data_array as $party_id=>$party_data)
    				{
    					foreach ($party_data as $sys_id => $row)
    					{
							$bgcolor = ($i%2==0) ? "#ffffff" : "#f6faff";
							$po_number = implode(",",array_unique(array_filter(explode(",", $row['po_number']))));
							$color_name = implode(",",array_unique(array_filter(explode(",", $row['color_name']))));
							$order_cut_no = implode(",",array_unique(array_filter(explode(",", $order_cut_no_arr[$row['cut_no']]))));
							$floor=$floor_array[$row['cut_no']];

							?>
								<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_1nd<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_1nd<?=$i; ?>" >

									<td width="30">
										<input type="checkbox" name="checkbox_chk" id="tbl_<?=$i; ?>"  onClick="fnc_checkbox_check_party(<?=$i;?>);"  />
										<input type="hidden" id="mstidall_<?=$i; ?>" value="<?=$sys_id; ?>"/>
	                   					<input type="hidden" id="party_<?=$i; ?>" name="party[]"   value="<?=$party_id; ?>" />
										<?=$i;?>
									</td>
					    			<td width="100"><? echo($row['production_source']==3)? $supplierArr[$party_id] :""; ?></td>
					    			<td width="100"><?=$locationArr[$row['location']];?></td>
					    			<td width="100"><?=$row['sys_number'];?></td>
					    			<td width="60" align="center"><?=change_date_format($row['production_date']);?></td>
					    			<td width="80"><?=$buyerArr[$row['buyer_name']];?></td>
					    			<td width="100"><?=$row['job_no'];?></td>
					    			<td width="80"><?=$po_number;?></td>
					    			<td width="80"><?=$row['grouping'];?></td>
					    			<td width="80"><?=$row['style'];?></td>
					    			<td width="80"><?=$row['style_desc'];?></td>
					    			<td width="80"><?=$color_name;?></td>
					    			<td width="80"><?=$row['cut_no'];?></td>
									<td width="100"><?=$floorArr[$floor];?></td>
					    			<td width="40"><?=$order_cut_no;?></td>
					    			<td width="80">
					    				<?
					    				if($row['embel_name']==1)
					    				{
					    					echo $emblishment_print_type_arr[$row['embel_type']];
					    				}
					    				else
					    				{
					    					echo $emblishment_print_type[$row['embel_type']];
					    				}
					    				?>
					    			</td>
					    			<td width="60" align="right"><?=$row['qty'];?></td>
					    			<td width="40" align="right"><?=$row['total_bundle'];?></td>
								</tr>
							<?
							$i++;
						}
    				}
	    			?>
	    		</table>
	    	</div>

	    </div>
    </fieldset>
   <?
	exit();
}

if($action=="print_report")
{
	echo load_html_head_contents("Multi Challan Wise Bundle Issue to Embellishment","../../", 1, 1, $unicode,1);

	extract($_REQUEST);
	$companyArr 	= return_library_array("select id,company_name from lib_company","id","company_name");
	$locationArr 	= return_library_array("select id,location_name from lib_location","id","location_name");
	$buyerArr 		= return_library_array("select id,buyer_name from lib_buyer","id","buyer_name");
	$supplierArr 	= return_library_array("select id,supplier_name from lib_supplier","id","supplier_name");
	$floorArr 		= return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");
	$countryArr 	= return_library_array("select id,country_name from lib_country","id","country_name");
	$colorArr 		= return_library_array("select id,color_name from lib_color","id","color_name");
	$sizeArr 		= return_library_array("select id,size_name from lib_size","id","size_name");
	$bodypartArr	= return_library_array("select id,bundle_use_for from ppl_bundle_title","id","bundle_use_for");
	// ================================= GETTING FORM DATA ====================================
	$challan_ids 		= str_replace("'","",$challan_ids);
	$party_ids 			= str_replace("'","",$party_ids);

	//******************************************* MAKE QUERY CONDITION ************************************************
	$sql_cond = "";
	$sql_cond .= ($party_ids=="") 		? "" : " and d.serving_company in($party_ids)";
	$sql_cond .= ($challan_ids=="") 	? "" : " and g.id in($challan_ids)";

	// ================================================ MAIN QUERY ==================================================
	$sql="SELECT a.job_no,a.buyer_name, a.style_ref_no as style,a.style_description as style_desc,b.id as po_id,b.po_number,b.grouping,c.color_number_id as color_id,c.size_number_id as size_id,c.country_id,c.size_order,d.embel_type,d.embel_name,d.serving_company,d.location,d.production_date,e.cut_no,d.floor_id,e.production_qnty as qty,e.bundle_no,g.sys_number,g.id as sys_id,g.body_part,d.production_source,g.working_company_id,g.remarks
	from  wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,pro_garments_production_mst d, pro_garments_production_dtls e,pro_gmts_delivery_mst g
	where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and d.id=e.mst_id and b.id=d.po_break_down_id and c.id=e.color_size_break_down_id and g.id=d.delivery_mst_id  $sql_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and d.status_active=1 and e.status_active=1 and g.status_active=1 and d.production_type = 2 and e.cut_no is not null order by c.size_order";
	// echo $sql;die();
	$sql_res = sql_select($sql);
	if (count($sql_res) < 1)
	{
		?>
		<style type="text/css">
			.alert
			{
				padding: 12px 35px 12px 14px;
				margin-bottom: 18px;
				text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
				background-color: #fcf8e3;
				border: 1px solid #fbeed5;
				-webkit-border-radius: 4px;
				-moz-border-radius: 4px;
				border-radius: 4px;
				color: #c09853;
				font-size: 16px;
			}
			.alert strong{font-size: 18px;}
			.alert-danger,
			.alert-error
			{
			  	background-color: #f2dede;
			  	border-color: #eed3d7;
			  	color: #b94a48;
			}
		</style>
		<div style="margin:20px auto; width: 90%">
			<div class="alert alert-error">
			  <strong>Sorry!</strong> Data not available. Please try again after something change.
			</div>
		</div>
		<?
		die();
	}

	$size_qty_arr = array();
	$size_arr = array();
	$data_array = array();
	$chk_arr = array();
	$cutting_no_arr = array();
	$emb_id_arr = array();
	// $source = 0;
	// $serving_company = 0;
	// $location = 0;
	$remarks = "";
	foreach ($sql_res as $val)
	{
		$size_arr[$val['SIZE_ID']] = $val['SIZE_ID'];
		$data_array[$val['SYS_NUMBER']][$val['PO_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']]['job_no'] 		= $val['JOB_NO'];
		$data_array[$val['SYS_NUMBER']][$val['PO_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']]['buyer_name'] 	= $val['BUYER_NAME'];
		$data_array[$val['SYS_NUMBER']][$val['PO_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']]['style'] 		= $val['STYLE'];
		$data_array[$val['SYS_NUMBER']][$val['PO_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']]['style_desc'] 	= $val['STYLE_DESC'];
		$data_array[$val['SYS_NUMBER']][$val['PO_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']]['po_number'] 	= $val['PO_NUMBER'];
		$data_array[$val['SYS_NUMBER']][$val['PO_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']]['grouping'] 	= $val['GROUPING'];
		$data_array[$val['SYS_NUMBER']][$val['PO_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']]['embel_type'] 	= $val['EMBEL_TYPE'];
		$data_array[$val['SYS_NUMBER']][$val['PO_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']]['embel_name'] 	= $val['EMBEL_NAME'];
		$data_array[$val['SYS_NUMBER']][$val['PO_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']]['cut_no'] 		= $val['CUT_NO'];
		$data_array[$val['SYS_NUMBER']][$val['PO_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']]['body_part'] 		= $val['BODY_PART'];
		$data_array[$val['SYS_NUMBER']]['embel_name'] 		= $val['EMBEL_NAME'];
		$remarks .= ($remarks=="") ? $val['REMARKS'] : "**".$val['REMARKS'];

		$size_qty_arr[$val['SYS_NUMBER']][$val['PO_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']][$val['SIZE_ID']] 	+= $val['QTY'];

		if($chk_arr[$val['SYS_NUMBER']][$val['PO_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']][$val['BUNDLE_NO']]=="")
		{
			$data_array[$val['SYS_NUMBER']][$val['PO_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']]['total_bundle']++;
			$chk_arr[$val['SYS_NUMBER']][$val['PO_ID']][$val['COUNTRY_ID']][$val['COLOR_ID']][$val['BUNDLE_NO']] = $val['BUNDLE_NO'];
		}
		$cutting_no_arr[$val['CUT_NO']] = $val['CUT_NO'];
		$emb_id_arr[$val['EMBEL_NAME']] = $val['EMBEL_NAME'];
		$source = $val['PRODUCTION_SOURCE'];
		$serving_company = $val['SERVING_COMPANY'];
		$location = $val['LOCATION'];
	}
	// echo "<pre>";print_r($data_array);die();

	$cutting_no_cond = where_con_using_array($cutting_no_arr,1,"a.cutting_no");
	$sql = "SELECT a.cutting_no,b.order_cut_no,b.color_id,a.floor_id from ppl_cut_lay_mst a,ppl_cut_lay_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 $cutting_no_cond";
	$res = sql_select($sql);
	$order_cut_no_arr = array();
	$floor_array=array();
	foreach ($res as $val)
	{
		$order_cut_no_arr[$val['CUTTING_NO']][$val['COLOR_ID']] .= $val['ORDER_CUT_NO'].",";
		$floor_array[$val['CUTTING_NO']][$val['COLOR_ID']]=$val['FLOOR_ID'];
	}

	$table_width = 1310+count($size_arr)*50;
	ob_start();
	?>
	<style type="text/css">
		table tr th, table tr td{ word-wrap: break-word;word-break: break-all; }
	</style>
	<fieldset class="main" style="margin: 0 auto; padding: 10px;  width: <? echo $table_width;?>px">
		<table width="<? echo $table_width;?>" cellspacing="0">
			<tr>
	            <td colspan="17" align="center" style="font-size:18px;"><strong><?=$companyArr[$company_id]; ?></strong></td>
	        </tr>
	        <tr>
	            <td colspan="17" align="center" style="font-size:18px;"><strong>Working Company:
	            <?
	            if($source==1)
	            {
	            	echo $companyArr[$serving_company];
	            }
	            else
	            {
	            	echo $supplierArr[$serving_company];
	            }
	            ?>

	            </strong></td>
	        </tr>
	        <tr>
	            <td colspan="17" align="center" style="font-size:18px;"><strong>Working Company Add: </strong>
	            	<?
	        		if($source==1)
	        		{
						$nameArray=sql_select( "SELECT plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$serving_company and status_active=1 and is_deleted=0");
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
	        		}
	        		else
	        		{
	        			$sql = "SELECT address_1,address_2,address_3,address_4 from lib_supplier where id=$serving_company";
	        			$res = sql_select($sql);
	        			echo $res[0]['ADDRESS_1'].$res[0]['ADDRESS_2'].$res[0]['ADDRESS_3'].$res[0]['ADDRESS_4'];
	        		}
	        		?>
	            </td>
	        </tr>
	        <tr>
	            <td colspan="17" align="center" style="font-size:18px;"><strong>Owner Company: <?=$companyArr[$company_id]; ?></strong></td>
	        </tr>
	        <tr class="form_caption" style="border:none;">
	            <td colspan="17" align="center" ><strong style="font-size: 18px">Embellishment Issue Challan</strong></td>
	        </tr>
	        <tr style="font-size:14px;font-weight: bold;">
	        	<td width="100">Embel. Name:</td>
	        	<td colspan="3" align="left">
	        		<?
	        		$embName = "";
	        		foreach ($emb_id_arr as $key => $val)
	        		{
	        			$embName .= ($embName=="") ? $emblishment_name_array[$key] : ", ".$emblishment_name_array[$key];
	        		}
	        		echo $embName;
	        		?>
	        	</td>

	        	<td width="100">Delivery Date: </td>
	        	<td colspan="3"><?=date('d-m-Y');?></td>

	        	<td width="100">Emb. Source: </td>
	        	<td colspan="3"><?=$knitting_source[$source];?></td>

	        	<td width="100">Emb. Company: </td>
	        	<td colspan="3">
	        		<?
	        		if($source==1)
	        		{
	        			echo $companyArr[$serving_company];
	        		}
	        		else
	        		{
	        			echo $supplierArr[$serving_company];
	        		}
	        		?>
	        	</td>
	        </tr>
	        <tr style="font-size:14px !important;font-weight: bold;">
	        	<td>Location:</td>
	        	<td colspan="3"><?=$locationArr[$location];?></td>

	        	<td>Remarks: </td>
	        	<td colspan="13"><?=implode(", ", array_unique(explode("**", $remarks)));?></td>
	        </tr>

	    </table>
	    <!-- ================================================ DETAIS PART ================================================ -->
	    <div>
    		<table cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all" width="<? echo $table_width;?>" id="tbl_search">
    			<?
    			;
    			$grand_total_array = array();
    			foreach ($data_array as $challan_no => $challan_data)
    			{
    				$i=1
    				?>
    				<thead>
			    		<tr>
			    			<th colspan="12">Challan No: <?=$challan_no;?>,Embel Type: <?=$emblishment_name_array[$challan_data['embel_name']];?></th>
			    			<th colspan="<?=count($size_arr);?>">Size</th>

			    			<th rowspan="2" width="80">Total Issue Qty</th>
			    			<th rowspan="2" width="80">No of Bundle</th>
			    			<th rowspan="2" width="100">Body Part</th>
			    			<th rowspan="2" width="100">Remarks</th>
		    			</tr>
		    			<tr>
		    				<th width="30">Sl.</th>
			    			<th width="80">Buyer</th>
			    			<th width="100">Job No</th>
			    			<th width="80">Order No</th>
			    			<th width="80">Int. Ref.</th>
			    			<th width="80">Style ref</th>
			    			<th width="80">Style Des</th>
			    			<th width="80">Country</th>
			    			<th width="80">Color</th>
			    			<th width="80">Cutting No</th>
							<th width="100">Cutting Floor</th>
			    			<th width="80">Order Cut No</th>
		    				<?
			    			foreach ($size_arr as $s_id => $s_val)
			    			{
			    				?>
			    				<th width="50"><?=$sizeArr[$s_id];?></th>
			    				<?
			    			}
			    			?>
		    			</tr>
		    		</thead>
    				<?
    				$sub_total_arr = array();
    				foreach ($challan_data as $po_id => $po_data)
    				{
    					foreach ($po_data as $country_id => $country_data)
    					{
    						foreach ($country_data as $color_id => $row)
    						{
								$bgcolor = ($i%2==0) ? "#ffffff" : "#f6faff";
								$order_cut_no = implode(",",array_unique(array_filter(explode(",", $order_cut_no_arr[$row['cut_no']][$color_id]))));
								$floor=$floor_array[$row['cut_no']][$color_id];
								?>
									<tr bgcolor="<? echo $bgcolor;?>">

										<td width="30"><?=$i;?></td>
						    			<td width="80"><?=$buyerArr[$row['buyer_name']];?></td>
						    			<td width="100"><?=$row['job_no'];?></td>
						    			<td width="80"><?=$row['po_number'];?></td>
						    			<td width="80"><?=$row['grouping'];?></td>
						    			<td width="80"><?=$row['style'];?></td>
						    			<td width="80"><?=$row['style_desc'];?></td>
						    			<td width="80"><?=$countryArr[$country_id];?></td>
						    			<td width="80"><?=$colorArr[$color_id];?></td>
						    			<td width="80"><?=$row['cut_no'];?></td>
										<td width="100"><?=$floorArr[$floor];?></td>
						    			<td width="80"><?=$order_cut_no;?></td>
						    			<?
						    			$tot_qty = 0;
						    			foreach ($size_arr as $s_id => $s_val)
						    			{
						    				$size_qty = $size_qty_arr[$challan_no][$po_id][$country_id][$color_id][$s_id];
						    				?>
						    				<td width="50" align="right"><?=$size_qty;?></td>
						    				<?
						    				$tot_qty += $size_qty;
						    				$sub_total_arr[$challan_no][$s_id] += $size_qty;
						    				$grand_total_array[$s_id] += $size_qty;
						    			}
						    			?>
						    			<td width="80" align="right"><?=$tot_qty;?></td>
						    			<td width="80" align="right"><?=$row['total_bundle']; $grand_total_bundle+=$row['total_bundle'];?></td>
						    			<td width="100" align="left"><?=$bodypartArr[$row['body_part']];?></td>
						    			<td width="100" align="left"></td>
									</tr>
								<?
								$i++;
							}
						}
					}
					?>
					<tr bgcolor="#cddcdc" style="text-align: right;font-weight: bold;">
						<td colspan="12">Sub Total</td>
						<?
		    			$tot_qty = 0;
		    			foreach ($size_arr as $s_id => $s_val)
		    			{
		    				$size_qty = $sub_total_arr[$challan_no][$s_id];
		    				?>
		    				<td width="50" align="right"><?=$size_qty;?></td>
		    				<?
		    				$tot_qty += $size_qty;
		    			}
		    			?>
		    			<td><?=number_format($tot_qty,0);?></td>
		    			<td></td>
		    			<td></td>
		    			<td></td>
					</tr>
					<?
				}
    			?>
    			<tfoot>
    				<tr>
						<th colspan="12">Grand Total</th>
						<?
		    			$tot_qty = 0;
		    			foreach ($size_arr as $s_id => $s_val)
		    			{
		    				$size_qty = $grand_total_array[$s_id];
		    				?>
		    				<th width="50" align="right"><?=$size_qty;?></th>
		    				<?
		    				$tot_qty += $size_qty;
		    			}
		    			?>
		    			<th><?=number_format($tot_qty,0);?></th>
		    			<th><? echo number_format($grand_total_bundle,0);?></th>
		    			<th></th>
		    			<th></th>
					</tr>
    			</tfoot>
    		</table>
	    </div>
    </fieldset>
   <?
	exit();
}
if($action=="print_report_one")
{
	echo load_html_head_contents("Multi Challan Wise Bundle Issue to Embellishment","../../", 1, 1, $unicode,1);

	extract($_REQUEST);
	$companyArr 	= return_library_array("select id,company_name from lib_company","id","company_name");
	$locationArr 	= return_library_array("select id,location_name from lib_location","id","location_name");
	$buyerArr 		= return_library_array("select id,buyer_name from lib_buyer","id","buyer_name");
	$supplierArr 	= return_library_array("select id,supplier_name from lib_supplier","id","supplier_name");
	$floorArr 		= return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");
	$countryArr 	= return_library_array("select id,country_name from lib_country","id","country_name");
	$colorArr 		= return_library_array("select id,color_name from lib_color","id","color_name");
	$sizeArr 		= return_library_array("select id,size_name from lib_size","id","size_name");
	$bodypartArr	= return_library_array("select id,bundle_use_for from ppl_bundle_title","id","bundle_use_for");
	// ================================= GETTING FORM DATA ====================================
	$challan_ids 		= str_replace("'","",$challan_ids);
	$party_ids 			= str_replace("'","",$party_ids);

	//******************************************* MAKE QUERY CONDITION ************************************************
	$sql_cond = "";
	$sql_cond .= ($party_ids=="") 		? "" : " and d.serving_company in($party_ids)";
	$sql_cond .= ($challan_ids=="") 	? "" : " and g.id in($challan_ids)";

	// ================================================ MAIN QUERY ==================================================
	$sql="SELECT a.job_no,a.buyer_name, a.style_ref_no as style,a.style_description as style_desc,b.id as po_id,b.po_number,b.grouping,c.color_number_id as color_id,c.size_number_id as size_id,c.country_id,c.size_order,d.embel_type,d.embel_name,d.serving_company,d.location,d.production_date,e.cut_no,d.floor_id,e.production_qnty as qty,e.bundle_no,g.sys_number,g.id as sys_id,g.body_part,d.production_source,g.working_company_id,g.remarks,g.organic,c.item_number_id
	from  wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,pro_garments_production_mst d, pro_garments_production_dtls e,pro_gmts_delivery_mst g
	where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and d.id=e.mst_id and b.id=d.po_break_down_id and c.id=e.color_size_break_down_id and g.id=d.delivery_mst_id  $sql_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and d.status_active=1 and e.status_active=1 and g.status_active=1 and d.production_type = 2 and e.cut_no is not null order by c.size_order";
	//  echo $sql;die();
	$sql_res = sql_select($sql);
	if (count($sql_res) < 1)
	{
		?>
		<style type="text/css">
			.alert
			{
				padding: 12px 35px 12px 14px;
				margin-bottom: 18px;
				text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
				background-color: #fcf8e3;
				border: 1px solid #fbeed5;
				-webkit-border-radius: 4px;
				-moz-border-radius: 4px;
				border-radius: 4px;
				color: #c09853;
				font-size: 16px;
			}
			.alert strong{font-size: 18px;}
			.alert-danger,
			.alert-error
			{
			  	background-color: #f2dede;
			  	border-color: #eed3d7;
			  	color: #b94a48;
			}
		</style>
		<div style="margin:20px auto; width: 90%">
			<div class="alert alert-error">
			  <strong>Sorry!</strong> Data not available. Please try again after something change.
			</div>
		</div>
		<?
		die();
	}

	$size_qty_arr = array();
	$size_arr = array();
	$data_array = array();
	$chk_arr = array();
	$cutting_no_arr = array();
	$emb_id_arr = array();
	
	
	$remarks = "";
	foreach ($sql_res as $val)
	{
		$size_arr[$val['SIZE_ID']] = $val['SIZE_ID'];
		
		$data_array[$val['SYS_NUMBER']][$val['JOB_NO']][$val['GROUPING']][$val['STYLE']]['buyer_name'] 	= $val['BUYER_NAME'];
	
		$data_array[$val['SYS_NUMBER']][$val['JOB_NO']][$val['GROUPING']][$val['STYLE']]['style_desc'] 	= $val['STYLE_DESC'];
		$data_array[$val['SYS_NUMBER']][$val['JOB_NO']][$val['GROUPING']][$val['STYLE']]['po_number'] .= $val['PO_NUMBER'].",";
		
		$data_array[$val['SYS_NUMBER']][$val['JOB_NO']][$val['GROUPING']][$val['STYLE']]['embel_type'] 	= $val['EMBEL_TYPE'];
		$data_array[$val['SYS_NUMBER']][$val['JOB_NO']][$val['GROUPING']][$val['STYLE']]['embel_name'] 	= $val['EMBEL_NAME'];
		$data_array[$val['SYS_NUMBER']][$val['JOB_NO']][$val['GROUPING']][$val['STYLE']]['cut_no'] 		= $val['CUT_NO'];
		$data_array[$val['SYS_NUMBER']][$val['JOB_NO']][$val['GROUPING']][$val['STYLE']]['item_number_id'] 		= $val['ITEM_NUMBER_ID'];
		$data_array[$val['SYS_NUMBER']][$val['JOB_NO']][$val['GROUPING']][$val['STYLE']]['color_id'] 		= $val['COLOR_ID'];
		
		$data_array[$val['SYS_NUMBER']]['embel_name'] 		= $val['EMBEL_NAME'];
		$remarks .= ($remarks=="") ? $val['REMARKS'] : "**".$val['REMARKS'];
		$size_qty_arr[$val['SYS_NUMBER']][$val['JOB_NO']][$val['GROUPING']][$val['STYLE']][$val['SIZE_ID']] 	+= $val['QTY'];

		if($chk_arr[$val['SYS_NUMBER']][$val['JOB_NO']][$val['GROUPING']][$val['STYLE']][$val['BUNDLE_NO']]=="")
		{
			$data_array[$val['SYS_NUMBER']][$val['JOB_NO']][$val['GROUPING']][$val['STYLE']]['total_bundle']++;
			$chk_arr[$val['SYS_NUMBER']][$val['JOB_NO']][$val['GROUPING']][$val['STYLE']][$val['BUNDLE_NO']] = $val['BUNDLE_NO'];
		}
		$cutting_no_arr[$val['CUT_NO']] = $val['CUT_NO'];
		$emb_id_arr[$val['EMBEL_NAME']] = $val['EMBEL_NAME'];
		$source = $val['PRODUCTION_SOURCE'];
		$serving_company = $val['SERVING_COMPANY'];
		$location = $val['LOCATION'];
		$emb_name=$val['EMBEL_NAME'];
		$date = $val['PRODUCTION_DATE'];
		$organic= $val['ORGANIC'];
	}
	//  echo "<pre>";print_r($data_array);die();

	$cutting_no_cond = where_con_using_array($cutting_no_arr,1,"a.cutting_no");
	$sql = "SELECT a.cutting_no,b.order_cut_no,b.color_id,a.floor_id from ppl_cut_lay_mst a,ppl_cut_lay_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 $cutting_no_cond";
	$res = sql_select($sql);
	$order_cut_no_arr = array();
	$floor_array=array();
	foreach ($res as $val)
	{
		$order_cut_no_arr[$val['CUTTING_NO']].= $val['ORDER_CUT_NO'].",";
		$floor_array[$val['CUTTING_NO']][$val['COLOR_ID']]=$val['FLOOR_ID'];
		$cutting_floor.=$floorArr[$val['FLOOR_ID']].",";
	}

	$table_width = 1130+count($size_arr)*50;
	ob_start();
	?>
	<style type="text/css">
		table tr th, table tr td{ word-wrap: break-word;word-break: break-all; }
	</style>
	<fieldset class="main" style="margin: 0 auto; padding: 10px;  width: <? echo $table_width;?>px">
		<table width="<? echo $table_width;?>" cellspacing="0">
			<tr>
	            <td colspan="17" align="center" style="font-size:18px;"><strong><?=$companyArr[$company_id]; ?></strong></td>
	        </tr>
	        <tr>
	            <td colspan="17" align="center" style="font-size:18px;"><strong>Working Company:
	            <?
	            if($source==1)
	            {
	            	echo $companyArr[$serving_company];
	            }
	            else
	            {
	            	echo $supplierArr[$serving_company];
	            }
	            ?>

	            </strong></td>
	        </tr>
			<tr>
	            <td colspan="17" align="center" style="font-size:18px;"><strong>Factory: <?=$companyArr[$company_id]; ?></strong></td>
	        </tr>
	        <tr>
	            <td colspan="17" align="center" style="font-size:18px;">
	            	<?
	        		if($source==1)
	        		{
						$nameArray=sql_select( "SELECT plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$serving_company and status_active=1 and is_deleted=0");
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
	        		}
	        		else
	        		{
	        			$sql = "SELECT address_1,address_2,address_3,address_4 from lib_supplier where id=$serving_company";
	        			$res = sql_select($sql);
	        			echo $res[0]['ADDRESS_1'].$res[0]['ADDRESS_2'].$res[0]['ADDRESS_3'].$res[0]['ADDRESS_4'];
	        		}
	        		?>
	            </td>
	        </tr>
	    </table>
		<br>
		<!-- First Part -->
		<div align="center">
		    <table width="900px" align="center" border="1" rules="all" class="rpt_table" >
			  <tbody>

			    <tr>
					<td width="200px" valign="top" style="font-size:16px;"><strong>Party Name:</strong></td>
					<td width="180px" valign="top" style="font-size:16px;" ><strong><? 
					   if($source==1)
						{
							echo $companyArr[$serving_company];
						}
						else
						{
							echo $supplierArr[$serving_company];
						}
					    ?>
					</strong>
					</td>
					<td width="200px" valign="top" style="font-size:16px;"><strong>Party Source:</strong></td>
					<td width="150px" valign="top" style="font-size:16px;" ><strong><? echo $knitting_source[$source];?></strong></td>
					<td width="200px" valign="top" style="font-size:16px;"><strong>Embel Name:</strong></td>
					<td width="150px" valign="top" style="font-size:16px;"><strong><? echo $emblishment_name_array[$emb_name];?></strong></td>
				</tr>
				<tr>
                  <td width="200px" valign="top" style="font-size:16px;"><strong>Party Address:<strong></td>
                  <td  colspan="3" valign="top" style="font-size:16px;"><strong>
				   <?
				    if($source==1)
					{
						$nameArray=sql_select( "SELECT plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$serving_company and status_active=1 and is_deleted=0");
						echo $nameArray[0]['PLOT_NO'].",".$nameArray[0]['ROAD_NO'];
						
						
					}
					else
					{
						$sql = "SELECT address_1,address_2,address_3,address_4 from lib_supplier where id=$serving_company";
	        			$res = sql_select($sql);
	        			echo $res[0]['ADDRESS_1'].$res[0]['ADDRESS_2'];

					}
				 				 
				   ?>
				  <strong></td>
       
                  <td width="200px" valign="top" style="font-size:16px;"><strong>Cutting Floor:<strong></td>
                  <td  width="100px" valign="top" style="font-size:16px;" ><strong><? $cut_floor = implode(",",array_unique(array_filter(explode(",",$cutting_floor)))); echo $cut_floor;?></strong></td>
				</tr>
				<tr>
					<td width="200px" valign="top" style="font-size:16px;"><strong>Location :</strong></td>
					<td width="100px" valign="top" style="font-size:16px;"  ><strong><? echo $locationArr[$location];?></strong></td>
					<td width="200px" valign="top" style="font-size:16px;"><strong>Delivery Date:</strong></td>
					<td width="100px" valign="top" style="font-size:16px;" ><strong><?  echo $date; ?></strong></td>
					<td width="200px" valign="top" style="font-size:16px;"><strong>Organic:</strong></td>
					<td width="100px" valign="top" style="font-size:16px;"><strong><? echo $organic;?></strong></td>
				</tr>
				<tr>
					<td width="200px" valign="top" style="font-size:16px;"><strong>Gate Pass No:</strong></td>
					<td width="100px" valign="top"  ><??></td>
					<td width="200px" valign="top" style="font-size:16px;"><strong>Vechicle No:</strong></td>
					<td width="100px" valign="top" ><??></td>
					<td width="200px" valign="top" style="font-size:16px;"><strong>Security Lock No:</strong></td>
					<td width="100px" valign="top"><??></td>
				</tr>



			 </tbody>
			

			</table>
		</div>
        



		<br>
	    <!-- ================================================ DETAIS PART ================================================ -->
	    <div>
    		<table cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all" width="<? echo $table_width;?>" id="tbl_search">
    			<?
    			;
    			$grand_total_array = array();
    			foreach ($data_array as $challan_no => $challan_data)
    			{
    				$i=1
    				?>
    				<thead>
			    		<tr>
			    			<th colspan="11">Challan No: <?=$challan_no;?>,Embel Type: <?=$emblishment_name_array[$challan_data['embel_name']];?></th>
			    			<th colspan="<?=count($size_arr);?>">Size</th>
			    			<th rowspan="2" width="80">Total Issue Qty</th>
			    			<th rowspan="2" width="80">No of Bundle</th>		    	
		    			</tr>
		    			<tr>
		    				<th width="130">Challan No</th>
							<th width="100">QR Code</th>
			    			<th width="80">Buyer</th>
			    			<th width="100">Job No</th>
			    			<th width="80">Int. Ref.</th>
			    			<th width="80">Style ref</th>
			    			<th width="80">Order No</th>
			    			<th width="80">Item Name</th>
			    			<th width="80">Color</th>
			    			<th width="80">Cutting No</th>
			    			<th width="80">Order Cut No</th>
		    				<?
			    			foreach ($size_arr as $s_id => $s_val)
			    			{
			    				?>
			    				<th width="50"><?=$sizeArr[$s_id];?></th>
			    				<?
			    			}
			    			?>
		    			</tr>
		    		</thead>
    				<?
    				$sub_total_arr = array();
    				foreach ($challan_data as $job_id => $job_data)
    				{
    					foreach ($job_data as $group => $group_data)
    					{
    						foreach ($group_data as $style => $row)
    						{
								$bgcolor = ($i%2==0) ? "#ffffff" : "#f6faff";
								$order_cut_no = implode(",",array_unique(array_filter(explode(",", $order_cut_no_arr[$row['cut_no']]))));

								$po_number= implode(",",array_unique(array_filter(explode(",", $row['po_number']))));
								$floor=$floor_array[$row['cut_no']][$color_id];
								?>
									<tr bgcolor="<? echo $bgcolor;?>">

										<td width="130"><?=$challan_no;?></td>
										<th width="100"><??></th>
						    			<td width="80"><?=$buyerArr[$row['buyer_name']];?></td>
						    			<td width="100"><?=$job_id;?></td>
						    			<td width="80"><?=$group;?></td>
						    			<td width="80"><?=$style;?></td>
						    			<td width="80"><?=$po_number;?></td>
						    			<td width="80"><?=$garments_item[$row['item_number_id']];?></td>
						    			<td width="80"><?=$colorArr[$row['color_id']];?></td>
						    			<td width="80"><?=$row['cut_no'];?></td>
						    			<td width="80"><?=$order_cut_no;?></td>
						    			<?
						    			$tot_qty = 0;
						    			foreach ($size_arr as $s_id => $s_val)
						    			{
						    				$size_qty = $size_qty_arr[$challan_no][$job_id][$group][$style][$s_id];
						    				?>
						    				<td width="50" align="right"><?=$size_qty;?></td>
						    				<?
						    				$tot_qty += $size_qty;
						    				$sub_total_arr[$challan_no][$s_id] += $size_qty;
						    				$grand_total_array[$s_id] += $size_qty;
						    			}
						    			?>
						    			<td width="80" align="right"><?=$tot_qty;?></td>
						    			<td width="80" align="right"><?=$row['total_bundle']; $grand_total_bundle+=$row['total_bundle'];?></td>
						    			
									</tr>
								<?
								$i++;
							}
						}
					}
					?>
					<tr bgcolor="#cddcdc" style="text-align: right;font-weight: bold;">
						<td colspan="11">Challan Wise Sub Total</td>
						<?
		    			$tot_qty = 0;
		    			foreach ($size_arr as $s_id => $s_val)
		    			{
		    				$size_qty = $sub_total_arr[$challan_no][$s_id];
		    				?>
		    				<td width="50" align="right"><?=$size_qty;?></td>
		    				<?
		    				$tot_qty += $size_qty;
		    			}
		    			?>
		    			<td><?=number_format($tot_qty,0);?></td>
		    			<td></td>
		    		
					</tr>
					<?
				}
    			?>
    			<tfoot>
    				<tr>
						<th colspan="11">Grand Total</th>
						<?
		    			$tot_qty = 0;
		    			foreach ($size_arr as $s_id => $s_val)
		    			{
		    				$size_qty = $grand_total_array[$s_id];
		    				?>
		    				<th width="50" align="right"><?=$size_qty;?></th>
		    				<?
		    				$tot_qty += $size_qty;
		    			}
		    			?>
		    			<th><?=number_format($tot_qty,0);?></th>
		    			<th><? echo number_format($grand_total_bundle,0);?></th>
					</tr>
    			</tfoot>
    		</table>
	    </div>
    </fieldset>
   <?
	exit();
}


?>