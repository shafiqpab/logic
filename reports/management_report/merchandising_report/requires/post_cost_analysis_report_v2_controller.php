<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');
require_once('../../../../includes/class4/class.conditions.php');
require_once('../../../../includes/class4/class.reports.php');
require_once('../../../../includes/class4/class.commisions.php');
require_once('../../../../includes/class4/class.trims.php');
require_once('../../../../includes/class4/class.fabrics.php');
require_once('../../../../includes/class4/class.yarns.php');
require_once('../../../../includes/class4/class.conversions.php');
require_once('../../../../includes/class4/class.others.php');
require_once('../../../../includes/class4/class.emblishments.php');
require_once('../../../../includes/class4/class.commercials.php');
require_once('../../../../includes/class4/class.washes.php');


$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];

$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$company_short_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name"  );
$item_library=return_library_array( "select id,item_name from  lib_item_group", "id", "item_name"  );
$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id", "supplier_name"  );
$team_leader_library=return_library_array( "select id, team_leader_name from lib_marketing_team", "id", "team_leader_name");
$country_name_arr=return_library_array( "select id, country_name   from lib_country  where status_active=1 and is_deleted =0",'id','country_name');



if($db_type==2) $select_date=" to_char(a.insert_date,'YYYY')";
else if ($db_type==0) $select_date=" year(a.insert_date)";

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond  group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0);
	exit();
}


if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
	
		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#hide_job_id").val(splitData[0]); 
			$("#hide_job_no").val(splitData[1]); 
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
							
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID  $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref",3=>"Order No");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" value="<? echo $txt_job_no;?>" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>'+'**'+'<? echo $type; ?>', 'create_job_no_search_list_view', 'search_div', 'post_cost_analysis_report_v2_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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

