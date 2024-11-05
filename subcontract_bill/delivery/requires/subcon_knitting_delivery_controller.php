<?

use Matrix\Operators\Division;

session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//=====DROP DOWN LOCATION=============
if ($action=="load_drop_down_location")
{
	//echo $data;
	echo create_drop_down( "cbo_location", 152, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "",0 );
	exit();
}

if ($action=="load_drop_down_party_name")
{
    echo create_drop_down( "cbo_party_name", 152, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and b.tag_company='$data' and buy.id in (select buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $selected, "",'' );
	exit();
}

if ($action=="load_drop_down_party_name_pop")
{
    echo create_drop_down( "cbo_party_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and b.tag_company='$data' and buy.id in (select buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $selected, "",'' );
	exit();
}

if ($action=="order_number_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1,'');
	$ex_data=explode('_',$data);
	$company_id=$ex_data[0];
	$party_id=$ex_data[1];
	?>
	<script>
		$(document).ready(function(e) {
            $("#txt_search_order").focus();
        });

		function js_set_value(id)
		{
			$("#hidden_order_value").val(id);
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
        <div align="center" style="width:100%;" >
            <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
                <table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                        <tr>
                            <th colspan="5" align="center"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                        </tr>
                    	<tr>
                            <th width="100">Job No</th>
                            <th width="100">Style No</th>
                            <th width="100">Order No</th>
                            <th width="170">Date Range</th>
                            <th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:80px;" /></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="ganeral">
                            <td  align="center">
                                <input type="text" style="width:100px" class="text_boxes"  name="txt_search_job" id="txt_search_job" placeholder="Search Job"/>
                            </td>
                            <td  align="center">
                                <input type="text" style="width:100px" class="text_boxes"  name="txt_search_style" id="txt_search_style" placeholder="Search Style" />
                            </td>
                            <td align="center">
                                <input type="text" style="width:100px" class="text_boxes"  name="txt_search_order" id="txt_search_order" placeholder="Search Order" />
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px"> To
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                            </td>
                            <td align="center">
                                <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_job').value+'_'+document.getElementById('txt_search_style').value+'_'+document.getElementById('txt_search_order').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company_id; ?>+'_'+<? echo $party_id; ?>+'_'+document.getElementById('cbo_string_search_type').value, 'create_order_search_list_view', 'search_div', 'subcon_knitting_delivery_controller', 'setFilterGrid(\'tbl_order_list\',-1)')" style="width:80px;" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" align="center" height="40" valign="middle">
                                <? echo load_month_buttons(1);  ?>
                                <input type="hidden" id="hidden_order_value">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" align="center" valign="top" id=""><div id="search_div"></div></td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="create_order_search_list_view")
{
 	$ex_data = explode("_",$data);
	$search_job = $ex_data[0];
	$search_style = $ex_data[1];
	$search_order = $ex_data[2];
	$date_from = $ex_data[3];
	$date_to = $ex_data[4];
	$company = $ex_data[5];
	$party = $ex_data[6];
	$search_type = $ex_data[7];

	if($search_job=="" && $search_style=="" && $search_order=="" && $date_to=="")
	{

		echo "<b>Please select one search category from search panel.</b>";die;
	}

	if($search_type==1)
	{
		if ($search_job!='') $job_cond=" and b.job_no_prefix_num='$search_job'"; else $job_cond="";
		if ($search_style!='') $style_cond=" and a.cust_style_ref='$search_style'"; else $style_cond="";
		if ($search_order!='') $order_cond=" and a.order_no='$search_order'"; else $order_cond="";
	}
	else if($search_type==4 || $search_type==0)
	{
		if ($search_job!='') $job_cond=" and b.job_no_prefix_num like '%$search_job%'"; else $job_cond="";
		if ($search_style!='') $style_cond=" and a.cust_style_ref like '%$search_style%'"; else $style_cond="";
		if ($search_order!='') $order_cond=" and a.order_no like '%$search_order%'"; else $order_cond="";
	}
	else if($search_type==2)
	{
		if ($search_job!='') $job_cond=" and b.job_no_prefix_num like '$search_job%'"; else $job_cond="";
		if ($search_style!='') $style_cond=" and a.cust_style_ref like '$search_style%'"; else $style_cond="";
		if ($search_order!='') $order_cond=" and a.order_no like '$search_order%'"; else $order_cond="";
	}
	else if($search_type==3)
	{
		if ($search_job!='') $job_cond=" and b.job_no_prefix_num like '%$search_job'"; else $job_cond="";
		if ($search_style!='') $style_cond=" and a.cust_style_ref like '%$search_style'"; else $style_cond="";
		if ($search_order!='') $order_cond=" and a.order_no like '%$search_order'"; else $order_cond="";
	}

	if(	$party!=0) $party_cond=" and b.party_id='$party'"; else $party_cond="";

	if($db_type==0)
	{
		if ($date_from!="" &&  $date_to!="") $date_cond= "and a.delivery_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'"; else $date_cond= "";
		$year_cond= "year(b.insert_date)as year";

	}
	else if ($db_type==2)
	{
		if ($date_from!="" &&  $date_to!="") $date_cond= "and a.delivery_date between '".change_date_format($date_from, "", "",1)."' and '".change_date_format($date_to, "", "",1)."'";  else $date_cond= "";
		$year_cond= "TO_CHAR(b.insert_date,'YYYY') as year";
	}

	$sql = "select a.id, a.order_rcv_date, a.order_no, a.order_uom, a.main_process_id, a.order_quantity, b.party_id, a.cust_style_ref, b.subcon_job, b.job_no_prefix_num, $year_cond from  subcon_ord_dtls a, subcon_ord_mst b where a.main_process_id=2 and a.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0 and b.company_id='$company' $party_cond $job_cond $style_cond $order_cond $date_cond order by b.id DESC";

	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (4=>$production_process,5=>$party_arr);
	echo  create_list_view("tbl_order_list", "Job,Year,Delivery Date,Order No,Process,Party,Order Qty, Style", "60,60,70,100,100,120,100,100","750","250",0, $sql , "js_set_value", "id", "", 1, "0,0,0,0,main_process_id,party_id,0,0", $arr , "job_no_prefix_num,year,order_rcv_date,order_no,main_process_id,party_id,order_quantity,cust_style_ref", "requires/subcon_knitting_delivery_controller",'','0,0,3,0,0,0,2,0') ;
	exit();
}

if($action=="populate_data_from_search_popup")
{
	$balance_ord_qty_arr=array();
	$delivery_qty="select b.order_id, sum(b.delivery_qty) as delivery_qty from subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and a.process_id=2 and a.status_active=1 and a.is_deleted=0 and  b.status_active=1  group by b.order_id";
	$result_del_qty = sql_select($delivery_qty);
	foreach($result_del_qty as $row)
	{
		$balance_ord_qty_arr[$row[csf("order_id")]]['qty']=$row[csf("delivery_qty")];
	}

	$res = sql_select("select a.id, a.delivery_date as order_date, a.main_process_id, a.process_id, a.order_no, a.order_quantity, a.order_uom, a.cust_style_ref, b.company_id, b.party_id, b.subcon_job from  subcon_ord_mst b, subcon_ord_dtls a where a.main_process_id=2 and a.job_no_mst=b.subcon_job and a.id='$data'");
	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

 	foreach($res as $result)
	{
		$bal_order_qty=$result[csf("order_quantity")]-$balance_ord_qty_arr[$result[csf("id")]]['qty'];
		echo "document.getElementById('txt_order_no').value 					= '".$result[csf("order_no")]."';\n";
	    echo "document.getElementById('txt_order_id').value            			= '".$result[csf("id")]."';\n";
		echo "document.getElementById('cbo_process_name').value 				= '".$result[csf("main_process_id")]."';\n";
		echo "document.getElementById('txt_order_date').value 					= '".change_date_format($result[csf("order_date")])."';\n";
		echo "document.getElementById('txt_ordr_qnty').value 					= '".$result[csf("order_quantity")]."';\n";
		echo "document.getElementById('txt_uom').value 							= '".$unit_of_measurement[$result[csf("order_uom")]]."';\n";
		echo "document.getElementById('txt_style').value 						= '".$result[csf("cust_style_ref")]."';\n";
		echo "document.getElementById('txt_cal_order_qnty').value 				= '".$bal_order_qty."';\n";

		if(in_array(3, explode(",", $result[csf("process_id")])))
		{

			echo "$('#txt_collar_cuff_mgt').removeAttr('disabled','disabled');\n";
			//echo "$('#txt_delivery_pcs').removeAttr('disabled','disabled');\n";
		}
		else
		{
			echo "$('#txt_collar_cuff_mgt').attr('disabled','disabled');\n";
			//echo "$('#txt_delivery_pcs').attr('disabled','disabled');\n";
		}

	}
	exit();
}
if($action=="show_fabric_desc_listview")
{
	$data=explode('_',$data);
	$order_id=$data[0];
	$process_id=$data[1];
	$prod_id=$data[2];
	$company_id=$data[3];
	//echo $company_id;
	$item_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');
	$machine_arr=return_library_array( "select id, machine_no from lib_machine_name",'id','machine_no');
	$color_arr=return_library_array( "select id,color_name from lib_color where status_active=1",'id','color_name');
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');

	$roll_maintained = return_field_value("fabric_roll_level", "variable_settings_production", "company_name ='$company_id' and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");
	//echo $roll_maintained.'SSSSSS';

	$delivery_qty_array=array();
	   $delivery_sql="SELECT b.item_id, b.gsm, b.dia, b.width_dia_type, b.color_id, b.yarn_lot,b.collar_cuff, sum(b.delivery_qty) as delivery_qty from subcon_delivery_dtls b,subcon_delivery_mst a where a.id=b.mst_id and b.order_id='$order_id' and b.process_id=2 and a.status_active=1  and b.status_active=1  group by b.item_id, b.gsm, b.dia, b.width_dia_type, b.color_id, b.yarn_lot,b.collar_cuff";
	$delivery_data_sql=sql_select($delivery_sql);
	foreach($delivery_data_sql as $row)
	{
		$delivery_qty_array[$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('collar_cuff')]]=$row[csf('delivery_qty')];
	}
	if($prod_id!='') $prod_cond=" and a.cons_comp_id='$prod_id'"; else $prod_cond="";
	
if($roll_maintained==1) //Fabric in Roll  level
{

	 
	$roll_sql="select a.id as dtls_id,a.machine_id,a.yrn_count_id,a.stitch_len,a.cons_comp_id, a.gsm, a.dia_width, a.dia_width_type, a.color_id,
		a.yarn_lot,b.product_size,(b.qc_pass_qnty) as production_qnty,(b.qc_pass_qnty_pcs) as product_qnty_pcs from subcon_production_dtls a,
		subcon_pro_roll_details b where a.mst_id=b.mst_id and 
    a.order_id=b.po_breakdown_id and a.order_id='$order_id' and a.product_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $prod_cond ";
	$roll_sql_res=sql_select($roll_sql);
	foreach($roll_sql_res as $row)
	{
	 $strData=$row[csf('cons_comp_id')].'_'.$row[csf('gsm')].'_'.$row[csf('dia_width')].'_'.$row[csf('dia_width_type')].'_'.$row[csf('color_id')].'_'.$row[csf('yarn_lot')].'_'.$row[csf('product_size')];
		//$fabSize_array[$strData]['product_size']=$row[csf('product_size')];
		//$fabSize_array[$strData]['product_qnty_pcs']+=$row[csf('product_qnty_pcs')];
		$prod_delArr[$strData]['production_qnty']+=$row[csf('production_qnty')];
		$prod_delArr[$strData]['product_qnty_pcs']+=$row[csf('product_qnty_pcs')];
	} 
}
else
{
	$roll_sql="select a.id as dtls_id,a.machine_id,a.yrn_count_id,a.stitch_len,a.cons_comp_id, a.gsm, a.dia_width, a.dia_width_type, a.color_id,
		a.yarn_lot,(a.product_qnty) as production_qnty  from subcon_production_dtls a  where 
      a.order_id='$order_id' and a.product_type=2 and a.status_active=1 and a.is_deleted=0   $prod_cond ";
	$roll_sql_res=sql_select($roll_sql);
	foreach($roll_sql_res as $row)
	{
	 $strData=$row[csf('cons_comp_id')].'_'.$row[csf('gsm')].'_'.$row[csf('dia_width')].'_'.$row[csf('dia_width_type')].'_'.$row[csf('color_id')].'_'.$row[csf('yarn_lot')].'_'.$row[csf('product_size')];
		//$fabSize_array[$strData]['product_size']=$row[csf('product_size')];
		//$fabSize_array[$strData]['product_qnty_pcs']+=$row[csf('product_qnty_pcs')];
		$prod_delArr[$strData]['production_qnty']+=$row[csf('production_qnty')];
		$prod_delArr[$strData]['product_qnty_pcs']+=$row[csf('product_qnty_pcs')];
	} 
}
 

 //listagg((cast(a.machine_id as varchar2(4000))),',') within group (order by a.machine_id) as machine_id,
		//listagg((cast(a.yrn_count_id as varchar2(4000))),',') within group (order by a.yrn_count_id) as yrn_count_id,
		//listagg((cast(a.stitch_len as varchar2(4000))),',') within group (order by a.stitch_len) as stitch_len,
		
		
/*$sql="select a.cons_comp_id, a.gsm, a.dia_width, a.dia_width_type, a.color_id,
		a.yarn_lot,
		sum(b.qc_pass_qnty) as production_qnty,sum(b.qc_pass_qnty_pcs) as product_qnty_pcs,b.product_size from subcon_production_dtls a,
		subcon_pro_roll_details b where a.mst_id=b.mst_id and a.order_id=b.po_breakdown_id and b.dtls_id=a.id and a.order_id='$order_id'  and a.product_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $prod_cond group by a.cons_comp_id, a.gsm, a.dia_width, a.dia_width_type, a.color_id, a.yarn_lot,b.product_size";
	 // echo $sql;//die;
	$sql_result = sql_select($sql);*/
	?>
	 <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="600">
		<thead>
			<th width="15">SL</th>
			<th width="90">Item Desc.</th>
			<th width="35">GSM</th>
			<th width="35">Dia</th>
            <th width="70">Dia Type</th>
			<th width="60">Color</th>
            <th width="40">Lot</th>
			<th width="50">Prod. Qty</th>
			<th width="50">Pic. Qty</th>
			<th width="50">Size</th>
			<th width="50">Delv. Qty</th>
			<th>Bal. Qty</th>
		</thead>
		<tbody>
			<?
			$i=1;
			foreach($prod_delArr as $fab_key=>$row)
			{
				
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$fab_keyArr=explode("_",$fab_key);
				$cons_comp_id=$fab_keyArr[0];
				$gsm=$fab_keyArr[1];
				$dia_width=$fab_keyArr[2];
				$dia_width_type=$fab_keyArr[3];
				$color_id=$fab_keyArr[4];
				$yarn_lot=$fab_keyArr[5];
				$size=$fab_keyArr[6];
				$availabe_delivery_qty=$row['production_qnty']-$delivery_qty_array[$cons_comp_id][$gsm][$dia_width][$dia_width_type][$color_id][$yarn_lot][$size];
				$delivery_qty=$delivery_qty_array[$cons_comp_id][$gsm][$dia_width][$dia_width_type][$color_id][$yarn_lot][$size];
				

				//if($availabe_delivery_qty!=0)
				//{
			 ?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="get_php_form_data('<? echo $order_id.'_'.$cons_comp_id.'_'.$gsm.'_'.$dia_width.'_'.$dia_width_type.'_'.$color_id.'_'.$yarn_lot.'_'.$size.'_'.$roll_maintained; ?>','load_php_data_for_display','requires/subcon_knitting_delivery_controller');" style="cursor:pointer"> <!--//onClick='set_form_data("<? //echo $row[csf('cons_comp_id')]."**".$row[csf('fabric_description')]."**".$availabe_delivery_qty."**".$row[csf('gsm')]."**".$row[csf('dia_width')]."**".$machine_name."**".$count_name."**".$row[csf('color_id')]."**".$color_arr[$row[csf('color_id')]]."**".$stitch_len_name."**".$lot_name; ?>")' style="cursor:pointer" >-->
					<td><? echo $i; ?></td>
					<td><p><? echo $item_arr[$cons_comp_id]; ?></p></td>
					<td><? echo $gsm; ?></td>
					<td><? echo $dia_width; ?></td>
                    <td><p><? echo $fabric_typee[$dia_width_type]; ?></p></td>
                    <td><p><? echo $color_arr[$color_id]; ?></p></td>
                    <td><p><? echo $yarn_lot; ?></p></td>
					<td align="right"><? echo number_format($row['production_qnty'],2,'.',''); ?></td>
					<td align="right"><? echo number_format($row['product_qnty_pcs'],2,'.',''); ?></td>
					<td align="right"><? echo $size; ?></td>
					<td align="right" title="<? echo $delivery_qty;?>"><? echo number_format($delivery_qty_array[$cons_comp_id][$gsm][trim($dia_width)][$dia_width_type][$color_id][$yarn_lot][$size],2,'.',''); ?></td>
					<td align="right"><? echo number_format($availabe_delivery_qty,2,'.',''); ?></td>
				</tr>
			<?
				$i++;
				//}
			}
			?>
		</tbody>
	</table>
	<?
	exit();
}
if($action=="show_fabric_desc_listview_old")
{
	$data=explode('_',$data);
	$order_id=$data[0];
	$process_id=$data[1];
	$prod_id=$data[2];
	//echo $process_id;
	$item_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');
	$machine_arr=return_library_array( "select id, machine_no from lib_machine_name",'id','machine_no');
	$color_arr=return_library_array( "select id,color_name from lib_color",'id','color_name');
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');

	$delivery_qty_array=array();
	   $delivery_sql="SELECT b.item_id, b.gsm, b.dia, b.width_dia_type, b.color_id, b.yarn_lot,b.collar_cuff, sum(b.delivery_qty) as delivery_qty from subcon_delivery_dtls b,subcon_delivery_mst a where a.id=b.mst_id and b.order_id='$order_id' and b.process_id=2 and a.status_active=1  and b.status_active=1  group by b.item_id, b.gsm, b.dia, b.width_dia_type, b.color_id, b.yarn_lot,b.collar_cuff";
	$delivery_data_sql=sql_select($delivery_sql);
	foreach($delivery_data_sql as $row)
	{
		$delivery_qty_array[$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('collar_cuff')]]=$row[csf('delivery_qty')];
	}


	if($prod_id!='') $prod_cond=" and cons_comp_id='$prod_id'"; else $prod_cond="";
	

	// $sql="SELECT a.cons_comp_id, a.gsm, a.dia_width, a.dia_width_type, a.color_id,	a.yarn_lot, sum(b.qc_pass_qnty) as production_qnty,sum(b.qc_pass_qnty_pcs) as product_qnty_pcs,b.product_size from subcon_production_dtls a, subcon_pro_roll_details b where a.mst_id=b.mst_id and a.order_id='$order_id' and a.product_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $prod_cond group by a.cons_comp_id, a.gsm, a.dia_width, a.dia_width_type, a.color_id, a.yarn_lot,b.product_size";

	/*$sql="SELECT a.cons_comp_id, a.gsm, a.dia_width, a.dia_width_type, a.color_id,
		listagg((cast(a.machine_id as varchar2(4000))),',') within group (order by a.machine_id) as machine_id,
		listagg((cast(a.yrn_count_id as varchar2(4000))),',') within group (order by a.yrn_count_id) as yrn_count_id,
		listagg((cast(a.stitch_len as varchar2(4000))),',') within group (order by a.stitch_len) as stitch_len,
		a.yarn_lot,
		sum(b.qc_pass_qnty) as production_qnty,sum(b.qc_pass_qnty_pcs) as product_qnty_pcs,b.product_size from subcon_production_dtls a,
		subcon_pro_roll_details b where a.mst_id=b.mst_id and a.order_id='$order_id' and a.product_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $prod_cond group by a.cons_comp_id, a.gsm, a.dia_width, a.dia_width_type, a.color_id, a.yarn_lot,b.product_size";*/
		$sql="select a.cons_comp_id, a.gsm, a.dia_width, a.dia_width_type, a.color_id,
		a.yarn_lot,
		sum(b.qc_pass_qnty) as production_qnty,sum(b.qc_pass_qnty_pcs) as product_qnty_pcs,b.product_size from subcon_production_dtls a,
		subcon_pro_roll_details b where a.mst_id=b.mst_id and a.order_id=b.po_breakdown_id and b.dtls_id=a.id and a.order_id='$order_id'  and a.product_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $prod_cond group by a.cons_comp_id, a.gsm, a.dia_width, a.dia_width_type, a.color_id, a.yarn_lot,b.product_size";


	//echo $sql;die;
	$sql_result = sql_select($sql);
	?>
	 <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="600">
		<thead>
			<th width="15">SL</th>
			<th width="90">Item Desc.</th>
			<th width="35">GSM</th>
			<th width="35">Dia</th>
            <th width="70">Dia Type</th>
			<th width="60">Color</th>
            <th width="40">Lot</th>
			<th width="50">Prod. Qty</th>
			<th width="50">Pic. Qty</th>
			<th width="50">Size</th>
			<th width="50">Delv. Qty</th>
			<th>Bal. Qty</th>
		</thead>
		<tbody>
			<?
			$i=1;
			foreach($sql_result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				$availabe_delivery_qty=$row[csf('production_qnty')]-$delivery_qty_array[$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('dia_width_type')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('product_size')]];
				//if($availabe_delivery_qty!=0)
				//{
			 ?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="get_php_form_data('<? echo $order_id.'_'.$row[csf('cons_comp_id')].'_'.$row[csf('gsm')].'_'.$row[csf('dia_width')].'_'.$row[csf('dia_width_type')].'_'.$row[csf('color_id')].'_'.$row[csf('yarn_lot')].'_'.$row[csf('product_size')].'_'.$roll_maintained; ?>','load_php_data_for_display','requires/subcon_knitting_delivery_controller');" style="cursor:pointer"> <!--//onClick='set_form_data("<? //echo $row[csf('cons_comp_id')]."**".$row[csf('fabric_description')]."**".$availabe_delivery_qty."**".$row[csf('gsm')]."**".$row[csf('dia_width')]."**".$machine_name."**".$count_name."**".$row[csf('color_id')]."**".$color_arr[$row[csf('color_id')]]."**".$stitch_len_name."**".$lot_name; ?>")' style="cursor:pointer" >-->
					<td><? echo $i; ?></td>
					<td><p><? echo $item_arr[$row[csf('cons_comp_id')]]; ?></p></td>
					<td><? echo $row[csf('gsm')]; ?></td>
					<td><? echo $row[csf('dia_width')]; ?></td>
                    <td><p><? echo $fabric_typee[$row[csf('dia_width_type')]]; ?></p></td>
                    <td><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                    <td><p><? echo $row[csf('yarn_lot')]; ?></p></td>
					<td align="right"><? echo number_format($row[csf('production_qnty')],2,'.',''); ?></td>
					<td align="right"><? echo number_format($row[csf('product_qnty_pcs')],2,'.',''); ?></td>
					<td align="right"><? echo $row[csf('product_size')];; ?></td>
					<td align="right"><? echo number_format($delivery_qty_array[$row[csf('cons_comp_id')]][$row[csf('gsm')]][trim($row[csf('dia_width')])][$row[csf('dia_width_type')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('product_size')]],2,'.',''); ?></td>
					<td align="right"><? echo number_format($availabe_delivery_qty,2,'.',''); ?></td>
				</tr>
			<?
				$i++;
				//}
			}
			?>
		</tbody>
	</table>
	<?
	exit();
}

if($action=="load_php_data_for_display")
{
	$ex_data=explode('_',$data);
	$order_id=$ex_data[0];
	$prod_id=$ex_data[1];
	$gsm=$ex_data[2];
	$dia=$ex_data[3];
	$dia_type=$ex_data[4];
	$color_id=$ex_data[5];
	$yarn_lot=$ex_data[6]; 
	$collar_cuff=$ex_data[7];
	$roll_maintained=$ex_data[8];

	$item_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');
	$machine_arr=return_library_array( "select id, machine_no from lib_machine_name",'id','machine_no');
	$color_arr=return_library_array( "select id,color_name from lib_color where status_active=1",'id','color_name');
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');

	if($color_id!='') $color_id_cond1=" and b.color_id='$color_id'"; else $color_id_cond1="";
	if($color_id!='') $color_id_cond2=" and a.color_id='$color_id'"; else $color_id_cond2="";
	if($dia_type!='') $dia_type_cond=" and dia_width_type='$dia_type'"; else $dia_type_cond="";
	if($collar_cuff!='') $collar_cuff_cond=" and b.product_size='$collar_cuff'"; else $collar_cuff_cond="";

	$modify_yrn_lot=implode("+", explode(" ", trim($yarn_lot))); // for special character like "+"

	if($yarn_lot!='') $yarn_lot_cond=" and (yarn_lot='$yarn_lot' or yarn_lot='$modify_yrn_lot')"; else $yarn_lot_cond="";

	$delivery_qty_array=array();
	// $delivery_sql="select item_id, gsm, dia, width_dia_type, color_id, yarn_lot, sum(delivery_qty) as delivery_qty from subcon_delivery_dtls where order_id='$order_id' and item_id='$prod_id' and gsm='$gsm' and dia='$dia' and (yarn_lot='$yarn_lot' or yarn_lot='$modify_yrn_lot')  and status_active=1 and process_id=2 $color_id_cond group by item_id, gsm, dia, width_dia_type, color_id, yarn_lot";

	$delivery_sql="SELECT b.item_id, b.gsm, b.dia, b.width_dia_type, b.color_id, b.yarn_lot, sum(b.delivery_qty) as delivery_qty, b.collar_cuff from subcon_delivery_mst a,subcon_delivery_dtls b where a.id=b.mst_id and b.order_id='$order_id' and b.item_id='$prod_id' and b.gsm='$gsm' and b.dia='$dia' and (b.yarn_lot='$yarn_lot' or b.yarn_lot='$modify_yrn_lot')  and b.status_active=1 and b.process_id=2 $color_id_cond1 group by b.item_id, b.gsm, b.dia, b.width_dia_type, b.color_id, b.yarn_lot,b.collar_cuff";

  //echo $delivery_sql;die;
	$delivery_data_sql=sql_select($delivery_sql);
	foreach($delivery_data_sql as $row)
	{
		$delivery_qty_array[$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('collar_cuff')]]=$row[csf('delivery_qty')];
	}


	if($prod_id!='') $prod_cond=" and cons_comp_id='$prod_id'"; else $prod_cond="";
	if($roll_maintained==1) //Fabric in Roll Level- yes
	{
		$roll_sql="select a.machine_id,a.yrn_count_id,a.stitch_len,a.cons_comp_id, a.gsm, a.dia_width, a.dia_width_type, a.color_id,
		a.yarn_lot,b.product_size from subcon_production_dtls a,
		subcon_pro_roll_details b where a.mst_id=b.mst_id and 
    a.order_id=b.po_breakdown_id and a.order_id='$order_id' and a.cons_comp_id='$prod_id' and a.gsm='$gsm' and a.dia_width='$dia'  $dia_type_cond $color_id_cond2 $yarn_lot_cond $collar_cuff_cond and a.product_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $prod_cond ";
	}
	else{
		$roll_sql="select a.machine_id,a.yrn_count_id,a.stitch_len,a.cons_comp_id, a.gsm, a.dia_width, a.dia_width_type, a.color_id,
		a.yarn_lot from subcon_production_dtls a where a.order_id='$order_id' and a.cons_comp_id='$prod_id' and a.gsm='$gsm' and a.dia_width='$dia'  $dia_type_cond $color_id_cond2 $yarn_lot_cond  and a.product_type=2 and a.status_active=1 and a.is_deleted=0  $prod_cond ";
	}
	
	$roll_sql_res=sql_select($roll_sql);
	foreach($roll_sql_res as $row)
	{
		if($row[csf('machine_id')]>0)
		{
		$fab_array[$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('dia_width_type')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('product_size')]]['machine_id'].=$row[csf('machine_id')].',';
		}
		if($row[csf('yrn_count_id')]>0)
		{
		$fab_array[$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('dia_width_type')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('product_size')]]['yrn_count_id'].=$row[csf('yrn_count_id')].',';
		}
		if($row[csf('stitch_len')]>0)
		{
		$fab_array[$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('dia_width_type')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('product_size')]]['stitch_len'].=$row[csf('stitch_len')].',';
		}
	}
	//listagg((cast(a.machine_id as varchar2(4000))),',') within group (order by a.machine_id) as machine_id,
	//listagg((cast(a.yrn_count_id as varchar2(4000))),',') within group (order by a.yrn_count_id) as yrn_count_id,
	//listagg((cast(a.stitch_len as varchar2(4000))),',') within group (order by a.stitch_len) as stitch_len,
	
	if($roll_maintained==1) //Fabric in Roll Level- Yes.
	{
		$sql="select a.cons_comp_id, a.gsm, a.dia_width, a.dia_width_type, a.color_id,
		a.yarn_lot,
		sum(b.qc_pass_qnty) as production_qnty,b.product_size from subcon_production_dtls a,  subcon_pro_roll_details b where a.mst_id=b.mst_id and a.order_id='$order_id' and a.product_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.cons_comp_id='$prod_id' and a.gsm='$gsm' and a.dia_width='$dia'  $dia_type_cond $color_id_cond2 $yarn_lot_cond $collar_cuff_cond group by a.cons_comp_id, a.gsm, a.dia_width, a.dia_width_type, a.color_id, a.yarn_lot,b.product_size";
	}
	else{
		$sql="select a.cons_comp_id, a.gsm, a.dia_width, a.dia_width_type, a.color_id,
		a.yarn_lot,
		sum(a.product_qnty) as production_qnty from subcon_production_dtls a where  a.order_id='$order_id' and a.product_type=2 and a.status_active=1 and a.is_deleted=0  and a.cons_comp_id='$prod_id' and a.gsm='$gsm' and a.dia_width='$dia'  $dia_type_cond $color_id_cond2 $yarn_lot_cond  group by a.cons_comp_id, a.gsm, a.dia_width, a.dia_width_type, a.color_id, a.yarn_lot";	
	}

	//echo $sql;die;
	$sql_result = sql_select($sql);
	foreach ($sql_result as $row)
	{
		$machineid=rtrim($fab_array[$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('dia_width_type')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('product_size')]]['machine_id'],',');
		$yrn_count_id=rtrim($fab_array[$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('dia_width_type')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('product_size')]]['yrn_count_id'],',');
		$stitch_len=rtrim($fab_array[$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('dia_width_type')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('product_size')]]['stitch_len'],',');
		
		$machine_id=array_unique(explode(",",$machineid));
		$machine_name="";
		foreach($machine_id as $val)
		{
			if($machine_name=="") $machine_name=$machine_arr[$val]; else $machine_name.=", ".$machine_arr[$val];
		}

		$yarn_count=array_unique(explode(",",$yrn_count_id));
		$count_name="";
		foreach($yarn_count as $val)
		{
			if($count_name=="") $count_name=$count_arr[$val]; else $count_name.=", ".$count_arr[$val];
		}

		$stitch_len_id=array_unique(explode(",",$stitch_len));
		$stitch_len_name="";
		foreach($stitch_len_id as $val)
		{
			if($stitch_len_name=="") $stitch_len_name=$val; else $stitch_len_name.=", ".$val;
		}

		$availabe_delivery_qty=$row[csf('production_qnty')]-$delivery_qty_array[$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('dia_width_type')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('product_size')]];
		$availabe_delivery_qty=number_format($availabe_delivery_qty,0,'.','');
		echo "document.getElementById('txt_item_id').value		 			= '".$row[csf("cons_comp_id")]."';\n";
		echo "document.getElementById('txt_dalivery_item').value		 	= '".$item_arr[$row[csf("cons_comp_id")]]."';\n";
		echo "document.getElementById('txt_production_qnty').value		 	= '".$availabe_delivery_qty."';\n";
		echo "document.getElementById('txt_color_id').value		 			= '".$row[csf("color_id")]."';\n";
		echo "document.getElementById('txt_color').value		 			= '".$color_arr[$row[csf("color_id")]]."';\n";
		echo "document.getElementById('txt_gsm').value		 				= '".$row[csf("gsm")]."';\n";
		echo "document.getElementById('txt_dia').value		 				= '".$row[csf("dia_width")]."';\n";
		echo "document.getElementById('txt_dia_type').value		 			= '".$row[csf("dia_width_type")]."';\n";
		echo "document.getElementById('txt_matchine_neme').value		 	= '".$machine_name."';\n";
		echo "document.getElementById('txt_count').value		 			= '".$count_name."';\n";
		echo "document.getElementById('txt_stitch_length').value		 	= '".$stitch_len_name."';\n";
		echo "document.getElementById('txt_lot').value		 				= '".$row[csf('yarn_lot')]."';\n";
		echo "document.getElementById('txt_collar_cuff_mgt').value		 	= '".$row[csf('product_size')]."';\n";
	}
	exit();
}

