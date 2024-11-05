<? 
session_start();
header('Content-type:text/html; charset=utf-8');
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:../../../login.php");
require_once('../../../includes/common.php');
$user_name=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

/*if($action=="print_button_variable_setting")
{
	 
	$print_report_format_arr = return_library_array("select format_id,format_id from lib_report_template where template_name in($data) and module_id=4 and report_id=78 and is_deleted=0 and status_active=1","format_id","format_id");
	echo "print_report_button_setting('".implode(',',$print_report_format_arr)."');\n";
	exit(); 
}*/

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "- All Buyer -", $selected, "" );
	exit();
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 120, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id in($data) order by location_name","id,location_name", 0, "--Select Location--", $selected, "" );
	exit();     	 
}

if ($action=="load_drop_down_floor")
{
	extract($_REQUEST);
	list($company_id,$location_id)=explode('__',$data);
	echo create_drop_down( "cbo_floor_id", 120, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and company_id in($company_id) and location_id in($location_id) and production_process=5 order by floor_name","id,floor_name", 0, "--Select Floor--", $selected, "" );
	exit();     	 
}

if ($action=="load_drop_down_line")
{
	extract($_REQUEST);
	list($company_id,$location_id,$floor_id)=explode('__',$data);
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name in($company_id) and variable_list=23 and is_deleted=0 and status_active=1");
	
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

		echo create_drop_down( "cbo_line_id", 120,$line_array,"", 0, "-- Select Line--", $selected, "",0,0 );
	}
	else
	{
		$cond .= " and location_name in($location_id)";
		$cond .= " and floor_name in($floor_id)";

		echo create_drop_down( "cbo_line_id", 120, "select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1 and floor_name!=0 $cond order by line_name","id,line_name", 1, "-- Select --", $selected, "",0,0 );
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
								echo create_drop_down( "cbo_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'floor_wise_loading_plan_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
							?>
                        </td>
                        <td align="center" id="buyer_td">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_id').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_job_no_search_list_view', 'search_div', 'floor_wise_loading_plan_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:70px;" />
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
								echo create_drop_down( "cbo_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'floor_wise_loading_plan_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
							?>
                        </td>
                        <td align="center" id="buyer_td">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_id').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_po_no_search_list_view', 'search_div', 'floor_wise_loading_plan_report_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:70px;" />
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
	
	$sql="select a.id, a.po_number, b.job_no_prefix_num, b.job_no, b.buyer_name, b.style_ref_no from wo_po_details_master b, wo_po_break_down a where b.job_no=a.job_no_mst and b.company_name=$company_id and b.is_deleted=0 $buyer_name $job_no_cond ORDER BY b.job_no_prefix_num DESC";
	$buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name");
	$arr=array(1=>$buyer);
	
	echo  create_list_view("list_view", "Job No,Buyer,Style Ref.,Order No", "80,110,150,180","600","300",0, $sql, "js_set_value", "id,po_number", "", 1, "0,buyer_name,0,0,0", $arr , "job_no_prefix_num,buyer_name,style_ref_no,po_number", "floor_wise_loading_plan_report_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0','') ;
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
	
	$company_lib_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );

	//--------------------------------------------------------------------------------------------------------------------
	
		if(str_replace("'","",$cbo_buyer_name)==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and d.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
			}
			else
			{
				$buyer_id_cond="";
			}
		}
		else
		{
			$buyer_id_cond=" and d.buyer_name=$cbo_buyer_name";
		}
		
		if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and d.job_no_prefix_num in ('$job_no') ";
		
		if ($cbo_location_id==0){$location_id_cond="";$location_id_cond2="";}
		else{
			$location_id_cond=" and e.location_name in($cbo_location_id) ";
			$location_id_cond2=" and location_id in($cbo_location_id) ";
		}
		
		
		if ($cbo_floor_id==0) $floor_id_cond=""; else $floor_id_cond=" and e.floor_name in($cbo_floor_id) ";
		if ($cbo_line_id==0) $line_id_cond=""; else $line_id_cond=" and a.line_id in($cbo_line_id) ";
		if($order_no=="")
		{
			$po_cond="";
		}
		else
		{
			if(str_replace("'","",$hide_order_id)!="")
			{
				$po_id=str_replace("'","",$hide_order_id);
				$po_cond="and c.id in(".$po_id.")";
			}
			else
			{
				$po_number=trim($order_no)."%";
				$po_cond="and c.po_number like '$po_number'";
			}
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
		
		
		if($cbo_date_type==1){
			$date_cond=" and c.pub_shipment_date between '$start_date' and '$end_date'";
		}
		else
		{
			$date_cond=" and b.plan_date between '$start_date' and '$end_date'";
		}
		
	
		
		
		$working_day_arr=return_library_array( "select a.comapny_id,count(b.day_status) as total_working_day  from lib_capacity_calc_mst a,lib_capacity_calc_dtls b  where a.id=b.mst_id and b.date_calc between '$start_date' and '$end_date' and b.day_status=1 and a.comapny_id in($company_id) $location_id_cond2  and a.status_active=1 and a.is_deleted=0 group by a.comapny_id", "comapny_id", "total_working_day"  );
	
		
		$sql="select a.company_id,a.po_break_down_id,a.line_id,a.plan_id, b.plan_date,b.plan_qnty,(c.unit_price/d.total_set_qnty) as rate,d.currency_id,d.buyer_name,e.location_name,e.floor_name as floor_id,f.floor_name
  from ppl_sewing_plan_board a, ppl_sewing_plan_board_dtls b,wo_po_break_down c,wo_po_details_master d,lib_sewing_line e,lib_prod_floor f
 where a.plan_id = b.plan_id and a.po_break_down_id=c.id and c.job_no_mst=d.job_no and   a.line_id=e.id and e.floor_name=f.id and a.status_active = 1 and a.is_deleted = 0 and f.production_process=5 and a.company_id in($company_id) $po_cond $job_no_cond $floor_id_cond $line_id_cond $date_cond $buyer_id_cond
       order by f.floor_name";	
		$sql_result_arr=sql_select($sql );
		$planDataArr=array();
		foreach($sql_result_arr as $row)
		{
			 $planDataArr[$row[csf('company_id')]]['floor_id'][$row[csf('floor_id')]]=$row[csf('floor_name')];
			 $planDataArr[$row[csf('company_id')]]['buyer_id'][$buyer_arr[$row[csf('buyer_name')]]]=$row[csf('buyer_name')];
			
			 $planDataArr[$row[csf('company_id')]]['plan_qty'][$row[csf('buyer_name')]][$row[csf('floor_id')]]+=$row[csf('plan_qnty')];
			 $planDataArr[$row[csf('company_id')]]['plan_val'][$row[csf('buyer_name')]][$row[csf('floor_id')]]+=($row[csf('rate')]*$row[csf('plan_qnty')]);
			 $planDataArr[$row[csf('company_id')]]['grand_plan_qty'][$row[csf('floor_id')]]+=$row[csf('plan_qnty')];
			 $planDataArr[$row[csf('company_id')]]['grand_plan_val'][$row[csf('floor_id')]]+=($row[csf('rate')]*$row[csf('plan_qnty')]);
			 
		}
		//ksort($planDataArr['buyer_id']);
			$company_id_arr=explode(',',$company_id);
			
			
			$totalFloor=count($planDataArr[$company_id_arr[0]]['floor_id']);
			$width=($totalFloor*283)+358;
			ob_start();	
		?>
		<div>
			<fieldset style="width:<? echo $width+10; ?>px;">
				<table width="<? echo $width; ?>" cellspacing="0" cellpadding="0" border="0" rules="" class=""  >
					<thead>
						<tr>
							<th align="center"  colspan="<? echo $totalFloor+2;?>">
                            	<strong style="font-size:16px;">Floor Wise Loading Plan Report</strong>
                            </th>
						</tr>
						<tr>
							<th align="center"  colspan="<? echo $totalFloor+2;?>">
                            	From: <? echo change_date_format(str_replace("'","",$txt_date_from));?> 
                                to <? echo change_date_format(str_replace("'","",$txt_date_to));?>
                            </th>
						</tr>

					</thead>
				</table>
                
               <? 
                
               
			   
			    foreach($company_id_arr as $company_id)
                {
                    foreach($planDataArr[$company_id]['buyer_id'] as $buyer_name=>$buyer_id)
                    {
						$buyerDataArr['pcs'][$buyer_id]+=array_sum($planDataArr[$company_id]['plan_qty'][$buyer_id]);
						$buyerDataArr['fob'][$buyer_id]+=array_sum($planDataArr[$company_id]['plan_val'][$buyer_id]);
					}
				}  
				
				echo '
					<h2>Buyer Wise Summary</h2>
					<table width="" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" >
						<thead>
                            <th width="120">Buyer</th>
                            <th width="80">Pcs</th>
                            <th width="80">FOB($)</th>
                        </thead>';
					$ii=1;
					foreach($buyerDataArr['pcs'] as $buyer_id=>$pcsQty){
                       $bgcolor=($ii%2==0)?"#E9F3FF":"#FFFFFF";
					    $onclick="change_color('trr_".$ii."','".$bgcolor."')";
						
						echo '
						<tr bgcolor="'.$bgcolor.'" id="trr_'.$ii.'" onClick="'.$onclick.'" >
                            <td width="120">'.$buyer_arr[$buyer_id].'</td>
                            <td width="80" align="right">'.$pcsQty.'</td>
                            <td width="80" align="right">'.$buyerDataArr['fob'][$buyer_id].'</td>
                        </tr>';
					$ii++;
					}
					
					echo '
						<tfoot>
                            <th width="120">G.Total</th>
                            <th width="80" align="right">'.array_sum($buyerDataArr['pcs']).'</th>
                            <th width="80" align="right">'.array_sum($buyerDataArr['fob']).'</th>
                        </tfoot>
					</table>';




                foreach($company_id_arr as $company_id)
                {
				
					$totalFloor=count($planDataArr[$company_id]['floor_id']);
					$width=($totalFloor*283)+358;
				
				?>
                <h2><? echo $company_lib_arr[$company_id];?></h2>
				<table width="<? echo $width; ?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table"  >
					<thead>
						<tr>
                            <th width="35" rowspan="2">SL</th>
                            <? foreach($planDataArr[$company_id]['floor_id'] as $floor_id=>$floor_name){?>
                            <th colspan="3"><? echo $floor_name;?></th>
                            <? } ?>
                            <th colspan="3">Total</th>
                        </tr>
						<tr>
                            <? foreach($planDataArr[$company_id]['floor_id'] as $floor_id=>$floor_name){?>
                            <th width="120">Buyer</th>
                            <th width="80">Pcs</th>
                            <th width="80">FOB($)</th>
                            <? } ?>
                            <th width="120">Buyer</th>
                            <th width="100">Pcs</th>
                            <th>FOB($)</th>
                        </tr>
					</thead>
				</table>
				
                <div style="width:<? echo $width+20; ?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
                    <table width="<? echo $width; ?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
                    <?
                    $i=1;
                    foreach($planDataArr[$company_id]['buyer_id'] as $buyer_name=>$buyer_id)
                    {
                    $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
                    ?>
                        <tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $i.$company_id;?>" onClick="change_color('tr_<? echo $i.$company_id; ?>','<? echo $bgcolor; ?>')">
                            <td width="35" align="center"><? echo $i;?></td>
                            
							<? foreach($planDataArr[$company_id]['floor_id'] as $floor_id=>$floor_name){?>
                            <td width="120"><b>&nbsp;<? echo $buyer_name;?></b></td>
                            <td width="80" align="right" bgcolor="#FDE9D9"><? echo $planDataArr[$company_id]['plan_qty'][$buyer_id][$floor_id];?></td>
                            <td width="80" align="right"><? echo number_format($planDataArr[$company_id]['plan_val'][$buyer_id][$floor_id],2);?></td>
                            <? } ?>
                            <td width="120"><b><? echo $buyer_name;?></b></td>
                            <td width="100" align="right" bgcolor="#FDE9D9"><? echo array_sum($planDataArr[$company_id]['plan_qty'][$buyer_id]);?></td>
                            <td align="right"><? echo number_format(array_sum($planDataArr[$company_id]['plan_val'][$buyer_id]),2);?></td>
                        </tr>
                    <? $i++;} ?>
						<tfoot>
                            <tr>
                            <th></th>
								<? foreach($planDataArr[$company_id]['floor_id'] as $floor_id=>$floor_name){?>
                                <th align="right"><b>G. Total</b></th>
                                <th align="right" bgcolor="#FDE9D9"><? echo $planDataArr[$company_id]['grand_plan_qty'][$floor_id];?></th>
                                <th align="right"><? echo number_format($planDataArr[$company_id]['grand_plan_val'][$floor_id],2);?></th>
                                <? } ?>
                                <th><b>G. Total</b></th>
                                <th align="right" bgcolor="#FDE9D9"><? echo array_sum($planDataArr[$company_id]['grand_plan_qty']);?></th>
                                <th align="right"><? echo number_format(array_sum($planDataArr[$company_id]['grand_plan_val']),2);?></th>
                            </tr>
                            <tr>
                            <th></th>
								<? foreach($planDataArr[$company_id]['floor_id'] as $floor_id=>$floor_name){?>
                                <th align="right" title="Working Day:<? echo array_sum($working_day_arr);?>"><b>Day Avg.</b></th>
                                <th align="right" bgcolor="#FDE9D9"><? echo number_format($planDataArr[$company_id]['grand_plan_qty'][$floor_id]/array_sum($working_day_arr),2);?></th>
                                <th align="right"><? echo number_format(($planDataArr[$company_id]['grand_plan_val'][$floor_id]/array_sum($working_day_arr)),2);?></th>
                                <? } ?>
                                <th title="Working Day:<? echo array_sum($working_day_arr);?>"><b>Day Avg.</b></th>
                                <th align="right" bgcolor="#FDE9D9"><? echo number_format((array_sum($planDataArr[$company_id]['grand_plan_qty'])/array_sum($working_day_arr)),2);?></th>
                                <th align="right"><? echo number_format((array_sum($planDataArr[$company_id]['grand_plan_val'])/array_sum($working_day_arr)),2);?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
              <? } ?> 
                
                
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
      
 