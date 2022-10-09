<?php
function com_install(){
	$table  = "";
	$table .= "<table>";
	$table .= 		"<tr>";
	$table .= 			"<td>";
	$table .= 				"<img src=\"components/com_arrauserexportimport/images/logo_arra_user_export.png\"/>";
	$table .= 			"</td>";
	$table .= 			"<td>";
	$table .= 				"<b>Successfully installed ARRA User Export Import."."</b><br /><br />";
	$table .= 				"You can now use the component to export users from your current Joomla installation or to import users into your website (available formats: csv, txt, html, sql, zip)."."<br /><br />";
	$table .= 				"Useful links:"."<br /><br />";
	$table .= 				"Forum -> <a href=\"http://www.joomlarra.com/forum\" target=\"_blank\">http://www.joomlarra.com/forum"."</a><br/>".
							"Documentation -> <a href=\"http://www.joomlarra.com/user-export-import-documentation/\" target=\"_blank\">http://www.joomlarra.com/user-export-import-documentation/"."</a><br/>".
							"Report bugs -> <a href=\"http://www.joomlarra.com/4-report-bugs/\" target=\"_blank\">http://www.joomlarra.com/4-report-bugs/"."</a><br/>".
							"Request new feature -> <a href=\"http://www.joomlarra.com/6-feature-request/\" target=\"_blank\">http://www.joomlarra.com/6-feature-request/</a>";
	$table .= 			"</td>";
	$table .= 		"</tr>";
	$table .= "</table>";
	echo $table;
}
?>