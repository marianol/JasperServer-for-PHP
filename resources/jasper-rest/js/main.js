/* ========================================================================== 

 Copyright (C) 2005 - 2012 Jaspersoft Corporation. All rights reserved.
 http://www.jaspersoft.com.

 Unless you have purchased a commercial license agreement from Jaspersoft,
 the following license terms apply:

 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU Affero General Public License as
 published by the Free Software Foundation, either version 3 of the
 License, or (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 GNU Affero  General Public License for more details.

 You should have received a copy of the GNU Affero General Public  License
 along with this program. If not, see <http://www.gnu.org/licenses/>.

=========================================================================== */

$(document).ready(function(){
	$('#userForm').hide();

});

$('#userButton').live('click', function(){
	$.getJSON('getuser.php', function(data){
		$.each(data, function(i, user){
			$('#userlist').append('<li>'+user.username+'|'+user.tenantId+'</li>').append('<a href=?deluser='+user.username+'|'+user.tenantId+' id="userlink"> delete user </a>');
		});
	});
});

$('#newUser').live('click', function(){
	$('#userForm').slideToggle();
});

jQuery(function(){
	jQuery.getJSON('wordpress/wp-content/plugins/jasper/runreport.php?func=getReports&uri=/reports/samples',
		function(data){
			var sel = jQuery('#reportList').empty();
				jQuery.each(data, function(){
					sel.append($('<option />').val(this.uri).text(this.name));
		});
	});
});