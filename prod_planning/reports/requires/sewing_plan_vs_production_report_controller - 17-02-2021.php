<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_name=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="print_button_variable_setting")
    {
    	 
        $print_report_format_arr = return_library_array("select format_id,format_id from lib_report_template where template_name in($data) and module_id=4 and report_id=78 and is_deleted=0 and status_active=1","format_id","format_id");
        echo "print_report_button_setting('".implode(',',$print_report_format_arr)."');\n";
        exit(); 
    }

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "- All Buyer -", $selected, "" );
	//echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "- All Buyer -", $selected, "" );      	 
	exit();
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 150, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id in($data) order by location_name","id,location_name", 0, "--Select Location--", $selected, "" );
	exit();     	 
}

if ($action=="load_drop_down_floor")
{
	extract($_REQUEST);
	echo create_drop_down( "cbo_floor_id", 150, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and company_id in($company_id) and location_id in($location_id) order by floor_name","id,floor_name", 0, "--Select Floor--", $selected, "" );
	exit();     	 
}

if ($action=="load_drop_down_line")
{
	extract($_REQUEST);
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$company_id and variable_list=23 and is_deleted=0 and status_active=1");
	
	$cond="";
	$prod_reso_allo=0;
	if($prod_reso_allo==1)
	{
		$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
		$line_array=array();	
		
		$cond .= " and location_id in($location_id)";
		$cond .= " and floor_id in($floor_id)";
		$line_data=sql_select("select id, line_number from prod_resource_mst where is_deleted=0 $cond");		
		
		foreach($line_data as $row)
		{
			$line='';
			$line_number=explode(",",$row[csf('line_number')]);
			foreach($line_number as $val)
			{
				if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
			}
			$line_array[$row[csf('id')]]=$line;
		}

		echo create_drop_down( "cbo_line_id", 150,$line_array,"", 0, "-- Select Line--", $selected, "",0,0 );
	}
	else
	{
		$cond .= " and location_name in($location_id)";
		$cond .= " and floor_name in($floor_id)";

		echo create_drop_down( "cbo_line_id", 150, "select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1 and floor_name!=0 $cond order by line_name","id,line_name", 1, "-- Select --", $selected, "",0,0 );
	}
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
                	<th>Company</th>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="100">Please Enter Job No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:70px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                    <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                </thead>
                <tbody>
                	<tr>
                    	<td align="center">
                        	 <? 
								echo create_drop_down( "cbo_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'sewing_plan_vs_production_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
							?>
                        </td>
                        <td align="center" id="buyer_td">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 90, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_id').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_job_no_search_list_view', 'search_div', 'sewing_plan_vs_production_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:70px;" />
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

if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	$month_id=$data[5];
	//echo $month_id;
	if($company_id==0) { echo "Please Select Company Name."; die;}
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
	$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_field from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond  order by job_no_prefix_num DESC";
	
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","600","230",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','') ;
	exit(); 
} // Job Search end

if($action=="order_no_popup")
{
	echo load_html_head_contents("Order Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
	
		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#order_no_id").val(splitData[0]); 
			$("#order_no_val").val(splitData[1]); 
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
                	<th>Company</th>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="100">Please Enter Po No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:70px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" id="order_no_id" />
					<input type="hidden" id="order_no_val" />
                </thead>
                <tbody>
                	<tr>
                    	<td align="center">
                        	 <? 
								echo create_drop_down( "cbo_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'sewing_plan_vs_production_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
							?>
                        </td>
                        <td align="center" id="buyer_td">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Po No",2=>"Job No");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 90, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_id').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_po_no_search_list_view', 'search_div', 'sewing_plan_vs_production_report_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:70px;" />
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

if ($action=="create_po_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	$month_id=$data[5];
	
	if($company_id==0) { echo "Please Select Company Name."; die;}
	if ($data[1]==0) $buyer_name=""; else $buyer_name=" and b.buyer_name=$data[1]";
	$order_no="";
	if($data[2]==1)
	{
		if ($data[3]=="") $order_no=""; else $order_no=" and a.po_number=$data[3]";
	}
	else if($data[2]==2)
	{
		if ($data[3]=="") $order_no=""; else $order_no=" and b.job_no_prefix_num=$data[3]";
	}
	/*$job_no=str_replace("'","",$txt_job_id);
	if($db_type==0)
	{
		if ($data[2]=="") $job_no_cond=""; else $job_no_cond="  and FIND_IN_SET(b.job_no_prefix_num,'$data[2]')";
	}
	else if($db_type==2)
	{
		if ($data[2]=="") $job_no_cond=""; else $job_no_cond="  and ',' || b.job_no_prefix_num || ',' LIKE '%$data[2]%' ";
	}*/
	
	$sql="select a.id, a.po_number, b.job_no_prefix_num, b.job_no, b.buyer_name, b.style_ref_no from wo_po_details_master b, wo_po_break_down a where b.job_no=a.job_no_mst and b.company_name=$company_id and b.is_deleted=0 $buyer_name $job_no_cond ORDER BY b.job_no_prefix_num DESC";
	//echo $sql;
	$buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name");
	$arr=array(1=>$buyer);
	
	echo  create_list_view("list_view", "Job No,Buyer,Style Ref.,Order No", "80,110,150,180","600","300",0, $sql, "js_set_value", "id,po_number", "", 1, "0,buyer_name,0,0,0", $arr , "job_no_prefix_num,buyer_name,style_ref_no,po_number", "sewing_plan_vs_production_report_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0','') ;
	disconnect($con);
	exit(); 
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_id=str_replace("'","",$cbo_company_id);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_location_id=str_replace("'","",$cbo_location_id);
	$cbo_floor_id=str_replace("'","",$cbo_floor_id);
	$cbo_line_id=str_replace("'","",$cbo_line_id);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$job_no=str_replace("'","",$txt_job_no);
	$order_no=str_replace("'","",$txt_order_no);
	$cbo_date_type=str_replace("'","",$cbo_date_type);
	
	$company_library=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
	$company_lib_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$location_library=return_library_array( "select id, location_name from lib_location",'id','location_name');	
	$floor_library=return_library_array( "select id, floor_name from lib_prod_floor",'id','floor_name');
	$line_name_library=return_library_array( "select id, line_name from lib_sewing_line",'id','line_name');
	//--------------------------------------------------------------------------------------------------------------------
	
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
			$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
		}
		
		if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ('$job_no') ";
		if ($cbo_location_id==0) $location_id_cond=""; else $location_id_cond=" and c.location_id in($cbo_location_id) ";
		if ($cbo_floor_id==0) $floor_id_cond=""; else $floor_id_cond=" and e.floor_name in($cbo_floor_id) ";
		if ($cbo_line_id==0) $line_id_cond=""; else $line_id_cond=" and c.line_id in($cbo_line_id) ";
		if($order_no=="")
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
				$po_number=trim($order_no)."%";
				$po_cond="and b.po_number like '$po_number'";
			}
		}
		$start_date_db=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
		
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
		
		if($cbo_date_type==2){
			//$date_cond="and (c.start_date between '".$start_date."' and '".$end_date."'  or  c.end_date between '".$start_date."' and '".$end_date."'  or   c.start_date < '".$start_date."' and c.end_date> '".$end_date."')";
			$date_cond=" and pd.plan_date between '$start_date' and '$end_date'";
			
			//"and (a.start_date between TO_DATE('".$from_date."','YYYY-MM-DD')  and TO_DATE('".$to_date."','YYYY-MM-DD')   or a.end_date between TO_DATE('".$from_date."','YYYY-MM-DD')  and TO_DATE('".$to_date."','YYYY-MM-DD')  or ( a.start_date < TO_DATE('".$from_date."','YYYY-MM-DD')  and a.end_date> TO_DATE('".$to_date."','YYYY-MM-DD'))) ";
		}
		else
		{
			$date_cond=" and b.pub_shipment_date between '$start_date' and '$end_date'";
		}
		
		
		
		
		if($db_type==0) $lead_day="DATEDIFF(b.pub_shipment_date,b.po_received_date) as  date_diff";
		else if($db_type==2) $lead_day="(b.pub_shipment_date-b.po_received_date) as  date_diff";
			
		if ($cbo_location_id==0) $location_id_cond_res=""; else $location_id_cond_res=" and location_id=$cbo_location_id ";
		if ($cbo_location_id==0) $location_id_cond_sewing=""; else $location_id_cond_sewing=" and location=$cbo_location_id ";
		
		$prod_res_arr=array();
		//echo "select id,line_number from  prod_resource_mst where company_id in($company_id)  and is_deleted=0 $location_id_cond_res order by id ";
		$prod_reso=sql_select("select id,line_number from  prod_resource_mst where company_id in($company_id)  and is_deleted=0 $location_id_cond_res order by id ");
		foreach($prod_reso as $row)
		{
			$line_ids=explode(",",$row[csf('line_number')]);
			$prod_res_arr[$row[csf('id')]]=$line_ids[0];
		}
		
		
		 /*$sql_data="select c.location_id,c.line_id,c.plan_qnty,c.plan_id,c.start_hour,c.end_hour,c.duration,$lead_day,c.comp_level,c.first_day_output,c.increment_qty,c.terget,c.day_wise_plan,c.company_id,c.item_number_id ,c.off_day_plan,c.extra_param,  b.id as po_id,b.pub_shipment_date,b.po_quantity,c.start_date,c.end_date, b.po_number,a.buyer_name,a.job_no_prefix_num,a.style_ref_no as style 
		from  wo_po_break_down b,wo_po_details_master a, ppl_sewing_plan_board c,lib_sewing_line e where  a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.company_id=$company_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.line_id=e.id  $buyer_id_cond $po_cond $date_cond $job_no_cond $location_id_cond
		order by e.line_name
		"; *///order by c.start_date asc
		
		$sql_data="SELECT c.company_id,c.location_id,c.line_id,c.plan_qnty,c.plan_id,c.start_hour,c.end_hour,c.duration,$lead_day,c.comp_level,c.first_day_output,c.increment_qty,c.terget,c.day_wise_plan,c.company_id,c.item_number_id ,c.off_day_plan,c.extra_param,  b.id as po_id,b.pub_shipment_date,b.po_quantity,c.start_date,c.end_date, b.po_number,a.buyer_name,a.job_no_prefix_num,a.style_ref_no as style ,pd.plan_qnty as pdplan_qnty,pd.plan_date
		from  wo_po_break_down b,wo_po_details_master a,ppl_sewing_plan_board_powise pp,ppl_sewing_plan_board_dtls pd, ppl_sewing_plan_board c,lib_sewing_line e where  a.job_no=b.job_no_mst and b.id=pp.po_break_down_id and pp.plan_id=pd.plan_id and pp.plan_id=c.plan_id  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.line_id=e.id and c.company_id in($company_id) $buyer_id_cond $po_cond $date_cond $job_no_cond $location_id_cond
		order by e.line_name";//and c.company_id in($company_id)
		  //echo $sql_data;
   
		$data_result=sql_select($sql_data);
		$sewing_data=array();
		$sewing_data2=array();
		$rowcount=count($data_result);
		$poline=array();
		$i=0;
		$start_date_db=date("Y-m-d",strtotime($start_date));
		$end_date_db=date("Y-m-d",strtotime($end_date));
		foreach( $data_result as $row)
		{
			$i++;
			
			
			$date_found[strtotime($row[csf("plan_date")])]=strtotime($row[csf("plan_date")]);
			$poid[$row[csf("po_id")]]=$row[csf("po_id")];
			
			$poline[$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("po_id")]][$row[csf("line_id")]]=$row[csf("line_id")];
			
			$sewing_data[$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("po_id")]][$row[csf("line_id")]]['po']=$sewing_data;
			$sewing_data[$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("po_id")]][$row[csf("line_id")]]['plan_id']=$row[csf("plan_id")];
			$sewing_data[$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("po_id")]][$row[csf("line_id")]]['plan_qnty']=$row[csf("plan_qnty")];
			$sewing_data[$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("po_id")]][$row[csf("line_id")]]['start']=$row[csf("start_hour")];
			$sewing_data[$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("po_id")]][$row[csf("line_id")]]['end']=$row[csf("end_hour")];
			$sewing_data[$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("po_id")]][$row[csf("line_id")]]['duration']=$row[csf("duration")];
			$sewing_data[$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("po_id")]][$row[csf("line_id")]]['comp']=$row[csf("comp_level")];
			$sewing_data[$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("po_id")]][$row[csf("line_id")]]['first_day']=$row[csf("first_day_output")];
			$sewing_data[$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("po_id")]][$row[csf("line_id")]]['increment']=$row[csf("increment_qty")];
			$sewing_data[$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("po_id")]][$row[csf("line_id")]]['terget']=$row[csf("terget")];
			$sewing_data[$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("po_id")]][$row[csf("line_id")]]['item_id']=$row[csf("item_number_id")];
			$sewing_data[$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("po_id")]][$row[csf("line_id")]]['off_day']=$row[csf("off_day_plan")];
			
			$sewing_data[$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("po_id")]][$row[csf("line_id")]]['po_qty']=$row[csf("po_quantity")];
			$sewing_data[$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("po_id")]][$row[csf("line_id")]]['lead_day']=$row[csf("date_diff")];
			$sewing_data[$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("po_id")]][$row[csf("line_id")]]['buyer']=$row[csf("buyer_name")];
			$sewing_data[$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("po_id")]][$row[csf("line_id")]]['job']=$row[csf("job_no_prefix_num")];
			$sewing_data[$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("po_id")]][$row[csf("line_id")]]['style']=$row[csf("style")];
			$sewing_data[$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("po_id")]][$row[csf("line_id")]]['po_number']=$row[csf("po_number")];
			$sewing_data[$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("po_id")]][$row[csf("line_id")]]['pub_shipment']=$row[csf("pub_shipment_date")];
			$sewing_data[$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("po_id")]][$row[csf("line_id")]][date("M-d",strtotime($row[csf("plan_date")]))]['day_wise']=$row[csf("pdplan_qnty")];
			
			$sewing_data[$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("po_id")]][$row[csf("line_id")]]['pdplan_qntyLine']+=$row[csf("pdplan_qnty")];

		}
		$start_date_db=date("Y-m-d",min($date_found));
		$end_date_db=date("Y-m-d",max($date_found));
		
				
			
			$total_sewing_data_arr=array();
			$total_sewing_data_arr2=array();
			$total_sewing_data_arr3=array();
			
			
			$tot_sewing_prod=sql_select( "select serving_company as company_id,po_break_down_id,location,production_date,item_number_id,sewing_line,prod_reso_allo,production_type,production_quantity as prod_quantity from pro_garments_production_mst where company_id in($company_id) and status_active=1 and is_deleted=0 and production_type in(5,4) $location_id_cond_sewing");
			foreach($tot_sewing_prod as $row) 
			{
				 $ddate=add_date($start_date_db,$d);
				 $prod_date=date("M-d",strtotime($row[csf('production_date')]));
				 $prod_reso_allo=$row[csf('prod_reso_allo')]; 

				 if($row[csf('production_type')]==5){
					$total_sewing_data_arr3[$prod_date]+=$row[csf('prod_quantity')];
					
					
					$total_sewing_data_arr[$row[csf('company_id')]][$row[csf('location')]][$row[csf('po_break_down_id')]][$row[csf('item_number_id')]]['prod_reso_allo']=$row[csf('prod_reso_allo')];
					
					if($prod_reso_allo==0)
					{
					$total_sewing_data_arr[$row[csf('company_id')]][$row[csf('location')]][$row[csf('po_break_down_id')]][$row[csf('sewing_line')]][$row[csf('item_number_id')]][$prod_date]['prod_qty']+=$row[csf('prod_quantity')];
					}
					else
					{ 
					$line_number=$prod_res_arr[$row[csf('sewing_line')]];
					$total_sewing_data_arr[$row[csf('company_id')]][$row[csf('location')]][$row[csf('po_break_down_id')]][$line_number][$row[csf('item_number_id')]][$prod_date]['prod_qty']+=$row[csf('prod_quantity')];
					}
				 }
				 
				 if($row[csf('production_type')]==4){
					 if($prod_reso_allo==0)
					 {
						 $total_sewing_input_data_arr[$row[csf('company_id')]][$row[csf('location')]][$row[csf('po_break_down_id')]][$row[csf('sewing_line')]][$row[csf('item_number_id')]]+=$row[csf('prod_quantity')];
					 }
					 else
					 { 
						 $line_number=$prod_res_arr[$row[csf('sewing_line')]];
						 $total_sewing_input_data_arr[$row[csf('company_id')]][$row[csf('location')]][$row[csf('po_break_down_id')]][$line_number][$row[csf('item_number_id')]]+=$row[csf('prod_quantity')];
					 }
				 }
				 $production_days_data_arr[$row[csf('company_id')]][$row[csf("location")]][$row[csf("po_break_down_id")]][$row[csf("sewing_line")]][$row[csf("item_number_id")]][$row[csf("production_date")]]=$row[csf("production_date")];

			}
			
			
		//print_r($total_sewing_data_arr);die;	
			
			
			 //echo "select company_id,po_break_down_id,location, item_number_id, sum(production_quantity) as production_quantity from pro_garments_production_mst where status_active=1 and is_deleted=0 and production_type=5   group by po_break_down_id, item_number_id,location";
			$sql_sewing_prod=sql_select( "select company_id,po_break_down_id,location, item_number_id, sum(production_quantity) as production_quantity from pro_garments_production_mst where status_active=1 and is_deleted=0 and production_type=5   group by company_id,po_break_down_id, item_number_id,location");//Output qty//Actual Qty
			foreach($sql_sewing_prod as $row)
			{
				 $ddate=add_date($start_date_db,$d);
				 $prod_date=date("M-d",strtotime($row[csf('production_date')]));
				 $total_sewing_data_arr2[$row[csf('company_id')]][$row[csf('location')]][$row[csf('po_break_down_id')]][$row[csf('item_number_id')]]['qty']=$row[csf('production_quantity')];
			}
			
			
			
			$num_days=datediff("d",$start_date_db,$end_date_db);
			$total_day=$num_days;
			if($type==1){$width=$total_day*80+920; }
			else if($type==2){$width=$total_day*80+1020;}
			else{$width=$total_day*80+1220;}
			ob_start();	
		?>
		<div>
			<fieldset style="width:<? echo $width+10; ?>px;">
				<table width="<? echo $width; ?>" cellspacing="0" cellpadding="0" border="0" rules="" class=""  >
					<thead>
						<tr>
							<th align="center"  colspan="<? echo 12+$num_days;?>">
								<strong>
									<?
									 $comp=explode(",",str_replace("'", "", $cbo_company_id));
									 $name="";
									 foreach($comp as $v)
									 {
									 	$name.=(!$name)? $company_lib_arr[$v] : ",".$company_lib_arr[$v];
									 }
									 echo $name;
									?>
								</strong>
							</th>
						</tr>
						<tr>
							<th align="center"  colspan="<? echo 12+$num_days;?>">
								<strong>
									 Line wise Sewing Plan
								</strong>
							</th>
						</tr>

					</thead>
				</table>			


			<?
			if($type==1) // SHOW BUTTON 1
			{
				?>
				<table width="<? echo $width; ?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table"  >
					<thead>
						<th width="40">SL</th>
						<th width="90">Buyer / Style Ref/ Job No</th>
						<th width="90">Order No & Ship Date</th>
						<th width="70">Order Qty</th>
						<th width="80">Prod. Qty</th>
						<th width="80">Yet to Prod.</th>
						<th width="50">Early / Late By</th>
						<th width="100">Line No / Input Qty</th>
						<th width="80">Line Plan / Line Prod. Total</th>
						<th width="70">Line Avg. / Day</th>
						<th width="100">Particulars</th>
						<?
						for($m=0;$m<$num_days;$m++)
						{
							$ddate=add_date($start_date_db,$m)
							?>
							<th width="80"><? echo  date("M-d",strtotime($ddate)); ?></th>
							<?
						}
						?>
					</thead>
				</table>

					<div style="width:<? echo $width+20; ?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
							<table width="<? echo $width; ?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
								<?
								$j=1;
								$k=1;
								foreach($sewing_data as $company_key=>$unit_arr)
								{
									foreach($unit_arr as $unit_key=>$sewing_data_arr) 
									{
										?>
										<tr  bgcolor="#BEC6BB">
											<td align="left" colspan="<? echo $num_days+11; ?>"><strong>Sewing Unit : <? echo $company_library[$company_key].",".$location_library[$unit_key]; ?> </strong></td>
										</tr>
										<?
										$actual_line_balance_array=array(); 
										$date_actual_qty_arr=array();
										$date_actual_qty_arr2=array();
										$order_wise_actual_qty_arr=array();

										foreach($sewing_data_arr as $po_id=>$linedata)
										{
											$po=0;
											$prod_qty=0;
											$order_check_arr=array();
											$order_qty_arr=array();
											$tdbgcolor="";
											foreach($linedata as $lineid=>$pdata)
											{
												if ($k%2==0)  
													$bgcolor="#E9F3FF";
												else
													$bgcolor="#FFFFFF";
												$po++;
												if ($po==1){  
													$tdbgcolor=$bgcolor;
												}


												$k++;

												$item_id=$sewing_data[$company_key][$unit_key][$po_id][$lineid]['item_id'];
												$prod_qty=$total_sewing_data_arr2[$company_key][$unit_key][$po_id][$item_id]['qty'];

												$production_days=$production_days_data_arr[$company_key][$unit_key][$po_id][$lineid][$item_id]['day'];
												$daily_production=$prod_qty/$production_days;

												$balance=$pdata['po_qty']-$prod_qty;
												$days_required=ceil($balance/$daily_production);

												$to_be_end=add_date(date("Y-m-d",time()),$days_required);
												$late_early=datediff("d",$to_be_end,$pdata['pub_shipment']);

												$color="";
												if($late_early<3)
												{
													$color="red";
												}
												$late_early=$late_early-2;

												$z=1;
												?>
												<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $j; ?>"> 
													<td width="40" rowspan="3" style="background-color: <? if($po !=1){echo $tdbgcolor;}  ?>; color:<? if($po !=1){echo $tdbgcolor;}  ?>"  >
														<? echo $j; ?>
													</td>
													<td width="90" rowspan="3" align="center" style="background-color: <? if($po !=1){echo $tdbgcolor;} ?>; color:<? if($po !=1){echo $tdbgcolor;}  ?>" >
														<p>
															<? 
															echo $buyer_arr[$pdata['buyer']].'<br>'.$pdata['style'].'<br>'.$pdata['job']; 
															?> 
														</p>
													</td>
													<td width="90" rowspan="3" align="center" style="background-color: <? if($po !=1){echo $tdbgcolor;} ?>; color:<? if($po !=1){echo $tdbgcolor;}  ?>" >
														<p>
															<? 
															echo $pdata['po_number'].'<br>'.change_date_format($pdata['pub_shipment']).'<br>'.$pdata['lead_day'].' Days';
															?> 
														</p>
													</td>
													<td width="70" rowspan="3" align="right" style="background-color: <? if($po !=1){echo $tdbgcolor;}  ?>; color:<? if($po !=1){echo $tdbgcolor;}  ?>" >
														<? 
														if ($po==1){
															$order_qty+=$pdata['po_qty'];
														}
														echo number_format($pdata['po_qty']); 
														?> 
													</td>
													<td width="80" rowspan="3" align="right" style="background-color: <? if($po !=1){echo $tdbgcolor;}  ?>; color:<? if($po !=1){echo $tdbgcolor;}  ?>" >
														<? 
														if ($po==1){
															$tot_prod_qty+=$prod_qty;
														}
														echo number_format($prod_qty,0); 
														?>
													</td>
													<td width="80" rowspan="3" align="right" style="background-color: <? if($po !=1){echo $tdbgcolor;}  ?>; color:<? if($po !=1){echo $tdbgcolor;}  ?>"> 								
														<? 
														$yet_to_prod=$prod_qty-$pdata['po_qty'];
														if ($po==1){
															$tot_yet_to_prod+=$yet_to_prod;
														}
														echo number_format($yet_to_prod); 
														?> 
													</td>
													<td width="50" rowspan="3" bgcolor="<? echo $color; ?>" style="background-color: <? if($po !=1 && $color==""){echo $tdbgcolor;}else{echo $color;}  ?>; color:<? if($po ==1){echo '';} if($po !=1 && $color!=""){echo $color;}if($po !=1 && $color==""){echo $tdbgcolor;}  ?>" title="Early/Late By" align="center" > 
														<? echo $late_early; ?> 
													</td>

													<td width="100" rowspan="3" align="center" title="Sewing Input" >
														<? 
														$item_id=$sewing_data[$company_key][$unit_key][$po_id][$lineid]['item_id'];
														$sewing_input=$total_sewing_input_data_arr[$company_key][$unit_key][$po_id][$lineid][$item_id];
														$tot_input_qty+=$sewing_input;
														echo $line_name_library[$lineid].'<br>'.number_format($sewing_input); 
														?> 
													</td>
													<td width="80" rowspan="3"  title="Actaul Total"  align="right">

														<?
														$tot_line_prod_qty=0;
														$tot_row=0;
														$actual_qty_tot=0;
														for($p=0;$p<$num_days;$p++)
														{
															$ddate=add_date($start_date_db,$p);
															$pdate=date("M-d",strtotime($ddate));
															$item_id=$sewing_data[$company_key][$unit_key][$po_id][$lineid]['item_id'];
															$actual_qty_tot+=$total_sewing_data_arr[$company_key][$unit_key][$po_id][$lineid][$item_id][$pdate]['prod_qty'];
															$tot_row+=count($total_sewing_data_arr[$company_key][$unit_key][$po_id][$lineid][$item_id][$pdate]['prod_qty']);
															$tot_line_prod_qty+=$actual_qty_tot;
														} 
														$tot_actual+=$actual_qty_tot;
														echo $sewing_data[$company_key][$unit_key][$po_id][$lineid]['pdplan_qntyLine']."<br/>".number_format($actual_qty_tot); 
														?> 
													</td>
													<td width="70" rowspan="3"  title="Actaul Total/Days" align="right" >
														<? 
														echo number_format($actual_qty_tot/$tot_row); 
														?> 
													</td>
													<td width="100"><p>Planed</p> </td>
													<?
													for($m=0;$m<$num_days;$m++)
													{
														?>
														<td align="right" width="80">
															<? 
															$ddate=add_date($start_date_db,$m);
															$pdate=date("M-d",strtotime($ddate));
															$duration=$sewing_data[$company_key][$unit_key][$po_id][$lineid][$pdate]['duration'];
															$plan_qty=$sewing_data[$company_key][$unit_key][$po_id][$lineid][$pdate]['day_wise'];
															if($plan_qty==0 || $plan_qty=="")
															{
																echo "";	
															}
															else
															{
																echo  number_format($plan_qty);
															}
															?>
														</td>
														<?
													}
													?>
												</tr>

												<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tract_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tract_<? echo $k; ?>">
													<td width="100"><p>Actual</p> </td>
													<?
													$tot_line_prod_qty=0;$tot_row=0;$actual_line_balance_array=array();
													for($m=0;$m<$num_days;$m++)
													{
														$ddate=add_date($start_date_db,$m);
														$pdate=date("M-d",strtotime($ddate));
														$item_id=$sewing_data[$company_key][$unit_key][$po_id][$lineid]['item_id'];
														$actual_qty=$total_sewing_data_arr[$company_key][$unit_key][$po_id][$lineid][$item_id][$pdate]['prod_qty'];
														$tot_row+=count($actual_qty);
														?>
														<td align="right" width="80">
															<?
															
															//echo $company_key.'**'.$unit_key.'**'.$po_id.'**'.$lineid.'**'.$item_id.'**'.$pdate;
															
															 
															$tot_line_prod_qty+=$actual_qty;
															if($actual_qty==0 || $actual_qty=="")
															{
																echo "";
															}
															else
															{
																echo  number_format($actual_qty);
															}
															?>
														</td>
														<?
														$order_wise_actual_qty_arr[$pdate]+=$actual_qty;
														$date_actual_qty_arr[$pdate]+=$actual_qty;
													}
													?>
												</tr>
												<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trvari_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="trvari_<? echo $k; ?>">
													<td width="100"><p>Variance</p> </td>
													<?
													for($m=0;$m<$num_days;$m++)
													{
														$ddate=add_date($start_date_db,$m);
														$pdate=date("M-d",strtotime($ddate));
														$plan_qty=$sewing_data[$company_key][$unit_key][$po_id][$lineid][$pdate]['day_wise'];

														$ddate=add_date($start_date_db,$m);
														$pdate=date("M-d",strtotime($ddate));
														$item_id=$sewing_data[$company_key][$unit_key][$po_id][$lineid]['item_id'];
														$actual_qty=$total_sewing_data_arr[$company_key][$unit_key][$po_id][$lineid][$item_id][$pdate]['prod_qty'];
														?>
														<td align="right" width="80">
															<? 
															$tot_variance=$actual_qty-$plan_qty;
															if($tot_variance==0 || $tot_variance=="")
															{
																echo "";	
															}
															else
															{
																echo  number_format($tot_variance);	
															}
															?>
														</td>
														<?
													}
													?>			
												</tr>
												<?
												$j++;

											}

											?>
											<tr class="tbl_bottom">
												<td  colspan="3">Sub Total</td>
												<td width="70">
													<? 
													echo  number_format($order_qty,0);
													$order_qty_unit+=$order_qty;
													$order_qty_grand+=$order_qty;
													$order_qty=0; 
													?>
												</td>
												<td width="80">
													<? 
													echo number_format($tot_prod_qty,0);
													$prod_qty_unit+=$tot_prod_qty;
													$prod_qty_grand+=$tot_prod_qty;
													$tot_prod_qty=0;
													?>
												</td>
												<td width="80">
													<? 
													echo number_format($tot_yet_to_prod,0);
													$yet_prod_qty_unit+=$tot_yet_to_prod;
													$yet_prod_qty_grand+=$tot_yet_to_prod;
													$tot_yet_to_prod=0;
													?>
												</td>
												<td width="50"></td>
												<td width="100">
													<? 
													echo number_format($tot_input_qty,0); 
													$tot_input_qty_grnd +=$tot_input_qty;
													$tot_input_qty_unit+=$tot_input_qty; 
													$tot_input_qty=0;
													?>
												</td>
												<td width="80">
													<? 
													echo number_format($tot_actual,0);
													$actual_qty_tot_unit +=$tot_actual;
													$actual_qty_tot_grnd +=$tot_actual; 
													$tot_actual=0;
													?>
												</td>
												<td width="70"></td>
												<td width="100"></td>
												<? 
												$order_wise_actual_qty=0;
												for($m=0;$m<$num_days;$m++)
												{
													$ddate=add_date($start_date_db,$m);
													$pdate=date("M-d",strtotime($ddate));
													$item_id=$sewing_data[$company_key][$unit_key][$po_id][$lineid]['item_id'];
													$actual_qty=$total_sewing_data_arr[$company_key][$unit_key][$po_id][$lineid][$item_id][$pdate]['prod_qty'];
													$order_wise_actual_qty=$order_wise_actual_qty_arr[$pdate];
													?>
													<td width="80">
														<? 
														echo  $order_wise_actual_qty_arr[$pdate]; 
														$order_wise_actual_qty_arr[$pdate]=0;
														?>
													</td>
													<?
												}
												unset($order_wise_actual_qty_arr[$pdate]); 
												?>
											</tr> 
											<?
										}
										?>

										<tr class="tbl_bottom">
											<td colspan="3"> Unit Wise  Total</td>
											<td width="70">
												<? 
												echo number_format($order_qty_unit,0);
												$order_qty_unit=0;
												?>
											</td>
											<td width="80">
												<? 
												echo number_format($prod_qty_unit,0);$prod_qty_unit=0;
												?>
											</td>
											<td width="80">
												<? 
												echo number_format($yet_prod_qty_unit,0); 
												$yet_prod_qty_unit=0;
												?>
											</td>
											<td width="50"></td>
											<td width="100">
												<? 
												echo number_format($tot_input_qty_unit,0); 
												$tot_input_qty_unit=0; 
												?>
											</td>
											<td width="80">
												<? 
												echo number_format($actual_qty_tot_unit,0); 
												$actual_qty_tot_unit=0; 
												?>
											</td>
											<td width="70"></td>
											<td width="100"></td>
											<?
											$total_actual_qty=0;$actual_line_balance_array=array();
											for($m=0;$m<$num_days;$m++)
											{
												$ddate=add_date($start_date_db,$m);
												$pdate=date("M-d",strtotime($ddate));
												$date_actual_qty_arr[$pdate];
												?>
												<td width="80">
													<? 
													echo number_format($date_actual_qty_arr[$pdate]); 
													?>
												</td>
												<?
												$date_actual_qty_arr2[$pdate]+=$date_actual_qty_arr[$pdate];
											}
											?>
										</tr>

										<?
									} 
								}
		                    if($num_days!="") //Grand Total
		                    {  
		                    	?>				
		                    	<tr class="tbl_bottom">
		                    		<td colspan="3"> Grand Total </td>
		                    		<td width="70"><? echo  number_format($order_qty_grand,0);?></td>
		                    		<td width="80"><? echo  number_format($prod_qty_grand,0);?></td>
		                    		<td width="80"><? echo  number_format($yet_prod_qty_grand,0);?></td>
		                    		<td width="50"></td>
		                    		<td width="100"><? echo number_format($tot_input_qty_grnd,0);?></td>
		                    		<td width="80"><? echo number_format($actual_qty_tot_grnd,0);?></td>
		                    		<td width="70"></td>
		                    		<td width="100"></td>
		                    		<?
		                    		for($m=0;$m<$num_days;$m++)
		                    		{
		                    			$ddate=add_date($start_date_db,$m);
		                    			$pdate=date("M-d",strtotime($ddate));
		                    			?>
		                    			<td width="80" align="right"> 
		                    				<? echo number_format($date_actual_qty_arr2[$pdate]); ?>
		                    			</td>
		                    			<?
		                    		}
		                    		?>
		                    	</tr>
		                    	<?
		                    }
		                    ?>
		                </table>
	            </div>

				<?
			}
			elseif($type==2) // SHOW BUTTON 2
			{
				?>
				<table width="<? echo $width; ?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table"  >
					<thead>
						<th width="40">SL</th>
						<th width="90">Buyer / Style Ref/ Job No</th>
						<th width="90">Order No & Ship Date</th>
						<th width="70">Order Qty</th>
						<th width="80">Prod. Qty</th>
						<th width="80">Yet to Prod.</th>
						<th width="50">Early / Late By</th>
						<th width="100">Line No / Input Qty</th>
						<th width="80">Line Plan / Line Prod. Total</th>
						<th width="70">Line Avg. / Day</th>
						<th width="100">Particulars</th>
						<?
						for($m=0;$m<$num_days;$m++)
						{
							$ddate=add_date($start_date_db,$m)
							?>
							<th width="80"><? echo  date("M-d",strtotime($ddate)); ?></th>
							<?
						}
						?>
						<th width="100">Total</th>							
						
					</thead>
				</table>

					<div style="width:<? echo $width+20; ?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
						<table width="<? echo $width; ?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
							<?
							$j=1;
							$k=1;
							$gr_date_wise_planed_arr=array();
							foreach($sewing_data as $company_key=>$unit_arr)
							{
								foreach($unit_arr as $unit_key=>$sewing_data_arr) 
								{
									?>
									<tr  bgcolor="#BEC6BB">
										<td align="left" colspan="<? echo $num_days+12; ?>"><strong>Sewing Unit : <? echo $company_library[$company_key].",".$location_library[$unit_key]; ?> </strong></td>
									</tr>
									<?
									$actual_line_balance_array=array(); 
									$date_actual_qty_arr=array();
									$date_actual_qty_arr2=array();
									$order_wise_actual_qty_arr=array();
									$date_wise_planed_arr=array();

									foreach($sewing_data_arr as $po_id=>$linedata)
									{
										$po=0;
										$prod_qty=0;
										$order_check_arr=array();
										$order_qty_arr=array();
										$tdbgcolor="";
										$sub_date_wise_planed_arr=array();
										foreach($linedata as $lineid=>$pdata)
										{
											if ($k%2==0)  
												$bgcolor="#E9F3FF";
											else
												$bgcolor="#FFFFFF";
											$po++;
											if ($po==1){  
												$tdbgcolor=$bgcolor;
											}


											$k++;

											$item_id=$sewing_data[$company_key][$unit_key][$po_id][$lineid]['item_id'];
											$prod_qty=$total_sewing_data_arr2[$company_key][$unit_key][$po_id][$item_id]['qty'];

											$production_days=$production_days_data_arr[$company_key][$unit_key][$po_id][$lineid][$item_id]['day'];
											$daily_production=$prod_qty/$production_days;

											$balance=$pdata['po_qty']-$prod_qty;
											$days_required=ceil($balance/$daily_production);

											$to_be_end=add_date(date("Y-m-d",time()),$days_required);
											$late_early=datediff("d",$to_be_end,$pdata['pub_shipment']);

											$color="";
											if($late_early<3)
											{
												$color="red";
											}
											$late_early=$late_early-2;

											$z=1;
											?>
											<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $j; ?>"> 
												<td width="40"  style="background-color: <? if($po !=1){echo $tdbgcolor;}  ?>; color:<? if($po !=1){echo $tdbgcolor;}  ?>"  >
													<? echo $j; ?>
												</td>
												<td width="90"  align="center" style="background-color: <? if($po !=1){echo $tdbgcolor;} ?>; color:<? if($po !=1){echo $tdbgcolor;}  ?>" >
													<p>
														<? 
														echo $buyer_arr[$pdata['buyer']].'<br>'.$pdata['style'].'<br>'.$pdata['job']; 
														?> 
													</p>
												</td>
												<td width="90"  align="center" style="background-color: <? if($po !=1){echo $tdbgcolor;} ?>; color:<? if($po !=1){echo $tdbgcolor;}  ?>" >
													<p>
														<? 
														echo $pdata['po_number'].'<br>'.change_date_format($pdata['pub_shipment']).'<br>'.$pdata['lead_day'].' Days';
														?> 
													</p>
												</td>
												<td width="70"  align="right" style="background-color: <? if($po !=1){echo $tdbgcolor;}  ?>; color:<? if($po !=1){echo $tdbgcolor;}  ?>" >
													<? 
													if ($po==1){
														$order_qty+=$pdata['po_qty'];
													}
													echo number_format($pdata['po_qty']); 
													?> 
												</td>
												<td width="80"  align="right" style="background-color: <? if($po !=1){echo $tdbgcolor;}  ?>; color:<? if($po !=1){echo $tdbgcolor;}  ?>" >
													<? 
													if ($po==1){
														$tot_prod_qty+=$prod_qty;
													}
													echo number_format($prod_qty,0); 
													?>
												</td>
												<td width="80"  align="right" style="background-color: <? if($po !=1){echo $tdbgcolor;}  ?>; color:<? if($po !=1){echo $tdbgcolor;}  ?>"> 								
													<? 
													$yet_to_prod=$prod_qty-$pdata['po_qty'];
													if ($po==1){
														$tot_yet_to_prod+=$yet_to_prod;
													}
													echo number_format($yet_to_prod); 
													?> 
												</td>
												<td width="50"  bgcolor="<? echo $color; ?>" style="background-color: <? if($po !=1 && $color==""){echo $tdbgcolor;}else{echo $color;}  ?>; color:<? if($po ==1){echo '';} if($po !=1 && $color!=""){echo $color;}if($po !=1 && $color==""){echo $tdbgcolor;}  ?>" title="Early/Late By" align="center" > 
													<? echo $late_early; ?> 
												</td>

												<td width="100"  align="center" title="Sewing Input" >
													<? 
													$item_id=$sewing_data[$company_key][$unit_key][$po_id][$lineid]['item_id'];
													$sewing_input=$total_sewing_input_data_arr[$company_key][$unit_key][$po_id][$lineid][$item_id];
													$tot_input_qty+=$sewing_input;
													echo $line_name_library[$lineid].'<br>'.number_format($sewing_input); 
													?> 
												</td>
												<td width="80"   title="Actual Total"  align="right">

													<?
													$tot_line_prod_qty=0;
													$tot_row=0;
													$actual_qty_tot=0;
													for($p=0;$p<$num_days;$p++)
													{
														$ddate=add_date($start_date_db,$p);
														$pdate=date("M-d",strtotime($ddate));
														$item_id=$sewing_data[$company_key][$unit_key][$po_id][$lineid]['item_id'];
														$actual_qty_tot+=$total_sewing_data_arr[$company_key][$unit_key][$po_id][$lineid][$item_id][$pdate]['prod_qty'];
														$tot_row+=count($total_sewing_data_arr[$company_key][$unit_key][$po_id][$lineid][$item_id][$pdate]['prod_qty']);
														$tot_line_prod_qty+=$actual_qty_tot;

													} 
													$tot_actual+=$actual_qty_tot;
													echo $sewing_data[$company_key][$unit_key][$po_id][$lineid]['pdplan_qntyLine']."<br/>".number_format($actual_qty_tot); 
													?> 
												</td>
												<td width="70"   title="Actaul Total/Days" align="right" >
													<? 
													echo number_format($actual_qty_tot/$tot_row); 
													?> 
												</td>
												<td width="100"><p>Planed</p> </td>
												<?
												$day_total=0;
												for($m=0;$m<$num_days;$m++)
												{
													?>
													<td align="right" width="80">
														<? 
														$ddate=add_date($start_date_db,$m);
														$pdate=date("M-d",strtotime($ddate));
														$duration=$sewing_data[$company_key][$unit_key][$po_id][$lineid][$pdate]['duration'];
														$plan_qty=$sewing_data[$company_key][$unit_key][$po_id][$lineid][$pdate]['day_wise'];
														if($plan_qty==0 || $plan_qty=="")
														{
															echo "";	
														}
														else
														{
															echo  number_format($plan_qty);
														}
														?>
													</td>
													<?
													$date_wise_planed_arr[$pdate]+=$plan_qty;
													$gr_date_wise_planed_arr[$pdate]+=$plan_qty;
													$sub_date_wise_planed_arr[$pdate]+=$plan_qty;
													$day_total+=$plan_qty;
												}
												?>
												<td align="right" width="100"><? echo $day_total;?></td>
											</tr>



											
											<?
											$j++;

										}

										?>
										<tr class="tbl_bottom">
											<td  colspan="3">Sub Total</td>
											<td width="70">
												<? 
												echo  number_format($order_qty,0);
												$order_qty_unit+=$order_qty;
												$order_qty_grand+=$order_qty;
												$order_qty=0; 
												?>
											</td>
											<td width="80">
												<? 
												echo number_format($tot_prod_qty,0);
												$prod_qty_unit+=$tot_prod_qty;
												$prod_qty_grand+=$tot_prod_qty;
												$tot_prod_qty=0;
												?>
											</td>
											<td width="80">
												<? 
												echo number_format($tot_yet_to_prod,0);
												$yet_prod_qty_unit+=$tot_yet_to_prod;
												$yet_prod_qty_grand+=$tot_yet_to_prod;
												$tot_yet_to_prod=0;
												?>
											</td>
											<td width="50"></td>
											<td width="100">
												<? 
												echo number_format($tot_input_qty,0); 
												$tot_input_qty_grnd +=$tot_input_qty;
												$tot_input_qty_unit+=$tot_input_qty; 
												$tot_input_qty=0;
												?>
											</td>
											<td width="80">
												<? 
												echo number_format($tot_actual,0);
												$actual_qty_tot_unit +=$tot_actual;
												$actual_qty_tot_grnd +=$tot_actual; 
												$tot_actual=0;
												?>
											</td>
											<td width="70"></td>
											<td width="100"></td>
											<? 
											$order_wise_actual_qty=0;
											$sub_day_total=0;
											for($m=0;$m<$num_days;$m++)
											{
												$ddate=add_date($start_date_db,$m);
												$pdate=date("M-d",strtotime($ddate));
												$item_id=$sewing_data[$company_key][$unit_key][$po_id][$lineid]['item_id'];
												$actual_qty=$total_sewing_data_arr[$company_key][$unit_key][$po_id][$lineid][$item_id][$pdate]['prod_qty'];
												$order_wise_actual_qty=$order_wise_actual_qty_arr[$pdate];
												?>
												<td width="80">
													<? 
													echo $sub_date_wise_planed_arr[$pdate] ;
													$order_wise_actual_qty_arr[$pdate]=0;
													?>
												</td>
												<?
												$sub_day_total+=$sub_date_wise_planed_arr[$pdate];
											}
											unset($order_wise_actual_qty_arr[$pdate]); 
											?>
											<td width="100"><? echo $sub_day_total; ?></td>
										</tr> 
										<?
									}
									?>

									<tr class="tbl_bottom">
										<td colspan="3"> Unit Wise  Total</td>
										<td width="70">
											<? 
											echo number_format($order_qty_unit,0);
											$order_qty_unit=0;
											?>
										</td>
										<td width="80">
											<? 
											echo number_format($prod_qty_unit,0);$prod_qty_unit=0;
											?>
										</td>
										<td width="80">
											<? 
											echo number_format($yet_prod_qty_unit,0); 
											$yet_prod_qty_unit=0;
											?>
										</td>
										<td width="50"></td>
										<td width="100">
											<? 
											echo number_format($tot_input_qty_unit,0); 
											$tot_input_qty_unit=0; 
											?>
										</td>
										<td width="80">
											<? 
											echo number_format($actual_qty_tot_unit,0); 
											$actual_qty_tot_unit=0; 
											?>
										</td>
										<td width="70"></td>
										<td width="100"></td>
										<?
										$total_actual_qty=0;$actual_line_balance_array=array();
										$unit_wise_day_total=0;
										for($m=0;$m<$num_days;$m++)
										{
											$ddate=add_date($start_date_db,$m);
											$pdate=date("M-d",strtotime($ddate));
											$date_actual_qty_arr[$pdate];
											?>
											<td width="80">
												<? 
												echo number_format($date_wise_planed_arr[$pdate]); 
												?>
											</td>
											<?
											$date_actual_qty_arr2[$pdate]+=$date_actual_qty_arr[$pdate];
											$unit_wise_day_total+=number_format($date_wise_planed_arr[$pdate]);
										}
										?>
										<td width="100"><? echo $unit_wise_day_total;?></td>
									</tr>

									<?
								} 
							}
	                    if($num_days!="") //Grand Total
	                    {  
	                    	?>				
	                    	<tr class="tbl_bottom">
	                    		<td colspan="3"> Grand Total </td>
	                    		<td width="70"><? echo  number_format($order_qty_grand,0);?></td>
	                    		<td width="80"><? echo  number_format($prod_qty_grand,0);?></td>
	                    		<td width="80"><? echo  number_format($yet_prod_qty_grand,0);?></td>
	                    		<td width="50"></td>
	                    		<td width="100"><? echo number_format($tot_input_qty_grnd,0);?></td>
	                    		<td width="80"><? echo number_format($actual_qty_tot_grnd,0);?></td>
	                    		<td width="70"></td>
	                    		<td width="100"></td>
	                    		<?
	                    		$gr_wise_day_total=0;
	                    		for($m=0;$m<$num_days;$m++)
	                    		{
	                    			$ddate=add_date($start_date_db,$m);
	                    			$pdate=date("M-d",strtotime($ddate));
	                    			?>
	                    			<td width="80" align="right"> 
	                    				<? echo number_format($gr_date_wise_planed_arr[$pdate]); ?>
	                    			</td>
	                    			<?
	                    			$gr_wise_day_total+=number_format($gr_date_wise_planed_arr[$pdate]);
	                    		}
	                    		?>
	                    		<td width="100"><? echo $gr_wise_day_total;?></td>
	                    	</tr>
	                    	<?
	                    }
	                    ?>
	                </table>
	            </div>

				<?
			}
				else // SHOW BUTTON 3 
			{
				if ($cbo_line_id==0) $line_id_cond=""; else $line_id_cond=" and e.id in($cbo_line_id) ";
				$sql_data="SELECT  c.company_id,c.location_id,c.line_id,c.plan_qnty,c.plan_id,c.start_hour,c.end_hour,c.duration,$lead_day,c.comp_level,c.first_day_output,c.increment_qty,c.terget,c.day_wise_plan,c.company_id,c.item_number_id ,c.off_day_plan,c.extra_param,  b.id as po_id,b.pub_shipment_date,b.po_quantity,c.start_date,c.end_date, b.po_number,a.buyer_name,a.job_no_prefix_num,a.style_ref_no as style ,pd.plan_qnty as pdplan_qnty,pd.plan_date,e.man_power,e.floor_name
					from  wo_po_break_down b,wo_po_details_master a,ppl_sewing_plan_board_powise pp,ppl_sewing_plan_board_dtls pd, ppl_sewing_plan_board c,lib_sewing_line e where  a.job_no=b.job_no_mst and b.id=pp.po_break_down_id and pp.plan_id=pd.plan_id and pp.plan_id=c.plan_id  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.line_id=e.id and c.company_id in($company_id)  $buyer_id_cond $po_cond $date_cond $job_no_cond $location_id_cond $floor_id_cond $line_id_cond
					order by e.line_name";//and c.company_id in($company_id)

		        $sql_planwise_data="SELECT line_id,prev_plan_id FROM ppl_sewing_plan_board  where merge_type='Merged'";
		        $line_arr=return_library_array( "SELECT id, line_name from lib_sewing_line",'id','line_name');
   
				$data_result=sql_select($sql_data);
				$sewing_data=array();
				$sewing_data2=array();
				$planwise_data_arr=array();
				$planwise_data=sql_select($sql_planwise_data);
				foreach($planwise_data as $val)
				{
					if($val[csf('prev_plan_id')])
					{
						if($planwise_data_arr[$val[csf('prev_plan_id')]]!="")
						{
							$planwise_data_arr[$val[csf('prev_plan_id')]].=",".$line_arr[$val[csf('line_id')]]; 
						}
						else
						{
							$planwise_data_arr[$val[csf('prev_plan_id')]]=$line_arr[$val[csf('line_id')]]; 
						}
					}
					
				}
				$sewing_floor=array();
				$rowcount=count($data_result);
				$poline=array();
				$i=0;
				$start_date_db=date("Y-m-d",strtotime($start_date));
				$end_date_db=date("Y-m-d",strtotime($end_date));
				foreach( $data_result as $row)
				{

					$i++;		
					
					$date_found[strtotime($row[csf("plan_date")])]=strtotime($row[csf("plan_date")]);
					$comp_id_arr[$row[csf("company_id")]]=$row[csf("company_id")];
					$poid[$row[csf("po_id")]]=$row[csf("po_id")];
					$loc_id_arr[$row[csf("location_id")]]=$row[csf("location_id")];
					$floor_id_arr[$row[csf("floor_name")]]=$row[csf("floor_name")];
					$line_id_arr[$row[csf("line_id")]]=$row[csf("line_id")];
					
					$poline[$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("po_id")]][$row[csf("line_id")]]=$row[csf("line_id")];
					
					$sewing_data[$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("floor_name")]][$row[csf("po_id")]][$row[csf("line_id")]]['po']=$sewing_data;
					
					$sewing_data[$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("floor_name")]][$row[csf("po_id")]][$row[csf("line_id")]]['plan_qnty']=$row[csf("plan_qnty")];
					$sewing_data[$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("floor_name")]][$row[csf("po_id")]][$row[csf("line_id")]]['start']=$row[csf("start_hour")];
					$sewing_data[$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("floor_name")]][$row[csf("po_id")]][$row[csf("line_id")]]['end']=$row[csf("end_hour")];
					$sewing_data[$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("floor_name")]][$row[csf("po_id")]][$row[csf("line_id")]]['duration']=$row[csf("duration")];
					$sewing_data[$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("floor_name")]][$row[csf("po_id")]][$row[csf("line_id")]]['comp']=$row[csf("comp_level")];
					$sewing_data[$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("floor_name")]][$row[csf("po_id")]][$row[csf("line_id")]]['first_day']=$row[csf("first_day_output")];
					$sewing_data[$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("floor_name")]][$row[csf("po_id")]][$row[csf("line_id")]]['increment']=$row[csf("increment_qty")];
					$sewing_data[$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("floor_name")]][$row[csf("po_id")]][$row[csf("line_id")]]['terget']=$row[csf("terget")];
					$sewing_data[$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("floor_name")]][$row[csf("po_id")]][$row[csf("line_id")]]['item_id']=$row[csf("item_number_id")];
					$sewing_data[$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("floor_name")]][$row[csf("po_id")]][$row[csf("line_id")]]['off_day']=$row[csf("off_day_plan")];
					
					$sewing_data[$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("floor_name")]][$row[csf("po_id")]][$row[csf("line_id")]]['po_qty']=$row[csf("po_quantity")];
					$sewing_data[$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("floor_name")]][$row[csf("po_id")]][$row[csf("line_id")]]['lead_day']=$row[csf("date_diff")];
					$sewing_data[$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("floor_name")]][$row[csf("po_id")]][$row[csf("line_id")]]['buyer']=$row[csf("buyer_name")];
					$sewing_data[$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("floor_name")]][$row[csf("po_id")]][$row[csf("line_id")]]['job']=$row[csf("job_no_prefix_num")];
					$sewing_data[$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("floor_name")]][$row[csf("po_id")]][$row[csf("line_id")]]['style']=$row[csf("style")];
					$sewing_data[$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("floor_name")]][$row[csf("po_id")]][$row[csf("line_id")]]['po_number']=$row[csf("po_number")];

					$sewing_data[$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("floor_name")]][$row[csf("po_id")]][$row[csf("line_id")]]['plan_id']=$row[csf("plan_id")];

					$sewing_data[$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("floor_name")]][$row[csf("po_id")]][$row[csf("line_id")]]['pub_shipment']=$row[csf("pub_shipment_date")];
					$sewing_data[$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("floor_name")]][$row[csf("po_id")]][$row[csf("line_id")]][date("M-d",strtotime($row[csf("plan_date")]))]['day_wise']=$row[csf("pdplan_qnty")];
					$sewing_data[$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("floor_name")]][$row[csf("po_id")]][$row[csf("line_id")]]['pdplan_qntyLine']+=$row[csf("pdplan_qnty")];
					$sewing_data[$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("floor_name")]][$row[csf("po_id")]][$row[csf("line_id")]]['floor_name']+=$row[csf("floor_name")]; 
					$sewing_floor[$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("floor_name")]]=$row[csf("floor_name")];
					
				}
				/* echo "<pre>";
				print_r($sewing_data);
				 echo "</pre>";die;*/
				$start_date_db=date("Y-m-d",min($date_found));
				$end_date_db=date("Y-m-d",max($date_found));
		
				$comp_id 	= implode(",", $comp_id_arr);
				$po_ids 	= implode(",", $poid);
				$loc_id 	= implode(",", $loc_id_arr);
				$floor_id 	= implode(",", $floor_id_arr);
				$line_ids 	= implode(",", $line_id_arr);

				// ==================================== QUERY FOR OPERATOR ==========================================
				$sql_op = "SELECT a.company_id,a.location_id,a.floor_id,a.line_number,c.operator,d.po_id 
				from prod_resource_mst a,prod_resource_dtls b,prod_resource_dtls_mast c, prod_resource_color_size d
				where a.id=c.mst_id and a.id=b.mst_id and a.id=d.mst_id and c.id=b.mast_dtl_id and c.id=d.dtls_id and a.company_id in($comp_id) and a.location_id in($loc_id) and a.floor_id in($floor_id)  and d.po_id in($po_ids) and a.is_deleted=0 and c.is_deleted=0 and d.is_deleted=0 and d.status_active=1"; // and a.line_number in($line_ids)
				$sql_op_res = sql_select($sql_op);
				$operator_array = array();
				foreach ($sql_op_res as $key => $val) 
				{
					$operator_array[$val[csf('company_id')]][$val[csf('location_id')]][$val[csf('line_number')]] += $val[csf('operator')];
				}
				// ================================= FOR PRODUCTION QUANTITY ==========================================
				$total_sewing_data_arr=array();
				$total_sewing_data_arr2=array();
				$total_sewing_data_arr3=array();
				
				$tot_sewing_prod=sql_select( "select company_id,po_break_down_id,location,production_date,item_number_id,sewing_line,prod_reso_allo,production_type,production_quantity as prod_quantity from pro_garments_production_mst where company_id in($company_id) and status_active=1 and is_deleted=0 and production_type in(5,4) $location_id_cond_sewing");
				foreach($tot_sewing_prod as $row) 
				{
					 $ddate=add_date($start_date_db,$d);
					 $prod_date=date("M-d",strtotime($row[csf('production_date')]));
					 $prod_reso_allo=$row[csf('prod_reso_allo')]; 

					 if($row[csf('production_type')]==5){
						$total_sewing_data_arr3[$prod_date]+=$row[csf('prod_quantity')];
						//$total_sewing_data_arr2[$row[csf('location')]][$row[csf('po_break_down_id')]][$row[csf('item_number_id')]]['qty']+=$row[csf('prod_quantity')];
						
						
						$total_sewing_data_arr[$row[csf('company_id')]][$row[csf('location')]][$row[csf('po_break_down_id')]][$row[csf('item_number_id')]]['prod_reso_allo']=$row[csf('prod_reso_allo')];
						
						if($prod_reso_allo==0)
						{
						$total_sewing_data_arr[$row[csf('company_id')]][$row[csf('location')]][$row[csf('po_break_down_id')]][$row[csf('sewing_line')]][$row[csf('item_number_id')]][$prod_date]['prod_qty']=$row[csf('prod_quantity')];
						}
						else
						{ 
						$line_number=$prod_res_arr[$row[csf('sewing_line')]];
						$total_sewing_data_arr[$row[csf('company_id')]][$row[csf('location')]][$row[csf('po_break_down_id')]][$line_number][$row[csf('item_number_id')]][$prod_date]['prod_qty']=$row[csf('prod_quantity')];
						}
					 }
					 
					 if($row[csf('production_type')]==4){
						 if($prod_reso_allo==0)
						 {
							 $total_sewing_input_data_arr[$row[csf('company_id')]][$row[csf('location')]][$row[csf('po_break_down_id')]][$row[csf('sewing_line')]][$row[csf('item_number_id')]]+=$row[csf('prod_quantity')];
						 }
						 else
						 { 
							 $line_number=$prod_res_arr[$row[csf('sewing_line')]];
							 $total_sewing_input_data_arr[$row[csf('company_id')]][$row[csf('location')]][$row[csf('po_break_down_id')]][$line_number][$row[csf('item_number_id')]]+=$row[csf('prod_quantity')];
						 }
					 }
					 $production_days_data_arr[$row[csf('company_id')]][$row[csf("location")]][$row[csf("po_break_down_id")]][$row[csf("sewing_line")]][$row[csf("item_number_id")]][$row[csf("production_date")]]=$row[csf("production_date")];

				}
			
				 //echo "select company_id,po_break_down_id,location, item_number_id, sum(production_quantity) as production_quantity from pro_garments_production_mst where status_active=1 and is_deleted=0 and production_type=5   group by po_break_down_id, item_number_id,location";
				$sql_sewing_prod=sql_select( "select company_id,po_break_down_id,location, item_number_id, sum(production_quantity) as production_quantity from pro_garments_production_mst where status_active=1 and is_deleted=0 and production_type=5   group by company_id,po_break_down_id, item_number_id,location");//Output qty//Actual Qty
				foreach($sql_sewing_prod as $row)
				{
					 $ddate=add_date($start_date_db,$d);
					 $prod_date=date("M-d",strtotime($row[csf('production_date')]));
					 $total_sewing_data_arr2[$row[csf('company_id')]][$row[csf('location')]][$row[csf('po_break_down_id')]][$row[csf('item_number_id')]]['qty']=$row[csf('production_quantity')];
				}
				?>
				<table width="<? echo $width; ?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table"  >
					<thead>
						<th width="40">SL</th>
						<th width="90">Buyer / Style Ref/ Job No</th>
						<th width="90">Order No & Ship Date</th>
						<th width="70">Order Qty</th>
						<th width="80">Prod. Qty</th>
						<th width="100">Item Name</th>
						<th width="80">Yet to Prod.</th>
						<th width="50">Early / Late By</th>
						<th width="100">Line No / Input Qty</th>
						<th width="100">Operator</th>
						<th width="80">Line Plan / Line Prod. Total</th>
						<th width="70">Line Avg. / Day</th>
						<th width="100">Particulars</th>
						<?
						for($m=0;$m<$num_days;$m++)
						{
							$ddate=add_date($start_date_db,$m)
							?>
							<th width="80"><? echo  date("M-d",strtotime($ddate)); ?></th>
							<?
						}
							?>
							<th width="100">Total</th>

							
					</thead>
				</table>

					<div style="width:<? echo $width+20; ?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
						<table width="<? echo $width; ?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
							<?
							$j=1;
							$k=1;
							$gr_total=0;
							$gr_date_wise_planed_arr=array();
							foreach($sewing_data as $company_key=>$unit_arr)
							{
								foreach($unit_arr as $unit_key=>$sewing_data_arr) 
								{
									//$f_name = "";
									$unit_total=0;
									foreach ($sewing_data_arr  as $f_id=>$po_data) 
									{
										$floor_total=0;
										//$f_name .= ($f_name == "") ? $floor_library[$f_id]: ",".$floor_library[$f_id];
									
										?>
										<tr  bgcolor="#BEC6BB">
											<td align="left" colspan="<? echo $num_days+14; ?>"><strong>Sewing Unit : <? echo $company_library[$company_key].",".$floor_library[$f_id].",".$location_library[$unit_key]; ?> </strong></td>
										</tr>
										<?
										$actual_line_balance_array=array(); 
										$date_actual_qty_arr=array();
										$date_actual_qty_arr2=array();
										$order_wise_actual_qty_arr=array();
										$date_wise_planed_arr=array();

										foreach($po_data as $po_id=>$linedata)
										{
											$po=0;
											$prod_qty=0;
											$order_check_arr=array();
											$order_qty_arr=array();
											$tdbgcolor="";
											$sub_date_wise_planed_arr=array();
											foreach($linedata as $lineid=>$pdata)
											{
												if ($k%2==0)  
													$bgcolor="#E9F3FF";
												else
													$bgcolor="#FFFFFF";
												$po++;
												if ($po==1){  
													$tdbgcolor=$bgcolor;
												}


												$k++;

												$item_id=$sewing_data[$company_key][$unit_key][$f_id][$po_id][$lineid]['item_id'];
												$prod_qty=$total_sewing_data_arr2[$company_key][$unit_key][$po_id][$item_id]['qty'];

												$production_days=$production_days_data_arr[$company_key][$unit_key][$po_id][$lineid][$item_id]['day'];
												$daily_production=$prod_qty/$production_days;

												$balance=$pdata['po_qty']-$prod_qty;
												$days_required=ceil($balance/$daily_production);

												$to_be_end=add_date(date("Y-m-d",time()),$days_required);
												$late_early=datediff("d",$to_be_end,$pdata['pub_shipment']);

												$color="";
												if($late_early<3)
												{
													$color="red";
												}
												$late_early=$late_early-2;

												$z=1;
												?>
												<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $j; ?>"> 
													<td width="40"  style="background-color: <? if($po !=1){echo $tdbgcolor;}  ?>; color:<? if($po !=1){echo $tdbgcolor;}  ?>"  >
														<? echo $j; ?>
													</td>
													<td width="90"  align="center" style="background-color: <? if($po !=1){echo $tdbgcolor;} ?>; color:<? if($po !=1){echo $tdbgcolor;}  ?>" >
														<p>
															<? 
															echo $buyer_arr[$pdata['buyer']].'<br>'.$pdata['style'].'<br>'.$pdata['job']; 
															?> 
														</p>
													</td>
													<td width="90"  align="center" style="background-color: <? if($po !=1){echo $tdbgcolor;} ?>; color:<? if($po !=1){echo $tdbgcolor;}  ?>" >
														<p>
															<? 
															echo $pdata['po_number'].'<br>'.change_date_format($pdata['pub_shipment']).'<br>'.$pdata['lead_day'].' Days';
															?> 
														</p>
													</td>
													<td width="70"  align="right" style="background-color: <? if($po !=1){echo $tdbgcolor;}  ?>; color:<? if($po !=1){echo $tdbgcolor;}  ?>" >
														<? 
														if ($po==1){
															$order_qty+=$pdata['po_qty'];
														}
														echo number_format($pdata['po_qty']); 
														?> 
													</td>
													<td width="80"  align="right" style="background-color: <? if($po !=1){echo $tdbgcolor;}  ?>; color:<? if($po !=1){echo $tdbgcolor;}  ?>" >
														<? 
														if ($po==1){
															$tot_prod_qty+=$prod_qty;
														}
														echo number_format($prod_qty,0); 
														?>
													</td>
													<td width="100"  align="left" style="background-color: <? if($po !=1){echo $tdbgcolor;}  ?>; color:<? if($po !=1){echo $tdbgcolor;}  ?>" >
														<? 
														echo $garments_item[$pdata['item_id']]; 
														?>
													</td>
													<td width="80"  align="right" style="background-color: <? if($po !=1){echo $tdbgcolor;}  ?>; color:<? if($po !=1){echo $tdbgcolor;}  ?>"> 								
														<? 
														$yet_to_prod=$prod_qty-$pdata['po_qty'];
														if ($po==1){
															$tot_yet_to_prod+=$yet_to_prod;
														}
														echo number_format($yet_to_prod); 
														?> 
													</td>
													<td width="50"  bgcolor="<? echo $color; ?>" style="background-color: <? if($po !=1 && $color==""){echo $tdbgcolor;}else{echo $color;}  ?>; color:<? if($po ==1){echo '';} if($po !=1 && $color!=""){echo $color;}if($po !=1 && $color==""){echo $tdbgcolor;}  ?>" title="Early/Late By" align="center" > 
														<? echo $late_early; ?> 
													</td>

													<td width="100"  align="center" title="Sewing Input" >
														<? 
														$item_id=$sewing_data[$company_key][$unit_key][$f_id][$po_id][$lineid]['item_id'];
														$sewing_input=$total_sewing_input_data_arr[$company_key][$unit_key][$po_id][$lineid][$item_id];
														$tot_input_qty+=$sewing_input;
														echo $line_name_library[$lineid].','.$planwise_data_arr[$pdata['plan_id']].'<br>'.number_format($sewing_input); 
														?> 
													</td>

													<td width="100"  align="right" title="Operator" >
														<? 
														echo $operator_array[$company_key][$unit_key][$lineid];
														?> 
													</td>
													<td width="80"   title="Actual Total"  align="right">

														<?
														$tot_line_prod_qty=0;
														$tot_row=0;
														$actual_qty_tot=0;
														for($p=0;$p<$num_days;$p++)
														{
															$ddate=add_date($start_date_db,$p);
															$pdate=date("M-d",strtotime($ddate));
															$item_id=$sewing_data[$company_key][$unit_key][$f_id][$po_id][$lineid]['item_id'];
															$actual_qty_tot+=$total_sewing_data_arr[$company_key][$unit_key][$po_id][$lineid][$item_id][$pdate]['prod_qty'];
															$tot_row+=count($total_sewing_data_arr[$company_key][$unit_key][$po_id][$lineid][$item_id][$pdate]['prod_qty']);
															$tot_line_prod_qty+=$actual_qty_tot;

														} 
														$data_val=$sewing_data[$company_key][$unit_key][$f_id][$po_id][$lineid]['pdplan_qntyLine'];
														$unit_total+=$data_val;
														$floor_total+=$data_val;
														$tot_actual+=$actual_qty_tot;
														echo $sewing_data[$company_key][$unit_key][$f_id][$po_id][$lineid]['pdplan_qntyLine']."<br/>".number_format($actual_qty_tot); 
														?> 
													</td>
													<td width="70"   title="Actaul Total/Days" align="right" >
														<? 
														echo number_format($actual_qty_tot/$tot_row); 
														?> 
													</td>
													<td width="100"><p>Planed</p> </td>
													<?
													$day_total=0;
													for($m=0;$m<$num_days;$m++)
													{
														?>
														<td align="right" width="80">
															<? 
															$ddate=add_date($start_date_db,$m);
															$pdate=date("M-d",strtotime($ddate));
															$duration=$sewing_data[$company_key][$unit_key][$f_id][$po_id][$lineid][$pdate]['duration'];
															$plan_qty=$sewing_data[$company_key][$unit_key][$f_id][$po_id][$lineid][$pdate]['day_wise'];
															if($plan_qty==0 || $plan_qty=="")
															{
																echo "";	
															}
															else
															{
																echo  number_format($plan_qty);
															}
															?>
														</td>
														<?
														//$date_wise_planed_arr[$pdate]+=$plan_qty;
														$gr_date_wise_planed_arr[$pdate]+=$plan_qty;
														$sub_date_wise_planed_arr[$pdate]+=$plan_qty;
														$day_total+=$plan_qty;
													}
													?>
													<td align="right" width="100"><? echo $day_total;?></td>
												</tr>



												
												<?
												$j++;

											}

											
										}
										?>

									 
										<tr class="tbl_bottom">
											<td  colspan="3">Floor Total</td>
											<td width="70">
												<? 
												echo  number_format($order_qty,0);
												$order_qty_unit+=$order_qty;
												$order_qty_grand+=$order_qty;
												$order_qty=0; 
												?>
											</td>
											<td width="80">
												<? 
												echo number_format($tot_prod_qty,0);
												$prod_qty_unit+=$tot_prod_qty;
												$prod_qty_grand+=$tot_prod_qty;
												$tot_prod_qty=0;
												?>
											</td>
											<td width="100"></td>
											<td width="80">
												<? 
												echo number_format($tot_yet_to_prod,0);
												$yet_prod_qty_unit+=$tot_yet_to_prod;
												$yet_prod_qty_grand+=$tot_yet_to_prod;
												$tot_yet_to_prod=0;
												?>
											</td>
											<td width="50"></td>
											<td width="100">
												<? 
												echo number_format($tot_input_qty,0); 
												$tot_input_qty_grnd +=$tot_input_qty;
												$tot_input_qty_unit+=$tot_input_qty; 
												$tot_input_qty=0;
												?>
											</td>
											<td width="100"></td>
											<td width="80">
												<? 
												echo number_format($tot_actual,0);
												$actual_qty_tot_unit +=$tot_actual;
												$actual_qty_tot_grnd +=$tot_actual; 
												$tot_actual=0;
												?>
											</td>
											<td width="70"></td>
											<td width="100"></td>
											<? 
											$order_wise_actual_qty=0;
											//$sub_day_total=0;
											for($m=0;$m<$num_days;$m++)
											{
												$ddate=add_date($start_date_db,$m);
												$pdate=date("M-d",strtotime($ddate));
												$item_id=$sewing_data[$company_key][$unit_key][$f_id][$po_id][$lineid]['item_id'];
												$actual_qty=$total_sewing_data_arr[$company_key][$unit_key][$po_id][$lineid][$item_id][$pdate]['prod_qty'];
												$order_wise_actual_qty=$order_wise_actual_qty_arr[$pdate];
												?>
												<td width="80">
													<? 
													echo $sub_date_wise_planed_arr[$pdate] ;
													$order_wise_actual_qty_arr[$pdate]=0;
													?>
												</td>
												<?
												$sub_day_total+=$sub_date_wise_planed_arr[$pdate];
												$date_wise_planed_arr[$pdate]+=$sub_date_wise_planed_arr[$pdate];
											}
											unset($order_wise_actual_qty_arr[$pdate]); 
											?>
											<td width="100"><? echo $floor_total;$sub_day_total=0; ?></td>
										</tr> 
									 <?
									}
									?>
										 

									<tr class="tbl_bottom">
										<td colspan="3"> Unit Wise  Total</td>
										<td width="70">
											<? 
											echo number_format($order_qty_unit,0);
											$order_qty_unit=0;
											?>
										</td>
										<td width="80">
											<? 
											echo number_format($prod_qty_unit,0);$prod_qty_unit=0;
											?>
										</td>
										<td width="100"></td>
										<td width="80">
											<? 
											echo number_format($yet_prod_qty_unit,0); 
											$yet_prod_qty_unit=0;
											?>
										</td>
										<td width="50"></td>
										<td width="100">
											<? 
											echo number_format($tot_input_qty_unit,0); 
											$tot_input_qty_unit=0; 
											?>
										</td>
										<td width="100"></td>
										<td width="80">
											<? 
											echo number_format($actual_qty_tot_unit,0); 
											$actual_qty_tot_unit=0; 
											?>
										</td>
										<td width="70"></td>
										<td width="100"></td>
										<?
										$total_actual_qty=0;$actual_line_balance_array=array();
										$unit_wise_day_total=0;
										for($m=0;$m<$num_days;$m++)
										{
											$ddate=add_date($start_date_db,$m);
											$pdate=date("M-d",strtotime($ddate));
											$date_actual_qty_arr[$pdate];
											?>
											<td width="80">
												<? 
												echo number_format($date_wise_planed_arr[$pdate]); 
												?>
											</td>
											<?
											$date_actual_qty_arr2[$pdate]+=$date_actual_qty_arr[$pdate];
											$unit_wise_day_total+=number_format($date_wise_planed_arr[$pdate]);
										}
										?>
										<td width="100"><? echo $unit_total;?></td>
									</tr>

									<?
									$gr_total+=$unit_total;
								} 
							}
	                    if($num_days!="") //Grand Total
	                    {  
	                    	?>				
	                    	<tr class="tbl_bottom">
	                    		<td colspan="3"> Grand Total </td>
	                    		<td width="70"><? echo  number_format($order_qty_grand,0);?></td>
	                    		<td width="80"><? echo  number_format($prod_qty_grand,0);?></td>
								<td width="100"></td>
	                    		<td width="80"><? echo  number_format($yet_prod_qty_grand,0);?></td>
	                    		<td width="50"></td>
	                    		<td width="100"><? echo number_format($tot_input_qty_grnd,0);?></td>
								<td width="100"></td>
	                    		<td width="80"><? echo number_format($actual_qty_tot_grnd,0);?></td>
	                    		<td width="70"></td>
	                    		<td width="100"></td>
	                    		<?
	                    		$gr_wise_day_total=0;
	                    		for($m=0;$m<$num_days;$m++)
	                    		{
	                    			$ddate=add_date($start_date_db,$m);
	                    			$pdate=date("M-d",strtotime($ddate));
	                    			?>
	                    			<td width="80" align="right"> 
	                    				<? echo number_format($gr_date_wise_planed_arr[$pdate]); ?>
	                    			</td>
	                    			<?
	                    			$gr_wise_day_total+=number_format($gr_date_wise_planed_arr[$pdate]);
	                    		}
	                    		?>
	                    		<td width="100"><? echo $gr_total;?></td>
	                    	</tr>
	                    	<?
	                    }
	                    ?>
	                </table>
	            </div>

				<?
			}

			?>
        </fieldset>
    </div>
                 
                 
                 
                  <br /><br /><br />
                  <?
				   $prod_sewing_data_arr=array();
				  $sql_prod=sql_select( "select po_break_down_id, sum(production_quantity) as production_quantity from pro_garments_production_mst where status_active=1 and is_deleted=0 and production_type=5   group by po_break_down_id ");//Output qty//Actual Qty
					foreach($sql_prod as $row)
					{
						 $prod_sewing_data_arr[$row[csf('po_break_down_id')]]['qty']=$row[csf('production_quantity')];
					}
                  //ppl_sewing_plan_board
				
					if($cbo_date_type==2)
					{
						$sql_unplan=("select $lead_day, b.id as po_id,b.pub_shipment_date,b.po_quantity,b.po_number,a.buyer_name,a.job_no_prefix_num,a.style_ref_no as style 
							from  wo_po_break_down b,wo_po_details_master a where  a.job_no=b.job_no_mst   and a.company_name=$company_id and b.id not in(select a.po_break_down_id  from ppl_sewing_plan_board a,ppl_sewing_plan_board_dtls d where  a.plan_id=d.plan_id and status_active=1 $date_cond ) and b.status_active=1 and b.is_deleted=0  $buyer_id_cond $po_cond $job_no_cond $location_id_cond ");
					}
					else
					{
						$sql_unplan=("select $lead_day, b.id as po_id,b.pub_shipment_date,b.po_quantity,b.po_number,a.buyer_name,a.job_no_prefix_num,a.style_ref_no as style 
							from  wo_po_break_down b,wo_po_details_master a where  a.job_no=b.job_no_mst   and a.company_name=$company_id and b.id not in(select a.po_break_down_id  from ppl_sewing_plan_board a where status_active=1 ) and b.status_active=1 and b.is_deleted=0  $buyer_id_cond $po_cond $date_cond $job_no_cond $location_id_cond ");
					}
		
					$result_data=sql_select($sql_unplan);
		
				  ?>
				  <div>
				  	<fieldset style="width:840" >
				  		<table width="830" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >

				  			<thead>
				  				<tr class="form_caption">
				  					<th colspan="10" align="center" style="border:none;font-size:16px; font-weight:bold"><strong> <? echo 'Unplan Order'; ?></strong></th>
				  				</tr>
				  				<tr>
				  					<th width="30">Sl.</th>    
				  					<th width="100">Buyer</th>
				  					<th width="100">Style Ref.</th>
				  					<th width="100">Job No</th>
				  					<th width="100">Order No</th>
				  					<th width="80">Ship Date</th>
				  					<th width="60">Lead Day</th>
				  					<th width="80">Order Qty</th>
				  					<th width="80">Prod. Qty</th>
				  					<th width="">Yet to Prod.</th>
				  				</tr>
				  			</thead>
				  		</table>
				  		<div style="max-height:350px; width:850px; overflow-y:scroll;" id="scroll_body">
				  			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="830" class="rpt_table"  id="tbl_header"  >
				  				<?
				  				$m=1;$total_po_qty=0;$total_prod_qty=0;$total_yet_prod_qty=0;
				  				foreach($result_data as $unplan)
				  				{
				  					if ($m%2==0)  
				  						$bgcolor="#E9F3FF";
				  					else
				  						$bgcolor="#FFFFFF";
				  					$prod_qnty=$prod_sewing_data_arr[$unplan[csf('po_id')]]['qty'];
				  					$unplan_yet_to_prod=$prod_qnty-$unplan[csf('po_quantity')];

				  					?>
				  					<tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('trun_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="trun_<? echo $m; ?>">
				  						<td width="30"> <? echo $m;?> </td>
				  						<td width="100"><p><? echo $buyer_arr[$unplan[csf('buyer_name')]]; ?></p></td>
				  						<td width="100"><div style="word-wrap:break-word; width:100px;"><? echo $unplan[csf('style')]; ?></div></td>
				  						<td width="100"><p><? echo $unplan[csf('job_no_prefix_num')];  ?></p></td>
				  						<td width="100"><div style="word-wrap:break-word; width:100px;"><? echo $unplan[csf('po_number')]; ?></div></td>
				  						<td width="80"><p><? echo change_date_format($unplan[csf('pub_shipment_date')]); ?></p></td>
				  						<td width="60"><p><?  echo $unplan[csf('date_diff')]; ?></p></td>
				  						<td width="80" align="right"><p><?  echo number_format($unplan[csf('po_quantity')],2); ?></p></td>
				  						<td width="80" align="right"><p><?  echo number_format($prod_qnty,2); ?></p></td>
				  						<td width="" align="right"><p><?  echo number_format($unplan_yet_to_prod,2); ?></p></td>
				  					</tr>

				  					<?
				  					$total_po_qty+=$unplan[csf('po_quantity')];
				  					$total_prod_qty+=$prod_qnty;
				  					$total_yet_prod_qty+=$unplan_yet_to_prod;
				  					$m++;
				  				}
				  				?>
				  				<tr class="tbl_bottom">
				  					<td colspan="7"> Total </td>
				  					<td><? echo number_format($total_po_qty,2);?> </td>
				  					<td><? echo number_format($total_prod_qty,2);?>  </td>
				  					<td><? echo number_format($total_yet_prod_qty,2);?>  </td>

				  				</tr>
				  			</table>
				  		</div>
				  	</fieldset>
				  </div>
		<?
	foreach (glob("$user_name*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename="".$user_name."_".$name.".xls";
	echo "$total_data####$filename####$type";
	exit();
}
?>
      
 