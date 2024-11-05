<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
//require_once('../../../includes/class4/class.conditions.php');
//require_once('../../../includes/class4/class.reports.php');
//require_once('../../../includes/class4/class.washes.php');

$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );     	 
	exit();
}

$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
//--------------------------------------------------------------------------------------------------------------------

if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $job_no;
	
	if($search_type==1 || $search_type==2 || $search_type==3 || $search_type==4 || $search_type==5) $search_cond=$job_no;
	//else if($search_type==2) $search_cond=$job_no;
	
	?>
	<script>
	
		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#hide_job_id").val(splitData[0]); 
			$("#hide_job_no").val(splitData[1]); 
			//return;
			parent.emailwindow.hide();
		}
	function js_set_value_multi_job(str)
	{
		
		//alert(str);
		$("#hidden_sys_number").val(str);
		//alert($("#hidden_sys_number").val())
		parent.emailwindow.hide(); 
	}
    </script>
    <script type="text/javascript">
	var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();
	 
		function toggle( x, origColor ) {
		var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		function check_all_data()
		{
			var row_num=$('#list_view tr').length-1;
			for(var i=1;  i<=row_num;  i++)
			{
				$("#tr_"+i).click();
			}
			
		}
		function js_set_value_multi_job(id)
		{
			var str=id.split("_");
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
			var strdt=str[2];
			str=str[1];
		
			if( jQuery.inArray(  str , selected_id ) == -1 ) {
				selected_id.push( str );
				selected_name.push( strdt );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str  ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i,1 );
			}
			var id = '';
			var ddd='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				ddd += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			ddd = ddd.substr( 0, ddd.length - 1 );
			$('#hide_job_id').val( id );
			$('#hide_job_no').val( ddd );
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
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					
                    <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                    <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />                
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
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
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" value="<? echo $search_cond;?>" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>'+'**'+'<? echo $search_type; ?>'+'**'+'<? echo $job_no; ?>'+'**'+'<? echo $po_no; ?>', 'create_job_no_search_list_view', 'search_div', 'production_status_summary_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    </td>
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
if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	$month_id=$data[5];
	$search_type=$data[6];
	$po_no=$data[8];
	$job_no=$data[7];
	//echo $month_id;
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
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
	if($search_by==2) $search_field="a.style_ref_no"; else $search_field="job_no";
	//if($job_no!='') $job_no_cond="and a.job_no_prefix_num=$job_no"; else $job_no_cond="";
	//$year="year(insert_date)";
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";
	
	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and year(a.insert_date)=$year_id"; else $year_cond="";	
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(a.insert_date,'YYYY')";
		if($year_id!=0) $year_cond="$year_field_con=$year_id"; else $year_cond="";	
	}
	if($search_type==1)
	{
		$search_filed="id,job_no_prefix_num";
	}
	else if($search_type==2)
	{
		$search_filed="po_id,po_number";
	}
	else if($search_type==4)
	{
		$search_filed="po_id,grouping";
	}
	else if($search_type==5)
	{
		$search_filed="po_id,file_no";
	}
	else
	{
		$search_filed="id,style_ref_no";
	}
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	$sql= "select a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,b.id as po_id,b.po_number,b.grouping,b.file_no, $year_field from wo_po_details_master a,wo_po_break_down b where   b.job_no_mst=a.job_no and a.status_active=1 and a.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond  order by job_no";
	
	if ($search_type==1) 
	{
		echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No,Po No,Internal Ref,File No", "120,130,80,60,150,100,100","900","240",0, $sql , "js_set_value_multi_job", "$search_filed", "", 1, "company_name,buyer_name,0,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no,po_number,grouping,file_no", "",'','0,0,0,0,0,0','',1) ;
	$sql_data=sql_select($sql);
	}
	else
	{
		echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No,Po No,Internal Ref,File No", "120,130,80,60,150,100,100","900","240",0, $sql , "js_set_value", "$search_filed", "", 1, "company_name,buyer_name,0,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no,po_number,grouping,file_no", "",'','0,0,0,0,0,0','') ;
	}


	
	

	exit(); 
} // Job Search end


$yarn_count_details=return_library_array( "select id,yarn_count from lib_yarn_count", "id", "yarn_count"  );
$season_details=return_library_array( "select id, season_name from  lib_buyer_season", "id", "season_name"  );
$color_details=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );


$tmplte=explode("**",$data);

if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;

