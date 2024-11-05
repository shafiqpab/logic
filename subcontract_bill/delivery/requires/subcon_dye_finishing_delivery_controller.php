<?
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//=======================DROP DOWN LOCATION==================
if ($action=="load_drop_down_location")
{
	//echo $data;
	echo create_drop_down( "cbo_location", 152, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "",0 );
}

if ($action=="load_drop_down_party_name")
{
    echo create_drop_down( "cbo_party_name", 152, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and b.tag_company='$data' and buy.id in (select buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $selected, "",'' );
	exit();
}

if ($action=="load_drop_down_party_popup")
{
    echo create_drop_down( "cbo_party_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and b.tag_company='$data' and buy.id in (select buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $selected, "",'' );
	exit();
}

if ($action == "collar_and_cuff_popup") 
{
	echo load_html_head_contents("Plies Info Roll Wise", "../../../", 1, 1, '', '1', '');
	extract($_REQUEST);
	?>
		<script>
			function add_break_down_tr(i) 
			{ 
				var row_num = $('#txt_tot_row').val();
				row_num++;

				var clone = $("#tr_" + i).clone();
				clone.attr({
					id: "tr_" + row_num,
				});

				clone.find("input,select").each(function() {

					$(this).attr({
						'id': function(_, id) {
							var id = id.split("_");
							return id[0] + "_" + row_num
						},
						'name': function(_, name) {
							return name
						},
						'value': function(_, value) {
							return ''
						}
					});

				}).end();

				$("#tr_" + i).after(clone);


				$('#increase_' + row_num).removeAttr("value").attr("value", "+");
				$('#decrease_' + row_num).removeAttr("value").attr("value", "-");
				$('#increase_' + row_num).removeAttr("onclick").attr("onclick", "add_break_down_tr(" + row_num + ");");
				$('#decrease_' + row_num).removeAttr("onclick").attr("onclick", "fn_deleteRow(" + row_num + ");");
				set_all_onclick();
				$('#txt_tot_row').val(row_num);
				
			}
			function fn_deleteRow(rowNo) 
			{
				var numRow = $('#tbl_list_search tbody tr').length;
				if (numRow != 1) {
					$("#tr_" + rowNo).remove();
				}
			} 
			function fnc_close() 
			{
				var save_string = ''; 
				var i = 1;
				$("#tbl_list_search tbody").find('tr').each(function() 
				{
					var bodyPart = $(this).find('input[name="bodyPart[]"]').val();
					var greySize = $(this).find('input[name="greySize[]"]').val();
					var finishSize = $(this).find('input[name="finishSize[]"]').val();
					var gmtsSize = $(this).find('input[name="gmtsSize[]"]').val();
					var qtyPices = $(this).find('input[name="qtyPices[]"]').val();
					var needlePerCm = $(this).find('input[name="needlePerCm[]"]').val(); 

					if (qtyPices * 1 > 0) {
						if (save_string == "") {
							save_string = bodyPart + "=" + greySize + "=" + finishSize + "=" + gmtsSize + "=" + qtyPices + "=" + needlePerCm;
						} else {
							save_string += "$$" + bodyPart + "=" + greySize + "=" + finishSize + "=" + gmtsSize + "=" + qtyPices + "=" + needlePerCm;
						}
					} 
					i++;
				});

				$('#hide_data').val(save_string); 
				parent.emailwindow.hide();
		}
		</script>
		</head> 
		<body>
			<div align="center" style="width:100%; overflow-y:hidden;">
				<fieldset style="width:590px"> 
					<table width="690" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list_search">
						<thead>
							<th>Body Part</th>
							<th>Grey Size</th>
							<th>Finish Size</th> 
							<th>Gmts Size</th>
							<th>Qty. Pcs</th>
							<th>Needle Per CM</th>
							<th> </th>
						</thead>
						<tbody>
							<? 
								if ($collarAndCuffStr != "") 
								{
									$collarAndCuffArr = explode('$$',$collarAndCuffStr); 
									foreach ($collarAndCuffArr as  $dataStr) 
									{ 
										$collarAndCuffDataArr = explode('=',$dataStr);
										$body_part 		= $collarAndCuffDataArr[0];
										$grey_size 		= $collarAndCuffDataArr[1];
										$finish_size	= $collarAndCuffDataArr[2];
										$gmts_size 		= $collarAndCuffDataArr[3];
										$qnty_pics 		= $collarAndCuffDataArr[4];
										$needle_per_cm 	= $collarAndCuffDataArr[5];
										$i = 1;
										// echo $collarAndCuffDataArr; 
										?>
											<tr id="tr_<?= $i ?>" class="general">
												<td>
													<input type="text" value="<?= $body_part ?>" id="bodyPart_<?= $i ?>" name="bodyPart[]" class="text_boxes" style="width:80px" value="" disabled/> 
												</td>
												<td>
													<input type="text" value="<?= $grey_size ?>" id="greySize_<?= $i ?>" name="greySize[]" class="text_boxes" style="width:80px" value="" disabled/> 
												</td>
												<td>
													<input type="text" value="<?= $finish_size ?>" id="finishSize_<?= $i ?>" name="finishSize[]" class="text_boxes" style="width:80px" value="" disabled/> 
												</td> 
												<td>
													<input type="text" value="<?= $gmts_size ?>" id="gmtsSize_<?= $i ?>" name="gmtsSize[]" class="text_boxes" style="width:80px" value="" disabled/> 
												</td> 
												<td>
													<input type="text" value="<?= $qnty_pics ?>" id="qtyPices_<?= $i ?>" name="qtyPices[]" class="text_boxes_numeric" style="width:80px" value="" /> 
												</td>  
												<td>
													<input type="text" value="<?= $needle_per_cm ?>" id="needlePerCm_<?= $i ?>" name="needlePerCm[]" class="text_boxes_numeric" style="width:80px" value="" /> 
												</td>  
												<td width="70"> 
													<input type="button" id="increase_<?= $i ?>" name="increase[]" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<?= $i ?>)" /> 
													<input type="button" id="decrease_<?= $i ?>" name="decrease[]" style="width:30px" class="formbutton" value="-" onClick="fn_deleteRow(<?= $i ?>);" />
												</td>
											</tr>
										<?
									}
								}
								else
								{
									$collarCuff_sql = "select collar_and_cuff_str as collar_cuff_data from pro_batch_create_mst where id=$hiddin_job_no and status_active=1  and is_deleted=0";
        							$collarCuff_data=sql_select($collarCuff_sql);
									foreach($collarCuff_data as $row)
									{
										$collarAndCuffString=$row[csf("collar_cuff_data")];
									}
									$collarAndCuffArray = explode('$$',$collarAndCuffString); 
									foreach ($collarAndCuffArray as  $dataStr) 
									{ 
										$collarAndCuffDataArray = explode('=',$dataStr);
										$body_part 		= $collarAndCuffDataArray[0];
										$grey_size 		= $collarAndCuffDataArray[1];
										$finish_size	= $collarAndCuffDataArray[2];
										$gmts_size 		= $collarAndCuffDataArray[3];
										$qnty_pics 		= $collarAndCuffDataArray[4];
										$needle_per_cm 	= $collarAndCuffDataArray[5];
										$i = 1;
										// echo $collarAndCuffDataArr; 
										?>
											<tr id="tr_<?= $i ?>" class="general">
												<td>
													<input type="text" value="<?= $body_part ?>" id="bodyPart_<?= $i ?>" name="bodyPart[]" class="text_boxes" style="width:80px" value="" disabled/> 
												</td>
												<td>
													<input type="text" value="<?= $grey_size ?>" id="greySize_<?= $i ?>" name="greySize[]" class="text_boxes" style="width:80px" value="" disabled/> 
												</td>
												<td>
													<input type="text" value="<?= $finish_size ?>" id="finishSize_<?= $i ?>" name="finishSize[]" class="text_boxes" style="width:80px" value="" disabled/> 
												</td> 
												<td>
													<input type="text" value="<?= $gmts_size ?>" id="gmtsSize_<?= $i ?>" name="gmtsSize[]" class="text_boxes" style="width:80px" value="" disabled/> 
												</td> 
												<td>
													<input type="text" value="<?= $qnty_pics ?>" id="qtyPices_<?= $i ?>" name="qtyPices[]" class="text_boxes_numeric" style="width:80px" value="" /> 
												</td>  
												<td>
													<input type="text" value="<?= $needle_per_cm ?>" id="needlePerCm_<?= $i ?>" name="needlePerCm[]" class="text_boxes_numeric" style="width:80px" value="" /> 
												</td>  
												<td width="70"> 
													<input type="button" id="increase_<?= $i ?>" name="increase[]" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<?= $i ?>)" /> 
													<input type="button" id="decrease_<?= $i ?>" name="decrease[]" style="width:30px" class="formbutton" value="-" onClick="fn_deleteRow(<?= $i ?>);" />
												</td>
											</tr>
										<?
									}
								}
							?> 
						</tbody>
					</table>
					<div align="center" style="margin-top:10px">
						<input type="button" class="formbutton" onClick="fnc_close()" value="Close" style="width:100px" />
						<input type="hidden" id="hide_data" /> 
						<input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="1">
					</div>
				</fieldset>
			</div>
		</body>
		<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

		</html>
	<?
}

if ($action=="print_report_setting")
{
   
	$print_report_format=return_field_value("format_id","lib_report_template","template_name='$data' and module_id=8 and report_id=163 and is_deleted=0 and status_active=1");
	$report_format_id=explode(",",$print_report_format);

	foreach($report_format_id as $row){

	
		if($row==346){?>
 			<input type="button" name="search" id="search" value="With Gate Pass" onClick="generate_report(1)" style="width:100px" class="formbuttonplasminus" />
		<? }elseif($row==347){?>
			<input type="button" name="search" id="search" value="With Gate Pass2" onClick="generate_report(4)" style="width:100px" class="formbuttonplasminus" />			
		<? }elseif($row==348){?>
			<input type="button" name="search" id="search" value="WithOut G.Pass" onClick="generate_report(2)" style="width:100px" class="formbuttonplasminus" />
		<? }elseif($row==349){?>
			<input type="button" name="search" id="search" value="WithOut G.Pass-2" onClick="generate_report(3)" style="width:100px" class="formbuttonplasminus" />
		<? }elseif($row==350){?>
			<input type="button" name="search" id="search" value="With Gate Pass JK" onClick="generate_report(5)" style="width:100px" class="formbuttonplasminus" />			
		<? }

	} 

	// print_r($report_format_id);

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
                            <th colspan="7" align="center"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                        </tr>
                    	<tr>
                            <th width="100">Job No</th>
                            <th width="100">Batch No</th>
                            <th width="100">Style No</th>
                            <th width="100">Order No</th>
                            <th width="130" colspan="2">Date Range</th>
                            <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:80px;" /></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td><input type="text" style="width:90px" class="text_boxes" name="txt_search_job" id="txt_search_job" placeholder="Search Job"/></td>
                            <td><input type="text" style="width:90px" class="text_boxes" name="txt_batch_no" id="txt_batch_no" placeholder="Search Batch"/></td>
                            <td><input type="text" style="width:90px" class="text_boxes" name="txt_search_style" id="txt_search_style" placeholder="Style" /></td>
                            <td><input type="text" style="width:90px" class="text_boxes"  name="txt_search_order" id="txt_search_order" placeholder="Order" /></td>
                            <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From"></td>
                            <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px" placeholder="To"></td>
                            <td>
                                <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_job').value+'_'+document.getElementById('txt_search_style').value+'_'+document.getElementById('txt_search_order').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<?=$company_id; ?>+'_'+<?=$party_id; ?>+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_batch_no').value, 'create_order_search_list_view', 'search_div', 'subcon_dye_finishing_delivery_controller', 'setFilterGrid(\'tbl_order_list\',-1)')" style="width:80px;" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="7" align="center" valign="middle">
                                <?=load_month_buttons(1);  ?>
                                <input type="hidden" id="hidden_order_value">
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
	$batch_no = $ex_data[8];
	if($search_job=="" && $search_style=="" && $search_order=="" && $batch_no=="" && $date_from=="" && $date_to=="")
	{
		echo "<b>Please input value anyone from search panel </b>";die;
	}

	if($search_type==1)
	{
		if ($search_job!='') $job_cond=" and b.job_no_prefix_num='$search_job'"; else $job_cond="";
		if ($search_style!='') $style_cond=" and a.cust_style_ref='$search_style'"; else $style_cond="";
		if ($search_order!='') $order_cond=" and a.order_no='$search_order'"; else $order_cond="";
		if ($batch_no!='') $batch_cond="join pro_batch_create_mst d on d.id=c.batch_id and d.entry_form=36 and d.batch_no = '$batch_no'"; else $batch_cond="";
	}
	else if($search_type==4 || $search_type==0)
	{
		if ($search_job!='') $job_cond=" and b.job_no_prefix_num like '%$search_job%'"; else $job_cond="";
		if ($search_style!='') $style_cond=" and a.cust_style_ref like '%$search_style%'"; else $style_cond="";
		if ($search_order!='') $order_cond=" and a.order_no like '%$search_order%'"; else $order_cond="";
		if ($batch_no!='') $batch_cond="join pro_batch_create_mst d on d.id=c.batch_id and d.entry_form=36 and d.batch_no like '%$batch_no%'"; else $batch_cond="";
	}
	else if($search_type==2)
	{
		if ($search_job!='') $job_cond=" and b.job_no_prefix_num like '$search_job%'"; else $job_cond="";
		if ($search_style!='') $style_cond=" and a.cust_style_ref like '$search_style%'"; else $style_cond="";
		if ($search_order!='') $order_cond=" and a.order_no like '$search_order%'"; else $order_cond="";
		if ($batch_no!='') $batch_cond="join pro_batch_create_mst d on d.id=c.batch_id and d.entry_form=36 and d.batch_no like '$batch_no%'"; else $batch_cond="";
	}
	else if($search_type==3)
	{
		if ($search_job!='') $job_cond=" and b.job_no_prefix_num like '%$search_job'"; else $job_cond="";
		if ($search_style!='') $style_cond=" and a.cust_style_ref like '%$search_style'"; else $style_cond="";
		if ($search_order!='') $order_cond=" and a.order_no like '%$search_order'"; else $order_cond="";
		if ($batch_no!='') $batch_cond="join pro_batch_create_mst d on d.id=c.batch_id and d.entry_form=36 and d.batch_no like'%$batch_no'"; else $batch_cond="";
	}

	if(	$party!=0) $party_cond=" and b.party_id='$party'"; else $party_cond="";

	if($db_type==0)
	{
		if ($date_from!="" &&  $date_to!="") $date_cond= "and a.delivery_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'"; else $date_cond= "";
		$year_cond= "year(b.insert_date)as year";
		$order_id_cond="a.id=SUBSTRING_INDEX(c.order_id, ',', 1)";
	}
	else if ($db_type==2)
	{
		if ($date_from!="" &&  $date_to!="") $date_cond= "and a.delivery_date between '".change_date_format($date_from, "", "",1)."' and '".change_date_format($date_to, "", "",1)."'";  else $date_cond= "";
		$year_cond= "TO_CHAR(b.insert_date,'YYYY') as year";
		$order_id_cond=" and to_char(a.id)=c.order_id";
	}

	$sql = "SELECT a.id, a.order_rcv_date, a.order_no, a.order_uom, a.main_process_id, a.order_quantity, b.party_id, a.cust_style_ref, b.subcon_job, b.job_no_prefix_num, $year_cond from subcon_ord_dtls a JOIN subcon_ord_mst b ON a.job_no_mst = b.subcon_job JOIN SUBCON_PRODUCTION_QNTY e ON a.id=e.order_id JOIN subcon_production_dtls c On c.id=e.dtls_id and c.status_active=1 and c.is_deleted=0  $batch_cond where a.main_process_id in (3,4,26) and a.status_active=1 and a.is_deleted=0 and b.company_id='$company' $party_cond $job_cond $style_cond $order_cond $date_cond  group by a.id, a.order_rcv_date, a.order_no, a.order_uom, a.main_process_id, a.order_quantity, b.party_id, a.cust_style_ref, b.subcon_job, b.job_no_prefix_num, b.insert_date order by a.id DESC";
	 //echo $sql;
	$resrow = sql_select($sql); $poid="";
	foreach($resrow as $row)
	{
		$poid.=$row[csf("id")].',';
		$poIdArr[$row[csf("id")]]=$row[csf("id")];
	}
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id."   and ENTRY_FORM=72");
	oci_commit($con);
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 72, 1, $poIdArr, $empty_arr);//PO ID Ref from=1

	$po_ids=array_filter(array_unique(explode(",",$poid)));
	$poidCond=where_con_using_array($po_ids,0,"b.po_id"); 
	$batch_arr=array();
	// $batch_sql="select a.batch_no, b.po_id, b.batch_qnty from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.entry_form=36 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $poidCond";
	$batch_sql="select a.batch_no, b.po_id, b.batch_qnty from pro_batch_create_mst a, pro_batch_create_dtls b,gbl_temp_engine g where a.id=b.mst_id and  b.po_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=72 and a.entry_form=36 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$result_batch_sql=sql_select($batch_sql);
	foreach($result_batch_sql as $row)
	{
		$batch_arr[$row[csf('po_id')]]['batch_no'].=$row[csf('batch_no')].',';
		$batch_arr[$row[csf('po_id')]]['bqty']+=$row[csf('batch_qnty')];
	}
	unset($result_batch_sql);
	$batchNoArr=array(); $batQtyArr=array();
	foreach($batch_arr as $pid=>$val)
	{
		$batchNo="";
		$batchNo=implode(",",array_filter(array_unique(explode(",",$val['batch_no']))));
		$batchNoArr[$pid]=$batchNo;
		$batQtyArr[$pid]=number_format($val['bqty'],2,'.','');
	}
	unset($batch_arr);
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id."   and ENTRY_FORM=72");
	oci_commit($con);
	disconnect($con);
	//print_r($batQtyArr);
	// $sql;
	if(count($poIdArr)>0)
	{
		$poids=implode(',',$poIdArr);
		$poidcond=" and a.id in($poids)";
	}
	   $sql = "SELECT a.id, a.order_rcv_date, a.order_no, a.order_uom, a.main_process_id, a.order_quantity, b.party_id, a.cust_style_ref, b.subcon_job, b.job_no_prefix_num, $year_cond from subcon_ord_dtls a, subcon_ord_mst b, subcon_production_dtls c	where a.main_process_id in (3,4,26) and a.job_no_mst=b.subcon_job $order_id_cond and a.status_active=1 and a.is_deleted=0 and b.company_id='$company' $poidcond $party_cond $job_cond $style_cond $order_cond $date_cond group by a.id, a.order_rcv_date, a.order_no, a.order_uom, a.main_process_id, a.order_quantity, b.party_id, a.cust_style_ref, b.subcon_job, b.job_no_prefix_num , b.insert_date ";

	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (4=>$production_process,5=>$batchNoArr,6=>$party_arr,7=>$batQtyArr);
	echo  create_list_view("tbl_order_list", "Job,Year,Delivery Date,Order No,Process,Batch No,Party,Batch Qty,Order Qty, Style", "50,50,60,100,100,90,120,80,80,90","900","250",0, $sql , "js_set_value", "id", "", 1, "0,0,0,0,main_process_id,id,party_id,id,0,0", $arr , "job_no_prefix_num,year,order_rcv_date,order_no,main_process_id,id,party_id,id,order_quantity,cust_style_ref", "requires/subcon_dye_finishing_delivery_controller",'','0,0,3,0,0,0,0,0,2,0') ;
	exit();
}

if($action=="populate_data_from_search_popup")
{
	//echo "select a.id, a.delivery_date as order_date, a.main_process_id, a.order_no, a.order_quantity, a.order_uom, a.cust_style_ref, b.company_id, b.party_id, b.subcon_job from  subcon_ord_mst b, subcon_ord_dtls a where a.job_no_mst=b.subcon_job and a.id='$data'";
	$res = sql_select("select a.id, a.delivery_date as order_date, a.main_process_id, a.order_no, a.order_quantity, a.order_uom, a.cust_style_ref, b.company_id, b.party_id, b.subcon_job from  subcon_ord_mst b, subcon_ord_dtls a where a.main_process_id in (3,4,26) and a.job_no_mst=b.subcon_job and a.id='$data'");
	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
 	foreach($res as $result)
	{
		echo "document.getElementById('txt_order_no').value 					= '".$result[csf("order_no")]."';\n";
	    echo "document.getElementById('txt_order_id').value            			= '".$result[csf("id")]."';\n";
		echo "document.getElementById('cbo_process_name').value 				= '".$result[csf("main_process_id")]."';\n";
		echo "document.getElementById('txt_order_date').value 					= '".change_date_format($result[csf("order_date")])."';\n";
		echo "document.getElementById('txt_ordr_qnty').value 					= '".$result[csf("order_quantity")]."';\n";
		echo "document.getElementById('txt_uom').value 							= '".$unit_of_measurement[$result[csf("order_uom")]]."';\n";
		echo "document.getElementById('txt_style').value 						= '".$result[csf("cust_style_ref")]."';\n";
	}
	exit();
}

