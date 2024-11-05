<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');

$permission = $_SESSION['page_permission'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];


if($action=="check_conversion_rate")
{
	$data=explode("**",$data);
	if($db_type==0)
	{
		$conversion_date=change_date_format($data[1], "Y-m-d", "-",1);
	}
	else
	{
		$conversion_date=change_date_format($data[1], "d-M-y", "-",1);
	}
	$currency_rate=set_conversion_rate( $data[0], $conversion_date, $data[2] );
	echo "1"."_".$currency_rate;
	exit();
}

if($action=="load_drop_down_attention")
{
	$data=explode("_",$data);
	if($data[1]==5 || $data[1]==3 )
	{
			$supplier_name=return_field_value("contract_person","lib_company","id ='".$data[0]."' and is_deleted=0 and status_active=1");
	}
	else
	{
			$supplier_name=return_field_value("contact_person","lib_supplier","id ='".$data[0]."' and is_deleted=0 and status_active=1");
	}
	echo "document.getElementById('txt_attention').value = '".$supplier_name."';\n";
	exit();
}
if ($action=="load_drop_down_supplier")
{
	//echo $data;die;

	if($data==5 || $data==3){
	   echo create_drop_down( "cbo_supplier_name", 172, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name",1, "-- Select Company --", "", "",0,"" );
	}
	else{
	   echo create_drop_down( "cbo_supplier_name", 172, "select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id  and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name","id,supplier_name", 1, "--Select Supplier--",$selected,"","");

	}
	exit();
}

if($action=="work_order_popup")
{
	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode); 
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(work_order_no)
		{
			document.getElementById('selected_work_order').value=work_order_no;
			parent.emailwindow.hide();
		}
    </script>
    </script>
	</head>

	<body>
		<div align="center" style="width:100%;" >
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table width="800" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
			        <thead>
			            <tr>
			                <th>Company Name</th>
			                <th>Buyer Name</th>
			                <th>Search By</th>
			                <th id="search_by_td_up" width="170">Please Enter FSO No </th>
			                <th colspan="2">Program Date Range</th>
			                <th>&nbsp;</th>
			            </tr>
			        </thead>
			        <tbody>
			            <tr class="general">
			                <td align="center"> <input type="hidden" id="selected_work_order">
			                    <?
			                    if($company_id!="" && $company_id!=0){
									$on=1;
								}else{
									$on=0;
								}
			                    echo create_drop_down( "cbo_company_mst", 172, "select id,company_name from lib_company comp where status_active=1 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", $company_id, "load_drop_down( 'knitting_work_order_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );",$on);
			                    ?>
			                </td>
			                <td id="buyer_td"  align="center">

			                    <?
			                    	if($company_id!="" && $company_id!=0)
			                    	{

			                    		echo create_drop_down("cbo_buyer_name", 172, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", "0", "", 0);
			                    	}else{
			                    		 echo create_drop_down( "cbo_buyer_name", 172, $blank_array,"", 1, "-- Select Buyer --" );
			                    	}
			                   
			                    ?>
			                </td>
			               
			                <th align="center">
								<?
								$search_by_arr = array(1 => "FSO No", 2 => "Fabric Booking No",3=>"Style Ref",4=>"Work Order no");
								$dd = "change_search_event(this.value, '0*0*0*0', '0*0*0*0', '../../../') ";
								echo create_drop_down("cbo_search_by", 140, $search_by_arr, "", 0, "", "", $dd, 0);
								?>
							</th>
			                <td align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
								id="txt_search_common"/>
							</td>
							 
			               
			                <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From" /></td>
			                <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To" /></td>
			                <td align="center">
				                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value, 'create_work_order_search_list_view', 'search_div', 'knitting_work_order_controller', 'setFilterGrid(\'list_view\',-1)') " style="width:100px;" />
				            </td>
			            </tr>
			            <tr>
			                <th align="center" valign="middle" colspan="7"><? echo load_month_buttons(1); ?> </th>
			            </tr>
			        </tbody>
			    </table>
    			<div id="search_div"> </div>
    		</form>
   		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();

}
if($action=="create_work_order_search_list_view")
{
	$data=explode('_',$data);
	//print_r($data);die;
	$search_field_cond = '';
	$search_string=$data[5];
	if ($data[5] != "") {
		if ($data[4] == 1) {
			$search_field_cond = " and LOWER(b.fabric_sales_order_no) like LOWER('%" . $search_string . "%')";
		}
		else if($data[4] == 2)
		{
			$search_field_cond = " and LOWER(b.booking_no) like LOWER('%" . $search_string . "%')";
		}
		else if($data[4] == 3)
		{
			$search_field_cond = " and LOWER(b.style_ref_no) like LOWER('%" . $search_string . "%')";
		
		}
		else
		{
			$search_field_cond = " and LOWER(a.wo_number_prefix_num) like LOWER('%" . $search_string . "%')";	
		}
	}
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and b.buyer_id='$data[1]'"; else $buyer="";//{ echo "Please Select Buyer 

	if($db_type==0)
	{
		if ($data[2]!="" &&  $data[3]!="") $program_date  = "and b.program_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $program_date ="";
	}

	if($db_type==2)
	{
		if ($data[2]!="" &&  $data[3]!="") $program_date  = "and b.program_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $program_date ="";
	}
	if ($db_type == 0) {
		
		$details_arr = return_library_array("select mst_id, group_concat(id order by id desc) as dtls_id from knitting_work_order_dtls where  status_active=1 and is_deleted=0 group by mst_id", 'mst_id', 'id');
	} else if ($db_type == 2) {
		
		$details_arr = return_library_array("select mst_id, LISTAGG(id, ',') WITHIN GROUP (ORDER BY id desc) as id from knitting_work_order_dtls where  status_active=1 and is_deleted=0 group by mst_id", 'mst_id', 'id');
	} 

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');

	
	$supllier_arr = return_library_array("select id,supplier_name from lib_supplier where  status_active =1 and is_deleted=0 and party_type =  '20' or party_type like '20,%' or party_type like '%,20' or party_type like'%,20,%' order by supplier_name", 'id', 'supplier_name');
	$booking_arr = return_library_array("select id, booking_no from wo_booking_mst", 'id', 'booking_no');


   /* $sql= "select a.id,a.wo_no,a.wo_number_prefix,a.wo_number_prefix_num,a.company_id,a.currency_id,a.exchange_rate,a.pay_mode,a.booking_date,a.delivery_date,a.booking_month,a.booking_year,a.supplier_id,a.attention,a.remark,a.booking_percent,sum(b.program_qnty) as program_qnty,sum(b.wo_qty) as working_qnty
		from knitting_work_order_mst a, knitting_work_order_dtls b
		where a.id=b.mst_id  and a.status_active=1 and b.status_active=1 $search_field_cond  $company $buyer $program_date
		group by  select a.id,a.wo_no,a.wo_number_prefix,a.wo_number_prefix_num,a.company_id,a.currency_id,a.exchange_rate,a.pay_mode,a.booking_date,a.delivery_date,a.booking_month,a.booking_year,a.supplier_id,a.attention,a.remark,a.
		";
		*/
	 $sql="select a.id,a.wo_no,a.wo_number_prefix,a.wo_number_prefix_num,a.company_id,a.currency_id,a.exchange_rate,a.pay_mode,a.booking_date,a.delivery_date,a.booking_month,a.booking_year,a.supplier_id,a.attention,a.remark,a.booking_percent,sum(b.program_qnty) as program_qnty,sum(b.wo_qty) as working_qnty
		from knitting_work_order_mst a left join knitting_work_order_dtls b 
		on  a.id=b.mst_id 
		where a.status_active=1 and b.status_active=1  $search_field_cond  $company $buyer $program_date
		group by  a.id,a.wo_no,a.wo_number_prefix,a.wo_number_prefix_num,a.company_id,a.currency_id,a.exchange_rate,a.pay_mode,a.booking_date,a.delivery_date,a.booking_month,a.booking_year,a.supplier_id,a.attention,a.remark,a.booking_percent order by a.wo_no";
	//echo $sql;
	$result = sql_select($sql);
	
	?>
	<table class="rpt_table" id="rpt_tablelist_view" rules="all" width="750" cellspacing="0" cellpadding="0" border="0">
        <thead>
            <tr>
                <th width="50">SL No</th>
                <th width="150">Company name</th>
                <th width="150">Knitting WO No</th>
                <th width="150">Supplier</th>
                <th width="100">Program qnty</th>
                <th>WO qnty</th>
            </tr>
        </thead>
    </table>
    <table  class="rpt_table" id="list_view" rules="all" width="750" cellspacing="0" cellpadding="0" border="0">
        <tbody>
			<?
			$i=0;
			

			foreach($result as $row )
			{
				$i++;
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
				$id=$row[csf('id')];
				$data=$row[csf('id')]."**".$row[csf('wo_no')]."**".$row[csf('company_id')]."**".$row[csf('currency_id')]."**".$row[csf('exchange_rate')]."**".$row[csf('pay_mode')]."**".change_date_format($row[csf('booking_date')])."**".change_date_format($row[csf('delivery_date')])."**".$row[csf('booking_month')]."**".$row[csf('booking_year')]."**".$row[csf('supplier_id')]."**".$row[csf('attention')]."**".$row[csf('remark')]."**".$comp[$row[csf('company_id')]]."**".$supllier_arr[csf('supplier_id')];
				
            ?>
	            <tr onClick="js_set_value('<? echo $data; ?>')" style="cursor:pointer" id="tr_<? echo $i; ?>" height="20" bgcolor="<? echo $bgcolor; ?>">
	                <td width="50"><? echo $i; ?></td>
	                <td width="150"><p><? echo $comp[$row[csf('company_id')]]; ?></p></td>
	                <td width="150" style="word-break:break-all"><? echo $row[csf('wo_no')]; ?></td>
	                <td width="150" style="word-break:break-all"><? echo $supllier_arr[$row[csf('supplier_id')]]; ?></td>
	                <td width="100" ><? echo number_format($row[csf('program_qnty')],2); ?></td>
	                <td ><? echo number_format($row[csf('working_qnty')],2); ?></td>
	            </tr>
            <?
			}
			?>
        </tbody>
    	</table>
	<?
	exit();
}

