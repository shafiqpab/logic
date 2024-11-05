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
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "- All Buyer -", $selected, "" );
	exit();
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 150, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id in($data) order by location_name","id,location_name", 1, "--Select Location--", $selected, "" );
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
								echo create_drop_down( "cbo_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $companyID, "load_drop_down( 'month_wise_sewing_plan_status_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_id').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_job_no_search_list_view', 'search_div', 'month_wise_sewing_plan_status_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:70px;" />
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
	list($company_id)=explode('_',$data);
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
								echo create_drop_down( "cbo_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $company_id, "load_drop_down( 'month_wise_sewing_plan_status_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_id').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_po_no_search_list_view', 'search_div', 'month_wise_sewing_plan_status_report_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:70px;" />
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
	
	echo  create_list_view("list_view", "Job No,Buyer,Style Ref.,Order No", "80,110,150,180","600","300",0, $sql, "js_set_value", "id,po_number", "", 1, "0,buyer_name,0,0,0", $arr , "job_no_prefix_num,buyer_name,style_ref_no,po_number", "month_wise_sewing_plan_status_report_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0','') ;
	disconnect($con);
	exit(); 
}

if($action=="report_generate_btn_1")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_id=str_replace("'","",$cbo_company_id);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_location_id=str_replace("'","",$cbo_location_id);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$job_no=str_replace("'","",$txt_job_no);
	$order_no=str_replace("'","",$txt_order_no);
	$cbo_date_type=str_replace("'","",$cbo_date_type);
	$cbo_order_status=str_replace("'","",$cbo_order_status);
	
	$company_lib_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
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
			$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
		}
		
		if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ('$job_no') ";
		if ($cbo_location_id==0) $location_id_cond=""; else $location_id_cond=" and c.location_id in($cbo_location_id) ";
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
			$date_cond=" and pd.plan_date between '$start_date' and '$end_date'";
		}
		else
		{
			$date_cond=" and b.pub_shipment_date between '$start_date' and '$end_date'";
		}
		
		if($cbo_order_status>0){$order_status_cond=" and b.IS_CONFIRMED=$cbo_order_status";}
		if ($cbo_location_id==0) $location_id_cond_res=""; else $location_id_cond_res=" and location_id=$cbo_location_id ";
		if ($cbo_location_id==0) $location_id_cond_sewing=""; else $location_id_cond_sewing=" and location=$cbo_location_id ";
		
	
		
		$sql="SELECT b.IS_CONFIRMED,b.JOB_NO_MST,b.UNIT_PRICE,pp.PO_BREAK_DOWN_ID,pp.ITEM_NUMBER_ID,pp.SMV,pd.PLAN_QNTY as PLAN_QNTY, pd.plan_date AS PLAN_DATE,pd.WORKING_HOUR,a.buyer_name as BUYER_NAME,pd.ID
		from  wo_po_break_down b,wo_po_details_master a,ppl_sewing_plan_board_powise pp,ppl_sewing_plan_board_dtls pd, ppl_sewing_plan_board c where  a.id=b.job_id and b.id=pp.po_break_down_id and pp.plan_id=pd.plan_id and pp.plan_id=c.plan_id  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.company_id in($company_id) $buyer_id_cond $po_cond $date_cond $job_no_cond $location_id_cond $order_status_cond
		group by b.IS_CONFIRMED,b.JOB_NO_MST,b.UNIT_PRICE,pp.PO_BREAK_DOWN_ID,pp.ITEM_NUMBER_ID,pp.SMV,pd.PLAN_QNTY, pd.plan_date, pd.WORKING_HOUR, a.buyer_name,pd.ID
		order by pd.plan_date";//and c.company_id in($company_id)
		    // echo $sql;die; 
		$sql_data_result=sql_select($sql);
		foreach( $sql_data_result as $row)
		{
			$joArr[$row[JOB_NO_MST]]=$row[JOB_NO_MST];
		}
				
	
			
			
		$precostSql="SELECT JOB_NO,CM_COST,COSTING_PER_ID from WO_PRE_COST_DTLS where STATUS_ACTIVE=1 and IS_DELETED=0 ".where_con_using_array($joArr,1,'JOB_NO')."	";
		// echo $precostSql;die();		
		$precostSql_result=sql_select($precostSql);
		foreach( $precostSql_result as $row)
		{
			$costing_per_value=0;
			if($row[COSTING_PER_ID]==1) $costing_per_value=12;
			else if($row[COSTING_PER_ID]==2) $costing_per_value=1;
			else if($row[COSTING_PER_ID]==3) $costing_per_value=24;
			else if($row[COSTING_PER_ID]==4) $costing_per_value=36;
			else if($row[COSTING_PER_ID]==5) $costing_per_value=48;
			
			$jobWiseCMArr[$row[JOB_NO]]=$row[CM_COST]/$costing_per_value;

		}
			
		//echo $precostSql;	
		$date_chk_array = array();
		foreach( $sql_data_result as $row)
		{
			// if(!in_array($row[PLAN_DATE],$date_chk_array))
			// {
				$dateKey=date('M-Y',strtotime($row[PLAN_DATE]));
				$monthArr[$dateKey]=$dateKey;
				$dataArr[$row[BUYER_NAME]][$row[IS_CONFIRMED]][$dateKey][PLAN_QNTY][$row[ID]]=$row[PLAN_QNTY];
				// $dataArr[$row[BUYER_NAME]][$row[IS_CONFIRMED]][$dateKey][PLANNED_SAH][$row[ID]]=$row[WORKING_HOUR];
				$dataArr[$row[BUYER_NAME]][$row[IS_CONFIRMED]][$dateKey][PLANNED_SAH][$row[ID]]=($row[PLAN_QNTY]*$row[SMV])/60;
				$dataArr[$row[BUYER_NAME]][$row[IS_CONFIRMED]][$dateKey][FOB][$row[ID]]=($row[PLAN_QNTY]*$row[UNIT_PRICE]);
				$dataArr[$row[BUYER_NAME]][$row[IS_CONFIRMED]][$dateKey][CM][$row[ID]]=($row[PLAN_QNTY]*$jobWiseCMArr[$row[JOB_NO_MST]]);
				// $dataArr[$row[BUYER_NAME]][$row[IS_CONFIRMED]][$dateKey][PLANNED_SAH]+=($row[PLAN_QNTY]*$row[SMV])/60;
				// $dataArr[$row[BUYER_NAME]][$row[IS_CONFIRMED]][$dateKey][FOB]+=($row[PLAN_QNTY]*$row[UNIT_PRICE]);
				// $dataArr[$row[BUYER_NAME]][$row[IS_CONFIRMED]][$dateKey][CM]+=($row[PLAN_QNTY]*$jobWiseCMArr[$row[JOB_NO_MST]]);
				$date_chk_array[$row[PLAN_DATE]] = $row[PLAN_DATE];
			// }
			
		}
		// echo "<pre>";print_r($dataArr);die();
				
	$totalMonth=count($monthArr);	
	$date_type_arr=array(1=>'Shipment Date',2=>' Plan Date');
			
	$width=($totalMonth*80)+300;
	ob_start();	
		?>
		<div>
			<fieldset style="width:<? echo $width+10; ?>px; margin:1px auto;">
				<table width="<? echo $width; ?>" cellspacing="0" cellpadding="0" border="0" rules="" class=""  >
					<thead>
						<tr>
							<th align="center"  colspan="<? echo 3+$totalMonth;?>">
								<strong style="font-size:22px"><?= $company_lib_arr[$company_id];?></strong><br>
                                <strong style="font-size:18px">Month wise Plan  Status </strong><br>
                                <strong style="font-size:12px"><?=$date_type_arr[$cbo_date_type];?>: <?= $start_date.' to ' .$end_date;?></strong><br>
                                <strong style="font-size:12px">Report Generate Time: <?= date('h:i A',time());?></strong>
							</th>
						</tr>

					</thead>
				</table>			

				<table width="<? echo $width; ?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table"  >
					<thead>
						<th width="90">Buyer</th>
						<th width="90">Order Status</th>
						<th width="110">Item/Month</th>
						<? foreach($monthArr as $monthName){?><th width="80"><?= $monthName;?></th><? } ?>
					</thead>
				</table>
				<div style="width:<? echo $width+20; ?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
					<table width="<? echo $width; ?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
                    <? 
					$i=0;
					foreach($dataArr as $buyer_id=>$rowsArr)
					{
						foreach($rowsArr as $isconf=>$bdrows)
						{
							$i++;
							$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";	
							?>
	                        <tr bgcolor="<? echo $bgcolor; ?>">
	                            <td rowspan="6" width="90" valign="middle"><p><?=$buyer_arr[$buyer_id];?></p></td>
	                            <td rowspan="6" width="90" valign="middle"><p><?=$order_status[$isconf];?></p></td>
	                            <td width="110">Planned Qty</td>
	                            <? foreach($monthArr as $mk=>$monthName)
	                            {
	                            	?>
	                            	<td align="right" width="80"><?= fn_number_format(array_sum($dataArr[$buyer_id][$isconf][$mk][PLAN_QNTY]));?></td>
									<? 
								} 
								?>
	                        </tr>
	                        <tr>
	                            <td>Planned SAH</td>
	                            <? foreach($monthArr as $mk=>$monthName){?>
	                            	<td align="right" width="80"><?= fn_number_format(array_sum($dataArr[$buyer_id][$isconf][$mk][PLANNED_SAH]));?></td>
								<? } ?>
	                        </tr>
	                        <tr>
	                            <td>CM</td>
	                            <? foreach($monthArr as $mk=>$monthName){?>
	                            	<td width="80" align="right"><?= fn_number_format(array_sum($dataArr[$buyer_id][$isconf][$mk][CM]),2);?></td>
								<? } ?>
	                        </tr>
	                        <tr>
	                            <td>FOB [USD]</td>
	                            <? foreach($monthArr as $mk=>$monthName){?>
	                            	<td width="80" align="right"><?= fn_number_format(array_sum($dataArr[$buyer_id][$isconf][$mk][FOB]),2);?></td>
								<? } ?>
	                        </tr>
	                        <tr>
	                            <td>Avg. FOB [USD]</td>
	                            <? foreach($monthArr as $mk=>$monthName){?>
	                            	<td width="80" align="right"><?= fn_number_format(array_sum($dataArr[$buyer_id][$isconf][$mk][FOB])/array_sum($dataArr[$buyer_id][$isconf][$mk][PLAN_QNTY]),2);?></td>
								<? } ?>
	                        </tr>
	                        <tr>
	                            <td>EPM [USD]</td>
	                            <? foreach($monthArr as $mk=>$monthName){?>
	                            	<td width="80" align="right"><?= fn_number_format((array_sum($dataArr[$buyer_id][$isconf][$mk][CM])/array_sum($dataArr[$buyer_id][$isconf][$mk][PLANNED_SAH]))/60,2);?></td>
								<? } ?>
	                        </tr>
	                        <tr><td bgcolor="#CCCCCC" colspan="<?= $totalMonth+3;?>">&nbsp;  </td></tr>
	                        <? 
	                    }
	                } 
	                ?>
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


