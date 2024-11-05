<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_id", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "" );   	 
	exit();
}

if($action=="load_drop_down_supplier")
{
	echo create_drop_down( "cbo_supplier_id", 120, "select a.id, a.supplier_name from lib_supplier a, lib_supplier_tag_company b where a.status_active =1 and a.is_deleted=0 and a.id=b.supplier_id and b.tag_company='$data' and a.id in (select supplier_id from lib_supplier_party_type where party_type in (21,25)) order by a.supplier_name","id,supplier_name", 1, "--Select Supplier--", $selected, "" ); 
	//parti type 4 to 21,25  	 
	exit();
}

if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	list($companyID,$buyer_name,$cbo_year_id)=explode('_',$data);
	?>
	<script>
	
	function js_set_value( job_id )
	{
		//alert(po_id)
		document.getElementById('txt_job_id').value=job_id;
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
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>', 'job_no_popup_search_list_view', 'search_div', 'wo_or_fabric_booking_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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

if ($action=="job_no_popup_search_list_view")
{
	extract($_REQUEST);
	list($companyID,$buyer_name,$search_type,$search_value,$cbo_year_id)=explode('**',$data);
	?>	
     <input type="hidden" id="txt_job_id" />
 <?
	if ($companyID==0) $company_id=""; else $company_id=" and company_name=$companyID";
	if ($buyer_name==0) $buyer_id=""; else $buyer_id=" and buyer_name=$buyer_name";
	if($db_type==0)
	{
		if ($cbo_year_id==0) $year_id_cond=""; else $year_id_cond=" and YEAR(insert_date)=$cbo_year_id";
	}
	elseif($db_type==2)
	{
		if ($cbo_year_id==0) $year_id_cond=""; else $year_id_cond=" and TO_CHAR(insert_date,'YYYY')=$cbo_year_id";
	}
	
	if($db_type==0)
	{
		$year=" YEAR(insert_date) as year";
	}
	elseif($db_type==2)
	{
		$year=" TO_CHAR(insert_date,'YYYY') as year";
	}
	
	if($search_type==1 && $search_value!=''){
		$search_con=" and job_no like('%$search_value')";	
	}
	else if($search_type==2 && $search_value!=''){
		$search_con=" and style_ref_no like('%$search_value%')";	
	}
	
	
	
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
	
	$sql= "select id, job_no, $year, job_no_prefix_num, style_ref_no, buyer_name, style_description from wo_po_details_master where status_active=1 and is_deleted=0 $company_id $buyer_id $search_con $year_id_cond order by id DESC";
	
	$arr=array(3=>$buyerArr);
	echo  create_list_view("list_view", "Job No,Year,Style Ref.,Buyer,Style Description", "70,70,130,140,170","630","320",0, $sql , "js_set_value", "id,job_no", "", 1, "0,0,0,buyer_name,0", $arr , "job_no_prefix_num,year,style_ref_no,buyer_name,style_description", "",'setFilterGrid("list_view",-1);','0,0,0,0,0','') ;
	
	exit();
}

if($action=="po_no_popup")
{
	echo load_html_head_contents("Order No Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	list($companyID,$buyer,$job)=explode('_',$data);
	?>
	<script>
		
	var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();
	 
	function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
	function js_set_value(id)
	{
		//alert (id)
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
		$('#txt_po_id').val( id );
		$('#txt_po_val').val( ddd );
	} 
    </script>
	</head>
	<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset>
	            <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
	            	<thead>
	                    <th>Buyer</th>
	                    <th>Search By</th>
	                    <th id="search_by_td_up" width="130">Please Enter Order No</th>
	                    <th>Shipment Date</th>
	                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
	                    <input type="hidden" name="hide_order_no" id="hide_order_no" value="" />
	                    <input type="hidden" name="hide_order_id" id="hide_order_id" value="" />
	                </thead>
	                <tbody>
	                	<tr>
	                        <td align="center">
	                        	 <? 
									echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer,"",0 );
								?>
	                        </td>                 
	                        <td align="center">	
	                    	<?
	                       		$search_by_arr=array(1=>"Order No",2=>"Style Ref",3=>"Job No");
								$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
								echo create_drop_down( "cbo_search_by", 80, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
	                        </td>     
	                        <td align="center" id="search_by_td">				
	                            <input type="text" style="width:80px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
	                        </td> 
	                        <td align="center">
	                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" readonly>To
	                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" readonly>
	                        </td>	
	                        <td align="center">
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+'<? echo $job; ?>'+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'po_no_popup_list_view', 'search_div', 'wo_or_fabric_booking_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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

if ($action=="po_no_popup_list_view")
{
	//echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $data;
	list($company,$buyer,$job,$search_type,$search_value,$start_date,$end_date)=explode('**',$data);
	//print_r ($data);
	
	?>	
     <input type="hidden" id="txt_po_id" />
     <input type="hidden" id="txt_po_val" />
     <?
	 if ($company==0) $company_name=""; else $job_num=" and a.company_name='$company'";
	 if ($buyer==0) $buyer_name=""; else $buyer_name=" and a.buyer_name='$buyer'";
	 if ($job=="") $job_num=""; else $job_num=" and b.job_no_mst='$job'";
	
	if($search_type==1 && $search_value!=''){
		$search_con=" and b.po_number like('%".trim($search_value)."')";	
	}
	elseif($search_type==2 && $search_value!=''){
		$search_con=" and a.style_ref_no like('%".trim($search_value)."')";		
	}
	elseif($search_type==3 && $search_value!=''){
		$search_con=" and b.job_no_mst like('%".trim($search_value)."')";		
	}
	
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
	else
	{
		$date_cond="";
	}
	
	
	$sql= "select b.id, b.po_number, b.job_no_mst, b.pub_shipment_date,c.gmts_item_id from wo_po_details_master a, wo_po_break_down  b, wo_po_details_mas_set_details c where a.job_no=b.job_no_mst and a.job_no=c.job_no and a.status_active=1 and a.is_deleted=0 $job_num $company_name $buyer_name $search_con $date_cond order by b.id Desc";
	
	$arr=array(3=>$garments_item);
	echo  create_list_view("list_view", "PO No.,Job No.,Pub Shipment Date,Item Name", "100,100,80,150","450","240",0, $sql , "js_set_value", "id,po_number", "", 1, "0,0,0,gmts_item_id", $arr , "po_number,job_no_mst,pub_shipment_date,gmts_item_id", "",'setFilterGrid("list_view",-1);','0,0,3,0','',1) ;
	exit();	 
}

if ($action=="report_generate")
{
	extract($_REQUEST);
	$cbo_company=str_replace("'","",$cbo_company_id);
	$cbo_buyer=str_replace("'","",$cbo_buyer_id);
	$cbo_supplier=str_replace("'","",$cbo_supplier_id);
	$year_id=str_replace("'","",$cbo_year_id);
	$job_year_id=str_replace("'","",$cbo_job_year_id);
	$wo_no=str_replace("'","",$txt_wo_no);
	$wo_id=str_replace("'","",$hidd_wo_id);
	
	$job_no=str_replace("'","",$txt_job_no);
	$hidd_job=str_replace("'","",$hidd_job_id);
	$po_no=str_replace("'","",$txt_po_no);
	$hidd_po=str_replace("'","",$hidd_po_id);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	
		
	if ($cbo_company==0) $company_id=""; else $company_id=" and a.company_id=$cbo_company";
	if ($cbo_buyer) $buyer_id=" and a.buyer_id=$cbo_buyer"; else $buyer_id=""; 
	if ($cbo_supplier) $supplier_cond=" and a.supplier_id=$cbo_supplier"; else $supplier_cond=""; 

	if ($wo_no) $wo_no_cond=" and a.booking_no_prefix_num='$wo_no'"; else $wo_no_cond="";  
	if ($year_id)  $wo_no_cond.=" and a.booking_year='$year_id'"; else $wo_no_cond.=""; 
	
	if ($job_no)  $job_num=" and b.job_no='$job_no'"; else $job_num="";
	if ($hidd_po) $po_id_cond=" and b.po_break_down_id in ( $hidd_po )"; else $po_id_cond="";  
	
	
	if($db_type==0)
	{
		if( $date_from=="" && $date_to=="" ) $booking_date_cond=""; else $booking_date_cond=" and a.booking_date between '".$date_from."' and '".$date_to."'";
	}
	if($db_type==2)
	{

		if( $date_from=="" && $date_to=="" ) $booking_date_cond=""; else $booking_date_cond=" and a.booking_date between '".change_date_format($date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($date_to,'dd-mm-yyyy','-',1)."'";
	}

	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$supplierArr=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name");	
	$buyerArr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$style_ref_no_arr=return_library_array( "select job_no, style_ref_no from wo_po_details_master", "job_no", "style_ref_no");
	$color_arr=return_library_array( "select id,color_name from lib_color ", "id", "color_name");

	if($db_type==0)
	{
		$select_program = "group_concat(b.program_no) as program_no";
		$select_po_id = "group_concat(b.po_break_down_id) as po_break_down_id";
		$gmts_color_id = "group_concat(b.gmts_color_id)";
		$item_color_id = "group_concat(b.fabric_color_id) ";
		$fin_dia = "group_concat(b.dia_width)";
		$fin_gsm = "group_concat(b.fin_gsm)";
	}
	else
	{
		$select_program = "listagg(b.program_no,',') within group (order by b.program_no) as program_no";
		$select_po_id = "listagg(b.po_break_down_id,',') within group (order by b.po_break_down_id) as po_break_down_id";
		$gmts_color_id = "listagg(b.gmts_color_id,',') within group (order by b.gmts_color_id)";
		$item_color_id = "listagg(b.fabric_color_id,',') within group (order by b.fabric_color_id)";
		$fin_dia = "listagg(b.dia_width,',') within group (order by b.dia_width)";
		$fin_gsm = "listagg(b.fin_gsm,',') within group (order by b.fin_gsm)";
	}
	$sql="select a.booking_no, a.entry_form, a.pay_mode, a.booking_date, a.is_approved, $select_program, b.job_no, $select_po_id ,a.supplier_id, a.buyer_id, sum(b.wo_qnty) as wo_qnty, b.rate, $gmts_color_id as gmts_color_id, $item_color_id as item_color_id, $fin_dia as fin_dia, $fin_gsm  as fin_gsm, d.color_type_id, d.construction, d.composition
	from wo_booking_mst a, wo_booking_dtls b,  wo_pre_cost_fab_conv_cost_dtls c, wo_pre_cost_fabric_cost_dtls d
	where a.booking_no = b.booking_no and b.pre_cost_fabric_cost_dtls_id = c.id and c.fabric_description = d.id and a.booking_type = 3 and a.item_category = 12 and a.process = 31 and a.status_active=1  and b.status_active=1  $booking_date_cond $company_id $buyer_id $wo_no_cond $job_num $po_id_cond $supplier_cond
	group by  a.booking_no, a.entry_form, a.pay_mode, a.booking_date, b.job_no, a.buyer_id, a.is_approved, a.supplier_id, b.rate, d.color_type_id, d.construction, d.composition";
		
	$sql_result=sql_select($sql); 
	$job_arr = array();
	foreach ($sql_result as $val) 
	{
		$job_arr[$val[csf("job_no")]] = $val[csf("job_no")];
	}

	$job_nos = "'".implode("','", array_filter($job_arr))."'";
	if($job_nos=="") $job_nos=0;
	$jobCond = $job_nos_cond = ""; 
	$job_nos_arr=explode(",",$job_nos);
	if($db_type==2 && count($job_nos_arr)>999)
	{
		$job_nos_chunk=array_chunk($job_nos_arr,999) ;
		foreach($job_nos_chunk as $chunk_arr)
		{
			$jobCond.=" a.job_no in(".implode(",",$chunk_arr).") or ";	
		}
		$job_nos_cond.=" and (".chop($jobCond,'or ').")";			
	}
	else
	{ 	
		$job_nos_cond=" and a.job_no in($job_nos)";
	}

	$job_sql = sql_select("select a.job_no, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no = b.job_no_mst and b.status_active = 1 and a.status_active = 1 $job_nos_cond");
	foreach ($job_sql as $value) 
	{
		$style_ref_no_arr[$value[csf("job_no")]] = $value[csf("style_ref_no")];
		$order_no_arr[$value[csf("id")]] = $value[csf("po_number")];
	}
	unset($job_sql);
		ob_start();
		?>
		<fieldset>
			<table width="1900" cellspacing="0" >
				<tr class="form_caption" style="border:none;">
					<td align="center" style="border:none; font-size:18px;" colspan="20">
						<? echo $company_library[str_replace("'","",$cbo_company_id)]; ?>                                
					</td>
				</tr>
				<tr class="form_caption" style="border:none;">
					<td align="center" style="border:none;font-size:16px; font-weight:bold" colspan="20"> <? echo $report_title; ?></td>
				</tr>
				<tr class="form_caption" style="border:none;">
					<td align="center" style="border:none;font-size:12px; font-weight:bold" colspan="20"> <? if( $date_from!="" && $date_to!="" ) echo "From ".change_date_format($date_from)." To ".change_date_format($date_to);?></td>
				</tr>
			</table>
			<table width="1900" cellspacing="0" border="1" class="rpt_table" rules="all">
				<thead>
					<tr>
                        <th width="30">SL</th>    
                        <th width="70">W/O Date</th>
                        <th width="100">W/O No</th>
                        <th width="150">Program No</th>
                        <th width="100">Job No.</th>
                        <th width="100">Buyer</th>
                        <th width="100">Style Ref.</th>
                        <th width="100">Order No</th>
                        <th width="110">Sub Con.Party Name</th>
                        <th width="80">Color/Category</th>
                        
                        <th width="90">Gmts Color </th>
                        <th width="90">Item Color</th>
                        <th width="60">Fin Dia</th>
                        <th width="60">Fin GSM</th>
                        <th width="120">Fabric Type</th>
                        <th width="180">Composition</th>
                        <th width="90">Booking Qty(kg</th>
                        <th width="70">Rate(tk)</th>
                        <th width="100">Total Amount</th>
                        <th>Approval Status</th>
					</tr>
				</thead>
			</table>
			<div style="max-height:350px; overflow-y:scroll; width:1900px" id="scroll_body" >
				<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="1880" rules="all" id="table_body" >
				<?
				$i=1;
				foreach($sql_result as $row)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$supplier_str=""; $gmts_color=""; $item_color=""; $fin_dia=""; $fin_gsm="";
					if($row[csf("pay_mode")]==3 || $row[csf("pay_mode")]==5) $supplier_str=$company_library[$row[csf("supplier_id")]]; else  $supplier_str=$supplierArr[$row[csf("supplier_id")]];
					$ex_gmts_color=explode(',',$row[csf("gmts_color_id")]);
					foreach($ex_gmts_color as $gmts_colorId)
					{
						if($gmts_color=="")	$gmts_color=$color_arr[$gmts_colorId]; else $gmts_color.=','.$color_arr[$gmts_colorId];
					}
					$gmts_color=implode(",",array_filter(array_unique(explode(',',$gmts_color))));
					
					$ex_item_color=explode(',',$row[csf("item_color_id")]);
					foreach($ex_item_color as $item_colorId)
					{
						if($item_color=="")	$item_color=$color_arr[$item_colorId]; else $item_color.=','.$color_arr[$item_colorId];
					}
					$item_color=implode(",",array_filter(array_unique(explode(',',$item_color))));
					
					$fin_dia=implode(",",array_filter(array_unique(explode(',',$row[csf("fin_dia")]))));
					$fin_gsm=implode(",",array_filter(array_unique(explode(',',$row[csf("fin_gsm")]))));
					
					if($row[csf("entry_form")]=="") $row[csf("entry_form")]=0;
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
						<td width="30"><? echo $i;?></td>	
						<td width="70"><? echo change_date_format($row[csf("booking_date")]); ?></td>
						<td width="100" style="word-break:break-all"><a href="##" onClick="generate_print('<? echo $row[csf("booking_no")].'__'.$row[csf("entry_form")];?>');"><? echo $row[csf("booking_no")]; ?></a></td>
						<td width="150" style="word-break:break-all" align="center"><? echo implode(",",array_filter(array_unique(explode(",", $row[csf("program_no")])))); ?>&nbsp;</td>
						<td width="100" style="word-break:break-all"><? echo $row[csf("job_no")]; ?></td>
						<td width="100" style="word-break:break-all"><? echo $buyerArr[$row[csf("buyer_id")]]; ?>&nbsp;</td>
						<td width="100" style="word-break:break-all"><? echo $style_ref_no_arr[$row[csf("job_no")]]; ?></td>
						<td width="100" style="word-break:break-all"><? 
									$po_number="";
									foreach (array_filter(array_unique(explode(",", $row[csf("po_break_down_id")]))) as $val) 
									{
										$po_number .= $order_no_arr[$val].",";
									}
									echo chop($po_number,","); 
								?>
						</td>
						<td width="110" style="word-break:break-all"><? echo $supplier_str; ?></td>
						<td width="80" style="word-break:break-all"><? echo $color_type[$row[csf("color_type_id")]]; ?></td>
                        
                        <td width="90" style="word-break:break-all"><? echo $gmts_color; ?></td>
                        <td width="90" style="word-break:break-all"><? echo $item_color; ?></td>
                        <td width="60" style="word-break:break-all"><? echo $fin_dia; ?></td>
                        <td width="60" style="word-break:break-all"><? echo $fin_gsm; ?></td>
                        
						<td width="120" style="word-break:break-all"><? echo $row[csf("construction")]; ?></td>
						<td width="180" style="word-break:break-all"><? echo $row[csf("composition")]; ?></td>
						<td width="90" align="right"><? echo number_format($row[csf("wo_qnty")],2); ?></td>
						<td width="70" align="right"><? echo number_format($row[csf("rate")],4); ?></td>
						<td width="100" align="right"><? echo number_format($row[csf("wo_qnty")]*$row[csf("rate")],2); ?></td>
						<td style="word-break:break-all"><? echo $approval_type_arr[$row[csf("is_approved")]]; ?></td>
					</tr>
					<?
					$i++;
					$total_wo_qnty +=  $row[csf("wo_qnty")];
					$total_wo_amt +=  $row[csf("wo_qnty")]*$row[csf("rate")];
				}
				?>
				</table>
            </div>
            <table rules="all" class="rpt_table" width="1900" cellspacing="0" cellpadding="0" border="1">
                <tfoot>
                    <tr>
                        <th width="30">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="150">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        
                        <th width="90">&nbsp;</th>
                        <th width="90">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        
                        <th width="120">&nbsp;</th>
                        <th width="180">Total : </th>
                        <th width="90" align="right" style="word-break:break-all" id="value_total_wo_qnty"><? echo number_format($total_wo_qnty,2,".",""); ?></th>
                        <th width="70">&nbsp;</th>
                        <th width="100" align="right" style="word-break:break-all" id="value_total_wo_amnt" title="<? echo $total_wo_amt;?>"><? echo number_format($total_wo_amt,2,".",""); ?></th>
                        <th>&nbsp;</th>
                    </tr>
                </tfoot>
            </table>
		</fieldset>
		<?
	
	$html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) 
    {
    	@unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename"; 
	exit();
}
?>
