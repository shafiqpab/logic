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

if ($action=="fabricBooking_popup")
{
	echo load_html_head_contents("WO Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
?> 
	<script>
	
		function js_set_value(booking_id,booking_no,color_id,color,job_no,type)
		{
			$('#hidden_booking_id').val(booking_id);
			$('#hidden_booking_no').val(booking_no);
			$('#hidden_color_id').val(color_id);
			$('#hidden_color').val(color);
			$('#hidden_job_no').val(job_no);
			$('#booking_without_order').val(type);
			parent.emailwindow.hide();
		}
	
    </script>

</head>

<body>
<div align="center" style="width:962px;">
	<form name="searchwofrm"  id="searchwofrm" autocomplete=off>
		<fieldset style="width:100%;">
		<legend>Enter search words</legend>           
            <table cellpadding="0" cellspacing="0" width="750" class="rpt_table" border="1" rules="all">
                <thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="200">Enter Booking No</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
                    	<input type="hidden" name="hidden_booking_id" id="hidden_booking_id" class="text_boxes" value="">  
                        <input type="hidden" name="hidden_booking_no" id="hidden_booking_no" class="text_boxes" value="">
                        <input type="hidden" name="hidden_color_id" id="hidden_color_id" class="text_boxes" value="">
                        <input type="hidden" name="hidden_color" id="hidden_color" class="text_boxes" value="">  
                        <input type="hidden" name="hidden_job_no" id="hidden_job_no" class="text_boxes" value="">
                        <input type="hidden" name="booking_without_order" id="booking_without_order" class="text_boxes" value="">  
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
                       		$search_by_arr=array(1=>"Booking No",2=>"Buyer Order",3=>"Job No",4=>"Booking Date",5=>"Internal Ref.",6=>"File No");
							$dd="change_search_event(this.value, '0*0*0*3*0*0', '0*0*0*3*0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 170, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                    </td>                 
                    <td align="center" id="search_by_td">				
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td> 						
            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+'<? echo $batch_against; ?>', 'create_booking_search_list_view', 'search_div', 'batch_creation_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                     </td>
                </tr>
           </table>
           <table width="100%" style="margin-top:5px">
                <tr>
                    <td colspan="5">
                        <div style="width:100%; margin-top:10px; margin-left:3px" id="search_div" align="left"></div>
                    </td>
                </tr>
            </table> 
		</fieldset>
	</form>
</div>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_booking_search_list_view")
{
	$data = explode("_",$data);
	
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$company_id =$data[2];
	$buyer_id =$data[3];
	$batch_against =$data[4];
	
	//if($buyer_id==0) { echo "Please Select Buyer First."; die; }
	if($buyer_id==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_samp_cond=" and s.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_samp_cond="";
		}
		else
		{
			$buyer_id_cond="";
			$buyer_id_samp_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_id=$buyer_id";
		$buyer_id_samp_cond=" and s.buyer_id=$buyer_id";
	}
	
	if(trim($data[0])!="")
	{
		if($search_by==1)
			$search_field_cond="and a.booking_no like '$search_string'";
		else if($search_by==2)	
			$search_field_cond="and c.po_number like '$search_string'";
		else if($search_by==3)	
			$search_field_cond="and c.job_no_mst like '$search_string'";
		else if($search_by==5)	
			$search_field_cond="and c.grouping like '$search_string'";
		else if($search_by==6)	
			$search_field_cond="and c.file_no like '$search_string'";		
		else	
		{
			if($db_type==0)
			{
				$search_field_cond="and a.booking_date like '".change_date_format(trim($data[0]), "yyyy-mm-dd", "-")."'";
			}
			else
			{
				$search_field_cond="and a.booking_date like '".change_date_format(trim($data[0]),'','',1)."'";
			}
		}
	}
	else
	{
		$search_field_cond="";
	}
	
	$style_ref_arr=return_library_array( "select job_no, style_ref_no from wo_po_details_master where company_name=$company_id",'job_no','style_ref_no');
	$po_number_array=array();
	$poData=sql_select( "select id, po_number, grouping, file_no from wo_po_break_down");
	foreach($poData as $rowP)
	{
		$po_number_array[$rowP[csf('id')]]['no']=$rowP[csf('po_number')];
		$po_number_array[$rowP[csf('id')]]['ref']=$rowP[csf('grouping')];
		$po_number_array[$rowP[csf('id')]]['file_no']=$rowP[csf('file_no')];
	}

	if($batch_against==1)
	{
		if($db_type==0)
		{
			$sql= "SELECT a.id, a.booking_no, a.booking_date, a.buyer_id, b.fabric_color_id, c.job_no_mst, group_concat(c.id) as po_id, 0 as type FROM wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c WHERE a.booking_no=b.booking_no and a.booking_type<>4 and b.po_break_down_id=c.id and a.company_id=$company_id and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and c.status_active =1 and c.is_deleted =0 and a.item_category=2 $buyer_id_cond $search_field_cond group by a.id, b.fabric_color_id";// and a.buyer_id=$buyer_id , group_concat(distinct(c.po_number)) as po_number
		}
		else
		{
			//$sql= "SELECT a.id, a.booking_no, a.booking_date, a.buyer_id, b.fabric_color_id, c.job_no_mst, wm_concat(distinct(c.id)) as po_id, wm_concat(distinct CAST(c.id || '**' || PO_NUMBER  AS VARCHAR2(4000))) as po_data, 0 as type FROM wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c WHERE a.booking_no=b.booking_no and a.booking_type<>4 and b.po_break_down_id=c.id and a.company_id=$company_id and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and c.status_active =1 and c.is_deleted =0 and a.item_category=2 $buyer_id_cond $search_field_cond group by a.id, b.fabric_color_id, a.booking_no, a.booking_date, a.buyer_id, c.job_no_mst";
			$sql= "SELECT a.id, a.booking_no, a.booking_date, a.buyer_id, b.fabric_color_id, c.job_no_mst, LISTAGG(c.id, ',') WITHIN GROUP (ORDER BY c.id) as po_id,0 as type FROM wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c WHERE a.booking_no=b.booking_no and a.booking_type<>4 and b.po_break_down_id=c.id and a.company_id=$company_id and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and c.status_active =1 and c.is_deleted =0 and a.item_category=2 $buyer_id_cond $search_field_cond group by a.id, b.fabric_color_id, a.booking_no, a.booking_date, a.buyer_id, c.job_no_mst";// LISTAGG(CAST(c.id || '**' || PO_NUMBER  AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY c.id) as po_data, 
		}
	}
	else
	{
		if($search_by==1)
			$search_field_cond_sample="and s.booking_no like '$search_string'";
		else if($search_by==4)	
		{
			if($db_type==0)
			{
				$search_field_cond_sample="and s.booking_date like '".change_date_format(trim($data[0]), "yyyy-mm-dd", "-")."'";
			}
			else
			{
				$search_field_cond_sample="and s.booking_date like '".change_date_format(trim($data[0]),'','',1)."'";
			}
		}
		else	
			$search_field_cond_sample="";
			
		if($db_type==0)
		{
			$sql= "SELECT a.id, a.booking_no, a.booking_date, a.buyer_id, b.fabric_color_id, c.job_no_mst, group_concat(c.id) as po_id, 0 as type FROM wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c WHERE a.booking_no=b.booking_no and a.booking_type=4 and b.po_break_down_id=c.id and a.company_id=$company_id and a.status_active=1 and a.is_deleted =0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.item_category=2 $buyer_id_cond $search_field_cond group by a.id, b.fabric_color_id
			union all
				SELECT s.id, s.booking_no, s.booking_date, s.buyer_id, f.fabric_color as fabric_color_id, '' as job_no_mst, '' as po_id, 1 as type FROM wo_non_ord_samp_booking_mst s, wo_non_ord_samp_booking_dtls f WHERE s.booking_no=f.booking_no and s.company_id=$company_id and s.status_active =1 and s.is_deleted =0 and f.status_active =1 and f.is_deleted =0 and s.item_category=2 $buyer_id_samp_cond $search_field_cond_sample group by s.id, f.fabric_color";// and a.buyer_id=$buyer_id and s.buyer_id=$buyer_id
		}
		else
		{
			$sql= "SELECT a.id, a.booking_no, a.booking_date, a.buyer_id, b.fabric_color_id, c.job_no_mst, LISTAGG(c.id, ',') WITHIN GROUP (ORDER BY c.id) as po_id, 0 as type FROM wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c WHERE a.booking_no=b.booking_no and a.booking_type=4 and b.po_break_down_id=c.id and a.company_id=$company_id and a.status_active=1 and a.is_deleted =0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.item_category=2 $buyer_id_cond $search_field_cond group by a.id, b.fabric_color_id, a.booking_no, a.booking_date, a.buyer_id, c.job_no_mst
			union all
				SELECT s.id, s.booking_no, s.booking_date, s.buyer_id, f.fabric_color as fabric_color_id, null as job_no_mst, null as po_id, 1 as type FROM wo_non_ord_samp_booking_mst s, wo_non_ord_samp_booking_dtls f WHERE s.booking_no=f.booking_no and s.company_id=$company_id and s.status_active =1 and s.is_deleted =0 and f.status_active =1 and f.is_deleted =0 and s.item_category=2 $buyer_id_samp_cond $search_field_cond_sample group by s.id, f.fabric_color, s.booking_no, s.booking_date, s.buyer_id";
		}
	}
	//echo $sql;die;
	
	$result = sql_select($sql);
	$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$color_arr=return_library_array( "select id,color_name from lib_color",'id','color_name');
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="930" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th width="115">Booking No</th>
            <th width="75">Booking Date</th>               
            <th width="100">Buyer</th>
            <th width="85">Job No</th>
            <th width="100">Style Ref.</th>
            <th width="70">Color</th>
           	<? if($batch_against==3){?> <th width="60">Without Order</th><? } ?>
            <th width="100">Internal Ref.</th>
            <th width="80">File No</th>
            <th>Buyer Order</th>
        </thead>
	</table>
	<div style="width:950px; max-height:270px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="930" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            {  
                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	 

				/*$po_array=array(); $po_no='';
				$po_data=array_unique(explode(",",$row[csf('po_data')]));
				foreach($po_data as $value)
				{
					$value=explode("**",$value);
					$po_array[$value[0]]=$value[1];
				}*/
				//print_r($po_array);
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
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('booking_no')]; ?>','<? echo $row[csf('fabric_color_id')]; ?>','<? echo $color_arr[$row[csf('fabric_color_id')]]; ?>','<? echo $row[csf('job_no_mst')]; ?>','<? echo $row[csf('type')]; ?>');"> 
                    <td width="30"><? echo $i; ?></td>
                    <td width="115"><p><? echo $row[csf('booking_no')]; ?></p></td>
                    <td width="75" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>               
                    <td width="100"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>
                    <td width="85" align="center"><p><? echo $row[csf('job_no_mst')]; ?>&nbsp;</p></td>
                    <td width="100"><p><? echo $style_ref_arr[$row[csf('job_no_mst')]]; ?>&nbsp;</p></td>
                    <td width="70"><p><? echo $color_arr[$row[csf('fabric_color_id')]]; ?>&nbsp;</p></td>
                    <? if($batch_against==3){?> <td width="60" align="center"><? if($row[csf('type')]==0) echo "No"; else echo "Yes"; ?></td><? } ?>
                    <td width="100"><p><? echo $intl_ref; ?>&nbsp;</p></td>
                    <td width="80"><p><? echo $file_no; ?>&nbsp;</p></td>
                    <td><p><? echo $po_no; ?>&nbsp;</p></td>
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

if($action=="populate_color_id")
{
	$data=explode("**",$data);
	$booking_no=$data[0];
	$color_name=$data[1];
	
	$color_id=return_field_value("distinct(a.id) as id","lib_color a, wo_booking_dtls b ","a.id=b.fabric_color_id and a.color_name='$color_name' and b.booking_no='$booking_no' and b.status_active=1 and b.is_deleted=0","id");
	echo $color_id;  
	exit();
}

if($action=="populate_barcode_data")
{
	$data=explode("**",$data);
	$bar_code=$data[0];
	$barcodeData=''; $roll_ids=''; $po_ids_arr=array(); $barcodeDataArr=array(); $rollDataArray=array();
	$sql_barcode= sql_select("select a.barcode_no,a.qc_pass_qnty,a.roll_id,a.roll_no,b.po_number,a.po_breakdown_id, a.roll_split_from from pro_roll_details a,wo_po_break_down b where a.entry_form=62 and a.barcode_no in($bar_code) and a.po_breakdown_id=b.id and a.status_active=1 and a.is_deleted=0 order by a.barcode_no");
	foreach($sql_barcode as $rowb)
	{
		$barcodeDataArr[$rowb[csf('barcode_no')]]=$rowb[csf('qc_pass_qnty')]."__".$rowb[csf('po_number')]."__".$rowb[csf('roll_no')]."__".$rowb[csf('roll_id')]."__".$rowb[csf('po_breakdown_id')];
		
		$roll_ids.=$rowb[csf('roll_id')].",";
		$po_ids_arr[$rowb[csf('po_breakdown_id')]]=$rowb[csf('po_breakdown_id')];
	}
	
	$po_ids=implode(",",$po_ids_arr);
	$dia_type_arr=return_library_array( "select a.id, a.width_dia_type from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b where a.id=b.dtls_id and b.po_id in($po_ids)",'id','width_dia_type');
	
	$roll_ids=chop($roll_ids,',');
	$data_array=sql_select("SELECT a.booking_no, a.booking_id, a.receive_basis, a.entry_form, b.prod_id, b.body_part_id, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.barcode_no, d.product_name_details FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, product_details_master d WHERE a.id=b.mst_id and b.id=c.dtls_id and b.prod_id=d.id and c.id in(".$roll_ids.") and a.entry_form in(2,22) and c.entry_form in(2,22) and c.status_active=1 and c.is_deleted=0");
	foreach($data_array as $row)
	{
		$barcode_no=$row[csf('barcode_no')];
		$qc_pass_qnty=$barcodeDataArr[$barcode_no]['qty'];
		$roll_no=$row[csf('roll_no')];
		
		if($row[csf("entry_form")]==2 && $row[csf("receive_basis")]==2) 
		{
			$promram_id=$row[csf('booking_id')];
			$widthDiaType=$dia_type_arr[$row[csf('booking_id')]];
		}
		else 
		{
			$promram_id=0;
			$widthDiaType=0;
		}
		
		$data=$row[csf('booking_no')]."**".$promram_id."**".$row[csf('prod_id')]."**".$row[csf('product_name_details')]."**".$row[csf('body_part_id')]."**".$body_part[$row[csf('body_part_id')]]."**".$widthDiaType;
		
		$rollDataArray[$row[csf('roll_id')]]=$data;
	}
	
	if(count($barcodeDataArr)>0)
	{
		foreach($barcodeDataArr as $barcode_no=>$value)
		{
			$barcodeDatas=explode("__",$value);
			$qc_pass_qnty=$barcodeDatas[0];
			$po_no=$barcodeDatas[1];
			$roll_no=$barcodeDatas[2];
			$roll_id=$barcodeDatas[3];
			$po_breakdown_id=$barcodeDatas[4];
			
			$rollDatas=explode("**",$rollDataArray[$roll_id]);
			$booking_no=$rollDatas[0];
			$promram_id=$rollDatas[1];
			$prod_id=$rollDatas[2];
			$product_name_details=$rollDatas[3];
			$body_part_id=$rollDatas[4];
			$body_part_name=$rollDatas[5];
			$widthDiaType=$rollDatas[6];
			
			$barcodeData.=$booking_no."**".$promram_id."**".$prod_id."**".$product_name_details."**".$roll_id."**".$roll_no."**".$po_breakdown_id."**".$po_no."**".$qc_pass_qnty."**".$barcode_no."**".$body_part_id."**".$body_part_name."**".$widthDiaType."_";
		}
		echo substr($barcodeData,0,-1);
	}
	else
	{
		echo "0";
	}
	exit();
}

if($action=="barcode_popup")
{
	echo load_html_head_contents("Barcode Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	if($company_id>0) $disable=1; else $disable=0;  
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
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			
			$('#hidden_barcode_nos').val( id );
		}
		
		function fnc_close()
		{
			parent.emailwindow.hide();
		}
		
		function reset_hide_field()
		{
			$('#hidden_barcode_nos').val( '' );
			selected_id = new Array();
		}
	
    </script>

</head>

<body>
<div align="center" style="width:920px;">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:920px; margin-left:2px">
		<legend>Enter search words</legend>           
            <table cellpadding="0" cellspacing="0" width="550" border="1" rules="all" class="rpt_table">
                <thead>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="180">Please Enter Order No</th>
                    <th>Barcode No</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        <input type="hidden" name="hidden_barcode_nos" id="hidden_barcode_nos">  
                    </th>
                </thead>
                <tr class="general">
                    <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Order No",2=>"Internal Ref.",3=>"File No");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"", 0, "--Select--", 1,$dd,0 );
						?>
                    </td>     
                    <td align="center" id="search_by_td">				
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td> 				
                    <td><input type="text" name="barcode_no" id="barcode_no" style="width:120px" class="text_boxes" /></td>  		
            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $company_id; ?>+'_'+document.getElementById('barcode_no').value, 'create_barcode_search_list_view', 'search_div', 'batch_creation_controller', 'setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')" style="width:100px;" />
                     </td>
                </tr>
           </table>
           <div style="width:100%; margin-top:5px; margin-left:10px" id="search_div" align="left"></div>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_barcode_search_list_view")
{
	$data = explode("_",$data);
	
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$company_id =$data[2];
	$barcode_no =trim($data[3]);

	$search_field_cond="";
	if(trim($data[0])!="")
	{
		if($search_by==1) $search_field_cond="and b.po_number like '$search_string'";
		else if($search_by==2) $search_field_cond="and b.grouping like '$search_string'";
		else if($search_by==3) $search_field_cond="and b.file_no like '$search_string'";
	}
	
	if($barcode_no!="")
	{
		$barcode_cond="and a.barcode_no='$barcode_no'";
	}
	
	$scanned_barcode_arr=array();
	$barcodeData=sql_select( "select barcode_no from pro_roll_details where entry_form=64 and status_active=1 and is_deleted=0");
	foreach ($barcodeData as $row)
	{
		$scanned_barcode_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
	}

	$product_arr=return_library_array( "select id, product_name_details from product_details_master where item_category_id=13",'id','product_name_details');
	$batch_barcode_arr=array();
	$barcodeData_batch=sql_select("SELECT a.recv_number, b.prod_id,c.roll_id, d.po_number FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, wo_po_break_down d WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.company_id=$company_id and a.entry_form in (2,22) and c.entry_form in (2,22) and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 ");
	foreach ($barcodeData_batch as $val)
	{
		$batch_barcode_arr[$val[csf('roll_id')]]['prod_id']=$val[csf('prod_id')];
	}
	
	$sql= "select a.barcode_no,a.qc_pass_qnty,a.roll_id,a.roll_no, b.po_number, b.grouping, b.file_no, b.pub_shipment_date, b.job_no_mst from pro_roll_details a,wo_po_break_down b where a.entry_form=62 and a.po_breakdown_id=b.id and a.status_active=1 and a.is_deleted=0 $search_field_cond $barcode_cond order by a.barcode_no";
	//echo $sql;//die;
	$result = sql_select($sql);
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="890" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="150">Fabric Description</th>
            <th width="100">Job No</th>
            <th width="110">Order No</th>
            <th width="90">Internal Ref.</th>
            <th width="70">File No</th>
            <th width="80">Shipment Date</th>
            <th width="100">Barcode No</th>
            <th width="60">Roll No</th>
            <th>Roll Qty.</th>
        </thead>
	</table>
	<div style="width:910px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="890" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            {  
				if($scanned_barcode_arr[$row[csf('barcode_no')]]=="")
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)"> 
						<td width="40">
							<? echo $i; ?>
							 <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
						</td>
						<td width="150"><p><? echo $product_arr[$batch_barcode_arr[$val[csf('roll_id')]]['prod_id']]; ?></p></td>
						<td width="100"><p><? echo $row[csf('job_no_mst')]; ?></p></td>
						<td width="110"><p><? echo $row[csf('po_number')]; ?></p></td>
                        <td width="90"><p><? echo $row[csf('grouping')]; ?></p></td>
                        <td width="70"><p><? echo $row[csf('file_no')]; ?></p></td>
						<td width="80" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
						<td width="100"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
						<td width="60"><? echo $row[csf('roll_no')]; ?></td>
						<td align="right"><? echo number_format($row[csf('qc_pass_qnty')],2); ?></td>
					</tr>
				<?
					$i++;
				}
			}
        	?>
        </table>
    </div>
    <table width="720">
        <tr>
            <td align="center" >
                <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
            </td>
        </tr>
    </table>
