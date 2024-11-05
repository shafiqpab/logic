<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
$user_name=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//------------------------------------------------------------------------------------------

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 110, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" );     	 
	exit();
}
if($action == "load_drop_down_supplier"){
	$explode_data = explode("**",$data);
	$data = $explode_data[0];
	$selected_company = $explode_data[1];

	if($data==3)
	{
			echo create_drop_down( "cbo_supplier", 110, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=23 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--Select--", $selected, "" );
		//}
	}
	else if($data==1)
		echo create_drop_down( "cbo_supplier", 110, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name","id,company_name", 1, "--- Select ---", "", "",0,0 );
	else
		echo create_drop_down( "cbo_supplier", 110, $blank_array,"", 1, "--- Select ---", $selected, "",0,0 );
	exit();
	
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$company_id=str_replace("'", "", $cbo_company_name);
	$txt_date_from=str_replace("'", "", $txt_date_from);
	$txt_date_to=str_replace("'", "", $txt_date_to);
	$buyer_id=str_replace("'", "", $cbo_buyer_name);
	$txt_job_no=str_replace("'", "", $txt_job_no);
	$txt_order_no=str_replace("'", "", $txt_order_no);
	$txt_file_no=str_replace("'", "", $txt_file_no);
	$txt_internal_ref=str_replace("'", "", $txt_internal_ref);
	$cbo_supplier_id=str_replace("'", "", $cbo_supplier);
	$cbo_source_id=str_replace("'", "", $cbo_source);
    //echo $cbo_supplier_id.$cbo_source_id;die;
	$txt_order_no_cond= ($txt_order_no=="")? "": "and e.po_number like '%".$txt_order_no."%'";
	$txt_job_no_cond= ($txt_job_no=="")? "": "and d.job_no like '%".$txt_job_no."%'";
	$txt_buyer_cond= ($buyer_id==0)? "": "and d.buyer_name='".$buyer_id."'";
	$internal_ref_cond= ($txt_internal_ref=="")? "": "and e.grouping='".$txt_internal_ref."'";
	$file_no_cond= ($txt_file_no=="")? "": "and e.file_no='".$txt_file_no."'";
	if($cbo_supplier_id !=0){
		$cbo_supplier_id= "AND a.serving_company=$cbo_supplier_id";
	 }else{
		$cbo_supplier_id="";
	 }
	 if($cbo_source_id !=0){
		
		$cbo_source_id= "AND a.production_source='$cbo_source_id'";
	 }else{
		$cbo_source_id="";
	 }
	$buyer_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
	$serving_sup_arr=return_library_array( "select id, supplier_name from  lib_supplier",'id','supplier_name');
	$serving_com_arr=return_library_array( "select id, company_name from  lib_company",'id','company_name');

	if($db_type==0)
	{ 
		$date_cond="and a.production_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."' ";
	}
	else
	{
		$date_cond="and a.production_date between '".change_date_format($txt_date_from, "", "",1)."' and '".change_date_format($txt_date_to, "", "",1)."' ";
	}

	$dataArray=sql_select("SELECT a.id, a.production_type, a.production_source, a.serving_company, a.po_break_down_id, a.embel_name, a.embel_type, sum(b.production_qnty) as production_qnty, d.buyer_name, d.job_no, d.style_ref_no, e.po_number, e.grouping from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c, wo_po_details_master d, wo_po_break_down e where a.company_id='$company_id' $cbo_supplier_id $cbo_source_id $date_cond $txt_order_no_cond $txt_job_no_cond $txt_buyer_cond $internal_ref_cond $file_no_cond and a.id=b.mst_id and a.production_type='2' and b.production_type='2' and a.po_break_down_id=c.po_break_down_id and b.color_size_break_down_id=c.id and d.job_no=e.job_no_mst and d.job_no=c.job_no_mst and e.id=a.po_break_down_id and e.id=c.po_break_down_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 group by a.id, a.production_type, a.production_source, a.serving_company, a.po_break_down_id, a.embel_name, a.embel_type, d.buyer_name, d.job_no, d.style_ref_no, e.po_number, e.grouping order by a.id desc");
?>	
 	<div align="center" style="height:auto; width:auto; margin:0 auto; padding:0;">
 			<script type="text/javascript">
				setFilterGrid('tbl_list_search',-1);
			</script>
        <table width="100%" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
            <thead>
               <tr>
	                <th width="30"></th>
	                <th width="50">SL</th>
	                <th width="80">System ID</th>
	                <th width="130">Buyer Name</th>
	             	<th width="100">Job No</th>
	                <th width="80">Job Year</th>
	                <th width="120">Style Ref.</th>
	                <th width="100">Order No.</th>
	                <th width="100">Internal Ref.</th>
	                <th width="120">Embel. Name</th>
	                <th width="100">Embel. Type</th>
	                <th width="150">Serving Company</th>
					<th align="right">Production Qty</th>
				</tr>
            </thead>

    		<tbody id="tbl_list_search" align="center">
        	<?
        	$j=1;
        	foreach ($dataArray as $selectResult) 
        	{
       			if ($j%2==0)  
                $bgcolor="#E9F3FF";
            	else
                $bgcolor="#FFFFFF";

            	if($selectResult[csf('embel_name')]==1){ $embel_type=$emblishment_print_type; }
				elseif($selectResult[csf('embel_name')]==2){ $embel_type=$emblishment_embroy_type; }
				elseif($selectResult[csf('embel_name')]==3){ $embel_type=$emblishment_wash_type; }	
				elseif($selectResult[csf('embel_name')]==4){ $embel_type=$emblishment_spwork_type; }
				elseif($selectResult[csf('embel_name')]==5){ $embel_type=$emblishment_gmts_type; }
        	?>
        	<tr bgcolor="<? echo $bgcolor; ?>">
        		<td>
        			<input type="checkbox" id="tbl_<? echo $j; ?>"  onClick="fnc_checkbox_check(<? echo $j; ?>);"  />

        			<input type="hidden" id="mstidall_<? echo $j; ?>" value="<? echo $selectResult[csf('id')]; ?>" />
        			<input type="hidden" id="issue_to_<? echo $j; ?>" value="<? echo $selectResult[csf('serving_company')]; ?>" />	
                	<input type="hidden" id="emb_source_<? echo $j; ?>" value="<? echo $selectResult[csf('production_source')]; ?>" />
        		</td>
	            <td><? echo $j; ?></td>
	            <td><? echo $selectResult[csf('id')]; ?></td>
	            <td><? echo $buyer_arr[$selectResult[csf('buyer_name')]]; ?></td>
	            <td><? echo $selectResult[csf('job_no')]; ?></td>
	            <td>
	            	<? 
	            		$year=explode("-", $selectResult[csf('job_no')]);
	            		echo "20".$year[1]; 
	            	?>  		
	            </td>
	            <td><? echo $selectResult[csf('style_ref_no')]; ?></td>
	            <td><? echo $selectResult[csf('po_number')]; ?></td>
	            <td><? echo $selectResult[csf('grouping')]; ?></td>
	            <td><? echo $emblishment_name_array[$selectResult[csf('embel_name')]]; ?></td>
	            <td><? echo $embel_type[$selectResult[csf('embel_type')]]; ?></td>
	            <td>
	            	<? 
		            	if ($selectResult[csf('production_source')]==1) 
						{
							echo $serving_com_arr[$selectResult[csf('serving_company')]]; 
						}
						else
						{
							echo $serving_sup_arr[$selectResult[csf('serving_company')]]; 
						}		
	            	?>
	            </td>
	            <td align="right"><? echo $selectResult[csf('production_qnty')]; ?> &nbsp; </td>
        	</tr>
        	<?
        		$j++;  
        	}
        	?>
        	</tbody>
        </table>
    </div>
<?
	exit();
}

if($action=="delivery_challan_print")
{
	extract($_REQUEST);
	echo load_html_head_contents("Emb. Delivery Challan Print", "../", 1, 1,'','','');
	$data=explode('*',$data);
    $mst_id=implode(',',explode("_",$data[1])); 

	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_library=return_library_array( "select id,supplier_name from lib_supplier", "id","supplier_name");
	$buyer_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');

	$order_wise_dtls=array();
	$sql="SELECT a.id, a.production_type, a.production_source, a.serving_company, a.po_break_down_id, a.embel_name, a.embel_type, sum(b.production_qnty) as production_qnty, d.buyer_name, d.job_no, d.style_ref_no, e.po_number from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c, wo_po_details_master d, wo_po_break_down e where a.company_id='$data[0]' and a.id in($mst_id) and a.id=b.mst_id and a.production_type='2' and b.production_type='2' and a.po_break_down_id=c.po_break_down_id and b.color_size_break_down_id=c.id and d.job_no=e.job_no_mst and d.job_no=c.job_no_mst and e.id=a.po_break_down_id and e.id=c.po_break_down_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 group by a.id, a.production_type, a.production_source, a.serving_company, a.po_break_down_id, a.embel_name, a.embel_type, d.buyer_name, d.job_no, d.style_ref_no, e.po_number order by a.id desc";

	$dataArray=sql_select($sql);
	foreach ($dataArray as $value) 
	{
		$order_wise_dtls[$value[csf('po_break_down_id')]]['buyer']=$value[csf('buyer_name')];
		$order_wise_dtls[$value[csf('po_break_down_id')]]['style']=$value[csf('style_ref_no')];
		$order_wise_dtls[$value[csf('po_break_down_id')]]['order']=$value[csf('po_number')];
	}
?>
<div style="width:1230px;">
    <table width="750" cellspacing="0" align="center">
        <tr>
            <td colspan="4" align="center" style="font-size:20px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="4" align="center" style="font-size:14px">  
				<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
					foreach ($nameArray as $result)
					{ 
					?>
						<? echo $result[csf('plot_no')]; ?> &nbsp;
						<? echo $result[csf('level_no')]?>&nbsp;
						<? echo $result[csf('road_no')]; ?> &nbsp;
						<? echo $result[csf('block_no')];?> &nbsp;
						<? echo $result[csf('city')];?> &nbsp;
						<? echo $result[csf('zip_code')]; ?> &nbsp;
						<? echo $result[csf('province')];?> &nbsp;
						<? echo $country_arr[$result[csf('country_id')]]; ?><br> 
						<? echo $result[csf('email')];?> &nbsp;
						<? echo $result[csf('website')];
					}
                ?> 
            </td>  
        </tr>
        <tr><td colspan="4" align="center" style="font-size:18px"><strong><? echo $data[2]; ?> </strong></td></tr>
        <tr><td colspan="4"> &nbsp; </td></tr>
        <tr>
			<?
                $supp_add=$dataArray[0][csf('serving_company')];
                $nameArray=sql_select( "select address_1,web_site,email,country_id from lib_supplier where id=$supp_add"); 
                foreach ($nameArray as $result)
                { 
                    $address="";
                    if($result!="") $address=$result[csf('address_1')];
                }
            ?> 
        	<td><strong>Issue To <span style="float: right;">: &nbsp;</span> </strong></td>
        	<td>
        		<? 
        			if($dataArray[0][csf('production_source')]==1) 
        			{ 
        				echo $company_library[$dataArray[0][csf('serving_company')]]; 
        			} 
        			else if($dataArray[0][csf('production_source')]==3) 
        			{
        				echo $supplier_library[$dataArray[0][csf('serving_company')]];
        				//.'<br>'.$address; 
        			} 
        		?>
        	</td>
        	<td><strong> Emb. Source <span style="float: right;">: &nbsp;</span> </strong></td>
        	<td>
        		<? echo $knitting_source[$dataArray[0][csf('production_source')]]; ?>
        	</td>
        </tr>
         <tr>
            <td><strong>Address <span style="float: right;">: &nbsp;</span></strong></td>
            <td>
            	<? 
        			if($dataArray[0][csf('production_source')]==1) 
        			{ 
        				$serving_company=$dataArray[0][csf('serving_company')];	
        				$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$serving_company"); 
						foreach ($nameArray as $result)
						{ 
							echo $result[csf('plot_no')]."&nbsp".$result[csf('level_no')]."&nbsp";
							echo $result[csf('road_no')]."&nbsp".$result[csf('block_no')]."&nbsp";
							echo $result[csf('city')]."&nbsp".$result[csf('zip_code')]."&nbsp";
							echo $result[csf('province')]."&nbsp".$country_arr[$result[csf('country_id')]];
						}
        			} 
        			else if($dataArray[0][csf('production_source')]==3) 
        			{
        				echo $address; 
        			} 
        		?>
            </td>
        </tr>
        <tr>
        	<td><strong>Date <span style="float: right;">: &nbsp;</span></strong></td> 
        	<td><? echo change_date_format($data[3]); ?></td>
        </tr>
    </table>
    	<br>
        <!-- ############################ Body ############################### -->
         <?
			unset($sql);
			$sql="select a.id, a.embel_name, a.embel_type, a.po_break_down_id, a.item_number_id, b.production_qnty, c.color_number_id, c.size_number_id from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c where a.production_type=2 and b.production_type=2 and a.id in($mst_id) and a.id=b.mst_id and a.po_break_down_id=c.po_break_down_id and b.color_size_break_down_id=c.id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 order by c.color_number_id, c.id";

			$result=sql_select($sql);
			$size_array=array ();
			$color_array=array ();
			$qun_array=array ();
			foreach ( $result as $row )
			{
				$size_array[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$color_array[$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$qun_array[$row[csf('id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('production_qnty')];
			}

			$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
			$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
		?> 
	<div style="width:100%;">
	    <table align="center" cellspacing="0" width="1230"  border="1" rules="all" class="rpt_table" >
	        <thead bgcolor="#dddddd" align="center">
	            <th width="30">SL</th>
	            <th width="60">Issue ID</th>
	            <th width="60">Emb. Name</th>
	            <th width="80">Emb. Type</th>
	            <th width="100">Buyer Name</th>
	            <th width="80">Style Ref</th>
	            <th width="60">Order No</th>
	            <th width="150">Item</th>
	            <th width="80" align="center">Color/Size</th>
					<?
	                foreach ($size_array as $sizid)
	                {
	                    ?>
	                        <th width="40"><strong><? echo  $sizearr[$sizid];  ?></strong></th>
	                    <?
	                }
	                ?>
	            <th width="80" align="center">Total Issue Qty.</th>
	            <th width="100">Remarks</th>
	        </thead>
	        <tbody align="center">
	        	<?
	        		$embl_dtls=array(); $embl_arr=array();
	        		$sql_prod="select a.id, a.embel_name, a.embel_type, a.po_break_down_id, a.item_number_id, a.remarks, c.color_number_id from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c where a.production_type=2 and b.production_type=2 and a.id in($mst_id) and a.id=b.mst_id and a.po_break_down_id=c.po_break_down_id and b.color_size_break_down_id=c.id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 group by a.id, a.embel_name, a.embel_type, a.po_break_down_id, a.item_number_id, a.remarks, c.color_number_id order by a.embel_type";
	        		$result_prod=sql_select($sql_prod);

	        		foreach ($result_prod as $embl_val) 
	        		{
	        			$embl_dtls[$embl_val[csf('embel_name')]][$embl_val[csf('id')]]=$embl_val[csf('id')];
	        		}

					$i=1;
					$tot_specific_size_qnty=array();

					foreach ($result_prod as $val) 
					{
						$tot_color_size_qty=0;
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						if($val[csf("embel_name")]==1){ $embel_type=$emblishment_print_type; }
						elseif($val[csf("embel_name")]==2){ $embel_type=$emblishment_embroy_type; }
						elseif($val[csf("embel_name")]==3){ $embel_type=$emblishment_wash_type; }	
						elseif($val[csf("embel_name")]==4){ $embel_type=$emblishment_spwork_type; }
						elseif($val[csf("embel_name")]==5){ $embel_type=$emblishment_gmts_type; }
				?>
						<tr>
	                        <td> <? echo $i;  ?> </td>
	                        <td> <? echo $val[csf("id")];  ?> </td>

	                        <?
	                        	if(!in_array($val[csf("embel_name")], $embl_arr))
	                        	{
	                        		$embl_arr[]=$val[csf("embel_name")];
	                        ?>
	                        <td rowspan="<? echo count($embl_dtls[$val[csf("embel_name")]]); ?>"> 
	                        	<? echo $emblishment_name_array[$val[csf("embel_name")]]; ?> 
	                        </td>
	                        <?
	                        	}
	                        ?>
	                        <td> <? echo $embel_type[$val[csf("embel_type")]]; ?> </td>
	                        <td> <? echo $buyer_arr[$order_wise_dtls[$val[csf("po_break_down_id")]]['buyer']]; ?> </td>
	                        <td> <? echo $order_wise_dtls[$val[csf("po_break_down_id")]]['style']?> </td>
	                        <td> <? echo $order_wise_dtls[$val[csf("po_break_down_id")]]['order']; ?> </td>
	                        <td> <? echo $garments_item[$val[csf("item_number_id")]]; ?> </td>
	                        <td> <? echo $colorarr[$val[csf("color_number_id")]]; ?> </td>
	                        <?
	                        foreach ($size_array as $sizval)
	                        {
	                        ?>
	                            <td align="right">
	                            <? 
	                            	echo $qun_array[$val[csf("id")]][$val[csf("color_number_id")]][$sizval]; 

	                            	$tot_color_size_qty+=$qun_array[$val[csf("id")]][$val[csf("color_number_id")]][$sizval];
	                           		$tot_specific_size_qnty[$sizval]+=$qun_array[$val[csf("id")]][$val[csf("color_number_id")]][$sizval];
	                            ?>	
	                            </td>
	                        <?  
	                        }
	                        ?>
	                        <td align="right"> 
	                        	<? 
	                        	echo $tot_color_size_qty; 
	                        	?> 
	                        </td>
	                        <td> <? echo $val[csf("remarks")]; ?> </td>
	                     </tr>
	            <?
					$i++;	}
				?>
	        </tbody>
	        <tr>
	            <td colspan="9" align="right"><strong>Grand Total : &nbsp;</strong></td>
	            <?
					foreach ($size_array as $sizval)
					{
						?>
	                    <td align="right"><?php echo $tot_specific_size_qnty[$sizval]; ?> </td>
	                    <?
					}
				?>
	            <td align="right"><?php echo array_sum($tot_specific_size_qnty); ?> </td>
	            <td>&nbsp;</td>
	        </tr>                           
	    </table>

        <br>
		 <?
            echo signature_table(26, $data[0], "1350px");
         ?>
	</div>
	</div>
<?	
exit();
}
?>