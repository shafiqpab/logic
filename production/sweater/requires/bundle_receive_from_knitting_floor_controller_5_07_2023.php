<?
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="challan_duplicate_check")
{

    // $result=sql_select("select a.id, a.issue_challan_id , a.sys_number from pro_gmts_delivery_mst a, pro_gmts_delivery_mst b where b.id=a.issue_challan_id and a.production_type=51 and b.production_type=50 and b.sys_number='".$data."' and a.status_active=1 and a.is_deleted=0");
    $result=sql_select("select a.id, a.issue_challan_id , a.sys_number from pro_gmts_delivery_mst a where a.production_type=50 and b.sys_number='".$data."' and a.status_active=1 and a.is_deleted=0");
    $msg=1;
    $datastr="";
    if(count($result)>0)
    {
        foreach ($result as $row)
        { 
            $msg=2;
            $datastr=$row[csf('sys_number')];
        }
    }
    else
    {
    	$result=sql_select("select b.id from pro_gmts_delivery_mst b where b.sys_number='".$data."' and b.status_active=1 and b.is_deleted=0");	
    	foreach ($result as $row)
        { 
            $datastr=$row[csf('id')];
        }
	}
    echo rtrim($msg)."_".rtrim($datastr);
    exit();
}

if($action=="show_dtls_yarn_listview")
{

	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$data=explode("_",$data);
	//echo $data[1];die;
	$sql_cut=sql_select("SELECT a.job_no, b.color_id, b.roll_data, a.id, b.id as dtls_id from ppl_cut_lay_mst a, ppl_cut_lay_dtls b where a.cutting_no='".$data[0]."' and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 ");
	$job_no				=$sql_cut[0][csf("job_no")];
	$color_id			=$sql_cut[0][csf("color_id")];
	$mst_id				=$sql_cut[0][csf("id")];
	$dtls_id			=$sql_cut[0][csf("dtls_id")];

	// ==================== getting issued bundle ===========
	$issued_bundle=return_library_array( "SELECT bundle_no,bundle_no from pro_garments_production_dtls where status_active=1 and is_deleted=0 and delivery_mst_id=$data[2] and production_type=50", "bundle_no", "bundle_no"  ); 
	$bundle_cond = where_con_using_array($issued_bundle,1,"bundle_no");
	// ==================== getting delv id ===========
	$delivery_ids=return_library_array( "SELECT delivery_mst_id,delivery_mst_id from pro_garments_production_dtls where status_active=1 and is_deleted=0 $bundle_cond and production_type=51", "delivery_mst_id", "delivery_mst_id"  ); 
	$delivery_cond = where_con_using_array($delivery_ids,0,"delivery_mst_id");

	$sql="SELECT gmts_color , yarn_color, sample_color , sample_color_ids, receive_qty from pro_gmts_knitting_issue_dtls where production_type=51 and status_active=1 and is_deleted=0 $delivery_cond";
	// echo $sql;
	$res = sql_select($sql);
	$prev_rcv_qty_arr = array();
	foreach ($res as $val) 
	{
		$prev_rcv_qty_arr[$val['GMTS_COLOR']][$val['YARN_COLOR']][$val['SAMPLE_COLOR']][$val['SAMPLE_COLOR_IDS']] += $val['RECEIVE_QTY'];
	}
	// echo "<pre>";print_r($prev_rcv_qty_arr);echo "</pre>";


	$data_array_strip=sql_select("SELECT id , gmts_color , yarn_color, sample_color , sample_color_ids, required_qty, returanable_qty , issue_qty , short_excess_qty , issue_balance_qty from pro_gmts_knitting_issue_dtls where delivery_mst_id=$data[2] and production_type=50 and status_active=1 and is_deleted=0 order by id ");
	
	$sample_reference	=$data_array_strip[0][csf("sample_ref")];
	$colspan			=count($data_array_strip);

	// ========================= size set consumption ====================
	$sql="SELECT c.color_id,c.sample_color_id, c.yarn_color_id,sum(c.actual_consumption) as actual_consumption from ppl_size_set_mst a, ppl_size_set_consumption c where a.id=c.mst_id and a.job_no='$job_no' and c.color_id=$color_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.color_id,c.sample_color_id, c.yarn_color_id";
	// echo $sql;
	$res = sql_select($sql);
	$total_cons = 0;
	foreach ($res as $val) 
	{
		$total_cons += $val['ACTUAL_CONSUMPTION']*2.2046;
	}

	$size_set_avg_cons_arr = array();
	foreach ($res as $val) 
	{
		$size_set_avg_cons_arr[$val['SAMPLE_COLOR_ID']][$val['YARN_COLOR_ID']] += ($total_cons) ? ($val['ACTUAL_CONSUMPTION']*2.2046)/$total_cons : 0;
	}
	// echo "<pre>"; print_r($size_set_avg_cons_arr); echo "</pre>";
	
	?>	
    <table 
        cellpadding="0" 
        width="930" 
        cellspacing="0" 
        border="1" 
        class="rpt_table" 
        rules="all">
        
        <thead>
            <th width="30">SL</th>
            <th width="100">Sample Color</th>
            <th width="200">Yarn Color</th>
            <th width="100">Required Qty(Lbs)</th>
            <th width="100">Issue Qty. (Lbs)</th>           
            <th width="100">Cuml. Rcv Qty (Lbs)</th>  
            <th width="100">Receive Qty (Lbs)</th>           
            <th width="100">Returnable Qty (Lbs)</th>
            <th width="">Wastage (Lbs)</th>
           
        </thead>
    </table>	
		
	<div 
        style="width:950px;max-height:250px;overflow-y:scroll" 
        align="left"> 
           
        <table 
            cellpadding="0" 
            width="930" 
            cellspacing="0" 
            border="1" 
            class="rpt_table" 
            rules="all" 
            id="tbl_yarn_details">      
            <tbody>
            
		<?php  
			$i=1;	
			foreach($data_array_strip as $row)
			{
				if ($i%2==0)  $bgcolor="#E9F3FF";
                else $bgcolor="#FFFFFF";
                $prev_rcv_qty = $prev_rcv_qty_arr[$row['GMTS_COLOR']][$row['YARN_COLOR']][$row['SAMPLE_COLOR']][$row['SAMPLE_COLOR_IDS']];
 			?>
                <tr 
                	bgcolor="<? echo $bgcolor; ?>" 
                	style="text-decoration:none; cursor:pointer" 
                	id="tr_<? echo $i; ?>" > 

                    <td width="30" align="center"><? echo $i; ?></td>
                    <td width="100" align="center"><p><?
                    	foreach (explode(",",$row[csf('sample_color_ids')]) as  $sin_smaple_color) {
                           echo $color_library[$sin_smaple_color]." ";
                        }
                    	// echo $color_library[$row[csf('sample_color')]];
                     ?></p></td>
                    <td width="200" align="center" style="word-break:break-all"><p><?php  echo $color_library[$row[csf('yarn_color')]]; ?></p></td>
                     <td width="100" align="right"><p><?php echo number_format($row[csf('required_qty')],4,".",""); ?></p></td>
                    		
                    <td width="100" align="right"><p><?php echo number_format($row[csf('returanable_qty')]+$row[csf('issue_qty')],4,".",""); ?></p></td>
                    <td width="100" align="right" id="cuml_rcv_qty_<? echo $i; ?>"><p><?php echo number_format($prev_rcv_qty,4); ?></p>
                    <td width="100" align="right" id="returanable_qty_<? echo $i; ?>"><p></p>

                    </td>
             
                    <td width="100" align="center">
                    	<input type="hidden" id="prev_rcv_qty_<? echo $i; ?>" value="<?php echo number_format($prev_rcv_qty,4); ?>">
                    	<input 
                    		onkeyup="fnc_total_issue_balance() "
                        	type="text" 
                            id="txt_returnable_qty_<? echo $i; ?>" 
                            name="txt_returnable_qty[]"
                            style="width:80px;"
                            class="text_boxes_numeric">
                            
                        <input
                        	type="hidden" 
                            id="hidden_yarn_color_<? echo $i; ?>" 
                            name="hidden_yarn_color[]"
                            value="<?php echo $row[csf('yarn_color')]; ?>">
                        <input
                        	type="hidden" 
                            id="hidden_sample_color_<? echo $i; ?>" 
                            name="hidden_sample_color[]"
                            value="<?php echo $row[csf('sample_color_ids')]; ?>">

                        <input
                        	type="hidden" 
                            id="hidden_yarn_dtls_id_<? echo $i; ?>" 
                            name="hidden_yarn_dtls_id[]"
                            value="">

                        <input
                        	type="hidden" 
                            id="hidden_size_set_cons_<? echo $i; ?>" 
                            name="hidden_size_set_cons[]"
                            value="<?=$size_set_avg_cons_arr[$row[csf('sample_color')]][$row[csf('yarn_color')]];?>">    

                    </td>
                   
                    <td width="" align="right"><?php //echo number_format(($total_consumption*$row[csf('size_qty')]*2.2046226)/12,4,".","");?></td>
                </tr>
            	<?php
                $i++;
                $total_required_qty    	+=$row[csf('required_qty')];
                $total_issue_qty		+=$row[csf('returanable_qty')]+$row[csf('issue_qty')];
                $total_prev_rcv_qty		+=$prev_rcv_qty;
			}
			?>
            </tbody>
            <tfoot>
            	<tr>
                    <th   colspan="3" > Total</th>
                    <th width="100"  id="total_required_qty"><?php echo number_format($total_required_qty,4,".",""); ?></th>       
                    <th width="100"  id="total_issue_qty"><?php echo number_format($total_issue_qty,4,".",""); ?></th>
                    <th width="100"  id="total_cuml_rcv_qty"><?php echo number_format($total_prev_rcv_qty,4,".",""); ?></th>
                    <th width="100"  id="total_receive_qty"><?php //echo number_format($total_prev_rcv_qty,4,".",""); ?></th>
                    <th width="100"  id="total_returnable_qty"></th> 
                    <th width=""  	 id="total_wastage_qty"></th>
                </tr>
            </tfoot>
		</table>
	</div>
	<?
	exit();
}