if($action=="delivery_entry_list_view")
{
	?>
	<div style="width:810px;">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table" id="table_body">
            <thead>
                <th width="30">SL</th>
                <th width="90">Order</th>
                <th width="80">Challan No</th>
                <th width="160">Delivery Item</th>
                <th width="75">Delivery Date</th>
                <th width="80">Delivery Qty</th>
                <th width="70">Carton /Roll</th>
                <th width="110">Dia type</th>
                <th width="">Bill Status</th>
            </thead>
            <tbody>
		<?
			$i=1;
			$party_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
			$lib_item_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');
			//$bill_row_status=array(0=>"Active",1=>"In Active");
			$bill_row_status = array(0 => "Bill Pending", 1 => "Bill Complete");
			//echo "select a.id, a.delivery_date, a.challan_no, a.transport_company, a.forwarder, b.id as dtls_id, b.item_id, b.delivery_qty, b.carton_roll,  b.bill_status, b.order_id, b.process_id from subcon_delivery_mst a, subcon_delivery_dtls b where a.process_id=2 and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and a.id='$data'";
			$sql = sql_select("select a.id, a.delivery_date, a.challan_no, a.transport_company, a.forwarder, b.id as dtls_id, b.item_id, b.width_dia_type, b.delivery_qty, b.carton_roll,  b.bill_status, b.order_id, b.process_id,c.order_no from subcon_delivery_mst a, subcon_delivery_dtls b,subcon_ord_dtls c where a.process_id=2 and a.id=b.mst_id and c.id=b.order_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id='$data'"); 
			// $sql = sql_select("select a.id, a.delivery_date, a.challan_no, a.transport_company, a.forwarder, b.id as dtls_id, b.item_id, b.width_dia_type, b.delivery_qty, b.carton_roll,  b.bill_status, b.order_id, b.process_id from subcon_delivery_mst a, subcon_delivery_dtls b where a.process_id=2 and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and a.id='$data'");
			foreach($sql as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data('<? echo $row[csf('dtls_id')].'_'.$row[csf('process_id')]; ?>','load_php_data_to_form_delivery','requires/subcon_knitting_delivery_controller');" >
                    <td width="30" align="center"><? echo $i; ?></td>
                    <?
						$item_name=$lib_item_arr[$row[csf('item_id')]];
                    ?>
                    <td width="90" align="center"><p><? echo $row[csf('order_no')]; ?></p></td>
                     <td width="80" align="center"><p><? echo $row[csf('challan_no')]; ?></p></td>
                    <td width="160" align="center"><p><? echo $item_name; ?></p></td>
                    <td width="75" align="center"><? echo change_date_format($row[csf('delivery_date')]); ?></td><!--change_date_format()-->
                    <td width="80" align="right"><? echo $row[csf('delivery_qty')]; ?>&nbsp;</td>
                    <td width="70" align="center"><? echo $row[csf('carton_roll')]; ?></td>
                    <td width="115" align="center"><p><? echo $fabric_typee[$row[csf('width_dia_type')]]; ?></p></td>
                    <td width="" align="center"><p><? echo $bill_row_status[$row[csf('bill_status')]]; ?></p></td>
                </tr>
                <?
                $i++;
			}
			?>
            </tbody>
		</table>
       </div>
	<?
	exit();
}

if($action=="load_php_data_to_form_delivery")
{
	$ex_data=explode('_',$data);
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1", "id", "color_name");
	$lib_item_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');

 

	$order_array=array();
	// $sql_order=sql_select("select id, order_no, main_process_id,process_id, order_rcv_date, order_quantity, order_uom, cust_style_ref from subcon_ord_dtls where main_process_id=2 and  status_active=1 and is_deleted=0 ");
	$sql_order=sql_select("select c.id, c.order_no, c.main_process_id,c.process_id, c.order_rcv_date, c.order_quantity, c.order_uom, c.cust_style_ref from subcon_ord_dtls c,subcon_delivery_dtls b where c.id=b.order_id and c.main_process_id=2 and b.id='$ex_data[0]' and  c.status_active=1 and c.is_deleted=0 ");

	foreach ($sql_order as $row)
	{
		$order_array[$row[csf("id")]]['order_no']=$row[csf("order_no")];
		$order_array[$row[csf("id")]]['main_process_id']=$row[csf("main_process_id")];
		$order_array[$row[csf("id")]]['order_rcv_date']=$row[csf("order_rcv_date")];
		$order_array[$row[csf("id")]]['order_quantity']=$row[csf("order_quantity")];
		$order_array[$row[csf("id")]]['order_uom']=$row[csf("order_uom")];
		
		$order_array[$row[csf("id")]]['cust_style_ref']=$row[csf("cust_style_ref")];
		$sub_process_id_arr[$row[csf("id")]]=$row[csf("process_id")];
		$order_id_arr[$row[csf("id")]]=$row[csf("id")];
	}


	$balance_ord_qty_arr=array();
	$delivery_qty="select a.company_id,b.order_id, sum(b.delivery_qty) as delivery_qty from subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and a.process_id=2 and a.status_active=1 and a.is_deleted=0 and b.order_id in(".implode(",",$order_id_arr).") group by b.order_id,a.company_id";
	$result_del_qty = sql_select($delivery_qty);
	foreach($result_del_qty as $row)
	{
		$balance_ord_qty_arr[$row[csf("order_id")]]['qty']=$row[csf("delivery_qty")];
		$company_id=$row[csf("company_id")];
	}
	$roll_maintained = return_field_value("fabric_roll_level", "variable_settings_production", "company_name ='$company_id' and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");
	//echo $roll_maintained.'DDD';

	if($db_type==0)
	{
		$bill_info=return_field_value("concat(b.delivery_id,'**',a.bill_no) as delivery_info", "subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b", "a.id=b.mst_id and b.process_id=2 and a.party_source=2 and b.delivery_id='$ex_data[0]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","delivery_info");
	}
	elseif($db_type==2)
	{
		$bill_info=return_field_value("b.delivery_id || '**' || a.bill_no as delivery_info", "subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b", "a.id=b.mst_id and b.process_id=2 and a.party_source=2 and b.delivery_id='$ex_data[0]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","delivery_info");
	}
	$nameArray =sql_select("select id, order_id, process_id, sub_process_id, item_id, gsm, dia, color_id, yarn_lot,width_dia_type, delivery_qty, delivery_pcs, carton_roll,remarks, reject_qty, moisture_gain, gray_qty,collar_cuff from  subcon_delivery_dtls a where id='$ex_data[0]' and process_id=2");


	foreach ($nameArray as $row)
	{

		$item_name=$lib_item_arr[$row[csf('item_id')]];
		$bal_order_qty=$order_array[$row[csf("order_id")]]['order_quantity']-$balance_ord_qty_arr[$row[csf("order_id")]]['qty'];

		echo "document.getElementById('txt_order_id').value		 					= '".$row[csf("order_id")]."';\n";
		echo "document.getElementById('txt_order_no').value		 					= '".$order_array[$row[csf("order_id")]]['order_no']."';\n";
		echo "document.getElementById('cbo_process_name').value		 				= '".$order_array[$row[csf("order_id")]]['main_process_id']."';\n";
		echo "document.getElementById('txt_order_date').value		 				= '".change_date_format($order_array[$row[csf("order_id")]]['order_rcv_date'])."';\n";
		echo "document.getElementById('txt_ordr_qnty').value		 				= '".$order_array[$row[csf("order_id")]]['order_quantity']."';\n";
		echo "document.getElementById('txt_uom').value		 						= '".$unit_of_measurement[$order_array[$row[csf("order_id")]]['order_uom']]."';\n";
		echo "document.getElementById('txt_style').value		 					= '".$order_array[$row[csf("order_id")]]['cust_style_ref']."';\n";
		echo "document.getElementById('txt_dalivery_item').value		 			= '".$item_name."';\n";
		echo "document.getElementById('txt_item_id').value		 					= '".$row[csf("item_id")]."';\n";
		echo "document.getElementById('txt_delivery_qnty').value		 			= '".$row[csf("delivery_qty")]."';\n";
		echo "document.getElementById('txt_pre_delivery_qnty').value		 		= '".$row[csf("delivery_qty")]."';\n";
		echo "document.getElementById('txt_gsm').value		 						= '".$row[csf("gsm")]."';\n";
		echo "document.getElementById('txt_dia').value		 						= '".$row[csf("dia")]."';\n";
		//echo "document.getElementById('txt_batch_no').value		 					= '".$row[csf("batch_no")]."';\n";
		echo "document.getElementById('txt_cal_order_qnty').value 					= '".$bal_order_qty."';\n";
		echo "document.getElementById('txt_color').value		 					= '".$color_arr[$row[csf("color_id")]]."';\n";
		echo "document.getElementById('txt_color_id').value		 					= '".$row[csf("color_id")]."';\n";
		echo "document.getElementById('txt_lot').value		 						= '".$row[csf("yarn_lot")]."';\n";
		echo "document.getElementById('txt_dia_type').value		 					= '".$row[csf("width_dia_type")]."';\n";
		echo "document.getElementById('txt_grey_used').value		 				= '".$row[csf("gray_qty")]."';\n";
		echo "document.getElementById('txt_moisture_gain').value		 			= '".$row[csf("moisture_gain")]."';\n";
		$bill_delivery=explode("**",$bill_info);
		echo "document.getElementById('bill_info').value		 					= '".$bill_info."';\n";
		echo "active_inactive(document.getElementById('bill_info').value);\n";


		echo "document.getElementById('txt_reject_qty').value					= '".$row[csf("reject_qty")]."';\n";
		echo "document.getElementById('txt_carton_roll_no').value					= '".$row[csf("carton_roll")]."';\n";
		echo "document.getElementById('txt_collar_cuff_mgt').value		 			= '".$row[csf("collar_cuff")]."';\n";
		echo "show_list_view(document.getElementById('txt_order_id').value+'_'+document.getElementById('cbo_process_name').value+'_'+document.getElementById('txt_item_id').value+'_'+document.getElementById('cbo_company_name').value, 'show_fabric_desc_listview','list_fabric_desc_container','requires/subcon_knitting_delivery_controller','');\n";
		echo "get_php_form_data(document.getElementById('txt_order_id').value+'_'+document.getElementById('txt_item_id').value+'_'+document.getElementById('txt_gsm').value+'_'+document.getElementById('txt_dia').value+'_'+document.getElementById('txt_dia_type').value+'_'+document.getElementById('txt_color_id').value+'_'+document.getElementById('txt_lot').value+'_'+document.getElementById('txt_collar_cuff_mgt').value+'_'+'".$roll_maintained."','load_php_data_for_display','requires/subcon_knitting_delivery_controller');\n";



		echo "document.getElementById('txt_delivery_pcs').value		 			= '".$row[csf("delivery_pcs")]."';\n";

		echo "document.getElementById('txt_remarks').value		 					= '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('update_id_dtls').value		 				= '".$row[csf("id")]."';\n";

		// if(in_array(3, explode(",", $sub_process_id_arr[$row[csf("order_id")]])))
		// {
		// 	echo "$('#txt_collar_cuff_mgt').removeAttr('disabled','disabled');\n";
		// 	//echo "$('#txt_delivery_pcs').removeAttr('disabled','disabled');\n";
		// }
		// else
		// {
		// 	echo "$('#txt_collar_cuff_mgt').attr('disabled','disabled');\n";
		// 	//echo "$('#txt_delivery_pcs').attr('disabled','disabled');\n";
		// }



		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_subcon_delivery_entry',1,1);\n";
	}
	exit();
}

