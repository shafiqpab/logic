<?
session_start();
include('../../includes/common.php');
 
$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//------------------------------------------------------------------------------------------------------
if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 170, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/actual_production_resource_entry_controller', this.value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_floor', 'floor_td' );","","","","","",3 );     	 
	exit();
}

if ($action=="load_drop_down_floor")
{
	$data=explode('_',$data);
	$loca=$data[0];
	$com=$data[1];
	echo create_drop_down( "cbo_floor", 170, "select id,floor_name from lib_prod_floor where production_process=5 and status_active =1 and is_deleted=0 and company_id='$com' and location_id='$loca' order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "","","","","","",4 );     	 
	exit();
}

if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../", 1, 1,'','','',1);
	extract($_REQUEST);
	$sql_result=sql_select("select sewing_production from variable_settings_production where company_name=$company_name and variable_list=1 and status_active=1");
	$prod_update_var=$sql_result[0][csf('sewing_production')];
	
	
	?>
	<script>
		var permission='<? echo $permission; ?>';
		function set_all()
		{
			return;
			var old=document.getElementById('txt_select_row_id').value; 
			if(old!="")
			{   
				old=old.split(",");
				for(var k=0; k<old.length; k++)
				{   
					js_set_value( old[k] );
				} 
			}
		}
		
	
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				if($('#tr_'+i).css('display')!='none')
				{
					$('#tr_'+i).trigger('click'); 
				}
			}
		}
		
		function toggle( x, origColor ) {
			
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str, val) {
			
			var string=$('#txt_individual_str' + str).val();

			if(val==1)
			{
				alert("Sorry. Production found.");
				return;
			}
			
			toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
			if($('#tr_selection'+str).is(":checked"))
			{
				$('#tr_selection'+str).attr('checked', false);
				$("#txt_target"+str).val("");
			}
			else
			{
				$('#tr_selection'+str).attr('checked', true);
			}
		}		
				
    </script>
    </head>
    <body onLoad="set_hotkey()">
    
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:870px;">
        	<div id="msg_box_popp" style=" height:15px; width:400px;  position:relative; left:50px "></div>
        	<? echo load_freeze_divs ("../../../",$permission);  ?>
            <table width="790" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Job Year</th>
                    <th>Search By</th>
                    <th id="search_by_td_up">Please Enter Job No</th>
                    <th colspan="2">Shipment Date</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" name="hide_style_no" id="hide_style_no" value="" />
                    <input type="hidden" name="hide_sys_id" id="hide_sys_id" value="<? echo $sid; ?>" />
                    <input type="hidden" name="hide_po_id" id="hide_po_id" value="" />
                    <input type="hidden" name="hide_all_data" id="hide_all_data" value="" />
                    <input type="hidden" name="hide_target_hour" id="hide_target_hour" value="<? echo $target_hour; ?>" />
                    <input type="hidden" name="hide_prod_update_var" id="hide_prod_update_var" value="<? echo $prod_update_var; ?>" />
                </thead>
                <tbody>
                	<tr class="general">
                        <td>
                        	 <? echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company_name $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>
                        <td><? echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?></td>  
                        <td>	
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref",3=>"Order");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '') ";							
							echo create_drop_down( "cbo_search_by", 80, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" >
                        </td> 
                        <td>
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date"  >
                        </td>	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company_name; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_year').value+'**'+document.getElementById('hide_prod_update_var').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+'<? echo $sid; ?>'+'**'+'<? echo $from_date; ?>'+'**'+'<? echo $to_date; ?>'+'**'+'<? echo $mst_dtls_id; ?>', 'create_job_no_search_list_view', 'search_div', 'actual_production_resource_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1)'); set_all();" style="width:100px;" />
                    </td>
                    </tr>
                    <tr>
                    	<td colspan="7" align="center" >
							<? echo load_month_buttons(1); ?>
                        </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div">
            <?php	
                
            $sql_colSiz="select dtls_id, po_id, gmts_item_id, color_id, size_id, target_per_line as target,country_id,helper,operator from  prod_resource_color_size where  po_id>0 and dtls_id=$mst_dtls_id and status_active=1 and is_deleted=0";
			// echo $sql_colSiz;die;
	
			$sql_colSiz_res=sql_select($sql_colSiz);

			$tot_rows=0; $poIds='';
			foreach($sql_colSiz_res as $row)
			{
				//echo $row[csf('target')]."ss";
				$real_string=$row[csf('po_id')]."_".$row[csf('gmts_item_id')]."_".$row[csf('country_id')]."_".$row[csf('color_id')]."_".$row[csf('size_id')];
				$style_real_arr[$real_string]=$row[csf('target')];
				$style_real_array[$real_string]['helper']=$row[csf('helper')];
				$style_real_array[$real_string]['operator']=$row[csf('operator')];
				$po_id_all[$row[csf("po_id")]]=$row[csf("po_id")];
			}
		if(count($po_id_all)>0)
		{

			$sql_check=sql_select("select c.po_break_down_id, c.item_number_id, c.country_id, c.size_number_id, c.color_number_id from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c where c.po_break_down_id in (".implode(",",$po_id_all).") and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.sewing_line='$line' and a.production_type in (4) and b.color_size_break_down_id=c.id and b.production_qnty!=0  group by c.po_break_down_id, c.item_number_id, c.country_id, c.size_number_id, c.color_number_id");
		
			$prod_arr=array();
			foreach($sql_check as $prow)
			{
				$prod_arr[$prow[csf('po_break_down_id')]][$prow[csf('item_number_id')]][$prow[csf('country_id')]][$prow[csf('color_number_id')]][$prow[csf('size_number_id')]]=1;
			}
			unset($sql_check);
			
			$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
			$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name");
			$size_arr=return_library_array( "select id,size_name from lib_size", "id", "size_name");
			$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
			
			if($db_type==0)
			{
				 $year_field="YEAR(a.insert_date)"; 
				if($year!=0) $year_cond=" and year(a.insert_date)=$year"; else $year_cond="";	
			}
			else if($db_type==2)
			{
				$year_field="to_char(a.insert_date,'YYYY')";
				if($year!=0) $year_cond="and to_char(a.insert_date,'YYYY')=$year"; else $year_cond="";
			}
			if($prod_update_var==1)//gross level
			{
				$sql_update="select a.job_no_prefix_num, a.job_no, $year_field as year, a.buyer_name, a.style_ref_no, b.id, b.po_number, b.pub_shipment_date, c.item_number_id, c.country_id,  sum(c.order_quantity) as po_qty from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and a.company_name='$company_name' and b.id in (".implode(",",$po_id_all).") group by a.job_no_prefix_num, a.job_no, a.insert_date, a.buyer_name, a.style_ref_no, b.id, b.po_number, b.pub_shipment_date, c.item_number_id, c.country_id order by id Desc";
				
			}	
			else if($prod_update_var==2)//Color Wise
			{
		
					$sql_update="select a.job_no_prefix_num, a.job_no, $year_field as year, a.buyer_name, a.style_ref_no, b.id, b.po_number, b.pub_shipment_date, c.item_number_id, c.color_number_id, c.country_id, sum(c.order_quantity) as po_qty from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and a.company_name='$company_name' and b.id in (".implode(",",$po_id_all).") group by a.job_no_prefix_num, a.job_no, a.insert_date, a.buyer_name, a.style_ref_no, b.id, b.po_number, b.pub_shipment_date, c.item_number_id, c.color_number_id, c.country_id  order by id Desc";
					
			}
			else if($prod_update_var==3)//Size Wise
			{
				
				$sql_update="select a.job_no_prefix_num, a.job_no, $year_field as year, a.buyer_name, a.style_ref_no, b.id, b.po_number, b.pub_shipment_date, c.item_number_id, c.color_number_id, c.country_id, c.size_number_id, sum(c.order_quantity) as po_qty from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and a.company_name='$company_name'  and b.id in (".implode(",",$po_id_all).")  group by a.job_no_prefix_num, a.job_no, a.insert_date, a.buyer_name, a.style_ref_no, b.id, b.po_number, b.pub_shipment_date, c.item_number_id, c.color_number_id, c.country_id, c.size_number_id  order by id Desc";
				
			}
			
			?>
                
            
            <div style="width:1220px;">
                <table cellspacing="0" width="1220" border="1" rules="all" class="rpt_table">
                    <thead>
                        <tr>
                            <th width="30">SL</th>
                            <th width="50">Job</th>
                            <th width="50">Year</th>
                            <th width="100">Style</th>
                            <th width="100">Buyer</th>
                            <th width="100">PO No</th>
                            <th width="70">Ship Date</th>
                            <th width="100">Gmts. Item</th>
                            <th width="100">Country</th>
                            <th width="80">Color</th>
                            <th width="80">Size</th>
                            <th width="80">Po Qty</th>
                            <th width="80">Target/Line/Hr</th>
                            <th width="80">Oprtr/Line/Sty</th>
                            <th width="">Helper/Line/Sty</th>
                        </tr>
                    </thead>
                </table>  
            </div>         
            <div style="width:1220px; max-height:260px; overflow-y:auto;">
                <table cellspacing="0" width="1202"  border="1" rules="all" class="rpt_table" id="tbl_list_search" >
                    <tbody>  
                    <?
                   // echo $sql_update;
                    
                    $sql_update_data=sql_select($sql_update);
                  //  $sql_data=sql_select($sql);
                    $sl=1; $style_temp_id=''; $list_style_arr=array(); $new_po_id_arr=array(); $new_style_arr=array();
                    foreach($sql_update_data as $row)
                    {
                        $bgcolor=($sl%2==0)?"#E9F3FF":"#FFFFFF";
                        $job_no='';
                        $ex_job=explode('-',$row[csf('job_no')]);
                        $job_no=$ex_job[1].'-'.$ex_job[2].'*'.$row[csf('style_ref_no')];
                        $is_prod=0;
                        $is_prod=$prod_arr[$row[csf('id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]];
                        //10_29390_42_21_193_6,11_29389_42_16_43_1
                        
                        $row_select='';
                        $row_select=$row[csf('id')].'_'.$row[csf('item_number_id')].'_'.$row[csf('country_id')].'_'.$row[csf('color_number_id')].'_'.$row[csf('size_number_id')];
                        
                        $str=$row[csf('id')].'_'.$row[csf('item_number_id')].'_'.$row[csf('country_id')].'_'.$row[csf('color_number_id')].'_'.$row[csf('size_number_id')];
                        if($check_row_arr[$str]=="")
                        {
                            $tr_checked="";
                            if(array_key_exists($str,$style_real_arr)) 
                            { 
                                if($style_temp_id=="") $style_temp_id=$sl; else $style_temp_id.=",".$sl;
                                $list_style_arr[]=$str;
                                $bgcolor="yellow";
                                $tr_checked="checked";
                            }
                            
                            $new_po_id_arr[]=$row[csf('id')];
                            $check_row_arr[$str]=$str;
                            ?>    
                            <tr  id="tr_<? echo $sl; ?>" data-sl="<? echo $sl; ?>" data-prod-id="<? echo $is_prod; ?>" style="cursor:pointer;background-color:<? echo $bgcolor ; ?>">
                                <td width="30"><? echo $sl; ?>
                                <input type="hidden" name="txt_individual_str" id="txt_individual_str<?php echo $sl ?>" value="<? echo $row_select; ?>"/>
                                <input type="checkbox" id="tr_selection<?php echo $sl ?>" name="tr_selection[]"  <?php echo $tr_checked; ?> style="display:none"/> <!--style="display:none"-->
                                </td>
                                <td width="50"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                                <td width="50"><? echo $row[csf('year')]; ?></td>
                                <td width="100"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                                <td width="100"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></p></td>
                                <td width="100"><p><? echo $row[csf('po_number')]; ?></p></td>
                                <td width="70"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
                                <td width="100"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
                                <td width="100"><p><? echo $country_arr[$row[csf('country_id')]]; ?></p></td>
                                <td width="80"><p><? echo $color_arr[$row[csf('color_number_id')]]; ?></p></td>
                                <td width="80"><p><? echo $size_arr[$row[csf('size_number_id')]]; ?>&nbsp;</p></td>
                                <td align="right" width="80"><? echo $row[csf('po_qty')]; ?></td>
                                <td align="right" class="not_clickable" width="80"><input type="text" class="text_boxes"  style=" width:60px;"id="txt_target<?php echo $sl ?>" onKeyUp="calculate_target_hour(<?php echo $sl ?>)" name="txt_target[]" value="<?php  echo $style_real_arr[$str]; ?>" /></td>
                                <td align="right" class="not_clickable" width="80"><input type="text" class="text_boxes"  style=" width:60px;"id="txt_operator<?php echo $sl ?>" name="txt_operator[]" value="<?php  echo $style_real_array[$str]['operator']; ?>" /></td>
                                <td align="right" class="not_clickable"><input type="text" class="text_boxes"  style=" width:60px;"id="txt_helper<?php echo $sl ?>" name="txt_helper[]" value="<?php  echo $style_real_array[$str]['helper']; ?>" /></td>
                            </tr>
                            <? 
                            $sl++; 
                    
                        } 
                    }
                   
                    
                    
                    ?>
             
                    <tbody>
                </table>
            </div> 
            <br/>
            <br/>
            <table width="1060" cellspacing="0" cellpadding="0" style="border:none" align="center">
                    <tr>
                        <td align="center" height="30" valign="bottom" >
                            <div style="width:100%"> 
                                <div style="width:50%;" align="center">
                                    <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                                    <? 
                                        if(count($po_id_all)>0)
                                        {
                                            echo load_submit_buttons( $permission, "fnc_Ac_Production_Resource_color", 1,0,"",2);
                                        }
                                        else
                                        {
                                            echo load_submit_buttons( $permission, "fnc_Ac_Production_Resource_color", 0,0,"",2);
                                        }
                                    ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            <?php
			
			}
			
			?>
            
            </div>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>
	
	 
    	$(document).ready(function(){
			$(document).on("click","#tbl_list_search tr td:not(.not_clickable)",function(){
				var sl = $(this).parent("tr").attr("data-sl");
				var data = $(this).parent("tr").attr("data-prod-id");
				//alert(sl);
				js_set_value(sl,data);
			});
		});	
		
		function calculate_target_hour(id)
		{
			if($("#tr_selection"+id).is(":checked"))
			{
				var total_target=0;
				var line_target=$("#hide_target_hour").val()*1;
				$("#tbl_list_search tbody tr:not(.fltrow)").each(function() {
					
					if($(this).find('input[name="tr_selection[]"]').is(":checked"))
					{
						total_target=total_target+($(this).find('input[name="txt_target[]"]').val())*1;
					}
				});
					
				if(total_target>line_target)
				{
					$("#txt_target"+id).val("");
				}
			}
			else
			{
				$("#txt_target"+id).val("");
				return;
			}
			
		}
		
		
		function fnc_Ac_Production_Resource_color(operation)
		{ 
			var j=0; var dataString=''; var all_barcodes='';
			$("#tbl_list_search").find('tbody tr').each(function()
			{
				if($(this).find('input[name="tr_selection[]"]').is(":checked"))
				{
					var po_data=$(this).find('input[name="txt_individual_str"]').val();
					var target=$(this).find('input[name="txt_target[]"]').val();
					var operator=$(this).find('input[name="txt_operator[]"]').val();
					var helper=$(this).find('input[name="txt_helper[]"]').val();
					j++;
					dataString+='&po_data_' + j + '=' + po_data + '&target_' + j + '=' + target+ '&operator_' + j + '=' + operator+ '&helper_' + j + '=' + helper;
				}
			});
		
			if(j<1)
			{
				alert('No data');
				return;
			}
			
			var mst_dtls_id=<? echo $mst_dtls_id; ?>;
			var system_id=<? echo $sid; ?>;
			
			var data="action=save_update_delete_color_size&operation="+operation+dataString+"&mst_dtls_id="+mst_dtls_id+"&system_id="+system_id+"&tot_row="+j;
			//alert(data)
			freeze_window(operation);
			http.open("POST","actual_production_resource_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_Resource_color_reponse;
		}

		function fnc_Resource_color_reponse()
		{
			if(http.readyState == 4) 
			{ 
		 //alert(http.responseText)
				var response=trim(http.responseText).split('**');
				
				 if(response[0]==0)
				 {
					 $('#msg_box_popp').fadeTo(100,1,function() //start fading the messagebox
					 {
						$('#msg_box_popp').html("Data Save Sussessfully").removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
					 });
				 }
				 else if(response[0]==1)
				 {
					 $('#msg_box_popp').fadeTo(100,1,function() //start fading the messagebox
					 {
						$('#msg_box_popp').html("Data Update Sussessfully").removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
					 });
				 }
				if(response[0]==0 || response[0]==1)
				{	
					//set_button_status(1, permission, 'fnc_Ac_Production_Resource_color',1);
					set_button_status(1, permission, 'fnc_Ac_Production_Resource_color',2);
				}
				release_freezing();
			}
		}
		
		
    </script>
    </html>
    <?
	exit(); 
}

if($action=='create_job_no_search_list_view')
{
	$exdata=explode('**',$data);
	$comp_id=$exdata[0];
	$buyer_id=$exdata[1];
	$year=$exdata[2];
	$prod_update_var=$exdata[3];
	$search_by=$exdata[4];
	$search_common=$exdata[5];
	$form_date=$exdata[6];
	$to_date=$exdata[7];
	$line=$exdata[8];
	
	$reform_date=$exdata[9];
	$reto_date=$exdata[10];
	$mst_details_id=$exdata[11];
	
	$sql_colSiz="select dtls_id, po_id, gmts_item_id, color_id, size_id, target_per_line as target,country_id,helper,operator from  prod_resource_color_size where  po_id>0 and dtls_id=$mst_details_id and status_active=1 and is_deleted=0";
	//echo $sql_colSiz;die;
	
	$sql_colSiz_res=sql_select($sql_colSiz);

	$tot_rows=0; $poIds='';
	$style_real_array = array();
	foreach($sql_colSiz_res as $row)
	{
		//echo $row[csf('target')]."ss";
		$real_string=$row[csf('po_id')]."_".$row[csf('gmts_item_id')]."_".$row[csf('country_id')]."_".$row[csf('color_id')]."_".$row[csf('size_id')];
		$style_real_arr[$real_string]=$row[csf('target')];
		$style_real_array[$real_string]['operator']=$row[csf('operator')];
		$style_real_array[$real_string]['helper']=$row[csf('helper')];
		$po_id_all[$row[csf("po_id")]]=$row[csf("po_id")];
	}
	//	echo "<pre>";
	//print_r($style_real_arr);die;
	if($db_type==0)
	{ 
		if ($reform_date!="" && $reto_date!="") $reDate_cond = "and a.production_date between '".change_date_format($reform_date,'yyyy-mm-dd')."' and '".change_date_format($reto_date,'yyyy-mm-dd')."'"; else $reDate_cond ="";
	}
	else
	{
		if ($reform_date!="" && $reto_date!="") $reDate_cond = "and a.production_date between '".change_date_format($reform_date, "", "",1)."' and '".change_date_format($reto_date, "", "",1)."'"; else $reDate_cond ="";
	}

	
	$sql_check=sql_select("select c.po_break_down_id, c.item_number_id, c.country_id, c.size_number_id, c.color_number_id from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.sewing_line='$line' and a.production_type in (4) and b.color_size_break_down_id=c.id and b.production_qnty!=0 $reDate_cond group by c.po_break_down_id, c.item_number_id, c.country_id, c.size_number_id, c.color_number_id");

	$prod_arr=array();
	foreach($sql_check as $prow)
	{
		$prod_arr[$prow[csf('po_break_down_id')]][$prow[csf('item_number_id')]][$prow[csf('country_id')]][$prow[csf('color_number_id')]][$prow[csf('size_number_id')]]=1;
	}
	unset($sql_check);
	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name");
	$size_arr=return_library_array( "select id,size_name from lib_size", "id", "size_name");
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	if($exdata[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$buyer_id";
	}
	if($db_type==0)
	{
		 $year_field="YEAR(a.insert_date)"; 
		if($year!=0) $year_cond=" and year(a.insert_date)=$year"; else $year_cond="";	
	}
	else if($db_type==2)
	{
		$year_field="to_char(a.insert_date,'YYYY')";
		if($year!=0) $year_cond="and to_char(a.insert_date,'YYYY')=$year"; else $year_cond="";
	}
	if ($search_by==1) 
	{
		if($search_common=="") $src_cond=""; else $src_cond=" and a.job_no_prefix_num='$search_common'";
	}
	else if ($search_by==2) 
	{
		if($search_common=="") $src_cond=""; else $src_cond=" and a.style_ref_no Like '%$search_common%'";
	}
	else if ($search_by==3) 
	{
		if($search_common=="") $src_cond=""; else $src_cond=" and b.po_number Like '%$search_common%'";
	}
	
	if($db_type==0)
	{ 
		if ($form_date!="" && $to_date!="") $ship_date_cond = "and b.pub_shipment_date between '".change_date_format($form_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'"; else $ship_date_cond ="";
	}
	else
	{
		if ($form_date!="" && $to_date!="") $ship_date_cond = "and b.pub_shipment_date between '".change_date_format($form_date, "", "",1)."' and '".change_date_format($to_date, "", "",1)."'"; else $ship_date_cond ="";
	}
	
	if($prod_update_var==1)//gross level
	{
		$sql="select a.job_no_prefix_num, a.job_no, $year_field as year, a.buyer_name, a.style_ref_no, b.id, b.po_number, b.pub_shipment_date, c.item_number_id, c.country_id,  sum(c.order_quantity) as po_qty from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and a.company_name='$comp_id' $buyer_id_cond $src_cond $year_cond $ship_date_cond group by a.job_no_prefix_num, a.job_no, a.insert_date, a.buyer_name, a.style_ref_no, b.id, b.po_number, b.pub_shipment_date, c.item_number_id, c.country_id order by id Desc";
		 
		if(count($po_id_all)>0)
		{
			$sql_update="select a.job_no_prefix_num, a.job_no, $year_field as year, a.buyer_name, a.style_ref_no, b.id, b.po_number, b.pub_shipment_date, c.item_number_id, c.country_id,  sum(c.order_quantity) as po_qty from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and a.company_name='$comp_id' and b.id in (".implode(",",$po_id_all).") group by a.job_no_prefix_num, a.job_no, a.insert_date, a.buyer_name, a.style_ref_no, b.id, b.po_number, b.pub_shipment_date, c.item_number_id, c.country_id order by id Desc";
			
		}
	}	
	else if($prod_update_var==2)//Color Wise
	{
		$sql="select a.job_no_prefix_num, a.job_no, $year_field as year, a.buyer_name, a.style_ref_no, b.id, b.po_number, b.pub_shipment_date, c.item_number_id, c.color_number_id, c.country_id, sum(c.order_quantity) as po_qty from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and a.company_name='$comp_id' $buyer_id_cond $src_cond $year_cond $ship_date_cond group by a.job_no_prefix_num, a.job_no, a.insert_date, a.buyer_name, a.style_ref_no, b.id, b.po_number, b.pub_shipment_date, c.item_number_id, c.color_number_id, c.country_id order by id Desc";
		if(count($po_id_all)>0)
		{
			$sql_update="select a.job_no_prefix_num, a.job_no, $year_field as year, a.buyer_name, a.style_ref_no, b.id, b.po_number, b.pub_shipment_date, c.item_number_id, c.color_number_id, c.country_id, sum(c.order_quantity) as po_qty from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and a.company_name='$comp_id' and b.id in (".implode(",",$po_id_all).") group by a.job_no_prefix_num, a.job_no, a.insert_date, a.buyer_name, a.style_ref_no, b.id, b.po_number, b.pub_shipment_date, c.item_number_id, c.color_number_id, c.country_id  order by id Desc";
			
		}
	}
	else if($prod_update_var==3)//Size Wise
	{
		$sql="select a.job_no_prefix_num, a.job_no, $year_field as year, a.buyer_name, a.style_ref_no, b.id, b.po_number, b.pub_shipment_date, c.item_number_id, c.color_number_id, c.country_id, c.size_number_id, sum(c.order_quantity) as po_qty from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and a.company_name='$comp_id' $buyer_id_cond $src_cond $year_cond $ship_date_cond group by a.job_no_prefix_num, a.job_no, a.insert_date, a.buyer_name, a.style_ref_no, b.id, b.po_number, b.pub_shipment_date, c.item_number_id, c.color_number_id, c.country_id, c.size_number_id order by id Desc";
		if(count($po_id_all)>0)
		{
			$sql_update="select a.job_no_prefix_num, a.job_no, $year_field as year, a.buyer_name, a.style_ref_no, b.id, b.po_number, b.pub_shipment_date, c.item_number_id, c.color_number_id, c.country_id, c.size_number_id, sum(c.order_quantity) as po_qty from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and a.company_name='$comp_id'  and b.id in (".implode(",",$po_id_all).")  group by a.job_no_prefix_num, a.job_no, a.insert_date, a.buyer_name, a.style_ref_no, b.id, b.po_number, b.pub_shipment_date, c.item_number_id, c.color_number_id, c.country_id, c.size_number_id  order by id Desc";
			
		}
		
	}
	//echo $sql_update;die;
	?>	
	<form>
        <div style="width:1220px;">
            <table cellspacing="0" width="1220" border="1" rules="all" class="rpt_table">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="50">Job</th>
                        <th width="50">Year</th>
                        <th width="100">Style</th>
                        <th width="100">Buyer</th>
                        <th width="100">PO No</th>
                        <th width="70">Ship Date</th>
                        <th width="100">Gmts. Item</th>
                        <th width="100">Country</th>
                        <th width="80">Color</th>
                        <th width="80">Size</th>
                        <th width="80">Po Qty</th>
                        <th width="80">Target/Line/Hr</th>
                        <th width="80" title="Operator/Line/Sty">Oprtr/Line/Sty</th>
                        <th width="">Helper/Line/Sty</th>
                    </tr>
                </thead>
            </table>  
        </div>         
        <div style="width:1220px; max-height:260px; overflow-y:auto;">
            <table cellspacing="0" width="1202"  border="1" rules="all" class="rpt_table" id="tbl_list_search" >
                <tbody>  
				<?
                //echo $sql;
				$sql_data=sql_select($sql);
				$sl=1; $style_temp_id=''; $list_style_arr=array(); $new_po_id_arr=array(); $new_style_arr=array();
				if($sql_update!="")
				{
					$sql_update_data=sql_select($sql_update);
					foreach($sql_update_data as $row)
					{
						$bgcolor=($sl%2==0)?"#E9F3FF":"#FFFFFF";
						$job_no='';
						$ex_job=explode('-',$row[csf('job_no')]);
						$job_no=$ex_job[1].'-'.$ex_job[2].'*'.$row[csf('style_ref_no')];
						$is_prod=0;
						$is_prod=$prod_arr[$row[csf('id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]];
						//10_29390_42_21_193_6,11_29389_42_16_43_1
						
						$row_select='';
						$row_select=$row[csf('id')].'_'.$row[csf('item_number_id')].'_'.$row[csf('country_id')].'_'.$row[csf('color_number_id')].'_'.$row[csf('size_number_id')];
						
						$str=$row[csf('id')].'_'.$row[csf('item_number_id')].'_'.$row[csf('country_id')].'_'.$row[csf('color_number_id')].'_'.$row[csf('size_number_id')];
						if($check_row_arr[$str]=="")
						{
							$tr_checked="";
							if(array_key_exists($str,$style_real_arr)) 
							{ 
								if($style_temp_id=="") $style_temp_id=$sl; else $style_temp_id.=",".$sl;
								$list_style_arr[]=$str;
								$bgcolor="yellow";
								$tr_checked="checked";
							}
							
							$new_po_id_arr[]=$row[csf('id')];
							$check_row_arr[$str]=$str;
							?>    
							<tr  id="tr_<? echo $sl; ?>" data-sl="<? echo $sl; ?>" data-prod-id="<? echo $is_prod; ?>" style="cursor:pointer;background-color:<? echo $bgcolor ; ?>">
								<td width="30"><? echo $sl; ?>
								<input type="hidden" name="txt_individual_str" id="txt_individual_str<?php echo $sl ?>" value="<? echo $row_select; ?>"/>
								<input type="checkbox" id="tr_selection<?php echo $sl ?>" name="tr_selection[]"  <?php echo $tr_checked; ?> style="display:none"/> <!--style="display:none"-->
								</td>
								<td width="50"><? echo $row[csf('job_no_prefix_num')]; ?></td>
								<td width="50"><? echo $row[csf('year')]; ?></td>
								<td width="100"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
								<td width="100"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></p></td>
								<td width="100"><p><? echo $row[csf('po_number')]; ?></p></td>
								<td width="70"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
								<td width="100"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
								<td width="100"><p><? echo $country_arr[$row[csf('country_id')]]; ?></p></td>
								<td width="80"><p><? echo $color_arr[$row[csf('color_number_id')]]; ?></p></td>
								<td width="80"><p><? echo $size_arr[$row[csf('size_number_id')]]; ?>&nbsp;</p></td>
								<td align="right" width="80"><? echo $row[csf('po_qty')]; ?></td>
								<td align="right" class="not_clickable" width="80"><input type="text" class="text_boxes"  style=" width:60px;"id="txt_target<?php echo $sl ?>" onKeyUp="calculate_target_hour(<?php echo $sl ?>)" name="txt_target[]" value="<?php  echo $style_real_arr[$str]; ?>" /></td>
								<td align="right" class="not_clickable" width="80"><input type="text" class="text_boxes"  style=" width:60px;"id="txt_operator<?php echo $sl ?>" name="txt_operator[]" value="<?php  echo $style_real_array[$str]['operator']; ?>" /></td>
								<td align="right" class="not_clickable"><input type="text" class="text_boxes"  style=" width:60px;"id="txt_helper<?php echo $sl ?>" name="txt_helper[]" value="<?php  echo $style_real_array[$str]['helper']; ?>" /></td>
							</tr>
							<? 
							$sl++; 
					
						} 
					}
				}
                foreach($sql_data as $row)
				{
					$bgcolor=($sl%2==0)?"#E9F3FF":"#FFFFFF";
					$job_no='';
					$ex_job=explode('-',$row[csf('job_no')]);
					$job_no=$ex_job[1].'-'.$ex_job[2].'*'.$row[csf('style_ref_no')];
					$is_prod=0;
					$is_prod=$prod_arr[$row[csf('id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]];
					//10_29390_42_21_193_6,11_29389_42_16_43_1
					
					$row_select='';
					$row_select=$row[csf('id')].'_'.$row[csf('item_number_id')].'_'.$row[csf('country_id')].'_'.$row[csf('color_number_id')].'_'.$row[csf('size_number_id')];
					
					$str=$row[csf('id')].'_'.$row[csf('item_number_id')].'_'.$row[csf('country_id')].'_'.$row[csf('color_number_id')].'_'.$row[csf('size_number_id')];
					if($check_row_arr[$str]=="")
					{
						$tr_checked="";
						if(array_key_exists($str,$style_real_arr)) 
						{ 
							if($style_temp_id=="") $style_temp_id=$sl; else $style_temp_id.=",".$sl;
							$list_style_arr[]=$str;
							$bgcolor="yellow";
							$tr_checked="checked";
						}
						
						$new_po_id_arr[]=$row[csf('id')];
						$check_row_arr[$str]=$str;
						?>    
						<tr  id="tr_<? echo $sl; ?>" data-sl="<? echo $sl; ?>" data-prod-id="<? echo $is_prod; ?>" style="cursor:pointer;background-color:<? echo $bgcolor ; ?>">
							<td width="30"><? echo $sl; ?>
							<input type="hidden" name="txt_individual_str" id="txt_individual_str<?php echo $sl ?>" value="<? echo $row_select; ?>"/>
							<input type="checkbox" id="tr_selection<?php echo $sl ?>" name="tr_selection[]"  <?php echo $tr_checked; ?> style="display:none"/> <!--style="display:none"-->
							</td>
							<td width="50"><? echo $row[csf('job_no_prefix_num')]; ?></td>
							<td width="50"><? echo $row[csf('year')]; ?></td>
							<td width="100"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
							<td width="100"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></p></td>
							<td width="100"><p><? echo $row[csf('po_number')]; ?></p></td>
							<td width="70"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
							<td width="100"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
							<td width="100"><p><? echo $country_arr[$row[csf('country_id')]]; ?></p></td>
							<td width="80"><p><? echo $color_arr[$row[csf('color_number_id')]]; ?></p></td>
							<td width="80"><p><? echo $size_arr[$row[csf('size_number_id')]]; ?>&nbsp;</p></td>
							<td align="right" width="80"><? echo $row[csf('po_qty')]; ?></td>
							<td align="right" class="not_clickable" width="80"><input type="text" class="text_boxes"  style=" width:60px;"id="txt_target<?php echo $sl ?>" onKeyUp="calculate_target_hour(<?php echo $sl ?>)" name="txt_target[]" value="<?php  echo $style_real_arr[$str]; ?>" /></td>
							<td align="right" class="not_clickable" width="80"><input type="text" class="text_boxes"  style=" width:60px;"id="txt_operator<?php echo $sl ?>" name="txt_operator[]" value="<?php  echo $style_real_array[$str]['operator']; ?>" /></td>
							<td align="right" class="not_clickable"><input type="text" class="text_boxes"  style=" width:60px;"id="txt_helper<?php echo $sl ?>" name="txt_helper[]" value="<?php  echo $style_real_array[$str]['helper']; ?>" /></td>
						</tr>
						<? 
						$sl++; 
				
					} 
				}
				
				
				?>
         
                <tbody>
            </table>
        </div> 
        <br/>
        <br/>
        <table width="1220" cellspacing="0" cellpadding="0" style="border:none" align="center">
                <tr>
                    <td align="center" height="30" valign="bottom" >
                        <div style="width:100%"> 
                            <div style="width:50%;" align="center">
                                <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                                <? 
									if(count($po_id_all)>0)
									{
										echo load_submit_buttons( $permission, "fnc_Ac_Production_Resource_color", 1,0,"",2);
									}
									else
									{
										echo load_submit_buttons( $permission, "fnc_Ac_Production_Resource_color", 0,0,"",2);
									}
								?>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
	</form>
	<?
	exit();
}

if ($action=="save_update_delete_color_size")
{  
	$flag=0;
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
 		$id_colSiz= return_next_id( "id", "prod_resource_color_size", 1 );
		$field_colSiz="id, mst_id, dtls_id, po_id, gmts_item_id, country_id, color_id, size_id,target_per_line,operator,helper, inserted_by, insert_date";
		for($j=1;$j<=$tot_row;$j++)
		{ 	
			$po_data="po_data_".$j;
			$po_information_arr=explode("_",$$po_data);
			$po_id=$po_information_arr[0];
			$item_id=$po_information_arr[1];
			$country_id=$po_information_arr[2];
			$color_id=$po_information_arr[3];
			$size_id=$po_information_arr[4];
			$target_line="target_".$j;
			$operator="operator_".$j;
			$helper="helper_".$j;
			
			if($data_colSiz!="") $data_colSiz.=",";
			$data_colSiz.="(".$id_colSiz.",".$system_id.",".$mst_dtls_id.",'".$po_id."','".$item_id."','".$country_id."','".$color_id."','".$size_id."','".$$target_line."','".$$operator."','".$$helper."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$id_colSiz = $id_colSiz+1;
			
		}
		if($data_colSiz!="")
		{
			//echo "10**insert into prod_resource_color_size($field_colSiz)values".$data_colSiz;die;
			$rID_colSiz=sql_insert("prod_resource_color_size",$field_colSiz,$data_colSiz,1);
		}
		if($db_type==0 )
		{	
			if($rID_colSiz)
			{
				mysql_query("COMMIT");  
				echo "0**";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		if($db_type==2 || $db_type==1 )
		{	if($rID_colSiz)
			{
				oci_commit($con);
				echo "0**";
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
  	else if ($operation==1) // Update Here End------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$id_colSiz= return_next_id( "id", "prod_resource_color_size", 1 );
		$field_colSiz="id, mst_id, dtls_id, po_id, gmts_item_id, country_id, color_id, size_id,target_per_line,operator,helper, inserted_by, insert_date";
		for($j=1;$j<=$tot_row;$j++)
		{ 	
			$po_data="po_data_".$j;
			$po_information_arr=explode("_",$$po_data);
			$po_id=$po_information_arr[0];
			$item_id=$po_information_arr[1];
			$country_id=$po_information_arr[2];
			$color_id=$po_information_arr[3];
			$size_id=$po_information_arr[4];
			$target_line="target_".$j;
			$operator="operator_".$j;
			$helper="helper_".$j;
			
		
			if($data_colSiz!="") $data_colSiz.=",";
			$data_colSiz.="(".$id_colSiz.",".$system_id.",".$mst_dtls_id.",'".$po_id."','".$item_id."','".$country_id."','".$color_id."','".$size_id."','".$$target_line."','".$$operator."','".$$helper."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$id_colSiz = $id_colSiz+1;
			
		}
		if($data_colSiz!="")
		{
			//echo "10**insert into prod_resource_color_size($field_colSiz)values".$data_colSiz;die;
			$dlete_rID=execute_query("delete from prod_resource_color_size where mst_id=$system_id and dtls_id=$mst_dtls_id");
			$rID_colSiz=sql_insert("prod_resource_color_size",$field_colSiz,$data_colSiz,1);
		}
		
		if($db_type==0)
		{
		 	if($rID_colSiz && $dlete_rID)
			{
				mysql_query("COMMIT");  
				echo "1**";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		if($db_type==2 || $db_type==1 )
		{			
			if($rID_colSiz && $dlete_rID)
			{
				oci_commit($con);
				echo "1**";
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)  // Delete Here---------------------------------------------------------- 
	{
	}
	exit();
}// action save_update_delete end;

if($action=="check_production")
{
	$exdata=explode("***",$data);
	$otherexdata=explode("_",$exdata[0]);
	$po_id=$otherexdata[0];
	$item_number_id=$otherexdata[1];
	$country_id=$otherexdata[2];
	$color_number_id=$otherexdata[3];
	$size_number_id=$otherexdata[4];
	
	$color_size_id=return_field_value("id", "wo_po_color_size_breakdown", "po_break_down_id='$po_id' and item_number_id='$item_number_id' and country_id='$country_id' and size_number_id='$size_number_id' and color_number_id='$color_number_id' and status_active=1 and is_deleted=0");
	
	$chk_id=0;
	
	if($color_size_id!="")
	{
		$sql_check=sql_select("select a.id from pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.sewing_line='$exdata[1]' and a.production_type in (4,5) and b.color_size_break_down_id='$color_size_id'");
		$count_id=count($sql_check);
		if($count_id>0) $chk_id=1; else $chk_id=0;
	}
	echo $chk_id;
	exit();
}

if($action=='SystemIdPopup')
{
echo load_html_head_contents("Popup Info","../../",1, 1,'',1,'');
extract($_REQUEST);
//echo $company_name;die; 
?>	

<script>

// flowing script for multy select data------------------------------------------------------------------------------start;
  function js_set_value(id)
	  { 
		document.getElementById('selected_id').value=id;
		  parent.emailwindow.hide();
	  }

// avobe script for multy select data------------------------------------------------------------------------------end;

</script>

<form>
        <input type="hidden" id="selected_id" name="selected_id" /> 
       
        <table cellspacing="0" width="600"  border="1" rules="all" class="rpt_table" >
            <thead>
                <tr>
                    <th width="100"><strong>Resource Number</strong></th>
                    <th width="100"><strong>Company</strong></th>
                    <th width="100"><strong>Location</strong></th>
                    <th width="100"><strong>Floor</strong></th>
                    <th width="100"><strong>Line Marge</strong></th>
                    <th width="100"><strong>Line Number</strong></th>
                </tr>
            </thead>
        </table>
        
  <div style=" height:380px; overflow:auto;">          
        <table cellspacing="0" width="600"  border="1" rules="all" class="rpt_table" id="tbl_style_ref" >
        <?
       $sql_con="SELECT id, prefix, prod_resource_num, resource_num, company_id, location_id, floor_id, line_marge,line_number FROM prod_resource_mst where is_deleted=0 and company_id=$company_name order by prod_resource_num Asc";
        $sql_data=sql_select($sql_con);
	    $company_nameArr = return_library_array("select id,company_name from lib_company","id","company_name");
	    $locatinArr = return_library_array("select id,location_name from lib_location","id","location_name");
	    $floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");
	    $lineArr = return_library_array("select id,line_name from lib_sewing_line","id","floor_name");

	    $line_name = return_library_array("select id,line_name from lib_sewing_line","id","line_name");
	    $sl=1;
        foreach($sql_data as $row){
        $bgcolor=($sl%2==0)?"#E9F3FF":"#FFFFFF";
        
		$lname='';
		$line_id=explode(",",$row[csf('line_number')]);
			for($i=0; $i<count($line_id); $i++)
			{
			$lname.=($i==(count($line_id)-1))?$line_name[$line_id[$i]]:$line_name[$line_id[$i]].',';
			}

        ?>    
                <tr bgcolor="<? echo $bgcolor ; ?>" id="tr_<? echo $sl; ?>" onClick="js_set_value('<? echo $row[csf('id')].'_'.$row[csf('resource_num')].'_'.$row[csf('company_id')].'_'.$row[csf('location_id')].'_'.$row[csf('floor_id')].'_'.$row[csf('line_marge')].'_'.$row[csf('line_number')].'_'.$lname; ?>')" style="cursor:pointer;">
                    <td width="100" align="center"><? echo $row[csf('prod_resource_num')]; ?></td>
                    <td width="100" align="center"><? echo $company_nameArr[$row[csf('company_id')]]; ?></td>
                    <td width="100" align="center"><? echo $locatinArr[$row[csf('location_id')]]; ?></td>
                    <td width="100" align="center"><? echo $floorArr[$row[csf('floor_id')]]; ?></td>
                    <td width="100" align="center"><? echo $yes_no[$row[csf('line_marge')]]; ?></td>
                    <td width="100" align="center"><? echo $lname; ?></td>
                </tr>
         <? $sl++; } ?>
        </table>
      </div>
        
</form>
<script language="javascript" type="text/javascript">
  setFilterGrid("tbl_style_ref",-1);
</script>


<?
}// action SystemIdPopup end;

$linedata_acttion=explode('**',$action);
if($linedata_acttion[0]=="Line_popup")
{
	echo load_html_head_contents("Sewing Out Info","../../", 1, 1, $unicode,1,'');
?>

<script>
var lineid="<? echo $linedata_acttion[1];?>";
var line_merge="<? echo $linedata_acttion[2];?>";

// flowing script for multy select data------------------------------------------------------------------------------start;
 var selected_id = new Array();
 var selected_line = new Array();
 var selected_line_serial = new Array();
 
	function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		
		
		function js_set_value(str) {
			
			var linetexts=trim($("#lnnetext_"+str).html());
			var lineserial=trim($("#lineserial"+str).html());



			toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( str, selected_id ) == -1 ) {
				selected_id.push( str );
				selected_line.push(linetexts);
				selected_line_serial.push(parseInt(lineserial));
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str ) break;
				}
				selected_id.splice( i, 1 );
				selected_line.splice( i, 1 );
				selected_line_serial.splice( i, 1 );
				
			}

			var id =''; var ln='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				ln += selected_line[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			ln = ln.substr( 0, ln.length - 1 );
			$('#selecteds').val( id );
			$('#linename').val( ln);

		}
	
	function fn_onClosed()
	{
	
		var x=Math.min.apply(Math,selected_line_serial);	
		if(line_merge==1 && (selected_line_serial.length!=0))
		{
			for( var i = 0; i < selected_line_serial.length; i++ )
			{	
				if(( jQuery.inArray( x, selected_line_serial ))== -1)
				{
				//alert(selected_line_serial.min());
				//if(selected_line_serial.length!=1){ alert("Line sequence must be sequentially."); return;}
				}
				x++;
				
			}
		}
	
		if(line_merge==2 && (selected_line_serial.length!=0))
		{
			if(selected_line_serial.length!=1){ alert("Please select single line"); return;}	
		}

		var txt_string = $('#selecteds').val();
		$('#linename').val();
		if(txt_string==""){ alert("Please Select The Serial"); return;}
		parent.emailwindow.hide();
		
	}

</script>
<form>
 <div style="max-height:370px; overflow-y:auto;">       
        <input type="hidden" id="selecteds" name="selecteds" /> 
        <input type="hidden" id="linename" name="linename" />
        <table cellspacing="0" width="100%"  border="1" rules="all" class="rpt_table" id="tbl_style_ref" >
                           
            <thead>
                <tr>
                    <th width="50"><strong>SL</strong></th>
                    <th width="60"><strong>Sequence</strong></th>
                    <th><strong>Line Name</strong></th>
                </tr>
            </thead>
            <tbody>
           
        <?
       
	$com=$linedata_acttion[3];
	$loca=$linedata_acttion[4];
	$floor=$linedata_acttion[5];
	   
	    //$sql_con="select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1 and floor_name!=0 order by line_name";
		
	   $sql_con="select id,line_name,sewing_line_serial from lib_sewing_line where  is_deleted=0 and status_active=1 and company_name='$com' and location_name='$loca' and floor_name=$floor and floor_name!=0 order by sewing_line_serial";
		
		
        $sql_data=sql_select($sql_con);
	    $sl=1;
        foreach($sql_data as $row){
        $bgcolor=($sl%2==0)?"#E9F3FF":"#FFFFFF";
        ?>    
                <tr bgcolor="<? echo $bgcolor ; ?>" id="tr_<? echo $row[csf('id')]; ?>" onClick="js_set_value(<? echo $row[csf('id')]; ?>)" style="cursor:pointer;">
                    <td align="center"><? echo $sl; ?></td>
                    <td align="center" id="lineserial<? echo $row[csf('id')]; ?>"><? echo $row[csf('sewing_line_serial')]; ?></td>
                    <td id="lnnetext_<? echo $row[csf('id')]; ?>"><? echo $row[csf('line_name')]; ?></td>
                </tr>
         <? $sl++; } ?>
            </tbody>
        </table>
</div>     
        <table width="100%">
            <tr>
                <td colspan="2" align="right"><input type="button" name="btn_close" class="formbutton" style="width:100px" value="Close" onClick="fn_onClosed()" /></td>
            </tr>
        </table>
        
</form>

<script>
var lineid=lineid.split(',');
	for(i=0; i<lineid.length; i++)
	{
		js_set_value(parseInt(trim(lineid[i])));	
	}
</script>
<?
}// action Line_popup end;

//pro_ex_factory_mst
if ($action=="save_update_delete")
{  $flag=0;
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		
		$check_same_date=sql_select("select a.resource_num,a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_name and a.location_id=$cbo_location and a.floor_id=$cbo_floor  and a.line_marge=$cbo_line_merge and a.line_number=$cbo_line_no and b.pr_date between ".$txt_date_from." and ".$txt_date_to." and a.is_deleted=0 and b.is_deleted=0");
		if($check_same_date[0]['LINE_NUMBER']){echo "6**".$check_same_date[0][csf('resource_num')];die;}
				
		$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$cbo_company_name and variable_list=23 and is_deleted=0 and status_active=1");
 	
	
		if($prod_reso_allo!=1) { echo 45;die;}
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
 		//table lock here 
		if(str_replace("'",'',$system_id)=="")
		{	
			$resource_mst= return_field_value("id","prod_resource_mst" ,"company_id=$cbo_company_name and location_id=$cbo_location and floor_id=$cbo_floor and line_marge=$cbo_line_merge and line_number=$cbo_line_no and is_deleted=0 ","id");
			
			if(str_replace("'","",$resource_mst)=='')
			{
				if($db_type==2)
				{
					$new_sys_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'APR',date('Y',time()), 5, "select prefix,prod_resource_num  from prod_resource_mst where company_id=$cbo_company_name and  TO_CHAR(insert_date,'YYYY')=".date('Y',time())." order by id DESC ", "prefix", "prod_resource_num" ));
				
				}
				if($db_type==0)
				{
					$new_sys_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'APR', date('Y',time()), 5, "select prefix,prod_resource_num  from prod_resource_mst where company_id=$cbo_company_name  order by id DESC ", "prefix", "prod_resource_num" ));//and YEAR(insert_date)=".date('Y',time())."
				
				}
				$id=return_next_id("id", "prod_resource_mst", 1);
				$field_array1="id, prefix,prod_resource_num,resource_num, company_id,location_id,floor_id,line_marge,line_number,inserted_by,insert_date,is_deleted";
				$data_array1="(".$id.",'".$new_sys_number[1]."',".$new_sys_number[2].",'".$new_sys_number[0]."',".$cbo_company_name.",".$cbo_location.",".$cbo_floor.",".$cbo_line_merge.",".$cbo_line_no.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0)";
				//echo "insert into prod_resource_mst ($field_array1) values $data_array1";die;
			
			}
			else
			{
				$id= $resource_mst;   
				$new_sys_number[0]=return_field_value("resource_num","prod_resource_mst" ," id=$id","resource_num"); 
			}
		}
		else 
		{
			$id=$system_id;
		}
		
		if(str_replace("'",'',$h_dtl_mst_id)=="")
		{	
			if(str_replace("'","",$system_id)!="") $system_id_cond=" and a.id=".str_replace("'","",$system_id)."";
			
			$cbo_line=explode(',',str_replace("'","",$cbo_line_no));
			$retn_line=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_name and a.location_id=$cbo_location and a.floor_id=$cbo_floor and b.pr_date between ".$txt_date_from." and ".$txt_date_to." and a.is_deleted=0 and b.is_deleted=0");
			
			//echo "10**";print_r($retn_line);die;
			
			$acline_id_arr=array();
			foreach($retn_line as $row)
			{
				$ex_line=explode(',',$row[csf('line_number')]);
				foreach($ex_line as $ret_id)
				{
					$acline_id_arr[$ret_id]=$row[csf('id')];
				}
			}
			unset($retn_line);
			//print_r($acline_id_arr); die;
			$line_cond="";
			foreach($cbo_line as $line)
			{
				if($acline_id_arr[$line]!='') $line_cond.=" or a.id='".$acline_id_arr[$line]."'";
			}
			//echo "0**"."select a.resource_num from prod_resource_mst a,prod_resource_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_name and a.location_id=$cbo_location and a.floor_id=$cbo_floor and ( a.line_number=$cbo_line_no $line_cond ) and b.is_deleted=0 and b.pr_date between ".$txt_date_from." and ".$txt_date_to." and a.is_deleted=0 and b.is_deleted=0";
			$resource=return_field_value("a.resource_num","prod_resource_mst a,prod_resource_dtls b","a.id=b.mst_id and a.company_id=$cbo_company_name and a.location_id=$cbo_location and a.floor_id=$cbo_floor and a.line_marge=2 and ( a.line_number=$cbo_line_no $line_cond ) and b.is_deleted=0 and b.pr_date between ".$txt_date_from." and ".$txt_date_to." and a.is_deleted=0 and b.is_deleted=0","resource_num");
		
			if($resource!='' && str_replace("'",'',$cbo_line_merge)==2)
			{
				echo "6**".$resource;
				die;			
			}
			 
			$id3=return_next_id("id", "prod_resource_dtls_mast", 1);
			$field_array3="id, mst_id, from_date, to_date, man_power, operator, helper, line_chief, active_machine, target_per_hour, working_hour, capacity,target_efficiency, total_smv, inserted_by, insert_date, is_deleted";
		
			$total_smv=((str_replace("'","",$txt_man_power)*str_replace("'","",$txt_working_hour))*60);
			$data_array3="(".$id3.",".$id.",".$txt_date_from.",".$txt_date_to.",".$txt_man_power.",".$txt_operator.",".$txt_helper.",".$txt_Line_chief.",".$txt_active_machine.",".$txt_target_hour.",".$txt_working_hour.",".$txt_capacity.",".$txt_efficiency.",".$total_smv.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0)";
			$id2=return_next_id("id", "prod_resource_dtls", 1);
			$date_diff=datediff('d',str_replace("'","",$txt_date_from),str_replace("'","",$txt_date_to));
			//echo "5**".$date_diff;die;
			for($i=0; $i < $date_diff ; $i++)
			{
				$date = add_date(str_replace("'","",$txt_date_from),$i); 
				if($db_type==2) $dd=change_date_format($date,'mm-dd-yyyy','-',1);
				if($db_type==0) $dd=change_date_format($date,'yyyy-mm-dd');
				
				$field_array="id, mast_dtl_id,mst_id, pr_date, man_power, operator, helper, line_chief, active_machine, target_per_hour, working_hour, total_smv, inserted_by, insert_date, is_deleted";     
				if($i!=0)$data_array.=",";
				$data_array.="(".$id2.",".$id3.",".$id.",'".$dd."',".$txt_man_power.",".$txt_operator.",".$txt_helper.",".$txt_Line_chief.",".$txt_active_machine.",".$txt_target_hour.",".$txt_working_hour.",".$total_smv.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0)";
				$id2++;
			}
			//echo "5**".$date_test;die;

		}
		//echo "insert into prod_resource_dtls ($field_array) values $data_array";die;
		//echo "10**".$data_array3;die;
		$field_array_time="id,mst_id,mast_dtl_id,shift_id,prod_start_time,lunch_start_time,remarks";
		$id_time= return_next_id( "id", "prod_resource_dtls_time", 1 );
		$save_string=explode(",",str_replace("'","",$save_string));
		foreach($save_string as $prevData)
		{
			$time_dtls=explode("_",$prevData);
			$shift_id=$time_dtls[0];
			$prod_start_time=$time_dtls[1];
			$lunch_start_time=$time_dtls[2];
			$remarks=$time_dtls[3];
			
			if($db_type==2)
			{
				$prod_start_time="to_date('".$prod_start_time."','HH24:MI:SS')";
				$lunch_start_time="to_date('".$lunch_start_time."','HH24:MI:SS')";
				$data_array_time.=" INTO prod_resource_dtls_time (".$field_array_time.") VALUES(".$id_time.",".$id.",".$id3.",'".$shift_id."',".$prod_start_time.",".$lunch_start_time.",'".$remarks."')";
			}
			else
			{
				if($data_array_time!="") $data_array_time.=",";
				$data_array_time.="(".$id_time.",".$id.",".$id3.",'".$shift_id."','".$prod_start_time."','".$lunch_start_time."','".$remarks."')";
			}
			$id_time = $id_time+1;
		}
		
		
		
		//echo $data_colSiz;die;
		$rID=1;	$rID2=1; $rID3=1; $rID_time=true;
	
		if($data_array1!="") 
		{ 
			 $rID=sql_insert("prod_resource_mst",$field_array1,$data_array1,0);
		}
	
		if(str_replace("'",'',$h_dtl_mst_id)=="")
		{	
			$rID2=sql_insert("prod_resource_dtls_mast",$field_array3,$data_array3,0);
			$rID3=sql_insert("prod_resource_dtls",$field_array,$data_array,0);
		}
	   
		if($data_array_time!="")
	  	{	
		  	if($db_type==2)
			{
				$query="INSERT ALL".$data_array_time." SELECT * FROM dual";
				$rID_time=execute_query($query);
			}
			else
			{
				$rID_time=sql_insert("prod_resource_dtls_time",$field_array_time,$data_array_time,1);
			} 
	   	}
		
	
	  // echo $rID."**".$rID2."**".$rID3."**".$rID_time;die;
		if($db_type==0 )
		{	
			if($rID && $rID2 && $rID3 && $rID_time)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'","",$id)."**".$new_sys_number[0];
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$rID);
			}
		}
		if($db_type==2 || $db_type==1 )
		{	if($rID && $rID2 && $rID3 && $rID_time)
			{
				oci_commit($con);
				echo "0**".str_replace("'","",$id)."**".$new_sys_number[0];
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$rID);
			}
		}
		disconnect($con);
		die;
	}
  	else if ($operation==1) // Update Here End------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$cbo_company_name and variable_list=23 and is_deleted=0 and status_active=1");
 
		if($prod_reso_allo!=1) { echo 45; die;}
	
		$h_dtl_mst_id = str_replace("'","",$h_dtl_mst_id);
		$pre_po_id=return_field_value("po_id","prod_resource_dtls_mast","id=$h_dtl_mst_id and is_deleted=0", "po_id");
		
		
		if(str_replace("'",'',$system_id)!="")
		{
			$cbo_line=explode(',',str_replace("'","",$cbo_line_no));
		
			$retn_line=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and b.mast_dtl_id!=".str_replace("'","",$h_dtl_mst_id)." and a.company_id=$cbo_company_name and a.location_id=$cbo_location and a.floor_id=$cbo_floor and b.pr_date between ".$txt_date_from." and ".$txt_date_to." and a.is_deleted=0 and b.is_deleted=0");
			$acline_id_arr=array();
			foreach($retn_line as $row)
			{
				$ex_line=explode(',',$row[csf('line_number')]);
				foreach($ex_line as $ret_id)
				{
					$acline_id_arr[$ret_id]=$row[csf('id')];
				}
			}
			unset($retn_line);
			$line_cond="";
			foreach($cbo_line as $line)
			{
				if($acline_id_arr[$line]!='') $line_cond.=" or a.id='".$acline_id_arr[$line]."'";
			}
			
			$resource=return_field_value("a.resource_num","prod_resource_mst a,prod_resource_dtls b","a.id=b.mst_id and  b.mast_dtl_id!=".str_replace("'","",$h_dtl_mst_id)." and a.company_id=$cbo_company_name and a.location_id=$cbo_location and a.floor_id=$cbo_floor and a.line_marge=2 and ( a.line_number=$cbo_line_no $line_cond) and b.pr_date between ".$txt_date_from." and ".$txt_date_to." and a.is_deleted=0 and b.is_deleted=0 ","resource_num");
			//echo "6**".$resource."**".$cbo_line_merge;die;
			if($resource!='' && str_replace("'",'',$cbo_line_merge)==2)
			{
				echo "6**".$resource;
				die;			
			}
			$mst_id=str_replace("'",'',$system_id);
			
			$resource_pre_min=sql_select("select pr_date, smv_adjust, smv_adjust_type,id from prod_resource_dtls where mst_id=$mst_id and mast_dtl_id=$h_dtl_mst_id");
			$previous_resource=array();
			foreach($resource_pre_min as $p_val)
			{
				$previous_resource[strtotime($p_val[csf('pr_date')])]['id']=$p_val[csf('id')];
				$previous_resource[strtotime($p_val[csf('pr_date')])]['smv_adjust']=$p_val[csf('smv_adjust')];
				$previous_resource[strtotime($p_val[csf('pr_date')])]['smv_adjust_type']=$p_val[csf('smv_adjust_type')];
			}
			
			$field_array_up="updated_by*update_date";
			$data_array_up="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			$field_array_up3="from_date*to_date*man_power*operator*helper*line_chief*active_machine*target_per_hour*working_hour*capacity*target_efficiency*total_smv*updated_by*update_date*is_deleted";
		
			$total_smv=((str_replace("'","",$txt_man_power)*str_replace("'","",$txt_working_hour))*60);
			$data_array_up3="".$txt_date_from."*".$txt_date_to."*".$txt_man_power."*".$txt_operator."*".$txt_helper."*".$txt_Line_chief."*".$txt_active_machine."*".$txt_target_hour."*".$txt_working_hour."*".$txt_capacity."*".$txt_efficiency."*".$total_smv."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0";
			$field_array="id,mast_dtl_id,mst_id,pr_date,man_power,operator,helper,line_chief,active_machine,target_per_hour,working_hour,smv_adjust,smv_adjust_type ,total_smv,inserted_by,insert_date, is_deleted";

			$id=return_next_id("id", "prod_resource_dtls", 1);
			$date_diff=datediff('d',str_replace("'","",$txt_date_from),str_replace("'","",$txt_date_to));
			
			
			for($i=0; $i < $date_diff; $i++)
			{
				    $date = add_date(str_replace("'","",$txt_date_from),$i);
					if($db_type==2) $dd=change_date_format($date,'mm-dd-yyyy','-',1);
				    if($db_type==0) $dd=change_date_format($date,'yyyy-mm-dd');
					$adjoustment_min=$previous_resource[strtotime($date)]['smv_adjust'];
					$adjustment_type=$previous_resource[strtotime($date)]['smv_adjust_type'];
				  
				   $resource_id_arr[]=$previous_resource[strtotime($date)]['id'];
				   $resource_data_arr[$previous_resource[strtotime($date)]['id']]=explode("*",($id));
					if($i!=0)$data_array.=",";
					$data_array.="(".$id.",".$h_dtl_mst_id.",".$mst_id.",'".$dd."',".$txt_man_power.",".$txt_operator.",".$txt_helper.",".$txt_Line_chief.",".$txt_active_machine.",".$txt_target_hour.",".$txt_working_hour.",'".$adjoustment_min."','".$adjustment_type."',".$total_smv.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0)";
					$id++;
			}
		}
		
		$field_array_time="id,mst_id,mast_dtl_id,shift_id,prod_start_time,lunch_start_time,remarks";
		$id_time= return_next_id( "id", "prod_resource_dtls_time", 1 );
		$save_string=explode(",",str_replace("'","",$save_string));
		foreach($save_string as $prevData)
		{
			$time_dtls=explode("_",$prevData);
			$shift_id=$time_dtls[0];
			$prod_start_time=$time_dtls[1];
			$lunch_start_time=$time_dtls[2];
			$remarks=$time_dtls[3];
			
			if($db_type==2)
			{
				$prod_start_time="to_date('".$prod_start_time."','HH24:MI:SS')";
				$lunch_start_time="to_date('".$lunch_start_time."','HH24:MI:SS')";
				$data_array_time.=" INTO prod_resource_dtls_time (".$field_array_time.") VALUES(".$id_time.",".$mst_id.",".$h_dtl_mst_id.",'".$shift_id."',".$prod_start_time.",".$lunch_start_time.",'".$remarks."')";
			}
			else
			{
				if($data_array_time!="") $data_array_time.=",";
				$data_array_time.="(".$id_time.",".$mst_id.",".$h_dtl_mst_id.",".$shift_id.",'".$prod_start_time."','".$lunch_start_time."','".$remarks."')";
			}
			
			$id_time = $id_time+1;
		}


		$rID=$rID1=$delete=$rID2=$delete2=$rID_time=$rID_colSiz=$resourceUpdate=true;
		if(str_replace("'",'',$system_id)!="")
		{
			$rID=sql_update("prod_resource_mst",$field_array_up,$data_array_up,"id",$mst_id,1);
			
			$rID1=sql_update("prod_resource_dtls_mast",$field_array_up3,$data_array_up3,"id",$h_dtl_mst_id,1);
			$delete=execute_query("delete from prod_resource_dtls where mst_id=$mst_id and mast_dtl_id=$h_dtl_mst_id");
			$delete2=execute_query("delete from prod_resource_dtls_time where mst_id=$mst_id and mast_dtl_id=$h_dtl_mst_id");
			$rID2=sql_insert("prod_resource_dtls",$field_array,$data_array,1);
			$resourceUpdate=execute_query(bulk_update_sql_statement( "prod_resource_smv_adj", "dtl_id", "dtl_id", $resource_data_arr, $resource_id_arr ));
			
			//echo "10** insert into prod_resource_dtls ($field_array) values".$data_array;die;
		//echo bulk_update_sql_statement( "prod_resource_smv_adj", "dtl_id", "dtl_id", $resource_data_arr, $resource_id_arr );die;	
			if($data_array_time!="")
			{	
				if($db_type==2)
				{
					$query="INSERT ALL".$data_array_time." SELECT * FROM dual";
					$rID_time=execute_query($query);
				}
				else
				{
					$rID_time=sql_insert("prod_resource_dtls_time",$field_array_time,$data_array_time,1);
				} 
			}
		}
		
		if($db_type==0)
		{
		 	if($rID && $rID1 && $delete && $rID2 && $delete2 && $rID_time)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$mst_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$rID);
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{			
			if($rID && $rID1 && $delete && $rID2 && $delete2 && $rID_time)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$mst_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$rID);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)  // Delete Here---------------------------------------------------------- 
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$mst_id=str_replace("'","",$system_id);
		$order_wise_used_min=return_field_value("resource_id","pro_resource_ava_min_mst","resource_id=$mst_id and production_date between $txt_date_from and $txt_date_to  and status_active=1 and is_deleted=0");
		$production_data=return_field_value("id","pro_garments_production_mst","sewing_line =$mst_id and prod_reso_allo=1   and production_date  between $txt_date_from and $txt_date_to and status_active=1 and is_deleted=0 ");
	
		if($order_wise_used_min!="")
		{
			echo "201**Delete not Allow.Line found in Order Wise Used Minute Page.";die;
		}
		if($production_data!="")
		{
			echo "201**Delete not Allow.Line found in Garments Production.";die;
		}
		//echo "10**".$mst_id; die;
		$rID=1; $rID1=1; $rID2=1; $rID3=1; $rID4=true;
	
		$rID=execute_query("delete from prod_resource_dtls_mast where id=$h_dtl_mst_id");
		$rID1=execute_query("delete from prod_resource_dtls where mst_id=$mst_id and mast_dtl_id=$h_dtl_mst_id");
		$rID2=execute_query("delete from prod_resource_smv_adj where mst_id=$mst_id and mast_dtl_id=$h_dtl_mst_id");
		$rID3=execute_query("delete from prod_resource_dtls_time where mst_id=$mst_id and mast_dtl_id=$h_dtl_mst_id");
		$rID4=execute_query("delete from prod_resource_color_size where mst_id=$mst_id and dtls_id=$h_dtl_mst_id");
		
		
		if($db_type==0)
		{
		 	if($rID && $rID1 && $rID2 && $rID3 && $rID4)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$mst_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$rID);
			}
		}
		if($db_type==2 || $db_type==1 )
		{			
			if($rID && $rID1 && $rID2 && $rID3 && $rID4)
			{
				oci_commit($con);
				echo "2**".str_replace("'",'',$mst_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$rID);
			}
		}
		disconnect($con);
		die;
	}
	exit();
}// action save_update_delete end;




