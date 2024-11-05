<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") {
	header("location:login.php");
}

include '../../includes/common.php';

$permission = $_SESSION['page_permission'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

$process_finishing = "4";
 
if ($action == "load_drop_down_location") {
	echo create_drop_down("cbo_location_name", 140, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name", "id,location_name", 1, "--Select Location--", $selected, "load_drop_down( 'requires/subcon_fabric_finishing_production_controller',document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_down_floor', 'floor_td' );", "", "", "", "", "", 3);
	exit();
}

if ($action == "load_drop_down_party_name") {
	echo create_drop_down("cbo_party_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name", "id,buyer_name", 1, "-- Select Party --", $selected, "", "", "", "", "", "", 4);
	exit();
}

if ($action == "load_drop_down_floor") {
	$data = explode("_", $data);
	$company_id = $data[0];
	$location_id = $data[1];
	if ($location_id == 0 || $location_id == "") {
		$location_cond = "";
	} else {
		$location_cond = " and a.location_id=$location_id";
	}

	if ($db_type == 0) {
		$group_cond = " GROUP BY a.id";
	} else if ($db_type == 2) {
		$group_cond = " GROUP BY a.id, a.floor_name";
	}
 

	//echo "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=4 and b.company_id=$company_id and b.status_active=1 and b.is_deleted=0 and a.production_process=4 $location_cond $group_cond order by a.floor_name";die;

	echo create_drop_down("cbo_floor_id", 140, "SELECT a.id, a.floor_name FROM lib_prod_floor a WHERE a.company_id = $company_id and  a.production_process = 4 AND a.status_active = 1AND a.is_deleted = 0 $location_cond $group_cond ORDER BY a.floor_name", "id,floor_name", 1, "-- Select Floor --", $selected, "load_drop_down( 'requires/subcon_fabric_finishing_production_controller',document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_machine', 'machine_td' );", "");
	exit();
}

if ($action == "load_drop_machine") {
	$data = explode("_", $data);
	$company_id = $data[0];
	$floor_id = $data[1];
	if ($floor_id == 0 || $floor_id == "") {
		$floor_cond = "";
	} else {
		$floor_cond = " and floor_id=$floor_id";
	}

	if ($db_type == 0) {
		$sql = "select id, concat(machine_no, '-', brand) as machine_name from lib_machine_name where category_id=4 and company_id=$company_id  and status_active=1 and is_deleted=0 and is_locked=0 $floor_cond order by machine_name";
	} else if ($db_type == 2) {
		$sql = "select id, machine_no || '-' || brand as machine_name from lib_machine_name where category_id=4 and company_id=$company_id  and status_active=1 and is_deleted=0 and is_locked=0 $floor_cond order by machine_name";
	}

	echo create_drop_down("cbo_machine_id", 140, $sql, "id,machine_name", 1, "-- Select Machine --", $selected, "", "");
	exit();
}

if ($action == "finishing_id_popup") {
	echo load_html_head_contents("Popup Info", "../../", 1, 1, $unicode, '', '');
	$ex_data = explode("_", $data);
	?>
	<script>
		function js_set_value(id)
		{
			document.getElementById('finishing_id').value=id;
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
        <form name="searchjobfrm_1"  id="searchjobfrm_1" autocomplete="off">
        <table width="850" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
                <th width="160">Company Name</th>
                <th width="160">Party Name</th>
                <th width="120">Production ID</th>
                <th width="120">Batch NO</th>
                <th width="200">Date Range</th>
                <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
            </thead>
            <tbody>
                <tr class="general">
                    <td> <input type="hidden" id="finishing_id">
						<?
							echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $ex_data[0],"load_drop_down( 'subcon_fabric_finishing_production_controller', this.value, 'load_drop_down_party_name', 'party_td' );",0 );
                        ?>
                    </td>
                    <td id="party_td">
                        <? echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$ex_data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $ex_data[1], "" );
                        ?>
                    </td>
                    <td>
                        <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes" style="width:95px" />
                    </td>
                    <td>
                        <input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:95px" />
                    </td>
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:65px">
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:65px">
                    </td>
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_batch_no').value, 'fabric_finishing_id_search_list_view', 'search_div', 'subcon_fabric_finishing_production_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:100px;" />
                    </td>
                </tr>
                <tr>
                    <td colspan="6" align="center" valign="middle"><?=load_month_buttons(1); ?></td>
                </tr>
            </tbody>
        </table>
        <div id="search_div"></div>
        </form>
        </div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=="fabric_finishing_id_search_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company_name=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }

	if($db_type==0)
	{
		if ($data[1]!="" &&  $data[2]!="") $production_date_cond = "and a.product_date between '".change_date_format($data[1],'yyyy-mm-dd')."' and '".change_date_format($data[2],'yyyy-mm-dd')."'"; else $production_date_cond ="";
	}
	else
	{
		if ($data[1]!="" &&  $data[2]!="") $production_date_cond = "and a.product_date between '".change_date_format($data[1], "", "",1)."' and '".change_date_format($data[2], "", "",1)."'"; else $production_date_cond ="";
	}

	if ($data[3]!='') $product_id_cond=" and a.prefix_no_num='$data[3]'"; else $product_id_cond="";
	if ($data[4]!=0) $buyer_cond=" and a.party_id='$data[4]'"; else $buyer_cond="";
	if ($data[5]!='') $batch_src_con=" and lower(c.batch_no) like('%".strtolower(trim($data[5]))."%')"; else $batch_src_con="";

	$return_to=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$batch_array=array();
	$batch_id_sql="select id, batch_no, extention_no from pro_batch_create_mst where entry_form=36 and status_active=1 and is_deleted=0";
	$batch_id_sql_result=sql_select($batch_id_sql);
	foreach ($batch_id_sql_result as $row)
	{
		$batch_array[$row[csf("id")]]["batch_no"]=$row[csf("batch_no")];
		$batch_array[$row[csf("id")]]["extention_no"]=$row[csf("extention_no")];
	}
//var_dump($batch_array);
	//$arr=array (2=>$receive_basis_arr,3=>$return_to);
	if($db_type==0)
	{
		$year_cond= "year(a.insert_date)as year";
		$batch_cond="group_concat(b.batch_id) as batch_id";
	}
	else if($db_type==2)
	{
		$year_cond= "TO_CHAR(a.insert_date,'YYYY') as year";
		$batch_cond="listagg((cast(b.batch_id as varchar2(4000))),',') within group (order by b.batch_id) as batch_id";
	}
	 //$sql= "select a.id, product_no, a.prefix_no_num, $year_cond, a.party_id, a.product_date, a.prod_chalan_no, $batch_cond, sum(b.product_qnty) as product_qnty from subcon_production_mst a, subcon_production_dtls b where a.entry_form=292 and a.id=b.mst_id and a.product_type=4 and b.product_type=4 and a.status_active=1 $company_name $buyer_cond $production_date_cond $product_id_cond group by a.id, product_no, a.prefix_no_num, a.insert_date, a.party_id, a.product_date, a.prod_chalan_no order by a.id DESC";

	 $sql= "SELECT a.id, product_no, a.prefix_no_num, $year_cond, a.party_id, a.product_date, a.prod_chalan_no, $batch_cond, sum(b.product_qnty) as product_qnty from subcon_production_mst a join subcon_production_dtls b on a.id=b.mst_id left join pro_batch_create_mst c on c.id=b.batch_id where a.entry_form=292 and a.product_type=4 and b.product_type=4 and a.status_active=1 $company_name $buyer_cond $production_date_cond $product_id_cond $batch_src_con group by a.id, product_no, a.prefix_no_num, a.insert_date, a.party_id, a.product_date, a.prod_chalan_no order by a.id DESC";
	 //echo $sql;

	//echo  create_list_view("list_view", "Prod. ID,Year,Basis,Party,Prod. Date,Product Challan", "80,80,120,120,70,120","750","250",0, $sql , "js_set_value", "id", "", 1, "0,0,basis,party_id,0,0", $arr , "prefix_no_num,year,basis,party_id,product_date,prod_chalan_no", "subcon_fabric_finishing_production_controller","",'0,0,0,0,3,0');
    ?>
    <div>
    <script>
		$(document).ready(function(e) {
            setFilterGrid('tbl_po_list',-1);
        });

	</script>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table">
            <thead>
                <th width="30" >SL</th>
                <th width="60" >Prod. ID</th>
                <th width="60" >Year</th>
                <th width="120" >Party</th>
                <th width="70" >Prod. Date</th>
                <th width="100" >Product Challan</th>
                <th width="100" >Batch</th>
                <th>Prod. Qty</th>
            </thead>
     	</table>
     </div>
     <div style="width:750px; max-height:270px;overflow-y:scroll;" >
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="730" class="rpt_table" id="tbl_po_list">
			<?
			$result_sql= sql_select($sql);
			$i=1;
            foreach($result_sql as $row )
            {
                if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				$batch_no="";
				$batch_id=array_unique(explode(",",$row[csf("batch_id")]));
				foreach($batch_id as $key)
				{
					if($batch_no=="") $batch_no=$batch_array[$key]['batch_no']; else $batch_no.=", ".$batch_array[$key]['batch_no'];
				}
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value(<? echo $row[csf("id")];?>);" >
						<td width="30" align="center"><? echo $i; ?></td>
						<td width="60" align="center"><? echo $row[csf("prefix_no_num")]; ?></td>
                        <td width="60" align="center"><? echo $row[csf("year")]; ?></td>
						<td width="120"><? echo $return_to[$row[csf("party_id")]];  ?></td>
						<td width="70"><? echo change_date_format($row[csf("product_date")]); ?></td>
						<td width="100"><? echo $row[csf("prod_chalan_no")];?> </td>
						<td width="100"><p><? echo $batch_no; ?></p></td>
                        <td align="right"><? echo number_format($row[csf("product_qnty")],2,'.',''); ?></td>
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

if ($action=="load_php_data_to_form_mst")
{
	$nameArray=sql_select( "select id, product_no, basis, company_id, location_id, party_id, product_date, prod_chalan_no, remarks from subcon_production_mst where id='$data'" );
	foreach ($nameArray as $row)
	{
		echo "document.getElementById('txt_finishing_id').value 			= '".$row[csf("product_no")]."';\n";
		echo "document.getElementById('cbo_receive_basis').value			= '".$row[csf("basis")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "load_drop_down('requires/subcon_fabric_finishing_production_controller',document.getElementById('cbo_company_id').value,'load_drop_down_location','location_td' );\n";
		echo "document.getElementById('cbo_location_name').value			= '".$row[csf("location_id")]."';\n";
		echo "load_drop_down('requires/subcon_fabric_finishing_production_controller',document.getElementById('cbo_company_id').value,'load_drop_down_party_name','party_td' );\n";
		echo "document.getElementById('cbo_party_name').value				= '".$row[csf("party_id")]."';\n";
		echo "document.getElementById('txt_finishing_date').value 			= '".change_date_format($row[csf("product_date")])."';\n";
		echo "document.getElementById('txt_chal_no').value					= '".$row[csf("prod_chalan_no")]."';\n";
		echo "document.getElementById('txt_remarks').value					= '".$row[csf("remarks")]."';\n";
	    echo "document.getElementById('update_id').value            		= '".$row[csf("id")]."';\n";
		echo "disable_enable_fields('cbo_company_id*cbo_location_name*cbo_party_name',1);\n";

		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'subcon_fabric_finishing',1);\n";
	}
	exit();
}

if ($action=="batch_numbers_popup")
{
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode,'','');
	?>
	<script>
		function js_set_value(id)
		{
			document.getElementById('selected_batch_id').value=id;
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
            <form name="searchjobfrm_1"  id="searchjobfrm_1" autocomplete="off">
            <table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                <thead>
                    <tr>
                        <th colspan="4"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                    </tr>
                	<tr>
                        <th width="160">Company Name</th>
                        <th width="120">Batch No</th>
                        <th width="200">Date Range</th>
                        <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="general">
                        <td> <input type="hidden" id="selected_batch_id">
                            <?
                                $data=explode("_",$data);
								//echo  $data[1].',ddd';
                                echo create_drop_down( "cbo_company_id", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $data[0],"",1 );
                            ?>
                        </td>
                        <td>
                            <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes" style="width:95px" />
                        </td>
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
                        </td>
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+<? echo $data[1];?>, 'batch_search_list_view', 'search_div', 'subcon_fabric_finishing_production_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" align="center" valign="middle">
							<? echo load_month_buttons(1);  ?>
                        </td>
                    </tr>
                 </tbody>
            </table>
            </form>
            <div id="search_div"></div>
        </div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=="batch_search_list_view")
{
	$data=explode('_',$data);
	$search_type =$data[4];
	$party_id =$data[5];
	//echo $party_id.',';;

	$color_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$po_arr=return_library_array( "select id,order_no from subcon_ord_dtls",'id','order_no');

	if ($data[0]!=0) $company_con=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if($db_type==0)
	{
		if ($data[1]!="" &&  $data[2]!="") $batch_date_cond = "and a.batch_date between '".change_date_format($data[1],'yyyy-mm-dd')."' and '".change_date_format($data[2],'yyyy-mm-dd')."'"; else $batch_date_cond ="";
	}
	else
	{
		if ($data[1]!="" &&  $data[2]!="") $batch_date_cond = "and a.batch_date between '".change_date_format($data[1], "", "",1)."' and '".change_date_format($data[2], "", "",1)."'"; else $batch_date_cond ="";
	}

	if($search_type==1)
	{
		if ($data[3]!='') $batch_no_cond=" and a.batch_no='$data[3]'"; else $batch_no_cond="";
	}
	else if($search_type==4 || $search_type==0)
	{
		if ($data[3]!='') $batch_no_cond=" and a.batch_no like '%$data[3]%'"; else $batch_no_cond="";
	}
	else if($search_type==2)
	{
		if ($data[3]!='') $batch_no_cond=" and a.batch_no like '$data[3]%'"; else $batch_no_cond="";
	}
	else if($search_type==3)
	{
		if ($data[3]!='') $batch_no_cond=" and a.batch_no like '%$data[3]'"; else $batch_no_cond="";
	}//
	if ($party_id>0) $party_cond=" and d.party_id=$party_id"; else $party_cond="";
	$sql="SELECT a.id, a.batch_no, a.extention_no, a.color_id,a.process_id, a.batch_weight, a.total_liquor, (b.batch_qnty) as batch_qnty, b.po_id as po_id, c.main_process_id 
	from pro_batch_create_mst a,pro_batch_create_dtls b,subcon_ord_dtls c, subcon_ord_mst d 
	where a.id=b.mst_id and c.id=b.po_id and d.subcon_job=c.job_no_mst and a.status_active=1 and a.entry_form=36 $company_con $batch_date_cond $batch_no_cond $party_cond 
	 order by a.id DESC";// and a.batch_against=1
	 //group by a.id,a.process_id, a.batch_no, a.extention_no, a.color_id, a.batch_weight, a.total_liquor, b.po_id, c.main_process_id
	 // echo $sql;
	$result = sql_select($sql); $batchid="";
	$unloadData_omit_Arr=array();
	foreach( $result as $row )
	{
		$batchid.=$row[csf("id")].',';		 
		//else $unloadData_omit_Arr[$row[csf("id")]]='No';
	}
	//echo $batchid;
	$batchids=array_filter(array_unique(explode(",",$batchid)));
	$batchidsCond=where_con_using_array($batchids,0,"batch_id");
	// echo "select id, batch_id from pro_fab_subprocess where load_unload_id=2 and entry_form=38 and is_deleted=0 and status_active=1 $batchidsCond";
	$unloadDataArr=return_library_array( "select id, batch_id from pro_fab_subprocess where load_unload_id=2 and entry_form=38 and is_deleted=0 and status_active=1 $batchidsCond", "batch_id", "id");//ISD-21-03527
	foreach( $result as $row )
	{
		//echo $row[csf("id")].'SS';
		$batch_str=$row[csf("id")].'_'.$row[csf("po_id")];
		if($unloadDataArr[$row[csf("id")]]!="")
		{
			$batch_data_arr[$batch_str]['id']=$row[csf("id")];
			$batch_data_arr[$batch_str]['total_liquor']=$row[csf("total_liquor")];
			//$batch_data_arr[$batch_str]['batch_weight']+=$row[csf("batch_qnty")];
			$batch_data_arr[$batch_str]['color_id']=$row[csf("color_id")];
			$batch_data_arr[$batch_str]['extention_no']=$row[csf("extention_no")];
			$batch_data_arr[$batch_str]['batch_no']=$row[csf("batch_no")];
			$batch_data_arr[$batch_str]['po_id']=$row[csf("po_id")];
			$batch_data_arr[$batch_str]['process_id']=$row[csf("process_id")];
		}
		
		// $row[csf("main_process_id")]==4 is main process Finishing in Sub-Contract Order Entry, 
		$process_idArr=explode(",",$row[csf("process_id")]);
		if(in_array(440,$process_idArr) || $row[csf("main_process_id")]==4 || $row[csf("main_process_id")]==26) // Finishing Process
		{
			$batch_data_arr[$batch_str]['id']=$row[csf("id")];
			$batch_data_arr[$batch_str]['total_liquor']=$row[csf("total_liquor")];
			$batch_data_arr[$batch_str]['batch_weight']+=$row[csf("batch_qnty")];
			$batch_data_arr[$batch_str]['color_id']=$row[csf("color_id")];
			$batch_data_arr[$batch_str]['extention_no']=$row[csf("extention_no")];
			$batch_data_arr[$batch_str]['batch_no']=$row[csf("batch_no")];
			$batch_data_arr[$batch_str]['po_id']=$row[csf("po_id")];
			$batch_data_arr[$batch_str]['process_id']=$row[csf("process_id")];
		}

		if(in_array(171,$process_idArr)) // Drying Process
		{
			$batch_data_arr[$batch_str]['id']=$row[csf("id")];
			$batch_data_arr[$batch_str]['total_liquor']=$row[csf("total_liquor")];
			$batch_data_arr[$batch_str]['batch_weight']==$row[csf("batch_weight")];
			$batch_data_arr[$batch_str]['color_id']=$row[csf("color_id")];
			$batch_data_arr[$batch_str]['extention_no']=$row[csf("extention_no")];
			$batch_data_arr[$batch_str]['batch_no']=$row[csf("batch_no")];
			$batch_data_arr[$batch_str]['po_id']=$row[csf("po_id")];
			$batch_data_arr[$batch_str]['process_id']=$row[csf("process_id")];
		}
	}
	
	 
	//print_r($unloadDataArr);
	//echo "select id, batch_id from pro_fab_subprocess where load_unload_id=2 and entry_form=38 and is_deleted=0 and status_active=1 $batchidsCond";
	?>
    <div>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table">
            <thead>
                <th width="30" >SL</th>
                <th width="100" >Batch no</th>
                <th width="100" >Batch Ext.</th>
                <th width="120" >Batch Color</th>
                <th width="100" >Batch weight</th>
                <th width="80" >Batch liquor</th>
                <th>Order No</th>
            </thead>
     	</table>
     <div style="width:750px; max-height:270px;overflow-y:scroll;" >
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="730" class="rpt_table" id="list_view">
			<?
			$i=1;
            foreach( $batch_data_arr as $batch_id=>$row )
            {
				//echo $unloadData_omit_Arr[$row[csf("id")]].'D';
				  $batch_idArr=explode("_",$batch_id);
				  $batch_id= $batch_idArr[0];
				   $po_id= $batch_idArr[1];
				//if($unloadDataArr[$row[("id")]]!="")
				//{
					if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					//$order_id=explode(',',$row[csf("po_id")]);
					$order_no='';
					$order_id=array_unique(explode(",",$po_id));
					foreach($order_id as $val)
					{
						if($order_no=="") $order_no=$po_arr[$val]; else $order_no.=",".$po_arr[$val];
					}
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $batch_id.'_'.$po_id;?>');" >
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="100" align="center"><? echo $row[("batch_no")]; ?></td>
							<td width="100" align="center"><? echo $row[("extention_no")]; ?></td>
							<td width="120" align="center"><? echo $color_arr[$row[("color_id")]]; ?></td>
							<td width="100" align="right"><? echo number_format($row[("batch_weight")],2); ?></td>
							<td width="80" align="right"><? echo number_format($row[("total_liquor")],2);  ?></td>
							<td><p><? echo $order_no; ?></p></td>
						</tr>
					<?
					$i++;
				//}
            }
   		?>
			</table>
		</div>
     </div>
    <?
	exit();
}

if($action=="load_php_data_to_form_batch")
{
	$ex_data=explode('_',$data);
	//$po_arr=return_library_array( "select id,order_no from subcon_ord_dtls",'id','order_no');
	//$job_no_arr=return_library_array( "select id,job_no_mst from subcon_ord_dtls",'id','job_no_mst');
	$process_arr=return_library_array( "select id,main_process_id from subcon_ord_dtls",'id','main_process_id');
	$party_id_arr=return_library_array( "select subcon_job,party_id from subcon_ord_mst",'subcon_job','party_id');
	$color_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');

	

	//echo "select a.batch_no, a.extention_no, a.color_id, b.width_dia_type, $select_field"."_concat(distinct(b.po_id)) as po_id from pro_batch_create_mst a,pro_batch_create_dtls b where a.id=b.mst_id and a.id='$data' $grop_cond";
	if($db_type==0)
	{
		$nameArray=sql_select( "select a.id, a.batch_no, a.extention_no, a.color_id, a.process_id, b.po_id as po_id from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.id='$ex_data[0]' and b.po_id='$ex_data[1]'  group by a.batch_no, a.extention_no" );
	}
	elseif($db_type==2)
	{
		$nameArray=sql_select( "select a.id, a.batch_no, a.extention_no, a.color_id, a.process_id, b.po_id as po_id from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.id='$ex_data[0]' and b.po_id='$ex_data[1]' group by a.id, a.batch_no, a.extention_no, a.color_id, a.process_id, b.po_id" );  //listagg(b.width_dia_type,',') within group (order by b.width_dia_type)
	}
	foreach ($nameArray as $row)
	{
		$poIdArr[$row[csf("po_id")]]=$row[csf("po_id")];
	}
	$po_sql=sql_select("select ID,ORDER_NO,JOB_NO_MST, CUST_BUYER from subcon_ord_dtls where  status_active=1 and is_deleted=0 and id in(".implode(',',$poIdArr).")");
	foreach ($po_sql as $row)
	{
		$po_arr[$row["ID"]]=$row["ORDER_NO"];
		$job_no_arr[$row["ID"]]=$row["JOB_NO_MST"];
		$order_buyer_arr[$row["ID"]]=$row["CUST_BUYER"];
	}

	foreach ($nameArray as $row)
	{
		$order_no='';$order_buyer=''; $main_process_id=''; $process_name=''; $party_id_array='';

		$order_id_hidde=implode(",",array_unique(explode(",",$row[csf("po_id")])));
		$order_id=array_unique(explode(",",$row[csf("po_id")]));
		foreach($order_id as $val)
		{
			if($order_no=="") $order_no=$po_arr[$val]; else $order_no.=",".$po_arr[$val];
			if($order_buyer=="") $order_buyer=$order_buyer_arr[$val]; else $order_buyer.=",".$order_buyer_arr[$val];
			if($main_process_id=="") $main_process_id=$process_arr[$val]; else $main_process_id.=",".$process_arr[$val];
			if($party_id_array=="") $party_id_array=$party_id_arr[$job_no_arr[$val]];
		}

		$process_id=array_unique(explode(",",$row[csf("process_id")]));
		foreach($process_id as $val)
		{
			if($process_name=="") $process_name=$conversion_cost_head_array[$val]; else $process_name.=",".$conversion_cost_head_array[$val];
		}

		echo "document.getElementById('txt_batch_no').value				= '".$row[csf("batch_no")]."';\n";
		echo "document.getElementById('txt_batch_id').value				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_batch_ext_no').value			= '".$row[csf("extention_no")]."';\n";
		echo "document.getElementById('txt_order_numbers').value		= '".$order_no."';\n";
		echo "document.getElementById('txt_cust_buyer').value		= '".$order_buyer."';\n";
		echo "document.getElementById('txt_process_name').value			= '".$process_name."';\n";
		//echo "document.getElementById('cbo_party_name').value			= '".$party_id_array."';\n";
		echo "document.getElementById('txt_process_id').value			= '".$row[csf("process_id")]."';\n";
		echo "document.getElementById('process_id').value				= '".$main_process_id."';\n";
		echo "document.getElementById('order_no_id').value				= '".$order_id_hidde."';\n";
		echo "document.getElementById('txt_color').value				= '".$color_arr[$row[csf("color_id")]]."';\n";
		echo "document.getElementById('hidden_color_id').value			= '".$row[csf("color_id")]."';\n";
		//echo "document.getElementById('hidden_dia_type').value			= '".$row[csf("width_dia_type")]."';\n";
		echo "disable_enable_fields('cbo_company_id*cbo_location_name*cbo_party_name',1);\n";
	}
	exit();
} 

if($action=="show_fabric_desc_listview")
{
	$data=explode('_',$data);
	$order_id=$data[0];
	$process_id=$data[1];
	//echo $gsm_val.jahid ;die;
	//$batch_arr=return_library_array( "select id, prod_id, item_description from lib_subcon_charge",'id','const_comp');
	$gsm_arr=return_library_array( "select id,gsm from lib_subcon_charge",'id','gsm');
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');

	$production_qty_array=array();
	$prod_sql="Select batch_id, cons_comp_id, sum(product_qnty) as product_qnty from subcon_production_dtls where order_id='$data[0]' and status_active=1 and is_deleted=0 and product_type=4 group by  batch_id, cons_comp_id";
	$prod_data_sql=sql_select($prod_sql);
	foreach($prod_data_sql as $row)
	{
		$production_qty_array[$row[csf('batch_id')]][$row[csf('cons_comp_id')]]=$row[csf('product_qnty')];
	}
	//var_dump($production_qty_array);
	 $sql = "select  a.id as batch_id, a.batch_no, a.extention_no, a.color_id, b.id, b.width_dia_type, b.po_id, b.item_description, b.batch_qnty as qnty, b.fin_dia, b.gsm from  pro_batch_create_mst a, pro_batch_create_dtls b where a.id='$data[2]' and a.id=b.mst_id and a.entry_form=36 and b.po_id in ($data[0]) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";//group by a.batch_no, a.extention_no, a.color_id, b.id, b.prod_id, b.item_description
	$data_array=sql_select($sql);
	?>
     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="400">
        <thead>
            <th width="15">SL</th>
            <th>Fabric, GSM, G/Dia, F/Dia</th>
            <th width="60">Color</th>
            <th width="60">Batch Qty</th>
            <th width="40">Prod. Qty</th>
            <th width="60">Bal. Qty</th>
        </thead>
        <tbody>
            <?
            $i=1;
            foreach($data_array as $row)
            {
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
             ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='set_form_data("<? echo $row[csf('id')]."**".$row[csf('item_description')].'_'.$row[csf('fin_dia')]."**".$row[csf('gsm')]."**".$row[csf('color_id')]."**".$color_arr[$row[csf('color_id')]]."**".$row[csf('po_id')]."**".$row[csf('width_dia_type')]; ?>")' style="cursor:pointer" >
                    <td><? echo $i; ?></td>
                    <td><p><? echo $row[csf('item_description')].','.$row[csf('fin_dia')]; ?></p></td>
                    <td><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                    <td align="right"><? echo number_format($row[csf('qnty')],2,'.',''); ?></td>
                    <td align="right"><? echo number_format($production_qty_array[$row[csf('batch_id')]][$row[csf('id')]],2,'.',''); ?></td>
                    <td align="right"><? echo number_format($row[csf('qnty')]-$production_qty_array[$row[csf('batch_id')]][$row[csf('id')]],2,'.',''); ?></td>
                </tr>
            <?
            $i++;
            }
            ?>
        </tbody>
    </table>
<?
	exit();
}

if($action=="process_name_popup")
{
  	echo load_html_head_contents("Process Name Info","../../", 1, 1, '','1','');
	extract($_REQUEST);
?>
	<script>

		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1);
		});

		var selected_id = new Array(); var selected_name = new Array(); var buyer_id=''; var style_ref_array= new Array();

		function check_all_data()
		 {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function set_all()
		{
			var old=document.getElementById('txt_process_row_id').value;
			if(old!="")
			{
				old=old.split(",");
				for(var k=0; k<old.length; k++)
				{
					js_set_value( old[k] )
				}
			}
		}

		function js_set_value( str )
		{
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );

			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
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
			//id.sort();

			$('#hidden_process_id').val(id);
			$('#hidden_process_name').val(name);
		}
    </script>

</head>
<body>
<div align="center">
	<fieldset style="width:370px;margin-left:10px">
    	<input type="hidden" name="hidden_process_id" id="hidden_process_id" class="text_boxes" value="">
        <input type="hidden" name="hidden_process_name" id="hidden_process_name" class="text_boxes" value="">
        <form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table" >
                <thead>
                    <th width="50">SL</th>
                    <th>Process Name</th>
                </thead>
            </table>
            <div style="width:350px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="332" class="rpt_table" id="tbl_list_search" >
                <?
                    $i=1; $process_row_id=''; $not_process_id_print_array=array();
					$process_id_print_array=array(25,31,32,33,34,39,60,63,64,65,66,67,68,69,70,71,82,83,84,89,90,91,93,94,125,129,132,133,136,137,147,148,149,150,151,127,35,80,176,177,178,180,181,182,183,63,167,168,175,228,186,189,173,251,252,253,88,156);
					$hidden_process_id=explode(",",$txt_process_id);
                    foreach($conversion_cost_head_array as $id=>$name)
                    {
						if(in_array($id,$process_id_print_array))
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							if(in_array($id,$hidden_process_id))
							{
								if($process_row_id=="") $process_row_id=$i; else $process_row_id.=",".$i;
							}
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
								<td width="50" align="center"><?php echo "$i"; ?>
									<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $id; ?>"/>
									<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $name; ?>"/>
								</td>
								<td><p><? echo $name; ?></p></td>
							</tr>
							<?
							$i++;
						}
                    }
                ?>
                    <input type="hidden" name="txt_process_row_id" id="txt_process_row_id" value="<?php echo $process_row_id; ?>"/>
                </table>
            </div>
             <table width="350" cellspacing="0" cellpadding="0" style="border:none" align="center">
                <tr>
                    <td align="center" height="30" valign="bottom">
                        <div style="width:100%">
                            <div style="width:50%; float:left" align="left">
                                <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                            </div>
                            <div style="width:50%; float:left" align="left">
                                <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </form>
    </fieldset>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	set_all();
</script>
</html>
<?
exit();
}

