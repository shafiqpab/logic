<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="varible_setting_wvn_style_wise")
{
	$sql_variable_inv_wvn_style_wise=sql_select("select id, user_given_code_status  from variable_settings_inventory where company_name=$data and variable_list=34 and status_active=1 and is_deleted=0 and item_category_id = 3");

 	echo $sql_variable_inv_wvn_style_wise[0][csf("user_given_code_status")];
	//if(count($sql_variable_inv_wvn_style_wise)>0){echo 1;}else{echo 0;}
	die;
}

if($action=="layPlan_popup")
{
  	echo load_html_head_contents("Cut and Lay Info","../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
		<script>
			function js_set_cutting_value(strCon ) 
			{
				var data=strCon.split("_");
				document.getElementById('hidden_lay_plan_id').value=data[0];
				document.getElementById('hidden_layPlan_no').value=data[1];
				parent.emailwindow.hide();
			}
	    </script>
	</head>
	<body>
	<div align="center" style="width:100%; overflow-y:hidden;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
			<table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
	            <thead>
	                <tr>                	 
	                    <th>Cutting No</th>
	                    <th>Job No</th>
	                    <th>Order No</th>
	                    <th>Plan Start Date Range</th>
	                    <th>
	                    	<input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  />
	                        <input type="hidden" id="hidden_lay_plan_id" name="hidden_lay_plan_id" />
	                        <input type="hidden" id="hidden_layPlan_no" name="hidden_layPlan_no" />
	                    </th>           
	                </tr>
	            </thead>
	            <tbody>
					<tr class="general">                    
	                    <td>
	                    	<input type="text" id="txt_cut_no" name="txt_cut_no" style="width:120px"  class="text_boxes"/>
	                    </td>
	                    <td>
	                    	<input name="txt_job_search" id="txt_job_search" class="text_boxes" style="width:120px"  />
	                    </td>
	                    <td>
	                    	<input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:120px"  />
	                    </td>
	                    <td width="250">
	                       <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:100px" placeholder="From Date" />
	                       <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:100px" placeholder="To Date" />
	                    </td>
	                    <td>
	                           <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( <? echo $company_id; ?>+'_'+document.getElementById('txt_cut_no').value+'_'+document.getElementById('txt_job_search').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_order_search').value, 'create_cutting_search_list_view', 'search_div', 'woven_fabric_requisition_for_cutting_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
	                    </td>
	                 </tr>
	        		 <tr>                  
	                    <td align="center" valign="middle" colspan="5">
	                        <? echo load_month_buttons(1);  ?>
	                    </td>
	                </tr>   
	            </tbody>
	         </tr>         
	      </table> 
	     <div align="center" valign="top" style="margin-top:5px" id="search_div"> </div>  
	  </form>
	</div>    
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="create_cutting_search_list_view")
{
    $ex_data = explode("_",$data);
	$company = $ex_data[0];	
	$cutting_no = $ex_data[1];
	$job_no = $ex_data[2];
	$from_date = $ex_data[3];
	$to_date = $ex_data[4];
	$cut_year= $ex_data[5];
	$order_no= $ex_data[6];

    if($db_type==2) 
	{
		 $year_cond=" and TO_CHAR(a.insert_date,'YYYY')=$cut_year"; 
		 $year=" TO_CHAR(a.insert_date,'YYYY') as year ";
	}
    else if($db_type==0) 
	{ 
		$year_cond=" and year(a.insert_date)=$cut_year"; 
		$year=" year(a.insert_date) as year ";
	}
	
	if(str_replace("'","",$company)==0) $conpany_cond=""; else $conpany_cond="and a.company_id=".str_replace("'","",$company)."";
	if(str_replace("'","",$cutting_no)=="") $cut_cond=""; else $cut_cond="and a.cut_num_prefix_no='".str_replace("'","",$cutting_no)."'  $year_cond";
	if(str_replace("'","",$job_no)=="") $job_cond=""; else $job_cond="and b.job_no_prefix_num='".str_replace("'","",$job_no)."'";
	if(str_replace("'","",$order_no)=="") $order_cond=""; else $order_cond="and c.po_number like '%".trim($order_no)."%' ";
	
	if($from_date!="" && $to_date!="" )
	{
		if($db_type==0)
		{
			$sql_cond= " and entry_date between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
		}
		else if($db_type==2)
		{
			$sql_cond= " and entry_date between '".change_date_format($from_date,'yyyy-mm-dd','-',1)."' and '".change_date_format($to_date,'yyyy-mm-dd','-',1)."'";
		}
	}
	
	$table_no_arr=return_library_array("select id,table_no from lib_cutting_table",'id','table_no');

	if($db_type==0) $order_no="group_concat(c.po_number) as order_no"; else $order_no="LISTAGG(cast(c.po_number as varchar2(4000)), ',') WITHIN GROUP (ORDER BY c.id) as order_no";
	$sql_order="select a.id, a.cutting_no, a.cut_num_prefix_no, a.table_no, a.job_no, a.entry_date, $order_no, $year FROM ppl_cut_lay_mst a, wo_po_details_master b, wo_po_break_down c, ppl_cut_lay_dtls d where a.id=d.mst_id and a.job_no=b.job_no and b.job_no=c.job_no_mst $conpany_cond $cut_cond $job_cond $sql_cond $order_cond group by a.id, a.cutting_no, a.cut_num_prefix_no, a.table_no, a.job_no, a.entry_date, a.insert_date order by a.id DESC";
	//echo  $sql_order;
	$result=sql_select($sql_order);  
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="740">
        <thead>
            <th width="40">SL</th>
            <th width="90">Cut No</th>
            <th width="70">Year</th>
            <th width="80">Table No</th>
            <th width="120">Job No</th>
            <th width="200">Order No</th>
            <th>Entry Date</th>
        </thead>
    </table>
    <div style="width:740px; max-height:200px; overflow-y:scroll" id="list_container" align="left"> 
        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="720" id="list_view">  
            <? 
            $i=1; 
            foreach($result as $row)
            {
                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
             ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer" onClick="js_set_cutting_value('<? echo $row[csf('id')]."_".$row[csf('cutting_no')]; ?>');">
                	<td width="40"><? echo $i; ?></td>
                    <td width="85" style="padding-left:5px"><? echo $row[csf('cut_num_prefix_no')]; ?></td>
                    <td width="70" align="center"><? echo $row[csf('year')]; ?></td>
                    <td width="78" style="padding-left:2px"><? echo $table_no_arr[$row[csf('table_no')]]; ?>&nbsp;</td>
                    <td width="120" align="center"><? echo $row[csf('job_no')]; ?></td>
                    <td width="200"><p><? echo implode(",",array_unique(explode(",",$row[csf('order_no')]))); ?><p></td>
                    <td align="center"><? echo change_date_format($row[csf('entry_date')]); ?>&nbsp;</td>
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


if ($action=="po_popup")
{
	echo load_html_head_contents("Fabric Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	?> 
		<script>
			
			function toggle( x, origColor ) {
				var newColor = 'yellow';
				if ( x.style ) {
					x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
				}
			}
			
			
			function check_all_data(is_checked)
			{ 
				var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
				tbl_row_count = tbl_row_count - 1;

				for( var i = 1; i <= tbl_row_count; i++ ) {
					var budgQnty=$('#trBudgQnty'+ i).val();
					if(budgQnty<=0)
					{
						alert('Budget Qnty Not Available');return;
					}
					
					if($("#search"+i).css("display") !='none'){
						if(is_checked==true)
						{
							document.getElementById( 'search' + i ).style.backgroundColor='yellow';
						}
						else
						{
							document.getElementById( 'search' + i ).style.backgroundColor='#FFFFCC';	
						}
					}
				}
			}
						
			function js_set_value( str,buyer_name,budget_qty) 
			{
				
				if (budget_qty<=0)
				{
					alert('Budget Qnty Not Available');return;
				}
				if ( document.getElementById('hidden_buyer').value!="" && document.getElementById('hidden_buyer').value!=buyer_name )
				{
					alert('Buyer Mix Not Allowed');return;
				}
				toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
				document.getElementById('hidden_buyer').value=buyer_name;				
			}
			
			function reset_hide_field()
			{
				$('#hidden_data').val( '' );
			}
			
			function fnc_close()
			{
				var hidden_data='';
				
				$("#tbl_list_search").find('tr:not(:first)').each(function()
				{
					var tr_id = $(this).attr("id");
					//var bgColor=$(this).css("background-color");bg
					var bgColor=document.getElementById(tr_id).style.backgroundColor;
					if(bgColor=='yellow')
					{
						var trData=$(this).find('input[name="trData[]"]').val();
						if(hidden_data=="")
						{
							hidden_data=trData;
						}
						else
						{
							hidden_data+="_"+trData;
						}
					}
				});
				
				$('#hidden_data').val( hidden_data );
				parent.emailwindow.hide();
			}

			function checkValidation(){
				job = document.getElementById('txt_job_no').value;
				order = document.getElementById('txt_order_no').value;
				buyer = document.getElementById('cbo_buyer_name').value;
				intRef = document.getElementById('txt_internal_ref_no').value;
				styleRef = document.getElementById('txt_style_ref').value;
				// alert(styleRef) ;
				// batch = document.getElementById('txt_batch_no').value;
				// job_no = elem.value;
				if(job != "" || order != "" || buyer != 0 || intRef != "" || styleRef != "")
				{
					show_list_view ( <? echo $company_id; ?>+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_year').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_order_no').value+'_'+document.getElementById('txt_internal_ref_no').value+'_'+document.getElementById('txt_style_ref').value+'_'+<? echo $hdn_variable_setting_status; ?>, 'create_fabric_search_list_view', 'search_div', 'woven_fabric_requisition_for_cutting_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();');
				}
				else
				{
					alert('Please fillup anyone field for search.');
					// elem.focus();
					// elem.style.background="red";
				}
			}
	    </script>

	</head>

	<body>
	<div align="center" style="width:1190px;">
		<form name="searchwofrm" id="searchwofrm" autocomplete=off>
			<fieldset style="width:1180px; margin-left:2px">
			<legend>Enter search words</legend>           
	            <table cellpadding="0" cellspacing="0" border="1" rules="all" width="800" class="rpt_table">
	                <thead>
	                    <th>Buyer</th>
	                    <th>Job Year</th>
	                    <th>Job No</th>
	                    <th>Order No</th>
	                    <th>Style Ref.</th>
	                    <th>Internal Ref.</th>
	                    <!-- <th>Batch No.</th> -->
	                    <th>
	                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
	                    	<input type="hidden" name="hidden_data" id="hidden_data" class="text_boxes" value=""> 
	                    	<input type="hidden" name="hidden_buyer" id="hidden_buyer"> 
	                    </th> 
	                </thead>
	                <tr class="general">
	                	<td>
	                    	<?
								echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",$data[0] ); 
							?>       
	                    </td>
	                    <td>
							<?
								echo create_drop_down( "cbo_year", 80, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
							?>
	                    </td>
	                    <td><input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes_numeric" style="width:100px" /></td>
	                    <td>				
	                        <input type="text" name="txt_order_no" id="txt_order_no" style="width:100px" class="text_boxes" />	
	                    </td>
	                    <td>				
	                        <input type="text" name="txt_style_ref" id="txt_style_ref" style="width:100px" class="text_boxes" />	
	                    </td>
	                    <td>				
	                        <input type="text" name="txt_internal_ref_no" id="txt_internal_ref_no" style="width:100px" class="text_boxes" />	
	                    </td>
	                    <!-- <td>				 -->
	                        <!-- <input type="text" name="txt_batch_no" id="txt_batch_no" style="width:100px" class="text_boxes" />	 -->
	                    <!-- </td> 						 -->
	            		<td align="center">
	                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="checkValidation()" style="width:100px;" />
	                     </td>
	                </tr>
				</table>
				<div style="margin-top:10px;" id="search_div" align="left"></div> 
			</fieldset>
		</form>
	</div>
	</body>         
	<script type="text/javascript">
		var bId = '<? echo $buyerId;?>';
		if(bId!="")
		{
			document.getElementById('cbo_buyer_name').value=bId;
			document.getElementById("cbo_buyer_name").disabled = true;  
		}
	</script>  
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="create_fabric_search_list_view")
{
	$data = explode("_",$data);
	
	$company_id =$data[0];
	$buyer_id =$data[1];
	$cbo_year=trim($data[2]);
	$job_no=trim($data[3]);
	$order_no=trim($data[4]);
	$int_ref=trim($data[5]);
	// $batch_no=trim($data[6]);
	$style_ref=trim($data[6]);
	$hdn_variable_setting_status=trim($data[7]);
    // echo $style_ref;die;
	
	$buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$sizeArr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	
	if($buyer_id==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$buyer_id";
	}
	
	$job_no_cond="";
	if($job_no!="")
	{
		$job_no_cond=" and a.job_no_prefix_num=$job_no";
	}

	$year_cond="";
	if($cbo_year!=0) 
	{
		if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$cbo_year";
		else if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
	}
	
	$po_cond="";
	if($order_no!="")
	{
		$po_cond=" and b.po_number like '".$order_no."%'";
	}

	$int_ref_cond="";
	if($int_ref!="")
	{
		$int_ref_cond=" and b.grouping ='$int_ref'";
	}

	$style_ref_cond="";
	if($style_ref!="")
	{
		$style_ref_cond=" and a.style_ref_no ='$style_ref'";
	}

	$batch_cond="";
	if($batch_no!="")
	{
		$batch_cond=" and f.batch_no ='$batch_no'";
	}
	
	if($db_type==2) 
	{
		 $year=" TO_CHAR(a.insert_date,'YYYY') as year ";
		 $null_cond="NVL";
	}
    else if($db_type==0) 
	{ 
		$year=" year(a.insert_date) as year ";
		$null_cond="IFNULL";
	}
	if($hdn_variable_setting_status==1)
	{
		$sql_po_wise = sql_select("SELECT a.buyer_name,a.job_no,a.style_ref_no, c.item_number_id as item_id, e.body_part_id, e.lib_yarn_count_deter_id, c.color_number_id as color_id, NVL(c.size_number_id,0) as size_id,e.uom, e.avg_cons ,sum((d.cons/d.pcs)*c.plan_cut_qnty) as budget_qty ,b.id as po_id ,b.grouping, e.gsm_weight, d.dia_width 
		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls d, wo_pre_cost_fabric_cost_dtls e
		where a.id=b.job_id and b.id=c.po_break_down_id and c.po_break_down_id=d.po_break_down_id and c.color_number_id=d.color_number_id and c.size_number_id=d.gmts_sizes and d.pre_cost_fabric_cost_dtls_id=e.id and c.job_no_mst=e.job_no and c.item_number_id=e.item_number_id and a.is_deleted=0 and a.status_active=1 and b.is_confirmed=1 and a.company_name=$company_id $buyer_id_cond $job_no_cond $year_cond $po_cond $int_ref_cond $batch_cond $style_ref_cond
		group by  a.buyer_name,a.job_no,a.style_ref_no, c.item_number_id, e.body_part_id, e.lib_yarn_count_deter_id, c.color_number_id, nvl(c.size_number_id,0),e.uom, e.avg_cons ,a.insert_date,b.id,b.grouping, e.gsm_weight, d.dia_width");
		foreach($sql_po_wise as $row)
		{
			$po_wise_budgt_qnt_arr[$row[csf('po_id')]][$row[csf('buyer_name')]][$row[csf('job_no')]][$row[csf('style_ref_no')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('color_id')]][$row[csf('size_id')]][$row[csf('grouping')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['qnty']+=$row[csf('budget_qty')];
		}



		$sql = "SELECT a.buyer_name,a.job_no, a.job_no_prefix_num,a.style_ref_no, c.item_number_id as item_id, e.body_part_id, e.lib_yarn_count_deter_id, c.color_number_id as color_id, NVL(c.size_number_id,0) as size_id,e.uom, e.avg_cons ,sum((d.cons/d.pcs)*c.plan_cut_qnty) as budget_qty, TO_CHAR(a.insert_date,'YYYY') as year ,LISTAGG(b.id,',') WITHIN GROUP (ORDER BY b.id) as po_id ,LISTAGG(b.po_number,',') WITHIN GROUP (ORDER BY b.po_number) as po_number ,b.grouping, e.gsm_weight, d.dia_width 
		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls d, wo_pre_cost_fabric_cost_dtls e
		where a.id=b.job_id and b.id=c.po_break_down_id and c.po_break_down_id=d.po_break_down_id and c.color_number_id=d.color_number_id and c.size_number_id=d.gmts_sizes and d.pre_cost_fabric_cost_dtls_id=e.id and c.job_no_mst=e.job_no and c.item_number_id=e.item_number_id and a.is_deleted=0 and a.status_active=1 and b.is_confirmed=1 and a.company_name=$company_id $buyer_id_cond $job_no_cond $year_cond $po_cond $int_ref_cond $batch_cond $style_ref_cond
		group by  a.buyer_name,a.job_no, a.job_no_prefix_num,a.style_ref_no, c.item_number_id, e.body_part_id, e.lib_yarn_count_deter_id, c.color_number_id, nvl(c.size_number_id,0),e.uom, e.avg_cons ,a.insert_date,b.grouping, e.gsm_weight, d.dia_width";

		$results = sql_select($sql);
		$poIDs="";
		foreach ($results as $val) 
		{
			$poIDs=$val[csf('po_id')].",";
		}
		$poIDs=chop($poIDs,",");

		$determination_info_sql = sql_select("SELECT b.body_part_id,b.lib_yarn_count_deter_id,b.uom, b.gsm_weight, a.booking_no as wopi_number, a.dia_width, a.fabric_color_id as color_id,c.rd_no,c.fabric_ref,c.weight_type,c.cutable_width from wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b ,lib_yarn_count_determina_mst c where a.pre_cost_fabric_cost_dtls_id=b.id and b.lib_yarn_count_deter_id=c.id and a.po_break_down_id in($poIDs) and a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 group by b.body_part_id,a.booking_no, b.lib_yarn_count_deter_id,b.gsm_weight, a.dia_width, a.fabric_color_id,b.uom,c.rd_no,c.fabric_ref,c.weight_type,c.cutable_width"); 
		foreach($determination_info_sql as $row)
		{
			$determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('body_part_id')]]['rd_no']=$row[csf('rd_no')];
			$determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('body_part_id')]]['fabric_ref']=$row[csf('fabric_ref')];
			$determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('body_part_id')]]['weight_type']=$row[csf('weight_type')];
			$determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('body_part_id')]]['cutable_width']=$row[csf('cutable_width')];
		}
	}
	else
	{
		$sql = "SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, $year, b.id as po_id, b.po_number,b.grouping, c.item_number_id as item_id, c.color_number_id as color_id, $null_cond(c.size_number_id,0) as size_id, e.body_part_id, e.lib_yarn_count_deter_id, e.gsm_weight, d.dia_width, sum((d.cons/d.pcs)*c.plan_cut_qnty) as budget_qty,e.uom, e.avg_cons,a.style_ref_no
		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls d, wo_pre_cost_fabric_cost_dtls e
		where a.id=b.job_id and b.id=c.po_break_down_id and c.po_break_down_id=d.po_break_down_id and c.color_number_id=d.color_number_id and c.size_number_id=d.gmts_sizes and d.pre_cost_fabric_cost_dtls_id=e.id and c.job_no_mst=e.job_no and c.item_number_id=e.item_number_id and a.is_deleted=0 and a.status_active=1 and b.is_confirmed=1 and a.company_name=$company_id $buyer_id_cond $job_no_cond $year_cond $po_cond $int_ref_cond $batch_cond $style_ref_cond
		group by a.job_no, a.job_no_prefix_num, a.buyer_name,a.insert_date, b.id, b.po_number,b.grouping, c.item_number_id, c.color_number_id, nvl(c.size_number_id,0), e.body_part_id, e.lib_yarn_count_deter_id, e.gsm_weight, d.dia_width,e.uom, e.avg_cons,a.style_ref_no";
	}

	
	/*$sql = "SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, $year, b.id as po_id, b.po_number,b.grouping, c.item_number_id as item_id, c.color_number_id as color_id, $null_cond(c.size_number_id,0) as size_id, e.body_part_id, e.lib_yarn_count_deter_id, e.gsm_weight, d.dia_width, sum((d.cons/d.pcs)*c.plan_cut_qnty) as budget_qty,f.batch_no
	from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls d, wo_pre_cost_fabric_cost_dtls e, pro_batch_create_mst f,pro_batch_create_dtls g 
	where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.po_break_down_id=d.po_break_down_id and c.color_number_id=d.color_number_id and c.size_number_id=d.gmts_sizes and d.pre_cost_fabric_cost_dtls_id=e.id and c.job_no_mst=e.job_no and c.item_number_id=e.item_number_id and f.id=g.mst_id and b.id=g.po_id and a.is_deleted=0 and a.status_active=1 and f.is_deleted=0 and f.status_active=1 and a.company_name=$company_id $buyer_id_cond $job_no_cond $year_cond $po_cond $int_ref_cond $batch_cond 
	group by a.job_no, a.job_no_prefix_num, a.buyer_name,a.insert_date, b.id, b.po_number,b.grouping, c.item_number_id, c.color_number_id, nvl(c.size_number_id,0), e.body_part_id, e.lib_yarn_count_deter_id, e.gsm_weight, d.dia_width,f.batch_no";*/		  
	// echo $sql;//die();					  
	$result = sql_select($sql);

	$po_id_array = array();
	foreach ($result as $val) 
	{
		$po_id_array[$val[csf('po_id')]] = $val[csf('po_id')];
	}

	//============================== getting previous receive qnty ================================
	if(count($po_id_array))
	{
		$po_id_cond=where_con_using_array($po_id_array,0,"po_id");
	}
	$sqlPrev="SELECT id, buyer_id, po_id, job_no, item_id, body_part, determination_id, gsm, dia, color_id, size_id, reqn_qty from pro_fab_reqn_for_cutting_dtls where status_active=1 and is_deleted=0 $po_id_cond";
	// echo $sqlPrev;die();
	$resultPrev=sql_select($sqlPrev);
	$prevRcvQntyArr = array();
	foreach ($resultPrev as $val) 
	{
		$prevRcvQntyArr[$val[csf('buyer_id')]][$val[csf('po_id')]][$val[csf('job_no')]][$val[csf('item_id')]][$val[csf('body_part')]][$val[csf('determination_id')]][$val[csf('gsm')]][$val[csf('dia')]][$val[csf('color_id')]][$val[csf('size_id')]]+=$val[csf('reqn_qty')];
	}
	
	$composition_arr=array(); $constructtion_arr=array();
 	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	
	$reqn_qnty_array=array();
	$reqnData=sql_select("SELECT buyer_id, po_id, job_no, item_id, body_part, determination_id, gsm, dia, color_id, size_id, sum(reqn_qty) as qnty from pro_fab_reqn_for_cutting_dtls where status_active=1 and is_deleted=0 $po_id_cond group by buyer_id, po_id, job_no, item_id, body_part, determination_id, gsm, dia, color_id, size_id");
	foreach($reqnData as $row)
	{
		$reqn_qnty_array[$row[csf('buyer_id')]][$row[csf('po_id')]][$row[csf('job_no')]][$row[csf('item_id')]][$row[csf('body_part')]][$row[csf('determination_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('color_id')]][$row[csf('size_id')]]=$row[csf('qnty')];
	}
	//print_r($reqn_qnty_array);


	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1260" class="rpt_table">
		<thead>
			<th style="word-wrap: break-word;word-break: break-all;" width="30">SL</th>
			<th style="word-wrap: break-word;word-break: break-all;" width="55">Buyer</th>
            <th style="word-wrap: break-word;word-break: break-all;" width="50">Job Year</th>
			<th style="word-wrap: break-word;word-break: break-all;" width="60">Job No</th>
			<th style="word-wrap: break-word;word-break: break-all;" width="80">Order No</th>
			<th style="word-wrap: break-word;word-break: break-all;" width="100">Style Ref.</th>
			<th style="word-wrap: break-word;word-break: break-all;" width="80">Int. Ref.</th>
		    <th style="word-wrap: break-word;word-break: break-all;" width="100">Gmts. Item</th>
            <th style="word-wrap: break-word;word-break: break-all;" width="80">Body part</th>
			<th style="word-wrap: break-word;word-break: break-all;" width="140">Const/Composition</th>
			<th style="word-wrap: break-word;word-break: break-all;" width="40">GSM</th>
			<th style="word-wrap: break-word;word-break: break-all;" width="40">Dia</th>
			<th style="word-wrap: break-word;word-break: break-all;" width="70">Gmts. Color</th>
			<th style="word-wrap: break-word;word-break: break-all;" width="50">Size</th>
			<th style="word-wrap: break-word;word-break: break-all;" width="70">Uom</th>
			<th style="word-wrap: break-word;word-break: break-all;" width="70">Consumption</th>
			<th style="word-wrap: break-word;word-break: break-all;" width="75">Budget Qty</th>
			<th style="word-wrap: break-word;word-break: break-all;" width="70">Reqn. Qty</th>
		</thead>
	</table>
	<div style="width:1280px; max-height:250px; overflow-y:scroll" id="list_container_batch" align="left">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1260" class="rpt_table" id="tbl_list_search">  
		<?
			$i=1;
			foreach ($result as $row)
			{  
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";				
				$reqnQty=$reqn_qnty_array[$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('job_no')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('size_id')]];
				$prevRcvQnty=$prevRcvQntyArr[$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('job_no')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('size_id')]];
				$balance_qty=$row[csf('budget_qty')]-$prevRcvQnty;

				$uomData=$uom_data[$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('job_no')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('size_id')]]=$row[csf('uom')];
				$avg_cons=$avg_cons_data[$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('job_no')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('size_id')]]=$row[csf('avg_cons')];
				
				$cons_comp=$constructtion_arr[$row[csf('lib_yarn_count_deter_id')]].", ".$composition_arr[$row[csf('lib_yarn_count_deter_id')]];

				if($hdn_variable_setting_status==1)
				{

					$po_arr_uniq=array_unique(explode(",", chop($row[csf('po_id')],",") ));
					$hdn_po_ratio_qnty="";$hdn_po_ratio_ref="";//$iss_qty="";
					foreach ($po_arr_uniq as $poID) 
					{
						$orderRequiredQnty=  $po_wise_budgt_qnt_arr[$poID][$row[csf('buyer_name')]][$row[csf('job_no')]][$row[csf('style_ref_no')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('color_id')]][$row[csf('size_id')]][$row[csf('grouping')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['qnty']; 


						$hdn_po_ratio_qnty.=$poID."##".($orderRequiredQnty/$row[csf('budget_qty')])*$balance_qty.",";

						$hdn_po_ratio_ref.=$poID."##".$orderRequiredQnty.",";

						//$orderRecvRequiredQnty=$prev_recv_qnty_arr2[$jobNo][$bookingNo][$styleRef][$poID]['qnty'];
						//$hdn_po_recv_ratio_ref.=$poID."=".$orderRecvRequiredQnty.",";

						//$po_ratio=$orderRecvRequiredQnty*1/$rev_req_qnty_total*1;
						//$iss_qty+=$finish_qnty_array[$poID];

					}
					$hdn_po_ratio_ref=chop($hdn_po_ratio_ref,",");
					$hdn_po_ratio_qnty=chop($hdn_po_ratio_qnty,",");



					$rd_no=$determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('body_part_id')]]['rd_no'];
					$fabric_ref=$determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('body_part_id')]]['fabric_ref'];
					$weight_type=$determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('body_part_id')]]['weight_type'];
					$cutable_width=$determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('body_part_id')]]['cutable_width'];   

					$data=$buyer_short_name_arr[$row[csf('buyer_name')]]."**".$row[csf('buyer_name')]."**".$row[csf('year')]."**".$row[csf('job_no_prefix_num')]."**".$row[csf('job_no')]."**".$row[csf('po_number')]."**".$row[csf('po_id')]."**".$row[csf('item_id')]."**".$garments_item[$row[csf('item_id')]]."**".$row[csf('body_part_id')]."**".$body_part[$row[csf('body_part_id')]]."**".$cons_comp."**".$row[csf('lib_yarn_count_deter_id')]."**".$row[csf('gsm_weight')]."**".$row[csf('dia_width')]."**".$color_arr[$row[csf('color_id')]]."**".$row[csf('color_id')]."**".$sizeArr[$row[csf('size_id')]]."**".$row[csf('size_id')]."**".$unit_of_measurement[$row[csf('uom')]]."**".$avg_cons."**".number_format($row[csf('budget_qty')],2,'.','')."**".number_format($balance_qty,2,'.','')."**".number_format($prevRcvQnty,2,'.','')."**".$row[csf('style_ref_no')]."**".$fabric_ref."**".$rd_no."**".$weight_type."**".$fabric_weight_type[$weight_type]."**".$cutable_width."**".$hdn_po_ratio_ref."**".$hdn_po_ratio_qnty;
				}
				else
				{
					$data=$buyer_short_name_arr[$row[csf('buyer_name')]]."**".$row[csf('buyer_name')]."**".$row[csf('year')]."**".$row[csf('job_no_prefix_num')]."**".$row[csf('job_no')]."**".$row[csf('po_number')]."**".$row[csf('po_id')]."**".$row[csf('item_id')]."**".$garments_item[$row[csf('item_id')]]."**".$row[csf('body_part_id')]."**".$body_part[$row[csf('body_part_id')]]."**".$cons_comp."**".$row[csf('lib_yarn_count_deter_id')]."**".$row[csf('gsm_weight')]."**".$row[csf('dia_width')]."**".$color_arr[$row[csf('color_id')]]."**".$row[csf('color_id')]."**".$sizeArr[$row[csf('size_id')]]."**".$row[csf('size_id')]."**".$unit_of_measurement[$row[csf('uom')]]."**".$avg_cons."**".number_format($row[csf('budget_qty')],2,'.','')."**".number_format($balance_qty,2,'.','')."**".number_format($prevRcvQnty,2,'.','')."**".$row[csf('style_ref_no')];

				}
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>,<? echo $row[csf('buyer_name')];?>,<? echo $row[csf('budget_qty')];?>)"> 
					<td style="word-wrap: break-word;word-break: break-all;" width="30">
						<? echo $i; ?>
						<input type="hidden" name="trData[]" id="trData<? echo $i; ?>" value="<? echo $data; ?>"/>
					</td>
					<td style="word-wrap: break-word;word-break: break-all;" width="55"><p><? echo $buyer_short_name_arr[$row[csf('buyer_name')]]; ?></p></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="50" align="center"><p><? echo $row[csf('year')]; ?></p></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="60"><p>&nbsp;&nbsp;<? echo $row[csf('job_no_prefix_num')]; ?></p></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="80"><p><? echo $row[csf('po_number')]; ?></p></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="100"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="80"><p><? echo $row[csf('grouping')]; ?></p></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="100"><p><? echo $garments_item[$row[csf('item_id')]]; ?></p></td>
                    <td style="word-wrap: break-word;word-break: break-all;" width="80"><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="140"><p><? echo $cons_comp; ?></p></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="40"><p><? echo $row[csf('gsm_weight')]; ?></p></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="40"><p><? echo $row[csf('dia_width')]; ?></p></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="70"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="50"><p><? echo $sizeArr[$row[csf('size_id')]]; ?></p></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="70"><p><? echo $unit_of_measurement[$uomData]; ?></p></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="70"><p><? echo $avg_cons; ?></p></td>
					<td style="word-wrap: break-word;word-break: break-all;" width="75" align="right"><p><? echo number_format($row[csf('budget_qty')],2,'.',''); ?></p>
						<input type="hidden" name="trBudgQnty[]" id="trBudgQnty<? echo $i; ?>" value="<? echo $row[csf('budget_qty')]; ?>"/>
					</td>
					<td style="word-wrap: break-word;word-break: break-all;" width="70" align="right"><p><? echo number_format($reqnQty,2,'.',''); ?></p></td>
				</tr>
			<?
			$i++;
			}
			?>
		</table>
	</div>
	<table width="1100" cellspacing="0" cellpadding="0" border="1" align="center">
		<tr>
			<td align="center" height="30" valign="bottom">
				<div style="width:100%"> 
					<div style="width:45%; float:left" align="left">
						<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data(this.checked)" /> Check / Uncheck All
					</div>
					<div style="width:55%; float:left" align="left">
						<input type="button" name="close" onClick="fnc_close();" class="formbutton" value="Close" style="width:100px" />
					</div>
				</div>
			</td>
		</tr>
	</table>
	<?	
	exit();
}

