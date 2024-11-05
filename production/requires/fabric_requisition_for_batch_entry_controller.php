<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "" );
	exit();
}

if ($action=="po_popup")
{
	echo load_html_head_contents("Fabric Info", "../../", 1, '','','','');
	extract($_REQUEST);
?> 
	<script>
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function check_all_data(is_checked)
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ ) {

				if($("#search"+i).is(":visible")) {
				
					if(is_checked==true)
					{
						document.getElementById( 'search' + i ).style.backgroundColor='yellow';
					}
					else
					{
						document.getElementById( 'search' + i ).style.backgroundColor='#FFFFCC';	
					}
				}
			}
		}
		
		function js_set_value( str) 
		{
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
		}
		
		function reset_hide_field()
		{
			$('#hidden_data').val( '' );
		}

		function set_receive_basis(recieve_basis)
		{
			$('#txt_search_common').val('');
			$('#txt_search_common').removeAttr('disabled','disabled');
			
			if(recieve_basis == 1)
			{
				$('#td_caption').text('Enter Program No');	
			}
			else if(recieve_basis == 2)
			{
				$('#td_caption').text('Enter Booking No');	
			}
			else if(recieve_basis == 3)
			{
				$('#td_caption').text('Enter PI No');	
			}
			else
			{
				$('#td_caption').text('');	
				$('#txt_search_common').attr('disabled','disabled');
			}
		}
		
		function set_search_by(type)
		{
			$('#txt_search_val').val('');
			
			if(type == 1)
			{
				$('#td_search').text('Enter Job No');	
			}
			else if(type == 2)
			{
				$('#td_search').text('Enter Order No');	
			}
			else if(type == 3)
			{
				$('#td_search').text('Enter File No');	
			}
			else if(type == 4)
			{
				$('#td_search').text('Enter Ref. No');	
			}
			else
			{
				$('#td_search').text('Enter Style Ref');	
			}
		}
		
		function fnc_close()
		{
			var hidden_data='';
			
			$("#tbl_list_search").find('tr:not(:first)').each(function()
			{
				var tr_id = $(this).attr("id");
				//var bgColor=$(this).css("background-color");bg
				var bgColor=document.getElementById(tr_id).style.backgroundColor;
				if(bgColor=='yellow')
				{
					var buyerId=$(this).find('input[name="buyerId[]"]').val();
					var programBookingId=$(this).find('input[name="programBookingId[]"]').val();
					var poId=$(this).find('input[name="poId[]"]').val();
					var deterId=$(this).find('input[name="deterId[]"]').val();
					var colorId=$(this).find('input[name="colorId[]"]').val();
					var countId=$(this).find('input[name="countId[]"]').val();
					var reqQty=$(this).find('input[name="reqQty[]"]').val();
					var receiveBasisId=$(this).find('input[name="receiveBasisId[]"]').val();
					var receiveBasis=$(this).find('input[name="receiveBasis[]"]').val();
					var progBookNo=$(this).find('input[name="progBookNo[]"]').val();
					var totReqnQty=$(this).find('input[name="totReqnQty[]"]').val();
					var balance=$(this).find('input[name="balance[]"]').val();

					var data='';
					$(this).find('td:not(:first-child)').each (function() 
					{
						data+="**"+$(this).text();
					});
					
					if(hidden_data=="")
					{
						hidden_data=buyerId+"**"+poId+"**"+programBookingId+"**"+deterId+"**"+colorId+"**"+countId+"**"+reqQty+"**"+receiveBasisId+"**"+receiveBasis+data+"**"+progBookNo+"**"+totReqnQty+"**"+balance;
					}
					else
					{
						hidden_data+="_"+buyerId+"**"+poId+"**"+programBookingId+"**"+deterId+"**"+colorId+"**"+countId+"**"+reqQty+"**"+receiveBasisId+"**"+receiveBasis+data+"**"+progBookNo+"**"+totReqnQty+"**"+balance;
					}
					//alert(hidden_data);
				}
			});
			
			$('#hidden_data').val( hidden_data );
			parent.emailwindow.hide();
		}
    </script>

</head>

<body>
<div align="center" style="width:1135px;">
	<form name="searchwofrm"  id="searchwofrm" autocomplete=off>
		<fieldset style="width:1130px; margin-left:2px">
		<legend>Enter search words</legend>           
            <table cellpadding="0" cellspacing="0" border="1" rules="all" width="800" class="rpt_table">
                <thead>
                    <th>Buyer</th>
                    <th>Basis</th>
                    <th id="td_caption">Enter Program No</th>
                    <th>Search Type</th>
                    <th id="td_search">Enter Job No</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                    	<input type="hidden" name="hidden_data" id="hidden_data" class="text_boxes" value=""> 
                    </th> 
                </thead>
                <tr class="general">
                	<td>
                    	<?
							echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",$data[0] ); 
						?>       
                    </td>
                	<td>	
                    	<?
							$basis_arr=array(1=>"Program Based",2=>"Booking Based",3=>"PI Based",4=>"Others");
							echo create_drop_down( "cbo_receive_basis", 130, $basis_arr,"",0, "-- Select --","","set_receive_basis(this.value)",0 );
						?>
                    </td>
                    <td>
                        <input type="text" style="width:100px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                    </td>
                    <td>
                    	<?
							$search_by_arr=array(1=>"Job",2=>"Order",3=>"File",4=>"Ref. No",5=>"Style Ref");
							echo create_drop_down( "cbo_search_by", 90, $search_by_arr,"",0, "--Select--","","set_search_by(this.value)",0 );
						?>			
                    </td>
                    <td>				
                        <input type="text" name="txt_search_val" id="txt_search_val" style="width:100px" class="text_boxes" />	
                    </td> 						
            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( <? echo $company_id; ?>+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_receive_basis').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_val').value, 'create_fabric_search_list_view', 'search_div', 'fabric_requisition_for_batch_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')" style="width:100px;" />
                     </td>
                </tr>
			</table>
			<div style="margin-top:10px;" id="search_div" align="left"></div> 
		</fieldset>
	</form>
