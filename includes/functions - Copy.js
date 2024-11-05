
/*function createObject() {
	var request_type;
	var browser = navigator.appName;
	//alert(browser);
	if( browser == "Microsoft Internet Explorer" ) {
		request_type = new ActiveXObject("Microsoft.XMLHTTP");
	} else {
		request_type = new XMLHttpRequest();
	}
	return request_type;
}*/

function createObject() {
	var request_type;
	//var xmlhttp;
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  request_type=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  request_type=new ActiveXObject("Microsoft.XMLHTTP");
  }
	return request_type;
}

var http = createObject();

var operation=new Array (5);
operation[0]="Save";
operation[1]="Update";	
operation[2]="Delete"; 
operation[3]="Approve"; 
operation[4]="Print"; 
 
 var operation_msg=new Array (6);
operation_msg[0]="Data is Saving, Please wait...";
operation_msg[1]="Data is Updating, Please wait...";	
operation_msg[2]="Data is Deleting, Please wait..."; 
operation_msg[3]="Report Generating, Please wait..."; 
operation_msg[4]="List View is Populating, Please wait..."; 
operation_msg[5]="Data is Populating, Please wait..."; 
 
var operation_success_msg=new Array (13);
operation_success_msg[0]="Data is Saved Successfully";
operation_success_msg[1]="Data is Updated Successfully";	
operation_success_msg[2]="Data is Deleted Successfully"; 
operation_success_msg[3]="Report is Generated Successfully"; 
operation_success_msg[4]="List View is Populated Successfully"; 

operation_success_msg[5]="Data is not Saved Successfully";
operation_success_msg[6]="Data is not Updated Successfully";	
operation_success_msg[7]="Data is not Deleted Successfully"; 
operation_success_msg[8]="Report is not Generated Successfully"; 
operation_success_msg[9]="List View is not Populated Successfully"; 
operation_success_msg[10]="Invalid Operation"; 
operation_success_msg[11]="Duplicate Data Found, Please check again."; 
operation_success_msg[12]="Old Password not Matching, Please check again."; 
operation_success_msg[13]="Delete restricted, This Information is used in another Table."; 
operation_success_msg[14]="Update restricted, This Information is used in another Table."; 
operation_success_msg[15]="Database is Busy, Please wait..."; 
function show_msg( msg )
{
	 if(!msg) var msg=10;
	 msg=trim(msg);
	 $('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
	 { 
		$('#messagebox_main', window.parent.document).html(operation_success_msg[msg]).removeClass('messagebox').addClass('messagebox_error').fadeOut(2500);

	 });
}

var time=0;
function freeze_window(msg)
{
	document.getElementById('msg_text').innerHTML=operation_msg[msg];
	
	var id = '#dialog';
	//Get the screen height and width
	var maskHeight = $(document).height();
	var maskWidth = $(window).width();
	//Set height and width to mask to fill up the whole screen
	$('#mask').css({'width':maskWidth,'height':maskHeight});
	//transition effect    
	$('#mask').fadeIn(0);   
	$('#mask').fadeTo("slow",0.8); 
	//Get the window height and width
	var winH = $(window).height();
	var winW = $(window).width();
	//Set the popup window to center
	$(id).css('top',  winH/2-$(id).height()/2);
	$(id).css('left', winW/2-$(id).width()/2);
 	setInterval('count_process_time(document.getElementById("msg").innerHTML)',1000);
	//transition effect
	$(id).fadeIn(0);
	//$(id).fadeOut(9000);
	//setTimeout("$('#mask, .window').hide();",5000);
}

function count_process_time(time)
{
	time=(time*1)+1;
	document.getElementById('msg').innerHTML=time;
}

function release_freezing()
{
	$('#mask, .window').hide();
}

function set_button_status(is_update, permission, submit_func,btn_id)
{
 
	permission=permission.split('_');
	 
	if (is_update==1)   //Update Mode
	{
		 if (permission[0] == 2 )
		 {
		 	$('#save'+btn_id).removeClass('formbutton').addClass('formbutton_disabled') 
			$('#save'+btn_id).attr('onclick', 'show_no_permission_msg(0)');
		 }
		 else 
		 {
			 $('#save'+btn_id).removeClass('formbutton').addClass('formbutton_disabled');
			 $('#save'+btn_id).attr('onclick', 'show_button_disable_msg(0)');
		 }
		if( permission[1] == 2 )
		{
			 $('#update'+btn_id).removeClass('formbutton').addClass('formbutton_disabled');
			 $('#update'+btn_id).attr('onclick', 'show_no_permission_msg(1)');
		}
		else
		{
			 $('#update'+btn_id).removeClass('formbutton_disabled').addClass('formbutton');
			 $('#update'+btn_id).attr('onclick', submit_func+'(1)'); 
		}
		if( permission[2] == 2 )
		{
			 $('#Delete'+btn_id).removeClass('formbutton').addClass('formbutton_disabled');
			 $('#Delete'+btn_id).attr('onclick', 'show_no_permission_msg(2)');
		}
		else
		{
			 $('#Delete'+btn_id).removeClass('formbutton_disabled').addClass('formbutton');
			 $('#Delete'+btn_id).attr('onclick', submit_func+'(2)'); 
		}
		if(permission[3] == 2)
		 {
			  $('#approve'+btn_id).removeClass('formbutton').addClass('formbutton_disabled');
			  $('#approve'+btn_id).attr('onclick', 'show_no_permission_msg(3)');
		 }
		 else
		 {
			  $('#approve'+btn_id).removeClass('formbutton_disabled').addClass('formbutton');
			  $('#approve'+btn_id).attr('onclick', submit_func+'(3)');
		 }
		 
		if( permission[4] == 2 )
		{
			 $('#Print'+btn_id).removeClass('formbutton').addClass('formbutton_disabled');
			 $('#Print').attr('onclick', 'show_no_permission_msg(4)');
		}
		else
		{
			 $('#Print'+btn_id).removeClass('formbutton_disabled').addClass('formbutton'); 
			 $('#Print'+btn_id).attr('onclick', submit_func+'(4)');
		}
	}
	else   //New Insert Mode
	{
		 if (permission[0] == 2 )
		 {
		 	$('#save'+btn_id).removeClass('formbutton').addClass('formbutton_disabled') 
			$('#save'+btn_id).attr('onclick', 'show_no_permission_msg(0)');
		 }
		 else 
		 {
			 $('#save'+btn_id).removeClass('formbutton_disabled').addClass('formbutton');
			 $('#save'+btn_id).attr('onclick', submit_func+'(0)');
		 }
		 if (permission[1] == 2 )
		 {
		 	$('#update'+btn_id).removeClass('formbutton').addClass('formbutton_disabled') 
			$('#update'+btn_id).attr('onclick', 'show_no_permission_msg(1)');
		 }
		 else 
		 {
			 $('#update'+btn_id).removeClass('formbutton').addClass('formbutton_disabled');
			 $('#update'+btn_id).attr('onclick', 'show_button_disable_msg(1)');
		 }
		 if (permission[2] == 2 )
		 {
		 	$('#Delete'+btn_id).removeClass('formbutton').addClass('formbutton_disabled') 
			$('#Delete'+btn_id).attr('onclick', 'show_no_permission_msg(2)');
		 }
		 else 
		 {
			 $('#Delete'+btn_id).removeClass('formbutton').addClass('formbutton_disabled');
			 $('#Delete'+btn_id).attr('onclick', 'show_button_disable_msg(2)');
		 }
		 if (permission[3] == 2 )
		 {
		 	$('#approve'+btn_id).removeClass('formbutton').addClass('formbutton_disabled') 
			$('#approve'+btn_id).attr('onclick', 'show_no_permission_msg(3)');
		 }
		 else 
		 {
			 $('#approve'+btn_id).removeClass('formbutton').addClass('formbutton_disabled');
			 $('#approve'+btn_id).attr('onclick', 'show_button_disable_msg(3)');
		 }
		  if (permission[4] == 2 )
		 {
		 	$('#Print'+btn_id).removeClass('formbutton').addClass('formbutton_disabled') 
			$('#Print'+btn_id).attr('onclick', 'show_no_permission_msg(4)');
		 }
		 else 
		 {
			 $('#Print'+btn_id).removeClass('formbutton').addClass('formbutton_disabled');
			 $('#Print'+btn_id).attr('onclick', 'show_button_disable_msg(4)');
		 }
		 
	}  
	return;
}
 
 function get_submitted_variables( flds )
 {
	  var fld_data='';
	 flds=flds.split('*');
	 for (var i=0; i< flds.length; i++)
	 {
		 fld_data=fld_data+'var '+flds[i]+'=  escape(document.getElementById("'+flds[i]+'").value);\n';
	 }
	 return fld_data;
 }
 
 function get_submitted_data_string( flds, path )
 {
	  
	 //var p_name = new RegExp("([^a-zA-Z0-9])");  http://blog.ftwr.co.uk/archives/2011/03/18/understanding-complex-regex/

	 if (!path) var path="";
	 var fld_data='';
	 flds=flds.split('*');
	 for (var i=0; i< flds.length; i++)
	 {
		 /* if (! document.getElementById(flds[i]).value.search(p_name)) // new RegExp('*!@')).test(document.getElementById(flds[i]).value)))
		  {
			  alert('some field ');
			  return;
		  }*/
		 if (document.getElementById(flds[i]).className=="datepicker hasDatepicker")
		 {
			
		 	fld_data=fld_data+'&'+flds[i]+"='"+  trim(change_date_format(document.getElementById(flds[i]).value,path))+"'";
		 }
		 else fld_data=fld_data+'&'+flds[i]+"='"+  (document.getElementById(flds[i]).value)+"'";
	 }
	 return fld_data;
 }
 
 
