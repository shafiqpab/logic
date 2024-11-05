<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_machine")
{
	if($db_type==2)
	{
		echo create_drop_down( "cbo_machine_name", 172, "select id,machine_no || '-' || brand as machine_name from lib_machine_name where category_id=2 and company_id=$data and status_active=1 and is_deleted=0 and is_locked=0 order by seq_no","id,machine_name", 1,"-- Select Machine --", $selected, "","" );
	}
	else if($db_type==0)
	{
		echo create_drop_down( "cbo_machine_name", 172, "select id,concat(machine_no,'-',brand) as machine_name from lib_machine_name where category_id=2 and company_id=$data and status_active=1 and is_deleted=0 and is_locked=0 order by  seq_no","id,machine_name", 1, "-- Select Machine --", $selected, "","" );
	}
	exit();
}
if ($action=="load_drop_down_floor")
	{
		echo create_drop_down( "cbo_floor", 172, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and production_process=3 and company_id='$data' order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "load_drop_down( 'requires/trims_batch_creation_controller',this.value, 'load_drop_machine', 'td_dyeing_machine' );",0 );

		exit();     	 
	} 	
if ($action=="load_drop_down_trims_item_desc")
{
	
	if($db_type==2)
	{
		echo create_drop_down( "txtitemDesc_1", 200, "select a.item_name || '-' || b.description as id, a.item_name || '-' || b.description as description from   lib_item_group a,wo_pre_cost_trim_cost_dtls b where b.trim_group=a.id and b.status_active=1 and b.is_deleted=0 and b.job_no='$data' order by a.id","id,description", 1,"-- Select description --", $selected, "", "", "", "", "", "", "", "", "txtitemDesc[]" );
	}
	else if($db_type==0)
	{
		echo create_drop_down( "txtitemDesc_1", 200, "select concat(a.item_name,'-',b.description) as id , concat(a.item_name,'-',b.description) as description from   lib_item_group a,wo_pre_cost_trim_cost_dtls b where b.trim_group=a.id and b.status_active=1 and b.is_deleted=0 and b.job_no='$data' order by a.id","id,description", 1,"-- Select description --", $selected, "","" );
		
	}
	exit();
}

if ($action == "trim_batch_card_print") {
	extract($_REQUEST);
	$data = explode('*', $data);
	$company = $data[0];
	$batch_update_id = $data[1];
	$batch_sl_no = $data[2];
	$job_no = $data[6];
	//print_r($data);die;
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and color_name  is not null", 'id', 'color_name');
	$buyer_arr = return_library_array("select id,buyer_name from lib_buyer", 'id', 'buyer_name');
	$machine_no_arr = return_library_array("select id,machine_no from lib_machine_name", 'id', 'machine_no');

	$job_array2 = array();
	$job_sql = "select distinct(a.buyer_name) as buyer_name,a.style_ref_no, a.job_no_prefix_num, a.job_no, b.pub_shipment_date, b.id, b.po_number, b.grouping, b.file_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$job_sql_result = sql_select($job_sql);
	foreach ($job_sql_result as $row) {
		$job_array2[$row[csf('job_no')]]['po_number'] .= $row[csf('po_number')].',';
		$job_array2[$row[csf('job_no')]]['buyer_name'] = $row[csf('buyer_name')];
		$job_array2[$row[csf('job_no')]]['grouping'] = $row[csf('grouping')];
		$job_array2[$row[csf('job_no')]]['file_no'] = $row[csf('file_no')];
		$job_array2[$row[csf('job_no')]]['pub_shipment_date'] = $row[csf('pub_shipment_date')];
		$job_array2[$row[csf('job_no')]]['style_ref_no'] = $row[csf('style_ref_no')];
	}
	if ($db_type == 0) {
		$sql = "select a.id, a.batch_no,a.working_company_id, a.batch_date, a.booking_no_id, a.booking_no,a.booking_without_order,a.color_id, a.batch_against, a.batch_for,a.company_id,a.job_no, a.color_range_id, a.organic,a.dyeing_machine, a.extention_no, a.total_trims_weight, a.process_id as process_id, a.batch_weight, a.remarks, a.collar_qty, a.cuff_qty from pro_batch_create_mst a where a.id=$batch_update_id and a.status_active=1 and a.is_deleted=0 group by a.id, a.batch_no,a.working_company_id, a.batch_date, a.booking_no_id, a.booking_no,a.booking_without_order,a.color_id, a.batch_against, a.batch_for,a.company_id,a.job_no, a.color_range_id, a.organic,a.dyeing_machine, a.extention_no, a.total_trims_weight, a.process_id, a.batch_weight, a.remarks, a.collar_qty, a.cuff_qty";
	} else {
		$sql = "select a.id, a.batch_no,a.working_company_id, a.batch_date, a.booking_no_id, a.booking_no,a.booking_without_order,a.color_id, a.batch_against, a.batch_for,a.company_id,a.job_no, a.color_range_id, a.organic,a.dyeing_machine, a.extention_no, a.total_trims_weight, a.process_id as process_id, a.batch_weight, a.remarks, a.collar_qty, a.cuff_qty from pro_batch_create_mst a where a.id=$batch_update_id and a.status_active=1 and a.is_deleted=0 group by a.id,a.working_company_id, a.batch_no, a.batch_date, a.booking_no_id, a.booking_no,a.booking_without_order,a.color_id, a.batch_against, a.batch_for,a.company_id,a.job_no, a.color_range_id, a.organic,a.dyeing_machine, a.extention_no, a.total_trims_weight, a.process_id, a.batch_weight, a.remarks, a.collar_qty, a.cuff_qty";
	}
	//echo $sql;
	$dataArray = sql_select($sql);
	$order_no=rtrim($job_array2[$dataArray[0][csf('job_no')]]['po_number'],",");
    $order_nos=implode(", ", array_unique(explode(",", $order_no)));
	$working_company_id=$dataArray[0][csf('working_company_id')];
	?>
    <div style="width:980px;">
        <table width="980" cellspacing="0" align="center" border="0">
            <tr>
                <td colspan="6" align="center" style="font-size:22px">
                   <strong>Working Company:<? echo $company_library[$working_company_id].'<br>'.'LC Company:'.$company_library[$company]; ?></strong></td>
                <td colspan="2" align="left">Print Time:<? echo $date = date("F j, Y, g:i a"); ?></td>
            </tr>
            <tr>
                <td colspan="6" align="center" style="font-size:18px"><strong><u>Trims Batch Card</u></strong></td>
                <td colspan="2" id="barcode_img_id" align="right" style="font-size:24px"></td>
            </tr>
            <tr>
                <td width="110"><strong>Batch No</strong></td>
                <td width="135px">:&nbsp;<? echo $dataArray[0][csf('batch_no')]; ?></td>
                <td width="110"><strong>Batch SL</strong></td>
                <td width="135px">:&nbsp;<? echo $batch_sl_no; ?></td>
                <td width="110"><strong>B. Color</strong></td>
                <td width="135px">:&nbsp;<? echo $color_arr[$dataArray[0][csf('color_id')]]; ?></td>
                <td width="110"><strong>Color Ran.</strong></td>
                <td width="135px">:&nbsp;<? echo $color_range[$dataArray[0][csf('color_range_id')]]; ?></td>
            </tr>
            <tr>
                <td><strong>Batch Against</strong></td>
                <td>:&nbsp;<? echo $batch_against[$dataArray[0][csf('batch_against')]]; ?></td>
                <td><strong>Batch Ext.</strong></td>
                <td>:&nbsp;<? echo $dataArray[0][csf('extention_no')]; ?></td>
                <td><strong>Batch For</strong></td>
                <td>:&nbsp;<? echo $batch_for[$dataArray[0][csf('batch_for')]]; ?></td>
                <td><strong>B. Weight</strong></td>
                <td>:&nbsp;<? echo $dataArray[0][csf('batch_weight')]; ?></td>
            </tr>
            <tr>
                <td><strong>Buyer</strong></td>
                <td>:&nbsp;<? echo $buyer_arr[$job_array2[$dataArray[0][csf('job_no')]]['buyer_name']]; ?></td>
                <td><strong>Job</strong></td>
                <td>:&nbsp;<? echo $dataArray[0][csf('job_no')]; ?></td>
				<td><strong>Order No</strong></td>
                <td>:&nbsp;<? echo $order_nos; ?></td>
                <td><strong>Ship Date</strong></td>
                <td>:&nbsp;<? echo $job_array2[$dataArray[0][csf('job_no')]]['pub_shipment_date']; ?></td>
            </tr>
            <tr>
                <td><strong>Collar Qty (Pcs)</strong></td>
                <td>:&nbsp;<? echo $dataArray[0][csf('collar_qty')]; ?></td>
                <td><strong>Cuff Qty (Pcs)</strong></td>
                <td>:&nbsp;<? echo $dataArray[0][csf('cuff_qty')]; ?></td>
                <td><strong>Int. Ref.</strong></td>
                <td>:&nbsp;<? echo $job_array2[$dataArray[0][csf('job_no')]]['grouping']; ?></td>
                <td><strong>File No</strong></td>
                <td>:&nbsp;<? echo $job_array2[$dataArray[0][csf('job_no')]]['file_no']; ?></td>
            </tr>
            <tr>
                <td><strong>Dying Machine</strong></td>
                <td>:&nbsp;<? echo $machine_no_arr[$dataArray[0][csf('dyeing_machine')]]; ?></td>
                <td><strong>Remarks</strong></td>
                <td>:&nbsp;<? echo $dataArray[0][csf('remarks')]; ?></td>
                <td><strong>Booking No.</strong></td>
                <td>:&nbsp;<? echo $dataArray[0][csf('booking_no')]; ?></td>
                <td><strong>Style Ref.</strong></td>
                <td>:&nbsp;<? echo $job_array2[$dataArray[0][csf('job_no')]]['style_ref_no']; ?></td>
            </tr>
        </table>
        <br/>


        <table align="center" cellspacing="0" width="980" border="1" rules="all" class="rpt_table"
               style="border-top:none">
            <thead bgcolor="#dddddd" align="center">
            <tr>
            	<td align="left" style="font-size:  20px;" colspan="4"><strong>Item Details</strong></td>
            </tr>
            <tr>
	            <th width="50">SL</th>
	            <th width="350" align="center">Item Description</th>
	            <th width="250" align="center">Weight In Kg</th>
	            <th width="" align="center">Remarks</th>
	        </tr>
        </thead>
        <tbody>
        <?
        $sql="select  b.id, b.item_description as item_desc, b.trims_wgt_qnty, b.remarks from pro_batch_create_mst a, pro_batch_trims_dtls b where a.id=b.mst_id and b.mst_id=$batch_update_id and b.status_active=1 and b.is_deleted=0 order by b.id";
		$data_array=sql_select($sql);
		$i=1;
		foreach($data_array as $row)
		{				
        ?>
			<tr>
				<td align="center"><?echo $i;?></td>
				<td align="left"><?echo $row[csf('item_desc')];?></td>
				<td align="center"><?echo $row[csf('trims_wgt_qnty')];?></td>
				<td align="center"><?echo $row[csf('remarks')];?></td>
			</tr>
		<?
		$i++;
		$total_trims_wgt+=$row[csf('trims_wgt_qnty')];
		//echo $total_trims_wgt;
		}
		?>
            <tr>
                <td align="right" colspan="2"><strong>Total</strong></td>
                <td align="center"><?echo $total_trims_wgt; ?></td>
                <td align="center">&nbsp;</td>
            </tr>
        </tbody>
        </table>
        <br/><br/><br/>
        <table width="980" cellspacing="0" align="center">
            <tr>
                <td width="480" valign="top">
                    <table width="475" cellspacing="0" border="1" rules="all" class="rpt_table">
                        <tr>
                            <th align="left"><strong>Shade Result(<i>Hand Written</i>)</strong></th>
                        </tr>
                        <tr>
                            <td colspan="1" style="width:475px; height:80px">&nbsp;</td>
                        </tr>
                    </table>
                </td>
                <td width="10" align="justify" valign="top">&nbsp;</td>
                <td width="480" valign="top" align="right">
                    <table cellspacing="0" border="1" rules="all" class="rpt_table" width="475">
                        <tr>
                            <th align="left" colspan="3"><strong>Shrinkage(<i>Hand Written</i>)</strong></th>
                        </tr>
                        <tr>
                            <th><b>Length % </b></th>
                            <th><b>Width % </b></th>
                            <th><b> Twist % </b></th>
                        </tr>
                        <tr height="30">
                            <td>&nbsp; </td>
                            <td>&nbsp; </td>
                            <td>&nbsp; </td>
                        </tr>
                        <tr height="30">
                            <td>&nbsp; </td>
                            <td>&nbsp; </td>
                            <td>&nbsp; </td>
                        </tr>
                        <tr height="30">
                            <td>&nbsp; </td>
                            <td>&nbsp; </td>
                            <td>&nbsp; </td>
                        </tr>
                        <tr height="30">
                            <td>&nbsp; </td>
                            <td>&nbsp; </td>
                            <td>&nbsp; </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td width="980" colspan="3">
                    <table cellspacing="0" border="1" rules="all" class="rpt_table" width="980">
                        <tr>
			            	<td align="center" style="height: 30px;" colspan="4"><strong>Dyeing & Finishing Information(<i>Hand Written</i>)</strong></td>
			            </tr>
			            <tr>
				            <th align="center">Dyeing</th>
				            <th align="center">Finishing</th>
				        </tr>
                        <tr>
                            <td style="width:980px; height:120px">&nbsp;</td>
                            <td style="width:980px; height:120px">&nbsp;</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <br>
		<?
		echo signature_table(52, $company, "980px");
		?>
    </div>
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
        function generateBarcode(valuess) {
            var value = valuess;//$("#barcodeValue").val();
            //alert(value)
            var btype = 'code39';//$("input[name=btype]:checked").val();
            var renderer = 'bmp';// $("input[name=renderer]:checked").val();

            var settings = {
                output: renderer,
                bgColor: '#FFFFFF',
                color: '#000000',
                barWidth: 1,
                barHeight: 30,
                moduleSize: 5,
                posX: 10,
                posY: 20,
                addQuietZone: 1
            };
            //$("#barcode_img_id").html('11');
            value = {code: value, rect: false};

            $("#barcode_img_id").show().barcode(value, btype, settings);
        }
        generateBarcode('<? echo $batch_mst_update_id; ?>');
    </script>
	<?
	exit();
}