if($action=="order_qnty_popup")
{
	echo load_html_head_contents("order qnty Info", "../../", 1, 1,'','','');
	extract($_REQUEST);

	?>
	<script>
		function fnc_close()
		{
			var tot_row=$('#tbl_qnty tbody tr').length;
			var qnty_qn="";
			var qnty_tot="";
			var qnty_tbl_id="";
			for(var i=1; i<=tot_row; i++)
			{
				if(i*1>1) qnty_qn +=",";
				if(i*1>1) qnty_tbl_id +=",";
				qnty_qn += $("#orderqnty_"+i).val();
				qnty_tbl_id += $("#hiddtblid_"+i).val();
				qnty_tot=qnty_tot*1+$("#orderqnty_"+i).val()*1;
			}
			document.getElementById('hidden_qnty_tot').value=qnty_tot;
			document.getElementById('hidden_qnty').value=qnty_qn;
			document.getElementById('hidd_qnty_tbl_id').value=qnty_tbl_id;
			parent.emailwindow.hide();
		}
	</script>
	<head>
	<body>
        <form name="searchfrm_1"  id="searchfrm_1">
        <div style="margin-left:10px; margin-top:10px" align="center">
            <table class="rpt_table" id="tbl_qnty" border="1" cellpadding="0" cellspacing="0" rules="all" width="400">
                <thead>
                    <th width="150">Order No</th>
                    <th width="150">Production Qty</th>
                </thead>
                <tbody>
					<?
                    $data=explode('_',$data);
                    if($data[1]=="")
                    {
						$i=1;
						$order_name=return_library_array( "select id,order_no from subcon_ord_dtls",'id','order_no');
						//$nameArray=sql_select( "select id,order_no from subcon_ord_dtls where id in ($data[0])");
						$break_order_id=explode(',',$data[0]);
						$break_order_qnty=explode(',',$data[3]);
						for($k=0; $k<count($break_order_id); $k++)
						{
							if ($i%2==0)
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
                   		?>
                            <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
                                <td width="150">
									<? echo $order_name[$break_order_id[$k]]; ?>
                                </td>
                                <td width="150" align="center">
                                    <input type="text" name="orderqnty_<? echo $i; ?>" id="orderqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:140px;"  value="<? echo $break_order_qnty[$k]; ?>"/>
                                    <input type="hidden" name="hiddtblid_<? echo $i; ?>" id="hiddtblid_<? echo $i; ?>" value="">
                                </td>
                                <input type="hidden" name="hidden_qnty_tot" id="hidden_qnty_tot">
                                <input type="hidden" name="hidden_qnty" id="hidden_qnty">
                                <input type="hidden" name="hidd_qnty_tbl_id" id="hidd_qnty_tbl_id">
                            </tr>
							<?
                            $i++;
                        }
					}
					else
					{
						if($data[2]!="")
						{
							$i=1;
							$order_name=return_library_array( "select id,order_no from subcon_ord_dtls",'id','order_no');
							//$nameArray=sql_select( "select id,order_no from subcon_ord_dtls where id in ($data[0])");
							$break_order_id=explode(',',$data[0]);
							$break_order_qnty=explode(',',$data[3]);
							for($k=0; $k<count($break_order_id); $k++)
							{
								if ($i%2==0)
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";
								?>
                                <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
                                    <td width="150">
										<? echo $order_name[$break_order_id[$k]]; ?>
                                    </td>
                                    <td width="150" align="center">
                                        <input type="text" name="orderqnty_<? echo $i; ?>" id="orderqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:140px;"  value="<? echo $break_order_qnty[$k]; ?>"/>
                                        <input type="hidden" name="hiddtblid_<? echo $i; ?>" id="hiddtblid_<? echo $i; ?>" value="">
                                    </td>
                                    <input type="hidden" name="hidden_qnty_tot" id="hidden_qnty_tot">
                                    <input type="hidden" name="hidden_qnty" id="hidden_qnty">
                                    <input type="hidden" name="hidd_qnty_tbl_id" id="hidd_qnty_tbl_id">
                                </tr>
                                <?
                                $i++;
                            }
						}
						else
						{
							$i=1;
							$order_arr=return_library_array( "select id,order_no from subcon_ord_dtls",'id','order_no');
							$nameArray=sql_select( "select id,order_id,quantity from subcon_production_qnty where dtls_id='$data[1]' and order_id in ($data[0])");
							foreach($nameArray as $row)
							{
								if ($i%2==0)
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";
							?>
                                <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
                                    <td width="150">
										<? echo $order_arr[$row[csf('order_id')]]; ?>
                                    </td>
                                    <td width="150" align="center">
                                        <input type="text" name="orderqnty_<? echo $i; ?>" id="orderqnty_<? echo $i; ?>" value="<? echo $row[csf('quantity')]; ?>" class="text_boxes_numeric" style="width:140px;" />
                                        <input type="hidden" name="hiddtblid_<? echo $i; ?>" id="hiddtblid_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
                                    </td>
                                    <input type="hidden" name="hidden_qnty_tot" id="hidden_qnty_tot">
                                    <input type="hidden" name="hidden_qnty" id="hidden_qnty">
                                    <input type="hidden" name="hidd_qnty_tbl_id" id="hidd_qnty_tbl_id">
                                </tr>
								<?
                                $i++;
                            }
						}
					}
					?>
                </tbody>
            </table>
            <table width="400">
                <tr>
                    <td align="center" >
                        <input type="button" name="main_close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
                    </td>
                </tr>
            </table>
        </div>
        </form>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=="fabric_finishing_list_view")
{
	?>
	<div style="width:100%;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="90" align="center">Process</th>
                <th width="60" align="center">Batch No</th>
                <th width="80" align="center">Order No</th>
                <th width="150" align="center">Const. and Compo.</th>
                <th width="70" align="center">Color</th>
                <th width="50" align="center">Gsm</th>
                <th width="60" align="center">Dia/Width</th>
                <th width="80" align="center">Prod. Qty</th>
                <th width="50" align="center">Roll</th>
                <th width="" align="center">Machine</th>
            </thead>
        </table>
    </div>
    <div style="width:100%;max-height:180px; overflow:y-scroll" id="sewing_production_list_view" align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
        <?php
$i = 1;
	$color_arr = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst", 'id', 'batch_no');
	$machine_arr = return_library_array("select id,machine_no from  lib_machine_name", 'id', 'machine_no');
	$sql = "select id, batch_id, order_id, process, fabric_description, color_id, gsm, dia_width, no_of_roll, product_qnty, machine_id from subcon_production_dtls where status_active=1 and mst_id='$data'";
	$sql_result = sql_select($sql);
	foreach ($sql_result as $row) {
		if ($i % 2 == 0) {
			$bgcolor = "#E9F3FF";
		} else {
			$bgcolor = "#FFFFFF";
		}

		$process_id = explode(',', $row[csf('process')]);
		$process_val = '';
		foreach ($process_id as $val) {
			if ($process_val == '') {
				$process_val = $conversion_cost_head_array[$val];
			} else {
				$process_val .= "," . $conversion_cost_head_array[$val];
			}

		}

		?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick="get_php_form_data(<? echo $row[csf('id')]; ?>,'load_php_data_to_form_dtls','requires/subcon_fabric_finishing_production_controller');" style="text-decoration:none; cursor:pointer" >
                    <td width="30" align="center"><? echo $i; ?></td>
                    <td width="90" align="center"><p><? echo $process_val; ?></p></td>
                    <td width="60" align="center"><p><? echo $batch_arr[$row[csf('batch_id')]]; ?></p></td>
					<?
                    $ord_id=$row[csf('order_id')];
                    $order_arr=sql_select("select id,order_no from subcon_ord_dtls where id in($ord_id)");
                    $order_num='';
                    foreach($order_arr as $okey)
                    {
                        if($order_num=="") $order_num=$okey[csf("order_no")]; else $order_num .=",".$okey[csf("order_no")];
                    }
                    ?>
                    <td width="80" align="center"><p><? echo $order_num; ?></p></td>
                    <td width="150" align="center"><p><? echo $row[csf('fabric_description')]; ?></p></td>
                    <td width="70" align="center"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                    <td width="50" align="center"><p><? echo $row[csf('gsm')]; ?></p></td>
                    <td width="60" align="center"><p><? echo $row[csf('dia_width')]; ?></p></td>
                    <td width="80" align="right"><p><? echo $row[csf('product_qnty')]; ?>&nbsp;</p></td>
                    <td width="50" align="right"><p><? echo $row[csf('no_of_roll')]; ?>&nbsp;</p></td>
                    <td width="" align="center"><p><? echo $machine_arr[$row[csf('machine_id')]]; ?></p></td>
                </tr>
			<?php
$i++;
	}
	?>
        </table>
	</div>
	<?
}

