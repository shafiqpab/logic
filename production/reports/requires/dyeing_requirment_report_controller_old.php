<?

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
//--------------------------------------------------------------------------------------------------------------------
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
//$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
//$yarn_count_library=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count"  );
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond order by buyer_name","id,buyer_name", 1, "- All Buyer -", $selected, "" );     	 
	exit();
}
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_job_no_search_list_view', 'search_div', 'dyeing_requirment_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
		
		
		if(str_replace("'","",$txt_season)!="") $season_cond=" and UPPER(a.season) like '%".strtoupper(str_replace("'","",$txt_season))."%'"; else $season_cond="";
		if(str_replace("'","",$txt_order)!="") $order_cond=" and b.id in(".str_replace("'","",$txt_order).")"; else $order_cond="";
		if(str_replace("'","",$txt_color)!="") $color_cond=" and b.fabric_color_id in(select id from lib_color where color_name=UPPER($txt_color) )"; else $color_cond="";
		
		if(str_replace("'","",$txt_gsm)!="" && str_replace("'","",$txt_gsm_to)!="")
		{
			if(str_replace("'","",$txt_gsm)!="") 
			{
				$gsm_cond=" and d.gsm_weight between '".str_replace("'","",$txt_gsm)."' and '".str_replace("'","",$txt_gsm_to)."'";
				$gsm_cond1=" and b.gsm_weight between '".str_replace("'","",$txt_gsm)."' and '".str_replace("'","",$txt_gsm_to)."'";
			}
			else 
			{
				$gsm_cond="";
				$gsm_cond1="";
			}
		}
		
		
	
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
		 if($db_type==0)
			{
				
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
			}
			else if($db_type==2)
			{
				
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
			}
		$date_cond=" and a.booking_date between '$start_date' and '$end_date'";
		$date_cond1=" and c.booking_date between '$start_date' and '$end_date'";
		}
	
		
	ob_start();
