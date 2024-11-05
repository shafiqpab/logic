<?

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
require_once('../../../includes/class3/class.conditions.php');
require_once('../../../includes/class3/class.reports.php');
require_once('../../../includes/class3/class.fabrics.php');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$user_name=$_SESSION['logic_erp']['user_id'];
//--------------------------------------------------------------------------------------------------------------------
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond order by buyer_name","id,buyer_name", 1, "- All Buyer -", $selected, "" );     	 
	exit();
}
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$yarn_count_library=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count"  );
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_job_no_search_list_view', 'search_div', 'knitting_requirment_report_for_period_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	$month_id=$data[5];
	//echo $month_id;
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	if($data[1]==0)
	{
	if ($_SESSION['logic_erp']["data_level_secured"]==1)
	{
	if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
	}
	else
	{
	$buyer_id_cond="";
	}
	}
	else
	{
	$buyer_id_cond=" and buyer_name=$data[1]";
	}
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";
	if($search_by==2) $search_field="style_ref_no"; else $search_field="job_no";
	//$year="year(insert_date)";
	if($db_type==0) $year_field="YEAR(insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year";
	else $year_field="";
	if($db_type==0)
	{
	if($year_id!=0) $year_cond=" and year(insert_date)=$year_id"; else $year_cond="";	
	}
	else if($db_type==2)
	{
	$year_field_con=" and to_char(insert_date,'YYYY')";
	if($year_id!=0) $year_cond="$year_field_con=$year_id"; else $year_cond="";	
	}
	//if($month_id!=0) $month_cond=" and month(insert_date)=$month_id"; else $month_cond="";
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_field from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond  order by job_no";
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","600","240",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','') ;
	exit(); 
} // Job Search end
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
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
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
	echo $data[1];
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
	$arr=array(0=>$company_arr,1=>$buyer_arr);
	$sql= "select b.id, $year_field a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $date_cond order by b.id, b.pub_shipment_date";
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Po No, Shipment Date", "80,130,50,60,130,130","760","220",0, $sql , "js_set_value", "id,po_number", "", 1, "company_name,buyer_name,0,0,0,0,0", $arr , "company_name,buyer_name,year,job_no_prefix_num,style_ref_no,po_number,pub_shipment_date", "",'','0,0,0,0,0,0,3','',1) ;
   exit(); 
}
if($action=="report_generate")
{
		$process = array( &$_POST );
		extract(check_magic_quote_gpc( $process )); 
		//$fabric_desc_library=return_library_array( "select id, fabric_description from wo_pre_cost_fabric_cost_dtls", "id", "fabric_description"  );
		//$fabric_gsm_library=return_library_array( "select id, gsm_weight from wo_pre_cost_fabric_cost_dtls", "id", "gsm_weight"  );
		
		//$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
		$sql_fab="select id,fabric_description as des,gsm_weight,lib_yarn_count_deter_id as deter_id from  wo_pre_cost_fabric_cost_dtls where status_active=1 and is_deleted=0";
		$data_fab=sql_select($sql_fab);
		$fabric_desc_library=array();$fabric_gsm_library=array();$fabric_determin_library=array();
		foreach($data_fab as $row)
		{
			$fabric_desc_library[$row[csf('id')]]=$row[csf('des')];
			$fabric_gsm_library[$row[csf('id')]]=$row[csf('gsm_weight')];
			$fabric_determin_library[$row[csf('id')]]=$row[csf('deter_id')];
		}

		$company_name= str_replace("'","",$cbo_company_name);
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
			$buyer_id_cond=" and a.buyer_name in (".str_replace("'","",$cbo_buyer_name).")";
		}
		
		//if(str_replace("'","",$txt_order)!="") $order_cond=" and b.id in(".str_replace("'","",$txt_order_no).")"; else $order_cond="";
		if(str_replace("'","",$txt_order_no)!="") $order_cond=" and b.po_number=$txt_order_no"; else $order_cond="";
		if(str_replace("'","",$txt_job_no)!="") $job_cond=" and a.job_no_prefix_num=".str_replace("'","",$txt_job_no).""; else $job_cond="";
	$date_cond="";
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
		 if($db_type==0)
			{
				
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
				$year_field="SUBSTRING_INDEX(a.insert_date, '-', 1) as year"; 
				if($cbo_year!=0) $year_cond=" and year(a.insert_date)=$cbo_year"; else $year_cond="";
			}
			else if($db_type==2)
			{
				
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
				$year_field="to_char(a.insert_date,'YYYY') as year";
				if($cbo_year!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year"; else $year_cond="";	
			}
		$date_cond=" and b.pub_shipment_date between '$start_date' and '$end_date'";
		}
	
		
		 
		 $sql_pro=sql_select("select id,item_description as item_desc,gsm,dia_width from product_details_master where status_active=1 and is_deleted=0");
		 $prod_arr=array();
		 foreach($sql_pro as $row)
		 {
			$prod_arr[$row[csf('id')]]['desc']=$row[csf('item_desc')];
			$prod_arr[$row[csf('id')]]['gsm']=$row[csf('gsm')];
			$prod_arr[$row[csf('id')]]['dia']=$row[csf('dia_width')];
		 }
		// print_r($prod_arr);
		 $poDataArray=sql_select("select b.id,b.pub_shipment_date, b.po_number,a.buyer_name,a.job_no_prefix_num,a.style_ref_no as style from  wo_po_break_down b,wo_po_details_master a where  a.job_no=b.job_no_mst and a.company_name=$company_name and b.status_active=1 and b.is_deleted=0 $buyer_id_cond  $order_cond $job_cond $date_cond $year_cond");// and a.season like '$txt_season'
		//$po_array=array(); 
		$all_po_id='';
		$job_array=array(); 
		foreach($poDataArray as $row)
		{
		$job_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
		$job_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
		$job_array[$row[csf('id')]]['po']=$row[csf('po_number')];
		$job_array[$row[csf('id')]]['ship_date']=$row[csf('pub_shipment_date')];
		if($all_po_id=="") $all_po_id=$row[csf('id')]; else $all_po_id.=",".$row[csf('id')]; 
		} //echo $all_po_id;die;
		$po_ids=count(array_unique(explode(",",$all_po_id)));
		//echo $all_po_id;
		$all_po_ids=chop($all_po_id,','); $poIds_cond="";
		//print_r($all_po_ids);
		if($db_type==2 && $po_ids>990)
		{
			$poIds_cond=" and (";
			$poIdsArr=array_chunk(explode(",",$all_po_ids),990);
			//print_r($gate_outIds);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$poIds_cond.=" c.po_breakdown_id  in($ids) or ";
			}
			$poIds_cond=chop($poIds_cond,'or ');
			$poIds_cond.=")";
		}
		else
		{
			$poIds_cond=" and  c.po_breakdown_id  in($all_po_id)";
		}
		//echo $poIds_cond;
		
		 /* $sql_data = "select  c.po_breakdown_id, d.id as prod_id,sum(c.quantity) as issue_qty, c.color_id,d.dia_width,d.gsm
			from inv_issue_master a, inv_grey_fabric_issue_dtls b,order_wise_pro_details c, product_details_master d 
			where a.id=b.mst_id and b.prod_id=d.id and b.id=c.dtls_id and a.entry_form=16 and c.entry_form=16 and a.company_id=$company_name $buyer_id_cond $poIds_cond and a.issue_basis=2 and a.is_deleted=0 and a.status_active=1  and b.is_deleted=0 and b.status_active=1  and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 group by c.po_breakdown_id, d.id,c.color_id,d.dia_width,d.gsm";
		$result_data=sql_select($sql_data);
		$z=0;$issue_qty_array=array();
		foreach($result_data as $row)
		{
			//$type=2;
			$issue_qty_array[$row[csf('po_breakdown_id')]].=$row[csf('po_breakdown_id')]."**".$row[csf('item_description')]."**".$row[csf('color_id')]."**".$row[csf('gsm')]."**".$row[csf('dia_width')]."**".$row[csf('issue_qty')]."**".$type.",";
			//$issue_qty_array[$row[csf('po_breakdown_id')]]=$row[csf('item_description')].",";
			
			$z++;
		}*/
		 $sql_plan="select c.company_id,c.buyer_id,c.po_breakdown_id as po_id,c.ship_date,c.fabric_description as fab_des,c.color_id,c.gsm,c.dia_width,c.no_of_batch  from pro_batch_plan c  where  c.company_id=$company_name and c.status_active=1 and c.is_deleted=0 $poIds_cond";
		$plan_data=sql_select($sql_plan);
		$batch_plan_arr=array();
		 foreach($plan_data as $row)
		 {
			$batch_plan_arr[$row[csf('po_id')]][$row[csf('fab_des')]][$row[csf('color_id')]][$row[csf('gsm')]][$row[csf('dia_width')]]['batch']+=$row[csf('no_of_batch')];	
		 }
		
		   $sql_data="select c.po_breakdown_id as po_id,d.id as prod_id,b.color_id,d.dia_width,d.gsm,d.detarmination_id as determin_id,
		 sum(CASE WHEN a.issue_basis=2 THEN c.quantity ELSE 0 END) AS indep_qty,
		 sum(CASE WHEN a.issue_basis!=2 THEN c.quantity ELSE 0 END) AS issue_qty
		 from inv_issue_master a,inv_grey_fabric_issue_dtls b,order_wise_pro_details c,product_details_master d 
		 where a.id=b.mst_id and b.prod_id=d.id and b.id=c.dtls_id  and a.entry_form=16 and c.entry_form=16 and a.company_id=$company_name $buyer_id_cond $poIds_cond  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 group by c.po_breakdown_id,d.id,b.color_id,d.dia_width,d.gsm,d.detarmination_id";
			$result_data=sql_select($sql_data);
			$issue_qty_arr_qty=array();
			foreach($result_data as $row)
			{
				$issue_qty_arr_qty[$row[csf('po_id')]][$row[csf('determin_id')]][$row[csf('color_id')]][$row[csf('gsm')]][$row[csf('dia_width')]]['indep_qty']=$row[csf('indep_qty')];	
				$issue_qty_arr_qty[$row[csf('po_id')]][$row[csf('determin_id')]][$row[csf('color_id')]][$row[csf('gsm')]][$row[csf('dia_width')]]['issue_qty']=$row[csf('issue_qty')];
			}
			
				if($db_type==0) $grp_concat="group_concat(a.challan_no) AS challan_no,";
				else if($db_type==2) $grp_concat="listagg(cast(a.challan_no as varchar2(4000)),',') within group (order by a.challan_no) AS challan_no,";
				 $sql_dtls="select $grp_concat a.knitting_company,c.po_breakdown_id as po_id,sum(c.quantity) as fab_recev,b.prod_id,b.fabric_description_id as deter_id, b.color_id, b.gsm, b.width,b.remarks from inv_receive_master a,pro_finish_fabric_rcv_dtls b,order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id  and c.entry_form=7 $poIds_cond  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.po_breakdown_id,b.prod_id,b.fabric_description_id, b.batch_id, b.color_id, b.gsm, b.width,a.knitting_company,b.remarks ";
				$res_data=sql_select($sql_dtls);
				$fin_recv_data_arr_qty=array();
				foreach($res_data as $row)
				{
					 $fin_recv_data_arr_qty[$row[csf('po_id')]][$row[csf('deter_id')]][$row[csf('color_id')]][$row[csf('gsm')]][$row[csf('width')]]['recv_qty']=$row[csf('fab_recev')];	
					 $fin_recv_data_arr_qty[$row[csf('po_id')]][$row[csf('deter_id')]][$row[csf('color_id')]][$row[csf('gsm')]][$row[csf('width')]]['recv_challan']=$row[csf('challan_no')];
					  $fin_recv_data_arr_qty[$row[csf('po_id')]][$row[csf('deter_id')]][$row[csf('color_id')]][$row[csf('gsm')]][$row[csf('width')]]['dye_factory']=$row[csf('knitting_company')];
					  $fin_recv_data_arr_qty[$row[csf('po_id')]][$row[csf('deter_id')]][$row[csf('color_id')]][$row[csf('gsm')]][$row[csf('width')]]['remarks']=$row[csf('remarks')];	
				}
			
			//print_r($fin_recv_data_arr_qty);
			  $sql_po="(select 1 as type,b.id as po_id,sum(c.cons) as cons,c.color_number_id,c.dia_width,c.pre_cost_fabric_cost_dtls_id as fab_dtls_id   from wo_po_details_master a, wo_po_break_down b 
					LEFT JOIN wo_pre_cos_fab_co_avg_con_dtls c on  c.po_break_down_id=b.id where a.job_no=b.job_no_mst   and a.company_name=$company_name $buyer_id_cond  $order_cond $job_cond $date_cond  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by b.id,c.color_number_id,c.dia_width,c.pre_cost_fabric_cost_dtls_id) 