function change_date_format(date, path, new_format, new_sep)
{
	//This function will return newly formatted date String
	// uses  --> echo change_date_format($date,"dd-mm-yyyy","/")

	if (!new_sep) var new_sep="-";
	if (!new_format) var new_format="yyyy-mm-dd"
	
	var action="change_date_format";
	 return $.ajax({
		  url: path+"includes/common_functions_for_js.php?data="+date+"&action="+action+'&new_sep='+new_sep+'&new_format='+new_format,
		  async: false
		}).responseText
}

function show_no_permission_msg(str)
{
	alert('Ask Your admin for '+ operation[str]+' Persmission.');
}

function show_button_disable_msg(str)
{
	return false;
}

function set_date_range(mon)
{
	var year = document.getElementById('cbo_year_selection').value;
	
	 $('.month_button_selected').removeClass('month_button_selected').addClass('month_button');
	 if (mon.substr(0,1)=="0") id_id=mon.replace("0",""); else id_id=mon;
	 $('#btn_'+id_id).removeClass('month_button').addClass('month_button_selected');
	 
	var currentTime = new Date();
	var month = currentTime.getMonth() + 1;
	var day = currentTime.getDate();
	//var year = currentTime.getFullYear();
	
	var start_date="01" + "-" + mon  + "-" + year;
	var to_date=daysInMonth(mon,year) + "-" + mon  + "-" + year;
	
	document.getElementById('txt_date_from').value=start_date;
	document.getElementById('txt_date_to').value=to_date;		
}

function daysInMonth( month, year ) 
{
	return new Date(year, month, 0).getDate();
}

//------------------------------------------------------------------------- Form Serach List View show starts Here 
function show_list_view( data, action, div, path, extra_func ) 
{
	//alert(data);
	if (!extra_func) var extra_func="";
	if (!data) var data="0";
	
	if( trim(data).length == 0 ) {
		document.getElementById(div).innerHTML = "";
		return;
	}
	var http = createObject();
	http.onreadystatechange = function() {
		if( http.readyState == 4 && http.status == 200 ) {
			//alert(div)
			document.getElementById(div).innerHTML = http.responseText;
			eval(extra_func);
			set_all_onclick();
		}
	}
	http.open( "GET", path+".php?data=" + trim( data ) + "&action=" + action, false );
	http.send();
}

//------------------------------------------------------------------------- Supporting Form Value Fill Starts Here
 var ajax = new sack();

