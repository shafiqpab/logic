<?
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create Process Wise Finish Fabric Rate Chart
Functionality	:
JS Functions	:
Created by		:	Md Mamun Ahmed Sagor
Creation date 	: 	10-03-2022
Updated by 		:	
Update date		:	
QC Performed BY	:
QC Date			:
Comments		: 
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
echo load_html_head_contents("Yarn Count Determination", "../../", 1, 1,$unicode,'','');
?>
<script type="text/javascript">

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	$(document).ready(function(){
		$( "#cbo_no_color" ).prop( "disabled", true );
		$( "#txt_coverage_from" ).prop( "disabled", true );
		$( "#txt_coverage_to" ).prop( "disabled", true );
		$( "#cbo_aop_type" ).prop( "disabled", true );
		$( "#cbo_aop_process_upto" ).prop( "disabled", true );
		
	});
	function fnc_fabric_count_determination( operation )
	{
		freeze_window(operation);
		var color_type=$('#cbo_color_type').val();
		if(color_type==5 || color_type==7 || color_type==45 || color_type==47 || color_type==48 || color_type==49 || color_type==54 || color_type==55 || color_type==56 || color_type==57 || color_type==67  || color_type==69){
			if (form_validation('cbo_no_color*txt_coverage_from*txt_coverage_to*cbo_aop_type*cbo_aop_process_upto','No Of Color*Coverage From*Coverage To*Aop Type*Aop Process')==false)
			{
				release_freezing();
				return;
			}
		}
		
		if (form_validation('cbo_company_name*txtcompone*cbo_fabric_source*cbo_body_part_id*cbo_body_part_type_id*cbo_color_type*cbo_party_type*cbo_party_name*txt_effective_date','Company Name*Fabric Name*Fabric Source*Body Part*Body Part Type*Party Type*Party Name*Rate BDT*Effective Date')==false)
		{
			release_freezing();
			return;
		}
		else
		{
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name*txtcompone*cbocompone*cbo_fabric_source*cbo_body_part_id*cbo_body_part_type_id*cbo_color_type*cbo_no_color*txt_coverage_from*txt_coverage_to*cbo_aop_type*cbo_aop_process_upto*cbo_party_type*cbo_party_name*cbo_uom*txt_rate_bdt*txt_effective_date*txt_rate_usd*txt_count_range_from*txt_count_range_to*cbo_color_range*update_id',"../../");
			 //alert(data);
			
			http.open("POST","requires/process_wise_finish_fabric_rate_chart_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_fabric_count_determination_reponse;
		}
	}
	
	function fnc_fabric_count_determination_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
			if(reponse[0]==50)
			{
				alert(reponse[1]);
				release_freezing();
				return;	
			}
			if(reponse[0]==15) 
			{ 
				 setTimeout('fnc_fabric_count_determination('+ reponse[1]+')',8000); 
			}
			else if(reponse[0]==10 || reponse[0]==11)
			{
				show_msg(trim(reponse[0]));
				release_freezing();
			}
			else
			{
				//alert(reponse[0]);
				show_msg(trim(reponse[0]));
				show_list_view($('#cbo_company_name').val(),'search_list_view','yarn_count_container','requires/process_wise_finish_fabric_rate_chart_controller','setFilterGrid("list_view",-1)');
				//reset_form('yarncountdetermination_1','','');
				fn_reset_form();
				
				set_button_status(0, permission, 'fnc_fabric_count_determination',1);
				release_freezing();
			}
		}
	}
	
	function fn_reset_form()
	{
		reset_form('yarncountdetermination_1','','','','','cbo_company_name');
		set_button_status(0, permission, 'fnc_fabric_count_determination',1);
	}	
		
	function openmypage_comp(inc)
	{
		var page_link="requires/process_wise_finish_fabric_rate_chart_controller.php?action=composition_popup&inc="+inc;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Composition Popup", 'width=650px,height=350px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
		var hidcompid=this.contentDoc.getElementById("hidcompid").value;
		var hidcompname=this.contentDoc.getElementById("hidcompname").value;
		$('#cbocompone').val(hidcompid);
		$('#txtcompone').val(hidcompname);
		}
	}	