<?	
exit();
}

if($action=="load_drop_down_po")
{
	$data=explode("**",$data);
	$booking_no=$data[0];
	$color_id=$data[1];
	echo create_drop_down( "cboPoNo_1", 130, "SELECT a.id, a.po_number FROM wo_po_break_down a, wo_booking_dtls b WHERE a.id=b.po_break_down_id and b.booking_no='$booking_no' and b.fabric_color_id='$color_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.po_number","id,po_number", 1, "-- Select Po Number --",'0',"load_item_desc(this.value,this.id );",'', "", "", "", "", "", "", "cboPoNo[]");  
	exit();
}

if($action=="load_drop_down_po_from_program")
{
	$data=explode("**",$data);
	$program_id=$data[0];
	$row_no=$data[1];
	$booking_no=$data[2];
	$color_id=trim($data[3]);
	
	if($program_id==0)
	{
		echo create_drop_down( "cboPoNo_".$row_no, 130, "SELECT a.id, a.po_number FROM wo_po_break_down a, wo_booking_dtls b WHERE a.id=b.po_break_down_id and b.booking_no='$booking_no' and b.fabric_color_id='$color_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.po_number","id,po_number", 1, "-- Select Po Number --",'0',"load_item_desc(this.value,this.id );",'', "", "", "", "", "", "", "cboPoNo[]");  
	}
	else
	{
		echo create_drop_down( "cboPoNo_".$row_no, 130, "SELECT b.id, b.po_number FROM ppl_planning_entry_plan_dtls a, wo_po_break_down b, wo_booking_dtls c WHERE a.po_id=b.id and b.id=c.po_break_down_id and a.booking_no=c.booking_no and c.booking_no='$booking_no' and c.fabric_color_id='$color_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.dtls_id='$program_id' group by b.id, b.po_number","id,po_number", 1, "-- Select Po Number --",'0','load_item_desc(this.value,this.id );','', "", "", "", "", "", "", "cboPoNo[]");
	}
	exit();
}


if($action=="load_drop_down_program")
{
	echo create_drop_down( "cboProgramNo_1", 80, "SELECT b.id as program_id, b.id as program_no FROM ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b WHERE a.id=b.mst_id and a.booking_no='$data' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.id","program_id,program_no", 1, "-- Select --",'0','load_item_desc(this.value,this.id );','', "", "", "", "", "", "", "cboProgramNo[]");  
	exit();
}

if($action=="load_drop_down_program_against_po")
{
	$data=explode("**",$data);
	$po_id=$data[0];
	$row_no=$data[1];
	
	echo create_drop_down( "cboProgramNo_".$row_no, 80, "SELECT b.id as program_id, b.id as program_no FROM ppl_planning_entry_plan_dtls a, ppl_planning_info_entry_dtls b WHERE a.dtls_id=b.id and a.po_id='$po_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","program_id,program_no", 1, "-- Select --",'0','load_item_desc(this.value,this.id );','', "", "", "", "", "", "", "cboProgramNo[]");  
	exit();
}

if($action=="load_drop_down_item_desc")
{
	$data=explode("**",$data);
	$po_id=$data[0];
	$row_no=$data[1];
	$booking_without_order=$data[2];
	$program_no=$data[3];
	$batch_maintained=$data[4];
	
	if($batch_maintained==0)
	{
		if($booking_without_order==1)
		{
			$sql="select a.id, a.product_name_details from product_details_master a, inv_grey_fabric_issue_dtls b, inv_issue_master c where a.id=b.prod_id and b.mst_id=c.id and c.booking_no='$po_id' and c.issue_basis=1 and c.issue_purpose=8 and c.entry_form in(16) and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details";
		}
		else
		{
			if($program_no>0)
			{
				//$sql="select a.id, a.product_name_details from product_details_master a, inv_grey_fabric_issue_dtls b, inv_issue_master c, order_wise_pro_details d where a.id=b.prod_id and b.mst_id=c.id and a.id=d.prod_id and d.entry_form=16 and d.trans_type=2 and b.program_no=$program_no and d.po_breakdown_id=$po_id and c.issue_basis=3 and c.entry_form=16 and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details";
				$sql="select a.id, a.product_name_details from inv_issue_master c, inv_grey_fabric_issue_dtls b, order_wise_pro_details d, product_details_master a where c.id=b.mst_id and b.id=d.dtls_id and b.prod_id=a.id and d.entry_form=16 and d.trans_type=2 and b.program_no=$program_no and d.po_breakdown_id=$po_id and c.issue_basis=3 and c.entry_form=16 and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details";
			}
			else
			{
				$sql="select a.id, a.product_name_details from product_details_master a, order_wise_pro_details b where a.id=b.prod_id and b.po_breakdown_id=$po_id and b.entry_form in(16) and b.trans_type in(2) and a.item_category_id=13 and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details";
			}
		}
	}
	else
	{
		if($booking_without_order==1)
		{
			$sql="select a.id, a.product_name_details from product_details_master a, pro_grey_prod_entry_dtls b, inv_receive_master c where a.id=b.prod_id and b.mst_id=c.id and c.booking_no='$po_id' and c.booking_without_order=1 and c.entry_form in(2,22) and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details";
		}
		else
		{
			if($program_no>0)
			{
				//$sql="select a.id, a.product_name_details from product_details_master a, pro_grey_prod_entry_dtls b, inv_receive_master c, order_wise_pro_details d where a.id=b.prod_id and b.mst_id=c.id and a.id=d.prod_id and d.entry_form=2 and d.trans_type=1 and c.booking_id=$program_no and c.receive_basis=2 and d.po_breakdown_id=$po_id and c.booking_without_order=0 and receive_basis=2 and c.entry_form=2 and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details";
				$sql="select a.id, a.product_name_details from inv_receive_master c, pro_grey_prod_entry_dtls b, order_wise_pro_details d, product_details_master a where c.id=b.mst_id and b.id=d.dtls_id and b.prod_id=a.id and d.entry_form=2 and d.trans_type=1 and c.booking_id=$program_no and c.receive_basis=2 and d.po_breakdown_id=$po_id and c.booking_without_order=0 and c.receive_basis=2 and c.entry_form=2 and b.status_active=1 and b.is_deleted=0 group by  a.id, a.product_name_details";
			}
			else
			{
				$sql="select a.id, a.product_name_details from product_details_master a, order_wise_pro_details b where a.id=b.prod_id and b.po_breakdown_id=$po_id and b.entry_form in(2,13,22) and b.trans_type in(1,5) and a.item_category_id=13 and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details";
			}
		}
	}
	//echo $sql;// and a.current_stock>0die;
	echo create_drop_down( "cboItemDesc_".$row_no, 180, $sql,'id,product_name_details', 1, "-- Select Item Desc --",'0','load_body_part(this.value,this.id );','', "", "", "", "", "", "", "cboItemDesc[]");  
	exit();
}

if($action=="load_drop_down_body_part")
{
	$data=explode("**",$data);
	$booking_po_id=$data[0];
	$row_no=$data[1];
	$booking_without_order=$data[2];
	$prod_id=$data[3];
	
	$body_part_ids='';
	if($booking_without_order==1)
	{
		$sql="select b.body_part_id from pro_grey_prod_entry_dtls b, inv_receive_master c where b.mst_id=c.id and c.booking_no='$booking_po_id' and c.booking_without_order=1 and c.entry_form in(2,22) and b.status_active=1 and b.is_deleted=0 and b.prod_id=$prod_id group by b.body_part_id";
	}
	else
	{
		$sql="select a.body_part_id from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.po_breakdown_id='$booking_po_id' and b.entry_form in(2,22) and b.trans_type=1 and b.status_active=1 and b.is_deleted=0 and b.prod_id=$prod_id group by a.body_part_id";
	}
	
	$result=sql_select($sql);
	foreach($result as $row)
	{
		$body_part_ids.=$row[csf('body_part_id')].",";
	}
	
	$body_part_ids=chop($body_part_ids,',');
	if($body_part_ids=="") {$body_part_ids=0;}
	
	echo create_drop_down( "cboBodyPart_".$row_no, 120, $body_part,"", 1, "-- Select --", 0,'',0,$body_part_ids, "", "", "", "", "", "cboBodyPart[]");  
	exit();
	
}