if($action=="show_fabric_desc_listview")
{
	$data=explode('_',$data);
	$order_id=$data[0];
	$process_id=$data[1];
	$company_id=$data[2];
	$click_type=$data[3];
	//echo $process_id;
	$item_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');
	$gsm_arr=return_library_array( "select id,gsm from lib_subcon_charge",'id','gsm');
	$color_arr=return_library_array( "select id,color_name from lib_color",'id','color_name');

	$order_qty_array=array();
	$sql_qty_order="Select a.id, b.item_id, color_id, sum(b.qnty) as qnty from subcon_ord_dtls a, subcon_ord_breakdown b where a.id=b.order_id and a.status_active=1 and a.is_deleted=0 and a.order_id in($order_id)  group by  a.id, b.item_id, color_id";
	$order_data_sql=sql_select($sql_qty_order);
	foreach($order_data_sql as $row)
	{
		$order_qty_array[$row[csf('id')]][$row[csf('item_id')]]=$row[csf('qnty')];
	}
	//var_dump($order_qty_array);

	// $batch_color_arr=array();
	// $batch_sql="select id, batch_no, extention_no, color_id from pro_batch_create_mst where company_id='$company_id' and entry_form=36 and status_active=1 and is_deleted=0";
	// $result_batch_sql=sql_select($batch_sql);
	// foreach($result_batch_sql as $row)
	// {
	// 	$batch_color_arr[$row[csf('id')]]['color']=$row[csf('color_id')];
	// 	$batch_color_arr[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
	// 	$batch_color_arr[$row[csf('id')]]['extention_no']=$row[csf('extention_no')];
	// }
	//var_dump($batch_color_arr);

	$delivery_qty_array=array();
	$gray_qty_array=array();
	if ($process_id==3 || $process_id==4 || $process_id==26)
	{
		// $delivery_sql="select batch_id, item_id, dia, sum(delivery_qty) as delivery_qty, sum(gray_qty) as gray_qty from subcon_delivery_dtls where order_id='$order_id' and process_id='$process_id' and status_active=1 and is_deleted=0 group by batch_id, item_id, dia order by batch_id, item_id, dia DESC";

		$delivery_sql="select a.batch_id, a.item_id, a.dia, sum(a.delivery_qty) as delivery_qty, sum(a.gray_qty) as gray_qty, c.item_description as fabric_description	from subcon_delivery_dtls a,pro_batch_create_mst b,pro_batch_create_dtls c where a.order_id='$order_id' and c.id=a.item_id and  a.batch_id=b.id and b.id=c.mst_id and a.process_id='$process_id'  and a.dia=c.fin_dia and a.color_id=b.color_id  and a.process_id='$process_id' and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.batch_id, a.item_id, a.dia,c.item_description order by a.batch_id, a.item_id, a.dia DESC";

		$delivery_data_sql=sql_select($delivery_sql);
		foreach($delivery_data_sql as $row)
		{
			// $delivery_qty_array[$row[csf('batch_id')]][$row[csf('item_id')]][$row[csf('dia')]]=$row[csf('delivery_qty')];
			// $gray_qty_array[$row[csf('batch_id')]][$row[csf('item_id')]][$row[csf('dia')]]=$row[csf('gray_qty')];


			$delivery_qty_array[$row[csf('batch_id')]][$row[csf('item_id')]][$row[csf('dia')]][$row[csf('fabric_description')]]=$row[csf('delivery_qty')];
			$gray_qty_array[$row[csf('batch_id')]][$row[csf('item_id')]][$row[csf('dia')]][$row[csf('fabric_description')]]=$row[csf('gray_qty')];
		}
	}
	//print_r($delivery_qty_array);
	
	if($process_id==3)
	{
		$sql="SELECT a.batch_id, c.gsm, c.fin_dia as dia_width, c.width_dia_type, c.id as cons_comp_id, b.color_id, c.item_description as fabric_description, a.process_id as process, sum(c.batch_qnty) as production_qnty
		from pro_fab_subprocess a, pro_batch_create_mst b, pro_batch_create_dtls c
		where a.batch_id=b.id and b.id=c.mst_id and c.po_id in ($order_id) and a.load_unload_id=2 and a.result=1 and a.entry_form=38 and b.entry_form=36 and c.status_active=1 and c.is_deleted=0
		group by a.batch_id, c.gsm, c.fin_dia, c.width_dia_type, c.id, b.color_id, c.item_description, a.process_id
		order by a.batch_id DESC";
	}
	else
	{
		$processid.=$process_id.',4';
		$sql = "SELECT a.batch_id, a.gsm, a.dia_width, c.width_dia_type, a.cons_comp_id, a.color_id, c.item_description as fabric_description, a.process, sum(b.quantity) as production_qnty
		from subcon_production_dtls a, subcon_production_qnty b, pro_batch_create_dtls c
		where b.order_id in ($order_id) and a.id=b.dtls_id and a.cons_comp_id=c.id and a.product_type in($processid)
		group by a.batch_id, a.gsm, a.dia_width, a.cons_comp_id, a.color_id, c.item_description, a.process, c.width_dia_type
		order by a.batch_id,a.cons_comp_id,a.dia_width DESC";
	}

 //echo $sql;  
	$sql_result = sql_select($sql);
	foreach($sql_result as $row)
	{
		$batch_idArr[$row[csf('batch_id')]]=$row[csf('batch_id')];
	}
	$batch_color_arr=array();
	$batch_sql="select id, batch_no, extention_no, color_id from pro_batch_create_mst where company_id='$company_id' and entry_form=36 and status_active=1 and is_deleted=0 and id in (".implode(",",$batch_idArr).") ";
	$result_batch_sql=sql_select($batch_sql);
	foreach($result_batch_sql as $row)
	{
		$batch_color_arr[$row[csf('id')]]['color']=$row[csf('color_id')];
		$batch_color_arr[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
		$batch_color_arr[$row[csf('id')]]['extention_no']=$row[csf('extention_no')];
	}
	//var_dump($batch_color_arr);

		?>
		 <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="500">
			<thead>
				<th width="15">SL</th>
                <th width="40">Batch</th>
                <th width="20">Ex.</th>
                <th width="120">Process</th>
                <th width="50">Color</th>
				<th width="70">Fabric Des.</th>
				<th width="30">GSM</th>
				<th width="30">Dia</th>
				<th width="40">Prod. Qty</th>
				<th width="55">Delv. Qty</th>
				<th>Bal. Qty</th>
			</thead>
			<tbody>
				<?
				$i=1;
				foreach($sql_result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

					$item_name=explode(',',$row[csf('fabric_description')]);

					$gsm_val=$gsm_arr[$row[csf('item_id')]];

					$sub_process_id=explode(',',$row[csf('process')]);
					$process_val='';
					foreach ($sub_process_id as $val)
					{
						if($process_val=='') $process_val=$conversion_cost_head_array[$val]; else $process_val.=" + ".$conversion_cost_head_array[$val];
					}
					
					$row_dia_width = $row[csf('dia_width')];
					if($row[csf('dia_width')]=='' || $row[csf('dia_width')]=='.' || $row[csf('dia_width')]=='..')
					{
						$row[csf('dia_width')]=0;
					}
					else
					{
						$row[csf('dia_width')]=$row[csf('dia_width')];
					}
					
					//if($row[csf('dia_width')]=='' || $row[csf('dia_width')]=='.' || $row[csf('dia_width')]=='..') $row[csf('dia_width')]=0;else $row[csf('dia_width')]=$row[csf('dia_width')];

					$del_qty=$delivery_qty_array[$row[csf('batch_id')]][$row[csf('cons_comp_id')]][$row[csf('dia_width')]][$row[csf('fabric_description')]];
					$gray_qty=$gray_qty_array[$row[csf('batch_id')]][$row[csf('cons_comp_id')]][$row[csf('dia_width')]][$row[csf('fabric_description')]];

					// $del_qty=$delivery_qty_array[$row[csf('batch_id')]][$row[csf('cons_comp_id')]][$row[csf('dia_width')]];
					// $gray_qty=$gray_qty_array[$row[csf('batch_id')]][$row[csf('cons_comp_id')]][$row[csf('dia_width')]];

					$availabe_delivery_qty=$row[csf('production_qnty')]-$del_qty;
					// echo $row[csf('production_qnty')].'='.$del_qty.'<br>';
					if($availabe_delivery_qty!=0)
					{
					 ?>
                     
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="get_php_form_data('<? echo $order_id.'_'.$process_id.'_'.$row[csf('cons_comp_id')].'_'.$row[csf('batch_id')].'_'.$row[csf('gsm')].'_'.$row_dia_width.'_'.$row[csf('color_id')].'_'.$row[csf('process')].'_'.$click_type.'_'.$gray_qty.'_'.$row[csf('width_dia_type')]; ?>','load_php_data_for_batch','requires/subcon_dye_finishing_delivery_controller');" style="cursor:pointer">
							<td><? echo $i; ?></td>
							<td><p><? echo $batch_color_arr[$row[csf('batch_id')]]['batch_no']; ?></p></td>
							<td><p><? echo $batch_color_arr[$row[csf('batch_id')]]['extention_no']; ?></p></td>
                            <td><p><? echo $process_val; ?></p></td>
							<td><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td><p><? echo $item_name[0]; ?></p></td>
							<td><? echo $row[csf('gsm')]; ?></td>
							<td><? echo $row[csf('dia_width')]; ?></td>
							<td align="right"><? echo number_format($row[csf('production_qnty')],2,'.',''); ?></td>
							<td align="right"><? echo number_format($del_qty,2,'.',''); ?></td>
							<td align="right"><? echo number_format($row[csf('production_qnty')]-$del_qty,2,'.',''); ?></td>
						</tr>
					<?
					$i++;
					}
				}
				?>
			</tbody>
		</table>
	<?
	exit();
}

if($action=="load_php_data_for_batch")
{
	//echo $data; die;
	$ex_data=explode('_',$data);
	$order_id=$ex_data[0];
	$process_id=$ex_data[1];
	$prod_id=$ex_data[2];
	$batch_id=$ex_data[3];
	$gsm=$ex_data[4];
	$dia=$ex_data[5];
	$color_id=$ex_data[6];
	$sub_process=$ex_data[7];
	$click_type=$ex_data[8];
	$gray_qty=$ex_data[9];
	$width_dia_type=$ex_data[10];
	if($process_id==3)
	{
		if($dia!='') $dia_cond=" and c.fin_dia='$dia'"; else $dia_cond="";
	}
	else
	{
		if($dia!='') $dia_cond=" and a.dia_width='$dia'"; else $dia_cond="";
	}
	$item_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');
	$machine_arr=return_library_array( "select id, machine_no from lib_machine_name",'id','machine_no');
	$color_arr=return_library_array( "select id,color_name from lib_color where status_active=1",'id','color_name');
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');

	$batch_weight_arr=return_library_array( "select id, batch_weight from pro_batch_create_mst where id=$batch_id",'id','batch_weight');
	// if($color_id!='') $color_id_cond=" and color_id='$color_id'"; else $color_id_cond="";

	if($color_id!='') $color_id_cond=" and a.color_id='$color_id'"; else $color_id_cond="";
	
	


	$delivery_qty_array=array();
	// $delivery_sql="select item_id, gsm, dia, color_id,sum(reject_qty) as reject_qty, sum(delivery_qty) as delivery_qty from subcon_delivery_dtls where order_id='$order_id' and item_id='$prod_id' and gsm='$gsm' and dia='$dia' and process_id in (3,4) and status_active=1 and is_deleted=0 $color_id_cond group by item_id, gsm, dia, color_id";

	// $delivery_sql="select a.batch_id, a.item_id, a.dia,a.color_id,a.gsm, sum(a.delivery_qty) as delivery_qty, sum(a.gray_qty) as gray_qty, c.item_description as fabric_description from subcon_delivery_dtls a,pro_batch_create_mst b,pro_batch_create_dtls c where a.order_id='$order_id' and  a.batch_id=b.id and b.id=c.mst_id and a.process_id in (3,4) and a.item_id='$prod_id' and a.gsm='$gsm' and a.dia='$dia' and a.status_active=1 and a.is_deleted=0 $color_id_cond group by a.batch_id, a.item_id, a.dia,c.item_description,a.color_id,a.gsm  order by a.batch_id, a.item_id, a.dia DESC";
	
	$delivery_sql="select a.batch_id, a.item_id, a.dia,a.color_id,a.gsm, sum(a.delivery_qty) as delivery_qty, sum(a.gray_qty) as gray_qty, c.item_description as fabric_description from subcon_delivery_dtls a,pro_batch_create_mst b,pro_batch_create_dtls c where a.order_id='$order_id' and  a.batch_id=b.id and b.id=c.mst_id and a.process_id in (3,4,26)  and a.item_id=c.id and a.item_id='$prod_id' and a.gsm='$gsm'  and a.dia='$dia' and a.status_active=1 and a.is_deleted=0 $color_id_cond group by a.batch_id, a.item_id, a.dia,c.item_description,a.color_id,a.gsm  order by a.batch_id, a.item_id, a.dia DESC";
	
	$delivery_data_sql=sql_select($delivery_sql);
	foreach($delivery_data_sql as $row)
	{
		//$fabric_descriptionArr=explode(",",$row[csf('fabric_description')]);
		$fabric_desc=$row[csf('fabric_description')];
		$delivery_qty_array[$row[csf('batch_id')]][$fabric_desc][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('color_id')]]['delivery_qty']=$row[csf('delivery_qty')];
		$delivery_qty_array[$row[csf('batch_id')]][$fabric_desc][$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('color_id')]]['reject_qty']=$row[csf('reject_qty')];
	}
	$batch_color_arr=array();
	$batch_sql="select id, batch_no, extention_no, color_id from pro_batch_create_mst where entry_form=36 and id=$batch_id and status_active=1 and is_deleted=0";
	$result_batch_sql=sql_select($batch_sql);
	foreach($result_batch_sql as $row)
	{
		$batch_color_arr[$row[csf('id')]]['color']=$row[csf('color_id')];
		$batch_color_arr[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
		$batch_color_arr[$row[csf('id')]]['extention_no']=$row[csf('extention_no')];
	}
	if($prod_id!='') $prod_cond=" and cons_comp_id='$prod_id'"; else $prod_cond="";

	//print_r($delivery_qty_array);

	if($process_id==3)
	{
		$sql="SELECT a.batch_id, c.gsm, c.fin_dia as dia_width, c.id as cons_comp_id, b.color_id, c.item_description as fabric_description, a.process_id as process, sum(c.roll_no) as no_of_roll, sum(c.batch_qnty) as production_qnty from pro_fab_subprocess a, pro_batch_create_mst b, pro_batch_create_dtls c where a.batch_id=b.id and b.id=c.mst_id and c.po_id in ($order_id) and c.id='$prod_id' and a.batch_id='$batch_id' and c.gsm='$gsm' $dia_cond and b.color_id='$color_id' and a.load_unload_id=2 and a.result=1 and a.entry_form=38 and b.entry_form=36 and c.status_active=1 and c.is_deleted=0 group by a.batch_id, c.gsm, c.fin_dia, c.width_dia_type, c.id, b.color_id, c.item_description, a.process_id order by a.batch_id DESC";
	}
	else
	{
		$processid.=$process_id.',4';
		// $sql = "SELECT a.batch_id, a.gsm, a.dia_width, a.cons_comp_id, a.color_id, a.fabric_description, a.process, sum(a.no_of_roll) as no_of_roll, sum(b.quantity) as production_qnty from subcon_production_dtls a, subcon_production_qnty b where b.order_id in ($order_id) and a.id=b.dtls_id and a.product_type='$process_id' and a.cons_comp_id='$prod_id' and a.batch_id='$batch_id' and a.gsm='$gsm' $dia_cond and a.color_id='$color_id' and a.process='$sub_process' group by a.batch_id, a.cons_comp_id, a.color_id, a.gsm, a.dia_width, a.fabric_description, a.process";
		$sql = "SELECT a.batch_id, a.gsm, a.dia_width, a.cons_comp_id, a.color_id, c.item_description as fabric_description, a.process, sum(a.no_of_roll) as no_of_roll, sum(b.quantity) as production_qnty from subcon_production_dtls a, subcon_production_qnty b,pro_batch_create_dtls c where b.order_id in ($order_id) and a.id=b.dtls_id  and a.cons_comp_id=c.id  and a.product_type in($processid) and a.cons_comp_id='$prod_id' and a.batch_id='$batch_id' and a.gsm='$gsm' $dia_cond and a.color_id='$color_id' and a.process='$sub_process' group by a.batch_id, a.cons_comp_id, a.color_id, a.gsm, a.dia_width, c.item_description, a.process";
	}

	$sql_result = sql_select($sql);

	//echo $sql;
	$sql_result = sql_select($sql);
	foreach ($sql_result as $row)
	{
		$sub_process_id=explode(',',$row[csf('process')]);
		$process_val='';
		foreach ($sub_process_id as $val)
		{
			if($process_val=='') $process_val=$conversion_cost_head_array[$val]; else $process_val.=" + ".$conversion_cost_head_array[$val];
		}
		if($process_id==3)
		{
			//$fabric_descArr=explode(',',$row[csf('fabric_description')]);
			$fabric_desc=$row[csf('fabric_description')];
			$delivery_qty=$delivery_qty_array[$row[csf('batch_id')]][$fabric_desc][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('color_id')]]['delivery_qty'];
			$reject_qty=$delivery_qty_array[$row[csf('batch_id')]][$fabric_desc][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('color_id')]]['reject_qty'];
		}
		else{
			//$fabric_descArr=explode(',',$row[csf('fabric_description')]);
			$fabric_desc=$row[csf('fabric_description')];
			$delivery_qty=$delivery_qty_array[$row[csf('batch_id')]][$fabric_desc][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('color_id')]]['delivery_qty'];
			$reject_qty=$delivery_qty_array[$row[csf('batch_id')]][$fabric_desc][$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('color_id')]]['reject_qty'];
		}


		$availabe_delivery_qty=$row[csf('production_qnty')]-$delivery_qty;
		$reject_qty=$reject_qty;


		echo "document.getElementById('txt_item_id').value		 			= '".$row[csf("cons_comp_id")]."';\n";
		echo "document.getElementById('txt_dalivery_item').value		 	= '".$row[csf("fabric_description")]."';\n";
		//echo "document.getElementById('txt_reject_qty').value		 			= '".$row[csf("reject_qty")]."';\n";
		if($click_type==2)
		{
			echo "document.getElementById('txt_carton_roll_no').value		 	= '".$row[csf("no_of_roll")]."';\n";
		}
		echo "document.getElementById('txt_production_qnty').value		 	= '".number_format($availabe_delivery_qty, 2,'.','')."';\n";
		echo "document.getElementById('txt_reject_qty').value		 		= '".$reject_qty."';\n";
		echo "document.getElementById('txt_cumullative_gray_qnty').value		 	= '".$gray_qty."';\n";
		echo "document.getElementById('hidden_dia_type').value		 				= '".$width_dia_type."';\n";
		echo "document.getElementById('txt_color_id').value		 			= '".$row[csf("color_id")]."';\n";
		echo "document.getElementById('txt_color').value		 			= '".$color_arr[$row[csf("color_id")]]."';\n";
		echo "document.getElementById('txt_gsm').value		 				= '".$row[csf("gsm")]."';\n";
		echo "document.getElementById('txt_dia').value		 				= '".$row[csf("dia_width")]."';\n";
		echo "document.getElementById('txt_batch_no').value		 			= '".$batch_color_arr[$row[csf("batch_id")]]['batch_no']."';\n";
		echo "document.getElementById('txt_batch_id').value		 			= '".$row[csf("batch_id")]."';\n";
		echo "document.getElementById('txt_ext_no').value		 			= '".$batch_color_arr[$row[csf("batch_id")]]['extention_no']."';\n";
		echo "document.getElementById('hid_sub_process').value		 		= '".$row[csf("process")]."';\n";
		echo "document.getElementById('txt_sub_process').value 				= '".$process_val."';\n";
		echo "document.getElementById('txt_batch_weight').value		 		= '".$batch_weight_arr[$row[csf("batch_id")]]."';\n";
	}
	exit();
}

