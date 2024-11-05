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
			show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_dyeing_source').value+'_'+document.getElementById('cbo_dyeing_comp').value+'_'+document.getElementById('within_group').value+'_'+document.getElementById('cbo_year_selection').value, 'create_work_order_search_list_view', 'search_div', 'fso_fabric_service_work_order_controller', 'setFilterGrid(\'list_view\',-1)');
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
			                <th width="120">Service Source</th>
			                <th width="140">Service Company</th>
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
			                    echo create_drop_down( "cbo_company_mst", 130, "select id,company_name from lib_company comp where status_active=1 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", $company_id, "load_drop_down( 'fso_fabric_service_work_order_controller', this.value, 'load_drop_down_buyer_wo', 'buyer_wo_td' );",1);
			                    ?>
			                </td>
			                <td id="buyer_wo_td"  align="center">
			                    <?
			                    echo create_drop_down( "cbo_buyer_name", 110, "select id,buyer_name from lib_buyer where  status_active =1 and is_deleted=0 $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select Buyer --",$buyer_id,"","" );
			                   
			                    ?>
			                    <input type="hidden" name="within_group" id="within_group" value="<?php echo $within_group;?>">
			                </td>
			                <td>
			                	<?
								echo create_drop_down( "cbo_dyeing_source", 110, $knitting_source, "", 1, "-- Select --", 3, "load_drop_down( 'requires/fso_fabric_service_work_order_controller',this.value+'**'+$('#cbo_company_name').val(),'load_drop_down_knitting_com','dyeing_company_td' );",1,"1,3" );

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

	$search_field_cond = '';
	$search_string=$data[5];
	if ($data[5] != "") {
		if ($data[4] == 1) {
			$search_field_cond = " and LOWER(c.job_no) like LOWER('%" . $search_string . "%')";
		}
		else if($data[4] == 2)
		{
			$search_field_cond = " and LOWER(c.sales_booking_no) like LOWER('%" . $search_string . "%')";
		}
		else if($data[4] == 3)
		{
			$search_field_cond = " and LOWER(c.style_ref_no) like LOWER('%" . $search_string . "%')";
		
		}
		else if($data[4]==4)
		{
			$search_field_cond = " and LOWER(a.do_number_prefix_num) like LOWER('%" . $search_string . "%')";	
		}
		else if($data[4]==6)
		{
			$search_field_cond = " and LOWER(e.grouping) = LOWER('".$search_string."')";
		}
		else{
			$search_field_cond = " and LOWER(b.issue_no) like LOWER('%" . $search_string . "%')";
		}
	}
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) 
	{
		$buyer=" and ((a.within_group=1 and c.po_buyer=$data[1]) or (a.within_group=2 and c.buyer_id=$data[1]))"; 
	}

	if ($data[8]!=0) $within_group_con=" and a.within_group='$data[8]'"; else $within_group_con="";//{ echo "Please Select Buyer 

	if($db_type==0)
	{
		if ($data[2]!="" &&  $data[3]!="") $issue_date  = "and a.wo_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $issue_date ="";
	}

	if($db_type==2)
	{
		if ($data[2]!="" &&  $data[3]!="") $issue_date  = "and a.wo_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $issue_date ="";
	}

	if($db_type==0)
	{
		$search_field_cond .=" and YEAR(a.insert_date)=".$data[9];	
	}
	else if($db_type==2)
	{
		$search_field_cond .=" and TO_CHAR(a.insert_date,'YYYY')=".$data[9];	
	}
	
	if($data[6]!=0) $dyeing_source=" and a.dyeing_source='$data[6]'"; else $dyeing_source="";
	if($data[7]!=0) $dyeing_compnay_id=" and a.dyeing_compnay_id='$data[7]'"; else $dyeing_compnay_id="";
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$dyeing_comp=return_library_array( "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name",'id','supplier_name');

	$supllier_arr = return_library_array("select id,supplier_name from lib_supplier where  status_active =1 and is_deleted=0 and party_type='20' order by supplier_name", 'id', 'supplier_name');

	$sql="SELECT a.id,a.do_no,a.company_id,a.buyer_id,a.currency_id,a.exchange_rate,a.pay_mode,a.wo_date,a.delivery_date,a.attention,a.remark,a.dyeing_source,a.dyeing_compnay_id,a.approved,a.wo_basis,a.process_id,a.import_source,a.discount,a.upcharge,a.tenor, a.ready_approval, b.id as dtls_id, b.issue_qnty as issue_qnty,b.wo_qty as wo_qty, e.grouping
	from dyeing_work_order_mst a , dyeing_work_order_dtls b, fabric_sales_order_mst c left join wo_booking_dtls d on c.sales_booking_no = d.booking_no and c.within_group=1 and d.status_active=1 left join wo_po_break_down e on d.po_break_down_id=e.id
	where a.id=b.mst_id and b.fso_id=c.id and a.entry_form=696 and a.status_active=1 and b.status_active=1  $search_field_cond  $company $buyer $issue_date $dyeing_compnay_id $within_group_con
	order by a.do_no";
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
		$wo_data_arr[$row[csf("id")]]['process_id']=$row[csf("process_id")];
		$wo_data_arr[$row[csf("id")]]['import_source']=$row[csf("import_source")];
		$wo_data_arr[$row[csf("id")]]['discount']=$row[csf("discount")];
		$wo_data_arr[$row[csf("id")]]['upcharge']=$row[csf("upcharge")];
		$wo_data_arr[$row[csf("id")]]['tenor']=$row[csf("tenor")];
		$wo_data_arr[$row[csf("id")]]['ready_approval']=$row[csf("ready_approval")];

		if($dtls_id_arr[$row[csf("dtls_id")]]==""){
			$dtls_id_arr[$row[csf("dtls_id")]]=$row[csf("dtls_id")];
			$wo_data_arr[$row[csf("id")]]['issue_qnty']+=$row[csf("issue_qnty")];
			$wo_data_arr[$row[csf("id")]]['wo_qty']+=$row[csf("wo_qty")];
			$wo_data_arr[$row[csf("id")]]['grouping'] .=$row[csf("grouping")].',';
		}
	}

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
				<th width="100"> Internal Ref </th>
                <th width="60">Issue Qnty</th>
                <th>WO Qnty</th>
            </tr>
        </thead>
    </table>
    <table class="rpt_table" id="list_view" rules="all" width="980" cellspacing="0" cellpadding="0" border="0">
        <tbody>
			<?
			$i=0;
			foreach($wo_data_arr as $dtls_id=>$row )
			{
				$i++;
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";  
				
				$id=$row['id'];
				$dyeing_source=$knitting_source[$row['dyeing_source']];
				$data=$row['id']."**".$row['do_no']."**".$row['company_id']."**".$row['currency_id']."**".$row['exchange_rate']."**".$row['pay_mode']."**".change_date_format($row['wo_date'])."**".change_date_format($row['delivery_date'])."**".$row['dyeing_source']."**".$row['dyeing_compnay_id']."**".$row['attention']."**".$row['remark']."**".$row['within_group']."**".$row['approved']."**".$row['wo_basis']."**".$row['process_id']."**".$row['import_source']."**".$row['discount']."**".$row['upcharge']."**".$row['tenor']."**".$row['ready_approval'];
            ?>
	            <tr onClick="js_set_value('<? echo $data; ?>')" style="cursor:pointer" id="tr_<? echo $i; ?>" height="20" bgcolor="<? echo $bgcolor; ?>">
	                <td width="50"><? echo $i; ?></td>
	                
	                <td width="150"><p><? echo $comp[$row['company_id']]; ?></p></td>
	               
	                <td width="130" style="word-break:break-all"><? echo $row['do_no']; ?></td>
	                <td width="100" style="word-break:break-all"><? echo $wo_basis_arr[$row['wo_basis']]; ?></td>
	                <td width="170" style="word-break:break-all"><? echo $dyeing_source; ?></td>
	                <td width="150" style="word-break:break-all"><? echo $dyeing_comp[$row['dyeing_compnay_id']]; ?></td>
					<td width="100" ><? echo implode(",",array_unique(explode(",",chop($row['grouping'],',')))); ?></td>
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
	

	$sql="SELECT a.id,a.mst_id,a.issue_date,a.fabric_desc,a.machine_dia,a.body_part_id,a.machine_gg,a.stitch_length,a.color_range,a.color_id,a.shade,a.proccess_loss,a.process_name, a.issue_qnty,a.wo_qty,a.rate,a.amount,a.remark_text,a.issue_no,a.yarn,a.issue_id, a.fso_id, a.color_type, a.dia_type, c.job_no, c.sales_booking_no, c.style_ref_no, c.within_group, c.buyer_id, c.po_buyer, b.wo_basis, b.process_id 
	from dyeing_work_order_dtls a, dyeing_work_order_mst b, fabric_sales_order_mst c
	where a.MST_ID=b.id and a.fso_id=c.id and b.entry_form=696 and a.mst_id=$mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	//echo $sql;die;
	$result=sql_select($sql);
	$data="";

	$dtls_id_string='';
	foreach ($result as $row) {
		$dtls_id_string.=$row[csf('id')].",";

		if($row[csf('wo_basis')]==1){
			$issueid_arr[$row[csf('issue_id')]]=$row[csf('issue_id')];
		}
		else
		{
			$fsoid_arr[$row[csf('fso_id')]]=$row[csf('fso_id')];
		}

		$process_id=$row[csf('process_id')];
	}

	if(!empty($issueid_arr))
	{
		$issueids=implode(",",$issueid_arr);
	    $all_issueid_cond="";$issCond="";
	    $all_issueid_cond_2="";$issCond_2="";
		if($db_type==2 && count($issueid_arr)>999)
		{
			$issueid_arr_chunk=array_chunk($issueid_arr,999) ;
			foreach($issueid_arr_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$issCond.="  a.issue_id in($chunk_arr_value) or ";
				$issCond_2.="  a.id in($chunk_arr_value) or ";
			}

			$all_issueid_cond.=" and (".chop($issCond,'or ').")";
			$all_issueid_cond_2.=" and (".chop($issCond_2,'or ').")";
		}
		else
		{
			$all_issueid_cond=" and a.issue_id in($issueids)";
			$all_issueid_cond_2=" and a.id in($issueids)";
		}


		$iss_sql= "SELECT a.id as issue_id, a.issue_number, a.issue_date, b.body_part_id, c.detarmination_id, c.gsm, c.dia_width, b.color_id, e.job_no, e.buyer_id, e.within_group, e.id as fso_id, e.po_buyer, e.style_ref_no, sum(d.quantity) as issue_qnty
		from inv_issue_master a, inv_grey_fabric_issue_dtls b, order_wise_pro_details d, product_details_master c, fabric_sales_order_mst e
		where a.id=b.mst_id and b.id=d.dtls_id and b.prod_id=c.id and d.po_breakdown_id=e.id and a.status_active=1 and d.status_active=1 and d.status_active=1 and d.is_sales=1 and a.entry_form in (16,61) and d.entry_form in (16,61) and d.is_sales=1 $all_issueid_cond_2
		group by a.id,a.issue_number, a.issue_date, b.body_part_id, c.detarmination_id, c.gsm, c.dia_width, b.color_id, e.job_no, e.buyer_id, e.within_group, e.id, e.po_buyer, e.style_ref_no";

		$iss_result=sql_select($iss_sql);

		foreach ($iss_result as $row ) 
		{
			$issue_basis_qty_arr[$row[csf('issue_id')]."__".$row[csf('body_part_id')]."__".$row[csf('detarmination_id')]."__".$row[csf('color_id')]."__".$row[csf('gsm')]."__".$row[csf('dia_width')].'__'.$row[csf("fso_id")]]['issue_qty'] +=$row[csf('issue_qnty')];
		}
		unset($iss_result);

	}
	else if(!empty($fsoid_arr))
	{
		$fsoids=implode(",",$fsoid_arr);
	    $all_fsoid_cond="";$fsoCond="";
	    $all_fsoid_cond_2="";$fsoCond_2="";
		if($db_type==2 && count($fsoid_arr)>999)
		{
			$fsoid_arr_chunk=array_chunk($fsoid_arr,999) ;
			foreach($fsoid_arr_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$fsoCond.="  a.fso_id in($chunk_arr_value) or ";
				$fsoCond_2.="  a.id in($chunk_arr_value) or ";
			}

			$all_fsoid_cond.=" and (".chop($fsoCond,'or ').")";
			$all_fsoid_cond_2.=" and (".chop($fsoCond_2,'or ').")";
		}
		else
		{
			$all_fsoid_cond=" and a.fso_id in($fsoids)";
			$all_fsoid_cond_2=" and a.id in($fsoids)";
		}

		$fso_sql="SELECT a.id as fso_id, a.job_no, a.style_ref_no, a.sales_booking_no, a.within_group, a.po_buyer, a.buyer_id, b.body_part_id, b.color_type_id,b.determination_id, b.fabric_desc,b.gsm_weight, b.dia, b.width_dia_type, b.color_id, b.color_range_id, sum(b.finish_qty) as finish_qty
		from fabric_sales_order_mst a, fabric_sales_order_dtls b
		where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 $all_fsoid_cond_2
		group by a.id, a.job_no, a.style_ref_no, a.sales_booking_no, a.within_group, a.po_buyer, a.buyer_id, b.body_part_id, b.color_type_id,b.determination_id, b.fabric_desc,b.gsm_weight, b.dia, b.width_dia_type, b.color_id, b.color_range_id";
		$fso_result=sql_select($fso_sql);

		foreach ($fso_result as $row ) 
		{
			$fso_basis_qty_arr[$row[csf('color_range_id')]."__".$row[csf('color_type_id')]."__".$row[csf('width_dia_type')]."__".$row[csf('body_part_id')]."__".$row[csf('determination_id')]."__".$row[csf('color_id')]."__".$row[csf('gsm_weight')]."__".$row[csf('dia')].'__'.$row[csf("fso_id")]]['fso_qty'] +=$row[csf('finish_qty')];
		}
		unset($fso_result);
	}
	
	if(!empty($issueid_arr) || !empty($fsoid_arr))
	{
		$prewosql = sql_select("SELECT a.id,a.mst_id,a.issue_date,a.fabric_desc,a.machine_dia,a.body_part_id,a.machine_gg,a.stitch_length,a.color_range,a.color_id,a.shade,a.proccess_loss,a.process_name, a.issue_qnty,a.wo_qty,a.rate,a.amount,a.remark_text,a.issue_no,a.yarn,a.issue_id, a.fso_id, a.color_type, a.dia_type, c.job_no, c.sales_booking_no, c.style_ref_no, c.within_group, c.buyer_id, c.po_buyer, b.wo_basis 
		from dyeing_work_order_dtls a, dyeing_work_order_mst b, fabric_sales_order_mst c
		where a.MST_ID=b.id and a.fso_id=c.id and b.entry_form=696 and a.mst_id !=$mst_id and b.process_id=$process_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $all_issueid_cond $all_fsoid_cond");

		foreach ($prewosql as $row ) 
		{
			if($row[csf('wo_basis')]==1){
				$issue_basis_qty_arr[$row[csf('issue_id')]."__".$row[csf('body_part_id')]."__".$row[csf('fabric_desc')]."__".$row[csf('color_id')]."__".$row[csf('machine_gg')]."__".$row[csf('machine_dia')].'__'.$row[csf("fso_id")]]['wo_qty'] +=$row[csf('wo_qty')];
			}
			else
			{
				$fso_basis_qty_arr[$row[csf('color_range')]."__".$row[csf('color_type')]."__".$row[csf('dia_type')]."__".$row[csf('body_part_id')]."__".$row[csf('fabric_desc')]."__".$row[csf('color_id')]."__".$row[csf('machine_gg')]."__".$row[csf('machine_dia')].'__'.$row[csf("fso_id")]]['wo_qty'] +=$row[csf('wo_qty')];
			}
		}
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

		if($row[csf('within_group')]==1)
		{
			$buyer_id =$row[csf('po_buyer')];
		}
		else{
			$buyer_id =$row[csf('buyer_id')];
		}

		$buyer_name = $buyer_arr[$buyer_id];

		if($data!="") $data.="**";

		if($row[csf('wo_basis')]==1)
		{
			$cummu_wo = $issue_basis_qty_arr[$row[csf('issue_id')]."__".$row[csf('body_part_id')]."__".$row[csf('fabric_desc')]."__".$row[csf('color_id')]."__".$row[csf('machine_gg')]."__".$row[csf('machine_dia')].'__'.$row[csf("fso_id")]]['wo_qty'];

			$issue_quantity = $issue_basis_qty_arr[$row[csf('issue_id')]."__".$row[csf('body_part_id')]."__".$row[csf('fabric_desc')]."__".$row[csf('color_id')]."__".$row[csf('machine_gg')]."__".$row[csf('machine_dia')].'__'.$row[csf("fso_id")]]['issue_qty'];

			$data.=change_date_format($row[csf('issue_date')])."__".$row[csf('issue_no')]."__".$row[csf('issue_id')]."__".$row[csf('body_part_id')]."__".$body_part[$row[csf('body_part_id')]]."__".$cons_comp."__".$row[csf('fabric_desc')]."__".$row[csf('color_id')]."__".$color."__".$row[csf('machine_gg')]."__".$row[csf('machine_dia')].'__'.$row[csf("style_ref_no")].'__'.$buyer_name.'__'.$buyer_id.'__'.$row[csf("issue_qnty")].'__'.$row[csf("fso_id")]."&&&&".$row[csf('id')]."__".$row[csf('rate')]."__".$row[csf('amount')]."__".$row[csf('remark_text')]."__".$row[csf('proccess_loss')]."__".$row[csf('process_name')]."__".$process."__".$row[csf('color_range')]."__".$dtls_id."__".$cummu_wo."__".$issue_quantity;
		}
		else
		{
			$cummu_wo = $fso_basis_qty_arr[$row[csf('color_range')]."__".$row[csf('color_type')]."__".$row[csf('dia_type')]."__".$row[csf('body_part_id')]."__".$row[csf('fabric_desc')]."__".$row[csf('color_id')]."__".$row[csf('machine_gg')]."__".$row[csf('machine_dia')].'__'.$row[csf("fso_id")]]['wo_qty'];

			$fso_quantity = $fso_basis_qty_arr[$row[csf('color_range')]."__".$row[csf('color_type')]."__".$row[csf('dia_type')]."__".$row[csf('body_part_id')]."__".$row[csf('fabric_desc')]."__".$row[csf('color_id')]."__".$row[csf('machine_gg')]."__".$row[csf('machine_dia')].'__'.$row[csf("fso_id")]]['fso_qty'];

			$data.=$row[csf('job_no')]."__".$row[csf('sales_booking_no')]."__".$row[csf('issue_id')]."__".$row[csf('body_part_id')]."__".$body_part[$row[csf('body_part_id')]]."__".$cons_comp."__".$row[csf('fabric_desc')]."__".$row[csf('color_id')]."__".$color."__".$row[csf('machine_gg')]."__".$row[csf('machine_dia')].'__'.$row[csf("style_ref_no")].'__'.$buyer_name.'__'.$buyer_id.'__'.$fso_quantity.'__'.$row[csf("fso_id")]."&&&&".$row[csf('id')]."__".$row[csf('rate')]."__".$row[csf('amount')]."__".$row[csf('remark_text')]."__".$row[csf('proccess_loss')]."__".$row[csf('process_name')]."__".$process."__".$row[csf('color_range')]."__".$dtls_id."__".$cummu_wo."__".$row[csf("wo_qty")].'__'.$color_range[$row[csf("color_range")]].'__'.$row[csf("color_type")].'__'.$color_type[$row[csf("color_type")]].'__'.$row[csf("dia_type")].'__'.$fabric_typee[$row[csf("dia_type")]];
		}
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
				if(id!='') id+='***';
				id += selected_id[i];
			}

			$('#selected_fso_no').val(id);
			
		}

		function change_buyer_title(within_group)
		{
			if(within_group==1)
			{
				$('#buyer_title').text('PO Buyer Name');
			}
			else
			{
				$('#buyer_title').text('Buyer Name');
			}
		}

		function fnc_show()
		{
			if (form_validation('cbo_company_mst','Company Name')==false)
			{
				return;
			}

			if($("#txt_search_common").val().trim() =="")
			{
				if (form_validation('cbo_company_mst*txt_date_from*txt_date_to','Company Name*From Date*To Date')==false)
				{
					return;
				}
			}

			show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('within_group').value+'_'+document.getElementById('cbo_booking_type').value+'_'+document.getElementById('hdn_process_id').value+'_'+document.getElementById('cbo_year_selection').value, 'fso_search_list_view', 'search_div', 'fso_fabric_service_work_order_controller', 'setFilterGrid(\'list_view\',-1)');
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
							<th>Within Group</th>
			                <th id='buyer_title'>PO Buyer Name</th>
							<th>Booking Type</th>
			                <th>Search By</th>
			                <th id="search_by_td_up" width="170">Please Enter FSO No</th>
			                <th colspan="2">FSO Date</th>
			                <th>&nbsp;</th>
			            </tr>
			        </thead>
			        <tbody>
			            <tr class="general">
			                <td align="center"> 
			                	<input type="hidden" id="selected_fso_no" name="selected_fso_no">
			                    <?
			                    echo create_drop_down( "cbo_company_mst", 122, "select id,company_name from lib_company comp where status_active=1 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", $company_id, "load_drop_down( 'fso_fabric_service_work_order_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );",1);
			                    ?>
			                </td>
							<td>
								<?
									echo create_drop_down("within_group", 122, $yes_no, "", 0, "--  --", 0, "change_buyer_title(this.value)");
								?>
							</td>
			                <td id="buyer_td" align="center">
			                  <?
			                  		$buyer_cond='';
				                    if($buyer_id)
									{
										$buyer_cond=" and id=$buyer_id ";
									}
									echo create_drop_down( "cbo_buyer_name", 122, "select id,buyer_name from lib_buyer where  status_active =1 and is_deleted=0 $buyer_cond  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --",$buyer_id,"",$buyer_on );
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
								$search_by_arr = array(1 => "FSO No", 2 => "Fabric Booking No",3=>"Style Ref",4=>"Internal Ref No");
								$dd = "change_search_event(this.value, '0*0*0*0', '0*0*0*0', '../../../') ";
								echo create_drop_down("cbo_search_by", 110, $search_by_arr, "", 0, "", "", $dd, 0);
								?>
                                
							</th>
			                <td align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common"/>
							</td>
							
			                <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From" /></td>
			                <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To" /></td>
			                <td align="center">
			                	<input type="button" name="button2" class="formbutton" value="Show" onClick="fnc_show();" style="width:100px;" />
								<input type="hidden" id='hdn_process_id' name="hdn_process_id" value="<? echo $process_id;?>" />
							</td>
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

	$cbo_booking_type=$data[7];
	$process_id=$data[8];

	$search_field_cond = '';
	$search_string=$data[5];
	$issue_cond='';
	if ($data[5] != "") {
		if ($data[4] == 1) 
		{
			$search_field_cond = " and LOWER(a.job_no_prefix_num) = LOWER('" . $search_string . "')";
			if($db_type==0)
			{
				$search_field_cond .=" and YEAR(a.insert_date)=".$data[9];	
			}
			else if($db_type==2)
			{
				$search_field_cond .=" and TO_CHAR(a.insert_date,'YYYY')=".$data[9];	
			} 
		}
		else if($data[4] == 2) {
			$search_field_cond = " and LOWER(a.sales_booking_no) like LOWER('%" . $search_string . "%')";
		}
		else if($data[4] == 4) {
			$search_field_cond = " and LOWER(d.grouping) = LOWER('".$search_string."')";

		}else{
			$search_field_cond = " and LOWER(a.style_ref_no) like LOWER('%" . $search_string . "%')";
		}
	}
	if ($data[0]!=0) $company_cond=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	//if ($data[1]!=0) $buyer=" and buyer_id='$data[1]'"; else $buyer="";

	

	if($data[1] !=0)
		if($data[6] ==1)
		{
			$buyer_condition=" and a.po_buyer='$data[1]'"; 
		}
		else{
			$buyer_condition=" and a.buyer_id='$data[1]'"; 
		}


	if ($data[6]!=0) $within_group_con=" and a.within_group='$data[6]'"; else $within_group_con="";
	
	$booking_date="";
	if($db_type==0)
	{
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}

	if($db_type==2)
	{
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	}

	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');

	//$booking_type_arr = array("118"=>"Main","108"=>"Partial","88"=>"Short","89"=>"Sample With Order","90"=>"Sample Without Order");
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

	$sql="SELECT b.id as dtls_id, a.id as fso_id, a.job_no, a.style_ref_no, a.sales_booking_no, a.within_group, a.po_buyer, a.buyer_id, b.body_part_id, b.color_type_id,b.determination_id, b.fabric_desc,b.gsm_weight, b.dia, b.width_dia_type, b.color_id, b.color_range_id, d.grouping , b.grey_qty as grey_qty
	from fabric_sales_order_mst a left join wo_booking_dtls c on a.sales_booking_no = c.booking_no and a.within_group=1 and c.status_active=1 left join wo_po_break_down d on c.po_break_down_id=d.id, fabric_sales_order_dtls b
	where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_field_cond $company_cond $buyer_condition $booking_date $within_group_con $booking_type_cond
	order by a.id, b.id";
	//echo $sql;//die;
	$result=sql_select($sql);

	$fsoDtlsDupliChk=array();
	foreach ($result as $row) 
	{
		$fso_string = $row[csf("fso_id")].'='.$row[csf("body_part_id")].'='.$row[csf("color_type_id")].'='.$row[csf("determination_id")].'='.$row[csf("gsm_weight")].'='.$row[csf("dia")].'='.$row[csf("width_dia_type")].'='.$row[csf("color_id")].'='.$row[csf("color_range_id")];
		$fso_data_arr[$fso_string]['fso_id']=$row[csf("fso_id")];
		$fso_data_arr[$fso_string]['body_part_id']=$row[csf("body_part_id")];
		$fso_data_arr[$fso_string]['color_type_id']=$row[csf("color_type_id")];
		$fso_data_arr[$fso_string]['determination_id']=$row[csf("determination_id")];
		$fso_data_arr[$fso_string]['gsm_weight']=$row[csf("gsm_weight")];
		$fso_data_arr[$fso_string]['dia']=$row[csf("dia")];
		$fso_data_arr[$fso_string]['width_dia_type']=$row[csf("width_dia_type")];
		$fso_data_arr[$fso_string]['color_id']=$row[csf("color_id")];
		$fso_data_arr[$fso_string]['color_range_id']=$row[csf("color_range_id")];
		$fso_data_arr[$fso_string]['grouping'] .=$row[csf("grouping")].',';
		$fso_data_arr[$fso_string]['fabric_desc']=$row[csf("fabric_desc")];
		$fso_data_arr[$fso_string]['job_no']=$row[csf("job_no")];
		$fso_data_arr[$fso_string]['style_ref_no']=$row[csf("style_ref_no")];
		$fso_data_arr[$fso_string]['sales_booking_no']=$row[csf("sales_booking_no")];
		$fso_data_arr[$fso_string]['within_group']=$row[csf("within_group")];
		$fso_data_arr[$fso_string]['po_buyer']=$row[csf("po_buyer")];
		$fso_data_arr[$fso_string]['buyer_id']=$row[csf("buyer_id")];

		if($fsoDtlsDupliChk[$row[csf("dtls_id")]]=="")
		{
			$fsoDtlsDupliChk[$row[csf("dtls_id")]]=$row[csf("dtls_id")];
			$fso_data_arr[$fso_string]['grey_qty']+=$row[csf("grey_qty")];
		}
	}

	if(!empty($result))
	{
		$sql_wo = sql_select("SELECT b.id dtls_id, a.id as fso_id, b.body_part_id, b.color_type, b.fabric_desc as determination_id, b.machine_gg, b.machine_dia, b.dia_type,b.color_id, b.color_range, b.wo_qty as wo_qnty from fabric_sales_order_mst a left join wo_booking_dtls e on a.sales_booking_no=e.booking_no and a.within_group=1 and e.status_active=1 left join wo_po_break_down d on e.po_break_down_id=d.id, dyeing_work_order_dtls b, dyeing_work_order_mst c where a.id=b.fso_id and b.mst_id=c.id and c.entry_form= 696 and c.process_id=$process_id and c.wo_basis=2 $search_field_cond $company_cond $buyer_condition $booking_date $within_group_con $booking_type_cond and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
		foreach ($sql_wo as $row) 
		{
			if($woDtlsDupliChk[$row[csf("dtls_id")]]=="")
			{
				$woDtlsDupliChk[$row[csf("dtls_id")]]=$row[csf("dtls_id")];
				$wo_qty_data[$row[csf("fso_id")]][$row[csf("body_part_id")]][$row[csf("color_type")]][$row[csf("determination_id")]][$row[csf("machine_gg")]][$row[csf("machine_dia")]][$row[csf("dia_type")]][$row[csf("color_id")]][$row[csf("color_range")]] +=$row[csf("wo_qnty")];
			}
		}
	}
	
	?>

	<div style="width:1320px; overflow-y:scroll; max-height:340px;" id="buyer_list_view" align="center">

		<table class="rpt_table" id="heading" rules="all" width="1300" cellspacing="0" cellpadding="0" border="0">
        <thead>
            <tr>
                <th width="35">SL No</th>
				<th width="120">FSO No</th>
				<th width="100">Internal Ref No</th>
                <th width="100">Body Part</th>
                <th width="100">Color Type</th>
                <th width="100">Fabrication</th>
                <th width="50">Fabric GSM</th>
                <th width="50">Fabric Dia</th>
                <th width="80">Dia Width</th>
                <th width="100">Fab. Color</th>
                <th width="100">Color Range</th>
                <th width="100">FSO Qty.</th>
                <th width="100">Cumu. WO Qty</th>
                <th >Balance Qty</th>
            </tr>
        </thead>
      </table>
	</div>
	  <div style="width:1320px; overflow-y:scroll; max-height:340px;" id="buyer_list_view" align="center">
		<table class="rpt_table" id="list_view" rules="all" width="1300" cellspacing="0" cellpadding="0" border="0">
			<tbody>
				<?
				$i=0;
				foreach ($fso_data_arr as $fsoStr => $row) 
				{
					$balance_qnty=0;
					$wo_qnty =  $wo_qty_data[$row["fso_id"]][$row["body_part_id"]][$row["color_type_id"]][$row["determination_id"]][$row["gsm_weight"]][$row["dia"]][$row["width_dia_type"]][$row["color_id"]][$row["color_range_id"]];

					$balance_qnty = $row["grey_qty"]-$wo_qnty;

					if($balance_qnty > 0)
					{
						$i++;
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
						$buyer='';
						if($row['within_group']==1){
							$buyer_id=$row["po_buyer"];
						}else{
							$buyer_id=$row["buyer_id"];
						}
						$buyer_name=$buyer_arr[$buyer_id];

						$data = $row['job_no'].'__'.$row['sales_booking_no'].'__'.$row['issue_id'].'__'.$row['body_part_id'].'__'.$body_part[$row["body_part_id"]].'__'.$row['fabric_desc'].'__'.$row['determination_id'].'__'.$row['color_id'].'__'.$color_arr[$row['color_id']].'__'.$row['gsm_weight'].'__'.$row['dia'].'__'.$row["style_ref_no"].'__'.$buyer_name.'__'.$buyer_id.'__'.$row["grey_qty"].'__'.$row["fso_id"].'__'.$row["color_type_id"].'__'.$color_type[$row["color_type_id"]].'__'.$row["width_dia_type"].'__'.$fabric_typee[$row["width_dia_type"]].'__'.$row["color_range_id"].'__'.$color_range[$row["color_range_id"]].'__'.number_format($wo_qnty,2,'.','');


						$grouping = implode(",",array_unique(explode(",",chop($row['grouping'],','))));
					?>
					<tr onClick="js_set_value('<? echo $i; ?>')" style="cursor:pointer" id="tr_<? echo $i; ?>" height="20" bgcolor="<? echo $bgcolor; ?>">

						<td  width="35"><? echo $i; ?>
							<input type="hidden" name="hidden_data" id="hidden_data_id_<?php echo $i ?>" value="<? echo $data; ?>"/>
						</td>
						
						<td width="120"> <p><? echo $row['job_no']; ?></p></td>
						<td width="100"> <p><? echo $grouping; ?></p></td>
						<td width="100"> <p><? echo $body_part[$row['body_part_id']]; ?></p></td>
						<td width="100"> <p><? echo $color_type[$row['color_type_id']]; ?></p></td>
						<td width="100"> <p><? echo $row['fabric_desc']; ?></p></td>
						<td width="50"> <p><? echo $row['gsm_weight']; ?></p></td>
						<td width="50"> <p><? echo $row['dia']; ?></p></td>
						<td width="80"> <p><? echo $fabric_typee[$row['width_dia_type']]; ?></p></td>
						<td width="100"><p><? echo $color_arr[$row["color_id"]]; ?></p></td>
						<td width="100"  style="word-break:break-all"><? echo $color_range[$row["color_range_id"]]; ?></p>
						</td>
						<td width="100" style="word-break:break-all"><p><?php echo number_format($row["grey_qty"],2,'.',''); ?></p></td>
						<td width="100"><p><? echo number_format($wo_qnty,2,'.',''); ?></p></td>
						<td ><p><? echo number_format($balance_qnty,2,'.','');?></p></td>
					
					</tr>
					<?
					}
				}
				?>
			</tbody>
		</table>
	</div>

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
 
	<?
	exit();
}

if ($action=="issue_no_pop")
{
  	echo load_html_head_contents("Dyeing Work Order","../../../", 1, 1, $unicode);

  	extract($_REQUEST);
	?>
    <script>

		var selected_id = new Array; var final_selected_id=new Array;
		
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

		/* function js_set_value(str) {

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
		} */

		function js_set_value(str) {

			toggle(document.getElementById('tr_' + str), '#FFFFCC');
			if (jQuery.inArray($('#hiddenIssueId_' + str).val(), selected_id) == -1) {
				selected_id.push($('#hiddenIssueId_' + str).val());
			}
			else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == $('#hiddenIssueId_' + str).val()) break;
				}
				selected_id.splice(i, 1);
			}
		}

		function fnc_close()
		{
			final_selected_id=[];
			for (var i = 0; i < selected_id.length; i++) {
				var splited_sl_iss = selected_id[i].split('__');
				if(jQuery.inArray(splited_sl_iss[1], final_selected_id) == -1)
				{
					final_selected_id.push(splited_sl_iss[1]);
				}
			}
			var id = '';
			var name = '';
			for (var i = 0; i < final_selected_id.length; i++) {
				if(id!='') id+=',';
				id += final_selected_id[i];
			}

			$('#selected_work_order').val(id);
			parent.emailwindow.hide();
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
							<th>Search by</th>
							<th id="search_by_td_up" width="170">Please Enter FSO No</th>
			                <th>Issue No</th>
			                <th colspan="2">Issue Date Range</th>
			                <th>&nbsp;</th>
			            </tr>
			        </thead>
			        <tbody>
			            <tr class="general">
			                <td align="center">
								<input type="hidden" id="selected_work_order" >
			                    <?
			                    echo create_drop_down( "cbo_company_mst", 172, "select id,company_name from lib_company comp where status_active=1 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", $company_id, "",1);
			                    ?>
			                </td>
			               
							<th align="center">
								<?
								$search_by_arr = array(1 => "FSO No", 2 => "Fabric Booking No",3=>"Style Ref");
								$dd = "change_search_event(this.value, '0*0*0', '0*0*0', '../../../') ";
								echo create_drop_down("cbo_search_by", 110, $search_by_arr, "", 0, "", "", $dd, 0);
								?>
                                
							</th>

							<td align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
								id="txt_search_common"/>
							</td>

			                
			                <td align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes" name="txt_issue_no" id="txt_issue_no"/>
							</td>
							 
			                <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From" /></td>
			                <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To" /></td>
			                <td align="center">
			                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_issue_no').value+'_'+document.getElementById('cbo_year_selection').value, 'data_search_list_view', 'search_div', 'fso_fabric_service_work_order_controller', 'setFilterGrid(\'list_view\',-1)') " style="width:100px;" /></td>
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

if ($action=="data_search_list_view") 
{
	$data=explode('_',$data);

	if ($data[0]!=0) $company_condition=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	$issue_cond='';
	if($data[5]!="")
	{
		$issue_cond= " and a.issue_number_prefix_num=".$data[5]."";
	}

	$search_field_cond = '';
	$search_string=trim($data[4]);

	if ($search_string != "") {
		if ($data[3] == 1) 
		{
			$search_field_cond = " and LOWER(e.job_no_prefix_num) = LOWER('" . $search_string . "')";
			if($db_type==0)
			{
				$search_field_cond .=" and YEAR(e.insert_date)=".$data[6];	
			}
			else if($db_type==2)
			{
				$search_field_cond .=" and TO_CHAR(e.insert_date,'YYYY')=".$data[6];	
			} 
		} else if($data[3] == 2) {
			$search_field_cond = " and LOWER(e.sales_booking_no) like LOWER('%" . $search_string . "%')";
		}else{
			$search_field_cond = " and LOWER(e.style_ref_no) like LOWER('%" . $search_string . "%')";
		}
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


	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and b.status_active=1 and a.status_active=1";
	$deter_array=sql_select($sql_deter);
	foreach( $deter_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}

	$sql= "SELECT a.id as issue_id, a.issue_number, a.issue_date, b.body_part_id, c.detarmination_id, c.gsm, c.dia_width, b.color_id, e.job_no, e.buyer_id, e.within_group, e.id as fso_id, e.po_buyer, e.style_ref_no, sum(d.quantity) as issue_qnty, f.mst_id as saved_wo
	from inv_issue_master a left join dyeing_work_order_dtls f on a.issue_number=f.issue_no, inv_grey_fabric_issue_dtls b, order_wise_pro_details d, product_details_master c, fabric_sales_order_mst e
	where a.id=b.mst_id and b.id=d.dtls_id and b.prod_id=c.id and d.po_breakdown_id=e.id and a.status_active=1 and d.status_active=1 and d.status_active=1 and d.is_sales=1 and a.entry_form in (16,61) and d.entry_form in (16,61) and d.is_sales=1 $company_condition $issue_cond $issue_date $search_field_cond
	group by a.id,a.issue_number, a.issue_date, b.body_part_id, c.detarmination_id, c.gsm, c.dia_width, b.color_id, e.job_no, e.buyer_id, e.within_group, e.id, e.po_buyer, e.style_ref_no, f.mst_id order by a.id desc";


	$result = sql_select($sql);
	?>
	<script type="text/javascript">
    	
    	$(document).ready(function(e) {
			
			set_all();
		});
    </script>
	<table class="rpt_table"  rules="all" width="1275" cellspacing="0" cellpadding="0" border="0">
        <thead>
            <tr>
				<th width="35">SL No</th>
				<th width="120">FSO No</th>
				<th width="100">Issue Date</th>
				<th width="120">Issue No</th>
                <th width="100">Body Part</th>
                <th width="200">Fabrication</th>
                <th width="50">Fabric GSM</th>
                <th width="50">Fabric Dia</th>
                <th width="120">Fab. Color</th>
                <th width="100">Issue Qty.</th>
                <th width="80">Buyer</th>
            </tr>
        </thead>
    </table>
	<div style="width:1285px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
	<table id="list_view" class="rpt_table"  rules="all" width="1275" cellspacing="0" cellpadding="0" border="0">
        <tbody>
			<?
			$i=1;
			
			foreach($result as $row )
			{
				if($row[csf('saved_wo')]=="")
				{
					$barcode_string=$row[csf('barcode_no')];
					
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

					$buyer='';
					if($row[csf("within_group")]==1)
					{
						$buyer_name=$buyer_arr[$row[csf("po_buyer")]];
						$buyer_id=$row[csf("po_buyer")];
					}else{
						$buyer_name=$buyer_arr[$row[csf("buyer_id")]];
						$buyer_id=$row[csf("buyer_id")];
					}

					$colorArr =explode(",",$row[csf("color_id")]);
					$color_names = ""; 
					foreach ($colorArr as $color) {
						$color_names.= $color_arr[$color].',';
					}

					//$data =$row[csf('issue_id')];
					$data = $row[csf('issue_date')].'__'.$row[csf('issue_number')].'__'.$row[csf('issue_id')].'__'.$row[csf('body_part_id')].'__'.$body_part[$row[csf("body_part_id")]].'__'.$constructtion_arr[$row[csf('detarmination_id')]].', '.$composition_arr[$row[csf('detarmination_id')]].'__'.$row[csf('detarmination_id')].'__'.$row[csf('color_id')].'__'.chop($color_names,',').'__'.$row[csf('gsm')].'__'.$row[csf('dia_width')].'__'.$row[csf("style_ref_no")].'__'.$buyer_name.'__'.$buyer_id.'__'.$row[csf("issue_qnty")].'__'.$row[csf("fso_id")];
					?>
					<tr onClick="js_set_value('<? echo $i; ?>')" style="cursor:pointer" id="tr_<? echo $i; ?>" height="20" bgcolor="<? echo $bgcolor; ?>">
						<td width="35"><? echo $i; ?>
							<input type="hidden" name="hidden_data" id="hidden_data_id_<?php echo $i ?>" value="<? echo $data; ?>"/>
							<input type="hidden" name="hiddenIssueId" id="hiddenIssueId_<?php echo $i ?>" value="<? echo $i.'__'.$row[csf('issue_id')]; ?>"/>
						</td>
						<td  width="120" style="word-break:break-all"><? echo $row[csf("job_no")]; ?></td>
						<td width="100"><p><? echo change_date_format($row[csf('issue_date')]); ?></td>
						<td  width="120" ><p><? echo $row[csf('issue_number')]; ?></p></td>
						<td  width="100" style="word-break:break-all"><? echo $body_part[$row[csf("body_part_id")]]; ?></td>
						<td  width="200" style="word-break:break-all"><? echo $constructtion_arr[$row[csf('detarmination_id')]].', '.$composition_arr[$row[csf('detarmination_id')]]; ?></td>
						<td  width="50" style="word-break:break-all"><? echo $row[csf("gsm")]; ?></td>
						<td  width="50" style="word-break:break-all"><? echo $row[csf("dia_width")]; ?></td>
						<td  width="120" style="word-break:break-all"><? echo chop($color_names,','); ?></td>
						<td width="100" align="right"><p><? echo number_format($row[csf('issue_qnty')],2); ?></p></td>
						<td width="80" ><p><? echo $buyer_name; ?></p>
					</tr>
            		<?
       		 	}
				$i++;
			}
			?>
        </tbody>
    </table>
		</div>
    <table width="800" cellspacing="0" cellpadding="0" style="border:none" align="center" id="ds">
		<tr>
			<td align="center" height="30" valign="bottom">
				<div style="width:100%">
					<div style="width:50%; float:left" align="left">
						<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()"/> Check /
						Uncheck All
					</div>
					<div style="width:50%; float:left" align="left">
						<input type="button" name="close" onClick="fnc_close();" class="formbutton"
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
	$issue_ids=$data;

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');

	$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0","id","color_name");

	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and b.status_active=1 and a.status_active=1";
	$deter_array=sql_select($sql_deter);
	foreach( $deter_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}


	$sql= "SELECT a.id as issue_id, a.issue_number, a.issue_date, b.body_part_id, c.detarmination_id, c.gsm, c.dia_width, b.color_id, e.job_no, e.buyer_id, e.within_group, e.id as fso_id, e.po_buyer, e.style_ref_no, sum(d.quantity) as issue_qnty
	from inv_issue_master a, inv_grey_fabric_issue_dtls b, order_wise_pro_details d, product_details_master c, fabric_sales_order_mst e
	where a.id=b.mst_id and b.id=d.dtls_id and b.prod_id=c.id and d.po_breakdown_id=e.id and a.status_active=1 and d.status_active=1 and d.status_active=1 and d.is_sales=1 and a.entry_form in (16,61) and d.entry_form in (16,61) and d.is_sales=1 and a.id in ($issue_ids)
	group by a.id,a.issue_number, a.issue_date, b.body_part_id, c.detarmination_id, c.gsm, c.dia_width, b.color_id, e.job_no, e.buyer_id, e.within_group, e.id, e.po_buyer, e.style_ref_no order by a.id desc";

	$result = sql_select($sql);
	$string="";
	foreach($result as $row )
	{
		$buyer='';
		if($row[csf("within_group")]==1)
		{
			$buyer_name=$buyer_arr[$row[csf("po_buyer")]];
			$buyer_id=$row[csf("po_buyer")];
		}else{
			$buyer_name=$buyer_arr[$row[csf("buyer_id")]];
			$buyer_id=$row[csf("buyer_id")];
		}

		$colorArr =explode(",",$row[csf("color_id")]);
		$color_names = ""; 
		foreach ($colorArr as $color) {
			$color_names.= $color_arr[$color].',';
		}


		$string .= $row[csf('issue_date')].'__'.$row[csf('issue_number')].'__'.$row[csf('issue_id')].'__'.$row[csf('body_part_id')].'__'.$body_part[$row[csf("body_part_id")]].'__'.$constructtion_arr[$row[csf('detarmination_id')]].', '.$composition_arr[$row[csf('detarmination_id')]].'__'.$row[csf('detarmination_id')].'__'.$row[csf('color_id')].'__'.chop($color_names,',').'__'.$row[csf('gsm')].'__'.$row[csf('dia_width')].'__'.$row[csf("style_ref_no")].'__'.$buyer_name.'__'.$buyer_id.'__'.$row[csf("issue_qnty")].'__'.$row[csf("fso_id")]."***";

	}

	$string=chop($string,"***");
	echo $string;
	die;





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

if($action=="load_color_range_id_val")
{
	echo json_encode($color_range);
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

		//$new_wo_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'FSWO', date("Y",time()), 5, "select id,do_number_prefix,do_number_prefix_num from  dyeing_work_order_mst where status_active=1 and is_deleted=0 and company_id=$cbo_company_name  $year_cond=".date('Y',time())." order by id desc ", "do_number_prefix", "do_number_prefix_num" ));

		$new_wo_number = explode("*", return_next_id_by_sequence("DYEING_WORK_ORDER_MST_SEQ", "dyeing_work_order_mst",$con,1,$cbo_company_name,'FSWO',696,date("Y",time()),13 ));

		$field_array = "id,do_number_prefix,do_number_prefix_num,do_no,company_id,entry_form,currency_id,ready_approval,exchange_rate,pay_mode,wo_date,delivery_date,process_id,import_source,tenor,dyeing_source,dyeing_compnay_id,attention,remark,wo_basis,discount,upcharge,inserted_by,insert_date";
		
		$data_array = "(" . $id . ",'" . $new_wo_number[1] . "'," . $new_wo_number[2] . ",'" . $new_wo_number[0] . "'," . $cbo_company_name . ",696," . $cbo_currency . ",". $cbo_ready_approval . "," . $txt_exchange_rate . "," . $cbo_pay_mode . "," . $txt_wo_date . "," . $txt_delivery_date . "," . $cbo_proccess_name .",".$cbo_import_source.",".$txt_tenor.",".$cbo_dyeing_source.",".$cbo_dyeing_comp .  "," . $txt_attention . "," . $txt_remark . "," . $cbo_wo_basis. ",". $txt_discount_qty. ",". $txt_upcharge_qty . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";


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
			$fsoid="fsoid_".$j;
			
			
			$woqnty="woqnty_".$j;
			$rate="rate_".$j;
			$amount="amount_".$j;
			$remark="remark_".$j;
			$processloss="processloss_".$j;


			if(str_replace("'", "", $cbo_wo_basis)==1)
			{
				//$stitchlength="stitchlength_".$j;
				//$count="count_".$j;
				
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
				$data_array_dtls .= "(" . $dtls_id . "," . $id . ",'" . $$issueno . "','" . $issuedate . "','" . $$fabricdescription . "','" . $$dia . "','" . $$bodypart. "','" . $$gms . "','" . $$colorrange .  "','".$$dayingcolor .  "','".$$processloss. "','" . $$proccessid. "','" .  $$issueqty. "','" . $$rate. "','" . $$amount."','".$$remark."','".$$woqnty."','".$$issueid."','".$$fsoid."','" . $_SESSION['logic_erp']['user_id'] . "','" . $pc_date_time . "',0)";
			}
			else
			{
				$colortype="colortype_".$j;
				$diatype="diatype_".$j;

				if ($data_array_dtls != "") $data_array_dtls .= ",";
				$data_array_dtls .= "(" . $dtls_id . "," . $id . ",'" . $$fabricdescription . "','" . $$dia . "','" . $$bodypart. "','" . $$gms .  "','". $$colorrange .  "','".$$dayingcolor. "','" . $$processloss . "','" . $$proccessid. "','" . $$rate. "','" . $$amount."','".$$remark."','".$$woqnty."','".$$fsoid."','".$$colortype."','".$$diatype."','" . $_SESSION['logic_erp']['user_id'] . "','" . $pc_date_time . "',0)";
			}
		}
		
		if(str_replace("'", "", $cbo_wo_basis)==1)
		{
			$dtls_field_array = "id,mst_id,issue_no,issue_date,fabric_desc,machine_dia,body_part_id,machine_gg,color_range,color_id,proccess_loss,process_name,issue_qnty,rate,amount,remark_text,wo_qty,issue_id,fso_id,inserted_by,insert_date,is_deleted";
		}
		else
		{
			$dtls_field_array = "id,mst_id,fabric_desc,machine_dia,body_part_id,machine_gg,color_range,color_id,proccess_loss,process_name,rate,amount,remark_text,wo_qty,fso_id,color_type,dia_type,inserted_by,insert_date,is_deleted";
		}

		//echo "10**insert into dyeing_work_order_mst (".$field_array.") values ".$data_array;oci_rollback($con);disconnect($con);die;
		//echo "10**insert into dyeing_work_order_dtls (".$dtls_field_array.") values ".$data_array_dtls;oci_rollback($con);disconnect($con);die;
		
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
			$master_field_array = "company_id*currency_id*ready_approval*exchange_rate*pay_mode*wo_date*delivery_date*process_id*import_source*tenor*discount*upcharge*dyeing_source*dyeing_compnay_id*attention*remark*updated_by*update_date";

			$master_data_array = "". $cbo_company_name . "*" . $cbo_currency ."*" . $cbo_ready_approval . "*" . $txt_exchange_rate . "*" . $cbo_pay_mode . "*" . $txt_wo_date . "*" . $txt_delivery_date . "*" . $cbo_proccess_name . "*" . $cbo_import_source ."*".$txt_tenor."*".$txt_discount_qty."*".$txt_upcharge_qty."*".$cbo_dyeing_source."*".$cbo_dyeing_comp .  "*" . $txt_attention . "*" . $txt_remark . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

		
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
			$fsoid="fsoid_".$j;

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
					$data_array_dtls .= "(" . $dtls_id . "," . $update_id . ",'" . $$issueno . "','" . $issuedate . "','" . $$fabricdescription . "','" . $$dia . "','" . $$bodypart. "','" . $$gms . "','" . $$colorrange .  "','".$$dayingcolor .  "','".$$processloss. "','" . $$proccessid. "','" . $$issueqty. "','" . $$rate. "','" . $$amount."','".$$remark."','".$$woqnty."','".$$issueid."','".$$fsoid."','" . $_SESSION['logic_erp']['user_id'] . "','" . $pc_date_time . "',0)";
				}
				else
				{
					$field_update_array = "color_range*color_id*proccess_loss*process_name*issue_qnty*rate*amount*remark_text*wo_qty*issue_id*updated_by*update_date";
					$data_update_status="'".$$colorrange .  "'*'".$$dayingcolor .  "'*'".$$processloss. "'*'" . $$proccessid. "'*'" . $$issueqty. "'*'" . $$rate. "'*'" . $$amount."'*'".$$remark."'*'".$$woqnty."'*'".$$issueid."'*'".$_SESSION['logic_erp']['user_id']."'*'". $pc_date_time ."'";
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

				$colortype="colortype_".$j;
				$diatype="diatype_".$j;
	
				if(str_replace("'","",$$detailsId)*1 ==0 )
				{
					$dtls_id = return_next_id_by_sequence("dyeing_work_order_dtls_seq", "dyeing_work_order_dtls", $con);
					if ($data_array_dtls != "") $data_array_dtls .= ",";
					$data_array_dtls .= "(" . $dtls_id . "," . $update_id . ",'" . $$fabricdescription . "','" . $$dia . "','" . $$bodypart. "','" . $$gms . "','" . $$colorrange .  "','".$$dayingcolor. "','" . $$processloss . "','" . $$proccessid. "','" . $$rate. "','" . $$amount."','".$$remark."','".$$woqnty."','" . $$fsoid."','". $$colortype ."','". $$diatype."','" . $_SESSION['logic_erp']['user_id'] . "','" . $pc_date_time . "',0)";

				}
				else
				{
					$field_update_array = "process_name*rate*amount*remark_text*wo_qty*updated_by*update_date";
					$data_update_status="'". $$proccessid. "'*'" . $$rate. "'*'" . $$amount."'*'".$$remark."'*'".$$woqnty."'*'".$_SESSION['logic_erp']['user_id']."'*'". $pc_date_time ."'";
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
			$dtls_field_array = "id,mst_id,issue_no,issue_date,fabric_desc,machine_dia,body_part_id,machine_gg,color_range,color_id,proccess_loss,process_name,issue_qnty,rate,amount,remark_text,wo_qty,issue_id,fso_id,inserted_by,insert_date,is_deleted";
		}
		else
		{
			$dtls_field_array = "id,mst_id,fabric_desc,machine_dia,body_part_id,machine_gg,color_range,color_id,proccess_loss,process_name,rate,amount,remark_text,wo_qty,fso_id,color_type,dia_type,inserted_by,insert_date,is_deleted";
		}

		//echo "10**insert into dyeing_work_order_mst (".$field_array.") values ".$data_array;
		$rID3=true;
		if($data_array_dtls!="")
		{
		 	//echo "10**insert into dyeing_work_order_dtls($dtls_field_array)values".$data_array_dtls;oci_rollback($con);die;
		 	$rID3=sql_insert("dyeing_work_order_dtls",$dtls_field_array,$data_array_dtls,0);
		}

		$txt_dyeing_wo_order_no=str_replace("'", "", $txt_dyeing_wo_order_no);
		

		//echo "10**$rID && $rID1 && $rID2 && $rID3";oci_rollback($con);die;

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

		$delete_check_sql="select a.bill_no, b.wo_dtls_id from wo_bill_mst a, wo_bill_dtls b, dyeing_work_order_mst c,dyeing_work_order_dtls d where a.id=b.mst_id and c.id=d.mst_id and b.wo_dtls_id =d.id and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and b.entry_form=422 and c.id=$update_id";

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

	$dataArray = sql_select("SELECT DISTINCT  a.id,a.inserted_by,a.do_no,a.do_number_prefix,a.do_number_prefix_num,a.company_id,a.buyer_id,a.within_group,a.currency_id,a.exchange_rate,a.booking_no,a.fabric_sales_order_no,a.po_breakdown_id,a.style_ref_no,a.pay_mode,a.wo_date,a.delivery_date,a.booking_month,a.attention,a.remark,a.dyeing_source,a.dyeing_compnay_id,a.approved,a.process_id,a.wo_basis , c.job_no
	from dyeing_work_order_mst a, dyeing_work_order_dtls b, fabric_sales_order_mst c where a.id=b.MST_ID and b.fso_id=c.id and a.entry_form=696 and a.status_active=1 and a.id=$data[0]");

	// var_dump($dataArray);
	// die;
	$fso_job_no="";
	foreach($dataArray as $row){
		$fso_job_no= "," . $row[csf("job_no")];
	}
	$fso_job_no = ltrim($fso_job_no,",");

	$result_details=sql_select("select * from dyeing_work_order_dtls where status_active=1 and is_deleted=0 and mst_id=$data[0]");

	foreach ($result_details as $row) 
	{
		$all_fso_arr[$row[csf("fso_id")]]=$row[csf("fso_id")];
	}

	$fso_int_ref_sql=sql_select("SELECT a.id, c.grouping from fabric_sales_order_mst a, wo_booking_dtls b, wo_po_break_down c where a.sales_booking_no=b.booking_no and a.within_group=1 and b.po_break_down_id=c.id and b.status_active=1 and a.status_active=1 and a.id in (".implode(',',$all_fso_arr).") group by a.id, c.grouping");
	foreach ($fso_int_ref_sql as $val) 
	{
		$fso_int_ref_arr[$val[csf("id")]].=$val[csf("grouping")].',';
	}
	unset($fso_int_ref_sql);
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
							<td align="center" style="font-size:20px"><u ><span style="padding: 3px;"><b><? echo $conversion_cost_head_array[$dataArray[0][csf('process_id')]];?> Work Order</b><span></u></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>

		<?
		$cur='';
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
				<td><?php echo $fso_job_no; ?></td>
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
			<? 
				if($dataArray[0][csf('wo_basis')]==1)
				{
					echo '<th width="100">Issue No</th>';
				}
				else
				{
					echo '<th width="100">Internal Ref.</th>';
				}
			?>
			
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

									echo "Buyer - ".$buyer." , Style Ref. No - ".$dataArray[0][csf('style_ref_no')]." , Fab. Booking No - ".$dataArray[0][csf('booking_no')] .", FSO No - ". $dataArray[0][csf('job_no')].", Process Loss %-".$row[csf('proccess_loss')].", Process Name -".$process; 
									?>
								</td>
							</tr>
						<?
							$rate=0;
							$amount=0;
							$wo_qnty=0;

						}else{?>
							<tr bgcolor="#ddd">
								<td colspan="<?php echo $data[2]==1 ? 15:13;?>" align="left">
									<?php echo "Buyer - ".$buyer." , Style Ref. No - ".$dataArray[0][csf('style_ref_no')]." , Fab. Booking No - ".$dataArray[0][csf('booking_no')] .", FSO No - ".$dataArray[0][csf('job_no')].", Process Loss %-".$row[csf('proccess_loss')].", Process Name -".$process; ?>
								</td>
							</tr>
						<?}
					}
					$i++;
					if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td align="center"><? echo $i; ?></td>
						<td>
							<? 
							if($dataArray[0][csf('wo_basis')]==1)
							{
								echo $row[csf('issue_no')];
							}
							else {
								echo chop($fso_int_ref_arr[$row[csf("fso_id")]],',');
							}
							
							?>
						</td>
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

	<table  width='70%' class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
	<thead>
		<tr style="border:1px solid black;">
			<th width="3%" style="border:1px solid black;">Sl</th><th width="97%" style="border:1px solid black;">Terms and Condition</th>
		</tr>
	</thead>
	<tbody>
	<?
	$data_array = sql_select("select id, terms,terms_prefix from  wo_booking_terms_condition where booking_no='" . $dataArray[0][csf('do_no')] . "' and entry_form=696 order by id");
	if (count($data_array) > 0) {
		$i = 0;
		foreach ($data_array as $row) {
			$i++;
			?>
			<tr id="settr_1" align="" style="border:1px solid black;">
				<td style="border:1px solid black;"><? echo $i ?></td>
				<td style="border:1px solid black;"><? echo $row[csf('terms')]?></td>
			</tr>
			<?
		}
	}
	?>
	</tbody>
	</table>
	<br>
	<br>

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

   
   echo get_app_signature(327, $data[1], "1400 px",$template_id, 50,$inserted_by,$userDtlsArr); 
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
				<td width="80" class="wordwrapbreak"><? echo $row['fso_qnty'];?></td>
				<td width="80" class="wordwrapbreak">
					<input type="text" class="text_boxes_numeric" style="width: 70px;" placeholder="display" value="<? echo $pre_wo_qnty;?>" disabled readonly/>
				</td>
				<td width="80" class="wordwrapbreak">
					<input type="text" class="text_boxes_numeric" style="width: 70px;" name="balancewoqnty[]" id='balancewoqnty_"<? echo $i; ?>"' placeholder="display" value="<? echo $balance_wo_qnty;?>" disabled readonly/>
				</td>
				<td width="80" class="wordwrapbreak"><input type="text" class="text_boxes_numeric" style="width: 70px;" name="woqnty[]" id="woqnty_<? echo $i; ?>" placeholder="write" value="<? echo $balance_wo_qnty;?>" onkeyup="calculate_amount(<? echo $i;?>)"/></td>
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
				<td width="80" class="wordwrapbreak"><? echo $fso_qnty;?></td>
				<td width="80" class="wordwrapbreak">
					<input type="text" class="text_boxes_numeric" style="width: 70px;" value="<? echo number_format($pre_wo_qnty,2,'.','');?>" placeholder="display" disabled readonly/>
				</td>
				<td width="80" class="wordwrapbreak">
					<input type="text" class="text_boxes_numeric" style="width: 70px;" name="balancewoqnty[]" id='balancewoqnty_"<? echo $i; ?>"' value="<? echo number_format($balance_wo_qnty,2,'.','');?>" placeholder="display" disabled readonly/>
				</td>
				<td width="80" class="wordwrapbreak"><input type="text" class="text_boxes_numeric" style="width: 70px;" name="woqnty[]" id="woqnty_<? echo $i; ?>" placeholder="write" onkeyup="calculate_amount(<? echo $i;?>)" value="<? echo $row[csf('wo_qty')];?>"/></td>
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