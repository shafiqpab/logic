<? 
include('../../../includes/common.php');
session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');
if ($action=="load_fabric_source_from_variable_settings")
{
	$sql_result = sql_select("select dyeing_fin_bill from variable_settings_subcon where company_id = $data and variable_list = 4 and is_deleted = 0 and status_active = 1");
	
	$fabricfrom=array(1=>"Receive",2=>"Production",3=>"Issue"); 
	if($sql_result)
	{
		$data_ids=explode(",", $sql_result[0][csf('dyeing_fin_bill')]);
		$values=$sql_result[0][csf('dyeing_fin_bill')];

		$selected = (count($data_ids)==1)? $data_ids[0] : "0";
	}
	else
	{
		$values=1;
		$selected =1;
	}

	//echo create_drop_down("cbofabricfrom_1", 70, $fabricfrom, "", 1, "--Select --", $selected, "", 0,$values,"","","","","","","fabric_source");
	echo create_drop_down("cbofabricfrom", 70, $fabricfrom, "", 1, "--Select --", $selected, "", 0, $values, "", "", "", "", "", "cbofabricfrom[]");

	/*if($sql_result)
	{
	    foreach($sql_result as $result)
		{
	        echo "$('.fabric_source').val(".$result[csf("dyeing_fin_bill")].");\n";
		}
    }
    else
    {
            echo "$('.fabric_source').val(1);\n";
    }*/
 	exit();
}

