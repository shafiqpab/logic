<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');
require_once('../../includes/class4/class.conditions.php');
require_once('../../includes/class4/class.reports.php');
require_once('../../includes/class4/class.conversions.php');

$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_dropdown_table")
{ 
	//echo $data;
	$data=explode('**',$data);
	$cbo_company_id=$data[0];
	$location_cond=$data[1];
	$floor_cond=$data[2];
	$row=$data[3];
	echo create_drop_down( "cbotableName_$row", 100, "select id,table_no from lib_cutting_table where company_id='$data[0]' and location_id='$data[1]' and floor_id='$data[2]'  and status_active =1 and is_deleted=0 and table_no is not null order by table_no","id,table_no", 1, "--Select Table--", $selected, "","","","","","");
	exit();	 
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select Location--", $selected, "","","","","","",0 );
	exit();	 
}

if ($action=="load_variable_settings")
{
	echo "$('#variable_check').val(0);\n";
	echo "$('#bill_on').text('');\n"; 
	$sql_result = sql_select("select variable_list, dyeing_fin_bill from  variable_settings_subcon where company_id='$data' and variable_list in (1,3,8,11) order by id");
 	foreach($sql_result as $result)
	{
		if($result[csf("variable_list")]==1)// bill on qty
		{
			echo "$('#variable_check').val(".$result[csf("dyeing_fin_bill")].");\n";
			if ($result[csf("dyeing_fin_bill")]==1)
			{
				echo "$('#bill_on').text('Bill On Grey Qty');\n"; 
			}
			else if ($result[csf("dyeing_fin_bill")]==2)
			{
				echo "$('#bill_on').text('Bill On Delivery Qty');\n"; 
			}
			else
			{
				echo "$('#bill_on').text('');\n"; 
			}
		}
		else if($result[csf("variable_list")]==3)//rate from
		{
			$rate_from=$result[csf("dyeing_fin_bill")];
			if($rate_from=="") $rate_from=3; else if ($rate_from==0) $rate_from=3;
			else $rate_from=$rate_from;
			
			echo "$('#hidd_rate_from').val(".$rate_from.");\n";
		}
		else if($result[csf("variable_list")]==8)// inhouse bill from
		{
			$finishdata_source=$result[csf("dyeing_fin_bill")];
			if($finishdata_source=="") $finishdata_source=1; else if ($finishdata_source==0) $finishdata_source=1;
			else $finishdata_source=$finishdata_source;
			
			echo "$('#hidd_inhouse_bill_from').val(".$finishdata_source.");\n";
		}
		else if($result[csf("variable_list")]==11)// Control With
		{
			$control_with=$result[csf("dyeing_fin_bill")];
			if($control_with=="") $control_with=0;
			
			echo "$('#hddn_control_with').val(".$control_with.");\n";
		}
	}
 	exit();
}

