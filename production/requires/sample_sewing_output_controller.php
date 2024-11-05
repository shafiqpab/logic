<?
session_start();
include('../../includes/common.php');
 
$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//------------------------------------------------------------------------------------------------------
$sample_name_library=return_library_array( "select sample_name,id from lib_sample where is_deleted=0 and status_active=1 order by sample_name", "id", "sample_name"  );

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 157, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" ); 
	exit();	 
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 167, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/sample_sewing_output_controller', this.value, 'load_drop_down_floor', 'floor_td' );load_drop_down( 'requires/sample_sewing_output_controller', document.getElementById('cbo_location').value+'_'+'_'+document.getElementById('txt_sewing_date').value, 'load_drop_down_sewing_output_line', 'sewing_line_td' );" );     	 
	
	//echo create_drop_down( "cbo_location", 167, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/sample_sewing_output_controller', this.value, 'load_drop_down_floor', 'floor_td' );" );     	 
	
	
	exit();



}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor", 170, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and production_process in (5) order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "load_drop_down( 'requires/sample_sewing_output_controller', this.value+'_'+document.getElementById('cbo_location').value+'_'+document.getElementById('txt_sewing_date').value, 'load_drop_down_sewing_line_floor', 'sewing_line_td' );",0 );     	 

}



if($action=="load_drop_down_sewing_output")
{
	$explode_data = explode("**",$data);
	$data = $explode_data[0];
	$selected_company = $explode_data[1];
	
	if($data==3)
	{
		if($db_type==0)
		{
			echo create_drop_down( "cbo_sewing_company", 170, "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 and find_in_set(22,party_type) order by supplier_name","id,supplier_name", 1, "--- Select ---", $selected, "get_php_form_data(this.value+'**'+$data+'**'+$('#hidden_po_break_down_id').val()+'**'+$('#cbo_item_name').val()+'**'+$('#cbo_country_name').val(),'display_bl_qnty','requires/sample_sewing_output_controller');",0,0 ); 
		}
		else
		{	
			echo create_drop_down( "cbo_sewing_company", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=22 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--- Select ---", $selected, "get_php_form_data(this.value+'**'+$data+'**'+$('#hidden_po_break_down_id').val()+'**'+$('#cbo_item_name').val()+'**'+$('#cbo_country_name').val(),'display_bl_qnty','requires/sample_sewing_output_controller');",0,0 );
		}
	}
	else if($data==1)
	{
 		echo create_drop_down( "cbo_sewing_company", 170, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name","id,company_name", 1, "--- Select ---", $selected_company,  "load_drop_down( 'requires/sample_sewing_output_controller', this.value, 'load_drop_down_location', 'location_td' );",0,0 ); 
		
	}
 	else
	{
		echo create_drop_down( "cbo_sewing_company", 170, $blank_array,"", 1, "--- Select ---", $selected, "",0,0 );
	}
			
	exit();
}




if($action=="sample_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Sample Development Info","../../", 1, 1, $unicode);
?>
<html>
    <head>
	<script>
		$(document).ready(function(e) {
            $("#txt_search_common").focus();
        });
		function search_populate(str)
		{
			//alert(str); 
			if(str==0) 
			{		
				document.getElementById('search_by_th_up').innerHTML="Enter Style ID";
				document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:130px " class="text_boxes" id="txt_search_common"	value=""  />';		 
			}
			else if(str==1) 
			{
				document.getElementById('search_by_th_up').innerHTML="Enter Style Name";
				document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:130px " class="text_boxes" id="txt_search_common"	value=""  />';
			}																																								
		}
		
		function js_set_value( mst_id )
		{
			document.getElementById('selected_id').value=mst_id;
			parent.emailwindow.hide();
		}
    </script>

</head>

<body>
	<div align="center" style="width:100%;" >
	<form name="searchsampledevelopmentfrm_1"  id="searchsampledevelopmentfrm_1" autocomplete="off">
		<table width="900" cellspacing="0" cellpadding="0" class="rpt_table" align="center" border="1" rules="all">
    		<tr>
        		<td align="center" width="100%">
            		<table  cellspacing="0" cellpadding="0" class="rpt_table" align="center" border="1" rules="all">
                        <thead>
                        	<th  colspan="6">
                              <?
                               echo create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --" );
                              ?>
                            </th>
                        
                        </thead>
                        <thead>
                        	<th width="140">Company Name</th>
                            <th width="160">Buyer Name</th>                	 
                            <th width="130">Style ID</th>
                            <th  width="130" >Style Name</th>
                            <th width="200">Est. Ship Date Range</th>
                            <th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100%" /></th>
                        </thead>
        				<tr>
                        	<td width="140"> 
								<input type="hidden" id="selected_id"/>
								<? 
                                    echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $company,"load_drop_down( 'sample_sewing_output_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                                ?>
                    		</td>
                   			<td id="buyer_td" width="160">
								 <? 
                                    echo create_drop_down( "cbo_buyer_name", 157, $blank_array,'', 1, "-- Select Buyer --" );
                                ?>	
                            </td>
                            <td width="130">  
								<input type="text" style="width:130px" class="text_boxes"  name="txt_style_id" id="txt_style_id"  />	
                            </td>
                            <td width="130" align="center">				
                                <input type="text" style="width:130px" class="text_boxes"  name="txt_style_name" id="txt_style_name"  />			
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"> To
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
                            </td> 
                            <td align="center">
                                <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_style_id').value+'_'+document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_style_name').value, 'create_po_search_list_view', 'search_div', 'sample_sewing_output_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:80px;" />
                            </td>
        				</tr>
             		</table>
          		</td>
        	</tr>
        	<tr>
            	<td  align="center" height="40" valign="middle">
				    <? 
                    echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );		
                    ?>
					<? echo load_month_buttons();  ?>
          		</td>
            </tr>
        	<tr>
            	<td align="center" valign="top" id="search_div"></td>
        	</tr>
    	</table>
    </form>
	</div>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	load_drop_down( 'sample_sewing_output_controller',<? echo $company; ?>, 'load_drop_down_buyer', 'buyer_td' );
</script>
</html>
<?
exit();
}