if($action=="roll_popup")
{
	echo load_html_head_contents("Roll Info", "../../", 1, 1,'','1','');
	extract($_REQUEST);
?> 

	<script>
		/*$(document).ready(function(e) {
            setFilterGrid('tbl_list_search',-1);
        });*/
		
		function js_set_value(data)
		{
			var data=data.split("_");
			$('#hidden_roll_table_id').val(data[0]);
			$('#hidden_roll_no').val(data[1]);
			$('#hidden_roll_qnty').val(data[2]);
			parent.emailwindow.hide();
		}
	
    </script>

</head>

<body>
<div align="center" style="width:550px;">
	<form name="searchwofrm" id="searchwofrm">
		<fieldset style="width:100%; margin-left:20px">
         	<input type="hidden" name="hidden_roll_table_id" id="hidden_roll_table_id" class="text_boxes" value="">  
        	<input type="hidden" name="hidden_roll_no" id="hidden_roll_no" class="text_boxes" value="">   
            <input type="hidden" name="hidden_roll_qnty" id="hidden_roll_qnty" class="text_boxes" value="">      
        	<?
				
				$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
				$po_arr=array(); $po_buyer_arr=array();
				$sql_po=sql_select( "select b.id, b.po_number, a.buyer_name from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst");
				
				foreach($sql_po as $row)
				{
					$po_arr[$row[csf('id')]]=$row[csf('po_number')];
					$po_buyer_arr[$row[csf('id')]]=$buyer_arr[$row[csf('buyer_name')]];
				}
				
				$sql="select a.id, a.po_breakdown_id, a.roll_no, a.qnty from pro_roll_details a, inv_receive_master b where a.mst_id=b.id and b.company_id=$cbo_company_id and a.entry_form=1 and a.roll_no>0 and a.status_active=1 and a.is_deleted=0";
				
				$po_arr=return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');
				$arr=array(0=>$po_arr,1=>$po_buyer_arr);
				 
				echo create_list_view("tbl_list_search", "Order Number,Buyer Name,Roll No,Roll Qnty", "130,120,80","510","280",0, $sql, "js_set_value", "id,roll_no,qnty", "", 1, "po_breakdown_id,po_breakdown_id,0,0", $arr, "po_breakdown_id,po_breakdown_id,roll_no,qnty", "","setFilterGrid('tbl_list_search',-1)",'0,0,0,2','');

			?>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="po_popup")
{
  	echo load_html_head_contents("Order Info","../../", 1, 1, '','1','');
	extract($_REQUEST);
	
?>
	<script>
		var job_no='';
		var hide_job_no='<? echo $hide_job_no; ?>';
		var no_of_row=<? echo $no_of_row; ?>;
		
		function js_set_value( po_id,po_no,job_no)
		{
			if(no_of_row>1 && hide_job_no!="")
			{
				if(job_no!=hide_job_no)
				{
					alert("Job Mix Not Allowed");
					return;
				}
			}
			
			document.getElementById('po_id').value=po_id;
			document.getElementById('po_no').value=po_no;
			document.getElementById('job_no').value=job_no;
			parent.emailwindow.hide();
		}
	
    </script>

</head>

<body>
	<fieldset style="width:620px;margin-left:10px">
        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table cellpadding="0" cellspacing="0" width="620" class="rpt_table">
                <thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th>Search</th>
                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        <input type="hidden" name="po_id" id="po_id" value="">
                        <input type="hidden" name="po_no" id="po_no" value="">
                        <input type="hidden" name="job_no" id="job_no" value="">
                    </th> 
                </thead>
                <tr class="general">
                    <td align="center">
                        <?
							if($batch_against==5)
							{
                            	echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", 0 ); 
							}
							else
							{
								echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (2,3)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", 0 ); 
							}
                        ?>       
                    </td>
                    <td align="center">	
                        <?
                            $search_by_arr=array(1=>"PO No",2=>"Job No");
                            echo create_drop_down("cbo_search_by", 170, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                        ?>
                    </td>                 
                    <td align="center">				
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td> 						
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('cbo_buyer_name').value+'_'+<? echo $batch_against; ?>, 'create_po_search_list_view', 'search_div', 'batch_creation_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                    </td>
                </tr>
            </table>
            <div id="search_div" style="margin-top:10px"></div>   
        </form>
    </fieldset>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_po_search_list_view")
{
	$data=explode('_',$data);
	
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$company_id =$data[2];
	$buyer_id =$data[3];
	$batch_against =$data[4];
	
	if($batch_against==5)
	{
		if($search_by==1)
			$search_field='b.po_number';
		else
			$search_field='a.job_no';
	}
	else if($batch_against==4)
	{
		if($search_by==1)
			$search_field='b.order_no';
		else
			$search_field='a.subcon_job';
	}
		
	if($buyer_id==0) { echo "Please Select Buyer First."; die; }
	
	if($batch_against==5)
	{
		$sql = "select a.job_no, a.style_ref_no, a.order_uom, b.id, b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company_id and a.buyer_name=$buyer_id and $search_field like '$search_string' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0"; 
		 
	}
	else if($batch_against==4)
	{
		$sql = "select a.subcon_job as job_no, b.id, b.cust_style_ref as style_ref_no, b.order_uom, b.order_no as po_number, b.order_quantity as po_qnty_in_pcs, b.delivery_date as pub_shipment_date from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.company_id=$company_id and a.party_id=$buyer_id and $search_field like '$search_string' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0"; 
		 
	}
	
	?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" >
            <thead>
                <th width="40">SL</th>
                <th width="100">Job No</th>
                <th width="110">Style No</th>
                <th width="110">PO No</th>
                <th width="90">PO Quantity</th>
                <th width="50">UOM</th>
                <th><? if($batch_against==5) echo "Shipment"; else if($batch_against==4) echo "Delivery"; ?> Date</th>
            </thead>
        </table>
        <div style="width:618px; overflow-y:scroll; max-height:240px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" id="tbl_list_search" >
            <?
				$i=1;
				$nameArray=sql_select( $sql );
				foreach ($nameArray as $selectResult)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>,'<? echo $selectResult[csf('po_number')]; ?>','<? echo $selectResult[csf('job_no')]; ?>')"> 
                        <td width="40" align="center"><? echo $i; ?></td>	
                        <td width="100"><p><? echo $selectResult[csf('job_no')]; ?></p></td>
                        <td width="110"><p><? echo $selectResult[csf('style_ref_no')]; ?></p></td>
                        <td width="110"><p><? echo $selectResult[csf('po_number')]; ?></p></td>
                        <td width="90" align="right"><? echo $selectResult[csf('po_qnty_in_pcs')]; ?></td> 
                        <td width="50" align="center"><p><? echo $unit_of_measurement[$selectResult[csf('order_uom')]]; ?></p></td>
                        <td align="center"><? echo change_date_format($selectResult[csf('pub_shipment_date')]); ?></td>	
                    </tr>
                <?
                	$i++;
				}
			?>
            </table>
        </div>
	</div>           
<?
exit();	
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$product_array=return_library_array( "select id, product_name_details from product_details_master where item_category_id=13",'id','product_name_details');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	
	$po_batch_no_arr=array();
	$po_batch_data=sql_select("select max(a.po_batch_no) as po_batch_no, a.po_id, b.color_id from pro_batch_create_dtls a, pro_batch_create_mst b where a.mst_id=b.id group by b.color_id, a.po_id");
	foreach($po_batch_data as $row)
	{
		$po_batch_no_arr[$row[csf('color_id')]][$row[csf('po_id')]]=$row[csf('po_batch_no')];
	}
	
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
		
		if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con); die;}
		
		$batch_update_id=''; $batch_no_creation=str_replace("'","",$batch_no_creation); $roll_maintained=str_replace("'","",$roll_maintained);
		$color_id=return_id( $txt_batch_color, $color_arr, "lib_color", "id,color_name");
		
		if(str_replace("'","",$update_id)=="")
		{
			$id=return_next_id( "id", "pro_batch_create_mst", 1 ) ;
			
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
					check_table_status( $_SESSION['menu_id'],0);
					echo "11**0"; 
					disconnect($con);
					die;			
				}
				
				$txt_batch_number=$txt_batch_number;
			}
			//cbo_machine_name
			$field_array="id, batch_no, batch_date, batch_against, batch_for, company_id, booking_no_id, booking_no, booking_without_order, extention_no, color_id, batch_weight, total_trims_weight,save_string, color_range_id, process_id, organic, dur_req_hr, dur_req_min, collar_qty, cuff_qty, dyeing_machine, remarks, inserted_by, insert_date";
			
			$data_array="(".$id.",".$txt_batch_number.",".$txt_batch_date.",".$cbo_batch_against.",".$cbo_batch_for.",".$cbo_company_id.",".$txt_booking_no_id.",".$txt_booking_no.",".$booking_without_order.",".$txt_ext_no.",".$color_id.",".$txt_batch_weight.",".$txt_tot_trims_weight.",".$save_data.",".$cbo_color_range.",".$txt_process_id.",".$txt_organic.",".$txt_du_req_hr.",".$txt_du_req_min.",".$txt_color_qty.",".$txt_cuff_qty.",".$cbo_machine_name.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
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
					check_table_status( $_SESSION['menu_id'],0);
					echo "11**0"; 
					disconnect($con);
					die;			
				}
			}
			
			$field_array_update="batch_no*batch_date*batch_against*batch_for*company_id*booking_no_id*booking_no*booking_without_order*extention_no*color_id*batch_weight*total_trims_weight*save_string*color_range_id*process_id*organic*dur_req_hr*dur_req_min*collar_qty*cuff_qty*dyeing_machine*remarks*updated_by*update_date";
			
			$data_array_update=$txt_batch_number."*".$txt_batch_date."*".$cbo_batch_against."*".$cbo_batch_for."*".$cbo_company_id."*".$txt_booking_no_id."*".$txt_booking_no."*".$booking_without_order."*".$txt_ext_no."*".$color_id."*".$txt_batch_weight."*".$txt_tot_trims_weight."*".$save_data."*".$cbo_color_range."*".$txt_process_id."*".$txt_organic."*".$txt_du_req_hr."*".$txt_du_req_min."*".$txt_color_qty."*".$txt_cuff_qty."*".$cbo_machine_name."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			/*$rID=sql_update("pro_batch_create_mst",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID) $flag=1; else $flag=0; */
		}
		
		$id_dtls=return_next_id( "id", "pro_batch_create_dtls", 1 ) ;
		$id_roll = return_next_id( "id", "pro_roll_details", 1 );
		
		$field_array_dtls="id, mst_id, program_no, po_id, po_batch_no, prod_id, item_description, body_part_id, width_dia_type, roll_no, roll_id,barcode_no,batch_qnty, inserted_by, insert_date";
		$field_array_roll="id, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, roll_no, roll_id, barcode_no, inserted_by, insert_date";
		$roll_table_id='';
		
		for($i=1;$i<=$total_row;$i++)
		{
			if(str_replace("'","",$cbo_batch_against)==5)
			{
				$po_id="poId_".$i;  
			}
			else
			{
				$po_id="cboPoNo_".$i;  
			}
			
			$program_no="cboProgramNo_".$i;
			$prod_id="cboItemDesc_".$i;
			$body_part_id="cboBodyPart_".$i;
			$txtRollNo="txtRollNo_".$i;
			$hideRollNo="hideRollNo_".$i;
			$txtBatchQnty="txtBatchQnty_".$i;
			$cboDiaWidthType="cboDiaWidthType_".$i;
			$barcodeNo="barcodeNo_".$i;
			$ItemDesc=$product_array[str_replace("'","",$$prod_id)];
			
			$po_batch_no=$po_batch_no_arr[$color_id][str_replace("'","",$$po_id)]+1;
		
			if(str_replace("'","",$$hideRollNo)!="")
			{
				//if($roll_table_id=="") $roll_table_id=str_replace("'","",$$hideRollNo); else $roll_table_id.=",".str_replace("'","",$$hideRollNo);
				if($data_array_roll!="") $data_array_roll.=","; 
				$data_array_roll.="(".$id_roll.",".$batch_update_id.",".$id_dtls.",".$$po_id.",64,".$$txtBatchQnty.",".$$txtRollNo.",".$$hideRollNo.",".$$barcodeNo.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				$id_roll = $id_roll+1;
			}
			
			if($data_array_dtls!="") $data_array_dtls.=","; 	
			$data_array_dtls.="(".$id_dtls.",".$batch_update_id.",".$$program_no.",".$$po_id.",'".$po_batch_no."',".$$prod_id.",'".$ItemDesc."',".$$body_part_id.",".$$cboDiaWidthType.",".$$txtRollNo.",".$$hideRollNo.",".$$barcodeNo.",".$$txtBatchQnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; 
			
			$id_dtls=$id_dtls+1;
		}
		$id_dtls_trim=return_next_id( "id","pro_batch_trims_dtls", 1 ) ;
		$field_array_dtls_trims="id,mst_id,item_description,trims_wgt_qnty,remarks,inserted_by, insert_date,status_active,is_deleted"; 
		$save_string=explode(",",str_replace("'","",$save_data));
		for($i=0;$i<count($save_string);$i++)
		{
			$data=explode("_",$save_string[$i]);  
			$item_des=$data[0];
			$trims_qty=$data[1];
			$remarks=$data[2];
			//if($trims_qty=='') $trims_qty=0;else $trims_qty=$trims_qty;
			if($trims_qty>0)
			{
			if ($i!=0) $data_array_dtls_trims.=",";
			$data_array_dtls_trims.="(".$id_dtls_trim.",".$batch_update_id.",'".$item_des."',".$trims_qty.",'".$remarks."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)"; 
			$id_dtls_trim=$id_dtls_trim+1;
			}
		}
		
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
			//echo "insert into pro_roll_details (".$field_array_roll.") values ".$data_array_roll;die;
		
		
		$rID2=sql_insert("pro_batch_create_dtls",$field_array_dtls,$data_array_dtls,1);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} 
		
		if($data_array_roll!="" && $roll_maintained==1)
		{
			$rID3=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
			if($flag==1) 
			{
				if($rID3) $flag=1; else $flag=0; 
			} 
		}
		
		if($data_array_dtls_trims!="")
		{
			//echo "insert into pro_batch_trims_dtls (".$field_array_dtls_trims.") values ".$data_array_dtls_trims;die;
			$rID4=sql_insert("pro_batch_trims_dtls",$field_array_dtls_trims,$data_array_dtls_trims,1);
			if($flag==1)
			{
				if($rID4) $flag=1; else $flag=0; 
			}
		}
		//echo $rID.'='.$rID2.'='.$rID3.'='.$rID4;die;
		/*//echo $flag;die;
		if($roll_table_id!="")
		{
			$rID4=sql_multirow_update("pro_roll_details","roll_used",1,"id",$roll_table_id,1);
			if($flag==1) 
			{
				if($rID4) $flag=1; else $flag=0; 
			} 
		}*/
		
		check_table_status( $_SESSION['menu_id'],0);
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".$batch_update_id."**".$serial_no."**".str_replace("'","",$txt_batch_number);
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
				echo "0**".$batch_update_id."**".$serial_no."**".str_replace("'","",$txt_batch_number);
			}
			else
			{
				oci_rollback($con);
				echo "5**0**0";
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
		
		$prev_batch_data_arr=array();
		$prev_batch_data=sql_select("select a.id as dtls_id, a.po_id, b.color_id from pro_batch_create_dtls a, pro_batch_create_mst b where a.mst_id=b.id and b.id=$update_id");
		foreach($prev_batch_data as $row)
		{
			$prev_batch_data_arr[$row[csf('dtls_id')]]['po_id']=$row[csf('po_id')];
			$prev_batch_data_arr[$row[csf('dtls_id')]]['color']=$row[csf('color_id')];
		}

		$color_id=return_id( $txt_batch_color, $color_arr, "lib_color", "id,color_name");
		$flag=1; $batch_no_creation=str_replace("'","",$batch_no_creation); $roll_maintained=str_replace("'","",$roll_maintained);
		
		if(str_replace("'","",$cbo_batch_against)==2 && str_replace("'","",$hide_update_id)=="")
		{
			$id=return_next_id( "id", "pro_batch_create_mst", 1 ) ;
			
			$batch_update_id=$id;
			$serial_no=date("y",strtotime($pc_date_time))."-".$id;
					 
		 	if(is_duplicate_field( "batch_no", "pro_batch_create_mst", "batch_no=$txt_batch_number and $extention_no_cond and status_active=1 and is_deleted=0" )==1)
			{
				check_table_status( $_SESSION['menu_id'],0);
				echo "11**0"; 
				disconnect($con);
				die;			
			}
			//cbo_machine_name 
			$field_array="id, batch_no, batch_date, batch_against, batch_for, company_id, booking_no_id, booking_no, booking_without_order, extention_no, color_id, batch_weight, total_trims_weight,save_string, color_range_id, process_id, organic, dur_req_hr, dur_req_min, re_dyeing_from, collar_qty, cuff_qty, dyeing_machine, remarks, inserted_by, insert_date";
			
			$data_array="(".$id.",".$txt_batch_number.",".$txt_batch_date.",".$cbo_batch_against.",".$cbo_batch_for.",".$cbo_company_id.",".$txt_booking_no_id.",".$txt_booking_no.",".$booking_without_order.",".$txt_ext_no.",".$color_id.",".$txt_batch_weight.",".$txt_tot_trims_weight.",".$save_data.",".$cbo_color_range.",".$txt_process_id.",".$txt_organic.",".$txt_du_req_hr.",".$txt_du_req_min.",".$update_id.",".$txt_color_qty.",".$txt_cuff_qty.",".$cbo_machine_name.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					 
			//echo "insert into pro_batch_create_mst (".$field_array.") values ".$data_array;die;
			/*$rID=sql_insert("pro_batch_create_mst",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0;*/
			
			$id_dtls=return_next_id( "id", "pro_batch_create_dtls", 1 ) ;
			$id_roll = return_next_id( "id", "pro_roll_details", 1 );
			
			$field_array_dtls="id, mst_id, program_no, po_id, po_batch_no, prod_id, item_description, body_part_id, width_dia_type,roll_no,roll_id,barcode_no,batch_qnty,inserted_by,insert_date";
			$field_array_roll="id, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, roll_no, roll_id, barcode_no, inserted_by, insert_date";
			
			$roll_table_id='';
			
			for($i=1;$i<=$total_row;$i++)
			{
				if(str_replace("'","",$hide_batch_against)==5)
				{
					$po_id="poId_".$i;  
				}
				else
				{
					$po_id="cboPoNo_".$i;  
				}
				
				$program_no="cboProgramNo_".$i;
				$prod_id="cboItemDesc_".$i;
				$body_part_id="cboBodyPart_".$i;
				$txtRollNo="txtRollNo_".$i;
				$hideRollNo="hideRollNo_".$i;
				$txtBatchQnty="txtBatchQnty_".$i;
				$ItemDesc=$product_array[str_replace("'","",$$prod_id)];
				$po_batch_no="txtPoBatchNo_".$i;
				$updateIdDtls="updateIdDtls_".$i;
				$cboDiaWidthType="cboDiaWidthType_".$i;
				$barcodeNo="barcodeNo_".$i;
			
				if(str_replace("'","",$$hideRollNo)!="")
				{
					//if($roll_table_id=="") $roll_table_id=str_replace("'","",$$hideRollNo); else $roll_table_id.=",".str_replace("'","",$$hideRollNo);
					
					if($data_array_roll!="") $data_array_roll.=","; 
					$data_array_roll.="(".$id_roll.",".$batch_update_id.",".$id_dtls.",".$$po_id.",64,".$$txtBatchQnty.",".$$txtRollNo.",".$$hideRollNo.",".$$barcodeNo.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					
					$id_roll = $id_roll+1;
				}
				
				if($data_array_dtls!="") $data_array_dtls.=","; 	
				$data_array_dtls.="(".$id_dtls.",".$batch_update_id.",".$$program_no.",".$$po_id.",".$$po_batch_no.",".$$prod_id.",'".$ItemDesc."',".$$body_part_id.",".$$cboDiaWidthType.",".$$txtRollNo.",".$$hideRollNo.",".$$barcodeNo.",".$$txtBatchQnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; 
				
				$id_dtls=$id_dtls+1;
			}
			$id_dtls_trim=return_next_id( "id","pro_batch_trims_dtls", 1 ) ;
			$field_array_dtls_trims="id,mst_id,item_description,trims_wgt_qnty,remarks,inserted_by, insert_date,status_active,is_deleted"; 
			$save_string=explode(",",str_replace("'","",$save_data));
			for($i=0;$i<count($save_string);$i++)
			{
			$data=explode("_",$save_string[$i]);  
			$item_des=$data[0];
			$trims_qty=$data[1];
			$remarks=$data[2];
			//if($trims_qty=='') $trims_qty=0;else $trims_qty=$trims_qty;
				if($trims_qty>0)
				{
				if ($i!=0) $data_array_dtls_trims.=",";
				$data_array_dtls_trims.="(".$id_dtls_trim.",".$batch_update_id.",'".$item_des."',".$trims_qty.",'".$remarks."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)"; 
				$id_dtls_trim=$id_dtls_trim+1;
				}
			}
			$rID=sql_insert("pro_batch_create_mst",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0;
			
			//echo "insert into pro_batch_create_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
			$rID2=sql_insert("pro_batch_create_dtls",$field_array_dtls,$data_array_dtls,1);
			if($flag==1) 
			{
				if($rID2) $flag=1; else $flag=0; 
			} 
			
			if($data_array_roll!="" && $roll_maintained==1)
			{
				$rID3=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
				if($flag==1) 
				{
					if($rID3) $flag=1; else $flag=0; 
				} 
			}
			$delete_trims_dtls=execute_query( "delete from pro_batch_trims_dtls where mst_id=$batch_update_id",0);
			if($flag==1) 
			{
				if($delete_trims_dtls) $flag=1; else $flag=0; 
			} 
			if($data_array_dtls_trims!="")
			{
				$rID6=sql_insert("pro_batch_trims_dtls",$field_array_dtls_trims,$data_array_dtls_trims,1);
				if($flag==1)
				{
					if($rID6) $flag=1; else $flag=0; 
				}
			}
			
			/*if($roll_table_id!="")
			{
				$rID4=sql_multirow_update("pro_roll_details","roll_used",1,"id",$roll_table_id,1);
				if($flag==1) 
				{
					if($rID4) $flag=1; else $flag=0; 
				} 
			}*/
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
					check_table_status( $_SESSION['menu_id'],0);
					echo "11**0"; 
					disconnect($con);
					die;			
				}
			}
			
			$field_array_update="batch_no*batch_date*batch_against*batch_for*company_id*booking_no_id*booking_no*booking_without_order*extention_no*color_id*batch_weight*total_trims_weight*save_string*color_range_id*process_id*organic*dur_req_hr*dur_req_min*collar_qty*cuff_qty*dyeing_machine*remarks*updated_by*update_date";
			
			$data_array_update=$txt_batch_number."*".$txt_batch_date."*".$cbo_batch_against."*".$cbo_batch_for."*".$cbo_company_id."*".$txt_booking_no_id."*".$txt_booking_no."*".$booking_without_order."*".$txt_ext_no."*".$color_id."*".$txt_batch_weight."*".$txt_tot_trims_weight."*".$save_data."*".$cbo_color_range."*".$txt_process_id."*".$txt_organic."*".$txt_du_req_hr."*".$txt_du_req_min."*".$txt_color_qty."*".$txt_cuff_qty."*".$cbo_machine_name."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
		/*$rID=sql_update("pro_batch_create_mst",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID) $flag=1; else $flag=0;*/
			
			$id_dtls_batch=return_next_id( "id", "pro_batch_create_dtls", 1 ) ;
			$id_roll = return_next_id( "id", "pro_roll_details", 1 );
			
			$field_array_dtls="id, mst_id, program_no, po_id, po_batch_no, prod_id,item_description, body_part_id, width_dia_type, roll_no,roll_id,barcode_no,batch_qnty,inserted_by,insert_date";
			$field_array_dtls_update="program_no*po_id*po_batch_no*prod_id*item_description*body_part_id*width_dia_type*roll_no*roll_id*barcode_no*batch_qnty*updated_by*update_date";
			$field_array_roll="id, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, roll_no, roll_id, barcode_no, inserted_by, insert_date";
			$roll_table_id='';
			
			/*$delete_roll=execute_query("delete from pro_roll_details where mst_id=$update_id and entry_form=64",1);
			if($flag==1) 
			{
				if($delete_roll) $flag=1; else $flag=0; 
			} */
	
			for($i=1;$i<=$total_row;$i++)
			{
				if(str_replace("'","",$cbo_batch_against)==5)
				{
					$po_id="poId_".$i;  
				}
				else
				{
					$po_id="cboPoNo_".$i;  
				}
				
				$program_no="cboProgramNo_".$i;
				$prod_id="cboItemDesc_".$i;
				$body_part_id="cboBodyPart_".$i;
				$txtRollNo="txtRollNo_".$i;
				$hideRollNo="hideRollNo_".$i;
				$txtBatchQnty="txtBatchQnty_".$i;
				$ItemDesc=$product_array[str_replace("'","",$$prod_id)];
				$txtPoBatchNo="txtPoBatchNo_".$i;
				$updateIdDtls="updateIdDtls_".$i;
				$cboDiaWidthType="cboDiaWidthType_".$i;
				$barcodeNo="barcodeNo_".$i;
				
				if(str_replace("'","",$$updateIdDtls)!="")
				{
					$prev_po_id=$prev_batch_data_arr[str_replace("'",'',$$updateIdDtls)]['po_id'];
					$prev_color_id=$prev_batch_data_arr[str_replace("'",'',$$updateIdDtls)]['color'];
					
					if($prev_po_id==str_replace("'","",$$po_id) && $prev_color_id==$color_id)
					{
						$po_batch_no=str_replace("'","",$$txtPoBatchNo);
						$poBatchNoArr[$prev_color_id][$prev_po_id]=$po_batch_no;
					}
					else
					{
						if($poBatchNoArr[$color_id][str_replace("'","",$$po_id)]=="")
						{
							$po_batch_no=$po_batch_no_arr[$color_id][str_replace("'","",$$po_id)]+1;
							$poBatchNoArr[$color_id][str_replace("'","",$$po_id)]=$po_batch_no;
						}
						else
						{
							$po_batch_no=$poBatchNoArr[$color_id][str_replace("'","",$$po_id)];
						}
					}
					
					$id_arr[]=str_replace("'",'',$$updateIdDtls);
					$data_array_dtls_update[str_replace("'",'',$$updateIdDtls)] = explode("*",($$program_no."*".$$po_id."*'".$po_batch_no."'*".$$prod_id."*'".$ItemDesc."'*".$$body_part_id."*".$$cboDiaWidthType."*".$$txtRollNo."*".$$hideRollNo."*".$$barcodeNo."*".$$txtBatchQnty."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					
					$id_dtls=str_replace("'",'',$$updateIdDtls);
				}
				else
				{
					if($poBatchNoArr[$color_id][str_replace("'","",$$po_id)]=="")
					{
						$po_batch_no=$po_batch_no_arr[$color_id][str_replace("'","",$$po_id)]+1;
						$poBatchNoArr[$color_id][str_replace("'","",$$po_id)]=$po_batch_no;
					}
					else
					{
						$po_batch_no=$poBatchNoArr[$color_id][str_replace("'","",$$po_id)];
					}
					
					if($data_array_dtls!="") $data_array_dtls.=","; 	
					$data_array_dtls.="(".$id_dtls_batch.",".$batch_update_id.",".$$program_no.",".$$po_id.",'".$po_batch_no."',".$$prod_id.",'".$ItemDesc."',".$$body_part_id.",".$$cboDiaWidthType.",".$$txtRollNo.",".$$hideRollNo.",".$$barcodeNo.",".$$txtBatchQnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; 
					
					$id_dtls_batch=$id_dtls_batch+1;
					$id_dtls=$id_dtls_batch;
				}
				
				if(str_replace("'","",$$hideRollNo)!="")
				{
					if($data_array_roll!="") $data_array_roll.=","; 
					$data_array_roll.="(".$id_roll.",".$batch_update_id.",".$id_dtls.",".$$po_id.",64,".$$txtBatchQnty.",".$$txtRollNo.",".$$hideRollNo.",".$$barcodeNo.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					
					$id_roll = $id_roll+1;
				}
			}
			
				$id_dtls_trim=return_next_id( "id","pro_batch_trims_dtls", 1 ) ;
				$field_array_dtls_trims="id,mst_id,item_description,trims_wgt_qnty,remarks,inserted_by, insert_date,status_active,is_deleted"; 
				$save_string=explode(",",str_replace("'","",$save_data));
				for($i=0;$i<count($save_string);$i++)
				{
				$data=explode("_",$save_string[$i]);  
				$item_des=$data[0];
				$trims_qty=$data[1];
				$remarks=$data[2];
				//if($trims_qty=='') $trims_qty=0;else $trims_qty=$trims_qty;
				if($trims_qty>0)
				{
				if ($i!=0) $data_array_dtls_trims.=",";
				$data_array_dtls_trims.="(".$id_dtls_trim.",".$batch_update_id.",'".$item_des."',".$trims_qty.",'".$remarks."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)"; 
				
				$id_dtls_trim=$id_dtls_trim+1;
				}
				}
			//echo "insert into pro_batch_trims_dtls (".$field_array_dtls_trims.") values ".$data_array_dtls_trims;die;
			$rID=sql_update("pro_batch_create_mst",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID) $flag=1; else $flag=0;
			//echo "6**$rID**$field_array_update**$data_array_update**$update_id";die;
			//echo "delete from pro_roll_details where mst_id=$update_id and entry_form=64";
			$delete_roll=execute_query("delete from pro_roll_details where mst_id=$update_id and entry_form=64",1);
			if($flag==1) 
			{
				if($delete_roll) $flag=1; else $flag=0; 
			} 
			
			//echo bulk_update_sql_statement( "pro_batch_create_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $id_arr );die;
			if($data_array_dtls_update!="")
			{
				$rID2=execute_query(bulk_update_sql_statement( "pro_batch_create_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $id_arr ));
				if($flag==1) 
				{
					if($rID2) $flag=1; else $flag=0; 
				} 
			}
			
			//echo "6**0**insert into pro_batch_create_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		
			if($data_array_dtls!="")
			{
				
				$rID3=sql_insert("pro_batch_create_dtls",$field_array_dtls,$data_array_dtls,1);
				if($flag==1) 
				{
					if($rID3) $flag=1; else $flag=0; 
				} 
			}
		
			if($txt_deleted_id!="")
			{
				$field_array_status="updated_by*update_date*status_active*is_deleted";
				$data_array_status=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
		
				$rID4=sql_multirow_update("pro_batch_create_dtls",$field_array_status,$data_array_status,"id",$txt_deleted_id,1);
				if($flag==1) 
				{
					if($rID4) $flag=1; else $flag=0; 
				} 
			}
			
			//echo "1**".str_replace("'", '', $batch_update_id)."**1**"."insert into pro_roll_details (".$field_array_roll.") values ".$data_array_roll;die;]
			//echo "6**0**insert into pro_roll_details (".$field_array_roll.") values ".$data_array_roll;die;
			if($data_array_roll!="" && $roll_maintained==1)
			{
				$rID5=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
				if($flag==1) 
				{
					if($rID5) $flag=1; else $flag=0; 
				} 
			}
			//echo "delete from pro_batch_trims_dtls where mst_id=$batch_update_id";
			$delete_trims_dtls=execute_query("delete from pro_batch_trims_dtls where mst_id=$batch_update_id",0);
			if($flag==1) 
			{
				if($delete_trims_dtls) $flag=1; else $flag=0; 
			} 
			//echo "insert into pro_batch_trims_dtls (".$field_array_dtls_trims.") values ".$data_array_dtls_trims;die;
			if($data_array_dtls_trims!="")
			{
			$rID6=sql_insert("pro_batch_trims_dtls",$field_array_dtls_trims,$data_array_dtls_trims,1);
			if($flag==1)
			{
				if($rID6) $flag=1; else $flag=0; 
			}
			}
			/*if($roll_table_id!="")
			{
				$rID6=sql_multirow_update("pro_roll_details","roll_used",1,"id",$roll_table_id,1);
				if($flag==1) 
				{
					if($rID6) $flag=1; else $flag=0; 
				} 
			}*/
		}

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".$batch_update_id."**".$serial_no."**".str_replace("'","",$txt_batch_number);
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
				echo "1**".$batch_update_id."**".$serial_no."**".str_replace("'","",$txt_batch_number);
			}
			else
			{
				oci_rollback($con);
				echo "6**0**1";
			}
		}
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
		$changeStatus2 = sql_update("pro_batch_create_dtls",$field_array_status,$data_array_status,"mst_id",$update_id,1);
		$changeStatus3 = sql_update("pro_roll_details",$field_array_status,$data_array_status,"mst_id*entry_form",$update_id."*64",1);
		$changeStatus4 = sql_update("pro_batch_trims_dtls",$field_array_status,$data_array_status,"mst_id",$update_id,1);
		
		//echo $changeStatus."&&".$changeStatus2."&&".$changeStatus3;die;
		if($db_type==0)
		{
			if($changeStatus && $changeStatus2 && $changeStatus3 && $changeStatus4)
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
			if($changeStatus && $changeStatus2 && $changeStatus3 && $changeStatus4)
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
                            $search_by_arr=array(1=>"Batch No",2=>"Booking No");
                            echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                        ?>
                    </td>                 
                    <td align="center">				
                        <input type="text" style="width:140px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td> 						
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+<? echo $batch_against; ?>, 'create_batch_search_list_view', 'search_div', 'batch_creation_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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
		$search_field='booking_no';
		
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
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	
	$arr=array(2=>$po_name_arr,7=>$batch_against,8=>$batch_for,9=>$color_arr);
	
	$sql = "select id, batch_no, extention_no, batch_weight, total_trims_weight, batch_date, batch_against, batch_for, booking_no, color_id from pro_batch_create_mst where company_id=$company_id and $search_field like '$search_string' and page_without_roll=0 and status_active=1 and entry_form=0 and is_deleted=0 $batch_cond"; 
	//echo $sql;// and batch_against<>0	 
	echo  create_list_view("tbl_list_search", "Batch No,Ext. No,Order No,Booking No,Batch Weight,Total Trims Weight, Batch Date,Batch Against,Batch For, Color", "100,70,150,105,80,80,80,80,85,80","1010","250",0, $sql, "js_set_value", "id", "", 1, "0,0,id,0,0,0,0,batch_against,batch_for,color_id", $arr, "batch_no,extention_no,id,booking_no,batch_weight,total_trims_weight,batch_date,batch_against,batch_for,color_id", "",'','0,0,0,0,2,2,3,0,0');
	
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
	
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$data_array=sql_select("select id, company_id, batch_no, extention_no, batch_weight, total_trims_weight,save_string, batch_date, batch_against, batch_for, booking_no, booking_no_id,booking_without_order, color_id, re_dyeing_from, color_range_id, organic, process_id, dur_req_hr, dur_req_min, collar_qty, cuff_qty, dyeing_machine, remarks, $year_field as year from pro_batch_create_mst where id='$batch_id'");
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
		echo "document.getElementById('txt_tot_trims_weight').value = '".$row[csf("total_trims_weight")]."';\n"; 
		echo "document.getElementById('save_data').value = '".$row[csf("save_string")]."';\n";  
		echo "document.getElementById('txt_batch_number').value = '".$row[csf("batch_no")]."';\n";  
		echo "document.getElementById('txt_booking_no').value = '".$row[csf("booking_no")]."';\n";  
		echo "document.getElementById('txt_booking_no_id').value = '".$row[csf("booking_no_id")]."';\n";  
		echo "document.getElementById('booking_without_order').value = '".$row[csf("booking_without_order")]."';\n";
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
		
		if($row[csf("booking_no")]!="")
		{
			echo "show_list_view('".$row[csf("booking_no")]."'+'**'+".$row[csf("booking_without_order")].",'show_color_listview','list_color','requires/batch_creation_controller','');\n";
		}
		
		if($batch_against==2)
		{
			echo "document.getElementById('cbo_batch_against').value = '".$batch_against."';\n";
			echo "$('#txt_ext_no').removeAttr('disabled','disabled');\n";
			echo "$('#txt_booking_no').attr('disabled','disabled');\n";
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

if( $action == 'batch_details' ) 
{
	$data=explode('**',$data);
	$batch_against=$data[0];
	$batch_for=$data[1];
	$batch_id=$data[2];
	$roll_maintained=$data[3];
	$batch_maintained=$data[4];
	$tblRow=0;
	
	if($batch_against==2)
	{
		$disbled="disabled='disabled'";
		$disbled_drop_down=1; 
	}
	else 
	{
		$disbled="";
		$disbled_drop_down=0; 
	}
	
	$po_array=array(); $program_no_array=array(); $body_part_ids_array=array();
	$data_array=sql_select("select a.batch_against, a.batch_for, a.booking_no, a.re_dyeing_from, a.color_id, a.booking_without_order, b.id, b.program_no, b.po_id, b.prod_id, b.item_description, b.body_part_id, b.width_dia_type, b.roll_no, b.roll_id, b.barcode_no, b.batch_qnty, b.po_batch_no from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and b.mst_id=$batch_id and b.status_active=1 and b.is_deleted=0"); 
	
	if($data_array[0][csf('batch_against')]==2)
	{
		foreach($data_array as $row)
		{
			$tblRow++;
			
			$batch_array=sql_select("select batch_against, batch_for, booking_no from pro_batch_create_mst where id=".$row[csf("re_dyeing_from")]);
		
			?>
			<tr class="general" id="tr_<? echo $tblRow; ?>">
            	<td id="slTd_<? echo $tblRow; ?>"><? echo $tblRow; ?></td>
				<?
					if($batch_array[0][csf('batch_against')]==1 || $batch_array[0][csf('batch_against')]==3)
					{
						if($roll_maintained==1)
						{
							if($tblRow==1)
							{
								$po_array=return_library_array( "SELECT a.id, a.po_number FROM wo_po_break_down a, pro_batch_create_dtls b WHERE a.id=b.po_id and b.mst_id=$batch_id and b.status_active=1 and b.is_deleted=0 group by a.id, a.po_number",'id','po_number');
								if(empty($po_array)) $po_array=array();
								
								$fab_description_array=return_library_array( "select a.id, a.product_name_details from product_details_master a, pro_batch_create_dtls b where a.id=b.prod_id and b.mst_id=$batch_id and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details",'id','product_name_details');
							}
							
							if($row[csf('program_no')]==0)
							{
								$program_no_array=array();
							}
							else
							{
								$program_no_array[$row[csf('program_no')]]=$row[csf('program_no')];
							}
						}
						else
						{
							if($tblRow==1)
							{
								if($row[csf('booking_without_order')]==0)
								{
									$po_array=return_library_array( "SELECT a.id, a.po_number FROM wo_po_break_down a, wo_booking_dtls b WHERE a.id=b.po_break_down_id and b.booking_no='".$row[csf('booking_no')]."' and b.fabric_color_id=".$row[csf('color_id')]." and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.po_number",'id','po_number');
									if(empty($po_array)) $po_array=array();
									
									$program_no_array=return_library_array( "SELECT b.id as program_id, b.id as program_no FROM ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b WHERE a.id=b.mst_id and a.booking_no='".$row[csf('booking_no')]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0",'program_id','program_no');
									if(empty($program_no_array)) $program_no_array=array();
								}
								else if($row[csf('booking_without_order')]==1)
								{
									if($batch_maintained==0)
									{
										$fab_description_array=return_library_array( "select a.id, a.product_name_details from product_details_master a, inv_grey_fabric_issue_dtls b, inv_issue_master c where a.id=b.prod_id and b.mst_id=c.id and c.booking_no='".$row[csf('booking_no')]."' and c.issue_basis=1 and c.issue_purpose=8 and c.entry_form in(16) and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details",'id','product_name_details');
									}
									else
									{
										$fab_description_array=return_library_array( "select a.id, a.product_name_details from product_details_master a, pro_grey_prod_entry_dtls b, inv_receive_master c where a.id=b.prod_id and b.mst_id=c.id and c.booking_no='".$row[csf('booking_no')]."' and c.booking_without_order=1 and c.entry_form in(2,22) and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details",'id','product_name_details');
									}
									if(empty($fab_description_array)) $fab_description_array=array();
								}
							}
							
							if($row[csf('booking_without_order')]==0)
							{
								if($batch_maintained==0)
								{
									if($row[csf('program_no')]>0)
									{
										//$fab_description_array=return_library_array( "select a.id, a.product_name_details from product_details_master a, inv_grey_fabric_issue_dtls b, inv_issue_master c, order_wise_pro_details d where a.id=b.prod_id and b.mst_id=c.id and a.id=d.prod_id and d.entry_form=16 and d.trans_type=2 and b.program_no='".$row[csf('program_no')]."' and d.po_breakdown_id='".$row[csf('po_id')]."' and c.issue_basis=3 and c.entry_form=16 and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details",'id','product_name_details');
										$fab_description_array=return_library_array( "select a.id, a.product_name_details from inv_issue_master c, inv_grey_fabric_issue_dtls b, order_wise_pro_details d, product_details_master a where c.id=b.mst_id and b.id=d.dtls_id and b.prod_id=a.id and d.entry_form=16 and d.trans_type=2 and b.program_no='".$row[csf('program_no')]."' and d.po_breakdown_id='".$row[csf('po_id')]."' and c.issue_basis=3 and c.entry_form=16 and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details",'id','product_name_details');
									}
									else
									{
										$fab_description_array=return_library_array( "select a.id, a.product_name_details from product_details_master a, order_wise_pro_details b where a.id=b.prod_id and b.po_breakdown_id='".$row[csf('po_id')]."' and b.entry_form in(16) and b.trans_type in(2) and a.item_category_id=13 and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details",'id','product_name_details');
									}
								}
								else
								{
									if($row[csf('program_no')]>0)
									{
										//$fab_description_array=return_library_array( "select a.id, a.product_name_details from product_details_master a, pro_grey_prod_entry_dtls b, inv_receive_master c, order_wise_pro_details d where a.id=b.prod_id and b.mst_id=c.id and a.id=d.prod_id and d.entry_form=2 and d.trans_type=1 and c.booking_id='".$row[csf('program_no')]."' and d.po_breakdown_id='".$row[csf('po_id')]."' and c.booking_without_order=0 and c.entry_form=2 and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details",'id','product_name_details');
										$fab_description_array=return_library_array( "select a.id, a.product_name_details from inv_receive_master c, pro_grey_prod_entry_dtls b, order_wise_pro_details d, product_details_master a where c.id=b.mst_id and b.id=d.dtls_id and b.prod_id=a.id and d.entry_form=2 and d.trans_type=1 and c.booking_id='".$row[csf('program_no')]."' and c.receive_basis=2 and d.po_breakdown_id='".$row[csf('po_id')]."' and c.booking_without_order=0 and c.receive_basis=2 and c.entry_form=2 and b.status_active=1 and b.is_deleted=0 group by  a.id, a.product_name_details",'id','product_name_details');
									}
									else
									{
										$fab_description_array=return_library_array( "select a.id, a.product_name_details from product_details_master a, order_wise_pro_details b where a.id=b.prod_id and b.po_breakdown_id='".$row[csf('po_id')]."' and b.entry_form in(2,13,22) and b.trans_type in(1,5) and a.item_category_id=13 and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details",'id','product_name_details');
									}
	
								}
								if(empty($fab_description_array)) $fab_description_array=array();
							}
						}
						
						if($tblRow==1)
						{
							$prodIds=implode(",", array_keys($fab_description_array));
							if($row[csf('booking_without_order')]==1)
							{
								$bodyPartData=sql_select("select b.prod_id, b.body_part_id from pro_grey_prod_entry_dtls b, inv_receive_master c where b.mst_id=c.id and c.booking_no='".$row[csf('booking_no')]."' and b.prod_id in($prodIds) and c.booking_without_order=1 and c.entry_form in(2,22) and b.status_active=1 and b.is_deleted=0 group by b.prod_id, b.body_part_id");
								foreach($bodyPartData as $rowB)
								{
									$body_part_ids_array[$rowB[csf('prod_id')]].=$rowB[csf('body_part_id')].",";
								}
							}
							else
							{
								$poIds=implode(",", array_keys($po_array));
								$bodyPartData=sql_select("select b.prod_id, b.po_breakdown_id, a.body_part_id from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.po_breakdown_id in ($poIds) and b.entry_form in(2,22) and b.trans_type=1 and b.status_active=1 and b.is_deleted=0 and b.prod_id in($prodIds) group by b.prod_id, b.po_breakdown_id, a.body_part_id");
								foreach($bodyPartData as $rowB)
								{
									$body_part_ids_array[$rowB[csf('po_breakdown_id')]][$rowB[csf('prod_id')]].=$rowB[csf('body_part_id')].",";
								}
							}
						}
						
						if($row[csf('booking_without_order')]==1)
						{
							$body_part_ids=chop($body_part_ids_array[$row[csf('prod_id')]],',');
						}
						else
						{
							$body_part_ids=chop($body_part_ids_array[$row[csf('po_id')]][$row[csf('prod_id')]],',');
						}
						
						echo "<td align='center' id='programNoTd_$tblRow'>";
						echo create_drop_down("cboProgramNo_$tblRow", 80, $program_no_array,"", 1, "-- Select --",$row[csf('program_no')],"load_item_desc(this.value,this.id );",1,"", "", "", "", "", "", "cboProgramNo[]");
						echo "</td>";
						echo "<td align='center' id='poNoTd_$tblRow'>";
						echo create_drop_down( "cboPoNo_".$tblRow, 130, $po_array,'', 1, "-- Select Po Number --", $row[csf('po_id')], "load_item_desc(this.value,this.id );",1,"", "", "", "", "", "", "cboPoNo[]");	
						echo "</td>";
						echo "<td align='center' id='itemDescTd_$tblRow'>";
						echo create_drop_down( "cboItemDesc_".$tblRow, 180,$fab_description_array,"",1,"-- Select Item Desc --",$row[csf('prod_id')],"load_body_part(this.value,this.id);",1,"", "", "", "", "", "", "cboItemDesc[]");
						echo "</td>";
						echo "<td align='center' id='bodyPartTd_$tblRow'>";
						echo create_drop_down( "cboBodyPart_".$tblRow, 120,$body_part,"",1,"-- Select --",$row[csf('body_part_id')],"",1,$body_part_ids, "", "", "", "", "", "cboBodyPart[]");
						echo "</td>";
					}
					else if($batch_array[0][csf('batch_against')]==5)
					{
						if($tblRow==1)
						{
							//$po_no_array=return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');
							$po_array=return_library_array( "SELECT a.id, a.po_number FROM wo_po_break_down a, pro_batch_create_dtls b WHERE a.id=b.po_id and b.mst_id=$batch_id and b.status_active=1 and b.is_deleted=0 group by a.id, a.po_number",'id','po_number');
						}
						$po_no=$po_no_array[$row[csf('po_id')]];
						
						if($roll_maintained==1)
						{
							if($tblRow==1)
							{
								$fab_description_array=return_library_array( "select a.id, a.product_name_details from product_details_master a, pro_batch_create_dtls b where a.id=b.prod_id and b.mst_id=$batch_id and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details",'id','product_name_details');
							}
							
							if($row[csf('program_no')]==0)
							{
								$program_no_array=array();
							}
							else
							{
								$program_no_array[$row[csf('program_no')]]=$row[csf('program_no')];
							}
						}
						else
						{
							$program_no_array=return_library_array( "SELECT b.id as program_id, b.id as program_no FROM ppl_planning_entry_plan_dtls a, ppl_planning_info_entry_dtls b WHERE a.dtls_id=b.id and a.po_id='".$row[csf('po_id')]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0",'program_id','program_no');
							if(empty($program_no_array)) $program_no_array=array();
							
							if($batch_maintained==0)
							{
								$fab_description_array=return_library_array( "select a.id, a.product_name_details from product_details_master a, order_wise_pro_details b where a.id=b.prod_id and b.po_breakdown_id='".$row[csf('po_id')]."' and b.entry_form in(16) and b.trans_type in(2) and a.item_category_id=13 and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details",'id','product_name_details');
							}
							else
							{
								$fab_description_array=return_library_array( "select a.id, a.product_name_details from product_details_master a, order_wise_pro_details b where a.id=b.prod_id and b.po_breakdown_id='".$row[csf('po_id')]."' and b.entry_form in(2,13,22) and b.trans_type in(1,5) and a.item_category_id=13 and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details",'id','product_name_details');
							}
							if(empty($fab_description_array)) $fab_description_array=array();
						}
						
						if($tblRow==1)
						{
							$prodIds=implode(",", array_keys($fab_description_array));
							$poIds=implode(",", array_keys($po_array));
							$bodyPartData=sql_select("select b.prod_id, b.po_breakdown_id, a.body_part_id from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.po_breakdown_id in ($poIds) and b.entry_form in(2,22) and b.trans_type=1 and b.status_active=1 and b.is_deleted=0 and b.prod_id in($prodIds) group by b.prod_id, b.po_breakdown_id, a.body_part_id");
							foreach($bodyPartData as $rowB)
							{
								$body_part_ids_array[$rowB[csf('po_breakdown_id')]][$rowB[csf('prod_id')]].=$rowB[csf('body_part_id')].",";
							}
						}
						
						$body_part_ids=chop($body_part_ids_array[$row[csf('po_id')]][$row[csf('prod_id')]],',');
						
						echo "<td align='center' id='programNoTd_$tblRow'>";
						echo create_drop_down("cboProgramNo_$tblRow", 80, $program_no_array,"", 1, "-- Select --", $row[csf('program_no')], "",1,"", "", "", "", "", "", "cboProgramNo[]");
						echo "</td>";
					?>
						<td align='center' id='poNoTd_<? echo $tblRow; ?>'>
							<input type="text" name="cboPoNo[]" id="cboPoNo_<? echo $tblRow; ?>" class="text_boxes" style="width:120px;" placeholder="Double Click to Search" onDblClick="openmypage_po(<? echo $tblRow; ?>)" value="<? echo $po_no; ?>" disabled="disabled"/>
						</td>
						<td align='center' id='itemDescTd_<? echo $tblRow; ?>'>
							<? echo create_drop_down( "cboItemDesc_".$tblRow, 180, $fab_description_array,"", 1, "-- Select Item Desc --", $row[csf('prod_id')], "",1 ,"", "", "", "", "", "", "cboItemDesc[]"); ?>
						</td>
                        <td align='center' id='bodyPartTd_<? echo $tblRow; ?>'>
							<? echo create_drop_down( "cboBodyPart_".$tblRow, 120, $body_part,"", 1, "-- Select --", $row[csf('body_part_id')], "",1,$body_part_ids, "", "", "", "", "", "cboBodyPart[]" ); ?>
						</td>
					<?
					}
					?>
                <td>
					<? echo create_drop_down( "cboDiaWidthType_".$tblRow, 90, $fabric_typee,"",1, "-- Select --", $row[csf('width_dia_type')], "",1,"", "", "", "", "", "", "cboDiaWidthType[]" ); ?>
                </td>
				<td>
                	<input type="text" name="txtRollNo[]" id="txtRollNo_<? echo $tblRow; ?>" class="text_boxes" style="width:50px" value="<? if($row[csf('roll_no')]!=0) echo $row[csf('roll_no')]; ?>" disabled="disabled"/>
					<input type="hidden" name="hideRollNo[]" id="hideRollNo_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('roll_id')]; ?>" />
					<? if($batch_array[0][csf('batch_against')]==5) $po_id=$row[csf('po_id')]; else $po_id=""; ?>
					<input type="hidden" name="poId[]" id="poId_<? echo $tblRow; ?>"  value="<? echo $po_id; ?>" class="text_boxes" readonly />
					<input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('id')]; ?>" />
                    <!--<input type="hidden" name="barcodeNo_<?echo $tblRow;?>" id="barcodeNo_<?echo $tblRow; ?>" value="<?echo $row[csf('barcode_no')]; ?>" />-->
				</td>
                <td>
					<input type="text" name="barcodeNo[]" id="barcodeNo_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('barcode_no')]; ?>" style="width:70px" placeholder="Display" readonly />
				</td>
				<td>
					<input type="text" name="txtBatchQnty[]"  id="txtBatchQnty_<? echo $tblRow; ?>" class="text_boxes_numeric" onKeyUp="calculate_batch_qnty();" style="width:60px" disabled="disabled" value="<? echo $row[csf('batch_qnty')]; ?>" />
				</td>
                <td>
                    <input type="text" name="txtPoBatchNo[]"  id="txtPoBatchNo_<? echo $tblRow; ?>" class="text_boxes_numeric" style="width:45px" value="<? echo $row[csf('po_batch_no')]; ?>" disabled />
                </td>
				<td width="65">
					<input type="button" id="increase_<? echo $tblRow; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<? echo $tblRow; ?>)" />
					<input type="button" id="decrease_<? echo $tblRow; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $tblRow; ?>);" />
				</td>
			</tr>
		<?
		}
	}
	else
	{
		foreach($data_array as $row)
		{
			$tblRow++;

			?>
			<tr class="general" id="tr_<? echo $tblRow; ?>">
            	<td id="slTd_<? echo $tblRow; ?>"><? echo $tblRow; ?></td>
				<?
					if($row[csf('batch_against')]==1 || $row[csf('batch_against')]==3)
					{
						if($roll_maintained==1)
						{
							$disbled="disabled='disabled'";
							$disbled_drop_down=1;
							
							if($tblRow==1)
							{
								$po_array=return_library_array( "SELECT a.id, a.po_number FROM wo_po_break_down a, pro_batch_create_dtls b WHERE a.id=b.po_id and b.mst_id=$batch_id and b.status_active=1 and b.is_deleted=0 group by a.id, a.po_number",'id','po_number');
								if(empty($po_array)) $po_array=array();
								
								$fab_description_array=return_library_array( "select a.id, a.product_name_details from product_details_master a, pro_batch_create_dtls b where a.id=b.prod_id and b.mst_id=$batch_id and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details",'id','product_name_details');
							}
							
							if($row[csf('program_no')]==0)
							{
								$program_no_array=array();
							}
							else
							{
								$program_no_array[$row[csf('program_no')]]=$row[csf('program_no')];
							}
						}
						else
						{
							if($tblRow==1)
							{
								if($row[csf('booking_without_order')]==0)
								{
									$po_array=return_library_array( "SELECT a.id, a.po_number FROM wo_po_break_down a, wo_booking_dtls b WHERE a.id=b.po_break_down_id and b.booking_no='".$row[csf('booking_no')]."' and b.fabric_color_id=".$row[csf('color_id')]." and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.po_number",'id','po_number');
									if(empty($po_array)) $po_array=array();
									
									$program_no_array=return_library_array( "SELECT b.id as program_id, b.id as program_no FROM ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b WHERE a.id=b.mst_id and a.booking_no='".$row[csf('booking_no')]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0",'program_id','program_no');
									if(empty($program_no_array)) $program_no_array=array();
								}
								else if($row[csf('booking_without_order')]==1)
								{
									if($batch_maintained==0)
									{
										$fab_description_array=return_library_array( "select a.id, a.product_name_details from product_details_master a, inv_grey_fabric_issue_dtls b, inv_issue_master c where a.id=b.prod_id and b.mst_id=c.id and c.booking_no='".$row[csf('booking_no')]."' and c.issue_basis=1 and c.issue_purpose=8 and c.entry_form in(16) and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details",'id','product_name_details');
									}
									else
									{
										$fab_description_array=return_library_array( "select a.id, a.product_name_details from product_details_master a, pro_grey_prod_entry_dtls b, inv_receive_master c where a.id=b.prod_id and b.mst_id=c.id and c.booking_no='".$row[csf('booking_no')]."' and c.booking_without_order=1 and c.entry_form in(2,22) and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details",'id','product_name_details');
									}
									if(empty($fab_description_array)) $fab_description_array=array();
								}
							}
							
							if($row[csf('booking_without_order')]==0)
							{
								if($batch_maintained==0)
								{
									if($row[csf('program_no')]>0)
									{
										$fab_description_array=return_library_array( "select a.id, a.product_name_details from inv_issue_master c, inv_grey_fabric_issue_dtls b, order_wise_pro_details d, product_details_master a where c.id=b.mst_id and b.id=d.dtls_id and b.prod_id=a.id and d.entry_form=16 and d.trans_type=2 and b.program_no='".$row[csf('program_no')]."' and d.po_breakdown_id='".$row[csf('po_id')]."' and c.issue_basis=3 and c.entry_form=16 and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details",'id','product_name_details');
									}
									else
									{
										$fab_description_array=return_library_array( "select a.id, a.product_name_details from product_details_master a, order_wise_pro_details b where a.id=b.prod_id and b.po_breakdown_id='".$row[csf('po_id')]."' and b.entry_form in(16) and b.trans_type in(2) and a.item_category_id=13 and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details",'id','product_name_details');
									}
								}
								else
								{
									if($row[csf('program_no')]>0)
									{
										$fab_description_array=return_library_array( "select a.id, a.product_name_details from inv_receive_master c, pro_grey_prod_entry_dtls b, order_wise_pro_details d, product_details_master a where c.id=b.mst_id and b.id=d.dtls_id and b.prod_id=a.id and d.entry_form=2 and d.trans_type=1 and c.booking_id='".$row[csf('program_no')]."' and c.receive_basis=2 and d.po_breakdown_id='".$row[csf('po_id')]."' and c.booking_without_order=0 and c.receive_basis=2 and c.entry_form=2 and b.status_active=1 and b.is_deleted=0 group by  a.id, a.product_name_details",'id','product_name_details');
									}
									else
									{
										$fab_description_array=return_library_array( "select a.id, a.product_name_details from product_details_master a, order_wise_pro_details b where a.id=b.prod_id and b.po_breakdown_id='".$row[csf('po_id')]."' and b.entry_form in(2,13,22) and b.trans_type in(1,5) and a.item_category_id=13 and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details",'id','product_name_details');
									}
								}
								if(empty($fab_description_array)) $fab_description_array=array();
							}
						}
						
						if($tblRow==1)
						{
							$prodIds=implode(",", array_keys($fab_description_array));
							if($row[csf('booking_without_order')]==1)
							{
								$bodyPartData=sql_select("select b.prod_id, b.body_part_id from pro_grey_prod_entry_dtls b, inv_receive_master c where b.mst_id=c.id and c.booking_no='".$row[csf('booking_no')]."' and b.prod_id in($prodIds) and c.booking_without_order=1 and c.entry_form in(2,22) and b.status_active=1 and b.is_deleted=0 group by b.prod_id, b.body_part_id");
								foreach($bodyPartData as $rowB)
								{
									$body_part_ids_array[$rowB[csf('prod_id')]].=$rowB[csf('body_part_id')].",";
								}
							}
							else
							{
								$poIds=implode(",", array_keys($po_array));
								$bodyPartData=sql_select("select b.prod_id, b.po_breakdown_id, a.body_part_id from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.po_breakdown_id in ($poIds) and b.entry_form in(2,22) and b.trans_type=1 and b.status_active=1 and b.is_deleted=0 and b.prod_id in($prodIds) group by b.prod_id, b.po_breakdown_id, a.body_part_id");
								foreach($bodyPartData as $rowB)
								{
									$body_part_ids_array[$rowB[csf('po_breakdown_id')]][$rowB[csf('prod_id')]].=$rowB[csf('body_part_id')].",";
								}
							}
						}
						
						if($row[csf('booking_without_order')]==1)
						{
							$body_part_ids=chop($body_part_ids_array[$row[csf('prod_id')]],',');
						}
						else
						{
							$body_part_ids=chop($body_part_ids_array[$row[csf('po_id')]][$row[csf('prod_id')]],',');
						}
						//echo $body_part_ids_array[5683][5524].Fuad;
						//var_dump($body_part_ids_array)."Fuad";//die;
						echo "<td align='center' id='programNoTd_$tblRow'>";
						echo create_drop_down("cboProgramNo_$tblRow", 80, $program_no_array,"", 1, "-- Select --", $row[csf('program_no')],"load_item_desc(this.value,this.id);",$disbled_drop_down,"", "", "", "", "", "", "cboProgramNo[]");
						echo "</td>";
						
						echo "<td align='center' id='poNoTd_$tblRow'>";
						echo create_drop_down( "cboPoNo_".$tblRow, 130, $po_array,'', 1, "-- Select Po Number --", $row[csf('po_id')], "load_item_desc(this.value,this.id);",$disbled_drop_down,"", "", "", "", "", "", "cboPoNo[]");	
						echo "</td>";
						echo "<td align='center' id='itemDescTd_$tblRow'>";
						echo create_drop_down( "cboItemDesc_".$tblRow, 180,$fab_description_array,"",1,"-- Select Item Desc --",$row[csf('prod_id')],"load_body_part(this.value,this.id);", $disbled_drop_down, "", "", "", "", "", "", "cboItemDesc[]");
						echo "</td>";
						echo "<td align='center' id='bodyPartTd_$tblRow'>";
						echo create_drop_down( "cboBodyPart_".$tblRow, 120,$body_part,"",1,"-- Select --",$row[csf('body_part_id')],"",$disbled_drop_down,$body_part_ids, "", "", "", "", "", "cboBodyPart[]");
						echo "</td>";
					}
					else if($row[csf('batch_against')]==5)
					{
						if($tblRow==1)
						{
							//$po_no_array=return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');
							$po_array=return_library_array( "SELECT a.id, a.po_number FROM wo_po_break_down a, pro_batch_create_dtls b WHERE a.id=b.po_id and b.mst_id=$batch_id and b.status_active=1 and b.is_deleted=0 group by a.id, a.po_number",'id','po_number');
							if(empty($po_array)) $po_array=array();
						}
						$po_no=$po_array[$row[csf('po_id')]];
						
						if($roll_maintained==1)
						{
							$disbled="disabled='disabled'";
							$disbled_drop_down=1;
							if($tblRow==1)
							{
								$fab_description_array=return_library_array( "select a.id, a.product_name_details from product_details_master a, pro_batch_create_dtls b where a.id=b.prod_id and b.mst_id=$batch_id and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details",'id','product_name_details');
							}
							
							if($row[csf('program_no')]==0)
							{
								$program_no_array=array();
							}
							else
							{
								$program_no_array[$row[csf('program_no')]]=$row[csf('program_no')];
							}
						}
						else
						{
							if($batch_against!=2) 
							{
								$disbled="";
								$disbled_drop_down=0;
							}
							
							$program_no_array=return_library_array( "SELECT b.id as program_id, b.id as program_no FROM ppl_planning_entry_plan_dtls a, ppl_planning_info_entry_dtls b WHERE a.dtls_id=b.id and a.po_id='".$row[csf('po_id')]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0",'program_id','program_no');
							if(empty($program_no_array)) $program_no_array=array();
							
							if($batch_maintained==0)
							{
								$fab_description_array=return_library_array( "select a.id, a.product_name_details from product_details_master a, order_wise_pro_details b where a.id=b.prod_id and b.po_breakdown_id='".$row[csf('po_id')]."' and b.entry_form in(16) and b.trans_type in(2) and a.item_category_id=13 and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details",'id','product_name_details');
							}
							else
							{
								$fab_description_array=return_library_array( "select a.id, a.product_name_details from product_details_master a, order_wise_pro_details b where a.id=b.prod_id and b.po_breakdown_id='".$row[csf('po_id')]."' and b.entry_form in(2,13,22) and b.trans_type in(1,5) and a.item_category_id=13 and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details",'id','product_name_details');
							}
							if(empty($fab_description_array)) $fab_description_array=array();
						}
						
						if($tblRow==1)
						{
							$prodIds=implode(",", array_keys($fab_description_array));
							$poIds=implode(",", array_keys($po_array));
							$bodyPartData=sql_select("select b.prod_id, b.po_breakdown_id, a.body_part_id from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.po_breakdown_id in ($poIds) and b.entry_form in(2,22) and b.trans_type=1 and b.status_active=1 and b.is_deleted=0 and b.prod_id in($prodIds) group by b.prod_id, b.po_breakdown_id, a.body_part_id");
							foreach($bodyPartData as $rowB)
							{
								$body_part_ids_array[$rowB[csf('po_breakdown_id')]][$rowB[csf('prod_id')]].=$rowB[csf('body_part_id')].",";
							}
						}
						
						$body_part_ids=chop($body_part_ids_array[$row[csf('po_id')]][$row[csf('prod_id')]],',');
						
						echo "<td align='center' id='programNoTd_$tblRow'>";
						echo create_drop_down("cboProgramNo_$tblRow", 80, $program_no_array,"", 1, "-- Select --", $row[csf('program_no')], "",$disbled_drop_down,"", "", "", "", "", "", "cboProgramNo[]" );
						echo "</td>";
					?>
						<td id='poNoTd_<? echo $tblRow; ?>'>
							<input type="text" name="cboPoNo[]" id="cboPoNo_<? echo $tblRow; ?>" class="text_boxes" style="width:120px;" placeholder="Double Click to Search" onDblClick="openmypage_po(<? echo $tblRow; ?>)" value="<? echo $po_no; ?>" <? echo $disbled; ?> readonly />
						</td>
						<td id='itemDescTd_<? echo $tblRow; ?>'>
							<? echo create_drop_down( "cboItemDesc_".$tblRow, 180, $fab_description_array,"", 1, "-- Select Item Desc --", $row[csf('prod_id')], "load_body_part(this.value,this.id);",$disbled_drop_down,"", "", "", "", "", "", "cboItemDesc[]"); ?>
						</td>
                        <td align='center' id='bodyPartTd_<? echo $tblRow; ?>'>
							<? echo create_drop_down( "cboBodyPart_".$tblRow, 120, $body_part,"", 1, "-- Select --", $row[csf('body_part_id')], "",$disbled_drop_down,$body_part_ids, "", "", "", "", "", "cboBodyPart[]"); ?>
						</td>
				<?
					}
				?>
                <td>
					<?
						if($batch_against==2) $disbled_drop=1; else $disbled_drop=0;
                        echo create_drop_down( "cboDiaWidthType_".$tblRow, 90, $fabric_typee,"",1, "-- Select --", $row[csf('width_dia_type')], "", $disbled_drop,"", "", "", "", "", "", "cboDiaWidthType[]");
                    ?>
                </td>
				<td>
                	<input type="text" name="txtRollNo[]" id="txtRollNo_<? echo $tblRow; ?>" class="text_boxes" style="width:50px" value="<? if($row[csf('roll_no')]!=0) echo $row[csf('roll_no')]; ?>" <? echo $disbled; ?>/>
					<input type="hidden" name="hideRollNo[]" id="hideRollNo_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('roll_id')]; ?>" />
					<? if($row[csf('batch_against')]==5) $po_id=$row[csf('po_id')]; else $po_id=""; ?>
					<input type="hidden" name="poId[]" id="poId_<? echo $tblRow; ?>"  value="<? echo $po_id; ?>" class="text_boxes" readonly />
					<input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('id')]; ?>" />
                    <!--<input type="hidden" name="barcodeNo_<?echo $tblRow;?>" id="barcodeNo_<?echo $tblRow; ?>" value="<?echo $row[csf('barcode_no')]; ?>" />-->
				</td>
                <td>
					<input type="text" name="barcodeNo[]" id="barcodeNo_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('barcode_no')]; ?>" style="width:70px" placeholder="Display" readonly />
				</td>
				<td>
					<input type="text" name="txtBatchQnty[]" id="txtBatchQnty_<? echo $tblRow; ?>" class="text_boxes_numeric" onKeyUp="calculate_batch_qnty();" style="width:60px" value="<? echo $row[csf('batch_qnty')]; ?>" <? echo $disbled; ?>/>
				</td>
                <td>
                    <input type="text" name="txtPoBatchNo[]" id="txtPoBatchNo_<? echo $tblRow; ?>" class="text_boxes_numeric" style="width:45px" value="<? echo $row[csf('po_batch_no')]; ?>" disabled />
                </td>
				<td width="65">
					<input type="button" id="increase_<? echo $tblRow; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<? echo $tblRow; ?>)" />
					<input type="button" id="decrease_<? echo $tblRow; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $tblRow; ?>);" />
				</td>
			</tr>
		<?
		}
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

if($action=="roll_maintained")
{
	$roll_maintained=return_field_value("fabric_roll_level","variable_settings_production","company_name ='$data' and item_category_id=2 and variable_list=3 and is_deleted=0 and status_active=1");
	if($roll_maintained=="" || $roll_maintained==2) $roll_maintained=0; else $roll_maintained=$roll_maintained;
	
	echo "document.getElementById('roll_maintained').value 				= '".$roll_maintained."';\n";
	exit();	
}

if($action=="show_color_listview")
{
	$data=explode("**",$data);
	$booking_no=$data[0];
	$booking_without_order=$data[1];
	
	$batch_qnty_array=array();
	$batch_data_array=sql_select("SELECT a.color_id, a.booking_no, sum(b.batch_qnty) as qnty FROM pro_batch_create_mst a, pro_batch_create_dtls b WHERE a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.entry_form=0 group by a.color_id, a.booking_no");
	foreach($batch_data_array as $row)
	{
		$batch_qnty_array[$row[csf('color_id')]][$row[csf('booking_no')]]=$row[csf('qnty')];
	}
	//var_dump($batch_qnty_array);
?>	
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="330" class="rpt_table">
        <thead>
            <th width="25">SL</th>
            <th width="80">Color</th>
            <th width="75">Booking Qty.</th>
            <th width="75">Batch Qty.</th>
            <th>Balance</th>                    
        </thead>
		<?  
		$i=1;
		if($booking_without_order==1)
		{
			$sql=sql_select("select b.id, b.color_name, sum(a.grey_fabric) as qnty from wo_non_ord_samp_booking_dtls a, lib_color b where a.fabric_color=b.id and a.booking_no='$booking_no' and a.status_active=1 and a.is_deleted=0 group by b.id, b.color_name");
		}
		else
		{
			$sql=sql_select("select b.id, b.color_name, sum(a.grey_fab_qnty) as qnty from wo_booking_dtls a, lib_color b where a.fabric_color_id=b.id and a.booking_no='$booking_no' and  a.status_active=1 and a.is_deleted=0 group by b.id, b.color_name");
		}
		
		foreach($sql as $row)
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			
			$batch_qnty=$batch_qnty_array[$row[csf('id')]][$booking_no];
			$balance=$row[csf('qnty')]-$batch_qnty;
		?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="put_country_data(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('color_name')]; ?>')"> 
				<td width="25"><? echo $i; ?></td>
				<td width="80"><p><? echo $row[csf('color_name')]; ?></p></td>
				<td width="75" align="right"><p><? echo number_format($row[csf('qnty')],2); ?>&nbsp;</p></td>
				<td width="75" align="right"><? echo number_format($batch_qnty,2); ?>&nbsp;</td>
				<td align="right"><? echo number_format($balance,2); ?></td>
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
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$machine_no_arr=return_library_array( "select id,machine_no from lib_machine_name",'id','machine_no');
	
	$job_array=array();
	$job_sql="select distinct(a.buyer_name) as buyer_name, a.job_no_prefix_num, a.job_no, b.pub_shipment_date, b.id, b.po_number, b.grouping, b.file_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$job_sql_result=sql_select($job_sql);
	foreach($job_sql_result as $row)
	{
		$job_array[$row[csf('id')]]['job']=$row[csf('job_no')];
		$job_array[$row[csf('id')]]['po']=$row[csf('po_number')];
		$job_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
		$job_array[$row[csf('id')]]['ship_date']=$row[csf('pub_shipment_date')];
		$job_array[$row[csf('id')]]['ref']=$row[csf('grouping')];
		$job_array[$row[csf('id')]]['file_no']=$row[csf('file_no')];
	}
	
	if($db_type==0)
	{
		$sql="select a.id, a.batch_no, a.booking_no_id, a.booking_no,a.booking_without_order,a.color_id, a.batch_against, a.color_range_id, a.organic,a.dyeing_machine, a.extention_no, a.total_trims_weight, a.process_id as process_id, a.batch_for, a.batch_weight, a.remarks, a.collar_qty, a.cuff_qty, group_concat(b.po_id) as po_id, group_concat(b.prod_id) as prod_id from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.id=$batch_update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no, a.booking_no_id, a.booking_no,a.booking_without_order,a.color_id, a.batch_against, a.color_range_id, a.organic,a.dyeing_machine, a.extention_no, a.total_trims_weight, a.process_id, a.batch_for, a.batch_weight,a.remarks,a.collar_qty, a.cuff_qty";
	}
	else
	{
		$sql="select a.id, a.batch_no, a.booking_no_id,a.booking_no,a.booking_without_order, a.color_id, a.batch_against, a.color_range_id, a.organic,a.dyeing_machine, a.extention_no, a.total_trims_weight, a.process_id as process_id, a.batch_for, a.batch_weight, a.remarks, a.collar_qty, a.cuff_qty, LISTAGG(b.po_id, ',') WITHIN GROUP (ORDER BY b.po_id) AS po_id , LISTAGG(CAST(b.prod_id AS VARCHAR2(4000)),',') WITHIN GROUP (ORDER BY b.prod_id) AS prod_id from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.id=$batch_update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no, a.color_id, a.batch_against, a.color_range_id, a.organic ,a.dyeing_machine,a.extention_no, a.booking_no_id, a.booking_no,a.booking_without_order,a.total_trims_weight, a.process_id, a.batch_for, a.batch_weight,a.remarks,a.collar_qty,a.cuff_qty";	
	}
	//echo $sql;
	$dataArray=sql_select($sql);
	
	$po_number=""; $job_number=""; $buyer_id=""; $ship_date=""; $internal_ref=""; $file_nos="";
	$po_id=array_unique(explode(",",$dataArray[0][csf('po_id')]));
	$booking_no=$dataArray[0][csf('booking_no')];
	$batch_against_id=$dataArray[0][csf('batch_against')];
	$batch_booking_id=$dataArray[0][csf('booking_no_id')];
	$batch_product_id=$dataArray[0][csf('prod_id')];
	$batch_booking_without=$dataArray[0][csf('booking_without_order')];
	foreach($po_id as $val)
	{
		if($po_number=="") $po_number=$job_array[$val]['po']; else $po_number.=','.$job_array[$val]['po'];
		if($job_number=="") $job_number=$job_array[$val]['job']; else $job_number.=','.$job_array[$val]['job'];
		if($buyer_id=="") $buyer_id=$job_array[$val]['buyer']; else $buyer_id.=','.$job_array[$val]['buyer'];
		if($ship_date=="") $ship_date=change_date_format($job_array[$val]['ship_date']); else $ship_date.=','.change_date_format($job_array[$val]['ship_date']);
		
		if($internal_ref=="") $internal_ref=$job_array[$val]['ref']; else $internal_ref.=','.$job_array[$val]['ref'];
		if($job_array[$val]['file_no']>0)
		{
			if($file_nos=="") $file_nos=$job_array[$val]['file_no']; else $file_nos.=','.$job_array[$val]['file_no'];
		}
	}
	
	$job_no=implode(",",array_unique(explode(",",$job_number)));
	$buyer=implode(",",array_unique(explode(",",$buyer_id)));
	$internal_ref=implode(",",array_unique(array_filter(explode(",",$internal_ref))));
	$file_nos=implode(",",array_unique(explode(",",$file_nos)));
	//$booking_without_order=return_field_value( "booking_no as booking_no", "wo_non_ord_samp_booking_mst","booking_no=$booking_no","booking_no");

	$booking_without_order=sql_select("select booking_no_prefix_num, buyer_id from wo_non_ord_samp_booking_mst where company_id=$company and booking_no='$booking_no' and booking_type=4");
	$booking_id=$booking_without_order[0][csf('booking_no_prefix_num')];
	$buyer_id_booking=$booking_without_order[0][csf('buyer_id')];

?>
    <div style="width:980px;">
     <table width="980" cellspacing="0" align="center" border="0">
        <tr>
            <td colspan="6" align="center" style="font-size:22px"><strong><? echo $company_library[$company]; ?></strong></td>
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
        	<td><strong>Remarks</strong></td><td colspan="7">:&nbsp;<? echo $dataArray[0][csf('remarks')]; ?></td>
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
			$supplier_from_prod=return_library_array("select lot,supplier_id from  product_details_master where item_category_id=1", "lot","supplier_id");
			
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
			
	$sql_dtls="select b.id, a.batch_no, a.total_trims_weight, a.batch_date, a.batch_weight, a.color_id, a.booking_no_id, a.booking_without_order, a.process_id, a.extention_no, b.batch_qnty AS batch_qnty, b.roll_no, b.item_description, b.program_no, b.po_id, b.prod_id, b.body_part_id, b.width_dia_type from pro_batch_create_mst a,pro_batch_create_dtls b where a.company_id=$data[0] and a.id=b.mst_id and a.id=$batch_update_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0";
	//echo $sql_dtls;
	$sql_result=sql_select($sql_dtls);
	foreach($sql_result as $row)
	{
		if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			//echo $row[csf('prod_id')].'='.$row[csf('po_id')];
		$desc=explode(",",$row[csf('item_description')]);
		
		if($batch_against_id==3 && $row[csf('booking_without_order')]==1)
		{
			$yarn_lot=$sample_arr[$row[csf('booking_no_id')]][$row[csf('prod_id')]]['samplelot'];
			$y_count=$sample_arr[$row[csf('booking_no_id')]][$row[csf('prod_id')]]['yarncount'];
			$stitch=$sample_arr[$row[csf('booking_no_id')]][$row[csf('prod_id')]]['stitch_length'];	
			$yarn_count_value=$yarncount[$y_count];
			$dya_gage=$dya_gauge_arr[$sample_arr[$row[csf('booking_no_id')]][$row[csf('prod_id')]]['machine_no_id']]["dia_width"]."<br>".$dya_gauge_arr[$sample_arr[$row[csf('booking_no_id')]][$row[csf('prod_id')]]['machine_no_id']]["gauge"];
			
		}
		else
		{
			$y_count=chop($yarn_count[$row[csf('prod_id')]][$row[csf('po_id')]]['yarn_count'],",");
			$y_count_id=array_unique(explode(',',$y_count));
			$yarn_count_value='';
			$machine_no_id_arr=array_unique(explode(',',chop($machine_no_id[$row[csf('prod_id')]][$row[csf('po_id')]]['machine_no_id'],",")));
			$dya_gage="";
			foreach($machine_no_id_arr as $machine_id)
			{
				$dya_gage=$dya_gauge_arr[$machine_id]["dia_width"]."<br>".$dya_gauge_arr[$machine_id]["gauge"];
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
		}
		//$yarn_lot_arr[$rows[csf('prod_id')]][$rows['po_breakdown_id']]['stitch_length']
		//$st_len=implode(", ", array_unique(explode(",",$stitch)));
		//$machine_dia_up=$machine_array_lib_dia[$machine_dia[$row[csf('program_no')]]['m_dia']];
		//$machine_gauge_up=$machine_array_lib_gauge[$machine_dia[$row[csf('program_no')]]['m_gauge']];
		$lot_data=explode(",",$yarn_lot);
		$lot_supplier="";
		foreach($lot_data as $row_lot)
		{
			if($lot_supplier=="") $lot_supplier=$supplier_array_lib[$supplier_from_prod[$row_lot]];else $lot_supplier.=",".$supplier_array_lib[$supplier_from_prod[$row_lot]];	
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
                <td width="80"><p><? echo rtrim($lot_supplier,",");//$supplier_array_lib[$supplier_from_prod[$yarn_lot]]; ?></p></td>
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
           Heat Setting:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;   Loading Date: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;  UnLoading Date:&nbsp;
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
                        <th align="center"><strong>Other Information(<i>Hand Written</i>)</strong></th>
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

if($action=="batch_card_print_2")
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
	$supplier_library=return_library_array( "select id, short_name from lib_supplier", "id", "short_name");
	$machine_library=return_library_array( "select id, machine_no from lib_machine_name", "id", "machine_no");
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	
	$job_array=array();
	$job_sql="select distinct(a.buyer_name) as buyer_name, a.job_no_prefix_num, a.job_no, b.pub_shipment_date, b.id, b.po_number, b.grouping, b.file_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$job_sql_result=sql_select($job_sql);
	foreach($job_sql_result as $row)
	{
		$job_array[$row[csf('id')]]['job']=$row[csf('job_no')];
		$job_array[$row[csf('id')]]['po']=$row[csf('po_number')];
		$job_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
		$job_array[$row[csf('id')]]['ship_date']=$row[csf('pub_shipment_date')];
		$job_array[$row[csf('id')]]['ref']=$row[csf('grouping')];
		$job_array[$row[csf('id')]]['file_no']=$row[csf('file_no')];
	}
	
	if($db_type==0)
	{
		$sql="select a.id, a.batch_no, a.booking_no_id, a.booking_no,a.booking_without_order,a.color_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.total_trims_weight, a.process_id as process_id, a.batch_for, a.batch_weight, a.dyeing_machine, a.remarks, a.collar_qty, a.cuff_qty, group_concat(b.po_id) as po_id, group_concat(b.prod_id) as prod_id from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.id=$batch_update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no, a.booking_no_id, a.booking_no,a.booking_without_order,a.color_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.total_trims_weight, a.process_id, a.batch_for, a.batch_weight, a.dyeing_machine,a.remarks,a.collar_qty, a.cuff_qty";
	}
	else
	{
		$sql="select a.id, a.batch_no, a.booking_no_id,a.booking_no,a.booking_without_order, a.color_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.total_trims_weight, a.process_id as process_id, a.batch_for, a.batch_weight, a.dyeing_machine, a.remarks, a.collar_qty, a.cuff_qty, LISTAGG(b.po_id, ',') WITHIN GROUP (ORDER BY b.po_id) AS po_id , LISTAGG(CAST(b.prod_id AS VARCHAR2(4000)),',') WITHIN GROUP (ORDER BY b.prod_id) AS prod_id from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.id=$batch_update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no, a.color_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.booking_no_id, a.booking_no,a.booking_without_order,a.total_trims_weight, a.process_id, a.batch_for, a.batch_weight, a.dyeing_machine,a.remarks,a.collar_qty,a.cuff_qty";	
	}
	//echo $sql;
	$dataArray=sql_select($sql);
	
	$po_number=""; $job_number=""; $buyer_id=""; $ship_date=""; $internal_ref=""; $file_nos="";
	$po_id=array_unique(explode(",",$dataArray[0][csf('po_id')]));
	$booking_no=$dataArray[0][csf('booking_no')];
	$batch_against_id=$dataArray[0][csf('batch_against')];
	$batch_booking_id=$dataArray[0][csf('booking_no_id')];
	$batch_product_id=$dataArray[0][csf('prod_id')];
	$batch_booking_without=$dataArray[0][csf('booking_without_order')];
	foreach($po_id as $val)
	{
		if($po_number=="") $po_number=$job_array[$val]['po']; else $po_number.=','.$job_array[$val]['po'];
		if($job_number=="") $job_number=$job_array[$val]['job']; else $job_number.=','.$job_array[$val]['job'];
		if($buyer_id=="") $buyer_id=$job_array[$val]['buyer']; else $buyer_id.=','.$job_array[$val]['buyer'];
		if($ship_date=="") $ship_date=change_date_format($job_array[$val]['ship_date']); else $ship_date.=','.change_date_format($job_array[$val]['ship_date']);
		
		if($internal_ref=="") $internal_ref=$job_array[$val]['ref']; else $internal_ref.=','.$job_array[$val]['ref'];
		if($job_array[$val]['file_no']>0)
		{
			if($file_nos=="") $file_nos=$job_array[$val]['file_no']; else $file_nos.=','.$job_array[$val]['file_no'];
		}
	}
	
	$job_no=implode(",",array_unique(explode(",",$job_number)));
	$buyer=implode(",",array_unique(explode(",",$buyer_id)));
	$internal_ref=implode(",",array_unique(array_filter(explode(",",$internal_ref))));
	$file_nos=implode(",",array_unique(explode(",",$file_nos)));
	//$booking_without_order=return_field_value( "booking_no as booking_no", "wo_non_ord_samp_booking_mst","booking_no=$booking_no","booking_no");

	$booking_without_order=sql_select("select booking_no_prefix_num, buyer_id from wo_non_ord_samp_booking_mst where company_id=$company and booking_no='$booking_no' and booking_type=4");
	$booking_id=$booking_without_order[0][csf('booking_no_prefix_num')];
	$buyer_id_booking=$booking_without_order[0][csf('buyer_id')];

?>
    <div style="width:1060px;">
     <table width="1060" cellspacing="0" align="center" border="0">
        <tr>
            <td colspan="6" align="center" style="font-size:22px"><strong><? echo $company_library[$company]; ?></strong></td>
            <td colspan="2" align="left">Print Time:<? echo $date=date("F j, Y, g:i a"); ?></td>
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:18px"><strong><u>Batch Card</u></strong></td>
            <td colspan="2" id="barcode_img_id" align="right" style="font-size:24px"></td>
        </tr>
         <tr>
           <td colspan="8">&nbsp; </td> <td>&nbsp; </td>
        </tr>
        <tr>
           <td colspan="6" align="left" style="font-size:18px"><strong><u>Reference Details</u></strong></td>
           <td style="font-size:24px; border: solid 2px;" align="center" colspan="2">&nbsp;<? echo $dataArray[0][csf('organic')];?></td>
        </tr>
        <tr>
            <td width="120"><strong>Batch No</strong></td> <td width="135px">:&nbsp;<? echo $dataArray[0][csf('batch_no')]; ?></td>
            <td width="120"><strong>Batch SL</strong></td><td width="135px">:&nbsp;<? echo $batch_sl_no; ?></td>
            <td width="120"><strong>B. Color</strong></td><td width="135px">:&nbsp;<? echo $color_arr[$dataArray[0][csf('color_id')]]; ?></td>
            <td width="120"><strong>Color Ran.</strong></td><td width="135px">:&nbsp;<? echo $color_range[$dataArray[0][csf('color_range_id')]];?></td>
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
            <td><strong>Ship Date</strong></td><td>:&nbsp;<? if(trim($ship_date)!="0000-00-00" && trim($ship_date)!="") echo implode(",",array_unique(explode(",",$ship_date))); else echo "&nbsp;"; ?></td>
        </tr>
        <tr>
        	<td><strong>Collar Qty (Pcs)</strong></td><td>:&nbsp;<? echo $dataArray[0][csf('collar_qty')]; ?></td>
            <td><strong>Cuff Qty (Pcs)</strong></td><td>:&nbsp;<? echo $dataArray[0][csf('cuff_qty')]; ?></td>
            <td><strong>Int. Ref.</strong></td><td>:&nbsp;<? echo $internal_ref; ?></td>
            <td><strong>File No</strong></td><td>:&nbsp;<? echo $file_nos; ?></td>
        </tr>
        <tr>
        	<td><strong>Dying Machine</strong></td><td>:&nbsp;
			<?
			if($db_type==2)
			{
				$dyeing_machine= return_field_value("(machine_no || '-' || brand) as machine_name","lib_machine_name",  "id=".$dataArray[0][csf('dyeing_machine')],"machine_name" );
			}
			else if($db_type==0)
			{
				$dyeing_machine= return_field_value("concat(machine_no,'-',brand) as machine_name","lib_machine_name",  "id=".$dataArray[0][csf('dyeing_machine')],"machine_name" );
			}
			echo $dyeing_machine; 
			?></td>
        	<td><strong>Remarks</strong></td><td colspan="5">:&nbsp;<? echo $dataArray[0][csf('remarks')]; ?></td>
        </tr>
    </table>
    <div style="float:left; font-size:17px;"><strong><u>Fabrication Details</u></strong> </div>
    <table align="center" cellspacing="0" width="1060"  border="1" rules="all" class="rpt_table" style="border-top:none" >
        <thead bgcolor="#dddddd" align="center">
            <tr>
                <th width="30">SL</th>
                <th width="60">Prog. No</th>
                <th width="80">Machine /  Knitting Com</th>
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
                <th width="80"><strong>Brand</strong></th>
                <th width="80">Yarn Count</th>
                <th>ID Code</th>
            </tr>
        </thead>
		<?
			$i=1;
			$yarncount=return_library_array( "select id, yarn_count from  lib_yarn_count",'id','yarn_count');
			$brand_name_arr=return_library_array( "select id, brand_name from  lib_brand",'id','brand_name');
			$supplier_array_lib=return_library_array( "select id,short_name from  lib_supplier", "id", "short_name"  );
			$machine_array_lib_dia=return_library_array( "select id,dia_width from  lib_machine_name", "id", "dia_width"  );
			$machine_array_lib_gauge=return_library_array( "select id,gauge from  lib_machine_name", "id", "gauge"  );
			$supplier_from_prod=return_library_array("select lot,supplier_id from  product_details_master where item_category_id=1 ", "lot","supplier_id");
			
			$machine_lib_sql=sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
			foreach($machine_lib_sql as $row)
			{
				$dya_gauge_arr[$row[csf("id")]]["machine_no"]=$row[csf("machine_no")];
				$dya_gauge_arr[$row[csf("id")]]["dia_width"]=$row[csf("dia_width")];
				$dya_gauge_arr[$row[csf("id")]]["gauge"]=$row[csf("gauge")];
			}
			
			/*$yarn_lot_arr=array();
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
					
					$sample_arr[$rows[csf('prod_id')]]['yarncount']=$rows[csf('yarn_count')];
					$sample_arr[$rows[csf('prod_id')]]['stitch_length']=$rows[csf('stitch_length')];
					$sample_arr[$rows[csf('prod_id')]]['brand_id']=$rows[csf('brand_id')];
					$sample_arr[$rows[csf('prod_id')]]['samplelot']=implode(", ",array_unique($yarn_lot));
				}
				
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
			$fin_feb_data=sql_select("select a.id,a.program_no,c.machine_gg,c.machine_dia,b.color_type_id,c.fabric_dia from pro_batch_create_dtls a, ppl_planning_info_entry_mst b,ppl_planning_info_entry_dtls c where a.program_no=c.id and b.id=c.mst_id");
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
	/*$sql_dtls="select a.booking_no_id, a.booking_without_order, SUM(b.batch_qnty) AS batch_qnty, sum(b.roll_no) as roll_no, b.item_description, b.program_no, b.po_id, b.prod_id, b.body_part_id, b.width_dia_type,count(b.width_dia_type) as num_of_rows 
	from pro_batch_create_mst a,pro_batch_create_dtls b 
	where a.company_id=$data[0] and a.id=b.mst_id and a.id=$batch_update_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 
	group by a.booking_no_id,a.booking_without_order,b.prod_id,b.po_id,b.program_no,b.body_part_id,b.item_description,b.width_dia_type order by b.program_no";
	
	$sql_dtls="select a.booking_no_id, a.booking_without_order, SUM(b.batch_qnty) AS batch_qnty, sum(b.roll_no) as roll_no, b.item_description, b.program_no, b.po_id, b.prod_id, b.body_part_id, b.width_dia_type, count(b.width_dia_type) as num_of_rows, d.machine_no_id, e.knitting_source, e.knitting_company, e.id as rcv_id
	from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d,  inv_receive_master e 
	where a.id=b.mst_id and b.roll_id=c.id and c.dtls_id=d.id and d.mst_id=e.id and a.company_id=$data[0] and a.id=$batch_update_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22)
	group by a.booking_no_id,a.booking_without_order,b.prod_id,b.po_id,b.program_no,b.body_part_id,b.item_description,b.width_dia_type, d.machine_no_id, e.knitting_source, e.knitting_company, e.id order by b.program_no";*/
	
	
	
	if($db_type==0)
	{
		$sql_dtls="select a.booking_no_id, a.booking_without_order, SUM(b.batch_qnty) AS batch_qnty, sum(b.roll_no) as roll_no, b.item_description, b.program_no, b.prod_id, b.body_part_id, b.width_dia_type, count(b.width_dia_type) as num_of_rows, d.machine_no_id, group_concat(d.yarn_lot) as yarn_lot, group_concat(d.yarn_count) as yarn_count, d.stitch_length as stitch_length, group_concat(d.brand_id) as brand_id, e.knitting_source, e.knitting_company
		from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d,  inv_receive_master e 
		where a.id=b.mst_id and b.roll_id=c.id and c.dtls_id=d.id and d.mst_id=e.id and a.company_id=$data[0] and a.id=$batch_update_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22)
		group by a.booking_no_id,a.booking_without_order,b.prod_id,b.program_no,b.body_part_id,b.item_description,b.width_dia_type, d.machine_no_id, d.stitch_length, e.knitting_source, e.knitting_company order by b.program_no";
		
	}
	else
	{
		 $sql_dtls="select a.booking_no_id, a.booking_without_order, SUM(b.batch_qnty) AS batch_qnty, sum(b.roll_no) as roll_no, b.item_description, b.program_no, b.prod_id, b.body_part_id, b.width_dia_type, count(b.width_dia_type) as num_of_rows, d.machine_no_id, LISTAGG(CAST(d.yarn_lot AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.yarn_lot) as yarn_lot, LISTAGG(CAST(d.yarn_count AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.yarn_count) as yarn_count, d.stitch_length as stitch_length, LISTAGG(CAST(d.brand_id AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.brand_id) as brand_id, e.knitting_source, e.knitting_company
		from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d,  inv_receive_master e 
		where a.id=b.mst_id and b.roll_id=c.id and c.dtls_id=d.id and d.mst_id=e.id and a.company_id=$data[0] and a.id=$batch_update_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22)
		group by a.booking_no_id,a.booking_without_order,b.prod_id,b.program_no,b.body_part_id,b.item_description,b.width_dia_type, d.machine_no_id, d.stitch_length, e.knitting_source, e.knitting_company order by b.program_no";
	}
	
	//echo $sql_dtls;die;
	$sql_result=sql_select($sql_dtls);
	foreach($sql_result as $row)
	{ 
		if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			//echo $row[csf('prod_id')].'='.$row[csf('po_id')];
		$desc=explode(",",$row[csf('item_description')]);
		
		/*if($batch_against_id==3 && $row[csf('booking_without_order')]==1)
		{
			$yarn_lot=$sample_arr[$row[csf('booking_no_id')]][$row[csf('prod_id')]]['samplelot'];
			$y_count=$sample_arr[$row[csf('booking_no_id')]][$row[csf('prod_id')]]['yarncount'];
			$stitch=$sample_arr[$row[csf('booking_no_id')]][$row[csf('prod_id')]]['stitch_length'];	
			$yarn_count_value=$yarncount[$y_count];
			$dya_gage=$dya_gauge_arr[$sample_arr[$row[csf('booking_no_id')]][$row[csf('prod_id')]]['machine_no_id']]["dia_width"]."<br>".$dya_gauge_arr[$sample_arr[$row[csf('booking_no_id')]][$row[csf('prod_id')]]['machine_no_id']]["gauge"];
			
		}
		else
		{
			$y_count=chop($yarn_count[$row[csf('prod_id')]][$row[csf('po_id')]]['yarn_count'],",");
			$y_count_id=array_unique(explode(',',$y_count));
			$yarn_count_value='';
			//$dya_gage=$dya_gauge_arr[$machine_no_id[$row[csf('prod_id')]][$row[csf('po_id')]]['machine_no_id']]["dia_width"]."<br>".$dya_gauge_arr[$machine_no_id[$row[csf('prod_id')]][$row[csf('po_id')]]['machine_no_id']]["gauge"];
			foreach($y_count_id as $val)
			{
				if($val>0)
				{
					if($yarn_count_value=='') $yarn_count_value=$yarncount[$val]; else $yarn_count_value.=", ".$yarncount[$val];
				}
			}
			
			$machine_no_id_arr=array_unique(explode(',',chop($machine_no_id[$row[csf('prod_id')]][$row[csf('po_id')]]['machine_no_id'],",")));
			$dya_gage="";
			foreach($machine_no_id_arr as $machine_id)
			{
				$dya_gage=$dya_gauge_arr[$machine_id]["dia_width"]."<br>".$dya_gauge_arr[$machine_id]["gauge"];
			}
		
		$stitch=implode(", ", array_unique(explode(",",chop($yarn_lot_arr[$row[csf('prod_id')]][$row[csf('po_id')]]['stitch_length'],','))));	
		$yarn_lot=implode(", ", array_unique(explode(",",chop($yarn_lot_arr[$row[csf('prod_id')]][$row[csf('po_id')]]['lot'],','))));
		}*/
		
		$yarn_lot=implode(",",array_unique(explode(",",$row[csf('yarn_lot')])));
		$y_count=array_unique(explode(",",$row[csf('yarn_count')]));
		$brand_id=array_unique(explode(",",$row[csf('brand_id')]));
		$yarn_count_value="";
		foreach($y_count as $val)
		{
			if($val>0)
			{
				if($yarn_count_value=='') $yarn_count_value=$yarncount[$val]; else $yarn_count_value.=", ".$yarncount[$val];
			}
		}
		$brand_value="";
		foreach($brand_id as $bid)
		{
			if($bid>0)
			{
				if($brand_value=='') $brand_value=$brand_name_arr[$bid]; else $brand_value.=", ".$brand_name_arr[$bid];
			}
		}
		$stitch=implode(",",array_unique(explode(",",$row[csf('stitch_length')])));	
		$dya_gage=$dya_gauge_arr[$row[csf('machine_no_id')]]["dia_width"]."<br>".$dya_gauge_arr[$row[csf('machine_no_id')]]["gauge"];
		
		//$yarn_lot_arr[$rows[csf('prod_id')]][$rows['po_breakdown_id']]['stitch_length']
		//$st_len=implode(", ", array_unique(explode(",",$stitch)));
		//$machine_dia_up=$machine_array_lib_dia[$machine_dia[$row[csf('program_no')]]['m_dia']];
		//$machine_gauge_up=$machine_array_lib_gauge[$machine_dia[$row[csf('program_no')]]['m_gauge']];
		?>
            <tr bgcolor="<? echo $bgcolor; ?>" >
                <td  width="30" align="center" ><? echo $i; ?></td>
                <td width="60" align="center" style="word-break:break-all;"><? echo $row[csf('program_no')]; ?></td>
                <?
				if($row[csf('knitting_source')]==1)
				{
					$machin_knit_com=$machine_library[$row[csf('machine_no_id')]];
				}
				else
				{
					$machin_knit_com=$supplier_library[$row[csf('knitting_company')]];
				}
				?>
                <td width="80" style="word-break:break-all;" align="center" ><? echo $machin_knit_com; ?></td>
                <td width="80" style="word-break:break-all;"><? echo $body_part[$row[csf('body_part_id')]]; ?></td>
                <td width="150" style="word-break:break-all;"><? echo $desc[0].",".$desc[1]; ?></td>
                <td width="50" align="center" style="word-break:break-all;"><? echo $desc[2]; ?></td>
                <td width="50" align="center" style="word-break:break-all;"><? echo $desc[3]; ?></td>
                <td width="70" align="center" style="word-break:break-all;"><? echo $dya_gage; ?></td>
                <td width="70" style="word-break:break-all;"><? echo $fabric_typee[$row[csf('width_dia_type')]];  ?></td>
                <td width="60" align="center" style="word-break:break-all;"><? echo $stitch; ?></td>
                <td width="70" align="right" style="word-break:break-all;"><? echo number_format($row[csf('batch_qnty')],2); ?></td>
                <td align="center" width="50" style="word-break:break-all;"><? echo $row[csf('num_of_rows')]; ?></td>
                <td width="80" style="word-break:break-all;"><? echo implode(',',array_unique(explode(",",$yarn_lot)));  ?></td>
                <td width="80" style="word-break:break-all;">
				<? 
				/*$all_suplier="";
				$yarn_lot_arr=explode(",",$yarn_lot);
				foreach($yarn_lot_arr as $lot)
				{
					if($supplier_array_lib[$supplier_from_prod[$lot]]!="")
					{
						$all_suplier.=$supplier_array_lib[$supplier_from_prod[$lot]].",";
					}
				}
				$all_suplier=implode(',',array_unique(explode(",",chop($all_suplier,",")))); 
				echo $all_suplier;*/
				echo  $brand_value;
				?></td>
                <td width="80" style="word-break:break-all;" align="center" ><? echo $yarn_count_value;?></td>
              	<td>&nbsp;</td>
            </tr>
		<?php
        $total_roll_number+= $row[csf('num_of_rows')];
        $total_batch_qty+= $row[csf('batch_qnty')];
        $i++;
    }
	?>
            <tr>
                <td style="border:none;" colspan="10" align="right"><b>Sum:</b> <? //echo $b_qty; ?> </td>
                <td align="right"><b><? echo number_format($total_batch_qty,2); ?> </b></td>
                <td align="center"><b><? echo $total_roll_number; ?> </b></td>
                <td colspan="4" style="border:none;">&nbsp;</td>
            </tr>
            <tr>
                <td style="border:none;" colspan="10" align="right"><b>Trims Weight:</b> <? //echo $b_qty; ?> </td>
                <td align="right"><b><? echo number_format($dataArray[0][csf('total_trims_weight')],2); ?> </b></td>
                <td colspan="5" style="border:none;">&nbsp;</td>
            </tr>
             <tr>
                <td style="border:none;" colspan="10" align="right"><b>Total:</b>  </td>
                <td align="right"><b><? echo number_format($total_batch_qty+$dataArray[0][csf('total_trims_weight')],2);  ?> </b></td>
                <td colspan="5" style="border:none;">&nbsp;</td>
            </tr>
             <tr>
                <td colspan="16"  align="right">&nbsp; </td>
            </tr>
         <tr>
            <td colspan="16"  align="right">
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
           <table align="left" rules="all" class="rpt_table" width="1060">
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
           Heat Setting:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;   Loading Date: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;  UnLoading Date:&nbsp;
             </td>
            </tr>
          </table>
             </td>
    	</tr>
    </table>
     <div style="float:left; margin-left:10px;"><strong> Quality Instruction(Hand Written)</strong> </div>
    <table width="1060" cellspacing="0" align="center" >
        <tr>
            <td valign="top" align="left" width="520">
                <table cellspacing="0" width="515"  align="left" border="1" rules="all" class="rpt_table">
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
            <td width="520" valign="top" align="right">
                <table width="515"  cellspacing="0"  border="1" rules="all" class="rpt_table">
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
            <td width="520" valign="top">
                <table width="515" cellspacing="0" border="1" rules="all" class="rpt_table">
                    <tr>
                        <th align="left"><strong>Shade Result(<i>Hand Written</i>)</strong></th>
                    </tr>
                    <tr>
                        <td colspan="1" style="width:475px; height:80px" >&nbsp;</td>
                    </tr>
                </table>
        	</td>
            <td width="10" align="justify" valign="top">&nbsp;</td>
            <td width="520" valign="top" align="right">
                <table cellspacing="0" border="1" rules="all" class="rpt_table" width="515" >
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
            <td width="1060" colspan="3">
                <table cellspacing="0" border="1" rules="all" class="rpt_table" width="1060" >
                    <tr>
                        <th align="center"><strong>Other Information(<i>Hand Written</i>)</strong></th>
                    </tr>
                    <tr>
                        <td style="width:1060px; height:120px" >&nbsp;</td>
                    </tr>
                </table> 
            </td>
        </tr>
    </table>
     <br>
		 <?
            echo signature_table(52, $company, "1060px");
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

if ($action=="trims_weight_popup")
{
	echo load_html_head_contents("Trims Weight Entry", "../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $save_data;
	
?> 
	<script>
		var permission='<? echo $permission; ?>';
	function fn_addRow_trims( i )
	{ 
			//var row_num=$('#tbl_item_details tbody tr').length;
			//var lastTrId = $('#tbl_list tbody tr:last').attr('id').split('_');
			//alert(lastTrId);
			//var row_num=lastTrId[1];
			var row_num=$('#tbl_list tbody tr').length;
			//alert(lastTrId[1]);
			if (row_num!=i)
			{
				return false;
			}
			else
			{ 
				i++;
		
				$("#tbl_list tbody tr:last").clone().find("input,select").each(function(){
					  
				$(this).attr({ 
				  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
				  'name': function(_, name) { return name },
				  'value': function(_, value) { return value }              
				});
				 
				}).end().appendTo("#tbl_list");
					
				$('#slTd_'+i).val('');
				$('#txtitemDesc_'+i).val('');
				$('#trimsWeight_'+i).val('');
				$('#remarks_'+i).val('');
				$("#tbl_list tbody tr:last").removeAttr('id').attr('id','tr_'+i);
				$('#tr_' + i).find("td:eq(0)").removeAttr('id').attr('id','slTd_'+i);
				$('#tr_' + i).find("td:eq(0)").text(i);
				
				$('#increase_'+i).removeAttr("value").attr("value","+");
				$('#decrease_'+i).removeAttr("value").attr("value","-");
				$('#increase_'+i).removeAttr("onclick").attr("onclick","fn_addRow_trims("+i+");");
				$('#decrease_'+i).removeAttr("onclick").attr("onclick","fn_deleteRow("+i+");");
			}
			set_all_onclick();
	}
	
	function fn_deleteRow(rowNo) 
		{ 		
			
			var row_num=$('#tbl_list tbody tr').length;
			
			if(row_num!=1)
			{
				//alert(row_num);
				$("#tr_"+rowNo).remove();
			}
		}
		
		function window_close()
		{
			var save_data=''; var tot_trims_qnty='';

			$("#tbl_list").find('tr').each(function()
			{
				var txtitemDesc=$(this).find('input[name="txtitemDesc[]"]').val();
				var trimsWeight=$(this).find('input[name="trimsWeight[]"]').val();
				var remarks=$(this).find('input[name="remarks[]"]').val();
				if(trimsWeight*1>0)
				{
					
					if(save_data=="")
					{
						save_data=txtitemDesc+"_"+trimsWeight+"_"+remarks;
					}
					else
					{
						save_data+=","+txtitemDesc+"_"+trimsWeight+"_"+remarks;
					}
					tot_trims_qnty=tot_trims_qnty*1+trimsWeight*1;
					
				}
				
			});
			//alert(tot_trims_qnty);
			$('#save_data').val( save_data );
			$('#tot_trims_qnty').val(tot_trims_qnty);
			parent.emailwindow.hide();
		}
		
		function calculate_trims_qnty()
			{
			var total_trims_qnty='';
			$("#tbl_list tbody").find('tr').each(function()
			{
				var trimsQnty=$(this).find('input[name="trimsWeight[]"]').val();
				total_trims_qnty=total_trims_qnty*1+trimsQnty*1;
			});
			
			$('#txt_total_trims_qnty').val(total_trims_qnty.toFixed(2));
			
			}
	
    </script>

</head>

<body>
<div align="center">
	<? echo load_freeze_divs ("../../",$permission,1); ?>
	<form name="trimsWeight_1" id="trimsWeight_1">
		
        <fieldset style="width:600px; margin-top:10px">
            <legend>Batch Trims Pop Up</legend>
            <?
            if($save_data!="")
			{
			?>
            <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="600" id="tbl_list">
            	<thead>
                    <th width="40">SL</th>
                    <th width="200">Item Description</th>
                    <th width="80">Weight In Kg</th>
                    <th width="150">Remarks</th>
                    <th></th>
                    <input type="hidden" name="save_data" id="save_data" class="text_boxes">
                     <input type="hidden" name="tot_trims_qnty" id="tot_trims_qnty" class="text_boxes">
                </thead>
                <tbody>
                
                <?
				$tot_trims_wgt=0;$k=0;
               $explSaveData = explode(",",$save_data); 
				for($z=0;$z<count($explSaveData);$z++)
				{
					$data_all=explode("_",$explSaveData[$z]);
					$item_des=$data_all[0];
					$trims_wgt=$data_all[1];
					$remark=$data_all[2];
					$tot_trims_wgt+=$trims_wgt;
					$k++;
				
				?>
                    <tr id="tr_<? echo $k;?>">
                    <td id="slTd_<? echo $k;?>" width="30"><? echo $k;?></td>
                        <td>
                        <input type="text" name="txtitemDesc[]" id="txtitemDesc_<? echo $k;?>" class="text_boxes" style="width:200px;"  value="<? echo $item_des;?>"/>
                        </td>
                        <td>
                        <input type="text" name="trimsWeight[]" id="trimsWeight_<? echo $k;?>" class="text_boxes_numeric" style="width:80px;" onKeyUp="calculate_trims_qnty();"   value="<? echo $trims_wgt;?>"/>
                        </td>
                        <td>
                        <input type="text" name="remarks[]" id="remarks_<? echo $k;?>" class="text_boxes" style="width:150px;"  value="<? echo $remark;?>"/>
                        </td>
                        
                        <td>
                        <input type="button" id="increase_<? echo $k;?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fn_addRow_trims(<? echo $k;?>)" />
                        <input type="button" id="decrease_<? echo $k;?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $k;?>);"/>
                        </td>
        			</tr>
                    <?
				}
					?>
                   
                </tbody> 
                <tfoot class="tbl_bottom">
                        <td>&nbsp;</td>
                        
                        <td>Sum</td>
                        <td><input type="text" name="txt_total_trims_qnty" id="txt_total_trims_qnty" class="text_boxes_numeric" style="width:80px" readonly  value="<? echo $tot_trims_wgt;?>" /></td>
                        
                        <td colspan="2">&nbsp;</td>
                    </tfoot>   
            </table>
			<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="600">
             	
                <tr>
                    <td colspan="5" align="center">
						
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="window_close();" style="width:80px" />
                      
                    </td>	  
                </tr>
			</table>
            
            <?
			}
			else
			{ ?>
			<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="600" id="tbl_list">
            	<thead>
                    <th width="40">SL</th>
                    <th width="200">Item Description</th>
                    <th width="80">Weight In Kg</th>
                    <th width="150">Remarks</th>
                    <th></th>
                    <input type="hidden" name="save_data" id="save_data" class="text_boxes">
                     <input type="hidden" name="tot_trims_qnty" id="tot_trims_qnty" class="text_boxes">
                </thead>
                <tbody>
                    <tr id="tr_1">
                    <td id="slTd_1" width="30">1</td>
                        <td>
                        <input type="text" name="txtitemDesc[]" id="txtitemDesc_1" class="text_boxes" style="width:200px;"/>
                        </td>
                        <td>
                        <input type="text" name="trimsWeight[]" id="trimsWeight_1" class="text_boxes_numeric" style="width:80px;" onKeyUp="calculate_trims_qnty();"/>
                        </td>
                        <td>
                        <input type="text" name="remarks[]" id="remarks_1" class="text_boxes" style="width:150px;"/>
                        </td>
                        
                        <td>
                        <input type="button" id="increase_1" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fn_addRow_trims(1)" />
                        <input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);"/>
                        </td>
        			</tr>
                   
                </tbody> 
                <tfoot class="tbl_bottom">
                        <td>&nbsp;</td>
                        
                        <td>Sum</td>
                        <td><input type="text" name="txt_total_trims_qnty" id="txt_total_trims_qnty" class="text_boxes_numeric" style="width:80px" readonly /></td>
                        
                        <td colspan="2">&nbsp;</td>
                    </tfoot>   
            </table>
			<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="600">
             	
                <tr>
                    <td colspan="5" align="center">
						
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="window_close();" style="width:80px" />
                      
                    </td>	  
                </tr>
			</table>	
		<? }
			?>
		</fieldset>
	</form>
</div>
</body>  
		
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

?>