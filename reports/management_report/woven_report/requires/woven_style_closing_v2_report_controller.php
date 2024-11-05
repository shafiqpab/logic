<?php
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This file for daily Woven order entry report info.
Functionality	:	
JS Functions	:
Created by		:	Aziz
Creation date 	: 	18-07-2021
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
include('../../../../includes/common.php');
include('../../../../includes/class4/class.conditions.php');
include('../../../../includes/class4/class.reports.php');
include('../../../../includes/class4/class.yarns.php');
include('../../../../includes/class4/class.fabrics.php');
include('../../../../includes/class4/class.trims.php');

extract($_REQUEST);
 
$pc_time= add_time(date("H:i:s",time()),360);  
$pc_date = date("Y-m-d",strtotime(add_time(date("H:i:s",time()),360)));

$permission=$_SESSION['page_permission'];

//---------------------------------------------------- Start
if($action=="print_button_variable_setting")
    {
        $print_report_format_arr = return_library_array("select format_id,format_id from lib_report_template where template_name =$data and module_id=11 and report_id=73 and is_deleted=0 and status_active=1","format_id","format_id");
        echo "print_report_button_setting('".implode(',',$print_report_format_arr)."');\n";
        exit();  
    }

if ($action=="load_drop_down_buyer")
{


	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- All --", $selected, "" );     	 
	exit();
}

 if($action=="order_no_search_popup")
{
	echo load_html_head_contents("Order No Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$exdata=explode("_",$data);
	$companyID=$exdata[0];
	$type=$exdata[1];
	$buyer_id=$exdata[2];
	//echo $buyer_id.'DSDSD';
	$buy_conds="";
	if($buyer_id>0) $buy_conds=" and buy.id in ($buyer_id)";
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
		
		function js_set_value( str ) {
			
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
			<fieldset style="width:750px;">
				<table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<thead>
						<th width="140">Buyer</th>
						<th width="110">Search By</th>
						<th width="120" id="search_by_td_up">Please Enter Order No</th>
						<th width="130" colspan="2">Shipment Date</th>
						<th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:70px;"></th> 
						<input type="hidden" name="hide_order_no" id="hide_order_no" value="" />
						<input type="hidden" name="hide_order_id" id="hide_order_id" value="" />
						<input type="hidden" name="hide_pre_cost_ver_id" id="hide_pre_cost_ver_id" value="<?=$cbo_pre_cost_class;?>" />
					</thead>
					<tbody>
						<tr class="general">
							<td><?=create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buy_conds $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",0,"",0 ); ?></td>                 
							<td>	
								<?


								$search_by_arr=array(1=>"Order No",2=>"Style Ref",3=>"Job No");
								$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
								echo create_drop_down( "cbo_search_by", 110, $search_by_arr,"",0, "--Select--", "",$dd,0 );
								?>
							</td>     
							<td id="search_by_td"><input type="text" style="width:110px" class="text_boxes" name="txt_search_common" id="txt_search_common" /></td> 
							<td>
								<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" readonly></td>
								<td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date" readonly></td>	
								<td>
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<?=$companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+document.getElementById('hide_pre_cost_ver_id').value+'**'+'<?=$type; ?>', 'create_order_no_search_list_view', 'search_div', 'woven_style_closing_v2_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:70px;" />
								</td>
							</tr>
							<tr>
								<td colspan="6" valign="middle"><?=load_month_buttons(1); ?></td>
							</tr>
						</tbody>
					</table>
					<div style="margin-top:5px" id="search_div"></div>
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
	$pre_cost_ver_id=$data[6];
	$type=$data[7];

	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else $buyer_id_cond=" and a.buyer_name=$data[1]";
	
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";

	if($search_by==1) $search_field="b.po_number"; else if($search_by==2) $search_field="a.style_ref_no"; else $search_field="a.job_no";

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

	//$entry_form_cond="and c.entry_from in(111,158)";
	if($type==1)
	{
		$sql="select a.id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c where a.job_no=b.job_no_mst and a.job_no=c.job_no and  b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $date_cond $entry_form_cond group by a.id, a.job_no, a.insert_date, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no order by a.id Desc";

		echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No", "70,70,50,70,150","760","210",0, $sql , "js_set_value", "id,style_ref_no","",1,"company_name,buyer_name,0,0,0",$arr,"company_name,buyer_name,year,job_no_prefix_num,style_ref_no","",'','0,0,0,0,0','',1) ;
	}
	else if($type==2)
	{
		$sql="select b.id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c where a.job_no=b.job_no_mst and a.job_no=c.job_no and  b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $date_cond $entry_form_cond order by b.id, b.pub_shipment_date DESC";

		echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Po No, Shipment Date", "70,70,50,70,150,180","760","210",0, $sql , "js_set_value", "id,po_number","",1,"company_name,buyer_name,0,0,0,0,0",$arr,"company_name,buyer_name,year,job_no_prefix_num,style_ref_no,po_number,pub_shipment_date","",'','0,0,0,0,0,0,3','',1) ;
	}
	exit(); 
}
if ($action=="report_generate")
{	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$report_type=str_replace("'","",$report_type);
	$hide_job_id=str_replace("'","",$hide_job_id);

	if($report_type == 1 )
	{
		if($cbo_company_name==0 || $cbo_company_name=="")
		{ 
			$company_name="";$company_name2="";
		}
		else 
		{ 
			$company_name = "and a.company_name=$cbo_company_name";$company_name2 = "and a.company_id=$cbo_company_name";
		}//fabric_source//item_category
		
		

		if(str_replace("'","",$cbo_buyer_name)==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_cond="";
			}
			else
			{
				$buyer_id_cond="";$buyer_id_cond2="";
			}
		}
		else
		{
			$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
			$buyer_id_cond2=" and a.buyer_id=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
		}
		$style_cond='';$style_cond2='';
		if(str_replace("'","",$txt_style_ref)!="" && $hide_job_id!='')
		{
			$style_cond = " and a.id in($hide_job_id)";
		}
		else
		{
			$style_cond = " and a.style_ref_no like '%".str_replace("'","",$txt_style_ref)."%'";
		}
	
		/*if(str_replace("'","",$cbo_year)!=0)
		{
			if($db_type==0){$search_text.=" and YEAR(a.insert_date)=$cbo_year";}
			else if($db_type==2){$search_text.=" and to_char(a.insert_date,'YYYY')=$cbo_year";}
		}*/
		
		$txt_date_from=str_replace("'","",trim($txt_date_from));
		$txt_date_to=str_replace("'","",trim($txt_date_to));
		if($txt_date_from!="" && $txt_date_to!=""){
			if($db_type==0){
				$start_date = date('Y-m-d H:i:s',strtotime($txt_date_from)); 
				$end_date = date("Y-m-d",strtotime($txt_date_to));
				
					$date_cond =" and c.ex_factory_date between '".$start_date."' and '".$end_date."'";
					//$search_text2 .=" and c.pub_shipment_date between '".$start_date."' and '".$end_date."'";
			}
			else
			{
				$start_date = change_date_format(date("Y-m-d H:i:s",strtotime($txt_date_from)),'','',1);
				$end_date = change_date_format(date("Y-m-d H:i:s",strtotime($txt_date_to)),'','',1);
				$date_cond=" and c.ex_factory_date between '".$start_date."' and '".$end_date."'";
			}
		}
		
		//function for omit zero value
		function omitZero($value){
			if($value==0){
				return "";
				}
			else {
				return $value;
			}
		}
		//echo omitZero(10);
		
			$items_library=return_library_array( "select id, item_name from lib_garment_item where  status_active=1 and is_deleted=0", "id", "item_name"  );
			$buyer_library=return_library_array( "select id, buyer_name from lib_buyer where  status_active=1 and is_deleted=0", "id", "buyer_name"  );
			$company_lib=return_library_array( "select id, company_name from lib_company where  status_active=1 and is_deleted=0", "id", "company_name"  );
		
			if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
			else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
		
			$sql="select a.id as job_id, a.buyer_name,a.total_set_qnty,$year_field,a.job_no_prefix_num ,a.job_no,a.style_ref_no,b.po_number,a.set_smv,a.set_break_down,a.order_uom,a.gmts_item_id,b.id,a.buyer_name,(b.po_quantity*a.total_set_qnty) as po_quantity,b.po_quantity as  po_qty,b.plan_cut,b.unit_price,b.pub_shipment_date,b.po_received_date,b.insert_date,b.grouping, b.file_no,b.is_confirmed,b.inserted_by,b.po_total_price,b.details_remarks from wo_po_details_master a, wo_po_break_down b,pro_ex_factory_mst c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id  $company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $date_cond $buyer_id_cond $style_cond  order by c.ex_factory_date desc"; 		  		 
		$order_sql_result = sql_select($sql) or die(mysql_error());
		foreach($order_sql_result as $row){
			//$order_data_arr[$rows[csf('buyer_name')]][]=$rows;
			//$order_data_arr[]=$rows;
			$order_id_arr[$row[csf('id')]]=$row[csf('id')];
			$po_buyer_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$po_buyer_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
			$job_id_arr[$row[csf("job_id")]]=$row[csf("job_id")];
			$poJob_wise_qty[$row[csf("id")]]=$row[csf("job_no")];
		}
		unset($order_sql_result);
		
		$job_cond_for_in=where_con_using_array($job_id_arr,0,"c.job_id");
		$job_cond_for_in2=where_con_using_array($job_id_arr,0,"a.id");
	
	
		$po_id_list_arr=array_chunk($order_id_arr,999);
	
		$sql_exf_last="select c.job_id,max(b.ex_factory_date) as ex_factory_date,
		sum(CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END)-sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as qnty 
		from pro_ex_factory_mst b,wo_po_break_down c,wo_po_details_master a where c.id=b.po_break_down_id and a.id=c.job_id and  b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 $job_cond_for_in  group by c.job_id  ";
		$result_sql_exf_last=sql_select($sql_exf_last); 
		foreach($result_sql_exf_last as $row)
		{
			$last_ex_factory_date_arr[$row[csf("job_id")]]=$row[csf("ex_factory_date")];
	
		}
		unset($result_sql_exf_last);
		//print_r($last_ex_factory_date_arr);
		$sql_exf="select b.po_break_down_id,b.ex_factory_date,c.job_no_mst as job_no,
		(CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END)-(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as qnty 
		from pro_ex_factory_mst b,wo_po_break_down c where b.po_break_down_id=c.id and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 $job_cond_for_in  order by b.po_break_down_id ";
		$result_sql_exf=sql_select($sql_exf); 
		foreach($result_sql_exf as $row)
		{
			$ex_factory_date=$row[csf("ex_factory_date")];
			//$poJob=$row[csf("job_no")];
			$poJob=$poJob_wise_qty[$row[csf("po_break_down_id")]];
			//$newdate =change_date_format($ex_factory_date,'','',1);
			$ex_factory_arr[$row[csf("po_break_down_id")]]+=$row[csf("qnty")];
			$exfact_conf_arr[$poJob][$row[csf("po_break_down_id")]]=$row[csf("ex_factory_date")];
		}
		$sql_main="select a.id as job_id, a.buyer_name,a.total_set_qnty,$year_field,a.job_no_prefix_num ,a.job_no,a.style_ref_no,b.po_number,a.set_smv,a.set_break_down,a.order_uom,a.gmts_item_id,b.id,a.buyer_name,(b.po_quantity*a.total_set_qnty) as po_quantity,b.po_quantity as  po_qty,b.plan_cut,b.unit_price,b.pub_shipment_date,b.po_received_date,b.insert_date,b.shiping_status,b.grouping, b.file_no,b.is_confirmed,b.inserted_by,b.po_total_price,b.details_remarks from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst   $company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  $job_cond_for_in2 order by b.id,b.shiping_status desc"; 		  		 
		$sql_main_result = sql_select($sql_main) or die(mysql_error());
		foreach($sql_main_result as $row){
			//$order_data_arr[$rows[csf('buyer_name')]][]=$rows;
			//$order_data_arr[]=$rows;
			$po_id_arr[$row[csf('id')]]=$row[csf('id')];
			//$po_buyer_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			//$po_buyer_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
			//$job_id_arr[$row[csf("job_id")]]=$row[csf("job_id")];
		}
		//	unset($sql_main_result);
		$po_cond_for_in=where_con_using_array($po_id_arr,0,"b.order_id");
		$po_cond_for_in2=where_con_using_array($po_id_arr,0,"b.po_breakdown_id");
		$po_cond_for_in3=where_con_using_array($po_id_arr,0,"a.po_breakdown_id");
		$po_cond_for_in4=where_con_using_array($po_id_arr,0,"b.po_id");
		$po_cond_for_in5=where_con_using_array($po_id_arr,0,"a.po_break_down_id");
		
		/*$recvData=sql_select("select a.po_breakdown_id, (quantity) as grey_prod_qnty from order_wise_pro_details a, pro_grey_prod_entry_dtls b where a.dtls_id=b.id and a.prod_id=b.prod_id and a.entry_form =2 and a.is_deleted=0 and a.status_active=1 $po_cond_for_in3 ");
		foreach($recvData as $row)
		{
			$knit_prod_qty_array[$row[csf('po_breakdown_id')]]+=$row[csf('grey_prod_qnty')];
		}
		unset($recvData);*/

		//   $sql="select 
		// 	a.po_break_down_id, 
		// 	SUM(CASE WHEN a.production_type=1 THEN b.production_qnty END) as totalcut,
		// 	SUM(CASE WHEN a.production_type=4 THEN b.production_qnty END) as totalinput,
		// 	SUM(CASE WHEN a.production_type=5 THEN b.production_qnty ELSE 0 END) as totalsewing,
		// 	SUM(CASE WHEN a.production_type=8 THEN b.production_qnty ELSE 0 END) as totalfinish,
		// 	SUM(CASE WHEN a.production_type=2 THEN b.production_qnty ELSE 0 END) as totalprsend,
		// 	SUM(CASE WHEN a.production_type=3 THEN b.production_qnty ELSE 0 END) as totalprrec,
		// 	SUM(CASE WHEN a.production_type=63 THEN b.production_qnty ELSE 0 END) as totalembsend,
		// 	SUM(CASE WHEN a.production_type=64 THEN b.production_qnty ELSE 0 END) as totalembrec
		// from pro_garments_production_mst a,pro_garments_production_dtls b
		// WHERE  a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 $po_cond_for_in5 group by po_break_down_id";
		$sql="SELECT   a.po_break_down_id, 
		SUM (CASE WHEN  a.production_type = 1 AND d.production_type = 1 THEN d.production_qnty ELSE 0 END) AS totalcut,
		SUM (CASE WHEN  a.production_type = 4 AND d.production_type = 4 THEN d.production_qnty ELSE 0 END) AS totalinput,
		SUM (CASE WHEN  a.production_type = 5 AND d.production_type = 5 THEN d.production_qnty ELSE  0  END)  AS totalsewing,
		SUM (CASE WHEN  a.production_type = 2 AND d.production_type = 2 AND a.embel_name = 1 THEN d.production_qnty ELSE 0 END) AS totalprsend,	
		SUM (CASE WHEN  a.production_type = 3 AND d.production_type = 3 AND a.embel_name = 1 THEN d.production_qnty ELSE 0 END) AS totalprrec,
		SUM (CASE WHEN  a.production_type = 3 AND d.production_type = 3 AND a.embel_name = 2	THEN d.production_qnty ELSE 0 END) AS today_emb_receive,
		SUM (CASE WHEN  a.production_type = 63 AND d.production_type = 63 	THEN d.production_qnty ELSE 0 END) AS totalembsend,
		SUM (CASE WHEN  a.production_type = 64 AND d.production_type = 64 	THEN d.production_qnty ELSE 0 END) AS totalembrec,
		SUM (CASE WHEN  a.production_type = 8 AND d.production_type = 8 THEN d.production_qnty ELSE 0 END) AS totalfinish,	
		SUM (CASE WHEN  a.production_type = 2 AND d.production_type = 2 AND a.embel_name = 2 THEN d.production_qnty  ELSE  0 END) AS total_emb_issue

		FROM wo_po_details_master        b,
			wo_po_break_down            c,
			pro_garments_production_mst a,
			pro_garments_production_dtls d,
			wo_po_color_size_breakdown  e
		WHERE     b.job_no = c.job_no_mst
			AND c.id = a.po_break_down_id
			AND a.id = d.mst_id
			AND d.color_size_break_down_id = e.id
			AND a.po_break_down_id = e.po_break_down_id
			AND a.status_active = 1
			AND a.is_deleted = 0
			AND d.status_active = 1
			AND d.is_deleted = 0
			AND b.status_active = 1
			AND b.is_deleted = 0
			AND c.status_active = 1
			AND c.is_deleted = 0
			AND e.status_active = 1
			AND e.is_deleted = 0
			AND b.company_name = $cbo_company_name
			$po_cond_for_in5 
		GROUP BY a.po_break_down_id";

		$dataArray=sql_select($sql);
		foreach($dataArray as $row)
		{  
			$cut_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totalcut')];
			$sewing_in_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totalinput')];
			$sewing_out_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totalsewing')];
			$sewing_finish_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totalfinish')];
			$emb_issue_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totalembsend')];
			$emb_rec_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totalembrec')];
			$print_issue_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totalprsend')];
			$print_rec_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totalprrec')];
		
		}
		unset($dataArray);
					
		//Job Wise Data create......................................start;
		$start_date=strtotime($start_date);
		$end_date=strtotime($end_date);
		$partialship_job_count = array();
		foreach($sql_main_result as $row)
		{
			
			//echo $row[csf('po_quantity')].'D';
			//echo $ex_factory_arr[$row[csf('id')]].'DX';
			$job_wise_DataArr[$row[csf('job_no')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
			$job_wise_DataArr[$row[csf('job_no')]]['job_id']=$row[csf('job_id')];
			$job_wise_DataArr[$row[csf('job_no')]]['po_id'].=$row[csf('id')].',';
			$job_wise_DataArr[$row[csf('job_no')]]['buyer_name']=$row[csf('buyer_name')];
			$job_wise_DataArr[$row[csf('job_no')]]['style_ref_no']=$row[csf('style_ref_no')];
			$job_wise_DataArr[$row[csf('job_no')]]['year']=$row[csf('year')];
			$job_wise_DataArr[$row[csf('job_no')]]['po_quantity']+=$row[csf('po_quantity')];
			$job_wise_DataArr[$row[csf('job_no')]]['year']=$row[csf('year')];
			$job_wise_DataArr[$row[csf('job_no')]]['pub_shipment_date'].=$row[csf('pub_shipment_date')].',';
			$job_wise_DataArr[$row[csf('job_no')]]['sew_in']+=$sewing_in_qnty_array[$row[csf('id')]];
			$job_wise_DataArr[$row[csf('job_no')]]['sew_out']+=$sewing_out_qnty_array[$row[csf('id')]];
			$job_wise_DataArr[$row[csf('job_no')]]['ex_factQty']+=$ex_factory_arr[$row[csf('id')]];
			$job_wise_DataArr[$row[csf('job_no')]]['cut_qty']+=$cut_qnty_array[$row[csf('id')]];
			$job_wise_DataArr[$row[csf('job_no')]]['fin_qty']+=$sewing_finish_qnty_array[$row[csf('id')]];
			$job_wise_DataArr[$row[csf('job_no')]]['prod_qty']+=$knit_prod_qty_array[$row[csf('id')]];


			
			$is_confirmed=$row[csf("is_confirmed")];
			
			if($is_confirmed==1)
			{
				$shipId=$row[csf("shiping_status")];
				
				$exfact_date=strtotime($exfact_conf_arr[$row[csf("job_no")]][$row[csf("id")]]);
				//echo $row[csf("id")] . "**".$exfact_conf_arr[$row[csf("job_no")]][$row[csf("id")]]."=".$last_ex_factory_date_arr[$row[csf("job_id")]]."==<br />";
				$last_ex_factory_date=strtotime($last_ex_factory_date_arr[$row[csf("job_id")]]);
				//echo $shipId.'='.$exfact_date.',';
				//echo $exfact_date.'='.$shipId.'='.$row[csf("id")].'<br>';
				if($exfact_date=='' && $shipId!=3)
				{
				
					unset($fullship_job_po_chk_arr[$row[csf("job_no")]]);
					$is_partialship_job_count[$row[csf("job_no")]]=1;
					
					//echo $exfact_date.'X'.$row[csf("id")].'<br>'; 
				}
				else
				{
					/*
					po1 - ex full
					po2 - ex partial
					po3 - ex full 
					*/
					//echo $shipId.'A'.$row[csf("id")].'<br>';
					//echo $last_ex_factory_date.'B'.$start_date.'B'.$end_date.'<br>';
					if($start_date!="" && $end_date !="") 
					{
						//echo $last_ex_factory_date_arr[$row[csf("job_id")]].'='.$row[csf("id")].'='.$is_partialship_job_count[$row[csf("job_no")]].'<br>';
						
						if($shipId==3 && ($last_ex_factory_date>=$start_date && $last_ex_factory_date<=$end_date) && $is_partialship_job_count[$row[csf("job_no")]]!=1)
						{
							if($partialship_job_count[$row[csf("job_no")]]!=1)
							{	
								$fullship_job_po_chk_arr[$row[csf("job_no")]]=$last_ex_factory_date;
								$partialship_job_count[$row[csf("job_no")]]=0;
								//echo $shipId.'A'.$row[csf("id")].'<br>';
							}
							
						}
						else
						{
							unset($fullship_job_po_chk_arr[$row[csf("job_no")]]);
							$partialship_job_count[$row[csf("job_no")]]++;
							//echo $shipId.'B'.$row[csf("id")].'<br>';
						}
					}
					else
					{
						//$last_ex_factory_date_arr[$row[csf("job_id")]].'d';
						//echo $shipId.'AA'.$exfact_date.'<br>';
					//	echo $partialship_job_count2[$row[csf("job_no")]].'<br>';
						if($is_partialship_job_count[$row[csf("job_no")]]==1)
							{
								unset($fullship_job_po_chk_arr[$row[csf("job_no")]]);
								//$partialship_job_count2[$row[csf("job_no")]]=0;
							}
							else if($shipId!=3)
							{
								unset($fullship_job_po_chk_arr[$row[csf("job_no")]]);
								$is_partialship_job_count[$row[csf("job_no")]]=1;
								//echo $shipId.'D';;
								//$partialship_job_count2[$row[csf("job_no")]]=0;
							}
							else
							{
								
								if($is_partialship_job_count[$row[csf("job_no")]]==1)
								{
									unset($fullship_job_po_chk_arr[$row[csf("job_no")]]);
									//$partialship_job_count2[$row[csf("job_no")]]=0;
								}
								else
								{
									$fullship_job_po_chk_arr[$row[csf("job_no")]]=$last_ex_factory_date;
								}
									//echo $shipId.'T';;
								//$partialship_job_count2[$row[csf("job_no")]]=0;
							}
						
					}
				}
			}
			
			
		}
		//print_r($is_partialship_job_count);
		//==================Main query=====
		foreach($sql_main_result as $row)
		{
			$ex_factoryQty=$ex_factory_arr[$row[csf("id")]];
			$ship_id=$row[csf("shiping_status")];
			
			if($fullship_job_po_chk_arr[$row[csf("job_no")]] && $ship_id==3 && $ex_factoryQty>0) //Fullship Check
			{
			$job_wise_arr[$row[csf('job_no')]]['job_no_prefix_num']=$job_wise_DataArr[$row[csf('job_no')]]['job_no_prefix_num'];
			$job_wise_arr[$row[csf('job_no')]]['job_id']=$job_wise_DataArr[$row[csf('job_no')]]['job_id'];
			$job_wise_arr[$row[csf('job_no')]]['buyer_name']=$job_wise_DataArr[$row[csf('job_no')]]['buyer_name'];
			$job_wise_arr[$row[csf('job_no')]]['style_ref_no']=$job_wise_DataArr[$row[csf('job_no')]]['style_ref_no'];
			$job_wise_arr[$row[csf('job_no')]]['year']=$row[csf('year')];
			$job_wise_arr[$row[csf('job_no')]]['po_quantity']=$job_wise_DataArr[$row[csf('job_no')]]['po_quantity'];
			$job_wise_arr[$row[csf('job_no')]]['year']=$row[csf('year')];
			$job_wise_arr[$row[csf('job_no')]]['po_id'].=$row[csf('id')].',';
			$job_wise_arr[$row[csf('job_no')]]['pub_shipment_date'].=$row[csf('pub_shipment_date')].',';
			$job_wise_arr[$row[csf('job_no')]]['sew_in']=$job_wise_DataArr[$row[csf('job_no')]]['sew_in'];
			$job_wise_arr[$row[csf('job_no')]]['sew_out']=$job_wise_DataArr[$row[csf('job_no')]]['sew_out'];
			$job_wise_arr[$row[csf('job_no')]]['ex_factQty']=$job_wise_DataArr[$row[csf('job_no')]]['ex_factQty'];
			$job_wise_arr[$row[csf('job_no')]]['cut_qty']=$job_wise_DataArr[$row[csf('job_no')]]['cut_qty'];
			$job_wise_arr[$row[csf('job_no')]]['fin_qty']=$job_wise_DataArr[$row[csf('job_no')]]['fin_qty'];
				$job_arr[$row[csf('job_no')]]=$row[csf('job_no')];
			}
			
		}
		//print_r($job_wise_arr);
		//Summary Data create......................................end;	


		//=============================================Wash===================================================

		$wash_sql="SELECT a.id,a.garments_nature,a.company_id,a.po_break_down_id,b.job_no_mst,b.job_id,a.production_source,a.embel_name,a.embel_type,a.production_quantity,a.production_source,a.production_type,a.carton_qty,a.alter_qnty,a.reject_qnty from pro_garments_production_mst a,wo_po_break_down b where  b.id=a.po_break_down_id and a.production_type in (2,3) and a.embel_name=3 and a.status_active=1 and a.is_deleted=0 ".where_con_using_array($job_arr,1,'b.job_no_mst')." order by a.id";

		$wash_data=sql_select($wash_sql);

		foreach($wash_data as $val){
			if($val[csf('production_type')]==2){
				$job_wise_wash_qty[$val[csf('job_id')]]['send_qty']+=$val[csf('production_quantity')];
			}else{
				$job_wise_wash_qty[$val[csf('job_id')]]['recv_qty']+=$val[csf('production_quantity')];
			}
		}

		//=============================================printing===================================================

			$printing_sql="SELECT a.id,a.garments_nature,a.company_id,a.po_break_down_id,b.job_no_mst,b.job_id,a.production_source,a.embel_name,a.embel_type,a.production_quantity,a.production_source,a.production_type,a.carton_qty,a.alter_qnty,a.reject_qnty from pro_garments_production_mst a,wo_po_break_down b where  b.id=a.po_break_down_id and a.production_type in (2,3) and a.embel_name=1 and a.status_active=1 and a.is_deleted=0 ".where_con_using_array($job_arr,1,'b.job_no_mst')." order by a.id";

		$printing_data=sql_select($printing_sql);

		foreach($printing_data as $val){
			if($val[csf('production_type')]==2){
				$job_wise_printing_qty[$val[csf('job_id')]]['send_qty']+=$val[csf('production_quantity')];
			}else{
				$job_wise_printing_qty[$val[csf('job_id')]]['recv_qty']+=$val[csf('production_quantity')];
			}
		}

		//=============================================embroidery===================================================

		$embroidery_sql="SELECT a.id,a.garments_nature,a.company_id,a.po_break_down_id,b.job_no_mst,b.job_id,a.production_source,a.embel_name,a.embel_type,a.production_quantity,a.production_source,a.production_type,a.carton_qty,a.alter_qnty,a.reject_qnty from pro_garments_production_mst a,wo_po_break_down b where  b.id=a.po_break_down_id and a.production_type in (2,3) and a.embel_name=2 and a.status_active=1 and a.is_deleted=0 ".where_con_using_array($job_arr,1,'b.job_no_mst')." order by a.id";

		$embroidery_data=sql_select($embroidery_sql);

		foreach($embroidery_data as $val){
			if($val[csf('production_type')]==2){
				$job_wise_embroidery_qty[$val[csf('job_id')]]['send_qty']+=$val[csf('production_quantity')];
			}else{
				$job_wise_embroidery_qty[$val[csf('job_id')]]['recv_qty']+=$val[csf('production_quantity')];
			}
		}


		ob_start();	
		$width=2640;
		?>
		
		<fieldset>
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr><td colspan="20" align="center" style="font-size:22px;"><? echo str_replace("'","",$report_title);;?></td></tr>
				<tr>
					
					<td colspan="20" align="center" style="font-size:16px; font-weight:bold;">
						<? echo $company_lib[$cbo_company_name];?>
					</td>
				</tr>
				<? if($txt_date_from!='' && $txt_date_to!=''){?>
				<tr>
					<td colspan="20" align="center" style="font-size:14px;">
						From: <? echo change_date_format($txt_date_from);?> 
						To: <? echo change_date_format($txt_date_to);?>
					</td>
				</tr>
				<? }?>
				<tr>
					<td colspan="20" align="left" style="font-size:14px; color: red; font-weight:bold;">
						<? echo "This Report is Generated based on Last Ex-Factory date Against Style All PO Close";?>
					</td>
				</tr>
			</table>
			
			<table id="table_header_1" class="rpt_table" width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<tr>
					<th colspan="8" width="650">&nbsp;  </th>
					<th colspan="2"  width="160">Cutting </th>
					<th colspan="4">Print </th>
					<th colspan="4">Embroidery </th>
					<th colspan="4">Sewing </th>
					<th colspan="4">Wash </th>
					<th colspan="3">Packing & Finishing	 </th>
					<th>&nbsp;  </th>
					<th colspan="2">Production </th>
					</tr>
					<tr style="font-size:12px"> 
						<th width="30">Sl</th>	
						<th width="100">Buyer</th> 
					
						<th width="100">Job No</th>
						<th width="60">Job Year</th>
						<th width="120">Style No</th>
						<th width="80">Style Qty</th>
						<th width="80">Last Ship<br> Date</th>
						<th width="80">Last Ex-Factory<br> Date</th>
						
						<th width="80">TTL Cut Qty</th>	
						<th width="80" title="Cut Qty/PO Qty*100">Cutting %</th>

						<th width="80">Print Send</th>	
						<th width="80">Print Received</th>
						<th width="80" title="Print Sent-Print Received">Print Process<br> Loss</th>	
						<th width="80" title="(Print Proces loss/Print sent)*100">Print Process<br> Loss (%)</th>

						<th width="80">Embroidery Send</th>	
						<th width="80">Embroidery Received</th>
						<th width="80" title="Embroidery Sent-Embroidery Received">Embroidery Process<br> Loss </th>	
						<th width="80" title="(Embroidery Proces loss/Embroidery sent)*100">Embroidery Process<br> Loss (%)</th>

						<th width="80">Sewing Input</th>	
						<th width="80">Sewing Output</th>
						<th width="80" title="Sew In-Out">Sew. Process <br>Loss (Pcs)</th>	
						<th width="80" title="Sew Proces loss/Sew in*100">Sew. Process<br> Loss (%)</th>
						
						<th width="80">Wash Sent</th>	
						<th width="80">Wash Received</th>
						<th width="80" title="Wash Sent-Wash Received">Wash Process<br> Loss (Pcs)</th>	
						<th width="80" title="(Wash Proces loss/Wash sent)*100">Wash Process<br> Loss (%)</th>

				
						
						<th width="80" title="">Packing & Finish Qty</th>	
						<th width="80" title="Fin Qty-Ex Fact Qty">Fin Process<br>Loss (Pcs)</th>	
						<th width="80" title="Fin Process/Fin Qty*100">Fin Process<br>Loss (%)</th>	
						<th width="80">Ex-Factory</th>	
					
						<th width="80" title="Tot Cut-Ex-Fact Qty">Prod.<br>Process Loss(Pcs)</th>
						<th style="" title="Prod. Process/Cut Qty*100">Prod.<br>Process Loss(%)</th>	
						
					</tr>                            	
				</thead>
			</table>
			<div style="width:<? echo $width+20;?>px; max-height:400px; overflow-y:scroll" id="scroll_body">
				<table class="rpt_table" width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
				
					<?php
					$i=1;$tot_fin_qty=$tot_fin_process_loss=$tot_ex_factQty=$tot_prod_qty=$tot_sewing_prod_p_loss=0;
					foreach($job_wise_arr as $job_no=>$row)
					{
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
						
						
						$po_id=rtrim($row[('po_id')],',');
						$po_ids=implode(",",array_unique(explode(",", $po_id)));
						
						$pub_shipment_dateAll=rtrim($row[('pub_shipment_date')],',');
						$pub_shipment_dateArr=array_unique(explode(",", $pub_shipment_dateAll));
						$last_shipDate=max($pub_shipment_dateArr);
						$wash_process_loss=$job_wise_wash_qty[$row[("job_id")]]['send_qty']-$job_wise_wash_qty[$row[("job_id")]]['recv_qty'];
						$printing_process_loss=$row[('cut_qty')]-$job_wise_printing_qty[$row[("job_id")]]['recv_qty'];
						$embroidery_process_loss=$row[('cut_qty')]-$job_wise_embroidery_qty[$row[("job_id")]]['recv_qty'];
					
					
						?>	
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="font-size:12px; cursor:pointer;" valign="middle">
							<td width="30"><? echo $i;?></td>	
							<td width="100"><p style=" word-wrap:break-word;"><? echo $buyer_library[$row[('buyer_name')]];?></p></td>
							<td width="100"><p><? echo $row[('job_no_prefix_num')];?></p></td>
							<td width="60"><p><? echo $row[('year')];?></p></td> 
							<td width="120"><p style=" word-wrap:break-word;"><? echo $row[('style_ref_no')];?></p></td>	
							<td width="80" align="right" style="word-wrap:break-word;"><p><? echo number_format($row[('po_quantity')],0);?></p></td>	
							<td width="80"><p>
							<?
								
								echo date("d-M-Y", strtotime($last_shipDate));
							?>
							</p></td>	
							<td width="80" style="word-wrap:break-word;"><p><? echo $last_ex_factory_date_arr[$row[("job_id")]];?></p></td>
							<td width="80" align="right" style="word-wrap:break-word;"><p><? echo number_format($row[('cut_qty')],0);?></p></td>
							<td width="80" align="right"  style="word-wrap:break-word;"><p><? if($row[('cut_qty')])  echo number_format((($row[('cut_qty')]/$row[('po_quantity')])*100),2).'% ';else echo " ";?></p></td>	

							<td width="80" align="right"><? echo omitZero(number_format($job_wise_printing_qty[$row[("job_id")]]['send_qty'],0));?></td>
							<td width="80" align="right"><? echo  omitZero(number_format($job_wise_printing_qty[$row[("job_id")]]['recv_qty']));;?></td>
							<td width="80" align="right"><? echo  omitZero(number_format($printing_process_loss,0));;?></td>	                    
							<td width="80" align="right" ><?  if($printing_process_loss)  echo  number_format(($printing_process_loss/$row[('cut_qty')]*100),2).'% ' ; else echo " ";?></td>	

							<td width="80" align="right"><? echo omitZero(number_format($job_wise_embroidery_qty[$row[("job_id")]]['send_qty'],0));?></td>
							<td width="80" align="right"><? echo  omitZero(number_format($job_wise_embroidery_qty[$row[("job_id")]]['recv_qty']));;?></td>
							<td width="80" align="right"><? echo  omitZero(number_format($embroidery_process_loss,0));;?></td>	                    
							<td width="80" align="right" ><?  if($embroidery_process_loss)  echo  number_format(($embroidery_process_loss/$row[('cut_qty')]*100),2).'% ' ; else echo " ";?></td>	
						
							<td width="80" align="right"><? echo omitZero(number_format($row[('sew_in')],0));?></td>
							<td width="80" align="right"><? echo number_format($row[('sew_out')],0);?></td>
							<td width="80" align="right"><? $sewing_prod_p_loss=number_format($row[('sew_in')]-$row[('sew_out')],0);echo $sewing_prod_p_loss;?></td>	                    
							<td width="80" align="right" ><?  if($sewing_prod_p_loss) echo  number_format((($sewing_prod_p_loss/$row[('sew_in')])*100),2).'% ' ; else echo " ";?></td>	

							<td width="80" align="right"><? echo omitZero(number_format($job_wise_wash_qty[$row[("job_id")]]['send_qty'],0));?></td>
							<td width="80" align="right"><? echo  omitZero(number_format($job_wise_wash_qty[$row[("job_id")]]['recv_qty']));;?></td>
							<td width="80" align="right"><? echo  omitZero(number_format($wash_process_loss,0));;?></td>	                    
							<td width="80" align="right" ><?  if($wash_process_loss)  echo  number_format((($wash_process_loss/$job_wise_wash_qty[$row[("job_id")]]['send_qty'])*100),2).'% ' ; else echo " ";?></td>	

							<td width="80" align="right" <? //echo $fabAvlBgColor;?> title="<? //echo $fabAvlPer;?>">
								<? echo omitZero(number_format($row[('fin_qty')]),0);?>
							</td>	
							<td width="80" align="right"  ><? echo $fin_process_loss=omitZero(number_format($row[('fin_qty')]-$row[('ex_factQty')],0));?></td>
							<td width="80" align="right" ><?   
							if($fin_process_loss)  echo  number_format((($fin_process_loss/$row[('fin_qty')])*100),2).'% ' ;
							else echo " ";//omitZero(number_format($lay_cut_data[$row[csf('job_no')]][$row[csf('buyer_name')]],0));?></td>	
							<td width="80" align="right"><a href="##" onClick="generate_ex_factory_popup('style_ex_factory_popup','<?=$job_no_prefix_num;?>','<?=$po_ids; ?>','<?=str_replace("'","",$txt_date_from) ?>','<?=str_replace("'","",$txt_date_to) ?>','850px')"><?=omitZero(number_format($row['ex_factQty'],0)); ?></a><? //echo omitZero(number_format($row[('ex_factQty')],0));?></td>
							<?
							$prod_qty=$row[('cut_qty')]-$row[('ex_factQty')];
							if($prod_qty) 
							{
							$prod_qty_per=($prod_qty/$row[('cut_qty')])*100;
							$per='%';
							} 
							else { $prod_qty_per=""; $per='';}
							?>
							<td width="80" align="right" ><? echo omitZero(number_format($prod_qty,0));?></td>	
							<td width="" align="right" ><? echo omitZero(number_format($prod_qty_per,2)).$per;?></td>	
						
						</tr>
						<?
					
						$tot_po_quantity+=$row[('po_quantity')];
					
				
						//$tot_blance+=round($blance);
						
						//	$tot_ff_qnty+=round($ff_qnty_array[$row[csf('id')]]);
						$tot_cut_qnty+=$row[('cut_qty')];
						$tot_sew_in+=$row[('sew_in')];//$lay_cut_data[$row[csf('job_no')]][$row[csf('buyer_name')]];
						$tot_sew_out+=$row[('sew_out')];
						// $tot_print_in+=$row[('print_in')];
						// $tot_print_out+=$row[('print_out')];
						// $tot_embroidery_in+=$row[('embroidery_in')];
						// $tot_embroidery_out+=$row[('embroidery_out')];
						//$tot_fin_process_loss+=$fin_process_loss;
						$tot_sewing_in_qnty+=$row[('sew_in')];
						$tot_sewing_out_qnty+=$row[('sew_out')];
						$tot_sewing_finish_qnty+=$row[('fin_qty')];
						$tot_sewing_prod_p_loss+=$row[('sew_in')]-$row[('sew_out')];
						// $tot_print_prod_p_loss+=$row[('cut_qty')]-$row[('print_out')];
						// $tot_embroidery_prod_p_loss+=$row[('cut_qty')]-$row[('embroidery_out')];
						//$tot_print_prod_p_loss+=$row[('cut_qty')]-$row[('print_out')];
						//$tot_embroidery_prod_p_loss+=$row[('cut_qty')]-[$row[('embroidery_out')];
						$tot_fin_process_loss+=$row[('fin_qty')]-$row[('ex_factQty')];
						$tot_prod_qty+=$prod_qty;
						$tot_fin_qty+=$row[('fin_qty')];
						$tot_ex_factQty+=$row[('ex_factQty')];

						$tot_wash_send+=$job_wise_wash_qty[$row[("job_id")]]['send_qty'];
						$tot_wash_recv+=$job_wise_wash_qty[$row[("job_id")]]['recv_qty'];
						$tot_wash_process_pcs+=$wash_process_loss;
						$tot_wash_process_lost+=($wash_process_loss/$job_wise_wash_qty[$row[("job_id")]]['send_qty'])*100;

						$tot_printing_send+=$job_wise_printing_qty[$row[("job_id")]]['send_qty'];
						$tot_printing_recv+=$job_wise_printing_qty[$row[("job_id")]]['recv_qty'];
						$tot_printing_process_pcs+=$printing_process_loss;
						//$tot_printing_process_lost+=($printing_process_loss/$job_wise_printing_qty[$row[("job_id")]]['send_qty'])*100;

						$tot_embroidery_send+=$job_wise_embroidery_qty[$row[("job_id")]]['send_qty'];
						$tot_embroidery_recv+=$job_wise_embroidery_qty[$row[("job_id")]]['recv_qty'];
						$tot_embroidery_process_pcs+=$embroidery_process_loss;
						//$tot_embroidery_process_lost+=($embroidery_process_loss/$job_wise_embroidery[$row[("job_id")]]['send_qty'])*100;
						
						$i++;
					} 
				?> 
				</table>
			</div> 
			<table class="tbl_bottom" width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
				<tr>
					<td width="30">&nbsp; </td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>	
					<td width="60">&nbsp;</td>	
					<td width="120">Total:</td>	
					<td width="80" align="right"id="po_qty_th"><? echo number_format($tot_po_quantity,0);?></td>
					<td width="80">&nbsp;</td>
					<td width="80">&nbsp;</td>

					<td width="80" align="right"id="cut_qty_th"><? echo number_format($tot_cut_qnty,0);?></td>
					<td width="80"><? if($tot_cut_qnty) echo number_format(($tot_cut_qnty/$tot_po_quantity)*100,2)."%";else echo " ";?></td>
					
					<td width="80" align="right"> <p style="word-wrap:break-word;"><? echo omitZero(number_format($tot_printing_send,0));?></p></td>	
					<td width="80" align="right" id="sew_in_qty_th"><p><? echo omitZero(number_format($tot_printing_recv,0));?></p></td>				
					<td width="80" align="right" class=""   id="sew_ploss_qty_th"><p class="td_color"><? echo omitZero(number_format($tot_printing_process_pcs,0));?> </p></td>
					<td width="80" align="right">
					<p style="word-wrap:break-word;"><?	//if($tot_printing_process_pcs) echo number_format($tot_printing_process_pcs/$tot_printing_send*100,2)."%";else echo " ";?></p>
					</td>	

					<td width="80" align="right"> <p style="word-wrap:break-word;"><? echo omitZero(number_format($tot_embroidery_send,0));?></p></td>	
					<td width="80" align="right" id="sew_in_qty_th"><p><? echo omitZero(number_format($tot_embroidery_recv,0));?></p></td>				
					<td width="80" align="right" class=""   id="sew_ploss_qty_th"><p class="td_color"><? echo omitZero(number_format($tot_embroidery_process_pcs,0));?> </p></td>
					<td width="80" align="right">
					<p style="word-wrap:break-word;"><?	//if($tot_embroidery_process_pcs) echo number_format($tot_embroidery_process_pcs/$tot_embroidery_send*100,2)."%";else echo " ";?></p>
					</td>

					<td width="80" align="right"> <p   style="word-wrap:break-word;"><? echo number_format($tot_sew_in,0);?></p></td>	
					<td width="80" align="right" id="sew_in_qty_th"><p><? echo number_format($tot_sew_out,0);?></p></td>		
					<td width="80" align="right" class=""   id="sew_ploss_qty_th"><p class="td_color"><? echo number_format($tot_sewing_prod_p_loss,0);?> </p></td>
					<td width="80" align="right"><p style="word-wrap:break-word;"><? 
					if($tot_sewing_prod_p_loss) echo number_format($tot_sewing_prod_p_loss/$tot_sew_in*100,2);else echo " ";?></p></td>	

					<td width="80" align="right"> <p style="word-wrap:break-word;"><? echo omitZero(number_format($tot_wash_send,0));?></p></td>	
					<td width="80" align="right" id="sew_in_qty_th"><p><? echo omitZero(number_format($tot_wash_recv,0));?></p></td>				
					<td width="80" align="right" class=""   id="sew_ploss_qty_th"><p class="td_color"><? echo omitZero(number_format($tot_wash_process_pcs,0));?> </p></td>
					<td width="80" align="right">
					<p style="word-wrap:break-word;"><?	if($tot_wash_process_pcs) echo number_format($tot_wash_process_pcs/$tot_wash_send*100,2)."%";else echo " ";?></p>
					</td>	

					<td width="80" align="right" id="td_tot_fin_qty"><p  style="word-wrap:break-word;"><? echo number_format($tot_fin_qty,0);?></p></td>	
					<td width="80" align="right" id="td_fin_loss"><p  style="word-wrap:break-word;" class="td_color"><? echo number_format($tot_fin_process_loss,0);?></p></td>	
					<td width="80" align="right"><p  style="word-wrap:break-word;">
					<? if($tot_fin_process_loss) echo number_format(($tot_fin_process_loss/$tot_fin_qty)*100,2);else echo " ";?></p></td>	
					<td width="80" id=""><p  style="word-wrap:break-word;"><? echo number_format($tot_ex_factQty,0);?></p></td>
					<td width="80" align="right" id="td_prod_qnty"><p  class="td_color" style="word-wrap:break-word;"><? echo number_format($tot_prod_qty,0);?></p></td>	
					<td width="" align="right" ><p class="td_color"><? if($tot_prod_qty) echo number_format((($tot_prod_qty/$tot_cut_qnty)*100),2).'% ';else echo " ";?></p></td>	  
				
				</tr>
			</table>  
			<style>
			.td_color{ background: #FF3; width:80px;
				}
			</style>
		</fieldset>
	 
		<?
	}
	else if($report_type == 2) // Style 2 Button
	{
		if($cbo_company_name==0 || $cbo_company_name=="")
		{ 
			$company_name="";$company_name2="";
		}
		else 
		{ 
			$company_name = " and a.company_name=$cbo_company_name";$company_name2 = "and a.company_id=$cbo_company_name";
		}//fabric_source//item_category
		
		

		if(str_replace("'","",$cbo_buyer_name)==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_cond="";
			}
			else
			{
				$buyer_id_cond="";$buyer_id_cond2="";
			}
		}
		else
		{
			$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
			$buyer_id_cond2=" and a.buyer_id=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
		}
		$style_cond='';$style_cond2='';
		if(str_replace("'","",$txt_style_ref)!="" && $hide_job_id!='')
		{
			$style_cond = " and a.id in($hide_job_id)";
		}
		else
		{
			$style_cond = " and a.style_ref_no like '%".str_replace("'","",$txt_style_ref)."%'";
		}
	
		/*if(str_replace("'","",$cbo_year)!=0)
		{
			if($db_type==0){$search_text.=" and YEAR(a.insert_date)=$cbo_year";}
			else if($db_type==2){$search_text.=" and to_char(a.insert_date,'YYYY')=$cbo_year";}
		}*/
		
		$txt_date_from=str_replace("'","",trim($txt_date_from));
		$txt_date_to=str_replace("'","",trim($txt_date_to));
		if($txt_date_from!="" && $txt_date_to!=""){
			if($db_type==0){
				$start_date = date('Y-m-d H:i:s',strtotime($txt_date_from)); 
				$end_date = date("Y-m-d",strtotime($txt_date_to));
				
					$date_cond =" and c.ex_factory_date between '".$start_date."' and '".$end_date."'";
					//$search_text2 .=" and c.pub_shipment_date between '".$start_date."' and '".$end_date."'";
			}
			else
			{
				$start_date = change_date_format(date("Y-m-d H:i:s",strtotime($txt_date_from)),'','',1);
				$end_date = change_date_format(date("Y-m-d H:i:s",strtotime($txt_date_to)),'','',1);
				$date_cond=" and c.ex_factory_date between '".$start_date."' and '".$end_date."'";
			}
		}
		
		//function for omit zero value
		function omitZero($value){
			if($value==0){
				return "";
				}
			else {
				return $value;
			}
		}
		//echo omitZero(10);
		
			$items_library=return_library_array( "select id, item_name from lib_garment_item where  status_active=1 and is_deleted=0", "id", "item_name"  );
			$buyer_library=return_library_array( "select id, buyer_name from lib_buyer where  status_active=1 and is_deleted=0", "id", "buyer_name"  );
			$company_lib=return_library_array( "select id, company_name from lib_company where  status_active=1 and is_deleted=0", "id", "company_name"  );
		
			if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
			else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
		
			$sql="SELECT a.id as job_id, a.buyer_name,a.total_set_qnty,$year_field,a.job_no_prefix_num ,a.job_no,a.style_ref_no,b.po_number,a.set_smv,a.set_break_down,a.order_uom,a.gmts_item_id,b.id,a.buyer_name,(b.po_quantity*a.total_set_qnty) as po_quantity,b.po_quantity as  po_qty,b.plan_cut,b.unit_price,b.pub_shipment_date,b.po_received_date,b.insert_date,b.grouping, b.file_no,b.is_confirmed,b.inserted_by,b.po_total_price,b.details_remarks from wo_po_details_master a join wo_po_break_down b on a.job_no=b.job_no_mst left join pro_ex_factory_mst c on b.id=c.po_break_down_id and c.is_deleted=0 and c.status_active=1 where   a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  $company_name $date_cond $buyer_id_cond $style_cond  order by c.ex_factory_date desc"; 
		//echo $sql;		  		 
		$order_sql_result = sql_select($sql) ;
		foreach($order_sql_result as $row){
			//$order_data_arr[$rows[csf('buyer_name')]][]=$rows;
			//$order_data_arr[]=$rows;
			$order_id_arr[$row[csf('id')]]=$row[csf('id')];
			$po_buyer_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$po_buyer_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
			$job_id_arr[$row[csf("job_id")]]=$row[csf("job_id")];
			$poJob_wise_qty[$row[csf("id")]]=$row[csf("job_no")];
		}
		unset($order_sql_result);
		
		$job_cond_for_in=where_con_using_array($job_id_arr,0,"c.job_id");
		$job_cond_for_in2=where_con_using_array($job_id_arr,0,"a.id");
	
	
		$po_id_list_arr=array_chunk($order_id_arr,999);
	
		$sql_exf_last="SELECT c.job_id,max(b.ex_factory_date) as ex_factory_date,
		sum(CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END)-sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as qnty 
		from pro_ex_factory_mst b,wo_po_break_down c,wo_po_details_master a where c.id=b.po_break_down_id and a.id=c.job_id and  b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 $job_cond_for_in  group by c.job_id  ";
		$result_sql_exf_last=sql_select($sql_exf_last); 
		foreach($result_sql_exf_last as $row)
		{
			$last_ex_factory_date_arr[$row[csf("job_id")]]=$row[csf("ex_factory_date")];
	
		}
		unset($result_sql_exf_last);
		//print_r($last_ex_factory_date_arr);
		$sql_exf="SELECT b.po_break_down_id,b.ex_factory_date,c.job_no_mst as job_no,
		(CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END)-(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as qnty 
		from pro_ex_factory_mst b,wo_po_break_down c where b.po_break_down_id=c.id and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 $job_cond_for_in  order by b.po_break_down_id ";
		$result_sql_exf=sql_select($sql_exf); 
		foreach($result_sql_exf as $row)
		{
			$ex_factory_date=$row[csf("ex_factory_date")];
			//$poJob=$row[csf("job_no")];
			$poJob=$poJob_wise_qty[$row[csf("po_break_down_id")]];
			//$newdate =change_date_format($ex_factory_date,'','',1);
			$ex_factory_arr[$row[csf("po_break_down_id")]]+=$row[csf("qnty")];
			$exfact_conf_arr[$poJob][$row[csf("po_break_down_id")]]=$row[csf("ex_factory_date")];
		}
		$sql_main="SELECT a.id as job_id, a.buyer_name,a.total_set_qnty,$year_field,a.job_no_prefix_num ,a.job_no,a.style_ref_no,b.po_number,a.set_smv,a.set_break_down,a.order_uom,a.gmts_item_id,b.id,a.buyer_name,(b.po_quantity*a.total_set_qnty) as po_quantity,b.po_quantity as  po_qty,b.plan_cut,b.unit_price,b.pub_shipment_date,b.po_received_date,b.insert_date,b.shiping_status,b.grouping, b.file_no,b.is_confirmed,b.inserted_by,b.po_total_price,b.details_remarks from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst   $company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  $job_cond_for_in2 order by b.id,b.shiping_status desc"; 
		//echo $sql_main;		  		 
		$sql_main_result = sql_select($sql_main);
		foreach($sql_main_result as $row)
		{
			$po_id_arr[$row[csf('id')]]=$row[csf('id')];
		}
		//	unset($sql_main_result);
		$po_cond_for_in=where_con_using_array($po_id_arr,0,"b.order_id");
		$po_cond_for_in2=where_con_using_array($po_id_arr,0,"b.po_breakdown_id");
		$po_cond_for_in3=where_con_using_array($po_id_arr,0,"a.po_breakdown_id");
		$po_cond_for_in4=where_con_using_array($po_id_arr,0,"b.po_id");
		$po_cond_for_in5=where_con_using_array($po_id_arr,0,"a.po_break_down_id");
		$po_cond_for_in6=where_con_using_array($po_id_arr,0,"b.po_break_down_id");


		
		$sql="SELECT   a.po_break_down_id, 
		SUM (CASE WHEN  a.production_type = 1 AND d.production_type = 1 THEN d.production_qnty ELSE 0 END) AS totalcut,
		SUM (CASE WHEN  a.production_type = 4 AND d.production_type = 4 THEN d.production_qnty ELSE 0 END) AS totalinput,
		SUM (CASE WHEN  a.production_type = 5 AND d.production_type = 5 THEN d.production_qnty ELSE  0  END)  AS totalsewing,
		SUM (CASE WHEN  a.production_type = 2 AND d.production_type = 2 AND a.embel_name = 1 THEN d.production_qnty ELSE 0 END) AS totalprsend,	
		SUM (CASE WHEN  a.production_type = 3 AND d.production_type = 3 AND a.embel_name = 1 THEN d.production_qnty ELSE 0 END) AS totalprrec,
		SUM (CASE WHEN  a.production_type = 3 AND d.production_type = 3 AND a.embel_name = 2	THEN d.production_qnty ELSE 0 END) AS today_emb_receive,
		SUM (CASE WHEN  a.production_type = 63 AND d.production_type = 63 	THEN d.production_qnty ELSE 0 END) AS totalembsend,
		SUM (CASE WHEN  a.production_type = 64 AND d.production_type = 64 	THEN d.production_qnty ELSE 0 END) AS totalembrec,
		SUM (CASE WHEN  a.production_type = 8 AND d.production_type = 8 THEN d.production_qnty ELSE 0 END) AS totalfinish,	
		SUM (CASE WHEN  a.production_type = 2 AND d.production_type = 2 AND a.embel_name = 2 THEN d.production_qnty  ELSE  0 END) AS total_emb_issue

		FROM wo_po_details_master        b,
			wo_po_break_down            c,
			pro_garments_production_mst a,
			pro_garments_production_dtls d,
			wo_po_color_size_breakdown  e
		WHERE     b.job_no = c.job_no_mst
			AND c.id = a.po_break_down_id
			AND a.id = d.mst_id
			AND d.color_size_break_down_id = e.id
			AND a.po_break_down_id = e.po_break_down_id
			AND a.status_active = 1
			AND a.is_deleted = 0
			AND d.status_active = 1
			AND d.is_deleted = 0
			AND b.status_active = 1
			AND b.is_deleted = 0
			AND c.status_active = 1
			AND c.is_deleted = 0
			AND e.status_active = 1
			AND e.is_deleted = 0
			AND b.company_name = $cbo_company_name
			$po_cond_for_in5 
		GROUP BY a.po_break_down_id";

		$dataArray=sql_select($sql);
		foreach($dataArray as $row)
		{  
			$cut_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totalcut')];
			$sewing_in_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totalinput')];
			$sewing_out_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totalsewing')];
			$sewing_finish_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totalfinish')];
			$emb_issue_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totalembsend')];
			$emb_rec_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totalembrec')];
			$print_issue_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totalprsend')];
			$print_rec_qnty_array[$row[csf('po_break_down_id')]]=$row[csf('totalprrec')];
		
		}
		unset($dataArray);
					
		//Job Wise Data create......................................start;
		$start_date=strtotime($start_date);
		$end_date=strtotime($end_date);
		$partialship_job_count = array();
		foreach($sql_main_result as $row)
		{
			
			//echo $row[csf('po_quantity')].'D';
			//echo $ex_factory_arr[$row[csf('id')]].'DX';
			$job_wise_DataArr[$row[csf('job_no')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
			$job_wise_DataArr[$row[csf('job_no')]]['job_id']=$row[csf('job_id')];
			$job_wise_DataArr[$row[csf('job_no')]]['po_id'].=$row[csf('id')].',';
			$job_wise_DataArr[$row[csf('job_no')]]['buyer_name']=$row[csf('buyer_name')];
			$job_wise_DataArr[$row[csf('job_no')]]['style_ref_no']=$row[csf('style_ref_no')];
			$job_wise_DataArr[$row[csf('job_no')]]['year']=$row[csf('year')];
			$job_wise_DataArr[$row[csf('job_no')]]['po_quantity']+=$row[csf('po_quantity')];
			$job_wise_DataArr[$row[csf('job_no')]]['year']=$row[csf('year')];
			$job_wise_DataArr[$row[csf('job_no')]]['pub_shipment_date'].=$row[csf('pub_shipment_date')].',';
			$job_wise_DataArr[$row[csf('job_no')]]['sew_in']+=$sewing_in_qnty_array[$row[csf('id')]];
			$job_wise_DataArr[$row[csf('job_no')]]['sew_out']+=$sewing_out_qnty_array[$row[csf('id')]];
			$job_wise_DataArr[$row[csf('job_no')]]['ex_factQty']+=$ex_factory_arr[$row[csf('id')]];
			$job_wise_DataArr[$row[csf('job_no')]]['cut_qty']+=$cut_qnty_array[$row[csf('id')]];
			$job_wise_DataArr[$row[csf('job_no')]]['fin_qty']+=$sewing_finish_qnty_array[$row[csf('id')]];
			$job_wise_DataArr[$row[csf('job_no')]]['prod_qty']+=$knit_prod_qty_array[$row[csf('id')]];

			$is_confirmed=$row[csf("is_confirmed")];
			
			if($is_confirmed==1)
			{
				$shipId=$row[csf("shiping_status")];
				
				$exfact_date=strtotime($exfact_conf_arr[$row[csf("job_no")]][$row[csf("id")]]);
				//echo $row[csf("id")] . "**".$exfact_conf_arr[$row[csf("job_no")]][$row[csf("id")]]."=".$last_ex_factory_date_arr[$row[csf("job_id")]]."==<br />";
				$last_ex_factory_date=strtotime($last_ex_factory_date_arr[$row[csf("job_id")]]);
				//echo $shipId.'='.$exfact_date.',';
				//echo $exfact_date.'='.$shipId.'='.$row[csf("id")].'<br>';
				if($exfact_date=='' && $shipId!=3)
				{
					unset($fullship_job_po_chk_arr[$row[csf("job_no")]]);
					$is_partialship_job_count[$row[csf("job_no")]]=1;
					//echo $exfact_date.'X'.$row[csf("id")].'<br>'; 
				}
				else
				{
					/*
					po1 - ex full
					po2 - ex partial
					po3 - ex full 
					*/
					
					if($start_date!="" && $end_date !="") 
					{
						//echo $last_ex_factory_date_arr[$row[csf("job_id")]].'='.$row[csf("id")].'='.$is_partialship_job_count[$row[csf("job_no")]].'<br>';
						if($shipId==3 && ($last_ex_factory_date>=$start_date && $last_ex_factory_date<=$end_date) && $is_partialship_job_count[$row[csf("job_no")]]!=1)
						{
							if($partialship_job_count[$row[csf("job_no")]]!=1)
							{	
								$fullship_job_po_chk_arr[$row[csf("job_no")]]=$last_ex_factory_date;
								$partialship_job_count[$row[csf("job_no")]]=0;
								//echo $shipId.'A'.$row[csf("id")].'<br>';
							}
						}
						else
						{
							unset($fullship_job_po_chk_arr[$row[csf("job_no")]]);
							$partialship_job_count[$row[csf("job_no")]]++;
							//echo $shipId.'B'.$row[csf("id")].'<br>';
						}
					}
					else
					{
						
						if($is_partialship_job_count[$row[csf("job_no")]]==1)
						{
							unset($fullship_job_po_chk_arr[$row[csf("job_no")]]);
							//$partialship_job_count2[$row[csf("job_no")]]=0;
						}
						else if($shipId!=3)
						{
							unset($fullship_job_po_chk_arr[$row[csf("job_no")]]);
							$is_partialship_job_count[$row[csf("job_no")]]=1;
							//echo $shipId.'D';;
							//$partialship_job_count2[$row[csf("job_no")]]=0;
						}
						else
						{
							if($is_partialship_job_count[$row[csf("job_no")]]==1)
							{
								unset($fullship_job_po_chk_arr[$row[csf("job_no")]]);
								//$partialship_job_count2[$row[csf("job_no")]]=0;
							}
							else
							{
								$fullship_job_po_chk_arr[$row[csf("job_no")]]=$last_ex_factory_date;
							}
							
						}
					}
				}
			}
		}
		//$po_id_arr = array();
		//print_r($is_partialship_job_count);
		//==================Main query=====
		$poidInJob = array();
		foreach($sql_main_result as $row)
		{
			$ex_factoryQty=$ex_factory_arr[$row[csf("id")]];
			$ship_id=$row[csf("shiping_status")];
			
			// if($fullship_job_po_chk_arr[$row[csf("job_no")]] && $ship_id==3 && $ex_factoryQty>0) //Fullship Check
			// {
				$job_wise_arr[$row[csf('job_no')]]['job_no_prefix_num']=$job_wise_DataArr[$row[csf('job_no')]]['job_no_prefix_num'];
				$job_wise_arr[$row[csf('job_no')]]['job_id']=$job_wise_DataArr[$row[csf('job_no')]]['job_id'];
				$job_wise_arr[$row[csf('job_no')]]['buyer_name']=$job_wise_DataArr[$row[csf('job_no')]]['buyer_name'];
				$job_wise_arr[$row[csf('job_no')]]['style_ref_no']=$job_wise_DataArr[$row[csf('job_no')]]['style_ref_no'];
				$job_wise_arr[$row[csf('job_no')]]['year']=$row[csf('year')];
				$job_wise_arr[$row[csf('job_no')]]['po_quantity']=$job_wise_DataArr[$row[csf('job_no')]]['po_quantity'];
				$job_wise_arr[$row[csf('job_no')]]['year']=$row[csf('year')];
				$job_wise_arr[$row[csf('job_no')]]['po_id'].=$row[csf('id')].',';
				$job_wise_arr[$row[csf('job_no')]]['pub_shipment_date'].=$row[csf('pub_shipment_date')].',';
				$job_wise_arr[$row[csf('job_no')]]['sew_in']=$job_wise_DataArr[$row[csf('job_no')]]['sew_in'];
				$job_wise_arr[$row[csf('job_no')]]['sew_out']=$job_wise_DataArr[$row[csf('job_no')]]['sew_out'];
				$job_wise_arr[$row[csf('job_no')]]['ex_factQty']=$job_wise_DataArr[$row[csf('job_no')]]['ex_factQty'];
				$job_wise_arr[$row[csf('job_no')]]['cut_qty']=$job_wise_DataArr[$row[csf('job_no')]]['cut_qty'];
				$job_wise_arr[$row[csf('job_no')]]['fin_qty']=$job_wise_DataArr[$row[csf('job_no')]]['fin_qty'];
				$job_arr[$row[csf('job_no')]]=$row[csf('job_no')];
				$po_id_arr[$row[csf('id')]] = $row[csf('id')];
				$poidInJob[$row[csf('job_no')]][$row[csf('id')]] = $row[csf('id')];
			//}
			
		}
	$job_cond = where_con_using_array($job_arr,1,"d.job_no");
	$sql_fab="SELECT d.id as fab_dtls_id,d.job_id,d.job_no,d.body_part_id as bpart_id,d.color_size_sensitive,d.lib_yarn_count_deter_id as deter_id,d.color_type_id,d.construction,d.composition,e.gmts_color_id,e.contrast_color_id 
	from wo_pre_cost_fabric_cost_dtls d, wo_pre_cos_fab_co_color_dtls e 
	where d.id=e.pre_cost_fabric_cost_dtls_id and d.status_active=1 and e.status_active=1 $job_cond";
	$sql_fab_result=sql_select($sql_fab);
	$contrast_fabric_wise_arr = array();
	foreach($sql_fab_result as $row)
	{
		$bpart_id=$row[csf("bpart_id")];
		$deter_id=$row[csf("deter_id")];
		$color_id=$row[csf("gmts_color_id")];
		$contrast_fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$color_id]["contrast_color_id"]=$row[csf("contrast_color_id")];
	}
	unset($sql_fab_result);
	$sql_wo="SELECT a.id, a.booking_no,a.booking_date,a.pay_mode,a.supplier_id,a.currency_id,a.exchange_rate,b.gmts_color_id,a.booking_type,b.po_break_down_id as po_id,
	(b.grey_fab_qnty) as grey_fab_qnty, b.fin_fab_qnty,b.job_no,b.amount,c.body_part_id as bpart_id,c.color_size_sensitive,c.lib_yarn_count_deter_id as deter_id,c.fabric_description,c.avg_cons
	from wo_booking_mst a, wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c  where a.booking_no=b.booking_no and c.id=b.pre_cost_fabric_cost_dtls_id and a.booking_type=1 and b.fin_fab_qnty>0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.is_deleted=0 and c.status_active=1  $po_cond_for_in6";
	//echo $sql_wo;

	$res_wo = sql_select($sql_wo);
	$booking_data = array();
	$booking_color_data = array();
	foreach($res_wo as $row)
	{
		$color_id = $row[csf("gmts_color_id")];
		if ($row[csf("color_size_sensitive")]==3) 
        {
        	$fabric_color=$contrast_fabric_wise_arr[$row[csf("job_no")]][$bpart_id][$deter_id][$color_id]["contrast_color_id"];
        }
        else
        {
        	$fabric_color=$color_id;
        }

		$booking_data[$row[csf('job_no')]][$row[csf('bpart_id')]][$row[csf('deter_id')]]['grey']   +=$row[csf("grey_fab_qnty")];
		$booking_data[$row[csf('job_no')]][$row[csf('bpart_id')]][$row[csf('deter_id')]]['fin']    +=$row[csf("fin_fab_qnty")];
		$booking_data[$row[csf('job_no')]][$row[csf('bpart_id')]][$row[csf('deter_id')]]['amount'] +=$row[csf("grey_fab_qnty")];
		$booking_data[$row[csf('job_no')]][$row[csf('bpart_id')]][$row[csf('deter_id')]]['desc'] =$row[csf("fabric_description")];
		$booking_data[$row[csf('job_no')]][$row[csf('bpart_id')]][$row[csf('deter_id')]]['avg_cons'] =$row[csf("avg_cons")];
		$booking_color_data[$row[csf('job_no')]][$row[csf('bpart_id')]][$row[csf('deter_id')]][$fabric_color] = $fabric_color;
	}



	// echo "<pre>";
	// print_r($booking_data);
	// echo "</pre>";
	// die;


	$prodKnitDataArr=sql_select("SELECT a.po_breakdown_id as po_id,b.fabric_description_id as deter_id,b.rate,b.body_part_id,a.color_id,
	(CASE WHEN a.entry_form = 17 or a.entry_form  =37 or a.entry_form =225 THEN a.quantity ELSE 0 END) AS knit_qnty_rec
	from order_wise_pro_details a, pro_finish_fabric_rcv_dtls b, inv_receive_master c where a.dtls_id=b.id and b.mst_id=c.id  and c.item_category in (2,3) and (a.entry_form = 17 or a.entry_form = 37 or a.entry_form = 225) and (c.entry_form = 17 or c.entry_form = 37 or c.entry_form = 225) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   $po_cond_for_in3 ");// and c.receive_basis<>9
	$kniting_prod_arr=array();
	foreach($prodKnitDataArr as $row)
	{
		$kniting_prod_arr[$row[csf("po_id")]][$row[csf("body_part_id")]][$row[csf("deter_id")]]["knit_qnty_rec"]+=$row[csf("knit_qnty_rec")];
		$kniting_prod_arr[$row[csf("po_id")]][$row[csf("body_part_id")]][$row[csf("deter_id")]]["knit_qnty_rec_amt"]+=$row[csf("knit_qnty_rec")]*$row[csf("rate")];
	}
	unset($prodKnitDataArr);
	$issueprodKnitDataArr=sql_select("SELECT a.po_breakdown_id as po_id,d.detarmination_id as deter_id,b.cons_rate,b.body_part_id,a.color_id,
	(CASE WHEN a.entry_form =18 or a.entry_form =19 THEN a.quantity ELSE 0 END) AS knit_qnty_issue
	from order_wise_pro_details a, inv_transaction b, inv_issue_master c,product_details_master d where a.trans_id=b.id and b.mst_id=c.id and d.id=b.prod_id and a.prod_id=d.id and c.item_category in (2,3) and (a.entry_form =18 or a.entry_form =19) and (c.entry_form =18 or c.entry_form =19) and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1  and c.is_deleted=0 and c.status_active=1 $po_cond_for_in3 ");// and c.receive_basis<>9
	$issue_kniting_prod_arr=array();
	foreach($issueprodKnitDataArr as $row)
	{
		$issue_kniting_prod_arr[$row[csf("po_id")]][$row[csf("body_part_id")]][$row[csf("deter_id")]]["knit_qnty_issue"]+=$row[csf("knit_qnty_issue")];
		$issue_kniting_prod_arr[$row[csf("po_id")]][$row[csf("body_part_id")]][$row[csf("deter_id")]]["knit_qnty_issue_amt"]+=$row[csf("knit_qnty_issue")]*$row[csf("cons_rate")];
	}
	unset($issueprodKnitDataArr);
	// echo "<pre>";
	// print_r($kniting_prod_arr);
	// echo "</pre>";
		//print_r($job_wise_arr);
		//Summary Data create......................................end;	


		//=============================================Wash===================================================

		$wash_sql="SELECT a.id,a.garments_nature,a.company_id,a.po_break_down_id,b.job_no_mst,b.job_id,a.production_source,a.embel_name,a.embel_type,a.production_quantity,a.production_source,a.production_type,a.carton_qty,a.alter_qnty,a.reject_qnty from pro_garments_production_mst a,wo_po_break_down b where  b.id=a.po_break_down_id and a.production_type in (2,3) and a.embel_name=3 and a.status_active=1 and a.is_deleted=0 ".where_con_using_array($job_arr,1,'b.job_no_mst')." order by a.id";

		$wash_data=sql_select($wash_sql);

		foreach($wash_data as $val){
			if($val[csf('production_type')]==2){
				$job_wise_wash_qty[$val[csf('job_id')]]['send_qty']+=$val[csf('production_quantity')];
			}else{
				$job_wise_wash_qty[$val[csf('job_id')]]['recv_qty']+=$val[csf('production_quantity')];
			}
		}

		//=============================================printing===================================================

		$printing_sql="SELECT a.id,a.garments_nature,a.company_id,a.po_break_down_id,b.job_no_mst,b.job_id,a.production_source,a.embel_name,a.embel_type,a.production_quantity,a.production_source,a.production_type,a.carton_qty,a.alter_qnty,a.reject_qnty from pro_garments_production_mst a,wo_po_break_down b where  b.id=a.po_break_down_id and a.production_type in (2,3) and a.embel_name=1 and a.status_active=1 and a.is_deleted=0 ".where_con_using_array($job_arr,1,'b.job_no_mst')." order by a.id";

		$printing_data=sql_select($printing_sql);

		foreach($printing_data as $val){
			if($val[csf('production_type')]==2){
				$job_wise_printing_qty[$val[csf('job_id')]]['send_qty']+=$val[csf('production_quantity')];
			}else{
				$job_wise_printing_qty[$val[csf('job_id')]]['recv_qty']+=$val[csf('production_quantity')];
			}
		}

		//=============================================embroidery===================================================

		$embroidery_sql="SELECT a.id,a.garments_nature,a.company_id,a.po_break_down_id,b.job_no_mst,b.job_id,a.production_source,a.embel_name,a.embel_type,a.production_quantity,a.production_source,a.production_type,a.carton_qty,a.alter_qnty,a.reject_qnty from pro_garments_production_mst a,wo_po_break_down b where  b.id=a.po_break_down_id and a.production_type in (2,3) and a.embel_name=2 and a.status_active=1 and a.is_deleted=0 ".where_con_using_array($job_arr,1,'b.job_no_mst')." order by a.id";

		$embroidery_data=sql_select($embroidery_sql);

		foreach($embroidery_data as $val){
			if($val[csf('production_type')]==2){
				$job_wise_embroidery_qty[$val[csf('job_id')]]['send_qty']+=$val[csf('production_quantity')];
			}else{
				$job_wise_embroidery_qty[$val[csf('job_id')]]['recv_qty']+=$val[csf('production_quantity')];
			}
		}

	$condition1= new condition();
	$condition1->company_name("=$cbo_company_name");
	if(count($po_id_arr)>0){
		$condition1->po_id_in("".implode(",",$po_id_arr)."");
	}
					 
	$condition1->init();
	$fabric= new fabric($condition1);
	//echo $fabric->getQuery(); die;
	$fabric_req_arr=$fabric->getQtyArray_by_OrderBodypartDeterminIdAndGmtscolor_knitAndwoven_greyAndfinish();
	$fabric_req_cost_arr=$fabric->getAmountArray_by_OrderBodypartDeterminIdAndGmtscolor_knitAndwoven_greyAndfinish();
	//print_r($fabric_req_arr);

		ob_start();	
		$width=2640 + 920;
		?>
		
		<fieldset>
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr><td colspan="20" align="center" style="font-size:22px;"><? echo str_replace("'","",$report_title);;?></td></tr>
				<tr>
					
					<td colspan="20" align="center" style="font-size:16px; font-weight:bold;">
						<? echo $company_lib[$cbo_company_name];?>
					</td>
				</tr>
				<? if($txt_date_from!='' && $txt_date_to!=''){?>
				<tr>
					<td colspan="20" align="center" style="font-size:14px;">
						From: <? echo change_date_format($txt_date_from);?> 
						To: <? echo change_date_format($txt_date_to);?>
					</td>
				</tr>
				<? }?>
				<tr>
					<td colspan="20" align="left" style="font-size:14px; color: red; font-weight:bold;">
						<? //echo "This Report is Generated based on Last Ex-Factory date Against Style All PO Close";?>
					</td>
				</tr>
			</table>
			
			<table id="table_header_1" class="rpt_table" width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<tr>
						<th colspan="8" width="650">&nbsp;  </th>
						<th colspan="10" width="1070">FABRIC</th>
						<th colspan="2"  width="160">Cutting </th>
						<th colspan="4">Print </th>
						<th colspan="4">Embroidery </th>
						<th colspan="4">Sewing </th>
						<th colspan="4">Wash </th>
						<th colspan="2">Packing & Finishing	 </th>
						
						<th colspan="2">Production </th>
					</tr>
					<tr style="font-size:12px"> 
						<th width="30">Sl</th>	
						<th width="100">Buyer</th> 
					
						<th width="100">Job No</th>
						<th width="60">Job Year</th>
						<th width="120">Style No</th>
						<th width="80">Style Qty</th>
						<th width="80">Last Ship<br> Date</th>
						<th width="80">Last Ex-Factory<br> Date</th>

						<th width="400">Fabric Description</th>
						<th width="80">Body Part</th>
						<th width="80">Pre-Cost Cons</th>
						<th width="80">Required Qty</th>
						<th width="80">WO QTY</th>
						<th width="80" title="WO QTY / Style Qty">PO Cons</th>
						<th width="80">Recv Qty</th>
						<th width="80" title="WO QTY - Recv Qty">Recv Balance</th>
						<th width="80">Issued Qty</th>
						<th width="80">Actual Cons</th>
						
						<th width="80">TTL Cut Qty</th>	
						<th width="80" title="Cut Qty/PO Qty*100">Cutting %</th>

						<th width="80">Print Send</th>	
						<th width="80" title="Cutting-Print Send">Print Send Balance</th>	
						<th width="80">Print Received</th>
						<th width="80" title="Print Send-Print Received">Print Rcv Balance</th>

						<th width="80">Embroidery Send</th>	
						<th width="80" title="Cutting - Embroidery Sent">Embroidery Send Balance</th>	
						<th width="80">Embroidery Received</th>
						<th width="80" title="Embroidery Sent - Embroidery Received">Embroidery Rcv Balance</th>

						<th width="80">Sewing Input</th>	
						<th width="80" title="Cutting - Sewing Input">Sewing Input Balance</th>	
						<th width="80">Sewing Output</th>
						<th width="80" title="Sewing Input - Sewing Output">Sewing Output Balance</th>
						
						<th width="80">Wash Sent</th>	
						<th width="80" title="Sewing Output - Wash Sent">Wash Send Balance</th>	
						<th width="80">Wash Received</th>
						<th width="80" title="Wash Sent - Wash Received">Wash Rcv Balance</th>

				
						
						<th width="80" title="">Packing & Finish Qty</th>	
						<th width="80" title="Style Qty-Ex Fact Qty">Packing & Finish Balance</th>	
							
					
						<th width="80" >Ex-Factory</th>
						<th style="" title="Style Qty - Ex-Fact Qty">Short/Excess</th>	
						
					</tr>                            	
				</thead>
			</table>
			<div style="width:<? echo $width+20;?>px; max-height:400px; overflow-y:scroll" id="scroll_body">
					<table class="rpt_table" width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
					
			<?php
				$i=1;$tot_fin_qty=$tot_fin_process_loss=$tot_ex_factQty=$tot_prod_qty=$tot_sewing_prod_p_loss=0;
				$jobspan_arr = array();
				$deterspan_arr = array();
				foreach($booking_data as $job_no=> $job_row)
				{
					$job_span = 0;
					foreach($job_row as $body_part_id => $bodypart)
					{
						$body_span = 0;
						foreach($bodypart as $deter_id => $deter_data)
						{
							
							$body_span++;
							$job_span++;
						}
						$bodyspan_arr[$job_no][$body_part_id]=$body_span;
					}
					$jobspan_arr[$job_no]=$job_span;
				}
				// echo "<pre>";
				// print_r($body_part);
				// echo "</pre>";
				foreach($booking_data as $job_no=> $job_row)
				{
					$row = $job_wise_arr[$job_no];
					$job_span = 0;
					$po_id=rtrim($row[('po_id')],',');
					$po_ids=array_unique(explode(",", $po_id));
					//echo "<pre>$po_id</pre>";
					
					$pub_shipment_dateAll=rtrim($row[('pub_shipment_date')],',');
					$pub_shipment_dateArr=array_unique(explode(",", $pub_shipment_dateAll));
					$last_shipDate=max($pub_shipment_dateArr);
					$wash_process_loss=$job_wise_wash_qty[$row[("job_id")]]['send_qty']-$job_wise_wash_qty[$row[("job_id")]]['recv_qty'];
					$printing_process_loss=$row[('cut_qty')]-$job_wise_printing_qty[$row[("job_id")]]['recv_qty'];
					$embroidery_process_loss=$row[('cut_qty')]-$job_wise_embroidery_qty[$row[("job_id")]]['recv_qty'];
					foreach($job_row as $bprat_id => $partdata)
					{
						foreach($partdata as $deter_id => $deter_data)
						{
							$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
							
							$jobspan = $jobspan_arr[$job_no];
							//$poids = $poidInJob[$job];
							$tot_fabric_req_wov_cost=$tot_fabric_req_knit=$booking_req=$booking_amount=$kniting_prod_recv=$kniting_prod_recv_amt=$kniting_prod_issue=$kniting_prod_issue_amt=0;
							foreach($po_ids as $pId )
							{
								$colorsIds = $booking_color_data[$job_no][$bprat_id][$deter_id];
								foreach($colorsIds as $fabcolor)
								{
									$tot_fabric_req_knit+=array_sum($fabric_req_arr['woven']['grey'][$pId][$bprat_id][$deter_id][$fabcolor])+array_sum($fabric_req_arr['knit']['grey'][$pId][$bprat_id][$deter_id][$fabcolor]);
									$tot_fabric_req_wov_cost+=array_sum($fabric_req_cost_arr['woven']['grey'][$pId][$bprat_id][$deter_id][$fabcolor])+array_sum($fabric_req_cost_arr['knit']['grey'][$pId][$bprat_id][$deter_id][$fabcolor]);
								}
								

								$kniting_prod_recv+=$kniting_prod_arr[$pId][$bprat_id][$deter_id]["knit_qnty_rec"];
								$kniting_prod_recv_amt+=$kniting_prod_arr[$pId][$bprat_id][$deter_id]["knit_qnty_rec_amt"];
								$kniting_prod_issue+=$issue_kniting_prod_arr[$pId][$bprat_id][$deter_id]["knit_qnty_issue"];
								$kniting_prod_issue_amt+=$issue_kniting_prod_arr[$pId][$bprat_id][$deter_id]["knit_qnty_issue_amt"];
								//echo "<pre>helal</pre>";
							}

							
						
						
							?>	
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="font-size:12px; cursor:pointer;" valign="middle">
								<?php if ($job_span == 0): ?>
									
									<td rowspan="<?=$jobspan;?>" width="30"><? echo $i;?></td>	
									<td rowspan="<?=$jobspan;?>" width="100"><p style=" word-wrap:break-word;"><? echo $buyer_library[$row[('buyer_name')]];?></p></td>
									<td rowspan="<?=$jobspan;?>" width="100"><p><? echo $row[('job_no_prefix_num')];?></p></td>
									<td rowspan="<?=$jobspan;?>" width="60"><p><? echo $row[('year')];?></p></td> 
									<td rowspan="<?=$jobspan;?>" width="120"><p style=" word-wrap:break-word;"><? echo $row[('style_ref_no')];?></p></td>	
									<td rowspan="<?=$jobspan;?>" width="80" align="right" style="word-wrap:break-word;"><p><? echo number_format($row[('po_quantity')],0);?></p></td>	
									<td rowspan="<?=$jobspan;?>" width="80"><p>
									<?
										
										echo date("d-M-Y", strtotime($last_shipDate));
									?>
									</p></td>	
									<td rowspan="<?=$jobspan;?>" width="80" style="word-wrap:break-word;"><p><? echo $last_ex_factory_date_arr[$row[("job_id")]];?></p></td>

								<?php endif ?>
								

								<td width="400" align="right" style="word-wrap:break-word;"><p><? echo $deter_data['desc'];?></p></td>
								<td width="80" align="right" style="word-wrap:break-word;"><p><? echo $body_part[$bprat_id];?></p></td>
								<td width="80" align="right" style="word-wrap:break-word;"><p><? echo fn_number_format($deter_data['avg_cons'],2,".",",");?></p></td>
								<td width="80" align="right" style="word-wrap:break-word;"><p><? echo fn_number_format($tot_fabric_req_knit,2,".",",");?></p></td>
								<td width="80" align="right" style="word-wrap:break-word;"><p><? echo fn_number_format($deter_data['fin'],2,".",",");?></p></td>
								<td width="80" align="right" style="word-wrap:break-word;" title="<?=$deter_data['fin']?> / <?=$row[('po_quantity')]?> "><p><? echo fn_number_format($deter_data['fin']/$row[('po_quantity')],2,".",",") ;?></p></td>
								<td width="80" align="right" style="word-wrap:break-word;"><p><? echo fn_number_format($kniting_prod_recv,2,".",",");?></p></td>
								<td width="80" align="right" style="word-wrap:break-word;" title="<?=$deter_data['fin']?> - <?=$kniting_prod_recv?> "><p><? echo fn_number_format($deter_data['fin']-$kniting_prod_recv,2,".",",");?></p></td>
								<td width="80" align="right" style="word-wrap:break-word;"><p><? echo fn_number_format($kniting_prod_issue,2,".",",");?></p></td>
								<td width="80" align="right" style="word-wrap:break-word;"><p><? echo fn_number_format($kniting_prod_issue/$row[('cut_qty')],2,".",",");?></p></td>

								<?php if ($job_span == 0): ?>

									<td rowspan="<?=$jobspan;?>" width="80" align="right" style="word-wrap:break-word;"><p><? echo number_format($row[('cut_qty')],0);?></p></td>
									<td rowspan="<?=$jobspan;?>" width="80" align="right"  style="word-wrap:break-word;"><p><? if($row[('cut_qty')])  echo number_format((($row[('cut_qty')]/$row[('po_quantity')])*100),2).'% ';else echo " ";?></p></td>	

									<td rowspan="<?=$jobspan;?>" width="80" align="right"><? echo fn_number_format($job_wise_printing_qty[$row[("job_id")]]['send_qty'],0);?></td>
									<td rowspan="<?=$jobspan;?>" width="80" align="right"><? echo  fn_number_format($row[('cut_qty')]-$job_wise_printing_qty[$row[("job_id")]]['send_qty'],0);;?></td>	                    
									<td rowspan="<?=$jobspan;?>" width="80" align="right"><? echo  fn_number_format($job_wise_printing_qty[$row[("job_id")]]['recv_qty']);;?></td>
									<td rowspan="<?=$jobspan;?>" width="80" align="right" ><? echo  fn_number_format($job_wise_printing_qty[$row[("job_id")]]['send_qty']-$job_wise_printing_qty[$row[("job_id")]]['recv_qty'],2);?></td>	

									<td rowspan="<?=$jobspan;?>" width="80" align="right"><? echo fn_number_format($job_wise_embroidery_qty[$row[("job_id")]]['send_qty'],0);?></td>
									<td rowspan="<?=$jobspan;?>" width="80" align="right"><? echo fn_number_format($row[('cut_qty')]-$job_wise_embroidery_qty[$row[("job_id")]]['send_qty'],0);;?></td>	                    
									<td rowspan="<?=$jobspan;?>" width="80" align="right"><? echo fn_number_format($job_wise_embroidery_qty[$row[("job_id")]]['recv_qty']);;?></td>
									<td rowspan="<?=$jobspan;?>" width="80" align="right" ><? echo fn_number_format($job_wise_embroidery_qty[$row[("job_id")]]['send_qty']-$job_wise_embroidery_qty[$row[("job_id")]]['recv_qty'],2);?></td>	
								
									<td rowspan="<?=$jobspan;?>" width="80" align="right"><? echo fn_number_format($row[('sew_in')],0);?></td>
									<td rowspan="<?=$jobspan;?>" width="80" align="right"><? echo fn_number_format($row[('cut_qty')]-$row[('sew_in')],0);?></td>	                    
									<td rowspan="<?=$jobspan;?>" width="80" align="right"><? echo fn_number_format($row[('sew_out')],0);?></td>
									<td rowspan="<?=$jobspan;?>" width="80" align="right" ><? echo fn_number_format($row[('sew_in')]-$row[('sew_out')],0);?></td>	

									<td rowspan="<?=$jobspan;?>" width="80" align="right"><? echo fn_number_format($job_wise_wash_qty[$row[("job_id")]]['send_qty'],0);?></td>
									<td rowspan="<?=$jobspan;?>" width="80" align="right"><? echo fn_number_format($row[('sew_out')]-$job_wise_wash_qty[$row[("job_id")]]['send_qty'],0);?></td>	                    
									<td rowspan="<?=$jobspan;?>" width="80" align="right"><? echo fn_number_format($job_wise_wash_qty[$row[("job_id")]]['recv_qty']);?></td>
									<td rowspan="<?=$jobspan;?>" width="80" align="right" ><? echo fn_number_format($job_wise_wash_qty[$row[("job_id")]]['send_qty']-$job_wise_wash_qty[$row[("job_id")]]['recv_qty']);?></td>	

									<td rowspan="<?=$jobspan;?>" width="80" align="right" <? //echo $fabAvlBgColor;?> title="<? //echo $fabAvlPer;?>">
										<? echo fn_number_format($row[('fin_qty')],0);?>
									</td>	
									<td rowspan="<?=$jobspan;?>" width="80" align="right"  ><? echo fn_number_format($row[('po_quantity')]-$row[('ex_factQty')],0);?></td>
									
									<td rowspan="<?=$jobspan;?>" width="80" align="right"><a href="##" onClick="generate_ex_factory_popup('style_ex_factory_popup','<?=$row[('job_no_prefix_num')];?>','<?=implode(",", $po_ids); ?>','<?=str_replace("'","",$txt_date_from) ?>','<?=str_replace("'","",$txt_date_to) ?>','850px')"><?=fn_number_format($row['ex_factQty'],0); ?></a><? //echo omitZero(number_format($row[('ex_factQty')],0));?></td>
									<?
									$prod_qty=$row[('po_quantity')]-$row[('ex_factQty')];
									
									?>
									
									<td rowspan="<?=$jobspan;?>" width="" align="right" ><? echo fn_number_format($prod_qty,2);?></td>	
								<?php endif ?>
							
							</tr>
							<?
						
							$job_span++;
						}
					}
				

					$tot_po_quantity+=$row[('po_quantity')];
				
			
					$tot_cut_qnty+=$row[('cut_qty')];
					$tot_sew_in+=$row[('sew_in')];
					$tot_sew_out+=$row[('sew_out')];
					
					$tot_sewing_in_qnty+=$row[('sew_in')];
					$tot_sewing_out_qnty+=$row[('sew_out')];
					$tot_sewing_finish_qnty+=$row[('fin_qty')];
					$tot_sewing_prod_p_loss+=$row[('sew_in')]-$row[('sew_out')];
					
					
					$tot_prod_qty+=$prod_qty;
					$tot_fin_qty+=$row[('fin_qty')];
					$tot_ex_factQty+=$row[('ex_factQty')];

					$tot_wash_send+=$job_wise_wash_qty[$row[("job_id")]]['send_qty'];
					$tot_wash_recv+=$job_wise_wash_qty[$row[("job_id")]]['recv_qty'];
					

					$tot_printing_send+=$job_wise_printing_qty[$row[("job_id")]]['send_qty'];
					$tot_printing_recv+=$job_wise_printing_qty[$row[("job_id")]]['recv_qty'];
					
					

					$tot_embroidery_send+=$job_wise_embroidery_qty[$row[("job_id")]]['send_qty'];
					$tot_embroidery_recv+=$job_wise_embroidery_qty[$row[("job_id")]]['recv_qty'];
					
					
					
					$i++;
				}
					?> 
					</table>
			</div> 
			<table class="tbl_bottom" width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
				<tr>
					<td width="30">&nbsp; </td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>	
					<td width="60">&nbsp;</td>	
					<td width="120">Total:</td>	
					<td width="80" align="right"id="po_qty_th"><? echo number_format($tot_po_quantity,0);?></td>
					<td width="80">&nbsp;</td>
					<td width="80">&nbsp;</td>

					<td width="400">&nbsp;</td>
					<td width="80">&nbsp;</td>
					<td width="80">&nbsp;</td>
					<td width="80">&nbsp;</td>
					<td width="80">&nbsp;</td>
					<td width="80">&nbsp;</td>
					<td width="80">&nbsp;</td>
					<td width="80">&nbsp;</td>
					<td width="80">&nbsp;</td>
					<td width="80">&nbsp;</td>

					<td width="80" align="right"id="cut_qty_th"><? echo number_format($tot_cut_qnty,0);?></td>
					<td width="80"><? if($tot_cut_qnty) echo number_format(($tot_cut_qnty/$tot_po_quantity)*100,2)."%";else echo " ";?></td>
					
					<td width="80" align="right"> <p style="word-wrap:break-word;"><? echo fn_number_format($tot_printing_send,0);?></p></td>	
					<td width="80" align="right" class=""   id="sew_ploss_qty_th"><p > </p></td>
					<td width="80" align="right" id="sew_in_qty_th"><p><? echo fn_number_format($tot_printing_recv,0);?></p></td>				
					<td width="80" align="right"><p style="word-wrap:break-word;"></p></td>	

					<td width="80" align="right"> <p style="word-wrap:break-word;"><? echo fn_number_format($tot_embroidery_send,0);?></p></td>	
					<td width="80" align="right" class=""   id="sew_ploss_qty_th"><p > </p></td>
					<td width="80" align="right" id="sew_in_qty_th"><p><? echo fn_number_format($tot_embroidery_recv,0);?></p></td>				
					<td width="80" align="right"><p style="word-wrap:break-word;"></p></td>

					<td width="80" align="right"> <p   style="word-wrap:break-word;"><? echo fn_number_format($tot_sew_in,0);?></p></td>	
					<td width="80" align="right" class=""   id="sew_ploss_qty_th"><p > </p></td>
					<td width="80" align="right" id="sew_in_qty_th"><p><? echo number_format($tot_sew_out,0);?></p></td>		
					<td width="80" align="right"><p style="word-wrap:break-word;"></p></td>	

					<td width="80" align="right"> <p style="word-wrap:break-word;"><? echo fn_number_format($tot_wash_send,0);?></p></td>	
					<td width="80" align="right" class=""   id="sew_ploss_qty_th"><p > </p></td>
					<td width="80" align="right" id="sew_in_qty_th"><p><? echo fn_number_format($tot_wash_recv,0);?></p></td>				
					<td width="80" align="right"><p style="word-wrap:break-word;"></p></td>	

					<td width="80" align="right" id="td_tot_fin_qty"><p  style="word-wrap:break-word;"><? echo number_format($tot_fin_qty,0);?></p></td>	
					<td width="80" align="right" id="td_fin_loss"><p  style="word-wrap:break-word;"></p></td>	
						
					<td width="80" id=""><p  style="word-wrap:break-word;"><? echo number_format($tot_ex_factQty,0);?></p></td>	
					<td width="" align="right" ><p ></p></td>	  
				
				</tr>
			</table>  
			<style>
			
			</style>
		</fieldset>
	 
		<?
	}
	 	
	$html = ob_get_contents();
	ob_clean();
	 
	foreach (glob($_SESSION['logic_erp']['user_id']."*.xls") as $filename)
	{
		@unlink($filename);
	}
	
	$name=time();
	$filename=$_SESSION['logic_erp']['user_id']."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,$html);
	echo "$html****$filename****$report_type";
	exit();	
}
if($action=="style_ex_factory_popup")
{
	echo load_html_head_contents("Ex-Factory Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);

	$unit_price_arr=return_library_array( "select id,po_number, unit_price from wo_po_break_down where status_active=1 and is_deleted=0 and id in ($id)","id","unit_price");
	?>
	<script>

		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

			d.close();
		}

	</script>
	<div style="width:100%" align="center" id="report_container">
		<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<fieldset style="width:800px">
			<div class="form_caption" align="center"><strong>Ex-Factory Details</strong></div><br />
			<div style="width:100%">
				<table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
					<thead>
						<tr>
							<th width="35">SL</th>
							<th width="80">Order Number</th>
							<th width="80">Order Qty</th>
							<th width="90">Ex-fac. Date</th>
							<th width="100">System /Challan no</th>
							<th width="80">Ex-Fact. Del.Qty.</th>
							<th width="80">Ex-Fact. Return Qty.</th>
							<th width="80">Balance Qty.</th>
							<th width="100">Delivery Status</th>
							<th width="">Ex-Fact. Value</th>

						</tr>
					</thead>
				</table>
			</div>
			<div style="width:100%; max-height:400px;">
				<table cellpadding="0" width="100%" cellspacing="0" border="1" rules="all" class="rpt_table">
					<?
					$i=1;
					if($cbo_date_type==2)
					{
						if($start_date !=='' & $end_date !==''){
							$date_cond2=" and b.ex_factory_date between '$start_date' and '$end_date'";
						}
					}

					$sql_dtls=sql_select($exfac_sql);

					$exfac_sql="SELECT a.po_number, a.po_quantity ,b.challan_no,b.shiping_status,b.ex_factory_date,b.po_break_down_id as po_id,
					CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_qnty,
					CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_return_qnty
					from  wo_po_break_down a,pro_ex_factory_mst b where a.id=b.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_break_down_id in($id) order by b.ex_factory_date asc ";

					$sql_dtls=sql_select($exfac_sql);




					foreach($sql_dtls as $row_real)
					{
						if ($i%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
						$tot_exfact_qty=$row_real[csf("ex_factory_qnty")]-$row_real[csf("ex_factory_return_qnty")];
						$tot_exfact_val=$tot_exfact_qty*$unit_price_arr[$row_real[csf("po_id")]];
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_l<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="35"><? echo $i; ?></td>
							<td width="80"  align="center"><? echo $row_real[csf("po_number")]; ?></td>
							<td width="80"  align="right"><? echo number_format($row_real[csf("po_quantity")],2); ?></td>
							<td width="90"><? echo change_date_format($row_real[csf("ex_factory_date")]); ?></td>
							<td width="100"><? echo $row_real[csf("challan_no")]; ?></td>
							<td width="80" align="right"><? echo number_format($row_real[csf("ex_factory_qnty")],2); ?></td>
							<td width="80" align="right"><? echo number_format($row_real[csf("ex_factory_return_qnty")],2); ?></td>
							<td width="80" align="right">
								<?
								if($balance_qnty_array[$row_real[csf("po_number")]]>0){
									$balance_qty = $balance_qnty_array[$row_real[csf("po_number")]] - $row_real[csf("ex_factory_qnty")] + $row_real[csf("ex_factory_return_qnty")];
									echo number_format($balance_qty,2);
									$balance_qnty_array[$row_real[csf("po_number")]]=$balance_qty;
								}else{
									$balance_qty = $row_real[csf("po_quantity")] - $row_real[csf("ex_factory_qnty")] + $row_real[csf("ex_factory_return_qnty")];
									echo number_format($balance_qty,2);
									$balance_qnty_array[$row_real[csf("po_number")]]=$balance_qty;
								}
									
								?>
							</td>
							<td width="100"  align="center"><? echo $shipment_status[$row_real[csf("shiping_status")]]; ?></td>
							<td width="" align="right" title="Unit Rate=<? echo $unit_price_arr[$row_real[csf("po_id")]];?>"><? echo number_format($tot_exfact_val,2); ?></td>
						</tr>
						<?
						$total_po_qty+=$row_real[csf("po_quantity")];
						$rec_qnty+=$row_real[csf("ex_factory_qnty")];
						$rec_return_qnty+=$row_real[csf("ex_factory_return_qnty")];
						$total_exfact_val+=$tot_exfact_val;
						$total_balance_qty+=$balance_qty;
						$i++;
					}
					?>
					<tfoot>
						<tr>
							<th colspan="2">Total</th>
							<th><? echo number_format($total_po_qty,2); ?></th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th><? echo number_format($rec_qnty,2); ?></th>
							<th><? echo number_format($rec_return_qnty,2); ?></th>
							<th><? echo number_format($total_balance_qty,2); ?></th>
							<th>&nbsp;</th>
							<th><? echo number_format($total_exfact_val,2); ?></th>
						</tr>
						<!-- <tr>
							<th colspan="3">Total Balance</th>
							<th colspan="2" align="right"><? //echo number_format($rec_qnty-$rec_return_qnty,2); ?></th>
							<th></th>
							<th></th>
							<th><? //echo number_format($total_exfact_val,2); ?></th>
						</tr> -->
					</tfoot>
				</table>
			</div>
		</fieldset>
	</div>
	<?
	exit();
}
if ($action=='report_generate_stylewise') {
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$report_type=str_replace("'","",$report_type);
	$txt_style_ref = str_replace("'", '', $txt_style_ref);
	$from_date=str_replace("'", '', $txt_date_from);
	$to_date=str_replace("'", '', $txt_date_to);
	$search_cond = '';

	$year = str_replace("'", '', $cbo_year_selection);
	$cbo_year = str_replace("'", '', $cbo_year);

	if($cbo_year != 0) {
		$year = str_replace("'", '', $cbo_year);
	}

	$from_date = ($from_date == '') ? "01-01-$year" : '';
	$to_date = ($to_date == '') ? "31-12-$year" : '';

	/*if( ($jobNo=='') && ($txt_style_ref=='') && ($txt_order_no=='') && ($txt_file_no=='') && ($txt_ref_no=='') ) {
		$year = str_replace("'", '', $cbo_year_selection);
		$cbo_year = str_replace("'", '', $cbo_year);

		if($cbo_year != 0) {
			$year = str_replace("'", '', $cbo_year);
		}

		$from_date = ($from_date == '') ? "01-01-$year" : '';
		$to_date = ($to_date == '') ? "31-12-$year" : '';
	}*/

	if($jobNo != '') {
		$search_cond = "and a.job_no like '%$jobNo'";
	}
	

	if($txt_style_ref != '') {
		$search_cond = "and a.style_ref_no = '$txt_style_ref'";
	}

	if($txt_order_no != '') {
		$search_cond = "and b.po_number = '$txt_order_no'";
	}

	$po_no_arr = array();
	$booking_no_arr = array();
	if($db_type==0)
	{
		if ($from_date!="" &&  $to_date!="") $shipment_date_cond = "and b.pub_shipment_date between '".change_date_format($from_date, "yyyy-mm-dd", "-")."' and '".change_date_format($to_date, "yyyy-mm-dd", "-")."'"; else $shipment_date_cond = "";
	}
	if($db_type==2)
	{
		if ($from_date!="" &&  $to_date!="") $shipment_date_cond = "and b.pub_shipment_date between '".change_date_format($from_date,'','',1)."' and '".change_date_format($to_date,'','',1)."'"; else $shipment_date_cond = "";
	}

	$items_library=return_library_array( "select id, item_name from lib_garment_item where status_active=1 and is_deleted=0", "id", "item_name");
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0", 'id', 'buyer_name');
	$brand_library=return_library_array("select id, brand_name from lib_buyer_brand where status_active=1 and is_deleted=0", 'id', 'brand_name');
	$price_quatation_dia=return_library_array("select gmts_sizes,dia_width from wo_pri_quo_fab_co_avg_con_dtls where wo_pri_quo_fab_co_dtls_id=$pri_fab_cost_dtls_id", "gmts_sizes","dia_width");
	$color_library=return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$trim_group_library= return_library_array("select id, item_name from lib_item_group", 'id', 'item_name');
	$supplier_library = return_library_array("select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0", 'id', 'supplier_name');
	$size_library = return_library_array("select id, size_name from lib_size", 'id', 'size_name');
	$season_library = return_library_array("select id, season_name from lib_buyer_season", 'id', 'season_name');

	$mst_query = "select a.id, b.id as po_break_down_id, a.buyer_name, a.brand_id, a.season_buyer_wise, a.season_year, a.style_ref_no, a.style_description, b.grouping, a.job_no
			from wo_po_details_master a, wo_po_break_down b
			where a.company_name = $cbo_company_name $search_cond and b.job_no_mst = a.job_no and a.status_active=1 and b.status_active=1";
	// echo $mst_query;
	$mst_result = sql_select($mst_query);

	foreach ($mst_result as $row) {
		$po_no_arr[] = $row[csf('po_break_down_id')];
		$txt_job_no = "'" . $row[csf('job_no')] . "'";
	}
	$po_no_arr = array_unique($po_no_arr);

	$po_no_str = implode(',', $po_no_arr);

	$fabric_sql = "select a.id as fabric_cost_id, a.job_no, a.fabric_description, a.item_number_id, a.uom, b.color_number_id, a.avg_cons, a.rate, a.amount, a.avg_finish_cons, a.lib_yarn_count_deter_id, a.job_plan_cut_qty, b.dia_width, b.total, a.composition, b.po_break_down_id, b.item_size
	from wo_pre_cost_fabric_cost_dtls a
    join wo_pre_cos_fab_co_avg_con_dtls b on a.id = b.pre_cost_fabric_cost_dtls_id
	where a.job_no = $txt_job_no and a.status_active=1 and a.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0 and b.cons <> 0
	group by a.id, a.job_no, a.fabric_description, a.item_number_id, a.uom, b.color_number_id, a.avg_cons, a.rate, a.amount, a.avg_finish_cons, a.lib_yarn_count_deter_id, a.job_plan_cut_qty, b.dia_width, b.total, a.composition, b.po_break_down_id, b.item_size";

	// echo $fabric_sql;
	$fabric_rowspan = array();
	$fabric_result = sql_select($fabric_sql);

	$fabric_arr = array();
	foreach ($fabric_result as $row) {
		$fabric_arr[$row[csf('fabric_description')]][$row[csf('color_number_id')]]['fabric_description'] = $row[csf('fabric_description')];
		$fabric_arr[$row[csf('fabric_description')]][$row[csf('color_number_id')]]['color_number_id'] = $row[csf('color_number_id')];
		$fabric_arr[$row[csf('fabric_description')]][$row[csf('color_number_id')]]['width'] = $row[csf('dia_width')];
		$fabric_arr[$row[csf('fabric_description')]][$row[csf('color_number_id')]]['uom'] = $row[csf('uom')];
		$fabric_arr[$row[csf('fabric_description')]][$row[csf('color_number_id')]]['composition'] = $row[csf('composition')];
		$fabric_arr[$row[csf('fabric_description')]][$row[csf('color_number_id')]]['po_break_down_id'] = $row[csf('po_break_down_id')];
		$fabric_arr[$row[csf('fabric_description')]][$row[csf('color_number_id')]]['lib_yarn_count_deter_id'] = $row[csf('lib_yarn_count_deter_id')];
		$fabric_arr[$row[csf('fabric_description')]][$row[csf('color_number_id')]]['fabric_cost_id'] = $row[csf('fabric_cost_id')];
		$fabric_arr[$row[csf('fabric_description')]][$row[csf('color_number_id')]]['cutable_width'] = $row[csf('item_size')];
	}

	foreach ($fabric_arr as $fabric_desc => $fabricDescArr) {
		foreach ($fabricDescArr as $row) {
			$fabric_rowspan[$row['fabric_description']]++;
		}
	}

	$condition= new condition();
	if(str_replace("'","",$txt_job_no) !=''){
		$condition->job_no("=$txt_job_no");
	}

	$condition->init();
	$fabric= new fabric($condition);
	$trim= new trims($condition);

	$fabric_qty_arr=$fabric->getQtyArray_by_OrderFabriccostidGmtscolorAndDiaWidth_knitAndwoven_greyAndfinish();

	$fabric_req_qty = array();

	if($fabric_qty_arr['woven']['finish'] > 0) {
		foreach($fabric_qty_arr['woven']['finish'] as $poId=>$poArr){
			foreach($poArr as $fabricCostId=>$fabricCostArr){
				foreach ($fabricCostArr as $colorId => $colorArr) {
					foreach ($colorArr as $diaWidth => $diaArr) {
						foreach ($diaArr as $reqQty) {
							$fabric_req_qty[$fabricCostId][$colorId]['req_qty'] += $reqQty;
							$fabric_req_qty[$fabricCostId][$colorId]['dia_width'] = $diaWidth;
						}
					}
				}
			}
		}
	}

	$booking_sql = "select b.pre_cost_fabric_cost_dtls_id, b.po_break_down_id, b.color_number_id, a.id AS booking_id, a.fin_fab_qnty, a.grey_fab_qnty, a.rate, a.amount, a.adjust_qty, a.remark, a.dia_width, a.pre_cost_remarks, a.copmposition, a.booking_no, b.item_size, c.supplier_id
		from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_mst c
 		where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.po_break_down_id=b.po_break_down_id and a.gmts_color_id=b.color_number_id and a.dia_width=b.dia_width and a.po_break_down_id in($po_no_str) and b.cons>0 and a.booking_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no=c.booking_no
		group by b.pre_cost_fabric_cost_dtls_id,b.po_break_down_id,b.color_number_id,a.id,a.fin_fab_qnty,a.grey_fab_qnty,a.rate,a.amount, a.adjust_qty,a.remark,a.dia_width,a.pre_cost_remarks, a.copmposition, a.booking_no, b.item_size, c.supplier_id";

	// echo $booking_sql;
	$booking_result = sql_select($booking_sql);

	$booking_arr = array();

	foreach ($booking_result as $row) {
		if( isset($booking_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('color_number_id')]]) ) {
			// $booking_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('color_number_id')]]['receive_qty'] += $row[csf('grey_fab_qnty')];
			$booking_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('color_number_id')]]['booking_qty'] += $row[csf('grey_fab_qnty')];
		} else {
			// $booking_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('color_number_id')]]['receive_qty'] = $row[csf('grey_fab_qnty')];
			$booking_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('color_number_id')]]['booking_qty'] += $row[csf('grey_fab_qnty')];
		}
		$booking_no_array[] = $row[csf('booking_no')];
		$booking_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('color_number_id')]]['cutable_width'] = $row[csf('item_size')];
		// $supplierId = $row[csf('supplier_id')];
	}

	$booking_no_array = array_unique($booking_no_array);
	$booking_no_str = implode('\',\'', $booking_no_array);

	$trims_sql = "select id, job_no, trim_group, description, brand_sup_ref, remark, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, status_active, seq
	from wo_pre_cost_trim_cost_dtls
	where job_no=".$txt_job_no;
	//echo $trims_sql;
	$trims_result=sql_select($trims_sql);

	// echo $trims_sql;

	$trim_amount_arr=$trim->getAmountArray_by_jobAndPrecostdtlsid();
	$trim_qty_arr=$trim->getQtyArray_by_jobAndPrecostdtlsid();
	$totTrim=0;
	$trims_arr=array();
	foreach($trims_result as $row){
	    $trim_qty=$trim_qty_arr[$row[csf("job_no")]][$row[csf("id")]];
		$trim_amount=$trim_amount_arr[$row[csf("job_no")]][$row[csf("id")]];
		$summary_data[trims_cost_job]+=$trim_amount;

		$trims_arr[$row[csf('id')]]['trim_group']=$row[csf('trim_group')];
		$trims_arr[$row[csf('id')]]['description']=$row[csf('description')];
		$trims_arr[$row[csf('id')]]['brand_sup_ref']=$row[csf('brand_sup_ref')];
		$trims_arr[$row[csf('id')]]['remark']=$row[csf('remark')];
		$trims_arr[$row[csf('id')]]['cons_uom']=$row[csf('cons_uom')];
		$trims_arr[$row[csf('id')]]['cons_dzn_gmts']=$row[csf('cons_dzn_gmts')];
		$trims_arr[$row[csf('id')]]['rate']=$row[csf('rate')];
		$trims_arr[$row[csf('id')]]['amount']=$row[csf('amount')];
		$trims_arr[$row[csf('id')]]['apvl_req']=$row[csf('apvl_req')];
		$trims_arr[$row[csf('id')]]['nominated_supp']=$row[csf('nominated_supp_multi')];
		$trims_arr[$row[csf('id')]]['tot_cons']=$trim_qty;
		$trims_arr[$row[csf('id')]]['tot_amount']=$trim_amount;
		$totTrim+=$row[csf('cons_dzn_gmts')];
	}


	$trims_booking_sql = "select a.pre_cost_fabric_cost_dtls_id, b.description, b.item_color, sum (b.requirment) as cons, a.trim_group, a.booking_no, c.supplier_id
    				from wo_booking_dtls a, wo_trim_book_con_dtls b, wo_booking_mst c
   					where a.id = b.wo_trim_booking_dtls_id and a.booking_no=c.booking_no and a.booking_no = b.booking_no and a.job_no = $txt_job_no and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0
					group by a.pre_cost_fabric_cost_dtls_id, b.description, b.item_color, a.trim_group, a.booking_no, c.supplier_id";
	// echo $trims_booking_sql;
	$trims_booking_result=sql_select($trims_booking_sql);
	$trims_booking_no = '';

	$trims_booking_arr=array();
	foreach($trims_booking_result as $row) {
		if( isset($trims_booking_arr[$row[csf('trim_group')]]) ) {
			$trims_booking_arr[$row[csf('trim_group')]]['cons'] += $row[csf('cons')];
		} else {
			$trims_booking_arr[$row[csf('trim_group')]]['cons'] = $row[csf('cons')];
		}
		$trims_booking_no = $row[csf('booking_no')];

		$trims_booking_arr[$row[csf('trim_group')]]['supplier_id'] = $row[csf('supplier_id')];
		$trims_booking_arr[$row[csf('trim_group')]]['description'] = $row[csf('description')];
	}

	$trims_receive_sql = "select a.booking_no, b.order_id as po_id, b.item_group_id, b.item_description, b.gmts_color_id, b.item_color, sum (c.quantity) as receive_qnty, b.prod_id, a.supplier_id
    		from inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c
   			where a.id = b.mst_id and a.booking_no = b.booking_no and b.id = c.dtls_id and b.trans_id = c.trans_id and c.entry_form = 24 and a.entry_form = 24 and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 and c.trans_type = 1 and c.po_breakdown_id in ($po_no_str)
			group by a.booking_no, b.order_id, b.item_group_id, b.item_description, b.gmts_color_id, b.item_color, b.prod_id, a.supplier_id";

	// echo $trims_receive_sql;
	$trims_receive_result=sql_select($trims_receive_sql);
	$prod_id_arr = array();
	$trims_receive_arr=array();
	foreach($trims_receive_result as $row) {
		if( isset($trims_receive_arr[$row[csf('item_group_id')]]) ) {
			$trims_receive_arr[$row[csf('item_group_id')]]['receive_qty'] += $row[csf('receive_qnty')];
		} else {
			$trims_receive_arr[$row[csf('item_group_id')]]['receive_qty'] = $row[csf('receive_qnty')];
		}
		$trims_receive_arr[$row[csf('item_group_id')]]['supplier_id'] = $row[csf('supplier_id')];
		$prod_id_arr[] = $row[csf('prod_id')];
	}

	$prod_id_str = implode(',', array_unique($prod_id_arr));

	/*$receive_sql = "select a.recv_number, a.receive_date, a.receive_basis, b.cons_quantity as receive_qty, a.buyer_id, case when a.receive_basis = 2 then a.booking_no else null end as wo_number, case when a.receive_basis = 1 then a.booking_no else null end as pi_number, c.fabric_color_id, c.grey_fab_qnty, d.lib_yarn_count_deter_id, a.challan_no, b.batch_lot
    		from inv_transaction b, inv_receive_master a, wo_booking_dtls c, wo_pre_cost_fabric_cost_dtls d
   			where a.booking_no in('$booking_no_str') and a.booking_no=c.booking_no and c.pre_cost_fabric_cost_dtls_id = d.id and a.id = b.mst_id and a.entry_form = 17 and a.item_category = 3 and b.item_category = 3 and b.transaction_type = 1 and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0
		group by a.receive_date, a.receive_basis, a.buyer_id, a.booking_no, a.recv_number, c.fabric_color_id, c.grey_fab_qnty, d.lib_yarn_count_deter_id, a.challan_no, b.batch_lot, b.cons_quantity";*/

	$receive_sql = "select c.receive_basis, c.booking_without_order as without_order, a.fabric_description_id, c.store_id, a.uom, a.rate, a.color_id, b.po_breakdown_id as po_id, b.quantity, b.order_amount as amount, b.trans_type
  		from inv_receive_master c, pro_finish_fabric_rcv_dtls a, order_wise_pro_details b
 		where a.trans_id = b.trans_id and c.id = a.mst_id and b.trans_type = 1 and b.entry_form = 17 and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.item_category = 3 and c.entry_form = 17 and (b.po_breakdown_id in ($po_no_str))";
	// echo $receive_sql;
	$receive_result = sql_select($receive_sql);

	$receive_arr = array();
	$batch_lot = '';
	foreach ($receive_result as $row) {
		if( isset($receive_arr[$row[csf('fabric_description_id')]][$row[csf('color_id')]]) ) {
			$receive_arr[$row[csf('fabric_description_id')]][$row[csf('color_id')]]['receive_qty'] += $row[csf('quantity')];
		} else {
			$receive_arr[$row[csf('fabric_description_id')]][$row[csf('color_id')]]['receive_qty'] = $row[csf('quantity')];
		}
		$batch_lot = $row[csf('batch_lot')];
	}

	$trims_issue_sql = "select id, item_group_id, item_description, item_color_id, item_size, order_id, item_order_id, issue_qnty
    					from inv_trims_issue_dtls
   						where prod_id in($prod_id_str) and status_active = '1' and is_deleted = '0'";


   	$trims_issue_result = sql_select($trims_issue_sql);

	$trims_issue_arr = array();
	$batch_lot = '';
	foreach ($trims_issue_result as $row) {
		if( isset($trims_issue_arr[$row[csf('item_group_id')]]) ) {
			$trims_issue_arr[$row[csf('item_group_id')]]['issue_qty'] += $row[csf('issue_qnty')];
		} else {
			$trims_issue_arr[$row[csf('item_group_id')]]['issue_qty'] = $row[csf('issue_qnty')];
		}
	}

	/*$issue_sql = "select a.prod_id, a.batch_id, a.body_part_id, c.order_id,
		         (case
		             when a.floor_id is null or a.floor_id = 0 then 0
		             else a.floor_id
		          end)
            		floor_id,
			        nvl (a.rack, 0) rack,
			         (case when a.room is null or a.room = 0 then 0 else a.room end) room,
			         (case when a.self is null or a.self = 0 then 0 else a.self end) self,
			         (case
			             when a.bin_box is null or a.bin_box = 0 then 0
			             else a.bin_box
			          end)
            		bin_box,
			        sum (case when a.transaction_type = 2 then cons_quantity end)
			            as issue_qnty,
			          d.color_id
    			from inv_issue_master b, inv_transaction a, inv_wvn_finish_fab_iss_dtls c, pro_batch_create_mst d
   				where b.entry_form = 19 and b.id = a.mst_id and a.id = c.trans_id and a.status_active = 1 and a.is_deleted = 0 and a.item_category = 3
         		and a.transaction_type = 2 and c.status_active = 1 and c.is_deleted = 0 and a.batch_id = d.id and d.booking_no in('$booking_no_str') and b.status_active = 1 and b.is_deleted = 0 and b.company_id = 1
				group by a.prod_id, a.batch_id, a.body_part_id, a.floor_id, a.room, a.rack, a.self, a.bin_box, d.color_id, c.order_id";*/

	$issue_sql = "select b.po_breakdown_id, a.color as color_id, a.item_size, a.detarmination_id, b.quantity, c.cons_rate as rate, b.entry_form, b.trans_type, d.fabric_description
  		from product_details_master a, order_wise_pro_details b, inv_transaction c, wo_pre_cost_fabric_cost_dtls d
 		where a.id = b.prod_id and b.trans_id = c.id and a.detarmination_id=d.lib_yarn_count_deter_id and d.job_no=$txt_job_no and item_category_id = 3 and a.entry_form = 0 and b.entry_form in (19, 202, 209, 258) and b.trans_type in (2, 3, 4, 6, 5) and c.transaction_type in (2, 3, 4, 6, 5) and a.status_active = 1 and b.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 and (b.po_breakdown_id in ($po_no_str))";
	// echo $issue_sql;
	$issue_result = sql_select($issue_sql);

	$issue_arr = array();

	foreach ($issue_result as $row) {
		if( $row[csf('entry_form')]==19 && $row[csf('trans_type')] == 2 ) {
			if( isset($issue_arr[$row[csf('fabric_description')]][$row[csf('color_id')]]) ) {
				$issue_arr[$row[csf('fabric_description')]][$row[csf('color_id')]]['issue_qty'] += $row[csf('quantity')];
			} else {
				$issue_arr[$row[csf('fabric_description')]][$row[csf('color_id')]]['issue_qty'] = $row[csf('quantity')];
			}
		}
	}

	$color_sql = "select a.job_no, (a.job_quantity * a.total_set_qnty) as job_quantity, (c.po_quantity * a.total_set_qnty) as po_quantity, c.po_total_price, d.size_number_id, d.color_number_id, d.order_quantity, d.order_total
    	from wo_po_details_master a
        left join wo_po_break_down c 
        	on a.job_no = c.job_no_mst and c.is_deleted = 0 and c.status_active = 1
        left join wo_po_color_size_breakdown d
            on c.job_no_mst = d.job_no_mst and c.id = d.po_break_down_id and d.is_deleted = 0 and d.status_active = 1
   		where a.is_deleted = 0 and a.status_active = 1 and a.company_name = $cbo_company_name and to_char (a.insert_date, 'yyyy') = 2020 and a.job_no = $txt_job_no";

   	// echo $color_sql;
   	$color_result = sql_select($color_sql);

   	$color_arr = array();
   	$size_arr = array();

   	foreach ($color_result as $row) {
   		// $color_arr[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['size_number_id'] = $row[csf('size_number_id')];
   		$color_arr[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['order_quantity'] += $row[csf('order_quantity')];

   		$size_arr[] = $row[csf('size_number_id')];
   	}

   	$size_arr = array_unique($size_arr);

   	ob_start();
	?>
	<div id="report_div">
		<style>
			.heading th, .heading td {
				font-size: 12pt;
			}
		</style>
		<div>
			<table width="80%" cellpadding="0" cellspacing="0" style="margin: 30px auto;">
				<thead class="heading">
					<th>Buyer:</th>
					<td><?php echo $buyer_library[$mst_result[0][csf('buyer_name')]]; ?></td>
					<th>Brand:</th>
					<td><?php echo $brand_library[$mst_result[0][csf('brand_id')]]; ?></td>
					<th>Season:</th>
					<td><?php echo $season_library[$mst_result[0][csf('season_buyer_wise')]]; ?></td>
					<th>Season Year:</th>
					<td><?php echo $mst_result[0][csf('season_year')] ? $mst_result[0][csf('season_year')] : ''; ?></td>
					<th>Style Ref:</th>
					<td><?php echo $mst_result[0][csf('style_ref_no')]; ?></td>
					<th>Style Description:</th>
					<td><?php echo $mst_result[0][csf('style_description')]; ?></td>
					<th>Master Style/Int. Ref:</th>
					<td align="left"><?php echo $mst_result[0][csf('grouping')]; ?></td>
					<th>Job no:</th>
					<td><?php echo $mst_result[0][csf('job_no')]; ?></td>
				</thead>
			</table>
		</div>
		<div>
			<fieldset style="width: 500px;">
		        <table width="100%" cellpadding="0" cellspacing="0">
		            <tr><td colspan="12" align="center" style="font-size:22px;">Color Size Breakdown</td></tr>
		        </table>
		        <table width="100%" border="1" rules="all"  class="rpt_table"> 
		            <thead>
		            	<tr style="font-size:12px"> 
					        <th>color/size</th>
					        <?php
					        	foreach($size_arr as $size) {
					        		?>
					        			<th><?php echo $size_library[$size]; ?></th>
					        		<?php
					        	}
					        ?>
			            	<th>Total</th>
			            </tr>
			            <?php
			            	foreach ($color_arr as $colorId => $colorArr) {
			            		$colorTotal = 0;
			            		?>
			            		<tr>
			            			<td><?php echo $color_library[$colorId]; ?></td>
			            		<?php
			            		foreach ($colorArr as $value) {
			            				$ordQty = $value['order_quantity'];
			            				$colorTotal += $ordQty;
			            			?>
			            				<td><?php echo $ordQty; ?></td>
			            			<?php
			            		}
			            		?>
			            		<td><?php echo $colorTotal; ?></td>
			            		</tr>
			            		<?php
			            	}
			            ?>
		            </thead>
		        </table>
		    </fieldset>
		</div>
	    <div style="margin-top: 20px;">
	    	<fieldset style="width: 1000px;">
		        <table width="100%" cellpadding="0" cellspacing="0">
		            <tr><td colspan="12" align="center" style="font-size:22px;">Fabric Details</td></tr>
		        </table>
		        <table width="100%" border="1" rules="all"  class="rpt_table"> 
		            <thead>
		                <tr style="font-size:12px"> 
		                    <th>Fabrication</th>
							<th>GMT Color</th>
							<th>Width/Cutable Width</th>
							<th>UOM</th>
							<th>Required Qty</th>
							<th>Booking Qty</th>
							<th>Received Qty</th>
							<th>Issue Qty</th>
							<th>Issue Balance</th>
		               </tr>
		            </thead>
		            <tbody>
		            	<?php
		            		foreach ($fabric_arr as $fabDesc => $fabDescArr) {
		            			$rowspanCount = 0;
		            			foreach ($fabDescArr as $colorId => $value) {
		            					$receiveQty = $receive_arr[$value['lib_yarn_count_deter_id']][$value['color_number_id']]['receive_qty'];
		            					$issueQty = $issue_arr[$fabDesc][$colorId]['issue_qty'];
		            					$cutableWidth = $value['cutable_width'];
	            					?>
		            				<tr>
		            					<?php
		            						if($rowspanCount == 0) {
		            					?>
		            					<td rowspan="<?php echo $fabric_rowspan[$fabDesc]; ?>"><?php echo $value['fabric_description']; ?></td>
		            					<?php
		            						}
		            					?>
		            					<td><?php echo $color_library[$colorId]; ?></td>
		            					<td><?php echo $fabric_req_qty[$value['fabric_cost_id']][$value['color_number_id']]['dia_width'] . '/' . $cutableWidth; ?></td>
		            					<td><?php echo $unit_of_measurement[$value['uom']]; ?></td>
		            					<td><?php echo $fabric_req_qty[$value['fabric_cost_id']][$value['color_number_id']]['req_qty']; ?></td>
		            					<td><?php echo $booking_arr[$value['fabric_cost_id']][$colorId]['booking_qty']; ?></td>
		            					<td><?php echo $receiveQty; ?></td>
		            					<td><?php echo $issueQty; ?></td>
		            					<td><?php echo ($receiveQty - $issueQty); ?></td>
		            				</tr>
	            					<?php
	            					$rowspanCount++;
		            			}
		            		}
		            	?>
		            </tbody>
		        </table>
		    </fieldset>
	    </div>
	    <div style="margin-top: 20px;">
	    	<fieldset style="width: 1000px;">
		        <table width="100%" cellpadding="0" cellspacing="0">
		            <tr><td colspan="12" align="center" style="font-size:22px;">Accessories Details</td></tr>
		        </table>
		        <table width="100%" border="1" rules="all"  class="rpt_table"> 
		            <thead>
		                <tr style="font-size:12px"> 
		                    <th>Item Group</th>
							<th>Description</th>
							<th>Supplier Name</th>
							<th>Cons/Dzn</th>
							<th>UOM</th>
							<th>Required Qty</th>
							<th>Booking Qty</th>
							<th>Receive Qty</th>
							<th>Issue Qt</th>
							<th>Balance</th>
		               </tr>
		            </thead>
		            <tbody>
		            	<?php
		            		foreach ($trims_arr as $row) {
		            			$receiveQty = $trims_receive_arr[$row['trim_group']]['receive_qty'];
		            			$issueQty = $trims_issue_arr[$row['trim_group']]['issue_qty'];
		            			?>
		            			<tr>
		            				<td><?php echo $trim_group_library[$row['trim_group']]; ?></td>
		            				<td><?php echo $trims_booking_arr[$row['trim_group']]['description']; ?></td>
		            				<td><?php echo $supplier_library[$trims_booking_arr[$row['trim_group']]['supplier_id']]; ?></td>
		        					<td><?php echo number_format($row['cons_dzn_gmts'],4); ?></td>
		            				<td><?php echo $unit_of_measurement[$row['cons_uom']]; ?></td>
		        					<td><?php echo number_format($row['tot_cons'],4); ?></td>
		        					<td><?php echo $trims_booking_arr[$row['trim_group']]['cons']; ?></td>
		        					<td><?php echo $receiveQty; ?></td>
		        					<td><?php echo $issueQty; ?></td>
		        					<td><?php echo ($receiveQty - $issueQty); ?></td>
		            			</tr>
		            			<?php
		            		}
		            	?>
		            </tbody>
		            <tfoot>
		            </tfoot>
		        </table>
		    </fieldset>
	    </div>
	</div>
	<?php

	$html = ob_get_contents();
	ob_clean();
	 
	foreach (glob($_SESSION['logic_erp']['user_id']."*.xls") as $filename) {
	@unlink($filename);
	}
	
	$name=time();
	$filename=$_SESSION['logic_erp']['user_id']."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,$html);
	echo "$html****$filename****2";
}



 



if($action=="show_image")
{
	echo load_html_head_contents("Set Entry","../../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1");
	?>
    <table>
    <tr>
    <?
    foreach ($data_array as $row)
	{ 
	?>
    <td><img src='../../../../<? echo $row[csf('image_location')]; ?>' height='250' width='300' /></td>
    <?
	}
	?>
    </tr>
    </table>
    
    <?
}

?>
