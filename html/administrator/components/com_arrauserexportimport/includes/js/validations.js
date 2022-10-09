function setAdditionalColumn(number){
	var index  = number.selectedIndex;
	var option = number.options[index].value;

	for(i=1; i<=option; i++){
		var div = document.getElementById("column"+i);
	 	div.style.display = "block";	
	}
	var value = parseInt(option)+1;	
	for(j=value; j<=35; j++){
		var div2 = document.getElementById("column"+j);
	 	div2.style.display = "none";	
	}
}

function validateJomSocialExport(total){	
	/*var checked = "false";
	for(i=0; i<total; i++){	
		if(document.adminForm.getElementById("group_type_checkbox["+i+"]").checked == true){
			checked = "true";
			break;
		}
	}
	if(checked == "false"){
		alert("Please select at least one group!");
		return false;
	}*/
	return true;
}

function checkAllFields(total){
	if(document.adminForm.toggle1.checked == true){
		for (i=0; i<total; i++){
			document.adminForm.getElementById("top_column_checkbox["+i+"]").checked=true;	
		}
	}
	else{
		for (i=0; i<total; i++){
			if(i!=0 && i!=1 && i!=2){
				document.adminForm.getElementById("top_column_checkbox["+i+"]").checked=false;	
			}
		}	
	}
}

function checkAllFieldsCommunity(total){
	if(document.adminForm.toggle1.checked == true){
		for (i=0; i<total; i++){
			document.adminForm.getElementById("top_column_checkbox["+i+"]").checked=true;	
		}
	}
	else{
		for (i=0; i<total; i++){
			if(i!=0 && i!=1 && i!=2){
				document.adminForm.getElementById("top_column_checkbox["+i+"]").checked=false;	
			}
		}	
	}
}

function checkAllGroups(total){
	if(document.adminForm.toggle2.checked == true){
		for (i=0; i<total; i++){
			document.adminForm.getElementById("group_type_checkbox["+i+"]").checked=true;	
		}
	}
	else{
		for (i=0; i<total; i++){
			document.adminForm.getElementById("group_type_checkbox["+i+"]").checked=false;
		}	
	}
}

function checkAllGroupsCommunity(total){
	if(document.adminForm.toggle2.checked == true){
		for (i=0; i<total; i++){
			document.adminForm.getElementById("group_type_checkbox["+i+"]").checked=true;	
		}
	}
	else{
		for (i=0; i<total; i++){
			document.adminForm.getElementById("group_type_checkbox["+i+"]").checked=false;
		}	
	}
}

function showSeparator(){
	 var div1 = document.getElementById("file_type_id");
	 div1.style.display = "block";
	 
	 var div2 = document.getElementById("ordering_export");
	 div2.style.display = "block";
	 
	 return true;
}

function hideSeparator(){
	 var div1 = document.getElementById("file_type_id");
	 div1.style.display = "none";
	 
	 var div2 = document.getElementById("ordering_export");
	 div2.style.display = "none";
	 
	 return true;
}

function validateJomSocialImport(){
	form = document.adminForm;
	if(form.file_upload.value.lastIndexOf(".csv")<0 && form.file_upload.value.lastIndexOf(".txt")<0) {
			alert("Please upload only .csv or .txt extension file");
			return false;
	}

	if(form.file_upload.value.lastIndexOf(".csv")>0 || form.file_upload.value.lastIndexOf(".txt")>0 ){
		form.type_file.value="csv_txt";
	}
		
	var min_value = document.adminForm.getElementById("min_value").value;
	var max_value = document.getElementById("max_value").value;
	var temp1 = parseInt(min_value);
	var temp2 = parseInt(max_value);
	if((temp1 != min_value) || (temp2 != max_value)){
		alert("Please set numerical values for import limit!");
		return false;
	}
	
	return true;
}