if ($action=="save_update_delete_minutes_details")
{ 
	$flag=0;
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	
	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		if($db_type==2) $pr_date=change_date_format(str_replace("'","",$cbo_resource_date),'mm-dd-yyyy','-',1);
		if($db_type==0) $pr_date=change_date_format(str_replace("'","",$cbo_resource_date),'yyyy-mm-dd');
		
 		$dtls_id=return_field_value("id","prod_resource_dtls","pr_date='".$pr_date."' and mast_dtl_id=$h_dtl_mst_id and is_deleted=0 ");
			
		$id=return_next_id("id", "prod_resource_smv_adj", 1);
		$field_array="id, mst_id, mast_dtl_id, dtl_id, pr_date, adjustment_source, number_of_emp, adjust_hour, total_smv, inserted_by, insert_date";
		
		for($i=1; $i <= $total_row ; $i++)
		{
			$adjust_type="adjust_type".$i;
			$hours="hours".$i;
			$employee="employee".$i;
			$minutes="minutes".$i;
			if($data_array!="") $data_array.=",";
			$data_array.="(".$id.",".$system_id.",".$h_dtl_mst_id.",'".$dtls_id."','".$pr_date."',".$$adjust_type.",'".$$employee."','".$$hours."',".$$minutes.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$id++;
		}

		if($data_array!="") 
		{ 
			 $field_array_up="smv_adjust*smv_adjust_type";
			 $txt_total_minutes=str_replace("'","",$txt_total_minutes);
			 if(($txt_total_minutes*1)>=0)
			 { 
			 	$adjustment_type=1;
			 }
			 else
			 {
				  $adjustment_type=2;
				  $txt_total_minutes=$txt_total_minutes*(-1);
			 }
			 $data_array_up="".$txt_total_minutes."*".$adjustment_type."";
			 //echo $data_array_up;
			 $rID=sql_insert("prod_resource_smv_adj",$field_array,$data_array,0);
			 $rID1=sql_update("prod_resource_dtls",$field_array_up,$data_array_up,"id",$dtls_id,1);
		}
	 // echo $rID."**".$rID1."**".$rID3."**".$dtls_id;die;
		if($db_type==0 )
		{	
			if($rID && $rID1)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'","",$id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$rID);
			}
		}
		if($db_type==2 || $db_type==1 )
		{	if($rID && $rID1)
			{
				oci_commit($con);
				echo "0**".str_replace("'","",$id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$rID);
			}
		}
		disconnect($con);
		die;
	}
  	else if ($operation==1) // Update Here End------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		if($db_type==2) $pr_date=change_date_format(str_replace("'","",$cbo_resource_date),'mm-dd-yyyy','-',1);
		if($db_type==0) $pr_date=change_date_format(str_replace("'","",$cbo_resource_date),'yyyy-mm-dd');
		
		$previous_details_id=$dtls_id;
		$dtls_id=return_field_value("id","prod_resource_dtls","pr_date='".$pr_date."' and mast_dtl_id=$h_dtl_mst_id and is_deleted=0 ");	
		$id=return_next_id("id", "prod_resource_smv_adj", 1);
		$field_array="id, mst_id, mast_dtl_id, dtl_id, pr_date, adjustment_source, number_of_emp, adjust_hour, total_smv, inserted_by, insert_date";
		
		for($i=1; $i <= $total_row ; $i++)
		{
			$adjust_type="adjust_type".$i;
			$hours="hours".$i;
			$employee="employee".$i;
			$minutes="minutes".$i;
			if($data_array!="") $data_array.=",";
			$data_array.="(".$id.",".$system_id.",".$h_dtl_mst_id.",".$dtls_id.",'".$pr_date."',".$$adjust_type.",'".$$employee."','".$$hours."',".$$minutes.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$id++;
		}


		if($data_array!="") 
		{ 
			 $field_array_up="smv_adjust*smv_adjust_type";
			 $txt_total_minutes=str_replace("'","",$txt_total_minutes);
			 if(($txt_total_minutes*1)>=0)
			 {
				 $adjustment_type=1;
			 }
			 else 
			 {
				 $adjustment_type=2;
				 $txt_total_minutes=$txt_total_minutes*(-1);
			 }
			 $data_array_up="".$txt_total_minutes."*".$adjustment_type."";
			 $delete=execute_query("delete from prod_resource_smv_adj where dtl_id=$previous_details_id");
			 $rID=sql_insert("prod_resource_smv_adj",$field_array,$data_array,0);
			 
			 $rID1=sql_update("prod_resource_dtls",$field_array_up,$data_array_up,"id",$dtls_id,1);
		}

	  //echo $rID."**".$rID1."**".$delete."**".$dtls_id;die;
		if($db_type==0 )
		{	
			if($rID && $rID1 && $delete)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'","",$id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$rID);
			}
		}
		if($db_type==2 || $db_type==1 )
		{	if($rID && $rID1 && $delete)
			{
				oci_commit($con);
				echo "0**".str_replace("'","",$id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$rID);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)  // Delete Here---------------------------------------------------------- 
	{
	}
	exit();
}// action save_update_delete end;


