<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if ($action=="load_drop_down_store")
{
	//$data=explode('_',$data);
	echo create_drop_down( "cbo_store_name", 120, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id  and a.status_active=1 and a.is_deleted=0 and a.company_id=$data and  b.category_type=2 order by a.store_name","id,store_name", 1, "--Select Store--", 1, "",0 );
	exit();
}


if ($action=="load_drop_down_buyer")
{
	
	$party="1,3,21,90";
	echo create_drop_down( "cbo_buyer_id", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in ($party)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "load_drop_down( 'requires/color_wise_finish_fabric_stock_report_controller', this.value, 'load_drop_down_season_buyer', 'season_td');","" );
	
	exit();
}


if ($action=="load_drop_down_season_buyer")
{
	echo create_drop_down( "cbo_season_id", 100, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-- Select Season--", "", "" );
	exit();
}

if ($action=="item_account_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	list($cbo_company_name,$buyer_id)=explode('__',$data);


?>	
    <script>

	
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
		
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
			toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( selectID, selected_id ) == -1 ) {
				selected_id.push( selectID );
				selected_name.push( selectDESC );
				selected_no.push( str );				
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
			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name ); 
			$('#txt_selected_no').val( num );
		}
		  
	</script>
   
	<div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="600" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
                <tr>                	 
                    <th width="100">Product Id</th>
                    <th width="200">Item Description</th>
                    <th width="100">Gsm</th>
                    <th width="100">Dia</th>
                    <th><input type="reset" id="" value="Reset" style="width:80px;" class="formbutton" /></th>
                </tr>
            </thead>
            <tbody>
                <tr align="center">
                    <td align="center"><input type="text" style="width:70px" class="text_boxes"  name="txt_prod_id" id="txt_prod_id" /></td>
                    <td align="center"><input type="text" style="width:160px" class="text_boxes"  name="txt_item_description" id="txt_item_description" /></td>
                    <td align="center"><input type="text" style="width:70px" class="text_boxes"  name="txt_gsm" id="txt_gsm" /></td>
                    <td align="center"><input type="text" style="width:70px" class="text_boxes"  name="txt_dia" id="txt_dia" /></td> 
                    <td align="center">
                    	<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view( document.getElementById('txt_prod_id').value+'_'+document.getElementById('txt_item_description').value+'_'+document.getElementById('txt_gsm').value+'_'+document.getElementById('txt_dia').value+'_'+<? echo $cbo_company_name; ?>+'_'+<? echo $buyer_id; ?>, 'create_item_search_list_view', 'search_div', 'color_wise_finish_fabric_stock_report_controller', '');" style="width:80px;" />				
                    </td>
                </tr>
            </tbody>
        </table>    
    <div align="center" valign="top" style="margin-top:5px" id="search_div"> </div> 
    </form>	

    </div> 
    
     
 <?
 
}


if ($action=="create_item_search_list_view")
{
	
	$ex_data=explode("_",$data);
	$txt_prod_id=str_replace("'","",$ex_data[0]);
	$txt_item_description=str_replace("'","",$ex_data[1]);
	$txt_gsm=str_replace("'","",$ex_data[2]);
	$txt_dia=str_replace("'","",$ex_data[3]);
	$cbo_company_name=str_replace("'","",$ex_data[4]);
	$buyer_id=str_replace("'","",$ex_data[5]);



	
	$sql_cond_all="";

	if($txt_prod_id!="") $sql_cond_all=" and id=$txt_prod_id";
	if($txt_item_description!="") $sql_cond_all.=" and item_description like '%$txt_item_description'";
	if($txt_gsm!="") $sql_cond_all.=" and gsm='$txt_gsm'";
	if($txt_dia!="") $sql_cond_all.=" and dia_width='$txt_dia'";
	if($cbo_company_name!=0) $sql_cond_all.=" and company_id=$cbo_company_name";
	//if($cbo_item_category_id!=0) $sql_cond_all.=" and item_category_id=$cbo_item_category_id";
	
	
	$color_arr = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	
	$sql="SELECT id,item_description,color,gsm,dia_width,supplier_id from  product_details_master where status_active=1 and is_deleted=0 $sql_cond_all";
	 //echo $sql;
	$arr=array(1=>$color_arr,4=>$supplierArr);
	echo  create_list_view("list_view", "Item Description,Color,Gsm,Dia,Supplier,Product ID", "150,120,70,70,130","680","300",0, $sql , "js_set_value", "id,item_description", "", 1, "0,color,0,0,supplier_id,0", $arr , "item_description,color,gsm,dia_width,supplier_id,id", "",'setFilterGrid("list_view",-1);','0,0,0,0,0,0','',1) ;
	
	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";
	?>
    <script language="javascript" type="text/javascript">
	
	</script>  
    <?
	
	exit();
}

if ($action=="receive_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	
	list($batch,$gsm,$dia,$color,$prod_id,$form_date,$to_date,$store_name,$company,$po_breakdown_id)=explode('__',$data);
	
	if($store_name !=0){$sqlCon = " and a.store_id =$store_name";}
	if($batch){$sqlCon .= " and b.batch_id ='$batch'";}
	if ($company!=0) $sqlCon =" and a.company_id='$company'";
	if ($po_breakdown_id!=0) $sqlCon =" and c.po_breakdown_id='$po_breakdown_id'";

	
	$sql="select a.id,a.recv_number,a.receive_basis,a.receive_date,a.insert_date, sum( case when c.quantity> 0 then c.quantity else b.receive_qnty end ) as receive_qnty from inv_receive_master a, pro_finish_fabric_rcv_dtls b left join order_wise_pro_details c on b.trans_id = c.trans_id and c.entry_form=37 	where a.id=b.mst_id and  b.prod_id='$prod_id' and a.item_category=2 and b.color_id=$color  and a.receive_date between '$form_date' and '$to_date' and a.entry_form =37 and a.is_deleted=0 and b.is_deleted=0 and a.status_active=1  and b.status_active=1 and b.batch_id='$batch' $sqlCon group by a.id,a.recv_number,a.receive_basis,a.receive_date,a.insert_date";
	
	$arr=array(1=>$receive_basis_arr);
	echo  create_list_view("list_view", "Receive ID,Receive Basis,Receive Date,Insetr Date and Time,Receive Qty", "150,120,70,130","680","300",0, $sql , "js_set_value", "id", "", 1, "0,receive_basis,0,0,0", $arr , "recv_number,receive_basis,receive_date,insert_date,receive_qnty", "",'setFilterGrid("list_view",-1);','0,0,3,0,2','',0) ;
	
	exit();
}


