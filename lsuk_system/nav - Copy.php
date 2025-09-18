<?php //session_start(); ?>
<aside id="sidebar" class="column">
		<form class="quick_search" action="" method="post">
		  <input type="text" placeholder="Organisation Name or Invoice No." name="org_name">
          <input type="submit" name="submit" style="display:none" />
		</form>
        <?php if(isset($_POST['submit'])){header('Location: interp_search.php?org_name='.$_POST['org_name']);} ?>
		<hr/>
		<h3>Booking Form</h3>
		<ul class="toggle">
        
       
        
        
			<li class="icn_view_users"><a href="#" onClick="MM_openBrWindow('interpreter.php','_blank','scrollbars=yes,resizable=yes,width=900,height=650')">Interpreter</a></li>
			<li class="icn_ph"><a href="#" onClick="MM_openBrWindow('telephone.php','view','scrollbars=yes,resizable=yes,width=900,height=600')">Telephone Interpreter</a></li>
			<li class="icn_edit_article"><a href="#" onClick="MM_openBrWindow('translation.php','_blank','scrollbars=yes,resizable=yes,width=900,height=650')">Translation</a></li>
		</ul>
		<h3>Archive</h3>
		<ul class="toggle">
			<li class="icn_view_users"><a href="interp_list.php">Interpreters List</a></li>
			<li class="icn_ph"><a href="telep_list.php">Telephone List</a></li>
			<li class="icn_edit_article"><a href="trans_list.php">Translation List</a></li>
			<li class="icn_view_users"><a href="interp_list_commit.php">Interpreters Invoices Sent</a></li>
			<li class="icn_ph"><a href="telep_list_commit.php">Telephone Invoices Sent</a></li>
			<li class="icn_edit_article"><a href="trans_list_commit.php">Translation Invoices Sent</a></li>
		</ul>
		<?php if(@$_SESSION['prv']=='Management' || @$_SESSION['prv']=='Finance' ){?><h3>Other</h3>
		<ul class="toggle">
			<li class="icn_view_users"><a href="#" onClick="MM_openBrWindow('interp_reg.php','_blank','scrollbars=yes,resizable=yes,width=900,height=650')">Interpreter Registration</a></li>            
			<li class="icn_audio"><a href="reg_interp_list.php">Registered Interpreters</a></li>           
			<li class="icn_view_users"><a href="interp_work_list.php">Interpreters Salary</a></li> 
            <li class="icn_view_users"><a href="#" onClick="MM_openBrWindow('comp_reg.php','_blank','scrollbars=yes,resizable=yes,width=900,height=650')">Company Registration</a></li>                       
			<li class="icn_audio"><a href="reg_comp_list.php">Registered Companies</a></li>
			<li class="icn_photo"><a href="interp_pending_payment.php">Pending Payment Cases</a></li>
			<li class="icn_photo"><a href="#" onClick="MM_openBrWindow('rates.php','_blank','scrollbars=yes,resizable=yes,width=900,height=650')">Manage Rates</a></li>
			<li class="icn_photo"><a href="#" onClick="MM_openBrWindow('language.php','_blank','scrollbars=yes,resizable=yes,width=900,height=650')">Manage Languages</a></li>
			<li class="icn_video"><a href="advance_search.php">Advance Search</a></li> <li class="icn_view_users"><a href="#" onClick="MM_openBrWindow('comp_query.php','_blank','scrollbars=yes,resizable=yes,width=900,height=650')">Customer's History</a></li>
			<li class="icn_view_users"><a href="#" onClick="MM_openBrWindow('invoice_query.php','_blank','scrollbars=yes,resizable=yes,width=900,height=650')">Invoices Registered</a></li>
            <li class="icn_view_users"><a href="#" onClick="MM_openBrWindow('vat_query.php','_blank','scrollbars=yes,resizable=yes,width=900,height=650')">VAT Collected</a></li>
            <li class="icn_edit_article"><a href="interp_report.php">Booking Report</a></li>
            <li class="icn_view_users"><a href="#" onClick="MM_openBrWindow('report_query.php','_blank','scrollbars=yes,resizable=yes,width=900,height=650')">Generate Report</a></li>           
			<li class="icn_audio"><a href="interp_paid.php">Paid Invoices</a></li>
		</ul>
  <h3>Reports</h3>
		<ul class="toggle">
  <li class="icn_photo"><a href="rip_interp.php">Interpreters Rep(Ac Purposes)</a></li>
  <li class="icn_photo"><a href="rip_telep.php">Telephone Interp Rep(Ac Purposes)</a></li>
  <li class="icn_photo"><a href="rip_trans.php">Translation Interp Rep(Ac Purposes)</a></li>
  <li class="icn_photo"><a href="rip_comp.php">Company (Client) Report</a></li>
  <li class="icn_photo"><a href="rip_interp_12.php">Interpreter Report</a></li>
  <li class="icn_photo"><a href="rip_pending_inv.php">Pending  invoices (Intrp)</a></li>
  <li class="icn_photo"><a href="rip_pending_inv_telep.php">Pending  invoices (Telep)</a></li>
  <li class="icn_photo"><a href="rip_pending_inv_trans.php">Pending  invoices (Trans)</a></li>
  <li class="icn_photo"><a href="rip_pending_payment_12.php">Pending  Payment Report</a></li>
  <li class="icn_photo"><a href="rip_paid_inv.php">paid  invoices (Intrp)</a></li>
  <li class="icn_photo"><a href="rip_paid_inv_telep.php">paid  invoices (Telep)</a></li>
  <li class="icn_photo"><a href="rip_paid_inv_trans.php">paid  invoices (Trans)</a></li>
  <li class="icn_photo"><a href="rip_salary.php">Emp Salary</a></li>
		</ul>
  <h3>Admin</h3>
		<ul class="toggle">
        <?php if(@$_SESSION['userId']==1){ ?>
        <li class="icn_settings"><a href="#" onclick="MM_openBrWindow('employee.php','_blank','scrollbars=yes,resizable=yes,width=900,height=650')">Emp Registration</a></li>
        <li class="icn_settings"><a href="emp_list.php">Emp List</a></li>
        <li class="icn_settings"><a href="#" onclick="MM_openBrWindow('attendance.php','_blank','scrollbars=yes,resizable=yes,width=900,height=650')">Emp Attendance</a></li>
        <li class="icn_settings"><a href="#" onclick="MM_openBrWindow('expense.php','_blank','scrollbars=yes,resizable=yes,width=900,height=650')">Comp Expences</a></li>
  		<li class="icn_settings"><a href="#">Expences List</a></li>
        <li class="icn_settings"><a href="#" onClick="MM_openBrWindow('signup.php','_blank','scrollbars=yes,resizable=yes,width=900,height=650')">Sign Up</a></li>
        <li class="icn_view_users"><a href="reg_users_list.php">Users List</a></li>
			<?php } ?><?php } ?>
        <?php if(@$_SESSION['userId']){ ?>
        <hr/>
        <li class="icn_security"><a href="#" onClick="MM_openBrWindow('change_pass.php','_blank','scrollbars=yes,resizable=yes,width=900,height=650')">Change Password</a></li>
			<li class="icn_jump_back"><a href="logout.php">Sign out</a></li><?php } ?>
		</ul>
		
  <footer>
			<hr />
	<p><strong>Copyright &copy; 2015 Website Admin</strong></p>
		</footer>
	</aside>