//------------------------------------------------------------------------- Form Field Fill Module Entry
function get_php_form_data( id, type, path ) {
	 
	ajax.requestFile = path+'.php?data=' + id + '&action=' + type;	// Specifying which file to get
	ajax.onCompletion = eval_result;	// Specify function that will be executed after file has been found
	ajax.runAJAX();	 
}
function eval_result() {
	eval( ajax.response );
	set_all_onclick();
} 
function sack( file ) {
	this.xmlhttp = null;
	
	this.resetData = function() {
		this.method = "POST";
		this.queryStringSeparator = "?";
		this.argumentSeparator = "&";
		this.URLString = "";
		this.encodeURIString = true;
		this.execute = false;
		this.element = null;
		this.elementObj = null;
		this.requestFile = file;
		this.vars = new Object();
		this.responseStatus = new Array(2);
	};
	
	this.resetFunctions = function() {
		this.onLoading = function() {};
		this.onLoaded = function() {};
		this.onInteractive = function() {};
		this.onCompletion = function() {};
		this.onError = function() {};
		this.onFail = function() {};
	};
	
	this.reset = function() {
		this.resetFunctions();
		this.resetData();
	};
	
	this.createAJAX = function() {
		try {
			this.xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		} catch( e1 ) {
			try {
				this.xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			} catch( e2 ) {
				this.xmlhttp = null;
			}
		}
		
		if( !this.xmlhttp ) {
			if( typeof XMLHttpRequest != "undefined" ) this.xmlhttp = new XMLHttpRequest();
			else this.failed = true;
		}
	};
	
	this.setVar = function( name, value ) {
		this.vars[name] = Array( value, false );
	};
	
	this.encVar = function( name, value, returnvars ) {
		if (true == returnvars) return Array( encodeURIComponent( name ), encodeURIComponent( value ) );
		else this.vars[encodeURIComponent( name )] = Array( encodeURIComponent( value ), true );
	}
	
	this.processURLString = function( string, encode ) {
		encoded = encodeURIComponent( this.argumentSeparator );
		regexp = new RegExp( this.argumentSeparator + "|" + encoded );
		varArray = string.split( regexp );
		for( i = 0; i < varArray.length; i++ ) {
			urlVars = varArray[i].split("=");
			if( true == encode ) this.encVar( urlVars[0], urlVars[1] );
			else this.setVar( urlVars[0], urlVars[1] );
		}
	}
	
	this.createURLString = function( urlstring ) {
		if( this.encodeURIString && this.URLString.length ) this.processURLString( this.URLString, true );
		if( urlstring ) {
			if( this.URLString.length ) this.URLString += this.argumentSeparator + urlstring;
			else this.URLString = urlstring;
		}
		
		// prevents caching of URLString
		this.setVar( "rndval", new Date().getTime() );
		
		urlstringtemp = new Array();
		for( key in this.vars ) {
			if( false == this.vars[key][1] && true == this.encodeURIString ) {
				encoded = this.encVar( key, this.vars[key][0], true );
				delete this.vars[key];
				this.vars[encoded[0]] = Array( encoded[1], true );
				key = encoded[0];
			}
			urlstringtemp[urlstringtemp.length] = key + "=" + this.vars[key][0];
		}
		if( urlstring ) this.URLString += this.argumentSeparator + urlstringtemp.join( this.argumentSeparator );
		else this.URLString += urlstringtemp.join( this.argumentSeparator );
	}
	
	this.runResponse = function() {
		eval( this.response );
	}
	
	this.runAJAX = function( urlstring ) {
		if( this.failed ) this.onFail();
		else {
			this.createURLString( urlstring );
			if( this.element ) this.elementObj = document.getElementById( this.element );
			if( this.xmlhttp ) {
				var self = this;
				if( this.method == "GET" ) {
					totalurlstring = this.requestFile + this.queryStringSeparator + this.URLString;
					this.xmlhttp.open( this.method, totalurlstring, false );
				} else {
					this.xmlhttp.open( this.method, this.requestFile, false );
					try {
						this.xmlhttp.setRequestHeader( "Content-Type", "application/x-www-form-urlencoded" );
					}
					catch( e ) {}
				}
				
				this.xmlhttp.onreadystatechange = function() {
					switch( self.xmlhttp.readyState ) {
						case 1:
							self.onLoading();
							break;
						case 2:
							self.onLoaded();
							break;
						case 3:
							self.onInteractive();
							break;
						case 4:
							self.response = self.xmlhttp.responseText;
							self.responseXML = self.xmlhttp.responseXML;
							self.responseStatus[0] = self.xmlhttp.status;
							self.responseStatus[1] = self.xmlhttp.statusText;
							
							if( self.execute ) self.runResponse();
							
							if( self.elementObj ) {
								elemNodeName = self.elementObj.nodeName;
								elemNodeName.toLowerCase();
								if( elemNodeName == "input" || elemNodeName == "select" || elemNodeName == "option" || elemNodeName == "textarea") self.elementObj.value = self.response;
								else self.elementObj.innerHTML = self.response;
							}
							if( self.responseStatus[0] == "200" ) self.onCompletion();
							else self.onError();
							
							self.URLString = "";
							break;
					}
				};
				this.xmlhttp.send(this.URLString);
			}
		}
	};
	
	this.reset();
	this.createAJAX();
} 
//------------------------------------------------------------------------- Supporting Form Value Fill Ends Here
//------------------------------------------------------------------------- Check Numeric Value starts
function IsNumeric( strString ) {
	var strValidChars = "0123456789.";
	var strChar;
	var blnResult = true;
	
	if( strString.length == 0 ) return false;
	
	//test strString consists of valid characters listed above
	for( i = 0; i < strString.length && blnResult == true; i++ ) {
		strChar = strString.charAt(i);
		if( strValidChars.indexOf( strChar ) == -1 ) blnResult = false;
	}
	return blnResult;
}
//------------------------------------------------------------------------- Check Numeric Value Ends

function load_drop_down( plink, data, action, container ) {
	var strURL = plink+".php?data=" + data+"&action=" + action;
 	var http = createObject();
	if( http ) {
		http.onreadystatechange = function() {
			if( http.readyState == 4 ) {
				///alert(strURL+"Sumon"+http.status);
				if( http.status == 200 ){ document.getElementById( container ).innerHTML = http.responseText; set_all_onclick(); }
				
				//else alert("There was a problem while using XMLHTTP:\n" + http.statusText);
			}
		}
		http.open( "GET", strURL, false );
		http.send( null );
	}
}
//------------------------------------------------------------------------- load Drop Down List Value ends
function trim( stringToTrim ) {
	return stringToTrim.replace( /^\s+|\s+$/g, "" );
}

function ltrim( stringToTrim ) {
	return stringToTrim.replace( /^\s+/, "" );
}

function rtrim( stringToTrim ) {
	return stringToTrim.replace( /\s+$/, "" );
}

//------------------------------------------------------------------------- Return Next ID
function return_next_id( type ) {
	vid1 = document.getElementById('cbo_module_name').value;
	vid2 = document.getElementById('cbo_root_menu').value;
	vid3 = document.getElementById('cbo_root_menu_under').value;
	type = '1';
	
	var strURL = "../ajax_next_id.php?type=" + type + "&vid1=" + vid1 + "&vid2=" + vid2 + "&vid3=" + vid3;
	 var http = createObject();
	if( http ) {
		http.onreadystatechange = function() {
			if( http.readyState == 4 ) {
				if( http.status == 200 ) document.getElementById('menu_seq_menu_create').innerHTML=http.responseText;
				//else alert( "There was a problem while using XMLHTTP:\n" + http.statusText );
			}
		};			
		http.open( "GET", strURL, false );
		http.send( null );
	}
}


//Numeric Value allow field script


//function :: add days for adding some days with a specified date
// param   :: from_date, no_of_days
// return  :: adding date