if($action=="party_popup")
{
	echo load_html_head_contents("Party Info", "../../../", 1, 1,'','','');
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
        <input type="hidden" name="hide_party_name" id="hide_party_name" value="" />
        <input type="hidden" name="hide_party_id" id="hide_party_id" value="" />
	<?
	$sql="select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$companyID' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name";

	echo create_list_view("tbl_list_search", "Party Name", "400","380","270",0, $sql , "js_set_value", "id,buyer_name", "", 1, "0", $arr , "buyer_name", "",'setFilterGrid("tbl_list_search",-1);','0','',1) ;
	
   exit(); 
} 

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$date_from=str_replace("'","",$txt_date_from);
	$party_id=str_replace("'","",$txt_party_id);
	$value_with=str_replace("'","",$cbo_value_with);
	$fabric_from=str_replace("'","",$cbofabricfrom);
	
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$color_arr=return_library_array( "select id,color_name from lib_color",'id','color_name');

	ob_start();
	?>
        <div align="center">
         <fieldset style="width:950px;">
            <table cellpadding="0" cellspacing="0" width="930">
                <tr  class="form_caption" style="border:none;">
                   <td align="center" colspan="8" width="100%" style="font-size:20px"><strong><? echo $report_title; ?></strong></td>
                </tr>
                <tr class="form_caption" style="border:none;">
                   <td align="center" colspan="8" width="100%"  style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
                </tr>
                <tr class="form_caption" style="border:none;">
                    <td align="center" colspan="8" width="100%" style="font-size:12px">
                        <? if($date_from!="") echo "As On ".change_date_format($date_from);?>
                    </td>
                </tr>
            </table>
             <table width="930" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
                <thead>
                    <th width="30">SL</th>
                    <th width="80">Challan No</th>
                    <th width="80">Cha. Date</th>
                    <th width="250">Receive Item</th>
                    <th width="80">Color</th> 
                    <th width="100">Net Rec. Qty</th>                            
                    <th width="100">Batch Qty</th>
                    <th width="">Yet to Batch</th>
                </thead>
            </table>
            <div style="max-height:300px; overflow-y:scroll; width:930px" id="scroll_body">
            <table width="910" border="1" class="rpt_table" rules="all" id="table_body">
			<?
			$material_issue_arr=array();
			$material_return_arr=array();
			if ($party_id!='') $party_inv_cond=" and a.party_id in ($party_id)"; else $party_inv_cond="";
			if ($db_type==0)
			{
				$sql_issue="select b.rec_challan, concat(b.material_description,',',b.gsm,',',b.grey_dia,',',b.fin_dia) as description, b.color_id, sum(b.quantity) as quantity from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=2 and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $party_inv_cond group by b.rec_challan, b.material_description, b.gsm, b.grey_dia, b.fin_dia, b.color_id order by b.rec_challan";
				$sql_return="select b.rec_challan, concat(b.material_description,',',b.gsm,',',b.grey_dia,',',b.fin_dia) as description, b.color_id, sum(b.quantity) as quantity from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=3 and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $party_inv_cond group by b.rec_challan, b.material_description, b.gsm, b.grey_dia, b.fin_dia, b.color_id order by b.rec_challan";
			}
			elseif($db_type==2)
			{
				$sql_issue="select b.rec_challan, b.material_description || ',' || b.gsm || ',' || b.grey_dia || ',' || b.fin_dia as description, b.color_id, sum(b.quantity) as quantity from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=2 and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $party_inv_cond group by b.rec_challan, b.material_description, b.gsm, b.grey_dia, b.fin_dia, b.color_id order by b.rec_challan"; 
				$sql_return="select b.rec_challan, b.material_description || ',' || b.gsm || ',' || b.grey_dia || ',' || b.fin_dia as description, b.color_id, sum(b.quantity) as quantity from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=3 and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $party_inv_cond group by b.rec_challan, b.material_description, b.gsm, b.grey_dia, b.fin_dia, b.color_id order by b.rec_challan"; 
			}
			
			$nameArray_issue=sql_select($sql_issue);
			foreach ($nameArray_issue as $row)
			{
				$material_issue_arr[$row[csf('rec_challan')]][$row[csf('description')]][$row[csf('color_id')]]=$row[csf('quantity')];
			}
			
			$nameArray_return=sql_select($sql_return);
			foreach($nameArray_return as $row)
			{
				$material_return_arr[$row[csf('rec_challan')]][$row[csf('description')]][$row[csf('color_id')]]=$row[csf('quantity')];
			}
			//var_dump($material_return_arr[785]);
			unset($nameArray_issue);
			unset($nameArray_return);
			
				$batch_array=array();
				//$sql_batch="select b.prod_id, sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a, pro_batch_create_dtls b where a.company_id=$cbo_company_id and a.id=b.mst_id and a.entry_form=36 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.prod_id";
				$sql_batch="select b.prod_id, sum(b.batch_qnty) as batch_qnty from subcon_ord_mst a, pro_batch_create_dtls b, subcon_ord_dtls c, pro_batch_create_mst d where a.subcon_job=c.job_no_mst and b.po_id=c.id and d.id=b.mst_id and d.entry_form=36 and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  $party_inv_cond group by b.prod_id";
				
				$sql_batch_result=sql_select($sql_batch);
				foreach ($sql_batch_result as $row)
				{
					$batch_array[$row[csf("prod_id")]]=$row[csf("batch_qnty")];
				}
				
				if ($date_from!="") $rec_date_cond=" and a.subcon_date<='$date_from'"; else $rec_date_cond="";
				if ($party_id!='') $party_id_cond=" and a.party_id in ($party_id)"; else $party_id_cond="";
				 $sql_receive="select a.chalan_no, a.subcon_date, a.party_id, b.id as prod_id, b.material_description, b.gsm, b.grey_dia, b.fin_dia, b.color_id, b.subcon_uom, b.quantity from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=1 and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=2 and b.is_deleted=0 $party_id_cond $rec_date_cond order by a.party_id, a.subcon_date";
				//echo $sql_receive;
				$sql_receive_result=sql_select($sql_receive); $party_array=array();
				$i=1; $k=1;
				foreach ($sql_receive_result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$description=""; $net_rec_qty=0;
					$description=$row[csf('material_description')].','.$row[csf('gsm')].','.$row[csf('grey_dia')].','.$row[csf('fin_dia')];
					$issue_qty=$material_issue_arr[$row[csf('chalan_no')]][$description][$row[csf('color_id')]];
					$return_qty=$material_return_arr[$row[csf('chalan_no')]][$description][$row[csf('color_id')]];
					//echo $description.'==='.$issue_qty.'='.$return_qty.'= kausar <br>';
					if (!in_array($row[csf("party_id")],$party_array) )
                    {
						if($k!=1)
						{
                        ?>
                            <tr class="tbl_bottom">
                                <td colspan="5" align="right"><b>Party Total:</b></td>
                                <td align="right"><b><? echo number_format($rec_qty,2); ?></b></td>
                                <td align="right"><b><? echo number_format($batch_qty,2); ?></b></td>
                                <td align="right"><b><? echo number_format($batch_bal_qty,2); ?></b></td>
                            </tr>
                        <?
                            unset($rec_qty);
							unset($batch_qty);
							unset($batch_bal_qty);
                        }
                        ?>
                            <tr bgcolor="#dddddd">
                                <td colspan="8" align="left" ><b>Party Name: <? echo $buyer_arr[$row[csf("party_id")]]; ?></b></td>
                            </tr>
                        <?
                        $party_array[]=$row[csf('party_id')];            
                        $k++;
                    }
					
					if($value_with==2)
					{
						$net_rec_qty=$row[csf("quantity")]-$issue_qty-$return_qty;
						$batch_bal=$net_rec_qty-$batch_array[$row[csf("prod_id")]];
						//$row[csf("quantity")]-$batch_array[$row[csf("prod_id")]];
						$desc_con="";
						if($row[csf("gsm")]) $desc_con=",".$row[csf("gsm")]; 
						else if($row[csf("grey_dia")]) $desc_con.=",".$row[csf("grey_dia")]; 
						else if($row[csf("fin_dia")]) $desc_con.=",".$row[csf("fin_dia")]; 
						$dest_item=$row[csf("material_description")].$desc_con;
						if ($batch_bal!=0)
						{
							?>
							<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								 <td width="30" ><? echo $i; ?></td>
								 <td width="80"><div style="word-break:break-all"><? echo $row[csf("chalan_no")]; ?></div></td>
								 <td width="80" ><? echo change_date_format($row[csf("subcon_date")]); ?></td>
								 <td width="250"><div style="word-break:break-all"><? echo $dest_item;//$row[csf("material_description")].', '.$row[csf("gsm")].', '.$row[csf("grey_dia")].', '.$row[csf("fin_dia")]; ?></div></td>
								 <td width="80" ><p><? echo $color_arr[$row[csf("color_id")]]; ?></p></td>
								 <td width="100" align="right" title="(Rec-Issue-Ret.)"><? echo number_format($net_rec_qty,2,'.',''); ?></td>
								 <td width="100" align="right" title="Prod Id=<? echo $row[csf("prod_id")];?>" ><a href="##" onClick="openmypage_batch_dtls('<? echo $row[csf("prod_id")]; ?>','batch_popup');" ><? echo number_format($batch_array[$row[csf("prod_id")]],2,'.',''); ?></a></td>
								 <td width="" align="right" title="(Rec-Issue-Ret.-Batch)"><? echo number_format($batch_bal,2,'.',''); ?></td>
							</tr>
							<? $i++;
						
							$rec_qty+=$net_rec_qty;
							$batch_qty+=$batch_array[$row[csf("prod_id")]];
							$batch_bal_qty+=$batch_bal;
							
							$grand_rec_qty+=$net_rec_qty;
							$grand_batch_qty+=$batch_array[$row[csf("prod_id")]];
							$grand_batch_bal_qty+=$batch_bal;
						}
					}
					else if($value_with==1)
					{
						//$batch_bal=$row[csf("quantity")]-$batch_array[$row[csf("prod_id")]];
						$net_rec_qty=$row[csf("quantity")]-$issue_qty-$return_qty;
						$batch_bal=$net_rec_qty-$batch_array[$row[csf("prod_id")]];
						
						$desc_con="";
						if($row[csf("gsm")]) $desc_con=",".$row[csf("gsm")]; 
						else if($row[csf("grey_dia")]) $desc_con.=",".$row[csf("grey_dia")]; 
						else if($row[csf("fin_dia")]) $desc_con.=",".$row[csf("fin_dia")]; 
						$dest_item=$row[csf("material_description")].$desc_con;
						?>
						<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							 <td width="30" ><? echo $i; ?></td>
							 <td width="80" ><div style="word-break:break-all"><? echo $row[csf("chalan_no")]; ?></div></td>
							 <td width="80" ><? echo change_date_format($row[csf("subcon_date")]); ?></td>
							 <td width="250" ><div style="word-break:break-all"><? echo $dest_item;// $row[csf("material_description")].', '.$row[csf("gsm")].', '.$row[csf("grey_dia")].', '.$row[csf("fin_dia")]; ?></div></td>
							 <td width="80" ><p><? echo $color_arr[$row[csf("color_id")]]; ?></p></td>
							 <td width="100" align="right" title="(Rec-Issue-Ret.)"><? echo number_format($net_rec_qty,2,'.',''); ?></td>
							 <td width="100" align="right" ><a href="##" onClick="openmypage_batch_dtls('<? echo $row[csf("prod_id")]; ?>','batch_popup');" ><? echo number_format($batch_array[$row[csf("prod_id")]],2,'.',''); ?></a></td>
							 <td width="" align="right" title="(Rec-Issue-Ret.-Batch)"><? echo number_format($batch_bal,2,'.',''); ?></td>
						</tr>
						<? $i++;
					
						$rec_qty+=$net_rec_qty;
						$batch_qty+=$batch_array[$row[csf("prod_id")]];
						$batch_bal_qty+=$batch_bal;
						
						$grand_rec_qty+=$net_rec_qty;
						$grand_batch_qty+=$batch_array[$row[csf("prod_id")]];
						$grand_batch_bal_qty+=$batch_bal;
					}
				}
			?>
                <tr class="tbl_bottom">
                    <td colspan="5" align="right"><b>Party Total:</b></td>
                    <td align="right"><b><? echo number_format($rec_qty,2); ?></b></td>
                    <td align="right"><b><? echo number_format($batch_qty,2); ?></b></td>
                    <td align="right"><b><? echo number_format($batch_bal_qty,2); ?></b></td>
                </tr> 
                <tr class="tbl_bottom">
                    <td colspan="5" align="right"><b>Grand Total:</b></td>
                    <td align="right"><b><? echo number_format($grand_rec_qty,2); ?></b></td>
                    <td align="right"><b><? echo number_format($grand_batch_qty,2); ?></b></td>
                    <td align="right"><b><? echo number_format($grand_batch_bal_qty,2); ?></b></td>
                </tr> 
            </table>
        </div>
    </fieldset>
    </div>
	<?
	$html = ob_get_contents();
	ob_clean();
	$new_link=create_delete_report_file( $html, 1, 1, "../../../" );
	
	echo "$html";
	exit();	
}