?>

		 <?
		//echo "select b.id from wo_po_details_master a, wo_po_break_down b,wo_booking_mst c,wo_booking_dtls d where a.job_no=b.job_no_mst and a.job_no=c.job_no and a.job_no=d.job_no  and b.id=d.po_break_down_id and a.company_name=$company_name $buyer_id_cond $season_cond $order_cond  $gsm_cond $date_cond1   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 group by b.id order by b.id";
		 $po_id_array=array();
		  $sql_po_id=sql_select("select b.id from wo_po_details_master a, wo_po_break_down b,wo_booking_mst c,wo_booking_dtls d where a.job_no=b.job_no_mst and a.job_no=c.job_no and a.job_no=d.job_no  and b.id=d.po_break_down_id and a.company_name=$company_name $buyer_id_cond $season_cond $order_cond  $gsm_cond $date_cond1   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 group by b.id order by b.id");
		  foreach($sql_po_id as $row_po_id)
		  {
			$po_id_array[$row_po_id[csf('id')]]=$row_po_id[csf('id')]; 
		  }
		  
		  $po_id=array_chunk($po_id_array,1000, true);
		  
		  if($order_cond=="")
		  {
		   //$po_cond_in="";
		   $order_cond="";
		   $ji=0;
		   foreach($po_id as $key=> $value)
		   {
			   if($ji==0)
			   {
				$order_cond=" and b.id in(".implode(",",$value).")"; 
			   }
			   else
			   {
				$order_cond.=" or b.id in(".implode(",",$value).")";
			   }
			   $ji++;
		   }
		  }
		  
		  
		   $po_cond_in="";
		   $ji=0;
		   foreach($po_id as $key=> $value)
		   {
			   if($ji==0)
			   {
				$po_cond_in=" and b.po_break_down_id in(".implode(",",$value).")"; 
			   }
			   else
			   {
					$po_cond_in.=" or b.po_break_down_id in(".implode(",",$value).")";
			   }
			   $ji++;
		   }
		 
		 
	$sql_po=sql_select("select a.buyer_name,a.job_no,a.season,b.id,b.po_number,c.item_number_id,c.country_id,c.color_number_id,c.size_number_id,c.order_quantity ,c.plan_cut_qnty ,d.id as pre_cost_dtls_id,d.body_part_id,d.construction,d.composition,d.fab_nature_id,d.color_type_id,d.fabric_source,d.gsm_weight,d.width_dia_type   from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no  and b.id=c.po_break_down_id and a.company_name=$company_name $buyer_id_cond $season_cond $order_cond  $gsm_cond  and d.fab_nature_id=2 and d.fabric_source=1  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 order by b.id,pre_cost_dtls_id");
	
	$data_arr=array();
	foreach($sql_po as $sql_po_row)
	{
		//$data_arr[po_id][$sql_po_row[csf('id')]]=$sql_po_row[csf('id')];
		$data_arr[width_dia_type][$sql_po_row[csf('pre_cost_dtls_id')]]=$sql_po_row[csf('width_dia_type')];
		$data_arr[body_part_id][$sql_po_row[csf('pre_cost_dtls_id')]]=$sql_po_row[csf('body_part_id')];
		//$data_arr[gsm_weight][$sql_po_row[csf('pre_cost_dtls_id')]]=$sql_po_row[csf('gsm_weight')];
		//$data_arr[color_type][$sql_po_row[csf('pre_cost_dtls_id')]]=$sql_po_row[csf('color_type_id')];
	}
	
   /*$po_id=array_chunk($po_id_array,1000, true);
   $po_cond_in="";
   $ji=0;
   foreach($po_id as $key=> $value)
   {
	   if($ji==0)
	   {
	    $po_cond_in=" and b.po_break_down_id in(".implode(",",$value).")"; 
	   }
	   else
	   {
		    $po_cond_in.=" or b.po_break_down_id in(".implode(",",$value).")"; 
	   }
	   $ji++;
   }*/
  // $txt_order_no_id=implode(',',$data_arr[po_id]);

	
	$nameArray=sql_select("
	select
	a.booking_date,
	b.pre_cost_fabric_cost_dtls_id,
	b.job_no,
	b.po_break_down_id,
	b.booking_no,
	b.grey_fab_qnty,
	b.color_type,
	b.construction,
	b.copmposition,
	b.gsm_weight,
	b.dia_width,
	b.fabric_color_id,
	b.booking_type,
	b.is_short
FROM
	wo_booking_mst a,
	wo_booking_dtls b
WHERE
	a.job_no=b.job_no and
    a.booking_no=b.booking_no 
	$po_cond_in  $gsm_cond1 $date_cond $color_cond and a.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	order by b.dia_width");
	    $dia_total_array=array();
		$report_data_array=array();
		foreach($nameArray as $rows)
		{
			if($data_arr[body_part_id][$rows[csf('pre_cost_fabric_cost_dtls_id')]]==4)
			{
				$report_data_array[$rows[csf('fabric_color_id')]][$rows[csf('color_type')]][$data_arr[width_dia_type][$rows[csf('pre_cost_fabric_cost_dtls_id')]]][$rows[csf('gsm_weight')]][rib_qnty]+=$rows[csf('grey_fab_qnty')];

			}
			else
			{
			     $dia_total_array[$rows[csf('dia_width')]]+=$rows[csf('grey_fab_qnty')];
				 $report_data_array[dia_width][$rows[csf('dia_width')]]=$rows[csf('dia_width')];
				 $report_data_array[$rows[csf('fabric_color_id')]][$rows[csf('color_type')]][$data_arr[width_dia_type][$rows[csf('pre_cost_fabric_cost_dtls_id')]]][$rows[csf('gsm_weight')]][$rows[csf('dia_width')]][grey_fab_qnty]+=$rows[csf('grey_fab_qnty')];
			}
			
		}
		$tble_width=(count($report_data_array[dia_width])*70)+1000;
		?>
        
        <fieldset style="width:<? echo $tble_width?>px;">
 	    <table width="<? echo $tble_width?>" cellspacing="0" cellpadding="0" border="0" rules="all" >
            <tr class="form_caption">
                <td colspan="21" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
            </tr>
            <tr class="form_caption">
                <td colspan="21" align="center">
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
                <td colspan="21" align="center"><? echo $company_library[$company_name]; ?></td>
            </tr>
        </table>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tble_width?>" class="rpt_table" >
        <thead>
        <tr>
            <th width="40" rowspan="2">SL</th>
            <th width="100" rowspan="2">Fabric Color</th>
            <th width="100" rowspan="2">Color Type</th>
            <th width="100" rowspan="2">Dia/Width Type</th>
            <th width="50" rowspan="2">GSM</th>
           
            <th width="100" colspan="<? echo count($report_data_array[dia_width]) ?>">Dia Wise Qty(kg)</th>
            <th width="100" rowspan="2">Rib</th>
            <th width="100" rowspan="2">Dia</th>
            <th width="100" rowspan="2">Total</th>
            <th width="" rowspan="2">Lab Dip</th>
           </tr>
           <tr>
            <?
			foreach($report_data_array[dia_width] as $dia_width_id=>$dia_width_value)
			{
			?>
            <th width="70"><? echo $dia_width_id; ?></th>
            <?
			}
            ?>
           </tr>
        </thead>
    </table>
    <div style="width:<? echo $tble_width+17 ?>px; overflow-y:scroll; max-height:450px;" id="scroll_body">
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tble_width?>" class="rpt_table" id="tbl_list_search">
        
        <?
		$rib_total=0;
         $total_booking_qty=0;
         $ii=1; 
		 foreach($report_data_array as $fabric_color_id=>$fabric_color_value)
         {
		 foreach($report_data_array[$fabric_color_id] as $color_type_id=>$color_type_value)
         {
		 foreach($report_data_array[$fabric_color_id][$color_type_id] as $dia_width_type_id=>$dia_width_type_id_value)
         {
		 foreach($report_data_array[$fabric_color_id][$color_type_id][$dia_width_type_id] as $gsm_weight_id=>$gsm_weight_value)
         {
         if($ii%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
         ?>
         <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $ii; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $ii; ?>"> 
            <td width="40"><? echo $ii; ?></td>
             <td width="100"><p><? echo $color_library[$fabric_color_id]; ?></p></td>
            <td width="100"><p><? echo $color_type[$color_type_id]; ?></p></td>
            <td width="100" align="center"><p><? echo $fabric_typee[$dia_width_type_id]; ?></p></td>
            <td width="50" align="center"><p><?  echo $gsm_weight_id; ?></p></td>
            <?
			$row_total=0;
			foreach($report_data_array[dia_width] as $dia_width_id=>$dia_width_value)
			{
			?>
            <td width="70" align="right"><p>
			<? 
			if($gsm_weight_value[$dia_width_id][grey_fab_qnty]>0)
			{
			echo number_format($gsm_weight_value[$dia_width_id][grey_fab_qnty],2);
			}
			$row_total+=$gsm_weight_value[$dia_width_id][grey_fab_qnty]; 
			$total_booking_qty+=$gsm_weight_value[$dia_width_id][grey_fab_qnty]; 
			?>
            </p>
            </td>
            <?
			}
			?>
            <td width="100" align="right"><? 
			$rib_qty= $report_data_array[$fabric_color_id][$color_type_id][$dia_width_type_id][$gsm_weight_id][rib_qnty]; 
			echo  number_format($rib_qty,2);
			$rib_total+= $report_data_array[$fabric_color_id][$color_type_id][$dia_width_type_id][$gsm_weight_id][rib_qnty];
			?>
            </td>
            <td width="100" align="right"></td>
            <td width="100" align="right"><? echo number_format($row_total+$rib_qty,2); ?></td>
            <td width="" align="right"></td>
            
        </tr>
       <?
       $ii++; 
	   }
	   }
	   }
	   }
	   ?>
       <tfoot>
            <th  colspan="5" align="left"> Grand Total:</th>
            <?
			foreach($report_data_array[dia_width] as $dia_width_id=>$dia_width_value)
			{
			?>
            <th width="70"><? echo number_format($dia_total_array[$dia_width_id],2);?></th>
            <?
			}
			?>
            <th width="100"><? echo number_format($rib_total,2); ?></th>
            <th width="100"></th>
            <th width="100"><? echo number_format($total_booking_qty+$rib_total,2); ?></th>
            <th width=""></th>
            
        </tfoot>
     </table>
     <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tble_width?>" class="rpt_table">
        
     </table>
    </div>
</fieldset>
<?
	exit();
}
?>