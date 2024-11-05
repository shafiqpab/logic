<?
/*--------------------------------------------Comments----------------
Version (MySql)          :  
Version (Oracle)         :  
Converted by             :  
Converted Date           :  
Purpose			         : 	This form will create Order Allocation Entry
Functionality	         :	
JS Functions	         :
Created by		         :	Kaiyum 
Creation date 	         : 	5-10-2016
Requirment Client        : 
Requirment By            : 
Requirment type          : 
Requirment               : 
Affected page            : 
Affected Code            :                   
DB Script                : 
Updated by 		         : 	 
Update date		         : 		   
QC Performed BY	         :		
QC Date			         :	
Comments		         :
----------------------------------------------------------------------*/
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');

//************************************ Start*************************************************

if($action=="populate_data_wo_form")
{
	//$dbDataQty=return_field_value("po_quantity","wo_po_break_down","id='$data'");
	//$dbDataShipment=return_field_value("shipment_date","wo_po_break_down","id='$data'");
	$ex_data=explode("__",$data);
	if($ex_data[1]!=0) $item_id_cond=" and c.item_number_id='$ex_data[1]'"; else $item_id_cond="";
	$dbData=sql_select("Select b.shipment_date, sum(c.order_quantity) as po_qty_pcs from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.id='$ex_data[0]' $item_id_cond group by b.shipment_date");//(b.po_quantity*a.total_set_qnty)
	$dbDataQty=0; $newDate="";
	foreach($dbData as $po_row)
	{
		if($po_row[csf('shipment_date')]!="")
		{
			$newDate = date("d-m-Y", strtotime($po_row[csf('shipment_date')]));
			$dbDataQty=$po_row[csf('po_qty_pcs')];
		}
	}
	echo $dbDataQty."_".$newDate;
	exit();
}

if($action=="populate_data_smv_form")
{
	$data=explode("__",$data);
	$dbDataSmv=return_field_value("smv_pcs","wo_po_details_mas_set_details","gmts_item_id='$data[0]' and job_no='$data[1]'");
	echo $dbDataSmv."_" ." ";  
	exit();
}

if($action=="populate_data_po_cut_off_form")
{
	$data=explode("_",$data);
	$cut_date=$data[0];
	$po_id=$data[1];
	$item_id=$data[2];
	$sql_cut=sql_select("select sum(c.order_quantity) as po_qty from  wo_po_color_size_breakdown c where c.status_active=1 and c.is_deleted =0 and c.po_break_down_id in($po_id) and c.item_number_id in($item_id) and c.cutup_date='$cut_date'");
			
			$po_qty=0;
			foreach($sql_cut as $row)
			{
				
				$po_qty+=$row[csf('po_qty')];
			}
	echo "document.getElementById('txt_po_qty').value 		= '".$po_qty."';\n"; 
	exit();
}

if($action=="list_view_popup")
{
	$company_arr=return_library_array("select id,company_name from lib_company","id","company_name"); 
	$buyer_arr=return_library_array("select id,buyer_name from lib_buyer","id","buyer_name");
	$season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");
	
	$data=explode("__",$data);
	$allocated_arr=array();
	
	if($data[1]>0) $str_loc= "and location_name='".$data[1]."'"; else $str_loc="";
	
	$allocated_sql="select job_no, sum(allocated_qty) as allocated_qty from ppl_order_allocation_mst where is_deleted=0 and status_active=1  group by job_no";
	$allocated_res=sql_select($allocated_sql);
	
	$jobNo="";
	foreach($allocated_res as $row)
	{
		$allocated_arr[$row[csf('job_no')]]=$row[csf('allocated_qty')];
	}
	unset($allocated_res);
	
	$year_cond="";
	if($db_type==0)
	{
		$year_cond="YEAR(insert_date)";
	}
	else if ($db_type==2)
	{
		$year_cond="to_char(insert_date,'YYYY')";
	}
	//echo $job_no_cond; die;
	$sql="select id, job_no_prefix_num, buyer_name, style_ref_no, job_no, job_quantity, (job_quantity*total_set_qnty) as qty_pcs, season_matrix, $year_cond as year from wo_po_details_master where company_name='".$data[0]."' $str_loc and is_deleted=0 and status_active=1 order by id desc";// $job_no_cond
	//"select id,company_name,buyer_name,job_no,season from wo_po_details_master where is_deleted=0 and id<100"
	//echo $sql;die;
	$data_array=sql_select($sql);// die;
	?>
    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="520">
        <thead>
            <th width="20">SL</th>
            <th width="60">Job No.</th>
            <th width="60">Year</th>
            <th width="80">Buyer</th>
            <th width="90">Job Qty. (Pcs)</th>
            <th width="90">Style Ref</th>
            <th>Season</th>
        </thead>
     </table>
      <div style="width:520px; max-height:75px; overflow-y:scroll"> 
         <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="500" id="list_view">
            <tbody>
            <? 
            $i=1;
            foreach($data_array as $row)
            {  
				$allocated_qty=0;
				$allocated_qty=$allocated_arr[$row[csf('job_no')]];
				
				if( $allocated_qty<$row[csf('qty_pcs')] )
				{
					if($i%2==0) $bgcolor="#FFFFFF"; else $bgcolor="#E9F3FF";
					?>
					<tr id="tr_<? echo $i; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="change_color_tr('<? echo $i; ?>','<? echo $bgcolor; ?>'); get_php_form_data('<? echo $row[csf('id')]; ?>', 'load_php_data_to_forms_unallocated', 'requires/order_allocation_controller');"> 
						<td width="20"><? echo $i; ?></td>
						<td width="60"><? echo $row[csf('job_no_prefix_num')]; ?></td>
						<td width="60"><? echo $row[csf('year')]; ?></td>
						<td width="80"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></p></td>
						<td width="90" align="right"><? echo number_format($row[csf('qty_pcs')],0); ?></td>
                        <td width="90"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
						<td><p><? echo $season_arr[$row[csf('season_matrix')]]; ?></p></td>
					</tr>
				<? 
				$i++; 
				} 
			} 
            ?>
            </tbody>
        </table>
    </div>
    <?
	exit();
}

if($action=="order_allocation_list_view")
{
	$lib_company_arr=return_library_array( "select id,company_name from lib_company", "id","company_name" );
	$locationArr=return_library_array( "select id,location_name from lib_location", "id","location_name");
	$style_refArr=return_library_array( "select job_no,style_ref_no from  wo_po_details_master", "job_no","style_ref_no");
	$po_noArr=return_library_array( "select id,po_number from wo_po_break_down", "id","po_number");
	$arr=array(0=>$lib_company_arr,1=>$locationArr,3=>$style_refArr,4=>$po_noArr,5=>$garments_item,6=>$complexity_level);
	//echo $data;
	if($data!="") $jobs_cond=" and job_no='$data'"; else $jobs_cond="";
	$sql="select id, job_no, company_id, location_name, item, complexity,po_no, smv, allocated_qty, total_smv,cut_off_date from ppl_order_allocation_mst where is_deleted=0 and status_active=1 $jobs_cond";
	
	echo create_list_view("list_views","Company Name,Location,Job No,Style,Po No,Item,Complexity,Cut Off Date,SMV,Allocated Qty,Total SMV","140,140,110,130,100,100,100,70,100,80,80","1200","200",0, $sql,"get_php_form_data","id","'load_php_data_to_forms'", 1, "company_id,location_name,0,job_no,po_no,item,complexity,0,0,0,0", $arr , "company_id,location_name,job_no,job_no,po_no,item,complexity,cut_off_date,smv,allocated_qty,total_smv","requires/order_allocation_controller", 'setFilterGrid("list_views",-1);','0,0,0,0,0,0,0,3,1,1,1'); 
	exit();		
}