if($action=="populate_data_from_search_popup")
{
$res = sql_select("select id,company_id,buyer_name,style_ref_no,item_name from sample_development_mst where id=$data  and status_active=1 and is_deleted=0"); 
 
  	foreach($res as $result)
	{
		echo "$('#cbo_company_name').val('".$result[csf('company_id')]."');\n";
		echo "$('#cbo_buyer_name').val('".$result[csf('buyer_name')]."');\n";
		echo "$('#txt_style_no').val('".$result[csf('style_ref_no')]."');\n";
		echo "$('#cbo_item_name').val('".$result[csf('item_name')]."');\n";
		$dataArray=sql_select("select SUM(size_qty) as size_qty from sample_development_size WHERE mst_id=".$result[csf('id')]."");
 		foreach($dataArray as $row)
		{  
			echo "$('#txt_sample_qty').val('".$row[csf('size_qty')]."');\n";
		}
	}
	
	$smp_mst_id = sql_select("select id,company_id,production_source,sewing_company,location,floor_id from sample_sewing_output_mst where sample_development_id=$data and status_active=1 and is_deleted=0"); 
	
	echo "load_drop_down('requires/sample_sewing_output_controller', '".$smp_mst_id[0][csf('production_source')].'**'.$smp_mst_id[0][csf('company_id')]."', 'load_drop_down_sewing_output', 'sew_company_td' );";
	
	echo "$('#mst_update_id').val('".$smp_mst_id[0][csf('id')]."');\n";
	echo "$('#cbo_source').val('".$smp_mst_id[0][csf('production_source')]."');\n";
	echo "$('#cbo_sewing_company').val('".$smp_mst_id[0][csf('sewing_company')]."');\n";
	echo "$('#cbo_location').val('".$smp_mst_id[0][csf('location')]."');\n";
	echo "$('#cbo_floor').val('".$smp_mst_id[0][csf('floor_id')]."');\n";
	
	
	
	
	
 	exit();	
}




if($action=="show_sample_item_listview")
{
?>	
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th>Garmengts Item Name</th>
            <th width="120">Sample Name</th>
            <th width="60">Sample Qty</th>                    
        </thead>
		<?  
		$i=1;
		
		$sqlResult = sql_select("select a.id,a.item_name,b.sample_name,sum(c.size_qty) as size_qty from sample_development_mst a,sample_development_dtls b, sample_development_size c where a.id=b.sample_mst_id and b.id=c.dtls_id and a.id=$data group by b.sample_name,a.item_name,a.id"); 
		
		foreach($sqlResult as $row)
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="put_sample_item_data(<? echo $row[csf('id')];?>,<? echo $row[csf('sample_name')]; ?>);"> 
				<td><? echo $i; ?></td>
				<td><p><? echo $garments_item[$row[csf('item_name')]]; ?></p></td>
				<td><p><? echo $sample_name_library[$row[csf('sample_name')]]; ?></p></td>
				<td align="right"><?php  echo $row[csf('size_qty')]; ?></td>
			</tr>
		<?	
			$i++;
		}
		?>
	</table>
	<?
	exit();
}


if($action=="color_and_size_level")
{
		list($mst_id,$smp_id)=explode('**',$data);
		$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
		$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');
		
		//Save data;
		$colorResult = sql_select("
		select 
			b.sample_name,c.color_id,c.size_id,c.size_pass_qty,c.size_rej_qty 	
		from 
			sample_sewing_output_mst a, 
			sample_sewing_output_dtls b,
			sample_sewing_output_colorsize c
		where 
			a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and a.sample_development_id=$mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0"); 
 		foreach($colorResult as $row)
		{
		$qcPassQtyArr[$row[csf("sample_name")]][$row[csf("color_id")]][$row[csf("size_id")]]+=$row[csf("size_pass_qty")];	
		$totQcPassQty+=$row[csf("size_pass_qty")];	
		}
		
		// new data;
		$colorResult = sql_select("select a.sample_color,b.size_id,b.size_qty from sample_development_dtls a, sample_development_size b where a.id=b.dtls_id and a.sample_mst_id=$mst_id and a.sample_name=$smp_id  and a.status_active=1 and a.is_deleted=0"); 
 		foreach($colorResult as $row)
		{
		$colorData[$row[csf("sample_color")]][$row[csf("size_id")]]+=$row[csf("size_qty")];	
		}
		
		
		foreach($colorData as $color_id=>$color_value)
		{
			$colorHTML .= '<h3 align="left" id="accordion_h'.$color_id.'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color_id.'\', \'\',1)"> <span id="accordion_h'.$color_id.'span">+</span>'.$color_library[$color_id].' : <span id="total_'.$color_id.'"></span> </h3>';
			$colorHTML .= '<div id="content_search_panel_'.$color_id.'" style="display:none" class="accord_close"><table id="table_'.$color_id.'">';
			$i=1;
			foreach($color_value as $size_id=>$size_qty)
			{
				$colorID .= $color_id."*".$size_id.",";
				
				$colorHTML .='<tr><td>'.$size_library[$size_id].'</td><td><input type="text" name="colSizeQty" id="colSizeQty_'.$color_id.$i.'" class="text_boxes_numeric" style="width:80px" placeholder="'.($size_qty-$qcPassQtyArr[$smp_id][$color_id][$size_id]).'" onkeyup="fn_total('.$color_id.','.$i.')"><input type="text" name="colorSizeRej" id="colSizeRej_'.$color_id.$i.'"  class="text_boxes_numeric" style="width:50px" placeholder="Rej. Qty" onkeyup="fn_total_rej('.$color_id.','.$i.')" '.$disable.'></td></tr>';				
			$i++;
			}
			$colorHTML .= "</table></div>";
		
		}
		
		
		echo "$('#txt_cumul_sewing_qty').val(".$totQcPassQty.");\n";
		
		echo "var smpqty=$('#txt_sample_qty').val();\n";
		echo "var qcqty=$('#txt_cumul_sewing_qty').val();\n";
		echo "$('#txt_yet_to_sewing').val(smpqty-qcqty);\n";
		
		echo "$('#dtls_update_id').val('');\n";
		echo "$('#cbo_sample_name').val(".$smp_id.");\n";
		echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
		$colorList = substr($colorID,0,-1);
		echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
		exit();
}



if($action=="show_dtls_listview")
{
$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );

?>	 
 <fieldset style="overflow:hidden; margin:5px 0;">
     <div style="width:100%;">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="110">Sample Name</th>
                <th width="70">Prod. Date</th>
                <th width="80">QC Pass Qty</th>
                <th width="60">Alter Qty</th>
                <th width="60">Spot Qty</th> 
                <th width="60">Reject Qty</th>
                <th width="60">Sewing Line</th>
                <th width="60">Rep. Hour</th>
                <th width="80">Supervisor</th>
                <th>Challan No</th>
            </thead>
		</table>
	</div>
	<div style="width:100%; max-height:180px; overflow:y-scroll" id="sewing_production_list_view">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
		<?php  
			$i=1;
			if($db_type==2){$reporting_hour_fill=" TO_CHAR( reporting_hour,'HH24:MI' ) as reporting_hour ";}
			else{$reporting_hour_fill=" TIME_FORMAT( reporting_hour, '%H:%i' ) as reporting_hour ";}
			
			$sqlResult =sql_select("select a.id,a.sample_development_id,b.id as dtls_id, sample_name, sewing_date, line_no,$reporting_hour_fill, supervisor, qc_pass_qty, alter_qty, spot_qty, reject_qty, challan_no,challan_no from sample_sewing_output_mst a,sample_sewing_output_dtls b where a.id=b.sample_sewing_output_mst_id and a.sample_development_id=$data  and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0");
				
			foreach($sqlResult as $row){
				
				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
				
  		?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data('<? echo $row[csf('sample_development_id')].'**'.$row[csf('id')].'**'.$row[csf('dtls_id')]; ?>','populate_input_form_data','requires/sample_sewing_output_controller');" > 
				<td width="30" align="center"><? echo $i; ?></td>
                <td width="110"><p><? echo $sample_name_library[$row[csf('sample_name')]]; ?></p></td>
                <td width="70" align="center"><?php echo change_date_format($row[csf('sewing_date')]); ?></td>
                <td width="80" align="right"><?php echo $row[csf('qc_pass_qty')]; ?></td>
                <td width="60" align="right"><?php echo $row[csf('alter_qty')]; ?></td>
                <td width="60" align="right"><?php echo $row[csf('spot_qty')]; ?></td>
                <td width="60" align="right"><?php echo $row[csf('reject_qty')]; ?></td>
                <td width="60"><p><? echo $line_library[$row[csf('line_no')]]; ?></p></td>
                <td width="60"><p><? echo $row[csf('reporting_hour')]; ?></p></td>
                <td width="80"><p><? echo $row[csf('supervisor')]; ?></p></td>
                <td><p><? echo $row[csf('challan_no')]; ?></p></td>
			</tr>
			<?php
			$i++;
			}
			?>
		</table>
    </div>
</fieldset>


<?
	exit();	
}