if($action=="delivery_qty_check")
{
	$data=explode("**",$data);
	$sql="select a.id, sum(b.product_qnty) as product_qnty from subcon_production_mst a, subcon_production_dtls b where a.entry_form=292 and a.id=b.mst_id and  b.order_id='$data[0]' and a.product_type='$data[1]' and b.cons_comp_id='$data[2]' and a.status_active=1 and a.is_deleted=0 group by b.order_id, b.process";
	$delivery_sql="select sum(delivery_qnty) as delivery_qnty from  subcon_delivery where order_id='$data[0]' and process_id='$data[1]' and item_id='$data[2]' and status_active=1 and is_deleted=0 group by order_id, process_id, item_id";
	$data_array=sql_select($sql);
	$delivery_array=sql_select($delivery_sql);

	echo $data_array[0][csf('product_qnty')].'_'.$delivery_array[0][csf('delivery_qnty')];
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0)  // Insert Start Here========================================================================================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		if(str_replace("'",'',$cbo_party_name)!=0)
		{
			$outstanding_validation=return_field_value("control_delivery","lib_buyer","id=$cbo_party_name"); // 1 = yes; 2=no;
			if($outstanding_validation==1)
			{
				$order_arr=array();
				$order_sql="select b.id, c.item_id, sum(c.qnty) as qnty, sum(c.rate) as rate, sum(c.process_loss) as process_loss from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where a.subcon_job=b.job_no_mst and b.id=c.order_id and a.company_id=$cbo_company_name and a.party_id=$cbo_party_name and b.main_process_id=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.id, c.item_id";
				$order_sql_result=sql_select($order_sql);
				foreach($order_sql_result as $row)
				{
					$order_arr[$row[csf("id")]]['qty']+=$row[csf("qnty")];
					$order_arr[$row[csf("id")]]['rate']+=$row[csf("rate")];
					$order_arr[$row[csf("id")]]['pLoss']+=$row[csf("process_loss")];
				}
				$bill_issue_amt=0; $payment_rec_amt=0; $delivery_not_bill_amt=0; $delivery_bill_amt=0; $outstanding_knitting_bill_amt=0;
				if(str_replace("'",'',$txt_delivery_qnty)=='') $txt_delivery_qnty=0; else $txt_delivery_qnty=str_replace("'",'',$txt_delivery_qnty);

				$bill_issue_amt=return_field_value("sum(b.amount) as total_bill_amt","subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b","a.id=b.mst_id and a.company_id=$cbo_company_name and a.party_id=$cbo_party_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.process_id=2","total_bill_amt");

				$payment_rec_amt=return_field_value("sum(b.total_adjusted) as receive_amt","subcon_payment_receive_mst a, subcon_payment_receive_dtls b","a.id=b.master_id and a.company_id=$cbo_company_name and a.party_name=$cbo_party_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.bill_type=2","receive_amt");

				$mat_rec_sql=sql_select("select sum(b.quantity) as quantity, sum(b.quantity*b.rate) as tot_amount from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_name and a.party_id=$cbo_party_name and a.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=2 and b.is_deleted=0 and b.item_category_id=1");
				$receive_amount=$mat_rec_sql[0][csf('tot_amount')];
				$receive_qty=$mat_rec_sql[0][csf('quantity')];
				$rec_rate=$receive_amount/$receive_qty;

				$mat_ret_sql="select sum(b.quantity) as quantity from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_name and a.party_id=$cbo_party_name and a.trans_type=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category_id=1";
				$mat_ret_sql_result= sql_select($mat_ret_sql);
				$receive_ret_amount=$mat_ret_sql_result[0][csf('quantity')]*$rec_rate;

				$previous_del_qty_sql="select b.order_id, sum(CASE WHEN b.bill_status=0 THEN b.delivery_qty ELSE 0 END) AS nbill_delivery_qty, sum(CASE WHEN b.bill_status=1 THEN b.delivery_qty ELSE 0 END) AS bill_delivery_qty
				 from subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_name and a.party_id=$cbo_party_name  and a.status_active=1 and a.is_deleted=0 and b.process_id=2 group by b.order_id";//and b.order_id=$txt_order_id
				$previous_del_qty_sql_result= sql_select($previous_del_qty_sql);
				foreach($previous_del_qty_sql_result as $row)
				{
					$nbill_delivery_amt=0;
					$nbill_delivery_amt=$row[csf("nbill_delivery_qty")]*$order_arr[$row[csf("order_id")]]['rate'];
					$delivery_not_bill_amt+=$nbill_delivery_amt;
					$delivery_bill_amt=($row[csf("bill_delivery_qty")]+$row[csf("nbill_delivery_qty")])*$rec_rate;
				}
				$curr_delivery_amt=$txt_delivery_qnty*$order_arr[str_replace("'",'',$txt_order_id)]['rate'];
				$outstanding_knitting_bill_amt=($delivery_not_bill_amt+$curr_delivery_amt)+($bill_issue_amt-$payment_rec_amt);
				$inhand_stock=0;
				$inhand_stock=$receive_amount-($delivery_bill_amt+$receive_ret_amount);
				$control_delivery_ar=0;
				$control_delivery_ar=$inhand_stock-$outstanding_knitting_bill_amt;

				//echo "10**".$control_delivery_ar.'='.$inhand_stock.'='.$outstanding_knitting_bill_amt;
				if($control_delivery_ar<0)
				{
					echo "11**"."Delivery Control from A/R."."**".$delivery_not_bill_amt;
					disconnect($con);
					die;
				}
			}
		}
		//die;
		if($db_type==0) $year_cond=" and YEAR(insert_date)";
		else if($db_type==2) $year_cond=" and TO_CHAR(insert_date,'YYYY')";

		$return_delivery_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'KNT', date("Y",time()), 5, "select id, delivery_prefix, delivery_prefix_num from subcon_delivery_mst where company_id=$cbo_company_name and process_id=2 $year_cond=".date('Y',time())." order by id desc ", "delivery_prefix", "delivery_prefix_num" ));

		//echo $update_id;die;
		if(str_replace("'",'',$update_id)==0 || str_replace("'",'',$update_id)=='')
		{
			$id=return_next_id( "id"," subcon_delivery_mst", 1 ) ;
			$field_array="id, delivery_prefix, delivery_prefix_num, delivery_no, company_id, process_id, location_id, party_id, challan_no, delivery_date, forwarder, transport_company, vehical_no, inserted_by, insert_date, status_active, is_deleted";

			$challan=str_replace("'",'',$txt_challan_no);
			//echo $challan;die;
			if ($challan!='' && $challan!=0) $challan_no=$txt_challan_no; else $challan_no=$return_delivery_no[2];

			$data_array="(".$id.",'".$return_delivery_no[1]."','".$return_delivery_no[2]."','".$return_delivery_no[0]."',".$cbo_company_name.",2,".$cbo_location.",".$cbo_party_name.",".$challan_no.",".$txt_delivery_date.",".$cbo_forwarder.",".$txt_transport_company.",".$txt_vehical_no.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			//echo "insert into subcon_delivery_mst (".$field_array.") values ".$data_array;//die;
			$rID=sql_insert("subcon_delivery_mst",$field_array,$data_array,0);
			$return_no=$return_delivery_no[0];
		}
		else
		{
			$challan=str_replace("'",'',$txt_challan_no);
			//echo $challan;die;
			if ($challan!='' && $challan!=0) $challan_no=$txt_challan_no; else $challan_no=$update_id;

			$field_array="location_id*party_id*challan_no*delivery_date*forwarder*transport_company*vehical_no*updated_by*update_date";
			$data_array="".$cbo_location."*".$cbo_party_name."*".$txt_challan_no."*".$txt_delivery_date."*".$cbo_forwarder."*".$txt_transport_company."*".$txt_vehical_no."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

			//echo "0***"."insert into subcon_delivery_mst (".$field_array.") values ".$data_array;die;
			$id=$update_id;
			$rID=sql_update("subcon_delivery_mst",$field_array,$data_array,"id",$update_id,0);
			$return_no=$txt_sys_id;
		}

		$dtlsid=return_next_id( "id","subcon_delivery_dtls", 1 ) ;
		$field_array_dtls="id, mst_id, order_id, process_id, item_id, gsm, dia, width_dia_type, color_id, yarn_lot, delivery_qty, carton_roll, collar_cuff, remarks, delivery_pcs, reject_qty, moisture_gain, gray_qty";

		$data_array_dtls="(".$dtlsid.",".$id.",".$txt_order_id.",2,".$txt_item_id.",".$txt_gsm.",".$txt_dia.",".$txt_dia_type.",".$txt_color_id.",".$txt_lot.",".$txt_delivery_qnty.",".$txt_carton_roll_no.",".$txt_collar_cuff_mgt.",".$txt_remarks.",".$txt_delivery_pcs.",".$txt_reject_qty.",".$txt_moisture_gain.",".$txt_grey_used.")";
		//echo "insert into subcon_delivery_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		$rID1=sql_insert("subcon_delivery_dtls",$field_array_dtls,$data_array_dtls,1);

		if($db_type==0)
		{
			if($rID && $rID1)
			{
				mysql_query("COMMIT");
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".str_replace("'",'',$challan_no);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'",'',$id);
			}
		}
		else if($db_type==2)
		{
			if($rID && $rID1)
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".str_replace("'",'',$challan_no);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$id);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here==============================================================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$gate_pass = sql_select("SELECT id,SYS_NUMBER from inv_gate_pass_mst where issue_id=$update_id and company_id=$cbo_company_name and basis=8 and status_active=1 and is_deleted=0");
		if(count($gate_pass)>0)
		{
			echo "11**Update Not Allow. This System ID Found in Gate Pass Entry ".$gate_pass[0]["SYS_NUMBER"];disconnect($con); die;
		}

		if(str_replace("'",'',$txt_delivery_qnty)=='') $txt_delivery_qnty=0; else $txt_delivery_qnty=str_replace("'",'',$txt_delivery_qnty);
		if(str_replace("'",'',$cbo_party_name)!=0)
		{
			$outstanding_validation=return_field_value("control_delivery","lib_buyer","id=$cbo_party_name"); // 1 = yes; 2=no;
			if($outstanding_validation==1)
			{
				$order_arr=array();
				$order_sql="select b.id, c.item_id, sum(c.qnty) as qnty, sum(c.rate) as rate, sum(c.process_loss) as process_loss from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where a.subcon_job=b.job_no_mst and b.id=c.order_id and a.company_id=$cbo_company_name and a.party_id=$cbo_party_name and b.main_process_id=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.id, c.item_id";
				$order_sql_result=sql_select($order_sql);
				foreach($order_sql_result as $row)
				{
					$order_arr[$row[csf("id")]]['qty']+=$row[csf("qnty")];
					$order_arr[$row[csf("id")]]['rate']+=$row[csf("rate")];
					$order_arr[$row[csf("id")]]['pLoss']+=$row[csf("process_loss")];
				}
				$bill_issue_amt=0; $payment_rec_amt=0; $delivery_not_bill_amt=0; $delivery_bill_amt=0; $outstanding_knitting_bill_amt=0;

				$bill_issue_amt=return_field_value("sum(b.amount) as total_bill_amt","subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b","a.id=b.mst_id and a.company_id=$cbo_company_name and a.party_id=$cbo_party_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.process_id=2","total_bill_amt");

				$payment_rec_amt=return_field_value("sum(b.total_adjusted) as receive_amt","subcon_payment_receive_mst a, subcon_payment_receive_dtls b","a.id=b.master_id and a.company_id=$cbo_company_name and a.party_name=$cbo_party_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.bill_type=2","receive_amt");

				$mat_rec_sql=sql_select("select sum(b.quantity) as quantity, sum(b.quantity*b.rate) as tot_amount from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_name and a.party_id=$cbo_party_name and a.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=2 and b.is_deleted=0 and b.item_category_id=1");
				$receive_amount=$mat_rec_sql[0][csf('tot_amount')];
				$receive_qty=$mat_rec_sql[0][csf('quantity')];
				$rec_rate=$receive_amount/$receive_qty;

				$mat_ret_sql="select sum(b.quantity) as quantity from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_name and a.party_id=$cbo_party_name and a.trans_type=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category_id=1";
				$mat_ret_sql_result= sql_select($mat_ret_sql);
				$receive_ret_amount=$mat_ret_sql_result[0][csf('quantity')]*$rec_rate;

				$previous_del_qty_sql="select b.order_id, sum(CASE WHEN b.bill_status=0 THEN b.delivery_qty ELSE 0 END) AS nbill_delivery_qty, sum(CASE WHEN b.bill_status=1 THEN b.delivery_qty ELSE 0 END) AS bill_delivery_qty
				 from subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_name and a.party_id=$cbo_party_name and a.status_active=1 and a.is_deleted=0 and b.process_id=2 group by b.order_id";
				$previous_del_qty_sql_result= sql_select($previous_del_qty_sql);
				foreach($previous_del_qty_sql_result as $row)
				{
					$nbill_delivery_amt=0;
					$nbill_delivery_amt=$row[csf("nbill_delivery_qty")]*$order_arr[$row[csf("order_id")]]['rate'];
					$delivery_not_bill_amt+=$nbill_delivery_amt;
					$delivery_bill_amt=($row[csf("bill_delivery_qty")]+$row[csf("nbill_delivery_qty")])*$rec_rate;
				}
				$curr_delivery_amt=$txt_delivery_qnty*$order_arr[str_replace("'",'',$txt_order_id)]['rate'];
				$outstanding_knitting_bill_amt=($delivery_not_bill_amt+$curr_delivery_amt)+($bill_issue_amt-$payment_rec_amt);
				$inhand_stock=0;
				$inhand_stock=$receive_amount-($delivery_bill_amt+$receive_ret_amount);
				$control_delivery_ar=0;
				$control_delivery_ar=$inhand_stock-$outstanding_knitting_bill_amt;
				if($control_delivery_ar<0)
				{
					echo "11**"."Delivery Control fro A/R."."**".$delivery_not_bill_amt;
					disconnect($con);
					die;
				}
			}
		}

		$challan=str_replace("'",'',$txt_challan_no);
		//echo $challan;die;
		if ($challan!='' && $challan!=0) $challan_no=$txt_challan_no; else $challan_no=$update_id;

		$field_array="location_id*party_id*challan_no*delivery_date*forwarder*transport_company*vehical_no*updated_by*update_date";
		$data_array="".$cbo_location."*".$cbo_party_name."*".$txt_challan_no."*".$txt_delivery_date."*".$cbo_forwarder."*".$txt_transport_company."*".$txt_vehical_no."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		//echo "insert into subcon_delivery_mst (".$field_array.") values ".$data_array;die;txt_gsm*txt_dia
		$rID=sql_update("subcon_delivery_mst",$field_array,$data_array,"id",$update_id,0);
		$return_no=$txt_sys_id;
		$field_array_dtls="order_id*item_id*gsm*dia*width_dia_type*color_id*yarn_lot*delivery_qty*carton_roll*collar_cuff*remarks*delivery_pcs*reject_qty*moisture_gain*gray_qty";

		$data_array_dtls="".$txt_order_id."*".$txt_item_id."*".$txt_gsm."*".$txt_dia."*".$txt_dia_type."*".$txt_color_id."*".$txt_lot."*".$txt_delivery_qnty."*".$txt_carton_roll_no."*".$txt_collar_cuff_mgt."*".$txt_remarks."*".$txt_delivery_pcs."*".$txt_reject_qty."*".$txt_moisture_gain."*".$txt_grey_used."";

		$rID2=sql_update("subcon_delivery_dtls",$field_array_dtls,$data_array_dtls,"id",$update_id_dtls,1);// die;

		if($db_type==0)
		{
			if($rID && $rID2)
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'",'',$update_id)."**".str_replace("'",'',$return_no)."**".str_replace("'",'',$challan_no);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'",'',$update_id)."**".str_replace("'",'',$return_no)."**".str_replace("'",'',$challan_no);
			}
		}
		else if($db_type==2)
		{
			if($rID && $rID2)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$update_id)."**".str_replace("'",'',$return_no)."**".str_replace("'",'',$challan_no);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$update_id)."**".str_replace("'",'',$return_no)."**".str_replace("'",'',$challan_no);
			}
		}

		disconnect($con);
 		die;
	}
	else if ($operation==2)   // Delete Here =====================================================================================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$challan=str_replace("'",'',$txt_challan_no);
		//echo $challan;die;
		if ($challan!='' && $challan!=0)
		{
			$challan_no=$txt_challan_no;
		}
		else
		{
			$challan_no=$update_id;
		}
		$bill_info=return_field_value("a.delivery_id", "subcon_inbound_bill_dtls a, subcon_inbound_bill_mst b", "a.mst_id=b.id and b.party_source=2 and b.process_id=2 and a.delivery_id=$update_id_dtls","delivery_id");
		if($bill_info!=0 || $bill_info!="")
		{
			echo "13**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_sys_id)."**".str_replace("'",'',$challan_no);
			disconnect($con);
			die;
		}

		$gate_pass = sql_select("SELECT id,SYS_NUMBER from inv_gate_pass_mst where issue_id=$update_id and company_id=$cbo_company_name and basis=8 and status_active=1 and is_deleted=0");
		if(count($gate_pass)>0)
		{
			echo "11**Delete Not Allow. This System ID Found in Gate Pass Entry ".$gate_pass[0]["SYS_NUMBER"];disconnect($con); die;
		}

		$delivery_mst_id=str_replace("'","",$update_id);
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="'".$user_id."'*'".$pc_date_time."'*0*1";
		$sql_dtls=sql_select("select id from subcon_delivery_dtls where status_active=1 and is_deleted=0 and mst_id='$delivery_mst_id'");
		$rID=1; $flag=1;
		if(count($sql_dtls)==1)
		{
			$rID=sql_delete("subcon_delivery_mst",$field_array,$data_array,"id","".$delivery_mst_id."",1);
			if($rID==1 && $flag==1) $flag=1; else $flag=0;
		}
		if($flag==1)
		{
			$rID1=sql_delete("subcon_delivery_dtls",$field_array,$data_array,"id","".$update_id_dtls."",1);
		}
		//echo "10**".$rID.'='.$rID1; die;
		//echo $bill_info;die;
		//$rID=execute_query( "delete from subcon_delivery_dtls where id=$update_id_dtls",0);
/*		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$rID=sql_delete("subcon_delivery_mst",$field_array,$data_array,"id","".$update_id."",1);
*/		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_sys_id)."**".str_replace("'",'',$challan_no);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_sys_id)."**".str_replace("'",'',$challan_no);
			}
		}
		else if($db_type==2)
		{
			if($rID)
			{
				oci_commit($con);
				echo "2**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_sys_id)."**".str_replace("'",'',$challan_no);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_sys_id)."**".str_replace("'",'',$challan_no);
			}
		}
		disconnect($con);
		die;
	}
}