function add_days( dateObj, byMany, n_format, target_field  )//from_date, no_of_days )
{
	if(!target_field) var target_field="";
	var temp_date="";
	var split_date = dateObj.split("-");
	if(split_date[0].length!=4) dateObj = split_date[2]+"-"+split_date[1]+"-"+split_date[0];
	if (!n_format) var n_format=1;
	var timeU="d";
	var millisecond=1;
	var second=millisecond*1000;
	var minute=second*60;
	var hour=minute*60;
	var day=hour*24;
	var year=day*365;
	dateObj=new Date (dateObj);
	var newDate;
	var dVal=dateObj.valueOf();
	switch(timeU) {
		case "ms": newDate=new Date(dVal+millisecond*byMany); break;
		case "s": newDate=new Date(dVal+second*byMany); break;
		case "mi": newDate=new Date(dVal+minute*byMany); break;
		case "h": newDate=new Date(dVal+hour*byMany); break;
		case "d": newDate=new Date(dVal+day*byMany); break;
		case "y": newDate=new Date(dVal+year*byMany); break;
	}
	if (n_format==1)
		temp_date= $.datepicker.formatDate('dd-mm-yy', newDate);
		
	if (n_format==2)
		temp_date= $.datepicker.formatDate('yy-mm-dd', newDate);
		
	if(target_field!="") document.getElementById(target_field).value=temp_date;	
	else return temp_date;
		
	//return dateFormat(newDate,"yyyy/mm/dd");
}

// ----------------------------end ----------------------------------------

//function :: Date Diff 
// param   :: from_date, To Date
// return  :: Days in Diff
//datediff( $interval, $datefrom, $dateto, $using_timestamps = false ) 
	
function date_diff( interval, date_form, date_to  )//from_date, no_of_days )
{
	var split_date = date_form.split("-");
	if(split_date[0].length!=4) date_form = split_date[2]+"-"+split_date[1]+"-"+split_date[0];
	
	var split_date = date_to.split("-");
	if(split_date[0].length!=4) date_to = split_date[2]+"-"+split_date[1]+"-"+split_date[0];
	
	
	if (!n_format) var n_format=1;
	//var interval="d";
	var millisecond=1;
	var second=millisecond*1000;
	var minute=second*60;
	var hour=minute*60;
	var day=hour*24;
	//var month=day*30;
	var year=day*365;
	date_form=new Date (date_form);
	date_to=new Date (date_to);
	var newDate;
	//var dVal=dateObj.valueOf();
	switch(interval) {
		case "d": newDate=Math.ceil((date_form.getTime()-date_to.getTime())/(day));  break;
		case "y": newDate=Math.ceil((date_form.getTime()-date_to.getTime())/(year));  break;
	}
	 
		if (newDate>0) return newDate; else return ((newDate)*(-1));
	 
}

// ----------------------------end ----------------------------------------

		// reset a form
		// written by :: m@mit
function reset_form( forms,  divs, fields, default_val, extra_func ) 
{
  // iterate over all of the inputs for the form
  // element that was passed in
 
 // alert(document.getElementById('Delete1').getAttribute('onclick'));
 // return;
 //default_val== "id,val*id,val*id,val"
 if (!extra_func) var extra_func="";
 
 if (!default_val) var default_val="";
 
  if (forms!="")
  {
	   forms=forms.split('*');
		for (var i=0; i<forms.length; i++)  
		{
			var form_id=forms[i].split("_");
			var idd=$('#'+forms[i]).find('.formbutton').attr('id');
		//	alert(idd);
			
			var fnc=document.getElementById(idd).getAttribute('onclick').split('(');
			set_button_status(0, permission, fnc[0], form_id[1]);
			 
			
			$('#'+forms[i]).find(':input').each(function() 
			{
				
				var type = this.type;
				var tag = this.tagName.toLowerCase(); // normalize case
				// it's ok to reset the value attr of text inputs,
				// password inputs, and textareas
				if (type == 'text' || type == 'password' || type == 'hidden' || tag == 'textarea')
				  this.value = "";
				// checkboxes and radios need to have their checked state cleared
				// but should *not* have their 'value' changed
				else if (type == 'checkbox' || type == 'radio')
				  this.checked = false;
				// select elements need to have their 'selectedIndex' property set to -1
				// (this works for both single and multiple select elements)
				else if (type == 'select-one')
				  this.selectedIndex = 0;
				else if (type == 'hidden')
				  this.value = "";
			});
		}
    }
	if (divs!="")
  	{
	   divs=divs.split('*');
		for (var i=0; i<divs.length; i++)  
		{
			document.getElementById(divs[i]).innerHTML="";
		}
	}
	if (fields!="")
  	{
		
	   fields=fields.split('*');
		for (var i=0; i<fields.length; i++)  
		{
			
			var type = document.getElementById(fields[i]).type;
			var tag = document.getElementById(fields[i]).tagName.toLowerCase(); // normalize case
			// it's ok to reset the value attr of text inputs,
		 
			if (type == 'text' || type == 'password' || type == 'textarea')
			  document.getElementById(fields[i]).value = "";
			// checkboxes and radios need to have their checked state cleared
			// but should *not* have their 'value' changed
			else if (type == 'checkbox' || type == 'radio')
			  document.getElementById(fields[i]).checked = false;
			// select elements need to have their 'selectedIndex' property set to -1
			// (this works for both single and multiple select elements)
			else if (type == 'select-one')
			  document.getElementById(fields[i]).selectedIndex = 0;
			else if (type == 'hidden')
			  document.getElementById(fields[i]).value = "";
		}
	}
	
	if (default_val!="")
	{
		default_val=default_val.split('*');
		for (var i=0; i<default_val.length; i++)
		{
			def=default_val[i].split(',');
			if (!def[2])
				document.getElementById(def[0]).value = def[1];
			else
			{
				for (var k=1; k<=def[2]; k++)
				{
					document.getElementById(def[0]+k).value = def[1];
				}
			}
				
		}
	}
	eval(extra_func);
	
}

function disable_enable_fields( flds, operation, loop_flds, loop_leng )
{
	if (!loop_flds) var loop_flds="";
	if (!loop_leng) var loop_leng="";
	if (!flds) var flds="";
	if (!operation) var operation="";
	
	flds=flds.split('*');
	if (operation==0)  // Enable
	{
		for (var i=0; i<flds.length; i++)
		{
			$('#'+ flds[i]).removeAttr('disabled'); 
		}
	}
	else if (operation==1)  // Disable
	{
		for (var i=0; i<flds.length; i++)
		{
			$('#'+ flds[i]).attr('disabled',true);
		}
	}
	loop_flds=loop_flds.split('*');
	for (var i=0; i<flds.length; i++)
	{
		if (operation==0)  // Enable
		{
			for (var k=1; k<=loop_leng; k++)
			{
				$('#'+ loop_flds[i]+k).removeAttr('disabled'); 
			}
		}
		else if (operation==1)  // Enable
		{
			for (var k=1; k<=loop_leng; k++)
			{
				$('#'+ loop_flds[i]+k).attr('disabled',true);
			}
		}
	}
}

