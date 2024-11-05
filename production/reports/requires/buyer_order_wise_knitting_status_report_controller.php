<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_REQUEST);
$permission=$_SESSION['page_permission'];

include('../../../includes/common.php');

$user_name = $_SESSION['logic_erp']["user_id"];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
$yarn_count_details=return_library_array( "select id,yarn_count from lib_yarn_count", "id", "yarn_count"  );
$supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 0, "-- All Buyer --", $selected, "" ,0); 
	exit();
}

if($action=="buyer_name_search_popup")
{
	echo load_html_head_contents("Buyer Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
     
	<script>
		
		var selected_id = new Array; var selected_name = new Array;
		
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click'); 
			}
		}
		
		function toggle( x, origColor ) 
		{
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
			
			$('#hide_buyer_id').val( id );
			$('#hide_buyer_name').val( name );
		}
	
    </script>

	<input type="hidden" id="hide_buyer_id" name="hide_buyer_id">
    <input type="hidden" id="hide_buyer_name" name="hide_buyer_name">
<? 
	$arr=array();  
	$sql="select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$companyID' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name";
	
	echo create_list_view("list_view","Buyer Name","300","370","280",0,$sql,"js_set_value","id,buyer_name",'',1,0,$arr,"buyer_name",'','setFilterGrid("list_view",-1);','0','',1);

	exit();	 
}

if($action=="order_no_search_popup")
{
	echo load_html_head_contents("Order No Info", "../../../", 1, 1,'','','');
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_order_no_search_list_view', 'search_div', 'buyer_order_wise_knitting_status_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
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
		
	$start_date =trim($data[4]);
	$end_date =trim($data[5]);	
	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and b.pub_shipment_date between '".change_date_format($start_date,"yyyy-mm-dd", "-")."' and '".change_date_format($end_date,"yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and b.pub_shipment_date between '".change_date_format($start_date,'','',1)."' and '".change_date_format($end_date,'','',1)."'";	
		}
	}
	else
	{
		$date_cond="";
	}
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year,"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year,";
	else $year_field="";//defined Later
	
	$arr=array(0=>$company_arr,1=>$buyer_arr);
		
	$sql= "select b.id, $year_field a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $date_cond order by b.id, b.pub_shipment_date";
		
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Po No, Shipment Date", "80,130,50,60,130,130","760","220",0, $sql , "js_set_value", "id,po_number", "", 1, "company_name,buyer_name,0,0,0,0,0", $arr , "company_name,buyer_name,year,job_no_prefix_num,style_ref_no,po_number,pub_shipment_date", "",'','0,0,0,0,0,0,3','',1) ;
	
   exit(); 
}


