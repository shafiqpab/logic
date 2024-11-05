<?
session_start();
include('../../includes/common.php');
require_once('../../includes/common.php');
require_once('../../includes/class3/class.conditions.php');
require_once('../../includes/class3/class.reports.php');
require_once('../../includes/class3/class.fabrics.php');


$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//------------------------------------------------------------------------------------------------------


if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond order by buyer_name","id,buyer_name", 1, "- All Buyer -", $selected, "" );     	 
	exit();
}
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$yarn_count_library=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count"  );
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );

if ($action=="batch_plan_no_search_popup")
{
  	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	extract($_REQUEST);

?>
     
	<script>
	
	function js_set_value(type,save_data,fab_des)
	{
		//alert(save_data);return;
		document.getElementById('txt_save_data').value=save_data;
		document.getElementById('txt_fab_desc').value=fab_des;
		parent.emailwindow.hide();
	}

	
    </script>

</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="1000" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="left">
    	<tr>
        	<td align="center" width="100%">
            	<table  cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>                	 
                        <th width="150" class="must_entry_caption">Company Name</th>
                        <th width="130">Buyer Name</th>
                        <th width="70">Year</th>
                        <th width="80">Job No</th>
                        <th width="80">Order No</th>
                        <th width="200" class="must_entry_caption">Ship Date</th>
                        <th>&nbsp;</th>
                    </thead>
        			<tr>
                    	<td> <input type="hidden" id="txt_save_data">
                        		<input type="hidden" id="txt_fab_desc">
							<?
								echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name",'id,company_name', 1,"--- Select Company ---",'' ,"load_drop_down( 'batch_fixing_no_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" ); 
							?>
                    	</td>
                        <td id="buyer_td">
                         	<?
								echo create_drop_down( "cbo_buyer_name", 130, $blank_array,'', 1, '--- Select Location ---', 0, ""  );
                        	?>	
                        </td>
                        <td id="">
                        	<? 
								 //date("Y",time())
								 echo create_drop_down( "cbo_year", 65, create_year_array(),"", 1,"-- All --", 0, "",0,"" );
							?>
					 	</td> 
                        <td>
                        	<input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:80px" />
					 	</td>
                        <td>
                        	<input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:80px" />
					 	</td>
                         <td>
                        	 <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" > &nbsp;To  <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"  placeholder="To Date" >
					 	</td>
            		 <td align="center">
                     <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_year').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_order_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_batch_plan_search_list_view', 'search_div', 'batch_fixing_no_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" /></td>
        		</tr>
             </table>
          </td>
        </tr>
         
        <tr>
            <td align="center">
                <? echo load_month_buttons(1); ?>
            </td>
        </tr>
            
        
    </table> 
    <div id="search_div">
    
    </div>   
     
    </form>
   </div>
</body> 
       
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}


if($action=="create_batch_plan_search_list_view")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
		
	$data=explode('_',$data);
	$company_id=$data[0];
	$buyer_id=$data[1];
	
	$cbo_year=$data[2];
	$job_no=$data[3];
	$order_no=$data[4];
	$from_date=$data[5];
	$to_date=$data[6];
	/*if($job_no=='' || $order_no=='')
	{
		if ($company_id==0) 
		{ echo "Please Select Company First."; die;
		}
		if($from_date=="" || $to_date=="")
		{
				echo "Select  Date Range";die;	
		}
	}*/
	if($from_date=="" || $to_date=="")
		{
			echo "Select  Date Range";die;	
		}
		if ($company_id==0) 
		{ echo "Please Select Company First."; die;
		}
	
	//print_r($data);die;
	/*if ($data[0]!=0) $company=" and company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer_id=" and buyer_id='$data[1]'"; else { $buyer_id="";}
	if ($data[2]!=0) $year=" and floor_id='$data[2]'"; else { $year="";}
	if ($data[3]!=0) $job_cond=" and category_id='$data[3]'"; else { $job_cond="";}
	if ($order_no!="") $order_cond=" and category_id='$data[4]'"; else { $order_cond="";}*/
	
	
	$sql_fab="select id,fabric_description as des,gsm_weight from  wo_pre_cost_fabric_cost_dtls where status_active=1 and is_deleted=0";
		$data_fab=sql_select($sql_fab);
		$fabric_desc_library=array();$fabric_gsm_library=array();
		foreach($data_fab as $row)
		{
			$fabric_desc_library[$row[csf('id')]]=$row[csf('des')];
			$fabric_gsm_library[$row[csf('id')]]=$row[csf('gsm_weight')];
		}

		$company_name= str_replace("'","",$company_id);
		if(str_replace("'","",$buyer_id)==0)
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
			$buyer_id_cond=" and a.buyer_name in (".str_replace("'","",$buyer_id).")";
		}
		
		//if(str_replace("'","",$txt_order)!="") $order_cond=" and b.id in(".str_replace("'","",$txt_order_no).")"; else $order_cond="";
		if($order_no!="") $order_cond=" and b.po_number='$order_no'"; else $order_cond="";
		if($job_no!="") $job_cond=" and a.job_no_prefix_num=".$job_no.""; else $job_cond="";
		$date_cond="";
		if(str_replace("'","",$from_date)!="" && str_replace("'","",$to_date)!="")
		{
		 if($db_type==0)
			{
				
				$start_date=change_date_format(str_replace("'","",$from_date),"yyyy-mm-dd","");
				$end_date=change_date_format(str_replace("'","",$to_date),"yyyy-mm-dd","");
				$year_field="SUBSTRING_INDEX(a.insert_date, '-', 1) as year"; 
				if($cbo_year!=0) $year_cond=" and year(a.insert_date)=$cbo_year"; else $year_cond="";
			}
			else if($db_type==2)
			{
				
				$start_date=change_date_format(str_replace("'","",$from_date),"","",1);
				$end_date=change_date_format(str_replace("'","",$to_date),"","",1);
				$year_field="to_char(a.insert_date,'YYYY') as year";
				if($cbo_year!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year"; else $year_cond="";	
			}
		$date_cond=" and b.pub_shipment_date between '$start_date' and '$end_date'";
		}
	
		ob_start();
		 
		 $sql_pro=sql_select("select id,item_description as item_desc,gsm,dia_width from product_details_master where status_active=1 and is_deleted=0");
		 $prod_arr=array();
		 foreach($sql_pro as $row)
		 {
			$prod_arr[$row[csf('id')]]['desc']=$row[csf('item_desc')];
			$prod_arr[$row[csf('id')]]['gsm']=$row[csf('gsm')];
			$prod_arr[$row[csf('id')]]['dia']=$row[csf('dia_width')];
		 }
		// print_r($prod_arr);
		$poDataArray=sql_select("select b.id,b.pub_shipment_date, b.po_number,a.buyer_name,a.job_no_prefix_num,a.style_ref_no as style from  wo_po_break_down b,wo_po_details_master a where  a.job_no=b.job_no_mst and a.company_name=$company_name and b.status_active=1 and b.is_deleted=0 $buyer_id_cond  $order_cond $job_cond $date_cond $year_cond");// and a.season like '$txt_season'
		//$po_array=array(); 
		$all_po_id='';
		$job_array=array(); 
		foreach($poDataArray as $row)
		{
		$job_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
		$job_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
		$job_array[$row[csf('id')]]['po']=$row[csf('po_number')];
		$job_array[$row[csf('id')]]['ship_date']=$row[csf('pub_shipment_date')];
		if($all_po_id=="") $all_po_id=$row[csf('id')]; else $all_po_id.=",".$row[csf('id')]; 
		} //echo $all_po_id;die;
		$po_ids=count(array_unique(explode(",",$all_po_id)));
		//echo $all_po_id;
		$all_po_ids=chop($all_po_id,','); $poIds_cond="";
		//print_r($all_po_ids);
		if($db_type==2 && $po_ids>990)
		{
			$poIds_cond=" and (";
			$poIdsArr=array_chunk(explode(",",$all_po_ids),990);
			//print_r($gate_outIds);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$poIds_cond.=" c.po_breakdown_id  in($ids) or ";
			}
			$poIds_cond=chop($poIds_cond,'or ');
			$poIds_cond.=")";
		}
		else
		{
			$poIds_cond=" and  c.po_breakdown_id  in($all_po_id)";
		}
		$sql_plan="select c.company_id,c.buyer_id,c.po_breakdown_id as po_id,c.ship_date,c.fabric_description as fab_des,c.color_id,c.gsm,c.dia_width,c.no_of_batch  from pro_batch_plan c  where  c.company_id=$company_name and c.status_active=1 and c.is_deleted=0 $poIds_cond";
		$plan_data=sql_select($sql_plan);
		$batch_plan_arr=array();
		 foreach($plan_data as $row)
		 {
			$batch_plan_arr[$row[csf('po_id')]][$row[csf('fab_des')]][$row[csf('color_id')]][$row[csf('gsm')]][$row[csf('dia_width')]]['batch']+=$row[csf('no_of_batch')];	
		 }
		 $sql_issue_data = "select  c.po_breakdown_id as po_id, d.id as prod_id,c.color_id,d.dia_width,d.gsm,
		 sum(CASE WHEN a.issue_basis=2 THEN c.quantity ELSE 0 END) AS indep_qty,
		 sum(CASE WHEN a.issue_basis!=2 THEN c.quantity ELSE 0 END) AS issue_qty
		 from inv_issue_master a, inv_grey_fabric_issue_dtls b,order_wise_pro_details c, product_details_master d 
		 where a.id=b.mst_id and b.prod_id=d.id and b.id=c.dtls_id  and a.entry_form=16 and c.entry_form=16 and a.company_id=$company_name $buyer_id_cond $poIds_cond and a.issue_basis=2 and a.is_deleted=0 and a.status_active=1  and b.is_deleted=0 and b.status_active=1  and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 group by c.po_breakdown_id, d.id,c.color_id,d.dia_width,d.gsm";
			$result_data=sql_select($sql_issue_data);
			$issue_qty_arr_qty=array();
			foreach($result_data as $row)
			{
				$issue_qty_arr_qty[$row[csf('po_id')]][$row[csf('prod_id')]][$row[csf('color_id')]][$row[csf('gsm')]][$row[csf('dia_width')]]['indep_qty']=$row[csf('indep_qty')];	
				$issue_qty_arr_qty[$row[csf('po_id')]][$row[csf('prod_id')]][$row[csf('color_id')]][$row[csf('gsm')]][$row[csf('dia_width')]]['qty']=$row[csf('issue_qty')];
			}
				if($db_type==0) $grp_concat="group_concat(a.challan_no) AS challan_no,";
				else if($db_type==2) $grp_concat="listagg(cast(a.challan_no as varchar2(4000)),',') within group (order by a.challan_no) AS challan_no,";
				 $sql_dtls="select $grp_concat a.knitting_company,c.po_breakdown_id as po_id,sum(c.quantity) as fab_recev,b.prod_id, b.color_id, b.gsm, b.width,b.remarks from inv_receive_master a,pro_finish_fabric_rcv_dtls b,order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.knitting_source=1 and c.entry_form=7 $poIds_cond  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.po_breakdown_id,b.prod_id, b.batch_id, b.color_id, b.gsm, b.width,a.knitting_company,b.remarks ";
				$res_data=sql_select($sql_dtls);
				$fin_recv_data_arr_qty=array();
				foreach($res_data as $row)
				{
					 $fin_recv_data_arr_qty[$row[csf('po_id')]][$row[csf('prod_id')]][$row[csf('color_id')]][$row[csf('gsm')]][$row[csf('width')]]['recv_qty']=$row[csf('fab_recev')];	
					 $fin_recv_data_arr_qty[$row[csf('po_id')]][$row[csf('prod_id')]][$row[csf('color_id')]][$row[csf('gsm')]][$row[csf('width')]]['recv_challan']=$row[csf('challan_no')];
					  $fin_recv_data_arr_qty[$row[csf('po_id')]][$row[csf('prod_id')]][$row[csf('color_id')]][$row[csf('gsm')]][$row[csf('width')]]['dye_factory']=$row[csf('knitting_company')];
					  $fin_recv_data_arr_qty[$row[csf('po_id')]][$row[csf('prod_id')]][$row[csf('color_id')]][$row[csf('gsm')]][$row[csf('width')]]['remarks']=$row[csf('remarks')];	
				}
			
			//print_r($fin_recv_data_arr_qty);
			   $sql_po="(select 1 as type,b.id as po_id,sum(c.cons) as cons,c.color_number_id,c.dia_width,c.pre_cost_fabric_cost_dtls_id as fab_dtls_id   from wo_po_details_master a, wo_po_break_down b 
					LEFT JOIN wo_pre_cos_fab_co_avg_con_dtls c on  c.po_break_down_id=b.id where a.job_no=b.job_no_mst   and a.company_name=$company_name $buyer_id_cond  $order_cond $job_cond $date_cond  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by b.id,c.color_number_id,c.dia_width,c.pre_cost_fabric_cost_dtls_id) 
