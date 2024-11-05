<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

/*
|--------------------------------------------------------------------------
| get_buyer_array
| written by : Md. Nuruzzaman
|--------------------------------------------------------------------------
|This function will return buyer array
|
*/
function get_buyerShort_array()
{
	$data = return_library_array("SELECT id, short_name FROM lib_buyer", "id", "short_name");
	return $data;
}

/*
|--------------------------------------------------------------------------
| get_buyer_array
| written by : Md. Nuruzzaman
|--------------------------------------------------------------------------
|This function will return buyer array
|
*/
function get_po_information($poBreakdownId)
{
	global $buyer_name_array;
	$data=array();
	$sql="SELECT 
			a.job_no, a.job_no_prefix_num, a.buyer_name, a.insert_date, a.style_ref_no, b.id, b.po_number 
		FROM 
			wo_po_details_master a 
			INNER JOIN wo_po_break_down b ON a.job_no = b.job_no_mst
		WHERE 
			b.id IN(".implode(",",$poBreakdownId).")";
	
	$sqlPo=sql_select($sql);
	foreach($sqlPo as $row)
	{
		$data[$row[csf('id')]]['job_no'] = $row[csf('job_no_prefix_num')];
		$data[$row[csf('id')]]['job_no_full'] = $row[csf('job_no')];
		$data[$row[csf('id')]]['buyer_name'] = $buyer_name_array[$row[csf('buyer_name')]];
		$data[$row[csf('id')]]['buyer_id'] = $row[csf('buyer_name')];
		$data[$row[csf('id')]]['style_ref_no'] = $row[csf('style_ref_no')];
		$data[$row[csf('id')]]['year'] = date('Y',strtotime($row[csf('insert_date')]));
		$data[$row[csf('id')]]['po_number'] = $row[csf('po_number')];
	}
	return $data;
}

