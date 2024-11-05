<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="party_popup")
{
	echo load_html_head_contents("Party Info", "../../../../", 1, '','','','');
	extract($_REQUEST);
	
	?>
     
	<script>
		var selected_id = new Array; var selected_name = new Array;
		
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click'); 
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str ) {
			
			if (str!="") str=str.split("_");
			 
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			 
			if( jQuery.inArray( str[1], selected_id ) == -1 ) {
				selected_id.push( str[1] );
				selected_name.push( str[2] );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
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
			
			$('#hide_party_id').val( id );
			$('#hide_party_name').val( name );
		}
    </script>
    </head>
    <body>
    <div align="center">
    <fieldset style="width:390px;">
        <input type="hidden" name="hide_party_name" id="hide_party_name" value="" />
        <input type="hidden" name="hide_party_id" id="hide_party_id" value="" />
	<?

	if ($cbo_dyeing_source==3)
	{
		 $sql="select a.id, a.supplier_name as party_name from lib_supplier a, lib_supplier_party_type b,lib_supplier_tag_company c where a.id=b.supplier_id and c.supplier_id=b.supplier_id and c.tag_company=$companyID and b.party_type in(21,24) and a.status_active=1 group by a.id, a.supplier_name order by a.supplier_name";
	}
	else if($cbo_dyeing_source==1)
	{
		$sql="select id, company_name as party_name from lib_company comp where id=$companyID and status_active=1 and is_deleted=0 order by company_name";
	}

	echo create_list_view("tbl_list_search", "Party Name", "330","380","270",0, $sql , "js_set_value", "id,party_name", "", 1, "0", $arr , "party_name", "",'setFilterGrid("tbl_list_search",-1);','0','',1) ;
	?>
    </fieldset>
    </div>
    </body>
    </html>
    <?php
   exit(); 
}
 