if($action=="delivery_entry_list_view")
{
	?>
	<div style="width:810px;">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="810" class="rpt_table" id="table_body">
            <thead>
                <th width="20">SL</th>
                <th width="80">Order</th>
                <th width="70">Challan No</th>
                <th width="130">Delivery Item</th>
                <th width="60">Delivery Date</th>
                <th width="70">Delivery Qty</th>
                <th width="50">Carton /Roll</th>
                <th width="70">Batch No</th>
                <th width="100">Forwarder</th>
                <th width="60">Batch Qty</th>
                <th>Bill Status</th>
            </thead>
            <tbody>
		<?php
		$i = 1;
		$party_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
		//$order_id_arr = return_library_array("select id,order_no from  subcon_ord_dtls", 'id', 'order_no');
		$lib_item_arr = return_library_array("select id,const_comp from lib_subcon_charge", 'id', 'const_comp');
		//$inv_item_arr = return_library_array("select id,material_description from sub_material_dtls", 'id', 'material_description');
		//$prod_item_arr = return_library_array("select id,fabric_description from subcon_production_dtls", 'id', 'fabric_description');
		$batch_array = array();
		$batch_sql = "select a.id, a.batch_no, b.id as item_id, b.item_description, b.batch_qnty from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.entry_form=36 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
		$batch_sql_result = sql_select($batch_sql);
		foreach ($batch_sql_result as $row) {
			$batch_array[$row[csf('id')]][$row[csf('item_id')]]['item'] = $row[csf('item_description')];
			$batch_array[$row[csf('id')]][$row[csf('item_id')]]['batch'] = $row[csf('batch_no')];
			$batch_array[$row[csf('id')]][$row[csf('item_id')]]['qty']+= $row[csf('batch_qnty')];
		}
		$bill_row_status = array(0 => "Bill Pending", 1 => "Bill Complete");
		$sql = sql_select("select a.id, a.delivery_date, a.challan_no, a.transport_company, a.forwarder, b.batch_id, b.id as dtls_id, b.item_id, b.delivery_qty, b.carton_roll,  b.bill_status, b.order_id, b.process_id from subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id='$data'");
		foreach ($sql as $row) 
		{
			$orderIdArr[$row[csf('order_id')]]=$row[csf('order_id')];
			 
		}
		$order_id_arr = return_library_array("select id,order_no from  subcon_ord_dtls where id in(".implode(",",$orderIdArr).")", 'id', 'order_no');

		foreach ($sql as $row) 
		{
			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else  $bgcolor = "#FFFFFF";
	?>
                <tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data('<?=$row[csf('dtls_id')].'_'.$row[csf('process_id')]; ?>','load_php_data_to_form_delivery','requires/subcon_dye_finishing_delivery_controller');" >
                    <td width="20" align="center"><?=$i; ?></td>
                    <?
						$item_name=$batch_array[$row[csf('batch_id')]][$row[csf('item_id')]]['item'];
						$batchNo=$batch_array[$row[csf('batch_id')]][$row[csf('item_id')]]['batch'];
						$batchQty=$batch_array[$row[csf('batch_id')]][$row[csf('item_id')]]['qty'];
                    ?>
                    <td width="80" style="word-break:break-all"><?=$order_id_arr[$row[csf('order_id')]]; ?></td>
                    <td width="70" style="word-break:break-all"><?=$row[csf('challan_no')]; ?></td>
                    <td width="130" style="word-break:break-all"><?=$item_name; ?></td>
                    <td width="60" align="center"><?=change_date_format($row[csf('delivery_date')]); ?></td><!--change_date_format()-->
                    <td width="70" align="right"><?=$row[csf('delivery_qty')]; ?>&nbsp;</td>
                    <td width="50" align="center"><?=$row[csf('carton_roll')]; ?></td>
                    <td width="70" style="word-break:break-all"><?=$batchNo; ?></td>
                    <td width="100" style="word-break:break-all"><?=$party_arr[$row[csf('forwarder')]]; ?></td>
                    <td width="60" align="right"><?=number_format($batchQty,2); ?></td>
                    <td style="word-break:break-all"><?=$bill_row_status[$row[csf('bill_status')]]; ?></td>
                </tr>
                <?php
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
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$lib_item_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');

 
	if($db_type==0)
	{
		$bill_info=return_field_value("concat(b.delivery_id,'**',a.bill_no) as delivery_info", "subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b", "a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.party_source=2 and b.delivery_id='$ex_data[0]' ","delivery_info");
	}
	elseif($db_type==2)
	{
		//echo $ex_data[0].'D';;
		$bill_info=return_field_value("b.delivery_id || '**' || a.bill_no as delivery_info", "subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b", "a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.party_source=2  and b.delivery_id='$ex_data[0]' and a.bill_for=4 ","delivery_info");
	}
	$nameArray =sql_select("select id, order_id, process_id, sub_process_id, item_id, gsm, dia, batch_id, color_id, width_dia_type, delivery_qty, gray_qty, carton_roll, remarks, moisture_gain from subcon_delivery_dtls where id='$ex_data[0]'");

	foreach ($nameArray as $row)
	{
		$order_IdArr[$row[csf("order_id")]]=$row[csf("order_id")];
	}

	$batch_array=array();
	$batch_sql="select a.id, a.batch_no, a.extention_no, b.id as item_id, b.fabric_from, b.item_description from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and b.po_id in(".implode(",",$order_IdArr).") and a.entry_form=36 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$batch_sql_result=sql_select($batch_sql);
	foreach($batch_sql_result as $row)
	{
		//$batch_array[$row[csf('batch_no')]][$row[csf('extention_no')]][$row[csf('id')]]['fabric_from']=$row[csf('fabric_from')];
		$batch_array[$row[csf('id')]][$row[csf('item_id')]]['item_description']=$row[csf('item_description')];
		$batch_array[$row[csf('id')]][$row[csf('item_id')]]['batch_no']=$row[csf('batch_no')];
		$batch_array[$row[csf('id')]][$row[csf('item_id')]]['extention_no']=$row[csf('extention_no')];
	}

	$order_array=array();
	$sql_order=sql_select("select id, order_no, main_process_id, order_rcv_date, order_quantity, order_uom, cust_style_ref from subcon_ord_dtls where status_active=1 and is_deleted=0 and  id in(".implode(",",$order_IdArr).") ");

	foreach ($sql_order as $row)
	{
		$order_array[$row[csf("id")]]['order_no']=$row[csf("order_no")];
		$order_array[$row[csf("id")]]['main_process_id']=$row[csf("main_process_id")];
		$order_array[$row[csf("id")]]['order_rcv_date']=$row[csf("order_rcv_date")];
		$order_array[$row[csf("id")]]['order_quantity']=$row[csf("order_quantity")];
		$order_array[$row[csf("id")]]['order_uom']=$row[csf("order_uom")];
		$order_array[$row[csf("id")]]['cust_style_ref']=$row[csf("cust_style_ref")];
	}


	foreach ($nameArray as $row)
	{
		$process_id_val=$ex_data[1];

		$item_name=$batch_array[$row[csf('batch_id')]][$row[csf('item_id')]]['item_description'];
		$batch_no=$batch_array[$row[csf('batch_id')]][$row[csf("item_id")]]['batch_no'];
		$batch_ext=$batch_array[$row[csf('batch_id')]][$row[csf("item_id")]]['extention_no'];

		$sub_process_id=array_unique(explode(',',$row[csf('sub_process_id')]));
		$subprocess_val='';
		foreach ($sub_process_id as $val)
		{
			if($subprocess_val=='') $subprocess_val=$conversion_cost_head_array[$val]; else $subprocess_val.="+".$conversion_cost_head_array[$val];
		}

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
		echo "document.getElementById('txt_gray_qnty').value		 				= '".$row[csf("gray_qty")]."';\n";
		echo "document.getElementById('txt_gsm').value		 						= '".$row[csf("gsm")]."';\n";
		echo "document.getElementById('txt_dia').value		 						= '".$row[csf("dia")]."';\n";
		echo "document.getElementById('txt_batch_id').value		 					= '".$row[csf("batch_id")]."';\n";
		echo "document.getElementById('txt_batch_no').value		 					= '".$batch_no."';\n";
		echo "document.getElementById('txt_ext_no').value		 					= '".$batch_ext."';\n";
		echo "document.getElementById('txt_color').value		 					= '".$color_arr[$row[csf("color_id")]]."';\n";
		echo "document.getElementById('txt_color_id').value		 					= '".$row[csf("color_id")]."';\n";
		echo "document.getElementById('hidden_dia_type').value		 				= '".$row[csf("width_dia_type")]."';\n";
		echo "document.getElementById('txt_sub_process').value		 				= '".$subprocess_val."';\n";
		echo "document.getElementById('hid_sub_process').value		 				= '".$row[csf("sub_process_id")]."';\n";
		echo "document.getElementById('txt_moisture_gain').value		 			= '".$row[csf("moisture_gain")]."';\n";
		
		$bill_delivery=explode("**",$bill_info);
		echo "document.getElementById('bill_info').value		 					= '".$bill_info."';\n";
		echo "active_inactive(document.getElementById('bill_info').value);\n";

		echo "document.getElementById('txt_carton_roll_no').value					= '".$row[csf("carton_roll")]."';\n";
		echo "show_list_view(document.getElementById('txt_order_id').value+'_'+document.getElementById('cbo_process_name').value+'_'+document.getElementById('cbo_company_name').value+'_'+'2', 'show_fabric_desc_listview','list_fabric_desc_container','requires/subcon_dye_finishing_delivery_controller','');\n";
		echo "get_php_form_data(document.getElementById('txt_order_id').value+'_'+document.getElementById('cbo_process_name').value+'_'+document.getElementById('txt_item_id').value+'_'+document.getElementById('txt_batch_id').value+'_'+document.getElementById('txt_gsm').value+'_'+document.getElementById('txt_dia').value+'_'+document.getElementById('txt_color_id').value+'_'+document.getElementById('hid_sub_process').value+'_'+'1__'+document.getElementById('hidden_dia_type').value,'load_php_data_for_batch','requires/subcon_dye_finishing_delivery_controller');\n";

		echo "document.getElementById('txt_remarks').value		 					= '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('update_id_dtls').value		 				= '".$row[csf("id")]."';\n";

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
	//$collarAndCuffStr = str_replace("'",'',$collarAndCuffStr);
	if ($operation==0)  // Insert Start Here========================================================================================================================
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

		$return_delivery_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'DVY', date("Y",time()), 5, "select id, delivery_prefix, delivery_prefix_num from subcon_delivery_mst where company_id=$cbo_company_name and process_id=4 $year_cond=".date('Y',time())." order by id desc ", "delivery_prefix", "delivery_prefix_num" ));


		//echo $update_id;die;
		if(str_replace("'",'',$update_id)==0 || str_replace("'",'',$update_id)=='')
		{
			$id=return_next_id( "id"," subcon_delivery_mst", 1 ) ;
			$field_array="id, delivery_prefix, delivery_prefix_num, delivery_no, process_id, company_id, location_id, party_id, challan_no, delivery_date, forwarder, transport_company,vehical_no,driver_name,mobile_no,remark,inserted_by, insert_date, status_active, is_deleted";

			$challan=str_replace("'",'',$txt_challan_no);
			//echo $challan;die;
			if ($challan!='' && $challan!=0)
			{
				$challan_no=$txt_challan_no;
			}
			else
			{
				$challan_no=$return_delivery_no[2];
			}
			$data_array="(".$id.",'".$return_delivery_no[1]."','".$return_delivery_no[2]."','".$return_delivery_no[0]."',4,".$cbo_company_name.",".$cbo_location.",".$cbo_party_name.",".$challan_no.",".$txt_delivery_date.",".$cbo_forwarder.",".$txt_transport_company.",".$txt_vehical_no.",".$txt_driver_name.",".$txt_mobile_no.",".$txt_remark.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			//echo "insert into subcon_delivery_mst (".$field_array.") values ".$data_array;//die;
			$rID=sql_insert("subcon_delivery_mst",$field_array,$data_array,0);
			$return_no=$return_delivery_no[0];
		}
		else
		{
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
			$field_array="location_id*party_id*challan_no*delivery_date*forwarder*transport_company*vehical_no*driver_name*mobile_no*remark*updated_by*update_date";
			$data_array="".$cbo_location."*".$cbo_party_name."*".$txt_challan_no."*".$txt_delivery_date."*".$cbo_forwarder."*".$txt_transport_company."*".$txt_vehical_no."*".$txt_driver_name."*".$txt_mobile_no."*".$txt_remark."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

			//echo "0***"."insert into subcon_delivery_mst (".$field_array.") values ".$data_array;die;
			$id=$update_id;
			$rID=sql_update("subcon_delivery_mst",$field_array,$data_array,"id",$update_id,0);
			$return_no=$txt_sys_id;
		}

		$dtlsid=return_next_id( "id"," subcon_delivery_dtls", 1 ) ;
		$field_array_dtls="id, mst_id, order_id, process_id, sub_process_id, item_id, gsm, dia, batch_id, color_id, width_dia_type, delivery_qty, carton_roll, remarks, gray_qty, reject_qty, moisture_gain,collar_and_cuff_str";

		$data_array_dtls="(".$dtlsid.",".$id.",".$txt_order_id.",".$cbo_process_name.",".$hid_sub_process.",".$txt_item_id.",".$txt_gsm.",".$txt_dia.",".$txt_batch_id.",".$txt_color_id.",".$hidden_dia_type.",".$txt_delivery_qnty.",".$txt_carton_roll_no.",".$txt_remarks.",".$txt_gray_qnty.",".$txt_reject_qty.",".$txt_moisture_gain.",".$collarAndCuffStr.")";
		//echo "insert into subcon_delivery_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		$rID1=sql_insert("subcon_delivery_dtls",$field_array_dtls,$data_array_dtls,1);

		//=========================================================================================
		//										Collar AND CUFF 	
		//=========================================================================================	
		$flag=1;
		
		if($collarAndCuffStr !="" )
		{
			$field_array4="id,ord_mst_id,ord_dtls_id,ord_breakdown_id,body_part,grey_size,finish_size,gmts_size,qnty_pics,needle_per_cm,inserted_by,insert_date";
			
			$collarAndCuffArr = explode('$$',$collarAndCuffStr);
			$kk= 0 ;
			$data_array4 = ''; 
			foreach ($collarAndCuffArr as $row) 
			{
				$id4 = return_next_id_by_sequence(  "subcon_ord_collar_and_cuff_dtls_seq", "subcon_ord_collar_and_cuff_dtls", $con );

				$collarAndCuffDataArr = explode('=',$row);
				$body_part 		= $collarAndCuffDataArr[0];
				$grey_size 		= $collarAndCuffDataArr[1];
				$finish_size	= $collarAndCuffDataArr[2];
				$gmts_size 		= $collarAndCuffDataArr[3];
				$qnty_pics 		= $collarAndCuffDataArr[4];
				$needle_per_cm 	= $collarAndCuffDataArr[5];
				if ($kk==0) 
				{
					$data_array4 .="(".$id4.",".$id.",".$dtlsid.",".$txt_order_id.",'".$body_part."','".$grey_size."','".$finish_size."','".$gmts_size."','".$qnty_pics."','".$needle_per_cm."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					 
				}
				else
				{
					$data_array4 .=",(".$id4.",".$id.",".$dtlsid.",".$txt_order_id.",'".$body_part."','".$grey_size."','".$finish_size."','".$gmts_size."','".$qnty_pics."','".$needle_per_cm."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				}
				$kk++;
			}

			 //echo "10** INSERT INTO subcon_ord_collar_and_cuff_dtls (".$field_array4.") VALUES ".$data_array4;die;
			$rID4=sql_insert("subcon_ord_collar_and_cuff_dtls",$field_array4,$data_array4,0);
			if($rID4==1 &&  $flag==1) $flag=1; else $flag=0;
		}	
		//echo $rID1;die;
			 //echo "80** INSERT INTO subcon_ord_collar_and_cuff_dtls (".$field_array4.") VALUES ".$data_array4;die;
			 // echo "60**insert into subcon_delivery_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
			   //echo "70** insert into subcon_delivery_mst (".$field_array.") values ".$data_array;die;

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
		if($db_type==2)
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
		############ Start Check Gate Pass Entry ############
			$sql_insert_id=sql_select("SELECT SYS_NUMBER,ISSUE_ID from inv_gate_pass_mst where issue_id is not null and status_active=1 and is_deleted=0 and company_id=$cbo_company_name and basis=9");
			foreach($sql_insert_id as $iss_id_all)
			{
				$issue_id_arr=explode(",",$iss_id_all['ISSUE_ID']);
				$gate_pass_no=$iss_id_all['SYS_NUMBER'];
				foreach($issue_id_arr as $iss_id)
				{
					if($iss_id!=0)
					{
						$rID2=execute_query("INSERT into tmp_poid (userid, poid, pono) values ($user_id,$iss_id,'$gate_pass_no')");
						//echo $r_id2; die;
					}
				}
			}
			if($db_type==0)
			{
				if($rID2)
				{
					mysql_query("COMMIT");  
				}
			}
			if($db_type==2 || $db_type==1 )
			{
				if($rID2)
				{
					oci_commit($con);  
				}
			}
			$gate_pass = sql_select("SELECT PONO, POID from tmp_poid where poid=$update_id and userid=$user_id");
			if(count($gate_pass)>0)
			{
				$rID3=execute_query("DELETE from tmp_poid where userid=$user_id");
				if($db_type==0)
				{
					if($rID3)
					{
						mysql_query("COMMIT");  
					}
				}
				if($db_type==2 || $db_type==1 )
				{
					if($rID3)
					{
						oci_commit($con);  
					}
				}
				echo "11**Update Not Allow. This System ID Found in Gate Pass Entry ".$gate_pass[0]["PONO"];disconnect($con); die;
			}
			$rID3=execute_query("DELETE from tmp_poid where userid=$user_id");
			if($db_type==0)
			{
				if($rID3)
				{
					mysql_query("COMMIT");  
				}
			}
			if($db_type==2 || $db_type==1 )
			{
				if($rID3)
				{
					oci_commit($con);  
				}
			}
		############ End Check Gate Pass Entry ############
		$field_array="location_id*party_id*challan_no*delivery_date*forwarder*transport_company*vehical_no*driver_name*mobile_no*remark*updated_by*update_date";
		$data_array="".$cbo_location."*".$cbo_party_name."*".$txt_challan_no."*".$txt_delivery_date."*".$cbo_forwarder."*".$txt_transport_company."*".$txt_vehical_no."*".$txt_driver_name."*".$txt_mobile_no."*".$txt_remark."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		//echo "insert into subcon_delivery_mst (".$field_array.") values ".$data_array;die;txt_gsm*txt_dia
		$rID=sql_update("subcon_delivery_mst",$field_array,$data_array,"id",$update_id,0);

		$return_no=$txt_sys_id;
		$field_array_dtls="order_id*process_id*sub_process_id*item_id*gsm*dia*batch_id*color_id*width_dia_type*delivery_qty*carton_roll*remarks*gray_qty*reject_qty*moisture_gain*collar_and_cuff_str";

		$data_array_dtls="".$txt_order_id."*".$cbo_process_name."*".$hid_sub_process."*".$txt_item_id."*".$txt_gsm."*".$txt_dia."*".$txt_batch_id."*".$txt_color_id."*".$hidden_dia_type."*".$txt_delivery_qnty."*".$txt_carton_roll_no."*".$txt_remarks."*".$txt_gray_qnty."*".$txt_reject_qty."*".$txt_moisture_gain."*'".$collarAndCuffStr."'";

		$rID2=sql_update("subcon_delivery_dtls",$field_array_dtls,$data_array_dtls,"id",$update_id_dtls,1);// die;

		$flag=1;
		
		if($collarAndCuffStr !="" )
		{
			$field_array4="id,ord_mst_id,ord_dtls_id,ord_breakdown_id,body_part,grey_size,finish_size,gmts_size,qnty_pics,needle_per_cm,inserted_by,insert_date";
			
			$collarAndCuffArr = explode('$$',$collarAndCuffStr);
			$kk= 0 ;
			$data_array4 = ''; 
			foreach ($collarAndCuffArr as $row) 
			{
				$id6 = return_next_id_by_sequence(  "subcon_ord_collar_and_cuff_dtls_seq", "subcon_ord_collar_and_cuff_dtls", $con );

				$collarAndCuffDataArr = explode('=',$row);
				$body_part 		= $collarAndCuffDataArr[0];
				$grey_size 		= $collarAndCuffDataArr[1];
				$finish_size	= $collarAndCuffDataArr[2];
				$gmts_size 		= $collarAndCuffDataArr[3];
				$qnty_pics 		= $collarAndCuffDataArr[4];
				$needle_per_cm 	= $collarAndCuffDataArr[5];
				if ($kk==0) 
				{
					$data_array4 .="(".$id6.",".$update_id.",".$update_id_dtls.",".$txt_order_id.",'".$body_part."','".$grey_size."','".$finish_size."','".$gmts_size."','".$qnty_pics."','".$needle_per_cm."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					 
				}
				else
				{
					$data_array4 .=",(".$id6.",".$update_id.",".$update_id_dtls.",".$txt_order_id.",'".$body_part."','".$grey_size."','".$finish_size."','".$gmts_size."','".$qnty_pics."','".$needle_per_cm."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				}
				$kk++;
			}
			 //echo "10**INSERT INTO subcon_ord_collar_and_cuff_dtls (".$field_array4.") VALUES ".$data_array4; die;
			$rID6=sql_insert("subcon_ord_collar_and_cuff_dtls",$field_array4,$data_array4,0);
			if($rID6==1 &&  $flag==1) $flag=1; else $flag=0;
			 if($flag ==1)
			{
				$rID7=execute_query( "delete from subcon_ord_collar_and_cuff_dtls where ord_dtls_id=$update_id_dtls",0);
				if($rID7){
					$rID8=sql_insert("subcon_ord_collar_and_cuff_dtls",$field_array4,$data_array4,0);
				}
				if($rID8==1 &&  $flag==1) $flag=1; else $flag=0;
			} 

			
		}

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
		if($db_type==2)
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
		$update_id=str_replace("'","",$update_id);
		$update_id_dtls=str_replace("'","",$update_id_dtls);
		//echo $challan;die;
		if ($challan!='' && $challan!=0)
		{
			$challan_no=$txt_challan_no;
		}
		else
		{
			$challan_no=$update_id;
		}
		
		############ Start Check Gate Pass Entry ############
			$sql_insert_id=sql_select("SELECT SYS_NUMBER,ISSUE_ID from inv_gate_pass_mst where issue_id is not null and status_active=1 and is_deleted=0 and company_id=$cbo_company_name and basis=9");
			foreach($sql_insert_id as $iss_id_all)
			{
				$issue_id_arr=explode(",",$iss_id_all['ISSUE_ID']);
				$gate_pass_no=$iss_id_all['SYS_NUMBER'];
				foreach($issue_id_arr as $iss_id)
				{
					if($iss_id!=0)
					{
						$rID2=execute_query("INSERT into tmp_poid (userid, poid, pono) values ($user_id,$iss_id,'$gate_pass_no')");
						//echo $r_id2; die;
					}
				}
			}
			$rID4 = 1;
			if($collarAndCuffStr !="" )
			{
				$rID4 = sql_delete("subcon_ord_collar_and_cuff_dtls","status_active*is_deleted*updated_by*update_date","0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'",'ord_dtls_id',$update_id_dtls,1);
			}
			if($db_type==0)
			{
				if($rID2)
				{
					mysql_query("COMMIT");  
				}
			}
			if($db_type==2 || $db_type==1 )
			{
				if($rID2)
				{
					oci_commit($con);  
				}
			}
			// $gate_pass = return_field_value("poid","tmp_poid","poid=$update_id and userid=$user_id","poid");
			$gate_pass = sql_select("SELECT PONO, POID from tmp_poid where poid=$update_id and userid=$user_id");
			if(count($gate_pass)>0)
			{
				$rID3=execute_query("DELETE from tmp_poid where userid=$user_id");
				if($db_type==0)
				{
					if($rID3)
					{
						mysql_query("COMMIT");  
					}
				}
				if($db_type==2 || $db_type==1 )
				{
					if($rID3)
					{
						oci_commit($con);  
					}
				}
				echo "11**Delete Not Allow. This System ID Found in Gate Pass Entry ".$gate_pass[0]["PONO"];disconnect($con); die;
			}
			$rID3=execute_query("DELETE from tmp_poid where userid=$user_id");
			if($db_type==0)
			{
				if($rID3)
				{
					mysql_query("COMMIT");  
				}
			}
			if($db_type==2 || $db_type==1 )
			{
				if($rID3)
				{
					oci_commit($con);  
				}
			}
		############ End Check Gate Pass Entry ############

		$bill_info=return_field_value("delivery_id", "subcon_inbound_bill_dtls", "delivery_id=$update_id_dtls","delivery_id");
		if($bill_info!=0 || $bill_info!="")
		{
			echo "13**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_sys_id)."**".str_replace("'",'',$challan_no);
			disconnect($con);
			die;
		}

		//echo $bill_info;die;
		$rID=execute_query( "delete from subcon_delivery_dtls where id=$update_id_dtls",0);
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
		elseif($db_type==2)
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
                        <th width="140">Company</th>
                        <th width="140">Party</th>
                        <th width="110">Delivery ID</th>
                        <th width="80">Year</th>
                        <th width="170">Date Range</th>
                        <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('deliverysearch_1','search_div','','','','');" /></th>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td> <input type="hidden" id="selected_delivery_id"><? //$data=explode("_",$data); ?>  <!--  echo $data;-->
								<? echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $ex_data[0], " load_drop_down( 'subcon_dye_finishing_delivery_controller', this.value, 'load_drop_down_party_popup', 'party_td' );",0); ?>
                            </td>
                            <td id="party_td">
								<?    echo create_drop_down( "cbo_party_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and b.tag_company='$ex_data[0]' and buy.id in (select buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $ex_data[1], "",'' );
 ?>
                            </td>
                            <td>
                                <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes" style="width:95px" />
                            </td>
                            <td>
                                <?
                                    $selected_year=date("Y");
                                    echo create_drop_down( "cbo_year", 60, $year,"", 1, "-Year-", $selected_year, "",0 );
                                ?>
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:65px">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:65px">
                            </td>
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_year').value+'_'+document.getElementById('cbo_party_name').value, 'create_delivery_search_list_view', 'search_div', 'subcon_dye_finishing_delivery_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
                        </tr>
                        <tr>
                            <td colspan="6" align="center" valign="middle">
								<? echo load_month_buttons(1);  ?>
                                <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6" align="center" valign="top" id=""><div id="search_div"></div> </td>
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

if($action=="create_delivery_search_list_view")
{
	$data=explode('_',$data);
	//echo $data[3];
	if ($data[0]!=0) $company=" and company_id='$data[0]'"; else { echo "Please Select Company First."; die; }

	if($db_type==0)
	{
		if ($data[1]!="" &&  $data[2]!="") $delivery_date = "and delivery_date between '".change_date_format($data[1],'yyyy-mm-dd')."' and '".change_date_format($data[2],'yyyy-mm-dd')."'"; else $date_cond= "";
		$year_cond= "year(insert_date)as year";

	}
	else if ($db_type==2)
	{
		if ($data[1]!="" &&  $data[2]!="") $delivery_date = "and delivery_date between '".change_date_format($data[1], "mm-dd-yyyy", "/",1)."' and '".change_date_format($data[2], "mm-dd-yyyy", "/",1)."'"; else $delivery_date ="";
		$year_cond= "TO_CHAR(insert_date,'YYYY') as year";
	}


	if ($data[3]!='') $delivery_id_cond=" and delivery_prefix_num= '$data[3]'"; else $delivery_id_cond="";
	if ($data[5]!=0) $party_id_cond=" and party_id= '$data[5]'"; else $party_id_cond="";
	//$trans_Type="issue";

	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$location_arr=return_library_array( "select id, location_name from  lib_location",'id','location_name');

	?>
    <script>
		$(document).ready(function(e) {
            setFilterGrid('tbl_po_list',-1);
        });

	</script>
    <div>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table">
            <thead>
                <th width="50" >SL</th>
                <th width="70" >Delivery ID</th>
                <th width="60" >Year</th>
                <th width="120" >Party</th>
                <th width="120" >Challan No</th>
                <th width="70" >Delivery Date</th>
                <th>Location</th>
            </thead>
     	</table>
     </div>
     <div style="width:750px; max-height:270px;overflow-y:scroll;" >
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="730" class="rpt_table" id="tbl_po_list">
			<?

			$sql= "select id, delivery_no, company_id, delivery_prefix_num, $year_cond, location_id, party_id, challan_no, delivery_date, forwarder, transport_company from subcon_delivery_mst where process_id=4 and status_active=1 and is_deleted=0 $company $delivery_date $delivery_id_cond $party_id_cond order by id DESC";

			$result = sql_select($sql);
			$i=1;
            foreach($result as $row)
            {
                if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>

                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')]; ?>);" >
						<td width="50" align="center"><?php echo $i; ?></td>
						<td width="70" align="center"><?php echo $row[csf("delivery_prefix_num")]; ?></td>
                        <td width="60" align="center"><?php echo $row[csf("year")]; ?></td>
						<td width="120" align="center"><?php echo $party_arr[$row[csf("party_id")]]; ?></td>
						<td width="120"><?php echo $row[csf("challan_no")]; ?></td>
						<td width="70"><?php echo change_date_format($row[csf("delivery_date")]); ?></td>
						<td ><?php echo $location_arr[$row[csf("location_id")]]; ?> </td>
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
	$nameArray=sql_select( "select id, delivery_no, company_id, location_id, party_id, challan_no, delivery_date, forwarder, transport_company,vehical_no,driver_name,mobile_no,remark from subcon_delivery_mst where id='$data' and status_active=1 and is_deleted=0 " );
	foreach ($nameArray as $row)
	{
		echo "document.getElementById('txt_sys_id').value 				= '".$row[csf("delivery_no")]."';\n";
		echo "document.getElementById('cbo_company_name').value 		= '".$row[csf("company_id")]."';\n";
		echo "load_drop_down( 'requires/subcon_dye_finishing_delivery_controller', $('#cbo_company_name').val(), 'load_drop_down_location', 'location_td' );";
		echo "document.getElementById('cbo_location').value				= '".$row[csf("location_id")]."';\n";
		echo "load_drop_down( 'requires/subcon_dye_finishing_delivery_controller', $('#cbo_company_name').val(), 'load_drop_down_party_name', 'party_td' );";

		echo "document.getElementById('cbo_party_name').value			= '".$row[csf("party_id")]."';\n";
		echo "$('#cbo_party_name').attr('disabled','true')".";\n";
		echo "document.getElementById('txt_challan_no').value			= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('txt_delivery_date').value		= '".change_date_format($row[csf("delivery_date")])."';\n";
		echo "document.getElementById('txt_transport_company').value 	= '".trim($row[csf("transport_company")])."';\n";
		echo "document.getElementById('cbo_forwarder').value			= '".$row[csf("forwarder")]."';\n";
		echo "document.getElementById('txt_vehical_no').value			= '".$row[csf("vehical_no")]."';\n";
		echo "document.getElementById('txt_driver_name').value			= '".$row[csf("driver_name")]."';\n";
		echo "document.getElementById('txt_mobile_no').value			= '".$row[csf("mobile_no")]."';\n";
		echo "document.getElementById('txt_remark').value				= '".$row[csf("remark")]."';\n";
		echo "document.getElementById('update_id').value				= '".$row[csf("id")]."';\n";
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
	$reportType=$ex_data[4];
	$divHeight = $reportType == 4 ? '940px' : '1010px';
	//print_r ($data);
//	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library",'master_tble_id','image_location');
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_library=return_library_array( "select id,supplier_name from lib_supplier", "id","supplier_name");
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$country_arr=return_library_array( "select id, country_name from lib_country", "id", "country_name");
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");//die;
	//$item_arr=return_library_array( "select cons_comp_id, color_name from lib_color", "cons_comp_id", "color_name");
	//$color_arr=return_library_array( "select id, color_name from  lib_color", "id", "color_name"  );
	

	$sql="select id, party_id, challan_no, delivery_date, forwarder, transport_company,delivery_no,inserted_by,vehical_no,driver_name,mobile_no from subcon_delivery_mst where id='$update_id' and company_id='$company' and status_active=1 and is_deleted=0";

	$dataArray=sql_select($sql);
	$inserted_by=$dataArray[0][csf('inserted_by')];
	$com_dtls = fnc_company_location_address($company, $location, 2);


	$mst_id=$dataArray[0][csf('id')];
	$sql_dtls="select batch_id, color_id, width_dia_type, dia, order_id, sub_process_id, item_id,sum(reject_qty) as reject_qty, sum(delivery_qty) as delivery_qty, sum(gray_qty) as gray_qty, sum(carton_roll) as carton_roll, gsm, remarks from subcon_delivery_dtls where mst_id='$mst_id' and process_id in (3,4) group by batch_id, color_id, width_dia_type, gsm, dia, order_id, sub_process_id, item_id, remarks, id order by batch_id, sub_process_id, color_id";
	$dtls_value=sql_select($sql_dtls);
	$po_arr=array();
	$batch_id_arr=array();
	foreach($dtls_value as $row){
		$batch_id_arr[$row[csf('batch_id')]]=$row[csf('batch_id')];
		array_push($po_arr, $row[csf('order_id')]);
	}

	$po_arr=array_filter(array_unique($po_arr));
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id."   and ENTRY_FORM=72");
	oci_commit($con);
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 72, 1, $po_arr, $empty_arr);//PO ID Ref from=1

	


	//$job_po_cond=where_con_using_array($po_arr,0,"a.id");
	//$order_id_cond=where_con_using_array($po_arr,0,"b.order_id");

	$sql_job_po="select a.id, a.order_no, a.main_process_id, a.cust_buyer, a.cust_style_ref, b.party_id, b.subcon_job, a.process_id from  subcon_ord_dtls a, subcon_ord_mst b,gbl_temp_engine g where a.job_no_mst=b.subcon_job and  a.id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=72 and b.is_deleted=0 and b.status_active=1 and a.status_active=1 and a.is_deleted=0 $job_po_cond";
	$job_po_array=array();
	$result_job_po=sql_select($sql_job_po);
	foreach($result_job_po as $row)
	{
		$job_po_array[$row[csf('id')]]['order_no']=$row[csf('order_no')];
		$job_po_array[$row[csf('id')]]['main_process_id']=$row[csf('main_process_id')];
		$job_po_array[$row[csf('id')]]['process_id']=$row[csf('process_id')];
		$job_po_array[$row[csf('id')]]['party_id']=$row[csf('party_id')];
		$job_po_array[$row[csf('id')]]['subcon_job']=$row[csf('subcon_job')];
		$job_po_array[$row[csf('id')]]['cust_buyer']=$row[csf('cust_buyer')];
		$job_po_array[$row[csf('id')]]['cust_style_ref']=$row[csf('cust_style_ref')];
	}
	//var_dump($job_po_array);
	$recChallan_arr=array();
	if($db_type==0)
	{
		$sql_rec="select b.order_id, group_concat(distinct(a.chalan_no)) as chalan_no, group_concat(distinct(b.grey_dia)) as grey_dia from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=1 $order_id_cond group by b.order_id";
	}
	else if ($db_type==2)
	{
		$sql_rec="select b.order_id, wm_concat(distinct(cast(a.chalan_no as varchar2(500)))) as chalan_no, wm_concat(distinct(cast(b.grey_dia as varchar2(500)))) as grey_dia from sub_material_mst a, sub_material_dtls b,gbl_temp_engine g where a.id=b.mst_id  and  b.order_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=72 and a.trans_type=1  group by b.order_id";
	}
	$result_sql_rec=sql_select($sql_rec);
	foreach($result_sql_rec as $row)
	{
		$recChallan_arr[$row[csf('order_id')]]['chalan_no']=$row[csf('chalan_no')];
		$recChallan_arr[$row[csf('order_id')]]['grey_dia']=$row[csf('grey_dia')];
	}
?>
    <div style="width:930px;">
   <table width="100%" cellpadding="0" cellspacing="0" >
       <tr>
           <td width="200" align="right">
               <img  src='../../<? echo $com_dtls[2]; ?>' height='60%' width='60%' />
           </td>
           <td>
    <table width="800" cellspacing="0" align="center">
        <tr>
            <td align="center" style="font-size:22px"><strong ><? echo $com_dtls[0]; ?></strong></td>
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
                        <? echo $result[csf('level_no')]?> &nbsp;
                        <? echo $result[csf('road_no')]; ?> &nbsp;
                        <? echo $result[csf('block_no')];?> &nbsp;
                        <? echo $result[csf('city')];?> &nbsp;
                        <? echo $result[csf('zip_code')]; ?> &nbsp;
                        <? echo $result[csf('province')];?> &nbsp;
                        <? echo $country_arr[$result[csf('country_id')]]; ?>&nbsp; <br>
                        <? echo $result[csf('contact_no')];?> &nbsp;
                        <? echo $result[csf('email')];?> &nbsp;
                        <? echo $result[csf('website')]; ?> <br>
                        <b> Vat No : <? echo $result[csf('vat_number')]; ?></b> <?
					}*/
                ?>
            </td>
        </tr>
        <tr>
            <td align="center" style="font-size:18px"><strong><? echo $production_process[$job_po_array[$dataArray[0][csf('order_id')]]['main_process_id']]; ?> Delivery Challan</strong></td>
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
			//	echo "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$party_add";
                $nameArray=sql_select( "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$party_add");
				 $address="";
                foreach ($nameArray as $result)
                {

                    if($result[csf('address_1')]!="") $address=$result[csf('address_1')];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
                }
            ?>
        	<td width="300" rowspan="4" valign="top" colspan="2" style="font-size:14px"><strong>Party : <? echo $buyer_library[$party_add].'<br>'.'Party Address: '.$address;  ?></strong></td>
            <td width="125" style="font-size:14px"><strong>Challan No :</strong></td><td width="170px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
            <td width="125" style="font-size:14px"><strong>Delivery Date :</strong></td><td width="170px"><? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
        </tr>
        <tr>
            <td style="font-size:14px"><strong>Transport Com.:</strong></td><td><? echo $dataArray[0][csf('transport_company')]; ?></td>
            <td style="font-size:14px"><strong>Forwarder:</strong></td><td><? echo $supplier_library[$dataArray[0][csf('forwarder')]]; ?></td>
        </tr>
        <tr>
		<td style="font-size:14px"><strong>Vehicle No:</strong></td><td><? echo $dataArray[0][csf('vehical_no')]; ?></td>
			<td colspan="4"></td> </tr>
    </table>

    <div style="width:120%; height: <?php echo $divHeight; ?>">
			<?
			$gray_dia_array=array(); $prod_dia_array=array();
			/*
			if ($db_type==0)
			{
				$prod_dia_sql="select a.batch_id, a.cons_comp_id, a.process, a.dia_width from subcon_production_dtls a, subcon_production_qnty b where a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 group by a.batch_id, a.cons_comp_id, a.process ";
			}
			else if ($db_type==2)
			{
				$prod_dia_sql="select a.batch_id, a.cons_comp_id, cast(a.process as varchar2(100)) as process, a.dia_width from subcon_production_dtls a, subcon_production_qnty b where a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 group by a.batch_id, a.cons_comp_id, a.dia_width, a.process";
			}
			$result_prod_dia_sql=sql_select($prod_dia_sql);
			foreach($result_prod_dia_sql as $row)
			{
				$prod_dia_array[$row[csf('batch_id')]][$row[csf('cons_comp_id')]]['dia_width']=$row[csf('dia_width')];
				$prod_dia_array[$row[csf('batch_id')]][$row[csf('cons_comp_id')]]['process']=$row[csf('process')];
			}
			*/
			//print_r($prod_dia_array);
			//$inv_item_arr=return_library_array( "select id,material_description from sub_material_dtls",'id','material_description');
			//$prod_item_arr=return_library_array( "select id,fabric_description from subcon_production_dtls",'id','fabric_description');




			$i=1; $k=1; $width_dia_type_array=array(); $sub_process_array=array(); $batch_array=array(); $color_array=array();

			

			$batch_id=implode(",",$batch_id_arr);
			?>
			<table style="margin-left:20px;"  align="left" cellspacing="0" width="1080"  border="1" rules="all" class="rpt_table" >
				<thead bgcolor="#dddddd" align="center" style="font-size:14px">
					<th width="20">SL</th>
					<th width="70" align="center">Order No</th>
					<th width="70" align="center">Job No</th>
					<th width="60" align="center">Cust. Buyer</th>
                    <th width="60" align="center">Cust. Style</th>
					<th width="70" align="center">Rec. Challan</th>
					<th width="110" align="center">Description</th>
					<th width="50" align="center">GSM</th>
					<th width="50" align="center">G/Dia</th>
					<th width="50" align="center">F/Dia</th>
					<th width="50" align="center">Roll</th>
					<th width="70" align="center">Grey Qty</th>
					<th width="70" align="center">Fin. Qty</th>
                    <th width="70" align="center">Rej. Qty</th>
					<th width="70" align="center">Process %</th>
					<th align="center">Remarks</th>
				</thead>
				<?
					//$order_id_cond=where_con_using_array($po_arr,0,"b.po_id");
					// $sql_batch="Select a.id, a.batch_no, a.extention_no, b.fabric_from, b.po_id, b.id as item_id, b.width_dia_type, b.item_description, sum(b.roll_no) as roll_no, sum(b.batch_qnty) as batch_qnty, b.rec_challan from  pro_batch_create_mst a, pro_batch_create_dtls b where a.entry_form=36 and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id in ($batch_id) $order_id_cond group by a.id, a.batch_no, a.extention_no, a.process_id, b.fabric_from, b.item_description, b.po_id, b.id, b.width_dia_type, b.rec_challan";
					$sql_batch="Select a.id, a.batch_no, a.extention_no, b.fabric_from, b.po_id, b.id as item_id, b.width_dia_type, b.item_description, sum(b.roll_no) as roll_no, sum(b.batch_qnty) as batch_qnty, b.rec_challan from  pro_batch_create_mst a, pro_batch_create_dtls b,gbl_temp_engine g where a.entry_form=36 and a.id=b.mst_id and b.po_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=72  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id in ($batch_id)  group by a.id, a.batch_no, a.extention_no, a.process_id, b.fabric_from, b.item_description, b.po_id, b.id, b.width_dia_type, b.rec_challan";
					$batch_full_array=array();
					$result_batch=sql_select($sql_batch);
					foreach($result_batch as $row)
					{
						//$batch_array[$row[csf('po_id')]]['id']=$row[csf('id')];  *batch_no*batch_ext*color_id
						$item=explode(',',$row[csf('item_description')]);
						$batch_full_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('item_id')]]['item_description']=$row[csf('item_description')];
						$batch_full_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('item_id')]]['rec_challan']=$row[csf('rec_challan')];
						$batch_full_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('item_id')]]['dia']=$item[2];
						$batch_full_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('item_id')]]['roll_no']=$row[csf('roll_no')];
						$batch_full_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('item_id')]]['batch_qnty']=$row[csf('batch_qnty')];
						$batch_full_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('item_id')]]['fabric_from']=$row[csf('fabric_from')];
						$batch_full_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('item_id')]]['width_dia_type']=$row[csf('width_dia_type')];
						$batch_full_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('item_id')]]['batch_no']=$row[csf('batch_no')];
						$batch_full_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('item_id')]]['extention_no']=$row[csf('extention_no')];
					}

				   /*$sql_fin_dia=sql_select("select order_id,dia_width from subcon_production_dtls where status_active=1 and is_deleted=0");
				   foreach($sql_fin_dia as $val)
				   {
				   	$fin_dia_arr[$val[csf("order_id")]]=$val[csf("dia_width")];

				   }*/
				//    $order_id_cond=where_con_using_array($po_arr,0,"order_id");
				//    $sql_fin_dia=sql_select("select order_id,batch_id,cons_comp_id, dia_width,fabric_description from subcon_production_dtls where status_active=1 and is_deleted=0 and batch_id in ($batch_id)  $order_id_cond");
				$sql_fin_dia=sql_select("select b.order_id,b.batch_id,b.cons_comp_id, b.dia_width,b.fabric_description from subcon_production_dtls b ,gbl_temp_engine g where b.order_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=72  and b.status_active=1 and b.is_deleted=0 and b.batch_id in ($batch_id)  ");
				   foreach($sql_fin_dia as $val)
				   {
				  //	$fin_dia_arr[$val[csf("order_id")]][$val[csf("cons_comp_id")]]=$val[csf("dia_width")];
				 	if($val[csf("dia_width")]!="")
				  	{
				//	$fin_dia_arr[$val[csf("order_id")]][$val[csf("fabric_description")]]=$val[csf("dia_width")];
					}
				  }

				  //	$order_id_cond=where_con_using_array($po_arr,0,"po_id");
				    // $sql_grey_dia=sql_select("select id, po_id, grey_dia,fin_dia from pro_batch_create_dtls where 1=1 $order_id_cond");
					$sql_grey_dia=sql_select("select b.id, b.po_id, b.grey_dia,b.fin_dia from pro_batch_create_dtls b,gbl_temp_engine g  where b.order_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=72 and 1=1 ");
				   foreach($sql_grey_dia as $val)
				   {
				   	$grey_dia_arr[$val[csf("po_id")]][$val[csf("id")]]=$val[csf("grey_dia")];
					$fin_dia_arr[$val[csf("po_id")]][$val[csf("id")]]=$val[csf("fin_dia")];

				   }

				  // $order_id_cond=where_con_using_array($po_arr,0,"order_id");

				//    $sql_grey_qty=sql_select("select order_id,process_loss from subcon_ord_breakdown where 1=1 $order_id_cond");
				$sql_grey_qty=sql_select("select b.order_id,b.process_loss from subcon_ord_breakdown b,gbl_temp_engine g  where b.order_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=72 and 1=1 ");
				   foreach($sql_grey_qty as $val)
				   {
				   	$grey_arr[$val[csf("order_id")]]=$val[csf("process_loss")];

				   }
				   
				   execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id."   and ENTRY_FORM=72");
				   oci_commit($con);
				   disconnect($con);
					foreach($dtls_value as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$item_name=$batch_full_array[$row[csf('batch_id')]][$row[csf('po_id')]][$row[csf('item_id')]]['item_description'];
						$process_id=explode(',',$row[csf('sub_process_id')]);

						$process_val='';
						foreach ($process_id as $val)
						{
							//rsort($val);
							if($process_val=='') $process_val=$conversion_cost_head_array[$val]; else $process_val.="+".$conversion_cost_head_array[$val];
							//echo $val;
						}

						if ($batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['batch_no']!='' || $batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['extention_no']!='')
						{
							$batch_no=$batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['batch_no'].' '." Ex: ". $batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['extention_no'];
						}
						else
						{
							$batch_no=$batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['batch_no'];
						}
						$gsm_dia=explode(',',$batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['item_description']);
						if ($gsm_dia[1]!='' && $gsm_dia[1]!=0 ) $batch_dia=$gsm_dia[2]; else $batch_dia=$gsm_dia[2];

						$chack_string=$row[csf('batch_id')].$row[csf('color_id')].$row[csf('sub_process_id')];
						if(!in_array($chack_string,$sub_process_array))
						{
							if($i!=1)
							{
								$subProcessLoss=(($tot_grey_qty-$tot_finish_qty)/$tot_grey_qty)*100;
							?>
							<tr class="tbl_bottom" bgcolor="#CCCCCC" style="font-size:14px">
								<th width="20">&nbsp;</th>
								<th width="70">&nbsp;</th>
								<th width="70">&nbsp;</th>
								<th width="60">&nbsp;</th>
                                <th width="60">&nbsp;</th>
								<th width="70">&nbsp;</th>
								<th width="110">&nbsp;</th>
								<th width="50">&nbsp;</th>
								<th width="50">&nbsp;</th>
								<th width="50"><strong>Total</strong></th>
								<th width="50" align="right"><? echo number_format($tot_roll,2,'.',''); ?>&nbsp;</th>
								<th width="70" align="right"><? echo number_format($tot_grey_qty,2,'.',''); ?>&nbsp;</th>
								<th width="70" align="right"><? echo number_format($tot_finish_qty,2,'.',''); ?>&nbsp;</th>
                                <th width="70" align="right"><? echo $tot_rej_qty; ?>&nbsp;</th>
								<th width="70" align="right" title="<?='(('.$tot_grey_qty.'-'.$tot_finish_qty.')/'.$tot_grey_qty.')*100'; ?>"><? echo number_format($subProcessLoss,2); ?>&nbsp;</th>
								<th>&nbsp;</th>
							</tr>
						<?
								unset($tot_roll);
								unset($tot_grey_qty);
								unset($tot_finish_qty);
								unset($tot_rej_qty);
								//unset($tot_proces_loss);
							}
						?>
							<tr height="30"><td colspan="16" style="font-size:15px" bgcolor="#CCCCAA"><p><?php echo "<i>Batch No: </i>" . $batch_no;
								echo "; <i>Color: </i>" . $color_arr[$row[csf('color_id')]];
								echo "; <i>Process: </i>" . $process_val . ""; ?></p></td></tr>
						<?
							$sub_process_array[$i]=$chack_string;
							//unset($sub_process_array);
						}
						$dia_type='';
						if($batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['width_dia_type']==1) $dia_type="Open";
						else if($batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['width_dia_type']==2) $dia_type="Tube";
						else if($batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['width_dia_type']==3) $dia_type="Niddle";
						
						$item_description=$batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['item_description'];
						$fin_dia=$fin_dia_arr[$row[csf("order_id")]][$item_description];
						$fabric_desciption="";
						if($gsm_dia[1]!="") $fabric_desciption=$gsm_dia[0].','.$gsm_dia[1].'('.$dia_type.')'; else $fabric_desciption=$gsm_dia[0].'('.$dia_type.')';
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:14px">
						<td width="20"><? echo $i; ?></td>
						<td width="70" style="word-break:break-all"><? echo $job_po_array[$row[csf('order_id')]]['order_no']; ?></td>
						<td width="70" style="word-break:break-all"><? echo $job_po_array[$row[csf('order_id')]]['subcon_job']; ?></td>
						<td width="60" style="word-break:break-all"><? echo $job_po_array[$row[csf('order_id')]]['cust_buyer']; ?></td>
                        <td width="60" style="word-break:break-all"><? echo $job_po_array[$row[csf('order_id')]]['cust_style_ref']; ?></td>
						<td width="70" style="word-break:break-all"><? echo $batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['rec_challan']; ?></td>
						<td width="110" style="word-break:break-all"><? echo $fabric_desciption; ?></td>
						<td width="50" align="center" style="word-break:break-all"><? echo $row[csf('gsm')]; ?></td>
						<td width="50" align="center" style="word-break:break-all"><? echo $grey_dia_arr[$row[csf("order_id")]][$row[csf("item_id")]];  ?></td>
						<td width="50" align="center" style="word-break:break-all"><? echo $row[csf("dia")]; ?></td>
						<td width="50" align="right"><? echo $row[csf('carton_roll')]; ?>&nbsp;</td>

						<td width="70" align="right" style="word-break:break-all"><? echo number_format($row[csf('gray_qty')],2,'.',''); ?>&nbsp;</td>
						<td width="70" align="right" style="word-break:break-all"><? echo number_format($row[csf('delivery_qty')],2,'.',''); ?>&nbsp;</td>
                        <td width="70" align="right" style="word-break:break-all"><? echo $row[csf('reject_qty')]; ?>&nbsp;</td>
						<td width="70" align="right" style="word-break:break-all" title="<?='(('.$row[csf('gray_qty')].'-'.$row[csf('delivery_qty')].')/'.$row[csf('gray_qty')].')*100'; ?>">
								<?
								$proces_loss=(($row[csf('gray_qty')]-$row[csf('delivery_qty')])/$row[csf('gray_qty')])*100;
								echo number_format($proces_loss,2,'.','');
								?>
								&nbsp;
						</td>
						<td style="word-break:break-all"><? echo $row[csf('remarks')]; ?></td>
					</tr>
					<?
					$tot_roll+=$row[csf('carton_roll')];
					$tot_grey_qty+=$row[csf('gray_qty')];
					$tot_finish_qty+=$row[csf('delivery_qty')];


					$tot_rej_qty+=$row[csf('reject_qty')];
					$grand_tot_reject_qty+=$row[csf('reject_qty')];

					$grand_tot_roll+=$row[csf('carton_roll')];
					$grand_tot_grey_qty+=str_replace(",", "",number_format($row[csf('gray_qty')]));
					$grand_tot_finish_qty+=str_replace(",", "",number_format($row[csf('delivery_qty')]));

					//$tot_proces_loss+=(($tot_grey_qty-$tot_finish_qty)/$tot_grey_qty)*100;
					$i++;
				}
				?>
					<tr class="tbl_bottom" bgcolor="#CCCCCC" style="font-size:14px">
						<th width="20">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="110">&nbsp;</th>
						<th width="50">&nbsp;</th>
						<th width="50">&nbsp;</th>
						<th width="50"><strong>Total</strong></th>
						<th width="50" align="right"><? echo number_format($tot_roll,2,'.',''); ?>&nbsp;</th>
						<th width="70" align="right"><? echo number_format($tot_grey_qty,2,'.',''); ?>&nbsp;</th>
						<th width="70" align="right"><? echo number_format($tot_finish_qty,2,'.',''); ?>&nbsp;</th>
                        <th width="70" align="right"><? echo $tot_rej_qty; ?>&nbsp;</th>
						<th width="70" align="right" title="<?='(('.$tot_grey_qty.'-'.$tot_finish_qty.')/'.$tot_grey_qty.')*100'; ?>">
							<?
							$subProcessLoss=(($tot_grey_qty-$tot_finish_qty)/$tot_grey_qty)*100;
								//$tot_proces_loss=(($tot_grey_qty-$tot_finish_qty)/$tot_grey_qty)*100;
								echo number_format($subProcessLoss,2,'.','');
							?>
							&nbsp;
						</th>
						<th>&nbsp;</th>
					</tr>
					<tfoot style="font-size:14px">
						<th width="20">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="110">&nbsp;</th>
						<th width="50">&nbsp;</th>
						<th width="50">&nbsp;</th>
						<th width="50"><strong>Grand Total</strong></th>
						<th width="50" align="right"><? echo number_format($grand_tot_roll,2,'.',''); ?>&nbsp;</th>
						<th width="70" align="right"><? echo number_format($grand_tot_grey_qty,2,'.',''); ?>&nbsp;</th>
						<th width="70" align="right"><? echo number_format($grand_tot_finish_qty,2,'.',''); ?>&nbsp;</th>
                        <th width="70" align="right"><? echo $grand_tot_reject_qty; ?>&nbsp;</th>
						<th width="70" align="right" title="<?='(('.$grand_tot_grey_qty.'-'.$grand_tot_finish_qty.')/'.$grand_tot_grey_qty.')*100'; ?>">
							<?
								$grand_tot_proces_loss=(($grand_tot_grey_qty-$grand_tot_finish_qty)/$grand_tot_grey_qty)*100;
								echo number_format($grand_tot_proces_loss,2,'.','');
							?>
							&nbsp;
						</th>
						<th>&nbsp;</th>
					</tfoot>
				</table>

				 <?
            //echo signature_table(46, $company, "900px");
				 //echo signature_table(46, $company, "900px",$cbo_template_id,70,$user_lib_name[$inserted_by]);
	if($reportType == 1 || $reportType == 4 || $reportType == 5){
         ?>
          </div>
    	<table width="900" cellspacing="0" >
        	<tr><td colspan="6">

            </td></tr>
            <tr><td colspan="6" align="center">,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,</td></tr>
            <tr>
				<td colspan="6">
                    <table cellpadding="0" cellspacing="0" >
                        <tr>
                            <td width="200" align="right">
                                <img  src='../../<? echo $imge_arr[str_replace("'","",$company)]; ?>' height='60%' width='60%' />
                            </td>
                            <td align="right">
                                <table width="800px" cellspacing="0" align="center">
                                    <tr>
                                        <td align="center" style="font-size:18px"><strong ><? echo $company_library[$company]; ?></strong></td>
                                    </tr>
                                    <tr class="form_caption">
                                        <td  align="center" style="font-size:14px">
                                        <?
											$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, contact_no, country_id, province, city, zip_code, contact_no, email, website, vat_number from lib_company where id=$company and status_active=1 and is_deleted=0");
											foreach ($nameArray as $result)
											{
												echo $result[csf('plot_no')]; ?> &nbsp;
												<? echo $result[csf('level_no')]?> &nbsp;
												<? echo $result[csf('road_no')]; ?> &nbsp;
												<? echo $result[csf('block_no')];?> &nbsp;
												<? echo $result[csf('city')];?> &nbsp;
												<? echo $result[csf('zip_code')]; ?> &nbsp;
												<? echo $result[csf('province')];?> &nbsp;
												<? echo $country_arr[$result[csf('country_id')]]; ?>&nbsp; <br>
												<? echo $result[csf('contact_no')];?> &nbsp;
												<? echo $result[csf('email')];?> &nbsp;
												<? echo $result[csf('website')]; ?> <br>
                                                <b> Vat No : <? echo $result[csf('vat_number')]; ?></b> <?
											}
                                        ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center" style="font-size:16px"><strong><u>Gate Pass</u></strong></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
    			</td>
            </tr>
            <tr>
                <?
                $party_add=$dataArray[0][csf('party_id')];
                $nameArray=sql_select( "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$party_add");
                foreach ($nameArray as $result)
                {
                    $address="";
                    if($result[csf('address_1')]!="") $address=$result[csf('address_1')];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
                }
                ?>
                <td width="300" rowspan="4" valign="top" colspan="2" style="font-size:14px"><strong>Party : <? echo $buyer_library[$party_add].'<br>'.'Party Address: '.$address;  ?></strong></td>
                <td width="120" style="font-size:14px"><strong>Challan No :</strong></td><td width="170px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
                <td width="120" style="font-size:14px"><strong>Delivery Date :</strong></td><td width="170px"><? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
            </tr>
            <tr>
                <td style="font-size:14px"><strong>Transport Com.:</strong></td><td><? echo $dataArray[0][csf('transport_company')]; ?></td>
                <td style="font-size:14px"><strong>Forwarder:</strong></td><td><? echo $supplier_library[$dataArray[0][csf('forwarder')]]; ?></td>
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
        &nbsp;
        <?php 
        	echo signature_table(46, $company, "900px",$cbo_template_id,70,$user_lib_name[$inserted_by]);
        ?>
	</div>
	<?
    }
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
	$reportType=$ex_data[4];
	//$divHeight = $reportType == 4 ? '940px' : '1010px';
	//print_r ($data);
	//$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library",'master_tble_id','image_location');
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_library=return_library_array( "select id,supplier_name from lib_supplier", "id","supplier_name");
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$country_arr=return_library_array( "select id, country_name from lib_country", "id", "country_name");
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");//die;
	//$item_arr=return_library_array( "select cons_comp_id, color_name from lib_color", "cons_comp_id", "color_name");
	//$color_arr=return_library_array( "select id, color_name from  lib_color", "id", "color_name"  );
	

	$sql="select id, party_id, challan_no, delivery_date, forwarder, transport_company,delivery_no,vehical_no,remark,driver_name,mobile_no from subcon_delivery_mst where id='$update_id' and company_id='$company' and status_active=1 and is_deleted=0";
	$dataArray=sql_select($sql);
	$mst_id=$dataArray[0][csf('id')];

	$sql_job_po="select a.id, a.order_no, a.main_process_id, a.cust_buyer, a.cust_style_ref, b.party_id, b.subcon_job, a.process_id from  subcon_ord_dtls a, subcon_ord_mst b,subcon_delivery_dtls c where a.job_no_mst=b.subcon_job and a.id=c.order_id and c.mst_id=$mst_id and b.is_deleted=0 and b.status_active=1 and a.status_active=1 and a.is_deleted=0";
	$job_po_array=array();
	$result_job_po=sql_select($sql_job_po);
	foreach($result_job_po as $row)
	{
		$job_po_array[$row[csf('id')]]['order_no']=$row[csf('order_no')];
		$job_po_array[$row[csf('id')]]['main_process_id']=$row[csf('main_process_id')];
		$job_po_array[$row[csf('id')]]['process_id']=$row[csf('process_id')];
		$job_po_array[$row[csf('id')]]['party_id']=$row[csf('party_id')];
		$job_po_array[$row[csf('id')]]['subcon_job']=$row[csf('subcon_job')];
		$job_po_array[$row[csf('id')]]['cust_buyer']=$row[csf('cust_buyer')];
		$job_po_array[$row[csf('id')]]['cust_style_ref']=$row[csf('cust_style_ref')];
		$PoIdArr[$row[csf('id')]]=$row[csf('id')];
	}
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id."   and ENTRY_FORM=72");
	oci_commit($con);
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 72, 1, $PoIdArr, $empty_arr);//PO ID Ref from=1

	//var_dump($job_po_array);
	// $recChallan_arr=array();
	// if($db_type==0)
	// {
	// 	$sql_rec="select b.order_id, group_concat(distinct(a.chalan_no)) as chalan_no, group_concat(distinct(b.grey_dia)) as grey_dia from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=1 group by b.order_id";
	// }
	// else if ($db_type==2)
	// {
	// 	$sql_rec="select b.order_id, wm_concat(distinct(cast(a.chalan_no as varchar2(500)))) as chalan_no, wm_concat(distinct(cast(b.grey_dia as varchar2(500)))) as grey_dia from sub_material_mst a, sub_material_dtls b,subcon_delivery_dtls c where a.id=b.mst_id and b.order_id=c.order_id and c.mst_id=$mst_id   and a.trans_type=1 group by b.order_id";
	// }
	// $result_sql_rec=sql_select($sql_rec);
	// foreach($result_sql_rec as $row)
	// {
	// 	$recChallan_arr[$row[csf('order_id')]]['chalan_no']=$row[csf('chalan_no')];
	// 	$recChallan_arr[$row[csf('order_id')]]['grey_dia']=$row[csf('grey_dia')];
	// }

	
	$com_dtls = fnc_company_location_address($company, $location, 2);
	?>
    <div style="width:930px;">
   <table width="100%" cellpadding="0" cellspacing="0" >
       <tr>
           <td width="200" align="right">
               <img  src='../../<? echo $com_dtls[2]; ?>' height='60%' width='60%' />
           </td>
           <td>
    <table width="800" cellspacing="0" align="center">
        <tr>
            <td align="center" style="font-size:22px"><strong ><? echo $com_dtls[0]; ?></strong></td>
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
                        <? echo $result[csf('level_no')]?> &nbsp;
                        <? echo $result[csf('road_no')]; ?> &nbsp;
                        <? echo $result[csf('block_no')];?> &nbsp;
                        <? echo $result[csf('city')];?> &nbsp;
                        <? echo $result[csf('zip_code')]; ?> &nbsp;
                        <? echo $result[csf('province')];?> &nbsp;
                        <? echo $country_arr[$result[csf('country_id')]]; ?>&nbsp; <br>
                        <? echo $result[csf('contact_no')];?> &nbsp;
                        <? echo $result[csf('email')];?> &nbsp;
                        <? echo $result[csf('website')]; ?> <br>
                        <b> Vat No : <? echo $result[csf('vat_number')]; ?></b> <?
					}*/
                ?>
            </td>
        </tr>
        <tr>
            <td align="center" style="font-size:18px"><strong><? echo $production_process[$job_po_array[$dataArray[0][csf('order_id')]]['main_process_id']]; ?> Delivery Challan</strong></td>
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
			//	echo "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$party_add";
                $nameArray=sql_select( "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$party_add");
				 $address="";
                foreach ($nameArray as $result)
                {

                    if($result[csf('address_1')]!="") $address=$result[csf('address_1')];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
                }
            ?>
        	<td width="300" rowspan="4" valign="top" colspan="2" rowspan="2"style="font-size:14px"><strong>Party : <? echo $buyer_library[$party_add].'<br>'.'Party Address: '.$address;  ?></strong></td>
            <td width="125" style="font-size:14px"><strong>Challan No :</strong></td><td width="170px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
            <td width="125" style="font-size:14px"><strong>Delivery Date :</strong></td><td width="170px"><? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
        </tr>
        <tr>
            <td style="font-size:14px"><strong>Transport Com.:</strong></td><td><? echo $dataArray[0][csf('transport_company')]; ?></td>
            <td style="font-size:14px"><strong>Forwarder:</strong></td><td><? echo $supplier_library[$dataArray[0][csf('forwarder')]]; ?></td>
        </tr>
		<tr><td colspan="6"></td> </tr>
		<tr><td colspan="6"></td> </tr>
		<tr>
            <td style="font-size:14px"><strong>Vehicle No:</strong></td><td><? echo $dataArray[0][csf('vehical_no')]; ?></td>
            <td style="font-size:14px"><strong>Driver Name:</strong></td><td><? echo $dataArray[0][csf('driver_name')]; ?></td>
			<td style="font-size:14px"><strong>Mobile No.:</strong></td><td><? echo $dataArray[0][csf('mobile_no')]; ?></td>
        </tr>
		<tr>
		<td style="font-size:14px"><strong>Remarks:</strong></td><td  colspan="5"><? echo $dataArray[0][csf('remark')]; ?></td>
		</tr>
        
    </table>

    <div style="width:120%; height: <?php echo $divHeight; ?>">
			<?
			// $gray_dia_array=array(); $prod_dia_array=array();
			// if ($db_type==0)
			// {
			// 	$prod_dia_sql="select a.batch_id, a.cons_comp_id, a.process, a.dia_width from subcon_production_dtls a, subcon_production_qnty b where a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 group by a.batch_id, a.cons_comp_id, a.process ";
			// }
			// else if ($db_type==2)
			// {
			// 	$prod_dia_sql="select a.batch_id, a.cons_comp_id, cast(a.process as varchar2(100)) as process, a.dia_width from subcon_production_dtls a, subcon_production_qnty b where a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 group by a.batch_id, a.cons_comp_id, a.dia_width, a.process";
			// }
			// $result_prod_dia_sql=sql_select($prod_dia_sql);
			// foreach($result_prod_dia_sql as $row)
			// {
			// 	$prod_dia_array[$row[csf('batch_id')]][$row[csf('cons_comp_id')]]['dia_width']=$row[csf('dia_width')];
			// 	$prod_dia_array[$row[csf('batch_id')]][$row[csf('cons_comp_id')]]['process']=$row[csf('process')];
			// }
			//print_r($prod_dia_array);
			//$inv_item_arr=return_library_array( "select id,material_description from sub_material_dtls",'id','material_description');
			//$prod_item_arr=return_library_array( "select id,fabric_description from subcon_production_dtls",'id','fabric_description');


			
			 $sql_dtls="select batch_id, color_id, width_dia_type, dia, order_id, sub_process_id, item_id,sum(reject_qty) as reject_qty, sum(delivery_qty) as delivery_qty, sum(gray_qty) as gray_qty, sum(carton_roll) as carton_roll, gsm, remarks from subcon_delivery_dtls where mst_id='$mst_id' and process_id in (3,4) group by batch_id, color_id, width_dia_type, gsm, dia, order_id, sub_process_id, item_id, remarks, id order by batch_id, sub_process_id, color_id";
		

			$i=1; $k=1; $width_dia_type_array=array(); $sub_process_array=array(); $batch_array=array(); $color_array=array();

			$dtls_value=sql_select($sql_dtls);
			?>
			<table style="margin-left:20px;"  align="left" cellspacing="0" width="1010"  border="1" rules="all" class="rpt_table" >
				<thead bgcolor="#dddddd" align="center" style="font-size:14px">
					<th width="20">SL</th>
					<th width="70" align="center">Order No</th>
					<th width="60" align="center">Cust. Buyer</th>
                    <th width="60" align="center">Cust. Style</th>
					<th width="70" align="center">Rec. Challan</th>
					<th width="110" align="center">Description</th>
					<th width="50" align="center">GSM</th>
					<th width="50" align="center">G/Dia</th>
					<th width="50" align="center">F/Dia</th>
					<th width="50" align="center">Roll</th>
					<th width="70" align="center">Grey Qty</th>
					<th width="70" align="center">Fin. Qty</th>
                    <th width="70" align="center">Rej. Qty</th>
					<th width="70" align="center">Process %</th>
					<th align="center">Remarks</th>
				</thead>
				<?
					$sql_batch="Select a.id, a.batch_no, a.extention_no, b.fabric_from, b.po_id, b.id as item_id, b.width_dia_type, b.item_description, sum(b.roll_no) as roll_no, sum(b.batch_qnty) as batch_qnty, b.rec_challan,a.color_range_id from  pro_batch_create_mst a, pro_batch_create_dtls b,gbl_temp_engine g  where a.entry_form=36 and a.id=b.mst_id and  b.po_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=72 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no, a.extention_no, a.process_id, b.fabric_from, b.item_description, b.po_id, b.id, b.width_dia_type, b.rec_challan,a.color_range_id";

					$batch_full_array=array();
					$result_batch=sql_select($sql_batch);
					foreach($result_batch as $row)
					{
						//$batch_array[$row[csf('po_id')]]['id']=$row[csf('id')];  *batch_no*batch_ext*color_id
						$item=explode(',',$row[csf('item_description')]);
						$batch_full_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('item_id')]]['item_description']=$row[csf('item_description')];
						$batch_full_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('item_id')]]['rec_challan']=$row[csf('rec_challan')];
						$batch_full_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('item_id')]]['dia']=$item[2];
						$batch_full_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('item_id')]]['roll_no']=$row[csf('roll_no')];
						$batch_full_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('item_id')]]['batch_qnty']=$row[csf('batch_qnty')];
						$batch_full_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('item_id')]]['fabric_from']=$row[csf('fabric_from')];
						$batch_full_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('item_id')]]['width_dia_type']=$row[csf('width_dia_type')];
						$batch_full_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('item_id')]]['batch_no']=$row[csf('batch_no')];
						$batch_full_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('item_id')]]['extention_no']=$row[csf('extention_no')];
						$batch_full_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('item_id')]]['color_range']=$row[csf('color_range_id')];
					}

				   /*$sql_fin_dia=sql_select("select order_id,dia_width from subcon_production_dtls where status_active=1 and is_deleted=0");
				   foreach($sql_fin_dia as $val)
				   {
				   	$fin_dia_arr[$val[csf("order_id")]]=$val[csf("dia_width")];

				   }*/

				//    $sql_fin_dia=sql_select("select order_id,batch_id,cons_comp_id, dia_width,fabric_description from subcon_production_dtls where status_active=1 and is_deleted=0");
				//    foreach($sql_fin_dia as $val)
				//    {
				  
				//  	if($val[csf("dia_width")]!="")
				//   	{
				//   	$fin_dia_arr[$val[csf("order_id")]][$val[csf("fabric_description")]]=$val[csf("dia_width")];
				// 	}
				//   }


				    // $sql_grey_dia=sql_select("select id, po_id, grey_dia,fin_dia from pro_batch_create_dtls");
					$sql_grey_dia=sql_select("select b.id, b.po_id, b.grey_dia,b.fin_dia from pro_batch_create_dtls b,gbl_temp_engine g  where b.order_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=72 and 1=1 ");
				   foreach($sql_grey_dia as $val)
				   {
				   	$grey_dia_arr[$val[csf("po_id")]][$val[csf("id")]]=$val[csf("grey_dia")];
					$fin_dia_arr[$val[csf("po_id")]][$val[csf("id")]]=$val[csf("fin_dia")];

				   }

				//    $sql_grey_qty=sql_select("select order_id,process_loss from subcon_ord_breakdown");
				$sql_grey_qty=sql_select("select b.order_id,b.process_loss from subcon_ord_breakdown b,gbl_temp_engine g  where b.order_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=72 and 1=1 ");
				   foreach($sql_grey_qty as $val)
				   {
				   	$grey_arr[$val[csf("order_id")]]=$val[csf("process_loss")];

				   }
				   execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id."   and ENTRY_FORM=72");
				   oci_commit($con);
				   disconnect($con);
					foreach($dtls_value as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$item_name=$batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['item_description'];
						
						$process_id=explode(',',$row[csf('sub_process_id')]);

						$process_val='';
						foreach ($process_id as $val)
						{
							//rsort($val);
							if($process_val=='') $process_val=$conversion_cost_head_array[$val]; else $process_val.="+".$conversion_cost_head_array[$val];
							//echo $val;
						}

						if ($batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['batch_no']!='' || $batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['extention_no']!='')
						{
							$batch_no=$batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['batch_no'].' '." Ex: ". $batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['extention_no'];
							$color_range_id=$batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['color_range'];
						}
						else
						{
							$batch_no=$batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['batch_no'];
							$color_range_id=$batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['color_range'];
							
						}
						$gsm_dia=explode(',',$batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['item_description']);
						if ($gsm_dia[1]!='' && $gsm_dia[1]!=0 ) $batch_dia=$gsm_dia[2]; else $batch_dia=$gsm_dia[2];

						$chack_string=$row[csf('batch_id')].$row[csf('color_id')].$row[csf('sub_process_id')];
						if(!in_array($chack_string,$sub_process_array))
						{
							if($i!=1)
							{
								$subProcessLoss=(($tot_grey_qty-$tot_finish_qty)/$tot_grey_qty)*100;
							?>
							<tr class="tbl_bottom" bgcolor="#CCCCCC" style="font-size:14px">
								<th width="20">&nbsp;</th>
								<th width="70">&nbsp;</th>
								<th width="60">&nbsp;</th>
                                <th width="60">&nbsp;</th>
								<th width="70">&nbsp;</th>
								<th width="110">&nbsp;</th>
								<th width="50">&nbsp;</th>
								<th width="50">&nbsp;</th>
								<th width="50"><strong>Total</strong></th>
								<th width="50" align="right"><? echo number_format($tot_roll,2,'.',''); ?>&nbsp;</th>
								<th width="70" align="right"><? echo number_format($tot_grey_qty,2,'.',''); ?>&nbsp;</th>
								<th width="70" align="right"><? echo number_format($tot_finish_qty,2,'.',''); ?>&nbsp;</th>
                                <th width="70" align="right"><? echo $tot_rej_qty; ?>&nbsp;</th>
								<th width="70" align="right" title="<?='(('.$tot_grey_qty.'-'.$tot_finish_qty.')/'.$tot_grey_qty.')*100'; ?>"><? echo number_format($subProcessLoss,2); ?>&nbsp;</th>
								<th>&nbsp;</th>
							</tr>
						<?
								unset($tot_roll);
								unset($tot_grey_qty);
								unset($tot_finish_qty);
								unset($tot_rej_qty);
								//unset($tot_proces_loss);
							}
						
						?>
							<tr height="30"><td colspan="15" style="font-size:15px" bgcolor="#CCCCAA"><p><?php echo "<i>Batch No: </i>" . $batch_no;
								echo "; <i>Color: </i>" . $color_arr[$row[csf('color_id')]];
								echo "; <i>Color Range: </i>" .$color_range[$color_range_id];
								echo "; <i>Process: </i>" . $process_val . ""; ?></p></td></tr>
						<?
							$sub_process_array[$i]=$chack_string;
							//unset($sub_process_array);
						}
						$dia_type='';
						if($batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['width_dia_type']==1) $dia_type="Open";
						else if($batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['width_dia_type']==2) $dia_type="Tube";
						else if($batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['width_dia_type']==3) $dia_type="Niddle";
						
						$item_description=$batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['item_description'];
						$fin_dia=$fin_dia_arr[$row[csf("order_id")]][$item_description];
						$fabric_desciption="";
						if($gsm_dia[1]!="") $fabric_desciption=$gsm_dia[0].','.$gsm_dia[1].'('.$dia_type.')'; else $fabric_desciption=$gsm_dia[0].'('.$dia_type.')';
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:14px">
						<td width="20"><? echo $i; ?></td>
						<td width="70" style="word-break:break-all"><? echo $job_po_array[$row[csf('order_id')]]['order_no']; ?></td>
						<td width="60" style="word-break:break-all"><? echo $job_po_array[$row[csf('order_id')]]['cust_buyer']; ?></td>
                        <td width="60" style="word-break:break-all"><? echo $job_po_array[$row[csf('order_id')]]['cust_style_ref']; ?></td>
						<td width="70" style="word-break:break-all"><? echo $batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['rec_challan']; ?></td>
						<td width="110" style="word-break:break-all"><? echo $fabric_desciption; ?></td>
						<td width="50" align="center" style="word-break:break-all"><? echo $row[csf('gsm')]; ?></td>
						<td width="50" align="center" style="word-break:break-all"><? echo $grey_dia_arr[$row[csf("order_id")]][$row[csf("item_id")]];  ?></td>
						<td width="50" align="center" style="word-break:break-all"><? echo $row[csf("dia")]; ?></td>
						<td width="50" align="right"><? echo $row[csf('carton_roll')]; ?>&nbsp;</td>

						<td width="70" align="right" style="word-break:break-all"><? echo number_format($row[csf('gray_qty')],2,'.',''); ?>&nbsp;</td>
						<td width="70" align="right" style="word-break:break-all"><? echo number_format($row[csf('delivery_qty')],2,'.',''); ?>&nbsp;</td>
                        <td width="70" align="right" style="word-break:break-all"><? echo $row[csf('reject_qty')]; ?>&nbsp;</td>
						<td width="70" align="right" style="word-break:break-all" title="<?='(('.$row[csf('gray_qty')].'-'.$row[csf('delivery_qty')].')/'.$row[csf('gray_qty')].')*100'; ?>">
								<?
								$proces_loss=(($row[csf('gray_qty')]-$row[csf('delivery_qty')])/$row[csf('gray_qty')])*100;
								echo number_format($proces_loss,2,'.','');
								?>
								&nbsp;
						</td>
						<td style="word-break:break-all"><? echo $row[csf('remarks')]; ?></td>
					</tr>
					<?
					$tot_roll+=$row[csf('carton_roll')];
					$tot_grey_qty+=$row[csf('gray_qty')];
					$tot_finish_qty+=$row[csf('delivery_qty')];


					$tot_rej_qty+=$row[csf('reject_qty')];
					$grand_tot_reject_qty+=$row[csf('reject_qty')];

					$grand_tot_roll+=$row[csf('carton_roll')];
					$grand_tot_grey_qty+=$row[csf('gray_qty')];
					//$grand_tot_grey_qty+=str_replace(",", "",number_format($row[csf('gray_qty')]));
					//$grand_tot_finish_qty+=str_replace(",", "",number_format($row[csf('delivery_qty')]));
					$grand_tot_finish_qty+=$row[csf('delivery_qty')];

					//$tot_proces_loss+=(($tot_grey_qty-$tot_finish_qty)/$tot_grey_qty)*100;
					$i++;
				}
				?>
					<tr class="tbl_bottom" bgcolor="#CCCCCC" style="font-size:14px">
						<th width="20">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="110">&nbsp;</th>
						<th width="50">&nbsp;</th>
						<th width="50">&nbsp;</th>
						<th width="50"><strong>Total</strong></th>
						<th width="50" align="right"><? echo number_format($tot_roll,2,'.',''); ?>&nbsp;</th>
						<th width="70" align="right"><? echo number_format($tot_grey_qty,2,'.',''); ?>&nbsp;</th>
						<th width="70" align="right"><? echo number_format($tot_finish_qty,2,'.',''); ?>&nbsp;</th>
                        <th width="70" align="right"><? echo $tot_rej_qty; ?>&nbsp;</th>
						<th width="70" align="right" title="<?='(('.$tot_grey_qty.'-'.$tot_finish_qty.')/'.$tot_grey_qty.')*100'; ?>">
							<?
							$subProcessLoss=(($tot_grey_qty-$tot_finish_qty)/$tot_grey_qty)*100;
								//$tot_proces_loss=(($tot_grey_qty-$tot_finish_qty)/$tot_grey_qty)*100;
								echo number_format($subProcessLoss,2,'.','');
							?>
							&nbsp;
						</th>
						<th>&nbsp;</th>
					</tr>
					<tfoot style="font-size:14px">
						<th width="20">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="110">&nbsp;</th>
						<th width="50">&nbsp;</th>
						<th width="50">&nbsp;</th>
						<th width="50"><strong>Grand Total</strong></th>
						<th width="50" align="right"><? echo number_format($grand_tot_roll,2,'.',''); ?>&nbsp;</th>
						<th width="70" align="right"><? echo number_format($grand_tot_grey_qty,2,'.',''); ?>&nbsp;</th>
						<th width="70" align="right"><? echo number_format($grand_tot_finish_qty,2,'.',''); ?>&nbsp;</th>
                        <th width="70" align="right"><? echo $grand_tot_reject_qty; ?>&nbsp;</th>
						<th width="70" align="right" title="<?='(('.$grand_tot_grey_qty.'-'.$grand_tot_finish_qty.')/'.$grand_tot_grey_qty.')*100'; ?>">
							<?
								$grand_tot_proces_loss=(($grand_tot_grey_qty-$grand_tot_finish_qty)/$grand_tot_grey_qty)*100;
								echo number_format($grand_tot_proces_loss,2,'.','');
							?>
							&nbsp;
						</th>
						<th>&nbsp;</th>
					</tfoot>
				</table>

				 <?
            //echo signature_table(46, $company, "900px");
				 //echo signature_table(46, $company, "900px",$cbo_template_id,70,$user_lib_name[$inserted_by]);
			if($reportType == 1 || $reportType == 4 )
			{
         ?>
		
          </div>
		 
		
		<div>
		<? echo signature_table(46, $company, "900px"); ?>
		</div>
		<p style="page-break-after:always;"></p>
    	<table width="900" cellspacing="0" >
        	<tr><td colspan="6">

            </td></tr>
			<tr><td colspan="6" align="center"></td></tr>
            <tr>
				<td colspan="6">
                    <table cellpadding="0" cellspacing="0" >
                        <tr>
                            <td width="200" align="right">
                                <img  src='../../<? echo $imge_arr[str_replace("'","",$company)]; ?>' height='60%' width='60%' />
                            </td>
                            <td align="right">
                                <table width="800px" cellspacing="0" align="center">
                                    <tr>
                                        <td align="center" style="font-size:18px"><strong ><? echo $company_library[$company]; ?></strong></td>
                                    </tr>
                                    <tr class="form_caption">
                                        <td  align="center" style="font-size:14px">
                                        <?
											$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, contact_no, country_id, province, city, zip_code, contact_no, email, website, vat_number from lib_company where id=$company and status_active=1 and is_deleted=0");
											foreach ($nameArray as $result)
											{
												echo $result[csf('plot_no')]; ?> &nbsp;
												<? echo $result[csf('level_no')]?> &nbsp;
												<? echo $result[csf('road_no')]; ?> &nbsp;
												<? echo $result[csf('block_no')];?> &nbsp;
												<? echo $result[csf('city')];?> &nbsp;
												<? echo $result[csf('zip_code')]; ?> &nbsp;
												<? echo $result[csf('province')];?> &nbsp;
												<? echo $country_arr[$result[csf('country_id')]]; ?>&nbsp; <br>
												<? echo $result[csf('contact_no')];?> &nbsp;
												<? echo $result[csf('email')];?> &nbsp;
												<? echo $result[csf('website')]; ?> <br>
                                                <b> Vat No : <? echo $result[csf('vat_number')]; ?></b> <?
											}
                                        ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center" style="font-size:16px"><strong><u>Gate Pass</u></strong></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
    			</td>
            </tr>
            <tr>
                <?
                $party_add=$dataArray[0][csf('party_id')];
                $nameArray=sql_select( "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$party_add");
                foreach ($nameArray as $result)
                {
                    $address="";
                    if($result[csf('address_1')]!="") $address=$result[csf('address_1')];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
                }
                ?>
                <td width="300" rowspan="4" valign="top" colspan="2" style="font-size:14px"><strong>Party : <? echo $buyer_library[$party_add].'<br>'.'Party Address: '.$address;  ?></strong></td>
                <td width="120" style="font-size:14px"><strong>Challan No :</strong></td><td width="170px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
                <td width="120" style="font-size:14px"><strong>Delivery Date :</strong></td><td width="170px"><? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
            </tr>
            <tr>
                <td style="font-size:14px"><strong>Transport Com.:</strong></td><td><? echo $dataArray[0][csf('transport_company')]; ?></td>
                <td style="font-size:14px"><strong>Forwarder:</strong></td><td><? echo $supplier_library[$dataArray[0][csf('forwarder')]]; ?></td>
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
                               <td align="center"><?  echo fn_number_format($grand_tot_finish_qty,2); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
        &nbsp;
		<?php 
    	if ($reportType==4) {
    		echo signature_table(46, $company, '900px', '', 0);
    	} else {
        ?>
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
        <?php 
        	}
        ?>
	</div>
	<?
    }
    exit();
}