function form_validation(control,msg_text) 
{
  // iterate over all of the inputs for the form
  // element that was passed in
 // alert(control);
 //alert(parent.document.getElementById('messagebox_main').innerHTML);
 
	//parent.document.getElementById('messagebox_main').innerHTML=;
	//$('#messagebox_main', window.parent.document).html("sumon");
	
  control=control.split("*");
  msg_text=msg_text.split("*");
  var bgcolor='-moz-linear-gradient(bottom, rgb(254,151,174) 0%, rgb(255,255,255) 10%, rgb(254,151,174) 96%)';
  var new_elem="";
  for (var i=0; i<control.length; i++)
  {
	  var type = document.getElementById(control[i]).type;
		var tag = document.getElementById(control[i]).tagName;
		document.getElementById(control[i]).style.backgroundImage="";
		
		if (type == 'text' || type == 'password' || type == 'textarea')
		{
			if (trim(document.getElementById(control[i]).value)=="")
			{
		 	 document.getElementById(control[i]).focus();
	  		 document.getElementById(control[i]).style.backgroundImage=bgcolor;
			 $('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
			 { 
				$(this).html('Please Fill up '+msg_text[i]+' field Value').removeClass('messagebox').addClass('messagebox_error').fadeOut(2500);
			 });
			 return 0;
			}
		}
		else if (type == 'select-one' || type=='select' )
		{
			//alert(control[i]);
			 if ( trim(document.getElementById(control[i]).value)==0)
			 {
				 document.getElementById(control[i]).focus();
				 document.getElementById(control[i]).style.backgroundImage=bgcolor;
				 $('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
				 { 
					$(this).html('Please Fill up '+msg_text[i]+' field Value').removeClass('messagebox').addClass('messagebox_error').fadeOut(2500);

				 });
				 return 0;
			 }
		}
		else if (type == 'checkbox' || type == 'radio')
		{
			 document.getElementById(control[i]).style.backgroundImage=bgcolor;
			 if (new_elem=="") new_elem=control[i]; else new_elem=new_elem+","+control[i];
		}
		else if (type == 'hidden' )
		{
			if(trim(document.getElementById(control[i]).value)=='')
			{
				if(msg_text[i]!='')
				{
					$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
					 { 
						$(this).html('Please Fill up or Select '+msg_text[i]+' field Value').removeClass('messagebox').addClass('messagebox_error').fadeOut(2500);
		
					 });
					 return 0;
				}
				else
				{
					$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
					 { 
						$(this).html('Please fill up master field Value').removeClass('messagebox').addClass('messagebox_error').fadeOut(2500);
		
					 });
					 return 0;
					
				}
				 
			}
		}
		
  }
  return 1;
   
}

function change_search_event(mst_type, field_type, qry_array, path) 
{
	var fld = document.getElementById('cbo_search_by');
	var fld_data  =fld.options[fld.selectedIndex].text;		
	var msg_text="";
	field_type=field_type.split('*');
	qry_array=qry_array.split('*');
	var cntrl_type=field_type[mst_type];
	if (cntrl_type==0)	msg_text="Please Enter "+fld_data; else  msg_text="Select "+fld_data;
	
	document.getElementById('search_by_td_up').innerHTML=msg_text;
	
	if (cntrl_type==0)
		document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:130px " class="text_boxes"	id="txt_search_common"/>';
	else if (cntrl_type==1) // Drop Down
		document.getElementById('search_by_td').innerHTML=return_global_ajax_value(qry_array[mst_type], "search_by_drop_down",path);
	else  document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:130px " class="datepicker" onfocus="datepicker_()"	id="txt_search_common"/>';	
}

function return_global_ajax_value( data, action, path, page_name) {

	if (!page_name) var page_name="";
	if (page_name=="")  page_name='includes/common_functions_for_js';
  return $.ajax({
      url: path+page_name+".php?data="+data+"&action="+action,
      async: false
    }).responseText
}

function datepicker_()
{
	 
	$( ".datepicker" ).datepicker({
					dateFormat: 'dd-mm-yy',
					changeMonth: true,
					changeYear: true
				});
			 
}

function print_report( data, action, path ) 
{
	 
	if (!data) var data="0";
	
	if( trim(data).length == 0 ) {
		return;
	}
	var http = createObject();
	http.onreadystatechange = function() {
		if( http.readyState == 4 && http.status == 200 ) {
				var response = http.responseText;
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write(response);					
				d.close();
		}
	}
	http.open( "GET", path+".php?data=" + trim( data ) + "&action=" + action, false );
	http.send();
}


 function set_hotkey()
 {

	/*if ($('#index_page', window.parent.document).val()=="" || $('#index_page', window.parent.document).val()=="undefined")
	{
		alert('asd');
		document.body.innerHTML="";
	}*/
	document.onkeydown = function(e) {
            e = e || window.event; // because of Internet Explorer quirks...
            k = e.which || e.charCode || e.keyCode; // because of browser differences...
			 
			 
			 
				var form_id=$('#'+document.activeElement.id).closest('form').attr('id').split("_");
				
				 //alert(form_id[1]);
				
				if (k==13) return false;
				
				if (k == 83 && e.ctrlKey )   // save   control + s
				{
					$('#save'+form_id[1]).click();
					return false; 
				}
				else if (k == 85 && e.ctrlKey )  // Update   control + u
				{
					$('#update'+form_id[1]).click();
					return false; 
				}  
				else if (k == 68 && e.ctrlKey )  // Delete   control + d
				{
					$('#Delete'+form_id[1]).click();
					return false; 
				} 
				else if (k == 82 && e.ctrlKey )  // Refresh   control + r
				{
					 
					 $('#Refresh'+form_id[1]).click();
					return false; 
				} 
				/*else if (k == 120 && e.ctrlKey )  // Refresh   control + r
				{
					 
					//return_next_id_module( type );
					return_next_id();
					return false; 
				} */
				else  return true;
			   // we processed the event, stop now.
		 
	}
 }
  
 
 /*
 //------------------------------------------------------------------------- Form Refresh and Back Starts Here 
function checkKeycode( e, type ) {
	var keycode;
	var type = type;
	//alert(e);
	if( window.event ) {
		keycode = window.event.keyCode;
		if( keycode == 116 ) window.event.keyCode = 0;
	}
	else if( e ) keycode = e.which;
	
	if( keycode == 114 ) return false;
	else if( keycode == 116 ) return false;
	else if( keycode == 117 ) {
		window.event.keyCode = 0;
		return false;
	}
	else if( keycode == 8 ) {
		window.event.keyCode = 0;
		return false;
	}
	else if( keycode == 120 ) {
		return_next_id_module( type );
		return_next_id();
	}
}
//------------------------------------------------------------------------- Form Refresh and Back Ends Here 
*/