</div>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_fabric_search_list_view")
{
	$data = explode("_",$data);
	
	$company_id =$data[0];
	$buyer_id =$data[1];
	$recieve_basis=$data[2];
	$search_string=trim($data[3]);
	$search_type=trim($data[4]);
	$search_val=trim($data[5]);
	
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	//$yarncount=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$basis_arr=array(1=>"Program Based",2=>"Booking Based",3=>"PI Based",4=>"Others");
	
	if($buyer_id==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_id=$buyer_id";
	}
	
	$composition_arr=array(); $constructtion_arr=array();
 	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	
	if($db_type==0) $null_cond="IFNULL"; else $null_cond="NVL";
	
	$reqn_qnty_array=array();
	$reqnData=sql_select("select receive_basis, program_booking_pi_id, po_id, prod_id, determination_id, $null_cond(color_id,0) as color_id, sum(reqn_qty) as qnty from pro_fab_reqn_for_batch_dtls where status_active=1 and is_deleted=0 group by receive_basis, program_booking_pi_id, po_id, prod_id, determination_id, color_id");
	foreach($reqnData as $row)
	{
		$reqn_qnty_array[$row[csf('receive_basis')]][$row[csf('program_booking_pi_id')]][$row[csf('po_id')]][$row[csf('prod_id')]][$row[csf('determination_id')]][$row[csf('color_id')]]=$row[csf('qnty')];
	}
	
	$search_cond="";
	if ($search_type==1)
	{
		if($search_val!="") $search_cond.="and d.job_no_mst like '%".$search_val."'";
	}
	else if ($search_type==2)
	{
		if($search_val!="") $search_cond.="and d.po_number like '%".$search_val."%'";
	}
	else if ($search_type==3)
	{
		if($search_val!="") $search_cond.="and d.file_no like '%".$search_val."%'";
	}
	else if ($search_type==4)
	{
		if($search_val!="") $search_cond.="and d.grouping like '%".$search_val."%'";
	}else if ($search_type==5)
	{
		if($search_val!="") $search_cond.="and e.style_ref_no like '%".$search_val."%'";
	}
	
	if($recieve_basis==1)
	{
		$program_qnty_array=array(); $program_bookingNo_array=array();
		$programData=sql_select("select po_id, booking_no, dtls_id, determination_id, gsm_weight, dia, sum(program_qnty) as qnty from ppl_planning_entry_plan_dtls where status_active=1 and is_deleted=0 group by po_id, dtls_id, determination_id, gsm_weight, dia, booking_no");
		foreach($programData as $row)
		{
			$program_qnty_array[$row[csf('po_id')]][$row[csf('dtls_id')]][$row[csf('determination_id')]][$row[csf('gsm_weight')]][$row[csf('dia')]]=$row[csf('qnty')];
			$program_bookingNo_array[$row[csf('dtls_id')]]=$row[csf('booking_no')];
		}
		
		if($search_string!="") $program_cond="and a.booking_id=$search_string"; else $program_cond="";
		
		$sql = "SELECT a.company_id, a.buyer_id, a.booking_id, a.booking_no, b.prod_id, b.febric_description_id, b.gsm, b.width, $null_cond(b.color_id,0) as color_id, c.po_breakdown_id, d.job_no_mst, d.po_number, d.file_no, d.grouping, d.shipment_date, e.style_ref_no FROM inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, wo_po_break_down d, wo_po_details_master e WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and d.job_id=e.id and a.entry_form=2 and c.entry_form=2 and a.company_id=$company_id and a.receive_basis=2 and c.status_active=1 and c.is_deleted=0 $program_cond $search_cond $buyer_id_cond group by a.company_id, a.buyer_id, a.booking_id, a.booking_no, b.prod_id, b.febric_description_id, b.gsm, b.width, $null_cond(b.color_id,0), c.po_breakdown_id, d.job_no_mst, d.po_number, d.file_no, d.grouping, d.shipment_date, e.style_ref_no order by a.booking_id"; 
		//echo $sql;//die;
	}
	else if($recieve_basis==2)
	{
		$booking_qnty_array=array(); $samp_booking_qnty_array=array();
		$bookingData=sql_select("select a.po_break_down_id, a.booking_no, b.lib_yarn_count_deter_id as deter_id, b.gsm_weight, a.dia_width, sum(a.grey_fab_qnty) as qnty from wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b where a.pre_cost_fabric_cost_dtls_id=b.id and a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 group by a.po_break_down_id, a.booking_no, b.lib_yarn_count_deter_id, b.gsm_weight, a.dia_width");
		foreach($bookingData as $row)
		{
			$booking_qnty_array[$row[csf('po_break_down_id')]][$row[csf('booking_no')]][$row[csf('deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]=$row[csf('qnty')];
		}
		
		if($search_string!="") $booking_cond="and a.booking_no like '%".$search_string."'"; else $booking_cond="";
		if($search_type==0)
		{
			$sampBookingData=sql_select("select a.booking_no, a.lib_yarn_count_deter_id as deter_id, a.gsm_weight, a.dia_width, sum(a.grey_fabric) as qnty from wo_non_ord_samp_booking_dtls a, wo_non_ord_samp_booking_mst b where a.booking_no=b.booking_no and b.item_category=2 and a.status_active=1 and a.is_deleted=0 group by a.booking_no, a.lib_yarn_count_deter_id, a.gsm_weight, a.dia_width");
			foreach($sampBookingData as $row)
			{
				$samp_booking_qnty_array[$row[csf('booking_no')]][$row[csf('deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]=$row[csf('qnty')];
			}
			
			$sql = "SELECT a.company_id, a.buyer_id, a.booking_id, a.booking_no, b.prod_id, b.febric_description_id, b.gsm, b.width, $null_cond(b.color_id,0) as color_id, c.po_breakdown_id, d.job_no_mst, d.po_number, d.file_no, d.grouping, d.shipment_date, 0 as type FROM inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, wo_po_break_down d WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.receive_basis = (CASE WHEN a.entry_form = 2 THEN 1 WHEN a.entry_form = 22 THEN 2 ELSE null END) and a.company_id=$company_id and a.booking_without_order=0 and c.status_active=1 and c.is_deleted=0 $booking_cond $buyer_id_cond group by a.company_id, a.buyer_id, a.booking_id, a.booking_no, b.prod_id, b.febric_description_id, b.gsm, b.width, $null_cond(b.color_id,0), c.po_breakdown_id, d.job_no_mst, d.po_number, d.file_no, d.grouping, d.shipment_date
			union all
			SELECT a.company_id, a.buyer_id, a.booking_id, a.booking_no, b.prod_id, b.febric_description_id, b.gsm, b.width, $null_cond(b.color_id,0) as color_id, 0 as po_breakdown_id, null as job_no_mst, null as po_number, null as file_no, null as grouping, null as shipment_date, 1 as type FROM inv_receive_master a, pro_grey_prod_entry_dtls b WHERE a.id=b.mst_id and a.receive_basis = (CASE WHEN  a.entry_form = 2 THEN 1 WHEN  a.entry_form = 22 THEN 2  ELSE null END) and a.company_id=$company_id and a.booking_without_order=1 $booking_cond $buyer_id_cond group by a.company_id, a.buyer_id, a.booking_id, a.booking_no, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_lot, b.yarn_count, $null_cond(b.color_id,0) order by type, booking_id
			";
		}
		else
		{
			//if($job_no!="") $job_cond="and d.job_no_mst like '%".$job_no."'"; else $job_cond="";
			
			$sql = "SELECT a.company_id, a.buyer_id, a.booking_id, a.booking_no, b.prod_id, b.febric_description_id, b.gsm, b.width, $null_cond(b.color_id,0) as color_id, c.po_breakdown_id, d.job_no_mst, d.po_number, d.file_no, d.grouping, d.shipment_date, 0 as type FROM inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, wo_po_break_down d WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.receive_basis = (CASE WHEN a.entry_form = 2 THEN 1 WHEN a.entry_form = 22 THEN 2 ELSE null END) and a.company_id=$company_id and a.booking_without_order=0 and c.status_active=1 and c.is_deleted=0 $booking_cond $search_cond $buyer_id_cond group by a.company_id, a.buyer_id, a.booking_id, a.booking_no, b.prod_id, b.febric_description_id, b.gsm, b.width, $null_cond(b.color_id,0), c.po_breakdown_id, d.job_no_mst, d.po_number, d.file_no, d.grouping, d.shipment_date order by a.booking_id";
		}
		//echo $sql;die;
	}
	else if($recieve_basis==3)
	{
		if($search_string!="") $program_cond="and a.booking_no like '%".$search_string."%'"; else $program_cond="";
		//if($job_no!="") $job_cond="and d.job_no_mst like '%".$job_no."'"; else $job_cond="";
		
		$sql = "SELECT a.company_id, a.buyer_id, a.booking_id, a.booking_no, b.prod_id, b.febric_description_id, b.gsm, b.width, $null_cond(b.color_id,0) as color_id, c.po_breakdown_id, d.job_no_mst, d.po_number, d.file_no, d.grouping, d.shipment_date FROM inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, wo_po_break_down d WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.entry_form=22 and c.entry_form=22 and a.company_id=$company_id and a.receive_basis=1 and c.status_active=1 and c.is_deleted=0 $program_cond $search_cond $buyer_id_cond group by a.company_id, a.buyer_id, a.booking_id, a.booking_no, b.prod_id, b.febric_description_id, b.gsm, b.width, $null_cond(b.color_id,0), c.po_breakdown_id, d.job_no_mst, d.po_number, d.file_no, d.grouping, d.shipment_date order by a.booking_id"; 
		//echo $sql;//die;
	}
	else
	{
		//if($job_no!="") $job_cond="and d.job_no_mst like '%".$job_no."'"; else $job_cond="";
		$sql = "SELECT a.company_id, a.buyer_id, a.booking_id, a.booking_no, b.prod_id, b.febric_description_id, b.gsm, b.width, $null_cond(b.color_id,0) as color_id, c.po_breakdown_id, d.job_no_mst, d.po_number, d.file_no, d.grouping, d.shipment_date FROM inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, wo_po_break_down d WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.receive_basis in(0,4,6) and a.company_id=$company_id and a.booking_without_order=0 and c.status_active=1 and c.is_deleted=0 $booking_cond $search_cond $buyer_id_cond group by a.company_id, a.buyer_id, a.booking_id, a.booking_no, b.prod_id, b.febric_description_id, b.gsm, b.width, $null_cond(b.color_id,0), c.po_breakdown_id, d.job_no_mst, d.po_number, d.file_no, d.grouping, d.shipment_date order by a.booking_id";
	}

	// echo $sql;
	$result = sql_select($sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1220" class="rpt_table">
		<thead>
			<th width="25">SL</th>
            <? 
				if($recieve_basis==1) $recv_basis='Prog. No';
				else if($recieve_basis==2) $recv_basis='Book. No'; 
				else if($recieve_basis==3) $recv_basis='PI No'; 
				else $recv_basis=''; 
			?>
            <th width="90"><? echo $recv_basis; ?></th>
			<th width="60">Buyer</th>
			<th width="80">Job No</th>
			<th width="80">Style Ref </th>
			<th width="90">Order No</th>
			<th width="90">Shipment Date</th>
			<th width="100">Construction</th>
			<th width="130">Composition</th>
			<th width="50">GSM</th>
			<th width="50">Dia</th>
			<th width="80">Color</th>
			<th width="50">Prod. Id</th>
            <th width="80">File No</th>
            <th>Ref. No</th>
		</thead>
	</table>
	<div style="width:1220px; max-height:250px; overflow-y:scroll" id="list_container_batch" align="left">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1200" class="rpt_table" id="tbl_list_search">  
		<?
			$i=1;
			foreach ($result as $row)
			{  
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
				$color='';
				$color_id=array_unique(explode(',',$row[csf('color_id')]));
				foreach($color_id as $id)
				{
					if($id>0)
					{
						if($color=='') $color=$color_arr[$id]; else $color.=", ".$color_arr[$id];
					}
				}		
				
				$reqQty=0; $progBookNo=''; 
				if($recieve_basis==1)
				{
					//print_r($program_qnty_array[$row[csf('po_breakdown_id')]][$row[csf('booking_id')]]);
					$progBookNo=$program_bookingNo_array[$row[csf('booking_id')]];
					$reqQty=$program_qnty_array[$row[csf('po_breakdown_id')]][$row[csf('booking_id')]][$row[csf('febric_description_id')]][$row[csf('gsm')]][$row[csf('width')]];	
				}
				elseif($recieve_basis==2)
				{
					if($row[csf('type')]==0)
					{
						$reqQty=$booking_qnty_array[$row[csf('po_breakdown_id')]][$row[csf('booking_no')]][$row[csf('febric_description_id')]][$row[csf('gsm')]][$row[csf('width')]];
					}
					else
					{
						$reqQty=$samp_booking_qnty_array[$row[csf('booking_no')]][$row[csf('febric_description_id')]][$row[csf('gsm')]][$row[csf('width')]];
					}
				} 
				
				$totReqnQty=$reqn_qnty_array[$recieve_basis][$row[csf('booking_id')]][$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('febric_description_id')]][$row[csf('color_id')]];
				
				$reqQty=number_format($reqQty,2,'.','');
				$totReqnQty=number_format($totReqnQty,2,'.','');
				$balance=number_format($reqQty-$totReqnQty,2,'.','');
				
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)"> 
					<td width="25">
						<? echo $i; ?>
						<input type="hidden" name="buyerId[]" id="buyerId<? echo $i; ?>" value="<? echo $row[csf('buyer_id')]; ?>"/>
						<input type="hidden" name="poId[]" id="poId<? echo $i; ?>" value="<? echo $row[csf('po_breakdown_id')]; ?>"/>
						<input type="hidden" name="deterId[]" id="deterId<? echo $i; ?>" value="<? echo $row[csf('febric_description_id')]; ?>"/>
						<input type="hidden" name="colorId[]" id="colorId<? echo $i; ?>" value="<? echo $row[csf('color_id')]; ?>"/>
						<input type="hidden" name="countId[]" id="countId<? echo $i; ?>" value="<? //echo $row[csf('yarn_count')]; ?>"/>
						<input type="hidden" name="reqQty[]" id="reqQty<? echo $i; ?>" value="<? echo $reqQty; ?>"/>
                        <input type="hidden" name="programBookingId[]" id="programBookingId<? echo $i; ?>" value="<? echo $row[csf('booking_id')]; ?>"/>
                        <input type="hidden" name="receiveBasisId[]" id="receiveBasisId<? echo $i; ?>" value="<? echo $recieve_basis; ?>"/>
                        <input type="hidden" name="receiveBasis[]" id="receiveBasis<? echo $i; ?>" value="<? echo $basis_arr[$recieve_basis]; ?>"/>
                        <input type="hidden" name="progBookNo[]" id="progBookNo<? echo $i; ?>" value="<? echo $progBookNo; ?>"/>
                        <input type="hidden" name="totReqnQty[]" id="totReqnQty<? echo $i; ?>" value="<? echo $totReqnQty; ?>"/>
                        <input type="hidden" name="balance[]" id="balance<? echo $i; ?>" value="<? echo $balance; ?>"/>
                        <input type="hidden" name="style_ref[]" id="style_ref<? echo $i; ?>" value="<? echo $row[csf('style_ref_no')]; ?>"/>
					</td>
					<td width="90"><div style="word-wrap:break-word; width:90px"><? echo $row[csf('booking_no')]; ?></div></td>
					<td width="60"><div style="word-wrap:break-word; width:60px"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></div></td>
					<td width="80"><div style="word-wrap:break-word; width:80px"><? echo $row[csf('job_no_mst')]; ?></div></td>
					<td width="80"><div style="word-wrap:break-word; width:80px"><? echo $row[csf('style_ref_no')]; ?></div></td>
					<td width="90"><div style="word-wrap:break-word; width:90px"><? echo $row[csf('po_number')]; ?></div></td>
					<td width="90" align="center"><div style="word-wrap:break-word;"><?=change_date_format($row["SHIPMENT_DATE"]);?></div></td>
					<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $constructtion_arr[$row[csf('febric_description_id')]]; ?></div></td>
					<td width="130"><div style="word-wrap:break-word; width:130px"><? echo $composition_arr[$row[csf('febric_description_id')]]; ?></div></td>
					<td width="50"><div style="word-wrap:break-word; width:50px"><? echo $row[csf('gsm')]; ?></div></td>
					<td width="50"><div style="word-wrap:break-word; width:50px"><? echo $row[csf('width')]; ?></div></td>
					<td width="80"><div style="word-wrap:break-word; width:80px"><? echo $color; ?>&nbsp;</div></td>
					<td width="50"><div style="word-wrap:break-word; width:50px"><? echo $row[csf('prod_id')]; ?></div></td>
					<td width="80"><div style="word-wrap:break-word; width:80px"><? echo $row[csf('file_no')]; ?></div></td>
					<td><div style="word-wrap:break-word; width:75px"><? echo $row[csf('grouping')]; ?></div></td>
				</tr>
			<?
			$i++;
			}
			?>
		</table>
	</div>
    <!--<td width="70"><div style="word-wrap:break-word; width:70px"><?echo $yarn_count; ?></div></td>
					<td width="60"><div style="word-wrap:break-word; width:60px"><?echo $row[csf('yarn_lot')]; ?></div></td>-->
	<table width="900" cellspacing="0" cellpadding="0" border="1" align="center">
		<tr>
			<td align="center" height="30" valign="bottom">
				<div style="width:100%"> 
					<div style="width:45%; float:left" align="left">
						<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data(this.checked)" /> Check / Uncheck All
					</div>
					<div style="width:55%; float:left" align="left">
						<input type="button" name="close" onClick="fnc_close();" class="formbutton" value="Close" style="width:100px" />
					</div>
				</div>
			</td>
		</tr>
	</table>
	<?	
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	if ($operation==0)  // Insert Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if($db_type==0) $year_cond="YEAR(insert_date)"; 
		else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
		else $year_cond="";//defined Later
		
		$new_mrr_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'FRB', date("Y",time()), 5, "select reqn_number_prefix, reqn_number_prefix_num from pro_fab_reqn_for_batch_mst where company_id=$cbo_company_id and $year_cond=".date('Y',time())." order by id desc ", "reqn_number_prefix","reqn_number_prefix_num"));
		$id=return_next_id( "id", "pro_fab_reqn_for_batch_mst", 1 ) ;
				 
		$field_array="id,reqn_number_prefix,reqn_number_prefix_num,reqn_number,company_id,location_id,reqn_date,inserted_by,insert_date";
		$data_array="(".$id.",'".$new_mrr_number[1]."',".$new_mrr_number[2].",'".$new_mrr_number[0]."',".$cbo_company_id.",".$cbo_location_name.",".$txt_requisition_date.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		$field_array_dtls="id, mst_id, receive_basis, program_booking_pi_no, program_booking_pi_id, po_id, buyer_id, job_no, prod_id, determination_id, lot, count, color_id, reqn_qty, remarks, inserted_by, insert_date";
		$dtls_id = return_next_id( "id", "pro_fab_reqn_for_batch_dtls", 1 );

		for($j=1;$j<=$tot_row;$j++)
		{ 	
			$receiveBasis="receiveBasis".$j;
			$poId="poId".$j;
			$buyerId="buyerId".$j;
			$job="job".$j;
			$programNo="programNo".$j;
			$bookingNo="bookingNo".$j;
			$programBookingId="programBookingId".$j;
			$prodId="prodId".$j;
			$deterId="deterId".$j;
			$colorId="colorId".$j;
			$lot="lot".$j;
			$countId="countId".$j;
			$reqsnQty="reqsnQty".$j;
			$remarks="remarks".$j;
			
			if($$receiveBasis==1) $program_booking_pi_no=$$programNo; else $program_booking_pi_no=$$bookingNo;

			if($data_array_dtls!="") $data_array_dtls.=",";
			$data_array_dtls.="(".$dtls_id.",".$id.",".$$receiveBasis.",'".$program_booking_pi_no."','".$$programBookingId."','".$$poId."','".$$buyerId."','".$$job."','".$$prodId."','".$$deterId."','".$$lot."','".$$countId."','".$$colorId."','".$$reqsnQty."','".$$remarks."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			$dtls_id = $dtls_id+1;
		}
		
		//echo "10**insert into pro_fab_reqn_for_batch_mst (".$field_array.") values ".$data_array;oci_rollback($con);die;
		$rID=sql_insert("pro_fab_reqn_for_batch_mst",$field_array,$data_array,0);
		$rID2=sql_insert("pro_fab_reqn_for_batch_dtls",$field_array_dtls,$data_array_dtls,1);
		//oci_rollback($con);
		//echo "10**".$rID."&&".$rID2;die;

		if($db_type==0)
		{
			if($rID && $rID2)
			{
				mysql_query("COMMIT");  
				echo "0**".$id."**".$new_mrr_number[0];
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2)
			{
				oci_commit($con);  
				echo "0**".$id."**".$new_mrr_number[0];
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
		
		$field_array="company_id*location_id*reqn_date*updated_by*update_date";
		$data_array=$cbo_company_id."*".$cbo_location_name."*".$txt_requisition_date."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$field_array_dtls="id, mst_id, receive_basis, program_booking_pi_no, program_booking_pi_id, po_id, buyer_id, job_no, prod_id, determination_id, lot, count, color_id, reqn_qty, remarks, inserted_by, insert_date";
		$field_array_update="reqn_qty*remarks*updated_by*update_date";
		$dtls_id = return_next_id( "id", "pro_fab_reqn_for_batch_dtls", 1 );
		$deleted_id='';
		
		for($j=1;$j<=$tot_row;$j++)
		{ 	
			$receiveBasis="receiveBasis".$j;
			$poId="poId".$j;
			$buyerId="buyerId".$j;
			$job="job".$j;
			$programNo="programNo".$j;
			$bookingNo="bookingNo".$j;
			$programBookingId="programBookingId".$j;
			$prodId="prodId".$j;
			$deterId="deterId".$j;
			$colorId="colorId".$j;
			$lot="lot".$j;
			$countId="countId".$j;
			$reqsnQty="reqsnQty".$j;
			$remarks="remarks".$j;
			$dtlsId="dtlsId".$j;
			
			if($$dtlsId>0)
			{
				if($$reqsnQty>0)
				{
					$dtlsId_arr[]=$$dtlsId;
					$data_array_update[$$dtlsId]=explode("*",("'".$$reqsnQty."'*'".$$remarks."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				}
				else
				{
					$deleted_id.=$$dtlsId.",";
				}
			}
			else
			{
				if($$receiveBasis==1) $program_booking_pi_no=$$programNo; else $program_booking_pi_no=$$bookingNo;
	
				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls.="(".$dtls_id.",".$update_id.",".$$receiveBasis.",'".$program_booking_pi_no."','".$$programBookingId."','".$$poId."','".$$buyerId."','".$$job."','".$$prodId."','".$$deterId."','".$$lot."','".$$countId."','".$$colorId."','".$$reqsnQty."','".$$remarks."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				$dtls_id = $dtls_id+1;
	
			}
		}
		
		$rID=sql_update("pro_fab_reqn_for_batch_mst",$field_array,$data_array,"id",$update_id,0);
		
		$rID2=true; $rID3=true; $statusChange=true;
		if(count($data_array_update)>0)
		{
			$rID2=execute_query(bulk_update_sql_statement( "pro_fab_reqn_for_batch_dtls", "id", $field_array_update, $data_array_update, $dtlsId_arr ));
		}
		
		if($data_array_dtls!="")
		{
			$rID2=sql_insert("pro_fab_reqn_for_batch_dtls",$field_array_dtls,$data_array_dtls,1);
		}

		$deleted_id=substr($deleted_id,0,-1);
		if($deleted_id!="")
		{
			$field_array_status="updated_by*update_date*status_active*is_deleted";
			$data_array_status=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
			$statusChange=sql_multirow_update("pro_fab_reqn_for_batch_dtls",$field_array_status,$data_array_status,"id",$deleted_id,0);
		}
		//oci_rollback($con);
		//echo "10**".$rID."&&".$rID2."&&".$rID3."&&".$statusChange;die;
		
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $statusChange)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_requisition_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**".str_replace("'", '', $update_id)."**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3 && $statusChange)
			{
				oci_commit($con);  
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_requisition_no);
			}
			else
			{
				oci_rollback($con);
				echo "6**".str_replace("'", '', $update_id)."**1";
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="requisition_popup")
{
	echo load_html_head_contents("Requisition Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
?> 

	<script>
	
		function js_set_value(data)
		{
			$('#hidden_reqn_id').val(data);
			parent.emailwindow.hide();
		}
	
    </script>

</head>

<body>
<div align="center" style="width:760px;">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:760px; margin-left:2px">
            <table cellpadding="0" cellspacing="0" width="750" border="1" rules="all" class="rpt_table">
                <thead>
                    <th>Location</th>
                    <th>Requisition Date Range</th>
                    <th id="search_by_td_up" width="180">Requisition No</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                    	<input type="hidden" name="hidden_reqn_id" id="hidden_reqn_id">  
                    </th> 
                </thead>
                <tr class="general">
                    <td align="center">
                    	 <? echo create_drop_down( "cbo_location_id", 150,"select id,location_name from lib_location where company_id='$company_id' and status_active =1 and is_deleted=0 order by location_name",'id,location_name', 1, '-- Select Location --',0,"",0); ?>        
                    </td>
                    <td align="center">
                    	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" readonly>To
					  	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" readonly>
					</td>
                    <td align="center" id="search_by_td">				
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_reqn_no" id="txt_reqn_no" />	
                    </td> 						
            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_reqn_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_location_id').value+'_'+<? echo $company_id; ?>, 'create_reqn_search_list_view', 'search_div', 'fabric_requisition_for_batch_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                     </td>
                </tr>
                <tr>
                	<td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
           </table>
           <div style="width:100%; margin-top:5px;" id="search_div" align="center"></div>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	$('#cbo_location_id').val(0);
</script>
</html>
<?
}

if($action=="create_reqn_search_list_view")
{
	$data = explode("_",$data);
	
	$search_string="%".trim($data[0]);
	$start_date =$data[1];
	$end_date =$data[2];
	$location_id =$data[3];
	$company_id =$data[4];

	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and a.reqn_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and a.reqn_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}
	else
	{
		$date_cond="";
	}
	
	$search_field_cond="";
	if(trim($data[0])!="")
	{
		$search_field_cond="and a.reqn_number like '$search_string'";
	}
	
	$location_cond="";
	if($location_id>0)
	{
		$location_cond="and a.location_id=$location_id";
	}
	
	if($db_type==0) 
	{
		$year_field="YEAR(a.insert_date) as year,";
	}
	else if($db_type==2) 
	{
		$year_field="to_char(a.insert_date,'YYYY') as year,";
	}
	else $year_field="";//defined Later
	
	//$sql = "select id, $year_field reqn_number_prefix_num, reqn_number, location_id, reqn_date from pro_fab_reqn_for_batch_mst where status_active=1 and is_deleted=0 and company_id=$company_id $search_field_cond $location_cond $date_cond order by id"; 
	$sql ="SELECT a.id, to_char(a.insert_date,'YYYY') as year, a.reqn_number_prefix_num, a.reqn_number, a.location_id, a.reqn_date ,b.entry_form from pro_fab_reqn_for_batch_mst a, pro_fab_reqn_for_batch_dtls b
	where a.id=b.mst_id and b.entry_form is null and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $search_field_cond $location_cond $date_cond group by a.id, a.insert_date, a.reqn_number_prefix_num, a.reqn_number, a.location_id, a.reqn_date ,b.entry_form
	order by a.id";
	$arr=array(0=>$location_arr);
	
	echo create_list_view("tbl_list_search", "Location, Year, Requisition No, Requisition Date", "250,70,130","700","200",0, $sql, "js_set_value", "id", "", 1, "location_id,0,0,0", $arr, "location_id,year,reqn_number_prefix_num,reqn_date","","",'0,0,0,3','');
	
	exit();
}

if($action=='populate_data_from_requisition')
{
	$data_array=sql_select("select id, reqn_number, company_id, location_id, reqn_date from pro_fab_reqn_for_batch_mst where id='$data'");
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('txt_requisition_no').value 			= '".$row[csf("reqn_number")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_location_name').value 			= '".$row[csf("location_id")]."';\n";
		echo "document.getElementById('txt_requisition_date').value 		= '".change_date_format($row[csf("reqn_date")])."';\n";
		echo "document.getElementById('update_id').value 					= '".$row[csf("id")]."';\n";
		
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_fabric_requisition_for_batch',1);\n";  
		exit();
	}
}

if( $action == 'populate_list_view' ) 
{	
 	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$basis_arr=array(1=>"Program Based",2=>"Booking Based",3=>"PI Based",4=>"Others");

	$composition_arr=array(); $constructtion_arr=array();
 	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	
	$product_arr=array();
 	$sql_product="select id, gsm, dia_width from product_details_master where item_category_id=13";
	$data_array=sql_select($sql_product);
	foreach( $data_array as $row )
	{
		$product_arr[$row[csf('id')]]['gsm']=$row[csf('gsm')];
		$product_arr[$row[csf('id')]]['dia']=$row[csf('dia_width')];
	}
	
	$sql="SELECT id, receive_basis, program_booking_pi_no, program_booking_pi_id, po_id, buyer_id, job_no, prod_id, determination_id, color_id, reqn_qty, remarks from pro_fab_reqn_for_batch_dtls where mst_id='$data' and status_active=1 and is_deleted=0";
	$result=sql_select($sql);

	foreach ($result as $row)
	{
		$all_po_id[$row[csf('po_id')]]= $row[csf('po_id')];
		if($row[csf('receive_basis')]==2) 
		{
			if($row[csf('po_id')] ==0 || $row[csf('po_id')]=="")
			{
				$all_samp_book_arr[$row[csf('program_booking_pi_no')]] = "'".$row[csf('program_booking_pi_no')]."'";
			}
		}
	}
    $po_cond = "";
    $po_cond_2 = "";
    $po_cond_3 = "";
	if(count($all_po_id)>0)
	{
        $all_po_id_chunk = array_chunk($all_po_id, 999);
        $po_cond .= " and ( ";
        $po_cond_2 .= " and ( ";
        $po_cond_3 .= " and ( ";
        foreach ($all_po_id_chunk as $key => $val){
            if($key == 0){
                $po_cond .= " a.po_break_down_id in (". implode(",",$val) . ")";
                $po_cond_2 .= " a.id in (". implode(",",$val) . ")";
                $po_cond_3 .= " po_id in (". implode(",",$val) . ")";
            }else{
                $po_cond .= " or a.po_break_down_id in (". implode(",",$val) . ")";
                $po_cond_2 .= " or a.id in (". implode(",",$val) . ")";
                $po_cond_3 .= " or po_id in (". implode(",",$val) . ")";
            }
        }
        $po_cond .= " ) ";
        $po_cond_2 .= " ) ";
        $po_cond_3 .= " ) ";
    }

	if(count($all_samp_book_arr)<1000)
	{
		$samp_book_cond = " and a.booking_no in (". implode(",",$all_samp_book_arr) . ")";
	}


	$booking_qnty_array=array(); $samp_booking_qnty_array=array();
	$bookingData=sql_select("select a.po_break_down_id, a.booking_no, b.lib_yarn_count_deter_id as deter_id, b.gsm_weight, a.dia_width, sum(a.grey_fab_qnty) as qnty from wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b where a.pre_cost_fabric_cost_dtls_id=b.id and a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 $po_cond group by a.po_break_down_id, a.booking_no, b.lib_yarn_count_deter_id, b.gsm_weight, a.dia_width");
	foreach($bookingData as $row)
	{
		$booking_qnty_array[$row[csf('po_break_down_id')]][$row[csf('booking_no')]][$row[csf('deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]=$row[csf('qnty')];
	}

	$sampBookingData=sql_select("select a.booking_no, a.lib_yarn_count_deter_id as deter_id, a.gsm_weight, a.dia_width, sum(a.grey_fabric) as qnty from wo_non_ord_samp_booking_dtls a, wo_non_ord_samp_booking_mst b where a.booking_no=b.booking_no and b.item_category=2 and a.status_active=1 and a.is_deleted=0 $samp_book_cond group by a.booking_no, a.lib_yarn_count_deter_id, a.gsm_weight, a.dia_width");
	foreach($sampBookingData as $row)
	{
		$samp_booking_qnty_array[$row[csf('booking_no')]][$row[csf('deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]=$row[csf('qnty')];
	}

	$po_arr=array();
	$poDataArr=sql_select("select a.id, a.po_number, a.grouping, a.file_no, b.style_ref_no from wo_po_break_down a, wo_po_details_master b where a.job_no_mst = b.job_no and a.status_active = 1 and a.is_deleted = 0 $po_cond_2");
    foreach($poDataArr as $row )
	{
		$po_arr[$row[csf('id')]]['no']=$row[csf('po_number')];
		$po_arr[$row[csf('id')]]['ref']=$row[csf('grouping')];
		$po_arr[$row[csf('id')]]['file']=$row[csf('file_no')];
		$po_arr[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
	}

	$program_qnty_array=array(); $program_bookingNo_array=array();
	$programData=sql_select("select po_id, booking_no, dtls_id, determination_id, gsm_weight, dia, sum(program_qnty) as qnty from ppl_planning_entry_plan_dtls where status_active=1 and is_deleted=0 $po_cond_3 group by po_id, dtls_id, determination_id, gsm_weight, dia, booking_no");
	foreach($programData as $row)
	{
		$program_qnty_array[$row[csf('po_id')]][$row[csf('dtls_id')]][$row[csf('determination_id')]][$row[csf('gsm_weight')]][$row[csf('dia')]]=$row[csf('qnty')];
		$program_bookingNo_array[$row[csf('dtls_id')]]=$row[csf('booking_no')];
	}

	$reqn_qnty_array=array();
	$reqnData=sql_select("SELECT receive_basis, program_booking_pi_id, po_id, prod_id, determination_id, color_id, sum(reqn_qty) as qnty 
	from pro_fab_reqn_for_batch_dtls where status_active=1 and is_deleted=0
	group by receive_basis, program_booking_pi_id, po_id, prod_id, determination_id, color_id");// $po_cond_3
	foreach($reqnData as $row)
	{
		//echo $row[csf('receive_basis')].']['.$row[csf('program_booking_pi_id')].']['.$row[csf('po_id')].']['.$row[csf('prod_id')].']['.$row[csf('determination_id')].']['.$row[csf('color_id')].'*<br>';
		$reqn_qnty_array[$row[csf('receive_basis')]][$row[csf('program_booking_pi_id')]][$row[csf('po_id')]][$row[csf('prod_id')]][$row[csf('determination_id')]][$row[csf('color_id')]]=$row[csf('qnty')];
	}

	$i=1;
	foreach ($result as $row)
	{
		$gsm=$product_arr[$row[csf('prod_id')]]['gsm'];
		$dia=$product_arr[$row[csf('prod_id')]]['dia'];
		
		$programNo=''; $reqQty=0; $bookingNo='';
		if($row[csf('receive_basis')]==1) 
		{
			$programNo=$row[csf('program_booking_pi_no')];
			$bookingNo=$program_bookingNo_array[$row[csf('program_booking_pi_id')]];
			$reqQty=$program_qnty_array[$row[csf('po_id')]][$row[csf('program_booking_pi_id')]][$row[csf('determination_id')]][$gsm][$dia];
		}
		else if($row[csf('receive_basis')]==2) 
		{
			$bookingNo=$row[csf('program_booking_pi_no')];	
			if($row[csf('po_id')]>0)
				$reqQty=$booking_qnty_array[$row[csf('po_id')]][$row[csf('program_booking_pi_no')]][$row[csf('determination_id')]][$gsm][$dia];
			else 
				$reqQty=$samp_booking_qnty_array[$row[csf('program_booking_pi_no')]][$row[csf('determination_id')]][$gsm][$dia];
		}
		//echo $row[csf('receive_basis')].']['.$row[csf('program_booking_pi_id')].']['.$row[csf('po_id')].']['.$row[csf('prod_id')].']['.$row[csf('determination_id')].']['.$row[csf('color_id')].'=<br>';
		$totReqnQty=$reqn_qnty_array[$row[csf('receive_basis')]][$row[csf('program_booking_pi_id')]][$row[csf('po_id')]][$row[csf('prod_id')]][$row[csf('determination_id')]][$row[csf('color_id')]];
		$reqQty=number_format($reqQty,2,'.','');
		$totReqnQty=number_format($totReqnQty,2,'.','');
		// echo "$reqQty-$totReqnQty<br>";
		$balance=number_format($reqQty-$totReqnQty,2,'.','');
		
		$yarn_count='';

		
		$color='';
		$color_id=array_unique(explode(',',$row[csf('color_id')]));
		foreach($color_id as $id)
		{
			if($id>0)
			{
				if($color=='') $color=$color_arr[$id]; else $color.=", ".$color_arr[$id];
			}
		}	

		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		?>
		<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>"> 
            <td width="30" align="center"><? echo $i; ?></td>
            <td width="80" style="word-break:break-all;"><? echo $po_arr[$row[csf('po_id')]]['no']; ?></td>
            <td width="100" style="word-break:break-all;"><? echo $po_arr[$row[csf('po_id')]]['style']; ?></td>
            <td width="70" style="word-break:break-all;"><? echo $po_arr[$row[csf('po_id')]]['ref']; ?></td>
            <td width="60" style="word-break:break-all;"><? echo $po_arr[$row[csf('po_id')]]['file']; ?></td>
            <td width="55"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>
            <td width="80" id="job<? echo $i; ?>"><p><? echo $row[csf('job_no')]; ?></p></td>
            <td width="75"><p><? echo $constructtion_arr[$row[csf('determination_id')]]; ?></p></td>
            <td width="100"><p><? echo $composition_arr[$row[csf('determination_id')]]; ?></p></td>
            <td width="40" id="gsm<? echo $i; ?>"><p><? echo $gsm; ?></p></td>
            <td width="40" id="dia<? echo $i; ?>"><p><? echo $dia; ?></p></td>
            <td width="70"><p><? echo $color; ?></p></td>
            <td width="70" align="right"><? echo $reqQty; ?></td>
            <td width="70" align="right"><? echo $totReqnQty; ?></td>
            <td width="70" align="right" id="totBalQty<? echo $i; ?>"><? echo number_format($balance, 2,'.',''); ?></td>
            <td width="80" align="center">
            	<input type="text" value="<? echo number_format($row[csf('reqn_qty')],2,'.',''); ?>" class="text_boxes_numeric" style="width:65px" id="reqsnQty<? echo $i; ?>" name="reqsnQty[]" onKeyUp="fnc_count_total_qty();fnc_validation_qty(<? echo $i; ?>);"/>
            	<input type="hidden" id="previous_reqsnQty<? echo $i; ?>" value="<? echo number_format($row[csf('reqn_qty')], 2,'.',''); ?>">
            </td>
            <td width="90" align="center"><input type="text" value="<? echo $row[csf('remarks')]; ?>" class="text_boxes" style="width:75px" id="remarks<? echo $i; ?>" name="remarks[]"/></td>
            <td width="65" id="programNo<? echo $i; ?>"><p><? echo $programNo; ?></p></td>
            <td width="90" id="bookingNo<? echo $i; ?>"><p><? echo $bookingNo; ?></p></td>
            <td width="70" style="display:none"><p><? //echo $yarn_count; ?></p></td>
            <td width="70" style="display:none" id="lot<? echo $i; ?>"><p><? //echo $row[csf('lot')]; ?></p></td>
            <td id="prodId<? echo $i; ?>"><? echo $row[csf('prod_id')]; ?>
                <input type="hidden" value="<? echo $row[csf('buyer_id')]; ?>" id="buyerId<? echo $i; ?>" name="buyerId[]"/>
                <input type="hidden" value="<? echo $row[csf('po_id')]; ?>" id="poId<? echo $i; ?>" name="poId[]"/>
                <input type="hidden" value="<? echo $row[csf('determination_id')]; ?>" id="deterId<? echo $i; ?>" name="deterId[]"/>
                <input type="hidden" value="<? echo $row[csf('color_id')]; ?>" id="colorId<? echo $i; ?>" name="colorId[]"/>
                <input type="hidden" value="<? echo $row[csf('count')]; ?>" id="countId<? echo $i; ?>" name="countId[]"/>
                <input type="hidden" value="<? echo $row[csf('program_booking_pi_id')]; ?>" id="programBookingId<? echo $i; ?>" name="programBookingId[]"/>
                <input type="hidden" value="<? echo $row[csf('receive_basis')]; ?>" id="receiveBasis<? echo $i; ?>" name="receiveBasis[]"/>
                <input type="hidden" value="<? echo $row[csf('id')]; ?>" id="dtlsId<? echo $i; ?>" name="dtlsId[]"/>
                <input type="hidden" value="<? echo $reqQty; ?>" id="progBookingQntyID<? echo $i; ?>" name="progBookingQntyID[]"/>
                <input type="hidden" value="<? echo $totReqnQty; ?>" id="prevReqQntyID<? echo $i; ?>" name="prevReqQntyID[]"/>

            </td>
        </tr>
	<?		
	
	$totReqQty+=$reqQty;
	$grandtotReqnQty+=$totReqnQty;
	$totBalance+=$balance;
	$tot_reqNewQty+=$row[csf('reqn_qty')];
		$i++;
	}
	?>
    	<tr>
        	<td width="70" colspan="12" align="right"><strong>Total</strong></td>
            <td width="70" align="right" id="total_prog_booking_qty"><strong><? echo number_format($totReqQty,2); ?></strong></td>
            <td width="70" align="right" id="total_req_qty"><strong><? echo number_format($grandtotReqnQty,2); ?></strong></td>
            <td width="70" align="right" id="total_balance_qty"><strong><? echo number_format($totBalance,2); ?></strong></td>
            <td width="70" align="right"><strong><input type="text" class="text_boxes_numeric" style="width:65px" id="total_blnc_qty_td_id" name="" value="<? echo number_format($tot_reqNewQty,2); ?>"  readonly /></strong></td>
        </tr>
    <?		
	exit();
}

if($action=="print_fab_req_for_batch")
{
	extract($_REQUEST);
	//echo $data;
	$ex_data=explode('*',$data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	//$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library",'master_tble_id','image_location');
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$location_arr=return_library_array("select id,location_name from lib_location", "id","location_name");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	//$yarncount=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	
	$sql_mst="Select id, reqn_number,location_id,reqn_date from pro_fab_reqn_for_batch_mst where company_id=$ex_data[0] and id='$ex_data[1]' and status_active=1 and is_deleted=0";
	$dataArray=sql_select($sql_mst);
    ob_start();
	?>
    <div style="width:1060px;">
    <table width="100%" cellpadding="0" cellspacing="0" >
        <tr>
            <td width="70" align="right"> 
            	<img  src='../../<? echo $imge_arr[$ex_data[0]]; ?>' height='100%' width='100%' />
            </td>
            <td>
                <table width="800" cellspacing="0" align="center">
                    <tr class="form_caption">
                    	<td align="center" style="font-size:18px"><strong ><? echo $company_library[$ex_data[0]]; ?></strong></td>
                    </tr>
                    <tr class="form_caption">
                        <td align="center" style="font-size:14px"><strong>Unit : <? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></strong></td>
                    </tr>
                    <tr class="form_caption">
                        <td align="center" style="font-size:14px"><? echo show_company($ex_data[0],'',''); ?> </td>  
                    </tr>
                    <tr class="form_caption">
                    	<td align="center" style="font-size:16px"><u><strong><? echo $ex_data[3]; ?></strong></u></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <table width="930" cellspacing="0" align="" border="0">
        <tr>
            <td width="130"><strong>Requisition No :</strong></td> <td width="175"><? echo $dataArray[0][csf('reqn_number')]; ?></td>
            <td width="130"><strong>Requisition Date: </strong></td><td width="175px"> <? echo change_date_format($dataArray[0][csf('reqn_date')]); ?></td>
            <td width="130">&nbsp;</td> <td width="175">&nbsp;</td>
        </tr>
    </table>
    <br>
	<div style="width:100%;">
		<table align="left" cellspacing="0" width="1060"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" style="font-size:13px">
                <th width="20">SL</th>
                <th width="110">File/Ref. No</th>
                <th width="100">Buyer/ Job /Order</th>
                <th width="80">Style No</th>
                <th width="120">Construction, Composition</th>
                <th width="30">GSM</th> 
                <th width="30">Dia</th>
                <th width="70">Color/ Code</th>
                <th width="60">Prog. /Book Qty.</th>
                <th width="60">Total Req. Qty.</th>
                <th width="60">Balance</th>
                <th width="60">Reqsn. Qty.</th>
                <th width="90">Remarks</th>
                <th width="45">Prog. No</th>
                <th width="100">Booking No</th>
                <!--<th>Yarn Lot/  Count</th>-->
            </thead>
            <tbody>
    		<?
			if($db_type==0) $year_val="year(a.insert_date)"; else if( $db_type==2) $year_val="TO_CHAR(a.insert_date,'YYYY')";
			$po_arr=array();
			$po_sql="select a.style_ref_no, $year_val as year, b.id, b.po_number, b.file_no, b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$ex_data[0]'";
			$po_sql_result=sql_select($po_sql);
			foreach( $po_sql_result as $row )
			{
				$po_arr[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
				$po_arr[$row[csf('id')]]['po']=$row[csf('po_number')];
				$po_arr[$row[csf('id')]]['file']=$row[csf('file_no')];
				$po_arr[$row[csf('id')]]['ref']=$row[csf('grouping')];
				$po_arr[$row[csf('id')]]['year']=$row[csf('year')];
			}
			
			$composition_arr=array(); $constructtion_arr=array();
			$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
			$data_array=sql_select($sql_deter);
			foreach( $data_array as $row )
			{
				$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
				$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
			}
			
			
			$product_arr=array();
			$sql_product="select id, gsm, dia_width from product_details_master where item_category_id=13";
			$data_array=sql_select($sql_product);
			foreach( $data_array as $row )
			{
				$product_arr[$row[csf('id')]]['gsm']=$row[csf('gsm')];
				$product_arr[$row[csf('id')]]['dia']=$row[csf('dia_width')];
			}
			
			$program_qnty_array=array(); $program_bookingNo_array=array();
			$programData=sql_select("select po_id, booking_no, dtls_id, determination_id, gsm_weight, dia, sum(program_qnty) as qnty from ppl_planning_entry_plan_dtls where status_active=1 and is_deleted=0 group by po_id, dtls_id, determination_id, gsm_weight, dia, booking_no");
			foreach($programData as $row)
			{
				$program_qnty_array[$row[csf('po_id')]][$row[csf('dtls_id')]][$row[csf('determination_id')]][$row[csf('gsm_weight')]][$row[csf('dia')]]=$row[csf('qnty')];
				$program_bookingNo_array[$row[csf('dtls_id')]]=$row[csf('booking_no')];
			}
			
			$booking_qnty_array=array(); $samp_booking_qnty_array=array();
			$bookingData=sql_select("select a.po_break_down_id, a.booking_no, b.lib_yarn_count_deter_id as deter_id, b.gsm_weight, a.dia_width, sum(a.grey_fab_qnty) as qnty from wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b where a.pre_cost_fabric_cost_dtls_id=b.id and a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 group by a.po_break_down_id, a.booking_no, b.lib_yarn_count_deter_id, b.gsm_weight, a.dia_width");
			foreach($bookingData as $row)
			{
				$booking_qnty_array[$row[csf('po_break_down_id')]][$row[csf('booking_no')]][$row[csf('deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]=$row[csf('qnty')];
			}
			
			$sampBookingData=sql_select("select a.booking_no, a.lib_yarn_count_deter_id as deter_id, a.gsm_weight, a.dia_width, sum(a.grey_fabric) as qnty from wo_non_ord_samp_booking_dtls a, wo_non_ord_samp_booking_mst b where a.booking_no=b.booking_no and b.item_category=2 and a.status_active=1 and a.is_deleted=0 group by a.booking_no, a.lib_yarn_count_deter_id, a.gsm_weight, a.dia_width");
			foreach($sampBookingData as $row)
			{
				$samp_booking_qnty_array[$row[csf('booking_no')]][$row[csf('deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]=$row[csf('qnty')];
			}
			
			$reqn_qnty_array=array();
			$reqnData=sql_select("select receive_basis, program_booking_pi_id, po_id, prod_id, determination_id, color_id, sum(reqn_qty) as qnty from pro_fab_reqn_for_batch_dtls where status_active=1 and is_deleted=0 group by receive_basis, program_booking_pi_id, po_id, prod_id, determination_id, color_id");
			foreach($reqnData as $row)
			{
				$reqn_qnty_array[$row[csf('receive_basis')]][$row[csf('program_booking_pi_id')]][$row[csf('po_id')]][$row[csf('prod_id')]][$row[csf('determination_id')]][$row[csf('color_id')]]=$row[csf('qnty')];
			}
			
			$sql="select id, receive_basis, program_booking_pi_no, program_booking_pi_id, po_id, buyer_id, job_no, prod_id, determination_id, color_id, reqn_qty, remarks from pro_fab_reqn_for_batch_dtls where mst_id='$ex_data[1]' and status_active=1 and is_deleted=0";
			$result=sql_select($sql);
			$i=1;
			foreach($result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$file_ref_no="";
				$file_ref_no="F: K/".$po_arr[$row[csf('po_id')]]['year'].'/'.$po_arr[$row[csf('po_id')]]['file'].'<br> R: '.$po_arr[$row[csf('po_id')]]['ref'];
				$buyer_job_ord="";
				$buyer_job_ord="B: ".$buyer_arr[$row[csf('buyer_id')]].'<br> J: '.$row[csf('job_no')].'<br> O: '.$po_arr[$row[csf('po_id')]]['po'];
				$const_comp="";
				$const_comp=$constructtion_arr[$row[csf('determination_id')]].', '.$composition_arr[$row[csf('determination_id')]];
				
				$gsm=$product_arr[$row[csf('prod_id')]]['gsm'];
				$dia=$product_arr[$row[csf('prod_id')]]['dia'];
				
				$programNo=''; $reqQty=0; $bookingNo='';
				if($row[csf('receive_basis')]==1) 
				{
					$programNo=$row[csf('program_booking_pi_no')];
					$bookingNo=$program_bookingNo_array[$row[csf('program_booking_pi_id')]];
					$reqQty=$program_qnty_array[$row[csf('po_id')]][$row[csf('program_booking_pi_id')]][$row[csf('determination_id')]][$gsm][$dia];
				}
				else if($row[csf('receive_basis')]==2) 
				{
					$bookingNo=$row[csf('program_booking_pi_no')];	
					if($row[csf('po_id')]>0)
						$reqQty=$booking_qnty_array[$row[csf('po_id')]][$row[csf('program_booking_pi_no')]][$row[csf('determination_id')]][$gsm][$dia];
					else 
						$reqQty=$samp_booking_qnty_array[$row[csf('program_booking_pi_no')]][$row[csf('determination_id')]][$gsm][$dia];
				}
				
				$totReqnQty=$reqn_qnty_array[$row[csf('receive_basis')]][$row[csf('program_booking_pi_id')]][$row[csf('po_id')]][$row[csf('prod_id')]][$row[csf('determination_id')]][$row[csf('color_id')]];
				$reqQty=number_format($reqQty,2,'.','');
				$totReqnQty=number_format($totReqnQty,2,'.','');
				$balance=number_format($reqQty-$totReqnQty,2,'.','');
				
				$yarn_count='';
				/*$yarn_count_id=array_unique(explode(',',$row[csf('count')]));
				foreach($yarn_count_id as $id)
				{
					if($id>0)
					{
						if($yarn_count=='') $yarn_count=$yarncount[$id]; else $yarn_count.=", ".$yarncount[$id];
					}
				}*/
				
				$color='';
				$color_id=array_unique(explode(',',$row[csf('color_id')]));
				foreach($color_id as $id)
				{
					if($id>0)
					{
						if($color=='') $color=$color_arr[$id]; else $color.=", ".$color_arr[$id];
					}
				}	
				
				$lot_count="";
				//$lot_count="L :".$row[csf('lot')].'<br> C :'.$yarn_count;
				
				?>
				 <tr bgcolor="<? echo $bgcolor; ?>" style="font-size:13px">
					<td><? echo $i; ?></td>
					<td><div style="word-wrap:break-word; width:110px"><? echo $file_ref_no; ?></div></td>
					<td><div style="word-wrap:break-word; width:100px"><? echo $buyer_job_ord; ?></div></td>
					<td><div style="word-wrap:break-word; width:100px"><? echo $po_arr[$row[csf('po_id')]]['style']; ?></div></td>
					<td><div style="word-wrap:break-word; width:120px"><? echo $const_comp; ?></div></td>
					<td><? echo $gsm; ?></td> 
					<td><? echo $dia; ?></td>
					<td><div style="word-wrap:break-word; width:70px"><? echo $color; ?></div></td>
					<td align="right"><? echo number_format($reqQty,2); ?></td>
					<td align="right"><? echo number_format($totReqnQty,2); ?></td>
					<td align="right"><? echo number_format($balance,2); ?></td>
					<td align="right"><? echo number_format($row[csf('reqn_qty')],2); ?></td>
					<td><div style="word-wrap:break-word; width:110px"><? echo $row[csf('remarks')]; ?></div></td>
					<td align="center"><? echo $programNo; ?></td>
					<td><div style="word-wrap:break-word; width:100px"><? echo $bookingNo; ?></div></td>
					<!--<td><div style="word-wrap:break-word; width:95px"><?echo $lot_count; ?></div></td>-->
				</tr>
				<?
				$grnd_prog_book_qty+=$reqQty;
				$grnd_tot_req_qty+=$totReqnQty;
				$grnd_balance+=$balance;
				$grnd_reqn_qty+=$row[csf('reqn_qty')];
				$i++;
			}
			?>
            </tbody>
            <tfoot bgcolor="#dddddd" style="font-size:13px">
            	<tr>
                	<td colspan="8" align="right"><strong>Total :</strong></td>
                    <td align="right"><? echo number_format($grnd_prog_book_qty,2); ?></td>
                    <td align="right"><? echo number_format($grnd_tot_req_qty,2); ?></td>
                    <td align="right"><? echo number_format($grnd_balance,2); ?></td>
                    <td align="right"><? echo number_format($grnd_reqn_qty,2); ?></td>
                    <td colspan="4">&nbsp;</td>
                </tr>
            </tfoot>
        </table>
        </div>
        <br>
		 <?
            echo signature_table(93, $ex_data[0], "1060px");
         ?>
    </div>
    <?
    $user_id=$_SESSION['logic_erp']['user_id'];
    $html = ob_get_contents();
    ob_clean();
    foreach (glob("frb*.xls") as $filename) {
        @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename="frb".$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    $script = " <script type='text/javascript' src='../../js/jquery.js'></script><script>";
    $script .= "$('#hiddenPrint').removeAttr('href').attr('href','$filename');\n";
    $script .= "document.getElementById('hiddenPrint').click();\n</script>";
    echo $html.'<a href="" id="hiddenPrint" style="display: none;">Hidden print</a>'.$script;
	exit();
}
?>