if($action=="populate_input_form_data")
{
	
	$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');

	list($smp_id,$mst_id,$dtls_id)=explode('**',$data);
		
	
	//Save data........................
	if($db_type==2){$reporting_hour_fill=" TO_CHAR( a.reporting_hour,'HH24:MI' ) as reporting_hour ";}
	else{$reporting_hour_fill=" TIME_FORMAT( a.reporting_hour, '%H:%i' ) as reporting_hour ";}

		$colorResult = sql_select("select a.id, a.sample_name, a.sewing_date, a.line_no,$reporting_hour_fill, a.supervisor, a.qc_pass_qty, a.alter_qty, a.spot_qty, a.reject_qty, a.challan_no,a.remarks,b.color_id as sample_color,b.size_id,b.size_pass_qty as size_qty,b.size_rej_qty from sample_sewing_output_dtls a, sample_sewing_output_colorsize b where a.id=b.sample_sewing_output_dtls_id and a.sample_sewing_output_mst_id = $mst_id and b.sample_sewing_output_mst_id = $mst_id  and a.status_active=1 and a.is_deleted=0"); 
 		
		
	foreach($colorResult as $row)
	{	
		if($row[csf("sample_color")]){
			$colorTotal[$row[csf("id")]][$row[csf("sample_color")]]+=$row[csf("size_qty")];	
			$colorData[$row[csf("id")]][$row[csf("sample_color")]][$row[csf("size_id")]]+=$row[csf("size_qty")];	
			$colorDataRej[$row[csf("id")]][$row[csf("sample_color")]][$row[csf("size_id")]]+=$row[csf("size_rej_qty")];	
			
			$sizeQcPassQty[$row[csf("sample_color")]][$row[csf("size_id")]]+=$row[csf("size_qty")];	
			$totSizeQcPassQty+=$row[csf("size_qty")];	

			$dtlsArr[$row[csf("id")]]['sample_name']=$row[csf('sample_name')];
			
			$dtlsArr[$row[csf("id")]]['sewing_date']=$row[csf('sewing_date')];
			$dtlsArr[$row[csf("id")]]['line_no']=$row[csf('line_no')];
			
			$dtlsArr[$row[csf("id")]]['reporting_hour']=$row[csf('reporting_hour')];
			$dtlsArr[$row[csf("id")]]['supervisor']=$row[csf('supervisor')];
			$dtlsArr[$row[csf("id")]]['qc_pass_qty']=$row[csf('qc_pass_qty')];
			$dtlsArr[$row[csf("id")]]['reject_qty']=$row[csf('reject_qty')];
			$dtlsArr[$row[csf("id")]]['alter_qty']=$row[csf('alter_qty')];
			$dtlsArr[$row[csf("id")]]['spot_qty']=$row[csf('spot_qty')];
			$dtlsArr[$row[csf("id")]]['challan_no']=$row[csf('challan_no')];
			$dtlsArr[$row[csf("id")]]['remarks']=$row[csf('remarks')];
		}
	}
		echo "$('#txt_sys_chln').val('".$dtls_id."');\n";
		echo "$('#dtls_update_id').val('".$dtls_id."');\n";
		echo "$('#cbo_sample_name').val('".$dtlsArr[$dtls_id]['sample_name']."');\n";
		echo "$('#txt_sewing_date').val('".change_date_format($dtlsArr[$dtls_id]['sewing_date'])."');\n";
		echo "$('#cbo_sewing_line').val('".$dtlsArr[$dtls_id]['line_no']."');\n";
		echo "$('#txt_reporting_hour').val('".$dtlsArr[$dtls_id]['reporting_hour']."');\n";
		echo "$('#txt_supervisor').val('".$dtlsArr[$dtls_id]['supervisor']."');\n";
		echo "$('#txt_qc_pass_qty').val('".$dtlsArr[$dtls_id]['qc_pass_qty']."');\n";
		echo "$('#txt_reject_qnty').val('".$dtlsArr[$dtls_id]['reject_qty']."');\n";
		echo "$('#txt_alter_qnty').val('".$dtlsArr[$dtls_id]['alter_qty']."');\n";
		echo "$('#txt_spot_qnty').val('".$dtlsArr[$dtls_id]['spot_qty']."');\n";
		echo "$('#txt_challan').val('".$dtlsArr[$dtls_id]['challan_no']."');\n";
		echo "$('#txt_remark').val('".$dtlsArr[$dtls_id]['remarks']."');\n";
	
		
		
		//New data........................;
		$sqlResult = sql_select("select a.sample_color,b.size_id,b.size_qty from sample_development_dtls a, sample_development_size b where a.id=b.dtls_id and a.sample_mst_id=$smp_id and a.sample_name=".$dtlsArr[$dtls_id]['sample_name'].""); 
 		foreach($sqlResult as $row)
		{
		$smp_qty_arr[$row[csf("sample_color")]][$row[csf("size_id")]]+=$row[csf("size_qty")];	
		}

		
		
		foreach($colorData[$dtls_id] as $color_id=>$color_value)
		{
			$colorHTML .= '<h3 align="left" id="accordion_h'.$color_id.'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color_id.'\', \'\',1)"> <span id="accordion_h'.$color_id.'span">+</span>'.$color_library[$color_id].' : <span id="total_'.$color_id.'">'.$colorTotal[$dtls_id][$color_id].'</span> </h3>';
			$colorHTML .= '<div id="content_search_panel_'.$color_id.'" style="display:none" class="accord_close"><table id="table_'.$color_id.'">';
			$i=1;
			foreach($color_value as $size_id=>$size_qty)
			{
				$colorID .= $color_id."*".$size_id.",";
				
				$colorHTML .='<tr><td>'.$size_library[$size_id].'</td><td><input type="text" name="colSizeQty" id="colSizeQty_'.$color_id.$i.'"  class="text_boxes_numeric" style="width:80px" value="'.$size_qty.'" placeholder="'.($smp_qty_arr[$color_id][$size_id]-($sizeQcPassQty[$color_id][$size_id]-$size_qty)).'" onkeyup="fn_total('.$color_id.','.$i.')"><input type="text" name="colorSizeRej" id="colSizeRej_'.$color_id.$i.'"  class="text_boxes_numeric" style="width:50px" value="'.$colorDataRej[$dtls_id][$color_id][$size_id].'" placeholder="Rej. Qty" onkeyup="fn_total_rej('.$color_id.','.$i.')" '.$disable.'></td></tr>';				
			$i++;
			}
			$colorHTML .= "</table></div>";
		
		}
		
		
		echo "$('#txt_cumul_sewing_qty').val(".$totSizeQcPassQty.");\n";
		echo "var smpqty=$('#txt_sample_qty').val();\n";
		echo "var qcqty=$('#txt_cumul_sewing_qty').val();\n";
		echo "$('#txt_yet_to_sewing').val(smpqty-qcqty);\n";
		
		echo "set_button_status(1, permission, 'fnc_sample_sewing_output_entry',1,0);\n";
		echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
		$colorList = substr($colorID,0,-1);
		echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
	
 	exit();		
}