if($action=="populate_details_data")
{
	$data=explode("**", $data);
	$mst_id=$data[0];
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');

	
	$supllier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	$booking_arr = return_library_array("select id, booking_no from wo_booking_mst", 'id', 'booking_no');
	$sql="select id,mst_id,booking_no,fabric_sales_order_no,program_no,program_date,fabric_desc,machine_dia,machine_gg,stitch_length,color_range,wo_qty,rate,amount,remark_text,buyer_id,style_ref_no,program_qnty,within_group from knitting_work_order_dtls where mst_id=$mst_id and status_active=1 and is_deleted=0";
	$result=sql_select($sql);
	$data="";
	$dtls_id_string='';
	foreach ($result as $row) {
		$dtls_id_string.=$row[csf('id')].",";
	}
	$dtls_id_string=chop($dtls_id_string,",");
	$sql_check="select wo_dtls_id from wo_bill_dtls where status_active=1 and entry_form=421 and wo_dtls_id in ($dtls_id_string)";
	//echo $sql_check;die;
	$res_check=sql_select($sql_check);
	$check_data=array();
	foreach ($res_check as $k) {
		array_push($check_data, $k[csf('wo_dtls_id')]);
	}
	foreach ($result as $row) {
		if($data!="") $data.="**";
		$buyer='';
		if($row[csf('within_group')]==1)
		{
			$buyer=$comp[$row[csf('buyer_id')]];
		}else{
			$buyer=$buyer_arr[$row[csf('buyer_id')]];
		}
		$dtls_id='h';
		if(in_array($row[csf('id')], $check_data))
		{
			$dtls_id=$row[csf('id')];
		}

		$data.=$row[csf('buyer_id')]."__".$buyer."__".$row[csf('booking_no')]."__".$row[csf('style_ref_no')]."__".$row[csf('fabric_sales_order_no')]."__".$row[csf('program_date')]."__".change_date_format($row[csf('program_date')])."__".$row[csf('program_no')]."__".$row[csf('program_qnty')]."__".$row[csf('fabric_desc')]."__".$row[csf('machine_dia')]."__".$row[csf('machine_gg')]."__".$row[csf('color_range')]."__".$color_range[$row[csf('color_range')]]."__".$row[csf('stitch_length')]."__".$row[csf('wo_qty')]."__".$row[csf('within_group')]."&&&&".$row[csf('id')]."__".$row[csf('rate')]."__".$row[csf('amount')]."__".$row[csf('remark_text')]."__".$dtls_id;
	}
	echo $data;
	exit();
}