if($action=="generate_report")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$cbo_company_name =str_replace("'","",$cbo_company_name);
	$cbo_buyer_id =str_replace("'","",$cbo_buyer_id);
	$cbo_season_id =str_replace("'","",$cbo_season_id);
	$txt_product_id =str_replace("'","",$txt_product_id);
	$txt_product_id_des = trim(str_replace("'","",$txt_product_id_des));
	$txt_gsm = trim(str_replace("'","",$txt_gsm));
	$txt_color = trim(str_replace("'","",$txt_color));
	$cbo_sample_type =str_replace("'","",$cbo_sample_type);
	$txt_booking = trim(str_replace("'","",$txt_booking));
	$txt_date_from =str_replace("'","",$txt_date_from);
	$txt_date_to =str_replace("'","",$txt_date_to);
	$cbo_store_name =str_replace("'","",$cbo_store_name);
	$cbo_fabric_source =str_replace("'","",$cbo_fabric_source);
	$cbo_year =str_replace("'","",$cbo_year);
	$txt_job_prefix =str_replace("'","",$txt_job_prefix);



	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name"); 
	
	$yarn_count_arr=return_library_array("select id,yarn_count from lib_yarn_count where status_active=1 and is_deleted=0","id","yarn_count");
	
	$buyer_arr = return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$season_arr = return_library_array( "select id, season_name from lib_buyer_season",'id','season_name');
	
	if($db_type==0)
	{
		$select_from_date=change_date_format($txt_date_from,'yyyy-mm-dd');
		$select_to_date=change_date_format($txt_date_to,'yyyy-mm-dd');
	}
	else if($db_type==2) 
	{
		$select_from_date=change_date_format($txt_date_from,'','',1);
		$select_to_date=change_date_format($txt_date_to,'','',1);
	}
	else 
	{
		$select_from_date="";
		$select_to_date="";
	}

	$date_cond="";
	if($txt_date_from!="" && $txt_date_to!="")
	{
		$date_cond   = " and b.transaction_date <= '$select_to_date'";
	}
	if ($cbo_store_name!=0) $store_cond =" and b.store_id =$cbo_store_name ";


	if($cbo_buyer_id==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and g.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and g.buyer_id=$cbo_buyer_id";
	}
	

	if($txt_job_prefix!="")
	{
		$jobCond="and a.job_no_prefix_num=$txt_job_prefix";
	}
	else
	{
		$jobCond="";
	}
	if($db_type==0)
	{
		if($cbo_year!=0) $year_cond="and year(b.insert_date)='$cbo_year'"; else $year_cond="";
	}
	else if($db_type==2)
	{
		if($cbo_year!=0) $year_cond="and to_char(b.insert_date,'YYYY')='$cbo_year'";  else $year_cond="";
	}


	$sql_cond="";

	if ($cbo_store_name!=0) $sql_cond .=" and b.store_id =$cbo_store_name ";
	if ($txt_product_id_des) $sql_cond .=" and f.product_name_details like('%".$txt_product_id_des."%')";
	if ($txt_gsm) $sql_cond .=" and f.gsm like '%".$txt_gsm."%'";
	if($txt_booking != "") $sql_cond .= " and g.booking_no='".$txt_booking."'";
	if($txt_color != "") $sql_cond .= " and h.color_name='".$txt_color."'";
	if($cbo_season_id) $season_cond = " and a.season_buyer_wise='".$cbo_season_id."'";
	if ($cbo_fabric_source!=0) $sql_cond .=" and g.fabric_source =$cbo_fabric_source ";


	if($txt_job_prefix!="")
	{
		$jobInfoSqls=sql_select("select b.id,b.job_no_mst from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$cbo_company_name $year_cond $jobCond");
		$jobPoids="";
		foreach($jobInfoSqls as $rowData)
		{
			$jobPoids.=$rowData[csf("id")].",";
		}
		$jobPoids=chop($jobPoids,",");
		/*if($jobPoids!="")
		{*/
			$jobSearchCond="and d.po_breakdown_id in($jobPoids)";
		//}
	
	}

	
	if($report_type ==1)//Show Button
	{
		$trans_sql1 = "SELECT b.transaction_type, b.id, e.booking_no, e.booking_no_id, b.transaction_date, b.prod_id, f.detarmination_id, f.gsm, f.dia_width, f.color as color_id,  h.color_name, b.cons_uom, d.po_breakdown_id, c.po_number, g.buyer_id, a.style_ref_no, a.season_buyer_wise, b.pi_wo_batch_no, e.batch_no, sum(d.quantity) as quantity
		from  inv_transaction b, order_wise_pro_details d, pro_batch_create_mst e, product_details_master f, wo_booking_mst g, wo_po_break_down c, wo_po_details_master a, lib_color h
		where g.company_id =$cbo_company_name and b.transaction_type in (1,2,3,4,5,6) and b.item_category=2  and b.status_active =1 and b.id = d.trans_id  and d.entry_form in (37,52,14,18,46) and d.po_breakdown_id <>0 and b.pi_wo_batch_no=e.id and e.booking_no =g.booking_no and d.po_breakdown_id=c.id and c.job_id=a.id and b.prod_id = f.id and f.color=h.id $sql_cond $buyer_id_cond $season_cond $date_cond $jobSearchCond 
		group by  b.transaction_type, b.id,e.booking_no,e.booking_no_id, b.transaction_date, b.prod_id, f.detarmination_id, f.gsm, f.dia_width, f.color, h.color_name, b.cons_uom, d.po_breakdown_id, c.po_number, g.buyer_id, a.style_ref_no, a.season_buyer_wise, b.pi_wo_batch_no,  e.batch_no";

		if($cbo_season_id==0)
		{
			$concate = " union all ";
			$trans_sql2 = " SELECT b.transaction_type, b.id, e.booking_no, e.booking_no_id, b.transaction_date, b.prod_id, f.detarmination_id, f.gsm, f.dia_width, f.color as color_id,  h.color_name, b.cons_uom, 0 as po_breakdown_id, null as po_number, g.buyer_id, null as style_ref_no, null as season_buyer_wise, b.pi_wo_batch_no, e.batch_no, sum(b.cons_quantity) as quantity
			from  inv_transaction b, pro_batch_create_mst e, product_details_master f, wo_non_ord_samp_booking_mst g, lib_color h
			where g.company_id =$cbo_company_name  and b.transaction_type in (1,2,3,4,5,6) and b.item_category=2  and b.status_active =1  and b.pi_wo_batch_no=e.id and e.booking_no =g.booking_no and b.prod_id = f.id and f.color=h.id $sql_cond $buyer_id_cond $date_cond 
			group by  b.transaction_type, b.id,e.booking_no,e.booking_no_id, b.transaction_date, b.prod_id, f.detarmination_id, f.gsm, f.dia_width, f.color,  h.color_name, b.cons_uom, g.buyer_id, b.pi_wo_batch_no,  e.batch_no";
		}
		else
		{
			$trans_sql2 ="";
		}
		

		if($cbo_sample_type == 1)
		{
			$trans_sql = $trans_sql1;
		}
		else if($cbo_sample_type == 2)
		{
			$trans_sql = $trans_sql2;
		}
		else{
			if($txt_job_prefix!="")
			{
				$trans_sql = $trans_sql1;
			}
			else
			{
				$trans_sql = $trans_sql1.$concate.$trans_sql2;
			}
		}

		//echo $trans_sql;

		$trans_data = sql_select($trans_sql);

		foreach ($trans_data as  $val)
		{
			$po_id_arr[$val[csf('po_breakdown_id')]] = $val[csf('po_breakdown_id')];
			$date_frm=date('Y-m-d',strtotime($txt_date_from));
			$transaction_date=date('Y-m-d',strtotime($val[csf('transaction_date')]));

			$dia_width = $val[csf("dia_width")]*1;

			$ref_str="";
			$ref_str = $val[csf("prod_id")]."*".$val[csf("pi_wo_batch_no")]."*".$val[csf("po_breakdown_id")];
			if($transaction_date >= $date_frm)
			{
				$data_array[$ref_str][$val[csf("transaction_type")]]["qnty"] += $val[csf("quantity")];
			}
			else
			{
				$data_array[$ref_str][$val[csf("transaction_type")]]["opening"] += $val[csf("quantity")];
			}

			$data_array[$ref_str]["detarmination_id"] = $val[csf("detarmination_id")];
			$data_array[$ref_str]["gsm"] = $val[csf("gsm")];
			$data_array[$ref_str]["uom"] = $val[csf("cons_uom")];
			$data_array[$ref_str]["batch_id"] = $val[csf("pi_wo_batch_no")];
			$data_array[$ref_str]["width"] =$dia_width;
			$data_array[$ref_str]["po_number"] =$val[csf("po_number")];
			$data_array[$ref_str]["po_breakdown_id"] =$val[csf("po_breakdown_id")];
			$data_array[$ref_str]["buyer_id"] =$val[csf("buyer_id")];
			$data_array[$ref_str]["style_ref_no"] =$val[csf("style_ref_no")];
			$data_array[$ref_str]["color_id"] =$val[csf("color_id")];
			$data_array[$ref_str]["color_name"] =$val[csf("color_name")];
			$data_array[$ref_str]["batch_no"] =$val[csf("batch_no")];
			$data_array[$ref_str]["booking_no"] =$val[csf("booking_no")];
			$data_array[$ref_str]["uom"] =$val[csf("cons_uom")];
			$data_array[$ref_str]["season_buyer_wise"] =$val[csf("season_buyer_wise")];
		}
		unset($trans_data);
		/*echo "<pre>";
		print_r($data_array);die;*/


		$po_id_arr = array_filter($po_id_arr);
		if(!empty($po_id_arr))
		{
			$po_id_arr = array_filter($po_id_arr);
			if($db_type==2 && count($po_id_arr)>999)
			{
				$po_id_arr_arr_chunk=array_chunk($po_id_arr,999) ;
				foreach($po_id_arr_arr_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$poidCond.="  id in($chunk_arr_value) or ";
				}

				$all_pi_id_cond.="  (".chop($poidCond,'or ').")";
			}
			else
			{
				$all_pi_id_cond="  id in(".implode(",",$po_id_arr).")";
			}
			$jobInfoSql=sql_select("select id,job_no_mst from WO_PO_BREAK_DOWN where  $all_pi_id_cond");
			foreach($jobInfoSql as $rows)
			{
				$jobNoArr[$rows[csf("id")]]["job_no_mst"]=$rows[csf("job_no_mst")];
			}
		}
		/*echo "<pre>";
		print_r($jobNoArr);
		echo "</pre>";*/
		//echo $all_pi_id_cond;
		
		$i=1;
		ob_start();	
		if($report_type ==1)
		{ 
		?>
		<div> 
			<table width="2310" border="1" cellpadding="2" cellspacing="0" class="" id="table_header_1" > 
				<thead>
					<tr class="form_caption" style="border:none;">
						<td colspan="25" align="center" style="border:none;font-size:16px; font-weight:bold" > <? echo $report_title; ?></td> 
					</tr>
					<tr style="border:none;">
						<td colspan="25" align="center" style="border:none; font-size:14px;">
						   <b>Company Name : <? echo $companyArr[$cbo_company_name]; ?></b>                               
						</td>
					</tr>
					<tr style="border:none;">
						<td colspan="25" align="center" style="border:none;font-size:12px; font-weight:bold">
							<? if($from_date!="" || $to_date!="")echo "From : ".change_date_format($from_date,'dd-mm-yyyy')." To : ".change_date_format($to_date,'dd-mm-yyyy')."" ;?>
						</td>
					</tr>
				</thead>
			</table>
			<table width="2310" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all"> 
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="100">Buyer</th>
                        <th width="80">Season</th>
                        <th width="120">Booking No.</th>
                        <th width="80">Order No</th>
                        <th width="100">Job No</th>
                        <th width="100">Style</th>
                        <th width="100">Color</th>
                        <th width="80">Batch No</th>
                        <th width="80">Fabric Type</th>
                        <th width="180">Composition</th>
                        <th width="60">Yarn Count</th>
                        <th width="60">GSM</th>
                        <th width="60">Dia</th>
                        <th width="60">UOM</th>
                        <th width="80">Opening Stock</th>
                        <th width="80">Rcv Qty</th>
                        <th width="80">Transfer In</th>
                        <th width="80">Issue Return</th>
                        <th width="80">Total Rcv</th>
                        <th width="80">Issue Qty</th>
                        <th width="80">Transfer Out</th>
                        <th width="80">Rcv Return</th>
                        <th width="80">Total Issue</th>
                        <th width="80">Closing Stock</th>
                        <th width="80">Prouct ID</th>
                        <th>Remarks</th>
					</tr> 
				</thead>
			</table>
			<div style="width:2330px; max-height:280px; overflow-y:scroll" id="scroll_body" > 
			<table align="left" width="2310" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
			<?


				$composition_arr=array();
			    $sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id ";
			    $data_deter=sql_select($sql_deter);

			    if(count($data_deter)>0)
			    {
			    	foreach( $data_deter as $row )
			    	{
			    		if(array_key_exists($row[csf('id')],$composition_arr))
			    		{
			    			$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			    			$constructionArr[$row[csf('id')]]=$constructionArr[$row[csf('id')]];
			    			list($cst,$cps)=explode(',',$composition_arr[$row[csf('id')]]);
			    			$copmpositionArr[$row[csf('id')]]=$cps;
			    		}
			    		else
			    		{
			    			$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			    			$constructionArr[$row[csf('id')]]=$row[csf('construction')];
			    			list($cst,$cps)=explode(',',$composition_arr[$row[csf('id')]]);
			    			$copmpositionArr[$row[csf('id')]]=$cps;
			    		}
			    	}
			    }
			    unset($data_deter);

				
				$tot_opening_bal=0;$tot_purchuse=0;$tot_issue_return=0;$tot_receive=0; $tot_issue=0;
				$tot_receive_return=0;$tot_total_issue=0;$total_closing_stock=0;$tot_closing_stock=0;
				$total_trans_in=0;$tot_trans_out=0;
				
				foreach($data_array as $strRef=>$row)
				{
					$strRefArr = explode("*",$strRef);
					$prod_id  = $strRefArr[0];
					$batch_id = $strRefArr[1];
					$po_breakdown_id = $strRefArr[2];
				
					$openingBalance = ($row[1]['opening']+$row[4]['opening']+$row[5]['opening']) - ($row[2]['opening']+$row[3]['opening']+$row[6]['opening']);
					
					$receive = $row[1]['qnty'];
					$issue_return=$row[4]['qnty'];
					$trans_in=$row[5]['qnty'];
					$totalReceive=$receive+$issue_return+$trans_in;

					$issue=$row[2]['qnty'];
					$rec_return=$row[3]['qnty'];
					$trans_out=$row[6]['qnty'];
					$totalIssue=$issue+$rec_return+$trans_out;

					$closingStock=($openingBalance+$totalReceive)-$totalIssue;
					
					
					$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF"; 
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            
                        <td width="30" title="<? echo $prod_id;?>"><? echo $i; ?></td>
                        <td width="100"><p><? echo $buyer_arr[$row['buyer_id']];?></p></td>
                        <td width="80" align="center"><p><? echo $season_arr[$row['season_buyer_wise']];?></p></td>
                        <td width="120" align="center"><p><? echo $row['booking_no']; ?></p></td>
                        <td width="80"><p><? echo $row["po_number"];?></p></td>
                        <td width="100"><p><? echo $jobNoArr[$row["po_breakdown_id"]]["job_no_mst"] ;?></p></td>
                        <td width="100"><p><? echo $row["style_ref_no"];?></p></td>
                        <td width="100"><p><? echo $row["color_name"]; ?></p></td>
                        <td width="80" align="center"><p><? echo $row['batch_no'];?></p></td>
                        <td width="80"><p><? echo $constructionArr[$row['detarmination_id']];?></p></td>
                        <td width="180"><p><? echo $composition_arr[$row['detarmination_id']]; ?></p></td>
                        <td width="60" align="center"><? //echo $yarn_count_arr[$yarn_count_id_arr[$row[csf('order_id')]]] ?></td>
                        <td width="60"><p><? echo $row["gsm"]; ?></p></td>
                        <td width="60"><p><? echo $row["width"]; ?></p></td>
                        <td width="60" align="center"><p><? echo $unit_of_measurement[$row["uom"]]; ?></p></td>
                        <td width="80" align="right"><? echo number_format($openingBalance,2,'.','');$tot_opening_bal+=$openingBalance; ?></td>
                        <td width="80" align="right"><a href="javascript:fn_receive_dtls('<? echo $row['batch_id'].'__'.$row["gsm"].'__'.$row["width"].'__'.$row["color_id"].'__'.$prod_id.'__'.$select_from_date.'__'.$select_to_date.'__'.$cbo_store_name.'__'.$cbo_company_name.'__'.$po_breakdown_id;?>');"><? echo number_format($receive,2,'.','');$total_rcv_qty+=$receive;?></a></td>
                        <td width="80" align="right"><? echo number_format($trans_in,2,'.',''); $tot_trans_in+=$trans_in;?></td>
                        <td width="80" align="right"><? echo number_format($issue_return,2,'.',''); $tot_issue_return+=$issue_return; ?></td>
                        <td width="80" align="right"><? echo number_format($totalReceive,2,'.',''); $tot_receive+=$totalReceive; ?></td>
                        <td width="80" align="right"><? echo number_format($issue,2,'.',''); $tot_issue+=$issue; ?></td>
                        <td width="80" align="right"><? echo number_format($trans_out,2,'.',''); $tot_trans_out+=$trans_out?></td>
                        <td width="80" align="right"><? echo number_format($rec_return,2,'.',''); $tot_receive_return+=$rec_return; ?></td>
                        <td width="80" align="right"><? echo number_format($totalIssue,2,'.',''); $tot_total_issue+=$totalIssue; ?></td>
                        <td width="80" align="right"><? echo number_format($closingStock,2,'.',''); $total_closing_stock+=$closingStock; ?></td>
                        <td width="80" align="right"><? echo $prod_id; ?></td>
                        <td><? echo $row["remarks"]; ?></td>
					</tr>
					<? 												
					$i++;
					
					 
				}
			?>
			</table>
		   </div>
			<table width="2310" border="1" cellpadding="0" cellspacing="0" class="tbl_bottom" rules="all">
				<tr>
                    <td width="30"></td>
                    <td width="100"></td>
                    <td width="80"></td>
                    <td width="120"></td>
                    <td width="80"></td>
                    <td width="100"></td>
                    <td width="100"></td>
                    <td width="100"></td>
                    <td width="80"></td>
                    <td width="80"></td>
                    <td width="180"></td>
                    <td width="60"></td>
                    <td width="60"></td>
                    <td width="60"></td>
                    <td width="60"></td>
                    <td width="80" align="right" id="td_opening_stock"><? echo number_format($tot_opening_bal,2);?></td>
                    <td width="80" align="right" id="td_rcv_qty"><? echo number_format($total_rcv_qty,2);?></td>
                    <td width="80" align="right" id="td_transfer_in"><? echo $tot_trans_in;?></td>
                    <td width="80" align="right" id="td_issue_return"><? echo number_format($tot_issue_return,2);?></td>
                    <td width="80" align="right" id="td_total_rcv"><? echo number_format($tot_receive,2);?></td>
                    <td width="80" align="right" id="td_issue_qty"><? echo number_format($tot_issue,2);?></td>
                    <td width="80" align="right" id="td_transfer_out"><? echo $tot_trans_out;?></td>
                    <td width="80" align="right" id="td_rcv_return"><? echo number_format($tot_receive_return,2);?></td>
                    <td width="80" align="right" id="td_total_issue"><? echo number_format($tot_total_issue,2);?></td>
                    <td width="80" align="right" id="td_closing_stock"><? echo number_format($total_closing_stock,2);?></td>
                    <td width="80"></td>
                    <td></td>
				</tr>
			</table>
		</div>
		<?
			} 
	
	
	}
	
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
    echo "$html**$filename**$report_type"; 
    exit();
}



