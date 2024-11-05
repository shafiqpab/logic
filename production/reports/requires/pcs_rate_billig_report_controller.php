<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  );
$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  ); 
$buyer_short_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  ); 
$location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name"  );




if ($action=="load_drop_down_location")
{
	//echo $data;
	echo create_drop_down( "cbo_location_id", 140, "select id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/pcs_rate_billig_report_controller', this.value, 'load_dropdown_floor', 'floor_td' );",""); 
	exit();    	 
}
if ($action=="load_dropdown_floor")
{ 
	echo create_drop_down( "cbo_floor_name", 100, "select id,floor_name from lib_prod_floor  where    location_id=$data and  production_process =1 and status_active=1","id,floor_name", 1, "--Select Floor--", $selected, "load_drop_down( 'requires/pcs_rate_billig_report_controller', this.value, 'load_dropdown_table', 'table_id' );","");
	exit();	 
}
if ($action=="load_dropdown_table")
{ 
	echo create_drop_down( "cbo_table_name", 80, "select id,table_name from lib_table_entry where   floor_name='$data' and status_active =1 and is_deleted=0 and table_name is not null  order by table_name","id,table_name", 1, "--Select Table--", $selected, "","","","","","");
	// echo "select id,table_name from lib_table_entry where   floor_name='$data' and status_active =1 and is_deleted=0 and table_name is not null  order by table_name";die;
	exit();	 
}

if($action=="job_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $buyer;die;
	?>
	
	<script>
    function js_set_value(id)
    {
		//alert(id);
		document.getElementById('selected_id').value=id;
		parent.emailwindow.hide();
    }
    </script>
    </head>
    <body>
    <div align="center" style="width:820px;">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:800px;">
            <table width="800" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Company</th>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Job No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" id="selected_id" name="selected_id" />
                </thead>
                <tbody>
                	<tr class="general">
                    	<td align="center"> 
							<?
                                echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $company, "" );
                            ?>
                        </td>
                        <td align="center">
                        	 <? 
								if($buyer>0) $buy_cond=" and a.id=$buyer";
								echo create_drop_down( "cbo_buyer_name", 140, "select a.id,a.buyer_name from lib_buyer a where a.status_active=1 and a.is_deleted=0 $buy_cond order by a.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0,"" );
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year; ?>', 'job_popup_search_list_view', 'search_div', 'pcs_rate_billig_report_controller', 'setFilterGrid(\'table_body2\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>   
	  <script>
        document.getElementById('cbo_company_id').value='<?=$company;?>';
    </script>              
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}

if ($action=="job_popup_search_list_view")
{
  	//echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	list($company_id,$buyer_id,$search_type,$search_value,$cbo_year)=explode('**',$data);
	if($company_id==0)
	{
		echo "Please Select Company Name";
		die;
	}
	//echo $company_id."==".$buyer_id."==".$search_type."==".$search_value."==".$cbo_year;die;
	if($search_type==1 && $search_value!=''){
		$search_con=" and a.job_no like('%$search_value')";	
	}
	else if($search_type==2 && $search_value!=''){
		$search_con=" and a.style_ref_no like('%$search_value%')";	
	}

	if($buyer_id==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_cond="";
		}
		else
		{
			$buyer_cond="";
		}
	}
	else
	{
		$buyer_cond=" and a.buyer_name=$buyer_id";
	}
	
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0)
		{
			$year_cond=" and YEAR(a.insert_date)=$cbo_year";
		}
		else
		{
			$year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";	
		}
	}
	else $year_cond="";
	
	if($db_type==2)
	{
		$group_field="LISTAGG(CAST(b.po_number AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY b.po_number) as po_number";
		$year_field="to_char(a.insert_date,'YYYY')";
	} 
	else if($db_type==0) 
	{
		$group_field="group_concat(distinct b.po_number ) as po_number";
		$year_field="YEAR(a.insert_date)";
	}

	$arr=array (2=>$company_arr,3=>$buyer_arr);
	$sql= "SELECT a.id, a.job_no_prefix_num, a.job_no, a.company_name,a.buyer_name,a.style_ref_no,$year_field as year , $group_field
	from wo_po_details_master a,  wo_po_break_down b 
	where a.job_no=b.job_no_mst and b.status_active in(1,2,3) and a.company_name=$company_id $buyer_cond $year_cond $search_con 
	group by a.id, a.job_no_prefix_num, a.job_no, a.company_name,a.buyer_name,a.style_ref_no,a.insert_date
	order by a.id desc";
	//echo $sql;//die;
	$rows=sql_select($sql);
	?>
    <table width="800" border="1" rules="all" class="rpt_table">
        <thead>
            <tr>
                <th width="30">SL</th>
                <th width="120">Company</th>
                <th width="120">Buyer</th>
                <th width="50">Year</th>
                <th width="120">Job no</th>
                <th width="120">Style</th>
                <th>Po number</th>
                
            </tr>
       </thead>
    </table>
    <div style="max-height:820px; overflow:auto;">
    <table id="table_body2" width="800" border="1" rules="all" class="rpt_table">
     <? $rows=sql_select($sql);
         $i=1;
         foreach($rows as $data)
         {
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$po_num=implode(",",array_unique(explode(",",$data[csf('po_number')])));
			?>
			<tr bgcolor="<? echo  $bgcolor;?>" onClick="js_set_value('<? echo $data[csf('id')]; ?>'+'_'+'<? echo $data[csf('job_no')]; ?>')" style="cursor:pointer;">
                <td width="30" align="center"><? echo $i; ?></td>
                <td width="120"><p><? echo $company_arr[$data[csf('company_name')]]; ?></p></td>
                <td width="120"><p><? echo $buyer_short_library[$data[csf('buyer_name')]]; ?></p></td>
                <td align="center" width="50"><p><? echo $data[csf('year')]; ?></p></td>
                <td width="120"><p><? echo $data[csf('job_no')]; ?></p></td>
                <td width="120"><p><? echo $data[csf('style_ref_no')]; ?></p></td>
                <td><p><? echo $po_num; ?></p></td>
			</tr>
			<? 
			$i++; 
		} 
		?>
    </table>
    </div>
    <?
	
	exit();
}