union
(select  2 as type,c.po_breakdown_id as po_id,sum(c.quantity) as cons,c.color_id as color_number_id, d.dia_width as dia_width,d.id as fab_dtls_id 
			from inv_issue_master a, inv_grey_fabric_issue_dtls b,order_wise_pro_details c, product_details_master d 
			where a.id=b.mst_id and b.prod_id=d.id and b.id=c.dtls_id  and a.entry_form=16 and c.entry_form=16 and a.company_id=$company_name $buyer_id_cond $poIds_cond and a.issue_basis=2 and a.is_deleted=0 and a.status_active=1  and b.is_deleted=0 and b.status_active=1  and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 group by 	 c.po_breakdown_id,c.color_id,d.dia_width,d.id  ) order by po_id";
		$sql_data=sql_select($sql_po);
		?>
        <div style="width:1750px;">
        <fieldset style="width:1750px;">
 	<table width="1750" cellspacing="0" cellpadding="0" border="0" rules="all" >
            <tr class="form_caption">
                <td colspan="19" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
            </tr>
            <tr class="form_caption">
                <td colspan="19" align="center">
                <strong>
				<? 
				if(str_replace("'","",$start_date)!="" && str_replace("'","",$end_date)!="")
				{
					echo "From ".$start_date." To ".$end_date;
				}
				?>
                </strong>
                </td>
            </tr>
            <tr class="form_caption">
                <td colspan="19" align="center"><? echo $company_library[$company_name]; ?></td>
            </tr>
    </table>
      <table  class="rpt_table" width="1750" cellpadding="0" cellspacing="0" border="1" rules="all">
        <thead>
            <th width="30">SL</th>
            <th width="80">Ship Date</th>
            <th width="110">Buyer</th>
            <th width="110">Order No</th>
            <th width="150">Fabrics Desc.</th>
            <th width="100">Fabrics Color</th>
            <th width="70">F/ GSM</th>
            <th width="70">F/ Dia</th>
            <th width="70">Fab.  Req. Qty</th>
            <th width="70"><p>Grey Fab.  Del</p></th>
            <th width="90"><p>Grey Fab.  Balance</p></th>
            <th width="80">Fin Fab. Req. Qty</th>
            <th width="80"><p>Inhouse Qty</p></th>
            <th width="80">Inhouse Balance</th>
            <th width="80">Dyeing Process Loss %</th>
            <th width="80">Receive Challan No</th>
            <th width="100">Dye Factory</th>
            <th width="80">InHouse Status</th>
            <th width="100">Remarks</th>
            <th width="">Batch</th>
        </thead>
    </table>
       <div style="width:1750px; max-height:450px; overflow-y:scroll" id="scroll_body">
       <table class="rpt_table" width="1730" cellpadding="0" cellspacing="0" border="1" rules="all" id="list_view">
        <?
         $total_grey_issue_qty=0;  $total_fab_qty=0;
         $i=1; 
		 $condition= new condition();
		if(str_replace("'","",$job_no) !='')
		{
		  $condition->job_no_prefix_num("=$job_no");
		}
		 if(str_replace("'","",$order_no)!='')
		 {
			$condition->po_number("='$order_no'"); 
		 }
		 if(str_replace("'","",$from_date)!="" && str_replace("'","",$to_date)!="")
		 {
			$condition->country_ship_date(" between '$start_date' and '$end_date'");
		 }
		 $condition->init();
		
		$fabric= new fabric($condition);
		// echo $fabric->getQuery(); die;
		$fabric_costing_arr=$fabric->getQtyArray_by_OrderFabriccostidGmtscolorAndDiaWidth_knitAndwoven_greyAndfinish();
         foreach($sql_data as $row)
         {
		
		if($row[csf('type')]==1)
		{
			$fab_grey_knit_req=$fabric_costing_arr['knit']['grey'][$row[csf('po_id')]][$row[csf('fab_dtls_id')]][$row[csf('color_number_id')]][$row[csf('dia_width')]];
		$fab_grey_finish_req=$fabric_costing_arr['knit']['finish'][$row[csf('po_id')]][$row[csf('fab_dtls_id')]][$row[csf('color_number_id')]][$row[csf('dia_width')]];
		
			$fab_des=$fabric_desc_library[$row[csf('fab_dtls_id')]];
			$f_gsm=$fabric_gsm_library[$row[csf('fab_dtls_id')]];
			//$f_dia=$fabric_gsm_library[$row[csf('fab_dtls_id')]];	
				$grey_issue_qty=$issue_qty_arr_qty[$row[csf('po_id')]][$fab_des][$row[csf('color_number_id')]][$f_gsm][$row[csf('dia_width')]]['issue_qty'];
				$fin_recv_qty=$fin_recv_data_arr_qty[$row[csf('po_id')]][$fab_des][$row[csf('color_number_id')]][$f_gsm][$row[csf('dia_width')]]['recv_qty'];
				$recv_challan=$fin_recv_data_arr_qty[$row[csf('po_id')]][$fab_des][$row[csf('color_number_id')]][$f_gsm][$row[csf('dia_width')]]['recv_challan'];	
				
				$grey_balance=$fab_grey_knit_req-$grey_issue_qty;
				$inhouse_balance_qty=$fab_grey_finish_req-$fin_recv_qty;
				$dyeing_process_per=(($grey_issue_qty-$fin_recv_qty)/$grey_issue_qty)*100;
				$dyeing_factory=$company_library[$fin_recv_data_arr_qty[$row[csf('po_id')]][$fab_des][$row[csf('color_number_id')]][$f_gsm][$row[csf('dia_width')]]['dye_factory']];
				$remarks=$fin_recv_data_arr_qty[$row[csf('po_id')]][$fab_des][$row[csf('color_number_id')]][$f_gsm][$row[csf('dia_width')]]['remarks'];
				
				if($fab_grey_finish_req>$fin_recv_qty)
				{
				
				 	$dyeing_status=$fab_grey_finish_req-$fin_recv_qty;
				 	$dyeing_status='Due '.number_format($dyeing_status).' KG';	
				}
				else if($fab_grey_finish_req==$fin_recv_qty)
				{
					$dyeing_status='In House';	
				}
				else if($fab_grey_finish_req<$fin_recv_qty)
				{
					$dyeing_status=$fab_grey_finish_req-$fin_recv_qty;
					$dyeing_status='Over '.number_format($dyeing_status).' KG';	
				}	
		}
		else
		{
			 $fab_des=$prod_arr[$row[csf('fab_dtls_id')]]['desc'];
			$f_gsm=$prod_arr[$row[csf('fab_dtls_id')]]['gsm'];	
			$grey_issue_qty=$issue_qty_arr_qty[$row[csf('po_id')]][$row[csf('fab_dtls_id')]][$row[csf('color_number_id')]][$f_gsm][$row[csf('dia_width')]]['indep_qty'];
			$fin_recv_qty=$fin_recv_data_arr_qty[$row[csf('po_id')]][$row[csf('fab_dtls_id')]][$row[csf('color_number_id')]][$f_gsm][$row[csf('dia_width')]]['recv_qty'];
			$recv_challan=$fin_recv_data_arr_qty[$row[csf('po_id')]][$row[csf('fab_dtls_id')]][$row[csf('color_number_id')]][$f_gsm][$row[csf('dia_width')]]['recv_challan'];	
			$grey_balance=$fab_grey_knit_req-$grey_issue_qty;
			$inhouse_balance=$fab_grey_finish_req-$fin_recv_qty;
			$dyeing_process_per=(($grey_issue_qty-$fin_recv_qty)/$grey_issue_qty)*100;
			$dyeing_factory=$company_library[$fin_recv_data_arr_qty[$row[csf('po_id')]][$row[csf('fab_dtls_id')]][$row[csf('color_number_id')]][$f_gsm][$row[csf('dia_width')]]['dye_factory']];
			$remarks=$fin_recv_data_arr_qty[$row[csf('po_id')]][$row[csf('fab_dtls_id')]][$row[csf('color_number_id')]][$f_gsm][$row[csf('dia_width')]]['remarks'];
			if($fab_grey_finish_req>$fin_recv_qty)
			{
			
				$dyeing_status=$fab_grey_finish_req-$fin_recv_qty;	
				$dyeing_status='Due '.number_format($dyeing_status).' KG';
			}
			else if($fab_grey_finish_req==$fin_recv_qty)
			{
				$dyeing_status='In House';	
			}
			else if($fab_grey_finish_req<$fin_recv_qty)
			{
				$dyeing_status=$fab_grey_finish_req-$fin_recv_qty;	
				$dyeing_status='Over '.number_format($dyeing_status).' KG';	
			}		
		}
		$no_of_batch=$batch_plan_arr[$row[csf('po_id')]][$fab_des][$row[csf('color_number_id')]][$f_gsm][$row[csf('dia_width')]]['batch'];
		//echo $row[csf('po_id')];
		//print_r($po_data);
         if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		 //$job_array[$row[csf('po_id')]]['ship_date']
		 $save_data=$company_name.'**'.$job_array[$row[csf('po_id')]]['buyer'].'**'.$row[csf('po_id')].'**'.$job_array[$row[csf('po_id')]]['ship_date'].'**'.$row[csf('color_number_id')].'**'.$f_gsm.'**'.$row[csf('dia_width')].'**'.$recv_challan.'**'.$dyeing_factory;	//echo $save_data;$recv_challan
         ?>
         <tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('0','<? echo $save_data; ?>','<? echo $fab_des; ?>');"> 
            <td width="30"><? echo $i; ?></td>
            <td width="80"><div style="width:80px; word-wrap:break-word;"><? 
			echo change_date_format($job_array[$row[csf('po_id')]]['ship_date']); ?></div></td>
            <td width="110" ><div style="width:110px; word-wrap:break-word;"><?  echo $buyer_library[$job_array[$row[csf('po_id')]]['buyer']]; ?></div></td>
            <td width="110" title="<? echo $row[csf('job_no')];?>"><div style="width:110px; word-wrap:break-word;"><?  echo  $job_array[$row[csf('po_id')]]['po']; ?></div></td>
            <td width="150"><div style="width:150px; word-wrap:break-word;"><?  echo  $fab_des;  ?></div></td>
            <td width="100"><p><? echo  $color_library[$row[csf('color_number_id')]];  ?></p></td>
            <td width="70"><p><? echo  $f_gsm;  ?></p></td>
            <td width="70"><p><? echo  $row[csf('dia_width')];  ?></p></td>
            <td width="70" align="right"><p><? echo  number_format($fab_grey_knit_req,2); ?></p></td>
            <td width="70" align="right"><p><?  echo  number_format($grey_issue_qty,2); ?></p></td>
            <td width="90" align="right"><p><? echo  number_format($grey_balance,2);  ?></p></td>
            <td width="80" align="right"><p><? echo  number_format($fab_grey_finish_req,2);  ?></p></td>           
            <td width="80" align="right"><div style="width:80px; word-wrap:break-word;"><? echo  number_format($fin_recv_qty,2);  ?></div></td>
            <td width="80" align="right"><div style="width:80px; word-wrap:break-word;"><? echo  number_format($inhouse_balance,2);  ?></div></td>
            <td width="80" align="center"><div style="width:80px; word-wrap:break-word;"><? echo number_format($dyeing_process_per,2);  ?></div></td>
            <td width="80"><div style="width:80px; word-wrap:break-word;"><? echo  $recv_challan;  ?></div></td>
            <td width="100"><div style="width:100px; word-wrap:break-word;"><? echo   $dyeing_factory;  ?></div></td>
            <td width="80"><div style="width:80px; word-wrap:break-word;"><? echo  $dyeing_status;  ?></div></td>
            <td width="100"><div style="width:100px; word-wrap:break-word;"><? echo  $remarks;  ?></div></td>
            <td width="" align="right"><p><? echo  $no_of_batch;  ?> </p></td>
        </tr>
       <?
       $i++;
		
	   }
	   ?>
     
     </table>
    </div>