function check_exchange_rate()
{
	var txt_rate_bdt=$('#txt_rate_bdt').val();
	var txt_effective_date = $('#txt_effective_date').val();
	var cbo_party_name = $('#cbo_party_name').val();
	var cbo_party_type = $('#cbo_party_type').val();
	var response=return_global_ajax_value( 2+"**"+txt_effective_date+"**"+cbo_party_name+"**"+txt_rate_bdt+"**"+cbo_party_type, 'check_conversion_rate', '', 'requires/process_wise_finish_fabric_rate_chart_controller');
	var response=response.split("_");
	$('#txt_rate_usd').val(response[1]);
}

function aop_diasble(){

	var color_type=$('#cbo_color_type').val();
	//  alert(color_type);
	if(color_type==5 || color_type==7 || color_type==45 || color_type==47 || color_type==48 || color_type==49 || color_type==54 || color_type==55 || color_type==56 || color_type==57 || color_type==67  || color_type==69){
		$( "#cbo_no_color" ).prop( "disabled", false );
		$( "#txt_coverage_from" ).prop( "disabled", false );
		$( "#txt_coverage_to" ).prop( "disabled", false );
		$( "#cbo_aop_type" ).prop( "disabled", false );
		$( "#cbo_aop_process_upto" ).prop( "disabled", false );
		
	}else{
		$( "#cbo_no_color" ).prop( "disabled", true );
		$( "#txt_coverage_from" ).prop( "disabled", true );
		$( "#txt_coverage_to" ).prop( "disabled", true );
		$( "#cbo_aop_type" ).prop( "disabled", true );
		$( "#cbo_aop_process_upto" ).prop( "disabled", true );
	}
}

	function fnc_process_rate(upid)
	{
	 	if(upid=="")
		{
			alert("Save Data First");
			return;
		}
		
		var page_link="requires/process_wise_finish_fabric_rate_chart_controller.php?action=process_wise_rate_popup&mst_id="+trim(upid);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Process Wise Rate Pop Up", 'width=450px,height=300px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var tot_rate=this.contentDoc.getElementById("tot_rate").value;
			document.getElementById('txt_rate_bdt').value=tot_rate;
			check_exchange_rate();
			show_list_view($('#cbo_company_name').val(),'search_list_view','yarn_count_container','requires/process_wise_finish_fabric_rate_chart_controller','setFilterGrid("list_view",-1)');
		}
	}
</script>

