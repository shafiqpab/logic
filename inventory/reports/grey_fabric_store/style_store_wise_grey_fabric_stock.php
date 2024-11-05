<?
/*-------------------------------------------- Comments
Purpose			      : 	This form will create File Wise Grey Fabrics Stock Report

Functionality	    :
JS Functions	    :
Created by		    :	
Creation date     : 	
Updated by 		    :
Update date		    :
QC Performed BY	  :
QC Date			      :
Comments		      :
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

  require_once('../../../includes/common.php');
  extract($_REQUEST);
  $_SESSION['page_permission']=$permission;

  echo load_html_head_contents("File Wise Grey Fabrics Stock Report","../../../", 1, 1, $unicode,1,1);
  ?>
  <script>
   var permission='<? echo $permission; ?>';
   var tableFilters = {
    col_operation: {
      id: ["value_tot_recv_only","value_tot_iss_rtn","value_tot_recv","value_tot_iss","value_tot_stock"],
      col: [21,22,23,24,25],
      operation: ["sum","sum","sum","sum","sum"],
      write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
    }
  }
  if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

  function generate_report(rpt_type)
  {
    if( form_validation('cbo_company_id*txt_date_from','Company Name*Date')==false )
    {
     return;
   }

   var txt_job_no = $("#txt_job_no").val().trim();
   var txt_file_no = $("#txt_file_no").val().trim();
   var txt_ref_no = $("#txt_ref_no").val().trim();
   var txt_booking_no = $("#txt_booking_no").val().trim();
   var txt_fso_no = $("#txt_fso_no").val().trim();
   var cbo_buyer_id = $("#cbo_buyer_id").val();
   var cbo_shiping_status = $("#cbo_shiping_status").val();
   var cbo_value_with = $("#cbo_value_with").val();
   var cbo_string_search_type = $("#cbo_string_search_type").val();

  if(rpt_type != 12 )
  {
     if(cbo_buyer_id == 0 && txt_job_no =="" && txt_booking_no =="" && txt_file_no =="" && txt_ref_no =="" && txt_fso_no =="")
     {
      alert("Please select job no.");
      $("#txt_job_no").focus();
      return;
    }

    if((txt_job_no !="" || txt_booking_no !="" || txt_file_no !="" ||txt_ref_no !="") && txt_fso_no !="")
    {
      alert("Please select either job reference or sales order no");
      $("#txt_fso_no").val("");
      $("#hdn_fso_id").val("");
      $("#txt_fso_no").focus();
      return;
    }
  }
  else
  {
    if(cbo_buyer_id == 0 && txt_job_no =="" && txt_booking_no =="" && txt_file_no =="" && txt_ref_no =="" && txt_fso_no =="" )
    {
        alert("Please select Buyer.");
        $("#cbo_buyer_id").focus();
        return;
    }
  }

  var report_title=$( "div.form_caption" ).html();


  var is_sales = 0
  if ($('#is_sales').is(":checked"))
  {
    is_sales = 1;
  }

  if(rpt_type == 1 && txt_fso_no == "" && is_sales ==0)
  {
    var action = "report_generate";
  }
  else if(rpt_type == 13 && txt_fso_no == "" && is_sales ==0)
  {
    var action = "report_generate_dtls2";
  }
  else if(rpt_type == 1 && (txt_fso_no != "" || is_sales ==1))
  {
    var action = "sales_report_generate";
  }
  else if(rpt_type == 2 && (txt_fso_no != "" || is_sales ==1))
  {
    var action = "sales_summary_report_generate";
  }
  else if(rpt_type == 2 && txt_fso_no == "" && is_sales ==0)
  {
    var action = "report_generate2";
  }
  else if(rpt_type == 10 && txt_fso_no == "" && is_sales ==0)
  {
    var action = "report_generate2_newBtn";
  }
  else if(rpt_type == 11 && txt_fso_no == "" && is_sales ==0)
  {
    //var action = "report_generate3";
  }
  else if(rpt_type == 12 && txt_fso_no == "" && is_sales ==0)
  {
    var action = "report_generate3";
  }
  var data="action="+ action + get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_year*txt_job_no*txt_job_id*txt_file_no*txt_ref_no*txt_date_from*txt_hide_booking_id*txt_booking_no*cbo_store_name*txt_fso_no',"../../../")+'&report_title='+report_title+'&rpt_type='+rpt_type+'&cbo_shiping_status='+cbo_shiping_status+'&cbo_value_with='+cbo_value_with+'&cbo_string_search_type='+cbo_string_search_type; 

  http.open("POST","requires/style_store_wise_grey_fabric_stock_controller.php",true);
  http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
  http.send(data);
  http.onreadystatechange = generate_report_reponse;
  freeze_window(2);
}

function generate_report_reponse()
{
  if(http.readyState == 4)
  {
   var reponse=trim(http.responseText).split("####");
   $("#report_container2").html(reponse[0]);
   document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';
   show_msg('3');
   release_freezing();
 }
}

function new_window()
{
  document.getElementById('scroll_body').style.overflow="auto";
  document.getElementById('scroll_body').style.maxHeight="none";

  var w = window.open("Surprise", "#");
  var d = w.document.open();
  d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
   '<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
  d.close();

  document.getElementById('scroll_body').style.overflowY="scroll";
  document.getElementById('scroll_body').style.maxHeight="380px";
}

function generate_report_exel_only(excl_no)
{
  if(excl_no==1)
  {
    if( form_validation('cbo_company_id*txt_date_from','Company Name*Date')==false )
    {
      return;
    }
    var report_title=$( "div.form_caption" ).html();

    var data="action=report_generate_exel_only" + get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_year*txt_job_no*txt_job_id*txt_file_no*txt_ref_no*txt_date_from*txt_hide_booking_id*txt_booking_no*cbo_store_name',"../../../")+'&report_title='+report_title;
  }
  else
  {
    var txt_job_no = $("#txt_job_no").val().trim();
    var txt_file_no = $("#txt_file_no").val().trim();
    var txt_ref_no = $("#txt_ref_no").val().trim();
    var txt_booking_no = $("#txt_booking_no").val().trim();
    var txt_fso_no = $("#txt_fso_no").val().trim();
    var cbo_buyer_id = $("#cbo_buyer_id").val();
    var cbo_shiping_status = $("#cbo_shiping_status").val();
    var cbo_value_with = $("#cbo_value_with").val();
    var rpt_type=12;
    if( form_validation('cbo_company_id*txt_date_from','Company Name*Date')==false )
    {
      return;
    }
    if(cbo_buyer_id == 0 && txt_job_no =="" && txt_booking_no =="" && txt_file_no =="" && txt_ref_no =="" && txt_fso_no =="" )
    {
        alert("Please select Buyer.");
        $("#cbo_buyer_id").focus();
        return;
    }
    var report_title=$( "div.form_caption" ).html();

    var data="action=report_generate_exel_only_3" + get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_year*txt_job_no*txt_job_id*txt_file_no*txt_ref_no*txt_date_from*txt_hide_booking_id*txt_booking_no*cbo_store_name*txt_fso_no',"../../../")+'&report_title='+report_title+'&rpt_type='+rpt_type+'&cbo_shiping_status='+cbo_shiping_status+'&cbo_value_with='+cbo_value_with; 
  }


  http.open("POST","requires/style_store_wise_grey_fabric_stock_controller.php",true);
  http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
  http.send(data);
  http.onreadystatechange = generate_report_reponse_exel_only;
  freeze_window(2);
}

function generate_report_reponse_exel_only()
{
  if(http.readyState == 4) 
  {  
    var reponse=trim(http.responseText).split("####");
   //$("#report_container2").html(reponse[0]);
    if(reponse!='')
    {
      $('#aa1').removeAttr('href').attr('href','requires/'+reponse[0]);
      document.getElementById('aa1').click();
    }
    show_msg('3');
    release_freezing();
  }

}


function openmypage_job()
{
  if( form_validation('cbo_company_id','Company Name')==false )
  {
   return;
 }

 var companyID = $("#cbo_company_id").val();
 var buyer_name = $("#cbo_buyer_id").val();
 var cbo_year_id = $("#cbo_year").val();
 var cbo_month_id = $("#cbo_month").val();

 var page_link='requires/style_store_wise_grey_fabric_stock_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
 var title='Job No Search';
 emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=400px,center=1,resize=1,scrolling=0','../../');
 emailwindow.onclose=function()
 {
   var theform=this.contentDoc.forms[0];
   var job_no=this.contentDoc.getElementById("hide_job_no").value;
   var job_id=this.contentDoc.getElementById("hide_job_id").value;
   $('#txt_job_no').val(job_no);
   $('#txt_job_id').val(job_id);
 }
}

function openmypage_booking()
{
  if( form_validation('cbo_company_id','Company Name')==false )
  {
    return;
  }
  var companyID = $("#cbo_company_id").val();
  var buyer_name = $("#cbo_buyer_id").val();
  var cbo_year_id = $("#cbo_year").val();

  var page_link='requires/style_store_wise_grey_fabric_stock_controller.php?action=booking_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
  var title='Booking No Search';
  emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1010px,height=370px,center=1,resize=1,scrolling=0','../../');
  emailwindow.onclose=function()
  {
    var theform=this.contentDoc.forms[0];
    var booking_no=this.contentDoc.getElementById("hide_booking_no").value;
    var booking_id=this.contentDoc.getElementById("hide_booking_id").value;
    $('#txt_booking_no').val(booking_no);
    $('#txt_hide_booking_id').val(booking_id);
  }
}

function openpage_fabric_booking(action,po_id)
{
  emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_store_wise_grey_fabric_stock_controller.php?action='+action+'&po_id='+po_id, 'Booking Details Info', 'width=900px,height=370px,center=1,resize=0,scrolling=0','../../');
}

function openpage(action,data)
{
  emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_store_wise_grey_fabric_stock_controller.php?action='+action+'&data='+data, 'Details Info', 'width=1200px,height=390px,center=1,resize=0,scrolling=0','../../');
}

function openmypage(job_no,constPartColor,lst_issue_date,po_ids,quantity,is_sales,action)
{
  var companyID = $("#cbo_company_id").val();
  var storeId = $("#cbo_store_name").val();
  var today_date = $("#txt_date_from").val();
  emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_store_wise_grey_fabric_stock_controller.php?action='+action+'&job_no='+job_no+'&constPartColor='+constPartColor+'&lst_issue_date='+lst_issue_date+'&po_ids='+po_ids+'&quantity='+quantity+'&is_sales='+is_sales+'&storeId='+storeId+'&companyID='+companyID+'&today_date='+today_date, 'Details Info', 'width=1200px,height=390px,center=1,resize=0,scrolling=0','../../');
}

function openmypage_sales()
{
  if( form_validation('cbo_company_id','Company Name')==false )
  {
    return;
  }
  var companyID = $("#cbo_company_id").val();
  var buyer_name = $("#cbo_buyer_id").val();
  var cbo_year_id = $("#cbo_year").val();

  var page_link='requires/style_store_wise_grey_fabric_stock_controller.php?action=sales_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
  var title='Booking No Search';
  emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1010px,height=370px,center=1,resize=1,scrolling=0','../../');
  emailwindow.onclose=function()
  {
    var theform=this.contentDoc.forms[0];
    var booking_no=this.contentDoc.getElementById("hide_sales_no").value;
    var booking_id=this.contentDoc.getElementById("hide_sales_id").value;
    $('#txt_fso_no').val(booking_no);
    $('#hdn_fso_id').val(booking_id);
  }
}

function clr_job_ref()
{
  var is_sales = 0
  if ($('#is_sales').is(":checked"))
  {
   is_sales = 1;
  }
  if(is_sales == 1)
  {
    var txt_job_no = $("#txt_job_no").val("");
    var txt_file_no = $("#txt_file_no").val("");
    var txt_ref_no = $("#txt_ref_no").val("");
    var txt_booking_no = $("#txt_booking_no").val("");
  }
}
function print_report_button_setting(report_ids)
{
    $('#search').hide();
    $('#search2').hide();
    $('#search4').hide();
    $('#search3').hide();
    $('#search6').hide();
    $('#search7').hide();
    $('#search8').hide();
    var report_id=report_ids.split(",");
    report_id.forEach(function(items)
    {
        if(items==261){$('#search').show();} //Details 
        else if(items==23){$('#search2').show();} //Summary
        else if(items==150){$('#search4').show();} //Summary 2
        else if(items==422){$('#search3').show();} //Excel Only
        else if(items==421){$('#search6').show();} //Summary 3
        else if(items==423){$('#search7').show();} //Excel Only 3
        else if(items==282){$('#search8').show();} //Details 2
    });
}

</script>
</head>
<body onLoad="set_hotkey()">
  <div style="width:100%;" align="center">
   <? echo load_freeze_divs ("../../../",$permission); ?>
   <form name="orderwisegreyfabricstock_1" id="orderwisegreyfabricstock_1" autocomplete="off" >
    <h3 style="width:1860px; margin-top:10px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
    <div id="content_search_panel" style="width:100%;" align="center">
      <fieldset style="width:1865px;">
        <table class="rpt_table" width="1865" cellpadding="0" cellspacing="0" border="1" rules="all">
          <thead>
            <th colspan="14" align="center"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
          </thead>
          <thead>
            <tr>
              <th class="must_entry_caption">Company</th>
              <th>Buyer</th>
              <th>Year</th>
              <th>Job</th>
              <th>Booking No</th>
              <th>File No.</th>
              <th>Ref No.</th>
              <th>Fso No.</th>
              <th>Shiping Status</th>
              <th>Value</th>
              <th>Store</th>
              <th class="must_entry_caption">Transaction Date</th>
              <th>&nbsp;Is sales&nbsp;</th>
              <th><input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" onClick="reset_form('orderwisegreyfabricstock_1','report_container*report_container2','','','','');" /></th>
            </tr>
          </thead>
          <tr class="general">
            <td>
              <?
              echo create_drop_down( "cbo_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/style_store_wise_grey_fabric_stock_controller',this.value+'_'+1+'_'+4, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/style_store_wise_grey_fabric_stock_controller', this.value, 'load_drop_down_store', 'store_td' );get_php_form_data( this.value, 'eval_multi_select', 'requires/style_store_wise_grey_fabric_stock_controller' );get_php_form_data(this.value,'print_button_variable_setting','requires/style_store_wise_grey_fabric_stock_controller' );" );
              ?>
            </td>
            <td id="buyer_td">
              <? echo create_drop_down( "cbo_buyer_id", 140, $blank_array,"", 1, "--Select Buyer--", 0, "",0 ); ?>
            </td>
            <td>
              <? echo create_drop_down( "cbo_year", 70, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?>
            </td>
            <td>
              <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes_numeric" style="width:100px" onDblClick="openmypage_job();" placeholder="Browse/Write" />
              <input type="hidden" id="txt_job_id" name="txt_job_id"/>
            </td>
            <td>
             <input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:100px" placeholder="Browse Or Write" onDblClick="openmypage_booking();" >
             <input type="hidden" name="txt_hide_booking_id" id="txt_hide_booking_id" readonly>
           </td>
           <td>
            <input type="text" id="txt_file_no" name="txt_file_no" class="text_boxes" style="width:80px" placeholder="Write"/>
          </td>
          <td>
            <input type="text" id="txt_ref_no" name="txt_ref_no" class="text_boxes" style="width:80px" placeholder="Write"/>
          </td>
          <td>
            <input type="text" id="txt_fso_no" name="txt_fso_no" class="text_boxes" style="width:80px" onDblClick="openmypage_sales();" placeholder="Browse/Write"/>
            <input type="hidden" name="hdn_fso_id" id="hdn_fso_id" readonly>            
          </td>
          <td>
              <?   
              //$shiping_status_arr=array(0=>"All",1=>"Full Pending + Partial Delivery",2=>"Full Delivery/Closed");
              //echo create_drop_down( "cbo_shiping_status", 100, $shipment_status,"",0, "", 0,'',0 );

              $ship_status_arr = array(1=>"Full Pending",2=>"Partial Shipment",3=>"Full Shipment/Closed"); 
              echo create_drop_down( "cbo_shiping_status", 100, $ship_status_arr,"", 1,"-All-","", "",0,"" );

              ?>
          </td>
          <td>
              <?
                $valueWithArr=array(1=>'Value With 0',2=>'Value Without 0');
                echo create_drop_down( "cbo_value_with", 100, $valueWithArr, "", 0, "", 2, "", "", "");
              ?>
          </td>
          <td id="store_td">
            <? 
              //echo create_drop_down( "cbo_store_name", 180, $blank_array,"", 1, "-- All Store --", $storeName, "",0 );
              echo create_drop_down( "cbo_store_name", 120, $blank_array,"", 0, "", 0, "",0 );
              ?>

          </td>
          <td>
            <input type="text" name="txt_date_from" id="txt_date_from" value="<? echo date("d-m-Y", time());?>" class="datepicker" style="width:70px;" readonly/>
          </td>
          <td>
            <input type="checkbox" name="is_sales" id="is_sales" onClick="clr_job_ref();">
          </td>
          <td>
            <input type="button" name="search" id="search" value="Details" onClick="generate_report(1)" style="width:50px;display: none;" class="formbutton" />
            <input type="button" name="search" id="search8" value="Details-2" onClick="generate_report(13)" style="width:50px;display: none;" class="formbutton" />
            <input type="button" name="search" id="search2" value="Summary" onClick="generate_report(2)" style="width:60px;display: none;" class="formbutton" />
            <input type="button" name="search" id="search4" value="Summary 2 " onClick="generate_report(10)" style="width:70px;display: none;" class="formbutton" />
            <!--<input type="button" name="search" id="search5" value="Summary 3 " onClick="generate_report(11)" style="width:70px" class="formbutton" />-->            
            <input type="button" name="search" id="search3" value="Excel Only" onClick="generate_report_exel_only(1)" style="width:60px;display: none;" class="formbutton" />
            <input type="button" name="search" id="search6" value="Summary 3 " onClick="generate_report(12)" style="width:70px;display: none;" class="formbutton" />
            <input type="button" name="search" id="search7" value="Excel Only 3" onClick="generate_report_exel_only(3)" style="width:70px;display: none;" class="formbutton" />
            <input type="hidden" name="search" id="search3" value="E" style="width:10px" class="formbutton" />
            <a href="" id="aa1"></a>
          </td>
        </tr>
      </table>
    </fieldset>
  </div>
  <div id="report_container" align="center"></div>
  <div id="report_container2"></div>
</form>
</div>
</body>
<script>
  set_multiselect('cbo_store_name','0','0','0');
</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