union
(select  2 as type,c.po_breakdown_id as po_id,sum(c.quantity) as cons,c.color_id as color_number_id, d.dia_width as dia_width,d.id as fab_dtls_id 
			from inv_issue_master a, inv_grey_fabric_issue_dtls b,order_wise_pro_details c, product_details_master d 
			where a.id=b.mst_id and b.prod_id=d.id and b.id=c.dtls_id  and a.entry_form=16 and c.entry_form=16 and a.company_id=$company_name $buyer_id_cond $poIds_cond and a.issue_basis=2 and a.is_deleted=0 and a.status_active=1  and b.is_deleted=0 and b.status_active=1  and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 group by 	 c.po_breakdown_id,c.color_id,d.dia_width,d.id  ) order by po_id";
		$sql_data=sql_select($sql_po);
		
		ob_start();
		?>
        <div style="width:1750px;">
        <fieldset style="width:1750px;">
 	<table width="1750" cellspacing="0" cellpadding="0" border="0" rules="all" >
            <tr class="form_caption">
                <td colspan="19" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
            </tr>
            <tr class="form_caption">
                <td colspan="19" align="center">
                <strong>
				<? 
				if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
				{
				echo "From ".$start_date." To ".$end_date;
				}
				?>
                </strong>
                </td>
            </tr>
            <tr class="form_caption">
                <td colspan="19" align="center"><? echo $company_library[$company_name]; ?></td>
            </tr>
    </table>
      <table  class="rpt_table" width="1750" cellpadding="0" cellspacing="0" border="1" rules="all">
        <thead>
            <th width="30">SL</th>
            <th width="80">Ship Date</th>
            <th width="110">Buyer</th>
            <th width="110">Order No</th>
            <th width="150">Fabrics Desc.</th>
            <th width="100">Fabrics Color</th>
            <th width="70">F/ GSM</th>
            <th width="70">F/ Dia</th>
            <th width="70">Fab.  Req. Qty</th>
            <th width="70"><p>Grey Fab.  Del</p></th>
            <th width="90"><p>Grey Fab.  Balance</p></th>
            <th width="80">Fin Fab. Req. Qty</th>
            <th width="80"><p>Inhouse Qty</p></th>
            <th width="80">Inhouse Balance</th>
            <th width="80">Dyeing Process Loss %</th>
            <th width="80">Receive Challan No</th>
            <th width="100">Dye Factory</th>
            <th width="80">InHouse Status</th>
            <th width="100">Remarks</th>
            <th width="">Batch</th>
          
            
           
        </thead>
    </table>
       <div style="width:1750px; max-height:450px; overflow-y:scroll" id="scroll_body">
       <table class="rpt_table" width="1730" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">

        <?
         $total_grey_issue_qty=0;  $total_fab_qty=0;$total_grey_del_qty=0; $total_grey_balance=0; $total_fab_grey_knit_req=0;
         $i=1; $total_inhouse_recv_qty=0;$total_inhouse_balance=0;$total_fab_grey_finish_req=0;
		
		 $condition= new condition();
		if(str_replace("'","",$txt_job_no) !='')
		{
		  $condition->job_no_prefix_num("=$txt_job_no");
		}
		 if(str_replace("'","",$txt_order_no)!='')
		 {
			$condition->po_number("=$txt_order_no"); 
		 }
		 if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		 {
			$condition->country_ship_date(" between '$start_date' and '$end_date'");
		 }
		 $condition->init();
		
		$fabric= new fabric($condition);
		// echo $fabric->getQuery(); die;
		$fabric_costing_arr=$fabric->getQtyArray_by_OrderFabriccostidGmtscolorAndDiaWidth_knitAndwoven_greyAndfinish();
		// print_r($fabric_costing_arr);
         foreach($sql_data as $row)
         {
		
		//$po_data=explode(",",substr($issue_qty_array,0,-1));
		//print_r($po_data);
		
		
		if($row[csf('type')]==1)
		{
			
			$fab_grey_knit_req=$fabric_costing_arr['knit']['grey'][$row[csf('po_id')]][$row[csf('fab_dtls_id')]][$row[csf('color_number_id')]][$row[csf('dia_width')]];
		$fab_grey_finish_req=$fabric_costing_arr['knit']['finish'][$row[csf('po_id')]][$row[csf('fab_dtls_id')]][$row[csf('color_number_id')]][$row[csf('dia_width')]];
		
			$fab_des=$fabric_desc_library[$row[csf('fab_dtls_id')]];
			$f_gsm=$fabric_gsm_library[$row[csf('fab_dtls_id')]];
			 $determin_id=$fabric_determin_library[$row[csf('fab_dtls_id')]];
			//$f_dia=$fabric_gsm_library[$row[csf('fab_dtls_id')]];	
				$grey_issue_qty=$issue_qty_arr_qty[$row[csf('po_id')]][$determin_id][$row[csf('color_number_id')]][$f_gsm][$row[csf('dia_width')]]['issue_qty'];
				$fin_recv_qty=$fin_recv_data_arr_qty[$row[csf('po_id')]][$determin_id][$row[csf('color_number_id')]][$f_gsm][$row[csf('dia_width')]]['recv_qty'];
				$recv_challan=$fin_recv_data_arr_qty[$row[csf('po_id')]][$determin_id][$row[csf('color_number_id')]][$f_gsm][$row[csf('dia_width')]]['recv_challan'];	
				
				$grey_balance=$fab_grey_knit_req-$grey_issue_qty;
				$inhouse_balance_qty=$fab_grey_finish_req-$fin_recv_qty;
				$dyeing_process_per=(($grey_issue_qty-$fin_recv_qty)/$grey_issue_qty)*100;
				$dyeing_factory=$company_library[$fin_recv_data_arr_qty[$row[csf('po_id')]][$fab_des][$row[csf('color_number_id')]][$f_gsm][$row[csf('dia_width')]]['dye_factory']];
				$remarks=$fin_recv_data_arr_qty[$row[csf('po_id')]][$fab_des][$row[csf('color_number_id')]][$f_gsm][$row[csf('dia_width')]]['remarks'];
				
				if($fab_grey_finish_req>$fin_recv_qty)
				{
				
				 	$dyeing_status=$fab_grey_finish_req-$fin_recv_qty;
				 	$dyeing_status='Due '.number_format($dyeing_status).' KG';	
				}
				else if($fab_grey_finish_req==$fin_recv_qty)
				{
					$dyeing_status='In House';	
				}
				else if($fab_grey_finish_req<$fin_recv_qty)
				{
					$dyeing_status=$fab_grey_finish_req-$fin_recv_qty;
					$dyeing_status='Over '.number_format($dyeing_status).' KG';	
				}	
		}
		else
		{
			 $fab_des=$prod_arr[$row[csf('fab_dtls_id')]]['desc'];
			$f_gsm=$prod_arr[$row[csf('fab_dtls_id')]]['gsm'];	
			$grey_issue_qty=$issue_qty_arr_qty[$row[csf('po_id')]][$row[csf('fab_dtls_id')]][$row[csf('color_number_id')]][$f_gsm][$row[csf('dia_width')]]['indep_qty'];
			$fin_recv_qty=$fin_recv_data_arr_qty[$row[csf('po_id')]][$row[csf('fab_dtls_id')]][$row[csf('color_number_id')]][$f_gsm][$row[csf('dia_width')]]['recv_qty'];
			$recv_challan=$fin_recv_data_arr_qty[$row[csf('po_id')]][$row[csf('fab_dtls_id')]][$row[csf('color_number_id')]][$f_gsm][$row[csf('dia_width')]]['recv_challan'];	
			$grey_balance=$fab_grey_knit_req-$grey_issue_qty;
			$inhouse_balance_qty=$fab_grey_finish_req-$fin_recv_qty;
			$dyeing_process_per=(($grey_issue_qty-$fin_recv_qty)/$grey_issue_qty)*100;
			$dyeing_factory=$company_library[$fin_recv_data_arr_qty[$row[csf('po_id')]][$row[csf('fab_dtls_id')]][$row[csf('color_number_id')]][$f_gsm][$row[csf('dia_width')]]['dye_factory']];
			$remarks=$fin_recv_data_arr_qty[$row[csf('po_id')]][$row[csf('fab_dtls_id')]][$row[csf('color_number_id')]][$f_gsm][$row[csf('dia_width')]]['remarks'];
			if($fab_grey_finish_req>$fin_recv_qty)
			{
			
				$dyeing_status=$fab_grey_finish_req-$fin_recv_qty;	
				$dyeing_status='Due '.number_format($dyeing_status).' KG';
			}
			else if($fab_grey_finish_req==$fin_recv_qty)
			{
				$dyeing_status='In House';	
			}
			else if($fab_grey_finish_req<$fin_recv_qty)
			{
				$dyeing_status=$fab_grey_finish_req-$fin_recv_qty;	
				$dyeing_status='Over '.number_format($dyeing_status).' KG';	
			}		
		}
		
		$no_of_batch=$batch_plan_arr[$row[csf('po_id')]][$fab_des][$row[csf('color_number_id')]][$f_gsm][$row[csf('dia_width')]]['batch'];
		//echo $row[csf('po_id')];
		//print_r($po_data);
         if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		 //$job_array[$row[csf('po_id')]]['ship_date']
         ?>
         <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
            <td width="30"><? echo $i; ?></td>
            <td width="80"><div style="width:80px; word-wrap:break-word;"><? 
			
			echo change_date_format($job_array[$row[csf('po_id')]]['ship_date']); ?></div></td>
            <td width="110" ><div style="width:110px; word-wrap:break-word;"><?  echo $buyer_library[$job_array[$row[csf('po_id')]]['buyer']]; ?></div></td>
            <td width="110" title="<? echo $row[csf('job_no')];?>"><div style="width:110px; word-wrap:break-word;"><?  echo  $job_array[$row[csf('po_id')]]['po']; ?></div></td>
            <td width="150"><div style="width:150px; word-wrap:break-word;"><?  echo  $fab_des;  ?></div></td>
            <td width="100"><p><? echo  $color_library[$row[csf('color_number_id')]];  ?></p></td>
            <td width="70"><p><? echo  $f_gsm;  ?></p></td>
            <td width="70"><p><? echo  $row[csf('dia_width')];  ?></p></td>
            <td width="70" align="right"><p><? echo  number_format($fab_grey_knit_req,2); ?></p></td>
            <td width="70" align="right"><p><?  echo  number_format($grey_issue_qty,2); ?></p></td>
            <td width="90" align="right"><p><? echo  number_format($grey_balance,2);  ?></p></td>
            <td width="80" align="right"><p><? echo  number_format($fab_grey_finish_req,2);  ?></p></td>           
            <td width="80" align="right"><div style="width:80px; word-wrap:break-word;"><? echo  number_format($fin_recv_qty,2);  ?></div></td>
            <td width="80" align="right" title="fab Grey_finish Req-Inhouse Qty"><div style="width:80px; word-wrap:break-word;"><? echo  number_format($inhouse_balance_qty,2);  ?></div></td>
            <td width="80" align="center"><div style="width:80px; word-wrap:break-word;"><? echo number_format($dyeing_process_per,2);  ?></div></td>
            <td width="80"><div style="width:80px; word-wrap:break-word;"><? echo  $recv_challan;  ?></div></td>
            <td width="100"><div style="width:100px; word-wrap:break-word;"><? echo   $dyeing_factory;  ?></div></td>
            <td width="80"><div style="width:80px; word-wrap:break-word;"><? echo  $dyeing_status;  ?></div></td>
            <td width="100"><div style="width:100px; word-wrap:break-word;"><? echo  $remarks;  ?></div></td>
            <td width="" align="right"><p><?php echo $no_of_batch;?> </p></td>
            
            
        </tr>
       <?
       $i++;
	   $total_grey_del_qty+=$grey_issue_qty;
	   $total_inhouse_recv_qty+=$fin_recv_qty;
		$total_inhouse_balance+=$inhouse_balance_qty;
	   $total_grey_balance+=$grey_balance;
	   $total_fab_grey_knit_req+=$fab_grey_knit_req;
	   $total_fab_grey_finish_req+=$fab_grey_finish_req;
		
	   }
	  ?>
     </table>
     <table class="rpt_table" width="1730" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
    <tfoot>
    <tr>
        <th width="30">&nbsp;</th>
        <th width="80">&nbsp;</th>
        <th width="110">&nbsp;</th>
        <th width="110">&nbsp;</th>
        <th width="150">&nbsp;</th>
        <th width="100">&nbsp;</th>  
        <th width="70">&nbsp;</th> 
        <th width="70">&nbsp;</th>
        <th width="70"><? echo number_format($total_fab_grey_knit_req,2); ?> </th>
        <th width="70"><? echo number_format($total_grey_del_qty,2); ?></th>
        <th width="90"><? echo number_format($total_grey_balance,2); ?></th>
        <th width="80"><? echo number_format($total_fab_grey_finish_req,2); ?></th>
        <th width="80"><? echo number_format($total_inhouse_recv_qty,2); ?></th>
        <th width="80"><? echo number_format($total_inhouse_balance,2); ?></th>
        <th width="80">&nbsp;</th>
        <th width="80">&nbsp;</th>
        <th width="100">&nbsp;</th>
        <th width="80">&nbsp;</th>
        <th width="100">&nbsp;</th>
        <th>&nbsp;</th>
    </tr>
    </tfoot>
    </table>
    
    </div>
</fieldset>
</div>

<?

	
	exit();
}

?>