</head>	
<body onLoad="set_hotkey()">
    <div align="center" style="width:100%; position:relative; margin-bottom:5px; margin-top:5px">
		<? echo load_freeze_divs ("../../",$permission);  ?>
        <form name="yarncountdetermination_1" id="yarncountdetermination_1" autocomplete="off">
            <fieldset style="width:1000px;">
                <legend>Process Wise Finish Fabric Rate Chart</legend>
                <table width="100%" border="0" cellpadding="0" cellspacing="2">
                    <tr>
						<td class="must_entry_caption">Company Name</td>
						<td><?=create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/process_wise_finish_fabric_rate_chart_controller', document.getElementById('cbo_party_type').value+'_'+this.value, 'load_drop_down_party', 'party_id' );" ); ?></td>
                        <td class="must_entry_caption">Fabric name</td>

                        <td>
							<input type="text" id="txtcompone"  name="txtcompone"  class="text_boxes" style="width:120px" value="" readonly placeholder="Browse" onDblClick="openmypage_comp(1);" />
                            <input type="hidden" id="cbocompone"  name="cbocompone" class="text_boxes" style="width:50px" value="" /></td>
							<input type="hidden" id="update_id"  name="update_id" class="text_boxes" style="width:50px" value="" /></td>
						<td class="must_entry_caption">Fabric Source</td>
                        <td><?=create_drop_down( "cbo_fabric_source",130, $fabric_source,"", 1, "-- Select --", '', "",$disabled,"" ); ?></td>
                    </tr>
                    <tr>
						<td class="must_entry_caption">Body part</td>
						<td><?=create_drop_down( "cbo_body_part_id", 130, "select id, body_part_full_name from lib_body_part where is_deleted=0", "id,body_part_full_name", 1, "-- Select --", "", "get_php_form_data(this.value,'load_drop_down_body_type','requires/process_wise_finish_fabric_rate_chart_controller' );","","" );?> </td>
						<td class="must_entry_caption">Body Part Type</td>
						<td id="body_type_id"><? echo create_drop_down( "cbo_body_part_type_id", 130, $body_part_type, "", 1, "-- Select --", "", "","1","" ); ?></td>
						<td class="must_entry_caption">Color Type</td>
						<td onchange="aop_diasble()"><?=create_drop_down( "cbo_color_type", 130, $color_type, "", 1, "-- Select --", "", "","","" ); ?></td>
                    </tr>
                    <tr>
						<td>Count Range</td>
						<td><input type="text" id="txt_count_range_from"  name="txt_count_range_from" class="text_boxes" style="width:52px" value="" placeholder="From"/><input type="text" id="txt_count_range_to"  name="txt_count_range_to" class="text_boxes" style="width:52px" value=""  placeholder="To"/></td>
                        <td>Color Range</td>
                        <td><? echo create_drop_down( "cbo_color_range", 130, $color_range,"", 1, "-- Select --", '', "",$disabled,"" ); ?></td>
						<td>No. Of Color</td>
						<td><? 
							$no_color_arr=array(1=>"1 - 3",2=>"1 - 5",3=>"4 - 6",4=>"7 - 12");
						echo create_drop_down( "cbo_no_color", 130, $no_color_arr,"", 1, "-- Select --", '', "",$disabled,"" ); ?></td>
                    </tr>
					<tr>
						<td>Coverage%</td>
                        <td><input type="text" id="txt_coverage_from"  name="txt_coverage_from" class="text_boxes" style="width:52px" value="" placeholder="From" /><input type="text" id="txt_coverage_to"  name="txt_coverage_to" class="text_boxes" style="width:52px" value="" placeholder="To"/></td>
                    	<td>AOP type</td>
                        <td><? echo create_drop_down( "cbo_aop_type", 130, $conversion_cost_head_array,"", 1, "-- Select --", '', "",$disabled,"" ); ?></td>
						<td>AOP Process Upto</td>
                        <td><? 
							$aop_process_arr=array(1=>"Compacting",2=>"Sanforizing");
						echo create_drop_down( "cbo_aop_process_upto", 130, $aop_process_arr,"", 1, "-- Select --", '', "",$disabled,"" ); ?></td>
                    </tr>
					<tr>
						<td class="must_entry_caption">Party Type</td>
                        <td><? 
						$blank_arr=array();
						$part_type_arr=array(0=>"-- Select --",1=>"Within Group",2=>"In-Bound");
						echo create_drop_down( "cbo_party_type", 130, $part_type_arr,"", 0, "-- Select --", '', "load_drop_down( 'requires/process_wise_finish_fabric_rate_chart_controller', this.value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_party', 'party_id' );",$disabled,"" ); ?></td>
                    	<td class="must_entry_caption">Party Name</td>
                        <td id="party_id"><? echo create_drop_down( "cbo_party_name", 130, $blank_arr,"", 1, "-- Select --", '', "",$disabled,"" ); ?></td>
						<td>UOM</td>
                        <td><? echo create_drop_down( "cbo_uom", 130,  $unit_of_measurement,"", 1, "-- Select --", '', "",$disabled,"1,12,23,27" ); ?></td>
                       
                    </tr>
					<tr>
						<td class="must_entry_caption">Rate (BDT)</td>
                        <td><input class="text_boxes_numeric" type="text" onChange="check_exchange_rate();" style="width:120px;" name="txt_rate_bdt" id="txt_rate_bdt" value="" onClick="fnc_process_rate(document.getElementById('update_id').value);" readonly placeholder="Browse"/></td>
                    	<td class="must_entry_caption">Effective Date</td>
                        <td><input class="datepicker" type="text"  style="width:120px;" name="txt_effective_date" id="txt_effective_date" onChange="check_exchange_rate()" placeholder="Date"/></td>
						<td class="must_entry_caption">Rate (USD)</td>
                        <td><input class="text_boxes_numeric" type="text" style="width:120px;"  name="txt_rate_usd" id="txt_rate_usd" disabled /></td>
                    </tr>
                </table>
                <table width="100%" border="" cellpadding="0" cellspacing="0"  rules="all">
                    <tr>
                        <td colspan="6" align="center" class="button_container"><?=load_submit_buttons( $permission, "fnc_fabric_count_determination", 0,0 ,"fn_reset_form()",1); ?> 
                        </td>		
                    </tr>	
                </table>
            </fieldset>
        </form>	
		<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
        
        <div id="yarn_count_container">
			<?
				$composition_arr=array();
				$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
				$user_arr=return_library_array( "select user_full_name,id from user_passwd", "id", "user_full_name");
				$lib_group_short=return_library_array( "select id,group_short_name from lib_group where id=1 and status_active=1", "id", "group_short_name");
			
				$body_type_arr=return_library_array( "select id,body_part_full_name from lib_body_part where  status_active=1", "id", "body_part_full_name");
				$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
				$group_short_name=$lib_group_short[1];
				
				$part_type_arr=array(1=>"Within Group",2=>"In-Bound");
				
				$sql="select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,b.copmposition_id,b.percent,b.count_id,b.type_id,a.id,b.id as bid from  lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id,b.id";
				$data_array=sql_select($sql);
				$sysCodeArr=array();
				if (count($data_array)>0)
				{
					foreach( $data_array as $row )
					{
						if(array_key_exists($row[csf('id')],$composition_arr))
						{
							$composition_arr[$row[csf('id')]]=$row[csf('construction')].','.$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
						}
						else
						{
							$composition_arr[$row[csf('id')]]=$row[csf('construction')].','.$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
						}
						$sys_code=$group_short_name.'-'.$row[csf('id')];
						$sysCodeArr[$row[csf('id')]]=$sys_code;
					}
				}
				//print_r($sysCodeArr);				
				$sql="select id, company_id, fabric_description, fabric_source, body_part_id, body_part_type, color_type, party_type, party_name, uom,rate_bdt, rate_usd, aop_type, aop_process_upto, no_of_color, effective_date, coverage_range_from, coverage_range_to, count_range_from, count_range_to, color_range from process_finish_fabric_rate_chat where is_deleted=0 order by id DESC";				
				$arr=array (0=>$company_arr,1=>$composition_arr,2=>$fabric_source,3=>$body_type_arr,4=>$body_part_type,5=>$color_type,8=>$color_range,9=>$no_color_arr,12=>$conversion_cost_head_array,13=>$aop_process_arr,14=>$part_type_arr,15=>$company_arr,18=>$unit_of_measurement);
				echo  create_list_view ( "list_view", "Company Name,Fabric Name,Fabric Source,Body part,Body Part Type,Color Type,Count Range From,Count Range To,Color Range,No. Of Color,Coverage % from,Coverage % to,AOP Type,AOP Process Upto,Party Type,Party Name,Rate BDT,Rate USD,UOM,Effect Date", "100,200,70,80,70,80,50,50,50,50,50,50,50,50,60,60,60,80,60,70","1470","350",0, $sql, "get_php_form_data", "id", "'load_php_data_to_form'",1, "company_id,fabric_description,fabric_source,body_part_id,body_part_type,color_type,0,0,color_range,no_of_color,0,0,aop_type,aop_process_upto,party_type,party_name,0,0,uom", $arr , "company_id,fabric_description,fabric_source,body_part_id,body_part_type,color_type,count_range_from,count_range_to,color_range,no_of_color,coverage_range_from,coverage_range_to,aop_type,aop_process_upto,party_type,party_name,rate_bdt,rate_usd,uom,effective_date", "requires/process_wise_finish_fabric_rate_chart_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0,0,2,2,0,0,2,2,0,0,0,0,2,2,0,3') ;
				exit();
            ?>
        </div>
    </div>
</body>
</html>