if ($action=="batch_popup")
{
	echo load_html_head_contents("Batch Details", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $id;
	$color_arr=return_library_array( "select id,color_name from lib_color",'id','color_name');
	$po_arr=return_library_array( "select id,order_no from subcon_ord_dtls",'id','order_no');
	?>
    <div>
    <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="540">
        <thead>
            <th width="30">SL</th>
            <th width="80">Batch No</th>
            <th width="60">Batch Ext.</th>
            <th width="80">Batch Date</th>
            <th width="100">Color</th>
            <th width="100">Order No</th>
            <th width="">Batch Qty</th>
        </thead>
        <tbody>
        <?
			$batch_sql="select a.batch_no, a.extention_no, a.batch_date, a.color_id, b.po_id, sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.entry_form=36 and b.prod_id='$id' and a.company_id='$cbo_company_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.batch_no, a.extention_no, a.batch_date, a.color_id, b.po_id";
			$i=1;
			$batch_sql_result=sql_select($batch_sql);
			foreach ($batch_sql_result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
					 <td><? echo $i; ?></td>
                     <td><? echo $row[csf("batch_no")]; ?></td>
                     <td><? echo $row[csf("extention_no")]; ?></td>
                     <td><? echo change_date_format($row[csf("batch_date")]); ?></td>
                     <td><? echo $color_arr[$row[csf("color_id")]]; ?></td>
                     <td><? echo $po_arr[$row[csf("po_id")]]; ?></td>
                     <td align="right"><? echo number_format($row[csf("batch_qnty")],2); ?></td>
                 </tr>
                <?
				$batch_qty_tot+=$row[csf("batch_qnty")];
				$i++;
			}
		?>
        </tbody>
        <tfoot>
            <tr class="tbl_bottom">
                <td colspan="6" align="right"><b>Total:</b></td>
                <td align="right"><b><? echo number_format($batch_qty_tot,2); ?></b></td>
            </tr> 
        </tfoot>
    </table>
    </div>
	<?
	exit();
}
?>