if ($action=="load_php_data_to_form_dtls")
{
	//$order_arr=return_library_array("select id,order_no from subcon_ord_dtls",'id','order_no');
	$process_arr=return_library_array("select id,main_process_id from subcon_ord_dtls",'id','main_process_id');
	$color_no_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	
	

	$batch_array=array();
	$batch_id_sql="select id, batch_no, extention_no from pro_batch_create_mst where entry_form=36 and status_active=1 and is_deleted=0";
	$batch_id_sql_result=sql_select($batch_id_sql);
	foreach ($batch_id_sql_result as $row)
	{
		$batch_array[$row[csf("id")]]["batch_no"]=$row[csf("batch_no")];
		$batch_array[$row[csf("id")]]["extention_no"]=$row[csf("extention_no")];
	}

	$sql= "select id, batch_id, width_dia_type, order_id, process, fabric_description, cons_comp_id, color_id, gsm, dia_width, product_qnty, reject_qnty, no_of_roll, floor_id, machine_id, start_hour, start_minutes, start_date, end_hour, end_minutes, end_date from subcon_production_dtls where id='$data'";
	$nameArray=sql_select($sql);
	foreach ($nameArray as $row)
	{
		$orderId_arr[$row[csf("order_id")]]=$row[csf("order_id")];
	}
$poid=implode(",",$orderId_arr);
	
	//echo "select ID,ORDER_NO, CUST_BUYER from subcon_ord_dtls where  status_active=1 and is_deleted=0 and id in($poid)";
	$po_sql=sql_select("select ID,ORDER_NO, CUST_BUYER from subcon_ord_dtls where  status_active=1 and is_deleted=0 and id in($poid)");
	foreach ($po_sql as $row)
	{
		$order_arr[$row["ID"]]=$row["ORDER_NO"];
		$order_buyer_arr[$row["ID"]]=$row["CUST_BUYER"];
	}

	foreach ($nameArray as $row)
	{
		$order_id=explode(',',$row[csf("order_id")]);
		$order_no='';$buyer_cust='';
		foreach($order_id as $okey)
		{
			if($order_no=="") $order_no=$order_arr[$okey]; else $order_no .=",".$order_arr[$okey];
			if($buyer_cust=="") $buyer_cust=$order_buyer_arr[$okey]; else $buyer_cust .=",".$order_buyer_arr[$okey];
		}
		$process_name='';
		$process_id_array=explode(",",$row[csf("process")]);
		foreach($process_id_array as $val)
		{
			if($process_name=="") $process_name=$conversion_cost_head_array[$val]; else $process_name.=",".$conversion_cost_head_array[$val];
		}

		echo "document.getElementById('txt_batch_no').value		 				= '".$batch_array[$row[csf("batch_id")]]["batch_no"]."';\n";
		echo "document.getElementById('txt_batch_ext_no').value		 			= '".$batch_array[$row[csf("batch_id")]]["extention_no"]."';\n";
		echo "document.getElementById('txt_batch_id').value		 				= '".$row[csf("batch_id")]."';\n";
		echo "document.getElementById('hidden_dia_type').value		 			= '".$row[csf("width_dia_type")]."';\n";
		echo "document.getElementById('txt_order_numbers').value		 		= '".$order_no."';\n";
		echo "document.getElementById('txt_cust_buyer').value		 		= '".$buyer_cust."';\n";
		echo "document.getElementById('process_id').value		 				= '".$process_id."';\n";
		echo "document.getElementById('order_no_id').value						= '".$row[csf("order_id")]."';\n";
		//echo "set_multiselect('txt_process_id','0','1','".$row[csf("process")]."','0');\n";
		echo "document.getElementById('txt_process_name').value					= '".$process_name."';\n";
		echo "document.getElementById('txt_process_id').value					= '".$row[csf("process")]."';\n";
		echo "document.getElementById('txt_description').value					= '".$row[csf("fabric_description")]."';\n";
		echo "document.getElementById('comp_id').value							= '".$row[csf("cons_comp_id")]."';\n";
		echo "document.getElementById('txt_color').value		 				= '".$color_no_arr[$row[csf("color_id")]]."';\n";
		echo "document.getElementById('hidden_color_id').value		 			= '".$row[csf("color_id")]."';\n";
		echo "document.getElementById('txt_gsm').value		 					= '".$row[csf("gsm")]."';\n";
		echo "document.getElementById('txt_dia_width').value		 			= '".$row[csf("dia_width")]."';\n";
		echo "document.getElementById('txt_product_qnty').value            		= '".$row[csf("product_qnty")]."';\n";
		echo "document.getElementById('txt_reject_qty').value            		= '".$row[csf("REJECT_QNTY")]."';\n";
		echo "document.getElementById('txt_roll_no').value            			= '".$row[csf("NO_OF_ROLL")]."';\n";
		echo "load_drop_down( 'requires/subcon_fabric_finishing_production_controller', document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_location_name').value, 'load_drop_down_floor', 'floor_td' );\n";
		echo "document.getElementById('cbo_floor_id').value		 			= '".$row[csf("floor_id")]."';\n";
		echo "load_drop_down( 'requires/subcon_fabric_finishing_production_controller', document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_floor_id').value, 'load_drop_machine', 'machine_td');\n";
		echo "show_list_view(document.getElementById('order_no_id').value+'_'+document.getElementById('process_id').value+'_'+document.getElementById('txt_batch_id').value,'show_fabric_desc_listview','list_fabric_desc_container','requires/subcon_fabric_finishing_production_controller','');\n";

		echo "document.getElementById('cbo_machine_id').value		 			= '".$row[csf("machine_id")]."';\n";
		echo "document.getElementById('update_id_dtl').value            		= '".$row[csf("id")]."';\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'subcon_fabric_finishing',1);\n";
	}
	$qry_result=sql_select( "select id, order_id,quantity from subcon_production_qnty where dtls_id='$data'");// and quantity!=0
	$order_qnty=""; $order_id="";
	foreach ($qry_result as $row)
	{
		if($order_qnty=="") $order_qnty=$row[csf("quantity")]; else $order_qnty.=",".$row[csf("quantity")];
		if($order_id=="") $order_id=$row[csf("order_id")]; else $order_id.=",".$row[csf("order_id")];
	}
	echo "document.getElementById('item_order_id').value 	 				= '".$order_id."';\n";
	echo "document.getElementById('txt_receive_qnty').value 	 			= '".$order_qnty."';\n";
	echo "set_button_status(1, '".$_SESSION['page_permission']."', 'subcon_fabric_finishing',1);\n";
	exit();
}