if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	$month_id=$data[5];
	$type_id=$data[6];
	//echo $type_id;
	//echo $month_id;
	//echo $data[1];
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
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
	//echo $buyer_id_cond;
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";
	if($search_by==1) $search_field=" a.job_no";
	else if($search_by==2) $search_field=" a.style_ref_no";
	else $search_field="b.po_number";
	//$year="year(insert_date)";
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";
	
	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and year(a.insert_date)=$year_id"; else $year_cond="";	
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(a.insert_date,'YYYY')";
		if($year_id!=0) $year_cond="$year_field_con=$year_id"; else $year_cond="";	
	}
	//if($month_id!=0) $month_cond=" and month(insert_date)=$month_id"; else $month_cond="";
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	// $sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_field from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond  order by job_no";
	
	if($type_id==1 && $search_by!=3)
	{
		  $sql= "select a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name,a.style_ref_no, $year_field from wo_po_details_master a where a.status_active=1 and a.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond  order by a.job_no";
		echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","600","240",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','') ;
		exit(); 
	}
	else
	{
		  $sql= "select a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name,a.style_ref_no, $year_field,b.id,b.po_number from wo_po_details_master a,wo_po_break_down b where  a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond  order by a.job_no";
		echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No,PO No", "120,130,80,60,100","700","240",0, $sql , "js_set_value", "id,po_number", "", 1, "company_name,buyer_name,0,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no,po_number", "",'','0,0,0,0,0,0','') ;
	exit(); 
	}
} // Job Search end

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$reporttype=str_replace("'","",$reporttype);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_job_id=str_replace("'","",$txt_job_id);
	$txt_order=str_replace("'","",$txt_order);
	$txt_order_id=str_replace("'","",$txt_order_id);
	$year_id=str_replace("'","",$cbo_year);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	
	if($cbo_company_name==0) $company_name_cond=""; else $company_name_cond=" and a.company_name='$cbo_company_name' ";

	if(str_replace("'","",$cbo_buyer_name)==0)
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
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
	}
	$job_style_cond="";
	if(trim(str_replace("'","",$txt_job_no))!="")
	{
		if(str_replace("'","",$txt_job_id)!="")
		{
			$job_style_cond=" and a.id in(".str_replace("'","",$txt_job_id).")";
		}
		else
		{
			$job_style_cond=" and a.job_no_prefix_num = '".trim(str_replace("'","",$txt_job_no))."'";
		}
	}
	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and year(a.insert_date)=$year_id"; else $year_cond="";	
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(a.insert_date,'YYYY')";
		if($year_id!=0) $year_cond="$year_field_con=$year_id"; else $year_cond="";	
	}
	
	//$order_cond="";
	//if(trim($txt_order)!="") $order_cond="and b.po_number='$txt_order'";
	//if($txt_order_id!="") $order_cond="and b.id in($txt_order_id)";
	
	$order_cond="";
	if(trim(str_replace("'","",$txt_order_no))!="")
	{
		if(str_replace("'","",$hide_order_id)!="")
		{
			$order_cond=" and b.id in(".str_replace("'","",$hide_order_id).")";
		}
		else
		{
			$order_cond=" and b.po_number = '".trim(str_replace("'","",$txt_order_no))."'";
		}
	}
	ob_start();
	
	 $sql_po="select a.job_no_prefix_num as job_prefix,a.ship_mode,a.avg_unit_price, a.job_no,a.team_leader, a.product_dept,a.company_name, a.buyer_name, a.team_leader, a.style_description as style_desc, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, b.id as po_id, b.po_number,b.is_confirmed, b.pub_shipment_date,b.shipment_date,b.up_charge ,b.po_quantity,b.plan_cut,b.matrix_type, b.unit_price, b.po_total_price,c.exchange_rate from wo_po_details_master a, wo_po_break_down b ,wo_pre_cost_mst c where a.id=b.job_id and a.id=c.job_id and b.job_id=c.job_id    and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 $company_name_cond  $job_style_cond $order_cond $buyer_id_cond  $year_cond order  by b.pub_shipment_date, b.id";
	
	$sql_po_result=sql_select($sql_po);
	
	$all_po_id="";$all_po_no="";$all_job="";$all_full_job="";$all_style="";$all_style_desc=""; $all_buyer=""; $all_pub_date=""; 
	$order_qty_pcs=0;$total_fob_value=0;$total_order_qty=0;$total_unit_price=0;$total_job_unit_price=0;
	//echo $buyer_name;die;
	$po_numer_arr=$po_data_arr=array();$tot_count=0;$total_order_upcharge=0;
	foreach($sql_po_result as $row)
	{
		if($all_po_id=="") $all_po_id=$row[csf("po_id")]; else $all_po_id.=",".$row[csf("po_id")];
		if($all_po_no=="") $all_po_no=$row[csf("po_number")]; else $all_po_no.=",".$row[csf("po_number")];
		if($all_job=="") $all_job=$row[csf("job_prefix")]; else $all_job.=",".$row[csf("job_prefix")];
		if($all_buyer=="") $all_buyer=$buyer_arr[$row[csf("buyer_name")]]; else $all_buyer.=",".$buyer_arr[$row[csf("buyer_name")]];
		if($all_full_job=="") $all_full_job="'".$row[csf('job_no')]."'"; else $all_full_job.=","."'".$row[csf('job_no')]."'";
		if($all_pub_date=="") $all_pub_date=$row[csf("pub_shipment_date")]; else $all_pub_date.=",".$row[csf("pub_shipment_date")];
		if($all_style=="") $all_style=$row[csf("style_ref_no")]; else $all_style.=",".$row[csf("style_ref_no")];
	  //	if($all_order_uom=="") $all_order_uom=$unit_of_measurement[$row[csf("order_uom")]]; else $all_order_uom.=",".$unit_of_measurement[$row[csf("order_uom")]];
		$job_no=$row[csf('job_no')];
		
	
		$product_depertment=$product_dept[$row[csf("product_dept")]];
		$gmts_item_id=$row[csf("gmts_item_id")];$exchange_rate=$row[csf("exchange_rate")];
		$team_leader=$team_leader_library[$row[csf("team_leader")]];
		
		$po_numer_arr[$row[csf("po_id")]]=$row[csf("po_number")];
		$po_data_arr[$row[csf("po_id")]]['po_qty']=$row[csf("po_quantity")];
		$po_data_arr[$row[csf("po_id")]]['plan_cut']=$row[csf("plan_cut")];
		
		$po_data_arr_qty[$row[csf("po_id")]]['ratio']=$row[csf("ratio")];
		$order_qty_pcs+=$row[csf('po_quantity')]*$row[csf('ratio')];
		$total_order_qty+=$row[csf('po_quantity')];
		$total_order_upcharge+=$row[csf('up_charge')];
		$total_unit_price+=$row[csf('unit_price')];
		$total_fob_value+=$row[csf('po_total_price')];
		$tot_count+=count($row[csf('po_id')]);
		//echo $total_fob_value.'='.$total_order_qty.'='.$dzn_qnty;
	} 
	$total_job_unit_price=($total_fob_value/$total_order_qty);
	$all_pub_date=explode(",",$all_pub_date);
	$all_last_pub_date= max($all_pub_date);
	 $all_last_mon=date('M-y',strtotime($all_last_pub_date));
	$all_job_no=array_unique(explode(",",$all_full_job));
	$all_jobs="";
	foreach($all_job_no as $jno)
	{
			if($all_jobs=="") $all_jobs=$jno; else $all_jobs.=",".$jno;
	}
	//echo $all_jobs.'DD';
		$all_po=array_unique(explode(",",$all_po_id));
		$po_arr_cond=array_chunk($all_po,1000, true);
		$po_cond_for_in="";$po_cond_for_in2="";$po_cond_for_in3="";$po_cond_for_in4="";$po_cond_for_in5="";
		$pi=0;
		foreach($po_arr_cond as $key=>$value)
		{
		   if($pi==0)
		   {
			$po_cond_for_in=" and ( c.po_break_down_id  in(".implode(",",$value).")"; 
			$po_cond_for_in2=" and ( d.po_break_down_id  in(".implode(",",$value).")"; 
			$po_cond_for_in3=" and ( b.id  in(".implode(",",$value).")"; 
			$po_cond_for_in4=" and ( b.po_break_down_id  in(".implode(",",$value).")"; 
			$po_cond_for_in5=" and ( b.po_breakdown_id  in(".implode(",",$value).")"; 
		   }
		   else //po_break_down_id
		   {
			$po_cond_for_in.=" or c.po_break_down_id  in(".implode(",",$value).")";
			$po_cond_for_in2.=" or d.po_break_down_id  in(".implode(",",$value).")";
			$po_cond_for_in3.=" and b.id  in(".implode(",",$value).")"; 
			$po_cond_for_in4.=" and b.po_break_down_id  in(".implode(",",$value).")"; 
			$po_cond_for_in5.=" and b.po_breakdown_id  in(".implode(",",$value).")"; 
		   }
		   $pi++;
		}	
		$po_cond_for_in.=" )";
		$po_cond_for_in2.=" )";
		$po_cond_for_in3.=" )";
		$po_cond_for_in4.=" )";
		$po_cond_for_in5.=" )";
		
		 $sql_emb="select
		c.po_break_down_id as po_id,sum(c.amount) as famount 
		from  wo_pre_cost_mst a,wo_pre_cost_embe_cost_dtls b ,wo_booking_dtls c ,wo_booking_mst d 
		where a.job_no=b.job_no and a.job_no=c.job_no and c.booking_no=d.booking_no and a.job_no=d.job_no and b.id=c.pre_cost_fabric_cost_dtls_id  and c.booking_type in(6) and c.is_short=2 and  d.item_category=25 and a.is_deleted=0 and  a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and c.wo_qnty>0 $po_cond_for_in
		group by c.po_break_down_id order by c.po_break_down_id";
		$emb_result=sql_select($sql_emb);
		$embl_data_arr=array();
		foreach($emb_result as $row)
		{
			$embl_data_arr[$row[csf('po_id')]]['eamount']=$row[csf('famount')];
		}
		unset($emb_result);
	 $sql_datas=("select b.po_breakdown_id as po_id,a.country_id,a.invoice_no,max(a.invoice_date) as invoice_date,a.shipping_mode,
		sum(b.current_invoice_qnty) as invo_qnty
		 from  com_export_invoice_ship_mst a,com_export_invoice_ship_dtls b where a.id=b.mst_id   and a.status_active ='1' and a.is_deleted ='0' $po_cond_for_in5 group by b.po_breakdown_id,a.country_id,a.invoice_no,a.shipping_mode"); 
		 $sql_inv_result=sql_select($sql_datas);
		
		$export_invoice_arr=array();
		foreach($sql_inv_result as $row)
		{
			
			$export_invoice_arr[$row[csf('po_id')]][1]['sea']+=$row[csf('sea_qnty')];
			$export_invoice_arr[$row[csf('po_id')]][2]['air']+=$row[csf('air_qnty')];
			$invoice_arr[$row[csf('invoice_no')]]['qty']+=$row[csf('invo_qnty')];
			$invoice_arr[$row[csf('invoice_no')]]['country_id']=$row[csf('country_id')];
			$invoice_arr[$row[csf('invoice_no')]]['po_id'].=$row[csf('po_id')].',';
			$invoice_arr[$row[csf('invoice_no')]]['shipping_mode']=$row[csf('shipping_mode')];
		}
		unset($sql_inv_result);
		$sql_inv=sql_select("select b.po_break_down_id as po_id,max(b.ex_factory_date) as factory_date,max(b.shiping_status) as shiping_status,
		sum(CASE WHEN   b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END) as exf_qnty,
		sum(CASE WHEN   b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as exf_ret_qnty
		from  pro_ex_factory_mst b where  b.status_active=1 and b.is_deleted =0 $po_cond_for_in4 group by b.po_break_down_id");
		$shiping_id=2;
		$export_invoice_arr=array();$tot_ship_qty=0;$job_ratio=0;
		foreach($sql_inv as $row)
		{
			$export_invoice_arr[$row[csf('po_id')]]['exf_qnty']+=$row[csf('exf_qnty')]-$row[csf('exf_ret_qnty')];
			$export_invoice_arr[$row[csf('po_id')]]['shiping_status']=$row[csf('shiping_status')];//shipment_status
			$tot_ship_qty+=$row[csf('exf_qnty')]-$row[csf('exf_ret_qnty')];
			
			$job_ratio=$po_data_arr_qty[$row[csf("po_id")]]['ratio'];
			if($row[csf('shiping_status')]==3) $shiping_id=3; 
		}
		unset($sql_inv);
										
		$sql_fab_book= "SELECT a.job_no,a.style_ref_no,b.id as po_id,b.po_number,c.booking_date,c.supplier_id,c.booking_no,c.is_short,c.pay_mode,c.currency_id,d.exchange_rate,d.trim_group,c.booking_type,c.entry_form,d.pre_cost_fabric_cost_dtls_id as fab_dtls_id,d.gmt_item,d.color_type,d.construction,d.process,d.copmposition,d.gsm_weight,d.uom,d.dia_width,d.grey_fab_qnty as grey_fab_qnty, d.fin_fab_qnty as fin_fab_qnty,d.rate as rate, (d.amount) as amount,d.wo_qnty from wo_po_details_master a, wo_po_break_down b, wo_booking_mst c,wo_booking_dtls d  where a.job_no=b.job_no_mst  and b.id=d.po_break_down_id and d.booking_no=c.booking_no and c.booking_type in(1,3,4,6,2)  and b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.amount>0  $company_name_cond  $job_no_cond $job_style_cond $order_cond $buyer_id_cond $year_cond order by c.booking_no,d.pre_cost_fabric_cost_dtls_id";
		//echo $sql_fab_book; die;
	$book_result=sql_select($sql_fab_book);
	$sum_total_fab_amount=0;$fabric_booking_arr=array(); $avg_cons=0;
	foreach($book_result as $rows)
	{
		$pay_mode=$rows[csf("pay_mode")];
		$is_short=$rows[csf("is_short")];
		$booking_type=$rows[csf("booking_type")];
		$booking_type=$rows[csf("booking_type")];$entry_form=$rows[csf("entry_form")];
		if($booking_type==1 || $booking_type==4)
		{
			if($pay_mode==3 || $pay_mode==5) //comp
			{
				//echo $rows[csf('supplier_id')].'DDD';
				$fab_booking_arr[$rows[csf('job_no')]]['comp_supp']=$company_library[$rows[csf('supplier_id')]];
			}
			else
			{
				$fab_booking_arr[$rows[csf('job_no')]]['comp_supp']=$supplier_library[$rows[csf('supplier_id')]];
			}
		}
		
		if($booking_type==1 || $booking_type==4)
		{
		 	$fab_booking_data_arr[$rows[csf("fab_dtls_id")]]['rate']=$rows[csf("rate")];
		 	$fab_booking_data_arr[$rows[csf("fab_dtls_id")]]['booking_fab_qty']+=$rows[csf("fin_fab_qnty")];
		  	$fab_booking_data_arr[$rows[csf("fab_dtls_id")]]['booking_fab_amt']+=$rows[csf("amount")];
		 if($is_short==2 && $booking_type==1) //Main
		 {
			  $fab_booking_data_arr[$rows[csf("fab_dtls_id")]]['booking_fab_qty_main']+=$rows[csf("fin_fab_qnty")];
		 }
		 if($is_short==1 && $booking_type==1) //short_fabric booking
		 {
			  $fab_booking_data_arr[$rows[csf("fab_dtls_id")]]['booking_fab_qty_short']+=$rows[csf("fin_fab_qnty")];
		 }
		 if($is_short==2 && $booking_type==4) //short_fabric booking
		 {
			  $fab_booking_data_arr[$rows[csf("fab_dtls_id")]]['booking_fab_qty_sample']+=$rows[csf("fin_fab_qnty")];
		 }
		// echo $rows[csf("fin_fab_qnty")].'d';
		}
		else if($booking_type==6 && $entry_form==201)
		{
			 $emblish_booking_data_arr[$rows[csf("fab_dtls_id")]]['rate']=$rows[csf("rate")];
			 $emblish_booking_data_arr[$rows[csf("fab_dtls_id")]]['booking_fab_qty']+=$rows[csf("wo_qnty")];
		}
		else if($booking_type==2 && $entry_form==87)//Trim Booking
		{
			if($rows[csf("rate")]>0)
			{
			 $trim_booking_data_arr[$rows[csf("fab_dtls_id")]]['rate']=$rows[csf("rate")]/$rows[csf("exchange_rate")];
			}
			// echo $rows[csf("rate")].'kkkk';
			 $trim_booking_data_arr[$rows[csf("fab_dtls_id")]]['booking_fab_qty']+=$rows[csf("wo_qnty")];
			$trim_booking_data_arr[$rows[csf("fab_dtls_id")]]['booking_amount']+=$rows[csf("amount")];
			$trim_group_booking_data_arr[$rows[csf("trim_group")]]['exchange_rate']=$rows[csf("exchange_rate")];
			$trim_group_booking_data_arr[$rows[csf("trim_group")]]['booking_amount']+=$rows[csf("amount")];
			$trim_group_booking_data_arr[$rows[csf("trim_group")]]['trim_booking_qnty']+=$rows[csf("wo_qnty")];
		}
		else if($booking_type==3)//Service Booking
		{
		//echo $rows[csf("fab_dtls_id")].'='.$rows[csf("process")].'<br>';
			$service_booking_data_arr[$rows[csf("process")]]['rate']=$rows[csf("rate")];
			 $service_booking_data_arr[$rows[csf("process")]]['booking_fab_qty']+=$rows[csf("wo_qnty")];
		}
	}
	unset($book_result);
				//print_r($service_booking_data_arr);
						
		/* $salesDataSql = "select c.job_no,a.id from wo_booking_dtls c,fabric_sales_order_mst a where  a.sales_booking_no=c.booking_no  and c.is_deleted=0 and c.status_active=1 and a.within_group in(1) $po_cond_for_in group by c.job_no,a.id";
		$salesDataResult = sql_select($salesDataSql);
		foreach ($salesDataResult as $row) {
			
			$job_no_from_sales_arr[$row[csf('mst_id')]]['job_no']=$row[csf('job_no')];
			$sales_mst_id.=$row[csf('mst_id')].',';
		}
		$sales_mst_id=rtrim($sales_mst_id,',');*/
		
			 $sql_knit_prod="select b.po_breakdown_id,c.body_part_id,c.fabric_description_id,
				sum(CASE WHEN b.entry_form  in(225,37) and b.trans_type in(1) and a.item_category=2  THEN  b.quantity ELSE 0 END) AS fin_qnty
				from inv_receive_master a, order_wise_pro_details b,pro_finish_fabric_rcv_dtls c where a.id=c.mst_id and c.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(225,37) and a.receive_basis in(10) and a.item_category in(2)  and b.trans_type in(1) $po_cond_for_in5 group by b.po_breakdown_id,c.body_part_id,c.fabric_description_id";
				$result_knit=sql_select( $sql_knit_prod );
				$knit_fin_qty_arr=array();
				foreach($result_knit as $row){
				
				$fabDesc=$row[csf("body_part_id")].$row[csf("fabric_description_id")];
				$knit_fin_qty_arr[$fabDesc]+=$row[csf("fin_qnty")];
				}
				unset($result_knit);
				//print_r($knit_fin_qty_arr);
				
				
				$condition= new condition();
				$condition->company_name("=$cbo_company_name");
				if(str_replace("'","",$cbo_buyer_name)>0){
					$condition->buyer_name("=$cbo_buyer_name");
				 }
				 if($txt_order_id!='' || $txt_order_id!=0)
				 {
					$condition->po_id("in($txt_order_id)"); 
				 }
				 if(str_replace("'","",$txt_job_no)!='')
				 {
					//echo "in($all_jobs)".'dd';die;
					$condition->job_no("in($all_jobs)");
				 }
				$condition->init();
				$emblishment= new emblishment($condition);
			
				$trims= new trims($condition);
				$wash= new wash($condition);
				$conversion= new conversion($condition);
			
				$trims_ReqQty_arr=$trims->getQtyArray_by_precostdtlsid();//getQtyArray_by_jobAndPrecostdtlsid();
				//print_r($trims_ReqQty_arr);
				$trims= new trims($condition);
				$trims_costing_arr=$trims->getAmountArray_precostdtlsid();//getAmountArray_by_jobAndPrecostdtlsid();
				//$trims_costing_arr=$trims->getQtytArray_precostdtlsid();//getAmountArray_by_jobAndPrecostdtlsid();
				//print_r($trims_ReqQty_arr);
				//echo $emblishment->getQuery(); die;
				$emblishment_costing_arr=$emblishment->getAmountArray_by_order();
				$emblishment_qty_arr=$emblishment->getQtyArray_by_emblishmentid();
				$emblishment_amount_arr=$emblishment->getAmountArray_by_emblishmentid();
			
				
				$conversion_costing_arr_process=$conversion->getAmountArray_by_jobAndConversionid();
				$conversion_qty_arr_process=$conversion->getQtyArray_by_jobAndConversionid();
				
				$emblishment_qty_wash_arr=$wash->getQtyArray_by_emblishmentid();
				$emblishment_amount_wash_arr=$wash->getAmountArray_by_emblishmentid();
				
				$commission= new commision($condition);
				$commission_costing_arr=$commission->getAmountArray_by_orderAndItemid();
				$fabric= new fabric($condition);
				$fabric_costing_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
				
				$fabric_qty_arr=$fabric->getQtyArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
				$fabric_amount_arr=$fabric->getAmountArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
				//echo $tot_ship_qty;
				
				     $sql_fab_arr= "select c.id,c.fabric_description,c.avg_cons,c.uom,c.rate,c.lib_yarn_count_deter_id as deter_id,c.job_no,c.body_part_id,c.body_part_type, sum(c.amount) as amount  from wo_pre_cost_fabric_cost_dtls c,wo_pre_cos_fab_co_avg_con_dtls d where c.job_id=c.job_id and  d.pre_cost_fabric_cost_dtls_id=c.id  $po_cond_for_in2 group by  c.id,c.color_type_id,c.fabric_description,c.avg_cons,c.body_part_id,c.job_no,c.uom,c.rate,c.item_number_id,c.body_part_type,c.uom,c.lib_yarn_count_deter_id order by c.id";
					 //echo $sql_fab_arr; die;
					 $fab_results=sql_select($sql_fab_arr);
					 $total_pre_amt=$total_actual_amt=$summ_total_pre_qty=$summ_total_booking_qty=$summ_total_knit_fin_qty=$summ_total_booking_qty=0;
					  foreach( $fab_results as $row )
					  {
						$item_name=$garments_item[$row[csf("item_number_id")]];
						$fab_desc=$body_part[$row[csf('body_part_id')]].','.$body_part_type[$row[csf('body_part_type')]].','.$row[csf('fabric_description')].','.$row[csf('gsm_weight')];
						$fab_dete_id=$row[csf('body_part_id')].$row[csf('deter_id')];
						$pre_fab_amt=$fabric_amount_arr['knit']['grey'][$row[csf("id")]][$row[csf("uom")]]+$fabric_amount_arr['woven']['grey'][$row[csf("id")]][$row[csf("uom")]];
						if($tot_ship_qty>0)
						{
						$pre_fab_qty=($tot_ship_qty/12)*$row[csf('avg_cons')];
						}//$fabric_qty_arr['knit']['grey'][$row[csf("id")]][$row[csf("uom")]]+$fabric_qty_arr['woven']['grey'][$row[csf("id")]][$row[csf("uom")]];
						//echo $pre_fab_qty.'<br>';
						
						 $fab_data_arr[$fab_desc][$row[csf("uom")]]['des']=$fab_desc;
						 $fab_data_arr[$fab_desc][$row[csf("uom")]]['booking_avg_rate']+= $fab_booking_data_arr[$row[csf("id")]]['booking_fab_amt']/$fab_booking_data_arr[$row[csf("id")]]['booking_fab_qty'];//booking_fab_amt
						 $fab_data_arr[$fab_desc][$row[csf("uom")]]['booking_qty']+= $fab_booking_data_arr[$row[csf("id")]]['booking_fab_qty'];
						 $fab_data_arr[$fab_desc][$row[csf("uom")]]['booking_qty_main']+= $fab_booking_data_arr[$row[csf("id")]]['booking_fab_qty_main'];
						 $fab_data_arr[$fab_desc][$row[csf("uom")]]['avg_rate']=$row[csf("rate")];//$pre_fab_amt/$pre_fab_qty;
						 $fab_data_arr[$fab_desc][$row[csf("uom")]]['pre_fab_qty']+=$pre_fab_qty;
						 $fab_data_arr[$fab_desc][$row[csf("uom")]]['pre_fab_amt']+=$pre_fab_amt;
						 $fab_data_arr[$fab_desc][$row[csf("uom")]]['knit_fin_qty']+=$knit_fin_qty_arr[$fab_dete_id];
						 $fab_uom_arr[$row[csf("id")]]=$row[csf("uom")];
						if($pre_fab_qty>0)
						{
						$total_pre_amt+=$pre_fab_qty*$row[csf("rate")];
						}
						if($knit_fin_qty_arr[$fab_dete_id]>0)
						{
						$total_actual_amt+=$knit_fin_qty_arr[$fab_dete_id]*$fab_booking_data_arr[$row[csf("id")]]['rate'];
						}
						//For Summary
						if($row[csf("uom")]==12)//Budget//Booking
						{
							$summ_total_pre_qty+=$pre_fab_qty;
							$summ_total_booking_qty+=$fab_booking_data_arr[$row[csf("id")]]['booking_fab_qty'];
							$summ_total_knit_fin_qty+=$knit_fin_qty_arr[$fab_dete_id];
						}
						
						
					  }
					  //echo $summ_total_pre_qty;
					  unset($fab_results);
					  // left join wo_pre_cost_fabric_cost_dtls d on c.job_no=d.job_no and c.fabric_description=d.id
					  $sql_conv="select c.id as id,c.fabric_description as pre_costdtl_id, a.job_no, c.cons_process,(c.req_qnty) as req_qnty,c.avg_req_qnty,(c.charge_unit) as charge_unit,(c.amount) as amount,c.color_break_down,a.order_uom from wo_po_details_master a,wo_pre_cost_fab_conv_cost_dtls c  where c.job_no=a.job_no  and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.job_no in($all_jobs)   $company_name_cond  $job_style_cond   $year_cond $buyer_id_cond order by c.id";
				  
					$result_conv=sql_select($sql_conv);
					$total_pre_conver_cost=$total_actual_conver_cost=0;
					foreach($result_conv as $row)
					{
						 $cons_process =$conversion_cost_head_array[$row[csf("cons_process")]];
						$conv_amount=array_sum($conversion_costing_arr_process[$row[csf("job_no")]][$row[csf("id")]]);
						
						
						if($conv_amount>0)
						{
							$conv_rowspan+=1;
							$conv_detail_arr[$row[csf("cons_process")]]['emb_name']=$row[csf("emb_name")];
							$conv_detail_arr[$row[csf("cons_process")]]['order_uom']=$fab_uom_arr[$row[csf("pre_costdtl_id")]];
							$conv_detail_arr[$row[csf("cons_process")]]['cons_dzn_gmts']=$row[csf("avg_req_qnty")];
							$conv_detail_arr[$row[csf("cons_process")]]['amount']=$row[csf("amount")];
							$conv_detail_arr[$row[csf("cons_process")]]['pre_conv_rate']=$row[csf("charge_unit")];
							$conv_detail_arr[$row[csf("cons_process")]]['booking_conv_qty']+=$service_booking_data_arr[$row[csf("cons_process")]]['booking_fab_qty'];
							$conv_detail_arr[$row[csf("cons_process")]]['booking_conv_rate']=$service_booking_data_arr[$row[csf("cons_process")]]['rate'];
							$conv_detail_arr[$row[csf("cons_process")]]['job_no'].=$row[csf("job_no")].',';
							$conv_detail_arr[$row[csf("cons_process")]]['desc']=$cons_process;
							$conv_detail_arr[$row[csf("cons_process")]]['pre_conv_amount']+=$conv_amount;
							$conv_detail_arr[$row[csf("cons_process")]]['pre_embl_qty']+=array_sum($conversion_qty_arr_process[$row[csf("job_no")]][$row[csf("id")]]);
							//$emblishment_qty_arr
							if($tot_ship_qty>0)
							{
							$summ_budgeted_qty=($tot_ship_qty/12)*$row[csf("avg_req_qnty")];
							}
							if($order_qty_pcs>0)
							{
							$summ_actual_conv_qty=($order_qty_pcs/12)*$row[csf("avg_req_qnty")];
							}
							if($summ_budgeted_qty>0)
							{
							$total_pre_conver_cost+=$summ_budgeted_qty*$row[csf("charge_unit")];
							}
							if($summ_actual_conv_qty>0)
							{
							$total_actual_conver_cost+=$summ_actual_conv_qty*$service_booking_data_arr[$row[csf("cons_process")]]['rate'];
							}
						}
					}
					
					 $sql_emblish="select a.order_uom,c.id, c.job_no, c.emb_name,c.emb_type,c.cons_dzn_gmts,c.rate, c.amount from wo_po_details_master a, wo_pre_cost_embe_cost_dtls c  where  c.job_no=a.job_no  and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.cons_dzn_gmts>0 $company_name_cond  $job_style_cond $order_cond $year_cond $buyer_id_cond   order by c.id";
				  
					$result_emblish=sql_select($sql_emblish);
					$emblish_detail_arr=array();$total_pre_emblish_cost=$total_actual_emblish_cost=$total_actual_emblish_cost2=0;
					//$type_array=array(0=>$blank_array,1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$emblishment_gmts_type,6=>$blank_array,99=>$emblishment_other_type_arr);
					
					
					
					foreach($result_emblish as $row)
					{
						$emb_type=$row[csf("emb_type")];
						$emb_name_id=$row[csf("emb_name")];
						if($emb_name_id==1) //Print type
						{
							if($emb_type>0) $emb_typeCond=$emblishment_print_type[$emb_type];else $emb_typeCond="";
							//$emb_name=$emblishment_name_array[$row[csf('emb_name')]].$emb_typeCond;
						}
						else if($emb_name_id==2) //embro type
						{
							if($emb_type>0) $emb_typeCond=$emblishment_embroy_type_arr[$emb_type];else $emb_typeCond="";
							//$emb_name=$emblishment_name_array[$row[csf('emb_name')]].$emb_typeCond;
							 //echo $emb_type.'<br>';
						}
						else if($emb_name_id==4) //Special type
						{
							if($emb_type>0) $emb_typeCond=$emblishment_spwork_type[$emb_type];else $emb_typeCond="";
							//$emb_name=$emblishment_name_array[$row[csf('emb_name')]].$emb_typeCond;
						}
						else if($emb_name_id==5) //GMT type
						{
							if($emb_type>0) $emb_typeCond=$emblishment_gmts_type[$emb_type];else $emb_typeCond="";
							//$emb_name=$emblishment_name_array[$row[csf('emb_name')]].$emb_typeCond;
						}
						else if($emb_name_id==3) //Wash type
						{
							if($emb_type>0) $emb_typeCond=$emblishment_wash_type[$emb_type];else $emb_typeCond="";
							//$emb_name=$emblishment_name_array[$row[csf('emb_name')]].$emb_typeCond;
						}
						
						$item_descrition =$row[csf("description")];
						$emblishment_amount=$emblishment_amount_arr[$row[csf("id")]];
						$emblishment_amount_wash=$emblishment_amount_wash_arr[$row[csf("id")]];
						$emb_name_str=$row[csf("emb_name")].'__'.$emb_typeCond;
						//echo $emblishment_amount.'DDDDD';//emblishment_qty_wash_arr
						if($emblishment_amount>0 || $emblishment_amount_wash>0)
						{
							$embl_rowspan+=1;
							$emblish_detail_arr[$emb_name_str]['emb_name']=$row[csf("emb_name")];
							$emblish_detail_arr[$emb_name_str]['emb_type']=$row[csf("emb_type")];
							$emblish_detail_arr[$emb_name_str]['order_uom']=$row[csf("order_uom")];
							$emblish_detail_arr[$emb_name_str]['cons_dzn_gmts']=$row[csf("cons_dzn_gmts")];
							$emblish_detail_arr[$emb_name_str]['amount']=$row[csf("amount")];
							if($row[csf("amount")]>0)
							{
							$emblish_detail_arr[$emb_name_str]['pre_embl_rate']=$row[csf("amount")]/$row[csf("cons_dzn_gmts")];
							}
							$emblish_detail_arr[$emb_name_str]['booking_emblish_qty']=$emblish_booking_data_arr[$row[csf("id")]]['booking_fab_qty'];
							$emblish_detail_arr[$emb_name_str]['booking_embl_rate']=$emblish_booking_data_arr[$row[csf("id")]]['rate'];
							$emblish_detail_arr[$emb_name_str]['job_no'].=$row[csf("job_no")].',';
							$emblish_detail_arr[$emb_name_str]['desc']=$item_descrition;
							$emblish_detail_arr[$emb_name_str]['pre_embl_amount']+=$emblishment_amount_arr[$row[csf("id")]]+$emblishment_amount_wash[$row[csf("id")]];
							$emblish_detail_arr[$emb_name_str]['pre_embl_qty']+=$emblishment_qty_arr[$row[csf("id")]]+$emblishment_qty_wash_arr[$row[csf("id")]];
						//	echo $emblishment_qty_arr[$row[csf("id")]]+$emblishment_qty_wash_arr[$row[csf("id")]].'<br>';
							//$emblishment_qty_arr
							if($tot_ship_qty>0)
							{
							$summ_budgeted_qty=($tot_ship_qty/12)*$row[csf("cons_dzn_gmts")];
							}
							if($order_qty_pcs>0)
							{
							$summ_actual_emblish_qty=($order_qty_pcs/12)*$row[csf("cons_dzn_gmts")];
							}
							if($summ_budgeted_qty>0)
							{
							$total_pre_emblish_cost+=$summ_budgeted_qty*$row[csf("rate")];
							}

							// $total_actual_emblish_cost+=$summ_actual_emblish_qty*$emblish_booking_data_arr[$row[csf("id")]]['rate'];
							if($emblish_booking_data_arr[$row[csf("id")]]['rate']>0)
							{
							$total_actual_emblish_cost+=$emblish_booking_data_arr[$row[csf("id")]]['booking_fab_qty']*$emblish_booking_data_arr[$row[csf("id")]]['rate'];
							}
						
						}
					}
					//echo $total_actual_emblish_cost.'DD';
					
					unset($result_emblish);
					$sql_trim_recv="select a.currency_id,a.exchange_rate,b.po_breakdown_id,c.item_group_id,c.rate,c.amount,
					(CASE WHEN b.entry_form  in(24) and b.trans_type in(1) and a.item_category=4  THEN  b.quantity ELSE 0 END) AS trim_recv_qnty
					from inv_receive_master a, order_wise_pro_details b,inv_trims_entry_dtls c where a.id=c.mst_id and c.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(24) and a.item_category in(4)  and b.trans_type in(1) $po_cond_for_in5 order by c.item_group_id";
					$result_trim_recv=sql_select( $sql_trim_recv );
					$trim_recv_qty_arr=array();$total_summ_trim_recv_cost=0;
					foreach($result_trim_recv as $row){
					$currency_id=$row[csf("currency_id")];
					//$fabDesc=$row[csf("item_group_id")].$row[csf("fabric_description_id")];
					$trim_recv_qty_arr[$row[csf("item_group_id")]]['trim_recv_qnty']+=$row[csf("trim_recv_qnty")];
						if($currency_id==1) //TK
						{
							if($row[csf("amount")]>0)
							{
							$trim_recv_qty_arr[$row[csf("item_group_id")]]['trim_recv_amount']+=$row[csf("amount")]/$row[csf("exchange_rate")];
							}
							if($row[csf("rate")]>0)
							{
							$trim_recv_qty_arr[$row[csf("item_group_id")]]['trim_recv_rate']=$row[csf("rate")]/$row[csf("exchange_rate")];
							}
							//$total_summ_trim_recv_cost+=$row[csf("amount")]/$row[csf("exchange_rate")];
						}
						else
						{
							$trim_recv_qty_arr[$row[csf("item_group_id")]]['trim_recv_amount']+=$row[csf("amount")];
							$trim_recv_qty_arr[$row[csf("item_group_id")]]['trim_recv_rate']=$row[csf("rate")];
							//$total_summ_trim_recv_cost+=$row[csf("amount")];
						}
					
					
					}
				
					$sql_trims="select c.id,c.trim_group,c.description,c.cons_uom, c.nominated_supp,(c.amount) as amount,(c.cons_dzn_gmts) as cons_qty,(c.rate) as rate from wo_po_details_master a, wo_pre_cost_trim_cost_dtls c  where c.job_no=a.job_no  and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_name_cond  $job_no_cond $job_style_cond $order_cond $buyer_id_cond $year_cond   order by c.trim_group";
					$result_trims=sql_select($sql_trims);$total_summ_pre_trim_cost=0;
					$trims_detail_arr=array();
					foreach($result_trims as $row)
					{
						$item_descrition =$row[csf("description")];
						$trims_rowspan+=1;
						$trims_detail_arr[$row[csf("trim_group")]][$row[csf("description")]]['nominated_supp']=$row[csf("nominated_supp")];
						$trims_detail_arr[$row[csf("trim_group")]][$row[csf("description")]]['order_uom']=$row[csf("cons_uom")];
						$trims_detail_arr[$row[csf("trim_group")]][$row[csf("description")]]['trim_group']=$row[csf("trim_group")];
						$trims_detail_arr[$row[csf("trim_group")]][$row[csf("description")]]['amount']=$row[csf("amount")];
						$trims_detail_arr[$row[csf("trim_group")]][$row[csf("description")]]['cons_qty']=$row[csf("cons_qty")];
						$trims_detail_arr[$row[csf("trim_group")]][$row[csf("description")]]['trim_rate']=$row[csf("rate")];
						
						
						$trimBooking_exchange_rate=$trim_group_booking_data_arr[$row[csf("trim_group")]]['exchange_rate'];
						$tot_trim_amount=$trim_group_booking_data_arr[$row[csf("trim_group")]]['booking_amount'];
						$tot_trim_qty=$trim_group_booking_data_arr[$row[csf("trim_group")]]['trim_booking_qnty'];
						if($tot_trim_amount>0)
						{
						$trim_booking_avg_rate=$tot_trim_amount/$tot_trim_qty;
						}
						else $trim_booking_avg_rate=0;
						//echo $tot_trim_amount.'='.$tot_trim_qty.'<br>';
						if($trim_booking_avg_rate>0)
						{
						$trims_detail_arr[$row[csf("trim_group")]][$row[csf("description")]]['booking_trim_rate']=$trim_booking_avg_rate/$trimBooking_exchange_rate;
						}
						$trims_detail_arr[$row[csf("trim_group")]][$row[csf("description")]]['pre_req_qty']+=$trims_ReqQty_arr[$row[csf("id")]];
						//$trim_booking_data_arr[$row[csf("id")]]['rate']
						$trims_detail_arr[$row[csf("trim_group")]][$row[csf("description")]]['trim_booking_req_qty']+=$trim_booking_data_arr[$row[csf("id")]]['booking_fab_qty'];
						$trims_detail_arr[$row[csf("trim_group")]][$row[csf("description")]]['desc']=$item_descrition;
						
						//$conv_detail_process_arr[$row[csf("cons_process")]]=$row[csf("cons_process")];
						$pre_cons_qty=($tot_ship_qty/12)*$row[csf("cons_qty")];
						$total_summ_pre_trim_cost+=$pre_cons_qty*$row[csf("rate")];
						// echo $trim_booking_data_arr[$row[csf("id")]]['booking_fab_qty'].'='.$trim_recv_qty_arr[$row[csf("trim_group")]]['trim_recv_rate'].'<br/>';
						if($trim_booking_data_arr[$row[csf("id")]]['booking_fab_qty'] >0 && ($trim_booking_avg_rate/$trimBooking_exchange_rate)>0){
							$total_summ_trim_recv_cost+=$trim_booking_data_arr[$row[csf("id")]]['booking_fab_qty']*($trim_booking_avg_rate/$trimBooking_exchange_rate);
						}
						
						//echo $pre_cons_qty*$row[csf("rate")].'<br/>';
						
					}
					//echo $total_summ_trim_recv_cost.'=';
					unset($result_trims);	
					$fabriccostSqlArray="select a.set_smv,c.job_no,c.margin_dzn, c.costing_per_id,c. embel_cost, c.wash_cost,c.cm_cost, c.commission, c.currier_pre_cost, c.lab_test, freight from wo_po_details_master a,wo_pre_cost_dtls c where a.job_no=c.job_no and c.status_active=1 and c.is_deleted=0 and c.job_no in($all_jobs) $job_style_cond $year_cond $buyer_id_cond  ";
					$fabriccostDataArray=sql_select($fabriccostSqlArray);
					$summ_cm_cost=$set_smv=$currier_pre_cost=$margin_dzn=$freight=0;
				foreach($fabriccostDataArray as $fabRow)
				{					
					 $summ_cm_cost=$fabRow[csf('cm_cost')]; $set_smv=$fabRow[csf('set_smv')];
					 $currier_pre_cost=$fabRow[csf('currier_pre_cost')];
					 $freight=$fabRow[csf('freight')]; $margin_dzn=$fabRow[csf('margin_dzn')];					
				}
				$asking_profit=sql_select("select id, company_id, applying_period_date, applying_period_to_date, cost_per_minute from lib_standard_cm_entry where status_active=1 and is_deleted=0 and company_id=$cbo_company_name");
			//echo "select id, company_id, applying_period_date, applying_period_to_date, asking_profit, max_profit from lib_standard_cm_entry where status_active=1 and is_deleted=0 and company_id=$company_name";
			foreach($asking_profit as $ask_row )
			{
				$applying_period_date=change_date_format($ask_row[csf('applying_period_date')],'','',1);
				$applying_period_to_date=change_date_format($ask_row[csf('applying_period_to_date')],'','',1);
				$diff=datediff('d',$applying_period_date,$applying_period_to_date);
				for($j=0;$j<$diff;$j++)
				{
					//$newdate =change_date_format(add_date(str_replace("'","",$applying_period_date),$j),'','',1);
					$date_all=add_date(str_replace("'","",$applying_period_date),$j);
				    $newdate =change_date_format($date_all,'','',1);
					$cpm_arr[$newdate]['cost_per_minute']=$ask_row[csf('cost_per_minute')];
					
				}
			}
			unset($fabriccostDataArray);	
			$sql_sewing="select c.production_quantity, c.production_type,c.po_break_down_id from pro_garments_production_mst c where c.production_type=5 $po_cond_for_in";
			$result_sewing=sql_select($sql_sewing);
			$tot_sewing_output=0;
			foreach( $result_sewing as $row)
			{
					$tot_sewing_output+=$row[csf('production_quantity')];
			}
					
				$style1="#E9F3FF"; 
				$style="#FFFFFF";
	?>
   
       
        <div style="margin-left:10px; width:1000px">
		  <table width="850px" >
             <tr class="form_caption">
                  
					<td align="center"  colspan="8" class="form_caption"><strong style=" font-size:x-large"><? echo $company_library[$cbo_company_name].'<br>'; ?></strong></td>
                </tr>
                <tr>
                      <td colspan="8" align="center"><strong style=""><? echo $report_title;?></strong></td>
                </tr>
            </table>
             <table width="850" class="rpt_table" cellpadding="0" cellspacing="0" border="2" rules="all" id="table_header_1">
                
             <tr>
             <td style="border:none">
            	<table width="600"  class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <tr  bgcolor="<? echo $style; ?>">
                        <td width="120"> <strong>Buyer</strong> </td> 
                        <td width=""><? if($cbo_buyer_name==0) echo implode(",",array_unique(explode(",",$all_buyer)));else echo $buyer_arr[$cbo_buyer_name];?> </td>
                    </tr>
					 <tr  bgcolor="<? echo $style1; ?>">
					  <td width="140" ><strong>Job No</strong></td> 
                        <td width=""><p><? echo $job_no;	?> </p></td>
					</tr>
                     <tr  bgcolor="<? echo $style1; ?>">
					  <td width="140" ><strong>Order Number</strong></td> 
                        <td width=""><p><? echo $all_po_no;	?> </p></td>
					</tr>
					<tr  bgcolor="<? echo $style; ?>">
                      <td width="120"><strong>Style Ref.</strong></td> 
                      <td width=""><? echo implode(",",array_unique(explode(",",$all_style)));?></td>
                    </tr>
                     <tr  bgcolor="<? echo $style1; ?>">
					  <td width="140"> <strong>Unit Value</strong> </td> 
                       <td width=""><? echo number_format($total_job_unit_price,4).' USD';?></td>
					</tr>
					<tr  bgcolor="<? echo $style1; ?>">
                        <td width="140"><strong>Style Value</strong></td> 
                        <td width=""><? echo number_format($total_job_unit_price*$order_qty_pcs,2).' USD'; ?></td>
					</tr>
					  <tr  bgcolor="<? echo $style; ?>">
                        <td width="140"><strong> Total Order Qty</strong> </td> 
                        <td width=""><? echo $order_qty_pcs.' Pcs';?></td>
                    </tr>
					
                    <tr  bgcolor="<? echo $style1; ?>">
                        <td width="100"><strong>Invoice Value</strong></td> 
                        <td title="Job Avg Rate*Total Po qty Pcs)"><? $invoice_value=$tot_ship_qty*$total_job_unit_price;echo number_format($invoice_value,2).' USD';?></td>
					</tr>
                     <tr  bgcolor="<? echo $style; ?>">
                        <td width="100"><strong>Total Shipped Qty:</strong></td> 
                        <td><? echo  $tot_ship_qty.' Pcs';?></td>
						</tr>
						<tr  bgcolor="<? echo $style1; ?>">
                         <td width="140"> <b>Shipment Status : </b></td>
                          <td >  <? echo $shipment_status[$shiping_id];?>
                         </td>
                    </tr>
                       
                </table> 
             </td>
             <td   width="450" height="50px" valign="middle">
                   <table width="100%"   cellpadding="0" class="rpt_table"  rules="all" cellspacing="0" border="1">
                        <tr>
                        	<td align="left"> <strong>Product department:</strong></td>
                            <td  align="left"> <strong><? echo $product_depertment;?></strong></td>
                        </tr>
                        <tr>
                        	<td> <strong>Items Name:</strong> </td>
                            <td  align="left"><? 
							$gmts_item=''; $gmts_item_id=explode(",",$gmts_item_id);
							foreach($gmts_item_id as $item_id)
							{
								//echo $item_id;
								if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
							}
							
							
							echo $gmts_item;?> </td>
                        </tr>
                         <tr>
                        	<td> <strong>Merchandiser: </strong></td>
                            <td  align="left"><? echo $team_leader;?> </td>
                        </tr>
                         <tr>
                        	<td> <strong>Fab. Supplier:</strong> </td>
                            <td  align="left"><? echo $fab_booking_arr[$job_no]['comp_supp'];?> </td>
                        </tr>
                        
                         <tr bgcolor="#CCCCCC">
                        	<td> <strong>Conversion Rate:</strong> </td>
                            <td  align="right"><? echo $exchange_rate;?></td>
                        </tr>
                        
                      </table>
					  <table style=" float:left" class="rpt_table" width="400" cellpadding="0" cellspacing="0" border="1" rules="all">
						<thead>
						<tr>
							<th width="100">Country</th>
							<th width="100">Invoice</th>
							<th width="70">Order Qty</th>
							<th width="70"> Ship. Qty </th>
							<th> Ship Mode </th>
						</tr>
						</thead>
						<tbody>
							<?
							$k=1;$tot_inv_po_qty=$total_ship_qty=0;
							foreach($invoice_arr as $invo_no=>$row)
							{
							
							//$country_qty=$country_arr[$row['country_id']]['order_quantity'];
							$po_id=rtrim($row['po_id'],',');
							$po_ids=array_unique(explode(",",$po_id));
							$po_qty=0;
							foreach($po_ids as $pid)
							{
								$po_qty+=$po_data_arr[$pid]['po_qty'];
							}
							?>
							<tr>
								<td> <? echo $country_name_arr[$row['country_id']];?></td>
								<td> <? echo $invo_no;?></td>
								<td align="right"> <? echo number_format($po_qty,0);?></td>
								<td align="right"> <? echo number_format($row['qty'],0);?></td>
								<td> <? echo $shipment_mode[$row['shipping_mode']];?></td>
							</tr>
							<?
								$k++;
								$tot_inv_po_qty+=$po_qty;
								$total_ship_qty+=$row['qty'];
							}
							?>
							<tfoot>
								<tr>
								<th colspan="2">&nbsp; </th>
								<th align="right"> <? //echo number_format($tot_inv_po_qty,0);?> </th>
								<th align="right"> <? echo number_format($total_ship_qty,0);?> </th>
								<th>&nbsp; </th>
								</tr>
							</tfoot>
						</tbody>
					</table>
             </td>
			 
                </tr>
            </table>
            <br/>
			<table class="rpt_table" width="1000" cellpadding="0" cellspacing="0" border="1" rules="all">
			<thead>
				<tr>
				<th>Summary Report</th>
				<th>Budg.</th>
				<th>Bk.&EPM </th>
				<th>Act.&CPM </th>
				<th colspan="2" >Cost in USD	</th>
				<th colspan="2">Variance in </th>
				<th colspan="2">Cost Percentage</th>
				</tr>
				<tr>
				<th>Elements</th>
				<th>Kg/Dzn</th>
				<th>(Kg/Dzn) </th>
				<th>(Kg/Dzn) </th>
				<th> Budgeted </th>
				<th> Actual</th>
				<th> USD </th>
				<th>(%)</th>
				<th> Budgeted </th>
				<th>Actual</th>
				</tr>
				<tbody>
					<tr  bgcolor="<? echo $style; ?>">
						<td>Fabric </td>
						<td align="right" title="<? echo "Total Budget Qty-Kg(".$summ_total_pre_qty.")/Total Shipped Qty".$tot_ship_qty;?>*12">
						 <? if($summ_total_pre_qty>0 && $tot_ship_qty>0) echo number_format(($summ_total_pre_qty/$tot_ship_qty)*12,2); ?> </td>
						<td align="right" title="<? echo "Total Booking  Qty(".$summ_total_booking_qty.")/Total Shipped Qty".$tot_ship_qty;?>*12">
						<? if($summ_total_booking_qty>0  && $tot_ship_qty>0)  echo number_format(($summ_total_booking_qty/$tot_ship_qty)*12,2); ?> </td>
						
						<td align="right" title="<? echo "Total Actual  Qty(".$summ_total_knit_fin_qty.")/Total Shipped Qty".$tot_ship_qty;?>*12">
						<? if($summ_total_knit_fin_qty>0  && $tot_ship_qty>0)  echo number_format(($summ_total_knit_fin_qty/$tot_ship_qty)*12,2); ?> </td>
						
						<td  align="right" title="<? echo "Total Budget Cost" ?>"><? echo number_format($total_pre_amt,2); ?>  </td> 
						<td  align="right" title="<? echo "Total Actual Cost" ?>" ><? echo number_format($total_actual_amt,2); ?>  </td>
						<td  align="right" title="Budget Cost-Actual Cost" ><?  $variance_usd_in=$total_pre_amt-$total_actual_amt;
						if($total_pre_amt>$total_actual_amt) echo number_format($variance_usd_in,0).' F';
						else  if($total_actual_amt>$total_pre_amt) echo number_format(abs($variance_usd_in),0).' UF';
						else if($total_actual_amt==$total_pre_amt) echo ''; ?>  </td>
						<td  align="right"  title="Variance USD/Budget Cost*100"><? if($variance_usd_in>0) echo number_format(($variance_usd_in/$total_pre_amt)*100,2).' %'; ?>  </td>
						<td  align="right" title="Budget Cost/Invoice Value*100"><? if($total_pre_amt>0)  echo  number_format((($total_pre_amt/$invoice_value)*100),2).' %'; ?>  </td>
						<td  align="right" title="Actual Cost/Invoice Value*100"><? if($total_actual_amt>0)  echo  number_format((($total_actual_amt/$invoice_value)*100),2).' %'; ?>  </td>
						<?
						//$total_remaining_actual_amt=$total_actual_amt;
						?>
					</tr>
					<tr  bgcolor="<? echo $style1; ?>">
						<td>Print/Embroidary/Wash/Others </td>
						<td align="right" title="">&nbsp;</td>
						<td align="right" title="">&nbsp; </td>
						<td align="right" title="">&nbsp;</td>
						<td  align="right" title="<? echo "Total Budget Cost" ?>"><? echo number_format($total_pre_emblish_cost,2); ?>  </td> 
						<td  align="right" title="<? echo "Total Actual Cost" ?>" ><? echo number_format($total_actual_emblish_cost,2); ?>  </td>
						<td  align="right" title="Budget Cost-Actual Cost" ><?  $embl_variance_usd_in=$total_pre_emblish_cost-$total_actual_emblish_cost;
						if($total_pre_emblish_cost>$total_actual_emblish_cost) echo number_format($embl_variance_usd_in,0);
						else  if($total_actual_emblish_cost>$total_pre_emblish_cost) echo number_format(abs($embl_variance_usd_in),0);
						else if($total_pre_emblish_cost==$total_actual_emblish_cost) echo '';
						//echo number_format($embl_variance_usd_in,2); ?>  </td>
						<td  align="right"  title="Variance USD/Budget Cost*100"><? if($embl_variance_usd_in>0) echo number_format(($embl_variance_usd_in/$total_pre_emblish_cost)*100,2).' %'; ?>  </td>
						<td  align="right" title="Budget Cost/Invoice Value*100"><? if($total_pre_emblish_cost>0) echo  number_format((($total_pre_emblish_cost/$invoice_value)*100),2).' %'; ?>  </td>
						<td  align="right" title="Actual Cost/Invoice Value*100"><?  if($total_actual_emblish_cost>0) echo  number_format((($total_actual_emblish_cost/$invoice_value)*100),2).' %'; ?>  </td>
						
					</tr>
					<tr  bgcolor="<? echo $style1; ?>">
						<td>Conversion Cost Fabric </td>
						<td align="right" title="">&nbsp;</td>
						<td align="right" title="">&nbsp; </td>
						<td align="right" title="">&nbsp;</td>
						<td  align="right" title="<? echo "Total Budget Cost" ?>"><? echo number_format($total_pre_conver_cost,2); ?>  </td> 
						<td  align="right" title="<? echo "Total Actual Cost" ?>" ><? echo number_format($total_actual_conver_cost,2); ?>  </td>
						<td  align="right" title="Budget Cost-Actual Cost" ><?  $conv_variance_usd_in=$total_pre_conver_cost-$total_actual_conver_cost;
						if($total_pre_conver_cost>$total_actual_conver_cost) echo number_format($conv_variance_usd_in,0).' F';
						else  if($total_actual_conver_cost>$total_pre_conver_cost) echo number_format(abs($conv_variance_usd_in),0).' UF';
						else if($total_pre_emblish_cost==$total_actual_conver_cost) echo '';
						//echo number_format($embl_variance_usd_in,2); ?>  </td>
						<td  align="right"  title="Variance USD/Budget Cost*100"><? if($conv_variance_usd_in>0) echo number_format(($conv_variance_usd_in/$total_pre_conver_cost)*100,2).' %'; ?>  </td>
						<td  align="right" title="Budget Cost/Invoice Value*100"><? if($total_pre_conver_cost>0) echo  number_format((($total_pre_conver_cost/$invoice_value)*100),2).' %'; ?>  </td>
						<td  align="right" title="Actual Cost/Invoice Value*100"><?  if($total_actual_conver_cost>0) echo  number_format((($total_actual_conver_cost/$invoice_value)*100),2).' %'; ?>  </td>
						
					</tr>
					<tr  bgcolor="<? echo $style; ?>">
						<td>Accessories </td>
						<td align="right">
						<? //echo number_format(($total_pre_emblish_cost/$tot_ship_qty)*12,2); ?> </td>
						<td align="right" >
						<? //echo number_format(($summ_total_booking_qty/$tot_ship_qty)*12,2); ?> </td>
						
						<td align="right" >
						<? //echo number_format(($summ_total_knit_fin_qty/$tot_ship_qty)*12,2); ?> </td>
						
						<td  align="right" title="<? echo "Total Budget Cost=((shipQnty/12)*cons_qty)*rate" ?>"><? echo number_format($total_summ_pre_trim_cost,2); ?>  </td> 
						<td  align="right" title="<? echo "Total Actual Cost" ?>" ><? echo fn_number_format($total_summ_trim_recv_cost,2); ?>  </td>
						<td  align="right" title="Budget Cost-Actual Cost" ><?  $trim_variance_usd_in=$total_summ_pre_trim_cost-$total_summ_trim_recv_cost;
						if($total_summ_pre_trim_cost>$total_summ_trim_recv_cost) echo number_format($trim_variance_usd_in,0).' F';
						else  if($total_summ_trim_recv_cost>$total_summ_pre_trim_cost) echo number_format(abs($trim_variance_usd_in),0).' UF';
						else if($total_summ_trim_recv_cost==$total_summ_pre_trim_cost) echo '';
						//echo number_format($trim_variance_usd_in,2); ?>  </td>
						<td  align="right"  title="Variance USD/Budget Cost*100"><? if($trim_variance_usd_in>0) echo number_format(($trim_variance_usd_in/$total_summ_pre_trim_cost)*100,2).' %'; ?>  </td>
						<td  align="right" title="Budget Cost/Invoice Value"><? if($total_summ_pre_trim_cost>0) echo  number_format((($total_summ_pre_trim_cost/$invoice_value)*100),2).' %'; ?>  </td>
						<td  align="right" title="Actual Cost/Invoice Value"><? if($total_summ_trim_recv_cost>0) echo  number_format((($total_summ_trim_recv_cost/$invoice_value)*100),2).' %'; ?>  </td>
						
					</tr>
					<tr  bgcolor="<? echo $style1; ?>">
						<td>Cost of Making (CM) </td>
						<td align="right">
						<? 
						$ship_newdate =change_date_format($all_last_pub_date,'','',1);
						$cost_per_minute=$cpm_arr[$ship_newdate]['cost_per_minute'];
						
						$target_prod_min=$order_qty_pcs*$set_smv;
						$actual_prod_min=$tot_sewing_output*$set_smv;
						if($tot_ship_qty>0)
						{
						$budgeted_cm=($tot_ship_qty/12)*$summ_cm_cost;
						}
						else $budgeted_cm=0;
						 $tot_makingcost_of_cm=$cost_per_minute*$actual_prod_min;
						
						//echo number_format(($total_pre_emblish_cost/$tot_ship_qty)*12,2); ?> </td>
						<td align="right" title="Budgeted CM/Actual Prod. Min*Exchange Rate" >
							&#2547;<? if($budgeted_cm>0) $making_cm_bk_epm=($budgeted_cm/$actual_prod_min)*$exchange_rate;
						
						echo number_format($making_cm_bk_epm,2); ?> </td>
						
						<td align="right" title="Actual CM Cost/Actual Prod Min*Exchange Rate">
							&#2547;<? $act_cpm_cost_cm=($tot_makingcost_of_cm/$actual_prod_min)*$exchange_rate;if($tot_makingcost_of_cm>0) echo number_format($act_cpm_cost_cm,2); ?> </td>
						
						<td  align="right" title="Budget CM"><? echo number_format($budgeted_cm,2); ?>  </td> 
						<td  align="right"  title="Total Making Cost" ><? echo number_format($tot_makingcost_of_cm,2); ?>  </td>
						<td  align="right" title="Budget Cost-Actual Cost" >  <?  $cm_variance_usd_in=$budgeted_cm-$tot_makingcost_of_cm;
						if($budgeted_cm>$tot_makingcost_of_cm) echo number_format($cm_variance_usd_in,0).' F';
						else  if($tot_makingcost_of_cm>$budgeted_cm) echo  number_format(abs($cm_variance_usd_in)).' UF';
						else if($tot_makingcost_of_cm==$budgeted_cm) echo '';
						
						//echo number_format($cm_variance_usd_in,2); ?>  </td>
						<td  align="right"  title="Variance USD/Budget Cost*100"><?  if($cm_variance_usd_in>0) echo number_format(($cm_variance_usd_in/$budgeted_cm)*100,2).' %'; ?>  </td>
						<td  align="right" title="Budget Cost/Invoice Value*100"><? if($budgeted_cm>0) echo  number_format((($budgeted_cm/$invoice_value)*100),2).' %'; ?>  </td>
						<td  align="right" title="Actual Cost/Invoice Value*100"><? if($tot_makingcost_of_cm>0) echo  number_format((($tot_makingcost_of_cm/$invoice_value)*100),2).' %'; ?>  </td>
						
					</tr>
					<tr  bgcolor="<? echo $style1; ?>">
						<td>Currier Cost </td>
						<td align="right">
						<? //echo number_format(($total_pre_emblish_cost/$tot_ship_qty)*12,2); ?> </td>
						<td align="right" >
						<? //echo number_format(($summ_total_booking_qty/$tot_ship_qty)*12,2); ?> </td>
						
						<td align="right" >
						<? //echo number_format(($summ_total_knit_fin_qty/$tot_ship_qty)*12,2);
						 $budgeted_currier= ($tot_ship_qty/12)*$currier_pre_cost;
						 $currier_actual_cost=0;
						 ?> </td>
						
						<td  align="right" title="<? echo "Total Ship Qty/12*Currier Cost=".$currier_pre_cost ?>"><? echo number_format($budgeted_currier,2); ?>  </td> 
						<td  align="right" title="<? echo "Total Actual Cost" ?>" ><? echo number_format($currier_actual_cost,2); ?>  </td>
						<td  align="right" title="Budget Cost-Actual Cost" ><?  $currier_variance_usd_in=$budgeted_currier-$currier_actual_cost;
						if($budgeted_currier>$currier_actual_cost) echo number_format($currier_variance_usd_in,0).' F';
						else  if($currier_actual_cost>$budgeted_currier) echo number_format(abs($currier_variance_usd_in),0).' UF';
						else if($currier_actual_cost==$budgeted_currier) echo '';
						//echo number_format($currier_variance_usd_in,2); ?>  </td>
						<td  align="right"  title="Variance USD/Budget Cost*100"><?  if($currier_variance_usd_in>0)  echo number_format(($currier_variance_usd_in/$currier_pre_cost)*100,2).' %'; ?>  </td>
						<td  align="right" title="Budget Cost/Invoice Value*100"><? if($currier_pre_cost>0) echo  number_format((($currier_pre_cost/$invoice_value)*100),2).' %'; ?>  </td>
						<td  align="right" title="Actual Cost/Invoice Value*100"><?   if($currier_actual_cost>0) echo  number_format((($currier_actual_cost/$invoice_value))*100,2).' %'; ?>  </td>
						
					</tr>
					
					<tr  bgcolor="<? echo $style1; ?>">
						<td title="Fright">Marketing & selling(Freight) </td>
						<td align="right">
						<? //echo number_format(($total_pre_emblish_cost/$tot_ship_qty)*12,2); ?> </td>
						<td align="right" >
						<? //echo number_format(($summ_total_booking_qty/$tot_ship_qty)*12,2); ?> </td>
						
						<td align="right" >
						<? //echo number_format(($summ_total_knit_fin_qty/$tot_ship_qty)*12,2);
						if($tot_ship_qty>0)
						{
						 $budgeted_freight= ($tot_ship_qty/12)*$freight;
						}
						else $budgeted_freight=0;
						
						 $freight_actual_cost=$budgeted_freight;
						 ?> </td>
						
						<td  align="right" title="<? echo "Total Ship Qty/12*Freight Cost=".$budgeted_freight ?>"><? echo number_format($budgeted_freight,2); ?>  </td> 
						<td  align="right" title="<? echo "Total Actual Cost" ?>" ><? echo number_format($freight_actual_cost,2); ?>  </td>
						<td  align="right" title="Budget Cost-Actual Cost" ><?  $freight_variance_usd_in=$budgeted_freight-$freight_actual_cost;
						if($budgeted_freight>$currier_actual_cost) echo number_format($freight_variance_usd_in,0).' F';
						else  if($freight_actual_cost>$budgeted_freight) echo number_format(abs($freight_variance_usd_in),0).' UF';
						else if($freight_actual_cost==$budgeted_freight) echo '';
						//echo number_format($freight_variance_usd_in,2); ?>  </td>
						<td  align="right"  title="Variance USD/Budget Cost*100"><? if($freight_variance_usd_in) echo number_format(($freight_variance_usd_in/$budgeted_freight)*100,2).' %';else echo ""; ?>  </td>
						<td  align="right" title="Budget Cost/Invoice Value"><? if($budgeted_freight>0) echo  number_format((($budgeted_freight/$invoice_value)*100),2).' %'; ?>  </td>
						<td  align="right" title="Actual Cost/Invoice Value"><? if($freight_actual_cost>0) echo  number_format((($freight_actual_cost/$invoice_value)*100),2).' %'; ?>  </td>
						
					</tr>
					<tr  bgcolor="#CCCCCC">
						<td title="" >Total Cost</td>
						<td align="right">
						<? //echo number_format(($total_pre_emblish_cost/$tot_ship_qty)*12,2); ?> </td>
						<td align="right" >
						<? //echo number_format(($summ_total_booking_qty/$tot_ship_qty)*12,2); ?> </td>
						
						<td align="right" >
						<? //echo number_format(($summ_total_knit_fin_qty/$tot_ship_qty)*12,2);
						 $total_summary_budgeted_cost= $total_pre_amt+$total_pre_emblish_cost+$total_pre_conver_cost+$total_summ_pre_trim_cost+$budgeted_cm+$budgeted_currier+$budgeted_freight;
						$total_summary_actual_cost=$total_actual_amt+$total_actual_emblish_cost+$total_actual_conver_cost+$total_summ_trim_recv_cost+$tot_makingcost_of_cm+$currier_actual_cost+$freight_actual_cost;
						
						 ?> </td>
						
						<td  align="right" title="<? echo "Total  Budgeted" ?>"><? echo number_format($total_summary_budgeted_cost,2); ?>  </td> 
						<td  align="right" title="<? echo "Total Actual Cost" ?>" ><? echo number_format($total_summary_actual_cost,2); ?>  </td>
						<td  align="right" title="Budget Cost-Actual Cost" ><?  $tot_summ_variance_usd_in=$total_summary_budgeted_cost-$total_summary_actual_cost;
						if($total_summary_budgeted_cost>$currier_actual_cost) echo number_format($tot_summ_variance_usd_in,0).' F';
						else  if($total_summary_actual_cost>$total_summary_budgeted_cost) echo number_format(abs($tot_summ_variance_usd_in),0).' UF';
						else if($total_summary_actual_cost==$total_summary_budgeted_cost) echo '';
						//echo number_format($tot_summ_variance_usd_in,2); ?>  </td>
						<td  align="right"  title="Variance USD/Budget Cost*100"><? if($tot_summ_variance_usd_in>0) echo number_format(($tot_summ_variance_usd_in/$total_summary_budgeted_cost)*100,2).' %';else echo ""; ?>  </td>
						<td  align="right" title="Budget Cost/Invoice Value*100"><? if($total_summary_budgeted_cost>0) echo  number_format((($total_summary_budgeted_cost/$invoice_value)*100),2).' %';else echo ""; ?>  </td>
						<td  align="right" title="Actual Cost/Invoice Value*100"><? if($total_summary_budgeted_cost>0) echo  number_format((($total_summary_actual_cost/$invoice_value)*100),2).' %';else echo ""; ?>  </td>
						
					</tr>
					<tr  bgcolor="<? echo $style; ?>">
						<td title="" >Margin</td>
						<td align="right">
						<? //echo number_format(($total_pre_emblish_cost/$tot_ship_qty)*12,2); ?> </td>
						<td align="right" >
						<? //echo number_format(($summ_total_booking_qty/$tot_ship_qty)*12,2); ?> </td>
						
						<td align="right" >
						<? //echo number_format(($summ_total_knit_fin_qty/$tot_ship_qty)*12,2);
						if($tot_ship_qty)
						{
						  $budgeted_markup_margin= ($tot_ship_qty/12)*$margin_dzn;
						}
						  $total_margin_summary_actual_cost=$invoice_value-$total_summary_actual_cost;
						 ?> </td>
						
						<td  align="right" title="<? echo "Total  Ship Qty/12*Margin Dzn" ?>"><? echo number_format($budgeted_markup_margin,2); ?>  </td> 
						<td  align="right" title="<? echo "Total Actual Cost" ?>" ><? echo number_format($total_margin_summary_actual_cost,2); ?>  </td>
						<td  align="right" title="Budget Cost-Actual Cost" ><?  $tot_summ_margin_variance_usd_in=$budgeted_markup_margin-$total_margin_summary_actual_cost;
						if($budgeted_markup_margin>$currier_actual_cost) echo number_format($tot_summ_margin_variance_usd_in,0).' F';
						else  if($total_margin_summary_actual_cost>$budgeted_markup_margin) echo number_format(abs($tot_summ_margin_variance_usd_in),0).' UF';
						else if($total_margin_summary_actual_cost==$budgeted_markup_margin) echo '';
						//echo number_format($tot_summ_margin_variance_usd_in,2); ?>  </td>
						<td  align="right"  title="Variance USD/Budget Cost*100"><? if($tot_summ_margin_variance_usd_in>0) echo number_format(($tot_summ_margin_variance_usd_in/$budgeted_markup_margin)*100,2).' %';else echo ""; ?>  </td>
						<td  align="right" title="Budget Cost/Invoice Value*100"><? if($budgeted_markup_margin>0) echo  number_format((($budgeted_markup_margin/$invoice_value)*100),2).' %';else echo ""; ?>  </td>
						<td  align="right" title="Actual Cost/Invoice Value*100"><? if($total_margin_summary_actual_cost>0) echo  number_format((($total_margin_summary_actual_cost/$invoice_value)*100),2).' %';else echo ""; ?>  </td>
						
					</tr>
					<tr  bgcolor="#CCCCCC">
						<td title="">Invoice Value</td>
						<td align="right">
						<? //echo number_format(($total_pre_emblish_cost/$tot_ship_qty)*12,2); ?> </td>
						<td align="right" >
						<? //echo number_format(($summ_total_booking_qty/$tot_ship_qty)*12,2); ?> </td>
						
						<td align="right" >
						<? //echo number_format(($summ_total_knit_fin_qty/$tot_ship_qty)*12,2);
						  $budgeted_invoice_value=$total_summary_budgeted_cost+$budgeted_markup_margin;// ($tot_ship_qty/12)*$freight;
						 ?> </td>
						
						<td  align="right" title="<? echo "Total  Budgeted Cost+Markup" ?>"><? $summ_budgeted_invoice_value=$budgeted_invoice_value;echo number_format($summ_budgeted_invoice_value,2); ?>  </td> 
						<td  align="right" title="<? echo "Total Margin Cost+Total Actual Cost" ?>" ><? $summ_actual_invoice_value=$total_margin_summary_actual_cost+$total_summary_actual_cost;echo number_format($summ_actual_invoice_value,2); ?>  </td>
						<td  align="right" title="Actual Cost-Budget Cost" ><?  $tot_invoice_summ_variance_usd_in=$summ_actual_invoice_value-$summ_budgeted_invoice_value;echo number_format($tot_invoice_summ_variance_usd_in,0); ?>  </td>
						<td  align="right"  title="Variance USD/Budget Cost*100"><?  if($tot_invoice_summ_variance_usd_in>0) echo number_format(($tot_invoice_summ_variance_usd_in/$summ_budgeted_invoice_value)*100,2).' %'; ?>  </td>
						<td  align="right" title="Budget Cost/Invoice Value"><? if($summ_budgeted_invoice_value>0) echo  number_format((($summ_budgeted_invoice_value/$invoice_value)*100),2).' %'; ?>  </td>
						<td  align="right" title="Actual Cost/Invoice Value"><? if($summ_actual_invoice_value>0)  echo  number_format((($summ_actual_invoice_value/$invoice_value)*100),2).' %'; ?>  </td>
						
					</tr>
					
					<?
						$total_remaining_actual_amt=$total_margin_summary_actual_cost;
						?>
					
				</tbody>
			</thead>
			</table>
			<br/>
			<table class="rpt_table" width="350" cellpadding="0" cellspacing="0" border="1" rules="all">
				<tr>
				<td><b>Remaining Amount</b> </td>
				<td  align="right" title="<? echo "Total Actual Cost" ?>"><? echo 'USD '.number_format($total_remaining_actual_amt,2); ?>  </td> 
				<td  align="right" title="<? echo "Total Actual Cost/Invoice Value*100" ?>"><? if($total_remaining_actual_amt>0) echo number_format((($total_remaining_actual_amt/$invoice_value)*100),2).' %'; ?>  </td> 
				</tr>
			</table>
			 <br/>
			<table class="rpt_table" width="1200" cellpadding="0" cellspacing="0" border="1" rules="all" id="fabric_part">
			
			<thead>
				<tr>
					<th colspan="2">Fabrication 	</th>
					<th colspan="2" >Rate per unit</th>
					<th colspan="4"> Consumption</th>
					<th colspan="4"> Total Cost in USD </th>
					<th colspan="2">Cost %</th>
				</tr>
				
				<tr>
					<th>Description</th>
					<th>Unit</th>
					<th>Budgeted</th>
					<th>Actual </th>
					
					<th> Budgeted </th>
					<th> Booking All</th>
                    <th> Booking Main </th>
					<th> Actual</th>
                    
					<th>Budgeted</th>
					<th> Booking All </th>
                    <th> Booking Main </th>
					<th>Actual </th>
					<th>Budgeted </th>
					<th>Actual</th>
				</tr>
				<tbody>
					<?
					$k=1;$total_budget_fab_qty=$total_fab_booking_qty=$total_knit_fin_qty=$total_pre_fab_cost=$total_booking_fab_cost=$total_knit_fin_recv_cost=$total_booking_fab_cost_main=0;
					foreach($fab_data_arr as $desc=>$desc_data)
					{
						foreach($desc_data as $uom_id=>$val)
						{
						//$avg_rate=$val['avg_rate'];
					?>
					<tr>
						
						<td><? echo $desc; ?> </td>
						<td><? echo  $unit_of_measurement[$uom_id]; ?>  </td>
						<td><? echo number_format($val['avg_rate'],4); ?></td>
						<td><? echo number_format($val['booking_avg_rate'],4); ?></td>
						
						<td align="right" title="Ship Qty/12*Fab Avg Cons"><? echo number_format($val['pre_fab_qty'],2); ?>  </td>
						<td align="right"><? echo number_format($val['booking_qty'],2);?>  </td>
                        <td align="right"><? echo number_format($val['booking_qty_main'],2);?>  </td>
						<td  align="right"><? echo number_format($val['knit_fin_qty'],2); ?>  </td>
						
						<td align="right" title="Budget Rate*Budget Fab Qty"><? $pre_fab_cost=($val['pre_fab_qty']*$val['avg_rate']);if($val['pre_fab_qty']>0) echo number_format($pre_fab_cost,2);else echo "";?> </td>
						<td align="right" title="Booking Rate*Booking Fab Qty"><? $booking_fab_cost=($val['booking_qty']*$val['booking_avg_rate']);if($val['booking_qty']>0) echo number_format($booking_fab_cost,2);else   $booking_fab_cost=0;?>  </td>
                         <td align="right"><?   $booking_fab_cost_main=($val['booking_qty_main']*$val['booking_avg_rate']) ;if($val['booking_qty_main']>0) echo number_format($booking_fab_cost_main,2);else $booking_fab_cost_main=0;?>  </td>
						<td align="right" title="Booking Rate*Knit Fin Recv Qty"><? $knit_fin_recv_cost=($val['knit_fin_qty']*$val['booking_avg_rate']);if($val['knit_fin_qty']>0) echo number_format($knit_fin_recv_cost,2);else $knit_fin_recv_cost=0;$tot_knit_fin_recv_cost+=$knit_fin_recv_cost;?>  </td>
						<? 
						if($k==1)
						{
						?>
						<td align="right" rowspan="<? echo  count($fab_data_arr);?>" title="Total Budget(<? echo $total_pre_amt;?>)/Invoice Value*100 "><? if($total_pre_amt>0) echo number_format(($total_pre_amt/$invoice_value)*100,2).' %';else echo "";?>  </td>
						<td align="right" rowspan="<? echo  count($fab_data_arr);?>" title="Total Fin Recv Actual(<? echo $total_actual_amt;?>)/Invoice Value*100"><? if($total_actual_amt>0) echo number_format(($total_actual_amt/$invoice_value)*100,2).' %';else echo "";?>  </td>
							<?
						}
							?>
					</tr>
					<?	
					$k++;
						if($uom_id==12)//Budget//Booking
						{
						$total_budget_fab_qty+=$val['pre_fab_qty'];
						$total_fab_booking_qty+=$val['booking_qty'];
						$total_fab_booking_main_qty+=$val['booking_qty_main'];//total_fab_booking_main_qty
						$total_knit_fin_qty+=$val['knit_fin_qty'];
						}
						$total_pre_fab_cost+=$pre_fab_cost;
						$total_booking_fab_cost+=$booking_fab_cost;$total_booking_fab_cost_main+=$booking_fab_cost_main;
						$total_knit_fin_recv_cost+=$knit_fin_recv_cost;
						
						
						$total_knit_fin_recv_cost+=$knit_fin_recv_cost;
						$total_knit_fin_recv_cost+=$knit_fin_recv_cost;
						
						}
					}
					?>
				</tbody>
				<tfoot>
					<tr>
					<th colspan="2" align="right"> Sub Total</th>
					<th>&nbsp;  </th>
					<th>&nbsp;  </th>
					<th title="Kg"> <? echo number_format($total_budget_fab_qty,2);?></th>
					<th title="Kg"> <? echo number_format($total_fab_booking_qty,2);?></th>
                    <th title="Kg"> <? echo number_format($total_fab_booking_main_qty,2);?></th>
					<th title="Kg"> <? echo number_format($total_knit_fin_qty,2);?></th>
					<th> <? echo number_format($total_pre_fab_cost,2);?></th>
					<th> <? echo number_format($total_booking_fab_cost,2);?></th>
                    <th> <? echo number_format($total_booking_fab_cost_main,2);?></th>
					<th> <? echo number_format($tot_knit_fin_recv_cost,2);?></th>
					<th>&nbsp;  </th>
					<th>&nbsp;  </th>
					
					</tr>
				</tfoot>
			</thead>
			</table>
			<br>
			<table class="rpt_table" width="1000" cellpadding="0" cellspacing="0" border="1" rules="all">
			<caption><b style="float:left">Print / Embroidery / Others/Wash/ Conversion</b> </caption>
			<thead>
				<tr>
					<th colspan="2">Fabrication	</th>
					<th colspan="2" >Rate per unit</th>
					<th colspan="2"> Consumption</th>
					<th colspan="2"> Total Cost in USD </th>
					<th colspan="2">Cost %</th>
				</tr>
				
				<tr>
					<th>Description</th>
					<th>Unit</th>
					<th>Budgeted</th>
					<th>Actual </th>
					
					<th> Budgeted </th>
					<th> Actual </th>
					
					
					<th>Budgeted</th>
					<th> Actual </th>
					<!--<th>Actual </th>-->
					<th> Budgeted </th>
					<th>Actual</th>
				</tr>
				<tbody>
					<?
					$totembli_booking_cost=$tot_pre_embli_cost=0;
					foreach($emblish_detail_arr as $embl_name=>$val)
					{
						$cons_dzn_gmts=$val['cons_dzn_gmts'];
						$budgeted_qty=($tot_ship_qty/12)*$cons_dzn_gmts;
						$totembli_booking_cost+=($val['booking_emblish_qty']*$val['booking_embl_rate']);
						$tot_pre_embli_cost+=($budgeted_qty*$val['pre_embl_rate']);
					}
					
					$m=1;$total_budget_embl_qty=$total_embl_booking_qty=$total_embl_actual_booking_qty=$total_pre_embli_cost=$total_actual_embli_cost=$total_embl_booking_cost=0;
					foreach($emblish_detail_arr as $embl_name=>$val)
					{
						
						$embl_nameArr=explode("__",$embl_name);
						$embl_name=$embl_nameArr[0];
						$embl_type=$embl_nameArr[1];
						$cons_dzn_gmts=$val['cons_dzn_gmts'];
						$pre_embl_amount=$val['pre_embl_amount'];
						$pre_embl_qty=$val['pre_embl_qty'];
						
						if($pre_embl_amount>0)
						{
						$pre_embl_rate=$pre_embl_amount/$pre_embl_qty;
						}
						if($tot_ship_qty>0)
						{
						$budgeted_qty=($tot_ship_qty/12)*$cons_dzn_gmts;
						}
						
						//pre_embl_amount
						//echo $pre_embl_amount.'='.$pre_embl_qty.',';
						if($embl_type!="") $embl_type_cond= '('.$embl_type.')';else $embl_type_cond="";
						?>
					<tr>
						
						<td><? echo $emblishment_name_array[$embl_name].$embl_type_cond; ?> </td>
						<td><? echo  'Dzn';//$unit_of_measurement[$val['order_uom']]; ?>  </td>
						<td align="right" title="Tot Budget Amount(<? echo $pre_embl_amount;?>)/Tot Qty(<? echo $pre_embl_qty;?>)"><? //echo number_format($pre_embl_rate,4);
						echo $val['pre_embl_rate']; ?></td>
						<td align="right"><? echo number_format($val['booking_embl_rate'],4); ?></td>
						<td align="right" title="Tot Shipped Qty/12*Cons Dzn Qty(<? echo $cons_dzn_gmts; ?>)"><? echo number_format($budgeted_qty,2); ?>  </td>
						<td align="right" title="Booking Embl Qty"><? echo number_format($val['booking_emblish_qty'],2); ?>  </td>
						
						
						<td align="right" title="Budget Rate*Budget Embl Qty"><? if($budgeted_qty>0)  $pre_embli_cost=($budgeted_qty*$val['pre_embl_rate']);echo number_format($pre_embli_cost,2);?> </td>
						<td align="right" title="Booking Rate(<? echo $val['booking_embl_rate'];?>)* Booking Embl Qty"><? $embli_booking_cost=($val['booking_emblish_qty']*$val['booking_embl_rate']);echo number_format($embli_booking_cost,2);?> </td>
						
						<!--<td align="right" title="Booking Rate*Actual Qty"><? //$actual_embli_cost=($val['booking_emblish_qty']*$val['booking_embl_rate']);echo number_format($actual_embli_cost,2);?>  </td>-->
						<? if($m==1)
						{
						?>
						<td align="right" rowspan="<? echo  count($emblish_detail_arr);?>" title="Total Budget(<? echo $tot_pre_embli_cost;?>)/Invoice Value*100 "><? if($tot_pre_embli_cost>0) echo number_format(($tot_pre_embli_cost/$invoice_value)*100,2).' %';?>  </td>
						<td align="right" rowspan="<? echo  count($emblish_detail_arr);?>" title="Total Actual(<? echo $totembli_booking_cost;?>)/Invoice Value*100"><? if($totembli_booking_cost>0) echo number_format(($totembli_booking_cost/$invoice_value)*100,2).' %';?>   </td>
							<?
						}
							?>
							
					</tr>
					<?	
						$m++;
						$total_budget_embl_qty+=$budgeted_qty;
						$total_embl_booking_qty+=$val['booking_emblish_qty'];
						$total_embl_actual_booking_qty+=$actual_emblish_qty;
						
						$total_embl_booking_cost+=$embli_booking_cost;
						
						$total_pre_embli_cost+=$pre_embli_cost;
						$total_actual_embli_cost+=$actual_embli_cost;
					}
					$m=1;
					foreach($conv_detail_arr as $process_name=>$val)
					{
						$conv_cons_dzn_gmts=$val['cons_dzn_gmts'];
						//echo $conv_cons_dzn_gmts.'DD';
						?>
					<tr>
						
						<td title="Conversion Process"><? echo $conversion_cost_head_array[$process_name]; ?> </td>
						<td><? echo  $unit_of_measurement[$val['order_uom']]; ?>  </td>
						<td align="right"><? echo $val['pre_conv_rate']; ?></td>
						<td align="right"><? echo $val['booking_conv_rate']; ?></td>
						 
						
						<td align="right" title="Tot Shipped Qty/12*Cons Dzn Qty"><? $budgeted_qty=($tot_ship_qty/12)*$conv_cons_dzn_gmts;if($tot_ship_qty>0) echo number_format($budgeted_qty,2); ?>  </td>
						<td align="right" title="Booking Service Qty"><? echo number_format($val['booking_conv_qty'],2); ?>  </td>
					
						
						<td align="right" title="Budget Rate*Budget Service Qty"><? $pre_conv_cost=($budgeted_qty*$val['pre_conv_rate']);if($budgeted_qty>0) echo number_format($pre_conv_cost,2);?> </td>
						<td align="right" title="Booking Rate* Booking Service Qty"><? $conver_booking_cost=($val['booking_conv_qty']*$val['booking_conv_rate']);if($val['booking_conv_qty']>0) echo number_format($conver_booking_cost,2);?> </td>
						
						<!--<td align="right" title="Booking Rate*Actual Qty"><? //$actual_conver_cost=($val['booking_conv_qty']*$val['booking_conv_rate']);echo number_format($actual_conver_cost,2);?>  </td>-->
						<? if($m==1)
						{
						?>
						<td align="right" rowspan="<? echo  count($conv_detail_arr);?>" title="Total Budget(<? echo $total_pre_conver_cost;?>)/Invoice Value*100 "><? if($total_pre_conver_cost>0) echo number_format(($total_pre_conver_cost/$invoice_value)*100,2).' %';?>  </td>
						<td align="right" rowspan="<? echo  count($conv_detail_arr);?>" title="Total Actual(<? echo $total_actual_conver_cost;?>)/Invoice Value*100"><? if($total_actual_conver_cost>0) echo number_format(($total_actual_conver_cost/$invoice_value)*100,2).' %';?>  </td>
							<?
						}
							?>
							
					</tr>
					<?	
						$m++;
						$total_budget_conv_qty+=$budgeted_qty;
						$total_conv_booking_qty+=$val['booking_conv_qty'];
					//	$total_conv_actual_booking_qty+=$actual_emblish_qty;
						
						$total_conv_booking_cost+=$conver_booking_cost;
						
						$total_pre_conv_cost+=$pre_conv_cost;
						$total_actual_conv_cost+=$actual_conver_cost;
					}
					?>
				</tbody>
				<tfoot>
					<tr>
					<th colspan="2" align="right">Total </th>
					
					<th>&nbsp;  </th>
					<th>&nbsp;  </th>
					
					<th align="right"> <? //echo number_format($total_budget_embl_qty+$total_budget_conv_qty,2);?></th>
					<th align="right"> <? //echo number_format($total_embl_booking_qty+$total_conv_booking_qty,2);?></th>
					<!--<th align="right"> <? //echo number_format($total_embl_actual_booking_qty,2);?></th>-->
					<th align="right"> <?  echo number_format($total_pre_embli_cost+$total_pre_conv_cost,2);?></th>
					<th align="right"> <? echo number_format($total_embl_booking_cost+$total_conv_booking_cost,2);?></th>
					<!--<th align="right"> <? //echo number_format($total_actual_embli_cost,2);?></th>-->
					<th>&nbsp;  </th>
					<th>&nbsp;  </th>
					
					</tr>
				</tfoot>
			</thead>
			</table>
			<br>
			<table class="rpt_table" style="display:none" width="1000" cellpadding="0" cellspacing="0" border="1" rules="all">
			<caption><b style="float:left">Conversion Cost Fabric </b> </caption>
			<thead>
				<tr>
					<th colspan="2">Fabrication	</th>
					<th colspan="2" >Rate per unit</th>
					<th colspan="2"> Consumption</th>
					<th colspan="2"> Total Cost in USD </th>
					<th colspan="2">Cost %</th>
				</tr>
				
				<tr>
					<th>Description</th>
					<th>Unit</th>
					<th>Budgeted</th>
					<th>Actual </th>
					
					<th> Budgeted </th>
					<th> Actual </th>
										
					<th>Budgeted</th>
					<th> Actual </th>

					<th> Budgeted </th>
					<th>Actual</th>
				</tr>
				<tbody>
					<?
					
					$m=1;$total_budget_conv_qty=$total_conv_booking_qty=$total_conv_actual_booking_qty=$total_pre_conv_cost=$total_actual_conv_cost=$totalconv_booking_cost=0;
					foreach($conv_detail_arr as $process_name=>$val)
					{
						$conv_cons_dzn_gmts=$val['cons_dzn_gmts'];
						//echo $conv_cons_dzn_gmts.'DD';
						?>
					<tr>
						
						<td><? echo $conversion_cost_head_array[$process_name]; ?> </td>
						<td><? echo  $unit_of_measurement[$val['order_uom']]; ?>  </td>
						<td align="right"><? echo $val['pre_conv_rate']; ?></td>
						<td align="right"><? echo $val['booking_conv_rate']; ?></td>
						 
						
						<td align="right" title="Tot Shipped Qty/12*Cons Dzn Qty"><? $budgeted_qty=($tot_ship_qty/12)*$conv_cons_dzn_gmts;if($tot_ship_qty>0) echo number_format($budgeted_qty,2); ?>  </td>
						<td align="right" title="Booking Service Qty"><? echo number_format($val['booking_conv_qty'],2); ?>  </td>
					
						
						<td align="right" title="Budget Rate*Budget Service Qty"><? $pre_conv_cost=($budgeted_qty*$val['pre_conv_rate']);if($budgeted_qty>0) echo number_format($pre_conv_cost,2);?> </td>
						<td align="right" title="Booking Rate* Booking Service Qty"><? $conver_booking_cost=($val['booking_conv_qty']*$val['booking_conv_rate']);if($val['booking_conv_qty']>0)  echo number_format($conver_booking_cost,2);?> </td>
						
						<!--<td align="right" title="Booking Rate*Actual Qty"><? //$actual_conver_cost=($val['booking_conv_qty']*$val['booking_conv_rate']);echo number_format($actual_conver_cost,2);?>  </td>-->
						<? if($m==1)
						{
						?>
						<td align="right" rowspan="<? echo  count($conv_detail_arr);?>" title="Total Budget(<? echo $total_pre_conver_cost;?>)/Invoice Value "><? if($total_pre_conver_cost>0) echo number_format($total_pre_conver_cost/$invoice_value,2).' %';?>  </td>
						<td align="right" rowspan="<? echo  count($conv_detail_arr);?>" title="Total Actual(<? echo $total_actual_conver_cost;?>)/Invoice Value"><? if($total_actual_conver_cost>0) echo number_format($total_actual_conver_cost/$invoice_value,2).' %';?>  </td>
							<?
						}
							?>
							
					</tr>
					<?	
						$m++;
						$total_budget_conv_qty+=$budgeted_qty;
						$total_conv_booking_qty+=$val['booking_conv_qty'];
					//	$total_conv_actual_booking_qty+=$actual_emblish_qty;
						
						$total_conv_booking_cost+=$conver_booking_cost;
						
						$total_pre_conv_cost+=$pre_conv_cost;
						$total_actual_conv_cost+=$actual_conver_cost;
					}
					?>
				</tbody>
				<tfoot>
					<tr>
					<th colspan="2" align="right"> Sub Total</th>
					
					<th>&nbsp;  </th>
					<th>&nbsp;  </th>
					
					<th align="right"> <? echo number_format($total_budget_conv_qty,2);?></th>
					<th align="right"> <? echo number_format($total_conv_booking_qty,2);?></th>
					
					<th align="right"> <? echo number_format($total_pre_conv_cost,2);?></th>
					<th align="right"> <? echo number_format($total_conv_booking_cost,2);?></th>
					<!--<th align="right"> <? //echo number_format($total_actual_conv_cost,2);?></th>-->
					<th>&nbsp;  </th>
					<th>&nbsp;  </th>
					
					</tr>
				</tfoot>
			</thead>
			</table>
			<br>
			<table class="rpt_table" width="1000" cellpadding="0" cellspacing="0" border="1" rules="all">
			<caption><b style="float:left">Accessories</b> </caption>
			<thead>
				<tr>
					<th colspan="3">Fabrication	</th>
					<th colspan="2" >Rate per unit</th>
					<th colspan="2"> Consumption</th>
					<th colspan="2"> Total Cost in USD </th>
					<th colspan="2">Cost %</th>
				</tr>
				
				<tr>
					<th>Item</th>
					<th>Description</th>
					<th>Unit</th>
					<th>Budgeted</th>
					<th>Actual </th>
					
					<th> Budgeted </th>
					<th> Actual </th>
					
					<th>Budgeted</th>
					<th> Actual </th>
					<!--<th>Actual </th>-->
					<th> Budgeted </th>
					<th>Actual</th>
				</tr>
				<tbody>
					<?
					
					$m=1;$total_actual_trim_recv_qty=$total_budget_trim_qty=$total_trim_booking_req_qty=$total_pre_trim_cost=$total_trim_booking_cost=$total_actual_trim_cost=0;
					foreach($trims_detail_arr as $trim_id=>$desc_data)
					{
						foreach($desc_data as $desc_id=>$val)
				    	{
						$actual_trim_recv_qnty=$trim_recv_qty_arr[$trim_id]['trim_recv_qnty'];
						$trim_recv_rate=$trim_recv_qty_arr[$trim_id]['trim_recv_rate']; //booking_trim_rate
						//$pre_req_qty=$val['pre_req_qty'];
						$trim_recv_amount=$val['trim_recv_amount'];	
						$booking_trim_rate=$val['booking_trim_rate'];
						if($tot_ship_qty>0)
						{
							$pre_req_cons_qty=($tot_ship_qty/12)*$val['cons_qty'];
						}
						else $pre_req_cons_qty=0;
						
						//echo $tot_ship_qty.'='.$val['trim_booking_req_qty'];
						?>
					<tr>
						
						<td><? echo $item_library[$trim_id]; ?> </td>
						<td><? echo $val['desc']; ?> </td>
						<td><? echo  $unit_of_measurement[$val['order_uom']]; ?>  </td>
						<td align="right"><? echo $val['trim_rate']; ?></td>
						<td align="right" title="Item Trim WO Amount/Trim WO Qty"><? $trim_actual_rate=$booking_trim_rate; echo number_format($trim_actual_rate,2); ?></td>
						<td align="right" title="Tot Ship Qty/12*Cons Dzn Qty"><? echo number_format($pre_req_cons_qty,2); ?>  </td>
						<td align="right" title="Booking Trim"><? echo number_format($val['trim_booking_req_qty'],2); ?>  </td>
						
						
						<td align="right" title="Budget Qty*Budget Rate==><?=$pre_req_cons_qty.'*'.$val['trim_rate'];?>"><? $trim_booking_cost=$pre_req_cons_qty*$val['trim_rate'];	if($pre_req_cons_qty>0) echo number_format($trim_booking_cost,2); ?>  </td>
						
						<td align="right" title="Trim Booking Rate*Booking Trim"> <? 
						if($val['trim_booking_req_qty']>0)
						{
						$pre_trim_cost=($val['trim_booking_req_qty']*$trim_actual_rate);
						}
						else $pre_trim_cost=0;
						if($val['trim_booking_req_qty']>0) echo number_format($pre_trim_cost,2);?> </td>
						
						<!--<td align="right" title="Trim Recv Rate*Actual Qty"><? //$actual_trim_recv_cost=($actual_trim_recv_qnty*$trim_actual_rate);echo number_format($actual_trim_recv_cost,2);?>  </td>-->
						<? if($m==1)
						{
						?>
						<td align="right" rowspan="<? echo  count($trims_detail_arr);?>" title="Total Budget(<? echo $total_summ_pre_trim_cost;?>)/Invoice Value*100 "><? if($total_summ_pre_trim_cost>0) echo number_format(($total_summ_pre_trim_cost/$invoice_value)*100,2).' %';?>  </td>
						<td align="right" rowspan="<? echo  count($trims_detail_arr);?>" title="Total Actual(<? echo $total_summ_trim_recv_cost;?>)/Invoice Value*100"><? if($total_summ_trim_recv_cost>0) echo number_format(($total_summ_trim_recv_cost/$invoice_value)*100,2).' %';?>  </td>
							<?
						}
							?>
					</tr>
					<?	
					$m++;
						$total_budget_trim_qty+=$pre_req_cons_qty;
						$total_trim_booking_req_qty+=$val['trim_booking_req_qty'];
						$total_actual_trim_recv_qty+=$actual_trim_recv_qnty;
						
						$total_pre_trim_cost+=$pre_trim_cost;
						$total_trim_booking_cost+=$trim_booking_cost;
						$total_actual_trim_cost+=$actual_trim_recv_cost;
					}}
					?>
				</tbody>
				<tfoot>
					<tr>
					<th colspan="5" align="right"> Sub Total</th>
					
					
					
					<th align="right"> <? echo number_format($total_budget_trim_qty,2);?></th>
					<th align="right"> <? echo number_format($total_trim_booking_req_qty,2);?></th>
					
					<th align="right"> <? echo number_format($total_trim_booking_cost,2);?></th>
					<th align="right"> <? echo number_format($total_pre_trim_cost,2);?></th>
					<!--<th align="right"> <? //echo number_format($total_actual_trim_cost,2);?></th>-->
					<th>&nbsp;  </th>
					<th>&nbsp;  </th>
					
					
					</tr>
				</tfoot>
			</thead>
			</table>
			<br>
			<table class="rpt_table" width="1000" cellpadding="0" cellspacing="0" border="1" rules="all">
			<caption><b style="float:left">Cost of Making</b> </caption>
			<thead>
				<tr>
					<th>Unit</th>
					<th>Month</th>
					<th>Efficiency</th>
					<th> SMV </th>
					<th> CPM</th>
					<th>Target Prod. Min</th>
					<th> Actual Prod. Min </th>
					<th>  Spent Min  </th>
					<th>Production(Pcs)</th>
					<th> Total </th>
					<th> % </th>
					<th> % </th>
				</tr>	
			</thead>
			<tbody>
				</tr>
					<tr  bgcolor="<? echo $style; ?>">
						<td align="center">
						<? echo  $company_short_library[$cbo_company_name]; ?> </td>
						<td align="center" ><? echo $all_last_mon; ?> </td>
						<td align="right" ><?
						if($total_pre_amt>0) 
						{
						 $efficiency=($total_pre_amt/$invoice_value)*100;
						}
						else  $efficiency=0;
						if($total_pre_amt>0) echo number_format($efficiency,2).' %'; ?> </td>
						<td  align="right" title=""><? echo number_format($set_smv,2); ?>  </td> 
						<td  align="right" title="" ><? 
						 
					
						echo number_format($cost_per_minute,2); ?>  </td>
						<td  align="right" title="Tot Oder Qty Pcs*SMV" ><?  echo number_format($target_prod_min,2); ?>  </td>
						<td  align="right"  title="Sewing Out*SMV"><? echo number_format($actual_prod_min,2); ?>  </td>
						<td  align="right" title="Actual Prod Min/Efficiency*100"><? 
						if($actual_prod_min>0) 
						{
						$spent_min=($actual_prod_min/$efficiency)*100;
						}
						else $spent_min=0;
						if($actual_prod_min>0) echo  number_format($spent_min,2); $tot_spent_min+=$spent_min;?>  </td>
						<td  align="right" title="Actual Prod Min/SMV"><? $production_pcs=$actual_prod_min/$set_smv; if($actual_prod_min>0) echo  number_format($production_pcs,2); ?>  </td>
						<td  align="right"  title="CPM*Actual Prod Min"><? echo number_format($tot_makingcost_of_cm,2); ?>  </td>
						<td  align="right" title="Actual Prod Min/Invoice Value"><? if($actual_prod_min>0)  echo  number_format(($actual_prod_min/$invoice_value),2); ?>  </td>
						<td  align="right" title="Spent min/Invoice Value"><? if($spent_min>0)  echo  number_format(($spent_min/$invoice_value),2);else $spent_min=0;
						 if($spent_min>0)
						 {
						$per_tot+=$spent_min/$invoice_value;
						 }?>  </td>
						
					</tr>
					<tr bgcolor="#CCCCCC">
						<td align="right" colspan="2"> Sub Total</td>
						
						<td align="right" ><? $efficiency=($total_pre_amt/$invoice_value)*100; if($total_pre_amt>0) echo number_format($efficiency,2).' %'; ?> </td>
						<td  align="right" title=""><? echo number_format($set_smv,2); ?>  </td> 
						<td  align="right" title="" ><? 
						echo number_format($cost_per_minute,2); ?>  </td>
						<td  align="right" title="Tot Oder Qty Pcs*SMV" ><?  echo number_format($target_prod_min,2); ?>  </td>
						<td  align="right"  title="Sewing Out*SMV"><? echo number_format($actual_prod_min,2); ?>  </td>
						<td  align="right" title="Atual Prod Min/Efficiency"><? echo  number_format($tot_spent_min,2); ?>  </td>
						<td  align="right" title="Actual Prod Min/SMV"><? $production_pcs=$actual_prod_min/$set_smv; if($actual_prod_min>0)  echo  number_format($production_pcs,2); ?>  </td>
						<td  align="right"  title="CPM*Actual Prod Min"><? echo number_format($tot_makingcost_of_cm,2); ?>  </td>
						<td  align="right" title="Actual Prod Min/Invoice Value"><?  if($actual_prod_min>0) echo  number_format(($actual_prod_min/$invoice_value),2); ?>  </td>
						<td  align="right" title="Spent min/Invoice Value"><? echo  number_format($per_tot,2); ?> </td>
					</tr>
			</tbody>
			
			</table>
			<br>
			<?
				$short_fabric_booking_qty=0; $sample_fabric_booking_qty=0;
				foreach($fab_booking_data_arr as $fid_data){
					foreach($fid_data as $type=>$value){
						if($type=='booking_fab_qty_short'){
							$short_fabric_booking_qty+=$value;
						}
						if($type=='booking_fab_qty_sample'){
							$sample_fabric_booking_qty+=$value;
						}
						if($type=='booking_fab_qty_main'){
							$main_fabric_booking_qty+=$value;
						}
					}
				}
			?>
			<div>
				<p> <b>Note : </b> </p>
				<p> # Fabric booking was&nbsp;<? echo $main_fabric_booking_qty; ?>&nbsp;kgs</p>
				<p> # Short Fabric booking was&nbsp;<? echo $short_fabric_booking_qty; ?>&nbsp;kgs</p>
				<p> # Sample Fabric booking was&nbsp;<? echo $sample_fabric_booking_qty; ?>&nbsp;kgs</p>
				<p> # Short   shipment 	 <? echo $order_qty_pcs-$tot_ship_qty; ?>   Pcs Garments.</p>
				<p> # store received <? echo $total_knit_fin_qty; ?> kgs, short received &nbsp;<? echo $total_fab_booking_qty-$total_knit_fin_qty; ?> &nbsp;kgs.</p>
				
			</div>
			<?
				echo signature_table(144, $cbo_company_name, "950px");
			?>
        </div>
<?

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
    echo "$html****$filename"; 
    exit();
}

if($action=='trims_popup')
{
	echo load_html_head_contents("Trims Details info", "../../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id."*".$tot_po_qnty;die;
	
	//echo $ratio;die;

?>	
<script>

	function window_close()
	{
		parent.emailwindow.hide();
	}
	
</script>	
<fieldset style="width:650px;" >
<legend>Accessories Status pop up</legend>
    <div style="100%" id="report_container">
       <table width="650" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr>
                    <th colspan="7">Accessories Status</th>
                </tr>
                <tr>
                    <th width="110">Item</th>
                    <th width="70">UOM</th>
                    <th width="90">Req. Qty.</th>
                    <th width="90">Received</th>
                    <th width="90">Recv. Balance</th>
                    <th width="90">Issued</th>
                    <th>Left Over</th>
                </tr>
            </thead>
            <?
			$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
			$trims_array=array();
			$trimsDataArr=sql_select("select b.item_group_id,  
									sum(CASE WHEN a.entry_form=24 THEN a.quantity ELSE 0 END) AS recv_qnty,
									sum(CASE WHEN a.entry_form=25 THEN a.quantity ELSE 0 END) AS issue_qnty
									from order_wise_pro_details a, product_details_master b where a.prod_id=b.id and a.po_breakdown_id in($po_break_down_id) and b.item_category_id=4 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 group by b.item_group_id");
			foreach($trimsDataArr as $row)	
			{
				$trims_array[$row[csf('item_group_id')]]['recv']=$row[csf('recv_qnty')];
				$trims_array[$row[csf('item_group_id')]]['iss']=$row[csf('issue_qnty')];
			}
			
			
			//$costing_per_arr=return_library_array( "select job_no, costing_per from wo_pre_cost_mst", "job_no", "costing_per"  );
			$trimsDataArr=sql_select("select c.po_break_down_id, max(a.costing_per) as costing_per, b.trim_group, max(b.cons_uom) as cons_uom, sum(b.cons_dzn_gmts) cons_dzn_gmts from wo_pre_cost_mst a, wo_pre_cost_trim_cost_dtls b , wo_pre_cost_trim_co_cons_dtls c where a.job_no=b.job_no and b.id=c.wo_pre_cost_trim_cost_dtls_id and b.status_active=1 and b.is_deleted=0 and c.po_break_down_id=$po_break_down_id group by b.trim_group, c.po_break_down_id");
			$i=1; $tot_accss_req_qnty=0; $tot_recv_qnty=0; $tot_iss_qnty=0; $tot_recv_bl_qnty=0; $tot_trims_left_over_qnty=0;
			foreach($trimsDataArr as $row)
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$dzn_qnty='';
				if($row[csf('costing_per')]==1) $dzn_qnty=12;
				else if($row[csf('costing_per')]==3) $dzn_qnty=12*2;
				else if($row[csf('costing_per')]==4) $dzn_qnty=12*3;
				else if($row[csf('costing_per')]==5) $dzn_qnty=12*4;
				else $dzn_qnty=1;
				
				$dzn_qnty=$dzn_qnty*$ratio;
        		$accss_req_qnty=($row[csf('cons_dzn_gmts')]/$dzn_qnty)*$tot_po_qnty;
				
				$trims_recv=$trims_array[$row[csf('trim_group')]]['recv'];
				$trims_issue=$trims_array[$row[csf('trim_group')]]['iss'];
				$recv_bl=$accss_req_qnty-$trims_recv;
				$trims_left_over=$trims_recv-$trims_issue;
			?>
            	<tr bgcolor="<? echo $bgcolor; ?>">
                    <td><p><? echo $item_library[$row[csf('trim_group')]]; ?>&nbsp;</p></td>
                    <td align="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?>&nbsp;</td>
                    <td align="right"><? echo number_format($accss_req_qnty,2,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($trims_recv,2,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($recv_bl,2,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($trims_issue,2,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($trims_left_over,2,'.',''); ?>&nbsp;</td>
                </tr>
            <?
				$tot_accss_req_qnty+=$accss_req_qnty;
				$tot_recv_qnty+=$trims_recv;
				$tot_recv_bl_qnty+=$recv_bl;
				$tot_iss_qnty+=$trims_issue;
				$tot_trims_left_over_qnty+=$trims_left_over;
				$i++;
			}
			$tot_trims_left_over_qnty_perc=($tot_trims_left_over_qnty/$tot_recv_qnty)*100;
			?>
            <tfoot>
                <tr>
                    <th align="right">&nbsp;</th>
                    <th align="right">Total</th>
                    <th align="right"><? echo number_format($tot_accss_req_qnty,0,'.',''); ?>&nbsp;</th>
                    <th align="right"><? echo number_format($tot_recv_qnty,0,'.',''); ?>&nbsp;</th>
                    <th align="right"><? echo number_format($tot_recv_bl_qnty,0,'.',''); ?>&nbsp;</th>
                    <th align="right"><? echo number_format($tot_iss_qnty,0,'.',''); ?>&nbsp;</th>
                    <th align="right"><? echo number_format($tot_trims_left_over_qnty,0,'.',''); ?>&nbsp;</th>
                </tr>
             </tfoot>
        </table>  
        </div>
    </fieldset>
<?	

	exit();
}
//Ex-Factory Delv. and Return
if($action=="ex_factory_popup")
{
 	echo load_html_head_contents("Ex-Factory Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	//echo $id;//$job_no;
	?>
	<div style="width:100%" align="center">
		<fieldset style="width:500px"> 
        <div class="form_caption" align="center"><strong>Ex-Factory Details</strong></div><br />
            <div style="width:100%"> 
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th width="35">SL</th>
                        <th width="90">Ex-fac. Date</th>
                        <th width="120">System /Challan no</th>
                        <th width="100">Ex-Fact. Del.Qty.</th>
                        <th width="">Ex-Fact.Return Qty.</th>
                       
                     </tr>   
                </thead> 	 	
            </table>  
        </div>
        <div style="width:100%; max-height:400px;">
            <table cellpadding="0" width="100%" cellspacing="0" border="1" rules="all" class="rpt_table">
                <?
                $i=1;
              
				$exfac_sql=("select b.challan_no,a.sys_number,b.ex_factory_date, 
				CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_qnty,
				CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_return_qnty 
				from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b  where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.po_break_down_id in($id) ");
                $sql_dtls=sql_select($exfac_sql);
                
                foreach($sql_dtls as $row_real)
                { 
                    if ($i%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";                               
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_l<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="35"><? echo $i; ?></td> 
                        <td width="90"><? echo change_date_format($row_real[csf("ex_factory_date")]); ?></td>
                        <td width="120"><? echo $row_real[csf("sys_number")]; ?></td>
                        <td width="100" align="right"><? echo $row_real[csf("ex_factory_qnty")]; ?></td>
                         <td width="" align="right"><? echo $row_real[csf("ex_factory_return_qnty")]; ?></td>
                    </tr>
                    <? 
                    $rec_qnty+=$row_real[csf("ex_factory_qnty")];
					 $rec_return_qnty+=$row_real[csf("ex_factory_return_qnty")];
                    $i++;
                }
                ?>
                <tfoot>
                <tr>
                    <th colspan="3">Total</th>
                    <th><? echo number_format($rec_qnty,2); ?></th>
                    <th><? echo number_format($rec_return_qnty,2); ?></th>
                </tr>
                <tr>
                 <th colspan="3">Total Balance</th>
                 <th colspan="2" align="right"><? echo number_format($rec_qnty-$rec_return_qnty,2); ?></th>
                </tr>
                </tfoot>
            </table>
        </div> 
		</fieldset>
	</div>    
	<?
    exit();	
}
//disconnect($con);
?>