if ($action=="save_update_delete")
{
	/*$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); */
	//$process = array( &$_POST );
	extract( $_POST );	
	//echo "10**".$operation.'systm';
	if ($operation==0)  // Insert Here
	{ 	
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if($db_type==0) $year_cond="YEAR(insert_date)"; 
		else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
		else $year_cond="";//defined Later
		
		$new_mrr_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'FFRC', date("Y",time()), 5, "select reqn_number_prefix, reqn_number_prefix_num from pro_fab_reqn_for_cutting_mst where company_id=$cbo_company_id and $year_cond=".date('Y',time())." order by id desc ", "reqn_number_prefix","reqn_number_prefix_num"));
		$id=return_next_id( "id", " pro_fab_reqn_for_cutting_mst", 1 ) ;
				 
		$field_array="id,reqn_number_prefix,reqn_number_prefix_num,reqn_number,company_id,lay_plan_id,reqn_date,inserted_by,insert_date";
		$data_array="(".$id.",'".$new_mrr_number[1]."',".$new_mrr_number[2].",'".$new_mrr_number[0]."',".$cbo_company_id.",".$layPlan_id.",".$txt_requisition_date.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		
		$dtls_id = return_next_id( "id", "pro_fab_reqn_for_cutting_dtls", 1 );

		$hdn_variable_setting_status=str_replace("'","",$hdn_variable_setting_status);
		if($hdn_variable_setting_status==1)
		{
			$field_array_dtls="id, mst_id, buyer_id, po_id, job_no, item_id, body_part, determination_id, gsm, dia, color_id, size_id,fabric_ref,weight_type,cutable_width,rd_no,reqn_qty, inserted_by, insert_date";
			for($j=1;$j<=$tot_row;$j++)
			{ 
				$hdnPoRatioQty="hdnPoRatioQty".$j;
				$hdnPoRatioQtyArr=explode(",", $$hdnPoRatioQty);
				foreach ($hdnPoRatioQtyArr as $poData) 
				{
					$hdnPoAndQtyArr=explode("##", $poData);

					$buyerId="buyerId".$j;
					$jobNo="jobNo".$j;
					$poId="poId".$j;
					$itemId="itemId".$j;
					$bodyPartId="bodyPartId".$j;
					$deterId="deterId".$j;
					$gsm="gsm".$j;
					$dia="dia".$j;
					$colorId="colorId".$j;
					$sizeId="sizeId".$j;

					$fabricRef="fabricRef".$j;
					$weightType="weightType".$j;
					$cutableWidth="cutableWidth".$j;
					$rdNo="rdNo".$j;

					$reqsnQty="reqsnQty".$j;
					
					if($data_array_dtls!="") $data_array_dtls.=",";
					$data_array_dtls.="(".$dtls_id.",".$id.",".$$buyerId.",'".$hdnPoAndQtyArr[0]."','".$$jobNo."','".$$itemId."','".$$bodyPartId."','".$$deterId."','".$$gsm."','".$$dia."','".$$colorId."','".$$sizeId."','".$$fabricRef."','".$$weightType."','".$$cutableWidth."','".$$rdNo."','".$hdnPoAndQtyArr[1]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					$dtls_id = $dtls_id+1;

				}
			}
		}
		else
		{
			$field_array_dtls="id, mst_id, buyer_id, po_id, job_no, item_id, body_part, determination_id, gsm, dia, color_id, size_id,reqn_qty, inserted_by, insert_date";
			for($j=1;$j<=$tot_row;$j++)
			{ 
				$buyerId="buyerId".$j;
				$jobNo="jobNo".$j;
				$poId="poId".$j;
				$itemId="itemId".$j;
				$bodyPartId="bodyPartId".$j;
				$deterId="deterId".$j;
				$gsm="gsm".$j;
				$dia="dia".$j;
				$colorId="colorId".$j;
				$sizeId="sizeId".$j;
				$reqsnQty="reqsnQty".$j;
				
				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls.="(".$dtls_id.",".$id.",".$$buyerId.",'".$$poId."','".$$jobNo."','".$$itemId."','".$$bodyPartId."','".$$deterId."','".$$gsm."','".$$dia."','".$$colorId."','".$$sizeId."','".$$reqsnQty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$dtls_id = $dtls_id+1;
			}
		}

		
		//echo "10**$data_array_dtls"; die;
		//echo "10**insert into pro_fab_reqn_for_cutting_mst (".$field_array_dtls.") values ".$data_array_dtls;die;
		$rID=sql_insert("pro_fab_reqn_for_cutting_mst",$field_array,$data_array,0);
		$rID2=sql_insert("pro_fab_reqn_for_cutting_dtls",$field_array_dtls,$data_array_dtls,1);
		//echo "10**".$rID."&&".$rID2;die;

		if($db_type==0)
		{
			if($rID && $rID2)
			{
				mysql_query("COMMIT");  
				echo "0**".$id."**".$new_mrr_number[0];
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2)
			{
				oci_commit($con);  
				echo "0**".$id."**".$new_mrr_number[0];
			}
			else
			{
				oci_rollback($con);
				echo "5**0**0";
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
		
		$field_array="company_id*lay_plan_id*reqn_date*updated_by*update_date";
		$data_array=$cbo_company_id."*".$layPlan_id."*".$txt_requisition_date."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$dtls_id = return_next_id( "id", "pro_fab_reqn_for_cutting_dtls", 1 );
		$field_array_update="reqn_qty*updated_by*update_date";
		$deleted_id='';
		$hdn_variable_setting_status=str_replace("'","",$hdn_variable_setting_status);
		if($hdn_variable_setting_status==1)
		{
			$field_array_dtls="id, mst_id, buyer_id, po_id, job_no, item_id, body_part, determination_id, gsm, dia, color_id, size_id,fabric_ref,weight_type,cutable_width,rd_no, reqn_qty, inserted_by, insert_date";

			for($j=1;$j<=$tot_row;$j++)
			{ 
				$hdnPoRatioQty="hdnPoRatioQty".$j;
				$hdnPoRatioQtyArr=explode(",", $$hdnPoRatioQty);
				foreach ($hdnPoRatioQtyArr as $poData) 
				{
					$hdnPoAndQtyArr=explode("##", $poData);


					$buyerId="buyerId".$j;
					$jobNo="jobNo".$j;
					$poId="poId".$j;
					$itemId="itemId".$j;
					$bodyPartId="bodyPartId".$j;
					$deterId="deterId".$j;
					$gsm="gsm".$j;
					$dia="dia".$j;
					$colorId="colorId".$j;
					$sizeId="sizeId".$j;

					$fabricRef="fabricRef".$j;
					$weightType="weightType".$j;
					$cutableWidth="cutableWidth".$j;
					$rdNo="rdNo".$j;

					$reqsnQty="reqsnQty".$j;
					$dtlsId="dtlsId".$j;
					
					if($$dtlsId>0)
					{
						$dtlsIds=explode(",",$$dtlsId);
						foreach ($dtlsIds as $key => $dtID) 
						{
							if($$reqsnQty>0)
							{
								$dtlsId_arr[]=$dtID;
								$data_array_update[$dtID]=explode("*",("'".$$reqsnQty."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
								//print_r($data_array_update);
							}
							else
							{
								$deleted_id.=$dtID.",";
							}
						}	
					}
					else
					{
						if($data_array_dtls!="") $data_array_dtls.=",";
						$data_array_dtls.="(".$dtls_id.",".$update_id.",".$$buyerId.",'".$hdnPoAndQtyArr[0]."','".$$jobNo."','".$$itemId."','".$$bodyPartId."','".$$deterId."','".$$gsm."','".$$dia."','".$$colorId."','".$$sizeId."','".$$fabricRef."','".$$weightType."','".$$cutableWidth."','".$$rdNo."','".$hdnPoAndQtyArr[1]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
						$dtls_id = $dtls_id+1;	
					}
				}
			}
		}
		else
		{
			$field_array_dtls="id, mst_id, buyer_id, po_id, job_no, item_id, body_part, determination_id, gsm, dia, color_id, size_id,reqn_qty, inserted_by, insert_date";
			for($j=1;$j<=$tot_row;$j++)
			{ 	
				$buyerId="buyerId".$j;
				$jobNo="jobNo".$j;
				$poId="poId".$j;
				$itemId="itemId".$j;
				$bodyPartId="bodyPartId".$j;
				$deterId="deterId".$j;
				$gsm="gsm".$j;
				$dia="dia".$j;
				$colorId="colorId".$j;
				$sizeId="sizeId".$j;
				$reqsnQty="reqsnQty".$j;
				$dtlsId="dtlsId".$j;
				
				if($$dtlsId>0)
				{
					if($$reqsnQty>0)
					{
						$dtlsId_arr[]=$$dtlsId;
						$data_array_update[$$dtlsId]=explode("*",("'".$$reqsnQty."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					}
					else
					{
						$deleted_id.=$$dtlsId.",";
					}
				}
				else
				{
					if($data_array_dtls!="") $data_array_dtls.=",";
					$data_array_dtls.="(".$dtls_id.",".$update_id.",".$$buyerId.",'".$$poId."','".$$jobNo."','".$$itemId."','".$$bodyPartId."','".$$deterId."','".$$gsm."','".$$dia."','".$$colorId."','".$$sizeId."','".$$reqsnQty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					$dtls_id = $dtls_id+1;	
				}
			}
		}
		$rID=sql_update("pro_fab_reqn_for_cutting_mst",$field_array,$data_array,"id",$update_id,0);
		
		$rID2=true; $rID3=true; $statusChange=true;
		if(count($data_array_update)>0)
		{
			$rID2=execute_query(bulk_update_sql_statement( "pro_fab_reqn_for_cutting_dtls", "id", $field_array_update, $data_array_update, $dtlsId_arr ));
		}
		
		if($data_array_dtls!="")
		{
			$rID2=sql_insert("pro_fab_reqn_for_cutting_dtls",$field_array_dtls,$data_array_dtls,1);
		}

		$deleted_id=substr($deleted_id,0,-1);
		if($deleted_id!="")
		{
			$field_array_status="updated_by*update_date*status_active*is_deleted";
			$data_array_status=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
			$statusChange=sql_multirow_update("pro_fab_reqn_for_cutting_dtls",$field_array_status,$data_array_status,"id",$deleted_id,0);
		}
		//echo "10**".$rID."&&".$rID2."&&".$rID3."&&".$statusChange;die;
		
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $statusChange)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_requisition_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**".str_replace("'", '', $update_id)."**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3 && $statusChange)
			{
				oci_commit($con);  
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_requisition_no);
			}
			else
			{
				oci_rollback($con);
				echo "6**".str_replace("'", '', $update_id)."**1";
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="requisition_popup")
{
	echo load_html_head_contents("Requisition Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	?> 

		<script>
		
			function js_set_value(data)
			{
				$('#hidden_reqn_id').val(data);
				parent.emailwindow.hide();
			}
		
	    </script>

	</head>

	<body>
	<div align="center" style="width:760px;">
		<form name="searchwofrm"  id="searchwofrm">
			<fieldset style="width:760px; margin-left:2px">
	            <table cellpadding="0" cellspacing="0" width="750" border="1" rules="all" class="rpt_table">
	                <thead>
	                    <th>Requisition Date Range</th>
	                    <th id="search_by_td_up" width="180">Requisition No</th>
	                    <th>
	                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
	                    	<input type="hidden" name="hidden_reqn_id" id="hidden_reqn_id">  
	                    </th> 
	                </thead>
	                <tr class="general">
	                    <td align="center">
	                    	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" readonly>To
						  	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" readonly>
						</td>
	                    <td align="center" id="search_by_td">				
	                        <input type="text" style="width:130px" class="text_boxes"  name="txt_reqn_no" id="txt_reqn_no" />	
	                    </td> 						
	            		<td align="center">
	                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_reqn_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company_id; ?>+'_'+document.getElementById('cbo_year_selection').value+'_'+<? echo ($buyerId !="") ? $buyerId : 0; ?>, 'create_reqn_search_list_view', 'search_div', 'woven_fabric_requisition_for_cutting_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
	                     </td>
	                </tr>
	                <tr>
	                	<td colspan="5" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
	                </tr>
	           </table>
	           <div style="width:100%; margin-top:5px;" id="search_div" align="center"></div>
			</fieldset>
		</form>
	</div>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		$('#cbo_location_id').val(0);
	</script>
	</html>
	<?
	exit();
}

if($action=="create_reqn_search_list_view")
{
	$data = explode("_",$data);
	
	$search_string="%".trim($data[0]);
	$start_date =$data[1];
	$end_date =$data[2];
	$company_id =$data[3];
	$year =$data[4];
	$buyerId =$data[5];

	$lay_plan_arr=return_library_array( "select id, cutting_no from ppl_cut_lay_mst",'id','cutting_no');
	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and reqn_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and reqn_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}
	else $date_cond="";
	
	$search_field_cond="";
	if(trim($data[0])!="")
	{
		$search_field_cond="and reqn_number like '$search_string'";
	}
	
	if($db_type==0) $year_field="YEAR(insert_date) as year,";
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year,";
	else $year_field="";//defined Later

	if($db_type==0 && $year !="") $year_field_cond=" and YEAR(insert_date)=$year";
	else if($db_type==2 && $year !="") $year_field_cond=" and to_char(insert_date,'YYYY')=$year";

	// $buyer_id_cond = ($buyerId !="") ? $buyerId : '';
	
	$sql = "select id, $year_field reqn_number_prefix_num, reqn_number, lay_plan_id, reqn_date from pro_fab_reqn_for_cutting_mst where status_active=1 and is_deleted=0 and company_id=$company_id $search_field_cond $location_cond $date_cond $year_field_cond order by id DESC"; 
	// echo $sql;
	$arr=array(3=>$lay_plan_arr);
	
	echo create_list_view("tbl_list_search", "Year, Requisition No, Requisition Date, Lay Plan Cutting No", "80,150,150","700","200",0, $sql, "js_set_value", "id", "", 1, "0,0,0,lay_plan_id", $arr, "year,reqn_number_prefix_num,reqn_date,lay_plan_id","","",'0,0,3,0','');
	
	exit();
}

if($action=='populate_data_from_requisition')
{
	$data_array=sql_select("select id, reqn_number, company_id, lay_plan_id, reqn_date from pro_fab_reqn_for_cutting_mst where id='$data'");
	foreach ($data_array as $row)
	{ 
		$lay_plan_cutting_no=return_field_value( "cutting_no","ppl_cut_lay_mst","id='".$row[csf("lay_plan_id")]."'");
		
		echo "document.getElementById('txt_requisition_no').value 			= '".$row[csf("reqn_number")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('layPlan_id').value 					= '".$row[csf("lay_plan_id")]."';\n";
		echo "document.getElementById('txt_layPlan_No').value 				= '".$lay_plan_cutting_no."';\n";
		echo "document.getElementById('txt_requisition_date').value 		= '".change_date_format($row[csf("reqn_date")])."';\n";
		echo "document.getElementById('update_id').value 					= '".$row[csf("id")]."';\n";
		
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_fabric_requisition_for_cutting',1);\n";  
		exit();
	}
}

if( $action == 'populate_list_view' ) 
{	

	$data=explode("**", $data);
 	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$sizeArr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	
	$composition_arr=array(); $constructtion_arr=array();
 	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	
	if($db_type==2) 
	{
		 $year=" TO_CHAR(a.insert_date,'YYYY') as year ";
		 $null_cond="NVL";
	}
    else if($db_type==0) 
	{ 
		$year=" year(a.insert_date) as year ";
		$null_cond="IFNULL";
	}
	
	$all_po_arr=array();
	if($data[1]==1)
	{
		/*$sql_po_wise="SELECT buyer_id, po_id as po_id , job_no, item_id, body_part, determination_id, gsm, dia, color_id, size_id,fabric_ref,rd_no,cutable_width,weight_type, sum(reqn_qty) as  reqn_qty from pro_fab_reqn_for_cutting_dtls where status_active=1 and is_deleted=0 and mst_id=$data[0] group by buyer_id,po_id, job_no, item_id, body_part, determination_id, gsm, dia, color_id, size_id,fabric_ref,rd_no,cutable_width,weight_type ";
		$result_sql=sql_select($sql_po_wise);
		foreach ($result_sql as $row)
		{
			$po_wise_budgt_qnt_arr[$row[csf('po_id')]][$row[csf('buyer_id')]][$row[csf('job_no')]][$row[csf('item_id')]][$row[csf('body_part')]][$row[csf('determination_id')]][$row[csf('color_id')]][$row[csf('size_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('fabric_ref')]][$row[csf('rd_no')]][$row[csf('cutable_width')]][$row[csf('weight_type')]]['qnty']+=$row[csf('reqn_qty')];
		}*/


		$sql="SELECT  LISTAGG(id,',') WITHIN GROUP (ORDER BY id) as id, buyer_id, LISTAGG(po_id,',') WITHIN GROUP (ORDER BY po_id) as po_id , job_no, item_id, body_part, determination_id, gsm, dia, color_id, size_id,fabric_ref,rd_no,cutable_width,weight_type, sum(reqn_qty) as  reqn_qty from pro_fab_reqn_for_cutting_dtls where status_active=1 and is_deleted=0 and mst_id=$data[0] group by buyer_id, job_no, item_id, body_part, determination_id, gsm, dia, color_id, size_id,fabric_ref,rd_no,cutable_width,weight_type ";
	}
	else
	{
		$sql="SELECT id, buyer_id, po_id, job_no, item_id, body_part, determination_id, gsm, dia, color_id, size_id, reqn_qty from pro_fab_reqn_for_cutting_dtls where status_active=1 and is_deleted=0 and mst_id=$data[0]";
	}
	
	
	$result=sql_select($sql);
	foreach ($result as $row)
	{
		$all_po_arr[$row[csf('po_id')]]=$row[csf('po_id')];
	}
	$all_po_id=implode(",", $all_po_arr);

	//============================== getting previous receive qnty ================================
	if($data[1]==1)
	{
		$sqlPrev="SELECT  buyer_id, LISTAGG(po_id,',') WITHIN GROUP (ORDER BY po_id) as po_id, job_no, item_id, body_part, determination_id, gsm, dia, color_id, size_id,fabric_ref,rd_no,cutable_width,weight_type, sum(reqn_qty) as reqn_qty from pro_fab_reqn_for_cutting_dtls where status_active=1 and is_deleted=0 and po_id in($all_po_id) and mst_id not in($data[0]) group by buyer_id,job_no, item_id, body_part, determination_id, gsm, dia, color_id, size_id,fabric_ref,rd_no,cutable_width,weight_type";
		// echo $sqlPrev;die();
		$resultPrev=sql_select($sqlPrev);
		$prevRcvQntyArr = array();
		foreach ($resultPrev as $val) 
		{
			$prevRcvQntyArr[$val[csf('buyer_id')]][$val[csf('job_no')]][$val[csf('item_id')]][$val[csf('body_part')]][$val[csf('determination_id')]][$val[csf('gsm')]][$val[csf('dia')]][$val[csf('color_id')]][$val[csf('size_id')]][$val[csf('fabric_ref')]][$val[csf('rd_no')]][$val[csf('cutable_width')]][$val[csf('weight_type')]]+=$val[csf('reqn_qty')];
		}
		//print_r($prevRcvQntyArr);
	}
	else
	{
		$sqlPrev="SELECT id, buyer_id, po_id, job_no, item_id, body_part, determination_id, gsm, dia, color_id, size_id, reqn_qty from pro_fab_reqn_for_cutting_dtls where status_active=1 and is_deleted=0 and po_id in($all_po_id) and mst_id not in($data[0])";
		// echo $sqlPrev;die();
		$resultPrev=sql_select($sqlPrev);
		$prevRcvQntyArr = array();
		foreach ($resultPrev as $val) 
		{
			$prevRcvQntyArr[$val[csf('buyer_id')]][$val[csf('po_id')]][$val[csf('job_no')]][$val[csf('item_id')]][$val[csf('body_part')]][$val[csf('determination_id')]][$val[csf('gsm')]][$val[csf('dia')]][$val[csf('color_id')]][$val[csf('size_id')]]+=$val[csf('reqn_qty')];
		}
		// print_r($prevRcvQntyArr);
	}
	
	$budget_qty_array=array(); $po_data_array=array();$avg_cons_data=array();$uom_data=array();

	if($data[1]==1)
	{
		$sql_budget_po_wise= sql_select("SELECT a.job_no, a.job_no_prefix_num, a.buyer_name,a.style_ref_no, $year, b.id as  po_id,b.po_number as po_number, c.item_number_id as item_id, c.color_number_id as color_id, $null_cond(c.size_number_id,0) as size_id, e.body_part_id, e.lib_yarn_count_deter_id, e.gsm_weight, d.dia_width, sum((d.cons/d.pcs)*c.plan_cut_qnty) as budget_qty, e.uom, e.avg_cons from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls d, wo_pre_cost_fabric_cost_dtls e where a.id=b.job_id and b.id=c.po_break_down_id and c.po_break_down_id=d.po_break_down_id and c.color_number_id=d.color_number_id and c.size_number_id=d.gmts_sizes and d.pre_cost_fabric_cost_dtls_id=e.id and c.job_no_mst=e.job_no and c.item_number_id=e.item_number_id and a.is_deleted=0 and a.status_active=1 and b.id in($all_po_id) group by a.job_no, a.job_no_prefix_num, a.buyer_name,a.style_ref_no,b.id,b.po_number,a.insert_date, b.po_number, c.item_number_id, c.color_number_id, nvl(c.size_number_id,0), e.body_part_id, e.lib_yarn_count_deter_id, e.gsm_weight, d.dia_width, e.uom, e.avg_cons");

		foreach ($sql_budget_po_wise as $row)
		{
			$po_wise_budgt_qnt_arr[$row[csf('po_id')]][$row[csf('buyer_name')]][$row[csf('job_no')]][$row[csf('style_ref_no')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('color_id')]][$row[csf('size_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['qnty']+=$row[csf('budget_qty')];

		}

		$sql_budget= sql_select("SELECT a.job_no, a.job_no_prefix_num, a.buyer_name,a.style_ref_no, $year, LISTAGG(b.id,',') WITHIN GROUP (ORDER BY b.id) as  po_id,LISTAGG(b.po_number,',') WITHIN GROUP (ORDER BY b.po_number) as po_number, c.item_number_id as item_id, c.color_number_id as color_id, $null_cond(c.size_number_id,0) as size_id, e.body_part_id, e.lib_yarn_count_deter_id, e.gsm_weight, d.dia_width, sum((d.cons/d.pcs)*c.plan_cut_qnty) as budget_qty, e.uom, e.avg_cons from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls d, wo_pre_cost_fabric_cost_dtls e where a.id=b.job_id and b.id=c.po_break_down_id and c.po_break_down_id=d.po_break_down_id and c.color_number_id=d.color_number_id and c.size_number_id=d.gmts_sizes and d.pre_cost_fabric_cost_dtls_id=e.id and c.job_no_mst=e.job_no and c.item_number_id=e.item_number_id and a.is_deleted=0 and a.status_active=1 and b.id in($all_po_id) group by a.job_no, a.job_no_prefix_num, a.buyer_name,a.style_ref_no,a.insert_date, b.po_number, c.item_number_id, c.color_number_id, nvl(c.size_number_id,0), e.body_part_id, e.lib_yarn_count_deter_id, e.gsm_weight, d.dia_width, e.uom, e.avg_cons");

		foreach ($sql_budget as $row)
		{
			$budget_qty_array[$row[csf('buyer_name')]][$row[csf('job_no')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('size_id')]]+=$row[csf('budget_qty')];
			$avg_cons_data[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]=$row[csf('avg_cons')];
			$uom_data[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]=$row[csf('uom')];
			
			//$po_data_array[$row[csf('po_id')]]['po_no']=$row[csf('po_number')];
			//$po_data_array[$row[csf('po_id')]]['prefix']=$row[csf('job_no_prefix_num')];
			//$po_data_array[$row[csf('po_id')]]['year']=$row[csf('year')];

			$po_data_array[$row[csf('buyer_name')]][$row[csf('job_no')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('size_id')]]['po_no'].=$row[csf('po_number')].",";
			

			$po_data_array[$row[csf('buyer_name')]][$row[csf('job_no')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('size_id')]]['prefix']=$row[csf('job_no_prefix_num')];
			$po_data_array[$row[csf('buyer_name')]][$row[csf('job_no')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('size_id')]]['year']=$row[csf('year')];
			$po_data_array[$row[csf('buyer_name')]][$row[csf('job_no')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('size_id')]]['style_ref_no']=$row[csf('style_ref_no')];


		
		}


	}

	else
	{
		$sql_budget= sql_select("SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, $year, b.id as po_id, b.po_number, c.item_number_id as item_id, c.color_number_id as color_id, $null_cond(c.size_number_id,0) as size_id, e.body_part_id, e.lib_yarn_count_deter_id, e.gsm_weight, d.dia_width, sum((d.cons/d.pcs)*c.plan_cut_qnty) as budget_qty, e.uom, e.avg_cons from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls d, wo_pre_cost_fabric_cost_dtls e where a.id=b.job_id and b.id=c.po_break_down_id and c.po_break_down_id=d.po_break_down_id and c.color_number_id=d.color_number_id and c.size_number_id=d.gmts_sizes and d.pre_cost_fabric_cost_dtls_id=e.id and c.job_no_mst=e.job_no and c.item_number_id=e.item_number_id and a.is_deleted=0 and a.status_active=1 and b.id in($all_po_id) group by a.job_no, a.job_no_prefix_num, a.buyer_name,a.insert_date, b.id, b.po_number, c.item_number_id, c.color_number_id, nvl(c.size_number_id,0), e.body_part_id, e.lib_yarn_count_deter_id, e.gsm_weight, d.dia_width, e.uom, e.avg_cons");

		foreach ($sql_budget as $row)
		{
			$budget_qty_array[$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('job_no')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('size_id')]]=$row[csf('budget_qty')];
			$avg_cons_data[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]=$row[csf('avg_cons')];
			$uom_data[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]=$row[csf('uom')];
			
			$po_data_array[$row[csf('po_id')]]['po_no']=$row[csf('po_number')];
			$po_data_array[$row[csf('po_id')]]['prefix']=$row[csf('job_no_prefix_num')];
			$po_data_array[$row[csf('po_id')]]['year']=$row[csf('year')];
		}
	}
	//print_r($po_wise_budgt_qnt_arr);
	//print_r($po_data_array);

	
	//echo '<pre>';print_r($uom_data);
	/*$cons_uom_data=sql_select("SELECT job_no, body_part_id, lib_yarn_count_deter_id, avg_cons, uom from wo_pre_cost_fabric_cost_dtls where is_deleted=0 and status_active=1 and job_no='OG-18-00070'");
	$avg_cons_data=array();$uom_data=array();
	foreach($cons_uom_data as $row)
	{
		$avg_cons_data[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]=$row[csf('avg_cons')];
		$uom_data[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]=$row[csf('uom')];
	}*/
	/*echo "<pre>";
	print_r($avg_cons_data);die;*/
	$i=1;
	foreach($result as $row)
	{

		if($data[1]==1)
		{
			$year=$po_data_array[$row[csf('buyer_id')]][$row[csf('job_no')]][$row[csf('item_id')]][$row[csf('body_part')]][$row[csf('determination_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('color_id')]][$row[csf('size_id')]]['year'];

			$poNO=$po_data_array[$row[csf('buyer_id')]][$row[csf('job_no')]][$row[csf('item_id')]][$row[csf('body_part')]][$row[csf('determination_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('color_id')]][$row[csf('size_id')]]['po_no'];

			$job_prefix=$po_data_array[$row[csf('buyer_id')]][$row[csf('job_no')]][$row[csf('item_id')]][$row[csf('body_part')]][$row[csf('determination_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('color_id')]][$row[csf('size_id')]]['prefix'];

			$style_ref_no=$po_data_array[$row[csf('buyer_id')]][$row[csf('job_no')]][$row[csf('item_id')]][$row[csf('body_part')]][$row[csf('determination_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('color_id')]][$row[csf('size_id')]]['style_ref_no'];

			$budget_qty=$budget_qty_array[$row[csf('buyer_id')]][$row[csf('job_no')]][$row[csf('item_id')]][$row[csf('body_part')]][$row[csf('determination_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('color_id')]][$row[csf('size_id')]];
			$prevRcvQnty = $prevRcvQntyArr[$row[csf('buyer_id')]][$row[csf('job_no')]][$row[csf('item_id')]][$row[csf('body_part')]][$row[csf('determination_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('color_id')]][$row[csf('size_id')]][$val[csf('fabric_ref')]][$val[csf('rd_no')]][$val[csf('cutable_width')]][$val[csf('weight_type')]];

			$balance_qty_for_ration = $budget_qty - $prevRcvQnty;
			$po_arr_uniq=array_unique(explode(",", chop($row[csf('po_id')],",") ));
			$hdn_po_ratio_qnty="";$hdn_po_ratio_ref="";//$iss_qty="";
			foreach ($po_arr_uniq as $poID) 
			{
			
				//$po_wise_budgt_qnt_arr[$row[csf('po_id')]][$row[csf('buyer_id')]][$row[csf('job_no')]][$row[csf('item_id')]][$row[csf('body_part')]][$row[csf('determination_id')]][$row[csf('color_id')]][$row[csf('size_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('fabric_ref')]][$row[csf('rd_no')]][$row[csf('cutable_width')]][$row[csf('weight_type')]]['qnty']+=$row[csf('reqn_qty')];



				$orderRequiredQnty=  $po_wise_budgt_qnt_arr[$poID][$row[csf('buyer_id')]][$row[csf('job_no')]][$style_ref_no][$row[csf('item_id')]][$row[csf('body_part')]][$row[csf('determination_id')]][$row[csf('color_id')]][$row[csf('size_id')]][$row[csf('gsm')]][$row[csf('dia')]]['qnty']; 


				//$hdn_po_ratio_qnty.=$poID."##".($orderRequiredQnty/$row[csf('budget_qty')])*$balance_qty_for_ration.",";
				$hdn_po_ratio_qnty.=$poID."##".($orderRequiredQnty/$row[csf('reqn_qty')])*$balance_qty_for_ration.",";

				$hdn_po_ratio_ref.=$poID."##".$orderRequiredQnty.",";

				//$orderRecvRequiredQnty=$prev_recv_qnty_arr2[$jobNo][$bookingNo][$styleRef][$poID]['qnty'];
				//$hdn_po_recv_ratio_ref.=$poID."=".$orderRecvRequiredQnty.",";

				//$po_ratio=$orderRecvRequiredQnty*1/$rev_req_qnty_total*1;
				//$iss_qty+=$finish_qnty_array[$poID];

			}
			$hdn_po_ratio_ref=chop($hdn_po_ratio_ref,",");
			$hdn_po_ratio_qnty=chop($hdn_po_ratio_qnty,",");



		}
		else
		{
			$year=$po_data_array[$row[csf('po_id')]]['year'];
			$job_prefix=$po_data_array[$row[csf('po_id')]]['prefix'];
			$poNO=$po_data_array[$row[csf('po_id')]]['po_no'];

			$budget_qty=$budget_qty_array[$row[csf('buyer_id')]][$row[csf('po_id')]][$row[csf('job_no')]][$row[csf('item_id')]][$row[csf('body_part')]][$row[csf('determination_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('color_id')]][$row[csf('size_id')]];
			$prevRcvQnty = $prevRcvQntyArr[$row[csf('buyer_id')]][$row[csf('po_id')]][$row[csf('job_no')]][$row[csf('item_id')]][$row[csf('body_part')]][$row[csf('determination_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('color_id')]][$row[csf('size_id')]];
		}
		
		
		$avg_cons=$avg_cons_data[$row[csf('job_no')]][$row[csf('body_part')]][$row[csf('determination_id')]];
		$uomData=$uom_data[$row[csf('job_no')]][$row[csf('body_part')]][$row[csf('determination_id')]];
		$balance_qty = $budget_qty - $prevRcvQnty;
		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		?>
		<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>"> 
            <td width="40"><? echo $i; ?></td>
            <td width="60" style="word-break:break-all;"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
            <td width="50"><p><? echo $year; ?></p></td>
            <td width="60"><p><? echo $job_prefix; ?></p></td>

            <td width="100"><p><? echo $style_ref_no; ?></p></td>

            <td width="80" style="word-break:break-all;"><p><? echo $poNO; ?></p></td>
            <td width="100" style="word-break:break-all;"><p><? echo $garments_item[$row[csf('item_id')]]; ?></p></td>
            <td width="100" style="word-break:break-all;"><? echo $body_part[$row[csf('body_part')]]; ?></td>
            <td width="150" style="word-break:break-all;"><? echo $constructtion_arr[$row[csf('determination_id')]].", ".$composition_arr[$row[csf('determination_id')]]; ?></td>

            <td width="60" style="word-break:break-all;"><? echo $row[csf('fabric_ref')]; ?></td>
            <td width="60" style="word-break:break-all;"><? echo $row[csf('rd_no')]; ?></td>
            <td width="60" style="word-break:break-all;" id="gsm<? echo $i; ?>"><? echo $row[csf('gsm')]; ?></td>
            <td width="60" style="word-break:break-all;"><? echo $fabric_weight_type[$row[csf('weight_type')]]; ?></td>
            <td width="60" style="word-break:break-all;" id="dia<? echo $i; ?>"><? echo $row[csf('dia')]; ?></td>
            <td width="60" style="word-break:break-all;"><? echo $row[csf('cutable_width')]; ?></td>
           
            <td width="80" style="word-break:break-all;"><? echo $color_arr[$row[csf('color_id')]]; ?></td>
            <td width="60"><? echo $sizeArr[$row[csf('size_id')]]; ?></td>
            <td width="80" align="right" title="id=<? echo $uomData;?>"><? echo $unit_of_measurement[$uomData]; ?></td>
            <td width="80" align="right"><? echo $avg_cons; ?></td>
            <td width="80" align="right"><? echo number_format($budget_qty,2,'.',''); ?></td>
            <td width="80" align="right"><? echo number_format($prevRcvQnty,2,'.',''); ?></td>
            <td align="center">
            	<input type="text" class="text_boxes_numeric" style="width:80px" id="reqsnQty<? echo $i; ?>" name="reqsnQty[]" value="<? echo number_format($row[csf('reqn_qty')],2,'.',''); ?>" onBlur="fn_check_qnty(this.id,this.value,'<? echo number_format($budget_qty,2,'.',''); ?>',<? echo $i; ?>)"/>
                <input type="hidden" value="<? echo $row[csf('buyer_id')]; ?>" id="buyerId<? echo $i; ?>" name="buyerId[]"/>
                <input type="hidden" value="<? echo $row[csf('po_id')]; ?>" id="poId<? echo $i; ?>" name="poId[]"/>
                <input type="hidden" value="<? echo $row[csf('determination_id')]; ?>" id="deterId<? echo $i; ?>" name="deterId[]"/>
                <input type="hidden" value="<? echo $row[csf('color_id')]; ?>" id="colorId<? echo $i; ?>" name="colorId[]"/>
                <input type="hidden" value="<? echo $row[csf('item_id')]; ?>" id="itemId<? echo $i; ?>" name="itemId[]"/>
                <input type="hidden" value="<? echo $row[csf('body_part')]; ?>" id="bodyPartId<? echo $i; ?>" name="bodyPartId[]"/>
                <input type="hidden" value="<? echo $row[csf('job_no')]; ?>" id="jobNo<? echo $i; ?>" name="jobNo[]"/>
                <input type="hidden" value="<? echo $row[csf('size_id')]; ?>" id="sizeId<? echo $i; ?>" name="sizeId[]"/>
                <input type="hidden" value="<? echo $row[csf('id')]; ?>" id="dtlsId<? echo $i; ?>" name="dtlsId[]"/>

                <input type="hidden" value="<? echo $row[csf('fabric_ref')]; ?>" id="fabricRef<? echo $i; ?>" name="fabricRef[]"/>
                <input type="hidden" value="<? echo $row[csf('rd_no')]; ?>" id="rdNo<? echo $i; ?>" name="rdNo[]"/>
                <input type="hidden" value="<? echo $row[csf('weight_type')]; ?>" id="weightType<? echo $i; ?>" name="weightType[]"/>
                <input type="hidden" value="<? echo $row[csf('cutable_width')]; ?>" id="cutableWidth<? echo $i; ?>" name="cutableWidth[]"/>

                <input type="hidden" value="<? echo number_format($prevRcvQnty,2,'.',''); ?>" id="prevReqQty<? echo $i; ?>" name="prevReqQty[]"/>
                <input type="hidden" value="<? echo $hdn_po_ratio_ref; ?>" id="hdnPoRatioRef<? echo $i; ?>" name="hdnPoRatioRef[]"/>
                <input type="hidden" value="<? echo number_format($budget_qty,2,'.',''); ?>" id="hdnBudgtQty<? echo $i; ?>" name="hdnBudgtQty[]"/>
                <input type="hidden" value="<? echo $hdn_po_ratio_qnty; ?>" id="hdnPoRatioQty<? echo $i; ?>" name="hdnPoRatioQty[]"/>
            </td>
        </tr>
		<?		
		$i++;
	}
	echo "<script>document.getElementById('hidden_buyer_id').value='".$row[csf('buyer_id')]."';document.getElementById('cbo_company_id').disabled=true;</script>";			
	exit();
}
//---------------------------
if($action=="grey_delivery_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	//$dealing_merchant_library=return_library_array( "select id,team_member_name from lib_mkt_team_member_info", "id", "team_member_name"  );
	//$sample_library=return_library_array( "select id, sample_name from lib_sample", "id", "sample_name");
	$size_library=return_library_array( "select id, size_name from lib_size", "id", "size_name"  );
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
	$order_number=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number"  );



	//select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name
	?>

		<div style="width:1360px;">
	    
	     <table width="1360" cellspacing="0" border="0" align="right">
	        <tr>
	            <td colspan="6" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
	        </tr>
	        <tr class="form_caption">
	            <td colspan="6" align="center">
					<?
					//echo "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]";
						$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
						foreach ($nameArray as $result)
						{ 
												 
							 echo $result[csf('plot_no')].', '.$result[csf('level_no')].', '.$result[csf('road_no')].', '.$result[csf('block_no')].', '.$result[csf('city')].', '.$result[csf('zip_code')].', '.$result[csf('province')].', '.$country_arr[$result[csf('country_id')]]; ?><br> 
							 <? echo $result[csf('email')];?> 
							 <? echo $result[csf('website')];
						}
						
						$sql="SELECT a.id,a.reqn_number,a.company_id,a.reqn_date,b.job_no,c.style_ref_no
						  from pro_fab_reqn_for_cutting_mst a,pro_fab_reqn_for_cutting_dtls b,wo_po_details_master c
						  where  a.reqn_number='$data[1]' and a.id=b.mst_id and b.job_no=c.job_no  and a.is_deleted=0 and a.status_active=1 
						  group by  a.id,a.reqn_number,a.company_id,a.reqn_date,b.job_no,c.style_ref_no";
						//echo $sql;
						$dataArray=sql_select($sql);
		
	                ?> 
	            </td>
	        </tr>
	        <tr>
	            <td colspan="6" align="center" style="font-size:medium"><strong><u>Finish Fabric Requisition for Cutting</u></strong></td>
	        </tr>
	        <tr> <td colspan="6">&nbsp;</td></tr>
	         <tr>
	        	<td width="250" align="left" valign="top" colspan="2">
	            	<table align="left" cellspacing="0" border="0" width="90%" >
	                <tr>
	        	<td width="100"><strong>Company Name :</strong></td> <td width="120" align="left">:&nbsp<? echo $company_library[$dataArray[0][csf('company_id')]];?></td> <td width="100" align="left"><strong>Requisition No :</strong></td> <td width="120">:&nbsp<? echo $dataArray[0][csf('reqn_number')];?></td><td width="100" align="left"><strong>Req Date :</strong></td> <td width="100">:&nbsp<? echo change_date_format($dataArray[0][csf('reqn_date')]);?></td>
	        </tr>
	        <tr>  
	            <td width="120" align="left"><strong> Lay Plan Cutting No :</strong></td> <td width="100">:&nbsp<? echo $data[3];?></td>	          
	        </tr>
	        <!-- <tr>     	
	            
	        </tr> -->
	        
	        </table>
	        </td>
	        </tr>	        
	         <tr> <td colspan="6">&nbsp;</td></tr>
	        <tr>
	        	<td align="left" valign="top" colspan="2">
	            	<table align="left" cellspacing="0" border="1" width="100%" class="rpt_table" rules="all">
	                    <tbody>
	                        <tr>
	                           <th width="30" align="center">SL</th>
	                           <th width="100" align="center">Job No</th> 
	                           <th width="100" align="center">Style Ref. No.</th> 
	                           <th width="100" align="center">Buyer</th> 
	                           <th width="120" align="center">Order No</th>
	                           <th width="150" align="center">Body Part</th>
	                           <th width="200" align="center">Fabric Des.</th>
	                           <th width="80" align="center">GSM</th>
	                           <th width="40" align="center">Dia</th>
	                           <th width="40" align="center">Gmts. Color</th>
	                           <th width="70" align="center">Uom</th>
	                           <th width="70" align="center">Consumption</th>
	                           <th width="80" align="center">Int. Ref.</th>
	                           <!-- <th width="80" align="center">Batch No.</th> -->
	                           <th width="80" align="center">Budget Qty</th>
	                           <th width="100" align="center">Reqn. Qty</th>
	                        </tr>
	                        
	                        <?
							if($db_type==2) 
							{
								 $year=" TO_CHAR(a.insert_date,'YYYY') as year ";
								 $null_cond="NVL";
							}
						    else if($db_type==0) 
							{ 
								$year=" year(a.insert_date) as year ";
								$null_cond="IFNULL";
							}
							$all_po_id='';
							$sql="SELECT id, buyer_id, po_id, job_no, item_id, body_part, determination_id, gsm, dia, color_id, size_id, reqn_qty from pro_fab_reqn_for_cutting_dtls where status_active=1 and is_deleted=0 and mst_id=$data[2]";
							$result=sql_select($sql);
							foreach ($result as $row)
							{
								$all_po_id.=$row[csf('po_id')].",";
							}
							$all_po_id=substr($all_po_id,0,-1);

							// ======================= GETTING BATCH NO ===========================
							$batch_arr = [];
							$batch_sql = "SELECT a.batch_no,b.po_id from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and b.po_id in($all_po_id) and a.status_active=1 and a.is_deleted=0";
							$batch_sql_res = sql_select($batch_sql);
							foreach ($batch_sql_res as $key => $val) 
							{
								if($check_batch_arr['batch_no'] != $val[csf('batch_no')])
								{
									$batch_arr[$val[csf('po_id')]] .= $val[csf('batch_no')].",";
									$check_batch_arr['batch_no'] = $val[csf('batch_no')];
								}
							}
							// print_r($batch_arr);
							$budget_qty_array=array();$avg_cons_data=array();$uom_data=array(); 						
							$sql_budget= sql_select("SELECT a.job_no,a.style_ref_no, a.job_no_prefix_num, a.buyer_name, $year, b.id as po_id, b.po_number, c.item_number_id as item_id, c.color_number_id as color_id, $null_cond(c.size_number_id,0) as size_id, e.body_part_id, e.lib_yarn_count_deter_id, e.gsm_weight, d.dia_width, sum((d.cons/d.pcs)*c.plan_cut_qnty) as budget_qty,b.grouping,e.uom, e.avg_cons
								from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls d, wo_pre_cost_fabric_cost_dtls e
								where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.po_break_down_id=d.po_break_down_id and c.color_number_id=d.color_number_id and c.size_number_id=d.gmts_sizes and d.pre_cost_fabric_cost_dtls_id=e.id and c.job_no_mst=e.job_no and c.item_number_id=e.item_number_id and a.is_deleted=0 and a.status_active=1 and b.id in($all_po_id) 
								group by a.job_no,a.style_ref_no, a.job_no_prefix_num, a.buyer_name,a.insert_date, b.id, b.po_number, c.item_number_id, c.color_number_id, nvl(c.size_number_id,0), e.body_part_id, e.lib_yarn_count_deter_id, e.gsm_weight, d.dia_width,b.grouping,e.uom, e.avg_cons");
												
							foreach ($sql_budget as $row)
							{
								$budget_qty_array[$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('job_no')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('size_id')]]=$row[csf('budget_qty')];		
								$int_ref_array[$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('job_no')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('size_id')]]=$row[csf('grouping')];
								$avg_cons_data[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]=$row[csf('avg_cons')];
								$uom_data[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]=$row[csf('uom')];

								//$uom_data[$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('job_no')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('size_id')]]=$row[csf('uom')];
								//$avg_cons_data[$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('job_no')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('size_id')]]=$row[csf('avg_cons')];									

								$job_array[$row[csf('job_no')]]=$row[csf('job_no')];		
								$style_ref_array[$row[csf('job_no')]]=$row[csf('style_ref_no')];	
							}
							// ============================ for batch =====================================
							$sql_batch = "SELECT a.batch_no, b.po_id from pro_batch_create_mst a,pro_batch_create_dtls b where a.id=b.mst_id and a.status_active=1";
							$sql_batch_res = sql_select($sql_batch);
							$batchArr = [];
							foreach($sql_batch_res as $val)
							{
								$batchArr[$val[csf('po_id')]] = $val[csf('batch_no')];
							}
					
							
							$composition_arr=array(); $constructtion_arr=array();
						 	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
							$data_array=sql_select($sql_deter);
							foreach( $data_array as $row )
							{
								$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
								$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
							}
							
							// echo "<pre>";
							// print_r($avg_cons_data);die;
							
	                      $sql_qry="SELECT a.id,a.reqn_number,a.company_id,a.reqn_date,b.job_no,b.buyer_id,b.po_id,b.gsm,b.dia,b.body_part,b.color_id,b.size_id,b.determination_id,sum(b.reqn_qty) as reqn_qty,b.item_id
	                      from pro_fab_reqn_for_cutting_mst a,pro_fab_reqn_for_cutting_dtls b
	                      where  a.reqn_number='$data[1]' and a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 group by a.id,a.reqn_number, a.company_id, a.reqn_date,
						       b.job_no, b.buyer_id, b.po_id, b.gsm, b.dia, b.body_part, b.color_id, b.size_id, b.determination_id, b.item_id";
						       //echo $sql_qry;
							$result=sql_select($sql_qry);

							$i=1; //$total_gmts_pcs=0;$total_bhqty=0;$k=0;
							foreach($result as $row)
							{
								
								$budget_qty=$budget_qty_array[$row[csf('buyer_id')]][$row[csf('po_id')]][$row[csf('job_no')]][$row[csf('item_id')]][$row[csf('body_part')]][$row[csf('determination_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('color_id')]][$row[csf('size_id')]];
								$int_reference=$int_ref_array[$row[csf('buyer_id')]][$row[csf('po_id')]][$row[csf('job_no')]][$row[csf('item_id')]][$row[csf('body_part')]][$row[csf('determination_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('color_id')]][$row[csf('size_id')]];
								$batch=$batch_array[$row[csf('buyer_id')]][$row[csf('po_id')]][$row[csf('job_no')]][$row[csf('item_id')]][$row[csf('body_part')]][$row[csf('determination_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('color_id')]][$row[csf('size_id')]];

								$avg_cons=$avg_cons_data[$row[csf('job_no')]][$row[csf('body_part')]][$row[csf('determination_id')]];
								$uomData=$uom_data[$row[csf('job_no')]][$row[csf('body_part')]][$row[csf('determination_id')]];										
								$style_ref_no = $style_ref_array[$row[csf('job_no')]]							

								?>
		                        <tr>
		                            <?
									$k++;
									?>
		                            <td  align="center"><? echo $k;?></td>
		                            <td  align="left"><? echo $row[csf('job_no')];?></td>
		                            <td  align="left"><? echo $style_ref_no;?></td>
		                            <td  align="left"><? echo $buyer_library[$row[csf('buyer_id')]];?></td>
		                            <td title="<? echo $row[csf('po_id')];?>"  align="left"><? echo $order_number[$row[csf('po_id')]];?></td>
		                            <td  align="left"><? echo $body_part[$row[csf('body_part')]];?></td>
		                            <td  align="left"><? echo $constructtion_arr[$row[csf('determination_id')]].", ".$composition_arr[$row[csf('determination_id')]];?></td>
		                            <td  align="center"><? echo $row[csf('gsm')];?></td>
		                            <td  align="center"><? echo  $row[csf('dia')];?></td>
		                            <td  align="center"><? echo $color_library[$row[csf('color_id')]];?></td>
		                            <td  align="center" title="id=<? echo $uomData;?>"><? echo $unit_of_measurement[$uomData]; ?></td>
		                            <td  align="center"><? echo $avg_cons;?></td>
		                            <!-- <td  align="center"><? //echo $size_library[$row[csf('size_id')]];?></td> -->
		                            <td  align="center"><? echo  $int_reference;?></td>
		                            <!-- <td  align="center"><? //echo  $batchArr[$row[csf('po_id')]];//$batch;?></td> -->
		                            <td  align="center"><? echo number_format($budget_qty,2);?></td>
		                            <td  align="center"><? echo $row[csf('reqn_qty')];?></td>
		                            
		                        </tr>
		                        <?
							}
							?>
	                    </tbody>
	                    
	               </table>
	             </td>
	        </tr>
	         <tr> <td colspan="6"><? echo signature_table(128, $data[0],1360,$data[4]); ?></td></tr>
	        </table>
			
	    
	    </div>
	    
	<?
 
}

//---------------------------
if($action=="grey_delivery_print2")
{
	// echo load_html_head_contents("Cut and Lay Info","../../../", 1, 1, '','1','');
    extract($_REQUEST);
	$data=explode('*',$data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	//$dealing_merchant_library=return_library_array( "select id,team_member_name from lib_mkt_team_member_info", "id", "team_member_name"  );
	//$sample_library=return_library_array( "select id, sample_name from lib_sample", "id", "sample_name");
	$size_library=return_library_array( "select id, size_name from lib_size", "id", "size_name"  );
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
	$order_number=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number"  );



	//select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name
	?>

		<div style="width:1360px;">
	    
	     <table width="1360" cellspacing="0" border="0" align="right">
	        <tr>
	            <td colspan="8" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
	        </tr>
	        <tr class="form_caption">
	            <td colspan="8" align="center">
					<?
					//echo "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]";
						$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
						foreach ($nameArray as $result)
						{ 
												 
							 echo $result[csf('plot_no')].', '.$result[csf('level_no')].', '.$result[csf('road_no')].', '.$result[csf('block_no')].', '.$result[csf('city')].', '.$result[csf('zip_code')].', '.$result[csf('province')].', '.$country_arr[$result[csf('country_id')]]; ?><br> 
							 <? echo $result[csf('email')];?> 
							 <? echo $result[csf('website')];
						}
						
						$sql="SELECT a.id,a.reqn_number,a.company_id,a.reqn_date,b.job_no,c.style_ref_no
						  from pro_fab_reqn_for_cutting_mst a,pro_fab_reqn_for_cutting_dtls b,wo_po_details_master c
						  where  a.reqn_number='$data[1]' and a.id=b.mst_id and b.job_no=c.job_no  and a.is_deleted=0 and a.status_active=1 
						  group by  a.id,a.reqn_number,a.company_id,a.reqn_date,b.job_no,c.style_ref_no";
						//echo $sql;
						$dataArray=sql_select($sql);
		
	                ?> 
	            </td>
	        </tr>
	        <tr>
	            <td colspan="8" align="center" style="font-size:medium"><strong><u>Finish Fabric Requisition for Cutting</u></strong></td>
	        </tr>
	        <tr> <td colspan="8">&nbsp;</td></tr>
	         <tr>
	        	<td align="left" valign="top" colspan="8">
	            	<table align="left" cellspacing="0" border="0" width="100%" >
		                <tr>
				        	<td width="8.5%"><strong>Company Name :</strong></td>
				        	<td width="16.5%" align="left">&nbsp<? echo $company_library[$dataArray[0][csf('company_id')]];?></td> 
				        	<td width="8.5%" align="left"><strong>Requisition No :</strong></td> 
				        	<td width="16.5%">&nbsp<? echo $dataArray[0][csf('reqn_number')];?></td>
				        	<td width="8.5%" align="left"><strong>Req Date :</strong></td> 
				        	<td width="16.5%">&nbsp<? echo change_date_format($dataArray[0][csf('reqn_date')]);?></td>
				        	<td width="8.5%" align="left"><strong>Lay Plan Cutting No :</strong></td> 
				        	<td width="16.5%">&nbsp<? echo $data[3];?></td>
				        </tr>
				        <tr>  
				            <td colspan="8" align="left"><strong> Remarks :</strong></td> <td>:&nbsp</td>	          
				        </tr>			        
			        </table>
		        </td>
	        </tr>	        
	         <tr> <td colspan="8">&nbsp;</td></tr>
	        <tr>
	        	<td align="left" valign="top" colspan="8">
	            	<table align="left" cellspacing="0" border="1" width="100%" class="rpt_table" rules="all">
	                    <tbody>
	                        <tr>
	                           <th width="30" align="center">SL</th>
	                           <th width="100" align="center">Buyer</th> 
	                           <th width="100" align="center">Job Year</th> 
	                           <th width="100" align="center">Style Ref. No.</th>
	                           <th width="100" align="center">Job No</th>  
	                           <th width="120" align="center">Order No</th>
	                           <th width="150" align="center">Body Part</th>
	                           <th width="200" align="center">Fabric Des.</th>
	                           <th width="80" align="center">GSM</th>
	                           <th width="40" align="center">Dia</th>
	                           <th width="40" align="center">Gmts. Color</th>
	                           <th width="40" align="center">Size</th>
	                           <th width="70" align="center">Uom</th>
	                           <th width="70" align="center">Consumption</th>
	                           <th width="80" align="center">Budget Qty</th>
	                           <th width="100" align="center">Reqn. Qty</th>
	                        </tr>
	                        
	                        <?
							if($db_type==2) 
							{
								 $year=" TO_CHAR(a.insert_date,'YYYY') as year ";
								 $null_cond="NVL";
							}
						    else if($db_type==0) 
							{ 
								$year=" year(a.insert_date) as year ";
								$null_cond="IFNULL";
							}
							$all_po_id='';
							$sql="SELECT id, buyer_id, po_id, job_no, item_id, body_part, determination_id, gsm, dia, color_id, size_id, reqn_qty from pro_fab_reqn_for_cutting_dtls where status_active=1 and is_deleted=0 and mst_id=$data[2]";
							$result=sql_select($sql);
							foreach ($result as $row)
							{
								$all_po_id.=$row[csf('po_id')].",";
							}
							$all_po_id=substr($all_po_id,0,-1);

							// ======================= GETTING BATCH NO ===========================
							$batch_arr = [];
							$batch_sql = "SELECT a.batch_no,b.po_id from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and b.po_id in($all_po_id) and a.status_active=1 and a.is_deleted=0";
							$batch_sql_res = sql_select($batch_sql);
							foreach ($batch_sql_res as $key => $val) 
							{
								if($check_batch_arr['batch_no'] != $val[csf('batch_no')])
								{
									$batch_arr[$val[csf('po_id')]] .= $val[csf('batch_no')].",";
									$check_batch_arr['batch_no'] = $val[csf('batch_no')];
								}
							}
							// print_r($batch_arr);
							$budget_qty_array=array();$avg_cons_data=array();$uom_data=array(); 						
							$sql_budget= sql_select("SELECT a.job_no,a.style_ref_no, a.job_no_prefix_num, a.buyer_name, $year, b.id as po_id, b.po_number, c.item_number_id as item_id, c.color_number_id as color_id, $null_cond(c.size_number_id,0) as size_id, e.body_part_id, e.lib_yarn_count_deter_id, e.gsm_weight, d.dia_width, sum((d.cons/d.pcs)*c.plan_cut_qnty) as budget_qty,b.grouping,e.uom, e.avg_cons
								from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls d, wo_pre_cost_fabric_cost_dtls e
								where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.po_break_down_id=d.po_break_down_id and c.color_number_id=d.color_number_id and c.size_number_id=d.gmts_sizes and d.pre_cost_fabric_cost_dtls_id=e.id and c.job_no_mst=e.job_no and c.item_number_id=e.item_number_id and a.is_deleted=0 and a.status_active=1 and b.id in($all_po_id) 
								group by a.job_no,a.style_ref_no, a.job_no_prefix_num, a.buyer_name,a.insert_date, b.id, b.po_number, c.item_number_id, c.color_number_id, nvl(c.size_number_id,0), e.body_part_id, e.lib_yarn_count_deter_id, e.gsm_weight, d.dia_width,b.grouping,e.uom, e.avg_cons");
												
							foreach ($sql_budget as $row)
							{
								$budget_qty_array[$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('job_no')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('size_id')]]=$row[csf('budget_qty')];		
								$int_ref_array[$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('job_no')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('size_id')]]=$row[csf('grouping')];
								$avg_cons_data[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]=$row[csf('avg_cons')];
								$uom_data[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]=$row[csf('uom')];
								$year_data[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]=$row[csf('year')];

								//$uom_data[$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('job_no')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('size_id')]]=$row[csf('uom')];
								//$avg_cons_data[$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('job_no')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('size_id')]]=$row[csf('avg_cons')];									

								$job_array[$row[csf('job_no')]]=$row[csf('job_no')];		
								$style_ref_array[$row[csf('job_no')]]=$row[csf('style_ref_no')];	
							}
							// ============================ for batch =====================================
							$sql_batch = "SELECT a.batch_no, b.po_id from pro_batch_create_mst a,pro_batch_create_dtls b where a.id=b.mst_id and a.status_active=1";
							$sql_batch_res = sql_select($sql_batch);
							$batchArr = [];
							foreach($sql_batch_res as $val)
							{
								$batchArr[$val[csf('po_id')]] = $val[csf('batch_no')];
							}
					
							
							$composition_arr=array(); $constructtion_arr=array();
						 	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
							$data_array=sql_select($sql_deter);
							foreach( $data_array as $row )
							{
								$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
								$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
							}
							
							// echo "<pre>";
							// print_r($avg_cons_data);die;
							
	                      $sql_qry="SELECT a.id,a.reqn_number,a.company_id,a.reqn_date,b.job_no,b.buyer_id,b.po_id,b.gsm,b.dia,b.body_part,b.color_id,b.size_id,b.determination_id,sum(b.reqn_qty) as reqn_qty,b.item_id
	                      from pro_fab_reqn_for_cutting_mst a,pro_fab_reqn_for_cutting_dtls b
	                      where  a.reqn_number='$data[1]' and a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 group by a.id,a.reqn_number, a.company_id, a.reqn_date,
						       b.job_no, b.buyer_id, b.po_id, b.gsm, b.dia, b.body_part, b.color_id, b.size_id, b.determination_id, b.item_id";
						       //echo $sql_qry;
							$result=sql_select($sql_qry);

							$data_array = array();
							foreach($result as $row)
							{
								$data_array[$row[csf('buyer_id')]][$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('item_id')]][$row[csf('body_part')]][$row[csf('determination_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('color_id')]][$row[csf('size_id')]]['reqn_qty'] += $row[csf('reqn_qty')];
							}

							$i=1; 
							$total_budget_qty = 0;
							$total_req_qty = 0;
							foreach($data_array as $buyer=>$buyer_data)
							{
								foreach ($buyer_data as $job => $job_data) 
								{
									foreach ($job_data as $po => $po_data) 
									{
										foreach ($po_data as $item => $item_data) 
										{
											foreach ($item_data as $body => $body_data) 
											{
												foreach ($body_data as $deterId => $deter_data) 
												{
													foreach ($deter_data as $gsm => $gsm_data) 
													{
														foreach ($gsm_data as $dia => $dia_data) 
														{
															foreach ($dia_data as $color => $color_data) 
															{
																$color_budget_qty = 0;
																$color_req_qty = 0;
																foreach ($color_data as $size => $row) 
																{																
																	$budget_qty=$budget_qty_array[$buyer][$po][$job][$item][$body][$deterId][$gsm][$dia][$color][$size];
																	$int_reference=$int_ref_array[$buyer][$po][$job][$item][$body][$deterId][$gsm][$dia][$color][$size];
																	$batch=$batch_array[$buyer][$po][$job][$item][$body][$deterId][$gsm][$dia][$color][$size];

																	$avg_cons=$avg_cons_data[$job][$body][$deterId];
																	$uomData=$uom_data[$job][$body][$deterId];										
																	$year=$year_data[$job][$body][$deterId];										
																	$style_ref_no = $style_ref_array[$job]							

																	?>
											                        <tr>
											                            <?
																		$k++;
																		?>
											                            <td  align="center"><? echo $k;?></td>
											                            <td  align="left"><? echo $buyer_library[$buyer];?></td>
											                            <td  align="center"><? echo $year;?></td>
											                            <td  align="left"><? echo $style_ref_no;?></td>
											                            <td  align="left"><? echo $job;?></td>
											                            <td title="<? echo $po;?>"  align="left"><? echo $order_number[$po];?></td>
											                            <td  align="left"><? echo $body_part[$body];?></td>
											                            <td  align="left"><? echo $constructtion_arr[$deterId].", ".$composition_arr[$deterId];?></td>
											                            <td  align="center"><? echo $gsm;?></td>
											                            <td  align="center"><? echo  $dia;?></td>
											                            <td  align="center"><? echo $color_library[$color];?></td>
											                            <td  align="center"><? echo $size_library[$size];?></td>
											                            <td  align="center" title="id=<? echo $uomData;?>"><? echo $unit_of_measurement[$uomData]; ?></td>
											                            <td  align="center"><? echo $avg_cons;?></td>
											                            <td  align="right"><? echo number_format($budget_qty,4);?></td>
											                            <td  align="right"><? echo number_format($row['reqn_qty'],4);?></td>
											                            
											                        </tr>
											                        <?
											                        $k++;
											                        $color_budget_qty += $budget_qty;
																	$color_req_qty += $row['reqn_qty'];
											                        $total_budget_qty += $budget_qty;
																	$total_req_qty += $row['reqn_qty'];
																}
																?>
																<tr style="background: #dccdcd;font-size: 12;font-weight: bold;">
																	<td align="right" colspan="14">Total</td>
																	<td align="right"><?=number_format($color_budget_qty,4);?></td>
																	<td align="right"><?=number_format($color_req_qty,4);?></td>
																</tr>
																<?
															}
														}
													}
												}
											}
										}
									}
								}
							}
							?>
	                    </tbody>
	                    <tfoot>	                    	
							<tr style="background: #cddcdc;font-size: 12;font-weight: bold;">
								<td align="right" colspan="14">Grand Total</td>
								<td align="right"><?=number_format($total_budget_qty,4);?></td>
								<td align="right"><?=number_format($total_req_qty,4);?></td>
							</tr>
	                    </tfoot>
	                    
	               </table>
	             </td>
	        </tr>
	         <tr> <td colspan="8"><? echo signature_table(128, $data[0],1360,$data[4]); ?></td></tr>
	        </table>
			
	    
	    </div>
	    
	<?
 
}

if($action=="grey_delivery_print3")//for style level print 3
{
    extract($_REQUEST);
	$data=explode('*',$data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	//$dealing_merchant_library=return_library_array( "select id,team_member_name from lib_mkt_team_member_info", "id", "team_member_name"  );
	//$sample_library=return_library_array( "select id, sample_name from lib_sample", "id", "sample_name");
	$size_library=return_library_array( "select id, size_name from lib_size", "id", "size_name"  );
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
	$order_number=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number"  );



	//select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name
	?>

		<div style="width:1360px;">
	    
	     <table width="1360" cellspacing="0" border="0" align="right">
	        <tr>
	            <td colspan="6" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
	        </tr>
	        <tr class="form_caption">
	            <td colspan="6" align="center">
					<?
					//echo "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]";
						$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
						foreach ($nameArray as $result)
						{ 
												 
							 echo $result[csf('plot_no')].', '.$result[csf('level_no')].', '.$result[csf('road_no')].', '.$result[csf('block_no')].', '.$result[csf('city')].', '.$result[csf('zip_code')].', '.$result[csf('province')].', '.$country_arr[$result[csf('country_id')]]; ?><br> 
							 <? echo $result[csf('email')];?> 
							 <? echo $result[csf('website')];
						}
						
						$sql="SELECT a.id,a.reqn_number,a.company_id,a.reqn_date,b.job_no,c.style_ref_no
						  from pro_fab_reqn_for_cutting_mst a,pro_fab_reqn_for_cutting_dtls b,wo_po_details_master c
						  where  a.reqn_number='$data[1]' and a.id=b.mst_id and b.job_no=c.job_no  and a.is_deleted=0 and a.status_active=1 
						  group by  a.id,a.reqn_number,a.company_id,a.reqn_date,b.job_no,c.style_ref_no";
						//echo $sql;
						$dataArray=sql_select($sql);
		
	                ?> 
	            </td>
	        </tr>
	        <tr>
	            <td colspan="6" align="center" style="font-size:medium"><strong><u>Finish Fabric Requisition for Cutting</u></strong></td>
	        </tr>
	        <tr> <td colspan="6">&nbsp;</td></tr>
	         <tr>
	        	<td width="250" align="left" valign="top" colspan="2">
	            	<table align="left" cellspacing="0" border="0" width="90%" >
	                <tr>
	        	<td width="100"><strong>Company Name :</strong></td> <td width="120" align="left">:&nbsp<? echo $company_library[$dataArray[0][csf('company_id')]];?></td> <td width="100" align="left"><strong>Requisition No :</strong></td> <td width="120">:&nbsp<? echo $dataArray[0][csf('reqn_number')];?></td><td width="100" align="left"><strong>Req Date :</strong></td> <td width="100">:&nbsp<? echo change_date_format($dataArray[0][csf('reqn_date')]);?></td>
	        </tr>
	        <tr>  
	            <td width="120" align="left"><strong> Lay Plan Cutting No :</strong></td> <td width="100">:&nbsp<? echo $data[3];?></td>	          
	        </tr>
	        <!-- <tr>     	
	            
	        </tr> -->
	        
	        </table>
	        </td>
	        </tr>	        
	         <tr> <td colspan="6">&nbsp;</td></tr>
	        <tr>
	        	<td align="left" valign="top" colspan="2">
	            	<table align="left" cellspacing="0" border="1" width="100%" class="rpt_table" rules="all">
	                    <tbody>
	                        <tr>
	                           <th width="30" align="center">SL</th>
	                           <th width="100" align="center">Job No</th> 
	                           <th width="100" align="center">Style Ref. No.</th> 
	                           <th width="80" align="center">Int. Ref.</th>
	                           <th width="100" align="center">Buyer</th> 
	                    


	                    					

 <th width="80" align="center">Fabric Ref</th>
 <th width="80" align="center">RD No</th>
 <th width="80" align="center">Weight</th>
 <th width="80" align="center">Weight Type</th>
 <th width="80" align="center">Width</th>
 <th width="80" align="center">Cutable Width</th>


	                           <th width="150" align="center">Body Part</th>
	                           <th width="200" align="center">Fabric Des.</th>

	                           <th width="40" align="center">Gmts. Color</th>
	                           <th width="70" align="center">Uom</th>
	                           <th width="70" align="center">Consumption</th>
	                   
	                           <!-- <th width="80" align="center">Batch No.</th> -->
	                           <th width="80" align="center">Budget Qty</th>
	                           <th width="100" align="center">Reqn. Qty</th>
	                        </tr>
	                        
	                        <?
							if($db_type==2) 
							{
								 $year=" TO_CHAR(a.insert_date,'YYYY') as year ";
								 $null_cond="NVL";
							}
						    else if($db_type==0) 
							{ 
								$year=" year(a.insert_date) as year ";
								$null_cond="IFNULL";
							}
							$all_po_id='';
							$sql="SELECT id, buyer_id, po_id, job_no, item_id, body_part, determination_id, gsm, dia, color_id, size_id, reqn_qty from pro_fab_reqn_for_cutting_dtls where status_active=1 and is_deleted=0 and mst_id=$data[2]";
							$result=sql_select($sql);
							foreach ($result as $row)
							{
								$all_po_id.=$row[csf('po_id')].",";
							}
							$all_po_id=substr($all_po_id,0,-1);

							// ======================= GETTING BATCH NO ===========================
							$batch_arr = [];
							$batch_sql = "SELECT a.batch_no,b.po_id from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and b.po_id in($all_po_id) and a.status_active=1 and a.is_deleted=0";
							$batch_sql_res = sql_select($batch_sql);
							foreach ($batch_sql_res as $key => $val) 
							{
								if($check_batch_arr['batch_no'] != $val[csf('batch_no')])
								{
									$batch_arr[$val[csf('po_id')]] .= $val[csf('batch_no')].",";
									$check_batch_arr['batch_no'] = $val[csf('batch_no')];
								}
							}
							// print_r($batch_arr);
							$budget_qty_array=array();$avg_cons_data=array();$uom_data=array(); 						
							$sql_budget= sql_select("SELECT a.job_no,a.style_ref_no, a.job_no_prefix_num, a.buyer_name, $year, c.item_number_id as item_id, c.color_number_id as color_id, $null_cond(c.size_number_id,0) as size_id, e.body_part_id, e.lib_yarn_count_deter_id, e.gsm_weight, d.dia_width, sum((d.cons/d.pcs)*c.plan_cut_qnty) as budget_qty,b.grouping,e.uom, e.avg_cons
								from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls d, wo_pre_cost_fabric_cost_dtls e
								where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.po_break_down_id=d.po_break_down_id and c.color_number_id=d.color_number_id and c.size_number_id=d.gmts_sizes and d.pre_cost_fabric_cost_dtls_id=e.id and c.job_no_mst=e.job_no and c.item_number_id=e.item_number_id and a.is_deleted=0 and a.status_active=1 and b.id in($all_po_id) 
								group by a.job_no,a.style_ref_no, a.job_no_prefix_num, a.buyer_name,a.insert_date, c.item_number_id, c.color_number_id, nvl(c.size_number_id,0), e.body_part_id, e.lib_yarn_count_deter_id, e.gsm_weight, d.dia_width,b.grouping,e.uom, e.avg_cons");
												
							foreach ($sql_budget as $row)
							{
								$budget_qty_array[$row[csf('buyer_name')]][$row[csf('job_no')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('size_id')]]=$row[csf('budget_qty')];		
								$int_ref_array[$row[csf('buyer_name')]][$row[csf('job_no')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('size_id')]]=$row[csf('grouping')];
								$avg_cons_data[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]=$row[csf('avg_cons')];
								$uom_data[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]=$row[csf('uom')];

								//$uom_data[$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('job_no')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('size_id')]]=$row[csf('uom')];
								//$avg_cons_data[$row[csf('buyer_name')]][$row[csf('po_id')]][$row[csf('job_no')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('size_id')]]=$row[csf('avg_cons')];									

								$job_array[$row[csf('job_no')]]=$row[csf('job_no')];		
								$style_ref_array[$row[csf('job_no')]]=$row[csf('style_ref_no')];	
							}
							// ============================ for batch =====================================
							$sql_batch = "SELECT a.batch_no, b.po_id from pro_batch_create_mst a,pro_batch_create_dtls b where a.id=b.mst_id and a.status_active=1";
							$sql_batch_res = sql_select($sql_batch);
							$batchArr = [];
							foreach($sql_batch_res as $val)
							{
								$batchArr[$val[csf('po_id')]] = $val[csf('batch_no')];
							}
					
							
							$composition_arr=array(); $constructtion_arr=array();
						 	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
							$data_array=sql_select($sql_deter);
							foreach( $data_array as $row )
							{
								$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
								$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
							}
							
							// echo "<pre>";
							// print_r($avg_cons_data);die;
							
	                      $sql_qry="SELECT a.id,a.reqn_number,a.company_id,a.reqn_date,b.job_no,b.buyer_id,b.gsm,b.dia,b.body_part,b.color_id,b.size_id,b.determination_id,sum(b.reqn_qty) as reqn_qty,b.item_id,b.fabric_ref,b.rd_no,b.weight_type,b.cutable_width 
	                      from pro_fab_reqn_for_cutting_mst a,pro_fab_reqn_for_cutting_dtls b
	                      where  a.reqn_number='$data[1]' and a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 group by a.id,a.reqn_number, a.company_id, a.reqn_date,
						       b.job_no, b.buyer_id, b.gsm, b.dia, b.body_part, b.color_id, b.size_id, b.determination_id, b.item_id,b.fabric_ref,b.rd_no,b.weight_type,b.cutable_width ";
						       //echo $sql_qry;
							$result=sql_select($sql_qry);

							$i=1; //$total_gmts_pcs=0;$total_bhqty=0;$k=0;
							foreach($result as $row)
							{
								
								$budget_qty=$budget_qty_array[$row[csf('buyer_id')]][$row[csf('job_no')]][$row[csf('item_id')]][$row[csf('body_part')]][$row[csf('determination_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('color_id')]][$row[csf('size_id')]];
								$int_reference=$int_ref_array[$row[csf('buyer_id')]][$row[csf('job_no')]][$row[csf('item_id')]][$row[csf('body_part')]][$row[csf('determination_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('color_id')]][$row[csf('size_id')]];
								$batch=$batch_array[$row[csf('buyer_id')]][$row[csf('job_no')]][$row[csf('item_id')]][$row[csf('body_part')]][$row[csf('determination_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('color_id')]][$row[csf('size_id')]];

								$avg_cons=$avg_cons_data[$row[csf('job_no')]][$row[csf('body_part')]][$row[csf('determination_id')]];
								$uomData=$uom_data[$row[csf('job_no')]][$row[csf('body_part')]][$row[csf('determination_id')]];										
								$style_ref_no = $style_ref_array[$row[csf('job_no')]]							

								?>
		                        <tr>
		                            <?
									$k++;
									?>
		                            <td  align="center"><? echo $k;?></td>
		                            <td  align="left"><? echo $row[csf('job_no')];?></td>
		                            <td  align="left"><? echo $style_ref_no;?></td>
		                            <td  align="center"><? echo  $int_reference;?></td>
		                            <td  align="left"><? echo $buyer_library[$row[csf('buyer_id')]];?></td>
		                            
		                            <td  align="left"><? echo $row[csf('fabric_ref')];?></td>
		                            <td  align="left"><? echo $row[csf('rd_no')];?></td>
		                            <td  align="left"><? echo $row[csf('gsm')];?></td>
		                            <td  align="left"><? echo $fabric_weight_type[$row[csf('weight_type')]];?></td>
		                            <td  align="left"><? echo $row[csf('dia')];?></td>
		                            <td  align="left"><? echo $row[csf('cutable_width')];?></td>
		                           
		                            <td  align="left"><? echo $body_part[$row[csf('body_part')]];?></td>
		                            <td  align="left"><? echo $constructtion_arr[$row[csf('determination_id')]].", ".$composition_arr[$row[csf('determination_id')]];?></td>
		                            
		                            <td  align="center"><? echo $color_library[$row[csf('color_id')]];?></td>
		                            <td  align="center" title="id=<? echo $uomData;?>"><? echo $unit_of_measurement[$uomData]; ?></td>
		                            <td  align="center"><? echo $avg_cons;?></td>
		                            <!-- <td  align="center"><? //echo $size_library[$row[csf('size_id')]];?></td> -->
		                           
		                            <!-- <td  align="center"><? //echo  $batchArr[$row[csf('po_id')]];//$batch;?></td> -->
		                            <td  align="center"><? echo number_format($budget_qty,2);?></td>
		                            <td  align="center"><? echo number_format($row[csf('reqn_qty')],2);?></td>
		                            
		                        </tr>
		                        <?
							}
							?>
	                    </tbody>
	                    
	               </table>
	             </td>
	        </tr>
	         <tr> <td colspan="6"><? echo signature_table(128, $data[0],1360,$data[4]); ?></td></tr>
	        </table>
			
	    
	    </div>
	    
	<?
 
}
?>
