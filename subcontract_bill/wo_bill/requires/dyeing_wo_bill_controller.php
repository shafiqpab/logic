<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');

$permission = $_SESSION['page_permission'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

if ($action=="load_drop_down_supplier")
{
	$exdata=explode("_",$data);
	if($exdata[1]==5 || $exdata[1]==3){
	   echo create_drop_down( "cbo_supplier_id", 150, "select id, company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name",1, "--Select Company--", $exdata[0], "",1,"" );
	}
	else{
	   echo create_drop_down( "cbo_supplier_id", 150, "select c.supplier_name, c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$exdata[0]' and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name","id,supplier_name", 1, "--Select Supplier--",$selected,"",1);

	}
	exit();
}

if($action=="wo_popup")
{
	echo load_html_head_contents("Work Order Info.","../../../", 1, 1, $unicode); 
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array, selected_name = new Array();
		
		function check_all_data() {
			var tbl_row_count = document.getElementById('list_view').rows.length;
			//tbl_row_count = tbl_row_count - 1;

			for (var i = 1; i <= tbl_row_count; i++) {
				js_set_value(i);
			}
		}

		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			document.getElementById(x).style.backgroundColor = ( newColor == document.getElementById(x).style.backgroundColor )? origColor : newColor;
		}

		function js_set_value( str_data, tr_id)
		{
			//alert(str_data+'___'+tr_id); return;
			//$row[csf('id')]."**".$row[csf('do_no')]."**".$row[csf('pay_mode')]."**".$row[csf('dyeing_compnay_id')]."**".$row[csf('currency_id')];
			var str_all=str_data.split("**");
			var str_wo=str_all[1];
			var str_wo_id=str_all[0];
			
			var str_pay_mode=str_all[2];
			var str_supplier=str_all[3];
			var str_currency=str_all[4];
			var str=str_all[0];
			
			if ( document.getElementById('hidd_pay_mode').value!="" && document.getElementById('hidd_pay_mode').value!=str_all[2] )
			{
				alert('Pay Mode Mixing Not Allowed')
				return;
			}
			//toggle( tr_id, '#FFFFCC');
			document.getElementById('hidd_pay_mode').value=str_all[2];
			
			if ( document.getElementById('hidd_supplier').value!="" && document.getElementById('hidd_supplier').value!=str_all[3] )
			{
				alert('Supplier Mixing Not Allowed')
				return;
			}
			//toggle( tr_id, '#FFFFCC');
			document.getElementById('hidd_supplier').value=str_all[3];
			
			if ( document.getElementById('hidd_currency').value!="" && document.getElementById('hidd_currency').value!=str_all[4] )
			{
				alert('Currency Mixing Not Allowed')
				return;
			}
			toggle( tr_id, '#FFFFCC');
			document.getElementById('hidd_currency').value=str_all[4];

			if( jQuery.inArray( str , selected_id ) == -1 )
			{
				selected_id.push( str_wo_id );
				selected_name.push( str_wo );
			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == str ) break;
				}

				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
					//alert(selected_id.length)
				if(selected_id.length==0)
				{
					document.getElementById('hidd_pay_mode').value="";
					document.getElementById('hidd_supplier').value="";
					document.getElementById('hidd_currency').value="";
				}
			}
			var id = '' ; var name = '';
			for( var i = 0; i < selected_id.length; i++ )
			{
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			$('#hidd_wo_id').val( id );
			$('#hidd_wo_no').val( name );
		}
    </script>
	</head>

	<body>
		<div align="center" style="width:100%;" >
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table width="910" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
			        <thead>
			            <tr>
			                <th width="140" class="must_entry_caption">Company</th>
			                <th width="120">Buyer</th>
                            <th width="100">Source</th>
			                <th width="120">Supplier</th>
			                <th width="100">Search By</th>
			                <th id="search_by_td_up" width="100">Enter WO No</th>
			                <th width="130" colspan="2">W/O Date Range</th>
			                <th>&nbsp;</th>
			            </tr>
			        </thead>
			        <tbody>
			            <tr class="general">
			                <td align="center">
                            	<input type="hidden" id="hidd_wo_id">
                            	<input type="hidden" id="hidd_wo_no">
                                <input type="hidden" id="hidd_pay_mode">
                                <input type="hidden" id="hidd_supplier">
                                <input type="hidden" id="hidd_currency">
			                    <?
			                    if($company_id!="" && $company_id!=0) $on=1; else $on=0;
								
			                    echo create_drop_down( "cbo_company_mst", 130, "select id,company_name from lib_company comp where status_active=1 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", $company_id, "load_drop_down( 'dyeing_wo_bill_controller', this.value, 'load_drop_down_buyer_wo', 'buyer_wo_td' );",$on); ?>
			                </td>
			                <td id="buyer_wo_td"  align="center">
			                    <?
			                    //if($buyer_id!="" && $buyer_id!=0) $buyer_on=1; else $buyer_on=0;
								
			                    echo create_drop_down( "cbo_buyer_name", 110, "select id,buyer_name from lib_buyer where  status_active =1 and is_deleted=0  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --",$buyer_id,"",$buyer_on ); ?>
			                </td>
			                <td><? echo create_drop_down( "cbo_dyeing_source", 100, $knitting_source, "", 1, "-- Select --", 3, "load_drop_down( 'requires/dyeing_wo_bill_controller',this.value+'**'+$('#cbo_company_name').val(),'load_drop_down_knitting_com','dyeing_company_td' );",1,"1,3" ); ?>
			                </td>
			                 <td id="dyeing_company_td"><? echo create_drop_down( "cbo_dyeing_comp", 130, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name",1, "-- Select Company --", $selected, "","","" ); ?></td>
			                <th>
								<?
								$search_by_arr = array(1 => "W/O No", 2 => "FSO No",3=>"F.Booking No",4=>"Style Ref");//,5=>"Batch No"
								$dd = "change_search_event(this.value, '0*0*0*0', '0*0*0*0', '../../../') ";
								echo create_drop_down("cbo_search_by", 110, $search_by_arr, "", 0, "", "", $dd, 0);
								?>
							</th>
			                <td id="search_by_td"><input type="text"  class="text_boxes" name="txt_search_common" id="txt_search_common" style="width: 90px;"/></td>
			                <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px;" placeholder="From" /></td>
			                <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width: 60px;" placeholder="To" /></td>
			                <td>
				                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_dyeing_source').value+'_'+document.getElementById('cbo_dyeing_comp').value, 'wo_search_list_view', 'search_div', 'dyeing_wo_bill_controller', 'setFilterGrid(\'list_view\',-1)');" style="width: 70px;" />
				            </td>
			            </tr>
			            <tr>
			                <th align="center" valign="middle" colspan="9"><? echo load_month_buttons(1); ?> </th>
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

if($action=="wo_search_list_view")
{
	$data=explode('_',$data);
	$search_field_cond = '';
	$search_string=$data[5];
	if ($data[5]!= "") {
		if($data[4] == 1) $search_field_cond = " and LOWER(a.do_number_prefix_num) like LOWER('%" . $search_string . "%')";
		else if($data[4] == 2) $search_field_cond = " and LOWER(a.fabric_sales_order_no) like LOWER('%" . $search_string . "%')";
		else if($data[4] == 3) $search_field_cond = " and LOWER(a.booking_no) like LOWER('%" . $search_string . "%')";
		else if($data[4] == 4) $search_field_cond = " and LOWER(a.style_ref_no) like LOWER('%" . $search_string . "%')";
		//else $search_field_cond = " and LOWER(b.issue_no) like LOWER('%" . $search_string . "%')"; //batch
	}
	
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.buyer_id='$data[1]'"; else $buyer="";//{ echo "Please Select Buyer 

	if($db_type==0)
	{
		if ($data[2]!="" &&  $data[3]!="") $issue_date  = "and a.wo_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $issue_date ="";
	}
	else if($db_type==2)
	{
		if ($data[2]!="" &&  $data[3]!="") $issue_date  = "and a.wo_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $issue_date ="";
	}
	
	if ($db_type == 0) {
		$details_arr = return_library_array("select mst_id, group_concat(id order by id desc) as dtls_id from knitting_work_order_dtls where  status_active=1 and is_deleted=0 group by mst_id", 'mst_id', 'id');
	} else if ($db_type == 2) {
		
		$details_arr = return_library_array("select mst_id, LISTAGG(id, ',') WITHIN GROUP (ORDER BY id desc) as id from knitting_work_order_dtls where  status_active=1 and is_deleted=0 group by mst_id", 'mst_id', 'id');
	} 

	if($data[6]!=0) $dyeing_source=" and a.dyeing_source='$data[6]'"; else $dyeing_source="";
	if($data[7]!=0) $dyeing_compnay_id=" and a.dyeing_compnay_id='$data[7]'"; else $dyeing_compnay_id="";
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supllier_arr=return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 order by supplier_name", 'id','supplier_name');
	$booking_arr = return_library_array("select id, booking_no from wo_booking_mst", 'id', 'booking_no');

	$sql="SELECT a.id, a.do_no, a.do_number_prefix_num, a.company_id, a.buyer_id,a.within_group, a.currency_id, a.exchange_rate, a.booking_no, a.fabric_sales_order_no, a.po_breakdown_id, a.style_ref_no, a.pay_mode, a.wo_date, a.delivery_date, a.booking_month, a.attention, a.remark, a.dyeing_source, a.dyeing_compnay_id, sum(b.issue_qnty) as issue_qnty, sum(b.wo_qty) as wo_qty
		from dyeing_work_order_mst a, dyeing_work_order_dtls b 
		where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and b.id not in(select wo_dtls_id from wo_bill_dtls where status_active=1 and is_deleted=0 and entry_form=422) $search_field_cond $company $buyer $issue_date $dyeing_compnay_id
		group by  a.id, a.do_no, a.do_number_prefix_num, a.company_id, a.buyer_id,a.within_group, a.currency_id, a.exchange_rate, a.booking_no, a.fabric_sales_order_no, a.po_breakdown_id, a.style_ref_no, a.pay_mode, a.wo_date, a.delivery_date, a.booking_month, a.attention, a.remark, a.dyeing_source, a.dyeing_compnay_id order by a.id DESC";
	//echo $sql;
	$result = sql_select($sql);
	?>
	<table class="rpt_table" id="rpt_tablelist_view" rules="all" width="920" cellspacing="0" cellpadding="0" border="0">
        <thead>
            <tr>
                <th width="30">SL No</th>
                <th width="100">Buyer</th>
                <th width="60">WO No</th>
                <th width="100">FSO No</th>
                <th width="100">F.Booking No</th>
                <th width="100">Style Ref.</th>
                <th width="100">Source</th>
                <th width="100">Dyeing Company</th>
                <th width="70">W/O Date</th>
                <th>WO Qty</th>
            </tr>
        </thead>
    </table>
    <div style="width:920px; max-height:270px;overflow-y:scroll;" >
        <table class="rpt_table" id="list_view" rules="all" width="900" cellspacing="0" cellpadding="0" border="0">
            <tbody>
                <?
                $i=0;
                foreach($result as $row )
                {
                    $i++;
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    
                    $data=$row[csf('id')]."**".$row[csf('do_no')]."**".$row[csf('pay_mode')]."**".$row[csf('dyeing_compnay_id')]."**".$row[csf('currency_id')];
                    
                    $dyComp="";
                    if($row[csf('pay_mode')]==5 || $row[csf('pay_mode')]==3) $dyComp=$comp[$row[csf('dyeing_compnay_id')]]; else $dyComp=$supllier_arr[$row[csf('dyeing_compnay_id')]];

                    $buyer='';
                    if($row[csf('within_group')]==1)
                    {
                    	$buyer=$comp[$row[csf('buyer_id')]];
                    }else{
                    	$buyer=$buyer_arr[$row[csf('buyer_id')]];
                    }
                ?>
                    <tr onClick="js_set_value('<?=$data; ?>',this.id);" style="cursor:pointer" id="tr_<?=$i; ?>" bgcolor="<?=$bgcolor; ?>">
                        <td width="30"><?=$i; ?></td>
                        <td width="100" style="word-break:break-all"><?=$buyer; ?></td>
                        <td width="60" style="word-break:break-all"><?=$row[csf('do_number_prefix_num')]; ?></td>
                        <td width="100" style="word-break:break-all"><?=$row[csf('fabric_sales_order_no')]; ?></td>
                        <td width="100" style="word-break:break-all"><?=$row[csf('booking_no')]; ?></td>
                        <td width="100" style="word-break:break-all"><?=$row[csf('style_ref_no')]; ?></td>
                        <td width="100" style="word-break:break-all"><?=$knitting_source[$row[csf('dyeing_source')]]; ?></td>
                        <td width="100" style="word-break:break-all"><?=$dyComp; ?></td>
                        <td width="70"><?=change_date_format($row[csf('wo_date')]); ?></td>
                        <td align="right"><? echo $row[csf('wo_qty')]; ?></td>
                    </tr>
                <?
                }
                ?>
            </tbody>
        </table>
    </div>
    <table class="rpt_table" rules="all" width="920" cellspacing="0" cellpadding="0" border="0">
    	<tr>
            <td align="center" ><input type="button" name="close" onClick="parent.emailwindow.hide();"  class="formbutton" value="Close" style="width:100px" /></td>
        </tr>
    </table>
	<?
	exit();
}

if($action=="bill_popup")
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
    </script>
	</head>
	<body>
		<div align="center" style="width:100%;" >
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table width="830" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
			        <thead>
			            <tr>
			                <th width="130" class="must_entry_caption">Company</th>
			                <th width="130">Supplier</th>
			                <th width="80">Bill No</th>
			                <th width="100">Search By</th>
			                <th width="110" id="search_by_td_up">Please Enter W/O No</th>
			                <th width="130" colspan="2">Bill Date Range</th>
			                <th>&nbsp;</th>
			            </tr>
			        </thead>
			        <tbody>
			            <tr class="general">
			                <td align="center"> <input type="hidden" id="selected_work_order">
			                    <?
			                    if($company_id!="" && $company_id!=0) $on=1; else $on=0;
								
			                    echo create_drop_down( "cbo_company_mst", 130, "select id,company_name from lib_company comp where status_active=1 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", $company_id, "load_drop_down( 'dyeing_wo_bill_controller', this.value, 'load_drop_down_supplier_wo', 'supplier_wo_td' );",$on);
			                    ?>
			                </td>
			                <td id="supplier_wo_td">
			                    <?
			                    if($supplier_id!="" && $supplier_id!=0) $supplier_on=1; else $supplier_on=0;
								
			                      echo create_drop_down( "cbo_supplier_id", 130, "select c.supplier_name, c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$company_id' and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name","id,supplier_name", 1, "--Select Supplier--",$selected,"",$supplier_on);
			                    ?>
			                </td>
                            <td><input type="text" class="text_boxes" name="txt_bill_no" id="txt_bill_no" style="width:70px" /></td>
			                <th>
								<?
								$search_by_arr = array(1 => "W/O No", 2 => "FSO No",3=>"F.Booking No",4=>"Style Ref");
								$dd = "change_search_event(this.value, '0*0*0*0', '0*0*0*0', '../../../') ";
								echo create_drop_down("cbo_search_by", 110, $search_by_arr, "", 0, "", "", $dd, 0);
								?>
							</th>
			                <td id="search_by_td"><input type="text" class="text_boxes" name="txt_search_common" id="txt_search_common" style="width:70px" /></td>
			                <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" placeholder="From" style="width:60px" /></td>
			                <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" placeholder="To" style="width:60px" /></td>
			                <td align="center">
				                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_supplier_id').value+'_'+document.getElementById('txt_bill_no').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'bill_search_list_view', 'search_div', 'dyeing_wo_bill_controller', 'setFilterGrid(\'list_view\',-1)') "  />
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

if($action=="bill_search_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $companyCond=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $supplierCond=" and a.supplier_id='$data[1]'"; else $supplierCond="";//{ echo "Please Select Buyer 
	if ($data[2]!=0) $billCond=" and a.prefix_no_num='$data[2]'"; else $billCond="";
	$search_field_cond = '';
	$search_string=trim($data[4]);
	if ($data[4] != "") {
		if ($data[3] == 2) {
			$search_field_cond = " and LOWER(c.fabric_sales_order_no) like LOWER('%" . $search_string . "%')";
		}
		else if($data[3] == 3)
		{
			$search_field_cond = " and LOWER(c.booking_no) like LOWER('%" . $search_string . "%')";
		}
		else if($data[3] == 4)
		{
			$search_field_cond = " and LOWER(c.style_ref_no) like LOWER('%" . $search_string . "%')";
		
		}
		else if($data[3]==1)
		{
			$search_field_cond = " and LOWER(a.wo_no)like LOWER('%" . $search_string . "%')";	
		}else{
			$search_field_cond = " and LOWER(b.issue_no) like LOWER('%" . $search_string . "%')";
		}
	}
	

	if($db_type==0)
	{
		if($data[5]!="" && $data[6]!="") $billDate= "and a.bill_date between '".change_date_format($data[5], "yyyy-mm-dd", "-")."' and '".change_date_format($data[6], "yyyy-mm-dd", "-")."'"; else $billDate ="";
	}
	else if($db_type==2)
	{
		if($data[5]!="" && $data[6]!="") $billDate= "and a.bill_date between '".change_date_format($data[5], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[6], "yyyy-mm-dd", "-",1)."'"; else $billDate ="";
	}
	
	$supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	
	if($db_type==0) $yearCond=" YEAR(a.insert_date)";	
	else if($db_type==2) $yearCond=" TO_CHAR(a.insert_date,'YYYY')";	

	
	 $sql="select a.id, TO_CHAR(a.insert_date,'YYYY') as year, a.bill_no, a.prefix_no_num, a.bill_date, a.pay_mode, a.supplier_id, a.wo_no, a.wo_id, a.manual_bill_no, a.tot_wo_qty, a.tot_bill_qty, a.tot_bill_amt, a.upchage, a.discount, a.remarks,a.is_posted_account 
	 from wo_bill_mst a, wo_bill_dtls b, dyeing_work_order_mst c , dyeing_work_order_dtls d 
	 where a.id=b.mst_id and b.wo_dtls_id=d.id and c.id=d.mst_id and b.entry_form=422 and  a.entry_form=422 and a.is_deleted=0 and a.status_active=1  $companyCond $supplierCond $billCond $billDate $search_field_cond group by a.id, TO_CHAR(a.insert_date,'YYYY') , a.bill_no, a.prefix_no_num, a.bill_date, a.pay_mode, a.supplier_id, a.wo_no, a.wo_id, a.manual_bill_no, a.tot_wo_qty, a.tot_bill_qty, a.tot_bill_amt, a.upchage, a.discount, a.remarks,a.is_posted_account order by a.id DESC ";
	//echo $sql;
	$result = sql_select($sql);
	
	?>
	<table class="rpt_table" rules="all" width="740" cellspacing="0" cellpadding="0" border="0">
        <thead>
            <tr>
                <th width="40">SL</th>
                <th width="70">Year</th>
                <th width="70">Bill No</th>
                <th width="80">Bill Date</th>
                <th width="110">Pay Mode</th>
                <th width="150">Supplier</th>
                <th width="90">Bill Qty.</th>
                <th>Bill Amount</th>
            </tr>
        </thead>
    </table>
    <div style="width:740px; max-height:270px;overflow-y:scroll;" >
        <table class="rpt_table" id="list_view" rules="all" width="720" cellspacing="0" cellpadding="0" border="0">
            <tbody>
                <?
                $i=0;
                foreach($result as $row )
                {
                    $i++;
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    
                    $datastr=$row[csf('id')]."**".$row[csf('bill_no')]."**".change_date_format($row[csf('bill_date')])."**".$row[csf('pay_mode')]."**".$row[csf('supplier_id')]."**".$row[csf('wo_no')]."**".$row[csf('wo_id')]."**".$row[csf('manual_bill_no')]."**".$row[csf('tot_wo_qty')]."**".$row[csf('tot_bill_qty')]."**".$row[csf('tot_bill_amt')]."**".$row[csf('upchage')]."**".$row[csf('discount')]."**".$row[csf('remarks')]."**".$row[csf('is_posted_account')];
                    
                	?>
                    <tr onClick="js_set_value('<?=$datastr; ?>');" style="cursor:pointer" id="tr_<?=$i; ?>" bgcolor="<?=$bgcolor; ?>">
                        <td width="40" align="center"><?=$i; ?></td>
                        <td width="70" align="center"><?=$row[csf('year')]; ?></td>
                        <td width="70" align="center"><?=$row[csf('prefix_no_num')]; ?></td>
                        <td width="80" align="center"><?=change_date_format($row[csf('bill_date')]); ?></td>
                        <td width="110" style="word-break:break-all"><?=$pay_mode[$row[csf('pay_mode')]]; ?></td>
                        <td width="150" style="word-break:break-all"><?=$supllier_arr[$row[csf('supplier_id')]]; ?></td>
                        <td width="90" align="right"><?=$row[csf('tot_bill_qty')]; ?></td>
                        <td align="right"><?=$row[csf('tot_bill_amt')]; ?></td>
                    </tr>
                <?
                }
                ?>
            </tbody>
    	</table>
    </div>
	<?
	exit();
}

if($action=="populate_details_data")
{
	$data=explode("__", $data);
	$womst_id=$data[0];
	$up_id=$data[1];
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$updataArr=array();
	$update_arr=array();
	if($up_id>0)
	{
		$sqlDtls="select id, wo_dtls_id, bill_qty, rate, amount, remarks from wo_bill_dtls where mst_id='$up_id' and status_active=1 and status_active=1 and entry_form=422";
		$sqlDtlsRes=sql_select($sqlDtls);
		
		foreach($sqlDtlsRes as $row)
		{
			array_push($update_arr, $row[csf('wo_dtls_id')]);
			$updataArr[$row[csf('wo_dtls_id')]]=$row[csf('id')].'__'.$row[csf('bill_qty')].'__'.$row[csf('rate')].'__'.$row[csf('amount')].'__'.$row[csf('remarks')];
		}
		unset($sqlDtlsRes);
	}
	
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
	if($up_id>0)
	{
		$sql="SELECT a.id, a.buyer_id,a.within_group, a.style_ref_no, a.booking_no, a.fabric_sales_order_no, a.do_no, a.currency_id, a.exchange_rate, a.pay_mode, b.id as dtls_id, b.body_part_id, b.fabric_desc, b.color_id, b.color_range, b.shade, b.proccess_loss, b.process_name, b.wo_qty, b.rate, b.amount, b.remark_text
		from dyeing_work_order_mst a, dyeing_work_order_dtls b 
		where a.id=b.mst_id and a.id in ($womst_id) and a.status_active=1 and b.status_active=1 
		order by a.id ASC";
	}else{
		$sql="SELECT a.id, a.buyer_id,a.within_group, a.style_ref_no, a.booking_no, a.fabric_sales_order_no, a.do_no, a.currency_id, a.exchange_rate, a.pay_mode, b.id as dtls_id, b.body_part_id, b.fabric_desc, b.color_id, b.color_range, b.shade, b.proccess_loss, b.process_name, b.wo_qty, b.rate, b.amount, b.remark_text
		from dyeing_work_order_mst a, dyeing_work_order_dtls b 
		where a.id=b.mst_id and a.id in ($womst_id) and a.status_active=1 and b.status_active=1 and b.id not in (select wo_dtls_id from wo_bill_dtls where status_active=1 and is_deleted=0 and entry_form=422)
		order by a.id ASC";
	}
	


	//echo $sql;die;
	$result=sql_select($sql);
	

	$strdata="";
	foreach ($result as $row)
	{
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
	    $buyer='';

	    if($row[csf('within_group')]==1){
	    	$buyer=$comp[$row[csf('buyer_id')]];
	    }else{
	    	$buyer=$buyer_arr[$row[csf('buyer_id')]];
	    }

	    $cons_comp=$constructtion_arr[$row[csf('fabric_desc')]].", ".$composition_arr[$row[csf('fabric_desc')]];
		
		if($updataArr[$row[csf('dtls_id')]]=="") $updataArr[$row[csf('dtls_id')]]='________';

		if($up_id>0){
			if(in_array($row[csf('dtls_id')], $update_arr))
			{
				if($strdata!="") $strdata.="**";
				$strdata.=$row[csf('id')]."__".$row[csf('buyer_id')]."__".$buyer."__".$row[csf('style_ref_no')]."__".$row[csf('booking_no')]."__".$row[csf('fabric_sales_order_no')]."__".$row[csf('do_no')]."__".$row[csf('exchange_rate')]."__".$row[csf('dtls_id')]."__".$body_part[$row[csf('body_part_id')]]."__".$cons_comp."__".$color."__".$color_range[$row[csf('color_range')]]."__".$row[csf('shade')]."__".$row[csf('proccess_loss')]."__".$process."__".$row[csf('wo_qty')]."__".$row[csf('rate')]."__".$row[csf('amount')]."__".$row[csf('remark_text')]."&&&&".$updataArr[$row[csf('dtls_id')]];
			}
		}
		else
		{
			
				if($strdata!="") $strdata.="**";
				$strdata.=$row[csf('id')]."__".$row[csf('buyer_id')]."__".$buyer."__".$row[csf('style_ref_no')]."__".$row[csf('booking_no')]."__".$row[csf('fabric_sales_order_no')]."__".$row[csf('do_no')]."__".$row[csf('exchange_rate')]."__".$row[csf('dtls_id')]."__".$body_part[$row[csf('body_part_id')]]."__".$cons_comp."__".$color."__".$color_range[$row[csf('color_range')]]."__".$row[csf('shade')]."__".$row[csf('proccess_loss')]."__".$process."__".$row[csf('wo_qty')]."__".$row[csf('rate')]."__".$row[csf('amount')]."__".$row[csf('remark_text')]."&&&&".$updataArr[$row[csf('dtls_id')]];
			
			
		}

		
	}
	echo $strdata;
	exit();
}


if ($action=="load_drop_down_supplier_wo")
{
	$exdata=explode("_",$data);
	if($exdata[1]==5 || $exdata[1]==3){
	   echo create_drop_down( "cbo_supplier_id", 130, "select id, company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name",1, "--Select Company--", $exdata[0], "",1,"" );
	}
	else{
	   echo create_drop_down( "cbo_supplier_id", 130, "select c.supplier_name, c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$exdata[0]' and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name","id,supplier_name", 1, "--Select Supplier--",$selected,"",0);
	}
	exit();
}

if($action=="save_update_delete")
{
	$process = array(&$_POST);
	// echo "10**";
	// print_r($process);die;
	extract(check_magic_quote_gpc( $process ));

	$update_id=str_replace("'", "", $update_id);

	/**
	|--------------------------------------------------------------------------
	| is_posted_account checking
	|--------------------------------------------------------------------------
	|
	*/
	if ($operation==1 || $operation==2) 
	{
		$wo_data=sql_select("select IS_POSTED_ACCOUNT from wo_bill_mst where id=$update_id");
		$is_posted_account=$wo_data[0]["IS_POSTED_ACCOUNT"]*1;
		
		if($is_posted_account>0)
		{
			echo "20**Update Restricted. Data already posted in Accounting.";
			oci_rollback($con);
			disconnect($con);
			die;
		}
	}

	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		if($db_type==0) $year_cond=" and YEAR(insert_date)";	
		else if($db_type==2) $year_cond=" and TO_CHAR(insert_date,'YYYY')";	

		$id = return_next_id_by_sequence("wo_bill_mst_seq", "wo_bill_mst", $con);

		$newbill_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'DWB', date("Y",time()), 5, "select id, prefix_no, prefix_no_num from wo_bill_mst where company_id=$cbo_company_name and status_active=1 and entry_form=422 $year_cond=".date('Y',time())." order by id desc ", "prefix_no", "prefix_no_num"));

		$field_array = "id, prefix_no, prefix_no_num, bill_no, company_id, bill_date, pay_mode, supplier_id, wo_no, wo_id, manual_bill_no, remarks, entry_form, tot_wo_qty, tot_bill_qty, tot_bill_amt, upchage, discount, inserted_by, insert_date, status_active, is_deleted";
		
		$data_array="(". $id . ",'" . $newbill_no[1] . "'," . $newbill_no[2] . ",'" . $newbill_no[0] . "'," . $cbo_company_name . "," . $txt_bill_date . "," . $hidd_pay_mode . "," . $cbo_supplier_id . "," . $txt_wo_no . "," . $hidd_wo_id . "," . $txt_manual_bill_no . "," . $txt_remark.",422,".$txt_woQty.",".$txt_billQty.",".$txt_woAmt.",".$txt_upCharge.",".$txt_discount .  "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',1,0)";
		
		$billNo=$newbill_no[0];
		$dtlsArrField = "id, mst_id, buyer_id, wo_dtls_id, wo_id, bill_qty, rate, amount, remarks, entry_form, inserted_by, insert_date, status_active, is_deleted";
		$dataArrDtls="";
		for ($j = 1; $j <= $tot_row; $j++) 
		{
			$detailsId="detailsId_".$j;
			$dtlsupId="dtlsupId_".$j;
			$buyerId="buyerId_".$j;
			$womstId="womstId_".$j;
			$billqty="billqty_".$j;
			$rate="rate_".$j;
			$amount="amount_".$j;
			$remark="remark_".$j;
			$dtls_id = return_next_id_by_sequence("wo_bill_dtls_seq", "wo_bill_dtls", $con);
			
			if ($dataArrDtls != "") $dataArrDtls .= ",";
			$dataArrDtls .= "(" . $dtls_id . "," . $id . ",'" . $$buyerId . "','" . $$detailsId . "','" . $$womstId . "','" . $$billqty. "','" . $$rate. "','" . $$amount."','".$$remark."',422,'" . $_SESSION['logic_erp']['user_id'] . "','" . $pc_date_time . "',1,0)";
		}

		//echo "10**insert into wo_bill_dtls (".$dtlsArrField.") values ".$dataArrDtls; die;
		//echo "insert into dyeing_work_order_dtls($dtls_field_array)values".$data_array_dtls;die;
		
		$flag=1;
		$rID=sql_insert("wo_bill_mst",$field_array,$data_array,0);
		if($rID==1) $flag=1; else $flag=0;
		$rID1=sql_insert("wo_bill_dtls",$dtlsArrField,$dataArrDtls,0);
		if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		
		//echo "10**".$rID.'--'.$rID1.'--'.$flag; die;

		if($db_type==0)
		{
			if($flag==1){
				mysql_query("COMMIT");
				echo "0**".$id."**".$billNo;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1){
				oci_commit($con);
				echo "0**".$id."**".$billNo;
			}
			else{
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
		$total_amount_update=str_replace("'", "", $total_amount_update);
		$total_bill_qnty_update=str_replace("'", "", $total_bill_qnty_update);
		$total_wo_qnty_update=str_replace("'", "", $total_wo_qnty_update);
	
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$field_array = "bill_date*pay_mode*supplier_id*wo_no*wo_id*manual_bill_no*remarks*tot_wo_qty*tot_bill_qty*tot_bill_amt*upchage*discount*updated_by*update_date";
		
		$data_array="" . $txt_bill_date . "*" . $hidd_pay_mode . "*" . $cbo_supplier_id . "*" . $txt_wo_no . "*" . $hidd_wo_id . "*" . $txt_manual_bill_no . "*" . $txt_remark."*".$total_wo_qnty_update."*".$total_bill_qnty_update."*".$total_amount_update."*".$txt_upCharge."*".$txt_discount . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

		$billNo=$txt_bill_no;
		
		$dataArrDtls=""; $dataArrDtlsUp="";
		
		
		$dtlsArrField = "id, mst_id, buyer_id, wo_dtls_id, wo_id, bill_qty, rate, amount, remarks, entry_form, inserted_by, insert_date, status_active, is_deleted";
		$dtlsArrFieldUp = "buyer_id*wo_dtls_id*wo_id*bill_qty*rate*amount*remarks*updated_by*update_date*status_active*is_deleted";
		$rID1=true;
		for ($j = 1; $j <= $tot_row; $j++) 
		{
			$detailsId="detailsId_".$j;
			$dtlsupId="dtlsupId_".$j;
			$buyerId="buyerId_".$j;
			$womstId="womstId_".$j;
			$billqty="billqty_".$j;
			$rate="rate_".$j;
			$amount="amount_".$j;
			$remark="remark_".$j;

			$checkid="checkid_".$j;
			//echo "10**".str_replace("'","",$$dtlsupId);die;

			if($$checkid==2)
			{
				if(str_replace("'","",$$dtlsupId)!="")
				{
					$id_arr[]=str_replace("'",'',$$dtlsupId);
					$dataArrDtlsUp[str_replace("'",'',$$dtlsupId)] =explode("*",("'".$$buyerId."'*'".$$detailsId."'*'".$$womstId."'*'".$$billqty."'*'".$$rate."'*'".$$amount."'*'".$$remark."'*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*0*1"));
				}
			}
			else
			{
				if(str_replace("'","",$$dtlsupId)!="")
				{
					$id_arr[]=str_replace("'",'',$$dtlsupId);
					$dataArrDtlsUp[str_replace("'",'',$$dtlsupId)] =explode("*",("'".$$buyerId."'*'".$$detailsId."'*'".$$womstId."'*'".$$billqty."'*'".$$rate."'*'".$$amount."'*'".$$remark."'*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*1*0"));
				}
				else
				{
					//echo "10**insert**error";die;
					$dtls_id = return_next_id_by_sequence("wo_bill_dtls_seq", "wo_bill_dtls", $con);
					if ($dataArrDtls != "") $dataArrDtls .= ",";
					$dataArrDtls .= "(" . $dtls_id . "," . $id . ",'" . $$buyerId . "','" . $detailsId . "','" . $$womstId . "','" . $$billqty. "','" . $$rate. "','" . $$amount."','".$$remark."',422,'" . $_SESSION['logic_erp']['user_id'] . "','" . $pc_date_time . "',1,0)";
				}
			}
			
			
		}
		
		
		
		$rID=sql_update("wo_bill_mst",$field_array,$data_array,"id","".$update_id."",0);
		
		//echo "10**".bulk_update_sql_statement("wo_bill_dtls", "id",$dtlsArrFieldUp, $dataArrDtlsUp, $id_arr ); die;
		$rID1=true;
		if($dataArrDtlsUp!="")
		{
			$rID1=execute_query(bulk_update_sql_statement("wo_bill_dtls", "id",$dtlsArrFieldUp,$dataArrDtlsUp,$id_arr ));
			
		}

		//echo "10**insert into dyeing_work_order_mst (".$field_array.") values ".$data_array;;
		//echo "insert into dyeing_work_order_dtls($dtls_field_array)values".$data_array_dtls;die;
		$rID3=true;
		if($dataArrDtls!=""){
			$rID3=sql_insert("wo_bill_dtls",$dtlsArrField,$dataArrDtls,0);
			
		}
		
		//echo "10**".$rID.'--'.$rID1.'--'.$rID3; die;
		
		if($db_type==0)
		{
			if($rID && $rID1 && $rID3){
				mysql_query("COMMIT");
				echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$billNo);
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$rID ."**". $rID1 ."**". $rID3;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID1 && $rID3){
				oci_commit($con);
				echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$billNo);
			}
			else{
				oci_rollback($con);
				echo "10**".$rID ."**". $rID1 ."**". $rID3;
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
		
		$flag=1;
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$rID=sql_delete("wo_bill_mst",$field_array,$data_array,"id","".$update_id."",0);
		if($rID==1) $flag=1; else $flag=0;

		
		$rID2=1;
		
		$deleted_field_array="updated_by*update_date*status_active*is_deleted";
		$deleted_data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$rID2=sql_delete("wo_bill_dtls",$deleted_field_array,$deleted_data_array,"mst_id","".$update_id."",0);
		if($rID2==1 && $flag==1) $flag=1; else $flag=0;
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
		else if($db_type==2 || $db_type==1 )
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

if ($action == "print_dyeing_bill") 
{
	echo load_html_head_contents("Dyeing W/O Bill", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$data = explode("**", $data);
	
	$company_library = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[1]'","image_location");
	$supplier_arr = return_library_array("select id,supplier_name from lib_supplier", 'id', 'supplier_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

	$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0","id","color_name");

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
                    	<td align="center" style="font-size:20px"><strong ><?=$company_library[$data[1]]; ?></strong></td>
                    </tr>
                    <tr class="form_caption">
                        <td align="center" style="font-size:14px"><?=show_company($data[1],'',''); ?></td>  
                    </tr>
                    <tr>
                    	<td align="center" style="font-size:18px"><strong><?=$data[2]; ?></strong></td>
                    </tr>
                </table>
            </td>
		</table>
		<?
		$sql_mst="select a.id, a.bill_no, a.prefix_no_num, a.bill_date, a.pay_mode, a.supplier_id, a.wo_no, a.wo_id, a.manual_bill_no, a.tot_wo_qty, a.tot_bill_qty, a.tot_bill_amt, a.upchage, a.discount, a.remarks from wo_bill_mst a where a.id='$data[0]' and a.entry_form=422 and a.is_deleted=0 and a.status_active=1";
		$dataArray=sql_select($sql_mst);
		
		$womst_id=$dataArray[0][csf('wo_id')];


		$sql="SELECT a.id, a.buyer_id,a.within_group, a.style_ref_no, a.booking_no, a.fabric_sales_order_no, a.do_no, a.currency_id, a.exchange_rate, a.pay_mode, b.id as dtls_id, b.body_part_id, b.fabric_desc, b.color_id, b.color_range, b.shade, b.proccess_loss, b.process_name, b.wo_qty, b.rate, b.amount, b.remark_text
				from dyeing_work_order_mst a, dyeing_work_order_dtls b 
				where a.id=b.mst_id and a.id in ($womst_id) and a.status_active=1 and b.status_active=1 order by a.id ASC";

			$result=sql_select($sql); 
			$currency_ids=array();
			$exchange_rates=array();
			foreach ($result as $row) {
				$currency_ids[]=$row[csf('currency_id')];
				$exchange_rates[]=$row[csf('exchange_rate')];
			}

			$exchange_rates=array_unique($exchange_rates);
			$currency_ids=array_unique($currency_ids);
		?>
		<table width="1400" cellspacing="0" align="" border="0">
            <tr>
                <td width="130"><strong>Bill No :</strong></td> <td width="175"><?=$dataArray[0][csf('bill_no')]; ?></td>
                <td width="130"><strong>Bill Date: </strong></td><td width="175px"><?=change_date_format($dataArray[0][csf('bill_date')]); ?></td>
                <td width="130"><strong>Manual Bill No :</strong></td> <td width="175"><?=$dataArray[0][csf('manual_bill_no')]; ?></td>
            </tr>
            <tr>
				<?
                    if($dataArray[0][csf('pay_mode')]==3 || $dataArray[0][csf('pay_mode')]==5)
                    {
						$party_name=$company_library[$dataArray[0][csf('supplier_id')]];
                    }
                    else
                    {
                        
                        $party_name=$supplier_arr[$dataArray[0][csf('supplier_id')]];
                    }

                    $address='';
                    $party_add=$dataArray[0][csf('supplier_id')];

                     $nameArray=sql_select( "select address_1 from lib_supplier where id=$party_add"); 
                   // echo "select address_1 from lib_supplier where id=$party_add";
                     
                    foreach ($nameArray as $row)
                    { 
                       
                       $address=$row[csf('address_1')];
                       break;
                    }

                ?>
                <td><strong>Supplier Name : </strong></td><td ><?=$party_name; ?></td>
                <td><strong> Address :</strong> </td>
                <td><?php echo $address; ?></td>
               
            </tr>
            <tr>
            	 <td><strong>Currency : </strong></td><td><? if(count($currency_ids)==1)
                	{
                		echo $currency[$currency_ids[0]];
                		$carrency_id=$currency_ids[0];
                	} ?>
                		
                </td>
                <td><strong>Conversion Rate: </strong></td><td><? 

                	if(count($exchange_rates)==1)
                	{
                		echo $exchange_rates[0];
                	}
                 ?>
                 	
                 </td>
                
            </tr>
            <tr><td><strong>Remarks : </strong></td><td colspan="3"><?=$dataArray[0][csf('remarks')]; ?></td></tr>
        </table>
        <table style="margin-top:10px;" width="1400" border="1" rules="all" cellpadding="3" cellspacing="0" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="80">Buyer</th>
                <th width="90">Style Ref.</th>
                <th width="100">F.Booking No</th>
                <th width="100">FSO No</th>
                <th width="100">WO No</th>
                <th width="80">Body Part</th>
                <th width="150">Fabric Description</th>
                <th width="80">Color Name</th>
                <th width="70">Color Range</th>
                <th width="50">Shade %</th>
                <th width="50">Process Loss %</th>
                <th width="120">Process Name</th>
                <th width="70">WO Qty.</th>
                <th width="70">Bill Qty.</th>
                <th width="60">Rate</th>
                <th>Amount</th>
            </thead>
            <tbody>
			<?
				$updataArr=array();
				$sqlDtls="select id, wo_dtls_id, bill_qty, rate, amount, remarks from wo_bill_dtls where mst_id='$data[0]' and status_active=1 and status_active=1";
				$sqlDtlsRes=sql_select($sqlDtls);
				
				foreach($sqlDtlsRes as $row)
				{
					$updataArr[$row[csf('wo_dtls_id')]]=$row[csf('id')].'__'.$row[csf('bill_qty')].'__'.$row[csf('rate')].'__'.$row[csf('amount')].'__'.$row[csf('remarks')];
				}
				unset($sqlDtlsRes); 
			
				
				//echo $sql;die;
				$i=1;

				foreach($result as $row)
				{
					$dtls_id=$updataArr[$row[csf('dtls_id')]];
					if($dtls_id!="")
					{
						$ex_dtls_data=explode("__",$dtls_id);
						
						$billQty=$rate=$amount=$remarks="";
						
						$billQty=$ex_dtls_data[1];
						$rate=$ex_dtls_data[2];
						$amount=$ex_dtls_data[3];
						$remarks=$ex_dtls_data[4];
						
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
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

						$buyer='';
						if($row[csf('within_group')]==1)
						{
							$buyer=$company_library[$row[csf('buyer_id')]];
						}
						else
						{
							$buyer=$buyer_arr[$row[csf('buyer_id')]];
						}
				
						$cons_comp=$constructtion_arr[$row[csf('fabric_desc')]].", ".$composition_arr[$row[csf('fabric_desc')]];
					
					?>
                        <tr bgcolor="<?=$bgcolor; ?>" > 
                            <td align="center"><?=$i; ?></td>
                            <td style="word-break:break-all"><?=$buyer; ?></td>
                            <td style="word-break:break-all"><?=$row[csf('style_ref_no')]; ?></td>
                            <td style="word-break:break-all"><?=$row[csf('booking_no')]; ?></td>
                            <td style="word-break:break-all"><?=$row[csf('fabric_sales_order_no')]; ?></td>
                            <td style="word-break:break-all"><?=$row[csf('do_no')]; ?></td>
                            <td style="word-break:break-all"><?=$body_part[$row[csf('body_part_id')]]; ?></td>
                            <td style="word-break:break-all"><?=$cons_comp; ?></td>
                            <td style="word-break:break-all"><?=$color; ?></td>
                            <td style="word-break:break-all"><?=$color_range[$row[csf('color_range')]]; ?></td>
                            <td align="center"><?=$row[csf('shade')]; ?></td>
                            <td align="center"><?=$row[csf('proccess_loss')]; ?></td>
                            <td style="word-break:break-all"><?=$process; ?></td>
                            <td align="right"><?=number_format($row[csf('wo_qty')],2); ?></td>
                            <td align="right"><?=number_format($billQty,2); ?></td>
                            <td align="right"><?=number_format($rate,2); ?></td>
                            <td align="right"><?=number_format($amount,2); ?></td>
                        </tr>
                        <?
						$i++;
						$totWoQty+=$row[csf('wo_qty')];
						$totBillQty+=$billQty;
						$totBillAmt+=$amount;
					}
				}
				?>
             </tbody>
             <tfoot>
				<tr > 
					<td align="right" colspan="13"><strong>Total:</strong></td>
					<td align="right"><?=number_format($totWoQty,2); ?>&nbsp;</td>
					<td align="right"><?=number_format($totBillQty,2); ?>&nbsp;</td>
					<td align="right">&nbsp;</td>
					<td align="right"><?=number_format($totBillAmt,2); ?></td>
				</tr>
                <tr > 
					<td align="right" colspan="16"><strong>Upcharge:</strong></td>
					<td align="right"><?=number_format($dataArray[0][csf('upchage')],2); ?></td>
				</tr>
                <tr > 
					<td align="right" colspan="16"><strong>Discount:</strong></td>
					<td align="right"><?=number_format($dataArray[0][csf('discount')],2); ?></td>
				</tr>
                <tr > 
					<td align="right" colspan="16"><strong>Grand Total:</strong></td>
                    <? $grandTot=($totBillAmt+$dataArray[0][csf('upchage')])-$dataArray[0][csf('discount')]; 
					
					$carrency_id=1;
					if($carrency_id==1){ $paysa_sent="Paisa"; } else if($carrency_id==2){ $paysa_sent="CENTS"; }
					$format_total_amount=number_format($grandTot,2,'.','');
					
					?>
					<td align="right"><?=number_format($grandTot,2); ?></td>
				</tr>
                
			   <tr>
				   <td colspan="16" align="left"><b>In Word: <? echo number_to_words($format_total_amount,$currency[$carrency_id],$paysa_sent); ?></b></td>
			   </tr>
            </tfoot>
        </table>
		<? echo signature_table(199, $data[1], "1400px"); ?>
    </div>
    <?
    exit();
}
?>