if ($action=="delivery_id_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	$ex_data=explode("_",$data);
	?>
	<script>
		function js_set_value(id)
		{
			document.getElementById('selected_delivery_id').value=id;
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
            <form name="deliverysearch_1"  id="deliverysearch_1" autocomplete="off">
                <table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                   <tr>
                            <th colspan="6" align="center"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                    </tr>
                    <tr>
                        <th width="150">Company Name</th>
                        <th width="150">Party Name</th>
                        <th width="70">Delivery ID</th>
                        <th width="60">Year</th>
                        <th width="170">Date Range</th>
                        <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('deliverysearch_1','search_div','','','','');" /></th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td> <input type="hidden" id="selected_delivery_id"><? //$data=explode("_",$data); ?>  <!--  echo $data;-->
								<? echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $ex_data[0], "load_drop_down( 'subcon_knitting_delivery_controller', this.value, 'load_drop_down_party_name_pop', 'buyer_td' );",0); ?>
                            </td>
                            <td id="buyer_td"><? echo create_drop_down( "cbo_party_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$ex_data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $ex_data[1], "" ); ?></td>
                            <td><input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes" style="width:65px" /></td>
                            <td>
                                <?
                                    $selected_year=date("Y");
                                    echo create_drop_down( "cbo_year", 60, $year,"", 1, "-Year-", $selected_year, "",0 );
                                ?>
                            </td>
                            <td>
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:65px">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:65px">
                            </td>
                            <td>
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_year').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('cbo_string_search_type').value, 'create_delivery_search_list_view', 'search_div', 'subcon_knitting_delivery_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
                        </tr>
                        <tr>
                            <td colspan="6" align="center" valign="middle">
								<? echo load_month_buttons(1);  ?>
                                <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div id="search_div"></div>
            </form>
        </div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_delivery_search_list_view")
{
	$data=explode('_',$data);
	//echo $data[3];
	$search_type=$data[6];
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[5]!=0) $party_cond=" and a.party_id= '$data[5]'"; else $party_cond="";
	//$trans_Type="issue";
	if($search_type==1)
	{
		if ($data[3]!='') $delivery_id_cond=" and a.delivery_prefix_num ='$data[3]'"; else $delivery_id_cond="";
	}
	else if($search_type==4 || $search_type==0)
	{
		if ($data[3]!='') $delivery_id_cond=" and a.delivery_prefix_num like '%$data[3]%'"; else $delivery_id_cond="";
	}
	else if($search_type==2) //Starts With
	{
		if ($data[3]!='') $delivery_id_cond=" and a.delivery_prefix_num like '$data[3]%'"; else $delivery_id_cond="";
	}
	else if($search_type==3)
	{
		if ($data[3]!='') $delivery_id_cond=" and a.delivery_prefix_num like '%$data[3]'"; else $delivery_id_cond="";
	}

	if($db_type==0)
	{
		if ($data[1]!="" &&  $data[2]!="") $delivery_date= " and a.delivery_date between '".change_date_format($data[1],'yyyy-mm-dd')."' and '".change_date_format($data[2],'yyyy-mm-dd')."'"; else $delivery_date= "";
		$year_cond= "year(a.insert_date)as year";
		$order_id_cond= "group_concat(b.order_id) as order_id";
	}
	else if ($db_type==2)
	{
		if ($data[1]!="" &&  $data[2]!="") $delivery_date= " and a.delivery_date between '".change_date_format($data[1], "", "",1)."' and '".change_date_format($data[2], "", "",1)."'";  else $delivery_date= "";
		$year_cond= "TO_CHAR(a.insert_date,'YYYY') as year";
		$order_id_cond= "listagg(b.order_id,',') within group (order by b.order_id) as order_id";
	}

	$sql= "select a.id, a.delivery_no, a.company_id, a.delivery_prefix_num, $year_cond, a.location_id, a.party_id, a.challan_no, a.delivery_date, a.forwarder, a.transport_company, $order_id_cond from subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and a.process_id=2 $company $delivery_date $delivery_id_cond $party_cond group by a.id, a.delivery_no, a.company_id, a.delivery_prefix_num, a.insert_date, a.location_id, a.party_id, a.challan_no, a.delivery_date, a.forwarder, a.transport_company order by a.id DESC";

	$result = sql_select($sql);
	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$location_arr=return_library_array( "select id, location_name from  lib_location",'id','location_name');

	// $po_array=array();
	// $po_sql="select id, order_no, cust_style_ref from  subcon_ord_dtls where status_active=1 and is_deleted=0";
	// $result_po = sql_select($po_sql);
	// foreach($result_po as $row)
	// {
	// 	$po_array[$row[csf("id")]]['po']=$row[csf("order_no")];
	// 	$po_array[$row[csf("id")]]['style']=$row[csf("cust_style_ref")];
	// }
	$po_array=array();
	 $po_sql="select c.id, c.order_no, c.cust_style_ref from  subcon_ord_dtls c,subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and c.id=b.order_id   and c.status_active=1 and c.is_deleted=0  and b.status_active=1 and b.is_deleted=0 $company $delivery_date $delivery_id_cond $party_cond";
	$result_po = sql_select($po_sql);
	foreach($result_po as $row)
	{
		$po_array[$row[csf("id")]]['po']=$row[csf("order_no")];
		$po_array[$row[csf("id")]]['style']=$row[csf("cust_style_ref")];
	}

	?>
    <script>
		$(document).ready(function(e) {
            setFilterGrid('tbl_po_list',-1);
        });

	</script>
    <div>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table">
            <thead>
                <th width="50">SL</th>
                <th width="70">Delivery ID</th>
                <th width="60">Year</th>
                <th width="120">Party</th>
                <th width="120">Challan No</th>
                <th width="70">Delivery Date</th>
                <th width="110">Style</th>
                <th>Order</th>
            </thead>
     	</table>
     </div>
     <div style="width:750px; max-height:270px;overflow-y:scroll;" >
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="730" class="rpt_table" id="tbl_po_list">
			<?
			$i=1;
            foreach( $result as $row )
            {
                if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$order_no=''; $style_name="";
				$order_id=array_unique(explode(",",$row[csf("order_id")]));
				foreach($order_id as $val)
				{
					if($order_no=="") $order_no=$po_array[$val]['po']; else $order_no.=", ".$po_array[$val]['po'];
					if($style_name=="") $style_name=$po_array[$val]['style']; else $style_name.=", ".$po_array[$val]['style'];
				}
				?>

                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')]; ?>);" >
						<td width="50" align="center"><?php echo $i; ?></td>
						<td width="70" align="center"><?php echo $row[csf("delivery_prefix_num")]; ?></td>
                        <td width="60" align="center"><?php echo $row[csf("year")]; ?></td>
						<td width="120" align="center"><?php echo $party_arr[$row[csf("party_id")]]; ?></td>
						<td width="120"><?php echo $row[csf("challan_no")];  ?></td>
						<td width="70"><?php echo $row[csf("delivery_date")]; ?></td>
                        <td width="110"><p><?php echo $style_name; ?></p></td>
						<td><p><?php echo $order_no; ?></p></td>
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

if ($action=="load_php_data_to_form")
{
	//echo "select id, delivery_no, company_id, location_id, party_id, challan_no, delivery_date, forwarder, transport_company from subcon_delivery_mst where id='$data' and status_active=1 and is_deleted=0";die;
	$nameArray=sql_select( "select id, delivery_no, company_id, location_id, party_id, challan_no, delivery_date, forwarder, transport_company, vehical_no from subcon_delivery_mst where id='$data' and status_active=1 and is_deleted=0 " );
	foreach ($nameArray as $row)
	{
		echo "document.getElementById('txt_sys_id').value 			= '".$row[csf("delivery_no")]."';\n";
		echo "document.getElementById('cbo_company_name').value 	= '".$row[csf("company_id")]."';\n";
		echo "load_drop_down( 'requires/subcon_knitting_delivery_controller', $('#cbo_company_name').val(), 'load_drop_down_location', 'location_td' );";
		echo "document.getElementById('cbo_location').value			= '".$row[csf("location_id")]."';\n";
		echo "load_drop_down( 'requires/subcon_knitting_delivery_controller', $('#cbo_company_name').val(), 'load_drop_down_party_name', 'party_td' );";
		echo "document.getElementById('cbo_party_name').value		= '".$row[csf("party_id")]."';\n";
		echo "$('#cbo_party_name').attr('disabled','true')".";\n";
		echo "document.getElementById('txt_challan_no').value		= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('txt_delivery_date').value	= '".change_date_format($row[csf("delivery_date")])."';\n";
		echo "document.getElementById('txt_transport_company').value 		= '".$row[csf("transport_company")]."';\n";
		echo "document.getElementById('cbo_forwarder').value		= '".$row[csf("forwarder")]."';\n";
		echo "document.getElementById('txt_vehical_no').value		= '".$row[csf("vehical_no")]."';\n";
		echo "document.getElementById('update_id').value			= '".$row[csf("id")]."';\n";
		//echo "set_button_status(0, '".$_SESSION['page_permission']."','fnc_material_issue',1);\n";
	}
	exit();
}

if($action=="subcon_delivery_entry_print5")
{
	extract($_REQUEST);
	$ex_data=explode('*',$data);
	$company=$ex_data[0];
	$location=$ex_data[5];
	$cbo_template_id=$ex_data[6];
	$update_id=$ex_data[1];
	$sys_id=$ex_data[2];
	//print_r ($data);
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_library=return_library_array( "select id,supplier_name from lib_supplier", "id","supplier_name");
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$country_arr=return_library_array( "select id, country_name from lib_country", "id", "country_name");
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1", "id", "color_name");
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	//$item_arr=return_library_array( "select cons_comp_id, color_name from lib_color", "cons_comp_id", "color_name");

	//$color_arr=return_library_array( "select id, color_name from  lib_color", "id", "color_name"  );
	$sql_job_po="select a.id, a.order_no, a.cust_buyer, b.subcon_job, a.process_id from  subcon_ord_dtls a, subcon_ord_mst b where a.job_no_mst=b.subcon_job and b.is_deleted=0 and b.status_active=1 and a.status_active=1 and a.is_deleted=0";
	$job_po_array=array();
	$result_job_po=sql_select($sql_job_po);
	foreach($result_job_po as $row)
	{
		$job_po_array[$row[csf('id')]]['order_no']=$row[csf('order_no')];
		$job_po_array[$row[csf('id')]]['process_id']=$row[csf('process_id')];
		$job_po_array[$row[csf('id')]]['party_id']=$row[csf('party_id')];
		$job_po_array[$row[csf('id')]]['subcon_job']=$row[csf('subcon_job')];
		$job_po_array[$row[csf('id')]]['cust_buyer']=$row[csf('cust_buyer')];
	}
	//var_dump($job_po_array);
	$recChallan_arr=array();
	if($db_type==0)
	{
		$sql_rec="select b.order_id, group_concat(a.chalan_no) as chalan_no from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=1 group by b.order_id";
	}
	else if ($db_type==2)
	{
		$sql_rec="select b.order_id, listagg((cast(a.chalan_no as varchar2(4000))),',') within group (order by a.chalan_no) as chalan_no from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=1 group by b.order_id";
	}
	$result_sql_rec=sql_select($sql_rec);
	foreach($result_sql_rec as $row)
	{
		$chalan_no=array_unique(explode(",",$row[csf("chalan_no")]));
		$challan='';
		foreach($chalan_no as $val)
		{
			if($challan=="") $challan=$val; else $challan.=", ".$val;
		}

		$recChallan_arr[$row[csf('order_id')]]['chalan_no']=$challan;
	}

	$sql="select id, party_id, challan_no, process_id, delivery_date, forwarder, transport_company, vehical_no,delivery_no,inserted_by from subcon_delivery_mst where id='$update_id' and company_id='$company' and status_active=1 and is_deleted=0";

	$dataArray=sql_select($sql);
	$inserted_by=$dataArray[0][csf('inserted_by')];
	$com_dtls = fnc_company_location_address($company, $location, 2);
?>
    <div style="width:930px;">
   	<table width="100%" cellpadding="0" cellspacing="0" >
       <tr>
           <td width="200" align="right">
               <img src='../../<? echo $com_dtls[2]; /*if($imge_arr[str_replace("'","",$company)]!="") echo $imge_arr[str_replace("'","",$company)]; else echo $com_dtls[2];*/ ?>' height='120%' width='120%' />
           </td>
           <td>
                <table width="800" cellspacing="0" align="center">
                    <tr>
                        <td align="center" style="font-size:x-large">
                        	<strong ><? echo $com_dtls[0]; ?></strong>
                        </td>
                    </tr>
                    <tr class="form_caption">
                        <td  align="center" style="font-size:14px">
                            <?
                            	echo $com_dtls[1];
                                /*$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, contact_no, country_id, province, city, zip_code, contact_no, email, website, vat_number from lib_company where id=$company and status_active=1 and is_deleted=0");
                                foreach ($nameArray as $result)
                                {
                                ?>
                                    <? echo $result[csf('plot_no')]; ?> &nbsp;
                                    <? echo $result[csf('level_no')]; ?> &nbsp;
                                    <? echo $result[csf('road_no')]; ?> &nbsp;
                                    <? echo $result[csf('block_no')];?> &nbsp;
                                    <? echo $result[csf('city')]; ?> &nbsp;
                                    <? echo $result[csf('contact_no')]; ?> &nbsp;
                                    <? echo $result[csf('email')]; ?> &nbsp;
                                    <? echo $result[csf('website')];?> <br>
                                   <b> Vat No : <? echo $result[csf('vat_number')]; ?></b> <?
                                }*/
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="font-size:18px"><strong><? echo $ex_data[3]; ?> Challan</strong></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <table width="900" cellspacing="0" align="right">
        <tr><td colspan="6" align="center"><hr></hr></td></tr>
        <tr>
        	<td colspan="2">&nbsp;</td>
        	<td><strong>Delivery No :</strong></td>
        	<td> <? echo $dataArray[0][csf('delivery_no')] ; ?> </td>
        	<td colspan="2">&nbsp;</td>
        </tr>
        <tr>
			<?
                $party_add=$dataArray[0][csf('party_id')];
                $nameArray=sql_select( "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$party_add");
                foreach ($nameArray as $result)
                {
                    $address="";
                    if($result!="")  $address=$result[csf('address_1')];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
                }
            ?>
        	<td width="300" rowspan="4" valign="top" colspan="2" style="font-size:12px"><strong>Party : <? echo $buyer_library[$party_add].'<br>'.$address;  ?></strong>


            </td>
            <td width="125" style="font-size:12px"><strong>Challan No :</strong></td><td width="170px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
            <td width="125" style="font-size:12px"><strong>Delivery Date :</strong></td><td width="170px"><? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
        </tr>
        <tr>
            <td style="font-size:12px"><strong>Transport Com.:</strong></td><td><? echo $dataArray[0][csf('transport_company')]; ?></td>
            <td style="font-size:12px"><strong>Forwarder:</strong></td><td><? echo $supplier_library[$dataArray[0][csf('forwarder')]]; ?></td>
        </tr>
        <tr>
            <td style="font-size:12px"><strong>Vehicle No:</strong></td><td><? echo $dataArray[0][csf('vehical_no')]; ?></td>
            <td style="font-size:12px"><strong>D.O No.</strong></td><td><? //echo $supplier_library[$dataArray[0][csf('forwarder')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Bar Code:</strong></td><td colspan="3" id="barcode_img_id"></td>
        </tr>
    </table>
    <br>
    <div style="width:120%; height:920px">
		<?
		$machine_array=array();
		$mac_sql="Select id, machine_no, dia_width, gauge from  lib_machine_name where status_active=1 and is_deleted=0";

		$result_mac_sql=sql_select($mac_sql);
        foreach($result_mac_sql as $row)
        {
			$machine_array[$row[csf('id')]]['machine_no']=$row[csf('machine_no')];
			$machine_array[$row[csf('id')]]['gauge']=$row[csf('gauge')];
			$machine_array[$row[csf('id')]]['dia_width']=$row[csf('dia_width')];
		}
		$mst_id=$dataArray[0][csf('id')];
        $prod_dia_array=array();
        if($db_type==2)
        {
            // $prod_dia_sql="select order_id, cons_comp_id, fabric_description, gsm, dia_width, dia_width_type, color_id, yarn_lot,
            // listagg((cast(machine_id as varchar2(4000))),',') within group (order by machine_id) as machine_id,
			// listagg((cast(machine_gg as varchar2(4000))),',') within group (order by machine_gg) as machine_gg,
            // listagg((cast(machine_dia as varchar2(4000))),',') within group (order by machine_dia) as machine_dias,
            // listagg((cast(yrn_count_id as varchar2(4000))),',') within group (order by yrn_count_id) as yrn_count_id,
            // listagg((cast(stitch_len as varchar2(4000))),',') within group (order by stitch_len) as stitch_len,
			// listagg((cast(brand as varchar2(4000))),',') within group (order by brand) as brand,
            // sum(product_qnty) as production_qnty from subcon_production_dtls where product_type=2 and status_active=1 and is_deleted=0 group by order_id, cons_comp_id, fabric_description, gsm, dia_width, dia_width_type, color_id,yarn_lot";
			$prod_dia_sql="select b.order_id, b.cons_comp_id, b.fabric_description, b.gsm, b.dia_width, b.dia_width_type, b.color_id, b.yarn_lot,
			b.machine_id  as machine_id,
			 b.machine_gg as machine_gg,
			 b.machine_dia as machine_dias,
			b.yrn_count_id as yrn_count_id,
			 b.stitch_len  as stitch_len,
			 b.brand as brand,
			 (b.product_qnty) as production_qnty from subcon_production_dtls b,subcon_delivery_dtls c where b.order_id=c.order_id and c.mst_id='$mst_id' and c.process_id=2 and b.product_type=2 and b.status_active=1 and b.is_deleted=0  ";
        }
        else if($db_type==0)
        {
            $prod_dia_sql="select order_id, cons_comp_id, fabric_description, gsm, dia_width, dia_width_type, color_id,yarn_lot,
            group_concat(machine_id) as machine_id,  group_concat(machine_gg) as machine_gg, group_concat(machine_dia) as machine_dias,
            group_concat(yrn_count_id) as yrn_count_id,
            group_concat(stitch_len) as stitch_len,
			group_concat(brand) as brand,
            sum(product_qnty) as production_qnty from subcon_production_dtls where product_type=2 and status_active=1 and is_deleted=0 group by order_id, cons_comp_id, fabric_description, gsm, dia_width, dia_width_type, color_id, yarn_lot";
        }
        //echo $prod_dia_sql;
        $result_prod_dia_sql=sql_select($prod_dia_sql);
        foreach($result_prod_dia_sql as $row)
        {
			$machine_id=array_unique(explode(",",$row[csf('machine_id')]));
			$machine_name=""; $gauge_value=""; $machine_dia="";
			foreach($machine_id as $val)
			{
				if($machine_name=="") $machine_name=$machine_array[$val]['machine_no']; else $machine_name.=", ".$machine_array[$val]['machine_no'];
				if($gauge_value=="") $gauge_value=$machine_array[$val]['gauge']; else $gauge_value.=", ".$machine_array[$val]['gauge'];
				if($machine_dia=="") $machine_dia=$machine_array[$val]['dia_width']; else $machine_dia.=", ".$machine_array[$val]['dia_width'];
			}

			// if($row[csf('machine_dias')]=="") $machine_dia=$machine_dia; else $machine_dia=implode(",",array_filter(array_unique(explode(",",$row[csf('machine_dias')]))));
			// if($row[csf('machine_gg')]=="") $gauge_value=$gauge_value; else $gauge_value=implode(",",array_filter(array_unique(explode(",",$row[csf('machine_gg')]))));

			// $yarn_count=array_unique(explode(",",$row[csf('yrn_count_id')]));
			// $count_name="";
			// foreach($yarn_count as $val)
			// {
			// 	if($count_name=="") $count_name=$count_arr[$val]; else $count_name.=", ".$count_arr[$val];
			// }

			// $stitch_len_id=array_unique(explode(",",$row[csf('stitch_len')]));
			// $stitch_len_name="";
			// foreach($stitch_len_id as $val)
			// {
			// 	if($stitch_len_name=="") $stitch_len_name=$val; else $stitch_len_name.=", ".$val;
			// }

			// $brand=array_unique(explode(",",$row[csf('brand')]));
			// $brand_name="";
			// foreach($brand as $val)
			// {
			// 	if($brand_name=="") $brand_name=$val; else $brand_name.=", ".$val;
			// }
			$count_name=$count_arr[$val]; 
			$gauge_value=$machine_array[$val]['gauge']; 
			$machine_dia=$machine_array[$val]['dia_width'];
			$stitch_len_name=$row[csf('stitch_len')];
			$brand_name=$row[csf('brand')];

            $prod_dia_array[$row[csf('order_id')]][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('dia_width_type')]][$row[csf('color_id')]]['machine_dia'].=$machine_dia.',';
			$prod_dia_array[$row[csf('order_id')]][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('dia_width_type')]][$row[csf('color_id')]]['fabric_description'].=$row[csf('fabric_description')];
			$prod_dia_array[$row[csf('order_id')]][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('dia_width_type')]][$row[csf('color_id')]]['gauge'].=$gauge_value.',';
            $prod_dia_array[$row[csf('order_id')]][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('dia_width_type')]][$row[csf('color_id')]]['yrn_count_id'].=$count_name.',';
			$prod_dia_array[$row[csf('order_id')]][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('dia_width_type')]][$row[csf('color_id')]]['stitch_len'].=$stitch_len_name.',';
			$prod_dia_array[$row[csf('order_id')]][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('dia_width_type')]][$row[csf('color_id')]]['brand_name'].=$brand_name.',';
        }
        //var_dump($prod_dia_array);

     

        $sql_kniting="select order_id, item_id, dia, width_dia_type, gsm, color_id, yarn_lot, delivery_qty, delivery_pcs, carton_roll, remarks, collar_cuff,reject_qty from subcon_delivery_dtls where mst_id='$mst_id' and process_id=2 ";
        $sql_kniting_result=sql_select($sql_kniting);
		?>
        <div>
        <table align="left" cellspacing="0" width="1020"  border="1" rules="all" class="rpt_table" style="margin-top: 10px;">

			<?
			//die;
			$collar_cuff_arr=array();
			foreach($sql_kniting_result as $row)
            {
				if($row[csf('collar_cuff')]!="")
				{
					$collar_cuff_arr[]=$row[csf('collar_cuff')];
				}
				$collar_cuff_count=count($collar_cuff_arr);
			}
            $lib_item_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');
            $i=1; $z=1; $collar_cuff_span=0;
            foreach($sql_kniting_result as $row)
            {
				//echo $collar_cuff_cond;
				if($z==1)
				{
				?>
                <thead bgcolor="#dddddd" align="center">
                    <th width="25">SL</th>
                    <th width="65">Order No</th>
                    <th width="70">Job Number</th>
                    <th width="60">Cust. Buyer </th>
                    <th width="60">Count</th>
                    <th width="50">Brand</th>
                    <th width="40">Lot</th>
                    <th width="30">M/C Dia</th>
                    <th width="40">Gauge</th>
                    <th width="30">F.Dia</th>
                    <th width="50">Dia Type</th>
                    <th width="30">F.GSM</th>
                    <th width="50">S/L</th>
                    <th width="60">Color</th>
                    <th width="100">Description</th>
                    <?
					if($collar_cuff_count!=0)
					{
						$row_show=0;
					?>
                    <th width="50">Collar Cuff Measurement</th>
                    <?
					}
					else
					{
						$row_show=1;
					}
					?>
                    <th width="40">Roll /Bag</th>
                    <th width="50">Delivery Qty (KG)</th>
                    <th width="50">Delivery Qty (Pcs)</th>
                    <th width="50">Reject Qty</th>
                    <th width="50">Rec. Challan</th>
                    <th>Remarks</th>
                </thead>
                <?
				$z++;
				}
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				$fabric_description=$prod_dia_array[$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('color_id')]]['fabric_description'];
				$stitch_len=rtrim($prod_dia_array[$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('color_id')]]['stitch_len'],',');
				$gauge_val=rtrim($prod_dia_array[$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('color_id')]]['gauge'],',');
				$machine_dia_val=rtrim($prod_dia_array[$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('color_id')]]['machine_dia'],',');
				$yrn_count_val.=rtrim($prod_dia_array[$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('color_id')]]['yrn_count_id'],',');
				$brand_name_val==rtrim($prod_dia_array[$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('color_id')]]['brand_name'],',');
				$machine_dia_uni=implode(",",array_unique(explode(", ",$machine_dia_val)));
				$gauge_val_uni=implode(",",array_unique(explode(", ",$gauge_val)));

                ?>

                <tr bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $i; ?></td>
                    <td><p><? echo $job_po_array[$row[csf('order_id')]]['order_no']; ?></p></td>
                    <td><p><? echo $job_po_array[$row[csf('order_id')]]['subcon_job']; ?></p></td>
                    <td><p><? echo $job_po_array[$row[csf('order_id')]]['cust_buyer']; ?></p></td>
                    <td><p><? echo $yrn_count_val; ?></p></td>
                    <td><p><? echo $brand_name_val; ?></p></td>
                    <td><div style="word-wrap:break-word; width:40px"><? echo $row[csf('yarn_lot')]; ?></div></td>
                    <td><p><? echo $machine_dia_uni; ?></p></td>
                    <td><p><? echo $gauge_val_uni; ?></p></td>
                    <td><p><? echo $row[csf('dia')]; ?></p></td>
                    <td><p><? echo $fabric_typee[$row[csf('width_dia_type')]]; ?></p></td>
                    <td><p><? echo $row[csf('gsm')]; ?></p></td>
                    <td><p><? echo $stitch_len; ?></p></td>
                    <td><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                    <td><p><? echo $fabric_description; ?></p></td>
                    <? if($row_show==0)
					{
					?>
                        <td><p><? echo $row[csf('collar_cuff')]; ?></p></td>
                    <?
					}
					?>
                    <td align="right"><? echo number_format($row[csf('carton_roll')],2,'.',''); ?></td>
                    <td align="right"><? echo number_format($row[csf('delivery_qty')],2,'.',''); ?></td>
                    <td align="right"><? echo number_format($row[csf('delivery_pcs')],2,'.',''); ?></td>
                    <td align="right"><? echo $row[csf('reject_qty')]; ?></td>
                    <td><p><? echo $recChallan_arr[$row[csf('order_id')]]['chalan_no']; ?></p></td>
                    <td><p><? echo $row[csf('remarks')]; ?></p></td>
                </tr>
                <?
                $grand_tot_roll+=$row[csf('carton_roll')];
                $grand_tot_finish_qty+=$row[csf('delivery_qty')];
                $grand_tot_delivery_pcs+=$row[csf('delivery_pcs')];
				$grand_tot_reject_qty+=$row[csf('reject_qty')];
				if($row_show==0)
				{
					$collar_cuff_span=15;
				}
				else
				{
					$collar_cuff_span=14;
				}
                $i++;
                }
            ?>
                <tfoot style="font-size:11px">
                    <th colspan="<? echo $collar_cuff_span; ?>" align="right"><strong>Total</strong></th>
                    <th align="right"><? echo number_format($grand_tot_roll,2,'.',''); ?></th>
                    <th align="right"><? echo number_format($grand_tot_finish_qty,2,'.',''); ?></th>
                    <th align="right"><? echo number_format($grand_tot_delivery_pcs,2,'.',''); ?></th>
                    <th align="right"><? echo $grand_tot_reject_qty; ?></th>
                    <th>&nbsp;</th>
                </tfoot>
            </table>
            </div>
           	<?
            //echo signature_table(64, $company, "930px");
            //echo signature_table(64, $company, "930px",$cbo_template_id,70,$user_lib_name[$inserted_by]);
			if( $ex_data[4]==5)
			{
         ?>
          </div>
    	<table width="900" cellspacing="0" >
        	<tr><td colspan="6">


            </td></tr>
            <tr><td colspan="6" align="center">,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,</td></tr>
            <tr>
				<td colspan="6">
                    <table width="100%" cellpadding="0" cellspacing="0" >
                       <tr>
                           <td width="70" align="right">
                               <img src='../../<? echo $imge_arr[str_replace("'","",$company)]; ?>' height='70%' width='70%' />
                           </td>
                           <td>
                                <table width="800" cellspacing="0" align="center">
                                    <tr>
                                        <td align="center" style="font-size:x-large"><strong ><? echo $company_library[$company]; ?></strong></td>
                                    </tr>
                                    <tr class="form_caption">
                                        <td  align="center" style="font-size:14px">
                                            <?
												$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, contact_no, country_id, province, city, zip_code, contact_no, email, website, vat_number from lib_company where id=$company and status_active=1 and is_deleted=0");
                                                foreach ($nameArray as $result)
                                                {
                                                ?>
                                                    <? echo $result[csf('plot_no')]; ?> &nbsp;
                                                    <? echo $result[csf('level_no')]; ?> &nbsp;
                                                    <? echo $result[csf('road_no')]; ?> &nbsp;
                                                    <? echo $result[csf('block_no')];?> &nbsp;
                                                    <? echo $result[csf('city')]; ?> &nbsp;
                                                    <? echo $result[csf('contact_no')]; ?> &nbsp;
                                                    <? echo $result[csf('email')]; ?> &nbsp;
                                                    <? echo $result[csf('website')]; ?> <br>
                                                   <b> Vat No : <? echo $result[csf('vat_number')]; ?></b> <?
                                                }
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center" style="font-size:18px"><strong><? echo $production_process[$dataArray[0][csf('process_id')]]; ?> Gate Pass</strong></td>
                                    </tr>
                                </table>
                           </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="6" align="right" style="font-size:14px">
                    <table cellspacing="0" width="350"  border="1" rules="all" class="rpt_table" >
                        <thead bgcolor="#dddddd" align="center">
                            <th width="150">Roll</th>
                            <th width="150">Weight</th>
                        </thead>
                        <tbody>
                        	<tr>
                            	<td align="center"><? echo $grand_tot_roll; ?></td>
                               <td align="center"><? echo $grand_tot_finish_qty; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
        <?php
        echo signature_table(64, $company, "930px",$cbo_template_id,70,$user_lib_name[$inserted_by]);
        ?>
	</div>
	<?
	}
	?>
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
		function generateBarcode( valuess )
		{
			//alert(valuess);
			var value = valuess;//$("#barcodeValue").val();
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();
			var settings = {
			  output:renderer,
			  bgColor: '#FFFFFF',
			  color: '#000000',
			  barWidth: 1,
			  barHeight: 30,
			  moduleSize:5,
			  posX: 10,
			  posY: 20,
			  addQuietZone: 1
			};
			$("#barcode_img_id").html('11');
			 value = {code:value, rect: false};
			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $sys_id; ?>');
	</script>
    <?
	exit();
}
if($action=="subcon_delivery_entry_print_seven")
{
	extract($_REQUEST);
	$ex_data=explode('*',$data);
	$company=$ex_data[0];
	$type=$ex_data[4];
	$location=$ex_data[5];
	$update_id=$ex_data[1];
	$sys_id=$ex_data[2];

	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_library=return_library_array( "select id,supplier_name from lib_supplier", "id","supplier_name");
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$country_arr=return_library_array( "select id, country_name from lib_country", "id", "country_name");
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$sql_job_po="select a.id, a.order_no, a.cust_buyer, b.subcon_job, a.process_id from  subcon_ord_dtls a, subcon_ord_mst b where a.job_no_mst=b.subcon_job and b.is_deleted=0 and b.status_active=1 and a.status_active=1 and a.is_deleted=0";
	$job_po_array=array();
	$result_job_po=sql_select($sql_job_po);
	foreach($result_job_po as $row)
	{
		$job_po_array[$row[csf('id')]]['order_no']=$row[csf('order_no')];
		$job_po_array[$row[csf('id')]]['process_id']=$row[csf('process_id')];
		$job_po_array[$row[csf('id')]]['party_id']=$row[csf('party_id')];
		$job_po_array[$row[csf('id')]]['subcon_job']=$row[csf('subcon_job')];
		$job_po_array[$row[csf('id')]]['cust_buyer']=$row[csf('cust_buyer')];
	}
	$recChallan_arr=array();
	$sql_rec="select b.order_id, listagg((cast(a.chalan_no as varchar2(4000))),',') within group (order by a.chalan_no) as chalan_no from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=1 group by b.order_id";
	$result_sql_rec=sql_select($sql_rec);
	foreach($result_sql_rec as $row)
	{
		$chalan_no=array_unique(explode(",",$row[csf("chalan_no")]));
		$challan='';
		foreach($chalan_no as $val)
		{
			if($challan=="") $challan=$val; else $challan.=", ".$val;
		}

		$recChallan_arr[$row[csf('order_id')]]['chalan_no']=$challan;
	}

	$sql_bill_status="select id, bill_status from subcon_delivery_dtls where mst_id='$update_id' and process_id=2 ";
	$sql_bill_status_result=sql_select($sql_bill_status);
	foreach($sql_bill_status_result as $rate_row)
	{ 
		$bill_status_result=$rate_row[csf('bill_status')];
	}?>
	<div style="width:1100px;">
	<?
  	if($bill_status_result==1)
		{
			
			$sql="SELECT a.order_id, a.item_id, a.dia, a.width_dia_type, a.gsm, a.color_id, a.yarn_lot, a.delivery_qty, a.delivery_pcs, a.carton_roll, a.remarks, a.collar_cuff, a.reject_qty, c.challan_no, c.process_id, c.vehical_no, c.delivery_date,c.delivery_no,c.party_id FROM subcon_delivery_dtls a, subcon_inbound_bill_dtls b, subcon_delivery_mst c, subcon_inbound_bill_mst d WHERE c.id= '$update_id' AND c.company_id = '$company' AND b.challan_no = c.challan_no AND a.process_id = 2 AND a.mst_id = c.ID AND d.id = b.mst_id GROUP BY a.order_id, a.item_id, a.dia, a.width_dia_type, a.gsm, a.color_id, a.yarn_lot, a.delivery_qty, a.delivery_pcs, a.carton_roll, a.remarks, a.collar_cuff, a.reject_qty, c.challan_no, c.process_id, c.vehical_no, c.delivery_date,c.delivery_no,c.party_id ";
			$dataArray=sql_select($sql);
			$com_dtls = fnc_company_location_address($company, $location, 2);
			?>

			
				<div style="width:1100px;">
						<table width="100%" cellpadding="0" cellspacing="0" >
							<tr>
								<td width="200" align="right">
									<img src='../../<? echo $com_dtls[2]; /*if($imge_arr[str_replace("'","",$company)]!="") echo $imge_arr[str_replace("'","",$company)]; else echo $com_dtls[2];*/ ?>' height='60%' width='60%' />
								</td>
								<td>
									<table width="800" cellspacing="0" align="center">
										<tr>
											<td align="center" style="font-size:40">
												<strong ><? echo $com_dtls[0]; ?></strong>
											</td>
										</tr>
										<tr class="form_caption">
											<td  align="center" style="font-size:18px">
												<?
													echo $com_dtls[1];
												?>
											</td>
										</tr>
										<tr>
											<td align="center" style="font-size:26px"><strong><? echo $ex_data[3]; ?> & Bill</strong></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
						<table width="100%" cellspacing="0" align="right" style="font-size:18px">
							<tr><td colspan="6" align="center"><hr></hr></td></tr>
							<tr>
									<?
									$party_add=$dataArray[0][csf('party_id')];
									$nameArray=sql_select( "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$party_add");
									foreach ($nameArray as $result)
									{
										$address="";
										if($result!="")  $address=$result[csf('address_1')];
									}
									?>
								<td width="100"><strong>Party</strong></td><td width="200">:<? echo $buyer_library[$party_add];  ?></td>
								<td width="100"><strong>Delivery No</strong></td><td width="200">:<? echo $dataArray[0][csf('delivery_no')] ; ?></td>
								<td width="100"><strong>Delivery Date</strong></td><td width="200">:<? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
							</tr>
							<tr>
								<td width="100" style="vertical-align:top; padding-top: 10px;"><strong>Address</strong> </td><td width="200" style="vertical-align:top ; padding-top: 10px;">:<? echo $address;  ?></td>
								<td width="100" style="vertical-align:top ; padding-top: 10px;"><strong>Challan No</strong></td><td style="vertical-align:top ; padding-top: 10px;">:<? echo $dataArray[0][csf('challan_no')]; ?></td>
								<? echo "<td style='vertical-align:top; padding-top:10px;' width='200' colspan='2' id='barcode_img_id'></td>";?>
							</tr>
						</table>
							<br>
								<?
								$machine_array=array();
								$mac_sql="Select id, machine_no, dia_width, gauge from  lib_machine_name where status_active=1 and is_deleted=0";
								$result_mac_sql=sql_select($mac_sql);
								foreach($result_mac_sql as $row)
								{
									$machine_array[$row[csf('id')]]['machine_no']=$row[csf('machine_no')];
									$machine_array[$row[csf('id')]]['gauge']=$row[csf('gauge')];
									$machine_array[$row[csf('id')]]['dia_width']=$row[csf('dia_width')];
								}
								$size_array=array();
								$size_sql="Select b.id as dtls_id,c.product_size from  subcon_production_dtls b,subcon_pro_roll_details c where b.id=c.dtls_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.entry_form = 159";
								$result_size_sql=sql_select($size_sql);
								foreach($result_size_sql as $row)
								{
									$size_array[$row[csf('dtls_id')]]['product_size']=$row[csf('product_size')];
								}
								$prod_dia_array=array();
								$prod_dia_sql="select a.mst_id,a.order_id, a.cons_comp_id, a.fabric_description, a.gsm, a.dia_width, a.dia_width_type, a.color_id, a.yarn_lot,c.product_size,
								listagg((cast(a.machine_id as varchar2(4000))),',') within group (order by a.machine_id) as machine_id,
								listagg((cast(a.machine_gg as varchar2(4000))),',') within group (order by a.machine_gg) as machine_gg,
								listagg((cast(a.machine_dia as varchar2(4000))),',') within group (order by a.machine_dia) as machine_dias,
								listagg((cast(a.yrn_count_id as varchar2(4000))),',') within group (order by a.yrn_count_id) as yrn_count_id,
								listagg((cast(a.stitch_len as varchar2(4000))),',') within group (order by a.stitch_len) as stitch_len,
								listagg((cast(a.brand as varchar2(4000))),',') within group (order by a.brand) as brand,
								sum(a.product_qnty) as production_qnty from subcon_production_dtls a, subcon_pro_roll_details c where a.mst_id=c.mst_id and a.product_type=2 and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0  group by a.mst_id,a.order_id, a.cons_comp_id, a.fabric_description, a.gsm, a.dia_width, a.dia_width_type, a.color_id,a.yarn_lot,c.product_size";
								$result_prod_dia_sql=sql_select($prod_dia_sql);
								foreach($result_prod_dia_sql as $row)
								{
									$machine_id=array_unique(explode(",",$row[csf('machine_id')]));
									$machine_name=""; $gauge_value=""; $machine_dia="";
									foreach($machine_id as $val)
									{
										if($machine_name=="") $machine_name=$machine_array[$val]['machine_no']; else $machine_name.=", ".$machine_array[$val]['machine_no'];
										if($gauge_value=="") $gauge_value=$machine_array[$val]['gauge']; else $gauge_value.=", ".$machine_array[$val]['gauge'];
										if($machine_dia=="") $machine_dia=$machine_array[$val]['dia_width']; else $machine_dia.=", ".$machine_array[$val]['dia_width'];
									}

									//if($row[csf('machine_dias')]=="") $machine_dia=$machine_dia; else $machine_dia=implode(",",array_filter(array_unique(explode(",",$row[csf('machine_dias')]))));
									if($row[csf('machine_dias')]=="") $machine_dia=$machine_dia; else $machine_dia=implode(",",array_unique(explode(",",$row[csf('machine_dias')])));
									if($row[csf('machine_gg')]=="") $gauge_value=$gauge_value; else $gauge_value=implode(",",array_filter(array_unique(explode(",",$row[csf('machine_gg')]))));

									$yarn_count=array_unique(explode(",",$row[csf('yrn_count_id')]));
									$count_name="";
									foreach($yarn_count as $val)
									{
										if($count_name=="") $count_name=$count_arr[$val]; else $count_name.=", ".$count_arr[$val];
									}

									$stitch_len_id=array_unique(explode(",",$row[csf('stitch_len')]));
									$stitch_len_name="";
									foreach($stitch_len_id as $val)
									{
										if($stitch_len_name=="") $stitch_len_name=$val; else $stitch_len_name.=", ".$val;
									}
									
								    $brand=array_unique(explode(",",$row[csf('brand')]));
									$brand_name="";
									foreach($brand as $val)
									{
										if($brand=="") $brand_name=$val; else $brand_name.=$val.", ";
									}
									
									$order_idarr=array_unique(explode(",",$row[csf('order_id')]));
									foreach($order_idarr as $order_id)
									{

									//and MST_ID = 129767
									if($machine_dia)
									{
										$prod_dia_array[$order_id][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('dia_width_type')]][$row[csf('color_id')]]['machine_dia']=$machine_dia;
									}
									$prod_dia_array[$order_id][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('dia_width_type')]][$row[csf('color_id')]]['fabric_description']=$row[csf('fabric_description')];
									$prod_dia_array[$order_id][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('dia_width_type')]][$row[csf('color_id')]]['gauge']=$gauge_value;
									$prod_dia_array[$order_id][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('dia_width_type')]][$row[csf('color_id')]]['yrn_count_id']=$count_name;
									$prod_dia_array[$order_id][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('dia_width_type')]][$row[csf('color_id')]]['stitch_len']=$stitch_len_name;
									$prod_dia_array[$order_id][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('dia_width_type')]][$row[csf('color_id')]]['brand_name']=$brand_name;
									}
								}
								 $sql_kniting="select a.bill_status,a.order_id, a.item_id, a.dia, a.width_dia_type, a.gsm, a.color_id, a.yarn_lot, a.delivery_qty, a.delivery_pcs, a.carton_roll, a.remarks, a.collar_cuff, a.reject_qty, c.challan_no, c.process_id, c.vehical_no, c.delivery_date,b.rate, b.amount from subcon_delivery_dtls a, subcon_inbound_bill_dtls b, subcon_delivery_mst c, subcon_inbound_bill_mst d where c.id = '$update_id' and c.company_id = '$company' and b.challan_no = c.challan_no and b.delivery_date=c.delivery_date and a.process_id = 2 and a.mst_id = c.id and d.id = b.mst_id and b.delivery_id=a.id group by a.bill_status,a.order_id, a.item_id, a.dia, a.width_dia_type, a.gsm, a.color_id, a.yarn_lot, a.delivery_qty, a.delivery_pcs, a.carton_roll, a.remarks, a.collar_cuff, a.reject_qty, c.challan_no, c.process_id, c.vehical_no, c.delivery_date,b.rate, b.amount ";					

								$sql_kniting_result=sql_select($sql_kniting);
								?>
								<div>
									<table align="left" cellspacing="0" width="100%"  border="1" rules="all" class="rpt_table" style="margin-top: 10px; font-size:16px;">
										<?
										$collar_cuff_arr=array();
										foreach($sql_kniting_result as $row)
										{
											if($row[csf('collar_cuff')]!="")
											{
												$collar_cuff_arr[]=$row[csf('collar_cuff')];
											}
											$collar_cuff_count=count($collar_cuff_arr);
										}
										$lib_item_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');
										$i=1; $z=1; $collar_cuff_span=0;
										foreach($sql_kniting_result as $row)
										{
											if($z==1)
											{
											?>
											<thead bgcolor="#dddddd" align="center">
												<th width="25">SL</th>
												<th width="65">Order No</th>
												<th width="70">Job No</th>
												<th width="60">Cust. Buyer </th>
												<th width="60">Yarn Count</th>
												<th width="50">Brand</th>
												<th width="40">Lot</th>
												<th width="30">Mc Dia/Gage</th>
												<th width="30">Finish Dia</th>
												<th width="50">Dia Type</th>
												<th width="30">Finish GSM</th>
												<th width="50">S/L</th>
												<th width="60">Colour</th>
												<th width="100">F/Description</th>
												<th width="40">Roll</th>
												<th width="50">Del. Qty.Kg</th>
												<th width="50">Del. Qty Pcs</th>
												<th width="50">Rate</th>
												<th width="50">Amount</th>
												<th width="50">Remark</th>
											</thead>
											<?
											$z++;
											}
											if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
											$fabric_description=$prod_dia_array[$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('color_id')]]['fabric_description'];
											$stitch_len=$prod_dia_array[$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('color_id')]]['stitch_len'];
											$gauge_val=$prod_dia_array[$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('color_id')]]['gauge'];
											$machine_dia_val=$prod_dia_array[$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('color_id')]]['machine_dia'];
											$yrn_count_val=$prod_dia_array[$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('color_id')]]['yrn_count_id'];
											$brand_name_val=$prod_dia_array[$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('color_id')]]['brand_name'];
											$machine_dia_uni=implode(",",array_unique(explode(", ",$machine_dia_val)));
											$gauge_val_uni=implode(",",array_unique(explode(", ",$gauge_val)));
											?>
											<tr bgcolor="<? echo $bgcolor; ?>">
												<td><? echo $i; ?></td>
												<td><p><? echo $job_po_array[$row[csf('order_id')]]['order_no']; ?></p></td>
												<td><p><? echo $job_po_array[$row[csf('order_id')]]['subcon_job']; ?></p></td>
												<td><p><? echo $job_po_array[$row[csf('order_id')]]['cust_buyer']; ?></p></td>
												<td><p><? echo $yrn_count_val; ?></p></td>
												<td><p><? echo $brand_name_val; ?></p></td>
												<td><div style="word-wrap:break-word; width:40px"><? echo $row[csf('yarn_lot')]; ?></div></td>
												<td><p><? echo $machine_dia_uni."x".$gauge_val_uni; ?></p></td>
												<td><p><? echo $row[csf('dia')]; ?></p></td>
												<td><p><? echo $fabric_typee[$row[csf('width_dia_type')]]; ?></p></td>
												<td><p><? echo $row[csf('gsm')]; ?></p></td>
												<td><p><? echo $stitch_len; ?></p></td>
												<td><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
												<td><p><? echo $fabric_description; ?></p></td>
												<td align="right"><? echo number_format($row[csf('carton_roll')],2,'.',''); ?></td>
												<td align="right"><? echo number_format($row[csf('delivery_qty')],2,'.',''); ?></td>
												<td align="right"><? echo number_format($row[csf('delivery_pcs')],2,'.',''); ?></td>
												<td align="right"><p><?=$row[csf('rate')]; ?></p></td>
												<td align="right"><?  echo number_format($row[csf('amount')]); ?></td>
												<td><p><? echo $row[csf('remarks')]; ?></p></td>
												
											</tr>
											<?
											$grand_tot_roll+=$row[csf('carton_roll')];
											$grand_tot_finish_qty+=$row[csf('delivery_qty')];
											$grand_tot_delivery_pcs+=$row[csf('delivery_pcs')];
											$grand_tot_reject_qty+=$row[csf('reject_qty')];
											$total_amount+=$row[csf('amount')];
											if($row_show==0)
											{
												$collar_cuff_span=14;
											}
											else
											{
												$collar_cuff_span=13;
											}
											$i++;
										}
											?>
											<tfoot style="font-size:18px">
												<th colspan="<? echo $collar_cuff_span; ?>" align="right"><strong>Total</strong></th>
												<th align="right"><? echo number_format($grand_tot_roll,2,'.',''); ?></th>
												<th align="right"><? echo number_format($grand_tot_finish_qty,2,'.',''); ?></th>
												<th align="right"><? echo number_format($grand_tot_delivery_pcs,2,'.',''); ?></th>
												<th></th>
												<th align="right"><? echo number_format($total_amount,2,'.','');?></th>
											</tfoot>
									</table>
								</div>
								<?
								echo signature_table(64, $company, "930px"); 
								?>
							</div>
				</div>
				<script type="text/javascript" src="../../js/jquery.js"></script>
				<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
				<script>
				function generateBarcode( valuess )
				{
								
					var value = valuess;
					var btype = 'code39';
					var renderer ='bmp';
					var settings = {
									output:renderer,
									bgColor: '#FFFFFF',
									color: '#000000',
									barWidth: 1,
									barHeight: 30,
									moduleSize:5,
									posX: 10,
									posY: 20,
									addQuietZone: 1
								};
								$("#barcode_img_id").html('11');
								value = {code:value, rect: false};
								$("#barcode_img_id").show().barcode(value, btype, settings);
				}
				generateBarcode('<? echo $sys_id; ?>');
				</script>
	<? }else{ 
						$sql="SELECT a.order_id, a.item_id, a.dia, a.width_dia_type, a.gsm, a.color_id, a.yarn_lot, a.delivery_qty, a.delivery_pcs, a.carton_roll, a.remarks, a.collar_cuff, a.reject_qty, c.challan_no, c.process_id, c.vehical_no, c.delivery_date,c.delivery_no,c.party_id FROM subcon_delivery_dtls a, subcon_delivery_mst c WHERE c.id= '$update_id' AND c.company_id = '$company' AND a.process_id = 2 AND a.mst_id = c.ID GROUP BY a.order_id, a.item_id, a.dia, a.width_dia_type, a.gsm, a.color_id, a.yarn_lot, a.delivery_qty, a.delivery_pcs, a.carton_roll, a.remarks, a.collar_cuff, a.reject_qty, c.challan_no, c.process_id, c.vehical_no, c.delivery_date,c.delivery_no,c.party_id ";
						$dataArray=sql_select($sql);
						$com_dtls = fnc_company_location_address($company, $location, 2);
						?>
					<div style="width:1100px;">
						<table width="100%" cellpadding="0" cellspacing="0" >
							<tr>
								<td width="200" align="right">
									<img src='../../<? echo $com_dtls[2]; /*if($imge_arr[str_replace("'","",$company)]!="") echo $imge_arr[str_replace("'","",$company)]; else echo $com_dtls[2];*/ ?>' height='60%' width='60%' />
								</td>
								<td>
									<table width="800" cellspacing="0" align="center">
										<tr>
											<td align="center" style="font-size:40">
												<strong ><? echo $com_dtls[0]; ?></strong>
											</td>
										</tr>
										<tr class="form_caption">
											<td  align="center" style="font-size:18px">
												<?
													echo $com_dtls[1];
												?>
											</td>
										</tr>
										<tr>
											<td align="center" style="font-size:26px"><strong><? echo $ex_data[3]; ?> & Bill</strong></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
						<table width="100%" cellspacing="0" align="right" style="font-size:18px">
							<tr><td colspan="6" align="center"><hr></hr></td></tr>
							<tr>
									<?
									$party_add=$dataArray[0][csf('party_id')];
									$nameArray=sql_select( "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$party_add");
									foreach ($nameArray as $result)
									{
										$address="";
										if($result!="")  $address=$result[csf('address_1')];
									}
									?>
								<td width="100"><strong>Party</strong></td><td width="200">:<? echo $buyer_library[$party_add];  ?></td>
								<td width="100"><strong>Delivery No</strong></td><td width="200">:<? echo $dataArray[0][csf('delivery_no')] ; ?></td>
								<td width="100"><strong>Delivery Date</strong></td><td width="200">:<? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
							</tr>
							<tr>
								<td width="100" style="vertical-align:top; padding-top: 10px;"><strong>Address</strong> </td><td width="200" style="vertical-align:top ; padding-top: 10px;">:<? echo $address;  ?></td>
								<td width="100" style="vertical-align:top ; padding-top: 10px;"><strong>Challan No</strong></td><td style="vertical-align:top ; padding-top: 10px;">:<? echo $dataArray[0][csf('challan_no')]; ?></td>
								<? echo "<td style='vertical-align:top; padding-top:10px;' width='200' colspan='2' id='barcode_img_id'></td>";?>
							</tr>
							<tr>
								<td colspan="1"></td>
								
							</tr>
						</table>
							<br>
							<div style="width:120%; height:920px">
								<?
								$machine_array=array();
								$mac_sql="Select id, machine_no, dia_width, gauge from  lib_machine_name where status_active=1 and is_deleted=0";

								$result_mac_sql=sql_select($mac_sql);
								foreach($result_mac_sql as $row)
								{
									$machine_array[$row[csf('id')]]['machine_no']=$row[csf('machine_no')];
									$machine_array[$row[csf('id')]]['gauge']=$row[csf('gauge')];
									$machine_array[$row[csf('id')]]['dia_width']=$row[csf('dia_width')];
								}

								$size_array=array();
								$size_sql="Select b.id as dtls_id,c.product_size from  subcon_production_dtls b,subcon_pro_roll_details c where b.id=c.dtls_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.entry_form = 159";

								$result_size_sql=sql_select($size_sql);
								foreach($result_size_sql as $row)
								{
									$size_array[$row[csf('dtls_id')]]['product_size']=$row[csf('product_size')];
								}

									$prod_dia_array=array();
									$prod_dia_sql="select a.mst_id,a.order_id, a.cons_comp_id, a.fabric_description, a.gsm, a.dia_width, a.dia_width_type, a.color_id, a.yarn_lot,c.product_size,
									listagg((cast(a.machine_id as varchar2(4000))),',') within group (order by a.machine_id) as machine_id,
									listagg((cast(a.machine_gg as varchar2(4000))),',') within group (order by a.machine_gg) as machine_gg,
									listagg((cast(a.machine_dia as varchar2(4000))),',') within group (order by a.machine_dia) as machine_dias,
									listagg((cast(a.yrn_count_id as varchar2(4000))),',') within group (order by a.yrn_count_id) as yrn_count_id,
									listagg((cast(a.stitch_len as varchar2(4000))),',') within group (order by a.stitch_len) as stitch_len,
									listagg((cast(a.brand as varchar2(4000))),',') within group (order by a.brand) as brand,
									sum(a.product_qnty) as production_qnty from subcon_production_dtls a, subcon_pro_roll_details c where a.mst_id=c.mst_id and a.product_type=2 and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0  group by a.mst_id,a.order_id, a.cons_comp_id, a.fabric_description, a.gsm, a.dia_width, a.dia_width_type, a.color_id,a.yarn_lot,c.product_size";

								//echo $prod_dia_sql;
								$result_prod_dia_sql=sql_select($prod_dia_sql);
								foreach($result_prod_dia_sql as $row)
								{
									$machine_id=array_unique(explode(",",$row[csf('machine_id')]));
									$machine_name=""; $gauge_value=""; $machine_dia="";
									foreach($machine_id as $val)
									{
										if($machine_name=="") $machine_name=$machine_array[$val]['machine_no']; else $machine_name.=", ".$machine_array[$val]['machine_no'];
										if($gauge_value=="") $gauge_value=$machine_array[$val]['gauge']; else $gauge_value.=", ".$machine_array[$val]['gauge'];
										if($machine_dia=="") $machine_dia=$machine_array[$val]['dia_width']; else $machine_dia.=", ".$machine_array[$val]['dia_width'];
									}

									//if($row[csf('machine_dias')]=="") $machine_dia=$machine_dia; else $machine_dia=implode(",",array_filter(array_unique(explode(",",$row[csf('machine_dias')]))));
									if($row[csf('machine_dias')]=="") $machine_dia=$machine_dia; else $machine_dia=implode(",",array_unique(explode(",",$row[csf('machine_dias')])));
									if($row[csf('machine_gg')]=="") $gauge_value=$gauge_value; else $gauge_value=implode(",",array_filter(array_unique(explode(",",$row[csf('machine_gg')]))));

									$yarn_count=array_unique(explode(",",$row[csf('yrn_count_id')]));
									$count_name="";
									foreach($yarn_count as $val)
									{
										if($count_name=="") $count_name=$count_arr[$val]; else $count_name.=", ".$count_arr[$val];
									}

									$stitch_len_id=array_unique(explode(",",$row[csf('stitch_len')]));
									$stitch_len_name="";
									foreach($stitch_len_id as $val)
									{
										if($stitch_len_name=="") $stitch_len_name=$val; else $stitch_len_name.=", ".$val;
									}

									$brand=array_unique(explode(",",$row[csf('brand')]));
									$brand_name="";
									foreach($brand as $val)
									{
										if($brand_name=="") $brand_name=$val; else $brand_name.=$val.", ";
									}
									$order_idarr=array_unique(explode(",",$row[csf('order_id')]));
									foreach($order_idarr as $order_id)
									{
									if($machine_dia)
									{
										$prod_dia_array[$order_id][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('dia_width_type')]][$row[csf('color_id')]]['machine_dia']=$machine_dia;
									}
									$prod_dia_array[$order_id][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('dia_width_type')]][$row[csf('color_id')]]['fabric_description']=$row[csf('fabric_description')];
									$prod_dia_array[$order_id][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('dia_width_type')]][$row[csf('color_id')]]['gauge']=$gauge_value;
									$prod_dia_array[$order_id][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('dia_width_type')]][$row[csf('color_id')]]['yrn_count_id']=$count_name;
									$prod_dia_array[$order_id][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('dia_width_type')]][$row[csf('color_id')]]['stitch_len']=$stitch_len_name;
									$prod_dia_array[$order_id][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('dia_width_type')]][$row[csf('color_id')]]['brand_name']=$brand_name;
									}
								}
								$sql_kniting="select order_id, item_id, dia, width_dia_type, gsm, color_id, yarn_lot, delivery_qty, delivery_pcs, carton_roll, remarks, collar_cuff,reject_qty,bill_status from subcon_delivery_dtls where mst_id='$update_id' and process_id=2 ";
								$sql_kniting_result=sql_select($sql_kniting);
								?>
							<div>
								<table align="left" cellspacing="0" width="100%"  border="1" rules="all" class="rpt_table" style="margin-top: 10px;">

									<?
									$collar_cuff_arr=array();
									foreach($sql_kniting_result as $row)
									{
										if($row[csf('collar_cuff')]!="")
										{
											$collar_cuff_arr[]=$row[csf('collar_cuff')];
										}
										$collar_cuff_count=count($collar_cuff_arr);
									}
									$lib_item_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');
									$i=1; $z=1; $collar_cuff_span=0;
									foreach($sql_kniting_result as $row)
									{
										if($z==1)
										{
										?>
										<thead bgcolor="#dddddd" align="center">
											<th width="25">SL</th>
											<th width="65">Order No</th>
											<th width="70">Job No</th>
											<th width="60">Cust. Buyer </th>
											<th width="60">Yarn Count</th>
											<th width="50">Brand</th>
											<th width="40">Lot</th>
											<th width="30">Mc Dia/Gage</th>
											<th width="30">Finish Dia</th>
											<th width="50">Dia Type</th>
											<th width="30">Finish GSM</th>
											<th width="50">S/L</th>
											<th width="60">Colour</th>
											<th width="100">F/Description</th>
											<th width="40">Roll</th>
											<th width="50">Del. Qty.Kg</th>
											<th width="50">Del. Qty Pcs</th>
											<th width="50">Remark</th>
										</thead>
										<?
										$z++;
										}
										if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

										$fabric_description=$prod_dia_array[$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('color_id')]]['fabric_description'];
										$stitch_len=$prod_dia_array[$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('color_id')]]['stitch_len'];
										$gauge_val=$prod_dia_array[$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('color_id')]]['gauge'];
										$machine_dia_val=$prod_dia_array[$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('color_id')]]['machine_dia'];
										$yrn_count_val=$prod_dia_array[$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('color_id')]]['yrn_count_id'];
										$brand_name_val=$prod_dia_array[$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('color_id')]]['brand_name'];
										$machine_dia_uni=implode(",",array_unique(explode(", ",$machine_dia_val)));
										$gauge_val_uni=implode(",",array_unique(explode(", ",$gauge_val)));

										?>
										<tr bgcolor="<? echo $bgcolor; ?>">
											<td><? echo $i; ?></td>
											<td><p><? echo $job_po_array[$row[csf('order_id')]]['order_no']; ?></p></td>
											<td><p><? echo $job_po_array[$row[csf('order_id')]]['subcon_job']; ?></p></td>
											<td><p><? echo $job_po_array[$row[csf('order_id')]]['cust_buyer']; ?></p></td>
											<td><p><? echo $yrn_count_val; ?></p></td>
											<td><p><? echo $brand_name_val; ?></p></td>
											<td><div style="word-wrap:break-word; width:40px"><? echo $row[csf('yarn_lot')]; ?></div></td>
											<td><p><? echo $machine_dia_uni."x".$gauge_val_uni; ?></p></td>
											<td><p><? echo $row[csf('dia')]; ?></p></td>
											<td><p><? echo $fabric_typee[$row[csf('width_dia_type')]]; ?></p></td>
											<td><p><? echo $row[csf('gsm')]; ?></p></td>
											<td><p><? echo $stitch_len; ?></p></td>
											<td><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
											<td><p><? echo $fabric_description; ?></p></td>
											<td align="right"><? echo number_format($row[csf('carton_roll')],2,'.',''); ?></td>
											<td align="right"><? echo number_format($row[csf('delivery_qty')],2,'.',''); ?></td>
											<td align="right"><? echo number_format($row[csf('delivery_pcs')],2,'.',''); ?></td>
											<td><p><? echo $row[csf('remarks')]; ?></p></td>
											
										</tr>
										<?
										$grand_tot_roll+=$row[csf('carton_roll')];
										$grand_tot_finish_qty+=$row[csf('delivery_qty')];
										$grand_tot_delivery_pcs+=$row[csf('delivery_pcs')];
										$grand_tot_reject_qty+=$row[csf('reject_qty')];
										$total_amount+=$amount;
										if($row_show==0)
										{
											$collar_cuff_span=14;
										}
										else
										{
											$collar_cuff_span=13;
										}
										$i++;
										}
									?>
										<tfoot style="font-size:11px">
											<th colspan="<? echo $collar_cuff_span; ?>" align="right"><strong>Total</strong></th>
											<th align="right"><? echo number_format($grand_tot_roll,2,'.',''); ?></th>
											<th align="right"><? echo number_format($grand_tot_finish_qty,2,'.',''); ?></th>
											<th align="right"><? echo number_format($grand_tot_delivery_pcs,2,'.',''); ?></th>
										</tfoot>
								</table>
							</div>
							<?
								echo signature_table(64, $company, "930px")."<br>";
							?>
					</div>
					<script type="text/javascript" src="../../js/jquery.js"></script>
					<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
					<script>
							function generateBarcode( valuess )
							{
								
								var value = valuess;
								var btype = 'code39';
								var renderer ='bmp';
								var settings = {
								output:renderer,
								bgColor: '#FFFFFF',
								color: '#000000',
								barWidth: 1,
								barHeight: 30,
								moduleSize:5,
								posX: 10,
								posY: 20,
								addQuietZone: 1
								};
								$("#barcode_img_id").html('11');
								value = {code:value, rect: false};
								$("#barcode_img_id").show().barcode(value, btype, settings);
							}
							generateBarcode('<? echo $sys_id; ?>');
					</script>
						
			<? }?>
		</div>
			<div style="width:1100px;">
				<?
				//-------------------------------------------------------END-------------------------------------------------------
				//----------------------------Gate pass page start----------------------------------------------------------------
				
				$sql_gate_pass="SELECT a.id,a.party_id, a.challan_no,a.process_id,  a.delivery_date,  a.forwarder, a.transport_company, a.vehical_no,a.delivery_no,a.delivery_date, b.sent_by,b.get_pass_no,b.carried_by,b.vhicle_number,b.driver_license_no, b.challan_no,b.sys_number ,b.driver_name,b.mobile_no,c.item_category_id,b.remarks from subcon_delivery_mst a,inv_gate_pass_mst b, inv_gate_pass_dtls c where a.id = '$update_id' and b.id=c.mst_id and a.company_id = '$company' and a.status_active = 1  and a.is_deleted = 0 and a.delivery_no = b.challan_no";
				$dataArray[0][csf('sys_number')];
				$dataArray=sql_select($sql_gate_pass);
				foreach($dataArray as $result){
					$gate_pass_system_number=$result[csf('sys_number')];
				}
				if( count($dataArray)>0 && $bill_status_result==1)
				{
						?>
						
						<table width="900" cellspacing="0">
							<tr>
								<td colspan="6">
									<table width="100%" cellpadding="0" cellspacing="0" >
														<tr>
															<td width="100" align="right">
																<img src='../../<? echo $imge_arr[str_replace("'","",$company)]; ?>' height='90%' width='90%' />
															</td>
															<td>
																<table width="800" cellpadding="0" cellspacing="0" align="center" >
																	<tr>
																		<td align="center" style="font-size:36px"><strong ><? echo $company_library[$company]; ?></strong></td>
																	</tr>
																	<tr class="form_caption">
																		<td  align="center" style="font-size:22px">
																			<?
																				$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, contact_no, country_id, province, city, zip_code, contact_no, email, website, vat_number from lib_company where id=$company and status_active=1 and is_deleted=0");
																				foreach ($nameArray as $result)
																				{
																				?>
																					<? echo $result[csf('plot_no')]; ?> &nbsp;
																					<? echo $result[csf('level_no')]; ?> &nbsp;
																					<? echo $result[csf('road_no')]; ?> &nbsp;
																					<? echo $result[csf('block_no')];?> &nbsp;
																					<? echo $result[csf('city')]; ?> &nbsp;
																					<? echo $result[csf('contact_no')]; ?> &nbsp;
																					<? echo $result[csf('email')]; ?> &nbsp;
																					<? echo $result[csf('website')]; ?> <br>
																				 <?
																				}
																			?>
																		</td>
																	</tr>
																	<tr>
																		<td align="center" style="font-size:24px"><strong><? echo $production_process[$dataArray[0][csf('process_id')]]; ?> Job Work / Subcontract Gate Pass</strong></td>
																	</tr>
																</table>	
															</td>
														</tr>
													<table width="1000px" cellspacing="0" align="right" style="font-size:18px">
														<tr><td colspan="6" align="center"><hr></hr></td></tr>
														<tr style="height: 30px;">
																<?
																$party_add=$dataArray[0][csf('party_id')];
																$nameArray=sql_select( "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$party_add");
																foreach ($nameArray as $result)
																{
																	$address="";
																	if($result!="")  $address=$result[csf('address_1')];
																}
																?>
															<td width="100"><strong>Party Name</strong></td><td width="200">:<? echo $buyer_library[$party_add];  ?></td>
															<td width="100"></td>
															<td width="100"><strong>Gate Pass</strong></td><td width="200">: <? echo $dataArray[0][csf('sys_number')]; ?></td>
														</tr>
														<tr style="height: 40px;">
															<td width="100" colspan="1" style="vertical-align:top; padding-top: 10px;"><strong>Address</strong></td><td width="200" style="vertical-align:top; padding-top: 10px;">:<? echo $address;  ?></td>
															<td width="100" colspan="1" style="vertical-align:top; padding-top: 10px;"><strong></strong></td>
															<? echo "<td style='vertical-align:top; padding-top:20px;' width='200' colspan='2' id='barcode_img_id2'></td>";?>
															
														</tr>

														<tr style="height: 40px;">
															<td width="100"><strong>Challan No</strong></td><td width="200">:<? echo $dataArray[0][csf('challan_no')]; ?></td>
															<td width="100"></td>
															<td width="100"><strong>Driver Name</strong></td><td width="200"> :<? echo $dataArray[0][csf('driver_name')]; ?></td>
														</tr>
														<tr style="height: 30px;">
															<td width="100"><strong>Challan Date</strong></td><td width="200">:<? echo $dataArray[0][csf('delivery_date')]; ?></td>
															<td width="100"></td>
															<td width="100"><strong>Driver LC No</strong></td><td width="200">:<? echo $dataArray[0][csf('driver_license_no')]; ?></td>
														</tr> 
														<tr style="height: 30px;">
															<td width="100"><strong>Vehicle No</strong></td><td width="200">:<? echo $dataArray[0][csf('vhicle_number')]; ?></td>
															<td width="100"></td>
															<td width="100"><strong>Mobile No</strong></td><td width="200">:<? echo $dataArray[0][csf('mobile_no')]; ?></td>
														</tr>
														<tr style="height: 30px;">
															<td width="100"><strong>Receiver Name</strong></td><td width="200">:<? echo $dataArray[0][csf('carried_by')]; ?></td>
														</tr>
														<tr style="height: 30px;">
															<td width="100"><strong>Sent By</strong></td><td width="200">:<? echo $dataArray[0][csf('sent_by')]; ?></td>
														</tr>
														<tr>
															<td colspan="6" align="right" style="font-size:14px">
																<table cellspacing="0" width="350"  border="1" rules="all" class="rpt_table" style="font-size:18px">
																	<thead bgcolor="#dddddd" align="center">
																		<th width="150">Delivery Type</th>
																		<th width="150">Order No</th>
																		<th width="150">Roll</th>
																		<th width="150">Weight [KG]</th>
																		<th width="150">Remarks</th>
																	</thead> 
																	<tbody>
																		<tr>
																			<td align="center"><? echo $item_category[$dataArray[0][csf('item_category_id')]]; ?></td>
																			<td align="center"><? echo $job_po_array[$row[csf('order_id')]]['order_no']; ?></td>
																			<td align="center"><? echo $grand_tot_roll; ?></td>
																			<td align="center"><? echo $grand_tot_finish_qty; ?></td>
																			<td align="center"><? echo $dataArray[0][csf('remarks')]; ?></td>
																		</tr>
																	</tbody>
																</table>
															</td>
														</tr>
													</table>
											
									</table>
								</td>
							</tr>
						</table>
						<br><br><br>
						<table cellspacing="0" width="100%" >
							<thead>
								<tr><th colspan="9">&nbsp;</th></tr>
								<tr height="16px" style="font-size:14px">
									<th width="50">&nbsp;</th>
									<th width="100"><hr>Received By</th>
									<th width="50">&nbsp;</th>
									<th width="100"><hr>Prepared By</th>
									<th width="50">&nbsp;</th>
									<th width="100"><hr>Accounts</th>
									<th width="50">&nbsp;</th>
									<th width="100"><hr>Audit</th>
									<th width="50">&nbsp;</th>
									<th width="100"><hr>Authorised By</th>
									<th width="50">&nbsp;</th>
								</tr>
							</thead>
						</table>
						<?
				}
				?>
			</div>
	

	
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
		function generateBarcode( valuess )
		{
			//alert(valuess);
			var value = valuess;//$("#barcodeValue").val();
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();
			var settings = {
			  output:renderer,
			  bgColor: '#FFFFFF',
			  color: '#000000',
			  barWidth: 1,
			  barHeight: 30,
			  moduleSize:5,
			  posX: 10,
			  posY: 20,
			  addQuietZone: 1
			};
			$("#barcode_img_id2").html('11');
			 value = {code:value, rect: false};
			$("#barcode_img_id2").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $gate_pass_system_number; ?>');
	</script>
	<p style="page-break-after:always;"></p>
    <?
	exit();
}
if($action=="subcon_delivery_entry_print")
{
	extract($_REQUEST);
	$ex_data=explode('*',$data);
	$company=$ex_data[0];
	$location=$ex_data[5];
	$update_id=$ex_data[1];
	$sys_id=$ex_data[2];
	//print_r ($data);
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_library=return_library_array( "select id,supplier_name from lib_supplier", "id","supplier_name");
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$country_arr=return_library_array( "select id, country_name from lib_country", "id", "country_name");
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	//$item_arr=return_library_array( "select cons_comp_id, color_name from lib_color", "cons_comp_id", "color_name");

	//$color_arr=return_library_array( "select id, color_name from  lib_color", "id", "color_name"  );
	$sql_job_po="select a.id, a.order_no, a.cust_buyer, b.subcon_job, a.process_id from  subcon_ord_dtls a, subcon_ord_mst b where a.job_no_mst=b.subcon_job and b.is_deleted=0 and b.status_active=1 and a.status_active=1 and a.is_deleted=0";
	$job_po_array=array();
	$result_job_po=sql_select($sql_job_po);
	foreach($result_job_po as $row)
	{
		$job_po_array[$row[csf('id')]]['order_no']=$row[csf('order_no')];
		$job_po_array[$row[csf('id')]]['process_id']=$row[csf('process_id')];
		$job_po_array[$row[csf('id')]]['party_id']=$row[csf('party_id')];
		$job_po_array[$row[csf('id')]]['subcon_job']=$row[csf('subcon_job')];
		$job_po_array[$row[csf('id')]]['cust_buyer']=$row[csf('cust_buyer')];
	}
	//var_dump($job_po_array);
	$recChallan_arr=array();
	if($db_type==0)
	{
		$sql_rec="select b.order_id, group_concat(a.chalan_no) as chalan_no from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=1 group by b.order_id";
	}
	else if ($db_type==2)
	{
		$sql_rec="select b.order_id, listagg((cast(a.chalan_no as varchar2(4000))),',') within group (order by a.chalan_no) as chalan_no from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=1 group by b.order_id";
	}
	$result_sql_rec=sql_select($sql_rec);
	foreach($result_sql_rec as $row)
	{
		$chalan_no=array_unique(explode(",",$row[csf("chalan_no")]));
		$challan='';
		foreach($chalan_no as $val)
		{
			if($challan=="") $challan=$val; else $challan.=", ".$val;
		}

		$recChallan_arr[$row[csf('order_id')]]['chalan_no']=$challan;
	}

	$sql="select id, party_id, challan_no, process_id, delivery_date, forwarder, transport_company, vehical_no,delivery_no from subcon_delivery_mst where id='$update_id' and company_id='$company' and status_active=1 and is_deleted=0";

	$dataArray=sql_select($sql);

	$com_dtls = fnc_company_location_address($company, $location, 2);
?>
    <div style="width:930px;">
   	<table width="100%" cellpadding="0" cellspacing="0" >
       <tr>
           <td width="200" align="right">
               <img src='../../<? echo $com_dtls[2]; /*if($imge_arr[str_replace("'","",$company)]!="") echo $imge_arr[str_replace("'","",$company)]; else echo $com_dtls[2];*/ ?>' height='120%' width='120%' />
           </td>
           <td>
                <table width="800" cellspacing="0" align="center">
                    <tr>
                        <td align="center" style="font-size:x-large">
                        	<strong ><? echo $com_dtls[0]; ?></strong>
                        </td>
                    </tr>
                    <tr class="form_caption">
                        <td  align="center" style="font-size:14px">
                            <?
                            	echo $com_dtls[1];
                                /*$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, contact_no, country_id, province, city, zip_code, contact_no, email, website, vat_number from lib_company where id=$company and status_active=1 and is_deleted=0");
                                foreach ($nameArray as $result)
                                {
                                ?>
                                    <? echo $result[csf('plot_no')]; ?> &nbsp;
                                    <? echo $result[csf('level_no')]; ?> &nbsp;
                                    <? echo $result[csf('road_no')]; ?> &nbsp;
                                    <? echo $result[csf('block_no')];?> &nbsp;
                                    <? echo $result[csf('city')]; ?> &nbsp;
                                    <? echo $result[csf('contact_no')]; ?> &nbsp;
                                    <? echo $result[csf('email')]; ?> &nbsp;
                                    <? echo $result[csf('website')];?> <br>
                                   <b> Vat No : <? echo $result[csf('vat_number')]; ?></b> <?
                                }*/
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="font-size:18px"><strong><? echo $ex_data[3]; ?> Challan</strong></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <table width="900" cellspacing="0" align="right">
        <tr><td colspan="6" align="center"><hr></hr></td></tr>
        <tr>
        	<td colspan="2">&nbsp;</td>
        	<td><strong>Delivery No :</strong></td>
        	<td> <? echo $dataArray[0][csf('delivery_no')] ; ?> </td>
        	<td colspan="2">&nbsp;</td>
        </tr>
        <tr>
			<?
                $party_add=$dataArray[0][csf('party_id')];
                $nameArray=sql_select( "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$party_add");
                foreach ($nameArray as $result)
                {
                    $address="";
                    if($result!="")  $address=$result[csf('address_1')];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
                }
            ?>
        	<td width="300" rowspan="4" valign="top" colspan="2" style="font-size:12px"><strong>Party : <? echo $buyer_library[$party_add].'<br>'.$address;  ?></strong>


            </td>
            <td width="125" style="font-size:12px"><strong>Challan No :</strong></td><td width="170px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
            <td width="125" style="font-size:12px"><strong>Delivery Date :</strong></td><td width="170px"><? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
        </tr>
        <tr>
            <td style="font-size:12px"><strong>Transport Com.:</strong></td><td><? echo $dataArray[0][csf('transport_company')]; ?></td>
            <td style="font-size:12px"><strong>Forwarder:</strong></td><td><? echo $supplier_library[$dataArray[0][csf('forwarder')]]; ?></td>
        </tr>
        <tr>
            <td style="font-size:12px"><strong>Vehicle No:</strong></td><td><? echo $dataArray[0][csf('vehical_no')]; ?></td>
            <td style="font-size:12px"><strong>D.O No.</strong></td><td><? //echo $supplier_library[$dataArray[0][csf('forwarder')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Bar Code:</strong></td><td colspan="3" id="barcode_img_id"></td>
        </tr>
    </table>
    <br>
    <div style="width:120%; height:920px">
		<?
		$machine_array=array();
		$mac_sql="Select id, machine_no, dia_width, gauge from  lib_machine_name where status_active=1 and is_deleted=0";

		$result_mac_sql=sql_select($mac_sql);
        foreach($result_mac_sql as $row)
        {
			$machine_array[$row[csf('id')]]['machine_no']=$row[csf('machine_no')];
			$machine_array[$row[csf('id')]]['gauge']=$row[csf('gauge')];
			$machine_array[$row[csf('id')]]['dia_width']=$row[csf('dia_width')];
		}

		$size_array=array();
		 $size_sql="Select b.id as dtls_id,c.product_size from  subcon_production_dtls b,subcon_pro_roll_details c where b.id=c.dtls_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.entry_form = 159";

		$result_size_sql=sql_select($size_sql);
        foreach($result_size_sql as $row)
        {
			$size_array[$row[csf('dtls_id')]]['product_size']=$row[csf('product_size')];
		}

        $prod_dia_array=array();
            $prod_dia_sql="select a.mst_id,a.order_id, a.cons_comp_id, a.fabric_description, a.gsm, a.dia_width, a.dia_width_type, a.color_id, a.yarn_lot,c.product_size,
            listagg((cast(a.machine_id as varchar2(4000))),',') within group (order by a.machine_id) as machine_id,
			listagg((cast(a.machine_gg as varchar2(4000))),',') within group (order by a.machine_gg) as machine_gg,
            listagg((cast(a.machine_dia as varchar2(4000))),',') within group (order by a.machine_dia) as machine_dias,
            listagg((cast(a.yrn_count_id as varchar2(4000))),',') within group (order by a.yrn_count_id) as yrn_count_id,
            listagg((cast(a.stitch_len as varchar2(4000))),',') within group (order by a.stitch_len) as stitch_len,
			listagg((cast(a.brand as varchar2(4000))),',') within group (order by a.brand) as brand,
            sum(a.product_qnty) as production_qnty from subcon_production_dtls a, subcon_pro_roll_details c where a.mst_id=c.mst_id and a.product_type=2 and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0  group by a.mst_id,a.order_id, a.cons_comp_id, a.fabric_description, a.gsm, a.dia_width, a.dia_width_type, a.color_id,a.yarn_lot,c.product_size";

        //echo $prod_dia_sql;
        $result_prod_dia_sql=sql_select($prod_dia_sql);
        foreach($result_prod_dia_sql as $row)
        {
			$machine_id=array_unique(explode(",",$row[csf('machine_id')]));
			$machine_name=""; $gauge_value=""; $machine_dia="";
			foreach($machine_id as $val)
			{
				if($machine_name=="") $machine_name=$machine_array[$val]['machine_no']; else $machine_name.=", ".$machine_array[$val]['machine_no'];
				if($gauge_value=="") $gauge_value=$machine_array[$val]['gauge']; else $gauge_value.=", ".$machine_array[$val]['gauge'];
				if($machine_dia=="") $machine_dia=$machine_array[$val]['dia_width']; else $machine_dia.=", ".$machine_array[$val]['dia_width'];
			}

			//if($row[csf('machine_dias')]=="") $machine_dia=$machine_dia; else $machine_dia=implode(",",array_filter(array_unique(explode(",",$row[csf('machine_dias')]))));
			if($row[csf('machine_dias')]=="") $machine_dia=$machine_dia; else $machine_dia=implode(",",array_unique(explode(",",$row[csf('machine_dias')])));
			if($row[csf('machine_gg')]=="") $gauge_value=$gauge_value; else $gauge_value=implode(",",array_filter(array_unique(explode(",",$row[csf('machine_gg')]))));

			$yarn_count=array_unique(explode(",",$row[csf('yrn_count_id')]));
			$count_name="";
			foreach($yarn_count as $val)
			{
				if($count_name=="") $count_name=$count_arr[$val]; else $count_name.=", ".$count_arr[$val];
			}

			$stitch_len_id=array_unique(explode(",",$row[csf('stitch_len')]));
			$stitch_len_name="";
			foreach($stitch_len_id as $val)
			{
				if($stitch_len_name=="") $stitch_len_name=$val; else $stitch_len_name.=", ".$val;
			}

			$brand=array_unique(explode(",",$row[csf('brand')]));
			$brand_name="";
			foreach($brand as $val)
			{
				if($brand_name=="") $brand_name=$val; else $brand_name.=", ".$val;
			}
			$order_idarr=array_unique(explode(",",$row[csf('order_id')]));
			foreach($order_idarr as $order_id)
			{

           //and MST_ID = 129767
		   if($machine_dia)
		   {
		   $prod_dia_array[$order_id][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('dia_width_type')]][$row[csf('color_id')]]['machine_dia']=$machine_dia;
		  // echo $order_id.'='.$row[csf('cons_comp_id')].'='.$row[csf('gsm')].'='.$row[csf('dia_width')].'='.$row[csf('dia_width_type')].'='.$row[csf('dia_width_type')].'<br>';
		   }
		  // if($row[csf('mst_id')]==129767) echo  $order_id.'=A'.$row[csf('cons_comp_id')].'='.$row[csf('gsm')].'='.$row[csf('dia_width')].'='.$row[csf('dia_width_type')].'='.$machine_dia.'<br>';
		  // else echo " ";
			$prod_dia_array[$order_id][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('dia_width_type')]][$row[csf('color_id')]]['fabric_description']=$row[csf('fabric_description')];
			$prod_dia_array[$order_id][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('dia_width_type')]][$row[csf('color_id')]]['gauge']=$gauge_value;
            $prod_dia_array[$order_id][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('dia_width_type')]][$row[csf('color_id')]]['yrn_count_id']=$count_name;
			$prod_dia_array[$order_id][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('dia_width_type')]][$row[csf('color_id')]]['stitch_len']=$stitch_len_name;
			$prod_dia_array[$order_id][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('dia_width_type')]][$row[csf('color_id')]]['brand_name']=$brand_name;
			}
        }
        //var_dump($prod_dia_array);

        $mst_id=$dataArray[0][csf('id')];

        $sql_kniting="select order_id, item_id, dia, width_dia_type, gsm, color_id, yarn_lot, delivery_qty, delivery_pcs, carton_roll, remarks, collar_cuff,reject_qty from subcon_delivery_dtls where mst_id='$mst_id' and process_id=2 ";
        $sql_kniting_result=sql_select($sql_kniting);
		?>
        <div>
        <table align="left" cellspacing="0" width="1020"  border="1" rules="all" class="rpt_table" style="margin-top: 10px;">

			<?
			//die;
			$collar_cuff_arr=array();
			foreach($sql_kniting_result as $row)
            {
				if($row[csf('collar_cuff')]!="")
				{
					$collar_cuff_arr[]=$row[csf('collar_cuff')];
				}
				$collar_cuff_count=count($collar_cuff_arr);
			}
            $lib_item_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');
            $i=1; $z=1; $collar_cuff_span=0;
            foreach($sql_kniting_result as $row)
            {
				//echo $collar_cuff_cond;
				if($z==1)
				{
				?>
                <thead bgcolor="#dddddd" align="center">
                    <th width="25">SL</th>
                    <th width="65">Order No</th>
                    <th width="70">Job Number</th>
                    <th width="60">Cust. Buyer </th>
                    <th width="60">Count</th>
                    <th width="50">Brand</th>
                    <th width="40">Lot</th>
                    <th width="30">M/C Dia</th>
                    <th width="40">Gauge</th>
                    <th width="30">F.Dia</th>
                    <th width="50">Dia Type</th>
                    <th width="30">F.GSM</th>
                    <th width="50">S/L</th>
                    <th width="60">Color</th>
                    <th width="100">Description</th>
                    <?
					if($collar_cuff_count!=0)
					{
						$row_show=0;
					?>
                    <th width="50">Collar Cuff Measurement</th>
                    <?
					}
					else
					{
						$row_show=1;
					}
					?>
                    <th width="40">Roll /Bag</th>
                    <th width="50">Delivery Qty (KG)</th>
                    <th width="50">Delivery Qty (Pcs)</th>
                    <th width="50">Reject Qty</th>
                    <th width="50">Rec. Challan</th>
                    <th>Remarks</th>
                </thead>
                <?
				$z++;
				}
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				$fabric_description=$prod_dia_array[$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('color_id')]]['fabric_description'];
				$stitch_len=$prod_dia_array[$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('color_id')]]['stitch_len'];
				$gauge_val=$prod_dia_array[$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('color_id')]]['gauge'];
				$machine_dia_val=$prod_dia_array[$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('color_id')]]['machine_dia'];
			//	echo $row[csf('order_id')].'=B'.$row[csf('item_id')].'='.$row[csf('gsm')].'='.$row[csf('dia')].'='.$row[csf('width_dia_type')].'='.$machine_dia_val.'<br>';

				$yrn_count_val=$prod_dia_array[$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('color_id')]]['yrn_count_id'];
				$brand_name_val=$prod_dia_array[$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('color_id')]]['brand_name'];
				$machine_dia_uni=implode(",",array_unique(explode(", ",$machine_dia_val)));
				$gauge_val_uni=implode(",",array_unique(explode(", ",$gauge_val)));

                ?>

                <tr bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $i; ?></td>
                    <td><p><? echo $job_po_array[$row[csf('order_id')]]['order_no']; ?></p></td>
                    <td><p><? echo $job_po_array[$row[csf('order_id')]]['subcon_job']; ?></p></td>
                    <td><p><? echo $job_po_array[$row[csf('order_id')]]['cust_buyer']; ?></p></td>
                    <td><p><? echo $yrn_count_val; ?></p></td>
                    <td><p><? echo $brand_name_val; ?></p></td>
                    <td><div style="word-wrap:break-word; width:40px"><? echo $row[csf('yarn_lot')]; ?></div></td>
                    <td><p><? echo $machine_dia_uni; ?></p></td>
                    <td><p><? echo $gauge_val_uni; ?></p></td>
                    <td><p><? echo $row[csf('dia')]; ?></p></td>
                    <td><p><? echo $fabric_typee[$row[csf('width_dia_type')]]; ?></p></td>
                    <td><p><? echo $row[csf('gsm')]; ?></p></td>
                    <td><p><? echo $stitch_len; ?></p></td>
                    <td><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                    <td><p><? echo $fabric_description; ?></p></td>
                    <? if($row_show==0)
					{
					?>
                        <td><p><? echo $row[csf('collar_cuff')]; ?></p></td>
                    <?
					}
					?>
                    <td align="right"><? echo number_format($row[csf('carton_roll')],2,'.',''); ?></td>
                    <td align="right"><? echo number_format($row[csf('delivery_qty')],2,'.',''); ?></td>
                    <td align="right"><? echo number_format($row[csf('delivery_pcs')],2,'.',''); ?></td>
                    <td align="right"><? echo $row[csf('reject_qty')]; ?></td>
                    <td><p><? echo $recChallan_arr[$row[csf('order_id')]]['chalan_no']; ?></p></td>
                    <td><p><? echo $row[csf('remarks')]; ?></p></td>
                </tr>
                <?
                $grand_tot_roll+=$row[csf('carton_roll')];
                $grand_tot_finish_qty+=$row[csf('delivery_qty')];
                $grand_tot_delivery_pcs+=$row[csf('delivery_pcs')];
				$grand_tot_reject_qty+=$row[csf('reject_qty')];
				if($row_show==0)
				{
					$collar_cuff_span=15;
				}
				else
				{
					$collar_cuff_span=14;
				}
                $i++;
                }
            ?>
                <tfoot style="font-size:11px">
                    <th colspan="<? echo $collar_cuff_span; ?>" align="right"><strong>Total</strong></th>
					<th>&nbsp;</th>
                    <th align="right"><? echo number_format($grand_tot_roll,2,'.',''); ?></th>
                    <th align="right"><? echo number_format($grand_tot_finish_qty,2,'.',''); ?></th>
                    <th align="right"><? echo number_format($grand_tot_delivery_pcs,2,'.',''); ?></th>

					<th align="right">
					<?
                     if ($grand_tot_reject_qty !=""){

						echo $grand_tot_reject_qty;

					 }else {
                         echo " ";
					 }
					?>
					</th>
                    <th>&nbsp;</th>
                </tfoot>
            </table>
            </div>
           	<?
            echo signature_table(64, $company, "930px");

			if( $ex_data[4]==1)
			{
         ?>
          </div>
    	<table width="900" cellspacing="0" >
        	<tr><td colspan="6">


            </td></tr>
            <tr><td colspan="6" align="center">,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,</td></tr>
            <tr>
				<td colspan="6">
                    <table width="100%" cellpadding="0" cellspacing="0" >
                       <tr>
                           <td width="70" align="right">
                               <img src='../../<? echo $imge_arr[str_replace("'","",$company)]; ?>' height='70%' width='70%' />
                           </td>
                           <td>
                                <table width="800" cellspacing="0" align="center">
                                    <tr>
                                        <td align="center" style="font-size:x-large"><strong ><? echo $company_library[$company]; ?></strong></td>
                                    </tr>
                                    <tr class="form_caption">
                                        <td  align="center" style="font-size:14px">
                                            <?
												$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, contact_no, country_id, province, city, zip_code, contact_no, email, website, vat_number from lib_company where id=$company and status_active=1 and is_deleted=0");
                                                foreach ($nameArray as $result)
                                                {
                                                ?>
                                                    <? echo $result[csf('plot_no')]; ?> &nbsp;
                                                    <? echo $result[csf('level_no')]; ?> &nbsp;
                                                    <? echo $result[csf('road_no')]; ?> &nbsp;
                                                    <? echo $result[csf('block_no')];?> &nbsp;
                                                    <? echo $result[csf('city')]; ?> &nbsp;
                                                    <? echo $result[csf('contact_no')]; ?> &nbsp;
                                                    <? echo $result[csf('email')]; ?> &nbsp;
                                                    <? echo $result[csf('website')]; ?> <br>
                                                   <b> Vat No : <? echo $result[csf('vat_number')]; ?></b> <?
                                                }
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center" style="font-size:18px"><strong><? echo $production_process[$dataArray[0][csf('process_id')]]; ?> Gate Pass</strong></td>
                                    </tr>
                                </table>
                           </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="6" align="right" style="font-size:14px">
                    <table cellspacing="0" width="350"  border="1" rules="all" class="rpt_table" >
                        <thead bgcolor="#dddddd" align="center">
                            <th width="150">Roll</th>
                            <th width="150">Weight</th>
                        </thead>
                        <tbody>
                        	<tr>
                            	<td align="center"><? echo $grand_tot_roll; ?></td>
                               <td align="center"><? echo $grand_tot_finish_qty; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
       <table cellspacing="0" width="900" >
        	<thead>
            	<tr><th colspan="9">&nbsp;</th></tr>
            	<tr height="16px" style="font-size:14px">
                	<th width="50">&nbsp;</th>
                    <th width="100"><hr>Receive By</th>
                    <th width="50">&nbsp;</th>
                    <th width="100"><hr>Audited By</th>
                    <th width="50">&nbsp;</th>
                    <th width="100"><hr>Prepared By</th>
                    <th width="50">&nbsp;</th>
                    <th width="100"><hr>Gate Entry</th>
                    <th width="50">&nbsp;</th>
                </tr>
            </thead>
        </table>
	</div>
	<?
	}
	?>
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
		function generateBarcode( valuess )
		{
			//alert(valuess);
			var value = valuess;//$("#barcodeValue").val();
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();
			var settings = {
			  output:renderer,
			  bgColor: '#FFFFFF',
			  color: '#000000',
			  barWidth: 1,
			  barHeight: 30,
			  moduleSize:5,
			  posX: 10,
			  posY: 20,
			  addQuietZone: 1
			};
			$("#barcode_img_id").html('11');
			 value = {code:value, rect: false};
			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $sys_id; ?>');
	</script>
    <?
	exit();
}
 
