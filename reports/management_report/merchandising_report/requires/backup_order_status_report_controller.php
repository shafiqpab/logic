<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );



if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0); 
	exit();
}



if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$txt_order_no=str_replace("'","",$txt_order_no);

	//echo $cbo_company_name."______".$txt_order_no;
?>

<div style="width:1345px" id="scroll_body">
    <div align="left" style="margin-left:5px;">
        <table width="1150" cellpadding="0" cellspacing="0" id="caption" align="center">
            <tr>
            	<td align="center" width="100%" colspan="12" class="form_caption" ><strong style="font-size:18px">Company Name:<? echo " ".$company_library[$cbo_company_name]; ?></strong></td>
            </tr>
            <tr>  
            	<td align="center" width="100%" colspan="12" class="form_caption" ><strong style="font-size:18px"><? echo $report_title; ?></strong></td>
            </tr> 
        </table>
        <br />
    
        <br />&nbsp;<br />
        <table width="900" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="">
            <thead>
                <tr>
                    <th width="120" >Order No</th>
                    <th width="110" >Buyer</th>					
                    <th width="120">Job No</th>
                    <th width="110" >Order Qty.</th>
                    <th width="110">Ship Date</th>
                    <th width="110">Ex-Fact. Qty.</th>
                    <th width="80" >Ship Out %</th>
                    <th >Short / Exces</th>
                </tr>
            </thead>
            <tbody>
            <?
			$sql="SELECT
			b.id as order_id, b.job_no_mst, b.po_number, b.po_quantity as order_qty, a.company_name, a.buyer_name, b.pub_shipment_date 
			from 
					wo_po_details_master a, wo_po_break_down b
			where 
					a.job_no=b.job_no_mst and a.company_name=$cbo_company_name and b.po_number like '$txt_order_no' group by b.id";
			
			//echo $sql;die;
			$order_id=0;$k=1;
			$sql_re=sql_select($sql);
			foreach($sql_re as $row)
			{
				if($k==1) $order_id_grp=$row[csf("order_id")]; else $order_id_grp=$order_id_grp.",".$row[csf("order_id")];
				$order_id=$row[csf("order_id")];
				?>
                <tr>
                    <td><? echo $row[csf("po_number")];?></td>
                    <td><? echo $buyer_arr[$row[csf("buyer_name")]];?></td>
                    <td><? echo $row[csf("job_no_mst")];?></td>
                    <td align="right"><? $oder_qty=$row[csf("order_qty")]; echo $oder_qty; ?></td>
                    <td align="center"><? if($row[csf("pub_shipment_date")]!=0000-00-00) echo $row[csf("pub_shipment_date")]; else echo '00-00-0000';?></td>
                    <td align="right"><? echo $ex_fact_qty=return_field_value("sum(ex_factory_qnty) as ex_factory_qnty","pro_ex_factory_mst","po_break_down_id=$order_id and status_active=1 and is_deleted=0","ex_factory_qnty");?></td>
                    <td align="center"><? echo ($ex_fact_qty/$row[csf("order_qty")])*100;?></td>
                    <td align="right"><? echo $ex_fact_qty-$row[csf("order_qty")];?></td>
                </tr>
            	<?
				$k++;
			}
			
			/*$sql="SELECT id, cons as finish_cons, (requirment/pcs) as grey_cons, pcs as uom_pcs from wo_pre_cos_fab_co_avg_con_dtls where po_break_down_id in($order_id_grp) group by id";
			echo $sql;//$order_id_grp*/
			if($order_id_grp =="")
			{
			echo '<div align="left" style="width:1000px;"><h1 align="center" style="color:#f00;">Order not found</h></div>';die;
			}
			else
			{
				
				$sql="select 
				sum((b.requirment/b.pcs)*order_quantity) as requirment, sum((b.cons/b.pcs)*order_quantity) as cons 
				from 
						wo_po_color_size_breakdown a, wo_pre_cos_fab_co_avg_con_dtls b 
				where 
						a.po_break_down_id=b.po_break_down_id and a.color_number_id=b.color_number_id and a.size_number_id=b.gmts_sizes and a.is_deleted=0 and b.po_break_down_id in($order_id_grp) and a.status_active=1 and b.requirment!=0 and b.cons!=0";
				//echo $sql;die;
				$sql_re=sql_select($sql);
				foreach($sql_re as $row)
				{
					$total_grey_qty=$row[csf("requirment")];
					$total_finish_qty=$row[csf("cons")];
				}
				//echo ($total_grey_qty)."__".$total_finish_qty; die;
				?>
				</tbody>
			</table>
		</div>
		<br />
        
		<div align="left" style="width:1335px;" id="">
        	<div style="float:left; width:360;" align="left">
                <table width="360" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="" align="left">
                    <thead>
                        <tr>
                            <th width="50" rowspan="2">particul.</th>
                            <th  colspan="5">Yarn Consumption</th>
                        </tr>
                        <tr>
                            <th width="60">Required</th>
                            <th width="60">Issued Inside</th>
                            <th width="60">Issued SubCon</th>
                            <th width="60">Total Issued</th>
                            <th width="60">Excess Booking</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
						$sql="select 
						sum(c.quantity) as quantity, sum(case when a.knit_dye_source=1 then c.quantity else 0 end) as inside, sum(case when a.knit_dye_source!=1 then c.quantity else 0 end) as subcon  
						from
						inv_issue_master a, inv_transaction b, order_wise_pro_details c
						where 
						a.id=b.mst_id and b.id=c.trans_id and a.issue_purpose !=2 and c.trans_type=2 and c.trans_id !=0 and c.entry_form=3 and c.po_breakdown_id in($order_id_grp)";
						
						//echo $sql;
						
						$sql_result=sql_select($sql);$yern_inside="";$yern_subcon="";
						foreach($sql_result as $row)
						{
							$yern_inside =$row[csf("inside")];
							$yern_subcon =$row[csf("subcon")];
						}
						
						$issue_return_sql="select 
						sum(c.quantity) as quantity, sum(case when a.knit_dye_source=1 then c.quantity else 0 end) as inside, sum(case when a.knit_dye_source!=1 then c.quantity else 0 end) as subcon  
						from
						inv_issue_master a, inv_transaction b, order_wise_pro_details c
						where 
						a.id=b.mst_id and b.id=c.trans_id and c.trans_type=4 and c.trans_id !=0 and c.entry_form=3 and c.po_breakdown_id in($order_id_grp)";
						
						$sql_result=sql_select($issue_return_sql);
						foreach($sql_result as $row)
						{
							$yern_inside =$yern_inside-$row[csf("inside")];
							$yern_subcon =$yern_subcon-$row[csf("subcon")];
						}
						
					?>
                        <tr>
                            <td>qty</td>
                            <td align="right"><? echo number_format($total_grey_qty,2,".",""); ?></td>
                            <td align="right"><? echo  number_format($yern_inside,2,".",""); ?></td>
                            <td align="right"><? echo number_format($yern_subcon,2,".",""); ?></td>
                            <td align="right"><? $total_yern_issue=$yern_inside+$yern_subcon; echo number_format($total_yern_issue,2,".",""); ?></td>
                            <td align="right"><? $excess_bookin=$total_grey_qty-$total_yern_issue; echo number_format($excess_bookin,2,".",""); ?></td>
                        </tr>
                        <tr>
                            <td>In %</td>
                            <td></td>
                            <td align="right"><? $grey_issu_percent_inside=($yern_inside/$total_grey_qty)*100; echo number_format($grey_issu_percent_inside,2,".","")."%";  ?></td>
                            <td align="right"><? $grey_issu_percent_subcon=($yern_subcon/$total_grey_qty)*100; echo number_format($grey_issu_percent_subcon,2,".","")."%";  ?></td>
                            <td align="right"><? $grey_issu_percent_total=($total_yern_issue/$total_grey_qty)*100; echo number_format($grey_issu_percent_total,2,".","")."%";  ?></td>
                            <td align="right"><? $grey_issu_percent_access=($excess_bookin/$total_grey_qty)*100; echo number_format($grey_issu_percent_access,2,".","")."%";  ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div style="float:left; width:10px;" >
            	<table width="10" border="0">
                <tr><td>&nbsp;</td></tr>
                </table>
            </div>
            
            <div style="float:left; width:470px;" >
            	<table width="470" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="">
                    <thead>
                        <tr>
                            <th  colspan="8">Knitting Production</th>
                        </tr>
                        <tr>
                            <th width="60">Gray Fab Req.</th>
                            <th width="60">Inside Prod</th>
                            <th width="60">SubCon Prod.</th>
                            <th width="60">Total Prod.</th>
                            <th width="55">Process Loss</th>
                            <th width="60">Issued To Dyeing</th>
                            <th width="55">Left Over</th>
                            <th width="60">For Short Booking</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
						$sql_gry_pro="select 
						sum(c.quantity) as quantity, sum(case when a.knitting_source=1 then c.quantity else 0 end) as inside, sum(case when a.knitting_source!=1 then c.quantity else 0 end) as subcon  
						from
						inv_receive_master a, inv_transaction b, order_wise_pro_details c
						where 
						a.id=b.mst_id and b.id=c.trans_id and c.trans_id !=0 and c.trans_type=1 and c.entry_form=2 and c.po_breakdown_id in($order_id_grp)";
						
						//echo $sql_gry_pro;
						$sql_result=sql_select($sql_gry_pro);$grey_inside="";$grey_subcon="";
						foreach($sql_result as $row)
						{
							$grey_inside =$row[csf("inside")];
							$grey_subcon =$row[csf("subcon")];
						}
						
						$sql_gry_receive="select 
						sum(c.quantity) as quantity, sum(case when a.knitting_source=1 then c.quantity else 0 end) as inside, sum(case when a.knitting_source!=1 then c.quantity else 0 end) as subcon  
						from
						inv_receive_master a, inv_transaction b, order_wise_pro_details c
						where 
						a.id=b.mst_id and b.id=c.trans_id and c.trans_id !=0 and c.trans_type=1 and c.entry_form=22 and c.po_breakdown_id in($order_id_grp)";
						
						//echo $sql_gry_receive;
						$sql_result=sql_select($sql_gry_receive);
						foreach($sql_result as $row)
						{
							if($grey_inside=="") $grey_inside =$row[csf("inside")]; else $grey_inside +=$row[csf("inside")];
							if($grey_subcon=="") $grey_subcon =$row[csf("subcon")]; else $grey_subcon +=$row[csf("subcon")];
						}
						
						$gry_order_to_order_rece=return_field_value("sum(c.quantity) as quantity","inv_item_transfer_mst a, inv_transaction b, order_wise_pro_details c","a.id=b.mst_id and b.id=c.trans_id and c.trans_id !=0 and c.trans_type=5 and c.entry_form=13 and c.po_breakdown_id in($order_id_grp)","quantity");
						
						if($gry_order_to_order_rece!="") $grey_inside=$grey_inside+$gry_order_to_order_rece;
						
						$gry_order_to_order_issue=return_field_value("sum(c.quantity) as quantity","inv_item_transfer_mst a, inv_transaction b, order_wise_pro_details c","a.id=b.mst_id and b.id=c.trans_id and c.trans_id !=0 and c.trans_type=6 and c.entry_form=13 and c.po_breakdown_id in($order_id_grp)","quantity");
						
						if($gry_order_to_order_issue!="") $grey_inside=$grey_inside-$gry_order_to_order_issue;
					?>
                        <tr>
                            <td align="right"><? echo number_format($total_grey_qty,2,".",""); ?></td>
                            <td align="right"><? echo number_format($grey_inside,2,".",""); ?></td>
                            <td align="right"><? echo number_format($grey_subcon,2,".",""); ?></td>
                            <td align="right"><? $total_gray_pro=$grey_inside+$grey_subcon; echo number_format($total_gray_pro,2,".",""); ?></td>
                            <td align="right"><? $total_process_loss=$total_yern_issue-$total_gray_pro; echo number_format($total_process_loss,2,".",""); ?></td>
                            <td align="right"><? echo number_format($gry_issue_to_duying,2,".",""); ?></td>
                            <td align="right"><? $late_over=$total_gray_pro-$gry_issue_to_duying; echo number_format($late_over,2,".",""); ?></td>
                            <td align="right"><? $total_short_booking=$total_grey_qty-$total_gray_pro; echo number_format($total_short_booking,2,".",""); ?></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td align="right"><? $grey_pro_percent_inside=($grey_inside/$total_grey_qty)*100; echo number_format($grey_pro_percent_inside,2,".","")."%";  ?></td>
                            <td align="right"><? $grey_pro_percent_subcon=($grey_subcon/$total_grey_qty)*100; echo number_format($grey_pro_percent_subcon,2,".","")."%";  ?></td>
                            <td align="right"><? $grey_pro_percent_total=($total_gray_pro/$total_grey_qty)*100; echo number_format($grey_pro_percent_total,2,".","")."%";  ?></td>
                            <td align="right"><? $grey_pro_percent_loss=($total_process_loss/$total_yern_issue)*100; echo number_format($grey_pro_percent_loss,2,".","")."%";  ?></td>
                            <td align="right"><? $grey_pro_percent_dying=($gry_issue_to_duying/$total_gray_pro)*100; echo number_format($grey_pro_percent_dying,2,".","")."%";  ?></td>
                            <td align="right"><? $grey_percent_late_over=($late_over/$total_gray_pro)*100; echo number_format($grey_percent_late_over,2,".","")."%";  ?></td>
                            <td align="right"><? $grey_percent_short_book=($total_short_booking/$total_grey_qty)*100; echo number_format($grey_percent_short_book,2,".","")."%";  ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div style="float:left; width:10px;">
            	<table width="10" border="0">
                <tr><td>&nbsp;</td></tr>
                </table>
            </div>
            
            <div style="float:left; width:470px;" >
            	<table width="475" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="">
                    <thead>
                        <tr>
                            <th  colspan="8">Dyeing and Finish Fabric Production</th>
                        </tr>
                        <tr>
                            <th width="60">Fin. Fab Req.</th>
                            <th width="60">Inside Prod</th>
                            <th width="60">SubCon Prod.</th>				
                            <th width="60">Total Prod.</th>
                            <th width="60">Process Loss</th>
                            <th width="60">For Short Booking</th>
                            <th width="60">Issued To Cutting</th>
                            <th width="55">Left Over</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
						$sql_finish_receive="select 
						sum(c.quantity) as quantity, sum(case when a.knitting_source=1 then c.quantity else 0 end) as inside, sum(case when a.knitting_source!=1 then c.quantity else 0 end) as subcon  
						from
						inv_receive_master a, inv_transaction b, order_wise_pro_details c
						where 
						a.id=b.mst_id and b.id=c.trans_id and c.trans_id !=0 and c.trans_type=1 and c.entry_form=7 and c.po_breakdown_id in($order_id_grp)";
						
						//echo $sql_finish_receive;
						$sql_result=sql_select($sql_finish_receive);$finish_inside="";$finish_subcon="";
						foreach($sql_result as $row)
						{
							if($finish_inside=="") $finish_inside =$row[csf("inside")]; else $finish_inside +=$row[csf("inside")];
							if($finish_subcon=="") $finish_subcon =$row[csf("subcon")]; else $finish_subcon +=$row[csf("subcon")];
						}
						
						
						//echo "select sum(c.quantity) as quantity from inv_issue_master a, inv_transaction b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.trans_id and c.trans_type=2 and c.entry_form=16 and c.po_breakdown_id in($order_id_grp) and a.issue_purpose in(4,11)";
						
						
						$gry_issue_to_duying=return_field_value("sum(c.quantity) as quantity","inv_issue_master a, inv_transaction b, order_wise_pro_details c","a.id=b.mst_id and b.id=c.trans_id and c.trans_id !=0 and c.trans_type=2 and c.entry_form=16 and c.po_breakdown_id in($order_id_grp) and a.issue_purpose in(4,11)","quantity");
						
						//echo "select sum(c.quantity) as quantity from inv_issue_master a, inv_transaction b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.trans_id and c.trans_type=2 and c.entry_form=18 and c.po_breakdown_id in($order_id_grp) and a.issue_purpose in(4,9)";
						
						$finish_issue_to_cutting=return_field_value("sum(c.quantity) as quantity","inv_issue_master a, inv_transaction b, order_wise_pro_details c","a.id=b.mst_id and b.id=c.trans_id and c.trans_id !=0 and c.trans_type=2 and c.entry_form=18 and c.po_breakdown_id in($order_id_grp) and a.issue_purpose in(4,9)","quantity");
					?>
                        <tr>
                            <td align="right"><? echo number_format($total_finish_qty,2,".",""); ?></td>
                            <td align="right"><? echo number_format($finish_inside,2,".",""); ?></td>
                            <td align="right"><? echo number_format($finish_subcon,2,".",""); ?></td>
                            <td align="right"><? $total_finish_pro=$finish_inside+$finish_subcon; echo number_format($total_finish_pro,2,".",""); ?></td>
                            <td align="right"><? $finish_process_loss=$gry_issue_to_duying-$total_finish_pro; echo number_format($finish_process_loss,2,".",""); ?></td>
                            <td align="right"><? $total_finish_short_booking=$total_finish_qty-$total_finish_pro; echo number_format($total_finish_short_booking,2,".",""); ?></td>
                            <td align="right"><? echo number_format($finish_issue_to_cutting,2,".",""); ?></td>
                            <td align="right"><? $finish_late_over=$total_finish_pro-$finish_issue_to_cutting; echo number_format($finish_late_over,2,".",""); ?></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td align="right"><? $finish_percent_inside=($finish_inside/$total_finish_qty)*100; echo number_format($finish_percent_inside,2,".","")."%";  ?></td>
                            <td  align="right"><? $finish_percent_inside=($finish_subcon/$total_finish_qty)*100; echo number_format($finish_percent_inside,2,".","")."%";  ?></td>
                            <td align="right"><? $finish_percent_total=($total_finish_pro/$total_finish_qty)*100; echo number_format($finish_percent_total,2,".","")."%";  ?></td>
                            <td align="right"><? $finish_percent_prc_loss=($finish_process_loss/$gry_issue_to_duying)*100; echo number_format($finish_percent_prc_loss,2,".","")."%";  ?></td>
                            <td align="right"><? $finish_percent_shor_book=($total_finish_short_booking/$total_finish_qty)*100; echo number_format($finish_percent_shor_book,2,".","")."%";  ?></td>
                            <td align="right"><? $finish_percent_issue_cut=($finish_issue_to_cutting/$total_finish_pro)*100; echo number_format($finish_percent_issue_cut,2,".","")."%";  ?></td>
                            <td align="right"><? $finish_percent_late_ovr=($finish_late_over/$total_finish_pro)*100; echo number_format($finish_percent_late_ovr,2,".","")."%";  ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>   
			<?
			}
		?>
    </div>