if($action=="check_date_available")
{
	$data=explode("_",$data);

	if($db_type==2) $pr_date=change_date_format(str_replace("'","",$data[0]),'mm-dd-yyyy','-',1);
	if($db_type==0) $pr_date=change_date_format(str_replace("'","",$data[0]),'yyyy-mm-dd');

	$sql="select dtl_id from prod_resource_smv_adj where mast_dtl_id=$data[1] and pr_date='".$pr_date."' and status_active=1  and is_deleted=0  group by dtl_id";	

	//echo $sql;
	$sql_rows=sql_select($sql, 1);
	foreach($sql_rows as $rows)
		echo $rows[csf("dtl_id")];
	exit();
	 
}



if ($action=="adjustment_minint_popup")
{
	echo load_html_head_contents("Production Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
?> 
	<script>
		var permission='<? echo $permission; ?>';


		function check_date_available(pr_date)
		{
			var dtls_id=return_global_ajax_value( pr_date+"_<?php echo $h_dtl_mst_id;?>", "check_date_available", "", 'actual_production_resource_entry_controller');
			if(trim(dtls_id)!='')
			{
				var dicission=confirm("Data already inserted for this production date.Click Yes for update this entry or No for another production date entry.");
				if(dicission==1)
				{
					update_details_data(dtls_id,pr_date)
				}
				else
				{
					$("#cbo_resource_date").val(0);
				}

			}
		}

		function calculate_adjust_minit(id,type)
		{
			if (form_validation('cboSmvAdjustSource_'+id,'Particulars')==false)
			{
				if(type==1) $("#txtNoOfEmployee_"+id).val('');
				else  		$("#txtHour_"+id).val('');
				return;
			}
			$("#txtMinutes_"+id).val(($("#txtNoOfEmployee_"+id).val()*1)*($("#txtHour_"+id).val()*1)*60);
			calculate_adjust_minit_total();
		}
		
		
		function calculate_adjust_minit_total()
		{
			var total_minutes=0;
			$("#tbl_list_search tbody tr").each(function() {
				if($(this).find('select[name="cboSmvAdjustSource[]"]').val()==1)
				{
                	total_minutes+=($(this).find('input[name="txtMinutes[]"]').val()*1);
				}
				else
				{
					total_minutes-=($(this).find('input[name="txtMinutes[]"]').val()*1);
				}
            });
			
			$("#txt_total_minutes").val(total_minutes);
		}
		
		function add_break_down_tr( i )
		{
			if (form_validation('cboSmvAdjustSource_'+i+'*txtNoOfEmployee_'+i+'*txtHour_'+i,'Particulars*No Of Employee*Hour')==false) 
			{
				return;
			}
			
			var i=$("#max_tr_id").val();
			i++;
		
			$("#tbl_list_search tbody tr:first").clone().find("input,select").each(function(){
			$(this).attr({ 
				'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
				'value': function(_, value) { return value }              
			});
			}).end().appendTo("#tbl_list_search tbody");
			$("#tbl_list_search tbody tr:last").removeAttr('id').attr('id','tr_'+i);
			


			$("#tbl_list_search tbody tr:last td:first").text(i);
			//alert($(this).attr("id"));
			//$(this).attr("id","+i+");	
			$('#cboSmvAdjustSource_'+i).val(0);
			$('#txtNoOfEmployee_'+i).val('');
			$('#txtHour_'+i).val('');
			$('#txtMinutes_'+i).val('');
			$('#txtNoOfEmployee_'+i).removeAttr("onKeyUp").attr("onKeyUp","calculate_adjust_minit("+i+",1);");
			$('#txtHour_'+i).removeAttr("onKeyUp").attr("onKeyUp","calculate_adjust_minit("+i+",2);");
			$('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
			$('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deleteRow("+i+");");
			$("#max_tr_id").val(i);
			//alert($("#tbl_particulars_details tr").length)
			$("#tr_"+i).find("td:eq(0)").text($("#tbl_particulars_details tr").length);
			set_all_onclick();
		}
		
		
		function fnc_Adjust_Minutes_Entry(operation)
		{
			if (form_validation('cbo_resource_date','Production Date')==false) 
			{
				return;
			}
			var j=0;
			var data_all="&system_id=<?php echo $system_id;?>&h_dtl_mst_id=<?php echo $h_dtl_mst_id;?>";
			$("#tbl_list_search tbody tr").each(function() {
					var tr_id=($(this).attr("id")).split("_");
					var i=tr_id[1];
					if (form_validation('cboSmvAdjustSource_'+i+'*txtNoOfEmployee_'+i+'*txtHour_'+i,'Particulars*No Of Employee*Hour')==false) 
					{
						return;
					}
					
					var adjust_type 			= $(this).find('select[name="cboSmvAdjustSource[]"]').val();
					var hours   				= $(this).find('input[name="txtHour[]"]').val();
					var employee   				= $(this).find('input[name="txtNoOfEmployee[]"]').val();
					var minutes					= $(this).find('input[name="txtMinutes[]"]').val();
					j++;
					
					data_all += "&adjust_type" + j + "=" + adjust_type 
					 			+ "&hours" + j + "=" + hours 
					 			+ "&employee" + j + "=" + employee 
								+ "&minutes" + j + "=" + minutes ;
			
			  });

			var data="action=save_update_delete_minutes_details&operation="+operation+"&total_row="+j+data_all+get_submitted_data_string("cbo_resource_date*txt_total_minutes*dtls_id","../../");
			freeze_window(operation);
			http.open("POST","actual_production_resource_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_smv_adjust_reponse;
		}
		
		function fnc_smv_adjust_reponse()
		{
			if(http.readyState == 4) 
			{ 
				var response=trim(http.responseText).split('**');
				set_button_status(1, permission, 'fnc_Adjust_Minutes_Entry',2);

				show_list_view('<?php echo $h_dtl_mst_id;?>','create_adjust_date','listview_adjust_date','actual_production_resource_entry_controller','');
			//document.getElementById('cbo_location').value=theemail[3];
				
				release_freezing();
			}
		}
		
		function update_details_data(details_id,pr_date)
		{
			
			//alert(pr_date);
			$("#cbo_resource_date").val(pr_date);
			$("#cbo_resource_date").attr("disabled",true);
			show_list_view(details_id+"_"+pr_date,'show_particulars_details','tbl_list_search','actual_production_resource_entry_controller','');

			$("#dtls_id").val(details_id);
			set_button_status(1, permission, 'fnc_Adjust_Minutes_Entry',2);
		}
		
		
		function fnResetForm()
		{
			$("#cbo_resource_date").val(0);
			$("#cbo_resource_date").attr("disabled",false);
			$("#txt_total_minutes").val('');
			
			var html='<tr id="tr_1" align="center" valign="middle"> <td id="tdId_1">1</td><td><?  echo create_drop_down( "cboSmvAdjustSource_1",120, $smv_adjustment_head,"", 1, "-- Select --", $selected,"calculate_adjust_minit_total()", "" ,"","","","","","","cboSmvAdjustSource[]" );
  ?> </td><td><input class="text_boxes_numeric" type="text" style="width:70px" name="txtNoOfEmployee[]" id="txtNoOfEmployee_1" value=""  onKeyUp="calculate_adjust_minit(1,1)"/></td><td><input class="text_boxes_numeric" type="text" style="width:50px" name="txtHour[]" id="txtHour_1" onKeyUp="calculate_adjust_minit(1,2)"/></td><td><input class="text_boxes_numeric" type="text" style="width:100px" name="txtMinutes[]" id="txtMinutes_1"  readonly/></td><td ><input type="button" id="increase_1" name="increase_1" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(1)" /><input type="button" id="decrease_1" name="decrease_1" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" /></td></tr>';
  
			$("#tbl_particulars_details").html("");
			$("#tbl_particulars_details").html(html);
			
			
			set_button_status(0, permission, 'fnc_Adjust_Minutes_Entry',2);
		}

		function fn_deleteRow(row_id)
		{
			if($("#tbl_particulars_details tr").length>1)
			{
				$("#tr_"+row_id).remove();
			}
			else
			{
				$("#cboSmvAdjustSource_"+row_id).val(0);
				$("#txtNoOfEmployee_"+row_id).val('');
				$("#txtHour_"+row_id).val('');
				$("#txtMinutes_"+row_id).val('');
			}

			
			//$("#row_id__"+i).remove();
			calculate_adjust_minit_total();
			rearange_sl();
		}

		function rearange_sl()
		{
			var i=1;
			$("#tbl_list_search tbody tr").each(function() {
				//$(this).find("td:eq(0)).text();
				$(this).find("td:eq(0)").text(i);
				i++;
			});
		}

		

    </script>

</head>
<?php


?>
<body>
	<form name="searchdescfrm"  id="searchdescfrm">
		<fieldset style="width:820px;margin-left:10px">
            
            <input type="hidden" name="dtls_id" id="dtls_id" class="text_boxes" value="">
             <? echo load_freeze_divs ("../../../",$permission);  ?>
             <table>
             	<tr>
                	<td>
                    	<div style="width:620px;max-height:250px;" align="left">
                        	
                                <table width="300"   align="center" >
                                    <tr align="center">
                                        <td width="100" class="must_entry_caption">Production Date</td>
                                        <td>
                                            <? 
												$resource_date_arr=array();
												for($i=0;$i<datediff( "d", $txt_date_from, $txt_date_to);$i++)
												{
													$r_date=add_date($txt_date_from,$i);
													$resource_date_arr[date("d-m-Y",strtotime($r_date))]=date("d-m-Y",strtotime($r_date));
												}
                                                echo create_drop_down( "cbo_resource_date",120, $resource_date_arr,"", 1, "-- Select --", $selected,"", "" ,"","","","","","","cbo_resource_date[]" );
                                            ?>
                                        </td>
                                    </tr>
                                </table>
                            
                           
                            <table cellspacing="0" width="500" cellpadding="0" class="rpt_table" rules="all" border="1" id="tbl_list_search">
                                <thead>
                                    <th width="30">SL</th>
                                    <th width="120" class="must_entry_caption">Particulars</th>
                                    <th width="100" class="must_entry_caption">No Of Employee</th>
                                    <th width="60" class="must_entry_caption">Hour</th>
                                    <th width="60">Minutes</th>
                                    <th width="100">Action</th>
                                </thead>
                                <tbody id="tbl_particulars_details">
                                    <tr bgcolor="<? echo $bgcolor; ?>" id="tr_1" align="center" valign="middle"> 
                                        <td id="tdId_1">1</td>
                                        <td>
                                        <? 
                                            echo create_drop_down( "cboSmvAdjustSource_1",120, $smv_adjustment_head,"", 1, "-- Select --", $selected,"calculate_adjust_minit_total()", "" ,"","","","","","","cboSmvAdjustSource[]" );
                                        ?>
                                        </td>
                                        <td>
                                            <input class="text_boxes_numeric" type="text" style="width:70px" name="txtNoOfEmployee[]" id="txtNoOfEmployee_1" value=""  onKeyUp="calculate_adjust_minit(1,1)"/>
                                        </td>
                                        <td>
                                            <input class="text_boxes_numeric" type="text" style="width:50px" name="txtHour[]" id="txtHour_1" onKeyUp="calculate_adjust_minit(1,2)"/>
                                        </td>
                                        <td><input class="text_boxes_numeric" type="text" style="width:100px" name="txtMinutes[]" id="txtMinutes_1"  readonly/></td>
                                        <td >
                                            <input type="button" id="increase_1" name="increase_1" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(1)" />
                                            <input type="button" id="decrease_1" name="decrease_1" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" />
                                        </td>
                                    </tr>
                                   
                                </tbody>
                                <tfoot class="tbl_bottom">
                                    <th width="" colspan="4">Net Changes</th>
                                    
                                    <th width="">
                                    	<input class="text_boxes_numeric" type="text" style="width:100px" name="txt_total_minutes" id="txt_total_minutes"  readonly/>
                                    	<input type="hidden" name="max_tr_id" id="max_tr_id" class="text_boxes" value="1">
                                    </th>
                                    <th width=""></th>
                                </tfoot>
                            </table>
                            <table width="500" align="left">
                                 <tr>
                                    <td align="center">
                                    <? 
                                        echo load_submit_buttons( $permission, "fnc_Adjust_Minutes_Entry", 0,0,"fnResetForm()",2);
                                    ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </td>
                    <td style="vertical-align:top">
                    	<div style="width:200px;" align="left" >
                        	<table width="150" cellpadding="0" class="rpt_table" rules="all" border="1">
                                <thead>
                                    <th width="30">SL</th>
                                    <th width="100">Date</th>
                                    
                                </thead>
                          	</table>
                            <div style="width:200px; max-height:200px; overflow-y:scroll;" id="listview_adjust_date">
                                <table width="150" cellpadding="0" class="rpt_table" rules="all" border="1" id="list_view3">
                                    <tbody id="">
                                       <?php
									   	$sql_result=sql_select(" select pr_date,dtl_id from prod_resource_smv_adj where mast_dtl_id=$h_dtl_mst_id and status_active=1 group  by pr_date,dtl_id");
										$i=1;
										foreach($sql_result as $val)
										{
											$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
											?>
											<tr id="tr_<? echo $i; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="update_details_data('<? echo $val[csf('dtl_id')]; ?>','<? echo date("d-m-Y",strtotime($val[csf('pr_date')])); ?>');">
												<td width="30"><?php echo $i; ?></td>
												<td width="100"><?php echo change_date_format($val[csf("pr_date")]); ?></td>
											</tr>
										<?php
											$i++;
                                        }
									   ?> 
                                    </tbody>
                                </table>
                            </div>
                      </div>
                    </td>
                </tr>
             </table>
            
		</fieldset>
	</form>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	$("#cbo_resource_date").val(0);
	$("#cbo_resource_date").attr("onchange","calculate_adjust_minit_total();check_date_available(this.value);");

</script>
</html>
<?
exit();
}

if($action=='show_particulars_details')
{
	$data=explode("_",$data);
	
	?>
    <thead>
        <th width="30">SL</th>
        <th width="120">Particulars</th>
        <th width="100">No Of Employee</th>
        <th width="60">Hour</th>
        <th width="60">Minutes</th>
        <th width="100">Action</th>
    </thead>
    <tbody id="tbl_particulars_details">
	<?php
	$sql_result=sql_select(" select * from prod_resource_smv_adj where dtl_id=$data[0] and status_active=1");
	$i=0;
	$total_min=0;
	foreach($sql_result as $val)
	{
		$i++;
	?>	
        <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<?php echo $i; ?>" align="center" valign="middle"> 
            <td id="tdId_<?php echo $i; ?>"><?php echo $i; ?></td>
            <td>
            <? 
                echo create_drop_down( "cboSmvAdjustSource_".$i,120, $smv_adjustment_head,"", 1, "-- Select --", $val[csf("adjustment_source")],"calculate_adjust_minit_total()", "" ,"","","","","","","cboSmvAdjustSource[]" );
            ?>
            </td>
            <td>
                <input class="text_boxes_numeric" type="text" style="width:70px" name="txtNoOfEmployee[]" id="txtNoOfEmployee_<?php echo $i; ?>" value="<?php echo $val[csf("number_of_emp")]; ?>" onKeyUp="calculate_adjust_minit(<?php echo $i; ?>,1)"/>
            </td>
            <td>
                <input class="text_boxes_numeric" type="text" style="width:50px" name="txtHour[]" id="txtHour_<?php echo $i; ?>" value="<?php echo $val[csf("adjust_hour")]; ?>" onKeyUp="calculate_adjust_minit(<?php echo $i; ?>,2)"/>
            </td>
            <td><input class="text_boxes_numeric" type="text" style="width:100px" name="txtMinutes[]" id="txtMinutes_<?php echo $i; ?>"  value="<?php echo $val[csf("total_smv")]; ?>" readonly/></td>
            <td >
                <input type="button" id="increase_<?php echo $i; ?>" name="increase_<?php echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<?php echo $i; ?>)" />
                <input type="button" id="decrease_<?php echo $i; ?>" name="decrease_<?php echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<?php echo $i; ?>);" />
            </td>
        </tr>
	<?
	if($val[csf("adjustment_source")]==1)
	{
		$total_min+=$val[csf("total_smv")];
	}
	else
	{
		$total_min-=$val[csf("total_smv")];
	}
	
	}
	?>
    	<tfoot class="tbl_bottom">
            <th width="" colspan="4">Net Changes</th>
            
            <th width=""><input class="text_boxes_numeric" type="text" style="width:100px" value="<?php echo $total_min; ?>" name="txt_total_minutes" id="txt_total_minutes"  readonly/>
            <input type="hidden" name="max_tr_id" id="max_tr_id" class="text_boxes" value="<?php echo $i; ?>"></th>
            <th width=""></th>
        </tfoot>
    </table>
    
    
    
    <?php
	exit();
}

if($action=='create_adjust_date')
{
	
	?>	
	<table width="150" cellpadding="0" class="rpt_table" rules="all" border="1" id="list_view3">
        <tbody id="">
           <?php
            $sql_result=sql_select(" select pr_date,dtl_id from prod_resource_smv_adj where mast_dtl_id=$data and status_active=1 group  by pr_date,dtl_id");
            $i=1;
            foreach($sql_result as $val)
            {
                $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
                ?>
                <tr id="tr_<? echo $i; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="update_details_data('<? echo $val[csf('dtl_id')]; ?>','<? echo date("d-m-Y",strtotime($val[csf('pr_date')])); ?>');">
                    <td width="30"><?php echo $i; ?></td>
                    <td width="100"><?php echo change_date_format($val[csf("pr_date")]); ?></td>
                </tr>
            <?php
				$i++;
            }
           ?> 
        </tbody>
    </table>
	<?
	exit();
}
if($action=='create_acl_pdc_rec_entry_list_view')
{
	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name");
	$size_arr=return_library_array( "select id,size_name from lib_size", "id", "size_name");
	
	$actual_col_size_arr=array();
	$sql_colSiz="select dtls_id, po_id, gmts_item_id, color_id, size_id from  prod_resource_color_size where mst_id=$data";
	$sql_colSiz_res=sql_select($sql_colSiz);
	$tot_rows=0; $poIds='';
	foreach($sql_colSiz_res as $row)
	{
		$tot_rows++;
		$poIds.=$row[csf("po_id")].",";
		$actual_col_size_arr[$row[csf('dtls_id')]]['po_id'].=$row[csf('po_id')].',';
		$actual_col_size_arr[$row[csf('dtls_id')]]['item_id'].=$row[csf('gmts_item_id')].',';
		$actual_col_size_arr[$row[csf('dtls_id')]]['color_id'].=$row[csf('color_id')].',';
		$actual_col_size_arr[$row[csf('dtls_id')]]['size_id'].=$row[csf('size_id')].',';
	}
	//echo $poIds;die;
	unset($sql_colSiz_res);
	$ex_po_id=array_filter(array_unique(explode(',',$poIds)));
	$idsPo=implode(",",$ex_po_id); $job_arr=array(); $po_arr=array();
	if($idsPo!='')
	{
		$sql_job="select a.style_ref_no, a.job_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where b.id in ($idsPo) and a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
		//echo $sql_job;die;
		$sql_job_res=sql_select($sql_job);
		foreach($sql_job_res as $row)
		{
			$job_arr[$row[csf('id')]]['job_no']=$row[csf('job_no')];
			$job_arr[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
			$job_arr[$row[csf('id')]]['po']=$row[csf('po_number')];
		}
	}
	//print_r($job_arr);
	unset($sql_job_res);
	//echo  $sql."<br>";
	?>	
	<form>
        <div style="width:1130px;">
            <table cellspacing="0" width="1130"  border="1" rules="all" class="rpt_table" align="left">
                <thead>
                    <tr>
                        <th width="35">SL</th>
                        <th width="80">Form Date</th>
                        <th width="80">To Date</th>
                        <th width="50">Man Power</th>
                        <th width="60">Operator</th>
                        <th width="50">Helper</th>
                        <th width="50">Act. M/C</th>
                        <th width="50">Target /Hour</th>
                        <th width="60">Working Hour</th>
                        <th width="100">Job No</th>
                        <th width="100">Style Ref No</th>
                        <th width="100">Po No</th>
                        <th width="100">Gmts Item</th>
                        <th width="100">Color Name</th>
                        <th>Size Name</th>
                    </tr>
                </thead>
            </table>  
        </div>         
        <div style="width:1130px; max-height:180px; overflow-y:scroll;">
            <table cellspacing="0" width="1110"  border="1" rules="all" align="left" class="rpt_table" id="list_view" >
                <tbody>  
				<?
				$sql_dtls ="SELECT id, from_date, to_date, man_power, operator, helper, active_machine, target_per_hour,style_ref_id, working_hour FROM prod_resource_dtls_mast where mst_id=$data and is_deleted=0 order by from_date desc";
                //echo $sql_dtls;
                $sql_data=sql_select($sql_dtls);
                $i=1;
                foreach($sql_data as $row)
				{
					$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					$job_no='';$style_no=''; $po_no=''; $gmt_item=''; $color_nam=''; $size_nam='';
					$ex_po_id=array_filter(array_unique(explode(',',$actual_col_size_arr[$row[csf('id')]]['po_id'])));
					
					foreach($ex_po_id as $po_id)
					{
						if($job_no=='') $job_no=$job_arr[$po_id]['job_no']; else $job_no.=','.$job_arr[$po_id]['job_no'];
						if($style_no=='') $style_no=$job_arr[$po_id]['style_ref_no']; else $style_no.=','.$job_arr[$po_id]['style_ref_no'];
						if($po_no=='') $po_no=$job_arr[$po_id]['po']; else $po_no.=','.$job_arr[$po_id]['po'];
					}
					
					
					$ex_item='';
					$ex_item=array_filter(array_unique(explode(',',$actual_col_size_arr[$row[csf('id')]]['item_id'])));
					foreach($ex_item as $item)
					{
						if($gmt_item=='') $gmt_item=$garments_item[$item]; else $gmt_item.=','.$garments_item[$item];
					}
					$ex_color='';
					$ex_color=array_filter(array_unique(explode(',',$actual_col_size_arr[$row[csf('id')]]['color_id'])));
					foreach($ex_color as $color)
					{
						if($color_nam=='') $color_nam=$color_arr[$color]; else $color_nam.=','.$color_arr[$color];
					}
					
					$ex_size='';
					$ex_size=array_filter(array_unique(explode(',',$actual_col_size_arr[$row[csf('id')]]['size_id'])));
					foreach($ex_size as $size)
					{
						if($size_nam=='') $size_nam=$size_arr[$size]; else $size_nam.=','.$size_arr[$size];
					}
					
					$job_num=''; $po_num=''; $item_name=''; $color_val=''; $size_val="";
					
					if($job_no!='')
					{
						$exjob_no=array_unique(explode(',',$job_no));
						foreach($exjob_no as $job)
						{
							$ejob=explode('-',$job);
							if($job_num=='') $job_num=$ejob[1].'-'.$ejob[2]; else $job_num.=','.$ejob[1].'-'.$ejob[2];
						}
					}
					$po_num=implode(", ",array_unique(explode(',',$po_no)));
					
					$item_name=implode(", ",array_unique(explode(',',$gmt_item)));
					$color_val=implode(", ",array_unique(explode(',',$color_nam)));
					$size_val=implode(", ",array_unique(explode(',',$size_nam)));
					
					?> 
                    <tr id="tr_<? echo $i; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="get_php_form_data('<? echo $row[csf('id')]; ?>', 'load_php_data_to_form', 'requires/actual_production_resource_entry_controller');">    
                        <td width="35"><? echo $i; ?></td>
                        <td width="80"><? echo change_date_format($row[csf('from_date')]); ?></td>
                        <td width="80"><? echo change_date_format($row[csf('to_date')]); ?></td>
                        <td width="50" align="right"><? echo $row[csf('man_power')]; ?>&nbsp;</td>
                        <td width="60" align="right"><? echo $row[csf('operator')]; ?>&nbsp;</td>
                        <td width="50" align="right"><? echo $row[csf('helper')]; ?>&nbsp;</td>
                        <td width="50" align="right"><? echo $row[csf('active_machine')]; ?>&nbsp;</td>
                        <td width="50" align="right"><? echo $row[csf('target_per_hour')]; ?>&nbsp;</td>
                        <td width="60" align="right"><? echo $row[csf('working_hour')]; ?>&nbsp;</td>
                        <td width="100"><p><? echo $job_num; ?>&nbsp;</p></td>
                        <td width="100"><p><? echo $style_no; ?>&nbsp;</p></td>
                        <td width="100"><p><? echo $po_num; ?>&nbsp;</p></td>
                        <td width="100"><p><? echo $item_name; ?>&nbsp;</p></td>
                        <td width="100"><p><? echo $color_val; ?>&nbsp;</p></td>
                        <td><? echo $size_val; ?>&nbsp;</td>
					</tr>
					<? 
					$i++; 
				} ?>
                <tbody>
            </table>
        </div> 
	</form>
	<?
	exit();
}

if($action=="load_php_data_to_form")
{
	list($id,$mast_id,$form_date,$form_to)=explode("_",$data);
/*$fdate=explode("-",$form_date);
$tdate=explode("-",$form_to);
$form_date=$fdate[2].'-'.$fdate[1].'-'.$fdate[0];
$form_to=$tdate[2].'-'.$tdate[1].'-'.$tdate[0];*/
//echo $frm_d=change_date_format($form_date);die;
	

	
	$sql_con = return_library_array("SELECT id, resource_num FROM prod_resource_mst","id","resource_num"); 
	
	$res = sql_select("SELECT  a.company_id,a.location_id,a.floor_id,a.line_number,b.id, b.mst_id, b.from_date, b.to_date, b.man_power, b.operator, b.helper, b.line_chief, b.active_machine, b.target_per_hour, b.working_hour, b.po_id, smv_adjust, smv_adjust_type, b.capacity,b.target_efficiency FROM prod_resource_mst a,prod_resource_dtls_mast b where a.id=b.mst_id and b.id=$id and b.is_deleted=0");
	
	$job_arr=array();
	
	$job_sql=sql_select("SELECT b.id, a.job_no, a.style_ref_no FROM wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.status_active=1");
	foreach($job_sql as $vals)
	{
		$ex_job=explode("-",$vals[csf("job_no")]);
		$job_arr[$vals[csf("id")]]=$ex_job[1].'-'.$ex_job[2]."*".$vals[csf("style_ref_no")];
	}
	unset($job_sql );
	/*$jobArr = return_library_array("SELECT b.id, a.job_no FROM wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.status_active=1","id","job_no");
 
	$styleSql = sql_select("SELECT b.id, a.job_no,a.style_ref_no FROM wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.status_active=1");
	foreach($styleSql as $k=>$vals)
	{
		$styleArr[$vals[csf("id")]]=$vals[csf("job_no")].",".$vals[csf("style_ref_no")];
	}*/
 
 	
	foreach($res as $result)
	{
		$ex_poid=array_filter(explode(',',$result[csf('po_id')]));
		
		$style_no='';
		foreach($ex_poid as $po_id)
		{
			//if($job_no=="") $job_no=$jobArr[$po_id]; else  $job_no.=','.$jobArr[$po_id];
			if($style_no=="") $style_no=$job_arr[$po_id]; else  $style_no.=','.$job_arr[$po_id];
		}
		//echo $job_no;
		
		//-----------------------------------
		if($db_type==0)
		{ 
			if ($result[csf('from_date')]!="" && $result[csf('to_date')]!="") $date_cond = "and production_date between '".change_date_format($result[csf('from_date')],'yyyy-mm-dd')."' and '".change_date_format($result[csf('to_date')],'yyyy-mm-dd')."'"; else $date_cond ="";
		}
		else
		{
			if ($result[csf('from_date')]!="" && $result[csf('to_date')]!="") $date_cond = "and production_date between '".change_date_format($result[csf('from_date')], "", "",1)."' and '".change_date_format($result[csf('to_date')], "", "",1)."'"; else $date_cond ="";
		}
			
		$isSewInputOut=return_field_value("count(id) as id","pro_garments_production_mst","production_type in(4,5,11) $date_cond and is_deleted=0 and serving_company=".$result[csf('company_id')]." and location=".$result[csf('location_id')]." and floor_id=".$result[csf('floor_id')]." and sewing_line in(".$result[csf('mst_id')].") and  is_deleted=0 ","id");
		if($isSewInputOut > 0){
			echo "$('#txt_date_from').attr('disabled',true);\n";	
			echo "$('#txt_date_to').attr('disabled',true);\n";	
		}
		else
		{
			echo "$('#txt_date_from').attr('disabled',false);\n";	
			echo "$('#txt_date_to').attr('disabled',false);\n";	
		}
		
		
		//----------------------------------------
		


		echo "$('#h_dtl_mst_id').val('".$id."');\n";
		//echo "$('#system_id_show').val('".$sql_con[$mast_id]."');\n";
		//echo "$('#system_id').val('".$mast_id."');\n";
		echo "$('#txt_date_from').val('".change_date_format($result[csf('from_date')])."');\n";
		echo "$('#txt_date_to').val('".change_date_format($result[csf('to_date')])."');\n";
		echo "$('#txt_man_power').val('".$result[csf('man_power')]."');\n";
		
		echo "$('#txt_operator').val('".$result[csf('operator')]."');\n";
		echo "$('#txt_helper').val('".$result[csf('helper')]."');\n";
		echo "$('#txt_Line_chief').val('".$result[csf('line_chief')]."');\n";

        echo "$('#txt_smv_adjustment').val('".$result[csf('smv_adjust')]."');\n";
		//echo "$('#cbo_smv_adjust_by').val('".$result[csf('smv_adjust_type')]."');\n";
		echo "$('#txt_active_machine').val('".$result[csf('active_machine')]."');\n";
		echo "$('#txt_target_hour').val('".$result[csf('target_per_hour')]."');\n"; 		
		echo "$('#txt_working_hour').val('".$result[csf('working_hour')]."');\n";
	
		echo "$('#txt_capacity').val('".$result[csf('capacity')]."');\n";
		echo "$('#txt_efficiency').val('".$result[csf('target_efficiency')]."');\n";
		echo "$('#cbo_company_name').attr('disabled',true);\n";
		echo "$('#cbo_location').attr('disabled',true);\n";
		echo "$('#cbo_floor').attr('disabled',true);\n";
		echo "$('#cbo_line_merge').attr('disabled',true);\n";
		echo "$('#cbo_line_no_sow').attr('disabled',true);\n";
		
		if($db_type==0)
		{
			$nameArray=sql_select("select shift_id, TIME_FORMAT( prod_start_time, '%H:%i' ) as prod_start_time,TIME_FORMAT( lunch_start_time, '%H:%i' ) as lunch_start_time, remarks from prod_resource_dtls_time where mast_dtl_id='$id' and status_active=1 and is_deleted=0");
		}
		else
		{
			$nameArray=sql_select("select shift_id, TO_CHAR(prod_start_time,'HH24:MI') as prod_start_time, TO_CHAR(lunch_start_time,'HH24:MI') as lunch_start_time, remarks from prod_resource_dtls_time where mast_dtl_id='$id' and status_active=1 and is_deleted=0");
		}
		
		$save_string='';
		foreach($nameArray as $row)
		{
			if($save_string=="") $save_string=$row[csf('shift_id')]."_".$row[csf('prod_start_time')]."_".$row[csf('lunch_start_time')]."_".$row[csf('remarks')];
			else $save_string.=",".$row[csf('shift_id')]."_".$row[csf('prod_start_time')]."_".$row[csf('lunch_start_time')]."_".$row[csf('remarks')];
		}
		
		echo "document.getElementById('save_string').value 					= '".$save_string."';\n";
		echo "set_button_status(1, permission, 'fnc_Ac_Production_Resource_Entry',1);";
  	}
 	exit();	
}

if ($action=="time_popup")
{
	echo load_html_head_contents("Production Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
?> 
	<script>
		
		function fnc_valid_time(val,field_id)
		{
			var val_length=val.length;
			if(val_length==2)
			{
				document.getElementById(field_id).value=val+":";
			}
			
			var colon_contains=val.contains(":");
			if(colon_contains==false)
			{
				if(val>23)
				{
					document.getElementById(field_id).value='23:';
				}
			}
			else
			{
				var data=val.split(":");
				var minutes=data[1];
				var str_length=minutes.length;
				var hour=data[0]*1;
				
				if(hour>23)
				{
					hour=23;
				}
				
				if(str_length>=2)
				{
					minutes= minutes.substr(0, 2);
					if(minutes*1>59)
					{
						minutes=59;
					}
				}
				
				var valid_time=hour+":"+minutes;
				document.getElementById(field_id).value=valid_time;
			}
		}
		
		function numOnly(myfield, e, field_id)
		{
			var key;
			var keychar;
			if (window.event)
				key = window.event.keyCode;
			else if (e)
				key = e.which;
			else
				return true;
			keychar = String.fromCharCode(key);

			// control keys
			if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27) )
			return true;
			// numbers
			else if ((("0123456789:").indexOf(keychar) > -1))
			{
				var dotposl=document.getElementById(field_id).value.lastIndexOf(":");
				if(keychar==":" && dotposl!=-1)
				{
					return false;
				}
				return true;
			}
			else
				return false;
		}  
	
		function fnc_close()
		{
			var save_string='';
			
			$("#tbl_list_search").find('tbody tr').each(function()
			{
				var shiftId=$(this).find('input[name="shiftId[]"]').val();
				var txt_prod_start_time=$(this).find('input[name="txt_prod_start_time[]"]').val();
				var txt_lunch_start_time=$(this).find('input[name="txt_lunch_start_time[]"]').val();
				var txtRemarks=$(this).find('input[name="txtRemarks[]"]').val();

				if(save_string=="")
				{
					save_string=shiftId+"_"+txt_prod_start_time+"_"+txt_lunch_start_time+"_"+txtRemarks;
				}
				else
				{
					save_string+=","+shiftId+"_"+txt_prod_start_time+"_"+txt_lunch_start_time+"_"+txtRemarks;
				}
			});
			
			$('#hide_save_string').val( save_string );
			
			parent.emailwindow.hide();
		}
    </script>

</head>

<body>
	<form name="searchdescfrm"  id="searchdescfrm">
		<fieldset style="width:620px;margin-left:10px">
            <input type="hidden" name="hide_save_string" id="hide_save_string" class="text_boxes" value="">
            <div style="width:620px;max-height:250px;" align="center">
                <table cellspacing="0" width="100%" cellpadding="0" class="rpt_table" rules="all" border="1" id="tbl_list_search">
                	<thead>
                    	<th width="100">Shift Name</th>
                        <th width="160">Production Start Time</th>
                        <th width="160">Lunch Start Time</th>
                        <th>Remarks</th>
                    </thead>
                    <tbody>
						<?
                            $i=1; $dataArray=array();
							if($save_string!="")
							{
								$save_string=explode(",",$save_string);
								foreach($save_string as $prevData)
								{
									$prevData=explode("_",$prevData);
									$shift_id=$prevData[0];
									$prod_start_time=$prevData[1];
									$lunch_start_time=$prevData[2];
									$remarks=$prevData[3];
									
									$dataArray[$shift_id]['pst']=$prod_start_time;
									$dataArray[$shift_id]['lst']=$lunch_start_time;
									$dataArray[$shift_id]['rk']=$remarks;
								}
							}
							else
							{
								if($db_type==0)
								{
									$nameArray=sql_select("select shift_id,TIME_FORMAT( prod_start_time, '%H:%i' ) as prod_start_time,TIME_FORMAT( lunch_start_time, '%H:%i' ) as lunch_start_time from variable_settings_production where company_name='$cbo_company_name' and variable_list=26 and status_active=1 and is_deleted=0");
								}
								else
								{
									$nameArray=sql_select("select shift_id, TO_CHAR(prod_start_time,'HH24:MI') as prod_start_time, TO_CHAR(lunch_start_time,'HH24:MI') as lunch_start_time from variable_settings_production where company_name='$cbo_company_name' and variable_list=26 and status_active=1 and is_deleted=0");	
								}
								foreach($nameArray as $row)
								{
									$dataArray[$row[csf('shift_id')]]['pst']=$row[csf('prod_start_time')];
									$dataArray[$row[csf('shift_id')]]['lst']=$row[csf('lunch_start_time')];
								}
							}
							//print_r($dataArray);
                            
                            foreach($shift_name as $id=>$name)
                            {
                                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                
                                $prod_start_time=$dataArray[$id]['pst'];
                                $lunch_start_time=$dataArray[$id]['lst'];
                                $remarks=$dataArray[$id]['rk'];
                                
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" align="center" valign="middle"> 
                                    <td><b><? echo $name; ?></b></td>
                                    <td>
                                    	<input class="text_boxes" type="text" style="width:140px" name="txt_prod_start_time[]" id="txt_prod_start_time<? echo $i; ?>" value="<? echo $prod_start_time; ?>" onBlur="fnc_valid_time(this.value,'txt_prod_start_time<? echo $i; ?>');" onKeyUp="fnc_valid_time(this.value,'txt_prod_start_time<? echo $i; ?>');" onKeyPress="return numOnly(this,event,this.id);" maxlength="8"/>
                                    </td>
                                    <td>
                                    	<input class="text_boxes" type="text" style="width:140px" name="txt_lunch_start_time[]" id="txt_lunch_start_time<? echo $i; ?>" value="<? echo $lunch_start_time; ?>" onBlur="fnc_valid_time(this.value,'txt_lunch_start_time<? echo $i; ?>');" onKeyUp="fnc_valid_time(this.value,'txt_lunch_start_time<? echo $i; ?>');" onKeyPress="return numOnly(this,event,this.id);" maxlength="8"/>
                                      	<input type="hidden"name="shiftId[]" id="shiftId<? echo $i; ?>" value="<? echo $id; ?>">
                                    </td>
                                    <td><input class="text_boxes" type="text" style="width:220px" name="txtRemarks[]" id="txtRemarks<? echo $i; ?>" value="<? echo $remarks; ?>"/></td>
                                </tr>
                            	<? 
                                $i++;
                            }
                        ?>
                    </tbody>
                </table>
                <table width="620">
                     <tr>
                        <td align="center" >
                            <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
	</form>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}




if($action=="sweing_production_start")
{
	if($db_type==0)
	{
		$nameArray=sql_select("select shift_id,TIME_FORMAT( prod_start_time, '%H:%i' ) as prod_start_time,TIME_FORMAT( lunch_start_time, '%H:%i' ) as lunch_start_time from variable_settings_production where company_name='$data' and variable_list=26 and status_active=1 and is_deleted=0");
	}
	else
	{
		$nameArray=sql_select("select shift_id, TO_CHAR(prod_start_time,'HH24:MI') as prod_start_time, TO_CHAR(lunch_start_time,'HH24:MI') as lunch_start_time from variable_settings_production where company_name='$data' and variable_list=26 and status_active=1 and is_deleted=0");	
	}
	$save_string='';
	foreach($nameArray as $row)
	{
		if($save_string=="") $save_string=$row[csf('shift_id')]."_".$row[csf('prod_start_time')]."_".$row[csf('lunch_start_time')];
		else $save_string.=",".$row[csf('shift_id')]."_".$row[csf('prod_start_time')]."_".$row[csf('lunch_start_time')];
	}
	
	echo "document.getElementById('save_string').value 					= '".$save_string."';\n";
	exit();	
}


?>