if($action=="delivery_entry_without_gp2_print")
{
	extract($_REQUEST);
	$ex_data=explode('*',$data);
	$company=$ex_data[0];
	$location=$ex_data[5];
	$update_id=$ex_data[1];
	$sys_id=$ex_data[2];
	//print_r ($data);
	//$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_library=return_library_array( "select id,supplier_name from lib_supplier", "id","supplier_name");
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$country_arr=return_library_array( "select id, country_name from lib_country", "id", "country_name");
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	//$item_arr=return_library_array( "select cons_comp_id, color_name from lib_color", "cons_comp_id", "color_name");

	 
	$sql="select id, party_id, challan_no, process_id, delivery_date, forwarder, transport_company, vehical_no,delivery_no from subcon_delivery_mst where id='$update_id' and company_id='$company' and status_active=1 and is_deleted=0";

	$dataArray=sql_select($sql);
	$mst_id=$dataArray[0][csf('id')];

	$sql_job_po="select a.id, a.order_no, a.cust_buyer, b.subcon_job, a.process_id from  subcon_ord_dtls a, subcon_ord_mst b,subcon_delivery_dtls c where a.job_no_mst=b.subcon_job and c.order_id=b.order_id and c.mst_id=$mst_id and b.is_deleted=0 and b.status_active=1 and a.status_active=1 and a.is_deleted=0";
	$job_po_array=array();
	$result_job_po=sql_select($sql_job_po);
	foreach($result_job_po as $row)
	{
		$job_po_array[$row[csf('id')]]['order_no']=$row[csf('order_no')];
		$job_po_array[$row[csf('id')]]['process_id']=$row[csf('process_id')];
		$job_po_array[$row[csf('id')]]['party_id']=$row[csf('party_id')]; 
		$job_po_array[$row[csf('id')]]['subcon_job']=$row[csf('subcon_job')]; 
		$job_po_array[$row[csf('id')]]['cust_buyer']=$row[csf('cust_buyer')];
		$poId_array[$row[csf('id')]]=$row[csf('id')]; 
	}
	//var_dump($job_po_array);
	$recChallan_arr=array();
	if($db_type==0)
	{
		$sql_rec="select b.order_id, group_concat(a.chalan_no) as chalan_no from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=1 group by b.order_id";
	}
	else if ($db_type==2)
	{
		$sql_rec="select b.order_id,  a.chalan_no  as chalan_no from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and b.order_id in(".implode(",",$poId_array).") and a.trans_type=1";
	}
	$result_sql_rec=sql_select($sql_rec);
	foreach($result_sql_rec as $row)
	{
		$chalan_no=array_unique(explode(",",$row[csf("chalan_no")]));
		$challan='';
		foreach($chalan_no as $val)
		{
			if($challan=="") $challan=$val; else $challan.=", ".$val;
		}
		$recChallan_arr[$row[csf('order_id')]]['chalan_no'].=$row[csf("chalan_no")].',';
	}

	$com_dtls = fnc_company_location_address($company, $location, 2);
?>
    <div style="width:930px;">
   	<table width="100%" cellpadding="0" cellspacing="0" >
       <tr>
           <td width="200" align="right">
               <img src='../../<?=$com_dtls[2]; ?>' height='40%' width='70%' />
           </td>
           <td>
                <table width="800" cellspacing="0" align="center">
                    <tr>
                        <td align="center" style="font-size:20px"><strong><?=$com_dtls[0]; ?></strong></td>
                    </tr>
                    <tr class="form_caption">
                        <td align="center" style="font-size:14px"><?=$com_dtls[1]; ?></td>
                    </tr>
                    <tr>
                        <td align="center" style="font-size:18px"><strong><?=$ex_data[3]; ?> Challan</strong></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <table width="900" cellspacing="0" align="right">
        <tr><td colspan="6" align="center"><hr></hr></td></tr>
        <tr>
        	<td colspan="2">&nbsp;</td>
        	<td><strong>Delivery No :</strong></td>
        	<td><?=$dataArray[0][csf('delivery_no')] ; ?> </td>
        	<td colspan="2">&nbsp;</td>
        </tr>
        <tr>
			<?
                $party_add=$dataArray[0][csf('party_id')];
                $nameArray=sql_select( "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$party_add");
                foreach ($nameArray as $result)
                {
                    $address="";
                    if($result!="") $address=$result[csf('address_1')];
                }
            ?>
        	<td width="300" rowspan="4" valign="top" colspan="2" style="font-size:12px"><strong>Party :<?=$buyer_library[$party_add].'<br>'.$address; ?></strong></td>
            <td width="125" style="font-size:12px"><strong>Challan No :</strong></td><td width="170px"><?=$dataArray[0][csf('challan_no')]; ?></td>
            <td width="125" style="font-size:12px"><strong>Delivery Date :</strong></td><td width="170px"><?=change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
        </tr>
        <tr>
            <td style="font-size:12px"><strong>Transport Com.:</strong></td><td><?=$dataArray[0][csf('transport_company')]; ?></td>
            <td style="font-size:12px"><strong>Forwarder:</strong></td><td><?=$supplier_library[$dataArray[0][csf('forwarder')]]; ?></td>
        </tr>
        <tr>
            <td style="font-size:12px"><strong>Vehicle No:</strong></td><td><?=$dataArray[0][csf('vehical_no')]; ?></td>
            <td style="font-size:12px"><strong>D.O No.</strong></td><td><? //=$supplier_library[$dataArray[0][csf('forwarder')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Bar Code:</strong></td><td colspan="3" id="barcode_img_id"></td>
        </tr>
    </table>
    <br>
    <div style="width:1080px">
		<?
		$machine_array=array();
		$mac_sql="Select id, machine_no, dia_width, gauge from  lib_machine_name where status_active=1 and is_deleted=0";

		$result_mac_sql=sql_select($mac_sql);
        foreach($result_mac_sql as $row)
        {
			$machine_array[$row[csf('id')]]['machine_no']=$row[csf('machine_no')];
			$machine_array[$row[csf('id')]]['gauge']=$row[csf('gauge')];
			$machine_array[$row[csf('id')]]['dia_width']=$row[csf('dia_width')];
		}

        $prod_dia_array=array();
        if($db_type==2)
        {
            $prod_dia_sql="select order_id, cons_comp_id, fabric_description, gsm, dia_width, dia_width_type, color_id, yarn_lot,
            listagg((cast(machine_id as varchar2(4000))),',') within group (order by machine_id) as machine_id,
			listagg((cast(machine_gg as varchar2(4000))),',') within group (order by machine_gg) as machine_gg,
            listagg((cast(machine_dia as varchar2(4000))),',') within group (order by machine_dia) as machine_dias,
            listagg((cast(yrn_count_id as varchar2(4000))),',') within group (order by yrn_count_id) as yrn_count_id,
            listagg((cast(stitch_len as varchar2(4000))),',') within group (order by stitch_len) as stitch_len,
			listagg((cast(brand as varchar2(4000))),',') within group (order by brand) as brand,
            sum(product_qnty) as production_qnty from subcon_production_dtls where product_type=2 and status_active=1 and order_id in(".implode(",",$poId_array).") and is_deleted=0 group by order_id, cons_comp_id, fabric_description, gsm, dia_width, dia_width_type, color_id,yarn_lot";
        }
        else if($db_type==0)
        {
            $prod_dia_sql="select order_id, cons_comp_id, fabric_description, gsm, dia_width, dia_width_type, color_id,yarn_lot,
            group_concat(machine_id) as machine_id,  group_concat(machine_gg) as machine_gg, group_concat(machine_dia) as machine_dias,
            group_concat(yrn_count_id) as yrn_count_id,
            group_concat(stitch_len) as stitch_len,
			group_concat(brand) as brand,
            sum(product_qnty) as production_qnty from subcon_production_dtls where product_type=2 and status_active=1 and is_deleted=0 group by order_id, cons_comp_id, fabric_description, gsm, dia_width, dia_width_type, color_id, yarn_lot";
        }
        //echo $prod_dia_sql;
        $result_prod_dia_sql=sql_select($prod_dia_sql);
        foreach($result_prod_dia_sql as $row)
        {
			$machine_id=array_unique(explode(",",$row[csf('machine_id')]));
			$machine_name=""; $gauge_value=""; $machine_dia="";
			foreach($machine_id as $val)
			{
				if($machine_name=="") $machine_name=$machine_array[$val]['machine_no']; else $machine_name.=", ".$machine_array[$val]['machine_no'];
				if($gauge_value=="") $gauge_value=$machine_array[$val]['gauge']; else $gauge_value.=", ".$machine_array[$val]['gauge'];
				if($machine_dia=="") $machine_dia=$machine_array[$val]['dia_width']; else $machine_dia.=", ".$machine_array[$val]['dia_width'];
			}

			if($row[csf('machine_dias')]=="") $machine_dia=$machine_dia; else $machine_dia=implode(",",array_filter(array_unique(explode(",",$row[csf('machine_dias')]))));
			if($row[csf('machine_gg')]=="") $gauge_value=$gauge_value; else $gauge_value=implode(",",array_filter(array_unique(explode(",",$row[csf('machine_gg')]))));

			$yarn_count=array_unique(explode(",",$row[csf('yrn_count_id')]));
			$count_name="";
			foreach($yarn_count as $val)
			{
				if($count_name=="") $count_name=$count_arr[$val]; else $count_name.=", ".$count_arr[$val];
			}

			$stitch_len_id=array_unique(explode(",",$row[csf('stitch_len')]));
			$stitch_len_name="";
			foreach($stitch_len_id as $val)
			{
				if($stitch_len_name=="") $stitch_len_name=$val; else $stitch_len_name.=", ".$val;
			}

			$brand=array_unique(explode(",",$row[csf('brand')]));
			$brand_name="";
			foreach($brand as $val)
			{
				if($brand_name=="") $brand_name=$val; else $brand_name.=", ".$val;
			}

            $prod_dia_array[$row[csf('order_id')]][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('dia_width_type')]][$row[csf('color_id')]]['machine_dia']=$machine_dia;
			$prod_dia_array[$row[csf('order_id')]][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('dia_width_type')]][$row[csf('color_id')]]['fabric_description']=$row[csf('fabric_description')];
			$prod_dia_array[$row[csf('order_id')]][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('dia_width_type')]][$row[csf('color_id')]]['gauge']=$gauge_value;
            $prod_dia_array[$row[csf('order_id')]][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('dia_width_type')]][$row[csf('color_id')]]['yrn_count_id']=$count_name;
			$prod_dia_array[$row[csf('order_id')]][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('dia_width_type')]][$row[csf('color_id')]]['stitch_len']=$stitch_len_name;
			$prod_dia_array[$row[csf('order_id')]][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('dia_width_type')]][$row[csf('color_id')]]['brand_name']=$brand_name;
        }
        //var_dump($prod_dia_array);

       // $mst_id=$dataArray[0][csf('id')];

        $sql_kniting="select order_id, item_id, dia, width_dia_type, gsm, color_id, yarn_lot, delivery_qty, delivery_pcs, carton_roll, remarks, collar_cuff, reject_qty, moisture_gain from subcon_delivery_dtls where mst_id='$mst_id' and process_id=2 ";
        $sql_kniting_result=sql_select($sql_kniting);
		?>
        <div>
        <table align="left" cellspacing="0" width="1120"  border="1" rules="all" class="rpt_table" style="margin-top: 10px;">
			<?
			//die;
			$collar_cuff_arr=array();
			foreach($sql_kniting_result as $row)
            {
				if($row[csf('collar_cuff')]!="")
				{
					$collar_cuff_arr[]=$row[csf('collar_cuff')];
				}
				$collar_cuff_count=count($collar_cuff_arr);
			}
            $lib_item_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');
            $i=1; $z=1; $collar_cuff_span=0;
            foreach($sql_kniting_result as $row)
            {
				if($z==1)
				{
					?>
					<thead bgcolor="#dddddd" align="center" style="font-size:12px">
						<th width="25">SL</th>
						<th width="65">Order No</th>
						<th width="70">Job Number</th>
						<th width="60">Cust. Buyer </th>
						<th width="60">Count</th>
						<th width="50">Brand</th>
						<th width="40">Lot</th>
						<th width="30">M/C Dia</th>
						<th width="40">Gauge</th>
						<th width="30">F.Dia</th>
						<th width="50">Dia Type</th>
						<th width="30">F.GSM</th>
						<th width="50">S/L</th>
						<th width="60">Color</th>
						<th width="100">Description</th>
						<?
						if($collar_cuff_count!=0)
						{
							$row_show=0;
							?><th width="50">Collar Cuff Measurement</th><?
						}
						else $row_show=1;
						?>
						<th width="40">Roll /Bag</th>
						<th width="50">Delivery Qty (KG)</th>
						<th width="50">Delivery Qty (Pcs)</th>
						<th width="50">Reject Qty</th>
                        <th width="50">Mos. Gain Qty (Kg)</th>
                        <th width="50">Ttl. With Gain (Kg)</th>
						<th width="50">Rec. Challan</th>
						<th>Remarks</th>
					</thead>
					<?
					$z++;
					}
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

					$fabric_description=$prod_dia_array[$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('color_id')]]['fabric_description'];
					$stitch_len=$prod_dia_array[$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('color_id')]]['stitch_len'];
					$gauge_val=$prod_dia_array[$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('color_id')]]['gauge'];
					$machine_dia_val=$prod_dia_array[$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('color_id')]]['machine_dia'];
					$yrn_count_val=$prod_dia_array[$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('color_id')]]['yrn_count_id'];
					$brand_name_val=$prod_dia_array[$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('color_id')]]['brand_name'];
					$machine_dia_uni=implode(",",array_unique(explode(", ",$machine_dia_val)));
					$gauge_val_uni=implode(",",array_unique(explode(", ",$gauge_val)));
					$rowgain=0;
					$rowgain=$row[csf('delivery_qty')]+$row[csf('moisture_gain')];
					?>
					<tr bgcolor="<?=$bgcolor; ?>" style="font-size:12px">
						<td align="center"><?=$i; ?></td>
						<td style="word-break:break-all;"><?=$job_po_array[$row[csf('order_id')]]['order_no']; ?></td>
						<td style=""><?=$job_po_array[$row[csf('order_id')]]['subcon_job']; ?></td>
						<td style="word-break:break-all;"><?=$job_po_array[$row[csf('order_id')]]['cust_buyer']; ?></td>
						<td style="word-break:break-all;"><?=$yrn_count_val; ?></td>
						<td style="word-break:break-all;"><?=$brand_name_val; ?></td>
						<td style="word-break:break-all;"><?=$row[csf('yarn_lot')]; ?></td>
						<td style="word-break:break-all;"><?=$machine_dia_uni; ?></td>
						<td style="word-break:break-all;"><?=$gauge_val_uni; ?></td>
						<td style="word-break:break-all;"><?=$row[csf('dia')]; ?></td>
						<td style="word-break:break-all;"><?=$fabric_typee[$row[csf('width_dia_type')]]; ?></td>
						<td style="word-break:break-all;"><?=$row[csf('gsm')]; ?></td>
						<td style="word-break:break-all;"><?=$stitch_len; ?></td>
						<td style="word-break:break-all;"><?=$color_arr[$row[csf('color_id')]]; ?></td>
						<td style="word-break:break-all;"><?=$fabric_description; ?></td>
						<? if($row_show==0)
						{
							?><td style="word-break:break-all;"><?=$row[csf('collar_cuff')]; ?></td><?
						}
						?>
						<td style="word-break:break-all;" align="right"><?=number_format($row[csf('carton_roll')],2,'.',''); ?></td>
						<td style="word-break:break-all;" align="right"><?=number_format($row[csf('delivery_qty')],2,'.',''); ?></td>
						<td style="word-break:break-all;" align="right"><?=number_format($row[csf('delivery_pcs')],2,'.',''); ?></td>
						<td style="word-break:break-all;" align="right"><?=$row[csf('reject_qty')]; ?></td>
                        <td style="word-break:break-all;" align="right"><?=number_format($row[csf('moisture_gain')],2,'.',''); ?></td>
                        <td style="word-break:break-all;" align="right"><?=number_format($rowgain,2,'.',''); ?></td>
						<td style="word-break:break-all;"><?=rtrim($recChallan_arr[$row[csf('order_id')]]['chalan_no'],','); ?></td>
						<td style="word-break:break-all;"><?=$row[csf('remarks')]; ?></td>
					</tr>
					<?
					$grand_tot_roll+=$row[csf('carton_roll')];
					$grand_tot_finish_qty+=$row[csf('delivery_qty')];
					$grand_tot_delivery_pcs+=$row[csf('delivery_pcs')];
					$grand_tot_reject_qty+=$row[csf('reject_qty')];
					$grand_moisture_gain+=$row[csf('moisture_gain')];
					$grand_tot_moisture_gain+=$rowgain;
					if($row_show==0) $collar_cuff_span=15; else $collar_cuff_span=14;
					$i++;
                }
            	?>
                <tfoot style="font-size:11px">
                    <th colspan="<?=$collar_cuff_span; ?>" align="right"><strong>Total</strong></th>
                    <th align="right"><?=number_format($grand_tot_roll,2,'.',''); ?></th>
                    <th align="right"><?=number_format($grand_tot_finish_qty,2,'.',''); ?></th>
                    <th align="right"><?=number_format($grand_tot_delivery_pcs,2,'.',''); ?></th>
                    <th align="right"><?=$grand_tot_reject_qty; ?></th>
                    <th align="right"><?=number_format($grand_moisture_gain,2,'.',''); ?></th>
                    <th align="right"><?=number_format($grand_tot_moisture_gain,2,'.',''); ?></th>
                    <th>&nbsp;</th>
                </tfoot>
            </table>
            </div> <br>
           	<?=signature_table(64, $company, "1080px"); ?>
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
		function generateBarcode( valuess )
		{
			//alert(valuess);
			var value = valuess;//$("#barcodeValue").val();
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();
			var settings = {
			  output:renderer,
			  bgColor: '#FFFFFF',
			  color: '#000000',
			  barWidth: 1,
			  barHeight: 30,
			  moduleSize:5,
			  posX: 10,
			  posY: 20,
			  addQuietZone: 1
			};
			$("#barcode_img_id").html('11');
			 value = {code:value, rect: false};
			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<?=$sys_id; ?>');
	</script>
    <?
	exit();
}