if($action=="load_drop_down_sewing_output_line")
{
	list($location,$txt_sewing_date)= explode("_",$data);
	echo create_drop_down( "cbo_sewing_line", 110, "select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1 and location_name='$location' and location_name!=0 order by line_name","id,line_name", 1, "Select Line", $selected, "" );
}




if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if ($operation==0) // Insert part----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
 		$flag=1;
		$mst_update_id=str_replace("'","",$mst_update_id);
		$dtls_update_id=str_replace("'","",$dtls_update_id);
		
		if($mst_update_id=='')
		{
		// master part--------------------------------------------------------------;
			$mst_id=return_next_id("id", "sample_sewing_output_mst", 1);
			$field_array_mst="id, company_id, sample_development_id, item_number_id, production_source, sewing_company, location, floor_id, inserted_by, insert_date, status_active, is_deleted";
			$data_array_mst="(".$mst_id.",".$cbo_company_name.",".$txt_sample_devlopment_id.",".$cbo_item_name.",".$cbo_source.",".$cbo_sewing_company.",".$cbo_location.",".$cbo_floor.",".$user_id.",'".$pc_date_time."','1','0')";
	
		// Details part--------------------------------------------------------------;
		if($db_type==2 || $db_type==1){
			$txt_reporting_hour=str_replace("'","",$txt_sewing_date)." ".str_replace("'","",$txt_reporting_hour);
			$txt_reporting_hour="to_date('".$txt_reporting_hour."','DD MONTH YYYY HH24:MI:SS')";
			}

			$dtls_id=return_next_id("id", "sample_sewing_output_dtls", 1);
			$field_array_dtls="id, sample_sewing_output_mst_id, sample_name, sewing_date, line_no, reporting_hour, supervisor, qc_pass_qty, alter_qty, spot_qty, reject_qty, challan_no, remarks, inserted_by, insert_date, status_active, is_deleted";
			$data_array_dtls="(".$dtls_id.",".$mst_id.",".$cbo_sample_name.",".$txt_sewing_date.",".$cbo_sewing_line.",".$txt_reporting_hour.",".$txt_supervisor.",".$txt_qc_pass_qty.",".$txt_alter_qnty.",".$txt_spot_qnty.",".$txt_reject_qnty.",".$txt_challan.",".$txt_remark.",".$user_id.",'".$pc_date_time."','1','0')";
	
	
		// Color & Size Breakdown part--------------------------------------------------------------;
		$field_array_brk="id, sample_sewing_output_mst_id, sample_sewing_output_dtls_id, color_id, size_id, size_pass_qty, size_rej_qty, inserted_by, insert_date, status_active, is_deleted";
		$colorsize_brk_id=return_next_id("id", "sample_sewing_output_colorsize", 1);
			
			// size reject value;
			$rowExRej = explode("***",$colorIDvalueRej);
			foreach($rowExRej as $rowR=>$valR)
			{
				$colorAndSizeRej_arr = explode("*",$valR);
				$colorID = $colorAndSizeRej_arr[0];				
				$sizeID = $colorAndSizeRej_arr[1];
				$colorSizeRej = $colorAndSizeRej_arr[2];
				$index = $colorID.$sizeID;
				$rejQtyArr[$index]=$colorSizeRej;
			}
			
			
			// size quantity value;
			$rowEx = explode("***",$colorIDvalue); 
			$data_array_brk="";$j=0;
			foreach($rowEx as $rowE=>$valE)
			{
				$colorAndSizeAndValue_arr = explode("*",$valE);
				$colorID = $colorAndSizeAndValue_arr[0];				
				$sizeID = $colorAndSizeAndValue_arr[1];
				$colorSizeValue = $colorAndSizeAndValue_arr[2];
				$index = $colorID.$sizeID;
	
				if($j==0)$data_array_brk = "(".$colorsize_brk_id.",".$mst_id.",".$dtls_id.",'".$colorID."','".$sizeID."','".$colorSizeValue."','".$rejQtyArr[$index]."',".$user_id.",'".$pc_date_time."','1','0')";
				else $data_array_brk .= ",(".$colorsize_brk_id.",".$mst_id.",".$dtls_id.",'".$colorID."','".$sizeID."','".$colorSizeValue."','".$rejQtyArr[$index]."',".$user_id.",'".$pc_date_time."','1','0')";
				$colorsize_brk_id+=1;
				$j++;
			}
		
			
			//insert here----------------------------------------;
			$rID_mst=sql_insert("sample_sewing_output_mst",$field_array_mst,$data_array_mst,0);
			if($flag==1) 
			{
				if($rID_mst) $flag=1; else $flag=0; 
			} 
			
			$rID_dtls=execute_query("insert into sample_sewing_output_dtls ($field_array_dtls) values $data_array_dtls");
			//$rID_dtls=sql_insert("sample_sewing_output_dtls",$field_array_dtls,$data_array_dtls,0);
			if($flag==1) 
			{
				if($rID_dtls) $flag=1; else $flag=0; 
			} 
	
			$rID_brk=sql_insert("sample_sewing_output_colorsize",$field_array_brk,$data_array_brk,0);
			if($flag==1) 
			{
				if($rID_brk) $flag=1; else $flag=0; 
			} 

			// echo "10**Insert into sample_sewing_output_dtls ($field_array_dtls) values $data_array_dtls"; die;
	
			//ROLLBACK/COMMIT here----------------------------------------;
			if($db_type==0)
			{
				if($flag==1)
				{
					mysql_query("COMMIT");  
					echo "0**".$mst_id."**".$txt_sample_devlopment_id."**0**".$dtls_id;
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**0**"."&nbsp;"."**0";
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($flag==1)
				{
					oci_commit($con);  
					echo "0**".$mst_id."**".$txt_sample_devlopment_id."**0**".$dtls_id;
				}
				else
				{
					oci_rollback($con);
					echo "10**0**"."&nbsp;"."**0";
				}
			}
			
		}
		else
		{
		// Details part--------------------------------------------------------------;
		if($db_type==2 || $db_type==1){
			$txt_reporting_hour=str_replace("'","",$txt_sewing_date)." ".str_replace("'","",$txt_reporting_hour);
			$txt_reporting_hour="to_date('".$txt_reporting_hour."','DD MONTH YYYY HH24:MI:SS')";
			}	
	
			$dtls_id=return_next_id("id", "sample_sewing_output_dtls", 1);
			$field_array_dtls="id, sample_sewing_output_mst_id, sample_name, sewing_date, line_no, reporting_hour, supervisor, qc_pass_qty, alter_qty, spot_qty, reject_qty, challan_no, remarks, inserted_by, insert_date, status_active, is_deleted";
			$data_array_dtls="(".$dtls_id.",".$mst_update_id.",".$cbo_sample_name.",".$txt_sewing_date.",".$cbo_sewing_line.",".$txt_reporting_hour.",".$txt_supervisor.",".$txt_qc_pass_qty.",".$txt_alter_qnty.",".$txt_spot_qnty.",".$txt_reject_qnty.",".$txt_challan.",".$txt_remark.",".$user_id.",'".$pc_date_time."','1','0')";
	
		// Color & Size Breakdown part--------------------------------------------------------------;
		$field_array_brk="id, sample_sewing_output_mst_id, sample_sewing_output_dtls_id, color_id, size_id, size_pass_qty, size_rej_qty, inserted_by, insert_date, status_active, is_deleted";
		$colorsize_brk_id=return_next_id("id", "sample_sewing_output_colorsize", 1);
			
			// size reject value;
			$rowExRej = explode("***",$colorIDvalueRej);
			foreach($rowExRej as $rowR=>$valR)
			{
				$colorAndSizeRej_arr = explode("*",$valR);
				$colorID = $colorAndSizeRej_arr[0];				
				$sizeID = $colorAndSizeRej_arr[1];
				$colorSizeRej = $colorAndSizeRej_arr[2];
				$index = $colorID.$sizeID;
				$rejQtyArr[$index]=$colorSizeRej;
			}
			
			
			// size quantity value;
			$rowEx = explode("***",$colorIDvalue); 
			$data_array_brk="";$j=0;
			foreach($rowEx as $rowE=>$valE)
			{
				$colorAndSizeAndValue_arr = explode("*",$valE);
				$colorID = $colorAndSizeAndValue_arr[0];				
				$sizeID = $colorAndSizeAndValue_arr[1];
				$colorSizeValue = $colorAndSizeAndValue_arr[2];
				$index = $colorID.$sizeID;
	
				if($j==0)$data_array_brk = "(".$colorsize_brk_id.",".$mst_update_id.",".$dtls_id.",'".$colorID."','".$sizeID."','".$colorSizeValue."','".$rejQtyArr[$index]."',".$user_id.",'".$pc_date_time."','1','0')";
				else $data_array_brk .= ",(".$colorsize_brk_id.",".$mst_update_id.",".$dtls_id.",'".$colorID."','".$sizeID."','".$colorSizeValue."','".$rejQtyArr[$index]."',".$user_id.",'".$pc_date_time."','1','0')";
				$colorsize_brk_id+=1;
				$j++;
			}
		
			
			$rID_dtls=execute_query("insert into sample_sewing_output_dtls ($field_array_dtls) values $data_array_dtls");
			//$rID_dtls=sql_insert("sample_sewing_output_dtls",$field_array_dtls,$data_array_dtls,0);
			if($flag==1) 
			{
				if($rID_dtls) $flag=1; else $flag=0; 
			} 
	
			$rID_brk=sql_insert("sample_sewing_output_colorsize",$field_array_brk,$data_array_brk,0);
			if($flag==1) 
			{
				if($rID_brk) $flag=1; else $flag=0; 
			} 
	
			//ROLLBACK/COMMIT here----------------------------------------;
			if($db_type==0)
			{
				if($flag==1)
				{
					mysql_query("COMMIT");  
					echo "0**".$mst_update_id."**".$txt_sample_devlopment_id."**0**".$dtls_id;
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**0**"."&nbsp;"."**0";
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($flag==1)
				{
					oci_commit($con);  
					echo "0**".$mst_update_id."**".$txt_sample_devlopment_id."**0**".$dtls_id;
				}
				else
				{
					oci_rollback($con);
					echo "10**0**"."&nbsp;"."**0";
				}
			}
			
		}
					
		disconnect($con);
		die;
	}
  	else if ($operation==1) // Update part ------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		$mst_update_id=str_replace("'","",$mst_update_id);
		$dtls_update_id=str_replace("'","",$dtls_update_id);
		
		if($mst_update_id!='')
		{
			// master part--------------------------------------------------------------;
			$field_array_mst="company_id*item_number_id*production_source*sewing_company* location*floor_id*updated_by*update_date";
			$data_array_mst="".$cbo_company_name."*".$cbo_item_name."*".$cbo_source."*".$cbo_sewing_company."*".$cbo_location."*".$cbo_floor."*".$user_id."*'".$pc_date_time."'";
			$rID_mst=sql_update("sample_sewing_output_mst",$field_array_mst,$data_array_mst,"id","".$mst_update_id."",1);
			
			
		// Dtls part--------------------------------------------------------------;
		if($db_type==2 || $db_type==1){
			$txt_reporting_hour=str_replace("'","",$txt_sewing_date)." ".str_replace("'","",$txt_reporting_hour);
			$txt_reporting_hour="to_date('".$txt_reporting_hour."','DD MONTH YYYY HH24:MI:SS')";
			}
				
			$field_array_dtls="sewing_date*line_no*reporting_hour*supervisor*qc_pass_qty*alter_qty*spot_qty*reject_qty*challan_no*remarks*updated_by*update_date";
			$data_array_dtls="".$txt_sewing_date."*".$cbo_sewing_line."*".$txt_reporting_hour."*".$txt_supervisor."*".$txt_qc_pass_qty."*".$txt_alter_qnty."*".$txt_spot_qnty."*".$txt_reject_qnty."*".$txt_challan."*".$txt_remark."*".$user_id."*'".$pc_date_time."'";
			$rID_dtls=sql_update("sample_sewing_output_dtls",$field_array_dtls,$data_array_dtls,"id","".$dtls_update_id."",1);
			
		// Color & Size Breakdown part--------------------------------------------------------------;
		$rID_brk_delete = execute_query("DELETE from sample_sewing_output_colorsize WHERE sample_sewing_output_dtls_id=$dtls_update_id");
		
		$field_array_brk="id, sample_sewing_output_mst_id, sample_sewing_output_dtls_id, color_id, size_id, size_pass_qty, size_rej_qty, inserted_by, insert_date, status_active, is_deleted";
		$colorsize_brk_id=return_next_id("id", "sample_sewing_output_colorsize", 1);
			
			// size reject value;
			$rowExRej = explode("***",$colorIDvalueRej);
			foreach($rowExRej as $rowR=>$valR)
			{
				$colorAndSizeRej_arr = explode("*",$valR);
				$colorID = $colorAndSizeRej_arr[0];				
				$sizeID = $colorAndSizeRej_arr[1];
				$colorSizeRej = $colorAndSizeRej_arr[2];
				$index = $colorID.$sizeID;
				$rejQtyArr[$index]=$colorSizeRej;
			}
			
			
			// size quantity value;
			$rowEx = explode("***",$colorIDvalue); 
			$data_array_brk="";$j=0;
			foreach($rowEx as $rowE=>$valE)
			{
				$colorAndSizeAndValue_arr = explode("*",$valE);
				$colorID = $colorAndSizeAndValue_arr[0];				
				$sizeID = $colorAndSizeAndValue_arr[1];
				$colorSizeValue = $colorAndSizeAndValue_arr[2];
				$index = $colorID.$sizeID;
	
				if($j==0)$data_array_brk = "(".$colorsize_brk_id.",".$mst_update_id.",".$dtls_update_id.",'".$colorID."','".$sizeID."','".$colorSizeValue."','".$rejQtyArr[$index]."',".$user_id.",'".$pc_date_time."','1','0')";
				else $data_array_brk .= ",(".$colorsize_brk_id.",".$mst_update_id.",".$dtls_update_id.",'".$colorID."','".$sizeID."','".$colorSizeValue."','".$rejQtyArr[$index]."',".$user_id.",'".$pc_date_time."','1','0')";
				$colorsize_brk_id+=1;
				$j++;
			}
			
			$rID_brk=sql_insert("sample_sewing_output_colorsize",$field_array_brk,$data_array_brk,0);
			
		
		
		//echo $rID_mst .'&&'. $rID_dtls .'&&'. $rID_brk_delete .'&&'. $rID_brk; die;
		
		//-------------------------------------------------------------------------------------------	
			if($db_type==0)
			{
				if($rID_mst && $rID_dtls && $rID_brk_delete && $rID_brk)
				{
					mysql_query("COMMIT");  
					echo "1**".$mst_update_id."**".$txt_sample_devlopment_id."**0**".$dtls_update_id;
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**".$mst_update_id."**".$txt_sample_devlopment_id."**0";
				}
			
			}
			if($db_type==2 || $db_type==1 )
			{
				if($rID_mst && $rID_dtls && $rID_brk_delete && $rID_brk)
				{
					oci_commit($con);  
					echo "1**".$mst_update_id."**".$txt_sample_devlopment_id."**0**".$dtls_update_id;
				}
				else
				{
					oci_rollback($con);
					echo "10**".$mst_update_id."**".$txt_sample_devlopment_id."**0";
				}
			}
		}
		disconnect($con);
		die;
	}
 
	else if ($operation==2)  // Delete Here---------------------------------------------------------- 
	{
 		
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		$mst_update_id=str_replace("'","",$mst_update_id);
		$dtls_update_id=str_replace("'","",$dtls_update_id);


 		$rID = sql_delete("sample_sewing_output_dtls","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'sample_sewing_output_mst_id  ',$mst_update_id,1);
		$dtlsrID = sql_delete("sample_sewing_output_colorsize","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'sample_sewing_output_mst_id',$mst_update_id,1);

 		if($db_type==0)
		{
			if($rID && $dtlsrID)
			{
				mysql_query("COMMIT");  
				echo "2**".$mst_update_id; 
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$mst_update_id; 
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);   
				echo "2**".$mst_update_id; 
			}
			else
			{
				oci_rollback($con);
				echo "10**".$mst_update_id; 
			}
		}
		disconnect($con);
		die;
	}
}//fnc.............;




