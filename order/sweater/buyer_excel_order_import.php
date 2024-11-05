<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../includes/common.php');

echo load_html_head_contents("Order Import","../../", 1, 1, $unicode,1,'');

$txt_job_no=$_POST["txt_job_no"];
$buyer_format=$_POST["cbo_buyer_format"];
if($buyer_format==0)
{
	echo "Buyer Format Not Found";
	die;
}
$cdate=date("d-m-Y");

include( 'excel_reader.php' );
$output = `uname -a`;
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
		$card_colum=0; $m=1; $style_data_array=array(); $po_data_array=array(); $country_data_array=array(); $style_all_data_arr=array(); $styleArr=array();
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
				$all_data='';
				$str_rep=array("/", "&", "*", "(", ")", "=","'",",","\r", "\n",'"','#');
				//$str_rep= preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $excel->sheets[0]['cells'][$i]); 
				if($buyer_format==1)//PUMA
				{
					$style_ref=str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][10]);
					$style_description=str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][11]);
					//$style_data_array[$style_ref]=$style_description;
					
					$po_number=str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][1]);
					$countryCode=$excel->sheets[0]['cells'][$i][2];
					$code=$excel->sheets[0]['cells'][$i][5];
					$po_rec_date=$excel->sheets[0]['cells'][$i][8];
					$po_ship_date=$excel->sheets[0]['cells'][$i][19];
					$avg_rate=$excel->sheets[0]['cells'][$i][41];
					//$str_rep=array("/", "&", "*", "(", ")", "=","'",",","\r", "\n",'"','#');
					$po_remarks=str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][46]);
					
					//$po_data_array[$style_ref][$po_number]=$po_rec_date.'__'.$po_ship_date.'__'.$avg_rate.'__'.$po_remarks; 
					if(trim($excel->sheets[0]['cells'][$i][12])!="")
					{
						$color_name=str_replace($str_rep,' ',trim($excel->sheets[0]['cells'][$i][13])).'-'.trim($excel->sheets[0]['cells'][$i][12]);
					}
					else
					{
						$color_name=str_replace($str_rep,' ',trim($excel->sheets[0]['cells'][$i][13]));
					}
					$size_name=str_replace($str_rep,' ',trim($excel->sheets[0]['cells'][$i][39]));
					$country_po_qty=$excel->sheets[0]['cells'][$i][40];
					$exper="";
				}
				else if($buyer_format==2)//GAP
				{
					$exper="";
					//echo $i.'<br>';
					$style_ref=str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][4]);
					$style_description=str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][2]);
					//$style_data_array[$style_ref]=$style_description;
					
					$po_number=str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][7]);
					$countryCode=trim(strtoupper($excel->sheets[0]['cells'][$i][5]));//country
					$code=trim(strtoupper($excel->sheets[0]['cells'][$i][13]));
					$po_rec_date=$cdate;//$excel->sheets[0]['cells'][$i][8];
					$po_ship_date=$excel->sheets[0]['cells'][$i][9];
					$avg_rate=$excel->sheets[0]['cells'][$i][17];
					$po_remarks="";//str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][46]);
					
					$color_name=str_replace($str_rep,' ',trim($excel->sheets[0]['cells'][$i][8]));
					
					$size_name=trim($excel->sheets[0]['cells'][$i][23]);
					$country_po_qty=$excel->sheets[0]['cells'][$i][24];
					$exper=$excel->sheets[0]['cells'][$i][20];
				}
				
				if(($country_po_qty*1)>0)
				{
					//if($po_number=='XP18T5A') {echo $exper.'<br>';}
					//if($exper==0) $exper="";
					//if($exper=="") $planCutQty=$country_po_qty; else $planCutQty=($country_po_qty*($exper/100))+$country_po_qty;
					if($exper=="") $exper=0;
					$all_data=$style_description.'__'.$po_rec_date.'__'.change_date_format($po_ship_date).'__'.$avg_rate.'__'.$po_remarks.'__'.$code.'__'.$countryCode;
					$style_all_data_arr[$style_ref][$po_number][$color_name][$size_name][$all_data][$exper]+=$country_po_qty;
					$styleArr[$style_ref]=$style_ref;
				}
			}
		}
		$_SESSION['excel']=$style_all_data_arr;
		//$countStyle=count($styleArr);
		//print_r($style_all_data_arr);
		//die;
		?>
        <script>
			var permission='<? echo $permission; ?>';
			if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
			
			function location_select()
			{
				if($('#cbo_location_id option').length==2)
				{
					if($('#cbo_location_id option:first').val()==0)
					{
						$('#cbo_location_id').val($('#cbo_location_id option:last').val());
						//eval($('#cbo_location_id').attr('onchange'));
					}
				}
				else if($('#cbo_location_id option').length==1)
				{
					$('#cbo_location_id').val($('#cbo_location_id option:last').val());
					//eval($('#cbo_location_id').attr('onchange'));
				}
			}
			
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
				if($('#txt_ready_to_save').val()==3)
				{
					alert('Country Shipment date can not be Less than Current Insert Date.\n Please check and Upload the file again.');
					release_freezing();
					return;
				}
				
				if( form_validation('cbo_company_id*cbo_location_id*cbo_buyer_id*cbo_product_department*cbo_currercy_id*cbo_season_id*cbo_prod_catgory*cbo_team_leader*cbo_dealing_merchant*txt_masterStyle*cbo_order_uom*cbo_gmtsItem_id*tot_smv_qty*hid_buyer_format*cbo_gauge','Company*Location*Buyer*Prod. Dept.*Currency*Season*Prod. Catgory*Team Leader*Dealing Marchant*Master Style*Order Uom*Gmts Item*SMV*Buyer Format*Gauge')==false)
				{
					release_freezing();
					return;
				}
				
				var counter =$('#tblJobtag tbody tr').length; 
				var preJobData="";
				for(var t=1; t<=counter; t++)
				{
					preJobData += '&txtexcelStyle_' + t + '=' + trim($('#txtexcelStyle_'+t).val()) + '&txtprevJob_' + t + '=' + trim($('#txtprevJob_'+t).val()); 
				}
				
				var totStyleCount=($('#styPoSzInc_id').val()*1)-1;
				
				var dataString="";
				
				for(var i=1; i<=totStyleCount; i++)
				{
					var totPoCount=($('#poCount_'+i).val()*1);
					
					for(var j=1; j<=totPoCount; j++)
					{
						var postatus=trim($('#cboOrderStatus_'+i+'_'+j).val());
						if(postatus==0){
							alert('Order Status Cannot Be Null');
							release_freezing();
							return;
						}
						dataString += '&styleRef_' + i + '=' + trim($('#styleRef_'+i).text()) + '&poNo_'+i+'_'+j+'=' + trim($('#poNo_'+i+'_'+j).text()) + '&cboOrderStatus_'+i+'_'+j+'=' + trim($('#cboOrderStatus_'+i+'_'+j).val()); 
					}
				}
				//alert(poStatus+"joy"); return;
				
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_buyer_id*cbo_style_owner_id*cbo_product_department*cbo_currercy_id*cbo_season_id*cbo_season_year*cbo_brand_id*cbo_prod_catgory*cbo_team_leader*cbo_dealing_merchant*cbo_order_uom*cbo_gmtsItem_id*tot_smv_qty*cbo_packing*txt_job_no*hid_job_id*hid_ws_id*hid_buyer_format*txt_masterStyle*hid_inquiry_id*cbo_gauge*txt_sclc',"../../")+dataString+preJobData;
				//alert(data);
				
				http.open("POST","requires/buyer_excel_order_import_controller.php",true);
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
					//show_msg(trim(reponse[0]));
					release_freezing();
				}
			}
			
			function fnc_style_owner(val)
			{
				$('#cbo_style_owner_id').val(val);
			}
			
			function fnc_variable_settings_check()
			{
				var company_id=$('#cbo_company_id').val();
				var all_variable_settings=return_ajax_request_value(company_id, 'load_variable_settings', 'requires/buyer_excel_order_import_controller');
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
						var page_link="requires/buyer_excel_order_import_controller.php?action=open_smv_list&txt_style_ref="+txt_style_ref+"&set_smv_id="+set_smv_id+"&item_id="+item_id+"&cbo_company_name="+company_id+"&cbo_buyer_name="+cbo_buyer_name;
					}
					
					emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'SMV Pop Up', 'width=650px,height=220px,center=1,resize=1,scrolling=0','../')
					emailwindow.onclose=function()
					{
						var theform=this.contentDoc.forms[0];
						var selected_smv_data=this.contentDoc.getElementById("selected_smv").value;
						var smv_data=selected_smv_data.split("_");
						
						$("#tot_smv_qty").val(smv_data[0]);
						$("#hid_ws_id").val(smv_data[1]);
						
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
						//alert(pinc) 
						for(var j=pinc; j<=totPoCount; j++)
						{
							$('#cboOrderStatus_'+i+'_'+j).val(value);
							//alert(i+'_'+j+'_'+value)
						}
					}
				}
			}
			
			function openmypage_inquiry()
			{
				if( form_validation('cbo_company_id','Company Name')==false )
				{
					return;
				}
				freeze_window(0);
				var company = $("#cbo_company_id").val();
				var page_link='requires/buyer_excel_order_import_controller.php?action=inquiry_popup&company='+company;
				var title="Master Style Search Popup";
				emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=830px,height=400px,center=1,resize=0,scrolling=0','../')
				emailwindow.onclose=function()
				{
					var theform=this.contentDoc.forms[0];
		
					var mrrNumber=this.contentDoc.getElementById("hidden_issue_number").value; // mrr number
					mrrNumber = mrrNumber.split("_");
					//var mrrId=this.contentDoc.getElementById("issue_id").value; // mrr number
					if (mrrNumber!="")
					{
						$("#txt_masterStyle").val(mrrNumber[0]);
						$("#hid_inquiry_id").val(mrrNumber[1]);
						//style_refernce,id,buyer_id,season_buyer_wise,season_year,brand_id
						$("#cbo_buyer_id").val(mrrNumber[2]);
						load_drop_down( 'requires/buyer_excel_order_import_controller', mrrNumber[2], 'load_drop_down_season', 'season_td');
						$("#cbo_season_id").val(mrrNumber[3]);
						load_drop_down( 'requires/buyer_excel_order_import_controller', mrrNumber[2], 'load_drop_down_brand', 'brand_td');
						$("#cbo_season_year").val(mrrNumber[4]);
						$("#cbo_brand_id").val(mrrNumber[5]);
						$("#cbo_gmtsItem_id").val(mrrNumber[6]);
						$("#cbo_gauge").val(mrrNumber[7]);
						$("#cbo_product_department").val(mrrNumber[8]);
						
						$("#cbo_company_id").attr("disabled",true);
						$("#cbo_buyer_id").attr("disabled",true);
						$("#cbo_season_id").attr("disabled",true);
						$("#cbo_season_year").attr("disabled",true);
						$("#cbo_brand_id").attr("disabled",true);
						$("#cbo_gmtsItem_id").attr("disabled",true);
						$("#cbo_gauge").attr("disabled",true);
						
						//load_drop_down( 'requires/buyer_excel_order_import_controller', ".$sql_job[0][csf('team_leader')].", 'load_drop_down_dealing_merchant', 'div_marchant');
						release_freezing();
					}
				}
				release_freezing();
			}
			
			function fncjobBrowse_popup(srow)
			{
				if( form_validation('cbo_company_id','Company Name')==false )
				{
					return;
				}
				var company = $("#cbo_company_id").val();
				freeze_window(0);
				var page_link='requires/buyer_excel_order_import_controller.php?action=prevjob_popup&company='+company;
				var title="Previous Style Search Popup";
				emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=430px,center=1,resize=0,scrolling=0','../')
				
				emailwindow.onclose=function()
				{
					var theform=this.contentDoc.forms[0];
					var jobdata=this.contentDoc.getElementById("selected_job").value;
					jobdata = jobdata.split("_");
					if (jobdata!="")
					{
						$('#txtprevJob_'+srow).val( jobdata[0] );
						$('#txtprevStyle_'+srow).val( jobdata[1] );
						$("#cbo_company_id").attr("disabled",true);
						
						get_php_form_data( jobdata[0], 'populate_job_data_form','requires/buyer_excel_order_import_controller');
					}
				}
				release_freezing();
			}
			
	  </script>
      </head>
      <body onLoad="set_hotkey();">
      <div style="width:100%;" align="center">
      <!-- Important Field outside Form -->  
      <?=load_freeze_divs ("../../",$permission);  ?>
      <fieldset style="width:1450px;">
      <form name="excelImport_1" id="excelImport_1" autocomplete="off"> 
      <table width="1440" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >      
        <thead>
            <tr>
                <th width="100" class="must_entry_caption">Company</th>
                <th width="100" class="must_entry_caption">Location</th>
                <th width="100" class="must_entry_caption">Master Style</th>
                <th width="100" class="must_entry_caption">Buyer</th>
                <th width="100">Style Owner</th>
                <th width="80" class="must_entry_caption">Prod. Dept</th>
                <th width="60" class="must_entry_caption">Currency</th>
                <th width="60" class="must_entry_caption">Season</th>
                <th width="60">Season Year</th>
                <th width="60">Brand</th>
                <th width="70" class="must_entry_caption">Prod. Category</th>
                <th width="80" class="must_entry_caption">Team Leader</th>
                <th width="80" class="must_entry_caption">Dealing Merchant</th>
                
                <th width="50" class="must_entry_caption">Order Uom</th>
                <th width="100" class="must_entry_caption">Gmts. Item</th>
                <th width="50" class="must_entry_caption">SMV/ Pcs</th>
                <th width="50" class="must_entry_caption">Gauge</th>
                <th width="50">SC/LC</th>
                <th>Packing</th>
             </tr>
          </thead>
          <tbody>
            <tr class="general">
                <td><? echo create_drop_down( "cbo_company_id", 100, "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-Select Company-", $selected, "load_drop_down( 'requires/buyer_excel_order_import_controller', this.value, 'load_drop_down_location', 'location_td'); location_select(); load_drop_down( 'requires/buyer_excel_order_import_controller', this.value, 'load_drop_down_buyer', 'buyer_td'); fnc_style_owner(this.value); "); ?></td>
                <td id="location_td"><? echo create_drop_down( "cbo_location_id", 100, $blank_array,"", 1, "-Location-", $selected, ""); ?></td>
                <td><input style="width:90px;" type="text" title="Browse" onDblClick="openmypage_inquiry();" class="text_boxes" placeholder="Browse" name="txt_masterStyle" id="txt_masterStyle" readonly /></td>
                <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_id", 100, $blank_array,"", 1, "-Buyer-", $selected, ""); ?></td>
                <td id="owner_td"><? echo create_drop_down( "cbo_style_owner_id", 100, "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-Style Owner-", $selected, ""); ?></td>
                <td><? echo create_drop_down( "cbo_product_department", 80, $product_dept, "", 1, "-Select-", $selected, "", "", "" ); ?>
                <td><? echo create_drop_down( "cbo_currercy_id", 60, $currency,'', 0, "",2, "" ); ?></td>
                <td id="season_td"><? echo create_drop_down( "cbo_season_id", 60, $blank_array,'', 1, "-Season-",$selected, "" ); ?></td>
                <td><? echo create_drop_down( "cbo_season_year", 60, create_year_array(),"", 1,"-S.Year-", 1, "",0,"" ); ?></td>
                <td id="brand_td"><? echo create_drop_down( "cbo_brand_id", 60, $blank_array,'', 1, "-Brand-",$selected, "" ); ?></td>
                <td><? echo create_drop_down( "cbo_prod_catgory", 70, $product_category,"", 1, "-Category-", 1, "","","" ); ?></td>
                <td><? echo create_drop_down( "cbo_team_leader", 80, "select id, team_leader_name from lib_marketing_team where project_type=6 and status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "-Select Team-", $selected, "load_drop_down( 'requires/buyer_excel_order_import_controller', this.value, 'load_drop_down_dealing_merchant', 'div_marchant'); " ); ?></td>
                <td id="div_marchant"><? echo create_drop_down( "cbo_dealing_merchant", 80, $blank_array,"", 1, "-Team Member-", $selected, "" ); ?></td>
                
                <td><? echo create_drop_down( "cbo_order_uom",50, $unit_of_measurement, "",0, "", 1, "","","1" ); ?></td>
                <td><? echo create_drop_down( "cbo_gmtsItem_id", 100, $garments_item, 0, 1, "--Select Item--", $selected,"fnc_variable_settings_check();",0); ?></td>
                <td>
                    <input class="text_boxes_numeric" type="text" style="width:42px;" name="tot_smv_qty" id="tot_smv_qty" />
                    <input type="hidden" name="txt_job_no" id="txt_job_no" style="width:50px;" value="<?=$txt_job_no; ?>"/>
                    <input type="hidden" name="hid_job_id" id="hid_job_id" style="width:50px;" value=""/>
                    <input type="hidden" name="hid_inquiry_id" id="hid_inquiry_id" style="width:50px;" value=""/>
                    <input type="hidden" name="hid_ws_id" id="hid_ws_id" style="width:50px;" value=""/>
                    <input type="hidden" name="hid_buyer_format" id="hid_buyer_format" style="width:50px;" value="<?=$buyer_format; ?>"/>
                </td>
                <td><?=create_drop_down( "cbo_gauge", 50, $gauge_arr,"", 1, "-Gauge-", $selected, "" ); ?></td>
                <td><input style="width:40px;" type="text" class="text_boxes" name="txt_sclc" id="txt_sclc" placeholder="Write" /></td>
                <td><? echo create_drop_down( "cbo_packing", 80, $packing,"", 1, "--Select--", $selected, "","","" ); ?></td>
            </tr>
          </tbody>
       </table> 
      <br />
      <table width="600" cellspacing="0" border="1" class="rpt_table" rules="all" id="tblJobtag" >
      	<thead>
        	<th width="30">SL.</th>
            <th width="220">Style Ref.[EXCEL]</th>
            <th width="110">Previous Job</th>
            <th>Previous Style Ref.</th>
      	</thead>
        <tbody>
        	<? $stag=1;
			foreach($styleArr as $styleRef)
			{
				if ($stag%2==0)  $sbgcolor="#E9F3FF"; else $sbgcolor="#FFFFFF";
				?>
        	<tr bgcolor="<?=$sbgcolor; ?>">
            	<td width="30" align="center"><?=$stag; ?></td>
            	<td><input name="txtexcelStyle_<?=$stag; ?>" id="txtexcelStyle_<?=$stag; ?>" class="text_boxes" type="text" value="<?=$styleRef; ?>" readonly placeholder="Display" disabled style="width:210px;"/></td>
                <td><input name="txtprevJob_<?=$stag; ?>" id="txtprevJob_<?=$stag; ?>" class="text_boxes" type="text" onDblClick="fncjobBrowse_popup(<?=$stag; ?>);" readonly placeholder="Browse" style="width:100px;"/></td>
                <td><input name="txtprevStyle_<?=$stag; ?>" id="txtprevStyle_<?=$stag; ?>" class="text_boxes" type="text" readonly placeholder="Display" style="width:210px;"/></td>
            </tr>
            <? $stag++; 
			} ?>
        </tbody>
      </table>
      <br />   
		<table width="1380" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
			<thead>
            	<tr>
                	<th width="30" rowspan="2">SL.</th>
                    <th colspan="2" style="background:#CCFF99">Style Details</th>
                    <th colspan="5" style="background:#FFCCFF">Po Details</th>
                    <th colspan="10" style="background:#00FF00">Country, Color & Size Details</th>
                </tr>
                <tr>
                    <th width="100">Style Ref.</th>
                    <th width="100">Style Des.</th>
                    <th width="80">Order Status<input type="checkbox" name="copy_id" id="copy_id" onClick="copy_check(1);" value="2" ></th>
                    <th width="100">Po No.</th>
                    <th width="70">Po Receive Date</th>
                    <th width="70">Shipment Date</th>
                    <th width="80">Po Remarks</th>
                    
                    <? if($buyer_format==1) { ?>
                    <th width="80">Code</th>
                    <th width="80">Country Code</th>
                    <? } else if($buyer_format==2) { ?>
                    <th width="80">Channel/City</th>
                    <th width="80">Delivery Country</th>
                    <? } ?>
                    <th width="70">Country Ship Date</th>
                    <th width="100">Color Name</th>
                    <th width="60">Size Name</th>
                    <th width="70">Qty</th>
                    <th width="60">Rate</th>
                    <th width="80">Amount</th>
                    <th width="50">Ex-Cut %</th>
                    <th>Plan Qty.</th>
                </tr>
			</thead>
		</table>
		<div style="width:1380px; max-height:320px; overflow-y:scroll" id="scroll_body" > 
		<table width="1362" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="scanning_tbl"><!--table_body-->
        	<?php
			$country_arr=return_library_array("select id, country_name from lib_country order by country_name","id","country_name");
			$countryNameArr=return_library_array("select country_name, id from lib_country order by country_name","country_name","id");
			$code_arr=array();
			$code_sql=sql_select("select ultimate_country_code from lib_country_loc_mapping where status_active=1 and is_deleted=0 order by ultimate_country_code");
			foreach($code_sql as $row)
			{
				$code_arr[trim($row[csf('ultimate_country_code')])]=$row[csf('ultimate_country_code')];
			}
			unset($code_sql);
			$ready_to_save=1;
			$i=1; $sty=1; $st_name=""; $gPoQty=0; $gAmount=0; $gPlanQty=0;
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
							foreach($extra_data as $ex_val=>$experdata)
							{
								foreach($experdata as $exper=>$sizeqty)
								{	
									$ex_data=explode('__',$ex_val);
									
									$style_des=''; $po_receive_date=''; $po_shiment_date=''; $po_avg_rate=0; $po_remark=''; $country_qty=0; $country_amt=0;
									$style_des=$ex_data[0]; $po_receive_date=$ex_data[1]; $po_shiment_date=$ex_data[2]; $po_avg_rate=number_format($ex_data[3],2); $po_remark=$ex_data[4]; $country_qty=$sizeqty; $country_amt=number_format($sizeqty*$po_avg_rate,2,'.','');
									
									if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									$td_color_code=""; $td_color_countryCode="";
									if($buyer_format==1)
									{
										if( $code_arr[trim($ex_data[6])]=="" ) { $td_color_code="red"; $ready_to_save=0; }
										if(trim($ex_data[5])==""){  $ex_data[5]= $ex_data[6]; $td_color_code="red"; }
									}
									else if($buyer_format==2)
									{
										if( $countryNameArr[trim($ex_data[6])]=="" ) { $td_color_code="red"; $ready_to_save=0; }
										if(trim($ex_data[5])==""){   $td_color_code="red"; }
									}
									if($exper==0) $exper="";
									if($exper=="") $planCutQty=$country_qty; else $planCutQty=($country_qty*($exper/100))+$country_qty;
									if(strtotime($po_receive_date)>=strtotime($po_shiment_date)) { $bgcolor="Red"; $ready_to_save=2; }
									if(strtotime(date())>=strtotime($po_shiment_date)) { $bgcolor="Red"; $ready_to_save=3; }

									?>
									<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_1nd<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_1nd<?=$i; ?>">
										<td width="30" align="center"><?=$i; ?></td>
										<?php if($st==1) { ?>
										<td width="100" id="styleRef_<?=$sty; ?>"><?=$style_name; ?></td>
										<td width="100" id="styleDes_<?=$sty; ?>"><?=$style_des; ?></td>
										<?php $st++; } else { ?>
										<td width="100">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<?php } if($p==1) { $auto_id=''; $auto_id=$sty.'_'.$pn; ?>
										<td width="80"><? echo create_drop_down( "cboOrderStatus_$auto_id", 75, $order_status, "", 1, "Select Status", $selected,"fnc_statuscopy('".$auto_id."',this.value);", "" ); ?></td>
                                       
										<td width="100" id="poNo_<?=$auto_id; ?>" ><?=$order_no; ?></td>
										<td width="70" id="recDate_<?=$auto_id; ?>"><?=$po_receive_date; ?></td>
										<td width="70" id="shipDate_<?=$auto_id; ?>"><?=$po_shiment_date; ?></td>
										<td width="80"><input name="txtpoRemarks_<?=$auto_id; ?>" id="txtpoRemarks_<?=$auto_id; ?>" class="text_boxes" type="text" value="<?=$po_remark; ?>" style="width:66px;"/></td>
										
										<?php $p++; } else { ?>
										<td width="80">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="70">&nbsp;</td>
										<td width="70">&nbsp;</td>
										<td width="80">&nbsp;</td>
										<?php } $ciid=''; $ciid=$sty.'_'.$pn.'_'.$ctpn; ?>
										<td width="80" id="code_<?=$ciid; ?>" bgcolor="<?=$td_color_code; ?>"><?=trim($ex_data[5]); ?></td>
										<td width="80" id="countryCode_<?=$ciid; ?>" bgcolor="<?=$td_color_countryCode; ?>"><?=trim($ex_data[6]); ?>&nbsp;</td>
										
										<td width="70" id="countryShipDate_<?=$ciid; ?>"><?=change_date_format($po_shiment_date); ?></td>
										<td width="100" id="colorName_<?=$ciid; ?>"><?=$color_val; ?></td>
										<td width="60" id="sizeName_<?=$ciid; ?>" align="center"><?=$size_val; ?></td>
										<td width="70" id="countryQty_<?=$ciid; ?>" align="right"><?=$country_qty; ?></td>
										<td width="60" id="countryRate_<?=$ciid; ?>" align="right"><?=$po_avg_rate; ?></td>
										<td width="80" id="countryAmt_<?=$ciid; ?>" align="right"><?=$country_amt; ?></td>
                                        <td width="50" id="exper_<?=$ciid; ?>" align="right"><?=$exper; ?></td>
										<td id="planqty_<?=$ciid; ?>" align="right"><?=number_format($planCutQty,0,'.',''); ?></td>
									</tr>
									<?php
									$i++; $ctpn++;
									$gPoQty+=$country_qty;
									$gAmount+=$country_amt;
									$gPlanQty+=$planCutQty;
								}
							}
						}
					}
					?><input type="hidden" class="text_boxes" id="couCount_<?=$sty.'_'.$pn; ?>" name="couCount_<?=$sty.'_'.$pn; ?>" style="width:30" value="<?=$ctpn-1; ?>"><?php 
					$pn++;
				}
				 ?><input type="hidden" class="text_boxes" id="poCount_<?=$sty; ?>" name="poCount_<?=$sty; ?>" style="width:30" value="<?=$pn-1; ?>"><?php 
				$sty++;
			}
			//echo $st_name;
			?>
            <input type="hidden" class="text_boxes" id="txt_ready_to_save" name="txt_ready_to_save" style="width:80" value="<?=$ready_to_save; ?>">
            <input type="hidden" class="text_boxes" id="styPoSzInc_id" name="styPoSzInc_id" style="width:80" value="<?=$sty; ?>">
        </table>
        </div>
        <table width="1380" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
			<tfoot>
                <tr>
                	<th width="30">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="70" align="right"><?=$gPoQty; ?></th>
                    <th width="60">&nbsp;</th>
                    <th width="80" align="right"><?=$gAmount; ?></th>
                    <th width="50">&nbsp;</th>
                    <th align="right"><?=$gPlanQty; ?></th>
                </tr>
			</tfoot>
		</table>
        </form>
        </fieldset>
		<div>
            <table>
            	<tr style="border:none">
                    <td align="center" colspan="17" class="button_container">
                         <input type="button" id="show_button" align="middle" class="formbutton" style="width:100px" value="Save" onClick="fnc_excel_import(0);" />
                    </td>
                </tr>
           </table>
      	</div>
        </div>
	</body>
    <script> 
		if ($('#txt_job_no').val()!="") { get_php_form_data( $('#txt_job_no').val(), 'populate_job_data_form','requires/buyer_excel_order_import_controller'); }
		else $('#cbo_company_id').val(0);
    
    </script>
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