if($action=="knitting_delivery_print")
{
	extract($_REQUEST);
	$ex_data=explode('*',$data);
	$company=$ex_data[0];
	$location=$ex_data[5];
	$update_id=$ex_data[1];
	$sys_id=$ex_data[2];
	//print_r ($data);
	//$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library",'master_tble_id','image_location');
	//$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_library=return_library_array( "select id,supplier_name from lib_supplier", "id","supplier_name");
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$country_arr=return_library_array( "select id, country_name from lib_country", "id", "country_name");
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$location_library=return_library_array( "select id, location_name from lib_location",'id','location_name');

	//$item_arr=return_library_array( "select cons_comp_id, color_name from lib_color", "cons_comp_id", "color_name");
	//$color_arr=return_library_array( "select id, color_name from  lib_color", "id", "color_name"  );
	 

	$sql="select id, party_id,location_id, challan_no, process_id, delivery_date, forwarder, transport_company, vehical_no,delivery_no from subcon_delivery_mst where id='$update_id' and company_id='$company' and status_active=1 and is_deleted=0";

	$dataArray=sql_select($sql);
	$mst_id=$dataArray[0][csf('id')];

	$sql_job_po="select a.id, a.order_no, a.cust_buyer, b.subcon_job, a.process_id from  subcon_ord_dtls a, subcon_ord_mst b,subcon_delivery_dtls c where a.job_no_mst=b.subcon_job and c.order_id=b.order_id and c.mst_id=$mst_id and b.is_deleted=0 and b.status_active=1 and a.status_active=1 and a.is_deleted=0";
	$job_po_array=array();
	$result_job_po=sql_select($sql_job_po);
	foreach($result_job_po as $row)
	{
		$job_po_array[$row[csf('id')]]['order_no']=$row[csf('order_no')];
		$job_po_array[$row[csf('id')]]['process_id']=$row[csf('process_id')];
		$job_po_array[$row[csf('id')]]['party_id']=$row[csf('party_id')]; 
		$job_po_array[$row[csf('id')]]['subcon_job']=$row[csf('subcon_job')]; 
		$job_po_array[$row[csf('id')]]['cust_buyer']=$row[csf('cust_buyer')];
		$poId_array[$row[csf('id')]]=$row[csf('id')]; 
	}
	//var_dump($job_po_array);
	$recChallan_arr=array();
	if($db_type==0)
	{
		$sql_rec="select b.order_id, group_concat(a.chalan_no) as chalan_no from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=1 group by b.order_id";
	}
	else if ($db_type==2)
	{
		$sql_rec="select b.order_id,  a.chalan_no  as chalan_no from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and b.order_id in(".implode(",",$poId_array).") and a.trans_type=1";
	}
	$result_sql_rec=sql_select($sql_rec);
	foreach($result_sql_rec as $row)
	{
		$chalan_no=array_unique(explode(",",$row[csf("chalan_no")]));
		$challan='';
		foreach($chalan_no as $val)
		{
			if($challan=="") $challan=$val; else $challan.=", ".$val;
		}
		$recChallan_arr[$row[csf('order_id')]]['chalan_no'].=$row[csf("chalan_no")].',';
	}

	$com_dtls = fnc_company_location_address($company, $location, 2);
	$k=0;
	$copy_no=array(1,2,3); //for Dynamic Copy here
	foreach($copy_no as $cid)
	{
		if ($cid!=1) $margin_cond="style='margin-top: 70px;'";
		else $margin_cond='';
		$k++;
		?>
    <div style="width:930px;">
	   		<table width="900" cellpadding="0" cellspacing="0">
	       <tr>
	           <td rowspan="4" align="right">
	               <img  src='../../<? echo $com_dtls[2]; ?>' height='100' width='200'/>
	           </td>
	           <td align="center" width="500" style="font-size:x-large" align="center"><strong ><? echo $com_dtls[0]; ?></strong></td>
	           <td rowspan="4" width="200" align="right">
	               <?
					if($k==1){
					echo "<b><h2>1<sup>st</sup> Copy</h2></b>";
					}
					else if($k==2){
					echo "<b><h2>2<sup>nd</sup> Copy</h2></b>";
					}
					else if($k==3){
					echo "<b><h2>3<sup>rd</sup> Copy</h2></b>";
					}
					?>
	           </td>
	        </tr>
	        <tr>
	        	<td align="center" style="font-size:14px;"><strong >( <? echo $location_library[$dataArray[0][csf('location_id')]]; ?> )</strong></td>
	        </tr>
	        <tr>
	        	<td align="center"><? echo $com_dtls[1]; ?></td>
	        </tr>
	        <tr>
	        	<td align="center"><strong><? echo $ex_data[3]; ?> Challan</strong></td>
	        </tr>
	    </table>
	    <table width="900" cellspacing="0" align="right">
	        <tr><td colspan="6" align="center"><hr></hr></td></tr>
	        <tr>
	        	<td colspan="2">&nbsp;</td>
	        	<td> <strong>Delivery No :</strong></td>
	        	<td> <? echo $dataArray[0][csf('delivery_no')] ; ?> </td>
	        	<td colspan="2">&nbsp;</td>
	        </tr>
	        <tr>
				<?
	                $party_add=$dataArray[0][csf('party_id')];
	                $nameArray=sql_select( "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$party_add");
	                foreach ($nameArray as $result)
	                {
	                    $address="";
	                    if($result!="") $address=$result[csf('address_1')];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
	                }
	            ?>
	        	<td width="300" rowspan="4" valign="top" colspan="2"><strong>Party : <? echo $buyer_library[$party_add].'<br>'.$address;  ?></strong></td>
	            <td width="125" ><strong>Challan No :</strong></td><td width="170px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
	            <td width="125"><strong>Delivery Date :</strong></td><td width="170px"><? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
	        </tr>
	        <tr>
	            <td><strong>Transport Com.:</strong></td><td><? echo $dataArray[0][csf('transport_company')]; ?></td>
	            <td><strong>Forwarder:</strong></td><td><? echo $supplier_library[$dataArray[0][csf('forwarder')]]; ?></td>
	        </tr>
	        <tr>
	            <td><strong>Vehicle No:</strong></td><td><? echo $dataArray[0][csf('vehical_no')]; ?></td>
	            <td><strong>D.O No.</strong></td><td><? //echo $supplier_library[$dataArray[0][csf('forwarder')]]; ?></td>
	        </tr>
	        <tr>
	            <td>&nbsp;</td><td colspan="3" id="barcode_img_id_<? echo $cid;?>"></td>
	        </tr>
	    </table>
    	<br>
    	<div style="width:100%;">
			<?
			$machine_array=array();
			$mac_sql="Select id, machine_no, dia_width, gauge from  lib_machine_name where status_active=1 and is_deleted=0";

			$result_mac_sql=sql_select($mac_sql);
	        foreach($result_mac_sql as $row)
	        {
				$machine_array[$row[csf('id')]]['machine_no']=$row[csf('machine_no')];
				$machine_array[$row[csf('id')]]['gauge']=$row[csf('gauge')];
				$machine_array[$row[csf('id')]]['dia_width']=$row[csf('dia_width')];
			}
			    $mst_id=$dataArray[0][csf('id')];
			   $sql_kniting="select order_id, item_id, dia, width_dia_type, gsm, color_id, yarn_lot, delivery_qty, carton_roll, remarks, collar_cuff from subcon_delivery_dtls where mst_id='$mst_id' and process_id=2 ";
	        $sql_kniting_result=sql_select($sql_kniting);
			 foreach($sql_kniting_result as $row)
	        {
				$order_idArr[$row[csf('order_id')]]=$row[csf('order_id')];
			}
			$order_ids=implode(",",$order_idArr);
			//echo $order_ids.'d';

	        $prod_dia_array=array();
	        if($db_type==2)
	        {
	            $prod_dia_sql="select order_id, cons_comp_id, fabric_description, gsm, dia_width, dia_width_type, color_id, yarn_lot,
	            listagg((cast(machine_id as varchar2(4000))),',') within group (order by machine_id) as machine_id,
	            listagg((cast(machine_gg as varchar2(4000))),',') within group (order by machine_gg) as machine_gg,
	            listagg((cast(machine_dia as varchar2(4000))),',') within group (order by machine_dia) as machine_dias,
	            listagg((cast(yrn_count_id as varchar2(4000))),',') within group (order by yrn_count_id) as yrn_count_id,
	            listagg((cast(stitch_len as varchar2(4000))),',') within group (order by stitch_len) as stitch_len,
				listagg((cast(brand as varchar2(4000))),',') within group (order by brand) as brand,
	            sum(product_qnty) as production_qnty from subcon_production_dtls where product_type=2 and status_active=1 and is_deleted=0 and order_id in($order_ids)  group by order_id, cons_comp_id, fabric_description, gsm, dia_width, dia_width_type, color_id,yarn_lot";
	        }
	        else if($db_type==0)
	        {
	            $prod_dia_sql="select order_id, cons_comp_id, fabric_description, gsm, dia_width, dia_width_type, color_id,yarn_lot,
	            group_concat(machine_id) as machine_id,group_concat(machine_gg) as machine_gg,group_concat(machine_dia) as machine_dias,
	            group_concat(yrn_count_id) as yrn_count_id,
	            group_concat(stitch_len) as stitch_len,
				group_concat(brand) as brand,
	            sum(product_qnty) as production_qnty from subcon_production_dtls where product_type=2 and status_active=1 and is_deleted=0 and order_id in($order_ids)  group by order_id, cons_comp_id, fabric_description, gsm, dia_width, dia_width_type, color_id, yarn_lot";
	        }
	         //echo $prod_dia_sql;
	        $result_prod_dia_sql=sql_select($prod_dia_sql);
	        foreach($result_prod_dia_sql as $row)
	        {
				$machine_id=array_unique(explode(",",$row[csf('machine_id')]));
				$machine_name=""; $gauge_value=""; $machine_dia="";
				foreach($machine_id as $val)
				{
					if($machine_name=="") $machine_name=$machine_array[$val]['machine_no']; else $machine_name.=", ".$machine_array[$val]['machine_no'];
					if($gauge_value=="") $gauge_value=$machine_array[$val]['gauge']; else $gauge_value.=", ".$machine_array[$val]['gauge'];
					if($machine_dia=="") $machine_dia=$machine_array[$val]['dia_width']; else $machine_dia.=", ".$machine_array[$val]['dia_width'];
				}
				if($row[csf('machine_dias')]=="") $machine_dia=$machine_dia; else $machine_dia=implode(",",array_filter(array_unique(explode(",",$row[csf('machine_dias')]))));
				if($row[csf('machine_gg')]=="") $gauge_value=$gauge_value; else $gauge_value=implode(",",array_filter(array_unique(explode(",",$row[csf('machine_gg')]))));
				$yarn_countArr=array_unique(explode(",",$row[csf('yrn_count_id')]));
				$count_name="";
				foreach($yarn_countArr as $val)
				{
					if($count_name=="") $count_name=$count_arr[$val]; else $count_name.=", ".$count_arr[$val];
				}
			//	echo $count_name.'D';
				$stitch_len_id=array_unique(explode(",",$row[csf('stitch_len')]));
				$stitch_len_name="";
				foreach($stitch_len_id as $val)
				{
					if($stitch_len_name=="") $stitch_len_name=$val; else $stitch_len_name.=", ".$val;
				}

				$brand=array_unique(explode(",",$row[csf('brand')]));
				$brand_name="";
				foreach($brand as $val)
				{
					if($brand_name=="") $brand_name=$val; else $brand_name.=", ".$val;
				}

	            $prod_dia_array[$row[csf('order_id')]][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('dia_width_type')]][$row[csf('color_id')]]['machine_dia']=$machine_dia;
				$prod_dia_array[$row[csf('order_id')]][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('dia_width_type')]][$row[csf('color_id')]]['fabric_description']=$row[csf('fabric_description')];
				$prod_dia_array[$row[csf('order_id')]][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('dia_width_type')]][$row[csf('color_id')]]['gauge']='D: '.implode(",", array_unique(explode(",", $row[csf('machine_dias')])))."<br> G:".implode(",", array_unique(explode(",", $row[csf('machine_gg')])));
	            $prod_dia_array[$row[csf('order_id')]][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('dia_width_type')]][$row[csf('color_id')]]['yrn_count_id'].=$count_name.',';
				$prod_dia_array[$row[csf('order_id')]][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('dia_width_type')]][$row[csf('color_id')]][$row[csf('yarn_lot')]]['stitch_len']=$stitch_len_name;
				$prod_dia_array[$row[csf('order_id')]][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('dia_width_type')]][$row[csf('color_id')]]['brand_name']=$brand_name;
	        }
	        //var_dump($prod_dia_array);




			?>
	        <div>
	        	<table align="left" cellspacing="0" width="930"  border="1" rules="all" class="rpt_table" >

				<?
				//die;
				$collar_cuff_arr=array();
				foreach($sql_kniting_result as $row)
	            {
					if($row[csf('collar_cuff')]!="")
					{
						$collar_cuff_arr[]=$row[csf('collar_cuff')];
					}
					$collar_cuff_count=count($collar_cuff_arr);
				}
	            $lib_item_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');
	            $i=1; $z=1; $collar_cuff_span=0; $grand_tot_roll=0; $grand_tot_finish_qty=0;
	            foreach($sql_kniting_result as $row)
	            {
					//echo $collar_cuff_cond;
					if($z==1)
					{
					?>
	                <thead bgcolor="#dddddd" align="center">
	                    <th width="25">SL</th>
	                    <th width="70">Order No</th>
	                    <th width="70">Job Number</th>
	                    <th width="70">Cust. Buyer</th>
	                    <th width="60">Count</th>
	                    <th width="70">Brand /Lot</th>
	                    <th width="60">M/C Dia/ Gauge</th>
	                    <th width="60">F.Dia/ F.GSM</th>
	                    <th width="60">Dia Type</th>
	                    <th width="60">S/L</th>
	                    <th width="70">Color</th>
	                    <th width="100">Description</th>
	                    <?
						if($collar_cuff_count!=0)
						{
							$row_show=0;
						?>
	                    <th width="50">Collar Cuff Measurement</th>
	                    <?
						}
						else
						{
							$row_show=1;
						}
						?>
	                    <th width="40">Roll /Bag</th>
	                    <th width="60">Delivery Qty</th>
	                    <th width="50">Rec. Challan</th>
	                    <th>Remarks</th>
	                </thead>
	                <?
					$z++;
					}
	                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

					$fabric_description=$prod_dia_array[$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('color_id')]]['fabric_description'];
					$stitch_len=$prod_dia_array[$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('color_id')]][$row[csf('yarn_lot')]]['stitch_len'];
					$gauge_val=$prod_dia_array[$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('color_id')]]['gauge'];
					$machine_dia_val=$prod_dia_array[$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('color_id')]]['machine_dia'];
					$yrn_count_val=rtrim($prod_dia_array[$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('color_id')]]['yrn_count_id'],',');
					$brand_name_val=$prod_dia_array[$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('color_id')]]['brand_name'];
					$mc_dia_gauge_uni=implode(",",array_unique(explode(", ",$gauge_val)));
					//$gauge_val_uni=implode(",",array_unique(explode(", ",$gauge_val)));
					$brand_lot='B:'.$brand_name_val.'<br> L:'.$row[csf('yarn_lot')];
					$fdia_fgsm='D:'.$row[csf('dia')].'<br> G:'.$row[csf('gsm')];
					//echo $yrn_count_val.'='.$row[csf('order_id')].'='.$row[csf('item_id')].'='.$row[csf('gsm')].'='.$row[csf('dia')].'='.$row[csf('width_dia_type')].'='.$row[csf('color_id')].'<br>';

	                ?>

	                <tr bgcolor="<? echo $bgcolor; ?>" >
	                    <td><? echo $i; ?></td>
	                    <td><p><? echo $job_po_array[$row[csf('order_id')]]['order_no']; ?></p></td>
	                    <td><p><? echo $job_po_array[$row[csf('order_id')]]['subcon_job']; ?></p></td>
	                    <td><p><? echo $job_po_array[$row[csf('order_id')]]['cust_buyer']; ?></p></td>
	                    <td><p><? echo $yrn_count_val; ?></p></td>
	                    <td><p><? echo $brand_lot; ?></p></td>
	                    <td><p><? echo $mc_dia_gauge_uni; ?></p></td>
	                    <td><p><? echo $fdia_fgsm; ?></p></td>
	                    <td><p><? echo $fabric_typee[$row[csf('width_dia_type')]]; ?></p></td>
	                    <td><p><? echo $stitch_len; ?></p></td>
	                    <td><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
	                    <td><p><? echo $fabric_description; ?></p></td>
	                    <? if($row_show==0)
						{
						?>
	                        <td><p><? echo $row[csf('collar_cuff')]; ?></p></td>
	                    <?
						}
						?>
	                    <td align="right"><? echo number_format($row[csf('carton_roll')],2,'.',''); ?></td>
	                    <td align="right"><? echo number_format($row[csf('delivery_qty')],2,'.',''); ?></td>
	                    <td><p><? echo $recChallan_arr[$row[csf('order_id')]]['chalan_no']; ?></p></td>
	                    <td><p><? echo $row[csf('remarks')]; ?></p></td>
	                </tr>
	                <?
	                $grand_tot_roll+=$row[csf('carton_roll')];
	                $grand_tot_finish_qty+=$row[csf('delivery_qty')];
					if($row_show==0)
					{
						$collar_cuff_span=13;
					}
					else
					{
						$collar_cuff_span=12;
					}
	                $i++;
	                }
	            	?>
	                <tfoot>
	                    <th colspan="<? echo $collar_cuff_span; ?>" align="right"><strong>Total</strong></th>
	                    <th align="right"><? echo number_format($grand_tot_roll,2,'.',''); ?></th>
	                    <th align="right"><? echo number_format($grand_tot_finish_qty,2,'.',''); ?></th>
	                    <th>&nbsp;</th>
	                    <th>&nbsp;</th>
	                </tfoot>
	            </table>
            </div>
            <br>

           	<?
            echo signature_table(64, $company, "930px", "", "45");
			?>
		</div>
	    <script type="text/javascript" src="../../js/jquery.js"></script>
	    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	    <script>
			function generateBarcode( valuess )
			{
				//alert(valuess);
				var value = valuess;//$("#barcodeValue").val();
				var btype = 'code39';//$("input[name=btype]:checked").val();
				var renderer ='bmp';// $("input[name=renderer]:checked").val();
				var settings = {
				  output:renderer,
				  bgColor: '#FFFFFF',
				  color: '#000000',
				  barWidth: 1,
				  barHeight: 30,
				  moduleSize:5,
				  posX: 10,
				  posY: 20,
				  addQuietZone: 1
				};
				$("#barcode_img_id_<? echo $cid; ?>").html('11');
				value = {code:value, rect: false};

				$("#barcode_img_id_<? echo $cid; ?>").show().barcode(value, btype, settings);

				/*$("#barcode_img_id").html('11');
				value = {code:value, rect: false};
				$("#barcode_img_id").show().barcode(value, btype, settings);*/
			}
			generateBarcode('<? echo $sys_id; ?>');
		</script>
	</div>
	<p style="page-break-after:always;"></p>
	<?
	}
	exit();
}