$color_library_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name"  );

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$process_finishing="4";
	
	if($operation!=2)
	{
		if($operation==1) $upsqlCond="and b.id!=$update_id_dtl"; else $upsqlCond="";
		$batchQty=return_field_value("sum(b.batch_qnty) as batchqty","pro_batch_create_mst a, pro_batch_create_dtls b","a.id=b.mst_id and a.id=$txt_batch_id and b.id=$comp_id and a.entry_form=36 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","batchqty");
		//"select sum(b.product_qnty) as prevProdQty from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and b.order_id=$order_no_id and a.entry_form=159 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
		$prevProdQty=return_field_value("sum(b.product_qnty) as prevProdQty","subcon_production_mst a, subcon_production_dtls b","a.id=b.mst_id and b.batch_id=$txt_batch_id and b.cons_comp_id=$comp_id and a.entry_form=292 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $upsqlCond","prevProdQty");
		
		//$issueQtyWithExcess=$issueQty+($issueQty*($allow_per/100));
		
		//echo "10**".$issueQty.'--'.$prevProdQty.'--'.$issueQtyWithExcess; die;
		if((str_replace("'","",$txt_product_qnty)+$prevProdQty)>$batchQty)
		{
			echo "17**Finish production Exceeds Batch Qty.\n Prod. Qty=".str_replace("'","",$txt_product_qnty).",\n Prev. Prod Qty=".$prevProdQty.",\n Batch Qty=".$batchQty;
			//check_table_status( $_SESSION['menu_id'],0);
			die;
		}	
	}

	if ($operation==0)   // Insert Here===============================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		if($db_type==0) $year_cond=" and YEAR(insert_date)"; else if($db_type==2) $year_cond=" and TO_CHAR(insert_date,'YYYY')";

		$new_return_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '','FFE', date("Y",time()), 5, "select id,prefix_no,prefix_no_num from  subcon_production_mst where entry_form=292 and company_id=$cbo_company_id and product_type='$process_finishing' $year_cond=".date('Y',time())." order by id desc ", "prefix_no", "prefix_no_num" ));

		if(str_replace("'",'',$update_id)=="")
		{
			$field_array="id,entry_form,prefix_no,prefix_no_num,product_no,product_type,basis,company_id,location_id,party_id,product_date,prod_chalan_no,remarks,inserted_by,insert_date";
			$id=return_next_id( "id","subcon_production_mst",1);
			$data_array="(".$id.",292,'".$new_return_no[1]."','".$new_return_no[2]."','".$new_return_no[0]."','".$process_finishing."',".$cbo_receive_basis.",".$cbo_company_id.",".$cbo_location_name.",".$cbo_party_name.",".$txt_finishing_date.",".$txt_chal_no.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$rID=sql_insert("subcon_production_mst",$field_array,$data_array,0);
			$return_no=$new_return_no[0];
		}
		else
		{
			$id=str_replace("'",'',$update_id);
			$field_array="product_no*basis*location_id*party_id*product_date*prod_chalan_no*remarks*updated_by*update_date";
			$data_array="".$txt_finishing_id."*".$cbo_receive_basis."*".$cbo_location_name."*".$cbo_party_name."*".$txt_finishing_date."*".$txt_chal_no."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$rID=sql_update("subcon_production_mst",$field_array,$data_array,"id",$update_id,0);
			$return_no=str_replace("'",'',$txt_finishing_id);
		}

		$id1=return_next_id("id","subcon_production_dtls",1);
		//$machine_tbl_id=return_field_value("id","lib_machine_name", "machine_no=$cbo_machine_id and status_active =1 and is_deleted=0" );
		//$color_tbl_id = return_id( str_replace("'","",$txt_color), $color_library_arr, "lib_color", "id,color_name");

		$txt_process_id=explode(",",str_replace("'","",$txt_process_id));
		asort($txt_process_id);
		$txt_process_id=implode(",",$txt_process_id);

		$field_array2="id, mst_id, batch_id, width_dia_type, order_id, product_type, process, fabric_description, cons_comp_id, color_id, gsm, dia_width, product_qnty, reject_qnty, no_of_roll, floor_id, machine_id, inserted_by, insert_date";
		$data_array2="(".$id1.",".$id.",".$txt_batch_id.",".$hidden_dia_type.",".$order_no_id.",'".$process_finishing."','".$txt_process_id."',".$txt_description.",".$comp_id.",".$hidden_color_id.",".$txt_gsm.",".$txt_dia_width.",".$txt_product_qnty.",".$txt_reject_qty.",".$txt_roll_no.",".$cbo_floor_id.",".$cbo_machine_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		//echo "INSERT INTO subcon_production_dtls (".$field_array2.") VALUES ".$data_array2; die;
		$rID2=sql_insert("subcon_production_dtls",$field_array2,$data_array2,0);
		//===========================================================================================================================================
		$product_type='4';
		$data_array3="";
		$order_no=explode(',',str_replace("'","",$item_order_id));
		$receive_qnty=explode(',',str_replace("'","",$txt_receive_qnty));

		for($i=0; $i<count($order_no); $i++)
		{
			$receive_qty='';
			if($receive_qnty[$i]=='')
			{
				$receive_qty=0;
			}
			else
			{
				$receive_qty=$receive_qnty[$i];
			}
			if($id_prod_qnty=="") $id_prod_qnty=return_next_id( "id", "subcon_production_qnty",1); else $id_prod_qnty=$id_prod_qnty+1;
			if($i==0) $add_comma=""; else $add_comma=",";
			$data_array3.="$add_comma(".$id_prod_qnty.",".$id.",".$id1.",".$order_no[$i].",".$receive_qty.",'".$product_type."')";
		}
		$field_array3="id,mst_id,dtls_id,order_id,quantity,product_type";
		if($data_array3!="")
		{
			//echo "INSERT INTO subcon_production_qnty (".$field_array3.") VALUES ".$data_array3; die;
			$rID3=sql_insert("subcon_production_qnty",$field_array3,$data_array3,1);
		}
		//===========================================================================================================================================
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3)
			{
				mysql_query("COMMIT");
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".str_replace("'",'',$id1);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".str_replace("'",'',$id1);
			}
		}
		if($db_type==2)
		{
			if($rID && $rID2 && $rID3)
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".str_replace("'",'',$id1);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".str_replace("'",'',$id1);
			}
		}

		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here==========================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$process_finishing="4";

		$field_array="product_no*basis*company_id*location_id*party_id*product_date*prod_chalan_no*remarks*updated_by*update_date";
		$data_array="".$txt_finishing_id."*".$cbo_receive_basis."*".$cbo_company_id."*".$cbo_location_name."*".$cbo_party_name."*".$txt_finishing_date."*".$txt_chal_no."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID=sql_update("subcon_production_mst",$field_array,$data_array,"id",$update_id,0);

		//$machine_tbl_id=return_field_value("id","lib_machine_name", "machine_no=$cbo_machine_id and status_active =1 and is_deleted=0" );
		//$color_tbl_id = return_id( str_replace("'","",$txt_color), $color_library_arr, "lib_color", "id,color_name");
		$txt_process_id=explode(",",str_replace("'","",$txt_process_id));
		asort($txt_process_id);
		$txt_process_id=implode(",",$txt_process_id);
		$field_array2="batch_id*width_dia_type*order_id*process*fabric_description*cons_comp_id*color_id*gsm*dia_width*product_qnty*reject_qnty*no_of_roll*floor_id*machine_id*updated_by*update_date";
		$data_array2="".$txt_batch_id."*".$hidden_dia_type."*".$order_no_id."*'".$txt_process_id."'*".$txt_description."*".$comp_id."*".$hidden_color_id."*".$txt_gsm."*".$txt_dia_width."*".$txt_product_qnty."*".$txt_reject_qty."*".$txt_roll_no."*".$cbo_floor_id."*".$cbo_machine_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		//echo $data_array2;
		$rID2=sql_update("subcon_production_dtls",$field_array2,$data_array2,"id",$update_id_dtl,0);
		//===========================================================================================================================================
		if(str_replace("'","",$update_id_qnty)!="")
		{
			$update_qnty=explode(',',str_replace("'","",$update_id_qnty));
			$receive_qnty=explode(',',str_replace("'","",$txt_receive_qnty));

			for($i=0; $i<count($update_qnty); $i++)
			{
				if($update_qnty[$i]!=="")
				{
					$update_arr[]=$update_qnty[$i];
					$data_array_up[str_replace("'",'',$update_qnty[$i])] =explode(",",("'".$receive_qnty[$i]."'"));
				}
			}
			$field_array_up="quantity";
			$rID3=execute_query(bulk_update_sql_statement( "subcon_production_qnty","id",$field_array_up,$data_array_up,$update_arr));
		}
		else
		{
			$rID4=execute_query( "delete from subcon_production_qnty where dtls_id=$update_id_dtl",1);
			$data_array3="";
			$order_no=explode(',',str_replace("'","",$item_order_id));
			$receive_qnty=explode(',',str_replace("'","",$txt_receive_qnty));

			for($i=0; $i<count($order_no); $i++)
			{
				$receive_qty='';
				if($receive_qnty[$i]=='')
				{
					$receive_qty=0;
				}
				else
				{
					$receive_qty=$receive_qnty[$i];
				}


				if($id_prod_qnty=="") $id_prod_qnty=return_next_id( "id", "subcon_production_qnty", 1 ); else $id_prod_qnty=$id_prod_qnty+1;
				if($i==0) $add_comma=""; else $add_comma=",";
				$data_array3.="$add_comma(".$id_prod_qnty.",".$update_id.",".$update_id_dtl.",".$order_no[$i].",".$receive_qty.",'".$process_finishing."')";
			}
			$field_array3="id,mst_id,dtls_id,order_id,quantity,product_type";
			if($data_array3!="")
			{
		//echo "INSERT INTO subcon_production_qnty (".$field_array3.") VALUES ".$data_array3; die;
				$rID3=sql_insert("subcon_production_qnty",$field_array3,$data_array3,1);
			}
		}
		//===========================================================================================================================================

		if($db_type==0)
		{
			if($rID && $rID2 && $rID3)
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_finishing_id)."**".str_replace("'",'',$update_id_dtl);
			}
			else if($rID && $rID2)
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_finishing_id)."**".str_replace("'",'',$update_id_dtl);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_finishing_id)."**".str_replace("'",'',$update_id_dtl);
			}
		}
		if($db_type==2)
		{
			if($rID && $rID2 && $rID3 )
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_finishing_id);
			}
			else if($rID && $rID2)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_finishing_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_finishing_id);
			}
		}

		disconnect($con);
 		die;
	}
	else if ($operation==2)   // Delete Here =========================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$rID=sql_delete("subcon_production_dtls",$field_array,$data_array,"id","".$update_id_dtl."",1);

		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_finishing_id)."**".str_replace("'",'',$update_id_dtl);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_finishing_id)."**".str_replace("'",'',$update_id_dtl);
			}
		}
		disconnect($con);
		die;
	}
}

