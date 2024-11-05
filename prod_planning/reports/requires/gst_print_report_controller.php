<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
$user_name=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$company_library		= return_library_array( "select id, company_name from lib_company",'id','company_name');
$lib_buyer=return_library_array( "select id,buyer_name from lib_buyer","id", "buyer_name"  );

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "- All Buyer -", $selected, "" );     	 
	exit();
}


//--------------------------------------------------------------------------------------------------------------------

if($action=="order_no_search_popup")
{
	echo load_html_head_contents("Order No Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
     
	<script>
		function js_set_value( str ) 
		{
			$('#hide_order_no').val(str);
			parent.emailwindow.hide();
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
                    <th>
                        <input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;">
                        <input type="hidden" name="hide_order_no" id="hide_order_no" value="" /> 
                    </th> 
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",0,"",0 );
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_order_no_search_list_view', 'search_div', 'gst_print_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	else
	{
		$date_cond="";
	}
	
	$arr=array (0=>$company_library,1=>$lib_buyer);
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	
	$sql= "select b.id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $date_cond order by b.id, b.pub_shipment_date";
		
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Po No, Shipment Date", "80,80,50,70,140,170","760","220",0, $sql , "js_set_value", "po_number,id","",1,"company_name,buyer_name,0,0,0,0,0",$arr,"company_name,buyer_name,year,job_no_prefix_num,style_ref_no,po_number,pub_shipment_date","",'','0,0,0,0,0,0,3') ;
   exit(); 
}



if($action=="style_search_popup")
{
	echo load_html_head_contents("Order No Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
     
	<script>
		function js_set_value( str ) 
		{
			$('#hide_style_no').val(str);
			parent.emailwindow.hide();
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
                    <th>
                        <input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;">
                        <input type="hidden" name="hide_style_no" id="hide_style_no" value="" /> 
                    </th> 
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",0,"",0 );
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_style_search_list_view', 'search_div', 'gst_print_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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

if($action=="create_style_search_list_view")
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
	else
	{
		$date_cond="";
	}
	
	$arr=array (0=>$company_library,1=>$lib_buyer);
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	
	$sql= "select b.id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $date_cond order by b.id, b.pub_shipment_date";
		
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Po No, Shipment Date", "80,80,50,70,140,170","760","220",0, $sql , "js_set_value", "style_ref_no,job_no","",1,"company_name,buyer_name,0,0,0,0,0",$arr,"company_name,buyer_name,year,job_no_prefix_num,style_ref_no,po_number,pub_shipment_date","",'','0,0,0,0,0,0,3') ;
   exit(); 
}





if($action=="report_generate")
{
	extract($_REQUEST);	
	if (str_replace("'","",$cbo_job_year)!=0){
		if($db_type==0) $where_con.=" and YEAR(a.insert_date)=$cbo_job_year";
		else if($db_type==2) $where_con.=" and to_char(a.insert_date,'YYYY')=$cbo_job_year"; 
	}
	if (str_replace("'","",$cbo_buyer_name)!=0){
		$where_con.=" and a.buyer_name=$cbo_buyer_name";	
	}
	if (str_replace("'","",$txt_ref_no)!=''){
		$where_con.=" and a.style_ref_no=$txt_ref_no";
	}
	if (str_replace("'","",$txt_orer_id)!=''){
		$where_con.=" and c.id=$txt_orer_id";
		
	}
	if (str_replace("'","",$txt_job_no)!=''){
		$where_con.=" and a.job_no like ('%".str_replace("'","",$txt_job_no)."')";
		
	}
	
	if($db_type==0){
		mysql_query("SET CHARACTER SET utf8");
		mysql_query("SET SESSION collation_connection ='utf8_general_ci'");
	}
 	$lib_country=return_library_array( "select id,country_name from lib_country","id", "country_name"  );
	$lib_attachment=return_library_array( "select id,attachment_name from lib_attachment","id", "attachment_name"  );
	
	$lib_sewing_operation=return_library_array( "select id,operation_name from lib_sewing_operation_entry","id", "operation_name"  );

	ob_start();
?>		
	<fieldset>
    <div style="width:1250px" align="center">
<? 
		$row_data=sql_select("select id,country_id,company_name,plot_no,level_no,road_no,block_no,city,zip_code,province,email,website from lib_company where id=$cbo_company_name order by id");
			foreach($row_data as $row_com)
			{
	?>
    		<table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td align="center" style="font-size:20px">
					<? echo $row_com[csf("company_name")];	?>
                </td>
            </tr>
            <tr>
                <td align="center" style="font-size:10px">
                 	Plot No: <? echo $row_com[csf("plot_no")]; ?> Level No: <? echo $row_com[csf("level_no")]?> Road No: <? echo $row_com[csf("road_no")]; ?> Block No: <? echo $row_com[csf("block_no")];?> City No: <? echo $row_com[csf("city")];?> Zip Code: <? echo $row_com[csf("zip_code")]; ?> Province No: <?php echo $row_com[csf("province")];?> Country: <? echo $lib_country[$row_com[csf("country_id")]]; ?><br> Email Address: <? echo $row_com[csf("email")];?> Website No: <? echo $row_com[csf("website")];?>
                </td>  
            </tr>
            <tr><td align="center"><b>Operation Bulletin</b></td></tr>
        </table>
        <?  }  ?>
        <style type="text/css">
           /* table.display { border-collapse: collapse; }
            table.display td { padding: .3em; border: 1px black solid; }  */              	                   	
        </style>
		<?php
			
			
			if($db_type==0)
			{
				$row_data=sql_select("select a.job_no,a.buyer_name,a.style_ref_no,a.style_description, group_concat(distinct(po_number)) as order_number,a.job_no from  wo_po_details_master a,  wo_po_break_down c where a.job_no=c.job_no_mst $where_con group by a.job_no");
			}
			else
			{
				$row_data=sql_select("select a.job_no,a.buyer_name,a.style_ref_no,a.style_description, listagg(CAST(po_number as VARCHAR(4000)),',') within group (order by po_number) as order_number,a.job_no from  wo_po_details_master a,  wo_po_break_down c where a.job_no=c.job_no_mst $where_con group by a.job_no,a.buyer_name,a.style_ref_no,a.style_description");
			}
			
			
			foreach($row_data as $row_wo)
			{
				$order_number=implode(",",array_unique(explode(",",$row_wo[csf("order_number")])));
				$job_no=$row_wo[csf("job_no")];
				$image_name_array=return_library_array( "select master_tble_id,image_location from  common_photo_library where master_tble_id='$job_no'", "master_tble_id", "image_location"  );

			?>
			<table class="rpt_table" width="100%"  border="1" rules="all">
				<tr>
					<td style="font-size:12px"><b>Job No</b></td>
					<td colspan="3"><? echo $job_no;?></td>
					<td rowspan="5" align="center" valign="middle"><img src="../../<? echo $image_name_array[$job_no]; ?>" width="150" height="70"; border="2" /></td>
				</tr>
				<tr>
					<td width="100" style="font-size:12px"><b>Buyer Name</b></td>
					<td width="200"><? echo $lib_buyer[$row_wo[csf("buyer_name")]];?></td>
					<td width="100" style="font-size:12px"><b>Style</b></td>
					<td width="200"><? echo $row_wo[csf('style_ref_no')];?></td>
				</tr>
				<tr>
					<td  style="font-size:12px"><b>Style Details</b></td>
					<td><? echo $row_wo[csf('style_description')]; ?></td>
					<td style="font-size:12px"><b>Style Details</b></td>
					<td><? echo $row_wo[csf('style_description')];?></td>
				</tr>
				<tr>
					<td  style="font-size:12px"><b>Order</b></td>
					<td colspan="3" ><? echo split_string($order_number,50); ?></td>
				</tr>
				<tr>
					<td  style="font-size:12px"><b>Prepared By</b></td>
					<td style="font-size:12px" align="center"></td>
					<td style="font-size:12px"><b>IE</b></td>
					<td style="font-size:12px" align="center"></td>
				</tr>
			</table>
			<?php
        	} // End of Work Order Mast
        // Start of Item Description

				$sam2="";
				$total_no_of_worker_real2="";
				$working_hour2="";
				$tar_per="";
				$tar_per2="";
				$tar_per3="";
				
				if($db_type==0)
				{
					$sql_data=sql_select("select sum(no_of_worker_rounding) as no_of_worker_rounding, a.sam_style, a.working_hour from ppl_gsd_entry_mst a, ppl_gsd_entry_dtls b where a.id=b.mst_id and a.po_job_no ='$job_no' and a.is_deleted=0 group by a.po_job_no ");
				}
				else
				{
					$sql_data=sql_select("select sum(no_of_worker_rounding) as no_of_worker_rounding, a.sam_style, a.working_hour from ppl_gsd_entry_mst a, ppl_gsd_entry_dtls b where a.id=b.mst_id and a.po_job_no ='$job_no' and a.is_deleted=0 group by a.po_job_no,a.sam_style, a.working_hour");
				
				}
				
                foreach($sql_data as $tar_day )
                {
					$sam2=$tar_day[csf("sam_style")]; 
					$total_no_of_worker_real2=$tar_day[csf("no_of_worker_rounding")];
					$working_hour2=$tar_day[csf("working_hour")]; 
				}
				
				$tar_per=(60/$sam2)*$working_hour2*$total_no_of_worker_real2;
				$tar_per2=(60/$sam2)*$working_hour2*$txt_man_power2;
				$tar_per3=(60/$sam2)*$working_hour2*$txt_man_power3;
		?>
        <table class="rpt_table" width="100%"  border="1" rules="all">
            <tr>
                <td rowspan="2" width="40" align="center">Seq No</td>
                <td rowspan="2" width="" align="center">Process Name</td>
                <td rowspan="2" width="100" align="center">Resource/ MC Type</td>
                <td rowspan="2" width="100" align="center">Attachment</td>
                
                <td rowspan="2" width="60" align="center">Operator's SMV</td>
                <td rowspan="2" width="60" align="center">Helper'S SMV</td>
                <td rowspan="2" width="30" align="center">Total SMV</td>
                 <td rowspan="2" width="60" align="center">Target/Hr</td>
                <td rowspan="2" width="60" align="center">No of Worker</td>
                <td rowspan="2" width="60" align="center">No of Worker(Real)</td>
               
                <td colspan="3" width="180" align="center">Target/Day</td>
            </tr>
            <tr>
                <td width="60" align="center"><? echo $total_no_of_worker_real2;?></td>
                <td width="60" align="center"><? echo $txt_man_power2;?></td>
                <td width="60" align="center"><? echo $txt_man_power3;?></td>
            </tr>
			<?php
			 
				$i=0;
				$counter="";
				$total_smv="";
				$total_operator_smv="";
				$total_helper_smv="";
				$total_no_of_worker_cal="";
				$total_no_of_worker_real="";
				$allowance="";
				$sam="";
				$working_hour="";
				$pitch_time="";
				$all_machine="";
				$new_category=array();
				  
				 $sql_ord=sql_select("select mst_id,row_sequence_no,body_part_id,lib_sewing_id,resource_gsd,attachment_id,oparetion_type_id,b.total_smv as b_total_smv,no_of_worker_calculative,no_of_worker_rounding,target_per_hour_operation,target_per_day_operation,operation_id,operator_smv,helper_smv,a.allowance,a.sam_style,a.working_hour,a.pitch_time,man_power_1,man_power_2 from ppl_gsd_entry_mst a, ppl_gsd_entry_dtls b where a.id=b.mst_id and a.po_job_no ='$job_no' and a.is_deleted=0 order by b.row_sequence_no, body_part_id asc");
				$counter=count($sql_ord); 
                foreach($sql_ord as $row_ord2 )
                {
                 	if(!in_array($row_ord2[csf("body_part_id")],$new_category))
					{
						 
						$new_category[]=$row_ord2[csf("body_part_id")];
						?>
                        <tr>
                        	<td colspan="9" height="10" style="padding-left:30px" bgcolor="#CCCCCC"><strong><? echo $body_part[$row_ord2[csf("body_part_id")]]; ?></strong></td>
                        </tr>
                        <?
					}
					$i++;
            ?>
            <tr bgcolor="<? echo $bgcolor; ?>">
                <td width="20"><? echo $row_ord2[csf("row_sequence_no")]; ?></td>
                <td width="">
					<? echo $lib_sewing_operation[$row_ord2[csf("operation_id")]]; ?>
                </td>
                <td width="100"><? 
				 
					echo $production_resource[$row_ord2[csf("resource_gsd")]];
					$machine_count[$row_ord2[csf("resource_gsd")]]=$machine_count[$row_ord2[csf("resource_gsd")]]+$row_ord2[csf("no_of_worker_rounding")];
				?></td> 
                <td width="30" align="center"><? echo $lib_attachment[$row_ord2[csf("attachment_id")]];?></td>	
                
                <td width="60" align="right"><? echo $row_ord2[csf("operator_smv")];?></td>
                <td width="60" align="right"><? echo $row_ord2[csf("helper_smv")];?></td>
                <td width="30" align="right"><? echo $row_ord2[csf("b_total_smv")];?></td>
                 <td width="60" align="right"><? echo $row_ord2[csf("target_per_hour_operation")];?></td>
                <td width="60" align="right"><? echo $row_ord2[csf("no_of_worker_calculative")];?></td>
                <td width="60" align="right"><? echo $row_ord2[csf("no_of_worker_rounding")];?></td>
               
					<?php
                    if($i==1)
                    {
						$count_header=return_field_value("count(distinct(body_part_id)) as body_part_id","ppl_gsd_entry_dtls"," mst_id=".$row_ord2[csf("mst_id")]." group by mst_id ","body_part_id");
						
						$counter=$count_header+$counter;
                    ?>
                        <td rowspan="<? echo $counter;?>" width="60" align="right" bgcolor="#FFFFFF"><? echo round($tar_per); ?></td>
                        <td rowspan="<? echo $counter;?>" width="60" align="right" bgcolor="#FFFFFF"><? echo $row_ord2[csf("man_power_1")]; ?></td>
                        <td rowspan="<? echo $counter;?>" width="60" align="right" bgcolor="#FFFFFF"><? echo $row_ord2[csf("man_power_2")]; ?></td>
                    <?
                    }
					//echo $counter;
					?>
               </tr>
               	<?
               		$total_smv=$total_smv+$row_ord2[csf("b_total_smv")];
					$total_operator_smv=$total_operator_smv+$row_ord2[csf("operator_smv")];
					$total_helper_smv=$total_helper_smv+$row_ord2[csf("helper_smv")];
					$total_no_of_worker_cal=$total_no_of_worker_cal+$row_ord2[csf("no_of_worker_calculative")];
					$total_no_of_worker_real=$total_no_of_worker_real+$row_ord2[csf("no_of_worker_rounding")];
					$allowance=$row_ord2[csf("allowance")]; 
					$sam=$row_ord2[csf("sam_style")];
					$working_hour=$row_ord2[csf("working_hour")];
					$pitch_time=$row_ord2[csf("pitch_time")];
					$all_man_machine=$machine_count[$row_ord2[csf("resource_gsd")]];
                 } 
                ?>
               <tr>
                    <td colspan="4" align="right">Total</td>
                    <td width="30" align="right"><? echo $total_operator_smv;?></td>
                    <td width="60" align="right"><? echo $total_helper_smv;?></td>
                    <td width="60" align="right"><? echo $total_smv;?></td>
                    <td width="60" align="right"><? echo $total_no_of_worker_cal;?></td>
                    <td width="60" align="right"><? echo $total_no_of_worker_real;?></td>
                </tr>
               <tr>
                    <td colspan="4" align="right">Allowance(%)</td>
                    <td width="30" align="right"><? echo $allowance;?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
               <tr>
                    <td height="25" colspan="4" align="right">SAM</td>
                    <td width="30" align="right"><? echo $sam;?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
             </table>
             
             <table class="rpt_table" width="100%"  border="1" rules="all">
               	<tr style="border-left:hidden; border-right:hidden">
                <td colspan="12">&nbsp;</td></tr>
               <?
			   	$sum="";
					foreach (array_keys($machine_count) as $key=>$values)
						{
							if ($values<40)
							{
								$sum=$sum+$machine_count[$values];
							}
						}
				?>
          		<tr style="border:hidden">
                    <td colspan="4">
                        <table class="rpt_table" width="100%"  border="1" rules="all">
                            <tr><td colspan="2" width="210" align="center">WORKERS SUMMARY</td><td width="120" align="center">PITCH TIME</td></tr>
                            <tr ><td width="160">Total Machine Operators</td><td width="50" align="center"><? echo $sum;?></td><td width="90" rowspan="4" align="center" bgcolor="#FFFFFF"><? echo $pitch_time;?></td></tr>
                            <tr ><td width="160">Total Helpers</td><td width="50" align="center"><? echo $machine_count[40]; ?></td></tr>
                            <tr ><td width="160">Total QI</td><td width="50" align="center"><? echo $machine_count[41]; ?></td></tr>
                            <tr><td width="160">Total Man Power</td><td width="50" align="center"><? echo $total_no_of_worker_real;?></td></tr>
                        </table>
                    </td>
                    <td style="border-left:hidden; border-right:hidden">&nbsp;</td>
                    <td colspan="3">
                       <table class="rpt_table" width="100%"  border="1" rules="all">
                            <tr><td colspan="2" width="220" align="center">TARGET SUMMARY</td></tr>
                            <tr><td width="170"><font size="-1">SAM</font></td><td width="50" align="right"><? echo $sam;?></td></tr>
                            <tr><td width="170">Total Working Hour</td><td width="50" align="right"><? echo $working_hour; ?></td></tr>
                            <tr><td width="170">Target Per Hour</td><td align="right"><? $target_per_hr=(60/$sam)*$total_no_of_worker_real; echo round($target_per_hr); ?></td></tr>
                            <tr><td width="170">Target Per Day</td><td align="right"><? $target_per_day=($working_hour*$target_per_hr); echo round($target_per_day); ?></td></tr>
                        </table>
                    </td>
                    <td style="border-left:hidden; border-right:hidden">&nbsp;</td>
                    <td colspan="3">
                        <table class="rpt_table" width="100%"  border="1" rules="all">
                            <tr><td colspan="3" width="240" align="center">MACHINE SUMMARY</td></tr>
                        <?  $i=0;
                            $bb="";
                            $cc="";
                            $aa="";
                             $machine_total=0;
                            foreach (array_keys($machine_count) as $key=>$values)
                            { 
                                if ($values<40 && $production_resource[$values]!="")
                                {	
                                    $machine_total=$machine_total+$machine_count[$values];
                                    if ($aa=="") $aa = $production_resource[$values]; else $aa=$aa.",".$production_resource[$values];
                                }
                            }
                            $bb=explode(',',$aa);
                            $cc=count($bb);
                            
                            
                            foreach (array_keys($machine_count) as $key=>$values)
                            {
                                if ($values<40 && $production_resource[$values]!="")
                                {
                                    $sum_td='';
                                    if ($i==0) $sum_td='<td rowspan="'.$cc.'">'.$machine_total.'</td>';
                                    $i++;
                                    echo '<tr><td width="120">'.$production_resource[$values].'</td><td width="50" align="center">'.$machine_count[$values].'</td>'.$sum_td.'</tr>';
                                }
                            }
                            ?> 
                      </table>
                    </td>
            	</tr>
             </table>
    </div>
   </fieldset>
<?php	   
   $html = ob_get_contents();
   ob_clean();
   //previous file delete code-----------------------------//
	foreach (glob(""."*.xls") as $filename) 
	{			
		@unlink($filename);
	}
	$name=time();
	$filename=$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,$html);
	echo "$html"."####".$filename;
	exit();

}



?>