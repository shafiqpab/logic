<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
$user_name=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//------------------------------------------------------------------------------------------

if($action=="order_wise_search")
{		  
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	?>
	<script>
		
		var selected_id = new Array;
		var selected_name = new Array;
		
    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
				
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) { 
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( strCon ) 
		{
				var splitSTR = strCon.split("_");
				var str = splitSTR[0];
				var selectID = splitSTR[1];
				var selectDESC = splitSTR[2];
				//$('#txt_individual_id' + str).val(splitSTR[1]);
				//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');
				
				toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
				
				if( jQuery.inArray( selectID, selected_id ) == -1 ) {
					selected_id.push( selectID );
					selected_name.push( selectDESC );					
				}
				else {
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == selectID ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 ); 
				}
				var id = ''; var name = ''; var job = '';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					name += selected_name[i] + ','; 
				}
				id 		= id.substr( 0, id.length - 1 );
				name 	= name.substr( 0, name.length - 1 ); 
				
				$('#txt_selected_id').val( id );
				$('#txt_selected').val( name ); 
		}
    </script>
	<?
	extract($_REQUEST);
	//echo $job_no;die;
	if($company==0) $company_name=""; else $company_name=" and b.company_name=$company";
	if($buyer==0) $buyer_name=""; else $buyer_name="and b.buyer_name=$buyer";
	$job_cond='';
	if(str_replace("'","",$job_id)!="")  $job_cond="and b.id in(".str_replace("'","",$job_id).")";
    else  if (str_replace("'","",$job_no)!="") $job_cond="and a.job_no_mst='".$job_no."'";
	else if($cbo_year!=0)
	{
	if($db_type==0) $job_cond=" and SUBSTRING_INDEX(b.insert_date, '-', 1)=".str_replace("'","",$cbo_year)." ";
    if($db_type==2) $job_cond=" and extract( year from b.insert_date)=".str_replace("'","",$cbo_year)."";
	}
	
	if(str_replace("'","",$style_id)!="")  $style_cond="and b.id in(".str_replace("'","",$style_id).")";
    else  if (str_replace("'","",$style_no)=="") $style_cond=""; else $style_cond="and b.style_ref_no='".$style_no."'";
	$sql = "select distinct a.id,a.po_number,b.style_ref_no,b.job_no_prefix_num,b.insert_date as year from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no $company_name ";
	
	echo create_list_view("list_view", "Year,Job No,Style Ref,Order Number","50,100,120,150,","550","310",0, $sql , "js_set_value", "id,po_number", "", 1, "0", $arr, "year,job_no_prefix_num,style_ref_no,po_number", "","setFilterGrid('list_view',-1)","0","",1) ;	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	exit();
}


if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" );     	 
	exit();
}