if ($action=="subcon_fabric_finishing_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	$company=$data[0];
	$location=$data[3];
	//print_r ($data);

	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	$color_name_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$machineArr=return_library_array( "select id, machine_no from  lib_machine_name", "id", "machine_no"  );

	$sql=" select id, product_no, basis, location_id, party_id, product_date, prod_chalan_no, remarks from subcon_production_mst where entry_form=292 and product_no='$data[1]'";
	$dataArray=sql_select($sql);
	$com_dtls = fnc_company_location_address($company, $location, 2);

?>
<div style="width:930px;">
    <table width="930" cellspacing="0" align="right" border="0">
        <tr>
            <td colspan="6" align="center" style="font-size:xx-large"><strong><? echo $com_dtls[0]; ?></strong></td>
        </tr>
        <tr class="form_caption">
            <td colspan="6" align="center">
				<?

					echo $com_dtls[1];
					/*$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website, vat_number from lib_company where id=$data[0] and status_active=1 and is_deleted=0");
					foreach ($nameArray as $result)
					{
					?>
						Plot No: <? echo $result[csf('plot_no')]; ?>
						Level No: <? echo $result[csf('level_no')]?>
						Road No: <? echo $result[csf('road_no')]; ?>
						Block No: <? echo $result[csf('block_no')];?>
						City No: <? echo $result[csf('city')];?>
						Zip Code: <? echo $result[csf('zip_code')]; ?>
						Province No: <?php echo $result[csf('province')]; ?>
						Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
						Email Address: <? echo $result[csf('email')];?>
						Website No: <? echo $result[csf('website')];?> <br>
                        <b> Vat No : <? echo $result[csf('vat_number')]; ?></b> <?
					}*/
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:x-large"><strong><u><? echo $data[2]; ?> Note/Challan</u></strong></center></td>
        </tr>
        <tr>
            <td width="160"><strong>Production ID:</strong></td> <td width="175px"><? echo $dataArray[0][csf('product_no')]; ?></td>
            <td width="120"><strong>Receive Basis:</strong></td><td width="175px"><? echo $receive_basis_arr[$dataArray[0][csf('basis')]]; ?></td>
            <td width="125"><strong>Party Name:</strong></td><td width="175px"><? echo  $buyer_arr[$dataArray[0][csf('party_id')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Finishing Date:</strong></td> <td width="175px"><? echo change_date_format($dataArray[0][csf('product_date')]); ?></td>
            <td><strong>Challan No :</strong></td><td colspan="3"><? echo $dataArray[0][csf('prod_chalan_no')];// ?></td>
        </tr>
        <tr>
            <td><strong>Remarks:</strong></td><td colspan="5"><? echo $dataArray[0][csf('remarks')]; ?></td>
        </tr>
    </table>
         <br>
	<div style="width:100%;">
		<table align="right" cellspacing="0" width="930"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="30">SL</th>
                <th width="70" align="center">Batch No</th>
                <th width="80" align="center">Order No</th>
				<th width="80" align="center">Cust Buyer</th>
                <th width="150" align="center">Process</th>
                <th width="160" align="center">Const. Compo.</th>
                <th width="60" align="center">Color</th>
                <th width="60" align="center">GSM</th>
                <th width="90" align="center">Dia/Width</th>
                <th width="60" align="center">Roll</th>
                <th width="80" align="center">Product Qty</th>
                <th width="" align="center">Machine No</th>
            </thead>
   <?
	$mst_id=$dataArray[0][csf('id')];
    $i=1;
	$poArr=return_library_array( "select id, order_no from subcon_ord_dtls", "id", "order_no");
	$batchArr=return_library_array( "select id, batch_no from pro_batch_create_mst", "id", "batch_no");
	
//cust_buyer
	$sqldtls=" select a.id, a.batch_id, a.order_id, a.process, a.fabric_description, a.color_id, a.gsm, a.dia_width, a.product_qnty, a.machine_id, a.no_of_roll from  subcon_production_dtls a where a.mst_id=$mst_id and a.status_active=1 and a.is_deleted=0";

	$sql_result=sql_select($sqldtls);
	foreach($sql_result as $row)
	{
		$order_id=explode(",",$row[csf('order_id')]);
			foreach($order_id as $poid)
			{
				$poIdArr[$poid]=$poid;
			}
	}
	//echo "select id, order_no,cust_buyer from subcon_ord_dtls where status_active=1 and id in(".implode(',',$poIdArr).")";
	$po_sqldtls=sql_select("select id, order_no,cust_buyer from subcon_ord_dtls where status_active=1 and id in(".implode(',',$poIdArr).")");
	foreach($po_sqldtls as $row)
	{
		$poArr[$row[csf('id')]]=$row[csf('order_no')];
		$poBuyerArr[$row[csf('id')]]=$row[csf('cust_buyer')];
	}
	 
	foreach($sql_result as $row)
	{
		if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

			$order_id=explode(",",$row[csf('order_id')]);
			$process=explode(",",$row[csf('process')]);
			$po_no='';$po_buyer=''; $process_arr='';
			//$data=explode('*',$data);

			foreach($order_id as $val)
			{
				if($po_no=='') $po_no=$poArr[$row[csf('order_id')]]; else $po_no.=", ".$poArr[$row[csf('order_id')]];
				if($po_buyer=='') $po_buyer=$poBuyerArr[$row[csf('order_id')]]; else $po_buyer.=", ".$poBuyerArr[$row[csf('order_id')]];
			}
			foreach($process as $val)
			{
				if($process_arr=='') $process_arr=$conversion_cost_head_array[$val]; else $process_arr.=", ".$conversion_cost_head_array[$val];
			}
		?>
			<tr bgcolor="<? echo $bgcolor; ?>">
                <td width="30"><? echo $i; ?></td>
                <td width="70"><p><? echo $batchArr[$row[csf('batch_id')]]; ?></p></td>
                <td width="80"><p><? echo $po_no; ?></p></td>
				<td width="80"><p><? echo $po_buyer; ?></p></td>
                <td width="150"><p><? echo $process_arr; ?></p></td>
                <td width="160"><p><? echo $row[csf('fabric_description')]; ?></p></td>
                <td width="60"><p><? echo $color_name_arr[$row[csf('color_id')]]; ?></p></td>
                <td width="60"><p><? echo $row[csf('gsm')]; ?></p></td>
                <td width="90"><p><? echo $row[csf('dia_width')]; ?></p></td>
                <td width="60" align="right"><? echo number_format($row[csf('no_of_roll')],2,'.',''); $total_roll+=$row[csf('no_of_roll')]; ?></td>
                <td width="80" align="right"><? echo number_format($row[csf('product_qnty')],2,'.',''); $total_qty+=$row[csf('product_qnty')]; ?></td>
                <td width=""><p><? echo $machineArr[$row[csf('machine_id')]]; ?></p></td>
			</tr>
			<?php
$uom_unit = "Kg";
	$uom_gm = "Grams";
	$i++;
}
?>
        	<tr>
                <td align="right" colspan="9" >Total</td>
                <td align="right"><? echo number_format($total_roll,2,'.',''); ?></td>
                <td align="right"><? echo number_format($total_qty,2,'.',''); ?></td>
                <td align="right" >&nbsp;</td>
			</tr>
		</table>
        <br>
		 <?
            echo signature_table(45, $data[0], "930px");
         ?>
	</div>
	</div>
<?
exit();
}
?>