if($action=="trim_batch_card_print___") 
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$company_id = $data[0];
	$update_id = $data[1];
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");

	$sql="select id, batch_no, batch_date, batch_against, batch_for, company_id, job_no, extention_no, color_id, batch_weight,color_range_id, process_id, organic,booking_without_order, dur_req_hr, dur_req_min, collar_qty, cuff_qty, dyeing_machine, remarks, inserted_by, insert_date from pro_batch_create_mst where id=$update_id and status_active=1 and is_deleted=0 ";	
	$result=sql_select($sql);
	foreach ($result as $row)
	{
		
	}
?>
<div style="width:1120px;">
    <table cellspacing="0" style="font: 12px tahoma; width: 100%;">
        <tr>
            <td colspan="2"></td>
            <td colspan="4" align="center" style="font-size:24px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
            <td colspan="2" align="center" style="font-size:12px">Print time: <? echo date("F j, Y, g:i a"); ?></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="8" align="center" style="font-size:14px"> 
            </td>  
        </tr>
        <tr>
            <td colspan="8" align="center" style="font-size:20px;"><u><strong>Trims Batch Card</strong></u></td>
        </tr>
        <tr>
            <td width="80" ><strong>Batch No</strong></td><td width="120">: <? echo $row[csf("batch_no")];?></td>
            <td width="80" ><strong>Batch SL</strong></td><td width="120" > :  </td>
            <td width="80" ><strong>B. Color</strong></td><td width="120" >: </td>
            <td width="80" ><strong>Color Ran.</strong></td><td width="120" >: </td>
        </tr>
        <tr>
            <td width="80" ><strong>Batch Against</strong></td><td width="120">: </td>
            <td width="80" ><strong>Batch Ext.</strong></td><td width="120" > :  </td>
            <td width="80" ><strong>Batch For</strong></td><td width="120" >: </td>
            <td width="80" ><strong>B. Weight</strong></td><td width="120" >: </td>
        </tr>
        <tr>
            <td width="80" ><strong>Buyer</strong></td><td width="120">: </td>
            <td width="80" ><strong>Job</strong></td><td width="120" > :  </td>
            <td width="80" ><strong>Order No</strong></td><td width="120" >: </td>
            <td width="80" ><strong>Ship Date</strong></td><td width="120" >: </td>
        </tr>
        <tr>
            <td width="80" ><strong>Collar Qty (Pcs)</strong></td><td width="120">: </td>
            <td width="80" ><strong>Cuff Qty (Pcs)</strong></td><td width="120" > :  </td>
            <td width="80" ><strong>Int. Ref.</strong></td><td width="120" >: </td>
            <td width="80" ><strong>File No</strong></td><td width="120" >: </td>
        </tr>
        <tr>
            <td width="80" ><strong>Dying Machine</strong></td><td width="120">: </td>
            <td width="80" ><strong>Remarks</strong></td><td width="120" > :  </td>
            <td width="80" ><strong>Booking No</strong></td><td width="120" >: </td>
            <td width="80" ><strong>Style Ref</strong></td><td width="120" >: </td>
        </tr>       
    </table><br/>
    <div style="width:100%; height: 300px;">
    <table cellspacing="0" width="1120" border="1" rules="all" class="rpt_table" style="font: 12px tahoma;">
        <thead bgcolor="#dddddd" align="center">
            <tr>
            	<td align="left" style="font-size:  20px;" colspan="4"><strong>Item Details</strong></td>
            </tr>
            <tr>
	            <th width="50">SL</th>
	            <th width="350" align="center">Item Description</th>
	            <th width="250" align="center">Weight In Kg</th>
	            <th width="" align="center">Remarks</th>
	        </tr>
        </thead>
        <tbody>
			<tr>
				<td>n</td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
        </tbody>             
    </table>
    </div>
    <br/>

    <div>
    <table cellspacing="0" width="500" border="1" rules="all" class="rpt_table" style="font: 12px tahoma; float: left;">
        <thead bgcolor="#dddddd" align="center">
            <tr>
	            <th align="center" style="height: 30px;">Shade Result(Hand Written)</th>
	        </tr>
        </thead>
        <tbody>
			<tr>
				<td>h</td>
			</tr>
        </tbody>               
    </table>
    

    <table cellspacing="0" width="450" border="1" rules="all" class="rpt_table" style="font: 12px tahoma;  float: right;">
        <thead bgcolor="#dddddd" align="center">            
            <tr>
	            <th align="center" colspan="3" style="height: 30px;">Shrinkage(Hand Written)</th>
	        </tr>
	        <tr>
				<th>Length %</th>
				<th>Width %</th>
				<th>Twist %</th>
			</tr>
        </thead>
        <tbody>
			<tr>
				<td>E</td>
				<td>H</td>
				<td>e</td>
			</tr>
        </tbody>                  
    </table>
    <br clear="all" />
    </div>
    <br />
    <div>
    <table cellspacing="0" width="1120" border="1" rules="all" class="rpt_table" style="font: 12px tahoma;">
        <thead bgcolor="#dddddd" align="center">
            <tr>
            	<td align="center" style="height: 30px;" colspan="4"><strong>Dyeing & Finishing Information(Hand Written)</strong></td>
            </tr>
            <tr>
	            <th align="center">Dyeing</th>
	            <th align="center">Finishing</th>
	        </tr>
        </thead>
        <tbody>
			<tr>
				<td>n</td>
				<td></td>
			</tr>
        </tbody>             
    </table>

    </div>
	</div>
   	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
<script>
	function generateBarcode( valuess ){
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
		
			 value = {code:value, rect: false};
			$("#barcode_img_id").show().barcode(value, btype, settings);
		} 
	    generateBarcode('<? echo $dataArray[0][csf('sys_number')]; ?>');
	 </script>
<?
exit();	
}

