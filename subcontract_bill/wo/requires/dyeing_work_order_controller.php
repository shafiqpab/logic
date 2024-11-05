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
// if ($action=="load_drop_down_buyer")
// {
// 	//echo $data;die;
// 	echo create_drop_down( "cbo_buyer_name", 172, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
// 	exit();
// }
if ($action == "load_drop_down_buyer") {
	$data = explode("_", $data);
	$company_id = $data[1];

	
	if ($data[0] == 1) {
		//echo "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.id=$company_id order by comp.company_name";die;
		echo create_drop_down("cbo_buyer_name", 162, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0  order by comp.company_name", "id,company_name", 0, "-- Select Buyer --", $data[1], "", 0);
	} else if ($data[0] == 2) {
		echo create_drop_down("cbo_buyer_name", 162, "select buy.id, buy.buyer_name from lib_buyer buy where status_active=1  order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "", 0);
	}
	
	exit();
}
if($action=="load_drop_down_knitting_com")
{
	$data = explode("**",$data);
	$company_id=$data[1];
	//echo $company_cond ;die;

	if($data[0]==1)
	{
		echo create_drop_down( "cbo_dyeing_comp", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "-- Select --", $company_id, "","" );
	}
	else if($data[0]==3)
	{
		echo create_drop_down( "cbo_dyeing_comp", 152, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "" );
	}
	else
	{
		echo create_drop_down( "cbo_dyeing_comp", 152, $blank_array,"",1, "-- Select --", 0, "" );
	}
	exit();
}
if($action=="check_delete")
{
	$data=explode("**", $data);
	$dtls_id=$data[0];

	$sql="select a.bill_no from wo_bill_mst a, wo_bill_dtls b where a.id=b.mst_id and b.entry_form=422 and b.wo_dtls_id=$dtls_id and b.status_active=1 and a.status_active=1";
	$result=sql_select($sql);
	if(count($result))
	{
		echo $result[0][csf('bill_no')];
		exit();
	}
	echo "";
	exit();
}
if($action=="dyeing_work_order_popup")
{
	echo load_html_head_contents("Dyeing Work Order","../../../", 1, 1, $unicode); 
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(work_order_no)
		{
			document.getElementById('selected_work_order').value=work_order_no;
			parent.emailwindow.hide();
		}

		function fnc_show()
		{
			if($("#txt_search_common").val().trim() =="")
			{
				if (form_validation('cbo_company_mst*txt_date_from*txt_date_to','Company Name*From Date*To Date')==false)
				{
					return;
				}
			}
			show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_dyeing_source').value+'_'+document.getElementById('cbo_dyeing_comp').value+'_'+document.getElementById('within_group').value, 'create_work_order_search_list_view', 'search_div', 'dyeing_work_order_controller', 'setFilterGrid(\'list_view\',-1)');
		}
    </script>
    </script>
	</head>

	<body>
		<div align="center" style="width:100%;" >
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table width="1078" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
			        <thead>
			            <tr>
			                <th width="140">Company Name</th>
			                <th width="120">Buyer Name</th>
			                <th width="120">Dyeing Source</th>
			                <th width="140">Dyeing Company</th>
			                <th width="120">Search By</th>
			                <th id="search_by_td_up" width="140">Please Enter FSO No</th>
			                <th width="140" colspan="2">WO Date Range</th>
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
			                    echo create_drop_down( "cbo_company_mst", 130, "select id,company_name from lib_company comp where status_active=1 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", $company_id, "load_drop_down( 'dyeing_work_order_controller', this.value, 'load_drop_down_buyer_wo', 'buyer_wo_td' );",$on);
			                    ?>
			                </td>
			                <td id="buyer_wo_td"  align="center">
			                    <?
			                    $buyer_cond='';
			                    if(!empty($buyer_id)){
									$buyer_on=1;
									$buyer_cond=' and id=$buyer_id ';
								}else{
									$buyer_on=0;
								}
								if($within_group==1)
								{
									if(!empty($buyer_id))
									{

										$buyer_cond=' and comp.id=$buyer_id ';
									}
									echo create_drop_down("cbo_buyer_name", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.id=$buyer_id order by comp.company_name", "id,company_name", 0, "-- Select Buyer --", 0, "", 1);
								}else{
									 echo create_drop_down( "cbo_buyer_name", 110, "select id,buyer_name from lib_buyer where  status_active =1 and is_deleted=0 $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select Buyer --",$buyer_id,"",$buyer_on );
								}
			                   
			                    ?>
			                    <input type="hidden" name="within_group" id="within_group" value="<?php echo $within_group;?>">
			                </td>
			                <td>
			                	<?
								echo create_drop_down( "cbo_dyeing_source", 110, $knitting_source, "", 1, "-- Select --", 3, "load_drop_down( 'requires/dyeing_work_order_controller',this.value+'**'+$('#cbo_company_name').val(),'load_drop_down_knitting_com','dyeing_company_td' );",1,"1,3" );

								?>
			                </td>
			                 <td id="dyeing_company_td">
		                    	<? 
		                            echo create_drop_down( "cbo_dyeing_comp", 130, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name",1, "-- Select Company --", $selected, "","","" );
		                        ?>	 
		                    </td>
			               
			                <th align="center">
								<?
								$search_by_arr = array(1 => "FSO No", 2 => "Fabric Booking No",3=>"Style Ref",4=>"Work Order no",5=>"Issue No",6=>"Internal Ref");
								$dd = "change_search_event(this.value, '0*0*0*0*0*0', '0*0*0*0*0*0', '../../../') ";
								echo create_drop_down("cbo_search_by", 110, $search_by_arr, "", 0, "", "", $dd, 0);
								?>
							</th>
			                <td align="center" id="search_by_td">
								<input type="text"  class="text_boxes" name="txt_search_common"
								id="txt_search_common"/>
							</td>
							 
			               
			                <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker"  placeholder="From" style="width:60px"/></td>
			                <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker"  placeholder="To" style="width:60px"/></td>
			                <td align="center">
				                <input type="button" name="button2" style="width:60px" class="formbutton" value="Show" onClick="fnc_show();"  />
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
	// var_dump($data);

	$search_field_cond = '';
	$search_string=$data[5];
	if ($data[5] != "") {
		if ($data[4] == 1) {
			$search_field_cond = " and LOWER(a.fabric_sales_order_no) like LOWER('%" . $search_string . "%')";
		}
		else if($data[4] == 2)
		{
			$search_field_cond = " and LOWER(a.booking_no) like LOWER('%" . $search_string . "%')";
		}
		else if($data[4] == 3)
		{
			$search_field_cond = " and LOWER(a.style_ref_no) like LOWER('%" . $search_string . "%')";
		
		}
		else if($data[4]==4)
		{
			$search_field_cond = " and LOWER(a.do_number_prefix_num) like LOWER('%" . $search_string . "%')";	
		}
		else if($data[4]==6)
		{
			$search_field_cond = " and LOWER(e.grouping) = LOWER('".$search_string."')";
				
		}else{
			$search_field_cond = " and LOWER(b.issue_no) like LOWER('%" . $search_string . "%')";
		}
	}
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.buyer_id='$data[1]'"; else $buyer="";//{ echo "Please Select Buyer 
	if ($data[8]!=0) $within_group_con=" and a.within_group='$data[8]'"; else $within_group_con="";//{ echo "Please Select Buyer 

	if($db_type==0)
	{
		if ($data[2]!="" &&  $data[3]!="") $issue_date  = "and a.wo_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $issue_date ="";
	}

	if($db_type==2)
	{
		if ($data[2]!="" &&  $data[3]!="") $issue_date  = "and a.wo_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $issue_date ="";
	}
	
	/* if ($db_type == 0) {
		
		$details_arr = return_library_array("select mst_id, group_concat(id order by id desc) as dtls_id from knitting_work_order_dtls where  status_active=1 and is_deleted=0 group by mst_id", 'mst_id', 'id');
	} else if ($db_type == 2) {
		
		$details_arr = return_library_array("select mst_id, LISTAGG(id, ',') WITHIN GROUP (ORDER BY id desc) as id from knitting_work_order_dtls where  status_active=1 and is_deleted=0 group by mst_id", 'mst_id', 'id');
	} */ 

	if($data[6]!=0) $dyeing_source=" and a.dyeing_source='$data[6]'"; else $dyeing_source="";
	if($data[7]!=0) $dyeing_compnay_id=" and a.dyeing_compnay_id='$data[7]'"; else $dyeing_compnay_id="";
	//$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$dyeing_comp=return_library_array( "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name",'id','supplier_name');

	$supllier_arr = return_library_array("select id,supplier_name from lib_supplier where  status_active =1 and is_deleted=0 and party_type='20' order by supplier_name", 'id', 'supplier_name');


	/* $sql="SELECT a.id,a.do_no,a.company_id,a.buyer_id,a.currency_id,a.exchange_rate,a.booking_no,a.fabric_sales_order_no,a.po_breakdown_id,a.style_ref_no,a.pay_mode,a.wo_date,a.delivery_date,a.booking_month,a.attention,a.remark,a.dyeing_source,a.dyeing_compnay_id, a.approved, a.wo_basis, a.ready_approval, sum(b.issue_qnty) as issue_qnty,sum(b.wo_qty) as wo_qty, e.grouping
		from dyeing_work_order_mst a , dyeing_work_order_dtls b, fabric_sales_order_mst c left join wo_booking_dtls d on c.sales_booking_no = d.booking_no and c.within_group=1 and d.status_active=1 
 left join wo_po_break_down e on d.po_break_down_id=e.id
		where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.entry_form=418 and a.fabric_sales_order_no=c.job_no $search_field_cond  $company $buyer $issue_date $dyeing_compnay_id $within_group_con
		group by  a.id,a.do_no,a.company_id,a.buyer_id,a.currency_id,a.exchange_rate,a.booking_no,a.fabric_sales_order_no,a.po_breakdown_id,a.style_ref_no,a.pay_mode,a.wo_date,a.delivery_date,a.booking_month,a.attention,a.remark,a.dyeing_source,a.dyeing_compnay_id, a.approved, a.wo_basis, a.ready_approval, e.grouping order by a.do_no, a.booking_no,a.fabric_sales_order_no"; */

		$sql="SELECT a.id,a.do_no,a.company_id,a.buyer_id,a.currency_id,a.exchange_rate,a.booking_no,a.fabric_sales_order_no,a.po_breakdown_id,a.style_ref_no,a.pay_mode,a.wo_date,a.delivery_date,a.booking_month,a.attention,a.remark,a.dyeing_source,a.dyeing_compnay_id, a.approved, a.wo_basis, a.ready_approval, b.id as dtls_id, b.issue_qnty,b.wo_qty, c.within_group, e.grouping
		from dyeing_work_order_mst a , dyeing_work_order_dtls b, fabric_sales_order_mst c left join wo_booking_dtls d on c.sales_booking_no = d.booking_no and c.within_group=1 and d.status_active=1 
 left join wo_po_break_down e on d.po_break_down_id=e.id
		where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.entry_form=418 and a.fabric_sales_order_no=c.job_no $search_field_cond  $company $buyer $issue_date $dyeing_compnay_id $within_group_con
		order by a.do_no, a.booking_no,a.fabric_sales_order_no";
		//echo $sql;
	$result = sql_select($sql);

	$dtls_id_arr=array();
	foreach($result as $row )
	{
		$wo_data_arr[$row[csf("id")]]['id']=$row[csf("id")];
		$wo_data_arr[$row[csf("id")]]['do_no']=$row[csf("do_no")];
		$wo_data_arr[$row[csf("id")]]['company_id']=$row[csf("company_id")];
		$wo_data_arr[$row[csf("id")]]['buyer_id']=$row[csf("buyer_id")];
		$wo_data_arr[$row[csf("id")]]['currency_id']=$row[csf("currency_id")];
		$wo_data_arr[$row[csf("id")]]['exchange_rate']=$row[csf("exchange_rate")];
		$wo_data_arr[$row[csf("id")]]['pay_mode']=$row[csf("pay_mode")];
		$wo_data_arr[$row[csf("id")]]['wo_date']=$row[csf("wo_date")];
		$wo_data_arr[$row[csf("id")]]['delivery_date']=$row[csf("delivery_date")];
		$wo_data_arr[$row[csf("id")]]['attention']=$row[csf("attention")];
		$wo_data_arr[$row[csf("id")]]['remark']=$row[csf("remark")];
		$wo_data_arr[$row[csf("id")]]['dyeing_source']=$row[csf("dyeing_source")];
		$wo_data_arr[$row[csf("id")]]['dyeing_compnay_id']=$row[csf("dyeing_compnay_id")];
		$wo_data_arr[$row[csf("id")]]['approved']=$row[csf("approved")];
		$wo_data_arr[$row[csf("id")]]['wo_basis']=$row[csf("wo_basis")];
		$wo_data_arr[$row[csf("id")]]['fabric_sales_order_no']=$row[csf("fabric_sales_order_no")];
		$wo_data_arr[$row[csf("id")]]['booking_month']=$row[csf("booking_month")];
		$wo_data_arr[$row[csf("id")]]['style_ref_no']=$row[csf("style_ref_no")];
		$wo_data_arr[$row[csf("id")]]['booking_no']=$row[csf("booking_no")];
		$wo_data_arr[$row[csf("id")]]['po_breakdown_id']=$row[csf("po_breakdown_id")];
		$wo_data_arr[$row[csf("id")]]['within_group']=$row[csf("within_group")];

		$wo_data_arr[$row[csf("id")]]['ready_approval']=$row[csf("ready_approval")];

		if($dtls_id_arr[$row[csf("dtls_id")]]==""){
			$dtls_id_arr[$row[csf("dtls_id")]]=$row[csf("dtls_id")];
			$wo_data_arr[$row[csf("id")]]['issue_qnty']+=$row[csf("issue_qnty")];
			$wo_data_arr[$row[csf("id")]]['wo_qty']+=$row[csf("wo_qty")];
			$wo_data_arr[$row[csf("id")]]['grouping'] .=$row[csf("grouping")].',';
		}
	}

	$data=$row['id']."**".$row['do_no']."**".$row['company_id']."**".$row['currency_id']."**".$row['exchange_rate']."**".$row['pay_mode']."**".change_date_format($row['wo_date'])."**".change_date_format($row['delivery_date'])."**".$row['booking_month']."**".$row['buyer_id']."**".$row['style_ref_no']."**".$row['booking_no']."**".$row['fabric_sales_order_no']."**".$row['dyeing_source']."**".$row['dyeing_compnay_id']."**".$row['attention']."**".$row['remark']."**".$row['po_breakdown_id']."**".$row['within_group']."**".$row['approved']."**".$row['wo_basis']."**".$row['ready_approval'];

	$wo_basis_arr=array(1=>"Challan Number",2=>"Fso Wise");
	?>
	<table class="rpt_table" id="rpt_tablelist_view" rules="all" width="980" cellspacing="0" cellpadding="0" border="0">
        <thead>
            <tr>
                <th width="50">SL No</th>
                <th width="150">Company Name</th>
                <th width="130">WO No</th>
                <th width="100">WO Basis</th>
                <th width="170">Dyeing Source</th>
                <th width="150">Dyeing Company</th>
				<th width="100">Internal Ref</th>
                <th width="60">Issue Qnty</th>
                <th>WO Qnty</th>
            </tr>
        </thead>
    </table>
    <table class="rpt_table" id="list_view" rules="all" width="980" cellspacing="0" cellpadding="0" border="0">
        <tbody>
			<?
			$i=0;
			foreach($wo_data_arr as $row )
			{
				$i++;
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
				$id=$row['id'];
				$dyeing_source=$knitting_source[$row['dyeing_source']];
				$data=$row['id']."**".$row['do_no']."**".$row['company_id']."**".$row['currency_id']."**".$row['exchange_rate']."**".$row['pay_mode']."**".change_date_format($row['wo_date'])."**".change_date_format($row['delivery_date'])."**".$row['booking_month']."**".$row['buyer_id']."**".$row['style_ref_no']."**".$row['booking_no']."**".$row['fabric_sales_order_no']."**".$row['dyeing_source']."**".$row['dyeing_compnay_id']."**".$row['attention']."**".$row['remark']."**".$row['po_breakdown_id']."**".$row['within_group']."**".$row['approved']."**".$row['wo_basis']."**".$row['ready_approval'];

			$grouping = implode(",",array_filter(explode(",",$row['grouping'])));
            ?>
	            <tr onClick="js_set_value('<? echo $data; ?>')" style="cursor:pointer" id="tr_<? echo $i; ?>" height="20" bgcolor="<? echo $bgcolor; ?>">
	                <td width="50"><? echo $i; ?></td>
	                
	                <td width="150"><p><? echo $comp[$row['company_id']]; ?></p></td>
	               
	                <td width="130" style="word-break:break-all"><? echo $row['do_no']; ?></td>
	                <td width="100" style="word-break:break-all"><? echo $wo_basis_arr[$row['wo_basis']]; ?></td>
	                <td width="170" style="word-break:break-all"><? echo $dyeing_source; ?></td>
	                <td width="150" style="word-break:break-all"><? echo $dyeing_comp[$row['dyeing_compnay_id']]; ?></td>
					<td width="100" style="word-break:break-all"><? echo $grouping; ?></td>
	                <td width="60" ><? echo number_format($row['issue_qnty'],2); ?></td>
	               
	                <td ><? echo number_format($row['wo_qty'],2); ?></td>
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

	
	$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0","id","color_name");
	$yarn_count_details=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");

	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	

	$sql="select id,mst_id,issue_date,fabric_desc,machine_dia,body_part_id,machine_gg,stitch_length,color_range,color_id,shade,proccess_loss,process_name,issue_qnty,wo_qty,rate,amount,remark_text,issue_no,yarn,issue_id from dyeing_work_order_dtls where mst_id=$mst_id and status_active=1 and is_deleted=0";
	//echo $sql;die;
	$result=sql_select($sql);
	$data="";

	$dtls_id_string='';
	foreach ($result as $row) {
		$dtls_id_string.=$row[csf('id')].",";
	}
	$dtls_id_string=chop($dtls_id_string,",");
	$sql_check="select wo_dtls_id from wo_bill_dtls where status_active=1 and entry_form=422 and wo_dtls_id in ($dtls_id_string)";
	//echo $sql_check;die;
	$res_check=sql_select($sql_check);
	$check_data=array();
	foreach ($res_check as $k) {
		array_push($check_data, $k[csf('wo_dtls_id')]);
	}

	foreach ($result as $row) {

		$yarn_count_arr=explode(",",$row[csf('yarn')]);
		$Ycount='';
		foreach($yarn_count_arr as $count_id)
		{
			if($Ycount=='') $Ycount=$yarn_count_details[$count_id]; else $Ycount.=",".$yarn_count_details[$count_id];
		}

		$color='';
		$color_id=array_unique(explode(",",$row[csf('color_id')]));
		foreach($color_id as $val)
		{
			if($val>0) $color.=$color_arr[$val].",";
		}
		$color=chop($color,',');
		$process_names=explode(",", $row[csf('process_name')]);
		$process='';
		foreach($process_names as $id)
	    {
	    	$process.=$conversion_cost_head_array[$id].",";
	    }
	    $process=chop($process,',');

	    $cons_comp=$constructtion_arr[$row[csf('fabric_desc')]].", ".$composition_arr[$row[csf('fabric_desc')]];
	    $dtls_id='h';
		if(in_array($row[csf('id')], $check_data))
		{
			$dtls_id=$row[csf('id')];
		}

		if($data!="") $data.="**";
		$data.=$row[csf('issue_date')]."__".change_date_format($row[csf('issue_date')])."__".$row[csf('issue_no')]."__".$row[csf('body_part_id')]."__".$body_part[$row[csf('body_part_id')]]."__".$row[csf('fabric_desc')]."__".$cons_comp."__".$row[csf('machine_gg')]."__".$row[csf('machine_dia')]."__".$row[csf('stitch_length')]."__".$row[csf('yarn')]."__".$Ycount."__".$row[csf('issue_qnty')]."__".$row[csf('color_id')]."__".$color."__".$row[csf('issue_id')]."&&&&".$row[csf('id')]."__".$row[csf('rate')]."__".$row[csf('amount')]."__".$row[csf('remark_text')]."__".$row[csf('shade')]."__".$row[csf('proccess_loss')]."__".$row[csf('process_name')]."__".$process."__".$row[csf('color_range')]."__".$dtls_id;

		
	}
	echo $data;
	exit();
}

if($action=="select_item_pop")
{

  	echo load_html_head_contents("Dyeing Work Order","../../../", 1, 1, $unicode);
  	//print_r($_REQUEST);die;
  	extract($_REQUEST);
	?>
	<script>
	function js_set_value(selected_fso_no)
	{
		document.getElementById('selected_fso_no').value=selected_fso_no;
		parent.emailwindow.hide();
	}
    </script>
    
	</head>

	<body>
		<div align="center" style="width:100%;" >
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table width="860" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
			        <thead>
			            <tr>
			                <th>Company Name</th>
			                <th>Buyer Name</th>
							<th>Booking Type</th>
			                <th>Search By</th>
			                <th id="search_by_td_up" width="170">Please Enter FSO No</th>
			                <th colspan="2">Booking date</th>
			                <th>&nbsp;</th>
			            </tr>
			        </thead>
			        <tbody>
			            <tr class="general">
			                <td align="center"> 
			                	<input type="hidden" id="selected_fso_no" name="selected_fso_no">
			                    <?
			                    if($company_id!="" && $company_id!=0){
									$on=1;
								}else{
									$on=0;
								}
			                    echo create_drop_down( "cbo_company_mst", 172, "select id,company_name from lib_company comp where status_active=1 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", $company_id, "load_drop_down( 'dyeing_work_order_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );",$on);
			                    ?>
			                </td>
			                <td id="buyer_td"  align="center">
			                  <?
			                  		$buyer_cond='';
				                    if($buyer_id!="" && $buyer_id!=0){
										$buyer_on=1;
										$buyer_cond=" and id=$buyer_id ";
									}else{
										$buyer_on=0;
									}

									if($within_group==1)
									{
										
										echo create_drop_down("cbo_buyer_name", 172, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.id=$buyer_id order by comp.company_name", "id,company_name", 0, "-- Select Buyer --", 0, "", 1);
									}else{
										
										echo create_drop_down( "cbo_buyer_name", 172, "select id,buyer_name from lib_buyer where  status_active =1 and is_deleted=0 $buyer_cond  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --",$buyer_id,"",$buyer_on );
										
									}
				                    
			                    ?>
			                    
			                </td>
			                
							<td>
								<?
								$booking_type = array(1 => "Main Booking No", 2 => "Sample Booking With Order", 3 => "Sample Booking Without Order", 4 => "Short Booking No");
								echo create_drop_down("cbo_booking_type", 130, $booking_type, "", 0, "--Select--", "", "", 0);
								?>
							</td>
			                
			                <th align="center">
								<?
								$search_by_arr = array(1 => "FSO No",2 => "Fabric Booking No",3=>"Style Ref",4=>"Internal Ref No");
								$dd = "change_search_event(this.value, '0*0*0*0', '0*0*0*0', '../../../') ";
								echo create_drop_down("cbo_search_by", 140, $search_by_arr, "", 0, "", "", $dd, 0);
								?>
                                
							</th>
			                <td align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
								id="txt_search_common"/>
                               
							</td>
							 
			               
			                <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From" /></td>
			                <td>
								<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To" />
								<input type="hidden" name="within_group" id="within_group" value="<?php echo $within_group;?>">
							</td>
			                <td align="center">
			                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('within_group').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_booking_type').value, 'fso_search_list_view', 'search_div', 'dyeing_work_order_controller', 'setFilterGrid(\'list_view\',-1)') " style="width:100px;" /></td>
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
if($action =="fso_search_list_view")
{
	$data=explode('_',$data);

	$search_field_cond = '';
	$search_string= trim($data[5]);
	$cbo_booking_type=$data[8];
	$issue_cond='';
	if ($data[5] != "") {
		if ($data[4] == 1) {
			$search_field_cond = " and a.job_no_prefix_num = '" . $search_string . "'";
		} else if($data[4] == 2) {
			$search_field_cond = " and LOWER(a.sales_booking_no) like LOWER('%" . $search_string . "%')";
		}else if($data[4] == 4) {
			$search_field_cond = " and LOWER(d.grouping) = LOWER('".$search_string."')";
		}
		else{
			$search_field_cond = " and LOWER(a.style_ref_no) like LOWER('%" . $search_string . "%')";
		}
	}
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.buyer_id='$data[1]'"; else $buyer="";
	if ($data[6]!=0) $within_group_con=" and a.within_group='$data[6]'"; else $within_group_con="";
	
	$booking_date="";
	if($db_type==0)
	{
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}

	if($db_type==2)
	{
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	}


	if($data[7]!="")
	{
		if($db_type==0)
		{
			$search_field_cond .=" and YEAR(a.insert_date)=".$data[7];	
		}
		else if($db_type==2)
		{
			$search_field_cond .=" and TO_CHAR(a.insert_date,'YYYY')=".$data[7];	
		} 
	}

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');

	//$booking_type_arr = array("118"=>"Main","108"=>"Partial","88"=>"Short","89"=>"Sample With Order","90"=>"Sample Without Order");

	$booking_type_cond="";
	if($data[6]==1)
	{
		if($cbo_booking_type==1)
		{
			$booking_type_cond = " and a.booking_entry_form in (118,108)";
		}
		else if($cbo_booking_type==2)
		{
			$booking_type_cond = " and a.booking_entry_form in (89)";
		}
		else if($cbo_booking_type==3)
		{
			$booking_type_cond = " and a.booking_entry_form in (90,140)";
		}
		else if($cbo_booking_type==4)
		{
			$booking_type_cond = " and a.booking_entry_form in (88)";
		}
	}

	$sql="select a.id as po_id, a.job_no, a.sales_booking_no, a.buyer_id, a.within_group, a.style_ref_no, a.company_id, a.booking_date, d.grouping from fabric_sales_order_mst a left join wo_booking_dtls c on a.sales_booking_no = c.booking_no and a.within_group=1 and c.status_active=1 left join wo_po_break_down d on c.po_break_down_id=d.id where a.status_active=1 and a.is_deleted=0 $search_field_cond $company $buyer $booking_date $within_group_con $booking_type_cond order by po_id";

	//echo $sql;
	$result=sql_select($sql);

	foreach($result as $row )
	{
		$fso_data_arr[$row[csf("po_id")]]['po_id']=$row[csf("po_id")];
		$fso_data_arr[$row[csf("po_id")]]['job_no']=$row[csf("job_no")];
		$fso_data_arr[$row[csf("po_id")]]['sales_booking_no']=$row[csf("sales_booking_no")];
		$fso_data_arr[$row[csf("po_id")]]['buyer_id']=$row[csf("buyer_id")];
		$fso_data_arr[$row[csf("po_id")]]['within_group']=$row[csf("within_group")];
		$fso_data_arr[$row[csf("po_id")]]['style_ref_no']=$row[csf("style_ref_no")];
		$fso_data_arr[$row[csf("po_id")]]['company_id']=$row[csf("company_id")];
		$fso_data_arr[$row[csf("po_id")]]['booking_date']=$row[csf("booking_date")];
		$fso_data_arr[$row[csf("po_id")]]['grouping'] .=$row[csf("grouping")].',';
	}
	
	?>
	<table class="rpt_table" id="heading" rules="all" width="850" cellspacing="0" cellpadding="0" border="0">
        <thead>
            <tr>
                <th width="35">SL No</th>
                <th width="100">Company Name</th>
                <th width="100">Buyer Name</th>
                <th width="120">FSO No</th>
                <th width="120">Fabric Booking No</th>
				<th width="100">Internal Ref No</th>
                <th width="110">Style Ref. No</th>
                <th>Booking Date</th>
            </tr>
        </thead>
      </table>
      <table class="rpt_table" id="list_view" rules="all" width="850" cellspacing="0" cellpadding="0" border="0">
	
        <tbody>
			<?
			$i=0;

			foreach($fso_data_arr as $fso_id =>$row )
			{
				$i++;
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
				$buyer='';
				if($row['within_group']==1){
					$buyer=$comp[$row["buyer_id"]];
				}else{
					$buyer=$buyer_arr[$row["buyer_id"]];
				}
				$data=$row['buyer_id']."__".$row['company_id']."__".$row['style_ref_no']."__".$row['job_no']."__".$row['sales_booking_no']."__".$row['po_id'];
            ?>
            <tr onClick="js_set_value('<? echo $data; ?>')" style="cursor:pointer" id="tr_<? echo $i; ?>" height="20" bgcolor="<? echo $bgcolor; ?>">
                <td  width="35"><? echo $i; ?></td>
                
                <td width="100"> <p><?php echo $comp[$row['company_id']]; ?></p></td>
                <td width="100"><p><? echo $buyer; ?></p></td>
                <td width="120"  style="word-break:break-all"><? echo $row["job_no"]; ?></p>
                </td>
                <td width="120" style="word-break:break-all"><p><?php echo $row["sales_booking_no"]; ?></p></td>
				<td width="100" style="word-break:break-all"><p><?php echo implode(",",array_unique(explode(',',chop($row["grouping"],',')))); ?></p></td>
                <td width="110"><p><? echo $row["style_ref_no"]; ?></p></td>
               
                <td  ><p><? echo change_date_format($row['booking_date']); ?></p></td>
               
            </tr>
            <?
			}
			?>
        </tbody>
    </table>
 
	<?
	exit();
}

if ($action=="issue_no_pop")
{
  	echo load_html_head_contents("Dyeing Work Order","../../../", 1, 1, $unicode);
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
		
		function check_all_data() {
			var tbl_row_count = document.getElementById('list_view').rows.length;
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

		function js_set_value(str) {

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
				if(id!='') id+=',';
				id += selected_id[i];
			}

			$('#selected_work_order').val(id);
			
		}

	</script>
	</head>

	<body>
		<div align="center" style="width:100%;" >
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table width="800" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
			        <thead>
			            <tr>
			                <th>Company Name</th>
			                <th>Dyeing Source</th>
			                <th>Dyeing Company</th>
			                <th>Issue No</th>
			                <th colspan="2">Issue Date Range</th>
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
			                    echo create_drop_down( "cbo_company_mst", 172, "select id,company_name from lib_company comp where status_active=1 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", $company_id, "",$on);
			                    ?>
			                </td>
			               
			                <td>
			                	
			                	<?
								echo create_drop_down( "cbo_dyeing_source", 130, $knitting_source, "", 1, "-- Select --", 3, "",1,"1,3" );

								?>
			                </td>
			                <td>
			                	<?
			                   if($dyeing_company_id!="" && $dyeing_company_id!=0){
									$on=1;
								}else{
									$on=0;
								}
			                    echo create_drop_down( "cbo_dyeing_company", 172, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name",1, "-- Select Company --", $dyeing_company_id, "",$on);
			                    ?>
			                </td>
			                
			                <td align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes" name="txt_issue_no"
								id="txt_issue_no"/>
							</td>
							 
			               <input type="hidden" name="po_breakdown_id" id="po_breakdown_id" value="<? echo $po_breakdown_id;?>">
			                <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From" /></td>
			                <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To" /></td>
			                <td align="center">
			                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_dyeing_source').value+'_'+document.getElementById('txt_issue_no').value+'_'+document.getElementById('cbo_dyeing_company').value+'_'+document.getElementById('po_breakdown_id').value, 'data_search_list_view', 'search_div', 'dyeing_work_order_controller', 'setFilterGrid(\'list_view\',-1)') " style="width:100px;" /></td>
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


if($action =="fso_details_list_view")
{
	echo load_html_head_contents("Dyeing Work Order","../../../", 1, 1, $unicode);
  	//print_r($_REQUEST);die;
  	extract($_REQUEST);
	?>
	<script>

	var selected_id = new Array;
	  
	function check_all_data() {
		var tbl_row_count = document.getElementById('list_view').rows.length;
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

	function js_set_value(str) 
	{
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
		for (var i = 0; i < selected_id.length; i++) 
		{
			if(id!='') id+=',';
			id += selected_id[i];
		}

		$('#selected_fso_details').val(id);
	}

	</script>
	</head>
	<?
	//print_r($data);die;
	// echo "<pre>";

	$search_field_cond = '';
	$search_string=$data[5];
	$issue_cond='';
	if ($data[5] != "") {
		if ($data[4] == 1) {
			$search_field_cond = " and LOWER(job_no_prefix_num) like LOWER('%" . $search_string . "%')";
		} else if($data[4] == 2) {
			$search_field_cond = " and LOWER(sales_booking_no) like LOWER('%" . $search_string . "%')";
		}else{
			$search_field_cond = " and LOWER(style_ref_no) like LOWER('%" . $search_string . "%')";
		}
	}
	if ($company_id!=0) $company=" and a.company_id='$company_id'"; else { echo "Please Select Company First."; die; }
	if ($buyer_id!=0) $buyer=" and a.buyer_id='$buyer_id'"; else $buyer="";
	if ($within_group!=0) $within_group_con=" and a.within_group='$within_group'"; else $within_group_con="";
	

	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	
	$sql="SELECT a.id as fso_id, a.job_no, a.sales_booking_no, a.customer_buyer, a.within_group, a.style_ref_no, a.company_id, b.determination_id, b.fabric_desc, b.gsm_weight, b.color_id, sum(b.finish_qty) as finish_qty, sum(b.grey_qty) as grey_qty
	from fabric_sales_order_mst a, fabric_sales_order_dtls b
	where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 $company $buyer $within_group_con and a.id=$po_breakdown_id 
	group by a.id, a.job_no, a.sales_booking_no, a.customer_buyer, a.within_group, a.style_ref_no, a.company_id, b.determination_id, b.fabric_desc, b.gsm_weight, b.color_id order by a.id";
	//echo $sql;
	$result=sql_select($sql);
	
	?>
	<table class="rpt_table" id="heading" rules="all" width="895" cellspacing="0" cellpadding="0" border="0">
        <thead>
            <tr>
                <th width="35">SL No</th>
				<th width="80">Buyer/Unit Name</th>
				<th width="110">FSO No</th>
				<th width="110">Fabric Booking No</th>
				<th width="110">Style Ref. No</th>
                <th width="150">Fabric Description</th>
                <th width="50">GSM</th>
                <th width="100">F. Color</th>
                <th width="75">F. Fab Qty</th>
                <th width="75">G. Fab Qty</th>
            </tr>
        </thead>
      </table>
      <table class="rpt_table" id="list_view" rules="all" width="895" cellspacing="0" cellpadding="0" border="0">
	
        <tbody>
			<?
			$i=0;
			foreach($result as $row )
			{
				$i++;
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
				$buyer=$buyer_arr[$row[csf("customer_buyer")]];

				$data=$row[csf('fso_id')]."__".$row[csf('determination_id')]."__".$row[csf('gsm_weight')]."__".$row[csf('color_id')];
            ?>
			<tr onClick="js_set_value('<? echo $i; ?>')" style="cursor:pointer" id="tr_<? echo $i; ?>" height="20" bgcolor="<? echo $bgcolor; ?>">
                <td  width="35">
					<? echo $i; ?>
					<input type="hidden" name="hidden_data" id="hidden_data_id_<?php echo $i ?>" value="<? echo $data; ?>"/>
				</td>
				<td width="80"><p><? echo $buyer; ?></p></td>
                <td width="110"> <p><?php echo $row[csf("job_no")]; ?></p></td>
                
				<td width="110" style="word-break:break-all"><p><?php echo $row[csf("sales_booking_no")]; ?></p></td>
				<td width="110"><p><? echo $row[csf('style_ref_no')]; ?></p></td>

                <td width="150"><? echo $row[csf("fabric_desc")]; ?></p></td>
                <td width="50"><? echo $row[csf("gsm_weight")]; ?></p></td>
                <td width="100"><? echo $color_arr[$row[csf("color_id")]]; ?></p></td>
                <td width="75"><? echo $row[csf("finish_qty")]; ?></p></td>
                <td width="75"><? echo $row[csf("grey_qty")]; ?></p></td>
                
               
            </tr>
            <?
			}
			?>
        </tbody>
    </table>
	<table width="800" cellspacing="0" cellpadding="0" style="border:none" align="center" id="ds">
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
						<input type="hidden" id="selected_fso_details">
					</div>
				</div>
			</td>
		</tr>
	</table>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<?
	exit();
}


if ($action=="load_drop_down_buyer")
{
	//echo $data;die;
	echo create_drop_down( "cbo_buyer_name", 172, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	exit();
}

if ($action=="load_drop_down_buyer_wo")
{
	//echo $data;die;
	echo create_drop_down( "cbo_buyer_name", 110, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	exit();
}


if ($action=="data_search_list_view")
{
	$data=explode('_',$data);
	//print_r($data);die;
	// echo "<pre>";
	// print_r($data);
	// echo "</pre>";
	

	
	$issue_cond='';
	$wo_po_cond='';
	if ($data[6] != "") {
		$wo_po_cond=" and d.po_breakdown_id in ($data[6])";
	}
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	$source_condition="";
	if($data[3]!="" && $data[3]!=0){
		$source_condition=" and a.knit_dye_source ='$data[3]' ";
	}
	if($data[4]!="")
	{
		$issue_cond= " and a.issue_number_prefix_num=".$data[4]."";
	}
	$dyeing_com_cond="";
	if($data[5]!=0 && $data[5]!="")
	{
		$dyeing_com_cond="  and a.knit_dye_company = '$data[5]'";
	}
	
	$issue_date="";
	if($db_type==0)
	{
		if ($data[1]!="" &&  $data[2]!="") $issue_date  = "and a.issue_date  between '".change_date_format($data[1], "yyyy-mm-dd", "-")."' and '".change_date_format($data[2], "yyyy-mm-dd", "-")."'"; else $issue_date ="";
	}

	if($db_type==2)
	{
		if ($data[1]!="" &&  $data[2]!="") $issue_date  = "and a.issue_date  between '".change_date_format($data[1], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."'"; else $issue_date ="";
	}

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');

	$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0","id","color_name");
	$supllier_arr = return_library_array("select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", 'id', 'supplier_name');
	$booking_arr = return_library_array("select id, booking_no from wo_booking_mst", 'id', 'booking_no');
	$fabric_sale = sql_select("select sales_booking_no,job_no,style_ref_no,booking_id,buyer_id,within_group from fabric_sales_order_mst where id in ($data[6])");
	$yarn_count_details=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");

	
	
	//$sql= "select a.id as issue_id,a.buyer_id,a.issue_number,sum(b.issue_qnty) as issue_qnty,d.po_breakdown_id,a.knit_dye_source,a.issue_date,a.knit_dye_company,LISTAGG(d.barcode_no, ',') WITHIN GROUP (ORDER BY d.id desc) as barcode_no from inv_issue_master a, inv_grey_fabric_issue_dtls b, pro_roll_details d where a.id=b.mst_id and a.order_id = d.po_breakdown_id   and a.status_active=1 and b.status_active=1 and  d.status_active=1 and d.is_sales=1 and a.entry_form in (61,16) $wo_po_cond $dyeing_com_cond $issue_date $source_condition $buyer $company $issue_cond group by a.id,a.buyer_id,a.issue_number,d.po_breakdown_id,a.knit_dye_source,a.issue_date,a.knit_dye_company order by a.issue_number,d.po_breakdown_id";

	$sql= "SELECT a.id as issue_id,a.buyer_id,a.issue_number,sum(b.issue_qnty) as issue_qnty,d.po_breakdown_id,a.knit_dye_source,a.issue_date,a.knit_dye_company,LISTAGG(d.barcode_no, ',') WITHIN GROUP (ORDER BY d.id desc) as barcode_no, c.mst_id as saved_wo from inv_issue_master a left join dyeing_work_order_dtls c on a.issue_number=c.issue_no and c.status_active=1 and c.is_deleted=0, inv_grey_fabric_issue_dtls b, pro_roll_details d where a.id=b.mst_id and b.id=d.dtls_id and a.status_active=1 and b.status_active=1 and d.status_active=1 and d.is_sales=1 and a.entry_form in (61) and d.entry_form in (61) $wo_po_cond $dyeing_com_cond $issue_date $source_condition $buyer $company $issue_cond group by a.id,a.buyer_id,a.issue_number,d.po_breakdown_id,a.knit_dye_source,a.issue_date,a.knit_dye_company, c.mst_id
	union all
	select a.id as issue_id,a.buyer_id,a.issue_number,sum(b.issue_qnty) as issue_qnty,d.po_breakdown_id,a.knit_dye_source,a.issue_date,a.knit_dye_company, null as barcode_no, c.mst_id as saved_wo from inv_issue_master a left join dyeing_work_order_dtls c on a.issue_number=c.issue_no and c.status_active=1 and c.is_deleted=0, inv_grey_fabric_issue_dtls b, order_wise_pro_details d where a.id=b.mst_id and b.id=d.dtls_id and a.status_active=1 and d.status_active=1 and d.status_active=1 and d.is_sales=1 and a.entry_form in (16) and d.entry_form in (16) $wo_po_cond $dyeing_com_cond $issue_date $source_condition $buyer $company $issue_cond group by a.id,a.buyer_id,a.issue_number,d.po_breakdown_id,a.knit_dye_source,a.issue_date,a.knit_dye_company, c.mst_id order by issue_number, po_breakdown_id";

   
	//echo $sql;//die;
	$result = sql_select($sql);
	$barcode_nos='';

	foreach ($result as $barcodes) {
		$barcode_nos.=$barcodes[csf('barcode_no')].',';
	}
	
	$dyeing_barcode_result=sql_select("SELECT barcode_no from dyeing_work_order_dtls where status_active=1 and is_deleted=0");
	$deying_barcode_arr=array();
	foreach ($dyeing_barcode_result as $row) {
		$barcode_nos=explode(",", $row[csf('barcode_no')]);
		foreach ($barcode_nos as $barcode) {
			array_push($deying_barcode_arr, $barcode);
		}
	}


	
	?>
	<script type="text/javascript">
    	
    	$(document).ready(function(e) {
			
			set_all();
		});
    </script>
	<table class="rpt_table"  rules="all" width="880" cellspacing="0" cellpadding="0" border="0">
        <thead>
            <tr>
                <th width="35">SL No</th>
                <th width="80">Buyer Name</th>
                <th width="100">FSO No</th>
                <th width="100">Fabric Booking No</th>
                <th width="100">Style Ref. No</th>
                <th width="110">Source</th>
                <th width="110">Dyeing Company</th>
                <th width="70">Issue Date</th>
                <th width="100">Issue No</th>
                <th>Issue Qty.</th>
            </tr>
        </thead>
    </table>
	<table id="list_view" class="rpt_table"  rules="all" width="880" cellspacing="0" cellpadding="0" border="0">
        <tbody>
			<?
			$i=0;
			

			foreach($result as $row )
			{
				/* $barcode_noss=explode(",", $row[csf('barcode_no')]);
				$flag=true;
				foreach ($barcode_noss as $barCode) {
					
					if(in_array($barCode, $deying_barcode_arr))
					{
						$flag=false;
						break;
					}
				} */
				//if($flag==true)
				if($row[csf('saved_wo')]=="")
				{

					$barcode_string=$row[csf('barcode_no')];
					$i++;
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					if ($row[csf('knit_dye_source')] == 1)
						$knit_party = $com[$row[csf('knit_dye_company')]];
					else
						$knit_party = $supllier_arr[$row[csf('knit_dye_company')]];

					$buyer='';

					if($fabric_sale[0][csf("within_group")]==1)
					{
						$buyer=$comp[$fabric_sale[0][csf("buyer_id")]];
					}else{
						$buyer=$buyer_arr[$fabric_sale[0][csf("buyer_id")]];
					}
					
					$data =$row[csf('issue_id')];
					?>
					<tr onClick="js_set_value('<? echo $i; ?>')" style="cursor:pointer" id="tr_<? echo $i; ?>" height="20" bgcolor="<? echo $bgcolor; ?>">
						<td width="35"><? echo $i; ?>
							<input type="hidden" name="hidden_data" id="hidden_data_id_<?php echo $i ?>" value="<? echo $data; ?>"/>
						</td>
						
						
						<td width="80" ><p><? echo $buyer; ?></p>
							
						</td>
						<td  width="100" style="word-break:break-all"><? echo $fabric_sale[0][csf("job_no")]; ?></td>
						<td width="100" style="word-break:break-all"><?php echo $fabric_sale[0][csf("sales_booking_no")]; ?></td>
						<td width="100" ><? echo $fabric_sale[0][csf("style_ref_no")]; ?></td>
						<td width="110" style="word-break:break-all"><? echo $knitting_source[$row[csf('knit_dye_source')]]; ?></td>
						<td width="110" ><?php echo $knit_party; ?></td>
						<td width="70" ><p><? echo change_date_format($row[csf('issue_date')]); ?></td>
						<td  width="100" ><p><? echo $row[csf('issue_number')]; ?></p></td>
						<td > <p><? echo number_format($row[csf('issue_qnty')],2); ?></p></td>
					</tr>
            		<?
       		 	}
			}
			?>
        </tbody>
    </table>
    <table width="800" cellspacing="0" cellpadding="0" style="border:none" align="center" id="ds">
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
    </div>
    
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<?
	exit();
}
if($action=="populate_group_details")
{
	//echo $data;die;
	$data=explode("**", $data);

	$issue_ids=$data[0];
	$po_breakdown_id=$data[1];

	$yarn_count_details=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0","id","color_name");

	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$deter_array=sql_select($sql_deter);
	foreach( $deter_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}

	$gross_issue_sql = sql_select("SELECT a.issue_number, a.issue_date, b.body_part_id, c.detarmination_id, c.gsm, c.dia_width, b.color_id, b.yarn_count, b.stitch_length, sum(d.quantity) as qty
	from inv_issue_master a, inv_grey_fabric_issue_dtls b, order_wise_pro_details d, product_details_master c
	where a.id=b.mst_id and b.id=d.dtls_id and b.prod_id=c.id and a.status_active=1 and d.status_active=1 and d.status_active=1 and d.is_sales=1 and a.entry_form in (16) and d.entry_form in (16) 
	and d.po_breakdown_id=$po_breakdown_id and a.id in ($issue_ids)
	group by a.issue_number, a.issue_date, b.body_part_id, c.detarmination_id, c.gsm, c.dia_width, b.color_id, b.yarn_count, b.stitch_length");

	foreach ($gross_issue_sql as $row ) 
	{
		$febric_description_id=$row[csf('detarmination_id')];
		$cons_comp=$constructtion_arr[$febric_description_id].", ".$composition_arr[$febric_description_id];

		$color='';
		$color_id=array_unique(explode(",",$row[csf('color_id')]));
		foreach($color_id as $val)
		{
			if($val>0) $color.=$color_arr[$val].",";
		}
		$color=chop($color,',');

		$Ycount='';
		$yarn_count=$row[csf('yarn_count')];
		//echo $yarn_count;die;
		$yarn_count_arr=explode(",",$yarn_count);
		foreach($yarn_count_arr as $count_id)
		{
			if($Ycount=='') $Ycount=$yarn_count_details[$count_id]; else $Ycount.=",".$yarn_count_details[$count_id];
		}

		$string.=$row[csf('issue_date')]."__".change_date_format($row[csf('issue_date')])."__".$row[csf('issue_number')]."__".$row[csf('body_part_id')]."__".$body_part[$row[csf('body_part_id')]]."__".$febric_description_id."__".$cons_comp."__".$row[csf('gsm')]."__".$row[csf('dia_width')]."__".$row[csf('stitch_length')]."__".$yarn_count."__".$Ycount."__".$row[csf('qty')]."__".$row[csf('color_id')]."__".$color."__".$row[csf('issue_id')]."***";
	}


	$roll_issue_sql = sql_select("SELECT a.id as issue_id, a.issue_number, a.issue_date, d.qnty, d.barcode_no from inv_issue_master a, inv_grey_fabric_issue_dtls b, pro_roll_details d 
	where a.id=b.mst_id and b.id=d.dtls_id and a.status_active=1 and b.status_active=1 and d.status_active=1 and d.is_sales=1 and a.entry_form in (61) and d.entry_form in (61)
	and d.po_breakdown_id=$po_breakdown_id and a.id in ($issue_ids)");

	foreach ($roll_issue_sql as $row ) 
	{
		$all_barcode_arr[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
	}

	if(count($all_barcode_arr)>0)
	{
		$all_barcode_nos=implode(",",$all_barcode_arr);
		$all_barcode_nos_cond=""; $barCond="";
		if($db_type==2 && count($all_barcode_arr)>999)
		{
			$barcode_nos_chunk=array_chunk($all_barcode_arr,999) ;
			foreach($barcode_nos_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$barCond.="  c.barcode_no in($chunk_arr_value) or ";
			}
			$all_barcode_nos_cond.=" and (".chop($barCond,'or ').")";
		}
		else
		{
			$all_barcode_nos_cond=" and c.barcode_no in($all_barcode_nos)";
		}
	}

	if ($db_type == 0) 
	{
		$sql_dtls=sql_select("SELECT b.body_part_id, b.febric_description_id,b.gsm, b.width, b.color_id, b.yarn_count, b.stitch_length, c.barcode_no
		FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
		WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58)  and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $all_barcode_nos_cond
		group by b.body_part_id, b.febric_description_id,b.gsm, b.width, b.color_id, b.yarn_count, b.stitch_length, c.barcode_no");
	}
	else if($db_type == 2) 
	{
		
		$sql_dtls=sql_select("SELECT b.body_part_id, b.febric_description_id, b.gsm, b.width, b.color_id, b.yarn_count, b.stitch_length, c.barcode_no FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58)  and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $all_barcode_nos_cond group by b.body_part_id, b.febric_description_id,b.gsm, b.width, b.color_id, b.yarn_count, b.stitch_length, c.barcode_no ");
	}

	foreach ($sql_dtls as $row ) {
		$barcode_info[$row[csf('barcode_no')]]['body_part_id']=$row[csf('body_part_id')];
		$barcode_info[$row[csf('barcode_no')]]['febric_description_id']=$row[csf('febric_description_id')];
		$barcode_info[$row[csf('barcode_no')]]['gsm']=$row[csf('gsm')];
		$barcode_info[$row[csf('barcode_no')]]['width']=$row[csf('width')];
		$barcode_info[$row[csf('barcode_no')]]['color_id']=$row[csf('color_id')];
		$barcode_info[$row[csf('barcode_no')]]['yarn_count']=$row[csf('yarn_count')];
		$barcode_info[$row[csf('barcode_no')]]['stitch_length']=$row[csf('stitch_length')];
	}


	foreach ($roll_issue_sql as $row ) 
	{
		$body_part_id = $barcode_info[$row[csf('barcode_no')]]['body_part_id'];
		$febric_description_id = $barcode_info[$row[csf('barcode_no')]]['febric_description_id'];
		$gsm = $barcode_info[$row[csf('barcode_no')]]['gsm'];
		$width = $barcode_info[$row[csf('barcode_no')]]['width'];
		$color_id = $barcode_info[$row[csf('barcode_no')]]['color_id'];
		$yarn_count=$barcode_info[$row[csf('barcode_no')]]['yarn_count'];
		$stitch_length = $barcode_info[$row[csf('barcode_no')]]['stitch_length'];

		$cons_comp=$constructtion_arr[$febric_description_id].", ".$composition_arr[$febric_description_id];


		$Ycount='';
		$yarn_count_arr=explode(",",$yarn_count);
		foreach($yarn_count_arr as $count_id)
		{
			if($Ycount=='') $Ycount=$yarn_count_details[$count_id]; else $Ycount.=",".$yarn_count_details[$count_id];
		}


		$color='';
		$color_id=array_unique(explode(",",$color_id));
		foreach($color_id as $val)
		{
			if($val>0) $color.=$color_arr[$val].",";
		}
		$color=chop($color,',');
		$color_ids = implode(",",$color_id);



		$roll_str =$row[csf('issue_date')]."__".change_date_format($row[csf('issue_date')])."__".$row[csf('issue_number')]."__".$body_part_id."__".$body_part[$body_part_id]."__".$febric_description_id."__".$cons_comp."__".$gsm ."__".$width."__".$stitch_length."__".$yarn_count."__".$Ycount."__".$color_ids."__".$color."__".$row[csf('issue_id')];


		$roll_issue_arr[$roll_str] +=$row[csf('qnty')];
	}

	foreach ($roll_issue_arr as $rowstr=>$val) 
	{
		$rowstrArr =explode("__",$rowstr);
		$string.=$rowstrArr[0]."__".$rowstrArr[1]."__".$rowstrArr[2]."__".$rowstrArr[3]."__".$rowstrArr[4]."__".$rowstrArr[5]."__".$rowstrArr[6]."__".$rowstrArr[7]."__".$rowstrArr[8]."__".$rowstrArr[9]."__".$rowstrArr[10]."__".$rowstrArr[11]."__".$val."__".$rowstrArr[12]."__".$rowstrArr[13]."__".$rowstrArr[14]."***";
	}


	//$string.=$row[csf('issue_date')]."__".change_date_format($row[csf('issue_date')])."__".$row[csf('issue_number')]."__".$row[csf('body_part_id')]."__".$body_part[$row[csf('body_part_id')]]."__".$febric_description_id."__".$cons_comp."__".$row[csf('gsm')]."__".$row[csf('dia_width')]."__".$row[csf('stitch_length')]."__".$yarn_count."__".$Ycount."__".$row[csf('qty')]."__".$row[csf('color_id')]."__".$color."***";


	$string=chop($string,"***");
	echo $string;
	die;
}

if($action=="load_color_range")
{

	$data=explode("**", $data);
	$disabled='';
	if(!empty($data[1]))
	{
		$disabled="disabled='disabled'";
	}
	?>
		<select class="combo_boxes <?php echo  $data[1];?>" id="colorrange_<?php echo $data[0];?>" name="colorrange[]" style="width:100px" <?php echo $disabled; ?>>
			<option data-attr="" value="0">--Select Range--</option>
			<? 
				foreach ($color_range as $id => $value) {?>

					<option value="<?php echo $id;?>" <?php echo $id==$data[1] ? 'selected' : ''; ?>><?php echo $value;?></option>
					
			<?	}
			?>
			
		</select>
	<?
	//echo create_drop_down( $data, 110, $color_range,"",1,"--Select Range--", 0,"", 0,"","" );
	exit();
}

if($action=="process_name_pop_up")
{
	echo load_html_head_contents("Process Name Info","../../../", 1, 1, '','1','');
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
						js_set_value( old[k] );
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
	                    $i=1; $process_row_id=''; $not_process_id_print_array = array(1, 2, 3, 4, 101, 120, 121, 122, 123, 124);
						//$process_id_print_array=array(25,31,32,33,34,35,39,60,63,64,65,66,67,68,69,70,71,82,83,84,89,90,91,93,125,129,132,133,136,137,146);
						$hidden_process_id=explode(",",$data);
	                    foreach($conversion_cost_head_array as $id=>$name)
	                    {
							//if(in_array($id,$process_id_print_array))
							if (!in_array($id, $not_process_id_print_array)) 
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
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		set_all();
	</script>
	</html>
	<?
	exit();
}



if($action=="save_update_delete_details")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc( $process ));
	$update_id=str_replace("'", "", $update_id);

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

		
		$id = return_next_id_by_sequence("dyeing_work_order_mst_seq", "dyeing_work_order_mst", $con);

		//$new_wo_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'DWO', date("Y",time()), 5, "select id,do_number_prefix,do_number_prefix_num from  dyeing_work_order_mst where status_active=1 and is_deleted=0 and company_id=$cbo_company_name  $year_cond=".date('Y',time())." order by id desc ", "do_number_prefix", "do_number_prefix_num" ));

		$new_wo_number = explode("*", return_next_id_by_sequence("DYEING_WORK_ORDER_MST_SEQ", "dyeing_work_order_mst",$con,1,$cbo_company_name,'DWO',418,date("Y",time()),13 ));

		$field_array = "id,do_number_prefix,do_number_prefix_num,do_no,company_id,entry_form,currency_id,ready_approval,exchange_rate,pay_mode,wo_date,delivery_date,booking_month,buyer_id,within_group,booking_no,fabric_sales_order_no,po_breakdown_id,style_ref_no,dyeing_source,dyeing_compnay_id,attention,remark,wo_basis,inserted_by,insert_date";
		
		$data_array = "(" . $id . ",'" . $new_wo_number[1] . "'," . $new_wo_number[2] . ",'" . $new_wo_number[0] . "'," . $cbo_company_name . ",418," . $cbo_currency . ",". $cbo_ready_approval . "," . $txt_exchange_rate . "," . $cbo_pay_mode . "," . $txt_wo_date . "," . $txt_delivery_date . "," . $cbo_booking_month . "," . $cbo_buyer_name.",".$cbo_within_group.",".$cbo_fabric_booking_no.",".$cbo_fso_no.",".$po_breakdown_id.",".$cbo_style_ref_no.",".$cbo_dyeing_source.",".$cbo_dyeing_comp .  "," . $txt_attention . "," . $txt_remark . "," . $cbo_wo_basis . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

		
		$data_array_dtls="";
		
		for ($j = 1; $j <= $tot_row; $j++) 
		{
			$dtls_id = return_next_id_by_sequence("dyeing_work_order_dtls_seq", "dyeing_work_order_dtls", $con);
			
			$bodypart="bodypart_".$j;
			
			$fabricdescription="fabricdescription_".$j;
			$gms="gms_".$j;
			$dia="dia_".$j;
			
			$dayingcolor="dayingcolor_".$j;
			$colorrange="colorrange_".$j;
			$proccessid="proccessid_".$j;
			$shade="shade_".$j;
			
			
			$woqnty="woqnty_".$j;
			$rate="rate_".$j;
			$amount="amount_".$j;
			$remark="remark_".$j;


			if(str_replace("'", "", $cbo_wo_basis)==1)
			{
				$stitchlength="stitchlength_".$j;
				$count="count_".$j;
				$processloss="processloss_".$j;
				$issueno="issueno_".$j;
				$issueid="issueid_".$j;
				$issueqty="issueqty_".$j;
				$issuedate="issuedate_".$j;
				if($db_type==0)
				{
					if ($$issuedate!="") $issuedate  = change_date_format($$issuedate, "yyyy-mm-dd", "-");
				}
	
				if($db_type==2)
				{
					if ($$issuedate!="") $issuedate  = change_date_format($$issuedate, "yyyy-mm-dd", "-",1);
				}
				
				if ($data_array_dtls != "") $data_array_dtls .= ",";
				$data_array_dtls .= "(" . $dtls_id . "," . $id . ",'" . $$issueno . "','" . $issuedate . "','" . $$fabricdescription . "','" . $$dia . "','" . $$bodypart. "','" . $$gms .  "','".$$stitchlength. "','" . $$colorrange .  "','".$$dayingcolor. "','" . $$shade .  "','".$$processloss. "','" . $$proccessid. "','" . $$count. "','" . $$issueqty. "','" . $$rate. "','" . $$amount."','".$$remark."','".$$woqnty."','".$$issueid."','" . $_SESSION['logic_erp']['user_id'] . "','" . $pc_date_time . "',0)";
			}
			else
			{
				if ($data_array_dtls != "") $data_array_dtls .= ",";
				$data_array_dtls .= "(" . $dtls_id . "," . $id . ",'" . $$fabricdescription . "','" . $$dia . "','" . $$bodypart. "','" . $$gms .  "','". $$colorrange .  "','".$$dayingcolor. "','" . $$shade . "','" . $$proccessid. "','" . $$rate. "','" . $$amount."','".$$remark."','".$$woqnty."','" . $_SESSION['logic_erp']['user_id'] . "','" . $pc_date_time . "',0)";
			}
		}
		
		if(str_replace("'", "", $cbo_wo_basis)==1)
		{
			$dtls_field_array = "id,mst_id,issue_no,issue_date,fabric_desc,machine_dia,body_part_id,machine_gg,stitch_length,color_range,color_id,shade,proccess_loss,process_name,yarn,issue_qnty,rate,amount,remark_text,wo_qty,issue_id,inserted_by,insert_date,is_deleted";
		}
		else
		{
			$dtls_field_array = "id,mst_id,fabric_desc,machine_dia,body_part_id,machine_gg,color_range,color_id,shade,process_name,rate,amount,remark_text,wo_qty,inserted_by,insert_date,is_deleted";
		}

		//echo "10**insert into dyeing_work_order_mst (".$field_array.") values ".$data_array;oci_rollback($con);disconnect($con);die;
		
		$rID=sql_insert("dyeing_work_order_mst",$field_array,$data_array,0);
		$rID1=sql_insert("dyeing_work_order_dtls",$dtls_field_array,$data_array_dtls,0);

		//echo "10**$rID**$rID1";oci_rollback($con);disconnect($con);die;
		if($db_type==0)
		{
			if($rID  && $rID1){
				mysql_query("COMMIT");
				echo "0**".$id."**".$new_wo_number[0];
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$rID."**".$rID1;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID  && $rID1){
				oci_commit($con);
				echo "0**".$id."**".$new_wo_number[0];
			}
			else{
				oci_rollback($con);
				echo "10**".$rID."**".$rID1;
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
		
		$approved_sql = "select a.approved from dyeing_work_order_mst a where a.id=$update_id and a.status_active=1 and a.approved!=0 and a.is_deleted=0";
		$approved_arr=sql_select($approved_sql); 
		if(count($approved_arr)>0)
		{
			if($approved_arr[0][csf('approved')]==1)
			{
				echo "13**Update Or Delete not allowed. Approved Found";disconnect($con);die;				
			}
			else
			{
				echo "13**Update Or Delete not allowed. Partial Approved Found";disconnect($con);die;			
			}
		}
		 	
			$pre_barcode_res=sql_select("select * from dyeing_work_order_dtls where mst_id=$update_id and status_active=1 and is_deleted=0");
			$pre_barcode_arr=array();
			$pre_ids=array();
			foreach ($pre_barcode_res as $row) 
			{
				$barcode_nos=$row[csf('barcode_no')];
				$brr_arr=explode(",", $barcode_nos);
				foreach ($brr_arr as $bar_code) {
					array_push($pre_barcode_arr,$bar_code);
				}
				array_push($pre_ids, $row[csf('id')]);

				
			}
			$master_field_array = "company_id*currency_id*ready_approval*exchange_rate*pay_mode*wo_date*delivery_date*booking_month*buyer_id*within_group*booking_no*fabric_sales_order_no*po_breakdown_id*style_ref_no*dyeing_source*dyeing_compnay_id*attention*remark*updated_by*update_date";

			$master_data_array = "". $cbo_company_name . "*" . $cbo_currency ."*" . $cbo_ready_approval . "*" . $txt_exchange_rate . "*" . $cbo_pay_mode . "*" . $txt_wo_date . "*" . $txt_delivery_date . "*" . $cbo_booking_month . "*" . $cbo_buyer_name ."*".$cbo_within_group. "*" . $cbo_fabric_booking_no . "*" . $cbo_fso_no ."*".$po_breakdown_id."*".$cbo_style_ref_no."*".$cbo_dyeing_source."*".$cbo_dyeing_comp .  "*" . $txt_attention . "*" . $txt_remark . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

		
		$dtls_sql="select id from dyeing_work_order_dtls where mst_id=$update_id and status_active=1 and is_deleted=0";
		$dtls_result=sql_select($dtls_sql);
		$dtls_id_string='';
		foreach ($dtls_result as $row) {
			$dtls_id_string.=$row[csf('id')].",";
		}
		$dtls_id_string=chop($dtls_id_string,",");
		$delete_check_sql="select a.bill_no, b.wo_dtls_id from wo_bill_mst a, wo_bill_dtls b where a.id=b.mst_id and  b.wo_dtls_id in ($dtls_id_string) and b.status_active=1 and b.is_deleted=0 and b.entry_form=422 and a.status_active=1 and a.is_deleted=0";
		$delete_check_result=sql_select($delete_check_sql);
		$dtls_id_in_wo_bill=array();
		$delete_data=array();
		foreach ($delete_check_result as $row) {
			array_push($delete_check_result, $row[csf('wo_dtls_id')]);
			$delete_data[$row[csf('wo_dtls_id')]]=$row[csf('bill_no')];
		}

		$rID=sql_update("dyeing_work_order_mst",$master_field_array,$master_data_array,"id","".$update_id."",0);

		$data_array_dtls="";
		
		$barcode_cur=array();
		$dtls_id_cur=array();
		$field_array_status = "updated_by*update_date*status_active*is_deleted";
		$data_array_status = $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*0*1";
		$data_update_status ='';
		$rID1=true;
		for ($j = 1; $j <= $tot_row; $j++) 
		{
			$detailsId="detailsId_".$j;
			$bodypart="bodypart_".$j;
			
			$fabricdescription="fabricdescription_".$j;
			$gms="gms_".$j;
			$dia="dia_".$j;
			
			$dayingcolor="dayingcolor_".$j;
			$colorrange="colorrange_".$j;
			$proccessid="proccessid_".$j;
			$shade="shade_".$j;
			
			$woqnty="woqnty_".$j;
			$rate="rate_".$j;
			$amount="amount_".$j;
			$remark="remark_".$j;

			if(str_replace("'", "", $cbo_wo_basis)==1)
			{
				$stitchlength="stitchlength_".$j;
				$count="count_".$j;
				$issueno="issueno_".$j;
				$issuedate="issuedate_".$j;
				$processloss="processloss_".$j;
				$issueqty="issueqty_".$j;
				$issueid="issueid_".$j;

				if($db_type==0)
				{
					if ($$issuedate!="") $issuedate  = change_date_format($$issuedate, "yyyy-mm-dd", "-");
				}
	
				if($db_type==2)
				{
					if ($$issuedate!="") $issuedate  = change_date_format($$issuedate, "yyyy-mm-dd", "-",1);
				}
				
	
				array_push($dtls_id_cur, $$detailsId);
	
				if(str_replace("'","",$$detailsId)*1 ==0 )
				{
					$dtls_id = return_next_id_by_sequence("dyeing_work_order_dtls_seq", "dyeing_work_order_dtls", $con);
					if ($data_array_dtls != "") $data_array_dtls .= ",";
					$data_array_dtls .= "(" . $dtls_id . "," . $update_id . ",'" . $$issueno . "','" . $issuedate . "','" . $$fabricdescription . "','" . $$dia . "','" . $$bodypart. "','" . $$gms .  "','".$$stitchlength. "','" . $$colorrange .  "','".$$dayingcolor. "','" . $$shade .  "','".$$processloss. "','" . $$proccessid. "','" . $$count. "','" . $$issueqty. "','" . $$rate. "','" . $$amount."','".$$remark."','".$$woqnty."','".$$issueid."','" . $_SESSION['logic_erp']['user_id'] . "','" . $pc_date_time . "',0)";
				}
				else
				{
					$field_update_array = "color_range*color_id*shade*proccess_loss*process_name*yarn*issue_qnty*rate*amount*remark_text*wo_qty*issue_id*updated_by*update_date";
					$data_update_status="'".$$colorrange .  "'*'".$$dayingcolor. "'*'" . $$shade .  "'*'".$$processloss. "'*'" . $$proccessid. "'*'" . $$count. "'*'" . $$issueqty. "'*'" . $$rate. "'*'" . $$amount."'*'".$$remark."'*'".$$woqnty."'*'".$$issueid."'*'".$_SESSION['logic_erp']['user_id']."'*'". $pc_date_time ."'";
					$rID1=sql_update("dyeing_work_order_dtls", $field_update_array, $data_update_status, "id", $$detailsId, 0);
					//$rID3 = sql_update("dyeing_work_order_dtls", $field_update_array, $data_update_status, "id", $update_id, 1);
					if($rID1==false)
					{
						break;
					}
				}
			}
			else
			{
				array_push($dtls_id_cur, $$detailsId);
	
				if(str_replace("'","",$$detailsId)*1 ==0 )
				{
					$dtls_id = return_next_id_by_sequence("dyeing_work_order_dtls_seq", "dyeing_work_order_dtls", $con);
					if ($data_array_dtls != "") $data_array_dtls .= ",";
					$data_array_dtls .= "(" . $dtls_id . "," . $update_id . ",'" . $$fabricdescription . "','" . $$dia . "','" . $$bodypart. "','" . $$gms . "','" . $$colorrange .  "','".$$dayingcolor. "','" . $$shade . "','" . $$proccessid. "','" . $$rate. "','" . $$amount."','".$$remark."','".$$woqnty."','" . $_SESSION['logic_erp']['user_id'] . "','" . $pc_date_time . "',0)";
				}
				else
				{
					$field_update_array = "color_range*color_id*shade*process_name*rate*amount*remark_text*wo_qty*updated_by*update_date";
					$data_update_status="'".$$colorrange .  "'*'".$$dayingcolor. "'*'" . $$shade . "'*'" . $$proccessid. "'*'" . $$rate. "'*'" . $$amount."'*'".$$remark."'*'".$$woqnty."'*'".$_SESSION['logic_erp']['user_id']."'*'". $pc_date_time ."'";
					$rID1=sql_update("dyeing_work_order_dtls", $field_update_array, $data_update_status, "id", $$detailsId, 0);
					//echo "10**".$field_update_array."<br>".$data_update_status;die;
					//$rID3 = sql_update("dyeing_work_order_dtls", $field_update_array, $data_update_status, "id", $update_id, 1);
					if($rID1==false)
					{
						break;
					}
				}
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
						echo "111**".$delete_data[$$detailsId];
					
					}
					else if($db_type==2 || $db_type==1 )
					{
						
						oci_rollback($con);
						echo "111**".$delete_data[$$detailsId];
						
					}
					disconnect($con);
					die;
				}else{
					$statusChange = sql_multirow_update("dyeing_work_order_dtls", $field_array_status, $data_array_status, "id", $value, 0);

				}
				
			}
			if($statusChange==false){
				$rID2=false;
				break;
			}
			
		}
		if(str_replace("'", "", $cbo_wo_basis)==1)
		{
			$dtls_field_array = "id,mst_id,issue_no,issue_date,fabric_desc,machine_dia,body_part_id,machine_gg,stitch_length,color_range,color_id,shade,proccess_loss,process_name,yarn,issue_qnty,rate,amount,remark_text,wo_qty,issue_id,inserted_by,insert_date,is_deleted";
		}
		else
		{
			$dtls_field_array = "id,mst_id,fabric_desc,machine_dia,body_part_id,machine_gg,color_range,color_id,shade,process_name,rate,amount,remark_text,wo_qty,inserted_by,insert_date,is_deleted";
		}

		//echo "10**insert into dyeing_work_order_mst (".$field_array.") values ".$data_array;
		$rID3=true;
		if($data_array_dtls!="")
		{
		 // echo "10*insert into dyeing_work_order_dtls($dtls_field_array)values".$data_array_dtls;die;
		 $rID3=sql_insert("dyeing_work_order_dtls",$dtls_field_array,$data_array_dtls,0);
		}

		$txt_dyeing_wo_order_no=str_replace("'", "", $txt_dyeing_wo_order_no);
		
		if($db_type==0)
		{
			if($rID && $rID1 && $rID2 && $rID3){
				mysql_query("COMMIT");
				echo "1**".str_replace("'","",$update_id)."**".$txt_dyeing_wo_order_no;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$rID ."**". $rID1 ."**". $rID2 ."**". $rID3;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID1 && $rID2 && $rID3){
				oci_commit($con);
				echo "1**".str_replace("'","",$update_id)."**".$txt_dyeing_wo_order_no;
			}
			else{
				oci_rollback($con);
				echo "10**".$rID ."**". $rID1 ."**". $rID2 ."**". $rID3;
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

		$approved_sql = "select a.approved from dyeing_work_order_mst a where a.id=$update_id and a.status_active=1 and a.approved!=0 and a.is_deleted=0";
		$approved_arr=sql_select($approved_sql); 
		if(count($approved_arr)>0)
		{
			if($approved_arr[0][csf('approved')]==1)
			{
				echo "13**Update Or Delete not allowed. Approved Found";disconnect($con);die;				
			}
			else
			{
				echo "13**Update Or Delete not allowed. Partial Approved Found";disconnect($con);die;			
			}
		}

		$delete_check_sql="select a.bill_no, b.wo_dtls_id from wo_bill_mst a, wo_bill_dtls b, dyeing_work_order_mst c,dyeing_work_order_dtls d where a.id=b.mst_id and c.id=d.mst_id and   b.wo_dtls_id =d.id and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and b.entry_form=422 and c.id=$update_id";

		$delete_check_result=sql_select($delete_check_sql);
		if(count($delete_check_result))
		{
			echo "112**".$delete_check_result[0][csf('a.bill_no')];
			disconnect($con);
			die;
		}

		
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$rID=sql_delete("dyeing_work_order_mst",$field_array,$data_array,"id","".$update_id."",0);

		$sql=sql_select("select * from dyeing_work_order_dtls where mst_id=$update_id and status_active=1");
		$rID2=1;
		if(count($sql)){
			$deleted_field_array="updated_by*update_date*status_active*is_deleted";
			$deleted_data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
			$rID2=sql_delete("dyeing_work_order_dtls",$deleted_field_array,$deleted_data_array,"mst_id","".$update_id."",0);
		}
		if($db_type==0)
		{
			if($rID && $rID2){
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
			if($rID && $rID2){
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
	echo load_html_head_contents("Dyeing Work Order", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$data = explode("**", $data);
    
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_library = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[1]'","image_location");
	$country_arr = return_library_array("select id,country_name from lib_country", 'id', 'country_name');
	$supplier_arr = return_library_array("select id,supplier_name from lib_supplier", 'id', 'supplier_name');
	$dyeing_com=return_library_array("select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id","supplier_name");
	$floor_arr = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name');

	$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0","id","color_name");
	$yarn_count_details=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");

	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}

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
								<p>
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
								</p>
							</td>
						</tr>
						<tr>
							<td align="center" style="font-size:20px"><u ><span style="padding: 3px;"><b>Dyeing Work Order</b><span></u></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>

		<?
		$cur='';
		$dataArray = sql_select("select a.id,a.inserted_by,a.do_no,a.do_number_prefix,a.do_number_prefix_num,a.company_id,a.buyer_id,a.within_group,a.currency_id,a.exchange_rate,a.booking_no,a.fabric_sales_order_no,a.po_breakdown_id,a.style_ref_no,a.pay_mode,a.wo_date,a.delivery_date,a.booking_month,a.attention,a.remark,a.dyeing_source,a.dyeing_compnay_id,a.approved
		from dyeing_work_order_mst a
		where a.status_active=1 and a.id=$data[0]");
		// echo "select a.id,a.inserted_by,a.do_no,a.do_number_prefix,a.do_number_prefix_num,a.company_id,a.buyer_id,a.within_group,a.currency_id,a.exchange_rate,a.booking_no,a.fabric_sales_order_no,a.po_breakdown_id,a.style_ref_no,a.pay_mode,a.wo_date,a.delivery_date,a.booking_month,a.attention,a.remark,a.dyeing_source,a.dyeing_compnay_id from dyeing_work_order_mst a where a.status_active=1 and a.id=$data[0]";die;

		$currency_id=$dataArray[0][csf('currency_id')];
		$approved=$dataArray[0][csf('approved')];
		$inserted_by=$dataArray[0][csf('inserted_by')];
		$buyer='';
		?>
		<table width="1400" style="margin-top:10px;margin-right: 10px;">
			<tr>
				<td ><b>WO No:</b></td>
				<td><? echo $dataArray[0][csf('do_no')]; ?></td>
				<td>
					<b>WO Date:</b>
				</td>
				<td><? echo change_date_format($dataArray[0][csf('wo_date')]); ?></td>
				<td>
					<b>Company Name:</b> 
				</td>
				<td><? echo $company_library[$dataArray[0][csf('company_id')]]; ?></td>
				
			</tr>
			<tr>
				<td ><b>Dyeing Company:</b></td>
				<td>
					<? echo $dyeing_com[$dataArray[0][csf('dyeing_compnay_id')]]; ?>
				</td>
				<td><b>Address:</b></td>
				<td colspan="3" >
					<?
                    if($dataArray[0][csf('pay_mode')]==3 || $dataArray[0][csf('pay_mode')]==5)
                    {
						$address='';
                    }
                    else
                    {
                        $party_add=$dataArray[0][csf('dyeing_compnay_id')];
                       // echo "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$party_add";die;
                        $nameArray=sql_select( "select address_1 from  lib_supplier where id=$party_add"); 
                        foreach ($nameArray as $result)
                        { 
                            $address="";
                            if($result!="") $address=$result[csf('address_1')];
                        }
                        
                    }
                ?>
					<p  style="word-wrap: break-word; word-break: break-all;"> <? echo $address; ?></p>
				</td>
				
				
			</tr>
			<tr>
				<td><b>Buyer Name :</b></td>
				<td> <?php
					
					if($dataArray[0][csf('within_group')]==1)
					{
						$buyer=$company_library[$dataArray[0][csf('buyer_id')]];
						echo $company_library[$dataArray[0][csf('buyer_id')]];
					}
					else
					{
						$buyer=$buyer_arr[$dataArray[0][csf('buyer_id')]];
						echo $buyer_arr[$dataArray[0][csf('buyer_id')]];
					}
				

				  ?>
				  	
				</td>
				<td><b>FSO No  :</b> </td>
				<td><?php echo $dataArray[0][csf('fabric_sales_order_no')]; ?></td>
				<td><b>Fab. Booking No :</b> </td>
				<td><?php echo $dataArray[0][csf('booking_no')]; ?></td>
			</tr>

			<tr>
				<td>
					<b>Style Ref. No:</b>
					
				</td>
				<td><?php echo $dataArray[0][csf('style_ref_no')];  ?></td>
				<td>
					<b>Delivery Date :</b> 
				</td>
				<td><? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
				<td>
					<b>Pay Mode:</b>
					
				</td>
				<td><?php echo $pay_mode[$dataArray[0][csf('currency_id')]];  ?></td>
				
			</tr>
			 
		
			
		<tr>
			<td><b>Currency:</b></td>
			<td><? echo $currency[$dataArray[0][csf('currency_id')]];
				$cur=$currency[$dataArray[0][csf('currency_id')]];
			 ?>
			 	
			 </td>
			 <td><b>Conversion Rate:</b></td>
			<td colspan="3"><? echo $dataArray[0][csf('exchange_rate')]; ?></td>
		</tr>
		<tr>
			<td><b>Attention : </b></td>
			<td colspan="5"> <?php echo $dataArray[0][csf('attention')] ?></td>
		</tr>
		<tr>
			<td><b>Remarks : </b></td>
			<td colspan="4"> <?php echo $dataArray[0][csf('remark')] ?></td>
			<td style="font-size: 22px;color:red;"><b><? if ($approved==1) echo "Approved"; else if ($approved==3) echo "Partial Approved"; ?></b></td>
		</tr>
	</table>
	<?php $tot_wo_qnty=0;
				$tot_rate=0;
				$tot_amount=0; ?>
	<table style="margin-top:10px;" width="1400" border="1" rules="all" cellpadding="3" cellspacing="0"
	class="rpt_table">
		
		<thead>
			<th width="30">SL No</th>
			<th width="100">Issue No</th>
			<th width="110">Body Part</th>
			<th width="210">Fabric Construction & Composition</th>
			<th width="120">GSM</th>
			<th width="80">DIA</th>
			<th width="50">S.L</th>
			<th width="100">Yarn Count</th>
			<th width="160">Dyeing Color Name</th>
			<th width="100">Color Range</th>
			<th width="60">Shade %</th>
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
			<th >Remarks</th>
		</thead>
		<tbody>
			<? 
				$result_details=sql_select("select * from dyeing_work_order_dtls where status_active=1 and is_deleted=0 and mst_id=$data[0]");
				
				
				$grouping="";
				$i=0;
				$rate=0;
				$amount=0;
				$wo_qnty=0;
				
				foreach ($result_details as $row) {

					$yarn_count_arr=explode(",",$row[csf('yarn')]);
					$Ycount='';
					foreach($yarn_count_arr as $count_id)
					{
						if($Ycount=='') $Ycount=$yarn_count_details[$count_id]; else $Ycount.=",".$yarn_count_details[$count_id];
					}

					$color='';
					$color_id=array_unique(explode(",",$row[csf('color_id')]));
					foreach($color_id as $val)
					{
						if($val>0) $color.=$color_arr[$val].",";
					}
					$color=chop($color,',');
					
					$process_names=explode(",", $row[csf('process_name')]);
					$process='';
					foreach($process_names as $id)
				    {
				    	$process.=$conversion_cost_head_array[$id].",";
				    }
				    $process=chop($process,',');
				    $cons_comp=$constructtion_arr[$row[csf('fabric_desc')]].", ".$composition_arr[$row[csf('fabric_desc')]];
						
					$grouping_check=$row[csf('buyer_id')]."***".$row[csf('style_ref_no')]."***".$row[csf('booking_no')]."***".$row[csf('fabric_sales_order_no')]."***".$process."***".$row[csf('proccess_loss')];
					if($grouping!=$grouping_check)
					{
						
						if($i>0){?>

							<tr bgcolor="#ddd">
								<td colspan="11" align="right"><b>Total</b></td>
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
								<td colspan="<?php echo $data[2]==1 ? 15:13;?>" align="left">
									<?php 

									echo "Buyer - ".$buyer." , Style Ref. No - ".$dataArray[0][csf('style_ref_no')]." , Fab. Booking No - ".$dataArray[0][csf('booking_no')] .", FSO No - ".$dataArray[0][csf('fabric_sales_order_no')].", Process Loss %-".$row[csf('proccess_loss')].", Process Name -".$process; ?>
								</td>
							</tr>
						<?
							$rate=0;
							$amount=0;
							$wo_qnty=0;

						}else{?>
							<tr bgcolor="#ddd">
								<td colspan="<?php echo $data[2]==1 ? 15:13;?>" align="left">
									<?php echo "Buyer - ".$buyer." , Style Ref. No - ".$dataArray[0][csf('style_ref_no')]." , Fab. Booking No - ".$dataArray[0][csf('booking_no')] .", FSO No - ".$dataArray[0][csf('fabric_sales_order_no')].", Process Loss %-".$row[csf('proccess_loss')].", Process Name -".$process; ?>
								</td>
							</tr>
						<?}
					}
					$i++;
					if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td align="center"><? echo $i; ?></td>
						<td><? echo $row[csf('issue_no')];?></td>
						<td><? echo $body_part[$row[csf('body_part_id')]];?></td>
						<td><? echo $cons_comp;?></td>
						<td><? echo $row[csf('machine_gg')];?></td>
						<td><? echo $row[csf('machine_dia')];?></td>
						<td><? echo $row[csf('stitch_length')];?></td>
						<td><? echo $Ycount;?></td>
						<td><? echo $color;?></td>
						<td><? echo $color_range[$row[csf('color_range')]];?></td>
						<td><? echo $row[csf('shade')];?></td>
						<td align="right"><? echo number_format($row[csf('wo_qty')],2);?></td>
						
						<?php 
			            	if($data[2]==1)
			            	{
			            		$rate+=$row[csf('rate')];
								$amount+=$row[csf('amount')];
								
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
						<td colspan="11" align="right"><b>Total</b></td>
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
            <th colspan="11" align="right"><b>Total</b></th>
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
    
	<?
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
	    <br>
	    <br>
	<?php }?>

    <?
   $appSql = "select a.APPROVED_BY, a.APPROVED_DATE,b.USER_FULL_NAME,c.CUSTOM_DESIGNATION from approval_mst a,USER_PASSWD b,LIB_DESIGNATION c where a.mst_id =$data[0] and a.APPROVED_BY=b.id and b.DESIGNATION=c.id and c.STATUS_ACTIVE=1 and c.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0
   union all
   select b.id as APPROVED_BY, a.INSERT_DATE as APPROVED_DATE,b.USER_FULL_NAME,c.CUSTOM_DESIGNATION from dyeing_work_order_mst a,USER_PASSWD b,LIB_DESIGNATION c where a.id =$data[0] and a.INSERTED_BY=b.id and b.DESIGNATION=c.id and c.STATUS_ACTIVE=1 and c.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0";
   // echo $appSql;die;
   $appSqlRes=sql_select($appSql);
   $userDtlsArr=array();
   foreach($appSqlRes as $row){
	   $userDtlsArr[$row['APPROVED_BY']]="<div><b>".$row['USER_FULL_NAME']."</b></div><div><b>".$row['CUSTOM_DESIGNATION']."</b></div><div><small>".$row['APPROVED_DATE']."</small></div>";
   }

   
   echo get_app_signature(308, $data[1], "900 px",$template_id, 50,$inserted_by,$userDtlsArr); 
    ?>
</div>
<?
exit();
}


if($action=="populate_fso_wise_details")
{
	//echo $data;die;
	$data=explode("**", $data);

	$ref_datas_str=$data[0];
	$po_breakdown_id=$data[1];

	//$row[csf('fso_id')]."__".$row[csf('determination_id')]."__".$row[csf('gsm_weight')]."__".$row[csf('color_id')]

	$ref_datas_set = explode(",",$ref_datas_str);

	foreach ($ref_datas_set as $val) {
		$ref_data_arr = explode("__",$val);
		$determination_id = $ref_data_arr[1];
		$gsm_weight = $ref_data_arr[2];
		$color_id = $ref_data_arr[3];

		$ref_data_array[$determination_id][$gsm_weight][$color_id]=$determination_id;

		$all_deter_arr[$determination_id]=$determination_id;
	}

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0","id","color_name");

	$sql="SELECT a.id as fso_id, a.job_no, a.sales_booking_no, a.customer_buyer, b.body_part_id, a.within_group, a.company_id, b.determination_id, b.fabric_desc, b.gsm_weight, b.dia, b.color_id, b.color_range_id,  process_seq_main, b.grey_qty as fso_qnty
		from fabric_sales_order_mst a, fabric_sales_order_dtls b
		where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=$po_breakdown_id and b.determination_id in (".implode(',',$all_deter_arr).")
		order by a.id";
	$result = sql_select($sql); 
	foreach ($result as $row) 
	{
		if($ref_data_array[$row[csf('determination_id')]][$row[csf('gsm_weight')]][$row[csf('color_id')]])
		{
			$sales_company = $row[csf('company_id')];
			$datastring= $row[csf('fso_id')] .'*'. $row[csf('body_part_id')] .'*'. $row[csf('determination_id')] .'*'. $row[csf('gsm_weight')] .'*'. $row[csf('dia')] .'*'. $row[csf('color_id')] .'*'. $row[csf('color_range_id')];
			$data_array[$datastring]['fso_id']=$row[csf('fso_id')];
			$data_array[$datastring]['customer_buyer']=$row[csf('customer_buyer')];
			$data_array[$datastring]['sales_booking_no']=$row[csf('sales_booking_no')];
			$data_array[$datastring]['body_part_id']=$row[csf('body_part_id')];
			$data_array[$datastring]['determination_id']=$row[csf('determination_id')];
			$data_array[$datastring]['fabric_desc']=$row[csf('fabric_desc')];
			$data_array[$datastring]['gsm_weight']=$row[csf('gsm_weight')];
			$data_array[$datastring]['dia']=$row[csf('dia')];
			$data_array[$datastring]['color_id']=$row[csf('color_id')];
			$data_array[$datastring]['color_range_id']=$row[csf('color_range_id')];
			$data_array[$datastring]['fso_qnty']+=$row[csf('fso_qnty')];

			$txtProcessWiseRateArr =explode("@@", $row[csf('process_seq_main')]);
			$dyeProcessRateArr = $txtProcessWiseRateArr[1];
			$dyeProcessRateArrDtls = explode("__",$dyeProcessRateArr);

			$DYEING_SUBPROCESS = $dyeProcessRateArrDtls[4];
			$dye_sub_f = explode(",",$DYEING_SUBPROCESS);
			$txtProcessNameDyeing=$txtProcessIdDyeing=""; $dye_rate = 0;
			foreach ($dye_sub_f as $subr )
			{
				$dye_sub_s = explode("_",$subr);
				$txtProcessIdDyeing .= $dye_sub_s[0].",";
				$txtProcessNameDyeing .= $conversion_cost_head_array[$dye_sub_s[0]].",";
				$dye_rate +=$dye_sub_s[2]*1;
				//echo $dye_sub_s[2]."<br>";
			}

			$data_array[$datastring]['process_id'] .=$txtProcessIdDyeing.',';
			$data_array[$datastring]['process_name'] .=$txtProcessNameDyeing.',';
			$data_array[$datastring]['dye_rate'] +=$dye_rate;
		}
	}

	$pre_wo_sql="SELECT a.fabric_desc, a.machine_dia, a.body_part_id, a.machine_gg, a.color_range, a.color_id, a.wo_qty from dyeing_work_order_dtls a, dyeing_work_order_mst b where a.mst_id=b.id and b.po_breakdown_id=$po_breakdown_id and a.status_active=1 and a.is_deleted=0";
	//echo $sql;die;
	$pre_wo_sql_result=sql_select($pre_wo_sql);

	foreach ($pre_wo_sql_result as $row) {
		$all_dtls_id_arr[$row[csf('id')]]=$row[csf('id')];

		$predatastring= $po_breakdown_id .'*'. $row[csf('body_part_id')] .'*'. $row[csf('fabric_desc')] .'*'. $row[csf('machine_gg')] .'*'. $row[csf('machine_dia')] .'*'. $row[csf('color_id')] .'*'. $row[csf('color_range')];

		$pre_data_array[$predatastring]['pre_wo_qnty']+=$row[csf('wo_qty')];
	}
	/* $finish_rate_come_from=return_field_value("finish_rate_come_from","variable_settings_production","variable_list=74 and company_name=$sales_company and status_active=1","finish_rate_come_from");
	$rate_disabled='';
	if($finish_rate_come_from==3)
	{
		$rate_disabled = "disabled";

	} */
	?>
	<script type="text/css">
		.wordwrapbreak {
			word-wrap: break-word;
			word-break: break-all;

		}
	</script>
	<table  width="1640" cellspacing="2" cellpadding="0" border="1" rules="all" class="rpt_table" id="scanning_tbl" align="left">
	<thead>
		<tr>
			<th width="30">SL No</th>
			<th width="100">Buyer</th>
			<th width="100">Booking No.</th>
			<th width="80">Body Part</th>
			<th width="200">Fabric Construction & Composition</th>
			<th width="50">GSM</th>
			<th width="50">DIA</th>
			<th width="100">Dyeing Color Name</th>
			<th width="100">Color Range</th>
			<th width="100">Shade %</th>
			<th width="120">Process Name</th>
			<th width="80">FSO Qty</th>
			<th width="80">Already WO Qty</th>
			<th width="80">Balance WO Qty</th>
			<th width="80">WO Qty.</th>
			<th width="70">Rate</th>
			<th width="80">Amount</th>
			<th width="120">Remarks</th>
		</tr>	 																	
	</thead>
	<tbody>
		<?
		$data_array[$datastring]['dye_rate'];
		$i=1;
		foreach ($data_array as $data_str => $row) 
		{
			if ($i%2==0) $trColor="#E9F3FF"; else $trColor="#FFFFFF";
			$fso_rate = number_format($row['dye_rate'],2);
			
			$process_name = implode(",",array_unique(explode(",",chop($row['process_name'],","))));
			$process_id = implode(",",array_unique(explode(",",chop($row['process_id'],","))));

			$pre_wo_qnty = $pre_data_array[$data_str]['pre_wo_qnty'];
			$balance_wo_qnty = $row['fso_qnty'] - $pre_data_array[$data_str]['pre_wo_qnty'];

			$wo_amount =  $balance_wo_qnty*$fso_rate;
			?>
			<tr id="tr_<? echo $i;?>" bgcolor="<? echo $trColor;?>" align='center' valign='middle' >
				<td width="30"><? echo $i;?></td>
				<td width="100" class="wordwrapbreak"><? echo $buyer_arr[$row['customer_buyer']];?></td>
				<td width="100" class="wordwrapbreak"><? echo $row['sales_booking_no'];?></td>
				<td width="80" class="wordwrapbreak"><? echo $body_part[$row['body_part_id']];?></td>
				<td width="200" class="wordwrapbreak"><? echo $row['fabric_desc'];?></td>
				<td width="50" class="wordwrapbreak"><? echo $row['gsm_weight'];?></td>
				<td width="50" class="wordwrapbreak"><? echo $row['dia'];?></td>
				<td width="100" class="wordwrapbreak"><? echo $color_arr[$row['color_id']];?></td>
				<td width="100" class="wordwrapbreak"><? echo $color_range[$row['color_range_id']];?></td>
				<td width="100" class="wordwrapbreak"><input type="text" class="text_boxes" style="width: 80px;" id="shade_<? echo $i; ?>" name="shade[]" placeholder="write"/></td>
				<td width="120" class="wordwrapbreak"><? echo $process_name;?></td>
				<td width="80" class="wordwrapbreak"><? echo number_format($row['fso_qnty'],2,'.','');?></td>
				<td width="80" class="wordwrapbreak">
					<input type="text" class="text_boxes_numeric" style="width: 70px;" placeholder="display" value="<? echo number_format($pre_wo_qnty,2,'.','');?>" disabled readonly/>
				</td>
				<td width="80" class="wordwrapbreak">
					<input type="text" class="text_boxes_numeric" style="width: 70px;" name="balancewoqnty[]" id='balancewoqnty_"<? echo $i; ?>"' placeholder="display" value="<? echo number_format($balance_wo_qnty,2,'.','');?>" disabled readonly/>
				</td>
				<td width="80" class="wordwrapbreak"><input type="text" class="text_boxes_numeric" style="width: 70px;" name="woqnty[]" id="woqnty_<? echo $i; ?>" placeholder="write" value="<? echo number_format($balance_wo_qnty,2,'.','');?>" onkeyup="calculate_amount(<? echo $i;?>)"/></td>
				<td width="70" class="wordwrapbreak">
					<input type="text" class="text_boxes_numeric" style="width: 60px;" id="rate_<? echo $i; ?>" name="rate[]" value="<? echo $fso_rate;?>" onkeyup="fnc_rate_chk('<? echo $i;?>');calculate_amount(<? echo $i;?>)" onblur="fnc_rate_chk('<? echo $i;?>')"/>
					<input type="hidden" class="text_boxes_numeric" style="width: 60px;" id="hidefsorate_<? echo $i; ?>" name="hidefsorate[]"  value="<? echo $fso_rate;?>" readonly disabled/>
				</td>
				<td width="80" class="wordwrapbreak"><input type="text" class="text_boxes_numeric" style="width: 70px;" id="amount_<? echo $i; ?>" name="amount[]" value="<? echo number_format($wo_amount,2);?>" placeholder="display" readonly/></td>
				<td width="120" class="wordwrapbreak">
					<input type="text" class="text_boxes" style="width: 110px;" id="remark_<? echo $i; ?>" name="remark[]" placeholder="write"/>

					<input type='hidden' name='proccessid[]' id="proccessid_<? echo $i; ?>" value="<? echo $process_id;?>" />
					<input type='hidden' name='bodypart[]' id="bodypart_<? echo $i; ?>" value="<? echo $row['body_part_id'];?>" />
					<input type='hidden' name='fabricdescription[]' id="fabricdescription_<? echo $i; ?>" value="<? echo $row['determination_id'];?>" />
					<input type='hidden' name='gms[]' id="gms_<? echo $i; ?>" value="<? echo $row['gsm_weight'];?>" />
					<input type='hidden' name='dia[]' id="dia_<? echo $i; ?>" value="<? echo $row['dia'];?>" />
					<input type='hidden' name='dayingcolor[]' id="dayingcolor_<? echo $i; ?>" value="<? echo $row['color_id'];?>" />
					<input type='hidden' name="colorrange[]" id="colorrange_<?php echo $i;?>"  value="<? echo $row['color_range_id'];?>" />
					<input type="hidden" name="prewoqnty[]" id="prewoqnty_<? echo $i; ?>" />
					<input type='hidden' name='detailsId[]' id="detailsId_<? echo $i; ?>" value="" >
				</td>
			</tr>	 		
			<?
			$i++;
		}
		
		?>
	</tbody>
	<tfoot>
		<tr align='center' valign='middle' >

			<td colspan='15'>
				<input type="hidden" name="total_row" id="total_row" value="0">
			</td>
			<td  align='left' id="total_issue_qnty">
				<input type='hidden' name='totalIssueQnty' id='totalIssueQnty' value='0'  />
			</td>
			<td  align='left' id="total_wo_qnty">
				<input type='hidden' name='totalWoQnty' id='totalWoQnty' value='0'  />
			</td>
			<td></td>
			<td  align='left' id="total_rate">
				<input type='hidden' name='totalRate' id='totalRate' value='0'  />
			</td>
		
			
		</tr>

	</tfoot>
	</table>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	<?

	die;
}



if($action=="populate_fso_wise_details_update")
{
	//echo $data;die;
	$data=explode("**", $data);

	$mst_id=$data[0];
	$po_breakdown_id=$data[1];

	$sql="SELECT id,mst_id,fabric_desc, machine_dia, body_part_id,machine_gg, stitch_length, color_range, color_id, shade, process_name, wo_qty, rate, amount,remark_text from dyeing_work_order_dtls where mst_id=$mst_id and status_active=1 and is_deleted=0";
	//echo $sql;die;
	$wo_result=sql_select($sql);

	$dtls_id_string='';
	foreach ($wo_result as $row) {
		$all_dtls_id_arr[$row[csf('id')]]=$row[csf('id')];
	}

	if(!empty($all_dtls_id_arr))
	{
		$all_dtls_id_arr = array_filter($all_dtls_id_arr);
		if(count($all_dtls_id_arr)>0)
		{
			$all_dtls_ids = implode(",", $all_dtls_id_arr);
			$all_dtls_id_cond=""; $dtlsCond="";
			if($db_type==2 && count($all_dtls_id_arr)>999)
			{
				$all_dtls_id_arr_chunk=array_chunk($all_dtls_id_arr,999) ;
				foreach($all_dtls_id_arr_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$dtlsCond.=" wo_dtls_id in($chunk_arr_value) or ";
				}

				$all_dtls_id_cond.=" and (".chop($dtlsCond,'or ').")";
			}
			else
			{
				$all_dtls_id_cond=" and wo_dtls_id in($all_dtls_ids)";
			}
		}

		$sql_check="select wo_dtls_id from wo_bill_dtls where status_active=1 and entry_form=422 $all_dtls_id_cond";
		$res_check=sql_select($sql_check);
		$check_data=array();
		foreach ($res_check as $k) {
			$bill_array[$row[csf('wo_dtls_id')]]=$row[csf('wo_dtls_id')];
		}
	}

	$pre_wo_sql="SELECT a.fabric_desc, a.machine_dia, a.body_part_id, a.machine_gg, a.color_range, a.color_id, a.wo_qty from dyeing_work_order_dtls a, dyeing_work_order_mst b where a.mst_id=b.id and b.po_breakdown_id=$po_breakdown_id and a.status_active=1 and a.is_deleted=0";
	//echo $sql;die;
	$pre_wo_sql_result=sql_select($pre_wo_sql);

	foreach ($pre_wo_sql_result as $row) {
		$all_dtls_id_arr[$row[csf('id')]]=$row[csf('id')];

		$predatastring= $po_breakdown_id .'*'. $row[csf('body_part_id')] .'*'. $row[csf('fabric_desc')] .'*'. $row[csf('machine_gg')] .'*'. $row[csf('machine_dia')] .'*'. $row[csf('color_id')] .'*'. $row[csf('color_range')];

		$data_array[$predatastring]['pre_wo_qnty']+=$row[csf('wo_qty')];
	}

	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$deter_array=sql_select($sql_deter);
	foreach( $deter_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}



	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0","id","color_name");

	$sql="SELECT a.id as fso_id, a.job_no, a.sales_booking_no, a.customer_buyer, b.body_part_id, a.within_group, a.company_id, b.determination_id, b.fabric_desc, b.gsm_weight, b.dia, b.color_id, b.color_range_id,  process_seq_main, b.grey_qty as fso_qnty
		from fabric_sales_order_mst a, fabric_sales_order_dtls b
		where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=$po_breakdown_id order by a.id";
	$result = sql_select($sql); 
	foreach ($result as $row) 
	{
		$datastring= $row[csf('fso_id')] .'*'. $row[csf('body_part_id')] .'*'. $row[csf('determination_id')] .'*'. $row[csf('gsm_weight')] .'*'. $row[csf('dia')] .'*'. $row[csf('color_id')] .'*'. $row[csf('color_range_id')];

		$fso_data_array[$row[csf('fso_id')]]['customer_buyer']=$row[csf('customer_buyer')];
		$fso_data_array[$row[csf('fso_id')]]['sales_booking_no']=$row[csf('sales_booking_no')];
		$data_array[$datastring]['fso_qnty']+=$row[csf('fso_qnty')];

		$sales_company=$row[csf('company_id')];
	}

	//print_r($data_array);


	/* $finish_rate_come_from=return_field_value("finish_rate_come_from","variable_settings_production","variable_list=74 and company_name=$sales_company and status_active=1","finish_rate_come_from");
	$rate_disabled='';
	if($finish_rate_come_from==3)
	{
		$rate_disabled = "disabled";

	} */
	?>
	<script type="text/css">
		.wordwrapbreak {
			word-wrap: break-word;
			word-break: break-all;

		}
	</script>
	<table  width="1640" cellspacing="2" cellpadding="0" border="1" rules="all" class="rpt_table" id="scanning_tbl" align="left">
	<thead>
		<tr>
			<th width="30">SL No</th>
			<th width="100">Buyer</th>
			<th width="100">Booking No.</th>
			<th width="80">Body Part</th>
			<th width="200">Fabric Construction & Composition</th>
			<th width="50">GSM</th>
			<th width="50">DIA</th>
			<th width="100">Dyeing Color Name</th>
			<th width="100">Color Range</th>
			<th width="100">Shade %</th>
			<th width="120">Process Name</th>
			<th width="80">FSO Qty</th>
			<th width="80">Already WO Qty</th>
			<th width="80">Balance WO Qty</th>
			<th width="80">WO Qty.</th>
			<th width="70">Rate</th>
			<th width="80">Amount</th>
			<th width="120">Remarks</th>
		</tr>	 																	
	</thead>
	<tbody>
		<?
		//$data_array[$datastring]['dye_rate'];
		$i=1;
		foreach ($wo_result as $row) 
		{
			if ($i%2==0) $trColor="#E9F3FF"; else $trColor="#FFFFFF";

			$disabled="";
			if($bill_array[$row[csf('id')]])
			{
				$disabled="disabled='disabled'";
			}

			$datastring= $po_breakdown_id .'*'. $row[csf('body_part_id')] .'*'. $row[csf('fabric_desc')] .'*'. $row[csf('machine_gg')] .'*'. $row[csf('machine_dia')] .'*'. $row[csf('color_id')] .'*'. $row[csf('color_range')];

			$fso_qnty = $data_array[$datastring]['fso_qnty'];
			$pre_wo_qnty = $data_array[$datastring]['pre_wo_qnty'];

			$customer_buyer = $fso_data_array[$po_breakdown_id]['customer_buyer'];
			$sales_booking_no = $fso_data_array[$po_breakdown_id]['sales_booking_no'];

			$compo = $constructtion_arr[$row[csf('fabric_desc')]]. ''. $composition_arr[$row[csf('fabric_desc')]];
			$process_name_arr = explode(",",$row[csf('process_name')]);
			foreach ($process_name_arr as $subr )
			{
				$txtProcessNameDyeing .= $conversion_cost_head_array[ $subr].",";
			}

			$txtProcessNameDyeing = chop($txtProcessNameDyeing,',');


			$balance_wo_qnty = $fso_qnty-$pre_wo_qnty;

			?>
			<tr id="tr_<? echo $i;?>" bgcolor="<? echo $trColor;?>" align='center' valign='middle' >
				<td width="30"><? echo $i;?></td>
				<td width="100" class="wordwrapbreak"><? echo $buyer_arr[$customer_buyer];?></td>
				<td width="100" class="wordwrapbreak"><? echo $sales_booking_no;?></td>
				<td width="80" class="wordwrapbreak"><? echo $body_part[$row[csf('body_part_id')]];?></td>
				<td width="200" class="wordwrapbreak"><? echo $compo;?></td>
				<td width="50" class="wordwrapbreak"><? echo $row[csf('machine_gg')];?></td>
				<td width="50" class="wordwrapbreak"><? echo $row[csf('machine_dia')];?></td>
				<td width="100" class="wordwrapbreak"><? echo $color_arr[$row[csf('color_id')]];?></td>
				<td width="100" class="wordwrapbreak"><? echo $color_range[$row[csf('color_range')]];?></td>
				<td width="100" class="wordwrapbreak"><input type="text" class="text_boxes" style="width: 80px;" id="shade_<? echo $i; ?>" name="shade[]" value="<? echo $row[csf("shade")];?>" placeholder="write" <? echo $disabled;?> /></td>
				<td width="120" class="wordwrapbreak"><? echo $txtProcessNameDyeing;?></td>
				<td width="80" class="wordwrapbreak"><? echo number_format($fso_qnty,2,'.','');?></td>
				<td width="80" class="wordwrapbreak">
					<input type="text" class="text_boxes_numeric" style="width: 70px;" value="<? echo number_format($pre_wo_qnty,2,'.','');?>" placeholder="display" disabled readonly/>
				</td>
				<td width="80" class="wordwrapbreak">
					<input type="text" class="text_boxes_numeric" style="width: 70px;" name="balancewoqnty[]" id='balancewoqnty_"<? echo $i; ?>"' value="<? echo number_format($balance_wo_qnty,2,'.','');?>" placeholder="display" disabled readonly/>
				</td>
				<td width="80" class="wordwrapbreak"><input type="text" class="text_boxes_numeric" style="width: 70px;" name="woqnty[]" id="woqnty_<? echo $i; ?>" placeholder="write" onkeyup="calculate_amount(<? echo $i;?>)" value="<? echo number_format($row[csf('wo_qty')],2,'.','');?>"/></td>
				<td width="70" class="wordwrapbreak">
					<input type="text" class="text_boxes_numeric" style="width: 60px;" id="rate_<? echo $i; ?>" name="rate[]" <? echo $rate_disabled; ?> value="<? echo $row[csf('rate')];?>" <? echo $disabled;?> />
				</td>
				<td width="80" class="wordwrapbreak"><input type="text" class="text_boxes_numeric" style="width: 70px;" id="amount_<? echo $i; ?>" name="amount[]" value="<? echo $row[csf('amount')];?>" placeholder="display" readonly <? echo $disabled;?>/></td>
				<td width="120" class="wordwrapbreak">
					<input type="text" class="text_boxes" style="width: 110px;" id="remark_<? echo $i; ?>" name="remark[]" value="<? echo $row[csf('remark_text')];?>" placeholder="write" <? echo $disabled;?>/>

					<input type='hidden' name='proccessid[]' id="proccessid_<? echo $i; ?>" value="<? echo $row[csf('process_name')];?>" />
					<input type='hidden' name='bodypart[]' id="bodypart_<? echo $i; ?>" value="<? echo $row[csf('body_part_id')];?>" />
					<input type='hidden' name='fabricdescription[]' id="fabricdescription_<? echo $i; ?>" value="<? echo $row[csf('fabric_desc')];?>" />
					<input type='hidden' name='gms[]' id="gms_<? echo $i; ?>" value="<? echo $row[csf('machine_gg')];?>" />
					<input type='hidden' name='dia[]' id="dia_<? echo $i; ?>" value="<? echo $row[csf('machine_dia')];?>" />
					<input type='hidden' name='dayingcolor[]' id="dayingcolor_<? echo $i; ?>" value="<? echo $row[csf('color_id')];?>" />
					<input type='hidden' name="colorrange[]" id="colorrange_<?php echo $i;?>"  value="<? echo $row[csf('color_range')];?>" />
					<input type="hidden" name="prewoqnty[]" id="prewoqnty_<? echo $i; ?>" value="<? echo number_format($pre_wo_qnty,2,'.','');?>"/>
					<input type='hidden' name='detailsId[]' id="detailsId_<? echo $i; ?>" value="<? echo $row[csf('id')];?>" >

				</td>
			</tr>	 		
			<?
			$i++;
		}
		
		?>
	</tbody>
	<tfoot>
		<tr align='center' valign='middle' >

			<td colspan='15'>
				<input type="hidden" name="total_row" id="total_row" value="0">
			</td>
			<td  align='left' id="total_issue_qnty">
				<input type='hidden' name='totalIssueQnty' id='totalIssueQnty' value='0'  />
			</td>
			<td  align='left' id="total_wo_qnty">
				<input type='hidden' name='totalWoQnty' id='totalWoQnty' value='0'  />
			</td>
			<td></td>
			<td  align='left' id="total_rate">
				<input type='hidden' name='totalRate' id='totalRate' value='0'  />
			</td>
		
			
		</tr>

	</tfoot>
	</table>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	<?

	die;
}
?>