if($action=="subcon_delivery_entry_print_21_12_23")
{
	extract($_REQUEST);
	$ex_data=explode('*',$data);
	$company=$ex_data[0];
	$location=$ex_data[5];
	$update_id=$ex_data[1];
	$sys_id=$ex_data[2];
	$reportType=$ex_data[4];
	$divHeight = $reportType == 4 ? '940px' : '1010px';
	//print_r ($data);
	//$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library",'master_tble_id','image_location');
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_library=return_library_array( "select id,supplier_name from lib_supplier", "id","supplier_name");
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$country_arr=return_library_array( "select id, country_name from lib_country", "id", "country_name");
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");//die;
	//$item_arr=return_library_array( "select cons_comp_id, color_name from lib_color", "cons_comp_id", "color_name");
	//$color_arr=return_library_array( "select id, color_name from  lib_color", "id", "color_name"  );
	

	$sql="select id, party_id, challan_no, delivery_date, forwarder, transport_company,delivery_no,vehical_no from subcon_delivery_mst where id='$update_id' and company_id='$company' and status_active=1 and is_deleted=0";
	$dataArray=sql_select($sql);
	$mst_id=$dataArray[0][csf('id')];

	$sql_job_po="select a.id, a.order_no, a.main_process_id, a.cust_buyer, a.cust_style_ref, b.party_id, b.subcon_job, a.process_id from  subcon_ord_dtls a, subcon_ord_mst b,subcon_delivery_dtls c where a.job_no_mst=b.subcon_job and a.id=c.order_id and c.mst_id=$mst_id and b.is_deleted=0 and b.status_active=1 and a.status_active=1 and a.is_deleted=0";
	$job_po_array=array();
	$result_job_po=sql_select($sql_job_po);
	foreach($result_job_po as $row)
	{
		$job_po_array[$row[csf('id')]]['order_no']=$row[csf('order_no')];
		$job_po_array[$row[csf('id')]]['main_process_id']=$row[csf('main_process_id')];
		$job_po_array[$row[csf('id')]]['process_id']=$row[csf('process_id')];
		$job_po_array[$row[csf('id')]]['party_id']=$row[csf('party_id')];
		$job_po_array[$row[csf('id')]]['subcon_job']=$row[csf('subcon_job')];
		$job_po_array[$row[csf('id')]]['cust_buyer']=$row[csf('cust_buyer')];
		$job_po_array[$row[csf('id')]]['cust_style_ref']=$row[csf('cust_style_ref')];
		$PoIdArr[$row[csf('id')]]=$row[csf('id')];
	}
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id."   and ENTRY_FORM=72");
	oci_commit($con);
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 72, 1, $PoIdArr, $empty_arr);//PO ID Ref from=1

	//var_dump($job_po_array);
	// $recChallan_arr=array();
	// if($db_type==0)
	// {
	// 	$sql_rec="select b.order_id, group_concat(distinct(a.chalan_no)) as chalan_no, group_concat(distinct(b.grey_dia)) as grey_dia from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=1 group by b.order_id";
	// }
	// else if ($db_type==2)
	// {
	// 	$sql_rec="select b.order_id, wm_concat(distinct(cast(a.chalan_no as varchar2(500)))) as chalan_no, wm_concat(distinct(cast(b.grey_dia as varchar2(500)))) as grey_dia from sub_material_mst a, sub_material_dtls b,subcon_delivery_dtls c where a.id=b.mst_id and b.order_id=c.order_id and c.mst_id=$mst_id   and a.trans_type=1 group by b.order_id";
	// }
	// $result_sql_rec=sql_select($sql_rec);
	// foreach($result_sql_rec as $row)
	// {
	// 	$recChallan_arr[$row[csf('order_id')]]['chalan_no']=$row[csf('chalan_no')];
	// 	$recChallan_arr[$row[csf('order_id')]]['grey_dia']=$row[csf('grey_dia')];
	// }

	
	$com_dtls = fnc_company_location_address($company, $location, 2);
	?>
    <div style="width:930px;">
   <table width="100%" cellpadding="0" cellspacing="0" >
       <tr>
           <td width="200" align="right">
               <img  src='../../<? echo $com_dtls[2]; ?>' height='60%' width='60%' />
           </td>
           <td>
    <table width="800" cellspacing="0" align="center">
        <tr>
            <td align="center" style="font-size:22px"><strong ><? echo $com_dtls[0]; ?></strong></td>
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
                        <? echo $result[csf('level_no')]?> &nbsp;
                        <? echo $result[csf('road_no')]; ?> &nbsp;
                        <? echo $result[csf('block_no')];?> &nbsp;
                        <? echo $result[csf('city')];?> &nbsp;
                        <? echo $result[csf('zip_code')]; ?> &nbsp;
                        <? echo $result[csf('province')];?> &nbsp;
                        <? echo $country_arr[$result[csf('country_id')]]; ?>&nbsp; <br>
                        <? echo $result[csf('contact_no')];?> &nbsp;
                        <? echo $result[csf('email')];?> &nbsp;
                        <? echo $result[csf('website')]; ?> <br>
                        <b> Vat No : <? echo $result[csf('vat_number')]; ?></b> <?
					}*/
                ?>
            </td>
        </tr>
        <tr>
            <td align="center" style="font-size:18px"><strong><? echo $production_process[$job_po_array[$dataArray[0][csf('order_id')]]['main_process_id']]; ?> Delivery Challan</strong></td>
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
			//	echo "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$party_add";
                $nameArray=sql_select( "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$party_add");
				 $address="";
                foreach ($nameArray as $result)
                {

                    if($result[csf('address_1')]!="") $address=$result[csf('address_1')];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
                }
            ?>
        	<td width="300" rowspan="4" valign="top" colspan="2" style="font-size:14px"><strong>Party : <? echo $buyer_library[$party_add].'<br>'.'Party Address: '.$address;  ?></strong></td>
            <td width="125" style="font-size:14px"><strong>Challan No :</strong></td><td width="170px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
            <td width="125" style="font-size:14px"><strong>Delivery Date :</strong></td><td width="170px"><? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
        </tr>
        <tr>
            <td style="font-size:14px"><strong>Transport Com.:</strong></td><td><? echo $dataArray[0][csf('transport_company')]; ?></td>
            <td style="font-size:14px"><strong>Forwarder:</strong></td><td><? echo $supplier_library[$dataArray[0][csf('forwarder')]]; ?></td>
        </tr>
        <tr>
		<td style="font-size:14px"><strong>Vehicle No:</strong></td><td><? echo $dataArray[0][csf('vehical_no')]; ?></td>
		<td colspan="4"></td> </tr>
    </table>

    <div style="width:120%; height: <?php echo $divHeight; ?>">
			<?
			// $gray_dia_array=array(); $prod_dia_array=array();
			// if ($db_type==0)
			// {
			// 	$prod_dia_sql="select a.batch_id, a.cons_comp_id, a.process, a.dia_width from subcon_production_dtls a, subcon_production_qnty b where a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 group by a.batch_id, a.cons_comp_id, a.process ";
			// }
			// else if ($db_type==2)
			// {
			// 	$prod_dia_sql="select a.batch_id, a.cons_comp_id, cast(a.process as varchar2(100)) as process, a.dia_width from subcon_production_dtls a, subcon_production_qnty b where a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 group by a.batch_id, a.cons_comp_id, a.dia_width, a.process";
			// }
			// $result_prod_dia_sql=sql_select($prod_dia_sql);
			// foreach($result_prod_dia_sql as $row)
			// {
			// 	$prod_dia_array[$row[csf('batch_id')]][$row[csf('cons_comp_id')]]['dia_width']=$row[csf('dia_width')];
			// 	$prod_dia_array[$row[csf('batch_id')]][$row[csf('cons_comp_id')]]['process']=$row[csf('process')];
			// }
			//print_r($prod_dia_array);
			//$inv_item_arr=return_library_array( "select id,material_description from sub_material_dtls",'id','material_description');
			//$prod_item_arr=return_library_array( "select id,fabric_description from subcon_production_dtls",'id','fabric_description');


			
			 $sql_dtls="select batch_id, color_id, width_dia_type, dia, order_id, sub_process_id, item_id,sum(reject_qty) as reject_qty, sum(delivery_qty) as delivery_qty, sum(gray_qty) as gray_qty, sum(carton_roll) as carton_roll, gsm, remarks from subcon_delivery_dtls where mst_id='$mst_id' and process_id in (3,4) group by batch_id, color_id, width_dia_type, gsm, dia, order_id, sub_process_id, item_id, remarks, id order by batch_id, sub_process_id, color_id";
		

			$i=1; $k=1; $width_dia_type_array=array(); $sub_process_array=array(); $batch_array=array(); $color_array=array();

			$dtls_value=sql_select($sql_dtls);
			?>
			<table style="margin-left:20px;"  align="left" cellspacing="0" width="1010"  border="1" rules="all" class="rpt_table" >
				<thead bgcolor="#dddddd" align="center" style="font-size:14px">
					<th width="20">SL</th>
					<th width="70" align="center">Order No</th>
					<th width="60" align="center">Cust. Buyer</th>
                    <th width="60" align="center">Cust. Style</th>
					<th width="70" align="center">Rec. Challan</th>
					<th width="110" align="center">Description</th>
					<th width="50" align="center">GSM</th>
					<th width="50" align="center">G/Dia</th>
					<th width="50" align="center">F/Dia</th>
					<th width="50" align="center">Roll</th>
					<th width="70" align="center">Grey Qty</th>
					<th width="70" align="center">Fin. Qty</th>
                    <th width="70" align="center">Rej. Qty</th>
					<th width="70" align="center">Process %</th>
					<th align="center">Remarks</th>
				</thead>
				<?
					$sql_batch="Select a.id, a.batch_no, a.extention_no, b.fabric_from, b.po_id, b.id as item_id, b.width_dia_type, b.item_description, sum(b.roll_no) as roll_no, sum(b.batch_qnty) as batch_qnty, b.rec_challan,a.color_range_id from  pro_batch_create_mst a, pro_batch_create_dtls b,gbl_temp_engine g  where a.entry_form=36 and a.id=b.mst_id and  b.po_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=72 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no, a.extention_no, a.process_id, b.fabric_from, b.item_description, b.po_id, b.id, b.width_dia_type, b.rec_challan,a.color_range_id";

					$batch_full_array=array();
					$result_batch=sql_select($sql_batch);
					foreach($result_batch as $row)
					{
						//$batch_array[$row[csf('po_id')]]['id']=$row[csf('id')];  *batch_no*batch_ext*color_id
						$item=explode(',',$row[csf('item_description')]);
						$batch_full_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('item_id')]]['item_description']=$row[csf('item_description')];
						$batch_full_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('item_id')]]['rec_challan']=$row[csf('rec_challan')];
						$batch_full_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('item_id')]]['dia']=$item[2];
						$batch_full_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('item_id')]]['roll_no']=$row[csf('roll_no')];
						$batch_full_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('item_id')]]['batch_qnty']=$row[csf('batch_qnty')];
						$batch_full_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('item_id')]]['fabric_from']=$row[csf('fabric_from')];
						$batch_full_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('item_id')]]['width_dia_type']=$row[csf('width_dia_type')];
						$batch_full_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('item_id')]]['batch_no']=$row[csf('batch_no')];
						$batch_full_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('item_id')]]['extention_no']=$row[csf('extention_no')];
						$batch_full_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('item_id')]]['color_range']=$row[csf('color_range_id')];
					}

				   /*$sql_fin_dia=sql_select("select order_id,dia_width from subcon_production_dtls where status_active=1 and is_deleted=0");
				   foreach($sql_fin_dia as $val)
				   {
				   	$fin_dia_arr[$val[csf("order_id")]]=$val[csf("dia_width")];

				   }*/

				//    $sql_fin_dia=sql_select("select order_id,batch_id,cons_comp_id, dia_width,fabric_description from subcon_production_dtls where status_active=1 and is_deleted=0");
				//    foreach($sql_fin_dia as $val)
				//    {
				  
				//  	if($val[csf("dia_width")]!="")
				//   	{
				//   	$fin_dia_arr[$val[csf("order_id")]][$val[csf("fabric_description")]]=$val[csf("dia_width")];
				// 	}
				//   }


				    // $sql_grey_dia=sql_select("select id, po_id, grey_dia,fin_dia from pro_batch_create_dtls");
					$sql_grey_dia=sql_select("select b.id, b.po_id, b.grey_dia,b.fin_dia from pro_batch_create_dtls b,gbl_temp_engine g  where b.order_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=72 and 1=1 ");
				   foreach($sql_grey_dia as $val)
				   {
				   	$grey_dia_arr[$val[csf("po_id")]][$val[csf("id")]]=$val[csf("grey_dia")];
					$fin_dia_arr[$val[csf("po_id")]][$val[csf("id")]]=$val[csf("fin_dia")];

				   }

				//    $sql_grey_qty=sql_select("select order_id,process_loss from subcon_ord_breakdown");
				$sql_grey_qty=sql_select("select b.order_id,b.process_loss from subcon_ord_breakdown b,gbl_temp_engine g  where b.order_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=72 and 1=1 ");
				   foreach($sql_grey_qty as $val)
				   {
				   	$grey_arr[$val[csf("order_id")]]=$val[csf("process_loss")];

				   }
				   execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id."   and ENTRY_FORM=72");
				   oci_commit($con);
				   disconnect($con);
					foreach($dtls_value as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$item_name=$batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['item_description'];
						
						$process_id=explode(',',$row[csf('sub_process_id')]);

						$process_val='';
						foreach ($process_id as $val)
						{
							//rsort($val);
							if($process_val=='') $process_val=$conversion_cost_head_array[$val]; else $process_val.="+".$conversion_cost_head_array[$val];
							//echo $val;
						}

						if ($batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['batch_no']!='' || $batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['extention_no']!='')
						{
							$batch_no=$batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['batch_no'].' '." Ex: ". $batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['extention_no'];
							$color_range_id=$batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['color_range'];
						}
						else
						{
							$batch_no=$batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['batch_no'];
							$color_range_id=$batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['color_range'];
							
						}
						$gsm_dia=explode(',',$batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['item_description']);
						if ($gsm_dia[1]!='' && $gsm_dia[1]!=0 ) $batch_dia=$gsm_dia[2]; else $batch_dia=$gsm_dia[2];

						$chack_string=$row[csf('batch_id')].$row[csf('color_id')].$row[csf('sub_process_id')];
						if(!in_array($chack_string,$sub_process_array))
						{
							if($i!=1)
							{
								$subProcessLoss=(($tot_grey_qty-$tot_finish_qty)/$tot_grey_qty)*100;
							?>
							<tr class="tbl_bottom" bgcolor="#CCCCCC" style="font-size:14px">
								<th width="20">&nbsp;</th>
								<th width="70">&nbsp;</th>
								<th width="60">&nbsp;</th>
                                <th width="60">&nbsp;</th>
								<th width="70">&nbsp;</th>
								<th width="110">&nbsp;</th>
								<th width="50">&nbsp;</th>
								<th width="50">&nbsp;</th>
								<th width="50"><strong>Total</strong></th>
								<th width="50" align="right"><? echo number_format($tot_roll,2,'.',''); ?>&nbsp;</th>
								<th width="70" align="right"><? echo number_format($tot_grey_qty,2,'.',''); ?>&nbsp;</th>
								<th width="70" align="right"><? echo number_format($tot_finish_qty,2,'.',''); ?>&nbsp;</th>
                                <th width="70" align="right"><? echo $tot_rej_qty; ?>&nbsp;</th>
								<th width="70" align="right" title="<?='(('.$tot_grey_qty.'-'.$tot_finish_qty.')/'.$tot_grey_qty.')*100'; ?>"><? echo number_format($subProcessLoss,2); ?>&nbsp;</th>
								<th>&nbsp;</th>
							</tr>
						<?
								unset($tot_roll);
								unset($tot_grey_qty);
								unset($tot_finish_qty);
								unset($tot_rej_qty);
								//unset($tot_proces_loss);
							}
						
						?>
							<tr height="30"><td colspan="15" style="font-size:15px" bgcolor="#CCCCAA"><p><?php echo "<i>Batch No: </i>" . $batch_no;
								echo "; <i>Color: </i>" . $color_arr[$row[csf('color_id')]];
								echo "; <i>Color Range: </i>" .$color_range[$color_range_id];
								echo "; <i>Process: </i>" . $process_val . ""; ?></p></td></tr>
						<?
							$sub_process_array[$i]=$chack_string;
							//unset($sub_process_array);
						}
						$dia_type='';
						if($batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['width_dia_type']==1) $dia_type="Open";
						else if($batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['width_dia_type']==2) $dia_type="Tube";
						else if($batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['width_dia_type']==3) $dia_type="Niddle";
						
						$item_description=$batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['item_description'];
						$fin_dia=$fin_dia_arr[$row[csf("order_id")]][$item_description];
						$fabric_desciption="";
						if($gsm_dia[1]!="") $fabric_desciption=$gsm_dia[0].','.$gsm_dia[1].'('.$dia_type.')'; else $fabric_desciption=$gsm_dia[0].'('.$dia_type.')';
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:14px">
						<td width="20"><? echo $i; ?></td>
						<td width="70" style="word-break:break-all"><? echo $job_po_array[$row[csf('order_id')]]['order_no']; ?></td>
						<td width="60" style="word-break:break-all"><? echo $job_po_array[$row[csf('order_id')]]['cust_buyer']; ?></td>
                        <td width="60" style="word-break:break-all"><? echo $job_po_array[$row[csf('order_id')]]['cust_style_ref']; ?></td>
						<td width="70" style="word-break:break-all"><? echo $batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['rec_challan']; ?></td>
						<td width="110" style="word-break:break-all"><? echo $fabric_desciption; ?></td>
						<td width="50" align="center" style="word-break:break-all"><? echo $row[csf('gsm')]; ?></td>
						<td width="50" align="center" style="word-break:break-all"><? echo $grey_dia_arr[$row[csf("order_id")]][$row[csf("item_id")]];  ?></td>
						<td width="50" align="center" style="word-break:break-all"><? echo $row[csf("dia")]; ?></td>
						<td width="50" align="right"><? echo $row[csf('carton_roll')]; ?>&nbsp;</td>

						<td width="70" align="right" style="word-break:break-all"><? echo number_format($row[csf('gray_qty')],2,'.',''); ?>&nbsp;</td>
						<td width="70" align="right" style="word-break:break-all"><? echo number_format($row[csf('delivery_qty')],2,'.',''); ?>&nbsp;</td>
                        <td width="70" align="right" style="word-break:break-all"><? echo $row[csf('reject_qty')]; ?>&nbsp;</td>
						<td width="70" align="right" style="word-break:break-all" title="<?='(('.$row[csf('gray_qty')].'-'.$row[csf('delivery_qty')].')/'.$row[csf('gray_qty')].')*100'; ?>">
								<?
								$proces_loss=(($row[csf('gray_qty')]-$row[csf('delivery_qty')])/$row[csf('gray_qty')])*100;
								echo number_format($proces_loss,2,'.','');
								?>
								&nbsp;
						</td>
						<td style="word-break:break-all"><? echo $row[csf('remarks')]; ?></td>
					</tr>
					<?
					$tot_roll+=$row[csf('carton_roll')];
					$tot_grey_qty+=$row[csf('gray_qty')];
					$tot_finish_qty+=$row[csf('delivery_qty')];


					$tot_rej_qty+=$row[csf('reject_qty')];
					$grand_tot_reject_qty+=$row[csf('reject_qty')];

					$grand_tot_roll+=$row[csf('carton_roll')];
					$grand_tot_grey_qty+=$row[csf('gray_qty')];
					//$grand_tot_grey_qty+=str_replace(",", "",number_format($row[csf('gray_qty')]));
					//$grand_tot_finish_qty+=str_replace(",", "",number_format($row[csf('delivery_qty')]));
					$grand_tot_finish_qty+=$row[csf('delivery_qty')];

					//$tot_proces_loss+=(($tot_grey_qty-$tot_finish_qty)/$tot_grey_qty)*100;
					$i++;
				}
				?>
					<tr class="tbl_bottom" bgcolor="#CCCCCC" style="font-size:14px">
						<th width="20">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="110">&nbsp;</th>
						<th width="50">&nbsp;</th>
						<th width="50">&nbsp;</th>
						<th width="50"><strong>Total</strong></th>
						<th width="50" align="right"><? echo number_format($tot_roll,2,'.',''); ?>&nbsp;</th>
						<th width="70" align="right"><? echo number_format($tot_grey_qty,2,'.',''); ?>&nbsp;</th>
						<th width="70" align="right"><? echo number_format($tot_finish_qty,2,'.',''); ?>&nbsp;</th>
                        <th width="70" align="right"><? echo $tot_rej_qty; ?>&nbsp;</th>
						<th width="70" align="right" title="<?='(('.$tot_grey_qty.'-'.$tot_finish_qty.')/'.$tot_grey_qty.')*100'; ?>">
							<?
							$subProcessLoss=(($tot_grey_qty-$tot_finish_qty)/$tot_grey_qty)*100;
								//$tot_proces_loss=(($tot_grey_qty-$tot_finish_qty)/$tot_grey_qty)*100;
								echo number_format($subProcessLoss,2,'.','');
							?>
							&nbsp;
						</th>
						<th>&nbsp;</th>
					</tr>
					<tfoot style="font-size:14px">
						<th width="20">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="110">&nbsp;</th>
						<th width="50">&nbsp;</th>
						<th width="50">&nbsp;</th>
						<th width="50"><strong>Grand Total</strong></th>
						<th width="50" align="right"><? echo number_format($grand_tot_roll,2,'.',''); ?>&nbsp;</th>
						<th width="70" align="right"><? echo number_format($grand_tot_grey_qty,2,'.',''); ?>&nbsp;</th>
						<th width="70" align="right"><? echo number_format($grand_tot_finish_qty,2,'.',''); ?>&nbsp;</th>
                        <th width="70" align="right"><? echo $grand_tot_reject_qty; ?>&nbsp;</th>
						<th width="70" align="right" title="<?='(('.$grand_tot_grey_qty.'-'.$grand_tot_finish_qty.')/'.$grand_tot_grey_qty.')*100'; ?>">
							<?
								$grand_tot_proces_loss=(($grand_tot_grey_qty-$grand_tot_finish_qty)/$grand_tot_grey_qty)*100;
								echo number_format($grand_tot_proces_loss,2,'.','');
							?>
							&nbsp;
						</th>
						<th>&nbsp;</th>
					</tfoot>
				</table>

				 <?
            echo signature_table(46, $company, "900px");
				 //echo signature_table(46, $company, "900px",$cbo_template_id,70,$user_lib_name[$inserted_by]);
			if($reportType == 1 || $reportType == 4 )
			{
         ?>
          </div>
    	<table width="900" cellspacing="0" >
        	<tr><td colspan="6">

            </td></tr>
            <tr><td colspan="6" align="center">,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,</td></tr>
            <tr>
				<td colspan="6">
                    <table cellpadding="0" cellspacing="0" >
                        <tr>
                            <td width="200" align="right">
                                <img  src='../../<? echo $imge_arr[str_replace("'","",$company)]; ?>' height='60%' width='60%' />
                            </td>
                            <td align="right">
                                <table width="800px" cellspacing="0" align="center">
                                    <tr>
                                        <td align="center" style="font-size:18px"><strong ><? echo $company_library[$company]; ?></strong></td>
                                    </tr>
                                    <tr class="form_caption">
                                        <td  align="center" style="font-size:14px">
                                        <?
											$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, contact_no, country_id, province, city, zip_code, contact_no, email, website, vat_number from lib_company where id=$company and status_active=1 and is_deleted=0");
											foreach ($nameArray as $result)
											{
												echo $result[csf('plot_no')]; ?> &nbsp;
												<? echo $result[csf('level_no')]?> &nbsp;
												<? echo $result[csf('road_no')]; ?> &nbsp;
												<? echo $result[csf('block_no')];?> &nbsp;
												<? echo $result[csf('city')];?> &nbsp;
												<? echo $result[csf('zip_code')]; ?> &nbsp;
												<? echo $result[csf('province')];?> &nbsp;
												<? echo $country_arr[$result[csf('country_id')]]; ?>&nbsp; <br>
												<? echo $result[csf('contact_no')];?> &nbsp;
												<? echo $result[csf('email')];?> &nbsp;
												<? echo $result[csf('website')]; ?> <br>
                                                <b> Vat No : <? echo $result[csf('vat_number')]; ?></b> <?
											}
                                        ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center" style="font-size:16px"><strong><u>Gate Pass</u></strong></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
    			</td>
            </tr>
            <tr>
                <?
                $party_add=$dataArray[0][csf('party_id')];
                $nameArray=sql_select( "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$party_add");
                foreach ($nameArray as $result)
                {
                    $address="";
                    if($result[csf('address_1')]!="") $address=$result[csf('address_1')];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
                }
                ?>
                <td width="300" rowspan="4" valign="top" colspan="2" style="font-size:14px"><strong>Party : <? echo $buyer_library[$party_add].'<br>'.'Party Address: '.$address;  ?></strong></td>
                <td width="120" style="font-size:14px"><strong>Challan No :</strong></td><td width="170px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
                <td width="120" style="font-size:14px"><strong>Delivery Date :</strong></td><td width="170px"><? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
            </tr>
            <tr>
                <td style="font-size:14px"><strong>Transport Com.:</strong></td><td><? echo $dataArray[0][csf('transport_company')]; ?></td>
                <td style="font-size:14px"><strong>Forwarder:</strong></td><td><? echo $supplier_library[$dataArray[0][csf('forwarder')]]; ?></td>
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
                               <td align="center"><?  echo fn_number_format($grand_tot_finish_qty,2); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
        &nbsp;
		<?php 
    	if ($reportType==4) {
    		echo signature_table(46, $company, '900px', '', 0);
    	} else {
        ?>
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
        <?php 
        	}
        ?>
	</div>
	<?
    }
    exit();
}
if($action=="subcon_delivery_entry_print_old")
{
	extract($_REQUEST);
	$ex_data=explode('*',$data);
	$company=$ex_data[0];
	$location=$ex_data[5]; 
	$update_id=$ex_data[1];
	$sys_id=$ex_data[2];
	$reportType=$ex_data[4];
	$divHeight = $reportType == 4 ? '940px' : '1010px';
	//print_r ($data);
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library",'master_tble_id','image_location');
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_library=return_library_array( "select id,supplier_name from lib_supplier", "id","supplier_name");
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$country_arr=return_library_array( "select id, country_name from lib_country", "id", "country_name");
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");//die;
	//$item_arr=return_library_array( "select cons_comp_id, color_name from lib_color", "cons_comp_id", "color_name");
	//$color_arr=return_library_array( "select id, color_name from  lib_color", "id", "color_name"  );
	 

	$dataArray=sql_select($sql);
	$mst_id=$dataArray[0][csf('id')];

	$sql_job_po="select a.id, a.order_no, a.main_process_id, a.cust_buyer, a.cust_style_ref, b.party_id, b.subcon_job, a.process_id from  subcon_ord_dtls a, subcon_ord_mst b,subcon_delivery_dtls c where a.job_no_mst=b.subcon_job and a.id=c.order_id and c.mst_id=$mst_id and b.is_deleted=0 and b.status_active=1 and a.status_active=1 and a.is_deleted=0";
	$job_po_array=array();
	$result_job_po=sql_select($sql_job_po);
	foreach($result_job_po as $row)
	{
		$job_po_array[$row[csf('id')]]['order_no']=$row[csf('order_no')];
		$job_po_array[$row[csf('id')]]['main_process_id']=$row[csf('main_process_id')];
		$job_po_array[$row[csf('id')]]['process_id']=$row[csf('process_id')];
		$job_po_array[$row[csf('id')]]['party_id']=$row[csf('party_id')];
		$job_po_array[$row[csf('id')]]['subcon_job']=$row[csf('subcon_job')];
		$job_po_array[$row[csf('id')]]['cust_buyer']=$row[csf('cust_buyer')];
		$job_po_array[$row[csf('id')]]['cust_style_ref']=$row[csf('cust_style_ref')];
		$PoIdArr[$row[csf('id')]]=$row[csf('id')];
	}
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id."   and ENTRY_FORM=72");
	oci_commit($con);
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 72, 1, $PoIdArr, $empty_arr);//PO ID Ref from=1

	//var_dump($job_po_array);
	// $recChallan_arr=array();
	// if($db_type==0)
	// {
	// 	$sql_rec="select b.order_id, group_concat(distinct(a.chalan_no)) as chalan_no, group_concat(distinct(b.grey_dia)) as grey_dia from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=1 group by b.order_id";
	// }
	// else if ($db_type==2)
	// {
	// 	$sql_rec="select b.order_id, wm_concat(distinct(cast(a.chalan_no as varchar2(500)))) as chalan_no, wm_concat(distinct(cast(b.grey_dia as varchar2(500)))) as grey_dia from sub_material_mst a, sub_material_dtls b,subcon_delivery_dtls c where a.id=b.mst_id and b.order_id=c.order_id and c.mst_id=$mst_id   and a.trans_type=1 group by b.order_id";
	// }
	// $result_sql_rec=sql_select($sql_rec);
	// foreach($result_sql_rec as $row)
	// {
	// 	$recChallan_arr[$row[csf('order_id')]]['chalan_no']=$row[csf('chalan_no')];
	// 	$recChallan_arr[$row[csf('order_id')]]['grey_dia']=$row[csf('grey_dia')];
	// }

	$com_dtls = fnc_company_location_address($company, $location, 2);