if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$company_name= str_replace("'","",$cbo_company_name);
	$cbo_search_by= str_replace("'","",$cbo_search_by);
	$txt_search_data= str_replace("'","",$txt_search_common);
	$type = str_replace("'","",$cbo_type);
	if(str_replace("'","",$hide_buyer_id)==0)
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
		$buyer_id_cond=" and a.buyer_name in (".str_replace("'","",$hide_buyer_id).")";
	}
	if($txt_search_data!='')
	{
		if($cbo_search_by==1) 
			$search_cond="and b.file_no='$txt_search_data'"; 
		else if($cbo_search_by==2) 
			$search_cond="and b.grouping='$txt_search_data'"; 	
		else 
			$search_cond="and a.style_ref_no='$txt_search_data'";
	}
		
	//echo $search_field;
	$txt_job_no=str_replace("'","",$txt_job_no);
	//if(trim($txt_job_no)!="") $job_no="%".trim($txt_job_no); else $job_no="%%";
	if(trim($txt_job_no)!="") $job_no=trim($txt_job_no); else $job_no="%%";
	
	$cbo_year=str_replace("'","",$cbo_year);
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0) 
		{
			$year_cond=" and YEAR(a.insert_date)=$cbo_year";
			$year_field=", YEAR(a.insert_date) as year ";
		}
		else if($db_type==2) 
		{
			$year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
			$year_field=", to_char(a.insert_date,'YYYY') as year ";
		}
		else 
		{
			$year_cond=""; $year_field="";
		}
	}
	else $year_cond="";
	
	if(str_replace("'","",trim($txt_order_no))=="")
	{
		$po_cond="";
	}
	else
	{
		if(str_replace("'","",$hide_order_id)!="")
		{
			$po_id=str_replace("'","",$hide_order_id);
			$po_cond="and b.id in(".$po_id.")";
		}
		else
		{
			$po_number=trim(str_replace("'","",$txt_order_no))."%";
			$po_cond="and b.po_number like '$po_number'";
		}
	}
	//echo $type;
	if($type==1)
	{
		$start_date=str_replace("'","",trim($txt_date_from));
		$end_date=str_replace("'","",trim($txt_date_to));
		
		$date_cond=""; $print_unplanned=1;
		if($start_date!="" && $end_date!="")
		{
			$date_cond="and b.start_date between '".$start_date."' and '".$end_date."'";
			$print_unplanned=0;
		}
		
		$plan_data_array=array(); $program_qnty_array=array();
		$sql_plan=sql_select("select a.booking_no, a.po_id, a.dia, a.yarn_desc as pre_cost_id, a.fabric_desc, a.gsm_weight, a.determination_id, a.width_dia_type, a.program_qnty, b.id as prog_id, b.knitting_source, b.knitting_party, b.color_id, b.machine_dia, b.machine_gg, b.fabric_dia, b.stitch_length, b.start_date, b.end_date, b.status from ppl_planning_entry_plan_dtls a, ppl_planning_info_entry_dtls b where a.dtls_id=b.id and a.company_id=$company_name and b.program_qnty>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond order by b.knitting_source DESC");
		foreach($sql_plan as $planRow)
		{
			$plan_data_array[$planRow[csf('po_id')]].=$planRow[csf('booking_no')]."__".$planRow[csf('dia')]."__".$planRow[csf('pre_cost_id')]."__".$planRow[csf('fabric_desc')]."__".$planRow[csf('gsm_weight')]."__".$planRow[csf('program_qnty')]."__".$planRow[csf('prog_id')]."__".$planRow[csf('knitting_source')]."__".$planRow[csf('knitting_party')]."__".$planRow[csf('color_id')]."__".$planRow[csf('machine_dia')]."__".$planRow[csf('machine_gg')]."__".$planRow[csf('fabric_dia')]."__".$planRow[csf('stitch_length')]."__".$planRow[csf('start_date')]."__".$planRow[csf('end_date')]."__".$planRow[csf('status')]."__".$planRow[csf('determination_id')]."__".$planRow[csf('width_dia_type')]."**";
			
			$program_qnty_array[$planRow[csf('booking_no')]][$planRow[csf('po_id')]][$planRow[csf('determination_id')]][$planRow[csf('gsm_weight')]][$planRow[csf('dia')]][$planRow[csf('width_dia_type')]]+=$planRow[csf('program_qnty')];
		}
	
		$pre_cost_array=array();
		if($db_type==0)
		{
			$costing_sql=sql_select("select id,body_part_id,gsm_weight,concat_ws(', ',construction,composition) as fab_desc,lib_yarn_count_deter_id from wo_pre_cost_fabric_cost_dtls");
		}
		else
		{
			$costing_sql=sql_select("select id,body_part_id,gsm_weight, construction || ', ' || composition as fab_desc,lib_yarn_count_deter_id from wo_pre_cost_fabric_cost_dtls");	
		}
		foreach($costing_sql as $row)
		{
			$costing_per_id_library[$row[csf('id')]]['body_part']=$row[csf('body_part_id')];
			$costing_per_id_library[$row[csf('id')]]['gsm']=$row[csf('gsm_weight')]; 
			$costing_per_id_library[$row[csf('id')]]['desc']=$row[csf('fab_desc')]; 
			$costing_per_id_library[$row[csf('id')]]['determination_id']=$row[csf('lib_yarn_count_deter_id')]; 
		}
		
		$reqsDataArr=array();
		if($db_type==0)
		{
			$reqsDataArr=return_library_array( "select knit_id, group_concat(distinct(prod_id)) as prod_id from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0 group by knit_id", "knit_id", "prod_id");
		}
		else
		{
			$reqsDataArr=return_library_array( "select knit_id, LISTAGG(prod_id, ',') WITHIN GROUP (ORDER BY prod_id) as prod_id from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0 group by knit_id", "knit_id", "prod_id");	
		}
		
		$yarn_desc_array=array();
		$sql="select id, lot, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type from product_details_master where item_category_id=1";
		$result = sql_select($sql);
		foreach($result as $row)
		{
			$compostion='';
			if($row[csf('yarn_comp_percent2nd')]!=0)
			{
				$compostion=$composition[$row[csf('yarn_comp_type1st')]]." ".$row[csf('yarn_comp_percent1st')]."%"." ".$composition[$row[csf('yarn_comp_type2nd')]]." ".$row[csf('yarn_comp_percent2nd')]."%";
			}
			else
			{
				$compostion=$composition[$row[csf('yarn_comp_type1st')]]." ".$row[csf('yarn_comp_percent1st')]."%"." ".$composition[$row[csf('yarn_comp_type2nd')]];
			}
	
			$yarn_desc=$yarn_count_details[$row[csf('yarn_count_id')]]." ".$compostion." ".$yarn_type[$row[csf('yarn_type')]];//$row[csf('lot')]." ".
			$yarn_desc_array[$row[csf('id')]]=$yarn_desc;
		}

		$bookingDataArr=array();
		$sql_wo=sql_select("select a.booking_no, b.pre_cost_fabric_cost_dtls_id, b.po_break_down_id, b.dia_width, sum(b.grey_fab_qnty) as qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_name and a.item_category=2 and a.fabric_source=1 and b.grey_fab_qnty>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.booking_no, b.po_break_down_id, b.pre_cost_fabric_cost_dtls_id, b.dia_width order by b.dia_width");
		foreach($sql_wo as $woRow)
		{
			$bookingDataArr[$woRow[csf('po_break_down_id')]].=$woRow[csf('booking_no')]."**".$woRow[csf('pre_cost_fabric_cost_dtls_id')]."**".$woRow[csf('dia_width')]."**".$woRow[csf('qnty')]."__";
		}
		
		$prodDataArr=array();
		$sql_prod=sql_select("select c.po_breakdown_id, a.booking_id, sum(c.quantity) as knitting_qnty from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and c.entry_form=2 and a.item_category=13 and a.entry_form=2 and a.receive_basis=2 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.po_breakdown_id, a.booking_id");
		foreach($sql_prod as $prodRow)
		{
			$prodDataArr[$prodRow[csf('po_breakdown_id')]][$prodRow[csf('booking_id')]]=$prodRow[csf('knitting_qnty')];
		}
	
		$sql="select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name,a.style_ref_no, b.id as po_id, b.po_number,b.file_no,b.grouping $year_field from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls d where a.job_no=b.job_no_mst and b.id=d.po_break_down_id and a.company_name=$company_name and a.job_no_prefix_num like '$job_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.grey_fab_qnty>0 $buyer_id_cond $po_cond $year_cond $search_cond group by b.id,b.file_no,b.grouping, a.job_no,a.style_ref_no, a.job_no_prefix_num, a.company_name, a.buyer_name, b.po_number, a.insert_date order by a.buyer_name, a.job_no, b.id";
		//echo $sql;//die;a.job_no like 
		ob_start();
		?>
		<fieldset style="width:1995px;">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1968" class="rpt_table" >
				<thead>
					<th width="40">SL</th>
                    <th width="70">File No</th>
                    <th width="70">Ref. No</th>
                    <th width="70">Style Ref.</th>
                    <th width="100">Booking No</th>
                    
					<th width="60">Job No</th>
					<th width="50">Year</th>
					<th width="110">Order No</th>
					<th width="120">Knitting Company</th>
					<th width="70">Program No</th>
					<th width="80">Start Date</th>
					<th width="80">End Date</th>
					<th width="180">Fabric Description</th>
					<th width="200">Yarn Description</th>
					<th width="80">Color</th>
					<th width="70">Stich Length</th>
					<th width="60">M/C Dia</th>
					<th width="60">F. Dia</th>
					<th width="70">GSM</th>
					<th width="100">Req. Qty</th>
					<th width="100">Prod. Qty</th>
					<th>Balance</th>
				</thead>
			</table>
			<div style="width:1990px; overflow-y:scroll; max-height:450px;" id="scroll_body">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1968" class="rpt_table" id="tbl_list_search">
					<? 
						$i=1; $buyer_array=array(); $po_array=array();
						$nameArray=sql_select( $sql );
						foreach ($nameArray as $row)
						{
							if(!in_array($row[csf('buyer_name')], $buyer_array))
							{
								if($z>1)
								{
								?>
									<tr bgcolor="#CCCCCC">
										<td colspan="19" align="right"><b>Order Total</b></td>
										<td align="right"><b><? echo number_format($order_req_qnty,2,'.',''); ?></b></td>
										<td align="right"><b><? echo number_format($order_prod_qnty,2,'.',''); ?></b></td>
										<td align="right"><b><? echo number_format($order_balance,2,'.',''); ?></b></td>
									</tr>
									<tr bgcolor="#CCCCCC">
										<td colspan="19" align="right"><b>Buyer Total</b></td>
										<td align="right"><b><? echo number_format($buyer_req_qnty,2,'.',''); ?></b></td>
										<td align="right"><b><? echo number_format($buyer_prod_qnty,2,'.',''); ?></b></td>
										<td align="right"><b><? echo number_format($buyer_balance,2,'.',''); ?></b></td>
									</tr>
								<?
									$buyer_req_qnty = 0;
									$buyer_prod_qnty = 0;
									$buyer_balance = 0;
									
									$order_req_qnty = 0;
									$order_prod_qnty = 0;
									$order_balance = 0;
								}
							?>
								<tr bgcolor="#EFEFEF">
									<td colspan="22">
										<b>Buyer Name:- <?php echo $buyer_arr[$row[csf('buyer_name')]]; ?></b>
									</td>
								</tr>
							<?
								$buyer_array[]=$row[csf('buyer_name')];
							}
							else
							{
								if(!in_array($row[csf('po_id')], $po_array))
								{
									if($z>1)
									{
									?>
										<tr bgcolor="#CCCCCC">
											<td colspan="19" align="right"><b>Order Total</b></td>
											<td align="right"><b><? echo number_format($order_req_qnty,2,'.',''); ?></b></td>
											<td align="right"><b><? echo number_format($order_prod_qnty,2,'.',''); ?></b></td>
											<td align="right"><b><? echo number_format($order_balance,2,'.',''); ?></b></td>
										</tr>
									<?
										$order_req_qnty = 0;
										$order_prod_qnty = 0;
										$order_balance = 0;
									}
									$po_array[]=$row[csf('po_id')];
								}
							}
							
							$planData=array_filter(explode("**",substr($plan_data_array[$row[csf('po_id')]],0,-2)));
							$plannedArray=array(); $printArray=array(); $z=1;
							if(count($planData)>0)
							{
								foreach($planData as $planRow)
								{
									$planRow=explode("__",$planRow);
									$booking_no=$planRow[0];
									$dia=$planRow[1];
									$pre_cost_id=$planRow[2];
									$fabric_desc=$planRow[3];
									$gsm_weight=$planRow[4];
									$program_qnty=$planRow[5];
									$prog_id=$planRow[6];
									$knitting_source=$planRow[7];
									$knitting_party=$planRow[8];
									$color_id=$planRow[9];
									$machine_dia=$planRow[10];
									$machine_gg=$planRow[11];
									$fabric_dia=$planRow[12];
									$stitch_length=$planRow[13];
									$start_date=$planRow[14];
									$end_date=$planRow[15];
									$status=$planRow[16];
									$determination_id=$planRow[17];
									$width_dia_type=$planRow[18];
									
									$plannedArray[$booking_no][$row[csf('po_id')]][$pre_cost_id][$dia]=$program_qnty;
									//$program_qnty=$program_qnty_array[$booking_no][$row[csf('po_id')]][$determination_id][$gsm_weight][$dia][$width_dia_type]; //old
									
									//if($status!=4) //old remove this condition issue id: 3347
									//{
										//if($printArray[$booking_no][$row[csf('po_id')]][$determination_id][$gsm_weight][$dia][$width_dia_type]=="") // old
										if($printArray[$booking_no][$row[csf('po_id')]][$determination_id][$gsm_weight][$dia][$width_dia_type][$prog_id]=="")
										{
											//$printArray[$booking_no][$row[csf('po_id')]][$determination_id][$gsm_weight][$dia][$width_dia_type]=$program_qnty; //old
											$printArray[$booking_no][$row[csf('po_id')]][$determination_id][$gsm_weight][$dia][$width_dia_type][$prog_id]=$program_qnty;
											if($z==1) 
											{
												$display_font_color="";
												$font_end="";
											}
											else 
											{
												$display_font_color="<font style='display:none' color='$bgcolor'>";
												$font_end="</font>";
											}
											
											if($knitting_source==1)
												$knit_party=$company_arr[$knitting_party]; 
											else if($knitting_source==3)
												$knit_party=$supllier_arr[$knitting_party];
											else
												$knit_party="Without Source";
										
											$prog_no=$prog_id;
											$booking_no=$booking_no;
											$prod_qnty=$prodDataArr[$row[csf('po_id')]][$prog_id];
											
											$balance_qnty=$program_qnty-$prod_qnty;
				
											$yarn_desc='';
											$prod_id=array_unique(explode(",",$reqsDataArr[$prog_id]));
											foreach($prod_id as $value)
											{
												if($yarn_desc=='') $yarn_desc=$yarn_desc_array[$value]; else $yarn_desc.=",<br>".$yarn_desc_array[$value];
											}
											
											$color_name='';
											$color_id=array_unique(explode(",",$color_id));
											foreach($color_id as $value)
											{
												if($color_name=='') $color_name=$color_library[$value]; else $color_name.=",".$color_library[$value];
											}
											
											if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
											
											?>
											<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
												<td width="40"><? echo $display_font_color.$i.$font_end; ?>&nbsp;</td>
                                                <td width="70">&nbsp;&nbsp;<? echo $display_font_color.$row[csf('file_no')].$font_end; ?></td>
                                                <td width="70">&nbsp;&nbsp;<? echo $display_font_color.$row[csf('grouping')].$font_end; ?></td>
                                                <td width="70"><div style="word-break:break-all"><? echo $display_font_color.$row[csf('style_ref_no')].$font_end; ?></div></td>
                                                <td width="100">&nbsp;&nbsp;<? echo $display_font_color.$booking_no.$font_end; ?></td>
                                                
												<td width="60">&nbsp;&nbsp;<? echo $display_font_color.$row[csf('job_no_prefix_num')].$font_end; ?></td>
												<td width="50" align="center"><? echo $display_font_color.$row[csf('year')].$font_end; ?>&nbsp;</td>
												<td width="110"><p><? echo $display_font_color.$row[csf('po_number')].$font_end; ?>&nbsp;</p></td>
												<td width="120"><p><? echo $knit_party; ?></p></td>
												<td width="70"><p>&nbsp;&nbsp;<? echo $prog_no; ?></p></td>
												<td width="80" align="center">&nbsp;<? echo change_date_format($start_date); ?></td>
												<td width="80" align="center">&nbsp;<? echo change_date_format($end_date); ?></td>
												<td width="180"><p><? echo $fabric_desc; ?></p></td>
												<td width="200"><p><? echo $yarn_desc; ?>&nbsp;</p></td>
												<td width="80"><p><? echo $color_name; ?>&nbsp;</p></td>
												<td width="70"><p><? echo $stitch_length; ?>&nbsp;</p></td>
												<td width="60"><p><? echo $machine_dia."X".$machine_gg; ?>&nbsp;</p></td>
												<td width="60"><p><? echo $fabric_dia; ?>&nbsp;</p></td>
												<td width="70"><p><? echo $gsm_weight; ?>&nbsp;</p></td>
												<td align="right" width="100"><? echo number_format($program_qnty,2,'.',''); ?></td>
												<td align="right" width="100"><? echo number_format($prod_qnty,2,'.',''); ?></td>
												<td align="right"><? echo number_format($balance_qnty,2,'.',''); ?></td>
											</tr>
											<?
											
											$total_req_qnty+=$program_qnty;
											$total_prod_qnty+=$prod_qnty;
											$total_balance+=$balance_qnty;
											
											$buyer_req_qnty+=$program_qnty;
											$buyer_prod_qnty+=$prod_qnty;
											$buyer_balance+=$balance_qnty;
											
											$order_req_qnty+=$program_qnty;
											$order_prod_qnty+=$prod_qnty;
											$order_balance+=$balance_qnty;
				
											$i++;
											$z++;
										}
									//} // old
								}
							}
							
							if($print_unplanned==1)
							{
								$bookingData=array_filter(explode("__",substr($bookingDataArr[$row[csf('po_id')]],0,-2)));
								if(count($bookingData)>0)
								{
									foreach($bookingData as $woRow)
									{
										$woRow=explode("**",$woRow);
										$booking_no=$woRow[0];
										$pre_cost_id=$woRow[1];
										$dia_width=$woRow[2];
										$req_qnty=$woRow[3];
										$program_qnty=$plannedArray[$booking_no][$row[csf('po_id')]][$pre_cost_id][$dia_width];
										
										if($program_qnty=="")
										{
											if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
											
											if($z==1) 
											{
												$display_font_color="";
												$font_end="";
											}
											else 
											{
												$display_font_color="<font style='display:none' color='$bgcolor'>";
												$font_end="</font>";
											}
											
											$knit_party="Unplanned";
											$fabric_desc=$costing_per_id_library[$pre_cost_id]['desc'];
											$gsm_weight=$costing_per_id_library[$pre_cost_id]['gsm'];
											$prog_no="&nbsp;";
											$prod_qnty=0;
											$balance_qnty=$req_qnty;
											
											?>
											<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
												<td width="40"><? echo $display_font_color.$i.$font_end; ?>&nbsp;</td>
												<td width="70">&nbsp;&nbsp;<? echo $display_font_color.$row[csf('file_no')].$font_end; ?></td>
                                                <td width="70">&nbsp;&nbsp;<? echo $display_font_color.$row[csf('grouping')].$font_end; ?></td>
                                                <td width="70"><div style="word-break:break-all"><? echo $display_font_color.$row[csf('style_ref_no')].$font_end; ?></div></td>
                                                <td width="100">&nbsp;&nbsp;<? echo $display_font_color.$booking_no.$font_end; ?></td>
                                                
                                                <td width="60">&nbsp;&nbsp;<? echo $display_font_color.$row[csf('job_no_prefix_num')].$font_end; ?></td>
												<td width="50" align="center"><? echo $display_font_color.$row[csf('year')].$font_end; ?>&nbsp;</td>
												<td width="110"><p><? echo $display_font_color.$row[csf('po_number')].$font_end; ?>&nbsp;</p></td>
												<td width="120"><p><? echo $knit_party; ?></p></td>
												<td width="70">&nbsp;</td>
												<td width="80" align="center">&nbsp;</td>
												<td width="80" align="center">&nbsp;</td>
												<td width="180"><p><? echo $fabric_desc; ?></p></td>
												<td width="200">&nbsp;</td>
												<td width="80">&nbsp;</td>
												<td width="70">&nbsp;</td>
												<td width="60">&nbsp;</td>
												<td width="60"><p><? echo $dia_width; ?>&nbsp;</p></td>
												<td width="70"><p><? echo $gsm_weight; ?>&nbsp;</p></td>
												<td align="right" width="100"><? echo number_format($req_qnty,2,'.',''); ?></td>
												<td align="right" width="100"><? echo number_format($prod_qnty,2,'.',''); ?></td>
												<td align="right"><? echo number_format($balance_qnty,2,'.',''); ?></td>
											</tr>
											<?
											
											$total_req_qnty+=$req_qnty;
											$total_prod_qnty+=$prod_qnty;
											$total_balance+=$balance_qnty;
											
											$buyer_req_qnty+=$req_qnty;
											$buyer_prod_qnty+=$prod_qnty;
											$buyer_balance+=$balance_qnty;
											
											$order_req_qnty+=$req_qnty;
											$order_prod_qnty+=$prod_qnty;
											$order_balance+=$balance_qnty;
			
											$i++;
											$z++;
										}
									}
								}
							}
						}
						
						if($z>1)
						{
						?>
							<tr bgcolor="#CCCCCC">
								<td colspan="19" align="right"><b>Order Total</b></td>
								<td align="right"><b><? echo number_format($order_req_qnty,2,'.',''); ?></b></td>
								<td align="right"><b><? echo number_format($order_prod_qnty,2,'.',''); ?></b></td>
								<td align="right"><b><? echo number_format($order_balance,2,'.',''); ?></b></td>
							</tr>
							<tr bgcolor="#CCCCCC" id="tr_<? echo $i; ?>">
								<td colspan="19" align="right"><b>Buyer Total</b></td>
								<td align="right"><b><? echo number_format($buyer_req_qnty,2,'.',''); ?></b></td>
								<td align="right"><b><? echo number_format($buyer_prod_qnty,2,'.',''); ?></b></td>
								<td align="right"><b><? echo number_format($buyer_balance,2,'.',''); ?></b></td>
							</tr>
						<?
						}
					?>
					<tfoot>
						<th colspan="19" align="right">Grand Total</th>
						<th align="right"><? echo number_format($total_req_qnty,2,'.',''); ?></th>
						<th align="right"><? echo number_format($total_prod_qnty,2,'.',''); ?></th>
						<th align="right"><? echo number_format($total_balance,2,'.',''); ?></th>
					</tfoot>
				</table>
			</div>
		</fieldset>
<?
	}
	else
	{
		$planDateArr=array();
		if($db_type==0)
		{
			$planData=sql_select("select b.po_id, min(case when a.start_date!='0000-00-00' then a.start_date end) as plan_start_date, max(a.end_date) as plan_end_date from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b where a.id=b.dtls_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by b.po_id");
		}
		else
		{
			$planData=sql_select("select b.po_id, min(case when a.start_date is not null then a.start_date end) as plan_start_date, max(a.end_date) as plan_end_date from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b where a.id=b.dtls_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by b.po_id");	
		}
		foreach($planData as $planRow)
		{
			$planDateArr[$planRow[csf('po_id')]]['start']=$planRow[csf('plan_start_date')];
			$planDateArr[$planRow[csf('po_id')]]['end']=$planRow[csf('plan_end_date')];
		}
		
		$trans_qnty_arr=array(); $grey_prod_qnty_arr=array(); $actualDateArr=array();
		$dataArrayTrans=sql_select("select po_breakdown_id, 
								sum(CASE WHEN entry_form ='2' THEN quantity ELSE 0 END) AS grey_receive,
								sum(CASE WHEN entry_form in(83,13) and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_knit,
								sum(CASE WHEN entry_form in(83,13) and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_knit
								from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form in(2,13,83) group by po_breakdown_id");
								
								
								

		/*$dataArrayTrans=sql_select("select c.po_breakdown_id, 
								sum(CASE WHEN c.entry_form ='2' THEN c.quantity ELSE 0 END) AS grey_receive,
								sum(CASE WHEN c.entry_form in(16,61) and a.knit_dye_source<>3 THEN c.quantity ELSE 0 END) AS transfer_in_qnty_knit,
								sum(CASE WHEN c.entry_form in(16,61) and a.knit_dye_source=3 THEN c.quantity ELSE 0 END) AS transfer_out_qnty_knit
								from inv_issue_master a, inv_grey_fabric_issue_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(16,61) and c.entry_form in(16,61) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.po_breakdown_id");					
					*/			
								
		foreach($dataArrayTrans as $row)
		{
			$trans_qnty_arr[$row[csf('po_breakdown_id')]]=$row[csf('transfer_in_qnty_knit')]-$row[csf('transfer_out_qnty_knit')];
			$grey_prod_qnty_arr[$row[csf('po_breakdown_id')]]=$row[csf('grey_receive')];
		}
		
		
		
		
		$sql_grey_prod="select min(a.receive_date) as prod_start_date, max(a.receive_date) as prod_end_date, c.po_breakdown_id from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2,22) and c.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.po_breakdown_id";
		$dataArrayGreyProd=sql_select($sql_grey_prod);
		foreach($dataArrayGreyProd as $greyRow)
		{
			$actualDateArr[$greyRow[csf('po_breakdown_id')]]['prod']['start']=date("Y-m-d",strtotime($greyRow[csf('prod_start_date')]));
			$actualDateArr[$greyRow[csf('po_breakdown_id')]]['prod']['end']=date("Y-m-d",strtotime($greyRow[csf('prod_end_date')]));
		}
		
		$sql_grey_purchase="select c.po_breakdown_id, sum(c.quantity) as grey_purchase_qnty from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.receive_basis<>9 and a.entry_form=22 and c.entry_form=22 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.po_breakdown_id";
		$dataArrayGreyPurchase=sql_select($sql_grey_purchase);
		foreach($dataArrayGreyPurchase as $greyRow)
		{
			$greyPurchaseQntyArray[$greyRow[csf('po_breakdown_id')]]=$greyRow[csf('grey_purchase_qnty')];
		}

		$transData=sql_select("select c.po_breakdown_id, min(a.transfer_date) as actual_trans_start_date, max(a.transfer_date) as actual_trans_end_date from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.item_category=13 and a.transfer_criteria=4 and c.trans_type=5 and c.entry_form=13 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by c.po_breakdown_id");
		foreach($transData as $transRow)
		{
			$actualDateArr[$transRow[csf('po_breakdown_id')]]['trans']['start']=date("Y-m-d",strtotime($transRow[csf('actual_trans_start_date')]));
			$actualDateArr[$transRow[csf('po_breakdown_id')]]['trans']['end']=date("Y-m-d",strtotime($transRow[csf('actual_trans_end_date')]));
		}
		
		$booking_qnty_arr=return_library_array( "select b.po_break_down_id, sum(b.grey_fab_qnty) as qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id", "po_break_down_id", "qnty"  );
		
		$yarn_iss_arr=array();
		$yarn_iss_data=sql_select("select b.po_breakdown_id, a.transaction_date, sum(b.quantity) as iss_qnty from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.item_category=1 and a.transaction_type=2 and b.entry_form=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, a.transaction_date");
		foreach($yarn_iss_data as $row)
		{
			$trans_date=date("Y-m-d",strtotime($row[csf('transaction_date')]));
			$yarn_iss_arr[$row[csf('po_breakdown_id')]][$trans_date]=$row[csf('iss_qnty')];
		}
		//print_r($yarn_iss_arr[4056]);
		
		$prod_arr=array();
		$prodData=sql_select("select c.po_breakdown_id, a.receive_date, sum(c.quantity) as prod_qnty from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2,22) and c.entry_form in(2,22) and a.receive_basis<>9 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.po_breakdown_id, a.receive_date");
		foreach($prodData as $row)
		{
			$trans_date=date("Y-m-d",strtotime($row[csf('receive_date')]));
			$prod_arr[$row[csf('po_breakdown_id')]][$trans_date]=$row[csf('prod_qnty')];
		}
		
		$prod_trans_arr=array();
		$transData=sql_select("select c.po_breakdown_id, a.transfer_date, sum(case when c.trans_type=5 then c.quantity else 0 end) as in_qnty, sum(case when c.trans_type=6 then c.quantity else 0 end) as out_qnty from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.item_category=13 and a.transfer_criteria=4 and c.entry_form=13 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by c.po_breakdown_id, a.transfer_date");
		foreach($transData as $row)
		{
			$trans_date=date("Y-m-d",strtotime($row[csf('transfer_date')]));
			$prod_trans_arr[$row[csf('po_breakdown_id')]][$trans_date]=$row[csf('in_qnty')]-$row[csf('out_qnty')];
		}
		
		$start_date=str_replace("'","",trim($txt_date_from));
		$end_date=str_replace("'","",trim($txt_date_to));
		
		$today=date("Y-m-d");
		
		if($start_date!="" && $end_date!="")
		{
			//$booking_qnty_arr=return_library_array("select b.po_break_down_id as po_id, sum(b.grey_fab_qnty) as qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id",'po_id','qnty');
			$booking_qnty_arr=array();
			$bk_sql=sql_select("select b.po_break_down_id as po_id,a.booking_no, sum(b.grey_fab_qnty) as qnty from  wo_booking_mst a, wo_booking_dtls b  where a.booking_no=b.booking_no and a.item_category in(2,13) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id, a.booking_no ");
			foreach($bk_sql as $row)
			{
				$booking_qnty_arr[$row[csf('po_id')]]['qnty']=$row[csf('qnty')];
				$booking_qnty_arr[$row[csf('po_id')]]['booking_no']=$row[csf('booking_no')];
			}
			
			$tna_search=1;
			$sql="select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name,a.style_ref_no,b.file_no,b.grouping, b.id as po_id, b.po_number, b.pub_shipment_date, t.task_start_date, t.task_finish_date $year_field from wo_po_details_master a, wo_po_break_down b, tna_process_mst t where a.job_no=b.job_no_mst and b.id=t.po_number_id and a.company_name=$company_name and a.job_no_prefix_num like '$job_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and t.is_deleted=0 and t.status_active=1 and t.task_number=60 and t.task_start_date between '$start_date' and '$end_date' $buyer_id_cond $po_cond $search_cond  $year_cond group by b.id, b.po_number, b.pub_shipment_date,b.file_no,b.grouping,a.style_ref_no, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.insert_date, t.task_start_date, t.task_finish_date order by a.buyer_name, a.job_no";
		}
		else
		{
			$tna_array=array();
			$tna_sql=sql_select("select po_number_id, task_start_date, task_finish_date from tna_process_mst where task_number=60 and is_deleted=0 and status_active=1");
			foreach($tna_sql as $row)
			{
				$tna_array[$row[csf('po_number_id')]]['start_d']=$row[csf('task_start_date')];
				$tna_array[$row[csf('po_number_id')]]['finish_d']=$row[csf('task_finish_date')];
			}
			$tna_search=0;
			$sql="select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name,a.style_ref_no,b.file_no,b.grouping, b.id as po_id, b.po_number, b.pub_shipment_date, sum(d.grey_fab_qnty) as qnty $year_field from wo_po_details_master a, wo_po_break_down b, wo_booking_mst c, wo_booking_dtls d where a.job_no=b.job_no_mst and b.id=d.po_break_down_id and a.job_no=c.job_no and c.booking_no=d.booking_no and a.company_name=$company_name and c.item_category in(2,13) and a.job_no_prefix_num like '$job_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.grey_fab_qnty>0 $buyer_id_cond $po_cond $search_cond $year_cond group by b.id, b.po_number, b.pub_shipment_date,b.file_no,b.grouping,a.style_ref_no,a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.insert_date order by a.buyer_name, a.job_no";
		}
		//echo $sql;//die;
		ob_start();
		?>
		<div style="width:100%; margin-top:5px;" align="center">
			<fieldset style="width:1740px;">
            	<table width="1330" cellpadding="0" cellspacing="0"> 
                    <tr class="form_caption">
                        <td colspan="18" align="center"><strong><? echo $report_title; ?></strong></td> 
                    </tr>
                </table>
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1720" class="rpt_table" align="left">
					<thead>
                    	<tr>
                            <th width="40" rowspan="2">SL</th>
                            <th width="70" rowspan="2">File No</th>
                            <th width="70" rowspan="2">Ref. No</th>
                            <th width="70" rowspan="2">Style Ref.</th>
                            <th width="100" rowspan="2">Booking No</th>
                            <th width="55" rowspan="2">Job No</th>
                            <th width="40" rowspan="2">Year</th>
                            <th width="100" rowspan="2">Order No</th>
                            <th width="70" rowspan="2">Shipment Date</th>
                            <th width="180" colspan="2">TNA</th>
                            <th width="180" colspan="2">Plan</th>
                            <th width="180" colspan="2">Actual</th>
                            <th width="90" rowspan="2">Required Qty<br/><font style="font-size:9px; font-weight:100">(As Per Booking)</font></th>
                            <th width="80" rowspan="2">Grey Production</th>
                            <th width="50" rowspan="2">Prod. %</th>
                            <th width="80" rowspan="2">Grey Receive/ Purchase</th>
                            <th width="80" rowspan="2">Net Transfer</th>
                            <th width="90" rowspan="2">Grey Available</th>
                            <th rowspan="2">Balance</th>
                        </tr>
                        <tr>
                        	<th width="80">Start Date</th>
                            <th width="100">End Date</th>
                            <th width="80">Start Date</th>
                            <th width="100">End Date</th>
                            <th width="80">Start Date</th>
                            <th width="100">End Date</th>
                        </tr>
					</thead>
				</table>
				<div style="width:1740px; overflow-y:scroll; max-height:450px;" id="scroll_body" align="left">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1720" class="rpt_table" id="tbl_list_search" align="left">
						<? 
							$i=1; $buyer_array=array(); $po_array=array();
							$nameArray=sql_select( $sql );
							foreach ($nameArray as $row)
							{
								$tna_start_date=''; $tna_end_date='';
								if($tna_search==0)
								{
									if($tna_array[$row[csf('po_id')]]['start_d']!="0000-00-00" && $tna_array[$row[csf('po_id')]]['start_d']!="")
									{
										$tna_start_date=change_date_format($tna_array[$row[csf('po_id')]]['start_d']);
									}
									
									if($tna_array[$row[csf('po_id')]]['finish_d']!="0000-00-00" && $tna_array[$row[csf('po_id')]]['finish_d']!="")
									{
										$tna_end_date=change_date_format($tna_array[$row[csf('po_id')]]['finish_d']);
									}
									$req_qnty=$row[csf('qnty')];
								}
								else
								{
									if($row[csf('task_start_date')]!="0000-00-00" && $row[csf('task_start_date')]!="")
									{
										$tna_start_date=change_date_format($row[csf('task_start_date')]);
									}
									
									if($row[csf('task_finish_date')]!="0000-00-00" && $row[csf('task_finish_date')]!="")
									{
										$tna_end_date=change_date_format($row[csf('task_finish_date')]);
									}
									
									$req_qnty=$booking_qnty_arr[$row[csf('po_id')]]['qnty'];//$booking_qnty_arr[$row[csf('po_id')]];
								}
								
								if($req_qnty>0)
								{
									if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									
									if(!in_array($row[csf('buyer_name')], $buyer_array))
									{
										if($i!=1)
										{
										?>
											<tr bgcolor="#CCCCCC">
												<td colspan="15" align="right"><b>Buyer Total</b></td>
												<td align="right"><b><? echo number_format($buyer_req_qnty,2,'.',''); ?></b></td>
												<td align="right"><b><? echo number_format($buyer_prod_qnty,2,'.',''); ?></b></td>
                                                <td align="right">&nbsp;<b><? //echo number_format($tot_prod_percent,2,'.',''); ?></b></td>
												<td align="right"><b><? echo number_format($buyer_recv_qnty,2,'.',''); ?></b></td>
												<td align="right"><b><? echo number_format($buyer_net_trans_qnty,2,'.',''); ?></b></td>
												<td align="right"><b><? echo number_format($buyer_available_qnty,2,'.',''); ?></b></td>
												<td align="right"><b><? echo number_format($buyer_balance,2,'.',''); ?></b></td>
											</tr>
										<?
											$buyer_req_qnty = 0;
											$buyer_prod_qnty = 0;
											$buyer_recv_qnty = 0;
											$buyer_net_trans_qnty = 0;
											$buyer_available_qnty = 0;
											$buyer_balance = 0;
										}
									?>
										<tr bgcolor="#EFEFEF">
											<td colspan="22">
												<b>Buyer Name:- <?php echo $buyer_arr[$row[csf('buyer_name')]]; ?></b>
											</td>
										</tr>
									<?
										$buyer_array[]=$row[csf('buyer_name')];
									}
									
									$prod_qnty=$grey_prod_qnty_arr[$row[csf('po_id')]];
									$recv_qnty=$greyPurchaseQntyArray[$row[csf('po_id')]];
									$net_trans_qnty=$trans_qnty_arr[$row[csf('po_id')]];
									$available_qnty=$prod_qnty+$recv_qnty+$net_trans_qnty;
									$balance_qnty=$req_qnty-$available_qnty;
									$tot_prod_percent=$prod_qnty/$req_qnty*100;
									
									$plan_start_date=''; $plan_end_date='';
									if($planDateArr[$row[csf('po_id')]]['start']!="0000-00-00" && $planDateArr[$row[csf('po_id')]]['start']!="")
									{
										$plan_start_date=change_date_format($planDateArr[$row[csf('po_id')]]['start']);
									}
									
									if($planDateArr[$row[csf('po_id')]]['end']!="0000-00-00" && $planDateArr[$row[csf('po_id')]]['end']!="")
									{
										$plan_end_date=change_date_format($planDateArr[$row[csf('po_id')]]['end']);
									}
									
									$actual_start_date=''; $actual_end_date='';
									
									$prod_start_date=$actualDateArr[$row[csf('po_id')]]['prod']['start'];
									$prod_end_date=$actualDateArr[$row[csf('po_id')]]['prod']['end'];
									
									$trans_start_date=$actualDateArr[$row[csf('po_id')]]['trans']['start'];
									$trans_end_date=$actualDateArr[$row[csf('po_id')]]['trans']['end'];
									
									$actual_start_date=$prod_start_date;
									if($trans_start_date<$actual_start_date && $trans_start_date!="") $actual_start_date=$trans_start_date;
									
									if($prod_end_date>$trans_end_date) $actual_end_date=$prod_end_date; else $actual_end_date=$trans_end_date;
									
									if($actual_start_date!="") $actual_start_date=change_date_format($actual_start_date);
									if($actual_end_date!="") $actual_end_date=change_date_format($actual_end_date);

									$pub_shipment_date=change_date_format($row[csf('pub_shipment_date')]);
									
									$shipment_date=change_date_format($row[csf('pub_shipment_date')], "yyyy-mm-dd", "-");
									$yet_to_ship_days=datediff( d, $today, $shipment_date);
									
									$booking_qnty=$booking_qnty_arr[$row[csf('po_id')]]['qnty'];//$booking_qnty_arr[$row[csf('po_id')]];
									$booking_no=$booking_qnty_arr[$row[csf('po_id')]]['booking_no'];
									//echo $booking_no.'='.$booking_qnty;
									$dateDiff_trans=datediff( d, $tna_start_date, $tna_end_date);
									
									$tna_wise_iss_qnty=$yarn_iss_arr[$row[csf('po_id')]][$tna_start_date];
									
									$tna_wise_prod_qnty=$prod_arr[$row[csf('po_id')]][$tna_start_date];
									$tna_wise_prod_trans_qnty=$prod_trans_arr[$row[csf('po_id')]][$tna_start_date];
									$tna_wise_grey_availlable_qnty=$tna_wise_prod_qnty+$tna_wise_prod_trans_qnty;
									
									if($dateDiff_trans>0)
									{
										for($k=0;$k<$dateDiff_trans;$k++)
										{
											$newdate=add_date($tna_start_date,$k);
											$tna_wise_iss_qnty+=$yarn_iss_arr[$row[csf('po_id')]][$newdate];
											$tna_wise_grey_availlable_qnty+=$prod_arr[$row[csf('po_id')]][$newdate]+$prod_trans_arr[$row[csf('po_id')]][$newdate];
										}
									}
									$tna_wise_iss_perc=number_format(($tna_wise_iss_qnty/$booking_qnty)*100,2);
									$tna_wise_prod_perc=number_format(($tna_wise_grey_availlable_qnty/$booking_qnty)*100,2);
	
									$dateDiff_plan=datediff( d, $tna_start_date, $plan_end_date);
									$plan_wise_iss_qnty=$yarn_iss_arr[$row[csf('po_id')]][$tna_start_date];
									
									$plan_wise_prod_qnty=$prod_arr[$row[csf('po_id')]][$tna_start_date];
									$plan_wise_prod_trans_qnty=$prod_trans_arr[$row[csf('po_id')]][$tna_start_date];
									$plan_wise_grey_availlable_qnty=$plan_wise_prod_qnty+$plan_wise_prod_trans_qnty;
									
									if($dateDiff_plan>0)
									{
										for($k=0;$k<$dateDiff_plan;$k++)
										{
											$newdate=add_date($tna_start_date,$k);
											$plan_wise_iss_qnty+=$yarn_iss_arr[$row[csf('po_id')]][$newdate];
											$plan_wise_grey_availlable_qnty+=$prod_arr[$row[csf('po_id')]][$newdate]+$prod_trans_arr[$row[csf('po_id')]][$newdate];
										}
									}
									$plan_wise_iss_perc=number_format(($plan_wise_iss_qnty/$booking_qnty)*100,2);
									$plan_wise_prod_perc=number_format(($plan_wise_grey_availlable_qnty/$booking_qnty)*100,2);
	
									$dateDiff_actual=datediff( d, $tna_start_date, $actual_end_date);
									$actual_iss_qnty=$yarn_iss_arr[$row[csf('po_id')]][$trans_start_date];
									
									$actual_prod_qnty=$prod_arr[$row[csf('po_id')]][$tna_start_date];
									$actual_wise_prod_trans_qnty=$prod_trans_arr[$row[csf('po_id')]][$tna_start_date];
									$actual_grey_availlable_qnty=$actual_prod_qnty+$actual_wise_prod_trans_qnty;
									
									if($dateDiff_actual>0)
									{
										for($k=0;$k<$dateDiff_actual;$k++)
										{
											$newdate=add_date($tna_start_date,$k);
											$actual_iss_qnty+=$yarn_iss_arr[$row[csf('po_id')]][$newdate];
											$actual_grey_availlable_qnty+=$prod_arr[$row[csf('po_id')]][$newdate]+$prod_trans_arr[$row[csf('po_id')]][$newdate];
										}
									}
									$actual_iss_perc=number_format(($actual_iss_qnty/$booking_qnty)*100,2);
									$actual_prod_perc=number_format(($actual_grey_availlable_qnty/$booking_qnty)*100,2);
									
									$tdColor_tna_yarn=''; $tdColor_plan_yarn=''; $tdColor_actual_yarn='';
									$tdColor_tna_prod=''; $tdColor_plan_prod=''; $tdColor_actual_prod='';
									
									$tna_end_date_for_comp=change_date_format($tna_end_date, "yyyy-mm-dd", "-");
									$plan_end_date_for_comp=change_date_format($plan_end_date, "yyyy-mm-dd", "-");
									$actual_end_date_for_comp=change_date_format($actual_end_date, "yyyy-mm-dd", "-");
									
									if($today>$tna_end_date_for_comp) 
									{
										if($tna_wise_iss_qnty<$booking_qnty) $tdColor_tna_yarn='red';
										if($tna_wise_grey_availlable_qnty<$booking_qnty) $tdColor_tna_prod='red';
									}
									
									if($today>$plan_end_date_for_comp) 
									{
										if($plan_wise_iss_qnty<$booking_qnty) $tdColor_plan_yarn='red';
										if($plan_wise_grey_availlable_qnty<$booking_qnty) $tdColor_plan_prod='red';
									}
									
									if($actual_end_date_for_comp>$tna_end_date_for_comp) 
									{
										if($actual_iss_qnty<$booking_qnty) $tdColor_actual_yarn='red';
										if($actual_grey_availlable_qnty<$booking_qnty) $tdColor_actual_prod='red';
									}
									
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
										<td width="40"><? echo $i; ?></td>
                                        <td width="70">&nbsp;<? echo $row[csf('file_no')]; ?></td>
                                        <td width="70">&nbsp;<? echo $row[csf('grouping')]; ?></td>
                                        <td width="70"><div style="word-break:break-all"><? echo $row[csf('style_ref_no')]; ?></div></td>
                                        <td width="100">&nbsp;<? echo $booking_no; ?></td>
                                                
										<td width="55">&nbsp;&nbsp;<? echo $row[csf('job_no_prefix_num')]; ?></td>
										<td width="40" align="center"><? echo $row[csf('year')]; ?>&nbsp;</td>
										<td width="100"><p><? echo $row[csf('po_number')]; ?></p></td>
                                        <td width="70" align="center"><p><a href="##" onClick="openmypage_greyAvailable('<? echo $row[csf('po_id')]; ?>','grey_yarn_info','<? echo $tna_start_date."_".$tna_end_date."_".$plan_start_date."_".$plan_end_date."_".$actual_start_date."_".$actual_end_date."_".$pub_shipment_date; ?>')"><? echo $pub_shipment_date; ?></a></p></td>
										<td width="80" align="center">&nbsp;<? echo $tna_start_date; ?></td>
										<td width="100" align="center">&nbsp;<? echo $tna_end_date; ?></td>
                                        <td width="80" align="center">&nbsp;<? echo $plan_start_date; ?></td>
										<td width="100" align="center">&nbsp;<? echo $plan_end_date; ?></td>
                                        <td width="80" align="center">&nbsp;<? echo $actual_start_date; ?></td>
										<td width="100" align="center">&nbsp;<? echo $actual_end_date; ?></td>
										<td align="right" width="90"><? echo number_format($req_qnty,2,'.',''); ?></td>
										<td align="right" width="80"><? echo number_format($prod_qnty,2,'.',''); ?></td>
                                        <td align="right" width="50"><? echo number_format($tot_prod_percent,2,'.',''); ?></td>
										<td align="right" width="80"><? echo number_format($recv_qnty,2,'.',''); ?></td>
										<td align="right" width="80"><? echo number_format($net_trans_qnty,2,'.',''); ?></td>
										<td align="right" width="90"><a href="##" onClick="openmypage_greyAvailable('<? echo $row[csf('po_id')]; ?>','grey_available','')"><? echo number_format($available_qnty,2,'.',''); ?></a></td>
										<td align="right"><? echo number_format($balance_qnty,2,'.',''); ?></td>
									</tr>
                                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2<? echo $i; ?>"> 
										<td width="40">&nbsp;</td>
                                        <td width="70">&nbsp;</td>
                                        <td width="70">&nbsp;</td>
                                        <td width="70">&nbsp;</td>
                                        <td width="100">&nbsp;</td>
										<td width="55">&nbsp;&nbsp;</td>
										<td width="40" align="center">&nbsp;</td>
										<td width="100">&nbsp;</td>
                                        <td width="70" align="center" rowspan="2">Yet To Ship:<br><? echo $yet_to_ship_days." days"; ?></a>&nbsp;</td>
										<td width="80" align="center">Yarn Delivered:</td>
										<td width="100" align="right" bgcolor="<? echo $tdColor_tna_yarn; ?>"><? echo number_format($tna_wise_iss_qnty,2)." Kg/ ".$tna_wise_iss_perc."%"; ?></td>
                                        <td width="80" align="center">Yarn Delivered:</td>
										<td width="100" align="right" bgcolor="<? echo $tdColor_plan_yarn; ?>"><? echo number_format($plan_wise_iss_qnty,2)." Kg/ ".$plan_wise_iss_perc."%"; ?></td>
                                        <td width="80" align="center">Yarn Delivered:</td>
										<td width="100" align="right" bgcolor="<? echo $tdColor_actual_yarn; ?>"><? echo number_format($actual_iss_qnty,2)." Kg/ ".$actual_iss_perc."%"; ?></td>
										<td align="right" width="90">&nbsp;</td>
										<td align="right" width="80">&nbsp;</td>
                                        <td align="right" width="50">&nbsp;</td>
										<td align="right" width="80">&nbsp;</td>
										<td align="right" width="80">&nbsp;</td>
										<td align="right" width="90">&nbsp;</td>
										<td align="right">&nbsp;</td>
									</tr>
                                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_3<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_3<? echo $i; ?>"> 
										<td width="40">&nbsp;</td>
                                        <td width="70">&nbsp;</td>
                                        <td width="70">&nbsp;</td>
                                        <td width="70">&nbsp;</td>
                                        <td width="100">&nbsp;</td>
                                        
										<td width="55">&nbsp;&nbsp;</td>
										<td width="40" align="center">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="80" align="center">Grey Production:</td>
										<td width="100" align="right" bgcolor="<? echo $tdColor_tna_prod; ?>"><? echo number_format($tna_wise_grey_availlable_qnty,2)." Kg/ ".$tna_wise_prod_perc."%"; ?></td>
                                        <td width="80" align="center">Grey Production:</td>
										<td width="100" align="right" bgcolor="<? echo $tdColor_plan_prod; ?>"><? echo number_format($plan_wise_grey_availlable_qnty,2)." Kg/ ".$plan_wise_prod_perc."%"; ?></td>
                                        <td width="80" align="center">Grey Production:</td>
										<td width="100" align="right" bgcolor="<? //echo $tdColor_actual_prod; ?>"><? echo number_format($actual_grey_availlable_qnty,2)." Kg/ ".$actual_prod_perc."%"; ?></td>
										<td align="right" width="90">&nbsp;</td>
										<td align="right" width="80">&nbsp;</td>
                                        <td align="right" width="50">&nbsp;</td>
										<td align="right" width="80">&nbsp;</td>
										<td align="right" width="80">&nbsp;</td>
										<td align="right" width="90">&nbsp;</td>
										<td align="right">&nbsp;</td>
									</tr>
									<?
									
									$total_req_qnty+=$req_qnty;
									$total_prod_qnty+=$prod_qnty;
									$total_recv_qnty+=$recv_qnty;
									$total_net_trans_qnty+=$net_trans_qnty;
									$total_available_qnty+=$available_qnty;
									$total_balance+=$balance_qnty;
									
									$buyer_req_qnty+=$req_qnty;
									$buyer_prod_qnty+=$prod_qnty;
									$buyer_recv_qnty+=$recv_qnty;
									$buyer_net_trans_qnty+=$net_trans_qnty;
									$buyer_available_qnty+=$available_qnty;
									$buyer_balance+=$balance_qnty;
		
									$i++;
								}
							}
							
							if($i>1)
							{
								$total_prod_percent_buyer=$buyer_req_qnty/$buyer_prod_qnty*100;
							?>
								<tr bgcolor="#CCCCCC" id="tr_<? echo $i; ?>">
									<td colspan="15" align="right"><b>Buyer Total</b></td>
									<td align="right"><b><? echo number_format($buyer_req_qnty,2,'.',''); ?></b></td>
                                    <td align="right"><b><? echo number_format($buyer_prod_qnty,2,'.',''); ?></b></td>
                                    <td align="right"><b>&nbsp;<? //echo number_format($total_prod_percent_buyer,2,'.',''); ?></b></td>
                                    <td align="right"><b><? echo number_format($buyer_recv_qnty,2,'.',''); ?></b></td>
                                    <td align="right"><b><? echo number_format($buyer_net_trans_qnty,2,'.',''); ?></b></td>
                                    <td align="right"><b><? echo number_format($buyer_available_qnty,2,'.',''); ?></b></td>
                                    <td align="right"><b><? echo number_format($buyer_balance,2,'.',''); ?></b></td>
								</tr>
							<?
							}
							$total_grand_prod_percent=$total_req_qnty/$total_prod_qnty*100;
						?>
						<tfoot>
							<th colspan="15" align="right">Grand Total</th>
							<th align="right"><? echo number_format($total_req_qnty,2,'.',''); ?></th>
							<th align="right"><? echo number_format($total_prod_qnty,2,'.',''); ?></th>
                            <th align="right">&nbsp;<? //echo number_format($total_grand_prod_percent,2,'.',''); ?></th>
                            <th align="right"><? echo number_format($total_recv_qnty,2,'.',''); ?></th>
                            <th align="right"><? echo number_format($total_net_trans_qnty,2,'.',''); ?></th>
                            <th align="right"><? echo number_format($total_available_qnty,2,'.',''); ?></th>
							<th align="right"><? echo number_format($total_balance,2,'.',''); ?></th>
						</tfoot>
					</table>
				</div>
			</fieldset>
		</div>
	<?	
	}
	
	foreach (glob("../../../ext_resource/tmp_report/$user_name*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$html=ob_get_contents();
	$name=time();
	$filename="../../../ext_resource/tmp_report/".$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,$html);
	$filename="../../ext_resource/tmp_report/".$user_name."_".$name.".xls";
	echo "$total_data####$filename";
	exit();

}

if($action=="grey_yarn_info")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$data=explode("_",$data);
	
	if($db_type==0)
	{
		$tna_start_date=change_date_format($data[0], "yyyy-mm-dd", "-");
		$tna_end_date=change_date_format($data[1], "yyyy-mm-dd", "-");
		$plan_start_date=change_date_format($data[2], "yyyy-mm-dd", "-");
		$plan_end_date=change_date_format($data[3], "yyyy-mm-dd", "-");
		$actual_start_date=change_date_format($data[4], "yyyy-mm-dd", "-");
		$actual_end_date=change_date_format($data[5], "yyyy-mm-dd", "-");
	}
	else
	{
		$tna_start_date=change_date_format($data[0],'','',1);
		$tna_end_date=change_date_format($data[1],'','',1);
		$plan_start_date=change_date_format($data[2],'','',1);
		$plan_end_date=change_date_format($data[3],'','',1);
		$actual_start_date=change_date_format($data[4],'','',1);
		$actual_end_date=change_date_format($data[5],'','',1);
	}
	
	$pub_shipment_date=change_date_format($data[6], "yyyy-mm-dd", "-");
	$today=date("Y-m-d");
	$yet_to_ship_days=datediff( d, $today, $pub_shipment_date);
	
	$booking_qnty=return_field_value("sum(b.grey_fab_qnty) as qnty","wo_booking_mst a, wo_booking_dtls b","a.booking_no=b.booking_no and a.item_category in(2,13) and b.po_break_down_id='$po_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0",'qnty');
	
	$yarn_iss_data=sql_select("select sum(case when a.transaction_date<='$tna_end_date' then b.quantity else 0 end) as tna_wise_iss_qnty, sum(case when a.transaction_date between '$tna_start_date' and '$plan_end_date' then b.quantity else 0 end) as plan_wise_iss_qnty, sum(case when a.transaction_date between '$tna_start_date' and '$actual_end_date' then b.quantity else 0 end) as actual_iss_qnty from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.item_category=1 and a.transaction_type=2 and b.po_breakdown_id='$po_id' and b.entry_form=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	
	$tna_wise_iss_qnty=$yarn_iss_data[0][csf('tna_wise_iss_qnty')];
	$plan_wise_iss_qnty=$yarn_iss_data[0][csf('plan_wise_iss_qnty')];
	$actual_iss_qnty=$yarn_iss_data[0][csf('actual_iss_qnty')];
	
	$prodData=sql_select("select sum(case when a.receive_date<='$tna_end_date' then c.quantity else 0 end) as tna_wise_prod_qnty, sum(case when a.receive_date between '$tna_start_date' and '$plan_end_date' then c.quantity else 0 end) as plan_wise_prod_qnty, sum(case when a.receive_date between '$tna_start_date' and '$actual_end_date' then c.quantity else 0 end) as actual_prod_qnty from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id='$po_id' and a.entry_form in(2,22) and c.entry_form in(2,22) and a.receive_basis<>9 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
	
	$tna_wise_prod_qnty=$prodData[0][csf('tna_wise_prod_qnty')];
	$plan_wise_prod_qnty=$prodData[0][csf('plan_wise_prod_qnty')];
	$actual_prod_qnty=$prodData[0][csf('actual_prod_qnty')];
	
	$transData=sql_select("select sum(case when a.transfer_date<='$tna_end_date' and c.trans_type=5 then c.quantity else 0 end) as tna_wise_trans_in_qnty, sum(case when a.transfer_date between '$tna_start_date' and '$plan_end_date' and c.trans_type=5 then c.quantity else 0 end) as plan_wise_trans_in_qnty, sum(case when a.transfer_date between '$tna_start_date' and '$actual_end_date' and c.trans_type=5 then c.quantity else 0 end) as actual_trans_in_qnty, sum(case when a.transfer_date<='$tna_end_date' and c.trans_type=6 then c.quantity else 0 end) as tna_wise_trans_out_qnty, sum(case when a.transfer_date between '$tna_start_date' and '$plan_end_date' and c.trans_type=6 then c.quantity else 0 end) as plan_wise_trans_out_qnty, sum(case when a.transfer_date between '$tna_start_date' and '$actual_end_date' and c.trans_type=6 then c.quantity else 0 end) as actual_trans_out_qnty from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id='$po_id' and a.item_category=13 and a.transfer_criteria=4 and c.entry_form=13 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by c.po_breakdown_id");
	
	$tna_wise_grey_availlable_qnty=$tna_wise_prod_qnty+$transData[0][csf('tna_wise_trans_in_qnty')]-$transData[0][csf('tna_wise_trans_out_qnty')];
	$plan_wise_grey_availlable_qnty=$plan_wise_prod_qnty+$transData[0][csf('plan_wise_trans_in_qnty')]-$transData[0][csf('plan_wise_trans_out_qnty')];
	$actual_grey_availlable_qnty=$actual_prod_qnty+$transData[0][csf('actual_trans_in_qnty')]-$transData[0][csf('actual_trans_out_qnty')];
	
	$tna_wise_iss_perc=($tna_wise_iss_qnty/$booking_qnty)*100;
	$plan_wise_iss_perc=($plan_wise_iss_qnty/$booking_qnty)*100;
	$actual_iss_perc=($actual_iss_qnty/$booking_qnty)*100;
	
	$tna_wise_prod_perc=($tna_wise_grey_availlable_qnty/$booking_qnty)*100;
	$plan_wise_prod_perc=($plan_wise_grey_availlable_qnty/$booking_qnty)*100;
	$actual_prod_perc=($actual_grey_availlable_qnty/$booking_qnty)*100;
	
?>
	<fieldset style="width:730px; margin-left:5px">
        <table border="1" class="rpt_table" rules="all" width="720" cellpadding="0" cellspacing="0">
        	<thead>
                <tr>
                    <th width="100" rowspan="2">Yet To Ship</th>
                    <th width="200" colspan="2">TNA</th>
                    <th width="200" colspan="2">Plan</th>
                    <th width="200" colspan="2">Actual</th>
                </tr>
                <tr>
                    <th width="100">Yarn Delivered</th>
                    <th width="100">Grey Production</th>
                    <th width="100">Yarn Delivered</th>
                    <th width="100">Grey Production</th>
                    <th width="100">Yarn Delivered</th>
                    <th width="100">Grey Production</th>
                </tr>
            </thead>
            <tr bgcolor="#FFFFFF">
                <td align="center"><? echo $yet_to_ship_days." Days"; ?></td>
                <td align="right"><? echo number_format($tna_wise_iss_qnty,2); ?>&nbsp;</td>
                <td align="right"><? echo number_format($tna_wise_grey_availlable_qnty,2); ?>&nbsp;</td>
                <td align="right"><? echo number_format($plan_wise_iss_qnty,2); ?>&nbsp;</td>
                <td align="right"><? echo number_format($plan_wise_grey_availlable_qnty,2); ?>&nbsp;</td>
                <td align="right"><? echo number_format($actual_iss_qnty,2); ?>&nbsp;</td>
                <td align="right"><? echo number_format($actual_grey_availlable_qnty,2); ?>&nbsp;</td>
            </tr>
            <tr bgcolor="#E9F3FF">
                <td align="center">In %</td>
                <td align="right"><? echo number_format($tna_wise_iss_perc,2); ?>&nbsp;</td>
                <td align="right"><? echo number_format($tna_wise_prod_perc,2); ?>&nbsp;</td>
                <td align="right"><? echo number_format($plan_wise_iss_perc,2); ?>&nbsp;</td>
                <td align="right"><? echo number_format($plan_wise_prod_perc,2); ?>&nbsp;</td>
                <td align="right"><? echo number_format($actual_iss_perc,2); ?>&nbsp;</td>
                <td align="right"><? echo number_format($actual_prod_perc,2); ?>&nbsp;</td>
            </tr>
         </table>
	</fieldset>   
<?
exit();
}

if($action=="grey_available")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$tna_start_date=''; $tna_end_date=''; $tna_start_date_print=''; $tna_end_date_print='';
	$tnaData=sql_select("select task_start_date, task_finish_date from tna_process_mst where task_number=60 and po_number_id='$po_id' and is_deleted=0 and status_active=1");
	if($tnaData[0][csf('task_start_date')]!="0000-00-00" && $tnaData[0][csf('task_start_date')]!="")
	{
		$tna_start_date=date("Y-m-d",strtotime($tnaData[0][csf('task_start_date')]));
		$tna_start_date_print=change_date_format($tnaData[0][csf('task_start_date')]);
	}
	
	if($tnaData[0][csf('task_finish_date')]!="0000-00-00" && $tnaData[0][csf('task_finish_date')]!="")
	{
		$tna_end_date=date("Y-m-d",strtotime($tnaData[0][csf('task_finish_date')]));
		$tna_end_date_print=change_date_format($tnaData[0][csf('task_finish_date')]);
	}
	$tna_days=datediff( d, $tna_start_date, $tna_end_date);
	
	$plan_start_date=''; $plan_end_date=''; $plan_start_date_print=''; $plan_end_date_print='';
	
	if($db_type==0)
	{
		$planData=sql_select("select min(case when a.start_date!='0000-00-00' then a.start_date end) as plan_start_date, max(a.end_date) as plan_end_date from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b where a.id=b.dtls_id and b.po_id='$po_id' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1");
	}
	else
	{
		$planData=sql_select("select min(case when a.start_date is not null then a.start_date end) as plan_start_date, max(a.end_date) as plan_end_date from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b where a.id=b.dtls_id and b.po_id='$po_id' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1");
	}
	
	if($planData[0][csf('plan_start_date')]!="0000-00-00" && $planData[0][csf('plan_start_date')]!="")
	{
		$plan_start_date=date("Y-m-d",strtotime($planData[0][csf('plan_start_date')]));
		$plan_start_date_print=change_date_format($planData[0][csf('plan_start_date')]);
	}
	
	if($planData[0][csf('plan_end_date')]!="0000-00-00" && $planData[0][csf('plan_end_date')]!="")
	{
		$plan_end_date=date("Y-m-d",strtotime($planData[0][csf('plan_end_date')]));
		$plan_end_date_print=change_date_format($planData[0][csf('plan_end_date')]);
	}
	$plan_days=datediff(d,$plan_start_date, $plan_end_date);

	$actual_start_date=''; $actual_end_date=''; $actual_start_date_print=''; $actual_end_date_print='';
	$prodData=sql_select("select min(a.receive_date) as actual_start_date, max(a.receive_date) as actual_end_date from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id='$po_id' and a.entry_form in(2,22) and c.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
	
	if($prodData[0][csf('actual_start_date')]!="0000-00-00" && $prodData[0][csf('actual_start_date')]!="")
	{
		$actual_start_date=date("Y-m-d",strtotime($prodData[0][csf('actual_start_date')]));
		$actual_start_date_print=change_date_format($prodData[0][csf('actual_start_date')]);
	}
	
	if($prodData[0][csf('actual_end_date')]!="0000-00-00" && $prodData[0][csf('actual_end_date')]!="")
	{
		$actual_end_date=date("Y-m-d",strtotime($prodData[0][csf('actual_end_date')]));
		$actual_end_date_print=change_date_format($prodData[0][csf('actual_end_date')]);
	}
	
	$actual_trans_start_date=''; $actual_trans_end_date='';
	$transData=sql_select("select min(a.transfer_date) as actual_trans_start_date, max(a.transfer_date) as actual_trans_end_date from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id='$po_id' and a.item_category=13 and a.transfer_criteria=4 and c.trans_type=5 and c.entry_form=13 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	
	if($transData[0][csf('actual_trans_start_date')]!="0000-00-00" && $transData[0][csf('actual_trans_start_date')]!="")
	{
		$actual_trans_start_date=date("Y-m-d",strtotime($transData[0][csf('actual_trans_start_date')])); 
	}
	
	if($transData[0][csf('actual_trans_end_date')]!="0000-00-00" && $transData[0][csf('actual_trans_end_date')]!="")
	{
		$actual_trans_end_date=date("Y-m-d",strtotime($transData[0][csf('actual_trans_end_date')]));
	}

	if($actual_trans_start_date<$actual_start_date && $actual_trans_start_date!="") 
	{
		$actual_start_date=$actual_trans_start_date;
		$actual_start_date_print=change_date_format($transData[0][csf('actual_trans_start_date')]);
	}

	if($actual_trans_end_date>$actual_end_date) 
	{
		$actual_end_date=$actual_trans_end_date;
		$actual_end_date_print=change_date_format($transData[0][csf('actual_trans_end_date')]);
	}
	
	$actual_prod_days=datediff(d,$actual_start_date, $actual_end_date);
	
	$bg_color_plan=""; $bg_color_actual=""; $bg_color_plan_end=""; $bg_color_actual_end=""; $bg_color_deviation=""; $bg_color_actual_deviation="";
	
	if($plan_start_date>$tna_start_date)
	{
		$bg_color_plan="red";
	}
	
	if($actual_start_date>$tna_start_date)
	{
		$bg_color_actual="red";
	}
	
	if($plan_end_date>$tna_end_date)
	{
		$bg_color_plan_end="red";
	}
	
	if($actual_end_date>$tna_end_date)
	{
		$bg_color_actual_end="red";
	}
	
	/*$plan_deviation=$tna_days-$plan_days; 
	$actual_deviation=$tna_days-$actual_prod_days;*/
	if($plan_end_date!="" && $tna_end_date!="")
	{
		$plan_deviation=datediff(d,$plan_end_date, $tna_end_date);
		if($plan_deviation<=0) $plan_deviation=$plan_deviation-1;
		//$plan_deviation=$tna_end_date-$plan_end_date;
	}
	if($plan_end_date!="" && $actual_end_date!="")
	{
		$actual_deviation=datediff(d,$actual_end_date,$tna_end_date);
		if($actual_deviation<=0) $actual_deviation=$actual_deviation-1;
		//$actual_deviation=$plan_end_date-$actual_end_date;
	}
	
	
	if($plan_deviation<0)
	{
		$bg_color_deviation="red";
	}
	
	if($actual_deviation<0)
	{
		$bg_color_actual_deviation="red";
	}
	
?>
	<fieldset style="width:480px; margin-left:5px">
        <table border="1" class="rpt_table" rules="all" width="475" cellpadding="0" cellspacing="0">
            <thead>
                <th width="120">Particulars</th>
                <th width="100">Start Date</th>
                <th width="100">End Date</th>
                <th width="70">Days</th>
                <th>Deviation</th>
            </thead>
            <tr bgcolor="#E9F3FF">
                <td>As Per TNA</td>
                <td align="center"><? echo $tna_start_date_print; ?>&nbsp;</td>
                <td align="center"><? echo $tna_end_date_print; ?>&nbsp;</td>
                <td align="right" style="padding-right:5px">&nbsp;<? echo $tna_days; ?></td>
                <td align="right">&nbsp;</td>
            </tr>
            <tr bgcolor="#FFFFFF">
                <td>As Knitting Plan</td>
                <td align="center" bgcolor="<? echo $bg_color_plan; ?>"><? echo $plan_start_date_print; ?>&nbsp;</td>
                <td align="center" bgcolor="<? echo $bg_color_plan_end; ?>"><? echo $plan_end_date_print; ?>&nbsp;</td>
                <td align="right" style="padding-right:5px">&nbsp;<? if($plan_days>0) echo $plan_days; ?></td>
               <td align="right" bgcolor="<? echo $bg_color_deviation; ?>" style="padding-right:5px">&nbsp;<? if($plan_deviation!=0 && $plan_days>0) echo $plan_deviation; ?></td>
            </tr>
            <tr bgcolor="#E9F3FF">
                <td>As Per Actual Production</td>
                <td align="center" bgcolor="<? echo $bg_color_actual; ?>"><? echo $actual_start_date_print; ?>&nbsp;</td>
                <td align="center" bgcolor="<? echo $bg_color_actual_end; ?>"><? echo $actual_end_date_print; ?>&nbsp;</td>
                <td align="right" style="padding-right:5px">&nbsp;<? if($actual_prod_days>0) echo $actual_prod_days; ?></td>
                <td align="right" bgcolor="<? echo $bg_color_actual_deviation; ?>" style="padding-right:5px">&nbsp;<? if($actual_deviation!=0 && $actual_prod_days>0) echo $actual_deviation; ?></td>
            </tr>
         </table>
	</fieldset>   
<?
exit();
}


if($action=="order_wise_knitting_tna_report")
{
	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$company_name= str_replace("'","",$cbo_company_name);
	$cbo_search_by= str_replace("'","",$cbo_search_by);
	$txt_search_data= str_replace("'","",$txt_search_common);
	$type = str_replace("'","",$cbo_type);
	if(str_replace("'","",$hide_buyer_id)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and c.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and c.buyer_id in (".str_replace("'","",$hide_buyer_id).")";
	}
	
	if($txt_search_data!='')
	{
		if($cbo_search_by==1) 
			$search_cond="and e.file_no='$txt_search_data'"; 
		else if($cbo_search_by==2) 
			$search_cond="and e.grouping='$txt_search_data'"; 	
		else 
			$search_cond="and d.style_ref_no='$txt_search_data'";
	}
	
	$txt_job_no=str_replace("'","",$txt_job_no);
	if(trim($txt_job_no)!=""){$job_no_con=" and d.job_no_prefix_num = '".trim($txt_job_no)."'";}else{$job_no_con="";}
	
	
	
	$cbo_year=str_replace("'","",$cbo_year);
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0) 
		{
			$year_cond=" and YEAR(d.insert_date)=$cbo_year";
			$year_field=", YEAR(d.insert_date) as year ";
		}
		else if($db_type==2) 
		{
			$year_cond=" and to_char(d.insert_date,'YYYY')=$cbo_year";
			$year_field=", to_char(d.insert_date,'YYYY') as year ";
		}
		else 
		{
			$year_cond=""; $year_field="";
		}
	}
	else $year_cond="";
	
	if(str_replace("'","",trim($txt_order_no))=="")
	{
		$po_cond="";
	}
	else
	{
		if(str_replace("'","",$hide_order_id)!="")
		{
			$po_id=str_replace("'","",$hide_order_id);
			$po_cond="and c.po_id in(".$po_id.")";
		}
		else
		{
			$po_number=trim(str_replace("'","",$txt_order_no))."%";
			$po_cond="and e.po_number like '$po_number'";
		}
	}
	
	
		$start_date=str_replace("'","",trim($txt_date_from));
		$end_date=str_replace("'","",trim($txt_date_to));
		
		
		if($type==1){
			$date_cond="";
			if($start_date!="" && $end_date!="")
			{
				$date_cond="and b.start_date between '".$start_date."' and '".$end_date."'";
			}
		}
		else
		{
			$tna_date_cond="";
			if($start_date!="" && $end_date!="")
			{
				$tna_date_cond="and task_start_date between '".$start_date."' and '".$end_date."'";
			}
		}

	//count arr.................................
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
	//$programArr=return_library_array("select barcode_no,booking_no from pro_roll_details where status_active=1 and is_deleted=0 and booking_no is not null", "barcode_no", "booking_no");
	//var_dump($programArr[16220001092];	
	
//TNA Data................................................
	$tna_array=array();
	$tna_sql=sql_select("select id, po_number_id, task_start_date, task_finish_date from tna_process_mst where task_number=60 and is_deleted=0 and status_active=1 $tna_date_cond");
	foreach($tna_sql as $row)
	{
		$tna_arr[$row[csf('po_number_id')]]['tna_start']=$row[csf('task_start_date')];
		$tna_arr[$row[csf('po_number_id')]]['tna_finish']=$row[csf('task_finish_date')];
		$tna_arr[$row[csf('po_number_id')]]['act_start']=$row[csf('actual_start_date')];
		$tna_arr[$row[csf('po_number_id')]]['act_finish']=$row[csf('actual_finish_date')];
		$tna_po_id_arr[$row[csf('po_number_id')]]=$row[csf('po_number_id')];
		
	}
	unset($tna_sql);	
		$po_con='';
		if($type==2 && $start_date!='' && $end_date!=''){
			$po_con =" and ";
			$p=1;
			$po_id_chunk_arr=array_chunk($tna_po_id_arr,995) ;
			foreach($po_id_chunk_arr as $chunk_arr)
			{
				if($p==1) $po_con .="  ( c.po_id in(".implode(",",$chunk_arr).")"; else  $po_con .=" or c.po_id in(".implode(",",$chunk_arr).")";
				
				$p++;
			}
			$po_con .=")";
		
		}
		
	
//Requisition................................
	$sql="select a.knit_id as program_no,a.requisition_no,b.lot,b.yarn_count_id,b.brand,a.yarn_qnty from ppl_yarn_requisition_entry a,product_details_master b where b.id=a.prod_id and b.company_id=$company_name and a.is_deleted=0 and a.status_active=1";
	$sql_result=sql_select($sql);
		foreach( $sql_result as $row )
		{
			$requisition_no_arr[$row[csf('program_no')]][_req_no]=$row[csf('requisition_no')];
			$requisition_no_arr[$row[csf('program_no')]][_lot]=$row[csf('lot')];
			$requisition_no_arr[$row[csf('program_no')]][_count]=$row[csf('yarn_count_id')];
			$requisition_no_arr[$row[csf('program_no')]][_brand]=$row[csf('brand')];
			$requisition_no_arr[$row[csf('program_no')]][_qty]+=$row[csf('yarn_qnty')];
		}
	unset($sql_result);
	//Issue................................
	$sql="select a.transaction_type,a.requisition_no,a.cons_quantity as rec_qty,0 as issue_qty from inv_transaction a where a.receive_basis=3 and a.transaction_type =2 and a.company_id=$company_name and a.is_deleted=0 and a.status_active=1
	union all
	select a.transaction_type,mst.booking_id as requisition_no,0 as rec_qty,a.cons_quantity as issue_qty from inv_receive_master mst,inv_transaction a where mst.id=a.mst_id and  a.receive_basis=3 and a.transaction_type =4 and a.company_id=$company_name and a.is_deleted=0 and a.status_active=1
	";
	
	
	
	$sql_result=sql_select($sql);
		foreach( $sql_result as $row )
		{
			if($row[csf('transaction_type')]==2){$issue_qty_arr[$row[csf('requisition_no')]]+=$row[csf('rec_qty')];}
			elseif($row[csf('transaction_type')]==4){$yern_iss_ret_qty_arr[$row[csf('requisition_no')]]+=$row[csf('issue_qty')];}
		}
	unset($sql_result);


//var_dump($yern_iss_ret_qty_arr);

//------------------------------------------------------------------
	
	
	
$sql="select 
		a.dia,
		a.color_type_id,
		a.gsm_weight,
		b.id as program_no, 
		b.knitting_source, 
		b.knitting_party, 
		b.color_id, 
		b.color_range, 
		b.machine_dia, 
		b.width_dia_type, 
		b.machine_gg, 
		b.fabric_dia, 
		b.program_qnty, 
		b.stitch_length, 
		b.spandex_stitch_length, 
		b.draft_ratio, 
		b.machine_id, 
		b.machine_capacity, 
		b.distribution_qnty, 
		b.status, 
		b.start_date, 
		b.end_date, 
		b.program_date, 
		b.feeder, 
		b.remarks, 
		b.save_data, 
		b.no_fo_feeder_data, 
		b.location_id, 
		b.advice, 
		b.collar_cuff_data,
		b.grey_dia,
		c.po_id,
		c.buyer_id,
		c.program_qnty,
		e.po_number,
		d.job_no,
		d.style_ref_no,
		(d.total_set_qnty*e.po_quantity) as po_quantity_pcs,
		e.pub_shipment_date as ship_date,
		c.fabric_desc
	from 
		ppl_planning_info_entry_mst a,
		ppl_planning_info_entry_dtls b,
		ppl_planning_entry_plan_dtls c ,
		wo_po_details_master d, 
		wo_po_break_down e
	where 
		a.id=b.mst_id and
		b.id=c.dtls_id and
		c.po_id=e.id and
		e.job_no_mst=d.job_no and
		c.company_id=$company_name and
		e.is_deleted=0 and
		c.is_deleted=0 and
		c.status_active=1 and
		d.status_active=1 and
		d.is_deleted=0
		$job_no_con
		$buyer_id_cond
		$year_cond
		$date_cond
		$po_con
		$po_cond $search_cond and
		c.program_qnty>0
	order by c.po_id,c.fabric_desc
	
	";
	    //echo $date_cond;die;
		  // echo $sql;
	$programmeData=array();
	$data_array=sql_select($sql);
	foreach ($data_array as $row)
	{ 

		
		$po_no_arr[$row[csf('po_id')]]=$row[csf('po_number')];
		$job_no_arr[$row[csf('po_id')]]=$row[csf('job_no')];
		$buyer_arr[$row[csf('po_id')]]=$row[csf('buyer_id')];
		$po_pcs_qty_arr[$row[csf('po_id')]]=$row[csf('po_quantity_pcs')];
		$style_arr[$row[csf('po_id')]]=$row[csf('style_ref_no')];
		$ship_date_arr[$row[csf('po_id')]]=$row[csf('ship_date')];
		$programme_qty_arr[$row[csf('program_no')]][$row[csf('po_id')]]+=$row[csf('program_qnty')];
		
		//$programme_qty_arr[$row[csf('po_id')]][$row[csf('program_no')]]=$row[csf('program_qnty')];
		
		list($construction,$copmposition)=explode(",",$row[csf('fabric_desc')]);
			//$construction_row_span_arr[$row[csf('po_id')]][$construction]+=1;
			//$copmposition_row_span_arr[$row[csf('po_id')]][$copmposition]+=1;
		
			$key=$row[csf('po_id')].trim($construction).trim($copmposition).$row[csf('color_type_id')];
			$row_span_arr[$key]+=1;
			$row_sub_tot_arr[$row[csf('po_id')]][$row[csf('program_no')]]=1;
			$plan_date_arr_str[$row[csf('po_id')]][]=$row[csf('start_date')];
			$plan_date_arr_end[$row[csf('po_id')]][]=$row[csf('end_date')];
		
			$construction_arr[$key]=$construction;
			$copmposition_arr[$key]=$copmposition;
			$color_type_arr[$key]=$row[csf('color_type_id')];
		
		
		$programmeDataArr[$row[csf('po_id')]][$key][$row[csf('program_no')]]=array(
			start_date=>$row[csf('start_date')],
			end_date=>$row[csf('end_date')],
			machine_dia=>$row[csf('machine_dia')],
			knitting_source=>$row[csf('knitting_source')],
			color_range=>$row[csf('color_range')],
			color_id=>$row[csf('color_id')],
			stitch_length=>$row[csf('stitch_length')],
			dia=>$row[csf('dia')],
			color_type_id=>$row[csf('color_type_id')],
			gsm_weight=>$row[csf('gsm_weight')],
			grey_dia=>$row[csf('grey_dia')],
			fabric_dia=>$row[csf('fabric_dia')]
		);
		$orderIdArr[$row[csf('po_id')]]=$row[csf('po_id')];
		
		$programNOArr[$row[csf('po_id')]][$key][$row[csf('program_no')]]=$row[csf('program_no')];
		$numberOfPOInJob[$row[csf('job_no')]][$row[csf('po_id')]]=1;
		
	}
	unset($data_array);
//var_dump($row_span_arr);	
//----------------------------------------------------	

//Fabric Dtls.........................................................................
	if(count($orderIdArr)!=0){
		$po_con_fabric_dtls =" and ";
		$p=1;
		$po_id_chunk_arr=array_chunk($orderIdArr,995) ;
		foreach($po_id_chunk_arr as $chunk_arr)
		{
			if($p==1){$po_con_fabric_dtls .=" ( b.po_break_down_id in(".implode(",",$chunk_arr).")";} 
			else {$po_con_fabric_dtls .=" or b.po_break_down_id in(".implode(",",$chunk_arr).")";} 
			$p++;
		}
		$po_con_fabric_dtls .=")";
	}
	else
	{
		$po_con_fabric_dtls =" and  b.po_break_down_id=0";	
	}
	
	$sql="select a.supplier_id,b.lot_no,b.brand,b.po_break_down_id,b.construction,b.color_type,b.copmposition, sum(b.grey_fab_qnty) as grey_req_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $po_con_fabric_dtls group by a.supplier_id,b.po_break_down_id,b.construction,b.copmposition,b.lot_no,b.brand,b.color_type";
	$sql_result=sql_select($sql);
		foreach( $sql_result as $row )
		{
			$key=$row[csf('po_break_down_id')].$row[csf('construction')].$row[csf('copmposition')].$row[csf('color_type')];
			$grey_qty_arr[$key]+=$row[csf('grey_req_qnty')];
			$out_lot_no_arr[$key]=$row[csf('lot_no')];
			$out_brand_arr[$key]=$row[csf('brand')];
			$out_knitting_com_arr[$key]=$row[csf('supplier_id')];

			//$grey_qty_arr[$row[csf('po_break_down_id')]]+=$row[csf('grey_req_qnty')];
			//$construction_arr[$row[csf('po_break_down_id')]]=$row[csf('construction')];
			//$copmposition_arr[$row[csf('po_break_down_id')]]=$row[csf('copmposition')];
		}

//Knitting Production..................................................................
	if(count($orderIdArr)!=0){
		$po_con_knitting_pro =" and ";
		$p=1;
		$po_id_chunk_arr=array_chunk($orderIdArr,995) ;
		foreach($po_id_chunk_arr as $chunk_arr)
		{
			if($p==1){$po_con_knitting_pro .=" ( c.po_breakdown_id in('".implode("','",$chunk_arr)."')";} 
			else {$po_con_knitting_pro .=" or c.po_breakdown_id in('".implode("','",$chunk_arr)."')";} 
			$p++;
		}
		$po_con_knitting_pro .=")";
	}
	else
	{
		$po_con_knitting_pro .=" and  c.po_breakdown_id=0";	
	}
	//$sql="select a.id,b.no_of_roll,b.grey_receive_qnty,a.booking_id,a.knitting_source,b.order_id,a.knitting_company, b.prod_id,b.yarn_lot,b.brand_id from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.receive_basis=2 and a.company_id=$company_name and a.entry_form=2 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $po_con_knitting_pro";
	
    $sql="select a.booking_id,c.po_breakdown_id as order_id, b.trans_id, c.quantity as grey_receive_qnty from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $po_con_knitting_pro";
	
	$sql_result=sql_select($sql);
	foreach( $sql_result as $row )
	{
		//$programme_id_arr[$row[csf('id')]][$row[csf('order_id')]]=$row[csf('booking_id')];
		$knit_pro_qty_arr[$row[csf('booking_id')]][$row[csf('order_id')]]+=$row[csf('grey_receive_qnty')];
		$rol_qty_arr[$row[csf('booking_id')]][$row[csf('order_id')]]+=$row[csf('no_of_roll')];
		$in_yarn_lot_arr[$row[csf('booking_id')]][$row[csf('order_id')]]=$row[csf('yarn_lot')];
		$in_brand_id_arr[$row[csf('booking_id')]][$row[csf('order_id')]]=$row[csf('brand_id')];
		$in_knitting_com_arr[$row[csf('booking_id')]][$row[csf('order_id')]]=$row[csf('knitting_company')];
		if($row[csf('trans_id')])
		{
			$rec_qty_arr[$row[csf('booking_id')]][$row[csf('order_id')]]+=$row[csf('grey_receive_qnty')];
		}
	}

//echo $sql;

//Out bound............................................................................
	if(count($orderIdArr)!=0){
		$po_con_out_bound =" and ";
		$p=1;
		$po_id_chunk_arr=array_chunk($orderIdArr,995) ;
		foreach($po_id_chunk_arr as $chunk_arr)
		{
			if($p==1){$po_con_out_bound .=" ( c.po_breakdown_id in(".implode(",",$chunk_arr).")";} 
			else {$po_con_out_bound .=" or c.po_breakdown_id in(".implode(",",$chunk_arr).")";} 
			$p++;
		}
		$po_con_out_bound .=")";
	}
	else
	{
		$po_con_out_bound =" and  c.po_breakdown_id=0";	
	}
	//$sql="select b.no_of_roll,b.grey_receive_qnty,b.program_no,c.po_break_down_id from inv_receive_master a, pro_grey_prod_entry_dtls b,wo_booking_dtls c where a.booking_no=c.booking_no and a.id=b.mst_id and a.receive_basis =11 and a.company_id=$company_name and a.entry_form =22 and c.booking_type=3 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $po_con_out_bound";
	
	
	$sql="select 1 as no_of_roll,b.brand_id,b.yarn_lot,c.qnty,c.booking_no,c.po_breakdown_id from inv_receive_master a, pro_grey_prod_entry_dtls b,pro_roll_details c where a.id=c.mst_id and b.id=c.dtls_id and a.id=b.mst_id and a.receive_basis =11 and a.company_id=$company_name and a.entry_form =22  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 $po_con_out_bound";
	
	
	$sql_result=sql_select($sql);
		foreach( $sql_result as $row )
		{
			$knit_pro_qty_arr[$row[csf('booking_no')]][$row[csf('po_breakdown_id')]]+=$row[csf('qnty')];
			$rol_qty_arr[$row[csf('booking_no')]][$row[csf('po_breakdown_id')]]+=$row[csf('no_of_roll')];
			$in_yarn_lot_arr[$row[csf('booking_no')]][$row[csf('po_breakdown_id')]]=$row[csf('yarn_lot')];
			$in_brand_id_arr[$row[csf('booking_no')]][$row[csf('po_breakdown_id')]]=$row[csf('brand_id')];
			//$in_knitting_com_arr[$row[csf('booking_no')]][$row[csf('po_breakdown_id')]]=$row[csf('knitting_company')];
		
		}

// echo $sql;
	
//Order to Order Transfer...............................................................
	if(count($orderIdArr)!=0){
		$po_con_order_to_order_tran =" and ";
		$p=1;
		$po_id_chunk_arr=array_chunk($orderIdArr,995) ;
		foreach($po_id_chunk_arr as $chunk_arr)
		{
			if($p==1){$po_con_order_to_order_tran .=" ( c.po_breakdown_id in(".implode(",",$chunk_arr).")";} 
			else {$po_con_order_to_order_tran .=" or c.po_breakdown_id in(".implode(",",$chunk_arr).")";} 
			$p++;
		}
		$po_con_order_to_order_tran .=")";
	}
	else
	{
		$po_con_order_to_order_tran =" and  c.po_breakdown_id=0";	
	}
	

	$product_arr=return_library_array( "select id, product_name_details from product_details_master where item_category_id=13",'id','product_name_details');
 
$sql="
	select mst.color_type_id,c.po_breakdown_id as order_id, a.id as program_no, c.prod_id,b.from_prod_id,
		a.knitting_source,
		a.machine_dia,
		a.stitch_length,
		a.color_range,
		a.color_id,
		a.fabric_dia,
		a.grey_dia,
		mst.gsm_weight,
		max(a.start_date) as start_date, 
		MIN(a.end_date) as end_date,  
		sum(a.program_qnty) as program_qnty,
		sum(case when c.trans_type=5 then c.quantity end) as ret_in_qty,
		sum(case when c.trans_type=6 then c.quantity end) as ret_out_qty
	from
		ppl_planning_info_entry_mst mst, 
		ppl_planning_info_entry_dtls a,
		inv_item_transfer_dtls b,
		order_wise_pro_details c 
	where mst.id=a.mst_id and a.id=b.from_program and b.id=c.dtls_id and c.entry_form =83 and c.trans_type in(5,6) $po_con_order_to_order_tran
	group by c.po_breakdown_id, a.id, mst.color_type_id,a.color_range,a.color_id,a.fabric_dia,a.grey_dia,mst.gsm_weight, c.prod_id, a.knitting_source,b.from_prod_id, a.machine_dia, a.stitch_length
 ";
 //echo $sql; 
	$sql_result=sql_select($sql);
		foreach( $sql_result as $row )
		{
			$trn_in_qty_arr[$row[csf('program_no')]][$row[csf('order_id')]]+=$row[csf('ret_in_qty')];
			$trn_out_qty_arr[$row[csf('program_no')]][$row[csf('order_id')]]+=$row[csf('ret_out_qty')];
			
			if($programme_qty_arr[$row[csf('program_no')]][$row[csf('order_id')]]=='' && $row[csf('program_no')]!=''){
				
				if($row[csf('ret_in_qty')]){$is_trn_in[$row[csf('program_no')]][$row[csf('order_id')]]=1;}
				list($construction,$copmposition)=explode(",",$product_arr[$row[csf('from_prod_id')]]);
				
				$key=$row[csf('order_id')].trim($construction).trim($copmposition).$row[csf('color_type_id')];
				$row_span_arr[$key]+=1;
				$row_sub_tot_arr[$row[csf('order_id')]][$row[csf('program_no')]]=1;
				
				$plan_date_arr_str[$row[csf('order_id')]][]=$row[csf('start_date')];
				$plan_date_arr_end[$row[csf('order_id')]][]=$row[csf('end_date')];
					
				$construction_arr[$key]=$construction;
				$copmposition_arr[$key]=$copmposition;
				$color_type_arr[$key]=$row[csf('color_type_id')];

				$programme_qty_arr[$row[csf('program_no')]][$row[csf('order_id')]]+=$row[csf('program_qnty')];

				$programmeDataArr[$row[csf('order_id')]][$key][$row[csf('program_no')]]=array(
					start_date		=>$row[csf('start_date')],
					end_date		=>$row[csf('end_date')],
					machine_dia		=>$row[csf('machine_dia')],
					knitting_source	=>$row[csf('knitting_source')],
					color_range		=>$row[csf('color_range')],
					color_id		=>$row[csf('color_id')],
					stitch_length	=>$row[csf('stitch_length')],
					dia				=>$row[csf('grey_dia')],
					color_type_id	=>$row[csf('color_type_id')],
					gsm_weight		=>$row[csf('gsm_weight')],
					fabric_dia		=>$row[csf('fabric_dia')]
				);
			
				$numberOfPOInJob[$job_no_arr[$row[csf('order_id')]]][$row[csf('order_id')]]=1;
				$programNOArr[$row[csf('order_id')]][$key][$row[csf('program_no')]]=$row[csf('program_no')];
			
			}
			
		}
		
	unset($sql_result);


	$programArr=return_library_array("select barcode_no,booking_no from pro_roll_details where status_active=1 and is_deleted=0 and booking_no is not null and po_breakdown_id in(".implode(',',$orderIdArr).")", "barcode_no", "booking_no");


//Order to Sample Transfer...............................................................
	if(count($orderIdArr)!=0){
		$po_con_order_to_order_tran =" and ";
		$p=1;
		$po_id_chunk_arr=array_chunk($orderIdArr,995) ;
		foreach($po_id_chunk_arr as $chunk_arr)
		{
			if($p==1){$po_con_order_to_order_tran .=" ( b.from_order_id in(".implode(",",$chunk_arr).")";} 
			else {$po_con_order_to_order_tran .=" or b.from_order_id in(".implode(",",$chunk_arr).")";} 
			$p++;
		}
		$po_con_order_to_order_tran .=")";
	}
	else
	{
		$po_con_order_to_order_tran =" and b.from_order_id =0";	
	}
	
	
$sql="
	select b.from_order_id, c.barcode_no, 
        sum(c.qnty) as ret_in_qty
    from
        inv_item_transfer_mst b,
        pro_roll_details c
    where b.id=c.mst_id  and c.entry_form =110 and b.transfer_criteria=6 $po_con_order_to_order_tran
    group by b.from_order_id,c.barcode_no
 ";
//echo $sql;
	$sql_result=sql_select($sql);
		foreach( $sql_result as $row )
		{
			$program_no=$programArr[$row[csf('barcode_no')]]; 
			$trn_out_qty_arr[$program_no][$row[csf('from_order_id')]]+=$row[csf('ret_in_qty')];
			
		}
	
//receive,issue.........................................................................
	if(count($orderIdArr)!=0){
		$po_con_rec_iss =" and ";
		$p=1;
		$po_id_chunk_arr=array_chunk($orderIdArr,995) ;
		foreach($po_id_chunk_arr as $chunk_arr)
		{
			if($p==1){$po_con_rec_iss .=" ( c.po_breakdown_id in(".implode(",",$chunk_arr).")";} 
			else {$po_con_rec_iss .=" or c.po_breakdown_id in(".implode(",",$chunk_arr).")";} 
			$p++;
		}
		$po_con_rec_iss .=")";
	}
	else
	{
		$po_con_rec_iss =" and c.po_breakdown_id =0";	
	}

	$sql="select c.booking_no,c.po_breakdown_id,b.rack,b.self,c.barcode_no,
		max(transaction_date) as rec_date,
		null as issue_date,
		count(case when c.entry_form=58 and a.transaction_type=1 then b.no_of_roll end) as tot_rec_roll,
		0 as tot_iss_roll,
		sum(case when c.entry_form =58 and a.transaction_type=1 then c.qnty end) as rec_qty,
		sum(case when c.entry_form=84 and a.transaction_type=4 then c.qnty end) as iss_rtn_qty,
		0 as iss_qty
	
	from inv_transaction a, pro_grey_prod_entry_dtls b,pro_roll_details c 
	where a.prod_id=b.prod_id and c.dtls_id=b.id and a.id=b.trans_id and a.mst_id=c.mst_id and c.mst_id=b.mst_id and a.transaction_type in(1,4) and c.entry_form in(58,84) and a.company_id=$company_name and a.item_category=13   AND b.status_active = 1 AND b.is_deleted = 0 $po_con_rec_iss
	group by c.booking_no,c.po_breakdown_id,b.rack,b.self,c.barcode_no
	
	union all
	select c.booking_no,c.po_breakdown_id,b.rack,b.self,c.barcode_no,
		null as rec_date,
		max(transaction_date) as issue_date,
		
		0 as tot_rec_roll,
		count(case when c.entry_form=61 and a.transaction_type=2 then b.no_of_roll end) as tot_iss_roll,
		0 as rec_qty,
		0 as iss_rtn_qty,
		sum(case when c.entry_form=61 and a.transaction_type=2 then c.qnty end) as iss_qty
		
	from inv_transaction a, inv_grey_fabric_issue_dtls b,pro_roll_details c 
	where a.prod_id=b.prod_id and c.dtls_id=b.id and a.id=b.trans_id and a.mst_id=c.mst_id and c.mst_id=b.mst_id and a.transaction_type in(2) and c.entry_form in(61) and a.company_id=$company_name and a.item_category=13
	  AND b.status_active = 1 AND b.is_deleted = 0
	 $po_con_rec_iss
	group by c.booking_no,c.po_breakdown_id	,b.rack,b.self,c.barcode_no
	";
	
	$sql_result=sql_select($sql);
		foreach( $sql_result as $row )
		{
			
			//$program_no=return_field_value("booking_no","pro_roll_details","entry_form=61 and status_active=1 and is_deleted=0 and barcode_no=".$row[csf('barcode_no')]."","booking_no");
			$program_no=$programArr[$row[csf('barcode_no')]];
			$rec_roll_qty_arr[$row[csf('booking_no')]][$row[csf('po_breakdown_id')]]+=$row[csf('tot_rec_roll')];
			$iss_roll_qty_arr[$row[csf('booking_no')]][$row[csf('po_breakdown_id')]]+=$row[csf('tot_iss_roll')];
			$rec_qty_arr[$row[csf('booking_no')]][$row[csf('po_breakdown_id')]]+=$row[csf('rec_qty')];
			//$iss_rtn_qty_arr[$row[csf('pi_wo_batch_no')]][$row[csf('po_breakdown_id')]]+=$row[csf('iss_rtn_qty')];
			$iss_rtn_qty_arr[$program_no][$row[csf('po_breakdown_id')]]+=$row[csf('iss_rtn_qty')];
			$iss_qty_arr[$row[csf('booking_no')]][$row[csf('po_breakdown_id')]]+=$row[csf('iss_qty')];
			$rec_rtn_qty_arr[$row[csf('booking_no')]][$row[csf('po_breakdown_id')]]+=$row[csf('rec_rtn_qty')];
		
			if($row[csf('rack')])$rec_rack_arr[$row[csf('booking_no')]][$row[csf('po_breakdown_id')]]=$row[csf('rack')];
			if($row[csf('self')])$rec_self_arr[$row[csf('booking_no')]][$row[csf('po_breakdown_id')]]=$row[csf('self')];
			if($row[csf('issue_date')])$last_iss_date_arr[$row[csf('booking_no')]][$row[csf('po_breakdown_id')]]=$row[csf('issue_date')];
			if($row[csf('rec_date')])$last_rec_date_arr[$row[csf('booking_no')]][$row[csf('po_breakdown_id')]]=$row[csf('rec_date')];
		
		}
			
	
	
	$orderIdArr_str = implode("','",$orderIdArr);
	//$sql = "select qnty from inv_material_allocation_mst where job_no in('".$job_no_str."')";
	$alo_qty_arr=return_library_array( "select po_break_down_id,sum(qnty) as qnty from inv_material_allocation_dtls where po_break_down_id in('".$orderIdArr_str."') group by po_break_down_id",'po_break_down_id','qnty');
	
	
ob_start();	
?>	

	
<fieldset style="width:5160px;">
   <span style="background:#FF3;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> Transfer in [Program No./Booking No].&nbsp;&nbsp;&nbsp;
   <span style="background:#8DCB62;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> Programme Qty < Knit Qty [Knit Qnty].
   <span style="background:#D00;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> (Programme Qty + 1% Toleration Qty of Grey Qty)< Programme Qty	[Prog. Qty].
    <table cellspacing="0" width="5140" cellpadding="0" border="1" rules="all" class="rpt_table" >
        <thead>
            <tr>
                <th width="40" rowspan="3">SL</th>
                <th rowspan="2" colspan="6">Order Details</th>
                <th colspan="4">Kniitng TNA Details</th>
                <th rowspan="2" colspan="6">Fab. Details as per Booking</th>
                <th rowspan="2" colspan="7">Program Details</th>
                <th rowspan="2" colspan="10">Fabric Details [As per Prog.]</th>
                <th rowspan="2" colspan="5">Yarn Issue Details</th>
                <th rowspan="2" colspan="2">Used Yarn</th>
                <th rowspan="2" colspan="4">Knitting Production</th>
                <th rowspan="2" colspan="3">Receive Details</th>
                <th rowspan="2" colspan="3">Issue Details</th>
                <th rowspan="2" colspan="2">Stock Details</th>
                <th rowspan="2" colspan="3">Location</th>
                <th rowspan="2" colspan="3">Balance Details</th>
            </tr>
            <tr>
                <th colspan="2">TNA</th>
                <th colspan="2">PLAN</th>
            </tr>
            <tr>
                <th width="100">Buyer Name</th>
                <th width="80">Job No</th>
                <th width="100">Order No</th>
                <th width="100">Style Name</th>
                <th width="80">Order Qnty(Pcs)</th>
                <th width="80">Shipment Date</th>
                
                <th width="80">Start Date</th>
                <th width="80">End Date</th>
                <th width="80">Start Date</th>
                <th width="80">End Date</th>
                
                <th width="110">Construction</th>
                <th width="110">Composition</th>
                <th width="100">Color Type</th>
                <th width="80">Grey Req. Qty.</th>
                
                <th width="80">Yarn Allocation</th>
                <th width="80">Allocation Balance</th>
              
                <th width="120">Program No./Booking No</th>
                <th width="80">Prog. Start Date</th>
                <th width="80">Prog. End Date</th>
                <th width="100">Source</th>
                <th width="80">Prog. Qty.</th>
                <th width="80">Prog. Balance</th>
                <th width="100">Requisition No</th>

                <th width="100">Y Count</th>
                <th width="100">Y. Brand</th>
                <th width="100">Lot</th>
                <th width="100">M/Dia</th>
                <th width="100">Grey Fabric Dia</th>
                <th width="100">F/Dia</th>
                <th width="100">GSM</th>
                <th width="100">Stich Length</th>
                <th width="100">Fabric Color</th>
                <th width="100">Color Range</th>

                <th width="80">Requisition Qnty.</th>
                <th width="80">Yarn Issue Qnty</th>
                <th width="80">Issue. Bal. Qnty</th>
                <th width="80">Issue Return</th>
                <th width="80">Returnable Bal</th>

                <th width="80">Yarn Brand</th>
                <th width="80">LOT</th>


                <th width="80">Knit Qnty</th>
                <th width="80">Knit Balance Qnty</th>
                <th width="80">Total Roll </th>
                <th width="80">Avg. Roll Wt.</th>

                <th width="80">Total Received Qnty</th>
                <th width="80">Receive Balance</th>
                <th width="80">Total Recv. Roll</th>

                <th width="80">Total Issue Qty.</th>
                <th width="80">Issue Balance</th>
                <th width="80">Total Iss. Roll</th>

                <th width="80">Stock Qty.</th>
                <th width="80">Roll Qty.</th>

                <th width="70">Rack</th>
                <th width="70">Shelf</th>
                <th width="70">DOH</th>
                
                <th width="90">Recv. Balance</th>
                <th width="80">Issue Balance</th>
                <th>Last Issue date</th>
            </tr>
        </thead>
    </table>
    
    
    
    <div style="width:5160px; overflow-y:scroll; max-height:450px;" id="scroll_body">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="5140" class="rpt_table" id="tbl_list_search">
       <? 
	   $i=1;$joBarPrint=0;
	   foreach($programmeDataArr as $po_id=>$data_rows_arr){
		   $joBarPrint++;
		  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
		  	$rowSpan=count($row_sub_tot_arr[$po_id])+count($data_rows_arr);
		  ?> 
        
            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style=" font-size:9pt;">
                <td width="40" rowspan="<? echo $rowSpan;?>"><? echo $i;?></td>
                <td width="100" rowspan="<? echo $rowSpan;?>"><p><? echo $buyer_arr[$buyer_arr[$po_id]];?></p></td>
                <td width="80" rowspan="<? echo $rowSpan;?>"><p><? echo $job_no_arr[$po_id];?></p></td>
                <td width="100" rowspan="<? echo $rowSpan;?>"><p><? echo $po_no_arr[$po_id];?></p></td>
                <td width="100" rowspan="<? echo $rowSpan;?>"><p><? echo $style_arr[$po_id];?></p></td>
                <td width="80" rowspan="<? echo $rowSpan;?>" align="right"><? echo number_format($po_pcs_qty_arr[$po_id],2);?></td>
                <td width="80" rowspan="<? echo $rowSpan;?>" align="center"><? echo change_date_format($ship_date_arr[$po_id]);?></td>
                
                <td width="80" rowspan="<? echo $rowSpan;?>" align="center"><? echo change_date_format($tna_arr[$po_id]['tna_start']);?></td>
                <td width="80" rowspan="<? echo $rowSpan;?>" align="center"><? echo change_date_format($tna_arr[$po_id]['tna_finish']);?></td>
                <td width="80" rowspan="<? echo $rowSpan;?>" align="center"><? echo change_date_format(max($plan_date_arr_str[$po_id]));?></td>
                <td width="80" rowspan="<? echo $rowSpan;?>" align="center"><? echo change_date_format(min($plan_date_arr_end[$po_id]));?></td>
                
               <? 
			   
			   
			  foreach($data_rows_arr as $key=>$program_rows_arr){ ?>
			   
                <td width="110" valign="middle" rowspan="<? echo count($program_rows_arr);?>"><? echo $construction_arr[$key];?></td>
                <td width="110" rowspan="<? echo count($program_rows_arr);?>" valign="middle"><? echo $copmposition_arr[$key];?></td>
                <td width="100" valign="middle" rowspan="<? echo count($program_rows_arr);?>"><p><? echo $color_type[$color_type_arr[$key]];?></p></td>
                <td width="80" rowspan="<? echo count($program_rows_arr);?>" align="right" valign="middle"><? 
				if($grey_qty_arr[$key]){
				echo number_format($grey_qty_arr[$key],2); $totReqQty+=$grey_qty_arr[$key];
				}
				else
				{
				 echo "N/A";	
				}
				 ?>
                 </td>
                 <td width="80" rowspan="<? echo count($program_rows_arr);?>" align="right" valign="middle"><? //echo number_format($alo_qty_arr[$po_id],2);?></td>
                 <td width="80" rowspan="<? echo count($program_rows_arr);?>" align="right" valign="middle"><? //echo number_format($grey_qty_arr[$key]-$alo_qty_arr[$po_id],2);
				 $orderWiseGreyQty[$po_id]+=$grey_qty_arr[$key]
				 ?></td>
			   
			   <? 	$tr=1; 
			   foreach($program_rows_arr as $program_no=>$row){
				   
				  $recQty=($rec_qty_arr[$program_no][$po_id]+$trn_in_qty_arr[$program_no][$po_id]);
				  $issQty=($iss_qty_arr[$program_no][$po_id]+$rec_rtn_qty_arr[$program_no][$po_id]+$trn_out_qty_arr[$program_no][$po_id])-$iss_rtn_qty_arr[$program_no][$po_id];
				  $daysOnHand=datediff("d",change_date_format($last_iss_date_arr[$program_no][$po_id]),date("Y-m-d")); 
				 if($programme_qty_arr[$program_no][$po_id]<$knit_pro_qty_arr[$program_no][$po_id]){
					 $bgColor="bgcolor='#8DCB62'";}else{$bgColor="";}
				 
				$tolerationPrograme=($grey_qty_arr[$key]/100);
				if(($grey_qty_arr[$key]+$tolerationPrograme)<$programme_qty_arr[$program_no][$po_id]){
					$pbgColor="bgcolor='#D00'";}else{$pbgColor="";}
				 
				 if($is_trn_in[$program_no][$po_id]==1){
					$trnbgColor="bgcolor='#FF0'";}else{$trnbgColor="";	 
				 }
				 
				 if($tr!=1){echo "<tr>";} 
				 ?>
                
                 
                 
                <td width="120" align="center" <? echo $trnbgColor;?>><? echo $program_no;?></td>
                <td width="80" align="center"><? echo change_date_format($row[start_date]);?></td>
                <td width="80" align="center"><? echo change_date_format($row[end_date]);?></td>
                <td width="100"><? 
					 echo $knitting_source[$row[knitting_source]];
 					//if($row[knitting_source]==1){echo $in_knitting_com_arr[$program_no][$po_id];}
					//else{echo $out_knitting_com_arr[$key];}
				?></td>
                <td width="80" align="right" <? echo $pbgColor;?>><? 
				if($is_trn_in[$program_no][$po_id]==1){ echo "N/A";}else{
				$programeQty=$programme_qty_arr[$program_no][$po_id];echo number_format($programeQty,2);$totProgrameQty+=$programeQty;$subTotProgrameQty+=$programeQty; }
				 ?></td>
                <?
                if($tr==1){
                echo '<td width="80" rowspan="'.count($programNOArr[$po_id][$key]).'" align="center" valign="middle">';
                	
						$programeQtyGroupSum=0;
						foreach($programNOArr[$po_id][$key] as $p_no){
							$programeQtyGroupSum+=$programme_qty_arr[$p_no][$po_id];
						}
						$ProgBalance=$grey_qty_arr[$key]-$programeQtyGroupSum;
						if($ProgBalance>0.001)echo number_format($ProgBalance,2);
						$totProgBalance+=$ProgBalance;$subTotProgBalance+=$ProgBalance;
                echo '</td>';
                }
				
                 ?>
                 
                <td width="100"><? echo $requisition_no_arr[$program_no][_req_no];?></td>

                <td width="100"><? echo $count_arr[$requisition_no_arr[$program_no][_count]];?></td>
                <td width="100"><? echo $brand_arr[$requisition_no_arr[$program_no][_brand]];?></td>
                <td width="100"><? echo $requisition_no_arr[$program_no][_lot];?></td>
                <td width="100" align="center"><? echo $row[machine_dia];?></td>
                <td width="100"><? echo $row[grey_dia];?></td>
                <td width="100"><p><? echo $row[fabric_dia];?></p></td>
                <td width="100"><p><? echo $row[gsm_weight];?></p></td>
                <td width="100"><p><? echo $row[stitch_length];?></td>
                <td width="100"><p>
					<? 
                    	$explodeColor=explode(',',$row[color_id]);
						$txtColor='';
						foreach($explodeColor as $color_id){
							if($txtColor==''){$txtColor = $color_library[$color_id];}
							else{$txtColor .= ', '.$color_library[$color_id];}
						}
						echo $txtColor;
						
                    ?>
                </p></td>
                <td width="100"><p><? echo $color_range[$row[color_range]];?></p></td>

                
                <td width="80" align="right"><? echo number_format($requisition_no_arr[$program_no][_qty],2);?></td>
                <td width="80" align="right"><? 
				if($is_trn_in[$program_no][$po_id]==1){ echo "N/A";}else{ $yarn_is_qty=$issue_qty_arr[$requisition_no_arr[$program_no][_req_no]]; echo number_format($yarn_is_qty,2); $subtotalyarn_is_qty+=$yarn_is_qty; $totalyarn_is_qty+=$yarn_is_qty; }?></td>
                <td width="80" align="right"><? 
				if($is_trn_in[$program_no][$po_id]==1){ echo "N/A";}else{ $yarn_is_blance=$requisition_no_arr[$program_no][_qty]-$issue_qty_arr[$requisition_no_arr[$program_no][_req_no]]; echo number_format($yarn_is_blance,2); $subtotalyarn_is_blance+=$yarn_is_blance; $totalyarn_is_blance+=$yarn_is_blance; }?></td>
                
                <td width="80" align="right"><? 
				echo number_format($yern_iss_ret_qty_arr[$requisition_no_arr[$program_no][_req_no]],2);
				//echo number_format($iss_rtn_qty_arr[$program_no][$po_id],2);?></td>
                <td width="80" align="right" title="No returnable bal to be calculated where issue bal is positive value"><? 
					if($yarn_is_blance<0){echo number_format($yarn_is_blance+$yern_iss_ret_qty_arr[$requisition_no_arr[$program_no][_req_no]],2);}else{ echo '<span style="color:#f00;">N/A</span>';}
				?></td>
                <td width="80"><? 
					//if($row[knitting_source]==1){echo $brand_arr[$in_brand_id_arr[$program_no][$po_id]];}
					//else{echo $brand_arr[$out_brand_arr[$key]];}
					echo $brand_arr[$in_brand_id_arr[$program_no][$po_id]];
				?></td>
                <td width="80"><? 
					//if($row[knitting_source]==1){echo $in_yarn_lot_arr[$program_no][$po_id];}
					//else{echo $out_lot_no_arr[$key];}
					echo $in_yarn_lot_arr[$program_no][$po_id];
				?></td>

                <td width="80" align="right" <? echo $bgColor;?>><? 
				if($is_trn_in[$program_no][$po_id]==1){ echo "N/A";}else{
				$knitQty=$knit_pro_qty_arr[$program_no][$po_id];echo number_format($knitQty,2); $subtotKnitQty+=$knitQty;$totKnitQty+=$knitQty;}?></td>
                <td width="80" align="right"><? 
				if($is_trn_in[$program_no][$po_id]==1){ echo "N/A";}else{ $KnitBl=$programme_qty_arr[$program_no][$po_id]-$knit_pro_qty_arr[$program_no][$po_id];
				echo number_format( $KnitBl,2); $subtotKnitBl+=$KnitBl; $totKnitBl+=$KnitBl; }?></td>
                <td width="80" align="right"><? 
				if($is_trn_in[$program_no][$po_id]==1){ echo "N/A";}else{ $Troll=$rol_qty_arr[$program_no][$po_id];
				echo $Troll; $subtotRoll+=$Troll; $totRoll+=$Troll; }?></td>
                <td width="80" align="right"><? 
				if($is_trn_in[$program_no][$po_id]==1){ echo "N/A";}else{
				echo number_format($knit_pro_qty_arr[$program_no][$po_id]/$rol_qty_arr[$program_no][$po_id],2);}?></td>

                <td width="80" align="right"><a href="javascript:openmypage_greyAvailable(<? echo $po_id;?>,'total_receiv_breakdown','<? echo $program_no;?>')"><? 
					//if($row[knitting_source]==3){
						$recQty=/*$knitQty+*/$recQty;
					//}
						echo number_format($recQty,2);
					$subrecQty+=$recQty; $totalrecQty+=$recQty;
				?></a></td>
                <td width="80" align="right"><?
				if($is_trn_in[$program_no][$po_id]==1){ echo "N/A";}else{ $RecBalance=$programme_qty_arr[$program_no][$po_id]-$recQty;
				 echo number_format($RecBalance,2); $subrecBalance+=$RecBalance; $totalrecBalance+=$RecBalance;}?></td>
                <td width="80" align="right"><? 
					if($row[knitting_source]==3){ $recRoll=$rec_roll_qty_arr[$program_no][$po_id]+$rol_qty_arr[$program_no][$po_id];
						 echo $recRoll;
						 $subrecRoll+=$recRoll; $totalrecRoll+=$recRoll;
						}
						else{
							$recRoll=$rec_roll_qty_arr[$program_no][$po_id];
							echo $recRoll;
							$subrecRoll+=$recRoll; $totalrecRoll+=$recRoll;
						}

				
				
				?></td>

                <td width="80" align="right"><? echo number_format($issQty,2); $SubissQty+=$issQty; $TotissQty+=$issQty; ?></td>
                <td width="80" align="right"><? $IsBl=$recQty-$issQty; echo number_format($IsBl,2); $SubIsBl+=$IsBl; $TotIsBl+=$IsBl; ?></td>
                <td width="80" align="right"><? $iS_r_Q=$iss_roll_qty_arr[$program_no][$po_id]; echo $iS_r_Q; $subiS_r_Q+=$iS_r_Q; $TotiS_r_Q+=$iS_r_Q; ?></td>

                <td width="80" align="right"><? $sTock_Qty=$recQty-$issQty; echo number_format($sTock_Qty,2); $subsTock_Qty+=$sTock_Qty; $totsTock_Qty+=$sTock_Qty; ?></td>
                <td width="80" align="right"><? $sTock_r_qty=$rec_roll_qty_arr[$program_no][$po_id]-$iss_roll_qty_arr[$program_no][$po_id]; echo $sTock_r_qty; $subsTock_r_qty+=$sTock_r_qty; $totsTock_r_qty+=$sTock_r_qty; ?></td>

                <td width="70"><p><? echo $rec_rack_arr[$program_no][$po_id];?></p></td>
                <td width="70"><p><? echo $self_rack_arr[$program_no][$po_id];?></p></td>
                <td width="70" align="right"><? echo $daysOnHand;?></td>
                
                <td width="80" align="right"><?  $blance_rve=$programme_qty_arr[$program_no][$po_id]-$recQty; echo number_format($blance_rve,2);   $subtotalblance_rve+=$blance_rve; $totalblance_rve+=$blance_rve; ?></td>
                <td width="80" align="right"><? $blance_issue=$recQty-$issQty; echo number_format($blance_issue,2); $subtotalblance_issue+=$blance_issue; $totalblance_issue+=$blance_issue; ?></td>
                <td align="center"><? echo change_date_format($last_iss_date_arr[$program_no][$po_id]);?></td>
            </tr>
            
            
            <? 
			$tr++;
			
			} 
			
				
				$order_subTotProgBalance+=$subTotProgBalance;
				$job_subTotProgBalance+=$subTotProgBalance;
				
				$order_subTotProgrameQty+=$subTotProgrameQty;
				$job_subTotProgrameQty+=$subTotProgrameQty;
				
			
			?>
           <tr bgcolor="#DDD">
            	<th colspan="10" align="right">Total :</th>
                <th align="right"><? echo number_format($subTotProgrameQty,2);$subTotProgrameQty=0;?></th>
                <th width="80" align="right"><? if($subTotProgBalance>0.001){echo number_format($subTotProgBalance,2);}$subTotProgBalance=0;?></th>
                <th width="80" colspan="12" align="right"></th>
                <th align="right"><? echo number_format($subtotalyarn_is_qty,2); $subtotalyarn_is_qty=0;?></th>
                <th width="80" align="right"><?  echo number_format($subtotalyarn_is_blance,2); $subtotalyarn_is_blance=0;?></th>
                <th width="80" align="right"></th>
                <th width="80" align="right"></th>
                <th width="80" align="right"></th>
                <th width="80" align="right"></th>
                <th width="80" align="right"><? echo number_format($subtotKnitQty,2); $subtotKnitQty=0;?></th>
                <th width="80" align="right"><? echo number_format($subtotKnitBl,2); $subtotKnitBl=0;?></th>
                <th width="80" align="right"><?  echo number_format($subtotRoll,2); $subtotRoll=0; ?></th>
                <th width="80" align="right"><? ?></th>
                <th width="80" align="right"><? echo number_format($subrecQty,2); $subrecQty=0; ?></th>
                <th width="80" align="right"><? echo number_format($subrecBalance,2); $subrecBalance=0; ?></th>
                <th width="80" align="right"><? echo number_format($subrecRoll,2); $subrecRoll=0;?></th>
                <th width="80" align="right"><? echo number_format($SubissQty,2); $SubissQty=0;?></th>
                <th width="80" align="right"><? echo number_format($SubIsBl,2); $SubIsBl=0;?></th>
                <th width="80" align="right"><? echo  number_format($subiS_r_Q,2); $subiS_r_Q=0;?></th>
                <th width="80" align="right"><? echo number_format($subsTock_Qty,2); $subsTock_Qty=0; ?></th>
                <th width="80" align="right"><? echo number_format($subsTock_r_qty,2); $subsTock_r_qty=0; ?></th>
                <th width="70"><p><? ?></p></th>
                <th width="70"><p><? ?></p></th>
                <th width="70" align="right"><? ?></th>
                <th  align="right" width="90"><? echo number_format($subtotalblance_rve,2);$subtotalblance_rve=0; ?></th>
                <th align="right" width="80"><? echo number_format($subtotalblance_issue,2);$subtotalblance_issue=0; ?></th>
                <th align="center"><? ?></th>
                
            </tr>
			
			<?
			}
			$totalNumberOfPO=count($numberOfPOInJob[$job_no_arr[$po_id]]);
			$jobWiseGreyQty+=$orderWiseGreyQty[$po_id];
			
			$job_alo_qty+=$alo_qty_arr[$po_id];
			$job_alo_qty_bal+=$orderWiseGreyQty[$po_id]-$alo_qty_arr[$po_id];

			$tot_alo_qty+=$alo_qty_arr[$po_id];
			$tot_alo_qty_bal+=$orderWiseGreyQty[$po_id]-$alo_qty_arr[$po_id];
			
			?>
        	<tr bgcolor="#FFFF99" id="osl<? echo $osl+=1; ?>" onClick="change_color('osl<? echo $osl; ?>','#FFFF99')">
            	<td colspan="14" align="right">Order Total</td>
            	<td align="right"><? echo number_format($orderWiseGreyQty[$po_id]);?></td>
            	<td align="right"><? echo number_format($alo_qty_arr[$po_id]);?></td>
            	<td align="right"><? echo number_format($orderWiseGreyQty[$po_id]-$alo_qty_arr[$po_id]);?></td>
                <td colspan="4"></td>
            	<td align="right"><? echo number_format($order_subTotProgrameQty);$order_subTotProgrameQty=0;?></td>
            	<td align="right"><? if($order_subTotProgBalance>0){echo number_format($order_subTotProgBalance);}$order_subTotProgBalance=0;?></td>
                <td colspan="36"></td>
            </tr>
            
            <? if($totalNumberOfPO==$joBarPrint){ $joBarPrint=0;?>
        	<tr bgcolor="#FFFF66" id="jsl<? echo $jsl+=1; ?>" onClick="change_color('jsl<? echo $jsl; ?>','#FFFF66')">
            	<td colspan="14" align="right">Job Total</td>
            	<td align="right"><? echo number_format($jobWiseGreyQty);$jobWiseGreyQty=0;?></td>
            	<td align="right"><? echo number_format($job_alo_qty);$job_alo_qty=0;?></td>
            	<td align="right"><? echo number_format($job_alo_qty_bal);$job_alo_qty_bal=0;?></td>
                <td colspan="4"></td>
            	<td align="right"><? echo number_format($job_subTotProgrameQty);$job_subTotProgrameQty=0;?></td>
            	<td align="right"><? if($job_subTotProgBalance>0.001){echo number_format($job_subTotProgBalance);}$job_subTotProgBalance=0;?></td>
                <td colspan="36"></td>
            </tr>
            
        <? 
			}
			$i++;
		} 
		?>
        
        </table>
    </div>
    <div style="width:5160px; overflow-y:scroll; max-height:450px;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="5140" class="rpt_table">
        	<tfoot>
                <th width="40"><? ?></th>
                <th width="100"><p><? ?></p></th>
                <th width="80"><p><? ?></p></th>
                <th width="100"><p><? ?></p></th>
                <th width="100"><p><? ?></p></th>
                <th width="80" align="right"><? ?></th>
                <th width="80" align="center"><? ?></th>
                
                <th width="80" align="center"><? ?></th>
                <th width="80" align="center"><? ?></th>
                <th width="80" align="center"><? ?></th>
                <th width="80" align="center"><? ?></th>
                
                
                
                <th width="110"><? ?></th>
                <th width="110"><? ?></th>
                <th width="100">Grand Total</th>
                <th width="80" align="right" ><? echo $totReqQty; ?></th>
                <th width="80" align="right" ><? echo number_format($tot_alo_qty); ?></th>
                <th width="80" align="right" ><? echo number_format($tot_alo_qty_bal); ?></th>
                <th width="120" align="center"><? ?></th>
                <th width="80" align="center"><? ?></th>
                <th width="80" align="center"><? ?></th>
                <th width="100"></th>
                <th width="80" align="right"><? echo number_format($totProgrameQty,2);?></th>
                <th width="80"><? if($order_subTotProgBalance>0.001)echo number_format($totProgBalance,2);?></th>
                <th width="100"><? ?></th>

                <th width="100"><? ?></th>
                <th width="100"><? ?></th>
                <th width="100"><? ?></th>
                <th width="100" align="center"><? ?></th>
                <th width="100"></th>
                <th width="100"><p><? ?></p></th>
                <th width="100"><p><? ?></p></th>
                <th width="100"><p><? ?></th>
                <th width="100"><p><? ?></p></th>
                <th width="100"><p><? ?></p></th>
                
                <th width="80" align="right"></th>
                <th width="80" align="right"><? echo number_format($totalyarn_is_qty,2);?></th>
                <th width="80" align="right"><? echo number_format($totalyarn_is_blance,2); ?></th>
                <th width="80" align="right"></th>
                <th width="80" align="right"></th>
                <th width="80" align="right"></th>
                <th width="80" align="right"></th>



                <th width="80" align="right"><? echo number_format($totKnitQty,2);?></th>
                <th width="80" align="right"><? echo number_format($totKnitBl,2); ?></th>
                <th width="80" align="right"><? echo number_format($totRoll,2); ?></th>
                <th width="80" align="right"><? ?></th>

                <th width="80" align="right"><? echo number_format($totalrecQty,2); ?></th>
                <th width="80" align="right"><? echo number_format($totalrecBalance,2);?></th>
                <th width="80" align="right"><? echo number_format($totalrecRoll,2); ?></th>
				
                <th width="80" align="right"><? echo number_format($TotissQty,2); ?></th>
                <th width="80" align="right"><? echo number_format($TotIsBl,2); ?></th>
                <th width="80" align="right"><? echo number_format($TotiS_r_Q,2); ?></th>
               

                <th width="80" align="right"><? echo number_format($totsTock_Qty,2); ?></th>
                <th width="80" align="right"><? echo number_format($totsTock_r_qty,2); ?></th>

                <th width="70"><p><? ?></p></th>
                <th width="70"><p><? ?></p></th>
                <th width="70" align="right"><? ?></th>
                
                <th width="90" align="right"><? echo number_format($totalblance_rve,2); ?></th>
                <th width="80" align="right"><? echo number_format($totalblance_issue,2); ?></th>
                <th align="center"><? ?></th>
            </tfoot>
        </table>
    </div>    
</fieldset>	
	
<?	

	foreach (glob("../../../ext_resource/tmp_report/$user_name*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$html=ob_get_contents();
	$name=time();
	$filename="../../../ext_resource/tmp_report/".$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,$html);
	$filename="../../ext_resource/tmp_report/".$user_name."_".$name.".xls";
	echo "$total_data####$filename";
	exit();


	
exit();	
}

if($action=="total_receiv_breakdown")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	
?>
<table border="1" class="rpt_table" rules="all" width="475" cellpadding="0" cellspacing="0">
    <thead>
        <th width="35">SL</th>
        <th width="150">Receive ID</th>
        <th width="120">Challan No</th>
        <th width="70">Receive Date</th>
        <th>Qty</th>
    </thead>
<?
	$knit_pro_qty_arr=array();
	/*$sql="select a.recv_number,a.receive_date,a.challan_no,a.booking_id,c.po_breakdown_id as order_id, c.quantity as grey_receive_qnty from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id =$po_id and a.booking_id=$data and a.knitting_source in(1,3)"; 
	$sql_result=sql_select($sql);
		foreach( $sql_result as $row )
		{
			 $key=$row[csf('recv_number')].'*'.$row[csf('receive_date')].'*'.$row[csf('challan_no')];
			 $knit_pro_qty_arr[$key]+=$row[csf('grey_receive_qnty')];
		}*/
	$sql="SELECT a.recv_number,a.receive_date,a.challan_no,a.booking_id,c.po_breakdown_id as order_id, c.quantity as grey_receive_qnty, c.id as prop_id, 1 as type 
	from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c 
	where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in (2) and c.entry_form in (2) and c.po_breakdown_id in($po_id) and b.trans_id>0 and a.knitting_source in(1,3) and a.booking_id=$data and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
	group by a.recv_number,a.receive_date,a.challan_no,a.booking_id,c.po_breakdown_id, c.quantity, c.id
	union all
	SELECT a.recv_number,a.receive_date,a.challan_no,a.booking_id,c.po_breakdown_id as order_id, c.quantity as grey_receive_qnty, c.id as prop_id, 2 as type 
	from inv_receive_master p, inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c 
	where p.id=a.booking_id and a.id=b.mst_id and b.id=c.dtls_id and p.entry_form=2 and a.entry_form in (22) and c.entry_form in (22) and c.po_breakdown_id in($po_id) and b.trans_id>0 and a.receive_basis=9 and a.knitting_source in(1,3) and p.booking_id=$data and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
	group by a.recv_number,a.receive_date,a.challan_no,a.booking_id,c.po_breakdown_id, c.quantity, c.id
	union all
	SELECT a.recv_number,a.receive_date,a.challan_no,a.booking_id,c.po_breakdown_id as order_id, c.quantity as grey_receive_qnty, c.id as prop_id, 3 as type 
	from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c 
	where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in (22) and c.entry_form in (22) and c.po_breakdown_id in($po_id) and b.trans_id>0 and a.receive_basis<>9 and a.knitting_source in(1,3) and a.booking_id=$data and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
	group by a.recv_number,a.receive_date,a.challan_no,a.booking_id,c.po_breakdown_id, c.quantity, c.id
	union all
	select a.recv_number,a.receive_date,a.challan_no, a.booking_id, b.po_breakdown_id as order_id,c.quantity as grey_receive_qnty, c.id as prop_id, 4 as type 
	from inv_receive_master a, pro_roll_details b, order_wise_pro_details c 
	where a.id = b.mst_id and B.ENTRY_FORM=58 and a.entry_form =58  and b.dtls_id=c.dtls_id and c.entry_form=58
	and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.booking_no = '$data' and  c.po_breakdown_id in($po_id) 
	group by  a.recv_number,a.receive_date,a.challan_no, a.booking_id, b.po_breakdown_id, c.quantity, c.id";

	/*
	union all
	SELECT a.recv_number,a.receive_date,a.challan_no,a.booking_id,c.po_breakdown_id as order_id, c.quantity as grey_receive_qnty, c.id as prop_id, 4 as type 
	from inv_receive_master p, pro_grey_prod_delivery_dtls q, inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c 
	where p.id=q.grey_sys_id and q.mst_id=a.booking_id and a.id=b.mst_id and b.id=c.dtls_id and p.entry_form=2 and q.entry_form=56 and a.entry_form in (58) and c.entry_form in (58) and c.po_breakdown_id in($po_id) and b.trans_id>0 and a.receive_basis=10 and a.knitting_source in(1,3) and p.booking_id=$data and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
	group by a.recv_number,a.receive_date,a.challan_no,a.booking_id,c.po_breakdown_id, c.quantity, c.id
	*/
	//echo $sql;

	$sql_result=sql_select($sql);
	
	$checkArr=array();
	foreach( $sql_result as $row )
	{
		if (!isset($checkArr[$row[csf('prop_id')]])) 
		{
			$key=$row[csf('recv_number')].'*'.$row[csf('receive_date')].'*'.$row[csf('challan_no')];
			$knit_pro_qty_arr[$key]+=$row[csf('grey_receive_qnty')];
		}
		$checkArr[$row[csf('prop_id')]]=$row[csf('prop_id')];
	}
	//echo "<pre>";print_r($knit_pro_qty_arr);	


	
	/*$sql="select a.knitting_source,a.recv_number,a.receive_date,a.challan_no,1 as no_of_roll,b.brand_id,b.yarn_lot,c.qnty,c.booking_no,c.po_breakdown_id from inv_receive_master a, pro_grey_prod_entry_dtls b,pro_roll_details c where a.id=c.mst_id and b.id=c.dtls_id and a.id=b.mst_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=$po_id and c.booking_no='$data' and a.knitting_source=3 and a.entry_form in(84,58) and a.receive_basis =10";*/
	$sql="SELECT a.knitting_source,a.recv_number,a.receive_date,a.challan_no,1 as no_of_roll,b.brand_id,b.yarn_lot,c.qnty,c.booking_no,c.po_breakdown_id from inv_receive_master a, pro_grey_prod_entry_dtls b,pro_roll_details c where a.id=c.mst_id and b.id=c.dtls_id and a.id=b.mst_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=$po_id and c.booking_no='$data' and a.knitting_source=3 and a.entry_form in(84) and a.receive_basis =10";
	
	//and a.receive_basis =11 and a.company_id=3 and a.entry_form =22	
	$sql_result=sql_select($sql);
		foreach( $sql_result as $row )
		{
			$key=$row[csf('recv_number')].'*'.$row[csf('receive_date')].'*'.$row[csf('challan_no')];
			$knit_pro_qty_arr[$key]+=$row[csf('qnty')];
		}

	$i=1;
	foreach($knit_pro_qty_arr as $key=>$qty){
		list($recv_number,$receive_date,$challan_no)=explode('*',$key);
		$bgcolor=($i%2==0)?"#F9FCCC":"#FFFFFF";
		
		?>
        <tr bgcolor="<? echo $bgcolor;?>">
            <td><? echo $i++;?></td>
            <td><? echo $recv_number;?></td>
            <td><? echo $challan_no;?></td>
            <td><? echo change_date_format($receive_date);?></td>
            <td align="right"><? echo $qty;?></td>
        </tr>
        <?
		
		
	}

?>
    <tfoot bgcolor="<? echo $bgcolor;?>">
        <th colspan="4"><strong>Total</strong></th>
        <th align="right"><? echo array_sum($knit_pro_qty_arr);?></th>
    </tfoot>
</table>
<?



exit();	
}



?>