if($action=="report_generate")
{ 
	$process = array( &$_POST );
	//print_r($process);
	extract(check_magic_quote_gpc( $process ));
	$company_id 		= str_replace("'","",$cbo_company_id);
	$location_id 		= str_replace("'","",$cbo_location_id);
	$section_id 		= str_replace("'","",$cbo_rate_category);
	$process_id 		= str_replace("'","",$cbo_process);
	$txt_job_no 		 = str_replace("'","",$txt_job_no);
	$hidden_job_id 		 = str_replace("'","",$hidden_job_id);
	$floor_id 		    = str_replace("'","",$cbo_floor_name);
	$table_id 		    = str_replace("'","",$cbo_table_name);
	$date_from 			= str_replace("'","",$txt_date_from);
	$date_to 			= str_replace("'","",$txt_date_to);
	//echo $company_id;die;
	$sql_cond = "";
	$sql_cond .= ($company_id!=0) ? " and a.company_id=$company_id" : "";
	$sql_cond .= ($location_id!=0) ? " and a.location_id=$location_id" : "";
	//$sql_cond .= ($section_id!=0) ? " and a.location_id=$section_id" : "";
	$sql_cond .= ($process_id!=0) ? " and b.process_id=$process_id" : "";
	// $sql_cond .= ($hidden_job_id!=0) ? " and a.job_id=$hidden_job_id" : "";
	$sql_cond .= ($floor_id!=0) ? " and b.floor_id=$floor_id" : "";
	$sql_cond .= ($table_id!=0) ? " and b.table_id=$table_id" : "";
	$sql_cond .= ($txt_job_no!="") ? " and c.job_no like '%$txt_job_no%'" : "";
	if($date_from && $date_to!="")
	{  
		$start_date=date("j-M-Y",strtotime($date_from));
		$end_date=date("j-M-Y",strtotime($date_to));
		$sql_cond .="and a.entry_date between '$start_date' and '$end_date'";
	}

	$company_arr = return_library_array("SELECT id,company_name from lib_company", "id", "company_name");
	$emp_array=return_library_array( "SELECT id, first_name from lib_employee", "id", "first_name"  );
	$id_card_array=return_library_array( "SELECT id, id_card_no from lib_employee", "id", "id_card_no"  );
	$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$location_arr = return_library_array("SELECT id,location_name from lib_location", "id", "location_name");
	$table_arr = return_library_array("SELECT id,table_name from lib_table_entry", "id", "table_name");
	$garments_item=return_library_array( "select id, item_name from lib_garment_item",'id','item_name');

    $sql="SELECT a.company_id,a.location_id,b.process_id,a.job_id,a.po_id,a.item_id,a.style_ref,b.floor_id,b.table_id,a.entry_date,b.operator_id , b.qty, c.job_no,c.buyer_name,d.po_number from operator_wise_cutting_entry_mst a, operator_wise_cutting_entry_dtls b,wo_po_details_master c,wo_po_break_down d where a.id=b.mst_id and c.id=a.job_id and d.id=a.po_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.qty>0 $sql_cond ";
	// echo $sql; die;
	$sql_result=sql_select($sql);
	$po_and_opt_wise_data_array=array();
	$po_wise_data_array=array();
	$po_id_array=array();
	$process_id_array=array();
	$table_wise_data_array=array();
	$process_wise_summary_array=array();
	foreach($sql_result as $row )
	{
		$po_wise_data_array[$row['PO_ID']]['style_ref']=$row['STYLE_REF'];
		$po_wise_data_array[$row['PO_ID']]['job_no']=$row['JOB_NO'];
		$po_wise_data_array[$row['PO_ID']]['buyer_name']=$row['BUYER_NAME'];
		$po_wise_data_array[$row['PO_ID']]['po_number']=$row['PO_NUMBER'];

		$po_and_opt_wise_data_array[$row['PO_ID']][$row['OPERATOR_ID']][$row['PROCESS_ID']]['qty'] += $row['QTY'];
		
		// $po_and_opt_wise_data_array[$row['PO_ID']][$row['OPERATOR_ID']]['process_id'] .= $row['PROCESS_ID'].",";

		$po_id_array[$row['PO_ID']]=$row['PO_ID'];
		$job_id_array[$row['JOB_ID']]=$row['JOB_ID'];
   
		$process_id_array[$row['PROCESS_ID']]=$row['PROCESS_ID'];

		$table_wise_data_array[$row['PO_ID']][$row['TABLE_ID']][$row['OPERATOR_ID']]['qty'] += $row['QTY'];

		$process_wise_summary_array[$row['PROCESS_ID']][$row['TABLE_ID']][$row['PO_ID']][$row['ITEM_ID']]['qty'] += $row['QTY'];
		$process_wise_summary_array[$row['PROCESS_ID']][$row['TABLE_ID']][$row['PO_ID']][$row['ITEM_ID']]['style_ref']=$row['STYLE_REF'];
		$process_wise_summary_array[$row['PROCESS_ID']][$row['TABLE_ID']][$row['PO_ID']][$row['ITEM_ID']]['job_no']=$row['JOB_NO'];
		$process_wise_summary_array[$row['PROCESS_ID']][$row['TABLE_ID']][$row['PO_ID']][$row['ITEM_ID']]['buyer_name']=$row['BUYER_NAME'];
		$process_wise_summary_array[$row['PROCESS_ID']][$row['TABLE_ID']][$row['PO_ID']][$row['ITEM_ID']]['po_number']=$row['PO_NUMBER'];

	}
	unset($sql_result);
	//echo "<pre>";print_r($process_wise_summary_array);die;
	//echo "<pre>";print_r($po_id_array);die;
	//echo "<pre>";print_r($process_id_array);die;
	$po_ids=implode(",",$po_id_array);
	$job_ids=implode(",",$job_id_array);
	$process_ids=implode(",",$process_id_array);
	
	$process_sql=("SELECT po_ids,process_id,uom ,rate from PROCESS_ORDER_WISE_RATE_ENTRY_DTLS where job_id in($job_ids) and process_id in($process_ids)  and status_active=1 and is_deleted=0 ");
	//echo $process_sql; die;
	$process_result=sql_select($process_sql);
	$po_and_porcess_array=array();
	$po_and_porcess_rate_array=array();
	foreach($process_result as $val)
	{
		$po_ex = explode(",",$val['PO_IDS']);
		foreach ($po_ex as $key => $v) 
		{
			if($val['UOM']==1)
			{
				$po_and_porcess_array[$v][$val['PROCESS_ID']]['rate'] = $val['RATE']*12;
			}
			else
			{
				$po_and_porcess_array[$v][$val['PROCESS_ID']]['rate'] = $val['RATE'];
			}
			
			if($val['UOM']==1)
			{
				$po_and_porcess_rate_array[$v]['rate'] = $val['RATE']*12;
			}
			else
			{
				$po_and_porcess_rate_array[$v]['rate'] = $val['RATE'];
			}
		}
	}    
   // echo "<pre>";print_r($po_and_porcess_array);die;	
  

	if($type==1) //Operator Wise Details 
	{

		ob_start();	
		?>
		
		<fieldset style="width:550px;margin:0 auto ;">
			<table width="520" cellpadding="0" cellspacing="0"> 
				
				<tr class="form_caption">
					
					<td colspan="33" align="center" style="border:none;font-size:15px; font-weight:bold">Company:<strong><? echo $company_arr[$company_id]; ?></strong></td> 
				</tr>
				
				<tr class="form_caption">
					<td colspan="33" align="center"style="border:none;font-size:15px; font-weight:bold">Location:<strong><? echo $location_arr[$location_id]; ?></strong></td> 
				</tr>
				<tr class="form_caption" style="border:none;">
					<td colspan="15" align="center" style="border:none;font-size:15px; font-weight:bold" >Date: <?=$date_from;?> To <?=$date_to;?></td>
				</tr>
				<tr class="form_caption" style="border:none;">
					<td colspan="15" align="center" style="border:none;font-size:15px; font-weight:bold" >Operator And Order Wise Statement</td>
				</tr>
			</table>
			<table id="table_header_1" class="rpt_table" width="520" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
						<tr>
						<th width="120">Operator Name</th>
						<th width="80">Operator ID Card No</th>
						<th width="80">Process/Operation</th>
						<th width="60">Qty [Pcs]</th>
						<th width="60">Qty [Dzn]</th>
						<th width="60">Rate/Dzn</th>
						<th width="80">Bill Amount</th>
						</tr>								
					</thead>
					<tbody>
					<?
					$i=0;
					$gr_pcs_qty=0;
					$gr_dzn_qty=0;
					$gr_bill_amount =0;
                    foreach($po_and_opt_wise_data_array as $po_key=>$po_val)
					{
						$po_wise_pcs_qty=0;
						$po_wise_dzn_qty=0;
						$po_and_opt_wise_bill_amount =0;
						
						?>
						<tr>
							<td bgcolor="#dccdcd" colspan="7"><b>Order:<?=$po_wise_data_array[$po_key]['po_number']?></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Job:<?=$po_wise_data_array[$po_key]['job_no'];?></b><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Style:<?=$po_wise_data_array[$po_key]['style_ref']?></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Buyer:<?=$buyer_arr[$po_wise_data_array[$po_key]['buyer_name']];?></b>
							</td>
						</tr>
						<?
							foreach($po_val as $emp_key =>$op_data)
							{
								foreach($op_data as $p_key =>$val)
								{
									if($val['qty']>0)
									{
										if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										$bill_amount=($po_and_porcess_array[$po_key][$p_key]['rate']*($val['qty']/12));

										/* $process_id_arr = array_unique(array_filter(explode(",",$val['process_id'])));
										$process_name = "";
										foreach ($process_id_arr as $r) 
										{
											$process_name .= ($process_name=="") ? $process_array[$r] : ", ".$process_array[$r];
										} */
										?>	
									
										<tr bgcolor='<? echo $bgcolor; ?>' onclick="change_color('tr_<? echo $i;?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i;?>">
											<td width="120" align="left"><?=$emp_array[$emp_key];?></td>
											<td width="80" align="left"><?=$id_card_array[$emp_key];?></td>
											<td width="80" align="left"><?=$process_array[$p_key];?></td>
											<td width="60" align="right"><?=$val['qty'];?></td>
											<td width="60" align="right"><?=number_format(($val['qty']/12),2) ;?></td>
											<td width="60" align="right"><?=$po_and_porcess_array[$po_key][$p_key]['rate'];?></td>
											<td width="80" align="right"><?=number_format($bill_amount);?></td>
										</tr>
										<?
										$i++;
										$po_wise_pcs_qty += $val['qty'];
										$po_wise_dzn_qty += ($val['qty']/12);
										$gr_bill_amount  += $bill_amount;
										$po_and_opt_wise_bill_amount += $bill_amount;
										$gr_pcs_qty +=$val['qty'];;
										$gr_dzn_qty +=($val['qty']/12);
									}
								}
							}	
						?>
							<tr bgcolor="#cddcdc">							
								<td colspan="3" align="middle"><b>Order Wise Total</b></td>
								<td align="right"><b><?=$po_wise_pcs_qty?></b></td>
								<td align="right"><b><?=number_format($po_wise_dzn_qty,2)?></b></td>
								<td></td>
								<td align="right"><b><?=number_format($po_and_opt_wise_bill_amount)?></b></td>
							</tr>
						<?
				    }	
						?>		
						
					</tbody>
					<tfoot>
					<tr>
						<td colspan="3" align="middle"><b>Grand Total</b></td>
						<td align="right"><b><?=$gr_pcs_qty?></b></td>
						<td align="right"><b><?=number_format($gr_dzn_qty,2)?></b></td>
						<td></td>
						<td align="right"><b><?=number_format($gr_bill_amount)?></b></td>
					</tr>
					</tfoot>
			
			</table>
			</div>
		</fieldset>
       
     
		<?  
	}
	else if($type==2) //Operator Wise Summary
	{
		$tbl_op_wise_data_array = array();
		foreach($table_wise_data_array as $po_id => $po_data)
		{
			foreach($po_data as $tbl_id => $tbl_data)
			{
				foreach ($tbl_data as $op_id => $v) 
				{
					$rate = $po_and_porcess_rate_array[$po_id]['rate'];
					$tbl_op_wise_data_array[$tbl_id][$op_id]['qty'] += $v['qty'];
					$tbl_op_wise_data_array[$tbl_id][$op_id]['amount'] += $v['qty']*$rate;
				}
			}
		}
		//echo "<pre>";print_r($tbl_op_wise_data_array);die;
		ob_start();	
		?>
		
		<fieldset style="width:670px;margin:0 auto ;">
			<table width="650" cellpadding="0" cellspacing="0"> 
				
					<tr class="form_caption">
						
						<td colspan="33" align="center" style="border:none;font-size:15px; font-weight:bold">Company:<strong><? echo $company_arr[$company_id]; ?></strong></td> 
					</tr>
					
					<tr class="form_caption">
						<td colspan="33" align="center"style="border:none;font-size:15px; font-weight:bold">Location:<strong><? echo $location_arr[$location_id]; ?></strong></td> 
					</tr>

					<tr class="form_caption" style="border:none;">
						<td colspan="15" align="center" style="border:none;font-size:15px; font-weight:bold" >Date: <?=$date_from;?> To <?=$date_to;?></td>
					</tr>
					
					<tr class="form_caption" style="border:none;">
						<td colspan="15" align="center" style="border:none;font-size:15px; font-weight:bold" >Weekly Wages Statement - Cutting</td>
					</tr>
			</table>
			<table id="table_header_1" class="rpt_table" width="650" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<tr>
						<th width="100">Line/Table No</th>
						<th width="150">Operator Name</th>
						<th width="80">Operator ID Card NO</th>
						<th width="60">Qty [Pcs]</th>
						<th width="60">Qty [Dzn]</th>
						<th width="80">Bill Amount</th>
						<th width="120">Signature</th>
					</tr>		
				</thead>				
						<tbody>
							<?
								$i=0;
								$gr_pcs_qty=0;
								$gr_dzn_qty=0;
								$gr_bill_amount=0;
								foreach($tbl_op_wise_data_array as $talble_id => $talble_value)
								{
									$table_wise_pcs_qty=0;
									$table_wise_dzn_qty=0;
									$table_wise_bill_amount=0;
									foreach($talble_value as $emp_key => $row)
									{
										if($row['qty']>0)
										{
										if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									
											?>
												<tr bgcolor='<? echo $bgcolor; ?>' onclick="change_color('tr_<? echo $i;?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i;?>">
													<td width="100" align="center"><?=$table_arr[$talble_id];?></td>
													<td width="120" align="center"><?=$emp_array[$emp_key];?></td>
													<td width="80" align="center"><?=$id_card_array[$emp_key];;?></td>
													<td width="60" align="right"><?=$row['qty'];?></td>
													<td width="60" align="right"><?=number_format(($row['qty']/12),2) ;?></td>
													<td width="80" align="right"><?=number_format($row['amount']/12);?></td>
													<td></td>
											</tr>
										
											<?
											$i++;
											$table_wise_pcs_qty += $row['qty'];
											$table_wise_dzn_qty +=($row['qty']/12);
											$table_wise_bill_amount +=($row['amount']/12);
											$gr_pcs_qty +=$row['qty'];;
											$gr_dzn_qty +=($row['qty']/12);
											$gr_bill_amount +=($row['amount']/12);
										}	
									}	
									?>
										<tr bgcolor="#cddcdc">
											<td colspan="3" align="middle"><b>Order Wise Total</b></td>
											<td align="right"><b><?=$table_wise_pcs_qty?></b></td>
											<td align="right"><b><?=number_format($table_wise_dzn_qty,2)?></b></td>
											<td align="right"><b><?=number_format($table_wise_bill_amount);?></b></td>
											<td></td>
										</tr>
									<?

								}
							?>								
								
						</tbody>
						<tfoot>
							<tr>
								<td colspan="3" align="middle"><b>Grand Total</b></td>
								<td align="right"><b><?=$gr_pcs_qty?></b></td>
								<td align="right"><b><?=number_format($gr_dzn_qty,2)?></b></td>
								<td align="right"><b><?=number_format($gr_bill_amount)?></b></td>
								<td></td>
							</tr>
					   </tfoot>
			</table>
			</div>
		</fieldset>     
		<?  
	}
	else if($type==3) //Process & Job Wise Summary 
	{
		ob_start();	
		?>
		
		<fieldset style="width:800px;margin:0 auto ;">
			<table width="780" cellpadding="0" cellspacing="0"> 
				
				<tr class="form_caption">
					
					<td colspan="33" align="center" style="border:none;font-size:15px; font-weight:bold">Company:<strong><? echo $company_arr[$company_id]; ?></strong></td> 
				</tr>
				
				<tr class="form_caption">
					<td colspan="33" align="center"style="border:none;font-size:15px; font-weight:bold">Location:<strong><? echo $location_arr[$location_id]; ?></strong></td> 
				</tr>
				<tr class="form_caption" style="border:none;">
					<td colspan="15" align="center" style="border:none;font-size:15px; font-weight:bold" >Date: <?=$date_from;?> To <?=$date_to;?></td>
				</tr>
				<tr class="form_caption" style="border:none;">
					<td colspan="15" align="center" style="border:none;font-size:15px; font-weight:bold" >Process Wise Summary</td>
				</tr>
			</table>
			
			<table id="table_header_1" class="rpt_table" width="780" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<tr>
						<th width="100">Line/Table No</th>
						<th width="80">Job No</th>
						<th width="100">Style Ref</th>
						<th width="80">Order No</th>
						<th width="80">Garments Item</th>
						<th width="80">Buyer</th>
						<th width="60">Qty [Pcs]</th>
						<th width="60">Qty [Dzn]</th>
						<th width="60">Rate/Dzn</th>
						<th width="80">Bill Amount</th>
					</tr>												
				</thead>
				<tbody>
				<?
			 	$i=0;
				foreach($process_wise_summary_array as $process_key => $process_val)
				{
						
					$process_pcs_qty=0;
					$process_dzn_qty=0;
					$process_bill_amount=0;
			
					?>
						
					<td bgcolor="#dccdcd" colspan="10"><b>Operation/Process:<?=$process_array[$process_key];?></b></td>
					<?
					foreach($process_val as $table_key => $table_val)
					{
						$table_wise_pcs_qty=0;
						$table_wise_dzn_qty=0;
						$table_wise_pcs_qty_bill_amount=0;
						foreach($table_val as $po_key => $po_val)
						{
							foreach($po_val as $item_key => $value )
							{
								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$bill_amount=($po_and_porcess_array[$po_key][$process_key]['rate']*($value['qty']/12));
								?>
									
									<tr bgcolor='<? echo $bgcolor; ?>' onclick="change_color('tr_<? echo $i;?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i;?>">
											<td width="100" align="center"><?=$table_arr[$table_key];?></td>
											<td width="80"><?=$value['job_no'];?></td>
											<td width="100"><?=$value['style_ref'];?></td>
											<td width="80"><?=$value['po_number'];?></td>
											<td width="80"><?=$garments_item[$item_key];?></td>
											<td width="80"><?=$buyer_arr[$value['buyer_name']]?></td>
											<td width="60" align="right"><?=$value['qty']?></td>
											<td width="60" align="right"><?=number_format(($value['qty']/12),2) ;?></td>
											<td width="60"  align="right"><?=$po_and_porcess_array[$po_key][$process_key]['rate'];?></td>
											<td width="80"  align="right"><?=number_format($bill_amount);?></td>
									</tr>
									<?
										$i++;		
										$table_wise_pcs_qty += $value['qty'];
										$table_wise_dzn_qty +=($value['qty']/12);
										$process_pcs_qty +=$value['qty'];;
										$process_dzn_qty +=($value['qty']/12);
										$process_bill_amount +=$bill_amount;
										$table_wise_pcs_qty_bill_amount +=$bill_amount;
								
							}
						}
						?>
		
						<tr bgcolor="#cdddc">							
							<td colspan="6" align="middle"><b>Line /Table Wise Total</b></td>
							<td  align="right"><b><?=$table_wise_pcs_qty?></b></td>
							<td  align="right"><b><?=number_format($table_wise_dzn_qty,2)?></b></td>
							<td></td>
							<td  align="right"><b><?= number_format($table_wise_pcs_qty_bill_amount)?></b></td>
						</tr>
						<?
					}	

					?>
						<tr bgcolor="#cddcdc">							
							<td colspan="6" align="middle"><b>Process Wise Total</b></td>
							<td align="right"><b><?=$process_pcs_qty?></b></td>
							<td align="right"><b><?=number_format($process_dzn_qty,2)?></b></td>
							<td></td>
							<td align="right"><b><?=number_format($process_bill_amount)?></b></td>
						</tr>
					<?

			 	}
								
				?>							
							
				</tbody>
			</table>
			</div>
		</fieldset>
       
     
		<?   
    	
	}
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();      
}









