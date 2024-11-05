<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action == "load_drop_down_knitting_com") {
	$data = explode("_", $data);
	$company_id = $data[1];
	//$company_id
	if ($data[0] == 1) { 
		echo create_drop_down("cbo_working_company_name", 142, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0  order by comp.company_name", "id,company_name", 1, "--Select Knit Company--", $company_id, "load_location();", "");
	} else if ($data[0] == 3) {
		echo create_drop_down("cbo_working_company_name", 142, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--Select Knit Company--", 0, "load_location();");
	} else {
		echo create_drop_down("cbo_working_company_name", 142, $blank_array, "", 1, "--Select Knit Company--", 0, "load_location();");
	}
	exit();
}

 //--------------------------------------------------------------------------------------------
if ($action=="load_drop_down_wo_location")
{
	echo create_drop_down( "cbo_wo_location_name", 130, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );     	 
	exit();
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 130, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/pcs_wise_sticker_generation_controller', this.value, 'load_drop_down_floor', 'floor_td' );" );     	 
	exit();
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor", 130, "select id,floor_name from lib_prod_floor where production_process=2 and status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0 );     	 
}



if($action=="lotratio_search_popup")
{
  	echo load_html_head_contents("Cutting Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
		<script>
			function js_set_cutting_value(strCon ) 
			{
				
			document.getElementById('hidden_system_no').value=strCon;
			parent.emailwindow.hide();
			}
	    </script>
	</head>
	<body>
	<div align="center" style="width:100%; overflow-y:hidden;" >


	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	            <table width="950" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
	            <thead>
	                <tr>                	 
	                    <th width="140">Company name</th>
	                    <th width="130">System No</th>
	                    <th width="130">Style Ref.</th>
	                    <th width="130">Job No</th>
	                    <th width="130" style="display:none">Order No</th>
	                    <th width="250">Date Range</th>
	                    <th width="120"><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
	                </tr>
	            </thead>
	            <tbody>
	                  <tr class="general">                    
	                        <td>
	                              <? 
	                                   echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 order by company_name","id,company_name", 0, "-- Select Company --",$cbo_company_id, "",1);
	                             ?>
	                        </td>
	                      
	                        <td align="center" >
	                                <input type="text" id="txt_cut_no" name="txt_cut_no" style="width:120px"  class="text_boxes_numeric"/>
	                                <input type="hidden" id="hidden_system_no" name="hidden_system_no" />
	                        </td>
	                        <td align="center">
	                               <input name="txt_style_search" id="txt_style_search" class="text_boxes" style="width:120px"  />
	                        </td>
	                        <td align="center">
	                               <input name="txt_job_search" id="txt_job_search" class="text_boxes_numeric" style="width:120px"  />
	                        </td>
	                        <td align="center" style="display:none">
	                               <input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:120px"  />
	                        </td>
	                        
	                        <td align="center" width="250">
	                               <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:100px" placeholder="From Date" />
	                               <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:100px" placeholder="To Date" />
	                        </td>
	                        <td align="center">
	                               <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_cut_no').value+'_'+document.getElementById('txt_job_search').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style_search').value, 'create_lotratio_search_list_view', 'search_div', 'pcs_wise_sticker_generation_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
	                        </td>
	                 </tr>
	        		 <tr>                  
	                    <td align="center"  valign="middle" colspan="6">
	                        <? echo load_month_buttons(1);  ?>
	                    </td>
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

if($action=="create_lotratio_search_list_view")
{
    $ex_data = explode("_",$data);

	$company = $ex_data[0];	
	$cutting_no = $ex_data[1];
	$job_no = $ex_data[2];
	$from_date = $ex_data[3];
	$to_date = $ex_data[4];
	$cut_year= $ex_data[5];
	$order_no= $ex_data[6];
	$style_serch_no= $ex_data[7];

    if($db_type==2) { $year_cond=" and extract(year from a.insert_date)=$cut_year"; $year=" extract(year from a.insert_date) as year ";}
    if($db_type==0) {$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$cut_year"; $year=" SUBSTRING_INDEX(a.insert_date, '-', 1) as year ";}
	
	if(str_replace("'","",$company)==0) $conpany_cond=""; else $conpany_cond="and a.company_id=".str_replace("'","",$company)."";
	if(str_replace("'","",$cutting_no)=="") $cut_cond=""; else $cut_cond="and a.cut_num_prefix_no='".str_replace("'","",$cutting_no)."'  $year_cond";
	if(str_replace("'","",$job_no)=="") $job_cond=""; else $job_cond="and b.job_no_prefix_num='".str_replace("'","",$job_no)."'";
	if(str_replace("'","",$style_serch_no)=="") $style_cond=""; else $style_cond="and b.style_ref_no  like '%".$style_serch_no."%' ";
	
	if( $from_date!="" && $to_date!="" )
	{
		if($db_type==0)
		{
			$sql_cond= " and entry_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
		}
		if($db_type==2)
		{
			$sql_cond= " and entry_date  between '".change_date_format($from_date,'yyyy-mm-dd','-',1)."' and '".change_date_format($to_date,'yyyy-mm-dd','-',1)."'";
		}
	}
	

	
	$sql_order="SELECT a.id,a.cut_num_prefix_no,a.cutting_no, a.table_no, a.job_no, a.batch_id, a.entry_date, a.cad_marker_cons, a.marker_width, a.fabric_width,b.style_ref_no,c.color_id, c.marker_qty, c.order_cut_no,$year FROM ppl_cut_lay_mst a,wo_po_details_master b,ppl_cut_lay_dtls c where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  c.mst_id=a.id and a.job_no=b.job_no and a.entry_form=253 $conpany_cond $cut_cond $job_cond $sql_cond $style_cond order by id DESC";
	//echo $sql_order;die;
	$table_no_arr=return_library_array( "select id,table_no from lib_cutting_table",'id','table_no');
	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	
	$arr=array(5=>$color_arr);//,4=>$order_number_arr,5=>$color_arr,Order NO,Color
	echo create_list_view("list_view", "System No,Year,Order Cut No,Job No,Style Ref.,Color,Ratio Qty,Cons/Dzn(Lbs),Entry Date","60,50,60,90,140,200,80,90,80","950","270",0, $sql_order , "js_set_cutting_value", "id,cutting_no", "", 1, "0,0,0,0,0,color_id,0,0,0,0", $arr, "cut_num_prefix_no,year,order_cut_no,job_no,style_ref_no,color_id,marker_qty,cad_marker_cons,entry_date", "","setFilterGrid('list_view',-1)","0,0,0,0,0,0,1,2,3") ;
	exit();
}

if ($action == "generate_bundle" )
{
	// var_dump($_REQUEST);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 	
	
	$working_company	= str_replace( "'", "", $cbo_working_company_name );
	$wo_location		= str_replace( "'", "", $cbo_wo_location_name );
	$company_name		= str_replace( "'", "", $cbo_company_name );
	$location_name		= str_replace( "'", "", $cbo_location_name);
	$floor				= str_replace( "'", "", $cbo_floor);
	$lot_ratio_no		= str_replace( "'", "", $txt_lot_ratio_no);
	$lot_ratio_id		= str_replace( "'", "", $hidden_lot_ratio_id );
	$date_from			= str_replace( "'", "", $txt_date_from );	
	$date_to			= str_replace( "'", "", $txt_date_to );
	
	$sql_cond	= "";	
	$sql_cond .= ($working_company!=0) ? " and a.working_company_id=$working_company" : "";
	$sql_cond .= ($wo_location!=0) ? " and a.location_id=$wo_location" : "";
	$sql_cond .= ($company_name!=0) ? " and a.company_id=$company_name" : "";
	$sql_cond .= ($location_name!=0) ? " and a.location_id=$location_name" : "";
	$sql_cond .= ($floor!=0) ? " and a.floor_id=$floor" : "";
	$sql_cond .= ($lot_ratio_id!="") ? " and a.id=$lot_ratio_id" : "";
	// $sql_cond .= ($lot_ratio_no!="") ? " and a.cutting_no='$lot_ratio_no'" : "";
	
	// ========================================= MAIN QUERY ========================================		
	$sql="SELECT a.id AS cut_id,a.cutting_no,c.id as bndl_id,c.order_id,c.bundle_no,c.size_id,c.size_qty,c.number_start,c.number_end,c.barcode_no
	FROM 
    	ppl_cut_lay_mst a, 
    	ppl_cut_lay_dtls b, 
    	ppl_cut_lay_bundle c
		WHERE   b.mst_id = a.id
        AND b.id = c.dtls_id
        AND a.id = c.mst_id
        AND a.status_active=1
        AND c.status_active=1
        AND b.status_active=1
        AND a.is_deleted=0
        AND b.is_deleted=0
        AND c.is_deleted=0
        $sql_cond
	ORDER BY a.id ASC";
	// echo $sql;die;	
	$result=sql_select($sql);	
	if(count($result)==0)
	{
		echo "<div style='text-align:center;color:red;font-size:20px;font-weight:bold;'>Data Not Found.</div>";
		die;
	}
	$order_id_arr = array();
	foreach ($result as $row) 
    {
    	$order_id_arr[$row['ORDER_ID']] = $row['ORDER_ID'];
	}

	$order_id_cond = where_con_using_array($order_id_arr,0,"id");
	$lib_order_no = return_library_array("SELECT id,po_number from wo_po_break_down where status_active=1 $order_id_cond", "id", "po_number");
	$lib_size = return_library_array("select id, size_name from  lib_size", "id", "size_name");
	ob_start();	
	
	$table_width=850;
	$i=1;          
	?>
    <fieldset style="width:<? echo $table_width; ?>px;margin: 0 auto; margin-top: 10px;">
    	<center>
        	<button type="button" class="button" style="cursor: pointer;padding: 5px;background: #FFAD60;margin: 5px 0;border-radius: 4px;" onclick="fnc_bundle_report_qr_code();">Generate QR Code</button>      
        </center>
        <table class="rpt_table" width="<? echo $table_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
            <thead>      	
                <tr>
                    <th rowspan="2" width="40">Sl</th>
                    <th rowspan="2" width="100">Lot Ratio</th>
                    <th rowspan="2" width="100">Order No</th>
                    <th rowspan="2" width="100">Size</th>
                    <th rowspan="2" width="100">Bundle No</th>
                    <th rowspan="2" width="60">Bundle Qty</th>
                    <th rowspan="2" width="100">Main Bundle QR Code</th>
                    <th width="100" colspan="2">RMG Number</th>
                    <th rowspan="2" width="100">QR Code </th>
                    <th rowspan="2" width="30"><input type="checkbox" name="check_all"  id="check_all" onClick="check_all_report()"></th>                 
                </tr>
                <tr>
                	<th width="50">From</th>
                	<th width="50">To</th>
                </tr>
	        </thead>
	        <tbody>
            	<?	
            	$i=1;   
            	$j = 1;        	 
            	foreach ($result as $row) 
            	{
            		$bgcolor = ($i%2==0) ? "#E9F3FF" : "#FFFFFF";
            		?>
            		<tr bgcolor="#FFAD60">
            			<td><?=$i;?></td>
            			<td><?=$row['CUTTING_NO'];?></td>
            			<td><?=$lib_order_no[$row['ORDER_ID']];?></td>
            			<td><?=$lib_size[$row['SIZE_ID']];?></td>
            			<td><?=$row['BUNDLE_NO'];?></td>
            			<td align="right"><?=$row['SIZE_QTY'];?></td>
            			<td align="center"><?=$row['BARCODE_NO'];?></td>
            			<td><?=$row['NUMBER_START'];?></td>
            			<td><?=$row['NUMBER_END'];?></td>
            			<td></td>
            			<td align="center">
            				<input id="chk_bundle_<?=$i;?>" type="checkbox" name="chk_bundle" class="parent_bndl" onclick="select_chield()">
            				<input type="hidden" id="hiddenid_<?=$i; ?>" name="hiddenid_<?=$i; ?>" value="<?=$row['BNDL_ID']; ?>" data-sl="">
            			</td>
            		</tr>
            		<?
            		$i++;
            		$j=1;
            		for ($k=0; $k < $row['SIZE_QTY']; $k++) 
            		{ 
            			
            			$bgcolor = ($i%2==0) ? "#E9F3FF" : "#FFFFFF";
            			?>
	            		<tr bgcolor="<?=$bgcolor;?>" id="tr_<?=$i;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')">
	            			<td><?=$i;?></td>
	            			<td><?=$row['CUTTING_NO'];?></td>
	            			<td><?=$lib_order_no[$row['ORDER_ID']];?></td>
	            			<td><?=$lib_size[$row['SIZE_ID']];?></td>
	            			<td><?=$row['BUNDLE_NO'];?></td>
	            			<td align="right">1</td>
	            			<td align="center"></td>
	            			<td></td>
	            			<td></td>
	            			<td><?=$row['BARCODE_NO']."-".$j;?></td>
	            			<td align="center"><input id="chk_bundle_<?=$i;?>" type="checkbox" name="chk_bundle" class="chield_bndl" ></td>
            				<input type="hidden" id="hiddenid_<?=$i; ?>" name="hiddenid_<?=$i; ?>" value="<?=$row['BNDL_ID'];  ?>" data-sl="<?=$j;?>">
	            		</tr>
	            		<?
	            		$j++;
	            		$i++;
            		}
            	}
            	?>
	        </tbody>
	    </table>
    </fieldset>
	<?

	foreach (glob("*.xls") as $filename)
	{		
		@unlink($filename);

	}
	$name=time().".xls";
	$create_new_excel = fopen($name, 'w');	
	$report_data=ob_get_contents();
	ob_clean();
	$is_created = fwrite($create_new_excel,$report_data);
	echo $report_data."####".$name;
	exit();
}

if($action=="print_qrcode_operation")
{
	// print_r($_REQUEST);echo $_POST['data'];
	// echo "string".$data;die();
	$dataEx = explode(",", $data);
	$bndle_id_arr = array(); 
	$bndle_sl_arr = array();
	foreach ($dataEx as $value) 
	{
		$value_ex = explode("_", $value);
		if($value_ex[1]!="")
		{
			$bndle_id_arr[$value_ex[0]] = $value_ex[0];
			$bndle_sl_arr[$value_ex[0]][] = $value_ex[1];
		}
	}
	$bndle_id = implode(",", $bndle_id_arr);

	// print_r($bndle_sl_arr);
	// echo "string".count($bndle_sl_arr[391834]);

	$color_sizeID_arr=sql_select("SELECT a.id,										
										a.size_id,
										a.bundle_no,
										a.barcode_no,
										a.order_id,
										a.mst_id,
										a.size_qty
									from 
										ppl_cut_lay_bundle a, 
										ppl_cut_lay_size_dtls b 
									where 
										a.mst_id=b.mst_id and 
										a.dtls_id=b.dtls_id and 
										a.size_id=b.size_id and 
										a.id in ($bndle_id) 
									order by 
										b.bundle_sequence,
										a.id");
	
	foreach($color_sizeID_arr as $val_qty)
	{
		$mst_id = $val_qty[csf('mst_id')];
		$total_cut_qty+=$val_qty[csf('size_qty')];
		$total_size_qty_arr[$val_qty[csf('size_id')]]+=$val_qty[csf('size_qty')];
		$order_id_arr[$val_qty[csf('order_id')]] = $val_qty[csf('order_id')];
	}
	$order_ids = implode(",", $order_id_arr);
	$bundle_array=array();
	$sql_name=sql_select("SELECT 
							b.buyer_name,
							b.style_ref_no,
							b.job_no_prefix_num,
							a.po_number,
							a.id
						from 
							wo_po_details_master b,
							wo_po_break_down a
						where 
							b.id=a.job_id and 
							a.id in($order_ids)");

	foreach($sql_name as $value)
	{
		$job_no 								=$value[csf('job_no_prefix_num')];
		$style_name 							=$value[csf('style_ref_no')];
		$buyer_name 							=$value[csf('buyer_name')];
		$po_number_arr[$value[csf('id')]] 		=$value[csf('po_number')];
	}
	$buyer_short_name=return_field_value("short_name","lib_buyer", "id=$buyer_name ");
	//return_library_array( "select id,short_name from lib_buyer where id=$buyer_name ", "id", "short_name");
	$lib_size = return_library_array("select id, size_name from  lib_size", "id", "size_name");
	$sql_cut_name=sql_select("SELECT 
								cutting_no 
							from 
								ppl_cut_lay_mst 
							where id=$mst_id");

	foreach($sql_cut_name as $cut_value)
	{
		$ful_cut_no 		=$cut_value[csf('cutting_no')];
	}

    $PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'qrcode_image'.DIRECTORY_SEPARATOR.$ful_cut_no.DIRECTORY_SEPARATOR;
    $PNG_WEB_DIR = 'qrcode_image/'.$ful_cut_no.'/';

    foreach (glob($PNG_WEB_DIR."*.png") as $filename) {			
		@unlink($filename);
	}

    if (!file_exists($PNG_TEMP_DIR)) mkdir($PNG_TEMP_DIR);
     
    $filename = $PNG_TEMP_DIR.'test.png';
    $errorCorrectionLevel = 'L';
    $matrixPointSize = 4;

    include "../../../ext_resource/phpqrcode/qrlib.php"; 
	require_once("../../../ext_resource/mpdf60/mpdf.php");

	$mpdf = new mPDF('',    // mode - default ''
					array(40,70),		// array(65,210),    // format - A4, for example, default ''
					 5,     // font size - default 0
					 '',    // default font family
					 3,    // margin_left
					 3,    // margin right
					 3,     // margin top
					 0,    // margin bottom
					 0,     // margin header
					 0,     // margin footer
					 'L');
	$i=1; 
	foreach($color_sizeID_arr as $val)
	{
		for ($k=0; $k < count($bndle_sl_arr[$val['ID']]); $k++) 
		{ 
			
			$barcode_no = $val[csf("barcode_no")].'-'.$bndle_sl_arr[$val['ID']][$k];
			$filename = $PNG_TEMP_DIR.'test'.md5($barcode_no).'.png';
	    	QRcode::png($barcode_no, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
					
			$mpdf->AddPage('',    // mode - default ''
				array(40,70),		// array(65,210),    // format - A4, for example, default ''
				 5,     // font size - default 0
				 '',    // default font family
				 3,    // margin_left
				 3,    // margin right
				 3,     // margin top
				 0,    // margin bottom
				 0,     // margin header
				 0,     // margin footer
				 'L');

			$html.='<table cellpadding="0" cellspacing="0" width="" class="" style="font-size:12px; font-weight:bold;margin:0px" rules="all" id="">
				    	
				        	<tr>
								<td  width="50%"  >
									<table  width="100%" border="0">
										<tr>
											<td  width=""  >
											<div id="div_'.$i.'"><img src="'.$PNG_WEB_DIR.basename($filename).'" height="100" width=""></div></td>
										</tr>
									</table>
								</td>
								<td  width="50%"  >
									<table  width="100%">
										<tr>
											<td width="">'.$buyer_short_name.'</td>
										</tr>
										<tr>
											<td width="">'.$style_name.'</td>
										</tr>
										<tr>
											<td width="">'.$job_no.'</td>
										</tr>
										<tr>
											<td width="" style="font-size:18px;">'.$lib_size[$val[SIZE_ID]].'</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
				            	<td>'.$barcode_no.'</td>
				            	<td align="right"></td>
				            </tr>
							<tr>
				            	<td></td>
				            	<td align="right">Qty '.$val[SIZE_QTY].'/'.$bndle_sl_arr[$val['ID']][$k].'</td>
				            </tr>

				</table>';


			$mpdf->WriteHTML($html);
			$html='';
			$i++;
		}
		
		
	} 
	
	//$mpdf->WriteHTML($html);
	foreach (glob("*.pdf") as $filename) {			
			@unlink($filename);
		}
	$name = 'lotRatioEntry_' . date('j-M-Y_h-iA') .'_'.$user_id.'.pdf';
	$mpdf->Output($name, 'F');	
	echo "1###$name";
			
	exit();

}
?>