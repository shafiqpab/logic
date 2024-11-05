<?
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$sessionUnit_id=$_SESSION['logic_erp']['company_id'];

// $sweater_reject_type=array( 1=>"Needle Drop", 2=>"Drop Stitches", 3=>"Selvedge Broken", 4=>"Hole", 5=>"Misplatting", 6=>"Lycra Visible", 7=>"Lycra Missing", 8=>"Yarn Ply Missing", 9=>"Broken Stitch", 10=>"Loose Thread", 11=>"Mixed Dye Lot", 12=>"Size Yarn Mistake", 13=>"Knot", 14=>"Slub / Neps", 15=>"Uneven Dyeing", 16=>"Stripeness", 17=>"Fibre Contamination", 18=>"Oil Spot  ", 19=>"Dirty Spot", 20=>"Tuck Stitches", 21=>"Design Mistake", 22=>"Measurement Discripencies", 23=>"Others");

$sweater_reject_type=array( 1=>"Needle Drop", 2=>"Double Line", 3=>"Puckering Yarn", 4=>"Side Needle Drop", 5=>"Color Shading", 6=>"Wrong Measurement", 7=>"Defective Needle", 8=>"Tention Tight Loose", 9=>"Starting c/s", 10=>"Wrong Needle", 11=>"Dirty Fashion", 12=>"Yarn Thin & Thick", 13=>"Nylon Visible");