</div>

<?	
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


if ($action=="file_job_popup")
{
  	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $company_id;die;  
?>
<script>
	function js_set_value(str)
	{
		$("#hide_job_no").val(str);
		parent.emailwindow.hide(); 
	}
</script>
<?
	/*$sql = "select a.id, a.po_number from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name=$company_id order by a.id"; 
	//echo $sql;die;
	echo create_list_view("list_view", "Order No","230","280","300",0, $sql , "js_set_value", "po_number", "", 1, "0", $arr, "po_number", "","setFilterGrid('list_view',-1)") ;*/	
	if($buyer_id!=0) $buyer_con="and a.buyer_name=$buyer_id"; else $buyer_con="";
	$arr=array (2=>$company_library,3=>$buyer_arr);
	$sql= "select a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,SUBSTRING_INDEX(a.`insert_date`, '-', 1) as year from wo_po_details_master a where a.company_name=$company_id $buyer_con  group by a.id order by  a.job_no";
	//echo $sql;die;  
	echo  create_list_view("list_view1", "Job No,Year,Company,Buyer Name,Style Ref. No", "90,100,120,100,100","570","320",0, $sql , "js_set_value", "job_no", "", 1, "0,0,company_name,buyer_name,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no", "","setFilterGrid('list_view1',-1)",'0,0,0,0,0');
	echo "<input type='hidden' id='hide_job_no' />";
	exit();
}



