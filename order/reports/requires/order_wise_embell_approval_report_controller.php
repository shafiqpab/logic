<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];



if($db_type==0)
{
	$defalt_date_format="0000-00-00";
}
else
{
	$defalt_date_format="";
}



//--------------------------------------------------------------------------------------------------------------------
$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$company_library_short=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
$supplier_library=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
$team_library=return_library_array( "select id, team_name from lib_marketing_team", "id", "team_name"  );
$team_member_library=return_library_array( "select id,team_member_name from  lib_mkt_team_member_info", "id", "team_member_name"  );

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );   	 
	exit();
}
if ($action=="load_drop_down_team_member")
{
if($data!=0)
	{
        echo create_drop_down( "cbo_team_member", 140, "select id,team_member_name 	 from lib_mkt_team_member_info  where team_id='$data' and status_active=1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-Select Team Member-", $selected, "" ); 
	}
 else
   {
		 echo create_drop_down( "cbo_team_member", 140, $blank_array,"", 1, "-Select Team Member- ", $selected, "" );
   }
}

$tmplte=explode("**",$data);

if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;


if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if(str_replace("'","",$cbo_company_name)==0) $company_name="%%"; else $company_name=str_replace("'","",$cbo_company_name);
	//if(str_replace("'","",$cbo_buyer_name)==0) $buyer_name="%%"; else $buyer_name=str_replace("'","",$cbo_buyer_name);
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
	//echo $txt_date_from;