function file_uploader ( url, mst_id, det_id,  form, file_type, is_multi )
{
	if (file_type=="") file_type=1;
	if (!is_multi) var is_multi=0;
	
	if (mst_id=="" || mst_id==0 ) 
	{
		 $('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
		 { 
			$(this).html('Please Select or Save any Information before File Upload.').removeClass('messagebox').addClass('messagebox_error').fadeOut(2500);

		 });
		return false;
	}
	
	var im_url="";
	if (url.length==3)im_url=""; else if (url.length==6)im_url="../"; else if (url.length==9)im_url="../../"; else if (url.length==12)im_url="../../../"; 
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', url+'includes/common_functions_for_js.php?action=file_uploader&det_id='+det_id+'&form='+form+'&is_multi='+is_multi+'&mst_id='+mst_id+'&file_type='+file_type, 'File Uploader', 'width=640px,height=330px,center=1,resize=0,scrolling=0', im_url )
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
		var order_id=this.contentDoc.getElementById("txt_selected_id")
		var order_no=this.contentDoc.getElementById("txt_selected") //Access form field with id="emailfield"
		if (title=="Company Selection")
		{
			document.getElementById('cbo_unit_name_show').value=order_no.value;	
			document.getElementById('cbo_unit_name').value=order_id.value;	
		}
		else
		{
			document.getElementById('cbo_user_buyer_show').value=order_no.value;	
			document.getElementById('cbo_user_buyer').value=order_id.value;	
		}
		 
	}
}

function confirm_msg_box( msg, btn_type, url )
{
	var im_url="";
	if (url.length==3)im_url=""; else if (url.length==6)im_url="../"; else if (url.length==9)im_url="../../"; else if (url.length==12)im_url="../../../"; 
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', url+'includes/common_functions_for_js.php?action=confirm_msg_box&msg='+msg+'&btn_type='+btn_type, 'Confirm Box', 'width=240px,height=130px,center=1,resize=0,scrolling=0', im_url )
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
		var action=this.contentDoc.getElementById("txt_action")
		return (action.value);
	}
}

function return_ajax_request_value(data, action, path) {
  return $.ajax({
      url: path+".php?data="+data+"&action="+action,
      async: false
    }).responseText
}

function math_operation( target_fld, value_fld, operator, fld_range,dec_point)
{
	//number_format_common( number, dec_type, comma, path, currency )
	if (!dec_point) var dec_point=0;
	//alert('eeeeee');
	//alert(dec_point.currency);
	
	if(!fld_range) var fld_range="";
	if (fld_range=="")
	{
		value_fld=value_fld.split('*');
		var tot="";
		if (operator=="+")
		{
			
			for (var i=0;i<value_fld.length; i++)
			{
				tot=(tot*1)+(document.getElementById(value_fld[i]).value*1)
			}
			document.getElementById(target_fld).value=number_format_common(tot,dec_point.dec_type, dec_point.comma, dec_point.currency) ; //(document.getElementById(value_fld[0]).value*1)+(document.getElementById(value_fld[1]).value*1)
		}
		
		else if (operator=="-")
		{
			var tot =(document.getElementById(value_fld[0]).value*1)-(document.getElementById(value_fld[1]).value*1);
			document.getElementById(target_fld).value=number_format_common(tot,dec_point.dec_type, dec_point.comma, dec_point.currency);
		}
		else if (operator=="*")
		{
			var tot=1;
			for (var i=0;i<value_fld.length; i++)
			{
				tot=(tot*1)*(document.getElementById(value_fld[i]).value*1)
			}
			//document.getElementById(target_fld).value=tot.toFixed(4); //(document.getElementById(value_fld[0]).value*1)*(document.getElementById(value_fld[1]).value*1)
			document.getElementById(target_fld).value=number_format_common(tot,dec_point.dec_type, dec_point.comma, dec_point.currency); //(document.getElementById(value_fld[0]).value*1)*(document.getElementById(value_fld[1]).value*1)
		}
		else if (operator=="/")
		{
			//document.getElementById(target_fld).value=((document.getElementById(value_fld[0]).value*1)/(document.getElementById(value_fld[1]).value*1)).toFixed(4)
			var tot=((document.getElementById(value_fld[0]).value*1)/(document.getElementById(value_fld[1]).value*1))
			document.getElementById(target_fld).value=number_format_common(tot,dec_point.dec_type, dec_point.comma, dec_point.currency) ;
		}
	}
	else
	{
		//alert(fld_range);
		//alert(value_fld);
		//alert(target_fld);
		var tot=0;
		for (var i=1; i<=fld_range; i++)
		{
			tot=(tot*1) + (document.getElementById(value_fld+i).value*1);
			
		}
		//document.getElementById(target_fld).value=tot.toFixed(2);
		document.getElementById(target_fld).value=number_format_common(tot,dec_point.dec_type, dec_point.comma,dec_point.currency);

	}
}


//    All ACtivities of **********************************************************************************************FUNCTIONS_BOTTOM.js