if ($action=="select_item_pop")
{
  	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
  	//print_r($_REQUEST);die;
  	extract($_REQUEST);
	?>
	<!-- <script>
	function js_set_value(work_order_no)
	{
		document.getElementById('selected_work_order').value=work_order_no;
		parent.emailwindow.hide();
	}
    </script> -->
    <script>

		var selected_id = new Array;
		var fso_array={};
		

		function check_all_data() {
			var tbl_row_count = document.getElementById('table_search_list').rows.length;
			//tbl_row_count = tbl_row_count - 1;

			for (var i = 1; i <= tbl_row_count; i++) {
				js_set_value(i);
			}
		}

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
			}
		}

		function js_set_value(str,supplier,fso_no) {
			if(fso_no in fso_array)
			{
				if(fso_array[fso_no]!=supplier)
				{
					alert('Multiple supplier not allowed in a single Fabric sales order');
				}
				else
				{
					fso_array[fso_no]=supplier;	
					toggle(document.getElementById('tr_' + str), '#FFFFCC');

					if (jQuery.inArray($('#hidden_data_id_' + str).val(), selected_id) == -1) {
						selected_id.push($('#hidden_data_id_' + str).val());
						

					}
					else {
						for (var i = 0; i < selected_id.length; i++) {
							if (selected_id[i] == $('#hidden_data_id_' + str).val()) break;
						}
						selected_id.splice(i, 1);
						
					}
					var id = '';
					var name = '';
					for (var i = 0; i < selected_id.length; i++) {
						if(id!='') id+='***';
						id += selected_id[i];
					}
					$('#selected_work_order').val(id);
				}
			}
			else
			{
				fso_array[fso_no]=supplier;
				toggle(document.getElementById('tr_' + str), '#FFFFCC');

				if (jQuery.inArray($('#hidden_data_id_' + str).val(), selected_id) == -1) {
					selected_id.push($('#hidden_data_id_' + str).val());
					

				}
				else {
					for (var i = 0; i < selected_id.length; i++) {
						if (selected_id[i] == $('#hidden_data_id_' + str).val()) break;
					}
					selected_id.splice(i, 1);
					
				}
				var id = '';
				var name = '';
				for (var i = 0; i < selected_id.length; i++) {
					if(id!='') id+='***';
					id += selected_id[i];
				}
				$('#selected_work_order').val(id);
			}
			

			
		}

	</script>
	</head>

	<body>
		<div align="center" style="width:100%;" >
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table width="1170" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
			        <thead>
			            <tr>
			                <th>Company Name</th>
			                <th>Within Group</th>
			                <th>Buyer Name</th>
			                <th>Sub Con Supplier</th>
			                <th>Search By</th>
			                <th id="search_by_td_up" width="170">Please Enter FSO No</th>
			                <th colspan="2">Program Date Range</th>
			                <th>&nbsp;</th>
			            </tr>
			        </thead>
			        <tbody>
			            <tr class="general">
			                <td align="center"> <input type="hidden" id="selected_work_order">
			                    <?
			                    if($company_id!="" && $company_id!=0){
									$on=1;
								}else{
									$on=0;
								}
			                    echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", $company_id, "load_drop_down( 'knitting_work_order_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );",$on);
			                    ?>
			                </td>
			                <td>
								<?php echo create_drop_down("cbo_within_group", 110, $yes_no, "", 0, "-- Select --", 0, "active_inactive();"); ?>
							</td>
			                <td id="buyer_td"  align="center">
			                   
			                    	 <?
			                    	if($company_id!="" && $company_id!=0)
			                    	{
			                    		echo create_drop_down("cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", "0", "", 0);
			                    	}else{
			                    		 echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- Select Buyer --" );
			                    	}
			                   
			                    ?>
			                    
			                </td>
			                <td>
			                	<?php 

			                		if($supplier_id!="" && $supplier_id!=0){
										$on=1;
									}else{
										$on=0;
									}
			                		echo create_drop_down( "cbo_supplier_name", 140, "select id,supplier_name from lib_supplier where  status_active =1 and is_deleted=0 and party_type =  '20' or party_type like '20,%' or party_type like '%,20' or party_type like'%,20,%' order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $supplier_id, "",$on ); ?></td>
			                <th align="center">
								<?
								$search_by_arr = array(1 => "FSO No", 2 => "Fabric Booking No",3=>"Style Ref");
								$dd = "change_search_event(this.value, '0*0*0', '0*0*0', '../../../') ";
								echo create_drop_down("cbo_search_by", 140, $search_by_arr, "", 0, "", "", $dd, 0);
								?>
							</th>
			                <td align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common"/>
							</td>
			                <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From" /></td>
			                <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To" /></td>
			                <td align="center">
			                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_within_group').value, 'data_search_list_view', 'search_div', 'knitting_work_order_controller', 'setFilterGrid(\'table_search_list\',-1)') " style="width:100px;" /></td>
			            </tr>
			            <tr>
			                <th align="center" valign="middle" colspan="8"><? echo load_month_buttons(1); ?> </th>
			            </tr>
			        </tbody>
			    </table>
    			<div id="search_div"> </div>
    		</form>
   		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=="load_drop_down_buyer")
{
	//echo $data;die;
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	exit();
}

if ($action=="data_search_list_view")
{
	$data=explode('_',$data);
	//print_r($data);die;
	
	$search_field_cond = '';
	$search_string=$data[6];
	$supplier_con='';
	if($data[4]!=0 && $data[4]!="")
	{
		$supplier_con=" and b.knitting_party=$data[4]";
	}
	if ($data[6] != "") {
		if ($data[5] == 1) {
			$search_field_cond = " and LOWER(a.job_no) like LOWER('%" . $search_string . "%')";
		} else if($data[5] == 2) {
			$search_field_cond = " and LOWER(a.sales_booking_no) like LOWER('%" . $search_string . "%')";
		}else{
			$search_field_cond = " and LOWER(a.style_ref_no) like LOWER('%" . $search_string . "%')";
		}
	}
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.buyer_id='$data[1]'"; else $buyer="";//{ echo "Please Select Buyer 

	if($db_type==0)
	{
		if ($data[2]!="" &&  $data[3]!="") $program_date  = "and b.program_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $program_date ="";
	}

	if($db_type==2)
	{
		if ($data[2]!="" &&  $data[3]!="") $program_date  = "and b.program_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $program_date ="";
	}

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');

	$company_short = return_library_array("select id,company_short_name from lib_company", 'id', 'company_short_name');
	$buyer_short = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	

	
	$supllier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	$booking_arr = return_library_array("select id, booking_no from wo_booking_mst", 'id', 'booking_no');
	
	if ($db_type==0) 
	{
		$sales_order_dtls_id="group_concat(b.id) as sales_order_dtls_id";
		
	}
	else
	{
		$sales_order_dtls_id="listagg(b.id, ',') within group (order by b.id) as sales_order_dtls_id";
		
	}
	if($data[7]==1)
	{
		$sql_sale = " SELECT a.id, a.company_id, a.within_group, a.job_no, a.sales_booking_no, a.booking_id, a.buyer_id, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type, sum(b.finish_qty) finish_qty, sum(b.grey_qty) grey_qty, a.is_apply_last_update,a.is_master_part_updated ,b.id as fso_dtls_id,b.pre_cost_fabric_cost_dtls_id from fabric_sales_order_mst a,fabric_sales_order_dtls b,wo_booking_mst c where a.id=b.mst_id and a.sales_booking_no=c.booking_no $active_status_sql $company $buyer $search_field_cond and c.fabric_source in(1,2) and a.booking_without_order=0 group by a.id, a.company_id, a.within_group, a.job_no, a.sales_booking_no, a.booking_id, a.buyer_id, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type,a.is_apply_last_update,a.is_master_part_updated,b.pre_cost_fabric_cost_dtls_id,c.po_break_down_id,b.id
		union all
		select a.id, a.company_id, a.within_group, a.job_no, a.sales_booking_no, a.booking_id, a.buyer_id, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type, sum(b.finish_qty) finish_qty, sum(b.grey_qty) grey_qty,a.is_apply_last_update,a.is_master_part_updated,b.id as fso_dtls_id,b.pre_cost_fabric_cost_dtls_id from fabric_sales_order_mst a,fabric_sales_order_dtls b,wo_non_ord_samp_booking_mst c,wo_non_ord_samp_booking_dtls d where a.id=b.mst_id and a.sales_booking_no=c.booking_no and c.booking_no=d.booking_no $buyer $active_status_sql $company $search_field_cond and (c.fabric_source in(1,2) or d.fabric_source in(1,2))  and a.booking_without_order=1 group by a.id, a.company_id, a.within_group, a.job_no, a.sales_booking_no, a.booking_id, a.buyer_id, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type,a.is_apply_last_update,a.is_master_part_updated,b.pre_cost_fabric_cost_dtls_id,b.id";

	}
	else
	{
		$active_status_sql = "and b.status_active=1 and b.is_deleted=0";
		$sql_sale = "SELECT a.id, a.company_id, a.within_group, a.job_no, a.sales_booking_no, a.booking_id, a.buyer_id, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type, sum(b.finish_qty) finish_qty, sum(b.grey_qty) grey_qty,(select c.po_break_down_id from wo_booking_mst c where a.sales_booking_no = c.booking_no) po_break_down_id,a.is_apply_last_update,a.is_master_part_updated,b.id as fso_dtls_id,b.pre_cost_fabric_cost_dtls_id from fabric_sales_order_mst a,fabric_sales_order_dtls b where a.id=b.mst_id $buyer $active_status_sql $company $search_field_cond  group by a.id, a.company_id, a.within_group, a.job_no, a.sales_booking_no, a.booking_id, a.buyer_id, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type,a.is_apply_last_update,a.is_master_part_updated,b.pre_cost_fabric_cost_dtls_id,b.id order by b.dia";
	}
	

	
	
	$all_sales_booking_arr=array();
	$nameArray = sql_select($sql_sale);
	if(count($nameArray)==0)
	{
		echo "No data ";die;
	}
	$sales_ids='';
	$sales_order_data=array();
	foreach ($nameArray as $value)
	{
		$all_sales_booking_arr[]=$value[csf('sales_booking_no')];
		$sales_booking_arr[] = "'".$value[csf('sales_booking_no')]."'";
		$sales_order_data[$value[csf('fso_dtls_id')]]['grey_qty']+=$value[csf('grey_qty')];
		$sales_order_data[$value[csf('fso_dtls_id')]]['company_id']=$value[csf('company_id')];
		$sales_order_data[$value[csf('fso_dtls_id')]]['within_group']=$value[csf('within_group')];
		$sales_order_data[$value[csf('fso_dtls_id')]]['job_no']=$value[csf('job_no')];
		$sales_order_data[$value[csf('fso_dtls_id')]]['sales_booking_no']=$value[csf('sales_booking_no')];
		$sales_order_data[$value[csf('fso_dtls_id')]]['booking_id']=$value[csf('booking_id')];
		$sales_order_data[$value[csf('fso_dtls_id')]]['buyer_id']=$value[csf('buyer_id')];
		$sales_order_data[$value[csf('fso_dtls_id')]]['style_ref_no']=$value[csf('style_ref_no')];
		$sales_order_data[$value[csf('fso_dtls_id')]]['booking_date']=$value[csf('booking_date')];
	}
	
	if(!empty($all_sales_booking_arr)){
		$job_no_array=array();
		$booking_list=implode(",", array_unique($all_sales_booking_arr));
		$sql_data=sql_select("select a.id, b.buyer_name,c.booking_no from wo_po_break_down a, wo_po_details_master b, wo_booking_dtls c where b.job_no=a.job_no_mst and a.id=c.po_break_down_id and c.booking_no in ('".$booking_list."') and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
			union all
			select 0 as id, buyer_id,booking_no from wo_non_ord_samp_booking_mst where booking_no in ('".$booking_list."') and status_active=1 and is_deleted=0
			");
		foreach ($sql_data as $row) {
			$job_no_array[$row[csf('booking_no')]]['buyer_id']=$row[csf('buyer_name')];
		}
	}
	
	if(!empty($sales_booking_arr))
	{
		$sales_booking = implode(",",$sales_booking_arr);
	    $sales_booking=implode(",",array_filter(array_unique(explode(",",$sales_booking))));
	    if($sales_booking!="")
	    {
	        $sales_booking=explode(",",$sales_booking);  
	        $sales_booking_chnk=array_chunk($sales_booking,999);
	        $sales_booking_cond=" and";
	        foreach($sales_booking_chnk as $dtls_id)
	        {
	        if($sales_booking_cond==" and")  $sales_booking_cond.="(a.booking_no in(".implode(',',$dtls_id).")"; else $sales_booking_cond.=" or a.booking_no in(".implode(',',$dtls_id).")";
	        }
	        $sales_booking_cond.=")";
	        //echo $sales_booking_cond;die;
	    }	
		//$sales_booking_cond = "and a.booking_no in(".implode(",",$sales_booking_arr).")";
	}

	$sales_sqls="select * from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 $sales_ids_cond";

	if ($db_type == 0) {
		$sql_plan = "SELECT a.id,a.mst_id,a.booking_no, a.po_id, a.yarn_desc as job_dtls_id, a.body_part_id, a.fabric_desc, a.gsm_weight, a.dia, a.color_type_id, a.dtls_id as program_no,sum(a.program_qnty) as program_qnty,a.sales_order_dtls_ids,a.pre_cost_fabric_cost_dtls_id,b.recv_number from ppl_planning_entry_plan_dtls a where a.status_active=1 and a.is_deleted=0 and a.is_sales=1 and a.is_revised=0 $sales_booking_cond   group by a.id,a.mst_id,booking_no, a.po_id, a.yarn_desc, a.body_part_id, a.fabric_desc, a.gsm_weight, a.dia, a.color_type_id,a.sales_order_dtls_ids,a.pre_cost_fabric_cost_dtls_id,b.recv_number,a.dtls_id order by a.booking_no";
	} else {
		$sql_plan = "SELECT a.id,a.mst_id,a.booking_no, a.po_id, a.yarn_desc as job_dtls_id, a.body_part_id, a.fabric_desc, a.gsm_weight, a.dia, a.color_type_id, a.dtls_id as program_no,sum(a.program_qnty) as program_qnty,a.sales_order_dtls_ids, a.pre_cost_fabric_cost_dtls_id from ppl_planning_entry_plan_dtls a where a.is_sales=1 and a.is_revised=0 $sales_booking_cond  group by a.id,a.mst_id,a.booking_no, a.po_id, a.yarn_desc, a.body_part_id, a.fabric_desc, a.gsm_weight, a.dia, a.color_type_id,a.sales_order_dtls_ids,a.pre_cost_fabric_cost_dtls_id,a.dtls_id order by a.booking_no";
	}

	
	

	/*
    $sql= "select a.company_id,a.buyer_id,c.sales_booking_no,c.job_no, c.booking_id,b.id as program_no, b.program_date,sum(b.program_qnty) as qnty,b.knitting_party,b.knitting_source,c.style_ref_no,c.within_group,sum(d.grey_qty) as booking_qnty,d.fabric_desc,b.color_range, b.machine_dia, b.machine_gg, b.stitch_length
		from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, fabric_sales_order_mst c ,fabric_sales_order_dtls d
		where a.id=b.mst_id and  a.booking_no=c.sales_booking_no and c.id=d.mst_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and b.knitting_source=3  $search_field_cond  $company $buyer $program_date
			group by a.company_id,a.buyer_id,c.sales_booking_no, c.booking_id,b.id, b.program_date,b.knitting_party,b.knitting_source,c.style_ref_no,c.job_no,c.within_group,d.fabric_desc,b.color_range, b.machine_dia, b.machine_gg, b.stitch_length
		";
	*/

	$result_knitting_dtls=sql_select("select program_no from knitting_work_order_dtls where status_active=1 and is_deleted=0");
	$programNoArr=array();
	foreach ($result_knitting_dtls as $row) {
		array_push($programNoArr, $row[csf('program_no')]);
	}

	

		$sql_program="SELECT b.id as program_no, b.program_date,b.knitting_party,b.knitting_source,b.color_range, b.machine_dia, b.machine_gg, b.stitch_length from ppl_planning_info_entry_dtls b where b.status_active=1 and b.knitting_source=3 $supplier_con  $program_date order by b.id";
		$result_plan=sql_select($sql_program);
		$program_ids='';
		$program_data=array();
		$program_no_arr=array();
		$program_date=array();
		foreach ($result_plan as $row) {
			$program_data[$row[csf('program_no')]]['program_no']=$row[csf('program_no')];
			$program_data[$row[csf('program_no')]]['program_date']=$row[csf('program_date')];
			$program_data[$row[csf('program_no')]]['knitting_party']=$row[csf('knitting_party')];
			$program_data[$row[csf('program_no')]]['knitting_source']=$row[csf('knitting_source')];
			$program_data[$row[csf('program_no')]]['color_range']=$row[csf('color_range')];
			$program_data[$row[csf('program_no')]]['machine_dia']=$row[csf('machine_dia')];
			$program_data[$row[csf('program_no')]]['machine_gg']=$row[csf('machine_gg')];
			$program_data[$row[csf('program_no')]]['stitch_length']=$row[csf('stitch_length')];
			$program_ids.=$row[csf('program_no')].',';
			$program_no_arr[]=$row[csf('program_no')];
			$program_date[]=$row[csf('program_date')];
		}
		
	
	
	
	?>
	<table class="rpt_table" id="rpt_tablelist_view" rules="all" width="1110" cellspacing="0" cellpadding="0" border="0">
        <thead>
            <tr>
                <th width="35">SL No</th>
                <th width="100">Company Name</th>
                <th width="100">Buyer Name</th>
                <th width="120">FSO No</th>
                <th width="120">Fabric Booking No</th>
                <th width="110">Style Ref. No</th>
                <th width="130">Source</th>
                <th width="100">Knitting Company</th>
                <th width="100">Program Date</th>
                <th width="100">Program No</th>
                <th>Program Qty.</th>
            </tr>
        </thead>
	</table>
    
    <table class="rpt_table" id="table_search_list" rules="all" width="1110" cellspacing="0" cellpadding="0" border="0" style="max-height: 300px;">
        <tbody>
			<?
			$i=0;
			
			$nameArray=sql_select($sql_plan);
			//echo $sql_plan;die;
			// echo "<pre>";
			// print_r($sales_order_data);
			// echo "<pre>";
			foreach($nameArray as $row )
			{
				$i++;
				
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				$pr_date=$program_data[$row[csf('program_no')]]['program_date'];
				
				if(in_array($pr_date, $program_date) && !in_array($row[csf('program_no')], $programNoArr))
				{
					$source=$program_data[$row[csf('program_no')]]['knitting_source'];
					$knitting_party=$program_data[$row[csf('program_no')]]['knitting_party'];
					$colorRange=$program_data[$row[csf('program_no')]]['color_range'];
					$machine_dia=$program_data[$row[csf('program_no')]]['machine_dia'];
					$machine_gg=$program_data[$row[csf('program_no')]]['machine_gg'];
					$stitch_length=$program_data[$row[csf('program_no')]]['stitch_length'];
					$sales_order_dtls_ids=$row[csf('sales_order_dtls_ids')];
					$sales_dtls_id=explode(",", $sales_order_dtls_ids);
					$company_id='';
					$buyer_id='';
					$job_no='';
					$style_ref_no='';
					$wo_qnty=0;
					$booking_id='';
					$sales_booking_no='';
					$within_group='';
					foreach ($sales_dtls_id as $dtls) {

						
						$wo_qnty+=$sales_order_data[$dtls]['grey_qty'];
						
						if($job_no=='')
						{
							$job_no=$sales_order_data[$dtls]['job_no'];
						}
						if($style_ref_no=='')
						{
							$style_ref_no=$sales_order_data[$dtls]['style_ref_no'];
						}
						if($sales_booking_no=='')
						{
							$sales_booking_no=$sales_order_data[$dtls]['sales_booking_no'];
						}
						if($company_id=='')
						{
							$company_id=$sales_order_data[$dtls]['company_id'];
						}
						if($buyer_id=='')
						{
							$buyer_id=$sales_order_data[$dtls]['buyer_id'];
						}
						if($booking_id=='')
						{
							$booking_id=$sales_order_data[$dtls]['booking_id'];
						}
						if($within_group=='')
						{
							$within_group=$sales_order_data[$dtls]['within_group'];
						}
					
					}


					if ($source == 1)
						$knit_party = $com[$knitting_party];
					else
						$knit_party = $supllier_arr[$knitting_party];
					if(!empty($sales_booking_no)){
						$booking_no=$sales_booking_no;
						
					}else{
						$booking_no=$booking_arr[$booking_id];
					}
					
					if ($within_group == 1)
					{
						$buyer = $comp[$buyer_id];
						if($buyer=='')
						{
							$buyer = $buyer_short[$buyer_id];
						}
					} else {
						$buyer = $buyer_arr[$buyer_id];
						if($buyer=='')
						{
							$buyer = $buyer_short[$buyer_id];
						}
					}
					
					
					$data=$buyer_id."__".$buyer."__".$booking_no."__".$style_ref_no."__".$job_no."__".$pr_date."__".change_date_format($pr_date)."__".$row[csf('program_no')]."__".$row[csf('program_qnty')]."__".$row[csf('fabric_desc')]."__".$machine_dia."__".$machine_gg."__".$colorRange."__".$color_range[$colorRange]."__".$stitch_length."__".$row[csf('program_qnty')]."__".$within_group;
					
	            ?>
	            <tr onClick="js_set_value('<? echo $i; ?>','<?php echo $knit_party;?>','<?php echo $job_no;?>')" style="cursor:pointer" id="tr_<? echo $i; ?>" height="20" bgcolor="<? echo $bgcolor; ?>">
	                <td width="35"><? echo $i; ?>
	                	<input type="hidden" name="hidden_data" id="hidden_data_id_<?php echo $i ?>"
						value="<? echo $data; ?>"/>
	                </td>
	                
	                <td width="100"><p><? echo $comp[$company_id]; ?></p></td>
	                <td width="100">
	                	</p><? echo $buyer; ?>
	           		 </td>
	                <td width="120" style="word-break:break-all"><? echo $job_no;?></td>
	                <td width="120" style="word-break:break-all"><?php echo $booking_no; ?></td>
	                <td width="110"><? echo $style_ref_no; ?></td>
	                <td width="130" style="word-break:break-all"><? echo $knitting_source[$source]; ?></td>
	                <td width="100"><?php echo $knit_party; ?></td>
	                <td width="100" ><p><? echo change_date_format($pr_date); ?></td>
	                <td width="100"><p><? echo $row[csf('program_no')]; ?></p></td>
	                <td ><? echo number_format($row[csf('program_qnty')],2); ?></td>
	            </tr>
	            <?
				}
			}
			?>
        </tbody>
    </table>
    <table width="1063" cellspacing="0" cellpadding="0" style="border:none" align="center">
		<tr>
			<td align="center" height="30" valign="bottom">
				<div style="width:100%">
					<div style="width:50%; float:left" align="left">
						<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()"/> Check /
						Uncheck All
					</div>
					<div style="width:50%; float:left" align="left">
						<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton"
						value="Close" style="width:100px"/>
					</div>
				</div>
			</td>
		</tr>
	</table>
	<?
	exit();
}
if($action=="check_delete")
{
	$data=explode("**", $data);
	$dtls_id=$data[0];

	$sql="select a.bill_no from wo_bill_mst a, wo_bill_dtls b where a.id=b.mst_id and b.entry_form=421 and b.wo_dtls_id=$dtls_id and a.status_active=1 and b.status_active=1";
	$result=sql_select($sql);
	if(count($result))
	{
		echo $result[0][csf('bill_no')];
		exit();
	}
	echo "";
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	// echo "<pre>";
	// print_r($process);
	// echo "</pre>";
	// die;
	$cbo_company_name=str_replace("'", "", $cbo_company_name);
	$txt_booking_date=str_replace("'", "", $txt_booking_date);
	$txt_delivery_date=str_replace("'", "", $txt_delivery_date);
	
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		if($db_type==0)
		{
			$year_cond=" and YEAR(insert_date)";	
		}
		else if($db_type==2)
		{
			$year_cond=" and TO_CHAR(insert_date,'YYYY')";	
		}
			

		$id = return_next_id_by_sequence("knitting_work_order_mst_seq", "knitting_work_order_mst", $con);

		$new_wo_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'KWO', date("Y",time()), 5, "select id,wo_number_prefix,wo_number_prefix_num from  knitting_work_order_mst where company_id=$cbo_company_name  $year_cond=".date('Y',time())." order by id desc ", "wo_number_prefix", "wo_number_prefix_num" ));
		
		$field_array = "id,wo_number_prefix,wo_number_prefix_num,wo_no,company_id,currency_id,exchange_rate,pay_mode,booking_date,delivery_date,booking_month,booking_year,supplier_id,attention,remark,inserted_by,insert_date,is_deleted";
		
		"id,do_number_prefix,do_number_prefix_num,do_no,company_id,currency_id,exchange_rate,pay_mode,wo_date,delivery_date,booking_month,buyer_id,booking_no,fabric_sales_order_no,po_breakdown_id,style_ref_no,dyeing_source,dyeing_compnay_id,attention,remark,inserted_by,insert_date";
		
		$data_array = "(" . $id . ",'" . $new_wo_number[1] . "'," . $new_wo_number[2] . ",'" . $new_wo_number[0] . "'," . $cbo_company_name . "," . $cbo_currency . "," . $txt_exchange_rate . "," . $cbo_pay_mode . "," . $txt_booking_date . "," . $txt_delivery_date . "," . $cbo_booking_month . "," . $cbo_booking_year . "," . $cbo_supplier_name . "," . $txt_attention . "," . $txt_remark . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',0)";
		//echo "10**insert into barcode_issue_to_finishing_mst (".$field_array.") values ".$data_array;die;

			//echo "insert into knitting_work_order_mst($field_array)values".$data_array;die;
		    $rID=sql_insert("knitting_work_order_mst",$field_array,$data_array,0);
			

		if($db_type==0)
		{
			if($rID){
				mysql_query("COMMIT");
				echo "0**".$new_wo_number[0]."**".$id;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$new_wo_number[0];
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID){
				oci_commit($con);
				echo "0**".$new_wo_number[0]."**".$id;
			}
			else{
				oci_rollback($con);
				echo "10**".$new_wo_number[0];
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
		$field_array = "company_id*currency_id*exchange_rate*pay_mode*booking_date*delivery_date*booking_month*booking_year*supplier_id*attention*remark*updated_by*update_date";
		
		$data_array = "". $cbo_company_name . "*" . $cbo_currency . "*" . $txt_exchange_rate . "*" . $cbo_pay_mode . "*" . $txt_booking_date . "*" . $txt_delivery_date . "*" . $cbo_booking_month . "*" . $cbo_booking_year . "*" . $cbo_supplier_name . "*" . $txt_attention . "*" . $txt_remark . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";


		 //=======================================================================================================
		 $rID=sql_update("knitting_work_order_mst",$field_array,$data_array,"id","".$update_id."",0);
		if($db_type==0)
		{
			if($rID){
				mysql_query("COMMIT");
				echo "1**".str_replace("'","",$txt_work_order_no)."**".$update_id;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_work_order_no);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID){
				oci_commit($con);
				echo "1**".str_replace("'","",$txt_work_order_no)."**".$update_id;
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_work_order_no);
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
		$delete_check_sql="select a.bill_no, b.wo_dtls_id from wo_bill_mst a, wo_bill_dtls b, knitting_work_order_mst c,knitting_work_order_dtls d where a.id=b.mst_id and c.id=d.mst_id and   b.wo_dtls_id =d.id and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and b.entry_form=421 and c.id=$update_id";

		$delete_check_result=sql_select($delete_check_sql);
		if(count($delete_check_result))
		{
			echo "112**".$delete_check_result[0][csf('a.bill_no')];
			disconnect($con);
			die;
		}
		
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$rID=sql_delete("knitting_work_order_mst",$field_array,$data_array,"id","".$update_id."",1);
		if($db_type==0)
		{
			if($rID){
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$txt_work_order_no)."**".$update_id;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_work_order_no);
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($rID){
				oci_commit($con);
				echo "2**".str_replace("'","",$txt_work_order_no)."**".$update_id;
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_work_order_no);
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="save_update_delete_details")
{
	$process = array(&$_POST);
	// echo "<pre>";
	// print_r($process);
	// echo "</pre>";
	extract(check_magic_quote_gpc( $process ));

	$update_id=str_replace("'", "", $update_id);

	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
			

		$field_array = "id,mst_id,booking_no,fabric_sales_order_no,program_no,program_date,fabric_desc,machine_dia,machine_gg,stitch_length,color_range,wo_qty,rate,amount,remark_text,buyer_id,style_ref_no,within_group,program_qnty,inserted_by,insert_date,is_deleted";
		$data_array_dtls="";
		for ($j = 1; $j <= $tot_row; $j++) 
		{
			$dtls_id = return_next_id_by_sequence("knitting_work_order_dtls_seq", "knitting_work_order_dtls", $con);
			$buyer="buyer_".$j;
			$styleref="styleref_".$j;
			$booking="booking_".$j;
			$jobno="jobno_".$j;
			$programdate="programdate_".$j;
			$programno="programno_".$j;
			$description="description_".$j;
			$dia="dia_".$j;
			$machinegg="machinegg_".$j;
			$colorrange="colorrange_".$j;
			$stitchlength="stitchlength_".$j;
			$programqnty="programqnty_".$j;
			$bookingqnt="bookingqnt_".$j;
			$rate="rate_".$j;
			$amount="amount_".$j;
			$remark="remark_".$j;
			$withingroup="withingroup_".$j;
			if($$withingroup=="")
			{
				$withing=0;
			}else{
				$withing=$$withingroup;
			}

			if($db_type==0)
			{
				if ($$programdate!="") $program_date  = change_date_format($$programdate, "yyyy-mm-dd", "-");
			}

			if($db_type==2)
			{
				if ($$programdate!="") $program_date  = change_date_format($$programdate, "yyyy-mm-dd", "-",1);
			}
			
			if ($data_array_dtls != "") $data_array_dtls .= ",";
			$data_array_dtls .= "(" . $dtls_id . "," . $update_id . ",'" . $$booking . "','" . $$jobno . "'," . $$programno . ",'" . $program_date . "','" . $$description. "','" . $$dia .  "','".$$machinegg. "','" . $$stitchlength .  "','".$$colorrange. "'," . $$bookingqnt .  ",".$$rate. "," . $$amount. ",'" . $$remark. "','" . $$buyer. "','" . $$styleref."'," .$withing."," .$$programqnty."," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',0)";
			
			
		}

		//echo "10**insert into barcode_issue_to_finishing_mst (".$field_array.") values ".$data_array;die;

			//echo "insert into knitting_work_order_dtls($field_array)values".$data_array_dtls;die;

		    $rID=sql_insert("knitting_work_order_dtls",$field_array,$data_array_dtls,0);
		    
		   
			

		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "0**".$dtls_id;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$dtls_id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "0**".$dtls_id;
			}
			else{
				oci_rollback($con);
				echo "10**".$dtls_id;
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

		 	
		$pre_dtls_res=sql_select("select * from knitting_work_order_dtls where mst_id=$update_id and status_active=1 and is_deleted=0");
		$pre_ids=array();
		$dtls_id_string='';
		foreach ($pre_dtls_res as $row) 
		{
			array_push($pre_ids, $row[csf('id')]);
			$dtls_id_string.=$row[csf('id')].",";
		}
		$dtls_id_string=chop($dtls_id_string,",");
		$delete_check_sql="select a.bill_no, b.wo_dtls_id from wo_bill_mst a, wo_bill_dtls b where a.id=b.mst_id and  b.wo_dtls_id in ($dtls_id_string) and b.status_active=1 and b.is_deleted=0 and b.entry_form=421 and a.status_active=1 and a.is_deleted=0";
		//echo $delete_check_sql;die;
		$delete_check_result=sql_select($delete_check_sql);
		$dtls_id_in_wo_bill=array();
		$delete_data=array();
		foreach ($delete_check_result as $row) {
			array_push($delete_check_result, $row[csf('wo_dtls_id')]);
			$delete_data[$row[csf('wo_dtls_id')]]=$row[csf('bill_no')];
		}

		$data_array_dtls="";
		
		
		$dtls_id_cur=array();
		$field_array_status = "updated_by*update_date*status_active*is_deleted";
		$data_array_status = $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*0*1";
		$data_update_status ='';
		$rID1=true;
		for ($j = 1; $j <= $tot_row; $j++) 
		{
			
			$buyer="buyer_".$j;
			$detailsId="detailsId_".$j;
			$styleref="styleref_".$j;
			$booking="booking_".$j;
			$jobno="jobno_".$j;
			$programdate="programdate_".$j;
			$programno="programno_".$j;
			$description="description_".$j;
			$dia="dia_".$j;
			$machinegg="machinegg_".$j;
			$colorrange="colorrange_".$j;
			$stitchlength="stitchlength_".$j;
			$programqnty="programqnty_".$j;
			$bookingqnt="bookingqnt_".$j;
			$rate="rate_".$j;
			$amount="amount_".$j;
			$remark="remark_".$j;
			$withingroup="withingroup_".$j;
			if($$withingroup=="")
			{
				$withing=0;
			}else{
				$withing=$$withingroup;
			}

			if($db_type==0)
			{
				if ($$programdate!="") $program_date  = change_date_format($$programdate, "yyyy-mm-dd", "-");
			}

			if($db_type==2)
			{
				if ($$programdate!="") $program_date  = change_date_format($$programdate, "yyyy-mm-dd", "-",1);
			}
			array_push($dtls_id_cur, $$detailsId);

			if(in_array($$detailsId, $pre_ids))
			{
				
			
				$field_update_array = "rate*amount*remark_text";
				$data_update_status="'".$$rate."'*'".$$amount."'*'".$$remark."'";
				$rID1=sql_update("knitting_work_order_dtls", $field_update_array, $data_update_status, "id", $$detailsId, 0);
				
					//$rID3 = sql_update("dyeing_work_order_dtls", $field_update_array, $data_update_status, "id", $update_id, 1);
				if($rID1==false)
				{
					break;
				}
				
			}
			else
			{
				$dtls_id = return_next_id_by_sequence("knitting_work_order_dtls_seq", "knitting_work_order_dtls", $con);
				if ($data_array_dtls != "") $data_array_dtls .= ",";
				$data_array_dtls .= "(" . $dtls_id . "," . $update_id . ",'" . $$booking . "','" . $$jobno . "'," . $$programno . ",'" . $program_date . "','" . $$description. "','" . $$dia .  "','".$$machinegg. "','" . $$stitchlength .  "','".$$colorrange. "'," . $$bookingqnt .  ",".$$rate. "," . $$amount. ",'" . $$remark. "','" . $$buyer. "','" . $$styleref."'," .$withing."," .$$programqnty."," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',0)";
			}
			
			
			
			
		}
		$rID2=true;
		foreach ($pre_ids as  $value)
		{
			$statusChange=true;
			if(!in_array($value,$dtls_id_cur)){
				if(in_array($value, $delete_check_result))
				{
					if($db_type==0)
					{
						mysql_query("ROLLBACK");
						echo "111**".$delete_data[$value];
					
					}
					else if($db_type==2 || $db_type==1 )
					{
						
						oci_rollback($con);
						echo "111**".$delete_data[$value];
						
					}
					disconnect($con);
					die;
				}else{
					$statusChange = sql_multirow_update("knitting_work_order_dtls", $field_array_status, $data_array_status, "id", $value, 0);
				}
				
			}
			if($statusChange==false){
				$rID2=false;
				break;
			}
			
		}
		$field_array = "id,mst_id,booking_no,fabric_sales_order_no,program_no,program_date,fabric_desc,machine_dia,machine_gg,stitch_length,color_range,wo_qty,rate,amount,remark_text,buyer_id,style_ref_no,within_group,program_qnty,inserted_by,insert_date,is_deleted";

			//echo "10**insert into dyeing_work_order_mst (".$field_array.") values ".$data_array;;

			//echo "insert into dyeing_work_order_dtls($dtls_field_array)values".$data_array_dtls;die;
		$rID3=true;
		if($data_array_dtls!=""){
		 // echo "10*insert into dyeing_work_order_dtls($dtls_field_array)values".$data_array_dtls;die;
		
		 $rID3=sql_insert("knitting_work_order_dtls",$field_array,$data_array_dtls,0);
		}

		$txt_dyeing_wo_order_no=str_replace("'", "", $txt_dyeing_wo_order_no);
		
		if($db_type==0)
		{
			if($rID1 && $rID2 && $rID3){
				mysql_query("COMMIT");
				echo "1**".str_replace("'","",$update_id)."**".$txt_dyeing_wo_order_no;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**". $rID1 ."**". $rID2 ."**". $rID3;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID1 && $rID2 && $rID3){
				oci_commit($con);
				echo "1**".str_replace("'","",$update_id)."**".$txt_dyeing_wo_order_no;
			}
			else{
				oci_rollback($con);
				echo "10**". $rID1 ."**". $rID2 ."**". $rID3;
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

		$delete_check_sql="select a.bill_no, b.wo_dtls_id from wo_bill_mst a, wo_bill_dtls b, knitting_work_order_mst c,knitting_work_order_dtls d where a.id=b.mst_id and c.id=d.mst_id and   b.wo_dtls_id =d.id and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and b.entry_form=421 and c.id=$update_id";

		$delete_check_result=sql_select($delete_check_sql);
		if(count($delete_check_result))
		{
			echo "112**".$delete_check_result[0][csf('a.bill_no')];
			die;
		}
		
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$rID=sql_delete("knitting_work_order_dtls",$field_array,$data_array,"mst_id","".$update_id."",1);
		//$rID2=sql_delete("knitting_work_order_mst",$field_array,$data_array,"id","".$update_id."",1);
		if($db_type==0)
		{
			if($rID){
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$update_id)."**".$update_id;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$update_id);
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($rID){
				oci_commit($con);
				echo "2**".str_replace("'","",$update_id)."**".$update_id;
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$update_id);
			}
		}
		disconnect($con);
		die;
	}
}

if ($action == "print_knitting_work_order") 
{
	echo load_html_head_contents("Knitting Work Order", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$data = explode("**", $data);

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_library = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[1]'","image_location");
	$country_arr = return_library_array("select id,country_name from lib_country", 'id', 'country_name');
	$supplier_arr = return_library_array("select id,supplier_name from lib_supplier", 'id', 'supplier_name');
	$floor_arr = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name');

	$address="";
?>
	<div style="margin-left:20px">
		<table width="100%" cellpadding="0" cellspacing="0" >
			<tr>
				<td width="180" align="right">
					<img  src='../../<? echo $image_location; ?>' height='100%' width='100%' />
				</td>
				<td>
					<table width="800" cellspacing="0" align="center">
						<tr>
							<td align="center" style="font-size:x-large"><strong ><? echo $company_library[$data[1]]; ?></strong></td>
						</tr>
						<tr class="">
							<td  align="center" style="font-size:14px">
								<?
								$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, contact_no, country_id, province, city, zip_code, contact_no, email, website, vat_number from lib_company where id='$data[1]' and status_active=1 and is_deleted=0");
								foreach ($nameArray as $result)
								{
									?>
									Plot No: <? echo $result[csf('plot_no')]; ?>
									Level No: <? echo $result[csf('level_no')] ?>
									Road No: <? echo $result[csf('road_no')]; ?>
									Block No: <? echo $result[csf('block_no')]; ?>
									City Name: <? echo $result[csf('city')]; ?>
									Zip Code: <? echo $result[csf('zip_code')]; ?>
									Province No: <?php echo $result[csf('province')]; ?>
									Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
									Email Address: <? echo $result[csf('email')]; ?>
									Website No: <? echo $result[csf('website')];

									$address="Plot No: ".$result[csf('plot_no')]." Level No: ".$result[csf('level_no')]." Road No: ".$result[csf('road_no')]." Block No: ".$result[csf('block_no')]." City Name: ".$result[csf('city')]." Zip Code: ".$result[csf('zip_code')]." Province No: ".$result[csf('province')]." Country: ".$country_arr[$result[csf('country_id')]]." Email Address: ".$result[csf('email')]." Website No: ".$result[csf('website')];
								}
								?>
							</td>
						</tr>
						<tr>
							<td align="center" style="font-size:18px"><b><u>Knitting Work Order</u></b></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>

		<?
		$cur='';
		$dataArray = sql_select("select a.id,a.wo_no,a.wo_number_prefix,a.wo_number_prefix_num,a.company_id,a.currency_id,a.exchange_rate,a.pay_mode,a.booking_date,a.delivery_date,a.booking_month,a.booking_year,a.supplier_id,a.attention,a.remark,a.booking_percent
		from knitting_work_order_mst a
		where a.status_active=1 and id=$data[0]");

		$salesOrderNo=$dataArray[0][csf('wo_no')];

		$currency_id=$dataArray[0][csf('currency_id')];
		?>
		<table width="1270" style="margin-top:10px;margin-right: 10px;">
				<tr>
				<td><b>WO No:</b></td>
				<td><? echo $dataArray[0][csf('wo_no')]; ?></td>
				<td>
					<b>Company Name:</b> 
				</td>
				<td><? echo $company_library[$dataArray[0][csf('company_id')]]; ?></td>
				
				
			</tr>
			<tr>
				<td><b>Supplier Name:</b></td>
				<td><? echo $supplier_arr[$dataArray[0][csf('supplier_id')]]; ?></td>
				<td>
					<?
                    
                    $party_add=$dataArray[0][csf('supplier_id')];
                   // echo "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$party_add";die;
                    $nameArray=sql_select( "select address_1 from  lib_supplier where id=$party_add"); 
                     $address="";
                    foreach ($nameArray as $result)
                    { 
                        $address="";
                        if($result!="") $address=$result[csf('address_1')];
                    }
                        
                    
                ?>
					
					<b>Address:</b> 
				</td>
				<td><? echo $address; ?></td>
				
			</tr>
			<tr>
				<td><b>WO Date:</b></td>
				<td><? echo change_date_format($dataArray[0][csf('booking_date')]); ?></td>
				<td>
					<b>Delivery Date :</b> 
				</td>
				<td><? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
				
			</tr>
			 
		
			
		<tr>
			<td><b>Currency:</b></td>
			 <td><? echo $currency[$dataArray[0][csf('currency_id')]];
				$cur=$currency[$dataArray[0][csf('currency_id')]];
			 ?></td>
			<td><b>Pay Mode:</b></td>
			<td><? echo $pay_mode[$dataArray[0][csf('pay_mode')]]; ?></td>
		</tr>
		<tr>
			<td ><b>Attention : </b></td>
			<td colspan="3"> <?php echo $dataArray[0][csf('attention')] ?></td>
		</tr>
		<tr>
			<td ><b>Remarks : </b> </td>
			<td colspan="3"><?php echo $dataArray[0][csf('remark')] ?></td>
		</tr>
	</table>
	<?php $tot_wo_qnty=0;
				$tot_rate=0;
				$tot_amount=0; ?>
	<table style="margin-top:10px;" width="1500" border="1" rules="all" cellpadding="3" cellspacing="0"
	class="rpt_table">
		
		<thead>
			<th width="30">SL No</th>
			<th width="100">Program Date</th>
			<th width="100">Program no</th>
			<th width="180">Fabric Description</th>
			<th width="80">M/C Dia x Gauge</th>
			<th width="40">S.L</th>
			<th width="100">Color Range</th>
			<th width="80">WO Qty.</th>
			<? 
				if($data[2]==1)
				{ 
					?>
					<th width="80">Rate</th>
					<th width="90">Amount</th>
					<? 
				}
			?>
			<th width="150">Remarks</th>
		</thead>
		<tbody>
			<? 
				$result_details=sql_select("select * from knitting_work_order_dtls where status_active=1 and is_deleted=0 and mst_id=$data[0]");
				
				
				$grouping="";
				$i=0;
				$rate=0;
				$amount=0;
				$wo_qnty=0;
				
				foreach ($result_details as $row) {
						
					$grouping_check=$row[csf('buyer_id')]."***".$row[csf('style_ref_no')]."***".$row[csf('booking_no')]."***".$row[csf('fabric_sales_order_no')];
					if($grouping!=$grouping_check)
					{
						
						if($i>0){?>

							<tr bgcolor="#ddd">
								<td colspan="7" align="right"><b>Total</b></td>
								 <td align="right"><? echo number_format($wo_qnty, 2); ?></td>
					            <?php 
					            	if($data[2]==1)
					            	{
					            		?>
					            		<td align="right"></td>
					            		<td align="right"><? echo number_format($amount, 2); ?></td>
					            	<?	}
					            ?>
					            
					            <td align="left"></td>
							</tr>
							<tr bgcolor="#efefef">
								<td colspan="<?php echo $data[2]==1 ? 11:9;?>" align="center">
									<?php 

									if($row[csf('within_group')]==1)
									{
										$buyer=$company_library[$row[csf('buyer_id')]];
									}else{
										$buyer=$buyer_arr[$row[csf('buyer_id')]];
									}
									echo "Buyer - ".$buyer." , Style Ref. No - ".$row[csf('style_ref_no')]." , Fab. Booking No - ".$row[csf('booking_no')] .", FSO No - ".$row[csf('fabric_sales_order_no')]; ?>
								</td>
							</tr>
						<?
							$rate=0;
							$amount=0;
							$wo_qnty=0;

						}else{?>
							<tr bgcolor="#ddd">
								<td colspan="<?php echo $data[2]==1 ? 11:9;?>" align="center">

									<?php
									if($row[csf('within_group')]==1)
									{
										$buyer=$company_library[$row[csf('buyer_id')]];
									}else{
										$buyer=$buyer_arr[$row[csf('buyer_id')]];
									}

									 echo "Buyer - ".$buyer." , Style Ref. No - ".$row[csf('style_ref_no')]." , Fab. Booking No - ".$row[csf('booking_no')] .", FSO No - ".$row[csf('fabric_sales_order_no')]; ?>
								</td>
							</tr>
						<?}
					}
					$i++;
					if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td align="center"><? echo $i; ?></td>
						<td><? echo change_date_format($row[csf('program_date')]);?></td>
						<td><? echo $row[csf('program_no')];?></td>
						<td><? echo $row[csf('fabric_desc')];?></td>
						<td><? echo $row[csf('machine_dia')]." X ".$row[csf('machine_gg')];?></td>
						<td><? echo $row[csf('stitch_length')];?></td>
						<td><? echo $color_range[$row[csf('color_range')]];?></td>
						<td align="right"><? echo number_format($row[csf('wo_qty')],2);?></td>
						
						<?php 
			            	if($data[2]==1)
			            	{
			            		$rate+=$row[csf('rate')];
								$amount+=$row[csf('amount')];
								$tot_rate+=$row[csf('rate')];
								$tot_amount+=$row[csf('amount')];
			            		?>
			            	<td align="right"><? echo number_format($row[csf('rate')],2);?></td>
			            	<td align="right"><? echo number_format($row[csf('amount')],2);?></td>
			            	<?	}
			            ?>
			            <td align="center"><? echo $row[csf('remark_text')];?></td>
					</tr>
					<? 
					
					$wo_qnty+=$row[csf('wo_qty')];
					$grouping=$grouping_check;
					$tot_wo_qnty+=$row[csf('wo_qty')];
					
					
				}

				if($i>0)
				{	
					?>

					<tr bgcolor="#ddd">
						<td colspan="7" align="right"><b>Total</b></td>
						 <td align="right"><? echo number_format($wo_qnty, 2); ?></td>
			            <?php 
			            	if($data[2]==1)
			            	{
			            		?>
			            		<td align="right"></td>
			            		<td align="right"><? echo number_format($amount, 2); ?></td>
			            	<?	}
			            ?>
			            
			            <td align="left"></td>
					</tr>
					<?
				}
				?>
		</tbody>

		<tfoot>
            <th colspan="7" align="right"><b>Total</b></th>
            <th align="right"><? echo number_format($tot_wo_qnty, 2); ?></th>
            <?php 
            	if($data[2]==1)
            	{
            		?>
            		<th align="right"></th>
            		<th align="right"><? echo number_format($tot_amount, 2); ?></th>
            	<?	}
            ?>
            
            <th align="left"></th>
        </tfoot>
    </table>
    <br>
    <?php 

    if($data[2]==1)
	{
     ?>
	    <table>
	    	<tr>

	    		<?php 
	    			
	    			
					if($currency_id==1){ $paysa_sent="Paisa"; } else if($currency_id==2){ $paysa_sent="CENTS"; }
					$tot_amount=number_format($tot_amount,2);
	    		 ?>
	    		<td style="font-size: 16px;">
	    			<b>WO Amount (in word) : <? echo number_to_words($tot_amount,$cur,$paysa_sent); ?></b>
	    		</td>
	    	</tr>
	    </table>
	<?php }?>
   
		
			<? /*echo get_spacial_instruction($data[0],400,412); */?>
		<?php echo get_spacial_instruction($salesOrderNo,600); ?>
	
	
    <?
    //echo signature_table(412, $data[0], "1180px");
    echo signature_table(249, $data[1], "1500px");
    ?>
</div>
<?
exit();
}


?>