<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../includes/common.php');



$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0); 
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
		
		
		
		$i=1; $all_po_id=""; $job_no=''; $tot_po_qnty=0; $tot_exfactory_qnty=0; $ratio=0;
		 $sql="select a.id,a.buyer_name, a.job_no, a.total_set_qnty as ratio, a.style_ref_no, a.gmts_item_id, b.id as po_id, b.po_number, b.po_quantity as po_qnty, b.plan_cut, b.pub_shipment_date,b.shipment_date,
		sum(CASE WHEN c.production_type =1 and c.production_type =1 THEN c.production_quantity  ELSE 0 END) AS cutting_qnty,
		sum(CASE WHEN c.production_type =4 and c.production_type =4 THEN c.production_quantity ELSE 0 END) AS sewing_in_qnty,
		sum(CASE WHEN c.production_type =8 and c.production_type =8 THEN c.production_quantity ELSE 0 END) AS paking_finish_qnty
		  from wo_po_details_master a, wo_po_break_down b left join pro_garments_production_mst c on (b.id=c.po_break_down_id and c.status_active=1 and  c.is_deleted=0) where a.job_no=b.job_no_mst and a.company_name=$cbo_company_name and a.job_no_prefix_num =$job_no_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $buyer_id_cond $year_cond $file_no_cond $ref_no_cond
		  group by a.id, a.buyer_name, a.job_no, a.total_set_qnty, a.style_ref_no, a.gmts_item_id, b.id, b.po_number, b.po_quantity, b.plan_cut, b.pub_shipment_date,b.shipment_date
		   order by b.pub_shipment_date, b.id";	
		   //echo $sql;
		$result=sql_select($sql);
		$all_po_arr=array();
		foreach($result as $row)
		{
			$all_po_arr[]=$row[csf('po_id')];
			$all_job_id.=$row[csf('id')].',';
			$job_arr[$row[csf('po_id')]]=$row[csf('job_no')];
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
			$sql_finish_issue=sql_select("select  sum(c.quantity) as finish_qty,c.po_breakdown_id from inv_issue_master a,inv_finish_fabric_issue_dtls b,order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and  c.po_breakdown_id in (".implode(",",$all_po_arr).") and c.trans_type=2 and c.entry_form in (18,71) and a.issue_purpose in(9) group by c.po_breakdown_id");
		
			
			foreach($sql_finish_issue as $f_val)
			{
				$po_finish_issue_qty_arr[$f_val[csf('po_breakdown_id')]]=$f_val[csf('finish_qty')];
			}
			

			$sql_booking=sql_select("select  b.po_break_down_id,sum(b.fin_fab_qnty) as fin_qty  from  wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no  and b.is_short=2 and b.booking_type=1 and  b.po_break_down_id in (".implode(",",$all_po_arr).") and b.status_active=1  and b.is_deleted=0 group by b.po_break_down_id ");
			
			foreach($sql_booking as $f_val)
			{
				$po_booking_qty_arr[$f_val[csf('po_break_down_id')]]=$f_val[csf('fin_qty')];
			}
			$bundle_sql=sql_select("select a.cutting_no,a.cad_marker_cons, b.color_id,b.gmt_item_id as gmt_id, c.size_id, c.size_qty,c.order_id as po_id
			from ppl_cut_lay_mst a, ppl_cut_lay_dtls b, ppl_cut_lay_bundle c  where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form=97 and c.order_id in (".implode(",",$all_po_arr).")");
			//echo "select a.cutting_no,a.cad_marker_cons, b.color_id,b.gmt_item_id as gmt_id, c.size_id, c.size_qty,c.order_id as po_id
			//from ppl_cut_lay_mst a, ppl_cut_lay_dtls b, ppl_cut_lay_bundle c  where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form=97 and c.order_id in (".implode(",",$all_po_arr).")";
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
					}			
					
				
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
			$sampl_result=sql_select("select a.id, b.sample_name,b.sample_development_id,b.ex_factory_qty from sample_ex_factory_mst a,sample_ex_factory_dtls b where a.id=b.sample_ex_factory_mst_id  and b.sample_name=24 and a.entry_form_id=132 and a.company_id=$cbo_company_name and a.status_active=1  and b.sample_development_id in (".implode(",",$all_requ_id_arr).")");
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
            <table width="2000" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <tr>
                        <th width="30" rowspan="2">SL</th>
                        <th width="100" rowspan="2">Buyer</th>
                        <th width="100" rowspan="2">Style</th>
                        <th width="100" rowspan="2">Order No</th>
                        <th width="100" rowspan="2">Order Qty. (Pcs)</th>
                        <th width="80" rowspan="2">Plan Ship Date</th>
                        <th width="80" rowspan="2">Actual Ship date</th>					
                        <th width="80" rowspan="2">OTD</th>
                        <th width="100" rowspan="2">Fabric Rcvd Store (Kg)</th>
						<th width="100" rowspan="2">Fabric Rcvd Cutting (Kg)</th>
						<th width="100" rowspan="2">Fabric In-Hand</th>
                        <th width="100" colspan="2">Consumption (Kg/dzn)</th>
                        <th width="100" rowspan="2"> Cuttable Qty</th>
                        <th width="100" rowspan="2">Actual Cut Qty</th>
                        <th width="100" rowspan="2">Input Qty</th>
                        <th width="100" rowspan="2">Finish Receive Qty</th>
                        <th width="100" rowspan="2">Shipped qty</th>
                        <th width="80" rowspan="2">Cut to Ship</th>
                        <th width="" rowspan="2">Ord to Ship</th>
                    </tr>
                    <tr>
                        <th width="100" >Booking</th>
                        <th width="100" >Practical</th>					
                    </tr>
                </thead>
                <tbody>
                <?	$tot_in_hand_qty=$tot_cuttable_qty=$tot_sewing_out_qnty=0;
                    foreach($result as $row)
                    {
                        if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        
                        $po_qnty=$row[csf('po_qnty')]*$row[csf('ratio')];
						$plun_cut_qnty=$row[csf('plan_cut')]*$row[csf('ratio')];
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
					
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></div></td>
                            
                            <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $row[csf('style_ref_no')]; ?></div></td>
                            <td width="110"><div style="word-wrap:break-word; width:110px"><? echo $row[csf('po_number')]; ?></div></td>
                            
                            <td width="100" align="right"><? echo $po_qnty; ?>&nbsp;</td>
                           
                            <td width="80" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
                            <td width="80" align="center"><? echo change_date_format($row[csf('shipment_date')]); ?></td>
                            <td width="80" align="right"><? if($exfactory_qnty>=$po_qnty) echo "100%"; ?></td>
                            <td width="80" align="right"><? echo $po_finish_qty_arr[$row[csf('po_id')]]; ?>&nbsp;</td>
							<td width="100" align="right"><? echo number_format($po_finish_issue_qty_arr[$row[csf('po_id')]],2); ?>&nbsp;</td>
							<td width="100" align="right"><? echo $po_finish_qty_arr[$row[csf('po_id')]]-$po_finish_issue_qty_arr[$row[csf('po_id')]]; ?>&nbsp;</td>
                            <td width="80" align="right"><? echo  number_format((($po_booking_qty_arr[$row[csf('po_id')]]*12)/$po_qnty),2); ?>&nbsp;</td>
                            <td width="80" align="right"><? echo number_format($tot_size_qty,2); ?>&nbsp;</td>
                            <td width="80" align="right" title="Fab Rcv Qty/Cad Practical*12"><?  $cuttable_qty=$po_finish_issue_qty_arr[$row[csf('po_id')]]/$tot_cad_practical_qty*12;echo $cuttable_qty; ?>&nbsp;</td>
                            <td width="80" align="right"><? echo $row[csf('cutting_qnty')]; ?>&nbsp;</td>
                            <td width="80" align="right"><? echo $row[csf('sewing_in_qnty')]; ?>&nbsp;</td>
                        
                            <td width="80" align="right"><? echo $production_data_arr[$row[csf("po_id")]]["sewing_out_qnty"]; ?>&nbsp;</td>
                            <td width="80" align="right"><? echo $exfactory_qnty; ?>&nbsp;</td>
                            <td width="80" align="right"><? echo number_format(((100*$exfactory_qnty)/$plun_cut_qnty),0)."%"; ?>&nbsp;</td>
                            <td width="80" align="right"><? echo number_format(((100*$exfactory_qnty)/$po_qnty),0)."%"; ?>&nbsp;</td>
                            
                        </tr>
                    <?
                        $i++;
						$total_fabric_received+=$po_finish_qty_arr[$row[csf('po_id')]];
						$total_fabric_issued+=$po_finish_issue_qty_arr[$row[csf('po_id')]];
						$total_booking_qty+=$po_booking_qty_arr[$row[csf('po_id')]];
						$tot_cut_qnty+=$row[csf('cutting_qnty')]; 
						$tot_sew_qnty+=$row[csf('sewing_in_qnty')]; 
						$tot_sewing_out_qnty+=$production_data_arr[$row[csf("po_id")]]["sewing_out_qnty"]; 
						$tot_cuttable_qty+=$cuttable_qty; 
						$tot_in_hand_qty+=$po_finish_qty_arr[$row[csf('po_id')]]-$po_finish_issue_qty_arr[$row[csf('po_id')]]; 
                    }
					unset($result);
                ?>
                </tbody>
                <tfoot>
                    <th align="right" colspan="4">Total</th>					
                    <th><? echo $tot_po_qnty; ?>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th><?php echo $total_fabric_received; ?></th>
					 <th><?php //echo $total_fabric_received; ?></th>
					  <th><?php echo $tot_in_hand_qty; ?></th>
					  
                    <th><?php echo number_format((($total_booking_qty*12)/$tot_po_qnty),2); ?></th>
                    <th><?php echo number_format((($total_fabric_issued*12)/$tot_po_qnty),2); ?></th>
                    <th><?php echo $tot_cuttable_qty; ?></th>
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
			$second_table=300;
			if(count($po_defect_data[1])>0) $second_table+=(count($po_defect_data[1])*50)+100;
			if(count($po_defect_data[5])>0) $second_table+=(count($po_defect_data[5])*50)+100;
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
							 <th colspan="<?php echo count($po_defect_data[1])+1; ?>" width="">Cut Panel Rejection</th>
						<?php
						}
						
						if(count($po_defect_data[5])>0)
						{
						?>
							 <th colspan="<?php echo count($po_defect_data[5])+1; ?>" width="">Sewing Line Rejection</th>
						<?php
						}
						if(count($po_defect_data[8])>0)
						{
						?>
							 <th colspan="<?php echo count($po_defect_data[8])+1; ?>" width="">Finishing Rejection</th>
						<?php
						}
						?>
                        <th rowspan="2" width="100">FG Sample</th>
						<th rowspan="2" width="100">FG In Hand (Leftover)</th>
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
						$unshiped_good=$po_all_reject_qty;//$fg_inhand+$total_defect_data[1]['total']+$total_defect_data[5]['total']+$total_defect_data[8]['total'];
						$variance_pcs=$tot_cut_qnty-($unshiped_good+$fg_inhand+$tot_exfactory_qnty);
					?>
                       
                           	 <td width="100" align="right"><?php echo $tot_sample_del_qty;?></td>
							 <td width="100" align="right"><?php echo $fg_inhand;?></td>
                            <td width="100" align="right" title="All Reject Qty of Production"><?php echo $unshiped_good;?></td>
                            <td width="" align="right" title="Tot Actual Cut Qty-Unshipped-FG_inhand-Ship Out Qty"><?php echo $variance_pcs;?></td>
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

disconnect($con);
?>