function set_all_onclick()
{
		
 																	// To Change Background Color of Validated Field\
 
jQuery(".text_boxes").click(function() {
    var contentPanelId = jQuery(this).attr("id");
    if(document.getElementById(contentPanelId).style.backgroundColor!="")
		document.getElementById(contentPanelId).style.backgroundColor="";
		 
});

 jQuery(".text_area").click(function() {
    var contentPanelId = jQuery(this).attr("id");
    if(document.getElementById(contentPanelId).style.backgroundColor!="")
		document.getElementById(contentPanelId).style.backgroundColor="";
		 
});
  jQuery(".combo_boxes").click(function() {
    var contentPanelId = jQuery(this).attr("id");
    if(document.getElementById(contentPanelId).style.backgroundColor!="")
		document.getElementById(contentPanelId).style.backgroundColor="";
		 
});
  jQuery(".text_boxes_numeric").click(function() {
    var contentPanelId = jQuery(this).attr("id");
    if(document.getElementById(contentPanelId).style.backgroundColor!="")
		document.getElementById(contentPanelId).style.backgroundColor="";
		 
});
jQuery(".datepicker").click(function() {
    var contentPanelId = jQuery(this).attr("id");
    if(document.getElementById(contentPanelId).style.backgroundColor!="")
		document.getElementById(contentPanelId).style.backgroundColor="";
		 
});
 // To Change Background Color of Validated Field ends
 
 																						//Numeric Text Box Validation
 jQuery(".text_boxes_numeric").keydown(function(e) {
 	var evt = (e) ? e : window.event;
      var key = (evt.keyCode) ? evt.keyCode : evt.which;
 

      if(key != null) {
        key = parseInt(key, 10);


        if((key < 48 || key > 57) && (key < 96 || key > 105)) {
          if(!isUserFriendlyChar(key))
            return false;
        }
        else {
          if(evt.shiftKey)
            return false;
        }
      }
		/*
	var decPos = $(this).val().split('.'); 
    if(decPos.length > 1)
    {
        decPos = decPos[1];
        if(decPos.length >= 2) return false;
    }
*/
      return true;
   
});

function isUserFriendlyChar(val) {
      // Backspace, Tab, Enter, Insert, and Delete
      if(val == 8 || val == 9 || val == 13 || val == 45 || val == 46 || val == 110)
        return true;
	// Ctrl, Alt, CapsLock, Home, End, and Arrows
      if((val > 16 && val < 21) || (val > 34 && val < 41))
        return true;
	// The rest
      return false;
}
 //Numeric Text Box Validation Ends
 
 
																		// Special Character Validation
jQuery(".text_boxes").keypress(function(e) {
     var c = String.fromCharCode(e.which);
	 var allowed = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890.-,%@!/\<>?+()[]{};:# '; // ~ replace of Hash(#)
	 if (e.which != 8 && e.which !=0 && allowed.indexOf(c) < 0) 
	  	return false; 
	
});

 
  	$('.text_boxes').blur(function(e) {
	    var target = e.target || e.srcElement;
   		document.getElementById(target.id).value=document.getElementById(target.id).value.replace("#","~");
	   });
  
  																		 
jQuery(".text_area").keypress(function(e) {
     var c = String.fromCharCode(e.which);
	 var allowed = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890.-,%@!/\<>?+()[]{};:# '; // ~ replace of Hash(#)
	 if (e.which != 8 && e.which !=0 && allowed.indexOf(c) < 0) 
	  	return false; 
	
});

  	$('.text_area').blur(function(e) {
	    var target = e.target || e.srcElement;
   		document.getElementById(target.id).value=document.getElementById(target.id).value.replace("#","~");
	   });
  
 
 // Special Character Validation Ends
 
	 															 // Global Date Picker Initialisaton
		  $( ".datepicker" ).datepicker({
					dateFormat: 'dd-mm-yy',
					changeMonth: true,
					changeYear: true
				});
		
	 // Datapickker ENds
 
 				function set_bangla()
				{
					$(".bangla").bnKb({
						'switchkey': {"webkit":"k","mozilla":"y","safari":"k","chrome":"k","msie":"y"},
						'driver': phonetic
					});
				}
          
	}
	


function number_format_common( number, dec_type, comma, currency )
{
	if (currency==undefined) var currency="";
	if (currency!="")
	{
		if (currency==1) dec_type=4; else dec_type=5;
	}
	var dec_place= new Array;
	dec_place[1]=2;
	dec_place[2]=2;
	dec_place[3]=8;
	dec_place[4]=2;
	dec_place[5]=4;
	dec_place[6]=0;
	dec_place[7]=2;
	return number_format (number, dec_place[dec_type],'.' , "") ;
}



function number_format (number, decimals, dec_point, thousands_sep) 
{
   
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);            return '' + Math.round(n * k) / k;
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');    }
    return s.join(dec);
}
//========================Defined by Monzu==============================================
function array_key_exists (key, search) 
{
  if (!search || (search.constructor !== Array && search.constructor !== Object)) {
    return false;
  }
  return key in search;
}

function array_sum (array) 
{
  var key, sum = 0;
  if (array && typeof array === 'object' && array.change_key_case) { 
    return array.sum.apply(array, Array.prototype.slice.call(arguments, 0));
  }
  if (typeof array !== 'object') {
    return null;
  }

  for (key in array) {
    if (!isNaN(parseFloat(array[key]))) {
      sum += parseFloat(array[key]);
    }
  }
  return sum;
}

//========================Defined by Monzu End======================================
	 
 /*
function freeze_table_column( tbl )
{
	  //****Copywrite CoastWorx, Version 1.1******
  //****Please make a donation if you wish to remove this notice!******
  
  var freezeRow=3; //change to row to freeze at
  var freezeCol=4; //change to column to freeze at
  var myRow=freezeRow;
  var myCol=freezeCol;
  var speed=100; //timeout speed
  var myTable;
  var noRows;
  var myCells,ID;
	 myTable=document.getElementById(tbl);
	
	$('#'+tbl).scroll(function () { 
			if(myCol<(myCells-1)){
				for( var x = 0; x < noRows; x++ ) {
					myTable.rows[x].cells[myCol].style.display="none";
				}
				myCol++
				ID = window.setTimeout('left()',speed);
		
			}
		});
		 
}

function left(up){
	if(up){window.clearTimeout(ID);return;}
	if(!myTable){setUp();}

	if(myCol<(myCells-1)){
		for( var x = 0; x < noRows; x++ ) {
			myTable.rows[x].cells[myCol].style.display="none";
		}
		myCol++
		ID = window.setTimeout('left()',speed);

	}
}
*/
 	
function show_graph( settings_file, data_file, chart_type, div, caption, rel_path, is_link, gheight, gwidth )
{
	//document.getElementById('stack_company').style.visibility="hidden";
	
	var chart_settings=new Array();
	var chart_flash=new Array();
	
	chart_settings['pie']="<settings><background><file></file></background><data_type>csv</data_type><legend><enabled>0</enabled></legend><pie><inner_radius>25</inner_radius><height>10</height><angle>10</angle><gradient></gradient></pie><animation><start_time>1</start_time><pull_out_time>1</pull_out_time></animation><data_labels><show>{title}</show><max_width>300</max_width><min_width>600</min_width></data_labels></settings>";
	
	chart_settings['column']="<settings><background><file></file></background><data_type>csv</data_type><legend><enabled>0</enabled></legend><pie><inner_radius>25</inner_radius><height>10</height><angle>10</angle><gradient></gradient></pie><animation><start_time>1</start_time><pull_out_time>1</pull_out_time></animation><data_labels><show>{title}</show><max_width>300</max_width></data_labels></settings>";
	
	
	chart_flash['pie']="ampie";
	chart_flash['column']="amcolumn";
	//document.getElementById('caption_text').innerHTML="Monthly Order Status (Quantity Wise) for Year: ";
	//alert(is_link);
	//data_file=escape(data_file);
	//alert(data_file);
	var params = 
	{
		bgcolor:"#CCCCCC"
	};
	if (is_link!="")
	{
		var flashVars = 
		{
			path: rel_path+"ext_resource/amcharts/flash/", 
			settings_file: settings_file+".php",
			data_file: data_file+".php"
		}; 
	}
	else
	{
		var flashVars = 
		{
			path: rel_path+"ext_resource/amcharts/flash/", 
			chart_data: data_file,
        	chart_settings: chart_settings[chart_type]
		};		
	}
	// change 8 to 80 to test javascript version            
	if (swfobject.hasFlashPlayerVersion("8"))
	{
		swfobject.embedSWF( rel_path+"ext_resource/amcharts/flash/"+chart_flash[chart_type]+".swf", div, gwidth, gheight, "8.0.0", "../../../amcharts/flash/expressInstall.swf", flashVars, params );
	}
	else
	{
		// Note, as this example loads external data, JavaScript version might only work on server
		var amFallback = new AmCharts.AmFallback();
		amFallback.pathToImages = "../../../amcharts/javascript/images/";
		amFallback.settingsFile = flashVars.settings_file;
		amFallback.dataFile = flashVars.data_file;				
		amFallback.type = chart_type; //"column";
		amFallback.write(div);
	}
}