if ($action=="job_popup")
{
	echo load_html_head_contents("WO Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
?> 
	<script>
	
		function js_set_value(po_id,job_no)
		{
			//alert(type);
			
			$('#hidden_po_id').val(po_id);
			$('#hidden_job_no').val(job_no);
			//$('#booking_without_order').val(type);
			parent.emailwindow.hide();
		}
	
    </script>
</head>

<body>
<div align="center" style="width:962px;">
	<form name="searchwofrm"  id="searchwofrm" autocomplete=off>
		<fieldset style="width:100%;">
		<legend>Enter search words</legend>           
            <table cellpadding="0" cellspacing="0" width="840" class="rpt_table" border="1" rules="all">
                <thead>
                    <th>Buyer</th>
                    <th>Booking Type</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="200"><?php echo "Enter Order No";  ?></th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
                    	
                        <input type="hidden" name="hidden_po_id" id="hidden_po_id" class="text_boxes" value="">  
                        <input type="hidden" name="hidden_job_no" id="hidden_job_no" class="text_boxes" value="">
                       
                    </th> 
                </thead>
                <tr>
                    <td align="center">
                    	<?
							echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",$data[0] ); 
						?>       
                    </td>
                    <td align="center">
                    	<?
							if($batch_against==3)
							{
								$disabled=0;
							}
							else
							{
								$disabled=1;
							}
							$booking_type=array(1=>"With Order",2=>"Without Order");
							echo create_drop_down( "cbo_booking_type", 140, $booking_type,"", 0, "-- All --", '', '',$disabled); 
						?>       
                    </td>
                    <td align="center">	
                    	<?
                    		if($batch_against == 7)
							{
								$disabled = 1;
								$selected = 7;
							}
							else
							{
								$disabled = 0;
								$selected = 1;
							}
                       		$search_by_arr=array(1=>"Buyer Order",2=>"Job No",3=>"Internal Ref.",4=>"File No");
							$dd="change_search_event(this.value, '0*0*0*3*0*0', '0*0*0*3*0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", $selected, $dd, $disabled );
						?>
                    </td>                 
                    <td align="center" id="search_by_td">				
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td> 						
            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+'<? echo $batch_against; ?>'+'_'+document.getElementById('cbo_booking_type').value, 'create_job_search_list_view', 'search_div', 'trims_batch_creation_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                     </td>
                </tr>
           </table>
          <div style="width:100%; margin-top:10px; margin-left:3px" id="search_div" align="left"></div>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_job_search_list_view")
{
	$data = explode("_",$data);
	
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$company_id =$data[2];
	$buyer_id =$data[3];
	$batch_against =$data[4];
	$booking_type =$data[5];

	if($buyer_id==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_samp_cond=" and s.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_samp_cond="";
		}
		else
		{
			$buyer_id_cond="";
			//$buyer_id_samp_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$buyer_id";
		//$buyer_id_samp_cond=" and s.buyer_id=$buyer_id";
	}
	
	if(trim($data[0])!="")
	{
		 if($search_by==1)	
			$search_field_cond="and b.po_number like '$search_string'";
		else if($search_by==2)	
			$search_field_cond="and a.job_no like '$search_string'";
		else if($search_by==3)	
			$search_field_cond="and b.grouping like '$search_string'";
		else if($search_by==4)	
			$search_field_cond="and b.file_no like '$search_string'";		
		
	}
	else
	{
		$search_field_cond="";
	}	
	
	$color_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and color_name  is not null",'id','color_name');
	
	
		$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
		
		
		if($batch_against==3)
		{
			$booking_type_cond=" and a.booking_type=4";
		}
		else
		{
			$booking_type_cond=" and a.booking_type<>4";	
		}
		
		
			$sql= "SELECT a.id, a.job_no, a.style_ref_no, a.buyer_name, b.id as po_id, b.po_number,b.grouping,b.file_no,b.job_no_mst FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no=b.job_no_mst and a.company_name=$company_id and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0  $buyer_id_cond $search_field_cond ";
		$result = sql_select($sql);
	?>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="730" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="130">Job No</th>
                <th width="120">Buyer</th>
                <th width="130">Style Ref.</th>
                <th width="100">Internal Ref.</th>
                <th width="100">File No</th>
                <th>Buyer Order</th>
            </thead>
        </table>
        <div style="width:750px; max-height:270px; overflow-y:scroll" id="list_container_batch" align="left">	 
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="730" class="rpt_table" id="tbl_list_search">  
            <?
                $i=1;
				foreach ($result as $row)
				{  
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	 
					$po_no=''; $intl_ref=''; $file_no='';
					$po_id=array_unique(explode(",",$row[csf('po_id')]));
					foreach($po_id as $id)
					{
						if($po_no=="") 
						{
							$po_no=$po_number_array[$id]['no'];
							if($po_number_array[$id]['ref']!="") $intl_ref=$po_number_array[$id]['ref'];
							if($po_number_array[$id]['file_no']>0) $file_no=$po_number_array[$id]['file_no'];
						}
						else 
						{
							$po_no.=",".$po_number_array[$id]['no'];
							if($po_number_array[$id]['ref']!="") $intl_ref.=",".$po_number_array[$id]['ref'];
							if($po_number_array[$id]['file_no']>0) $file_no.=",".$po_number_array[$id]['file_no'];
						}
					}
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf('po_id')]; ?>,'<? echo $row[csf('job_no')]; ?>');"> 
						<td width="30"><? echo $i; ?></td>
						<td width="130"><p><? echo $row[csf('job_no')]; ?></p></td>
						           
						<td width="120"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></p></td>
						<td width="130" align="center"><p><? echo $row[csf('style_ref_no')]; ?>&nbsp;</p></td>
						
						<td width="100"><p><? echo $row[csf('grouping')] ?>&nbsp;</p></td>
						<td width="100"><p><? echo $row[csf('file_no')]; ?>&nbsp;</p></td>
						<td><p><? echo $row[csf('po_number')]; ?>&nbsp;</p></td>
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

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	

	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	
	/*$po_batch_no_arr=array();
	$po_batch_data=sql_select("select max(a.po_batch_no) as po_batch_no, a.po_id, b.color_id from pro_batch_create_dtls a, pro_batch_create_mst b where a.mst_id=b.id group by b.color_id, a.po_id");
	foreach($po_batch_data as $row)
	{
		$po_batch_no_arr[$row[csf('color_id')]][$row[csf('po_id')]]=$row[csf('po_batch_no')];
	}*/
	
	if(str_replace("'","",$txt_ext_no)!="" || $db_type==0)
	{
		$extention_no_cond="extention_no=$txt_ext_no";
	}
	else 
	{
		$extention_no_cond="extention_no is null";
	}
	
	if($operation==0)  // Insert Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		
		$batch_update_id=''; $batch_no_creation=str_replace("'","",$batch_no_creation); $roll_maintained=str_replace("'","",$roll_maintained);
		//$color_id=return_id( $txt_batch_color, $color_arr, "lib_color", "id,color_name");
        if(str_replace("'","",$txt_batch_color)!="")
        {
            if (!in_array(str_replace("'","",$txt_batch_color),$new_array_color))
            {
                $color_id = return_id( str_replace("'","",$txt_batch_color), $color_arr, "lib_color", "id,color_name","136");
                //echo $$txtColorName.'='.$color_id.'<br>';
                $new_array_color[$color_id]=str_replace("'","",$txt_batch_color);

            }
            else $color_id =  array_search(str_replace("'","",$txt_batch_color), $new_array_color);
        }
        else
        {
            $color_id=0;
        }
		
		if(str_replace("'","",$update_id)=="")
		{
			//$id=return_next_id( "id", "pro_batch_create_mst", 1 ) ;
			$id = return_next_id_by_sequence("PRO_BATCH_CREATE_MST_PK_SEQ", "pro_batch_create_mst", $con);
			$batch_update_id=$id;
			$serial_no=date("y",strtotime($pc_date_time))."-".$id;
			
		 	if($batch_no_creation==1)
			{
				//$txt_batch_number="'".$serial_no."'";
				$txt_batch_number="'".$id."'";
			}
			else
			{
				if(is_duplicate_field( "batch_no", "pro_batch_create_mst", "batch_no=$txt_batch_number and $extention_no_cond and status_active=1 and is_deleted=0" )==1)
				{
					//check_table_status( $_SESSION['menu_id'],0);
					echo "11**0"; 
                    disconnect($con);
					die;			
				}
				
				$txt_batch_number=$txt_batch_number;
			}
			//cbo_machine_name
			//$batch_against=str_replace("'","",$cbo_batch_against);
			//if($batch_against!=5) $without_job=0;else $without_job=1;
			$field_array="id, batch_no,entry_form, batch_date, batch_against, batch_for, company_id,working_company_id,floor_id,job_no, extention_no, color_id, batch_weight,color_range_id, process_id, organic,booking_without_order, dur_req_hr, dur_req_min, collar_qty, cuff_qty, dyeing_machine, remarks, inserted_by, insert_date";
			
			$data_array="(".$id.",".$txt_batch_number.",136,".$txt_batch_date.",".$cbo_batch_against.",".$cbo_batch_for.",".$cbo_company_id.",".$cbo_working_company_id.",".$cbo_floor.",".$txt_job_no.",".$txt_ext_no.",".$color_id.",".$txt_batch_weight.",".$cbo_color_range.",".$txt_process_id.",".$txt_organic.",".$hidden_booking_without_order.",".$txt_du_req_hr.",".$txt_du_req_min.",".$txt_color_qty.",".$txt_cuff_qty.",".$cbo_machine_name.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			//echo "insert into pro_batch_create_mst (".$field_array.") values ".$data_array;die;
			/*$rID=sql_insert("pro_batch_create_mst",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0;*/
		}
		else
		{
			$batch_update_id=str_replace("'","",$update_id);
			$serial_no=str_replace("'","",$txt_batch_sl_no);
			
			if($batch_no_creation!=1)
			{
				if(is_duplicate_field( "batch_no", "pro_batch_create_mst", "batch_no=$txt_batch_number and $extention_no_cond and id<>$update_id and status_active=1 and is_deleted=0" )==1)
				{
					//check_table_status( $_SESSION['menu_id'],0);
					echo "11**0"; 
                    disconnect($con);
					die;			
				}
			}
			
			$field_array_update="batch_no*entry_form*batch_date*batch_against*batch_for*company_id*working_company_id*floor_id*job_no*extention_no*color_id*batch_weight*color_range_id*process_id*organic*booking_without_order*dur_req_hr*dur_req_min*collar_qty*cuff_qty*dyeing_machine*remarks*updated_by*update_date";
			
			$data_array_update=$txt_batch_number."*136*".$txt_batch_date."*".$cbo_batch_against."*".$cbo_batch_for."*".$cbo_company_id."*".$cbo_working_company_id."*".$cbo_floor."*".$txt_job_no."*".$txt_ext_no."*".$color_id."*".$txt_batch_weight."*".$cbo_color_range."*".$txt_process_id."*".$txt_organic."*".$booking_without_order."*".$txt_du_req_hr."*".$txt_du_req_min."*".$txt_color_qty."*".$txt_cuff_qty."*".$cbo_machine_name."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			/*$rID=sql_update("pro_batch_create_mst",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID) $flag=1; else $flag=0; */
		}
		
		
		//$id_dtls_trim=return_next_id( "id","pro_batch_trims_dtls", 1 ) ;
		$field_array_dtls_trims="id,mst_id,item_description,trims_wgt_qnty,remarks,inserted_by, insert_date,status_active,is_deleted"; 
		
		
		for($i=1;$i<=$total_row;$i++)
		{
			$id_dtls_trim = return_next_id_by_sequence("PRO_BATCH_TRIMS_DTLS_PK_SEQ", "pro_batch_trims_dtls", $con);
			$txtitemDesc="txtitemDesc_".$i;
			$trimsWeight="trimsWeight_".$i;
			$trim_remarks="remarks_".$i;
			$trims_weight=str_replace("'","",$$trimsWeight);
		
			//if($trims_weight>0)
			//{
				if ($data_array_dtls_trims!="") $data_array_dtls_trims.=",";
				$data_array_dtls_trims.="(".$id_dtls_trim.",".$batch_update_id.",".$$txtitemDesc.",".$trims_weight.",".$$trim_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)"; 
				//$id_dtls_trim=$id_dtls_trim+1;
			//}
			
			//$id_dtls_trim=$id_dtls_trim+1;
		}
		
		//echo "10**insert into pro_batch_trims_dtls (".$field_array_dtls_trims.") values ".$data_array_dtls_trims;die;
		
		
		if(str_replace("'","",$update_id)=="")
		{
			$rID=sql_insert("pro_batch_create_mst",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0;
		}
		else
		{
			$rID=sql_update("pro_batch_create_mst",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID) $flag=1; else $flag=0; 
		}
		
		//echo "10**insert into pro_batch_create_mst (".$field_array.") values ".$data_array;die;
		
		
		if($data_array_dtls_trims!="")
		{
			//echo "insert into pro_batch_trims_dtls (".$field_array_dtls_trims.") values ".$data_array_dtls_trims;die;
			$rID4=sql_insert("pro_batch_trims_dtls",$field_array_dtls_trims,$data_array_dtls_trims,1);
			if($flag==1)
			{
				if($rID4) $flag=1; else $flag=0; 
			}
		}
		//echo "10**".$flag;die;
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".$batch_update_id."**".$serial_no."**".str_replace("'","",$txt_batch_number)."**".str_replace("'","",$total_row);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);   
				echo "0**".$batch_update_id."**".$serial_no."**".str_replace("'","",$txt_batch_number)."**".str_replace("'","",$total_row);
			}
			else
			{
				oci_rollback($con);
				echo "5**0**0";
			}
		}
			
		//check_table_status( $_SESSION['menu_id'],0);		
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
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		/*$prev_batch_data_arr=array();
		$prev_batch_data=sql_select("select a.id as dtls_id, a.po_id, b.color_id from pro_batch_create_dtls a, pro_batch_trims_dtls b where a.mst_id=b.id and b.id=$update_id");
		foreach($prev_batch_data as $row)
		{
			$prev_batch_data_arr[$row[csf('dtls_id')]]['po_id']=$row[csf('po_id')];
			$prev_batch_data_arr[$row[csf('dtls_id')]]['color']=$row[csf('color_id')];
		}*/

		//$color_id=return_id( $txt_batch_color, $color_arr, "lib_color", "id,color_name");
        if(str_replace("'","",$txt_batch_color)!="")
        {
            if (!in_array(str_replace("'","",$txt_batch_color),$new_array_color))
            {
                $color_id = return_id( str_replace("'","",$txt_batch_color), $color_arr, "lib_color", "id,color_name","136");
                //echo $$txtColorName.'='.$color_id.'<br>';
                $new_array_color[$color_id]=str_replace("'","",$txt_batch_color);

            }
            else $color_id =  array_search(str_replace("'","",$txt_batch_color), $new_array_color);
        }
        else
        {
            $color_id=0;
        }
		$flag=1; $batch_no_creation=str_replace("'","",$batch_no_creation); $roll_maintained=str_replace("'","",$roll_maintained);
		
		if(str_replace("'","",$cbo_batch_against)==2 && str_replace("'","",$hide_update_id)=="")
		{
			//$id=return_next_id( "id", "pro_batch_create_mst", 1 ) ;
			$id = return_next_id_by_sequence("PRO_BATCH_CREATE_MST_PK_SEQ", "pro_batch_create_mst", $con);
			$batch_update_id=$id;
			$serial_no=date("y",strtotime($pc_date_time))."-".$id;
					 
		 	if(is_duplicate_field( "batch_no", "pro_batch_create_mst", "batch_no=$txt_batch_number and $extention_no_cond and status_active=1 and is_deleted=0" )==1)
			{
				//check_table_status( $_SESSION['menu_id'],0);
				echo "11**0"; 
                disconnect($con);
				die;			
			}
			//cbo_machine_name 
			$field_array="id, batch_no,entry_form, batch_date, batch_against, batch_for, company_id,working_company_id,floor_id, job_no, extention_no, color_id, batch_weight, color_range_id, process_id, organic,booking_without_order, dur_req_hr, dur_req_min, re_dyeing_from, collar_qty, cuff_qty, dyeing_machine, remarks, inserted_by, insert_date";
			
			$data_array="(".$id.",".$txt_batch_number.",136,".$txt_batch_date.",".$cbo_batch_against.",".$cbo_batch_for.",".$cbo_company_id.",".$cbo_working_company_id.",".$cbo_floor.",".$txt_job_no.",".$txt_ext_no.",".$color_id.",".$txt_batch_weight.",".$cbo_color_range.",".$txt_process_id.",".$txt_organic.",".$hidden_booking_without_order.",".$txt_du_req_hr.",".$txt_du_req_min.",".$update_id.",".$txt_color_qty.",".$txt_cuff_qty.",".$cbo_machine_name.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					 
			//echo "insert into pro_batch_create_mst (".$field_array.") values ".$data_array;die;
			/*$rID=sql_insert("pro_batch_create_mst",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0;*/
			
			//$id_dtls=return_next_id( "id","pro_batch_trims_dtls", 1 ) ;
			$field_array_dtls="id,mst_id,item_description,trims_wgt_qnty,remarks,inserted_by,insert_date,status_active,is_deleted"; 
			

			for($i=1;$i<=$total_row;$i++)
			{
			$id_dtls = return_next_id_by_sequence("PRO_BATCH_TRIMS_DTLS_PK_SEQ", "pro_batch_trims_dtls", $con);
			$txtitemDesc="txtitemDesc_".$i;
			$trimsWeight="trimsWeight_".$i;
			$trim_remarks="remarks_".$i;
			$trims_weight=str_replace("'","",$$trimsWeight);
			$updateIdDtls="updateIdDtls_".$i;
			
				if ($i!=0) $data_array_dtls.=",";
					$data_array_dtls.="(".$id_dtls.",".$batch_update_id.",".$$txtitemDesc.",".$trims_weight.",".$$trim_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)"; 
					//$id_dtls=$id_dtls+1;
				
				
			}
			
			
			
			$rID=sql_insert("pro_batch_create_mst",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0;
			
			//echo "10**insert into pro_batch_trims_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
			$rID2=sql_insert("pro_batch_trims_dtls",$field_array_dtls,$data_array_dtls,1);
			//echo "10**$rID**$rID2";die;
			if($flag==1) 
			{
				if($rID2) $flag=1; else $flag=0; 
			} 
			
			
		}
		else
		{
			$poBatchNoArr=array();
			$batch_update_id=str_replace("'","",$update_id);
			$serial_no=str_replace("'","",$txt_batch_sl_no);
			
			if($batch_no_creation!=1)
			{
				if(is_duplicate_field( "batch_no", "pro_batch_create_mst", "batch_no=$txt_batch_number and $extention_no_cond and id<>$update_id and status_active=1 and is_deleted=0" )==1)
				{
					//check_table_status( $_SESSION['menu_id'],0);
					echo "11**0"; 
                    disconnect($con);
					die;			
				}
			}
			
			$field_array_update="batch_no*batch_date*batch_against*batch_for*company_id*working_company_id*floor_id*job_no*booking_without_order*extention_no*color_id*batch_weight*color_range_id*process_id*organic*dur_req_hr*dur_req_min*collar_qty*cuff_qty*dyeing_machine*remarks*updated_by*update_date";
			
			$data_array_update=$txt_batch_number."*".$txt_batch_date."*".$cbo_batch_against."*".$cbo_batch_for."*".$cbo_company_id."*".$cbo_working_company_id."*".$cbo_floor."*".$txt_job_no."*".$hidden_booking_without_order."*".$txt_ext_no."*".$color_id."*".$txt_batch_weight."*".$cbo_color_range."*".$txt_process_id."*".$txt_organic."*".$txt_du_req_hr."*".$txt_du_req_min."*".$txt_color_qty."*".$txt_cuff_qty."*".$cbo_machine_name."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
		/*$rID=sql_update("pro_batch_create_mst",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID) $flag=1; else $flag=0;*/
			
			
			
		
			//$id_dtls_trim=return_next_id( "id","pro_batch_trims_dtls", 1 ) ;
			$field_array_dtls="id,mst_id,item_description,trims_wgt_qnty,remarks,inserted_by,insert_date,status_active,is_deleted"; 
			$field_array_dtls_update="item_description*trims_wgt_qnty*remarks*updated_by*update_date"; 
			for($i=1;$i<=$total_row;$i++)
			{
				$txtitemDesc="txtitemDesc_".$i;
				$trimsWeight="trimsWeight_".$i;
				$trim_remarks="remarks_".$i;
				$trims_weight=str_replace("'","",$$trimsWeight);
				$updateIdDtls="updateIdDtls_".$i;
				$id_dtls_trim = return_next_id_by_sequence("PRO_BATCH_TRIMS_DTLS_PK_SEQ", "pro_batch_trims_dtls", $con);
				if(str_replace("'","",$$updateIdDtls)!="")
				{
					$id_arr[]=str_replace("'",'',$$updateIdDtls);
					$data_array_dtls_update[str_replace("'",'',$$updateIdDtls)] = explode("*",($$txtitemDesc."*".$trims_weight."*".$$trim_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					
					$id_dtls=str_replace("'",'',$$updateIdDtls);
				}
				else
				{
					if ($data_array_dtls!="") $data_array_dtls.=",";
					$data_array_dtls.="(".$id_dtls_trim.",".$batch_update_id.",".$$txtitemDesc.",".$trims_weight.",".$$trim_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)"; 
					
					//$id_dtls_trim=$id_dtls_trim+1;
					$id_dtls=$id_dtls_trim;
				}
			
				
			}
			//echo "insert into pro_batch_trims_dtls (".$field_array_dtls_trims.") values ".$data_array_dtls_trims;die;
			//echo bulk_update_sql_statement( "pro_batch_create_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $id_arr );die;
			//echo "10**0**insert into pro_batch_trims_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
			//echo "10**$rID**$rID2**$rID3";die;
			$dtlsUpdate_id_array=array();
			$sql_dtls="Select id from pro_batch_trims_dtls where mst_id=$update_id and status_active=1 and is_deleted=0";
			$nameArray=sql_select( $sql_dtls );
			foreach($nameArray as $row)
			{
				$dtlsUpdate_id_array[]=$row[csf('id')];
			}
			if(implode(',',$id_arr)!="")
			{
				$distance_delete_id=array_diff($dtlsUpdate_id_array,$id_arr);
			}
			else
			{
				$distance_delete_id=$dtlsUpdate_id_array;
			}
			//print_r($distance_delete_id);
			$field_array_del="status_active*is_deleted*updated_by*update_date";
			$data_array_del="'0'*'1'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			if(implode(',',$distance_delete_id)!="")
			{
				foreach($distance_delete_id as $id_val)
				{
					$rID=sql_update("pro_batch_trims_dtls",$field_array_del,$data_array_del,"id","".$id_val."",1);
					//if($rID) $flag=1; else $flag=0;
				}
			}
			$rID=sql_update("pro_batch_create_mst",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID) $flag=1; else $flag=0;
			if($data_array_dtls_update!="")
			{
				$rID2=execute_query(bulk_update_sql_statement( "pro_batch_trims_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $id_arr ));
				if($flag==1) 
				{
					if($rID2) $flag=1; else $flag=0; 
				} 
			}
			
			if($data_array_dtls!="")
			{
				
				$rID3=sql_insert("pro_batch_trims_dtls",$field_array_dtls,$data_array_dtls,1);
				if($flag==1) 
				{
					if($rID3) $flag=1; else $flag=0; 
				} 
			}
			
		}

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".$batch_update_id."**".$serial_no."**".str_replace("'","",$txt_batch_number)."**".str_replace("'","",$total_row);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**0**1";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".$batch_update_id."**".$serial_no."**".str_replace("'","",$txt_batch_number)."**".str_replace("'","",$total_row);
			}
			else
			{
				oci_rollback($con);
				echo "6**0**1";
			}
		}
		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;
	}
	else if ($operation==2) // Not Used Delete Here----------------------------------------------------------
	{
		$con = connect(); 
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		$sql="select id from pro_fab_subprocess where batch_id=$update_id and entry_form in(32,35) and status_active=1 and is_deleted=0";
		$data_array=sql_select($sql,1);
		if(count($data_array)>0)
		{
			echo "13**".str_replace("'","",$update_id);
            disconnect($con);
            die;
		}
		
		$field_array_status="updated_by*update_date*status_active*is_deleted";
		$data_array_status=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
		$changeStatus = sql_update("pro_batch_create_mst",$field_array_status,$data_array_status,"id",$update_id,1);
		$changeStatus4 = sql_update("pro_batch_trims_dtls",$field_array_status,$data_array_status,"mst_id",$update_id,1);
		
		//echo $changeStatus."&&".$changeStatus2."&&".$changeStatus3;die;
		if($db_type==0)
		{
			if($changeStatus && $changeStatus4)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "7**".str_replace("'","",$update_id);

			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($changeStatus && $changeStatus4)
			{
				oci_commit($con); 
				echo "2**".str_replace("'","",$update_id);
			}
			else
			{
				oci_rollback($con);
				echo "7**".str_replace("'","",$update_id);
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="batch_popup")
{
  	echo load_html_head_contents("Batch Info","../../", 1, 1, '','1','');
	extract($_REQUEST);
?>
	<script>
	
		function js_set_value( batch_id)
		{
			document.getElementById('hidden_batch_id').value=batch_id;
			parent.emailwindow.hide();
		}
	
    </script>
</head>
<body>
<div align="center">
	<fieldset style="width:1030px;margin-left:4px;">
        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table cellpadding="0" cellspacing="0" width="500" border="1" rules="all" class="rpt_table">
                <thead>
                    <th>Search By</th>
                    <th>Search</th>
                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        <input type="hidden" name="hidden_batch_id" id="hidden_batch_id" value="">
                    </th> 
                </thead>
                <tr class="general">
                    <td align="center">	
                        <?
                            $search_by_arr=array(1=>"Batch No",2=>"Job No");
                            echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                        ?>
                    </td>                 
                    <td align="center">				
                        <input type="text" style="width:140px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td> 						
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+<? echo $batch_against; ?>, 'create_batch_search_list_view', 'search_div', 'trims_batch_creation_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                    </td>
                </tr>
            </table>
            <div id="search_div" style="margin-top:10px"></div>   
        </form>
    </fieldset>
</div>    
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_batch_search_list_view")
{
	$data=explode('_',$data);
	
	$search_string="%".trim($data[0])."%";
	$search_by =$data[1];
	$company_id =$data[2];
	$batch_against_id=$data[3];
	
	if($search_by==1)
		$search_field='batch_no';
	else
		$search_field='job_no';
		
	$batch_cond="";
	if($batch_against_id!=2) $batch_cond=" and batch_against=$batch_against_id";
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	
	$po_name_arr=array();
	if($db_type==2) $group_concat="  listagg(cast(b.po_number AS VARCHAR2(4000)),',') within group (order by b.id) as order_no" ;
	else if($db_type==0) $group_concat=" group_concat(b.po_number) as order_no" ;
	
    $sql_po=sql_select("select a.mst_id,$group_concat from pro_batch_create_dtls a, wo_po_break_down b where a.po_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.mst_id");
	$po_name_arr=array();
	foreach($sql_po as $p_name)
	{
		$po_name_arr[$p_name[csf('mst_id')]]=implode(",",array_unique(explode(",",$p_name[csf('order_no')])));	
	}
	$color_arr=return_library_array( "select id, color_name from lib_color where  status_active=1 and  is_deleted=0 ",'id','color_name');
	
	$arr=array(5=>$batch_against,6=>$batch_for,7=>$color_arr);
	
	$sql = "select id, batch_no, extention_no, batch_weight, total_trims_weight, batch_date, batch_against, batch_for, job_no, color_id from pro_batch_create_mst where company_id=$company_id and $search_field like '$search_string' and page_without_roll=0  and status_active=1 and entry_form=136 and is_deleted=0 $batch_cond"; 
	//echo $sql;// and batch_against<>0	 
	echo  create_list_view("tbl_list_search", "Batch No,Ext. No,Job No,Batch Weight,Batch Date,Batch Against,Batch For, Color", "120,70,150,80,80,80,85,80","910","250",0, $sql, "js_set_value", "id", "", 1, "0,0,0,0,0,batch_against,batch_for,color_id", $arr, "batch_no,extention_no,job_no,batch_weight,batch_date,batch_against,batch_for,color_id", "",'','0,0,0,2,3,0,0,0');
	
exit();	
}

if ($action=="populate_data_from_search_popup")
{
	$data=explode("**",$data);
	$batch_id=$data[2];
	$batch_against=$data[0];
	$batch_for=$data[1];
	
	if($db_type==0) $year_field="DATE_FORMAT(insert_date,'%y')"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YY')";
	else $year_cond="";//defined Later
	
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and  is_deleted=0 ",'id','color_name');
	$data_array=sql_select("select id, company_id,working_company_id,floor_id,batch_no,job_no, extention_no, batch_weight,job_no, total_trims_weight,save_string, batch_date, batch_against, batch_for, booking_no, booking_no_id,booking_without_order, color_id, re_dyeing_from, color_range_id, organic, process_id, dur_req_hr, dur_req_min, collar_qty, cuff_qty, dyeing_machine, remarks, $year_field as year from pro_batch_create_mst where id='$batch_id'");
	foreach ($data_array as $row)
	{
		if($row[csf("extention_no")]==0) $ext_no=''; else $ext_no=$row[csf("extention_no")];
		
		$serial_no=$row[csf("id")]."-".$row[csf("year")];
		
		$process_name='';
		$process_id_array=explode(",",$row[csf("process_id")]);
		foreach($process_id_array as $val)
		{
			if($process_name=="") $process_name=$conversion_cost_head_array[$val]; else $process_name.=",".$conversion_cost_head_array[$val];
		}
		
		echo "document.getElementById('txt_batch_sl_no').value = '".$serial_no."';\n"; 
		echo "document.getElementById('cbo_batch_against').value = '".$row[csf("batch_against")]."';\n";  
		echo "document.getElementById('cbo_batch_for').value = '".$row[csf("batch_for")]."';\n";  
		echo "active_inactive();\n";
		echo "document.getElementById('txt_batch_date').value = '".change_date_format($row[csf("batch_date")])."';\n";  
		echo "document.getElementById('txt_batch_weight').value = '".$row[csf("batch_weight")]."';\n";  
		echo "document.getElementById('cbo_company_id').value = '".$row[csf("company_id")]."';\n"; 
		echo "document.getElementById('cbo_working_company_id').value = '" . $row[csf("working_company_id")] . "';\n"; 
		
		echo "document.getElementById('txt_batch_number').value = '".$row[csf("batch_no")]."';\n";  
		echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n";  
		echo "load_drop_down('requires/trims_batch_creation_controller','".$row[csf("working_company_id")]."', 'load_drop_down_floor', 'td_floor' );\n";
		echo "document.getElementById('cbo_floor').value = '" . $row[csf("floor_id")] . "';\n";
		//echo "document.getElementById('txt_booking_no_id').value = '".$row[csf("booking_no_id")]."';\n";  
		echo "document.getElementById('hidden_booking_without_order').value = '".$row[csf("booking_without_order")]."';\n";
		echo "document.getElementById('txt_ext_no').value = '".$ext_no."';\n";  
		echo "document.getElementById('txt_batch_color').value = '".$color_arr[$row[csf("color_id")]]."';\n";  
		echo "document.getElementById('cbo_color_range').value = '".$row[csf("color_range_id")]."';\n";
		echo "document.getElementById('txt_organic').value = '".$row[csf("organic")]."';\n";
		echo "document.getElementById('txt_process_id').value = '".$row[csf("process_id")]."';\n";
		echo "document.getElementById('txt_process_name').value = '".$process_name."';\n";
		echo "document.getElementById('txt_du_req_hr').value = '".$row[csf("dur_req_hr")]."';\n";
		echo "document.getElementById('txt_du_req_min').value = '".$row[csf("dur_req_min")]."';\n";
		echo "document.getElementById('txt_du_req_min').value = '".$row[csf("dur_req_min")]."';\n";
		echo "document.getElementById('txt_color_qty').value = '".$row[csf("collar_qty")]."';\n";
		echo "document.getElementById('txt_cuff_qty').value = '".$row[csf("cuff_qty")]."';\n";
		echo "document.getElementById('txt_remarks').value = '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('update_id').value = '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_machine_name').value = ".$row[csf("dyeing_machine")].";\n";
		
		if($row[csf("job_no")]!="")
		{
			echo "show_list_view('".$row[csf("job_no")]."'+'**'+'".$row[csf("job_no")]."','show_color_listview','list_color','requires/trims_batch_creation_controller','');\n";
		}
		
		if($batch_against==2)
		{
			echo "document.getElementById('cbo_batch_against').value = '".$batch_against."';\n";
			echo "$('#txt_ext_no').removeAttr('disabled','disabled');\n";
			echo "$('#txt_job_no').attr('disabled','disabled');\n";
			echo "$('#txt_batch_color').attr('disabled','disabled');\n";
			echo "$('#txt_batch_number').attr('readOnly','readOnly');\n";
			echo "$('#cbo_color_range').attr('disabled','disabled');\n";
			echo "$('#txt_process_name').attr('disabled','disabled');\n";
		}
		
		if($row[csf("batch_against")]==2)
		{
			$prv_batch_against=return_field_value("batch_against","pro_batch_create_mst","id='".$row[csf("re_dyeing_from")]."'");
			echo "document.getElementById('hide_batch_against').value = '".$prv_batch_against."';\n"; 
			echo "document.getElementById('hide_update_id').value = '".$row[csf("id")]."';\n";
		}
		else
		{
			echo "document.getElementById('hide_batch_against').value = '".$row[csf("batch_against")]."';\n"; 
			echo "document.getElementById('hide_update_id').value = '';\n";
		}
		
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_batch_creation',1);\n";	 
	}
	
	exit();
}

if( $action =='batch_details' ) 
{
	$data=explode('**',$data);
	$batch_against=$data[0];
	$batch_for=$data[1];
	$batch_id=$data[2];
	$roll_maintained=$data[3];
	$batch_maintained=$data[4];
	$txt_job_no=$data[5];
	$tblRow=0;
	
	if($batch_against==2)
	{
		$disbled="";
		$disbled_drop_down=1; 
	}
	else 
	{
		$disbled="";
		$disbled_drop_down=0; 
	}
	//$item_description_array = return_library_array("select item_description as id, item_description as item_desc from pro_batch_trims_dtls  where mst_id=$batch_id  and status_active=1 and is_deleted=0", 'id', 'item_desc');
	if($db_type==2)
	{
		$item_con="a.item_name || '-' || b.description as id, a.item_name || '-' || b.description as item_desc";	
	}
	else
	{
		$item_con="concat(a.item_name,'-',b.description) as id , concat(a.item_name,'-',b.description) as item_desc";	
	}
	$item_description_array = return_library_array("select $item_con from   lib_item_group a,wo_pre_cost_trim_cost_dtls b where b.trim_group=a.id and b.status_active=1 and b.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and b.job_no='$txt_job_no' order by a.id", 'id', 'item_desc');
	
	
	
	$po_array=array(); $program_no_array=array(); $body_part_ids_array=array();
	$data_array=sql_select("select  b.id, b.item_description as item_desc, b.trims_wgt_qnty, b.remarks from pro_batch_create_mst a, pro_batch_trims_dtls b where a.id=b.mst_id and b.mst_id=$batch_id and b.status_active=1 and b.is_deleted=0 order by b.id");
	
	//echo "select  b.id, b.item_description as item_desc, b.trims_wgt_qnty, b.remarks from pro_batch_create_mst a, pro_batch_trims_dtls b where a.id=b.mst_id and b.mst_id=$batch_id and b.status_active=1 and b.is_deleted=0"; 
	//if($data_array[0][csf('batch_against')]==2)
	//{
		foreach($data_array as $row)
		{
			$tblRow++;
			//$batch_array=sql_select("select batch_against, batch_for, booking_no from pro_batch_create_mst where id=".$row[csf("re_dyeing_from")]);
		//echo $row[csf('item_desc')];
		/*if(in_array($item_description_array,$row[csf('item_desc')]))
		{
			$item_description_array=$item_description_array;	
		}*/
			?>
			
                  <tr class="general" id="tr_<? echo $tblRow; ?>">
                    <td id="slTd_<? echo $tblRow; ?>"><? echo $tblRow; ?></td>
                        <td id="desc_td_id">
                         <?
                                echo create_drop_down( "txtitemDesc_".$tblRow, 200, $item_description_array,"",1, "-- Select --",$row[csf('item_desc')], "", "", "", "", "", "", "", "", "txtitemDesc[]");
                            ?>
                     
                        <input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('id')]; ?>" />
                        </td>
                        <td>
                        <input type="text" name="trimsWeight[]" id="trimsWeight_<? echo $tblRow; ?>" class="text_boxes_numeric" style="width:80px;" onKeyUp="calculate_trims_qnty();" value="<? echo $row[csf('trims_wgt_qnty')];?>"/>
                        </td>
                        <td>
                        <input type="text" name="remarks[]" id="remarks_<? echo $tblRow; ?>" class="text_boxes" style="width:150px;" value="<? echo $row[csf('remarks')];?>"/>
                        </td>
                        
                        <td>
                        <input type="button" id="increase_<? echo $tblRow; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fn_addRow_trims(<? echo $tblRow; ?>)" />
                        <input type="button" id="decrease_<? echo $tblRow; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $tblRow; ?>);"/>
                        </td>
        			</tr>
                   
                <?
		//}
	}
	
	
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
		
		var selected_id = new Array(); var selected_name = new Array();
		
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
			/*var currentRowColor=document.getElementById( 'search' + str ).style.backgroundColor;
			if(currentRowColor=='yellow')
			{
				var mandatory=$('#txt_mandatory' + str).val();
				var process_name=$('#txt_individual' + str).val();
				if(mandatory==1)
				{
					alert(process_name+" Subprocess is Mandatory. So You can't De-select");
					return;
				}
			}*/
			
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
                    $i=1; $process_row_id=''; $not_process_id_print_array=array(1,2,3,4,101,120,121,122,123,124); //$mandatory_subprocess_array=array(33,63,65,94);
					$hidden_process_id=explode(",",$txt_process_id);
                    foreach($conversion_cost_head_array as $id=>$name)
                    {
						if(!in_array($id,$not_process_id_print_array))
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							 
							if(in_array($id,$hidden_process_id)) 
							{ 
								if($process_row_id=="") $process_row_id=$i; else $process_row_id.=",".$i;
							}
							/*$mandatory=0;
							if(in_array($id,$mandatory_subprocess_array)) 
							{ 
								$mandatory=1;
							}*/
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)"> 
								<td width="50" align="center"><?php echo "$i"; ?>
									<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $id; ?>"/>	
									<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $name; ?>"/>
                                    <input type="hidden" name="txt_mandatory" id="txt_mandatory<?php echo $i ?>" value="<? echo $mandatory; ?>"/>
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

if($action=="batch_no_creation")
{
	//$batch_no_creation=return_field_value("batch_no_creation","variable_settings_production","company_name ='$data' and variable_list=24 and is_deleted=0 and status_active=1");
	$batch_no_creation=''; $batch_maintained='';
	$sql = sql_select("select variable_list, batch_no_creation, batch_maintained from variable_settings_production where company_name=$data and variable_list in (24,13) and status_active=1 and is_deleted=0"); 
	foreach($sql as $row)
	{
		if($row[csf('variable_list')]==13)
		{
			$batch_maintained=$row[csf('batch_maintained')];
		}
		else
		{
			$batch_no_creation=$row[csf('batch_no_creation')];
		}
	}
	
	if($batch_no_creation!=1) $batch_no_creation=0;
	if($batch_maintained!=1) $batch_maintained=0;
	
	echo "document.getElementById('batch_no_creation').value 				= '".$batch_no_creation."';\n";
	echo "document.getElementById('batch_maintained').value 				= '".$batch_maintained."';\n";
	echo "$('#txt_batch_number').val('');\n";
	echo "$('#update_id').val('');\n";
	if($batch_no_creation==1)
	{
		echo "$('#txt_batch_number').attr('readonly','readonly');\n";
	}
	else
	{
		echo "$('#txt_batch_number').removeAttr('readonly','readonly');\n";
	}
	
	exit();	
}

/*if($action=="roll_maintained")
{
	//Add New category id 50, old=category id was 3
	
	$roll_maintained=return_field_value("fabric_roll_level","variable_settings_production","company_name ='$data' and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");
	if($roll_maintained=="" || $roll_maintained==2) $roll_maintained=0; else $roll_maintained=$roll_maintained;
	
	echo "document.getElementById('roll_maintained').value 				= '".$roll_maintained."';\n";
	exit();	
}*/

if($action=="show_color_listview")
{
	$data=explode("**",$data);
	$job_no = $data[0];
	$po_id = $data[2];
	/*$batch_qnty_array = array();
	$batch_data_array = sql_select("SELECT a.color_id, a.booking_no, sum(b.batch_qnty) as qnty FROM pro_batch_create_mst a, pro_batch_create_dtls b WHERE a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=0 group by a.color_id, a.booking_no");
	foreach($batch_data_array as $row)
	{
		$batch_qnty_array[$row[csf('color_id')]][$row[csf('booking_no')]] = $row[csf('qnty')];
	}*/
?>	
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="200" class="rpt_table">
        <thead>
            <th width="20">SL</th>
            <th width="150">Color</th>
                           
        </thead>
		<?  
	/*$wo_pre_cost_fab_co_color_sql=sql_select("select b.gmts_color_id,b.contrast_color_id,c.id as fab_dtls_id from wo_pre_cos_fab_co_color_dtls b,wo_pre_cost_fab_conv_cost_dtls c  where  c.job_no=b.job_no and c.job_no='$job_no' and b.pre_cost_fabric_cost_dtls_id=c.fabric_description");
	foreach( $wo_pre_cost_fab_co_color_sql as $row)
	{
		$contrast_color_arr[$row[csf('gmts_color_id')]]['contrast_color']=$row[csf('contrast_color_id')];
	}*/
	
		$i=1;
		$sql=sql_select("select b.id, b.color_name from  wo_po_color_size_breakdown a, lib_color b where a.color_number_id=b.id and a.job_no_mst='$job_no' and  a.status_active=1 and  b.status_active=1 and a.is_deleted=0 group by b.id, b.color_name");
		foreach($sql as $row)
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";			
			$batch_qnty = $batch_qnty_array[$row[csf('id')]][$booking_no];
			$balance = $row[csf('qnty')] - $batch_qnty;
		?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="put_country_data(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('color_name')]; ?>')"> 
				<td width="20"><? echo $i; ?></td>
				<td width="150"><p><? echo $row[csf('color_name')]; ?></p></td>
				
			</tr>
		<?	
			$i++;
		}
		?>
	</table>
<?
	exit();
}

if($action=="batch_card_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$company=$data[0];
	$batch_update_id=$data[1];
	$batch_mst_update_id=str_pad($batch_update_id,10,'0',STR_PAD_LEFT);
	//echo $batch_mst_update_id;die;
	$batch_sl_no=$data[2];
	//echo $data[3].$data[4];die;
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and  is_deleted=0",'id','color_name');
	$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$machine_no_arr=return_library_array( "select id,machine_no from lib_machine_name",'id','machine_no');
	
	$job_array=array();
	$job_sql="select distinct(a.buyer_name) as buyer_name,a.style_ref_no, a.job_no_prefix_num, a.job_no, b.pub_shipment_date, b.id, b.po_number, b.grouping, b.file_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$job_sql_result=sql_select($job_sql);
	foreach($job_sql_result as $row)
	{
		$job_array[$row[csf('id')]]['job']=$row[csf('job_no')];
		$job_array[$row[csf('id')]]['po']=$row[csf('po_number')];
		$job_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
		$job_array[$row[csf('id')]]['ship_date']=$row[csf('pub_shipment_date')];
		$job_array[$row[csf('id')]]['ref']=$row[csf('grouping')];
		$job_array[$row[csf('id')]]['file_no']=$row[csf('file_no')];
		$job_array[$row[csf('id')]]['style_ref']=$row[csf('style_ref_no')];
	}
	
	if($db_type==0)
	{
		$sql="select a.id, a.batch_no, a.working_company_id,a.booking_no_id, a.booking_no,a.booking_without_order,a.color_id, a.batch_against, a.color_range_id, a.organic,a.dyeing_machine, a.extention_no, a.total_trims_weight, a.process_id as process_id, a.batch_for, a.batch_weight, a.remarks, a.collar_qty, a.cuff_qty, group_concat(b.po_id) as po_id, group_concat(b.prod_id) as prod_id from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.id=$batch_update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id,a.working_company_id, a.batch_no, a.booking_no_id, a.booking_no,a.booking_without_order,a.color_id, a.batch_against, a.color_range_id, a.organic,a.dyeing_machine, a.extention_no, a.total_trims_weight, a.process_id, a.batch_for, a.batch_weight,a.remarks,a.collar_qty, a.cuff_qty";
	}
	else
	{
		$sql="select a.id, a.batch_no, a.working_company_id,a.booking_no_id,a.booking_no,a.booking_without_order, a.color_id, a.batch_against, a.color_range_id, a.organic,a.dyeing_machine, a.extention_no, a.total_trims_weight, a.process_id as process_id, a.batch_for, a.batch_weight, a.remarks, a.collar_qty, a.cuff_qty, LISTAGG(b.po_id, ',') WITHIN GROUP (ORDER BY b.po_id) AS po_id , LISTAGG(CAST(b.prod_id AS VARCHAR2(4000)),',') WITHIN GROUP (ORDER BY b.prod_id) AS prod_id from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.id=$batch_update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id,a.working_company_id, a.batch_no, a.color_id, a.batch_against, a.color_range_id, a.organic ,a.dyeing_machine,a.extention_no, a.booking_no_id, a.booking_no,a.booking_without_order,a.total_trims_weight, a.process_id, a.batch_for, a.batch_weight,a.remarks,a.collar_qty,a.cuff_qty";	
	}
	//echo $sql;
	$dataArray=sql_select($sql);
	
	$po_number=""; $job_number="";$job_style=""; $buyer_id=""; $ship_date=""; $internal_ref=""; $file_nos="";
	$po_id=array_unique(explode(",",$dataArray[0][csf('po_id')]));
	$booking_no=$dataArray[0][csf('booking_no')];
	$batch_against_id=$dataArray[0][csf('batch_against')];
	$batch_booking_id=$dataArray[0][csf('booking_no_id')];
	$batch_product_id=$dataArray[0][csf('prod_id')];
	$batch_booking_without=$dataArray[0][csf('booking_without_order')];
	foreach($po_id as $val) //$job_array[$row[csf('id')]]['style_ref']
	{
		if($po_number=="") $po_number=$job_array[$val]['po']; else $po_number.=','.$job_array[$val]['po'];
		if($job_number=="") $job_number=$job_array[$val]['job']; else $job_number.=','.$job_array[$val]['job'];
		if($job_style=="") $job_style=$job_array[$val]['style_ref']; else $job_style.=','.$job_array[$val]['style_ref'];
		if($buyer_id=="") $buyer_id=$job_array[$val]['buyer']; else $buyer_id.=','.$job_array[$val]['buyer'];
		if($ship_date=="") $ship_date=change_date_format($job_array[$val]['ship_date']); else $ship_date.=','.change_date_format($job_array[$val]['ship_date']);
		
		if($internal_ref=="") $internal_ref=$job_array[$val]['ref']; else $internal_ref.=','.$job_array[$val]['ref'];
		if($job_array[$val]['file_no']>0)
		{
			if($file_nos=="") $file_nos=$job_array[$val]['file_no']; else $file_nos.=','.$job_array[$val]['file_no'];
		}
	}
	
	$job_no=implode(",",array_unique(explode(",",$job_number)));
	$jobstyle=implode(",",array_unique(explode(",",$job_style)));
	$buyer=implode(",",array_unique(explode(",",$buyer_id)));
	$internal_ref=implode(",",array_unique(array_filter(explode(",",$internal_ref))));
	$file_nos=implode(",",array_unique(explode(",",$file_nos)));
	//$booking_without_order=return_field_value( "booking_no as booking_no", "wo_non_ord_samp_booking_mst","booking_no=$booking_no","booking_no");
	if($dataArray[0][csf('booking_without_order')]==1)
	{
		$booking_without_order=sql_select("select booking_no_prefix_num, buyer_id from wo_non_ord_samp_booking_mst where company_id=$company and booking_no='$booking_no' and booking_type=4");
		//echo "select booking_no_prefix_num, buyer_id from wo_non_ord_samp_booking_mst where company_id=$company and booking_no='$booking_no' and booking_type=4";
		$booking_id=$booking_without_order[0][csf('booking_no_prefix_num')];
		$buyer_id_booking=$booking_without_order[0][csf('buyer_id')];
	}
	else
	{
		 $booking_with_order=sql_select("select booking_no_prefix_num, buyer_id from wo_booking_mst where company_id=$company and booking_no='$booking_no' and booking_type=4");
		 $booking_id=$booking_with_order[0][csf('booking_no_prefix_num')];
		 $buyer_id_booking=$booking_with_order[0][csf('buyer_id')];
	}
	$working_company_id=$dataArray[0][csf('working_company_id')];

?>
    <div style="width:980px;">
     <table width="980" cellspacing="0" align="center" border="0">
        <tr>
            <td colspan="6" align="center" style="font-size:22px"><strong>Working Company:<? echo $company_library[$working_company_id].'<br>'.'LC Company:'.$company_library[$company]; ?></strong></td>
            <td colspan="2" align="left">Print Time:<? echo $date=date("F j, Y, g:i a"); ?></td>
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:18px"><strong><u>Batch Card</u></strong></td>
            <td colspan="2" id="barcode_img_id" align="right" style="font-size:24px"></td>
        </tr>
         <tr>
           <td colspan="8">
           <? if($dataArray[0][csf('dyeing_machine')]!=0) { ?>
           <strong>M/C No:</strong>&nbsp;  <? echo $machine_no_arr[$dataArray[0][csf('dyeing_machine')]];} else echo '&nbsp; ';?></td> <td>&nbsp; </td>
        </tr>
        <tr>
           <td colspan="6" align="left" style="font-size:18px"><strong><u>Reference Details</u></strong></td>
           <td style="font-size:24px; border: solid 2px;" align="center" colspan="2">&nbsp;<? echo $dataArray[0][csf('organic')];?></td>
        </tr>
        <tr>
            <td width="110"><strong>Batch No</strong></td> <td width="135px">:&nbsp;<? echo $dataArray[0][csf('batch_no')]; ?></td>
            <td width="110"><strong>Batch SL</strong></td><td width="135px">:&nbsp;<? echo $batch_sl_no; ?></td>
            <td width="110"><strong>B. Color</strong></td><td width="135px">:&nbsp;<? echo $color_arr[$dataArray[0][csf('color_id')]]; ?></td>
            <td width="110"><strong>Color Ran.</strong></td><td width="135px">:&nbsp;<? echo $color_range[$dataArray[0][csf('color_range_id')]];?></td>
        </tr>
        <tr>
            <td><strong>Batch Against</strong></td><td>:&nbsp;<? echo $batch_against[$dataArray[0][csf('batch_against')]]; ?></td>
            <td><strong>Batch Ext.</strong></td><td>:&nbsp;<? echo $dataArray[0][csf('extention_no')];?></td>
            <td><strong>Batch For</strong></td><td>:&nbsp;<? echo $batch_for[$dataArray[0][csf('batch_for')]] ; ?></td>
            <td><strong>B. Weight</strong></td><td>:&nbsp;<? echo $dataArray[0][csf('batch_weight')]; ?></td>
        </tr>
        <tr>
            <td><strong>Buyer</strong></td><td>:&nbsp;<? if($dataArray[0][csf('batch_against')]==3) echo $buyer_arr[$buyer_id_booking]; else echo $buyer_arr[$buyer]; ?></td>
            <?
            if($dataArray[0][csf('batch_against')]==3)
			{?>
			 <td><strong>Booking no</strong></td><td>:&nbsp;<? echo $booking_id; ?></td>	
			
            <? }
			else
			{ ?>
				  <td><strong>Job</strong></td><td>:&nbsp;<? echo $job_no; ?></td>
		<? }
			?>
          
            <td><strong>Order No</strong></td><td>:&nbsp;<? echo $po_number; ?></td>
            <td><strong>Ship Date</strong></td><td>:&nbsp;<? if(trim($ship_date)!="0000-00-00" && trim($ship_date)!="") echo $ship_date; else echo "&nbsp;"; ?></td>
        </tr>
        <tr>
        	<td><strong>Collar Qty (Pcs)</strong></td><td>:&nbsp;<? echo $dataArray[0][csf('collar_qty')]; ?></td>
            <td><strong>Cuff Qty (Pcs)</strong></td><td>:&nbsp;<? echo $dataArray[0][csf('cuff_qty')]; ?></td>
            <td><strong>Int. Ref.</strong></td><td>:&nbsp;<? echo $internal_ref; ?></td>
            <td><strong>File No</strong></td><td>:&nbsp;<? echo $file_nos; ?></td>
        </tr>
        <tr>
        	<td><strong>Remarks</strong></td><td colspan="4">:&nbsp;<? echo $dataArray[0][csf('remarks')]; ?></td>
            <td><strong>Style</strong></td><td>:&nbsp;<? echo $jobstyle; ?></td>
        </tr>
    </table>
    <div style="float:left; font-size:17px;"><strong><u>Fabrication Details</u></strong> </div>
    <table align="center" cellspacing="0" width="980"  border="1" rules="all" class="rpt_table" style="border-top:none" >
        <thead bgcolor="#dddddd" align="center">
            <tr>
                <th width="30">SL</th>
                <th width="60">Prog. No</th>
                <th width="80">Body part</th>
                <th width="150">Const. & Comp.</th>
                <th width="50">Fin. GSM</th>
                <th width="50">Fin. Dia</th> 
                <th width="70">M/Dia X Gauge</th>
                <th width="70">D/W Type</th>
                <th width="60">S. Length </th>
                <th width="70">Grey Qty.</th>
                <th width="50">Roll No.</th>
                <th width="80">Yarn Lot</th>
                <th width="80"><strong>Yarn Suplier</strong></th>
                <th width="80">Yarn Count</th>
                <th>ID Code</th>
            </tr>
        </thead>
		<?
			$i=1;
			$yarncount=return_library_array( "select id, yarn_count from  lib_yarn_count",'id','yarn_count');
			$supplier_array_lib=return_library_array( "select id,short_name from  lib_supplier", "id", "short_name"  );
			$machine_array_lib_dia=return_library_array( "select id,dia_width from  lib_machine_name", "id", "dia_width"  );
			$machine_array_lib_gauge=return_library_array( "select id,gauge from  lib_machine_name", "id", "gauge"  );
			//$supplier_from_prod=return_library_array("select lot,supplier_id from  product_details_master where item_category_id=1", "lot","supplier_id");
			$supplier_brand=return_library_array("select id,brand_name from lib_brand", "id","brand_name");
			
			$machine_lib_sql=sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
			foreach($machine_lib_sql as $row)
			{
				$dya_gauge_arr[$row[csf("id")]]["machine_no"]=$row[csf("machine_no")];
				$dya_gauge_arr[$row[csf("id")]]["dia_width"]=$row[csf("dia_width")];
				$dya_gauge_arr[$row[csf("id")]]["gauge"]=$row[csf("gauge")];
			}
			
			$yarn_lot_arr=array();
			$sample_arr=array();
			$yarn_count=array();
			$s_length=array();
			if($batch_against_id==3 && $batch_booking_without==1)
			{
				$yarn_lot_data=sql_select("select  p.booking_id, a.prod_id, a.yarn_lot, a.yarn_count, a.stitch_length,a.machine_no_id from inv_receive_master p, pro_grey_prod_entry_dtls a where  p.id=a.mst_id and p.booking_id='$batch_booking_id' and p.booking_without_order=1 and a.prod_id in($batch_product_id) and p.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and p.status_active=1 and p.is_deleted=0");
				foreach($yarn_lot_data as $rows)
				{
					$sample_arr[$rows[csf('booking_id')]][$rows[csf('prod_id')]]['yarncount']=$rows[csf('yarn_count')];
					$sample_arr[$rows[csf('booking_id')]][$rows[csf('prod_id')]]['stitch_length']=$rows[csf('stitch_length')];
					$sample_arr[$rows[csf('booking_id')]][$rows[csf('prod_id')]]['samplelot']=$rows[csf('yarn_lot')];
					$sample_arr[$rows[csf('booking_id')]][$rows[csf('prod_id')]]['machine_no_id']=$rows[csf('machine_no_id')];
				}
			}
			else
			{
				/*if($db_type==0)
				{
					$yarn_lot_data=sql_select("select  a.brand_id, b.po_breakdown_id, a.prod_id, group_concat(distinct(a.yarn_lot)) as yarn_lot, a.yarn_count, group_concat(distinct(a.stitch_length)) as stitch_length, group_concat(distinct(a.machine_no_id)) as machine_no_id from pro_grey_prod_entry_dtls a, order_wise_pro_details b where  a.id=b.dtls_id and b.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.prod_id, b.po_breakdown_id");
				}
				else if($db_type==2)
				{
					$yarn_lot_data=sql_select("select  a.brand_id, b.po_breakdown_id, a.prod_id, LISTAGG(CAST(a.yarn_lot AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.yarn_lot) as yarn_lot, a.yarn_count, LISTAGG(CAST(a.stitch_length AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.stitch_length) as stitch_length, LISTAGG(CAST(a.machine_no_id AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.machine_no_id) as machine_no_id from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.entry_form in(2,22)   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.prod_id, b.po_breakdown_id,a.yarn_count,a.brand_id");
				}
				
				foreach($yarn_lot_data as $rows)
				{
					$yarn_lot=explode(",",$rows[csf('yarn_lot')]);
					$stitch_length_arr=explode(",",$rows[csf('stitch_length')]);
					$yarn_lot_arr[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['lot']=implode(", ",array_unique($yarn_lot));
					$yarn_count[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['yarn_count']=$rows[csf('yarn_count')];
					$yarn_lot_arr[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['stitch_length']=$rows[csf('stitch_length')];
					$yarn_count[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['brand_id']=$rows[csf('brand_id')];
					$machine_no_id[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['machine_no_id']=$rows[csf('machine_no_id')];
					
					/*$sample_arr[$rows[csf('prod_id')]]['yarncount']=$rows[csf('yarn_count')];
					$sample_arr[$rows[csf('prod_id')]]['stitch_length']=$rows[csf('stitch_length')];
					$sample_arr[$rows[csf('prod_id')]]['brand_id']=$rows[csf('brand_id')];
					$sample_arr[$rows[csf('prod_id')]]['samplelot']=implode(", ",array_unique($yarn_lot));*/
				//}*/
				
				$yarn_lot_data=sql_select("select  a.brand_id, b.po_breakdown_id, a.prod_id, a.yarn_lot as yarn_lot, a.yarn_count, a.stitch_length as stitch_length,a.machine_no_id as machine_no_id 
				from pro_grey_prod_entry_dtls a, order_wise_pro_details b 
				where a.id=b.dtls_id and b.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ");
				
				foreach($yarn_lot_data as $rows)
				{
					$yarn_lot_arr[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['lot'].=$rows[csf('yarn_lot')].",";
					$yarn_count[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['yarn_count'].=$rows[csf('yarn_count')].",";
					$yarn_lot_arr[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['stitch_length'].=$rows[csf('stitch_length')].",";
					$yarn_count[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['brand_id'].=$rows[csf('brand_id')].",";
					$machine_no_id[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['machine_no_id'].=$rows[csf('machine_no_id')].",";
				}
			}
			
			//var_dump($sample_arr);
			//echo $yarn_lot_arr[1939][3833]['lot'];
			/*$fin_feb_data=sql_select("select a.id,a.program_no,c.machine_gg,c.machine_dia,b.color_type_id,c.fabric_dia from pro_batch_create_dtls a, ppl_planning_info_entry_mst b,ppl_planning_info_entry_dtls c where a.program_no=c.id and b.id=c.mst_id");
			$fin_dia=array();
			$dia_type=array();
			$machine_dia=array();
			$color_type=array();
			foreach($fin_feb_data as $d_rows)
			{
				$fin_dia[$d_rows[csf('program_no')]]['f_dia']=$d_rows[csf('fabric_dia')];
				$machine_gg[$d_rows[csf('program_no')]]['m_gauge']=$d_rows[csf('machine_gg')];
				$machine_dia[$d_rows[csf('program_no')]]['m_dia']=$d_rows[csf('machine_dia')];
				$color_type[$d_rows[csf('program_no')]]['color_type']=$d_rows[csf('color_type_id')];
			}*/
			//var_dump($yarn_count);
			
			$sql_dtls_knit="select a.id as batch_id,a.booking_no_id,e.receive_basis,e.booking_id,a.booking_without_order,d.prod_id,d.machine_no_id,d.machine_dia,d.machine_gg,e.knitting_source, e.knitting_company
		from pro_batch_create_mst a,pro_batch_create_dtls b, pro_grey_prod_entry_dtls d,  inv_receive_master e 
		where a.id=b.mst_id and d.mst_id=e.id and a.company_id=$data[0] and a.id=$batch_update_id  and e.booking_id=$batch_booking_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22)";
		$result=sql_select($sql_dtls_knit);$machine_dia_guage_arr=array();
		foreach($result as $row)
		{
			$machine_dia_guage_arr[$row[csf('booking_no_id')]][$row[csf('prod_id')]]['basis']=$row[csf('receive_basis')];
			$machine_dia_guage_arr[$row[csf('booking_no_id')]][$row[csf('prod_id')]]['dia']=$row[csf('machine_dia')];
			$machine_dia_guage_arr[$row[csf('booking_no_id')]][$row[csf('prod_id')]]['gg']=$row[csf('machine_gg')];
		}
 

		
			
	$sql_dtls="select b.id, a.batch_no, a.total_trims_weight, a.batch_date, a.batch_weight, a.color_id, a.booking_no_id, a.booking_without_order, a.process_id, a.extention_no, b.batch_qnty AS batch_qnty, b.roll_no, b.item_description, b.program_no, b.po_id, b.prod_id, b.body_part_id, b.width_dia_type from pro_batch_create_mst a,pro_batch_create_dtls b where a.company_id=$data[0] and a.id=b.mst_id and a.id=$batch_update_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0";
	//echo $sql_dtls;
	$sql_result=sql_select($sql_dtls);
	foreach($sql_result as $row)
	{
		if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			//echo $row[csf('prod_id')].'='.$row[csf('po_id')];
		$desc=explode(",",$row[csf('item_description')]);
		if($row[csf('booking_without_order')]==0)
		{
		$recv_basis=$machine_dia_guage_arr[$row[csf('booking_no_id')]][$row[csf('prod_id')]]['basis'];
		}
		
		if($batch_against_id==3 && $row[csf('booking_without_order')]==1)
		{
			$yarn_lot=$sample_arr[$row[csf('booking_no_id')]][$row[csf('prod_id')]]['samplelot'];
			$y_count=$sample_arr[$row[csf('booking_no_id')]][$row[csf('prod_id')]]['yarncount'];
			$stitch=$sample_arr[$row[csf('booking_no_id')]][$row[csf('prod_id')]]['stitch_length'];	
			$yarn_count_value=$yarncount[$y_count];
			//$dya_gage=$dya_gauge_arr[$sample_arr[$row[csf('booking_no_id')]][$row[csf('prod_id')]]['machine_no_id']]["dia_width"]."<br>".$dya_gauge_arr[$sample_arr[$row[csf('booking_no_id')]][$row[csf('prod_id')]]['machine_no_id']]["gauge"];
			
		}
		else
		{
			$y_count=chop($yarn_count[$row[csf('prod_id')]][$row[csf('po_id')]]['yarn_count'],",");
			$y_count_id=array_unique(explode(',',$y_count));
			$yarn_count_value='';
			$machine_no_id_arr=array_unique(explode(',',chop($machine_no_id[$row[csf('prod_id')]][$row[csf('po_id')]]['machine_no_id'],",")));
			//$dya_gage="";
			foreach($machine_no_id_arr as $machine_id)
			{
				//$dya_gage=$dya_gauge_arr[$machine_id]["dia_width"]."<br>".$dya_gauge_arr[$machine_id]["gauge"];
			}
			
			//$dya_gage=$dya_gauge_arr[$machine_no_id[$row[csf('prod_id')]][$row[csf('po_id')]]['machine_no_id']]["dia_width"]."<br>".$dya_gauge_arr[$machine_no_id[$row[csf('prod_id')]][$row[csf('po_id')]]['machine_no_id']]["gauge"];
			
			foreach($y_count_id as $val)
			{
				if($val>0)
				{
					if($yarn_count_value=='') $yarn_count_value=$yarncount[$val]; else $yarn_count_value.=", ".$yarncount[$val];
				}
			}
		
			$stitch=implode(", ", array_unique(explode(",",chop($yarn_lot_arr[$row[csf('prod_id')]][$row[csf('po_id')]]['stitch_length'],","))));	
			$yarn_lot=implode(", ", array_unique(explode(",",chop($yarn_lot_arr[$row[csf('prod_id')]][$row[csf('po_id')]]['lot'],","))));
			$yarn_brand=array_unique(explode(",",chop($yarn_lot_arr[$row[csf('prod_id')]][$row[csf('po_id')]]['brand_id'],",")));
		}
		//$yarn_lot_arr[$rows[csf('prod_id')]][$rows['po_breakdown_id']]['stitch_length']
		//$st_len=implode(", ", array_unique(explode(",",$stitch)));
		//$machine_dia_up=$machine_array_lib_dia[$machine_dia[$row[csf('program_no')]]['m_dia']];
		//$machine_gauge_up=$machine_array_lib_gauge[$machine_dia[$row[csf('program_no')]]['m_gauge']];
		/*$lot_data=explode(",",$yarn_lot);
		$lot_supplier="";
		foreach($lot_data as $row_lot)
		{
			if($lot_supplier=="") $lot_supplier=$supplier_array_lib[$supplier_from_prod[$row_lot]];else $lot_supplier.=",".$supplier_array_lib[$supplier_from_prod[$row_lot]];	
		}*/
		
		$brand_suplier="";
		foreach($yarn_brand as $brand_id)
		{
			if($brand_suplier=="") $brand_suplier=$supplier_brand[$brand_id];else $brand_suplier.=",".$supplier_brand[$brand_id];	
		}
		
		
		if($recv_basis==0 || $recv_basis==1 || $recv_basis==4) //from Entry page
		{
			$machine_dia_width=$machine_dia_guage_arr[$row[csf('booking_no_id')]][$row[csf('prod_id')]]['dia'];
			$machine_gauge=$machine_dia_guage_arr[$row[csf('booking_no_id')]][$row[csf('prod_id')]]['gg'];
			$dya_gage=$machine_dia_width.'<br>'.$machine_gauge;
		}
		else if($recv_basis==2) //Knitting Plan
		{
			 $program_data=sql_select("select width_dia_type, machine_dia, machine_gg, machine_id from ppl_planning_info_entry_dtls where id='".$row[csf('booking_no_id')]."'");
			
			$machine_dia_width=$program_data[0][csf('machine_dia')];
			$machine_gauge=$program_data[0][csf('machine_gg')];
			$dya_gage=$machine_dia_width.'<br>'.$machine_gauge;
		}
		?>
            <tr bgcolor="<? echo $bgcolor; ?>" >
                <td  width="30"><? echo $i; ?></td>
                <td width="60" align="center"><p><? echo $row[csf('program_no')]; ?></p></td>
                <td width="80"><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
                <td width="150"><p><? echo $desc[0].",".$desc[1]; ?></p></td>
                <td width="50" align="center"><p><? echo $desc[2]; ?></p></td>
                <td width="50" align="center"><p><? echo $desc[3]; ?></p></td>
                <td width="70" align="center"><p><? echo $dya_gage; ?></p></td>
                <td width="70"><p><? echo $fabric_typee[$row[csf('width_dia_type')]];  ?></p></td>
                <td width="60" align="center"><p><? echo $stitch; ?></p></td>
                <td width="70" align="right"><p><? echo number_format($row[csf('batch_qnty')],2); ?></p></td>
                <td align="center" width="50"><p><? echo $row[csf('roll_no')]; ?></p></td>
                <td width="80"><p><? echo $yarn_lot; //$yarn_lot_arr[$row[csf('prod_id')]][$row[csf('po_id')]]['lot'];  ?></p></td>
                <td width="80"><p><? echo rtrim($brand_suplier,",");//$supplier_array_lib[$supplier_from_prod[$yarn_lot]]; ?></p></td>
                <td width="80"><p><? echo $yarn_count_value;?></p></td>
              	<td>&nbsp;</td>
            </tr>
		<?php
        //$total_roll_number+= $row[csf('roll_no')];
        $total_batch_qty+= $row[csf('batch_qnty')];
        $i++;
    }
	?>
            <tr>
                <td style="border:none;" colspan="9" align="right"><b>Sum:</b> <? //echo $b_qty; ?> </td>
                <td align="right"><b><? echo number_format($total_batch_qty,2); ?> </b></td>
                <td align="center"><b><? echo $total_roll_number; ?> </b></td>
                <td colspan="4" style="border:none;">&nbsp;</td>
            </tr>
            <tr>
                <td style="border:none;" colspan="9" align="right"><b>Trims Weight:</b> <? //echo $b_qty; ?> </td>
                <td align="right"><b><? echo number_format($dataArray[0][csf('total_trims_weight')],2); ?> </b></td>
                <td colspan="5" style="border:none;">&nbsp;</td>
            </tr>
             <tr>
                <td style="border:none;" colspan="9" align="right"><b>Total:</b>  </td>
                <td align="right"><b><? echo number_format($total_batch_qty+$dataArray[0][csf('total_trims_weight')],2);  ?> </b></td>
                <td colspan="5" style="border:none;">&nbsp;</td>
            </tr>
             <tr>
                <td colspan="15"  align="right">&nbsp; </td>
            </tr>
         <tr>
            <td colspan="15"  align="right">
			<? 
            $process=$dataArray[0][csf('process_id')];
            $process_id=explode(',',$process);
            //print_r($process_id);
			$process_value='';
			$i=1;
			foreach ($process_id as $val)
			{
				if($process_value=='') $process_value=$i.'. '. $conversion_cost_head_array[$val]; else $process_value.=", ".$i.'. '.$conversion_cost_head_array[$val];
			$i++;
			}
             ?>
           <table align="left" rules="all" class="rpt_table" width="980">
             <tr>
                 <th  align="left"  style="font-size:20px;"><strong>Process Required</strong></th>
            </tr>
             <tr>
                   <td  style="font-size:20px;" title="<? echo $process_value; ?>"> 
                <p><? echo $process_value; ?></p>
                  </td>
            </tr>
            <tr>
             <td align="left" style="font-size:19px;"> 
           Heat Setting:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;   Loading Date & Time: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;  UnLoading Date & Time:&nbsp;
             </td>
            </tr>
          </table>
             </td>
    	</tr>
    </table>
     <div style="float:left; margin-left:10px;"><strong> Quality Instruction(Hand Written)</strong> </div>
    <table width="980" cellspacing="0" align="center" >
        <tr>
            <td valign="top" align="left" width="480">
                <table cellspacing="0" width="475"  align="left" border="1" rules="all" class="rpt_table">
                    <tr>
                        <th>SL</th><th>Roll No</th><th>Roll Mark</th><th>Actual Dia</th><th>Roll Wgt.</th><th>Remarks</th>
                    </tr>
                    <tr height="30">
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                    <tr height="30">
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                    <tr height="30">
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                    <tr height="30">
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                    <tr height="30">
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                    <tr height="30">
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                </table>
            </td>
            <td width="10" align="justify" valign="top"></td>
            <td width="480" valign="top" align="right">
                <table width="475"  cellspacing="0"  border="1" rules="all" class="rpt_table">
                    <tr>
                        <th>SL</th><th>Roll No</th><th>Actual Dia</th><th>Roll Wgt.</th><th>Remarks</th>
                    </tr>
                    <tr height="30">
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                    <tr height="30">
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                    <tr height="30">
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                    <tr height="30">
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td width="480" valign="top">
                <table width="475" cellspacing="0" border="1" rules="all" class="rpt_table">
                    <tr>
                        <th align="left"><strong>Shade Result(<i>Hand Written</i>)</strong></th>
                    </tr>
                    <tr>
                        <td colspan="1" style="width:475px; height:80px" >&nbsp;</td>
                    </tr>
                </table>
        	</td>
            <td width="10" align="justify" valign="top">&nbsp;</td>
            <td width="480" valign="top" align="right">
                <table cellspacing="0" border="1" rules="all" class="rpt_table" width="475" >
                    <tr>
                        <th align="left" colspan="3"><strong>Shrinkage(<i>Hand Written</i>)</strong></th>
                    </tr>
                    <tr>
                        <th><b>Length % </b></th><th><b>Width % </b></th><th><b> Twist % </b></th>
                    </tr>
                    <tr height="30">
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                    <tr height="30">
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                    <tr height="30">
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                    <tr height="30">
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td width="980" colspan="3">
                <table cellspacing="0" border="1" rules="all" class="rpt_table" width="980" >
                    <tr>
                        <th align="center"><strong>Dyeing Information(<i>Hand Written</i>)</strong></th>
                    </tr>
                    <tr>
                        <td style="width:980px; height:120px" >&nbsp;</td>
                    </tr>
                </table> 
            </td>
        </tr>
    </table>
     <br>
		 <?
            echo signature_table(52, $company, "980px");
         ?>
    </div>
   <script type="text/javascript" src="../../js/jquery.js"></script>
     <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
     <script>
		function generateBarcode( valuess )
		{
			var value = valuess;//$("#barcodeValue").val();
		  //alert(value)
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
			//$("#barcode_img_id").html('11');
			 value = {code:value, rect: false};
			
			$("#barcode_img_id").show().barcode(value, btype, settings);
		} 
		generateBarcode('<? echo $batch_mst_update_id; ?>');
	</script>
<?
exit();
}

?>