if($action=="report_generate") 
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$report_type=str_replace("'","",$reporttype);
	$company_name=str_replace("'","",$cbo_company_name);
	
	$cbo_year=str_replace("'","",$cbo_year); 
	$job_no=str_replace("'","",$txt_job_no);
	$order_id=str_replace("'","",$txt_order_id);
	$order_num=str_replace("'","",$txt_order_no);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	
	$report_type=str_replace("'","",$report_type);
	$report_title=str_replace("'","",$report_title);
	
	$txt_internal_style_no=str_replace("'","",$txt_internal_style_no);
	$txt_internal_style_id=str_replace("'","",$txt_internal_style_id);
	$txt_file_no=str_replace("'","",$txt_file_no);
	$txt_file_id=str_replace("'","",$txt_file_id);

	$txt_style_no=str_replace("'","",$txt_style_no);
	$txt_style_id=str_replace("'","",$txt_style_id);
	
	if($txt_internal_style_no=="") $internal_style_no_cond=""; else $internal_style_no_cond=" and b.grouping='$txt_internal_style_no' ";
	if($txt_internal_style_id=="") $internal_style_id_cond=""; else $internal_style_id_cond=" and b.id in ($txt_internal_style_id) ";
	if($txt_file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='$txt_file_no' ";
	if($txt_file_id=="") $file_id_cond=""; else $file_id_cond=" and b.id in ($txt_file_id) ";


	if($txt_style_no=="") $style_no_cond=""; else $style_no_cond=" and a.style_ref_no='$txt_style_no' ";
	if($txt_style_id=="") $style_id_cond=""; else $style_id_cond=" and a.id in ($txt_style_id) ";

	
	if($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ($job_no) ";
	
	if($order_id!="" && $order_id!=0) $order_id_cond_trans=" and b.id in ($order_no)";
	else if ($order_num=="") $order_no_cond=""; else $order_no_cond=" and  b.po_number in ('$order_num') ";
	
	
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

	$cbo_year=str_replace("'","",$cbo_year);
	if($db_type==0)
	{
		if(trim($cbo_year)!=0) $year_cond=" and YEAR(a.insert_date)=$cbo_year"; else $year_cond="";
	}
	else if($db_type==2)
	{
		if(trim($cbo_year)!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year"; else $year_cond="";
	}
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	
	$date_cond='';	$c_date_cond='';
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			if($db_type==0)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
			}
			else if($db_type==2)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
			}
			$date_cond=" and b.pub_shipment_date between '$start_date' and '$end_date'";
			//$c_date_cond=" and c.country_ship_date between '$start_date' and '$end_date'";
			//$date_max_profit=" and applying_period_to_date between '$start_date' and '$end_date'";
		}
		ob_start();
	if($report_type==1)
	{
		?>
        <div style="width:3810px">
			<fieldset style="width:100%;">	
        <table id="table_header_1" class="rpt_table" width="3810" cellpadding="0" cellspacing="0" border="1" rules="all">
        <caption><strong><? echo $report_title.'<br>'.$company_arr[$company_name].'<br>'.$start_date.' To '.$end_date;
		?> </strong> </caption>
                <thead>
                <tr>
                	<th colspan="14" >Order Details </th>
                    <th colspan="12" >Fabric Detail </th>
                    <th colspan="19" >Gmt Production </th>
                </tr>
                    <tr>
                        <th width="30" rowspan="2">SL</th>
                        <th width="100" rowspan="2">Buyer</th>
                        <th width="70" rowspan="2">Season</th>
                        <th width="130" rowspan="2">Item Name</th>
                        <th width="100" rowspan="2">Style/Article</th>
                        <th width="100" rowspan="2">Job NO.</th>
                        <th width="80" rowspan="2">Order No.</th>
                        	<th width="80" rowspan="2">Internal Ref. No.</th>
                        	<th width="80" rowspan="2">File No.</th>
                        <th width="80" rowspan="2">Garments TOD</th>
                      
                        <th colspan="4">Fabric TOD</th>
                        <th width="170" rowspan="2">Fabrication</th> 
                        <th width="50" rowspan="2">Count</th>
                        <th width="50" rowspan="2">GSM</th>
                         
                        <th colspan="3">Knitting Status</th>
                        
                        <th width="100" rowspan="2">Fab. Color</th>
                        <th colspan="3">Dyeing Finishing Status</th>
                         
                        <th width="80" rowspan="2">Gmts Color</th>
                        <th width="100" rowspan="2">Order Qty(PCS)</th>
                        
                       
                        <th colspan="3">Cutting Status</th>
                        <th colspan="3">Print Status</th>
                        <th colspan="3">Embellishment Status</th>
                        <th colspan="2">Sewing Status</th>
                        <th colspan="3">Washing Status</th>
                        <th colspan="2">Finishing Status</th>
                      
                        <th width="80" rowspan="2">Shipment Qnty.</th> 
                        <th width="80" rowspan="2">Excess/ Short Qnty</th>
                        <th width="" rowspan="2">Actual Ex-Factory Date</th>
                        
                    </tr>
                    <tr>
                        <th width="80" title="23*80=2080 dwn">Knitting Delivery Start</th>
                        <th width="80">Knitting Delivery End</th>
                        <th width="80">Dyeing Delivery Start</th>
                        <th width="80">Dyeing Delivery End</th>
                        
                        <th width="80">Required Qty(kg)</th>
                        <th width="80">Complete</th>
                        <th width="80">Balance</th>
                        
                        <th width="80">Required Qty(kg)</th>
                        <th width="80">Complete</th>
                        <th width="80">Balance</th>
                        
                         <th width="80">Required Qty(Pcs)</th>
                        <th width="80">Complete</th>
                        <th width="80">Balance</th>
                        
                        <th width="80">Print Rcv Qty(Pcs)</th>
                        <th width="80">Print Delv.</th>
                        <th width="80">Balance</th>
                        
                        <th width="80">Emb. Rcv Qty(Pcs)</th>
                        <th width="80">Emb. Delv.</th>
                        <th width="80">Balance</th>
                        
                        <th width="80">Input</th>
                        <th width="80">Output</th>
                        
                        <th width="80" title="Wash Status">Wash Rcv Qty (Pcs)</th>
                        <th width="80">Wash Delv.</th>
                        <th width="80">Balance</th>
                        
                         <th width="80" title="Fin Status">Complete</th>
                        <th width="80">Balance</th>
                       
                       
                    </tr>
                </thead>
            </table>
            
            <div style="width:3810px; max-height:400px; overflow-y:scroll" id="scroll_body">
			<table class="rpt_table" width="3810" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
            <?
             	
				
				
					
				
					$sql_prod = "SELECT b.id as po_id,max(d.ex_factory_date) as prod_date,
				  sum(CASE WHEN d.entry_form!=85 THEN d.ex_factory_qnty ELSE 0 END) as ex_fact_qty,
				  sum(CASE WHEN d.entry_form=85 THEN d.ex_factory_qnty ELSE 0 END) as ret_ex_fact_qty
						 FROM wo_po_details_master a, wo_po_break_down b,pro_ex_factory_delivery_mst c,pro_ex_factory_mst d
				WHERE a.company_name=$company_name and a.job_no=b.job_no_mst and d.po_break_down_id=b.id  and c.id=d.delivery_mst_id and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 $date_cond $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond $internal_style_no_cond $style_no_cond $style_id_cond $internal_style_id_cond $file_no_cond $file_id_cond group by b.id  order by b.id ";
				$data_result_prod=sql_select($sql_prod);
				foreach($data_result_prod as $row)
				{
					$ship_prod_qty_arr[$row[csf('po_id')]]['prod_qty']+=$row[csf('ex_fact_qty')]-$row[csf('ret_ex_fact_qty')];
					$ship_prod_qty_arr[$row[csf('po_id')]]['prod_date']=$row[csf('prod_date')];
				}
				unset($data_result_prod);
				$sql_color = "SELECT b.id as po_id,c.color_number_id as color_id,c.order_quantity,c.plan_cut_qnty
						 FROM wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c
				WHERE a.company_name=$company_name and a.job_no=b.job_no_mst and c.po_break_down_id=b.id and a.job_no=c.job_no_mst and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   $date_cond $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond $internal_style_no_cond $internal_style_id_cond $style_no_cond $style_id_cond $file_no_cond $file_id_cond order by b.id ";
				$data_result_color=sql_select($sql_color);
				 $all_po_id="";
				foreach($data_result_color as $row)
				{
					$color_qty_arr[$row[csf('po_id')]][$row[csf('color_id')]]['po_qty_pcs']+=$row[csf('order_quantity')];
					$color_qty_arr[$row[csf('po_id')]][$row[csf('color_id')]]['plan_cut']+=$row[csf('plan_cut_qnty')];
					$color_po_qty_arr[$row[csf('po_id')]]['po_qty_pcs']+=$row[csf('order_quantity')];
					if($all_po_id=="") $all_po_id=$row[csf("po_id")]; else $all_po_id.=",".$row[csf("po_id")];
				}
				unset($data_result_color);
					 $poIds=implode(",",array_unique(explode(",",$all_po_id)));
				// $poIds=implode(",",$tmp_po);
						$poIds=chop($poIds,','); $po_cond_for_in=""; $po_cond_for_in2=""; 
						$po_ids=count(array_unique(explode(",",$poIds)));
						if($db_type==2 && $po_ids>1000)
						{
							$po_cond_for_in=" and (";
							$po_cond_for_in2=" and (";
							$poIdsArr=array_chunk(explode(",",$poIds),999);
							foreach($poIdsArr as $ids)
							{
								$ids=implode(",",$ids);
								$po_cond_for_in.=" b.po_breakdown_id in($ids) or"; 
								$po_cond_for_in2.=" b.id in($ids) or"; 
							}
							$po_cond_for_in=chop($po_cond_for_in,'or ');
							$po_cond_for_in.=")";
							$po_cond_for_in2=chop($po_cond_for_in2,'or ');
							$po_cond_for_in2.=")";
						}
						else
						{
							$poIds=implode(",",array_unique(explode(",",$poIds)));
							$po_cond_for_in=" and b.po_breakdown_id in($poIds)";
							$po_cond_for_in2=" and b.id in($poIds)";
						}
						$sql_gmtprod="SELECT b.id as po_id,d.color_number_id as gmt_color,
					  (CASE WHEN c.production_type=1 THEN e.production_qnty ELSE 0 END)  as cut_qty,
					  (CASE WHEN c.production_type=3  and c.embel_name=1 THEN e.production_qnty ELSE 0 END)  as recv_print_qty,
					  (CASE WHEN c.production_type=3  and c.embel_name=2 THEN e.production_qnty ELSE 0 END)  as recv_embrod_qty,
					  (CASE WHEN c.production_type=3  and c.embel_name=3 THEN e.production_qnty ELSE 0 END)  as recv_wash_qty,
					  (CASE WHEN c.production_type=2  and c.embel_name=1 THEN e.production_qnty ELSE 0 END)  as issue_print_qty,
					  (CASE WHEN c.production_type=2  and c.embel_name=2 THEN e.production_qnty ELSE 0 END)  as issue_embrod_qty,
					  (CASE WHEN c.production_type=2  and c.embel_name=3 THEN e.production_qnty ELSE 0 END)  as issue_wash_qty,
					  
					  (CASE WHEN c.production_type=4 THEN e.production_qnty ELSE 0 END)  as sew_in_qty,
					  (CASE WHEN c.production_type=5 THEN e.production_qnty ELSE 0 END)  as sew_out_qty,
					  (CASE WHEN c.production_type=11 THEN e.production_qnty ELSE 0 END)  as poly_qty,
					  (CASE WHEN c.production_type=8 THEN e.production_qnty ELSE 0 END)  as finish_qty
					   FROM wo_po_details_master a, wo_po_break_down b,pro_garments_production_mst c,pro_garments_production_dtls e,wo_po_color_size_breakdown d
         WHERE a.job_no=b.job_no_mst and c.po_break_down_id=d.po_break_down_id and c.po_break_down_id=b.id and  a.job_no=d.job_no_mst and b.job_no_mst=d.job_no_mst and
         d.po_break_down_id=b.id and e. color_size_break_down_id=d.id and c.id=e.mst_id and c.production_type in(1,2,3,4,5,11,8) and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $po_cond_for_in2  $date_cond $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond $internal_style_no_cond $internal_style_id_cond $style_no_cond $style_id_cond $file_no_cond $file_id_cond  order by b.id";
				$result_gmtprod=sql_select($sql_gmtprod);
				foreach($result_gmtprod as $row)
				{
					$gmt_prod_qty_arr[$row[csf('po_id')]][$row[csf('gmt_color')]]['cut_qty']+=$row[csf('cut_qty')];
					$gmt_prod_qty_arr[$row[csf('po_id')]][$row[csf('gmt_color')]]['sew_in_qty']+=$row[csf('sew_in_qty')];
					$gmt_prod_qty_arr[$row[csf('po_id')]][$row[csf('gmt_color')]]['sew_out_qty']+=$row[csf('sew_out_qty')];
					$gmt_prod_qty_arr[$row[csf('po_id')]][$row[csf('gmt_color')]]['finish_qty']+=$row[csf('finish_qty')];
					$gmt_prod_qty_arr[$row[csf('po_id')]][$row[csf('gmt_color')]]['recv_print_qty']+=$row[csf('recv_print_qty')];
					$gmt_prod_qty_arr[$row[csf('po_id')]][$row[csf('gmt_color')]]['recv_embrod_qty']+=$row[csf('recv_embrod_qty')];
					$gmt_prod_qty_arr[$row[csf('po_id')]][$row[csf('gmt_color')]]['recv_wash_qty']+=$row[csf('recv_wash_qty')];
					
					$gmt_prod_qty_arr[$row[csf('po_id')]][$row[csf('gmt_color')]]['issue_print_qty']+=$row[csf('issue_print_qty')];
					$gmt_prod_qty_arr[$row[csf('po_id')]][$row[csf('gmt_color')]]['issue_embrod_qty']+=$row[csf('issue_embrod_qty')];
					$gmt_prod_qty_arr[$row[csf('po_id')]][$row[csf('gmt_color')]]['issue_wash_qty']+=$row[csf('issue_wash_qty')];
					
				}
				unset($result_gmtprod);
				
				$prod_data="select a.febric_description_id as deter_id, b.po_breakdown_id as po_id,b.color_id,
				 (case when b.entry_form in(2,22) and b.trans_type=1 then b.quantity end) as kint_qty,
				 (case when b.entry_form in(7,66)  and b.trans_type=1 then b.quantity end) as fin_qty
				from pro_grey_prod_entry_dtls a, order_wise_pro_details b 
				where a.id=b.dtls_id and b.entry_form in(2,22,7,66) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in ";
				$prod_result=sql_select($prod_data);
				foreach($prod_result as $row)
				{
					$prod_qty_arr[$row[csf('po_id')]][$row[csf('deter_id')]]['kint_qty']+=$row[csf('kint_qty')];
					$prod_qty_fin_arr[$row[csf('po_id')]][$row[csf('color_id')]]['fin_qty']+=$row[csf('fin_qty')];
				}
				unset($prod_result);
				$sql_tna = "select b.id as po_id,c.po_number_id,
				 (case when c.task_number=61 then c.task_start_date end) as dyeing_start_date,
				 (case when c.task_number=61 then c.task_finish_date end) as dyeing_finish_date,
				  (case when c.task_number=60 then c.task_start_date end) as knit_start_date,
				 (case when c.task_number=60 then c.task_finish_date end) as knit_finish_date
				  from wo_po_details_master a, wo_po_break_down b, tna_process_mst c  where a.job_no=b.job_no_mst and a.job_no=c.job_no and 
				  b.id=c.po_number_id and c.task_number in(60,61) 
				     $date_cond $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond $internal_style_no_cond $internal_style_id_cond $style_no_cond $style_id_cond $file_no_cond $file_id_cond order by b.id";
				$data_arr_tna=sql_select($sql_tna);
				foreach($data_arr_tna as $row)
				{
					$tna_tmp_arr[$row[csf("po_id")]]['dyeing_start_date']=$row[csf("dyeing_start_date")];
					$tna_tmp_arr[$row[csf("po_id")]]['dyeing_finish_date']=$row[csf("dyeing_finish_date")];
					$tna_tmp_arr[$row[csf("po_id")]]['knit_start_date']=$row[csf("knit_start_date")];
					$tna_tmp_arr[$row[csf("po_id")]]['knit_finish_date']=$row[csf("knit_finish_date")];
				}
				unset($data_arr_tna);
					$sql_yarn = "select a.sequence_no,a.id,c.yarn_count,a.construction,b.copmposition_id,b.percent  from  lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b,lib_yarn_count c where a.id=b.mst_id and b.count_id=c.id  order by a.id, a.sequence_no";
					$data_arr_ycount=sql_select($sql_yarn);
					$composition1='';
				foreach($data_arr_ycount as $row)
				{
						$composition1=$composition[$row[csf("copmposition_id")]];
						//$percentage1=$com_percentage1_details[$row[csf("copmposition_id")]];
						$composition_name.=$composition1.' '.$row[csf("percent")].'%'.',';
						$construction=$row[csf("construction")];
						$precost_yarnCount_arr[$row[csf("id")]]['count'].=$row[csf("yarn_count")].',';
						$precost_fabric_arr[$row[csf("id")]]['comp']=$composition_name;
						$precost_fabric_arr[$row[csf("id")]]['construction']=$construction;
						
				}
					unset($data_arr_ycount);
				$sql_fabric = "select b.id as po_id,c.id, c.construction,c.composition,c.fabric_description,c.gsm_weight,c.lib_yarn_count_deter_id as count_deter_id,d.color_number_id   from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_fabric_cost_dtls c,wo_pre_cos_fab_co_avg_con_dtls d where a.job_no=b.job_no_mst and a.job_no=c.job_no and d.pre_cost_fabric_cost_dtls_id=c.id and d.po_break_down_id=b.id and d.job_no=a.job_no and d.job_no=c.job_no  $date_cond $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond $internal_style_no_cond $style_no_cond $style_id_cond $internal_style_id_cond $file_no_cond $file_id_cond order by c.id";
				$data_arr_fabric=sql_select($sql_fabric);
				foreach($data_arr_fabric as $fab_row)
				{
					
					$yarncount=$precost_yarnCount_arr[$fab_row[csf("count_deter_id")]]['count'];
					$precost_yarngsm_arr[$fab_row[csf("count_deter_id")]]['gsm'].=$fab_row[csf("gsm_weight")].',';
					$precost_fab_arr[$fab_row[csf("count_deter_id")]]['yarncount'].=$yarncount.',';
					
					$precost_fab_arr[$fab_row[csf("id")]][$fab_row[csf("po_id")]]['gsm']=$fab_row[csf("gsm_weight")];
					$precost_fab_arr[$fab_row[csf("id")]][$fab_row[csf("po_id")]]['count_deter_id']=$fab_row[csf("count_deter_id")];
					$precost_fab_desc_arr[$fab_row[csf("count_deter_id")]][$fab_row[csf("po_id")]]['desc']=$fab_row[csf("fabric_description")];
					
				}
				unset($data_arr_fabric);
				//print_r($precost_fab_arr);
				 $sql_book="select b.id as po_id,c.fabric_color_id as color_id,c.pre_cost_fabric_cost_dtls_id,
				 (case when c.booking_type in(1) and  c.is_short in(1) then c.fin_fab_qnty end) as short_fin_fab_qnty,
				  (case when c.booking_type in(4) and c.is_short in(2) then c.fin_fab_qnty end) as smp_fin_fab_qnty,
				  
				 (case when c.booking_type in(1) and  c.is_short in(1) then c.grey_fab_qnty end) as short_grey_fab_qnty,
				 (case when c.booking_type in(4) and c.is_short in(2) then c.grey_fab_qnty end) as smp_grey_fab_qnty
				  from wo_po_details_master a, wo_po_break_down b ,wo_booking_dtls c
				 where a.job_no=b.job_no_mst and c.po_break_down_id=b.id and a.job_no=c.job_no and c.booking_type in(1,4) 
				 $date_cond $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond $internal_style_no_cond $internal_style_id_cond $style_no_cond $style_id_cond $file_no_cond $file_id_cond  order by b.id";
				 $result_book=sql_select($sql_book);
				 foreach($result_book as $row)
				 {
					$fabric_cost_dtls_id=$row[csf("pre_cost_fabric_cost_dtls_id")];
					$deter_id=$precost_fab_arr[$fabric_cost_dtls_id][$row[csf("po_id")]]['count_deter_id'];
					
					$book_qty_fin_arr[$row[csf('po_id')]][$row[csf('color_id')]]['finish_qty']+=$row[csf('short_fin_fab_qnty')]+$row[csf('smp_fin_fab_qnty')];
					$book_qty_grey_arr[$row[csf('po_id')]][$deter_id]['grey_qty']+=$row[csf('short_grey_fab_qnty')]+$row[csf('smp_grey_fab_qnty')];
				 }
				 unset($result_book);
				// print_r($book_qty_fin_arr);
				 $sql_result="select a.company_name as company_id,a.job_no_prefix_num as job_prefix,a.season_matrix,  a.job_no, a.buyer_name, a.style_ref_no, 
				a.gmts_item_id, a.total_set_qnty as ratio,b.id as po_id, b.po_number, b.pub_shipment_date,(b.po_quantity) as po_quantity, (b.po_total_price) as po_total_price,c.fabric_color_id as fab_color,c.gmts_color_id,(c.fin_fab_qnty) as fin_fab_qnty,c.grey_fab_qnty,c.pre_cost_fabric_cost_dtls_id as pre_cost_fab_dtls_id,d.color_number_id as gmt_color,d.order_quantity,d.plan_cut_qnty,b.grouping,b.file_no 
				from wo_po_details_master a, wo_po_break_down b ,wo_booking_dtls c,wo_po_color_size_breakdown d
				 where a.job_no=b.job_no_mst and c.po_break_down_id=b.id and a.job_no=c.job_no and d.po_break_down_id=b.id and d.id=c.color_size_table_id and c.job_no=d.job_no_mst and d.job_no_mst=a.job_no and c.booking_type in(1) and  a.company_name='$company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_cond $buyer_id_cond $year_cond $job_no_cond $order_id_cond_trans $order_no_cond $internal_style_no_cond $style_no_cond $style_id_cond $internal_style_id_cond $file_no_cond $file_id_cond
				  order by b.id"; 
				$data_result=sql_select($sql_result);
				$prod_detail_arr=array();
				
				$i=$z=1;$all_full_job=""; $total_po_qty=$total_fab_req_qty=$total_po_qty_pcs=0;
				foreach($data_result as $row)
				{
					$fab_id=$row[csf("pre_cost_fab_dtls_id")];
					//$yarn_count=$precost_fab_arr[$fab_id][$fab_row[csf("po_id")]]['yarncount'];
					$gsm_desc=$precost_fab_arr[$fab_id][$row[csf('po_id')]]['gsm'];//$row[csf("pre_cost_fab_dtls_id")];
					$deter_ids=$precost_fab_arr[$fab_id][$row[csf("po_id")]]['count_deter_id'];
									
					$prod_detail_tmp[$row[csf('po_id')]]['pub_date']=$row[csf('pub_shipment_date')];
					$prod_detail_tmp[$row[csf('po_id')]]['job_no']=$row[csf('job_no')];
					$prod_detail_tmp[$row[csf('po_id')]]['buyer']=$row[csf('buyer_name')];
					$prod_detail_tmp[$row[csf('po_id')]]['season_matrix']=$row[csf('season_matrix')];
					$prod_detail_tmp[$row[csf('po_id')]]['gmts_item_id']=$row[csf('gmts_item_id')];
					$prod_detail_tmp[$row[csf('po_id')]]['style_ref_no']=$row[csf('style_ref_no')];
					$prod_detail_tmp[$row[csf('po_id')]]['po_no']=$row[csf('po_number')];
					//$prod_detail_tmp[$row[csf('po_id')]]['po_qty_pcs']+=$row[csf('order_quantity')];
				
					$prod_detail_booking_arr2[$row[csf('po_id')]][$row[csf('fab_color')]]['fin_fab_qnty']+=$row[csf('fin_fab_qnty')];
					$prod_detail_booking_arr[$row[csf('po_id')]][$deter_ids]['grey_fab_qnty']+=$row[csf('grey_fab_qnty')];
					$prod_detail_tmp[$row[csf('po_id')]]['pre_fab_id']=$fab_id;
					
					$prod_detail_tmp[$row[csf('po_id')]]['desc']=$deter_id;
					
					$prod_detail_tmp[$row[csf('po_id')]]['po_id']=$row[csf('po_id')];
					$prod_detail_tmp[$row[csf('po_id')]]['fab_color'][$row[csf('fab_color')]]+=1;
					$prod_detail_tmp[$row[csf('po_id')]]['fab_color_id'][$row[csf('fab_color')]]=$row[csf('fab_color')];
					$prod_detail_tmp[$row[csf('po_id')]]['gmt_color_id'][$row[csf('gmt_color')]]=$row[csf('gmt_color')];
					$prod_detail_tmp[$row[csf('po_id')]]['gmt_color'][$row[csf('gmt_color')]]+=1;
					$prod_detail_tmp[$row[csf('po_id')]]['deter_ids'][$deter_ids]=$deter_ids;
					$prod_detail_tmp[$row[csf('po_id')]]['grouping']=$row[csf('grouping')];
					$prod_detail_tmp[$row[csf('po_id')]]['file_no']=$row[csf('file_no')];
					//$prod_detail_rowspan[$row[csf('po_id')]][$row[csf('gmt_color')]]+=1;
					
				}
				// echo "<pre>";
				//print_r($prod_detail_rowspan);
				 
				$total_grey_fab_qnty=$total_fin_fab_qnty=$total_tot_knitting_com_qty=$total_tot_knit_balance_qnty=$total_tot_fin_com_qty=$total_tot_dye_balance_qnty=$total_tot_cut_balance_qnty=$total_tot_embrod_balance_qnty=$total_tot_sew_in_qty=0;
				
				$total_tot_recv_print_qty=$total_tot_print_balance_qnty=$total_tot_issue_print_qty=$total_tot_recv_embrod_qty=$total_tot_issue_embrod_qty=$total_tot_sew_in_qty=$total_tot_issue_wash_qty=$total_tot_sew_out_qty=$total_tot_wash_qty_recv=$total_tot_wash_balance_qnty=$total_tot_finish_qty=$total_tot_finish_balance_qnty=$total_short_excess_qty=0;
				$totalqty_po_qty_pcs=0;
				foreach($prod_detail_tmp as $po_key=>$val)
				{
					
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							
							$pre_fab_id=$val['pre_fab_id'];
							$fab_desc=$val['desc'];//$deter_key;//$precost_yarnCount_arr[$deter_key]['desc'];//$val['fab_desc'];//$precost_fab_arr[$fab_key][$po_key]['desc'];
							$yarn_count=$val['yarn_count'];
							$plan_cut_qnty=$val['plan_cut_qnty'];
							
							$dyeing_start_date=$tna_tmp_arr[$po_key]['dyeing_start_date'];
							$dyeing_finish_date=$tna_tmp_arr[$po_key]['dyeing_finish_date'];
							
							$knit_start_date=$tna_tmp_arr[$po_key]['knit_start_date'];
							$knit_finish_date=$tna_tmp_arr[$po_key]['knit_finish_date'];
							
							
							////$gmt_color_rowspan=$fab_fab_rowspan_arr[$po_key][$fab_color];
							//$fab_desc_rowspan2=$fab_desc_rowspan_arr[$po_key];
						//	$po_rowspan_rowspan=$po_rowspan_arr[$po_key];
							
						?>
                       <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                        	<td width="30"   ><? echo $i; ?></td>
                           
							<td width="100" ><? echo $buyer_arr[$val['buyer']]; ?></td>
							<td width="70"    align="center"><p><? echo $season_details[$val['season_matrix']]; ?></p></td>
							<td width="130"  style="word-break:break-all"><? 
							$gmts_item=''; $gmts_item_id=explode(",",$val['gmts_item_id']);
							foreach($gmts_item_id as $item_id)
							{
								if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=", ".$garments_item[$item_id];
							}
							echo $gmts_item; 
							 ?></td>
							<td width="100" style="word-break:break-all"><? echo $val['style_ref_no']; ?></td>
                            <td width="100" style="word-break:break-all"><? echo  $val['job_no']; ?></td>
							<td width="80" style="word-break:break-all"><? echo $val['po_no']; ?></td>
                            
                            	<td width="80" style="word-break:break-all"><? echo $val['grouping']; ?></td>
                            	<td width="80" style="word-break:break-all"><? echo $val['file_no']; ?></td>
                            
							<td width="80" ><div style="word-wrap:break-all;"><? echo change_date_format($val['pub_date']); ?></div></td>
                            
                            <td width="80" ><div style="word-break:break-all"><? echo change_date_format($knit_start_date); ?></div></td>
							<td width="80" > <div style="word-break:break-all"> <? echo change_date_format($knit_finish_date); ?>   </div>
							<td width="80"  align="center" > <? echo change_date_format($dyeing_start_date); ?>
                            <td width="80" ><div style="word-break:break-all"><?  echo change_date_format($dyeing_finish_date);  ?></div></td>
                            <td    valign="top" width="516">
                            	<table  class="rpt_table" width="100%" cellspacing="0" cellpadding="0" rules="all" border="0">
                                	<? 
									$tot_grey_fab_qnty=$tot_knitting_com_qty=$tot_knit_balance_qnty=0;
										foreach( $prod_detail_tmp[$po_key]['deter_ids'] as $deter )//Fabric part
										{
											$construction=$precost_fabric_arr[$deter]['construction'];
											$comp=rtrim($precost_fabric_arr[$deter]['comp'],',');
											$composition_name=implode(",",array_unique(explode(",",$comp)));
											$fab_descs=$precost_fab_desc_arr[$deter][$po_key]['desc'];//$construction.','.$composition_name;
											//$yarn_counts=$precost_yarnCount_arr[$deter]['count'];
											$yarn_counts=$precost_yarnCount_arr[$deter]['count'];
											$yarn_count=rtrim($yarn_counts,',');
											$count_name=implode(",",array_unique(explode(",",$yarn_count)));
												
											$fab_gsm=rtrim($precost_yarngsm_arr[$deter]['gsm'],',');
											$fab_gsms=implode(",",array_unique(explode(",",$fab_gsm)));
											$ss_grey_qty=$book_qty_grey_arr[$po_key][$deter]['grey_qty'];
											//echo $ss_grey_qty.'df';
											$grey_fab_qnty=$prod_detail_booking_arr[$po_key][$deter]['grey_fab_qnty']+$ss_grey_qty;
											$knitting_com_qty=$prod_qty_arr[$po_key][$deter]['kint_qty'];
											
											$knit_balance_qnty=$grey_fab_qnty-$knitting_com_qty;
											
											$tot_grey_fab_qnty+=$grey_fab_qnty;
											$tot_knitting_com_qty+=$knitting_com_qty;
											$tot_knit_balance_qnty+=$knit_balance_qnty;
									?>
                                	<tr>
                                    	<td width="170" style="word-break:break-all"  title="<? echo $deter.' Fab'.$po_key; ?>"> 
                                         <? echo $fab_descs; ?>    </td>
                                        
                                        <td width="50"  align="center" ><div style="word-break:break-all"><? echo $count_name; ?></div></td>
                                        <td width="50" title=""> <div style="word-break:break-all"> <? echo $fab_gsms; ?>   </div>	</td>
                                        
                                        <td width="80"  title="Fab Gray Qty" align="right"> <div style="word-break:break-all"> 
                                        <? echo number_format($grey_fab_qnty,2); ?>   </div>	</td>
                                        <td width="80"  title="" align="right"> <div style="word-break:break-all"> 
                                        <?  echo number_format($knitting_com_qty,2);//$val['fin_fab_qnty']; ?>  </div>	</td>
                                        <td width="80"  title=""  align="right"> <div style="word-break:break-all"> <? echo number_format($knit_balance_qnty,2); ?> </div>	</td>
                                    </tr>
                                    <? }?>
                                </table>
                            </td>
                            <td   valign="top" width="340">
                            	<table  class="rpt_table" width="100%" cellspacing="0" cellpadding="0" rules="all" border="1">
                                	<? 
										$tot_fin_fab_qnty=0;$tot_fin_com_qty=$tot_dye_balance_qnty=0;
										foreach( $prod_detail_tmp[$po_key]['fab_color_id'] as $fab_colo )//Dyeing part
										{
											$ss_finish_qty=$book_qty_fin_arr[$po_key][$fab_colo]['finish_qty'];
											$fin_fab_qnty=$prod_detail_booking_arr2[$po_key][$fab_colo]['fin_fab_qnty']+$ss_finish_qty;
											$fin_com_qty=$prod_qty_fin_arr[$po_key][$fab_colo]['fin_qty'];
											$dye_balance_qnty=$fin_fab_qnty-$fin_com_qty;
											$tot_fin_fab_qnty+=$fin_fab_qnty;
											$tot_fin_com_qty+=$fin_com_qty;	
											$tot_dye_balance_qnty+=$dye_balance_qnty;
											
											
									?>
                                	<tr>
                                    	 
                                        <td width="100" title=""> <div style="word-break:break-all"> <? echo $color_details[$fab_colo]; ?>   </div>	</td>
                                        
                                        <td width="80" title="Fab Finish Qty" align="right"> <div style="word-break:break-all"> 
                                        <? echo number_format($fin_fab_qnty,2); ?>   </div>	</td>
                                        <td width="80" title="" align="right"> <div style="word-break:break-all"> 
                                        <? echo number_format($fin_com_qty,2); ?>   </div>	</td>
                                        <td width="80" title="Dye Finish Balance" align="right"> <div style="word-break:break-all"> <? echo number_format($dye_balance_qnty,2);  ?> </div>	</td>
                                    </tr>
                                    <? }?>
                                </table>
                            </td>
                            <td   valign="top" width="1480">
                            	<table  class="rpt_table" width="100%" cellspacing="0" cellpadding="0" rules="all"  border="1">
                                	<? 
									$tot_cut_qnty=$tot_cut_balance_qnty=$tot_recv_print_qty=$tot_issue_print_qty=$tot_issue_embrod_qty=$tot_issue_wash_qty=$tot_print_balance_qnty=$tot_recv_embrod_qty=$tot_embrod_balance_qnty=$tot_sew_in_balance_qnty=$tot_sew_in_qty=$tot_sew_out_qty=$tot_wash_balance_qnty=$tot_wash_qty_recv=$tot_finish_qty=$tot_finish_balance_qnty=0;									
										foreach( $prod_detail_tmp[$po_key]['gmt_color_id'] as $gmt_colo )//Garments part
										{
											$cut_qnty=$gmt_prod_qty_arr[$po_key][$gmt_colo]['cut_qty'];
											$finish_qty=$gmt_prod_qty_arr[$po_key][$gmt_colo]['finish_qty'];
											$sew_in_qty=$gmt_prod_qty_arr[$po_key][$gmt_colo]['sew_in_qty'];
											$sew_out_qty=$gmt_prod_qty_arr[$po_key][$gmt_colo]['sew_out_qty'];
											
											$recv_print_qty=$gmt_prod_qty_arr[$po_key][$gmt_colo]['recv_print_qty'];
											$recv_embrod_qty=$gmt_prod_qty_arr[$po_key][$gmt_colo]['recv_embrod_qty'];
											$wash_qty_recv=$gmt_prod_qty_arr[$po_key][$gmt_colo]['recv_wash_qty'];
											
											$issue_print_qty=$gmt_prod_qty_arr[$po_key][$gmt_colo]['issue_print_qty'];
											$issue_embrod_qty=$gmt_prod_qty_arr[$po_key][$gmt_colo]['issue_embrod_qty'];
											$issue_wash_qty=$gmt_prod_qty_arr[$po_key][$gmt_colo]['issue_wash_qty'];
											
											//$plan_cut_qnty=$prod_detail_po_arr[$po_key][$gmt_colo]['plan_cut'];
											$po_qty_pcs=$color_qty_arr[$po_key][$gmt_colo]['po_qty_pcs'];
											$plan_cut_qnty=$color_qty_arr[$po_key][$gmt_colo]['plan_cut'];
											
											 $cut_balance_qnty=$plan_cut_qnty-$cut_qnty;
											 $tot_cut_balance_qnty+=$cut_balance_qnty;
											 $print_balance_qnty=$recv_print_qty-$issue_print_qty;
											 $tot_print_balance_qnty+=$print_balance_qnty;
											 $embrod_balance_qnty=$recv_embrod_qty-$issue_embrod_qty;
											 $tot_embrod_balance_qnty+=$embrod_balance_qnty;
											 $wash_balance_qnty=$wash_qty_recv-$issue_wash_qty;
											 $finish_balance_qnty=$po_qty_pcs-$finish_qty;
											 
											  //$tot_sew_in_balance_qnty+=$sew_in_qty;
											
											$tot_cut_qnty+=$cut_qnty;
											$tot_finish_qty+=$finish_qty;
											$tot_sew_in_qty+=$sew_in_qty;
											$tot_sew_out_qty+=$sew_out_qty;
											
											$tot_recv_print_qty+=$recv_print_qty;
											$tot_recv_embrod_qty+=$recv_embrod_qty;
											$tot_wash_qty_recv+=$wash_qty_recv;
											
											$tot_issue_print_qty+=$issue_print_qty;
											$tot_issue_embrod_qty+=$issue_embrod_qty;
											$tot_issue_wash_qty+=$issue_wash_qty;
											
											$tot_wash_balance_qnty+=$wash_balance_qnty;
											$tot_finish_balance_qnty+=$finish_balance_qnty;

											$totalqty_po_qty_pcs += $po_qty_pcs;
											
											//$tot_balance_ship_prod_qty+=$balance_ship_prod_qty;
											//$tot_ship_prod_qty+=$ship_prod_qty;
											
									?>
                                	<tr>
                                    	<td width="80" title="" > <div style="word-break:break-all"> <? echo $color_details[$gmt_colo]; ?>   </div>	</td>
                                        <td width="100"   title="<? echo $gmt_colo;?>" align="right"> <div style="word-break:break-all"> <? echo $po_qty_pcs; ?>  </div></td>
                                        
                                        <td width="80"  title="Plan Cut"  align="right"> <div style="word-break:break-all"> <? echo $plan_cut_qnty; ?>  </div>	</td>
                                        <td width="80"  title=""  align="right"> <div style="word-break:break-all"> 
                                        <? echo number_format($cut_qnty,2); ?> </div></td>
                                        <td width="80"  title="Req Qty-Cut(Complete) Qty" align="right"> <div style="word-break:break-all">
                                         <?  echo number_format($cut_balance_qnty,2); ?> </div>	</td>
                                        
                                        <td width="80" title="Print 3" align="right"> <div style="word-break:break-all"> <? echo $recv_print_qty; ?>  </div></td>
                                        <td width="80"  title="" align="right"> <div style="word-break:break-all">
                                         <? echo number_format($issue_print_qty,2); ?>  </div>	</td>
                                        <td width="80"  title="" align="right"> <div style="word-break:break-all">
                                         <? echo number_format($print_balance_qnty,2); ?>  </div></td>
                                        
                                        <td width="80"   title="Emrodery 3"  align="right"> <div style="word-break:break-all"> <? echo $recv_embrod_qty; ?> </div>	</td>
                                        <td width="80"   title=""  align="right"> <div style="word-break:break-all">
                                         <? echo number_format($issue_embrod_qty,2); ?>   </div>	</td>
                                        <td width="80"  title=""  align="right"> <div style="word-break:break-all"> <? echo  number_format($embrod_balance_qnty,2); ?> </div>	</td>
                                        
                                        <td width="80"   title="Sewing In" align="right"> <div style="word-break:break-all"> <? echo number_format($sew_in_qty,2); ?>  </div></td>
                                        <td width="80"  title="Sew Out" align="right"> <div style="word-break:break-all">
                                         <? echo number_format($sew_out_qty,2); ?>   </div>	</td>
                                        <td width="80"   title="Wash Recv." align="right"> <div style="word-break:break-all"> <? echo number_format($wash_qty_recv,2); ?>  </div>	</td>
                                       
                                        <td width="80"  title="Wash Del."  align="right"> <div style="word-break:break-all"> <? echo $issue_wash_qty; ?>   </div>	</td>
                                        <td width="80"   title="Wash balance"  align="right"> <div style="word-break:break-all"> <? echo number_format($wash_balance_qnty,2);  ?>   </div>	</td>
                                        <td width="80"   title="Finish Qty"  align="right"> 
                                        <div style="word-break:break-all"> <? echo number_format($finish_qty,2); ?>   </div>	</td>
                                         
                                         <td width="80"  title="PO Qty Pcs-Finish Complete" align="right"> <div style="word-break:break-all"> <? echo number_format($finish_balance_qnty,2); ?>   </div>	</td>
                                        
                                    </tr>
                                    <? }?>
                                </table>
                            </td>
                            <td width="80"  title=""  align="right"> <div style="word-break:break-all"> <?
							$ship_prod_qty=$ship_prod_qty_arr[$po_key]['prod_qty'];
							$tot_po_qty_pcs=$color_po_qty_arr[$po_key]['po_qty_pcs'];
							$balance_ship_prod_qty=$tot_po_qty_pcs-$ship_prod_qty;
							$prod_date=$ship_prod_qty_arr[$po_key]['prod_date'];
							 echo number_format($ship_prod_qty,2); ?> </div>	</td>
                            <td width="80"  title="PO Qty Pcs(<? echo $tot_po_qty_pcs;?>)-Ship Qty" align="right"> <div style="word-break:break-all"><? echo number_format($balance_ship_prod_qty,2); ?> </div>
                            </td>
                          <td width=""  title=""  align="center"> <div style="word-break:break-all"><? echo change_date_format($prod_date); ?> </div></td>
                        </tr>
                        
                            <?
							
							$i++;
							
							$total_grey_fab_qnty+=$tot_grey_fab_qnty;
							$total_tot_knitting_com_qty+=$tot_knitting_com_qty;
							$total_tot_knit_balance_qnty+=$tot_knit_balance_qnty;
							$total_fin_fab_qnty+=$tot_fin_fab_qnty;
							$total_tot_fin_com_qty+=$tot_fin_com_qty;
							$total_tot_dye_balance_qnty+=$tot_dye_balance_qnty;
							$total_tot_cut_qnty+=$tot_cut_qnty;
							$total_tot_cut_balance_qnty+=$tot_cut_balance_qnty;
							$total_tot_recv_print_qty+=$tot_recv_print_qty;
							$total_tot_issue_print_qty+=$tot_issue_print_qty;	
							$total_tot_print_balance_qnty+=$tot_print_balance_qnty;
							$total_tot_recv_embrod_qty+=$tot_recv_embrod_qty;
							$total_tot_issue_embrod_qty+=$tot_issue_embrod_qty;
							$total_tot_embrod_balance_qnty+=$tot_embrod_balance_qnty;
							$total_tot_sew_in_qty+=$tot_sew_in_qty;
							$total_tot_sew_out_qty+=$tot_sew_out_qty;
							//$total_tot_wash_balance_qnty+=$tot_wash_balance_qnty;
							$total_tot_wash_qty_recv+=$tot_wash_qty_recv;
							$total_tot_issue_wash_qty+=$tot_issue_wash_qty;
							$total_tot_wash_balance_qnty+=$tot_wash_balance_qnty;
							$total_tot_finish_qty+=$tot_finish_qty;
							$total_tot_finish_balance_qnty+=$tot_finish_balance_qnty;
							$total_po_qty_pcs+=$po_qty_pcs;
							$total_short_excess_qty+=$balance_ship_prod_qty;
							$total_tot_ship_prod_qty+=$ship_prod_qty;
					
				}
							?>
            </table>
        <table class="rpt_table" width="3810" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
				<tfoot>
                 
                    <th width="30">&nbsp; </th>
                    <th width="100">&nbsp;</th>
                    <th width="70">&nbsp;  </th>
                    <th width="130">&nbsp;  </th>
                    <th  width="100">&nbsp; </th>
                    <th width="100">&nbsp; </th>
                    <th width="80">&nbsp; </th>
                    <th width="80">&nbsp; </th>
                    <th width="80">&nbsp; </th>
                    <th width="80">&nbsp;  </th>
                    
                    <th width="80">&nbsp;</th>
                    <th  width="80">&nbsp;  </th>
                    <th width="80">&nbsp; </th>
                    <th width="80">&nbsp; </th>
                    <th width="170">&nbsp; </th>
                    <th width="50">&nbsp; </th>
                    <th  width="50">Total </th>
                    
                    
                    <th width="80"> <? echo number_format($total_grey_fab_qnty,2); ?> </th>
                    <th width="80"> <? echo number_format($total_tot_knitting_com_qty,2); ?> </th>
                    <th width="80"> <? echo number_format($total_tot_knit_balance_qnty,2); ?> </th>
                    <th width="100"> <? //echo number_format($total_fab_req_qty,2); ?> </th>
                    <th  width="80"> <? echo number_format($total_fin_fab_qnty,2); ?> </th>
                    
                    <th  width="80"> <? echo number_format($total_tot_fin_com_qty,2); ?> </th>
                    <th  width="80"> <? echo number_format($total_tot_dye_balance_qnty,2); ?> </th>
                    
                    <th  width="80"> <? //echo number_format($total_fab_req_qty,2); ?> </th>
                    <th  width="100"> <? echo number_format($totalqty_po_qty_pcs,2); ?> </th>
                    <th  width="80"> <? //echo number_format($total_tot_cut_qnty,2); ?> </th>
                    
                    <th  width="80"> <? echo number_format($total_tot_cut_qnty,2); ?> </th>
                    <th  width="80"> <? echo number_format($total_tot_cut_balance_qnty,2); ?> </th>
                    <th  width="80"> <? echo number_format($total_tot_recv_print_qty,2); ?> </th>
                    <th  width="80"> <? echo number_format($total_tot_issue_print_qty,2); ?> </th>
                    <th  width="80"> <? echo number_format($total_tot_print_balance_qnty,2); ?> </th>
                
                    <th  width="80"> <? echo number_format($total_tot_recv_embrod_qty,2); ?> </th>
                    <th  width="80"><? echo number_format($total_tot_issue_embrod_qty,2); ?> </th>
                    <th  width="80"> <? echo number_format($total_tot_embrod_balance_qnty,2); ?> </th>
                    <th  width="80"> <? echo number_format($total_tot_sew_in_qty,2); ?> </th>
                     <th  width="80"> <? echo number_format($total_tot_sew_out_qty,2); ?> </th>
                   
                    <th  width="80"> <? echo number_format($total_tot_wash_qty_recv,2); ?> </th>
                    <th  width="80"> <? echo number_format($total_tot_wash_qty_issue,2); ?> </th>
                    <th  width="80"> <? echo number_format($total_tot_wash_balance_qnty,2); ?> </th>
                    <th  width="80"><? echo number_format($total_tot_finish_qty,2); ?> </th>
                    <th  width="80"> <? echo number_format($total_tot_finish_balance_qnty,2); ?> </th>
                    <th  width="80"> <? echo number_format($total_tot_ship_prod_qty,2); ?> </th>
                    <th  width="80"> <? echo number_format($total_short_excess_qty,2); ?> </th>
                    <th> <? // echo number_format($total_short_excess_qty,2); ?> </th>
             </tfoot>
          </table>
            </div>
            </fieldset>
            </div>
            <?	
	}
	
	$html = ob_get_contents();
    ob_clean();
    foreach (glob("$user_id*.xls") as $filename) {
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html****$filename****$report_type"; 
	exit();	
}

?>