if($action=="issue_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	list($shortName,$ryear,$ratio_prifix)=explode('-',$lot_ratio);
	if($ryear=="") 	$ryear=date("Y",time());
	else 			$ryear=("20$ryear")*1;
	//echo $ryear;die;
	?>
	<script>
	
		function check_all_data() {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length; 
			for( var i = 1; i <= tbl_row_count; i++ ) {
				
				if($("#search"+i).css("display") !='none'){
				 js_set_value( i );
				}
			}
		}
		
		
		var selected_id = new Array();
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value(id) 
		{			
			$('#hidden_system_id').val( id );
			parent.emailwindow.hide();
		}
		
    </script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
		<form name="searchwofrm"  id="searchwofrm">
			<fieldset style="width:810px;">
			<legend></legend>           
	            <table cellpadding="0" cellspacing="0" width="550" border="1" rules="all" class="rpt_table">
	                <thead>
	                	<th>Company</th>
	                    <th>Lot Ratio Year</th>
	                    <th>Job No</th>
	                    <th class="must_entry_caption">Ratio No</th>
	                    <th>QR Code</th>
                       <th>Enter Challan No</th>
	                    <th>
	                    	<input 
	                        	type="reset" 
	                            name="reset" 
	                            id="reset" 
	                            value="Reset" 
	                            style="width:100px" 
	                            class="formbutton" />
	                        
	                         <input 
	                            type="hidden" 
	                            name="hidden_system_id" 
	                            id="hidden_system_id" 
	                            value="" />
	                    </th>
	                </thead>
	                <tr class="general">
	                	<td>
						<? 
	                        $sql_com="select id, company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name";
	                                    
	                        echo create_drop_down( "cbo_company_name", 140, $sql_com, "id,company_name", 1, "-- Select --", $company_id, "",0 );
	                    ?>
	                    </td>
	                    <td align="center">				
	                    <?
							echo create_drop_down( "cbo_lot_year", 60, $year, '', "", '-- Select --', $ryear, "" );
						?>
	                    </td> 				
	                    <td align="center">				
	                        <input 
	                        	type="text" 
	                            style="width:130px" 
	                            class="text_boxes" 
	                            name="txt_job_no" 
	                            id="txt_job_no" />	
	                    </td> 				 				
	                    <td>
	                        <input 
	                            type="text" 
	                            name="txt_lot_no" 
	                            id="txt_lot_no" 
	                            style="width:120px" 
	                            class="text_boxes"
	                            value="<?php if($ratio_prifix) echo $ratio_prifix*1; ?>" />
	                    </td>  		

						<td>
	                        <input 
	                            type="text" 
	                            name="txt_barcode_no" 
	                            id="txt_barcode_no" 
	                            style="width:120px" 
	                            class="text_boxes"
	                             />
	                    </td>  		
	                    <td>
	                    	<input 
	                        	type="text" 
	                            name="txt_challan_no" 
	                            id="txt_challan_no" 
	                            style="width:120px" 
	                            class="text_boxes" />
	                    </td>  		
	            		<td align="center">
	                     	<input type="button"name="button2"class="formbutton"value="Show"onClick="show_list_view 
							(document.getElementById('cbo_company_name').value+'_'+
							 document.getElementById('txt_challan_no').value+'_'+
							 document.getElementById('txt_job_no').value+'_'+
							 document.getElementById('txt_lot_no').value+'_'+ 
							 document.getElementById('cbo_lot_year').value+'_'+
							 document.getElementById('txt_barcode_no').value,

							 'create_issue_callan_search_list_view', 'search_div', 'bundle_receive_from_knitting_floor_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"style="width:100px;" />
	                     </td>
	                </tr>
	           </table>
	           <div 
	                style="width:100%; 
	                        margin-top:5px; 
	                        margin-left:10px" 
	                id="search_div" 
	                align="left">
	            </div>
			</fieldset>
		</form>
	</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	
	</html>
	<?
}


$new_conn=integration_params(2);
if($action=="create_issue_callan_search_list_view")
{
 	$ex_data 				= explode("_",$data);
	$company 				= $ex_data[0];
	$job_no					=$ex_data[2];
	$challan_no				=$ex_data[1];
	$lot_no					=$ex_data[3];
	
	$syear 					= substr($ex_data[4],2);
	$barcode_no				=$ex_data[5];

	if(trim($ex_data[2]))	$challan_no = "".trim($ex_data[2])."";
	
	if( trim($ex_data[0])==0)
	{
		echo "<h2 style='color:#D00; text-align:center;'>Please Select Company First. </h2>";
		exit();
	}


	if( trim($ex_data[3])=='' && trim($ex_data[2])=='' && trim($ex_data[1])=='' && trim($ex_data[5])=='')
	{
		echo "<h2 style='color:#D00; text-align:center;'>Please Select Lot No or Job No or Challan No </h2>";
		exit();
	}

	

	$employee_arr=return_library_array( "select id_card_no, (first_name||' '||middle_name|| '  ' || last_name) as emp_name from hrm_employee where status_active=1 and is_deleted=0",'id_card_no','emp_name',$new_conn);
	//print_r($employee_arr);die;
	if ($lot_no != '') 
	{
		$cutCon = " and c.cut_num_prefix_no=".$lot_no."";
    }
		
	if($job_no!='') 
		$jobCon=" and c.job_no like '%$job_no%' ";
	else 
		$jobCon="";

	if($challan_no!='') 
		$challanCon=" and a.sys_number_prefix_num like '%$challan_no%' ";
	else 
		$challanCon="";

	if($barcode_no!='') 
	$barcode_no=" and b.barcode_no like '%$barcode_no%' ";
	else 
	$barcodeCon="";	
	
	?>
    <table 
    	cellspacing="0" 
        cellpadding="0" 
        border="1" 
        rules="all" 
        width="830" 
        class="rpt_table">
        
        <thead>
            <th width="40">SL</th>
            <th width="100">Job No</th>
            <th width="120">Issue No</th>
            <th width="110">Lot Ratio No</th>
            <th width="150">Body Part </th>
            <th width="100">Operator ID</th>
            <th width="120">Operator</th>                      
            <th>Issue Qty.</th>
        </thead>
	</table>
	<div 
    	style=" width:850px; 
        		max-height:210px; 
                overflow-y:scroll"
       	id="list_container_batch"
        align="left">
        	 
        <table 
        	cellspacing="0" 
            cellpadding="0" 
            border="1" 
            rules="all" 
            width="830" 
            class="rpt_table" 
            id="tbl_list_search">  
        	<?
			$i=1;
			$sql="SELECT a.sys_number, a.id, a.operator_id , a.body_part_ids, a.body_part_type , c.cutting_no, c.job_no, sum(b.production_qnty) as production_qty from pro_garments_production_dtls b, ppl_cut_lay_mst c, pro_gmts_delivery_mst a where a.id=b.delivery_mst_id $cutCon $jobCon $challanCon $barcodeCon and b.cut_no=c.cutting_no and a.production_type=50 and b.production_type=50 and a.status_active=1  and b.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.is_deleted=0 group by a.sys_number, a.id, a.operator_id , a.body_part_ids, a.body_part_type , c.cutting_no, c.job_no order by a.id DESC";
			 //echo $sql;
			$result = sql_select($sql);
			$challan_id_arr=array();
			foreach ($result as $val)
			{ 
				$challan_id_arr[$val[csf('id')]]=$val[csf('id')];
			}

			$receive_data_arr=return_library_array( "select id,issue_challan_id from pro_gmts_delivery_mst where issue_challan_id in (".implode(",", $challan_id_arr).") and status_active=1 and is_deleted=0 ", "issue_challan_id", "id"  );

			foreach ($result as $row)
			{  
				// if(!$receive_data_arr[$row[csf('id')]])	
				// {
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$bodypart_name_arr=array();	
					$bodypart_arr=explode(',',$row[csf('body_part_ids')]);
				
					foreach ($bodypart_arr as $value) 
					{
						$bodypart_name_arr[$value]=$time_weight_panel[$value];
					}
					?>
					<tr 
                    	bgcolor="<? echo $bgcolor; ?>" 
                        style="
                        	text-decoration:none; 
                            cursor:pointer"
                        id="search<? echo $i;?>" 
                        onClick="js_set_value(<? echo $row[csf('id')]; ?>)"> 
                        
						<td width="40"><? echo $i; ?></td>
						<td width="100" align="center"><p><? echo $row[csf('job_no')]; ?></p></td>
						<td width="120" align="center"><p><? echo $row[csf('sys_number')]; ?></p></td>
						<td width="110"><p><? echo $row[csf('cutting_no')]; ?></p></td>
						<td width="150"><p><? echo implode(",", $bodypart_name_arr); ?></p></td>
						<td width="100"><p><? echo $row[csf('operator_id')]; ?></p></td>
						<td width="120"><p><? echo $employee_arr[$row[csf('operator_id')]]; ?></p></td>
						<td align="right"><? echo $row[csf('production_qty')]; ?></td>
					</tr>
					<?
					$i++;
				// }
			}
        	?>
        </table>
    </div>
	<?	
	exit();	
}

if($action=='populate_data_from_yarn_issue')
{
	$data_array=sql_select("SELECT a.sys_number, a.id, a.floor_id, a.location_id, a.production_type, a.working_company_id, a.working_location_id , a.operator_id, a.supervisor_id, a.body_part_ids, a.body_part_type , a.company_id, c.cutting_no, c.job_no, c.size_set_no, a.production_source, sum(b.production_qnty) as production_qty from pro_gmts_delivery_mst a, pro_garments_production_dtls b, ppl_cut_lay_mst c where a.id=$data and a.id=b.delivery_mst_id and b.cut_no=c.cutting_no and a.production_type=50 and b.production_type=50 and a.status_active=1  and b.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.is_deleted=0 group by a.sys_number, a.id, a.floor_id, a.location_id, a.production_type, a.working_company_id, a.working_location_id , a.operator_id, a.supervisor_id, a.body_part_ids, a.company_id, c.cutting_no, c.job_no, c.size_set_no, a.production_source, a.body_part_type ");
	
	foreach ($data_array as $row)
	{ 
		$bodypart_name_arr=array();	
		$bodypart_arr=explode(',',$row[csf('body_part_ids')]);
	
		foreach ($bodypart_arr as $value) {
			$bodypart_name_arr[$value]=$time_weight_panel[$value];
		}
		echo "document.getElementById('txt_issue_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_bodyPart_id').value 				= '".$row[csf("body_part_ids")]."';\n";
		echo "document.getElementById('cbo_bodypart_type').value 			= '".$row[csf("body_part_type")]."';\n";
		echo "document.getElementById('txt_bodypart_name').value 			= '".implode(",", $bodypart_name_arr)."';\n";
		echo "document.getElementById('txt_operator_id').value 				= '".$row[csf("operator_id")]."';\n";
		echo "document.getElementById('hidden_sup_id').value 				= '".$row[csf("supervisor_id")]."';\n";
		echo "document.getElementById('txt_issue_qty').value 				= '".$row[csf("production_qty")]."';\n";
		echo "document.getElementById('txt_issue_no').value 				= '".$row[csf("sys_number")]."';\n";
		echo "document.getElementById('txt_lot_ratio').value 				= '".$row[csf("cutting_no")]."';\n";
		echo "document.getElementById('txt_job_no').value 					= '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('txt_size_set_no').value 				= '".$row[csf("size_set_no")]."';\n";
		echo "document.getElementById('cbo_company_name').value 			= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_source').value 					= '".$row[csf("production_source")]."';\n";
		echo "load_drop_down( 'requires/bundle_receive_from_knitting_floor_controller', ".($row[csf("production_source")])."+'_'+".($row[csf("company_id")]).", 'load_drop_down_knitting_com','knitting_com');";
		
		echo "document.getElementById('cbo_working_company').value 			= '".$row[csf("working_company_id")]."';\n";
		
		echo "load_location(".$row[csf("production_source")].");\n";
		echo "document.getElementById('cbo_working_location').value 		= '".$row[csf("working_location_id")]."';\n";
		echo "load_drop_down( 'requires/bundle_receive_from_knitting_floor_controller', ".$row[csf("working_location_id")].", 'load_drop_down_floor', 'floor_td' );";
		echo "document.getElementById('cbo_floor').value  					= '".($row[csf("floor_id")])."';\n";
		echo "load_drop_down( 'requires/bundle_receive_from_knitting_floor_controller', '".$row[csf('company_id')]."', 'load_drop_down_lc_location', 'location_td' );\n";
		echo "document.getElementById('cbo_location').value 					= '".$row[csf("location_id")]."';\n";
		$employee_arr=return_library_array( "select id_card_no, (first_name||' '||middle_name|| '  ' || last_name) as emp_name from hrm_employee where id_card_no='".$row[csf("operator_id")]."' and status_active=1 and is_deleted=0",'id_card_no','emp_name',$new_conn);
		echo "document.getElementById('txt_operation_name').value 			= '".$employee_arr[$row[csf("operator_id")]]."';\n";
		$employee_arr=return_library_array( "select id_card_no, (first_name||' '||middle_name|| '  ' || last_name) as emp_name from hrm_employee where id_card_no='".$row[csf("supervisor_id")]."' and status_active=1 and is_deleted=0",'id_card_no','emp_name',$new_conn);
		echo "document.getElementById('txt_sup_name').value 			= '".$employee_arr[$row[csf("supervisor_id")]]."';\n";
		
		exit();
	}
}


if($action=="show_dtls_listview_from_issue")
{
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
	$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name" );
	$machine_library=return_library_array( "select id,machine_no from lib_machine_name where category_id=1 and status_active=1 and is_deleted=0 and is_locked=0  order by seq_no", "id", "machine_no" );
	$data=explode("_",$data);
    $body_part_id=$data[2];
    $delivery_mst_id=$data[3];
    $bodypart_type_id=$data[4];
	//echo $data[1];die;
	$sql_cut=sql_select("SELECT a.job_no, a.size_set_no, b.color_id, b.roll_data, a.id, b.id as dtls_id from ppl_cut_lay_mst a, ppl_cut_lay_dtls b where a.cutting_no='".$data[0]."' and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 ");
	$job_no				=$sql_cut[0][csf("job_no")];
	$color_id			=$sql_cut[0][csf("color_id")];
	$consumption_string	=$sql_cut[0][csf("roll_data")];
	$mst_id				=$sql_cut[0][csf("id")];
	$dtls_id			=$sql_cut[0][csf("dtls_id")];
	$size_set_no		=$sql_cut[0][csf("size_set_no")];
	list($shortName,$year,$job_prifix)=explode('-',$job_no);
	//echo $consumption_string;die;
	$consumption_data_arr=explode("**",$consumption_string);
	$color_wise_cons_arr=array();
	foreach($consumption_data_arr as $single_color_consumption)
	{
		$single_color_cons_arr=explode("=",$single_color_consumption);
		$color_wise_cons_arr[$single_color_cons_arr[1]]=$single_color_cons_arr[3];
	}
	
	$job_sql=sql_select("SELECT c.short_name as BUYER_NAME, a.po_number as PO_NUMBER, a.id as ID from wo_po_break_down  a,wo_po_details_master b ,lib_buyer c where b.job_no='".$job_no."' and a.job_no_mst=b.job_no   and b.buyer_name=c.id ");
	$jbp_arr=array();
	foreach($job_sql as $jval)
	{
		$jbp_arr["buyer_name"]=$jval["BUYER_NAME"];
		$jbp_arr[$jval["ID"]]=$jval["PO_NUMBER"];
	}
	

	$data_array_strip=sql_select("SELECT a.sample_color_id as sample_color, a.sample_color_ids as sample_color_ids, a.yarn_color_id as stripe_color, a.sample_color_percentage, a.production_color_percentage, a.actual_consumption, a.consumption, b.sample_ref,b.id from ppl_size_set_consumption a, ppl_size_set_mst b where b.job_no='".$job_no."' and b.sizeset_no='".$size_set_no."' and b.id=a.mst_id and a.color_id=$color_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 order by a.id ");
	
	$sample_reference	=$data_array_strip[0][csf("sample_ref")];
	$size_set_mstid		=$data_array_strip[0][csf("id")];
	$colspan			=count($data_array_strip);
	$table_width		=1360;
	$div_width			=$table_width+20;
	
	$bodypart_cond="b.body_part_id in (".$body_part_id.")";
	
	
	$sql_wet_sheet="SELECT b.color_id, sum(b.bodycolor) as  bodycolor, sum(CASE WHEN $bodypart_cond THEN b.bodycolor ELSE 0 END) as  bodycolor_aspect,b.body_part_id from sample_development_mst a, sample_development_rf_color b where a.id=b.mst_id and a.requisition_number='".$sample_reference."' and b.bodycolor>0 group by b.color_id,b.body_part_id";
	// echo $sql_wet_sheet;
	$wet_sheet_result=sql_select($sql_wet_sheet);

	$bodypart_color_qty_arr=array();
	$knitting_gmm_total=0;
	foreach($wet_sheet_result as $wet_row)
	{
		$total_color_bodypart+=$wet_row[csf('bodycolor')];

		if($wet_row[csf('body_part_id')]<=5) $body_type="Main"; else $body_type="Accessories";
	   	$bodypart_color_qty_arr[$body_type][$wet_row[csf('body_part_id')]][$wet_row[csf('color_id')]]+=$wet_row[csf('bodycolor')];
	   	$knitting_gmm_total+=$wet_row[csf('bodycolor')];
	}

	$color_percentage_bodypart=array();
	foreach($wet_sheet_result as $wet_row)
	{
		$color_percentage_bodypart[$wet_row[csf('color_id')]]+=$wet_row[csf('bodycolor_aspect')]/$wet_row[csf('bodycolor')];	
		$color_qty_bodypart[$wet_row[csf('color_id')]]['body_color']+=$wet_row[csf('bodycolor_aspect')]; 
        $color_qty_bodypart[$wet_row[csf('color_id')]]['total_body_color']+=$wet_row[csf('bodycolor')]; 
	}


	foreach($data_array_strip as $scolor)
    {
        if($scolor[csf("sample_color_ids")])
        {
            if(count(explode(",", $scolor[csf("sample_color_ids")]))>1)
            {
                foreach (explode(",", $scolor[csf("sample_color_ids")]) as $sin_sample_color) {
                    $color_qty_bodypart[$scolor[csf("sample_color_ids")]]['body_color']+=$color_qty_bodypart[$sin_sample_color]['body_color']; 
                    $color_qty_bodypart[$scolor[csf("sample_color_ids")]]['total_body_color']+=$color_qty_bodypart[$sin_sample_color]['total_body_color'];
                }
                $color_percentage_bodypart[$scolor[csf("sample_color_ids")]]=($color_qty_bodypart[$scolor[csf("sample_color_ids")]]['body_color']/$color_qty_bodypart[$scolor[csf("sample_color_ids")]]['total_body_color']); 
            }
        }
    }
	//print_r($color_percentage_bodypart);die;
	//echo $sql;die;

	// new add==================================

	$color_size_result=sql_select("SELECT gmt_size_id, production_weight from ppl_size_set_dtls where mst_id='$size_set_mstid' and color_id=$color_id and status_active=1 and is_deleted=0 order by id");
	//echo "select gmt_size_id, production_weight from ppl_size_set_dtls where mst_id='$size_set_mstid' and color_id=$color_id and status_active=1 and is_deleted=0 order by id"; die;
	$sizeWiseProdQtyArr=array();
	foreach($color_size_result as $row)
	{
		if($row[csf('gmt_size_id')]!=0)	
		{
			$sizeWiseProdQtyArr[$row[csf('gmt_size_id')]]=$row[csf('production_weight')];
		}
	}
	unset($color_size_result);


	$sqlStripe=sql_select("SELECT id, sample_color_id, sample_color_ids, production_color_percentage, process_loss, consumption from ppl_size_set_consumption where mst_id='$size_set_mstid' and color_id=$color_id and status_active=1 and is_deleted=0 order by id"); 
	$yarnColorArr=array(); $consumtion_without_process_loss=0;
	 foreach ($sqlStripe as $row)
	 {
		 if($row[csf('sample_color_ids')]!="") $row[csf('sample_color_id')]=$row[csf('sample_color_ids')];
		 $yarnColorArr[$row[csf('sample_color_id')]]['prod_color_per']=$row[csf('production_color_percentage')];
		 $yarnColorArr[$row[csf('sample_color_id')]]['process_loss']=$row[csf('process_loss')];
		 $consumtion_without_process_loss+=$row[csf('consumption')];
	 }
	 unset($sqlStripe);
	 // print_r($consumtion_without_process_loss);
	 $sizeSummArr=array();
	 foreach($yarnColorArr as $ycolor=>$ycolorVal)
	 {
		foreach($sizeWiseProdQtyArr as $gmt_size_id=>$prodQty)
		{
			$colorSizeQty=0;
			$colorSizeQty=(($prodQty*0.00220462262)*12)*($ycolorVal['prod_color_per']/100)*(1+($ycolorVal['process_loss']/100));
			//echo $colorSizeQty.'='.$prodQty.'='.$ycolorVal['prod_color_per'].'='.$ycolorVal['process_loss'].'<br>';
			$sizeSummArr[$ycolor][$gmt_size_id]+=$colorSizeQty;
		}
	 }
	// print_r($sizeSummArr); die;

	// ============================================
	$bodypart_color_total_arr=array();
	$color_bodypartmain_total_arr=array();
	$consumtion_without_process_loss_lbs_per_pcs=($consumtion_without_process_loss*1000)/12;
	foreach($bodypart_color_qty_arr["Main"] as $body_part_id=>$body_part_row)
	{ 
		foreach ($data_array_strip as $sample_color)
		{
			// echo $sample_color[csf('sample_color')].'--'.$knitting_gmm_total.'<br>';
			if($sample_color[csf('sample_color_ids')])
			{
				foreach (explode(",",$sample_color[csf('sample_color_ids')]) as $sc_id) 
				{
					$body_part_row[$sample_color[csf('sample_color_ids')]]+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
					$bodypart_color_total_arr[$body_part_id]+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
					$color_bodypartmain_total_arr[$sample_color[csf('sample_color_ids')]]+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
				}
			}
			else
			{
				$bodypart_color_total_arr[$body_part_id]+=($body_part_row[$sample_color[csf('sample_color')]]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
				$color_bodypartmain_total_arr[$sample_color[csf('sample_color')]]+=($body_part_row[$sample_color[csf('sample_color')]]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
			}
		}
	}
	// echo "<pre>";print_r($color_bodypartmain_total_arr);echo "</pre>";
	// ============================================
	
	$bodypart_color_total_arr=array();
	$color_bodypartacc_total_arr=array();
	$bodypart_main_total=0;
	foreach($bodypart_color_qty_arr["Accessories"] as $body_part_id=>$body_part_row)
	{ 
		foreach ($data_array_strip as  $sample_color)
		{
			if($sample_color[csf('sample_color_ids')])
			{
				foreach (explode(",",$sample_color[csf('sample_color_ids')]) as $sc_id) {
					$body_part_row[$sample_color[csf('sample_color_ids')]]+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
					$bodypart_color_total_arr[$body_part_id]+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
					$color_bodypartacc_total_arr[$sample_color[csf('sample_color_ids')]]+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
					$bodypart_main_total+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
				}
			}
			else
			{
				$bodypart_color_total_arr[$body_part_id]+=($body_part_row[$sample_color[csf('sample_color')]]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
				//echo $body_part_row[$sample_color[csf('sample_color')]].'='.$consumtion_without_process_loss_lbs_per_pcs.'='.$knitting_gmm_total.'<br>';
				$color_bodypartacc_total_arr[$sample_color[csf('sample_color')]]+=($body_part_row[$sample_color[csf('sample_color')]]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
				$bodypart_main_total+=($body_part_row[$sample_color[csf('sample_color')]]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
			}
		}
	}

	// =============================================
	$colorWiseTotArr=array();
	foreach ($data_array_strip as  $sample_color)
	{
		if($sample_color[csf('sample_color_ids')])
		{
			$colorWiseTotArr[$sample_color[csf('sample_color_ids')]]+=$color_bodypartmain_total_arr[$sample_color[csf('sample_color_ids')]]+$color_bodypartacc_total_arr[$sample_color[csf('sample_color_ids')]];
		}
		else
		{
			$colorWiseTotArr[$sample_color[csf('sample_color')]]+=$color_bodypartmain_total_arr[$sample_color[csf('sample_color')]]+$color_bodypartacc_total_arr[$sample_color[csf('sample_color')]];
		}
	}
	//print_r($colorWiseTotArr); die;


	$colorWiseAvgArr=array();
	foreach ($data_array_strip as  $sample_color)
	{
		$avgQty=0;
		if($sample_color[csf('sample_color_ids')])
		{
			$avgQty=$color_bodypartmain_total_arr[$sample_color[csf('sample_color_ids')]]/$colorWiseTotArr[$sample_color[csf('sample_color_ids')]];
			$colorWiseAvgArr[$sample_color[csf('sample_color_ids')]]+=$avgQty;
		}
		else
		{
			$avgQty=$colorWiseTotArr[$sample_color[csf('sample_color')]]/$colorWiseTotArr[$sample_color[csf('sample_color')]];
			// $avgQty=$color_bodypartmain_total_arr[$sample_color[csf('sample_color')]]/$colorWiseTotArr[$sample_color[csf('sample_color')]];
			$colorWiseAvgArr[$sample_color[csf('sample_color')]]+=$avgQty;
		}
	}

	// echo "<pre>";print_r($colorWiseAvgArr);echo "</pre>";
	?>	
        <table cellpadding="0"width="<?php echo $div_width;?>"cellspacing="0"border="1"class="rpt_table"rules="all">
            <thead>
            	<tr>
                    <th width="30"  rowspan="3">SL</th>
                    <th width="90" rowspan="3">Bundle No</th>
                    <th width="90" rowspan="3">Barcode No</th>
                    <th width="70" rowspan="3">MC No</th>
                    <th width="120" rowspan="3">G. Color</th>
                    <th width="50"  rowspan="3">Size</th>
                    <th width="50"  rowspan="3">Bundle Qty. (Pcs)</th>
                    <th width="50"  rowspan="3">Knit Qty.(Pcs)</th>
                    <th width="70"  rowspan="3">Incl. Process Loss (Lbs)</th>
                    <th width="70"  rowspan="3" title="((Size wise Prod Qty*0.00220462262)*Size Qty)*(production color percentage/100)">Exc. Process Loss (Lbs)</th>
                    <th width="60"  rowspan="3">Bundle Weight Rec. (GM)</th>
                    <th width="70"  rowspan="3">Bundle Weight Rec. (Lbs)</th>
                    <th width="70"  rowspan="3">Wastage Qty (Lbs)</th>

                    <th width="40"  rowspan="3">Year</th>
                    <th width="60"  rowspan="3">Job No</th>
                    <th width="65"  rowspan="3">Buyer</th>
                    <th width="90"  rowspan="3">Order No</th>
                    <th width="100" rowspan="3">Gmts. Item</th>
                    <th width="" rowspan="3">Country</th>
                    <th rowspan="3">
                    	<input type="hidden"id="txt_total_color"name="txt_total_color"style="width:80px;"value="<?php echo $colspan; ?>">
                    </th>
                </tr>
            </thead>
        </table>
	<div style="width:<?php echo $div_width;?>px;max-height:250px;overflow-y:scroll" align="left"> 
           
        <table cellpadding="0"width="<?php echo $table_width;?>"cellspacing="0"border="1"class="rpt_table"rules="all"id="tbl_details"> <tbody> 
		<?php  
			$i=1; $total_production_qnty=0; $grand_color_cons_arr=array(); $issue_color_consumtion=array();

			// $rcv_barcode_array = return_library_array("SELECT barcode_no,barcode_no as bno from pro_garments_production_dtls where production_type=51 and status_active=1 and cut_no='$data[0]'", "barcode_no", "bno" );
			$sql = "SELECT barcode_no,bodypart_type_id from pro_garments_production_dtls where production_type=51 and status_active=1 and cut_no='$data[0]' and bodypart_type_id=$bodypart_type_id";
			// echo $sql;
			$res = sql_select($sql);
			$rcv_barcode_array = array();
			foreach ($res as $val) 
			{
				$rcv_barcode_array[$val['BODYPART_TYPE_ID']][$val['BARCODE_NO']] = $val['BARCODE_NO'];
			}
			// print_r($rcv_barcode_array);
			$sqlResult =sql_select("SELECT b.* , a.gmt_item_id, c.machine_id, c.bundle_qty, c.color_size_break_down_id,c.barcode_no,c.bodypart_type_id from ppl_cut_lay_dtls a, ppl_cut_lay_bundle b, pro_garments_production_dtls c where c.delivery_mst_id=$delivery_mst_id and b.mst_id=$mst_id and b.dtls_id=$dtls_id and c.barcode_no=b.barcode_no and a.id=b.dtls_id and a.color_id=$color_id and c.bodypart_type_id=$bodypart_type_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.production_type=50");
			// echo "SELECT b.* , a.gmt_item_id, c.machine_id, c.bundle_qty, c.color_size_break_down_id,c.barcode_no,c.bodypart_type_id from ppl_cut_lay_dtls a, ppl_cut_lay_bundle b, pro_garments_production_dtls c where c.delivery_mst_id=$delivery_mst_id and b.mst_id=$mst_id and b.dtls_id=$dtls_id and c.barcode_no=b.barcode_no and a.id=b.dtls_id and a.color_id=$color_id and c.bodypart_type_id=$bodypart_type_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.production_type=50";

			foreach($sqlResult as $selectResult)
			{
				// if(!in_array($selectResult[csf('barcode_no')], $rcv_barcode_array))
				if($rcv_barcode_array[$selectResult[csf('bodypart_type_id')]][$selectResult[csf('barcode_no')]]=="")
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	 				?>
	                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="" > 
	                    <td width="30" align="center"><? echo $i; ?></td>
	                    <td width="90" align="center"><p><? echo $selectResult[csf('bundle_no')]; ?></p></td>
	                    <td width="90" align="center"><p><? echo $selectResult[csf('barcode_no')]; ?></p></td>
	                    <td width="70" align="center"><p><? echo $machine_library[$selectResult[csf('machine_id')]]; ?></p></td>
	                    <td width="120" align="center" style="word-break:break-all"><?php echo $color_library[$color_id]; ?></td>
	                    <td width="50" align="center"><?php  echo $size_library[$selectResult[csf('size_id')]]; ?></td>
	                    <td width="50" align="center"><?php  echo $selectResult[csf('size_qty')]; ?></td>
	                    <td width="50" align="center"> 
	                    	<input type="text"id="txt_knit_qty_<? echo $i; ?>"onkeyup="fnc_check_knit_qty() "name="txt_knit_qty[]"style="width:37px;"class="text_boxes_numeric">
	                    </td>
	             		<td width="70" align="right">
	                    <?php
							$total_consumption=0; $total_consumption_wpl=0;$bundle_weight_rec=0;						
							/*foreach($data_array_strip as $scolor)
							{
								if($scolor[csf("sample_color_ids")])
	                    		{
	                    			if($i==1)
									{
										$issue_color_consumtion[$scolor[csf("sample_color_ids")]]=$scolor[csf("consumption")]*$color_percentage_bodypart[$scolor[csf("sample_color_ids")]];
									}
									$total_consumption+=$scolor[csf("actual_consumption")]*$color_percentage_bodypart[$scolor[csf("sample_color_ids")]];
									$total_consumption_wpl+=$scolor[csf("consumption")]*$color_percentage_bodypart[$scolor[csf("sample_color_ids")]];
									$grand_color_cons_arr[$scolor[csf("sample_color_ids")]]+=($scolor[csf("actual_consumption")]*$selectResult[csf('size_qty')]*$color_percentage_bodypart[$scolor[csf("sample_color_ids")]]*2.2046226)/12;
	                    		}
	                    		else
	                    		{
	                    			if($i==1)
									{
										$issue_color_consumtion[$scolor[csf("sample_color")]]=$scolor[csf("consumption")]*$color_percentage_bodypart[$scolor[csf("sample_color")]];
									}
									$total_consumption+=$scolor[csf("actual_consumption")]*$color_percentage_bodypart[$scolor[csf("sample_color")]];
									$total_consumption_wpl+=$scolor[csf("consumption")]*$color_percentage_bodypart[$scolor[csf("sample_color")]];
									$grand_color_cons_arr[$scolor[csf("sample_color")]]+=($scolor[csf("actual_consumption")]*$selectResult[csf('size_qty')]*$color_percentage_bodypart[$scolor[csf("sample_color")]]*2.2046226)/12;
	                    		}
							}
							//print_r($grand_color_cons_arr);die;
							$grand_total_consumption+=($total_consumption*$selectResult[csf('size_qty')]*2.2046226)/12;
							$total_size_qty+=$selectResult[csf('size_qty')];

							$grand_total_consumption_wpl+=($total_consumption_wpl*$selectResult[csf('size_qty')]*2.2046226)/12;
							
						 	echo number_format(($total_consumption*$selectResult[csf('size_qty')]*2.2046226)/12,4,".","");*/
						 	foreach($data_array_strip as $scolor)
							{
							 	if($scolor[csf("sample_color_ids")])
	                            {                                
									$yarnColorWiseLbsQty=(($colorWiseAvgArr[$scolor[csf("sample_color_ids")]]*$sizeSummArr[$scolor[csf("sample_color_ids")]][$selectResult[csf('size_id')]])/12)*$selectResult[csf('size_qty')];
									// echo $colorWiseAvgArr[$scolor[csf("sample_color_ids")]]."*".$sizeSummArr[$scolor[csf("sample_color_ids")]][$selectResult[csf('size_id')]]."/12)*".$selectResult[csf('size_qty')];
									// echo number_format($yarnColorWiseLbsQty,4,".","");
									$total_consumption+=$yarnColorWiseLbsQty;
									$grand_total_consumption+=$yarnColorWiseLbsQty;
									$grand_color_cons_arr[$scolor[csf("sample_color_ids")]]+=$yarnColorWiseLbsQty;
									$bundle_weight_rec += (($sizeWiseProdQtyArr[$selectResult[csf('size_id')]]*0.00220462262)*$selectResult[csf('size_qty')])*($yarnColorArr[$scolor[csf("sample_color_ids")]]['prod_color_per']/100);
	                            }
	                            else 
	                            {
									$yarnColorWiseLbsQty=(($colorWiseAvgArr[$scolor[csf("sample_color")]]*$sizeSummArr[$scolor[csf("sample_color")]][$selectResult[csf('size_id')]])/12)*$selectResult[csf('size_qty')];
									// echo number_format($yarnColorWiseLbsQty,4,".","");echo "<br>";

									// echo $colorWiseAvgArr[$scolor[csf("sample_color")]]."*".$sizeSummArr[$scolor[csf("sample_color")]][$selectResult[csf('size_id')]]."/12)*".$selectResult[csf('size_qty')]."<br>";

									$total_consumption+=$yarnColorWiseLbsQty;
									$grand_total_consumption+=$yarnColorWiseLbsQty;
									$grand_color_cons_arr[$scolor[csf("sample_color")]]+=$yarnColorWiseLbsQty;
									$bundle_weight_rec += (($sizeWiseProdQtyArr[$selectResult[csf('size_id')]]*0.00220462262)*$selectResult[csf('size_qty')])*($yarnColorArr[$scolor[csf("sample_color")]]['prod_color_per']/100);
									// echo $sizeWiseProdQtyArr[$selectResult[csf('size_id')]]."*0.00220462262*".$selectResult[csf('size_qty')]."*".$yarnColorArr[$scolor[csf("sample_color")]]['prod_color_per']."/100<br>";
	                            }
	                        }
	                        echo number_format($total_consumption,4,".","");
	                        $grand_total_consumption_wpl += $bundle_weight_rec;
	                        // $grand_total_consumption_wpl += $total_consumption;
	                        
						 	?>
						 
						 </td>
	                    <td width="70" align="right"><?php echo number_format($bundle_weight_rec,4);?></td>
	                    <!-- <td width="70" align="right"><?php echo number_format(($total_consumption_wpl*$selectResult[csf('size_qty')]*2.2046226)/12,4,".","");?></td> -->
	                    <td width="60" align="center"><input type="text" onKeyUp="fnc_total_receive_qty();" id="txt_receive_qtygm_<? echo $i; ?>" name="txt_receive_qtygm[]" style="width:43px;" class="text_boxes_numeric" >
	                    <td width="70" align="center"><input type="text"id="txt_receive_qty_<? echo $i; ?>" name="txt_receive_qty[]" style="width:50px;" class="text_boxes_numeric" readonly></td>
	                    <td width="70" align="center">0.0000</td>
	                    <td width="40" align="center"><p><? echo $year; ?></p></td>
	                    <td width="60" align="center"><p><? echo $job_prifix*1; ?></p></td>
	                    <td width="65" align="center"><?php echo $jbp_arr["buyer_name"]; ?></td>
	                    <td width="90" align="center"><?php  echo $jbp_arr[$selectResult[csf('order_id')]]; ?></td>
	                    <td width="100" align="center"><p><?php echo $garments_item[$selectResult[csf('gmt_item_id')]]; ?></p></td>
	                    <td width="" align="center"><p><? echo $country_library[$selectResult[csf('country_id')]]; ?></p></td>
	                    <td>
	                     	<input type="button" value="-" name="minusButton[]" id="minusButton_<? echo $i;  ?>" style="width:30px" class="formbuttonplasminus"onClick="fn_deleteRow('<? echo $i;  ?>')"/> 
	                        <input type="hidden"id="txt_color_id_<? echo $i; ?>"name="txt_color_id[]"style="width:80px;"value="<?php echo $color_id; ?>"> 
	                        <input type="hidden"id="txt_size_id_<? echo $i; ?>"name="txt_size_id[]"style="width:80px;"value="<?php echo $selectResult[csf('size_id')]; ?>"> 
	                        <input type="hidden"id="txt_order_id_<? echo $i; ?>"name="txt_order_id[]"style="width:80px;"class="text_boxes"value="<?php echo $selectResult[csf('order_id')]; ?>"> 
	                        <input type="hidden"id="txt_gmt_item_id_<? echo $i; ?>"name="txt_gmt_item_id[]"style="width:80px;"class="text_boxes"value="<?php echo $selectResult[csf('gmt_item_id')]; ?>"> 
	                        <input type="hidden"id="txt_country_id_<? echo $i; ?>"name="txt_country_id[]"style="width:80px;"class="text_boxes"value="<?php echo $selectResult[csf('country_id')]; ?>">
	                        <input type="hidden"id="txt_barcode_<? echo $i; ?>"name="txt_barcode[]"style="width:80px;"class="text_boxes"value="<?php echo $selectResult[csf('barcode_no')]; ?>">
	                        <input type="hidden"id="txt_colorsize_id_<? echo $i; ?>"name="txt_colorsize_id[]"style="width:80px;"class="text_boxes"value="<?php echo $selectResult[csf('color_size_break_down_id')]; ?>"> 
	                        <input type="hidden"id="txt_machine_id_<? echo $i; ?>"name="txt_machine_id[]"value="<?php echo $selectResult[csf('machine_id')]; ?>"> 
	                        <input type="hidden"id="txt_dtls_id_<? echo $i; ?>"name="txt_dtls_id[]"style="width:80px;"class="text_boxes"value=""> 
	                        <input type="hidden"id="trId_<? echo $i; ?>"name="trId[]"value="<?php echo $i; ?>">
	                	</td>
	                </tr>
	            	<?php
	                $i++;
	            }
			}
			?>
            </tbody>
            <tfoot>
            	<tr id="bundle_footer">
                    <th colspan="6" > Total</th>
                    <th width="50" id="total_bundle_qty"><?php echo $total_size_qty; ?></th>
                    <th width="50" id="total_bundle_qty"><?php //echo $total_size_qty; ?></th>
                    <?php
					 	$strip_color_arr=array();
						foreach($data_array_strip as $scolor)
						{
							$strip_color_arr[$scolor[csf("stripe_color")]]=$scolor[csf("stripe_color")];
							?>
							<th 
                            	width="100"style=" display:none"id="ttl_<?php echo $scolor[csf("stripe_color")];?>"><?php 
                                	if($scolor[csf("sample_color_ids")]) echo number_format($grand_color_cons_arr[$scolor[csf("sample_color_ids")]],4,".","");
			                    	else  echo number_format($grand_color_cons_arr[$scolor[csf("sample_color")]],4,".","");
                                //echo number_format($grand_color_cons_arr[$scolor[csf("sample_color")]],4,".","");
                                ?> 

                                <input type="hidden"id="percentage_<?php echo $scolor[csf("stripe_color")];?>"name="percentage_<?php echo $scolor[csf("stripe_color")];?>"style="width:80px;"value="<?php
			                            	if($scolor[csf("sample_color_ids")]) echo $issue_color_consumtion[$scolor[csf("sample_color_ids")]];
			                            	else  echo $issue_color_consumtion[$scolor[csf("sample_color")]];

			                             ?>">

                            </th>

							<?php
						}
					?>
                    <th width="70" id="total_color_cons"><?php echo number_format($grand_total_consumption,4,".","");?></th>
                    <th width="70" id="total_color_cons_wpl"><?php echo number_format($grand_total_consumption_wpl,4,".","");?></th>
                    <th width="60" id="total_wst_consmg"><?php //echo number_format($grand_total_consumption,4,".","");?></th>
                    <th width="70" id="total_wst_cons"><?php //echo number_format($grand_total_consumption,4,".","");?></th>
                    <th width="70" id="total_wastageQty"></th>
                    <th width="40"></th>
                    <th width="60"></th>
                    <th width="65"></th>
                    <th width="90"></th>
                    <th width="100"></th>
                    <th></th>
                    <th id="" style="display:none">
                    	<input type="hidden"id="color_id_string"name="color_id_string"value="<?php echo implode(",",$strip_color_arr);?>">
                    </th>
                    <th></th>
                </tr>
            </tfoot>
		</table>
	</div>
	<?
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con 			= connect();
		$delivery_basis =3;
		if($db_type==0)	{ mysql_query("BEGIN"); }
			
		if($db_type==0) $year_cond="YEAR(insert_date)"; else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')"; else $year_cond="";

		$new_sys_number = explode("*", return_next_id_by_sequence("", "pro_gmts_delivery_mst",$con,1,$cbo_company_name,'PRKF',0,date("Y",time()),0,0,51,0,0));
	 	
		$field_array_delivery=" id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, issue_challan_id, production_type, location_id, operator_id,supervisor_id, delivery_basis, production_source, serving_company, floor_id, delivery_date, body_part_type, body_part_ids, working_company_id, working_location_id, remarks, size_set_no, inserted_by, insert_date";

		$mst_id = return_next_id_by_sequence( "pro_gmts_delivery_mst_seq",  "pro_gmts_delivery_mst", $con );

		$data_array_delivery="(".$mst_id.", '".$new_sys_number[1]."', '".(int)$new_sys_number[2]."', '".$new_sys_number[0]."', ".$cbo_company_name.", ".$txt_issue_id.", 51, ".$cbo_location.", ".$txt_operator_id.", ".$hidden_sup_id.", ".$delivery_basis.", ".$cbo_source.", ".$cbo_working_company.", ".$cbo_floor.", ".$txt_receive_date.", ".$cbo_bodypart_type.", ".$txt_bodyPart_id.", ".$cbo_working_company.", ".$cbo_working_location.", ".$txt_remarks.", ".$txt_size_set_no.", ".$user_id.", '".$pc_date_time."')";

		$challan_no 	=(int)$new_sys_number[2];
		$txt_challan_no =$new_sys_number[0];

		for($j=1;$j<=$tot_row;$j++)
        {   
            $bundleCheck="bundleNo_".$j;       
            $bundleCheckArr[$$bundleCheck]=$$bundleCheck;       
        }
        $bundle 		="'".implode("','",$bundleCheckArr)."'";

         $receive_sql="select b.barcode_no, b.bundle_no from pro_garments_production_mst a, pro_garments_production_dtls b , pro_gmts_bundle_bodypart c where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and a.production_type=51 and b.production_type=51 and c.production_type=51 and b.bodypart_type_id=$cbo_bodypart_type and c.body_part_id in (".str_replace("'", "", $txt_bodyPart_id).") and b.bundle_no  in ($bundle)  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and (b.is_rescan=0 or b.is_rescan is null)"; 
       // echo "10**".$receive_sql;die;

        $receive_result = sql_select($receive_sql);
        foreach ($receive_result as $row)
        {           
            $duplicate_bundle[$row[csf('bundle_no')]]=$row[csf('bundle_no')];
        }

 		$colorsize_sql="select id, po_break_down_id, country_id , size_number_id, color_number_id, item_number_id from wo_po_color_size_breakdown where job_no_mst=$txt_job_no and status_active=1 and is_deleted=0 ";

		$colorSizeIdArr 	=array();
		$colorsize_result 	= sql_select($colorsize_sql);
		foreach($colorsize_result as $cs_row)
		{
			$colorSizeIdArr[$cs_row[csf('po_break_down_id')]]
								[$cs_row[csf('country_id')]]
									[$cs_row[csf('item_number_id')]]
										[$cs_row[csf('color_number_id')]]
											[$cs_row[csf('size_number_id')]]=$cs_row[csf('id')];
		}

		
		//$id=return_next_id("id", "pro_garments_production_mst", 1);
		$field_array_mst="  id, delivery_mst_id, cut_no, company_id, garments_nature, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, production_date, production_quantity, production_type, entry_break_down_type, remarks, floor_id, inserted_by, insert_date";

		$mstArr=array(); $dtlsArr=array(); $colorSizeArr=array(); $mstIdArr=array(); 
		for($j=1;$j<=$tot_row;$j++)
		{ 	
			$bundleNo 		="bundleNo_".$j;
			$barcodeNo 		="barcodeNo_".$j;			
			$orderId 		="orderId_".$j;
			$gmtsitemId 	="gmtsitemId_".$j;
			$countryId 		="countryId_".$j;
			$colorId 		="colorId_".$j;
			$sizeId 		="sizeId_".$j;
			$colorSizeId 	="colorSizeId_".$j;
			$checkRescan 	="isRescan_".$j;
			$qty 			="qty_".$j;
			$machine_id 	="machine_id_".$j;
			$bundle_cons 	="bundle_cons_".$j;
			$bundle_consgm 	="bundle_consgm_".$j;
	
			if($duplicate_bundle[$$bundleNo]=='')
            {
				$mstArr[$$orderId][$$gmtsitemId][$$countryId]+=$$qty;
				$colorSizeArr[$$bundleNo] 					=$$orderId."**".$$gmtsitemId."**".$$countryId;
				$dtlsArr[$$bundleNo] 						+=$$qty;
				$dtlsArrCons[$$bundleNo] 					+=$$bundle_cons;
				$dtlsArrConsgm[$$bundleNo] 					+=$$bundle_consgm;
				$dtlsArrColorSize[$$bundleNo] 				=$$colorSizeId;
				$bundleRescanArr[$$bundleNo]				=0;
				$bundleBarcodeArr[$$bundleNo] 				=$$barcodeNo;
				$bundleMachineArr[$$bundleNo] 				=$$machine_id;
			}
		}
		
		$gmts_color_id=$$colorId;
		//echo "10**";print_r($dtlsArrColorSize);die;
		foreach($mstArr as $orderId=>$orderData)
		{
			foreach($orderData as $gmtsItemId=>$gmtsItemIdData)
			{
				foreach($gmtsItemIdData as $countryId=>$qty)
				{
					$id= return_next_id_by_sequence(  "pro_gar_production_mst_seq", "pro_garments_production_mst", $con );
					if($data_array_mst!="") $data_array_mst.=",";
					$data_array_mst.="(".$id.", ".$mst_id.", ".$txt_lot_ratio.", ".$cbo_company_name.", ".$garments_nature.", '".$challan_no."', ".$orderId.", ".$gmtsItemId.", ".$countryId.", ".$cbo_source.", ".$cbo_working_company.", ".$cbo_location.", ".$txt_receive_date.", ".$qty.", 51, 3, ".$txt_remarks.", ".$cbo_floor.", ".$user_id.", '".$pc_date_time."')";
					$mstIdArr[$orderId][$gmtsItemId][$countryId]=$id;
					//$id = $id+1;
				}
			}
		}
		
        $field_array_dtls="  id, delivery_mst_id, mst_id, production_type, color_size_break_down_id, production_qnty, bundle_qty, bundle_qtygm, cut_no, bundle_no, barcode_no, is_rescan, operator_id,supervisor_id, machine_id, bodypart_type_id, body_part_ids";

        $field_array_bodypart_dtls="id, mst_id, dtls_id, delivery_mst_id, bundle_no, is_rescan, barcode_no, bodypart_type_id, machine_id, body_part_id, production_type";

		$field_array_color_dtls="   id, delivery_mst_id, production_type, operator_id,supervisor_id, gmts_color, sample_color, sample_color_ids, yarn_color, required_qty, issue_qty, receive_qty, returanable_qty, wastage, lot_ratio_no, bodypart_type_id, body_part_ids, inserted_by, insert_date";
		
		foreach($dtlsArr as $bundle_no=>$qty)
		{
			$colorSizedData 		=explode("**",$colorSizeArr[$bundle_no]);
			$gmtsMstId 				=$mstIdArr[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[2]];
			if($data_array_dtls!="") $data_array_dtls.=",";

			$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );

			$data_array_dtls.= "(".$dtls_id.", ".$mst_id.", ".$gmtsMstId.", 51, '".$dtlsArrColorSize[$bundle_no]."', '".$qty."', '".$dtlsArrCons[$bundle_no]."','".$dtlsArrConsgm[$bundle_no]."', ".$txt_lot_ratio.", '".$bundle_no."', '".$bundleBarcodeArr[$bundle_no]."', '".$bundleRescanArr[$bundle_no]."', ".$txt_operator_id.", ".$hidden_sup_id.", '".$bundleMachineArr[$bundle_no]."', ".$cbo_bodypart_type.", ".$txt_bodyPart_id.")";

            $bodypart_id_arr=explode(",", str_replace("'", "", $txt_bodyPart_id));
            foreach($bodypart_id_arr as $single_body_part)
            {
                $bodypart_dtls_id   = return_next_id_by_sequence("pro_gmts_bundle_bodypart_seq","pro_gmts_bundle_bodypart", $con );
                if($data_array_bodypart_dtls!="") $data_array_bodypart_dtls.=",";

                $data_array_bodypart_dtls.= "(  ".$bodypart_dtls_id.", ".$gmtsMstId.", ".$dtls_id.", ".$mst_id.", '".$bundle_no."', '".$bundleRescanArr[$bundle_no]."', '".$bundleBarcodeArr[$bundle_no]."', ".$cbo_bodypart_type.", '".$bundleMachineArr[$bundle_no]."', ".$single_body_part.", 51)";
            }
		}
		
        $color_data_string='';
		for($k=1;$k<=$yarn_color_row;$k++)
		{ 	
			$yarnColor 		="yarnColor_".$k;
			$sampleColor 	="sampleColor_".$k;			
			$requiredQty 	="requiredQty_".$k;
			$receiveQty 	="receiveQty_".$k;
			$issueQty 		="issueQty_".$k;
			$returnableQty 	="returnableQty_".$k;			
			$wastage 	  	="wastage_".$k;
						
			$yarn_dtls_id = return_next_id_by_sequence(  "pro_gmts_knit_issue_dtls_seq",  "pro_gmts_knitting_issue_dtls", $con );
			if(count(explode(",", $$sampleColor))>1) $sample_color_id="";
            else  $sample_color_id=$$sampleColor;
			if($data_array_color_dtls!="") $data_array_color_dtls.=",";
			$data_array_color_dtls.= "(".$yarn_dtls_id.", ".$mst_id.", 51, ".$txt_operator_id.", ".$hidden_sup_id.", '".$gmts_color_id."', '".$sample_color_id."', '".$$sampleColor."', '".$$yarnColor."', '".$$requiredQty."', '".$$issueQty."', '".$$receiveQty."', '".$$returnableQty."', '".$$wastage."', ".$txt_lot_ratio.", ".$cbo_bodypart_type.", ".$txt_bodyPart_id.", ".$user_id.", '".$pc_date_time."')";
		      $color_data_string.=$$yarnColor."_".$yarn_dtls_id."#";
		}
		//echo "10**insert into pro_garments_production_mst (".$field_array_mst.") values ".$data_array_mst;die;
		
		$challanrID=sql_insert("pro_gmts_delivery_mst",$field_array_delivery,$data_array_delivery,1);
		$rID=sql_insert("pro_garments_production_mst",$field_array_mst,$data_array_mst,1);
		$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array_dtls,$data_array_dtls,1);
		$yarnColorrID=sql_insert("pro_gmts_knitting_issue_dtls",$field_array_color_dtls,$data_array_color_dtls,1);
        $bundleBodyPartrID=sql_insert("pro_gmts_bundle_bodypart",$field_array_bodypart_dtls,$data_array_bodypart_dtls,1);
		//$bundlerID=sql_insert("pro_gmts_delivery_mst",$field_array_delivery,$data_array_delivery,1);
		//echo "10**".$challanrID."**".$rID."**".$dtlsrID."**".$yarnColorrID."**".$bundleBodyPartrID;die;
	
		if($db_type==0)
		{  
			if($challanrID && $rID && $dtlsrID && $yarnColorrID && $bundleBodyPartrID)
			{
				mysql_query("COMMIT");  
				echo "0**".$mst_id."**".str_replace("'","",$txt_challan_no)."**".str_replace("'","",$color_data_string);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==1 || $db_type==2 )
		{
			if($challanrID && $rID && $dtlsrID && $yarnColorrID && $bundleBodyPartrID)
			{
				oci_commit($con); 
				echo "0**".$mst_id."**".str_replace("'","",$txt_challan_no)."**".str_replace("'","",$color_data_string); 
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		//check_table_status( 160,0);
		disconnect($con);
		die;
		//check_table_status( $_SESSION['menu_id'],0);
	}
  	else if ($operation==1) // Update Here End------------------------------------------------------
	{
 		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
	
		$mst_id=str_replace("'","",$txt_system_id);
		$txt_chal_no=explode("-",str_replace("'","",$txt_challan_no));
		$challan_no=(int) $txt_chal_no[3];

		$field_array_delivery="delivery_date*updated_by*update_date";
		$data_array_delivery="".$txt_receive_date."*".$user_id."*'".$pc_date_time."'";
	
		for($j=1;$j<=$tot_row;$j++)
        {   
            $bundleCheck="bundleNo_".$j;
           // $is_rescan="isRescan_".$j;
           // if($$is_rescan!=1)
           // {
              $bundleCheckArr[$$bundleCheck]=$$bundleCheck; 
           // }
        }
		
		
		
 
        $bundle="'".implode("','",$bundleCheckArr)."'";
        $receive_sql="select c.barcode_no,c.bundle_no from pro_garments_production_mst a,pro_garments_production_dtls c where a.id=c.mst_id and a.production_type=51 and c.bundle_no  in ($bundle)  and c.production_type=51 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.delivery_mst_id!=$mst_id and c.delivery_mst_id!=$mst_id and (c.is_rescan=0 or c.is_rescan is null)"; 
        $receive_result = sql_select($receive_sql);
        foreach ($receive_result as $row)
        {           
            $duplicate_bundle[$row[csf('bundle_no')]]=$row[csf('bundle_no')];
        }
		
		$non_delete_arr=production_validation($mst_id,'3_1');
		$issue_data_arr=production_data($mst_id,'2_1');
		
		$delete = execute_query("DELETE FROM pro_garments_production_mst WHERE delivery_mst_id=$mst_id and production_type=51");
		$delete_dtls = execute_query("DELETE FROM pro_garments_production_dtls WHERE delivery_mst_id=$mst_id and production_type=51");
		 $delete_bodypart_dtls = execute_query("update pro_gmts_bundle_bodypart set status_active=0,is_deleted=1 WHERE delivery_mst_id=$mst_id and production_type=51");
		
		for($j=1;$j<=$tot_row;$j++)
        {   
            $bundleCheck="bundleNo_".$j;       
            $bundleCheckArr[$$bundleCheck]=$$bundleCheck;
        }
            
        $bundle 		="'".implode("','",$bundleCheckArr)."'";
		
		$issueInsSql="select a.sys_number, b.bundle_no from pro_gmts_delivery_mst a, pro_garments_production_dtls b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.production_type=76 and b.entry_form=375 and b.bundle_no in ($bundle)";
		$issueInsSqlRes = sql_select($issueInsSql);
		//$nxtProcessArr=array();
        foreach ($issueInsSqlRes as $row)
        {           
            //$nxtProcessArr[$row[csf('bundle_no')]]=$row[csf('sys_number')];
			
			echo "insIssue**".$mst_id."**".$row[csf('sys_number')];
			disconnect($con);
			die;
        }

        $receive_sql="select c.barcode_no, c.bundle_no from pro_garments_production_mst a, pro_garments_production_dtls b , pro_gmts_bundle_bodypart c where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and a.production_type=51 and b.production_type=51 and c.production_type=51 and b.bodypart_type_id=$cbo_bodypart_type and c.body_part_id in (".str_replace("'", "", $txt_bodyPart_id).") and b.bundle_no  in ($bundle)  and b.production_type=51 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.delivery_mst_id!=$mst_id and b.delivery_mst_id!=$mst_id and (b.is_rescan=0 or b.is_rescan is null)";
       // echo "10**".$receive_sql;die;
        $receive_result = sql_select($receive_sql);
        foreach ($receive_result as $row)
        {           
            $duplicate_bundle[$row[csf('bundle_no')]]=$row[csf('bundle_no')];
        }

        $receive_result = sql_select($receive_sql);
        foreach ($receive_result as $row)
        {           
            $duplicate_bundle[$row[csf('bundle_no')]]=$row[csf('bundle_no')];
        }

 		$colorsize_sql="select id, po_break_down_id, country_id , size_number_id, color_number_id, item_number_id from wo_po_color_size_breakdown where job_no_mst=$txt_job_no and status_active=1 and is_deleted=0 "; 

		$colorSizeIdArr 	=array();
		$colorsize_result 	= sql_select($colorsize_sql);
		foreach($colorsize_result as $cs_row)
		{
			$colorSizeIdArr[$cs_row[csf('po_break_down_id')]]
								[$cs_row[csf('country_id')]]
									[$cs_row[csf('item_number_id')]]
										[$cs_row[csf('color_number_id')]]
											[$cs_row[csf('size_number_id')]]=$cs_row[csf('id')];
		}

		
		//$id=return_next_id("id", "pro_garments_production_mst", 1);
		$field_array_mst="  id, delivery_mst_id, cut_no, company_id, garments_nature, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, production_date, production_quantity, production_type, entry_break_down_type, remarks, floor_id, inserted_by, insert_date";

		$mstArr=array(); $dtlsArr=array(); $colorSizeArr=array(); $mstIdArr=array(); 
		for($j=1;$j<=$tot_row;$j++)
		{ 	
			
			$bundleNo 		="bundleNo_".$j;
			$barcodeNo 		="barcodeNo_".$j;			
			$orderId 		="orderId_".$j;
			$gmtsitemId 	="gmtsitemId_".$j;
			$countryId 		="countryId_".$j;
			$colorId 		="colorId_".$j;
			$sizeId 		="sizeId_".$j;
			$colorSizeId 	="colorSizeId_".$j;
			$checkRescan 	="isRescan_".$j;
			$qty 			="qty_".$j;
			$machine_id 	="machine_id_".$j;
			$bundle_cons 	="bundle_cons_".$j;
			$bundle_consgm 	="bundle_consgm_".$j;
	
			if($duplicate_bundle[$$bundleNo]=='')
            {
				$mstArr[$$orderId][$$gmtsitemId][$$countryId]+=$$qty;
				$colorSizeArr[$$bundleNo] 					=$$orderId."**".$$gmtsitemId."**".$$countryId;
				$dtlsArr[$$bundleNo] 						+=$$qty;
				$dtlsArrCons[$$bundleNo] 					+=$$bundle_cons;
				$dtlsArrConsgm[$$bundleNo] 					+=$$bundle_consgm;
				$dtlsArrColorSize[$$bundleNo] 				=$$colorSizeId;
				$bundleRescanArr[$$bundleNo]				=0;
				$bundleBarcodeArr[$$bundleNo] 				=$$barcodeNo;
				$bundleMachineArr[$$bundleNo] 				=$$machine_id;
			}
		}
		
		$gmts_color_id=$$colorId;
		//echo "10**";print_r($dtlsArrColorSize);die;
		
		foreach($mstArr as $orderId=>$orderData)
		{
			foreach($orderData as $gmtsItemId=>$gmtsItemIdData)
			{
				foreach($gmtsItemIdData as $countryId=>$qty)
				{
					$id= return_next_id_by_sequence(  "pro_gar_production_mst_seq", "pro_garments_production_mst", $con );
					if($data_array_mst!="") $data_array_mst.=",";
					$data_array_mst.="(".$id.", ".$mst_id.", ".$txt_lot_ratio.", ".$cbo_company_name.", ".$garments_nature.", '".$challan_no."', ".$orderId.", ".$gmtsItemId.", ".$countryId.", ".$cbo_source.", ".$cbo_working_company.", ".$cbo_location.", ".$txt_receive_date.", ".$qty.", 51, 3, ".$txt_remarks.", ".$cbo_floor.", ".$user_id.", '".$pc_date_time."')";
					$mstIdArr[$orderId][$gmtsItemId][$countryId]=$id;
					//$id = $id+1;
				}
			}
		}
		
		 $field_array_dtls="  id, delivery_mst_id, mst_id, production_type, color_size_break_down_id, production_qnty, bundle_qty, bundle_qtygm, cut_no, bundle_no, barcode_no, is_rescan, operator_id,supervisor_id, machine_id, bodypart_type_id, body_part_ids";
        $field_array_bodypart_dtls="id, mst_id, dtls_id, delivery_mst_id, bundle_no, is_rescan, barcode_no, bodypart_type_id, machine_id, body_part_id, production_type";
		$field_array_color_dtls="operator_id*supervisor_id* gmts_color* sample_color* sample_color_ids* yarn_color* required_qty* issue_qty* receive_qty* returanable_qty* wastage* lot_ratio_no* bodypart_type_id* updated_by* update_date";
		
		foreach($dtlsArr as $bundle_no=>$qty)
		{
			$colorSizedData 		=explode("**",$colorSizeArr[$bundle_no]);
			$gmtsMstId 				=$mstIdArr[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[2]];
			if($data_array_dtls!="") $data_array_dtls.=",";

			$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );

			$data_array_dtls.= "(".$dtls_id.", ".$mst_id.", ".$gmtsMstId.", 51, '".$dtlsArrColorSize[$bundle_no]."', '".$qty."', '".$dtlsArrCons[$bundle_no]."', '".$dtlsArrConsgm[$bundle_no]."', ".$txt_lot_ratio.", '".$bundle_no."', '".$bundleBarcodeArr[$bundle_no]."', '".$bundleRescanArr[$bundle_no]."', ".$txt_operator_id.", ".$hidden_sup_id.", '".$bundleMachineArr[$bundle_no]."', ".$cbo_bodypart_type.", ".$txt_bodyPart_id.")"; 

            $bodypart_id_arr=explode(",", str_replace("'", "", $txt_bodyPart_id));
            foreach($bodypart_id_arr as $single_body_part)
            {
                $bodypart_dtls_id   = return_next_id_by_sequence("pro_gmts_bundle_bodypart_seq","pro_gmts_bundle_bodypart", $con );
                if($data_array_bodypart_dtls!="") $data_array_bodypart_dtls.=",";

                $data_array_bodypart_dtls.= "(  ".$bodypart_dtls_id.", ".$gmtsMstId.", ".$dtls_id.", ".$mst_id.", '".$bundle_no."', '".$bundleRescanArr[$bundle_no]."', '".$bundleBarcodeArr[$bundle_no]."', ".$cbo_bodypart_type.", '".$bundleMachineArr[$bundle_no]."', ".$single_body_part.", 51)";
            }
		}

		for($k=1;$k<=$yarn_color_row;$k++)
		{ 	
			$yarnColor 			="yarnColor_".$k;
			$yarnColor 			="yarnColor_".$k;
			$sampleColor 		="sampleColor_".$k;			
			$requiredQty 		="requiredQty_".$k;
			$returnableQty 		="returnableQty_".$k;
			$receiveQty 		="receiveQty_".$k;
			$wastage 			="wastage_".$k;
			$issueQty 			="issueQty_".$k;
			$yarnDtlsId 		="yarnDtlsId_".$k;

			$color_dtls_id 		=$$yarnDtlsId;
			$color_dtls_id_arr[]=$color_dtls_id;

			if(count(explode(",", $$sampleColor))>1) $sample_color_id="";
            else  $sample_color_id=$$sampleColor;
			$data_array_color_dtls[$color_dtls_id]= explode("*",($txt_operator_id."* ".$hidden_sup_id."* '".$gmts_color_id."'* '".$sample_color_id."'* '".$$sampleColor."'* '".$$yarnColor."'* '".$$requiredQty."'* '".$$issueQty."'* '".$$receiveQty."'* '".$$returnableQty."'* '".$$wastage."'* ".$txt_lot_ratio."* ".$cbo_bodypart_type."* ".$user_id."* '".$pc_date_time."'"));
		}
		
		$challanrID=sql_update("pro_gmts_delivery_mst",$field_array_delivery,$data_array_delivery,"id",$txt_system_id,1);
		$rID=sql_insert("pro_garments_production_mst",$field_array_mst,$data_array_mst,1);
		$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array_dtls,$data_array_dtls,1);
		$bundleBodyPartrID=sql_insert("pro_gmts_bundle_bodypart",$field_array_bodypart_dtls,$data_array_bodypart_dtls,1);
		//$challanrID=sql_update("pro_gmts_knitting_issue_dtls",$field_array_delivery,$data_array_delivery,"id",$txt_system_id,1);
		$color_dtlsrID=execute_query(bulk_update_sql_statement( "pro_gmts_knitting_issue_dtls", "id", $field_array_color_dtls, $data_array_color_dtls, $color_dtls_id_arr ));
		//echo "10**".bulk_update_sql_statement( "pro_gmts_knitting_issue_dtls", "id", $field_array_color_dtls, $data_array_color_dtls, $color_dtls_id_arr );oci_rollback($con);die;	
		 // echo "10**".$challanrID .'&&'. $rID .'&&'. $dtlsrID .'&&'. $delete .'&&'. $delete_dtls."**".$color_dtlsrID;oci_rollback($con);die;
		//echo "10**".$dtlsrID;oci_rollback($con);die;
		
		
		if($db_type==0)
		{  
			if($challanrID && $rID && $dtlsrID && $delete && $delete_dtls)
			{ 
				mysql_query("COMMIT");  
				echo "1**".$mst_id."**".str_replace("'","",$txt_challan_no)."**".implode(',',$non_delete_arr);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==1 || $db_type==2 )
		{
			if($challanrID && $rID && $dtlsrID && $delete && $delete_dtls)
			{
				oci_commit($con); 
				echo "1**".$mst_id."**".str_replace("'","",$txt_challan_no)."**".implode(',',$non_delete_arr);
				 
			}
			else
			{
				oci_rollback($con);
				echo "10**";
				 
			}
		}
		//check_table_status( 160,0);
		disconnect($con);
		die;
	}
 
	else if ($operation==2)  // Delete Here---------------------------------------------------------- 
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		echo "10";disconnect($con);die;
		//$rID = sql_delete("pro_garments_production_mst","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'id ',$txt_mst_id,1);
		//$dtlsrID = sql_delete("pro_garments_production_dtls","status_active*is_deleted","0*1",'mst_id',$txt_mst_id,1);
		//$mst_id=str_replace("'","",$txt_system_id);
		
 		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "2**".$mst_id."**".str_replace("'","",$txt_challan_no); 
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$mst_id."**".str_replace("'","",$txt_challan_no); 
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);   
				echo "2**".$mst_id."**".str_replace("'","",$txt_challan_no); 
			}
			else
			{
				oci_rollback($con);
				echo "10**".$mst_id."**".str_replace("'","",$txt_challan_no); 
			}
		}
		disconnect($con);
		die;
	}
}