if($action=="knitting_delivery_print1")
{
	extract($_REQUEST);
	$ex_data=explode('*',$data);
	$company=$ex_data[0];
	$location=$ex_data[5];
	$update_id=$ex_data[1];
	$sys_id=$ex_data[2];
	//print_r ($data);
	//$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library",'master_tble_id','image_location');
	//$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_library=return_library_array( "select id,supplier_name from lib_supplier", "id","supplier_name");
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$country_arr=return_library_array( "select id, country_name from lib_country", "id", "country_name");
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$location_library=return_library_array( "select id, location_name from lib_location",'id','location_name');

	//$item_arr=return_library_array( "select cons_comp_id, color_name from lib_color", "cons_comp_id", "color_name");
	//$color_arr=return_library_array( "select id, color_name from  lib_color", "id", "color_name"  );
	 

	$sql="select id, party_id,location_id, challan_no, process_id, delivery_date, forwarder, transport_company, vehical_no,delivery_no from subcon_delivery_mst where id='$update_id' and company_id='$company' and status_active=1 and is_deleted=0";

	$dataArray=sql_select($sql);
	$mst_id=$dataArray[0][csf('id')];

	$sql_job_po="select a.id, a.order_no, a.cust_buyer, b.subcon_job, a.process_id from  subcon_ord_dtls a, subcon_ord_mst b,subcon_delivery_dtls c where a.job_no_mst=b.subcon_job and c.order_id=b.order_id and c.mst_id=$mst_id and b.is_deleted=0 and b.status_active=1 and a.status_active=1 and a.is_deleted=0";
	$job_po_array=array();
	$result_job_po=sql_select($sql_job_po);
	foreach($result_job_po as $row)
	{
		$job_po_array[$row[csf('id')]]['order_no']=$row[csf('order_no')];
		$job_po_array[$row[csf('id')]]['process_id']=$row[csf('process_id')];
		$job_po_array[$row[csf('id')]]['party_id']=$row[csf('party_id')]; 
		$job_po_array[$row[csf('id')]]['subcon_job']=$row[csf('subcon_job')]; 
		$job_po_array[$row[csf('id')]]['cust_buyer']=$row[csf('cust_buyer')];
		$poId_array[$row[csf('id')]]=$row[csf('id')]; 
	}
	//var_dump($job_po_array);
	$recChallan_arr=array();
	if($db_type==0)
	{
		$sql_rec="select b.order_id, group_concat(a.chalan_no) as chalan_no from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=1 group by b.order_id";
	}
	else if ($db_type==2)
	{
		$sql_rec="select b.order_id,  a.chalan_no  as chalan_no from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and b.order_id in(".implode(",",$poId_array).") and a.trans_type=1";
	}
	$result_sql_rec=sql_select($sql_rec);
	foreach($result_sql_rec as $row)
	{
		$chalan_no=array_unique(explode(",",$row[csf("chalan_no")]));
		$challan='';
		foreach($chalan_no as $val)
		{
			if($challan=="") $challan=$val; else $challan.=", ".$val;
		}
		$recChallan_arr[$row[csf('order_id')]]['chalan_no'].=$row[csf("chalan_no")].',';
	}

	
	$com_dtls = fnc_company_location_address($company, $location, 2);
	$k=0;
	$copy_no=array(1,2,3); //for Dynamic Copy here
	foreach($copy_no as $cid)
	{
		if ($cid!=1) $margin_cond="style='margin-top: 70px;'";
		else $margin_cond='';
		$k++;
		?>
    <div style="width:930px;">
	   		<table width="900" cellpadding="0" cellspacing="0">
	       <tr>
	           <td rowspan="4" align="right">
	               <img  src='../../<? echo $com_dtls[2]; ?>' height='100' width='200'/>
	           </td>
	           <td align="center" width="500" style="font-size:x-large" align="center"><strong ><? echo $com_dtls[0]; ?></strong></td>
	           <td rowspan="4" width="200" align="right">
	               <?
					if($k==1){
					echo "<b><h2>Office Copy</h2></b>";
					}
					else if($k==2){
					echo "<b><h2>Customer Copy</h2></b>";
					}
					else if($k==3){
					echo "<b><h2>Accounts Copy</h2></b>";
					}
					?>
	           </td>
	        </tr>
	        <tr>
	        	<td align="center" style="font-size:14px;"><strong >( <? echo $location_library[$dataArray[0][csf('location_id')]]; ?> )</strong></td>
	        </tr>
	        <tr>
	        	<td align="center"><? echo $com_dtls[1]; ?></td>
	        </tr>
	        <tr>
	        	<td align="center"><strong><? echo $ex_data[3]; ?> Challan</strong></td>
	        </tr>
	    </table>
	    <table width="900" cellspacing="0" align="right">
	        <tr><td colspan="6" align="center"><hr></hr></td></tr>
	        <tr>
	        	<td colspan="2">&nbsp;</td>
	        	<td> <strong>Delivery No :</strong></td>
	        	<td> <? echo $dataArray[0][csf('delivery_no')] ; ?> </td>
	        	<td colspan="2">&nbsp;</td>
	        </tr>
	        <tr>
				<?
	                $party_add=$dataArray[0][csf('party_id')];
	                $nameArray=sql_select( "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$party_add");
	                foreach ($nameArray as $result)
	                {
	                    $address="";
	                    if($result!="") $address=$result[csf('address_1')];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
	                }
	            ?>
	        	<td width="300" rowspan="4" valign="top" colspan="2"><strong>Party : <? echo $buyer_library[$party_add].'<br>'.$address;  ?></strong></td>
	            <td width="125" ><strong>Challan No :</strong></td><td width="170px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
	            <td width="125"><strong>Delivery Date :</strong></td><td width="170px"><? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
	        </tr>
	        <tr>
	            <td><strong>Transport Com.:</strong></td><td><? echo $dataArray[0][csf('transport_company')]; ?></td>
	            <td><strong>Forwarder:</strong></td><td><? echo $supplier_library[$dataArray[0][csf('forwarder')]]; ?></td>
	        </tr>
	        <tr>
	            <td><strong>Vehicle No:</strong></td><td><? echo $dataArray[0][csf('vehical_no')]; ?></td>
	            <td><strong>D.O No.</strong></td><td><? //echo $supplier_library[$dataArray[0][csf('forwarder')]]; ?></td>
	        </tr>
	        <tr>
	            <td>&nbsp;</td><td colspan="3" id="barcode_img_id_<? echo $cid;?>"></td>
	        </tr>
	    </table>
    	<br>
    	<div style="width:100%;">
			<?
			$machine_array=array();
			$mac_sql="Select id, machine_no, dia_width, gauge from  lib_machine_name where status_active=1 and is_deleted=0";

			$result_mac_sql=sql_select($mac_sql);
	        foreach($result_mac_sql as $row)
	        {
				$machine_array[$row[csf('id')]]['machine_no']=$row[csf('machine_no')];
				$machine_array[$row[csf('id')]]['gauge']=$row[csf('gauge')];
				$machine_array[$row[csf('id')]]['dia_width']=$row[csf('dia_width')];
			}
			    $mst_id=$dataArray[0][csf('id')];
			   $sql_kniting="select order_id, item_id, dia, width_dia_type, gsm, color_id, yarn_lot, delivery_qty, carton_roll, remarks, collar_cuff from subcon_delivery_dtls where mst_id='$mst_id' and process_id=2 ";
	        $sql_kniting_result=sql_select($sql_kniting);
			 foreach($sql_kniting_result as $row)
	        {
				$order_idArr[$row[csf('order_id')]]=$row[csf('order_id')];
			}
			$order_ids=implode(",",$order_idArr);
			//echo $order_ids.'d';

	        $prod_dia_array=array();
	        if($db_type==2)
	        {
	            $prod_dia_sql="select order_id, cons_comp_id, fabric_description, gsm, dia_width, dia_width_type, color_id, yarn_lot,
	            listagg((cast(machine_id as varchar2(4000))),',') within group (order by machine_id) as machine_id,
	            listagg((cast(machine_gg as varchar2(4000))),',') within group (order by machine_gg) as machine_gg,
	            listagg((cast(machine_dia as varchar2(4000))),',') within group (order by machine_dia) as machine_dias,
	            listagg((cast(yrn_count_id as varchar2(4000))),',') within group (order by yrn_count_id) as yrn_count_id,
	            listagg((cast(stitch_len as varchar2(4000))),',') within group (order by stitch_len) as stitch_len,
				listagg((cast(brand as varchar2(4000))),',') within group (order by brand) as brand,
	            sum(product_qnty) as production_qnty from subcon_production_dtls where product_type=2 and status_active=1 and is_deleted=0 and order_id in($order_ids)  group by order_id, cons_comp_id, fabric_description, gsm, dia_width, dia_width_type, color_id,yarn_lot";
	        }
	        else if($db_type==0)
	        {
	            $prod_dia_sql="select order_id, cons_comp_id, fabric_description, gsm, dia_width, dia_width_type, color_id,yarn_lot,
	            group_concat(machine_id) as machine_id,group_concat(machine_gg) as machine_gg,group_concat(machine_dia) as machine_dias,
	            group_concat(yrn_count_id) as yrn_count_id,
	            group_concat(stitch_len) as stitch_len,
				group_concat(brand) as brand,
	            sum(product_qnty) as production_qnty from subcon_production_dtls where product_type=2 and status_active=1 and is_deleted=0 and order_id in($order_ids)  group by order_id, cons_comp_id, fabric_description, gsm, dia_width, dia_width_type, color_id, yarn_lot";
	        }
	         //echo $prod_dia_sql;
	        $result_prod_dia_sql=sql_select($prod_dia_sql);
	        foreach($result_prod_dia_sql as $row)
	        {
				$machine_id=array_unique(explode(",",$row[csf('machine_id')]));
				$machine_name=""; $gauge_value=""; $machine_dia="";
				foreach($machine_id as $val)
				{
					if($machine_name=="") $machine_name=$machine_array[$val]['machine_no']; else $machine_name.=", ".$machine_array[$val]['machine_no'];
					if($gauge_value=="") $gauge_value=$machine_array[$val]['gauge']; else $gauge_value.=", ".$machine_array[$val]['gauge'];
					if($machine_dia=="") $machine_dia=$machine_array[$val]['dia_width']; else $machine_dia.=", ".$machine_array[$val]['dia_width'];
				}
				if($row[csf('machine_dias')]=="") $machine_dia=$machine_dia; else $machine_dia=implode(",",array_filter(array_unique(explode(",",$row[csf('machine_dias')]))));
				if($row[csf('machine_gg')]=="") $gauge_value=$gauge_value; else $gauge_value=implode(",",array_filter(array_unique(explode(",",$row[csf('machine_gg')]))));
				$yarn_countArr=array_unique(explode(",",$row[csf('yrn_count_id')]));
				$count_name="";
				foreach($yarn_countArr as $val)
				{
					if($count_name=="") $count_name=$count_arr[$val]; else $count_name.=", ".$count_arr[$val];
				}
			//	echo $count_name.'D';
				$stitch_len_id=array_unique(explode(",",$row[csf('stitch_len')]));
				$stitch_len_name="";
				foreach($stitch_len_id as $val)
				{
					if($stitch_len_name=="") $stitch_len_name=$val; else $stitch_len_name.=", ".$val;
				}

				$brand=array_unique(explode(",",$row[csf('brand')]));
				$brand_name="";
				foreach($brand as $val)
				{
					if($brand_name=="") $brand_name=$val; else $brand_name.=", ".$val;
				}

	            $prod_dia_array[$row[csf('order_id')]][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('dia_width_type')]][$row[csf('color_id')]]['machine_dia']=$machine_dia;
				$prod_dia_array[$row[csf('order_id')]][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('dia_width_type')]][$row[csf('color_id')]]['fabric_description']=$row[csf('fabric_description')];
				$prod_dia_array[$row[csf('order_id')]][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('dia_width_type')]][$row[csf('color_id')]]['gauge']='D: '.implode(",", array_unique(explode(",", $row[csf('machine_dias')])))."<br> G:".implode(",", array_unique(explode(",", $row[csf('machine_gg')])));
	            $prod_dia_array[$row[csf('order_id')]][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('dia_width_type')]][$row[csf('color_id')]]['yrn_count_id'].=$count_name.',';
				$prod_dia_array[$row[csf('order_id')]][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('dia_width_type')]][$row[csf('color_id')]][$row[csf('yarn_lot')]]['stitch_len']=$stitch_len_name;
				$prod_dia_array[$row[csf('order_id')]][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('dia_width_type')]][$row[csf('color_id')]]['brand_name']=$brand_name;
	        }
	        //var_dump($prod_dia_array);




			?>
	        <div>
	        	<table align="left" cellspacing="0" width="930"  border="1" rules="all" class="rpt_table" >

				<?
				//die;
				$collar_cuff_arr=array();
				foreach($sql_kniting_result as $row)
	            {
					if($row[csf('collar_cuff')]!="")
					{
						$collar_cuff_arr[]=$row[csf('collar_cuff')];
					}
					$collar_cuff_count=count($collar_cuff_arr);
				}
	            $lib_item_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');
	            $i=1; $z=1; $collar_cuff_span=0; $grand_tot_roll=0; $grand_tot_finish_qty=0;
	            foreach($sql_kniting_result as $row)
	            {
					//echo $collar_cuff_cond;
					if($z==1)
					{
					?>
	                <thead bgcolor="#dddddd" align="center">
	                    <th width="25">SL</th>
	                    <th width="70">Order No</th>
	                    <th width="70">Job Number</th>
	                    <th width="70">Cust. Buyer</th>
	                    <th width="60">Count</th>
	                    <th width="70">Brand /Lot</th>
	                    <th width="60">M/C Dia/ Gauge</th>
	                    <th width="60">F.Dia/ F.GSM</th>
	                    <th width="60">Dia Type</th>
	                    <th width="60">S/L</th>
	                    <th width="70">Color</th>
	                    <th width="100">Description</th>
	                    <?
						if($collar_cuff_count!=0)
						{
							$row_show=0;
						?>
	                    <th width="50">Collar Cuff Measurement</th>
	                    <?
						}
						else
						{
							$row_show=1;
						}
						?>
	                    <th width="40">Roll /Bag</th>
	                    <th width="60">Delivery Qty</th>
	                    <th width="50">Rec. Challan</th>
	                    <th>Remarks</th>
	                </thead>
	                <?
					$z++;
					}
	                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

					$fabric_description=$prod_dia_array[$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('color_id')]]['fabric_description'];
					$stitch_len=$prod_dia_array[$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('color_id')]][$row[csf('yarn_lot')]]['stitch_len'];
					$gauge_val=$prod_dia_array[$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('color_id')]]['gauge'];
					$machine_dia_val=$prod_dia_array[$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('color_id')]]['machine_dia'];
					$yrn_count_val=rtrim($prod_dia_array[$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('color_id')]]['yrn_count_id'],',');
					$brand_name_val=$prod_dia_array[$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('color_id')]]['brand_name'];
					$mc_dia_gauge_uni=implode(",",array_unique(explode(", ",$gauge_val)));
					//$gauge_val_uni=implode(",",array_unique(explode(", ",$gauge_val)));
					$brand_lot='B:'.$brand_name_val.'<br> L:'.$row[csf('yarn_lot')];
					$fdia_fgsm='D:'.$row[csf('dia')].'<br> G:'.$row[csf('gsm')];
					//echo $yrn_count_val.'='.$row[csf('order_id')].'='.$row[csf('item_id')].'='.$row[csf('gsm')].'='.$row[csf('dia')].'='.$row[csf('width_dia_type')].'='.$row[csf('color_id')].'<br>';

	                ?>

	                <tr bgcolor="<? echo $bgcolor; ?>" >
	                    <td><? echo $i; ?></td>
	                    <td><p><? echo $job_po_array[$row[csf('order_id')]]['order_no']; ?></p></td>
	                    <td><p><? echo $job_po_array[$row[csf('order_id')]]['subcon_job']; ?></p></td>
	                    <td><p><? echo $job_po_array[$row[csf('order_id')]]['cust_buyer']; ?></p></td>
	                    <td><p><? echo $yrn_count_val; ?></p></td>
	                    <td><p><? echo $brand_lot; ?></p></td>
	                    <td><p><? echo $mc_dia_gauge_uni; ?></p></td>
	                    <td><p><? echo $fdia_fgsm; ?></p></td>
	                    <td><p><? echo $fabric_typee[$row[csf('width_dia_type')]]; ?></p></td>
	                    <td><p><? echo $stitch_len; ?></p></td>
	                    <td><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
	                    <td><p><? echo $fabric_description; ?></p></td>
	                    <? if($row_show==0)
						{
						?>
	                        <td><p><? echo $row[csf('collar_cuff')]; ?></p></td>
	                    <?
						}
						?>
	                    <td align="right"><? echo number_format($row[csf('carton_roll')],2,'.',''); ?></td>
	                    <td align="right"><? echo number_format($row[csf('delivery_qty')],2,'.',''); ?></td>
	                    <td><p><? echo rtrim($recChallan_arr[$row[csf('order_id')]]['chalan_no'],','); ?></p></td>
	                    <td><p><? echo $row[csf('remarks')]; ?></p></td>
	                </tr>
	                <?
	                $grand_tot_roll+=$row[csf('carton_roll')];
	                $grand_tot_finish_qty+=$row[csf('delivery_qty')];
					if($row_show==0)
					{
						$collar_cuff_span=13;
					}
					else
					{
						$collar_cuff_span=12;
					}
	                $i++;
	                }
	            	?>
	                <tfoot>
	                    <th colspan="<? echo $collar_cuff_span; ?>" align="right"><strong>Total</strong></th>
	                    <th align="right"><? echo number_format($grand_tot_roll,2,'.',''); ?></th>
	                    <th align="right"><? echo number_format($grand_tot_finish_qty,2,'.',''); ?></th>
	                    <th>&nbsp;</th>
	                    <th>&nbsp;</th>
	                </tfoot>
	            </table>
            </div>
            <br>

           	<?
            echo signature_table(64, $company, "930px", "", "45");
			?>
		</div>
	    <script type="text/javascript" src="../../js/jquery.js"></script>
	    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	    <script>
			function generateBarcode( valuess )
			{
				//alert(valuess);
				var value = valuess;//$("#barcodeValue").val();
				var btype = 'code39';//$("input[name=btype]:checked").val();
				var renderer ='bmp';// $("input[name=renderer]:checked").val();
				var settings = {
				  output:renderer,
				  bgColor: '#FFFFFF',
				  color: '#000000',
				  barWidth: 1,
				  barHeight: 30,
				  moduleSize:5,
				  posX: 10,
				  posY: 20,
				  addQuietZone: 1
				};
				$("#barcode_img_id_<? echo $cid; ?>").html('11');
				value = {code:value, rect: false};

				$("#barcode_img_id_<? echo $cid; ?>").show().barcode(value, btype, settings);

				/*$("#barcode_img_id").html('11');
				value = {code:value, rect: false};
				$("#barcode_img_id").show().barcode(value, btype, settings);*/
			}
			generateBarcode('<? echo $sys_id; ?>');
		</script>
	</div>
	<p style="page-break-after:always;"></p>
	<?
	}
	exit();
}
?>