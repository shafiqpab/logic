<?
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create Knit Consumption Entry [CAD] For LA Costing
Functionality	:
JS Functions	:
Created by		:	Kausar
Creation date 	: 	12-09-2023
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
echo load_html_head_contents("Cons. la Costing-Knit","../../", 1, 1, $unicode,1,'');
$cons_for_arr = array(1=>"Merketing",2=>"Budget",3=>"Production");
?>
<script type="text/javascript">
    var permission='<? echo $permission; ?>';
    function open_consumptionpopup()
    {
       var title = "Consumption Popup";
        var page_link='requires/consumption_la_costing_controller.php?action=generate_cad_la_consting';
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1150,height=480px,center=1,resize=1,scrolling=0','../')
        emailwindow.onclose=function(){
            var theform=this.contentDoc.forms[0];
            var system_id=this.contentDoc.getElementById("hidden_system_number").value;
            if (system_id.value!=""){
                get_php_form_data(system_id, "populate_data_from_consumption", "requires/consumption_la_costing_controller");
                fnc_show_fabrication_list();
            }
        } 
    }
	function check_buyer_inquery()
	{
        var title = "Master Style Ref.";
        var page_link='requires/consumption_la_costing_controller.php?action=generate_buyer_inquery';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1150,height=480px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function(){
			var theform=this.contentDoc.forms[0];
            var inquery_data=this.contentDoc.getElementById("hidden_issue_number").value;
			if (inquery_data.value!=""){
                inquery_data_arr = inquery_data.split("_");
                get_php_form_data(inquery_data_arr[1], "populate_data_from_data", "requires/consumption_la_costing_controller");
				fnc_show_fabrication_list();
			}
		}
	}
    function fnc_show_fabrication_list(){
        var data="action=show_fabrication_list"+get_submitted_data_string('txt_style_ref*inquery_id*txt_fabrication*update_id',"../../");
        http.open("POST","requires/consumption_la_costing_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fnc_show_fabrication_list_reponse;
    }
    function fnc_show_fabrication_list_reponse(){
        if(http.readyState == 4){
            document.getElementById('fabric_details_breakdown').innerHTML=http.responseText;
        }
    }
    function open_body_part_popup(i){
        var fabricusageid=document.getElementById('fabricusageid_'+i).value;
        var page_link='requires/pre_cost_entry_controller_v2.php?action=body_part_popup&fabric_usage_id='+fabricusageid;
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Fabric Body Part', 'width=480px,height=450px,center=1,resize=1,scrolling=0','../');
        emailwindow.onclose=function()
        {
            var id=this.contentDoc.getElementById("gid");
            var name=this.contentDoc.getElementById("gname");
            var type=this.contentDoc.getElementById("gtype");
            document.getElementById('fabricusage_'+i).value=name.value;
            document.getElementById('fabricusageid_'+i).value=id.value;
        }
    }
    function fnc_consumption_entry(operation)
    {
        //freeze_window(operation);
        var data_all="";
        if (form_validation('txt_consumption_date*txt_style_ref*inquery_id*cbo_cons_for','Consumption Date*Master Style Ref*Inquiry Data*Cons For')==false){
            release_freezing();
            return;
        }
        else{
            var check_same_cons = return_global_ajax_value(document.getElementById('inquery_id').value+'**'+document.getElementById('cbo_cons_for').value+'**'+document.getElementById('update_id').value, 'check_same_cons', '', 'requires/consumption_la_costing_controller');
            if(check_same_cons==1){
                alert("Same Style Ref and Cons For Not Allowed");
                release_freezing();
                return;
            }
            data_all=data_all+get_submitted_data_string('txt_consumption_date*inquery_id*txt_fabrication*txt_merch_style*txt_style_desc*txt_pattern_master*txt_bom_no*txt_comments*cbo_company_name*update_id*txt_mclastmod_date*cbo_cons_for',"../../");
            var row_num=$('#fabric_dtls_tbl tr').length-2;
            for (var i=1; i<=row_num; i++){
                data_all=data_all+get_submitted_data_string('fabricusageid_'+i+'*updateiddtls_'+i+'*yarncountid_'+i,"../../",i);
            }
        }
        var data="action=save_update_delete&operation="+operation+'&total_row='+row_num+data_all;
        http.open("POST","requires/consumption_la_costing_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fnc_consumption_entry_reponse;
    }
    function fnc_consumption_entry_reponse()
    {
        if(http.readyState == 4){
            var reponse=trim(http.responseText).split('**');
            if(reponse[0]==0 || reponse[0]==1)
            {
                show_msg(trim(reponse[0]));
                document.getElementById('txt_system_id').value=reponse[1];
                document.getElementById('update_id').value=reponse[2];
                $("#txt_style_ref").attr("disabled",true);
                $("#txt_style_ref").attr("ondblclick", "").unbind("click");
                fnc_show_fabrication_list();
                release_freezing();
                //set_button_status(1, permission, 'fnc_consumption_entry',1);
            }
            if(reponse[0]==2)
            {
                show_msg(trim(reponse[0]));
                reset_form('consumption_form','','');
                $("#txt_style_ref").attr("disabled",false);
                $('#txt_style_ref').attr('ondblclick','check_buyer_inquery()');
                $("#fabric_details_breakdown").html("");
                release_freezing();
            }
            if(reponse[0]==10){
                show_msg(trim(reponse[0]));
                release_freezing();
            }
            
        }
    }
    function generate_report()
    {
        if (form_validation('txt_system_id','System No')==false){
            return;
        }
        freeze_window(5);
        var report_title="Consumption Entry [CAD] For LA Costing";
        var data="action=consumption_report"+get_submitted_data_string('update_id',"../../")+'&report_title='+report_title;
        http.open("POST","requires/consumption_la_costing_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = generate_report_reponse;
    }
    function generate_report_reponse(){
        if(http.readyState == 4){
            var file_data=http.responseText.split("****");
            //$('#pdf_file_name').html(file_data[1]);
            $('#data_panel').html(file_data[0]);
            var w = window.open("Surprise", "_blank");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
            '<html><head><link rel="stylesheet" href="../../css/prt.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
            d.close();
            release_freezing();
        }
        else{
            release_freezing();
        }
    }	
	function sendMail()
	{
        var alert_msg='';
        if($('#text_mail_send_date').val()){alert_msg='Last Mail Send Date:'+$('#text_mail_send_date').val();}
		if(confirm(alert_msg+'\n Do you want to send mail?')==0){
            return false
        }
		if (form_validation('update_id','System Id')==false)
		{
			return;
		}
		
		var update_id=$('#update_id').val();
		
		var data="update_id="+update_id;
 		freeze_window(operation);
		http.open("POST","../../auto_mail/woven/consumption_entry_cad_for_la_costing_auto_mail.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function fnc_btb_mst_reponse()
		{
			if(http.readyState == 4)
			{
				var reponse=trim(http.responseText);
				alert(reponse);
				release_freezing();
			}
		}

	}


    function call_print_button_for_mail(mail,mail_body,type)
	{
        var update_id=$('#update_id').val();
		var ret_data=return_global_ajax_value(update_id+'__'+mail+'__'+mail_body, 'auto_mail', '', 'requires/consumption_la_costing_controller');
		alert(ret_data);
	}

    function remarks_popup(i)
    {
        var txtdescription=document.getElementById('txtremarks_'+i).value;
        var data=txtdescription
        var title = 'Remarks';
        var page_link = 'requires/consumption_la_costing_controller.php?data='+data+'&action=remarks_popup';

        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=470px,height=200px,center=1,resize=1,scrolling=0','../');
        emailwindow.onclose=function()
        {
            var description=this.contentDoc.getElementById("description");
            $('#txtremarks_'+i).val(description.value);
        }
    }
    function fnc_load_fabric_dtls(up_id){
        if(up_id==0)
        {
            alert("Please Save Master Part");
            return;
        }
        var list_view_tr = return_global_ajax_value( up_id, 'load_php_dtls_form', '', 'requires/consumption_la_costing_controller');
        if(list_view_tr=="" || list_view_tr==0)
        {
            $('#fabric_dtls_id').val(up_id);
            $("#fabric_info_details_breakdown").find("tr:gt(0)").remove();
            reset_form('consumption_dtls_form','','','','','fabric_dtls_id');
            set_button_status(0, permission, 'fnc_consumption_fabric_entry',2);
            return;
        }
        else{
            $('#fabric_dtls_id').val(up_id);
            $("#fabric_info_details_breakdown").html('');
            $("#fabric_info_details_breakdown").append(list_view_tr);
            set_button_status(1, permission, 'fnc_consumption_fabric_entry',2);
            return;
        }
        
    }

    function fnc_consumption_fabric_entry(operation)
    {
        var fabric_id = $('#fabric_dtls_id').val();
        if(fabric_id==0){
            alert("Please Select Any Fabric");
            return;
        }
        freeze_window(operation);
        var data_all="";
        data_all=data_all+get_submitted_data_string('fabric_dtls_id',"../../");
        var row_num=$('#fabric_info_details_breakdown tr').length;
        for (var i=1; i<=row_num; i++){            
            if (form_validation('fabricfullwidth_'+i+'*fabriccutablewidth_'+i+'*txtbundleqty_'+i+'*txtbundleconsyds_'+i+'*txtconsydsdzn_'+i,'Full Width*Cuttable Width*Bundles Qty*Bundles Cons*Fabric Cons Yds/Dzn')==false){
                release_freezing();
                return;
            }
            else{
                data_all=data_all+get_submitted_data_string('fabricfullwidth_'+i+'*fabriccutablewidth_'+i+'*txteffiper_'+i+'*txtsizeratio_'+i+'*txtbundleqty_'+i+'*txtbundleconsyds_'+i+'*txtconsydsdzn_'+i+'*txtwastageper_'+i+'*txtfinalcons_'+i+'*shrinkagelength_'+i+'*shrinkagewidth_'+i+'*nestedpieces_'+i+'*txtremarks_'+i+'*updatefabricdtlsid_'+i,"../../",i);
            }
        }
        var data="action=save_update_delete_fabric_dtls&operation="+operation+'&total_row='+row_num+data_all;
        http.open("POST","requires/consumption_la_costing_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fnc_consumption_fabric_entry_reponse;
    }
    function fnc_consumption_fabric_entry_reponse()
    {
        if(http.readyState == 4)
        {
            var reponse=trim(http.responseText).split('**');
            if(reponse[0]==0 || reponse[0]==1)
            {
                show_msg(trim(reponse[0]));
                fnc_load_fabric_dtls(reponse[1]);
                release_freezing();
            }
            if(reponse[0]==10){
                show_msg(trim(reponse[0]));
                release_freezing();
            }
        }
    }
    function calculate_cons(i)
    {
        var bundles_qty = $("#txtbundleqty_"+i).val()*1;
        var bundles_cons = $("#txtbundleconsyds_"+i).val()*1;
		var wastageper = $("#txtwastageper_"+i).val()*1;
        var fabric_cons = '';
        if(bundles_qty !='' && bundles_cons!='')
        {
            fabric_cons = bundles_cons/bundles_qty*12;
            $("#txtconsydsdzn_"+i).val(number_format(fabric_cons,4));            
        }
        else{
           $("#txtconsydsdzn_"+i).val(''); 
        }
		
		var consydsdzn=$("#txtconsydsdzn_"+i).val()*1;
		
		if(wastageper==0) $("#txtfinalcons_"+i).val( consydsdzn );
		else 
		{
			var westqty=consydsdzn+(consydsdzn*(wastageper/100));
			//alert(westqty)
			$("#txtfinalcons_"+i).val(number_format(westqty,4)); 
		}
    }
	
    function add_break_down_tr(i)
    {
        var row_num=$('#fabric_info_details_breakdown tr').length;
        if (row_num!=i)
        {
            return false;
        }
        else
        {
            i++;         
             $("#fabric_info_details_breakdown tr:last").clone().find("input").each(function() {
                $(this).attr({
                  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
                  //'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i },
                  'value': function(_, value) { return value }              
                });  
              }).end().appendTo("#fabric_info_details_breakdown");
            $('#fabricfullwidth_'+i).val("");
            $('#fabriccutablewidth_'+i).val("");
            $('#txteffiper_'+i).val("");
            $('#txtsizeratio_'+i).val("");
            $('#txtbundleqty_'+i).val("");
            $('#txtbundleconsyds_'+i).val("");
            $('#txtconsydsdzn_'+i).val("");
			
			$('#txtwastageper_'+i).val("");
			$('#txtfinalcons_'+i).val("");
			
            $('#shrinkagelength_'+i).val("");
            $('#shrinkagewidth_'+i).val("");
            $('#nestedpieces_'+i).val("");
            $('#txtremarks_'+i).val("");
            $('#updatefabricdtlsid_'+i).val("");

            $('#txtbundleqty_'+i).removeAttr("onchange").attr("onchange","calculate_cons("+i+");");
            $('#txtbundleconsyds_'+i).removeAttr("onchange").attr("onchange","calculate_cons("+i+");");
            $('#txtremarks_'+i).removeAttr("ondblclick").attr("ondblclick","remarks_popup("+i+");");
            $('#increase_'+i).removeAttr("onclick").attr("onclick","add_break_down_tr("+i+");");
            $('#decrease_'+i).removeAttr("onclick").attr("onclick","fn_deleteRow("+i+");");
            set_all_onclick();
        }
    }

    function fn_deleteRow(rowNo)
    {
        if(rowNo !=1)
        {
            var index=rowNo-1                 
            $("#fabric_info_details_breakdown tr:eq("+index+")").remove();
            var numRow = $('#fabric_info_details_breakdown tr').length;
            for(i = rowNo; i <= numRow;i++)
            {
                var row_new = i-1;
                $("#fabric_info_details_breakdown tr:eq("+row_new+")").find("input").each(function() {
                    $(this).attr({
                        'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
                        //'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i },
                        'value': function(_, value) { return value }
                    });
                $('#txtbundleqty_'+i).removeAttr("onchange").attr("onchange","calculate_cons("+i+");");
                $('#txtbundleconsyds_'+i).removeAttr("onchange").attr("onchange","calculate_cons("+i+");");
                $('#increase_'+i).removeAttr("value").attr("value","+");
                $('#decrease_'+i).removeAttr("value").attr("value","-");
                $('#increase_'+i).removeAttr("onclick").attr("onclick","add_break_down_tr("+i+");");
                $('#decrease_'+i).removeAttr("onclick").attr("onclick","fn_deleteRow("+i+");");
                });
            }  
        }        
    }
    var row_color=new Array();
    var lastid='';
    function change_color_tr(v_id,e_color)
    {
        if(lastid!='') $('#tr_'+lastid).attr('bgcolor',row_color[lastid])

            if( row_color[v_id]==undefined ) row_color[v_id]=$('#tr_'+v_id).attr('bgcolor');

        if( $('#tr_'+v_id).attr('bgcolor')=='#FF9900')
            $('#tr_'+v_id).attr('bgcolor',row_color[v_id])
        else
            $('#tr_'+v_id).attr('bgcolor','#FF9900')

        lastid=v_id;
    }	
</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",$permission);  ?>
        <fieldset style="width:970px;">
            <legend>Consumption Entry [CAD] For LA Costing</legend>
            <form name="consumption_form" id="consumption_form" autocomplete="off">
                <table width="960" cellspacing="2" cellpadding="0" border="0">
                    <tr>
                        <td colspan="4" align="right">System NO.</td>
                        <td colspan="5">
                            <input style="width:140px;" type="text" title="Double Click to Search" onDblClick="open_consumptionpopup();" class="text_boxes" placeholder="Browse" name="txt_system_id" id="txt_system_id" readonly />
                            <input type="hidden" name="update_id" id="update_id" />
                        </td>
                    </tr>
                    <tr>
                        <td width="90" class="must_entry_caption">Company Name</td>
                        <td width="140"><? echo create_drop_down( "cbo_company_name", 120, "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "Select Company", $selected, "",1); ?></td>                        
                        <td width="90" class="must_entry_caption">Master Style Ref</td>
                        <td width="140">
                            <input class="text_boxes" type="text" style="width:110px" placeholder="Browse"  name="txt_style_ref" id="txt_style_ref" onDblClick="check_buyer_inquery();"/>
                            <input type="hidden" name="inquery_id" id="inquery_id" value="">
                            <input type="hidden" name="txt_fabrication" id="txt_fabrication" value="">
                        </td> 
                    	<td width="90" class="must_entry_caption">Consumption Date</td>
                        <td width="140"><input name="txt_consumption_date" style="width:110px"  id="txt_consumption_date" placeholder="Select Date" class="datepicker" type="text" value="<? echo date("d-m-Y")?>" /></td> 
                        <td width="90">Merch Style</td>
                        <td><input class="text_boxes" type="text" style="width:110px" placeholder="Write"  name="txt_merch_style" id="txt_merch_style"/></td>                     
                    </tr>
                    <tr>
                    	<td>Buyer Name</td>
                        <td><input name="txt_buyer_name" class="text_boxes" style="width:110px"  id="txt_buyer_name" type="text" value="" disabled="" /></td>
                        <td>Season</td>
                        <td><input name="txt_season" class="text_boxes" style="width:110px"  id="txt_season" type="text" value="" disabled="" /></td>
						<td>Season Year</td>
						<td><input name="txt_season_year" class="text_boxes" style="width:110px"  id="txt_season_year" type="text" value="" disabled="" /></td>
                        <td>Brand</td>
                        <td><input name="txt_brand_name" class="text_boxes" style="width:110px"  id="txt_brand_name" type="text" value="" disabled="" /></td>
                    </tr>
                    <tr>
                    	<td>MC Last Mod</td>
                        <td><input name="txt_mclastmod_date" style="width:110px"  id="txt_mclastmod_date" placeholder="Select Date" class="datepicker" type="text"  /></td>
                        <td>Style Desc.</td>
                        <td><input class="text_boxes" type="text" placeholder="Write" style="width:110px"  name="txt_style_desc" id="txt_style_desc" disabled="" /></td>
                        <td>Pattern Master</td>
                        <td><input class="text_boxes" type="text" placeholder="Write" style="width:110px"  name="txt_pattern_master" id="txt_pattern_master"/></td>
                        <td>BOM NO.</td>
                        <td><input class="text_boxes" type="text" placeholder="Write" style="width:110px"  name="txt_bom_no" id="txt_bom_no"/></td>
                    </tr>
                    <tr>
                        <td>Body/Wash Color</td>
                        <td><input class="text_boxes" type="text" style="width:110px"  name="txt_style_desc" id="txt_boby_wash_color" disabled="" /></td>
                        <td class="must_entry_caption">Cons For</td>
                        <td><?=create_drop_down( "cbo_cons_for", 120, $cons_for_arr, "", 1, "-- Select Cons --", $selected, "", "", "" ); ?></td>
                        <td>Comments</td>
                        <td colspan="3">
                            <input class="text_boxes" style="width:345px" type="text" placeholder="Write" name="txt_comments" id="txt_comments"/>
                            <input type="hidden" name="text_mail_send_date" id="text_mail_send_date"/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="8" align="center"><input type="button" class="image_uploader" style="width:150px" value=" ADD File" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'cad_entry', 2 ,1)">
                        <input type="hidden" class="image_uploader" style="width:150px" value="CLICK TO ADD/VIEW IMAGE" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'cad_entry', 0 ,1)"></td>
                    </tr>
                </table>
                <legend>Fabric Info</legend>
                <div id="fabric_details_breakdown"></div>
                </form>
                <form name="consumption_dtls_form" id="consumption_dtls_form" autocomplete="off">
                    <legend>Fabric Details</legend>
                    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="960" class="rpt_table" id="fabric_info_dtls_tbl">
                        <thead>
                            <tr>
                                <th width="70" rowspan="2" style="color: blue;">Full Width</th>
                                <th width="70" rowspan="2" style="color: blue;">Cuttable Width</th>
                                <th width="60" rowspan="2">Efficiency &percnt;</th>
                                <th width="120" rowspan="2">Size Ratio</th>
                                <th width="50" rowspan="2" style="color: blue;">Bundles Qty.</th>
                                <th width="50" rowspan="2" style="color: blue;">Bundles Cons KG</th>
                                <th width="50" rowspan="2" style="color: blue;">Cons KG / Dzn</th>
                                <th width="50" rowspan="2">Wastage %</th>
                                <th width="50" rowspan="2">Final Cons [KG]</th>
                                <th width="80" colspan="2">Shrinkage</th>
                                <th width="40" rowspan="2">Nested Pieces</th>
                                <th width="150" rowspan="2">Comments</th>
                                <th rowspan="2">&nbsp;</th>
                            </tr>
                            <tr>
                                <th width="40">L &percnt;</th>
                                <th width="40">W &percnt;</th>
                            </tr>
                        </thead>
                        <tbody id="fabric_info_details_breakdown">                
                            <tr>
                                <td><input style="width: 60px" class="text_boxes" type="text" name="fabricfullwidth_1" id="fabricfullwidth_1" placeholder="Write"></td>
                                <td><input style="width: 60px" class="text_boxes" type="text" name="fabriccutablewidth_1" id="fabriccutablewidth_1" placeholder="Write"></td>
                                <td><input style="width: 50px" class="text_boxes_numeric" type="text" name="txteffiper_1" id="txteffiper_1" placeholder="Write"></td>
                                <td><input style="width: 110px" class="text_area" type="text" name="txtsizeratio_1" id="txtsizeratio_1" placeholder="Write"></td>
                                <td><input style="width: 40px" class="text_boxes_numeric" type="text" name="txtbundleqty_1" id="txtbundleqty_1" placeholder="Write" onChange="calculate_cons(1);"></td>
                                <td><input style="width: 40px" class="text_boxes_numeric" type="text" name="txtbundleconsyds_1" id="txtbundleconsyds_1" placeholder="Write" onChange="calculate_cons(1);"></td>
                                <td><input style="width: 40px" class="text_boxes_numeric" type="text" name="txtconsydsdzn_1" id="txtconsydsdzn_1" readonly></td>
                                
                                <td><input style="width: 40px" class="text_boxes_numeric" type="text" name="txtwastageper_1" id="txtwastageper_1" placeholder="Write" onChange="calculate_cons(1);"></td>
                                <td><input style="width: 40px" class="text_boxes_numeric" type="text" name="txtfinalcons_1" id="txtfinalcons_1" readonly></td>
                                
                                <td><input style="width: 30px" class="text_boxes_numeric" type="text" name="shrinkagelength_1" id="shrinkagelength_1" placeholder="Write"></td>
                                <td><input style="width: 30px" class="text_boxes_numeric" type="text" name="shrinkagewidth_1" id="shrinkagewidth_1" placeholder="Write"></td>
                                <td><input style="width: 30px" class="text_boxes" type="text" name="nestedpieces_1"  id="nestedpieces_1" placeholder="Write"></td>
                                <td>
                                    <input style="width: 140px" class="text_boxes" type="text" name="txtremarks_1" id="txtremarks_1" placeholder="Write" onDblClick="remarks_popup(1);">
                                    <input type="hidden" id="updatefabricdtlsid_1" value="">
                                </td>
                                <td>
                                    <input type="button" id="increase_1" name="increase_1" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(1);" />
                                    <input type="button" id="decrease_1" name="decrease_1" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="960" class="rpt_table">       
                        <tr>
                            <td align="center" valign="middle" style="max-height:960px; min-height:15px;" id="fabric_dtls_save_update">
                                <? echo load_submit_buttons( $permission, "fnc_consumption_fabric_entry", 0,0 ,"reset_form('consumption_dtls_form','','')",2); ?>
                                <input type="hidden" name="fabric_dtls_id" id="fabric_dtls_id" value="">
                            </td>
                       </tr>
                    </table>
                </form>
        	</fieldset>
        </div>
        <div style="display:none" id="data_panel"></div>
    </body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>