?>
    <div style="width:930px;">
   <table width="100%" cellpadding="0" cellspacing="0" >
       <tr>
           <td width="200" align="right">
               <img  src='../../<? echo $com_dtls[2]; ?>' height='60%' width='60%' />
           </td>
           <td>
    <table width="800" cellspacing="0" align="center">
        <tr>
            <td align="center" style="font-size:22px"><strong ><? echo $com_dtls[0]; ?></strong></td>
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
                        <? echo $result[csf('level_no')]?> &nbsp;
                        <? echo $result[csf('road_no')]; ?> &nbsp;
                        <? echo $result[csf('block_no')];?> &nbsp;
                        <? echo $result[csf('city')];?> &nbsp;
                        <? echo $result[csf('zip_code')]; ?> &nbsp;
                        <? echo $result[csf('province')];?> &nbsp;
                        <? echo $country_arr[$result[csf('country_id')]]; ?>&nbsp; <br>
                        <? echo $result[csf('contact_no')];?> &nbsp;
                        <? echo $result[csf('email')];?> &nbsp;
                        <? echo $result[csf('website')]; ?> <br>
                        <b> Vat No : <? echo $result[csf('vat_number')]; ?></b> <?
					}*/
                ?>
            </td>
        </tr>
        <tr>
            <td align="center" style="font-size:18px"><strong><? echo $production_process[$job_po_array[$dataArray[0][csf('order_id')]]['main_process_id']]; ?> Delivery Challan</strong></td>
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
			//	echo "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$party_add";
                $nameArray=sql_select( "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$party_add");
				 $address="";
                foreach ($nameArray as $result)
                {

                    if($result[csf('address_1')]!="") $address=$result[csf('address_1')];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
                }
            ?>
        	<td width="300" rowspan="4" valign="top" colspan="2" style="font-size:14px"><strong>Party : <? echo $buyer_library[$party_add].'<br>'.'Party Address: '.$address;  ?></strong></td>
            <td width="125" style="font-size:14px"><strong>Challan No :</strong></td><td width="170px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
            <td width="125" style="font-size:14px"><strong>Delivery Date :</strong></td><td width="170px"><? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
        </tr>
        <tr>
            <td style="font-size:14px"><strong>Transport Com.:</strong></td><td><? echo $dataArray[0][csf('transport_company')]; ?></td>
            <td style="font-size:14px"><strong>Forwarder:</strong></td><td><? echo $supplier_library[$dataArray[0][csf('forwarder')]]; ?></td>
        </tr>
		<tr>
			<td style="font-size:14px"><strong>Vehicle No:</strong></td><td><? echo $dataArray[0][csf('vehical_no')]; ?></td>
            <td style="font-size:14px"><strong>Driver Name:</strong></td><td><? echo $dataArray[0][csf('driver_name')]; ?></td>
            
        </tr>
        <tr>
		<td style="font-size:14px"><strong>Mobile No:</strong></td><td><? echo $dataArray[0][csf('mobile_no')]; ?></td>
		<td style="font-size:14px"><strong>Remarks:</strong></td><td><? echo $dataArray[0][csf('remark')]; ?></td>
	</tr>
    </table>

    <div style="width:120%; height: <?php echo $divHeight; ?>">
			<?
			// $gray_dia_array=array(); $prod_dia_array=array();
			// if ($db_type==0)
			// {
			// 	$prod_dia_sql="select a.batch_id, a.cons_comp_id, a.process, a.dia_width from subcon_production_dtls a, subcon_production_qnty b where a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 group by a.batch_id, a.cons_comp_id, a.process ";
			// }
			// else if ($db_type==2)
			// {
			// 	$prod_dia_sql="select a.batch_id, a.cons_comp_id, cast(a.process as varchar2(100)) as process, a.dia_width from subcon_production_dtls a, subcon_production_qnty b where a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 group by a.batch_id, a.cons_comp_id, a.dia_width, a.process";
			// }
			// $result_prod_dia_sql=sql_select($prod_dia_sql);
			// foreach($result_prod_dia_sql as $row)
			// {
			// 	$prod_dia_array[$row[csf('batch_id')]][$row[csf('cons_comp_id')]]['dia_width']=$row[csf('dia_width')];
			// 	$prod_dia_array[$row[csf('batch_id')]][$row[csf('cons_comp_id')]]['process']=$row[csf('process')];
			// }
			// //print_r($prod_dia_array);
			// $inv_item_arr=return_library_array( "select id,material_description from sub_material_dtls",'id','material_description');
			// $prod_item_arr=return_library_array( "select id,fabric_description from subcon_production_dtls",'id','fabric_description');

			$sql="select id, party_id, challan_no, delivery_date, forwarder, transport_company,delivery_no,vehical_no from subcon_delivery_mst where id='$update_id' and company_id='$company' and status_active=1 and is_deleted=0";
			$dataArray=sql_select($sql);
			$mst_id=$dataArray[0][csf('id')];

			$mst_id=$dataArray[0][csf('id')];
			$sql_dtls="select batch_id, color_id, width_dia_type, dia, order_id, sub_process_id, item_id,sum(reject_qty) as reject_qty, sum(delivery_qty) as delivery_qty, sum(gray_qty) as gray_qty, sum(carton_roll) as carton_roll, gsm, remarks from subcon_delivery_dtls where mst_id='$mst_id' and process_id in (3,4) and status_active=1 group by batch_id, color_id, width_dia_type, gsm, dia, order_id, sub_process_id, item_id, remarks, id order by batch_id, sub_process_id, color_id";
		

			$i=1; $k=1; $width_dia_type_array=array(); $sub_process_array=array(); $batch_array=array(); $color_array=array();

			$dtls_value=sql_select($sql_dtls);
			foreach($dtls_value as $row)
				{
					$order_idArr[$row[csf('order_id')]]=$row[csf('order_id')];
				}
				$order_ids=implode(",",$order_idArr);
			?>
			<table style="margin-left:20px;"  align="left" cellspacing="0" width="1010"  border="1" rules="all" class="rpt_table" >
				<thead bgcolor="#dddddd" align="center" style="font-size:14px">
					<th width="20">SL</th>
					<th width="70" align="center">Order No</th>
					<th width="60" align="center">Cust. Buyer</th>
                    <th width="60" align="center">Cust. Style</th>
					<th width="70" align="center">Rec. Challan</th>
					<th width="110" align="center">Description</th>
					<th width="50" align="center">GSM</th>
					<th width="50" align="center">G/Dia</th>
					<th width="50" align="center">F/Dia</th>
					<th width="50" align="center">Roll</th>
					<th width="70" align="center">Grey Qty</th>
					<th width="70" align="center">Fin. Qty</th>
                    <th width="70" align="center">Rej. Qty</th>
					<th width="70" align="center">Process %</th>
					<th align="center">Remarks</th>
				</thead>
				<?
					$sql_batch="Select a.id, a.batch_no, a.extention_no, b.fabric_from, b.po_id, b.id as item_id, b.width_dia_type, b.item_description, sum(b.roll_no) as roll_no, sum(b.batch_qnty) as batch_qnty, b.rec_challan,a.color_range_id from  pro_batch_create_mst a, pro_batch_create_dtls b,gbl_temp_engine g where a.entry_form=36 and a.id=b.mst_id  and  b.po_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=72 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and group by a.id, a.batch_no, a.extention_no, a.process_id, b.fabric_from, b.item_description, b.po_id, b.id, b.width_dia_type, b.rec_challan,a.color_range_id";

					$batch_full_array=array();
					$result_batch=sql_select($sql_batch);
					foreach($result_batch as $row)
					{
						//$batch_array[$row[csf('po_id')]]['id']=$row[csf('id')];  *batch_no*batch_ext*color_id
						$item=explode(',',$row[csf('item_description')]);
						$batch_full_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('item_id')]]['item_description']=$row[csf('item_description')];
						$batch_full_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('item_id')]]['rec_challan']=$row[csf('rec_challan')];
						$batch_full_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('item_id')]]['dia']=$item[2];
						$batch_full_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('item_id')]]['roll_no']=$row[csf('roll_no')];
						$batch_full_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('item_id')]]['batch_qnty']=$row[csf('batch_qnty')];
						$batch_full_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('item_id')]]['fabric_from']=$row[csf('fabric_from')];
						$batch_full_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('item_id')]]['width_dia_type']=$row[csf('width_dia_type')];
						$batch_full_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('item_id')]]['batch_no']=$row[csf('batch_no')];
						$batch_full_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('item_id')]]['extention_no']=$row[csf('extention_no')];
						$batch_full_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('item_id')]]['color_range']=$row[csf('color_range_id')];
					}

				   /*$sql_fin_dia=sql_select("select order_id,dia_width from subcon_production_dtls where status_active=1 and is_deleted=0");
				   foreach($sql_fin_dia as $val)
				   {
				   	$fin_dia_arr[$val[csf("order_id")]]=$val[csf("dia_width")];

				   }*/

				//    $sql_fin_dia=sql_select("select order_id,batch_id,cons_comp_id, dia_width,fabric_description from subcon_production_dtls where status_active=1 and is_deleted=0");
				//    foreach($sql_fin_dia as $val)
				//    {
				  
				//  	if($val[csf("dia_width")]!="")
				//   	{
				// //	$fin_dia_arr[$val[csf("order_id")]][$val[csf("fabric_description")]]=$val[csf("dia_width")];
				// 	}
				//   }


				$sql_grey_dia=sql_select("select b.id, b.po_id, b.grey_dia,b.fin_dia from pro_batch_create_dtls b,gbl_temp_engine g  where b.order_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=72 and 1=1 ");
				   foreach($sql_grey_dia as $val)
				   {
				   	$grey_dia_arr[$val[csf("po_id")]][$val[csf("id")]]=$val[csf("grey_dia")];
					$fin_dia_arr[$val[csf("po_id")]][$val[csf("id")]]=$val[csf("fin_dia")];

				   }

				   $sql_grey_qty=sql_select("select b.order_id,b.process_loss from subcon_ord_breakdown b,gbl_temp_engine g  where b.order_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=72 and 1=1 ");
				   foreach($sql_grey_qty as $val)
				   {
				   	$grey_arr[$val[csf("order_id")]]=$val[csf("process_loss")];

				   }
				   execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id."   and ENTRY_FORM=72");
				   oci_commit($con);
				   disconnect($con);
					foreach($dtls_value as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$item_name=$batch_full_array[$row[csf('batch_id')]][$row[csf('po_id')]][$row[csf('item_id')]]['item_description'];
						
						$process_id=explode(',',$row[csf('sub_process_id')]);

						$process_val='';
						foreach ($process_id as $val)
						{
							//rsort($val);
							if($process_val=='') $process_val=$conversion_cost_head_array[$val]; else $process_val.="+".$conversion_cost_head_array[$val];
							//echo $val;
						}

						if ($batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['batch_no']!='' || $batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['extention_no']!='')
						{
							$batch_no=$batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['batch_no'].' '." Ex: ". $batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['extention_no'];
							$color_range_id=$batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['color_range'];
						}
						else
						{
							$batch_no=$batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['batch_no'];
							$color_range_id=$batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['color_range'];
							
						}
						$gsm_dia=explode(',',$batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['item_description']);
						if ($gsm_dia[1]!='' && $gsm_dia[1]!=0 ) $batch_dia=$gsm_dia[2]; else $batch_dia=$gsm_dia[2];

						$chack_string=$row[csf('batch_id')].$row[csf('color_id')].$row[csf('sub_process_id')];
						if(!in_array($chack_string,$sub_process_array))
						{
							if($i!=1)
							{
								$subProcessLoss=(($tot_grey_qty-$tot_finish_qty)/$tot_grey_qty)*100;
							?>
							<tr class="tbl_bottom" bgcolor="#CCCCCC" style="font-size:14px">
								<th width="20">&nbsp;</th>
								<th width="70">&nbsp;</th>
								<th width="60">&nbsp;</th>
                                <th width="60">&nbsp;</th>
								<th width="70">&nbsp;</th>
								<th width="110">&nbsp;</th>
								<th width="50">&nbsp;</th>
								<th width="50">&nbsp;</th>
								<th width="50"><strong>Total</strong></th>
								<th width="50" align="right"><? echo number_format($tot_roll,2,'.',''); ?>&nbsp;</th>
								<th width="70" align="right"><? echo number_format($tot_grey_qty,2,'.',''); ?>&nbsp;</th>
								<th width="70" align="right"><? echo number_format($tot_finish_qty,2,'.',''); ?>&nbsp;</th>
                                <th width="70" align="right"><? echo $tot_rej_qty; ?>&nbsp;</th>
								<th width="70" align="right" title="<?='(('.$tot_grey_qty.'-'.$tot_finish_qty.')/'.$tot_grey_qty.')*100'; ?>"><? echo number_format($subProcessLoss,2); ?>&nbsp;</th>
								<th>&nbsp;</th>
							</tr>
						<?
								unset($tot_roll);
								unset($tot_grey_qty);
								unset($tot_finish_qty);
								unset($tot_rej_qty);
								//unset($tot_proces_loss);
							}
						
						?>
							<tr height="30"><td colspan="15" style="font-size:15px" bgcolor="#CCCCAA"><p><?php echo "<i>Batch No: </i>" . $batch_no;
								echo "; <i>Color: </i>" . $color_arr[$row[csf('color_id')]];
								echo "; <i>Color Range: </i>" .$color_range[$color_range_id];
								echo "; <i>Process: </i>" . $process_val . ""; ?></p></td></tr>
						<?
							$sub_process_array[$i]=$chack_string;
							//unset($sub_process_array);
						}
						$dia_type='';
						if($batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['width_dia_type']==1) $dia_type="Open";
						else if($batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['width_dia_type']==2) $dia_type="Tube";
						else if($batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['width_dia_type']==3) $dia_type="Niddle";
						
						$item_description=$batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['item_description'];
						$fin_dia=$fin_dia_arr[$row[csf("order_id")]][$item_description];
						$fabric_desciption="";
						if($gsm_dia[1]!="") $fabric_desciption=$gsm_dia[0].','.$gsm_dia[1].'('.$dia_type.')'; else $fabric_desciption=$gsm_dia[0].'('.$dia_type.')';
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:14px">
						<td width="20"><? echo $i; ?></td>
						<td width="70" style="word-break:break-all"><? echo $job_po_array[$row[csf('order_id')]]['order_no']; ?></td>
						<td width="60" style="word-break:break-all"><? echo $job_po_array[$row[csf('order_id')]]['cust_buyer']; ?></td>
                        <td width="60" style="word-break:break-all"><? echo $job_po_array[$row[csf('order_id')]]['cust_style_ref']; ?></td>
						<td width="70" style="word-break:break-all"><? echo $batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['rec_challan']; ?></td>
						<td width="110" style="word-break:break-all"><? echo $fabric_desciption; ?></td>
						<td width="50" align="center" style="word-break:break-all"><? echo $row[csf('gsm')]; ?></td>
						<td width="50" align="center" style="word-break:break-all"><? echo $grey_dia_arr[$row[csf("order_id")]][$row[csf("item_id")]];  ?></td>
						<td width="50" align="center" style="word-break:break-all"><? echo $row[csf("dia")]; ?></td>
						<td width="50" align="right"><? echo $row[csf('carton_roll')]; ?>&nbsp;</td>

						<td width="70" align="right" style="word-break:break-all"><? echo number_format($row[csf('gray_qty')],2,'.',''); ?>&nbsp;</td>
						<td width="70" align="right" style="word-break:break-all"><? echo number_format($row[csf('delivery_qty')],2,'.',''); ?>&nbsp;</td>
                        <td width="70" align="right" style="word-break:break-all"><? echo $row[csf('reject_qty')]; ?>&nbsp;</td>
						<td width="70" align="right" style="word-break:break-all" title="<?='(('.$row[csf('gray_qty')].'-'.$row[csf('delivery_qty')].')/'.$row[csf('gray_qty')].')*100'; ?>">
								<?
								$proces_loss=(($row[csf('gray_qty')]-$row[csf('delivery_qty')])/$row[csf('gray_qty')])*100;
								echo number_format($proces_loss,2,'.','');
								?>
								&nbsp;
						</td>
						<td style="word-break:break-all"><? echo $row[csf('remarks')]; ?></td>
					</tr>
					<?
					$tot_roll+=$row[csf('carton_roll')];
					$tot_grey_qty+=$row[csf('gray_qty')];
					$tot_finish_qty+=$row[csf('delivery_qty')];


					$tot_rej_qty+=$row[csf('reject_qty')];
					$grand_tot_reject_qty+=$row[csf('reject_qty')];

					$grand_tot_roll+=$row[csf('carton_roll')];
					$grand_tot_grey_qty+=$row[csf('gray_qty')];
					//$grand_tot_grey_qty+=str_replace(",", "",number_format($row[csf('gray_qty')]));
					//$grand_tot_finish_qty+=str_replace(",", "",number_format($row[csf('delivery_qty')]));
					$grand_tot_finish_qty+=$row[csf('delivery_qty')];

					//$tot_proces_loss+=(($tot_grey_qty-$tot_finish_qty)/$tot_grey_qty)*100;
					$i++;
				}
				?>
					<tr class="tbl_bottom" bgcolor="#CCCCCC" style="font-size:14px">
						<th width="20">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="110">&nbsp;</th>
						<th width="50">&nbsp;</th>
						<th width="50">&nbsp;</th>
						<th width="50"><strong>Total</strong></th>
						<th width="50" align="right"><? echo number_format($tot_roll,2,'.',''); ?>&nbsp;</th>
						<th width="70" align="right"><? echo number_format($tot_grey_qty,2,'.',''); ?>&nbsp;</th>
						<th width="70" align="right"><? echo number_format($tot_finish_qty,2,'.',''); ?>&nbsp;</th>
                        <th width="70" align="right"><? echo $tot_rej_qty; ?>&nbsp;</th>
						<th width="70" align="right" title="<?='(('.$tot_grey_qty.'-'.$tot_finish_qty.')/'.$tot_grey_qty.')*100'; ?>">
							<?
							$subProcessLoss=(($tot_grey_qty-$tot_finish_qty)/$tot_grey_qty)*100;
								//$tot_proces_loss=(($tot_grey_qty-$tot_finish_qty)/$tot_grey_qty)*100;
								echo number_format($subProcessLoss,2,'.','');
							?>
							&nbsp;
						</th>
						<th>&nbsp;</th>
					</tr>
					<tfoot style="font-size:14px">
						<th width="20">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="110">&nbsp;</th>
						<th width="50">&nbsp;</th>
						<th width="50">&nbsp;</th>
						<th width="50"><strong>Grand Total</strong></th>
						<th width="50" align="right"><? echo number_format($grand_tot_roll,2,'.',''); ?>&nbsp;</th>
						<th width="70" align="right"><? echo number_format($grand_tot_grey_qty,2,'.',''); ?>&nbsp;</th>
						<th width="70" align="right"><? echo number_format($grand_tot_finish_qty,2,'.',''); ?>&nbsp;</th>
                        <th width="70" align="right"><? echo $grand_tot_reject_qty; ?>&nbsp;</th>
						<th width="70" align="right" title="<?='(('.$grand_tot_grey_qty.'-'.$grand_tot_finish_qty.')/'.$grand_tot_grey_qty.')*100'; ?>">
							<?
								$grand_tot_proces_loss=(($grand_tot_grey_qty-$grand_tot_finish_qty)/$grand_tot_grey_qty)*100;
								echo number_format($grand_tot_proces_loss,2,'.','');
							?>
							&nbsp;
						</th>
						<th>&nbsp;</th>
					</tfoot>
				</table>

				 <?
            echo signature_table(46, $company, "900px");
				 //echo signature_table(46, $company, "900px",$cbo_template_id,70,$user_lib_name[$inserted_by]);
			if($reportType == 1 || $reportType == 4 )
			{
         ?>
          </div>
    	<table width="900" cellspacing="0" >
        	<tr><td colspan="6">

            </td></tr>
            <tr><td colspan="6" align="center">,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,</td></tr>
            <tr>
				<td colspan="6">
                    <table cellpadding="0" cellspacing="0" >
                        <tr>
                            <td width="200" align="right">
                                <img  src='../../<? echo $imge_arr[str_replace("'","",$company)]; ?>' height='60%' width='60%' />
                            </td>
                            <td align="right">
                                <table width="800px" cellspacing="0" align="center">
                                    <tr>
                                        <td align="center" style="font-size:18px"><strong ><? echo $company_library[$company]; ?></strong></td>
                                    </tr>
                                    <tr class="form_caption">
                                        <td  align="center" style="font-size:14px">
                                        <?
											$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, contact_no, country_id, province, city, zip_code, contact_no, email, website, vat_number from lib_company where id=$company and status_active=1 and is_deleted=0");
											foreach ($nameArray as $result)
											{
												echo $result[csf('plot_no')]; ?> &nbsp;
												<? echo $result[csf('level_no')]?> &nbsp;
												<? echo $result[csf('road_no')]; ?> &nbsp;
												<? echo $result[csf('block_no')];?> &nbsp;
												<? echo $result[csf('city')];?> &nbsp;
												<? echo $result[csf('zip_code')]; ?> &nbsp;
												<? echo $result[csf('province')];?> &nbsp;
												<? echo $country_arr[$result[csf('country_id')]]; ?>&nbsp; <br>
												<? echo $result[csf('contact_no')];?> &nbsp;
												<? echo $result[csf('email')];?> &nbsp;
												<? echo $result[csf('website')]; ?> <br>
                                                <b> Vat No : <? echo $result[csf('vat_number')]; ?></b> <?
											}
                                        ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center" style="font-size:16px"><strong><u>Gate Pass</u></strong></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
    			</td>
            </tr>
            <tr>
                <?
                $party_add=$dataArray[0][csf('party_id')];
                $nameArray=sql_select( "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$party_add");
                foreach ($nameArray as $result)
                {
                    $address="";
                    if($result[csf('address_1')]!="") $address=$result[csf('address_1')];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
                }
                ?>
                <td width="300" rowspan="4" valign="top" colspan="2" style="font-size:14px"><strong>Party : <? echo $buyer_library[$party_add].'<br>'.'Party Address: '.$address;  ?></strong></td>
                <td width="120" style="font-size:14px"><strong>Challan No :</strong></td><td width="170px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
                <td width="120" style="font-size:14px"><strong>Delivery Date :</strong></td><td width="170px"><? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
            </tr>
            <tr>
                <td style="font-size:14px"><strong>Transport Com.:</strong></td><td><? echo $dataArray[0][csf('transport_company')]; ?></td>
                <td style="font-size:14px"><strong>Forwarder:</strong></td><td><? echo $supplier_library[$dataArray[0][csf('forwarder')]]; ?></td>
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
                               <td align="center"><?  echo fn_number_format($grand_tot_finish_qty,2); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
        &nbsp;
		<?php 
    	if ($reportType==4) {
    		echo signature_table(46, $company, '900px', '', 0);
    	} else {
        ?>
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
        <?php 
        	}
        ?>
	</div>
	<?
    }
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
	//$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library",'master_tble_id','image_location');
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_library=return_library_array( "select id,supplier_name from lib_supplier", "id","supplier_name");
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$country_arr=return_library_array( "select id, country_name from lib_country", "id", "country_name");
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");//die;
	//$item_arr=return_library_array( "select cons_comp_id, color_name from lib_color", "cons_comp_id", "color_name");
	//$color_arr=return_library_array( "select id, color_name from  lib_color", "id", "color_name"  );
	 
	//var_dump($job_po_array);
	// $recChallan_arr=array();
	// if($db_type==0)
	// {
	// 	$sql_rec="select b.order_id, group_concat(distinct(a.chalan_no)) as chalan_no, group_concat(distinct(b.grey_dia)) as grey_dia from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=1 group by b.order_id";
	// }
	// else if ($db_type==2)
	// {
	// 	$sql_rec="select b.order_id, wm_concat(distinct(cast(a.chalan_no as varchar2(500)))) as chalan_no, wm_concat(distinct(cast(b.grey_dia as varchar2(500)))) as grey_dia from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=1 group by b.order_id";
	// }
	// $result_sql_rec=sql_select($sql_rec);
	// foreach($result_sql_rec as $row)
	// {
	// 	$recChallan_arr[$row[csf('order_id')]]['chalan_no']=$row[csf('chalan_no')];
	// 	$recChallan_arr[$row[csf('order_id')]]['grey_dia']=$row[csf('grey_dia')];
	// }

	$sql="select id, party_id, challan_no, delivery_date, forwarder, transport_company,delivery_no,vehical_no,driver_name,mobile_no from subcon_delivery_mst where id='$update_id' and company_id='$company' and status_active=1 and is_deleted=0";

	$dataArray=sql_select($sql);
	$mst_id=$dataArray[0][csf('id')];

	$sql_job_po="select a.id, a.order_no, a.main_process_id, a.cust_buyer, a.cust_style_ref, b.party_id, b.subcon_job, a.process_id from  subcon_ord_dtls a, subcon_ord_mst b,subcon_delivery_dtls c where a.job_no_mst=b.subcon_job and a.id=c.order_id and c.mst_id=$mst_id and b.is_deleted=0 and b.status_active=1 and a.status_active=1 and a.is_deleted=0";
	$job_po_array=array();
	$result_job_po=sql_select($sql_job_po);
	foreach($result_job_po as $row)
	{
		$job_po_array[$row[csf('id')]]['order_no']=$row[csf('order_no')];
		$job_po_array[$row[csf('id')]]['main_process_id']=$row[csf('main_process_id')];
		$job_po_array[$row[csf('id')]]['process_id']=$row[csf('process_id')];
		$job_po_array[$row[csf('id')]]['party_id']=$row[csf('party_id')];
		$job_po_array[$row[csf('id')]]['subcon_job']=$row[csf('subcon_job')];
		$job_po_array[$row[csf('id')]]['cust_buyer']=$row[csf('cust_buyer')];
		$job_po_array[$row[csf('id')]]['cust_style_ref']=$row[csf('cust_style_ref')];
		$PoIdArr[$row[csf('id')]]=$row[csf('id')];
	}
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id."   and ENTRY_FORM=72");
	oci_commit($con);
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 72, 1, $PoIdArr, $empty_arr);//PO ID Ref from=1

	$com_dtls = fnc_company_location_address($company, $location, 2);