if ($action=="file_popup")
{
  	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$job_no=str_replace("'","",$job_no);
	 
?>
<script>
	function js_set_value(str)
	{
		$("#hide_order_no").val(str);
		parent.emailwindow.hide(); 
	}
</script>
<?
	/*$sql = "select a.id, a.po_number from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name=$company_id order by a.id"; 
	//echo $sql;die;
	echo create_list_view("list_view", "Order No","230","280","300",0, $sql , "js_set_value", "po_number", "", 1, "0", $arr, "po_number", "","setFilterGrid('list_view',-1)") ;*/	
	if($buyer_id!=0) $buyer_con="and a.buyer_name=$buyer_id"; else $buyer_con="";
	if($job_no!="") $job_con="and a.job_no='$job_no'"; else $job_con="";
	$arr=array (3=>$company_library,4=>$buyer_arr,5=>$item_category);
	$sql= "select a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity, b.po_number,b.po_quantity,b.shipment_date,a.garments_nature,SUBSTRING_INDEX(a.`insert_date`, '-', 1) as year from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company_id $buyer_con $job_con group by b.id order by  a.job_no_prefix_num";
	//echo $sql;die;  
	echo  create_list_view("list_view", "Job No,Year,PO number,Company,Buyer Name,Style Ref. No,Job Qty.,PO Quantity,Shipment Date", "90,100,90,120,100,100,90,90,80","1000","320",0, $sql , "js_set_value", "po_number", "", 1, "0,0,0,company_name,buyer_name,0,0,0,0", $arr , "job_no_prefix_num,year,po_number,company_name,buyer_name,style_ref_no,job_quantity,po_quantity,shipment_date", "","setFilterGrid('list_view',-1)",'0,0,0,0,0,0,1,1,3');
	echo "<input type='hidden' id='hide_order_no' />";
	exit();
}
disconnect($con);
?>