if($action=="generate_report_02_01_2021")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	
	
	$cbo_company_name =str_replace("'","",$cbo_company_name);
	$cbo_buyer_id =str_replace("'","",$cbo_buyer_id);
	$cbo_season_id =str_replace("'","",$cbo_season_id);
	$txt_product_id =str_replace("'","",$txt_product_id);
	$txt_product_id_des =str_replace("'","",$txt_product_id_des);
	$txt_gsm =str_replace("'","",$txt_gsm);
	$txt_color =str_replace("'","",$txt_color);
	$cbo_sample_type =str_replace("'","",$cbo_sample_type);
	$txt_booking =str_replace("'","",$txt_booking);
	$txt_date_from =str_replace("'","",$txt_date_from);
	$txt_date_to =str_replace("'","",$txt_date_to);
	$cbo_store_name =str_replace("'","",$cbo_store_name);
	

	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name"); 
	
	$yarn_count_arr=return_library_array("select id,yarn_count from lib_yarn_count where status_active=1 and is_deleted=0","id","yarn_count");
	
	$buyer_arr = return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$season_arr = return_library_array( "select id, season_name from lib_buyer_season",'id','season_name');
	
	if($db_type==0)
	{
		$select_from_date=change_date_format($txt_date_from,'yyyy-mm-dd');
		$select_to_date=change_date_format($txt_date_to,'yyyy-mm-dd');
	}
	else if($db_type==2) 
	{
		$select_from_date=change_date_format($txt_date_from,'','',1);
		$select_to_date=change_date_format($txt_date_to,'','',1);
	}
	else 
	{
		$select_from_date="";
		$select_to_date="";
	}
	
	
	
	
	
	$sql_cond="";
	if ($cbo_company_name!=0) $sql_cond =" and a.company_id=$cbo_company_name";
	if ($cbo_store_name!=0) $sql_cond .=" and a.store_id =$cbo_store_name ";
	
	if($report_type ==1)//Show Button
	{
		$sql="select a.pi_wo_batch_no,a.prod_id,
			sum(case when a.transaction_type=1 and item_category=2 and a.transaction_date<'".$select_from_date."' then a.cons_quantity else 0 end) as rcv_total_opening,
			sum(case when a.transaction_type=2 and item_category=2 and a.transaction_date<'".$select_from_date."' then a.cons_quantity else 0 end) as iss_total_opening,
			sum(case when a.transaction_type=3 and item_category=2 and a.transaction_date<'".$select_from_date."' then a.cons_quantity else 0 end) as rcv_return_opening,
			sum(case when a.transaction_type=4 and item_category=2 and a.transaction_date<'".$select_from_date."' then a.cons_quantity else 0 end) as iss_return_opening,
			sum(case when a.transaction_type in(1,4) and item_category=2 and a.transaction_date<'".$select_from_date."' then a.cons_amount else 0 end) as rcv_total_opening_amt,
			sum(case when a.transaction_type in(2,3) and item_category=2 and a.transaction_date<'".$select_from_date."' then a.cons_amount else 0 end) as iss_total_opening_amt,
			
			sum(case when a.transaction_type=6 and item_category=2 and a.transaction_date<'".$select_from_date."' then a.cons_quantity else 0 end) as trans_in_total_opening,
			sum(case when a.transaction_type=5 and item_category=2 and a.transaction_date<'".$select_from_date."' then a.cons_quantity else 0 end) as trans_out_total_opening,
			
			sum(case when a.transaction_type=1 and item_category=2 and a.transaction_date between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as receive,
			sum(case when a.transaction_type=2 and item_category=2 and a.transaction_date between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as issue,
			sum(case when a.transaction_type=3 and item_category=2 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as rec_return,
			sum(case when a.transaction_type=4 and item_category=2 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as issue_return,
			sum(case when a.transaction_type in(1,4) and item_category=2 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_amount else 0 end) as rcv_total_value,
			sum(case when a.transaction_type in(2,3) and item_category=2 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_amount else 0 end) as iss_total_value,
			
			sum(case when a.transaction_type=6 and item_category=2 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as trans_in,
			sum(case when a.transaction_type=5 and item_category=2 and a.transaction_date  between '".$select_from_date."' and '".$select_to_date."' then a.cons_quantity else 0 end) as trans_out,
			
			sum(case when a.transaction_date between '".$select_from_date."' and '".$select_to_date."' then a.cons_rate else 0 end) as rate
			from inv_transaction a
			where a.status_active=1 and a.is_deleted=0 $sql_cond group by a.pi_wo_batch_no,a.prod_id order by a.prod_id ASC";
		
		$data_trns_array=array();
		$trnasactionData=sql_select($sql);  //echo $sql;
		foreach($trnasactionData as $row)
		{
			$key=$row[csf("pi_wo_batch_no")].$row[csf("prod_id")];
			$data_trns_array[$key]['rcv_total_opening']=$row[csf("rcv_total_opening")];
			$data_trns_array[$key]['iss_total_opening']=$row[csf("iss_total_opening")];
			$data_trns_array[$key]['rcv_return_opening']=$row[csf("rcv_return_opening")];
			$data_trns_array[$key]['iss_return_opening']=$row[csf("iss_return_opening")];
			$data_trns_array[$key]['receive']=$row[csf("receive")];
			$data_trns_array[$key]['issue_return']=$row[csf("issue_return")];
			$data_trns_array[$key]['issue']=$row[csf("issue")];
			$data_trns_array[$key]['rec_return']=$row[csf("rec_return")];
			$data_trns_array[$key]['avg_rate']=$row[csf("rate")];
			$data_trns_array[$key]['rcv_total_opening_amt']=$row[csf("rcv_total_opening_amt")];
			$data_trns_array[$key]['iss_total_opening_amt']=$row[csf("iss_total_opening_amt")];
			$data_trns_array[$key]['rcv_total_value']=$row[csf("rcv_total_value")];
			$data_trns_array[$key]['iss_total_value']=$row[csf("iss_total_value")];
			$data_trns_array[$key]['trans_in']=$row[csf("trans_in")];
			$data_trns_array[$key]['trans_out']=$row[csf("trans_out")];
			
			$data_trns_array[$key]['trans_in_total_opening']=$row[csf("trans_in_total_opening")];
			$data_trns_array[$key]['trans_out_total_opening']=$row[csf("trans_out_total_opening")];
			
			
			
			if(($row[csf("prod_id")]>0 and $row[csf("receive")]>0) || ($row[csf("prod_id")]>0 and $row[csf("issue")]>0)){
				$prod_id_arr[$row[csf("prod_id")]]=$row[csf("prod_id")];
			}
			
		}
		$prod_id_list_arr=array_chunk($prod_id_arr,999);
		
		
		//Product...............................................................
		
		if ($txt_product_id_des) $composition_con =" and product_name_details like('%".trim($txt_product_id_des)."%')";

		if($db_type==0)
		{
			$sql="select id, detarmination_id, gsm, dia_width, color, unit_of_measure, current_stock from product_details_master where status_active=1 and is_deleted=0 $composition_con id in(".implode(',',$prod_id_arr).")";
		}
		else
		{
			
			$sql = "select id, detarmination_id, gsm, dia_width, color,product_name_details from product_details_master where status_active=1 and is_deleted=0 $composition_con";
			$p=1;
			foreach($prod_id_list_arr as $pordIdArr)
			{
				if($p==1) $sql .="  and ( id in(".implode(',',$pordIdArr).")"; 
				else  $sql .=" or id in(".implode(',',$pordIdArr).")";
				
				$p++;
			}
			$sql .=")";
		}
		   //echo $sql;
		 
		$temp_prod_id_arr=array();
		$product_data_array=sql_select($sql);
		foreach( $product_data_array as $row )
		{
			$product_data_arr[$row[csf('id')]]['product_name_details']=$row[csf('product_name_details')];
			$temp_prod_id_arr[$row[csf("id")]]=$row[csf("id")];

		}
		
		if(count($temp_prod_id_arr)>0){
			$prod_id_list_arr=array_chunk($temp_prod_id_arr,999);
		}

		
		//receive--------------------------------------------------------
		$sql_all_cond="";
		if ($cbo_company_name!=0) $sql_all_cond =" and a.company_id='$cbo_company_name'";
		if ($cbo_store_name) $sql_all_cond .=" and a.store_id ='".trim($cbo_store_name)."'";
		if ($txt_gsm) $sql_all_cond .=" and b.gsm ='".trim($txt_gsm)."'";
		
		if ($txt_booking and $cbo_sample_type==2){$sql_all_cond .=" and a.booking_no like('%".trim($txt_booking)."%')";}
		else if ($txt_booking and $cbo_sample_type!=2) $sql_all_cond .=" and d.booking_no like('%".trim($txt_booking)."%')";

		if ($txt_color){$sql_all_cond .=" and c.color_name ='".trim($txt_color)."'";}
				
		if($cbo_buyer_id!=0){$sql_all_cond .=" and b.buyer_id='$cbo_buyer_id'";}
		if($cbo_season_id!=0){$sql_all_cond .=" and h.season_buyer_wise=$cbo_season_id";}
		
		if($cbo_sample_type==1){$sql_all_cond .= " and f.po_breakdown_id > 0";}
		else if($cbo_sample_type==2){$sql_all_cond .= " and f.po_breakdown_id is NULL";}
				
				
				if($db_type==0)
				{
					$sql="select a.receive_basis,d.booking_no,d.batch_no,b.batch_id,b.prod_id,b.fabric_description_id,b.gsm,b.width,b.color_id,b.uom,f.po_breakdown_id as order_id, b.buyer_id, c.color_name ,h.season_buyer_wise ,h.style_ref_no,g.po_number,b.remarks
						from 
						inv_receive_master a join pro_finish_fabric_rcv_dtls b on a.id=b.mst_id join lib_color c on b.color_id=c.id 
						LEFT JOIN order_wise_pro_details f on b.id=f.dtls_id and f.prod_id=b.prod_id and f.entry_form =37 
						LEFT JOIN pro_batch_create_mst d ON b.batch_id=d.id and d.status_active=1 and d.is_deleted=0
						LEFT JOIN pro_batch_create_dtls e ON d.id=e.mst_id and b.batch_id=e.mst_id and d.color_id=b.color_id and e.body_part_id=b.body_part_id and b.prod_id=e.prod_id and e.status_active=1 and e.is_deleted=0
						LEFT JOIN wo_po_break_down g ON g.id=f.po_breakdown_id and g.status_active=1 and g.is_deleted=0
						LEFT JOIN wo_po_details_master h ON g.job_no_mst=h.job_no and h.status_active=1 and h.is_deleted=0
						where a.item_category=2 and a.entry_form =37 and b.trans_id>0 and a.status_active=1 
						and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_all_cond and a.id in(".implode(',',$prod_id_arr).")  
						group by a.receive_basis,d.booking_no,d.batch_no,b.batch_id,b.prod_id,b.fabric_description_id,b.gsm,b.width,b.color_id,b.uom,f.po_breakdown_id, b.buyer_id, c.color_name,h.season_buyer_wise ,h.style_ref_no,g.po_number,b.remarks
					order by b.prod_id
						
						";
				}
				else
				{
					
					
						$sql="select a.receive_basis,d.booking_no,d.batch_no,b.batch_id,b.prod_id,b.fabric_description_id,b.gsm,b.width,b.color_id,b.uom,f.po_breakdown_id as order_id, b.buyer_id, c.color_name ,h.season_buyer_wise ,h.style_ref_no,g.po_number,b.remarks
						from 
						
						inv_receive_master a join pro_finish_fabric_rcv_dtls b on a.id=b.mst_id join lib_color c on b.color_id=c.id  
						LEFT JOIN order_wise_pro_details f on b.id=f.dtls_id and f.prod_id=b.prod_id and f.entry_form =37 and f.trans_type=1 and f.status_active=1 and f.is_deleted=0
						
						LEFT JOIN pro_batch_create_mst d ON b.batch_id=d.id and d.status_active=1 and d.is_deleted=0
						LEFT JOIN pro_batch_create_dtls e ON d.id=e.mst_id and b.batch_id=e.mst_id and d.color_id=b.color_id and e.body_part_id=b.body_part_id and b.prod_id=e.prod_id and e.status_active=1 and e.is_deleted=0
						
						LEFT JOIN wo_po_break_down g ON g.id=f.po_breakdown_id and g.status_active=1 and g.is_deleted=0
						LEFT JOIN wo_po_details_master h ON g.job_no_mst=h.job_no and h.status_active=1 and h.is_deleted=0
						where a.item_category=2 and a.entry_form =37 and b.trans_id>0 
						and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_all_cond 
						";
					$p=1;
					foreach($prod_id_list_arr as $pordIdArr)
					{
						if($p==1) $sql .="  and ( b.prod_id in(".implode(',',$pordIdArr).")"; 
						else  $sql .=" or b.prod_id in(".implode(',',$pordIdArr).")";
						
						$p++;
					}
					$sql .=")
					group by a.receive_basis,d.booking_no,d.batch_no,b.batch_id,b.prod_id,b.fabric_description_id,b.gsm,b.width,b.color_id,b.uom,f.po_breakdown_id, b.buyer_id, c.color_name,h.season_buyer_wise ,h.style_ref_no,g.po_number,b.remarks
					order by b.prod_id
					";
				}
				
				//echo $sql; 
				
				$result = sql_select($sql);
				foreach($result as $row)
				{
					$key=$row[csf('batch_id')].$row[csf('prod_id')];
					$receiveDataArr[$key]=$row;
					$batch_id_arr[$row[csf('batch_id')]]=$row[csf('batch_id')];
					$booking_no_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];
					if($row[csf('order_id')]){$order_id_arr[$row[csf('order_id')]]=$row[csf('order_id')];}
					
				}
				
				
			$sql="select a.booking_no,b.style_ref_no,b.buyer_name from wo_non_ord_samp_booking_dtls a, sample_development_mst b where a.style_id=b.id ";
				$p=1;
				$booking_no_chunk_arr=array_chunk($booking_no_arr,999);
				foreach($booking_no_chunk_arr as $batchNoArr)
				{
					if($p==1) $sql .="  and ( a.booking_no in('".implode("','",$batchNoArr)."')"; 
					else  $sql .=" or a.booking_no in('".implode("','",$batchNoArr)."')";
					
					$p++;
				}
				$sql .=")";
			$sql;
			$result = sql_select($sql);
			foreach($result as $row)
			{
				$style_ref_arr[$row[csf('booking_no')]]=$row[csf('style_ref_no')];
				$style_buyer_arr[$row[csf('booking_no')]]=$row[csf('buyer_name')];
				
			}
			//$style_ref_arr = return_library_array( $sql,'booking_no','style_ref_no');
			
			

		
		
		$i=1;
		ob_start();	
		if($report_type ==1)
		{ 
		?>
		<div> 
			<table width="2210" border="1" cellpadding="2" cellspacing="0" class="" id="table_header_1" > 
				<thead>
					<tr class="form_caption" style="border:none;">
						<td colspan="25" align="center" style="border:none;font-size:16px; font-weight:bold" > <? echo $report_title; ?></td> 
					</tr>
					<tr style="border:none;">
						<td colspan="25" align="center" style="border:none; font-size:14px;">
						   <b>Company Name : <? echo $companyArr[$cbo_company_name]; ?></b>                               
						</td>
					</tr>
					<tr style="border:none;">
						<td colspan="25" align="center" style="border:none;font-size:12px; font-weight:bold">
							<? if($from_date!="" || $to_date!="")echo "From : ".change_date_format($from_date,'dd-mm-yyyy')." To : ".change_date_format($to_date,'dd-mm-yyyy')."" ;?>
						</td>
					</tr>
				</thead>
			</table>
			<table width="2210" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all"> 
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="100">Buyer</th>
                        <th width="80">Season</th>
                        <th width="120">Booking No.</th>
                        <th width="80">Order No</th>
                        <th width="100">Style</th>
                        <th width="100">Color</th>
                        <th width="80">Batch No</th>
                        <th width="80">Fabric Type</th>
                        <th width="180">Composition</th>
                        <th width="60">Yarn Count</th>
                        <th width="60">GSM</th>
                        <th width="60">Dia</th>
                        <th width="60">UOM</th>
                        <th width="80">Opening Stock</th>
                        <th width="80">Rcv Qty</th>
                        <th width="80">Transfer In</th>
                        <th width="80">Issue Return</th>
                        <th width="80">Total Rcv</th>
                        <th width="80">Issue Qty</th>
                        <th width="80">Transfer Out</th>
                        <th width="80">Rcv Return</th>
                        <th width="80">Total Issue</th>
                        <th width="80">Closing Stock</th>
                        <th width="80">Prouct ID</th>
                        <th>Remarks</th>
					</tr> 
				</thead>
			</table>
			<div style="width:2230px; max-height:280px; overflow-y:scroll" id="scroll_body" > 
			<table align="left" width="2210" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
			<?
				$composition_arr=array();
				$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
				$data_array=sql_select($sql_deter);
				if(count($data_array)>0)
				{
					foreach( $data_array as $row )
					{
						if(array_key_exists($row[csf('id')],$composition_arr))
						{
							$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
						}
						else
						{
							$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
						}
						
						
					}
				}
				
				
				
				$tot_opening_bal=0;$tot_purchuse=0;$tot_issue_return=0;$tot_receive=0; $tot_issue=0;
				$tot_receive_return=0;$tot_total_issue=0;$total_closing_stock=0;$tot_closing_stock=0;
				$total_trans_in=0;$tot_trans_out=0;
				
				
				foreach($receiveDataArr as $row)
				{
					$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF"; 
					
					$key=$row[csf('batch_id')].$row[csf("prod_id")];
					
					$openingBalance =($data_trns_array[$key]['rcv_total_opening']+$data_trns_array[$key]['iss_return_opening']+$data_trns_array[$key]['trans_in_total_opening'])-($data_trns_array[$key]['iss_total_opening']+$data_trns_array[$key]['rcv_return_opening']+$data_trns_array[$key]['trans_in_total_opening']);
					
					list($fabric_type)=explode(',',$product_data_arr[$row[csf('prod_id')]]['product_name_details']);
					
					
					$trans_in=$data_trns_array[$key]['trans_in'];
					$trans_out=$data_trns_array[$key]['trans_out'];
					
					
					$receive = $data_trns_array[$key]['receive'];
					$issue_return=$data_trns_array[$key]['issue_return'];
					$totalReceive=$receive+$issue_return+$trans_in;
					$issue=$data_trns_array[$key]['issue'];
					$rec_return=$data_trns_array[$key]['rec_return'];
					$totalIssue=$issue+$rec_return+$trans_out;
					$closingStock=($openingBalance+$totalReceive)-$totalIssue;
					
					
					
					
					
					$row[csf("style_ref_no")]=($row[csf("style_ref_no")]!='')?$row[csf("style_ref_no")]:$style_ref_arr[$row[csf('booking_no')]];
					
					$row[csf("buyer_id")]=($row[csf("buyer_id")]!='')?$row[csf("buyer_id")]:$style_buyer_arr[$row[csf('booking_no')]];
					
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            
                        <td width="30" title="<? echo $row[csf("prod_id")];?>"><? echo $i; ?></td>
                        <td width="100"><p><? echo $buyer_arr[$row[csf('buyer_id')]];?></p></td>
                        <td width="80" align="center"><p><? echo $season_arr[$row[csf('season_buyer_wise')]];?></p></td>
                        <td width="120" align="center"><p><? echo $row[csf('booking_no')]; ?></p></td>
                        <td width="80"><p><? echo $row[csf("po_number")];?></p></td>
                        <td width="100"><p><? echo $row[csf("style_ref_no")];?></p></td>
                        <td width="100"><p><? echo $row[csf("color_name")]; ?></p></td>
                        <td width="80" align="center"><p><? echo $row[csf('batch_no')];//$batch_no_arr[$row[csf('batch_id')]];?></p></td>
                        <td width="80"><p><? echo $fabric_type;?></p></td>
                        <td width="180"><p><? echo $composition_arr[$row[csf('fabric_description_id')]]; ?></p></td>
                        <td width="60" align="center"><? echo $yarn_count_arr[$yarn_count_id_arr[$row[csf('order_id')]]] ?></td>
                        <td width="60"><p><? echo $row[csf("gsm")]; ?></p></td>
                        <td width="60"><p><? echo $row[csf("width")]; ?></p></td>
                        <td width="60" align="center"><p><? echo $unit_of_measurement[$row[csf("uom")]]; ?></p></td>
                        <td width="80" align="right"><? echo number_format($openingBalance,2,'.','');$tot_opening_bal+=$openingBalance; ?></td>
                        <td width="80" align="right"><a href="javascript:fn_receive_dtls('<? echo $row[csf('batch_id')].'__'.$row[csf("gsm")].'__'.$row[csf("width")].'__'.$row[csf("color_id")].'__'.$row[csf("prod_id")].'__'.$select_from_date.'__'.$select_to_date.'__'.$cbo_store_name.'__'.$cbo_company_name;?>');"><? echo number_format($receive,2,'.','');$total_rcv_qty+=$receive;?></a></td>
                        <td width="80" align="right"><? echo number_format($trans_in,2,'.',''); $tot_trans_in+=$trans_in;?></td>
                        <td width="80" align="right"><? echo number_format($issue_return,2,'.',''); $tot_issue_return+=$issue_return; ?></td>
                        <td width="80" align="right"><? echo number_format($totalReceive,2,'.',''); $tot_receive+=$totalReceive; ?></td>
                        <td width="80" align="right"><? echo number_format($issue,2,'.',''); $tot_issue+=$issue; ?></td>
                        <td width="80" align="right"><? echo number_format($trans_out,2,'.',''); $tot_trans_out+=$trans_out?></td>
                        <td width="80" align="right"><? echo number_format($rec_return,2,'.',''); $tot_receive_return+=$rec_return; ?></td>
                        <td width="80" align="right"><? echo number_format($totalIssue,2,'.',''); $tot_total_issue+=$totalIssue; ?></td>
                        <td width="80" align="right"><? echo number_format($closingStock,2,'.',''); $total_closing_stock+=$closingStock; ?></td>
                        <td width="80" align="right"><? echo $row[csf("prod_id")]; ?></td>
                        <td><? echo $row[csf("remarks")]; ?></td>
					</tr>
					<? 												
					$i++;
					
					 
				}
			?>
			</table>
		   </div>
			<table width="2210" border="1" cellpadding="0" cellspacing="0" class="tbl_bottom" rules="all">
				<tr>
                    <td width="30"></td>
                    <td width="100"></td>
                    <td width="80"></td>
                    <td width="120"></td>
                    <td width="80"></td>
                    <td width="100"></td>
                    <td width="100"></td>
                    <td width="80"></td>
                    <td width="80"></td>
                    <td width="180"></td>
                    <td width="60"></td>
                    <td width="60"></td>
                    <td width="60"></td>
                    <td width="60"></td>
                    <td width="80" align="right" id="td_opening_stock"><? echo number_format($tot_opening_bal,2);?></td>
                    <td width="80" align="right" id="td_rcv_qty"><? echo number_format($total_rcv_qty,2);?></td>
                    <td width="80" align="right" id="td_transfer_in"><? echo $tot_trans_in;?></td>
                    <td width="80" align="right" id="td_issue_return"><? echo number_format($tot_issue_return,2);?></td>
                    <td width="80" align="right" id="td_total_rcv"><? echo number_format($tot_receive,2);?></td>
                    <td width="80" align="right" id="td_issue_qty"><? echo number_format($tot_issue,2);?></td>
                    <td width="80" align="right" id="td_transfer_out"><? echo $tot_trans_out;?></td>
                    <td width="80" align="right" id="td_rcv_return"><? echo number_format($tot_receive_return,2);?></td>
                    <td width="80" align="right" id="td_total_issue"><? echo number_format($tot_total_issue,2);?></td>
                    <td width="80" align="right" id="td_closing_stock"><? echo number_format($total_closing_stock,2);?></td>
                    <td width="80"></td>
                    <td></td>
				</tr>
			</table>
		</div>
		<?
			} 
	
	
	}
	
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
    echo "$html**$filename**$report_type"; 
    exit();
}


?>