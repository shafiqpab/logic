<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../includes/common.php');
require_once('../../includes/class4/class.conditions.php');
require_once('../../includes/class4/class.reports.php');
require_once('../../includes/class4/class.fabrics.php');



$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond  group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0); 
	exit();
}

if($action=="job_popup")
{
	echo load_html_head_contents("Job Info", "../../", 1, 1,'','','');
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
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref",3=>"Order No",4=>"File No",5=>"Ref. No");
							$dd="change_search_event(this.value, '0*0*0*0*0', '0*0*0*0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company_id; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year; ?>', 'job_popup_search_list_view', 'search_div', 'order_reconcile_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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

if ($action=="job_popup_search_list_view")
{
  	//echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
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
	else if($search_type==4 && $search_value!=''){
		$search_con=" and b.file_no like('%$search_value%')";	
	}
	else if($search_type==5 && $search_value!=''){
		$search_con=" and b.grouping like('%$search_value%')";	
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
	$sql= "select a.id,a.job_no_prefix_num, a.job_no, a.company_name,a.buyer_name,a.style_ref_no,$year_field as year from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company_id $buyer_cond $year_cond $search_con group by a.id,a.job_no_prefix_num, a.job_no, a.company_name,a.buyer_name,a.style_ref_no,a.insert_date order by a.id DESC";
	//echo $sql;
	echo  create_list_view("list_view", "Job No,Year,Company,Buyer Name,Style Ref. No", "70,70,120,100,100","570","230",0, $sql , "js_set_value", "year,job_no_prefix_num", "", 1, "0,0,company_name,buyer_name,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no", "","setFilterGrid('list_view',-1)",'0,0,0,0,0');
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
	//if(trim($txt_job_no)!="") $job_no_cond="%".trim($txt_job_no); else $job_no_cond="%%";//."%"
	if(trim($txt_job_no)!="") $job_no_cond="".trim($txt_job_no); else $job_no_cond="";//."%"
	if(trim($txt_file_no)!="") $file_no_cond="and b.file_no=$txt_file_no"; else $file_no_cond="";
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


		$costing_per_arr = return_library_array("select job_no,costing_per from wo_pre_cost_mst","job_no", "costing_per");

			
		// print_r($con_array);
		$i=1; $all_po_id=""; $job_no=''; $tot_po_qnty=0; $tot_exfactory_qnty=0; $ratio=0;
		$sql="SELECT a.id,a.buyer_name, a.job_no, a.total_set_qnty as ratio, a.style_ref_no, a.gmts_item_id, b.id as po_id, b.po_number, b.po_quantity as po_qnty, b.plan_cut, b.pub_shipment_date,b.shipment_date,
		sum(CASE WHEN c.production_type =1 and c.production_type =1 THEN c.production_quantity  ELSE 0 END) AS cutting_qnty,
		sum(CASE WHEN c.production_type =1 and c.production_type =1 THEN c.reject_qnty  ELSE 0 END) AS cutting_reject_qnty,
		sum(CASE WHEN c.production_type =4 and c.production_type =4 THEN c.production_quantity ELSE 0 END) AS sewing_in_qnty,
		sum(CASE WHEN c.production_type =5 and c.production_type =5 THEN c.reject_qnty ELSE 0 END) AS sewing_reject_qnty,
		sum(CASE WHEN c.production_type =8 and c.production_type =8 THEN c.production_quantity ELSE 0 END) AS paking_finish_qnty,
		sum(CASE WHEN c.production_type =8 and c.production_type =8 THEN c.reject_qnty ELSE 0 END) AS paking_finish_reject_qnty
		  from wo_po_details_master a, wo_po_break_down b left join pro_garments_production_mst c on (b.id=c.po_break_down_id and c.status_active=1 and  c.is_deleted=0) where a.job_no=b.job_no_mst and a.company_name=$cbo_company_name and a.job_no_prefix_num =$job_no_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $buyer_id_cond $year_cond $file_no_cond $ref_no_cond
		  group by a.id, a.buyer_name, a.job_no, a.total_set_qnty, a.style_ref_no, a.gmts_item_id, b.id, b.po_number, b.po_quantity, b.plan_cut, b.pub_shipment_date,b.shipment_date
		   order by b.pub_shipment_date, b.id";	
		   // echo $sql;die();
		$result=sql_select($sql);
		$all_po_arr=array();
		$cutting_reject_qnty = 0;
		$sewing_reject_qnty = 0;
		$paking_finish_reject_qnty = 0;
		$jobArr = array();
		foreach($result as $row)
		{
			$all_po_arr[$row[csf('po_id')]]=$row[csf('po_id')];
			$all_job_id.=$row[csf('id')].',';
			$job_arr[$row[csf('po_id')]]=$row[csf('job_no')];
			$jobArr[$row[csf('job_no')]]=$row[csf('job_no')];

			$cutting_reject_qnty += $row[csf('cutting_reject_qnty')];
			$sewing_reject_qnty  += $row[csf('sewing_reject_qnty')];
			$paking_finish_reject_qnty  += $row[csf('paking_finish_reject_qnty')];
		}
		$poIds = implode(",", $all_po_arr);
		$condition= new condition();
		// $condition->company_name("=$cbo_company_id");

		if(isset($poIds))
		{
			$condition->po_id_in($poIds);
		}
		$condition->init();
		// var_dump($condition);

		$fabric= new fabric($condition);
		$fabric_req_qty_arr=$fabric->getQtyArray_by_order_knitAndwoven_greyAndfinish();
		// print_r($fabric_req_qty_arr);die();	
		

		$jobNo = "'".implode("','", $jobArr)."'";
		$sql_con = "SELECT b.po_break_down_id as po_id,sum(b.cons) as con,a.uom from wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b  where a.id=b.pre_cost_fabric_cost_dtls_id and a.job_no=$jobNo and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_no=b.job_no group by b.po_break_down_id,a.uom";
		// echo $sql_con;
		$sql_con_res = sql_select($sql_con);
		$con_array = array();
		$uom_array = array();
		foreach ($sql_con_res as $val) 
		{
			$con_array[$val[csf('po_id')]] = $val[csf('con')];
			$uom_array[$val[csf('po_id')]] = $val[csf('uom')];
		}
		//echo $all_job_id.'dd';
		$all_job_id=rtrim($all_job_id,',');
		if(!empty($all_po_arr))
		{
			//echo "select sum(ex_factory_qnty) as qnty,po_break_down_id from pro_ex_factory_mst where status_active=1 and is_deleted=0 and po_break_down_id in (".implode(",",$all_po_arr).") group by po_break_down_id";
			$exFactoryArr=return_library_array("select sum(ex_factory_qnty) as qnty,po_break_down_id from pro_ex_factory_mst where status_active=1 and is_deleted=0 and po_break_down_id in (".implode(",",$all_po_arr).") group by po_break_down_id","po_break_down_id","qnty");
			$sql_defect="select production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty from pro_gmts_prod_dft where po_break_down_id in (".implode(",",$all_po_arr).") and production_type in(1,5,8)";
			//echo $sql_defect;die;
			$defect_result=sql_select($sql_defect);
			foreach($defect_result as $d_val)
			{
				$po_defect_data[$d_val[csf('production_type')]][$d_val[csf('defect_point_id')]]['defect_qty']+=$d_val[csf('defect_qty')];
				$po_defect_data[$d_val[csf('production_type')]][$d_val[csf('defect_point_id')]]['defect_type']=$d_val[csf('defect_type_id')];
				$total_defect_data[$d_val[csf('production_type')]]['total']+=$d_val[csf('defect_qty')];
			}
			$sql_finish=sql_select("select  sum(quantity) as finish_qty,po_breakdown_id from order_wise_pro_details where po_breakdown_id in (".implode(",",$all_po_arr).") and trans_type=1 and entry_form in (7,37,68) group by po_breakdown_id");
			
			foreach($sql_finish as $f_val)
			{
				$po_finish_qty_arr[$f_val[csf('po_breakdown_id')]]=$f_val[csf('finish_qty')];
			}
			/*$sql_finish_issue=sql_select("select  sum(c.quantity) as finish_qty,c.po_breakdown_id from inv_issue_master a,inv_finish_fabric_issue_dtls b,order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and  c.po_breakdown_id in (".implode(",",$all_po_arr).") and c.trans_type=2 and c.entry_form in (18,71) and a.issue_purpose in(9) and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by c.po_breakdown_id");	*/	
			$sql_finish_issue=sql_select(" SELECT SUM (d.quantity) AS finish_qty, d.po_breakdown_id FROM inv_issue_master a, order_wise_pro_details d, inv_transaction c WHERE d.po_breakdown_id in (".implode(",",$all_po_arr).") AND d.trans_type = 2 AND d.entry_form IN (18, 71) and c.id = d.trans_id AND d.trans_id != 0 AND c.item_category = 2 and d.status_active=1 and d.is_deleted=0 and a.issue_purpose in(9) and a.id=c.mst_id and a.status_active=1 and a.is_deleted=0 GROUP BY po_breakdown_id");//and a.issue_purpose not in(44) change for BPF date: 25-07-2020
			foreach($sql_finish_issue as $f_val)
			{
				$po_finish_issue_qty_arr[$f_val[csf('po_breakdown_id')]]=$f_val[csf('finish_qty')];
			}

			$sql_transfer = "SELECT d.po_breakdown_id as po_id, 
			sum(case when d.entry_form in (7,37,66,68) then d.quantity else 0 end) as receive_qnty, 
			sum(case when d.entry_form in(15,14,134) and d.trans_type=5 then d.quantity else 0 end) as rec_trns_qnty, 
			sum(case when d.entry_form in (18,71) then d.quantity else 0 end) as issue_qnty, 
			sum(case when d.entry_form in (126,52) then d.quantity else 0 end) as issue_ret_qnty, 
			sum(case when d.entry_form in(15,14,134) and d.trans_type=6 then d.quantity else 0 end) as issue_trns_qnty 
			from order_wise_pro_details d where d.po_breakdown_id in (".implode(",",$all_po_arr).") and d.status_active=1 and d.is_deleted=0
			group by d.po_breakdown_id ";
			// echo $sql_transfer;die();
			$sql_transfer_res = sql_select($sql_transfer);
			$transfer_qty_array = array();
			foreach ($sql_transfer_res as $val) 
			{
				$transfer_qty_array[$val[csf('po_id')]]['trans_rcv'] = $val[csf('rec_trns_qnty')];
				$transfer_qty_array[$val[csf('po_id')]]['issue_rtn'] = $val[csf('issue_ret_qnty')];
			}

			$sql_booking=sql_select("select  b.po_break_down_id,sum(b.fin_fab_qnty) as fin_qty  from  wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no  and b.is_short=2 and b.booking_type=1 and  b.po_break_down_id in (".implode(",",$all_po_arr).") and b.status_active=1  and b.is_deleted=0 group by b.po_break_down_id ");
			
			foreach($sql_booking as $f_val)
			{
				$po_booking_qty_arr[$f_val[csf('po_break_down_id')]]=$f_val[csf('fin_qty')];
			}
			$bundle_sql=sql_select("SELECT a.cutting_no,a.cad_marker_cons, b.color_id,b.gmt_item_id as gmt_id, c.size_id, c.size_qty,c.order_id as po_id
			from ppl_cut_lay_mst a, ppl_cut_lay_dtls b, ppl_cut_lay_bundle c  where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form=77 and c.order_id in (".implode(",",$all_po_arr).")");
			/*$bundle_sql=sql_select("select a.cutting_no,a.cad_marker_cons,b.order_id as po_id
			from ppl_cut_lay_mst a, ppl_cut_lay_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=77 and b.order_id in (".implode(",",$all_po_arr).") group by a.cutting_no,a.cad_marker_cons, b.order_id");*/
			//Issue ID=3138 For BFL
		
		
			$bundle_data_array=array();
			$cut_tot_bundle=array();
			foreach($bundle_sql as $row)
			{
				$job_no=$job_arr[$row[csf('po_id')]];
				if($row[csf('cad_marker_cons')]>0)
				{
					//echo $row[csf('cad_marker_cons')].', ';
					$bundle_data_array[$job_no]['cad_marker_cons']+=$row[csf('cad_marker_cons')];
					$bundle_po_data_array[$row[csf('po_id')]]['size_qty']+=$row[csf('size_qty')]*$row[csf('cad_marker_cons')];
					$bundle_job_data_array[$job_no]['size_qty']+=$row[csf('size_qty')];
					
					$cad_marker_dzn_po_data_array[$row[csf('po_id')]]['cad_marker_cons']+=$row[csf('cad_marker_cons')]*12;
					$cad_marker_dzn_po_data_array[$row[csf('po_id')]]['cutting_no'].=$row[csf('cutting_no')].',';
				
				}				
			}
			//print_r($cad_marker_dzn_po_data_array);
			$cutlay_sql=sql_select("SELECT c.dtls_id,b.order_id as po_id,c.roll_wgt,c.size_qty
			from ppl_cut_lay_mst a, ppl_cut_lay_dtls b, ppl_cut_lay_roll_dtls c  where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form=77 and b.order_id in (".implode(",",$all_po_arr).")");
		
			$lay_data_array=array();
			$dtlsIdChk = array();
			foreach($cutlay_sql as $row)
			{
				$lay_data_array[$row[csf('po_id')]]['size_qty']+=$row[csf('size_qty')];
				if(!isset($dtlsIdChk[$row[csf('dtls_id')]]))
				{
					$lay_data_array[$row[csf('po_id')]]['roll_wgt']+=$row[csf('roll_wgt')];	
				}
				$dtlsIdChk[$row[csf('dtls_id')]]=$row[csf('dtls_id')];
			}
			
			$production_sql="SELECT a.production_date, c.po_break_down_id as order_id, c.item_number_id, c.country_id, c.color_number_id as color_id, sum(b.reject_qty) as all_reject_qnty,
			sum(CASE WHEN b.production_type =1 and a.production_type =1 THEN b.production_qnty  ELSE 0 END) AS cutting_qnty,
			sum(CASE WHEN b.production_type =1 and a.production_type =1 THEN b.reject_qty ELSE 0 END) AS cutting_reject_qty,
			sum(CASE WHEN b.production_type =4 and a.production_type =4 THEN b.production_qnty ELSE 0 END) AS sewing_in_qnty,
			sum(CASE WHEN b.production_type =5 and a.production_type =5 THEN b.production_qnty ELSE 0 END) AS sewing_out_qnty,
			sum(CASE WHEN b.production_type =5 and a.production_type =5 THEN b.reject_qty ELSE 0 END) AS sewing_reject_qty,
			sum(CASE WHEN b.production_type =7 and a.production_type =7  THEN b.production_qnty ELSE 0 END) AS iron_qnty,
			sum(CASE WHEN b.production_type =8 and a.production_type =8 THEN b.production_qnty ELSE 0 END) AS paking_finish_qnty,
			sum(CASE WHEN b.production_type =8 and a.production_type =8  THEN b.reject_qty ELSE 0 END) AS paking_finish_reject_qty,
			sum(CASE WHEN b.production_type =11 and a.production_type=11 THEN b.production_qnty ELSE 0 END) AS poly_qnty,
			sum(CASE WHEN b.production_type =11 and a.production_type =11  THEN b.reject_qty ELSE 0 END) AS poly_reject_qty 
			from  pro_garments_production_dtls b,pro_garments_production_mst a,wo_po_color_size_breakdown c 
			where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active in(1,2,3) and c.is_deleted=0 and a.production_source=1 and b.production_type in(1,4,5,7,8,11) and a.production_type in(1,4,5,7,8,11) and c.po_break_down_id in (".implode(",",$all_po_arr).")
			group by a.production_date, c.po_break_down_id, c.item_number_id, c.country_id, c.color_number_id";
				$production_sql_result=sql_select($production_sql);
				$po_all_reject_qty=0;
			foreach($production_sql_result as $row)
			{
					$production_data_arr[$row[csf("order_id")]]["cutting_reject_qty"]+=$row[csf("cutting_reject_qty")];
					$production_data_arr[$row[csf("order_id")]]["sewing_out_qnty"]+=$row[csf("sewing_out_qnty")];
					$production_data_arr[$row[csf("order_id")]]["sewing_reject_qty"]+=$row[csf("sewing_reject_qty")];
					$production_data_arr[$row[csf("order_id")]]["paking_finish_reject_qty"]+=$row[csf("paking_finish_reject_qty")];
					$po_all_reject_qty+=$row[csf("all_reject_qnty")];
				//echo $po_all_reject_qty;
			}
			$req_array=array();//and id=$req_id 
			$req_sql=sql_select("select id,requisition_number_prefix_num,sample_stage_id,style_ref_no,quotation_id from sample_development_mst where is_deleted=0 and status_active=1 and entry_form_id=117 and sample_stage_id=1 and quotation_id in (".$all_job_id.")");
			//echo "select id,requisition_number_prefix_num,sample_stage_id,style_ref_no,quotation_id from sample_development_mst where is_deleted=0 and status_active=1 and entry_form_id=117 and sample_stage_id=1 and quotation_id in (".$all_job_id.")";
			foreach($req_sql as $row)
			{
				$req_array[$row[csf("id")]]['buyer_name']=$row[csf("buyer_name")];
				$req_array[$row[csf("id")]]['style_ref_no']=$row[csf("style_ref_no")];
				$req_array[$row[csf("id")]]['dealing_marchant']=$row[csf("dealing_marchant")];
				$req_array[$row[csf("id")]]['requisition_number_prefix_num']=$row[csf("requisition_number_prefix_num")];
				$req_array[$row[csf("id")]]['sample_stage_id']=$row[csf("sample_stage_id")];
				$req_array[$row[csf("id")]]['quotation_id']=$row[csf("quotation_id")];
				$all_requ_id_arr[]=$row[csf('id')];
			}
			$tot_sample_del_qty=0;
			//$sampl_result=sql_select("select a.id, b.sample_name,b.sample_development_id,b.ex_factory_qty from sample_ex_factory_mst a,sample_ex_factory_dtls b where a.id=b.sample_ex_factory_mst_id  and b.sample_name=24 and a.entry_form_id=132 and a.company_id=$cbo_company_name and a.status_active=1  and b.sample_development_id in (".implode(",",$all_requ_id_arr).")");
			$sampl_result=sql_select("select a.id, b.sample_name,b.sample_development_id,b.ex_factory_qty from sample_ex_factory_mst a,sample_ex_factory_dtls b,lib_sample c where a.id=b.sample_ex_factory_mst_id and c.id=b.sample_name and c.sample_type=11 and a.entry_form_id=132 and a.company_id=$cbo_company_name and a.status_active=1  and b.sample_development_id in (".implode(",",$all_requ_id_arr).")");
			foreach($sampl_result as $row)
			{
				$tot_sample_del_qty+=$row[csf("ex_factory_qty")];
			}
			//echo $tot_ex_factory_qty;
		
		}
		
		//print_r($po_booking_qty_arr);die;
		if(empty($result))
		{
			echo '<div align="left" style="width:1000px;"><h1 align="center" style="color:#f00;">Order not found</h></div>'; die;
		}
		?>
        <div style="width:100%"> 
            <table width="2260" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <tr>
                        <th width="30" rowspan="2">SL</th>
                        <th width="100" rowspan="2">Buyer</th>
						<th width="100" rowspan="2">Job No</th>
                        <th width="100" rowspan="2">Style</th>
                        <th width="100" rowspan="2">Order No</th>
                        <th width="100" rowspan="2">Order Qty. (Pcs)</th>
                        <th width="80" rowspan="2">Plan Ship Date</th>
                        <th width="80" rowspan="2">Actual Ship date</th>					
                        <th width="80" rowspan="2">OTD</th>
                        <!-- <th width="80" rowspan="2">UOM</th> -->
                        <th width="80" rowspan="2">FF. Req. Qty</th>
                        <th width="100" rowspan="2">Fabric Rcvd Store (Kg)</th>
						<th width="100" rowspan="2">Fabric Rcvd Cutting (Kg)</th>
						<th width="100" rowspan="2">Fabric In-Hand</th>
                        <th width="100" colspan="2">Consumption (Kg/dzn)</th>
                        <th width="100" rowspan="2"> Cuttable Qty</th>
                        <th width="100" rowspan="2">Actual Cut Qty</th>
                        <th width="100" rowspan="2">Input Qty</th> 
                        <th width="100" rowspan="2">Finish Receive Qty</th>
                        <th width="100" rowspan="2">Shipped qty</th>
                        <th width="80" rowspan="2" title="(100*Ex Factory Qty)/Actual Cut Qty">Cut to Ship</th>
                        <th rowspan="2"  title="(100*Ex Factory Qty)/PO Qty">Ord to Ship</th>
                    </tr>
                    <tr>
                        <th width="100" >Finish</th>
                        <th width="100" >Practical</th>					
                    </tr>
                </thead>
                <tbody>
                <?	$tot_in_hand_qty=$tot_cuttable_qty=$tot_sewing_out_qnty=$tot_avg_cut_lay_qty_cad_practical=$tot_cutting_nos=$tot_cad_marker_dzn=$tot_fin_fab_req_qty=0;
                	$totpo=0;
                    foreach($result as $row)
                    {
                    	$totpo++;
                        if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        
                        $po_qnty=$row[csf('po_qnty')]*$row[csf('ratio')];
						// $plun_cut_qnty=$row[csf('plan_cut')]*$row[csf('ratio')];
						$plun_cut_qnty=$row[csf('cutting_qnty')];
                        $exfactory_qnty=$exFactoryArr[$row[csf('po_id')]];
                        $ship_perc=($exfactory_qnty*100)/$po_qnty;
                        
                        $tot_po_qnty+=$po_qnty; 
						$tot_plun_cut_qnty+=$plun_cut_qnty; 
                        $tot_exfactory_qnty+=$exfactory_qnty; 
						$job_no=$job_arr[$row[csf('po_id')]];
                       // echo $job_no;
              			$cad_marker_cons=$bundle_data_array[$job_no]['cad_marker_cons'];			
					    $size_qty=$bundle_po_data_array[$row[csf('po_id')]]['size_qty'];
						$tot_cad_practical_qty= $size_qty/$bundle_job_data_array[$job_no]['size_qty'];
					
						$ratio=$row[csf('ratio')];
						
						$cad_marker_dzn=$cad_marker_dzn_po_data_array[$row[csf('po_id')]]['cad_marker_cons'];
						$cutting_nos=rtrim($cad_marker_dzn_po_data_array[$row[csf('po_id')]]['cutting_no'],',');
						$cutting_nos=array_unique(explode(",",$cutting_nos));
						$cutting_nos=count($cutting_nos)*12;
						//echo $cutting_nos.'dd';
						$tot_cutting_nos+=$cutting_nos;
						$tot_cad_marker_dzn+=$cad_marker_dzn;
						
						$avg_cut_lay_qty_cad_practical=$cad_marker_dzn/$cutting_nos;

						//==============================================
						$roll_wgt = $lay_data_array[$row[csf('po_id')]]['roll_wgt'];
						$sizeQty = $lay_data_array[$row[csf('po_id')]]['size_qty'];
						$cad_practical = ($roll_wgt/$sizeQty)*12;
						// echo $cad_practical ."= (".$roll_wgt."/".$sizeQty.")*12";


                    	if($costing_per_arr[$job_no]==1) $dzn_qnty=12;
						else if($costing_per_arr[$job_no]==3) $dzn_qnty=12*2;
						else if($costing_per_arr[$job_no]==4) $dzn_qnty=12*3;
						else if($costing_per_arr[$job_no]==5) $dzn_qnty=12*4;
						else $dzn_qnty=1;
						$wovReqQty = array_sum($fabric_req_qty_arr['woven']['finish'][$row[csf('po_id')]]);
						$knitReqQty = array_sum($fabric_req_qty_arr['knit']['finish'][$row[csf('po_id')]]);
						// $finCon = ($fabReqQty/$po_qnty)*$dzn_qnty;
						$fabReqQty = ($wovReqQty + $knitReqQty);
						$finCon = ($fabReqQty/$po_qnty)*12;
						$po_id = $row[csf('po_id')];
					
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></div></td>
							<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $job_no; ?></div></td>
                            
                            <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $row[csf('style_ref_no')]; ?></div></td>
                            <td width="110"><div style="word-wrap:break-word; width:110px"><? echo $row[csf('po_number')]; ?></div></td>
                            
                            <td width="100" align="right"><? echo $po_qnty; ?>&nbsp;</td>
                           
                            <td width="80" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
                            <td width="80" align="center"><? echo change_date_format($row[csf('shipment_date')]); ?></td>
                            <td width="80" align="right"><? if($exfactory_qnty>=$po_qnty) echo "100%"; ?></td>
                            <!-- <td width="80" align="center"><? //echo $unit_of_measurement[$uom_array[$row[csf('po_id')]]]; ?></td> -->
                            <td width="80" align="right">
                            	<a href="javascript:void(0)" onClick="open_ff_popup('<? echo $job_no; ?>','<? echo $po_id;?>')">
                            		<? echo number_format($fabReqQty,0); ?>
								</a>
                            </td>


                            <td width="80" align="right"><? echo $po_finish_qty_arr[$row[csf('po_id')]]+$transfer_qty_array[$row[csf('po_id')]]['trans_rcv']; ?>&nbsp;</td>
							<td width="100" align="right"><? echo number_format($po_finish_issue_qty_arr[$row[csf('po_id')]],2); ?>&nbsp;</td>
							<td width="100" align="right"><? echo number_format(($po_finish_qty_arr[$row[csf('po_id')]]+$transfer_qty_array[$row[csf('po_id')]]['trans_rcv'])-$po_finish_issue_qty_arr[$row[csf('po_id')]],3); ?>&nbsp;</td>
                            <!-- <td width="80" align="right" title="Booking Qty(<? //echo $po_booking_qty_arr[$row[csf('po_id')]];?>)*12/PO Qty"><? //echo  number_format((($po_booking_qty_arr[$row[csf('po_id')]]*12)/$po_qnty),2); ?>&nbsp;</td> -->
                            <td width="80" align="right" title="(Fin. Fab. Req. Qty / PO Qty) * 12" ><? echo  number_format($finCon,3);?>&nbsp;</td>
                            <td width="80" align="right" title="(PO wise total roll wgt/PO wise total lay qty)*12"><? echo number_format($cad_practical,3); //number_format($avg_cut_lay_qty_cad_practical,3); ?>&nbsp;</td>
                            <td width="80" align="right" title="Fab Rcv Qty/Cad Practical*12"><?  $cuttable_qty=($po_finish_issue_qty_arr[$row[csf('po_id')]]/$cad_practical)*12;echo number_format($cuttable_qty,0); ?>&nbsp;</td>
                            <td width="80" align="right"><? echo $row[csf('cutting_qnty')]; ?>&nbsp;</td>
                             <td width="80" align="right"><? echo $row[csf('sewing_in_qnty')]; ?>&nbsp;</td> 
                        
                            <td width="80" align="right"><? echo $production_data_arr[$row[csf("po_id")]]["sewing_out_qnty"]; ?>&nbsp;</td>
                            <td width="80" align="right"><? echo $exfactory_qnty; ?>&nbsp;</td>
                            <td width="80" align="right" title="(100*<? echo $exfactory_qnty; ?>)/<? echo $row[csf('cutting_qnty')]; ?>"><? echo number_format(((100*$exfactory_qnty)/$row[csf('cutting_qnty')]),0)."%"; ?>&nbsp;</td>
                            <td width="80" align="right" title="(100*<? echo $exfactory_qnty; ?>)/<? echo $po_qnty; ?>"><? echo number_format(((100*$exfactory_qnty)/$po_qnty),0)."%"; ?>&nbsp;</td>
                            
                        </tr>
                    <?
                        $i++;
						$total_fabric_received+=$po_finish_qty_arr[$row[csf('po_id')]]+$transfer_qty_array[$row[csf('po_id')]]['trans_rcv'];
						$total_fabric_cutt+=$po_finish_issue_qty_arr[$row[csf('po_id')]];
						$total_fabric_issued+=$po_finish_issue_qty_arr[$row[csf('po_id')]];
						// $total_booking_qty+=$po_booking_qty_arr[$row[csf('po_id')]];
						$total_booking_qty+=$finCon;
						$tot_cut_qnty+=$row[csf('cutting_qnty')]; 
						$tot_sew_qnty+=$row[csf('sewing_in_qnty')]; 
						$tot_avg_cut_lay_qty_cad_practical+=$avg_cut_lay_qty_cad_practical; 
						$tot_sewing_out_qnty+=$production_data_arr[$row[csf("po_id")]]["sewing_out_qnty"]; 
						$tot_cuttable_qty+=$cuttable_qty; 
						$tot_in_hand_qty+=($po_finish_qty_arr[$row[csf('po_id')]]+$transfer_qty_array[$row[csf('po_id')]]['trans_rcv'])-$po_finish_issue_qty_arr[$row[csf('po_id')]]; 
						$tot_fin_fab_req_qty += $fabReqQty;
                    }
					unset($result);
					// echo $i;
                ?>
                </tbody>
                <tfoot>
                    <th align="right" colspan="5">Total</th>					
                    <th><? echo $tot_po_qnty; ?>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <!-- <th>&nbsp;</th> -->
                    <th><?php echo number_format($tot_fin_fab_req_qty,0); ?></th>
                    <th><?php echo $total_fabric_received; ?></th>
					 <th><?php echo number_format($total_fabric_cutt,2); ?></th>
					  <th><?php echo number_format($tot_in_hand_qty,3); ?></th>
					  
                    <th><?php //echo number_format($total_booking_qty/$totpo,3); ?></th>
                      <th title="Total CadDzn=<? echo $tot_cad_marker_dzn.',Tot Cut nos='.$tot_cutting_nos;?>"><?php $tot_avg_cad_practical=$tot_cad_marker_dzn/($tot_cutting_nos); //echo number_format($tot_avg_cad_practical,2);// number_format((($total_fabric_issued*12)/$tot_po_qnty),2); //?></th>
                    <th><?php echo number_format($tot_cuttable_qty,0); ?></th>
                    <th><?php echo $tot_cut_qnty; ?></th>
                    <th><?php echo $tot_sew_qnty; ?></th> 
                    <th><?php echo $tot_sewing_out_qnty; ?></th>
                    <th><? echo $tot_exfactory_qnty; ?>&nbsp;</th>
                    <th><? echo number_format(((100*$tot_exfactory_qnty)/$tot_plun_cut_qnty),0)."%"; ?></th>
                    <th><? echo number_format(((100*$tot_exfactory_qnty)/$tot_po_qnty),0)."%"; ?></th>
                </tfoot>
            </table>
        </div>
        <br />
        
        <?php 
			$second_table=640;
			if(count($po_defect_data[1])>0) $second_table+=(count($po_defect_data[1])*50)+100;
			if(count($po_defect_data[5])>0) $second_table+=(count($po_defect_data[5])*50)+180;
			if(count($po_defect_data[8])>0) $second_table+=(count($po_defect_data[8])*50)+100;
		
		?>
        
        <div style="width:100%"> 
            <table width="<?php echo $second_table; ?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                	<tr>
                    	<?php
						if(count($po_defect_data[1])>0)
						{
						?>
							 <th colspan="<?php echo count($po_defect_data[1])+1; ?>" width="">Cut Panel Alter</th>
						<?php
						}
						
						if(count($po_defect_data[5])>0)
						{
						?>
							 <th colspan="<?php echo count($po_defect_data[5])+2; ?>" width="">Sewing Line Alter</th>
						<?php
						}
						if(count($po_defect_data[8])>0)
						{
						?>
							 <th colspan="<?php echo count($po_defect_data[8])+1; ?>" width="">Pac. & Fin. Alter</th>
						<?php
						}
						?>
                        <th colspan="3" width="240">Rejection</th>
                        <th rowspan="2" width="100">FG Sample</th>
						<th rowspan="2" width="100">FG In Hand (Leftover)</th>
						<th rowspan="2" width="100">Total Leftover</th>
                        <th rowspan="2" width="100"> Total Unship Goods</th>
                        <th rowspan="2" width="">Variance</th>
                    </tr>
                    <tr>
                    <?php
                    	if(count($po_defect_data[1])>0)
						{
							foreach($po_defect_data[1] as $defect_point=>$defect_data)
							{
							?>
							 	<th  width="50"><?php echo $cutting_qc_reject_type[$defect_point]; ?> </th>
							<?php
							}
							?>
                            <th width="80">Total</th>
                            
                            <?php
						}
						if(count($po_defect_data[5])>0)
						{
							foreach($po_defect_data[5] as $defect_point=>$defect_data)
							{
								if($defect_data['defect_type']==1)
								{
								?>
									<th  width="50"><?php echo $sew_fin_alter_defect_type[$defect_point]; ?> </th>
								<?php
								}
								else
								{
								?>
									<th  width="50"><?php echo $sew_fin_spot_defect_type[$defect_point]; ?> </th>
								<?php
								}
							}
							?>
                            <th width="80">Total</th>
                            <th width="80">Alter%</th>
                            
                            <?php
						}
						if(count($po_defect_data[8])>0)
						{
							foreach($po_defect_data[8] as $defect_point=>$defect_data)
							{
								if($defect_data['defect_type']==1)
								{
								?>
									<th  width="50"><?php echo $sew_fin_alter_defect_type[$defect_point]; ?> </th>
								<?php
								}
								else
								{
								?>
									<th  width="50"><?php echo $sew_fin_spot_defect_type[$defect_point]; ?> </th>
								<?php
								}							
							}
							
							?>
                            <th width="80">Total</th>
                            
                            <?php
						}
					?>
                    
                        <th rowspan="2" width="80">Cut Panel Rejection (Cutting section)</th>
                        <th rowspan="2" width="80">Sew. Complete Garments Rejection</th>
                        <th rowspan="2" width="80">Cut Panel Rejection (Sew. section)</th>	
                      
                    </tr>
                </thead>
               
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <?php
                    	if(count($po_defect_data[1])>0)
						{
							foreach($po_defect_data[1] as $defect_point=>$defect_data)
							{
							?>
							 	<td  width="50" align="right"><?php echo $defect_data['defect_qty']; ?> </td>
							<?php
							}
							?>
                            <td width="80" align="right"><?php echo $total_defect_data[1]['total']; ?></td>
                            
                            <?php
						}
						if(count($po_defect_data[5])>0)
						{
							foreach($po_defect_data[5] as $defect_point=>$defect_data)
							{
							?>
							 	<td  width="50" align="right"><?php echo $defect_data['defect_qty']; ?> </td>
							<?php
							}
							?>
                            <td width="80" align="right"><?php echo $total_defect_data[5]['total']; ?></td>
                            <td width="80" align="right"><?php echo number_format(($total_defect_data[5]['total']/$tot_po_qnty)*100,2); ?>%</td>
                            
                            <?php
						}
						if(count($po_defect_data[8])>0)
						{
							foreach($po_defect_data[8] as $defect_point=>$defect_data)
							{
							?>
							 	<td  width="50" align="right"><?php echo $defect_data['defect_qty']; ?> </td>
							<?php
							}
							
							?>
                            <td width="80" align="right"><?php echo $total_defect_data[8]['total']; ?></td>
                            
                            <?php
						}
						$fg_inhand=$tot_sewing_out_qnty-$tot_exfactory_qnty;
						$unshiped_good=$po_all_reject_qty;
						//$fg_inhand+$total_defect_data[1]['total']+$total_defect_data[5]['total']+$total_defect_data[8]['total'];
						// $variance_pcs=$tot_cut_qnty-($unshiped_good+$fg_inhand+$tot_exfactory_qnty);//+$tot_sample_del_qty
						$variance_pcs=$tot_cuttable_qty-($tot_sample_del_qty+$tot_exfactory_qnty+$cutting_reject_qnty+$sewing_reject_qnty+$paking_finish_reject_qnty+$fg_inhand);//+$tot_sample_del_qty
						//echo $tot_cuttable_qty."-(".$tot_exfactory_qnty."+".$tot_sample_del_qty."+".$cutting_reject_qnty."+".$sewing_reject_qnty."+".$paking_finish_reject_qnty."+".$fg_inhand.")";
					?>
                       
                           	<td width="80" align="right"><?php echo $cutting_reject_qnty;?></td>
                           	<td width="80" align="right"><?php echo $sewing_reject_qnty;?></td>
                           	<td width="80" align="right"><?php echo $paking_finish_reject_qnty;?></td>
                           	<td width="100" align="right"><?php echo $a = $tot_sample_del_qty;?></td>
							<td width="100" align="right"><?php echo $b = $tot_sewing_out_qnty - $tot_exfactory_qnty;?></td>
							<td width="100" align="right"><?php echo $b-$a;?></td>
                            <td width="100" align="right" title="All Reject Qty of Production"><?php echo $unshiped_good;?></td>
                            <td width="" align="right" title="Cuttable Qty - (Shipped qty + Cut Panel Rejection (Cutting section) + Sew. Complete Garments Rejection + Cut Panel Rejection (Sew. section) + FG Sample + FG In Hand)"><?php echo number_format($variance_pcs,0);?></td>
                        </tr>
                    
            </table>
		<?
			echo signature_table(137, str_replace("'","",$cbo_company_name), "1800px");
			
		?>
        </div>
       
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


if($action=="finish_fabric_req_popup")
{
	echo load_html_head_contents("Job Info", "../../", 1, 1,'','','');
	extract($_REQUEST);

	$condition= new condition();
	// $condition->company_name("=$cbo_company_id");
	
	$condition->po_id_in($po_id);
	
	$condition->init();

	$fabric= new fabric($condition);
	$fabric_req_qty_arr=$fabric->getQtyArray_by_order_knitAndwoven_greyAndfinish();
	$ffReqQtyArr = array(); 
	foreach ($fabric_req_qty_arr['knit']['finish'] as $po_key => $po_val) 
	{
		foreach ($po_val as $uom_key => $req_qty) 
		{
			$ffReqQtyArr[$po_key][$uom_key] =+ $req_qty;
		}
	 	
	}
	//  ============for woven=================
	foreach ($fabric_req_qty_arr['woven']['finish'] as $po_key => $po_val) 
	{
		foreach ($po_val as $uom_key => $req_qty) 
		{
			$ffReqQtyArr[$po_key][$uom_key] =+ $req_qty;
		}
	 	
	}
	// print_r($ffReqQtyArr);die();		
	?>
    </head>
    <body>
    <div>
		<fieldset style="width:380px;">
            <table width="380" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table">
            	<caption><h2>Required Qty Info</h2></caption>
            	<thead>
                    <th width="180">Order No</th>
                    <th width="100">UOM</th>
                    <th width="100">FF. Req. Qty </th>
                </thead>
                <tbody>
                	<?
                	foreach ($ffReqQtyArr as $poid => $poval) 
                	{
                		foreach ($poval as $uom => $qty) 
                		{
                			$po_number = return_field_value("po_number","wo_po_break_down","id=$poid");
                			?>                			
		                	<tr>
		                		<td><? echo $po_number;?></td>
		                		<td><? echo $unit_of_measurement[$uom];?></td>
		                		<td align="right"><? echo number_format($qty,0);?></td>
		                    </tr>
                			<?
                		}
                	}
                	?>
            	</tbody>
           	</table>
		</fieldset>
    </div>
    </body>           
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>

    </html>
    <?
	exit(); 
}
disconnect($con);
?>
