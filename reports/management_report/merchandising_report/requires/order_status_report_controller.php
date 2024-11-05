<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

/*require_once('../../../../includes/common.php');
require_once('../../../../includes/class.reports.php');
require_once('../../../../includes/class.yarns.php');
require_once('../../../../includes/class.fabrics.php');*/

require_once('../../../../includes/common.php');
include('../../../../includes/class4/class.conditions.php');
include('../../../../includes/class4/class.reports.php');
include('../../../../includes/class4/class.fabrics.php');
include('../../../../includes/class4/class.yarns.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name  $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0); 
	exit();
}

if($action=="job_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	
	<script>
	function js_set_value(str)
	{
		$("#hide_job_no").val(str);
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
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company_id $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref",3=>"Order No");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company_id; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year; ?>', 'job_popup_search_list_view', 'search_div', 'order_status_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}

if ($action=="job_popup_search_list_view")
{
  	//echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	list($company_id,$buyer_id,$search_type,$search_value,$cbo_year)=explode('**',$data);

	if($search_type==1 && $search_value!=''){
		$search_con=" and a.job_no like('%$search_value')";	
	}
	else if($search_type==2 && $search_value!=''){
		$search_con=" and a.style_ref_no like('%$search_value%')";	
	}
	else if($search_type==3 && $search_value!=''){
		$search_con=" and b.po_number like('%$search_value%')";	
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
			$year_field="YEAR(a.insert_date)";
		}
		else
		{
			$year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";	
			$year_field="to_char(a.insert_date,'YYYY')";
		}
	}
	else $year_cond="";

	$arr=array (2=>$company_library,3=>$buyer_arr);
	$sql= "select a.job_no_prefix_num, a.job_no, a.company_name,a.buyer_name,a.style_ref_no,$year_field as year from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and  a.company_name=$company_id  $buyer_cond $year_cond $search_con order by a.id DESC";
	//echo $sql;
	echo  create_list_view("list_view", "Job No,Year,Company,Buyer Name,Style Ref. No", "70,70,120,100,100","570","230",0, $sql , "js_set_value", "year,job_no", "", 1, "0,0,company_name,buyer_name,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no", "","setFilterGrid('list_view',-1)",'0,0,0,0,0');
	echo "<input type='hidden' id='hide_job_no' />";
	
	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
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
	
	$cbo_year=str_replace("'","",$cbo_year);
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
	
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_file_no=str_replace("'","",$txt_file_no);
	$txt_ref_no=str_replace("'","",$txt_ref_no);
	if(trim($txt_job_no)!="") $job_no_cond="%".trim($txt_job_no); else $job_no_cond="%%";//."%"
	if(trim($txt_file_no)!="") $file_no_cond="and b.file_no='$txt_file_no'"; else $file_no_cond="";
	if(trim($txt_ref_no)!="") $ref_no_cond="and b.grouping='$txt_ref_no'"; else $ref_no_cond="";
	
	ob_start();

?>
	<fieldset style="width:1200px; margin-left:5px">	
        <table width="1200">
            <tr>
                <td align="center" width="100%" colspan="12" class="form_caption"><? echo $company_library[str_replace("'","",$cbo_company_name)]; ?></td>
            </tr>
        </table>
        <?
		
		$exFactoryArr=return_library_array("select sum(ex_factory_qnty) as qnty,po_break_down_id from pro_ex_factory_mst where status_active=1 and is_deleted=0 group by po_break_down_id","po_break_down_id","qnty");
		
		$i=1; $all_po_id=""; $job_no=''; $tot_po_qnty=0; $tot_exfactory_qnty=0; $ratio=0;
		 $sql="select a.buyer_name, a.job_no, a.total_set_qnty as ratio, a.style_ref_no, a.gmts_item_id, b.id as po_id, b.po_number, b.po_quantity as po_qnty, b.plan_cut, b.pub_shipment_date,b.file_no,b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$cbo_company_name and a.job_no like '$job_no_cond' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $buyer_id_cond $year_cond $file_no_cond $ref_no_cond order by b.pub_shipment_date, b.id";	
	
         $result=sql_select($sql);
		
		$txt_job_no=$result[0][csf('job_no')];

		if(empty($result))
		{
			echo '<div align="left" style="width:1200px;"><h1 align="center" style="color:#f00;">Order not found</h></div>'; die;
		}
		?>
        <div style="width:100%"> 
            <table width="1200" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <th width="30">SL</th>
                    <th width="100">Job No</th>
                    <th width="110">Order No</th>
                    <th width="100">Ref. No</th>
                    <th width="100">File No</th>
                   
                    <th width="100">Buyer</th>
                    <th width="100">Style</th>
                    <th width="100">Gmts. Item</th>					
                    <th width="100">Order Qty. (Pcs)</th>
                    <th width="80">Shipment Date</th>
                    <th width="100">Ex-Fact. Qty.</th>
                    <th width="70">Ship Out %</th>
                    <th>Short / Exces</th>
                </thead>
                <?
                    foreach($result as $row)
                    {
                        if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        
                        $po_qnty=$row[csf('po_qnty')]*$row[csf('ratio')];
						$plun_cut_qnty=$row[csf('plan_cut')]*$row[csf('ratio')];
                        $exfactory_qnty=$exFactoryArr[$row[csf('po_id')]];
                        $short_excess=$po_qnty-$exfactory_qnty;
                        $ship_perc=($exfactory_qnty*100)/$po_qnty;
                        
                        $tot_po_qnty+=$po_qnty; 
						$tot_plun_cut_qnty+=$plun_cut_qnty; 
                        $tot_exfactory_qnty+=$exfactory_qnty; 
                        
                        if($all_po_id=="") $all_po_id=$row[csf('po_id')]; else $all_po_id.=",".$row[csf('po_id')];
						
						$job_no=$row[csf('job_no')];
						$ratio=$row[csf('ratio')];
						$gmts_item_name="";
						$item_id_ex=explode(',',$row[csf('gmts_item_id')]);
						foreach($item_id_ex as $item_id)
						{
							if($gmts_item_name=="") $gmts_item_name=$garments_item[$item_id]; else $gmts_item_name.=',<br>'.$garments_item[$item_id];
						}
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $row[csf('job_no')]; ?></div></td>
                            <td width="110"><div style="word-wrap:break-word; width:110px"><? echo $row[csf('po_number')]; ?></div></td>
                            <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $row[csf('grouping')]; ?></div></td>
                            <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $row[csf('file_no')]; ?></div></td>
                       
                            <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></div></td>
                            <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $row[csf('style_ref_no')]; ?></div></td>
                            <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $gmts_item_name; ?></div></td>
                            <td width="100" align="right"><? echo $po_qnty; ?>&nbsp;</td>
                            <td width="80" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
                            <td width="100" align="right"><? echo $exfactory_qnty; ?>&nbsp;</td>
                            <td width="70" align="right"><? echo number_format($ship_perc,2); ?>&nbsp;</td>
                            <td align="right"><? echo $short_excess; ?>&nbsp;</td>
                        </tr>
                    <?
                        $i++;
                    }
					unset($result);
                ?>
                <tfoot>
                    <th align="right" colspan="8">Total</th>					
                    <th><? echo $tot_po_qnty; ?>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th><? echo $tot_exfactory_qnty; ?>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th><? echo $tot_short_excess=$tot_po_qnty-$tot_exfactory_qnty; ?>&nbsp;</th>
                </tfoot>
            </table>
        </div>
        <br />
        <?
		
        $costing_per_qnty=0;
        $costing_per_id=return_field_value( "costing_per", "wo_pre_cost_mst","job_no ='".$txt_job_no."'");
        if($costing_per_id==1)
               {
                   $costing_per="1 Dzn";
                   $costing_per_qnty=12;
   
               }
               if($costing_per_id==2)
               {
                   $costing_per="1 Pcs";
                   $costing_per_qnty=1;
   
               }
               if($costing_per_id==3)
               {
                   $costing_per="2 Dzn";
                   $costing_per_qnty=24;
   
               }
               if($costing_per_id==4)
               {
                   $costing_per="3 Dzn";
                   $costing_per_qnty=36;
   
               }
               if($costing_per_id==5)
               {
                   $costing_per="4 Dzn";
                   $costing_per_qnty=48;
   
               }

			$embroDataArr=sql_select("select  
						sum(CASE WHEN emb_name=1 THEN cons_dzn_gmts ELSE 0 END) AS print_cons,
						sum(CASE WHEN emb_name=2 THEN cons_dzn_gmts ELSE 0 END) AS embro_cons,
						sum(CASE WHEN emb_name=3 THEN cons_dzn_gmts ELSE 0 END) AS wash_cons
						from wo_pre_cost_embe_cost_dtls where job_no='$job_no' and is_deleted=0 and status_active=1");
			$print_cons=$embroDataArr[0][csf('print_cons')];				
			$embro_cons=$embroDataArr[0][csf('embro_cons')];				
			$wash_cons=$embroDataArr[0][csf('wash_cons')];
			unset($embroDataArr);

			$gmtsProdDataArr=sql_select("select  
						sum(CASE WHEN production_type=1 THEN production_quantity ELSE 0 END) AS cutting_qnty,
						sum(CASE WHEN production_type=2 and embel_name=1 and production_source=1 THEN production_quantity ELSE 0 END) AS print_issue_qnty_in,
						sum(CASE WHEN production_type=2 and embel_name=1 and production_source=3 THEN production_quantity ELSE 0 END) AS print_issue_qnty_out,
						sum(CASE WHEN production_type=3 and embel_name=1 and production_source=1 THEN production_quantity ELSE 0 END) AS print_recv_qnty_in,
						sum(CASE WHEN production_type=3 and embel_name=1 and production_source=3 THEN production_quantity ELSE 0 END) AS print_recv_qnty_out,
						sum(CASE WHEN production_type=2 and embel_name=2 and production_source=1 THEN production_quantity ELSE 0 END) AS emb_issue_qnty_in,
						sum(CASE WHEN production_type=2 and embel_name=2 and production_source=3 THEN production_quantity ELSE 0 END) AS emb_issue_qnty_out,
						sum(CASE WHEN production_type=3 and embel_name=2 and production_source=1 THEN production_quantity ELSE 0 END) AS emb_recv_qnty_in,
						sum(CASE WHEN production_type=3 and embel_name=2 and production_source=3 THEN production_quantity ELSE 0 END) AS emb_recv_qnty_out,
						sum(CASE WHEN production_type=4 and production_source=1 THEN production_quantity ELSE 0 END) AS sew_input_qnty_in,
						sum(CASE WHEN production_type=4 and production_source=3 THEN production_quantity ELSE 0 END) AS sew_input_qnty_out,
						sum(CASE WHEN production_type=5 and production_source=1 THEN production_quantity ELSE 0 END) AS sew_recv_qnty_in,
						sum(CASE WHEN production_type=5 and production_source=3 THEN production_quantity ELSE 0 END) AS sew_recv_qnty_out,
						sum(CASE WHEN production_type=8 and production_source=1 THEN production_quantity ELSE 0 END) AS finish_qnty_in,
						sum(CASE WHEN production_type=8 and production_source=3 THEN production_quantity ELSE 0 END) AS finish_qnty_out,
						sum(CASE WHEN production_type=3 and embel_name=3 and production_source=1 THEN production_quantity ELSE 0 END) AS wash_recv_qnty_in,
						sum(CASE WHEN production_type=3 and embel_name=3 and production_source=3 THEN production_quantity ELSE 0 END) AS wash_recv_qnty_out,
						sum(CASE WHEN production_type=3 and embel_name=1 THEN reject_qnty ELSE 0 END) AS print_reject_qnty,
						sum(CASE WHEN production_type=3 and embel_name=2 THEN reject_qnty ELSE 0 END) AS emb_reject_qnty,
						sum(CASE WHEN production_type=5 THEN reject_qnty ELSE 0 END) AS sew_reject_qnty,
						sum(CASE WHEN production_type=8 THEN reject_qnty ELSE 0 END) AS finish_reject_qnty
						from pro_garments_production_mst where po_break_down_id in($all_po_id) and is_deleted=0 and status_active=1");
							
			$cutting_qnty=$gmtsProdDataArr[0][csf('cutting_qnty')];

			/*$fabricDataArray=sql_select("select a.job_no, a.fab_nature_id, a.fabric_source, a.rate, b.yarn_cons_qnty, b.yarn_amount from wo_pre_cost_fabric_cost_dtls a, wo_pre_cost_sum_dtls b where a.job_no=b.job_no and a.fabric_source!=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
			foreach($fabricDataArray as $fabricRow)
			{
				$fabric_data_Array[$fabricRow[csf('job_no')]].=$fabricRow[csf('fab_nature_id')]."**".$fabricRow[csf('fabric_source')]."**".$fabricRow[csf('rate')]."**".$fabricRow[csf('yarn_cons_qnty')]."**".$fabricRow[csf('yarn_amount')].",";
			}*/
			
			//$fab_rate_data=sql_select("select  a.rate from wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b where b.pre_cost_fabric_cost_dtls_id=a.id and b.job_no=a.job_no and  a.fabric_source=2 and a.status_active=1 and a.is_deleted=0 and  b.po_break_down_id in ($all_po_id)");
			//$fab_rate=$fab_rate_data[0][csf('rate')];
			$sql_wo=sql_select("select sum(b.grey_fab_qnty) as grey_req_qnty, sum(b.fin_fab_qnty) as fin_fab_qnty  from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.po_break_down_id in ($all_po_id)");
			
			$fab_reqDataArray=sql_select("select sum( CASE WHEN c.fabric_source=1 and c.fab_nature_id=2 THEN ((b.requirment/b.pcs)*a.plan_cut_qnty) ELSE 0 END) as grey_req, 
 sum( CASE WHEN fabric_source=1  and c.fab_nature_id=2 THEN ((b.cons/b.pcs)*a.plan_cut_qnty) ELSE 0 END) as finish_req, 
 sum( CASE WHEN fabric_source=2  and c.fab_nature_id=2 THEN ((b.cons/b.pcs)*a.plan_cut_qnty) ELSE 0 END) as purchase_finish_req,
  sum( CASE WHEN fabric_source in(1,2,3)  and c.fab_nature_id=3 THEN ((b.cons/b.pcs)*a.plan_cut_qnty) ELSE 0 END) as woven_finish_req 
  from wo_po_color_size_breakdown a, wo_pre_cos_fab_co_avg_con_dtls b ,wo_pre_cost_fabric_cost_dtls c where b.pre_cost_fabric_cost_dtls_id=c.id and b.job_id=c.job_id   and a.job_id=c.job_id and   a.po_break_down_id=b.po_break_down_id and a.color_number_id=b.color_number_id and a.size_number_id=b.gmts_sizes and a.is_deleted=0 and a.status_active=1 and a.po_break_down_id in ($all_po_id)");
  
			  /*$yarn= new yarn($row[csf('job_no')],'job');
			  $yarn_req_qty_arr=$yarn->getJobWiseYarnQtyArray();
			  $yarn->unsetDataArray();
			  $fabric= new fabric($row[csf('job_no')],'job');
			  $finish_fabric_req_qty_arr=$fabric->getQtyArray_by_job_knitAndwoven_greyAndfinish_production();
			  $fabric->unsetDataArray();*/
			 $condition= new condition();
			 $condition->company_name("=$cbo_company_name");
			 if(str_replace("'","",$cbo_buyer_name)>0){
				  $condition->buyer_name("=$cbo_buyer_name");
			 }
			 if(str_replace("'","",$txt_job_no) !=''){
				  $condition->job_no("='$txt_job_no'");
			 }
			 if(str_replace("'","",$txt_file_no)!='')
			 {
				$condition->file_no("='$txt_file_no'"); 
			 }
			 if(str_replace("'","",$txt_ref_no)!='')
			 {
				$condition->grouping("=$txt_ref_no"); 
			 }
			 if(str_replace("'","",$txt_order_no)!='')
			 {
				$condition->po_number("=$txt_order_no"); 
			 }
			
			$condition->init();
			$yarn= new yarn($condition);
			$yarn_req_qty_arr=$yarn->getJobWiseYarnQtyArray();
			
			$fabric= new fabric($condition);
			$finish_fabric_req_qty_arr=$fabric->getQtyArray_by_job_knitAndwoven_greyAndfinish_production();
			
			
			 // print_r($finish_fabric_req_qty_arr);
			  //$fabric->unsetDataArray();
			$fab_purchase_req_qnty=$fab_reqDataArray[0][csf('purchase_finish_req')];
			$grey_fabric_req_qnty=$sql_wo[0][csf('grey_req_qnty')];
			$yarn_req_qty=$yarn_req_qty_arr[$txt_job_no];
			$finish_fabric_req_qnty= $sql_wo[0][csf('fin_fab_qnty')];//$fab_reqDataArray[0][csf('finish_req')];
			$woven_finish_fabric_req_qnty=$fab_reqDataArray[0][csf('woven_finish_req')];
			unset($sql_wo);
			unset($fab_reqDataArray);
			
			//$yarn->unsetDataArray();
			
			/*$reqDataArray=sql_select("select sum((b.requirment/b.pcs)*a.plan_cut_qnty) as grey_req, sum((b.cons/b.pcs)*a.plan_cut_qnty) as finish_req from wo_po_color_size_breakdown a, wo_pre_cos_fab_co_avg_con_dtls b,wo_pre_cost_fabric_cost_dtls c where b.pre_cost_fabric_cost_dtls_id=c.id and b.job_no=c.job_no and  c.fabric_source!=2 and a.job_no_mst=c.job_no and a.po_break_down_id=b.po_break_down_id and a.color_number_id=b.color_number_id and a.size_number_id=b.gmts_sizes and a.is_deleted=0 and a.status_active=1 and a.po_break_down_id in ($all_po_id)");
			$grey_fabric_req_qnty=$reqDataArray[0][csf('grey_req')];
			$finish_fabric_req_qnty=$reqDataArray[0][csf('finish_req')];*/
			
			
			$yarnDataArr=sql_select("select  
						sum(CASE WHEN a.entry_form=3 and c.entry_form=3 and c.knit_dye_source!=3 THEN a.quantity ELSE 0 END) AS issue_qnty_in,
						sum(CASE WHEN a.entry_form=3 and c.entry_form=3 and c.knit_dye_source=3 THEN a.quantity ELSE 0 END) AS issue_qnty_out
						from order_wise_pro_details a, inv_transaction b, inv_issue_master c where a.trans_id=b.id and b.mst_id=c.id and a.po_breakdown_id in($all_po_id) and b.item_category=1 and c.issue_purpose!=2 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1");
						
			$yarnReturnDataArr=sql_select("select  
						sum(CASE WHEN a.entry_form=9 and c.entry_form=9 and c.knitting_source!=3 THEN a.quantity ELSE 0 END) AS return_qnty_in,
						sum(CASE WHEN a.entry_form=9 and c.entry_form=9 and c.knitting_source=3 THEN a.quantity ELSE 0 END) AS return_qnty_out
						from order_wise_pro_details a, inv_transaction b, inv_receive_master c where a.trans_id=b.id and b.mst_id=c.id and a.po_breakdown_id in($all_po_id) and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1");
			
			$yarn_qnty_in=$yarnDataArr[0][csf('issue_qnty_in')]-$yarnReturnDataArr[0][csf('return_qnty_in')];
			$yarn_qnty_out=$yarnDataArr[0][csf('issue_qnty_out')]-$yarnReturnDataArr[0][csf('return_qnty_out')];
			unset($yarnDataArr); unset($yarnReturnDataArr);
			
			$dataArrayTrans=sql_select("select
					sum(CASE WHEN entry_form ='11' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_yarn,
					sum(CASE WHEN entry_form ='11' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_yarn,
					sum(CASE WHEN entry_form in (83,13) and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_knit,
					sum(CASE WHEN entry_form in (83,82,13) and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_knit,
					sum(CASE WHEN entry_form ='15' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_finish,
					sum(CASE WHEN entry_form ='15' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_finish
					from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form in(11,83,82,13,15) and po_breakdown_id in($all_po_id)");
			$transfer_in_qnty_yarn=$dataArrayTrans[0][csf('transfer_in_qnty_yarn')];
			$transfer_out_qnty_yarn=$dataArrayTrans[0][csf('transfer_out_qnty_yarn')];	
			$transfer_in_qnty_knit=$dataArrayTrans[0][csf('transfer_in_qnty_knit')];
			$transfer_out_qnty_knit=$dataArrayTrans[0][csf('transfer_out_qnty_knit')];
			$transfer_in_qnty_finish=$dataArrayTrans[0][csf('transfer_in_qnty_finish')];
			$transfer_out_qnty_finish=$dataArrayTrans[0][csf('transfer_out_qnty_finish')];
			
			unset($dataArrayTrans);
			
			$total_issued=$yarn_qnty_in+$yarn_qnty_out+$transfer_in_qnty_yarn-$transfer_out_qnty_yarn;
			$under_over_issued=$grey_fabric_req_qnty-$total_issued;
			
			$greyYarnIssueQnty=return_field_value("sum(a.cons_quantity) as issue_qnty","inv_transaction a, inv_issue_master b","a.mst_id=b.id and b.entry_form=3 and b.issue_basis=1 and b.issue_purpose=2 and a.job_no='$job_no' and a.status_active=1 and a.is_deleted=0 and a.item_category=1 and a.transaction_type=2","issue_qnty");
			
			$dyedYarnRecvQnty=return_field_value("sum(a.cons_quantity) as recv_qnty","inv_transaction a, inv_receive_master b","a.mst_id=b.id and b.entry_form=1 and b.receive_basis=2 and b.receive_purpose=2 and a.job_no='$job_no' and a.status_active=1 and a.is_deleted=0 and a.item_category=1 and a.transaction_type=1","recv_qnty");
			
			$dyed_yarn_balance=$greyYarnIssueQnty-$dyedYarnRecvQnty;
			
			$yarn_qnty_in_perc=($yarn_qnty_in/$yarn_req_qty)*100;
			$yarn_qnty_out_perc=($yarn_qnty_out/$yarn_req_qty)*100;
			$transfer_in_qnty_yarn_perc=($transfer_in_qnty_yarn/$yarn_req_qty)*100;
			$transfer_out_qnty_yarn_perc=($transfer_out_qnty_yarn/$yarn_req_qty)*100;
			$total_issued_perc=($total_issued/$yarn_req_qty)*100;
			$under_over_issued_perc=($under_over_issued/$yarn_req_qty)*100;
			$greyYarnIssueQnty_perc=($greyYarnIssueQnty/$yarn_req_qty)*100;
			$dyedYarnRecvQnty_perc=($dyedYarnRecvQnty/$yarn_req_qty)*100;
			$dyed_yarn_balance_perc=($dyed_yarn_balance/$yarn_req_qty)*100;
			
			$prodDataArr=sql_select("select  
						sum(CASE WHEN c.knitting_source!=3 THEN a.quantity ELSE 0 END) AS knit_qnty_in,
						sum(CASE WHEN c.knitting_source=3 THEN a.quantity ELSE 0 END) AS knit_qnty_out
						from order_wise_pro_details a, pro_grey_prod_entry_dtls b, inv_receive_master c where a.dtls_id=b.id and b.mst_id=c.id and a.po_breakdown_id in($all_po_id) and c.item_category=13 and a.entry_form in(2,22) and c.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and c.receive_basis<>9");
						
			 $knit_qnty_in=$prodDataArr[0][csf('knit_qnty_in')];
			 $knit_qnty_out=$prodDataArr[0][csf('knit_qnty_out')];
			 
			 unset($prodDataArr);
			
			$prodFinDataArr=sql_select("select  
						sum(CASE WHEN c.knitting_source!=3 THEN a.quantity ELSE 0 END) AS finish_qnty_in,
						sum(CASE WHEN c.knitting_source=3 THEN a.quantity ELSE 0 END) AS finish_qnty_out
						from order_wise_pro_details a, pro_finish_fabric_rcv_dtls b, inv_receive_master c where a.dtls_id=b.id and b.mst_id=c.id and a.po_breakdown_id in($all_po_id) and c.item_category=2 and a.entry_form in(7,37,66) and c.entry_form in(7,37,66) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and c.receive_basis<>9");
			$finish_qnty_in=$prodFinDataArr[0][csf('finish_qnty_in')];
			$finish_qnty_out=$prodFinDataArr[0][csf('finish_qnty_out')];
			
			unset($prodFinDataArr);
			$issue_return_qty="";
           $issue_roll_return_data=sql_select("select a.barcode_no, a.qc_pass_qnty, a.po_breakdown_id from pro_roll_details a,inv_finish_fabric_issue_dtls b, inv_receive_master c 
           where a.dtls_id=b.id  and a.entry_form=71 and a.status_active=1 and a.is_deleted=0 and a.roll_used=1
             and a.po_breakdown_id in ($all_po_id) and b.mst_id=c.issue_id and c.entry_form=126 and c.company_id=1 group by  a.barcode_no, a.qc_pass_qnty, a.po_breakdown_id");
             foreach($issue_roll_return_data as $val){
                $issue_return_qty+=$val[csf('qc_pass_qnty')];
             }
               
             $issue_return_data=sql_select("select b.company_id,b.id as prod_id, a.id as tr_id, a.store_id, a.issue_id, a.cons_quantity,c.id dtls_id,c.body_part_id,c.order_id
             from inv_transaction a,pro_finish_fabric_rcv_dtls c, product_details_master b 
            where  a.status_active=1 and a.item_category=2 and a.transaction_type=4 and c.order_id in ($all_po_id) and a.id=c.trans_id and a.prod_id=b.id and b.status_active=1 and c.status_active=1");

            foreach($issue_return_data as $val){
                $issue_return_qty+=$val[csf('cons_quantity')];
             }



			$batchDataArr=sql_select("select sum(b.batch_qnty) as batch_qty from pro_batch_create_mst a,pro_batch_create_dtls b where a.id=b.mst_id  and a.batch_against not in(2) and a.entry_form!=36 and b.status_active=1 and b.is_deleted=0 and b.po_id in($all_po_id)");
			$batch_qty=$batchDataArr[0][csf('batch_qty')];
			unset($batchDataArr);
			
			$issueData=sql_select("select 
				sum(CASE WHEN entry_form=16 THEN quantity ELSE 0 END) AS grey_issue_qnty,
				sum(CASE WHEN entry_form=61 THEN quantity ELSE 0 END) AS grey_issue_qnty_roll_wise,
				sum(CASE WHEN entry_form=18 THEN quantity ELSE 0 END) AS issue_to_cut_qnty,
				sum(CASE WHEN entry_form=71 THEN quantity ELSE 0 END) AS issue_to_cut_qnty_roll_wise
				from order_wise_pro_details where po_breakdown_id in($all_po_id) and status_active=1 and is_deleted=0");
			
			$issuedToDyeQnty=$issueData[0][csf('grey_issue_qnty')]+$issueData[0][csf('grey_issue_qnty_roll_wise')];	
			$issuedToCutQnty=$issueData[0][csf('issue_to_cut_qnty')]+$issueData[0][csf('issue_to_cut_qnty_roll_wise')]-$issue_return_qty;
			unset($issueData);	
			
			$sql_consumtiont_qty=sql_select("select c.body_part_id, avg(b.cons) as requirment, avg(b.pcs) pcs
                              from  wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls c
                              where b.pre_cost_fabric_cost_dtls_id=c.id and b.pcs>0 and b.po_break_down_id in ($all_po_id) group by c.body_part_id ");			
			$finish_consumtion=0;
			foreach($sql_consumtiont_qty as $row_consum)
			{
				$con_avg=0;
				$con_avg= $row_consum[csf('requirment')]/$row_consum[csf('pcs')];///str_replace("'","",$row_sew[csf("pcs")]);
				$finish_consumtion+=$con_avg;
			}
			unset($sql_consumtiont_qty);

            $sql_fabric_data=sql_select("select sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty   FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d 
            WHERE a.job_id=b.job_id and a.id=b.pre_cost_fabric_cost_dtls_id and c.job_id=a.job_id and c.id=b.color_size_table_id and b.po_break_down_id=d.po_break_down_id    and b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id   and d.status_active=1  and d.po_break_down_id in ($all_po_id)  and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0  ");
            $fabricQnty="";
            foreach($sql_fabric_data as $row)
                {
                    $fabricQnty+= $row[csf('fin_fab_qnty')];

                }
                unset($sql_fabric_data);
				
			
			$total_knitting=$knit_qnty_in+$knit_qnty_out;
			$grey_avilable=$total_knitting+$transfer_in_qnty_knit-$transfer_out_qnty_knit;
			$process_loss=$total_issued-$total_knitting;//$knit_qnty_in-$knit_qnty_out
			$under_over_prod=$grey_fabric_req_qnty-$total_knitting;
			$left_over=$total_knitting-$issuedToDyeQnty;
			
			$knit_qnty_in_perc=($knit_qnty_in/$grey_fabric_req_qnty)*100;
			$knit_qnty_out_perc=($knit_qnty_out/$grey_fabric_req_qnty)*100;
			$transfer_in_qnty_knit_perc=($transfer_in_qnty_knit/$grey_fabric_req_qnty)*100;
			$transfer_out_qnty_knit_perc=($transfer_out_qnty_knit/$grey_fabric_req_qnty)*100;
			$total_knitting_perc=($total_knitting/$grey_fabric_req_qnty)*100;
			$grey_avilable_perc=($grey_avilable/$grey_fabric_req_qnty)*100;
			$process_loss_perc=($process_loss/$total_issued)*100;
			$under_over_prod_perc=($under_over_prod/$grey_fabric_req_qnty)*100;
			$issuedToDyeQnty_perc=($issuedToDyeQnty/$grey_fabric_req_qnty)*100;
			$left_over_perc=($left_over/$total_knitting)*100;
			
			$total_finishing=$finish_qnty_in+$finish_qnty_out;
			$finish_available=$total_finishing+$transfer_in_qnty_finish-$transfer_out_qnty_finish;
			$process_loss_finishing=$issuedToDyeQnty-$total_finishing;
			$under_over_finish_prod=$finish_fabric_req_qnty-$total_finishing;
			$finish_left_over=$total_finishing-$issuedToCutQnty;
			
			$batch_qty_perc=(($issuedToDyeQnty-$batch_qty)/$issuedToDyeQnty)*100;
			$finish_qnty_in_perc=($finish_qnty_in/$finish_fabric_req_qnty)*100;
			$finish_qnty_out_perc=($finish_qnty_out/$finish_fabric_req_qnty)*100;
			$transfer_in_qnty_finish_perc=($transfer_in_qnty_finish/$finish_fabric_req_qnty)*100;
			$transfer_out_qnty_finish_perc=($transfer_out_qnty_finish/$finish_fabric_req_qnty)*100;
			$total_finishing_perc=($total_finishing/$finish_fabric_req_qnty)*100;
			$finish_available_perc=($finish_available/$finish_fabric_req_qnty)*100;
			$process_loss_finishing_perc=($process_loss_finishing/$issuedToDyeQnty)*100;
			$under_over_finish_prod_perc=($under_over_finish_prod/$finish_fabric_req_qnty)*100;
			$issuedToCutQnty_perc=($issuedToCutQnty/$finish_fabric_req_qnty)*100;
			$finish_left_over_perc=($finish_left_over/$total_finishing)*100;
			
			$fab_recv_perc=($issuedToCutQnty/$finish_fabric_req_qnty)*100;
            $fab_cons=($fabricQnty/$plun_cut_qnty)*($ratio*$costing_per_qnty);
			
			$actual_perc=($cutting_qnty/$tot_plun_cut_qnty)*100;
			//$cons_per=($finish_consumtion/$tot_plun_cut_qnty);
			// $possible_cut_pcs=$issuedToCutQnty/$finish_consumtion;
            $possible_cut_pcs=$issuedToCutQnty/$fab_cons;
			$cutting_process_loss=$possible_cut_pcs-$cutting_qnty;
			$cutting_process_loss_perc=($cutting_process_loss/$cutting_qnty)*100;
			
			$purchase_finish_qnty_in_perc=($finish_qnty_in/$fab_purchase_req_qnty)*100;
			
			// $woven_recv=("select sum(CASE WHEN c.entry_form in (17) and a.item_category=3  THEN c.quantity END) AS finish_receive_qnty from inv_receive_master a,product_details_master b, order_wise_pro_details c where a.entry_form=c.entry_form and  and c.trans_id=a.id and b.id=c.prod_id and c.entry_form in (17) and c.trans_id!=0 and a.item_category=3 and a.entry_form in(17) and c.po_breakdown_id in($all_po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
			$woven_recv= sql_select("select sum(CASE WHEN c.entry_form in (17) and a.item_category=3  THEN c.quantity END) AS woven_receive_qnty from inv_transaction a,product_details_master b, order_wise_pro_details c where a.id=c.trans_id  and b.id=c.prod_id and c.entry_form in (17) and c.trans_id!=0 and a.item_category=3 and c.po_breakdown_id in($all_po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ");
			$wov_qnty_recv=$woven_recv[0][csf('woven_receive_qnty')];
			unset($woven_recv);
			//echo "select sum(b.quantity) as issue_qnty from inv_issue_master a,inv_transaction c, order_wise_pro_details b where a.id=c.mst_id and c.prod_id=b.prod_id  and b.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and b.entry_form=19 and b.po_breakdown_id in($all_po_id)"; die;
			$sql_issue=sql_select("select sum(b.quantity) as issue_qnty from inv_transaction c, order_wise_pro_details b where c.id=b.trans_id and b.status_active=1 and b.is_deleted=0 and b.entry_form=19 and b.po_breakdown_id in($all_po_id)");
			$wov_qnty_issue=$sql_issue[0][csf('issue_qnty')];
			unset($sql_issue);
			$woven_left_over=$wov_qnty_recv-$wov_qnty_issue;
			$tot_woven_available_qty=$wov_qnty_recv; //-($transfer_in_qnty_finish+$transfer_out_qnty_finish);
			
			$woven_issuedToCutQnty_perc=($wov_qnty_issue/$woven_finish_fabric_req_qnty)*100;
			$total_woven_qty=$finish_qnty_in+$finish_qnty_out+$transfer_in_qnty_finish-$transfer_out_qnty_finish;
			
			$woven_left_over_perc=($woven_left_over/$wov_qnty_recv)*100;
			$woven_recv_qnty_perc=($wov_qnty_recv/$woven_finish_fabric_req_qnty)*100;
		?>
        <table width="1000" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
			<thead>
                <tr>
                    <th width="90" rowspan="2">Particulars</th>
                    <th colspan="7" width="630">Yarn Status</th>
                    <th colspan="3" width="270">Dyed Yarn Status</th>
                </tr>
                <tr>
                    <th width="90">Required <br/><font style="font-size:9px; font-weight:100">(As Per Pre-Cost)</font></th>
                    <th width="90">Issued Inside</th>
                    <th width="90">Issued SubCon</th>
                    <th width="90">Transfer In</th>
                    <th width="90">Transfer Out</th>
                    <th width="90">Total Issued</th>
                    <th width="90">Under or Over Issued</th>
                    <th width="90">Grey Yarn Issued</th>
                    <th width="90">Dyed Yarn Received</th>
                    <th>Balance</th>
                </tr>
			</thead>
            <tr bgcolor="#FFFFFF">
            	<td>Quantity</td>
                <td align="right"><? echo number_format($yarn_req_qty,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($yarn_qnty_in,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($yarn_qnty_out,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($transfer_in_qnty_yarn,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($transfer_out_qnty_yarn,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($total_issued,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($under_over_issued,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($greyYarnIssueQnty,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($dyedYarnRecvQnty,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($dyed_yarn_balance,0,'.',''); ?>&nbsp;</td>
            </tr>
            <tr bgcolor="#E9F3FF">
            	<td>In %</td>
                <td>&nbsp;</td>
                <td align="right"><? echo number_format($yarn_qnty_in_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($yarn_qnty_out_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($transfer_in_qnty_yarn_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($transfer_out_qnty_yarn_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($total_issued_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($under_over_issued_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($greyYarnIssueQnty_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($dyedYarnRecvQnty_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($dyed_yarn_balance_perc,0,'.',''); ?>&nbsp;</td>
            </tr>
    	</table> 
        <br />
        <table width="1090" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
			<thead>
                <tr>
                    <th width="90" rowspan="2">Particulars</th>
                    <th colspan="11" width="1000">Knitting Production</th>
                </tr>
                <tr>
                    <th width="90">Gray Fab Req.<br/><font style="font-size:9px; font-weight:100">(As Per Booking)</font></th>
                    <th width="90">Inside Prod.</th>
                    <th width="90">SubCon Prod.</th>
                    <th width="90">Transfer In</th>
                    <th width="90">Transfer Out</th>
                    <th width="90">Total Prod.</th>
                    <th width="90">Grey Available</th>
                    <th width="90">Process Loss</th>
                    <th width="90">Under or Over Prod.</th>
                    <th width="90">Issued To Dyeing</th>
                    <th>Left Over</th>
                </tr>
			</thead>
            <tr bgcolor="#FFFFFF">
            	<td>Quantity</td>
                <td align="right"><? echo number_format($grey_fabric_req_qnty,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($knit_qnty_in,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($knit_qnty_out,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($transfer_in_qnty_knit,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($transfer_out_qnty_knit,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($total_knitting,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($grey_avilable,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($process_loss,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($under_over_prod,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($issuedToDyeQnty,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($left_over,0,'.',''); ?>&nbsp;</td>
            </tr>
            <tr bgcolor="#E9F3FF">
            	<td>In %</td>
                <td>&nbsp;</td>
                <td align="right"><? echo number_format($knit_qnty_in_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($knit_qnty_out_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($transfer_in_qnty_knit_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($transfer_out_qnty_knit_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($total_knitting_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($grey_avilable_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($process_loss_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($under_over_prod_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($issuedToDyeQnty_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($left_over_perc,0,'.',''); ?>&nbsp;</td>
            </tr>
    	</table>  
        <br />
        <table width="1250" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
			<thead>
                <tr>
                    <th width="90" rowspan="2">Particulars</th>
                    <th colspan="13" width="1170">Dyeing and Finish Fabric Production</th>
                </tr>
                <tr>
                    <th width="90">Finish Fab Req.<br/><font style="font-size:9px; font-weight:100">(As Per Booking)</font></th>
                    <th width="90">Batch Qty.</th>
                    <th width="90">Inside Prod.</th>
                    <th width="90">SubCon Prod.</th>
                    <th width="90">Transfer In</th>
                    <th width="90">Transfer Out</th>
                    <th width="90">Total Prod.</th>
                    <th width="90">Finish Available</th>
                    <th width="90">Process Loss</th>
                    <th width="90">Under or Over Prod.</th>
                    <th width="90">Purchase Qty.</th>
                    <th width="90">Issued To Cutting</th>
                    <th>Left Over</th>
                </tr>
			</thead>
            <tr bgcolor="#FFFFFF">
            	<td>Quantity</td>
                <td align="right"><? echo number_format($finish_fabric_req_qnty,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($batch_qty,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($finish_qnty_in,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($finish_qnty_out,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($transfer_in_qnty_finish,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($transfer_out_qnty_finish,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($total_finishing,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($finish_available,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($process_loss_finishing,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($under_over_finish_prod,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($fab_purchase_req_qnty,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($issuedToCutQnty,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($finish_left_over,0,'.',''); ?>&nbsp;</td>
            </tr>
            <tr bgcolor="#E9F3FF">
            	<td>In %</td>
                <td>&nbsp;</td>
                <td align="right"><? echo number_format($batch_qty_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($finish_qnty_in_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($finish_qnty_out_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($transfer_in_qnty_finish_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($transfer_out_qnty_finish_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($total_finishing_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($finish_available_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($process_loss_finishing_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($under_over_finish_prod_perc,0,'.',''); ?>&nbsp;</td>
                 <td align="right"><? echo number_format($purchase_finish_qnty_in_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($issuedToCutQnty_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($finish_left_over_perc,0,'.',''); ?>&nbsp;</td>
            </tr>
    	</table>
         <br />
        <table width="800" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
			<thead>
                <tr>
                    <th width="90" rowspan="2">Particulars</th>
                    <th colspan="8" width="910">Woven Fabric</th>
                </tr>
                <tr>
                    <th width="90">Woven Fab Req.</th>
                    <th width="90">Receive/Prod.</th>
                    <th width="90">Transfer In.</th>
                    <th width="90">Transfer Out</th>
                    <th width="90">Total Available Qty.</th>
                    <th width="90">Issued To Cutting</th>
                   
                    <th>Left Over</th>
                </tr>
			</thead>
            <tr bgcolor="#FFFFFF">
            	<td>Quantity</td>
                <td align="right"><? echo number_format($woven_finish_fabric_req_qnty,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($wov_qnty_recv,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? //echo number_format($transfer_in_qnty_finish,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? //echo number_format($transfer_out_qnty_finish,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($tot_woven_available_qty,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($wov_qnty_issue,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($woven_left_over,0,'.',''); ?>&nbsp;</td>
                
            </tr>
            <tr bgcolor="#E9F3FF">
            	<td>In %</td>
                <td>&nbsp;</td>
                <td align="right"><? echo number_format($woven_recv_qnty_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? //echo number_format($woven_recv_qnty_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? //echo number_format($woven_recv_qnty_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? //echo number_format($transfer_out_qnty_finish_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($woven_issuedToCutQnty_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><?  echo number_format($woven_left_over_perc,0,'.',''); ?>&nbsp;</td>
                
            </tr>
    	</table>
        <br />
        <table width="730" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
			<thead>
                <tr>
                    <th width="90" rowspan="2">Particulars</th>
                    <th colspan="7" width="640">Cutting Production</th>
                </tr>
                <tr>
                    <th width="90">Fabric Req.</th>
                    <th width="90">Fabric Recv.</th>
                    <th width="90">Finish Consumption</th>
                    <th width="90">Possible Cut (Pcs)</th>
                    <th width="90">Gmts. Req.</th>
                    <th width="90">Actual Cut (Pcs)</th>
                    <th>Process Loss</th>
                </tr>
			</thead>
            <tr bgcolor="#FFFFFF">
            	<td>Quantity</td>
                <td align="right"><? echo number_format($finish_fabric_req_qnty,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($issuedToCutQnty,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($fab_cons/12,5,'.','');  // $finish_consumtion; ?>&nbsp;</td>
                <td align="right"><? echo number_format($possible_cut_pcs); ?>&nbsp;</td>
                <td align="right"><? echo number_format($tot_plun_cut_qnty,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($cutting_qnty,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($cutting_process_loss,0,'.',''); ?>&nbsp;</td>
            </tr>
            <tr bgcolor="#E9F3FF">
            	<td>In %</td>
                <td>&nbsp;</td>
                <td align="right"><? echo number_format($fab_recv_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? //echo number_format($fab_recv_perc,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($possible_cut_qnty_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right">&nbsp;</td>
                <td align="right"><? echo number_format($actual_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($cutting_process_loss_perc,0,'.',''); ?>&nbsp;</td>
            </tr>
    	</table> 
        <?
		if($print_cons>0)
		{
			$print_issue_qnty_in=$gmtsProdDataArr[0][csf('print_issue_qnty_in')];
			$print_issue_qnty_out=$gmtsProdDataArr[0][csf('print_issue_qnty_out')];
			$print_recv_qnty_in=$gmtsProdDataArr[0][csf('print_recv_qnty_in')];
			$print_recv_qnty_out=$gmtsProdDataArr[0][csf('print_recv_qnty_out')];
			$print_reject_qnty=$gmtsProdDataArr[0][csf('print_reject_qnty')];
			
			$total_print_issued=$print_issue_qnty_in+$print_issue_qnty_out;
			$total_print_recv=$print_recv_qnty_in+$print_recv_qnty_out;
			
			$print_issue_qnty_in_perc=($print_issue_qnty_in/$tot_po_qnty)*100;
			$print_issue_qnty_out_perc=($print_issue_qnty_out/$tot_po_qnty)*100;
			$print_recv_qnty_in_perc=($print_recv_qnty_in/$tot_po_qnty)*100;
			$print_recv_qnty_out_perc=($print_recv_qnty_out/$tot_po_qnty)*100;
			
			$total_print_issued_perc=($total_print_issued/$tot_po_qnty)*100;
			$total_print_recv_perc=($total_print_recv/$tot_po_qnty)*100;
			
			$print_reject_qnty_perc=($print_reject_qnty/$total_print_recv)*100;
		?>
        	<br />
            <table width="820" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <tr>
                        <th width="90" rowspan="2">Particulars</th>
                        <th colspan="8" width="730">Garments Printing</th>
                    </tr>
                    <tr>
                        <th width="90">Gmts. Req.</th>
                        <th width="90">Issued Inside</th>
                        <th width="90">Issued SubCon</th>
                        <th width="90">Total Issued</th>
                        <th width="90">Print Inside</th>
                        <th width="90">Print SubCon</th>
                        <th width="90">Total Print</th>
                        <th>Reject</th>
                    </tr>
                </thead>
                <tr bgcolor="#FFFFFF">
                    <td>Quantity</td>
                    <td align="right"><? echo number_format($tot_po_qnty,0,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($print_issue_qnty_in,0,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($print_issue_qnty_out,0,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($total_print_issued,0,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($print_recv_qnty_in,0,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($print_recv_qnty_out,0,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($total_print_recv,0,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($print_reject_qnty,0,'.',''); ?>&nbsp;</td>
                </tr>
                <tr bgcolor="#E9F3FF">
                    <td>In %</td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format($print_issue_qnty_in_perc,0,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($print_issue_qnty_out_perc,0,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($total_print_issued_perc,0,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($print_recv_qnty_in_perc,0,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($print_recv_qnty_out_perc,0,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($total_print_recv_perc,0,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($print_reject_qnty_perc,0,'.',''); ?>&nbsp;</td>
                 </tr>
            </table>
        <?
		}
		
		if($embro_cons>0)
		{
			$emb_issue_qnty_in=$gmtsProdDataArr[0][csf('emb_issue_qnty_in')];
			$emb_issue_qnty_out=$gmtsProdDataArr[0][csf('emb_issue_qnty_out')];
			$emb_recv_qnty_in=$gmtsProdDataArr[0][csf('emb_recv_qnty_in')];
			$emb_recv_qnty_out=$gmtsProdDataArr[0][csf('emb_recv_qnty_out')];
			$emb_reject_qnty=$gmtsProdDataArr[0][csf('emb_reject_qnty')];
			
			$total_emb_issued=$emb_issue_qnty_in+$emb_issue_qnty_out;
			$total_emb_recv=$emb_recv_qnty_in+$emb_recv_qnty_out;
			
			$emb_issue_qnty_in_perc=($emb_issue_qnty_in/$tot_po_qnty)*100;
			$emb_issue_qnty_out_perc=($emb_issue_qnty_out/$tot_po_qnty)*100;
			$emb_recv_qnty_in_perc=($emb_recv_qnty_in/$tot_po_qnty)*100;
			$emb_recv_qnty_out_perc=($emb_recv_qnty_out/$tot_po_qnty)*100;
			
			$total_emb_issued_perc=($total_emb_issued/$tot_po_qnty)*100;
			$total_emb_recv_perc=($total_emb_recv/$tot_po_qnty)*100;
			
			$emb_reject_qnty_perc=($emb_reject_qnty/$total_print_recv)*100;
		?>
        	<br />
            <table width="820" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <tr>
                        <th width="90" rowspan="2">Particulars</th>
                        <th colspan="8" width="730">Embroidery</th>
                    </tr>
                    <tr>
                        <th width="90">Gmts. Req.</th>
                        <th width="90">Issued Inside</th>
                        <th width="90">Issued SubCon</th>
                        <th width="90">Total Issued</th>
                        <th width="90">Emb. Inside</th>
                        <th width="90">Emb. SubCon</th>
                        <th width="90">Total Emb.</th>
                        <th>Reject</th>
                    </tr>
                </thead>
                <tr bgcolor="#FFFFFF">
                    <td>Quantity</td>
                    <td align="right"><? echo number_format($tot_po_qnty,0,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($emb_issue_qnty_in,0,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($emb_issue_qnty_out,0,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($total_emb_issued,0,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($emb_recv_qnty_in,0,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($emb_recv_qnty_out,0,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($total_emb_recv,0,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($emb_reject_qnty,0,'.',''); ?>&nbsp;</td>
                </tr>
                <tr bgcolor="#E9F3FF">
                    <td>In %</td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format($emb_issue_qnty_in_perc,0,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($emb_issue_qnty_out_perc,0,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($total_emb_issued_perc,0,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($emb_recv_qnty_in_perc,0,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($emb_recv_qnty_out_perc,0,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($total_emb_recv_perc,0,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($emb_reject_qnty_perc,0,'.',''); ?>&nbsp;</td>
                 </tr>
            </table>
        <?
		}
		
		$sew_input_qnty_in=$gmtsProdDataArr[0][csf('sew_input_qnty_in')];
		$sew_input_qnty_out=$gmtsProdDataArr[0][csf('sew_input_qnty_out')];
		$total_sew_issued=$sew_input_qnty_in+$sew_input_qnty_out;
		
		$sew_input_qnty_in_perc=($sew_input_qnty_in/$tot_po_qnty)*100;
		$sew_input_qnty_out_perc=($sew_input_qnty_out/$tot_po_qnty)*100;
		$total_sew_issued_perc=($total_sew_issued/$tot_po_qnty)*100;
		
		$sew_balance_qnty=$tot_po_qnty-$total_sew_issued;
		$sew_balance_qnty_perc=($sew_balance_qnty/$tot_po_qnty)*100;
		?>    
        <br />
        <table width="550" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr>
                    <th width="90" rowspan="2">Particulars</th>
                    <th colspan="5" width="460">Input</th>
                </tr>
                <tr>
                    <th width="90">Gmts. Req.</th>
                    <th width="90">Input Inside</th>
                    <th width="90">Input SubCon</th>
                    <th width="90">Total Input</th>
                    <th>Balance</th>
                </tr>
            </thead>
            <tr bgcolor="#FFFFFF">
                <td>Quantity</td>
                <td align="right"><? echo number_format($tot_po_qnty,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($sew_input_qnty_in,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($sew_input_qnty_out,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($total_sew_issued,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($sew_balance_qnty,0,'.',''); ?>&nbsp;</td>
            </tr>
            <tr bgcolor="#E9F3FF">
                <td>In %</td>
                <td>&nbsp;</td>
                <td align="right"><? echo number_format($sew_input_qnty_in_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($sew_input_qnty_out_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($total_sew_issued_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($sew_balance_qnty_perc,0,'.',''); ?>&nbsp;</td>
             </tr>
        </table>   
        <br />
        <table width="720" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr>
                    <th width="90" rowspan="2">Particulars</th>
                    <th colspan="7" width="630">Accessories Status</th>
                </tr>
                <tr>
                    <th width="110">Item</th>
                    <th width="70">UOM</th>
                    <th width="90">Req. Qty.</th>
                    <th width="90">Received</th>
                    <th width="90">Recv. Balance</th>
                    <th width="90">Issued</th>
                    <th>Left Over</th>
                </tr>
            </thead>
            <?
			$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
			$trims_array=array();
			$trimsDataArr=sql_select("select b.item_group_id,  
									sum(CASE WHEN a.entry_form=24 THEN a.quantity ELSE 0 END) AS recv_qnty,
									sum(CASE WHEN a.entry_form=25 THEN a.quantity ELSE 0 END) AS issue_qnty
									from order_wise_pro_details a, product_details_master b where a.prod_id=b.id and a.po_breakdown_id in($all_po_id) and b.item_category_id=4 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 group by b.item_group_id");
			foreach($trimsDataArr as $row)	
			{
				$trims_array[$row[csf('item_group_id')]]['recv']=$row[csf('recv_qnty')];
				$trims_array[$row[csf('item_group_id')]]['iss']=$row[csf('issue_qnty')];
			}
			unset($trimsDataArr);

			$trimsDataArr=sql_select("select a.job_no, a.costing_per, b.trim_group, b.cons_uom, sum(b.cons_dzn_gmts) cons_dzn_gmts from wo_pre_cost_mst a, wo_pre_cost_trim_cost_dtls b where a.job_no=b.job_no and b.status_active=1 and b.is_deleted=0 and b.job_no='$job_no' group by b.trim_group, a.job_no, a.costing_per, b.cons_uom");
			$i=1; $tot_accss_req_qnty=0; $tot_recv_qnty=0; $tot_iss_qnty=0; $tot_recv_bl_qnty=0; $tot_trims_left_over_qnty=0;
			foreach($trimsDataArr as $row)
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$dzn_qnty='';
				if($row[csf('costing_per')]==1) $dzn_qnty=12;
				else if($row[csf('costing_per')]==3) $dzn_qnty=12*2;
				else if($row[csf('costing_per')]==4) $dzn_qnty=12*3;
				else if($row[csf('costing_per')]==5) $dzn_qnty=12*4;
				else $dzn_qnty=1;
				
				$dzn_qnty=$dzn_qnty*$ratio;
        		$accss_req_qnty=($row[csf('cons_dzn_gmts')]/$dzn_qnty)*$tot_po_qnty;
				
				$trims_recv=$trims_array[$row[csf('trim_group')]]['recv'];
				$trims_issue=$trims_array[$row[csf('trim_group')]]['iss'];
				$recv_bl=$accss_req_qnty-$trims_recv;
				$trims_left_over=$trims_recv-$trims_issue;
			?>
            	<tr bgcolor="<? echo $bgcolor; ?>">
                    <td>&nbsp;</td>
                    <td><p><? echo $item_library[$row[csf('trim_group')]]; ?>&nbsp;</p></td>
                    <td align="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?>&nbsp;</td>
                    <td align="right"><? echo number_format($accss_req_qnty,0,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($trims_recv,0,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($recv_bl,0,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($trims_issue,0,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($trims_left_over,0,'.',''); ?>&nbsp;</td>
                </tr>
            <?
				$tot_accss_req_qnty+=$accss_req_qnty;
				$tot_recv_qnty+=$trims_recv;
				$tot_recv_bl_qnty+=$recv_bl;
				$tot_iss_qnty+=$trims_issue;
				$tot_trims_left_over_qnty+=$trims_left_over;
				$i++;
			}
			unset($trimsDataArr);
			$tot_trims_left_over_qnty_perc=($tot_trims_left_over_qnty/$tot_recv_qnty)*100;
			?>
            <tfoot>
                <tr>
                    <th>Total</th>
                    <th align="right">&nbsp;</th>
                    <th align="right">&nbsp;</th>
                    <th align="right"><? echo number_format($tot_accss_req_qnty,0,'.',''); ?>&nbsp;</th>
                    <th align="right"><? echo number_format($tot_recv_qnty,0,'.',''); ?>&nbsp;</th>
                    <th align="right"><? echo number_format($tot_recv_bl_qnty,0,'.',''); ?>&nbsp;</th>
                    <th align="right"><? echo number_format($tot_iss_qnty,0,'.',''); ?>&nbsp;</th>
                    <th align="right"><? echo number_format($tot_trims_left_over_qnty,0,'.',''); ?>&nbsp;</th>
                </tr>
             </tfoot>
        </table>    
        <br />
        <?
			$sew_recv_qnty_in=$gmtsProdDataArr[0][csf('sew_recv_qnty_in')];
			$sew_recv_qnty_out=$gmtsProdDataArr[0][csf('sew_recv_qnty_out')];
			$total_sew_recv=$sew_recv_qnty_in+$sew_recv_qnty_out;
			
			$sew_recv_qnty_in_perc=($sew_recv_qnty_in/$tot_po_qnty)*100;
			$sew_recv_qnty_out_perc=($sew_recv_qnty_out/$tot_po_qnty)*100;
			$total_sew_recv_perc=($total_sew_recv/$tot_po_qnty)*100;
			
			$sew_balance_recv_qnty=$tot_po_qnty-$total_sew_recv;
			$sew_balance_recv_qnty_perc=($sew_balance_recv_qnty/$tot_po_qnty)*100;
			
			$sew_reject_qnty=$gmtsProdDataArr[0][csf('sew_reject_qnty')];
			$sew_reject_qnty_perc=($sew_reject_qnty/$total_print_recv)*100;
		?>
        <table width="720" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr>
                    <th width="90" rowspan="2">Particulars</th>
                    <th colspan="7" width="630">Sewing Production</th>
                </tr>
                <tr>
                    <th width="90">Gmts. Req.</th>
                    <th width="90">Input Revd</th>
                    <th width="90">Sew Inside</th>
                    <th width="90">Sew SubCon</th>
                    <th width="90">Total Sew</th>
                    <th width="90">Sew Balance</th>
                    <th>Reject</th>
                </tr>
            </thead>
            <tr bgcolor="#FFFFFF">
                <td>Quantity</td>
                <td align="right"><? echo number_format($tot_po_qnty,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($total_sew_issued,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($sew_recv_qnty_in,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($sew_recv_qnty_out,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($total_sew_recv,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($sew_balance_recv_qnty,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($sew_reject_qnty,0,'.',''); ?>&nbsp;</td>
            </tr>
            <tr bgcolor="#E9F3FF">
                <td>In %</td>
                <td>&nbsp;</td>
                <td align="right"><? echo number_format($total_sew_issued_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($sew_recv_qnty_in_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($sew_recv_qnty_out_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($total_sew_recv_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($sew_balance_recv_qnty_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($sew_reject_qnty_perc,0,'.',''); ?>&nbsp;</td>
             </tr>
        </table>  
        <?
		if($wash_cons>0)
		{
			$wash_recv_qnty_in=$gmtsProdDataArr[0][csf('wash_recv_qnty_in')];
			$wash_recv_qnty_out=$gmtsProdDataArr[0][csf('wash_recv_qnty_out')];
			$total_wash_recv=$wash_recv_qnty_in+$wash_recv_qnty_out;
			
			$wash_recv_qnty_in_perc=($wash_recv_qnty_in/$tot_po_qnty)*100;
			$wash_recv_qnty_out_perc=($wash_recv_qnty_out/$tot_po_qnty)*100;
			$total_wash_recv_perc=($total_wash_recv/$tot_po_qnty)*100;
			
			$wash_balance_qnty=$tot_po_qnty-$total_wash_recv;
			$wash_balance_qnty_perc=($wash_balance_qnty/$tot_po_qnty)*100;
		?>
        	<br />
            <table width="550" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <tr>
                        <th width="90" rowspan="2">Particulars</th>
                        <th colspan="5" width="460">Garment Washing</th>
                    </tr>
                    <tr>
                        <th width="90">Gmts. Req.</th>
                        <th width="90">Wash Inside</th>
                        <th width="90">Wash SubCon</th>
                        <th width="90">Total Wash</th>
                        <th>Balance</th>
                    </tr>
                </thead>
                <tr bgcolor="#FFFFFF">
                    <td>Quantity</td>
                    <td align="right"><? echo number_format($tot_po_qnty,0,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($wash_recv_qnty_in,0,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($wash_recv_qnty_out,0,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($total_wash_recv,0,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($wash_balance_qnty,0,'.',''); ?>&nbsp;</td>
                </tr>
                <tr bgcolor="#E9F3FF">
                    <td>In %</td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format($wash_recv_qnty_in_perc,0,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($wash_recv_qnty_out_perc,0,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($total_wash_recv_perc,0,'.',''); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($wash_balance_qnty_perc,0,'.',''); ?>&nbsp;</td>
                 </tr>
            </table>
        <?
		}
		
		$finish_qnty_in=$gmtsProdDataArr[0][csf('finish_qnty_in')];
		$finish_qnty_out=$gmtsProdDataArr[0][csf('finish_qnty_out')];
		$total_finish_qnty=$finish_qnty_in+$finish_qnty_out;
		
		$finish_qnty_in_perc=($finish_qnty_in/$tot_po_qnty)*100;
		$finish_qnty_out_perc=($finish_qnty_out/$tot_po_qnty)*100;
		$total_finish_qnty_perc=($total_finish_qnty/$tot_po_qnty)*100;
		
		$finish_balance_qnty=$tot_po_qnty-$total_finish_qnty;
		$finish_balance_qnty_perc=($finish_balance_qnty/$tot_po_qnty)*100;
		
		$finish_reject_qnty=$gmtsProdDataArr[0][csf('finish_reject_qnty')];
		$finish_reject_qnty_perc=($finish_reject_qnty/$total_print_recv)*100;
		unset($gmtsProdDataArr);
		?>
        <br />
        <table width="720" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr>
                    <th width="90" rowspan="2">Particulars</th>
                    <th colspan="7" width="630">Garment Finishing</th>
                </tr>
                <tr>
                    <th width="90">Gmts. Req.</th>
                    <th width="90">Received</th>
                    <th width="90">Finish Inside</th>
                    <th width="90">Finish SubCon</th>
                    <th width="90">Total Finish</th>
                    <th width="90">Finish Balance</th>
                    <th>Reject</th>
                </tr>
            </thead>
            <tr bgcolor="#FFFFFF">
                <td>Quantity</td>
                <td align="right"><? echo number_format($tot_po_qnty,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($total_sew_recv,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($finish_qnty_in,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($finish_qnty_out,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($total_finish_qnty,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($finish_balance_qnty,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($finish_reject_qnty,0,'.',''); ?>&nbsp;</td>
            </tr>
            <tr bgcolor="#E9F3FF">
                <td>In %</td>
                <td>&nbsp;</td>
                <td align="right"><? echo number_format($total_sew_recv_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($finish_qnty_in_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($finish_qnty_out_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($total_finish_qnty_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($finish_reject_qnty_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($finish_reject_qnty_perc,0,'.',''); ?>&nbsp;</td>
             </tr>
        </table> 
        <br />
        <?
			$left_over_finish_gmts=$total_finish_qnty-$tot_exfactory_qnty;
			$left_over_finish_gmts_perc=($left_over_finish_gmts/$tot_po_qnty)*100;
			$tot_exfactory_qnty_perc=($tot_exfactory_qnty/$tot_po_qnty)*100;
		?>
        <table width="450" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr>
                    <th width="90" rowspan="2">Particulars</th>
                    <th colspan="4" width="360">Garment Delivery</th>
                </tr>
                <tr>
                    <th width="90">Gmts. Req.</th>
                    <th width="90">Total Finish</th>
                    <th width="90">Ex-Factory</th>
                    <th>Left Over</th>
                </tr>
            </thead>
            <tr bgcolor="#FFFFFF">
                <td>Quantity</td>
                <td align="right"><? echo number_format($tot_po_qnty,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($total_finish_qnty,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($tot_exfactory_qnty,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($left_over_finish_gmts,0,'.',''); ?>&nbsp;</td>
            </tr>
            <tr bgcolor="#E9F3FF">
                <td>In %</td>
                <td>&nbsp;</td>
                <td align="right"><? echo number_format($total_finish_qnty_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($tot_exfactory_qnty_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($left_over_finish_gmts_perc,0,'.',''); ?>&nbsp;</td>
             </tr>
        </table>  
        <br />
        <table width="450" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr>
                    <th width="90" rowspan="2">Particulars</th>
                    <th colspan="4" width="360">Left Over</th>
                </tr>
                <tr>
                    <th width="90">Gray Fab.</th>
                    <th width="90">Finish Fab.</th>
                    <th width="90">Garment</th>
                    <th>Trims</th>
                </tr>
            </thead>
            <tr bgcolor="#FFFFFF">
                <td>Quantity</td>
                <td align="right"><? echo number_format($left_over,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($finish_left_over,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($left_over_finish_gmts,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($tot_trims_left_over_qnty,0,'.',''); ?>&nbsp;</td>
            </tr>
            <tr bgcolor="#E9F3FF">
                <td>In %</td>
                <td align="right"><? echo number_format($left_over_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($finish_left_over_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($left_over_finish_gmts_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($tot_trims_left_over_qnty_perc,0,'.',''); ?>&nbsp;</td>
             </tr>
        </table> 
        <br />
        <table width="720" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
            	<tr>
                    <th width="450" colspan="5">Reject</th>
                    <th width="270" colspan="3">Process Loss</th>
                </tr>
                <tr>
                    <th width="90">Particulars</th>
                    <th width="90">Print Gmts.</th>
                    <th width="90">Emb Gmts.</th>
                    <th width="90">Sewing Gmts.</th>
                    <th width="90">Finishing Gmts.</th>
                    <th width="90">Yarn</th>
                    <th width="90">Dyeing</th>
                    <th>Cutting</th>
            	</tr>
            </thead>
            <tr bgcolor="#FFFFFF">
                <td>Quantity</td>
                <td align="right"><? echo number_format($print_reject_qnty,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($emb_reject_qnty,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($sew_reject_qnty,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($finish_reject_qnty,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($process_loss,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($process_loss_finishing,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($cutting_process_loss,0,'.',''); ?>&nbsp;</td>
            </tr>
            <tr bgcolor="#E9F3FF">
                <td>In %</td>
                <td align="right"><? echo number_format($print_reject_qnty_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($emb_reject_qnty_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($sew_reject_qnty_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($finish_reject_qnty_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($process_loss_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($process_loss_finishing_perc,0,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($cutting_process_loss_perc,0,'.',''); ?>&nbsp;</td>
             </tr>
        </table>     
	</fieldset>
<?
	$user_id=$_SESSION['logic_erp']['user_id'];
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

disconnect($con);
?>