?>
    <div style="width:930px;">
   <table width="100%" cellpadding="0" cellspacing="0" >
       <tr>
           <td width="200" align="right">
               <img  src='../../<? echo $com_dtls[2]; ?>' height='60%' width='60%' />
           </td>
           <td>
    <table width="800" cellspacing="0" align="center">
        <tr>
            <td align="center" style="font-size:22px"><strong ><? echo $com_dtls[0]; ?></strong></td>
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
                        <? echo $result[csf('level_no')]?> &nbsp;
                        <? echo $result[csf('road_no')]; ?> &nbsp;
                        <? echo $result[csf('block_no')];?> &nbsp;
                        <? echo $result[csf('city')];?> &nbsp;
                        <? echo $result[csf('zip_code')]; ?> &nbsp;
                        <? echo $result[csf('province')];?> &nbsp;
                        <? echo $country_arr[$result[csf('country_id')]]; ?>&nbsp; <br>
                        <? echo $result[csf('contact_no')];?> &nbsp;
                        <? echo $result[csf('email')];?> &nbsp;
                        <? echo $result[csf('website')]; ?> <br>
                        <b> Vat No : <? echo $result[csf('vat_number')]; ?></b> <?
					}*/
                ?>
            </td>
        </tr>
        <tr>
            <td align="center" style="font-size:18px"><strong><? echo $production_process[$job_po_array[$dataArray[0][csf('order_id')]]['main_process_id']]; ?> Delivery Challan</strong></td>
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
			//	echo "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$party_add";
                $nameArray=sql_select( "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$party_add");
				 $address="";
                foreach ($nameArray as $result)
                {

                    if($result[csf('address_1')]!="") $address=$result[csf('address_1')];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
                }
            ?>
        	<td width="300" rowspan="4" valign="top" colspan="2" style="font-size:14px"><strong>Party : <? echo $buyer_library[$party_add].'<br>'.'Party Address: '.$address;  ?></strong></td>
            <td width="125" style="font-size:14px"><strong>Challan No :</strong></td><td width="170px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
            <td width="125" style="font-size:14px"><strong>Delivery Date :</strong></td><td width="170px"><? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
        </tr>
        <tr>
            <td style="font-size:14px"><strong>Transport Com.:</strong></td><td><? echo $dataArray[0][csf('transport_company')]; ?></td>
            <td style="font-size:14px"><strong>Forwarder:</strong></td><td><? echo $supplier_library[$dataArray[0][csf('forwarder')]]; ?></td>
        </tr>
        <tr>
		<td style="font-size:14px"><strong>Vehicle No:</strong></td><td><? echo $dataArray[0][csf('vehical_no')]; ?></td>
		<td colspan="4"></td> </tr>
    </table>

    <div style="width:120%; height:1010px">
			<?
			// $gray_dia_array=array(); $prod_dia_array=array();
			// if ($db_type==0)
			// {
			// 	$prod_dia_sql="select a.batch_id, a.cons_comp_id, a.process, a.dia_width from subcon_production_dtls a, subcon_production_qnty b where a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 group by a.batch_id, a.cons_comp_id, a.process ";
			// }
			// else if ($db_type==2)
			// {
			// 	$prod_dia_sql="select a.batch_id, a.cons_comp_id, cast(a.process as varchar2(100)) as process, a.dia_width from subcon_production_dtls a, subcon_production_qnty b where a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 group by a.batch_id, a.cons_comp_id, a.dia_width, a.process";
			// }
			// $result_prod_dia_sql=sql_select($prod_dia_sql);
			// foreach($result_prod_dia_sql as $row)
			// {
			// 	$prod_dia_array[$row[csf('batch_id')]][$row[csf('cons_comp_id')]]['dia_width']=$row[csf('dia_width')];
			// 	$prod_dia_array[$row[csf('batch_id')]][$row[csf('cons_comp_id')]]['process']=$row[csf('process')];
			// }
			// //print_r($prod_dia_array);
			// $inv_item_arr=return_library_array( "select id,material_description from sub_material_dtls",'id','material_description');
			// $prod_item_arr=return_library_array( "select id,fabric_description from subcon_production_dtls",'id','fabric_description');


			 
			$sql_dtls="select batch_id, color_id, width_dia_type, dia, order_id, sub_process_id, item_id,sum(reject_qty) as reject_qty, sum(delivery_qty) as delivery_qty, sum(gray_qty) as gray_qty, sum(carton_roll) as carton_roll, gsm, remarks, sum(moisture_gain) as moisture_gain from subcon_delivery_dtls where mst_id='$mst_id' and process_id in (3,4) group by batch_id, color_id, width_dia_type, gsm, dia, order_id, sub_process_id, item_id, remarks, id order by batch_id, sub_process_id, color_id";

			$i=1; 
			$k=1; 
			$width_dia_type_array=array(); 
			$sub_process_array=array(); 
			$batch_array=array(); 
			$color_array=array();

			$dtls_value=sql_select($sql_dtls);
			?>
			<table style="margin-left:20px;"  align="left" cellspacing="0" width="1150"  border="1" rules="all" class="rpt_table" >
				<thead bgcolor="#dddddd" align="center" style="font-size:14px">
					<th width="20">SL</th>
					<th width="70" align="center">Order No</th>
					<th width="60" align="center">Cust. Buyer</th>
                    <th width="60" align="center">Cust. Style</th>
					<th width="70" align="center">Rec. Challan</th>
					<th width="110" align="center">Description</th>
					<th width="50" align="center">GSM</th>
					<th width="50" align="center">G/Dia</th>
					<th width="50" align="center">F/Dia</th>
					<th width="50" align="center">Roll</th>
					<th width="70" align="center">Grey Qty</th>
					<th width="70" align="center">Fin. Qty</th>
                    <th width="70" align="center">Rej. Qty</th>
					<th width="70" align="center">Process %</th>
                    <th width="70" align="center">Mos. Gain Qty (Kg)</th>
                    <th width="70" align="center">Ttl. With Gain (Kg)</th>
					<th align="center">Remarks</th>
				</thead>
				<?
					$sql_batch="Select a.id, a.batch_no, a.extention_no, b.fabric_from, b.po_id, b.id as item_id, b.width_dia_type, b.item_description, sum(b.roll_no) as roll_no, sum(b.batch_qnty) as batch_qnty, b.rec_challan from  pro_batch_create_mst a, pro_batch_create_dtls b,gbl_temp_engine g  where a.entry_form=36 and a.id=b.mst_id  and  b.po_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=72  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no, a.extention_no, a.process_id, b.fabric_from, b.item_description, b.po_id, b.id, b.width_dia_type, b.rec_challan";
					$batch_full_array=array();
					$result_batch=sql_select($sql_batch);
					foreach($result_batch as $row)
					{
						//$batch_array[$row[csf('po_id')]]['id']=$row[csf('id')];  *batch_no*batch_ext*color_id
						$item=explode(',',$row[csf('item_description')]);
						$batch_full_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('item_id')]]['item_description']=$row[csf('item_description')];
						$batch_full_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('item_id')]]['rec_challan']=$row[csf('rec_challan')];
						$batch_full_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('item_id')]]['dia']=$item[2];
						$batch_full_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('item_id')]]['roll_no']=$row[csf('roll_no')];
						$batch_full_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('item_id')]]['batch_qnty']=$row[csf('batch_qnty')];
						$batch_full_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('item_id')]]['fabric_from']=$row[csf('fabric_from')];
						$batch_full_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('item_id')]]['width_dia_type']=$row[csf('width_dia_type')];
						$batch_full_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('item_id')]]['batch_no']=$row[csf('batch_no')];
						$batch_full_array[$row[csf('id')]][$row[csf('po_id')]][$row[csf('item_id')]]['extention_no']=$row[csf('extention_no')];
					}

				   /*$sql_fin_dia=sql_select("select order_id,dia_width from subcon_production_dtls where status_active=1 and is_deleted=0");
				   foreach($sql_fin_dia as $val)
				   {
				   	$fin_dia_arr[$val[csf("order_id")]]=$val[csf("dia_width")];

				   }*/

				//    $sql_fin_dia=sql_select("select order_id,batch_id,cons_comp_id, dia_width,fabric_description from subcon_production_dtls where status_active=1 and is_deleted=0");
				//    foreach($sql_fin_dia as $val)
				//    {
				//   //	$fin_dia_arr[$val[csf("order_id")]][$val[csf("cons_comp_id")]]=$val[csf("dia_width")];
				//  	if($val[csf("dia_width")]!="")
				//   	{
				// //	$fin_dia_arr[$val[csf("order_id")]][$val[csf("fabric_description")]]=$val[csf("dia_width")];
				// 	}
				//   }


				$sql_grey_dia=sql_select("select b.id, b.po_id, b.grey_dia,b.fin_dia from pro_batch_create_dtls b,gbl_temp_engine g  where b.order_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=72 and 1=1 ");
				   foreach($sql_grey_dia as $val)
				   {
				   	$grey_dia_arr[$val[csf("po_id")]][$val[csf("id")]]=$val[csf("grey_dia")];
					$fin_dia_arr[$val[csf("po_id")]][$val[csf("id")]]=$val[csf("fin_dia")];

				   }

				   $sql_grey_qty=sql_select("select b.order_id,b.process_loss from subcon_ord_breakdown b,gbl_temp_engine g  where b.order_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=72 and 1=1 ");
				   foreach($sql_grey_qty as $val)
				   {
				   	$grey_arr[$val[csf("order_id")]]=$val[csf("process_loss")];

				   }
				   execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id."   and ENTRY_FORM=72");
				   oci_commit($con);
				   disconnect($con);
					foreach($dtls_value as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$item_name=$batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['item_description'];
						$process_id=explode(',',$row[csf('sub_process_id')]);

						$process_val='';
						foreach ($process_id as $val)
						{
							//rsort($val);
							if($process_val=='') $process_val=$conversion_cost_head_array[$val]; else $process_val.="+".$conversion_cost_head_array[$val];
							//echo $val;
						}

						if ($batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['batch_no']!='' || $batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['extention_no']!='')
						{
							$batch_no=$batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['batch_no'].' '." Ex: ". $batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['extention_no'];
						}
						else
						{
							$batch_no=$batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['batch_no'];
						}
						$gsm_dia=explode(',',$batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['item_description']);
						if ($gsm_dia[1]!='' && $gsm_dia[1]!=0 ) $batch_dia=$gsm_dia[2]; else $batch_dia=$gsm_dia[2];

						$chack_string=$row[csf('batch_id')].$row[csf('color_id')].$row[csf('sub_process_id')];
						if(!in_array($chack_string,$sub_process_array))
						{
							if($i!=1)
							{
								$subProcessLoss=(($tot_grey_qty-$tot_finish_qty)/$tot_grey_qty)*100;
							?>
							<tr class="tbl_bottom" bgcolor="#CCCCCC" style="font-size:14px">
								<th width="20">&nbsp;</th>
								<th width="70">&nbsp;</th>
								<th width="60">&nbsp;</th>
                                <th width="60">&nbsp;</th>
								<th width="70">&nbsp;</th>
								<th width="110">&nbsp;</th>
								<th width="50">&nbsp;</th>
								<th width="50">&nbsp;</th>
								<th width="50"><strong>Total</strong></th>
								<th width="50" align="right"><? echo number_format($tot_roll,2,'.',''); ?>&nbsp;</th>
								<th width="70" align="right"><? echo number_format($tot_grey_qty,2,'.',''); ?>&nbsp;</th>
								<th width="70" align="right"><? echo number_format($tot_finish_qty,2,'.',''); ?>&nbsp;</th>
                                <th width="70" align="right"><? echo $tot_rej_qty; ?>&nbsp;</th>
								<th width="70" align="right" title="<?='(('.$tot_grey_qty.'-'.$tot_finish_qty.')/'.$tot_grey_qty.')*100'; ?>"><? echo number_format($subProcessLoss,2); ?>&nbsp;</th>
								<th width="70" align="right"><? echo number_format($tot_moisture_gain,2,'.',''); ?>&nbsp;</th>
								<th width="70" align="right"><? echo number_format($tot_with_gain,2,'.',''); ?>&nbsp;</th>
                                <th>&nbsp;</th>
							</tr>
						<?
								unset($tot_roll);
								unset($tot_grey_qty);
								unset($tot_finish_qty);
								unset($tot_rej_qty);
								
								unset($tot_moisture_gain);
								unset($tot_with_gain);
								//unset($tot_proces_loss);
							}
						?>
							<tr height="30"><td colspan="17" style="font-size:15px" bgcolor="#CCCCAA"><p><?php echo "<i>Batch No: </i>" . $batch_no;
echo "; <i>Color: </i>" . $color_arr[$row[csf('color_id')]];
echo "; <i>Process: </i>" . $process_val . ""; ?></p></td></tr>
						<?
							$sub_process_array[$i]=$chack_string;
							//unset($sub_process_array);
						}
						$dia_type='';
						if($batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['width_dia_type']==1) $dia_type="Open";
						else if($batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['width_dia_type']==2) $dia_type="Tube";
						else if($batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['width_dia_type']==3) $dia_type="Niddle";
						
						$item_description=$batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['item_description'];
						$fin_dia=$fin_dia_arr[$row[csf("order_id")]][$item_description];
						$fabric_desciption="";
						if($gsm_dia[1]!="") $fabric_desciption=$gsm_dia[0].','.$gsm_dia[1].'('.$dia_type.')'; else $fabric_desciption=$gsm_dia[0].'('.$dia_type.')';
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:14px">
						<td width="20"><? echo $i; ?></td>
						<td width="70" style="word-break:break-all"><? echo $job_po_array[$row[csf('order_id')]]['order_no']; ?></td>
						<td width="60" style="word-break:break-all"><? echo $job_po_array[$row[csf('order_id')]]['cust_buyer']; ?></td>
                        <td width="60" style="word-break:break-all"><? echo $job_po_array[$row[csf('order_id')]]['cust_style_ref']; ?></td>
						<td width="70" style="word-break:break-all"><? echo $batch_full_array[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('item_id')]]['rec_challan']; ?></td>
						<td width="110" style="word-break:break-all"><? echo $fabric_desciption; ?></td>
						<td width="50" align="center" style="word-break:break-all"><? echo $row[csf('gsm')]; ?></td>
						<td width="50" align="center" style="word-break:break-all"><? echo $grey_dia_arr[$row[csf("order_id")]][$row[csf("item_id")]];  ?></td>
						<td width="50" align="center" style="word-break:break-all"><? echo $row[csf("dia")]; ?></td>
						<td width="50" align="right"><? echo $row[csf('carton_roll')]; ?>&nbsp;</td>

						<td width="70" align="right" style="word-break:break-all"><? echo number_format($row[csf('gray_qty')],2,'.',''); ?>&nbsp;</td>
						<td width="70" align="right" style="word-break:break-all"><? echo number_format($row[csf('delivery_qty')],2,'.',''); ?>&nbsp;</td>
                        <td width="70" align="right" style="word-break:break-all"><? echo $row[csf('reject_qty')]; ?>&nbsp;</td>
						<td width="70" align="right" style="word-break:break-all" title="<?='(('.$row[csf('gray_qty')].'-'.$row[csf('delivery_qty')].')/'.$row[csf('gray_qty')].')*100'; ?>">
								<?
								$proces_loss=(($row[csf('gray_qty')]-$row[csf('delivery_qty')])/$row[csf('gray_qty')])*100;
								echo number_format($proces_loss,2,'.','');
								?>
								&nbsp;
						</td>
						<td width="70" align="right" style="word-break:break-all"><? echo number_format($row[csf('moisture_gain')],2,'.',''); ?>&nbsp;</td>
						<td width="70" align="right" style="word-break:break-all"><? echo number_format(($row[csf('delivery_qty')]*1)+($row[csf('moisture_gain')]*1),2,'.',''); ?>&nbsp;</td>
                        <td style="word-break:break-all"><? echo $row[csf('remarks')]; ?></td>
					</tr>
					<?
					$tot_roll+=$row[csf('carton_roll')];
					$tot_grey_qty+=$row[csf('gray_qty')];
					$tot_finish_qty+=$row[csf('delivery_qty')];
					
					$tot_moisture_gain+=$row[csf('moisture_gain')];
					$tot_with_gain+=$row[csf('delivery_qty')]*1+$row[csf('moisture_gain')]*1;
					
					$grand_tot_moisture_gain+=$row[csf('moisture_gain')];
					$grand_tot_with_gain+=$row[csf('delivery_qty')]*1+$row[csf('moisture_gain')]*1;

					$tot_rej_qty+=$row[csf('reject_qty')];
					$grand_tot_reject_qty+=$row[csf('reject_qty')];

					$grand_tot_roll+=$row[csf('carton_roll')];
					$grand_tot_grey_qty+=str_replace(",", "",number_format($row[csf('gray_qty')]));
					$grand_tot_finish_qty+=str_replace(",", "",number_format($row[csf('delivery_qty')]));
					//$tot_proces_loss+=(($tot_grey_qty-$tot_finish_qty)/$tot_grey_qty)*100;
					$i++;
				}
				?>
					<tr class="tbl_bottom" bgcolor="#CCCCCC" style="font-size:14px">
						<th width="20">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="110">&nbsp;</th>
						<th width="50">&nbsp;</th>
						<th width="50">&nbsp;</th>
						<th width="50"><strong>Total</strong></th>
						<th width="50" align="right"><? echo number_format($tot_roll,2,'.',''); ?>&nbsp;</th>
						<th width="70" align="right"><? echo number_format($tot_grey_qty,2,'.',''); ?>&nbsp;</th>
						<th width="70" align="right"><? echo number_format($tot_finish_qty,2,'.',''); ?>&nbsp;</th>
                        <th width="70" align="right"><? echo $tot_rej_qty; ?>&nbsp;</th>
						<th width="70" align="right" title="<?='(('.$tot_grey_qty.'-'.$tot_finish_qty.')/'.$tot_grey_qty.')*100'; ?>">
							<?
							$subProcessLoss=(($tot_grey_qty-$tot_finish_qty)/$tot_grey_qty)*100;
								//$tot_proces_loss=(($tot_grey_qty-$tot_finish_qty)/$tot_grey_qty)*100;
								echo number_format($subProcessLoss,2,'.','');
							?>
							&nbsp;
						</th>
						<th width="70" align="right"><? echo number_format($tot_moisture_gain,2,'.',''); ?>&nbsp;</th>
						<th width="70" align="right"><? echo number_format($tot_with_gain,2,'.',''); ?>&nbsp;</th>
						<th>&nbsp;</th>
					</tr>
					<tfoot style="font-size:14px">
						<th width="20">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="110">&nbsp;</th>
						<th width="50">&nbsp;</th>
						<th width="50">&nbsp;</th>
						<th width="50"><strong>Grand Total</strong></th>
						<th width="50" align="right"><? echo number_format($grand_tot_roll,2,'.',''); ?>&nbsp;</th>
						<th width="70" align="right"><? echo number_format($grand_tot_grey_qty,2,'.',''); ?>&nbsp;</th>
						<th width="70" align="right"><? echo number_format($grand_tot_finish_qty,2,'.',''); ?>&nbsp;</th>
                        <th width="70" align="right"><? echo $grand_tot_reject_qty; ?>&nbsp;</th>
						<th width="70" align="right" title="<?='(('.$grand_tot_grey_qty.'-'.$grand_tot_finish_qty.')/'.$grand_tot_grey_qty.')*100'; ?>">
							<?
								$grand_tot_proces_loss=(($grand_tot_grey_qty-$grand_tot_finish_qty)/$grand_tot_grey_qty)*100;
								echo number_format($grand_tot_proces_loss,2,'.','');
							?>
							&nbsp;
						</th>
						<th width="70" align="right"><? echo number_format($grand_tot_moisture_gain,2,'.',''); ?>&nbsp;</th>
						<th width="70" align="right"><? echo number_format($grand_tot_with_gain,2,'.',''); ?>&nbsp;</th>
                        <th>&nbsp;</th>
					</tfoot>
				</table> <br>

				 <?
            echo signature_table(46, $company, "900px");
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
                    <table cellpadding="0" cellspacing="0" >
                        <tr>
                            <td width="200" align="right">
                                <img  src='../../<? echo $imge_arr[str_replace("'","",$company)]; ?>' height='60%' width='60%' />
                            </td>
                            <td align="right">
                                <table width="800px" cellspacing="0" align="center">
                                    <tr>
                                        <td align="center" style="font-size:18px"><strong ><? echo $company_library[$company]; ?></strong></td>
                                    </tr>
                                    <tr class="form_caption">
                                        <td  align="center" style="font-size:14px">
                                        <?
											$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, contact_no, country_id, province, city, zip_code, contact_no, email, website, vat_number from lib_company where id=$company and status_active=1 and is_deleted=0");
											foreach ($nameArray as $result)
											{
												echo $result[csf('plot_no')]; ?> &nbsp;
												<? echo $result[csf('level_no')]?> &nbsp;
												<? echo $result[csf('road_no')]; ?> &nbsp;
												<? echo $result[csf('block_no')];?> &nbsp;
												<? echo $result[csf('city')];?> &nbsp;
												<? echo $result[csf('zip_code')]; ?> &nbsp;
												<? echo $result[csf('province')];?> &nbsp;
												<? echo $country_arr[$result[csf('country_id')]]; ?>&nbsp; <br>
												<? echo $result[csf('contact_no')];?> &nbsp;
												<? echo $result[csf('email')];?> &nbsp;
												<? echo $result[csf('website')]; ?> <br>
                                                <b> Vat No : <? echo $result[csf('vat_number')]; ?></b> <?
											}
                                        ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center" style="font-size:16px"><strong><u>Gate Pass</u></strong></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
    			</td>
            </tr>
            <tr>
                <?
                $party_add=$dataArray[0][csf('party_id')];
                $nameArray=sql_select( "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$party_add");
                foreach ($nameArray as $result)
                {
                    $address="";
                    if($result[csf('address_1')]!="") $address=$result[csf('address_1')];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
                }
                ?>
                <td width="300" rowspan="4" valign="top" colspan="2" style="font-size:14px"><strong>Party : <? echo $buyer_library[$party_add].'<br>'.'Party Address: '.$address;  ?></strong></td>
                <td width="120" style="font-size:14px"><strong>Challan No :</strong></td><td width="170px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
                <td width="120" style="font-size:14px"><strong>Delivery Date :</strong></td><td width="170px"><? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
            </tr>
            <tr>
                <td style="font-size:14px"><strong>Transport Com.:</strong></td><td><? echo $dataArray[0][csf('transport_company')]; ?></td>
                <td style="font-size:14px"><strong>Forwarder:</strong></td><td><? echo $supplier_library[$dataArray[0][csf('forwarder')]]; ?></td>
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
        &nbsp;<br>
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
    exit();
}
?>