if ($action=="load_drop_down_buyer")
{
	if($data != 0){
		echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "sub_dept_load(this.value,document.getElementById('cbo_product_department').value); check_tna_templete(this.value); load_drop_down( 'requires/order_allocation_controller', this.value, 'load_drop_down_season', 'season_td'); " );   
		exit();
	}
	else{
		echo create_drop_down( "cbo_buyer_name", 140, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "sub_dept_load(this.value,document.getElementById('cbo_product_department').value); check_tna_templete(this.value); load_drop_down( 'requires/order_allocation_controller', this.value, 'load_drop_down_season', 'season_td'); " );   
		exit();
	}
		 
} 

if ($action=="load_drop_down_po_no")
{
	echo create_drop_down( "cbo_po_no", 120, "select id,po_number from wo_po_break_down where status_active =1 and is_deleted=0 and job_no_mst='$data'","id,po_number", 1, "-- Select PO No --", $selected, "set_value_po_qty(this.value);load_drop_down( 'requires/order_allocation_controller',document.getElementById('cbo_item').value+'_'+this.value, 'load_drop_down_cut_of_date', 'cut_off_td' );" );   
	exit();
} 

if ($action=="load_drop_down_item")
{
	$ex_data=explode('__', $data);
	$job=$ex_data[0];
	
    if(count(explode(",",$ex_data[1]))>1)
	{
		echo create_drop_down( "cbo_item", 140,  $garments_item,"",1, "-- Select Item --",$selected, "set_value_smv(this.value,'$job');load_drop_down( 'requires/order_allocation_controller',this.value+'_'+document.getElementById('cbo_po_no').value, 'load_drop_down_cut_of_date', 'cut_off_td' );", 0, $ex_data[1]);  
	}
	else
	{
		echo create_drop_down( "cbo_item",140,$garments_item,"",0,"-- Select Item --",$selected,"set_value_smv(this.value,'$job');load_drop_down( 'requires/order_allocation_controller',this.value+'_'+document.getElementById('cbo_po_no').value, 'load_drop_down_cut_of_date', 'cut_off_td' );",0,$ex_data[1]);  	
	}
	exit();
} 



if ($action=="load_cbo_item")
{
	$ex_data=explode("__",$data);
	//echo count(explode(',',$ex_data[0]));
	if(count(explode(',',$ex_data[0]))==1)
	{
		echo create_drop_down( "cbo_item", 140, $garments_item,"", 1, "--Select Item--", $ex_data[0], "set_value_smv(this.value,'$ex_data[1]');load_drop_down( 'requires/order_allocation_controller',this.value+'_'+document.getElementById('cbo_po_no').value, 'load_drop_down_cut_of_date', 'cut_off_td' );","",$ex_data[0]);
	}
	else
	{
		 echo create_drop_down( "cbo_item", 140, $garments_item,"", 0, "-- Select Item --", $ex_data[0], "set_value_smv(this.value,'$ex_data[1]');load_drop_down( 'requires/order_allocation_controller',this.value+'_'+document.getElementById('cbo_po_no').value, 'load_drop_down_cut_of_date', 'cut_off_td' );","",$ex_data[0] );	
	}  	
	exit(); 
}
if ($action=="load_drop_down_cut_of_date")
{
	$ex_data=explode('_', $data);
	$item_id=$ex_data[0];
	$po_id=$ex_data[1];
	
	$sql_cut=sql_select("select c.cutup_date from  wo_po_color_size_breakdown c where c.status_active=1 and c.is_deleted =0 and c.po_break_down_id in($po_id) and c.item_number_id in($item_id) group by c.cutup_date ");
			
			foreach($sql_cut as $row)
			{
				
				$cut_off_data_array[$row[csf('cutup_date')]]=change_date_format($row[csf('cutup_date')]);
			}
	if(count($cut_off_data_array) == 1){
		$selected = array_key_first($cut_off_data_array);
	}
	echo create_drop_down( "cbo_po_cut_date", 80, $cut_off_data_array,"", 1, "-Select Cut-Off Date-", $selected, "set_cut_po_qty(this.value);","" );   
	exit();
} 
  
if ($action=="load_cbo_complexity")
{
	//( $field_id, $field_width, $query, $field_list, $show_select, $select_text_msg, $selected_index, $onchange_func, $is_disabled, $array_index, $fixed_options, $fixed_values, $not_show_array_index, $tab_index, $new_conn, $field_name )
	$dt=explode(",",$data);
	if(count($dt)>1)
		echo create_drop_down( "cbo_complexity", 80, $complexity_level,"", 1, "-- Select Item --", $selected, "",0,$data );  
	else
		 echo create_drop_down( "cbo_complexity", 80, $complexity_level,"", 0, "-- Select Item --", $selected, "",0,$data );  
	exit();	 
}

if ($action=="load_cbo_dealing_merchant")
{
	echo create_drop_down( "cbo_dealing_merchant", 120, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "",1 );
	exit();
}

if ($action=="load_drop_down_season_buyer")
{
	$datas=explode('_',$data);
	echo create_drop_down( "cbo_season_name", 120, "select a.id,a.season_name from lib_buyer_season a,variable_order_tracking b where a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and a.buyer_id='$datas[0]' and b.company_name='$datas[1]' and b.season_mandatory=1 and b.variable_list=44","id,season_name", 1, "-- Select Season --", $selected, "",1 );
	exit();
}

if ($action=="load_location")
{
	echo create_drop_down( "cbo_location_name", 130, "select location_name,id from lib_location where company_id='$data' and is_deleted=0  and status_active=1  order by location_name",'id,location_name', 1, '--- Select Location ---', 0, ""  );
	exit();
}

