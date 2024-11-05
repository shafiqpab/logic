<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];



if($db_type==0)
{
	$defalt_date_format="0000-00-00";
}
else
{
	$defalt_date_format="";
}


//--------------------------------------------------------------------------------------------------------------------
$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$company_library_short=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$supplier_library=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');

$party_library=return_library_array( "SELECT a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=23 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id", "supplier_name"  );

$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );
$location_arr=return_library_array( "select id, location_name from lib_location", "id", "location_name");

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 130, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id  in($data) order by location_name","id,location_name", 1, "-- Select Location--", $selected, "",0 );   
	exit();  	 
}
if ($action=="load_drop_down_buyer_popup")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" );     	 
	exit();
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "onchange_buyer()" );     	 
	exit();
}

if ($action == "load_drop_down_party")
{
	$data = explode("_", $data);
	$company_id = $data[1];

	//$company_id
	if ($data[0] == 1) 
	{
		echo create_drop_down("cbo_party_name", 130, "SELECT comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "--Select Company--", "", "", "");
	} 
	else if ($data[0] == 3) 
	{
		echo create_drop_down("cbo_party_name", 130, "SELECT a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=23 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--Select Supplier--", 0, "");
	} 
	else 
	{
		echo create_drop_down("cbo_party_name", 130, $blank_array, "", 1, "--Select Company--", 0, "");
	}
	exit();
}


if($action=="order_popup")
{
			extract($_REQUEST);
			echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
			//echo $search_by;



		?>
			<script>
				$(document).ready(function(e) {
					$("#txt_search_common").focus();
								$("#company_search_by").val(<?php echo $_REQUEST['company_name'] ?>);

				});

				function search_populate(str)
				{
					//alert(str);
					if(str==1)
					{
						document.getElementById('search_by_th_up').innerHTML="Po No";
						document.getElementById('search_by_td').innerHTML='<input onkeydown="getActionOnEnter(event)"	type="text"	name="txt_search_common" style="width:142px " class="text_boxes" id="txt_search_common"	value=""  />';
					}
					else if(str==2)
					{
						document.getElementById('search_by_th_up').innerHTML="Job NO";
						document.getElementById('search_by_td').innerHTML='<input	type="text" onkeydown="getActionOnEnter(event)"	name="txt_search_common" style="width:142px " class="text_boxes" id="txt_search_common"	value=""  />';
					}
					else if(str==3)
					{
						document.getElementById('search_by_th_up').innerHTML="Style Ref";
						document.getElementById('search_by_td').innerHTML='<input	type="text" onkeydown="getActionOnEnter(event)"	name="txt_search_common" style="width:142px " class="text_boxes" id="txt_search_common"	value=""  />';
					}


					
				}

			function js_set_value(style_no)
			{
				
				let style_no_val = style_no.split(",");
				$("#txt_selected_style_no").val(style_no_val[1]);

				// alert(style_no);
				// //$("#selected_name").val(buyer_name);	
				parent.emailwindow.hide();
			}
		</script>
		</head>
		<body>
		<div align="center" style="width:100%;" >
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table width="780" cellspacing="0" cellpadding="0" class="rpt_table" align="center" border="1" rules="all" >
					<tr>
						<td align="center" width="100%">
							<table ellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
								<thead>
									<th width="130">Company</th>
									<th width="130">Search By</th>
									<th  width="130" align="center" id="search_by_th_up">Enter Order Number</th>
									<th width="200">Date Range</th>
									<th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
									<input type="hidden" id="selected_id">
									<input type="hidden" id="txt_selected_style_no">
									<input type="hidden" id="txt_selected_job"> 
									<input type="hidden" id="selected_name"> 
									
								</thead>
								<tr>
									
								<td width="130">
										<?
										echo create_drop_down( "company_search_by", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select --", $selected, "", 0 );
										?>
									</td>
									<td width="130">
									<?
									$ttt_search_by=array(1=>"Po No",2=>"Job No", 3=>"Style No");
									echo create_drop_down( "cbo_search_by", 130, $ttt_search_by,"", 1, "-- Select --",$search_by , "search_populate(this.value)",1);
									?>
									</td>
									<td width="130" align="center" id="search_by_td">
										<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" onKeyDown="if (event.keyCode == 13) document.getElementById('btn_show').click()" />
									</td>
									<td align="center">
										<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"> To
										<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
									</td>
									<td align="center">
										<input type="button" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('company_search_by').value+'_'+<? echo $garments_nature; ?>+'_'+<? echo $garments_nature; ?>, 'create_po_search_list_view', 'search_div', 'emb_delivery_and_received_status_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:100px;" />
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td  align="center" height="40" valign="middle">
							<? echo load_month_buttons(1);  ?>
							<input type="hidden" id="selected_id">
							<input type="hidden" id="selected_name">
						
						</td>
					</tr>
				</table>
				<div style="margin-top:10px" id="search_div"></div>
			</form>
		</div>
		</body>
		<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
		</html>
		<?
}

if($action=="create_po_search_list_view")
{
 	$ex_data = explode("_",$data);
	if($ex_data[4]== 0)
	{
		//print_r ($data);die;
		echo "Please Select Company First."; die;
	}
	if($ex_data[1]=="" && $ex_data[3]=="" )
	{
     echo "Please Select Search By OR Date Range Field"; die;
	}
	$cbo_search_by = $ex_data[0];
	$txt_search_common = $ex_data[1];
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company = $ex_data[4];
 	$garments_nature = $ex_data[5];
	//echo $cbo_search_by;die;

	$sql_cond="";
	if(trim($txt_search_common)!="")
	{
		if(trim($cbo_search_by)==1)
			$sql_cond = " and b.po_number like '%".trim($txt_search_common)."%'";
			else if(trim($cbo_search_by)==2)
			$sql_cond = " and a.job_no_prefix_num like '%".trim($txt_search_common)."%'";
		else if(trim($cbo_search_by)==3)
			$sql_cond = " and a.style_ref_no like '%".trim($txt_search_common)."%'";
		else if(trim($cbo_search_by)==4)
			$sql_cond = " and a.buyer_name=trim('$txt_search_common')";
		
			
 	}
	if($txt_date_from!="" || $txt_date_to!="")
	{
		if($db_type==0){$sql_cond .= " and b.shipment_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";}
		if($db_type==2 || $db_type==1){ $sql_cond .= " and b.shipment_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";}
	}

	if(trim($company)!="") $sql_cond .= " and a.company_name='$company'";

	$is_projected_po_allow=return_field_value("production_entry","variable_settings_production","variable_list=58 and company_name=$company");
    $projected_po_cond = ($is_projected_po_allow==2) ? " and b.is_confirmed=1" : "";


 	  $sql = "SELECT b.id, a.id as job_id, a.job_no_prefix_num,a.order_uom, a.buyer_name, a.company_name, a.total_set_qnty, a.set_break_down, a.job_no, a.style_ref_no, a.gmts_item_id, a.location_name, b.shipment_date, b.po_number,b.file_no,b.grouping, b.po_quantity, b.plan_cut from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_mst c,wo_pre_cost_dtls d where a.id = b.job_id and a.status_active=1 and  a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond $projected_po_cond  and a.id=c.job_id and c.job_no=d.job_no and ( d.embel_cost !=0 or d.wash_cost !=0 ) and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 order by b.id desc";  //and a.garments_nature=$garments_nature 
       // echo $sql; die;


	$result = sql_select($sql);
	$po_id_arr = array();
	foreach ($result as $val) 
	{
		$po_id_arr[$val[csf('id')]] = $val[csf('id')];
	}
	$allPoIds = implode(",", $po_id_arr);
 	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

	
	if($db_type==0)
	{
		$po_country_arr=return_library_array( "SELECT po_break_down_id, group_concat(distinct(country_id)) as country from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 and po_break_down_id in($allPoIds) group by po_break_down_id",'po_break_down_id','country');
	}
	else
	{
		$po_country_arr=return_library_array( "SELECT po_break_down_id, listagg(CAST(country_id as VARCHAR(4000)),',') within group (order by country_id) as country from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 and po_break_down_id in($allPoIds) group by po_break_down_id",'po_break_down_id','country');
	}

	$po_country_data_arr=array();
	$poCountryData=sql_select( "SELECT po_break_down_id, item_number_id, country_id, sum(order_quantity) as qnty, sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 and po_break_down_id in($allPoIds) group by po_break_down_id, item_number_id, country_id");

	foreach($poCountryData as $row)
	{
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['po_qnty']=$row[csf('qnty')];
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['plan_cut_qnty']=$row[csf('plan_cut_qnty')];
	}

	$total_issu_qty_data_arr=array();
	$total_issu_qty_arr=sql_select( "SELECT po_break_down_id, item_number_id, country_id, sum(production_quantity) as production_quantity from pro_garments_production_mst where status_active=1 and is_deleted=0 and production_type=2 and po_break_down_id in($allPoIds) group by po_break_down_id, item_number_id, country_id");

	foreach($total_issu_qty_arr as $row)
	{
		$total_issu_qty_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]=$row[csf('production_quantity')];
	}

	?>
    <div style="width:1190px;">
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="30">SL</th>
              
              
                <th width="100">Order No</th>
                <th width="80">Buyer</th>
                <th width="100">Style</th>  
                <th width="140">Item</th>
                <th width="100">Country</th>
                <th width="80">Order Qty</th>
                
                <th width="100">Company Name</th>
            </thead>
     	</table>
     </div>
     <div style="width:1190px; max-height:240px;overflow-y:scroll;" >
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1172" class="rpt_table" id="tbl_po_list">
			<?
			$i=1;
            foreach( $result as $row )
            {
				$exp_grmts_item = explode("__",$row[csf("set_break_down")]);
				$numOfItem = count($exp_grmts_item);
				$set_qty=""; $grmts_item="";

				$country=array_unique(explode(",",$po_country_arr[$row[csf("id")]]));
				//$country=explode(",",$po_country_arr[$row[csf("id")]]);
				$numOfCountry = count($country);

				for($k=0;$k<$numOfItem;$k++)
				{
					if($row["total_set_qnty"]>1)
					{
						$grmts_item_qty = explode("_",$exp_grmts_item[$k]);
						$grmts_item = $grmts_item_qty[0];
						$set_qty = $grmts_item_qty[1];
					}else
					{
						$grmts_item_qty = explode("_",$exp_grmts_item[$k]);
						$grmts_item = $grmts_item_qty[0];
						$set_qty = $grmts_item_qty[1];
					}

					foreach($country as $country_id)
					{
						if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						//$po_qnty=$row[csf("po_quantity")]; $plan_cut_qnty=$row[csf("plan_cut")];
						$po_qnty=$po_country_data_arr[$row[csf('id')]][$grmts_item][$country_id]['po_qnty'];
						$plan_cut_qnty=$po_country_data_arr[$row[csf('id')]][$grmts_item][$country_id]['plan_cut_qnty'];
						if($cbo_search_by==1)
						{
							$searc_by=$row[csf("id")].",".$row[csf("po_number")];
						}
						else if($cbo_search_by==2)
						{
							$searc_by=$row[csf("job_id")].",".$row[csf("job_no")];
						}
						else if($cbo_search_by==3)
						{
							$searc_by=$row[csf("job_id")].",".$row[csf("style_ref_no")];
						}

						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $searc_by;?>');" >
							<td width="30" align="center"><?php echo $i; ?></td>
						
							
							<td width="100"><p><?php echo $row[csf("po_number")]; ?></p></td>
							<td width="80"><p><?php echo $buyer_arr[$row[csf("buyer_name")]]; ?></p></td>
							<td width="100"><p><?php echo $row[csf("style_ref_no")]; ?></p></td>
                            
                            
							<td width="140"><p><?php  echo $garments_item[$grmts_item];?></p></td>
							<td width="100"><p><?php echo $country_library[$country_id]; ?></p></td>
							<td width="80" align="right"><?echo $po_qnty;?></td>
                            
							<td width="100"><?php  echo $company_arr[$row[csf("company_name")]];?> </td>
						</tr>
						<?
						$i++;
					}
				}
            }
   		?>
        </table>
    </div>
	<?
 exit();
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	// ========================================= GETTING SEARCH PARAMETER ========================================
	$company_name 		= str_replace("'", "", $cbo_company_name);
	$buyer_name 		= str_replace("'", "", $cbo_buyer_name);
	$emb_type 			= str_replace("'", "", $cbo_emb_type);
	$source 			= str_replace("'", "", $cbo_source);
	$party_id 			= str_replace("'", "", $cbo_party_name);
	$po_no 			   = str_replace("'", "", $txt_po_no);
	$date_from 			= str_replace("'", "", $txt_date_from);
	$date_to 			= str_replace("'", "", $txt_date_to);
	$search_by  = str_replace("'", "", $cbo_search_by);
//	echo $search_by;die;
	
		 
	$sql_cond = "";
	if($search_by==1)
	{
		$sql_cond.=($po_no != "") ? " and b.po_number ='$po_no'" : "";
		
	}
	else if($search_by==2)
	{
		$sql_cond.=($po_no != "") ? " and a.job_no ='$po_no'" : "";
	}
	else if($search_by==3)
	{
		$sql_cond.=($po_no != "") ? " and a.style_ref_no ='$po_no'" : "";
	}

	$sql_cond .= ($company_name != 0) ? " and a.company_name =$company_name" : "";
	
	$sql_cond .= ($source != 0) ? " and d.production_source=$source" : "";
	$sql_cond .= ($party_id != 0) ? " and d.serving_company =$party_id" : "";

	$sql_cond .= ($emb_type != 0) ? " and d.embel_name = $emb_type" : "";
	$sql_cond .= ($date_from != "" && $date_to != "") ? " and d.production_date between $txt_date_from and $txt_date_to" : "";
	if($source!=0)$sql_cond .= " and d.production_source=$source";
	
	$com_sql=sql_select("select a.id, a.company_name, a.city from lib_company a where a.id=$company_name");

  
    $location_name=$com_sql[0][csf("city")];
	

	
	/* ==================================================================================== /
	/ 										main query										/
	/ ===================================================================================  */
	 $sql = "SELECT a.company_name,a.buyer_name, a.order_uom as uom, a.style_ref_no, b.id as po_id,b.po_number,c.item_number_id,c.color_number_id,d.embel_name,d.production_type,d.challan_no,d.serving_company,d.production_date,d.remarks, d.id as sys_challan_id, e.production_qnty,e.reject_qty
	 from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e
	 where  a.id=b.job_id and b.id=c.po_break_down_id and d.po_break_down_id=b.id and d.po_break_down_id=c.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id  and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3)  and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0 and d.production_type in (2,3) $sql_cond  ORDER BY  d.production_date"  ;				
	//echo $sql;die();		
	$sql_res = sql_select($sql);		
	
	$data_array = array();
	
	foreach($sql_res as $row ){
		$data_array[$row[csf('production_type')]][$row[csf('serving_company')]][$row[csf('po_id')]][$row[csf('color_number_id')]][$row[csf('challan_no')]][$row[csf('sys_challan_id')]][$row[csf('production_date')]]['buyer_name']= $row[csf('buyer_name')];

		$data_array[$row[csf('production_type')]][$row[csf('serving_company')]][$row[csf('po_id')]][$row[csf('color_number_id')]][$row[csf('challan_no')]][$row[csf('sys_challan_id')]][$row[csf('production_date')]]['company_name']= $row[csf('company_name')];

		

		$data_array[$row[csf('production_type')]][$row[csf('serving_company')]][$row[csf('po_id')]][$row[csf('color_number_id')]][$row[csf('challan_no')]][$row[csf('sys_challan_id')]][$row[csf('production_date')]]['po_number']= $row[csf('po_number')];

		$data_array[$row[csf('production_type')]][$row[csf('serving_company')]][$row[csf('po_id')]][$row[csf('color_number_id')]][$row[csf('challan_no')]][$row[csf('sys_challan_id')]][$row[csf('production_date')]]['item_number_id']= $row[csf('item_number_id')];


		$data_array[$row[csf('production_type')]][$row[csf('serving_company')]][$row[csf('po_id')]][$row[csf('color_number_id')]][$row[csf('challan_no')]][$row[csf('sys_challan_id')]][$row[csf('production_date')]]['embel_name']= $row[csf('embel_name')];

		$data_array[$row[csf('production_type')]][$row[csf('serving_company')]][$row[csf('po_id')]][$row[csf('color_number_id')]][$row[csf('challan_no')]][$row[csf('sys_challan_id')]][$row[csf('production_date')]]['uom']= $row[csf('uom')];

		$data_array[$row[csf('production_type')]][$row[csf('serving_company')]][$row[csf('po_id')]][$row[csf('color_number_id')]][$row[csf('challan_no')]][$row[csf('sys_challan_id')]][$row[csf('production_date')]]['production_qnty']+= $row[csf('production_qnty')];

		$data_array[$row[csf('production_type')]][$row[csf('serving_company')]][$row[csf('po_id')]][$row[csf('color_number_id')]][$row[csf('challan_no')]][$row[csf('sys_challan_id')]][$row[csf('production_date')]]['reject_qty']+= $row[csf('reject_qty')];

		$data_array[$row[csf('production_type')]][$row[csf('serving_company')]][$row[csf('po_id')]][$row[csf('color_number_id')]][$row[csf('challan_no')]][$row[csf('sys_challan_id')]][$row[csf('production_date')]]['remarks']= $row[csf('remarks')];

		$data_array[$row[csf('production_type')]][$row[csf('serving_company')]][$row[csf('po_id')]][$row[csf('color_number_id')]][$row[csf('challan_no')]][$row[csf('sys_challan_id')]][$row[csf('production_date')]]['style_ref_no']= $row[csf('style_ref_no')];

	}
	//echo "<pre>"; print_r($data_array); echo "</pre>"; die;
	
	
	ob_start();
	?>

		<div align="center"style="width:1100px; margin: 0 auto">
		
			<table  width="1100"  cellspacing="0" >
			<tr class="form_caption">
					<td  align="center" style="font-size:18px"><? echo $company_library[$company_name]; ?></td>
				</tr>
				<tr class="form_caption">
					<td  align="center" style="font-size:18px"><? echo $location_name;?></td>
				</tr>
				<tr class="form_caption">
					<td  align="center" style="font-size:20px">Cutting Part Print Delivery Summary</td>
				</tr>
				
			</table>
			
			<table align="left">
				<tr>
				 <td   style="font-size:22px">Applicant:<? echo $company_library[$row[csf('company_name')]];?></td>

			  </tr>
		   </table>
		  
			
			<table align="left" class="rpt_table" width="1080" cellpadding="0" cellspacing="0" border="1" rules="all">
			
				<thead>

					<th width="100">Name of Party</th>
                    <th width="100">Order</th>
                    <th width="100">Style</th>
                    <th width="100">Buyer </th>
					<th width="80">Color</th>
                    <th width="80"> Date </th>
                    <th width="80"> Issue Challan No </th>
					<th width="60">Cha.NO</th>
                    <th width="60">Item Name</th>
                    <th width="40">Unit</th>
                    <th width="60">Ok.Goods</th>
					<th width="80">Remarks</th>
					
				</thead>
			 </table>
			<div style="width:1100px; max-height:400px; overflow-y:auto">
				<table  align="left" class="rpt_table" width="1080" cellpadding="0" cellspacing="0" border="1" >
					<?
					$total_ok_goods=0;
					
					$i=1;
					// echo"<pre>";print_r($data_array[2]);die;
					foreach ($data_array[2] as $party_key => $party_data) 
					{
						foreach ($party_data as $po_id => $po_data) 
						{
							foreach ($po_data as $color_key => $color_data) 
							{
								foreach ($color_data as $challan_no => $challan_data) 
								{
								   foreach ($challan_data as $sys_challan => $sys_challan_data ) 
								   {
								   	foreach ($sys_challan_data as $prod_date => $v ) 
								   	{
                                 
									    	$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";

									    	$party_name = '';

									    	if($source==1)
									    	{
									    		$party_name = $company_library[$party_key];
									    	}
									    	else
									    	{
									    		$party_name = $supplier_library[$party_key];
									    	}	
						
										
											?>
											<tr bgcolor="<? echo $bgcolor;?>" id="tr_<?= $i;?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')">
												
												<td width="100"><p><? echo $party_name;?></p></td>
												<td width="100"><p><? echo $v['po_number'];?></p></td>
												<td width="100"><p><? echo $v['style_ref_no'];?></p></td>
												<td width="100"><p><? echo $buyer_library[$v['buyer_name']];?></p></td>
												<td width="80"><p><? echo $color_library[ $color_key];?></p></td>
												<td width="80" align="center"><? echo $prod_date;?></td>
												<td width="80" align="center"><? echo $sys_challan;?></td>
												<td width="60" align="center"><? echo $challan_no;?></td>
												<td width="60"><p><?echo $garments_item[$v['item_number_id']];?></p></td>
												<td width="40"><p><?echo $unit_of_measurement[$v['uom']]?></p></td>
												<td width="60" align="right"><? echo $v['production_qnty'];?></td>
												<td width="80"><?echo $v['remarks']?></td>
												
											</tr>
										
												<?
												$total_ok_goods+=$v['production_qnty'];
												
												$i++;
										}
							    	}			
							   }
				            }
			            }	 
					}
																	
									
							
					?>									
				</table>
			</div>
			<div style="width:1100px;">
				<table  align="left" class="rpt_table" width="1080" cellpadding="0" cellspacing="0" border="1">
					<tfoot>
					                        <th width="100"></th>
											<th width="100"></th>
											<th width="100"></th>
											<th width="100"></th>
											<th width="80"></th>
											<th width="80"></th>
											<th width="80"></th>
											<th width="60"></th>
											<th width="60">TOTAL</th>
											<th width="40" style="text-align:left"><? echo $unit_of_measurement[$rows['uom']]; ?></th>
											<th width="60" align="right"><? echo $total_ok_goods?></th>
											<th width="80"></th>
					</tfoot>
				</table>
			</div>  
			<br>
			<br>
      <!-- ==================================== 1st part end and 2nd part start ========================= -->
		<div>
			<table width="1100">
			<tr class="form_caption">
					<td  align="center" style="font-size:18px"><? echo $company_library[$company_name]; ?></td>
				</tr>
				<tr class="form_caption">
					<td  align="center" style="font-size:18px"><? echo $location_name;?></td>
				</tr>
				<tr class="form_caption">
					<td  align="center" style="font-size:20px">Cutting Part Print Received Summary</td>
				</tr>
				
			</table>
			<table align="left">
				<tr >
				  <td    style="font-size:22px">Applicant:<? echo $company_library[$row[csf('company_name')]];?></td>
			   </tr>
		   </table>
			<table  align="left" class="rpt_table" width="1080" cellpadding="0" cellspacing="0" >
			
				<thead>

					<th width="100">Name of Party</th>
                    <th width="100">Order</th>
                    <th width="100">Style</th>
                    <th width="100">Buyer </th>
					<th width="80">Color</th>
                    <th width="80"> Date </th>
                    <th width="80"> Delivery Chanllan No </th>
					<th width="60">Cha.NO</th>
                    <th width="60">Item Name</th>
                    <th width="40">Unit</th>
                    <th width="60">Ok.Goods</th>
					<th width="60">Rej.Goods</th>
					<th width="60">T.Goods</th>
                    <th width="80">Remarks</th>

					
				</thead>
			 </table>
		</div>	 
			 <div style="width:1100px; max-height:400px; overflow-y:auto">
				<table  align="left" class="rpt_table" width="1080" cellpadding="0" cellspacing="0" border="1" >
				<?
					   $total_ok_goods=0;
					   $total_rej_goods=0;
					   $i=1;
					  
					foreach ($data_array[3] as $party_key => $party_data) 
					{
						foreach ($party_data as $po_id => $po_data) 
						{
							foreach ($po_data as $color_key => $color_data) 
							{
								foreach ($color_data as $challan_no => $challan_data) 
								{
									foreach ($challan_data as $sys_challan => $sys_challan_data ) 
								    {
								    	foreach ($sys_challan_data as $prod_date => $v ) 
								   		{
									  		$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";

									  		$party_name = '';

									    	if($source==1)
									    	{
									    		$party_name = $company_library[$party_key];
									    	}
									    	else
									    	{
									    		$party_name = $supplier_library[$party_key];
									    	}
							
											?>
													<tr bgcolor="<? echo $bgcolor;?>" id="tr_<?= $i;?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')">	
														
															<td width="100"><p><? echo $party_name;?></p></td>
															<td width="100"><p><? echo $v['po_number'];?></p></td>
															<td width="100"><p><? echo $v['style_ref_no'];?></p></td>
															<td width="100"><p><? echo $buyer_library[$v['buyer_name']];?></p></td>
															<td width="80"><p><? echo $color_library[$color_key];?></p></td>
															<td width="80" align="center"><? echo $prod_date;?></td>
															<td width="80" align="center"><? echo $sys_challan;?></td>
															<td width="60" align="center"><?  echo $challan_no;?></td>
															<td width="60"><p><? echo $garments_item[$v['item_number_id']];?></p></td>
															<td width="40"><p><? echo $unit_of_measurement[$v['uom']];?></p></td>
															<td width="60" align="right"><? echo $v['production_qnty'];?></td>
															<td width="60" align="right"><? echo $v['reject_qty'] ;?></td>
															<td width="60" align="right"><? echo $v['production_qnty'];?></td>
															<td width="80"><? echo $v['remarks'];?></td>
														
													</tr>
												
														<?

														$total_ok_goods+=$v['production_qnty'];
														$total_rej_goods+=$v['reject_qty'] ;
													
														$i++;
												}
								        }			
					                }
				                 }
			             }
				   }	 
																	
									
							
					?>								
				</table>
			</div>
			<div  style="width:1100px;">
				<table  align="left" class="rpt_table" width="1080" cellpadding="0" cellspacing="0" border="1">
					<tfoot>
						<tr>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="80"></th>
							<th width="80"></th>
							<th width="80"></th>
							<th width="60"></th>
							<th width="60">TOTAL</th>
							<th width="40" style="text-align:left"><? echo $unit_of_measurement[$rows['uom']]; ?></th>
							<th width="60"><?echo $total_ok_goods;?></th>
							<th width="60"><?echo $total_rej_goods;?></th>
							<th width="60" style="text-align:right"><?echo $total_ok_goods;?></th>
							<th width="80"></th>
						</tr>
						<tr>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="100"></th>
							<th width="80"></th>
							<th width="80"></th>
							<th width="80"></th>
							<th width="60"></th>
							<th width="60">G.TOTAL</th>
							<th width="40" style="text-align:left"><? echo $unit_of_measurement[$rows['uom']];?></th>
							<th colspan="3" style="text-align:center"><? echo ($total_ok_goods+$total_rej_goods)?></th>
							<th width="80"></th>
						</tr>
					</tfoot>
				</table>
			</div> 
		
	</div>
	<?
		
	
	foreach (glob("*.xls") as $filename) 
	{
		//if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data****$filename";
	disconnect($con);
	exit();	
	
}



