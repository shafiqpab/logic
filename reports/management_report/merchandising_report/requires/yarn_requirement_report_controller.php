<?
session_start();
//ini_set('memory_limit','3072M');
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');
require_once('../../../../includes/class3/class.conditions.php');
require_once('../../../../includes/class3/class.reports.php');
require_once('../../../../includes/class3/class.yarns.php');
/*require_once('../../../../includes/class.reports.php');
require_once('../../../../includes/class.yarns.php');*/

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
//--------------------------------------------------------------------------------------------------------------------

$user_id = $_SESSION['logic_erp']["user_id"];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );     	 
	exit();
}

if($action=="order_no_search_popup")
{
	echo load_html_head_contents("Order No Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
     
	<script>
		
		var selected_id = new Array; var selected_name = new Array;
		
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click'); 
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str ) {
			
			if (str!="") str=str.split("_");
			 
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			 
			if( jQuery.inArray( str[1], selected_id ) == -1 ) {
				selected_id.push( str[1] );
				selected_name.push( str[2] );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hide_order_id').val( id );
			$('#hide_order_no').val( name );
		}
	
    </script>

</head>

<body>
<div align="center">
	<form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:780px;">
            <table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Order No</th>
                    <th>Shipment Date</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
                    <input type="hidden" name="hide_order_no" id="hide_order_no" value="" />
                    <input type="hidden" name="hide_order_id" id="hide_order_id" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",0,"",0 );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Order No",2=>"Style Ref",3=>"Job No");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 110, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
                        </td>	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_order_no_search_list_view', 'search_div', 'yarn_requirement_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    	</td>
                    </tr>
                    <tr>
                        <td colspan="5" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
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

if($action=="create_order_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];

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

	if($search_by==1) 
		$search_field="b.po_number"; 
	else if($search_by==2) 
		$search_field="a.style_ref_no"; 	
	else 
		$search_field="a.job_no";
		
	$start_date =$data[4];
	$end_date =$data[5];	
	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and b.pub_shipment_date between '".change_date_format(trim($start_date),"yyyy-mm-dd")."' and '".change_date_format(trim($end_date),"yyyy-mm-dd")."'";
		}
		else
		{
			$date_cond="and b.pub_shipment_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";	
		}
	}
	else
	{
		$date_cond="";
	}
	
	$company_short_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$arr=array (0=>$company_short_arr,1=>$buyer_arr);
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	
	$sql= "select b.id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $date_cond order by b.id, b.pub_shipment_date";
		
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Po No, Shipment Date", "70,70,50,70,150,180","760","220",0, $sql , "js_set_value", "id,po_number","",1,"company_name,buyer_name,0,0,0,0,0",$arr,"company_name,buyer_name,year,job_no_prefix_num,style_ref_no,po_number,pub_shipment_date","",'','0,0,0,0,0,0,3','',1) ;
   exit(); 
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');
	

	$company_name=str_replace("'","",$cbo_company_name);
	$cbo_yarn_type=str_replace("'","",$cbo_yarn_type);
	$cbo_yarn_count=str_replace("'","",$cbo_yarn_count);
	$txt_order_no=str_replace("'","",$txt_order_no);
	$hide_order_id=str_replace("'","",$hide_order_id);
	$approval_status=str_replace("'","",$cbo_approval_status);
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
	if($cbo_yarn_type!=0) $yarn_type_cond="and d.type_id=$cbo_yarn_type";else  $yarn_type_cond="";
	if($cbo_yarn_count!=0) $yarn_count_cond="and d.count_id=$cbo_yarn_count";else  $yarn_count_cond="";
	$txt_job_no=str_replace("'","",$txt_job_no);
	if(trim($txt_job_no)!="") $job_no_cond=" and a.job_no_prefix_num=$txt_job_no"; else $job_no_cond="";
	if($approval_status!=0) $approval_status_cond="and e.approved=$approval_status";else  $approval_status_cond="";
	//echo $job_no;
	$cbo_year=str_replace("'","",$cbo_year);
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$cbo_year";
		else if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
		else $year_cond="";
	}
	else $year_cond="";
	
	$date_cond='';
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		$date_cond=" and b.pub_shipment_date between $txt_date_from and $txt_date_to";
	}
	$po_order_num=explode(",",$txt_order_no);
	//$hide_order_id_data=explode(",",$hide_order_id);
	//print_r($hide_order_id_data);
	$po_num_cond='';
	foreach($po_order_num as $po_num)
	{
		if($po_num_cond=='') $po_num_cond="'".$po_num."'";else $po_num_cond.=','."'".$po_num."'";
	}
	/*$po_ids_cond='';
	foreach($hide_order_id_data as $po_id)
	{
		if($po_ids_cond=='') $po_ids_cond="'".$po_id."'";else $po_ids_cond.=','."'".$po_id."'";
	}*/
	//echo $po_ids_cond;
	$po_id_cond="";
	if(trim(str_replace("'","",$txt_order_no))!="")
	{
		if(str_replace("'","",$hide_order_id)!="")
		{
			$po_id_cond=" and b.id in(".str_replace("'","",$hide_order_id).")";
		}
		else
		{
			$po_id_cond=" and b.po_number in ($po_num_cond)";
		}
	}
	//echo $po_id_cond;
		//echo $po_ex=implode(",",("'".explode("*",$txt_order_no))."'");
		
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	if($db_type==0) $group_type="group_concat(d.type_id) as type_id";
	else if($db_type==2) $group_type="LISTAGG(d.type_id, ',') WITHIN GROUP (ORDER BY d.type_id) as type_id ";
	// if($db_type==0) $comb_type="group_concat(d.copm_one_id) as copm_one_id";
	// else if($db_type==2) $comb_type="LISTAGG(d.copm_one_id, ',') WITHIN GROUP (ORDER BY d.copm_one_id) as copm_one_id ";
	
		$yarn_count_head_arr=array();
	 $sql_res="select d.count_id 
			from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_fabric_cost_dtls c,wo_pre_cost_fab_yarn_cost_dtls d where a.job_no=b.job_no_mst  and a.job_no=c.job_no and b.job_no_mst=c.job_no and b.job_no_mst=d.job_no  and c.job_no=d.job_no and a.job_no=d.job_no and c.id=d.fabric_cost_dtls_id   and a.company_name='$company_name'  $date_cond  $po_id_cond $yarn_count_cond $yarn_type_cond $buyer_id_cond $job_no_cond $year_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0   order by d.count_id";
			
	//echo $sql_res; //die;
	$result=sql_select($sql_res);
	foreach($result as $row)
		 {
			 $yarn_count_head_arr[$row[csf('count_id')]]=$row[csf('count_id')]; 
		 }
		 
		  $sql_data="select a.job_no_prefix_num,a.buyer_name,a.style_ref_no,b.id as po_id,b.po_number,c.gsm_weight,c.construction,d.copm_one_id, $group_type,$year_field
			from wo_po_details_master a left join wo_pre_cost_mst e on  a.job_no=e.job_no, wo_po_break_down b,wo_pre_cost_fabric_cost_dtls c,wo_pre_cost_fab_yarn_cost_dtls d where a.job_no=b.job_no_mst  and a.job_no=c.job_no and b.job_no_mst=c.job_no and b.job_no_mst=d.job_no  and c.job_no=d.job_no and a.job_no=d.job_no and c.id=d.fabric_cost_dtls_id   and a.company_name='$company_name'  $date_cond  $po_id_cond $yarn_count_cond $yarn_type_cond $buyer_id_cond $job_no_cond $year_cond $approval_status_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by a.job_no_prefix_num,a.buyer_name,a.style_ref_no,b.id ,b.po_number,c.gsm_weight,c.construction,d.copm_one_id,a.insert_date  order by b.id";
		
		 //echo $sql_data;
		$result_data=sql_select($sql_data);
		
	 $td_with=60*count($yarn_count_head_arr);
	ob_start();
	?>
    <div>
    <fieldset style="width:<? echo $td_with+1040;?>px;">
    	<table width="<? echo $td_with+1040;?>">
            <tr class="form_caption">
                <td colspan="<? echo count($yarn_count_head_arr)+10;?>" align="center"><strong>Monthly Yarn Requirement Report</strong></td>
            </tr>
            <tr class="form_caption">
                <td colspan="<? echo count($yarn_count_head_arr)+10;?>" align="center"><strong><? echo $company_arr[$company_name];?></strong>
                <br>
                <strong>
                <? echo change_date_format(str_replace("'","",$txt_date_from)) .' To '. change_date_format(str_replace("'","",$txt_date_to)); ?>
                </strong>
                
                </td>
            </tr>
        </table>
        <table  class="rpt_table" width="<? echo $td_with+1040;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
             <tr>
                <th width="40" rowspan="2">SL</th>
                <th width="100" rowspan="2">Buyer</th>
                <th width="60" rowspan="2">Year</th>
                <th width="70" rowspan="2">Job No</th>
				<th width="100" rowspan="2">Style Ref</th>
                <th width="120" rowspan="2">Order No</th>               
                <th width="60" rowspan="2">F/ GSM</th>
                <th width="120" rowspan="2">Fabrics Type</th>
                <th width="150" rowspan="2">Yarn Type</th>
				<th width="100" rowspan="2"> Yarn Composition</th>
                <th align="center"  colspan="<? echo count($yarn_count_head_arr);?>">Yarn Count</th>
                <th width="" rowspan="2">Total</th>
                </tr>
                <tr>
                 <?
                foreach($yarn_count_head_arr as $count_id)
				{
					?>
                 <th width="60"><p><? echo $yarn_count_arr[$count_id];?></p></th>
                 
                 <?
				}
				 ?>
                 </tr>
            </thead>
        </table>
        <?  //die; 
		//echo $td_with;
		?>
        <div style="width:<? echo $td_with+1060;?>px; max-height:400px; overflow-y:scroll" id="scroll_body">
			<table class="rpt_table" width="<? echo $td_with+1040;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
            <?
            $i=1;
			$condition= new condition();
			 $condition->company_name("=$company_name");
			 if(str_replace("'","",$cbo_buyer_name)>0){
				  $condition->buyer_name("=$cbo_buyer_name");
			 }
			 if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
			 {
				  $condition->pub_shipment_date(" between $txt_date_from and $txt_date_to");
			 }
			 if(str_replace("'","",$txt_job_no) !=''){
				  $condition->job_no_prefix_num("=$txt_job_no");
			 }
			 /*if(str_replace("'","",$txt_order_no)!='')
			 {
				if(str_replace("'","",$hide_order_id)!="")
					{
						//echo $hide_order_id;die;
						//$po_id_cond=" and b.id in(".str_replace("'","",$hide_order_id).")";
						//$condition->id("($hide_order_id)"); 
					}
					else
					{
						//$po_id_cond=" and b.po_number in ($po_num_cond)";
						//$condition->po_number(" in($po_id_cond)"); 
					}
				
				
			 }*/
			
			 
			$condition->init();
			$yarn= new yarn($condition);
			//echo $yarn->getQuery(); die;
			//$yarn_costing_qty_arr=$yarn->get_order_and_construction_gsm_weight_Count_wise_QtyArray();
			$yarn_costing_qty_arr=$yarn->get_order_and_construction_gsm_weight_Count_type_wise_QtyArray();
			// print_r($yarn_costing_qty_arr);
			$col_yarn_qty=array();$total_yarn_qty=0;
			
			foreach($result_data as $row )
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				//$yarn_type
				$yarn_type_id=array_unique(explode(",",$row[csf('type_id')]));
			
				$yarn_type_val='';
			
				foreach($yarn_type_id as $yarn_key)
				{
					if($yarn_type_val=="") $yarn_type_val=$yarn_type[$yarn_key]; else $yarn_type_val.=", ".$yarn_type[$yarn_key];
					
				}
				
				// $copm_type_id=array_unique(explode(",",$row[csf('copm_one_id')]));
				// $copm_type_val='';
				// foreach($copm_type_id as $copm_key)
				// {
				// 	if($copm_type_val=="") $copm_type_val=$copm_type[$copm_key]; else $copm_type_val.=", ".$copm_type[$copm_key];
				// }
				
				?>
				
                 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
					 <td width="40"><? echo $i; ?></td>
					 <td width="100"><div style="width:100px;word-wrap:break-word;"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></div></td>
					 <td width="60"><p><? echo $row[csf('year')]; ?></p></td>
                     <td width="70"><p><? echo $row[csf('job_no_prefix_num')]; ?></p></td>
					 <td width="100"><div style="width:100px;word-wrap:break-word;"><? echo $row[csf('style_ref_no')]; ?></div></td>
                     <td width="120"><div style=" width:120px;word-wrap:break-word;"><? echo $row[csf('po_number')]; ?></div></td>
                     <td width="60"><p><? echo $row[csf('gsm_weight')]; ?></p></td>
                     <td width="120"><div style="width:120px;word-wrap:break-word;"><? echo $row[csf('construction')]; ?></div></td>
                     <td width="150"><div style="width:150px;word-wrap:break-word;"><? echo $yarn_type_val; ?></div></td>
					  <td width="100"><div style="width:100px;word-wrap:break-word;"><? echo $composition[$row[csf('copm_one_id')]]; ?></div></td> 
                      <?
					 	 $tot_yarn_qty=0;
						
						foreach($yarn_count_head_arr as $count_id)
						{
							foreach($yarn_type_id as $yarn_key)
							{
								$countQty=0;
								$countQty+=$yarn_costing_qty_arr[$row[csf('po_id')]][$row[csf('construction')]][$row[csf('gsm_weight')]][$yarn_key][$count_id];
								if($countQty>0)
											$tdcountQty=number_format($countQty,2);
										else
											$tdcountQty='0.00';
								?>
								<td width="60" align="right"><div style="width:60px;word-wrap:break-word;"><? echo $tdcountQty; ?></div></td>
								<?
								$tot_yarn_qty+=$yarn_costing_qty_arr[$row[csf('po_id')]][$row[csf('construction')]][$row[csf('gsm_weight')]][$yarn_key][$count_id];
								$col_yarn_qty[$count_id]+=$yarn_costing_qty_arr[$row[csf('po_id')]][$row[csf('construction')]][$row[csf('gsm_weight')]][$yarn_key][$count_id];
							}
						}
						
					 ?>
                     <td width="" align="right"><p><? echo  number_format($tot_yarn_qty,2); ?></p></td>
                      
			<?
			$total_yarn_qty+=$tot_yarn_qty;
			$i++;
			
				
			}
			?>
            </table>
            <table class="rpt_table"  width="<? echo $td_with+1040;?>" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
           
              <tfoot>
                <th width="40"></th>
                 <th width="100"></th>
                 <th width="60"></th>
                 <th width="70"></th>
				 <th width="100"></th>
                 <th width="120"></th>
                 <th width="60"></th>
                 <th width="120"></th>
                 <th width="150"></th>
				 <th width="100">Total</th>
				<?
				$total_array_element=count($yarn_count_head_arr);

                foreach($yarn_count_head_arr as $count_id)
                {
                ?>
                 <th width="60" id="td_total_yarn_qty__"><p><? echo number_format($col_yarn_qty[$count_id],2); ?></p></th>
                 <?
                }
                ?>
                <th width="" id="td_final_total_yarn_qty__" align="right"><? echo number_format($total_yarn_qty,2);?></th>
              </tfoot>
            </table>
         </fieldset>
         <br>
          <table class="rpt_table" style="margin-left:10px;" align="left"  width="200" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
          <caption><b>Yarn Summary</b> </caption>
          <thead>
          <th width="100">Yarn Count </th>
           <th width="80">Req.Qty</th>
          </thead>
          <tbody>
         
			<?
			$k=1;$total_yarn_summary=0;
            foreach($yarn_count_head_arr as $count_id)
            {
            ?>
             <tr>
            <td width="100"><? echo $yarn_count_arr[$count_id];?></td>
             <td width="80" align="right"> <? echo  number_format($col_yarn_qty[$count_id],2);?> </td>
             </tr>
            <?
			$k++;
			$total_yarn_summary+=$col_yarn_qty[$count_id];
            }
            ?>
         <tfoot>
         <tr bgcolor="#CCCCCC"> 
          <td colspan="2" width="80" align="right"><b> <? echo  number_format($total_yarn_summary,2);?></b> </td>
         </tr>
         </tfoot>
          
         
          </tbody>
         </table>
         </div>
    	<?
       
		$html = ob_get_contents();
		ob_clean();
		//$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
		foreach (glob("*.xls") as $filename) {
		//if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename=$user_id."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');	
		$is_created = fwrite($create_new_doc, $html);
		echo "$html****$filename****$report_type****$total_array_element"; 
		exit();
    }
      

?>