function accordion_menu( hid, div_id, onclick_fnc) 
{
	if (!onclick_fnc) var onclick_fnc="";
	var dd=$('#'+hid).html();
	if (dd.indexOf("+")>0)
	{
		dd=dd.replace("+","-");
		$('#'+hid).html(dd);
	}
	else
	{
		dd=dd.replace("-","+");
		$('#'+hid).html(dd);
	}
	
	$('#'+div_id).toggle('slow', function() {
		eval (onclick_fnc);
	});
}

function append_report_checkbox(tid)
{
	var i=0;
	$("#"+tid+" thead th").each(function() {
		
        $(this).append('<input type="checkbox" id="th_'+i+'" class="rpt_check" onclick="report_check_box_click(this.id)" checked="checked" />');
		i++
    });
}

function report_check_box_click(id)
{
	if ($('#'+id).is(":checked")==false)
	{
		$('#'+id).removeAttr('checked');
		$('#'+id).removeClass("rpt_check");
	}
	else
	{
		$('#'+id).attr('checked','checked');
		$('#'+id).addClass('rpt_check');
	}
}

function print_priview_html( report_div, scroll_div, header_table, footer_table, report_type, link_pos, rel_path )  //type: 1=xls, 2=pdf, 3=html
{
	var filter=0;
	if (report_type==3)
	{
		var mxheght= document.getElementById(scroll_div).style.maxHeight;
		var total_width=0;
		var top_wd=$("#"+header_table).width();
		var botom_wd=$("#"+scroll_div+ " table").width();
		var top_wd_new=0;
		var botom_wd_new=0;
			var i=0;
			$("#"+header_table+" thead th").each(function() {
				var wd=($(this).width());
				if (!$('#th_'+i).hasClass('rpt_check'))
				{
				  total_width=(total_width*1)+(wd*1);
					$("#"+header_table).find("tr").each(function(){
						$(this).find("th:eq("+i+")").hide();
					   
					});
					$("#"+scroll_div+" table").find("tr").each(function(){
						$(this).find("td:eq("+i+")").hide();
					});
				}
				i++;
			});
			top_wd_new=top_wd-total_width;
			botom_wd_new=botom_wd-total_width;
			
			 if ($("#"+scroll_div +" table tr:first").attr('class')=='fltrow') 
			 {
				filter=1;
				$("#"+scroll_div +" table tr:first").hide();	 
			 }
			 $("#"+header_table).width(top_wd_new);
			 $("#"+scroll_div+ " table").width(botom_wd_new);
			  
			 document.getElementById(scroll_div).style.overflow="auto";
			 document.getElementById(scroll_div).style.maxHeight="none";
			  
			var html = document.getElementById(report_div).innerHTML;
			 var tto=($(html).find('a').replaceWith(function() {
				return this.innerHTML;
			}).end().html());
			 tto=($(tto).find('input').replaceWith(function() {
				return this.innerHTML;
			}).end().html());
			
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			var btn='<input type="button" onclick="javascript:window.print()" value="  Print  " name="Print" class="formbutton" style="width:100px"/><br><br>';
			 
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><script src="'+rel_path+'includes/functions.js"></script><script src="'+rel_path+'js/jquery.js"></script><script>context_menu()</script><link rel="stylesheet" href="'+rel_path+'css/style_common.css" type="text/css" media="print" /><title></title></head><body><div id="report_on_popup">'+btn+tto+'</div></body</html>');
			d.close();
			
			document.getElementById(scroll_div).style.overflowY="scroll";
	 		document.getElementById(scroll_div).style.maxHeight=mxheght;
			if ( filter==1)
			{
				$("#"+scroll_div +" table tr:first").show();
			}
			i=0;
			$("#"+header_table+" thead th").each(function() {
				if (!$('#th_'+i).hasClass('check'))
				{
				  	$("#"+header_table).find("tr").each(function(){
						$(this).find("th:eq("+i+")").show(); 
					});
					$("#"+scroll_div+" table").find("tr").each(function(){
						$(this).find("td:eq("+i+")").show();
					});
				}
				i++;
			});
			 $("#"+header_table).width(top_wd);
			 $("#"+scroll_div+ " table").width(botom_wd);
		 
	}
	else if ( report_type==1  || report_type==2)
	{
		var im_url="";
		flink=document.getElementById('link_container').value;
		flink=flink.split("**");
		flink=flink[pos].split("ext_resource");
		var url=flink[0];
		if (url.length==3) im_url=""; else if (url.length==6) im_url=".."; else if (url.length==9)im_url="../.."; else if (url.length==12)im_url="../../.."; 
		//alert(pos)
		flink=im_url+'/ext_resource'+flink[1];
		//alert(flink);
		 
		 window.open(flink, "#");
		 return;
		/*
		var im_url="";
		flink=document.getElementById('link_container').value;
		flink=flink.split("**");
		flink=flink[pos].split("ext_resource");
		var url=flink[0];
		if (url.length==3) im_url=""; else if (url.length==6) im_url=".."; else if (url.length==9)im_url="../.."; else if (url.length==12)im_url="../../.."; 
		//alert(pos)
		flink=im_url+'/ext_resource'+flink[1];
		//alert(flink);
		 
		 window.open(flink, "#");
		 return;*/
	}
	 
}	

function context_menu(id, xls_link, doc_link, pdf_link)
{
	if (!xls_link) var xls_link="";
	if (!doc_link) var doc_link="";
	if (!pdf_link) var pdf_link=""; 
	
	$('.target1').contextMenu('context-menu-1', {
            'Convert TO XLS': {
                click: function(element) {  // element is the jquery obj clicked on when context menu launched
                  if (xls_link!="") window.open(xls_link, "#");
                }
               // a custom css class for this menu item (usable for styling)
            },
            'Convert To Doc': {
                click: function(element){ 
				 if (doc_link!="") window.open(doc_link, "#");
				 } 
                
            }
        });
}
 