/*	$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
	$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
*/	
   $job_cond="";
	if(str_replace("'","",$txt_job_no)!="") 
		{  
			$job_cond=" and a.job_no_prefix_num=".str_replace("'","",$txt_job_no)." ";
		    if($db_type==2) $job_cond.=" and extract(year from a.insert_date)=".str_replace("'","",$cbo_year)."";
	        if($db_type==0) $job_cond.=" and year(a.insert_date)=".str_replace("'","",$cbo_year)."";
		
		}
	$style_cond="";
	if(str_replace("'","",$txt_style)!="") $style_cond=" and a.style_ref_no like '%".str_replace("'","",$txt_style)."%'  ";
	$team_cond="";
	if(str_replace("'","",$cbo_team_name)!=0) $team_cond=" and a.team_leader=".str_replace("'","",$cbo_team_name)."  ";
	if(str_replace("'","",$cbo_team_member)!=0) $team_cond.=" and a.dealing_marchant=".str_replace("'","",$cbo_team_member)."  ";
	$file_cond="";
	if(str_replace("'","",$txt_file)!="") $file_cond=" and b.file_no='".str_replace("'","",$txt_file)."'";
	$ref_cond="";
	if(str_replace("'","",$txt_ref)!="") $ref_cond=" and b.grouping='".str_replace("'","",$txt_ref)."'";
	
	$date_cond='';
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		$start_date=(str_replace("'","",$txt_date_from));
		$end_date=(str_replace("'","",$txt_date_to));
		/*if($db_type==0)
		{
			$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
			$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
		}
		else
		{
			
			$start_date=date("j-M-Y",strtotime(str_replace("'","",$txt_date_from)));
			$end_date=date("j-M-Y",strtotime(str_replace("'","",$txt_date_to)));
		}*/
		$date_cond=" and b.pub_shipment_date between '$start_date' and '$end_date'";
	}
	if($template==1)
	{
		ob_start();
	?>
		<div style="width:2080px">
		<fieldset style="width:100%;">	
			<table width="2160">
				<tr class="form_caption">
					<td colspan="20" align="center">Embellishment Approval Report</td>
				</tr>
				<tr class="form_caption">
					<td colspan="20" align="center"><? echo $company_library[$company_name]; ?></td>
				</tr>
			</table>
            <div id="data_panel2" align="center" style="width:850px">
                 <input type="button" value="Print Preview" class="formbutton" style="width:100px" name="print" id="print" onclick="new_window('',2)" />
            </div>
            <div style="width:850px" id="embell_approval_div">
            	<div style="width:850px" align="center"><b>Embellishment Approval Summary</b></div>
                <?
					$arr=array (0=>$company_library_short,1=>$buyer_short_name_library);
					$sql="select a.company_name,a.buyer_name,
					  COUNT(c.embellishment_type_id) AS total_embell,
					  COUNT(CASE WHEN c.sent_to_supplier!='$defalt_date_format' THEN 1 END) AS send_to_supp,
					  COUNT(CASE WHEN c.approval_status='1' THEN 1 END) AS submitted_to_buyer,
					  COUNT(CASE WHEN c.approval_status='3' THEN 1 END) AS approved_quantity,
					  COUNT(CASE WHEN c.approval_status='2' THEN 1 END) AS rejected_quantity,
					  COUNT(CASE WHEN c.approval_status='3' THEN 1 END)/COUNT(c.color_name_id)*100 as approved_percent
					  from wo_po_details_master a, wo_po_break_down b, wo_po_embell_approval c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.company_name like '$company_name' and c.current_status=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $buyer_id_cond $date_cond group by a.company_name, a.buyer_name order by a.company_name, a.buyer_name";
					//echo $sql;die; 
					echo create_list_view("list_view,approval_div", "Company Name,Buyer Name,Total Embell,Sent To Supplier,Sent To Buyer,Approved Quantity,Rejected Quantity,Approval %", "80,80,100,100,100,100,100,100","850","380",1, $sql , "", "", "", 1, "company_name,buyer_name,0,0,0,0,0,0", $arr , "company_name,buyer_name,total_embell,send_to_lab,submitted_to_buyer,approved_quantity,rejected_quantity,approved_percent", "",'','0,0,1,1,1,1,1,2',"3,total_labdip,send_to_supp,submitted_to_buyer,approved_quantity,rejected_quantity,''");
				?>
            </div>
            <br />
			<table class="rpt_table" width="2160" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<th width="30">SL</th>
					<th width="100">Buyer</th>
                    <th width="130">Team</th>
                    <th width="170">Team Member</th>
					<th width="60">Job No</th>
                    <th width="50">Year</th>
					<th width="130">Style Ref</th>
					<th width="200">Order No</th>
                    <th width="80">File No</th>
                    <th width="80">Ref.No</th>
					<th width="80">Order Qnty</th>
					<th width="50">UOM</th>
					<th width="80">Qnty (Pcs)</th>
					<th width="80">Shipment Date</th>
					<th width="100">Embell. Name</th>
                    <th width="100">Embell. Type</th>
                    <th width="80">Target app. date</th>
                    <th width="80">Send Date</th>
                    <th width="80">Submission Date</th>
                    <th width="80">Status</th>
                    <th width="80">Approval/ Reject Date</th>
                    <th width="100">Supplier</th>
                    <th>Comment</th>
				</thead>
			</table>
			<div style="width:2190px; max-height:400px; overflow-y:scroll" id="scroll_body">
				<table class="rpt_table" width="2160" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
				<?
				$query=sql_select("select id,po_break_down_id,embellishment_id,embellishment_type_id, target_approval_date, sent_to_supplier, submitted_to_buyer, approval_status, approval_status_date, supplier_name, embellishment_comments 
				from wo_po_embell_approval 
				where current_status=1 and status_active=1 and is_deleted=0 order by embellishment_id,embellishment_type_id");
				$reference_arr=array();
				foreach($query as $row)
				{
					$reference_arr[$row[csf("po_break_down_id")]][$row[csf("id")]]["id"]=$row[csf("id")];
					$reference_arr[$row[csf("po_break_down_id")]][$row[csf("id")]]["po_break_down_id"]=$row[csf("po_break_down_id")];
					$reference_arr[$row[csf("po_break_down_id")]][$row[csf("id")]]["embellishment_id"]=$row[csf("embellishment_id")];
					$reference_arr[$row[csf("po_break_down_id")]][$row[csf("id")]]["embellishment_type_id"]=$row[csf("embellishment_type_id")];
					$reference_arr[$row[csf("po_break_down_id")]][$row[csf("id")]]["target_approval_date"]=$row[csf("target_approval_date")];
					$reference_arr[$row[csf("po_break_down_id")]][$row[csf("id")]]["sent_to_supplier"]=$row[csf("sent_to_supplier")];
					$reference_arr[$row[csf("po_break_down_id")]][$row[csf("id")]]["submitted_to_buyer"]=$row[csf("submitted_to_buyer")];
					$reference_arr[$row[csf("po_break_down_id")]][$row[csf("id")]]["approval_status"]=$row[csf("approval_status")];
					$reference_arr[$row[csf("po_break_down_id")]][$row[csf("id")]]["approval_status_date"]=$row[csf("approval_status_date")];
					$reference_arr[$row[csf("po_break_down_id")]][$row[csf("id")]]["supplier_name"]=$row[csf("supplier_name")];
					$reference_arr[$row[csf("po_break_down_id")]][$row[csf("id")]]["embellishment_comments"]=$row[csf("embellishment_comments")];
				}
				

				
				$i=1; $total_order_qnty=0; $total_req_qnty=0; $total_order_qnty_in_pcs=0; $s=1; $current_date=date("Y-m-d");
				if($db_type==2) $year_insert="  extract(year from a.insert_date) as year";
	            if($db_type==0) $year_insert="  SUBSTRING_INDEX(a.insert_date, '-', 1) as year";
				$sql="select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name,a.team_leader,a.dealing_marchant,$year_insert, a.order_uom, a.style_ref_no, a.total_set_qnty as ratio, b.id, b.po_number, b.file_no,b.grouping as ref_no,b.pub_shipment_date, b.po_quantity, b.unit_price, b.po_total_price from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond $buyer_id_cond $job_cond $style_cond $file_cond $ref_cond $team_cond  order by b.id, b.pub_shipment_date";// and a.buyer_name like '$buyer_name'
				//echo $sql;die;
				
				
				$nameArray=sql_select($sql);
				$tot_rows=count($nameArray);
				foreach($nameArray as $row )
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$k=1;
					
					$order_qnty_in_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
					$total_order_qnty+=$row[csf('po_quantity')];
					$total_order_qnty_in_pcs+=$order_qnty_in_pcs;
					?>
					<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_d<? echo $s; ?>','<? echo $bgcolor;?>')" id="tr_d<? echo $s; ?>">
						<td width="30"><p><? echo $i; ?>&nbsp;</p></td>
						<td width="100" align="center"><p><? echo $buyer_short_name_library[$row[csf('buyer_name')]]; ?>&nbsp;</p></td>
						<td width="130" align="center"><p><? echo $team_library[$row[csf('team_leader')]]; ?>&nbsp;</p></td>
                        <td width="170" align="center"><p><? echo $team_member_library[$row[csf('dealing_marchant')]]; ?>&nbsp;</p></td>
                        <td width="60" align="center"><p><? echo $row[csf('job_no_prefix_num')]; ?>&nbsp;</p></td>
                        <td width="50" align="center"><p><? echo $row[csf('year')]; ?>&nbsp;</p></td>
						<td width="130" align="center"><p><? echo $row[csf('style_ref_no')]; ?>&nbsp;</p></td>
						<td width="200" align="center"><p><? echo $row[csf('po_number')]; ?>&nbsp;</p></td>
                        <td width="80" align="center"><p><? echo $row[csf('file_no')]; ?>&nbsp;</p></td>
                        <td width="80" align="center"><p><? echo $row[csf('ref_no')]; ?>&nbsp;</p></td>
						<td width="80" align="right"><p><? echo number_format($row[csf('po_quantity')],0,'.',''); ?>&nbsp;</p></td>
						<td width="50" align="center"><p><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?>&nbsp;</p></td>
						<td width="80" align="right"><p><? echo number_format($order_qnty_in_pcs,0,'.',''); ?>&nbsp;</p></td>
						<td width="80" align="center"><p><? echo change_date_format($row[csf('pub_shipment_date')]); ?>&nbsp;</p></td>
					<?
					//$query="select embellishment_id,embellishment_type_id, target_approval_date, sent_to_supplier, submitted_to_buyer, approval_status, approval_status_date, supplier_name, embellishment_comments from wo_po_embell_approval where po_break_down_id='".$row[csf('id')]."' and job_no_mst='".$row[csf('job_no')]."' and current_status=1 and status_active=1 and is_deleted=0 order by embellishment_id,embellishment_type_id";
					//$embellArray=sql_select($query);
					
					foreach($reference_arr[$row[csf('id')]] as $selectResult)
					{
						if($selectResult[('embellishment_id')]==1)
						{ 
							$embell_type=$emblishment_print_type[$selectResult[('embellishment_type_id')]];
						}
						else if($selectResult[('embellishment_id')]==2) 
						{
							$embell_type=$emblishment_embroy_type[$selectResult[('embellishment_type_id')]];
						}
						else if($selectResult[('embellishment_id')]==3) 
						{
							$embell_type=$emblishment_wash_type[$selectResult[('embellishment_type_id')]];
						}
						else if($selectResult[('embellishment_id;')]==4)
						{ 
							$embell_type=$emblishment_spwork_type[$selectResult[('embellishment_type_id')]];
						}
						else if($selectResult[('embellishment_id')]==5) 
						{		
							$embell_type="-- Select --";
						}
						
						if($selectResult[('approval_status')]!=3 && $selectResult[('target_approval_date')] < $current_date &&( $selectResult[('target_approval_date')]!='0000-00-00' || $selectResult[('target_approval_date')]!=''))
						{
							$td_color="#FF0000";
						}
						else
						{
							$td_color="";
						}
						
						if($k==1)
						{
						?>
								<td width="100"><p><? echo $emblishment_name_array[$selectResult[('embellishment_id')]]; ?>&nbsp;</p></td>
                                <td width="100"><p><? echo $embell_type; ?></p></td>
                                <td width="80" align="center" bgcolor="<? echo $td_color; ?>"><p><? echo change_date_format($selectResult[('target_approval_date')]); ?>&nbsp;</p></td>
                                <td width="80" align="center"><p><? echo change_date_format($selectResult[('sent_to_supplier')]); ?>&nbsp;</p></td>
                                <td width="80" align="center"><p><? echo change_date_format($selectResult[('submitted_to_buyer')]); ?>&nbsp;</p></td>
                                <td width="80" align="center"><p><? echo $approval_status[$selectResult[('approval_status')]]; ?>&nbsp;</p></td>
                                <td width="80" align="center"><p><? echo change_date_format($selectResult[('approval_status_date')]); ?>&nbsp;</p></td>
                                <td width="100"><p><? echo $supplier_library[$selectResult[('supplier_name')]]; ?>&nbsp;</p></td>
                                <td><p><? echo $selectResult[('embellishment_comments')]; ?>&nbsp;</p></td>
							</tr>
						<?	
						}
						else
						{
						?>
							<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_d<? echo $s; ?>','<? echo $bgcolor;?>')" id="tr_d<? echo $s; ?>">
								<td width="30">
									<font style="display:none" color="<? echo $bgcolor; ?>">
										<? echo $i; ?>
									</font>
								</td>
								<td width="100">
									<font style="display:none" color="<? echo $bgcolor; ?>">
										<? echo $buyer_short_name_library[$row[csf('buyer_name')]]; ?>
									</font>
								</td>
								<td width="130">
									<font style="display:none" color="<? echo $bgcolor; ?>">
										<? echo $row[csf('job_no')]; ?>
									</font>
								</td>
                                	<td width="170">
									<font style="display:none" color="<? echo $bgcolor; ?>">
										<? echo $row[csf('job_no')]; ?>
									</font>
								</td>
                                	<td width="60">
									<font style="display:none" color="<? echo $bgcolor; ?>">
										<? echo $row[csf('job_no')]; ?>
									</font>
								</td>
                                	<td width="50">
									<font style="display:none" color="<? echo $bgcolor; ?>">
										<? echo $row[csf('job_no')]; ?>
									</font>
								</td>
                                
								<td width="130">
									<font style="display:none" color="<? echo $bgcolor; ?>">
										<p>
											<? echo $row[csf('style_ref_no')]; ?>
										</p>
									</font>
								</td>
								<td width="200">
									<font style="display:none" color="<? echo $bgcolor; ?>">
										<p>
											<? echo $row[csf('po_number')]; ?>
										</p>
									</font>
								</td>
                                <td width="80">
									<font style="display:none" color="<? echo $bgcolor; ?>">
										<p>
											<? echo $row[csf('file_no')]; ?>
										</p>
									</font>
								</td>
                                <td width="80">
									<font style="display:none" color="<? echo $bgcolor; ?>">
										<p>
											<? echo $row[csf('ref_no')]; ?>
										</p>
									</font>
								</td>
								<td width="80" align="right">&nbsp;</td>
								<td width="50" align="center">
									<font style="display:none" color="<? echo $bgcolor; ?>">
										<? echo $unit_of_measurement[$row[csf('order_uom')]]; ?>
									</font>
								</td>
								<td width="80" align="right">&nbsp;</td>
								<td width="80" align="center">
									<font style="display:none" color="<? echo $bgcolor; ?>">
										<? echo change_date_format($row[csf('pub_shipment_date')]); ?>
									</font>
								</td>
								<td width="100"><p><? echo $emblishment_name_array[$selectResult[('embellishment_id')]]; ?></p></td>
                                <td width="100"><p><? echo $embell_type; ?></p></td>
                                <td width="80" align="center" bgcolor="<? echo $td_color; ?>"><? echo change_date_format($selectResult[('target_approval_date')]); ?></td>
                                <td width="80" align="center"><? echo change_date_format($selectResult[('sent_to_supplier')]); ?></td>
                                <td width="80" align="center"><? echo change_date_format($selectResult[('submitted_to_buyer')]); ?></td>
                                <td width="80" align="center"><? echo $approval_status[$selectResult[('approval_status')]]; ?></td>
                                <td width="80" align="center"><? echo change_date_format($selectResult[('approval_status_date')]); ?></td>
                                <td width="100"><p><? echo $supplier_library[$selectResult[('supplier_name')]]; ?></td>
                                <td><p><? echo $selectResult[('embellishment_comments')]; ?></p></td>
							</tr>
						<?
						}
					$k++;
					$s++;	
					}
					
					if(count($reference_arr[$row[csf('id')]])<1)
					{
					?>
							<td width="100"></td>
                            <td width="100"></td>
                            <td width="80"></td>
                            <td width="80"></td>
                            <td width="80"></td>
                            <td width="80"></td>
                            <td width="80"></td>
                            <td width="100"></td>
                            <td></td>
						</tr>
					<?
					$s++;
					}
				$i++;
				}
				
				?>
				</table>
				<table class="rpt_table" width="2160" cellpadding="0" cellspacing="0" border="1" rules="all">
					<tfoot>
						<th width="30"></th>
						<th width="100"></th>
                        <th width="130"></th>
                        <th width="170"></th>
                        <th width="60"></th>
						<th width="50"></th>
						<th width="130"></th>
						<th width="200"></th>
                        <th width="80"></th>
                        <th width="80"></th>
						<th width="80" align="right" id="total_order_qnty"><? echo number_format($total_order_qnty,0); ?></th>
						<th width="50"></th>
						<th width="80" align="right" id="total_order_qnty_in_pcs"><? echo number_format($total_order_qnty_in_pcs,0); ?></th>
						<th width="80"></th>
						<th width="100"></th>
                        <th width="100"></th>
                        <th width="80"></th>
                        <th width="80"></th>
                        <th width="80"></th>
                        <th width="80"></th>
                        <th width="80"></th>
                        <th width="100"></th>
                        <th></th>
					</tfoot>
				</table>
				</div>
			</fieldset>
		</div>
	<?
	}
	foreach (glob("*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data****$filename****$tot_rows";
	exit();	
}
disconnect($con);
?>