if ($action=="order_popup")
{
  	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $_SERVER['SERVER_NAME'];
	?>
	<script>
		function set_checkvalue()
		{
			if(document.getElementById('chk_job_wo_po').value==0) document.getElementById('chk_job_wo_po').value=1;
			else document.getElementById('chk_job_wo_po').value=0;
		}
		
		function js_set_value( job_no )
		{
			document.getElementById('selected_job').value=job_no;
			parent.emailwindow.hide();
		}
    </script>
</head>
<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="900" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
    	<tr>
        	<td align="center" width="100%">
            	<table  cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                    <thead>
                    <th colspan="4">&nbsp;</th>
                       <th colspan="2"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                    <th colspan="4">&nbsp;</th>
                    </thead>
                    <thead>                	 
                        <th width="140" class="must_entry_caption">Company Name</th>
                        <th width="140" class="must_entry_caption">Buyer Name</th>
                        <th width="70">Job No</th>
                        <th width="70">Style Ref </th>
                        <th width="70">Internal Ref</th>
                        <th width="70">File No</th>
                        <th width="80">Order No</th>
                        <th colspan="2">Ship Date Range</th>
                        <th width="70"><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">Job Without PO</th>           
                    </thead>
        			<tr>
                    	<td> 
                        <input type="hidden" id="selected_job">
                        <input type="hidden" id="garments_nature" value="<? echo $garments_nature; ?>">
							<? 
								echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", '',"load_drop_down( 'order_allocation_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
							?>
                    </td>
                   	<td id="buyer_td">
                     <? 
						//echo create_drop_down( "cbo_buyer_name", 140, $blank_array,'', 1, "-- Select Buyer --" );
						echo create_drop_down( "cbo_buyer_name", 140, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "sub_dept_load(this.value,document.getElementById('cbo_product_department').value); check_tna_templete(this.value); load_drop_down( 'requires/order_allocation_controller', this.value, 'load_drop_down_season', 'season_td'); " ); 
					?>

					</td>
                    <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:60px"></td>
                    <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:65px"></td>
                    <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:65px"></td>
                    <td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:65px"></td>
                    <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:70px"></td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px"></td>
					<td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"></td> 
            		<td align="center">
                     <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('garments_nature').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file_no').value, 'create_po_search_list_view', 'search_div', 'order_allocation_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" /></td>
        		</tr>
             </table>
          </td>
        </tr>
        <tr>
            <td  align="center" height="40" valign="middle">
             <? 
			echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );		
			echo load_month_buttons();  ?>
            </td>
            </tr>
        <tr>
            <td align="center" valign="top" id="search_div"></td>
        </tr>
    </table>    
    </form>
   </div>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_po_search_list_view")
{
	//echo $data;die;
	$data=explode('_',$data);
	if($data[0]==0 && $data[1]==0)
	{
		echo "<span style='color:red; font-weight:bold; font-size:20px; text-align:center'>Please select company or buyer first.";
		die;
	}
	if ($data[0]!=0) $company=" and a.company_name='$data[0]'"; else $company="";
	if ($data[1]!=0) $buyer=" and a.buyer_name='$data[1]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	
	if($db_type==0)
	{
		$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[7]";
		if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}
	else if($db_type==2)
	{
		$year_cond=" and to_char(a.insert_date,'YYYY')=$data[7]";
		if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}
	$order_cond="";
	$job_cond=""; 
	$style_cond="";
	if($data[8]==1)
	{
		if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num='$data[6]'  $year_cond";
		if (trim($data[9])!="") $order_cond=" and b.po_number='$data[9]'  "; //else  $order_cond=""; 
		if (trim($data[10])!="") $style_cond=" and a.style_ref_no='$data[10]'  "; //else  $style_cond=""; 
	}
	else if($data[8]==4 || $data[8]==0)
	{
		if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '%$data[6]%'  $year_cond"; //else  $job_cond=""; 
		if (trim($data[9])!="") $order_cond=" and b.po_number like '%$data[9]%'  ";
		if (trim($data[10])!="") $style_cond=" and a.style_ref_no like '%$data[10]%'  "; //else  $style_cond=""; 
	}
	else if($data[8]==2)
	{
		if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '$data[6]%'  $year_cond"; //else  $job_cond=""; 
		if (trim($data[9])!="") $order_cond=" and b.po_number like '$data[9]%'  ";
		if (trim($data[10])!="") $style_cond=" and a.style_ref_no like '$data[10]%'  "; //else  $style_cond=""; 
	}
	else if($data[8]==3)
	{
		if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '%$data[6]'  $year_cond"; //else  $job_cond=""; 
		if (trim($data[9])!="") $order_cond=" and b.po_number like '%$data[9]'  ";
		if (trim($data[10])!="") $style_cond=" and a.style_ref_no like '%$data[10]'  "; //else  $style_cond=""; 
	}
			
	$internal_ref = str_replace("'","",$data[11]);
	$file_no = str_replace("'","",$data[12]);
	if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='".trim($file_no)."' "; 
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping='".trim($internal_ref)."' "; 
	/*if($file_no!="" || $internal_ref!="")
	{
	$sql_po=sql_select("select b.id from  wo_po_break_down b where   b.status_active=1 and b.is_deleted=0  $file_no_cond  $internal_ref_cond");
	 $po_id_data=$sql_po[0][csf('id')];
	}
	if($po_id_data!="" || $po_id_data!=0) $po_data_cond=" and b.id='$po_id_data' "; else $po_data_cond="";*/
	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (2=>$buyer_arr,8=>$item_category);
	if ($data[2]==0)
	{
		if($db_type==0)
		{
	 		$sql= "SELECT a.job_no_prefix_num, a.job_no,a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.po_number, (b.po_quantity*a.total_set_qnty) as po_quantity, b.shipment_date,a.garments_nature,b.grouping,b.file_no,DATEDIFF(pub_shipment_date,po_received_date) as  date_diff,SUBSTRING_INDEX(a.`insert_date`, '-', 1) as year from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.garments_nature=$data[5] and a.status_active=1 and b.status_active=1 $shipment_date $company $buyer $job_cond $order_cond $style_cond $file_no_cond $internal_ref_cond ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." order by a.id DESC";
		}
	 	else if($db_type==2)
		{
	 		$sql= "SELECT a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity, b.po_number, (b.po_quantity*a.total_set_qnty) as po_quantity, b.shipment_date, a.garments_nature,b.grouping,b.file_no,(pub_shipment_date - po_received_date) as  date_diff,to_char(a.insert_date,'YYYY') as year from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst  and a.status_active=1 and b.status_active=1 $shipment_date $company $buyer ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $job_cond $order_cond $style_cond $file_no_cond $internal_ref_cond order by a.id DESC";
		}
		// echo $sql;
		echo  create_list_view("list_view", "Job No,Year,Buyer Name,Style Ref. No,Job Qty.,PO number,PO Quantity,Shipment Date,Gmts Nature,Ref no, File No,Lead time", "40,30,100,100,70,90,70,60,60,70,70,50","900","220",0, $sql , "js_set_value", "job_no", "", 1, "0,0,buyer_name,0,0,0,0,0,garments_nature,0,0,0", $arr , "job_no_prefix_num,year,buyer_name,style_ref_no,job_quantity,po_number,po_quantity,shipment_date,garments_nature,grouping,file_no,date_diff", "",'','0,0,0,0,1,0,1,3,0,0,0,0');
	}
	else
	{
		$arr=array (2=>$comp,3=>$buyer_arr,5=>$item_category);
		if($db_type==0)
		{
			$sql= "SELECT a.job_no_prefix_num,a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.garments_nature,SUBSTRING_INDEX(a.`insert_date`, '-', 1) as year from wo_po_details_master a where a.job_no not in( select distinct job_no_mst from wo_po_break_down where status_active=1 and is_deleted=0 ) and a.garments_nature=$data[5] and a.status_active=1 ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')."   and a.is_deleted=0 $company $buyer $job_cond $style_cond  order by a.id desc";
		}
		else if($db_type==2)
		{
			$sql= "SELECT a.job_no_prefix_num,a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.garments_nature,to_char(a.insert_date,'YYYY') as year from wo_po_details_master a where a.job_no not in( select distinct job_no_mst from wo_po_break_down where status_active=1 and is_deleted=0 ) and a.garments_nature=$data[5] and a.status_active=1 ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')."  and a.is_deleted=0 $company $buyer $job_cond $style_cond order by a.id desc";
		}
		echo  create_list_view("list_view", "Job No,Year,Company,Buyer Name,Style Ref. No,Gmts Nature", "90,60,120,100,100,90","900","320",0, $sql , "js_set_value", "job_no", "", 1, "0,0,company_name,buyer_name,0,garments_nature", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no,garments_nature", "",'','0,0,0,0,0,0');
	}
	exit();
} 

if ($action=="populate_data_from_search_popup")
{
	$company_id=return_field_value("company_name","wo_po_details_master","job_no ='$data' and is_deleted=0 and status_active=1");
	//$po_no=return_field_value("po_number","wo_po_break_down","job_no_mst ='$data' and is_deleted=0 and status_active=1");
	 //$po_nos=return_field_value("po_number","wo_po_break_down","job_no ='$data' and is_deleted=0 and status_active=1");
	//echo "document.getElementById('cbo_po_no').value 		= '".$po_nos."';\n"; 
	
	
	$data_array=sql_select("select a.id, a.garments_nature,a.season, a.job_no, a.job_no_prefix, a.job_no_prefix_num, a.copy_from, a.company_name, a.buyer_name, a.location_name, a.style_ref_no, a.style_description, a.product_dept, a.product_code, a.pro_sub_dep, a.currency_id, a.agent_name, a.client_id, a.order_repeat_no, a.region, a.product_category, a.team_leader, a.dealing_marchant, a.bh_merchant, a.packing, a.remarks, a.ship_mode, a.order_uom, a.set_break_down, a.gmts_item_id, a.total_set_qnty, a.set_smv, a.season_buyer_wise, a.quotation_id, (a.job_quantity*a.total_set_qnty) as job_quantity , a.order_uom, a.avg_unit_price, a.currency_id, a.total_price, a.factory_marchant, a.insert_date, b.gmts_item_id, b.smv_pcs,b.complexity,c.id,c.po_number,c.po_quantity 
 from wo_po_details_master a,wo_po_details_mas_set_details b,wo_po_break_down c  where a.job_no=b.job_no and a.job_no='$data' and b.job_no='$data' and a.job_no=c.job_no_mst");
 $i=0;
 $job_number="";
  
   
	foreach ($data_array as $row)
	{
		if($i==0)
		{
			$i++;
			$cbo_dealing_merchant= create_drop_down( "cbo_dealing_merchant", 172, "select id,team_member_name from lib_mkt_team_member_info where team_id='".$row[csf("team_leader")]."'and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );
			$job_number=$row[csf("job_no")];
			// echo "document.getElementById('cbo_po_no').value 		= '".$row[csf("po_number")]."';\n"; 
			echo "document.getElementById('txt_job_no').value 		= '".$row[csf("job_no")]."';\n"; 
			echo "document.getElementById('txt_style').value 		= '".$row[csf("style_ref_no")]."';\n"; 
			echo "document.getElementById('cbo_company_name').value 		= '".$row[csf("company_name")]."';\n";  
			echo "document.getElementById('cbo_buyer_name').value 			= '".$row[csf("buyer_name")]."';\n"; 
			echo "document.getElementById('cbo_team_leader').value 			= '".$row[csf("team_leader")]."';\n"; 
			echo "document.getElementById('cbo_dealing_merchant').value 	= '".$row[csf("dealing_marchant")]."';\n"; 
			echo "document.getElementById('cbo_product_department').value 	= '".$row[csf("product_dept")]."';\n";
			echo "document.getElementById('txt_product_code').value 		= '".$row[csf("product_code")]."';\n";
			echo "document.getElementById('cbo_season_name').value 			= '".$row[csf("season_buyer_wise")]."';\n"; 
			echo "document.getElementById('tot_smv_qnty').value 			= '".$row[csf("set_smv")]."';\n";
			echo "document.getElementById('txt_total_job_quantity').value 	= '".$row[csf("job_quantity")]."';\n";
			//echo "document.getElementById('txt_qty_balance').value 	= '".$tot."';\n";
			echo "document.getElementById('cbo_item').value 				= '".$row[csf("gmts_item_id")]."';\n";
			
			$insert_date=$row[csf("insert_date")];
			$insert_date=explode(" ",$insert_date);
			//echo $insert_date[0];
			echo "document.getElementById('txt_entry_date').value 			= '".change_date_format($insert_date[0])."';\n";
			echo "document.getElementById('txt_po_qty').value 				= '".$row[csf("po_quantity")]."';\n";
			//echo "document.getElementById('cbo_po_no').value 				= '".$row[csf("po_number")]."';\n";
			echo "document.getElementById('update_id').value 				= '".$row[csf("job_no")]."';\n";  
		}
		
		if($item=='') $item =$row[csf("gmts_item_id")]; else $item .= ",".$row[csf("gmts_item_id")]; 
		if($complexity=='') $complexity =$row[csf("complexity")]; else $complexity .= ",".$row[csf("complexity")];
		//if($po_number=='') $po_number =$row[csf("po_number")]; else $po_number .= ",".$row[csf("po_number")]; 
	}
	
	echo "load_drop_down( 'requires/order_allocation_controller', '".$row[csf("job_no")]."', 'load_drop_down_po_no', 'po_td' ) ;\n";
	echo "load_drop_down( 'requires/order_allocation_controller', '".$item.'__'.$row[csf("job_no")]."', 'load_cbo_item', 'gmts_item_td' ) ;\n";
 	echo "load_drop_down( 'requires/order_allocation_controller', '".$complexity."', 'load_cbo_complexity', 'complexity_td' ) ;\n";
	echo "document.getElementById('cbo_complexity').value 			= '".$row[csf("complexity")]."';\n";
	//echo "load_drop_down( 'requires/order_allocation_controller', '".$item.'_'.$row[csf("job_no")]."', 'load_cbo_item', 'gmts_item_td' ) ;\n";
	//load_drop_down( 'requires/order_allocation_controller',this.value+'_'+document.getElementById('cbo_po_no').value, 'load_drop_down_cut_of_date', 'cut_off_td' );
	// echo "document.getElementById('cbo_po_no').value 				= '".$po_no."';\n";  
	 
	//echo "show_list_view('".$job_number."','order_allocation_list_view','section_list_view','../prod_planning/requires/order_allocation_controller','setFilterGrid(\'list_views\',-1)')";
	exit();
}

if ($action=="date_wise_distr_popup")
{
  	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
	function js_set_value( )
	{
		var tr_no=$('table #dist_tr_id').length;
		var fst='';
		//var date_pop='';
		//var qty_pop='';
		//var smv_pop='';
		
		for(i=1;i<=tr_no;i++)
		{
			fst += $("#each_date_"+i).val()+'*'+$("#each_qty_"+i).val()+'*'+$("#each_smv_"+i).val()+'_';
		}
		  
		  		
		  /*		for(i=1;i<=tr_no;i++)
		  		{
		  			if(date_pop =="" || qty_pop =="" || smv_pop ==""){
		  				date_pop += $("#each_date_"+i).val();
		  				qty_pop += $("#each_qty_"+i).val();
		  				smv_pop += $("#each_smv_"+i).val();
		  			}else{
		  				date_pop += "*"+$("#each_date_"+i).val();
		  				qty_pop += "*"+$("#each_qty_"+i).val();
		  				smv_pop += "*"+$("#each_smv_"+i).val();
		  			}
		  			//alert(qty_pop);
		  		}*/		
		  		//document.getElementById('hidden_distr_month').value=(date_pop+'_'+qty_pop+'_'+smv_pop);
		document.getElementById('hidden_distr_month_row').value=(fst);
		//return;
		parent.emailwindow.hide();
	}
	
	function multi_smv(i, smv)
	{
		var  allocated_qty='<? echo $txt_allocated_qty; ?>';
		var count_qty=0;
		var tot_tr_no=$('table #dist_tr_id').length;
		for(j=1;j<=tot_tr_no;j++)
		{
			count_qty += $("#each_qty_"+j).val()*1;
		}
		//alert(count_qty);
		if(count_qty>allocated_qty)
		{
			alert('Quantity is greater than previous quantity');
			 $("#each_qty_"+i).css("background-color", "red");
			 $("#each_qty_"+i).val('');
		}
		else
		{
			$("#each_smv_"+i).val( $("#each_qty_"+i).val()*smv);
		}
	}
    </script>

</head>

<body>
<div align="center" style="width:100%;" >
<form name="distributionfrm_1"  id="distributionfrm_1" autocomplete="off">
	<table width="100%" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
    	<tr>
        	<td align="center" width="100%">
            	<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" id="tbl_id">
                       
                    <thead>                	 
                        <th width="30">SL</th>
                        <th width="100">Date Name</th>
                        <th width="70">Qty</th>
                        <th width="70">SMV</th>
                        
                    </thead>
                    <?
						
						$days=datediff("d",$date_form,$date_to);
						//calculate qty
						$txt_allocated_qty_cal=floor($txt_allocated_qty/$days);
						$txt_allocated_qty_last= $txt_allocated_qty-($txt_allocated_qty_cal*($days-1));
					   //echo $dist_data;
						if(trim($dist_data)=='')
						{
							for($i=1; $i<=$days; $i++)
							{
								$date = add_date($date_form,$i-1); //date ("d-m-Y", strtotime("+1 day", strtotime($date)));
								if($i==$days) $txt_allocated_qty_cal=$txt_allocated_qty_last;
								?>
								<tr id="dist_tr_id">
									<td><? echo $i;?></td>
									<td><input type="text" class="text_boxes" id="each_date_<? echo $i;?>" name="each_date" value="<? echo $date; ?>" style="width:70px;" readonly></td>
									<td><input type="text" class="text_boxes" id="each_qty_<? echo $i;?>" name="each_qty" value="<? echo $txt_allocated_qty_cal; ?>" onBlur="multi_smv(<? echo $i; ?>,<? echo $tot_smv_qnty; ?>);" style="width:70px; text-align:right;"></td>
									<td><input type="text" class="text_boxes" id="each_smv_<? echo $i;?>" name="each_smv" value="<? echo $txt_allocated_qty_cal*$tot_smv_qnty; ?>" style="width:70px; text-align:center;" readonly></td>
								</tr>
								<?
								
								//$i++;
							}
						}
						else
						{
							$dist_data=explode("_",$dist_data);
							$i=0;
							foreach($dist_data as $dta)
							{
								$i++;
								$ndata=explode("*",$dta);
								?>
								<tr id="dist_tr_id">
									<td><? echo $i;?></td>
									<td><input type="text" class="text_boxes" id="each_date_<? echo $i;?>" name="each_date" value="<? echo $ndata[0]; ?>" style="width:70px;" readonly></td>
									<td><input type="text" class="text_boxes" id="each_qty_<? echo $i;?>" name="each_qty" value="<? echo $ndata[1]; ?>" onBlur="multi_smv(<? echo $i; ?>,<? echo $tot_smv_qnty; ?>);" style="width:70px; text-align:right;"></td>
									<td><input type="text" class="text_boxes" id="each_smv_<? echo $i;?>" name="each_smv" value="<? echo $ndata[2]; ?>" style="width:70px; text-align:center;" readonly></td>
								</tr>
								<?
							}
						}
						
					?>
                    <tr>
                        <input type="hidden" id="hidden_distr_month_row">
                    	<td colspan="4" align="center"><input type="button" name="button1" class="formbutton" value="Close" onClick="js_set_value(1);"></td>
                    </tr>
        			
             	</table>
          </td>
       </tr>
    </table>    
     
    </form>
   </div>
      
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

?>

 <?
if($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	//	die();
	if ($operation==0)
	{
/*		if (is_duplicate_field( "subsection_name","lib_subsection","subsection_name=$txt_subsection" )==1)
		{
			echo "11**0"; die;
		}*/
		$con = connect();
		
		if($db_type==0)
		{
			
			mysql_query("BEGIN");
		}
		//txt_subsection*cbo_section*cbo_status*txt_remark*update_id 
		
		$tot=return_field_value("sum(allocated_qty)","ppl_order_allocation_mst","job_no=$txt_job_no and po_no=$cbo_po_no and item=$cbo_item and cut_off_date=$cbo_po_cut_date and status_active=1 and is_deleted=0");
		 $total_allow=$tot+str_replace("'","", $txt_allocated_qty );
		 $po_qty=str_replace("'","", $txt_po_qty);
		// echo "10**".$tot.'='.$po_qty;die;
		if( ($tot+str_replace("'","", $txt_allocated_qty ))>str_replace("'","", $txt_po_qty ))
		{
			echo "31**0"; 
			//echo $tot.'=='.str_replace("'","", $txt_allocated_qty ).'=='.str_replace("'","", $txt_po_qty );
			disconnect($con);
			die;
		}
		$po_cut_date=(str_replace("'", "", $cbo_po_cut_date)==0)? "" : str_replace("'", "", $cbo_po_cut_date);
		$id=return_next_id( "id", "ppl_order_allocation_mst", 1 ) ;
		$field_array="id,job_no,balance,company_id,location_name,item,complexity,smv,allocated_qty,total_smv,customize_smv,date_from,date_to,po_no,po_quantity,shipment_date,cut_off_date,inserted_by,insert_date,is_deleted"; 
		$data_array="(".$id.",".$txt_job_no.",".$txt_qty_balance.",".$cbo_company_mst.",".$cbo_location_name.",".$cbo_item.",".$cbo_complexity.",".$tot_smv_qnty.",".$txt_allocated_qty.",".$txt_total_smv.",".$tot_c_smv_qnty.",".$txt_date_from.",".$txt_date_to.",".$cbo_po_no.",".$txt_po_qty.",".$txt_shipment_date.",'".$po_cut_date."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','0')";
			
			// insert details table data //
			//echo $hidden_row_pop;
			$id_dtls=return_next_id( "id","ppl_order_allocation_dtls", 1 ) ;
			$field_array_dtls="id,mst_id,date_name,date_format_name,qty,smv,inserted_by,insert_date"; 
			$exp_row=explode("_",str_replace("'","",$hidden_row_pop));
			//$data_array_dtls="";
			for($i=0;$i<count($exp_row);$i++)
			{
				$data=explode("*",$exp_row[$i]);  
				if($db_type==0)	$date_name=$data[0]; else $date_name=date("d-M-Y",strtotime($data[0]));
				if($db_type==0)	$date_format_name=$data[0]; else $date_format_name=date("d-M-Y",strtotime($data[0]));
				$qty=$data[1];
				$smv=$data[2];
				//if($trims_qty=='') $trims_qty=0;else $trims_qty=$trims_qty;
				if($qty>0)
				{
					if ($data_array_dtls!="") $data_array_dtls.=",";
					$data_array_dtls.="(".$id_dtls.",".$id.",'".$date_name."','".$date_format_name."',".$qty.",'".$smv."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; 
					$id_dtls=$id_dtls+1;
				}
			}
			//echo "0**".$data_array_dtls; die;
			//echo "10**insert into ppl_order_allocation_mst (".$field_array.") values".$data_array.'<br>';
			//echo "10**insert into ppl_order_allocation_dtls (".$field_array_dtls.") values".$data_array_dtls; die;
			$rID=sql_insert("ppl_order_allocation_mst",$field_array,$data_array,1); //insert mst tbl
			$rID_dtls=sql_insert("ppl_order_allocation_dtls",$field_array_dtls,$data_array_dtls,1); //insert dtls tbl
		//echo "10**".$rID."**".$rID_dtls."**Aziz"; die;
		if($db_type==0)  
		{
			if($rID && $rID_dtls)
			{
				mysql_query("COMMIT");  
				echo "0**".$rID;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
			
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID_dtls)
			{
				oci_commit($con); 
				echo "0**".$rID;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$rID;
			}
		}
	disconnect($con);
	die;
	}
	elseif ($operation==1)//Update Here
	{		
/*		if (is_duplicate_field( "inv_pur_req_id","inv_reference_closing","inv_pur_req_id=$txt_subsection and id!=$update_id and is_deleted=0") == 1)
		{
			echo "11**0"; die;
		}
		else
		{*/
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN"); 
			}
			$po_cut_date=(str_replace("'", "", $cbo_po_cut_date)==0)? "" : str_replace("'", "", $cbo_po_cut_date);
			
			$tot=return_field_value("sum(allocated_qty)","ppl_order_allocation_mst","job_no=$txt_job_no and po_no=$cbo_po_no and item=$cbo_item and id!=$update_id and cut_off_date=$cbo_po_cut_date ");
		
			if( ($tot+str_replace("'","", $txt_allocated_qty )) > str_replace("'","", $txt_po_qty ))
			{
				echo "31**0"; disconnect($con); die;
			}
			
			
			$field_array="job_no*po_no*balance*company_id*location_name*item*complexity*smv*allocated_qty*total_smv*customize_smv*date_from*date_to*cut_off_date*updated_by*update_date";
			$data_array="".$txt_job_no."*".$cbo_po_no."*".$txt_qty_balance."*".$cbo_company_mst."*".$cbo_location_name."*".$cbo_item."*".$cbo_complexity."*".$tot_smv_qnty."*".$txt_allocated_qty."*".$txt_total_smv."*".$tot_c_smv_qnty."*".$txt_date_from."*".$txt_date_to."*'".$po_cut_date."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			
			// insert details table data //
			//echo $hidden_row_pop;
			$id_dtls=return_next_id("id","ppl_order_allocation_dtls", 1 ) ;
	 		$field_array_dtls="id,mst_id,date_name,date_format_name,qty,smv,inserted_by,insert_date"; 
		 
			$exp_row=explode("_",str_replace("'","",$hidden_row_pop));
			//$data_array_dtls="";
			for($i=0;$i<count($exp_row);$i++)
			{
			$data=explode("*",$exp_row[$i]);  
			if($db_type==0)	$date_name=$data[0]; else $date_name=date("d-M-Y",strtotime($data[0]));
			if($db_type==0)	$date_format_name=$data[0]; else $date_format_name=date("d-M-Y",strtotime($data[0]));
			$qty=$data[1];
			$smv=$data[2];
			$po_no=$data[3];
			//if($trims_qty=='') $trims_qty=0;else $trims_qty=$trims_qty;
				if($qty>0)
				{
					if ($data_array_dtls!="") $data_array_dtls.=",";
					$data_array_dtls.="(".$id_dtls.",".$update_id.",'".$date_name."','".$date_format_name."',".$qty.",'".$smv."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; 
					$id_dtls=$id_dtls+1;
				}
			}
			
			//$rID2=execute_query(bulk_update_sql_statement( "pro_batch_create_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $id_arr ));
			//echo "update ppl_order_allocation_mst set(".$field_array.")=".$data_array; die;
			//echo "delete from ppl_order_allocation_dtls (".$field_array_dtls.") values".$data_array_dtls; die;
			$rID=sql_update("ppl_order_allocation_mst", $field_array, $data_array,"id",$update_id,1);
			$rID_dtls_dlt=execute_query( "delete from ppl_order_allocation_dtls where mst_id=$update_id",0);
			$rID_dtls=sql_insert("ppl_order_allocation_dtls",$field_array_dtls,$data_array_dtls,1); //insert dtls tbl
			//echo "10**".$rID."**".$rID_dtls_dlt."**".$rID_dtls."**Aziz"; die;
			if($db_type==0)
			{
				if($rID && $rID_dtls_dlt && $rID_dtls)
				{
					mysql_query("COMMIT"); 
					echo "1**".$rID;
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID;
				}
			}
			if($db_type==2 || $db_type==1 )
			{
				//echo"555**".$rID."**".$rID_dtls_dlt."**".$rID_dtls."**".$rID_dtls; die;
				if($rID && $rID_dtls_dlt && $rID_dtls)
				{
					oci_commit($con);   
					echo "1**".$rID;
				}
				else
				{
					oci_rollback($con);
					echo "10**".$rID;
				}
			}
			disconnect($con);
			die;
		}
	
	else if ($operation==2)   // Delete Here
	{
		
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
			
		$field_array="updated_by*update_date*status_active*is_deleted";
	    $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		
		$rID=sql_delete("ppl_order_allocation_mst",$field_array,$data_array,"id",$update_id,1);
		//$rID=sql_delete("tbl_department_test",$field_array,$data_array,"id","$update_id",0);

		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "1**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		if($db_type==2 || $db_type==1 )
		{
	          if($rID )
			    {
					oci_commit($con);   
					echo "2**".$rID;
				}
			else{
					oci_rollback($con);
					echo "10**".$rID;
				}
		}
		disconnect($con);
		die;
	}	
}
/*
if($action=="load_php_data_to_form")
{
	//echo "document.getElementById('txt_sec_name').value = 'okk';\n";
	//$nameArray=sql_select( "select id,job_no,balance,company_id,item,complexity,smv,allocated_qty,total_smv,date_from,date_to  from ppl_order_allocation_mst where id='$data'" );
	
	//$nameArray=sql_select( "select a.id as main_id,a.job_no,a.balance,a.company_id,a.location_name,a.item,a.complexity,a.smv,a.allocated_qty,a.total_smv,a.date_from,a.date_to,b.mst_id,b.qty,b.smv,c.company_name,c.buyer_name,c.team_leader,c.dealing_marchant,c.product_dept,c.product_code,c.season_buyer_wise,c.job_quantity from ppl_order_allocation_mst a,ppl_order_allocation_dtls b,wo_po_details_master c where a.id='$data' and b.mst_id='$data' and a.job_no=c.job_no" );

	$nameArray=sql_select( "select a.id as main_id,a.job_no,a.balance,a.company_id,a.location_name,a.item,a.complexity,a.smv,a.allocated_qty,a.total_smv,a.date_from,a.date_to,c.company_name,c.buyer_name,c.team_leader,c.dealing_marchant,c.product_dept,c.product_code,c.season_buyer_wise,c.job_quantity from ppl_order_allocation_mst a,wo_po_details_master c where a.id='$data' and a.job_no=c.job_no" );

	//$dtls_update_ids="";
	$nameArray_dtls=sql_select( "select id as dtls_id,mst_id,po_no,date_name,qty,smv from ppl_order_allocation_dtls where mst_id='$data'");
	$update_dtls_row='';
	$po_no='';
	foreach ($nameArray_dtls as $inf) 
	{
		$update_dtls_row .= $inf[csf("date_name")].'*'.$inf[csf("qty")].'*'.$inf[csf("smv")].'*'.$inf[csf("po_no")].'_';
	$po_no=$inf[csf("po_no")];
	}
	//echo chop($str,"World!");
	//$update_dtls_data=chop($update_dtls_row,"-");
	echo "document.getElementById('hidden_row_pop').value   = '".chop($update_dtls_row,"_")."';\n";
	echo "document.getElementById('txt_popup_no').value   = '".chop($update_dtls_row,"_")."';\n";

	//echo "document.getElementById('cbo_po_no').value 		= '".$inf[csf("po_no")]."';\n";
	
	
	
	foreach ($nameArray as $inf) 
	{	
		echo "document.getElementById('update_id').value   			= '".($inf[csf("main_id")])."';\n";
  
		// echo "document.getElementById('cbo_po_no').value 			= '".($inf[csf("po_no")])."';\n";
		echo "document.getElementById('txt_job_no').value 			= '".($inf[csf("job_no")])."';\n";
		echo "document.getElementById('cbo_company_name').value 	= '".($inf[csf("company_name")])."';\n";
		echo "document.getElementById('cbo_buyer_name').value 	  	= '".($inf[csf("buyer_name")])."';\n";
		echo "document.getElementById('cbo_team_leader').value   	= '".($inf[csf("team_leader")])."';\n";
		echo "document.getElementById('cbo_dealing_merchant').value = '".($inf[csf("dealing_marchant")])."';\n";
		echo "document.getElementById('txt_total_job_quantity').value = '".($inf[csf("job_quantity")])."';\n";
		echo "document.getElementById('cbo_product_department').value = '".($inf[csf("product_dept")])."';\n";
		echo "document.getElementById('cbo_season_name').value 	  	= '".($inf[csf("season_buyer_wise")])."';\n";
		echo "document.getElementById('txt_qty_balance').value 		= '".($inf[csf("balance")])."';\n";
		echo "document.getElementById('cbo_company_mst').value 		= '".($inf[csf("company_id")])."';\n";
		
		echo "load_drop_down( 'requires/order_allocation_controller', '".($inf[csf("company_id")])."', 'load_location', 'location_td' );\n";
		echo "document.getElementById('cbo_location_name').value  = '".($inf[csf("location_name")])."';\n"; 

		echo "document.getElementById('cbo_item').value 			= '".($inf[csf("item")])."';\n";
		echo "document.getElementById('cbo_complexity').value 		= '".($inf[csf("complexity")])."';\n";
		echo "document.getElementById('tot_smv_qnty').value 		= '".($inf[csf("smv")])."';\n";
		 //echo "document.getElementById('cbo_po_no').value 		= '".($inf[csf("po_no")])."';\n";
		echo "document.getElementById('txt_allocated_qty').value 	= '".($inf[csf("allocated_qty")])."';\n";
		echo "document.getElementById('txt_total_smv').value 		= '".($inf[csf("total_smv")])."';\n";
		echo "document.getElementById('txt_date_from').value 		= '".change_date_format($inf[csf("date_from")])."';\n";
		echo "document.getElementById('txt_date_to').value 			= '".change_date_format($inf[csf("date_to")])."';\n";
		echo "load_drop_down( 'requires/order_allocation_controller', '".$inf[csf("job_no")]."', 'load_drop_down_po_no', 'po_td' ) ;\n";
		 
		//echo "load_drop_down( 'requires/order_allocation_controller', '".$inf[csf("job_no")]."', 'load_drop_down_po_no', 'po_td' ) ;\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_order_allocation_entry',1);\n";
	}
	//echo "document.getElementById('hidden_row_pop').value   	= '".($dtls_update_ids)."';\n";
	exit();	
}
*/

if($action=="section_list_view_action")
{
	$lib_company_arr=return_library_array( "select id,company_name from lib_company", "id","company_name" );
	$arr=array(0=>$lib_company_arr, 3=>$garments_item,4=>$complexity_level);
	$sql="select a.id,a.job_no,a.company_id,a.po_no,a.item,complexity,a.smv,a.allocated_qty,a.total_smv,b.po_number 
from ppl_order_allocation_mst a, wo_po_break_down b
where a.is_deleted=0 and a.status_active=1 and a.job_no='$data' and a.po_no=b.id";

	echo create_list_view("list_view","Company Name,Job No,Po No,Item,Complexity,SMV,Allocated Qty,Total SMV","150,120,100,160,100,100,80","950","200",0, $sql,"get_php_form_data","id","'load_php_data_to_forms'", 1, "company_id,0,0,item,complexity,0,0,0", $arr , "company_id,job_no,po_number,item,complexity,smv,allocated_qty,total_smv", "requires/order_allocation_controller", 'setFilterGrid("list_view",-1);' );  
}




if($action=="load_php_data_to_forms")
{
	//echo "document.getElementById('txt_sec_name').value = 'okk';\n";
	//$nameArray=sql_select( "select id,job_no,balance,company_id,item,complexity,smv,allocated_qty,total_smv,date_from,date_to  from ppl_order_allocation_mst where id='$data'" );
	
	//$nameArray=sql_select( "select a.id as main_id,a.job_no,a.balance,a.company_id,a.location_name,a.item,a.complexity,a.smv,a.allocated_qty,a.total_smv,a.date_from,a.date_to,b.mst_id,b.qty,b.smv,c.company_name,c.buyer_name,c.team_leader,c.dealing_marchant,c.product_dept,c.product_code,c.season_buyer_wise,c.job_quantity from ppl_order_allocation_mst a,ppl_order_allocation_dtls b,wo_po_details_master c where a.id='$data' and b.mst_id='$data' and a.job_no=c.job_no" );

	//$nameArray=sql_select( "select a.id as main_id,a.job_no,a.balance,a.company_id,a.location_name,a.item,a.complexity,a.smv,a.allocated_qty,a.total_smv,a.date_from,a.date_to,c.company_name,c.buyer_name,c.team_leader,c.dealing_marchant,c.product_dept,c.product_code,c.season_buyer_wise,c.job_quantity from ppl_order_allocation_mst a,wo_po_details_master c where a.id='$data' and a.job_no=c.job_no" );
	
	$nameArray=sql_select( "select a.id as main_id, a.job_no, a.balance, a.company_id, a.location_name,a.customize_smv, a.item, a.complexity, a.smv, a.po_no, a.allocated_qty, a.total_smv, (a.po_quantity*c.total_set_qnty) as po_quantity,a.cut_off_date, a.shipment_date, a.date_from, a.date_to, c.company_name, c.buyer_name, c.team_leader, c.dealing_marchant, c.product_dept, c.product_code, c.season_buyer_wise, c.job_quantity,c.style_ref_no from ppl_order_allocation_mst a,wo_po_details_master c where a.id='$data' and a.job_no=c.job_no" );
	
	//$dtls_update_ids="";
	$nameArray_dtls=sql_select( "select id as dtls_id,mst_id,po_no,date_name,qty,smv,customize_smv from ppl_order_allocation_dtls where mst_id='$data'");
	$update_dtls_row='';
	 $po_no='';
	foreach ($nameArray_dtls as $inf) 
	{
		$update_dtls_row .= $inf[csf("date_name")].'*'.$inf[csf("qty")].'*'.$inf[csf("smv")].'*'.$inf[csf("po_no")].'*'.$inf[csf("customize_smv")].'_';
		$po_no=$inf[csf("po_no")];
	}
	//echo chop($str,"World!");
	//$update_dtls_data=chop($update_dtls_row,"-");
	echo "document.getElementById('hidden_row_pop').value   = '".chop($update_dtls_row,"_")."';\n";
	echo "document.getElementById('txt_popup_no').value   = '".chop($update_dtls_row,"_")."';\n";

	//echo "document.getElementById('cbo_po_no').value 		= '".$inf[csf("po_no")]."';\n";
	
	
	
	foreach ($nameArray as $inf) 
	{	
		echo "document.getElementById('update_id').value   			= '".($inf[csf("main_id")])."';\n";
		//echo "document.getElementById('txt_job_no').value 			= '".($inf[csf("job_no")])."';\n";
		//echo "document.getElementById('cbo_company_name').value 	= '".($inf[csf("company_name")])."';\n";
		//echo "document.getElementById('cbo_buyer_name').value 	  	= '".($inf[csf("buyer_name")])."';\n";
		//echo "document.getElementById('cbo_team_leader').value   	= '".($inf[csf("team_leader")])."';\n";
		//echo "document.getElementById('cbo_dealing_merchant').value = '".($inf[csf("dealing_marchant")])."';\n";
		//echo "document.getElementById('txt_total_job_quantity').value = '".($inf[csf("job_quantity")])."';\n";
		//echo "document.getElementById('cbo_product_department').value = '".($inf[csf("product_dept")])."';\n";
		//	echo "document.getElementById('cbo_season_name').value 	  	= '".($inf[csf("season_buyer_wise")])."';\n";
		
		$tot=return_field_value("sum(allocated_qty)","ppl_order_allocation_mst"," job_no='".$inf[csf("job_no")]."' and status_active=1 and is_deleted=0");
		//echo "document.getElementById('txt_qty_balance').value 		= '".("document.getElementById('txt_total_job_quantity').value"-$tot)."';\n";
		echo "document.getElementById('txt_qty_balance').value= document.getElementById('txt_total_job_quantity').value-$tot;";
		echo "document.getElementById('cbo_company_mst').value 		= '".($inf[csf("company_id")])."';\n";
		
		echo "load_drop_down( 'requires/order_allocation_controller', '".($inf[csf("company_id")])."', 'load_location', 'location_td' );\n";
		echo "document.getElementById('cbo_location_name').value  = '".($inf[csf("location_name")])."';\n"; 

		echo "document.getElementById('cbo_item').value 			= '".($inf[csf("item")])."';\n";
		echo "document.getElementById('cbo_complexity').value 		= '".($inf[csf("complexity")])."';\n";
		//echo "document.getElementById('tot_smv_qnty').value 		= '".($inf[csf("smv")])."';\n";
		 //echo "document.getElementById('cbo_po_no').value 		= '".($inf[csf("po_no")])."';\n";
		echo "document.getElementById('txt_allocated_qty').value 	= '".($inf[csf("allocated_qty")])."';\n";
		echo "document.getElementById('txt_total_smv').value 		= '".($inf[csf("total_smv")])."';\n";
		echo "document.getElementById('tot_c_smv_qnty').value 		= '".($inf[csf("customize_smv")])."';\n";
		echo "document.getElementById('txt_date_from').value 		= '".change_date_format($inf[csf("date_from")])."';\n";
		echo "document.getElementById('txt_date_to').value 			= '".change_date_format($inf[csf("date_to")])."';\n";
		echo "document.getElementById('cbo_po_no').value 			= '".($inf[csf("po_no")])."';\n";
		echo "document.getElementById('txt_style').value 		= '".$inf[csf("style_ref_no")]."';\n"; 
		//echo "document.getElementById('txt_po_qty').value 			= '".($inf[csf("po_quantity")])."';\n";
		$date=$inf[csf("shipment_date")];
		$newDate=date("d-m-Y",strtotime($date));
		//echo "document.getElementById('txt_shipment_date').value 			= '".($inf[csf("shipment_date")])."';\n";
		echo "document.getElementById('txt_shipment_date').value 			= '".$newDate."';\n";
		echo "set_value_smv('".$inf[csf("item")]."','".$inf[csf("job_no")]."');\n";
		$item=$inf[csf("item")];
		$po_no=$inf[csf("po_no")];
		$po_data=$item.'_'.$po_no;
		$cut_off_date=$inf[csf("cut_off_date")];
		echo "load_drop_down( 'requires/order_allocation_controller', '".$po_data."', 'load_drop_down_cut_of_date', 'cut_off_td' );\n";
		echo "document.getElementById('cbo_po_cut_date').value 			= '".$inf[csf("cut_off_date")]."';\n";
		echo "set_cut_po_qty('".$cut_off_date."');\n";
		//load_drop_down( 'requires/order_allocation_controller',this.value+'_'+document.getElementById('cbo_po_no').value, 'load_drop_down_cut_of_date', 'cut_off_td' );
		//echo "load_drop_down( 'requires/order_allocation_controller', '".$inf[csf("job_no")]."', 'load_drop_down_po_no', 'po_td' ) ;\n";
		//echo "show_list_view ('".$inf[csf("job_no")]."', 'order_allocation_list_view', 'section_list_view', 'requires/order_allocation_controller', 'setFilterGrid(\'list_views\',-1)');\n";
		//echo "load_drop_down( 'requires/order_allocation_controller', '".$inf[csf("job_no")]."', 'load_drop_down_po_no', 'po_td' ) ;\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_order_allocation_entry',1);\n";
	}
	//echo "document.getElementById('hidden_row_pop').value   	= '".($dtls_update_ids)."';\n";
	exit();	
}


if($action=="load_php_data_to_forms_unallocated")
{
	$nameArray=sql_select( "select company_name, buyer_name, gmts_item_id, team_leader, dealing_marchant,style_ref_no, job_no, product_dept, product_code, season_matrix, (job_quantity*total_set_qnty) as job_quantity from wo_po_details_master where id='$data' " );

	foreach ($nameArray as $inf) 
	{
		echo "document.getElementById('txt_job_no').value 			= '".($inf[csf("job_no")])."';\n";
		echo "document.getElementById('txt_style').value 			= '".($inf[csf("style_ref_no")])."';\n";
		echo "document.getElementById('cbo_company_name').value 	= '".($inf[csf("company_name")])."';\n";
		echo "document.getElementById('cbo_buyer_name').value 	  	= '".($inf[csf("buyer_name")])."';\n";
		echo "document.getElementById('cbo_team_leader').value   	= '".($inf[csf("team_leader")])."';\n";
		echo "document.getElementById('cbo_dealing_merchant').value = '".($inf[csf("dealing_marchant")])."';\n";
		echo "document.getElementById('txt_total_job_quantity').value = '".($inf[csf("job_quantity")])."';\n";
		echo "document.getElementById('cbo_product_department').value = '".($inf[csf("product_dept")])."';\n";
		echo "document.getElementById('txt_product_code').value= '".($inf[csf("product_code")])."';\n";
		echo "document.getElementById('cbo_season_name').value= '".($inf[csf("season_matrix")])."';\n";
		//echo "document.getElementById('cbo_season_name').value = '".($inf[csf("season")])."';\n";
		echo "document.getElementById('cbo_company_mst').value 		= '".($inf[csf("company_name")])."';\n";
		echo "document.getElementById('cbo_location_name').value 		= '0';\n";
		echo "load_drop_down( 'requires/order_allocation_controller', '".$inf[csf("job_no")]."', 'load_drop_down_po_no', 'po_td' ) ;\n";
		echo "load_drop_down( 'requires/order_allocation_controller', '".$inf[csf("job_no")].'__'.$inf[csf("gmts_item_id")]."', 'load_drop_down_item', 'gmts_item_td' ) ;\n";
		 echo "document.getElementById('tot_smv_qnty').value= '';\n";
		if(count(explode(',',$inf[csf("gmts_item_id")]))==1)
		{
			echo "set_value_smv('".$inf[csf("gmts_item_id")]."','".$inf[csf("job_no")]."');\n";
		}
		//echo "show_list_view ($("#txt_job_no").val(), 'order_allocation_list_view', 'section_list_view', 'requires/order_allocation_controller', 'setFilterGrid(\'list_views\',-1)')"; 
		echo "show_list_view( '".$inf[csf("job_no")]."','order_allocation_list_view','section_list_view','requires/order_allocation_controller','setFilterGrid(\"list_views\",-1)');";
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_order_allocation_entry',1);\n";
	}
	exit();	
}

if($action=="load_allocated_qty")
{
	$ex_data=explode("__",$data);
	if($ex_data[1]!='') $id_cond=" and id!='$ex_data[1]'"; else $id_cond="";
	//echo "select sum(allocated_qty) from ppl_order_allocation_mst where job_no='$ex_data[0]' and id!='ex_data[1]' and status_active=1 and is_deleted=0";
	$tot=return_field_value("sum(allocated_qty)","ppl_order_allocation_mst","job_no='$ex_data[0]' and status_active=1 and is_deleted=0 $id_cond");
	echo $tot;
	exit();
}

 ?>  
   