//Yarn Count Determin
function get_constructionComposition($yarnCountDeterminId)
{
	$i = 0;
	$id = '';
	$data = array();
	$construction = '';
	$composition_name = '';
	$sqlYarnCount = sql_select("SELECT a.id, a.construction, b.percent, c.composition_name 
	FROM lib_yarn_count_determina_mst a 
	INNER JOIN lib_yarn_count_determina_dtls b ON a.id = b.mst_id
	INNER JOIN lib_composition_array c ON b.copmposition_id = c.id 
	WHERE a.id IN(".implode(",",$yarnCountDeterminId).")");
	foreach( $sqlYarnCount as $row )
	{
		$id=$row[csf('id')];
		if($i==0)
		{
			$construction.= $row[csf('construction')].", ";
			$i++;
		}
		
		if($composition_name != '')
		{
			$composition_name .= ', ';
		}
		$composition_name .= $row[csf('composition_name')]." ".$row[csf('percent')]."%";
	}
	$data[$id] = $construction.$composition_name;
	return $data;
}

//batch
function get_batchFor_GreyRollIssueToProcess($barCode)
{
	$data=array();
	$sqlBatch=sql_select("SELECT a.id, a.batch_no, a.color_id, b.po_id, b.barcode_no  
	FROM pro_batch_create_mst a 
	INNER JOIN pro_batch_create_dtls b ON a.id = b.mst_id
	WHERE a.status_active=1 
	AND a.is_deleted = 0 
	AND b.barcode_no IN(".implode(",",$barCode).")");

	foreach($sqlBatch as $row)
	{
		$data[$row[csf('barcode_no')]][$row[csf('po_id')]]['batch_id']=$row[csf("id")];
		$data[$row[csf('barcode_no')]][$row[csf('po_id')]]['batch_no']=$row[csf("batch_no")];	
	}
	
	return $data;
}

function get_color_details($colorId)
{
	global $color_arr;
	$colorName='';
	$expColorId=explode(",",$colorId);
	foreach($expColorId as $id)
	{
		if($id>0)
			$colorName.=$color_arr[$id].",";
	}
	$colorName=chop($colorName,',');
	return $colorName;
}

//knitting_company
function get_knitting_company_details($knittingSource, $knittingCompany)
{ 
	global $company_name_array;
	global $supplier_arr;
	$data='';
	if($knittingSource == 1)
	{
		$data=$company_name_array[$knittingCompany];
	}
	else if($knittingSource ==3 )
	{
		$data=$supplier_arr[$knittingCompany];
	}
	return $data;
}

//receive_basis
function get_receive_basis($entryForm, $receiveBasis)
{
	$data=array();
	if(($entryForm==2 && $receiveBasis==0) || ($entryForm==22 && ($receiveBasis==4 || $receiveBasis==6)))
	{
		$data['id']=0;
		$data['dtls']='Independent';
	}
	else if(($entryForm==2 && $receiveBasis==1) || ($entryForm==22 && $receiveBasis==2)) 
	{
		$data['id']=2;
		$data['dtls']="Booking";
	}
	else if($entryForm==2 && $receiveBasis==2) 
	{
		$data['id']=3;
		$data['dtls']="Knitting Plan";
	}
	else if($entryForm==22 && $receiveBasis==1) 
	{
		$data['id']=1;
		$data['dtls']="PI";
	}
	return $data;
}

//===============================
//$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
$color_arr = get_color_array();
//print_r($color_arr);die;

if($action == 'grey_item_details_update' || $action == 'grey_item_details' || $action=="grey_item_details_both")
{
	$buyer_name_array = get_buyerShort_array();
	$company_name_array = return_library_array( "select id,company_name from lib_company", "id", "company_name");
	//$company_name_array = get_company_array();
	$supplier_arr = get_supplier_array();
}

if($action=="action_wo_popup")
{
	echo load_html_head_contents("WO Recv Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?> 
	<script>
		function js_set_value(bookingNo, ChallanNo, ChallanId, companyId, knittingSource, knittingCompany, booking_mst_id, process_id, wo_entry_form)
		{
			$('#hiddenBookingNo').val(bookingNo);
 			$('#hiddenChallanNo').val(ChallanNo);
			$('#hiddenChallanId').val(ChallanId);
			$('#hiddenCompanyId').val(companyId);
			$('#hiddenKnittingSource').val(knittingSource);
			$('#hiddenKnittingCompany').val(knittingCompany);
			$('#hiddenBookingMstId').val(booking_mst_id);
			$('#hiddenProcessId').val(process_id);
			$('#hiddenWOEntryForm').val(wo_entry_form);

			parent.emailwindow.hide();
		}
		function fnc_show()
		{
			if(form_validation('cbo_company_id','Company')==false)
			{
				return; 
			}
			show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_year_selection').value, 'create_wo_recv_search_list_view', 'search_div', 'aop_roll_receive_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);')
		}
    </script>
    </head>
    <body>
        <div align="center" style="width:760px;" >
            <form name="searchwofrm"  id="searchwofrm">
                <fieldset style="width:760px; margin-left:2px">
                <legend>Enter search words</legend>           
                    <table cellpadding="0" cellspacing="0" width="750" border="1" rules="all" class="rpt_table" align="center">
                        <thead>
                            <th>Company</th>
                            <th>Booking Date Range</th>
                            <th>Search By</th>
                            <th id="search_by_td_up" width="180">Please Enter Booking No</th>
                            <th>
                                <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                                <input type="hidden" name="hidden_challan_no" id="hidden_challan_no">  
                                <input type="hidden" name="hidden_challan_id" id="hidden_challan_id">  
                                <input type="hidden" name="hidden_booking_no" id="hidden_booking_no">
                                <input type="hidden" name="hidden_company_id" id="hidden_company_id">
                                <input type="hidden" name="hidden_knitting_source" id="hidden_knitting_source">
                                <input type="hidden" name="hidden_knitting_company" id="hidden_knitting_company"> 
                                
                                 
                                <input type="hidden" name="hiddenChallanNo" id="hiddenChallanNo">  
                                <input type="hidden" name="hiddenChallanId" id="hiddenChallanId">  
                                <input type="hidden" name="hiddenBookingNo" id="hiddenBookingNo">
                                <input type="hidden" name="hiddenCompanyId" id="hiddenCompanyId">
                                <input type="hidden" name="hiddenKnittingSource" id="hiddenKnittingSource">
                                <input type="hidden" name="hiddenKnittingCompany" id="hiddenKnittingCompany">  
                                <input type="hidden" name="hiddenBookingMstId" id="hiddenBookingMstId">  
                                <input type="hidden" name="hiddenProcessId" id="hiddenProcessId">  
                                <input type="hidden" name="hiddenWOEntryForm" id="hiddenWOEntryForm">  
                            </th> 
                        </thead>
                        <tr class="general">
                            <td align="center"><? echo create_drop_down( "cbo_company_id", 150,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, '-- Select Company --','0','',0); ?></td>
                            <td align="center">
                                <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
                                <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
                            </td>
                            <td align="center">	
                                <?
                                    $search_by_arr=array(1=>"Booking No",2=>"System No");
                                    $dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
                                    echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"", 0, "--Select--", 1,$dd,0 );
                                ?>
                            </td>     
                            <td align="center" id="search_by_td">				
                                <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                            </td> 						
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="fnc_show()" style="width:100px;" />
                             </td>
                        </tr>
                        <tr>
                            <td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                        </tr>
                   </table>
                   <div style="width:100%; margin-top:5px;" id="search_div" align="left"></div>
                </fieldset>
            </form>
        </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?php
}

if($action=="create_wo_recv_search_list_view")
{
	$data = explode("_",$data);
	$search_string=$data[0];
	$search_by=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[4];
	$year_id =$data[5];
	
	if($company_id==0)
	{
		echo "Please Select Company First.";
		die;
	}
	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and c.booking_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
			$date_cond_2="and c.wo_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and c.booking_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
			$date_cond_2="and c.wo_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}
	else
	{
		$date_cond="";
	}
	
	$search_field_cond="";
	if($db_type==0) 
	{
		$year_field=" YEAR(c.insert_date) as year"; $year_search=" and YEAR(a.insert_date)=$year_id ";
	}
	else if($db_type==2) 
	{
		$year_field=" to_char(c.insert_date,'YYYY') as year";$year_search="  to_char(a.insert_date,'YYYY')=$year_id ";
	}
	
	if(trim($data[0])!="")
	{
		if($search_by==1)
			$search_field_cond="and a.wo_no like '%$search_string%' ";
		else if($search_by==2)
			$search_field_cond="and a.recv_number_prefix_num=$search_string and $year_search";
	}
	
	
	  $sql="
		SELECT 
			a.id, a.recv_number_prefix_num, a.recv_number, a.challan_no, a.company_id, a.dyeing_source, a.dyeing_company, a.receive_date, $year_field, 
			b.booking_id, c.id as booking_mst_id, c.booking_no, c.booking_date, c.company_id as book_company, c.supplier_id, c.pay_mode,
			d.barcode_no, a.process_id, a.wo_entry_form
		FROM 
			inv_receive_mas_batchroll a, 
			pro_grey_batch_dtls b, 
			wo_booking_mst c, 
			pro_roll_details d
		WHERE 
			a.id = b.mst_id 
			AND a.wo_no = c.booking_no 
			AND a.entry_form = 63 
			AND a.process_id in(31,35)  
			AND c.booking_type = 3 
			AND a.company_id = ".$company_id." 
			AND a.status_active = 1 
			AND b.status_active = 1 
			AND c.status_active = 1
			AND d.entry_form = 63 
			AND a.id = d.mst_id 
			AND b.id = d.dtls_id 
			AND d.is_rcv_done=0
			".$search_field_cond." 
			".$date_cond." 
		union all
		SELECT a.id, a.recv_number_prefix_num, a.recv_number, a.challan_no, a.company_id, a.dyeing_source, a.dyeing_company, a.receive_date, to_char(c.insert_date,'YYYY') as year, b.booking_id, b.booking_id as booking_mst_id, c.booking_no, c.booking_date, c.company_id as book_company, c.supplier_id, c.pay_mode, d.barcode_no, a.process_id, a.wo_entry_form
		FROM inv_receive_mas_batchroll a, pro_grey_batch_dtls b, WO_NON_ORD_KNITDYE_BOOKING_MST c, pro_roll_details d 
		WHERE a.id = b.mst_id AND a.wo_no = c.booking_no AND a.id = d.mst_id AND b.id = d.dtls_id AND a.entry_form = 63 AND a.process_id in(31,35)
		AND a.company_id = ".$company_id."  
		AND a.status_active = 1 AND b.status_active = 1 AND c.status_active = 1 
		AND d.entry_form = 63 AND d.is_rcv_done=0 ".$search_field_cond." 
			".$date_cond." 

		union all
		SELECT a.id, a.recv_number_prefix_num, a.recv_number, a.challan_no, a.company_id, a.dyeing_source, a.dyeing_company, a.receive_date, to_char(c.insert_date,'YYYY') as year, b.booking_id, b.booking_id as booking_mst_id, c.do_no as booking_no, c.wo_date as booking_date, c.company_id as book_company, c.DYEING_COMPNAY_ID as supplier_id, c.pay_mode, d.barcode_no, a.process_id, a.wo_entry_form 
		FROM inv_receive_mas_batchroll a, pro_grey_batch_dtls b, DYEING_WORK_ORDER_MST c, pro_roll_details d 
		WHERE a.id = b.mst_id AND a.wo_no = c.do_no AND a.id = d.mst_id AND b.id = d.dtls_id AND a.entry_form = 63  
		AND a.company_id = ".$company_id."  
		AND a.status_active = 1 AND b.status_active = 1 AND c.status_active = 1 AND d.entry_form = 63 AND d.is_rcv_done=0 ".$search_field_cond." ".$date_cond_2."
		ORDER BY booking_no";
	//echo $sql;
	$result = sql_select($sql);
	//print_r($result);
	foreach ($result as $row)
	{ 
		//if(empty($receivedBarcode[$row[csf('barcode_no')]]))
		//{
			$aop_rec_arr[$row[csf('booking_no')]][$row[csf('pay_mode')]]['process_id']=$row[csf('process_id')];
			$aop_rec_arr[$row[csf('booking_no')]][$row[csf('pay_mode')]]['barcode_no']=$row[csf('barcode_no')];
			$aop_rec_arr[$row[csf('booking_no')]][$row[csf('pay_mode')]]['company_id']=$row[csf('company_id')];
			$aop_rec_arr[$row[csf('booking_no')]][$row[csf('pay_mode')]]['dyeing_source']=$row[csf('dyeing_source')];
			$aop_rec_arr[$row[csf('booking_no')]][$row[csf('pay_mode')]]['dyeing_company']=$row[csf('dyeing_company')];
			$aop_rec_arr[$row[csf('booking_no')]][$row[csf('pay_mode')]]['booking_no']=$row[csf('booking_no')];
			$aop_rec_arr[$row[csf('booking_no')]][$row[csf('pay_mode')]]['booking_mst_id']=$row[csf('booking_mst_id')];
			$aop_rec_arr[$row[csf('booking_no')]][$row[csf('pay_mode')]]['booking_date']=$row[csf('booking_date')];
			$aop_rec_arr[$row[csf('booking_no')]][$row[csf('pay_mode')]]['recv_number_prefix_num']=$row[csf('recv_number_prefix_num')];
			$aop_rec_arr[$row[csf('booking_no')]][$row[csf('pay_mode')]]['year']=$row[csf('year')];
			$aop_rec_arr[$row[csf('booking_no')]][$row[csf('pay_mode')]]['recv_number'].=$row[csf('recv_number')].',';
			$aop_rec_arr[$row[csf('booking_no')]][$row[csf('pay_mode')]]['mst_id'].=$row[csf('id')].',';
			$aop_rec_arr[$row[csf('booking_no')]][$row[csf('pay_mode')]]['wo_entry_form']=$row[csf('wo_entry_form')];
	
			if($row[csf('pay_mode')]==3 || $row[csf('pay_mode')]==5)
			{
				$aop_rec_arr[$row[csf('booking_no')]][$row[csf('pay_mode')]]['dye_comp']=$row[csf('book_company')];
			}
			else
			{
				$aop_rec_arr[$row[csf('booking_no')]][$row[csf('pay_mode')]]['dye_comp']=$row[csf('supplier_id')];
			}
		//}
	}
	unset($result);

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="140">Company</th>
            <th width="80">Booking No</th>
            <th width="80">System No</th>
            <th width="120">Dyeing Source</th>
            <th width="140">Dyeing Company</th>
            <th>Booking date</th>
        </thead>
	</table>
	<div style="width:820px; max-height:240px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table" id="tbl_list_search">  
		<?
        $i=1;
        foreach ($aop_rec_arr as $bookingNo=>$book_data)
        { 
			foreach ($book_data as $pay_mode_id=>$row)
			{ 
				if ($i%2==0)
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";	
				
				$knit_comp="&nbsp;";
				
				if($pay_mode_id==3 || $pay_mode_id==5)
					$knit_comp=$company_arr[$row[('dye_comp')]]; 
				else
					$knit_comp=$supllier_arr[$row[('dye_comp')]];
				
				$recv_numbers=implode(",",array_unique(explode(",",rtrim($row[('recv_number')],','))));
				 $mst_ids=implode(",",array_unique(explode(",",rtrim($row[('mst_id')],','))));
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $bookingNo; ?>', '<? echo $recv_numbers; ?>', '<? echo $mst_ids; ?>', '<? echo $row[('company_id')]; ?>', '<? echo $row[('dyeing_source')]; ?>', '<? echo $row[('dyeing_company')]; ?>', '<? echo $row[('booking_mst_id')]; ?>', '<? echo $row[('process_id')]; ?>', '<? echo $row[('wo_entry_form')]; ?>');"> 
				<td width="40"><? echo $i; ?></td>
				<td width="140" align="center"><p><? echo $company_arr[$row[('company_id')]]; ?></p></td>
				<td width="80" align="center"><p>&nbsp;<? echo $bookingNo; ?></p></td>
				<td width="80" align="center"><p>&nbsp;<? echo $recv_numbers; ?></p></td>
				
				<td width="120" align="center"><p><? echo $pay_mode[$pay_mode_id]; ?>&nbsp;</p></td>
				<td width="140" align="center"><p><? echo $knit_comp; ?>&nbsp;</p></td>
				<td align="center"><? echo change_date_format($row[('booking_date')]); ?></td>
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
 
//action_issue_challan_popup
if($action=="action_issue_challan_popup")
{
	echo load_html_head_contents("Challan Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?> 
	<script>
		function js_set_value2(data,id)
		{
			$('#hidden_challan_no').val(data);
			$('#hidden_challan_id').val(id);
			parent.emailwindow.hide();
		}
  
		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			if ( x.style )
			{
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
    	var selected_id = new Array();
		var selected_no = new Array();
		function js_set_value( str) 
		{
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 )
			{
				selected_id.push( $('#txt_individual_id' + str).val() );
				
			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
		//	alert(selected_id.length);
			var id = '';
			for( var i = 0; i < selected_id.length; i++ )
			{
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			//alert(id);
			$('#hiddenChallanId').val( id );
			
			//for recv_no
			if( jQuery.inArray( $('#txt_individual_no' + str).val(), selected_no ) == -1 )
			{
				selected_no.push( $('#txt_individual_no' + str).val() );
				
			}
			else
			{
				for( var i = 0; i < selected_no.length; i++ )
				{
					if( selected_no[i] == $('#txt_individual_no' + str).val() ) break;
				}
				selected_no.splice( i, 1 );
			}
			var no = '';
			for( var i = 0; i < selected_no.length; i++ )
			{
				no += selected_no[i] + ',';
			}
			no = no.substr( 0, no.length - 1 );
			
			$('#hidden_recv_nos').val( no );
		}
		
		function fnc_close()
		{
			parent.emailwindow.hide();
		}
		
		function reset_hide_field()
		{
			$('#hidden_recv_nos').val( '' );
			selected_id = new Array();
		}
	
    </script>
    </head>
    <body>
    <div align="center" style="width:760px;" >
        <form name="searchwofrm"  id="searchwofrm">
            <fieldset style="width:760px; margin-left:2px">
            <legend>Enter search words</legend>           
                <table cellpadding="0" cellspacing="0" width="750" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                        <th>Company</th>
                        <th>Receive Date Range</th>
                        <th>Search By</th>
                        <th id="search_by_td_up" width="180">Please Enter Challan No</th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                            <input type="hidden" name="hidden_challan_no" id="hidden_challan_no">  
                            <input type="hidden" name="hidden_challan_id" id="hidden_challan_id">
                            <input type="hidden" style="width:50px" name="hidden_challan_mst_id" id="hidden_challan_mst_id" value="<? echo $hidden_challan_id;?>">  
                            <input type="hidden" name="hidden_recv_nos" id="hidden_recv_nos">
                            <input type="hidden" name="hiddenChallanId" id="hiddenChallanId" style="width:50px"> 
                        </th> 
                    </thead>
                    <tr class="general">
                        <td align="center">
                             <? echo create_drop_down( "cbo_company_id", 150,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name",'id,company_name', 1, '-- Select Company --',$cbo_company_id,"",0); ?>        
                        </td>
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
                        </td>
                        <td align="center">	
                            <?
                                $search_by_arr=array(1=>"System No",2=>"Batch No");
                                $dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
                                echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"", 0, "--Select--", 1,$dd,0 );
                            ?> 
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                        </td> 						
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('hidden_challan_mst_id').value+'_'+'<?php echo $cbo_knitting_source; ?>'+'_'+'<?php echo $cbo_knitting_company; ?>', 'create_challan_search_list_view', 'search_div', 'aop_roll_receive_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                         </td>
                    </tr>
                    <tr>
                        <td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                    </tr>
               </table>
               <div style="width:100%; margin-top:5px;" id="search_div" align="left"></div>
            </fieldset>
        </form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
}

if($action=="create_challan_search_list_view")
{
	$data = explode("_",$data);
	$search_string = $data[0];
	$search_by = $data[1];
	$start_date = $data[2];
	$end_date = $data[3];
	$company_id = $data[4];
	$year_id = $data[5];
	$hidden_challan_id = $data[6];
	$knittingSource = $data[7];
	$knittingCompany = $data[8];
	
	if($company_id==0)
	{
		echo "Please Select Company First.";
		die;
	}
	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and receive_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and receive_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}
	else
	{
		$date_cond="";
	}
	
	$search_field_cond="";

	if($db_type==0) 
	{
		$year_field=" YEAR(a.insert_date) as year";
		$year_search=" and YEAR(a.insert_date)=$year_id ";
	}
	else if($db_type==2) 
	{
		$year_field=" to_char(a.insert_date,'YYYY') as year";
		$year_search="  to_char(a.insert_date,'YYYY')=$year_id ";
	}
	
	if(trim($data[0])!="")
	{
		if($search_by==1)
		{
			$search_field_cond="and a.recv_number_prefix_num=$search_string and $year_search";
		}
		else if($search_by==2)
		{
			$batch_cond="and c.batch_no='$search_string'";
			
		}
	}
		
			
	
	//echo $hidden_challan_id.'XXSSD';
	if($hidden_challan_id!="")
		$hidden_challan_cond="and a.id in($hidden_challan_id)";
	else
		$hidden_challan_cond="";
		
		 $sql_chk="SELECT a.id, a.recv_number_prefix_num, a.recv_number, a.challan_no, a.recv_number, a.company_id, a.dyeing_source, a.dyeing_company, a.receive_date,c.batch_no, $year_field
			FROM 
				inv_receive_mas_batchroll a,pro_grey_batch_dtls b,pro_batch_create_mst c
			WHERE 
				a.id=b.mst_id 
				AND b.batch_id=c.id 
				AND c.booking_no=b.booking_no
				AND a.entry_form=63 
				
				AND a.company_id=$company_id 
				AND a.dyeing_source=$knittingSource 
				AND a.dyeing_company=$knittingCompany 
				AND a.status_active=1
				AND b.status_active=1
				AND c.status_active=1
				$search_field_cond 
				$date_cond 
				$hidden_challan_cond 
				$batch_cond
			ORDER BY a.recv_number_prefix_num, a.receive_date";
			//AND a.process_id in(31,35 )
			//echo $sql_chk;
			
			$result_chk = sql_select($sql_chk);
			foreach($result_chk as $row)
			{
				$mst_idArr[$row[csf('id')]]=$row[csf('id')];
				$batch_no_idArr[$row[csf('id')]].=$row[csf('batch_no')].',';
			}
			//print_r($mst_idArr);
			$recv_cond_in="";
			if(count($mst_idArr)>0)
			{
			$recv_cond_in=where_con_using_array($mst_idArr,0,'a.id');
			}
	
	/*$sql="SELECT c.barcode_no,a.recv_number ,b.challan_no
	FROM 
		inv_receive_mas_batchroll a, 
		pro_roll_details c ,
		pro_grey_batch_dtls b
	WHERE a.id=c.mst_id 
	AND b.id=c.dtls_id
	AND a.id=b.mst_id
		AND a.status_active=1 
		AND a.is_deleted=0 
		AND b.status_active=1 
		AND b.is_deleted=0 
		AND c.status_active=1 
		AND c.is_deleted=0 
		AND a.entry_form=63 
		AND a.process_id=35
		AND a.company_id=$company_id 
		AND a.dyeing_source=$knittingSource 
		AND a.dyeing_company=$knittingCompany 
		AND c.entry_form=63 
		AND c.status_active=1 
		AND c.is_deleted=0";
	//echo $sql;	
	$data_array=sql_select($sql);
	$challan_barcode=array();
	$inserted_barcode=array();
	foreach($data_array as $val)
	{
		$issue_chk_barcodeArr[$val[csf('recv_number')]][$val[csf('barcode_no')]]=$val[csf('barcode_no')];
	}*/
	
	 $sql_recv="SELECT b.challan_no, a.barcode_no 
	FROM 
		pro_roll_details a, 
		inv_receive_mas_batchroll b,
		pro_grey_batch_dtls c
	WHERE 
		a.mst_id=b.id 
		AND c.id=a.dtls_id
		AND c.mst_id=b.id
		AND b.status_active=1 
		AND b.is_deleted=0 
		AND b.entry_form=65
		AND b.company_id=$company_id 
		AND b.dyeing_source=$knittingSource 
		AND b.dyeing_company=$knittingCompany 
		AND a.status_active=1 
		AND a.is_deleted=0 
		AND a.entry_form=65
		AND a.re_issued=0";
	//echo $sql;	
	$recv_inserted_roll=sql_select($sql_recv);
	foreach($recv_inserted_roll as $b_id)
	{
		$inserted_barcode[$b_id[csf('challan_no')]][$b_id[csf('barcode_no')]]=$b_id[csf('barcode_no')];	
	}
	
	$sql="SELECT a.id, a.recv_number_prefix_num, a.recv_number, a.challan_no, a.company_id, a.dyeing_source, a.dyeing_company, a.receive_date, $year_field,b.barcode_no
	FROM 
		inv_receive_mas_batchroll a,pro_roll_details b,pro_grey_batch_dtls c
	WHERE 
	a.id=b.mst_id
		AND c.id=b.dtls_id
		AND c.mst_id=a.id
		AND a.entry_form=63 
		AND b.entry_form=63 
		
		AND a.company_id=$company_id 
		AND a.dyeing_source=$knittingSource 
		AND a.dyeing_company=$knittingCompany 
		AND a.status_active=1 
		AND a.is_deleted=0 
		AND b.status_active=1 
		AND b.is_deleted=0 
		AND c.status_active=1 
		AND c.is_deleted=0 
		$search_field_cond 
		$date_cond 
		$hidden_challan_cond  $recv_cond_in
	ORDER BY a.recv_number_prefix_num, a.receive_date";
	//echo $sql;

	//AND a.process_id in(31,35 )
	
	$result = sql_select($sql);
	
	foreach($result as $row)
	{
		if($inserted_barcode[$row[csf('recv_number')]][$row[csf('barcode_no')]]=='')
		{
		$issue_arr_barcode[$row[csf('recv_number')]]['barcode_no']=$row[csf('barcode_no')];	
		$issue_arr_barcode[$row[csf('recv_number')]]['id']=$row[csf('id')];	
		$issue_arr_barcode[$row[csf('recv_number')]]['recv_number_prefix_num']=$row[csf('recv_number_prefix_num')];	
		$issue_arr_barcode[$row[csf('recv_number')]]['recv_number']=$row[csf('recv_number')];	
		$issue_arr_barcode[$row[csf('recv_number')]]['challan_no']=$row[csf('challan_no')];
		$issue_arr_barcode[$row[csf('recv_number')]]['company_id']=$row[csf('company_id')];	
		$issue_arr_barcode[$row[csf('recv_number')]]['dyeing_source']=$row[csf('dyeing_source')];	
		$issue_arr_barcode[$row[csf('recv_number')]]['dyeing_company']=$row[csf('dyeing_company')];	
		$issue_arr_barcode[$row[csf('recv_number')]]['receive_date']=$row[csf('receive_date')];	
		$issue_arr_barcode[$row[csf('recv_number')]]['year']=$row[csf('year')];	
		}
		 
	}
	
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="830" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="140">Company</th>
            <th width="80">System No</th>
            <th width="110">Batch No</th>
            <th width="70">Year</th>
            <th width="120">Dyeing Source</th>
            <th width="140">Dyeing Company</th>
            <th>Issue date</th>
        </thead>
	</table>
	<div style="width:850px; max-height:240px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="830" class="rpt_table" id="tbl_list_search">  
		<?
        $i=1;
        foreach ($issue_arr_barcode as $recv_no=>$row)
        { 
			//echo count($challan_barcode[$row[csf('recv_number')]])."==".count($inserted_barcode[$row[csf('recv_number')]]);
			//if(count($challan_barcode[$row[csf('recv_number')]]) - count($inserted_barcode[$row[csf('recv_number')]])>0)
			//{ 
				if($i%2==0)
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";	
				
				$knit_comp="&nbsp;";
				if($row[('dyeing_source')]==1)
					$knit_comp=$company_arr[$row[('dyeing_company')]]; 
				else
					$knit_comp=$supllier_arr[$row[('dyeing_company')]];
					$batch_noAll=rtrim($batch_no_idArr[$row[('id')]],',');
					$batch_nos=implode(",",array_unique(explode(",",$batch_noAll)));
					//echo $row[csf('id')].'f';
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)">
                <td width="40"><? echo $i; ?>
                <input type="hidden" name="txt_individual_id[]" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $row[('id')]; ?>"/>
                <input type="hidden" name="txt_individual_no[]" id="txt_individual_no<?php echo $i; ?>" value="<?php echo $row[('recv_number')]; ?>"/>
                </td>
				<td width="140" align="center"><p><? echo $company_arr[$row[('company_id')]]; ?></p></td>
				<td width="80" align="center"><p>&nbsp;<? echo $row[('recv_number_prefix_num')]; ?></p></td>
                <td width="110" align="center"><p>&nbsp;<? echo $batch_nos; ?></p></td>
				<td width="70" align="center"><p><? echo $row[('year')]; ?></p></td>
				<td width="120" align="center"><p><? echo $knitting_source[$row[('dyeing_source')]]; ?>&nbsp;</p></td>
				<td width="140" align="center"><p><? echo $knit_comp; ?>&nbsp;</p></td>
				<td align="center"><? echo change_date_format($row[('receive_date')]); ?></td>
				</tr>
				<?
				$i++;
			//}
        }
        ?>
        </table>
        <br>
         <table width="830">
        <tr>
            <td align="center" >
                <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
            </td>
        </tr>
    </table>
    </div>
	<?	
    exit();
}

if($action=="update_system_popup")
{
	echo load_html_head_contents("Challan Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?> 
	<script>
		function js_set_value(receiveNo, updateId, challanNo, challanId)
		{
			/*
			$('#hidden_receive_no').val(data);
			$('#hidden_update_id').val(id);
			$('#hidden_challan_no').val(challan);
			*/
			$('#hiddenReceiveNo').val(receiveNo);
			$('#hiddenUpdateId').val(updateId);
			$('#hiddenChallanNo').val(challanNo);
			$('#hiddenChallanId').val(challanId);
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center" style="width:760px;" >
        <form name="searchwofrm" id="searchwofrm">
            <fieldset style="width:760px; margin-left:2px">
            <legend>Receive Number Popup</legend>           
                <table cellpadding="0" cellspacing="0" width="750" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                        <th>Company</th>
                        <th>Receive No</th>
                        <th id="" width="250">Receive Date</th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                            <!--
                            <input type="hidden" name="hidden_receive_no" id="hidden_receive_no">  
                            <input type="hidden" name="hidden_update_id" id="hidden_update_id">
                            <input type="hidden" name="hidden_challan_no" id="hidden_challan_no">    
							-->
                            <input type="hidden" name="hiddenReceiveNo" id="hiddenReceiveNo">  
                            <input type="hidden" name="hiddenUpdateId" id="hiddenUpdateId">
                            <input type="hidden" name="hiddenChallanNo" id="hiddenChallanNo">  
                            <input type="hidden" name="hiddenChallanId" id="hiddenChallanId">  
                        </th> 
                    </thead>
                    <tr class="general">
                        <td align="center"><? echo create_drop_down( "cbo_company_id", 170,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name",'id,company_name', 1, '-- Select Company --',0,"",0); ?></td>
                        <td align="center"><input type="text" style="width:140px" class="text_boxes"  name="txt_receive_number" id="txt_receive_number" /></td>
                        <td align="center">	
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:90px" readonly>To
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:90px" readonly>
                        </td>     
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_receive_number').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value, 'create_update_search_list_view', 'search_div', 'aop_roll_receive_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                         </td>
                    </tr>
                    <tr>
                        <td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                    </tr>
               </table>
               <div style="width:100%; margin-top:5px;" id="search_div" align="left"></div>
            </fieldset>
        </form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
}

if($action=="create_update_search_list_view")
{
	$data = explode("_",$data);
	//$search_string="%".trim($data[0]);
	$receive_number=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[0];
	$year_id =$data[4];
	if($company_id==0)
	{
		echo "Please Select Company First.";
		die;
	}
	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and receive_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and receive_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}
	else
	{
		$date_cond="";
	}
	
	$search_field_cond="";

	if($db_type==0) 
	{
		$year_field=" YEAR(a.insert_date) as year"; $year_cond="YEAR(a.insert_date)=$year_id";
		
	}
	
	if($db_type==2) 
	{
		$year_field=" to_char(a.insert_date,'YYYY') as year"; $year_cond=" to_char(a.insert_date,'YYYY')=$year_id ";

	}
	
	if(trim($receive_number)!="")
	{
		$receiv_cond="and a.recv_number_prefix_num=$receive_number and $year_cond ";
	}
	
	$sql="SELECT a.id,a.recv_number_prefix_num,a.recv_number, a.challan_no, a.recv_number,a.company_id, a.dyeing_source,a.dyeing_company,a.receive_date,$year_field
		FROM 
			inv_receive_mas_batchroll a
		WHERE 
			a.entry_form=65 and a.status_active=1 and a.is_deleted=0
			AND a.company_id=$company_id $receiv_cond $date_cond 
		ORDER BY a.recv_number_prefix_num desc, a.receive_date";
	//echo $sql;
	$result = sql_select($sql);
	//print_r($result);
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="140">Company</th>
            <th width="80">Receive No</th>
            <th width="70">Year</th>
            <th width="120">Dyeing Source</th>
            <th width="140">Dyeing Company</th>
            <th>Receive date</th>
        </thead>
	</table>
	<div style="width:740px; max-height:240px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table" id="tbl_list_search">  
        	<?
            $i=1;
            foreach ($result as $row)
            {  
                if ($i%2==0)
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";	
				 
				$knit_comp="&nbsp;";
                if($row[csf('dyeing_source')]==1)
					$knit_comp=$company_arr[$row[csf('dyeing_company')]]; 
				else
					$knit_comp=$supllier_arr[$row[csf('dyeing_company')]];
        	?>
            <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('recv_number')]; ?>','<? echo $row[csf('id')]; ?>','<? echo $row[csf('challan_no')]; ?>', '');"> 
                <td width="40"><? echo $i; ?></td>
                <td width="140"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
                <td width="80"><p>&nbsp;<? echo $row[csf('recv_number_prefix_num')]; ?></p></td>
                <td width="70" align="center"><p><? echo $row[csf('year')]; ?></p></td>
                <td width="120"><p><? echo $knitting_source[$row[csf('dyeing_source')]]; ?>&nbsp;</p></td>
                <td width="140"><p><? echo $knit_comp; ?>&nbsp;</p></td>
                <td align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
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

if($action=="load_php_form_update")
{
	$sql=sql_select("SELECT a.id, a.recv_number_prefix_num, a.recv_number, a.challan_no, a.recv_number, a.company_id,a.batch_no, a.gray_issue_challan_no, a.receive_basis, a.dyeing_source, a.dyeing_company, a.receive_date, a.wo_no, b.barcode_no FROM inv_receive_mas_batchroll a, pro_roll_details b WHERE a.id=b.mst_id AND b.entry_form=65 AND a.id=".$data."");
	//echo $sql;die;
	$query='';
	$barCodeArr=array();
	foreach($sql as $val)
	{
		echo "document.getElementById('txt_wo_no').value  = '".($val[csf("wo_no")])."';\n";
		echo "document.getElementById('txt_delivery_challan').value  = '".($val[csf("gray_issue_challan_no")])."';\n"; 
		echo "document.getElementById('cbo_company_id').value  = '".($val[csf("company_id")])."';\n"; 
		echo "document.getElementById('txt_batch_no').value  = '".($val[csf("batch_no")])."';\n"; 
		echo "document.getElementById('txt_delivery_date').value  = '".change_date_format($val[csf("receive_date")])."';\n"; 
		echo "document.getElementById('cbo_knitting_source').value  = '".($val[csf("dyeing_source")])."';\n"; 
		
		echo "document.getElementById('update_id').value  = '".($val[csf("id")])."';\n";

		$dyeing_source = $val[csf("dyeing_source")];
		$dyeing_company = $val[csf("dyeing_company")];
		$company_id = $val[csf("company_id")];

		$barCodeAr[$val[csf("barcode_no")]]=$val[csf("barcode_no")];
		$query=" AND a.wo_no = '".$val[csf("wo_no")]."' AND a.company_id = ".$val[csf("company_id")]." AND a.dyeing_source = ".$val[csf("dyeing_source")]." AND a.dyeing_company = ".$val[csf("dyeing_company")]."";
		// AND a.recv_number <> '".$val[csf("challan_no")]."'
	}

	echo "load_drop_down('requires/aop_roll_receive_entry_controller', '".$dyeing_source."_".$company_id."', 'load_drop_down_knitting_com', 'knitting_com');\n"; 
	echo "document.getElementById('cbo_knitting_company').value  = '".($dyeing_company)."';\n";  


	unset($sql);
	
	$sql = "SELECT a.id, d.barcode_no, a.wo_entry_form
	  FROM inv_receive_mas_batchroll a, pro_grey_batch_dtls b, wo_booking_mst c, pro_roll_details d
	  WHERE a.id = b.mst_id AND c.booking_no = a.wo_no AND a.id=d.mst_id AND b.id=d.dtls_id AND  a.entry_form = 63 AND d.entry_form = 63 AND a.process_id in(31,35)  AND c.booking_type = 3 AND a.status_active = 1 AND b.status_active = 1 AND c.status_active = 1 ".$query." AND d.mst_id <>".$data." union all
		SELECT a.id, d.barcode_no, a.wo_entry_form
		FROM inv_receive_mas_batchroll a, pro_grey_batch_dtls b, DYEING_WORK_ORDER_MST c, pro_roll_details d 
		WHERE a.id = b.mst_id AND c.do_no = a.wo_no AND a.id=d.mst_id AND b.id=d.dtls_id AND a.entry_form = 63 AND d.entry_form = 63 AND a.status_active = 1 AND b.status_active = 1 AND c.status_active = 1 ".$query." AND d.mst_id <>".$data;

		//AND d.barcode_no NOT IN(".implode(",",$barCodeAr).")
	//echo $sql;
	$resultSet = sql_select($sql);
	$challanIdArr = array();
	foreach($resultSet as $row)
	{
		if($barCodeAr[$row[csf("barcode_no")]]=="")
		{
			$challanIdArr[$row[csf("id")]]= $row[csf("id")];
		}
		$wo_entry_form=$row[csf("wo_entry_form")];
	}
	$challanId = implode(",",$challanIdArr);
	echo "document.getElementById('hidden_challan_id').value  = '".$challanId."';\n";
	echo "document.getElementById('hidden_wo_entry_form').value  = '".$wo_entry_form."';\n";
}



if ($action=="save_update_delete")
{	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	$vari_sql = sql_select("select production_entry from variable_settings_production where company_name = $cbo_company_id and variable_list in (66) and status_active=1");
	$variable_textile_sales_maintain=0;
	foreach ($vari_sql as $val) 
	{
		if($val[csf("production_entry")]==2)
		{
			$variable_textile_sales_maintain=1;
		}
	}

	$hidden_wo_entry_form = str_replace("'","",$hidden_wo_entry_form)*1;

	if ($operation==0) // Insert Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		if($db_type==0) $year_cond="YEAR(insert_date)"; 
		else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
		else $year_cond="";//defined Later
		//$new_mrr_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'AOP', date("Y",time()), 5, "select recv_number_prefix, recv_number_prefix_num from  inv_receive_mas_batchroll where company_id=$cbo_company_id and entry_form=65 and $year_cond=".date('Y',time())." order by id desc ", "recv_number_prefix","recv_number_prefix_num"));
		
		//$id=return_next_id( "id", "inv_receive_mas_batchroll", 1 ) ;
		
		$id = return_next_id_by_sequence("INV_RCV_MAS_BATC_PK_SEQ", "inv_receive_mas_batchroll", $con);
        //print_r($id); die;
		$new_mrr_number = explode("*", return_next_id_by_sequence("INV_RCV_MAS_BATC_PK_SEQ", "inv_receive_mas_batchroll",$con,1,$cbo_company_id,'AOP',65,date("Y",time()),13 ));

		//txt_wo_no
		$txt_challan_no = "'".implode(",",array_unique(explode(",",str_replace("'","",$txt_challan_no))))."'";
		$field_array="id,recv_number_prefix,recv_number_prefix_num 	,recv_number,entry_form,receive_date,company_id,dyeing_source,dyeing_company,challan_no,batch_no,gray_issue_challan_no,wo_no,wo_id,inserted_by,insert_date";
		$data_array="(".$id.",'".$new_mrr_number[1]."',".$new_mrr_number[2].",'".$new_mrr_number[0]."',65,".$txt_delivery_date.",".$cbo_company_id.",".$cbo_knitting_source.",".$cbo_knitting_company.",".$txt_challan_no.",'".str_replace($txt_batch_no)."','".str_replace("'","",$txt_delivery_challan)."','".str_replace("'","",$txt_wo_no)."',".$txt_wo_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		$field_array_dtls="id, mst_id,roll_id,knitting_source,knitting_company,booking_no,receive_basis,prod_id,body_part_id,febric_description_id, gsm, width, roll_wgt, buyer_id, job_no, order_id,color_id,rate, amount, currency_id,challan_no,batch_id, exchange_rate,process_id,inserted_by, insert_date, is_sales";
		$field_array_roll="id, mst_id, dtls_id, po_breakdown_id, entry_form,roll_id,qnty, roll_no, barcode_no, issue_roll_id, booking_without_order, re_issued, inserted_by,insert_date, is_sales";

		$field_array_batch="id, batch_no, entry_form, batch_date, company_id, color_id, batch_weight, booking_no, booking_no_id, sales_order_no, sales_order_id, is_sales, working_company_id, inserted_by, insert_date";
		$field_array_batch_dtls="id, mst_id, po_id, prod_id, body_part_id, item_description, roll_no, roll_id, barcode_no, batch_qnty, dtls_id, is_sales, inserted_by, insert_date";
		//$dtls_id = return_next_id( "id", "pro_grey_batch_dtls", 1 );
		//$id_roll = return_next_id( "id", "pro_roll_details", 1 );
		$determination_ids = array();
		for($j=1;$j<=$tot_row;$j++)
		{ 	
		   	$activeId="activeId_".$j;
		   	if($$activeId==1)
		   	{
				$colorId="colorId_".$j;
				$bookingNo="bookingNo_".$j;
				$txtBatchNo="txtBatchNo_".$j;
				$barcodeNo="barcodeNo_".$j;
				$hdnProcessNo="hdnProcessNo_".$j;
				$batchId="batchId_".$j;
				$rollwgt="rollwgt_".$j;
				$deterId="deterId_".$j;
				$issueRollId="issueRollId_".$j;
				$orderNo="orderNo_".$j;
				$orderId="orderId_".$j;
				$isSalesId="isSalesId_".$j;
				$salesBookingID="salesBookingID_".$j;
				$salesBookingNO="salesBookingNO_".$j;

				$all_barcodes .= $$barcodeNo.",";

				$allIssueRollIds .=$$issueRollId.",";
				
				//in FSO basis WO, batch id not needed for heat setting (33), Back Sewing(100), Heat Setting + Back Sewing (476) processes

				//if(str_replace("'", "", $$hdnProcessNo) == 31 && str_replace("'", "", $$batchId) =="")
				//if(str_replace("'", "", $cbo_knitting_source) == 3 && (str_replace("'", "", $$hdnProcessNo) == 31 && str_replace("'", "", $$batchId) =="") || ($variable_textile_sales_maintain==1 && $$isSalesId==1 && str_replace("'", "", $$hdnProcessNo) != 33))  

				if(str_replace("'", "", $cbo_knitting_source) == 3 && str_replace("'", "", $$hdnProcessNo) == 31 && str_replace("'", "", $$batchId) =="")
				{
					$determination_ids[$$deterId] = $$deterId;

					if($hidden_wo_entry_form==418 || $hidden_wo_entry_form==696)
					{
						$batch_entry_grouping_data_arr[$$orderNo][$$colorId][$$txtBatchNo]['qnty'] += str_replace("'", "", $$rollwgt)*1;
						$batch_entry_grouping_data_arr[$$orderNo][$$colorId][$$txtBatchNo]['is_sales'] = str_replace("'", "", $$isSalesId)*1;
						$batch_entry_grouping_data_arr[$$orderNo][$$colorId][$$txtBatchNo]['sales_booking_id'] = $$salesBookingID;
						$batch_entry_grouping_data_arr[$$orderNo][$$colorId][$$txtBatchNo]['sales_booking_no'] = $$salesBookingNO;
						$batch_entry_grouping_data_arr[$$orderNo][$$colorId][$$txtBatchNo]['sales_fso_id'] = $$orderId;
					}
					else
					{
						$batch_entry_grouping_data_arr[$txt_wo_no][$$colorId][$$txtBatchNo]['qnty'] += str_replace("'", "", $$rollwgt)*1;
						$batch_entry_grouping_data_arr[$txt_wo_no][$$colorId][$$txtBatchNo]['is_sales'] = str_replace("'", "", $$isSalesId)*1;
						$batch_entry_grouping_data_arr[$txt_wo_no][$$colorId][$$txtBatchNo]['sales_booking_id'] = "";
						$batch_entry_grouping_data_arr[$txt_wo_no][$$colorId][$$txtBatchNo]['sales_booking_no'] = "";
						$batch_entry_grouping_data_arr[$txt_wo_no][$$colorId][$$txtBatchNo]['sales_fso_id'] = "";
					}
				}
		   	}
		}

		if(!empty($determination_ids))
		{
			$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id in (".implode(',', $determination_ids).") order by b.id asc";
			$deter_array=sql_select($sql_deter);
			if(count($deter_array)>0)
			{
				foreach( $deter_array as $row )
				{
					if(array_key_exists($row[csf('id')],$composition_arr))
					{
						$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
					}
					else
					{
						$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
					}
				}
			}
		}

		$all_barcodes = chop($all_barcodes,',');
		$allIssueRollIds = chop($allIssueRollIds,',');


		$aop_receive = sql_select("SELECT b.barcode_no, b.is_rcv_done, b.id as issue_roll_id, d.recv_number from inv_receive_mas_batchroll a, pro_roll_details b, pro_roll_details c, inv_receive_mas_batchroll d where a.id=b.mst_id and a.entry_form =63 and b.entry_form =63 and b.is_rcv_done=1 and b.status_active=1 and b.is_deleted=0 and b.id=c.issue_roll_id and c.mst_id=d.id and c.entry_form=65 and d.entry_form=65 and b.barcode_no in (".$all_barcodes.")");
		foreach ($aop_receive as $row) 
		{
			$already_received_chk_arr[$row[csf("barcode_no")]][$row[csf("issue_roll_id")]]=$row[csf("recv_number")];
		}

		/*echo "10**";
		echo $sql_deter;
		print_r($determination_ids);
		die;*/

		if(!empty($batch_entry_grouping_data_arr))
		{
			foreach ($batch_entry_grouping_data_arr as $batchbooking => $batchBookingData) 
			{
				foreach ($batchBookingData as $batchColor => $batchColorData) 
				{
					foreach ($batchColorData as $BatchNumber => $row) 
					{
						if($hidden_wo_entry_form==418 || $hidden_wo_entry_form==696)
						{
							$booking_sales_cond = " and a.sales_order_no='$batchbooking'";

							$batch_booking_number = $row['sales_booking_no'];
							$batch_booking_id = $row['sales_booking_id'];
							$batch_fso_id = $row['sales_fso_id'];
							$batch_fso_number = $batchbooking;
							$is_sales = $row['is_sales'];
						}
						else
						{
							$booking_sales_cond = " and a.booking_no='$batchbooking'";
							$batch_booking_number = $batchbooking;
							$batch_booking_id = $txt_wo_id;
							$batch_fso_id = "";
							$batch_fso_number = "";
							$is_sales = $row['is_sales'];
						}

						$batchData=sql_select("select a.id, a.batch_weight from pro_batch_create_mst a left join pro_batch_create_dtls b on a.id=b.mst_id where a.batch_no='$BatchNumber' and a.color_id='$batchColor' and a.working_company_id=$cbo_knitting_company $booking_sales_cond and a.status_active=1 and a.is_deleted=0 and a.entry_form=65 group by a.id, a.batch_weight");

						if(count($batchData)>0)
						{
							$batch_id=$batchData[0][csf('id')];
							$curr_batch_weight=$batchData[0][csf('batch_weight')]+$row['qnty'];
							$field_array_batch_update="batch_weight*updated_by*update_date";

							$update_batch_id[]=$batch_id;

							$data_array_batch_update[$batch_id]=explode("*",("'".$curr_batch_weight."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
						}
						else
						{
				
							$batch_id = return_next_id_by_sequence("PRO_BATCH_CREATE_MST_PK_SEQ", "pro_batch_create_mst", $con);
							
							if($data_array_batch!="") $data_array_batch.=",";
							$data_array_batch.="(".$batch_id.",'".$BatchNumber."',65,".$txt_delivery_date.",".$cbo_company_id.",'".$batchColor."',".$row['qnty'].",'".$batch_booking_number."','".$batch_booking_id."','".$batch_fso_number."','".$batch_fso_id."','".$is_sales."',".$cbo_knitting_company.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
						}

						if($hidden_wo_entry_form==418 || $hidden_wo_entry_form==696)
						{
							$batch_no_arr[$batch_fso_number][$batchColor][$BatchNumber]['id'] = $batch_id;
						}
						else
						{
							$batch_no_arr[$batch_booking_number][$batchColor][$BatchNumber]['id'] = $batch_id;
						}
					}
				}
			}
		}
		
		
		//echo "10**insert into pro_batch_create_mst ($field_array_batch) values".$data_array_batch;oci_rollback($con); disconnect($con); die;
		//echo "10**";
		//print_r($batch_no_arr);
		//die;


		$update_detals_all="";
		
		for($j=1;$j<=$tot_row;$j++)
		{ 	
		   $activeId="activeId_".$j;
		   if($$activeId==1)
		   {
			$rollId="rollId_".$j;
			$buyerId="buyerId_".$j;
			$bodyPart="bodyPart_".$j;
			$colorId="colorId_".$j;
			$deterId="deterId_".$j;
			$productId="productId_".$j;
			$orderId="orderId_".$j;
			$orderNo="orderNo_".$j;
			$rollGsm="rollGsm_".$j;
			$knittingSource="knittingSource_".$j;
			$knittingComp="knittingComp_".$j;
			$fabricId="fabricId_".$j;
			$receiveBasis="receiveBasis_".$j;
			$job_no="job_no_".$j;
			$rollwgt="rollwgt_".$j;
			$rolldia="rolldia_".$j;
			$bookingNo="bookingNo_".$j;
			$barcodeNo="barcodeNo_".$j;
			$rollNo="rollNo_".$j;
			$challanNo="challanNo_".$j;
			$batchNo="batchNo_".$j;
			$batchId="batchId_".$j;
			$rate="rate_".$j;
			$amount="amount_".$j;
			$currency="currency_".$j;

			$hdnProcessNo="hdnProcessNo_".$j;
			$txtBatchNo="txtBatchNo_".$j;
			$issueRollId="issueRollId_".$j;
			$bookingWithoutOrder="bookingWithoutOrder_".$j;
			$isSalesId="isSalesId_".$j;

			$exchangeRate="exchangeRate_".$j;
			$exchange_rate=$$exchangeRate;
			if($$rate!="" && $$currency>0 && $exchange_rate=="")
			{
				$exchange_date=str_replace("'","",$txt_delivery_date);
				$exchange_rate=set_conversion_rate( $$currency, $exchange_date );
			}

			if($already_received_chk_arr[str_replace("'", "", $$barcodeNo)][str_replace("'", "", $$issueRollId)] !="")
			{
				echo "20**Aop Receive found.\nBarcode no:".str_replace("'", "", $$barcodeNo)."\nReceive no: ".$already_received_chk_arr[str_replace("'", "", $$barcodeNo)][str_replace("'", "", $$issueRollId)];
				oci_rollback($con);
				disconnect($con);
				die;
			}

			
			
			$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
			$dtls_id = return_next_id_by_sequence("PRO_GREY_BATCH_DTLS_PK_SEQ", "pro_grey_batch_dtls", $con);
			
			if($update_detals_all!="") $update_detals_all.="##";
			$update_detals_all.="".$j."#".$dtls_id."";
			if($data_array_roll!="") $data_array_roll.= ",";
			$data_array_roll.="(".$id_roll.",".$id.",".$dtls_id.",'".$$orderId."',65,'".$$rollId."','".$$rollwgt."','".$$rollNo."','".$$barcodeNo."','".$$issueRollId."','".$$bookingWithoutOrder."','0',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$$isSalesId."')";

			$batch_no_id=0;
			//if(str_replace("'", "", $$hdnProcessNo) == 31 && str_replace("'", "", $$batchId) =="")
			/*echo "20**".str_replace("'", "", $cbo_knitting_source).'='.str_replace("'", "", $$batchId).'='.$variable_textile_sales_maintain.'='.$$isSalesId.'='.str_replace("'", "", $$hdnProcessNo);
			oci_rollback($con);
			disconnect($con);
			die;*/

			//N. B. in FSO basis WO, batch id not needed for heat setting (33) process

			//if( (str_replace("'", "", $cbo_knitting_source) == 3 && str_replace("'", "", $$batchId) =="") && ( str_replace("'", "", $$hdnProcessNo) == 31  || ($variable_textile_sales_maintain==1 && str_replace("'", "",$$isSalesId)==1) && str_replace("'", "", $$hdnProcessNo) != 33) )
			
			if( str_replace("'", "", $cbo_knitting_source) == 3 && str_replace("'", "", $$batchId) =="" && str_replace("'", "", $$hdnProcessNo) == 31 )
			{
				if($hidden_wo_entry_form==418 || $hidden_wo_entry_form==696)
				{
					$batch_no_id = $batch_no_arr[$$orderNo][$$colorId][$$txtBatchNo]['id'];
				}
				else
				{
					$batch_no_id = $batch_no_arr[$txt_wo_no][$$colorId][$$txtBatchNo]['id'];
				}
			}
			else if (str_replace("'", "", $$batchId) !="")
			{
				$batch_no_id =$$batchId; $msg=' id not';
			}

			if($batch_no_id==0 && str_replace("'", "", $$hdnProcessNo) != 33 && str_replace("'", "", $$hdnProcessNo) != 100 && str_replace("'", "", $$hdnProcessNo) != 476){
				echo "20**Batch No. not found";
				oci_rollback($con);
				disconnect($con);
				die;
			}
			
			if($data_array_dtls!="") $data_array_dtls.=",";
			$data_array_dtls.="(".$dtls_id.",".$id.",'".$$rollId."','".$$knittingSource."','".$$knittingComp."','".$$bookingNo."','".$$receiveBasis."','".$$productId."','".$$bodyPart."','".$$deterId."','".$$rollGsm."','".$$rolldia."','".$$rollwgt."','".$$buyerId."','".$$job_no."','".$$orderId."','".$$colorId."','".$$rate."','".$$amount."','".$$currency."','".$$challanNo."','".$batch_no_id."','".$exchange_rate."','".$$hdnProcessNo."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$$isSalesId."')";


				//if( (str_replace("'", "", $cbo_knitting_source) == 3 && str_replace("'", "", $$batchId) =="") && ( (str_replace("'", "", $$hdnProcessNo) == 31 ) || ($variable_textile_sales_maintain==1 && $$isSalesId==1 && str_replace("'", "", $$hdnProcessNo) != 33) ) )
				if(str_replace("'", "", $cbo_knitting_source) == 3 && str_replace("'", "", $$hdnProcessNo) == 31 && str_replace("'", "", $$batchId) =="")
				{
					$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
					$ItemDesc=$composition_arr[str_replace("'","",$$deterId)].", ".str_replace("'","",$$rollGsm).", ".str_replace("'","",$$rolldia);
					if($data_array_batch_dtls!="" ) $data_array_batch_dtls.=",";
					$data_array_batch_dtls.="(".$id_dtls_batch.",'".$batch_no_id."','".$$orderId."','".$$productId."','".$$bodyPart."','".$ItemDesc."','".$$rollNo."','".$id_roll."','".$$barcodeNo."',".$$rollwgt.",".$dtls_id.",'".$$isSalesId."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				}
		   }
		}
		//echo "10**";
		//echo $data_array_batch_dtls;die;

		// echo "10**INSERT INTO pro_roll_details(".$field_array_roll.") VALUES".$data_array_roll;oci_rollback($con); die;
		$rID=sql_insert("inv_receive_mas_batchroll",$field_array,$data_array,0);
		$rID2=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
		$rID3=sql_insert("pro_grey_batch_dtls",$field_array_dtls,$data_array_dtls,1);

		$rID4=$rID5=$rID6=$rID7=1;

		if(!empty($data_array_batch_update))
		{
					
			$rID4=execute_query(bulk_update_sql_statement("pro_batch_create_mst","id",$field_array_batch_update,$data_array_batch_update,$update_batch_id),1);		
		}

		if($data_array_batch != "")
		{
			//echo "10**INSERT INTO pro_batch_create_mst(".$field_array_batch.") VALUES".$data_array_batch;oci_rollback($con); die;
			$rID5=sql_insert("pro_batch_create_mst",$field_array_batch,$data_array_batch,0);
		}

		if($data_array_batch_dtls !="")
		{
			$rID6=sql_insert("pro_batch_create_dtls",$field_array_batch_dtls,$data_array_batch_dtls,1);
		}


		$rID7=sql_multirow_update("pro_roll_details","is_rcv_done","1","id",$allIssueRollIds,0);


		//echo "10** $rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 && $rID7";oci_rollback($con);disconnect($con);die;
		//echo "10**insert into pro_grey_batch_dtls ($field_array_dtls) values ".$data_array_dtls;oci_rollback($con);disconnect($con);die;
		

		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 && $rID7)
			{
				mysql_query("COMMIT");  
				echo "0**".$id."**".$new_mrr_number[0]."**".$update_detals_all;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 && $rID7)
			{
				oci_commit($con);  
				echo "0**".$id."**".$new_mrr_number[0]."**".$update_detals_all;
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
	else if ($operation==1) // Update Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$update_roll_sql=sql_select("select id,mst_id,dtls_id, issue_roll_id from pro_roll_details where entry_form=65 and status_active=1 and is_deleted=0 and mst_id=$update_id");
		$update_roll_arr=array();
		foreach($update_roll_sql as $r_val)
		{
			$update_roll_arr[$r_val[csf('mst_id')]][$r_val[csf('dtls_id')]]=$r_val[csf('id')];	
			$issue_roll_id_arr[$r_val[csf('dtls_id')]]=$r_val[csf('issue_roll_id')];	
		}
		
		$field_array="challan_no*receive_date*batch_no*gray_issue_challan_no*updated_by*update_date";
		$data_array="".$txt_challan_no."*".$txt_delivery_date."*'".str_replace($txt_batch_no)."'*'".str_replace("'","",$txt_delivery_challan)."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		//$dtls_id = return_next_id( "id", "pro_grey_batch_dtls", 1 );
		$field_array_dtls="updated_by*update_date*status_active*is_deleted";
		$field_array_roll="id, mst_id, dtls_id, po_breakdown_id, entry_form,roll_id, roll_no, qnty, barcode_no, issue_roll_id, is_sales, inserted_by, insert_date";
		$field_array_insert="id,mst_id,roll_id,challan_no,batch_id,knitting_source,knitting_company,booking_no,receive_basis,prod_id,body_part_id, febric_description_id,gsm,width,roll_wgt,buyer_id,job_no, order_id,color_id,rate, amount, currency_id, exchange_rate,process_id,is_sales, inserted_by, insert_date";
		$field_update_roll="qnty*updated_by* update_date";
		$field_update_dtls="roll_wgt*rate*amount*currency_id*exchange_rate*batch_id*color_id*updated_by*update_date";
		
		$field_romove_roll="updated_by*update_date*status_active*is_deleted";
		$field_remove_dtls="updated_by*update_date*status_active*is_deleted";

		$field_array_batch="id, batch_no, entry_form, batch_date, company_id, color_id, batch_weight, booking_no, booking_no_id, sales_order_no, sales_order_id, is_sales, working_company_id, inserted_by, insert_date";
		$field_array_batch_dtls="id, mst_id, po_id, prod_id, body_part_id, item_description, roll_no, roll_id, barcode_no, batch_qnty, dtls_id, is_sales, inserted_by, insert_date";

		$field_array_batch_update="batch_weight*updated_by*update_date";
		
		
		// Batch id generating here starts --------------------


		$determination_ids = array();
		for($j=1;$j<=$tot_row;$j++)
		{ 	
		   	$activeId="activeId_".$j;
		   	$barcodeNo="barcodeNo_".$j;
			$updateDetailsIds="updateDetailsId_".$j;
		   	$all_barcodes .= $$barcodeNo.",";
		   	if($$activeId==1)
		   	{
				$colorId="colorId_".$j;
				$bookingNo="bookingNo_".$j;
				$txtBatchNo="txtBatchNo_".$j;
				
				$hdnProcessNo="hdnProcessNo_".$j;
				$batchId="batchId_".$j;
				$rollwgt="rollwgt_".$j;
				$deterId="deterId_".$j;
				$orderNo="orderNo_".$j;
				$orderId="orderId_".$j;
				$isSalesId="isSalesId_".$j;
				$salesBookingID="salesBookingID_".$j;
				$salesBookingNO="salesBookingNO_".$j;
				

				//if(str_replace("'", "", $cbo_knitting_source) == 3 && (str_replace("'", "", $$hdnProcessNo) == 31 || ($variable_textile_sales_maintain==1 && $$isSalesId ==1 && str_replace("'", "", $$hdnProcessNo) != 33) )    )

				if(str_replace("'", "", $cbo_knitting_source) == 3 && str_replace("'", "", $$hdnProcessNo) == 31 )
				{
					//$batch_entry_grouping_data_arr[$txt_wo_no][$$colorId][$$txtBatchNo] += str_replace("'", "", $$rollwgt)*1;
					if($hidden_wo_entry_form==418 || $hidden_wo_entry_form==696)
					{
						$batch_entry_grouping_data_arr[$$orderNo][$$colorId][$$txtBatchNo]['qnty'] += str_replace("'", "", $$rollwgt)*1;
						$batch_entry_grouping_data_arr[$$orderNo][$$colorId][$$txtBatchNo]['is_sales'] = str_replace("'", "", $$isSalesId)*1;
						$batch_entry_grouping_data_arr[$$orderNo][$$colorId][$$txtBatchNo]['sales_booking_id'] = $$salesBookingID;
						$batch_entry_grouping_data_arr[$$orderNo][$$colorId][$$txtBatchNo]['sales_booking_no'] = $$salesBookingNO;
						$batch_entry_grouping_data_arr[$$orderNo][$$colorId][$$txtBatchNo]['sales_fso_id'] = $$orderId;
					}
					else
					{
						$batch_entry_grouping_data_arr[$txt_wo_no][$$colorId][$$txtBatchNo]['qnty'] += str_replace("'", "", $$rollwgt)*1;
						$batch_entry_grouping_data_arr[$txt_wo_no][$$colorId][$$txtBatchNo]['is_sales'] = str_replace("'", "", $$isSalesId)*1;
						$batch_entry_grouping_data_arr[$txt_wo_no][$$colorId][$$txtBatchNo]['sales_booking_id'] = "";
						$batch_entry_grouping_data_arr[$txt_wo_no][$$colorId][$$txtBatchNo]['sales_booking_no'] = "";
						$batch_entry_grouping_data_arr[$txt_wo_no][$$colorId][$$txtBatchNo]['sales_fso_id'] = "";
					}
				}

				$determination_ids[$$deterId] = $$deterId;

				if(str_replace("'", "", $$batchId) !="")
				{
					$all_checked_batch_id[str_replace("'", "", $$batchId)] = str_replace("'", "", $$batchId);
				}

				if($$updateDetailsIds==0)
				{

					$newBarcode_Nos="barcodeNo_".$k;
					$all_newBarcode_Nos .= $$newBarcode_Nos.",";
				}
		   	}
		}
		
		for($k=1;$k<=$tot_row;$k++)
		{ 
		    $activeIdChk="activeId_".$k;
			$updateDetailsIds="updateDetailsId_".$k;
			$hdnProductionEntryFrom="hdnProductionEntryFrom_".$k;
			$isSalesId="isSalesId_".$k;
			$hdnProcessNo="hdnProcessNo_".$k;
			if($$updateDetailsIds!=0)
			{
				if($$hdnProductionEntryFrom=="")
				{
					$barcode_Noss="barcodeNo_".$k;
					$NotProductionBarcodes .= $$barcode_Noss.",";
				}

			}
			if($$activeIdChk==0 )
			{
				if($$updateDetailsIds!=0)
				{
					$barcode_Nos="barcodeNo_".$k;
					$removeBarcodes .= $$barcode_Nos.",";
				}
			}
		}
		//echo "20**$remove_barcodes"; die; 
		//if(str_replace("'", "", $cbo_knitting_source) == 3 && (str_replace("'", "", $$hdnProcessNo) == 31 || ($variable_textile_sales_maintain==1 && $$isSalesId ==1 && str_replace("'", "", $$hdnProcessNo) != 33) ) )

		if(str_replace("'", "", $cbo_knitting_source) == 3 &&str_replace("'", "", $$hdnProcessNo) == 31 )
		{
			if($removeBarcodes!="")
			{
				$production_sql_checkByCheckbox = sql_select("SELECT a.barcode_no, b.recv_number from pro_roll_details a, inv_receive_master b where a.mst_id =b.id and a.entry_form = 66 and b.entry_form=66 and a.status_active=1 and b.status_active=1 and a.barcode_no in (". chop($removeBarcodes,',').")");
				if(!empty($production_sql_checkByCheckbox))
				{

					$production_sql = sql_select("SELECT a.barcode_no, b.recv_number from pro_roll_details a, inv_receive_master b where a.mst_id =b.id and a.entry_form = 66 and b.entry_form=66 and a.status_active=1 and b.status_active=1 and a.barcode_no in (". chop($all_barcodes,',').")");

					if(!empty($production_sql))
					{
						echo "20**Finish fabric roll wise production found.\nProduction No: ".$production_sql[0][csf("recv_number")]."\nBarcode no: ".$production_sql[0][csf("barcode_no")];
						oci_rollback($con);
						disconnect($con);
						die;
					}
				}
			}
			else
			{
				if($NotProductionBarcodes=="")
				{
					$production_sql = sql_select("SELECT a.barcode_no, b.recv_number from pro_roll_details a, inv_receive_master b where a.mst_id =b.id and a.entry_form = 66 and b.entry_form=66 and a.status_active=1 and b.status_active=1 and a.barcode_no in (". chop($all_barcodes,',').")");

					if(!empty($production_sql))
					{
						echo "20**Finish fabric roll wise production found.\nProduction No: ".$production_sql[0][csf("recv_number")]."\nBarcode no: ".$production_sql[0][csf("barcode_no")];
						oci_rollback($con);
						disconnect($con);
						die;
					}
				}
			}
		}
		else
		{
			$production_sql = sql_select("SELECT a.barcode_no, b.recv_number from pro_roll_details a, inv_receive_master b where a.mst_id =b.id and a.entry_form = 66 and b.entry_form=66 and a.status_active=1 and b.status_active=1 and a.barcode_no in (". chop($all_barcodes,',').")");

			if(!empty($production_sql))
			{
				echo "20**Finish fabric roll wise production found.\nProduction No: ".$production_sql[0][csf("recv_number")]."\nBarcode no: ".$production_sql[0][csf("barcode_no")];
				oci_rollback($con);
				disconnect($con);
				die;
			}
		}
		

		$aop_receive = sql_select("SELECT b.barcode_no, b.is_rcv_done, b.id as issue_roll_id, d.recv_number from inv_receive_mas_batchroll a, pro_roll_details b, pro_roll_details c, inv_receive_mas_batchroll d where a.id=b.mst_id and a.entry_form =63 and b.entry_form =63 and b.is_rcv_done=1 and b.status_active=1 and b.is_deleted=0 and b.id=c.issue_roll_id and c.mst_id=d.id and c.entry_form=65 and d.entry_form=65 and b.barcode_no in (".$all_newBarcode_Nos.")");
		foreach ($aop_receive as $row) 
		{
			$already_received_chk_arr[$row[csf("barcode_no")]][$row[csf("issue_roll_id")]]=$row[csf("recv_number")];
		}

		
	 //echo "10**Yes"; die;

		if(!empty($determination_ids))
		{
			$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id in (".implode(',', $determination_ids).") order by b.id asc";
			$deter_array=sql_select($sql_deter);
			if(count($deter_array)>0)
			{
				foreach( $deter_array as $row )
				{
					if(array_key_exists($row[csf('id')],$composition_arr))
					{
						$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
					}
					else
					{
						$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
					}
				}
			}
		}

		if(!empty($all_checked_batch_id))
		{
			$preBatchSql=sql_select("SELECT a.id, a.batch_no,a.color_id, sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a left join pro_batch_create_dtls b on a.id=b.mst_id where a.id in (".implode(",",$all_checked_batch_id).") and a.status_active=1 and a.is_deleted=0 and a.entry_form=65 group by a.id, a.batch_no,a.color_id");

			foreach ($preBatchData as $val) 
			{
				$preBatchData[$val[csf('id')]][$val[csf('color_id')]]['batch_no'] = $val[csf('batch_no')];
				$preBatchData[$val[csf('id')]][$val[csf('color_id')]]['batch_qnty'] = $val[csf('batch_qnty')];
			}
		}

		if(!empty($batch_entry_grouping_data_arr))
		{
			foreach ($batch_entry_grouping_data_arr as $batchbooking => $batchBookingData) 
			{
				foreach ($batchBookingData as $batchColor => $batchColorData) 
				{
					foreach ($batchColorData as $BatchNumber => $row) 
					{
						if($hidden_wo_entry_form==418 || $hidden_wo_entry_form==696)
						{
							$booking_sales_cond = " and a.sales_order_no='$batchbooking'";

							$batch_booking_number = $row['sales_booking_no'];
							$batch_booking_id = $row['sales_booking_id'];
							$batch_fso_id = $row['sales_fso_id'];
							$batch_fso_number = $batchbooking;
							$is_sales = $row['is_sales'];
						}
						else
						{
							$booking_sales_cond = " and a.booking_no='$batchbooking'";
							$batch_booking_number = $batchbooking;
							$batch_booking_id = $txt_wo_id;
							$batch_fso_id = "";
							$batch_fso_number = "";
							$is_sales = $row['is_sales'];
						}

						$batchData=sql_select("select a.id, a.batch_weight from pro_batch_create_mst a left join pro_batch_create_dtls b on a.id=b.mst_id where a.batch_no='$BatchNumber' and a.color_id='$batchColor' and a.working_company_id=$cbo_knitting_company $booking_sales_cond and a.status_active=1 and a.is_deleted=0 and a.entry_form=65 group by a.id, a.batch_weight");

						if(count($batchData)>0)
						{
							$batch_id=$batchData[0][csf('id')];
							$curr_batch_weight=$batchData[0][csf('batch_weight')]+$row['qnty'];
							$update_batch_id[]=$batch_id;

							$data_array_batch_update[$batch_id]=explode("*",("'".$curr_batch_weight."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
						}
						else
						{
				
							$batch_id = return_next_id_by_sequence("PRO_BATCH_CREATE_MST_PK_SEQ", "pro_batch_create_mst", $con);
							
							if($data_array_batch!="") $data_array_batch.=",";
							//$data_array_batch.="(".$batch_id.",'".$BatchNumber."',65,".$txt_delivery_date.",".$cbo_company_id.",'".$batchColor."',".$row.",".$batchbooking.",".$txt_wo_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

							$data_array_batch.="(".$batch_id.",'".$BatchNumber."',65,".$txt_delivery_date.",".$cbo_company_id.",'".$batchColor."',".$row['qnty'].",'".$batch_booking_number."','".$batch_booking_id."','".$batch_fso_number."','".$batch_fso_id."','".$is_sales."',".$cbo_knitting_company.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
						}

						if($hidden_wo_entry_form==418 || $hidden_wo_entry_form==696)
						{
							$batch_no_arr[$batch_fso_number][$batchColor][$BatchNumber]['id'] = $batch_id;
						}
						else
						{
							$batch_no_arr[$batch_booking_number][$batchColor][$BatchNumber]['id'] = $batch_id;
						}
					}
				}
			}
		}
		//echo "20**hi4";die;
		// Batch id generating here ends --------------------

		$barcodeNos='';
		$update_array_dtls=array();
		$update_detals_all="";
		for($j=1;$j<=$tot_row;$j++)
		{ 
		    $activeId="activeId_".$j;
			$updateDetailsId="updateDetailsId_".$j;
			if($$activeId==0 )
			{
				if($$updateDetailsId!=0)
				{
					$barcodeNo="barcodeNo_".$j;
					$remove_barcodes .= $$barcodeNo.",";
					if($update_detals_all!="")
						$update_detals_all.="##";
					$update_detals_all.="".$j."#0";	
					$roll_id_update=$update_roll_arr[str_replace("'","",$update_id)][str_replace("'","",$$updateDetailsId)];	
					$remove_dtls_id[]=str_replace("'","",$$updateDetailsId);
					$remove_roll_id[]=str_replace("'","",$roll_id_update);
					$remove_array_roll[str_replace("'","",$roll_id_update)]=explode("*",("".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1"));
					$remove_array_dtls[str_replace("'","",$$updateDetailsId)]=explode("*",("".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1"));

					$update_issue_roll_ids_arr[$issue_roll_id_arr[str_replace("'","",$$updateDetailsId)]] = $issue_roll_id_arr[str_replace("'","",$$updateDetailsId)];

			   }
			}
			
		   	if($$activeId==1)
			{
				$rollId="rollId_".$j;
				$buyerId="buyerId_".$j;
				$bodyPart="bodyPart_".$j;
				$colorId="colorId_".$j;
				$deterId="deterId_".$j;
				$productId="productId_".$j;
				$orderId="orderId_".$j;
				$orderNo="orderNo_".$j;
				$rollGsm="rollGsm_".$j;
				$knittingSource="knittingSource_".$j;
				$knittingComp="knittingComp_".$j;
				$fabricId="fabricId_".$j;
				$receiveBasis="receiveBasis_".$j;
				$job_no="job_no_".$j;
				$rollwgt="rollwgt_".$j;
				$rolldia="rolldia_".$j;
				$bookingNo="bookingNo_".$j;
				$barcodeNo="barcodeNo_".$j;
				$rollNo="rollNo_".$j;
				$challanNo="challanNo_".$j;
				$batchNo="batchNo_".$j;
				$batchId="batchId_".$j;
				$rate="rate_".$j;
				$amount="amount_".$j;
				$currency="currency_".$j;
				$isSalesId="isSalesId_".$j;
				$issueRollId="issueRollId_".$j;

				$hdnProcessNo="hdnProcessNo_".$j;
				$txtBatchNo="txtBatchNo_".$j;

				$exchangeRate="exchangeRate_".$j;
				$exchange_rate=$$exchangeRate;
				if($$rate!="" && $$currency>0 && $exchange_rate=="")
				{
					$exchange_date=str_replace("'","",$txt_delivery_date);
					$exchange_rate=set_conversion_rate( $$currency, $exchange_date );
				}	
				
				if($$updateDetailsId==0)
				{
					$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
					$dtls_id = return_next_id_by_sequence("PRO_GREY_BATCH_DTLS_PK_SEQ", "pro_grey_batch_dtls", $con);
					
					if($update_detals_all!="") $update_detals_all.="##";
					$update_detals_all.="".$j."#".$dtls_id."";
					if($data_array_roll!="") $data_array_roll.= ",";
					$data_array_roll.="(".$id_roll.",".$update_id.",".$dtls_id.",'".$$orderId."',65,'".$$rollId."','".$$rollNo."','".$$rollwgt."','".$$barcodeNo."','".$$issueRollId."','".$$isSalesId."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

					$batch_no_id=0;
					//if( str_replace("'", "", $$batchId) =="" && (str_replace("'", "", $$hdnProcessNo) == 31 || ($variable_textile_sales_maintain==1 && $$isSalesId==1 && str_replace("'", "", $$hdnProcessNo) != 33) ) )
					if( str_replace("'", "", $$batchId) =="" && str_replace("'", "", $$hdnProcessNo) == 31 )
					{
						//$batch_no_id = $batch_no_arr[$txt_wo_no][$$colorId][$$txtBatchNo]['id'];

						if($hidden_wo_entry_form==418 || $hidden_wo_entry_form==696)
						{
							$batch_no_id = $batch_no_arr[$$orderNo][$$colorId][$$txtBatchNo]['id'];
						}
						else
						{
							$batch_no_id = $batch_no_arr[$txt_wo_no][$$colorId][$$txtBatchNo]['id'];
						}
					}
					else if (str_replace("'", "", $$batchId) !="")
					{
						$batch_no_id =$$batchId;
					}

					if($batch_no_id==0 && str_replace("'", "", $$hdnProcessNo) != 33 && str_replace("'", "", $$hdnProcessNo) != 100 && str_replace("'", "", $$hdnProcessNo) != 476){
						echo "20**Batch No. not found.";
						oci_rollback($con);
						disconnect($con);
						die;
					}

					
					if($data_array_dtls!="") $data_array_dtls.=",";
					$data_array_dtls.="('".$dtls_id."',".$update_id.",'".$$rollId."','".$$challanNo."','".$batch_no_id."','".$$knittingSource."','".$$knittingComp."','".$$bookingNo."','".$$receiveBasis."', '".$$productId."','".$$bodyPart."','".$$deterId."','".$$rollGsm."','".$$rolldia."','".$$rollwgt."','".$$buyerId."','".$$job_no."','".$$orderId."','".$$colorId."','".$$rate."','".$$amount."','".$$currency."','".$exchange_rate."','".$$hdnProcessNo."','".$$isSalesId."','".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";


					//if(str_replace("'", "", $cbo_knitting_source) == 3 && (str_replace("'", "", $$hdnProcessNo) == 31 || ($$isSalesId==1 && $variable_textile_sales_maintain==1 && str_replace("'", "", $$hdnProcessNo) != 33)  )  )
					if(str_replace("'", "", $cbo_knitting_source) == 3 && str_replace("'", "", $$hdnProcessNo) == 31  )
					{
						$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
						$ItemDesc=$composition_arr[str_replace("'","",$$deterId)].", ".str_replace("'","",$$rollGsm).", ".str_replace("'","",$$rolldia);
						if($data_array_batch_dtls!="" ) $data_array_batch_dtls.=",";
						$data_array_batch_dtls.="(".$id_dtls_batch.",'".$batch_no_id."','".$$orderId."','".$$productId."','".$$bodyPart."','".$ItemDesc."','".$$rollNo."','".$id_roll."','".$$barcodeNo."',".$$rollwgt.",".$dtls_id.",'".$$isSalesId."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					}
				}
				
				if($$updateDetailsId!=0)
				{

					$batch_no_id=0;
					//if(str_replace("'", "", $cbo_knitting_source) == 3 && (str_replace("'", "", $$hdnProcessNo) == 31 ||  ($$isSalesId==1 && $variable_textile_sales_maintain==1 && str_replace("'", "", $$hdnProcessNo) != 33) )   )
					if(str_replace("'", "", $cbo_knitting_source) == 3 && str_replace("'", "", $$hdnProcessNo) == 31 )
					{
						if($preBatchData[str_replace("'", "", $$batchId)][str_replace("'", "", $$colorId)]['batch_no'] == $$txtBatchNo)
						{
							$batch_no_id =$$batchId;
						}
						else
						{
							//$batch_no_id = $batch_no_arr[$txt_wo_no][$$colorId][$$txtBatchNo]['id'];
							if($hidden_wo_entry_form==418 || $hidden_wo_entry_form==696)
							{
								$batch_no_id = $batch_no_arr[$$orderNo][$$colorId][$$txtBatchNo]['id'];
							}
							else
							{
								$batch_no_id = $batch_no_arr[$txt_wo_no][$$colorId][$$txtBatchNo]['id'];
							}
						}
					}
					else if (str_replace("'", "", $$batchId) !="")
					{
						$batch_no_id =$$batchId;
					}
					
					if($batch_no_id==0 && str_replace("'", "", $$hdnProcessNo) != 33 && str_replace("'", "", $$hdnProcessNo) != 100 && str_replace("'", "", $$hdnProcessNo) != 476)
					{
						echo "20**Batch No. not found";
						oci_rollback($con);
						disconnect($con);
						die;
					}
					
					
					$roll_id_update=$update_roll_arr[str_replace("'","",$update_id)][str_replace("'","",$$updateDetailsId)];	
					//$rollwgt="rollwgt_".$j;	
					if($update_detals_all!="") $update_detals_all.="##";
					$update_detals_all.="".$j."#".$$updateDetailsId."";
					$update_dtls_id[]=str_replace("'","",$$updateDetailsId);
					$update_roll_id[]=str_replace("'","",$roll_id_update);
					$update_array_roll[str_replace("'","",$roll_id_update)]=explode("*",("'".$$rollwgt."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					$update_array_dtls[str_replace("'","",$$updateDetailsId)]=explode("*",("'".$$rollwgt."'*'".$$rate."'*'".$$amount."'*'".$$currency."'*'".$exchange_rate."'*'".$batch_no_id."'*'".str_replace("'", "", $$colorId)."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

					 

					if(str_replace("'", "", $cbo_knitting_source)== 3 && str_replace("'", "", $$hdnProcessNo) == 31 )
					{
						$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
						$ItemDesc=$composition_arr[str_replace("'","",$$deterId)].", ".str_replace("'","",$$rollGsm).", ".str_replace("'","",$$rolldia);
						if($data_array_batch_dtls!="" ) $data_array_batch_dtls.=",";
						$data_array_batch_dtls.="(".$id_dtls_batch.",'".$batch_no_id."','".$$orderId."','".$$productId."','".$$bodyPart."','".$ItemDesc."','".$$rollNo."','".$roll_id_update."','".$$barcodeNo."',".$$rollwgt.",".$$updateDetailsId.",'".$$isSalesId."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					}
				}

				$all_details_id[str_replace("'","",$$updateDetailsId)]=str_replace("'","",$$updateDetailsId);

		 	}
		 }
		
		$remove_barcodes = chop($remove_barcodes,",");

		if(!empty($all_details_id))
		{
			$batch_dtls_sql = sql_select("SELECT b.dtls_id, b.id from pro_batch_create_mst a, pro_batch_create_dtls b 
			where a.id=b.mst_id and a.entry_form=65 and a.status_active=1 and b.status_active=1
			and dtls_id in (".implode(',',$all_details_id).")");

			foreach ($batch_dtls_sql as  $val) 
			{
				$batch_dtls_id_for_del[$val[csf("id")]] = $val[csf("id")];
			}
		}

		if($remove_barcodes != "")
		{
			$production_sql = sql_select("SELECT a.barcode_no, b.recv_number from pro_roll_details a, inv_receive_master b where a.mst_id =b.id and a.entry_form = 66 and b.entry_form=66 and a.status_active=1 and b.status_active=1 and a.barcode_no in (".implode(',',$remove_barcodes).")");

			if(!empty($production_sql))
			{
				echo "20**Finish fabric roll wise production found.\nProduction No: ".$production_sql[0][csf("recv_number")]."\nBarcode no: ".$production_sql[0][csf("barcode_no")];
				oci_rollback($con);
				disconnect($con);
				die;
			}
		}

		/* echo "10**";oci_rollback($con);
		disconnect($con);
		die; */
		 
		//$flag=0;
		$rID2=true;
		$rID3=true;
		$rID4=true;
		$rID5=true;
		$rID6=true;
		$rID7=true;
		$rID8=true;
		$statusChange=true;

		$rID=sql_update("inv_receive_mas_batchroll",$field_array,$data_array,"id",$update_id,0);
		if($rID)
			$flag=1;
		else
			$flag=50;
		
		 if(count($update_array_dtls)>0)
		 {

			//echo "10**".bulk_update_sql_statement("pro_grey_batch_dtls","id",$field_update_dtls,$update_array_dtls,$update_dtls_id); die;

			 $update_grey_dtls=execute_query(bulk_update_sql_statement("pro_grey_batch_dtls","id",$field_update_dtls,$update_array_dtls,$update_dtls_id),1);
			 if($flag==1) 
			 {
				if($update_grey_dtls)
					$flag=1;
				else
					$flag=0; 
			 }
		 }

		 if(count($update_array_roll)>0)
		 {
			 $update_grey_roll=execute_query(bulk_update_sql_statement("pro_roll_details","id",$field_update_roll,$update_array_roll,$update_roll_id),1);
			 if($flag==1) 
			 {
				if($update_grey_roll)
					$flag=1;
				else
					$flag=0; 
			 }
		 }
			
		 if(count($remove_array_roll)>0)
		 {
			/* echo "20**".bulk_update_sql_statement("pro_roll_details","id",$field_romove_roll,$remove_array_roll,$remove_roll_id); 
			oci_rollback($con); 
			disconnect($con);die; */
			$delete_roll=execute_query(bulk_update_sql_statement("pro_roll_details","id",$field_romove_roll,$remove_array_roll,$remove_roll_id),1);
			if($flag==1) 
			{
				if($delete_roll)
					$flag=1;
				else
					$flag=0; 
			} 
		 }
		 
		if(count($remove_array_dtls)>0)
		{
			$update_grey_prod=execute_query(bulk_update_sql_statement("pro_grey_batch_dtls","id",$field_remove_dtls,$remove_array_dtls,$remove_dtls_id),1);
			if($flag==1) 
			{
				if($update_grey_prod)
					$flag=1;
				else
					$flag=0; 
			} 
		}

		if(!empty($data_array_batch_update))
		{
			$rID4=execute_query(bulk_update_sql_statement("pro_batch_create_mst","id",$field_array_batch_update,$data_array_batch_update,$update_batch_id),1);	
			if($flag==1) 
			{
				if($rID4)
					$flag=1;
				else
					$flag=0; 
			} 	
		}

		if($data_array_batch != "")
		{
			//echo "20**INSERT INTO pro_batch_create_mst(".$field_array_batch.") VALUES".$data_array_batch;oci_rollback($con);die;
			$rID5=sql_insert("pro_batch_create_mst",$field_array_batch,$data_array_batch,0);
			if($flag==1) 
			{
				if($rID5)
					$flag=1;
				else
					$flag=0; 
			} 	
		}

		//Delete previous batch details to entry new
		if(!empty($batch_dtls_id_for_del))
		{
			$rID6=execute_query("delete from pro_batch_create_dtls where id in (".implode(",",$batch_dtls_id_for_del).")");
			if($flag==1) 
			{
				if($rID6)
					$flag=1;
				else
					$flag=0; 
			} 
		}

		//Insert new batch details
		if($data_array_batch_dtls !="")
		{
			//echo "10**INSERT INTO pro_batch_create_dtls(".$field_array_batch_dtls.") VALUES".$data_array_batch_dtls;oci_rollback($con);die;
			$rID7=sql_insert("pro_batch_create_dtls",$field_array_batch_dtls,$data_array_batch_dtls,1);
			if($flag==1) 
			{
				if($rID7)
					$flag=1;
				else
					$flag=0; 
			} 
		}


		if($data_array_roll!="")
		{
			//echo "20**INSERT INTO pro_roll_details(".$field_array_roll.") VALUES".$data_array_roll;oci_rollback($con);die;
			$rID8=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
			if($flag==1)
			{
				if($rID8)
					$flag=1;
				else
					$flag=0;
			}
		}

		if($data_array_dtls!="")
		{
			//echo "10**INSERT INTO pro_grey_batch_dtls(".$field_array_insert.") VALUES".$data_array_dtls;oci_rollback($con);die;
			$rID10=sql_insert("pro_grey_batch_dtls",$field_array_insert,$data_array_dtls,1);
			if($flag==1)
			{
				if($rID10)
					$flag=1;
				else
					$flag=0;
			}
		}

		if(!empty($update_issue_roll_ids_arr))
		{

			$rID9=sql_multirow_update("pro_roll_details","is_rcv_done","0","id",implode(",",$update_issue_roll_ids_arr),0);

			//echo "10**".implode(",",$update_issue_roll_ids_arr);oci_rollback($con);disconnect($con);die;
			if($flag==1) 
			{
				if($rID9)
					$flag=1;
				else
					$flag=0; 
			} 
		}

		//echo "20**".$flag ."#". $rID4 ."#". $rID5 ."#". $rID6 ."#". $rID7 ."#". $rID8 ."#". $rID9 ."#". $rID10 .'#'.$delete_roll.'='.$update_grey_roll; 
		//oci_rollback($con); 
		//disconnect($con);die;
		//echo "10**".$flag;oci_rollback($con);disconnect($con);die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_no)."**".$update_detals_all;
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
			    echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_no)."**".$update_detals_all;
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
}

if($action=="grey_item_details_update")
{
	$sqlBatch="SELECT 
		bd.id, bd.rate, bd.amount, bd.currency_id,bm.wo_no,bm.challan_no,
		r.barcode_no, bm.company_id
	FROM 
		inv_receive_mas_batchroll bm
		INNER JOIN pro_grey_batch_dtls bd ON bm.id=bd.mst_id 
		INNER JOIN pro_roll_details r ON bd.id=r.dtls_id
	WHERE 
		r.entry_form = 65 
		AND r.roll_no>0 
		AND r.status_active = 1 
		AND r.is_deleted = 0 
		AND bm.id='".$data."'";
	//echo $sqlBatch; die;
	$result_array=sql_select($sqlBatch);
	$data_array=array();
	$aopBarcode=array();
	foreach($result_array as $row)
	{
		$aopBarcode[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
		$wo_no=$row[csf('wo_no')];
		$company_id=$row[csf('company_id')];
		$challan_no[$row[csf('challan_no')]] = $row[csf('challan_no')];
		$data_array[$row[csf('barcode_no')]][$row[csf('id')]]['rate']=$row[csf('rate')];
		$data_array[$row[csf('barcode_no')]][$row[csf('id')]]['amount']=$row[csf('amount')];
		$data_array[$row[csf('barcode_no')]][$row[csf('id')]]['currency_id']=$row[csf('currency_id')];
	}
	//echo "<pre>";
	//print_r($data_array);

	$challan_no = "'".implode("','",explode(",",implode(",", $challan_no)))."'";
	
		$issue_sql="SELECT c.barcode_no,a.recv_number, c.po_breakdown_id, b.color_id
	FROM 
		inv_receive_mas_batchroll a, 
		pro_roll_details c ,
		pro_grey_batch_dtls b
	WHERE a.id=c.mst_id AND b.mst_id=a.id and b.id=c.dtls_id
	AND a.wo_no='$wo_no'
	AND a.recv_number in (". $challan_no.")
		AND a.status_active=1 
		AND a.is_deleted=0 
		AND a.entry_form=63 
		 
		AND c.entry_form=63 
		AND c.status_active=1 
		AND c.is_deleted=0";

		//AND a.process_id in(31,35)  AND c.is_rcv_done=0
	//echo $issue_sql;	
	$issue_data_array=sql_select($issue_sql);
	 
	$issue_challan_barcode=array(); $challan_barcodeArr=array();
	foreach($issue_data_array as $val)
	{
		if($val[csf('is_rcv_done')]==0)
		{
			//N.B. Receive not done yet barcode of those challans
			$challan_barcodeArr[$val[csf('barcode_no')]]=$val[csf('barcode_no')];
		}

		$po_color_arr[$val[csf('po_breakdown_id')]] .= $val[csf('color_id')].",";
	}
	unset($issue_data_array);
	$diff_barcode=array_diff($challan_barcodeArr,$aopBarcode);
	$remian_barcode=implode(",",$diff_barcode);

	
	$vari_sql = sql_select("select company_name, production_entry from variable_settings_production where company_name = $company_id and variable_list in (66) and status_active=1");
	$variable_textile_sales_maintain=0;
	foreach ($vari_sql as $val) 
	{
		if($val[csf("production_entry")]==2)
		{
			$variable_textile_sales_maintain=1;
		}
	}

	$sql="SELECT a.id, a.entry_form, a.company_id, a.recv_number, a.receive_basis, a.receive_date, a.wo_no as booking_no, a.dyeing_source as knitting_source, a.dyeing_company as knitting_company,b.id as dtls_id, b.prod_id, b.body_part_id, b.febric_description_id, b.gsm, b.width, 
	b.color_id,b.booking_id,b.color_range_id, b.yarn_lot, b.brand_id , b.process_id, b.challan_no, c.mst_id, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.qc_pass_qnty_pcs, d.id as batch_id, d.batch_no,e.entry_form as production_found, c.booking_without_order, b.is_sales, b.buyer_id, b.order_id, f.job_no as fso_no, c.issue_roll_id, f.sales_booking_no, f.booking_id as sales_booking_id
	from inv_receive_mas_batchroll a 
	left join  pro_grey_batch_dtls b on  a.id=b.mst_id  
	left join  fabric_sales_order_mst f on  b.order_id=f.id and b.is_sales=1
	left join  pro_roll_details c on  b.id=c.dtls_id 
	left join   pro_batch_create_mst d on b.batch_id=d.id 
	left join pro_roll_details e on c.barcode_no=e.barcode_no and e.entry_form=66 and e.status_active=1 and e.is_deleted=0
	where  a.id=b.mst_id  and
	a.status_active = 1 AND a.is_deleted = 0 and b.status_active = 1 AND b.is_deleted = 0 and c.status_active = 1 AND c.is_deleted = 0 and a.entry_form IN(65) AND c.entry_form IN(65) and a.id in(".$data.") AND c.roll_no>0  order by e.entry_form ASC"; 

	//echo $sql;
	$resultSet=sql_select($sql);
	$barCode=array();
	$poBreakdownId=array();
	$yarnCountDeterminId=array();
	foreach($resultSet as $row)
	{
		$barCode[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
		
		$yarnCountDeterminId[$row[csf('febric_description_id')]]=$row[csf('febric_description_id')];

		$booking_no_arr=explode("-", $row[csf('booking_no')]);
		if ($booking_no_arr[1]=="SBKD" && $row[csf('knitting_source')]==3) 
		{
			/*$fabricColorId=$row[csf('color_id')];
			$po_color_arr[$row[csf('po_breakdown_id')]] .= $fabricColorId.",";
			$all_colors .= $fabricColorId.",";*/
			$non_orderBooking_id_arr[$row[csf('po_breakdown_id')]]=$row[csf('po_breakdown_id')];
		}
		else if($row[csf('is_sales')] !=1)
		{
			$poBreakdownId[$row[csf('po_breakdown_id')]]=$row[csf('po_breakdown_id')];
		}
	}
	//echo "<pre>";
	//print_r($barCode);

	$poBreakdownId = array_unique($poBreakdownId);
	if(!empty($poBreakdownId))
	{
		$po_color_sql = sql_select("SELECT  e.color_number_id as COLOR_NUMBER_ID, g.contrast_color_id as CONTRAST_COLOR_ID, h.stripe_color as STRIPE_COLOR, b.id as PO_ID 
		from wo_po_break_down b join wo_pre_cos_fab_co_avg_con_dtls e on b.id=e.po_break_down_id 
		left join wo_pre_cos_fab_co_color_dtls g on b.job_id=g.job_id and e.pre_cost_fabric_cost_dtls_id=g.pre_cost_fabric_cost_dtls_id and g.is_deleted=0 
		and g.status_active=1
		left join wo_pre_stripe_color h on b.job_id=h.job_id  and e.pre_cost_fabric_cost_dtls_id=h.pre_cost_fabric_cost_dtls_id and e.color_number_id =h.color_number_id and e.po_break_down_id=h.po_break_down_id and e.gmts_sizes=h.size_number_id 
		where e.cons !=0 and  b.is_deleted=0 and b.status_active in(1,3) and b.id in (".implode(',',$poBreakdownId).")
		group by e.color_number_id , g.contrast_color_id , h.stripe_color , b.id");

		foreach ($po_color_sql as  $row) 
		{
			$fabricColorId=$row['STRIPE_COLOR'];
			
			if(!$fabricColorId){
				$fabricColorId=$row['CONTRAST_COLOR_ID'];
			}
			if(!$fabricColorId){
				$fabricColorId=$row['COLOR_NUMBER_ID'];
			}

			$po_color_arr[$row['PO_ID']] .= $fabricColorId.",";
			$all_colors .= $fabricColorId.",";
			
		}
		//for buyer
		$poArray = get_po_information($poBreakdownId);
	}
	else
	{
		$buyer_name_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	}
	$non_orderBooking_id_arr = array_unique($non_orderBooking_id_arr);
	if(!empty($non_orderBooking_id_arr))
	{
		$non_order_color_sql = sql_select("SELECT a.ID as BOOKIN_ID, C.COLOR_ID
		from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b, sample_development_rf_color c
		where a.booking_no=b.booking_no and b.style_id=c.mst_id and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and a.id in (".implode(',',$non_orderBooking_id_arr).")
		group by a.id, c.color_id");

		foreach ($non_order_color_sql as  $row) 
		{
			$fabricColorId=$row['COLOR_ID'];

			$po_color_arr[$row['BOOKIN_ID']] .= $fabricColorId.",";
			$all_colors .= $fabricColorId.",";
			
		}
	}
	$color_library= return_library_array("select id, color_name from lib_color where status_active=1",'id','color_name');

	
	//for batch
	$batchArray = get_batchFor_GreyRollIssueToProcess($barCode);

	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$deter_array=sql_select($sql_deter);
	if(count($deter_array)>0)
	{
		foreach( $deter_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$row[csf('construction')].",".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
		}
	}

	//for Yarn Count Determin
	//$constructionCompositionArray = get_constructionComposition($yarnCountDeterminId);
	
	
	$reportArray=array();
	foreach($resultSet as $row)
	{
		if(!empty($data_array[$row[csf('barcode_no')]]))
		{
			foreach($data_array[$row[csf('barcode_no')]] as $key=>$val)
			{
				$receiveBasisArray=get_receive_basis($row[csf('entry_form')],$row[csf('receive_basis')]);
				
				$reportArray[$key]['barcode_no'] = $row[csf('barcode_no')];
				$reportArray[$key]['roll_id'] = $row[csf('roll_id')];
				$reportArray[$key]['roll_no'] = $row[csf('roll_no')];
				
				$reportArray[$key]['recv_number'] = $row[csf('challan_no')];
				if($row[csf('knitting_source')] ==3){
					$reportArray[$key]['batch_no'] = $row[csf('batch_no')];
					$reportArray[$key]['batch_id'] = $row[csf('batch_id')];
				}
				else{
					$reportArray[$key]['batch_no'] = $batchArray[$row[csf('barcode_no')]][$row[csf('po_breakdown_id')]]['batch_no'];
					$reportArray[$key]['batch_id'] = $batchArray[$row[csf('barcode_no')]][$row[csf('po_breakdown_id')]]['batch_id'];
				}
				
				$reportArray[$key]['body_part'] = $body_part[$row[csf('body_part_id')]];
				$reportArray[$key]['body_part_id'] = $row[csf('body_part_id')];
				$reportArray[$key]['construction'] = $composition_arr[$row[csf('febric_description_id')]];
				$reportArray[$key]['gsm'] = $row[csf('gsm')];
				$reportArray[$key]['width'] = $row[csf('width')];
				
				$reportArray[$key]['color_id'] = $row[csf('color_id')];
				$reportArray[$key]['color'] = get_color_details($row[csf('color_id')]);
				
				$reportArray[$key]['roll_weight'] = number_format($row[csf('qnty')],2);;
				$reportArray[$key]['qty_in_pcs'] = $row[csf('qc_pass_qnty_pcs')]*1;
				
				if($row[csf('is_sales')]!=1)
				{
					$reportArray[$key]['job_no'] = $poArray[$row[csf('po_breakdown_id')]]['job_no'];
					$reportArray[$key]['job_no_full'] = $poArray[$row[csf('po_breakdown_id')]]['job_no_full'];
					
					$reportArray[$key]['year'] = $row['year']=$poArray[$row[csf('po_breakdown_id')]]['year'];
					
					$reportArray[$key]['buyer_id'] = $poArray[$row[csf('po_breakdown_id')]]['buyer_id'];
					$reportArray[$key]['buyer'] = $poArray[$row[csf('po_breakdown_id')]]['buyer_name'];
					$reportArray[$key]['order_no'] = $poArray[$row[csf('po_breakdown_id')]]['po_number'];
				}
				else
				{
					$reportArray[$key]['buyer_id'] = $row[csf('buyer_id')];
					$reportArray[$key]['buyer'] = $buyer_name_array[$row[csf('buyer_id')]];
					$reportArray[$key]['order_no'] = $row[csf('fso_no')];
				}

				$reportArray[$key]['order_id'] = $row[csf('po_breakdown_id')];
				$reportArray[$key]['is_sales'] = $row[csf('is_sales')];
				$reportArray[$key]['issue_roll_id'] = $row[csf('issue_roll_id')];
		
				$reportArray[$key]['knitting_company_id'] = $row[csf('knitting_company')];
				$reportArray[$key]['knitting_company'] = get_knitting_company_details($row[csf('knitting_source')],$row[csf('knitting_company')]);
				$reportArray[$key]['knitting_source'] = $row[csf('knitting_source')];
		
				$reportArray[$key]['booking_no'] = $row[csf('booking_no')];
				$reportArray[$key]['receive_basis_id'] = $receiveBasisArray['id'];
				$reportArray[$key]['receive_basis_dtls'] = $receiveBasisArray['dtls'];
				$reportArray[$key]['deter_id'] = $row[csf('febric_description_id')];
				$reportArray[$key]['prod_id'] = $row[csf('prod_id')];
				$reportArray[$key]['process_id'] = $row[csf('process_id')];
				
				$reportArray[$key]['rate'] = $val['rate'];
				$reportArray[$key]['amount'] = $val['amount'];
				$reportArray[$key]['currency_id'] = $val['currency_id'];
				$reportArray[$key]['production_found'] = $row[csf('production_found')];
				$reportArray[$key]['booking_without_order'] = $row[csf('booking_without_order')];
				$reportArray[$key]['sales_booking_no'] = $row[csf('sales_booking_no')];
				$reportArray[$key]['sales_booking_id'] = $row[csf('sales_booking_id')];
			}
		}
	}
	//echo "<pre>";
	//print_r($reportArray); die;
	
	$i=0;$total_sum_qty=0;
	foreach($reportArray as $dtlsId=>$row)
	{

		$color_id_arr = explode(",",chop($po_color_arr[$row['order_id']],","));

		$po_color_show=array();
		foreach ($color_id_arr as $value) {
			$po_color_show[$value] =$color_library[$value];
		}
		$i++;
		?>
		<tr id="tr_<?php echo $i; ?>" align="center" valign="middle">
			<td width="45" id="sl_<? echo $i; ?>"><input type="checkbox" id="checkRow_<? echo $i; ?>" name="checkRow[]" onClick="fnc_total_sum(<? echo $i; ?>)" checked="checked"     <?php if($row['production_found']==66){ echo "disabled"; } ?> > &nbsp; &nbsp;<? echo $i; ?></td>
			<td width="80"><?php echo $row['barcode_no']; ?></td>
			<td width="50"><?php echo $row['roll_no']; ?></td>
			<td width="100"><?php echo $row['recv_number']; ?></td>
			<td width="80">
				<?php 
				//if($row['knitting_source'] ==3 && ($row['process_id'] ==31 || ($row['is_sales'] ==1 && $variable_textile_sales_maintain==1) ) )
				if($row['knitting_source'] ==3 && $row['process_id'] ==31 )
				{
					?>
	            	<input type="text" style="width:65px;" id="txtBatchNo_<? echo $i; ?>" name="txtBatchNo[]" class="text_boxes" value="<?php echo $row['batch_no']; ?>" onKeyUp="fnc_copy_batch_no(<? echo $i;?>)" <?php if($row['production_found']==66){ echo "readonly"; } ?> />
	            	<?
				} 
				else
				{
					?>
	            	<input type="hidden" style="width:65px;" id="txtBatchNo_<? echo $i; ?>" name="txtBatchNo[]" class="text_boxes" value="<?php echo $row['batch_no']; ?>" />
	            	<?
	            	echo $row['batch_no']; 
				}
				
				?>
			</td>
			<td width="100">
				<?php 
				//if($row['knitting_source']==3 && $row['process_id'] !=33)
				if($row['knitting_source']==3 && $row['process_id'] ==31)
				{

					if($row['production_found']==66)
					{ 
						echo create_drop_down( "colorId_".$i, 80, $po_color_show,"", 1, "Select", $row['color_id'], "fnc_copy_color_no(".$i.")",1,"","","","","","","colorId[]" );
					}
					else
					{
						echo create_drop_down( "colorId_".$i, 80, $po_color_show,"", 1, "Select", $row['color_id'], "fnc_copy_color_no(".$i.")","","","","","","","","colorId[]" );
					}
				}
				else
				{
					?>
						<input type="hidden" name="colorId[]" id="colorId_<? echo $i; ?>" value="<? echo $row['color_id']; ?>" />
						
					<?
					echo $row['color'];
				}
				?>
			</td>
			<td width="90"><p><?php echo $row['body_part']; ?></p></td>
			<td width="100" style="word-break:break-all;"><?php echo $row['construction']; ?></td>
			<td width="60"><?php echo $row['gsm']; ?></td>
			<td width="60"><?php echo $row['width']; ?></td>
			<td width="70" style="word-break:break-all;"><?php echo $row['color']; ?></td>
			<td width="70" id="rollWgt_<? echo $i; ?>"><input type="text" onBlur="fnc_roll_wgt_check(<? echo $i; ?>)" class="text_boxes_numeric" name="rolWgt[]" id="rolWgt_<? echo $i; ?>" style="width:45px;" value="<?php echo $row['roll_weight']; ?>" <?php if($row['production_found']==66){ echo "readonly"; } ?> /></td>  
			<td width="70"><input type="text" class="text_boxes_numeric" id="rate_<? echo $i; ?>" name="rate[]" style="width:45px;" placeholder="Write/Browse" onKeyUp="Calculate_amount(this.value,<? echo $i; ?>)" onDblClick="rate_form_workorder(<?php echo $row['deter_id']; ?>,<?php echo $row['body_part_id']; ?>,'<?php echo $row['job_no_full']; ?>',<? echo $i; ?>)" value="<?php echo $row['rate']; ?>" <?php if($row['production_found']==66){ echo "readonly"; } ?>/></td>
			<td width="70"><input type="text" class="text_boxes_numeric" id="amount_<? echo $i; ?>" name="amount[]" style="width:45px;" value="<?php echo $row['amount']; ?>" readonly /></td>
			
			<td width="70">

			<?php 
			if($row['production_found']==66)
			{ 
				echo create_drop_down( "currencyId_".$i, 70, $currency,"", 1, "Select", $row['currency_id'], "",1,"","","","","","","currencyId[]" ); 
			}
			else
			{
				echo create_drop_down( "currencyId_".$i, 70, $currency,"", 1, "Select", $row['currency_id'], "","","","","","","","","currencyId[]" ); 
			}
			?>
				
			</td>
			<td width="50" id="job_<? echo $i; ?>" style="word-break:break-all;"><?php echo $row['job_no_full']; ?></td>
			<td width="50" id="year_<? echo $i; ?>" align="center"><?php echo $row['year']; ?></td>
			<td width="65" id="buyer_<? echo $i; ?>" style="word-break:break-all;"><?php echo $row['buyer']; ?></td>
			<td width="80" id="order_<? echo $i; ?>" style="word-break:break-all;" align="left"><?php echo $row['order_no']; ?></td>
			<td width="80" id="cons_<? echo $i; ?>" style="word-break:break-all;" align="left"><?php echo $row['knitting_company']; ?></td>
			<td width="100" id="comps_<? echo $i; ?>" style="word-break:break-all;" align="left"><?php echo $row['booking_no']; ?></td>
			<td width="" id="gsm_<? echo $i; ?>"><?php echo $row['receive_basis_dtls']; ?></td>  
			<input type="hidden" name="updateDetaisId[]" id="updateDetaisId_<? echo $i; ?>" value="<? echo $dtlsId; ?>" />                        
			<input type="hidden" name="rollId[]" id="rollId_<? echo $i; ?>" value="<? echo $row['roll_id']; ?>" />
			<input type="hidden" name="rollNo[]" id="rollNo_<? echo $i; ?>" value="<? echo $row['roll_no']; ?>" />
			<input type="hidden" name="chkrollwgt[]" id="chkrollwgt_<? echo $i; ?>" value="<? echo $row['roll_weight']; ?>" />
			<input type="hidden" name="challanNo[]" id="challanNo_<? echo $i; ?>" value="<? echo $row['recv_number']; ?>" />
			<input type="hidden" name="batchNo[]" id="batchNo_<? echo $i; ?>" value="<? echo $row['batch_no']; ?>" />
			<input type="hidden" name="batchId[]" id="batchId_<? echo $i; ?>" value="<? echo $row['batch_id']; ?>" />
			<input type="hidden" name="bodyPartId[]" id="bodyPartId_<? echo $i; ?>" value="<? echo $row['body_part_id']; ?>"/>
			<!-- <input type="hidden" name="colorId[]" id="colorId_<? echo $i; ?>" value="<? echo $row['color_id']; ?>" /> -->
			<input type="hidden" name="deterId[]" id="deterId_<? echo $i; ?>" value="<? echo $row['deter_id']; ?>"/>
			<input type="hidden" name="productId[]" id="productId_<? echo $i; ?>" value="<? echo $row['prod_id']; ?>" />
			<input type="hidden" name="orderId[]" id="orderId_<? echo $i; ?>" value="<? echo $row['order_id']; ?>" />
			<input type="hidden" name="orderNo[]" id="orderNo_<? echo $i; ?>" value="<? echo $row['order_no']; ?>" />
			<input type="hidden" name="buyerId[]" id="buyerId_<? echo $i; ?>" value="<? echo $row['buyer_id']; ?>"/>  
			<input type="hidden" name="rollDia[]" id="rollDia_<? echo $i; ?>" value="<? echo $row['width']; ?>"/>
			<input type="hidden" name="rollGsm[]" id="rollGsm_<? echo $i; ?>" value="<? echo $row['gsm']; ?>"/>
			<input type="hidden" name="fabricId[]" id="fabricId_<? echo $i; ?>" value="<? echo $dtlsId; ?>"/>
			<input type="hidden" name="receiveBasis[]" id="receiveBasis_<? echo $i; ?>" value="<? echo $row['receive_basis_id']; ?>"/>
			<input type="hidden" name="knittingSource[]" id="knittingSource_<? echo $i; ?>" value="<? echo $row['knitting_source']; ?>"/>
			<input type="hidden" name="knittingComp[]" id="knittingComp_<? echo $i; ?>" value="<? echo $row['knitting_company_id']; ?>"/>
			<input type="hidden" name="jobNo[]" id="jobNo_<? echo $i; ?>" value="<? echo $row['job_no_full']; ?>"/>
			<input type="hidden" name="bookingNo[]" id="bookingNo_<? echo $i; ?>" value="<? echo $row['booking_no']; ?>"/>
			<input type="hidden" name="barcodNumber[]" id="barcodNumber_<? echo $i; ?>" value="<? echo $row['barcode_no']; ?>"/>
			<input type="hidden" name="exchangeRate[]" id="exchangeRate_<? echo $i; ?>"/>
			<input type="hidden" name="hdnProcessNo[]" id="hdnProcessNo_<? echo $i; ?>" value="<? echo $row['process_id']; ?>"/>
			<input type="hidden" name="hdnProductionEntryFrom[]" id="hdnProductionEntryFrom_<? echo $i; ?>" value="<? echo $row['production_found']; ?>"/>
			<input type="hidden" name="issueRollId[]" id="issueRollId_<? echo $i; ?>" value="<? echo $row['issue_roll_id']; ?>"/>
			<input type="hidden" name="bookingWithoutOrder[]" id="bookingWithoutOrder_<? echo $i; ?>" value="<? echo $row['booking_without_order']; ?>"/>
			<input type="hidden" name="isSalesId[]" id="isSalesId_<? echo $i; ?>" value="<? echo $row['is_sales']; ?>"/>
			<input type="hidden" name="salesBookingNO[]" id="salesBookingNO_<? echo $i; ?>" value="<? echo $row['sales_booking_no']; ?>"/>
			<input type="hidden" name="salesBookingID[]" id="salesBookingID_<? echo $i; ?>" value="<? echo $row['sales_booking_id']; ?>"/>
		</tr>
		<?php
		$total_sum_qty+=$row['roll_weight'];
	}
	//===========New Issue add which are not save==========
    //for batch
	$new_sql="SELECT  a.id, a.entry_form, a.company_id, a.recv_number, a.receive_basis, a.receive_date, a.wo_no as booking_no,  a.dyeing_source as  knitting_source, a.dyeing_company as knitting_company, a.process_id, b.id as dtls_id, b.prod_id, b.body_part_id, b.febric_description_id, b.gsm, b.width, b.color_id,b.booking_id,b.color_range_id, b.yarn_lot, b.brand_id , c.mst_id, c.barcode_no, c.id as issue_roll_id, c.roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.qc_pass_qnty_pcs, c.booking_without_order, c.is_sales, b.buyer_id, d.job_no as fso_no, e.batch_no, e.id as batch_id, d.sales_booking_no, d.booking_id as sales_booking_id from inv_receive_mas_batchroll a, pro_grey_batch_dtls b left join pro_batch_create_mst e on b.batch_id=e.id and b.batch_id !=0, pro_roll_details c left join fabric_sales_order_mst d on c.po_breakdown_id=d.id and c.is_sales=1 where a.id=b.mst_id and b.id=c.dtls_id and a.id=b.mst_id and a.status_active = 1 AND a.is_deleted = 0 and b.status_active = 1 AND b.is_deleted = 0 and c.status_active = 1 AND c.is_deleted = 0 and a.entry_form IN(63) AND c.entry_form IN(63) AND a.wo_no='$wo_no'
	AND a.recv_number in (". $challan_no.") AND c.barcode_no IN(".$remian_barcode.")  AND c.roll_no>0 and c.is_rcv_done=0";

	//and a.process_id in(31,35)
	//b.shift_name, b.floor_id, b.machine_no_id, b.yarn_count --field not in issue_to_process table
	//,b.color_range_id, b.yarn_lot, b.brand_id --data not from issue_to_process page inserted

	$new_data_array=sql_select($new_sql);
	//echo "<pre>";
	//print_r($data_array); die;
	
	$barCode=array();
	$poBreakdownId=array();
	$yarnCountDeterminId=array();
	foreach($new_data_array as $row)
	{
		$barCode[]=$row[csf('barcode_no')];
		$booking_no=$row[csf('booking_no')];
		$poBreakdownId[]=$row[csf('po_breakdown_id')];
		$yarnCountDeterminId[]=$row[csf('febric_description_id')];
	}
	
	$batchArray = get_batchFor_GreyRollIssueToProcess($barCode);
	//for Yarn Count Determin
	//$constructionCompositionArray = get_constructionComposition($yarnCountDeterminId);
	//for buyer
	$poArray = get_po_information($poBreakdownId);
	
	foreach($new_data_array as $row)
	{
		$i++;

		if($row[csf('po_breakdown_id')]!="")
		{
			$row['batch_no']=$row[csf('batch_no')];
			$row['batch_id']=$row[csf('batch_id')];
		}
		else
		{
			$row['batch_no']=$batchArray[$row[csf('barcode_no')]][$row[csf('po_breakdown_id')]]['batch_no'];
			$row['batch_id']=$batchArray[$row[csf('barcode_no')]][$row[csf('po_breakdown_id')]]['batch_id'];
		}

		
		$row['construction']=$composition_arr[$row[csf('febric_description_id')]];
		
		if($row[csf('is_sales')]!=1)
		{
			$row['buyer']=$poArray[$row[csf('po_breakdown_id')]]['buyer_name'];
			$row['buyer_id']=$poArray[$row[csf('po_breakdown_id')]]['buyer_id'];

			$row['job_no']=$poArray[$row[csf('po_breakdown_id')]]['job_no'];
			$row['job_no_full']=$poArray[$row[csf('po_breakdown_id')]]['job_no_full'];
			$row['year']=$poArray[$row[csf('po_breakdown_id')]]['year'];
			$row['order_no']=$poArray[$row[csf('po_breakdown_id')]]['po_number'];
		}
		else
		{
			$row['buyer']=$buyer_name_array[$row[csf('buyer_id')]];
			$row['buyer_id']=$row[csf('buyer_id')];
			$row['order_no']=$row[csf('fso_no')];
		}

		$row['order_id']=$row[csf('po_breakdown_id')];
		$rollWeight=number_format($row[csf('qnty')],2);
		$qtyInPcs=$row[csf('qc_pass_qnty_pcs')]*1;
		$row['color']=get_color_details($row[csf('color_id')]);
		$row['knitting_company']=get_knitting_company_details($row[csf('knitting_source')],$row[csf('knitting_company')]);
		$receiveBasisArray=get_receive_basis($row[csf('entry_form')],$row[csf('receive_basis')]);
		$receive_basis_id=$receiveBasisArray['id'];
		$receive_basis_dtls=$receiveBasisArray['dtls'];

		$po_color_arr[$row[csf('po_breakdown_id')]] .= $row[csf('color_id')].',';

		$color_id_arr = array_unique(explode(",",chop($po_color_arr[$row[csf('po_breakdown_id')]],",")));
		$po_color_show=array();
		foreach ($color_id_arr as $value) {
			$po_color_show[$value] =$color_library[$value];
		}
		?>
        <tr id="tr_<?php echo $i; ?>" align="center" valign="middle">
            <td width="45" id="sl_<? echo $i; ?>"><input type="checkbox" id="checkRow_<? echo $i; ?>" name="checkRow[]"  onClick="fnc_total_sum(<? echo $i; ?>)" > &nbsp; &nbsp;<? echo $i; ?></td>
            <td width="80"><?php echo $row[csf('barcode_no')]; ?></td>
            <td width="50"><?php echo $row[csf('roll_no')]; ?></td>
            <td width="100"><?php echo $row[csf('recv_number')]; ?></td>
            <td width="80">
            	<?
            	//if($row['knitting_source']==3 && ($row[csf('process_id')] ==31 ) || ($row[csf('is_sales')] ==1 && $variable_textile_sales_maintain==1)  )
            	if($row['knitting_source']==3 && $row[csf('process_id')] ==31 )
				{
	            	?>
	            	<input type="text" style="width:65px;" id="txtBatchNo_<? echo $i; ?>" name="txtBatchNo[]" class="text_boxes" value="<?php echo $row['batch_no']; ?>" onKeyUp="fnc_copy_batch_no(<? echo $i;?>)" />
	            	<?
            	}
            	else
            	{
            		?>
	            	<input type="hidden" style="width:65px;" id="txtBatchNo_<? echo $i; ?>" name="txtBatchNo[]" class="text_boxes" value="<?php echo $row['batch_no']; ?>" />
	            	<?
            		echo $row['batch_no'];
            	}
            	?>
            </td>
			<td width="100">
				<?php 
				//if($row['knitting_source']==3 && $row[csf('process_id')] !=33)
				if($row['knitting_source']==3 && $row[csf('process_id')] ==31)
				{
					echo create_drop_down( "colorId_".$i, 80, $po_color_show,"", 1, "Select", $row[csf('color_id')], "fnc_copy_color_no(".$i.")","","","","","","","","colorId[]" );
				}
				else
				{
					?>
						<input type="hidden" name="colorId[]" id="colorId_<? echo $i; ?>" value="<? echo $row[csf('color_id')]; ?>" />
						
					<?
					echo $row['color'];
				}
				?>
			</td>
            <td width="90"><?php echo $body_part[$row[csf('body_part_id')]]; ?></td>
            <td width="100" style="word-break:break-all;"><?php echo $row['construction']; ?></td>
            <td width="60"><?php echo $row[csf('gsm')]; ?></td>
            <td width="60"><?php echo $row[csf('width')]; ?></td>
            <td width="70" style="word-break:break-all;"><?php echo $row['color']; ?></td>
            
            <td width="70" id="rollWgt_<? echo $i; ?>"> <input type="text" onBlur="fnc_roll_wgt_check(<? echo $i; ?>)" class="text_boxes_numeric" name="rolWgt[]" id="rolWgt_<? echo $i; ?>" style="width:45px;" value="<?php echo $rollWeight; ?>"/></td>  
            <td width="70">
            <input type="text"  class="text_boxes_numeric" id="rate_<? echo $i; ?>" name="rate[]"  style="width:45px;" placeholder="Write/Browse" onKeyUp="Calculate_amount(this.value,<? echo $i; ?>)" onDblClick="rate_form_workorder(<?php echo $row[csf("febric_description_id")]; ?>,<?php echo $row[csf("body_part_id")]; ?>,'<?php echo $row['job_no_full']; ?>',<? echo $i; ?>)"/>
            </td>
            <td width="70">
            <input type="text" class="text_boxes_numeric" id="amount_<? echo $i; ?>" name="amount[]"  style="width:45px;" readonly/>
            </td>
            <td width="70">
            <?php 
            echo create_drop_down( "currencyId_".$i, 70, $currency,"", 1, "Select", "", "","","","","","","","","currencyId[]" );
            ?>
            </td>
            <td width="50" id="job_<? echo $i; ?>" style="word-break:break-all;"><?php echo $row['job_no_full']; ?></td>
            <td width="50" id="year_<? echo $i; ?>" align="center"><?php echo $row['year']; ?></td>
            <td width="65" id="buyer_<? echo $i; ?>" style="word-break:break-all;"><?php echo $row['buyer']; ?></td>
            <td width="80" id="order_<? echo $i; ?>" style="word-break:break-all;" align="left"><?php echo $row['order_no']; ?></td>
            <td width="80" id="cons_<? echo $i; ?>" style="word-break:break-all;" align="left"><?php echo $row['knitting_company']; ?></td>
            <td width="100" id="comps_<? echo $i; ?>" style="word-break:break-all;" align="left"><?php echo $row[csf('booking_no')]; ?></td>
            <td width="" id="gsm_<? echo $i; ?>"><?php echo $receive_basis_dtls; ?></td>  
            <input type="hidden" name="updateDetaisId[]" id="updateDetaisId_<? echo $i; ?>" value="0" />                        
            <input type="hidden" name="rollId[]" id="rollId_<? echo $i; ?>" value="<? echo $row[csf('roll_id')]; ?>" />
            <input type="hidden" name="rollNo[]" id="rollNo_<? echo $i; ?>" value="<? echo $row[csf('roll_no')]; ?>" />
            <input type="hidden" name="chkrollwgt[]" id="chkrollwgt_<? echo $i; ?>" value="<? echo $rollWeight; ?>" />
            <input type="hidden" name="challanNo[]" id="challanNo_<? echo $i; ?>" value="<? echo $row[csf('recv_number')]; ?>" />
            <input type="hidden" name="batchNo[]" id="batchNo_<? echo $i; ?>" value="<? echo $row['batch_no']; ?>" />
            <input type="hidden" name="batchId[]" id="batchId_<? echo $i; ?>" value="<? echo $row['batch_id']; ?>" />
            <input type="hidden" name="bodyPartId[]" id="bodyPartId_<? echo $i; ?>" value="<? echo $row[csf('body_part_id')]; ?>"/>
            <input type="hidden" name="colorId[]" id="colorId_<? echo $i; ?>" value="<? echo $row[csf('color_id')]; ?>" />
            <input type="hidden" name="deterId[]" id="deterId_<? echo $i; ?>" value="<? echo $row[csf('febric_description_id')]; ?>"/>
            <input type="hidden" name="productId[]" id="productId_<? echo $i; ?>" value="<? echo $row[csf('prod_id')]; ?>" />
            <input type="hidden" name="orderId[]" id="orderId_<? echo $i; ?>" value="<? echo $row['order_id']; ?>" />
            <input type="hidden" name="buyerId[]" id="buyerId_<? echo $i; ?>" value="<? echo $row['buyer_id']; ?>"/>  
            <input type="hidden" name="rollDia[]" id="rollDia_<? echo $i; ?>" value="<? echo $row[csf('width')]; ?>"/>
            <input type="hidden" name="rollGsm[]" id="rollGsm_<? echo $i; ?>" value="<? echo $row[csf('gsm')]; ?>"/>
            <input type="hidden" name="fabricId[]" id="fabricId_<? echo $i; ?>" value="<? echo $row[csf('dtls_id')]; ?>"/>
            <input type="hidden" name="receiveBasis[]" id="receiveBasis_<? echo $i; ?>" value="<? echo $receive_basis_id; ?>"/>
            <input type="hidden" name="knittingSource[]" id="knittingSource_<? echo $i; ?>" value="<? echo $row[csf('knitting_source')]; ?>"/>
            <input type="hidden" name="knittingComp[]" id="knittingComp_<? echo $i; ?>" value="<? echo $row[csf('knitting_company')]; ?>"/>
            <input type="hidden" name="jobNo[]" id="jobNo_<? echo $i; ?>" value="<? echo $row['job_no_full']; ?>"/>
            <input type="hidden" name="bookingNo[]" id="bookingNo_<? echo $i; ?>" value="<? echo $row[csf('booking_no')]; ?>"/>
            <input type="hidden" name="barcodNumber[]" id="barcodNumber_<? echo $i; ?>" value="<? echo $row[csf('barcode_no')]; ?>"/>
            <input type="hidden" name="exchangeRate[]" id="exchangeRate_<? echo $i; ?>"/>
            <input type="hidden" name="hdnProcessNo[]" id="hdnProcessNo_<? echo $i; ?>" value="<? echo $row[csf('process_id')]; ?>"/>
            <input type="hidden" name="hdnProductionEntryFrom[]" id="hdnProductionEntryFrom_<? echo $i; ?>" value=""/>
			<input type="hidden" name="issueRollId[]" id="issueRollId_<? echo $i; ?>" value="<? echo $row[csf('issue_roll_id')]; ?>"/>
            <input type="hidden" name="bookingWithoutOrder[]" id="bookingWithoutOrder_<? echo $i; ?>" value="<? echo $row[csf('booking_without_order')]; ?>"/>
            <input type="hidden" name="isSalesId[]" id="isSalesId_<? echo $i; ?>" value="<? echo $row[csf('is_sales')]; ?>"/>
			<input type="hidden" name="salesBookingNO[]" id="salesBookingNO_<? echo $i; ?>" value="<? echo $row[csf('sales_booking_no')]; ?>"/>
			<input type="hidden" name="salesBookingID[]" id="salesBookingID_<? echo $i; ?>" value="<? echo $row[csf('sales_booking_id')]; ?>"/>
        </tr>
        <?php
		$total_sum_qty+=$rollWeight;
	}
	?>
    
     <tfoot>
    <tr bgcolor="#99CCFF">
    <td colspan="11" align="right"> Total Sum </td>
     <td id="" align="right">   <input type="text" class="text_boxes_numeric" id="tot_sum" name="tot_sum"  style="width:60px;"  value="<? echo number_format($total_sum_qty,2,'.','');?> " readonly/> </td>
      <td colspan="10">&nbsp;</td>
    </tr>
    </tfoot>
    <?
}

if($action=="grey_item_details")
{
	$data = explode('_', $data);
	$issue_to_process_ids = $data[0];
	$cbo_company_id = $data[1];

	$buyer_name_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");

	$vari_sql = sql_select("select company_name, production_entry from variable_settings_production where company_name = $cbo_company_id and variable_list in (66) and status_active=1");
	$variable_textile_sales_maintain=0;
	foreach ($vari_sql as $val) 
	{
		if($val[csf("production_entry")]==2)
		{
			$variable_textile_sales_maintain=1;
		}
	}
	
	$sql="SELECT  a.id, a.process_id, a.entry_form, a.company_id, a.recv_number, a.receive_basis, a.receive_date, a.wo_no as booking_no,  a.dyeing_source as  knitting_source, a.dyeing_company as knitting_company,b.id as dtls_id, b.prod_id, b.body_part_id, b.febric_description_id, b.gsm, b.width, b.color_id, b.booking_id,b.color_range_id, b.yarn_lot, b.brand_id , c.mst_id, c.barcode_no, c.id as issue_roll_id, c.roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.qc_pass_qnty_pcs, c.booking_without_order, c.is_sales, b.buyer_id, d.job_no as fso_no, d.sales_booking_no, d.booking_id as sales_booking_id
	from inv_receive_mas_batchroll a,pro_grey_batch_dtls b,pro_roll_details c left join fabric_sales_order_mst d on c.po_breakdown_id=d.id and c.is_sales=1 
	where a.id=b.mst_id and b.id=c.dtls_id and a.id=b.mst_id and a.status_active = 1 AND a.is_deleted = 0 and b.status_active = 1 AND b.is_deleted = 0 and c.status_active = 1 AND c.is_deleted = 0 and a.entry_form IN(63) AND c.entry_form IN(63) and a.id in(".$issue_to_process_ids.") AND c.roll_no>0 and c.is_rcv_done=0 AND c.is_returned=0";
	//and a.process_id in(31,35)
	//echo $sql;
	//b.shift_name, b.floor_id, b.machine_no_id, b.yarn_count --field not in issue_to_process table
	//,b.color_range_id, b.yarn_lot, b.brand_id --data not from issue_to_process page inserted

	$data_array=sql_select($sql);
	$barCode=array();
	$poBreakdownId=array();
	$yarnCountDeterminId=array();
	foreach($data_array as $row)
	{
		$barCode[]=$row[csf('barcode_no')];
		$booking_no=$row[csf('booking_no')];
		
		$yarnCountDeterminId[$row[csf('febric_description_id')]]=$row[csf('febric_description_id')];
		$all_iss_to_process_id[$row[csf('issue_roll_id')]] =$row[csf('issue_roll_id')];

		if($row[csf('is_sales')]==0)
		{
			$booking_no_arr=explode("-", $booking_no);
			if ($booking_no_arr[1]=="SBKD" && $row[csf('knitting_source')]==3) 
			{
				/*$fabricColorId=$row[csf('color_id')];
				$po_color_arr[$row[csf('po_breakdown_id')]] .= $fabricColorId.",";
				$all_colors .= $fabricColorId.",";*/
				$non_orderBooking_id_arr[$row[csf('po_breakdown_id')]]=$row[csf('po_breakdown_id')];
			}
			else 
			{
				$poBreakdownId[]=$row[csf('po_breakdown_id')];
			}	
		}
	}
	$barcode_nos=implode(",",$barCode);
	$issue_ids=implode(",",$all_iss_to_process_id);
	$chk_sql="SELECT b.challan_no, a.barcode_no 
	FROM 
		pro_roll_details a, 
		inv_receive_mas_batchroll b 
	WHERE 
		a.mst_id=b.id 
		AND b.status_active=1 
		AND b.is_deleted=0 
		AND b.entry_form=65
		AND b.wo_no='$booking_no' 
		AND a.barcode_no in($barcode_nos)
		 
		AND a.status_active=1 
		AND a.is_deleted=0 
		AND a.entry_form=65 
		AND a.issue_roll_id in ($issue_ids)";
	//echo $sql;	
	$inserted_roll=sql_select($chk_sql);
	foreach($inserted_roll as $row)
	{
		$inserted_barcode[$row[csf('barcode_no')]]=$row[csf('barcode_no')];	
	}

	$poBreakdownId = array_unique($poBreakdownId);
	if(!empty($poBreakdownId))
	{
		$po_color_sql = sql_select("SELECT  e.color_number_id as COLOR_NUMBER_ID, g.contrast_color_id as CONTRAST_COLOR_ID, h.stripe_color as STRIPE_COLOR, b.id as PO_ID 
		from wo_po_break_down b 
		join wo_pre_cos_fab_co_avg_con_dtls e on b.id=e.po_break_down_id 
		left join wo_pre_cos_fab_co_color_dtls g on b.job_id=g.job_id and e.pre_cost_fabric_cost_dtls_id=g.pre_cost_fabric_cost_dtls_id and g.is_deleted=0 and g.status_active=1
		left join wo_pre_stripe_color h on b.job_id=h.job_id  and e.pre_cost_fabric_cost_dtls_id=h.pre_cost_fabric_cost_dtls_id and e.color_number_id =h.color_number_id and e.po_break_down_id=h.po_break_down_id and e.gmts_sizes=h.size_number_id 
		where e.cons !=0 and  b.is_deleted=0 and b.status_active in(1,3) and b.id in (".implode(',',$poBreakdownId).")
		group by e.color_number_id , g.contrast_color_id , h.stripe_color , b.id");

		foreach ($po_color_sql as  $row) 
		{
			$fabricColorId=$row['STRIPE_COLOR'];
			
			if(!$fabricColorId){
				$fabricColorId=$row['CONTRAST_COLOR_ID'];
			}
			if(!$fabricColorId){
				$fabricColorId=$row['COLOR_NUMBER_ID'];
			}

			$po_color_arr[$row['PO_ID']] .= $fabricColorId.",";
			$all_colors .= $fabricColorId.",";
			
		}
	}

	$non_orderBooking_id_arr = array_unique($non_orderBooking_id_arr);
	if(!empty($non_orderBooking_id_arr))
	{
		$non_order_color_sql = sql_select("SELECT a.ID as BOOKIN_ID, C.COLOR_ID
		from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b, sample_development_rf_color c
		where a.booking_no=b.booking_no and b.style_id=c.mst_id and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and a.id in (".implode(',',$non_orderBooking_id_arr).")
		group by a.id, c.color_id");

		foreach ($non_order_color_sql as  $row) 
		{
			$fabricColorId=$row['COLOR_ID'];

			$po_color_arr[$row['BOOKIN_ID']] .= $fabricColorId.",";
			$all_colors .= $fabricColorId.",";
			
		}
	}
	
	$color_library= return_library_array("select id, color_name from lib_color where status_active=1",'id','color_name');
	
	//for batch
	$batchArray = get_batchFor_GreyRollIssueToProcess($barCode);
	//for Yarn Count Determin
	//$constructionCompositionArray = get_constructionComposition($yarnCountDeterminId);

	$yarnCountDeterminId = array_filter($yarnCountDeterminId);
	$constructionCompositionArray=array();
	if(!empty($yarnCountDeterminId))
	{
		$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id in(".implode(',',$yarnCountDeterminId).")  and b.status_active=1 and a.status_active=1 group by a.id, a.construction, b.copmposition_id, b.percent ";
		$deter_array=sql_select($sql_deter);
		foreach( $deter_array as $row )
		{
			if(array_key_exists($row[csf('id')],$constructionCompositionArray))
			{
				$constructionCompositionArray[$row[csf('id')]]=$constructionCompositionArray[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
			else
			{
				$constructionCompositionArray[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
		}
	}

	//print_r($constructionCompositionArray);die;
	//for buyer
	$poArray = get_po_information($poBreakdownId);

	$product_array=array();
	$product_sql = sql_select("select id, detarmination_id, gsm, dia_width,item_description, unit_of_measure from product_details_master where item_category_id=13");
	foreach($product_sql as $row)
	{
		$product_array[$row[csf("id")]]['item_description']=$row[csf("item_description")];
	}
	
	$i=0;$total_sum_qty=0;
	foreach($data_array as $row)
	{
		if($inserted_barcode[$row[csf('barcode_no')]]=='') //Already Barcode saved Checked
		{
			$i++;
			$row['batch_no']=$batchArray[$row[csf('barcode_no')]][$row[csf('po_breakdown_id')]]['batch_no'];
			$row['batch_id']=$batchArray[$row[csf('barcode_no')]][$row[csf('po_breakdown_id')]]['batch_id'];
			if($row[csf('process_id')]==31 && $product_array[$row[csf("prod_id")]]['item_description']!="")
			{
				$row['construction']=$product_array[$row[csf("prod_id")]]['item_description'];
			}
			else
			{
				$row['construction']=$constructionCompositionArray[$row[csf('febric_description_id')]];
			}

			if($row[csf('is_sales')]==1)
			{
				$row['buyer']=$buyer_name_array[$row[csf('buyer_id')]];
				$row['buyer_id']=$row[csf('buyer_id')];
				$row['order_no']=$row[csf('fso_no')];
			}
			else
			{
				$row['buyer']=$poArray[$row[csf('po_breakdown_id')]]['buyer_name'];
				$row['buyer_id']=$poArray[$row[csf('po_breakdown_id')]]['buyer_id'];
				$row['job_no']=$poArray[$row[csf('po_breakdown_id')]]['job_no'];
				$row['job_no_full']=$poArray[$row[csf('po_breakdown_id')]]['job_no_full'];
				$row['year']=$poArray[$row[csf('po_breakdown_id')]]['year'];
				$row['order_no']=$poArray[$row[csf('po_breakdown_id')]]['po_number'];
			}

			$row['order_id']=$row[csf('po_breakdown_id')];
			$rollWeight=number_format($row[csf('qnty')],2);
			$qtyInPcs=$row[csf('qc_pass_qnty_pcs')]*1;
			$row['color']=get_color_details($row[csf('color_id')]);
			$row['knitting_company']=get_knitting_company_details($row[csf('knitting_source')],$row[csf('knitting_company')]);
			$receiveBasisArray=get_receive_basis($row[csf('entry_form')],$row[csf('receive_basis')]);
			$receive_basis_id=$receiveBasisArray['id'];
			$receive_basis_dtls=$receiveBasisArray['dtls'];

			$po_color_arr[$row[csf('po_breakdown_id')]] .= $row[csf('color_id')].",";

			$color_id_arr = explode(",",chop($po_color_arr[$row[csf('po_breakdown_id')]],","));

			$po_color_show=array();
			foreach ($color_id_arr as $value) {
				$po_color_show[$value] =$color_library[$value];
			}

			?>
	        <tr id="tr_<?php echo $i; ?>" align="center" valign="middle">
	            <td width="45" id="sl_<? echo $i; ?>"><input type="checkbox" id="checkRow_<? echo $i; ?>" name="checkRow[]" onClick="fnc_total_sum(<? echo $i; ?>)" checked="checked"> &nbsp; &nbsp;<? echo $i; ?></td>
	            <td width="80"><?php echo $row[csf('barcode_no')]; ?></td>
	            <td width="50"><?php echo $row[csf('roll_no')]; ?></td>
	            <td width="100"><?php echo $row[csf('recv_number')]; ?></td>
	            <td width="80">
	            	<?

	            	if($row[csf('knitting_source')]==3 && ($row[csf('process_id')] ==31 || $row[csf('process_id')] ==33 || $row[csf('process_id')] ==100 || $row[csf('process_id')] ==476) && $variable_textile_sales_maintain==1 && $row[csf('is_sales')] ==1 )
					{
		            	?>
		            	<input type="text" style="width:65px;" id="txtBatchNo_<? echo $i; ?>" name="txtBatchNo[]" class="text_boxes" value="<?php //echo $row['batch_no']; ?>" onKeyUp="fnc_copy_batch_no(<? echo $i;?>)" />
		            	<?
						$row['batch_no']="";
						$row['batch_id']="";
						//N.B Here batch will written by user for every process;
	            	}
	            	else
	            	{
	            		?>
		            	<input type="hidden" style="width:65px;" id="txtBatchNo_<? echo $i; ?>" name="txtBatchNo[]" class="text_boxes" value="<?php echo $row['batch_no']; ?>" />
		            	<?
	            		echo $row['batch_no'];
	            	}
	            	?>
	            </td>
	            <td width="100">
	            	

	            	<?php 
					if($row[csf('knitting_source')]==3 && $row[csf('process_id')] ==31)
					{
						//Only for Dyeing
	            		echo create_drop_down( "colorId_".$i, 80, $po_color_show,"", 1, "Select", "", "fnc_copy_color_no(".$i.")","","","","","","","","colorId[]" );
					}
					else
					{
						//Heat setting and other finishing process
						?>
							<input type="hidden" name="colorId[]" id="colorId_<? echo $i; ?>" value="<? echo $row[csf('color_id')]; ?>" />
							
						<?
						echo $row['color'];
					}
	            	?>
	            </td>
	            <td width="90"><p><?php echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
	            <td width="100" style="word-break:break-all;"><?php echo $row['construction']; ?></td>
	            <td width="60"><?php echo $row[csf('gsm')]; ?></td>
	            <td width="60"><?php echo $row[csf('width')]; ?></td>
	            <td width="70" style="word-break:break-all;"><?php echo $row['color']; ?></td>
	            
	            <td width="70" id="rollWgt_<? echo $i; ?>"> <input type="text" onBlur="fnc_roll_wgt_check(<? echo $i; ?>)" class="text_boxes_numeric" name="rolWgt[]" id="rolWgt_<? echo $i; ?>" style="width:45px;" value="<?php echo $rollWeight; ?>"  /></td>  
	            <td width="70">
	            <input type="text"  class="text_boxes_numeric" id="rate_<? echo $i; ?>" name="rate[]"  style="width:45px;" placeholder="Write/Browse" onKeyUp="Calculate_amount(this.value,<? echo $i; ?>)" onDblClick="rate_form_workorder(<?php echo $row[csf("febric_description_id")]; ?>,<?php echo $row[csf("body_part_id")]; ?>,'<?php echo $row['job_no_full']; ?>',<? echo $i; ?>)"/>
	            </td>
	            <td width="70">
	            <input type="text" class="text_boxes_numeric" id="amount_<? echo $i; ?>" name="amount[]"  style="width:45px;" readonly/>
	            </td>
	            <td width="70">
	            <?php 
	            echo create_drop_down( "currencyId_".$i, 70, $currency,"", 1, "Select", "", "","","","","","","","","currencyId[]" );
	            ?>
	            </td>
	            <td width="50" id="job_<? echo $i; ?>" style="word-break:break-all;"><?php echo $row['job_no_full']; ?></td>
	            <td width="50" id="year_<? echo $i; ?>" align="center"><?php echo $row['year']; ?></td>
	            <td width="65" id="buyer_<? echo $i; ?>" style="word-break:break-all;"><?php echo $row['buyer']; ?></td>
	            <td width="80" id="order_<? echo $i; ?>" style="word-break:break-all;" align="left"><?php echo $row['order_no']; ?></td>
	            <td width="80" id="cons_<? echo $i; ?>" style="word-break:break-all;" align="left"><?php echo $row['knitting_company']; ?></td>
	            <td width="100" id="comps_<? echo $i; ?>" style="word-break:break-all;" align="left"><?php echo $row[csf('booking_no')]; ?></td>
	            <td width="" id="gsm_<? echo $i; ?>"><?php echo $receive_basis_dtls; ?></td>  
	            <input type="hidden" name="updateDetaisId[]" id="updateDetaisId_<? echo $i; ?>" value="0" />                        
	            <input type="hidden" name="rollId[]" id="rollId_<? echo $i; ?>" value="<? echo $row[csf('roll_id')]; ?>" />
	            <input type="hidden" name="rollNo[]" id="rollNo_<? echo $i; ?>" value="<? echo $row[csf('roll_no')]; ?>" />
	            <input type="hidden" name="chkrollwgt[]" id="chkrollwgt_<? echo $i; ?>" value="<? echo $rollWeight; ?>" />
	            <input type="hidden" name="challanNo[]" id="challanNo_<? echo $i; ?>" value="<? echo $row[csf('recv_number')]; ?>" />
	            <input type="hidden" name="batchNo[]" id="batchNo_<? echo $i; ?>" value="<? echo $row['batch_no']; ?>" />
	            <input type="hidden" name="batchId[]" id="batchId_<? echo $i; ?>" value="<? echo $row['batch_id']; ?>" />
	            <input type="hidden" name="bodyPartId[]" id="bodyPartId_<? echo $i; ?>" value="<? echo $row[csf('body_part_id')]; ?>"/>
	            
	            <input type="hidden" name="deterId[]" id="deterId_<? echo $i; ?>" value="<? echo $row[csf('febric_description_id')]; ?>"/>
	            <input type="hidden" name="productId[]" id="productId_<? echo $i; ?>" value="<? echo $row[csf('prod_id')]; ?>" />
	            <input type="hidden" name="orderId[]" id="orderId_<? echo $i; ?>" value="<? echo $row['order_id']; ?>" />
	            <input type="hidden" name="orderNo[]" id="orderNo_<? echo $i; ?>" value="<? echo $row['order_no']; ?>" />
	            <input type="hidden" name="buyerId[]" id="buyerId_<? echo $i; ?>" value="<? echo $row['buyer_id']; ?>"/>  
	            <input type="hidden" name="rollDia[]" id="rollDia_<? echo $i; ?>" value="<? echo $row[csf('width')]; ?>"/>
	            <input type="hidden" name="rollGsm[]" id="rollGsm_<? echo $i; ?>" value="<? echo $row[csf('gsm')]; ?>"/>
	            <input type="hidden" name="fabricId[]" id="fabricId_<? echo $i; ?>" value="<? echo $row[csf('dtls_id')]; ?>"/>
	            <input type="hidden" name="receiveBasis[]" id="receiveBasis_<? echo $i; ?>" value="<? echo $receive_basis_id; ?>"/>
	            <input type="hidden" name="knittingSource[]" id="knittingSource_<? echo $i; ?>" value="<? echo $row[csf('knitting_source')]; ?>"/>
	            <input type="hidden" name="knittingComp[]" id="knittingComp_<? echo $i; ?>" value="<? echo $row[csf('knitting_company')]; ?>"/>
	            <input type="hidden" name="jobNo[]" id="jobNo_<? echo $i; ?>" value="<? echo $row['job_no_full']; ?>"/>
	            <input type="hidden" name="bookingNo[]" id="bookingNo_<? echo $i; ?>" value="<? echo $row[csf('booking_no')]; ?>"/>
	            <input type="hidden" name="barcodNumber[]" id="barcodNumber_<? echo $i; ?>" value="<? echo $row[csf('barcode_no')]; ?>"/>
	            <input type="hidden" name="exchangeRate[]" id="exchangeRate_<? echo $i; ?>"/>
	            <input type="hidden" name="hdnProcessNo[]" id="hdnProcessNo_<? echo $i; ?>" value="<? echo $row[csf('process_id')]; ?>"/>
	            <input type="hidden" name="hdnProductionEntryFrom[]" id="hdnProductionEntryFrom_<? echo $i; ?>" value=""/>
	            <input type="hidden" name="issueRollId[]" id="issueRollId_<? echo $i; ?>" value="<? echo $row[csf('issue_roll_id')]; ?>"/>
	            <input type="hidden" name="bookingWithoutOrder[]" id="bookingWithoutOrder_<? echo $i; ?>" value="<? echo $row[csf('booking_without_order')]; ?>"/>
	            <input type="hidden" name="isSalesId[]" id="isSalesId_<? echo $i; ?>" value="<? echo $row[csf('is_sales')]; ?>"/>
	            <input type="hidden" name="salesBookingNO[]" id="salesBookingNO<? echo $i; ?>" value="<? echo $row[csf('sales_booking_no')]; ?>"/>
	            <input type="hidden" name="salesBookingID[]" id="salesBookingID_<? echo $i; ?>" value="<? echo $row[csf('sales_booking_id')]; ?>"/>
	        </tr>
	        <?php
			$total_sum_qty+=$rollWeight;
		} //Check End
	} 
	?>
    <tfoot>
    <tr bgcolor="#99CCFF">
    <td colspan="11" align="right"> Total Sum </td>
     <td id="" align="right">   <input type="text" class="text_boxes_numeric" id="tot_sum" name="tot_sum"  style="width:60px;"  value="<? echo number_format($total_sum_qty,2);?> " readonly/> </td>
      <td colspan="10">&nbsp;    </td>
    </tr>
    </tfoot>
    <?
	
	die;
}

//grey_item_details_both
if($action=="grey_item_details_both")
{
	$expData = explode('_', $data);
	

	
	$sqlBatch="
	SELECT 
		bd.id, bd.rate, bd.amount, bd.currency_id,
		r.barcode_no 
	FROM 
		inv_receive_mas_batchroll bm
		INNER JOIN pro_grey_batch_dtls bd ON bm.id=bd.mst_id 
		INNER JOIN pro_roll_details r ON bd.id=r.dtls_id
	WHERE 
		r.entry_form = 65 
		AND r.roll_no>0 
		AND r.status_active = 1 
		AND r.is_deleted = 0 
		AND bm.id IN(".$expData[0].")";
	//echo $sqlBatch; die;
	$result_array=sql_select($sqlBatch);
	$data_array=array();
	$aopBarcode=array();
	foreach($result_array as $row)
	{
		$aopBarcode[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
		$data_array[$row[csf('barcode_no')]][$row[csf('id')]]['rate']=$row[csf('rate')];
		$data_array[$row[csf('barcode_no')]][$row[csf('id')]]['amount']=$row[csf('amount')];
		$data_array[$row[csf('barcode_no')]][$row[csf('id')]]['currency_id']=$row[csf('currency_id')];
	}
	//echo $expData[1].'D';
	
	 $sql_roll_issue="select b.dtls_id,b.barcode_no,a.wo_no,a.recv_number from inv_receive_mas_batchroll a,pro_roll_details b where a.id=b.mst_id and a.id IN(".$expData[1].")
	 and a.entry_form = 63 and b.entry_form = 63 and a.status_active=1 and b.status_active=1";
	 $issue_result_array=sql_select($sql_roll_issue);
	foreach($issue_result_array as $row)
	{
		$issueBarcode[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
		$issueWo_noArr[$row[csf('barcode_no')]]=$row[csf('wo_no')];
	}
	
		//$aopBarcode=implode(",",$aopBarcode);
	$chk_sql="SELECT b.challan_no, a.barcode_no 
	FROM 
		pro_roll_details a, 
		inv_receive_mas_batchroll b 
	WHERE 
		a.mst_id=b.id 
		AND b.status_active=1 
		AND b.is_deleted=0 
		AND b.entry_form=65
		 
		AND a.barcode_no IN(".implode(',',$aopBarcode).")
		 
		AND a.status_active=1 
		AND a.is_deleted=0 
		AND a.entry_form=65";
	//echo $sql;	
	$inserted_roll=sql_select($chk_sql);
	foreach($inserted_roll as $row)
	{
		$inserted_barcode[$row[csf('barcode_no')]]=$row[csf('barcode_no')];	
	}
	
	$remain_barcode=array_diff($issueBarcode,$inserted_barcode);
	//echo "==";
	//print_r($remain_barcode);
	
	$diff_remain_barcode=implode(',',$remain_barcode);
	$update_barcode_cond="";$update_barcode_cond2="";
	if($diff_remain_barcode)
	{
		$update_barcode_cond="and c.barcode_no in($diff_remain_barcode)";
		$update_barcode_cond2="and r.barcode_no in($diff_remain_barcode)";
	}

	$issue_sql="SELECT  a.id, a.entry_form, a.company_id, a.recv_number, a.receive_basis, a.receive_date, a.wo_no as booking_no,  a.dyeing_source as  knitting_source, a.dyeing_company as knitting_company,b.id as dtls_id, b.prod_id, b.body_part_id, b.febric_description_id, b.gsm, b.width, b.color_id,b.booking_id,b.color_range_id, b.yarn_lot, b.brand_id , c.mst_id, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.qc_pass_qnty_pcs, c.is_sales from inv_receive_mas_batchroll a,pro_grey_batch_dtls b,pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and a.id=b.mst_id and a.status_active = 1 AND a.is_deleted = 0 and b.status_active = 1 AND b.is_deleted = 0 and c.status_active = 1 AND c.is_deleted = 0 and a.entry_form IN(63) AND c.entry_form IN(63) 	
	and a.id IN(".$expData[1].") and a.process_id in(31,35) AND c.roll_no>0 AND c.barcode_no IN(".implode(',',$aopBarcode).") ";
		
	//echo $sql;
	$resultSet=sql_select($issue_sql);
	$barCode=array();
	$poBreakdownId=array();
	$yarnCountDeterminId=array();
	foreach($resultSet as $row)
	{
		$barCode[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
		$poBreakdownId[$row[csf('po_breakdown_id')]]=$row[csf('po_breakdown_id')];
		$yarnCountDeterminId[$row[csf('febric_description_id')]]=$row[csf('febric_description_id')];
	}
	
	//for batch
	$batchArray = get_batchFor_GreyRollIssueToProcess($barCode);
	//for Yarn Count Determin
	$constructionCompositionArray = get_constructionComposition($yarnCountDeterminId);
	//for buyer
	$poArray = get_po_information($poBreakdownId);
	
	$reportArray=array();
	foreach($resultSet as $row)
	{
		if(!empty($data_array[$row[csf('barcode_no')]]))
		{
			foreach($data_array[$row[csf('barcode_no')]] as $key=>$val)
			{
				$receiveBasisArray=get_receive_basis($row[csf('entry_form')],$row[csf('receive_basis')]);
				
				$issueWo_no=$issueWo_noArr[$row[csf('barcode_no')]];
				$reportArray[$key]['barcode_no'] = $row[csf('barcode_no')];
				$reportArray[$key]['roll_id'] = $row[csf('roll_id')];
				$reportArray[$key]['roll_no'] = $row[csf('roll_no')];
				
				$reportArray[$key]['recv_number'] = $row[csf('recv_number')];
				$reportArray[$key]['batch_no'] = $batchArray[$row[csf('barcode_no')]][$row[csf('po_breakdown_id')]]['batch_no'];
				$reportArray[$key]['batch_id'] = $batchArray[$row[csf('barcode_no')]][$row[csf('po_breakdown_id')]]['batch_id'];
				
				$reportArray[$key]['body_part'] = $body_part[$row[csf('body_part_id')]];
				$reportArray[$key]['body_part_id'] = $row[csf('body_part_id')];
				$reportArray[$key]['construction'] = $constructionCompositionArray[$row[csf('febric_description_id')]];
				$reportArray[$key]['gsm'] = $row[csf('gsm')];
				$reportArray[$key]['width'] = $row[csf('width')];
				
				$reportArray[$key]['color_id'] = $row[csf('color_id')];
				$reportArray[$key]['color'] = get_color_details($row[csf('color_id')]);
				
				$reportArray[$key]['roll_weight'] = number_format($row[csf('qnty')],2);;
				$reportArray[$key]['qty_in_pcs'] = $row[csf('qc_pass_qnty_pcs')]*1;
				
				$reportArray[$key]['job_no'] = $poArray[$row[csf('po_breakdown_id')]]['job_no'];
				$reportArray[$key]['job_no_full'] = $poArray[$row[csf('po_breakdown_id')]]['job_no_full'];
				
				$reportArray[$key]['year'] = $row['year']=$poArray[$row[csf('po_breakdown_id')]]['year'];
				
				$reportArray[$key]['buyer_id'] = $poArray[$row[csf('po_breakdown_id')]]['buyer_id'];
				$reportArray[$key]['buyer'] = $poArray[$row[csf('po_breakdown_id')]]['buyer_name'];
				
				$reportArray[$key]['order_id'] = $row[csf('po_breakdown_id')];
				$reportArray[$key]['order_no'] = $poArray[$row[csf('po_breakdown_id')]]['po_number'];
				$reportArray[$key]['is_sales'] = $row[csf('is_sales')];
		
				$reportArray[$key]['knitting_company_id'] = $row[csf('knitting_company')];
				$reportArray[$key]['knitting_company'] = get_knitting_company_details($row[csf('knitting_source')],$row[csf('knitting_company')]);
				$reportArray[$key]['knitting_source'] = $row[csf('knitting_source')];
		//echo $row[csf('booking_no')].'dd';
				$reportArray[$key]['booking_no'] = $issueWo_no;
				$reportArray[$key]['receive_basis_id'] = $receiveBasisArray['id'];
				$reportArray[$key]['receive_basis_dtls'] = $receiveBasisArray['dtls'];
				$reportArray[$key]['deter_id'] = $row[csf('febric_description_id')];
				$reportArray[$key]['prod_id'] = $row[csf('prod_id')];
				
				$reportArray[$key]['rate'] = $val['rate'];
				$reportArray[$key]['amount'] = $val['amount'];
				$reportArray[$key]['currency_id'] = $val['currency_id'];
			}
		}
	}
	//echo "<pre>";
	//print_r($reportArray); die;
	$i=0;$total_sum_qty=0;
	foreach($reportArray as $dtlsId=>$row)
	{
		$i++;
		//echo $dtlsId.', ';
		?>
		<tr id="tr_<?php echo $i; ?>" align="center" valign="middle">
			<td width="45" id="sl_<? echo $i; ?>"><input type="checkbox" id="checkRow_<? echo $i; ?>" name="checkRow[]" onClick="fnc_total_sum(<? echo $i; ?>)" checked="checked"> &nbsp; &nbsp;<? echo $i; ?></td>
			<td width="80"><?php echo $row['barcode_no']; ?></td>
			<td width="50"><?php echo $row['roll_no']; ?></td>
			<td width="100"><?php echo $row['recv_number']; ?></td>
			<td width="80"><?php echo $row['batch_no']; ?></td>
			<td width="90"><?php echo $row['body_part']; ?></td>
			<td width="100" style="word-break:break-all;"><?php echo $row['construction']; ?></td>
			<td width="60"><?php echo $row['gsm']; ?></td>
			<td width="60"><?php echo $row['width']; ?></td>
			<td width="70" style="word-break:break-all;"><?php echo $row['color']; ?></td>
			<td width="70" id="rollWgt_<? echo $i; ?>"><input type="text" onBlur="fnc_roll_wgt_check(<? echo $i; ?>)" class="text_boxes_numeric" name="rolWgt[]" id="rolWgt_<? echo $i; ?>" style="width:45px;" value="<?php echo $row['roll_weight']; ?>" readonly /></td>  
			<td width="70"><input type="text" class="text_boxes_numeric" id="rate_<? echo $i; ?>" name="rate[]" style="width:45px;" placeholder="Write/Browse" onKeyUp="Calculate_amount(this.value,<? echo $i; ?>)" onDblClick="rate_form_workorder(<?php echo $row['deter_id']; ?>,<?php echo $row['body_part_id']; ?>,'<?php echo $row['job_no_full']; ?>',<? echo $i; ?>)" value="<?php echo $row['rate']; ?>" /></td>
			<td width="70"><input type="text" class="text_boxes_numeric" id="amount_<? echo $i; ?>" name="amount[]" style="width:45px;" value="<?php echo $row['amount']; ?>" readonly /></td>
			<td width="70"><?php echo create_drop_down( "currencyId_".$i, 70, $currency,"", 1, "Select", $row['currency_id'], "","","","","","","","","currencyId[]" ); ?></td>
			<td width="50" id="job_<? echo $i; ?>" style="word-break:break-all;"><?php echo $row['job_no_full']; ?></td>
			<td width="50" id="year_<? echo $i; ?>" align="center"><?php echo $row['year']; ?></td>
			<td width="65" id="buyer_<? echo $i; ?>" style="word-break:break-all;"><?php echo $row['buyer']; ?></td>
			<td width="80" id="order_<? echo $i; ?>" style="word-break:break-all;" align="left"><?php echo $row['order_no']; ?></td>
			<td width="80" id="cons_<? echo $i; ?>" style="word-break:break-all;" align="left"><?php echo $row['knitting_company']; ?></td>
			<td width="100" id="comps_<? echo $i; ?>" style="word-break:break-all;" align="left"><?php echo $row['booking_no']; ?></td>
			<td width="" id="gsm_<? echo $i; ?>"><?php echo $row['receive_basis_dtls']; ?></td>  
			<input type="hidden" name="updateDetaisId[]" id="updateDetaisId_<? echo $i; ?>" value="<? echo $dtlsId; ?>" />                        
			<input type="hidden" name="rollId[]" id="rollId_<? echo $i; ?>" value="<? echo $row['roll_id']; ?>" />
			<input type="hidden" name="rollNo[]" id="rollNo_<? echo $i; ?>" value="<? echo $row['roll_no']; ?>" />
			<input type="hidden" name="chkrollwgt[]" id="chkrollwgt_<? echo $i; ?>" value="<? echo $row['roll_weight']; ?>" />
			<input type="hidden" name="challanNo[]" id="challanNo_<? echo $i; ?>" value="<? echo $row['recv_number']; ?>" />
			<input type="hidden" name="batchNo[]" id="batchNo_<? echo $i; ?>" value="<? echo $row['batch_no']; ?>" />
			<input type="hidden" name="batchId[]" id="batchId_<? echo $i; ?>" value="<? echo $row['batch_id']; ?>" />
			<input type="hidden" name="bodyPartId[]" id="bodyPartId_<? echo $i; ?>" value="<? echo $row['body_part_id']; ?>"/>
			<input type="hidden" name="colorId[]" id="colorId_<? echo $i; ?>" value="<? echo $row['color_id']; ?>" />
			<input type="hidden" name="deterId[]" id="deterId_<? echo $i; ?>" value="<? echo $row['deter_id']; ?>"/>
			<input type="hidden" name="productId[]" id="productId_<? echo $i; ?>" value="<? echo $row['prod_id']; ?>" />
			<input type="hidden" name="orderId[]" id="orderId_<? echo $i; ?>" value="<? echo $row['order_id']; ?>" />
			<input type="hidden" name="orderNo[]" id="orderNo_<? echo $i; ?>" value="<? echo $row['order_no']; ?>" />
			<input type="hidden" name="buyerId[]" id="buyerId_<? echo $i; ?>" value="<? echo $row['buyer_id']; ?>"/>  
			<input type="hidden" name="rollDia[]" id="rollDia_<? echo $i; ?>" value="<? echo $row['width']; ?>"/>
			<input type="hidden" name="rollGsm[]" id="rollGsm_<? echo $i; ?>" value="<? echo $row['gsm']; ?>"/>
			<input type="hidden" name="fabricId[]" id="fabricId_<? echo $i; ?>" value="<? echo $dtlsId; ?>"/>
			<input type="hidden" name="receiveBasis[]" id="receiveBasis_<? echo $i; ?>" value="<? echo $row['receive_basis_id']; ?>"/>
			<input type="hidden" name="knittingSource[]" id="knittingSource_<? echo $i; ?>" value="<? echo $row['knitting_source']; ?>"/>
			<input type="hidden" name="knittingComp[]" id="knittingComp_<? echo $i; ?>" value="<? echo $row['knitting_company_id']; ?>"/>
			<input type="hidden" name="jobNo[]" id="jobNo_<? echo $i; ?>" value="<? echo $row['job_no_full']; ?>"/>
			<input type="hidden" name="bookingNo[]" id="bookingNo_<? echo $i; ?>" value="<? echo $row['booking_no']; ?>"/>
			<input type="hidden" name="barcodNumber[]" id="barcodNumber_<? echo $i; ?>" value="<? echo $row['barcode_no']; ?>"/>
			<input type="hidden" name="exchangeRate[]" id="exchangeRate_<? echo $i; ?>"/>
			<input type="hidden" name="isSalesId[]" id="isSalesId_<? echo $i; ?>" value="<? echo $row['is_sales']; ?>"/>
			<input type="hidden" name="salesBookingNO[]" id="salesBookingNO_<? echo $i; ?>" value=""/>
			<input type="hidden" name="salesBookingID[]" id="salesBookingID_<? echo $i; ?>" value=""/>
		</tr>
		<?php
		$total_sum_qty+=$row['roll_weight'];
	}

	//=============new=================
	$sqlBatchBarcode="
	SELECT 
		r.barcode_no 
	FROM 
		pro_roll_details r 
		INNER JOIN inv_receive_mas_batchroll bm ON r.mst_id=bm.id 
	WHERE 
		r.entry_form = 63 
		AND r.roll_no>0 
		AND r.status_active = 1 
		AND r.is_deleted = 0 
		AND bm.process_id in(31,35) 
		AND bm.id IN(".$expData[1].")"; //update_barcode_cond
		//AND bm.recv_number='".$data."'";
	//echo $sqlBatchBarcode; die;
	  $new_sql="select  a.id, a.entry_form, a.company_id, a.recv_number, a.receive_basis, a.receive_date, a.wo_no as booking_no,  a.dyeing_source as  knitting_source, a.dyeing_company as knitting_company,b.id as dtls_id, b.prod_id, b.body_part_id, b.febric_description_id, b.gsm, b.width, b.color_id,b.booking_id,b.color_range_id, b.yarn_lot, b.brand_id , c.mst_id, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.qc_pass_qnty_pcs, c.is_sales from inv_receive_mas_batchroll a,pro_grey_batch_dtls b,pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and a.id=b.mst_id and a.status_active = 1 AND a.is_deleted = 0 and b.status_active = 1 AND b.is_deleted = 0 and c.status_active = 1 AND c.is_deleted = 0 and a.entry_form IN(63) AND c.entry_form IN(63) 	
	 and c.is_rcv_done=0 and c.is_returned=0
	  and a.id IN(".$expData[1].") and a.process_id in(31,35)  AND c.roll_no>0 $update_barcode_cond";
	 
	/* $sql="
	SELECT 
		a.id, a.entry_form, a.company_id, a.recv_number, a.receive_basis, a.receive_date, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, 
		b.id as dtls_id, b.prod_id, b.body_part_id, b.febric_description_id, b.gsm, b.width, b.color_id,b.color_range_id, b.yarn_lot, b.brand_id, b.shift_name, b.floor_id, b.machine_no_id, b.yarn_count, 
		c.mst_id, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.qc_pass_qnty_pcs 
	FROM 
		inv_receive_master a 
		INNER JOIN pro_grey_prod_entry_dtls b ON a.id = b.mst_id
		INNER JOIN pro_roll_details c ON b.id = c.dtls_id
	WHERE 
		a.entry_form IN(2,22)
		AND c.entry_form IN(2,22)
		AND c.roll_no>0 
		AND c.status_active = 1 
		AND c.is_deleted = 0 
		AND c.barcode_no IN(".$sqlBatchBarcode.") $update_barcode_cond";*/

	$data_array=sql_select($new_sql);
	//echo "<pre>";
	//print_r($data_array); die;
	
	$barCode=array();
	$poBreakdownId=array();
	$yarnCountDeterminId=array();
	foreach($data_array as $row)
	{
		$barCode[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
		$poBreakdownId[$row[csf('po_breakdown_id')]]=$row[csf('po_breakdown_id')];
		$yarnCountDeterminId[$row[csf('febric_description_id')]]=$row[csf('febric_description_id')];
	}
	
	//for batch
	$batchArray = get_batchFor_GreyRollIssueToProcess($barCode);
	//for Yarn Count Determin
	$constructionCompositionArray = get_constructionComposition($yarnCountDeterminId);
	//for buyer
	$poArray = get_po_information($poBreakdownId);
	
	foreach($data_array as $row)
	{
		$i++;
		$row['batch_no']=$batchArray[$row[csf('barcode_no')]][$row[csf('po_breakdown_id')]]['batch_no'];
		$row['batch_id']=$batchArray[$row[csf('barcode_no')]][$row[csf('po_breakdown_id')]]['batch_id'];
		$row['construction']=$constructionCompositionArray[$row[csf('febric_description_id')]];
		$row['buyer']=$poArray[$row[csf('po_breakdown_id')]]['buyer_name'];
		$row['buyer_id']=$poArray[$row[csf('po_breakdown_id')]]['buyer_id'];

		$row['job_no']=$poArray[$row[csf('po_breakdown_id')]]['job_no'];
		$row['job_no_full']=$poArray[$row[csf('po_breakdown_id')]]['job_no_full'];
		$row['year']=$poArray[$row[csf('po_breakdown_id')]]['year'];
		$row['order_no']=$poArray[$row[csf('po_breakdown_id')]]['po_number'];
		$row['order_id']=$row[csf('po_breakdown_id')];
		$rollWeight=number_format($row[csf('qnty')],2);
		$qtyInPcs=$row[csf('qc_pass_qnty_pcs')]*1;
		$row['color']=get_color_details($row[csf('color_id')]);
		$row['knitting_company']=get_knitting_company_details($row[csf('knitting_source')],$row[csf('knitting_company')]);
		$receiveBasisArray=get_receive_basis($row[csf('entry_form')],$row[csf('receive_basis')]);
		$receive_basis_id=$receiveBasisArray['id'];
		$receive_basis_dtls=$receiveBasisArray['dtls'];
		$row[csf('booking_no')]=$issueWo_noArr[$row[csf('barcode_no')]];
		?>
        <tr id="tr_<?php echo $i; ?>" align="center" valign="middle">
            <td width="45" id="sl_<? echo $i; ?>"><input type="checkbox" id="checkRow_<? echo $i; ?>" name="checkRow[]"  onClick="fnc_total_sum(<? echo $i; ?>)" checked="checked"> &nbsp; &nbsp;<? echo $i; ?></td>
            <td width="80"><?php echo $row[csf('barcode_no')]; ?></td>
            <td width="50"><?php echo $row[csf('roll_no')]; ?></td>
            <td width="100"><?php echo $row[csf('recv_number')]; ?></td>
            <td width="80"><?php echo $row['batch_no']; ?></td>
            <td width="90"><?php echo $body_part[$row[csf('body_part_id')]]; ?></td>
            <td width="100" style="word-break:break-all;"><?php echo $row['construction']; ?></td>
            <td width="60"><?php echo $row[csf('gsm')]; ?></td>
            <td width="60"><?php echo $row[csf('width')]; ?></td>
            <td width="70" style="word-break:break-all;"><?php echo $row['color']; ?></td>
            
            <td width="70" id="rollWgt_<? echo $i; ?>"> <input type="text" onBlur="fnc_roll_wgt_check(<? echo $i; ?>)" class="text_boxes_numeric" name="rolWgt[]" id="rolWgt_<? echo $i; ?>" style="width:45px;" value="<?php echo $rollWeight; ?>"/></td>  
            <td width="70">
            <input type="text"  class="text_boxes_numeric" id="rate_<? echo $i; ?>" name="rate[]"  style="width:45px;" placeholder="Write/Browse" onKeyUp="Calculate_amount(this.value,<? echo $i; ?>)" onDblClick="rate_form_workorder(<?php echo $row[csf("febric_description_id")]; ?>,<?php echo $row[csf("body_part_id")]; ?>,'<?php echo $row['job_no_full']; ?>',<? echo $i; ?>)"/>
            </td>
            <td width="70">
            <input type="text" class="text_boxes_numeric" id="amount_<? echo $i; ?>" name="amount[]"  style="width:45px;" readonly/>
            </td>
            <td width="70">
            <?php 
            echo create_drop_down( "currencyId_".$i, 70, $currency,"", 1, "Select", "", "","","","","","","","","currencyId[]" );
            ?>
            </td>
            <td width="50" id="job_<? echo $i; ?>" style="word-break:break-all;"><?php echo $row['job_no_full']; ?></td>
            <td width="50" id="year_<? echo $i; ?>" align="center"><?php echo $row['year']; ?></td>
            <td width="65" id="buyer_<? echo $i; ?>" style="word-break:break-all;"><?php echo $row['buyer']; ?></td>
            <td width="80" id="order_<? echo $i; ?>" style="word-break:break-all;" align="left"><?php echo $row['order_no']; ?></td>
            <td width="80" id="cons_<? echo $i; ?>" style="word-break:break-all;" align="left"><?php echo $row['knitting_company']; ?></td>
            <td width="100" id="comps_<? echo $i; ?>" style="word-break:break-all;" align="left"><?php echo $row[csf('booking_no')]; ?></td>
            <td width="" id="gsm_<? echo $i; ?>"><?php echo $receive_basis_dtls; ?></td>  
            <input type="hidden" name="updateDetaisId[]" id="updateDetaisId_<? echo $i; ?>" value="0" />                        
            <input type="hidden" name="rollId[]" id="rollId_<? echo $i; ?>" value="<? echo $row[csf('roll_id')]; ?>" />
            <input type="hidden" name="rollNo[]" id="rollNo_<? echo $i; ?>" value="<? echo $row[csf('roll_no')]; ?>" />
            <input type="hidden" name="chkrollwgt[]" id="chkrollwgt_<? echo $i; ?>" value="<? echo $rollWeight; ?>" />
            <input type="hidden" name="challanNo[]" id="challanNo_<? echo $i; ?>" value="<? echo $row[csf('recv_number')]; ?>" />
            <input type="hidden" name="batchNo[]" id="batchNo_<? echo $i; ?>" value="<? echo $row['batch_no']; ?>" />
            <input type="hidden" name="batchId[]" id="batchId_<? echo $i; ?>" value="<? echo $row['batch_id']; ?>" />
            <input type="hidden" name="bodyPartId[]" id="bodyPartId_<? echo $i; ?>" value="<? echo $row[csf('body_part_id')]; ?>"/>
            <input type="hidden" name="colorId[]" id="colorId_<? echo $i; ?>" value="<? echo $row[csf('color_id')]; ?>" />
            <input type="hidden" name="deterId[]" id="deterId_<? echo $i; ?>" value="<? echo $row[csf('febric_description_id')]; ?>"/>
            <input type="hidden" name="productId[]" id="productId_<? echo $i; ?>" value="<? echo $row[csf('prod_id')]; ?>" />
            <input type="hidden" name="orderId[]" id="orderId_<? echo $i; ?>" value="<? echo $row['order_id']; ?>" />
            <input type="hidden" name="orderNo[]" id="orderNo_<? echo $i; ?>" value="<? echo $row['order_no']; ?>" />
            <input type="hidden" name="buyerId[]" id="buyerId_<? echo $i; ?>" value="<? echo $row['buyer_id']; ?>"/>  
            <input type="hidden" name="rollDia[]" id="rollDia_<? echo $i; ?>" value="<? echo $row[csf('width')]; ?>"/>
            <input type="hidden" name="rollGsm[]" id="rollGsm_<? echo $i; ?>" value="<? echo $row[csf('gsm')]; ?>"/>
            <input type="hidden" name="fabricId[]" id="fabricId_<? echo $i; ?>" value="<? echo $row[csf('dtls_id')]; ?>"/>
            <input type="hidden" name="receiveBasis[]" id="receiveBasis_<? echo $i; ?>" value="<? echo $receive_basis_id; ?>"/>
            <input type="hidden" name="knittingSource[]" id="knittingSource_<? echo $i; ?>" value="<? echo $row[csf('knitting_source')]; ?>"/>
            <input type="hidden" name="knittingComp[]" id="knittingComp_<? echo $i; ?>" value="<? echo $row[csf('knitting_company')]; ?>"/>
            <input type="hidden" name="jobNo[]" id="jobNo_<? echo $i; ?>" value="<? echo $row['job_no_full']; ?>"/>
            <input type="hidden" name="bookingNo[]" id="bookingNo_<? echo $i; ?>" value="<? echo $row[csf('booking_no')]; ?>"/>
            <input type="hidden" name="barcodNumber[]" id="barcodNumber_<? echo $i; ?>" value="<? echo $row[csf('barcode_no')]; ?>"/>
            <input type="hidden" name="exchangeRate[]" id="exchangeRate_<? echo $i; ?>"/>
			<input type="hidden" name="isSalesId[]" id="isSalesId_<? echo $i; ?>" value="<? echo $row[csf('is_sales')]; ?>"/>
        </tr>
        <?php
		$total_sum_qty+=$rollWeight;
	}
	?>
    <tfoot>
    <tr bgcolor="#99CCFF">
    <td colspan="10" align="right"> Total Sum </td>
     <td id="" align="right">   <input type="text" class="text_boxes_numeric" id="tot_sum" name="tot_sum"  style="width:60px;"  value="<? echo number_format($total_sum_qty,2);?> " readonly/> </td>
      <td colspan="10">&nbsp;    </td>
    </tr>
    </tfoot>
    
    <?
	die;
}

if($action=="load_drop_down_knitting_com")
{
	$data = explode("_",$data);
	$company_id=$data[1];
	
	if($data[0]==1)
	{
		echo create_drop_down( "cbo_knitting_company", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "--Select Knit Company--", "$company_id", "",1 );
	}
	else if($data[0]==3)
	{	
		echo create_drop_down( "cbo_knitting_company", 152, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in (21,24,25) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--Select Knit Company--", 1, "",1 );
		//echo "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in (21,24,25) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name";
	}
	else
	{
		echo create_drop_down( "cbo_knitting_company", 152, $blank_array,"",1, "--Select Knit Company--", 1, "",1 );
	}
	exit();
}

if($action=="load_php_form_booking")
{
	$ex_data=explode("_",$data);
	$booking_no=$ex_data[0];
	$comp=$ex_data[1];

	$sql=sql_select("select  a.id,a.booking_no,a.pay_mode, a.supplier_id,a.company_id from  wo_booking_mst a where a.booking_no='$booking_no' and a.status_active=1");
	foreach($sql as $val)
	{
		echo "document.getElementById('cbo_company_id').value  = '".($val[csf("company_id")])."';\n"; 
		$sql_result = sql_select("select distribute_qnty,auto_update from variable_settings_production where company_name = ".$val[csf("company_id")]." and variable_list = 51 and item_category_id=3 and is_deleted = 0 and status_active = 1");
		//echo "select distribute_qnty,auto_update from variable_settings_production where company_name = $comp and variable_list = 51 and item_category_id=3 and is_deleted = 0 and status_active = 1";
		foreach($sql_result as $val)
		{
			$auto_update=$val[csf("auto_update")];
			//echo $val[csf("distribute_qnty")].'DDD';
			if($auto_update==1)
			{
				$distribute_qnty=$val[csf("distribute_qnty")];
			}
			else
			{
				$distribute_qnty=0;
			}
		}
		echo "document.getElementById('aop_over_qty').value  = '".($distribute_qnty)."';\n"; 
	
		if($val[csf("pay_mode")]==3 || $val[csf("pay_mode")]==5)
		{
			echo "document.getElementById('cbo_knitting_source').value  = '1';\n"; 
			echo "load_drop_down( 'requires/aop_roll_receive_entry_controller', '1_".$val[csf("company_id")]."', 'load_drop_down_knitting_com', 'knitting_com');\n";
			echo "document.getElementById('cbo_knitting_company').value  = '".($val[csf("supplier_id")])."';\n"; 
		}
		else
		{
			echo "document.getElementById('cbo_knitting_source').value  = '1';\n";  
			echo "load_drop_down( 'requires/aop_roll_receive_entry_controller', '3_".$val[csf("company_id")]."', 'load_drop_down_knitting_com', 'knitting_com');\n";
			echo "document.getElementById('cbo_knitting_company').value  = '".($val[csf("supplier_id")])."';\n";   
		}
		//echo "document.getElementById('update_id').value  = '".($val[csf("id")])."';\n";
	}
}

if($action=="load_php_form")
{
	echo "select  a.id,a.recv_number_prefix_num,a.recv_number, a.challan_no,a.company_id,a.dyeing_source,a.dyeing_company,
    a.batch_no,a.receive_date
    from  inv_receive_mas_batchroll a
    where a.entry_form=63 and process_id in(31,35)  and  a.recv_number='$data'  ";
	
	$sql=sql_select("select  a.id,a.recv_number_prefix_num,a.recv_number, a.challan_no,a.company_id,a.dyeing_source,a.dyeing_company,
    a.batch_no,a.receive_date
    from  inv_receive_mas_batchroll a
    where a.entry_form=63 and process_id in(31,35)  and  a.recv_number='$data'  ");
	foreach($sql as $val)
	{
		echo "document.getElementById('cbo_company_id').value  = '".($val[csf("company_id")])."';\n"; 
		echo "document.getElementById('txt_batch_no').value  = '".($val[csf("batch_no")])."';\n"; 
		echo "document.getElementById('cbo_knitting_source').value  = '".($val[csf("dyeing_source")])."';\n"; 
		echo "load_drop_down( 'requires/aop_roll_receive_entry_controller', '".$val[csf("dyeing_source")]."_".$val[csf("company_id")]."', 'load_drop_down_knitting_com', 'knitting_com');\n"; 
		echo "document.getElementById('cbo_knitting_company').value  = '".($val[csf("dyeing_company")])."';\n";  
		echo "document.getElementById('update_id').value  = '".($val[csf("id")])."';\n";
	}
}

if($action=="woorder_rate_popup")
{
	echo load_html_head_contents("Challan Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?> 
	<script>
		function js_set_value(rate,currency_id,exchange_rate)
		{
		
			$('#hidden_rate').val(rate);
			$('#hidden_currency_id').val(currency_id);
			$('#hidden_exchange_rate').val(exchange_rate);
			parent.emailwindow.hide();
		}
    </script>
</head>
<body>
<div align="center" style="width:760px;" >
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:760px; margin-left:2px">
		<legend>Receive Number Popup</legend>           
            <table cellpadding="0" cellspacing="0" width="750" border="1" rules="all" class="rpt_table" align="center">
                <thead>
                	<th  width="30">Sl</th>
                    <th  width="100">Booking No</th>
                    <th  width="140">Body Part</th>
                    <th id="" width="250">Construction/ Compositon</th>
                    <th id="100" width="">Rate
                    	<input type="hidden" name="hidden_rate" id="hidden_rate">  
                        <input type="hidden" name="hidden_currency_id" id="hidden_currency_id">
                        <input type="hidden" name="hidden_exchange_rate" id="hidden_exchange_rate">  
                    </th>
                    <th  width="">Currency</th>
                </thead>
                <tbody>
				<?php
				$composition_arr=array();
				$constructtion_arr=array();
				$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=$determinationId";
				$data_array=sql_select($sql_deter);
				foreach( $data_array as $row )
				{
					$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
					$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
				}

                $sql="select sum(a.amount)/sum(a.wo_qnty) as rate ,a.booking_no ,b.currency_id,b.exchange_rate,a.description  from   wo_booking_dtls a,wo_booking_mst b,wo_pre_cost_fab_conv_cost_dtls c,wo_pre_cost_fabric_cost_dtls  d where b.booking_no=a.booking_no and a.job_no=c.job_no and c.fabric_description=d.id  and a.process=35 and a.wo_qnty>0 and  a.job_no='$job_no' and d.body_part_id=$bodyPart and d.lib_yarn_count_deter_id=$determinationId group by  a.booking_no ,b.currency_id,b.exchange_rate,a.description";
				$sql_result=sql_select($sql);
				$i=1;
                foreach($sql_result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                ?>
               		<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('rate')]; ?>','<? echo $row[csf('currency_id')]; ?>','<? echo $row[csf('exchange_rate')]; ?>');">
                        <td width="30"><? echo $i; ?></td>
                        <td width="100" style="word-break:break-all;"><? echo $row[csf('booking_no')]; ?></td>
                        <td width="140"><? echo $body_part[$bodyPart]; ?></td>
                        <td width="250"><? echo $constructtion_arr[$determinationId].",".$composition_arr[$determinationId]; ?></td>
                        <td width="100" style="word-break:break-all;" align="right"><? echo number_format($row[csf('rate')],2); ?></td>
                        <td width="" style="" align="center"><? echo $currency[$row[csf('currency_id')]]; ?></td>
                	</tr>
                <?php 
					$i++;
				}
				?>
                </tbody>
           </table>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="grey_delivery_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	
	$company=$data[0];
	$txt_challan_no=$data[1];
	$update_id=$data[2];

	$company_array=array();
	$company_data=sql_select("select id, company_name, company_short_name from lib_company");
	foreach($company_data as $row)
	{
		$company_array[$row[csf('id')]]['shortname']=$row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name']=$row[csf('company_name')];
	}

	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$supplier_arr = return_library_array("select id, short_name from lib_supplier","id","short_name");
	$buyer_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details=return_library_array( "select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	$machine_details=return_library_array( "select id, machine_no from lib_machine_name", "id", "machine_no");
	$brand_details=return_library_array( "select id, brand_name from lib_brand", "id", "brand_name");
	
	$mstData=sql_select("select company_id, delevery_date, knitting_source, knitting_company from pro_grey_prod_delivery_mst where id=$update_id");
	
	$job_array=array();
	$job_sql="select a.job_no_prefix_num, a.job_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$job_sql_result=sql_select($job_sql);
	foreach($job_sql_result as $row)
	{
		$job_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
		$job_array[$row[csf('id')]]['po']=$row[csf('po_number')];
	}
	
	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		if(array_key_exists($row[csf('id')],$composition_arr))
		{
			$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
		}
		else
		{
			$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
		}
	}
	
?>
    <div style="width:1330px;">
    	<table width="1330" cellspacing="0" align="center" border="0">
			<tr>
				<td align="center" style="font-size:x-large"><strong><? echo $company_array[$company]['name']; ?></strong></td>
			</tr>
			<tr>
				<td align="center" style="font-size:18px"><strong><u>Delivery Challan</u></strong></td>
			</tr>
			<tr>
				<td align="center" style="font-size:16px"><strong><u>Knitting Section</u></strong></td>
			</tr>
        </table> 
        <br>
		<table width="1330" cellspacing="0" align="center" border="0">
			<tr>
				<td style="font-size:16px; font-weight:bold;">Challan No</td>
                <td width="170">:&nbsp;<? echo $txt_challan_no; ?></td>
                <td width="1000" id="barcode_img_id" align="right"></td>
			</tr>
            <tr>
				<td style="font-size:16px; font-weight:bold;">Delivery Date </td>
                <td colspan="2" width="1170">:&nbsp;<? echo change_date_format($mstData[0][csf('delevery_date')]); ?></td>
			</tr>
		</table>
        <br>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1330" class="rpt_table" >
            <thead>
                <tr>
                    <th width="30">SL</th>
                    <th width="90">Order No</th>
                    <th width="60">Buyer <br> Job</th>
                    <th width="50">System ID</th>
                    <th width="100">Prog. / Booking No</th>
                    <th width="80">Production Basis</th>
                    <th width="70">Knitting Source</th>
                    <th width="70">Knitting Company</th>
                    <th width="50">Yarn Count</th>
                    <th width="70">Yarn Brand</th>
                    <th width="60">Lot No</th>
                    <th width="70">Fab Color</th>
                    <th width="70">Color Range</th>
                    <th width="150">Fabric Type</th>
                    <th width="50">Stich</th>
                    <th width="50">Fin GSM</th>
                    <th width="40">Fab. Dia</th>
                    <th width="40" >M/C Dia</th>
                    <th width="40">Roll No</th>
                    <th>Qty</th>
                </tr>
            </thead>
            <?
				$i=1; $tot_qty=0; $receive_basis=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan");
            	$sql="SELECT a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no, a.knitting_source, a.knitting_company, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, c.barcode_no, c.roll_no, c.po_breakdown_id, d.current_delivery FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, pro_grey_prod_delivery_dtls d WHERE a.id=b.mst_id and b.id=c.dtls_id and c.id=d.roll_id and d.entry_form=56 and d.mst_id=$update_id and a.entry_form=2 and c.entry_form=2 and d.status_active=1 and d.is_deleted=0";
				$result=sql_select($sql);
				foreach($result as $row)
				{
					$knit_company="&nbsp;";
					if($row[csf("knitting_source")]==1)
					{
						$knit_company=$company_array[$row[csf("knitting_company")]]['shortname'];
					}
					else if($row[csf("knitting_source")]==3)
					{
						$knit_company=$supplier_arr[$row[csf("knitting_company")]];
					}
					
					$count='';
					$yarn_count=explode(",",$row[csf('yarn_count')]);
					foreach($yarn_count as $count_id)
					{
						if($count=='') $count=$yarn_count_details[$count_id]; else $count.=",".$yarn_count_details[$count_id];
					}
				?>
                	<tr>
                        <td width="30"><? echo $i; ?></td>
                        <td width="90" style="word-break:break-all;"><? echo $job_array[$row[csf('po_breakdown_id')]]['po']; ?></td>
                        <td width="60"><? echo $buyer_array[$row[csf('buyer_id')]]."<br>".$job_array[$row[csf('po_breakdown_id')]]['job']; ?></td>
                        <td width="50"><? echo $row[csf('recv_number_prefix_num')]; ?></td>
                        <td width="100" style="word-break:break-all;"><? echo $row[csf('booking_no')]; ?></td>
                        <td width="80"><? echo $receive_basis[$row[csf('receive_basis')]]; ?></td>
                        <td width="70" style="word-break:break-all;"><? echo $knitting_source[$row[csf("knitting_source")]]; ?></td>
                        <td width="70"><? echo $knit_company; ?></td>
                        <td width="50" style="word-break:break-all;"><? echo $count; ?></td>
                        <td width="70" style="word-break:break-all;"><? echo $brand_details[$row[csf("brand_id")]]; ?></td>
                        <td width="60" style="word-break:break-all;"><? echo $row[csf('yarn_lot')]; ?></td>
                        <td width="70" style="word-break:break-all;"><? echo $color_arr[$row[csf("color_id")]]; ?></td>
                        <td width="70" style="word-break:break-all;"><? echo $color_range[$row[csf("color_range_id")]]; ?></td>
                        <td width="150" style="word-break:break-all;"><? echo $composition_arr[$row[csf('febric_description_id')]]; ?></td>
                        <td width="50" style="word-break:break-all;"><? echo $row[csf('stitch_length')]; ?></td>
                        <td width="50" style="word-break:break-all;"><? echo $row[csf('gsm')]; ?></td>
                        <td width="40" style="word-break:break-all;"><? echo $row[csf('width')]; ?></td>
                        <td width="40" style="word-break:break-all;"><? echo $machine_details[$row[csf('machine_no_id')]]; ?></td>
                        <td width="40"><? echo $row[csf('roll_no')]; ?></td>


                        <td align="right"><? echo number_format($row[csf('current_delivery')],2); ?></td>
                    </tr>
                <?
					$tot_qty+=$row[csf('current_delivery')];
					$i++;
				}
			?>
            <tr> 
                <td align="right" colspan="19"><strong>Total</strong></td>
                <td align="right"><? echo number_format($tot_qty,2,'.',''); ?></td>
			</tr>
            <tr>
                <td colspan="2" align="left"><b>Remarks:</b></td>
                <td colspan="18">&nbsp;</td>
            </tr>
		</table>
	</div>
    <? echo signature_table(44, $company, "1330px"); ?>
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
		generateBarcode('<? echo $txt_challan_no; ?>');
	</script>
<?
exit();
}
?>