if($action=="report_generate_btn_2")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_id=str_replace("'","",$cbo_company_id);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_location_id=str_replace("'","",$cbo_location_id);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$job_no=str_replace("'","",$txt_job_no);
	$order_no=str_replace("'","",$txt_order_no);
	$cbo_date_type=str_replace("'","",$cbo_date_type);
	$cbo_order_status=str_replace("'","",$cbo_order_status);
	
	$company_lib_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$location_arr=return_library_array( "select id, location_name from lib_location", "id", "location_name"  );
	//--------------------------------------------------------------------------------------------------------------------
	
		
		
		
		
		
		
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
		
 		$working_hour = return_field_value("working_hour","lib_standard_cm_entry","company_id=$company_id and status_active=1","working_hour");
		
		/*if ($cbo_location_id>0){$location_id_cond=" and a.LOCATION_ID =$cbo_location_id ";}
		$planSql="SELECT a.COMPANY_ID,a.LOCATION_ID,B.PLAN_DATE,B.WORKING_HOUR,B.PLAN_QNTY  from PPL_SEWING_PLAN_BOARD a,PPL_SEWING_PLAN_BOARD_DTLS b where A.PLAN_ID=B.PLAN_ID and B.PLAN_DATE between '$start_date' and '$end_date' and a.COMPANY_ID in($company_id) $location_id_cond";*/
		if ($cbo_location_id>0){$location_id_cond=" and c.LOCATION_ID =$cbo_location_id ";}
		$planSql="SELECT c.COMPANY_ID,c.LOCATION_ID,pd.WORKING_HOUR,pd.PLAN_QNTY as PLAN_QNTY, pd.plan_date AS PLAN_DATE,pd.ID,pp.smv as SMV
		from  wo_po_break_down b,ppl_sewing_plan_board_powise pp,ppl_sewing_plan_board_dtls pd, ppl_sewing_plan_board c 
		where b.id=pp.po_break_down_id and pp.plan_id=pd.plan_id and pp.plan_id=c.plan_id  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.company_id in($company_id) and pd.PLAN_DATE between '$start_date' and '$end_date' $location_id_cond
		group by c.company_id,c.location_id,pd.working_hour,pd.plan_qnty, pd.plan_date,pd.id,pp.smv
		order by pd.plan_date";
		//echo $planSql;
		$planSqlResult=sql_select($planSql);
		foreach( $planSqlResult as $row)
		{
			$dateKey=date('M-Y',strtotime($row[PLAN_DATE]));
			$monthArr[$dateKey]=$dateKey;

			// $dataArr[$row[COMPANY_ID]][$row[LOCATION_ID]][$dateKey][PLAN_HOUR]+=$row[WORKING_HOUR];
			// $dataArr[$row[COMPANY_ID]][$row[LOCATION_ID]][$dateKey][PLAN_QNTY]+=$row[PLAN_QNTY];
			// $dataArr[$row[COMPANY_ID]][$row[LOCATION_ID]][$dateKey][PLAN_HOUR][$row[ID]]=$row[WORKING_HOUR];
			$dataArr[$row[COMPANY_ID]][$row[LOCATION_ID]][$dateKey][PLAN_HOUR][$row[ID]]=($row[PLAN_QNTY]*$row[SMV])/60;
			$dataArr[$row[COMPANY_ID]][$row[LOCATION_ID]][$dateKey][PLAN_QNTY][$row[ID]]=$row[PLAN_QNTY];
		}
		
		//capacity cal.........................
		/* 	$capa_sql="SELECT  a.COMAPNY_ID,a.LOCATION_ID,a.EFFI_PERCENT,a.BASIC_SMV,a.AVG_MACHINE_LINE,c.NO_OF_LINE,b.WORKING_DAY,c.CAPACITY_MIN,c.CAPACITY_PCS,c.DATE_CALC from  LIB_CAPACITY_CALC_MST a,LIB_CAPACITY_YEAR_DTLS b,LIB_CAPACITY_CALC_DTLS c where a.id=b.mst_id and a.id=c.mst_id  and b.MONTH_ID=c.MONTH_ID and b.mst_id=c.mst_id and c.DAY_STATUS=1  and a.status_active=1 and a.is_deleted=0 and a.COMAPNY_ID in($company_id)  and c.DATE_CALC between '$start_date' and '$end_date' $location_id_cond 
		order by c.DATE_CALC";//and c.company_id in($company_id) */
		if ($cbo_location_id>0){$location_id_cond=" and a.LOCATION_ID =$cbo_location_id ";}
		$capa_sql="SELECT  a.COMAPNY_ID,a.LOCATION_ID,a.EFFI_PERCENT,a.BASIC_SMV,a.AVG_MACHINE_LINE,c.NO_OF_LINE,c.CAPACITY_MIN,c.CAPACITY_PCS,c.DATE_CALC from  LIB_CAPACITY_CALC_MST a,LIB_CAPACITY_CALC_DTLS c where a.id=c.mst_id and c.DAY_STATUS=1  and a.status_active=1 and a.is_deleted=0 and a.COMAPNY_ID in($company_id)  and c.DATE_CALC between '$start_date' and '$end_date' $location_id_cond 
		order by c.DATE_CALC";//and c.company_id in($company_id)
		//    echo $capa_sql;die; 
		$capa_sql_result=sql_select($capa_sql);
		foreach( $capa_sql_result as $row)
		{
			$dateKey=date('M-Y',strtotime($row[DATE_CALC]));
			$monthArr[$dateKey]=$dateKey;
			// $dataArr[$row[COMAPNY_ID]][$row[LOCATION_ID]][$dateKey][CapacityHours]+=($row[CAPACITY_MIN]/60);
			// $dataArr[$row[COMAPNY_ID]][$row[LOCATION_ID]][$dateKey][ExpectedCapacity ]+=($row[CAPACITY_MIN]/60)/$row[EFFI_PERCENT];

			// $dataArr[$row[COMAPNY_ID]][$row[LOCATION_ID]][$dateKey][CapacityHours]=($row[AVG_MACHINE_LINE]*$row[WORKING_DAY]*$working_hour);
			// $dataArr[$row[COMAPNY_ID]][$row[LOCATION_ID]][$dateKey][ExpectedCapacity]=(($row[AVG_MACHINE_LINE]*$row[WORKING_DAY]*$working_hour)*$row[EFFI_PERCENT])/100;
			$dataArr[$row[COMAPNY_ID]][$row[LOCATION_ID]][$dateKey][CapacityHours]+=$row[AVG_MACHINE_LINE]*$row[NO_OF_LINE]*$working_hour;
			$dataArr[$row[COMAPNY_ID]][$row[LOCATION_ID]][$dateKey][ExpectedCapacity]+=(($row[AVG_MACHINE_LINE]*$row[NO_OF_LINE]*$working_hour)*$row[EFFI_PERCENT])/100;
			// echo $row[AVG_MACHINE_LINE].'**'.$row[NO_OF_LINE].'**'.$row[WORKING_DAY].'**'.$working_hour;die;

		}
		//.........................capacity cal  end		
	
			
			
				
	$totalMonth=count($monthArr);	
	$date_type_arr=array(1=>'Shipment Date',2=>' Plan Date');
			
	$width=($totalMonth*80)+450;
	ob_start();	
		?>
		<div>
			<fieldset style="width:<? echo $width+10; ?>px; margin:1px auto;">
				<table width="<? echo $width; ?>" cellspacing="0" cellpadding="0" border="0" rules="" class=""  >
					<thead>
						<tr>
							<th align="center"  colspan="<? echo 3+$totalMonth;?>">
								<strong style="font-size:22px"><?= $company_lib_arr[$company_id];?></strong><br>
                                <strong style="font-size:18px">Month wise Plan  Status </strong><br>
                                <strong style="font-size:12px"><?=$date_type_arr[$cbo_date_type];?>: <?= $start_date.' to ' .$end_date;?></strong><br>
                                <strong style="font-size:12px">Report Generate Time: <?= date('h:i A',time());?></strong>
							</th>
						</tr>

					</thead>
				</table>			

				<table width="<? echo $width; ?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table"  >
					<thead>
						<th width="150">Company</th>
						<th width="90">Location</th>
						<th>Item/Month</th>
						<? foreach($monthArr as $monthName){?><th width="80"><?= $monthName;?></th><? } ?>
					</thead>
				</table>
				<div style="width:<? echo $width+20; ?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
					<table width="<? echo $width; ?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
                    <? 
					$i=0;
					foreach($dataArr as $company_id=>$rowsArr){
						foreach($rowsArr as $location_id=>$bdrows){
						$i++;
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";	
						?>
                        <tr bgcolor="<? echo $bgcolor; ?>">
                            <td rowspan="6" width="150" valign="middle"><p><?=$company_lib_arr[$company_id];?></p></td>
                            <td rowspan="6" width="90" valign="middle"><p><?=$location_arr[$location_id];?></p></td>
                            <td>Capacity Hours@100%[Available Hours]</td>
                            <? foreach($monthArr as $mk=>$monthName){?>
                            	<td align="right" width="80"><?= fn_number_format($dataArr[$company_id][$location_id][$mk][CapacityHours]);?></td>
							<? } ?>
                        </tr>
                        <tr>
                            <td>Expected Capacity Hours[Budgeted Hours]</td>
                            <? foreach($monthArr as $mk=>$monthName){?>
                            	<td align="right" width="80"><?= fn_number_format($dataArr[$company_id][$location_id][$mk][ExpectedCapacity],0);?></td>
							<? } ?>
                        </tr>
                        <tr>
                            <td>Planned Hours</td>
                            <? foreach($monthArr as $mk=>$monthName){?>
                            	<td width="80" align="right"><?= fn_number_format(array_sum($dataArr[$company_id][$location_id][$mk][PLAN_HOUR]));?></td>
							<? } ?>
                        </tr>
                        <tr>
                            <td>Variance</td>
                            <? foreach($monthArr as $mk=>$monthName){?>
                            	<td width="80" align="right"><?= fn_number_format($dataArr[$company_id][$location_id][$mk][ExpectedCapacity]-array_sum($dataArr[$company_id][$location_id][$mk][PLAN_HOUR]),2);?></td>
							<? } ?>
                        </tr>
                        <tr>
                            <td>Planned Qty</td>
                            <? foreach($monthArr as $mk=>$monthName){?>
                            	<td width="80" align="right"><?= fn_number_format(array_sum($dataArr[$company_id][$location_id][$mk][PLAN_QNTY]),2);?></td>
							<? } ?>
                        </tr>
                        <tr>
                            <td>Plan Efficiency%</td>
                            <? foreach($monthArr as $mk=>$monthName){?>
                            	<td width="80" align="right"><?= fn_number_format((array_sum($dataArr[$company_id][$location_id][$mk][PLAN_HOUR])/$dataArr[$company_id][$location_id][$mk][ExpectedCapacity])*100,2);?></td>
							<? } ?>
                        </tr>
                        <? }} ?>
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
      
 