function validateImportForm(){
	form = document.adminForm;
	subject_template = form.subject_template.value;
	from_email = form.from_email.value;
	from_name = form.from_name.value;
	sitename = form.sitename.value;
	email_template = form.email_template.value;
	file_import = form.file_import.value;

	if(file_import == "sql_zip"){
		if(form.sqlzip_file_upload.value.lastIndexOf(".sql")<0 && form.sqlzip_file_upload.value.lastIndexOf(".zip")<0) {
			alert("Please upload only .sql or .zip extension file");
			return false;
		}
	}
	else if(file_import == "csv_txt"){
		if(form.csvtxt_file_upload.value.lastIndexOf(".csv")<0 && form.csvtxt_file_upload.value.lastIndexOf(".txt")<0) {
			alert("Please upload only .csv or .txt extension file");
			return false;
		}	
	} else {
		if((form.sqlzip_file_upload.value!='')&&(form.sqlzip_file_upload.value.lastIndexOf(".sql")<0)&&(form.sqlzip_file_upload.value.lastIndexOf(".zip")<0)) {
			alert("Please upload only .sql or .zip extention file");
			return false;	
		}
		else if((form.csvtxt_file_upload.value!='')&&(form.csvtxt_file_upload.value.lastIndexOf(".csv")<0)&&(form.csvtxt_file_upload.value.lastIndexOf(".txt")<0)) {
			alert("Please upload only .csv or .txt extention file");
			return false;	
		}
		else if((form.csvtxt_file_upload.value=='')&&(form.sqlzip_file_upload.value=='')){
			alert("No file selected to import!");	
			return false;
		}
	}
	
	var add_columns = false;
	for(i=1; i<=35; i++){		
		var column = document.getElementById("column"+i);
		if(column.style.display != "none"){
			add_columns = true;
			break;
		}
	}
	if(add_columns == false){
		alert("Please set additional columns for import from 'Set new columns for import' area!");
		return false;
	}
	
	if(document.getElementById("send_email_to_import").checked == true){
		var message = new Array();
		var ids = new Array();
		var message_text = "";
			
		if(subject_template.trim().length == 0){
			message[message.length]= "Email subject";
			ids[ids.length] = "subject_template";
		}
	
		if(from_email.trim().length == 0){
			message[message.length]= "from email";
			ids[ids.length] = "from_email";
		}
		
		if(from_name.trim().length == 0){
			message[message.length]= "from name";
			ids[ids.length] = "from_name";
		}
		
		if(sitename.trim().length == 0){
			message[message.length]= "site name";
			ids[ids.length] = "sitename";
		}
		
		if(email_template.trim().length == 0){
			message[message.length]= "body email";
			ids[ids.length] = "email_template";
		}
		
		if(message.length != 0){
			for(i=0; i<message.length; i++){
				if(document.getElementById(ids[i])) {document.getElementById(ids[i]).style.border = "1px solid #FF0000";}
				if(i != message.length-1){
					message_text += "'{"+message[i] + "}', ";
				} 
				else{ 
					message_text += "'{"+message[i]+"}'"; 
				}
			}
			alert("Field(s) "+message_text+" is(are) not completed.");
			return false;
		}
	}
	
	var filter = /^([a-zA-Z0-9_.-])+@(([a-zA-Z0-9-])+.)+([a-zA-Z0-9]{2,4})+$/;
	if (!filter.test(from_email)) {
		alert('Please provide a valid email address');
		return false;
	}
	return true;
}

function hide_show(div_id){
	var display = document.getElementById(div_id).style.display;
	if(display == "none"){
		display = "block";	
	}
	else{
		display = "none";
	}
	document.getElementById(div_id).style.display = display;
}

function searchByFilters(){
	var field_id = document.getElementById("fields").value;
	var filteroptions = document.getElementById("filteroptions").value;
	var keyword = '';
	if(eval(document.getElementById("keyword"))){
		if(document.getElementById("keyword").value != 'Keyword...'){
			keyword = document.getElementById("keyword").value;
		}
	}
	document.getElementById("imagewait").style.display = "table-cell";
	var myAjax = new Ajax('index.php?option=com_arrauserexportimport&controller=additionalcolumns&task=search&format=raw&fields='+field_id+"&filteroptions="+filteroptions+'&keyword='+keyword, 
			   { 	
				onSuccess: function(response) {				  	
					to_be_replaced = document.getElementById('imagewait');
					to_be_replaced.innerHTML = response;			   
					}
				});
    myAjax.request();
}