</fieldset>
</div>
<?
	exit();
} 


if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$savedata=str_replace("'","",$txt_save_data);
		$save_data=explode("**",$savedata);
		$company_id=$save_data[0];
		$buyer_id=$save_data[1];
		$order_id=$save_data[2];
		$ship_date=$save_data[3];
		$color_id=$save_data[4];
		$gsm=$save_data[5];
		$dia=$save_data[6];
		$challan=$save_data[7];
		$dye_factory=$save_data[8];
		
		
		if(is_duplicate_field( "po_breakdown_id", "pro_batch_plan", "po_breakdown_id=$order_id and fabric_description=$txt_descrp_no and color_id=$color_id and gsm=$gsm and dia_width='$dia' and company_id=$company_id  and buyer_id=$buyer_id and status_active=1 and is_deleted=0" )==1)
				{
					
					echo "11**0"; 
					disconnect($con);
					die;			
				}
		//print_r($save_data);die;
		$id=return_next_id( "id", "pro_batch_plan", 1 ) ;
		
		$field_array="id,company_id,buyer_id,po_breakdown_id,ship_date,fabric_description,color_id,gsm,dia_width,no_of_batch,inserted_by,insert_date,status_active,is_deleted";
		$data_array="(".$id.",".$company_id.",".$buyer_id.",".$order_id.",'".$ship_date."',".$txt_descrp_no.",".$color_id.",".$gsm.",'".$dia."',".$txt_no_of_batch.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		
		//echo "INSERT INTO pro_batch_plan (".$field_array.") VALUES ".$data_array;die;
		
 		$rID=sql_insert("pro_batch_plan",$field_array,$data_array,0);
		if($rID) $flag=1; else $flag=0;

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".$id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$id;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
		if($flag==1)
			{
				oci_commit($con);
				echo "0**".$id;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$id;
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
		$update_id=str_replace("'","",$txt_update_id);
		$savedata=str_replace("'","",$txt_save_data);
		$save_data=explode("**",$savedata);
		$company_id=$save_data[0];
		$buyer_id=$save_data[1];
		$order_id=$save_data[2];
		$ship_date=$save_data[3];
		$color_id=$save_data[4];
		$gsm=$save_data[5];
		$dia=$save_data[6];
		$challan=$save_data[7];
		$dye_factory=$save_data[8];
		//echo "select po_breakdown_id from pro_batch_plan where  po_breakdown_id=$order_id and fabric_description='$txt_descrp_no' and color_id=$color_id and gsm=$gsm and dia_width='$dia' and company_id=$company_id  and buyer_id=$buyer_id and status_active=1 and is_deleted=0";
		
		
		$field_array_up="company_id*buyer_id*po_breakdown_id*ship_date*fabric_description*color_id*gsm*dia_width*no_of_batch*updated_by*update_date";
		$data_array_up="".$company_id."*".$buyer_id."*".$order_id."*'".$ship_date."'*".$txt_descrp_no."*".$color_id."*".$gsm."*'".$dia."'*".$txt_no_of_batch."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		//echo $data_array; die;
		$rID=sql_update("pro_batch_plan",$field_array_up,$data_array_up,"id","".$update_id."",1);
		if($rID) $flag=1; else $flag=0;

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".$update_id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$update_id;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".$update_id;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$update_id;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		$update_id=str_replace("'","",$txt_update_id);
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		//echo $txt_mst_id;
		$rID=sql_delete("pro_batch_plan",$field_array,$data_array,"id","".$update_id."",1);
		//echo "2**".$rID;
		if($db_type==0)
		{	
			echo "2**".$update_id;
		}
		if($db_type==2)
		{	oci_commit($con);
			echo "2**".$update_id;
		}
		else
		{
			oci_rollback($con);
			echo "2**".$update_id;	
		}
		disconnect($con);die;
	}
}
function sql_update2($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues,$commit)
{
	
	$strQuery = "UPDATE ".$strTable." SET ";
	$arrUpdateFields=explode("*",$arrUpdateFields);
	$arrUpdateValues=explode("*",$arrUpdateValues);	
	
	if(is_array($arrUpdateFields))
	{
		$arrayUpdate = array_combine($arrUpdateFields,$arrUpdateValues);
		$Arraysize = count($arrayUpdate);
		$i = 1;
		foreach($arrayUpdate as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value.", ":$key."=".$value." WHERE ";
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrUpdateFields."=".$arrUpdateValues." WHERE ";
	}
	$arrRefFields=explode("*",$arrRefFields);
	$arrRefValues=explode("*",$arrRefValues);	
	if(is_array($arrRefFields))
	{
		$arrayRef = array_combine($arrRefFields,$arrRefValues);
		$Arraysize = count($arrayRef);
		$i = 1;
		foreach($arrayRef as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value." AND ":$key."=".$value."";
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrRefFields."=".$arrRefValues."";
	}
	
	global $con;
	echo $strQuery; die;
	 //return $strQuery; die;
	$stid =  oci_parse($con, $strQuery);
	$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
	if ($exestd) 
		return "1";
	else 
		return "0";
	
	die;
	if ( $commit==1 )
	{
		if (!oci_error($stid))
		{
			oci_commit($con); 
			return "1";
		}
		else
		{
			oci_rollback($con);
			return "10";
		}
	}
	else
		return 1;
	die;
}
if($action=="show_active_listview")
{
	$arr=array(6=>$cause_type); 
	 
 	$sql= "select from_date,from_hour,from_minute,to_date,to_hour,to_minute,machine_idle_cause,remarks,id from pro_cause_of_machine_idle where status_active=1 and is_deleted=0 and machine_entry_tbl_id='$data' order by id"; 
	 
	echo  create_list_view("list_view", "From Date,From Hour,From Minute,To Date,To Hour,To Minute,Cause,Remarks", "80,50,50,80,50,50,200,300","900","220",0, $sql , "get_php_form_data", "id", "'populate_machine_details_form_data'", 1, "0,0,0,0,0,0,machine_idle_cause,0", $arr , "from_date,from_hour,from_minute,to_date,to_hour,to_minute,machine_idle_cause,remarks", "requires/cause_of_machine_idle_controller",'','3,0,0,3,0,0,0,0') ;
}

if($action=="populate_machine_details_form_data")
{
	$data_array=sql_select("select from_date, from_hour, from_minute, to_date, to_hour, to_minute, machine_idle_cause, remarks, id from  pro_cause_of_machine_idle  where id =$data");
	foreach ($data_array as $row)
	{
		echo "document.getElementById('txt_from_date').value = '".change_date_format($row[csf("from_date")], "dd-mm-yyyy", "-")."';\n";
		/*if($row[csf('from_hour')]>12)
		{
			$hour_from = $row[csf('from_hour')]-12;  $time_from=2;
 		}
		else if($row[csf('from_hour')]==12)
		{
			$hour_from = "12";  $time_from=2;
		}
		else if($row[csf('from_hour')]==0)
		{
			$hour_from = "12";  $time_from=1;
		}
		else
		{
			$hour_from = $row[csf('from_hour')]; $time_from=1;
		}*/
		echo "document.getElementById('txt_from_hour').value = '".$row[csf('from_hour')]."';\n";
		echo "document.getElementById('txt_from_minute').value = '".$row[csf("from_minute")]."';\n";
		//echo "document.getElementById('cbo_time_from').value = '".$time_from."';\n";
		echo "document.getElementById('txt_to_date').value = '".change_date_format($row[csf("to_date")], "dd-mm-yyyy", "-")."';\n"; 
		/*if($row[csf('to_hour')]>12)
		{
			$hour_to = $row[csf('to_hour')]-12;  $time_to=2;
 		}
		else if($row[csf('to_hour')]==12)
		{
			$hour_to = "12";  $time_to=2;
		}
		else if($row[csf('to_hour')]==0)
		{
			$hour_to = "12";  $time_to=1;
		}
		else
		{
			$hour_to = $row[csf('to_hour')]; $time_to=1;
		}*/
		
	    echo "document.getElementById('txt_to_hour').value = '".$row[csf('to_hour')]."';\n"; 
		echo "document.getElementById('txt_to_minute').value = '".$row[csf("to_minute")]."';\n";
		//echo "document.getElementById('cbo_time_to').value = '".$time_to."';\n"; 
		echo "document.getElementById('txt_cause_of_machine_idle').value = '".$row[csf("machine_idle_cause")]."';\n"; 
		echo "document.getElementById('txt_remark').value = '".$row[csf("remarks")]."';\n"; 
		echo "document.getElementById('txt_mst_id').value = '".$row[csf("id")]."';\n";  
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_cause_of_machine_idle_entry',1);\n"; 
     }
	 exit();
}

?>