if($action=="receive_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	list($shortName,$ryear,$ratio_prifix)=explode('-',$lot_ratio);
	if($ryear=="") 	$ryear=date("Y",time());
	else 			$ryear=("20$ryear")*1;
	//echo $ryear;die;
	?>
	<script>		
		
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
							echo create_drop_down( "cbo_company_name", 140, $sql_com, "id,company_name", 1, "-- Select --", $sessionUnit_id, "",0 );
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
	                            name="txt_challan_no" 
	                            id="txt_challan_no" 
	                            style="width:120px" 
	                            class="text_boxes" />
	                    </td>  		
	            		<td align="center">
	                     	<input type="button"name="button2"class="formbutton"value="Show"onClick="show_list_view (document.getElementById('cbo_company_name').value+'_'+ document.getElementById('txt_challan_no').value+'_'+ document.getElementById('txt_job_no').value+'_'+ document.getElementById('txt_lot_no').value+'_'+ document.getElementById('cbo_lot_year').value, 'create_receive_callan_search_list_view', 'search_div', 'bundle_knitting_qc_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"style="width:100px;" />
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
if($action=="create_receive_callan_search_list_view")
{
 	$ex_data 				= explode("_",$data);
	$company 				= $ex_data[0];
	$job_no					=$ex_data[2];
	$challan_no				=$ex_data[1];
	$lot_no					=$ex_data[3];
	$syear 					= substr($ex_data[4],2);

	if(trim($ex_data[2]))	$challan_no = "".trim($ex_data[2])."";
	
	if( trim($ex_data[0])==0)
	{
		echo "<h2 style='color:#D00; text-align:center;'>Please Select Company First. </h2>";
		exit();
	}


	if( trim($ex_data[3])=='' && trim($ex_data[2])=='' && trim($ex_data[1])=='')
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
            <th width="120">Receive No</th>
            <th width="110">Lot Ratio No</th>
            <th width="150">Body Part </th>
            <th width="100">Operator ID</th>
            <th width="120">Operator</th>                      
            <th>Receive Qty.</th>
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
			$sql="  select a.sys_number, a.id, a.operator_id , a.body_part_ids, a.body_part_type , c.cutting_no, c.job_no, sum(b.production_qnty) as production_qty from pro_garments_production_dtls b, ppl_cut_lay_mst c, pro_gmts_delivery_mst a where a.id=b.delivery_mst_id $cutCon $jobCon $challanCon and b.cut_no=c.cutting_no and a.production_type=51 and b.production_type=51 and a.status_active=1  and b.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.is_deleted=0 group by a.sys_number, a.id, a.operator_id , a.body_part_ids, a.body_part_type , c.cutting_no, c.job_no order by a.sys_number";
			 //echo $sql;
			$result = sql_select($sql);
			$challan_id_arr=array();
			foreach ($result as $val)
			{ 
				$challan_id_arr[$val[csf('id')]]=$val[csf('id')];
			}

			$receive_data_arr=return_library_array( "select id,issue_challan_id from pro_gmts_delivery_mst where issue_challan_id in (".implode(",", $challan_id_arr).") and production_type=52 and status_active=1 and is_deleted=0 ", "issue_challan_id", "id"  );

			foreach ($result as $row)
			{  
				if(!$receive_data_arr[$row[csf('id')]])	
				{
					if($i%2==0) 
						$bgcolor="#E9F3FF"; 
					else 
						$bgcolor="#FFFFFF";
					$bodypart_name_arr=array();	
					$bodypart_arr=explode(',',$row[csf('body_part_ids')]);
				
					foreach ($bodypart_arr as $value) {
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
				}				
			}
			
        	?>
        
        </table>
    </div>
  
	<?	
	exit();	
}


if($action=="show_dtls_listview_bundle")
{
	//$machine_library=return_library_array( "select id,machine_no from lib_machine_name where category_id=1 and status_active=1 and is_deleted=0 and is_locked=0  order by seq_no", "id", "machine_no" );

	$data=explode("_",$data);

	$sql_cut=sql_select("select a.job_no, b.color_id, b.roll_data, a.id, b.id as dtls_id from ppl_cut_lay_mst a, ppl_cut_lay_dtls b where a.cutting_no='".$data[0]."' and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 ");
	$job_no				=$sql_cut[0][csf("job_no")];
	$color_id			=$sql_cut[0][csf("color_id")];
	$consumption_string	=$sql_cut[0][csf("roll_data")];
	$mst_id				=$sql_cut[0][csf("id")];
	$dtls_id			=$sql_cut[0][csf("dtls_id")];
	list($shortName,$year,$job_prifix)=explode('-',$job_no);
	//echo $consumption_string;die;
	
	$color_library=return_library_array( "select id,color_name from lib_color where id='".$color_id."'", "id", "color_name"  );

	
	$job_sql=sql_select("select c.short_name as BUYER_NAME, a.po_number as PO_NUMBER, a.id as ID from wo_po_break_down  a,wo_po_details_master b ,lib_buyer c where b.job_no='".$job_no."' and a.job_no_mst=b.job_no   and b.buyer_name=c.id
					");
	$jbp_arr=array();
	foreach($job_sql as $jval)
	{
		$jbp_arr["buyer_name"]=$jval["BUYER_NAME"];
		$jbp_arr[$jval["ID"]]=$jval["PO_NUMBER"];
	}

	$table_width=1380;
	$div_width=$table_width+20;

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
                    <th width="30"  >SL</th>
                    <th width="100" >Bundle No</th>
                    <th width="100" >Barcode No</th>
                    <th width="120" > G. Color</th>
                    <th width="50"  >Size</th>
                    <th width="70"  >Bundle Qty. (Pcs)</th>
                    <th width="70"  >Defect Qty (Pcs)</th>
                    <th width="70"  >Reject Qty (Pcs)</th>
                    <th width="70"  >Replace Qty. (Pcs)</th>
                    <th width="70"  >QC Qty. (Pcs)</th>
                    <th width="50"  >Year</th>
                    <th width="60"  >Job No</th>
                    <th width="65"  >Buyer</th>
                    <th width="90"  >Order No</th>
                    <th width="100" >Gmts. Item</th>
                    <th width="100" >Country</th>
                    <th 			>Action</th>
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
			$barcode_no="'".implode("','",explode(",",$data[1]))."'";
			if($data[1]!="")
			$barcode_cond=" c.barcode_no in (".$barcode_no.") ";

			
			$sqlResult =sql_select("select b.* , a.gmt_item_id, c.machine_id, c.bundle_qty, c.production_qnty, c.color_size_break_down_id from ppl_cut_lay_dtls a, ppl_cut_lay_bundle b, pro_garments_production_dtls c where $barcode_cond and c.production_type=51 and b.mst_id=$mst_id and b.dtls_id=$dtls_id and c.bundle_no=b.bundle_no and a.id=b.dtls_id and a.color_id=$color_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
			$sizeidArr=array(); $countryidArr=array();
			foreach($sqlResult as $row)
			{
				$sizeidArr[$row[csf('size_id')]]=$row[csf('size_id')];
				$countryidArr[$row[csf('country_id')]]=$row[csf('country_id')];
			}
			$size_library=return_library_array( "select id,size_name from lib_size where 1=1 ".where_con_using_array($sizeidArr,1,'id')."", "id", "size_name");
			
			$country_library=return_library_array( "select id,country_name from lib_country where 1=1 ".where_con_using_array($countryidArr,1,'id')."", "id", "country_name" );

			foreach($sqlResult as $selectResult)
			{
				if ($i%2==0)  $bgcolor="#E9F3FF";
                else $bgcolor="#FFFFFF";
 			?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="" > 
                    <td width="30" align="center"><? echo $i; ?></td>
                    <td width="100" align="center"><p><? echo $selectResult[csf('bundle_no')]; ?></p></td>
                    <td width="100" align="center"><p><? echo $selectResult[csf('barcode_no')]; ?></p></td>
                    <td width="120" align="center" style="word-break:break-all"><p><?php  echo $color_library[$color_id]; ?></p></td>
                    <td width="50" align="center"><?php  echo $size_library[$selectResult[csf('size_id')]]; ?></td>
                    <td width="70" align="right"><?php  echo $selectResult[csf('production_qnty')]; ?></td>
                    <td width="70" align="center"> 
                    	<input 
                    		onkeyup="calculate_defect_qty()"
                    		onDblClick="defect_qty_popup(<? echo $i; ?>,<? echo $selectResult[csf('id')]; ?>,<? echo $selectResult[csf('production_qnty')]; ?>)"
							type="text" 
							id="txt_defect_qty_<? echo $i; ?>" 
							name="txt_defect_qty[]"
							style="width:50px;"
							class="text_boxes_numeric"
							value="<?php  //echo $selectResult[csf('production_qnty')]; ?>">

                    </td>
                    <td width="70" align="center"> 
                    	<input 
                    		onkeyup="calculate_reject_qty()"
                    		onDblClick="reject_qty_popup(<? echo $i; ?>,<? echo $selectResult[csf('id')]; ?>,<? echo $selectResult[csf('size_qty')]; ?>)"
							type="text" 
							id="txt_reject_qty_<? echo $i; ?>" 
							name="txt_reject_qty[]"
							style="width:50px;"
							class="text_boxes_numeric"
							value="<?php  //echo $selectResult[csf('production_qnty')]; ?>">

                    </td>
                    <td width="70" align="center"> 
                    	<input
                    		onkeyup="calculate_defect_qty()"
							type="text" 
							id="txt_replace_qty_<? echo $i; ?>" 
							name="txt_replace_qty[]"
							style="width:50px;"
							class="text_boxes_numeric"
							value="<?php  //echo $selectResult[csf('production_qnty')]; ?>">

                    </td>
                    <td width="70" align="center"><?php  echo $selectResult[csf('production_qnty')]; ?></td>
                    <td width="50" align="center"><p><? echo $year; ?></p></td>
                    <td width="60" align="center"><p><? echo $job_prifix*1; ?></p></td>
                    <td width="65" align="center"><?php echo $jbp_arr["buyer_name"]; ?></td>
                    <td width="90" align="center"><?php  echo $jbp_arr[$selectResult[csf('order_id')]]; ?></td>
                    		
                    <td width="100" align="center"><p><?php echo $garments_item[$selectResult[csf('gmt_item_id')]]; ?></p></td>
                    <td width="100" align="center"><p><? echo $country_library[$selectResult[csf('country_id')]]; ?></p></td>
                     <td >
                     	<input 
                        	type="button" 
                            value="-" 
                            name="minusButton[]" 
                            id="minusButton_<? echo $i;  ?>" 
                            style="width:30px" 
                            class="formbuttonplasminus" 
                            onClick="fnc_minusRow('<? echo $i;  ?>')"/>
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
                            value="<?php echo $selectResult[csf('color_size_break_down_id')]; ?>">
                  

                        <input
                            type="hidden" 
                            id="txt_dtls_id_<? echo $i; ?>" 
                            name="txt_dtls_id[]"
                            style="width:80px;"
                            class="text_boxes"
                            value="">

                       	<input
                            type="hidden" 
                            id="trId_<? echo $i; ?>" 
                            name="trId[]"
                            value="<?php echo $i; ?>">
                        <input
                            type="hidden" 
                            id="actual_defect_<? echo $i; ?>" 
                            name="actual_defect[]"
                            value="">
						<input
                            type="hidden" 
                            id="hidden_breakdown_defect_<? echo $i; ?>" 
                            name="hidden_breakdown_defect[]"
                            value="">
						<input
                            type="hidden" 
                            id="hidden_breakdown_reject_<? echo $i; ?>" 
                            name="hidden_breakdown_reject[]"
                            value="">
                        <input
                            type="hidden" 
                            id="actual_reject_<? echo $i; ?>" 
                            name="actual_reject[]"
                            value="">
                    	<input type="hidden" name="isRescan[]" id="isRescan_<? echo $i; ?>" value="<? echo 0;?>"/>
                	</td>
                </tr>
            <?php
                $i++;
                $total_bundle_qty+=$selectResult[csf('production_qnty')];
			}
			?>
            </tbody>
            <tfoot>
            	<tr id="bundle_footer">
                    <th   colspan="5" > Total</th>
                    <th width="70"  id="total_bundle_qty"><?php echo $total_bundle_qty; ?></th>
                    <th width="70"  id="total_defect_qty"><?php //echo $total_size_qty; ?></th>
                    <th width="70"  id="total_reject_qty"><?php //echo $total_size_qty; ?></th>
                
                 
                    <th width="70" id="total_replace_qty"><?php echo number_format($grand_total_consumption,4,".","");?></th>
                    <th width="70" id="total_qc_qty"><?php echo $total_bundle_qty; ?></th>
                    <th width="50"  ></th>
                    <th width="60"  ></th>
                    <th width="65"  ></th>
                    <th width="90"  ></th>
                    <th width="100" ></th>
                    <th width="100" ></th>
                    <th id=""></th>
                </tr>
            </tfoot>
		</table>
	</div>
	<?
	exit();
}

if($action=="defect_qty_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	$caption_name="";
	//print_r($sew_fin_alter_defect_type);die;
	?>
   	<script>
		function fnc_close( )
		{
			var rowCount = $('#tbl_purchase_item tr').length-1;
			
			var defect_breck_down_data="";
			var max_defect="";	var save_string='';	var total_qty=0;
			for(var i=1; i<=rowCount; i++)
			{

				var txtDefectQnty=$('#txtDefectQnty_'+i).val()*1;			
				var txtDefectId=$('#txtDefectId_'+i).val();
				var txtBodyPartId=$('#cboBodyPartId_'+i).val();
				
				 if(max_defect==txtDefectQnty || txtDefectQnty > max_defect){
					var max_defect=$('#txtDefectQnty_'+i).val()*1;
				 }
				
				 if(txtDefectQnty > <?=$bundle_qnty;?>){

					alert("Defect qnty more than Bundle Size Qnty.")
					$('#txtDefectQnty_'+i).val('');
					return
				}

				if(defect_breck_down_data=="")
				{
					defect_breck_down_data+=<?=$bundle_mst_id;?>+'_'+$('#txtDefectId_'+i).val()+'_'+$('#txtDefectName_'+i).val()+'_'+$('#cboBodyPartId_'+i).val()+'_'+($('#txtDefectQnty_'+i).val()*1);;
					save_string=txtDefectId+"*"+txtDefectQnty+"*"+txtBodyPartId;
					// total_qty+=txtDefectQnty*1;
				}
				else
				{
					defect_breck_down_data+="__"+<?=$bundle_mst_id;?>+'_'+$('#txtDefectId_'+i).val()+'_'+$('#txtDefectName_'+i).val()+'_'+$('#cboBodyPartId_'+i).val()+'_'+($('#txtDefectQnty_'+i).val()*1);;

					save_string+="**"+txtDefectId+"*"+txtDefectQnty+"*"+txtBodyPartId;
					// total_qty+=txtDefectQnty*1;
				}
			}
			$('#actual_defect_infos').val( save_string );
			$('#actual_defect_qty').val(max_defect);
			$('#hidden_breakdown_defect').val(defect_breck_down_data);			
			
			parent.emailwindow.hide();
		}

		function calculate_defect()
		{
			var defect_qty=0;
			$("#tbl_list_search").find('tbody tr').each(function()
				{
					//alert(4);
				defect_qty+=$(this).find('input[name="txtDefectQnty[]"]').val()*1;	
				});
			$("#defect_qty_td").text(defect_qty);
		}

	function add_break_down_tr_trim_cost( i )
	{
		var row_num=$('#tbl_purchase_item tr').length-1;
		if (i==0)
		{
			i=1;
			$("#itemColor_"+i).autocomplete({
				source: str_color
			});
			$("#itemsize_"+i).autocomplete({
				source:  str_size
			});
		   $("#itemdescription_"+i).autocomplete({
				source: str_trimdescription
			});
		  	return;
		}
		if (row_num!=i)
		{
			return false;
		}
		else
		{
			i++;
			$("#tbl_purchase_item tr:last").clone().find("input,select").each(function() {
				$(this).attr({
				  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
				  'name': function(_, name) { return name + i },
				  'value': function(_, value) { return value }
				});

			}).end().appendTo("#tbl_purchase_item");

			// var uom_id=$('#cboUom_'+Number(i-1)).val();
			// $('#cboUom_'+Number(i-1)).val();
			$('#sl_'+i).val(i);

			$('#txtdefectname_'+i).removeAttr("onDblClick").attr("onDblClick","openpopup_itemgroup("+i+")");
			$('#increasetrim_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr_trim_cost("+i+");");
			$('#decreasetrim_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_purchase_item');");

			/*$('#txtitemgroup_'+i).val("");
			$('#itemdescription_'+i).val("");
			$('#itemColor_'+i).val("");
			$('#itemsize_'+i).val("");
			$('#quantity_'+i).val("");
			$('#rate_'+i).val("");
			$('#amount_'+i).val("");
			$('#txtRemarks_'+i).val("");
			$('#hdnItemGroupId_'+i).val("");
			$('#hdnItemColorId_'+i).val("");
			$('#hdnItemSizeId_'+i).val("");
			$('#hdnDtlsId_'+i).val("");
			$('#cboUom_'+i).val(0);*/

			
		}
	}

	function fn_deletebreak_down_tr(rowNo,table_id)
	{
		if(table_id =='tbl_purchase_item')
		{
			var numRow = $('table#tbl_purchase_item tbody tr').length;

			if(numRow!=1)
			{
				
				var index=rowNo-1;
				$("table#tbl_purchase_item tbody tr:eq("+index+")").remove();
				var numRow = $('table#tbl_purchase_item tbody tr').length;
				for(i = rowNo;i <= numRow;i++)
				{
					$("#tbl_purchase_item tr:eq("+i+")").find("input,select").each(function() {
						$(this).attr({
							'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
							//'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i},
							'value': function(_, value) { return value }
						});
						$('#txtdefectname_'+i).removeAttr("onDblClick");
					})
				}
			}
		}
	}


   </script> 
    </head>
    <body>
        <div align="center" style="width:100%;" >
        <form name="defect_1"  id="defect_1" autocomplete="off">
			<? //echo load_freeze_divs ("../../",$permission,1); ?>
			<fieldset style="width:500px; margin-top:10px;">
	    <legend>Purchase Requisition Details</legend>
	    <form name="purchaserequisition_2" id="purchaserequisition_2" autocomplete="off">
	    	<table class="rpt_table" width="100%" cellspacing="1" id="tbl_purchase_item">
	            <thead>
	            	<tr>
	                    <th width="35">SL</th>
	                    <th width="100" >Defect Name</th>	             
	                    <th width="100">Body Part</th>	                
	                    <th width="100">No. of Defect</th>                  
	                    <th width="100">Action</th>
	                </tr>
	        	</thead>
	            <tbody>
				<?
				//echo $actual_infos;
					$explSaveData = explode("**",$actual_infos);
					$defect_dataArray=array();
					foreach($explSaveData as $val)
					{
						$difectVal = explode("*",$val);
						//$defect_dataArray['up_id']=$difectVal[0];
						$defect_dataArray[$difectVal[0]]['defectid']=$difectVal[0];
						$defect_dataArray[$difectVal[0]]['defectQnty']=$difectVal[1];
						$defect_dataArray[$difectVal[0]]['defectbodypart']=$difectVal[2];
					}
				
					$sql_cut=sql_select("select a.body_part_string from ppl_cut_lay_mst a where a.cutting_no='".$lot_ratio."' and a.status_active=1 ");
					$body_part_string	=$sql_cut[0][csf("body_part_string")];
					if($body_part_string)
					{
						foreach(explode(',', $body_part_string) as $bodypart_id)
						{
							if($bodypart_id!=14) $wet_sheet_bodypart[$bodypart_id]=$bodypart_id;	
						}
					}

					$wet_sheet_bodypart_string=implode(',', $wet_sheet_bodypart);
					//echo $wet_sheet_bodypart_string;die;
					$i=1;
					$total_reject=0;
					$defect_data=sql_select("select id, ppl_cut_lay_bundle_mst_id,defect_id,defect_name,body_part_id,no_of_defect from ppl_cut_lay_bundle_defect where  ppl_cut_lay_bundle_mst_id=$bundle_mst_id and status_active=1 and is_deleted=0");
					//echo "select id, ppl_cut_lay_bundle_mst_id,defect_id,defect_name,body_part_id,no_of_defect from ppl_cut_lay_bundle_defect where  ppl_cut_lay_bundle_mst_id=$bundle_mst_id and status_active=1 and is_deleted=0";
				if(count($defect_data)){
						foreach($defect_data as $row){
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
	                <tr class="general" bgcolor="<?=$bgcolor; ?>" id="tr_<?=$i; ?>">
	                    <td><input type="text"  id="sl_<?=$i;?>" class="text_boxes" style="width: 35px;" value="<?=$i;?>"/></td>
						<td>
							<?=create_drop_down("cboDeFectId_$i",120,$sweater_reject_type,"",1,"-- Select --",$row[csf('defect_id')],"",0 ,"1","","","","","","cboDeFectId[]"); ?>				
	                    	<input type="hidden" name="txtDefectName[]" id="txtDefectName_<?=$i;?>" class="text_boxes" value="<?=$row[csf('defect_name')];?>" style="width:85px;"  readonly/>
	                    </td>
	                    <td><?=create_drop_down( "cboBodyPartId_$i",120, $time_weight_panel,"", 1, "-- Select --", $row[csf('body_part_id')],"",0 ,$wet_sheet_bodypart_string,"","","","","","cboBodyPartId[]"); ?></td>
	                    <td>
							<input type="text" name="txtDefectQnty[]" id="txtDefectQnty_<?=$i;?>" class="text_boxes_numeric" style="width:80px" value="<?=$row[csf('no_of_defect')]; ?>" onKeyUp="calculate_defect();">
                            <input type="hidden" name="txtDefectId[]" id="txtDefectId_<?=$i; ?>" style="width:40px" value="<?=$row[csf('defect_id')]; ?>">
                            <input type="hidden" name="txtDefectUpdateId[]" id="txtDefectUpdateId_<?=$i; ?>" style="width:40px" value="<?=$row[csf('id')]; ?>">
	                    </td>
	                    <td style="display: inline-flex;">
	                    	<input type="button" id="increasetrim_<?=$i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_trim_cost(<?=$i; ?>)"  />
	                        <input type="button" id="decreasetrim_<?=$i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<?=$i; ?> ,'tbl_purchase_item');"  />
	                    </td>
	                </tr>
					<?
                            $i++;
						}
				}else{
					foreach($sweater_reject_type as $id=>$val)
					{
						if($id==1)
						{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$total_reject+=$defect_dataArray[$id]['defectQnty'];
						?>
	                <tr class="general" bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
	                    <td><input type="text"  id="sl_<?=$i;?>" class="text_boxes" style="width: 35px;" value="1"/></td>
	                    <td><input type="text" name="txtDefectName[]" id="txtDefectName_<?=$i;?>" class="text_boxes" value="<?=$val;?>" style="width:85px;"  readonly/></td>
	                
	                    <td><?=create_drop_down( "cboBodyPartId_$i",120, $time_weight_panel,"", 1, "-- Select --", $defect_dataArray[$id]['defectbodypart'],"",0 ,$wet_sheet_bodypart_string,"","","","","","cboBodyPartId[]"); ?></td>
	                    <td>
							<input type="text" name="txtDefectQnty[]" id="txtDefectQnty_<?=$i;?>" class="text_boxes_numeric" style="width:80px" value="<?=$defect_dataArray[$id]['defectQnty']; ?>"  onKeyUp="calculate_defect()">
                            <input type="hidden" name="txtDefectId[]" id="txtDefectId_<?=$i; ?>" style="width:40px" value="<?=$id; ?>">
                            <input type="hidden" name="txtDefectUpdateId[]" id="txtDefectUpdateId_<?=$i; ?>" style="width:40px" value="<?=$defect_dataArray[$id]['up_id']; ?>">
	                    </td>
	                    <td style="display: inline-flex;">
	                    	<input type="button" id="increasetrim_<?=$i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_trim_cost(<?=$i; ?>)"  />
	                        <input type="button" id="decreasetrim_<?=$i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<?=$i; ?> ,'tbl_purchase_item' );"  />
	                    </td>
	                </tr>
					<?
                            $i++;
							}
                        }
			}
					?>
	            </tbody>
				
	        </table>           
				<tr>
                    <td align="center" colspan="5">
           
						<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
                     	 <input type="hidden" id="actual_defect_infos" />
                         <input type="hidden" id="actual_defect_qty" />
						 <input type="hidden" id="hidden_breakdown_defect" />
                    </td>
                </tr>	
            </table>
	    </form>
	</fieldset>
    </form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
  	<?
	exit();	
}

if($action=="reject_qty_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	$caption_name="";
	//print_r($sew_fin_alter_defect_type);die;
	
	?>
   	<script>
   	function fnc_close()
	{
		var rowCount = $('#tbl_purchase_item tr').length-1;
		
		var defect_breck_down_data="";
		var max_defect=0;	var save_string='';	var total_qty=0;
		for(var i=1; i<=rowCount; i++)
		{
			var txtDefectQnty=$('#txtDefectQnty_'+i).val()*1;			
			var txtDefectId=$('#cboRejectId_'+i).val();
			var txtBodyPartId=$('#cboBodyPartId_'+i).val();
	
			if(max_defect==txtDefectQnty || txtDefectQnty > max_defect){
				var max_defect=$('#txtDefectQnty_'+i).val()*1;
			}
			//alert(max_defect); return;
	
			if(txtDefectQnty > <?=$bundle_qnty;?>){

				alert("Defect qnty more than Bundle Size Qnty.")
				$('#txtDefectQnty_'+i).val('');
				return
			}
			var rejecttxt=get_dropdown_text('cboRejectId_'+i);
			//alert(rejecttxt);
			$('#txtRejectName_'+i).val(trim(rejecttxt));

			if(defect_breck_down_data=="")
			{
				defect_breck_down_data+=<?=$bundle_mst_id;?>+'_'+$('#cboRejectId_'+i).val()+'_'+$('#cboBodyPartId_'+i).val()+'_'+($('#txtDefectQnty_'+i).val()*1);;
				save_string=txtDefectId+"*"+txtDefectQnty+"*"+txtBodyPartId;
				// total_qty+=txtDefectQnty*1;
			}
			else
			{
				defect_breck_down_data+="__"+<?=$bundle_mst_id;?>+'_'+$('#cboRejectId_'+i).val()+'_'+$('#cboBodyPartId_'+i).val()+'_'+($('#txtDefectQnty_'+i).val()*1);;

				save_string+="**"+txtDefectId+"*"+txtDefectQnty+"*"+txtBodyPartId;
				// total_qty+=txtDefectQnty*1;
			}
		}

		$('#actual_reject_infos').val( save_string );
		$('#actual_reject_qty').val(max_defect);
		$('#hidden_breakdown_reject').val(defect_breck_down_data);			
		
		parent.emailwindow.hide();
	}

   function calculate_defect()
   {
	 	var defect_qty=0;
	 	$("#tbl_list_search").find('tbody tr').each(function()
		{
			//alert(4);
			defect_qty+=$(this).find('input[name="txtDefectQnty[]"]').val()*1;	
		});
	   	$("#defect_qty_td").text(defect_qty);
   }

	function add_break_down_tr_trim_cost( i )
	{
		var row_num=$('#tbl_purchase_item tr').length-1;
		if (i==0)
		{
			i=1;
			$("#itemColor_"+i).autocomplete({
				source: str_color
			});
			$("#itemsize_"+i).autocomplete({
				source:  str_size
			});
		   $("#itemdescription_"+i).autocomplete({
				source: str_trimdescription
			});
		  	return;
		}
		if (row_num!=i)
		{
			return false;
		}
		else
		{
			i++;
			$("#tbl_purchase_item tr:last").clone().find("input,select").each(function() {
				$(this).attr({
				  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
				  'name': function(_, name) { return name + i },
				  'value': function(_, value) { return value }
				});

			}).end().appendTo("#tbl_purchase_item");

			// var uom_id=$('#cboUom_'+Number(i-1)).val();
			// $('#cboUom_'+Number(i-1)).val();
			$('#sl_'+i).val(i);

			$('#txtdefectname_'+i).removeAttr("onDblClick").attr("onDblClick","openpopup_itemgroup("+i+")");
			$('#increasetrim_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr_trim_cost("+i+");");
			$('#decreasetrim_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_purchase_item');");

			/*$('#txtitemgroup_'+i).val("");
			$('#itemdescription_'+i).val("");
			$('#itemColor_'+i).val("");
			$('#itemsize_'+i).val("");
			$('#quantity_'+i).val("");
			$('#rate_'+i).val("");
			$('#amount_'+i).val("");
			$('#txtRemarks_'+i).val("");
			$('#hdnItemGroupId_'+i).val("");
			$('#hdnItemColorId_'+i).val("");
			$('#hdnItemSizeId_'+i).val("");
			$('#hdnDtlsId_'+i).val("");
			$('#cboUom_'+i).val(0);*/
		}
	}

	function fn_deletebreak_down_tr(rowNo,table_id)
	{
		if(table_id =='tbl_purchase_item')
		{
			var numRow = $('table#tbl_purchase_item tbody tr').length;

			if(numRow!=1)
			{
				
				var index=rowNo-1;
				$("table#tbl_purchase_item tbody tr:eq("+index+")").remove();
				var numRow = $('table#tbl_purchase_item tbody tr').length;
				for(i = rowNo;i <= numRow;i++)
				{
					$("#tbl_purchase_item tr:eq("+i+")").find("input,select").each(function() {
						$(this).attr({
							'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
							//'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i},
							'value': function(_, value) { return value }
						});
						$('#txtdefectname_'+i).removeAttr("onDblClick");
					})
				}
			}
		}
	}

   </script> 
    </head>
    <body>
        <div align="center" style="width:100%;" >
        <form name="defect_1"  id="defect_1" autocomplete="off">
			<? //echo load_freeze_divs ("../../",$permission,1); ?>
			<fieldset style="width:500px; margin-top:10px;">
	    <legend>Reject Details</legend>
	    <form name="purchaserequisition_2" id="purchaserequisition_2" autocomplete="off">
	    	<table class="rpt_table" width="100%" cellspacing="1" id="tbl_purchase_item">
	            <thead>
	            	<tr>
	                    <th width="35">SL</th>
	                    <th width="100" >Reject Name</th>	             
	                    <th width="100">Body Part</th>	                
	                    <th width="100">No. of Defect</th>                  
	                    <th width="100">Action</th>
	                </tr>
	        	</thead>
	            <tbody>
				<?
					
					$explSaveData = explode("**",$actual_infos);
					
					$defect_dataArray=array();
					foreach($explSaveData as $val)
					{
						$difectVal = explode("*",$val);
						//$defect_dataArray['up_id']=$difectVal[0];
						$defect_dataArray[$difectVal[0]]['defectid']=$difectVal[0];
						$defect_dataArray[$difectVal[0]]['defectQnty']=$difectVal[1];
						$defect_dataArray[$difectVal[0]]['defectbodypart']=$difectVal[2];
					}
				
					$sql_cut=sql_select("select a.body_part_string from ppl_cut_lay_mst a where a.cutting_no='".$lot_ratio."' and a.status_active=1 ");
					$body_part_string	=$sql_cut[0][csf("body_part_string")];
					if($body_part_string)
					{
						foreach(explode(',', $body_part_string) as $bodypart_id)
						{
							if($bodypart_id!=14) $wet_sheet_bodypart[$bodypart_id]=$bodypart_id;	
						}
					}

					$wet_sheet_bodypart_string=implode(',', $wet_sheet_bodypart);
					//echo $wet_sheet_bodypart_string;die;
					$i=1;
					$total_reject=0;

					$reject_data=sql_select("select id, ppl_cut_lay_bundle_mst_id, reject_id, reject_name, body_part_id, no_of_reject from ppl_cut_lay_bundle_reject where  ppl_cut_lay_bundle_mst_id=$bundle_mst_id and status_active=1 and is_deleted=0");

				if(count($reject_data)>0){

					foreach($reject_data as $row){
							$val=$row[csf('reject_name')];
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$total_reject+=$defect_dataArray[$id]['defectQnty'];
						?>

	                <tr class="general" bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
	                	
	                    <td><input type="text" name="sl_<?=$i;?>" id="sl_<?=$i;?>" class="text_boxes" style="width: 35px;" value="<?=$i;?>"/></td>
	                    <td><?=create_drop_down( "cboRejectId_$i",120, $sweater_reject_type,"", 1, "-- Select --", $row[csf('reject_id')],"",0 ,"","","","","","","cboRejectId[]"); ?>
							<input type="hidden" name="txtRejectName[]" id="txtRejectName_<? echo $i;?>" class="text_boxes" value="<?=$val;?>" style="width:85px;"  readonly/>
	                    </td>
	                    <td><?=create_drop_down( "cboBodyPartId_$i",120, $time_weight_panel,"", 1, "-- Select --", $row[csf('body_part_id')],"",0 ,$wet_sheet_bodypart_string,"","","","","","cboBodyPartId[]"); ?></td>
	                    <td>
							<input type="text" name="txtDefectQnty[]" id="txtDefectQnty_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" value="<? echo $row[csf('no_of_reject')]; ?>"  onKeyUp="calculate_defect()">
                            <input type="hidden" name="txtDefectId[]" id="txtDefectId_<? echo $i; ?>" style="width:40px" value="<? echo $row[csf('reject_id')]; ?>">
                            <input type="hidden" name="txtDefectUpdateId[]" id="txtDefectUpdateId_<? echo $i; ?>" style="width:40px" value="<? echo $row[csf('id')]; ?>">
	                    </td>
	                    <td style="display: inline-flex;">
	                    	<input type="button" id="increasetrim_<? echo $i;?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_trim_cost(<? echo $i;?>)"  />
	                        <input type="button" id="decreasetrim_<? echo $i;?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i;?> ,'tbl_purchase_item' );"  />
	                    </td>
	                </tr>
					<?
                            $i++;
					}
				}else{

					foreach($sweater_reject_type as $id=>$val)
					{
						if($id==1)
						{

					
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$total_reject+=$defect_dataArray[$id]['defectQnty'];
						?>

	                <tr class="general" bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
	                	
	                    <td>	            
							<input type="text" name="sl_<?=$i;?>" id="sl_<?=$i;?>" class="text_boxes" style="width: 35px;" value="1"/>
	                    </td>
	                    <td>
							<?
							echo create_drop_down( "cboRejectId_$i",120, $sweater_reject_type,"", 1, "-- Select --", "","",0 ,"","","","","","","cboRejectId[]");
	                        ?>
							<input type="hidden" name="txtRejectName[]" id="txtRejectName_<? echo $i;?>" class="text_boxes" value="<?=$val;?>" style="width:85px;"  readonly/>
	                    </td>
	                
	                    <td>
							<?
						echo create_drop_down( "cboBodyPartId_$i",120, $time_weight_panel,"", 1, "-- Select --", $defect_dataArray[$id]['defectbodypart'],"",0 ,$wet_sheet_bodypart_string,"","","","","","cboBodyPartId[]");
	                        ?>
	                    </td>
	                    
	                    <td>
							<input type="text" name="txtDefectQnty[]" id="txtDefectQnty_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" value="<? echo $defect_dataArray[$id]['defectQnty']; ?>"  onKeyUp="calculate_defect()">
                            <input type="hidden" name="txtDefectId[]" id="txtDefectId_<? echo $i; ?>" style="width:40px" value="<? echo $id; ?>">
                            <input type="hidden" name="txtDefectUpdateId[]" id="txtDefectUpdateId_<? echo $i; ?>" style="width:40px" value="<? echo $defect_dataArray[$id]['up_id']; ?>">
	                    </td>
	                  
	                   
	                    <td style="display: inline-flex;">
	                    	<input type="button" id="increasetrim_<? echo $i;?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_trim_cost(<? echo $i;?>)"  />
	                        <input type="button" id="decreasetrim_<? echo $i;?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i;?> ,'tbl_purchase_item' );"  />
	                    </td>
	                </tr>
					<?
                            $i++;
							}
                        }

				  }
					?>
	            </tbody>
				
	        </table>
			</table>           
				<tr>
                    <td align="center" colspan="5">
           
						<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
						<input type="hidden" id="actual_reject_infos" />
                         <input type="hidden" id="actual_reject_qty" />
						 <input type="hidden" id="hidden_breakdown_reject" />
                     
                    </td>
                </tr>	
            </table>
	    </form>
	</fieldset>
        </form>
        </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
  <?
  exit();	
}

if($action=="populate_bundle_data")
{
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
	$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name" );
	$data=explode("**",$data);
	$i=$data[1]+1;
	
	//echo $i;die;
	$sql_cut=sql_select("select a.job_no, b.color_id, b.roll_data, a.id, b.id as dtls_id from ppl_cut_lay_mst a, ppl_cut_lay_dtls b
							where a.cutting_no='".$data[3]."' and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0");
	$job_no				=$sql_cut[0][csf("job_no")];
	$color_id			=$sql_cut[0][csf("color_id")];
	$consumption_string	=$sql_cut[0][csf("roll_data")];
	$mst_id				=$sql_cut[0][csf("id")];
	$dtls_id			=$sql_cut[0][csf("dtls_id")];
	list($shortName,$year,$job_prifix)=explode('-',$job_no);

	
	$job_sql=sql_select("select c.short_name as BUYER_NAME, a.po_number as PO_NUMBER, a.id as ID from wo_po_break_down  a, wo_po_details_master b, lib_buyer c where b.job_no='".$job_no."' and a.job_no_mst=b.job_no and b.buyer_name=c.id");
	$jbp_arr=array();
	foreach($job_sql as $jval)
	{
		$jbp_arr["buyer_name"]=$jval["BUYER_NAME"];
		$jbp_arr[$jval["ID"]]=$jval["PO_NUMBER"];
	}
	
	$table_width	=1300;
	$div_width		=$table_width+20;
	$barcode_no="'".implode("','",explode(",",$data[0]))."'";
	if($data[0]!="")
	$barcode_cond=" c.barcode_no in (".$barcode_no.") ";
	
	$total_production_qnty=0;
	$sqlResult =sql_select("select b.* , a.gmt_item_id, c.machine_id, c.bundle_qty, b.size_qty, c.color_size_break_down_id
									from ppl_cut_lay_dtls a, ppl_cut_lay_bundle b, pro_garments_production_dtls c 
									where $barcode_cond and c.production_type=51 and b.mst_id=$mst_id and b.dtls_id=$dtls_id and c.bundle_no=b.bundle_no and a.id=b.dtls_id and a.color_id=$color_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
								
	foreach($sqlResult as $selectResult)
	{
		if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		$total_production_qnty+=$selectResult[csf('production_qnty ')]; 	
	?>
		<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="" > 
            <td width="30" align="center"><? echo $i; ?></td>
            <td width="100" align="center"><p><? echo $selectResult[csf('bundle_no')]; ?></p></td>
            <td width="100" align="center"><p><? echo $selectResult[csf('barcode_no')]; ?></p></td>
            <td width="120" align="center" style="word-break:break-all"><p><?php  echo $color_library[$color_id]; ?></p></td>
            <td width="50" align="center"><?php  echo $size_library[$selectResult[csf('size_id')]]; ?></td>
            <td width="70" align="right"><?php  echo $selectResult[csf('size_qty')]; ?></td>
            <td width="70" align="center"> 
            	<input onKeyUp="calculate_defect_qty();" onDblClick="defect_qty_popup(<?=$i; ?>,<?=$selectResult[csf('id')]; ?>,<?=$selectResult[csf('size_qty')]; ?>);" type="text" id="txt_defect_qty_<?=$i; ?>" name="txt_defect_qty[]" style="width:50px;" class="text_boxes_numeric" value="<? //=$selectResult[csf('production_qnty')]; ?>">
            </td>
            <td width="70" align="center"> 
            	<input 
            		onkeyup="calculate_reject_qty()"
            		onDblClick="reject_qty_popup(<? echo $i; ?>,<? echo $selectResult[csf('id')]; ?>,<? echo $selectResult[csf('size_qty')]; ?>)"
					type="text" 
					id="txt_reject_qty_<? echo $i; ?>" 
					name="txt_reject_qty[]"
					style="width:50px;"
					class="text_boxes_numeric"
					value="<?php  //echo $selectResult[csf('production_qnty')]; ?>">

            </td>
            <td width="70" align="center"> 
            	<input
            		onkeyup="calculate_defect_qty()"
					type="text" 
					id="txt_replace_qty_<? echo $i; ?>" 
					name="txt_replace_qty[]"
					style="width:50px;"
					class="text_boxes_numeric"
					value="<?php  //echo $selectResult[csf('production_qnty')]; ?>">

            </td>
            <td width="70" align="center"></td>
            <td width="50" align="center"><p><? echo $year; ?></p></td>
            <td width="60" align="center"><p><? echo $job_prifix*1; ?></p></td>
            <td width="65" align="center"><?php echo $jbp_arr["buyer_name"]; ?></td>
            <td width="90" align="center"><?php  echo $jbp_arr[$selectResult[csf('order_id')]]; ?></td>
            		
            <td width="100" align="center"><p><?php echo $garments_item[$selectResult[csf('gmt_item_id')]]; ?></p></td>
            <td width="100" align="center"><p><? echo $country_library[$selectResult[csf('country_id')]]; ?></p></td>
             <td >
             	<input 
                	type="button" 
                    value="-" 
                    name="minusButton[]" 
                    id="minusButton_<? echo $i;  ?>" 
                    style="width:30px" 
                    class="formbuttonplasminus" 
                    onClick="fnc_minusRow('<? echo $i;  ?>')"/>
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
                    value="<?php echo $selectResult[csf('color_size_break_down_id')]; ?>">
            

                <input
                    type="hidden" 
                    id="txt_dtls_id_<? echo $i; ?>" 
                    name="txt_dtls_id[]"
                    style="width:80px;"
                    class="text_boxes"
                    value="">

               	<input
                    type="hidden" 
                    id="trId_<? echo $i; ?>" 
                    name="trId[]"
                    value="<?php echo $i; ?>">

                <input
                    type="hidden" 
                    id="actual_defect_<? echo $i; ?>" 
                    name="actual_defect[]"
                    value="">
				<input
					type="hidden" 
					id="hidden_breakdown_defect_<? echo $i; ?>" 
					name="hidden_breakdown_defect[]"
					value="">
				<input
					type="hidden" 
					id="hidden_breakdown_reject_<? echo $i; ?>" 
					name="hidden_breakdown_reject[]"
					value="">
                <input
                    type="hidden" 
                    id="actual_reject_<? echo $i; ?>" 
                    name="actual_reject[]"
                    value=""> 
                <input type="hidden" name="isRescan[]" id="isRescan_<? echo $i; ?>" value="<? echo 0;?>"/>   
        	</td>
        </tr>
	<?php
		$i++;
	}

	exit();
}

if($action=="challan_duplicate_check")
{
	$bundle_no="'".implode("','",explode(",",$data))."'";
	$msg=1;
	
	$bundle_count=count(explode(",",$bundle_no)); $bundle_nos_cond="";
	$bundle_nos_cond=" and b.barcode_no in ($bundle_no)";
	//echo "select a.cutting_qc_no, b.bundle_no from  pro_gmts_delivery_mst a, pro_garments_production_dtls b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bundle_nos_cond";
	$result=sql_select("SELECT a.cutting_qc_no, b.bundle_no from  pro_gmts_cutting_qc_mst a, pro_gmts_cutting_qc_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bundle_nos_cond");

	$datastr="";
	if(count($result)>0)
	{
		foreach ($result as $row)
		{ 
			$msg=2;
			$datastr=$row[csf('bundle_no')]."*".$row[csf('cutting_qc_no')];
		}
	}
	echo rtrim($msg)."_".rtrim($datastr)."_".$search_lot_no;
	exit();
}

if($action=='populate_data_from_barcode')
{
	$exdata=explode("_",$data);
	$barcode_no="'".implode("','",explode(",",$exdata[1]))."'";
	$cutNo=$exdata[0];
	if($cutNo!="") $cutNoCond=" and c.cutting_no='$cutNo'"; else $cutNoCond="";
	
	$data_array=sql_select("SELECT a.sys_number, a.id, a.floor_id, a.location_id, a.production_type, a.working_company_id, a.working_location_id , a.operator_id , a.body_part_ids, a.body_part_type , a.company_id, c.cutting_no, c.job_no, a.production_source, sum(b.production_qnty) as production_qty from pro_gmts_delivery_mst a, pro_garments_production_dtls b, ppl_cut_lay_mst c where b.barcode_no in ($barcode_no) $cutNoCond and a.id=b.delivery_mst_id and b.cut_no=c.cutting_no and a.production_type=51 and b.production_type=51 and a.status_active=1  and b.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.is_deleted=0 group by a.sys_number, a.id, a.floor_id, a.location_id, a.production_type, a.working_company_id, a.working_location_id , a.operator_id , a.body_part_ids, a.company_id, c.cutting_no, c.job_no, a.production_source, a.body_part_type ");
	
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('txt_lot_ratio').value 				= '".$row[csf("cutting_no")]."';\n";
		echo "document.getElementById('txt_job_no').value 					= '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('cbo_company_name').value 			= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_source').value 					= '".$row[csf("production_source")]."';\n";
		//echo "load_drop_down( 'requires/bundle_knitting_qc_controller', ".($row[csf("production_source")])."+'_'+".($row[csf("company_id")]).", 'load_drop_down_knitting_com','knitting_com');";
		
		if ($row[csf("production_source")] == 1) {
			$knitting_com= create_drop_down("cbo_working_company", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0  order by comp.company_name", "id,company_name", 1, "--Select Knit Company--", $row[csf("company_id")], "load_location();", 1);
		} else if ($row[csf("production_source")] == 3) {
			$knitting_com= create_drop_down("cbo_working_company", 130, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--Select Knit Company--", 0, "load_location();");
		} else {
			$knitting_com= create_drop_down("cbo_working_company", 130, $blank_array, "", 1, "--Select Knit Company--", 0, "load_location();");
		}
		
		if ($row[csf("production_source")] == 1)
		{
			$workingloaction=create_drop_down( "cbo_working_location", 130, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='".$row[csf("working_company_id")]."' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "",1 );
			$workingFloor=create_drop_down( "cbo_floor", 130, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='".$row[csf("working_location_id")]."' and production_process in (2) order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",1 );
		}
		else
		{
			$workingloaction= create_drop_down("cbo_working_location", 130, $blank_array, "", 1, "-- Select Location --", 0, "");
			$workingFloor= create_drop_down("cbo_floor", 130, $blank_array, "", 1, "-- Select Floor --", 0, "");
		}
		
		$lc_location=create_drop_down( "cbo_location", 130, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='".$row[csf("company_id")]."' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "",1 );
		
		echo "document.getElementById('location_td').innerHTML = '".$lc_location."';\n";
		echo "document.getElementById('knitting_com').innerHTML = '".$knitting_com."';\n";
		echo "document.getElementById('working_location_td').innerHTML = '".$workingloaction."';\n";
		echo "document.getElementById('floor_td').innerHTML = '".$workingFloor."';\n";
		
		echo "document.getElementById('cbo_working_company').value 			= '".$row[csf("working_company_id")]."';\n";
		
		//echo "load_location(".$row[csf("production_source")].");\n";
		echo "document.getElementById('cbo_working_location').value 		= '".$row[csf("working_location_id")]."';\n";
		
		//echo "load_drop_down( 'requires/bundle_knitting_qc_controller', ".$row[csf("working_location_id")].", 'load_drop_down_floor', 'floor_td' );";
		
		echo "document.getElementById('cbo_floor').value  					= '".($row[csf("floor_id")])."';\n";
		echo "document.getElementById('txt_operator_id').value  			= '".($row[csf("operator_id")])."';\n";
		//echo "load_drop_down( 'requires/bundle_knitting_qc_controller', '".$row[csf('company_id')]."', 'load_drop_down_lc_location', 'location_td' );\n";
		
		echo "document.getElementById('cbo_location').value 					= '".$row[csf("location_id")]."';\n";
		
		if($new_conn!=""){
			$employee_name=return_field_value( "(first_name||' '||middle_name|| '  ' || last_name) as emp_name", "hrm_employee", "id_card_no='".($row[csf("operator_id")])."' and status_active=1 and is_deleted=0", "emp_name",$new_conn);
		}
		else
		{
			$employee_name=return_field_value( "(first_name||' '||middle_name|| '  ' || last_name) as emp_name", "lib_employee", "id_card_no='".($row[csf("operator_id")])."' and status_active=1 and is_deleted=0", "emp_name","");
		}
		
		echo "document.getElementById('txt_operator_name').value 			= '".$employee_name."';\n";
		
		exit();
	}
}

if($action=="populate_bundle_data_rescan")
{
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
	$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name" );
	$data=explode("**",$data);
	$i=$data[1]+1;
	
	//echo $i;die;
	$sql_cut=sql_select("select a.job_no, b.color_id, b.roll_data, a.id, b.id as dtls_id from ppl_cut_lay_mst a, ppl_cut_lay_dtls b
							where a.cutting_no='".$data[3]."' and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0");
	$job_no				=$sql_cut[0][csf("job_no")];
	$color_id			=$sql_cut[0][csf("color_id")];
	$consumption_string	=$sql_cut[0][csf("roll_data")];
	$mst_id				=$sql_cut[0][csf("id")];
	$dtls_id			=$sql_cut[0][csf("dtls_id")];
	list($shortName,$year,$job_prifix)=explode('-',$job_no);

	
	$job_sql=sql_select("select c.short_name as BUYER_NAME, a.po_number as PO_NUMBER, a.id as ID from wo_po_break_down  a,wo_po_details_master b ,lib_buyer c
						 
						 where b.job_no='".$job_no."' and  a.job_no_mst=b.job_no and b.buyer_name=c.id ");
	$jbp_arr=array();
	foreach($job_sql as $jval)
	{
		$jbp_arr["buyer_name"]=$jval["BUYER_NAME"];
		$jbp_arr[$jval["ID"]]=$jval["PO_NUMBER"];
	}
	
	$table_width	=1300;
	$div_width		=$table_width+20;
	$barcode_no="'".implode("','",explode(",",$data[0]))."'";
	if($data[0]!="")
	$barcode_cond=" c.barcode_no in (".$barcode_no.") ";
	
	$total_production_qnty=0;
	$sqlResult =sql_select("select b.* ,a.gmt_item_id, c.machine_id, c.bundle_qty, b.size_qty, c.color_size_break_down_id
									from ppl_cut_lay_dtls a, ppl_cut_lay_bundle b, pro_garments_production_dtls c 
									where $barcode_cond and c.production_type=51 and b.mst_id=$mst_id and b.dtls_id=$dtls_id and c.bundle_no=b.bundle_no and a.id=b.dtls_id and a.color_id=$color_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

	
								
	foreach($sqlResult as $selectResult)
	{
		if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		$total_production_qnty+=$selectResult[csf('production_qnty ')]; 	
	?>
		<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="" > 
            <td width="30" align="center"><?=$i; ?></td>
            <td width="100" align="center"><p><? echo $selectResult[csf('bundle_no')]; ?></p></td>
            <td width="100" align="center"><p><? echo $selectResult[csf('barcode_no')]; ?></p></td>
            <td width="120" align="center" style="word-break:break-all"><p><?php  echo $color_library[$color_id]; ?></p></td>
            <td width="50" align="center"><?php  echo $size_library[$selectResult[csf('size_id')]]; ?></td>
            <td width="70" align="right"><?php  echo $selectResult[csf('size_qty')]; ?></td>
            <td width="70" align="center"> 
            	<input onKeyUp="calculate_defect_qty();" onDblClick="defect_qty_popup(<?=$i; ?>,<?=$selectResult[csf('id')]; ?>,<?=$selectResult[csf('size_qty')]; ?>);" type="text" id="txt_defect_qty_<?=$i; ?>" name="txt_defect_qty[]" style="width:50px;" class="text_boxes_numeric" value="<? //=$selectResult[csf('production_qnty')]; ?>">

            </td>
            <td width="70" align="center"> 
            	<input onKeyUp="calculate_defect_qty()" type="text" id="txt_replace_qty_<? echo $i; ?>" name="txt_replace_qty[]" style="width:50px;" class="text_boxes_numeric" value="<? //=$selectResult[csf('production_qnty')]; ?>">

            </td>
            <td width="70" align="center"></td>
            <td width="50" align="center"><p><? echo $year; ?></p></td>
            <td width="60" align="center"><p><? echo $job_prifix*1; ?></p></td>
            <td width="65" align="center"><?php echo $jbp_arr["buyer_name"]; ?></td>
            <td width="90" align="center"><?php  echo $jbp_arr[$selectResult[csf('order_id')]]; ?></td>
            		
            <td width="100" align="center"><p><?php echo $garments_item[$selectResult[csf('gmt_item_id')]]; ?></p></td>
            <td width="100" align="center"><p><? echo $country_library[$selectResult[csf('country_id')]]; ?></p></td>
             <td >
             	<input 
                	type="button" 
                    value="-" 
                    name="minusButton[]" 
                    id="minusButton_<? echo $i;  ?>" 
                    style="width:30px" 
                    class="formbuttonplasminus" 
                    onClick="fnc_minusRow('<? echo $i;  ?>')"/>
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
                    value="<?php echo $selectResult[csf('color_size_break_down_id')]; ?>">
            

                <input
                    type="hidden" 
                    id="txt_dtls_id_<? echo $i; ?>" 
                    name="txt_dtls_id[]"
                    style="width:80px;"
                    class="text_boxes"
                    value="">

               	<input
                    type="hidden" 
                    id="trId_<? echo $i; ?>" 
                    name="trId[]"
                    value="<?php echo $i; ?>">

                <input
                    type="hidden" 
                    id="actual_defect_<? echo $i; ?>" 
                    name="actual_reject[]"
                    value="">
				<input
					type="hidden" 
					id="hidden_breakdown_defect_<? echo $i; ?>" 
					name="hidden_breakdown_defect[]"
					value="">
				<input
					type="hidden" 
					id="hidden_breakdown_reject_<? echo $i; ?>" 
					name="hidden_breakdown_reject[]"
					value="">
                <input
                    type="hidden" 
                    id="actual_reject_<? echo $i; ?>" 
                    name="actual_reject[]"
                    value="">  
                <input type="hidden" name="isRescan[]" id="isRescan_<? echo $i; ?>" value="<? echo 1;?>"/>  
        	</td>
        </tr>
	<?php
		$i++;
	}

	exit();
}

if($action=="show_dtls_listview_bundle_rescan")
{

	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
	$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name" );
	$machine_library=return_library_array( "select id,machine_no from lib_machine_name where category_id=1 and status_active=1 and is_deleted=0 and is_locked=0  order by seq_no", "id", "machine_no" );

	$data=explode("_",$data);

	$sql_cut=sql_select("SELECT a.job_no, b.color_id, b.roll_data, a.id, b.id as dtls_id from ppl_cut_lay_mst a, ppl_cut_lay_dtls b where a.cutting_no='".$data[0]."' and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 ");
	$job_no				=$sql_cut[0][csf("job_no")];
	$color_id			=$sql_cut[0][csf("color_id")];
	$consumption_string	=$sql_cut[0][csf("roll_data")];
	$mst_id				=$sql_cut[0][csf("id")];
	$dtls_id			=$sql_cut[0][csf("dtls_id")];
	list($shortName,$year,$job_prifix)=explode('-',$job_no);
	//echo $consumption_string;die;

	
	$job_sql=sql_select("SELECT c.short_name as BUYER_NAME, a.po_number as PO_NUMBER, a.id as ID from wo_po_break_down  a,wo_po_details_master b ,lib_buyer c where b.job_no='".$job_no."' and a.job_no_mst=b.job_no   and b.buyer_name=c.id
					");
	$jbp_arr=array();
	foreach($job_sql as $jval)
	{
		$jbp_arr["buyer_name"]=$jval["BUYER_NAME"];
		$jbp_arr[$jval["ID"]]=$jval["PO_NUMBER"];
	}
	

	$table_width=1380;
	$div_width=$table_width+20;

	
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
                    <th width="30"  >SL</th>
                    <th width="100" >Bundle No</th>
                    <th width="100" >Barcode No</th>
                    <th width="120" > G. Color</th>
                    <th width="50"  >Size</th>
                    <th width="70"  >Bundle Qty. (Pcs)</th>
                    <th width="70"  >Defect Qty (Pcs)</th>
                    <th width="70"  >Reject Qty (Pcs)</th>
                    <th width="70"  >Replace Qty. (Pcs)</th>
                    <th width="70"  >QC Qty. (Pcs)</th>
                    <th width="50"  >Year</th>
                    <th width="60"  >Job No</th>
                    <th width="65"  >Buyer</th>
                    <th width="90"  >Order No</th>
                    <th width="100" >Gmts. Item</th>
                    <th width="100" >Country</th>
                    <th 			>Action</th>
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
			$barcode_no="'".implode("','",explode(",",$data[1]))."'";
			if($data[1]!="")
			$barcode_cond=" c.barcode_no in (".$barcode_no.") ";

			
			$sqlResult =sql_select("SELECT b.bundle_no, b.barcode_no, b.order_id, b.size_id, b.country_id, a.gmt_item_id, sum(c.production_qnty) as production_qnty, c.color_size_break_down_id, b.id as bundle_id from ppl_cut_lay_dtls a, ppl_cut_lay_bundle b, pro_garments_production_dtls c where $barcode_cond and c.production_type=51 and b.mst_id=$mst_id and b.dtls_id=$dtls_id and c.bundle_no=b.bundle_no and a.id=b.dtls_id and a.color_id=$color_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.bundle_no,b.barcode_no,b.order_id,b.size_id,b.country_id , a.gmt_item_id, c.color_size_break_down_id,b.id");
			foreach ($sqlResult as $key => $value) 
			{
				$input_qty_arr[$val[csf('barcode_no')]]+=$val[csf('production_qnty')];
				$input_barcode_arr[]=$val[csf('barcode_no')];
				$total_input[$val[csf('barcode_no')]]+=$val[csf('production_qnty')];
			}

			// ============== getting receive bundle ======================
			$receive_sql="SELECT a.barcode_no, sum(a.production_qnty+a.alter_qty+a.spot_qty) as qty,c.id as bundle_id from ppl_cut_lay_bundle c, pro_garments_production_dtls a, wo_po_color_size_breakdown b where c.barcode_no=a.barcode_no and a.production_type=52 and a.color_size_break_down_id=b.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and $barcode_cond and c.mst_id=$mst_id and c.dtls_id=$dtls_id and b.color_number_id=$color_id 
			group by a.barcode_no ,c.id
			order by a.barcode_no asc";
			// echo $receive_sql;die;
			$receive_result = sql_select($receive_sql);
			foreach ($receive_result as $row)
			{
				$output_qty_arr[$row[csf('barcode_no')]]+=$row[csf('qty')];
				$output_barcode_arr[]=$row[csf('barcode_no')];
				$total_output[$row[csf('barcode_no')]]+=$row[csf('qty')];
			}
			

			foreach($sqlResult as $selectResult)
			{
				if ($i%2==0)  $bgcolor="#E9F3FF";
                else $bgcolor="#FFFFFF";
                $production_qnty = $selectResult[csf('production_qnty')] - $output_qty_arr[$selectResult[csf('barcode_no')]];
 			?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="" > 
                    <td width="30" align="center"><? echo $i; ?></td>
                    <td width="100" align="center"><p><? echo $selectResult[csf('bundle_no')]; ?></p></td>
                    <td width="100" align="center"><p><? echo $selectResult[csf('barcode_no')]; ?></p></td>
                    <td width="120" align="center" style="word-break:break-all"><p><?php  echo $color_library[$color_id]; ?></p></td>
                    <td width="50" align="center"><?php  echo $size_library[$selectResult[csf('size_id')]]; ?></td>
                    <td width="70" align="right"><?php  echo $production_qnty; ?></td>
                    <td width="70" align="center"> 
                    	<input 
                    		onkeyup="calculate_defect_qty()"
                    		onDblClick="defect_qty_popup(<? echo $i; ?>,<? echo $selectResult[csf('bundle_id')]; ?>,<? echo $production_qnty; ?>)"						
							type="text" 
							id="txt_defect_qty_<? echo $i; ?>" 
							name="txt_defect_qty[]"
							style="width:50px;"
							class="text_boxes_numeric"
							value="<?php  //echo $selectResult[csf('production_qnty')]; ?>">

                    </td>
                    <td width="70" align="center"> 
                    	<input 
                    		onkeyup="calculate_reject_qty()"
                    		onDblClick="reject_qty_popup(<? echo $i; ?>,<? echo $selectResult[csf('id')]; ?>,<? echo $selectResult[csf('size_qty')]; ?>)"
							type="text" 
							id="txt_reject_qty_<? echo $i; ?>" 
							name="txt_reject_qty[]"
							style="width:50px;"
							class="text_boxes_numeric"
							value="<?php  //echo $selectResult[csf('production_qnty')]; ?>">

                    </td>
                    <td width="70" align="center"> 
                    	<input
                    		onkeyup="calculate_defect_qty()"
							type="text" 
							id="txt_replace_qty_<? echo $i; ?>" 
							name="txt_replace_qty[]"
							style="width:50px;"
							class="text_boxes_numeric"
							value="<?php  //echo $selectResult[csf('production_qnty')]; ?>">

                    </td>
                    <td width="70" align="center"><?php  echo $production_qnty; ?></td>
                    <td width="50" align="center"><p><? echo $year; ?></p></td>
                    <td width="60" align="center"><p><? echo $job_prifix*1; ?></p></td>
                    <td width="65" align="center"><?php echo $jbp_arr["buyer_name"]; ?></td>
                    <td width="90" align="center"><?php  echo $jbp_arr[$selectResult[csf('order_id')]]; ?></td>
                    		
                    <td width="100" align="center"><p><?php echo $garments_item[$selectResult[csf('gmt_item_id')]]; ?></p></td>
                    <td width="100" align="center"><p><? echo $country_library[$selectResult[csf('country_id')]]; ?></p></td>
                     <td >
                     	<input 
                        	type="button" 
                            value="-" 
                            name="minusButton[]" 
                            id="minusButton_<? echo $i;  ?>" 
                            style="width:30px" 
                            class="formbuttonplasminus" 
                            onClick="fnc_minusRow('<? echo $i;  ?>')"/>
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
                            value="<?php echo $selectResult[csf('color_size_break_down_id')]; ?>">
                  

                        <input
                            type="hidden" 
                            id="txt_dtls_id_<? echo $i; ?>" 
                            name="txt_dtls_id[]"
                            style="width:80px;"
                            class="text_boxes"
                            value="">

                       	<input
                            type="hidden" 
                            id="trId_<? echo $i; ?>" 
                            name="trId[]"
                            value="<?php echo $i; ?>">
                        <input
                            type="hidden" 
                            id="actual_defect_<? echo $i; ?>" 
                            name="actual_defect[]"
                            value="">
						<input
                            type="hidden" 
                            id="hidden_breakdown_defect_<? echo $i; ?>" 
                            name="hidden_breakdown_defect[]"
                            value="">
						<input
                            type="hidden" 
                            id="hidden_breakdown_reject_<? echo $i; ?>" 
                            name="hidden_breakdown_reject[]"
                            value="">
                        <input
                            type="hidden" 
                            id="actual_reject_<? echo $i; ?>" 
                            name="actual_reject[]"
                            value="">
                    	<input type="hidden" name="isRescan[]" id="isRescan_<? echo $i; ?>" value="<? echo 1;?>"/>
                	</td>
                </tr>
            <?php
                $i++;
                $total_bundle_qty+=$production_qnty;
			}
			?>
            </tbody>
            <tfoot>
            	<tr id="bundle_footer">
                    <th   colspan="5" > Total</th>
                    <th width="70"  id="total_bundle_qty"><?php echo $total_bundle_qty; ?></th>
                    <th width="70"  id="total_defect_qty"><?php //echo $total_size_qty; ?></th>
                    <th width="70"  id="total_reject_qty"><?php //echo $total_size_qty; ?></th>
                
                 
                    <th width="70" id="total_replace_qty"><?php echo number_format($grand_total_consumption,4,".","");?></th>
                    <th width="70" id="total_qc_qty"><?php echo $total_bundle_qty; ?></th>
                    <th width="50"  ></th>
                    <th width="60"  ></th>
                    <th width="65"  ></th>
                    <th width="90"  ></th>
                    <th width="100" ></th>
                    <th width="100" ></th>
                    <th id=""></th>
                </tr>
            </tfoot>
		</table>
	</div>
	<?
	exit();
}

if($action=="show_dtls_listview_bundle_rescan2")
{

	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
	$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name" );
	$machine_library=return_library_array( "select id,machine_no from lib_machine_name where category_id=1 and status_active=1 and is_deleted=0 and is_locked=0  order by seq_no", "id", "machine_no" );

	$data=explode("**",$data);
	$sql_cut=sql_select("SELECT a.job_no, b.color_id, b.roll_data, a.id, b.id as dtls_id from ppl_cut_lay_mst a, ppl_cut_lay_dtls b,ppl_cut_lay_bundle c where c.barcode_no='".$data[0]."' and a.id=b.mst_id and c.mst_id=a.id and b.id=c.dtls_id and a.status_active=1 and a.is_deleted=0 ");
	$job_no				=$sql_cut[0][csf("job_no")];
	$color_id			=$sql_cut[0][csf("color_id")];
	$consumption_string	=$sql_cut[0][csf("roll_data")];
	$mst_id				=$sql_cut[0][csf("id")];
	$dtls_id			=$sql_cut[0][csf("dtls_id")];
	list($shortName,$year,$job_prifix)=explode('-',$job_no);
	//echo $consumption_string;die;

	
	$job_sql=sql_select("SELECT c.short_name as BUYER_NAME, a.po_number as PO_NUMBER, a.id as ID from wo_po_break_down  a,wo_po_details_master b ,lib_buyer c where b.job_no='".$job_no."' and a.job_id=b.id   and b.buyer_name=c.id
					");
	$jbp_arr=array();
	foreach($job_sql as $jval)
	{
		$jbp_arr["buyer_name"]=$jval["BUYER_NAME"];
		$jbp_arr[$jval["ID"]]=$jval["PO_NUMBER"];
	}
	

	$table_width=1380;
	$div_width=$table_width+20;
	
	$i=1;	
	$total_production_qnty=0;
	$barcode_no="'".implode("','",explode(",",$data[0]))."'";
	if($data[0]!="")
	$barcode_cond=" c.barcode_no in (".$barcode_no.") ";

	
	$sqlResult =sql_select("SELECT b.id, b.bundle_no, b.barcode_no, b.order_id, b.size_id, b.country_id , a.gmt_item_id, sum(c.production_qnty) as production_qnty, c.color_size_break_down_id from ppl_cut_lay_dtls a, ppl_cut_lay_bundle b, pro_garments_production_dtls c where $barcode_cond and c.production_type=51 and b.mst_id=$mst_id and b.dtls_id=$dtls_id and c.bundle_no=b.bundle_no and a.id=b.dtls_id and a.color_id=$color_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.bundle_no,b.barcode_no,b.order_id,b.size_id,b.country_id , a.gmt_item_id, c.color_size_break_down_id");
	//echo "SELECT b.bundle_no,b.barcode_no,b.order_id,b.size_id,b.country_id , a.gmt_item_id, sum(c.production_qnty) as production_qnty, c.color_size_break_down_id from ppl_cut_lay_dtls a, ppl_cut_lay_bundle b, pro_garments_production_dtls c where $barcode_cond and c.production_type=51 and b.mst_id=$mst_id and b.dtls_id=$dtls_id and c.bundle_no=b.bundle_no and a.id=b.dtls_id and a.color_id=$color_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.bundle_no,b.barcode_no,b.order_id,b.size_id,b.country_id , a.gmt_item_id, c.color_size_break_down_id";
	foreach ($sqlResult as $key => $value) 
	{
		$input_qty_arr[$val[csf('barcode_no')]]+=$val[csf('production_qnty')];
		$input_barcode_arr[]=$val[csf('barcode_no')];
		$total_input[$val[csf('barcode_no')]]+=$val[csf('production_qnty')];
	}

	// ============== getting receive bundle ======================
	$receive_sql="SELECT a.barcode_no, sum(a.production_qnty+a.alter_qty+a.spot_qty) as qty,c.id as bundle_id from ppl_cut_lay_bundle c, pro_garments_production_dtls a, wo_po_color_size_breakdown b where c.barcode_no=a.barcode_no and a.production_type=52 and a.color_size_break_down_id=b.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and $barcode_cond and c.mst_id=$mst_id and c.dtls_id=$dtls_id and b.color_number_id=$color_id 
	group by a.barcode_no ,c.id
	order by a.barcode_no asc";
	// echo $receive_sql;die;
	$receive_result = sql_select($receive_sql);
	foreach ($receive_result as $row)
	{
		$output_qty_arr[$row[csf('barcode_no')]]+=$row[csf('qty')];
		$output_barcode_arr[]=$row[csf('barcode_no')];
		$total_output[$row[csf('barcode_no')]]+=$row[csf('qty')];
	}
	

	foreach($sqlResult as $selectResult)
	{
		if ($i%2==0)  $bgcolor="#E9F3FF";
        else $bgcolor="#FFFFFF";
        $production_qnty = $selectResult[csf('production_qnty')] - $output_qty_arr[$selectResult[csf('barcode_no')]];
			?>
        <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="" > 
            <td width="30" align="center"><? echo $i; ?></td>
            <td width="100" align="center"><p><? echo $selectResult[csf('bundle_no')]; ?></p></td>
            <td width="100" align="center"><p><? echo $selectResult[csf('barcode_no')]; ?></p></td>
            <td width="120" align="center" style="word-break:break-all"><p><?php  echo $color_library[$color_id]; ?></p></td>
            <td width="50" align="center"><?php  echo $size_library[$selectResult[csf('size_id')]]; ?></td>
            <td width="70" align="right"><?php  echo $production_qnty; ?></td>
            <td width="70" align="center"> 
            	<input 
            		onkeyup="calculate_defect_qty()"            	
					onDblClick="defect_qty_popup(<? echo $i; ?>,<? echo $selectResult[csf('id')]; ?>,<? echo $production_qnty; ?>)"
					type="text" 
					id="txt_defect_qty_<? echo $i; ?>" 
					name="txt_defect_qty[]"
					style="width:50px;"
					class="text_boxes_numeric"
					value="<?php  //echo $selectResult[csf('production_qnty')]; ?>">

            </td>
            <td width="70" align="center"> 
            	<input 
            		onkeyup="calculate_reject_qty()"
            		onDblClick="reject_qty_popup(<? echo $i; ?>,<? echo $selectResult[csf('id')]; ?>,<? echo $selectResult[csf('size_qty')]; ?>)"
					type="text" 
					id="txt_reject_qty_<? echo $i; ?>" 
					name="txt_reject_qty[]"
					style="width:50px;"
					class="text_boxes_numeric"
					value="<?php  //echo $selectResult[csf('production_qnty')]; ?>">

            </td>
            <td width="70" align="center"> 
            	<input
            		onkeyup="calculate_defect_qty()"
					type="text" 
					id="txt_replace_qty_<? echo $i; ?>" 
					name="txt_replace_qty[]"
					style="width:50px;"
					class="text_boxes_numeric"
					value="<?php  //echo $selectResult[csf('production_qnty')]; ?>">

            </td>
            <td width="70" align="center"><?php  echo $production_qnty; ?></td>
            <td width="50" align="center"><p><? echo $year; ?></p></td>
            <td width="60" align="center"><p><? echo $job_prifix*1; ?></p></td>
            <td width="65" align="center"><?php echo $jbp_arr["buyer_name"]; ?></td>
            <td width="90" align="center"><?php  echo $jbp_arr[$selectResult[csf('order_id')]]; ?></td>
            		
            <td width="100" align="center"><p><?php echo $garments_item[$selectResult[csf('gmt_item_id')]]; ?></p></td>
            <td width="100" align="center"><p><? echo $country_library[$selectResult[csf('country_id')]]; ?></p></td>
             <td >
             	<input 
                	type="button" 
                    value="-" 
                    name="minusButton[]" 
                    id="minusButton_<? echo $i;  ?>" 
                    style="width:30px" 
                    class="formbuttonplasminus" 
                    onClick="fnc_minusRow('<? echo $i;  ?>')"/>
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
                    value="<?php echo $selectResult[csf('color_size_break_down_id')]; ?>">
          

                <input
                    type="hidden" 
                    id="txt_dtls_id_<? echo $i; ?>" 
                    name="txt_dtls_id[]"
                    style="width:80px;"
                    class="text_boxes"
                    value="">

               	<input
                    type="hidden" 
                    id="trId_<? echo $i; ?>" 
                    name="trId[]"
                    value="<?php echo $i; ?>">
                <input
                    type="hidden" 
                    id="actual_defect_<? echo $i; ?>" 
                    name="actual_defect[]"
                    value="">
				<input
					type="hidden" 
					id="hidden_breakdown_defect_<? echo $i; ?>" 
					name="hidden_breakdown_defect[]"
					value="">
				<input
					type="hidden" 
					id="hidden_breakdown_reject_<? echo $i; ?>" 
					name="hidden_breakdown_reject[]"
					value="">
                <input
                    type="hidden" 
                    id="actual_reject_<? echo $i; ?>" 
                    name="actual_reject[]"
                    value="">
            	<input type="hidden" name="isRescan[]" id="isRescan_<? echo $i; ?>" value="<? echo 1;?>"/>
        	</td>
        </tr>
    <?php
        $i++;
        $total_bundle_qty+=$production_qnty;
	}
			
	exit();
}



if($action=="qty_rescan_check")
{
	//echo $data;die;
	$data=explode("__",$data);
	$bundle_no="'".implode("','",explode(",",$data[0]))."'";
	$msg='';

	$bundle_count=count(explode(",",$bundle_no)); $bundle_nos_cond=""; $recbundle_nos_cond="";
	if($db_type==2 && $bundle_count>400)
	{
		$recbundle_nos_cond=" and (";
		$bundle_nos_cond=" and (";
		$bundleArr=array_chunk(explode(",",$bundle_no),399);
		foreach($bundleArr as $bundleNos)
		{
			$bundleNos=implode(",",$bundleNos);
			$recbundle_nos_cond.=" b.barcode_no in($bundleNos) or ";
			$bundle_nos_cond.=" b.barcode_no in($bundleNos) or ";
		}
		$recbundle_nos_cond=chop($recbundle_nos_cond,'or ');
		$recbundle_nos_cond.=")";

		$bundle_nos_cond=chop($bundle_nos_cond,'or ');
		$bundle_nos_cond.=")";
	}
	else
	{
		$recbundle_nos_cond=" and b.barcode_no in ($bundle_no)";
		$bundle_nos_cond=" and b.barcode_no in ($bundle_no)";
	}

	$bundle_qty=return_field_value("sum(b.size_qty) as issue_qty","ppl_cut_lay_bundle b","b.status_active=1 and b.is_deleted=0 $bundle_nos_cond","issue_qty");

	$issue_qty=return_field_value("sum(b.production_qnty) as issue_qty","pro_garments_production_dtls b","b.production_type=51 and  b.status_active=1 and b.is_deleted=0 $bundle_nos_cond","issue_qty");

	$receive_qty=return_field_value("sum(b.production_qnty) as issue_qty","pro_garments_production_dtls b","b.production_type=52 and b.status_active=1 and b.is_deleted=0 $bundle_nos_cond group by b.bundle_no","issue_qty");
	// echo "$bundle_qty**$issue_qty==$receive_qty<br>";
	if($receive_qty=='' && $issue_qty=='' ) $msg=3;
	else if($receive_qty=='' && $issue_qty>0 ) $msg=4;
	else if(($issue_qty-$receive_qty)>0 && $receive_qty>0 ) $msg=1;
	else if(($issue_qty-$receive_qty)==0 && $receive_qty>0 )
	{

		$result=sql_select("SELECT a.cutting_qc_no,b.bundle_no from pro_gmts_cutting_qc_mst a,pro_garments_production_dtls b where a.id=b.delivery_mst_id and b.production_type=52 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $recbundle_nos_cond group by a.cutting_qc_no,b.bundle_no");
		$datastr="";
		if(count($result)>0)
		{
			foreach ($result as $row)
			{
				$msg=2;
				$datastr=$row[csf('bundle_no')]."##".$row[csf('cutting_qc_no')];
			}
		}
	}

	/*$sql_mst_data=sql_select("SELECT a.sewing_line, c.bundle_no
	from pro_gmts_delivery_mst a, pro_garments_production_dtls c where a.id=c.delivery_mst_id and c.production_type=4 and a.status_active=1 and a.is_deleted=0 and is_rescan=1 and c.status_active=1 and c.is_deleted=0 $recbundle_nos_cond");
	$kk=0;
	if(count($sql_mst_data)>0)
	{
		foreach ($sql_mst_data as $row)
		{
			$line=$row[csf('sewing_line')];
			if(count($sql_mst_data)>0)
			{
				$kk++;
				if($datastrmst!='') $datastrmst .="**";
					$datastrmst .=$row[csf('bundle_no')]."*".$row[csf('sewing_line')];
			}
		}
	}*/
	//else $line=$row[csf('sewing_line')];

	echo ($msg)."_".($datastr)."_".$datastrmst."_".$kk."_".$line;
	//echo rtrim($msg)."_".rtrim($datastr)."_".$datastrmst;
	exit();
}


if($action=="load_mst_data")
{
	$data=explode("__",$data);
	//echo $data;die;
 	$ex_data = "'".implode("','",explode(",",$data[0]))."'";	

	$bundle_count=count(explode(",",$ex_data)); $bundle_nos_cond="";
	if($db_type==2 && $bundle_count>400)
	{
		$bundle_nos_cond=" and (";
		$bundleArr=array_chunk(explode(",",$ex_data),399);
		foreach($bundleArr as $bundleNos)
		{
			$bundleNos=implode(",",$bundleNos);
			$bundle_nos_cond.=" c.barcode_no in($bundleNos) or ";
		}
		$bundle_nos_cond=chop($bundle_nos_cond,'or ');
		$bundle_nos_cond.=")";
	}
	else
	{
		$bundle_nos_cond=" and c.barcode_no in ($ex_data)";
	}

	$txt_order_no = "%".trim($ex_data[0])."%";
	$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");

	$sql_mst_data=sql_select("SELECT a.company_id, a.location_id,a.floor_id, a.serving_company,  a.service_location, a.production_source, a.cutting_qc_date
	from pro_gmts_cutting_qc_mst a, pro_garments_production_dtls c where a.id=c.delivery_mst_id $bundle_nos_cond $str_cond  and c.production_type=52 and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0 group by a.company_id, a.location_id,a.floor_id, a.serving_company,  a.service_location, a.production_source, a.cutting_qc_date");

	if(count($sql_mst_data)>0)
	{
		foreach($sql_mst_data as $val)
		{

		//	$cutting_no=return_field_value("cut_no", "pro_garments_production_dtls", "barcode_no in ($bundle_nos)");

			echo "load_drop_down( 'requires/bundle_knitting_qc_controller', '".$val[csf('company_id')]."', 'load_drop_down_lc_location', 'location_td' );\n";

			echo "load_drop_down( 'requires/bundle_knitting_qc_controller', '".$val[csf('serving_company')]."', 'load_drop_down_location', 'working_location_td' );\n";

			echo "load_drop_down( 'requires/bundle_knitting_qc_controller','".$val[csf('service_location')]."', 'load_drop_down_floor', 'floor_td');\n";



			if($val[csf('production_source')]==1) {$serv_comp=$company_arr[$val[csf('serving_company')]]; }
			else { $serv_comp=$supplier_arr[$val[csf('serving_company')]];}
			$location=$location_arr[$val[csf('location_id')]];
			$floor=$floor_arr[$val[csf('floor_id')]];
			echo "$('#cbo_source').val('".$val[csf('production_source')]."');\n";
			echo "$('#cbo_working_company').val('".$serv_comp."');\n";
			echo "$('#cbo_company_name').val(".$val[csf('company_id')].");\n";
			echo "$('#cbo_floor').val(".$val[csf('floor_id')].");\n";
			echo "$('#cbo_working_location').val(".$val[csf('service_location')].");\n";
			echo "$('#txt_qc_date').val('".change_date_format($val[csf('cutting_qc_date')])."');\n";

			/*echo "$('#cbo_company_name').attr('disabled','disabled');\n";
			echo "$('#cbo_source').attr('disabled','disabled');\n";
			echo "$('#cbo_emb_company_show').attr('disabled','disabled');\n";
			echo "$('#cbo_emb_company').attr('disabled','disabled');\n";
			//echo "$('#txt_location_name').val('".$location."');\n";
			echo "$('#txt_floor_name').attr('disabled','disabled');\n";
			echo "$('#txt_organic').attr('disabled','disabled');\n";
			echo "$('#cbo_floor').attr('disabled','disabled');\n";
			echo "$('#cbo_location').attr('disabled','disabled');\n";
			echo "$('#cbo_line_no').attr('disabled','disabled');\n";*/
		}
	}
	else
		echo "alert('All Bundle must be under Selected Company, Working Company, Location, Floor. Please Check');\n";

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
	                    		$sql_com="select id, company_name from lib_company comp where status_active =1 and is_deleted=0 order by company_name";
								
									
								echo create_drop_down( "cbo_company_name",160, $sql_com,"id,company_name", 1, "--- Select Company ---", $selected, "load_drop_down( 'bundle_knitting_qc_controller', this.value, 'load_drop_down_location_hrm','location_td_hrm');","","","","","","",$new_conn );  
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
		                	<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_location_name').value+'_'+document.getElementById('cbo_division_name').value+'_'+document.getElementById('cbo_dept_name').value+'_'+document.getElementById('cbo_section_name').value+'_'+document.getElementById('src_emp_code').value, 'create_emp_search_list_view', 'search_div','bundle_knitting_qc_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
		                </td>
		            </tr> 
	           </table>
	           <div style="width:100%; margin-top:10px; margin-left:3px" id="search_div" align="left"></div>
			</fieldset>
		</form>
	</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
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
	
	if($new_conn!="") $table_name = "hrm_employee"; else $table_name = "lib_employee";

 	//$sql_cond="";
	if( $company!=0 )  $company=" and company_id=$company"; else  $company="";
	if( $location!=0 )  $location=" and location_id=$location"; else  $location="";
	if( $division!=0 )  $division=" and division_id=$division"; else  $division="";
	if( $department!=0 )  $department=" and department_id=$department"; else  $department="";
	if( $section!=0 )  $section=" and section_id=$section"; else  $section="";
	if( $emp_code!=0 )  $emp_code=" and emp_code=$emp_code"; else  $emp_code="";
	
	/*if($db_type==2 || $db_type==1 )
	{
      $sql = "select emp_code,id_card_no,(first_name||' '||middle_name|| '  ' || last_name) as emp_name,designation_id, company_id, location_id, division_id,department_id,section_id from hrm_employee where status_active=1 and is_deleted=0 $company $location $division $department $section $line_no $emp_code";
    }*/
	
	$sql = "select emp_code, id_card_no, (first_name||' '||middle_name|| '  ' || last_name) as emp_name,designation_id, company_id, location_id, division_id,department_id,section_id from $table_name where status_active=1 and is_deleted=0 $company $location $division $department $section $line_no $emp_code";
	
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name',$new_conn);
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name',$new_conn);
	$division_arr=return_library_array( "select id, division_name from lib_division",'id','division_name',$new_conn);
	$department_arr=return_library_array( "select id, department_name from lib_department",'id','department_name',$new_conn);
	$section_arr=return_library_array( "select id, section_name from lib_section",'id','section_name',$new_conn);
	$designation_arr=return_library_array( "select id, custom_designation from lib_designation",'id','custom_designation',$new_conn);
	$arr=array(2=>$designation_arr,3=>$line_no_arr,3=>$company_arr,4=>$location_arr,5=>$division_arr,6=>$department_arr,7=>$section_arr);
		
	echo  create_list_view("list_view", "Emp Code,ID Card,Employee Name,Designation,Company,Location,Division,Department,Section", "80,140,120,110,110,110,110,110,80","1040","260",0, $sql, "js_set_value", "emp_code,id_card_no,emp_name", "", 1, "0,0,0,designation_id,company_id,location_id,division_id,department_id,section_id", $arr, "emp_code,id_card_no,emp_name,designation_id,company_id,location_id,division_id,department_id,section_id", "employee_info_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0,0,0,0',"","",$new_conn) ;
	exit();
}

if ($action=="load_drop_down_floor")
{
 	echo create_drop_down( "cbo_floor", 130, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and production_process in (2) order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",1 );
	exit();     	 
} 

if ($action == "load_drop_down_knitting_com") {
	$data = explode("_", $data);
	$company_id = $data[1];
	//$company_id
	if ($data[0] == 1) {
		echo create_drop_down("cbo_working_company", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0  order by comp.company_name", "id,company_name", 1, "--Select Knit Company--", $company_id, "load_location();", 1);
	} else if ($data[0] == 3) {
		echo create_drop_down("cbo_working_company", 130, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--Select Knit Company--", 0, "load_location();");
	} else {
		echo create_drop_down("cbo_working_company", 130, $blank_array, "", 1, "--Select Knit Company--", 0, "load_location();");
	}
	exit();
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_working_location", 130, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down('requires/bundle_knitting_qc_controller', this.value, 'load_drop_down_floor', 'floor_td' );",1 );
	exit();   
}

if ($action=="load_drop_down_lc_location")
{
	echo create_drop_down( "cbo_location", 130, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "",1 );
	exit();   
}

if($action=="bundle_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	list($shortName,$ryear,$lot_prifix)=explode('-',$lot_ratio);
	if($ryear=="") 	$ryear=date("Y",time());
	else 			$ryear=("20$ryear")*1;
	//echo $company_id;die;
	?>
	<script>
		function check_all_data() {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length-1; 
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
			if( $("#hidden_lot_ratio").val()!="" && $("#hidden_lot_ratio").val()!=$('#txt_individual_name' + str).val() ) {
				alert("Lot Ratio Mixed Not Allow.Previous Selected Lot Ratio "+$('#txt_individual_name' + str).val());
				return;
			}

			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_individual' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual' + str).val() );
				$('#hidden_lot_ratio').val( $('#txt_individual_name' + str).val() );	
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				
				if(selected_id.length==0 && $('#hidden_lot_ratio_pre').val()=="")
					$('#hidden_lot_ratio').val('');
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
			//return;
			parent.emailwindow.hide();
			//alert($('#hidden_bundle_nos').val())
		}
		
		function reset_hide_field()
		{
			//$('#hidden_bundle_nos').val( '' );return;
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
	                    <th class="must_entry_caption">Ratio No</th>
	                    <th>Bundle No</th>
	                    <th>
	                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
	                        <input type="hidden" name="hidden_bundle_nos" id="hidden_bundle_nos"> 
	                    </th>
	                </thead>
	                <tr class="general">
	                	<td>
						<? 
	                        $sql_com="select id, company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name";
	                        echo create_drop_down("cbo_company_name", 140, $sql_com, "id,company_name", 1, "-- Select --", $sessionUnit_id,"",0 );
	                    ?>
	                    </td>
	                    <td align="center"><?=create_drop_down( "cbo_lot_year", 60, $year, '', "", '-- Select --', $ryear, "" ); ?></td>  				
	                    <td align="center"><input type="text" style="width:100px" class="text_boxes" name="txt_job_no" id="txt_job_no" /></td> 				
	                    <td><input type="text" name="txt_lot_no" id="txt_lot_no" style="width:100px" value="<?php if($lot_prifix) echo $lot_prifix*1; ?>" class="text_boxes" /></td>  		
	                    <td>
	                    	<input type="hidden" name="hidden_lot_ratio" value="<?php echo $lot_ratio; ?>" id="hidden_lot_ratio"  />
				            <input type="hidden" name="hidden_lot_ratio_pre" value="<?php echo $lot_ratio; ?>" id="hidden_lot_ratio_pre"  />
				            <input type="text" name="bundle_no" id="bundle_no" style="width:100px" class="text_boxes" />
	                    </td>  		
	            		<td align="center">
	                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_name').value+'_'+document.getElementById('bundle_no').value+'_'+'<? echo trim($bundleNo,','); ?>'+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_lot_no').value+'_'+document.getElementById('cbo_lot_year').value+'_'+'<? echo trim($lot_ratio,','); ?>','create_bundle_search_list_view','search_div','bundle_knitting_qc_controller','setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')" style="width:100px;" />
	                     </td>
	                </tr>
	           </table>
	           <div style="width:100%; margin-top:5px; margin-left:10px" id="search_div" align="left"></div>
			</fieldset>
		</form>
	</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		if($("#hidden_lot_ratio").val()!="")
		{
			show_list_view (document.getElementById('cbo_company_name').value+'_'+document.getElementById('bundle_no').value+'_'+'<? echo trim($bundleNo,','); ?>'+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_lot_no').value+'_'+document.getElementById('cbo_lot_year').value+'_'+'<? echo trim($lot_ratio,','); ?>','create_bundle_search_list_view','search_div','bundle_knitting_qc_controller','setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();');
		}
	</script>
	</html>
	<?
	exit();
}

if($action=="create_bundle_search_list_view")
{
 	$ex_data 				= explode("_",$data);
	$company 				= $ex_data[0];
	$selectedBuldle			="'".implode("','",explode(",",$ex_data[2]))."'";
	$job_no					=$ex_data[3];
	$lot_no					=$ex_data[4];
	$syear 					= substr($ex_data[5],2);
	$full_lot_no			=$ex_data[7];
	
	if(trim($ex_data[1]))	$bundle_no_cond = " and a.bundle_no='".trim($ex_data[1])."'";

	if( trim($ex_data[0])=='' || trim($ex_data[0])==0)
	{
		echo "<h2 style='color:#D00; text-align:center;'>Please Select  Company First. </h2>";
		exit();
	}

	if( trim($ex_data[1])=='' && trim($ex_data[3])==''  && trim($ex_data[4])=='')
	{
		echo "<h2 style='color:#D00; text-align:center;'>Please Select Job No Or  Lot No Or Bundle No. </h2>";
		exit();
	} 

	$cutCon=''; $receiveCon='';
	if ($lot_no != '') 
	{
		$cutCon = " and a.cut_no like'%".$lot_no."%'";
    }
    if ($full_lot_no != '') 
	{
		$cutCon='';
		$cutCon = " and a.cut_no='".$full_lot_no."'";
    }
	
	if($job_no!='') $jobCon=" and b.job_no_mst like '%$job_no%'"; else  $jobCon="";
	if(str_replace("'","",$selectedBuldle)!=="")
		$selected_bundle_cond=" and a.bundle_no not in (".$selectedBuldle.")";

	$scanned_bundle_arr=return_library_array("SELECT a.bundle_no, a.bundle_no from pro_garments_production_mst b, pro_garments_production_dtls a where b.id=a.mst_id and a.production_type=52 and b.production_type=52 and b.status_active=1 and b.is_deleted=0 $bundle_no_cond $cutCon ",'bundle_no','bundle_no');
	foreach(explode(",",$selectedBuldle) as $bn)
	{
		$scanned_bundle_arr[$bn]=$bn;	
	}
	
	if($db_type==2) $group_field="LISTAGG(CAST(a.body_part_ids AS VARCHAR2(100)),',') WITHIN GROUP ( ORDER BY a.id) as body_part_ids"; 
	else if($db_type==0) $group_field="group_concat(distinct a.body_part_ids ) as body_part_ids";
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1000" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="50">Year</th>
            <th width="50">Job No</th>
            <th width="90">Order No</th>
            <th width="150">Gmts Item</th>
            <th width="110">Country</th>
            <th width="150">Color</th>
            <th width="50">Size</th>
            <th width="90">Lot Ratio No</th>
            <th width="90">Bundle No</th>
            <th>Bundle Qty.</th>
        </thead>
	</table>
	<div style="width:1020px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1000" class="rpt_table" id="tbl_list_search">  
        	<?
			$i=1;
			$sql="select b.job_no_mst, a.cut_no, b.color_number_id, b.item_number_id, b.size_number_id, a.bundle_no, b.po_break_down_id, b.country_id, c.size_qty, a.barcode_no, c.mst_id, $group_field
                 from ppl_cut_lay_bundle c, pro_garments_production_dtls a, wo_po_color_size_breakdown b
                 where c.barcode_no=a.barcode_no and a.production_type=51 and a.color_size_break_down_id=b.id $selected_bundle_cond  $jobCon $cutCon $bundle_no_cond and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 
                group by b.job_no_mst, a.cut_no, b.color_number_id, b.item_number_id, b.size_number_id, a.bundle_no, b.po_break_down_id, b.country_id, c.size_qty, a.barcode_no, c.mst_id  
                order by b.job_no_mst, a.cut_no, length(a.bundle_no) asc, a.bundle_no asc";
			// echo $sql;
			$result = sql_select($sql);	
 
			foreach ($result as $val)
			{
				$po_id_arr[$val[csf('po_break_down_id')]] 		=$val[csf('po_break_down_id')];
				$color_id_arr[$val[csf('color_number_id')]] 	=$val[csf('color_number_id')];
				$size_id_arr[$val[csf('size_number_id')]] 		=$val[csf('size_number_id')];
				$country_id_arr[$val[csf('country_id')]] 		=$val[csf('country_id')];
				$cutting_id_arr[$val[csf('mst_id')]] 			=$val[csf('mst_id')];
			}

			$size_arr=return_library_array( "select id, size_name from lib_size where id in (".implode(',', $size_id_arr).")",'id','size_name');
			$color_arr=return_library_array( "select id, color_name from lib_color where id in (".implode(',', $color_id_arr).")", "id", "color_name");
			$country_arr=return_library_array( "select id, country_name from lib_country where id in (".implode(',', $country_id_arr).")",'id','country_name');
			$po_number_arr=return_library_array( "select id, po_number from wo_po_break_down where id in (".implode(',', $po_id_arr).")",'id','po_number');
			$cutting_bodypart_arr=return_library_array( "select id, body_part_string from ppl_cut_lay_mst where id in (".implode(',', $cutting_id_arr).")",'id','body_part_string');

			foreach ($result as $row)
			{  
				if($scanned_bundle_arr[$row[csf('bundle_no')]]=="")
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
					list($shortName,$year,$job)=explode('-',$row[csf('job_no_mst')]);
					if(empty($wet_sheet_bodypart[$row[csf('mst_id')]]))
					{
						$cutting_bodypart_string=$cutting_bodypart_arr[$row[csf('mst_id')]];
						foreach(explode(',', $cutting_bodypart_string) as $bodypart_id)
						{
							if($bodypart_id!=14) $wet_sheet_bodypart[$row[csf('mst_id')]][$bodypart_id]=$time_weight_panel[$bodypart_id];				
						}
					}

					$receive_bodypart_string=$row[csf('body_part_ids')];
					$receive_bodypart_arr=array();
					foreach(explode(',', $receive_bodypart_string) as $rbodypart_id)
					{
						if($rbodypart_id!=14) $receive_bodypart_arr[$rbodypart_id]=$time_weight_panel[$rbodypart_id];				
					}
				
					$non_receive_bodypart = array_diff($wet_sheet_bodypart[$row[csf('mst_id')]], $receive_bodypart_arr);
			
					if(empty($non_receive_bodypart))
					{
					?>
						<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i;?>" onClick="js_set_value(<?=$i; ?>);"> 
							<td width="40"><?=$i; ?>
								<input type="hidden" name="txt_individual" id="txt_individual<?=$i; ?>" value="<?=$row[csf('barcode_no')]; ?>"/>
	                            <input type="hidden" name="txt_individual_name" id="txt_individual_name<?=$i; ?>" value="<?=$row[csf('cut_no')]; ?>"/>
							</td>
							<td width="50" align="center"><p><? echo $year; ?></p></td>
							<td width="50" align="center"><p><? echo $job*1; ?></p></td>
							<td width="90" style="word-break:break-all"><p><? echo $po_number_arr[$row[csf('po_break_down_id')]]; ?></p></td>
							<td width="150" style="word-break:break-all"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
							<td width="110" style="word-break:break-all"><p><? echo $country_arr[$row[csf('country_id')]]; ?></p></td>
							<td width="150" style="word-break:break-all"><p><? echo $color_arr[$row[csf('color_number_id')]]; ?></p></td>
							<td width="50" style="word-break:break-all"><p><? echo $size_arr[$row[csf('size_number_id')]]; ?></p></td>
							<td width="90" style="word-break:break-all"><? echo $row[csf('cut_no')]; ?></td>
							<td width="90" style="word-break:break-all"><? echo $row[csf('bundle_no')]; ?></td>
							<td align="right"><? echo $row[csf('size_qty')]; ?></td>
						</tr>
					<?
						$i++;
					}
				}
			}
        	?>
        </table>
    </div>
    <table width="1000">
        <tr>
            <td align="center" >
               <span style="float:left;"><input type="checkbox" name="check_all" id="check_all" onClick="check_all_data();" />
                    Check / Uncheck All
               </span>
                <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
            </td>
        </tr>
    </table>
	<?	
	exit();	
}

// ==================== for rescan =============================

if($action=="bundle_popup_rescan")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	list($shortName,$ryear,$lot_prifix)=explode('-',$lot_ratio);
	if($ryear=="") 	$ryear=date("Y",time());
	else 			$ryear=("20$ryear")*1;
	//echo $company_id;die;
	?>
	<script>
		var selected_id = new Array();
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str) 
		{

			if( $("#hidden_lot_ratio").val()!="" &&   $("#hidden_lot_ratio").val()!=$('#txt_individual_name' + str).val() ) {
				alert("Lot Ratio Mixed Not Allow.Previous Selected Lot Ratio "+$('#txt_individual_name' + str).val());
				return;
				
			}

			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_individual' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual' + str).val() );
				$('#hidden_lot_ratio').val( $('#txt_individual_name' + str).val() );	
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				
				if(selected_id.length==0 && $('#hidden_lot_ratio_pre').val()=="")
					$('#hidden_lot_ratio').val('');

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
			//return;
			parent.emailwindow.hide();
			//alert($('#hidden_bundle_nos').val())
		}
		
		function reset_hide_field()
		{
			//$('#hidden_bundle_nos').val( '' );return;
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
	                    <th class="must_entry_caption">Ratio No</th>
	                    <th>Bundle No</th>
	                    <th>
                        	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
	                        <input type="hidden" name="hidden_bundle_nos" id="hidden_bundle_nos"> 
	                    </th>
	                </thead>
	                <tr class="general">
	                	<td>
						<? 
	                        $sql_com="select id, company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name";
	                        echo create_drop_down( "cbo_company_name",140, $sql_com,"id,company_name", 1,"-- Select --", $sessionUnit_id,"",0 );
	                    ?>
	                    </td>
	                    <td align="center"><?=create_drop_down( "cbo_lot_year", 60, $year,'', "", '-- Select --',$ryear,"" ); ?></td>  				
	                    <td align="center"><input type="text" style="width:100px" class="text_boxes" name="txt_job_no" id="txt_job_no" /></td> 				
	                    <td><input type="text" name="txt_lot_no" id="txt_lot_no" style="width:100px" value="<?php if($lot_prifix) echo $lot_prifix*1; ?>" class="text_boxes" /></td>
	                    <td>
	                    	 <input type="hidden" name="hidden_lot_ratio" value="<?php echo $lot_ratio; ?>" id="hidden_lot_ratio"  />
				             <input type="hidden" name="hidden_lot_ratio_pre" value="<?php echo $lot_ratio; ?>" id="hidden_lot_ratio_pre"  />
				            <input type="text" name="bundle_no" id="bundle_no" style="width:100px" class="text_boxes" />
	                    </td>  		
	            		<td align="center">
	                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_name').value+'_'+document.getElementById('bundle_no').value+'_'+'<? echo trim($bundleNo,','); ?>'+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_lot_no').value+'_'+document.getElementById('cbo_lot_year').value+'_'+'<? echo trim($lot_ratio,','); ?>','create_bundle_rescan_search_list_view','search_div','bundle_knitting_qc_controller','setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')" style="width:100px;" />
	                     </td>
	                </tr>
	           </table>
	           <div style="width:100%;margin-top:5px; margin-left:10px" id="search_div" align="left"></div>
			</fieldset>
		</form>
	</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		if($("#hidden_lot_ratio").val()!="")
		{
			show_list_view (document.getElementById('cbo_company_name').value+'_'+document.getElementById('bundle_no').value+'_'+'<? echo trim($bundleNo,','); ?>'+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_lot_no').value+'_'+document.getElementById('cbo_lot_year').value+'_'+'<? echo trim($lot_ratio,','); ?>','create_bundle_search_list_view','search_div','bundle_knitting_qc_controller','setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();');
		}
	</script>
	</html>
	<?
	exit();
}

if($action=="create_bundle_rescan_search_list_view")
{
 	$ex_data 				= explode("_",$data);
	$company 				= $ex_data[0];
	$selectedBuldle			="'".implode("','",explode(",",$ex_data[2]))."'";
	$job_no					=$ex_data[3];
	$lot_no					=$ex_data[4];
	$syear 					= substr($ex_data[5],2);
	$full_lot_no			=$ex_data[7];
	
	if(trim($ex_data[1]))	$bundle_no_cond = " and a.bundle_no='".trim($ex_data[1])."'";

	if( trim($ex_data[0])=='' || trim($ex_data[0])==0)
	{
		echo "<h2 style='color:#D00; text-align:center;'>Please Select  Company First. </h2>";
		exit();
	}

	if( trim($ex_data[1])=='' && trim($ex_data[3])==''  && trim($ex_data[4])=='')
	{
		echo "<h2 style='color:#D00; text-align:center;'>Please Select Job No Or  Lot No Or Bundle No. </h2>";
		exit();
	} 
	
	
	
	$cutCon=''; $receiveCon='';
	if ($lot_no != '') 
	{
		$cutCon = " and a.cut_no like'%".$lot_no."%'";
    }
    if ($full_lot_no != '') 
	{
		$cutCon='';
		$cutCon = " and a.cut_no='".$full_lot_no."'";
    }

 
	
	if($job_no!='') 
		$jobCon=" and b.job_no_mst like '%$job_no%'";
	else 
		$jobCon="";
	if(str_replace("'","",$selectedBuldle)!=="")
		$selected_bundle_cond=" and a.bundle_no not in (".$selectedBuldle.")";

	// $scanned_bundle_arr=return_library_array("SELECT a.bundle_no, a.bundle_no from pro_garments_production_mst b, pro_garments_production_dtls a where b.id=a.mst_id and a.production_type=52 and b.production_type=52 and b.status_active=1 and b.is_deleted=0 $bundle_no_cond $cutCon ", 'bundle_no', 'bundle_no'); 
	foreach(explode(",",$selectedBuldle) as $bn)
	{
		$scanned_bundle_arr[$bn]=$bn;	
	}
	
	if($db_type==2) $group_field="LISTAGG(CAST(a.body_part_ids AS VARCHAR2(100)),',') WITHIN GROUP ( ORDER BY a.id) as body_part_ids"; 
	else if($db_type==0) $group_field="group_concat(distinct a.body_part_ids ) as body_part_ids";

	$sql="SELECT b.job_no_mst , a.cut_no, b.color_number_id , b.item_number_id  , b.size_number_id  , a.bundle_no, b.po_break_down_id  , b.country_id , c.size_qty , a.barcode_no, c.mst_id, $group_field from ppl_cut_lay_bundle c, pro_garments_production_dtls a, wo_po_color_size_breakdown b where c.barcode_no=a.barcode_no and a.production_type=51 and a.color_size_break_down_id=b.id $selected_bundle_cond  $jobCon $cutCon $bundle_no_cond and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 group by b.job_no_mst , a.cut_no, b.color_number_id , b.item_number_id  , b.size_number_id  , a.bundle_no, b.po_break_down_id  , b.country_id , c.size_qty , a.barcode_no , c.mst_id order by b.job_no_mst, a.cut_no, length(a.bundle_no) asc, a.bundle_no asc"; 
	// echo $sql;
	$result = sql_select($sql);	

	foreach ($result as $val)
	{
		$po_id_arr[$val[csf('po_break_down_id')]] 		=$val[csf('po_break_down_id')];
		$color_id_arr[$val[csf('color_number_id')]] 	=$val[csf('color_number_id')];
		$size_id_arr[$val[csf('size_number_id')]] 		=$val[csf('size_number_id')];
		$country_id_arr[$val[csf('country_id')]] 		=$val[csf('country_id')];
		$cutting_id_arr[$val[csf('mst_id')]] 			=$val[csf('mst_id')];
	}
	// =====================================================
	$sql_in="SELECT sum(a.production_qnty) as input_qty , a.barcode_no from pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.production_type=51 and a.color_size_break_down_id=b.id $selected_bundle_cond  $jobCon $cutCon $bundle_no_cond and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 group by a.barcode_no"; 
	// echo $sql;
	$in_result = sql_select($sql_in);	

	foreach ($in_result as $val)
	{
		$input_qty_arr[$val[csf('barcode_no')]]+=$val[csf('input_qty')];
		$input_barcode_arr[]=$val[csf('barcode_no')];
		$total_input[$val[csf('barcode_no')]]+=$val[csf('input_qty')];
	}

	$size_arr=return_library_array( "select id, size_name from lib_size where id in (".implode(',', $size_id_arr).")",'id','size_name');
	$color_arr=return_library_array( "select id, color_name from lib_color where id in (".implode(',', $color_id_arr).")", "id", "color_name");
	$country_arr=return_library_array( "select id, country_name from lib_country where id in (".implode(',', $country_id_arr).")",'id','country_name');
	$po_number_arr=return_library_array( "select id, po_number from wo_po_break_down where id in (".implode(',', $po_id_arr).")",'id','po_number');
	$cutting_bodypart_arr=return_library_array( "select id, body_part_string from ppl_cut_lay_mst where id in (".implode(',', $cutting_id_arr).")",'id','body_part_string');

	// ============== getting receive bundle ======================
	$receive_sql="SELECT a.barcode_no, sum(a.production_qnty+a.alter_qty+a.spot_qty) as qty from ppl_cut_lay_bundle c, pro_garments_production_dtls a, wo_po_color_size_breakdown b where c.barcode_no=a.barcode_no and a.production_type=52 and a.color_size_break_down_id=b.id $selected_bundle_cond  $jobCon $cutCon $bundle_no_cond and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 group by b.job_no_mst , a.cut_no, b.color_number_id , b.item_number_id  , b.size_number_id  , a.bundle_no, b.po_break_down_id  , b.country_id , c.size_qty , a.barcode_no , c.mst_id order by b.job_no_mst, a.cut_no, length(a.bundle_no) asc, a.bundle_no asc";
	// echo $receive_sql;die;
	$receive_result = sql_select($receive_sql);
	foreach ($receive_result as $row){
		$output_qty_arr[$row[csf('barcode_no')]]+=$row[csf('qty')];
		$output_barcode_arr[]=$row[csf('barcode_no')];
		$total_output[$row[csf('barcode_no')]]+=$row[csf('qty')];
	}
	
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="840" class="rpt_table">
        <thead>
            <th width="20">SL</th>
            <th width="50">Year</th>
            <th width="50">Job No</th>
            <th width="90">Order No</th>
            <th width="100">Gmts Item</th>
            <th width="100">Country</th>
            <th width="100">Color</th>
            <th width="50">Size</th>
            <th width="90">Lot Ratio No</th>
            <th width="80">Bundle No</th>
            <th>Bundle Qty.</th>
        </thead>
	</table>
	<div style="width:860px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">
        	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="840" class="rpt_table" id="tbl_list_search">  
        	<?
			$i=1;		
			// echo "<pre>";print_r($output_barcode_arr);echo "<pre>";
			foreach ($result as $row)
			{  
				$rescan_qty=$input_qty_arr[$row[csf('barcode_no')]]-$output_qty_arr[$row[csf('barcode_no')]];
				$rescan_qty_total=$total_input[$row[csf('barcode_no')]]-$total_output[$row[csf('barcode_no')]];
				// echo $input_qty_arr[$row[csf('barcode_no')]]."**".$output_qty_arr[$row[csf('barcode_no')]]."<br>";
				if($scanned_bundle_arr[$row[csf('bundle_no')]]=="" && $rescan_qty>0 && $rescan_qty_total>0 && in_array($row[csf('barcode_no')],$output_barcode_arr) )
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
					list($shortName,$year,$job)=explode('-',$row[csf('job_no_mst')]);
					if(empty($wet_sheet_bodypart[$row[csf('mst_id')]]))
					{
						$cutting_bodypart_string=$cutting_bodypart_arr[$row[csf('mst_id')]];
						foreach(explode(',', $cutting_bodypart_string) as $bodypart_id)
						{
							if($bodypart_id!=14) $wet_sheet_bodypart[$row[csf('mst_id')]][$bodypart_id]=$time_weight_panel[$bodypart_id];				
						}
					}

					$receive_bodypart_string=$row[csf('body_part_ids')];
					$receive_bodypart_arr=array();
					foreach(explode(',', $receive_bodypart_string) as $rbodypart_id)
					{
						if($rbodypart_id!=14) $receive_bodypart_arr[$rbodypart_id]=$time_weight_panel[$rbodypart_id];				
					}
				
					$non_receive_bodypart = array_diff($wet_sheet_bodypart[$row[csf('mst_id')]], $receive_bodypart_arr);
					
					// if(empty($non_receive_bodypart))
					// {
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)"> 
							<td width="20"><? echo $i; ?>
								<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
	                            <input type="hidden" name="txt_individual_name" id="txt_individual_name<?php echo $i; ?>" value="<?php echo $row[csf('cut_no')]; ?>"/>
							</td>
							<td width="50" align="center"><p><? echo $year; ?></p></td>
							<td width="50" align="center"><p><? echo $job*1; ?></p></td>
							<td width="90"><p><? echo $po_number_arr[$row[csf('po_break_down_id')]]; ?></p></td>
							<td width="100"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
							<td width="100"><p><? echo $country_arr[$row[csf('country_id')]]; ?></p></td>
							<td width="100"><p><? echo $color_arr[$row[csf('color_number_id')]]; ?></p></td>
							<td width="50"><p><? echo $size_arr[$row[csf('size_number_id')]]; ?></p></td>
							<td width="90"><? echo $row[csf('cut_no')]; ?></td>
							<td width="80"><? echo $row[csf('bundle_no')]; ?></td>
							<td align="right"><? echo $rescan_qty; ?></td>
						</tr>
						<?
						$i++;
					// }
				}
			}
			
        	?>
           
        </table>
    </div>
    <table width="840">
        <tr>
            <td align="center" >
               <span style="float:left;"><input type="checkbox" name="check_all" id="check_all" onClick="check_all_data();" />
                    Check / Uncheck All
               </span>
                <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
            </td>
        </tr>
    </table>
	<?	
	exit();	
}

if ($action == "check_if_barcode_receive")
{
	$barcodeData = '';
	$po_ids_arr = array();
	$po_details_array = array();
	$barcodeDataArr = array();
	$barcodeBuyerArr = array();
	//$sql="select a.id,a.mrr_no
	//from barcode_receive_mst a, barcode_receive_dtls b 
	//WHERE a.id=b.mst_id   and b.status_active=1 and b.is_deleted=0 and b.qrcode =$data";

	$sql="select barcode_no from pro_garments_production_dtls where barcode_no='$data' and production_type=53 and status_active=1 and is_deleted=0";
	//echo $sql;die;
	
	$data_array = sql_select($sql);
	//print_r($data_array);die;
	$roll_details_array = array();
	$barcode_array = array();
	foreach ($data_array as $row)
	{
		$barcodeData = $row[csf("barcode_no")]  ;
		echo $row[csf("barcode_no")];
		exit();
	}
	echo $barcodeData;
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
			
		if($db_type==0) 	 	$year_cond="YEAR(insert_date)"; 
		else if($db_type==2) 	$year_cond="to_char(insert_date,'YYYY')";
		else 					$year_cond="";	

		for($j=1;$j<=$tot_row;$j++)
        {   
            $bundleCheck="bundleNo_".$j;       
            $bundleCheckArr[$$bundleCheck]=$$bundleCheck;       
        }
            
        $bundle 		="'".implode("','",$bundleCheckArr)."'";

        $receive_sql="	SELECT c.barcode_no, c.bundle_no from pro_garments_production_mst a, pro_garments_production_dtls c where a.id=c.mst_id and a.production_type=52 and c.bundle_no in ($bundle)  and c.production_type=52 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 "; 
        //and (c.is_rescan=0 or c.is_rescan is null)

        $receive_result = sql_select($receive_sql);
        foreach ($receive_result as $row)
        {           
            $duplicate_bundle[$row[csf('bundle_no')]]=$row[csf('bundle_no')];
        }

 		$field_array_qc_mst="id, garments_nature, cut_qc_prefix, cut_qc_prefix_no, cutting_qc_no, cutting_no, job_no, location_id, floor_id, company_id, cutting_qc_date, production_source, serving_company, service_location, inspector_id, operator_id, supervisor_id, loss_min, remarks, production_type, inserted_by, insert_date, status_active, is_deleted";

	    $field_array_qc_dtls="id, mst_id, order_id, country_id, color_id, size_id, color_size_id, bundle_no, barcode_no, bundle_qty, reject_qty, defect_qty, replace_qty, qc_pass_qty,is_rescan, inserted_by, insert_date, status_active, is_deleted";

		$field_array_defect="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty, bodypart_id, bundle_no, inserted_by, insert_date";

		$field_array_mst="	id, delivery_mst_id, cut_no, company_id, garments_nature, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, production_date, production_quantity, production_type, entry_break_down_type, remarks, floor_id, inserted_by, insert_date";

		$new_system_id = explode("*", return_next_id_by_sequence("", "pro_gmts_cutting_qc_mst",$con,1,$cbo_company_name,'CQ',0,date("Y",time()),0,0,52,0,0 ));
		$qc_id = return_next_id_by_sequence(  "pro_gmts_cutting_qc_mst_seq",  "pro_gmts_cutting_qc_mst", $con );
		$qc_dtls_id = return_next_id_by_sequence(  "pro_gmts_cutting_qc_dtls_seq",   "pro_gmts_cutting_qc_dtls", $con );
		$dft_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq", "pro_gmts_prod_dft", $con ); 
		$data_arra_cutt_mst="(".$qc_id.",100,'".$new_system_id[1]."',".(int)$new_system_id[2].",'".$new_system_id[0]."',".$txt_lot_ratio.",".$txt_job_no.",".$cbo_location.",".$cbo_floor.",".$cbo_company_name.",".$txt_qc_date.",".$cbo_source.",".$cbo_working_company.",".$cbo_working_location.",".$txt_insp_opr_id.",".$txt_operator_id.",".$hidden_insp_sup_id.",".$txt_loss_min.",".$txt_remarks.",52,".$user_id.",'".$pc_date_time."',1,0)";
		$challan_no=(int)$new_system_id[2];						
		$mstArr=array(); $dtlsArr=array(); $colorSizeArr=array(); $mstIdArr=array(); 

		$defect_id=return_next_id( "id", "ppl_cut_lay_bundle_defect", 1);
		$reject_id=return_next_id( "id", "ppl_cut_lay_bundle_reject", 1);
		
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
			$qcpass_qty 	="qcpass_qty_".$j;
			$defect_qty 	="defect_qty_".$j;
			$reject_qty 	="reject_qty_".$j;
			$replace_qty 	="replace_qty_".$j;
			$actual_defect 	="actual_defect_".$j;
			$actual_reject 	="actual_reject_".$j;
			$breakdown_defect="hidden_breakdown_defect_".$j;
			$breakdown_reject="hidden_breakdown_reject_".$j;
			$isRescan 		="isRescan_".$j;
			if($$isRescan==1 || $$isRescan==2)
			{				
				$mstArr[$$orderId][$$gmtsitemId][$$countryId]+=$$qcpass_qty;
				$colorSizeArr[$$bundleNo] 					=$$orderId."**".$$gmtsitemId."**".$$countryId;
				$dtlsArr[$$bundleNo]['qc_pass'] 			+=$$qcpass_qty;
				$dtlsArr[$$bundleNo]['defect_qty']			+=$$defect_qty;
				$dtlsArr[$$bundleNo]['reject_qty']			+=$$reject_qty;
				$dtlsArr[$$bundleNo]['replace_qty']			+=$$replace_qty;
				if(trim(str_replace("'","",$$actual_reject))!="")
				{
					$dtlsArr[$$bundleNo]['actual_defect']		=$$actual_defect;
				}
				if(trim(str_replace("'","",$$breakdown_defect))!="")
				{
					$dtlsArr[$$bundleNo]['breakdown_defect']	=$$breakdown_defect;
				}
				if(trim(str_replace("'","",$$actual_reject))!="")
				{
					$dtlsArr[$$bundleNo]['actual_reject']		=$$actual_reject;
				}
				$dtlsArrColorSize[$$bundleNo] 				=$$colorSizeId;
				$bundleRescanArr[$$bundleNo]				=$$isRescan;
				$bundleBarcodeArr[$$bundleNo] 				=$$barcodeNo;

				if($data_array_qc_detls!='') $data_array_qc_detls.=",";
					 
				$data_array_qc_detls.="(".$qc_dtls_id.",".$qc_id.",".$$orderId.",".$$countryId.",".$$colorId.",".$$sizeId.",".$$colorSizeId.",'".$$bundleNo."','".$$barcodeNo."','".trim($$qty)."','".$$reject_qty."','".$$defect_qty."','".$$replace_qty."','".$$qcpass_qty."','".$$isRescan."',".$user_id.",'".$pc_date_time."',1,0)";

				$qc_dtls_id = return_next_id_by_sequence(  "pro_gmts_cutting_qc_dtls_seq","pro_gmts_cutting_qc_dtls", $con );
			}
			else
			{
				if($duplicate_bundle[$$bundleNo]=='')
	            {
					$mstArr[$$orderId][$$gmtsitemId][$$countryId]+=$$qcpass_qty;
					$colorSizeArr[$$bundleNo] 					=$$orderId."**".$$gmtsitemId."**".$$countryId;
					$dtlsArr[$$bundleNo]['qc_pass'] 			+=$$qcpass_qty;
					$dtlsArr[$$bundleNo]['defect_qty']			+=$$defect_qty;
					$dtlsArr[$$bundleNo]['reject_qty']			+=$$reject_qty;
					$dtlsArr[$$bundleNo]['replace_qty']			+=$$replace_qty;
					if(trim(str_replace("'","",$$actual_defect))!="")
					{
						$dtlsArr[$$bundleNo]['actual_defect']		=$$actual_defect;
					}
					if(trim(str_replace("'","",$$actual_reject))!="")
					{
						$dtlsArr[$$bundleNo]['actual_reject']		=$$actual_reject;
					}
					$dtlsArrColorSize[$$bundleNo] 				=$$colorSizeId;
					$bundleRescanArr[$$bundleNo]				=$$isRescan;
					$bundleBarcodeArr[$$bundleNo] 				=$$barcodeNo;

					if($data_array_qc_detls!='') $data_array_qc_detls.=",";
						 
					$data_array_qc_detls.="(".$qc_dtls_id.", ".$qc_id.",".$$orderId.",".$$countryId.",".$$colorId.",".$$sizeId.",".$$colorSizeId.",'".$$bundleNo."','".$$barcodeNo."','".trim($$qty)."','".$$reject_qty."','".$$defect_qty."','".$$replace_qty."','".$$qcpass_qty."','".$$isRescan."',".$user_id.",'".$pc_date_time."',1,0)";

					$qc_dtls_id = return_next_id_by_sequence(  "pro_gmts_cutting_qc_dtls_seq","pro_gmts_cutting_qc_dtls", $con );
				}
			}
		
			$breakdown_defect_arr=explode("__",$$breakdown_defect);		
			$field_array_breakdown_defect="id,ppl_cut_lay_bundle_mst_id,defect_id,defect_name,body_part_id,no_of_defect,inserted_by,insert_date";
			for($d=0;$d<count($breakdown_defect_arr); $d++){
				$breakdown_arr=explode("_",$breakdown_defect_arr[$d]);

				if( trim($data_array_breakdown_defect)!="") $data_array_breakdown_defect.=",";
				$data_array_breakdown_defect.="(".$defect_id.",".$breakdown_arr[0].",".$breakdown_arr[1].",'".$breakdown_arr[2]."',".$breakdown_arr[3].",".$breakdown_arr[4].",".$user_id.",'".$pc_date_time."')";
				$defect_id=$defect_id+1;
			}

			$breakdown_reject_arr=explode("__",$$breakdown_reject);			
			$field_array_breakdown_reject="id,ppl_cut_lay_bundle_mst_id,reject_id,body_part_id,no_of_reJect,inserted_by,insert_date";
			for($d=0;$d<count($breakdown_reject_arr); $d++){
				$breakdown_arr=explode("_",$breakdown_reject_arr[$d]);
					// echo "<pre>";
					// print_r($breakdown_arr)."<br>";
				if( trim($data_array_breakdown_reject)!="") $data_array_breakdown_reject.=",";
					$data_array_breakdown_reject.="(".$reject_id.",".$breakdown_arr[0].",".$breakdown_arr[1].",".$breakdown_arr[2].",".$breakdown_arr[3].",".$user_id.",'".$pc_date_time."')";

				$reject_id=$reject_id+1;
			}
			//  echo "10**insert into ppl_cut_lay_bundle_reject($field_array_breakdown_reject)values".$data_array_breakdown_reject;die;
		}
	
		// echo "10**$data_array_qc_detls";die();
		foreach($mstArr as $orderId=>$orderData)
		{
			foreach($orderData as $gmtsItemId=>$gmtsItemIdData)
			{
				foreach($gmtsItemIdData as $countryId=>$qty)
				{
					$id= return_next_id_by_sequence(  "pro_gar_production_mst_seq", "pro_garments_production_mst", $con );
					if($data_array_mst!="") $data_array_mst.=",";
					$data_array_mst.="(".$id.",".$qc_id.",".$txt_lot_ratio.",".$cbo_company_name.",100,'".$challan_no."',".$orderId.",".$gmtsItemId.",".$countryId.",".$cbo_source.",".$cbo_working_company.",".$cbo_location.",".$txt_qc_date.",".$qty.",52,3,".$txt_remarks.",".$cbo_floor.",".$user_id.",'".$pc_date_time."')";
					$mstIdArr[$orderId][$gmtsItemId][$countryId]=$id;
					//$id = $id+1;
				}
			}
		}
		
		$field_array_dtls="	id, delivery_mst_id, mst_id, production_type, color_size_break_down_id, production_qnty, cut_no, bundle_no, barcode_no, reject_qty, replace_qty,defect_qty, is_rescan";
		
		foreach($dtlsArr as $bundle_no=>$bundle_data)
		{
			$colorSizedData 		=explode("**",$colorSizeArr[$bundle_no]);
			$gmtsMstId 				=$mstIdArr[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[2]];
			if($data_array_dtls!="") $data_array_dtls.=",";

			$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );

			$data_array_dtls.= "(".$dtls_id.",".$qc_id.",".$gmtsMstId.",52,'".$dtlsArrColorSize[$bundle_no]."','".$bundle_data['qc_pass']."',".$txt_lot_ratio.",'".$bundle_no."','".$bundleBarcodeArr[$bundle_no]."','".$bundle_data['reject_qty']."','".$bundle_data['replace_qty']."','".$bundle_data['defect_qty']."','".$bundleRescanArr[$bundle_no]."')"; 
			$field_array_defect="id, mst_id, production_type,  po_break_down_id, defect_type_id, defect_point_id, defect_qty, bodypart_id, bundle_no, inserted_by, insert_date";

			// ========= for defect
			$rls=0;
			if($bundle_data['actual_defect']!="")
			{
				$actual_defect_info=explode("**",$bundle_data['actual_defect']); 
				for($rls=0;$rls<count($actual_defect_info); $rls++)
				{
					$bundle_defect_info=explode("*",$actual_defect_info[$rls]);	 
					$defectPointId=$bundle_defect_info[0];
					$defect_qty=$bundle_defect_info[1];
					$defect_bodypart=$bundle_defect_info[2];
					if($defectPointId!="" && $defect_qty!="" && $defect_bodypart!="")
					{
						if( trim($data_array_defect)!="") $data_array_defect.=",";
						$dft_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con );
					
						$data_array_defect.="(".$dft_id.",".$gmtsMstId.",52,".$colorSizedData[0].",3,".$defectPointId.",'".$defect_qty."','".$defect_bodypart."','".$bundle_no."',".$user_id.",'".$pc_date_time."')";
						//$dft_id++;
					}
				}
			}

			// ============ for reject
			$rls=0;
			if($bundle_data['actual_reject']!="")
			{
				$actual_reject_info=explode("**",$bundle_data['actual_reject']); 
				for($rls=0;$rls<count($actual_reject_info); $rls++)
				{
					$bundle_reject_info=explode("*",$actual_reject_info[$rls]);	
					$rejectPointId=$bundle_reject_info[0];
					$reject_qty=$bundle_reject_info[1];
					$reject_bodypart=$bundle_reject_info[2];
					if($rejectPointId!="" && $reject_qty!="" && $reject_bodypart!="")
					{
						if( trim($data_array_reject)!="") $data_array_reject.=",";
						$dft_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con ); 
					
						$data_array_reject.="(".$dft_id.",".$gmtsMstId.",52,".$colorSizedData[0].",4,".$rejectPointId.",'".$reject_qty."','".$reject_bodypart."','".$bundle_no."',".$user_id.",'".$pc_date_time."')";
						//$dft_id++;
					}
				}
			}
		}
		$rID_mst=sql_insert("pro_gmts_cutting_qc_mst",$field_array_qc_mst,$data_arra_cutt_mst,1);
		$rID_dtls = 1;
		$rID_dtls=sql_insert("pro_gmts_cutting_qc_dtls",$field_array_qc_dtls,$data_array_qc_detls,0);
		$rID=sql_insert("pro_garments_production_mst",$field_array_mst,$data_array_mst,1);
		$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array_dtls,$data_array_dtls,1);
		$rID_defect_id=sql_insert("ppl_cut_lay_bundle_defect",$field_array_breakdown_defect,$data_array_breakdown_defect,1);
		$rID_reject_id=sql_insert("ppl_cut_lay_bundle_reject",$field_array_breakdown_reject,$data_array_breakdown_reject,1);
		$defectQ=1;
		if($data_array_defect!="")
		{
			//echo $data_array_defect;die;
			$defectQ=sql_insert("pro_gmts_prod_dft",$field_array_defect,$data_array_defect,1);
		}
		$rejectQ=1;
		if($data_array_reject!="")
		{
			//echo $data_array_defect;die;
			$rejectQ=sql_insert("pro_gmts_prod_dft",$field_array_defect,$data_array_reject,1);
		}
		// echo "10**insert into pro_garments_production_mst($field_array_mst)values".$data_array_mst;die;
		// echo "10**insert into pro_garments_production_dtls($field_array_dtls)values".$data_array_dtls;die;
		// echo "10**insert into pro_gmts_prod_dft($field_array_defect)values".$data_array_defect;die;
		//$bundlerID=sql_insert("pro_cut_delivery_color_dtls",$field_array_bundle,$data_array_bundle,1);
		//  echo "10**".$rID_mst."**".$rID."**".$dtlsrID."**".$rID_dtls."**".$defectQ."**".$rejectQ."**".$rID_defect_id."**".$rID_reject_id;die;
	
		if($db_type==0)
		{  
			if($rID_mst && $rID_dtls && $rID && $dtlsrID && $defectQ )
			{
				mysql_query("COMMIT");  
				echo "0**".$qc_id."**".str_replace("'","",$new_system_id[0]);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==1 || $db_type==2 )
		{
			if($rID_mst && $rID_dtls && $rID && $dtlsrID && $defectQ )
			{
				oci_commit($con); 
				echo "0**".$qc_id."**".str_replace("'","",$new_system_id[0]);
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
		$data_array_delivery="".$txt_issue_date."*".$user_id."*'".$pc_date_time."'";
	
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
        $receive_sql="SELECT c.barcode_no,c.bundle_no from pro_garments_production_mst a,pro_garments_production_dtls c where a.id=c.mst_id and a.production_type=52 and c.bundle_no  in ($bundle)  and c.production_type=52 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.delivery_mst_id!=$mst_id and c.delivery_mst_id!=$mst_id and (c.is_rescan=0 or c.is_rescan is null)"; 
        $receive_result = sql_select($receive_sql);
        foreach ($receive_result as $row)
        {           
            $duplicate_bundle[$row[csf('bundle_no')]]=$row[csf('bundle_no')];
        }
		//$non_delete_arr=production_validation($mst_id,'3_1');
		//$issue_data_arr=production_data($mst_id,'2_1');

		$field_array_qc_mst="cutting_qc_date*operator_id*inspector_id*supervisor_id*loss_min*remarks*updated_by*update_date";
		$field_array_qc_dtls="id, mst_id, order_id, country_id, color_id, size_id, color_size_id, bundle_no, barcode_no, bundle_qty, defect_qty, reject_qty, replace_qty, qc_pass_qty, is_rescan, inserted_by, insert_date, status_active, is_deleted";
		$field_array_defect="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty, bodypart_id, bundle_no, inserted_by, insert_date";
		$field_array_mst="id, delivery_mst_id, cut_no, company_id, garments_nature, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, production_date, production_quantity, production_type, entry_break_down_type, remarks, floor_id, inserted_by, insert_date";
		$field_array_dtls="id, delivery_mst_id, mst_id, production_type, color_size_break_down_id, production_qnty, cut_no, bundle_no, barcode_no, reject_qty, replace_qty, defect_qty, is_rescan";

		$data_arra_cutt_mst=$txt_qc_date."*".$txt_operator_id."*".$txt_insp_opr_id."*".$hidden_insp_sup_id."*".$txt_loss_min."*".$txt_remarks."*".$user_id."*'".$pc_date_time."'";
		$mstArr=array(); $dtlsArr=array(); $colorSizeArr=array(); $mstIdArr=array();  $data_array_breakdown_reject=$data_array_breakdown_defect="";

		$defect_id=return_next_id( "id", "ppl_cut_lay_bundle_defect", 1 ) ;
		$reject_id=return_next_id( "id", "ppl_cut_lay_bundle_reject", 1 ) ;
		$rID_defect_id=$rID_reject_id=1;
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
			$qcpass_qty 	="qcpass_qty_".$j;
			$defect_qty 	="defect_qty_".$j;
			$reject_qty 	="reject_qty_".$j;
			$replace_qty 	="replace_qty_".$j;
			$actual_defect 	="actual_defect_".$j;
			$actual_reject 	="actual_reject_".$j;
			$breakdown_defect="hidden_breakdown_defect_".$j;
			$breakdown_reject="hidden_breakdown_reject_".$j;
			$isRescan 		="isRescan_".$j;

			if($$isRescan==1 || $$isRescan==2)
			{
				$mstArr[$$orderId][$$gmtsitemId][$$countryId]+=$$qcpass_qty;
				$colorSizeArr[$$bundleNo] 					=$$orderId."**".$$gmtsitemId."**".$$countryId;
				$dtlsArr[$$bundleNo]['qc_pass'] 			+=$$qcpass_qty;
				$dtlsArr[$$bundleNo]['defect_qty']			+=$$defect_qty;
				$dtlsArr[$$bundleNo]['reject_qty']			+=$$reject_qty;
				$dtlsArr[$$bundleNo]['replace_qty']			+=$$replace_qty;
				if(trim(str_replace("'","",$$actual_defect))!="")
				{
					$dtlsArr[$$bundleNo]['actual_defect']		=$$actual_defect;
				}
				if(trim(str_replace("'","",$$actual_reject))!="")
				{
					$dtlsArr[$$bundleNo]['actual_reject']		=$$actual_reject;
				}
				$dtlsArrColorSize[$$bundleNo] 				=$$colorSizeId;
				$bundleRescanArr[$$bundleNo]				=$$isRescan;
				$bundleBarcodeArr[$$bundleNo] 				=$$barcodeNo;
				$deleted_bundle_arr[$$bundleNo] 			=$$bundleNo;
				if($data_array_qc_detls!='') $data_array_qc_detls.=",";
				$qc_dtls_id = return_next_id_by_sequence(  "pro_gmts_cutting_qc_dtls_seq","pro_gmts_cutting_qc_dtls", $con );	 
				$data_array_qc_detls.="(".$qc_dtls_id.",".$mst_id.",".$$orderId.",".$$countryId.",".$$colorId.",".$$sizeId.",".$$colorSizeId.",'".$$bundleNo."','".$$barcodeNo."','".trim($$qty)."','".$$defect_qty."','".$$reject_qty."','".$$replace_qty."','".$$qcpass_qty."','".$$isRescan."',".$user_id.",'".$pc_date_time."',1,0)";
			}
			else
			{
				if($duplicate_bundle[$$bundleNo]=='')
	            {
					$mstArr[$$orderId][$$gmtsitemId][$$countryId]+=$$qcpass_qty;
					$colorSizeArr[$$bundleNo] 					=$$orderId."**".$$gmtsitemId."**".$$countryId;
					$dtlsArr[$$bundleNo]['qc_pass'] 			+=$$qcpass_qty;
					$dtlsArr[$$bundleNo]['defect_qty']			+=$$defect_qty;
					$dtlsArr[$$bundleNo]['reject_qty']			+=$$reject_qty;
					$dtlsArr[$$bundleNo]['replace_qty']			+=$$replace_qty;
					if(trim(str_replace("'","",$$actual_defect))!="")
					{
						$dtlsArr[$$bundleNo]['actual_defect']		=$$actual_defect;
					}
					if(trim(str_replace("'","",$$actual_reject))!="")
					{
						$dtlsArr[$$bundleNo]['actual_reject']		=$$actual_reject;
					}
					$dtlsArrColorSize[$$bundleNo] 				=$$colorSizeId;
					$bundleRescanArr[$$bundleNo]				=$$isRescan;
					$bundleBarcodeArr[$$bundleNo] 				=$$barcodeNo;
					$deleted_bundle_arr[$$bundleNo] 			=$$bundleNo;
					if($data_array_qc_detls!='') $data_array_qc_detls.=",";
					$qc_dtls_id = return_next_id_by_sequence(  "pro_gmts_cutting_qc_dtls_seq","pro_gmts_cutting_qc_dtls", $con );	 
					$data_array_qc_detls.="(".$qc_dtls_id.",".$mst_id.",".$$orderId.",".$$countryId.",".$$colorId.",".$$sizeId.",".$$colorSizeId.",'".$$bundleNo."','".$$barcodeNo."','".trim($$qty)."','".$$defect_qty."','".$$reject_qty."','".$$replace_qty."','".$$qcpass_qty."','".$$isRescan."',".$user_id.",'".$pc_date_time."',1,0)";
				}
			}
			
			if(trim(str_replace("'","",$$breakdown_defect))!="")
			{
				$breakdown_defect_arr=explode("__",$$breakdown_defect);		
				$field_array_breakdown_defect="id,ppl_cut_lay_bundle_mst_id,defect_id,defect_name,body_part_id,no_of_defect,inserted_by,insert_date";
				for($d=0;$d<count($breakdown_defect_arr); $d++){
					$breakdown_arr=explode("_",$breakdown_defect_arr[$d]);
					
					if( trim($data_array_breakdown_defect)!="") $data_array_breakdown_defect.=",";
						$data_array_breakdown_defect.="(".$defect_id.",".$breakdown_arr[0].",".$breakdown_arr[1].",'".$breakdown_arr[2]."',".$breakdown_arr[3].",".$breakdown_arr[4].",	".$user_id.",'".$pc_date_time."')";
					$defect_id=$defect_id+1;
	
					$rIDdefect_id = sql_delete("ppl_cut_lay_bundle_defect","status_active*is_deleted","0*1",'ppl_cut_lay_bundle_mst_id',$breakdown_arr[0],1);
				}
			}

			if(trim(str_replace("'","",$$breakdown_reject))!="")
			{
				$breakdown_reject_arr=explode("__",$$breakdown_reject);			
				$field_array_breakdown_reject="id,ppl_cut_lay_bundle_mst_id,reject_id,body_part_id,no_of_reJect,inserted_by,insert_date";
				for($d=0;$d<count($breakdown_reject_arr); $d++){
	
					$breakdown_arr=explode("_",$breakdown_reject_arr[$d]);
						/*echo "<pre>";
						print_r($breakdown_arr)."<br>";*/
					if( trim($data_array_breakdown_reject)!="") $data_array_breakdown_reject.=",";
						$data_array_breakdown_reject.="(".$reject_id.",".$breakdown_arr[0].",".$breakdown_arr[1].",".$breakdown_arr[2].",".$breakdown_arr[3].",".$user_id.",'".$pc_date_time."')";
	
					$reject_id=$reject_id+1;
					$rIDreject_id = sql_delete("ppl_cut_lay_bundle_reject","status_active*is_deleted","0*1",'ppl_cut_lay_bundle_mst_id',$breakdown_arr[0],1);
				}
			}
			// echo "10**insert into ppl_cut_lay_bundle_reject($field_array_breakdown_reject)values".$data_array_breakdown_reject;die;
		}
		//print_r($dtlsArr);die;
		$unique_mst_arr=array();
		foreach($mstArr as $orderId=>$orderData)
		{
			foreach($orderData as $gmtsItemId=>$gmtsItemIdData)
			{
				foreach($gmtsItemIdData as $countryId=>$qc_qty)
				{
					$id= return_next_id_by_sequence(  "pro_gar_production_mst_seq", "pro_garments_production_mst", $con );
					if($data_array_mst!="") $data_array_mst.=",";
					$data_array_mst.="(".$id.",".$mst_id.",".$txt_lot_ratio.",".$cbo_company_name.",".$garments_nature.",'".$challan_no."',".$orderId.",".$gmtsItemId.",".$countryId.",".$cbo_source.",".$cbo_working_company.",".$cbo_location.",".$txt_qc_date.",".$qc_qty.",52,3,".$txt_remarks.",".$cbo_floor.",".$user_id.",'".$pc_date_time."')";
					$mstIdArr[$orderId][$gmtsItemId][$countryId]=$id;
				}
			}
		}
		
		foreach($dtlsArr as $bundle_no=>$bundle_data)
		{
			$colorSizedData 		=explode("**",$colorSizeArr[$bundle_no]);
			$gmtsMstId 				=$mstIdArr[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[2]];
			if($data_array_dtls!="") $data_array_dtls.=",";

			$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );

			$data_array_dtls.= "(".$dtls_id.",".$mst_id.",".$gmtsMstId.",52,'".$dtlsArrColorSize[$bundle_no]."','".$bundle_data['qc_pass']."',".$txt_lot_ratio.",'".$bundle_no."','".$bundleBarcodeArr[$bundle_no]."','".$bundle_data['reject_qty']."','".$bundle_data['replace_qty']."','".$bundle_data['defect_qty']."','".$bundleRescanArr[$bundle_no]."')"; 
			$rls=0;
			if($bundle_data['actual_defect']!="")
			{
				$actual_defect_info=explode("**",$bundle_data['actual_defect']); 
				for($rls=0;$rls<count($actual_defect_info); $rls++)
				{
					$bundle_defect_info =explode("*",$actual_defect_info[$rls]);
					$defectPointId 		=$bundle_defect_info[0];
					$defect_qty 		=$bundle_defect_info[1];
					$defect_bodypart 	=$bundle_defect_info[2];
					if($defectPointId!="" && $defect_qty!="" && $defect_bodypart!="" && $gmtsMstId!="")
					{
						$dft_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con );			
						if( trim($data_array_defect)!="") $data_array_defect.=",";				

						$data_array_defect.="(".$dft_id.",".$gmtsMstId.",52,".$colorSizedData[0].",3,".$defectPointId.",'".$defect_qty."','".$defect_bodypart."','".$bundle_no."',".$user_id.",'".$pc_date_time."')";
					}
				}
			}
			// ============ for reject
			$rls=0;
			/*echo "10**";
			print_r($bundle_data['actual_reject']);*/
			if($bundle_data['actual_reject']!="")
			{
				$actual_reject_info=explode("**",$bundle_data['actual_reject']); 
				for($rls=0;$rls<count($actual_reject_info); $rls++)
				{
					$bundle_reject_info =explode("*",$actual_reject_info[$rls]);
					$rejectPointId 		=$bundle_reject_info[0];
					$reject_qty 		=$bundle_reject_info[1];
					$reject_bodypart 	=$bundle_reject_info[2];
					if($rejectPointId!="" && $reject_qty!="" && $reject_bodypart!="" && $gmtsMstId!="")
					{
						$dft_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con );			
						if( trim($data_array_reject)!="") $data_array_reject.=",";
						
						$data_array_reject.="(".$dft_id.",".$gmtsMstId.",52,".$colorSizedData[0].",4,".$rejectPointId.",'".$reject_qty."','".$reject_bodypart."','".$bundle_no."',".$user_id.",'".$pc_date_time."')";
					}
				}
			}
		}
	
		$delete = execute_query("update pro_garments_production_mst set status_active=0,is_deleted=1,updated_by=$user_id,update_date='".$pc_date_time."' WHERE delivery_mst_id=$mst_id and production_type=52 and status_active=1 and is_deleted=0");
		$delete_dtls = execute_query("update pro_garments_production_dtls set status_active=0,is_deleted=1 WHERE delivery_mst_id=$mst_id and production_type=52 and status_active=1 and is_deleted=0");
		$delete_qc = execute_query("update pro_gmts_cutting_qc_dtls set status_active=0,is_deleted=1,updated_by=$user_id,update_date='".$pc_date_time."' WHERE mst_id=$mst_id and status_active=1 and is_deleted=0");
		$delete_defect = execute_query("update pro_gmts_prod_dft set status_active=0,is_deleted=1,updated_by=$user_id,update_date='".$pc_date_time."' WHERE bundle_no in ('".implode("','", $deleted_bundle_arr)."') and production_type=52 and status_active=1 and is_deleted=0");
		//echo "10**"."update pro_gmts_prod_dft set status_active=0,is_deleted=1,updated_by=$user_id,update_date='".$pc_date_time."' WHERE bundle_no in ('".implode("','", $deleted_bundle_arr)."') and production_type=52 and status_active=1 and is_deleted=0"; die;
	
		$rID_mst_qc=sql_update("pro_gmts_cutting_qc_mst",$field_array_qc_mst,$data_arra_cutt_mst,"id",$mst_id,1);
		$rID_dtls=sql_insert("pro_gmts_cutting_qc_dtls",$field_array_qc_dtls,$data_array_qc_detls,0);
		$rID=sql_insert("pro_garments_production_mst",$field_array_mst,$data_array_mst,1);
		$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array_dtls,$data_array_dtls,1);
		
		if($data_array_breakdown_defect!="")
		{
			$rID_defect_id=sql_insert("ppl_cut_lay_bundle_defect",$field_array_breakdown_defect,$data_array_breakdown_defect,1);
		}
		if($data_array_breakdown_reject!="")
		{
			$rID_reject_id=sql_insert("ppl_cut_lay_bundle_reject",$field_array_breakdown_reject,$data_array_breakdown_reject,1);
		}
		
		//echo "10**insert into ppl_cut_lay_bundle_reject($field_array_breakdown_reject)values".$data_array_breakdown_reject;die;
		
		$defectQ=1;
		if($data_array_defect!="")
		{
			// echo "10**insert into pro_gmts_prod_dft($field_array_defect)values".$data_array_defect;die;	
			$defectQ=sql_insert("pro_gmts_prod_dft",$field_array_defect,$data_array_defect,1);
		}
		$rejectQ=1;
		if($data_array_reject!="")
		{
			//echo "10**insert into pro_gmts_prod_dft($field_array_defect)values".$data_array_defect;die;	
			$rejectQ=sql_insert("pro_gmts_prod_dft",$field_array_defect,$data_array_reject,1);
		}
		// echo "10**insert into pro_garments_production_dtls($field_array_dtls)values".$data_array_dtls;die;
		//echo "10**".bulk_update_sql_statement( "pro_gmts_knitting_issue_dtls", "id", $field_array_color_dtls, $data_array_color_dtls, $color_dtls_id_arr );die;	
		//echo "10**".$rID_mst_qc .'--'. $rID_dtls .'--'. $rID .'--'. $dtlsrID .'--'. $defectQ."--".$delete.'--'. $delete_dtls .'--'. $delete_qc."--".$delete_defect."--".$rID_defect_id."--".$rID_reject_id; oci_rollback($con);die;
		//echo "10**".$dtlsrID;oci_rollback($con);die;
		

		if($db_type==0)
		{  
			if($rID_mst_qc && $rID_dtls && $rID && $dtlsrID && $defectQ  && $delete && $delete_dtls && $delete_qc && $delete_defect && $rID_defect_id && $rID_reject_id)
			{ 
				mysql_query("COMMIT");  
				echo "1**".$mst_id."**".str_replace("'","",$txt_system_no)."**".implode(',',$non_delete_arr);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==1 || $db_type==2 )
		{
			if($rID_mst_qc && $rID_dtls && $rID && $dtlsrID && $defectQ  && $delete && $delete_dtls && $delete_qc && $delete_defect && $rID_defect_id && $rID_reject_id)
			{
				oci_commit($con); 
				echo "1**".$mst_id."**".str_replace("'","",$txt_system_no)."**".implode(',',$non_delete_arr);
				 
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
		
		$rID = sql_delete("pro_garments_production_mst","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'id ',$txt_mst_id,1);
		$dtlsrID = sql_delete("pro_garments_production_dtls","status_active*is_deleted","0*1",'mst_id',$txt_mst_id,1);
		$mst_id=str_replace("'","",$txt_system_id);
		
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

$new_conn=integration_params(2);
//print_r($new_conn);

if($action=='populate_data_from_qc')
{
	if($new_conn!=""){
		$emp_arr=return_library_array( "select id_card_no, (first_name||' '||middle_name|| ' ' || last_name) as emp_name from hrm_employee",'id_card_no','emp_name',$new_conn);
	}
	else
	{
		$emp_arr=return_library_array( "select id_card_no, (first_name||' '||middle_name|| ' ' || last_name) as emp_name from lib_employee",'id_card_no','emp_name',"");
	}
	//print_r($emp_arr);
	$data_array=sql_select("SELECT id, cut_qc_prefix, cut_qc_prefix_no, cutting_qc_no, cutting_no, job_no, company_id, cutting_qc_date, status_active, is_deleted, location_id, floor_id, production_source, serving_company, remarks, service_location, operator_id, inspector_id, supervisor_id,loss_min from pro_gmts_cutting_qc_mst where id=$data and status_active=1 and is_deleted=0 order by id desc");

	$tot_loss_min = return_field_value("sum(loss_min)","pro_gmts_cutting_qc_mst","cutting_qc_date='".$data_array[0][csf(cutting_qc_date)]."' and inspector_id='".$data_array[0][csf(inspector_id)]."'");
	
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('txt_system_no').value 				= '".$row[csf("cutting_qc_no")]."';\n";
		echo "document.getElementById('txt_system_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_lot_ratio').value 				= '".$row[csf("cutting_no")]."';\n";
		echo "document.getElementById('txt_job_no').value 					= '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('cbo_company_name').value 			= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_source').value 					= '".$row[csf("production_source")]."';\n";
		echo "load_drop_down( 'requires/bundle_knitting_qc_controller', ".($row[csf("production_source")])."+'_'+".($row[csf("company_id")]).", 'load_drop_down_knitting_com','knitting_com');";
		
		echo "document.getElementById('cbo_working_company').value 			= '".$row[csf("serving_company")]."';\n";
		
		echo "load_location(".$row[csf("production_source")].");\n";
		echo "document.getElementById('cbo_working_location').value 		= '".$row[csf("service_location")]."';\n";
		echo "load_drop_down( 'requires/bundle_knitting_qc_controller', ".$row[csf("service_location")].", 'load_drop_down_floor', 'floor_td' );";
		echo "document.getElementById('cbo_floor').value  					= '".($row[csf("floor_id")])."';\n";
		echo "load_drop_down( 'requires/bundle_knitting_qc_controller', '".$row[csf('company_id')]."', 'load_drop_down_lc_location', 'location_td' );\n";

		echo "document.getElementById('cbo_location').value 				= '".$row[csf("location_id")]."';\n";
		echo "document.getElementById('txt_insp_opr_id').value 				= '".$row[csf("inspector_id")]."';\n";
		echo "document.getElementById('hidden_insp_sup_id').value 			= '".$row[csf("supervisor_id")]."';\n";
		echo "document.getElementById('txt_operator_id').value 				= '".$row[csf("operator_id")]."';\n";
		echo "document.getElementById('txt_loss_min').value 				= '".$row[csf("loss_min")]."';\n"; 
		echo "document.getElementById('txt_qc_date').value 				    = '".change_date_format($row[csf("cutting_qc_date")])."';\n"; 
		echo "document.getElementById('txt_show_loss_min').value 			= '".$tot_loss_min."';\n"; 
		
		echo "document.getElementById('txt_operator_name').value 			= '".$emp_arr[$row[csf("operator_id")]]."';\n"; 
		echo "document.getElementById('txt_insp_opr_name').value 			= '".$emp_arr[$row[csf("inspector_id")]]."';\n"; 
		echo "document.getElementById('txt_insp_sup_name').value 			= '".$emp_arr[$row[csf("supervisor_id")]]."';\n"; 
		exit();
	}
}

if($action=="get_tot_loss_min")
{
	$dataEx = explode("**", $data);
	$date = date('d-M-Y',strtotime($dataEx[1]));
	$tot_loss_min = return_field_value("sum(loss_min)","pro_gmts_cutting_qc_mst","cutting_qc_date='".$date."' and inspector_id='".$dataEx[0]."'");

	echo "document.getElementById('txt_show_loss_min').value = '".$tot_loss_min."';\n"; 
}

if($action=='populate_data_from_issue')
{
	$data_array=sql_select("select a.sys_number, a.company_id , a.location_id , a.production_type , a.production_source, a.serving_company , a.working_location_id, a.working_company_id, a.body_part , a.floor_id, a.challan_no , a.remarks , b.cut_no, a.production_source, c.job_no_mst, a.operator_id, a.id from pro_gmts_delivery_mst  a, pro_garments_production_mst  b, wo_po_break_down c where a.id=3173 and a.id=b.delivery_mst_id and b.po_break_down_id=c.id and a.production_type=50 and b.production_type=50 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ");
	
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('txt_challan_no').value 				= '".$row[csf("sys_number")]."';\n";
		echo "document.getElementById('txt_lot_ratio').value 				= '".$row[csf("cut_no")]."';\n";
		echo "document.getElementById('txt_system_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_job_no').value 					= '".$row[csf("job_no_mst")]."';\n";
		echo "document.getElementById('cbo_company_name').value 			= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_source').value 					= '".$row[csf("production_source")]."';\n";
		echo "load_drop_down( 'requires/bundle_knitting_qc_controller', ".($row[csf("production_source")])."+'_'+".($row[csf("company_id")]).", 'load_drop_down_knitting_com','knitting_com');";
		
		echo "document.getElementById('cbo_working_company').value 			= '".$row[csf("working_company_id")]."';\n";
		
		echo "load_location();\n";
		echo "document.getElementById('cbo_working_location').value 		= '".$row[csf("working_location_id")]."';\n";
		echo "load_drop_down( 'requires/bundle_knitting_qc_controller', ".$row[csf("working_location_id")].", 'load_drop_down_floor', 'floor_td' );";
		echo "document.getElementById('cbo_floor').value  					= '".($row[csf("floor_id")])."';\n";
		echo "load_drop_down( 'requires/bundle_knitting_qc_controller', '".$row[csf('company_id')]."', 'load_drop_down_lc_location', 'location_td' );\n";
		echo "document.getElementById('cbo_location').value  				= '".($row[csf("location_id")])."';\n";
		echo "document.getElementById('txt_remarks').value  				= '".($row[csf("remarks")])."';\n";
		echo "document.getElementById('txt_operator_id').value  			= '".($row[csf("operator_id")])."';\n";	
		$employee_name=return_field_value("(first_name||' '||middle_name|| '  ' || last_name) as emp_name", "hrm_employee", " id_card_no='".($row[csf("operator_id")])."' and status_active=1 and is_deleted=0", "emp_name",$new_conn);

		echo "document.getElementById('txt_operation_name').value  			= '".$employee_name."';\n";		
		exit();
	}
}


if($action=='populate_data_from_yarn_lot')
{
	
	$data_array=sql_select("select id, company_id, source, working_company_id, location_id, cutting_no, floor_id, job_no from ppl_cut_lay_mst where cutting_no='$data'  and entry_form=253 and status_active=1 and is_deleted=0");
	
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('txt_lot_ratio').value 				= '".$row[csf("cutting_no")]."';\n";
		echo "document.getElementById('txt_job_no').value 					= '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('cbo_company_name').value 			= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_source').value 					= '".$row[csf("source")]."';\n";
		echo "load_drop_down('requires/bundle_knitting_qc_controller',".($row[csf("source")])."+'_'+".($row[csf("company_id")]).",'load_drop_down_knitting_com','knitting_com');";
		echo "document.getElementById('cbo_working_company').value 			= '".$row[csf("working_company_id")]."';\n";
		
		echo "load_location();\n";
		echo "document.getElementById('cbo_working_location').value 					= '".$row[csf("location_id")]."';\n";
		echo "load_drop_down( 'requires/bundle_knitting_qc_controller', ".$row[csf("location_id")].", 'load_drop_down_floor', 'floor_td' );";
		echo "document.getElementById('cbo_floor').value  = '".($row[csf("floor_id")])."';\n";
		echo "load_drop_down( 'requires/bundle_knitting_qc_controller', '".$row[csf('company_id')]."', 'load_drop_down_lc_location', 'location_td' );\n";
		
		exit();
	}
}

if($action=='populate_data_from_yarn_lot_bundle')
{
	$data_array=sql_select("select a.id, a.company_id, a.source, a.working_company_id, a.location_id, a.cutting_no, a.floor_id, job_no from ppl_cut_lay_mst a, ppl_cut_lay_bundle b where a.id=b.mst_id and b.barcode_no='$data' and a.entry_form=253 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ");
	
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('txt_lot_ratio').value 				= '".$row[csf("cutting_no")]."';\n";
		echo "document.getElementById('txt_job_no').value 					= '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('cbo_company_name').value 			= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_source').value 					= '".$row[csf("source")]."';\n";
		echo "load_drop_down( 'requires/bundle_knitting_qc_controller', ".($row[csf("source")])."+'_'+".($row[csf("company_id")]).", 'load_drop_down_knitting_com','knitting_com');";
		
		echo "document.getElementById('cbo_working_company').value 			= '".$row[csf("working_company_id")]."';\n";
		
		echo "load_location();\n";
		echo "document.getElementById('cbo_working_location').value 					= '".$row[csf("location_id")]."';\n";
		echo "load_drop_down( 'requires/bundle_knitting_qc_controller', ".$row[csf("location_id")].", 'load_drop_down_floor', 'floor_td' );";
		echo "document.getElementById('cbo_floor').value  = '".($row[csf("floor_id")])."';\n";
		echo "load_drop_down( 'requires/bundle_knitting_qc_controller', '".$row[csf('company_id')]."', 'load_drop_down_lc_location', 'location_td' );\n";
		
		exit();
	}
}

if ($action=="load_drop_down_location_hrm")
{
   echo create_drop_down("cbo_location_name", 135, "select id,location_name from lib_location where company_id=$data and status_active=1 and is_deleted=0","id,location_name", 1, "-- Select Location --",$selected,"load_drop_down( 'bundle_knitting_qc_controller', this.value,'load_drop_down_division', 'division_td_hrm');","","","","","","",$new_conn );
}

if ($action=="load_drop_down_division")
{
	//echo "select id,division_name from lib_division where company_id=$data and status_active=1 and is_deleted=0";die;
   echo create_drop_down( "cbo_division_name", 135, "select id,division_name from lib_division where location_id=$data and status_active=1 and is_deleted=0","id,division_name",1,"-- Select Division --",$selected,"load_drop_down( 'bundle_knitting_qc_controller',this.value, 'load_drop_down_department', 'department_td_hrm');","","","","","","",$new_conn );
}

if ($action=="load_drop_down_department")
{
   echo create_drop_down( "cbo_dept_name", 135, "select id,department_name from lib_department where division_id=$data and status_active=1 and is_deleted=0","id,department_name",1,"-- Select Department --", $selected,"load_drop_down( 'bundle_knitting_qc_controller',this.value, 'load_drop_down_section', 'section_td_hrm');","","","","","","",$new_conn );
}

if ($action=="load_drop_down_section")
{
   echo create_drop_down("cbo_section_name", 135, "select id,section_name from lib_section where department_id=$data and status_active=1 and is_deleted=0", "id,section_name", 1, "-- Select Section --", $selected, "", "", "", "", "", "", "", $new_conn );
}

if($action=="system_number_popup")
{
  	echo load_html_head_contents("Batch Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
?>
	<script>
		function js_set_system_value(strCon ) 
		{
		document.getElementById('update_mst_id').value=strCon;
		parent.emailwindow.hide();
		}
    </script>
</head>
<body>
<div align="center" style="width:100%; overflow-y:hidden;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table width="950" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
                <tr>                	 
                    <th width="130">Company name</th>
                    <th width="80">Knitting QC No</th>
                    <th width="80">Lot Ratio No</th>
                    <th width="80">Job No</th>
                    <th width="100">Order No</th>
                    <th width="100">QR Code</th>
                    <th width="130" colspan="2">Date Range</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:70px" class="formbutton"  /></th>           
                </tr>
            </thead>
            <tbody>
                  <tr class="general">                    
                        <td><? echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --",'', ""); ?></td>
                        <td><input name="txt_cut_qc" id="txt_cut_qc" class="text_boxes" style="width:70px"  placeholder="Write"/></td>
                        <td>
                            <input type="text" id="txt_cut_no" name="txt_cut_no" style="width:70px"  class="text_boxes" placeholder="Write"/>
                            <input type="hidden" id="update_mst_id" name="update_mst_id" />
                        </td>
                        <td><input name="txt_job_search" id="txt_job_search" class="text_boxes" style="width:70px"  placeholder="Write"/></td>
                        <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:70px" placeholder="Write" /></td>
                        <td><input name="txt_qr_search" id="txt_qr_search" class="text_boxes" style="width:90px" placeholder="Write" /></td>
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" /></td>
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date" /></td>
                        <td>
                               <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_cut_no').value+'_'+document.getElementById('txt_job_search').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_cut_qc').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_qr_search').value, 'create_system_search_list_view', 'search_div', 'bundle_knitting_qc_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />				
                        </td>
                 </tr>
        		 <tr>                  
                    <td align="center" valign="middle" colspan="9"><? echo load_month_buttons(1); ?></td>
                </tr>   
            </tbody>
         </tr>         
      </table> 
     <div align="center" valign="top" id="search_div"> </div>  
  </form>
</div>    
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_system_search_list_view")
{
    $ex_data = explode("_",$data);
	$company = $ex_data[0];	
	$cutting_no = $ex_data[1];
	$job_no = $ex_data[2];
	$from_date = $ex_data[3];
	$to_date = $ex_data[4];
	$cut_year= $ex_data[5];
	$system_no= $ex_data[6];
	$order_no= $ex_data[7];
	$qr_no= $ex_data[8];
	if(str_replace("'","",$company)==0) { echo "Please select company First"; die;}
    if($db_type==2)
	{ 
		$year_cond=" and extract(year from a.insert_date)=$cut_year"; $year=" extract(year from a.insert_date) as year";
	}
    else if($db_type==0) 
	{ $year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$cut_year"; $year=" SUBSTRING_INDEX(a.insert_date, '-', 1) as year";}
	if(str_replace("'","",$company)==0) $conpany_cond=""; else $conpany_cond="and a.company_id=".str_replace("'","",$company)."";
	if(str_replace("'","",$cutting_no)=="") $cut_cond=""; else $cut_cond="and b.cut_num_prefix_no='".str_replace("'","",$cutting_no)."'  ";
	if(str_replace("'","",$job_no)=="") $job_cond=""; else $job_cond="and c.job_no_prefix_num='".str_replace("'","",$job_no)."'";
	if(str_replace("'","",$system_no)=="") $system_cond=""; else $system_cond="and a.cut_qc_prefix_no=".trim($system_no)." $year_cond";
	if(str_replace("'","",$order_no)=="") $order_cond=""; else $order_cond=" and d.po_number='".str_replace("'","",$order_no)."'";
	if(trim($qr_no)!="") $barcode_no=" and f.barcode_no ='".trim($qr_no)."'"; else $barcode_no="";
	
	if( $from_date!="" && $to_date!="" )
	{
		if($db_type==0)
		{
			$sql_cond= " and a.cutting_qc_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
		}
		if($db_type==2)
		{
			$sql_cond= " and a.cutting_qc_date  between '".change_date_format($from_date,'yyyy-mm-dd','-',1)."' and '".change_date_format($to_date,'yyyy-mm-dd','-',1)."'";
		}
	}
	
	$sql_order="SELECT a.id, a.cutting_no, a.cut_qc_prefix_no, a.cutting_qc_no, a.table_no, a.job_no, a.cutting_qc_date, c.job_no_prefix_num, b.cut_num_prefix_no, $year, d.po_number
    FROM pro_gmts_cutting_qc_mst a, ppl_cut_lay_mst b, ppl_cut_lay_dtls e, wo_po_details_master c, wo_po_break_down d, pro_garments_production_dtls f
    where a.garments_nature=100 and a.production_type=52 and a.cutting_no=b.cutting_no and b.job_no=c.job_no and c.job_no=d.job_no_mst and a.id=f.delivery_mst_id $conpany_cond $cut_cond $job_cond $sql_cond $order_cond $system_cond $barcode_no and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and b.id=e.mst_id group by a.id, a.cutting_no, a.cut_qc_prefix_no, a.cutting_qc_no, a.table_no, a.job_no, a.cutting_qc_date, c.job_no_prefix_num, b.cut_num_prefix_no, a.insert_date, d.po_number order by a.id DESC";
//echo $sql_order;die;
	echo create_list_view("list_view", "Knitting QC No,Year,Lot Ratio No,Job No,Order No,Knitting QC Date","60,60,60,80,100,80","750","270",0, $sql_order , "js_set_system_value", "id", "", 1, "0,0,0,0,0,0", $arr, "cut_qc_prefix_no,year,cut_num_prefix_no,job_no,po_number,cutting_qc_date", "","setFilterGrid('list_view',-1)","0,0,0,0,0,3") ;	
	exit();
}

if($action=="show_dtls_listview_update")
{
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
	$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name" );
	$machine_library=return_library_array( "select id,machine_no from lib_machine_name where category_id=1 and status_active=1 and is_deleted=0 and is_locked=0  order by seq_no", "id", "machine_no" );

	$data=explode("_",$data);

	$sql_cut=sql_select("SELECT a.job_no, b.color_id, b.roll_data, a.id, b.id as dtls_id from ppl_cut_lay_mst a, ppl_cut_lay_dtls b where a.cutting_no='".$data[0]."' and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 ");
	$job_no				=$sql_cut[0][csf("job_no")];
	$color_id			=$sql_cut[0][csf("color_id")];
	$consumption_string	=$sql_cut[0][csf("roll_data")];
	$mst_id				=$sql_cut[0][csf("id")];
	$dtls_id			=$sql_cut[0][csf("dtls_id")];
	list($shortName,$year,$job_prifix)=explode('-',$job_no);
	//echo $consumption_string;die;
	
	$job_sql=sql_select("SELECT c.short_name as BUYER_NAME, a.po_number as PO_NUMBER, a.id as ID from wo_po_break_down  a,wo_po_details_master b ,lib_buyer c where b.job_no='".$job_no."' and a.job_no_mst=b.job_no   and b.buyer_name=c.id ");
	$jbp_arr=array();
	foreach($job_sql as $jval)
	{
		$jbp_arr["buyer_name"]=$jval["BUYER_NAME"];
		$jbp_arr[$jval["ID"]]=$jval["PO_NUMBER"];
	}

	$table_width=1450;
	$div_width=$table_width+20;
	?>	
       <table cellpadding="0"width="<?php echo $div_width;?>"cellspacing="0"border="1"class="rpt_table"rules="all">
            <thead>
            	<tr>
                    <th width="30"  >SL</th>
                    <th width="100" >Bundle No</th>
                    <th width="100" >Barcode No</th>
                    <th width="120" > G. Color</th>
                    <th width="50"  >Size</th>
                    <th width="70"  >Bundle Qty. (Pcs)</th>
                    <th width="70"  >Defect Qty (Pcs)</th>
                    <th width="70"  >Reject Qty (Pcs)</th>
                    <th width="70"  >Replace Qty. (Pcs)</th>
                    <th width="70"  >QC Qty. (Pcs)</th>
                    <th width="50"  >Year</th>
                    <th width="60"  >Job No</th>
                    <th width="65"  >Buyer</th>
                    <th width="90"  >Order No</th>
                    <th width="100" >Gmts. Item</th>
                    <th width="100" >Country</th>
                    <th 			>Action</th>
                </tr>
            </thead>
        </table>
	<div style="width:<?=$div_width;?>px;max-height:250px;overflow-y:scroll" align="left"> 
        <table cellpadding="0"width="<?=$table_width;?>"cellspacing="0"border="1"class="rpt_table"rules="all"id="tbl_details"> <tbody>
		<?php  
			$i=1;	
			$total_production_qnty=0;			
			$sqlResult =sql_select("SELECT b.* , a.gmt_item_id, c.bundle_qty, c.production_qnty, c.color_size_break_down_id, c.reject_qty, c.replace_qty,c.defect_qty, c.mst_id,c.is_rescan from ppl_cut_lay_dtls a, ppl_cut_lay_bundle b, pro_garments_production_dtls c where c.delivery_mst_id=$data[1] and c.production_type=52 and b.mst_id=$mst_id and b.dtls_id=$dtls_id and c.bundle_no=b.bundle_no and a.id=b.dtls_id and a.color_id=$color_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
			//echo "SELECT b.* , a.gmt_item_id, c.bundle_qty, c.production_qnty, c.color_size_break_down_id, c.reject_qty, c.replace_qty,c.defect_qty, c.mst_id,c.is_rescan from ppl_cut_lay_dtls a, ppl_cut_lay_bundle b, pro_garments_production_dtls c where c.delivery_mst_id=$data[1] and c.production_type=52 and b.mst_id=$mst_id and b.dtls_id=$dtls_id and c.bundle_no=b.bundle_no and a.id=b.dtls_id and a.color_id=$color_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
			
		foreach($sqlResult as $row )
		{
			$gmt_mst_id[$row[csf('mst_id')]]=$row[csf('mst_id')];
		}
		//print_r($gmt_mst_id);die;	
		$sql_defect=sql_select("SELECT bundle_no, defect_type_id, defect_point_id, defect_qty, bodypart_id from pro_gmts_prod_dft where mst_id in (".implode(',', $gmt_mst_id).") and production_type=52 and defect_type_id in(3,4) and status_active=1 and is_deleted=0");
		//echo "SELECT bundle_no, defect_type_id, defect_point_id, defect_qty, bodypart_id from pro_gmts_prod_dft where mst_id in (".implode(',', $gmt_mst_id).") and production_type=52 and defect_type_id in(3,4) and status_active=1 and is_deleted=0";

		foreach ($sql_defect as  $value) 
		{
			if($value[csf('defect_type_id')]==3)
			{	
				if(empty($defect_data[$value[csf('bundle_no')]]))
				{	
					$defect_data[$value[csf('bundle_no')]]=$value[csf('defect_point_id')]."*".$value[csf('defect_qty')]."*".$value[csf('bodypart_id')];
				}
				else
				{
					$defect_data[$value[csf('bundle_no')]]="**".$value[csf('defect_point_id')]."*".$value[csf('defect_qty')]."*".$value[csf('bodypart_id')];
				}
			}
			else if($value[csf('defect_type_id')]==4)// for reject
			{
				if(empty($reject_data[$value[csf('bundle_no')]]))
				{	
					$reject_data[$value[csf('bundle_no')]]=$value[csf('defect_point_id')]."*".$value[csf('defect_qty')]."*".$value[csf('bodypart_id')];
				}
				else
				{
					$reject_data[$value[csf('bundle_no')]]="**".$value[csf('defect_point_id')]."*".$value[csf('defect_qty')]."*".$value[csf('bodypart_id')];
				}
			}
		}
		//print_r($defect_data);die;
			foreach($sqlResult as $selectResult)
			{
				if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
 			   ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="" > 
                    <td width="30" align="center"><? echo $i; ?></td>
                    <td width="100" align="center"><p><? echo $selectResult[csf('bundle_no')]; ?></p></td>
                    <td width="100" align="center"><p><? echo $selectResult[csf('barcode_no')]; ?></p></td>
                    <td width="120" align="center" style="word-break:break-all"><p><?php  echo $color_library[$color_id]; ?></p></td>
                    <td width="50" align="center"><?php  echo $size_library[$selectResult[csf('size_id')]]; ?></td>
                    <td width="70" align="right"><?php  echo $selectResult[csf('size_qty')]; ?></td>
                    <td width="70" align="right"> 
                    	<input onKeyUp="calculate_defect_qty();" onDblClick="defect_qty_popup(<?=$i; ?>,<?=$selectResult[csf('id')]; ?>,<?=$selectResult[csf('size_qty')]; ?>);" type="text" id="txt_defect_qty_<?=$i; ?>" name="txt_defect_qty[]" style="width:50px;" class="text_boxes_numeric" value="<?=$selectResult[csf('defect_qty')]; ?>">

                    </td>
                    <td width="70" align="right"><input onKeyUp="calculate_reject_qty()" onDblClick="reject_qty_popup(<? echo $i; ?>,<? echo $selectResult[csf('id')]; ?>,<? echo $selectResult[csf('size_qty')]; ?>)" type="text" id="txt_reject_qty_<? echo $i; ?>" name="txt_reject_qty[]" style="width:50px;" class="text_boxes_numeric" value="<?php  echo $selectResult[csf('reject_qty')]; ?>"></td>
                    <td width="70" align="right"><input onKeyUp="calculate_defect_qty()" type="text" id="txt_replace_qty_<? echo $i; ?>" name="txt_replace_qty[]" style="width:50px;" class="text_boxes_numeric" value="<?php  echo $selectResult[csf('replace_qty')]; ?>"></td>
                    <td width="70" align="right"><?php  echo $selectResult[csf('production_qnty')]; ?></td>
                    <td width="50" align="center"><p><? echo $year; ?></p></td>
                    <td width="60" align="center"><p><? echo $job_prifix*1; ?></p></td>
                    <td width="65" align="center"><?php echo $jbp_arr["buyer_name"]; ?></td>
                    <td width="90" align="center"><?php  echo $jbp_arr[$selectResult[csf('order_id')]]; ?></td>
                    		
                    <td width="100" align="center"><p><?php echo $garments_item[$selectResult[csf('gmt_item_id')]]; ?></p></td>
                    <td width="100" align="center"><p><? echo $country_library[$selectResult[csf('country_id')]]; ?></p></td>
                    <td>
                     	<input type="button" value="-" name="minusButton[]" id="minusButton_<?=$i; ?>" style="width:30px" class="formbuttonplasminus" onClick="fnc_minusRow('<?=$i;  ?>')"/>
                        <input type="hidden" id="txt_color_id_<? echo $i; ?>" name="txt_color_id[]" style="width:80px;" value="<?php echo $color_id; ?>">
                        <input type="hidden" id="txt_size_id_<? echo $i; ?>" name="txt_size_id[]" style="width:80px;" value="<?php echo $selectResult[csf('size_id')]; ?>">
						<input type="hidden" id="txt_order_id_<? echo $i; ?>" name="txt_order_id[]" style="width:80px;" class="text_boxes" value="<?php echo $selectResult[csf('order_id')]; ?>">
                       	<input type="hidden" id="txt_gmt_item_id_<? echo $i; ?>" name="txt_gmt_item_id[]" style="width:80px;" class="text_boxes" value="<?php echo $selectResult[csf('gmt_item_id')]; ?>">
                        <input type="hidden" id="txt_country_id_<? echo $i; ?>" name="txt_country_id[]" style="width:80px;" class="text_boxes" value="<?php echo $selectResult[csf('country_id')]; ?>">
                     	<input type="hidden" id="txt_barcode_<? echo $i; ?>" name="txt_barcode[]" style="width:80px;" class="text_boxes" value="<?php echo $selectResult[csf('barcode_no')]; ?>"> 
                        <input type="hidden" id="txt_colorsize_id_<? echo $i; ?>" name="txt_colorsize_id[]" style="width:80px;" class="text_boxes" value="<?php echo $selectResult[csf('color_size_break_down_id')]; ?>">
                        <input type="hidden" id="txt_dtls_id_<? echo $i; ?>" name="txt_dtls_id[]" style="width:80px;" class="text_boxes" value="">
                       	<input type="hidden" id="trId_<? echo $i; ?>" name="trId[]" value="<?php echo $i; ?>">
                        <input type="hidden" id="actual_defect_<? echo $i; ?>" name="actual_defect[]" value="<?php echo $defect_data[$selectResult[csf('bundle_no')]]; ?>">
						<input type="hidden" id="hidden_breakdown_defect_<? echo $i; ?>" name="hidden_breakdown_defect[]" value="">
						<input type="hidden" id="hidden_breakdown_reject_<? echo $i; ?>" name="hidden_breakdown_reject[]" value="">
                        <input type="hidden" id="actual_reject_<? echo $i; ?>" name="actual_reject[]" value="<?php echo $reject_data[$selectResult[csf('bundle_no')]]; ?>">  
                    	<input type="hidden" name="isRescan[]" id="isRescan_<? echo $i; ?>" value="<?php  echo $selectResult[csf('is_rescan')]; ?>"/>  
                	</td>
                </tr>
            <?php
                $i++;
                $total_bundle_qty+=$selectResult[csf('production_qnty')];
                $total_size_qty+=$selectResult[csf('size_qty')];
			}
			?>
            </tbody>
            <tfoot>
            	<tr id="bundle_footer">
                    <th   colspan="5" > Total</th>
                    <th width="70"  id="total_bundle_qty"><?php echo $total_size_qty; ?></th>
                    <th width="70"  id="total_defect_qty"><?php //echo $total_size_qty; ?></th>
                    <th width="70"  id="total_reject_qty"><?php //echo $total_size_qty; ?></th>
                    <th width="70" id="total_replace_qty"><?php echo number_format($grand_total_consumption,4,".","");?></th>
                    <th width="70" id="total_qc_qty"><?php echo $total_bundle_qty; ?></th>
                    <th width="50"  ></th>
                    <th width="60"  ></th>
                    <th width="65"  ></th>
                    <th width="90"  ></th>
                    <th width="100" ></th>
                    <th width="100" ></th>
                    <th id=""></th>
                </tr>
            </tfoot>
		</table>
	</div>
	<?
	exit();
}

//------------------------------------------------------------------------------------------------------

$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name" );

if($action=="load_report_format")
{
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=7 and report_id in(50) and is_deleted=0 and status_active=1");		 
	echo trim($print_report_format);	
	exit();
}
 
if ($action=="load_drop_down_working_location")
{
	echo create_drop_down( "cbo_working_location", 180, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "",0 );
	exit();   
}
?>