if ($action=="challan_no_popup")
{
	echo load_html_head_contents("Challan Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?> 

	<script>
	
		function js_set_value(id)
		{
			$('#hidden_mst_id').val(id);
			parent.emailwindow.hide();
		}
	
    </script>

	</head>

	<body>
	<div align="center" style="width:1020px;">
		<form name="searchwofrm"  id="searchwofrm">
			<fieldset style="width:1020px;">
			<legend>Enter search words</legend>           
	            <table cellpadding="0" cellspacing="0" width="950" border="1" rules="all" class="rpt_table" align="center">
	                <thead>
	                	<th>Company Name</th>
	                    <th>Job No</th>
	                    <th>Style No</th>
	                    <th>Challan No</th>
	                    <th>Lot Ratio No</th>
						<th>QR Code</th>
	                    <th colspan="2">Receive Date</th>
	                    <th>
	                    	<input 	type="reset" 
	                    			name="reset" 
	                    			id="reset" 
	                    			value="Reset" 
	                    			style="width:100px" 
	                    			class="formbutton" />

	                    	<input 	type="hidden" 
	                    			name="hidden_mst_id" 
	                    			id="hidden_mst_id" 
	                    			class="text_boxes" 
	                    			value="">  
	                    </th> 
	                </thead>
	                <tr class="general">
	                    <td>
	                    	<? 
							$sql_com="select 
											id,
											company_name
										from 
											lib_company comp
						 				where 
											status_active =1 and 
											is_deleted=0 
											$company_cond 
										order by company_name";
										
                        	echo create_drop_down( "cbo_company_name",
													140, 
													$sql_com,
													"id,company_name", 
													1,
													"-- Select --", 
													$selected,
													 "",0 );
                        	?>
	                    </td>
	                    <td align="center" id="">				
	                        <input 	type="text" 
	                        		style="width:80px" 
	                        		class="text_boxes_numeric"  
	                        		name="txt_job_no" 
	                        		id="txt_job_no" />	
	                    </td> 
	                    <td align="center" id="">				
	                        <input 	type="text" 
			                        style="width:80px" 
			                        class="text_boxes"  
			                        name="txt_style_no" 
			                        id="txt_style_no" />	
	                    </td>
	                    <td align="center" id="">				
	                        <input 	type="text" 
	                        		style="width:80px" 
	                        		class="text_boxes"  
	                        		name="txt_challan_no" 
	                        		id="txt_challan_no" />	
	                    </td> 
	                    <td align="center" id="">				
	                        <input 	type="text" 
	                        		style="width:80px" 
	                        		class="text_boxes"  
	                        		name="txt_cutting_no" 
	                        		id="txt_cutting_no" />	
	                    </td>
						<td><input type="text" style="width:90px" class="text_boxes" name="txt_barcode_no" id="txt_barcode_no" /></td>
	                    <td>
	                    	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>
	                    </td> 	
	                    <td>
	                    	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
	                    </td> 	
	            		<td align="center">
	                     	<input type="button" 
	                     		name="button2" 
	                     		class="formbutton" 
	                     		value="Show" 
	                     		style="width:100px;"
	                     		onClick="show_list_view ( 
	                     									document.getElementById('cbo_company_name').value+'_'+
		                     								document.getElementById('txt_challan_no').value+'_'+
		                     								document.getElementById('txt_style_no').value+'_'+
		                     								document.getElementById('txt_job_no').value+'_'+
		                     								document.getElementById('txt_cutting_no').value+'_'+ 
		                     								document.getElementById('txt_date_from').value+'_'+ 
		                     								document.getElementById('txt_date_to').value
															+'_'+ document.getElementById('txt_barcode_no').value, 
		                     							'create_challan_search_list_view', 
		                     							'search_div', 
		                     							'bundle_receive_from_knitting_floor_controller', 
		                     							'setFilterGrid(\'tbl_list_search\',-1);')" />
	                     </td>
	                     
	                </tr>

                    <tr>
                        <td colspan="8" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
                    </tr>
	           	</table>
	           	<div 
	           		id="search_div" 
	           		align="left"
	           		style="	width:100%; 
	           				margin-top:10px; 
	           				margin-left:3px">	           				
	           </div>
			</fieldset>
		</form>
	</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_challan_search_list_view")
{
	$data = explode("_",$data);
	
	//$search_string="'%".trim($data[1)."'";

	$company_id =$data[0];
	if($data[1]!="") $search_field_cond=" and a.sys_number like '%".trim($data[1])."%'";
	if($data[4]!="") $search_field_cond.=" and b.cut_no like '%".trim($data[4])."%'";
	if($data[3]!="") $search_field_cond=" and d.job_no_prefix_num='$data[3]'";
	if($data[2]!="") $search_field_cond.=" and d.style_ref_no like '%".trim($data[2])."%'";
	if(trim($data[7])!="") $search_field_cond.=" and e.barcode_no ='".trim($data[7])."'";

	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later

	if($data[5]!="" && $data[6]!="")
	{
		if($db_type==0)
		{
			$txt_datefrom=change_date_format($data[5],'yyyy-mm-dd');
			$txt_dateto=change_date_format($data[6],'yyyy-mm-dd');
		}
		else if($db_type==2)
		{
			$txt_datefrom=change_date_format($data[5],'','',-1);
			$txt_dateto=change_date_format($data[6],'','',-1);
		}
		$search_field_cond .= " and b.production_date between '$txt_datefrom' and '$txt_dateto'";
	}

	if($company_id==0)
	{
		echo "<div style='font-size:20px;text-align:center;color:red;font-weight:bold;'> Please Select Company First. </div>";die;
	}

	if($data[1]=="" && $data[2]=="" && $data[3]=="" && $data[4]=="" && $data[5]=="" && $data[6]=="" && $data[7]=="")
    {
        echo "<div style='font-size:20px;text-align:center;color:red;font-weight:bold;'>Please enter search value of anyone field.</div>";
        die();
    }
	
	$sql = "SELECT a.id, a.delivery_date, $year_field, a.sys_number_prefix_num, a.sys_number, a.production_source, a.serving_company, a.location_id, a.floor_id, b.cut_no, d.job_no, d.style_ref_no, sum(b.production_quantity) as total_production_qty from pro_gmts_delivery_mst a, pro_garments_production_mst b , wo_po_break_down c, wo_po_details_master d, pro_garments_production_dtls e  where a.id=b.delivery_mst_id and b.po_break_down_id=c.id and c.job_no_mst=d.job_no and b.id=e.mst_id and a.production_type=51 and b.production_type=51 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.company_id=$company_id $search_field_cond group by a.id, a.delivery_date, a.insert_date, a.sys_number_prefix_num, a.sys_number, a.production_source, a.serving_company, a.location_id, a.floor_id, b.cut_no, d.job_no, d.style_ref_no order by a.id desc";
	//echo $sql;
	$result = sql_select($sql);
	$floor_arr=return_library_array( "select id,floor_name from lib_prod_floor",'id','floor_name');
	$location_arr=return_library_array( "select id,location_name from lib_location",'id','location_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1020" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th width="50">Challan</th>
            <th width="50">Year</th>
            <th width="70">Receive Date</th>
            <th width="90">Job No</th>               
            <th width="100">Source</th>
            <th width="110">Knit. Company</th>
            <th width="110">Location</th>
            <th width="100">Floor</th>
            <th width="80">Lot Ratio No</th>
            <th width="60">Challan Qty</th>
            <th>Style Ref.</th>
        </thead>
	</table>
	<div style="width:1020px; max-height:240px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1000" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					 
                if($row[csf('production_source')]==1) $serv_comp=$company_arr[$row[csf('serving_company')]]; else $serv_comp=$supllier_arr[$row[csf('serving_company')]];
        		?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')]; ?>);"> 
                    <td width="30"><? echo $i; ?></td>
                    <td width="50"><p>&nbsp;<? echo $row[csf('sys_number_prefix_num')]; ?></p></td>
                    <td width="50" align="center"><p><? echo $row[csf('year')]; ?></p></td>
                    <td width="70" align="center"><p><? echo change_date_format($row[csf('delivery_date')]); ?></p></td>
                    <td width="90"><p><? echo $row[csf('job_no')]; ?></p></td>               
                    <td width="100"><p><? echo $knitting_source[$row[csf('production_source')]]; ?></p></td>
                    <td width="110"><p><? echo $serv_comp; ?></p></td>
                    <td width="110"><p><? echo $location_arr[$row[csf('location_id')]]; ?></p></td>
                    <td width="100"><p><? echo $floor_arr[$row[csf('floor_id')]]; ?></p></td>
                    <td width="80"><p><? echo $row[csf('cut_no')]; ?></p></td>
                    <td width="60" align="right"><p><? echo $row[csf('total_production_qty')]; ?></p></td>
                    <td><p><? echo $row[csf('style_ref_no')]; ?></p></td>
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

//$new_conn=integration_params(2);
if($action=='populate_data_from_receive')
{
	$data_array=sql_select("SELECT a.sys_number, a.company_id , a.location_id , a.production_type , a.production_source, a.serving_company , a.working_location_id, a.working_company_id, a.body_part_type , a.body_part_ids, a.floor_id, a.challan_no , a.remarks , b.cut_no, a.size_set_no, a.production_source, c.job_no_mst, a.operator_id,a.supervisor_id, a.id, a.delivery_date, a.issue_challan_id, i.sys_number as issue_challan_no from pro_garments_production_mst  b, wo_po_break_down c , pro_gmts_delivery_mst  a inner join pro_gmts_delivery_mst i ON a.issue_challan_id = i.id and i.production_type=50 and i.is_deleted=0 and i.status_active=1 where a.id=$data and a.id=b.delivery_mst_id and b.po_break_down_id=c.id and a.production_type=51 and b.production_type=51 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 "); 

	$issue_qty=return_field_value(" sum(production_qnty) as production_qnty", "pro_garments_production_dtls", " delivery_mst_id=".$data_array[0][csf("issue_challan_id")]." and status_active=1 and is_deleted=0", "production_qnty");
	
	foreach ($data_array as $row)
	{
		echo "document.getElementById('txt_issue_qty').value 				= '".$issue_qty."';\n"; 
		echo "document.getElementById('txt_issue_id').value 				= '".$row[csf("issue_challan_id")]."';\n";
		echo "document.getElementById('txt_issue_no').value  				= '".$row[csf("issue_challan_no")]."';\n";
		echo "document.getElementById('txt_challan_no').value 				= '".$row[csf("sys_number")]."';\n";
		echo "document.getElementById('txt_lot_ratio').value 				= '".$row[csf("cut_no")]."';\n";
		echo "document.getElementById('txt_size_set_no').value 				= '".$row[csf("size_set_no")]."';\n";
		echo "document.getElementById('txt_system_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_job_no').value 					= '".$row[csf("job_no_mst")]."';\n";
		echo "document.getElementById('cbo_company_name').value 			= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_source').value 					= '".$row[csf("production_source")]."';\n";
		echo "load_drop_down( 'requires/bundle_issue_to_knitting_floor_controller', ".($row[csf("production_source")])."+'_'+".($row[csf("company_id")]).", 'load_drop_down_knitting_com','knitting_com');";
		
		echo "document.getElementById('cbo_working_company').value 			= '".$row[csf("working_company_id")]."';\n";
		
		echo "load_location();\n";
		echo "document.getElementById('cbo_working_location').value 		= '".$row[csf("working_location_id")]."';\n";
		echo "load_drop_down( 'requires/bundle_issue_to_knitting_floor_controller', ".$row[csf("working_location_id")].", 'load_drop_down_floor', 'floor_td' );";
		echo "document.getElementById('cbo_floor').value  					= '".($row[csf("floor_id")])."';\n";
		echo "load_drop_down( 'requires/bundle_issue_to_knitting_floor_controller', '".$row[csf('company_id')]."', 'load_drop_down_lc_location', 'location_td' );\n";
		echo "document.getElementById('cbo_location').value  				= '".($row[csf("location_id")])."';\n";
		echo "document.getElementById('txt_receive_date').value  				= '".change_date_format($row[csf("delivery_date")])."';\n";
		echo "document.getElementById('txt_remarks').value  				= '".($row[csf("remarks")])."';\n";
		echo "document.getElementById('txt_operator_id').value  			= '".($row[csf("operator_id")])."';\n";	
		echo "document.getElementById('hidden_sup_id').value  			= '".($row[csf("supervisor_id")])."';\n";	
		

		$bpdypart_id_arr=explode(",", $row[csf("body_part_ids")]);
        $bodypart_name='';
        foreach ($bpdypart_id_arr as $key => $value) {
            if($bodypart_name!="") $bodypart_name.=",";
            $bodypart_name.=$time_weight_panel[$value];
        }
        echo "document.getElementById('txt_bodyPart_id').value              = '".$row[csf("body_part_ids")]."';\n";
        echo "document.getElementById('txt_bodypart_name').value            = '".$bodypart_name."';\n";
        echo "document.getElementById('cbo_bodypart_type').value            = '".$row[csf("body_part_type")]."';\n";	
        $employee_name=return_field_value("(first_name||' '||middle_name|| '  ' || last_name) as emp_name", "hrm_employee", " id_card_no='".($row[csf("operator_id")])."' and status_active=1 and is_deleted=0", "emp_name",$new_conn);
        echo "document.getElementById('txt_operation_name').value           = '".$employee_name."';\n"; 

        $employee_name=return_field_value("(first_name||' '||middle_name|| '  ' || last_name) as emp_name", "hrm_employee", " id_card_no='".($row[csf("supervisor_id")])."' and status_active=1 and is_deleted=0", "emp_name",$new_conn);
        echo "document.getElementById('txt_sup_name').value           = '".$employee_name."';\n"; 

        echo "$('#txt_bodypart_name').attr('disabled',true);\n"; 
        echo "$('#txt_issue_no').attr('disabled',true);\n"; 
       // echo "$('#txt_bodypart_name').attr('disabled',true);\n"; 
		exit();
	}
}

if($action=="show_dtls_listview_update")
{
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
	$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name" );
	$machine_library=return_library_array( "select id,machine_no from lib_machine_name where category_id=1 and status_active=1 and is_deleted=0 and is_locked=0  order by seq_no", "id", "machine_no" );

	$data=explode("_",$data);
	$body_part_id=$data[3];
    $bodypart_cond="b.body_part_id in (".$body_part_id.")";
    $delivery_mst_id=$data[1];
	$sql_cut=sql_select("select a.job_no, a.size_set_no, b.color_id, b.roll_data, a.id, b.id as dtls_id from ppl_cut_lay_mst a, ppl_cut_lay_dtls b where a.cutting_no='".$data[0]."' and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 ");
	$job_no				=$sql_cut[0][csf("job_no")];
	$color_id			=$sql_cut[0][csf("color_id")];
	$consumption_string	=$sql_cut[0][csf("roll_data")];
	$mst_id				=$sql_cut[0][csf("id")];
	$dtls_id			=$sql_cut[0][csf("dtls_id")];
	$size_set_no        =$sql_cut[0][csf("size_set_no")];
	list($shortName,$year,$job_prifix)=explode('-',$job_no);
	//echo $consumption_string;die;
	$consumption_data_arr=explode("**",$consumption_string);
	$color_wise_cons_arr=array();
	foreach($consumption_data_arr as $single_color_consumption)
	{
		$single_color_cons_arr=explode("=",$single_color_consumption);
		$color_wise_cons_arr[$single_color_cons_arr[1]]=$single_color_cons_arr[3];
	}
	
	$job_sql=sql_select("select c.short_name as BUYER_NAME, a.po_number as PO_NUMBER, a.id as ID from wo_po_break_down  a,wo_po_details_master b ,lib_buyer c where b.job_no='".$job_no."' and a.job_no_mst=b.job_no   and b.buyer_name=c.id ");
	$jbp_arr=array();
	foreach($job_sql as $jval)
	{
		$jbp_arr["buyer_name"]=$jval["BUYER_NAME"];
		$jbp_arr[$jval["ID"]]=$jval["PO_NUMBER"];
	}

	$data_array_strip=sql_select("SELECT a.sample_color_ids as sample_color_ids, a.sample_color_id as sample_color, a.yarn_color_id as stripe_color, a.sample_color_percentage, a.production_color_percentage, a.actual_consumption, a.consumption, b.sample_ref,b.id from ppl_size_set_consumption a, ppl_size_set_mst b where b.job_no='".$job_no."' and b.sizeset_no='".$size_set_no."' and b.id=a.mst_id and a.color_id=$color_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 order by a.id ");
	
	$sample_reference	=$data_array_strip[0][csf("sample_ref")];
	$size_set_mstid		=$data_array_strip[0][csf("id")];
	$colspan			=count($data_array_strip);
	$table_width		=1360;
	$div_width			=$table_width+20;
	
	$sql_wet_sheet="SELECT b.color_id, sum(b.bodycolor) as  bodycolor, sum(CASE WHEN $bodypart_cond THEN b.bodycolor ELSE 0 END) as  bodycolor_aspect,b.body_part_id from sample_development_mst a, sample_development_rf_color b where a.id=b.mst_id and a.requisition_number='".$sample_reference."' and b.bodycolor>0 group by b.color_id,b.body_part_id";
	$wet_sheet_result=sql_select($sql_wet_sheet);
	$color_percentage_bodypart=array();
	$bodypart_color_qty_arr=array();
	$knitting_gmm_total=0;
	foreach($wet_sheet_result as $wet_row)
	{
		$color_percentage_bodypart[$wet_row[csf('color_id')]]=$wet_row[csf('bodycolor_aspect')]/$wet_row[csf('bodycolor')];
		$color_qty_bodypart[$wet_row[csf('color_id')]]['body_color']=$wet_row[csf('bodycolor_aspect')]; 
        $color_qty_bodypart[$wet_row[csf('color_id')]]['total_body_color']=$wet_row[csf('bodycolor')]; 

        if($wet_row[csf('body_part_id')]<=5) $body_type="Main"; else $body_type="Accessories";
	   	$bodypart_color_qty_arr[$body_type][$wet_row[csf('body_part_id')]][$wet_row[csf('color_id')]]+=$wet_row[csf('bodycolor')];
	   	$knitting_gmm_total+=$wet_row[csf('bodycolor')];
	}
	foreach($data_array_strip as $scolor)
    {
        if($scolor[csf("sample_color_ids")])
        {
            if(count(explode(",", $scolor[csf("sample_color_ids")]))>1)
            {
                foreach (explode(",", $scolor[csf("sample_color_ids")]) as $sin_sample_color) {
                    $color_qty_bodypart[$scolor[csf("sample_color_ids")]]['body_color']+=$color_qty_bodypart[$sin_sample_color]['body_color']; 
                    $color_qty_bodypart[$scolor[csf("sample_color_ids")]]['total_body_color']+=$color_qty_bodypart[$sin_sample_color]['total_body_color'];
                }
                $color_percentage_bodypart[$scolor[csf("sample_color_ids")]]=($color_qty_bodypart[$scolor[csf("sample_color_ids")]]['body_color']/$color_qty_bodypart[$scolor[csf("sample_color_ids")]]['total_body_color']); 
            }
        }
    }
	//print_r($color_percentage_bodypart);die;
	//echo $sql;die;

	// new add==================================

	$color_size_result=sql_select("SELECT gmt_size_id, production_weight from ppl_size_set_dtls where mst_id='$size_set_mstid' and color_id=$color_id and status_active=1 and is_deleted=0 order by id");
	//echo "select gmt_size_id, production_weight from ppl_size_set_dtls where mst_id='$size_set_mstid' and color_id=$color_id and status_active=1 and is_deleted=0 order by id"; die;
	$sizeWiseProdQtyArr=array();
	foreach($color_size_result as $row)
	{
		if($row[csf('gmt_size_id')]!=0)	
		{
			$sizeWiseProdQtyArr[$row[csf('gmt_size_id')]]=$row[csf('production_weight')];
		}
	}
	unset($color_size_result);


	$sqlStripe=sql_select("SELECT id, sample_color_id, sample_color_ids, production_color_percentage, process_loss, consumption from ppl_size_set_consumption where mst_id='$size_set_mstid' and color_id=$color_id and status_active=1 and is_deleted=0 order by id"); 
	$yarnColorArr=array(); $consumtion_without_process_loss=0;
	 foreach ($sqlStripe as $row)
	 {
		 if($row[csf('sample_color_ids')]!="") $row[csf('sample_color_id')]=$row[csf('sample_color_ids')];
		 $yarnColorArr[$row[csf('sample_color_id')]]['prod_color_per']=$row[csf('production_color_percentage')];
		 $yarnColorArr[$row[csf('sample_color_id')]]['process_loss']=$row[csf('process_loss')];
		 $consumtion_without_process_loss+=$row[csf('consumption')];
	 }
	 unset($sqlStripe);
	 // print_r($consumtion_without_process_loss);
	 $sizeSummArr=array();
	 foreach($yarnColorArr as $ycolor=>$ycolorVal)
	 {
		foreach($sizeWiseProdQtyArr as $gmt_size_id=>$prodQty)
		{
			$colorSizeQty=0;
			$colorSizeQty=(($prodQty*0.00220462262)*12)*($ycolorVal['prod_color_per']/100)*(1+($ycolorVal['process_loss']/100));
			//echo $colorSizeQty.'='.$prodQty.'='.$ycolorVal['prod_color_per'].'='.$ycolorVal['process_loss'].'<br>';
			$sizeSummArr[$ycolor][$gmt_size_id]+=$colorSizeQty;
		}
	 }
	// print_r($sizeSummArr); die;

	// ============================================
	$bodypart_color_total_arr=array();
	$color_bodypartmain_total_arr=array();
	$consumtion_without_process_loss_lbs_per_pcs=($consumtion_without_process_loss*1000)/12;
	foreach($bodypart_color_qty_arr["Main"] as $body_part_id=>$body_part_row)
	{ 
		foreach ($data_array_strip as $sample_color)
		{
			// echo $sample_color[csf('sample_color')].'--'.$knitting_gmm_total.'<br>';
			if($sample_color[csf('sample_color_ids')])
			{
				foreach (explode(",",$sample_color[csf('sample_color_ids')]) as $sc_id) 
				{
					$body_part_row[$sample_color[csf('sample_color_ids')]]+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
					$bodypart_color_total_arr[$body_part_id]+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
					$color_bodypartmain_total_arr[$sample_color[csf('sample_color_ids')]]+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
				}
			}
			else
			{
				$bodypart_color_total_arr[$body_part_id]+=($body_part_row[$sample_color[csf('sample_color')]]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
				$color_bodypartmain_total_arr[$sample_color[csf('sample_color')]]+=($body_part_row[$sample_color[csf('sample_color')]]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
			}
		}
	}
	// echo "<pre>";print_r($color_bodypartmain_total_arr);echo "</pre>";
	// ============================================
	
	$bodypart_color_total_arr=array();
	$color_bodypartacc_total_arr=array();
	$bodypart_main_total=0;
	foreach($bodypart_color_qty_arr["Accessories"] as $body_part_id=>$body_part_row)
	{ 
		foreach ($data_array_strip as  $sample_color)
		{
			if($sample_color[csf('sample_color_ids')])
			{
				foreach (explode(",",$sample_color[csf('sample_color_ids')]) as $sc_id) {
					$body_part_row[$sample_color[csf('sample_color_ids')]]+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
					$bodypart_color_total_arr[$body_part_id]+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
					$color_bodypartacc_total_arr[$sample_color[csf('sample_color_ids')]]+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
					$bodypart_main_total+=($body_part_row[$sc_id]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
				}
			}
			else
			{
				$bodypart_color_total_arr[$body_part_id]+=($body_part_row[$sample_color[csf('sample_color')]]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
				//echo $body_part_row[$sample_color[csf('sample_color')]].'='.$consumtion_without_process_loss_lbs_per_pcs.'='.$knitting_gmm_total.'<br>';
				$color_bodypartacc_total_arr[$sample_color[csf('sample_color')]]+=($body_part_row[$sample_color[csf('sample_color')]]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
				$bodypart_main_total+=($body_part_row[$sample_color[csf('sample_color')]]*$consumtion_without_process_loss_lbs_per_pcs/$knitting_gmm_total);
			}
		}
	}

	// =============================================
	$colorWiseTotArr=array();
	foreach ($data_array_strip as  $sample_color)
	{
		if($sample_color[csf('sample_color_ids')])
		{
			$colorWiseTotArr[$sample_color[csf('sample_color_ids')]]+=$color_bodypartmain_total_arr[$sample_color[csf('sample_color_ids')]]+$color_bodypartacc_total_arr[$sample_color[csf('sample_color_ids')]];
		}
		else
		{
			$colorWiseTotArr[$sample_color[csf('sample_color')]]+=$color_bodypartmain_total_arr[$sample_color[csf('sample_color')]]+$color_bodypartacc_total_arr[$sample_color[csf('sample_color')]];
		}
	}
	//print_r($colorWiseTotArr); die;


	$colorWiseAvgArr=array();
	foreach ($data_array_strip as  $sample_color)
	{
		$avgQty=0;
		if($sample_color[csf('sample_color_ids')])
		{
			$avgQty=$color_bodypartmain_total_arr[$sample_color[csf('sample_color_ids')]]/$colorWiseTotArr[$sample_color[csf('sample_color_ids')]];
			$colorWiseAvgArr[$sample_color[csf('sample_color_ids')]]+=$avgQty;
		}
		else
		{
			$avgQty=$colorWiseTotArr[$sample_color[csf('sample_color')]]/$colorWiseTotArr[$sample_color[csf('sample_color')]];
			// $avgQty=$color_bodypartmain_total_arr[$sample_color[csf('sample_color')]]/$colorWiseTotArr[$sample_color[csf('sample_color')]];
			$colorWiseAvgArr[$sample_color[csf('sample_color')]]+=$avgQty;
		}
	}

	// echo "<pre>";print_r($colorWiseAvgArr);echo "</pre>";
	?>	
       <table cellpadding="0"width="<?php echo $div_width;?>"cellspacing="0"border="1"class="rpt_table"rules="all">
            <thead>
            	<tr>
                    <th width="30" rowspan="3">SL</th>
                    <th width="90" rowspan="3">Bundle No</th>
                    <th width="90" rowspan="3">Barcode No</th>
                    <th width="70" rowspan="3">MC No</th>
                    <th width="120" rowspan="3">G. Color</th>
                    <th width="50" rowspan="3">Size</th>
                    <th width="50" rowspan="3">Bundle Qty. (Pcs)</th>
                    <th width="50" rowspan="3">Knit Qty.(Pcs)</th>
                    <th width="70" rowspan="3">Incl. Process Loss (Lbs)</th>
                    <th width="70" rowspan="3">Exc. Process Loss (Lbs)</th>
                    <th width="60" rowspan="3">Bundle Weight Rec. (GM)</th>
                    <th width="70" rowspan="3">Bundle Weight Rec. (Lbs)</th>
                    <th width="70" rowspan="3">Wastage Qty (Lbs)</th>

                    <th width="40" rowspan="3">Year</th>
                    <th width="60" rowspan="3">Job No</th>
                    <th width="65" rowspan="3">Buyer</th>
                    <th width="90" rowspan="3">Order No</th>
                    <th width="100" rowspan="3">Gmts. Item</th>
                    <th rowspan="3">Country
                    	<input type="hidden"id="txt_total_color"name="txt_total_color"style="width:80px;"value="<?php echo $colspan; ?>">
                    </th>
                </tr>
            </thead>
        </table>
	<div style="width:<?php echo $div_width;?>px;max-height:250px;overflow-y:scroll"  align="left"> 
        <table cellpadding="0"width="<?php echo $table_width;?>"cellspacing="0"border="1"class="rpt_table"rules="all"id="tbl_details"> <tbody>
		<?php  
			$i=1; $total_production_qnty=0; $grand_color_cons_arr=array(); $issue_color_consumtion=array();
			$sqlResult =sql_select("select b.* , a.gmt_item_id, c.machine_id, c.bundle_qty, c.bundle_qtygm, c.production_qnty, c.color_size_break_down_id from ppl_cut_lay_dtls a, ppl_cut_lay_bundle b, pro_garments_production_dtls c where c.delivery_mst_id=$delivery_mst_id and b.mst_id=$mst_id and b.dtls_id=$dtls_id and c.bundle_no=b.bundle_no and a.id=b.dtls_id and a.color_id=$color_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

			foreach($sqlResult as $selectResult)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
 			?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="" > 
                    <td width="30" align="center"><? echo $i; ?></td>
                    <td width="90" align="center"><p><? echo $selectResult[csf('bundle_no')]; ?></p></td>
                    <td width="90" align="center"><p><? echo $selectResult[csf('barcode_no')]; ?></p></td>
                    <td width="70" align="center"><p><? echo $machine_library[$selectResult[csf('machine_id')]]; ?></p></td>
                    <td width="120" align="center" style="word-break:break-all"><p><?php  echo $color_library[$color_id]; ?></p></td>
                    <td width="50" align="center"><?php echo $size_library[$selectResult[csf('size_id')]]; ?></td>
                    <td width="50" align="center"><?php echo $selectResult[csf('size_qty')]; ?></td>
                    <td width="50" align="center"><input type="text"id="txt_knit_qty_<? echo $i; ?>" onkeyup="fnc_check_knit_qty(<?=$i; ?>);"name="txt_knit_qty[]"style="width:38px;"class="text_boxes_numeric"value="<?php  echo $selectResult[csf('production_qnty')]; ?>"></td>
             		<td width="70" align="right">
                    <?php
						$total_consumption=0; $total_consumption_wpl=0;$bundle_weight_rec=0;
						/*foreach($data_array_strip as $scolor)
						{
							if($scolor[csf('sample_color_ids')])
							{
								if($i==1)
								{
									$issue_color_consumtion[$scolor[csf("sample_color_ids")]]=$scolor[csf("consumption")]*$color_percentage_bodypart[$scolor[csf("sample_color_ids")]];
								}
								$total_consumption+=$scolor[csf("actual_consumption")]*$color_percentage_bodypart[$scolor[csf("sample_color_ids")]];
								$total_consumption_wpl+=$scolor[csf("consumption")]*$color_percentage_bodypart[$scolor[csf("sample_color_ids")]];
								$grand_color_cons_arr[$scolor[csf("sample_color")]]+=($scolor[csf("actual_consumption")]*$selectResult[csf('size_qty')]*$color_percentage_bodypart[$scolor[csf("sample_color_ids")]]*2.2046226)/12;
							}
							else
							{
								if($i==1)
								{
									$issue_color_consumtion[$scolor[csf("sample_color")]]=$scolor[csf("consumption")]*$color_percentage_bodypart[$scolor[csf("sample_color")]];
								}
								$total_consumption+=$scolor[csf("actual_consumption")]*$color_percentage_bodypart[$scolor[csf("sample_color")]];
								$total_consumption_wpl+=$scolor[csf("consumption")]*$color_percentage_bodypart[$scolor[csf("sample_color")]];
								$grand_color_cons_arr[$scolor[csf("sample_color")]]+=($scolor[csf("actual_consumption")]*$selectResult[csf('size_qty')]*$color_percentage_bodypart[$scolor[csf("sample_color")]]*2.2046226)/12;
							}
						}
						//print_r($grand_color_cons_arr);die;
						$grand_total_consumption+=($total_consumption*$selectResult[csf('size_qty')]*2.2046226)/12;
						$total_size_qty+=$selectResult[csf('size_qty')];
						$grand_total_consumption_wpl+=($total_consumption_wpl*$selectResult[csf('size_qty')]*2.2046226)/12;
					 	echo number_format(($total_consumption*$selectResult[csf('size_qty')]*2.2046226)/12,4,".","");*/

					 	foreach($data_array_strip as $scolor)
						{
						 	if($scolor[csf("sample_color_ids")])
                            {                                
								$yarnColorWiseLbsQty=(($colorWiseAvgArr[$scolor[csf("sample_color_ids")]]*$sizeSummArr[$scolor[csf("sample_color_ids")]][$selectResult[csf('size_id')]])/12)*$selectResult[csf('size_qty')];
								// echo $colorWiseAvgArr[$scolor[csf("sample_color_ids")]]."*".$sizeSummArr[$scolor[csf("sample_color_ids")]][$selectResult[csf('size_id')]]."/12)*".$selectResult[csf('size_qty')];
								// echo number_format($yarnColorWiseLbsQty,4,".","");
								$total_consumption+=$yarnColorWiseLbsQty;
								$grand_total_consumption+=$yarnColorWiseLbsQty;
								$grand_color_cons_arr[$scolor[csf("sample_color_ids")]]+=$yarnColorWiseLbsQty;
								$bundle_weight_rec += (($sizeWiseProdQtyArr[$selectResult[csf('size_id')]]*0.00220462262)*$selectResult[csf('size_qty')])*($yarnColorArr[$scolor[csf("sample_color_ids")]]['prod_color_per']/100);
                            }
                            else 
                            {
								$yarnColorWiseLbsQty=(($colorWiseAvgArr[$scolor[csf("sample_color")]]*$sizeSummArr[$scolor[csf("sample_color")]][$selectResult[csf('size_id')]])/12)*$selectResult[csf('size_qty')];
								// echo number_format($yarnColorWiseLbsQty,4,".","");echo "<br>";

								// echo $colorWiseAvgArr[$scolor[csf("sample_color")]]."*".$sizeSummArr[$scolor[csf("sample_color")]][$selectResult[csf('size_id')]]."/12)*".$selectResult[csf('size_qty')]."<br>";

								$total_consumption+=$yarnColorWiseLbsQty;
								$grand_total_consumption+=$yarnColorWiseLbsQty;
								$grand_color_cons_arr[$scolor[csf("sample_color")]]+=$yarnColorWiseLbsQty;
								$bundle_weight_rec += (($sizeWiseProdQtyArr[$selectResult[csf('size_id')]]*0.00220462262)*$selectResult[csf('size_qty')])*($yarnColorArr[$scolor[csf("sample_color")]]['prod_color_per']/100);
								// echo $sizeWiseProdQtyArr[$selectResult[csf('size_id')]]."*0.00220462262*".$selectResult[csf('size_qty')]."*".$yarnColorArr[$scolor[csf("sample_color")]]['prod_color_per']."/100<br>";
                            }
                        }
                        echo number_format($total_consumption,4,".","");
                        $grand_total_consumption_wpl += $bundle_weight_rec;
					 	?>
					 
					 </td>
                    <td width="70" align="right"><?=number_format($bundle_weight_rec,4);?></td>
                    <td width="60" align="center"><input type="text"onkeyup="fnc_total_receive_qty();"id="txt_receive_qtygm_<? echo $i; ?>"name="txt_receive_qtygm[]"style="width:43px;"class="text_boxes_numeric"value="<?=$selectResult[csf('bundle_qtygm')]; ?>">
                    </td>
                    <td width="70" align="center"> 
                    	<input type="text" id="txt_receive_qty_<? echo $i; ?>"name="txt_receive_qty[]"style="width:50px;"class="text_boxes_numeric"value="<?=$selectResult[csf('bundle_qty')]; ?>" readonly>
                    </td>
                    <?php
                    	$wastage_qty=(($total_consumption*$selectResult[csf('size_qty')]*2.2046226)/12)-$selectResult[csf('bundle_qty')];
                    	$total_production_qnty+=$selectResult[csf('production_qnty')];
						$total_receive_qty+=$selectResult[csf('bundle_qty')];
						$total_receive_qtygm+=$selectResult[csf('bundle_qtygm')];
						$total_wastage_qty+=$wastage_qty; 
                    ?>
                    <td width="70" align="center"><?=number_format($wastage_qty,4,".",""); ?> </td>
                    <td width="40" align="center"><p><?=$year; ?></p></td>
                    <td width="60" align="center"><p><?=$job_prifix*1; ?></p></td>
                    <td width="65" align="center"><?=$jbp_arr["buyer_name"]; ?></td>
                    <td width="90" align="center"><?=$jbp_arr[$selectResult[csf('order_id')]]; ?></td>
                    		
                    <td width="100" align="center"><p><?=$garments_item[$selectResult[csf('gmt_item_id')]]; ?></p></td>
                    <td width="" align="center"><p><?=$country_library[$selectResult[csf('country_id')]]; ?></p></td>
                    <td style="display: none;">
                     	<input type="button"value="-"name="minusButton[]"id="minusButton_<? echo $i;  ?>"style="width:30px"class="formbuttonplasminus"onClick="fnc_minusRow('<? echo $i;  ?>')"/>
                        <input type="hidden"id="txt_color_id_<? echo $i; ?>"name="txt_color_id[]"style="width:80px;"value="<?php echo $color_id; ?>">
                        <input type="hidden"id="txt_size_id_<? echo $i; ?>"name="txt_size_id[]"style="width:80px;"value="<?php echo $selectResult[csf('size_id')]; ?>">
						<input type="hidden"id="txt_order_id_<? echo $i; ?>"name="txt_order_id[]"style="width:80px;"class="text_boxes"value="<?php echo $selectResult[csf('order_id')]; ?>">
                       	<input type="hidden"id="txt_gmt_item_id_<? echo $i; ?>"name="txt_gmt_item_id[]"style="width:80px;"class="text_boxes"value="<?php echo $selectResult[csf('gmt_item_id')]; ?>">
                        <input type="hidden"id="txt_country_id_<? echo $i; ?>"name="txt_country_id[]"style="width:80px;"class="text_boxes"value="<?php echo $selectResult[csf('country_id')]; ?>">
                     	<input type="hidden"id="txt_barcode_<? echo $i; ?>"name="txt_barcode[]"style="width:80px;"class="text_boxes"value="<?php echo $selectResult[csf('barcode_no')]; ?>"> 
                        <input type="hidden"id="txt_colorsize_id_<? echo $i; ?>"name="txt_colorsize_id[]"style="width:80px;"class="text_boxes"value="<?php echo $selectResult[csf('color_size_break_down_id')]; ?>">
                        <input type="hidden"id="txt_machine_id_<? echo $i; ?>"name="txt_machine_id[]"value="<?php echo $selectResult[csf('machine_id')]; ?>">
                        <input type="hidden"id="txt_dtls_id_<? echo $i; ?>"name="txt_dtls_id[]"style="width:80px;"class="text_boxes"value="">
                        <input type="hidden"id="trId_<? echo $i; ?>"name="trId[]"value="<?php echo $i; ?>">
                	</td>
                </tr>
            <?php
                $i++;
			}
			?>
            </tbody>
            <tfoot>
            	<tr id="bundle_footer">
                    <th colspan="6"> Total</th>
                    <th width="40" id="total_bundle_qty"><?php echo $total_size_qty; ?></th>
                    <th width="50" id="total_bundle_qty"><?php echo $total_production_qnty; ?></th>
                
                    <?php
					 	$strip_color_arr=array();
						foreach($data_array_strip as $scolor)
						{
							$strip_color_arr[$scolor[csf("stripe_color")]]=$scolor[csf("stripe_color")];
							?>
							<th 
                            	width="100" 
                                style=" display:none"
                                id="ttl_<?php echo $scolor[csf("stripe_color")];?>"><?php
                                if($scolor[csf('sample_color_ids')])
								{
									echo number_format($grand_color_cons_arr[$scolor[csf("sample_color_ids")]],4,".","");
								}
								else
								{
									echo number_format($grand_color_cons_arr[$scolor[csf("sample_color")]],4,".","");
								}
                                 ?> 
                                <input type="hidden"id="percentage_<?php echo $scolor[csf("stripe_color")];?>"name="percentage_<?php echo $scolor[csf("stripe_color")];?>"style="width:80px;"value="<?php
			                            if($scolor[csf('sample_color_ids')])
										{
											echo $issue_color_consumtion[$scolor[csf("sample_color_ids")]];
										}
										else
										{
											echo $issue_color_consumtion[$scolor[csf("sample_color")]];
										}
			                            ?>">
                            </th>
							<?php
						}
					?>
                    <th width="70" id="total_color_cons"><?php echo number_format($grand_total_consumption,4,".","");?></th>
                    <th width="70" id="total_color_cons_wpl"><?php echo number_format($grand_total_consumption_wpl,4,".","");?></th>
                    <th width="60" id="total_wst_consmg"><?php echo number_format($total_receive_qtygm,4,".","");?></th>
                    <th width="70" id="total_wst_cons"><?php echo number_format($total_receive_qty,4,".","");?></th>
                    <th width="70" id="total_wastageQty"><?php echo number_format($total_wastage_qty,4,".","");?></th>
                    <th width="40"></th>
                    <th width="60"></th>
                    <th width="65"></th>
                    <th width="90"></th>
                    <th width="100"></th>
                    <th>
                    	<input type="hidden"id="color_id_string"name="color_id_string"value="<?php echo implode(",",$strip_color_arr);?>">
                    </th>
                </tr>
            </tfoot>
		</table>
	</div>
	<?
	exit();
}


if($action=="show_dtls_yarn_listview_update")
{
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$data=explode("_",$data);

	$sql_cut=sql_select("SELECT a.job_no, b.color_id, b.roll_data, a.id, b.id as dtls_id from ppl_cut_lay_mst a, ppl_cut_lay_dtls b where a.cutting_no='".$data[0]."' and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 ");
	$job_no				=$sql_cut[0][csf("job_no")];
	$color_id			=$sql_cut[0][csf("color_id")];
	$mst_id				=$sql_cut[0][csf("id")];
	$dtls_id			=$sql_cut[0][csf("dtls_id")];

	// ==================== getting issued bundle ===========
	$issued_bundle=return_library_array( "SELECT bundle_no,bundle_no from pro_garments_production_dtls where status_active=1 and is_deleted=0 and delivery_mst_id=$data[2] and production_type=50", "bundle_no", "bundle_no"  ); 
	$bundle_cond = where_con_using_array($issued_bundle,1,"bundle_no");
	// ==================== getting delv id ===========
	$delivery_ids=return_library_array( "SELECT delivery_mst_id,delivery_mst_id from pro_garments_production_dtls where status_active=1 and is_deleted=0 $bundle_cond and production_type=51", "delivery_mst_id", "delivery_mst_id"  ); 
	$delivery_cond = where_con_using_array($delivery_ids,0,"delivery_mst_id");

	$sql="SELECT gmts_color , yarn_color, sample_color , sample_color_ids, receive_qty from pro_gmts_knitting_issue_dtls where production_type=51 and status_active=1 and is_deleted=0 $delivery_cond";
	// echo $sql;
	$res = sql_select($sql);
	$prev_rcv_qty_arr = array();
	foreach ($res as $val) 
	{
		$prev_rcv_qty_arr[$val['GMTS_COLOR']][$val['YARN_COLOR']][$val['SAMPLE_COLOR']][$val['SAMPLE_COLOR_IDS']] += $val['RECEIVE_QTY'];
	}
	// echo "<pre>";print_r($prev_rcv_qty_arr);echo "</pre>";

	$data_array_strip=sql_select("SELECT id , gmts_color , yarn_color, sample_color , sample_color_ids , required_qty, returanable_qty , issue_qty , wastage, receive_qty from pro_gmts_knitting_issue_dtls where delivery_mst_id=$data[1] and production_type=51 and status_active=1 and is_deleted=0 order by id ");
	
	$sample_reference	=$data_array_strip[0][csf("sample_ref")];
	$colspan			=count($data_array_strip);

	// ========================= size set consumption ====================
	$sql="SELECT c.color_id,c.sample_color_id, c.yarn_color_id,sum(c.actual_consumption) as actual_consumption from ppl_size_set_mst a, ppl_size_set_consumption c where a.id=c.mst_id and a.job_no='$job_no' and c.color_id=$color_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.color_id,c.sample_color_id, c.yarn_color_id";
	// echo $sql;
	$res = sql_select($sql);
	$total_cons = 0;
	foreach ($res as $val) 
	{
		$total_cons += $val['ACTUAL_CONSUMPTION']*2.2046;
	}

	$size_set_avg_cons_arr = array();
	foreach ($res as $val) 
	{
		$size_set_avg_cons_arr[$val['SAMPLE_COLOR_ID']][$val['YARN_COLOR_ID']] += ($total_cons) ? ($val['ACTUAL_CONSUMPTION']*2.2046)/$total_cons : 0;
	}
	// echo "<pre>"; print_r($size_set_avg_cons_arr); echo "</pre>";

	

	
	?>	
    <table 
        cellpadding="0" 
        width="930" 
        cellspacing="0" 
        border="1" 
        class="rpt_table" 
        rules="all">
        
        <thead>
            <th width="30">SL</th>
            <th width="100">Sample Color</th>
            <th width="200">Yarn Color</th>
            <th width="100">Required Qty(Lbs)</th>
            <th width="100">Issue Qty. (Lbs)</th>
            <th width="100">Cuml. Rcv Qty. (Lbs)</th>
            <th width="100">Receive Qty. (Lbs)</th>
            <th width="100">Returnable Qty (Lbs)</th>
            <th width="">Receive Balance</th>
           
        </thead>
    </table>
			
		
		
	<div 
        style="width:950px;max-height:250px;overflow-y:scroll" 
        align="left"> 
           
        <table 
            cellpadding="0" 
            width="930" 
            cellspacing="0" 
            border="1" 
            class="rpt_table" 
            rules="all" 
            id="tbl_yarn_details">      
            <tbody>
            
		<?php  
			$i=1;	
			foreach($data_array_strip as $row)
			{
				if ($i%2==0)  $bgcolor="#E9F3FF";
                else $bgcolor="#FFFFFF";
                $prev_rcv_qty = $prev_rcv_qty_arr[$row['GMTS_COLOR']][$row['YARN_COLOR']][$row['SAMPLE_COLOR']][$row['SAMPLE_COLOR_IDS']];
 			?>
                <tr 
                	bgcolor="<? echo $bgcolor; ?>" 
                	style="text-decoration:none; cursor:pointer" 
                	id="tr_<? echo $i; ?>" > 

                    <td width="30" align="center"><? echo $i; ?></td>
                    <td width="100" align="center"><p><? 
                    	foreach (explode(",",$row[csf('sample_color_ids')]) as  $sin_smaple_color) {
                           echo $color_library[$sin_smaple_color]." ";
                        }

                   		// echo $color_library[$row[csf('sample_color')]];
                   	?></p></td>
                    <td width="200" align="center" style="word-break:break-all"><p><?php  echo $color_library[$row[csf('yarn_color')]]; ?></p></td>
                    <td 
                    	width="100" 
                    	align="right"
                        id="required_qty_<? echo $i; ?>"><?php  echo number_format($row[csf('required_qty')],4,".",""); ?></td>
                    		
                    <td width="100" align="right"><p><?php echo number_format($row[csf('issue_qty')],4,".",""); ?></p></td>
                    <td width="100" align="right" id="cuml_rcv_qty_<? echo $i; ?>"><?php echo number_format($prev_rcv_qty,4,".",""); ?></td>
                    <td width="100" align="right" id="returanable_qty_<? echo $i; ?>"><?php echo number_format($row[csf('receive_qty')],4,".",""); ?></td>
                    <td width="100" align="center">

                    	<input type="hidden" id="prev_rcv_qty_<? echo $i; ?>" value="<?php echo number_format($prev_rcv_qty,4); ?>">
                    	<input 
                    		onkeyup="fnc_total_issue_balance() "
                        	type="text" 
                            id="txt_returnable_qty_<? echo $i; ?>" 
                            name="txt_returnable_qty[]"
                            style="width:80px;"
                            class="text_boxes_numeric"
                            value="<?php echo $row[csf('returanable_qty')]; ?>">
                            
                        <input
                        	type="hidden" 
                            id="hidden_yarn_color_<? echo $i; ?>" 
                            name="hidden_yarn_color[]"
                            value="<?php echo $row[csf('yarn_color')]; ?>">
                        <input
                        	type="hidden" 
                            id="hidden_sample_color_<? echo $i; ?>" 
                            name="hidden_sample_color[]"
                            value="<?php echo $row[csf('sample_color_ids')]; ?>">
                        <input
                        	type="hidden" 
                            id="hidden_yarn_dtls_id_<? echo $i; ?>" 
                            name="hidden_yarn_dtls_id[]"
                            value="<?php echo $row[csf('id')]; ?>">

                        <input
                        	type="hidden" 
                            id="hidden_size_set_cons_<? echo $i; ?>" 
                            name="hidden_size_set_cons[]"
                            value="<?=$size_set_avg_cons_arr[$row[csf('sample_color')]][$row[csf('yarn_color')]];?>">

                    </td>
                    <td width="" align="right"><?php echo number_format($row[csf('wastage')],4,".","");?></td>
                </tr>
            <?php

            	$total_required_qty 		+=$row[csf('required_qty')];
            	$total_returanable_qty 		+=$row[csf('returanable_qty')];
            	$total_issue_qty 			+=$row[csf('issue_qty')];
            	$total_wastage_qty 			+=$row[csf('wastage')];
            	$total_receive_qty 			+=$row[csf('receive_qty')];
            	$total_prev_rcv_qty 		+=$prev_rcv_qty;
                $i++;
			}
			?>
            </tbody>
            <tfoot>
            	<tr>
                    <th colspan="3" > Total</th>
                    <th width="100"  id="total_required_qty"><?php 		echo number_format($total_required_qty,4,".","");?></th>
                    <th width="100"  id="total_issue_qty"><?php 	echo number_format($total_issue_qty,4,".","");?></th>       
                    <th width="100"  id="total_cuml_rcv_qty"><?php 		echo number_format($total_prev_rcv_qty,4,".","");?></th>
                    <th width="100"  id="total_receive_qty"><?php 		echo number_format($total_receive_qty,4,".","");?></th>
                    <th width="100"  id="total_returnable_qty"><?php 	echo number_format($total_returanable_qty,4,".","");?></th>
                    <th width=""  	 id="total_wastage_qty"><?php 		echo number_format($total_wastage_qty,4,".","");?></th>
                </tr>
            </tfoot>
		</table>
	</div>
	<?
	exit();
}

if ($action=="load_drop_down_floor")
{
	
 	echo create_drop_down( "cbo_floor", 140, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and production_process in (2) order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",1 );
	exit();     	 
} 

if ($action == "load_drop_down_knitting_com") {
	$data = explode("_", $data);
	$company_id = $data[1];
	//$company_id
	if ($data[0] == 1) {
		echo create_drop_down("cbo_working_company", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0  order by comp.company_name", "id,company_name", 1, "--Select Knit Company--", $company_id, "load_location();", 1);
	} else if ($data[0] == 3) {
		echo create_drop_down("cbo_working_company", 140, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--Select Knit Company--", 0, "load_location();");
	} else {
		echo create_drop_down("cbo_working_company", 140, $blank_array, "", 1, "--Select Knit Company--", 0, "load_location();");
	}
	exit();
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_working_location", 140, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down('requires/bundle_receive_from_knitting_floor_controller', this.value, 'load_drop_down_floor', 'floor_td' );",1 );
	exit();   
}


if ($action=="load_drop_down_lc_location")
{
	echo create_drop_down( "cbo_location", 140, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "",1 );
	exit();   
}

if($action=="bundle_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	list($shortName,$ryear,$ratio_prifix)=explode('-',$lot_ratio);
	if($ryear=="") 	$ryear=date("Y",time());
	else 			$ryear=("20$ryear")*1;
	//echo $ryear;die;
	?>
	<script>
	
		function check_all_data() {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length; 
			for( var i = 1; i <= tbl_row_count; i++ ) {
				
				if($("#search"+i).css("display") !='none'){
				 js_set_value( i );
				}
			}
		}
		
		
		var selected_id = new Array();
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str) 
		{
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_individual' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual' + str).val() );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			
			$('#hidden_bundle_nos').val( id );
		 
		}
		
		function fnc_close()
		{	
			//alert($('#hidden_bundle_nos').val());
			parent.emailwindow.hide();
		}
		
		function reset_hide_field()
		{
			$('#hidden_bundle_nos').val( '' );
			selected_id = new Array();
		}
    </script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
		<form name="searchwofrm"  id="searchwofrm">
			<fieldset style="width:810px;">
			<legend></legend>           
	            <table cellpadding="0" cellspacing="0" width="550" border="1" rules="all" class="rpt_table">
	                <thead>
	                	<th>Company</th>
	                    <th>Lot Ratio Year</th>
	                    <th>Job No</th>
	                    <th>Order No</th>
	                    <th class="must_entry_caption">Ratio No</th>
	                    <th>Bundle No</th>
	                    <th>
	                    	<input 
	                        	type="reset" 
	                            name="reset" 
	                            id="reset" 
	                            value="Reset" 
	                            style="width:100px" 
	                            class="formbutton" />
	                        <input 
	                        	type="hidden" 
	                            name="hidden_bundle_nos" 
	                            id="hidden_bundle_nos"> 
	                         <input 
	                            type="hidden" 
	                            name="hidden_lot_no" 
	                            id="hidden_lot_no" 
	                            value="<?php echo $lot_ratio; ?>" />
	                    </th>
	                </thead>
	                <tr class="general">
	                	<td>
						<? 
	                        $sql_com="select 
	                                        id,
	                                        company_name
	                                    from 
	                                        lib_company comp
	                                    where 
	                                        status_active =1 and 
	                                        is_deleted=0 
	                                        $company_cond 
	                                    order by company_name";
	                                    
	                        echo create_drop_down( "cbo_company_name",
	                                                140, 
	                                                $sql_com,
	                                                "id,company_name", 
	                                                1,
	                                                "-- Select --", 
	                                                $company_id,
	                                                 "",0 );
	                    ?>
	                    </td>
	                    <td align="center">				
	                    <?
							echo create_drop_down( "cbo_lot_year"
													, 60, 
													$year,
													'', 
													"", 
													'-- Select --',
													$ryear,
													 "" );
						?>
	                    </td> 				
	                    <td align="center">				
	                        <input 
	                        	type="text" 
	                            style="width:130px" 
	                            class="text_boxes" 
	                            name="txt_job_no" 
	                            id="txt_job_no" />	
	                    </td> 				
	                    <td align="center" id="search_by_td">				
	                        <input 
	                        	type="text" 
	                            style="width:130px" 
	                            class="text_boxes" 
	                            name="txt_order_no" 
	                            id="txt_order_no" />	
	                    </td> 				
	                    <td>
	                        <input 
	                            type="text" 
	                            name="txt_lot_no" 
	                            id="txt_lot_no" 
	                            style="width:120px" 
	                            class="text_boxes"
	                            value="<?php if($ratio_prifix) echo $ratio_prifix*1; ?>" />
	                    </td>  		
	                    <td>
	                    	<input 
	                        	type="text" 
	                            name="bundle_no" 
	                            id="bundle_no" 
	                            style="width:120px" 
	                            class="text_boxes" />
	                    </td>  		
	            		<td align="center">
	                     	<input 
	                        	type="button" 
	                            name="button2" 
	                            class="formbutton" 
	                            value="Show" 
	                            onClick="show_list_view (
	                            			document.getElementById('txt_order_no').value+'_'+
	                                        document.getElementById('cbo_company_name').value+'_'+
	                            			document.getElementById('bundle_no').value+'_'+
	                            			'<? echo trim($bundleNo,','); ?>'+'_'+
	                            			document.getElementById('txt_job_no').value+'_'+
	                                		document.getElementById('txt_lot_no').value+'_'+
	                                		document.getElementById('cbo_lot_year').value+'_'+
	                                        '<? echo trim($lot_ratio,','); ?>',
	                                        'create_bundle_search_list_view',
	                                        'search_div',
	                                        'bundle_receive_from_knitting_floor_controller',
	                                        'setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')"
	                             style="width:100px;" />
	                     </td>
	                </tr>
	           </table>
	           <div 
	                style="width:100%; 
	                        margin-top:5px; 
	                        margin-left:10px" 
	                id="search_div" 
	                align="left">
	            </div>
			</fieldset>
		</form>
	</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		if($("#hidden_lot_no").val()!="")
		{
			show_list_view (
							document.getElementById('txt_order_no').value+'_'+
							document.getElementById('cbo_company_name').value+'_'+
							document.getElementById('bundle_no').value+'_'+
							'<? echo trim($bundleNo,','); ?>'+'_'+
							document.getElementById('txt_job_no').value+'_'+
							document.getElementById('txt_lot_no').value+'_'+
							document.getElementById('cbo_lot_year').value+'_'+
							'<? echo trim($lot_ratio,','); ?>',
							'create_bundle_search_list_view',
							'search_div',
							'bundle_receive_from_knitting_floor_controller',
							'setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')
		}
	</script>
	</html>
	<?
}

if($action=="create_bundle_search_list_view")
{
 	$ex_data 				= explode("_",$data);
	$txt_order_no 			= "%".trim($ex_data[0])."%";
	$company 				= $ex_data[1];
	$selectedBuldle			="'".implode("','",explode(",",$ex_data[3]))."'";
	//echo $selectedBuldle;die;
	$job_no					=$ex_data[4];
	$lot_no					=$ex_data[5];
	$syear 					= substr($ex_data[6],2);
	$full_lot_no			=$ex_data[7];
	
	if(trim($ex_data[2]))	$bundle_no = "".trim($ex_data[2])."";
	else					$bundle_no = "%".trim($ex_data[2])."%";
	 
	
	if( trim($ex_data[5])=='')
	{
		echo "<h2 style='color:#D00; text-align:center;'>Please Select- Lot No</h2>";
		exit();
	}
	

	$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	
	
	
	if ($lot_no != '') 
	{
		$cutCon = " and a.cut_num_prefix_no=".$lot_no."";
    }
	if ($full_lot_no != '') 
	{
		$cutCon='';
		$cutCon = " and a.cutting_no='".$full_lot_no."'";
    }
	
	if($job_no!='') 
		$jobCon=" and f.job_no_prefix_num = $job_no";
	else 
		$jobCon="";
	if(str_replace("'","",$selectedBuldle)!=="")
		$selected_bundle_cond=" and c.bundle_no not in (".$selectedBuldle.")";

	$scanned_bundle_arr=return_library_array("
												SELECT 
													b.bundle_no, 
													b.bundle_no
													 
												from 
													pro_garments_production_mst a,
													pro_garments_production_dtls b
													
												where 
													a.id=b.mst_id and 
													b.production_type  in (50) and
													b.bodypart_type=$bodypart_type 
													$cutCon_a  and 
													b.status_active=1 and 
													b.is_deleted=0
											",
											'bundle_no',
											'bundle_no');
	foreach(explode(",",$selectedBuldle) as $bn)
	{
		$scanned_bundle_arr[$bn]=$bn;	
	}
	
	 

	
	?>
    <table 
    	cellspacing="0" 
        cellpadding="0" 
        border="1" 
        rules="all" 
        width="830" 
        class="rpt_table">
        
        <thead>
            <th width="40">SL</th>
            <th width="50">Year</th>
            <th width="50">Job No</th>
            <th width="90">Order No</th>
            <th width="130">Gmts Item</th>
            <th width="110">Country</th>
            <th width="100">Color</th>
            <th width="50">Size</th>
            <th width="70">Cut No</th>
            <th width="80">Bundle No</th>
            <th>Bundle Qty.</th>
        </thead>
	</table>
	<div 
    	style=" width:850px; 
        		max-height:210px; 
                overflow-y:scroll"
       	id="list_container_batch"
        align="left">
        	 
        <table 
        	cellspacing="0" 
            cellpadding="0" 
            border="1" 
            rules="all" 
            width="830" 
            class="rpt_table" 
            id="tbl_list_search">  
        	<?
			$i=1;
			$sql="select 
				   a.job_no ,
				   a.cutting_no ,
				   a.cut_num_prefix_no,
				   b.color_id ,
				   b.gmt_item_id ,
				   c.size_id ,
				   c.bundle_no,
				   c.order_id ,
				   c.country_id,  
				   e.po_number,
				   c.size_qty ,
				   c.barcode_no
				   
				 from
					ppl_cut_lay_mst a,
					ppl_cut_lay_dtls b,
					ppl_cut_lay_bundle c,
					wo_po_break_down e,
					wo_po_details_master f
					
				 where 
					a.entry_form=253 and
					a.company_id=$company and 
					a.id=b.mst_id and
					b.mst_id=c.mst_id and
					b.id=c.dtls_id and
					c.bundle_no like '$bundle_no' and
					c.order_id=e.id and
					e.po_number like '$txt_order_no' and
					e.job_no_mst=f.job_no and
					f.job_no=a.job_no 
					$cutCon $jobCon $selected_bundle_cond
					
				order by 
					a.job_no,
					a.cutting_no,
					length(c.bundle_no) asc,
					c.bundle_no asc
					 ";
			 //echo $sql;
			$result = sql_select($sql);	
			foreach ($result as $row)
			{  
				
				if($scanned_bundle_arr[$row[csf('bundle_no')]]=="")
				{
					if($i%2==0) 
						$bgcolor="#E9F3FF"; 
					else 
						$bgcolor="#FFFFFF";
						
					list($shortName,$year,$job)=explode('-',$row[csf('job_no')]);	
				?>
					<tr 
                    	bgcolor="<? echo $bgcolor; ?>" 
                        style="
                        	text-decoration:none; 
                            cursor:pointer"
                        id="search<? echo $i;?>" 
                        onClick="js_set_value(<? echo $i; ?>)"> 
                        
						<td width="40"><? echo $i; ?>
							 <input 
                             	type="hidden" 
                                name="txt_individual" 
                                id="txt_individual<?php echo $i; ?>" 
                                value="<?php echo $row[csf('barcode_no')]; ?>"/>
						</td>
						<td width="50" align="center"><p><? echo $year; ?></p></td>
						<td width="50" align="center"><p><? echo $job*1; ?></p></td>
						<td width="90"><p><? echo $row[csf('po_number')]; ?></p></td>
						<td width="130"><p><? echo $garments_item[$row[csf('gmt_item_id')]]; ?></p></td>
						<td width="110"><p><? echo $country_arr[$row[csf('country_id')]]; ?></p></td>
						<td width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
						<td width="50"><p><? echo $size_arr[$row[csf('size_id')]]; ?></p></td>
						<td width="70"><? echo $row[csf('cutting_no')]; ?></td>
						<td width="80"><? echo $row[csf('bundle_no')]; ?></td>
						<td align="right"><? echo $row[csf('size_qty')]; ?></td>
					</tr>
				<?
					$i++;
				}
			}
			
        	?>
            <input 
                type="hidden" 
                name="hidden_cutting_no"  
                value="<?php echo $row[csf('cutting_no')]; ?>" 
                id="hidden_cutting_no"  />
        </table>
    </div>
    <table width="830">
        <tr>
            <td align="center" >
               <span  
               		style="float:left;"> 
                    	<input 
                        type="checkbox" 
                        name="check_all" 
                        id="check_all" 
                        onClick="check_all_data()" />
                    Check / Uncheck All
               </span>
                <input 
                	type="button" 
                    name="close" 
                    class="formbutton" 
                    value="Close" 
                    id="main_close" 
                    onClick="fnc_close();" 
                    style="width:100px" />
            </td>
        </tr>
    </table>
	<?	
	exit();	
}




if($action=='populate_data_from_yarn_lot')
{
	
	$data_array=sql_select("
								select 
										id, 
										company_id, 
										source, 
										working_company_id ,
										location_id,
										cutting_no,
										floor_id,
										job_no ,
										size_set_no 
									  
								from 
										ppl_cut_lay_mst 
								where 
										cutting_no='$data'  and 
										entry_form=253 and 
										status_active=1 and 
										is_deleted=0
							");
	
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('txt_lot_ratio').value 				= '".$row[csf("cutting_no")]."';\n";
		echo "document.getElementById('txt_job_no').value 					= '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('txt_size_set_no').value 				= '".$row[csf("size_set_no")]."';\n";
		echo "document.getElementById('cbo_company_name').value 			= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_source').value 					= '".$row[csf("source")]."';\n";
		echo "load_drop_down( 'requires/bundle_receive_from_knitting_floor_controller', ".($row[csf("source")])."+'_'+".($row[csf("company_id")]).", 'load_drop_down_knitting_com','knitting_com');";
		
		echo "document.getElementById('cbo_working_company').value 			= '".$row[csf("working_company_id")]."';\n";
		
		echo "load_location();\n";
		echo "document.getElementById('cbo_working_location').value 					= '".$row[csf("location_id")]."';\n";
		echo "load_drop_down( 'requires/bundle_receive_from_knitting_floor_controller', ".$row[csf("location_id")].", 'load_drop_down_floor', 'floor_td' );";
		echo "document.getElementById('cbo_floor').value  = '".($row[csf("floor_id")])."';\n";
		echo "load_drop_down( 'requires/bundle_receive_from_knitting_floor_controller', '".$row[csf('company_id')]."', 'load_drop_down_lc_location', 'location_td' );\n";
		
		exit();
	}
}

if($action=='populate_data_from_yarn_lot_bundle')
{
	
	$data_array=sql_select("
								select 
										a.id, 
										a.company_id, 
										a.source, 
										a.working_company_id ,
										a.location_id,
										a.cutting_no,
										a.floor_id,
										a.job_no ,
										a.size_set_no 
									  
								from 
										ppl_cut_lay_mst a,
										ppl_cut_lay_bundle b
								where 
										a.id=b.mst_id and
										b.barcode_no='$data'  and 
										a.entry_form=253 and 
										a.status_active=1 and 
										a.is_deleted=0 and
										b.status_active=1 and 
										b.is_deleted=0
							");
	
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('txt_lot_ratio').value 				= '".$row[csf("cutting_no")]."';\n";
		echo "document.getElementById('txt_job_no').value 					= '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('txt_size_set_no').value 				= '".$row[csf("size_set_no")]."';\n";
		echo "document.getElementById('cbo_company_name').value 			= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_source').value 					= '".$row[csf("source")]."';\n";
		echo "load_drop_down( 'requires/bundle_receive_from_knitting_floor_controller', ".($row[csf("source")])."+'_'+".($row[csf("company_id")]).", 'load_drop_down_knitting_com','knitting_com');";
		
		echo "document.getElementById('cbo_working_company').value 			= '".$row[csf("working_company_id")]."';\n";
		
		echo "load_location();\n";
		echo "document.getElementById('cbo_working_location').value 					= '".$row[csf("location_id")]."';\n";
		echo "load_drop_down( 'requires/bundle_receive_from_knitting_floor_controller', ".$row[csf("location_id")].", 'load_drop_down_floor', 'floor_td' );";
		echo "document.getElementById('cbo_floor').value  = '".($row[csf("floor_id")])."';\n";
		echo "load_drop_down( 'requires/bundle_receive_from_knitting_floor_controller', '".$row[csf('company_id')]."', 'load_drop_down_lc_location', 'location_td' );\n";
		
		exit();
	}
}






if($action=="populate_bundle_data")
{

	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
	$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name" );
	$data=explode("**",$data);
	$i=$data[1]+1;
	//echo $i;die;
	$sql_cut=sql_select(" 
							select 
								a.job_no,
								a.size_set_no,
								b.color_id,
								b.roll_data,
								a.id,
								b.id as dtls_id
							from 
								ppl_cut_lay_mst a,
								ppl_cut_lay_dtls b
							where
								a.cutting_no='".$data[4]."' and
								a.id=b.mst_id and 
								a.status_active=1 and 
								a.is_deleted=0
						");
	$job_no				=$sql_cut[0][csf("job_no")];
	$color_id			=$sql_cut[0][csf("color_id")];
	$consumption_string	=$sql_cut[0][csf("roll_data")];
	$mst_id				=$sql_cut[0][csf("id")];
	$dtls_id			=$sql_cut[0][csf("dtls_id")];
	$size_set_no        =$sql_cut[0][csf("size_set_no")];
	list($shortName,$year,$job_prifix)=explode('-',$job_no);
	//echo $job_no;die;
	$consumption_data_arr=explode("**",$consumption_string);
	$color_wise_cons_arr=array();
	foreach($consumption_data_arr as $single_color_consumption)
	{
		$single_color_cons_arr=explode("=",$single_color_consumption);
		$color_wise_cons_arr[$single_color_cons_arr[1]]=$single_color_cons_arr[3];
	}
	
	$job_sql=sql_select("
						select 
							c.short_name as BUYER_NAME,
							a.po_number as PO_NUMBER,
							a.id as ID
						 from wo_po_break_down  a,wo_po_details_master b ,lib_buyer c
						 
						 where
						 b.job_no='".$job_no."' and  
						 a.job_no_mst=b.job_no   and
						 b.buyer_name=c.id 
					");
	$jbp_arr=array();
	foreach($job_sql as $jval)
	{
		$jbp_arr["buyer_name"]=$jval["BUYER_NAME"];
		$jbp_arr[$jval["ID"]]=$jval["PO_NUMBER"];
	}
	

	$data_array_strip=sql_select("
									select 
                                          a.sample_color_id as sample_color,
                                          a.yarn_color_id as stripe_color,
                                          a.sample_color_percentage, 
                                          a.production_color_percentage,
                                          a.actual_consumption,
										  b.sample_ref
                                        from 
                                            ppl_size_set_consumption a,
                                            ppl_size_set_mst b
                                        where
                                            b.job_no='".$job_no."' and
                                            b.sizeset_no='".$size_set_no."' and
                                            b.id=a.mst_id and 
                                            a.color_id=$color_id and 
                                            a.status_active=1 and 
                                            a.is_deleted=0  and
											b.status_active=1 and 
                                            b.is_deleted=0 
										order by sample_color_id
								");

	$sample_reference	=$data_array_strip[0][csf("sample_ref")];
	$colspan=count($data_array_strip);
	$table_width	=1300+$colspan*100;
	$div_width		=$table_width+20;

	if($data[2]==1)		$bodypart_cond="b.body_part_id in (1,2,3,4,5)";
	else				$bodypart_cond="b.body_part_id not in (1,2,3,4,5)";
	
	$sql_wet_sheet="select
				b.color_id,
				sum(b.bodycolor) as  bodycolor,
				sum(CASE WHEN $bodypart_cond THEN b.bodycolor ELSE 0 END) as  bodycolor_aspect
			from 
				sample_development_mst a,
				sample_development_rf_color b 
			where 
				a.id=b.mst_id and 
				a.requisition_number='".$sample_reference."' and 
				b.bodycolor>0
			group by 
				b.color_id";
	$wet_sheet_result=sql_select($sql_wet_sheet);
	$color_percentage_bodypart=array();
	foreach($wet_sheet_result as $wet_row)
	{
		$color_percentage_bodypart[$wet_row[csf('color_id')]]=$wet_row[csf('bodycolor_aspect')]/$wet_row[csf('bodycolor')];		
	}



	$total_production_qnty=0;
	$sqlResult =sql_select("
							select  
								b.* ,
								a.gmt_item_id								
							from 
								ppl_cut_lay_dtls a,
								ppl_cut_lay_bundle b 
							where 
								b.mst_id=$mst_id and 
								b.dtls_id=$dtls_id and  
								a.id=b.dtls_id and
								a.color_id=$color_id  and
								b.barcode_no in (".$data[0].") and
								a.status_active=1 and 
								a.is_deleted=0 and 
								b.status_active=1 and 
								b.is_deleted=0");
								
	foreach($sqlResult as $selectResult)
	{
		if ($i%2==0)  $bgcolor="#E9F3FF";
		else $bgcolor="#FFFFFF";
		$total_production_qnty+=$selectResult[csf('size_qty ')]; 	
	?>
		<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="" > 
			<td width="30" align="center"><? echo $i; ?></td>
			<td width="120" align="center" title="<? echo $selectResult[csf('barcode_no')]; ?>"><p><? echo $selectResult[csf('bundle_no')]; ?></p></td>
			<td width="100" align="center">
				<input
					type="text" 
					onblur="checkMachineId(<? echo $i; ?>)"
					id="txt_machine_no_<? echo $i; ?>" 
					name="txt_machine_no[]"
					style="width:80px;"
					class="text_boxes">
					
				<input
					type="hidden" 
					id="txt_color_id_<? echo $i; ?>" 
					name="txt_color_id[]"
					style="width:80px;"
					value="<?php echo $color_id; ?>">
					
				<input
					type="hidden" 
					id="txt_size_id_<? echo $i; ?>" 
					name="txt_size_id[]"
					style="width:80px;"
					value="<?php echo $selectResult[csf('size_id')]; ?>">
					
				<input
					type="hidden" 
					id="txt_order_id_<? echo $i; ?>" 
					name="txt_order_id[]"
					style="width:80px;"
					class="text_boxes"
					value="<?php echo $selectResult[csf('order_id')]; ?>">
					
				<input
					type="hidden" 
					id="txt_gmt_item_id_<? echo $i; ?>" 
					name="txt_gmt_item_id[]"
					style="width:80px;"
					class="text_boxes"
					value="<?php echo $selectResult[csf('gmt_item_id')]; ?>">
					
				<input
					type="hidden" 
					id="txt_country_id_<? echo $i; ?>" 
					name="txt_country_id[]"
					style="width:80px;"
					class="text_boxes"
					value="<?php echo $selectResult[csf('country_id')]; ?>">
             
             	<input
                    type="hidden" 
                    id="txt_barcode_<? echo $i; ?>" 
                    name="txt_barcode[]"
                    style="width:80px;"
                    class="text_boxes"
                    value="<?php echo $selectResult[csf('barcode_no')]; ?>"> 

                <input
                    type="hidden" 
                    id="txt_colorsize_id_<? echo $i; ?>" 
                    name="txt_colorsize_id[]"
                    style="width:80px;"
                    class="text_boxes"
                    value=""> 
                <input
                        type="hidden" 
                        id="txt_dtls_id_<? echo $i; ?>" 
                        name="txt_dtls_id[]"
                        style="width:80px;"
                        class="text_boxes"
                        value="">

                <input
                    type="hidden" 
                    id="txt_machine_id_<? echo $i; ?>" 
                    name="txt_machine_id[]"
                    value="">

                <input
                    type="hidden" 
                    id="trId_<? echo $i; ?>" 
                    name="trId[]"
                    value="<?php echo $i; ?>">                           
					
			</td>
			<td width="150" align="center" style="word-break:break-all"><p><?php  echo $color_library[$color_id]; ?></p></td>
			<td width="70" align="center"><?php  echo $size_library[$selectResult[csf('size_id')]]; ?></td>
					
			<td width="65" align="center"><p><?php echo $selectResult[csf('size_qty')]; ?></p></td>
			<td width="50" align="center"><p><? echo $selectResult[csf('number_start')]; ?></p></td>
			<td width="50" align="center"><p><?php echo $selectResult[csf('number_end')]; ?></p></td>
  
			<?php
				$total_consumption=0;
				foreach($data_array_strip as $scolor)
				{
					$total_consumption+=$scolor[csf("actual_consumption")]*$color_percentage_bodypart[$scolor[csf("sample_color")]];
					$grand_color_cons_arr[$scolor[csf("sample_color")]]+=($scolor[csf("actual_consumption")]*$selectResult[csf('size_qty')]*$color_percentage_bodypart[$scolor[csf("sample_color")]]*2.2046226)/12;
					
					?>
					<td 
                    	width="100" 
                    	style="word-break:break-all" 
                        align="right"
                        id="bdl_<?php echo $i."_".$scolor[csf("stripe_color")];?>"><?php echo number_format(($scolor[csf("actual_consumption")]*$selectResult[csf('size_qty')]*$color_percentage_bodypart[$scolor[csf("sample_color")]]*2.2046226)/12,4,".","");?></td>
					<?php
				}
				//print_r($grand_color_cons_arr);die;
				$grand_total_consumption+=($total_consumption*$selectResult[csf('size_qty')]*2.2046226)/12;
				$total_size_qty+=$selectResult[csf('size_qty')];
			?>
			<td width="100" align="right"><?php echo number_format(($total_consumption*$selectResult[csf('size_qty')]*2.2046226)/12,4,".","");?></td>
			<td width="50" align="center"><p><? echo $year; ?></p></td>
			<td width="60" align="center"><p><? echo $job_prifix*1; ?></p></td>
			<td width="65" align="center"><?php echo $jbp_arr["buyer_name"]; ?></td>
			<td width="90" align="center"><?php  echo $jbp_arr[$selectResult[csf('order_id')]]; ?></td>
					
			<td width="120" align="center"><p><?php echo $garments_item[$selectResult[csf('gmt_item_id')]]; ?></p></td>
			<td width="100" align="center"><p><? echo $country_library[$selectResult[csf('country_id')]]; ?></p></td>
			 <td>
				<input 
					type="button" 
					value="-" 
					name="minusButton[]" 
					id="minusButton_<? echo $i;  ?>" 
					style="width:30px" 
					class="formbuttonplasminus" 
					onClick="fnc_minusRow('<? echo $i;  ?>')"/>
			</td>
		</tr>
	<?php
		$i++;
	}

	exit();
}








if($action=="show_dtls_listview_bundle")
{

	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
	$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name" );
	$data=explode("_", $data);
	//echo $data[1];die;
	$sql_cut=sql_select(" 
							select 
								a.job_no,
								a.size_set_no,
								b.color_id,
								b.roll_data,
								a.id,
								b.id as dtls_id
							from 
								ppl_cut_lay_mst a,
								ppl_cut_lay_dtls b,
								ppl_cut_lay_bundle c
							where
								c.barcode_no='".$data[0]."' and
								a.id=b.mst_id and 
								b.id=c.dtls_id and
								a.id=c.mst_id and
								a.status_active=1 and 
								a.is_deleted=0 and
								b.status_active=1 and 
								b.is_deleted=0 and
								c.status_active=1 and 
								c.is_deleted=0
						");
	$job_no				=$sql_cut[0][csf("job_no")];
	$color_id			=$sql_cut[0][csf("color_id")];
	$consumption_string	=$sql_cut[0][csf("roll_data")];
	$mst_id				=$sql_cut[0][csf("id")];
	$dtls_id			=$sql_cut[0][csf("dtls_id")];
	$size_set_no        =$sql_cut[0][csf("size_set_no")];
	list($shortName,$year,$job_prifix)=explode('-',$job_no);
	//echo $consumption_string;die;
	$consumption_data_arr=explode("**",$consumption_string);
	$color_wise_cons_arr=array();
	foreach($consumption_data_arr as $single_color_consumption)
	{
		$single_color_cons_arr=explode("=",$single_color_consumption);
		$color_wise_cons_arr[$single_color_cons_arr[1]]=$single_color_cons_arr[3];
	}
	
	$job_sql=sql_select("
						select 
							c.short_name as BUYER_NAME,
							a.po_number as PO_NUMBER,
							a.id as ID
						 from wo_po_break_down  a,wo_po_details_master b ,lib_buyer c
						 
						 where
						 b.job_no='".$job_no."' and  
						 a.job_no_mst=b.job_no   and
						 b.buyer_name=c.id 
					");
	$jbp_arr=array();
	foreach($job_sql as $jval)
	{
		$jbp_arr["buyer_name"]=$jval["BUYER_NAME"];
		$jbp_arr[$jval["ID"]]=$jval["PO_NUMBER"];
	}
	
	
	$data_array_strip=sql_select("
									select 
                                          a.sample_color_id as sample_color,
                                          a.yarn_color_id as stripe_color,
                                          a.sample_color_percentage, 
                                          a.production_color_percentage,
                                          a.actual_consumption,
										  b.sample_ref
                                        from 
                                            ppl_size_set_consumption a,
                                            ppl_size_set_mst b
                                        where
                                            b.job_no='".$job_no."' and
                                            b.sizeset_no='".$size_set_no."' and
                                            b.id=a.mst_id and 
                                            a.color_id=$color_id and 
                                            a.status_active=1 and 
                                            a.is_deleted=0  and
											b.status_active=1 and 
                                            b.is_deleted=0 
										order by sample_color_id
								");
								
	$sample_reference	=$data_array_strip[0][csf("sample_ref")];
	$colspan=count($data_array_strip);
	$table_width	=1300+$colspan*100;
	$div_width		=$table_width+20;
	if($data[1]==1)		$bodypart_cond="b.body_part_id in (1,2,3,4,5)";
	else				$bodypart_cond="b.body_part_id not in (1,2,3,4,5)";
	
	$sql_wet_sheet="select
				b.color_id,
				sum(b.bodycolor) as  bodycolor,
				sum(CASE WHEN $bodypart_cond THEN b.bodycolor ELSE 0 END) as  bodycolor_aspect
			from 
				sample_development_mst a,
				sample_development_rf_color b 
			where 
				a.id=b.mst_id and 
				a.requisition_number='".$sample_reference."' and 
				b.bodycolor>0
			group by 
				b.color_id";
	$wet_sheet_result=sql_select($sql_wet_sheet);
	$color_percentage_bodypart=array();
	foreach($wet_sheet_result as $wet_row)
	{
		$color_percentage_bodypart[$wet_row[csf('color_id')]]=$wet_row[csf('bodycolor_aspect')]/$wet_row[csf('bodycolor')];
	}
	
	?>	
   
        <table 
            cellpadding="0" 
            width="<?php echo $div_width;?>" 
            cellspacing="0" 
            border="1" 
            class="rpt_table" 
            rules="all">
            
            <thead>
            	<tr>
                    <th width="30"  rowspan="3">SL</th>
                    <th width="120" rowspan="3">Bundle No</th>
                    <th width="100" rowspan="3">MC No</th>
                    <th width="150" rowspan="3"> G. Color</th>
                    <th width="70"  rowspan="3">Size</th>
                    <th width="65"  rowspan="3">Bundle Qty.(Pcs)</th>
                    <th width="100"	colspan="2">RMG No.</th>
                    <th width="<?php echo $colspan*100; ?>" 
                    	colspan="<?php echo $colspan; ?>">Yarn Color Wise Cons Qty. (Lbs)</th>
                    <th width="100"  rowspan="3">Bndl. Cons. Qty.(Lbs)</th>
                    <th width="50"  rowspan="3">Year</th>
                    <th width="60"  rowspan="3">Job No</th>
                    <th width="65"  rowspan="3">Buyer</th>
                    <th width="90"  rowspan="3">Order No</th>
                    <th width="120" rowspan="3">Gmts. Item</th>
                    <th width="100" rowspan="3">Country</th>
                    <th 			rowspan="3"></th>
                </tr>
                <tr>
                	<th width="50"  rowspan="2">From</th>
                	<th width="50" rowspan="2">To</th>
                    <?php
						foreach($data_array_strip as $scolor)
						{
							?>
							<th width="100" ><?php echo $color_library[$scolor[csf("sample_color")]];?> </th>
							<?php
						}
					?>
                    
                </tr>
                <tr>
                	
                    <?php
						foreach($data_array_strip as $scolor)
						{
							?>
							<th width="100" style="word-break:break-all"><?php echo $color_library[$scolor[csf("stripe_color")]];?></th>
							<?php
						}
					?>
                </tr>
            </thead>
        </table>		
		
	<div 
        style="width:<?php echo $div_width;?>px;max-height:250px;overflow-y:scroll" 
        align="left"> 
           
        <table 
            cellpadding="0" 
            width="<?php echo $table_width;?>" 
            cellspacing="0" 
            border="1" 
            class="rpt_table" 
            rules="all" 
            id="tbl_details"> 
            <tbody>
		<?php  
			$i=1;	
			$total_production_qnty=0;
			$sqlResult =sql_select("
									select  
										b.* ,
										a.gmt_item_id
										
									from 
										ppl_cut_lay_dtls a,
										ppl_cut_lay_bundle b 
									where 
										b.mst_id=$mst_id and 
										b.dtls_id=$dtls_id and  
										a.id=b.dtls_id and
										a.color_id=$color_id  and
										b.barcode_no in (".$data[0].") and
										a.status_active=1 and 
										a.is_deleted=0 and 
										b.status_active=1 and 
										b.is_deleted=0");
						
			foreach($sqlResult as $selectResult)
			{
				if ($i%2==0)  $bgcolor="#E9F3FF";
                else $bgcolor="#FFFFFF";
               	$total_production_qnty+=$selectResult[csf('size_qty ')]; 	
 			?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="" > 
                    <td width="30" align="center"><? echo $i; ?></td>
                    <td width="120" align="center" title="<? echo $selectResult[csf('barcode_no')]; ?>"><p><? echo $selectResult[csf('bundle_no')]; ?></p></td>
                    <td width="100" align="center">
                    	<input
                        	type="text"
                        	onblur="checkMachineId(<? echo $i; ?>)" 
                            id="txt_machine_no_<? echo $i; ?>" 
                            name="txt_machine_no[]"
                            style="width:80px;"
                            class="text_boxes">
                            
                       	<input
                        	type="hidden" 
                            id="txt_color_id_<? echo $i; ?>" 
                            name="txt_color_id[]"
                            style="width:80px;"
                            value="<?php echo $color_id; ?>">
                            
                        <input
                            type="hidden" 
                            id="txt_size_id_<? echo $i; ?>" 
                            name="txt_size_id[]"
                            style="width:80px;"
                            value="<?php echo $selectResult[csf('size_id')]; ?>">
                            
						<input
                        	type="hidden" 
                            id="txt_order_id_<? echo $i; ?>" 
                            name="txt_order_id[]"
                            style="width:80px;"
                            class="text_boxes"
                            value="<?php echo $selectResult[csf('order_id')]; ?>">
                            
                       	<input
                        	type="hidden" 
                            id="txt_gmt_item_id_<? echo $i; ?>" 
                            name="txt_gmt_item_id[]"
                            style="width:80px;"
                            class="text_boxes"
                            value="<?php echo $selectResult[csf('gmt_item_id')]; ?>">
                            
                        <input
                            type="hidden" 
                            id="txt_country_id_<? echo $i; ?>" 
                            name="txt_country_id[]"
                            style="width:80px;"
                            class="text_boxes"
                            value="<?php echo $selectResult[csf('country_id')]; ?>">
                     
                     	<input
                            type="hidden" 
                            id="txt_barcode_<? echo $i; ?>" 
                            name="txt_barcode[]"
                            style="width:80px;"
                            class="text_boxes"
                            value="<?php echo $selectResult[csf('barcode_no')]; ?>"> 
                        <input
		                    type="hidden" 
		                    id="trId_<? echo $i; ?>" 
		                    name="trId[]"
		                    value="<?php echo $i; ?>">                         
                            
                    </td>
                    <td width="150" align="center" style="word-break:break-all"><p><?php  echo $color_library[$color_id]; ?></p></td>
                    <td width="70" align="center"><?php  echo $size_library[$selectResult[csf('size_id')]]; ?></td>
                    		
                    <td width="65" align="center"><p><?php echo $selectResult[csf('size_qty')]; ?></p></td>
                    <td width="50" align="center"><p><? echo $selectResult[csf('number_start')]; ?></p></td>
                    <td width="50" align="center"><p><?php echo $selectResult[csf('number_end')]; ?></p></td>
          
                    <?php
						
						$total_consumption=0;
						
						foreach($data_array_strip as $scolor)
						{
							$total_consumption+=$scolor[csf("actual_consumption")]*$color_percentage_bodypart[$scolor[csf("sample_color")]];
							$grand_color_cons_arr[$scolor[csf("sample_color")]]+=($scolor[csf("actual_consumption")]*$selectResult[csf('size_qty')]*$color_percentage_bodypart[$scolor[csf("sample_color")]]*2.2046226)/12;
							
							?>
							<td 
                            	width="100" 
                            	style="word-break:break-all" 
                                align="right"
                                id="bdl_<?php echo $i."_".$scolor[csf("stripe_color")];?>"><?php echo number_format(($scolor[csf("actual_consumption")]*$selectResult[csf('size_qty')]*$color_percentage_bodypart[$scolor[csf("sample_color")]]*2.2046226)/12,4,".","");?></td>
							<?php
						}
						//print_r($grand_color_cons_arr);die;
						$grand_total_consumption+=($total_consumption*$selectResult[csf('size_qty')]*2.2046226)/12;
						$total_size_qty+=$selectResult[csf('size_qty')];
					?>
                    <td width="100" align="right"><?php echo number_format(($total_consumption*$selectResult[csf('size_qty')]*2.2046226)/12,4,".","");?></td>
                    <td width="50" align="center"><p><? echo $year; ?></p></td>
                    <td width="60" align="center"><p><? echo $job_prifix*1; ?></p></td>
                    <td width="65" align="center"><?php echo $jbp_arr["buyer_name"]; ?></td>
                    <td width="90" align="center"><?php  echo $jbp_arr[$selectResult[csf('order_id')]]; ?></td>
                    		
                    <td width="120" align="center"><p><?php echo $garments_item[$selectResult[csf('gmt_item_id')]]; ?></p></td>
                    <td width="100" align="center"><p><? echo $country_library[$selectResult[csf('country_id')]]; ?></p></td>
                     <td>
                     	<input 
                        	type="button" 
                            value="-" 
                            name="minusButton[]" 
                            id="minusButton_<? echo $i;  ?>" 
                            style="width:30px" 
                            class="formbuttonplasminus" 
                            onClick="fnc_minusRow('<? echo $i;  ?>')"/>
                	</td>
                </tr>
            <?php
                $i++;
			}
			?>
            </tbody>
            <tfoot>
            	<tr>
                    <th   colspan="5" > Total</th>
                    <th width="65"  ><?php echo $total_size_qty; ?></th>
                    <th width="50"  ></th>       
                    <th width="50"  ></th>
                     <?php
					 	$strip_color_arr=array();
						foreach($data_array_strip as $scolor)
						{
							$strip_color_arr[$scolor[csf("stripe_color")]]=$scolor[csf("stripe_color")];
							?>
							<th 
                            	width="100" 
                                style="word-break:break-all"
                                id="ttl_<?php echo $scolor[csf("stripe_color")];?>"><?php echo number_format($grand_color_cons_arr[$scolor[csf("sample_color")]],4,".","");?></th>
							<?php
						}
					?>
                    <th width="100" id="total_color_cons"><?php echo number_format($grand_total_consumption,4,".","");?></th>
                    <th width="50"  ></th>
                    <th width="60"  ></th>
                    <th width="65"  ></th>
                    <th width="90"  ></th>
                    <th width="120" ></th>
                    <th width="100" ></th>
                    <th id="">
                    	<input
                        	type="hidden" 
                            id="color_id_string" 
                            name="color_id_string"
                            value="<?php echo implode(",",$strip_color_arr);?>">
                    </th>
                </tr>
            </tfoot>
		</table>
	</div>
	<?
	exit();
}


if ($action=="operator_popup")
{
	echo load_html_head_contents("Operator Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	
?> 

	<script>
	
		function js_set_value(str)
		{
			$("#hidden_emp_number").val(str);
			parent.emailwindow.hide(); 
		}
	
    </script>

</head>

<body>
<div align="center" style="width:1020px;">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:1020px;">
		<legend>Enter search words</legend>           
            <table cellpadding="0" cellspacing="0" width="700" border="1" rules="all" class="rpt_table" align="center">
                <thead>
	                <th width="160" align="center">Company</th>
	                <th width="135" align="center">Location</th>
	                <th width="135" align="center">Division</th>
	                <th width="135" align="center">Department</th>
	                <th width="135" align="center">Section</th>
	            	<th width="135" align="center">Employee Code</th>
	                <th width="90" align="center"><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /> <input type="hidden" id="hidden_emp_number"  /></th>           
	            </thead>
                <tr class="general">
                    <td align="center">
                    	<?
                    		$sql_com="select 
											id,
											company_name
										from 
											lib_company comp
						 				where 
											status_active =1 and 
											is_deleted=0 											 
										order by company_name";
							
								
							echo create_drop_down( "cbo_company_name",
													160, 
													$sql_com,
													"id,company_name", 
													1, 
													"--- Select Company ---", 
													$selected, 
													"load_drop_down( 	 
																	'bundle_receive_from_knitting_floor_controller', this.value, 
																	'load_drop_down_location_hrm', 
																	'location_td_hrm');",
													"",
													"",
													"",
													"",
													"",
													"",
													$new_conn );  
						?>       
                    </td>
                    <td id="location_td_hrm">
					 <? 
						echo create_drop_down( "cbo_location_name", 135, $blank_array,"", 1, "-- Select Location --", $selected );
                    ?>
	                </td>
	                 <td id="division_td_hrm">
						 <? 
	                    	echo create_drop_down( "cbo_division_name", 135,$blank_array ,"", 1, "-- Select Division --", $selected );
	                    ?>
	                </td> 
	                <td id="department_td_hrm">
						<? 
							echo create_drop_down( "cbo_dept_name", 135,$blank_array ,"", 1, "-- Select Department --", $selected );
	                    ?>
	                </td>   
	                <td id="section_td_hrm">
						<? 
							echo create_drop_down( "cbo_section_name", 135,$blank_array ,"", 1, "-- Select Section --", $selected );
	                    ?>
	                </td>
	           
	                <td>
						<input type="text" id="src_emp_code" name="src_emp_code" class="text_boxes" style="width:135px;" >
	                </td> 
	                <td>
	                	<input type="button" 
	                	name="btn_show" 
	                	class="formbutton" 
	                	value="Show" 
	                	onClick="show_list_view ( 
	                								document.getElementById('cbo_company_name').value+'_'+
	                								document.getElementById('cbo_location_name').value+'_'+
	                								document.getElementById('cbo_division_name').value+'_'+
	                								document.getElementById('cbo_dept_name').value+'_'+
	                								document.getElementById('cbo_section_name').value+'_'+
	                								document.getElementById('src_emp_code').value, 'create_emp_search_list_view', 
	                								'search_div', 
	                								'bundle_receive_from_knitting_floor_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
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
}

if($action=="create_emp_search_list_view")
{
	$ex_data = explode("_",$data);
	$company = $ex_data[0];
	$location = $ex_data[1];
	$division = $ex_data[2];
	$department = $ex_data[3];
	$section = $ex_data[4];
	$emp_code = $ex_data[6];


 	//$sql_cond="";
	if( $company!=0 )  $company=" and company_id=$company"; else  $company="";
	if( $location!=0 )  $location=" and location_id=$location"; else  $location="";
	if( $division!=0 )  $division=" and division_id=$division"; else  $division="";
	if( $department!=0 )  $department=" and department_id=$department"; else  $department="";
	if( $section!=0 )  $section=" and section_id=$section"; else  $section="";
	if( $emp_code!=0 )  $emp_code=" and emp_code=$emp_code"; else  $emp_code="";
	

	
	if($db_type==2 || $db_type==1 )
	{
      $sql = "select emp_code,id_card_no,(first_name||' '||middle_name|| '  ' || last_name) as emp_name,designation_id, company_id, location_id, division_id,department_id,section_id from hrm_employee where status_active=1 and is_deleted=0 $company $location $division $department $section $line_no $emp_code";
    }
	if($db_type==0)
	{
	  $sql = "select emp_code,id_card_no, concat(first_name,'  ',middle_name,last_name) as emp_name, designation_id, company_id, location_id, division_id,department_id,section_id from hrm_employee where status_active=1 and is_deleted=0 $company $location $division $department $section $line_no $emp_code";
		
	}

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name',$new_conn);
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name',$new_conn);
	$division_arr=return_library_array( "select id, division_name from lib_division",'id','division_name',$new_conn);
	$department_arr=return_library_array( "select id, department_name from lib_department",'id','department_name',$new_conn);
	$section_arr=return_library_array( "select id, section_name from lib_section",'id','section_name',$new_conn);
	$designation_arr=return_library_array( "select id, custom_designation from lib_designation",'id','custom_designation',$new_conn);
	

	$arr=array(2=>$designation_arr,3=>$line_no_arr,3=>$company_arr,4=>$location_arr,5=>$division_arr,6=>$department_arr,7=>$section_arr);

		
	echo  create_list_view(
							"list_view", 
							"Emp Code,ID Card,Employee Name,Designation,Company,Location,Division,Department,Section", 
							"80,140,120,110,110,110,110,110,80",
							"1040",
							"260",
							0, 
							$sql, 
							"js_set_value", 
							"emp_code,id_card_no,emp_name", 
							"", 
							1, 
							"0,0,0,designation_id,company_id,location_id,division_id,department_id,section_id", 
							$arr , 
							"emp_code,id_card_no,emp_name,designation_id,company_id,location_id,division_id,department_id,section_id", 
							"employee_info_controller",
							'setFilterGrid("list_view",-1);',
							'0,0,0,0,0,0,0,0',
							"",
							"",
							$new_conn) ;
	exit();
}



if ($action=="load_drop_down_location_hrm")
{

   echo create_drop_down( 
   						"cbo_location_name", 
   						135, 
   						"select id,location_name from lib_location where company_id=$data and status_active=1 and is_deleted=0",
   						"id,location_name", 
   						1, 
   						"-- Select Location --", 
   						$selected,
   						"load_drop_down( 
										'bundle_receive_from_knitting_floor_controller', this.value,
										'load_drop_down_division', 
										'division_td_hrm');",
   						"",
						"",
						"",
						"",
						"",
						"",
						$new_conn );
}


if ($action=="load_drop_down_division")
{
	//echo "select id,division_name from lib_division where company_id=$data and status_active=1 and is_deleted=0";die;
   echo create_drop_down( 
   						"cbo_division_name", 
   						135, 
   						"select id,division_name from lib_division where location_id=$data and status_active=1 and is_deleted=0",
   						"id,division_name", 
   						1, 
   						"-- Select Division --", 
   						$selected,
   						"load_drop_down( 
   										'bundle_receive_from_knitting_floor_controller',
   										 this.value, 
   										'load_drop_down_department', 
   										'department_td_hrm');",
   						"",
						"",
						"",
						"",
						"",
						"",
						$new_conn );
}

if ($action=="load_drop_down_department")
{
   echo create_drop_down( 
   						"cbo_dept_name", 
   						135, 
   						"select id,department_name from lib_department where division_id=$data and status_active=1 and is_deleted=0","id,department_name",
   						1, 
   						"-- Select Department --", 
   						$selected,
   						"load_drop_down( 'bundle_receive_from_knitting_floor_controller',this.value, 'load_drop_down_section', 'section_td_hrm');",
   						"",
						"",
						"",
						"",
						"",
						"",
						$new_conn );
}

if ($action=="load_drop_down_section")
{
   echo create_drop_down( 
   						"cbo_section_name", 
   						135, 
   						"select id,section_name from lib_section where department_id=$data and status_active=1 and is_deleted=0",
   						"id,section_name", 
   						1, 
   						"-- Select Section --", 
   						$selected,
   						"",
   						"",
						"",
						"",
						"",
						"",
						"",
						$new_conn );
}


if ($action == "emblishment_issue_print") {
    extract($_REQUEST);
    $data = explode('*', $data);
    //print_r ($data);
    $company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
    $floor_library = return_library_array("select id, floor_name from  lib_prod_floor", "id", "floor_name");
    $color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");
    $buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
    $order_sql = "SELECT 
    					a.job_no,
    					a.buyer_name,
    					a.style_ref_no 
    				from  
    					wo_po_details_master a 
    				where a.job_no='".$data[3]."' and 
    				a.status_active=1 and 
    				a.is_deleted=0";

    $order_sql_result = sql_select($order_sql);
    foreach ($order_sql_result as $row) {
        $buyer_name = $buyer_arr[$row[csf('buyer_name')]];
        $style_ref = $row[csf('style_ref_no')];
    }

    $sql = "select
   				sys_number,
    			location_id, 
   				floor_id,
   				delivery_date, 
   				issue_challan_id, 
   				challan_no,
   				size_set_no, 
  				body_part, 
  				working_company_id, 
   				working_location_id, 
   				operator_id, 
   				body_part_ids
			from pro_gmts_delivery_mst 
			where 
				production_type=51 and 
				id='$data[1]' and 
    			status_active=1 and 
    			is_deleted=0 ";
    $dataArray = sql_select($sql);
    $new_conn=integration_params(2);
 	$employee_arr=return_library_array( "select id_card_no, (first_name||' '||middle_name|| '  ' || last_name) as emp_name from hrm_employee where id_card_no='".$dataArray[0][csf('sys_number')]."' and  status_active=1 and is_deleted=0",'id_card_no','emp_name',$new_conn);
    $image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");

    $sql_yarn_issue="select
   						gmts_color, 
   						yarn_color, 
   						sample_color, 
 						required_qty, 
  						returanable_qty, 
  						issue_qty, 
   						issue_balance_qty,
   						receive_qty, 
   						wastage
					from 
						pro_gmts_knitting_issue_dtls
					where 
						delivery_mst_id=$data[1] and 
						production_type=51 and 
						status_active=1 and 
						is_deleted=0";
 //echo $sql_yarn_issue;
	$sql_yarn_issue_result=sql_select($sql_yarn_issue);
	$garments_color=$sql_yarn_issue_result[0][csf('gmts_color')];
	$size_set_no=$dataArray[0][csf('size_set_no')];
	//echo "a.job_no='".$data[3]."' and  a.id=b.mst_id  and b.color_id=$garments_color and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";die;
  	//$size_set_no=return_field_value("a.sizeset_no as sizeset_no","ppl_size_set_mst a, ppl_size_set_dtls b","a.job_no='".$data[3]."' and  a.id=b.mst_id  and b.color_id=$garments_color and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","sizeset_no");


    ?>
    <div style="width:930px;">
        <table cellspacing="0" style="font: 12px tahoma; width: 100%;">
            <tr>
            	<td  align="left"  colspan="2" rowspan="3"><img src="../../../<? echo $image_location; ?>" height="70" width="150"></td>
                <td colspan="4" align="center" style="font-size:24px">
                    <strong><? echo $company_library[$data[0]]; ?></strong></td>
                <td  colspan="2"  rowspan="3" align="right" style="vertical-align:top;">
                	<div style="height:13px; width:15px;" id="qrcode"></div> 


                </td>
            </tr>
            <tr class="form_caption">
                <td colspan="4" align="center" style="font-size:14px">
                    <?

                    $nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0] and status_active=1 and is_deleted=0");
                    foreach ($nameArray as $result) {
                        ?>
                        Plot No: <? echo $result[csf('plot_no')]; ?>
                        Level No: <? echo $result[csf('level_no')] ?>
                        Road No: <? echo $result[csf('road_no')]; ?>
                        Block No: <? echo $result[csf('block_no')]; ?>
                        City No: <? echo $result[csf('city')]."<br/>"; ?>
                        Zip Code: <? echo $result[csf('zip_code')]; ?>
                        Province No: <?php echo $result[csf('province')]; ?>
                        Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
                        Email Address: <? echo $result[csf('email')]; ?>
                        Website No: <? echo $result[csf('website')];

                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td colspan="4" align="center" style="font-size:20px"><u><strong>Issue Card - Knitting</strong></u></td>
            </tr>
            
            <tr>
                <td width="80"><strong>Issue Id</strong></td>
                <td width="150"><? echo ": ".$dataArray[0][csf('sys_number')]; ?></td>
                <td width="70"><strong>Issue Date</strong></td>
                <td width="130"><? echo ": ".change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
                <td width="80"><strong>Machine No</strong></td>
                <td  colspan="3">:
                    <?
             

                    ?>
                </td>
                <td width="80"><strong>Size Set No</strong></td>
                <td width="130">
                    <?
                    	 echo ": ".$size_set_no;
                    ?>
                </td>
            </tr>
            <tr>
                <td><strong>Op Card No </strong></td>
                <td><? echo ": ".$dataArray[0][csf('operator_id')]; ?></td>
                <td><strong>Style No  </strong></td>
                <td><? echo ": ".$style_ref; ?></td>
                <td><strong>Body Part </strong></td>
                <td colspan="5" align="left">:
                <?php
                	$bodypart_id_arr=explode(",", $dataArray[0][csf('body_part_ids')]);
                	foreach ($bodypart_id_arr as  $sbodypart_id) {
                		$bodypart_name_arr[$sbodypart_id]=$time_weight_panel[$sbodypart_id];
                	}

                	echo implode(",", $bodypart_name_arr);
                ?></td>

            </tr>
            <tr>
	            <td><strong>OP Name </strong></td>
	            <td colspan="3">: <? echo $employee_arr[$dataArray[0][csf('operator_id')]]; ?></td>
	            <td><strong>Buyer Name </strong></td>
	            <td colspan="2">: <? echo $buyer_name; ?></td>
	            <td width="60"><strong>Knit Floor </strong></td>
	            <td colspan="2">:<? echo $floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
            </tr>
        </table>
       

        <div style="width:100%;">
            <table cellspacing="0" width="900" border="1" rules="all" class="rpt_table"
                   style=" margin-top:20px; font: 12px tahoma;">
                <thead bgcolor="#dddddd" align="center">
	                <th width="180">Gmt Color</th>
	                <th width="60" align="center">Color Seq</th>
	                <th width="180" align="center">Yarn Color</th>
	                <th width="100" align="center">Dye Lot</th>
	                <th width="80" align="center">Req. Qty</th>
	                <th width="80" align="center">Issue Qty</th>
	                <th width="80" align="center">Returnable Qty</th>
	                <th width="" align="center">Yarn Returned</th>            
                </thead>
                <tbody>
                <?

                $i = 1;
                $color_count = count($sql_yarn_issue_result);
                foreach ($sql_yarn_issue_result as $val) {
                    if ($i % 2 == 0)
                        $bgcolor = "#E9F3FF";
                    else
                        $bgcolor = "#FFFFFF";
/*gmts_color, 
   						yarn_color, 
   						sample_color, 
 						required_qty, 
  						returanable_qty, 
  						issue_qty, 
   						issue_balance_qty,
   						receive_qty, 
   						wastage*/
                    
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                    	<?php
                    	{
                		?>
                			<td align="center" rowspan="<?php echo $color_count; ?>"><? echo $color_library[$val[csf('gmts_color')]]; ?></td>

                		<?php
                    	}
                    	?>
                        <td align="center"><? echo $color_library[$val[csf('sample_color')]]; ?></td>
                        <td align="center"><? echo $color_library[$val[csf('yarn_color')]]; ?></td>
                        <td align="center"><? //echo $order_array[$val[csf('po_break_down_id')]]['style_des']; ?></td>
                        <td align="center"><? echo number_format($val[csf('required_qty')],4); ?></td>
                        <td align="center"><? echo number_format($val[csf('issue_qty')],4);; ?></td>
                        <td align="center"><? echo number_format($val[csf('returanable_qty')],4);; ?></td>
                        <td align="center"><? //echo $color_library[$val[csf('color_number_id')]]; ?></td>                    
                    </tr>
                    <?
                  
                    $total_required_qty += $val[csf('required_qty')];
                    $total_issue_qty += $val[csf('issue_qty')];
                    $total_returanable_qty += $val[csf('returanable_qty')];
                    $i++;
                }
                ?>
                </tbody>
             	<tfooter>

	                <tr>
	                   <th width="180">Gmt Color</th>
		                <th width="60" align="center">Color Seq</th>
		                <th width="180" align="center">Yarn Color</th>
		                <th width="100" align="center">Dye Lot</th>
		                <th width="80" align="center">Req. Qty</th>
		                <th width="80" align="center">Issue Qty</th>
		                <th width="80" align="center">Returnable Qty</th>
		                <th width="" align="center">Yarn Returned</th>
	                </tr>

            	</tfooter>
            </table>
            <br clear="all">
            <table cellspacing="0" border="1" rules="all" class="rpt_table"
                   style=" margin-top:20px; font: 12px tahoma;">
                <thead>
                <tr>
                    <td colspan="4"><strong>Color Wise Summary</strong></td>
                </tr>
                <tr bgcolor="#dddddd" align="center">
                    <td>SL</td>
                    <td>Color</td>
                    <td>No Of Bundle</td>
                    <td>Quantity (Pcs)</td>
                </tr>
                </thead>
                <tbody>
                <? $i = 1;
                foreach ($color_qty_arr as $color_id => $color_qty):
                    $bgcolor = ($i % 2 == 0) ? "#E9F3FF" : "#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td align="center"><? echo $i; ?></td>
                        <td align="center"><? echo $color_library[$color_id]; ?></td>
                        <td align="center"><? echo $color_wise_bundle_no_arr[$color_id]; ?></td>
                        <td align="right"><? echo $color_qty; ?></td>
                    </tr>
                    <?
                    $i++;
                endforeach; ?>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="4"></td>
                </tr>
                <tr>
                    <td colspan="2" align="right">Total =</td>
                    <td align="center"><? echo $total_bundle; ?></td>
                    <td align="right"><? echo $production_quantity; ?></td>
                </tr>
                </tfoot>
            </table>
            <br>
            <?
            echo signature_table(28, $data[0], "900px");
            ?>
        </div>
    </div>
    <script type="text/javascript" src="../../../js/jquery.js"></script>
    <script type="text/javascript" src="../../../js/jquery.qrcode.min.js"></script>
    <script>
       $('#qrcode').qrcode({width: 80,height: 80, text: <? echo json_encode($data[1]); ?>});
    </script>
    <?
    exit();
}



 

?>