if ($action=="dyeingfinishing_bill_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company_name=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $party_name=" and a.party_id='$data[1]'"; else $party_name="";
	
	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $return_date = "and a.bill_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $return_date ="";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $return_date = "and a.bill_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $return_date ="";
	}
	
	if ($data[4]!='') $bill_id_cond=" and a.prefix_no_num='$data[4]'"; else $bill_id_cond="";
	//if ($data[5]!='') $recChallan_cond=" and challan_no='$data[5]'"; else $recChallan_cond="";
	if ($data[5]!='') $recChallan_cond=" and challan_no='$data[5]'"; else $recChallan_cond="";
	
	$rec_man_challan_arr=array();
	$sql_rec="select id, challan_no from inv_receive_master where status_active=1 and is_deleted=0 $recChallan_cond";
	$sql_rec_result = sql_select($sql_rec); $recId=""; $tot_rows=0;
	foreach($sql_rec_result as $row)
	{
		$tot_rows++;
		$rec_man_challan_arr[$row[csf("id")]]=$row[csf("challan_no")];
		$recId.="'".$row[csf("id")]."',";
	}
	unset($sql_rec_result);
	$rec_id_cond="";
	if ($data[5]!='')
	{
		$recIds=chop($recId,','); 
		if($db_type==2 && $tot_rows>1000)
		{
			$rec_id_cond=" and (";
			$recIdsArr=array_chunk(explode(",",$recIds),999);
			foreach($recIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$rec_id_cond.=" b.delivery_id in($ids) or ";
			}
			$rec_id_cond=chop($rec_id_cond,'or ');
			$rec_id_cond.=")";
		}
		else $rec_id_cond=" and b.delivery_id in ($recIds)";
	}
	
	if($db_type==0) $bid_cond="group_concat(id)";
	else if($db_type==2) $bid_cond="listagg(id,',') within group (order by id)";
	
	$batchidCond="";
	
	if($data[6]!='')
	{ 
		$batch_ids = return_field_value("$bid_cond as id", "pro_batch_create_mst", "batch_no='$data[6]' and status_active=1 and is_deleted=0", "id");
		if ($batch_ids!="") $batchidCond=" and b.batch_id in ($batch_ids)"; else $batchidCond="Batch Not found.".die;
	}
	
	$sub_del_challan_arr=array();
	$sql_sub_challan="select a.challan_no, b.id from subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id  and a.status_active=1 and a.is_deleted=0 $recChallan_cond";
	$sql_sub_challan_result = sql_select($sql_sub_challan);
	foreach ($sql_sub_challan_result as $row)
	{
		$sub_del_challan_arr[$row[csf("id")]]=$row[csf("challan_no")];
	}
	unset($sql_sub_challan_result);
	
	$company_id=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$location_arr=return_library_array( "select id,location_name from lib_location",'id','location_name');
	$party_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst where status_active=1 and is_deleted=0",'id','batch_no');
	
	$arr=array (2=>$location,4=>$party_arr,5=>$knitting_source,6=>$bill_for);
	
	if($db_type==0)
	{
		$year_cond= "year(a.insert_date)as year";
		$delivery_id_cond="group_concat(b.delivery_id)";
	}
	else if($db_type==2)
	{
		$year_cond= "TO_CHAR(a.insert_date,'YYYY') as year";
		$delivery_id_cond="LISTAGG(CAST(b.delivery_id AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY b.delivery_id)";
	}
	//group by a.id, a.bill_no, a.prefix_no_num, a.insert_date, a.location_id, a.bill_date, a.party_id, a.party_source, a.bill_for, c.batch_no
	
	$sql= "select a.id, a.bill_no, a.prefix_no_num, $year_cond, a.location_id, a.bill_date, a.party_id, a.party_source, a.bill_for, c.batch_no
	from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b, pro_batch_create_mst c
	where a.id=b.mst_id and a.process_id=4 and a.status_active=1 $company_name $party_name $return_date $bill_id_cond $rec_id_cond $batchidCond and b.batch_id=c.id
	
	order by a.id DESC";
	
	// $sql= "select a.id, a.bill_no, a.prefix_no_num, $year_cond, a.location_id, a.bill_date, a.party_id, a.party_source, a.bill_for, b.delivery_id as delivery_id from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and a.process_id=4 and a.status_active=1 $company_name $party_name $return_date $bill_id_cond $rec_id_cond  order by a.id DESC";
	 //group by a.id, a.bill_no, a.prefix_no_num, a.insert_date, a.location_id, a.bill_date, a.party_id, a.party_source, a.bill_for
	 $result = sql_select($sql);
	  foreach( $result as $row )
		{
			$fin_bill_issue_arr[$row[csf("bill_no")]]['id']=$row[csf("id")];
			$fin_bill_issue_arr[$row[csf("bill_no")]]['prefix_no_num']=$row[csf("prefix_no_num")];
			$fin_bill_issue_arr[$row[csf("bill_no")]]['year']=$row[csf("year")];
			$fin_bill_issue_arr[$row[csf("bill_no")]]['delivery_id'].=$row[csf("delivery_id")].',';
			$fin_bill_issue_arr[$row[csf("bill_no")]]['location_id']=$row[csf("location_id")];
			$fin_bill_issue_arr[$row[csf("bill_no")]]['bill_date']=$row[csf("bill_date")];
			$fin_bill_issue_arr[$row[csf("bill_no")]]['party_id']=$row[csf("party_id")];
			$fin_bill_issue_arr[$row[csf("bill_no")]]['batch_no']=$row[csf("batch_no")];
			$fin_bill_issue_arr[$row[csf("bill_no")]]['party_source']=$row[csf("party_source")];
			$fin_bill_issue_arr[$row[csf("bill_no")]]['bill_for']=$row[csf("bill_for")];
		}
		
	?>
	<div>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="60">Bill No</th>
                <th width="60">Year</th>
                <th width="110">Location</th>
                <th width="90">Source</th>
                <th width="60">Bill Date</th>
                <th width="120">Party</th>
                <th width="80">Bill For</th>
                <th width="60">Challan No</th>
                <th>Batch</th>
            </thead>
     	</table>
     </div>
     <div style="width:750px; max-height:250px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="730" class="rpt_table" id="list_view">
			<?
			$i=1; 
           // foreach( $result as $row )
		     foreach( $fin_bill_issue_arr as $bill_no=>$row )
            {
                if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$challan_no=""; $bill_company="";
				if($row[("party_source")]==1) 
				{
					$bill_company=$company_id[$row[("party_id")]];
					//$challan_no=$rec_man_challan_arr[$row[csf("delivery_id")]];
					$delivery_id=explode(",",$row[("delivery_id")]);
					$ex_del_id=rtrim($delivery_id,',');
					foreach($ex_del_id as $del_id)
					{
						if ($challan_no=="") $challan_no=$rec_man_challan_arr[$del_id]; else $challan_no.=','.$rec_man_challan_arr[$del_id];
					}
				}
				else 
				{
					$bill_company=$party_arr[$row[("party_id")]];
					$ex_del_id=explode("_",$row[("delivery_id")]);
					foreach($ex_del_id as $del_id)
					{
						if ($challan_no=="") $challan_no=$sub_del_challan_arr[$del_id]; else $challan_no.=','.$sub_del_challan_arr[$del_id];
					}
				}
				$unique_challan=implode(",",array_unique(explode(',',$challan_no)));
				
				//if($row[csf("party_source")]==1) $bill_company=$company_id[$row[csf("party_id")]]; else $bill_company=$party_arr[$row[csf("party_id")]];
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value(<? echo $row[("id")];?>);" > 
						<td width="30"><? echo $i; ?></td>
						<td width="60"><? echo $row[("prefix_no_num")]; ?></td>
                        <td width="60"><? echo $row[("year")]; ?></td>		
						<td width="110"><? echo $location_arr[$row[("location_id")]];  ?></td>	
                        <td width="90"><? echo $knitting_source[$row[("party_source")]];  ?></td>
						<td width="60"><? echo change_date_format($row[("bill_date")]); ?></td>
						<td width="120"><? echo $bill_company;?> </td>	
						<td width="80"><? echo $bill_for[$row[("bill_for")]]; ?></td>
                        <td style="word-wrap:break-word; word-break: break-all;"><? echo $unique_challan; ?>&nbsp;</td>
                        <td style="word-wrap:break-word; word-break: break-all;">
                        	<?php echo $row[('batch_no')]; ?>
                        </td>
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

if ($action=="load_php_data_to_form_issue")
{
	$sql="SELECT min(delivery_date) as min_date, max(delivery_date) as max_date FROM subcon_inbound_bill_dtls WHERE mst_id='$data' and status_active=1 and is_deleted=0 group by mst_id";
	
	$sql_result_arr =sql_select($sql); 
	$mindate='';  $maxdate='';
	$mindate=$sql_result_arr[0][csf('min_date')];
	$maxdate=$sql_result_arr[0][csf('max_date')];
	unset($sql_result_arr);
	
	$nameArray= sql_select("select id, bill_no, company_id,upcharge,discount, location_id, bill_date, party_id, party_source, party_location_id, inhouse_bill_from, bill_for, is_posted_account,post_integration_unlock,remarks from subcon_inbound_bill_mst where id='$data'");
	foreach ($nameArray as $row)
	{	
		echo "load_drop_down( 'requires/operator_wise_cutting_entry_controller', '".$row[csf("company_id")]."', 'load_drop_down_location', 'location_td');\n";
		
		
		
		
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_location_name').value			= '".$row[csf("location_id")]."';\n"; 
		echo "document.getElementById('txt_cuitting_date').value 				= '".change_date_format($row[csf("bill_date")])."';\n";   
	
	 
		
		
		
		
		echo "set_button_status(1, permission, 'fnc_operator_wise_cutting_entry',1);\n";
	}
	exit();
}

if($action=="today_cutting_popup")
{
	echo load_html_head_contents("Operator Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	$emp_data=explode("__",$empWiseCuttingData);

	$emp_data_array=array();
	foreach ($emp_data as $val)
	{
		$epm=explode("**",$val);
		$emp_data_array[$epm[0]]=$epm[1];
	}
	// echo"<pre>";print_r($emp_data_array) ;die;
	?>
	<?
	$sql=("SELECT emp_code,first_name from lib_employee where company_id=$company and location_id=$location and floor_id=$floor and status_active=1 ");
	//echo $sql;
	$sql_result=sql_select($sql);
	
	?>
	<script>
		function fn_sum_qty()
		{
			var tot_row = $("#tbl_list tbody tr").length;
			var tot_qty = 0;
			for (let i = 1; i <= tot_row; i++) 
			{
				tot_qty += $('#qty_'+i).val()*1;
			}
			$("#tot_dis_qty").text(tot_qty);
			// alert(tot_qty);
		}

		
		function fn_close()
		{
			var tot_row = $("#tbl_list tbody tr").length;
			var data_string = "";
			var qty = 0;
			var emp_id=0;
			for (let i = 1; i <= tot_row; i++) 
			{
				qty = $('#qty_'+i).val()*1;
				emp_id = $('#emp_id_'+i).val();
				//data_string=qty + emp_id;
				data_string +=  (data_string=="") ? emp_id+'**'+qty : "__"+emp_id+'**'+qty;
			}
			// alert(data_string);
			document.getElementById('data_string').value=data_string;
			parent.emailwindow.hide();
		}

	</script>
    </head>
    <body>
    <div align="center" style="width:550px;">
        <form name="styleRef_form" id="styleRef_form">	
		<input type="hidden" name="data_string" id="data_string">	
		<fieldset style="width:520px;">
            <table width="520" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
					<tr>
						<th colspan="2">Qty Distribution Basis:</th>
						<th>
							<? 
							$distribution_arr=array(1=>"Auto Distribution",2=>"Manual Distribution");
							echo create_drop_down( "cbo_distribution",160,$distribution_arr,"",1, "-- Select  --", $selected );
							?>
						</th>
					     <th style="text-align: center">Total:<span id="tot_dis_qty"></span></th>
					</tr>
					<tr>
						<th width="30">SL</th>
						<th width="150">Operator ID</th>
						<th width="150">Operator Name</th>
						<th width="120">Cutting Qty				
						</th>
					</tr>	
	          </thead>
                <tbody>
				       <?	
						$i=1;			
						foreach($sql_result as $row)
						{

				           ?>
							<tr bgcolor='<? echo $bgcolor; ?>' onclick="change_color('tr_2nd<? echo $i;?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i;?>">
								<td align="center"><?=$i;?></td>
								<td align="center"><?=$row ['EMP_CODE'];?></td>                 
								<td align="center"><?=$row ['FIRST_NAME'];?></td>     
								<td align="center">
								<input type="text" id="qty_<?=$i;?>"  name="qty_<?=$i;?>"  style="width:50px" class="text_boxes_numeric" maxlength="20" onblur="fn_sum_qty();" value="<?=$emp_data_array[$row['EMP_CODE']];?>"/>
								<input type="hidden" id="emp_id_<?=$i;?>"  value="<?= $row ['EMP_CODE']?>"/>
								</td>	 
								
							</tr>
							
							<?
							  $i++; 
				       }
						    ?>       
            	</tbody>
				
           	</table>
				
				<div style="width:70%; float:left" align="center">
						
							<input type="button" name="close" onclick="fn_close();" class="formbutton" value="Close" style="width:100px">
					
				</div>
		   </div>
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

if ($action=="operator_wise_cutting_entry_list_view") 
{
	echo load_html_head_contents("Popup Info","../", 1, 1, $data,1,'');
	//echo $data;
	$data=explode('***',$data);
	$cbo_company_id=$data[0];
	$location_cond=$data[1];	
	$txt_cuitting_date=$data[2];

	
	if($cbo_company_id=='')$company_id_cond=" "; else $company_id_cond=" and d.serving_company = $cbo_company_id";   
	if($location_cond=='')$location=" "; else $location=" and d.location= $location_cond";   
	
    if($txt_cuitting_date!=''){ 
        if($db_type==0){
            
            $txt_cuitting_date=change_date_format($txt_cuitting_date);
         
        }
        else
        {
            $txt_cuitting_date=change_date_format($txt_cuitting_date,'','',-1);
          
        }
        $date_con=" and d.production_date = '$txt_cuitting_date'";   
    }
    else
    {
        $date_con="";   
    }
	$garments_item=return_library_array( "select id, item_name from lib_garment_item",'id','item_name');
	$color_library=return_library_array( "select id,color_name from lib_color ", "id", "color_name"  ); 

	$floor_array=return_library_array( "select id,floor_name from lib_prod_floor  where  company_id=$data[0]  and location_id=$data[1] and  production_process =1 and status_active=1 ", "id", "floor_name"  );

	 //echo "select id,floor_name from lib_prod_floor  where  company_id=$data[0]  and location_id=$data[1] and  production_process =1 and status_active=1";die;
	$table_array=return_library_array( "select id,table_no from lib_cutting_table where company_id=$data[0]  and location_id=$data[1]  and status_active=1 and table_no is not null order by table_no ", "id", "table_no"  );

   // echo "select id,table_name from lib_table_entry where company_name=$data[0]  and location_name=$data[1] and table_type =1  and status_active=1"; die;
	$sql=("SELECT a.id as job_id, b.id as po_id,d.serving_company,d.location, a.job_no,a.style_ref_no,b.po_number,c.id as col_size_id,c.item_number_id,c.color_number_id,c.order_quantity,c.plan_cut_qnty,c.excess_cut_perc,
	  sum(case when d.production_type=1 then e.production_qnty else 0 end) as cutting_qty
	  from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and d.id=e.mst_id and d.po_break_down_id=b.id and e.color_size_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and e.production_qnty>0 and d.production_type=1 $company_id_cond $location $date_con group by a.id,b.id,d.serving_company,d.location,a.job_no,a.style_ref_no,b.po_number,c.id,c.item_number_id,c.color_number_id,c.order_quantity,c.plan_cut_qnty,c.excess_cut_perc
	  ");
	//echo $sql;die;
	$res = sql_select($sql);
	if(count($res)==0)
	{
		?>
		<div style="width:70%" class="alert alert-danger">Data not found! Please try again.</div>
		<?
		die;
	}
	$data_array = array();
	$po_id_array=array();
	
	foreach($res as $val)
	{
		$data_array[$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['job_no'] = $val['JOB_NO'];
		$data_array[$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['job_id'] = $val['JOB_ID'];
		$data_array[$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['style_ref_no'] = $val['STYLE_REF_NO'];
		$data_array[$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['po_number'] = $val['PO_NUMBER'];
		
		$data_array[$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['cutting_qty'] += $val['CUTTING_QTY'];
		$po_id_array[$val['PO_ID']]=$val['PO_ID'];
	}	
	//echo "<pre>";print_r($data_array);
	$po_ids=implode(",",$po_id_array);
	$color_sql="SELECT po_break_down_id as po_id,item_number_id,color_number_id,order_quantity,plan_cut_qnty,excess_cut_perc from wo_po_color_size_breakdown where po_break_down_id in($po_ids) and status_active=1 and is_deleted=0";
   //echo $color_sql;die;

     $color_sql_results=sql_select($color_sql);
	 $po_qnty_array=array();
	 foreach($color_sql_results as $v)
	 {
		$po_qnty_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['order_quantity'] += $v['ORDER_QUANTITY'];
		$po_qnty_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['plan_cut_qnty'] += $v['PLAN_CUT_QNTY'];
		$po_qnty_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['excess_cut_perc'] += $v['EXCESS_CUT_PERC'];
	 }
	// echo "<pre>";print_r($po_qnty_array);die;

	if($cbo_company_id=='')$company_id_cond1=" "; else $company_id_cond1=" and a.company_id = $cbo_company_id";   
	if($location_cond=='')$location1=" "; else $location1=" and a.location_id = $location_cond";   
	
    if($txt_cuitting_date!=''){ 
        if($db_type==0){
            
            $txt_cuitting_date=change_date_format($txt_cuitting_date);
         
        }
        else
        {
            $txt_cuitting_date=change_date_format($txt_cuitting_date,'','',-1);
          
        }
        $date_con1=" and a.entry_date = '$txt_cuitting_date'";   
    }
    else
    {
        $date_con="";   
    }
	$sql_result=("SELECT a.id, a.style_ref,a.job_id,a.po_id,a.item_id,a.color_id,a.floor_id,a.table_id,a.process_id,b.operator_id,b.qty from operator_wise_cutting_entry_mst a ,operator_wise_cutting_entry_dtld b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 $company_id_cond1
	$location1 $date_con1 ");
	// echo $sql_result;die;
	$result = sql_select($sql_result);
	$operator_data_array=array();
	foreach($result as $row)
	{
		$operator_data_array[$row['PO_ID']][$row['ITEM_ID']][$row['COLOR_ID']]['floor_id'] = $row['FLOOR_ID'];

		$operator_data_array[$row['PO_ID']][$row['ITEM_ID']][$row['COLOR_ID']]['table_id'] = $row['TABLE_ID'];

		$operator_data_array[$row['PO_ID']][$row['ITEM_ID']][$row['COLOR_ID']]['process_id'] = $row['PROCESS_ID'];
		$operator_data_array[$row['PO_ID']][$row['ITEM_ID']][$row['COLOR_ID']]['operator_id'] = $row['OPERATOR_ID'];
		$operator_data_array[$row['PO_ID']][$row['ITEM_ID']][$row['COLOR_ID']]['job_id'] = $row['JOB_ID'];
		$operator_data_array[$row['PO_ID']][$row['ITEM_ID']][$row['COLOR_ID']]['style_ref'] = $row['STYLE_REF'];
		// $operator_data_array[$row['PO_ID']][$row['ITEM_ID']][$row['COLOR_ID']]['qty'] = $row['QTY'];
		$operator_data_array[$row['PO_ID']][$row['ITEM_ID']][$row['COLOR_ID']]['id'] = $row['ID'];
		if($operator_data_array[$row['PO_ID']][$row['ITEM_ID']][$row['COLOR_ID']]['emp_data']=="")
		{
			$operator_data_array[$row['PO_ID']][$row['ITEM_ID']][$row['COLOR_ID']]['emp_data'] = $row['OPERATOR_ID']."**".$row['QTY'];
			$operator_data_array[$row['PO_ID']][$row['ITEM_ID']][$row['COLOR_ID']]['qty'] = $row['QTY'];
			// echo $row['PO_ID']."*".$row['ITEM_ID']."*".$row['COLOR_ID']."*".$row['QTY']."<br>";
		}
		else
		{
			$operator_data_array[$row['PO_ID']][$row['ITEM_ID']][$row['COLOR_ID']]['emp_data'] .= "__".$row['OPERATOR_ID']."**".$row['QTY'];
			$operator_data_array[$row['PO_ID']][$row['ITEM_ID']][$row['COLOR_ID']]['qty'] += $row['QTY'];
			// echo $row['PO_ID']."*".$row['ITEM_ID']."*".$row['COLOR_ID']."*".$row['QTY']."<br>";
		}		
		
	}	
	// echo "<pre>";print_r($operator_data_array);die;

 
		?>
            <div >
                <table cellspacing="0" cellpadding="0" border="1" rules="all"  align="center" width="1800px" class="rpt_table" >
                    <thead>
						<tr>
							<th width="50">SL</th>
							<th width="120">Order No</th>
							<th width="150">Style ref.</th>
							<th width="120">Job No.</th>
							<th width="150">Garments Item</th>
							<th width="150">Color</th>
							<th width="100">Order Qty</th>                    
							<th width="100">Excess%</th>
							<th width="100">Plan Cut Qty</th>
							<th width="100">Cutting Qty</th>
							<th width="100">Balance</th>
							<th width="150" class="must_entry_caption">Floor</th>
							<th width="100" class="must_entry_caption">Table</th>
							<th width="100" class="must_entry_caption">Process</th>
							<th width="120">Today Cutting</th>
						</tr>                    
                    </thead>
                 </table>
			</div>

			 <div>
				<table  cellspacing="0" cellpadding="0" border="1" rules="all"  align="center" width="1800px" class="rpt_table" id="tbl_list_search">  
					<tbody>
						<?
						$i=1;
						foreach ($data_array as $po_id =>$po_data)
						{  
							foreach ($po_data as $item_id => $item_data)
							{  
								foreach ($item_data as $color_id => $row)
								{ 	
									
									if($row['cutting_qty']>0)
									{
										$blance=$po_qnty_array[$po_id][$item_id][$color_id]['plan_cut_qnty'] - $row['cutting_qty'];	
										if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";				
										?>
										<tr bgcolor='<? echo $bgcolor; ?>
										' onclick="change_color('tr_2nd<? echo $i;?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i;?>">
										<td width="50"><p><?echo $i ;?></p></td>
										<td width="120"><p><?echo  $row['po_number'];?></p></td>
										<td width="150"><p><?echo  $row['style_ref_no'];?></p></td>
										<td width="120"><p><?echo  $row['job_no'];?></p></td>
										<td width="150"><p><?echo  $garments_item[$item_id];?></p></td>
										<td width="150"><p><?echo  $color_library[$color_id];?></p></td>
										<td width="100" align="right"><p><?echo $po_qnty_array[$po_id][$item_id][$color_id]['order_quantity'];?></p></td>                    
										<td width="100" align="right"><p><?echo $po_qnty_array[$po_id][$item_id][$color_id]['excess_cut_perc'];?></p></td>
										<td width="100" align="right"><p><?echo $po_qnty_array[$po_id][$item_id][$color_id]['plan_cut_qnty'];?></p></td>
										<td width="100" align="right"><p><?echo  $row['cutting_qty'];?></p></td>
										<td width="100" align="right"><p><?= $blance?></p></td>
										
										<td width="150">
											<?
											//echo $floor_id;
											$floor_id = $operator_data_array[$po_id][$item_id][$color_id]['floor_id'];
											// echo $floor_id."*".$po_id."*".$item_id."*".$color_id;
											echo create_drop_down( "cbofloorName_$i", 150, $floor_array,"", 1, "-- Select --", $floor_id, "fn_get_table(this.id,this.value);",0 ); 
											?>
										</td>
										<td width="100" id="td_table_<?=$i;?>">
											<?
											$table_id = $operator_data_array[$po_id][$item_id][$color_id]['table_id'];
											//echo $table_id;
											echo create_drop_down( "cbotableName_$i", 100, $table_array,"", 1, "-- Select --", $table_id , "",""); 
											?>
										</td>
										<td width="100">
											<?
											$process_id = $operator_data_array[$po_id][$item_id][$color_id]['process_id'];
											$cut_qty = $operator_data_array[$po_id][$item_id][$color_id]['qty'];
											echo create_drop_down("cboprocess_$i", 100, $process_array,"", 1,"-- Select Process--", $process_id,"","");
											?>
										</td>
										<td width="120" >
											<input type="text" id="txtcutting_<?=$i;?>"  name="txtcutting_<?=$i;?>"  style="width:110px" class="text_boxes" onDblClick="cutting_popup(<?=$i;?>)" placeholder="Browse" readonly value="<?=$cut_qty;?>" />
											<input type="hidden" name="empWiseCuttingData_<?=$i;?>" id="empWiseCuttingData_<?=$i;?>" value="<?= $operator_data_array[$po_id][$item_id][$color_id]['emp_data']; ?>">
											<input type="hidden" name="jobId_<?=$i;?>" id="jobId_<?=$i;?>" value="<?=$row['job_id']?>">
											<input type="hidden" name="orderId_<?=$i;?>" id="orderId_<?=$i;?>" value="<?=$po_id?>">
											<input type="hidden" name="gtmsId_<?=$i;?>" id="gtmsId_<?=$i;?>" value="<?=$item_id?>">
											<input type="hidden" name="colorId_<?=$i;?>" id="colorId_<?=$i;?>" value="<?=$color_id?>">
											<input type="hidden" name="styleRefNo_<?=$i;?>" id="styleRefNo_<?=$i;?>" value="<?=$row['style_ref_no']?>">
											<input type="hidden" name="mstId_<?=$i;?>" id="mstId_<?=$i;?>" value="<?= $operator_data_array[$po_id][$item_id][$color_id]['id']; ?>">
											</td>
										</tr>
										
										<?
										$i++;
									}	
								}
							}
						}
						?>
					</tbody>
               </table>	
			</div>  
	</html>
    <?
	
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	// echo $action;die;
	// print_r($process);die;
	
	if ($operation==0)   // Insert Here========================================================================================
	{		
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$field_array="id, company_id, location_id,entry_date,style_ref, job_id, po_id,item_id, color_id, floor_id, table_id, process_id,inserted_by, insert_date,status_active,is_deleted";
		
		$field_array_up="company_id*location_id*entry_date*style_ref*job_id*po_id*item_id*color_id*floor_id*table_id*process_id*updated_by*update_date";
		//print_r( $field_array_up);die;	
		
		$field_array_dtls="id, mst_id, operator_id,qty,inserted_by, insert_date,status_active,is_deleted";
		
		$data_array_dtls = "";
	
		
		$data_array_up = array();
		$mst_id_array = array();
		//echo $cbo_company_id;die;
		
		for($j=1;$j<=$row_num;$j++)
		{
			$cuttingdata="empWiseCuttingData_".$j;
			$jobId="jobId_".$j;
			$orderId="orderId_".$j;
			$gtmsId="gtmsId_".$j;
			$colorId="colorId_".$j;
			$cbofloorName="cbofloorName_".$j;
			$cbotableName="cbotableName_".$j;
			$styleRefNo="styleRefNo_".$j;
			$cboprocess="cboprocess_".$j;
			$mstId="mstId_".$j;
			if($$mstId>0)
			{
				//echo  $$mstId."888";
				if(str_replace("'","",$$cuttingdata) !="")
				{
					 //echo  $$cuttingdata."888<br>";
					$mst_id_array[]=$$mstId;
					$data_array_up[$$mstId]=explode("*",("".$cbo_company_id."*".$cbo_location_name."*".$txt_cuitting_date."*'".$$styleRefNo."'*".$$jobId."*".$$orderId."*".$$gtmsId."*".$$colorId."*".$$cbofloorName."*".$$cbotableName."*".$$cboprocess."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

					// =========== for dtls table ==============
					$cuttingdataArr=explode("__", str_replace("'","",$$cuttingdata));
					foreach ($cuttingdataArr as $rowStr) 
					{
						// print_r($cuttingdataArr);
						$mst_id = return_next_id_by_sequence("operator_wise_cutting_entry_dtld_seq", "operator_wise_cutting_entry_dtld", $con);

						$QntyArr = explode("**",$rowStr);
						$op_id = $QntyArr[0];
						$qty = $QntyArr[1];

						
						if($data_array_dtls!="") $data_array_dtls.=",";
						$data_array_dtls.="(".$mst_id.",".$$mstId.",".$op_id . "," . $qty . ",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
					}
				}
			}
			else
			{
				if(str_replace("'","",$$cuttingdata) !="")
				{
					$id = return_next_id_by_sequence("operator_wise_cutting_entry_mst_seq", "operator_wise_cutting_entry_mst", $con);
					// echo  $$cuttingdata."888<br>";
					// $mst_id_array[]=$id;
					if($data_array!="") $data_array.=",";
					$data_array.="(".$id.",".$cbo_company_id.",".$cbo_location_name.",".$txt_cuitting_date.",'".$$styleRefNo."',".$$jobId.",".$$orderId.",".$$gtmsId.",".$$colorId.",".$$cbofloorName.",".$$cbotableName.",".$$cboprocess.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";

					// =========== for dtls table ==============
					$cuttingdataArr=explode("__", str_replace("'","",$$cuttingdata));
					foreach ($cuttingdataArr as $rowStr) 
					{
						// print_r($cuttingdataArr);
						$mst_id = return_next_id_by_sequence("operator_wise_cutting_entry_dtld_seq", "operator_wise_cutting_entry_dtld", $con);

						$QntyArr = explode("**",$rowStr);
						$op_id = $QntyArr[0];
						$qty = $QntyArr[1];

						
						if($data_array_dtls!="") $data_array_dtls.=",";
						$data_array_dtls.="(".$mst_id.",".$id.",".$op_id . "," . $qty . ",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
					}
					
				}
			}
		}
	  	// echo $data_array_dtls; die;
		// echo  $data_array_dtls_up."888<br>";die;
	 	//	 echo bulk_update_sql_statement( "operator_wise_cutting_entry_dtld", "id", $field_array_dtls, $data_array_dtls_up, $mst_id_array );die;
		$rID1=$rID2=$rID3=$rID4=true;
		if(count($data_array_up)>0)
		{
			$rID1=execute_query(bulk_update_sql_statement( "OPERATOR_WISE_CUTTING_ENTRY_MST", "id", $field_array_up, $data_array_up, $mst_id_array ));
		}
	    if($data_array!="")
		{
			$rID2=sql_insert("operator_wise_cutting_entry_mst",$field_array,$data_array);
		}
		$deleted_id = implode(",",$mst_id_array);
		if($deleted_id!="")
		{
			$rID3=execute_query( "delete from operator_wise_cutting_entry_dtld where mst_id in ($deleted_id)",0);
		}

		// echo "10**insert into operator_wise_cutting_entry_dtld (".$field_array_dtls.") values ".$data_array_dtls;die;
	    if($data_array_dtls!="")
		{
			$rID4=sql_insert("operator_wise_cutting_entry_dtld",$field_array_dtls,$data_array_dtls);
		}	
		
		
		if($db_type==0)
		{
			if($rID1 && $rID2 && $rID3  && $rID4)
			{
				mysql_query("COMMIT");  
				echo "0**".$id."**".$mst_id;;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID1 && $rID2 && $rID3  && $rID4)
			{
				oci_commit($con);  
				echo "1**".$id."**".$mst_id;
			}
			else
			{
				oci_rollback($con);
				echo "6**".$id."**".$mst_id;
			}
		}
		disconnect($con);
		die;	
		
	}
	else if ($operation==1)   // Update Here
	{

		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$field_array="id, company_id, location_id,entry_date,style_ref, job_id, po_id,item_id, color_id, floor_id, table_id, process_id,inserted_by, insert_date,status_active,is_deleted";

		$field_array_up="company_id*location_id*entry_date*style_ref*job_id*po_id*item_id*color_id*floor_id*table_id*process_id*updated_by*update_date";
		//print_r( $field_array_up);die;	
		
		$field_array_dtls="id, mst_id, operator_id,qty,inserted_by, insert_date,status_active,is_deleted";
		
		$data_array_dtls = "";
	
		
		$data_array_up = array();
		$mst_id_array = array();
		//echo $cbo_company_id;die;
		
		for($j=1;$j<=$row_num;$j++)
		{
			$cuttingdata="empWiseCuttingData_".$j;
			$jobId="jobId_".$j;
			$orderId="orderId_".$j;
			$gtmsId="gtmsId_".$j;
			$colorId="colorId_".$j;
			$cbofloorName="cbofloorName_".$j;
			$cbotableName="cbotableName_".$j;
			$styleRefNo="styleRefNo_".$j;
			$cboprocess="cboprocess_".$j;
			$mstId="mstId_".$j;
			if($$mstId>0)
			{
				//echo  $$mstId."888";
				if(str_replace("'","",$$cuttingdata) !="")
				{
					 //echo  $$cuttingdata."888<br>";
					$mst_id_array[]=$$mstId;
					$data_array_up[$$mstId]=explode("*",("".$cbo_company_id."*".$cbo_location_name."*".$txt_cuitting_date."*'".$$styleRefNo."'*".$$jobId."*".$$orderId."*".$$gtmsId."*".$$colorId."*".$$cbofloorName."*".$$cbotableName."*".$$cboprocess."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

					// =========== for dtls table ==============
					$cuttingdataArr=explode("__", str_replace("'","",$$cuttingdata));
					foreach ($cuttingdataArr as $rowStr) 
					{
						// print_r($cuttingdataArr);
						$mst_id = return_next_id_by_sequence("operator_wise_cutting_entry_dtld_seq", "operator_wise_cutting_entry_dtld", $con);

						$QntyArr = explode("**",$rowStr);
						$op_id = $QntyArr[0];
						$qty = $QntyArr[1];

						
						if($data_array_dtls!="") $data_array_dtls.=",";
						$data_array_dtls.="(".$mst_id.",".$$mstId.",".$op_id . "," . $qty . ",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
					}
				}
			}
			else
			{
				if(str_replace("'","",$$cuttingdata) !="")
				{
					$id = return_next_id_by_sequence("operator_wise_cutting_entry_mst_seq", "operator_wise_cutting_entry_mst", $con);
					// echo  $$cuttingdata."888<br>";
					// $mst_id_array[]=$id;
					if($data_array!="") $data_array.=",";
					$data_array.="(".$id.",".$cbo_company_id.",".$cbo_location_name.",".$txt_cuitting_date.",'".$$styleRefNo."',".$$jobId.",".$$orderId.",".$$gtmsId.",".$$colorId.",".$$cbofloorName.",".$$cbotableName.",".$$cboprocess.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";

					// =========== for dtls table ==============
					$cuttingdataArr=explode("__", str_replace("'","",$$cuttingdata));
					foreach ($cuttingdataArr as $rowStr) 
					{
						// print_r($cuttingdataArr);
						$mst_id = return_next_id_by_sequence("operator_wise_cutting_entry_dtld_seq", "operator_wise_cutting_entry_dtld", $con);

						$QntyArr = explode("**",$rowStr);
						$op_id = $QntyArr[0];
						$qty = $QntyArr[1];

						
						if($data_array_dtls!="") $data_array_dtls.=",";
						$data_array_dtls.="(".$mst_id.",".$id.",".$op_id . "," . $qty . ",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
					}
					
				}
			}
		}
	  	// echo $data_array_dtls; die;
		// echo  $data_array_dtls_up."888<br>";die;
	 	//	 echo bulk_update_sql_statement( "operator_wise_cutting_entry_dtld", "id", $field_array_dtls, $data_array_dtls_up, $mst_id_array );die;
		$rID1=$rID2=$rID3=$rID4=true;
		if(count($data_array_up)>0)
		{
			$rID1=execute_query(bulk_update_sql_statement( "OPERATOR_WISE_CUTTING_ENTRY_MST", "id", $field_array_up, $data_array_up, $mst_id_array ));
		}
	    if($data_array!="")
		{
			$rID2=sql_insert("operator_wise_cutting_entry_mst",$field_array,$data_array);
		}
		$deleted_id = implode(",",$mst_id_array);
		if($deleted_id!="")
		{
			$rID3=execute_query( "delete from operator_wise_cutting_entry_dtld where mst_id in ($deleted_id)",0);
		}

		// echo "10**insert into operator_wise_cutting_entry_dtld (".$field_array_dtls.") values ".$data_array_dtls;die;
	    if($data_array_dtls!="")
		{
			$rID4=sql_insert("operator_wise_cutting_entry_dtld",$field_array_dtls,$data_array_dtls);
		}	
		
		
		if($db_type==0)
		{
			if($rID1 && $rID2 && $rID3  && $rID4)
			{
				mysql_query("COMMIT");  
				echo "0**".$id."**".$mst_id;;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID1 && $rID2 && $rID3  && $rID4)
			{
				oci_commit($con);  
				echo "1**".$id."**".$mst_id;
			}
			else
			{
				oci_rollback($con);
				echo "6**".$id."**".$mst_id;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)  //Delete here======================================================================================
	{
		
	}
}