if($action=="sewing_output_print")
{
	extract($_REQUEST);
	list($company_id,$mst_id,$sample_id)=explode('*',$data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');
	$buyer_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$sewing_library=return_library_array( "select id, line_name from  lib_sewing_line", "id", "line_name"  );
	
	$res = sql_select("select buyer_name,style_ref_no,item_name from sample_development_mst where id=$mst_id  and status_active=1 and is_deleted=0"); 
  	foreach($res as $rows)
	{
		$dtls_data['buyer_name']=$rows[csf('buyer_name')];
		$dtls_data['style_ref_no']=$rows[csf('style_ref_no')];
		$dtls_data['item_name']=$rows[csf('item_name')];
	}
	
	$sql="
	Select 
		a.sewing_date,a.line_no,a.reporting_hour ,a.qc_pass_qty,a.remarks ,a.challan_no ,
		b.color_id,b.size_id,b.size_pass_qty,b.size_rej_qty
	from 
		sample_sewing_output_dtls a,sample_sewing_output_colorsize b
	where 
		a.sample_sewing_output_mst_id=$mst_id and a.sample_name=$sample_id and a.id=b.sample_sewing_output_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	 	 
	$sql_result=sql_select($sql);
	foreach ($sql_result as $rows)
	{
		$dtls_data['sewing_date']=change_date_format($rows[csf('sewing_date')]);	
		$dtls_data['line_no']=$rows[csf('line_no')];	
		$dtls_data['reporting_hour']=$rows[csf('reporting_hour')];	
		$dtls_data['qc_pass_qty']=$rows[csf('qc_pass_qty')];	
		$dtls_data['remarks']=$rows[csf('remarks')];
		$dtls_data['challan_no']=$rows[csf('challan_no')];
		
		$size_arr[]=$rows[csf('size_id')];
		
		$tot_color_good_qty[$rows[csf('color_id')]]+=$rows[csf('size_pass_qty')];
		$tot_color_rej_qty[$rows[csf('color_id')]]+=$rows[csf('size_rej_qty')];
		
		$tot_size_good_qty[$rows[csf('size_id')]]+=$rows[csf('size_pass_qty')];
		$tot_size_rej_qty[$rows[csf('size_id')]]+=$rows[csf('size_rej_qty')];
		
		$good_qty[$rows[csf('color_id')]][$rows[csf('size_id')]]=$rows[csf('size_pass_qty')];	
		$rej_qty[$rows[csf('color_id')]][$rows[csf('size_id')]]=$rows[csf('size_rej_qty')];	
		
		$is_reject+=$rows[csf('size_rej_qty')];
	}

$tot_size=count($size_arr);
$width=round((100/$tot_size)+25);
$width_2=($width*$tot_size)+650;

?>
<div style="width:<? echo $width_2;?>px;">
    <table width="100%" cellspacing="0">
        <tr>
            <td colspan="3" align="center" style="font-size:22px"><strong><? echo $company_library[$company_id]; ?></strong></td>
        </tr>
        <tr>
        	<td colspan="3" align="center" style="font-size:12px">  
				<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$company_id"); 
					foreach ($nameArray as $result)
					{ 
					?>
						Plot No: <? echo $result[csf('plot_no')]; ?> 
						Level No: <? echo $result[csf('level_no')]?>
						Road No: <? echo $result[csf('road_no')]; ?> 
						Block No: <? echo $result[csf('block_no')];?> 
						City No: <? echo $result[csf('city')];?> 
						Zip Code: <? echo $result[csf('zip_code')]; ?> 
						Province No: <?php echo $result[csf('province')];?> 
						Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
						Email Address: <? echo $result[csf('email')];?> 
						Website No: <? echo $result[csf('website')];
					}
                ?> 
            </td>  
        </tr>
        <tr>
            <td colspan="3" align="center"><strong>Sample Sewing Output Challan</strong></td>
        </tr>
        <tr>
        	<td colspan="3"><strong>Challan : <? echo $dtls_data['challan_no']; ?></strong></td>
        </tr>
        <tr>
            <td><strong>Buyer : </strong><? echo $buyer_library[$dtls_data['buyer_name']]; ?></td>
            <td><strong>Style Ref. : </strong><? echo $dtls_data['style_ref_no']; ?></td>
            <td><strong>QC Pass Qty : </strong><? echo $dtls_data['qc_pass_qty']; ?> Pcs</td>
        </tr>
        <tr>
            <td><strong>Sample Development ID : </strong><? echo $mst_id; ?></td>
            <td><strong>Item : </strong><? echo $garments_item[$dtls_data['item_name']]; ?></td>
            <td><strong>Delivery Date : </strong><? echo $dtls_data['sewing_date'];  ?></td>
        </tr>
        <tr>
            <td><strong>Sample Name : </strong><? echo $sample_name_library[$sample_id]; ?></td>
            <td><strong>Sewing Line : </strong><? echo $sewing_library[$dtls_data['line_no']]; ?></td>
            <td><strong>Reporting Hour : </strong><? echo $dtls_data['reporting_hour']; ?></td>
        </tr>
        <tr>
            <td colspan="3"><strong><p>Remarks: <? echo $dtls_data['remarks']; ?></p></strong></td>
        </tr>
    </table>
    <br>


<!-- ......................Good Qty Part...................................... -->
   <div><strong> Good Qty.</strong></div>
    <table border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <tr>
                <th rowspan="2" width="30">SL</th>
                <th rowspan="2">Color</th>
                <th colspan="<? echo $tot_size;?>">Size</th>
                <th rowspan="2" width="80">QC Pass Qty(Pcs)</th>
                <th rowspan="2" width="80">Reject Qty</th>
            </tr>
            <tr>
				<?
                foreach ($size_arr as $size_id)
                {
                    ?>
                    <th align="center" width="<? echo $width;?>"><? echo $size_library[$size_id]; ?></th>
                    <?
                }
                ?>
            </tr>
        </thead>
        <tbody>
			<?
            //$mrr_no=$dataArray[0][csf('issue_number')];
            $i=1;
            foreach($good_qty as $color_id=>$size_val)
            {
             $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF"; 

                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td><? echo $i; ?></td>
                        <td><? echo $color_library[$color_id]; ?></td>
                        <?
                        foreach ($size_arr as $size_id)
                        {
                            ?>
                            <td align="right"><? echo $good_qty[$color_id][$size_id]; ?></td>
                            <?
                        }
                        ?>
                        <td align="right"><? echo $tot_color_good_qty[$color_id]; ?></td>
                        <td align="right"><? echo $tot_color_rej_qty[$color_id]; ?></td>
                    </tr>
                    <?
					$i++;
                }
            ?>
        </tbody>
        <tr>
            <td colspan="2" align="right"><strong>Grand Total :</strong></td>
            <?
				 foreach ($size_arr as $size_id)
				{
					?>
                    <td align="right"><?php echo $tot_size_good_qty[$size_id]; ?></td>
                    <?
				}
			?>
            <td colspan="2">&nbsp;</td>
        </tr>                           
    </table>
   <br>
	
<!-- ......................Reject Qty Part...................................... -->
<? if($is_reject){?>   
   <div><strong> Reject Qty.</strong></div>
    <table border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <tr>
                <th rowspan="2" width="30">SL</th>
                <th rowspan="2">Color</th>
                <th colspan="<? echo $tot_size;?>">Size</th>
                <th rowspan="2" width="80">QC Pass Qty(Pcs)</th>
                <th rowspan="2" width="80">Reject Qty</th>
            </tr>
            <tr>
				<?
                foreach ($size_arr as $size_id)
                {
                    ?>
                    <th align="center" width="<? echo $width;?>"><? echo $size_library[$size_id]; ?></th>
                    <?
                }
                ?>
            </tr>
        </thead>
        <tbody>
			<?
            //$mrr_no=$dataArray[0][csf('issue_number')];
            $i=1;
            foreach($rej_qty as $color_id=>$size_val)
            {
             $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF"; 

                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td><? echo $i; ?></td>
                        <td><? echo $color_library[$color_id]; ?></td>
                        <?
                        foreach ($size_arr as $size_id)
                        {
                            ?>
                            <td align="right"><? echo $rej_qty[$color_id][$size_id]; ?></td>
                            <?
                        }
                        ?>
                        <td align="right"><? echo $tot_color_good_qty[$color_id]; ?></td>
                        <td align="right"><? echo $tot_color_rej_qty[$color_id]; ?></td>
                    </tr>
                    <?
					$i++;
                }
            ?>
        </tbody>
        <tr>
            <td colspan="2" align="right"><strong>Grand Total :</strong></td>
            <?
				 foreach ($size_arr as $size_id)
				{
					?>
                    <td align="right"><?php echo $tot_size_rej_qty[$size_id]; ?></td>
                    <?
				}
			?>
            <td colspan="2">&nbsp;</td>
        </tr>                           
    </table>
 <? }else{echo "Note: Not found any reject quantity.";} ?>
   <br>
	
	<? //echo signature_table(29, $company_id, "750px"); ?>
  </div>
</div>
<?
exit();
}//fnc.............;




if($action=="load_drop_down_sewing_line_floor")
{
	$explode_data = explode("_",$data);	
	$prod_reso_allocation = $explode_data[2];
	$txt_sewing_date = $explode_data[3];
	$cond="";
	
	if($prod_reso_allocation==1)
	{
		$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
		$line_array=array();
		if($txt_sewing_date=="")
		{ 
			if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and location_id= $explode_data[1]";
			if( $explode_data[0]!=0 ) $cond = " and floor_id= $explode_data[0]";
			
			$line_data=sql_select("select id, line_number from prod_resource_mst where is_deleted=0 $cond");
		}
		else
		{
			if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and a.location_id= $explode_data[1]";
			if( $explode_data[0]!=0 ) $cond = " and a.floor_id= $explode_data[0]";
			
			if($db_type==0)
			{
				$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and b.pr_date='".change_date_format($txt_sewing_date,'yyyy-mm-dd')."' and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id");
			}
			if($db_type==2 || $db_type==1)
			{	
				$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and b.pr_date='".date("j-M-Y",strtotime($txt_sewing_date))."' and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number");
				
			}
		}

		foreach($line_data as $row)
		{
			$line='';
			$line_number=explode(",",$row[csf('line_number')]);
			foreach($line_number as $val)
			{
				if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
			}
			
			$line_array[$row[csf('id')]]=$line;
		}

		echo create_drop_down( "cbo_sewing_line", 110,$line_array,"", 1, "--- Select ---", $selected, "",0,0 );
	}
	else
	{
		if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and location_name= $explode_data[1]";
		if( $explode_data[0]!=0 ) $cond = " and floor_name= $explode_data[0]";
		
		echo create_drop_down( "cbo_sewing_line", 110, "select id,line_name from lib_sewing_line where  is_deleted=0 and status_active=1 and floor_name!=0 $cond order by line_name","id,line_name", 1, "--- Select ---", $selected, "",0,0 );
	}
}



if($action=="create_po_search_list_view")
{
	$data=explode('_',$data);
	
	if ($data[2]!=0) $company=" and company_id='$data[2]'"; else { echo "Please Select Company First."; die; }
	if ($data[3]!=0) $buyer=" and buyer_name='$data[3]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	if($data[0]==1)
		{
		   if (trim($data[1])!="") $style_id_cond=" and id='$data[1]'"; else $style_id_cond="";
		   if ($data[6]!="") $style_cond=" and style_ref_no='$data[6]'"; else $style_cond="";
		}
	
	if($data[0]==4 || $data[0]==0)
		{
		  if (trim($data[1])!="") $style_id_cond=" and id like '%$data[1]%' "; else $style_id_cond="";
		  if ($data[6]!="") $style_cond=" and style_ref_no like '%$data[6]%' "; else $style_cond="";
		}
	
	if($data[0]==2)
		{
		  if (trim($data[1])!="") $style_id_cond=" and id like '$data[1]%' "; else $style_id_cond="";
		  if ($data[6]!="") $style_cond=" and style_ref_no like '$data[6]%' "; else $style_cond="";
		}
	
	if($data[0]==3)
		{
		  if (trim($data[1])!="") $style_id_cond=" and id like '%$data[1]' "; else $style_id_cond="";
		  if ($data[6]!="") $style_cond=" and style_ref_no like '%$data[6]' "; else $style_cond="";
		}
	
	
	if($db_type==0)
	{
	if ($data[4]!="" &&  $data[5]!="") $estimated_shipdate  = "and estimated_shipdate  between '".change_date_format($data[4], "yyyy-mm-dd", "-")."' and '".change_date_format($data[5], "yyyy-mm-dd", "-")."'"; else $estimated_shipdate ="";
	}
	if($db_type==2)
	{
	if ($data[4]!="" &&  $data[5]!="") $estimated_shipdate  = "and estimated_shipdate  between '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[5], "yyyy-mm-dd", "-",1)."'"; else $estimated_shipdate ="";
	}
	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	
	$dealing_marchant=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
	$team_leader=return_library_array( "select id, team_leader_name from lib_marketing_team",'id','team_leader_name');
	
	$arr=array (1=>$comp,2=>$buyer_arr,4=>$product_dept,5=>$team_leader,6=>$dealing_marchant);
	
	$sql= "select id,company_id,buyer_name,style_ref_no,product_dept,team_leader,dealing_marchant,article_no from sample_development_mst where status_active=1 and is_deleted=0 $company $buyer $style_id_cond $style_cond $estimated_shipdate order by id";
		
		echo  create_list_view("list_view", "Style Id,Company,Buyer Name,Style Name,Product Department,Team Leader,Dealing Merchant,Article Number", "60,140,140,100,90,90,90,70","900","240",0, $sql , "js_set_value", "id", "", 1, "0,company_id,buyer_name,0,product_dept,team_leader,dealing_marchant,0", $arr , "id,company_id,buyer_name,style_ref_no,product_dept,team_leader,dealing_marchant,article_no", "",'','0,0,0,0,0,0,0,0') ;

	exit();
}


//--------======================----------end------------------------------------------==================

/*
$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );




if($action=="line_disable_enable")
{
	if($data==1)
		echo "disable_enable_fields('cbo_sewing_line',0,'','');\n";
	else
	{
		echo "$('#cbo_sewing_line').val(0);\n";
		echo "disable_enable_fields('cbo_sewing_line',1,'','');\n";	
	}
}

if($action=="display_bl_qnty")
{
	$explode_data = explode("**",$data);
	$sewing_company=$explode_data[0];
	$source=$explode_data[1];
	$po_break_down_id=$explode_data[2];
	$item_id=$explode_data[3];
	$country_id=$explode_data[4];
	
	$dataArray=sql_select("select SUM(CASE WHEN production_type=4 THEN production_quantity END) as totalinput,SUM(CASE WHEN production_type=5 THEN production_quantity ELSE 0 END) as totalsewing from pro_garments_production_mst WHERE po_break_down_id='$po_break_down_id' and item_number_id='$item_id' and country_id='$country_id' and production_source='$source' and serving_company='$sewing_company' and status_active=1 and is_deleted=0");
	foreach($dataArray as $row)
	{  
		echo "$('#txt_input_quantity').val('".$row['totalinput']."');\n";
		echo "$('#txt_cumul_sewing_qty').val('".$row['totalsewing']."');\n";
		$yet_to_produced = $row['totalinput']-$row['totalsewing'];
		echo "$('#txt_yet_to_sewing').val('".$yet_to_produced."');\n";
	}
	
	exit();
}



*/








/*

if ($action=="piece_rate_order_cheack")
{
	$ex_data=explode('**',$data);
	if($db_type==0)
	{
		$piece_sql="select a.sys_number, sum(b.wo_qty) as wo_qty from piece_rate_wo_mst a, piece_rate_wo_dtls b where a.id=b.mst_id and b.order_id=$ex_data[0] and b.item_id=$ex_data[1] and a.rate_for=30 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.sys_number";
	}
	else if($db_type==2)
	{
		$piece_sql="select a.sys_number, sum(b.wo_qty) as wo_qty from piece_rate_wo_mst a, piece_rate_wo_dtls b where a.id=b.mst_id and b.order_id=$ex_data[0] and b.item_id=$ex_data[1] and a.rate_for=30 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.sys_number";
	}
	//echo $piece_sql;
	$data_array=sql_select($piece_sql,0);
	if(count($data_array)>0)
	{
		$sys_number=""; $wo_qty=0;
		foreach($data_array as $row)
		{
			if ($sys_number=="") $sys_number=$row[csf('sys_number')]; else $sys_number.=','.$row[csf('sys_number')];
			$wo_qty+=$row[csf('wo_qty')];
		}
		echo "1"."_".$sys_number."_".$wo_qty;
	}
	else
	{
		echo "0_";
	}
	exit();	
}

*/



?>