if($action=="job_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $buyer;die;
	?>
	
	<script>
    function js_set_value(id)
    {
		//alert(id);
		document.getElementById('selected_id').value=id;
		parent.emailwindow.hide();
    }
    </script>
    </head>
    <body>
    <div align="center" style="width:820px;">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:800px;">
            <table width="800" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Company</th>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Job No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" id="selected_id" name="selected_id" />
                </thead>
                <tbody>
                	<tr class="general">
                    	<td align="center"> 
							<?
                                echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                            ?>
                        </td>
                        <td align="center">
                        	 <? 
								//echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company_id $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
								if($buyer>0) $buy_cond=" and a.id=$buyer";
								echo create_drop_down( "cbo_buyer_name", 140, "select a.id,a.buyer_name from lib_buyer a where a.status_active=1 and a.is_deleted=0 $buy_cond order by a.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0,"" );
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year; ?>', 'job_popup_search_list_view', 'search_div', 'cutting_and_input_status_report_controller', 'setFilterGrid(\'table_body2\',-1)');" style="width:100px;" />
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

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$company_id 	= str_replace("'", "", $cbo_company_name);	
	$buyer_id 		= str_replace("'", "", $cbo_buyer_name);
	$txt_style_no 	= str_replace("'", "", $txt_style_no);
	$txt_job_no 	= str_replace("'", "", $txt_job_no);
	$txt_order_no 	= str_replace("'", "", $txt_order_no);	
	$txt_date_from 	= str_replace("'", "", $txt_date_from);
	$txt_date_to 	= str_replace("'", "", $txt_date_to);
	 
	$lib_buyer=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
	$lib_supplier=return_library_array( "select id, supplier_name from  lib_supplier",'id','supplier_name');
	$lib_company=return_library_array( "select id, company_name from  lib_company",'id','company_name');
	$lib_location=return_library_array( "select id, location_name from  lib_location",'id','location_name');
	$lib_floor=return_library_array( "select id, floor_name from  lib_prod_floor",'id','floor_name');
	$lib_color=return_library_array( "select id, color_name from  lib_color",'id','color_name');

	

		/* =================================================================================/
	    / 										SQL Condition								/
	    /================================================================================= */

		$sql_cond = "";
		$sql_cond .= ($company_id==0) ? "": " and b.company_name=$company_id";
	
		$sql_cond .= ($buyer_id==0) ? "": " and b.buyer_name=$buyer_id";
	
		$sql_cond .= ($txt_order_no=="") ? "": " and a.po_number = '$txt_order_no'";
		$sql_cond .= ($txt_job_no=="") ? "": " and a.job_no_mst = '$txt_job_no'";	
	    $job_cond2 = ($txt_job_no=="") ? "": " and a.job_no = '$txt_job_no'";

		if(str_replace("'", "", $txt_date_from) !="")
		{
			if($db_type==0)
			{ 
				$date_cond="and production_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."' ";
				$date_cond2="and c.production_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."' ";
				$date_cond3="and con_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."' ";
				$issue_date_cond .= " and a.issue_date  between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
			}
			else
			{
				$date_cond="and production_date between '".change_date_format($txt_date_from, "", "",1)."' and '".change_date_format($txt_date_to, "", "",1)."' ";
				$date_cond2="and c.production_date between '".change_date_format($txt_date_from, "", "",1)."' and '".change_date_format($txt_date_to, "", "",1)."' ";
				$date_cond3="and con_date between '".change_date_format($txt_date_from, "", "",1)."' and '".change_date_format($txt_date_to, "", "",1)."' ";
				$issue_date_cond .= " and a.issue_date  between '".change_date_format($txt_date_from,'yyyy-mm-dd','',-1)."' and '".change_date_format($txt_date_to,'yyyy-mm-dd','',-1)."'";
			}

			
		}

		//	=====================================print qty============================================


		 $currency_data=sql_select("select id,company_id,currency,conversion_rate,marketing_rate,con_date from currency_conversion_rate where status_active=1 and is_deleted=0 and company_id=$company_id order by id asc ");

		//  echo "<pre>";
	//   print_r($currency_data);
		$currency=$currency_data[0][csf("conversion_rate")];
	

	$print_qty_data=sql_select("SELECT id, po_break_down_id,embel_name,production_source, item_number_id, country_id, production_date, production_quantity, reject_qnty, production_source, serving_company, embel_name from pro_garments_production_mst where   embel_name='1' and production_type='3' and production_source=1 and status_active=1 and is_deleted=0 $date_cond order by id");

	foreach($print_qty_data as $row){

		$print_qty[$row[csf("po_break_down_id")]]  +=$row[csf("production_quantity")];
	}


	//=============================================== rate query ==========================================

	$p_rate_data=sql_select("SELECT  a.id,a.emb_name,a.emb_type,a.job_no,a.cons_dzn_gmts, a.rate, a.amount,b.costing_per from wo_pre_cost_embe_cost_dtls a,wo_pre_cost_mst b where  a.emb_name=1  and  a.job_no=b.job_no $job_cond2 and a.status_active=1 and a.is_deleted=0 order by a.id");
	$rate_cal=1;
	$print_rate_arr=array();
	foreach($p_rate_data as $row){

				if($row[csf("costing_per")]=1){//For 1 Dzn
					$rate_cal=12;
				}elseif($row[csf("costing_per")]=2){//For 1 Pcs
					$rate_cal=1;
				}elseif($row[csf("costing_per")]=3){//For 2 Dzn
					$rate_cal=24;
				}elseif($row[csf("costing_per")]=4){//For 3 Dzn
					$rate_cal=36;
				}elseif($row[csf("costing_per")]=5){//For 4 Dzn
					$rate_cal=48;
				}

		
		// $print_rate_arr[$row[csf("job_no")]]=$row[csf("rate")]/$rate_cal;
		$print_rate_arr[$row[csf("job_no")]][$row[csf("emb_type")]]=$row[csf("rate")]/$rate_cal;
		$print_rate_id[$row[csf("job_no")]]=$row[csf("id")];

		//  $print_rate_arr[$row[csf("job_no")]]=$row[csf("job_no")];

	
}
$e_rate_data=sql_select("SELECT  a.id,a.emb_name,a.emb_type,a.job_no,a.cons_dzn_gmts, a.rate, a.amount,b.costing_per from wo_pre_cost_embe_cost_dtls a,wo_pre_cost_mst b where  a.emb_name=2  and  a.job_no=b.job_no $job_cond2 and a.status_active=1 and a.is_deleted=0 order by a.id");
$rate_cal=1;

foreach($e_rate_data as $row){

				if($row[csf("costing_per")]=1){//For 1 Dzn
					$rate_cal=12;
				}elseif($row[csf("costing_per")]=2){//For 1 Pcs
					$rate_cal=1;
				}elseif($row[csf("costing_per")]=3){//For 2 Dzn
					$rate_cal=24;
				}elseif($row[csf("costing_per")]=4){//For 3 Dzn
					$rate_cal=36;
				}elseif($row[csf("costing_per")]=5){//For 4 Dzn
					$rate_cal=48;
				}

			$embl_rate_arr[$row[csf("job_no")]][$row[csf("emb_type")]]=$row[csf("rate")]/$rate_cal;
			//  $embl_rate_arr[$row[csf("job_no")]]=$row[csf("job_no")];
	
}

//  print_r($print_rate_arr);


//	=====================================emb qty============================================

$emb_qty_data=sql_select("SELECT id, po_break_down_id,embel_name, item_number_id, production_date, production_quantity from pro_garments_production_mst where embel_name='2' and production_type='3' and production_source=1 and status_active=1 and is_deleted=0 $date_cond order by id");

foreach($emb_qty_data as $row){

	$emb_qty[$row[csf("po_break_down_id")]] +=$row[csf("production_quantity")];
}

$job_wise_order_qty=sql_select("SELECT a.is_confirmed,  a.po_number,  a.po_received_date,  a.pub_shipment_date, b.company_name, b.buyer_name,  a.shipment_date,b.style_ref_no,  a.po_quantity,  a.unit_price,  a.po_total_price,  a.excess_cut,  a.plan_cut, ( a.pub_shipment_date- a.po_received_date) as date_diff, ( a.pub_shipment_date- a.factory_received_date) as facdate_diff,  a.status_active,  a.id,b.job_no from wo_po_break_down a,wo_po_details_master b where  a.JOB_NO_MST=b.job_no and  a.status_active=1 and a.is_deleted=0 $sql_cond $job_cond order by a.id ASC");

		foreach($job_wise_order_qty as $row){
			$job_wise_order_arr[$row[csf("job_no")]]['job_no']=$row[csf("job_no")];
			$job_wise_order_arr[$row[csf("job_no")]]['buyer_name']=$row[csf("buyer_name")];
			$job_wise_order_arr[$row[csf("job_no")]]['style_ref_no']=$row[csf("style_ref_no")];
			$job_wise_order_arr[$row[csf("job_no")]]['po_quantity']=$row[csf("po_quantity")];
			$job_wise_order_arr[$row[csf("job_no")]]['plan_cut']=$row[csf("plan_cut")];
		}

		
	//================================issue qty ===========================

		$item_desc_data=sql_select("SELECT a.id,a.issue_number,   b.prod_id, c.item_category_id, c.item_group_id, b.order_id,c.avg_rate_per_unit,c.item_description
		from inv_issue_master a, inv_transaction b, product_details_master c where   b.prod_id=c.id and  c.item_category_id in(22,57) and  a.entry_form=21 and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
			foreach($item_desc_data as $row){
				$item_desc_arr[$row[csf("order_id")]] .=$row[csf("item_description")].",";
			}

			// inv_receive_master
			
		$issue_print_data=sql_select("SELECT a.id,a.issue_number, b.id as bid, b.cons_quantity, b.prod_id,c.item_category_id, c.item_group_id, b.order_id,c.avg_rate_per_unit
			from inv_issue_master a, inv_transaction b, product_details_master c where   b.prod_id=c.id and  c.item_category_id=22 and  a.entry_form=21 and a.id=b.mst_id $issue_date_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
		foreach($issue_print_data as $row){

			
				$issue_print_qty_arr[$row[csf("order_id")]]=$row[csf("cons_quantity")];
				$issue_p_rate_arr[$row[csf("order_id")]]=($row[csf("cons_quantity")]*$row[csf("avg_rate_per_unit")])/$currency;
				$iss_print_issue_num_arr[$row[csf("order_id")]]=$row[csf("bid")];
		}

	

		$issue_qty=sql_select("SELECT a.id,a.issue_number,b.id as bid, c.item_description, c.item_category_id, c.item_group_id,b.item_return_qty, b.cons_quantity, b.cons_uom, b.order_id,c.avg_rate_per_unit from inv_issue_master a,inv_transaction b, product_details_master c where b.prod_id=c.id and b.transaction_type=2 and b.item_category=57 and a.id=b.mst_id  $issue_date_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
		foreach($issue_qty as $row){

			
				$issue_embl_qty_arr[$row[csf("order_id")]]=$row[csf("cons_quantity")];		
				$issue_e_rate_arr[$row[csf("order_id")]] +=($row[csf("cons_quantity")]*$row[csf("avg_rate_per_unit")])/$currency;			
				$issue_e_oder_id_arr[$row[csf("order_id")]]=$row[csf("prod_id")];
				$iss_eble_issue_num_arr[$row[csf("order_id")]]=$row[csf("bid")];
				//  $issue_num_arr[$row[csf("order_id")]]+=$row[csf("cons_quantity")];
				//  $issue_num_arr[$row[csf("order_id")]]+=$row[csf("cons_quantity")];
		}
		
	
		
//   print_r($issue_num_arr);
		//  echo "<br>";
		
	    								
 //   ================================ main query GMTS_ITEM_ID================================================= 

 $data_array=sql_select("SELECT  a.po_number, a.po_received_date, a.pub_shipment_date, a.shipment_date, a.po_quantity, a.unit_price, a.po_total_price,a.id,a.job_no_mst,c.item_number_id,b.buyer_name,c.po_break_down_id,b.company_name,c.embel_name,c.embel_type,d.emb_type,d.emb_name from wo_po_break_down a,wo_po_details_master b,pro_garments_production_mst c,wo_pre_cost_embe_cost_dtls d where a.JOB_NO_MST=b.job_no and a.JOB_NO_MST=d.job_no and  c.po_break_down_id=a.id and d.emb_name in (1,2) and c.embel_name in (1,2) and c.production_source=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sql_cond $date_cond2 order by a.id ASC");
 $po_number=0; $i=0; $m=0; $buyer_id=0; $z=0; $is=0;$emb_type=0;
 $buyer_wise_qty_id=array();
 
foreach($data_array as $row ){

			$order_wise_data_arr[$row[csf("id")]][$row[csf("emb_type")]]['id']=$row[csf("id")];
			$order_wise_data_arr[$row[csf("id")]][$row[csf("emb_type")]]['job_no']=$row[csf("job_no_mst")];
			$order_wise_data_arr[$row[csf("id")]][$row[csf("emb_type")]]['po_number']=$row[csf("po_number")];
			$order_wise_data_arr[$row[csf("id")]][$row[csf("emb_type")]]['emb_type']=$row[csf("emb_type")];
			$order_wise_data_arr[$row[csf("id")]][$row[csf("emb_type")]]['emb_name']=$row[csf("emb_name")];
			
			// $order_wise_data_arr[$row[csf("id")]]['item_number_id']=$row[csf("item_number_id")];
			$order_wise_data_arr[$row[csf("id")]][$row[csf("emb_type")]]['item_number_id'] =$garments_item[$row[csf("item_number_id")]];
				
			$buyer_wise_embl_type_arr[$row[csf("buyer_name")]][$row[csf("job_no_mst")]][$row[csf("emb_type")]]['emb_type']=$row[csf("emb_type")];
			$buyer_wise_embl_type_arr[$row[csf("buyer_name")]][$row[csf("job_no_mst")]][$row[csf("emb_type")]]['emb_name']=$row[csf("emb_name")];
			$buyer_wise_embl_type_arr[$row[csf("buyer_name")]][$row[csf("job_no_mst")]][$row[csf("emb_type")]]['order_id']=$row[csf("id")];
		
			
			$order_wise_data_arr[$row[csf("id")]][$row[csf("emb_type")]]['buyer_name'] =$row[csf("buyer_name")];
			if($row[csf("id")] !==$po_number ){
				
					if($m==0){					
						$orderid_arr .=$row[csf("id")];
						$m++;
					}else{
						$orderid_arr.=",".$row[csf("id")];
					}

					$batch_no=$batch[csf('buyer_name')];
					if (!in_array($batch_no,$buyer_wise_qty_id))
						{ $z++;
							 $buyer_wise_qty_id[]=$batch_no;							
								if($is==0){				
										$buyer_wise_qty_arr[$row[csf("buyer_name")]]['issue_p_num'] .=$iss_print_issue_num_arr[$row[csf("id")]];
										$buyer_wise_qty_arr[$row[csf("buyer_name")]]['issue_embl_num'] .=$iss_eble_issue_num_arr[$row[csf("id")]];					
										$is++;									
								}else{									
									$buyer_wise_qty_arr[$row[csf("buyer_name")]]['issue_p_num'] .=",".$iss_print_issue_num_arr[$row[csf("id")]];	
									$buyer_wise_qty_arr[$row[csf("buyer_name")]]['issue_embl_num'] .=",".$iss_eble_issue_num_arr[$row[csf("id")]];					
								}
							  $buyer_wise_qty_arr[$row[csf("buyer_name")]]['company_name']=$row[csf("company_name")];          
							  $buyer_wise_qty_arr[$row[csf("buyer_name")]]['id']=$row[csf("id")];
							
							  
							  $buyer_wise_qty_arr[$row[csf("buyer_name")]]['print_qty']+=$print_qty[$row[csf("id")]];
							  $buyer_wise_qty_arr[$row[csf("buyer_name")]]['embl_qty']+=$emb_qty[$row[csf("id")]];
							  $buyer_wise_qty_arr[$row[csf("buyer_name")]]['b_job_no'] =$row[csf("job_no_mst")];
							  $buyer_wise_qty_arr[$row[csf("buyer_name")]]['issue_print_qty'] +=$issue_print_qty_arr[$row[csf("id")]];
							  $buyer_wise_qty_arr[$row[csf("buyer_name")]]['issue_embl_qty'] +=$issue_embl_qty_arr[$row[csf("id")]];
							  $buyer_wise_qty_arr[$row[csf("buyer_name")]]['issue_p_val'] +=$issue_p_rate_arr[$row[csf("id")]];
						
							  $buyer_wise_qty_arr[$row[csf("buyer_name")]]['item_number_id'] =$row[csf("item_number_id")];
					
					
							  $buyer_wise_qty_arr[$row[csf("buyer_name")]]['issue_e_val'] +=$issue_e_rate_arr[$row[csf("id")]];
							  $buyer_wise_qty_arr[$row[csf("buyer_name")]]['issue_e_order_id'] .=$issue_e_oder_id_arr[$row[csf("id")]];
							  
	
							 
							  $po_number=$row[csf("id")];
							//   $emb_type=$row[csf("emb_type")];
							 
						}
						else
						{
							 $buyer_wise_qty_id=0;
						}			
				$b=1;
			}	
			$buyer_id=$row[csf("buyer_name")];
}

    //    print_r($print_rate_arr);
	//   echo $orderid_arr;
	foreach ($order_wise_data_arr as $id => $emb_data) 
	{
		foreach ($emb_data as $emb_data_id => $row) 
		{
			//echo $print_qty[$id]*$print_rate_arr[$row["job_no"]][$emb_data_id].'<br>';
			$buyer_wise_qty_arr[$row["buyer_name"]]['print_value'] +=($print_qty[$id]*$print_rate_arr[$row["job_no"]][$emb_data_id]);
			$buyer_wise_qty_arr[$row["buyer_name"]]['embr_value'] +=($emb_qty[$id]*$embl_rate_arr[$row["job_no"]][$emb_data_id]);
							  
		if($print_qty[$id] !=''){
			if($is==0){		
				$buyer_wise_qty_arr[$row["buyer_name"]]['order_id'] .=$row["id"];	
				 $buyer_wise_qty_arr[$row[csf("buyer_name")]]['job_list'] .="*".$row["job_no"]."*";
				$is++;									
				}else{									
					$buyer_wise_qty_arr[$row["buyer_name"]]['order_id'] .=",".$row["id"];
					
					$buyer_wise_qty_arr[$row["buyer_name"]]['job_list'] .=",*".$row["job_no"]."*";
				}
		}
		if($emb_qty[$id] !=''){
			if($is==0){		
				$buyer_wise_qty_arr[$row["buyer_name"]]['emb_order_id'] .=$row["id"];				
				$buyer_wise_qty_arr[$row[csf("buyer_name")]]['embl_job_list'] .="*".$row["job_no"]."*";
				$is++;			

				}else{									
					$buyer_wise_qty_arr[$row["buyer_name"]]['emb_order_id'] .=",".$row["id"];	
					$buyer_wise_qty_arr[$row["buyer_name"]]['embl_job_list'] .=",*".$row["job_no"]."*";					
				}
		}
	  }
	}

	//  echo "<pre>";
    //  print_r($buyer_wises_qty_arr);
		ob_start();
		?>	
	 	<fieldset style="width:1180px;"> 	
	 		<style type="text/css">
	 			h3{font-weight: bold;}
	 		</style>
	 		<div align="center">
				
	 			 <h3><?= $lib_company[$company_id];?> </h3> 
				 <p>Inhouse Print/Embroidary Production Report</p> 
	 			<h4>Date: <? echo change_date_format(str_replace("'", "", $txt_date_from));?> To <? echo change_date_format(str_replace("'", "", $txt_date_to));?></h4>
	 		</div>	
	 		<!-- ========================== table heading ========================== -->
	        <table width="1180" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" align="left"> 
	            <thead>
	               <tr>
		                <th width="30">SL</th>
		                <th width="80">Buyer Name</th>
		                <th width="100">Total Print Rcv./Prodution Qty</th>
		                <th width="80">Total Print Value</th>
		                <th width="60">Total Embr. Rcv./Prodution Qty</th>
		                <th width="100">Total Embr. Value</th>
		                <th width="100" title="Total Print Rcv. Qty + Embr. Rcv. Qty">Total Print Rcv. Qty + Embr. Rcv. Qty</th>
		                <th width="80" title="Total Print Rcv. Value + Embr. Rcv. Value">Total Print Rcv. Value + Embr. Rcv. Value</th>
		                <th width="80">Issue Cost Print</th>
		                <th width="80">Issue Cost Embr.</th>
						<th width="80" title="Total Issue Cost Print+Issue Cost Embr.">Total Issue Cost Print+Issue Cost Embr.</th>
		                <th width="80">Income Print</th>
		                <th width="80">Income Embr.</th>
		             	<th width="80" title="Total Income Print+Total Income Embr.">Total Income Print+Total Income Embr.</th>
					</tr>
	            </thead>  
	        </table> 
	        <!-- ========================== table body ========================== -->         
	        <div id="scroll_body" style="width:1200px;max-height:300px;overfllow-y:auto;">
	        	<table width="1180" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body" align="left"> 
		    		<tbody id="tbl_list_search" align="left">
		        		<?
		        		$i=1;
		        		foreach($buyer_wise_qty_arr as $buyer_id=>$row){
							
							$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ;
							
							?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
								<td width="30" align="left"><? echo $i;?></td>
								<td width="80" align="left" title="<?=$row['b_job_no'];?>"><? echo $lib_buyer[$buyer_id];?></td>
								<td width="100" align="left"><? echo number_format($row['print_qty'],0);?></td>
								<td width="80" align="left"><a href="##"onclick="openmypage_order('<?=$buyer_id?>','<?=$row['order_id'];?>','<?=$row['job_list'];?>','<?=$txt_date_from;?>','<?=$txt_date_to;?>','Issue_PrintPopup')"><? echo number_format($row['print_value'],0);?></a></td>
								<td width="60" align="center"><? echo $row['embl_qty'];?></td>
								<td width="100" align="left"><a href="##"onclick="openmypage_order('<?=$buyer_id?>','<?=$row['emb_order_id'];?>','<?=$row['embl_job_list'];?>','<?=$txt_date_from;?>','<?=$txt_date_to;?>','Issue_emblPopup')"><? echo number_format($row['embr_value'],0);?></a></td>
								<td width="100" align="left" title="<?=$row['print_qty']."+".$row['embl_qty'];?>"><? echo $row['print_qty']+$row['embl_qty'];?></td>
								<td width="80" align="left"><? echo number_format($row['print_value']+$row['embr_value'],2);?></td>
								<td width="80" align="left"><a href="##"onclick="openmypage_order('<?=$row['issue_p_num']?>','<?=$orderid_arr;?>','<?=$row['b_job_no'];?>','<?=$txt_date_from;?>','<?=$txt_date_to;?>','Issue_cost_PrintPopup')"><? echo number_format($row['issue_p_val'],0);?></a></td>
								<td width="80" align="left"><a href="##"onclick="openmypage_order('<?=$row['issue_embl_num']?>','<?=$orderid_arr;?>','<?=$row['b_job_no'];?>','<?=$txt_date_from;?>','<?=$txt_date_to;?>','Issue_cost_EmblPopup')"><?echo number_format($row['issue_e_val'],0)?></a></td>
								<td width="80" align="left" title="<?=$row['issue_p_val']."+".$row['issue_e_val'];?>"><? echo number_format($row['issue_p_val']+$row['issue_e_val'],0);?></td>
								<td width="80" align="left"><? echo number_format($row['print_value']-$row['issue_p_val'],0);?></td>
								<td width="80" align="left"><? echo number_format($row['embr_value']-$row['issue_e_val'],0);?></td>
								<td width="80" align="left"><? echo number_format($row['print_value']+$row['embr_value']-$row['issue_p_val']-$row['issue_e_val'],0);?></td>
								
							</tr>
							<?
							$i++;
							
						}			
		        		?>
		        	</tbody>
		        </table>
		    </div>
		    <!-- ========================== table footer ========================== -->
	        <table width="1180" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" align="left"> 
	            <tfoot>
	               <tr>
		                <th width="30"></th>
		                <th width="80"> Total</th>
		                <th width="100" id="s_print_qty"> </th>
		                <th width="80" id="s_print_val"></th>
		                <th width="60"  id="s_embl_qty"> </th>
		                <th width="100" id="s_embl_val"></th>
		                <th width="100" id="s_print_embl_qty"></th>
		                <th width="80" id="s_print_embl_val"></th>
		                <th width="80" id="s_issue_print_val"></th>
		                <th width="80" id="s_issue_embl_val"></th>
		                <th width="80" id="s_issue_print_embl_val"></th>
		                <th width="80" id="s_inc_print_val"></th>
		             	<th width="80" id="s_inc_embl_val"></th>
		                <th width="80" id="s_inc_print_embl_val"></th>
		              
					</tr>
	            </tfoot>  
	        </table> 
	    </fieldset><br>


		<fieldset style="width:1220px;"> 	
	 		<style type="text/css">
	 			h3{font-size: 20px;font-weight: bold;}
	 		</style>
	 		<div>
	 		
	 			<!-- <h3>Details</h3> -->
	 			<!-- <h3> Date : <? echo change_date_format(str_replace("'", "", $txt_date_from));?> To <? echo change_date_format(str_replace("'", "", $txt_date_to));?></h3> -->
	 		</div>	
	 		<!-- ========================== table heading ========================== -->
	        <table width="1210" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" align="left"> 
	            <thead>
	               <tr>
		                <th width="30">SL</th>
		                <th width="80">Order Number</th>
		                <th width="80">Buyer Name</th>
		                <th width="80">Job No</th>
		                <th width="100">Style Ref.</th>		               
		                <th width="100">Item</th>
		                <th width="80">Order Qty</th>
						<th width="80">Embel. type</th>
		                <th width="80">Plan Cut Qty</th>
		                <th width="80">Print Rcv. Qty</th>
		                <th width="80">Print Rcv Rate(pcs)</th>
		                <th width="80" title="Print Rcv. Qty*Print Rcv Rate(pcs)">$Value</th>
		             	<th width="80">Embr. Rcv. Qty</th>
		                <th width="80">Embr. Rcv Rate(pcs)</th>
		                <th width="80" title="Embr. Rcv. Qty*Embr. Rcv Rate(pcs)">$Value</th>		              
					</tr>
	            </thead>  
	        </table> 
	        <!-- ========================== table body ========================== -->         
	        <div id="scroll_body" style="width:1220px;max-height:300px;overfllow-y:auto;">
	        	<table width="1210" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body1" align="left"> 
		    		<tbody id="tbl_list_search1" align="center">
		        		<?
		        		$t=1;
		        	
		        		foreach ($order_wise_data_arr as $id => $emb_data){
						
								foreach ($emb_data as $emb_data_id => $row){
								
							$job_no=$job_wise_order_arr[$row['job_no']]['job_no'];
							$buyer_name=$job_wise_order_arr[$row['job_no']]['buyer_name'];
							$style_ref=$job_wise_order_arr[$row['job_no']]['style_ref_no'];
							$po_qty=$job_wise_order_arr[$row['job_no']]['po_quantity'];
							$plan_cut=$job_wise_order_arr[$row['job_no']]['plan_cut'];
							$print_rate=$print_rate_arr[$row['job_no']][$row['emb_type']];
							$embl_rate=$embl_rate_arr[$row['job_no']][$row['emb_type']];
					
					
								$bgcolors=($t%2==0)?"#E9F3FF":"#FFFFFF" ;
							if($print_qty[$id] !='' || $emb_qty[$id]!=''){
							

							?>
							<tr bgcolor="<? echo $bgcolors;?>" onClick="change_color('tr1_<? echo $t; ?>','<? echo $bgcolors;?>')" id="tr1_<? echo $t; ?>">
							<td width="30" align="left"><? echo $t;?></td>
							<td width="80" align="left"><? 	echo $row['po_number'];				
							?></td>
							<td width="80" align="left"><?  echo $lib_buyer[$buyer_name]?></td>
							<td width="80" align="left"><?  echo $job_no; ?></td>
							<td width="100" align="center"><?echo $style_ref;?></td>						
							<td width="100" align="left"><?  echo implode(",", array_unique(explode(",", chop($row['item_number_id'],","))));?></td>
							<td width="80" align="left"><? echo $po_qty;?></td>
							<?
							if($row['emb_name']==1){?>
							<td width="80" align="left"><? echo $emblishment_print_type_arr[$row['emb_type']]?></td>
								<?}else{?>
									<td width="80" align="left"><? echo $emblishment_embroy_type_arr[$row['emb_type']]?></td>						
								<?}?>
						
							
							<td width="80" align="left"><?  echo $plan_cut;?></td>
							<td width="80" align="left"><? echo $print_qty[$id];?></td>
							<td width="80" align="left"><? echo $print_rate;?></td>
							<td width="80" align="left" title="<?= $print_qty[$id]."*".$print_rate;?>"><? echo $print_qty[$id]*$print_rate;?></td>
							<td width="80" align="left"><?  echo $emb_qty[$id];?></td>
							<td width="80" align="left"><? echo $embl_rate;?></td>
							<td width="80" align="left" title="<?=$emb_qty[$id]."*".$embl_rate;?>"><? echo $emb_qty[$id]*$embl_rate;?></td>
						
	
						</tr>
						<?
						$t++;
							
						  }
					   	}
		        	}
		        		?>
		        	</tbody>
		        </table>
		    </div>
		    <!-- ========================== table footer ========================== -->
	        <table width="1090" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" align="left"> 
	            <tfoot>
	               <tr>
		                <th width="30"></th>
		                <th width="80"></th>
		                <th width="80"></th>
		                <th width="80"></th>
		                <th width="60"> </th>		              
		                <th width="100"></th>
		                <th width="80"></th>
		                <th width="80"></th>
		                <th width="80"></th>
		                <th width="80">Total</th>
		                <th width="80" id="print_val"></th>
		             	<th width="80"></th>
		                <th width="80"></th>
		                <th width="80" id="embl_val"></th>
		               
					</tr>
	            </tfoot>  
	        </table> 
	    </fieldset>
		<?
		unset($dataArray);
		unset($qtyArray);
		$html = ob_get_contents();
	    ob_clean();
	    foreach (glob("*.xls") as $filename) {
	    @unlink($filename);
	    }
	    //---------end------------//
	    $name=time();
	    $filename=$user_id."_".$name.".xls";
	    $create_new_doc = fopen($filename, 'w');	
	    $is_created = fwrite($create_new_doc, $html);
	    echo "$html####$filename"; 
	    exit();
	
	 
}




if ($action=='Issue_cost_PrintPopup')
{
	echo load_html_head_contents("Inhouse Print/Embroidary Production Income Report", "../../../", 1, 1,$unicode,'','');
	$po_break_down_id=$_REQUEST['po_break_down_id'];
	$item_id=$_REQUEST['item_id'];
	$company_name=str_replace("'","",$_REQUEST['company_name']);
	$order_id=str_replace("'","",$_REQUEST['order_id']);
	$job_id=str_replace("'","",$_REQUEST['job_id']);
	$issue_num=str_replace("'","",$_REQUEST['issue_num']);
	
	$iss_num=array_unique(explode(",", chop($issue_num,",")));
	
	$order_array=sql_select("select  a.po_number,a.id,a.job_no_mst,c.item_number_id from wo_po_break_down a,wo_po_details_master b,pro_garments_production_mst c where a.JOB_NO_MST=b.job_no and c.po_break_down_id=a.id and c.production_source=1 and a.status_active=1 and a.is_deleted=0  and a.id in ($order_id) order by a.id ASC");
foreach($order_array as $row){
	$order_arr[$row[csf("id")]] .=$garments_item[$row[csf("item_number_id")]].",";
}


//  echo "<pre>";
//   print_r($currency_data);



	$lib_po_number=return_library_array( "select id, po_number from  wo_po_break_down",'id','po_number');
	
	 $i=0;
	 $issueId='';
	foreach($iss_num as $id){if($i==0){$i++;}elseif($i==1){$issueId.=$id;$i++;}else{$issueId .=",".$id;}}
	
	$issue_print_data=sql_select("SELECT a.id,a.issue_number,a.issue_date, b.id, b.store_id, b.cons_uom, b.cons_quantity, b.machine_category, b.machine_id, b.prod_id, b.location_id, b.department_id, b.section_id, c.item_category_id, c.item_group_id, b.order_id,c.avg_rate_per_unit,c.company_id,c.item_description
			from inv_issue_master a, inv_transaction b, product_details_master c where   b.prod_id=c.id and b.id in($issueId)  and  c.item_category_id=22 and  a.entry_form=21 and a.id=b.mst_id and b.order_id in ($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
	 
	//var_dump($ex_fact_qty_arr);
	
	?>
 <div id="data_panel" align="center" style="width:100%">
         <script>
		 	function new_window()
			 {
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write(document.getElementById('details_reports').innerHTML);
				d.close();
			 }
         </script>
 	<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onClick="new_window()" />
 </div>
  
<div style="width:700px" align="center" id="details_reports"> 
  	<legend>Issue Cost Print-$ pop up</legend>
    <table id="tbl_id" class="rpt_table" width="" border="1" rules="all" >
    	<thead>
        	<tr>
            	<th width="100">Issue Id </th>
                <th width="100">Issue Date</th>
                <th width="100">Order</th>
                <th width="300">Item Name</th>
                <th width="100">Issue Qty</th>
                <th width="100">rate</th>
                <th width="100">Value</th>
            </tr>
        </thead>
			 <?
			 	foreach($issue_print_data as $row){
					$company_id=$row[csf("company_id")];
					$currency_data=sql_select("select id,company_id,currency,conversion_rate,marketing_rate,con_date from currency_conversion_rate where status_active=1 and is_deleted=0 and company_id=$company_id order by id asc ");
					$currency=$currency_data[0][csf("conversion_rate")];
					$rate=$row[csf("avg_rate_per_unit")]/$currency;
					$value=$row[csf("cons_quantity")]*$rate;
					 ?>
        <tr>
        	<td><? echo $row[csf("issue_number")]; ?></td>
            <td><? echo $row[csf("issue_date")];  ?></td>
            <td><? echo $lib_po_number[$row[csf("order_id")]];  ?></td>
			<td><?	echo implode(",",array_unique(explode(",", chop($row[csf("item_description")],",")))); ?></td>
            <td><?  echo $row[csf("cons_quantity")];  ?></td>
            <td><?  echo $rate;  ?></td>
            <td><? echo $value; ?></td>
        </tr>
       <?
	   $tot_qty+=$row[csf("cons_quantity")];
	   $tot_value+=$value;

		 }?>

		<tr>
        	<td></td>
            <td></td>
            <td></td>
            <td><b>Total Qty</b></td>
            <td><b><?  echo $tot_qty;  ?></b></td>
            <td><b>Total Value</b></td>
            <td><b><? echo $tot_value; ?></b></td>
        </tr>
    </table>
    
   
</div>    


<?
exit();

}// end if condition


if ($action=='Issue_cost_EmblPopup')
{
	echo load_html_head_contents("Inhouse Print/Embroidary Production Income Report", "../../../", 1, 1,$unicode,'','');
	$po_break_down_id=$_REQUEST['po_break_down_id'];
	$item_id=$_REQUEST['item_id'];
	$company_name=str_replace("'","",$_REQUEST['company_name']);
	$order_id=str_replace("'","",$_REQUEST['order_id']);
	$issue_num=str_replace("'","",$_REQUEST['issue_num']);
	$iss_num=array_unique(explode(",", chop($issue_num,",")));
	$date_from=str_replace("'","",$_REQUEST['date_from']);
	$date_to=str_replace("'","",$_REQUEST['date_to']);
	$i=0;
	 $issueId='';
	foreach($iss_num as $id){if($i==0){$i++;}elseif($i==1){$issueId.=$id;$i++;}else{$issueId .=",".$id;}}

	if($date_from !=='' && $date_to !==''){
		$date_cond="and a.issue_date between '$date_from' and '$date_to' ";
	}else{
	$date_cond="";
 }
	$issue_print_data=sql_select("select b.id,a.issue_number, c.item_description, c.item_category_id, c.item_group_id,b.item_return_qty, b.cons_quantity, b.cons_uom, b.order_id,c.avg_rate_per_unit,c.company_id,c.item_description,a.issue_date
	from inv_issue_master a,inv_transaction b, product_details_master c
	where b.prod_id=c.id  and b.transaction_type=2 and a.id=b.mst_id and b.item_category=57 and b.order_id in ($order_id)  $date_cond");
	
			$lib_po_number=return_library_array( "select id, po_number from  wo_po_break_down",'id','po_number');

			$order_array=sql_select("select  a.po_number,a.id,a.job_no_mst,c.item_number_id from wo_po_break_down a,wo_po_details_master b,pro_garments_production_mst c where a.JOB_NO_MST=b.job_no and c.production_source=1 and  c.po_break_down_id=a.id and a.status_active=1  and a.is_deleted=0 and a.id in ($order_id) order by a.id ASC");
			foreach($order_array as $row){
				$order_arr[$row[csf("id")]] .=$garments_item[$row[csf("item_number_id")]].",";
			}
	
	?>
 <div id="data_panel" align="center" style="width:100%">
         <script>
		 	function new_window()
			 {
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write(document.getElementById('details_reports').innerHTML);
				d.close();
			 }
         </script>
 	<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onClick="new_window()" />
 </div>
  
<div style="width:700px" align="center" id="details_reports"> 
  	<legend>Issue Cost Embl-$ pop up</legend>
    <table id="tbl_id" class="rpt_table" width="" border="1" rules="all" >
    	<thead>
        	<tr>
            	<th width="100">Issue Id </th>
                <th width="100">Issue Date</th>
                <th width="100">Order</th>
                <th width="300">Item Name</th>
                <th width="100">Issue Qty</th>
                <th width="100">rate</th>
                <th width="100">Value</th>
            </tr>
        </thead>
			 <?
			 	foreach($issue_print_data as $row){
					$company_id=$row[csf("company_id")];
					$currency_data=sql_select("select id,company_id,currency,conversion_rate,marketing_rate,con_date from currency_conversion_rate where status_active=1 and is_deleted=0 and company_id=$company_id order by id asc ");
					$currency=$currency_data[0][csf("conversion_rate")];
					$rate=$row[csf("avg_rate_per_unit")]/$currency;
					$value=$row[csf("cons_quantity")]*$rate;



					 ?>
        <tr>
        	<td><? echo $row[csf("issue_number")]; ?></td>
            <td><? echo $row[csf("issue_date")];  ?></td>
            <td><?  echo $lib_po_number[$row[csf("order_id")]];  ?></td>
            <td><?	echo implode(",",array_unique(explode(",", chop($row[csf("item_description")],","))));
 ?></td>
            <td><?  echo $row[csf("cons_quantity")];  ?></td>
            <td><?  echo $rate;  ?></td>
            <td><? echo $value; ?></td>
        </tr>
       <?
	     $tot_qty+=$row[csf("cons_quantity")];
		 $tot_value+=$value;
		 }?>
		 <tr>
        	<td></td>
            <td></td>
            <td></td>
            <td><b>Total Qty</b></td>
            <td><b><?  echo $tot_qty;  ?></b></td>
            <td><b>Total Value</b></td>
            <td><b><? echo $tot_value; ?></b></td>
        </tr>
    </table>
    
   
</div>    


<?
exit();

}// end if condition

if ($action=='Issue_PrintPopup')
{
			echo load_html_head_contents("Inhouse Print/Embroidary Production Income Report", "../../../", 1, 1,$unicode,'','');
			$po_break_down_id=$_REQUEST['po_break_down_id'];
			$item_id=$_REQUEST['item_id'];
			$company_name=str_replace("'","",$_REQUEST['company_name']);
			$order_id=str_replace("'","",$_REQUEST['order_id']);
			$date_from=str_replace("'","",$_REQUEST['date_from']);
			$date_to=str_replace("'","",$_REQUEST['date_to']);
			$job_id=str_replace("*","'",$_REQUEST['job_id']);
			$buyer_id=str_replace("'","",$_REQUEST['issue_num']);
			$lib_po_number=return_library_array( "select id, po_number from  wo_po_break_down",'id','po_number');
			$lib_buyer=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
			// $job_id=str_replace("'","",$_REQUEST['job_id']);
			// echo $job_id;
			$order_data=array_unique(explode(",", chop($order_id,",")));
			$job_data=array_unique(explode(",", chop($job_id,",")));
			// echo $rate;die;
			$i=0;
			$m=0;
			$orderid='';
			$job_list='';
			
			if($date_from !=='' && $date_to !==''){
				$date_cond="and production_date between '$date_from' and '$date_to' ";
				$date_cond2="and c.production_date between '$date_from' and '$date_to' ";
			}else{	$date_cond="";$date_cond2=""; }

			foreach($order_data as $id){if($i==0){$i++;}elseif($i==1){$orderid.=$id;$i++;}else{$orderid .=",".$id;}}

			foreach($job_data as $id){if($m==0){$m++;}elseif($m==1){$job_list.=$id;$m++;}else{$job_list .=",".$id;}}
			
			$order_dataarr=array_unique(explode(",", chop($orderid,",")));
			$order_data_arr=array();
			//echo $job_list;
			$print_qty_data=sql_select("SELECT id, po_break_down_id, item_number_id, country_id, production_date, production_quantity,  embel_name from pro_garments_production_mst where   embel_name='1' and production_type='3' and production_source=1 and status_active=1 and is_deleted=0 and po_break_down_id in ($orderid) $date_cond order by id");

			foreach($print_qty_data as $row){
				// $order_data_arr[]=$row[csf("po_break_down_id")];
				$print_qty[$row[csf("po_break_down_id")]] +=$row[csf("production_quantity")];
			}

		// print_r($order_dataarr);

			$rate_data=sql_select("select a.id,a.emb_name,a.job_no,a.cons_dzn_gmts, a.rate, a.amount,b.costing_per,a.emb_type from wo_pre_cost_embe_cost_dtls a,wo_pre_cost_mst b where  emb_name=1  and a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and a.job_no in ($job_list) order by a.id ");
			$rate_cal=1;
			$print_rate_arr=array();
			foreach($rate_data as $row){
							if($row[csf("costing_per")]=1){//For 1 Dzn
								$rate_cal=12;
							}elseif($row[csf("costing_per")]=2){//For 1 Pcs
								$rate_cal=1;
							}elseif($row[csf("costing_per")]=3){//For 2 Dzn
								$rate_cal=24;
							}elseif($row[csf("costing_per")]=4){//For 3 Dzn
								$rate_cal=36;
							}elseif($row[csf("costing_per")]=5){//For 4 Dzn
								$rate_cal=48;
							}
					$print_rate_arr[$row[csf("job_no")]][$row[csf("emb_type")]]=$row[csf("rate")]/$rate_cal;
			}


		// echo "<pre>";
		//  print_r($print_rate_arr);

		//  print_r(array_unique($order_data_arr));
			
			//var_dump($ex_fact_qty_arr);
			


			$data_array=sql_select("select a.is_confirmed, a.po_number, a.po_received_date, a.pub_shipment_date, a.shipment_date, a.po_quantity, a.unit_price, a.po_total_price,a.id,a.job_no_mst,c.item_number_id,b.buyer_name,c.po_break_down_id,b.company_name,c.embel_name ,d.emb_type   from wo_po_break_down a,wo_po_details_master b,pro_garments_production_mst  c,wo_pre_cost_embe_cost_dtls d
			where a.JOB_NO_MST=b.job_no and b.job_no=d.job_no and emb_name=1 and  c.production_source=1 and c.po_break_down_id=a.id and a.status_active=1 and a.is_deleted=0 and a.JOB_NO_MST in ($job_list) $date_cond2 order by a.id ASC");
		foreach($data_array as $row){
			$order_wise_data_arr[$row[csf("id")]][$row[csf("emb_type")]]['id']=$row[csf("id")];
			$order_wise_data_arr[$row[csf("id")]][$row[csf("emb_type")]]['job_no']=$row[csf("job_no_mst")];
			$order_wise_data_arr[$row[csf("id")]][$row[csf("emb_type")]]['type']=$row[csf("emb_type")];
			$order_wise_data_arr[$row[csf("id")]][$row[csf("emb_type")]]['po_number']=$row[csf("po_number")];
		}
		// print_r($order_wise_data_arr);
			?>
		<div id="data_panel" align="center" style="width:100%">
				<script>
					function new_window()
					{
						var w = window.open("Surprise", "#");
						var d = w.document.open();
						d.write(document.getElementById('details_reports').innerHTML);
						d.close();
					}
				</script>
			<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onClick="new_window()" />
		</div>
		
		<div style="width:600px" align="center" id="details_reports"> 
			<legend>Issue Cost Print-$ pop up</legend>
			<table id="tbl_id" class="rpt_table" width="" border="1" rules="all" >
				<thead>
					<tr>
						<th width="20" align="center">SL </th>
						<th width="100">Buyer </th>
						<th width="100">Order </th>
						<th width="100">Embrodary Type </th>
						<th width="100">Production Qty</th>
						<th width="100">rate</th>
						<th width="300">Amount</th>                
					</tr>
				</thead>
					<?
					$i=1;
						foreach($order_wise_data_arr as $emb_id =>$emb_data){
							foreach($emb_data as $row){

							$amount=$print_qty[$row['id']]*$print_rate_arr[$row['job_no']][$row['type']];
							if($print_qty[$row['id']]!=''){
							?>
				<tr>
					<td><? echo  $i; ?></td>
					<td><? echo $lib_buyer[$buyer_id]; ?></td>
					<td><? echo $row['po_number']; ?></td>
					<td><? echo $emblishment_print_type_arr[$row['type']]; ?></td>
					<td><? echo $print_qty[$row['id']];  ?></td>
					<td><?  echo $print_rate_arr[$row['job_no']][$row['type']];  ?></td>          
					<td><?  echo  $amount;  ?></td>            
				</tr>
			<?
			$tot_qty +=$print_qty[$row['id']];
			$tot_amount +=$amount;
			$i++;	}
					}
				}?>
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td><b>Total Qty</b></td>
					<td><b><? echo $tot_qty;  ?></b></td>
					<td><b>Total Amount</b></td>          
					<td><b><?  echo  $tot_amount;  ?></b></td>            
				</tr>
			</table>
			

			
		
		</div>    


		<?
		exit();

}

if ($action=='Issue_emblPopup')
{
	echo load_html_head_contents("Inhouse Print/Embroidary Production Income Report", "../../../", 1, 1,$unicode,'','');
			$po_break_down_id=$_REQUEST['po_break_down_id'];
			$item_id=$_REQUEST['item_id'];
			$company_name=str_replace("'","",$_REQUEST['company_name']);
			$order_id=str_replace("'","",$_REQUEST['order_id']);
			$date_from=str_replace("'","",$_REQUEST['date_from']);
			$date_to=str_replace("'","",$_REQUEST['date_to']);
			$job_id=str_replace("*","'",$_REQUEST['job_id']);
			$buyer_id=str_replace("'","",$_REQUEST['issue_num']);
			$lib_po_number=return_library_array( "select id, po_number from  wo_po_break_down",'id','po_number');
			$lib_buyer=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
			// $job_id=str_replace("'","",$_REQUEST['job_id']);
			// echo $job_id;
			$order_data=array_unique(explode(",", chop($order_id,",")));
			$job_data=array_unique(explode(",", chop($job_id,",")));
			// echo $rate;die;
			$i=0;
			$m=0;
			$orderid='';
			$job_list='';
			
			if($date_from !=='' && $date_to !==''){
				$date_cond="and production_date between '$date_from' and '$date_to' ";
				$date_cond2="and c.production_date between '$date_from' and '$date_to' ";
			}else{	$date_cond="";$date_cond2=""; }

			foreach($order_data as $id){if($i==0){$i++;}elseif($i==1){$orderid.=$id;$i++;}else{$orderid .=",".$id;}}

			foreach($job_data as $id){if($m==0){$m++;}elseif($m==1){$job_list.=$id;$m++;}else{$job_list .=",".$id;}}
			
			$order_dataarr=array_unique(explode(",", chop($orderid,",")));
			$order_data_arr=array();
			//echo $job_list;
			$print_qty_data=sql_select("SELECT id, po_break_down_id, item_number_id, country_id, production_date, production_quantity,  
			embel_name from pro_garments_production_mst where   embel_name='2' and production_type='3' and production_source=1 and status_active=1 
			and is_deleted=0 and po_break_down_id in ($orderid) $date_cond order by id");

			foreach($print_qty_data as $row){
				// $order_data_arr[]=$row[csf("po_break_down_id")];
				$print_qty[$row[csf("po_break_down_id")]] +=$row[csf("production_quantity")];
			}

		// print_r($order_dataarr);

			$rate_data=sql_select("select a.id,a.emb_name,a.job_no,a.cons_dzn_gmts, a.rate, a.amount,b.costing_per,a.emb_type from wo_pre_cost_embe_cost_dtls a,wo_pre_cost_mst b 
			where  emb_name=2  and a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and a.job_no in ($job_list) order by a.id ");
			$rate_cal=1;
			$print_rate_arr=array();
			foreach($rate_data as $row){
							if($row[csf("costing_per")]=1){//For 1 Dzn
								$rate_cal=12;
							}elseif($row[csf("costing_per")]=2){//For 1 Pcs
								$rate_cal=1;
							}elseif($row[csf("costing_per")]=3){//For 2 Dzn
								$rate_cal=24;
							}elseif($row[csf("costing_per")]=4){//For 3 Dzn
								$rate_cal=36;
							}elseif($row[csf("costing_per")]=5){//For 4 Dzn
								$rate_cal=48;
							}
					$print_rate_arr[$row[csf("job_no")]][$row[csf("emb_type")]]=$row[csf("rate")]/$rate_cal;
			}


		// echo "<pre>";
		//  print_r($print_rate_arr);

		//  print_r(array_unique($order_data_arr));
			
			//var_dump($ex_fact_qty_arr);
			


			$data_array=sql_select("select a.is_confirmed, a.po_number, a.po_received_date, a.pub_shipment_date, a.shipment_date, a.po_quantity, 
			a.unit_price, a.po_total_price,a.id,a.job_no_mst,c.item_number_id,b.buyer_name,c.po_break_down_id,b.company_name,c.embel_name ,d.emb_type 
			from wo_po_break_down a,wo_po_details_master b,pro_garments_production_mst  c,wo_pre_cost_embe_cost_dtls d
			where a.JOB_NO_MST=b.job_no and b.job_no=d.job_no and emb_name=2 and  c.production_source=1 and c.po_break_down_id=a.id 
			and a.status_active=1 and a.is_deleted=0 and a.JOB_NO_MST in ($job_list) $date_cond2 order by a.id ASC");
		foreach($data_array as $row){
			$order_wise_data_arr[$row[csf("id")]][$row[csf("emb_type")]]['id']=$row[csf("id")];
			$order_wise_data_arr[$row[csf("id")]][$row[csf("emb_type")]]['job_no']=$row[csf("job_no_mst")];
			$order_wise_data_arr[$row[csf("id")]][$row[csf("emb_type")]]['type']=$row[csf("emb_type")];
			$order_wise_data_arr[$row[csf("id")]][$row[csf("emb_type")]]['po_number']=$row[csf("po_number")];
		}
		// print_r($order_wise_data_arr);
			?>
		<div id="data_panel" align="center" style="width:100%">
				<script>
					function new_window()
					{
						var w = window.open("Surprise", "#");
						var d = w.document.open();
						d.write(document.getElementById('details_reports').innerHTML);
						d.close();
					}
				</script>
			<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onClick="new_window()" />
		</div>
		
		<div style="width:600px" align="center" id="details_reports"> 
			<legend>Issue Cost Print-$ pop up</legend>
			<table id="tbl_id" class="rpt_table" width="" border="1" rules="all" >
				<thead>
					<tr>
						<th width="20" align="center">SL </th>
						<th width="100">Buyer </th>
						<th width="100">Order </th>
						<th width="100">Embrodary Type </th>
						<th width="100">Production Qty</th>
						<th width="100">rate</th>
						<th width="300">Amount</th>                
					</tr>
				</thead>
					<?
					$i=1;
						foreach($order_wise_data_arr as $emb_id =>$emb_data){
							foreach($emb_data as $row){

							$amount=$print_qty[$row['id']]*$print_rate_arr[$row['job_no']][$row['type']];
							if($print_qty[$row['id']]!=''){
							?>
				<tr>
					<td><? echo  $i; ?></td>
					<td><? echo $lib_buyer[$buyer_id]; ?></td>
					<td><? echo $row['po_number']; ?></td>
					<td><? echo $emblishment_embroy_type_arr[$row['type']]; ?></td>
					<td><? echo $print_qty[$row['id']];  ?></td>
					<td><?  echo $print_rate_arr[$row['job_no']][$row['type']];  ?></td>          
					<td><?  echo  $amount;  ?></td>            
				</tr>
			<?
			$tot_qty +=$print_qty[$row['id']];
			$tot_amount +=$amount;
			$i++;	}
					}
				}?>
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td><b>Total Qty</b></td>
					<td><b><? echo $tot_qty;  ?></b></td>
					<td><b>Total Amount</b></td>          
					<td><b><?  echo  $tot_amount;  ?></b></td>            
				</tr>
			</table>
			

			
		
		</div>    


		<?
		exit();

}
?>