if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");

	$date_cond='';$date_cond_recv='';
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
	 if($db_type==0)
		{
			$from_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
			$to_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
		}
		else if($db_type==2)
		{
			$from_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
			$to_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
		}
 	$date_cond=" and a.issue_date between '$from_date' and '$to_date'";
	$date_cond_recv=" and a.receive_date between '$from_date' and '$to_date'";
	
	}
	
	$dyeing_company=str_replace("'","",$txt_dyeing_com_id);
	$dyeing_source=str_replace("'","",$cbo_dyeing_source);
	$type=str_replace("'","",$type);


	if($dyeing_source!=0)
	{ 
		$dyeing_source_cond=" and a.knit_dye_source in($dyeing_source)";
		$dyeing_source_cond_recv=" and a.knitting_source in($dyeing_source)";
	}
	else 
	{ 
	 	$dyeing_source_cond="";$dyeing_source_cond_recv="";
	}
	if ($dyeing_company!='' || $dyeing_company!=0) 
	{
		 $dyeing_company_cond=" and a.knit_dye_company in($dyeing_company)";
		  $dyeing_company_cond_recv=" and a.knitting_company in($dyeing_company)";
	}
	else {
		
		 $dyeing_company_cond="";$dyeing_company_cond_recv="";
	}
	//echo $style_arr;
		ob_start();
	?>
        <fieldset style="width:950px">
           <div style="width:950px; max-height:350px;">  
            <table width="950" cellpadding="0" cellspacing="0" id="caption"><!-- style="visibility:hidden; border:none"-->
                <tr>
                   <td align="center" width="100%" colspan="9" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
                </tr> 
                <tr>  
                   <td align="center" width="100%" colspan="9" style="font-size:16px"><strong><? echo $report_title; ?> </strong></td>
                </tr>  
                <tr> 
                   <td align="center" width="100%" colspan="9" style="font-size:16px"><strong><? echo "From ".change_date_format(str_replace("'","",$txt_date_from))." To ".change_date_format(str_replace("'","",$txt_date_to)); ?></strong></td>
                </tr>
                 
            </table>
            <br />
            <table width="950" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                    <th width="30">SL</th> 
                    <th width="200">Party Name </th> 
                    <th width="100">Grey Delivery </th> 
                    <th width="100">Grey Return</th>  
                    <th width="100">Net Grey Delivery</th> 
                    <th width="100">Finished Received</th> 
                    <th width="100">Grey Used to Recv.</th>
                    <th width="100">Received Balance </th> 
                    <th width="">Process Loss</th> 
                    
                </thead>
            </table>
            <div style="width:950px; overflow-y: scroll; max-height:400px;" id="scroll_body">
             <table width="930" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body"> 
				<? 
				//a.knitting_company
						$dyeing_party_arr=array();$finnish_fab_recv_qty=array();$recv_ret_data_qty=array();
					 	$sql_issue=("select a.knitting_source,a.company_id,
						sum(case when a.entry_form in(51,84) and a.item_category=13  then b.cons_quantity end) as issue_return
						 from inv_receive_master a,  inv_transaction b where a.id=b.mst_id and b.transaction_type=4   and a.entry_form in(51,84)  and a.company_id=$cbo_company_name  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $date_cond_recv group by a.company_id,a.knitting_source");
						$result_data=sql_select($sql_issue);
						foreach($result_data as $row)
						{
							 $issue_ret_data_qty[$row[csf('company_id')]]['ret_qty']+=$row[csf('issue_return')];
						}
						 $material_data=sql_select("select dtls_id,used_qty from pro_material_used_dtls where  entry_form=37 and item_category=13");
						foreach($material_data as $value)
						{
							$grey_used_arr[$value[csf('dtls_id')]]=$value[csf('used_qty')];
							
						}
						$sql_fab_recv=("select a.knitting_company,a.knitting_source,c.id as dtls_id,
						sum(case when a.entry_form in(37) and a.item_category=2  then c.receive_qnty end) as finish_recv,
						sum(case when a.entry_form in(37) and a.item_category=2  then c.grey_used_qty end) as grey_used_qty
						 from inv_receive_master a,  inv_transaction b,pro_finish_fabric_rcv_dtls c where a.id=b.mst_id and b.id=c.trans_id and b.transaction_type in(1) and a.company_id=$cbo_company_name  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $dyeing_company_cond_recv $dyeing_source_cond_recv  $date_cond_recv group by c.id,a.knitting_company,a.knitting_source");
						$result_data_recv=sql_select($sql_fab_recv);
						foreach($result_data_recv as $row)
						{
							$finnish_fab_recv_qty[$row[csf('knitting_company')]]['finish_recv']+=$row[csf('finish_recv')];
							$finnish_fab_recv_qty[$row[csf('knitting_company')]]['grey_used_qty']+=$grey_used_arr[$row[csf('dtls_id')]];	
						}
						
				/*		$sql_fin_recv_ret=("select a.challan_no,c.po_breakdown_id,c.color_id,a.received_mrr_no,
						sum(case when a.entry_form in(46) and a.item_category=2  then c.quantity end) as recv_return
						 from inv_issue_master a,  inv_transaction b,order_wise_pro_details c where a.id=b.mst_id and c.trans_id=b.id  and a.company_id=$cbo_company_name  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by c.color_id,a.challan_no,c.po_breakdown_id,a.received_mrr_no");
						$result_data=sql_select($sql_fin_recv_ret);
						foreach($result_data as $row)
						{
							 $recv_ret_data_qty[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['recv_return']=$row[csf('recv_return')];	
						}*/
						
						$sql_data=("select  a.knit_dye_source,a.knit_dye_company,a.id as issue_id,
						sum(case when a.entry_form in (16,61) and a.item_category=13 then b.issue_qnty end) as issue_qnty,
						sum(case when a.entry_form=45 and a.item_category=13  then b.issue_qnty end) as recv_return_qnty
						from inv_issue_master a, inv_grey_fabric_issue_dtls b where a.id=b.mst_id  and a.company_id=$cbo_company_name   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.entry_form in (16,61)  $dyeing_company_cond $dyeing_source_cond $date_cond group by   a.knit_dye_source,a.knit_dye_company,a.id order by a.knit_dye_company");
					$dataArray=sql_select($sql_data);
					
					foreach($dataArray as $row)
                    {
							$dye_source=$row[csf('knit_dye_source')];
							if($dye_source==1)
							{
								
								$knitting_party_name=$company_arr[$row[csf('knit_dye_company')]];
							}
							else
							{
								$knitting_party_name=$supplier_arr[$row[csf('knit_dye_company')]];
								//echo $dye_source.'dd'.$knitting_party_name.', ';
							}
							$issue_ret_qty=$issue_ret_data_qty[$row[csf('knit_dye_company')]][$row[csf('issue_id')]]['ret_qty'];
							
							//echo $grey_used_qty.'DD';
							$dyeing_party_arr[$row[csf('knit_dye_company')]]['issue_qnty']+=$row[csf('issue_qnty')];
							$dyeing_party_arr[$row[csf('knit_dye_company')]]['recv_return_qnty']+=$row[csf('recv_return_qnty')];
							$dyeing_party_arr[$row[csf('knit_dye_company')]]['issue_ret_qty']+=$issue_ret_qty;
							$dyeing_party_arr[$row[csf('knit_dye_company')]]['finish_recv']+=$finish_recv;
							$dyeing_party_arr[$row[csf('knit_dye_company')]]['grey_used_qty']+=$grey_used_qty;
							$dyeing_party_arr[$row[csf('knit_dye_company')]]['party_name']=$knitting_party_name;
							$dyeing_party_arr[$row[csf('knit_dye_company')]]['issue_id'].=$row[csf('issue_id')].',';		
					}
					//print_r($dyeing_party_arr);
					$i=1; $k=1;$total_issue_qty=0;$total_grey_issue_return_qty=0;$total_fin_recv_qty=0;$total_fin_grey_used_qty=$total_recv_balance_qty=$total_process_loss_qty=$total_fin_grey_used_qty=0;
                    
                    foreach($dyeing_party_arr as $party_id=>$val)
                    {
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$issue_ids=rtrim($val['issue_id'],',');
							$issue_ids=array_unique(explode(",",$issue_ids));
							 $issue_ret_qty=$issue_ret_data_qty[$party_id]['ret_qty'];
						
							$actual_delivery_qty=$val[('issue_qnty')]-$issue_ret_qty;
							//$finish_recv_qty=$val['prod_id'];
							$finish_recv_qty=$finnish_fab_recv_qty[$party_id]['finish_recv'];
							
							$grey_used_qty=$finnish_fab_recv_qty[$party_id]['grey_used_qty'];//$val['grey_used_qty'];
							$recv_balance_qty=$actual_delivery_qty-$grey_used_qty;
							$process_loss_qty=(($actual_delivery_qty-$finish_recv_qty)/$actual_delivery_qty*100);
							?>
                        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="200"><p><? echo $val[('party_name')]; ?></p></td>
                            <td width="100" align="right"><p><a href='#report_details' onClick="openmypage_delivery('<? echo $party_id; ?>','<? echo $dyeing_source; ?>','<? echo $from_date; ?>','<? echo $to_date; ?>','1540px','grey_issue_popup',1);"><? echo number_format($val[('issue_qnty')],2,'.',''); ?></a></p></td>  
                           
                            <td width="100" align="right"><p><!--<a href='#report_details' onClick="openmypage_delivery('<? //echo $party_id; ?>','<? //echo $dyeing_source; ?>','<? //echo $from_date; ?>','<? //echo $to_date; ?>','540px','grey_issue_return_popup',2);"><? //echo number_format($issue_ret_qty,2,'.',''); ?></a>--><? echo  number_format($issue_ret_qty,2); ?></p></td>
                            <td width="100" align="right"><div style="word-wrap:break-word; width:100px;"><? echo number_format($actual_delivery_qty,2); ?></div></td>
                            <td width="100" align="right"><p><a href='#report_details' onClick="openmypage_delivery('<? echo $party_id; ?>','<? echo $dyeing_source; ?>','<? echo $from_date; ?>','<? echo $to_date; ?>','1320px','fin_fab_recv_popup',3);"><? echo number_format($finish_recv_qty,2,'.',''); ?></a><? //echo  number_format($finish_recv_qty,2); ?></p></td>
                            <td width="100" align="right" title="Grey Used"><p><? echo number_format($grey_used_qty,2,'.',''); ?></p></td>
                            <td width="100" align="right"><p><? echo number_format($recv_balance_qty,2,'.',''); ?></p></td>
                            <td width="" align="right" title="(Actual Delivery-Fin Recv)/Actual Delivery*100"><p><?  echo number_format($process_loss_qty,2,'.',''); ?></p></td>
                           
                        </tr>
						<? 
                            $total_grey_issue_return_qty+=$issue_ret_qty;
                            $total_fin_recv_qty+=$finish_recv_qty;
                            $total_fin_grey_used_qty+=$grey_used_qty;
							$total_actual_delivery_qty+=$actual_delivery_qty;
                            $total_issue_qty+=$val[('issue_qnty')];
							$total_process_loss_qty+=$process_loss_qty;
							$total_recv_balance_qty+=$recv_balance_qty;
                      
                            $i++;
						} 
						?>
                     </table>
                      <table width="930" border="1" cellpadding="0" cellspacing="0" class="tbl_bottom" rules="all" > 
                       <tr>
                            <td width="30">&nbsp;</th>
                            <td width="200">Total</th> 
                            <td width="100" id="value_tot_issue"><? echo number_format($total_issue_qty,2); ?></th> 
                            <td width="100" id="value_tot_issue_ret"><? echo number_format($total_grey_issue_return_qty,2); ?></th> 
                            <td width="100" id="value_tot_fin_rev"><? echo number_format($total_actual_delivery_qty,2); ?></th>
                            <td width="100"><? echo number_format($total_fin_recv_qty,2); ?></th>
                            <td width="100"><? echo number_format($total_fin_grey_used_qty,2); ?></th>
                            <td width="100"><? echo number_format($total_recv_balance_qty,2); ?></th>
                            <td width="" align="right"><? //echo number_format($total_process_loss_qty,2);  ?></td>
                           </tr>
                  </table>  
            </div>
            </div>
        </fieldset> 
	<?
	 //Color Wise End
	
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename####$reportType";
	
	exit();
}


if($action=="grey_issue_popup")
{
	echo load_html_head_contents("Grey Issue Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$product_details_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
	$color_name_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$yarncount = return_library_array("select id, yarn_count from  lib_yarn_count", 'id', 'yarn_count');
	$brand_name_arr = return_library_array("select id, brand_name from  lib_brand", 'id', 'brand_name');
	$buyer_name_arr = return_library_array("select id, buyer_name from  lib_buyer", 'id', 'buyer_name');

	?>

<script>
		function print_window()
		{
			$('.fltrow').hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="all"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			$('.fltrow').show();
		}
		function exportToExcel()
		{
			$(".fltrow").hide();
			var tableData = document.getElementById("report_container").innerHTML;
			var data_type = 'data:application/vnd.ms-excel;base64,',
			template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table border="2px">{table}</table></body></html>',
			base64 = function (s) {
				return window.btoa(unescape(encodeURIComponent(s)))
			},
			format = function (s, c) {
				return s.replace(/{(\w+)}/g, function (m, p) { return c[p]; })
			}
			
			var ctx = {
				worksheet: 'Worksheet',
				table: tableData
			}
			
			document.getElementById('dlink').href = data_type + base64(format(template, ctx));
			document.getElementById('dlink').traget = "_blank";
			document.getElementById('dlink').click();
			$(".fltrow").show();
		}	
	</script>
<fieldset style="width:<? echo $popup_width;?>; margin-left:3px">
            <div style="width:1490;" align="center">
        	  <input  type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
        	  <a href="##" id="dlink" onClick="exportToExcel()"><input  type="button" value="Excel Download"  style="width:110px"  class="formbutton"/></a>
            </div>

		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="1490" cellpadding="0" cellspacing="0">
                <thead>
                	<tr>
                        <th colspan="19"><b>Grey Delivery Details</b></th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="70">Date</th>
                        <th width="120">Challan/Issue Id</th>
                        <th width="130">Item Description</th>
                        <th width="80">Buyer</th>
                        <th width="80">Style</th>
                        <th width="100">Job </th>
                        <th width="100">Body Part</th>
                        <th width="60">Stich Length</th>
                        <th width="60">GSM</th>
                        <th width="60">Fin. Dia</th>
                        <th width="60">M/C Dia</th>
                        <th width="60">M/C Gauge</th>
                        <th width="80">Color</th>
                        <th width="80">Count</th>
                        <th width="80">Brand</th>
                        <th width="80">Yarn Lot</th>
                        <th width="80">Issue Qty</th>
                        <th width="">No of Roll</th>
                    </tr>
				</thead>
             </table>
             <div style="width:1510px; max-height:410px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="1490" cellpadding="0" cellspacing="0" id="table_body">
                    <?
                    $i=1; $issue_to='';
					//$colors_id=explode(",",$colors);
					if($to_date!='' && $from_date!='' ) $issye_date_cond=" and a.issue_date between '$from_date' and '$to_date'";
					else $issye_date_cond="";
					$party_id=str_replace("'","",$party_id);
					$knit_source=str_replace("'","",$knit_source);
					if($knit_source!=0)
					{ 
						$dyeing_source_cond=" and a.knit_dye_source in($knit_source)";
						
					}
					else 
					{ 
						$dyeing_source_cond="";
					}
					if ($party_id!='' || $party_id!=0) 
					{
						 $dyeing_company_cond=" and a.knit_dye_company in($party_id)";
						 
					}
					else
					 {
						 $dyeing_company_cond="";
					 }
					//echo $party_id;
					$machine_lib_sql = sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
					foreach ($machine_lib_sql as $row) {
						$dya_gauge_arr[$row[csf("id")]]["machine_no"] = $row[csf("machine_no")];
						$dya_gauge_arr[$row[csf("id")]]["dia_width"] = $row[csf("dia_width")];
						$dya_gauge_arr[$row[csf("id")]]["gauge"] = $row[csf("gauge")];
					}
					
					
								
                											
                 $sql_data="select a.id, a.knit_dye_source,a.knit_dye_company,a.issue_date,a.issue_number,b.id as dtls_id,b.prod_id,b.no_of_roll,b.roll_po_id,b.color_id,b.location_id,b.machine_id,b.stitch_length,b.yarn_lot,b.yarn_count,b.brand_id,
						(case when a.entry_form in (16,61) and a.item_category=13 then b.issue_qnty end) as issue_qnty,
						(case when a.entry_form=45 and a.item_category=13  then b.issue_qnty end) as recv_return_qnty
						from inv_issue_master a, inv_grey_fabric_issue_dtls b where a.id=b.mst_id  and a.company_id=$companyID   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.entry_form in (16,61) $issye_date_cond  $dyeing_company_cond $dyeing_source_cond order by a.issue_number,b.prod_id";
					$dataArray=sql_select($sql_data);
					$dtls_ids='';$barcode_no='';$po_ids='';
					foreach($dataArray as $row)
                    { 
						$dtls_ids.=$row[csf('dtls_id')].',';
					}
					$dtls_ids=chop($dtls_ids,',');
					$dtls_ids=implode(',',array_unique(explode(",",$dtls_ids)));
					if($dtls_ids!='') $dtls_ids=$dtls_ids;else $dtls_ids=0;
					
					
					$roll_sql ="select a.barcode_no,a.dtls_id,a.po_breakdown_id as po_id,b.prod_id,b.mst_id,b.color_id from  pro_roll_details a,inv_grey_fabric_issue_dtls b where  b.id=a.dtls_id and a.entry_form in(61) and a.dtls_id in($dtls_ids) and b.status_active=1 and b.is_deleted=0 ";
					$dataResult=sql_select($roll_sql);
					foreach ($dataResult as $row) {
						$roll_no_data_arr[$row[csf("dtls_id")]][$row[csf("prod_id")]]["barcode_no"] = $row[csf("barcode_no")];
						$roll_no_data_arr[$row[csf("dtls_id")]][$row[csf("prod_id")]]["po_id"] = $row[csf("po_id")];
						//$job_data_arr[$row[csf("po_id")]]["job_no"] = $row[csf("job_no")];
						//$job_data_arr[$row[csf("po_id")]]["style"] = $row[csf("style_ref_no")];
						$barcode_no.=$row[csf('barcode_no')].',';
						$tot_no_of_roll=count($row[csf('barcode_no')]);
						$roll_no_data_arr2[$row[csf("mst_id")]][$row[csf("prod_id")]][$row[csf("color_id")]]["no_of_roll"]+=$tot_no_of_roll;
						$po_ids.=$row[csf('po_id')].',';
					}
					$po_ids=chop($po_ids,',');
					$po_ids=implode(',',array_unique(explode(",",$po_ids)));
					if($po_ids!='') $po_ids=$po_ids;else $po_ids=0;
					
					$job_sql = sql_select("select a.job_no,a.style_ref_no,a.buyer_name, b.po_number, b.id as po_id from  wo_po_details_master a, wo_po_break_down b where b.job_no_mst=a.job_no and  a.company_name=$companyID and b.status_active=1 and b.is_deleted=0 and b.id in($po_ids) ");
					foreach ($job_sql as $row) {
						$job_data_arr[$row[csf("po_id")]]["buyer_name"] = $row[csf("buyer_name")];
						$job_data_arr[$row[csf("po_id")]]["job_no"] = $row[csf("job_no")];
						$job_data_arr[$row[csf("po_id")]]["style"] = $row[csf("style_ref_no")];
					}		
					
					$barcode_nos=chop($barcode_no,',');
					$barcode_nos=implode(',',array_unique(explode(",",$barcode_nos)));
					if($barcode_nos!='') $barcode_nos=$barcode_nos;else $barcode_nos=0;
					$data_prod=sql_select("SELECT a.id, a.entry_form, a.company_id, a.knitting_source, a.knitting_company, b.prod_id, b.body_part_id, b.febric_description_id, b.gsm, b.width, b.color_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.width,b.machine_no_id, b.brand_id, b.rack, b.self,c.po_breakdown_id as po_id, c.barcode_no, c.roll_no, c.booking_no as bwo, c.booking_without_order, c.is_sales FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_nos)");
					foreach($data_prod as $row)
					{
						$roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_id")]][$row[csf("prod_id")]]["body_part_id"] = $row[csf("body_part_id")];
						$roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_id")]][$row[csf("prod_id")]]["brand_id"] = $row[csf("brand_id")];
						$roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_id")]][$row[csf("prod_id")]]["gsm"] = $row[csf("gsm")];
						$roll_data_arr[$row[csf("barcode_no")]][$row[csf("po_id")]][$row[csf("prod_id")]]["width"] = $row[csf("width")];
					}
					
                  
        			foreach($dataArray as $row)
                    { 
						//$issue_id_arr[]=$row[csf('id')];
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
							$y_count = array_unique(explode(",", $row[csf('yarn_count')]));
							//$brand_id = array_unique(explode(",", $row[csf('brand_id')]));
							$yarn_count_value = "";
							foreach ($y_count as $val) {
							if ($val > 0) {
							if ($yarn_count_value == '') $yarn_count_value = $yarncount[$val]; else $yarn_count_value .= ", " . $yarncount[$val];
							}
							}
							/*$brand_value = "";
							foreach ($brand_id as $bid) {
							if ($bid > 0) {
							if ($brand_value == '') $brand_value = $brand_name_arr[$bid]; else $brand_value .= ", " . $brand_name_arr[$bid];
							}
							}*/
							$po_id=$roll_no_data_arr[$row[csf("dtls_id")]][$row[csf("prod_id")]]["po_id"];
							$barcode_no=$roll_no_data_arr[$row[csf("dtls_id")]][$row[csf("prod_id")]]["barcode_no"];
							$no_of_roll=$roll_no_data_arr2[$row[csf("id")]][$row[csf("prod_id")]][$row[csf("color_id")]]["no_of_roll"];
							//echo $po_id.'dff';
							$brand_value=$brand_name_arr[$roll_data_arr[$barcode_no][$po_id][$row[csf("prod_id")]]["brand_id"]];
							$body_part_name=$body_part[$roll_data_arr[$barcode_no][$po_id][$row[csf("prod_id")]]["body_part_id"]];
							$gsm=$roll_data_arr[$barcode_no][$po_id][$row[csf("prod_id")]]["gsm"] ;
							$width=$roll_data_arr[$barcode_no][$po_id][$row[csf("prod_id")]]["width"] ;
							
	
                    
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="70"><p><? echo $row[csf('issue_date')]; ?></p></td>
                            <td width="120"><? echo $row[csf('issue_number')];// ?></td>
                            <td width="130"><div style="word-break:break-all"><? echo $product_details_arr[$row[csf('prod_id')]]; ?></div></td>
                            <td width="80"><div style="word-break:break-all"><? echo $buyer_name_arr[$job_data_arr[$po_id]["buyer_name"]]; ?></div></td>
                            <td width="80"><div style="word-break:break-all"><? echo $job_data_arr[$po_id]["style"]; ?></div></td>
                            <td width="100"><div style="word-break:break-all"><? echo $job_data_arr[$po_id]["job_no"] ; ?></div></td>
                            <td width="100"><div style="word-break:break-all"><? echo $body_part_name; ?></div></td>
                            <td width="60"><? echo $row[csf('stitch_length')]; ?>&nbsp;</td>
                            <td width="60"><? echo $gsm; ?>&nbsp;</td>
                            <td width="60"><? echo $width; ?>&nbsp;</td>
                            <td width="60"><? echo $dya_gauge_arr[$row[csf("machine_id")]]["dia_width"] ?>&nbsp;</td> 
                            <td width="60"><? echo $dya_gauge_arr[$row[csf("machine_id")]]["gauge"]; ?>&nbsp;</td>
                            <td width="80"><? echo $color_name_arr[$row[csf('color_id')]]; ?></td>
                            <td width="80"><div style="word-break:break-all"><? echo $yarn_count_value; ?></div></td>
                            <td width="80"><? echo $brand_value; ?></td>
                              
                            <td width="80" align="center"><div style="word-break:break-all"><? echo $row[csf('yarn_lot')]; ?></div></td>
                            <td width="80" align="right">
								<?
                                        echo number_format($row[csf('issue_qnty')],2);
                                        $total_issue_qnty+=$row[csf('issue_qnty')];
                                   
                                ?>
                            </td>
                            <td align="right">
                                <?
                                      echo $no_of_roll; 
                                      
                                ?>
                                
                            </td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                    	<tr>
                            <th colspan="17" align="right">Total</th>
                            <th align="right"><? echo number_format($total_issue_qnty,2); ?></th>
                            <th align="right"><? // echo number_format($total_issue_qnty_ret,2); ?></th>
                        </tr>
                        
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>
  <script>
  setFilterGrid("table_body",-1);
  </script>
    <br>
    
    
    <?
	exit();
}
if($action=="grey_issue_return_popup")
{
	echo load_html_head_contents("WO Issue Return Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	?>
<fieldset style="width:540px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="540" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="6"><b>Issue Return Info</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="100">Transfer date</th>
                    <th width="120">Issue No</th>
                    <th width="80">Challan</th>
                    <th width="100">Return Qty</th>
                    <th>Remarks</th>
                    
				</thead>
             </table>
             <div style="width:560px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="540" cellpadding="0" cellspacing="0">
                    <?
				
					//and a.company_id=$companyID   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.entry_form in (16,61) $issye_date_cond  $dyeing_company_cond $dyeing_source_cond
						if($to_date!='' && $from_date!='' ) $issye_date_cond=" and a.receive_date between '$from_date' and '$to_date'";
						else $issye_date_cond="";
						$party_id=str_replace("'","",$party_id);
						$knit_source=str_replace("'","",$knit_source);
						if($knit_source!=0)
						{ 
						//$dyeing_source_cond=" and a.knitting_source in($knit_source)";
						
						}
						else 
						{ 
						$dyeing_source_cond="";
						}
						if ($party_id!='' || $party_id!=0) 
						{
						$dyeing_company_cond=" and a.company_id in($party_id)";
						
						}
						else
						{
						$dyeing_company_cond="";
						}
					 
					
                    $i=1;
                    $total_issue_ret_qnty=0; $dye_company=''; $recv_data_arr=array();
                  	$sql_issue=("select a.knitting_source,a.challan_no,a.recv_number,a.company_id,b.receive_date,b.remarks
						sum(case when a.entry_form in(51,84) and a.item_category=13  then b.cons_quantity end) as issue_return
						 from inv_receive_master a,  inv_transaction b where a.id=b.mst_id and b.transaction_type=4   and a.entry_form in(51,84)  and a.company_id=$companyID  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $issye_date_cond  $dyeing_company_cond $dyeing_source_cond");
						$result_data=sql_select($sql_issue);
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                        $total_issue_ret_qnty+=$row[csf('issue_return')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="120"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
                             <td width="120"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="80" align="center"><? echo $row[csf('challan_no')]; ?></td>
                            <td width="100"><? echo number_format($row[csf('quantity')],2); ?></td>
                            <td  align="right"><? echo $row[csf('remarks')]; ?></td>
                            
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                        <th colspan="4" align="right">Total</th>
                        <th align="right"><? echo number_format($total_issue_ret_qnty,2); ?></th>
                         <th align="right"><? //echo number_format($total_issue_ret_qnty,2); ?></th>
                        
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>
    <?
	exit();
}
if($action=="fin_fab_recv_popup")
{
	echo load_html_head_contents("WO Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$product_details_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
	$color_name_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$batch_no_arr=return_library_array( "select id, batch_no from pro_batch_create_mst", "id", "batch_no");
	$buyer_name_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	?>
<fieldset style="width:1320px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="1300" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="14"><b>Fabric Receive Info</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="120">System Id</th>
                    <th width="100">Rec. Date</th>
                    <th width="100">Challan No</th>
                    <th width="100">Batch No</th>
                    <th width="100">Color</th>
                    <th width="130">Fabric Description</th>
                    <th width="100">Buyer</th>
                    <th width="100">Style</th>
                    <th width="100">Job No.</th>
                    <th width="60">F/Dia</th>
                    <th width="80">Grey Used Qty.</th>
                    <th width="80">Fin. Rcv Qty</th>
                    <th>Collar/Cuff Pcs</th>
                    
				</thead>
             </table>
             <div style="width:1320px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="1300" cellpadding="0" cellspacing="0" id="tbl_search">
                    <?
					$job_sql = sql_select("select a.job_no,a.style_ref_no,a.buyer_name, b.po_number, b.id as po_id from  wo_po_details_master a, wo_po_break_down b where b.job_no_mst=a.job_no and  a.company_name=$companyID and b.status_active=1 and b.is_deleted=0  ");
					foreach ($job_sql as $row) {
						$job_data_arr[$row[csf("po_id")]]["buyer_name"] = $row[csf("buyer_name")];
						$job_data_arr[$row[csf("po_id")]]["job_no"] = $row[csf("job_no")];
						$job_data_arr[$row[csf("po_id")]]["style"] = $row[csf("style_ref_no")];
					}	
					
					if($to_date!='' && $from_date!='' ) $issye_date_cond=" and a.receive_date between '$from_date' and '$to_date'";
					else $issye_date_cond="";
					$party_id=str_replace("'","",$party_id);
					$knit_source=str_replace("'","",$knit_source);
					if($knit_source!=0)
					{ 
						$dyeing_source_cond=" and a.knitting_source in($knit_source)";
						
					}
					else 
					{ 
						$dyeing_source_cond="";
					}
					if ($party_id!='' || $party_id!=0) 
					{
						 $dyeing_company_cond=" and a.knitting_company in($party_id)";
						 
					}
					else
					 {
						 $dyeing_company_cond="";
					 }
					 $material_data=sql_select("select dtls_id,used_qty from pro_material_used_dtls where  entry_form=37 and item_category=13 ");
					foreach($material_data as $value)
					{
						$grey_used_arr[$value[csf('dtls_id')]]=$value[csf('used_qty')];
						
					}
					 
                    $i=1;
                    $total_fabric_recv_qnty=$total_grey_used_qty=0;
                  $sql_fab_recv=("select a.recv_number,a.receive_date,a.challan_no,c.id as dtls_id,c.prod_id,c.batch_id,c.color_id,c.width,c.order_id,c.body_part_id,
						(case when a.entry_form in(37) and a.item_category=2  then c.receive_qnty end) as finish_recv,
						(case when a.entry_form in(37) and a.item_category=2  then c.grey_used_qty end) as grey_used_qty
						 from inv_receive_master a,  inv_transaction b,pro_finish_fabric_rcv_dtls c where a.id=b.mst_id and b.id=c.trans_id and b.transaction_type in(1) and a.company_id=$companyID  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $dyeing_company_cond $dyeing_source_cond  $issye_date_cond ");
                    $result=sql_select($sql_fab_recv);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
						$order_ids=array_unique(explode(",",$row[csf('order_id')]));
						$bauyer_names='';	$styles='';$jobNo='';
						foreach($order_ids as $pid)
						{
								$buyer_name=$job_data_arr[$pid]["buyer_name"];
								$style=$job_data_arr[$pid]["style"];$job_no=$job_data_arr[$pid]["job_no"];
								if($bauyer_names=='') $bauyer_names=$buyer_name_arr[$buyer_name];else $bauyer_names.=",".$buyer_name_arr[$buyer_name];
								if($styles=='') $styles=$style;else $styles.=",".$style;
								if($jobNo=='') $jobNo=$job_no;else $jobNo.=",".$job_no;
						}
						$grey_used_qty=$grey_used_arr[$row[csf('dtls_id')]];
                        $total_fabric_recv_qnty+=$row[csf('finish_recv')];
						$total_grey_used_qty+=$grey_used_qty;
					
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="100"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
                            <td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="100"><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
                            <td width="100"><p><? echo $color_name_arr[$row[csf('color_id')]]; ?></p></td>
                            <td width="130"><p><? echo $product_details_arr[$row[csf('prod_id')]]; ?></p></td>
                            <td width="100"><p><? echo implode(',',array_unique(explode(",",$bauyer_names))); ?></p></td>
                            <td width="100"><p><? echo implode(',',array_unique(explode(",",$styles))); ?></p></td>
                            <td width="100"><p><? echo implode(',',array_unique(explode(",",$jobNo))); ?></p></td> 
                            <td width="60"><p><? echo $row[csf('width')]; ?></p></td>
                            <td width="80" align="right"><? echo number_format($grey_used_qty); ?></td>
                            <td  width="80" align="right"><? echo number_format($row[csf('finish_recv')],2); ?></td>
                           
                            <td  align="right"><? echo $body_part[$row[csf('body_part_id')]]; ?></td>
                            
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                        <th colspan="11" align="right">Total</th>
                        
                        <th align="right"><? echo number_format($total_grey_used_qty,2); ?></th>
                        <th align="right"><? echo number_format($total_fabric_recv_qnty,2); ?></th>
                       
                        <th align="right"><? //echo number_format($total_fabric_recv_qnty,2); ?></th>
                        
                    </tfoot>
                </table>
            </div>	
        </div>
	<script>
    		setFilterGrid("tbl_search",-1);
    </script>
	</fieldset>
    <?
	exit();
}
if($action=="style_grey_issue_popup")
{
	echo load_html_head_contents("WO Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	?>
<fieldset style="width:650px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="640" cellpadding="0" cellspacing="0">
                <thead>
                	<tr>
                        <th colspan="7"><b>Grey Issue Info</b></th>
                    </tr>
                    <tr>
                        <th width="40">SL</th>
                        <th width="120">Issue Id</th>
                        <th width="100">Issue Purpose</th>
                        <th width="100">Booking No</th>
                        <th width="80">Issue Date</th>
                        <th width="100">Issue Qty</th>
                        <th>Recv.Return Qty</th>
                    </tr>
				</thead>
             </table>
             <div style="width:660px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="640" cellpadding="0" cellspacing="0">
                    <?
					$sql_data_grey_ret=("select   a.received_id,
						sum(case when c.entry_form in (46) then c.quantity end) as recv_return
						
						from inv_issue_master a, inv_transaction b,order_wise_pro_details c where a.id=b.mst_id  and c.trans_id=b.id and a.knit_dye_company=$knit_source  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   and c.po_breakdown_id in($po_id) and a.item_category=2 and a.entry_form in(46) and c.entry_form in(46)  and a.knit_dye_source=$knit_source  
						 group by   a.received_id ");
						 $result_data_rec_ret=sql_select($sql_data_grey_ret);
						 $recv_ret_data_qty=array();
						foreach($result_data_rec_ret as $row)
						{
							 $recv_ret_data_qty[$row[csf('received_id')]]['ret_qty']=$row[csf('recv_return')];
						}
						
                    $i=1; $issue_to='';
					//$colors_id=explode(",",$colors);
					$knitting_company=str_replace("'","",$knitting_company);
					if($knitting_company!=0)
					{
						$kint_com="and a.knit_dye_company=$knitting_company";
					}
					else
					{
						$kint_com="";
					}
					
					if($issue_number!='') $issue_number_cond="and a.issue_number ='$issue_number'";
					if($db_type==2)
					{
					if($colors!='') $color_con="and  b.color_id like '%$colors%' ";else  $color_con="and b.color_id is null";
					}
					else
					{
						if($colors!='') $color_con="and  b.color_id like '%$colors%' ";else  $color_con="and b.color_id='' ";
					}
					//if($po_id!='') $po_id_con="and  b.color_id like '%$colors%' ";else  $po_id_con="and b.color_id is null"; issue_number style
                $sql="select a.id,a.issue_number,a.received_id, a.issue_date, a.issue_purpose,a.booking_no,
				  sum(d.quantity) as issue_qnty
				 
				  from inv_issue_master a, inv_grey_fabric_issue_dtls b,product_details_master c,order_wise_pro_details d,wo_po_break_down e,wo_po_details_master f where a.id=b.mst_id and b.prod_id=c.id and d.dtls_id=b.id and f.job_no=e.job_no_mst and d.po_breakdown_id=e.id and a.entry_form in(16,61) and d.entry_form in(16,61)  and a.knit_dye_source=$knit_source and c.detarmination_id=$deter_id and a.id in($issue_number) and f.style_ref_no='$style'  and d.po_breakdown_id in($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $kint_com group by a.id,a.issue_number,a.received_id, a.issue_date, a.issue_purpose,a.booking_no";
                    $result=sql_select($sql); // 	
        			foreach($result as $row)
                    { 
						$issue_id_arr[]=$row[csf('id')];
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
							$ret_qty=$recv_ret_data_qty[$row[csf('received_id')]]['ret_qty'];
                    
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="40"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="100"><? echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></td>
                            <td width="100"><? echo $row[csf('booking_no')]; ?>&nbsp;</td>
                            <td width="80" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                            <td width="100" align="right">
								<?
                                        echo number_format($row[csf('issue_qnty')],2);
                                        $total_issue_qnty+=$row[csf('issue_qnty')];
                                   
                                ?>
                            </td>
                            <td align="right">
                                <?
                                        echo number_format($ret_qty,2);
                                        $total_issue_qnty_ret+=$ret_qty;
                                ?>
                            </td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                    	<tr>
                            <th colspan="5" align="right">Total</th>
                            <th align="right"><? echo number_format($total_issue_qnty,2); ?></th>
                            <th align="right"><? echo number_format($total_issue_qnty_ret,2); ?></th>
                        </tr>
                        <tr>
                            <th colspan="5" align="right">Grand Total</th>
                            <th align="right" colspan="2"><? echo number_format($total_issue_qnty+$total_issue_qnty_ret,2); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>
  
    <br>
    
    
    <?
	exit();
}



//style start...
if($action=="fin_fab_recv_popup_style")
{
	echo load_html_head_contents("Finish Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$batch_booking_no_arr=return_library_array( "select a.id, a.booking_no from pro_batch_create_mst a ,pro_batch_create_dtls b where a.id=b.mst_id", "id", "booking_no");
	$color_name_arr=return_library_array( "select a.id, a.color_name from lib_color a ", "id", "color_name");
	?>
<fieldset style="width:650px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="640" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="7"><b>Fabric Receive Info</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="120">System Id</th>
                    <th width="120">Booking No</th>
                    <th width="80">Rec. Date</th>
                    <th width="100">Rec. Basis</th>
                    <th width="100">Color</th>
                    <th>Receive Qnty</th>
                    
				</thead>
             </table>
             <div style="width:660px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="640" cellpadding="0" cellspacing="0">
                    <?
					//if($colors!='') $color_con="and  b.color_id like '%$colors%' ";else  $color_con="and b.color_id is null";
					//if($po_id!='') $po_id_con="and  b.color_id like '%$colors%' ";else  $po_id_con="and b.color_id is null";
					
                    $knitting_company=str_replace("'","",$knitting_company);
					if($knitting_company!=0)
					{
						$kint_com="and a.knitting_company=$knitting_company";
					}
					else
					{
						$kint_com="";
					}
					
					//b.pi_wo_batch_no as batch_no
					//style,colors,prod_id,deter_id
					//
					 /*$sql_fab_recv=("select b.id as dtls_id ,b.gsm,b.width,c.po_breakdown_id,c.color_id,a.knitting_company,d.detarmination_id as deter_id,
						sum(case when c.entry_form in(7,37) and a.item_category in(2)  then b.grey_used_qty end) as grey_used_qty,
						sum(case when c.entry_form in(7,37) and a.item_category=2  then c.returnable_qnty end) as reject_qty,
						sum(case when c.entry_form in(7,37) and a.item_category in(2,13)  then c.quantity  end) as finish_recv
						 from inv_receive_master a,  pro_finish_fabric_rcv_dtls b,order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id  and d.id=c.prod_id  and d.id=b.prod_id and a.company_id=$cbo_company_name  and c.entry_form in(7,37) and  a.item_category in(2,13) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $po_id_cond  group by b.id,a.knitting_company,c.po_breakdown_id,b.gsm,b.width,c.color_id,d.detarmination_id");*///and e.gsm=$gsm and e.width='$dia'
					$colors=str_replace("'","",$colors);
					//echo $style;
					$booking_no=str_replace("'","",$prod_id);
					$diagsm=explode("_",$gsm_dia);
					$gsm=$diagsm[0];
					$dia=$diagsm[1];
					if($colors!=0) $colors_con=" and c.color_id in($colors)";else $colors_con="and c.color_id in(0)";
					$i=1;
                    $total_fabric_recv_qnty=0; $dye_company=''; $recv_data_arr=array();
					  $sql=("select a.booking_no, a.recv_number, a.receive_date, a.receive_basis,c.prod_id,c.color_id,
						sum(case when c.entry_form in(37,7)   then c.quantity  end) as finish_recv
						 from inv_receive_master a,  pro_finish_fabric_rcv_dtls b,order_wise_pro_details c, product_details_master d,wo_po_break_down e,wo_po_details_master f where a.id=b.mst_id and b.id=c.dtls_id  and d.id=c.prod_id  and d.id=b.prod_id and  e.job_no_mst=f.job_no and c.po_breakdown_id=e.id  and c.entry_form in(37,7)  and a.entry_form in(37,7) and  a.item_category in(2,13) and  d.item_category_id in(2,13) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0   and b.fabric_description_id='$deter_id' and f.style_ref_no='$style' $colors_con $kint_com  group by a.booking_no, a.recv_number, a.receive_date, a.receive_basis,c.prod_id,c.color_id");
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                        $total_fabric_recv_qnty+=$row[csf('finish_recv')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('recv_number')]; ?></p></td>
                             <td width="120"><p><? echo $row[csf('booking_no')]; ?></p></td>
                            <td width="80" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                            <td width="100"><? echo $receive_basis_arr[$row[csf('receive_basis')]]; ?></td>
                            <td width="100"><? echo $color_name_arr[$row[csf('color_id')]]; ?></td>
                            <td  align="right"><? echo number_format($row[csf('finish_recv')],2); ?></td>
                            
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                        <th colspan="6" align="right">Total</th>
                        <th align="right"><? echo number_format($total_fabric_recv_qnty,2); ?></th>
                        
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>
    <?
	exit();
}

?>