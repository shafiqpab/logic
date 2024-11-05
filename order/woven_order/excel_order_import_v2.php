<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../includes/common.php');

echo load_html_head_contents("Order Import V2","../../", 1, 1, $unicode,1,'');

$txt_job_no=$_POST["txt_job_no"];

include( 'excel_reader.php' );
$output = `uname -a`;
?>
<script type="text/javascript">
	<?
		$field_mst='';
		$message_mst='';
		foreach($_SESSION['logic_erp']['mandatory_field'][518] as $key=>$value)
		{
			$field_mst.='*'.$value;
			$message_mst.='*'.$value;
		}
		echo "var mandatory_field = '". ($field_mst) . "';\n";
		echo "var mandatory_message = '". ($message_mst) . "';\n";

	?>
</script>
<?
if( isset( $_POST["submit"] ) )
{	
	error_reporting(E_ALL);
	//ini_set('display_errors', '1');
	
	extract($_REQUEST);
	
	foreach (glob("files/"."*.xls") as $filename){			
		@unlink($filename);
	}
	foreach (glob("files/"."*.xlsx") as $filename){			
		@unlink($filename);
	}
	//die;
	$source = $_FILES['uploadfile']['tmp_name'];
	$targetzip ='files/'.$_FILES['uploadfile']['name'];
	$file_name=$_FILES['uploadfile']['name'];
	unset($_SESSION['excel']);
	if (move_uploaded_file($source, $targetzip)) 
	{
		$excel = new Spreadsheet_Excel_Reader($targetzip);  
		//$excel->read($targetzip);
		$card_colum=0; $m=1; $style_data_array=array(); $po_data_array=array(); $country_data_array=array(); $style_all_data_arr=array();
		for ($i = 1; $i <= $excel->sheets[0]['numRows']; $i++) 
		{
			if($m==1)
			{
				for ($j = 1; $j <= $excel->sheets[0]['numCols']; $j++) 
				{
					//$k++;
					//echo "\"".$data->sheets[0]['cells'][$i][4]."\",";
					//$card_colum=$excel->sheets[0]['cells'][$i][$j];
					
					//echo $card_colum.'=='.$i.'=='.$j.'<br>';
					/*$date_fld2=$data->sheets[0]['cells'][$i][$date_fld];
					$in_out_time=$data->sheets[0]['cells'][$i][$time_fld_len[0]].",".$data->sheets[0]['cells'][$i][$time_fld_len[1]];*/
					//print_r($in_out_time_arr);
					//$date_time_colum=$data->sheets[0]['cells'][$i][4];
				}
				$m++;
			}
			else
			{
				$str_rep=array("//", "&", "*", "(", ")", "=","'",",","\r", "\n",'"','#');
				$po_number=str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][3]);
				if(trim($po_number)!="")
				{
					$all_data=''; $alldatapo=""; $alldatacountry="";
					
					$style_ref=str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][1]);
					//echo $style_ref.'<br>';
					
					$style_description=str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][2]);
					//$style_data_array[$style_ref]=$style_description;
					
					$pubshipdate=date("Y-m-d",strtotime($excel->sheets[0]['cells'][$i][4]));
					//$pubshipdate=strtotime($excel->sheets[0]['cells'][$i][4]);
					
					//$pubshipdate=date("d-M-Y",strtotime($pubshipdate));
					$shipdate=date("Y-m-d",strtotime($excel->sheets[0]['cells'][$i][5]));
					$country=$excel->sheets[0]['cells'][$i][6];
					$countryshipdate=date("Y-m-d",strtotime($excel->sheets[0]['cells'][$i][7]));
					$color_name=str_replace($str_rep,' ',trim($excel->sheets[0]['cells'][$i][8]));
					$size_name=str_replace($str_rep,' ',trim($excel->sheets[0]['cells'][$i][9]));
					$country_po_qty=$excel->sheets[0]['cells'][$i][10]*1;
					$excutPer=$excel->sheets[0]['cells'][$i][11]*1;
					$avg_rate=$excel->sheets[0]['cells'][$i][12]*1;
					$poRecDate="";
					if($excel->sheets[0]['cells'][$i][13]!="") $poRecDate=date("Y-m-d",strtotime($excel->sheets[0]['cells'][$i][13])); else $poRecDate=date("Y-m-d");
					
					//$str_rep=array("/", "&", "*", "(", ")", "=","'",",","\r", "\n",'"','#');
					//$po_remarks=str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][46]);
					
					//$po_data_array[$style_ref][$po_number]=$po_rec_date.'__'.$po_ship_date.'__'.$avg_rate.'__'.$po_remarks; 
					/*if(trim($excel->sheets[0]['cells'][$i][12])!="")
					{
						$color_name=str_replace($str_rep,' ',trim($excel->sheets[0]['cells'][$i][13])).'-'.trim($excel->sheets[0]['cells'][$i][12]);
					}
					else
					{
						$color_name=str_replace($str_rep,' ',trim($excel->sheets[0]['cells'][$i][13]));
					}*/
					//echo $pubshipdate.'-'.change_date_format($pubshipdate).'<br>';
					
					if(($country_po_qty*1)>0)
					{
						$all_data=$style_description.'__'.change_date_format($pubshipdate).'__'.change_date_format($shipdate).'__'.$country.'__'.change_date_format($countryshipdate).'__'.$avg_rate.'__'.$excutPer.'__'.change_date_format($poRecDate);
						
						$style_all_data_arr[$style_ref][$po_number][$color_name][$size_name][$all_data]+=$country_po_qty;
					}
				}
			}
		}
		$_SESSION['excel']=$style_all_data_arr;
		//print_r($style_all_data_arr);
		//die;
	
		?>
        <script>
			var permission='<? echo $permission; ?>';
			if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
			
			function fnc_excel_import(operation)
			{
				freeze_window(operation);
				if($('#txt_ready_to_save').val()==0)
				{
					alert('Please check Country and Upload the file again.');
					release_freezing();
					return;
				}
				if($('#txt_ready_to_save').val()==2)
				{
					alert('Shipment date can not be greater than Receive Date.\n Please check and Upload the file again.');
					release_freezing();
					return;
				}
				
				if( form_validation('cbo_company_id*cbo_location_id*cbo_buyer_id*cbo_product_department*cbo_currercy_id*cbo_season_year*cbo_season_id*cbo_prod_catgory*cbo_team_leader*cbo_dealing_merchant*cbo_order_uom*cbo_gmtsItem_id*tot_smv_qty*cbo_ship_mode'+mandatory_field,'Company*Location*Buyer*Prod. Dept.*Currency*Season Year*Season*Prod. Catgory*Team Leader*Dealing Marchant*Order Uom*Gmts Item*SMV*Ship Mode'+mandatory_message)==false)
				{
					release_freezing();
					return;
				}
				
				var totStyleCount=($('#styPoSzInc_id').val()*1)-1;
				
				var dataString="";
				
				for(var i=1; i<=totStyleCount; i++)
				{
					var totPoCount=($('#poCount_'+i).val()*1);
					
					for(var j=1; j<=totPoCount; j++)
					{
						if( form_validation('cboOrderStatus_'+i+'_'+j,'Order Status')==false)
						{
							release_freezing();
							return;
						}
						else
						{
							dataString += '&styleRef_' + i + '=' + trim($('#styleRef_'+i).text()) + '&poNo_'+i+'_'+j+'=' + trim($('#poNo_'+i+'_'+j).text()) + '&cboOrderStatus_'+i+'_'+j+'=' + trim($('#cboOrderStatus_'+i+'_'+j).val())+ '&txtpoRemarks_'+i+'_'+j+'=' + trim($('#txtpoRemarks_'+i+'_'+j).val()); 
						}
					}
				}
				if(dataString=="")
				{
					alert('Please check Order Status or Others Data.');
					release_freezing();
					return;
				}
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_buyer_id*cbo_style_owner_id*cbo_product_department*cbo_currercy_id*cbo_season_year*cbo_season_id*cbo_brand_id*cbo_prod_catgory*cbo_team_leader*cbo_dealing_merchant*cbo_factory_merchant*cbo_order_uom*cbo_gmtsItem_id*tot_smv_qty*cbo_packing*cbo_ship_mode*txt_sclc*txt_job_no*hid_ws_data',"../../")+dataString;
				//alert(data);
				http.open("POST","requires/excel_order_import_controller_v2.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_excel_import_reponse;
			}
			
			function fnc_excel_import_reponse()
			{
				if(http.readyState == 4) 
				{
					var reponse=trim(http.responseText).split('**');
					//alert (reponse);
					alert(reponse[1]);
					release_freezing();
					//show_msg(trim(reponse[0]));
				}
			}
			
			function fnc_style_owner(val)
			{
				$('#cbo_style_owner_id').val(val);
			}
			
			function fnc_variable_settings_check()
			{
				var company_id=$('#cbo_company_id').val();
				var all_variable_settings=return_ajax_request_value(company_id, 'load_variable_settings', 'requires/excel_order_import_controller_v2');
				
				//alert(all_variable_settings)
				var ex_variable=all_variable_settings.split("_");
				var copy_quotation=ex_variable[0];
				var set_smv_id=ex_variable[1];
				
				var txt_style_ref="";
				
				var row_num=$('#scanning_tbl tr').length;
				for (var k=1;k<=row_num; k++)
				{
					if($('#styleRef_'+k).text()!="")
					{
						if(txt_style_ref=="") txt_style_ref=trim($('#styleRef_'+k).text()); else txt_style_ref+="***"+trim($('#styleRef_'+k).text());
					}
				}
				
				var item_id=$('#cbo_gmtsItem_id').val();
				var cbo_buyer_name=$('#cbo_buyer_id').val();
				
				if(set_smv_id==3 || set_smv_id==8)
				{
					$("#tot_smv_qty").attr('readonly','readonly');
					
					if(form_validation('cbo_company_id*cbo_buyer_id*cbo_gmtsItem_id','Company*Buyer*Gmts. Item')==false)
					{
						return;
					}
					else
					{
						var page_link="requires/excel_order_import_controller_v2.php?action=open_smv_list&txt_style_ref="+txt_style_ref+"&set_smv_id="+set_smv_id+"&item_id="+item_id+"&cbo_company_name="+company_id+"&cbo_buyer_name="+cbo_buyer_name;
					}
					
					emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'SMV Pop Up', 'width=650px,height=220px,center=1,resize=1,scrolling=0','../')
					emailwindow.onclose=function()
					{
						var theform=this.contentDoc.forms[0];
						var selected_smv_data=this.contentDoc.getElementById("txt_selected").value;
						
						var smv_data=selected_smv_data.split(",");
						var wscount=smv_data.length;
						var sewsmv=0;
						for (var i=0; i<wscount; i++)
						{
							var smvstr=smv_data[i].split("_");
							sewsmv=sewsmv+(smvstr[0]*1);
						}
						
						var smvavg=sewsmv/wscount;
						$("#tot_smv_qty").val( number_format(smvavg,4,'.','' ) );
						$("#hid_ws_data").val(selected_smv_data);
						
						$("#cbo_company_id").attr("disabled",true);
						$("#cbo_buyer_id").attr("disabled",true);
						$("#cbo_gmtsItem_id").attr("disabled",true);
					}
				}
				else
				{
					$('#tot_smv_qty').removeAttr('readOnly','readOnly');
				}
			}
			
			function fnc_statuscopy(data,value)
			{
				if(document.getElementById('copy_id').checked==true)
				{
					var exdata=trim(data).split('_');
					
					var totStyleCount=($('#styPoSzInc_id').val()*1)-1;
					for(var i=exdata[0]; i<=totStyleCount; i++)
					{
						var totPoCount=($('#poCount_'+i).val()*1);
						if(exdata[0]==i) { var pinc=exdata[1]; } else { var pinc=1; } 
						//alert(totPoCount) 
						for(var j=pinc; j<=totPoCount; j++)
						{
							$('#cboOrderStatus_'+i+'_'+j).val(value);
							//alert(i+'_'+j+'_'+value)
						}
					}
				}
			}
			
			function fnc_brandload()
			{
				var buyer=$('#cbo_buyer_id').val();
				if(buyer!=0)
				{
					load_drop_down( 'requires/excel_order_import_controller_v2', buyer, 'load_drop_down_brand', 'brand_td');
					load_drop_down( 'requires/excel_order_import_controller_v2', buyer, 'load_drop_down_season', 'season_td');
				}
			}
			
		</script>
        </head>
        <body onLoad="set_hotkey(); fnc_brandload();">
        <div style="width:100%;" align="center">
        <!-- Important Field outside Form -->  
            <? echo load_freeze_divs ("../../",$permission); ?>
            <fieldset style="width:1450px;">
                <form name="excelImport_1" id="excelImport_1" autocomplete="off"> 
          <table width="1450" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >      
          	<thead>
            	<tr>
                    <th width="100" class="must_entry_caption">Company</th>
                    <th width="100" class="must_entry_caption">Location</th>
                    <th width="100" class="must_entry_caption">Buyer</th>
                    <th width="100">Style Owner</th>
                    <th width="80" class="must_entry_caption">Prod. Dept</th>
                    <th width="60" class="must_entry_caption">Currency</th>
                    <th width="60" class="must_entry_caption">Season Year</th>
                    <th width="60" class="must_entry_caption">Season</th>
                    <th width="60">Brand</th>
                    <th width="70" class="must_entry_caption">Prod. Category</th>
                    <th width="80" class="must_entry_caption">Team Leader</th>
                    <th width="80" class="must_entry_caption">Dealing Merchant</th>
                    <th width="80">Factory Merchant</th>
                    <th width="50" class="must_entry_caption">Order Uom</th>
                    <th width="100" class="must_entry_caption">Product Name</th>
                    <th width="50" class="must_entry_caption">SMV/ Pcs</th>
                    <th width="70">Packing</th>
                    <th width="60">Ship Mode</th>
                    <th>SC/LC</th>
                 </tr>
              </thead>
              <tbody>
              	<tr>
                	<td><?=create_drop_down( "cbo_company_id", 100, "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-Select Company-", $selected, "load_drop_down( 'requires/excel_order_import_controller_v2', this.value, 'load_drop_down_location', 'location_td'); load_drop_down( 'requires/excel_order_import_controller_v2', this.value, 'load_drop_down_buyer', 'buyer_td'); fnc_style_owner(this.value); "); ?></td>
                    <td id="location_td"><?=create_drop_down( "cbo_location_id", 100, $blank_array,"", 1, "-Location-", $selected, ""); ?></td>
                    <td id="buyer_td"><?=create_drop_down( "cbo_buyer_id", 100, $blank_array,"", 1, "-Buyer-", $selected, ""); ?></td>
                    <td id="owner_td"><?=create_drop_down( "cbo_style_owner_id", 100, "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-Style Owner-", $selected, ""); ?></td>
                    <td><?=create_drop_down( "cbo_product_department", 80, $product_dept, "", 1, "-Select-", $selected, "", "", "" ); ?>
                    <td><?=create_drop_down( "cbo_currercy_id", 60, $currency,'', 0, "",2, "" ); ?></td>
                    <td><?=create_drop_down( "cbo_season_year", 60, create_year_array(),"", 1,"-S.Year-", 1, "",0,"" ); ?></td>
                    <td id="season_td"><?=create_drop_down( "cbo_season_id", 60, $blank_array,'', 1, "-Select Season-",$selected, "" ); ?></td>
                    <td id="brand_td"><?=create_drop_down( "cbo_brand_id", 60, $blank_array,'', 1, "-Brand-",$selected, "" ); ?></td>
                    <td><?=create_drop_down( "cbo_prod_catgory", 70, $product_category,"", 1, "-Category-", 1, "","","" ); ?></td>
                    <td><?=create_drop_down( "cbo_team_leader", 80, "select id, team_leader_name from lib_marketing_team where project_type=1 and status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "-Select Team-", $selected, "load_drop_down( 'requires/excel_order_import_controller_v2', this.value, 'load_drop_down_dealing_merchant', 'div_marchant'); load_drop_down( 'requires/excel_order_import_controller_v2', this.value, 'load_drop_down_factory_merchant', 'div_marchant_factory')" ); ?></td>
                    <td id="div_marchant"><?=create_drop_down( "cbo_dealing_merchant", 80, $blank_array,"", 1, "-Team Member-", $selected, "" ); ?></td>
                    <td id="div_marchant_factory"><?=create_drop_down( "cbo_factory_merchant", 80, $blank_array,"", 1, "-Fac Merchent-", $selected, "" ); ?></td>
                    <td><?=create_drop_down( "cbo_order_uom",50, $unit_of_measurement, "",0, "", 1, "","","1" ); ?></td>
                    <td><?=create_drop_down( "cbo_gmtsItem_id", 100, $garments_item, 0, 1, "--Select Item--", $selected,"fnc_variable_settings_check();",0); ?></td>
                    <td>
                    	<input class="text_boxes_numeric" type="text" style="width:42px;" name="tot_smv_qty" id="tot_smv_qty" onDblClick="fnc_variable_settings_check();" />
                        <input type="hidden" name="txt_job_no" id="txt_job_no" style="width:50px;" value="<? echo $txt_job_no; ?>"/>
                        <input type="hidden" name="hid_ws_data" id="hid_ws_data" style="width:50px;" value=""/>
                    </td>
                    <td><?=create_drop_down( "cbo_packing", 90, $packing,"", 1, "--Select--", $selected, "","","" ); ?></td>
                    <td><?=create_drop_down( "cbo_ship_mode", 60,$shipment_mode, 1, "", $selected, "" ); ?></td>
                    <td><input style="width:40px;" type="text" class="text_boxes" name="txt_sclc" id="txt_sclc" placeholder="Write" /></td>
                </tr>
              </tbody>
           </table> 
           <br />  
        <table width="1360" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
			<thead>
            	<tr>
                	<th width="30" rowspan="2">SL.</th>
                    <th colspan="2">Style Details</th>
                    <th colspan="6">Po Details</th>
                    <th colspan="9">Country or Color Size Details</th>
                </tr>
                <tr>
                    <th width="100">Style Ref.</th>
                    <th width="100">Style Des.</th>
                    
                    <th width="80">Order Status<input type="checkbox" name="copy_id" id="copy_id" value="2" ></th>
                    <th width="100">Po No.</th>
                    <th width="60">Po Receive Date</th>
                    <th width="60">Pub. Ship. Date</th>
                    <th width="60">Ship. Date</th>
                    <th width="100">Po Remarks</th>
                    
                    <th width="80">Delivery Contry</th>
                    <th width="60">Country Ship Date</th>
                    <th width="100">Gmts. Color</th>
                    <th width="60">Gmts. Size</th>
                    <th width="70">Qty</th>
                    <th width="50">Rate</th>
                    <th width="80">Amount</th>
                    <th width="50">Ex. Cut %</th>
                    <th>Plan Cut Qty.</th>
                </tr>
			</thead>
		</table>
		<div style="width:1360px; max-height:320px; overflow-y:scroll" id="scroll_body" > 
		<table width="1342" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="scanning_tbl"><!--table_body-->
        	<?php
			$country_arr=return_library_array("select id, country_name from lib_country order by country_name","country_name","id");
			/*$code_arr=array();
			$code_sql=sql_select("select ultimate_country_code from lib_country_loc_mapping where status_active=1 and is_deleted=0 order by ultimate_country_code");
			foreach($code_sql as $row)
			{
				$code_arr[trim($row[csf('ultimate_country_code')])]=$row[csf('ultimate_country_code')];
			}
			unset($code_sql);*/
			$ready_to_save=1;
			$i=1; $sty=1; $st_name="";
			foreach($style_all_data_arr as $style_name=>$order_data)
			{
				$st=1; $pn=1; $count=1;
				foreach($order_data as $order_no=>$color_size_data)
				{
					$p=1; $ctpn=1;
					foreach($color_size_data as $color_val=>$size_data)
					{
						$s=1;//$style_all_data_arr[$style_ref][$po_number][$color_name][$size_name][$all_data]
						foreach($size_data as $size_val=>$extra_data)
						{
							foreach($extra_data as $ex_val=>$sizeqty)
							{	
								$ex_data=explode('__',$ex_val);
								
								$style_des=$pubshipdate=$shipdate=$countryName=$po_avg_rate=0; $country_qty=0; $country_amt=0; $countryShipDate=""; $planCutQty=0;
								
								$style_des=$ex_data[0];
								$pubshipdate=$ex_data[1];
								$shipdate=$ex_data[2];
								$countryName=$ex_data[3];
								$countryShipDate=$ex_data[4];
								$po_avg_rate=number_format($ex_data[5],2); 
								$exPer=$ex_data[6];
								
								$poRecDate=$ex_data[7];//date("d-m-Y");
								
								$country_qty=$sizeqty;
								
								if($exPer==0) $exPer="";
								if($exPer=="") $planCutQty=$country_qty; else $planCutQty=($country_qty*($exPer/100))+$country_qty;
								$planCutQty=number_format($planCutQty,0,'.','');
								$country_amt=number_format($sizeqty*$po_avg_rate,2,'.','');
								
								if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$td_color_code=""; 
								
								if( $country_arr[$countryName]=="" ) { $td_color_code="red"; $ready_to_save=0; }
								
								if(strtotime($poRecDate)>=strtotime($pubshipdate)) { $bgcolor="Red"; $ready_to_save=2; }
								if(($po_avg_rate*1)==0) { $bgcolor="Red"; $ready_to_save=2; }
								//echo $poRecDate.'='.$pubshipdate."<br>";
								?>
								<tr bgcolor="<?php echo $bgcolor; ?>" onClick="change_color('tr_1nd<?php echo $i; ?>','<?php echo $bgcolor; ?>')" id="tr_1nd<?php echo $i; ?>">
									<td width="30"><?php echo $i; ?></td>
									<?php if($st==1) { ?>
									<td width="100" id="styleRef_<?php echo $sty; ?>"><?php echo $style_name; ?></td>
									<td width="100" id="styleDes_<?php echo $sty; ?>"><?php echo $style_des; ?></td>
									<?php $st++; } else { ?>
									<td width="100">&nbsp;</td>
									<td width="100">&nbsp;</td>
									<?php } if($p==1) { $auto_id=''; $auto_id=$sty.'_'.$pn; ?>
                                    <td width="80"><?=create_drop_down( "cboOrderStatus_$auto_id", 75, $order_status,"", 1, "-Select-", $selected, "fnc_statuscopy('".$auto_id."',this.value);", "" ); ?></td>
									<td width="100" id="poNo_<?php echo $auto_id; ?>" ><?php echo $order_no; ?></td>
									<td width="60" id="recDate_<?php echo $auto_id; ?>"><?php echo $poRecDate; ?></td>
									<td width="60" id="pubShipDate_<?php echo $auto_id; ?>"><?php echo $pubshipdate; ?></td>
									<td width="60" id="shipDate_<?php echo $auto_id; ?>"><?php echo $shipdate; ?></td>
									<td width="100"><input name="txtpoRemarks_<?php echo $auto_id; ?>" id="txtpoRemarks_<?php echo $auto_id; ?>" class="text_boxes" type="text" value="<?php echo $po_remark; ?>" style="width:86px;"/>
									</td>
									<?php  $p++; } else { ?>
									<td width="80">&nbsp;</td>
									<td width="100">&nbsp;</td>
									<td width="60">&nbsp;</td>
									<td width="60">&nbsp;</td>
									<td width="60">&nbsp;</td>
									<td width="100">&nbsp;</td>
									<?php } $ciid=''; $ciid=$sty.'_'.$pn.'_'.$ctpn; ?>
									<td width="80" id="country_<?php echo $ciid; ?>" bgcolor="<?php echo $td_color_code; ?>"><?php echo $countryName; ?></td>
									<td width="60" id="countryShipDate_<?php echo $ciid; ?>"><?php echo change_date_format($countryShipDate); ?></td>
									<td width="100" id="colorName_<?php echo $ciid; ?>"><?php echo $color_val; ?></td>
									<td width="60" id="sizeName_<?php echo $ciid; ?>" align="center"><?php echo $size_val; ?></td>
									<td width="70" id="countryQty_<?php echo $ciid; ?>" align="right"><?php echo $country_qty; ?></td>
									<td width="50" id="countryRate_<?php echo $ciid; ?>" align="right"><?php echo $po_avg_rate; ?></td>
									<td width="80" id="countryAmt_<?php echo $ciid; ?>" align="right"><?php echo $country_amt; ?></td>
                                    <td width="50" id="exCutPer_<?php echo $ciid; ?>" align="right"><?php echo $exPer; ?></td>
                                    <td id="planCut_<?php echo $ciid; ?>" align="right"><?php echo $planCutQty; ?></td>
								</tr>
								<?php
								$i++; $ctpn++; 
							}
						}
					}
					?><input type="hidden" class="text_boxes" id="couCount_<?php echo $sty.'_'.$pn; ?>" name="couCount_<?php echo $sty.'_'.$pn; ?>" style="width:30" value="<?php echo $ctpn-1; ?>"><?php 
					$pn++;
				}
				 ?><input type="hidden" class="text_boxes" id="poCount_<?php echo $sty; ?>" name="poCount_<?php echo $sty; ?>" style="width:30" value="<?php echo $pn-1; ?>"><?php 
				$sty++; 
			}
			//echo $st_name;
			?>
            <input type="hidden" class="text_boxes" id="txt_ready_to_save" name="txt_ready_to_save" style="width:80" value="<?php echo $ready_to_save; ?>">
            <input type="hidden" class="text_boxes" id="styPoSzInc_id" name="styPoSzInc_id" style="width:80" value="<?php echo $sty; ?>">
        </table>
        </div>
        </form>
        </fieldset>
		<div>
            <table>
            	<tr style="border:none">
                    <td align="center" colspan="15" class="button_container">
                         <input type="button" id="show_button" align="middle" class="formbutton" style="width:100px" value="Save" onClick="fnc_excel_import(0);" />
                    </td>
                </tr>
           </table>
      	</div>
        </div>
	</body>
    <script> if ($('#txt_job_no').val()!="") get_php_form_data( $('#txt_job_no').val(), 'populate_job_data_form','requires/excel_order_import_controller_v2');</script>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>
		/*var clicked_index=new Array;
		function check_me( tid )
		{
			if(clicked_index[tid]==undefined )
			{
				$('#cboDeliveryCountry_1_1_1').clone().appendTo( '#'+tid +'');
				clicked_index[tid]=tid;
			}
		}
		
		$("#scanning_tbl").find('tbody tr').each(function()
		{
			var trId =$(this).find("td:eq(12)").attr('id').split('_');
			
			var delCountry_id="cboDeliveryCountry_"+trId[1]+"_"+trId[2]+"_"+trId[3];
			var code_id="cboCodeId_"+trId[1]+"_"+trId[2]+"_"+trId[3];
			var country_id="cboCountryId_"+trId[1]+"_"+trId[2]+"_"+trId[3];
			var countryCode_id="cboCountryCode_"+trId[1]+"_"+trId[2]+"_"+trId[3];
			//alert(country_id);
			$(this).find('select[name="cboDeliveryCountry[]"]').removeAttr('id').attr('id',delCountry_id);
			$(this).find('select[name="cboCodeId[]"]').removeAttr('id').attr('id',code_id);
			$(this).find('select[name="cboCountryId[]"]').removeAttr('id').attr('id',country_id);
			$(this).find('select[name="cboCountryCode[]"]').removeAttr('id').attr('id',countryCode_id);
			
			$(this).find('select[name="cboDeliveryCountry[]"]').removeAttr("onChange").attr("onChange","fnc_country_data_load(1,'"+trId[1]+"_"+trId[2]+"_"+trId[3]+"',this.value);");
			$(this).find('select[name="cboCodeId[]"]').removeAttr("onChange").attr("onChange","fnc_country_data_load(2,'"+trId[1]+"_"+trId[2]+"_"+trId[3]+"',this.value);");
			$(this).find('select[name="cboCountryId[]"]').removeAttr("onChange").attr("onChange","fnc_country_data_load(3,'"+trId[1]+"_"+trId[2]+"_"+trId[3]+"',this.value);");
			$(this).find('select[name="cboCountryCode[]"]').removeAttr("onChange").attr("onChange","fnc_country_data_load(4,'"+trId[1]+"_"+trId[2]+"_"+trId[3]+"',this.value);");
		});*/
    
    </script>
</html>
        <?php
	}
	else
	{
		echo "Failed";	
	}
	die;
}
?>
